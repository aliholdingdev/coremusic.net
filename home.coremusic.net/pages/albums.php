<?php declare(strict_types=1);
/**
 * pages/albums.php — Albümler Sayfası (v2.1.0 — Figma SSOT)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-albums.css)
 * SSOT     : .ai/.png/home-1024/Linux  1024 - Albumler Page.png
 *            .ai/ui-design/screens/T07-embedded/albums.md
 * Route    : /albumler (SpaRoute: 'albums')
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

/* --- Figma SSOT Tür Sekmeleri --- */
$tabs = ['Tümü', 'Pop', 'Arabesk', 'Dans', 'Oyun Havası', 'Damar', 'Org', 'Yabancı Pop', 'Kpop Kore'];
$activeTab = (string)($_GET['genre'] ?? 'Tümü');

/* --- Figma SSOT Albüm Kartları (1024x600 mockup içeriği birebir) --- */
$albums = [
    [
        'id'       => 1,
        'title'    => 'Nöbetçi Eczane Ferhat Kasetleri',
        'artist'   => 'Nöbetçi Eczane Ferhat',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cassette.png',
    ],
    [
        'id'       => 2,
        'title'    => 'Bergen - Tüm Şarkılar',
        'artist'   => 'Bergen',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_bergen.png',
    ],
    [
        'id'       => 3,
        'title'    => 'Cansever - Tüm Şarkılar',
        'artist'   => 'Cansever',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cansever.png',
    ],
    [
        'id'       => 4,
        'title'    => 'İbrahim Tatlıses - Tüm Şarkılar',
        'artist'   => 'İbrahim Tatlıses',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_tatlises.png',
    ],
    [
        'id'       => 5,
        'title'    => 'Hüseyin Kağıt - Tüm Şarkılar',
        'artist'   => 'Hüseyin Kağıt',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_folder.png',
    ],
    [
        'id'       => 6,
        'title'    => 'Linet - Tüm Şarkılar',
        'artist'   => 'Linet',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_linet.png',
    ],
    [
        'id'       => 7,
        'title'    => 'Cansever - Dünden Bugüne',
        'artist'   => 'Cansever',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cansever.png',
    ],
    [
        'id'       => 8,
        'title'    => 'Bergen - Acıların Kadını',
        'artist'   => 'Bergen',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cassette.png',
    ],
    [
        'id'       => 9,
        'title'    => 'Cansever - Tüm Şarkılar',
        'artist'   => 'Cansever',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cansever.png',
    ],
    [
        'id'       => 10,
        'title'    => 'Bergen - Tüm Şarkılar',
        'artist'   => 'Bergen',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_bergen.png',
    ],
    [
        'id'       => 11,
        'title'    => 'Cansever - Özel Seri',
        'artist'   => 'Cansever',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cansever.png',
    ],
    [
        'id'       => 12,
        'title'    => 'Bergen - Klasikler',
        'artist'   => 'Bergen',
        'genre'    => 'Arabesk',
        'year'     => 'Bilinmeyen Yıl',
        'listens'  => 5,
        'duration' => '00:10:05',
        'cover'    => $figmaExtracted . '/album_cassette.png',
    ],
];

/* Dikey scrollbar ve zengin ızgara için: 4K'da 144 albüm (18 satır x 8 kolon), Wide'da 90 albüm (15 satır x 6 kolon), Embedded'da 60 albüm (15 satır x 4 kolon) */
$targetCount = $is4k ? 144 : ($isWide ? 90 : 60);
if (count($albums) < $targetCount && !empty($albums)) {
    $orig = $albums;
    $cnt = count($orig);
    for ($i = $cnt; $i < $targetCount; $i++) {
        $c = $orig[$i % $cnt];
        $c['id'] = $i + 1;
        $albums[] = $c;
    }
}

?>

