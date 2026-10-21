<?php declare(strict_types=1);

/**
 * CoreMusic Admin — Çalma Listesi (Playlist) Yönetimi (Light Theme)
 *
 * Yer: admin.coremusic.net/pages/admin-playlist.php
 * Tasarım: Açık Tema (Light Theme) Glassmorphism
 */

$pageTitle = 'Playlist Yönetimi';
$activeNav = 'admin-playlist';

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : '';
$csrf      = (string)($_SESSION['csrf_token'] ?? '');

$playlists = [
    [
        'id'       => 1,
        'title'    => 'Akustik & Chill Vakitler',
        'desc'     => 'Huzurlu akşamlar için özenle seçilmiş soft akustik parçalar.',
        'cover'    => $assetsUrl . '/Image/res-pink/album-goksel.png',
        'tracks'   => 24,
        'duration' => '1 sa 32 dk',
        'status'   => 'Genel Liste',
    ],
    [
        'id'       => 2,
        'title'    => 'Audiophile Master Tapes',
        'desc'     => 'Direct Stream 24-bit 96kHz stüdyo miksleri ve amfi test parçaları.',
        'cover'    => $assetsUrl . '/Image/res-pink/default-album.png',
        'tracks'   => 18,
        'duration' => '1 sa 45 dk',
        'status'   => 'Öne Çıkan',
    ],
    [
        'id'       => 3,
        'title'    => 'Türkçe 90lar Nostalji',
        'desc'     => '90\'ların unutulmaz efsane Türkçe pop ve nostalji klasikleri.',
        'cover'    => $assetsUrl . '/Image/res-pink/album-kursat.png',
        'tracks'   => 35,
        'duration' => '2 sa 15 dk',
        'status'   => 'Genel Liste',
    ],
];

$selectedPlaylistTracks = [
    ['id' => 1, 'order' => 1, 'title' => 'Baksana Talihe', 'artist' => 'Göksel', 'duration' => '04:12'],
    ['id' => 2, 'order' => 2, 'title' => 'Bazen Unutmak İstersin', 'artist' => 'Kürşat Başar', 'duration' => '03:45'],
    ['id' => 3, 'order' => 3, 'title' => 'Golden Gate Sunset', 'artist' => 'CoreMusic Ensemble', 'duration' => '06:10'],
    ['id' => 4, 'order' => 4, 'title' => 'Sen Ağlama', 'artist' => 'Sezen Aksu', 'duration' => '05:28'],
];

ob_start();
?>
<div class="adm-playlist-page" data-mode="light">
    <!-- Üst Kontrol Çubuğu -->
    <div class="adm-crud-toolbar">
        <div>
            <h2 class="adm-panel__subheading" style="margin:0;font-size:18px;font-weight:700;">Çalma Listesi Kataloğu</h2>
            <small style="color:rgba(26,16,37,0.6);">Kullanıcılara sunulan ve sistem tarafından önerilen playlistleri yönetin</small>
        </div>

        <button type="button" class="adm-btn adm-btn--sm adm-btn--primary" id="btn-create-playlist">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            <span>Yeni Playlist Oluştur</span>
        </button>
    </div>

    <!-- İki Kolonlu Yönetim Alanı -->
    <div class="adm-playlist-grid">
        <!-- Sol: Playlist Kartları -->
        <div class="adm-playlist-list">
            <?php foreach ($playlists as $idx => $pl): ?>
            <div class="adm-card adm-card--light adm-pl-card <?= $idx === 0 ? 'adm-pl-card--active' : '' ?>">
                <img src="<?= $h($pl['cover']) ?>" alt="" width="56" height="56" class="adm-pl-card__cover">
                <div class="adm-pl-card__info">
                    <strong class="adm-pl-card__title"><?= $h($pl['title']) ?></strong>
                    <p class="adm-pl-card__desc"><?= $h($pl['desc']) ?></p>
                    <div class="adm-pl-card__meta">
                        <span><?= $pl['tracks'] ?> Parça</span>
                        <span class="dot"></span>
                        <span><?= $h($pl['duration']) ?></span>
                        <span class="dot"></span>
                        <span class="adm-badge adm-badge--sm"><?= $h($pl['status']) ?></span>
                    </div>
                </div>
                <div class="adm-pl-card__actions">
                    <button type="button" class="adm-action-btn" title="Düzenle">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </button>
                    <button type="button" class="adm-action-btn adm-action-btn--danger" title="Sil">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Sağ: Seçili Liste Parça Düzeni -->
        <div class="adm-card adm-card--light adm-pl-detail">
            <div class="adm-card__header">
                <div>
                    <h3 class="adm-card__title" style="font-size:17px;">Akustik & Chill Vakitler — Parça Sıralaması</h3>
                    <small style="color:rgba(26,16,37,0.6);">Parça sırasını değiştirebilir veya yeni şarkı ekleyebilirsiniz</small>
                </div>
                <button type="button" class="adm-btn adm-btn--sm adm-btn--secondary">
                    + Şarkı Ekle
                </button>
            </div>

            <div class="adm-pl-track-list">
                <?php foreach ($selectedPlaylistTracks as $t): ?>
                <div class="adm-pl-track-row">
                    <span class="adm-pl-track-row__handle" title="Sırayı değiştirmek için sürükleyin">&#9776;</span>
                    <span class="adm-pl-track-row__order"><?= $t['order'] ?></span>
                    <div class="adm-pl-track-row__details">
                        <strong><?= $h($t['title']) ?></strong>
                        <small><?= $h($t['artist']) ?></small>
                    </div>
                    <span class="adm-pl-track-row__duration"><?= $h($t['duration']) ?></span>
                    <button type="button" class="adm-action-btn adm-action-btn--danger" title="Listeden Çıkar">
                        &times;
                    </button>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="adm-pl-detail__footer">
                <button type="button" class="adm-btn adm-btn--primary">
                    Sıralamayı Kaydet
                </button>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
