<?php declare(strict_types=1);
/**
 * pages/settings.php — Ayarlar Sayfası (v1.0.0)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-settings.css)
 * SSOT     : p-settings.css BEM: .settings-page, .settings-section
 * Route    : /ayarlar (SpaRoute: 'settings' or 'ayarlar')
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;

/* --- DeviceManager (viewport test fallback) --- */
$testViewportW = null;
if (isset($_GET['_test_resp']) && preg_match('/^(1024|1440|1920|3840)$/', (string)$_GET['_test_resp'])) {
    $testViewportW = (int)$_GET['_test_resp'];
}

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => $testViewportW ?: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}
$is4k   = $dm->is4k();
$isWide = $dm->shouldRenderWideLayout() || $is4k;

/* --- Yerel yardımcılar --- */
$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

/* --- CSP Nonce --- */
$cspNonce = (string)($_SESSION['csp_nonce'] ?? '');
$nonceAttr = $cspNonce !== '' ? ' nonce="' . $h($cspNonce) . '"' : '';
/* --- 31-Bant ISO 1/3-Oktav Frekansları (ADR-106 / eq_chain.h) --- */
$eqFrequencies = [
    20 => '20', 25 => '25', 31.5 => '31.5', 40 => '40', 50 => '50', 63 => '63', 80 => '80', 100 => '100',
    125 => '125', 160 => '160', 200 => '200', 250 => '250', 315 => '315', 400 => '400', 500 => '500', 630 => '630',
    800 => '800', 1000 => '1k', 1250 => '1.25k', 1600 => '1.6k', 2000 => '2k', 2500 => '2.5k', 3150 => '3.15k',
    4000 => '4k', 5000 => '5k', 6300 => '6.3k', 8000 => '8k', 10000 => '10k', 12500 => '12.5k', 16000 => '16k',
    20000 => '20k'
];
$subset10Indices = [2, 5, 8, 11, 14, 17, 20, 23, 26, 29]; // 31.5, 63, 125, 250, 500, 1k, 2k, 4k, 8k, 16k
?>

