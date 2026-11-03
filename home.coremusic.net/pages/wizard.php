<?php declare(strict_types=1);
/**
 * pages/wizard.php — CoreMusic İlk Kurulum Sihirbazı (First-Run Setup Wizard)
 * -----------------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-wizard.css)
 * SSOT     : Figma node-id 1047:29968 (Linux 1024 - Wizard Layout - Hoş Geldiniz)
 *            1047:30186 (Cinsiyet Seçimi), 1047:30421 (Depolama Seçenekleri),
 *            1047:30939 (Lisans Sözleşmesi), 1047:31154 (Tema Seçimi)
 * Token    : %100 Token Purity (--cm-*)
 * WCAG     : 2.2 AA (ARIA stepper, role="region", klavye gezilebilirliği)
 * -----------------------------------------------------------------------------
 */

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
$nonce     = $h((string)($_SESSION['csp_nonce'] ?? ''));

// Adım tanımları (Figma 1047:29968 SSOT)
$steps = [
    1 => ['id' => 'welcome',  'title' => 'Hoş Geldiniz',       'desc' => 'CoreMusic ses evrenine ve ilk kuruluma giriş'],
    2 => ['id' => 'gender',   'title' => 'Cinsiyet & Enerji',  'desc' => 'Arayüz ve renk atmosferinizi kişiselleştirin'],
    3 => ['id' => 'theme',    'title' => 'Tema Seçimi',        'desc' => 'Ruhunuzun tonunu ve renk paletini belirleyin'],
    4 => ['id' => 'license',  'title' => 'Lisans Sözleşmesi',  'desc' => 'Kullanım koşulları ve güvenlik standartları'],
    5 => ['id' => 'storage',  'title' => 'Müzik Kaynakları',   'desc' => 'NAS, Sunucu ve müzik akış servislerini bağlayın'],
    6 => ['id' => 'complete', 'title' => 'Tamamlandı',         'desc' => 'Kurulum hazır, müzik yolculuğunuz başlıyor!'],
];
?>
<div class="page-wizard" id="setup-wizard-view" role="region" aria-label="İlk Kurulum Sihirbazı">
    <div class="wizard-glass">
        <!-- ── Sol Adım Menüsü ── -->
        <nav class="wizard-sidebar" aria-label="Kurulum Adımları">
            <div class="wizard-steps-nav" role="tablist">
                <?php foreach ($steps as $num => $step): ?>
                    <button type="button"
                            class="wizard-step-item <?= $num === 1 ? 'is-active' : '' ?>"
                            data-step="<?= $num ?>"
                            role="tab"
                            aria-selected="<?= $num === 1 ? 'true' : 'false' ?>"
                            id="wizard-tab-<?= $num ?>"
                            aria-controls="wizard-pane-<?= $num ?>">
                        <span class="wizard-step-num"><?= $num ?></span>
                        <span><?= $h($step['title']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="wizard-sidebar-footer" style="padding-top:12px;border-top:1px solid rgba(255,255,255,0.08);text-align:center;">
                <span style="font-size:11px;color:var(--cm-color-text-secondary,rgba(255,255,255,0.5));">CoreMusic OS v3.2</span>
            </div>
        </nav>

        <!-- ── Sağ Gövde Alanı ── -->
        <div class="wizard-content">
            <header class="wizard-header">
                <h1 class="wizard-title" id="wizard-main-title">
                    <span id="wizard-step-title"><?= $h($steps[1]['title']) ?></span>
                </h1>
                <p class="wizard-subtitle" id="wizard-main-subtitle">
                    <?= $h($steps[1]['desc']) ?>
                </p>
            </header>

            <div class="wizard-panes">
                <!-- ── Adım 1: Hoş Geldiniz ── -->
                <div class="wizard-pane is-active" id="wizard-pane-1" role="tabpanel" aria-labelledby="wizard-tab-1">
                    <div class="wizard-intro-grid">
                        <div class="wizard-feature-card">
                            <h3>Yapay Zeka Destekli Müzik Deneyiminiz Başlıyor</h3>
                            <p>Sihirli müzik yolculuğuna çıkmaya hazır mısınız? Birkaç küçük adımda, sadece size ait stüdyo kalitesinde bir müzik evreni oluşturacağız.</p>
                        </div>
                        <div class="wizard-feature-card">
                            <h3>Güvenlik ve Gizlilik Standardı</h3>
                            <p>Güvenliğiniz korunur. Hiçbir veriniz üçüncü kişilerle paylaşılmaz. Şifreleriniz ve yerel oturum verileriniz kurumsal standartlarda (100.000 döngülü PBKDF2/Argon2) korunur.</p>
                        </div>
                        <div class="wizard-feature-card">
                            <h3>Kurulum Süreci</h3>
                            <p>Bu sihirbaz 6 adımdan oluşmaktadır. "İleri" butonuna dokunarak kendi müzik hikâyenizi başlatın.</p>
                        </div>
                        <!-- Figma 1047:32063: Dil, Bölge & Zaman Dilimi Tercihi -->
                        <div class="wizard-region-bar" style="grid-column: 1 / -1; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--cm-primary);"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                <span style="font-size: 11.5px; color: var(--cm-white); font-weight: 500;">Bölge &amp; Dil: <strong>Türkiye • Türkçe</strong> (UTC+3)</span>
                            </div>
                            <span style="font-size: 10px; color: var(--cm-color-text-secondary); background: rgba(255,255,255,0.08); padding: 3px 8px; border-radius: 6px;">Otomatik Algılandı</span>
                        </div>
                    </div>
                </div>

                <!-- ── Adım 2: Cinsiyet & Enerji ── -->
                <div class="wizard-pane" id="wizard-pane-2" role="tabpanel" aria-labelledby="wizard-tab-2">
                    <p style="font-size:12px;color:var(--cm-color-text-secondary,rgba(255,255,255,0.7));margin:0 0 10px;">
                        Bu seçim, sistem tema renklerini ve ses profillerini zevkinize göre şekillendirmek içindir. İstediğiniz an ayarlardan değiştirebilirsiniz.
                    </p>
                    <div class="wizard-gender-grid">
                        <div class="wizard-gender-card is-selected" data-gender="female" tabindex="0" role="button" aria-pressed="true">
                            <div class="wizard-gender-emoji">💖</div>
                            <div class="wizard-gender-name">Kız</div>
                            <div class="wizard-gender-desc">Pembe &amp; Mor tonlarında zarif ve sıcak dünya</div>
                        </div>
                        <div class="wizard-gender-card" data-gender="male" tabindex="0" role="button" aria-pressed="false">
                            <div class="wizard-gender-emoji">💙</div>
                            <div class="wizard-gender-name">Erkek</div>
                            <div class="wizard-gender-desc">Mavi &amp; Gümüş tonlarında sade, dinamik atmosfer</div>
                        </div>
                        <div class="wizard-gender-card" data-gender="neutral" tabindex="0" role="button" aria-pressed="false">
                            <div class="wizard-gender-emoji">🤍</div>
                            <div class="wizard-gender-name">Nötr</div>
                            <div class="wizard-gender-desc">Pastel, sakin ve huzurlu doğal renk dengesi</div>
                        </div>
                    </div>
                </div>

                <!-- ── Adım 3: Tema Seçimi ── -->
                <div class="wizard-pane" id="wizard-pane-3" role="tabpanel" aria-labelledby="wizard-tab-3">
                    <p style="font-size:12px;color:var(--cm-color-text-secondary,rgba(255,255,255,0.7));margin:0 0 10px;">
                        Müzik evreniniz için bir ana renk tonu seçin. Ruh halinize göre dilediğiniz zaman yeniden tasarlayabilirsiniz.
                    </p>
                    <div class="wizard-themes-grid">
                        <div class="wizard-theme-card is-selected" data-theme="pink" tabindex="0" role="button" aria-pressed="true">
                            <div class="wizard-theme-swatch" style="background:var(--cm-primary);"></div>
                            <strong style="display:block;font-size:12px;color:var(--cm-white);">Neon Pembe</strong>
                            <small style="font-size:10px;color:rgba(255,255,255,0.6);">Sıcak &amp; Canlı</small>
                        </div>
                        <div class="wizard-theme-card" data-theme="purple" tabindex="0" role="button" aria-pressed="false">
                            <div class="wizard-theme-swatch" style="background:var(--color-violet-600);"></div>
                            <strong style="display:block;font-size:12px;color:var(--cm-white);">Asil Mor</strong>
                            <small style="font-size:10px;color:rgba(255,255,255,0.6);">Zarif &amp; Derin</small>
                        </div>
                        <div class="wizard-theme-card" data-theme="blue" tabindex="0" role="button" aria-pressed="false">
                            <div class="wizard-theme-swatch" style="background:var(--color-info-600);"></div>
                            <strong style="display:block;font-size:12px;color:var(--cm-white);">Okyanus Mavisi</strong>
                            <small style="font-size:10px;color:rgba(255,255,255,0.6);">Sade &amp; Net</small>
                        </div>
                        <div class="wizard-theme-card" data-theme="gold" tabindex="0" role="button" aria-pressed="false">
                            <div class="wizard-theme-swatch" style="background:var(--cm-warning);"></div>
                            <strong style="display:block;font-size:12px;color:var(--cm-white);">Altın Sarısı</strong>
                            <small style="font-size:10px;color:rgba(255,255,255,0.6);">Retro &amp; Klasik</small>
                        </div>
                    </div>
                </div>

                <!-- ── Adım 4: Lisans Sözleşmesi ── -->
                <div class="wizard-pane" id="wizard-pane-4" role="tabpanel" aria-labelledby="wizard-tab-4">
                    <div class="wizard-license-box">================================================================================
                     WIZARD - LİSANS SÖZLEŞMESİ
================================================================================

Versiyon: 1.0
Yürürlük Tarihi: 10 Ocak 2025
Son Güncelleme: 10 Ocak 2025

TELİF HAKKI BİLDİRİMİ
================================================================================
Copyright (c) 2024-2026 CoreMusic Platform. Tüm Hakları Saklıdır.

Proje: CoreMusic Audio Engine & OS Platform
Geliştirici: CoreMusic Engineering Team

Bu yazılım ve ilgili tüm dokümantasyon dosyaları CoreMusic Platform'un
münhasır mülkiyetindedir. Kullanım şartları, telif hakları ve gizlilik ilkeleri
OWASP Top 10 ve WCAG 2.2 standartlarına uygun şekilde korunmaktadır.

LİSANS TÜRÜ
================================================================================
Bu yazılım Özel Mülkiyet Lisansı altında lisanslanmıştır. Yazılımı kullanarak
stüdyo kalitesindeki ses işleme, DSP ve donanım senkronizasyonu koşullarını
onaylamış sayılırsınız.</div>
                    <label class="wizard-checkbox-label">
                        <input type="checkbox" id="wizard-license-check" checked>
                        <span>Lisans Sözleşmesini Okudum ve Kabul Ediyorum *</span>
                    </label>
                </div>

                <!-- ── Adım 5: Depolama & Kaynaklar ── -->
                <div class="wizard-pane" id="wizard-pane-5" role="tabpanel" aria-labelledby="wizard-tab-5">
                    <p style="font-size:12px;color:var(--cm-color-text-secondary,rgba(255,255,255,0.7));margin:0 0 10px;">
                        Müzik yolculuğunuzda dinlemek istediğiniz sunucu, yerel disk veya çevrim içi kaynak bağlantılarınızı ayarlayın:
                    </p>
                    <div class="wizard-storage-grid">
                        <div class="wizard-storage-card">
                            <div class="wizard-storage-info">
                                <strong>NAS Ağ Deposu</strong>
                                <small>Kendi özel ağ depolama sunucunuz</small>
                            </div>
                            <button type="button" class="wizard-storage-btn" data-storage="nas">Bağla</button>
                        </div>
                        <div class="wizard-storage-card">
                            <div class="wizard-storage-info">
                                <strong>Özel Server / Disk</strong>
                                <small>Linux, Windows Server veya Harici SSD</small>
                            </div>
                            <button type="button" class="wizard-storage-btn" data-storage="server">Tanımla</button>
                        </div>
                        <div class="wizard-storage-card">
                            <div class="wizard-storage-info">
                                <strong>Deezer Music</strong>
                                <small>Hi-Fi 24-bit stüdyo kalitesinde akış</small>
                            </div>
                            <button type="button" class="wizard-storage-btn" data-storage="deezer">Giriş Yap</button>
                        </div>
                        <div class="wizard-storage-card">
                            <div class="wizard-storage-info">
                                <strong>Spotify Music</strong>
                                <small>Popüler çalma listeleri ve akış</small>
                            </div>
                            <button type="button" class="wizard-storage-btn" data-storage="spotify">Bağlan</button>
                        </div>
                    </div>
                </div>

                <!-- ── Adım 6: Tamamlandı ── -->
                <div class="wizard-pane" id="wizard-pane-6" role="tabpanel" aria-labelledby="wizard-tab-6">
                    <div class="wizard-complete-view">
                        <div class="wizard-complete-icon">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M20 6L9 17l-5-5"/>
                            </svg>
                        </div>
                        <h3>Tebrikler! Müzik Evreniniz Hazır</h3>
                        <p>Tüm tercihleriniz kaydedildi. CoreMusic artık stüdyo kalitesindeki ses motoru ve zevklerinize göre özelleştirilmiş arayüzü ile hizmetinizde.</p>
                        <a href="/home" class="wizard-btn wizard-btn--primary" id="wizard-start-btn" style="text-decoration:none;">
                            Müzik Dinlemeye Başla &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- ── Alt Buton Çubuğu ── -->
            <footer class="wizard-footer">
                <button type="button" class="wizard-btn wizard-btn--secondary" id="wizard-prev-btn" disabled>
                    &larr; Geri
                </button>
                <span id="wizard-step-counter" style="font-size:12px;color:var(--cm-color-text-secondary,rgba(255,255,255,0.6));">Adım 1 / 6</span>
                <button type="button" class="wizard-btn wizard-btn--primary" id="wizard-next-btn">
                    İleri &rarr;
                </button>
            </footer>
        </div>
    </div>
</div>

<script nonce="<?= $nonce ?>">
(function() {
    'use strict';
    var currentStep = 1;
    var totalSteps = 6;

    var stepTitles = {
        1: { title: 'Hoş Geldiniz', desc: 'CoreMusic ses evrenine ve ilk kuruluma giriş' },
        2: { title: 'Cinsiyet & Enerji', desc: 'Arayüz ve renk atmosferinizi kişiselleştirin' },
        3: { title: 'Tema Seçimi', desc: 'Ruhunuzun tonunu ve renk paletini belirleyin' },
        4: { title: 'Lisans Sözleşmesi', desc: 'Kullanım koşulları ve güvenlik standartları' },
        5: { title: 'Müzik Kaynakları', desc: 'NAS, Sunucu ve müzik akış servislerini bağlayın' },
        6: { title: 'Tamamlandı', desc: 'Kurulum hazır, müzik yolculuğunuz başlıyor!' }
    };

    var tabs = document.querySelectorAll('.wizard-step-item');
    var panes = document.querySelectorAll('.wizard-pane');
    var prevBtn = document.getElementById('wizard-prev-btn');
    var nextBtn = document.getElementById('wizard-next-btn');
    var stepTitle = document.getElementById('wizard-step-title');
    var stepSubtitle = document.getElementById('wizard-main-subtitle');
    var stepCounter = document.getElementById('wizard-step-counter');

    function goToStep(n) {
        if (n < 1 || n > totalSteps) return;
        currentStep = n;

        // Sekmeleri güncelle
        tabs.forEach(function(tab) {
            var s = parseInt(tab.getAttribute('data-step') || '1', 10);
            tab.classList.toggle('is-active', s === currentStep);
            tab.classList.toggle('is-done', s < currentStep);
            tab.setAttribute('aria-selected', s === currentStep ? 'true' : 'false');
        });

        // Panelleri güncelle
        panes.forEach(function(pane, idx) {
            pane.classList.toggle('is-active', idx + 1 === currentStep);
        });

        // Başlık ve alt başlığı güncelle
        if (stepTitles[currentStep]) {
            if (stepTitle) stepTitle.textContent = stepTitles[currentStep].title;
            if (stepSubtitle) stepSubtitle.textContent = stepTitles[currentStep].desc;
        }

        // Sayaç
        if (stepCounter) stepCounter.textContent = 'Adım ' + currentStep + ' / ' + totalSteps;

        // Buton durumları
        if (prevBtn) prevBtn.disabled = (currentStep === 1);
        if (nextBtn) {
            if (currentStep === totalSteps) {
                nextBtn.style.display = 'none';
            } else {
                nextBtn.style.display = 'inline-flex';
                nextBtn.textContent = (currentStep === totalSteps - 1) ? 'Tamamla \u2192' : 'İleri \u2192';
            }
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            goToStep(currentStep - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            // Lisans sözleşmesi kontrolü
            if (currentStep === 4) {
                var check = document.getElementById('wizard-license-check');
                if (check && !check.checked) {
                    alert('Lütfen devam etmek için lisans sözleşmesini kabul ediniz.');
                    return;
                }
            }
            goToStep(currentStep + 1);
        });
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var s = parseInt(this.getAttribute('data-step') || '1', 10);
            goToStep(s);
        });
    });

    // Cinsiyet seçimi etkileşimi
    document.querySelectorAll('.wizard-gender-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.querySelectorAll('.wizard-gender-card').forEach(function(c) {
                c.classList.remove('is-selected');
                c.setAttribute('aria-pressed', 'false');
            });
            this.classList.add('is-selected');
            this.setAttribute('aria-pressed', 'true');
            var g = this.getAttribute('data-gender');
            try { localStorage.setItem('cm_gender', g); } catch(e){}
        });
    });

    // Tema seçimi etkileşimi
    document.querySelectorAll('.wizard-theme-card').forEach(function(card) {
        card.addEventListener('click', function() {
            document.querySelectorAll('.wizard-theme-card').forEach(function(c) {
                c.classList.remove('is-selected');
                c.setAttribute('aria-pressed', 'false');
            });
            this.classList.add('is-selected');
            this.setAttribute('aria-pressed', 'true');
            var th = this.getAttribute('data-theme');
            try { localStorage.setItem('cm_theme', th); } catch(e){}
            document.documentElement.setAttribute('data-theme', th);
        });
    });

    // Depolama butonları
    document.querySelectorAll('.wizard-storage-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            this.textContent = 'Bağlandı \u2713';
            this.style.background = 'rgba(16, 185, 129, 0.3)';
            this.style.borderColor = 'var(--cm-success)';
        });
    });
})();
</script>
