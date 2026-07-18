/**
 * cb-loader.js — global loading-state UX.
 *
 * What it does:
 *   1. Auto-attaches to every <form> on submit:
 *      - Disables the clicked submit button
 *      - Replaces its label with a spinner + "Please wait..."
 *      - Re-enables on browser back-nav so the form is usable again
 *   2. <a data-loader> or <button data-loader> shows a spinner overlay before navigation.
 *   3. Razorpay / payment links (data-loader="payment") show a full-screen overlay.
 *
 * Opt out:
 *   - Add `data-no-loader` to a <form> or button to skip auto handling.
 *   - Add `data-loader-text="Saving..."` to override the loading label.
 */
(function () {
    'use strict';

    const SPINNER_SVG =
        '<svg class="cb-loader-spinner" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
        '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-opacity="0.25"/>' +
        '<path d="M22 12a10 10 0 0 1-10 10" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>' +
        '</svg>';

    function injectOverlayMarkup() {
        if (document.getElementById('cb-loader-overlay')) return;
        const wrap = document.createElement('div');
        wrap.id = 'cb-loader-overlay';
        wrap.setAttribute('role', 'status');
        wrap.setAttribute('aria-live', 'polite');
        wrap.setAttribute('aria-hidden', 'true');
        wrap.innerHTML =
            '<div class="cb-loader-overlay__panel">' +
            '<div class="cb-loader-logo-container">' +
            '<img src="/images/chandla-favicon.png" alt="Chandla Book" class="cb-loader-logo">' +
            '<span class="cb-loader-overlay__spinner">' + SPINNER_SVG + '</span>' +
            '</div>' +
            '<p class="cb-loader-overlay__text">Please wait…</p>' +
            '<p class="cb-loader-overlay__sub" id="cb-loader-overlay-sub"></p>' +
            '</div>';
        document.body.appendChild(wrap);
    }

    function showOverlay(text, sub) {
        injectOverlayMarkup();
        const el = document.getElementById('cb-loader-overlay');
        const textEl = el.querySelector('.cb-loader-overlay__text');
        const subEl = el.querySelector('#cb-loader-overlay-sub');
        if (text && textEl) textEl.textContent = text;
        if (sub && subEl) {
            subEl.textContent = sub;
            subEl.style.display = '';
        } else if (subEl) {
            subEl.style.display = 'none';
        }
        el.classList.add('cb-loader-overlay--visible');
        el.setAttribute('aria-hidden', 'false');
    }

    function hideOverlay() {
        const el = document.getElementById('cb-loader-overlay');
        if (!el) return;
        el.classList.remove('cb-loader-overlay--visible');
        el.setAttribute('aria-hidden', 'true');
    }

    function setButtonLoading(btn, customText) {
        if (!btn || btn.dataset.cbLoaderActive === '1') return;
        btn.dataset.cbLoaderActive = '1';
        btn.dataset.cbLoaderOriginal = btn.innerHTML;
        const label = customText || btn.dataset.loaderText || 'Please wait…';
        btn.innerHTML = '<span class="cb-loader-btn-inner">' + SPINNER_SVG + '<span>' + label + '</span></span>';
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        btn.classList.add('cb-btn-loading');
    }

    function unsetButtonLoading(btn) {
        if (!btn || btn.dataset.cbLoaderActive !== '1') return;
        if (btn.dataset.cbLoaderOriginal !== undefined) {
            btn.innerHTML = btn.dataset.cbLoaderOriginal;
            delete btn.dataset.cbLoaderOriginal;
        }
        btn.disabled = false;
        btn.removeAttribute('aria-busy');
        btn.classList.remove('cb-btn-loading');
        delete btn.dataset.cbLoaderActive;
    }

    // ----- Form submit handler -----
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (form.hasAttribute('data-no-loader')) return;
        // Skip GET forms (filters/search) — no loader needed for a regular nav.
        // But honor data-loader override.
        const method = (form.method || 'get').toLowerCase();
        const forceLoader = form.hasAttribute('data-loader');
        if (method === 'get' && !forceLoader) return;

        // Find the submit button that triggered this submission.
        const submitter = e.submitter;
        const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type])');
        buttons.forEach(function (b) {
            if (b.hasAttribute('data-no-loader')) return;
            if (submitter && b !== submitter) {
                // Disable other submits to prevent double-submission, but don't change their look.
                b.disabled = true;
                b.dataset.cbLoaderSilenced = '1';
                return;
            }
            setButtonLoading(b);
        });

        // Show global full-screen overlay with logo on form submits
        const loaderText = form.getAttribute('data-loader-text') || (submitter ? submitter.getAttribute('data-loader-text') : null) || 'Please wait…';
        showOverlay(loaderText);
    }, true);

    // Re-enable buttons when the page is restored from bfcache (browser back).
    window.addEventListener('pageshow', function (e) {
        if (!e.persisted) return;
        document.querySelectorAll('[data-cb-loader-active="1"]').forEach(unsetButtonLoading);
        document.querySelectorAll('[data-cb-loader-silenced="1"]').forEach(function (b) {
            b.disabled = false;
            delete b.dataset.cbLoaderSilenced;
        });
        hideOverlay();
    });

    // ----- Link / button click handler (overlay) -----
    document.addEventListener('click', function (e) {
        let el = e.target;
        // Walk up to find <a> or <button> or element with data-loader
        while (el && el !== document.body) {
            if (el.tagName === 'A' || el.tagName === 'BUTTON' || (el.hasAttribute && el.hasAttribute('data-loader'))) break;
            el = el.parentElement;
        }
        if (!el || el === document.body) return;
        if (el.hasAttribute('data-no-loader')) return;

        // Skip new-tab / cmd-click / right-click
        if (el.target === '_blank') return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 2) return;

        let shouldShowLoader = false;
        let text = 'Please wait…';
        let sub = '';

        const kind = el.getAttribute('data-loader');
        if (kind) {
            shouldShowLoader = true;
            if (kind === 'payment') {
                text = 'Redirecting to secure payment…';
                sub = "Please don't close this tab.";
            } else if (kind === 'download') {
                text = 'Preparing your download…';
                sub = 'This may take a moment.';
            } else if (kind !== 'true' && kind !== '') {
                text = kind;
            }
        } else if (el.tagName === 'A') {
            // For general <a> links, check if it's a real internal page transition
            const href = el.getAttribute('href');
            if (href && !href.startsWith('#') && !href.startsWith('mailto:') && !href.startsWith('tel:') && !href.startsWith('javascript:') && href !== '') {
                const isInternal = el.hostname === window.location.hostname || href.startsWith('/') || !href.includes('://');
                if (isInternal) {
                    shouldShowLoader = true;
                    text = 'Loading…';
                }
            }
        }

        if (shouldShowLoader) {
            showOverlay(text, sub);
            // Auto-hide after 30s as a safety net
            setTimeout(hideOverlay, 30000);
        }
    }, true);

    // Hide preloader when the page has finished loading
    function initPreloader() {
        hideOverlay();
    }
    if (document.readyState === 'complete') {
        initPreloader();
    } else {
        window.addEventListener('load', initPreloader);
    }

    // Expose for manual triggers if a page wants them.
    window.cbLoader = {
        show: showOverlay,
        hide: hideOverlay,
        button: setButtonLoading,
        unbutton: unsetButtonLoading,
    };
})();
