<?php declare(strict_types=1);
/**
 * pages/components/quick-network-panels.php — Global Header Quick Wi-Fi & Bluetooth Panels
 * ------------------------------------------------------------------
 * SSOT : Figma node 2831:9665 (Linux 1024 - Wifi Qucik Page Base)
 *        Figma node 2831:9687 (Linux 1024 - Bluethoot Qucik Page Base)
 * Layer: L3 Presentation · 04_Components (c-quick-network.css)
 * ------------------------------------------------------------------
 */
?>

<!-- ═══ Wİ-Fİ HIZLI AĞ PANELİ (Figma SSOT 2831:9665) ═══ -->
<div class="quick-network-overlay" id="quick-wifi-overlay" role="dialog" aria-modal="true" aria-labelledby="quick-wifi-title">
    <div class="quick-network-panel" role="document">
        <div class="quick-network-header">
            <div class="quick-network-header__title-group">
                <svg viewBox="0 0 24 24" class="quick-network-header__icon" fill="currentColor" aria-hidden="true">
                    <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4zm0 3.5c3.55 0 6.78 1.41 9.17 3.71L12 18.29 2.83 11.21C5.22 8.91 8.45 7.5 12 7.5z"/>
                </svg>
                <div>
                    <h3 class="quick-network-header__title" id="quick-wifi-title">WIFI</h3>
                    <p class="quick-network-header__subtitle">Ağ Bağlantıları</p>
                </div>
            </div>
            <button type="button" class="quick-network-close" id="quick-wifi-close" aria-label="Kapat" title="Kapat">✕</button>
        </div>

        <div class="quick-network-switch-row">
            <span class="quick-network-switch-label">WIFI</span>
            <label class="quick-network-switch" aria-label="WIFI Aç / Kapat">
                <input type="checkbox" id="quick-wifi-toggle" checked>
                <span class="quick-network-switch__slider"></span>
            </label>
        </div>

        <div class="quick-network-group">
            <h4 class="quick-network-group__label">Bağlı Olan Ağ</h4>
            <div class="quick-network-item quick-network-item--connected" id="quick-wifi-connected-card">
                <div class="quick-network-item__info">
                    <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                        <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                    </svg>
                    <div class="quick-network-item__details">
                        <div class="quick-network-item__name-row">
                            <span class="quick-network-item__name" id="quick-wifi-current-name">Bayram Ali - Home</span>
                            <span class="quick-network-item__tag">Bağlı</span>
                        </div>
                        <div class="quick-network-item__meta">5GHz • Mükemmel sinyal : %100 • 2.4GB Kullanıldı</div>
                    </div>
                </div>
                <button type="button" class="quick-network-btn quick-network-btn--disconnect" id="quick-wifi-disconnect">Bağlantıyı Kes</button>
            </div>
        </div>

        <div class="quick-network-group">
            <h4 class="quick-network-group__label">Kullanılabilir Ağlar</h4>
            <div id="quick-wifi-available-list">
                <div class="quick-network-item">
                    <div class="quick-network-item__info">
                        <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                            <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                        </svg>
                        <div class="quick-network-item__details">
                            <span class="quick-network-item__name">Studio_Control_Room</span>
                            <div class="quick-network-item__meta">5GHz • Güvenli Bağlantı</div>
                        </div>
                    </div>
                    <button type="button" class="quick-network-btn quick-network-btn--primary" data-action="quick-connect-wifi" data-ssid="Studio_Control_Room">Bağlan</button>
                </div>
                <div class="quick-network-item">
                    <div class="quick-network-item__info">
                        <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                            <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                        </svg>
                        <div class="quick-network-item__details">
                            <span class="quick-network-item__name">CoreMusic_Guest</span>
                            <div class="quick-network-item__meta">2.4GHz • Açık Ağ</div>
                        </div>
                    </div>
                    <button type="button" class="quick-network-btn quick-network-btn--primary" data-action="quick-connect-wifi" data-ssid="CoreMusic_Guest">Bağlan</button>
                </div>
                <div class="quick-network-item">
                    <div class="quick-network-item__info">
                        <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                            <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                        </svg>
                        <div class="quick-network-item__details">
                            <span class="quick-network-item__name">Apartment_5G</span>
                            <div class="quick-network-item__meta">5GHz • Güvenli Bağlantı</div>
                        </div>
                    </div>
                    <button type="button" class="quick-network-btn quick-network-btn--primary" data-action="quick-connect-wifi" data-ssid="Apartment_5G">Bağlan</button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ═══ BLUETOOTH HIZLI AYGIT PANELİ (Figma SSOT 2831:9687) ═══ -->
