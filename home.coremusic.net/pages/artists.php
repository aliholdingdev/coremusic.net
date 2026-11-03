<?php declare(strict_types=1);
/**
 * pages/artists.php — Sanatçılar Sayfası (v2.1.0 — Figma SSOT)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-artists.css)
 * SSOT     : .ai/.png/home-1024/Linux  1024 - Singer Page.png
 *            .ai/ui-design/screens/T07-embedded/singer.md
 * Route    : /sanatcilar (SpaRoute: 'artists')
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

/* --- Figma SSOT Sanatçı Kartları (1024x600 Singer Page birebir) --- */
$artists = [
    [
        'id'    => 1,
        'name'  => 'Sibel Can',
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_sibel.png',
    ],
    [
        'id'    => 2,
        'name'  => "Dilso'z",
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_dilsoz.png',
    ],
    [
        'id'    => 3,
        'name'  => 'Ankaralı Ayşe',
        'genre' => 'Oyun Havası',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_ayse.png',
    ],
    [
        'id'    => 4,
        'name'  => 'Ankaralı Yasemin',
        'genre' => 'Oyun Havası',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_yasemin.png',
    ],
    [
        'id'    => 5,
        'name'  => 'Bergen',
        'genre' => 'Arabesk',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_bergen.png',
    ],
    [
        'id'    => 6,
        'name'  => 'Sibel Can',
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_sibel.png',
    ],
    [
        'id'    => 7,
        'name'  => 'Sibel Can',
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_sibel.png',
    ],
    [
        'id'    => 8,
        'name'  => 'Sibel Can',
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_sibel.png',
    ],
    [
        'id'    => 9,
        'name'  => 'Sibel Can',
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_sibel.png',
    ],
    [
        'id'    => 10,
        'name'  => 'Sibel Can',
        'genre' => 'Türkçe Pop',
        'songs' => '48 Şarkı',
        'photo' => $figmaExtracted . '/singer_sibel.png',
    ],
];

/* Dikey scrollbar ve zengin ızgara için en az 60 sanatçı (Figma SSOT 96 sanatçı) */
if (count($artists) < 60 && !empty($artists)) {
    $orig = $artists;
    $cnt = count($orig);
    for ($i = $cnt; $i < 60; $i++) {
        $c = $orig[$i % $cnt];
        $c['id'] = $i + 1;
        $artists[] = $c;
    }
}
?>

