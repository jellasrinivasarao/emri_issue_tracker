import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.drawerState = function drawerState() {
    return {
        drawerOpen: false,
        selectedStatus: 'In Progress',
        selectedTicket: null,
        activeTab: 'details',
        init() {
            this.drawerOpen = false;
            this.activeTab = 'details';
        },
    };
};

window.clearIssuePopupSelect = function clearIssuePopupSelect(select, placeholder = 'Select') {
    if (!select) return;
    select.innerHTML = '';
    const option = document.createElement('option');
    option.value = '';
    option.textContent = placeholder;
    select.appendChild(option);
};

window.loadIssuePopupProjects = function loadIssuePopupProjects(stateId) {
    const projectSelect = document.getElementById('project_id');
    const applicationSelect = document.getElementById('application_id');
    const moduleSelect = document.getElementById('module_id');
    if (!projectSelect) return;

    window.clearIssuePopupSelect(projectSelect, 'Select Project');
    window.clearIssuePopupSelect(applicationSelect, 'Select Application');
    window.clearIssuePopupSelect(moduleSelect, 'Select Module');

    if (!stateId) return;

    fetch(`/ajax/projects?state_id=${encodeURIComponent(stateId)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then((r) => r.json())
        .then((data) => {
            const seen = new Set();
            data.forEach((p) => {
                if (!p || !p.project_id || seen.has(String(p.project_id))) return;
                seen.add(String(p.project_id));
                const option = document.createElement('option');
                option.value = p.project_id;
                option.textContent = p.project_name;
                projectSelect.appendChild(option);
            });

            if (projectSelect.options.length > 1) {
                projectSelect.value = String(projectSelect.options[1].value);
                if (window.loadIssuePopupApplications) {
                    window.loadIssuePopupApplications(projectSelect.value);
                }
            }
        })
        .catch((error) => console.error('[issue-popup] projects load failed', error));
};

window.loadIssuePopupApplications = function loadIssuePopupApplications(projectId) {
    const projectSelect = document.getElementById('project_id');
    const applicationSelect = document.getElementById('application_id');
    const moduleSelect = document.getElementById('module_id');
    if (!applicationSelect) return;

    window.clearIssuePopupSelect(applicationSelect, 'Select Application');
    window.clearIssuePopupSelect(moduleSelect, 'Select Module');

    if (!projectId) return;

    fetch(`/ajax/applications?project_id=${encodeURIComponent(projectId)}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then((r) => r.json())
        .then((data) => {
            const seen = new Set();
            data.forEach((a) => {
                if (!a || !a.application_id || seen.has(String(a.application_id))) return;
                seen.add(String(a.application_id));
                const option = document.createElement('option');
                option.value = a.application_id;
                option.textContent = a.application_name;
                applicationSelect.appendChild(option);
            });

            if (applicationSelect.options.length > 1) {
                applicationSelect.value = String(applicationSelect.options[1].value);
                if (window.loadIssuePopupModules) {
                    window.loadIssuePopupModules(applicationSelect.value, projectSelect?.value || '');
                }
            }
        })
        .catch((error) => console.error('[issue-popup] applications load failed', error));
};

window.loadIssuePopupModules = function loadIssuePopupModules(applicationId, projectId) {
    const moduleSelect = document.getElementById('module_id');
    if (!moduleSelect) return;

    window.clearIssuePopupSelect(moduleSelect, 'Select Module');
    if (!applicationId) return;

    const params = new URLSearchParams({ application_id: applicationId });
    if (projectId) params.set('project_id', projectId);

    fetch(`/ajax/modules?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then((r) => r.json())
        .then((data) => {
            const seen = new Set();
            data.forEach((m) => {
                if (!m || !m.module_id || seen.has(String(m.module_id))) return;
                seen.add(String(m.module_id));
                const option = document.createElement('option');
                option.value = m.module_id;
                option.textContent = m.module_name;
                moduleSelect.appendChild(option);
            });
        })
        .catch((error) => console.error('[issue-popup] modules load failed', error));
};

window.initIssueCreatePopup = function initIssueCreatePopup() {
    const stateSelect = document.getElementById('state_id');
    const projectSelect = document.getElementById('project_id');
    const applicationSelect = document.getElementById('application_id');

    if (stateSelect && !stateSelect.dataset.popupBound) {
        stateSelect.dataset.popupBound = '1';
        stateSelect.onchange = function () {
            window.loadIssuePopupProjects(this.value);
        };
    }

    if (projectSelect && !projectSelect.dataset.popupBound) {
        projectSelect.dataset.popupBound = '1';
        projectSelect.onchange = function () {
            window.loadIssuePopupApplications(this.value);
        };
    }

    if (applicationSelect && !applicationSelect.dataset.popupBound) {
        applicationSelect.dataset.popupBound = '1';
        applicationSelect.onchange = function () {
            window.loadIssuePopupModules(this.value, document.getElementById('project_id')?.value || '');
        };
    }

    if (stateSelect && stateSelect.value) {
        window.loadIssuePopupProjects(stateSelect.value);
    }
};

// Ensure visible debug logging in environments where console.debug may be filtered
window.__EMRI_DEBUG = window.__EMRI_DEBUG ?? true;
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