<div class="quick-network-overlay" id="quick-bt-overlay" role="dialog" aria-modal="true" aria-labelledby="quick-bt-title">
    <div class="quick-network-panel" role="document">
        <div class="quick-network-header">
            <div class="quick-network-header__title-group">
                <svg viewBox="0 0 24 24" class="quick-network-header__icon" fill="currentColor" aria-hidden="true">
                    <path d="M17.71 7.71L12 2h-1v7.59L6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 11 14.41V22h1l5.71-5.71-4.3-4.29 4.3-4.29zM13 5.83l1.88 1.88L13 9.59V5.83zm1.88 10.46L13 18.17v-3.76l1.88 1.88z"/>
                </svg>
                <div>
                    <h3 class="quick-network-header__title" id="quick-bt-title">Bluetooth</h3>
                    <p class="quick-network-header__subtitle">Cihaz Bağlantıları</p>
                </div>
            </div>
            <button type="button" class="quick-network-close" id="quick-bt-close" aria-label="Kapat" title="Kapat">✕</button>
        </div>

        <div class="quick-network-switch-row">
            <span class="quick-network-switch-label">Bluetooth</span>
            <label class="quick-network-switch" aria-label="Bluetooth Aç / Kapat">
                <input type="checkbox" id="quick-bt-toggle" checked>
                <span class="quick-network-switch__slider"></span>
            </label>
        </div>

        <div class="quick-network-group">
            <h4 class="quick-network-group__label">Bağlı Olan Cihaz</h4>
            <div class="quick-network-item quick-network-item--connected" id="quick-bt-connected-card">
                <div class="quick-network-item__info">
                    <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                        <path d="M12 3c-4.97 0-9 4.03-9 9v7c0 1.1.9 2 2 2h4v-8H5v-1c0-3.87 3.13-7 7-7s7 3.13 7 7v1h-4v8h4c1.1 0 2-.9 2-2v-7c0-4.97-4.03-9-9-9z"/>
                    </svg>
                    <div class="quick-network-item__details">
                        <div class="quick-network-item__name-row">
                            <span class="quick-network-item__name" id="quick-bt-current-name">Km - 50</span>
                            <span class="quick-network-item__tag">Bağlı</span>
                        </div>
                        <div class="quick-network-item__meta">Kulaklık • Mükemmel sinyal: 100% • AAC • Pil : 100%</div>
                    </div>
                </div>
                <button type="button" class="quick-network-btn quick-network-btn--disconnect" id="quick-bt-disconnect">Bağlantıyı Kes</button>
            </div>
        </div>

        <div class="quick-network-group">
            <h4 class="quick-network-group__label">Kullanılabilir Cihazlar</h4>
            <div id="quick-bt-available-list">
                <div class="quick-network-item">
                    <div class="quick-network-item__info">
                        <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                            <path d="M12 3c-4.97 0-9 4.03-9 9v7c0 1.1.9 2 2 2h4v-8H5v-1c0-3.87 3.13-7 7-7s7 3.13 7 7v1h-4v8h4c1.1 0 2-.9 2-2v-7c0-4.97-4.03-9-9-9z"/>
                        </svg>
                        <div class="quick-network-item__details">
                            <span class="quick-network-item__name">Car BT</span>
                            <div class="quick-network-item__meta">Kulaklık • Mükemmel sinyal: 100%</div>
                        </div>
                    </div>
                    <button type="button" class="quick-network-btn quick-network-btn--primary" data-action="quick-connect-bt" data-dev="Car BT">Bağlan</button>
                </div>
                <div class="quick-network-item">
                    <div class="quick-network-item__info">
                        <svg viewBox="0 0 24 24" class="quick-network-item__icon" fill="currentColor" aria-hidden="true">
                            <path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5v2h8v-2h5c1.1 0 1.99-.9 1.99-2L23 5c0-1.1-.9-2-2-2zm0 14H3V5h18v12z"/>
                        </svg>
                        <div class="quick-network-item__details">
                            <span class="quick-network-item__name">Samsung TV</span>
                            <div class="quick-network-item__meta">Televizyon • Mükemmel sinyal: 100%</div>
                        </div>
                    </div>
                    <button type="button" class="quick-network-btn quick-network-btn--primary" data-action="quick-connect-bt" data-dev="Samsung TV">Bağlan</button>
                </div>
            </div>
        </div>
    </div>
</div>
