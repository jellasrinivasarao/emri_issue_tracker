import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.drawerState = function drawerState() {
    return {
        drawerOpen: false,
        selectedStatus: '',
        selectedTicket: null,
        activeTab: 'details',
        init() {
            this.drawerOpen = false;
            this.activeTab = 'details';
        },
        isVendorAssignmentStatus() {
            if (!this.selectedStatus) return false;
            const status = String(this.selectedStatus).toLowerCase().trim();
            // ONLY show vendor field for these EXACT statuses:
            // 1. "Vendor Assignment"
            // 2. "Escalate to Vendor"
            return status === 'vendor assignment' || status === 'escalate to vendor';
        },
        shouldShowVendorPicker() {
            if (!this.selectedStatus) return false;
            const status = String(this.selectedStatus).toLowerCase().trim();
            return status === 'vendor assignment' || status === 'escalate to vendor';
        },
        handleStatusChange() {
            const showPicker = this.shouldShowVendorPicker();

            if (showPicker) {
                setTimeout(() => {
                    const currentTicket = this.selectedTicket || window.__EMRI_CURRENT_TICKET || null;
                    const list = document.querySelector('#vendor-multi-select [data-multi-select-list]');
                    const input = document.querySelector('#vendor-multi-select [data-multi-select-input]');

                    console.log('handleStatusChange: showPicker=true, currentTicket=', currentTicket);
                    if (currentTicket) {
                        console.log('currentTicket.state_id=', currentTicket.state_id, 'currentTicket.project_id=', currentTicket.project_id);
                    }

                    console.log('window.refreshVendorOptions type =', typeof window.refreshVendorOptions);
                    if (currentTicket && typeof window.refreshVendorOptions === 'function') {
                        window.refreshVendorOptions(currentTicket);
                    } else {
                        console.log('Vendor refresh skipped because window.refreshVendorOptions is NOT a function');
                    }

                    if (list) {
                        list.classList.remove('hidden');
                    }
                    if (input) {
                        input.focus();
                    }
                }, 20);
                return;
            }

            const vendorHidden = document.querySelector('[data-multi-select-hidden]');
            if (vendorHidden) {
                vendorHidden.innerHTML = '';
            }
            const vendorChips = document.querySelector('[data-multi-select-chips]');
            if (vendorChips) {
                vendorChips.innerHTML = '';
            }
            const vendorInput = document.querySelector('[data-multi-select-input]');
            if (vendorInput) {
                vendorInput.value = '';
            }
            const list = document.querySelector('#vendor-multi-select [data-multi-select-list]');
            if (list) {
                list.classList.add('hidden');
            }
        },
    };
};

// Ensure visible debug logging in environments where console.debug may be filtered
window.__EMRI_DEBUG = window.__EMRI_DEBUG ?? true;
window.__EMRI_VENDOR_DATA = window.__EMRI_VENDOR_DATA || {
    vendorOptions: [],
    vendorStateMappings: [],
    currentStateId: null,
    currentProjectId: null,
    selectedVendorIds: []
};

window.refreshVendorOptions = window.refreshVendorOptions || function (ticket) {
    console.log('fallback refreshVendorOptions called with ticket:', ticket);
    if (typeof window.__EMRI_renderVendorList === 'function') {
        window.__EMRI_renderVendorList('');
    }
    if (typeof window.__EMRI_renderVendorChips === 'function') {
        window.__EMRI_renderVendorChips();
    }
};

console.log('[app.js] __EMRI_DEBUG set =>', !!window.__EMRI_DEBUG);

let loadingTimer = null;
let isNavigating = false;

function showPageLoading() {
    // No full-page loading overlay for smoother navigation.
}

function hidePageLoading() {
    // No full-page loading overlay for smoother navigation.
}

function isInternalNavigation(anchor) {
    if (!anchor || !anchor.href) {
        return false;
    }
    const url = new URL(anchor.href, window.location.href);
    if (url.origin !== window.location.origin) {
        return false;
    }
    if (url.pathname === window.location.pathname && url.search === window.location.search) {
        return false;
    }
    if (url.hash && url.pathname === window.location.pathname && url.search === window.location.search) {
        return false;
    }
    return true;
}

