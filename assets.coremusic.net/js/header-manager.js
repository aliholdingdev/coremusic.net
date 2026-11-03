/**
 * header-manager.js — CoreMusic Global Header & Dropdown Etkileşim Yöneticisi
 *
 * Görevler:
 * 1. Header Dropdown menü açma/kapama, klavye ve click-outside yönetimi (WCAG 2.2 AA).
 * 2. api.coremusic.net/api/v1/user/header-data endpoint'inden kullanıcı, rol ve playlist verilerini dinamik yükleme.
 * 3. SPA geçişlerinde senkronizasyonu koruma.
 */
(function () {
    'use strict';

    class HeaderManager {
        constructor() {
            this.userBtn = document.querySelector('.header-user');
            this.dropdown = document.querySelector('.header-user-dropdown');
            this.nameEl = document.querySelector('.header-user__name');
            this.avatarEl = document.querySelector('.header-user__avatar');
            this.apiUrl = window.API_URL || 'https://api.coremusic.net';
            
            this.init();
        }

        init() {
            this.initQuickPanels();

            if (!this.userBtn || !this.dropdown) {
                return;
            }

            this.bindEvents();
            this.fetchHeaderData();
        }

        initQuickPanels() {
            const wifiBtn = document.querySelector('.header-widget--signal');
            const btBtn = document.querySelector('.header-widget--bt');
            const wifiOverlay = document.getElementById('quick-wifi-overlay');
            const btOverlay = document.getElementById('quick-bt-overlay');
            const wifiClose = document.getElementById('quick-wifi-close');
            const btClose = document.getElementById('quick-bt-close');
            const settingsBtn = document.getElementById('header-settings-btn');
            const settingsOverlay = document.getElementById('quick-settings-overlay');
            const settingsClose = document.getElementById('quick-settings-close');

            const openOverlay = (el) => {
                if (wifiOverlay) wifiOverlay.classList.remove('is-active');
                if (btOverlay) btOverlay.classList.remove('is-active');
                if (settingsOverlay) settingsOverlay.classList.remove('is-active');
                if (el) el.classList.add('is-active');
            };

            const closeOverlays = () => {
                if (wifiOverlay) wifiOverlay.classList.remove('is-active');
                if (btOverlay) btOverlay.classList.remove('is-active');
                if (settingsOverlay) settingsOverlay.classList.remove('is-active');
            };

            if (wifiBtn && wifiOverlay) {
                wifiBtn.setAttribute('role', 'button');
                wifiBtn.setAttribute('tabindex', '0');
                wifiBtn.setAttribute('aria-haspopup', 'dialog');
                wifiBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openOverlay(wifiOverlay);
                });
                wifiBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openOverlay(wifiOverlay);
                    }
                });
            }

            if (btBtn && btOverlay) {
                btBtn.setAttribute('role', 'button');
                btBtn.setAttribute('tabindex', '0');
                btBtn.setAttribute('aria-haspopup', 'dialog');
                btBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openOverlay(btOverlay);
                });
                btBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openOverlay(btOverlay);
                    }
                });
            }

            if (settingsBtn && settingsOverlay) {
                settingsBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    openOverlay(settingsOverlay);
                });
                settingsBtn.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        openOverlay(settingsOverlay);
                    }
                });
            }

            if (wifiClose) wifiClose.addEventListener('click', closeOverlays);
            if (btClose) btClose.addEventListener('click', closeOverlays);
            if (settingsClose) settingsClose.addEventListener('click', closeOverlays);

            [wifiOverlay, btOverlay, settingsOverlay].forEach(ov => {
                if (ov) {
                    ov.addEventListener('click', (e) => {
                        if (e.target === ov) closeOverlays();
                    });
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if ((wifiOverlay && wifiOverlay.classList.contains('is-active')) ||
                        (btOverlay && btOverlay.classList.contains('is-active')) ||
                        (settingsOverlay && settingsOverlay.classList.contains('is-active'))) {
                        closeOverlays();
                    }
                }
            });

            // Settings modal tab switching
            if (settingsOverlay) {
                const tabBtns = settingsOverlay.querySelectorAll('.quick-settings-tab-btn');
                tabBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const targetTab = btn.getAttribute('data-qs-tab');
                        tabBtns.forEach(b => b.classList.remove('is-active'));
                        btn.classList.add('is-active');

                        settingsOverlay.querySelectorAll('.quick-settings-tab-pane').forEach(pane => {
                            pane.classList.remove('is-active');
                        });
                        const targetPane = document.getElementById(`qs-pane-${targetTab}`);
                        if (targetPane) targetPane.classList.add('is-active');
                    });
                });

                // Theme swatch selection
                settingsOverlay.querySelectorAll('[data-qs-theme]').forEach(swatch => {
                    swatch.addEventListener('click', function() {
                        const th = this.getAttribute('data-qs-theme');
                        settingsOverlay.querySelectorAll('[data-qs-theme]').forEach(s => s.classList.remove('is-active'));
                        this.classList.add('is-active');
                        document.documentElement.setAttribute('data-theme', th);
                        try { localStorage.setItem('cm_theme', th); } catch(e){}
                    });
                });

                // Mode pill selection
                settingsOverlay.querySelectorAll('[data-qs-mode]').forEach(pill => {
                    pill.addEventListener('click', function() {
                        const m = this.getAttribute('data-qs-mode');
                        settingsOverlay.querySelectorAll('[data-qs-mode]').forEach(p => p.classList.remove('is-active'));
                        this.classList.add('is-active');
                        document.documentElement.setAttribute('data-mode', m);
                        try { localStorage.setItem('cm_mode', m); } catch(e){}
                    });
                });

                // Volume slider
                const qsVol = document.getElementById('qs-volume-slider');
                const qsVal = document.getElementById('qs-volume-val');
                if (qsVol && qsVal) {
                    qsVol.addEventListener('input', function() {
                        qsVal.textContent = this.value + '%';
                    });
                }
            }

            // Quick actions
            const wifiToggle = document.getElementById('quick-wifi-toggle');
            const wifiList = document.getElementById('quick-wifi-available-list');
            if (wifiToggle && wifiList) {
                wifiToggle.addEventListener('change', function() {
                    wifiList.style.opacity = this.checked ? '1' : '0.25';
                    wifiList.style.pointerEvents = this.checked ? 'auto' : 'none';
                });
            }

            const btToggle = document.getElementById('quick-bt-toggle');
            const btList = document.getElementById('quick-bt-available-list');
            if (btToggle && btList) {
                btToggle.addEventListener('change', function() {
                    btList.style.opacity = this.checked ? '1' : '0.25';
                    btList.style.pointerEvents = this.checked ? 'auto' : 'none';
                });
            }

            document.querySelectorAll('[data-action="quick-connect-wifi"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const ssid = this.getAttribute('data-ssid') || '';
                    const curName = document.getElementById('quick-wifi-current-name');
                    if (curName && ssid) curName.textContent = ssid;
                    this.textContent = 'Bağlandı';
                    setTimeout(() => closeOverlays(), 400);
                });
            });

            document.querySelectorAll('[data-action="quick-connect-bt"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const dev = this.getAttribute('data-dev') || '';
                    const curName = document.getElementById('quick-bt-current-name');
                    if (curName && dev) curName.textContent = dev;
                    this.textContent = 'Bağlandı';
                    setTimeout(() => closeOverlays(), 400);
                });
            });

            const wifiDisc = document.getElementById('quick-wifi-disconnect');
            if (wifiDisc) {
                wifiDisc.addEventListener('click', function() {
                    const curName = document.getElementById('quick-wifi-current-name');
                    if (curName) curName.textContent = 'Bağlantı Yok';
                    this.textContent = 'Ayrıldı';
                });
            }

            const btDisc = document.getElementById('quick-bt-disconnect');
            if (btDisc) {
                btDisc.addEventListener('click', function() {
                    const curName = document.getElementById('quick-bt-current-name');
                    if (curName) curName.textContent = 'Bağlantı Yok';
                    this.textContent = 'Ayrıldı';
                });
            }
        }


        bindEvents() {
            // Tıklama ile menü aç/kapa
            this.userBtn.addEventListener('click', (e) => {
                // Eğer menü içi linke tıklandıysa kapanmayı engelleme (link yönlensin)
                if (e.target.closest('.header-user-dropdown a')) {
                    this.closeDropdown();
                    return;
                }
                e.stopPropagation();
                this.toggleDropdown();
            });

            // Klavye ile menü kontrolü (Enter, Space, Escape)
            this.userBtn.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.toggleDropdown();
                } else if (e.key === 'Escape') {
                    this.closeDropdown();
                }
            });

            // Dışarı tıklandığında menüyü kapat (Click-Outside)
            document.addEventListener('click', (e) => {
                if (!this.userBtn.contains(e.target)) {
                    this.closeDropdown();
                }
            });

            // Escape ile kapatma
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isDropdownOpen()) {
                    this.closeDropdown();
                    this.userBtn.focus();
                }
            });
        }

        toggleDropdown() {
            if (this.isDropdownOpen()) {
                this.closeDropdown();
            } else {
                this.openDropdown();
            }
        }

        openDropdown() {
            this.dropdown.classList.add('header-user-dropdown--open');
            this.userBtn.setAttribute('aria-expanded', 'true');
        }

        closeDropdown() {
            this.dropdown.classList.remove('header-user-dropdown--open');
            this.userBtn.setAttribute('aria-expanded', 'false');
        }

        isDropdownOpen() {
            return this.dropdown.classList.contains('header-user-dropdown--open');
        }

        async fetchHeaderData() {
            try {
                const response = await fetch(`${this.apiUrl}/api/v1/user/header-data`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    },
                    credentials: 'include', // Cookie ve session için
                });

                if (!response.ok) {
                    this.#resetMenu();
                    return;
                }

                const result = await response.json();
                if (!result.success || !result.data) {
                    this.#resetMenu();
                    return;
                }

                this.renderHeaderData(result.data);
            } catch (err) {
                // Sessiz başarısızlık — offline veya ağ hatası durumunda mevcut UI korunur
                // (Adım 11 §9: eski menü verisiyle devam YASAK — menü alanı sıfırlanır,
                // statik PHP öğeleri görünür geri gelir)
                this.#resetMenu();
                console.debug('[HeaderManager] Header verisi alınamadı:', err);
            }
        }

        /**
         * Rol bazlı menü (Adım 11) — sunucu filtresinden gelen öğeler.
         * Hard Rule 2: menü öğesi API'den gelmiyorsa DOM'a hiç basılmaz;
         * hard rule 3: aynı-origin rota SPA router delegate'ine bırakılır
         * (RouterEventManager: data-no-spa OLMAYAN same-origin link otomatik
         * preventDefault + navigate eder — kendi delegate'imiz yok, çift tetikleme riski yok).
         * XSS: textContent-only · idempotent: replaceChildren.
         *
         * @param {Array|null} menu
         */
        #renderMenu(menu) {
            if (!Array.isArray(menu)) {
                return; // Sözleşmede alan yoksa dokunma (fail-safe)
            }

            // header.php statik öğeleri (Ayarlar/Geçmiş) — logout HARİÇ
            const staticLinks = [...this.dropdown.querySelectorAll('a[role="menuitem"]:not(.logout-btn):not(.header-dropdown-menu a):not(.header-dropdown-playlists a)')];
            let menuContainer = this.dropdown.querySelector('.header-dropdown-menu');

            if (menu.length === 0) {
                // Menü boş (ör. anonim) → statik öğeler de gizli (API'den gelmeyen öğe DOM'da olmaz)
                if (menuContainer) {
                    menuContainer.hidden = true;
                }
                staticLinks.forEach((el) => { el.hidden = true; });
                return;
            }

            if (!menuContainer) {
                menuContainer = document.createElement('div');
                menuContainer.className = 'header-dropdown-menu';
                menuContainer.setAttribute('role', 'presentation');
                const logoutBtn = this.dropdown.querySelector('.logout-btn');
                if (logoutBtn) {
                    this.dropdown.insertBefore(menuContainer, logoutBtn);
                } else {
                    this.dropdown.appendChild(menuContainer);
                }
            }

            menuContainer.replaceChildren();
            menu.forEach((item) => {
                if (!item || typeof item.route !== 'string' || item.route === '') return;
                if (typeof item.label !== 'string' || item.label === '') return;
                const link = document.createElement('a');
                link.href = item.route;
                link.setAttribute('role', 'menuitem');
                link.setAttribute('data-route', 'true');
                // Aynı origin olmayan rota (ör. admin alt domain) SPA delegate'inin dışındadır
                if (/^https?:\/\//i.test(item.route) && !item.route.startsWith(window.location.origin)) {
                    link.setAttribute('data-no-spa', '');
                    link.setAttribute('rel', 'external');
                }
                link.textContent = item.label;
                menuContainer.appendChild(link);
            });
            menuContainer.hidden = false;

            // Statik öğeler API menüsüyle değiştirildi (çift menü YOK)
            staticLinks.forEach((el) => { el.hidden = true; });
        }

        /**
         * Menü sıfırlama (Adım 11 §9) — fetch başarısız/401/oturum düştüğünde
         * eski API menüsü gösterilmez; statik PHP öğeleri görünür geri gelir.
         */
        #resetMenu() {
            const menuContainer = this.dropdown.querySelector('.header-dropdown-menu');
            if (menuContainer) {
                menuContainer.hidden = true;
            }
            this.dropdown.querySelectorAll('a[role="menuitem"]:not(.logout-btn):not(.header-dropdown-menu a):not(.header-dropdown-playlists a)')
                .forEach((el) => { el.hidden = false; });
        }

        renderHeaderData(data) {
            // Kullanıcı adı ve rol
            if (data.user && this.nameEl) {
                const username = data.user.username || 'Kullanıcı';
                const role = data.user.role || 'user';
                this.nameEl.textContent = `${username} (${role.toUpperCase()})`;
            }

            // Playlist Kısayolları Ekleme (Adım 10: idempotent + inline style YOK — CSP)
            if (Array.isArray(data.playlist_shortcuts) && data.playlist_shortcuts.length > 0) {
                let playlistContainer = this.dropdown.querySelector('.header-dropdown-playlists');
                if (!playlistContainer) {
                    playlistContainer = document.createElement('div');
                    playlistContainer.className = 'header-dropdown-playlists';

                    // Çıkış yap butonundan hemen önce ekle
                    const logoutBtn = this.dropdown.querySelector('.logout-btn');
                    if (logoutBtn) {
                        this.dropdown.insertBefore(playlistContainer, logoutBtn);
                    } else {
                        this.dropdown.appendChild(playlistContainer);
                    }
                }

                // Yeniden render'da önceki içeriği temizle (çoğalma yok)
                playlistContainer.replaceChildren();

                const title = document.createElement('span');
                title.className = 'header-dropdown-playlists__title';
                title.textContent = 'Çalma Listelerim';
                playlistContainer.appendChild(title);

                data.playlist_shortcuts.forEach(pl => {
                    const link = document.createElement('a');
                    link.href = `/playlist?id=${pl.playlist_id}`;
                    link.setAttribute('role', 'menuitem');
                    link.textContent = `🎵 ${pl.name}`;
                    playlistContainer.appendChild(link);
                });
            }

            // Okunmamış Bildirim Rozeti (Adım 10: inline style YOK — stil Css/03_Layout/_header.css)
            if (typeof data.unread_notifications === 'number') {
                let badge = document.querySelector('.header-notification-badge');
                const notifBtn = document.querySelector('.header-action--notifications') || document.querySelector('[data-action="notifications"]');
                if (data.unread_notifications > 0 && notifBtn) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'header-notification-badge';
                        badge.setAttribute('aria-hidden', 'true');
                        notifBtn.appendChild(badge);
                    }
                    badge.textContent = String(data.unread_notifications);
                    badge.hidden = false;
                } else if (badge) {
                    badge.hidden = true;
                }
            }

            // Arama Önerileri Datalist (Adım 10: innerHTML YOK — replaceChildren)
            if (Array.isArray(data.search_suggestions) && data.search_suggestions.length > 0) {
                const searchInput = document.querySelector('.header-search__input') || document.querySelector('input[type="search"]');
                if (searchInput) {
                    let datalist = document.getElementById('header-search-suggestions');
                    if (!datalist) {
                        datalist = document.createElement('datalist');
                        datalist.id = 'header-search-suggestions';
                        document.body.appendChild(datalist);
                        searchInput.setAttribute('list', 'header-search-suggestions');
                    }
                    datalist.replaceChildren();
                    data.search_suggestions.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.title;
                        opt.label = item.artist_name ? `${item.title} — ${item.artist_name}` : item.title;
                        datalist.appendChild(opt);
                    });
                }
            }

            // Rol bazlı menü (Adım 11) — sunucu filtresi geldi, statik öğelerin yerini alır
            if ('menu' in data) {
                this.#renderMenu(data.menu);
            }
        }
    }

    // DOM Hazır olduğunda başlat
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => new HeaderManager());
    } else {
        new HeaderManager();
    }
})();
