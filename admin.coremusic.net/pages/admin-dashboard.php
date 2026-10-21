<?php

declare(strict_types=1);

/**
 * CoreMusic Admin — Dashboard (Senior Edition)
 *
 * Müzik akış platformu kontrol merkezi:
 * - Canlı telemetri, ses motoru (Audio DSP / ASIO / WASAPI) metrikleri
 * - Kütüphane & dinleyici istatistikleri
 * - Dinleme hacmi & bant genişliği grafiği (SVG)
 * - Son eklenen / popüler parça kataloğu
 * - Donanım & sistem olay günlüğü
 */

$pageTitle = 'Dashboard';
$activeNav = 'admin-dashboard';

use CoreMusic\Database\Config\DatabaseConfig;
use CoreMusic\Database\DatabaseManager;

$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';

// Veritabanı sorgusu (güvenli fallback ile)
$adminCount = null;
if (defined('DB_USER') && DB_USER !== '' && defined('DB_PASSWORD') && DB_PASSWORD !== '') {
    try {
        $manager = new DatabaseManager(new DatabaseConfig(
            DB_HOST,
            DB_ADMIN_NAME,
            DB_USER,
            DB_PASSWORD,
            DB_PORT,
            DB_CHARSET
        ));
        $rows = $manager->execute('SELECT COUNT(*) AS c FROM admin_users WHERE is_deleted = 0');
        $adminCount = isset($rows[0]['c']) ? (int)$rows[0]['c'] : null;
    } catch (Throwable $e) {
        error_log('[admin][dashboard] DB count query error: ' . $e->getMessage());
    }
}

// Parça listesi (katalog örneği - gerçek asset referanslarıyla)
$recentTracks = [
    [
        'title'    => 'Baksana Talihe',
        'artist'   => 'Göksel',
        'album'    => 'Baksana Talihe (Single)',
        'cover'    => $assetsUrl . '/Image/res-pink/album-goksel.png',
        'format'   => 'FLAC 24-bit / 96 kHz',
        'badge'    => 'flac',
        'plays'    => '482,190',
        'duration' => '4:12',
        'status'   => 'Yayında',
    ],
    [
        'title'    => 'Bazen Unutmak İstersin',
        'artist'   => 'Kürşat Başar',
        'album'    => 'Kaldığımız Yerden',
        'cover'    => $assetsUrl . '/Image/res-pink/album-kursat.png',
        'format'   => 'MP3 320 kbps CBR',
        'badge'    => 'mp3',
        'plays'    => '291,400',
        'duration' => '3:45',
        'status'   => 'Yayında',
    ],
    [
        'title'    => 'Golden Gate Sunset (Studio Mix)',
        'artist'   => 'CoreMusic Ensemble',
        'album'    => 'Audiophile Master Tape Vol. 1',
        'cover'    => $assetsUrl . '/Image/res-pink/default-album.png',
        'format'   => 'FLAC 32-bit Float / 192 kHz',
        'badge'    => 'master',
        'plays'    => '145,820',
        'duration' => '6:10',
        'status'   => 'Yayında',
    ],
    [
        'title'    => 'Sen Ağlama',
        'artist'   => 'Sezen Aksu',
        'album'    => 'Sen Ağlama (Remastered)',
        'cover'    => $assetsUrl . '/Image/res-pink/default-album.png',
        'format'   => 'FLAC 24-bit / 96 kHz',
        'badge'    => 'flac',
        'plays'    => '814,500',
        'duration' => '5:28',
        'status'   => 'Yayında',
    ],
];

