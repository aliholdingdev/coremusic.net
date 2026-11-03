<?php declare(strict_types=1);
/**
 * pages/browse.php — Dosya Yöneticisi / Disk (Göz At) sayfası (v2.0.0 — Figma SSOT)
 * --------------------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-browse.css)
 * SSOT     : Figma node 2831:9555 (Linux 1024 - Göz At Page)
 *            + node 3155:18252 (Linux 1920 - Göz At Page)
 *            + node 2831:10282 / 1976:11757 (Linux 1024 - Göz At - Tıklama Clicked)
 *            + .ai/.png/home-1024/Linux  1024 - Göz At Page.png
 *            + .ai/.png/home-1024/Linux  1024 - Göz At - Tıklama Clicked.png
 * Route    : /browse, /gozat
 * --------------------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Class\ComponentLoader;
use CoreMusic\Home\Class\HomeLayoutVariant;
use CoreMusic\Home\Repository\MusicRepository;

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

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net:81';
$figmaBase = $assetsUrl . '/Image/figma-1920';
$diskIconsBase = $assetsUrl . '/Image/res-pink/disk';

/* --- CSP Nonce --- */
$cspNonce = (string)($_SESSION['csp_nonce'] ?? '');
$nonceAttr = $cspNonce !== '' ? ' nonce="' . $h($cspNonce) . '"' : '';


/* --- Saniye -> HH:MM:SS --- */
$formatDuration = static function (mixed $seconds): string {
    if (!is_numeric($seconds)) {
        return '00:03:30';
    }
    $s = max(0, (int)$seconds);
    return sprintf('%02d:%02d:%02d', intdiv($s, 3600), intdiv($s % 3600, 60), $s % 60);
};

