import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

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
    const newContentWrapper = doc.getElementById('page-content-wrapper');
    const contentWrapper = document.getElementById('page-content-wrapper');
    const newTitle = doc.querySelector('title');

    if (!newContentWrapper || !contentWrapper) {
        return false;
    }

    contentWrapper.innerHTML = newContentWrapper.innerHTML;

    if (newTitle) {
        document.title = newTitle.textContent;
    }

    executeInlineScripts(contentWrapper);

    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
        window.Alpine.initTree(contentWrapper);
    } else if (window.Alpine && typeof window.Alpine.discoverUninitializedComponents === 'function') {
        window.Alpine.discoverUninitializedComponents(contentWrapper);
    }

    document.dispatchEvent(new Event('DOMContentLoaded'));

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