<div class="page-settings <?= $isWide ? ($is4k ? 'page-settings--4k' : 'page-settings--wide') : 'page-settings--embedded' ?> page-layout settings-page <?= $h($dm->allClasses()) ?>" <?= $dm->dataAttributes() ?>>

    <header class="settings-header" style="margin-bottom: var(--space-4);">
        <h1 class="settings-title">Sistem ve Oynatıcı Ayarları</h1>
    </header>

    <!-- BÖLÜM 1: TEMA VE RENK AYARLARI -->
    <section class="settings-section" aria-labelledby="section-theme-title">
        <h2 id="section-theme-title" class="settings-section__title">Tema ve Görünüm</h2>
        <p class="settings-section__desc">Arayüz renk paleti ve vurgu temasını seçin.</p>

        <div class="settings-options" role="radiogroup" aria-label="Arayüz Teması">
            <button type="button" class="settings-option settings-option--active" role="radio" aria-checked="true" data-theme="pink">
                <span class="settings-option__swatch" style="background: var(--cm-primary); box-shadow: 0 0 10px rgba(255, 79, 216, 0.5);"></span>
                <span class="settings-option__label">Pembe Neon</span>
                <span class="settings-option__desc">Varsayılan</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-theme="purple">
                <span class="settings-option__swatch" style="background: var(--color-violet-500);"></span>
                <span class="settings-option__label">Mor Gece</span>
                <span class="settings-option__desc">Koyu Mor</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-theme="blue">
                <span class="settings-option__swatch" style="background: var(--color-blue-400);"></span>
                <span class="settings-option__label">Okyanus</span>
                <span class="settings-option__desc">Siber Mavi</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-theme="green">
                <span class="settings-option__swatch" style="background: var(--color-green-500);"></span>
                <span class="settings-option__label">Zümrüt</span>
                <span class="settings-option__desc">Doğal Yeşil</span>
            </button>
        </div>
    </section>

    <!-- BÖLÜM 2: SES ÇIKIŞI VE AMPLİFİKATÖR (DSP DONANIM) -->
    <section class="settings-section" aria-labelledby="section-audio-title">
        <h2 id="section-audio-title" class="settings-section__title">Ses Çıkışı &amp; Amplifikatör DSP</h2>
        <p class="settings-section__desc">Class AB amfi ve donanım DAC çıkış ayarları (SNR &gt;100dB, THD+N &lt;0.01%).</p>

        <div class="settings-options" role="radiogroup" aria-label="Ses Çıkış Modu">
            <button type="button" class="settings-option settings-option--active" role="radio" aria-checked="true" data-output="class-ab">
                <span class="settings-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12 3v9.28a4 4 0 1 0 2 3.47V6h4V3h-6z"/>
                    </svg>
                </span>
                <span class="settings-option__label">Class AB Çıkış</span>
                <span class="settings-option__desc">Saf Analog Amfi</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-output="ak4458">
                <span class="settings-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M12 3c-4.97 0-9 4.03-9 9v7c0 1.1.9 2 2 2h4v-8H5v-1c0-3.87 3.13-7 7-7s7 3.13 7 7v1h-4v8h4c1.1 0 2-.9 2-2v-7c0-4.97-4.03-9-9-9z"/>
                    </svg>
                </span>
                <span class="settings-option__label">AK4458 DAC</span>
                <span class="settings-option__desc">32-bit / 768kHz</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-output="bluetooth">
                <span class="settings-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M17.71 7.71L12 2h-1v7.59L6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 11 14.41V22h1l5.71-5.71-4.3-4.29 4.3-4.29zM13 5.83l1.88 1.88L13 9.59V5.83zm1.88 10.46L13 18.17v-3.76l1.88 1.88z"/>
                    </svg>
                </span>
                <span class="settings-option__label">Bluetooth HD</span>
                <span class="settings-option__desc">AAC / LDAC</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-output="xmos">
                <span class="settings-option__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M15 7v4h1v2h-3V5h2l-3-4-3 4h2v8H8v-2.07c.7-.37 1.2-1.08 1.2-1.93 0-1.21-.99-2.2-2.2-2.2-1.21 0-2.2.99-2.2 2.2 0 .85.5 1.56 1.2 1.93V13c0 1.11.89 2 2 2h3v3.05c-.71.37-1.2 1.1-1.2 1.95a2.2 2.2 0 0 0 4.4 0c0-.85-.49-1.58-1.2-1.95V15h3c1.11 0 2-.89 2-2v-2h1V7h-4z"/>
                    </svg>
                </span>
                <span class="settings-option__label">USB Audio</span>
                <span class="settings-option__desc">XMOS XU316</span>
            </button>
        </div>
    </section>

    <!-- BÖLÜM 2B: PARAMETRİK & GRAFİK HİBRİT EQ (31-BANT / 10-BANT ISO) -->
    <section class="settings-section settings-section--eq" aria-labelledby="section-eq-title">
        <div class="settings-section__header-row">
            <div>
                <h2 id="section-eq-title" class="settings-section__title">Parametrik &amp; Grafik Hibrit EQ</h2>
                <p class="settings-section__desc">Biquad Peaking Filtre Rafı (ISO 266 1/3-Oktav 31-Bant &amp; 10-Bant Alt Küme &bull; ±12 dB Aralığı).</p>
            </div>
            <div class="settings-eq-top-controls">
                <span class="settings-eq-bypass-label">Bypass</span>
                <label class="settings-switch" aria-label="EQ Aç/Kapat">
                    <input type="checkbox" id="eq-bypass-toggle" checked>
                    <span class="settings-switch__slider"></span>
                </label>
            </div>
        </div>

        <div class="settings-eq-toolbar">
            <div class="settings-eq-mode-group" role="group" aria-label="EQ Bant Modu">
                <button type="button" class="settings-eq-mode-btn is-active" data-eq-mode="31">31-Bant (1/3 Oktav)</button>
                <button type="button" class="settings-eq-mode-btn" data-eq-mode="10">10-Bant (Standart)</button>
            </div>

            <div class="settings-eq-preset-wrap">
                <label for="eq-preset-select" class="settings-eq-label">Ön Tanım:</label>
                <select id="eq-preset-select" class="settings-eq-select">
                    <option value="flat">Düz (Flat 0 dB)</option>
                    <option value="rock">Rock</option>
                    <option value="pop">Pop</option>
                    <option value="jazz">Caz</option>
                    <option value="classical">Klasik</option>
                    <option value="electronic">Elektronik / Dans</option>
                    <option value="vocal">Vokal / Akustik</option>
                    <option value="concert">Konser Salonu</option>
                    <option value="wedding">Düğün &amp; Canlı Performans</option>
                </select>
            </div>

            <div class="settings-eq-preamp-wrap">
                <label for="eq-preamp-gain" class="settings-eq-label">Pre-Amp: <span id="eq-preamp-val">0.0 dB</span></label>
                <input type="range" id="eq-preamp-gain" class="settings-eq-preamp-range" min="-12" max="12" step="0.5" value="0">
            </div>

            <button type="button" id="eq-reset-btn" class="settings-eq-reset-btn">Sıfırla</button>
        </div>

        <!-- 31/10-Band EQ Faders Rack Container -->
        <div class="settings-eq-rack-container" id="eq-rack-container" data-mode="31">
            <div class="settings-eq-rack" id="eq-faders-rack">
                <?php
                $fIndex = 0;
                foreach ($eqFrequencies as $hz => $label):
                    $isSubset = in_array($fIndex, $subset10Indices, true);
                    $subsetClass = $isSubset ? 'eq-fader--iso-highlight' : 'eq-fader--subset-hide';
                ?>
                <div class="eq-fader <?= $subsetClass ?>" data-index="<?= $fIndex ?>" data-freq="<?= $h((string)$hz) ?>">
                    <span class="eq-fader__val" id="eq-val-<?= $fIndex ?>">0</span>
                    <div class="eq-fader__track-wrap">
                        <div class="eq-fader__zero-line"></div>
                        <input type="range" class="eq-fader__range" id="eq-range-<?= $fIndex ?>" data-index="<?= $fIndex ?>" min="-12" max="12" step="0.5" value="0" aria-label="<?= $h($label) ?> Hz Kazanç">
                    </div>
                    <span class="eq-fader__freq"><?= $h($label) ?></span>
                </div>
                <?php
                    $fIndex++;
                endforeach;
                ?>
            </div>
        </div>

        <div class="settings-eq-footer">
            <span class="settings-eq-meta-badge">NEVA AUDIO DSP &bull; BIQUAD PEAKING EQ CHAIN &bull; 0 LATENCY RING BUFFER</span>
            <span class="settings-eq-status" id="eq-dsp-status">DSP Aktif &bull; 31-Bant 48kHz</span>
        </div>
    </section>

    <!-- BÖLÜM 3: PROFİL VE KULLANICI KİŞİSELLEŞTİRME -->
    <section class="settings-section" aria-labelledby="section-profile-title">
        <h2 id="section-profile-title" class="settings-section__title">Profil &amp; Cinsiyet Tercihi</h2>
        <p class="settings-section__desc">Karşılama mesajı ve avatar kişiselleştirme modu.</p>

        <div class="settings-options" role="radiogroup" aria-label="Cinsiyet Teması">
            <button type="button" class="settings-option settings-option--active" role="radio" aria-checked="true" data-gender="female">
                <span class="settings-option__icon" aria-hidden="true">👑</span>
                <span class="settings-option__label">Prenses</span>
                <span class="settings-option__desc">Işıl Peri (Aktif)</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-gender="male">
                <span class="settings-option__icon" aria-hidden="true">🧔</span>
                <span class="settings-option__label">Erkek</span>
                <span class="settings-option__desc">Standart Mod</span>
            </button>
            <button type="button" class="settings-option" role="radio" aria-checked="false" data-gender="neutral">
                <span class="settings-option__icon" aria-hidden="true">✨</span>
                <span class="settings-option__label">Nötr</span>
                <span class="settings-option__desc">Minimalist</span>
            </button>
        </div>
    </section>

    <!-- BÖLÜM 4: WİFİ AĞ YÖNETİCİSİ (Figma SSOT 2831:9665) -->
    <section class="settings-section" aria-labelledby="section-wifi-title">
        <div class="settings-section__header-row">
            <div>
                <h2 id="section-wifi-title" class="settings-section__title">Wi-Fi Bağlantısı</h2>
                <p class="settings-section__desc">Kablosuz ağ tarama ve bağlantı yönetimi.</p>
            </div>
            <label class="settings-switch" aria-label="Wi-Fi Aç/Kapat">
                <input type="checkbox" id="wifi-toggle" checked>
                <span class="settings-switch__slider"></span>
            </label>
        </div>

        <div class="settings-network-group">
            <h3 class="settings-network-group__label">Bağlı Olan Ağ</h3>
            <div class="settings-network-item settings-network-item--connected">
                <div class="settings-network-item__info">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="settings-network-item__icon">
                        <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4zm0 3.5c3.55 0 6.78 1.41 9.17 3.71L12 18.29 2.83 11.21C5.22 8.91 8.45 7.5 12 7.5z"/>
                    </svg>
                    <div>
                        <div class="settings-network-item__name">CoreMusic_Studio_5G</div>
                        <div class="settings-network-item__meta">Bağlandı &bull; Güç: %92 &bull; 5 GHz (WPA3)</div>
                    </div>
                </div>
                <span class="settings-badge settings-badge--active">Bağlı</span>
            </div>

            <h3 class="settings-network-group__label" style="margin-top: 12px;">Kullanılabilir Ağlar</h3>
            <div class="settings-network-list" id="wifi-list">
                <div class="settings-network-item" data-ssid="Studio_Control_Room" data-secure="true" data-signal="88">
                    <div class="settings-network-item__info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" class="settings-network-item__icon">
                            <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                        </svg>
                        <div>
                            <div class="settings-network-item__name">Studio_Control_Room</div>
                            <div class="settings-network-item__meta">Sinyal: %88 &bull; Güvenli</div>
                        </div>
                    </div>
                    <button type="button" class="settings-btn-connect" data-action="connect-wifi">Bağlan</button>
                </div>
                <div class="settings-network-item" data-ssid="CoreMusic_Guest" data-secure="false" data-signal="75">
                    <div class="settings-network-item__info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" class="settings-network-item__icon">
                            <path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98C20.93 5.9 16.69 4 12 4z"/>
                        </svg>
                        <div>
                            <div class="settings-network-item__name">CoreMusic_Guest</div>
                            <div class="settings-network-item__meta">Sinyal: %75 &bull; Açık</div>
                        </div>
                    </div>
                    <button type="button" class="settings-btn-connect" data-action="connect-wifi">Bağlan</button>
                </div>
            </div>
        </div>
    </section>

    <!-- BÖLÜM 5: BLUETOOTH AYGIT YÖNETİCİSİ (Figma SSOT 2831:9687) -->
    <section class="settings-section" aria-labelledby="section-bt-title">
        <div class="settings-section__header-row">
            <div>
                <h2 id="section-bt-title" class="settings-section__title">Bluetooth Aygıtları</h2>
                <p class="settings-section__desc">Kablosuz ses aygıtı eşleştirme ve yönetim.</p>
            </div>
            <label class="settings-switch" aria-label="Bluetooth Aç/Kapat">
                <input type="checkbox" id="bt-toggle" checked>
                <span class="settings-switch__slider"></span>
            </label>
        </div>

        <div class="settings-network-group">
            <h3 class="settings-network-group__label">Bağlı Olan Cihaz</h3>
            <div class="settings-network-item settings-network-item--connected">
                <div class="settings-network-item__info">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" class="settings-network-item__icon">
                        <path d="M17.71 7.71L12 2h-1v7.59L6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 11 14.41V22h1l5.71-5.71-4.3-4.29 4.3-4.29zM13 5.83l1.88 1.88L13 9.59V5.83zm1.88 10.46L13 18.17v-3.76l1.88 1.88z"/>
                    </svg>
                    <div>
                        <div class="settings-network-item__name">Sony WH-1000XM5</div>
                        <div class="settings-network-item__meta">LDAC 990 kbps &bull; Pil: %85 &bull; Ses Çıkışı</div>
                    </div>
                </div>
                <span class="settings-badge settings-badge--active">Bağlı</span>
            </div>

            <h3 class="settings-network-group__label" style="margin-top: 12px;">Kullanılabilir Cihazlar</h3>
            <div class="settings-network-list" id="bt-list">
                <div class="settings-network-item">
                    <div class="settings-network-item__info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" class="settings-network-item__icon">
                            <path d="M12 3c-4.97 0-9 4.03-9 9v7c0 1.1.9 2 2 2h4v-8H5v-1c0-3.87 3.13-7 7-7s7 3.13 7 7v1h-4v8h4c1.1 0 2-.9 2-2v-7c0-4.97-4.03-9-9-9z"/>
                        </svg>
                        <div>
                            <div class="settings-network-item__name">CoreMusic Amp BT</div>
                            <div class="settings-network-item__meta">Eşleşti &bull; Hazır</div>
                        </div>
                    </div>
                    <button type="button" class="settings-btn-connect">Bağlan</button>
                </div>
                <div class="settings-network-item">
                    <div class="settings-network-item__info">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" class="settings-network-item__icon">
                            <path d="M12 3c-4.97 0-9 4.03-9 9v7c0 1.1.9 2 2 2h4v-8H5v-1c0-3.87 3.13-7 7-7s7 3.13 7 7v1h-4v8h4c1.1 0 2-.9 2-2v-7c0-4.97-4.03-9-9-9z"/>
                        </svg>
                        <div>
                            <div class="settings-network-item__name">JBL Boombox 3</div>
                            <div class="settings-network-item__meta">Eşleşmedi</div>
                        </div>
                    </div>
                    <button type="button" class="settings-btn-connect">Eşle</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Wİ-Fİ ŞİFRE BAĞLANTI MODALI (Figma SSOT 2831:9644 / 2831:9648) -->
    <div class="settings-wifi-modal-overlay" id="wifi-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="wifi-modal-ssid">
        <div class="settings-wifi-modal">
            <div class="settings-wifi-modal__header">
                <div class="settings-wifi-modal__title-wrap">
                    <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" class="settings-wifi-modal__lock-icon" aria-hidden="true">
                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                    </svg>
                    <h3 class="settings-wifi-modal__title" id="wifi-modal-ssid">Studio_Control_Room</h3>
                </div>
            </div>
            <p class="settings-wifi-modal__meta" id="wifi-modal-meta">5GHz &bull; Mükemmel sinyal : %88 &bull; Güvenli Bağlantı</p>

            <form id="wifi-connect-form" onsubmit="return false;">
                <div class="settings-wifi-modal__field">
                    <label class="settings-wifi-modal__label" for="wifi-password-input">Kablosuz Ağ Şifresi</label>
                    <input type="password" id="wifi-password-input" class="settings-wifi-modal__input" placeholder="••••••••" autocomplete="current-password" required>
                </div>

                <label class="settings-wifi-modal__checkbox-wrap">
                    <input type="checkbox" id="wifi-auto-connect" class="settings-wifi-modal__checkbox" checked>
                    <span class="settings-wifi-modal__checkbox-text">Kablosuz ağa her zaman otomatik bağlan</span>
                </label>

                <div class="settings-wifi-modal__actions">
                    <button type="button" class="settings-wifi-modal__btn settings-wifi-modal__btn--cancel" id="wifi-modal-cancel">İptal</button>
                    <button type="submit" class="settings-wifi-modal__btn settings-wifi-modal__btn--connect" id="wifi-modal-submit">Bağlan</button>
                </div>
            </form>
        </div>
    </div>

    <script<?= $nonceAttr ?>>
    (function() {
        // Radio group options selection
        document.querySelectorAll('.settings-options').forEach(group => {
            const options = group.querySelectorAll('.settings-option');
            options.forEach(opt => {
                opt.addEventListener('click', function() {
                    options.forEach(o => {
                        o.classList.remove('settings-option--active');
                        o.setAttribute('aria-checked', 'false');
                    });
                    this.classList.add('settings-option--active');
                    this.setAttribute('aria-checked', 'true');
                });
            });
        });

        // Wi-Fi toggle
        const wifiToggle = document.getElementById('wifi-toggle');
        const wifiList = document.getElementById('wifi-list');
        if (wifiToggle && wifiList) {
            wifiToggle.addEventListener('change', function() {
                wifiList.style.opacity = this.checked ? '1' : '0.3';
                wifiList.style.pointerEvents = this.checked ? 'auto' : 'none';
            });
        }

        // Bluetooth toggle
        const btToggle = document.getElementById('bt-toggle');
        const btList = document.getElementById('bt-list');
        if (btToggle && btList) {
            btToggle.addEventListener('change', function() {
                btList.style.opacity = this.checked ? '1' : '0.3';
                btList.style.pointerEvents = this.checked ? 'auto' : 'none';
            });
        }

        // Wi-Fi Connect Modal Handler (Figma 2831:9644)
        const modalOverlay = document.getElementById('wifi-modal-overlay');
        const modalSsid = document.getElementById('wifi-modal-ssid');
        const modalMeta = document.getElementById('wifi-modal-meta');
        const modalInput = document.getElementById('wifi-password-input');
        const modalCancel = document.getElementById('wifi-modal-cancel');
        const modalForm = document.getElementById('wifi-connect-form');
        let currentTargetItem = null;

        function openWifiModal(item) {
            currentTargetItem = item;
            const ssid = item.getAttribute('data-ssid') || 'Wi-Fi Ağı';
            const signal = item.getAttribute('data-signal') || '80';
            if (modalSsid) modalSsid.textContent = ssid;
            if (modalMeta) modalMeta.textContent = '5GHz • Mükemmel sinyal : %' + signal + ' • Güvenli Bağlantı';
            if (modalInput) {
                modalInput.value = '';
            }
            if (modalOverlay) {
                modalOverlay.classList.add('is-active');
                setTimeout(() => modalInput && modalInput.focus(), 100);
            }
        }

        function closeWifiModal() {
            if (modalOverlay) modalOverlay.classList.remove('is-active');
            currentTargetItem = null;
        }

        document.querySelectorAll('[data-action="connect-wifi"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const item = this.closest('.settings-network-item');
                if (!item) return;
                const isSecure = item.getAttribute('data-secure') === 'true';
                if (isSecure) {
                    openWifiModal(item);
                } else {
                    btn.textContent = 'Bağlandı';
                    btn.classList.add('settings-badge--active');
                }
            });
        });

        if (modalCancel) {
            modalCancel.addEventListener('click', closeWifiModal);
        }

        if (modalOverlay) {
            modalOverlay.addEventListener('click', function(e) {
                if (e.target === modalOverlay) closeWifiModal();
            });
        }

        if (modalForm) {
            modalForm.addEventListener('submit', function(e) {
                e.preventDefault();
                if (currentTargetItem) {
                    const btn = currentTargetItem.querySelector('.settings-btn-connect');
                    if (btn) {
                        btn.textContent = 'Bağlandı';
                        btn.classList.add('settings-badge--active');
                    }
                }
                closeWifiModal();
            });
        }

        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalOverlay && modalOverlay.classList.contains('is-active')) {
                closeWifiModal();
            }
        });

        // ============================================================
        // 31-Band / 10-Band Equalizer DSP Controller
        // ============================================================
        const eqPresets = {
            flat: Array(31).fill(0),
            rock: [4.5, 4.0, 3.5, 3.0, 2.5, 1.5, 0.5, 0, -1.0, -1.5, -2.0, -1.5, -1.0, 0, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0],
            pop: [1.5, 2.0, 2.5, 3.0, 2.5, 1.5, 1.0, 0.5, 0, 0, 0.5, 1.0, 1.5, 2.0, 2.5, 2.5, 2.0, 1.5, 1.5, 2.0, 2.5, 3.0, 3.5, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0],
            jazz: [2.5, 2.5, 2.0, 1.5, 1.5, 1.0, 0.5, 0, 0, 0, 0.5, 1.0, 1.0, 1.5, 1.5, 1.5, 1.0, 1.0, 1.5, 2.0, 2.5, 2.5, 3.0, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0, 0],
            classical: [3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0, 0, 0, -0.5, -0.5, 0, 0, 0, 0.5, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0],
            electronic: [5.5, 6.0, 6.0, 5.5, 4.5, 3.5, 2.5, 1.5, 0.5, 0, -0.5, -1.0, -1.0, -0.5, 0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.5, 4.0, 4.5, 5.0, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5],
            vocal: [-2.0, -2.5, -2.5, -2.0, -1.5, -1.0, -0.5, 0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0, -0.5, -1.0, -1.5, -1.5, -2.0, -2.0, -2.5, -2.5],
            concert: [3.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0, -0.5, -0.5, 0, 0.5, 1.0, 1.5, 2.0, 2.5, 2.5, 3.0, 3.5, 4.0, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5, 0, 0],
            wedding: [4.0, 4.5, 4.0, 3.5, 3.0, 2.0, 1.5, 1.0, 0.5, 0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.0, 2.5, 2.5, 3.0, 3.5, 4.0, 4.5, 4.0, 3.5, 3.0, 2.5, 2.0, 1.5, 1.0, 0.5]
        };

        const eqRackContainer = document.getElementById('eq-rack-container');
        const eqModeBtns = document.querySelectorAll('.settings-eq-mode-btn');
        const eqBypassToggle = document.getElementById('eq-bypass-toggle');
        const eqPresetSelect = document.getElementById('eq-preset-select');
        const eqPreampGain = document.getElementById('eq-preamp-gain');
        const eqPreampVal = document.getElementById('eq-preamp-val');
        const eqResetBtn = document.getElementById('eq-reset-btn');
        const eqDspStatus = document.getElementById('eq-dsp-status');
        const eqFaders = document.querySelectorAll('.eq-fader__range');

        function saveEqState() {
            try {
                const values = Array.from(eqFaders).map(f => parseFloat(f.value));
                localStorage.setItem('coremusic_eq_bands', JSON.stringify(values));
            } catch (_) {}
        }

        // Mode Switching (31-Band vs 10-Band)
        eqModeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                eqModeBtns.forEach(b => b.classList.remove('is-active'));
                this.classList.add('is-active');
                const mode = this.getAttribute('data-eq-mode') || '31';
                if (eqRackContainer) eqRackContainer.setAttribute('data-mode', mode);
                if (eqDspStatus && (!eqBypassToggle || eqBypassToggle.checked)) {
                    eqDspStatus.textContent = `DSP Aktif • ${mode}-Bant 48kHz`;
                }
                try { localStorage.setItem('coremusic_eq_mode', mode); } catch (_) {}
            });
        });

        // Slider Fader Input
        eqFaders.forEach(slider => {
            slider.addEventListener('input', function() {
                const idx = this.getAttribute('data-index');
                const valElem = document.getElementById(`eq-val-${idx}`);
                const num = parseFloat(this.value);
                if (valElem) valElem.textContent = (num > 0 ? '+' : '') + num.toFixed(1);
                saveEqState();
            });
        });

        // Preamp Slider
        if (eqPreampGain && eqPreampVal) {
            eqPreampGain.addEventListener('input', function() {
                const num = parseFloat(this.value);
                eqPreampVal.textContent = (num > 0 ? '+' : '') + num.toFixed(1) + ' dB';
                try { localStorage.setItem('coremusic_eq_preamp', this.value); } catch (_) {}
            });
        }

        // Bypass Switch
        if (eqBypassToggle) {
            eqBypassToggle.addEventListener('change', function() {
                const isEnabled = this.checked;
                if (eqRackContainer) {
                    eqRackContainer.style.opacity = isEnabled ? '1' : '0.4';
                    eqRackContainer.style.pointerEvents = isEnabled ? 'auto' : 'none';
                }
                if (eqDspStatus) {
                    const activeMode = eqRackContainer ? eqRackContainer.getAttribute('data-mode') || '31' : '31';
                    eqDspStatus.textContent = isEnabled ? `DSP Aktif • ${activeMode}-Bant 48kHz` : 'DSP Devre Dışı (Bypass)';
                    eqDspStatus.style.color = isEnabled ? 'var(--color-green-500)' : 'rgba(255, 255, 255, 0.4)';
                }
                try { localStorage.setItem('coremusic_eq_bypass', isEnabled ? '0' : '1'); } catch (_) {}
            });
        }

        // Preset Selector
        if (eqPresetSelect) {
            eqPresetSelect.addEventListener('change', function() {
                const presetKey = this.value;
                const gains = eqPresets[presetKey] || eqPresets.flat;
                eqFaders.forEach((slider, i) => {
                    const g = gains[i] !== undefined ? gains[i] : 0;
                    slider.value = g;
                    const valElem = document.getElementById(`eq-val-${i}`);
                    if (valElem) valElem.textContent = (g > 0 ? '+' : '') + g.toFixed(1);
                });
                saveEqState();
            });
        }

        // Reset Button
        if (eqResetBtn) {
            eqResetBtn.addEventListener('click', function() {
                if (eqPresetSelect) eqPresetSelect.value = 'flat';
                if (eqPreampGain) eqPreampGain.value = 0;
                if (eqPreampVal) eqPreampVal.textContent = '0.0 dB';
                eqFaders.forEach((slider, i) => {
                    slider.value = 0;
                    const valElem = document.getElementById(`eq-val-${i}`);
                    if (valElem) valElem.textContent = '0';
                });
                saveEqState();
            });
        }

        // Restore saved EQ state from localStorage
        try {
            const savedMode = localStorage.getItem('coremusic_eq_mode');
            if (savedMode && (savedMode === '10' || savedMode === '31')) {
                eqModeBtns.forEach(b => {
                    b.classList.toggle('is-active', b.getAttribute('data-eq-mode') === savedMode);
                });
                if (eqRackContainer) eqRackContainer.setAttribute('data-mode', savedMode);
                if (eqDspStatus) eqDspStatus.textContent = `DSP Aktif • ${savedMode}-Bant 48kHz`;
            }
            const savedBypass = localStorage.getItem('coremusic_eq_bypass');
            if (savedBypass === '1' && eqBypassToggle) {
                eqBypassToggle.checked = false;
                eqBypassToggle.dispatchEvent(new Event('change'));
            }
            const savedPreamp = localStorage.getItem('coremusic_eq_preamp');
            if (savedPreamp !== null && eqPreampGain && eqPreampVal) {
                eqPreampGain.value = savedPreamp;
                const pNum = parseFloat(savedPreamp);
                eqPreampVal.textContent = (pNum > 0 ? '+' : '') + pNum.toFixed(1) + ' dB';
            }
            const savedBands = localStorage.getItem('coremusic_eq_bands');
            if (savedBands) {
                const parsed = JSON.parse(savedBands);
                if (Array.isArray(parsed)) {
                    eqFaders.forEach((slider, i) => {
                        if (parsed[i] !== undefined) {
                            slider.value = parsed[i];
                            const valElem = document.getElementById(`eq-val-${i}`);
                            if (valElem) valElem.textContent = (parsed[i] > 0 ? '+' : '') + parseFloat(parsed[i]).toFixed(1);
                        }
                    });
                }
            }
        } catch (_) {}
    })();
    </script>
</div>