<div class="page-albums <?= $isWide ? ($is4k ? 'page-albums--4k page-albums--wide' : 'page-albums--wide') : 'page-albums--embedded' ?> page-layout albums-layout <?= $h($dm->allClasses()) ?>"
     <?= $dm->dataAttributes() ?>>

    <!-- SOL ANA BUZLU CAM PANEL (Figma SSOT 743px) -->
    <section class="albums-main" aria-label="Albümler">

        <!-- ÜST BAŞLIK ALANI -->
        <header class="albums-header">
            <!-- 1. Satır: Geri Butonu + Breadcrumb -->
            <div class="albums-header__top">
                <a href="/home" class="albums-back-btn" aria-label="Geri Dön" title="Geri">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </a>
                <span class="albums-breadcrumb">Albümler / Tüm Albümler</span>
            </div>

            <!-- 2. Satır: Başlık & Arama / Filtre -->
            <div class="albums-header__title-row">
                <div>
                    <h1 class="albums-title">Albümler</h1>
                    <p class="albums-subtitle">Kütüphanedeki &amp; Sistemdeki Bütün Albümler</p>
                </div>
                <div class="albums-search-container">
                    <div class="albums-search-wrap" role="search">
                        <input type="search"
                               id="albums-search-input"
                               placeholder="Sanatçı Adı Ara"
                               aria-label="Sanatçı Adı Ara"
                               class="albums-search-input"
                               autocomplete="off">
                        <button type="button" class="albums-search-btn" aria-label="Ara">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="albums-filter-btn" aria-label="Filtrele" title="Filtrele">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                            <rect x="5" y="4" width="2.5" height="16" rx="1.25"/>
                            <rect x="11" y="4" width="2.5" height="16" rx="1.25"/>
                            <rect x="17" y="4" width="2.5" height="16" rx="1.25"/>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- TÜRLER / ÇİPLER (Figma SSOT 9 tür) -->
        <nav class="albums-tabs" aria-label="Müzik Türleri">
            <?php foreach ($tabs as $tab): ?>
                <button type="button"
                        class="albums-tabs__tab <?= ($tab === $activeTab) ? 'is-active' : '' ?>"
                        data-genre="<?= $h($tab) ?>"
                        aria-pressed="<?= ($tab === $activeTab) ? 'true' : 'false' ?>">
                    <?= $h($tab) ?>
                </button>
            <?php endforeach; ?>
        </nav>

        <!-- YATAY ALBÜM KARTLARI IZGARASI (4 Kolon) -->
        <div class="albums-grid" role="region" aria-label="Albüm Listesi">
            <?php foreach ($albums as $album): ?>
                <a href="/albumler/detay?id=<?= (int)$album['id'] ?>"
                   class="album-card"
                   tabindex="0"
                   data-title="<?= $h($album['title']) ?>"
                   data-artist="<?= $h($album['artist']) ?>"
                   data-genre="<?= $h($album['genre']) ?>"
                   aria-label="<?= $h($album['title']) ?> - <?= $h($album['artist']) ?>">
                    <div class="album-card__thumb">
                        <img src="<?= $h($album['cover']) ?>"
                             alt="<?= $h($album['title']) ?>"
                             width="52"
                             height="54"
                             loading="lazy">
                    </div>
                    <div class="album-card__info">
                        <div class="album-card__title" title="<?= $h($album['title']) ?>">
                            <?= $h($album['title']) ?>
                        </div>
                        <div class="album-card__artist">
                            <?= $h($album['artist']) ?>
                        </div>
                        <div class="album-card__meta">
                            <span>Tür : <?= $h($album['genre']) ?></span>
                            <span>Yıl : <?= $h($album['year']) ?></span>
                            <span>Dinleme Sayısı : <?= (int)$album['listens'] ?></span>
                        </div>
                    </div>
                    <span class="album-card__duration"><?= $h($album['duration']) ?></span>
                </a>
            <?php endforeach; ?>
            <div class="albums-empty-state" id="albums-empty-state" style="display: none;" role="status">
                Aradığınız kritere uygun albüm bulunamadı.
            </div>
        </div>

    </section>

    <?php if ($isWide): ?>
        <?php $loader->display('wide-sidebar', $variant); ?>
    <?php else: ?>
    <!-- SAĞ KENAR ÇUBUĞU (Figma SSOT Albüm Detay Paneli 1024) -->
    <aside class="albums-sidebar" aria-label="Seçilen Albüm Detayı">
        <div class="albums-sidebar__art">
            <img src="<?= $h($figmaExtracted . '/sidebar_sibel.png') ?>" alt="Albüm Kapağı" width="100" height="100" loading="lazy">
        </div>
        <h2 class="albums-sidebar__title">Nöbetçi Eczane Ferhat Kasetleri</h2>
        <p class="albums-sidebar__subtitle">Kaset</p>

        <div class="albums-sidebar__actions">
            <button type="button" class="albums-sidebar__btn-play">Hemen Çal</button>
            <div class="albums-sidebar__btn-row">
                <button type="button" class="albums-sidebar__btn-glass">Karışık Çal</button>
                <button type="button" class="albums-sidebar__btn-glass" style="flex: 0 0 36px;">•••</button>
            </div>
        </div>

        <div class="albums-sidebar__meta-grid">
            <div class="albums-sidebar__meta-col">
                <span>Kalite : 24 Bit / 48 kHz</span>
                <span>İndirme Sayısı : 2</span>
                <span>Tür : Arabesk</span>
                <span>Dinleme Sayısı : 5</span>
            </div>
            <div class="albums-sidebar__meta-col">
                <span>Boyut : 2 GB</span>
                <span>Parça Sayısı : 12</span>
                <span>Yıl : Bilinmeyen Yıl</span>
                <span>Süre : 00:00:00</span>
            </div>
        </div>
    </aside>
    <?php endif; ?>

</div>

<script<?= $nonceAttr ?>>
(function() {
    var searchInput = document.getElementById('albums-search-input');
    var tabs = document.querySelectorAll('.albums-tabs__tab');
    var cards = document.querySelectorAll('.album-card');
    var emptyState = document.getElementById('albums-empty-state');
    var currentGenre = 'Tümü';

    function filterAlbums() {
        var q = searchInput ? (searchInput.value || '').trim().toLowerCase() : '';
        var visibleCount = 0;

        cards.forEach(function(card) {
            var title = (card.getAttribute('data-title') || '').toLowerCase();
            var artist = (card.getAttribute('data-artist') || '').toLowerCase();
            var genre = card.getAttribute('data-genre') || '';

            var matchSearch = !q || title.indexOf(q) !== -1 || artist.indexOf(q) !== -1;
            var matchGenre = (currentGenre === 'Tümü') || (genre === currentGenre);

            var isVisible = matchSearch && matchGenre;
            card.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        if (emptyState) {
            emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterAlbums);
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) {
                t.classList.remove('is-active');
                t.setAttribute('aria-pressed', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-pressed', 'true');
            currentGenre = tab.getAttribute('data-genre') || 'Tümü';
            filterAlbums();
        });
    });
})();
</script>
