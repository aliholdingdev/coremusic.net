<?php declare(strict_types=1);
/**
 * pages/home.php — Ana Sayfa (v5.0.0 — Component System v1.0)
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (_home-layout.css)
 * SSOT     : Figma API extraction (node-id=1639-10160 / 2831-13747)
 *            + .ai/.png/home-1024/ (12 PNG) + .ai/.png/home-1920/ (1 PNG)
 * A11y     : role="main", aria-label, contrast ≥4.5:1
 * Scroll   : YALNIZCA .home-layout container'ında — header/footer fixed
 * ------------------------------------------------------------------
 *
 * Bileşen Mimarisi v4 (Component System v1.0):
 *   Shared: CoreMusic\Component\* (ComponentRegistry, ComponentRenderer)
 *   Home:   CoreMusic\Home\Component\* (PlayerInfo, RecentTracks)
 *   JS:     ComponentLoader + data-cm-component auto-mount
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Class\ComponentLoader;
use CoreMusic\Home\Class\HomeLayoutVariant;

/* --- DeviceManager (viewport cookie fallback) --- */
if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

/* --- Layout Kararları --- */
$is4k        = $dm->shouldRender4kLayout();
$isWide      = $dm->shouldRenderWideLayout() || $is4k;
$layoutClass = $is4k
    ? 'home-layout--4k'
    : ($isWide ? 'home-layout--wide' : 'home-layout--embedded');
$topClass = $is4k
    ? 'home-layout__top--4k'
    : 'home-layout__top--wide';
$variant = HomeLayoutVariant::fromFlags($isWide, $is4k);

/* --- Bileşen Yükleyici (mevcut — geriye dönük uyumlu) --- */
$loader = new ComponentLoader();

/* --- Header --- */
require __DIR__ . '/../header.php';
?>

<main class="page-home page-layout <?= $layoutClass ?> <?= $dm->allClasses() ?>"
      role="main"
      aria-label="Ana Sayfa"
      <?= $dm->dataAttributes() ?>>

<?php if ($isWide): ?>
    <!-- ════════════════════════════════════════════════════════════
         WIDE / 4K — Figma: node-id=2831-13747 (1920×1080)
         Layout: 3-sütun üst + tam genişlik kart satırları alt
         ════════════════════════════════════════════════════════════ -->

    <!-- ÜST SATIR: Now Playing | Hoş Geldin Banner | Widgets -->
    <div class="home-layout__top <?= $topClass ?>">

        <!-- Sol:Player Info (469×184, cover 150×150) -->
        <div class="home-layout__top-left--wide">
            <?php $loader->display('player-info', $variant); ?>
        </div>

    </div>

    <!-- ALT SATIR: tam genişlik kart bölümleri -->
    <div class="home-cards-wrapper--wide">
        <?php $loader->display('recent-tracks', $variant); ?>
    </div>

<?php else: ?>
    <!-- ════════════════════════════════════════════════════════════
         EMBEDDED 1024×600 — Figma: node-id=1639-10160
         Layout: Split 42/58 üst + 3 kolon alt
         ════════════════════════════════════════════════════════════ -->

    <!-- ÜST SATIR: Now Playing (sol %42) + Widgets (sağ %58) -->
    <div class="home-layout__top home-layout__top--embedded">

        <!-- Sol %42: Now Playing (392×131, cover 72×72) -->
        <div class="home-layout__top-left">
            <?php $loader->display('player-info', $variant); ?>
        </div>

    </div>

    <!-- ALT SATIR: 3 kolon — En Son | Playlister | Sıradaki -->
    <div class="home-layout__bottom home-layout__bottom--embedded">
    </div>

<?php endif; ?>

</main>

<!-- Welcome Modal JS — ilk girişte açılır, localStorage ile kontrol -->
<?php if (!$dm->isPhone()): ?>
<script nonce="<?= $h((string)($_SESSION['csp_nonce'] ?? '')) ?>">
(function() {
    var overlay = document.getElementById('welcomeModalOverlay');
    var btn = overlay ? overlay.querySelector('.welcome-modal__btn') : null;

    // İlk giriş kontrolü — localStorage kullan
    if (overlay && !localStorage.getItem('cm_welcome_seen')) {
        overlay.classList.remove('is-hidden');
    }

    // Kapatma fonksiyonu
    function closeModal() {
        if (overlay) {
            overlay.classList.add('is-hidden');
            localStorage.setItem('cm_welcome_seen', '1');
        }
    }

    // Başla butonu
    if (btn) btn.addEventListener('click', closeModal);

    // Escape tuşu
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
})();
</script>
<?php endif; ?>

<?php require __DIR__ . '/../footer.php'; ?>
