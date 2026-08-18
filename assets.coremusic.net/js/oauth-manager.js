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

        container.innerHTML = '';

        if (!platforms || platforms.length === 0) {
            container.innerHTML = '<p class="oauth-empty">No platforms available for your profile.</p>';
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

        card.innerHTML = `
            <div class="oauth-platform-icon">
                <i class="oauth-icon oauth-icon--${platform.icon}"></i>
            </div>
            <div class="oauth-platform-info">
                <h3 class="oauth-platform-name">${escapeHtml(platform.name)}</h3>
                <span class="oauth-platform-stats">${platform.female_percent || ''}% ${getGenderLabel(platform)}</span>
            </div>
            <div class="oauth-platform-actions">
                <button class="oauth-connect-btn" data-provider="${platform.provider}">
                    Connect
                </button>
            </div>
        `;

        // Connect button click handler
        const btn = card.querySelector('.oauth-connect-btn');
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
     * CSRF token'ı cookie'den al.
     */
    function getCsrfToken() {
        const cookies = document.cookie.split(';');
        for (const cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'csrf_token') {
                return value;
            }
        }
        return '';
    }

    /**
     * HTML escape.
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
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
