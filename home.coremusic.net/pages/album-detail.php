<?php declare(strict_types=1);
/**
 * pages/album-detail.php — Albüm Detay Sayfası (v2.0.0 — Figma SSOT)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-album-detail.css)
 * SSOT     : .ai/.png/home-1024/Linux  1024 - Albumler Details Detay Page.png
 *            .ai/ui-design/screens/T07-embedded/album-detail.md
 * Route    : /albumler/detay (SpaRoute: 'album-detail')
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Class\ComponentLoader;
use CoreMusic\Home\Class\HomeLayoutVariant;

/* --- DeviceManager --- */
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

$is4k    = $dm->is4k();
$isWide  = $dm->shouldRenderWideLayout() || $is4k;
$variant = HomeLayoutVariant::fromFlags($isWide, $is4k);
$loader  = new ComponentLoader();

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
$figmaExtracted = $assetsUrl . '/Image/figma-extracted';
$resPink = $assetsUrl . '/Image/res-pink';

/* --- CSP Nonce --- */
$cspNonce = (string)($_SESSION['csp_nonce'] ?? '');
$nonceAttr = $cspNonce !== '' ? ' nonce="' . $h($cspNonce) . '"' : '';

$albumId = (int)($_GET['id'] ?? 1);

/* --- Figma SSOT Albüm Bilgisi --- */
$albumInfo = [
    'title'    => 'Hayat Rüya Gibi',
    'artist'   => 'Göksel',
    'rating'   => 5,
    'bitrate'  => '350 Kbps',
    'meta'     => '2024 . 12 Şarkı . 00:30:00 . Pop',
    'cover'    => $figmaExtracted . '/cd_case_linet.png',
];

/* --- Figma SSOT 7 Şarkı Listesi --- */
$tracks = [
    ['num' => 1, 'title' => 'Göksel - Sevil Neşelen', 'duration' => '00:02:59', 'stars' => 5],
    ['num' => 2, 'title' => 'Göksel - Kabahat Seninle', 'duration' => '00:00:00', 'stars' => 5],
    ['num' => 3, 'title' => 'Göksel - Sevil Neşelen', 'duration' => '00:00:00', 'stars' => 5],
    ['num' => 4, 'title' => 'Göksel - Sevil Neşelen', 'duration' => '00:00:00', 'stars' => 5],
    ['num' => 5, 'title' => 'Göksel - Sevil Neşelen', 'duration' => '00:00:00', 'stars' => 5],
    ['num' => 6, 'title' => 'Göksel - Sevil Neşelen', 'duration' => '00:00:00', 'stars' => 5],
    ['num' => 7, 'title' => 'Göksel - Sevil Neşelen', 'duration' => '00:00:00', 'stars' => 5],
];

/* Dikey scrollbar için en az 20 şarkı */
if (count($tracks) < 20 && !empty($tracks)) {
    $orig = $tracks;
    $cnt = count($orig);
    for ($i = $cnt; $i < 20; $i++) {
        $c = $orig[$i % $cnt];
        $c['num'] = $i + 1;
        $tracks[] = $c;
    }
}
?>

