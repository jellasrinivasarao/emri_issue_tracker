import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

let loadingTimer = null;
let isNavigating = false;
// Enable verbose debug logs when troubleshooting navigation/sidebar issues
window.__EMRI_DEBUG = window.__EMRI_DEBUG === true;

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

function syncSidebar(hide) {
    try {
        if (window.__EMRI_DEBUG) console.debug('[app.js] syncSidebar called, hide=', hide);
        const desktopAside = document.querySelector('aside.fixed.inset-y-0.left-0.z-20');
        const contentEl = document.querySelector('.relative.flex.flex-1.flex-col.min-h-0.box-border');
        if (hide) {
            if (desktopAside) {
                desktopAside.classList.add('hidden');
                desktopAside.classList.remove('md:flex');
            }
            if (contentEl) {
                contentEl.classList.remove('md:pl-[260px]');
            }
        } else {
            if (desktopAside) {
                desktopAside.classList.remove('hidden');
                desktopAside.classList.add('md:flex');
            }
            if (contentEl) {
                if (!contentEl.classList.contains('md:pl-[260px]')) {
                    contentEl.classList.add('md:pl-[260px]');
                }
            }
        }
    } catch (e) {
        // ignore
    }
}

function replacePageContent(html, url) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newShell = doc.getElementById('page-shell');
    const currentShell = document.getElementById('page-shell');
    const newTitle = doc.querySelector('title');

    if (!newShell || !currentShell) {
        return false;
    }

    currentShell.innerHTML = newShell.innerHTML;

    if (newTitle) {
        document.title = newTitle.textContent;
    }

    executeInlineScripts(currentShell);

    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
        window.Alpine.initTree(currentShell);
    } else if (window.Alpine && typeof window.Alpine.discoverUninitializedComponents === 'function') {
        window.Alpine.discoverUninitializedComponents(currentShell);
    }

    document.dispatchEvent(new Event('DOMContentLoaded'));

    try {
        const hideSidebarFlag = !!doc.querySelector('[data-hide-sidebar]');
        if (window.__EMRI_DEBUG) console.debug('[app.js] replacePageContent: hideSidebarFlag=', hideSidebarFlag, 'url=', url);
        syncSidebar(hideSidebarFlag);
    } catch (e) {
        // ignore
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
        // If current page requests the sidebar hidden (role dashboard), show sidebar immediately
        try {
            const currentHide = !!document.querySelector('[data-hide-sidebar]');
            if (window.__EMRI_DEBUG) console.debug('[app.js] link click intercepted -> currentHide=', currentHide, 'href=', anchor ? anchor.href : null);
            // Always show the sidebar immediately to avoid waiting for the AJAX response
            if (window.__EMRI_DEBUG) console.debug('[app.js] forcing sidebar visible pre-navigation');
            syncSidebar(false);
        } catch (e) {
            // ignore
        }
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
    // On initial load, ensure sidebar visibility matches server-rendered flag
    try {
        const hide = !!document.querySelector('[data-hide-sidebar]');
        if (window.__EMRI_DEBUG) console.debug('[app.js] DOMContentLoaded -> initial hide=', hide);
        syncSidebar(hide);
    } catch (e) {
        // ignore
    }
});

window.addEventListener('load', function () {
    hidePageLoading();
});

Alpine.start();
