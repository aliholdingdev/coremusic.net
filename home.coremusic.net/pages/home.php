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
// Test mode: ?_test_resp=1024|1920|3840 parameters override viewport
$testViewportW = null;
if (isset($_GET['_test_resp']) && preg_match('/^(1024|1440|1920|3840)$/', $_GET['_test_resp'])) {
    $testViewportW = (int)$_GET['_test_resp'];
}

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => $testViewportW ?: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
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

/* --- Yerel yardımcılar (C-F-13: header/footer artık shell'de; $h/$assetsUrl
       önce header.php tanımlıyordu — sayfa kendi ihtiyaçını kendi karşılar) --- */
$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};
$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';
?>

<!-- DEBUG: Responsive Test Mode 1024/1920/3840px -->
<?php if (isset($_GET['_test_resp'])): ?>
<div style="position: fixed; top: 0; left: 0; right: 0; background: linear-gradient(90deg, #f00 0%, #ff1493 100%); color: #fff; padding: 12px 15px; font-size: 11px; z-index: 10000; text-align: center; font-weight: bold; font-family: monospace;">
    🧪 TEST MODE: <strong><?= htmlspecialchars($dm->shouldRender4kLayout() ? '4K (3840px)' : ($dm->shouldRenderWideLayout() ? 'WIDE (1920px)' : 'EMBEDDED (1024px)')) ?></strong> 
    | Variant: <strong><?= htmlspecialchars($variant->isWide() ? 'wide' : 'embedded') ?></strong> 
    | Player: <strong><?= $variant->isWide() ? '150×150 cover' : '72×72 cover' ?></strong>
    | 📍 <a href="?" style="color: #fff; text-decoration: underline;">Exit Test</a>
</div>
<div class="page-home page-layout home-layout <?= $layoutClass ?> <?= $dm->allClasses() ?>"
      <?= $dm->dataAttributes() ?>
      style="margin-top: 40px;">
<?php else: ?>
<div class="page-home page-layout home-layout <?= $layoutClass ?> <?= $dm->allClasses() ?>"
      <?= $dm->dataAttributes() ?>>
<?php endif; ?>

<?php if ($isWide): ?>
    <!-- ════════════════════════════════════════════════════════════
         WIDE / 4K — Figma: node-id=2831-13747 (1920×1080)
         Layout: 3-sütun üst + tam genişlik kart satırları alt
         ════════════════════════════════════════════════════════════ -->

    <!-- ÜST SATIR: Now Playing | Hoş Geldin Banner | Widgets -->
    <div class="home-layout__top <?= $topClass ?>">
        <?php $loader->display('player-info', $variant); ?>
        <?php $loader->display('welcome-banner', $variant); ?>
        <?php $loader->display('widget-grid', $variant); ?>
    </div>

    <!-- ALT SATIR: tam genişlik kart bölümleri -->
    <div class="home-song-btn-list-div">
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

        <!-- Sağ %58: Widget Grid — 12 slot (row1:2 · row2:5 · row3:5) -->
        <div class="home-layout__top-right">
            <?php $loader->display('widget-grid', $variant); ?>
        </div>

    </div>

    <!-- ALT SATIR: 3 kolon — En Son | Playlister | Sıradaki -->
    <div class="home-layout__bottom home-layout__bottom--embedded">

        <!-- Kolon 1: En Son Dinlenen Şarkılar (Figma node 1639:9904, 2×2 grid, 4 kart) -->
        <?php $loader->display('recent-tracks', $variant); ?>

    </div>

<?php endif; ?>

</div>

<!-- Welcome Modal — ilk girişte açılır (sadece 1024 embedded), localStorage 'cm_welcome_seen'
     Mantık: js/features/welcome-modal.js (shell footer.php yükler) — sadece [Başla] kapatır -->
<?php if (!$dm->isPhone()): ?>
<div id="welcomeModalOverlay" class="welcome-modal-overlay is-hidden">
    <div class="welcome-modal" role="dialog" aria-modal="true" aria-labelledby="welcomeModalTitle">
        <div class="welcome-modal__logo">
            <img class="welcome-modal__logo-img"
                 src="<?= $h($assetsUrl . '/Image/res-pink/logo/logo-text.png') ?>"
                 alt="CoreMusic">
        </div>
        <p class="welcome-modal__title" id="welcomeModalTitle">Hoş geldin</p>
        <p class="welcome-modal__sub">Prenses Işıl Peri</p>
        <p class="welcome-modal__desc">Sana özel seçilen melodiler, zarif deneyimler ve unutulmaz anlar burada başlıyor. CoreMusic ile müziğin senin dünyana dönüşsün. 💜</p>
        <button class="welcome-modal__btn" type="button">Başla</button>
    </div>
</div>
<?php endif; ?>

<?php /* C-F-13: footer.php artık shell'de (HtmlShellRenderer::renderChrome) — sayfa GÖVDESİNE include edilmez. */ ?>