$cleanTrackMeta = static function (string $rawTitle, string $rawArtist): array {
    $title = trim($rawTitle);
    $artist = trim($rawArtist);

    $title = preg_replace('/^(\\d+[\\s\\.\\-\\)]+)+/u', '', $title) ?? $title;
    $title = trim($title);

    if (preg_match('/^\\(\\s*\\d+\\s*\\)\\s*[-]?\\s*(.*)$/u', $artist, $m)) {
        $artist = trim($m[1]);
    }
    $artist = preg_replace('/^\\d+[\\s\\.\\-\\)]+/u', '', $artist) ?? $artist;
    $artist = trim($artist);

    if (str_contains($title, ' - ')) {
        $parts = explode(' - ', $title, 2);
        if (count($parts) === 2 && mb_strlen(trim($parts[0])) > 1 && mb_strlen(trim($parts[1])) > 1) {
            $lowerArt = mb_strtolower($artist);
            if ($artist === '' || str_contains($lowerArt, 'oryantel') || str_contains($lowerArt, 'repertuvar') || str_contains($lowerArt, 'turkce') || str_contains($lowerArt, 'slowturk') || str_contains($lowerArt, 'muzikler') || str_contains($lowerArt, 'akustik') || str_contains($lowerArt, 'roman')) {
                $artist = trim($parts[0]);
                $title = trim($parts[1]);
            }
        }
    }

    if (mb_strtoupper($title, 'UTF-8') === $title && mb_strlen($title) > 3) {
        $title = mb_convert_case(mb_strtolower($title, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }
    if (mb_strtoupper($artist, 'UTF-8') === $artist && mb_strlen($artist) > 3) {
        $artist = mb_convert_case(mb_strtolower($artist, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
    }

    return [$title, $artist];
};

$tracks = [];
try {
    if (defined('DB_HOST')) {
        $idx = 0;
        foreach (MusicRepository::fromEnvironment()->findRecentTracks() as $row) {
            $hex = strtolower((string)($row['music_hex'] ?? ''));
            if ($hex === '') {
                continue;
            }
            [$t, $a] = $cleanTrackMeta((string)$row['title'], (string)$row['artist_name']);
            $durSec = (int)($row['duration_sec'] ?? 0);
            if ($durSec <= 0) {
                $durSec = 195 + (($idx * 37) % 85);
            }
            $tracks[] = [
                't'      => $t,
                'album'  => (string)($row['album_name'] ?? 'Hayat Rüya Gibi'),
                'a'      => $a,
                'd'      => $formatDuration($durSec),
                'stream' => '/stream/' . $hex,
                'active' => $idx === 3,
            ];
            $idx++;
        }
    }
} catch (\Throwable) {
    $tracks = [];
}

if ($tracks === []) {
    $tracks = [
        ['t' => 'Göksel - Sevil Neşelen', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:03:05', 'stream' => '', 'active' => false],
        ['t' => 'Barış Manço - Dönence', 'album' => 'Sözüm Meclisten Dışarı', 'a' => 'Barış Manço', 'd' => '00:05:20', 'stream' => '', 'active' => false],
        ['t' => 'Müslüm Gürses - Affet', 'album' => 'Aşk Tesadüfleri Sever', 'a' => 'Müslüm Gürses', 'd' => '00:04:45', 'stream' => '', 'active' => false],
        ['t' => 'Göksel - Kabahat Seni Sevende', 'album' => 'Hayat Rüya Gibi', 'a' => 'Göksel', 'd' => '00:02:05', 'stream' => '', 'active' => true],
        ['t' => 'Barış Manço - Gülpembe', 'album' => 'Sözüm Meclisten Dışarı', 'a' => 'Barış Manço', 'd' => '00:04:01', 'stream' => '', 'active' => false],
        ['t' => 'Sezen Aksu - Geri Dön', 'album' => 'Sen Ağlama', 'a' => 'Sezen Aksu', 'd' => '00:04:22', 'stream' => '', 'active' => false],
    ];
}

/* --- Disk Verisi (Figma SSOT node 2831:9555 & 3155:18261) --- */
$diskSections = [
    [
        'id'    => 'system',
        'title' => 'Sistem Diskleri',
        'disks' => [
            [
                'id'        => 'system-disk',
                'name'      => 'System Disk',
                'icon'      => 'hard-disk-drive.png',
                'meta'      => 'Hard Disk . Dahili Disk',
                'total'     => '32 GB',
                'used'      => '16 GB',
                'free'      => '16 GB Kullanılabilir',
                'percent'   => 50,
                'type'      => 'system',
                'songs'     => '4.521',
                'albums'    => '100',
                'artists'   => '150',
                'videos'    => '50',
                'radios'    => '20',
            ],
            [
                'id'        => 'nas-drive',
                'name'      => 'NAS Drive',
                'icon'      => 'hard-disk-drive.png',
                'meta'      => 'Ağ Depolama . Harici',
                'total'     => '2.0 TB',
                'used'      => '840 GB',
                'free'      => '1.16 TB Kullanılabilir',
                'percent'   => 42,
                'type'      => 'nas',
                'songs'     => '18.420',
                'albums'    => '450',
                'artists'   => '320',
                'videos'    => '120',
                'radios'    => '45',
            ],
        ],
    ],
    [
        'id'    => 'internal',
        'title' => 'HDD & SSD Diskleri',
        'disks' => [
            [
                'id'        => 'hdd-drive',
                'name'      => 'HDD Drive',
                'icon'      => 'hard-disk-drive.png',
                'meta'      => 'Seagate IronWolf 4TB',
                'total'     => '1.0 TB',
                'used'      => '620 GB',
                'free'      => '380 GB Kullanılabilir',
                'percent'   => 62,
                'type'      => 'hdd',
                'songs'     => '8.950',
                'albums'    => '210',
                'artists'   => '180',
                'videos'    => '85',
                'radios'    => '15',
            ],
            [
                'id'        => 'ssd-nvme-2',
                'name'      => 'SSD Nvme 2 Drive',
                'icon'      => 'ssd-nvme-m2.png',
                'meta'      => 'Samsung 980 PRO M.2',
                'total'     => '512 GB',
                'used'      => '210 GB',
                'free'      => '302 GB Kullanılabilir',
                'percent'   => 41,
                'type'      => 'nvme',
                'songs'     => '5.200',
                'albums'    => '140',
                'artists'   => '160',
                'videos'    => '60',
                'radios'    => '25',
            ],
            [
                'id'        => 'ssd-drive',
                'name'      => 'SSD Drive',
                'icon'      => 'ssd-sata.png',
                'meta'      => 'Crucial MX500 SATA',
                'total'     => '500 GB',
                'used'      => '450 GB',
                'free'      => '50 GB Kullanılabilir',
                'percent'   => 90,
                'type'      => 'ssd',
                'songs'     => '7.110',
                'albums'    => '180',
                'artists'   => '190',
                'videos'    => '40',
                'radios'    => '30',
            ],
        ],
    ],
    [
        'id'    => 'removable',
        'title' => 'USB Diskleri',
        'disks' => [
            [
                'id'        => 'usb-drive-1',
                'name'      => 'USB Drive',
                'icon'      => 'usb.png',
                'meta'      => 'SanDisk Ultra 64GB (E:)',
                'total'     => '64 GB',
                'used'      => '32 GB',
                'free'      => '32 GB Kullanılabilir',
                'percent'   => 50,
                'type'      => 'usb',
                'songs'     => '1.240',
                'albums'    => '35',
                'artists'   => '60',
                'videos'    => '10',
                'radios'    => '5',
            ],
            [
                'id'        => 'usb-drive-2',
                'name'      => 'USB Drive',
                'icon'      => 'usb2.png',
                'meta'      => 'Kingston DataTraveler (F:)',
                'total'     => '128 GB',
                'used'      => '14 GB',
                'free'      => '114 GB Kullanılabilir',
                'percent'   => 11,
                'type'      => 'usb',
                'songs'     => '850',
                'albums'    => '20',
                'artists'   => '40',
                'videos'    => '5',
                'radios'    => '0',
            ],
            [
                'id'        => 'cd-dvd-drive',
                'name'      => 'CD DVD Drive',
                'icon'      => 'cd-dvd.png',
                'meta'      => 'Optik Sürücü (Ses CD / D:)',
                'total'     => '700 MB',
                'used'      => '700 MB',
                'free'      => '0 MB Kullanılabilir',
                'percent'   => 100,
                'type'      => 'cd',
                'songs'     => '18',
                'albums'    => '1',
                'artists'   => '1',
                'videos'    => '0',
                'radios'    => '0',
            ],
        ],
    ],
];

/* Varsayılan seçili disk (Figma SSOT: System Disk) */
$activeDisk = $diskSections[0]['disks'][0];
?>

<div class="page-browse <?= $is4k ? 'page-browse--4k' : ($isWide ? 'page-browse--wide' : 'page-browse--embedded') ?> page-layout <?= $h($dm->allClasses()) ?>"
     <?= $dm->dataAttributes() ?>>

    <!-- ════════════════════════════════════════════════════════════
         SOL CAM PANEL: Dosya Yöneticisi / Diskler
         Figma node 2831:9555 (1024) / node 3155:18261 (1920)
         ════════════════════════════════════════════════════════════ -->
    <section class="browse-glass" aria-label="Dosya Yöneticisi ve Diskler">

        <!-- Disk Listesi Görünümü -->
        <div class="browse-disk-view" id="browse-disk-view">
            <header class="browse-toolbar">
                <a class="browse-toolbar__btn" href="javascript:history.back()" aria-label="Geri" title="Geri">
                    <img src="<?= $h($figmaBase . '/back-arrow.png') ?>" width="40" height="40" alt="" aria-hidden="true">
                </a>
                <h1 class="browse-toolbar__title">Dosya Yöneticisi / Disk</h1>
                <?php if ($isWide): ?>
                    <button type="button" class="browse-action-btn browse-action-btn--primary" id="btn-browse-open" style="margin-left:auto;padding:6px 18px;border-radius:14px;background:var(--cm-gradient-primary);color:var(--cm-color-text);font-weight:600;font-size:13px;border:none;cursor:pointer;">
                        Göz At
                    </button>
                <?php endif; ?>
            </header>

            <div class="browse-content">
                <?php foreach ($diskSections as $sec): ?>
                    <div class="browse-disk-section">
                        <h2 class="browse-section-title"><?= $h($sec['title']) ?></h2>
                        <div class="browse-disk-grid">
                            <?php foreach ($sec['disks'] as $disk): ?>
                                <?php $isSelected = ($disk['id'] === $activeDisk['id']); ?>
                                <div class="browse-disk-card<?= $isSelected ? ' is-active' : '' ?>"
                                     tabindex="0"
                                     role="button"
                                     data-disk-id="<?= $h($disk['id']) ?>"
                                     data-disk-name="<?= $h($disk['name']) ?>"
                                     data-disk-meta="<?= $h($disk['meta']) ?>"
                                     data-disk-total="<?= $h($disk['total']) ?>"
                                     data-disk-free="<?= $h($disk['free']) ?>"
                                     data-disk-percent="<?= (int)$disk['percent'] ?>"
                                     data-disk-songs="<?= $h($disk['songs']) ?>"
                                     data-disk-albums="<?= $h($disk['albums']) ?>"
                                     data-disk-artists="<?= $h($disk['artists']) ?>"
                                     data-disk-videos="<?= $h($disk['videos']) ?>"
                                     data-disk-radios="<?= $h($disk['radios']) ?>"
                                     aria-selected="<?= $isSelected ? 'true' : 'false' ?>"
                                     aria-label="<?= $h($disk['name']) ?>: <?= $h($disk['used']) ?> dolu, toplam <?= $h($disk['total']) ?>">
                                    <div class="browse-disk-card__body">
                                        <div class="browse-disk-card__icon" aria-hidden="true">
                                            <img src="<?= $h($diskIconsBase . '/' . $disk['icon']) ?>" width="35" height="35" alt="" loading="lazy">
                                        </div>
                                        <div class="browse-disk-card__info">
                                            <div class="browse-disk-card__name"><?= $h($disk['name']) ?></div>
                                            <div class="browse-disk-card__progress">
                                                <div class="browse-disk-card__progress-fill" style="width: <?= (int)$disk['percent'] ?>%;"></div>
                                                <div class="browse-disk-card__progress-tick" style="left: <?= (int)$disk['percent'] ?>%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="browse-empty-state" id="browse-empty-state" style="display:none;padding:32px;text-align:center;color:var(--cm-color-text-secondary);" role="status">
                    <p>Bağlı depolama aygıtı bulunamadı.</p>
                </div>
            </div>
        </div>

        <!-- ════════════════════════════════════════════════════════════
             DOSYA YÖNETİCİSİ / KLASÖR GÖRÜNÜMÜ (CLICKED STATE)
             Figma node 2831:10282 "Linux 1024 - Göz At - Tıklama Clicked"
             ════════════════════════════════════════════════════════════ -->
        <div class="browse-explorer" id="browse-explorer-view" aria-label="Disk Dosya Yöneticisi">
            <div class="browse-explorer__pathbar">
                <div class="browse-explorer__nav-btns">
                    <button type="button" class="browse-explorer__btn browse-explorer__back-btn" id="explorer-back-btn" aria-label="Geri">
                        <img src="<?= $h($figmaBase . '/back-arrow.png') ?>" width="32" height="32" alt="" aria-hidden="true">
                    </button>
                    <button type="button" class="browse-explorer__btn browse-explorer__btn--fwd" aria-label="İleri">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                            <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
                        </svg>
                    </button>
                </div>
                <div class="browse-explorer__title-wrap">
                    <h2 class="browse-explorer__title">Dosya Yöneticisi / Musics : Root</h2>
                    <span class="browse-explorer__path-text" id="explorer-path-text">c:\users\Bayram Ali\Music</span>
                </div>
                <div class="browse-explorer__search">
                    <input type="search" id="explorer-search" class="browse-explorer__search-input" placeholder="Dosya Ara" aria-label="Dosya Ara">
                    <img class="browse-explorer__search-icon" src="<?= $h($figmaBase . '/search-playlist.png') ?>" width="14" height="14" alt="" aria-hidden="true">
                </div>
            </div>

            <div class="browse-explorer__body">
                <!-- Sol Klasör Ağacı -->
                <nav class="browse-explorer__tree" aria-label="Klasörler">
                    <div class="browse-explorer__folder is-active" data-folder="Pop" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Pop Şarkılar Ali</span>
                    </div>
                    <div class="browse-explorer__folder" data-folder="Rock" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Rock</span>
                    </div>
                    <div class="browse-explorer__folder" data-folder="Arabesk" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Arabesk</span>
                    </div>
                    <div class="browse-explorer__folder" data-folder="Nostalji" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Nostalji</span>
                    </div>
                    <div class="browse-explorer__folder" data-folder="Yabancı Pop" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Yabancı Pop</span>
                    </div>
                    <div class="browse-explorer__folder" data-folder="Klasik" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Klasik</span>
                    </div>
                    <div class="browse-explorer__folder" data-folder="Enstrümantal" tabindex="0">
                        <svg class="browse-explorer__folder-icon" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                        <span>Enstrümantal</span>
                    </div>
                </nav>

                <!-- Sağ Dosya Tablosu -->
                <div class="browse-explorer__files" role="table" aria-label="Müzik Dosyaları">
                    <div class="browse-explorer__header-row" role="row">
                        <span role="columnheader">Şarkı Adı</span>
                        <span role="columnheader">Album Adı</span>
                        <span role="columnheader">Sanatcı</span>
                        <span role="columnheader" style="text-align: right;">Süre</span>
                    </div>
                    <div class="browse-explorer__file-list" id="explorer-file-list">
                        <?php foreach ($tracks as $idx => $tr): ?>
                            <?php
                            $isActive = !empty($tr['active']);
                            $stream = (string)($tr['stream'] ?? '');
                            $streamAttr = preg_match('#^/stream/[0-9a-f]{32}$#i', $stream) === 1
                                ? ' data-stream="' . $h($stream) . '"'
                                : '';
                            ?>
                            <div class="browse-explorer__file-row<?= $isActive ? ' is-active' : '' ?>"
                                 role="row"
                                 tabindex="0"
                                 <?= $streamAttr ?>
                                 data-name="<?= $h((string)$tr['t']) ?>"
                                 data-artist="<?= $h((string)$tr['a']) ?>"
                                 data-album="<?= $h((string)$tr['album']) ?>"
                                 aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                                <span class="browse-explorer__file-title" role="cell">
                                    <img src="<?= $h($figmaBase . '/disc-playlist.png') ?>" width="18" height="14" alt="" class="browse-explorer__file-disc" aria-hidden="true">
                                    <?= $h((string)$tr['t']) ?>
                                </span>
                                <span class="browse-explorer__file-album" role="cell"><?= $h((string)$tr['album']) ?></span>
                                <span class="browse-explorer__file-artist" role="cell"><?= $h((string)$tr['a']) ?></span>
                                <span class="browse-explorer__file-duration" role="cell"><?= $h((string)$tr['d']) ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div class="explorer-empty-state" id="explorer-empty-state" role="status" style="display:none;padding:24px;text-align:center;color:var(--cm-color-text-secondary);">
                            <p>Arama kriterinize uygun dosya bulunamadı.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($isWide): ?>
        <?php
        $loader = new ComponentLoader();
        $loader->display('wide-sidebar', $variant);
        ?>
    <?php else: ?>
        <!-- ════════════════════════════════════════════════════════════
             SAĞ PANEL (Figma node 2831:9583 "Sağ Panel", 219×420)
             SSOT: Disk Bilgisi + İstatistikler + Aksiyon Butonları (1024 Embedded)
             ════════════════════════════════════════════════════════════ -->
        <aside class="browse-sidebar-panel page-sidebar--figma-wide" aria-label="Disk Detayları ve İşlemler">
            <!-- Üst Disk Bilgi Alanı -->
            <div class="browse-panel-header">
                <div class="browse-panel-disk-icon">
                    <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                        <path d="M2 20h20v-4H2v4zm2-3h2v2H4v-2zM2 4v4h20V4H2zm4 3H4V5h2v2zm-4 7h20v-4H2v4zm2-3h2v2H4v-2z"/>
                    </svg>
                </div>
                <div class="browse-panel-disk-titles">
                    <h3 class="browse-panel-diskname" id="panel-diskname"><?= $h($activeDisk['name']) ?></h3>
                    <span class="browse-panel-disktype" id="panel-disktype"><?= $h($activeDisk['meta']) ?></span>
                </div>
            </div>

            <div class="browse-panel-capacity">
                <div class="browse-panel-cap-row">
                    <span class="browse-panel-cap-total" id="panel-disktotal"><?= $h($activeDisk['total']) ?></span>
                    <span class="browse-panel-cap-percent" id="panel-diskpercent">%<?= (int)$activeDisk['percent'] ?></span>
                </div>
                <div class="browse-panel-progress">
                    <div class="browse-panel-progress-fill" id="panel-diskprogress" style="width: <?= (int)$activeDisk['percent'] ?>%;"></div>
                </div>
                <div class="browse-panel-cap-free" id="panel-diskfree"><?= $h($activeDisk['free']) ?></div>
            </div>

            <!-- 1px Separator Line -->
            <div class="browse-panel-divider"></div>

            <!-- 5 İstatistik Sütunu (Figma 2831:9626-9643) -->
            <div class="browse-panel-stats">
                <div class="browse-stat-col">
                    <svg class="browse-stat-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                    <span class="browse-stat-val" id="stat-songs"><?= $h($activeDisk['songs']) ?></span>
                    <span class="browse-stat-lbl">Şarkı</span>
                </div>
                <div class="browse-stat-sep"></div>
                <div class="browse-stat-col">
                    <svg class="browse-stat-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5zm0-5.5c-.55 0-1 .45-1 1s.45 1 1 1 1-.45 1-1-.45-1-1-1z"/></svg>
                    <span class="browse-stat-val" id="stat-albums"><?= $h($activeDisk['albums']) ?></span>
                    <span class="browse-stat-lbl">Album</span>
                </div>
                <div class="browse-stat-sep"></div>
                <div class="browse-stat-col">
                    <svg class="browse-stat-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    <span class="browse-stat-val" id="stat-artists"><?= $h($activeDisk['artists']) ?></span>
                    <span class="browse-stat-lbl">Sanatcı</span>
                </div>
                <div class="browse-stat-sep"></div>
                <div class="browse-stat-col">
                    <svg class="browse-stat-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M18 4l2 4h-3l-2-4h-2l2 4h-3l-2-4H8l2 4H7L5 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4h-4z"/></svg>
                    <span class="browse-stat-val" id="stat-videos"><?= $h($activeDisk['videos']) ?></span>
                    <span class="browse-stat-lbl">Video</span>
                </div>
                <div class="browse-stat-sep"></div>
                <div class="browse-stat-col">
                    <svg class="browse-stat-icon" viewBox="0 0 24 24" width="14" height="14" fill="currentColor"><path d="M3.24 6.15C2.51 6.43 2 7.17 2 8v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-.83-.51-1.57-1.24-1.85L12 2 3.24 6.15zM7 20c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm13-8h-8v-2h8v2zm0 4h-8v-2h8v2z"/></svg>
                    <span class="browse-stat-val" id="stat-radios"><?= $h($activeDisk['radios']) ?></span>
                    <span class="browse-stat-lbl">Radio</span>
                </div>
            </div>

            <!-- 1px Separator Line -->
            <div class="browse-panel-divider"></div>

            <!-- 5 Aksiyon Butonu (Figma 2831:9593-9607) -->
            <div class="browse-panel-actions">
                <button type="button" class="browse-action-btn browse-action-btn--primary" id="btn-browse-open">
                    Göz At
                </button>
                <button type="button" class="browse-action-btn" id="btn-play-all">
                    Bütün Şarkıları Çal
                </button>
                <button type="button" class="browse-action-btn" id="btn-browse-songs">
                    Şarkıları Göz At
                </button>
                <button type="button" class="browse-action-btn" id="btn-browse-artists">
                    Sanatcıları Göz At
                </button>
                <button type="button" class="browse-action-btn" id="btn-browse-videos">
                    Videoları Göz At
                </button>
            </div>

            <!-- 3 Nokta Butonları (Figma 2831:9608-9616) -->
            <div class="browse-panel-dots">
                <button type="button" class="browse-dot-btn" aria-label="Daha fazla seçenek 1">...</button>
                <button type="button" class="browse-dot-btn" aria-label="Daha fazla seçenek 2">...</button>
                <button type="button" class="browse-dot-btn" aria-label="Daha fazla seçenek 3">...</button>
            </div>
        </aside>
    <?php endif; ?>

</div>

<script<?= $nonceAttr ?>>
(function() {
    var cards = document.querySelectorAll('.browse-disk-card');
    var diskView = document.getElementById('browse-disk-view');
    var explorerView = document.getElementById('browse-explorer-view');
    var explorerBackBtn = document.getElementById('explorer-back-btn');
    var explorerPathText = document.getElementById('explorer-path-text');
    var explorerSearch = document.getElementById('explorer-search');
    var explorerFolders = document.querySelectorAll('.browse-explorer__folder');
    var explorerFileRows = document.querySelectorAll('.browse-explorer__file-row');
    var btnBrowseOpen = document.getElementById('btn-browse-open');
    var btnBrowseSongs = document.getElementById('btn-browse-songs');

    // Panel elements
    var panelName = document.getElementById('panel-diskname');
    var panelType = document.getElementById('panel-disktype');
    var panelTotal = document.getElementById('panel-disktotal');
    var panelPercent = document.getElementById('panel-diskpercent');
    var panelProgress = document.getElementById('panel-diskprogress');
    var panelFree = document.getElementById('panel-diskfree');
    var statSongs = document.getElementById('stat-songs');
    var statAlbums = document.getElementById('stat-albums');
    var statArtists = document.getElementById('stat-artists');
    var statVideos = document.getElementById('stat-videos');
    var statRadios = document.getElementById('stat-radios');

    var currentDisk = 'System Disk';
    var currentFolder = 'Pop Şarkılar Ali';

    function selectDisk(card) {
        cards.forEach(function(c) {
            c.classList.remove('is-active');
            c.setAttribute('aria-selected', 'false');
        });
        card.classList.add('is-active');
        card.setAttribute('aria-selected', 'true');

        currentDisk = card.getAttribute('data-disk-name') || 'System Disk';
        if (panelName) panelName.textContent = currentDisk;
        if (panelType) panelType.textContent = card.getAttribute('data-disk-meta') || '';
        if (panelTotal) panelTotal.textContent = card.getAttribute('data-disk-total') || '';
        var pct = card.getAttribute('data-disk-percent') || '50';
        if (panelPercent) panelPercent.textContent = '%' + pct;
        if (panelProgress) panelProgress.style.width = pct + '%';
        if (panelFree) panelFree.textContent = card.getAttribute('data-disk-free') || '';

        if (statSongs) statSongs.textContent = card.getAttribute('data-disk-songs') || '0';
        if (statAlbums) statAlbums.textContent = card.getAttribute('data-disk-albums') || '0';
        if (statArtists) statArtists.textContent = card.getAttribute('data-disk-artists') || '0';
        if (statVideos) statVideos.textContent = card.getAttribute('data-disk-videos') || '0';
        if (statRadios) statRadios.textContent = card.getAttribute('data-disk-radios') || '0';
    }

    function openExplorer() {
        if (explorerPathText) {
            explorerPathText.textContent = currentDisk + ':\\users\\Bayram Ali\\Music\\' + currentFolder;
        }
        if (diskView) diskView.style.display = 'none';
        if (explorerView) explorerView.classList.add('is-active');
    }

    function closeExplorer() {
        if (explorerView) explorerView.classList.remove('is-active');
        if (diskView) diskView.style.display = '';
    }

    cards.forEach(function(card) {
        card.addEventListener('click', function() {
            selectDisk(card);
        });
        card.addEventListener('dblclick', function() {
            selectDisk(card);
            openExplorer();
        });
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                selectDisk(card);
                openExplorer();
            }
        });
    });

    if (btnBrowseOpen) {
        btnBrowseOpen.addEventListener('click', openExplorer);
    }
    if (btnBrowseSongs) {
        btnBrowseSongs.addEventListener('click', openExplorer);
    }
    if (explorerBackBtn) {
        explorerBackBtn.addEventListener('click', closeExplorer);
    }

    // Folder select
    explorerFolders.forEach(function(fld) {
        fld.addEventListener('click', function() {
            explorerFolders.forEach(function(f) { f.classList.remove('is-active'); });
            this.classList.add('is-active');
            currentFolder = this.querySelector('span') ? this.querySelector('span').textContent.trim() : 'Müzikler';
            if (explorerPathText) {
                explorerPathText.textContent = currentDisk + ':\\users\\Bayram Ali\\Music\\' + currentFolder;
            }
        });
    });

    // Row click to play
    var fileList = document.getElementById('explorer-file-list');
    if (fileList) {
        fileList.addEventListener('click', function(e) {
            var row = e.target.closest('.browse-explorer__file-row');
            if (!row) return;
            fileList.querySelectorAll('.browse-explorer__file-row').forEach(function(r) {
                r.classList.remove('is-active');
                r.setAttribute('aria-selected', 'false');
            });
            row.classList.add('is-active');
            row.setAttribute('aria-selected', 'true');
            var stream = row.getAttribute('data-stream');
            if (stream && typeof window.playStreamUrl === 'function') {
                window.playStreamUrl(stream);
            }
        });
    }

    // File search filter
    if (explorerSearch) {
        explorerSearch.addEventListener('input', function() {
            var q = (this.value || '').trim().toLowerCase();
            explorerFileRows.forEach(function(row) {
                var title = (row.querySelector('.browse-explorer__file-title') ? row.querySelector('.browse-explorer__file-title').textContent : '').toLowerCase();
                var album = (row.querySelector('.browse-explorer__file-album') ? row.querySelector('.browse-explorer__file-album').textContent : '').toLowerCase();
                var artist = (row.querySelector('.browse-explorer__file-artist') ? row.querySelector('.browse-explorer__file-artist').textContent : '').toLowerCase();
                var match = !q || title.indexOf(q) !== -1 || album.indexOf(q) !== -1 || artist.indexOf(q) !== -1;
                row.style.display = match ? '' : 'none';
            });
        });
    }
})();
</script>
