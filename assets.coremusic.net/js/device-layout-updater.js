/**
 * CoreMusic — Device Layout Updater
 * Cihaz değişikliğinde HTML yapısını günceller.
 *
 * device-loader.js sadece CSS değiştirir.
 * Bu modül layout class'larını ve visibility'yi günceller.
 *
 * @module device-layout-updater
 * @version 1.0.0
 */
(function () {
    'use strict';

    /* ============================================================
       DEVICE LAYOUT CONFIG
       Her cihaz için hangi elementler gösterilir/gizlenir
       ============================================================ */
    var DEVICE_LAYOUT = {
        embedded: {
            navLinks: 4,        // PHP DeviceManager::NAV_LINKS: Ana Sayfa, Kütüphane, Radyo, Ayarlar
            widgets: 4,         // 2×2 grid
            recentCards: 3,     // PHP: recentCardCount() = 3
            playlists: 3,       // PHP: playlistCount() = 3
            upNext: 3,          // PHP: upNextCount() = 3
            showVolume: true,   // PNG home-1024: footer'da volume VAR (DeviceManager::showVolume() ile senkron)
            showFullMeta: false,
            showBattery: true,
            showSettingsBtn: true,
            showLogoutBtn: true,
        },
        phone: {
            navLinks: 3,        // PHP: Ana Sayfa, Kütüphane, Ayarlar
            widgets: 2,
            recentCards: 2,
            playlists: 2,
            upNext: 2,
            showVolume: false,
            showFullMeta: false,
            showBattery: false,
            showSettingsBtn: false,
            showLogoutBtn: false,
        },
        tablet: {
            navLinks: 5,        // PHP: Ana Sayfa, Keşfet, Albümler, Kütüphane, Ayarlar
            widgets: 4,
            recentCards: 4,
            playlists: 3,
            upNext: 3,
            showVolume: true,
            showFullMeta: true,
            showBattery: false,
            showSettingsBtn: true,
            showLogoutBtn: true,
        },
        laptop: {
            navLinks: 8,        // PHP: 8 links
            widgets: 4,
            recentCards: 5,
            playlists: 4,
            upNext: 4,
            showVolume: true,
            showFullMeta: true,
            showBattery: true,
            showSettingsBtn: true,
            showLogoutBtn: true,
        },
        desktop: {
            navLinks: 8,        // PHP: 8 links
            widgets: 6,
            recentCards: 7,
            playlists: 5,
            upNext: 6,
            showVolume: true,
            showFullMeta: true,
            showBattery: true,
            showSettingsBtn: true,
            showLogoutBtn: true,
        },
        '4k-tv': {
            navLinks: 7,        // PHP: 7 links (no /hakkimizda)
            widgets: 6,
            recentCards: 8,
            playlists: 6,
            upNext: 8,
            showVolume: true,
            showFullMeta: true,
            showBattery: true,
            showSettingsBtn: true,
            showLogoutBtn: true,
        },
        '4k-monitor': {
            navLinks: 8,        // PHP: 8 links
            widgets: 6,
            recentCards: 8,
            playlists: 6,
            upNext: 8,
            showVolume: true,
            showFullMeta: true,
            showBattery: true,
            showSettingsBtn: true,
            showLogoutBtn: true,
        },
    };

    /* ============================================================
       NAV LINK DEFINITIONS
       Her cihaz için hangi nav link'ler gösterilir
       NOT: Bu NAV_LINKS, DeviceManager.php'deki NAV_LINKS ile senkronize olmalı
       ============================================================ */
    var NAV_LINKS = {
        // PHP DeviceManager::NAV_LINKS ile birebir aynı
        embedded: [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kutuphane', label: 'Kütüphane' },
            { href: '/radyo', label: 'Radyo' },
            { href: '/ayarlar', label: 'Ayarlar' },
        ],
        phone: [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kutuphane', label: 'Kütüphane' },
            { href: '/ayarlar', label: 'Ayarlar' },
        ],
        tablet: [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kesfet', label: 'Keşfet' },
            { href: '/albumler', label: 'Albümler' },
            { href: '/kutuphane', label: 'Kütüphane' },
            { href: '/ayarlar', label: 'Ayarlar' },
        ],
        laptop: [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kesfet', label: 'Keşfet' },
            { href: '/albumler', label: 'Albümler' },
            { href: '/sanatcilar', label: 'Sanatçılar' },
            { href: '/goz-at', label: 'Göz At' },
            { href: '/gecmis', label: 'Geçmiş' },
            { href: '/ayarlar', label: 'Ayarlar' },
            { href: '/hakkimizda', label: 'Hakkımızda' },
        ],
        desktop: [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kesfet', label: 'Keşfet' },
            { href: '/albumler', label: 'Albümler' },
            { href: '/sanatcilar', label: 'Sanatçılar' },
            { href: '/goz-at', label: 'Göz At' },
            { href: '/gecmis', label: 'Geçmiş' },
            { href: '/ayarlar', label: 'Ayarlar' },
            { href: '/hakkimizda', label: 'Hakkımızda' },
        ],
        '4k-tv': [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kesfet', label: 'Keşfet' },
            { href: '/albumler', label: 'Albümler' },
            { href: '/sanatcilar', label: 'Sanatçılar' },
            { href: '/goz-at', label: 'Göz At' },
            { href: '/gecmis', label: 'Geçmiş' },
            { href: '/ayarlar', label: 'Ayarlar' },
        ],
        '4k-monitor': [
            { href: '/home', label: 'Ana Sayfa' },
            { href: '/kesfet', label: 'Keşfet' },
            { href: '/albumler', label: 'Albümler' },
            { href: '/sanatcilar', label: 'Sanatçılar' },
            { href: '/goz-at', label: 'Göz At' },
            { href: '/gecmis', label: 'Geçmiş' },
            { href: '/ayarlar', label: 'Ayarlar' },
            { href: '/hakkimizda', label: 'Hakkımızda' },
        ],
    };

    /* ============================================================
       HELPERS
       ============================================================ */

    /**
     * Element'i göster/gizle
     */
    function setVisible(el, visible) {
        if (!el) return;
        if (visible) {
            el.removeAttribute('hidden');
            el.style.display = '';
        } else {
            el.setAttribute('hidden', '');
            el.style.display = 'none';
        }
    }

    /* ============================================================
       LAYOUT UPDATER
       ============================================================ */

    /**
     * Map device type to 4 primary UI tiers
     */
    function getTier(device) {
        if (device === 'phone') return 'phone';
        // 4K devices use Wide tier — scaling is handled by d-4k.css (zoom), not JS transforms
        if (device === '4k-tv' || device === '4k-monitor') return 'wide';
        if (device === 'desktop' || device === 'laptop') return 'wide';
        return 'embedded';
    }

    /**
     * Sayfadaki tüm layout class'larını güncelle
     * BEM modifier'ları da dahil: site-header--*, footer--*, home-layout--*
     * @param {string} device  Yeni cihaz türü
     */
    function updateLayoutClasses(device) {
        var tier = getTier(device);

        // Main element — layout--* ve data-tier güncelle
        var main = document.querySelector('main.page-home, main.home-layout');
        if (main) {
            main.className = main.className.replace(/layout--\S+/g, '').replace(/home-layout--(phone|embedded|wide|4k)\b/g, '').trim();
            main.classList.add('layout--' + device);
            if (tier === 'phone') {
                main.classList.add('home-layout--phone');
            } else if (tier === '4k') {
                main.classList.add('home-layout--wide', 'home-layout--4k');
            } else if (tier === 'wide') {
                main.classList.add('home-layout--wide');
            } else {
                main.classList.add('home-layout--embedded');
            }
            main.setAttribute('data-tier', tier);
        }

        // Header — BEM modifier'ları güncelle
        var header = document.querySelector('.site-header');
        if (header) {
            header.className = header.className.replace(/site-header--(embedded|phone|tablet|laptop|desktop|4k-tv|4k-monitor|tv|wide|1920|1024)\b/g, '').trim();
            if (tier === 'phone') {
                header.classList.add('site-header--phone');
            } else if (tier === '4k') {
                header.classList.add('site-header--4k', 'site-header--tv', 'site-header--wide');
            } else if (tier === 'wide') {
                header.classList.add('site-header--wide', 'site-header--desktop', 'site-header--1920');
            } else {
                header.classList.add('site-header--1024', 'site-header--embedded');
            }
            header.classList.add('site-header--' + device);
            header.classList.add('layout--' + device);
        }

        // Footer — BEM modifier'ları güncelle
        var footer = document.querySelector('footer');
        if (footer) {
            footer.className = footer.className.replace(/footer--(mobile|embedded|phone|tablet|laptop|desktop|4k-tv|4k-monitor|tv|wide|1920|1024)\b/g, '').trim();
            if (tier === 'phone') {
                footer.classList.add('footer--mobile');
            } else if (tier === '4k') {
                footer.classList.add('footer--4k', 'footer--tv');
            } else if (tier === 'wide') {
                footer.classList.add('footer--wide', 'footer--desktop', 'footer--1920');
            } else {
                footer.classList.add('footer--1024', 'footer--embedded');
            }
            footer.classList.add('footer--' + device);
            footer.classList.add('layout--' + device);
        }
    }

    /**
     * Nav link'leri cihaza göre göster/gizle
     * XSS koruması: URL doğrulama + DocumentFragment ile toplu DOM güncellemesi
     * @param {string} device  Cihaz türü
     */
    function updateNavLinks(device) {
        var nav = document.querySelector('.site-header__nav');
        if (!nav) return;

        // Cihaz için link listesini al (yoksa desktop kullan)
        var links = NAV_LINKS[device] || NAV_LINKS.desktop;

        // DocumentFragment ile toplu DOM güncellemesi (reflow önleme)
        var fragment = document.createDocumentFragment();

        links.forEach(function (linkDef, index) {
            var a = document.createElement('a');

            // URL doğrulama — sadece relative path'e izin ver
            var href = linkDef.href;
            if (typeof href !== 'string' || !href.startsWith('/') || href.includes('://')) {
                return; // Zararlı URL'yi atla
            }

            a.href = href;
            a.className = 'nav-link';
            a.textContent = linkDef.label; // textContent güvenli (HTML injection yok)

            if (index === 0) {
                a.classList.add('active');
                a.setAttribute('aria-current', 'page');
            }

            fragment.appendChild(a);
        });

        // Mevcut child'ları temizle + fragment ekle (tek reflow)
        while (nav.firstChild) {
            nav.removeChild(nav.firstChild);
        }
        nav.appendChild(fragment);
    }

    /**
     * Widget sayısını cihaza göre güncelle
     * @param {string} device  Cihaz türü
     */
    function updateWidgets(device) {
        var config = DEVICE_LAYOUT[device] || DEVICE_LAYOUT.desktop;
        var grid = document.querySelector('.home-widget-grid');
        if (!grid) return;

        var widgets = grid.querySelectorAll('.home-widget');
        widgets.forEach(function (widget, index) {
            setVisible(widget, index < config.widgets);
        });

        // Grid sütun sayısını CSS variable ile güncelle
        var isWide = (device === 'desktop' || device === '4k-tv' || device === '4k-monitor');
        grid.style.setProperty('--widget-grid-cols', isWide ? '3' : '2');
    }

    /**
     * Kart sayısını cihaza göre güncelle
     * @param {string} device  Cihaz türü
     */
    function updateCards(device) {
        var config = DEVICE_LAYOUT[device] || DEVICE_LAYOUT.desktop;

        // Recent cards
        var recentSection = document.querySelector('.home-layout__bottom-left');
        if (recentSection) {
            var recentCards = recentSection.querySelectorAll('.media-card');
            recentCards.forEach(function (card, index) {
                setVisible(card, index < config.recentCards);
            });
        }

        // Playlists
        var playlistSection = document.querySelector('.home-layout__bottom-center');
        if (playlistSection) {
            var playlistCards = playlistSection.querySelectorAll('.media-card');
            playlistCards.forEach(function (card, index) {
                setVisible(card, index < config.playlists);
            });
        }

        // Up next
        var upNextSection = document.querySelector('.home-layout__bottom-right');
        if (upNextSection) {
            var upNextCards = upNextSection.querySelectorAll('.mini-card');
            upNextCards.forEach(function (card, index) {
                setVisible(card, index < config.upNext);
            });
        }
    }

    /**
     * Footer elementlerini cihaza göre güncelle
     * Phone'da: basitleştirilmiş 4-buton footer
     * Embedded'de: kompakt 90px touch player
     * Desktop/Laptop'da: tam Vaporwave player
     * @param {string} device  Cihaz türü
     */
    function updateFooter(device) {
        var config = DEVICE_LAYOUT[device] || DEVICE_LAYOUT.desktop;
        var isPhone = (device === 'phone');

        // Footer — phone'da footer--mobile, diğerlerinde cihaz adı
        var footer = document.querySelector('footer');
        if (footer) {
            if (isPhone) {
                // Phone: sadece basitleştirilmiş footer'ı göster
                setVisible(footer, true);
            }
        }

        // Volume section — phone'da gizle
        var volumeSection = document.querySelector('.footer__utility-section');
        if (volumeSection) {
            setVisible(volumeSection, config.showVolume);
        }

        // Full metadata — phone'da gizle
        var metaStack = document.querySelector('.footer__meta-stack');
        if (metaStack) {
            var albumName = metaStack.querySelector('.footer__album-name');
            var singerName = metaStack.querySelector('.footer__singer-name');
            var sure = metaStack.querySelector('.footer__sure');
            if (albumName) setVisible(albumName, config.showFullMeta);
            if (singerName) setVisible(singerName, config.showFullMeta);
            if (sure) setVisible(sure, config.showFullMeta);
        }

        // Album art boyutu — cihaza göre
        var albumArt = document.querySelector('.footer__album-art');
        if (albumArt) {
            var size = device === 'embedded' ? 80 : (device === '4k-tv' ? 140 : (device === 'desktop' ? 120 : 100));
            albumArt.style.width = size + 'px';
            albumArt.style.height = size + 'px';
        }
    }

    /**
     * Header elementlerini cihaza göre güncelle
     * Phone'da: site-header gizlenir, mobile-bottom-nav gösterilir (yoksa oluşturulur)
     * Diğer cihazlarda: site-header gösterilir, mobile-bottom-nav gizlenir
     * @param {string} device  Cihaz türü
     */
    function updateHeader(device) {
        var config = DEVICE_LAYOUT[device] || DEVICE_LAYOUT.desktop;
        var isPhone = (device === 'phone');

        // Site header — phone'da gizle, diğerlerinde göster
        var siteHeader = document.querySelector('.site-header');
        if (siteHeader) {
            setVisible(siteHeader, !isPhone);
        }

        // Mobile bottom nav — phone'da göster/gizle, yoksa oluştur
        var mobileNav = document.querySelector('.mobile-bottom-nav');
        if (isPhone) {
            if (!mobileNav) {
                // Mobile nav yoksa oluştur
                mobileNav = document.createElement('nav');
                mobileNav.className = 'mobile-bottom-nav';
                mobileNav.setAttribute('aria-label', 'Mobil navigasyon');
                mobileNav.innerHTML =
                    '<a href="/home" class="mobile-bottom-nav__link active" aria-current="page" data-no-spa>Ana Sayfa</a>' +
                    '<a href="/kesfet" class="mobile-bottom-nav__link" data-no-spa>Keşfet</a>' +
                    '<a href="/filemanager" class="mobile-bottom-nav__link" data-no-spa>Dosya</a>' +
                    '<a href="/about" class="mobile-bottom-nav__link" data-no-spa>Profil</a>';
                // Header'ın hemen önüne ekle
                if (siteHeader && siteHeader.parentNode) {
                    siteHeader.parentNode.insertBefore(mobileNav, siteHeader);
                } else {
                    document.body.prepend(mobileNav);
                }
            }
            setVisible(mobileNav, true);
        } else if (mobileNav) {
            setVisible(mobileNav, false);
        }

        // Battery pill
        var batteryPill = document.querySelector('.header-border--battery');
        if (batteryPill) {
            setVisible(batteryPill, config.showBattery);
        }

        // Settings button
        var settingsBtn = document.querySelector('.header-action-btn[aria-label="Ayarlar"]');
        if (settingsBtn) {
            setVisible(settingsBtn, config.showSettingsBtn);
        }

        // Logout button
        var logoutBtn = document.querySelector('.header-action-btn--logout');
        if (logoutBtn) {
            setVisible(logoutBtn, config.showLogoutBtn);
        }
    }

    /**
     * Welcome Modal gösterimini cihaza göre güncelle
     * SADECE 1024px gömülü (RPi5) cihazlarda gösterilir; masaüstü, laptop, TV ve telefonda gizlenir.
     * @param {string} device  Cihaz türü
     */
    function updateWelcomeModal(device) {
        var modal = document.getElementById('welcomeModalOverlay');
        if (!modal) return;

        var isEmbedded = (device === 'embedded');
        if (!isEmbedded) {
            // Masaüstü, laptop, 4K TV veya telefona geçildiğinde modalı kapat
            modal.style.display = 'none';
        } else if (!sessionStorage.getItem('cm_welcome_dismissed')) {
            modal.style.display = 'flex';
        }
    }

    /**
     * Tüm layout'u güncelle
     * @param {string} device  Cihaz türü
     */
    function updateAll(device) {
        updateLayoutClasses(device);
        updateNavLinks(device);
        updateWidgets(device);
        updateCards(device);
        updateFooter(device);
        updateHeader(device);
        updateWelcomeModal(device);
    }

    /* ============================================================
       EVENT LISTENERS
       ============================================================ */

    // devicechange event'ini dinle
    window.addEventListener('devicechange', function (e) {
        var device = e.detail?.device || 'desktop';
        updateAll(device);
    });

    // İlk yükleme
    document.addEventListener('DOMContentLoaded', function () {
        var device = window.CoreMusic?.deviceType || document.body?.dataset?.device || 'desktop';
        updateAll(device);
    });

    // CoreMusic global'ine ekle
    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.DeviceLayoutUpdater = {
        update: updateAll,
        updateLayoutClasses: updateLayoutClasses,
        updateNavLinks: updateNavLinks,
        updateWidgets: updateWidgets,
        updateCards: updateCards,
        updateFooter: updateFooter,
        updateHeader: updateHeader,
    };
})();
