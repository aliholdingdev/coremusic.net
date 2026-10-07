/**
 * CoreMusic — OAuth Manager (Frontend)
 *
 * ADR-088 compliant — Gender-based social OAuth UI yönetimi.
 * Vanilla JS ES6+ — ADR-001 uyumlu.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 * @see [[decisions/accepted/ADR-001-vanilla-js-itcss]]
 */

'use strict';

const OAuthManager = (() => {
    const API_BASE = '/api/oauth';

    /**
     * Kullanıcının cinsiyetine göre OAuth platformlarını getir.
     */
    async function getPlatforms(gender = 'neutral') {
        try {
            const response = await fetch(`${API_BASE}/platforms?gender=${gender}`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Failed to fetch OAuth platforms:', error);
            return { success: false, platforms: [] };
        }
    }

    /**
     * OAuth bağlama işlemini başlat (popup).
     */
    async function connect(provider) {
        try {
            const response = await fetch(`${API_BASE}/connect`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': getCsrfToken(),
                },
                body: JSON.stringify({ provider }),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.redirect) {
                // OAuth popup aç
                const width = 600;
                const height = 700;
                const left = (screen.width - width) / 2;
                const top = (screen.height - height) / 2;

                const popup = window.open(
                    data.redirect,
                    'oauth_connect',
                    `width=${width},height=${height},left=${left},top=${top},scrollbars=yes`
                );

                // C-F-05: reverse tabnabbing — sağlayıcı penceresi window.opener'ı
                // gezdirmesin. 'noopener' feature'ı YAZMAK popup referansını null'a
                // çevirir (aşağıdaki popup.closed/popup.location kontrolü kırılırdı);
                // open sonrası opener'ı kapatmak aynı korumayı referansı bozmadan verir.
                if (popup) {
                    try { popup.opener = null; } catch { /* cross-origin navigasyon sonrası erişim yok — yok sayılır */ }
                }

                // Popup kapanmasını bekle
                return new Promise((resolve) => {
                    const checkInterval = setInterval(() => {
                        try {
                            if (popup.closed || popup.location.href.includes('oauth-callback')) {
                                clearInterval(checkInterval);
                                // Callback sayfasından sonucu al
                                setTimeout(() => resolve(getConnectionStatus(provider)), 1000);
                            }
                        } catch {
                            // Cross-origin — henüz kapanmadı
                        }
                    }, 500);

                    // 60 saniye timeout
                    setTimeout(() => {
                        clearInterval(checkInterval);
                        if (!popup.closed) popup.close();
                        resolve({ success: false, message: 'Connection timeout' });
                    }, 60000);
                });
            }

            return data;
        } catch (error) {
            console.error('OAuth connect failed:', error);
            return { success: false, message: error.message };
        }
    }

    /**
     * OAuth bağlantısını kes.
     */
    async function disconnect(provider) {
        try {
            const response = await fetch(`${API_BASE}/disconnect`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': getCsrfToken(),
                },
                body: JSON.stringify({ provider }),
            });

            return await response.json();
        } catch (error) {
            console.error('OAuth disconnect failed:', error);
            return { success: false, message: error.message };
        }
    }

    /**
     * Kullanıcının tüm bağlantılarını getir.
     */
    async function getConnections() {
        try {
            const response = await fetch(`${API_BASE}/connections`, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            return await response.json();
        } catch (error) {
            console.error('Failed to fetch connections:', error);
            return { success: false, connections: [] };
        }
    }

    /**
     * Belirli bir provider için bağlantı durumunu kontrol et.
     */
    async function getConnectionStatus(provider) {
        const result = await getConnections();
        if (!result.success) return { connected: false };

        const connection = result.connections.find(
            (c) => c.provider === provider && c.is_active == 1
        );

        return {
            connected: !!connection,
            connection: connection || null,
        };
    }

    /**
     * Gender-based platform listesini render et.
     */
    function renderPlatforms(platforms, containerId = 'oauth-platforms') {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.replaceChildren();

        if (!platforms || platforms.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'oauth-empty';
            empty.textContent = 'No platforms available for your profile.';
            container.appendChild(empty);
            return;
        }

        platforms.forEach((platform) => {
            const card = createPlatformCard(platform);
            container.appendChild(card);
        });
    }

    /**
     * Platform card oluştur.
     */
    function createPlatformCard(platform) {
        const card = document.createElement('div');
        card.className = 'oauth-platform-card';
        card.dataset.provider = platform.provider;
        card.style.setProperty('--platform-color', platform.color);

        // DOM API ile kurulur — markup ataması yasak (ADR-001, assets AGENTS.md §4.3).
        const icon = document.createElement('div');
        icon.className = 'oauth-platform-icon';
        const iconGlyph = document.createElement('i');
        iconGlyph.className = 'oauth-icon oauth-icon--' + safeClassToken(platform.icon);
        icon.appendChild(iconGlyph);

        const info = document.createElement('div');
        info.className = 'oauth-platform-info';
        const nameEl = document.createElement('h3');
        nameEl.className = 'oauth-platform-name';
        nameEl.textContent = String(platform.name ?? '');
        const stats = document.createElement('span');
        stats.className = 'oauth-platform-stats';
        stats.textContent = (platform.female_percent || '') + '% ' + getGenderLabel(platform);
        info.append(nameEl, stats);

        const actions = document.createElement('div');
        actions.className = 'oauth-platform-actions';
        const btn = document.createElement('button');
        btn.className = 'oauth-connect-btn';
        btn.dataset.provider = platform.provider;
        btn.textContent = 'Connect';
        actions.appendChild(btn);

        card.append(icon, info, actions);

        // Connect button click handler
        btn.addEventListener('click', async () => {
            btn.disabled = true;
            btn.textContent = 'Connecting...';

            const result = await connect(platform.provider);

            if (result.success) {
                btn.textContent = 'Connected';
                btn.classList.add('oauth-connect-btn--connected');
            } else {
                btn.textContent = 'Connect';
                btn.disabled = false;
                showNotification(result.message, 'error');
            }
        });

        return card;
    }

    /**
     * CSRF token'ını DOM'daki gizli input'tan al — app sözleşmesi
     * `input[name="csrf_token"]` (shell: id="csrf-global"; CsrfSyncManager ile aynı selector).
     * Cookie'den OKUMAK YANLIŞTIR (C-F-04): PHP asla csrf_token cookie'si set etmez,
     * getToken() hep '' dönüyordu → OAuth POST'ları boş header'la 403 (fail-closed).
     */
    function getCsrfToken() {
        const input = document.querySelector('[name="csrf_token"]');
        return input && typeof input.value === 'string' ? input.value : '';
    }

    /**
     * Gender label döndür.
     */
    function getGenderLabel(platform) {
        if (platform.female_percent > 50) return 'female';
        if (platform.female_percent < 50) return 'male';
        return 'neutral';
    }

    /**
     * Sınıf adı segmentini güvenlikli hale getir (yalnız [A-Za-z0-9_-]).
     */
    function safeClassToken(value) {
        return String(value ?? '').replace(/[^\w-]/g, '');
    }

    /**
     * Bildirim göster.
     */
    function showNotification(message, type = 'info') {
        const event = new CustomEvent('oauth:notification', {
            detail: { message, type },
        });
        window.dispatchEvent(event);
    }

    /**
     * OAuth flow'unu başlat (anasayfadan çağrılır).
     *
     * @param {string} gender Kullanıcı cinsiyeti
     * @param {string} containerId Render edilecek container ID
     */
    async function init(gender = 'neutral', containerId = 'oauth-platforms') {
        const result = await getPlatforms(gender);
        if (result.success) {
            renderPlatforms(result.platforms, containerId);
        }
    }

    // Public API
    return {
        init,
        connect,
        disconnect,
        getConnections,
        getConnectionStatus,
        getPlatforms,
        renderPlatforms,
    };
})();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = OAuthManager;
}
