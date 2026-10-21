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
            this.apiUrl = window.API_URL || 'http://api.coremusic.net';
            
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
                    return;
                }

                const result = await response.json();
                if (!result.success || !result.data) {
                    return;
                }

                this.renderHeaderData(result.data);
            } catch (err) {
                // Sessiz başarısızlık — offline veya ağ hatası durumunda mevcut UI korunur
                console.debug('[HeaderManager] Header verisi alınamadı:', err);
            }
        }

        renderHeaderData(data) {
            // Kullanıcı adı ve rol
            if (data.user && this.nameEl) {
                const username = data.user.username || 'Kullanıcı';
                const role = data.user.role || 'user';
                this.nameEl.textContent = `${username} (${role.toUpperCase()})`;
            }

            // Playlist Kısayolları Ekleme
            if (data.playlist_shortcuts && Array.isArray(data.playlist_shortcuts) && data.playlist_shortcuts.length > 0) {
                let playlistContainer = this.dropdown.querySelector('.header-dropdown-playlists');
                if (!playlistContainer) {
                    playlistContainer = document.createElement('div');
                    playlistContainer.className = 'header-dropdown-playlists';
                    playlistContainer.style.borderTop = '1px solid rgba(255, 255, 255, 0.1)';
                    playlistContainer.style.paddingTop = '6px';
                    playlistContainer.style.marginTop = '4px';

                    const title = document.createElement('span');
                    title.textContent = 'Çalma Listelerim';
                    title.style.fontSize = '11px';
                    title.style.color = 'rgba(255, 255, 255, 0.5)';
                    title.style.padding = '0 8px 4px 8px';
                    title.style.display = 'block';
                    title.style.textTransform = 'uppercase';
                    title.style.letterSpacing = '0.5px';
                    playlistContainer.appendChild(title);

                    // Çıkış yap butonundan hemen önce ekle
                    const logoutBtn = this.dropdown.querySelector('.logout-btn');
                    if (logoutBtn) {
                        this.dropdown.insertBefore(playlistContainer, logoutBtn);
                    } else {
                        this.dropdown.appendChild(playlistContainer);
                    }
                }

                data.playlist_shortcuts.forEach(pl => {
                    const link = document.createElement('a');
                    link.href = `/playlist?id=${pl.playlist_id}`;
                    link.setAttribute('role', 'menuitem');
                    link.textContent = `🎵 ${pl.name}`;
                    link.style.fontSize = '12px';
                    link.style.padding = '6px 8px';
                    playlistContainer.appendChild(link);
                });
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
