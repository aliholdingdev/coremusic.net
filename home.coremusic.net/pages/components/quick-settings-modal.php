<?php declare(strict_types=1);
/**
 * pages/components/quick-settings-modal.php — Global Quick Settings Modal
 * ------------------------------------------------------------------
 * SSOT : Figma node 1047:16153 (Linux 1024 - Settings Popup Menu Dark)
 *        Figma node 1047:17671 (Settings Popup Menu Tema Dark)
 *        Figma node 1047:17834 (Settings Popup Menu Ses Dark)
 *        Figma node 1047:16317 (Settings Popup Menu Sistem White Dark)
 * Layer: L3 Presentation · 04_Components (c-quick-settings.css)
 * ------------------------------------------------------------------
 */
?>

<!-- ═══ AYARLAR HIZLI POPUP PANELİ (Figma SSOT 1047:16153) ═══ -->
<div class="quick-settings-overlay" id="quick-settings-overlay" role="dialog" aria-modal="true" aria-labelledby="quick-settings-title">
    <div class="quick-settings-modal" role="document">
        <!-- Close Button -->
        <button type="button" class="quick-settings-close" id="quick-settings-close" aria-label="Ayarlar panelini kapat" title="Kapat">✕</button>

        <!-- Sol Navigasyon Sidebar (~200px) -->
        <aside class="quick-settings-sidebar">
            <div class="quick-settings-brand">
                <svg viewBox="0 0 24 24" class="quick-settings-brand__icon" fill="currentColor" aria-hidden="true">
                    <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/>
                </svg>
                <h3 class="quick-settings-brand__title" id="quick-settings-title">Ayarlar</h3>
            </div>

            <nav class="quick-settings-nav" aria-label="Ayar Kategorileri">
                <button type="button" class="quick-settings-tab-btn is-active" data-qs-tab="theme">
                    <svg viewBox="0 0 24 24" class="quick-settings-tab-btn__icon" fill="currentColor" aria-hidden="true">
                        <path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 18.99C4.16 19.18 4.29 19.5 4.56 19.5h14.88c.27 0 .4-.32.21-.51l-.62-.62C20.26 16.07 21 14.12 21 12c0-4.97-4.03-9-9-9zm0 14c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z"/>
                    </svg>
                    <span>Tema</span>
                </button>
                <button type="button" class="quick-settings-tab-btn" data-qs-tab="audio">
                    <svg viewBox="0 0 24 24" class="quick-settings-tab-btn__icon" fill="currentColor" aria-hidden="true">
                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                    </svg>
                    <span>Ses</span>
                </button>
                <button type="button" class="quick-settings-tab-btn" data-qs-tab="system">
                    <svg viewBox="0 0 24 24" class="quick-settings-tab-btn__icon" fill="currentColor" aria-hidden="true">
                        <path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
                    </svg>
                    <span>Sistem</span>
                </button>
                <button type="button" class="quick-settings-tab-btn" data-qs-tab="devices">
                    <svg viewBox="0 0 24 24" class="quick-settings-tab-btn__icon" fill="currentColor" aria-hidden="true">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                    <span>Hesap & Cihaz</span>
                </button>
            </nav>

            <div class="quick-settings-sidebar__footer">
                <a href="/ayarlar" class="quick-settings-full-link">
                    <span>Tüm Ayarları Aç</span>
                    <span>&rarr;</span>
                </a>
            </div>
        </aside>

        <!-- Sağ İçerik Paneli -->
        <div class="quick-settings-body">
            <!-- TAB 1: TEMA (Figma SSOT 1047:17671) -->
            <div class="quick-settings-tab-pane is-active" id="qs-pane-theme">
                <h4 class="quick-settings-title">Tema ve Renk Tercihleri</h4>
                <p class="quick-settings-desc">Arayüz temasını, neon vurgu rengini ve görselleştirme modlarını seçin.</p>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Görünüm Modu</span>
                    </div>
                    <div class="qs-mode-pills" role="radiogroup" aria-label="Görünüm Modu">
                        <button type="button" class="qs-pill-btn" data-qs-mode="light">Açık Mod</button>
                        <button type="button" class="qs-pill-btn is-active" data-qs-mode="dark">Koyu Mod</button>
                        <button type="button" class="qs-pill-btn" data-qs-mode="auto">Otomatik</button>
                    </div>
                </div>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Tema Rengi (Ana Renk)</span>
                    </div>
                    <div class="qs-swatches-grid" role="radiogroup" aria-label="Ana Renk">
                        <button type="button" class="qs-swatch-btn is-active" data-qs-theme="pink">
                            <span class="qs-swatch-circle" style="background: var(--cm-primary); box-shadow: 0 0 8px var(--cm-primary);"></span>
                            <span>Pembe Neon</span>
                        </button>
                        <button type="button" class="qs-swatch-btn" data-qs-theme="purple">
                            <span class="qs-swatch-circle" style="background: var(--color-violet-500); box-shadow: 0 0 8px var(--color-violet-500);"></span>
                            <span>Mor Gece</span>
                        </button>
                        <button type="button" class="qs-swatch-btn" data-qs-theme="blue">
                            <span class="qs-swatch-circle" style="background: var(--color-info-400); box-shadow: 0 0 8px var(--color-info-400);"></span>
                            <span>Siber Mavi</span>
                        </button>
                        <button type="button" class="qs-swatch-btn" data-qs-theme="neutral">
                            <span class="qs-swatch-circle" style="background: var(--color-neutral-600); box-shadow: 0 0 8px var(--color-neutral-600);"></span>
                            <span>Nötr Gri</span>
                        </button>
                    </div>
                </div>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Tema Kişiselleştirme</span>
                    </div>
                    <div class="qs-switch-row">
                        <div class="qs-switch-info">
                            <span class="qs-switch-name">Ekran Geçiş Animasyonları</span>
                            <span class="qs-switch-sub">Sayfa ve menü geçişlerinde pürüzsüz animasyonlar</span>
                        </div>
                        <label class="qs-toggle-switch" aria-label="Animasyonlar">
                            <input type="checkbox" id="qs-toggle-anim" checked>
                            <span class="qs-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="qs-switch-row">
                        <div class="qs-switch-info">
                            <span class="qs-switch-name">Müzik Görselleştirme Efektleri</span>
                            <span class="qs-switch-sub">Müzik çalarken canlı equalizer ve dalga boyu</span>
                        </div>
                        <label class="qs-toggle-switch" aria-label="Görselleştirme Efektleri">
                            <input type="checkbox" id="qs-toggle-vis" checked>
                            <span class="qs-toggle-slider"></span>
                        </label>
                    </div>
                    <div class="qs-switch-row">
                        <div class="qs-switch-info">
                            <span class="qs-switch-name">Buzlu Cam (Blur) Efekti</span>
                            <span class="qs-switch-sub">Arka plan buzlanma ve derinlik efekti</span>
                        </div>
                        <label class="qs-toggle-switch" aria-label="Buzlu Cam Efekti">
                            <input type="checkbox" id="qs-toggle-blur" checked>
                            <span class="qs-toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SES (Figma SSOT 1047:17834) -->
            <div class="quick-settings-tab-pane" id="qs-pane-audio">
                <h4 class="quick-settings-title">Ses ve DSP Motoru</h4>
                <p class="quick-settings-desc">Audiophile donanım çıkışı, bit rate ve analog amfi kalibrasyonu.</p>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Donanım Mimarisi</span>
                    </div>
                    <div class="qs-audio-badges">
                        <span class="qs-audio-badge">Class AB Amp (MJL21194/93)</span>
                        <span class="qs-audio-badge">AK4458 32-bit DAC</span>
                        <span class="qs-audio-badge">SNR &gt; 105 dB</span>
                        <span class="qs-audio-badge">THD+N &lt; 0.005%</span>
                    </div>
                </div>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Ses Seviyesi &amp; Kazanç</span>
                        <span id="qs-volume-val">75%</span>
                    </div>
                    <div class="qs-slider-group">
                        <div class="qs-slider-label-row">
                            <span>0% (Sessiz)</span>
                            <span>100% (Maksimum)</span>
                        </div>
                        <input type="range" class="qs-range-slider" id="qs-volume-slider" min="0" max="100" value="75" aria-label="Ses Seviyesi">
                    </div>
                </div>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Format &amp; Bitrate</span>
                    </div>
                    <div class="qs-mode-pills" role="radiogroup" aria-label="Akış Kalitesi">
                        <button type="button" class="qs-pill-btn is-active" data-qs-bitrate="flac">FLAC 24-bit / 96 kHz</button>
                        <button type="button" class="qs-pill-btn" data-qs-bitrate="master">Master 32-bit Float</button>
                        <button type="button" class="qs-pill-btn" data-qs-bitrate="mp3">MP3 320 kbps</button>
                    </div>
                </div>
            </div>

            <!-- TAB 3: SİSTEM (Figma SSOT 1047:16317) -->
            <div class="quick-settings-tab-pane" id="qs-pane-system">
                <h4 class="quick-settings-title">Sistem Durumu</h4>
                <p class="quick-settings-desc">Donanım kaynakları, depolama ve platform senkronizasyonu.</p>

                <div class="qs-sys-grid">
                    <div class="qs-sys-card">
                        <div class="qs-sys-card__title">İşlemci / CPU</div>
                        <div class="qs-sys-card__val">BCM2712 Quad-core @ 2.4GHz (%12)</div>
                    </div>
                    <div class="qs-sys-card">
                        <div class="qs-sys-card__title">Bellek / RAM</div>
                        <div class="qs-sys-card__val">1.8 GB / 8.0 GB LPDDR4X</div>
                    </div>
                    <div class="qs-sys-card">
                        <div class="qs-sys-card__title">Depolama (NVMe SSD)</div>
                        <div class="qs-sys-card__val">48.6 GB / 256 GB Boş</div>
                    </div>
                    <div class="qs-sys-card">
                        <div class="qs-sys-card__title">Sistem Sürümü</div>
                        <div class="qs-sys-card__val">CoreMusic Linux OS v3.2.0</div>
                    </div>
                </div>

                <div class="quick-settings-section" style="margin-top: 14px;">
                    <div class="quick-settings-section__heading">
                        <span>Senkronizasyon</span>
                    </div>
                    <div class="qs-mode-pills">
                        <button type="button" class="qs-pill-btn" data-qs-action="sync-mac">Mac OS İle Senkronize Et</button>
                        <button type="button" class="qs-pill-btn" data-qs-action="sync-win">Windows İle Senkronize Et</button>
                    </div>
                </div>

                <div class="qs-sys-actions">
                    <button type="button" class="qs-sys-btn" data-qs-action="reboot">Sistemi Yeniden Başlat</button>
                    <button type="button" class="qs-sys-btn qs-sys-btn--danger" data-qs-action="shutdown">Kapat</button>
                </div>
            </div>

            <!-- TAB 4: HESAP & CİHAZ -->
            <div class="quick-settings-tab-pane" id="qs-pane-devices">
                <h4 class="quick-settings-title">Oturum ve Bağlı Cihazlar</h4>
                <p class="quick-settings-desc">Aktif profil ve ses aygıtı yönetimi.</p>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Kullanıcı Profili</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--cm-primary); display: flex; align-items: center; justify-content: center; font-weight: bold; color: var(--cm-white);">
                                BA
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--cm-white);">Bayram Ali</div>
                                <div style="font-size: 11px; color: rgba(255,255,255,0.6);">Yönetici / Audiophile Üye</div>
                            </div>
                        </div>
                        <a href="/logout" class="qs-pill-btn" style="text-decoration: none; padding: 6px 14px; max-width: 120px;" data-no-spa>Çıkış Yap</a>
                    </div>
                </div>

                <div class="quick-settings-section">
                    <div class="quick-settings-section__heading">
                        <span>Varsayılan Ses Aygıtı</span>
                    </div>
                    <div class="qs-switch-row">
                        <div class="qs-switch-info">
                            <span class="qs-switch-name">CoreMusic Analog DAC (I2S)</span>
                            <span class="qs-switch-sub">AK4458 Direct Stream • 192kHz/32-bit</span>
                        </div>
                        <span style="font-size: 11px; color: var(--cm-primary); font-weight: bold;">Etkin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
