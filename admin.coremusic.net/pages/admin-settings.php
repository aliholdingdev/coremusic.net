<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Sistem & Ses Motoru Ayarları (Light Theme)
 *
 * Yer: admin.coremusic.net/pages/admin-settings.php
 * Tasarım: Açık Tema (Light Theme) Glassmorphism
 */

$pageTitle = 'Sistem & Platform Ayarları';
$activeNav = 'admin-settings';

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$csrf = (string)($_SESSION['csrf_token'] ?? '');

ob_start();
?>
<div class="adm-settings-page" data-mode="light">
    <form class="adm-settings-form" method="post" action="/admin-settings">
        <input type="hidden" name="csrf_token" value="<?= $h($csrf) ?>">

        <div class="adm-settings-grid">
            <!-- 1. Ses Motoru & Audio DSP Ayarları -->
            <div class="adm-card adm-card--light adm-settings-card">
                <div class="adm-card__header">
                    <div class="adm-card__icon-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="23"/><line x1="8" y1="23" x2="16" y2="23"/></svg>
                    </div>
                    <div>
                        <h3 class="adm-card__title">Ses Motoru & DSP Ayarları</h3>
                        <p class="adm-card__subtitle">Bit-Exact akış, ASIO/WASAPI ve donanım katmanı</p>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="dsp-driver">Ses Sürücüsü Modu</label>
                    <select id="dsp-driver" name="dsp_driver" class="adm-form__select">
                        <option value="wasapi-exclusive" selected>WASAPI Exclusive (Düşük Gecikme - 1.2ms)</option>
                        <option value="asio">ASIO Direct Stream (Bit-Exact Pro)</option>
                        <option value="shared">Windows Shared Audio Engine</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="dsp-buffer">Tampon Boyutu (Buffer Size)</label>
                    <select id="dsp-buffer" name="dsp_buffer" class="adm-form__select">
                        <option value="64" selected>64 Samples (Audiophile Ultra-Fast / 1.2ms)</option>
                        <option value="128">128 Samples (Önerilen / 2.7ms)</option>
                        <option value="256">256 Samples (Yüksek Kararlılık / 5.4ms)</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__checkbox-label">
                        <input type="checkbox" name="class_ab_protection" value="1" checked class="adm-form__checkbox">
                        <span>Class AB Amfi DC Koruma ve Sıcaklık Telemetrisi Aktif</span>
                    </label>
                </div>
            </div>

            <!-- 2. Görünüm & Tema Tercihleri -->
            <div class="adm-card adm-card--light adm-settings-card">
                <div class="adm-card__header">
                    <div class="adm-card__icon-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ec4899" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2"/><path d="M12 21v2"/><path d="M4.22 4.22l1.42 1.42"/><path d="M18.36 18.36l1.42 1.42"/><path d="M1 12h2"/><path d="M21 12h2"/><path d="M4.22 19.78l1.42-1.42"/><path d="M18.36 5.64l1.42-1.42"/></svg>
                    </div>
                    <div>
                        <h3 class="adm-card__title">Görünüm & Tema Modu</h3>
                        <p class="adm-card__subtitle">Arayüz renk paleti ve cam efektleri</p>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="theme-mode">Panel Tema Modu</label>
                    <select id="theme-mode" name="theme_mode" class="adm-form__select">
                        <option value="light" selected>Açık Tema (Light Theme — Özel Beyaz Cam)</option>
                        <option value="system">Sistem Tercihine Göre Otomatik</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="glass-intensity">Cam Bulanıklık Yoğunluğu (Backdrop Blur)</label>
                    <select id="glass-intensity" name="glass_intensity" class="adm-form__select">
                        <option value="high" selected>Yüksek (24px Ultra Premium Blur)</option>
                        <option value="medium">Orta (16px Standart Cam)</option>
                        <option value="low">Düşük (8px Hafif Mat)</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__checkbox-label">
                        <input type="checkbox" name="ambient_glow" value="1" checked class="adm-form__checkbox">
                        <span>Arka plan ambient dinamik ışıltıları etkinleştir</span>
                    </label>
                </div>
            </div>

            <!-- 3. Güvenlik & Oturum Köprüsü -->
            <div class="adm-card adm-card--light adm-settings-card">
                <div class="adm-card__header">
                    <div class="adm-card__icon-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div>
                        <h3 class="adm-card__title">Güvenlik & Oturum Yönetimi</h3>
                        <p class="adm-card__subtitle">ADR-058 tek yetkili auth ve köprü parametreleri</p>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="session-timeout">Yönetici Oturum Zaman Aşımı</label>
                    <select id="session-timeout" name="session_timeout" class="adm-form__select">
                        <option value="30">30 Dakika Hareketsizlik</option>
                        <option value="60" selected>60 Dakika Hareketsizlik</option>
                        <option value="120">2 Saat Hareketsizlik</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="rate-limit">API İstek Sınırı (Rate Limiting)</label>
                    <select id="rate-limit" name="rate_limit" class="adm-form__select">
                        <option value="60" selected>60 İstek / Dakika (Standart Koruma)</option>
                        <option value="120">120 İstek / Dakika</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__checkbox-label">
                        <input type="checkbox" name="strict_csp" value="1" checked class="adm-form__checkbox">
                        <span>Strict CSP Nonce & Frame-Ancestors 'none' zorla</span>
                    </label>
                </div>
            </div>

            <!-- 4. Platform & Akış Kalitesi -->
            <div class="adm-card adm-card--light adm-settings-card">
                <div class="adm-card__header">
                    <div class="adm-card__icon-badge">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div>
                        <h3 class="adm-card__title">Yayın & Ağ Performansı</h3>
                        <p class="adm-card__subtitle">Medya sunucusu ve bant genişliği kontrolü</p>
                    </div>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="default-stream">Varsayılan Akış Formatı</label>
                    <select id="default-stream" name="default_stream" class="adm-form__select">
                        <option value="flac-auto" selected>FLAC Hi-Res (Adaptif Bant Genişliği)</option>
                        <option value="mp3-320">MP3 320k Sabit Bitrate</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__label" for="cdn-cache-ttl">Önbellek Yaşam Süresi (CDN TTL)</label>
                    <select id="cdn-cache-ttl" name="cdn_cache_ttl" class="adm-form__select">
                        <option value="86400" selected>24 Saat (Önerilen)</option>
                        <option value="604800">7 Gün</option>
                    </select>
                </div>

                <div class="adm-form__group">
                    <label class="adm-form__checkbox-label">
                        <input type="checkbox" name="nvme_cache" value="1" checked class="adm-form__checkbox">
                        <span>NVMe SSD parça önbelleği aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="adm-settings-footer">
            <button type="button" class="adm-btn adm-btn--secondary">Varsayılan Ayarlara Dön</button>
            <button type="submit" class="adm-btn adm-btn--primary">Ayarları Kaydet ve Uygula</button>
        </div>
    </form>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
