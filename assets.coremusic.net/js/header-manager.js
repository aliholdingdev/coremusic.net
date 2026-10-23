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
            if (!this.userBtn || !this.dropdown) {
                return;
            }

            this.bindEvents();
            this.fetchHeaderData();
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
