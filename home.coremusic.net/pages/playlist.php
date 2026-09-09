<?php declare(strict_types=1);
/**
 * pages/playlist.php — Playlist
 * Layer: L3 Presentation · 05_Pages (p-playlist.css)
 * SSOT: .ai/.png/home-1024/Linux  1024 - Playlist Page.png
 *       + .ai/ui-design/screens/D-player/playlist.md (v3.0.0)
 * Scope: 6 kolonlu track tablosu + detay panel (iskelet; veri $playlist/$tracks
 *        ile API entegrasyonunda doldurulur)
 * A11y: tek <h1> (playlist adı), satırlar tabindex=0, aksiyonlar aria-label
 * Version: 1.0.0 — 2026-09-09
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

/* Veri sözleşmesi (API entegrasyonu bekleniyor) */
$playlist = $playlist ?? null;
$tracks   = $tracks ?? [];

require __DIR__ . '/../header.php';
?>

<main class="page-playlist playlist-layout <?= $dm->allClasses() ?>" role="main" aria-label="Playlist" <?= $dm->dataAttributes() ?>>

    <div class="playlist-table" role="table" aria-label="Parçalar">
        <div class="playlist-table__header" role="row">
            <span>#</span>
            <span>Parça</span>
            <span>Albüm</span>
            <span>Sanatçı</span>
            <span>Süre</span>
            <span>Puan</span>
        </div>
<?php $i = 0; foreach ($tracks as $track): $i++; ?>
        <div class="playlist-row" role="row" tabindex="0">
            <span class="playlist-row__number"><?= $i ?></span>
            <span class="playlist-row__thumb"></span>
            <span class="playlist-row__title"><?= htmlspecialchars((string)($track['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="playlist-row__album"><?= htmlspecialchars((string)($track['album'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="playlist-row__artist"><?= htmlspecialchars((string)($track['artist'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="playlist-row__duration"><?= htmlspecialchars((string)($track['duration'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="playlist-row__rating" role="img" aria-label="Puan"></span>
        </div>
<?php endforeach; ?>
    </div>

    <aside class="playlist-detail" aria-label="Playlist bilgisi">
<?php if ($playlist !== null): ?>
        <div class="playlist-detail__artist-photo"></div>
        <h1 class="playlist-detail__title"><?= htmlspecialchars((string)($playlist['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="playlist-detail__artist"><?= htmlspecialchars((string)($playlist['artist'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="playlist-detail__actions">
            <button type="button" class="playlist-detail__action" aria-label="Çal"></button>
            <button type="button" class="playlist-detail__action" aria-label="Karıştır"></button>
        </div>
<?php endif; ?>
    </aside>

</main>

<?php require __DIR__ . '/../footer.php'; ?>