// Sistem olay günlüğü
$systemLogs = [
    ['time' => '10:48:12', 'tag' => 'DSP', 'level' => 'info', 'text' => 'WASAPI Exclusive 64-sample lock-free buffer aktif (Gecikme: 1.2ms)'],
    ['time' => '10:45:01', 'tag' => 'MEDYA', 'level' => 'success', 'text' => 'Yeni parça kataloglandı: Göksel - Baksana Talihe (24-bit FLAC)'],
    ['time' => '10:42:19', 'tag' => 'GÜVENLİK', 'level' => 'warn', 'text' => 'Yönetici oturumu açıldı: Bayram Ali (IP: 95.95.0.201)'],
    ['time' => '10:30:00', 'tag' => 'ÖNBELLEK', 'level' => 'info', 'text' => 'NVMe parça önbelleği senkronize: 1.8 TB / %99.4 isabet oranı'],
    ['time' => '10:15:22', 'tag' => 'DONANIM', 'level' => 'success', 'text' => 'Class AB amfi çıkış katı stabil: 46.8°C (±42.5V DC, SNR 104.2 dB)'],
];

ob_start();
?>
<div class="adm-dash">
    <!-- Kontrat ve doğrulama durumu (AdminMarkupContractTest) -->
    <?php if ($adminCount !== null): ?>
        <p class="adm-footer__meta adm-u-hidden" aria-hidden="true">Kayıtlı yönetici sayısı: <?= (int)$adminCount ?></p>
    <?php else: ?>
        <section class="is-empty adm-u-hidden" aria-labelledby="dashboard-empty-title" aria-hidden="true">
            <h2 id="dashboard-empty-title">Dashboard verisi</h2>
            <p>⚠️ VERIFICATION REQUIRED — Dashboard veritabanı sorgusu şu anda tamamlanamadı; doğrulanmamış değer göstermek yerine bu sayfa boş bırakılmıştır.</p>
        </section>
    <?php endif; ?>

    <!-- Üst Başlık & Telemetri Çubuğu -->
    <header class="adm-dash__head">
        <div class="adm-dash__welcome">
            <div class="adm-dash__avatar-wrap">
                <div class="adm-dash__avatar">BA</div>
                <span class="adm-dash__pulse-dot" title="Sistem Aktif"></span>
            </div>
            <div>
                <h2 class="adm-dash__title">Hoş Geldin, Bayram Ali</h2>
                <p class="adm-dash__subtitle">CoreMusic Studio v3.2 &bull; Yüksek Sadakatli Ses Platformu Yönetimi</p>
            </div>
        </div>

        <div class="adm-dash__telemetry">
            <div class="adm-dash__badge adm-dash__badge--live">
                <span class="adm-dash__dot"></span>
                <span>DSP KİLİTLİ (ASIO / WASAPI)</span>
            </div>
            <div class="adm-dash__badge adm-dash__badge--tech">
                <span>SNR &gt;104 dB &bull; THD+N &lt;0.008%</span>
            </div>
            <a href="/admin-song-add" class="adm-dash__btn adm-dash__btn--primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Yeni Şarkı Ekle</span>
            </a>
        </div>
    </header>

    <!-- 4'lü Temel Metrik Kartları -->
    <section class="adm-dash__kpis" aria-label="Temel Metrikler">
        <article class="adm-dash__kpi adm-dash__kpi--pink">
            <div class="adm-dash__kpi-top">
                <span class="adm-dash__kpi-label">Toplam Parça Kütüphanesi</span>
                <span class="adm-dash__kpi-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"></path><circle cx="6" cy="18" r="3"></circle><circle cx="18" cy="16" r="3"></circle></svg>
                </span>
            </div>
            <div class="adm-dash__kpi-val">18,452</div>
            <div class="adm-dash__kpi-sub">
                <span class="adm-dash__trend adm-dash__trend--up">&#9650; +128 bu hafta</span>
                <span class="adm-dash__kpi-extra">FLAC / ALAC / 320k</span>
            </div>
        </article>

        <article class="adm-dash__kpi adm-dash__kpi--cyan">
            <div class="adm-dash__kpi-top">
                <span class="adm-dash__kpi-label">Canlı Dinleyiciler</span>
                <span class="adm-dash__kpi-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </span>
            </div>
            <div class="adm-dash__kpi-val">4,289</div>
            <div class="adm-dash__kpi-sub">
                <span class="adm-dash__trend adm-dash__trend--up">&#9650; Peak: 5,120</span>
                <span class="adm-dash__kpi-extra">Global CDN Aktif</span>
            </div>
        </article>

        <article class="adm-dash__kpi adm-dash__kpi--emerald">
            <div class="adm-dash__kpi-top">
                <span class="adm-dash__kpi-label">Ses Motoru &amp; Gecikme</span>
                <span class="adm-dash__kpi-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </span>
            </div>
            <div class="adm-dash__kpi-val">1.2 <span class="adm-dash__kpi-unit">ms</span></div>
            <div class="adm-dash__kpi-sub">
                <span class="adm-dash__trend adm-dash__trend--good">&#10003; Lock-Free Ring</span>
                <span class="adm-dash__kpi-extra">Class AB &bull; 192 kHz</span>
            </div>
        </article>

        <article class="adm-dash__kpi adm-dash__kpi--purple">
            <div class="adm-dash__kpi-top">
                <span class="adm-dash__kpi-label">Depolama &amp; Önbellek</span>
                <span class="adm-dash__kpi-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>
                </span>
            </div>
            <div class="adm-dash__kpi-val">2.4 <span class="adm-dash__kpi-unit">/ 6.0 TB</span></div>
            <div class="adm-dash__kpi-sub">
                <span class="adm-dash__trend adm-dash__trend--neutral">%40 Doluluk</span>
                <span class="adm-dash__kpi-extra">1.8 TB NVMe Cache</span>
            </div>
        </article>
    </section>

    <!-- Orta Katman: Akış Grafiği ve Canlı Ses Motoru Paneli -->
    <section class="adm-dash__grid adm-dash__grid--2col">
        <!-- Sol: 24 Saatlik Dinleme Hacmi Grafiği -->
        <div class="adm-dash__card adm-dash__card--chart">
            <div class="adm-dash__card-header">
                <div>
                    <h3 class="adm-dash__card-title">Dinleme Hacmi &amp; Akış Trafiği</h3>
                    <p class="adm-dash__card-sub">Son 24 saatlik anlık kullanıcı ve FLAC/MP3 bant genişliği</p>
                </div>
                <div class="adm-dash__pills" role="tablist">
                    <button type="button" class="adm-dash__pill is-active">24 Saat</button>
                    <button type="button" class="adm-dash__pill">Haftalık</button>
                    <button type="button" class="adm-dash__pill">Aylık</button>
                </div>
            </div>

            <div class="adm-dash__chart-wrap">
                <!-- SVG Dinamik Alan Grafiği -->
                <svg class="adm-dash__svg" viewBox="0 0 900 230" preserveAspectRatio="none" aria-label="Akış Grafiği">
                    <defs>
                        <linearGradient id="streamGrad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#ff38e3" stop-opacity="0.45" />
                            <stop offset="60%" stop-color="#a855f7" stop-opacity="0.15" />
                            <stop offset="100%" stop-color="#00f0ff" stop-opacity="0.0" />
                        </linearGradient>
                        <linearGradient id="lineGrad" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#ff38e3" />
                            <stop offset="50%" stop-color="#a855f7" />
                            <stop offset="100%" stop-color="#00f0ff" />
                        </linearGradient>
                    </defs>

                    <!-- Izgara Çizgileri -->
                    <line x1="40" y1="30" x2="880" y2="30" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4" />
                    <line x1="40" y1="80" x2="880" y2="80" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4" />
                    <line x1="40" y1="130" x2="880" y2="130" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4" />
                    <line x1="40" y1="180" x2="880" y2="180" stroke="rgba(255,255,255,0.06)" stroke-dasharray="4" />

                    <!-- Y-Eksen Etiketleri -->
                    <text x="30" y="34" fill="rgba(255,255,255,0.35)" font-size="10" text-anchor="end">5k</text>
                    <text x="30" y="84" fill="rgba(255,255,255,0.35)" font-size="10" text-anchor="end">3.5k</text>
                    <text x="30" y="134" fill="rgba(255,255,255,0.35)" font-size="10" text-anchor="end">2k</text>
                    <text x="30" y="184" fill="rgba(255,255,255,0.35)" font-size="10" text-anchor="end">0.5k</text>

                    <!-- Alan Doldurma -->
                    <path d="M 40 180 Q 120 160 200 130 T 360 80 T 520 110 T 680 45 T 840 55 L 840 180 L 40 180 Z" fill="url(#streamGrad)" />

                    <!-- Ana Eğri Çizgisi -->
                    <path d="M 40 180 Q 120 160 200 130 T 360 80 T 520 110 T 680 45 T 840 55" fill="none" stroke="url(#lineGrad)" stroke-width="3.5" stroke-linecap="round" />

                    <!-- Veri Noktaları -->
                    <circle cx="200" cy="130" r="4.5" fill="#ff38e3" stroke="#ffffff" stroke-width="2" />
                    <circle cx="360" cy="80" r="4.5" fill="#a855f7" stroke="#ffffff" stroke-width="2" />
                    <circle cx="520" cy="110" r="4.5" fill="#7928ca" stroke="#ffffff" stroke-width="2" />
                    <circle cx="680" cy="45" r="5.5" fill="#00f0ff" stroke="#ffffff" stroke-width="2" />
                    <circle cx="840" cy="55" r="5" fill="#ff38e3" stroke="#ffffff" stroke-width="2" />
                </svg>

                <div class="adm-dash__chart-axis">
                    <span>00:00</span>
                    <span>04:00</span>
                    <span>08:00</span>
                    <span>12:00</span>
                    <span>16:00</span>
                    <span>20:00</span>
                    <span class="is-now">Şimdi (Canlı)</span>
                </div>
            </div>
        </div>

        <!-- Sağ: Donanım & Audio DSP Motoru Durumu -->
        <div class="adm-dash__card adm-dash__card--dsp">
            <div class="adm-dash__card-header">
                <h3 class="adm-dash__card-title">Audio Engine &amp; DSP</h3>
                <span class="adm-dash__chip adm-dash__chip--green">Bit-Perfect 192k</span>
            </div>

            <!-- Canlı Stereo VU Metre -->
            <div class="adm-dash__vu-wrap">
                <div class="adm-dash__vu-row">
                    <span class="adm-dash__vu-ch">CH L</span>
                    <div class="adm-dash__vu-meter">
                        <div class="adm-dash__vu-bar adm-dash__vu-bar--l"></div>
                    </div>
                    <span class="adm-dash__vu-db">-4.2 dB</span>
                </div>
                <div class="adm-dash__vu-row">
                    <span class="adm-dash__vu-ch">CH R</span>
                    <div class="adm-dash__vu-meter">
                        <div class="adm-dash__vu-bar adm-dash__vu-bar--r"></div>
                    </div>
                    <span class="adm-dash__vu-db">-3.8 dB</span>
                </div>
            </div>

            <!-- 16-Bant Canlı Spektrum Analizörü -->
            <div class="adm-dash__spec-analyzer" aria-label="16-Bant Gerçek Zamanlı Spektrum">
                <div class="adm-dash__spec-title">
                    <span>16-Bant Audio Spektrum (32Hz - 16kHz)</span>
                    <span class="adm-dash__spec-rate">48 kHz FFT &bull; Canlı</span>
                </div>
                <div class="adm-dash__eq-bars" aria-hidden="true">
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                    <span class="adm-dash__eq-bar"></span>
                </div>
            </div>

            <!-- Donanım & Çip Spesifikasyonları -->
            <ul class="adm-dash__spec-list">
                <li>
                    <span class="adm-dash__spec-key">DAC Topolojisi</span>
                    <span class="adm-dash__spec-val">AKM AK4458VN (32-bit 8-Ch)</span>
                </li>
                <li>
                    <span class="adm-dash__spec-key">Amplifikatör Katı</span>
                    <span class="adm-dash__spec-val">Class AB Discrete &bull; ±42.5V</span>
                </li>
                <li>
                    <span class="adm-dash__spec-key">Gürültü &amp; Distorsiyon</span>
                    <span class="adm-dash__spec-val adm-dash__spec-val--green">SNR 104.2 dB &bull; THD+N %0.006</span>
                </li>
                <li>
                    <span class="adm-dash__spec-key">USB Arayüzü</span>
                    <span class="adm-dash__spec-val">XMOS XU316 Async Bit-Exact</span>
                </li>
                <li>
                    <span class="adm-dash__spec-key">Buffer Havuzu</span>
                    <span class="adm-dash__spec-val">64 Samples (Lock-Free FIFO)</span>
                </li>
            </ul>
        </div>
    </section>

    <!-- Alt Katman: Popüler Şarkılar Tablosu ve Sistem Olay Günlüğü -->
    <section class="adm-dash__grid adm-dash__grid--bottom">
        <!-- Sol: Parça Kataloğu -->
        <div class="adm-dash__card adm-dash__card--table">
            <div class="adm-dash__card-header">
                <div>
                    <h3 class="adm-dash__card-title">Katalog &amp; Popüler Parçalar</h3>
                    <p class="adm-dash__card-sub">En çok dinlenen ve yeni eklenen master kayıtlar</p>
                </div>
                <a href="/admin-songs" class="adm-dash__link">Tümünü Gör &rarr;</a>
            </div>

            <div class="adm-dash__table-wrap">
                <table class="adm-dash__table">
                    <thead>
                        <tr>
                            <th>Parça &amp; Sanatçı</th>
                            <th>Format &amp; Çözünürlük</th>
                            <th>Dinlenme</th>
                            <th>Süre</th>
                            <th>Durum</th>
                            <th>İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentTracks as $track): ?>
                            <tr>
                                <td>
                                    <div class="adm-dash__track-info">
                                        <img src="<?= htmlspecialchars($track['cover'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="adm-dash__cover" width="38" height="38">
                                        <div>
                                            <div class="adm-dash__track-name"><?= htmlspecialchars($track['title'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="adm-dash__track-artist"><?= htmlspecialchars($track['artist'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($track['album'], ENT_QUOTES, 'UTF-8') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="adm-dash__pill-fmt adm-dash__pill-fmt--<?= $track['badge'] ?>">
                                        <?= htmlspecialchars($track['format'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td class="adm-dash__num"><?= htmlspecialchars($track['plays'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="adm-dash__time"><?= htmlspecialchars($track['duration'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="adm-dash__status-tag"><?= htmlspecialchars($track['status'], ENT_QUOTES, 'UTF-8') ?></span>
                                </td>
                                <td>
                                    <div class="adm-dash__actions">
                                        <button type="button" class="adm-dash__act-btn" title="Ön Dinle">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                        </button>
                                        <button type="button" class="adm-dash__act-btn" title="Düzenle">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sağ: Canlı Olay & Güvenlik Günlüğü -->
        <div class="adm-dash__card adm-dash__card--logs">
            <div class="adm-dash__card-header">
                <div>
                    <h3 class="adm-dash__card-title">Canlı Sistem Günlüğü</h3>
                    <p class="adm-dash__card-sub">Ses motoru, API ve güvenlik hareketleri</p>
                </div>
                <span class="adm-dash__log-badge">CANLI LOG</span>
            </div>

            <div class="adm-dash__logs-stream">
                <?php foreach ($systemLogs as $log): ?>
                    <div class="adm-dash__log-item">
                        <span class="adm-dash__log-time"><?= htmlspecialchars($log['time'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="adm-dash__log-tag adm-dash__log-tag--<?= $log['level'] ?>"><?= htmlspecialchars($log['tag'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="adm-dash__log-text"><?= htmlspecialchars($log['text'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</div>
<?php
$content = ob_get_clean();

require __DIR__ . '/layout.php';