<div class="page-artists <?= $isWide ? ($is4k ? 'page-artists--4k page-artists--wide' : 'page-artists--wide') : 'page-artists--embedded' ?> page-layout artists-layout <?= $h($dm->allClasses()) ?>" <?= $dm->dataAttributes() ?>>

    <!-- SOL ANA BUZLU CAM PANEL (Figma SSOT 743px) -->
    <section class="artists-main" aria-label="Sanatçılar">

        <!-- ÜST BAŞLIK ALANI -->
        <header class="artists-header">
            <!-- 1. Satır: Geri Butonu + Breadcrumb -->
            <div class="artists-header__top">
                <a href="/home" class="artists-back-btn" aria-label="Geri Dön" title="Geri">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </a>
                <span class="artists-breadcrumb">Sanatçılar / Tüm  Sanatçılar</span>
            </div>

            <!-- 2. Satır: Başlık & Arama / Filtre -->
            <div class="artists-header__title-row">
                <div>
                    <h1 class="artists-title">Sanatçılar</h1>
                    <p class="artists-subtitle">Kütüphanedeki &amp; Sistemdeki Bütün Sanatçılar</p>
                </div>
                <div class="artists-search-container">
                    <div class="artists-search-wrap" role="search">
                        <input type="search"
                               placeholder="Sanatçı Adı Ara"
                               aria-label="Sanatçı Adı Ara"
                               class="artists-search-input">
                        <button type="button" class="artists-search-btn" aria-label="Ara">
                            <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor">
                                <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/>
                            </svg>
                        </button>
                    </div>
                    <button type="button" class="artists-filter-btn" aria-label="Filtrele" title="Filtrele">
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
        <nav class="artists-tabs" aria-label="Müzik Türleri">
            <?php foreach ($tabs as $tab): ?>
                <button type="button"
                        class="artists-tabs__tab <?= ($tab === $activeTab) ? 'is-active' : '' ?>"
                        aria-pressed="<?= ($tab === $activeTab) ? 'true' : 'false' ?>">
                    <?= $h($tab) ?>
                </button>
            <?php endforeach; ?>
        </nav>

        <!-- DİKEY SANATÇI KARTLARI IZGARASI (5 Kolon) -->
        <div class="artists-grid" role="region" aria-label="Sanatçı Listesi">
            <?php foreach ($artists as $artist): ?>
                <div class="artist-card"
                     tabindex="0"
                     role="button"
                     aria-label="<?= $h($artist['name']) ?> - <?= $h($artist['genre']) ?>"
                     data-id="<?= (int)$artist['id'] ?>"
                     data-name="<?= $h($artist['name']) ?>"
                     data-genre="<?= $h($artist['genre']) ?>"
                     data-songs="<?= $h($artist['songs']) ?>"
                     data-photo="<?= $h($artist['photo']) ?>">
                    <div class="artist-card__thumb">
                        <img src="<?= $h($artist['photo']) ?>"
                             alt="<?= $h($artist['name']) ?>"
                             width="60"
                             height="60"
                             loading="lazy">
                    </div>
                    <div class="artist-card__name">
                        <?= $h($artist['name']) ?>
                    </div>
                    <div class="artist-card__genre">
                        <?= $h($artist['genre']) ?>
                    </div>
                    <div class="artist-card__count">
                        <?= $h($artist['songs']) ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="artists-empty-state" id="artists-empty-state" style="display: none;" role="status">
                Aradığınız kriterlere uygun sanatçı bulunamadı.
            </div>
        </div>

    </section>

    <?php if ($isWide): ?>
        <?php $loader->display('wide-sidebar', $variant); ?>
    <?php else: ?>
    <!-- SAĞ KENAR ÇUBUĞU (Figma SSOT Sanatçı Detay Paneli 1024) -->
    <aside class="artists-sidebar" aria-label="Seçilen Sanatçı Detayı">
        <div class="artists-sidebar__art">
            <img src="<?= $h($figmaExtracted . '/sidebar_sibel.png') ?>" alt="Sanatçı Görseli" width="100" height="100" loading="lazy">
        </div>
        <h2 class="artists-sidebar__name">Sibel Can</h2>
        <p class="artists-sidebar__genre">Türkçe Pop</p>

        <!-- 3 İstatistik Rozeti (Figma SSOT) -->
        <div class="artists-sidebar__badges">
            <span class="artists-sidebar__badge">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" style="color: var(--cm-primary);">
                    <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                </svg>
                <span id="artist-sidebar-songs">48</span>
            </span>
            <span class="artists-sidebar__badge">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" style="color: var(--cm-primary);">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"/>
                </svg>
                8
            </span>
            <span class="artists-sidebar__badge">
                <svg viewBox="0 0 24 24" width="12" height="12" fill="currentColor" style="color: var(--cm-primary);">
                    <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/>
                </svg>
                1986
            </span>
        </div>

        <!-- Biyografi Metni (Figma SSOT) -->
        <p class="artists-sidebar__bio">
            Sibel Can, doğum adıyla Sibel Cangüre, Türk şarkıcı ve oyuncu. Kariyerine dansöz olarak başlayan Can, daha sonra şarkıcılık yapmaya başladı ve günümüzde televizyon ve sinema oyunculuğu ile sunuculuk da yapmaktadır.
        </p>

        <!-- Eylemler -->
        <div class="artists-sidebar__actions">
            <button type="button" class="artists-sidebar__btn-play">Hemen Çal</button>
            <div class="artists-sidebar__btn-row">
                <button type="button" class="artists-sidebar__btn-glass">Karışık Çal</button>
                <button type="button" class="artists-sidebar__btn-glass" style="flex: 0 0 36px;">•••</button>
            </div>
        </div>
    </aside>
    <?php endif; ?>

    <script<?= $nonceAttr ?>>
    (function() {
        const searchInput = document.querySelector('.artists-search-input');
        const emptyState = document.getElementById('artists-empty-state');
        const cards = document.querySelectorAll('.artist-card');
        const tabs = document.querySelectorAll('.artists-tabs__tab');
        const sidebarArt = document.querySelector('.artists-sidebar__art img');
        const sidebarName = document.querySelector('.artists-sidebar__name');
        const sidebarGenre = document.querySelector('.artists-sidebar__genre');
        let currentGenre = 'Tümü';

        function filterArtists() {
            const query = (searchInput?.value || '').trim().toLowerCase();
            let visibleCount = 0;

            cards.forEach(card => {
                const name = (card.dataset.name || '').toLowerCase();
                const genre = (card.dataset.genre || '').toLowerCase();
                const matchesGenre = (currentGenre === 'Tümü') || genre.includes(currentGenre.toLowerCase());
                const matchesQuery = !query || name.includes(query) || genre.includes(query);

                if (matchesGenre && matchesQuery) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                tabs.forEach(t => {
                    t.classList.remove('is-active');
                    t.setAttribute('aria-pressed', 'false');
                });
                this.classList.add('is-active');
                this.setAttribute('aria-pressed', 'true');
                currentGenre = this.textContent.trim();
                filterArtists();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', filterArtists);
        }

        cards.forEach(card => {
            card.addEventListener('click', function() {
                cards.forEach(c => c.classList.remove('is-active'));
                this.classList.add('is-active');

                const name = this.dataset.name;
                const genre = this.dataset.genre;
                const photo = this.dataset.photo;

                if (sidebarName && name) sidebarName.textContent = name;
                if (sidebarGenre && genre) sidebarGenre.textContent = genre;
                if (sidebarArt && photo) sidebarArt.src = photo;
            });
        });
    })();
    </script>

</div>
