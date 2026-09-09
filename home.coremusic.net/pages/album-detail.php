<?php declare(strict_types=1);
/**
 * pages/album-detail.php — Albüm Detayı
 * Layer: L3 Presentation · 05_Pages (p-album-detail.css)
 * SSOT: .ai/.png/home-1024/Linux  1024 - Albumler Details Detay Page.png
 *       + .ai/ui-design/screens/C-music/album-detail.md (v3.0.0)
 * Scope: track list (4 kolon) + detay panel (iskelet; veri $album/$tracks ile
 *        API entegrasyonunda doldurulur). .track-row home ile paylaşılır;
 *        çakışma önlemi için seçiciler .album-detail-layout köküne bağlıdır.
 * A11y: tek <h1> (albüm adı), satırlar button değil role="row" pattern beklenir
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
$album  = $album ?? null;
$tracks = $tracks ?? [];

require __DIR__ . '/../header.php';
?>

<main class="page-album-detail album-detail-layout <?= $dm->allClasses() ?>" role="main" aria-label="Albüm Detayı" <?= $dm->dataAttributes() ?>>

    <section class="track-list" aria-label="Parça listesi">
        <div class="track-list__header" role="row">
            <span>#</span>
            <span>Parça</span>
            <span>Süre</span>
            <span>Puan</span>
        </div>
<?php $i = 0; foreach ($tracks as $track): $i++; ?>
        <div class="track-row" role="row" tabindex="0">
            <span class="track-row__number"><?= $i ?></span>
            <span class="track-row__thumb"><!-- kapak img — API --></span>
            <span class="track-row__title"><?= htmlspecialchars((string)($track['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="track-row__duration"><?= htmlspecialchars((string)($track['duration'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="track-row__rating" role="img" aria-label="Puan"></span>
        </div>
<?php endforeach; ?>
    </section>

    <aside class="album-detail-panel" aria-label="Albüm bilgisi">
<?php if ($album !== null): ?>
        <div class="album-detail-panel__art"></div>
        <h1 class="album-detail-panel__title"><?= htmlspecialchars((string)($album['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
        <p class="album-detail-panel__artist"><?= htmlspecialchars((string)($album['artist'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
    </aside>

</main>

<?php require __DIR__ . '/../footer.php'; ?>