function shouldInterceptLink(event) {
    if (event.defaultPrevented) {
        return false;
    }
    if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
        return false;
    }
    const anchor = event.target.closest('a');
    if (!anchor) {
        return false;
    }
    if (anchor.hasAttribute('download')) {
        return false;
    }
    if (anchor.target && anchor.target !== '_self') {
        return false;
    }
    if (anchor.href === '' || anchor.href.startsWith('mailto:') || anchor.href.startsWith('tel:')) {
        return false;
    }
    if (anchor.dataset.noAjax !== undefined) {
        return false;
    }
    return isInternalNavigation(anchor);
}

function executeInlineScripts(root) {
    const scripts = Array.from(root.querySelectorAll('script'));
    scripts.forEach((oldScript) => {
        const script = document.createElement('script');
        if (oldScript.src) {
            script.src = oldScript.src;
            script.async = false;
        }
        if (oldScript.type) {
            script.type = oldScript.type;
        }
        if (!oldScript.src) {
            script.textContent = oldScript.textContent;
        }
        oldScript.parentNode.replaceChild(script, oldScript);
    });
}

function replacePageContent(html, url) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newShell = doc.getElementById('page-shell');
    const oldShell = document.getElementById('page-shell');
    const newTitle = doc.querySelector('title');

    if (!newShell || !oldShell) {
        console.log('[nav] missing page-shell in fetched HTML', { newShell: !!newShell, oldShell: !!oldShell, url });
        return false;
    }

    // Replace the page shell (header + main content) while keeping the aside in DOM
    oldShell.innerHTML = newShell.innerHTML;

    if (newTitle) {
        document.title = newTitle.textContent;
    }

    executeInlineScripts(oldShell);

    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
        window.Alpine.initTree(oldShell);
    } else if (window.Alpine && typeof window.Alpine.discoverUninitializedComponents === 'function') {
        window.Alpine.discoverUninitializedComponents(oldShell);
    }

    document.dispatchEvent(new Event('DOMContentLoaded'));

    // After replacing content, check if the new page signals to hide the sidebar
    const wantsNoSidebar = !!oldShell.querySelector('[data-hide-sidebar]');
    console.log('[nav] navigation fetched', { url, wantsNoSidebar });

    // Notify Alpine layout to update sidebar state in a robust way
    try {
        window.dispatchEvent(new CustomEvent('layout:sidebar', { detail: { noSidebar: wantsNoSidebar } }));
    } catch (e) {
        console.warn('[nav] failed to dispatch layout event', e);
    }

    try {
        // Toggle aside visibility using Tailwind's `hidden` class so utility styles apply correctly.
        const asides = document.querySelectorAll('aside');
        asides.forEach(a => {
            if (wantsNoSidebar) {
                a.classList.add('hidden');
            } else {
                a.classList.remove('hidden');
            }
        });

        // Toggle content padding that reserves space for the sidebar (Tailwind class)
        const contentContainer = document.querySelector('.relative.flex.flex-1.flex-col.min-h-0.box-border');
        if (contentContainer) {
            const sidebarClass = 'md:pl-[260px]';
            if (wantsNoSidebar) {
                contentContainer.classList.remove(sidebarClass);
            } else {
                contentContainer.classList.add(sidebarClass);
            }
        }
    } catch (e) {
        console.warn('Sidebar toggle failed', e);
    }

    if (url) {
        window.history.pushState({}, '', url);
    }

    return true;
}

function fetchAndNavigate(url) {
    if (isNavigating) {
        return;
    }
    isNavigating = true;
    showPageLoading();

    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        },
        credentials: 'same-origin',
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error('Navigation failed');
            }
            return response.text();
        })
        .then((html) => {
            const success = replacePageContent(html, url);
            if (!success) {
                window.location.href = url;
            }
        })
        .catch(() => {
            window.location.href = url;
        })
        .finally(() => {
            isNavigating = false;
            hidePageLoading();
        });
}

function attachNavigationLoading() {
    document.addEventListener('click', function (event) {
        if (!shouldInterceptLink(event)) {
            return;
        }
        event.preventDefault();
        const anchor = event.target.closest('a');
        fetchAndNavigate(anchor.href);
    });

    window.addEventListener('popstate', function () {
        fetchAndNavigate(window.location.href);
    });

    window.addEventListener('pageshow', function () {
        hidePageLoading();
    });
}

document.addEventListener('DOMContentLoaded', function () {
    attachNavigationLoading();
    hidePageLoading();
});

window.addEventListener('load', function () {
    hidePageLoading();
});

Alpine.start();
