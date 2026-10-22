/**
 * music-spa-manager.js — CoreMusic SPA Müzik Akışı, Dinleme Geçmişi ve Playlist Yöneticisi
 *
 * Görevler:
 * 1. Sayfa yenilenmeden (SPA) şarkı tıklamalarını yakalama ve akışı başlatma.
 * 2. Şarkı çalındığında POST /api/v1/music/history ile dinleme kaydı oluşturma.
 * 3. Recent Tracks ve History sayfalarının canlı (DOM) veri senkronizasyonu.
 * 4. Playlist oluşturma, şarkı ekleme işlemleri ve erişilebilir (WCAG 2.2 AA) modal UI.
 * 5. Toast bildirim mekanizması.
 */
(function () {
    'use strict';

    // Görsel varlıkların servis edildiği origin — desen: ViewModeManager.js / UrlUtils.js
    const ASSETS_ORIGIN = window.CoreMusic?.RouterConfig?.assetsUrl || 'https://assets.coremusic.net';

    class MusicSpaManager {
        constructor() {
            this.apiUrl = window.API_URL || 'https://api.coremusic.net';
            this.currentTrack = null;
            this.init();
        }

        init() {
            this.bindTrackClicks();
            this.bindContextMenu();
            this.observeDomChanges();
            this.injectStyles();
            this.loadInitialData();
        }

        bindTrackClicks() {
            document.addEventListener('click', (e) => {
                // Playlist ekle butonu tıklandıysa oynatmayı tetikleme
                if (e.target.closest('[data-action="add-to-playlist"]')) {
                    e.preventDefault();
                    e.stopPropagation();
                    const trigger = e.target.closest('[data-action="add-to-playlist"]');
                    const card = trigger.closest('[data-stream], .home-song__mini-card, .history-row');
                    if (card) {
                        this.openAddToPlaylistModal(this.extractTrackInfo(card));
                    }
                    return;
                }

                const trackLink = e.target.closest('a[data-stream], [data-music-id], .home_song__btn, .mini-card, .home-song__mini-card, .history-row');
                if (!trackLink) {
                    return;
                }

                const streamUrl = trackLink.getAttribute('data-stream') || trackLink.getAttribute('href');
                if (!streamUrl || !streamUrl.includes('/stream/')) {
                    return;
                }

                // Sayfa yenilenmesini kesinlikle engelle
                e.preventDefault();
                e.stopPropagation();

                // Parça bilgilerini ayıkla
                const trackInfo = this.extractTrackInfo(trackLink);
                this.currentTrack = trackInfo;

                // Player bar'ı tetikle
                this.playTrack(streamUrl, trackLink, trackInfo);

                // Dinleme geçmişini API'ye kaydet
                if (trackInfo.hex) {
                    this.recordHistory(trackInfo);
                }
            });
        }

        bindContextMenu() {
            // Şarkı kartlarında sağ tık ile "Playlist'e Ekle" menüsünü tetikleme
            document.addEventListener('contextmenu', (e) => {
                const card = e.target.closest('a[data-stream], .home-song__mini-card, .history-row');
                if (!card) {
                    return;
                }
                const trackInfo = this.extractTrackInfo(card);
                if (trackInfo.hex) {
                    e.preventDefault();
                    this.openAddToPlaylistModal(trackInfo);
                }
            });
        }

        extractTrackInfo(el) {
            const streamUrl = el.getAttribute('data-stream') || el.getAttribute('href') || '';
            const match = streamUrl.match(/\/stream\/([a-f0-9]{32})/i);
            const hex = match ? match[1] : (el.getAttribute('data-music-id') || '');
            
            const titleEl = el.querySelector('.home-song__mini-card__title, .history-row__title, .mini-card__title');
            const artistEl = el.querySelector('.home-song__mini-card__subtitle, .history-row__artist, .mini-card__artist');
            const durationEl = el.querySelector('.history-row__duration, .home-song__mini-card__subtitle');
            const imgEl = el.querySelector('img');

            const title = el.getAttribute('data-title') || (titleEl ? titleEl.textContent.trim() : 'Bilinmeyen Parça');
            const artist = el.getAttribute('data-artist') || (artistEl ? artistEl.textContent.trim() : 'Bilinmeyen Sanatçı');
            const duration = durationEl ? durationEl.textContent.trim() : '--:--:--';
            const art = imgEl ? imgEl.src : '';

            return { hex, title, artist, duration, art, streamUrl };
        }

        playTrack(streamUrl, triggerEl, trackInfo) {
            // Audio elementini bul veya oluştur
            let audioEl = document.querySelector('audio') || document.querySelector('#core-audio-player');
            if (!audioEl) {
                audioEl = document.createElement('audio');
                audioEl.id = 'core-audio-player';
                audioEl.style.display = 'none';
                document.body.appendChild(audioEl);
            }

            audioEl.src = streamUrl;
            audioEl.play().catch(err => console.debug('[MusicSpa] Oynatma hatası:', err));

            // UI'da aktif şarkı görsel durumunu güncelle
            document.querySelectorAll('.is-playing').forEach(el => el.classList.remove('is-playing'));
            // Now Playing sayfası açıksa canlı güncelle
            this.updateNowPlayingPageLive(trackInfo);

            // Audio durumlarına göre rotasyon animasyonunu yönet
            audioEl.onplay = () => {
                document.querySelectorAll('.player-album-art, .page-player').forEach(el => el.classList.add('is-playing'));
            };
            audioEl.onpause = () => {
                document.querySelectorAll('.player-album-art, .page-player').forEach(el => el.classList.remove('is-playing'));
            };
            audioEl.onended = () => {
                document.querySelectorAll('.player-album-art, .page-player').forEach(el => el.classList.remove('is-playing'));
            };

            // Global oynatma olayı yayınla (diğer bileşenler ve player-bar için)
            window.dispatchEvent(new CustomEvent('coremusic:track-change', {
                detail: { streamUrl, track: trackInfo }
            }));
        }

        async recordHistory(trackInfo) {
            try {
                const response = await fetch(`${this.apiUrl}/api/v1/music/history`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        music_id: trackInfo.hex,
                        duration_sec: 180,
                        device_type: 'desktop'
                    })
                });

                if (response.ok) {
                    // Sayfadaki Recent Tracks ve History bileşenlerini canlı senkronize et
                    this.updateRecentTracksLive(trackInfo);
                    this.updateHistoryPageLive(trackInfo);

                    window.dispatchEvent(new CustomEvent('coremusic:history-updated', {
                        detail: { track: trackInfo }
                    }));
                }
            } catch (err) {
                console.debug('[MusicSpa] Dinleme geçmişi kaydedilemedi:', err);
            }
        }

        updateRecentTracksLive(trackInfo) {
            // Ana sayfadaki En Son Dinlenen Şarkılar grid'ini bul
            const recentSection = document.querySelector('section[aria-label="En Son Dinlenen Şarkılar"] .home_song__btn-card-grid');
            if (!recentSection) {
                return;
            }

            // Eğer bu parça zaten en başta değilse, yeni kart oluşturup başa ekle
            const firstCard = recentSection.querySelector('.home-song__mini-card');
            if (firstCard && firstCard.getAttribute('data-stream')?.includes(trackInfo.hex)) {
                return; // Zaten en başta
            }

            const card = document.createElement('a');
            card.href = '/playlist';
            card.className = 'home-song__mini-card is-playing';
            card.setAttribute('data-no-spa', '');
            card.setAttribute('data-stream', trackInfo.streamUrl || `/stream/${trackInfo.hex}`);
            card.setAttribute('data-title', trackInfo.title);
            card.setAttribute('data-artist', trackInfo.artist);
            card.innerHTML = `
                <div class="home-song__mini-card__art">
                    <img class="home-song__mini-card__art-img" src="${trackInfo.art || `${ASSETS_ORIGIN}/Image/res-pink/album-goksel.png`}" alt="" width="50" height="50" loading="lazy"/>
                    <img class="home-song__mini-card__art-case" src="${ASSETS_ORIGIN}/Image/res-pink/cd-case.png" alt="" aria-hidden="true"/>
                    <img class="home-song__mini-card__art-badge" src="${ASSETS_ORIGIN}/Image/res-pink/music.png" alt="" aria-hidden="true"/>
                    <img class="home-song__mini-card__art-cd" src="${ASSETS_ORIGIN}/Image/res-pink/cd-overlay.png" alt="" aria-hidden="true"/>
                </div>
                <div class="home-song__mini-card__info">
                    <h3 class="home-song__mini-card__title">${this.escapeHtml(trackInfo.title)}</h3>
                    <p class="home-song__mini-card__subtitle">${this.escapeHtml(trackInfo.artist)}</p>
                    <span class="home-song__mini-card__subtitle">${this.escapeHtml(trackInfo.duration || '--:--:--')}</span>
                </div>
            `;

            // Animasyonlu başa ekle
            card.style.opacity = '0';
            card.style.transform = 'translateY(-10px)';
            card.style.transition = 'all 0.3s ease';
            recentSection.insertBefore(card, recentSection.firstChild);

            requestAnimationFrame(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            });

            // Grid sınırını aşmaması için son kartı sil (Wide: 10, Embedded: 4)
            const cards = recentSection.querySelectorAll('.home-song__mini-card');
            const maxCards = window.innerWidth >= 1440 ? 10 : 4;
            if (cards.length > maxCards) {
                cards[cards.length - 1].remove();
            }
        }

        updateHistoryPageLive(trackInfo) {
            // Embedded liste (history.php)
            const historyList = document.querySelector('.history-list__items');
            if (historyList) {
                const li = document.createElement('li');
                li.className = 'history-list__item';
                li.innerHTML = `
                    <a class="history-row" href="/playlist" data-no-spa data-stream="${trackInfo.streamUrl || `/stream/${trackInfo.hex}`}" aria-label="Oynat: ${this.escapeHtml(trackInfo.title)} — ${this.escapeHtml(trackInfo.artist)}">
                        <span class="history-row__play" aria-hidden="true">
                            <svg class="history-row__icon" viewBox="0 0 24 24" focusable="false">
                                <path d="M8 5v14l11-7z" fill="currentColor"/>
                            </svg>
                        </span>
                        <span class="history-row__title">${this.escapeHtml(trackInfo.title)}</span>
                        <span class="history-row__artist">${this.escapeHtml(trackInfo.artist)}</span>
                        <span class="history-row__duration">${this.escapeHtml(trackInfo.duration || '--:--:--')}</span>
                    </a>
                `;
                historyList.insertBefore(li, historyList.firstChild);
            }

            // Wide kolonlar (history.php wide)
            const firstCol = document.querySelector('.history-cols .history-col .history-col__items');
            if (firstCol) {
                const li = document.createElement('li');
                li.className = 'history-list__item';
                li.innerHTML = `
                    <a class="history-row history-row--mini" href="/playlist" data-no-spa data-stream="${trackInfo.streamUrl || `/stream/${trackInfo.hex}`}" aria-label="Oynat: ${this.escapeHtml(trackInfo.title)} — ${this.escapeHtml(trackInfo.artist)}">
                        <img class="history-row__disc" src="${ASSETS_ORIGIN}/Image/figma-1920/disc-history.png" width="24" height="21" alt="" loading="lazy">
                        <span class="history-row__title">${this.escapeHtml(trackInfo.title)}</span>
                        <span class="history-row__duration">${this.escapeHtml(trackInfo.duration || '--:--:--')}</span>
                    </a>
                `;
                firstCol.insertBefore(li, firstCol.firstChild);
            }
        }

        /* ─── Adım 8/9 — İlk yükleme: API'den canlı veri (progressive enhancement) ───
         * Migration'lar henüz uygulanmadıysa / API kapalıysa fetch başarısız olur ve
         * PHP sunucu-render'ı sessizce korunur (fallback). */

        async loadInitialData() {
            await Promise.allSettled([
                this.loadRecentTracksFromApi(),
                this.loadHistoryFromApi(),
            ]);
        }

        /** GET /api/v1/music/recent — home "En Son Dinlenen Şarkılar" grid'ini tazeler */
        async loadRecentTracksFromApi() {
            const grid = document.querySelector('section[aria-label="En Son Dinlenen Şarkılar"] .home_song__btn-card-grid');
            if (!grid) {
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/api/v1/music/recent?limit=10`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'include',
                });
                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const items = payload?.data?.items;
                if (!Array.isArray(items) || items.length === 0) {
                    return;
                }

                grid.replaceChildren(...items.map((item) => this.buildRecentCard(item)));
            } catch (err) {
                console.debug('[MusicSpa] Recent tracks API yok — PHP render korunuyor:', err);
            }
        }

        /** GET /api/v1/music/history — history sayfası listelerini tazeler */
        async loadHistoryFromApi() {
            const embeddedList = document.querySelector('.history-list__items');
            const wideList = document.querySelector('.history-cols .history-col .history-col__items');
            if (!embeddedList && !wideList) {
                return;
            }

            try {
                const response = await fetch(`${this.apiUrl}/api/v1/music/history?page=1&per_page=20`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'include',
                });
                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                const items = payload?.data?.items;
                if (!Array.isArray(items) || items.length === 0) {
                    return;
                }

                if (embeddedList) {
                    embeddedList.replaceChildren(...items.map((item) => this.buildHistoryItem(item)));
                }
                if (wideList) {
                    wideList.replaceChildren(...items.map((item) => this.buildHistoryRowMini(item)));
                }
            } catch (err) {
                console.debug('[MusicSpa] History API yok — PHP render korunuyor:', err);
            }
        }

        /** DOM API ile recent kartı üretir (innerHTML yasağına uygun — textContent) */
        buildRecentCard(item) {
            const hex = item.music_hex || '';
            const card = document.createElement('a');
            card.href = '/playlist';
            card.className = 'home-song__mini-card';
            card.setAttribute('data-no-spa', '');
            card.setAttribute('data-stream', `/stream/${hex}`);
            card.setAttribute('data-title', item.title || '');
            card.setAttribute('data-artist', item.artist_name || '');

            const art = document.createElement('div');
            art.className = 'home-song__mini-card__art';
            [
                ['home-song__mini-card__art-img', `${ASSETS_ORIGIN}/Image/res-pink/album-goksel.png`, 50, 50],
                ['home-song__mini-card__art-case', `${ASSETS_ORIGIN}/Image/res-pink/cd-case.png`, 0, 0],
                ['home-song__mini-card__art-badge', `${ASSETS_ORIGIN}/Image/res-pink/music.png`, 0, 0],
                ['home-song__mini-card__art-cd', `${ASSETS_ORIGIN}/Image/res-pink/cd-overlay.png`, 0, 0],
            ].forEach(([cls, src, w, h]) => {
                const img = document.createElement('img');
                img.className = cls;
                img.src = src;
                img.alt = '';
                img.loading = 'lazy';
                if (w) { img.width = w; img.height = h; }
                if (cls !== 'home-song__mini-card__art-img') { img.setAttribute('aria-hidden', 'true'); }
                art.appendChild(img);
            });
            card.appendChild(art);

            const info = document.createElement('div');
            info.className = 'home-song__mini-card__info';

            const title = document.createElement('h3');
            title.className = 'home-song__mini-card__title';
            title.textContent = item.title || 'Bilinmeyen Parça';

            const artist = document.createElement('p');
            artist.className = 'home-song__mini-card__subtitle';
            artist.textContent = item.artist_name || 'Bilinmeyen Sanatçı';

            const duration = document.createElement('span');
            duration.className = 'home-song__mini-card__subtitle';
            duration.textContent = this.formatDuration(item.duration_sec);

            info.append(title, artist, duration);
            card.appendChild(info);

            return card;
        }

        /** DOM API ile embedded history satırı üretir */
        buildHistoryItem(item) {
            const li = document.createElement('li');
            li.className = 'history-list__item';

            const row = document.createElement('a');
            row.className = 'history-row';
            row.href = '/playlist';
            row.setAttribute('data-no-spa', '');
            row.setAttribute('data-stream', `/stream/${item.music_hex || ''}`);
            row.setAttribute('aria-label', `Oynat: ${item.title || ''} — ${item.artist_name || ''}`);

            const play = document.createElement('span');
            play.className = 'history-row__play';
            play.setAttribute('aria-hidden', 'true');
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('class', 'history-row__icon');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('focusable', 'false');
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.setAttribute('d', 'M8 5v14l11-7z');
            path.setAttribute('fill', 'currentColor');
            svg.appendChild(path);
            play.appendChild(svg);

            const title = document.createElement('span');
            title.className = 'history-row__title';
            title.textContent = item.title || 'Bilinmeyen Parça';

            const artist = document.createElement('span');
            artist.className = 'history-row__artist';
            artist.textContent = item.artist_name || 'Bilinmeyen Sanatçı';

            const duration = document.createElement('span');
            duration.className = 'history-row__duration';
            duration.textContent = this.formatDuration(item.duration_sec);

            row.append(play, title, artist, duration);
            li.appendChild(row);

            return li;
        }

        /** DOM API ile wide (çok kolonlu) history mini satırı üretir */
        buildHistoryRowMini(item) {
            const li = document.createElement('li');
            li.className = 'history-list__item';

            const row = document.createElement('a');
            row.className = 'history-row history-row--mini';
            row.href = '/playlist';
            row.setAttribute('data-no-spa', '');
            row.setAttribute('data-stream', `/stream/${item.music_hex || ''}`);
            row.setAttribute('aria-label', `Oynat: ${item.title || ''} — ${item.artist_name || ''}`);

            const disc = document.createElement('img');
            disc.className = 'history-row__disc';
            disc.src = `${ASSETS_ORIGIN}/Image/figma-1920/disc-history.png`;
            disc.width = 24;
            disc.height = 21;
            disc.alt = '';
            disc.loading = 'lazy';

            const title = document.createElement('span');
            title.className = 'history-row__title';
            title.textContent = item.title || 'Bilinmeyen Parça';

            const duration = document.createElement('span');
            duration.className = 'history-row__duration';
            duration.textContent = this.formatDuration(item.duration_sec);

            row.append(disc, title, duration);
            li.appendChild(row);

            return li;
        }

        /** API duration_sec (int) → "HH:MM:SS" (PHP render paritesi) */
        formatDuration(sec) {
            const total = Number(sec) || 0;
            const pad = (n) => String(n).padStart(2, '0');
            return `${pad(Math.floor(total / 3600))}:${pad(Math.floor((total % 3600) / 60))}:${pad(total % 60)}`;
        }

        updateNowPlayingPageLive(trackInfo) {
            // Eğer kullanıcı nowplaying sayfasındaysa bilgileri güncelle
            const infoTitle = document.getElementById('np-info-title');
            const infoArtist = document.getElementById('np-info-artist');
            const infoAlbum = document.getElementById('np-info-album');
            if (infoTitle) infoTitle.textContent = trackInfo.title || '';
            if (infoArtist) infoArtist.textContent = trackInfo.artist || '';

            // Now playing tablosunda ilgili satırı aktif yap
            const tbody = document.getElementById('nowplaying-tbody');
            if (tbody) {
                tbody.querySelectorAll('.player-row--active').forEach(r => r.classList.remove('player-row--active'));
                const activeRow = tbody.querySelector(`.player-row[data-stream*="${trackInfo.hex}"]`);
                if (activeRow) {
                    activeRow.classList.add('player-row--active');
                }
            }

            // Kapak görseli ve döner disk
            const coverImgs = document.querySelectorAll('.player-album-art__img, .player-bg__image');
            if (trackInfo.art && coverImgs.length > 0) {
                coverImgs.forEach(img => {
                    img.src = trackInfo.art;
                });
            }
        }

        async createPlaylist(name, description = '') {
            try {
                const res = await fetch(`${this.apiUrl}/api/v1/playlist`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({ name, description })
                });

                const data = await res.json();
                if (data.success) {
                    this.showToast(`✓ "${name}" çalma listesi oluşturuldu.`);
                }
                return data;
            } catch (err) {
                console.error('[MusicSpa] Playlist oluşturulamadı:', err);
                this.showToast('Çalma listesi oluşturulurken hata meydana geldi.', 'error');
                return null;
            }
        }

        async addTrackToPlaylist(playlistId, trackHex, playlistName = '') {
            try {
                const res = await fetch(`${this.apiUrl}/api/v1/playlist/tracks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        playlist_id: playlistId,
                        track_id: trackHex
                    })
                });

                const data = await res.json();
                if (data.success) {
                    const msg = playlistName ? `✓ "${playlistName}" listesine eklendi.` : '✓ Şarkı çalma listesine eklendi.';
                    this.showToast(msg);
                } else {
                    this.showToast(data.error?.message || 'Şarkı listeye eklenemedi.', 'error');
                }
                return data;
            } catch (err) {
                console.error('[MusicSpa] Parça çalma listesine eklenemedi:', err);
                this.showToast('İşlem sırasında bağlantı hatası oluştu.', 'error');
                return null;
            }
        }

        async openAddToPlaylistModal(trackInfo) {
            // Varsa eski modalı temizle
            const oldModal = document.querySelector('#cm-playlist-modal');
            if (oldModal) oldModal.remove();

            // Kullanıcının çalma listelerini al
            let playlists = [];
            try {
                const res = await fetch(`${this.apiUrl}/api/v1/user/header-data`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'include'
                });
                const json = await res.json();
                if (json.success && json.data?.playlist_shortcuts) {
                    playlists = json.data.playlist_shortcuts;
                }
            } catch (_) {}

            const modal = document.createElement('div');
            modal.id = 'cm-playlist-modal';
            modal.className = 'cm-modal-overlay';
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-modal', 'true');
            modal.setAttribute('aria-labelledby', 'cm-modal-title');

            let playlistOptionsHtml = '';
            if (playlists.length > 0) {
                playlistOptionsHtml = playlists.map(pl => `
                    <button type="button" class="cm-modal-item-btn" data-playlist-id="${pl.playlist_id}" data-playlist-name="${this.escapeHtml(pl.name)}">
                        <span>🎵 ${this.escapeHtml(pl.name)}</span>
                        <span class="cm-modal-add-icon">+</span>
                    </button>
                `).join('');
            } else {
                playlistOptionsHtml = '<p class="cm-modal-empty">Henüz bir çalma listeniz yok.</p>';
            }

            modal.innerHTML = `
                <div class="cm-modal-content">
                    <div class="cm-modal-header">
                        <h2 id="cm-modal-title" class="cm-modal-title">Çalma Listesine Ekle</h2>
                        <button type="button" class="cm-modal-close" aria-label="Kapat">&times;</button>
                    </div>
                    <div class="cm-modal-track-info">
                        <strong>${this.escapeHtml(trackInfo.title)}</strong>
                        <span>${this.escapeHtml(trackInfo.artist)}</span>
                    </div>
                    <div class="cm-modal-body">
                        <div class="cm-modal-playlists">
                            ${playlistOptionsHtml}
                        </div>
                        <div class="cm-modal-divider"></div>
                        <div class="cm-modal-create">
                            <input type="text" class="cm-modal-input" placeholder="Yeni Playlist Adı..." aria-label="Yeni Playlist Adı" />
                            <button type="button" class="cm-modal-create-btn">Oluştur ve Ekle</button>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Odaklanma ve klavye etkileşimi (WCAG 2.2 AA)
            const closeBtn = modal.querySelector('.cm-modal-close');
            const input = modal.querySelector('.cm-modal-input');
            const createBtn = modal.querySelector('.cm-modal-create-btn');
            closeBtn.focus();

            const closeModal = () => modal.remove();

            closeBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeModal();
            });

            document.addEventListener('keydown', function escHandler(e) {
                if (e.key === 'Escape') {
                    closeModal();
                    document.removeEventListener('keydown', escHandler);
                }
            });

            // Var olan playliste ekleme
            modal.querySelectorAll('.cm-modal-item-btn').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const plId = btn.getAttribute('data-playlist-id');
                    const plName = btn.getAttribute('data-playlist-name');
                    await this.addTrackToPlaylist(plId, trackInfo.hex, plName);
                    closeModal();
                });
            });

            // Yeni liste oluşturup ekleme
            createBtn.addEventListener('click', async () => {
                const name = input.value.trim();
                if (!name) return;
                const created = await this.createPlaylist(name);
                if (created && created.success && created.data?.playlist_id) {
                    await this.addTrackToPlaylist(created.data.playlist_id, trackInfo.hex, name);
                }
                closeModal();
            });
        }

        showToast(message, type = 'success') {
            let container = document.querySelector('#cm-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'cm-toast-container';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = `cm-toast cm-toast--${type}`;
            toast.textContent = message;
            container.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('cm-toast--fadeout');
                setTimeout(() => toast.remove(), 400);
            }, 3000);
        }

        escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        injectStyles() {
            if (document.querySelector('#cm-spa-injected-styles')) return;

            const style = document.createElement('style');
            style.id = 'cm-spa-injected-styles';
            style.textContent = `
                /* CoreMusic SPA Modal & Toast Styles (WCAG 2.2 AA) */
                .cm-modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    background: rgba(0, 0, 0, 0.75);
                    backdrop-filter: blur(8px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    animation: cmModalFadeIn 0.2s ease-out;
                }
                .cm-modal-content {
                    background: rgba(26, 26, 36, 0.95);
                    border: 1px solid rgba(255, 255, 255, 0.15);
                    border-radius: 12px;
                    width: 90%;
                    max-width: 420px;
                    padding: 20px;
                    color: #fff;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.6);
                }
                .cm-modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 12px;
                }
                .cm-modal-title {
                    margin: 0;
                    font-size: 16px;
                    font-weight: 600;
                }
                .cm-modal-close {
                    background: none;
                    border: none;
                    color: rgba(255, 255, 255, 0.6);
                    font-size: 24px;
                    cursor: pointer;
                    line-height: 1;
                    padding: 4px;
                }
                .cm-modal-close:hover { color: #fff; }
                .cm-modal-track-info {
                    background: rgba(255, 255, 255, 0.05);
                    padding: 10px;
                    border-radius: 8px;
                    margin-bottom: 16px;
                    display: flex;
                    flex-direction: column;
                    font-size: 13px;
                }
                .cm-modal-track-info span { color: rgba(255, 255, 255, 0.6); font-size: 12px; margin-top: 2px; }
                .cm-modal-playlists {
                    max-height: 180px;
                    overflow-y: auto;
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                }
                .cm-modal-item-btn {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    background: rgba(255, 255, 255, 0.08);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    color: #fff;
                    padding: 10px 14px;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 13px;
                    transition: all 0.2s ease;
                }
                .cm-modal-item-btn:hover {
                    background: rgba(255, 255, 255, 0.18);
                    border-color: rgba(255, 255, 255, 0.3);
                }
                .cm-modal-divider {
                    height: 1px;
                    background: rgba(255, 255, 255, 0.1);
                    margin: 16px 0;
                }
                .cm-modal-create {
                    display: flex;
                    gap: 8px;
                }
                .cm-modal-input {
                    flex: 1;
                    background: rgba(0, 0, 0, 0.4);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    border-radius: 6px;
                    padding: 8px 12px;
                    color: #fff;
                    font-size: 13px;
                }
                .cm-modal-create-btn {
                    background: #e91e63;
                    border: none;
                    color: #fff;
                    padding: 8px 14px;
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 600;
                    cursor: pointer;
                    white-space: nowrap;
                }
                .cm-modal-create-btn:hover { background: #d81557; }
                #cm-toast-container {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10001;
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    pointer-events: none;
                }
                .cm-toast {
                    background: rgba(20, 20, 28, 0.95);
                    color: #fff;
                    border: 1px solid rgba(255, 255, 255, 0.15);
                    padding: 12px 18px;
                    border-radius: 8px;
                    font-size: 13px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.5);
                    backdrop-filter: blur(10px);
                    animation: cmToastSlideIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                }
                .cm-toast--error { border-color: #f44336; }
                .cm-toast--fadeout { opacity: 0; transform: translateY(-10px); transition: all 0.4s ease; }
                @keyframes cmModalFadeIn { from { opacity: 0; } to { opacity: 1; } }
                @keyframes cmToastSlideIn { from { transform: translateX(50px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
            `;
            document.head.appendChild(style);
        }

        observeDomChanges() {
            window.addEventListener('coremusic:page-loaded', () => {
                // Sayfa değiştiğinde SPA eventleri
            });
        }
    }

    // Global erişim için window'a bağla
    window.MusicSpaManager = new MusicSpaManager();
})();

