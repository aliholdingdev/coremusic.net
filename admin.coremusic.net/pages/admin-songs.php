<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Şarkı Listesi & Yönetimi (Light Theme)
 *
 * Yer: admin.coremusic.net/pages/admin-songs.php
 * Tasarım: Açık Tema (Light Theme) Glassmorphism
 */

$pageTitle = 'Şarkılar';
$activeNav = 'admin-songs';

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';

// Örnek katalog listesi (güvenli fallback veri seti)
$songs = [
    [
        'id'       => 101,
        'title'    => 'Baksana Talihe',
        'artist'   => 'Göksel',
        'album'    => 'Baksana Talihe (Single)',
        'cover'    => $assetsUrl . '/Image/res-pink/album-goksel.png',
        'format'   => 'FLAC 24-bit / 96 kHz',
        'badge'    => 'flac',
        'plays'    => '482.190',
        'duration' => '04:12',
        'status'   => 'Yayında',
        'date'     => '18.10.2026',
    ],
    [
        'id'       => 102,
        'title'    => 'Bazen Unutmak İstersin',
        'artist'   => 'Kürşat Başar',
        'album'    => 'Kaldığımız Yerden',
        'cover'    => $assetsUrl . '/Image/res-pink/album-kursat.png',
        'format'   => 'MP3 320 kbps CBR',
        'badge'    => 'mp3',
        'plays'    => '291.400',
        'duration' => '03:45',
        'status'   => 'Yayında',
        'date'     => '16.10.2026',
    ],
    [
        'id'       => 103,
        'title'    => 'Golden Gate Sunset',
        'artist'   => 'CoreMusic Ensemble',
        'album'    => 'Audiophile Master Tape Vol. 1',
        'cover'    => $assetsUrl . '/Image/res-pink/default-album.png',
        'format'   => 'FLAC 32-bit Float / 192 kHz',
        'badge'    => 'master',
        'plays'    => '145.820',
        'duration' => '06:10',
        'status'   => 'Yayında',
        'date'     => '14.10.2026',
    ],
    [
        'id'       => 104,
        'title'    => 'Sen Ağlama',
        'artist'   => 'Sezen Aksu',
        'album'    => 'Sen Ağlama (Remastered)',
        'cover'    => $assetsUrl . '/Image/res-pink/default-album.png',
        'format'   => 'FLAC 24-bit / 96 kHz',
        'badge'    => 'flac',
        'plays'    => '814.500',
        'duration' => '05:28',
        'status'   => 'Yayında',
        'date'     => '10.10.2026',
    ],
    [
        'id'       => 105,
        'title'    => 'İstanbul Hatırası',
        'artist'   => 'Müzeyyen Senar',
        'album'    => 'Klasikler Vol. 2',
        'cover'    => $assetsUrl . '/Image/res-pink/default-album.png',
        'format'   => 'WAV 24-bit / 88.2 kHz',
        'badge'    => 'master',
        'plays'    => '320.150',
        'duration' => '04:50',
        'status'   => 'Yayında',
        'date'     => '05.10.2026',
    ],
];

ob_start();
?>
<div class="adm-songs-page" data-mode="light">
    <!-- Üst Kontrol & Filtre Çubuğu -->
    <div class="adm-crud-toolbar">
        <div class="adm-crud-toolbar__search">
            <span class="adm-crud-toolbar__icon" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </span>
            <input type="text" class="adm-crud-toolbar__input" placeholder="Şarkı adı, sanatçı veya albüm ara..." aria-label="Şarkı ara">
        </div>

        <div class="adm-crud-toolbar__actions">
            <select class="adm-crud-toolbar__select" aria-label="Format filtresi">
                <option value="all">Tüm Formatlar</option>
                <option value="flac">FLAC Hi-Res</option>
                <option value="master">Studio Master</option>
                <option value="mp3">MP3 320k</option>
            </select>

            <a href="/admin-song-add" class="adm-btn adm-btn--sm adm-btn--primary" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                <span>Yeni Şarkı Ekle</span>
            </a>
        </div>
    </div>

    <!-- Şarkı Listesi Tablosu (Açık Cam Kart) -->
    <div class="adm-table-card adm-table-card--light">
        <table class="adm-table" aria-label="Şarkı listesi">
            <thead>
                <tr>
                    <th scope="col" style="width: 44px;">#</th>
                    <th scope="col">Parça & Albüm</th>
                    <th scope="col">Sanatçı</th>
                    <th scope="col">Format</th>
                    <th scope="col">Dinlenme</th>
                    <th scope="col">Süre</th>
                    <th scope="col">Durum</th>
                    <th scope="col" style="text-align: right;">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($songs as $idx => $s): ?>
                <tr>
                    <td class="adm-table__cell-index"><?= $idx + 1 ?></td>
                    <td class="adm-table__cell-song">
                        <img class="adm-table__cover" src="<?= $h($s['cover']) ?>" alt="" width="40" height="40" loading="lazy">
                        <div class="adm-table__song-info">
                            <strong class="adm-table__title"><?= $h($s['title']) ?></strong>
                            <span class="adm-table__album"><?= $h($s['album']) ?></span>
                        </div>
                    </td>
                    <td class="adm-table__cell-artist"><?= $h($s['artist']) ?></td>
                    <td>
                        <span class="adm-badge adm-badge--<?= $h($s['badge']) ?>">
                            <?= $h($s['format']) ?>
                        </span>
                    </td>
                    <td class="adm-table__cell-plays"><?= $h($s['plays']) ?></td>
                    <td class="adm-table__cell-duration"><?= $h($s['duration']) ?></td>
                    <td>
                        <span class="adm-status-pill adm-status-pill--active">
                            <span class="adm-status-pill__dot"></span>
                            <?= $h($s['status']) ?>
                        </span>
                    </td>
                    <td class="adm-table__cell-actions">
                        <button type="button" class="adm-action-btn" title="Çal / Önizle" aria-label="Çal">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        </button>
                        <button type="button" class="adm-action-btn" title="Düzenle" aria-label="Düzenle">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                        </button>
                        <button type="button" class="adm-action-btn adm-action-btn--danger" title="Sil" aria-label="Sil">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Sayfalama -->
        <div class="adm-table-footer">
            <span class="adm-table-footer__info">Toplam 5 parça gösteriliyor (Sayfa 1 / 1)</span>
            <div class="adm-pagination">
                <button type="button" class="adm-pagination__btn" disabled>&laquo;</button>
                <button type="button" class="adm-pagination__btn adm-pagination__btn--active">1</button>
                <button type="button" class="adm-pagination__btn" disabled>&raquo;</button>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
