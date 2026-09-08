;(function (window) {
    'use strict';

    /**
     * helper.js — CoreMusic JS Temel Yardımcılar
     *
     * Bağımlılık sırası: Bu dosya tüm router ve player dosyalarından ÖNCE yüklenmeli.
     * index.php'de ilk <script> tag olarak yer alır.
     *
     * Klasik IIFE (ES2025 modül değil) — eski scriptler senkron bağımlılık bekler.
     *
     * İçerik:
     *   - window.dbg : debug logger (AppConfig.verboseConsole() guard'lı)
     *   - window.escapeHtml : XSS kaçış fonksiyonu (DOM-based, güvenli)
     *   - window.formatTime : HH:MM:SS süre formatlayıcı
     *   - window.CoreHelper : birleşik API — cookies, dom, time
     */

    function isVerbose() {
        return !!(window.AppConfig && window.AppConfig.debug);
    }

    const dbg = {
        log: function () {
            if (isVerbose() && window.console) {
                window.console.log.apply(window.console, arguments);
            }
        },
        warn: function () {
            if (isVerbose() && window.console) {
                window.console.warn.apply(window.console, arguments);
            }
        },
        error: function () {
            if (isVerbose() && window.console) {
                window.console.error.apply(window.console, arguments);
            }
        }
    };

    window.dbg = dbg;

    function escapeHtml(str) {
        if (str === null || str === undefined) { return ''; }
        const div = document.createElement('div');
        div.textContent = String(str);
        return div.innerHTML;
    }

    window.escapeHtml = escapeHtml;

    let cookieCache = Object.create(null);
    let cookieVersion = 0;
    let cookieCachedVersion = -1;

    function readAllCookies() {
        const map = Object.create(null);
        const raw = document.cookie || '';
        if (raw === '') { return map; }
        const parts = raw.split(';');
        for (let i = 0; i < parts.length; i++) {
            const p = parts[i].trim();
            if (p === '') { continue; }
            const eq = p.indexOf('=');
            if (eq < 0) {
                map[decodeURIComponent(p)] = '';
            } else {
                const k = decodeURIComponent(p.substring(0, eq));
                const v = decodeURIComponent(p.substring(eq + 1));
                map[k] = v;
            }
        }
        return map;
    }

    function cookiesGet(name) {
        if (cookieCachedVersion !== cookieVersion) {
            cookieCache = readAllCookies();
            cookieCachedVersion = cookieVersion;
        }
        return Object.prototype.hasOwnProperty.call(cookieCache, name)
            ? cookieCache[name]
            : null;
    }

    function cookiesSet(name, value, days) {
        let expires = '';
        if (days) {
            const d = new Date();
            d.setTime(d.getTime() + (days * 86400000));
            expires = '; expires=' + d.toUTCString();
        }
        document.cookie = name + '=' + (value == null ? '' : value) + expires + '; path=/';
        cookieVersion++;
    }

    function cookiesInvalidate() {
        cookieVersion++;
    }

    if (typeof window.setCookie === 'function' && !window.setCookie.__cmWrapped) {
        const legacySet = window.setCookie;
        const wrappedSet = function (name, value, days) {
            legacySet(name, value, days);
            cookieVersion++;
        };
        wrappedSet.__cmWrapped = true;
        window.setCookie = wrappedSet;
    }

    function $(id) {
        return document.getElementById(id);
    }

    function $$(selector, root) {
        return (root || document).querySelectorAll(selector);
    }

    function once(target, eventName, handler, options) {
        if (!target || typeof target.addEventListener !== 'function') {
            return function noop() {};
        }
        target.addEventListener(eventName, handler, options);
        const detach = function () {
            try {
                target.removeEventListener(eventName, handler, options);
            } catch (e) {
                if (window.dbg) { window.dbg.warn('[helper.once] removeEventListener failed:', e); }
            }
        };
        if (window.CoreCleanup && typeof window.CoreCleanup.register === 'function') {
            window.CoreCleanup.register(detach);
        }
        return detach;
    }

    function formatTime(seconds) {
        const s       = Math.floor(seconds || 0);
        const hours   = Math.floor(s / 3600);
        const minutes = Math.floor((s % 3600) / 60);
        const sec     = s % 60;
        return (
            String(hours).padStart(2, '0') + ':' +
            String(minutes).padStart(2, '0') + ':' +
            String(sec).padStart(2, '0')
        );
    }

    window.formatTime = formatTime;

    window.CoreHelper = {
        dbg:         dbg,
        escapeHtml:  escapeHtml,
        cookies: {
            get:        cookiesGet,
            set:        cookiesSet,
            invalidate: cookiesInvalidate
        },
        dom: {
            $:    $,
            $$:   $$,
            once: once
        },
        time: {
            format: formatTime
        }
    };

})(window);
