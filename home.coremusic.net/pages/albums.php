<?php declare(strict_types=1);
/**
 * pages/albums.php — Albümler
 * Layer: L3 Presentation · 05_Pages (p-albums.css)
 * SSOT: .ai/.png/home-1024/Linux  1024 - Albumler Page.png
 *       + .ai/ui-design/screens/C-music/albums.md (v3.0.0)
 * Scope: Standard 60/40 — tür sekmeleri + kart grid + detay panel (iskelet;
 *        veri $albums/$genres/$detail değişkenleriyle API entegrasyonunda doldurulur)
 * A11y: tek <h1>, sekmeler native button, detay aside aria-label
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
$genres = $genres ?? [];
$albums = $albums ?? [];
$detail = $detail ?? null;

require __DIR__ . '/../header.php';
?>

<main class="page-albums albums-layout <?= $dm->allClasses() ?>" role="main" aria-label="Albümler" <?= $dm->dataAttributes() ?>>

    <section aria-label="Albüm listesi">
        <header class="albums-header">
            <a class="albums-header__back" href="/" aria-label="Geri dön">
                <!-- PNG: geri ok ikonu — assets Image/res-pink/actions -->
            </a>
            <h1 class="albums-header__title">Albümler</h1>
            <div class="albums-header__search" role="search">
                <!-- PNG: arama ikonu + alan — API entegrasyonunda form'a dönüşür -->
            </div>
        </header>

        <nav class="albums-tabs" aria-label="Türler">
<?php foreach ($genres as $genre): ?>
            <button type="button" class="albums-tabs__tab"><?= htmlspecialchars((string)$genre, ENT_QUOTES, 'UTF-8') ?></button>
<?php endforeach; ?>
        </nav>

        <div class="albums-grid">
<?php foreach ($albums as $album): ?>
            <!-- C09 media-card yapısı — API verisiyle doldurulur -->
<?php endforeach; ?>
        </div>
    </section>

    <aside class="albums-detail" aria-label="Albüm detayı">
<?php if ($detail !== null): ?>
        <div class="albums-detail__art"></div>
        <h2 class="albums-detail__title"><?= htmlspecialchars((string)($detail['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="albums-detail__artist"><?= htmlspecialchars((string)($detail['artist'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>
    </aside>

</main>

<?php require __DIR__ . '/../footer.php'; ?>
