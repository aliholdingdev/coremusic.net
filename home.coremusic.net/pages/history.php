<?php declare(strict_types=1);
/**
 * pages/history.php — Geçmiş (Dinleme Geçmişi) sayfası (v1.0.0)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-history.css)
 * SSOT     : PNG .ai/ui-design/reference/figma/png/1339-23464-Laptop-1920---Ge-mi-Page.png
 *            + .ai/.png/home-1024/Linux  1024 - Home Page.png (Geçmiş render'ı)
 * Veri     : MusicRepository::findRecentTracks() — MEVCUT sorgu (yeni backend yok);
 *            DB yoksa PNG demo seed (RecentTracksComponent defaultTracks ile aynı)
 * Sütunlar : Şarkı Adı · Sanatçı · Süre
 *            ⚠️ PNG'deki "Album" sütunu veri kaynağında YOK → backend phase'e bırakıldı
 * A11y     : search label (aria-label), breadcrumb nav, touch ≥ var(--touch-min)
 * JS       : cm-history (HistoryComponent) — arama filtresi + geri/ileri butonları
 * Oynatma  : footer.init.js a[data-stream] kontratı (mini-card ile aynı)
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Class\ComponentLoader;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Repository\MusicRepository;

/* --- DeviceManager (home.php ile aynı kalıp — viewport test fallback) --- */
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

/* --- Layout kararları --- */
$is4k        = $dm->shouldRender4kLayout();
$isWide      = $dm->shouldRenderWideLayout() || $is4k;
$variant     = HomeLayoutVariant::fromFlags($isWide, $is4k);
$loader      = new ComponentLoader();

/* --- Yerel yardımcılar (home.php kalıbı) --- */
$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

/* --- Saniye → HH:MM:SS (RecentTracksComponent::formatDuration ile aynı davranış) --- */
$formatDuration = static function (mixed $seconds): string {
    if (!is_numeric($seconds)) {
        return '00:00:00';
    }
    $s = max(0, (int)$seconds);
    return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
};

/* --- Veri: mevcut repository (yeni sorgu YOK) → DB yoksa PNG demo seed --- */
$tracks = [];
try {
    if (defined('DB_HOST')) {
        foreach (MusicRepository::fromEnvironment()->findRecentTracks() as $row) {
            $hex = strtolower((string)($row['music_hex'] ?? ''));
            if ($hex === '') {
                continue;
            }
            $tracks[] = [
                't'      => (string)$row['title'],
                'a'      => (string)$row['artist_name'],
                'd'      => $formatDuration($row['duration_sec'] ?? null),
                'stream' => '/stream/' . $hex,
            ];
        }
    }
} catch (\Throwable) {
    $tracks = [];
}

if ($tracks === []) {
    /* PNG home-1024 node 1639:9904 demo içeriği (RecentTracksComponent ile birebir) */
    $tracks = [
        ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Göksel - Kabahat Seni Se...', 'a' => 'Göksel', 'd' => '00:02:05', 'stream' => ''],
        ['t' => 'Barış Manco - Gulpembe', 'a' => 'Barış Manco', 'd' => '00:04:01', 'stream' => ''],
        ['t' => 'Kış Masalı Ensturmental', 'a' => 'Org Dersleri', 'd' => '00:01:10', 'stream' => ''],
        ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => ''],
        ['t' => 'Barış Manco - Gulpembe', 'a' => 'Barış Manco', 'd' => '00:04:01', 'stream' => ''],
    ];
}
?>
<div class="page-history page-layout <?= $h($dm->allClasses()) ?>"
     <?= $dm->dataAttributes() ?>
     data-cm-component="cm-history">

    <!-- ÜST BAR — PNG: geri/ileri + "Geçmiş" breadcrumb + "Geçmiş'te ara" -->
    <div class="history-toolbar">
        <div class="history-toolbar__left">
            <button type="button" class="history-toolbar__btn" data-action="history-back" aria-label="Geri">
                <svg class="history-toolbar__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M15.41 7.41 14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/>
                </svg>
            </button>
            <button type="button" class="history-toolbar__btn" data-action="history-forward" aria-label="İleri">
                <svg class="history-toolbar__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M8.59 16.59 10 18l6-6-6-6-1.41 1.41L13.17 12z" fill="currentColor"/>
                </svg>
            </button>
            <nav class="history-toolbar__crumb" aria-label="Sayfa yolu">
                <h1 class="history-toolbar__title">Geçmiş</h1>
            </nav>
        </div>
        <div class="history-toolbar__search">
            <input id="history-search"
                   class="history-toolbar__input"
                   type="search"
                   placeholder="Geçmiş'te ara"
                   aria-label="Geçmiş'te ara"
                   autocomplete="off"
                   spellcheck="false">
        </div>
    </div>

    <!-- GÖVDE: liste + Player-Info sidebar (PNG: sağ panel) -->
    <div class="page-history__body">

        <section class="history-list" aria-label="Çalma geçmişi">
            <div class="history-list__head" aria-hidden="true">
                <span class="history-list__col history-list__col--play"></span>
                <span class="history-list__col">Şarkı Adı</span>
                <span class="history-list__col">Sanatçı</span>
                <span class="history-list__col history-list__col--end">Süre</span>
            </div>

            <ul class="history-list__items">
                <?php foreach ($tracks as $track): ?>
                    <?php
                    /* Oynatma bağı: yalnız geçerli /stream/{32hex} data-stream olur (HomeSongButton ile aynı kapı) */
                    $stream = (string)$track['stream'];
                    $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                        ? ' data-stream="' . $h($stream) . '"'
                        : '';
                    $label = 'Oynat: ' . $track['t'] . ' — ' . $track['a'];
                    ?>
                    <li class="history-list__item">
                        <a class="history-row"
                           href="/playlist"
                           data-no-spa
                           <?= $streamAttr ?>
                           aria-label="<?= $h($label) ?>">
                            <span class="history-row__play" aria-hidden="true">
                                <svg class="history-row__icon" viewBox="0 0 24 24" focusable="false">
                                    <path d="M8 5v14l11-7z" fill="currentColor"/>
                                </svg>
                            </span>
                            <span class="history-row__title"><?= $h((string)$track['t']) ?></span>
                            <span class="history-row__artist"><?= $h((string)$track['a']) ?></span>
                            <span class="history-row__duration"><?= $h((string)$track['d']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <p class="history-list__empty" hidden>Aramanızla eşleşen kayıt bulunamadı.</p>
        </section>

        <aside class="page-history__sidebar" aria-label="Şu an çalan">
            <?php $loader->display('player-info', $variant); ?>
        </aside>

    </div>
</div>