<div class="page-album-detail <?= $is4k ? 'page-album-detail--4k' : ($isWide ? 'page-album-detail--wide' : 'page-album-detail--embedded') ?> page-layout album-detail-layout <?= $h($dm->allClasses()) ?>"
     <?= $dm->dataAttributes() ?>>

    <!-- SOL ANA BUZLU CAM PANEL (Figma SSOT 743px) -->
    <section class="album-detail-main" aria-label="Albüm Detayları">

        <!-- ÜST BAŞLIK ALANI -->
        <header class="album-detail-header">
            <!-- Geri Butonu + Breadcrumb -->
            <div class="album-detail-header__top">
                <a href="/albumler" class="album-detail-back-btn" aria-label="Albümlere Dön" title="Geri">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </a>
                <span class="album-detail-breadcrumb">Albüm Detayları</span>
            </div>

            <!-- Arama Alanı -->
            <div class="album-detail-search-wrap" role="search">
                <input type="search"
                       id="album-detail-search"
                       placeholder="Şarkı Ara"
                       aria-label="Şarkı Ara"
                       class="album-detail-search-input"
                       autocomplete="off">
                <button type="button" class="album-detail-search-btn" aria-label="Ara">
                    <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                        <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                    </svg>
                </button>
            </div>
        </header>

        <!-- TABLO BAŞLIK ÇUBUĞU (Figma SSOT) -->
        <div class="album-detail-table-bar">
            <span class="album-detail-table-bar__album">Albüm : Göksel : Hayat Rüya Gibi &nbsp;/&nbsp; Şarkı Adı</span>
            <span class="album-detail-table-bar__duration">Süre</span>
            <span class="album-detail-table-bar__stars">Favori Yıldızı</span>
        </div>

        <!-- İKİ KOLONLU İÇERİK: SOL GENEL BAKIŞ + SAĞ ŞARKI LİSTESİ -->
        <div class="album-detail-content">

            <!-- SOL ÖZET KARTI -->
            <div class="album-detail-card">
                <div class="album-detail-card__art">
                    <img src="<?= $h($albumInfo['cover']) ?>" alt="<?= $h($albumInfo['title']) ?>" width="130" height="120" loading="lazy">
                </div>
                <h2 class="album-detail-card__title"><?= $h($albumInfo['title']) ?></h2>
                <div class="album-detail-card__artist-row">
                    <span class="album-detail-card__artist"><?= $h($albumInfo['artist']) ?></span>
                    <span class="album-detail-card__stars">★★★★★</span>
                </div>
                <div class="album-detail-card__bitrate">
                    <?= $h($albumInfo['bitrate']) ?>
                    <span class="album-detail-card__badge-mp3">MP3</span>
                </div>
                <div class="album-detail-card__meta">
                    <?= $h($albumInfo['meta']) ?>
                </div>
            </div>

            <!-- SAĞ ŞARKI LİSTESİ -->
            <div class="album-detail-tracks" role="table" aria-label="Albüm Şarkı Listesi">
                <?php foreach ($tracks as $track): ?>
                    <div class="album-track-row" role="row" tabindex="0" data-title="<?= $h($track['title']) ?>">
                        <div class="album-track-row__note">
                            <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="color: var(--cm-primary);">
                                <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                            </svg>
                        </div>
                        <span class="album-track-row__title" role="cell">
                            <?= $h($track['title']) ?>
                        </span>
                        <span class="album-track-row__duration" role="cell">
                            <?= $h($track['duration']) ?>
                        </span>
                        <span class="album-track-row__stars" role="cell" aria-label="5 yıldız">
                            ★★★★★
                        </span>
                    </div>
                <?php endforeach; ?>
                <div class="album-tracks-empty-state" id="album-tracks-empty" style="display: none;" role="status">
                    Aradığınız şarkı bu albümde bulunamadı.
                </div>
            </div>

        </div>

    </section>

    <?php if ($isWide): ?>
        <?php $loader->display('wide-sidebar', $variant); ?>
    <?php else: ?>
    <!-- SAĞ KENAR ÇUBUĞU (Figma SSOT Albüm Detay Paneli 1024) -->
    <aside class="album-detail-sidebar" aria-label="Seçilen Albüm Detayı">
        <div class="album-detail-sidebar__art">
            <img src="<?= $h($figmaExtracted . '/sidebar_sibel.png') ?>" alt="Albüm Kapağı" width="100" height="100" loading="lazy">
        </div>
        <h2 class="album-detail-sidebar__title">Hayat Rüya Gibi</h2>
        <p class="album-detail-sidebar__subtitle">Göksel</p>

        <div class="album-detail-sidebar__actions">
            <button type="button" class="album-detail-sidebar__btn-play">Hemen Çal</button>
            <div class="album-detail-sidebar__btn-row">
                <button type="button" class="album-detail-sidebar__btn-glass">Karışık Çal</button>
                <button type="button" class="album-detail-sidebar__btn-glass" style="flex: 0 0 36px;">•••</button>
            </div>
        </div>

        <div class="album-detail-sidebar__meta-grid">
            <div class="album-detail-sidebar__meta-col">
                <span>Kalite : 24 Bit / 48 kHz</span>
                <span>İndirme Sayısı : 2</span>
                <span>Tür : Arabesk</span>
                <span>Dinleme Sayısı : 10</span>
            </div>
            <div class="album-detail-sidebar__meta-col">
                <span>Boyut : 3 GB</span>
                <span>Parça Sayısı : 8</span>
                <span>Yıl : Bilinmeyen Yıl</span>
                <span>Süre : 00:00:00</span>
            </div>
        </div>
    </aside>
    <?php endif; ?>

</div>

<script<?= $nonceAttr ?>>
(function() {
    var searchInput = document.getElementById('album-detail-search');
    var rows = document.querySelectorAll('.album-track-row');
    var emptyState = document.getElementById('album-tracks-empty');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = (this.value || '').trim().toLowerCase();
            var visibleCount = 0;
            rows.forEach(function(row) {
                var title = (row.getAttribute('data-title') || '').toLowerCase();
                var match = !q || title.indexOf(q) !== -1;
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            if (emptyState) {
                emptyState.style.display = (visibleCount === 0 && q.length > 0) ? 'block' : 'none';
            }
        });
    }

    rows.forEach(function(row) {
        row.addEventListener('click', function() {
            rows.forEach(function(r) { r.classList.remove('is-active'); });
            row.classList.add('is-active');
        });
        row.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                rows.forEach(function(r) { r.classList.remove('is-active'); });
                row.classList.add('is-active');
            }
        });
    });
})();
</script>
