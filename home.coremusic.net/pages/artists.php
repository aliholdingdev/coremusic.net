<?php declare(strict_types=1);
/**
 * pages/artists.php — Sanatçılar
 * Layer: L3 Presentation · 05_Pages (p-artists.css)
 * SSOT: .ai/.png/home-1024/Linux  1024 - Singer Page.png
 *       + .ai/ui-design/screens/C-music/artists.md (v3.0.0)
 * Scope: dairesel sanatçı kart grid (5 kolon) + detay panel (iskelet;
 *        veri $artists/$genres/$detail ile API entegrasyonunda doldurulur)
 * A11y: tek <h1>, kartlar focus-visible, detay aside aria-label
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
$genres  = $genres ?? [];
$artists = $artists ?? [];
$detail  = $detail ?? null;

require __DIR__ . '/../header.php';
?>

<main class="page-artists artists-layout <?= $dm->allClasses() ?>" role="main" aria-label="Sanatçılar" <?= $dm->dataAttributes() ?>>

    <section aria-label="Sanatçı listesi">
        <header aria-label="Sayfa başlığı">
            <h1>Sanatçılar</h1>
            <div class="artists-header__search" role="search">
                <!-- PNG: arama ikonu + alan — API entegrasyonunda form'a dönüşür -->
            </div>
        </header>

        <nav class="artists-tabs" aria-label="Türler">
<?php foreach ($genres as $genre): ?>
            <button type="button" class="artists-tabs__tab"><?= htmlspecialchars((string)$genre, ENT_QUOTES, 'UTF-8') ?></button>
<?php endforeach; ?>
        </nav>

        <div class="artists-grid">
<?php foreach ($artists as $artist): ?>
            <article class="artist-card" tabindex="0">
                <div class="artist-card__thumb"></div>
                <div class="artist-card__info">
                    <span class="artist-card__name"><?= htmlspecialchars((string)($artist['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="artist-card__genre"><?= htmlspecialchars((string)($artist['genre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="artist-card__count"><?= htmlspecialchars((string)($artist['count'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </article>
<?php endforeach; ?>
        </div>
    </section>

    <aside class="artists-detail" aria-label="Sanatçı detayı">
<?php if ($detail !== null): ?>
        <div class="artists-detail__photo"></div>
        <h2 class="artists-detail__name"><?= htmlspecialchars((string)($detail['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="artists-detail__genre"><?= htmlspecialchars((string)($detail['genre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="artists-detail__stats">
            <!-- PNG: albüm/parça sayacı — API verisi -->
        </div>
<?php endif; ?>
    </aside>

</main>

<?php require __DIR__ . '/../footer.php'; ?>
