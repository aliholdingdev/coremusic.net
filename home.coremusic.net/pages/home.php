<?php declare(strict_types=1);
/**
 * pages/home.php — Ana Sayfa
 * Layer: L3 Presentation · 05_Pages (_home-layout.css, _home-components.css)
 * SSOT: .ai/.png/home-1024/ (Embedded 42/58 split) + .ai/.png/home-1920/ (3-sütun wide)
 * Layout: PNG sadakatli koşullu render (Guardrail #11)
 *   - Embedded (≤1024): top 42/58 + bottom 3 kolon (2×2 kart grid) + Welcome Modal (PNG S02)
 *   - Wide (≥1920): top 3-sütun (Now Playing | Welcome Banner | Widgets) + tam genişlik kart satırları
 *   - 4K (≥2561): wide markup, 4K ölçek token'ları (CSS otomatik ölçekler)
 * Bileşen mimarisi v2: tüm widget/bölümler pages/components/ altında ayrı PHP view'dır ve
 * her biri CoreMusic\Home\Component\ComponentInterface uygulayan IMMUTABLE sınıflar üzerinden,
 * ComponentLoader ile dinamik yüklenir. Layout varyantı HomeLayoutVariant enum'dur.
 *   now-playing | welcome-banner | home-widgets | recent-tracks | playlists | up-next | welcome-modal
 * Scroll sözleşmesi: scroll YALNIZCA .home-layout container'ında — header/footer fixed.
 * A11y: tek <h1> (şarkı adı), role="progressbar" seek, yıldız rating aria, kontrast ≥4.5:1
 * Version: 2.0.0 — 2026-09-09 (Bileşen mimarisi v2: ComponentInterface + HomeLayoutVariant enum +
 *            immutable component'lar; markup PNG birebir korunmuştur)
 */

use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Component\ComponentLoader;
use CoreMusic\Home\Component\HomeLayoutVariant;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

/* ── Layout kararları ── */
$is4k        = $dm->shouldRender4kLayout();
$isWide      = $dm->shouldRenderWideLayout() || $is4k;
$layoutClass = $is4k ? 'home-layout--4k' : ($isWide ? 'home-layout--wide' : 'home-layout--embedded');
$topClass    = $is4k ? 'home-layout__top--4k' : 'home-layout__top--wide';
$variant     = HomeLayoutVariant::fromFlags($isWide, $is4k);

/* ── Bileşen yükleyici (dinamik, class tabanlı) ── */
$loader = new ComponentLoader();

require __DIR__ . '/../header.php';
?>

<main class="page-home home-layout <?= $layoutClass ?> <?= $dm->allClasses() ?>" role="main" aria-label="Ana Sayfa" <?= $dm->dataAttributes() ?>>

<?php if ($isWide): ?>
    <!-- ═══════════ WIDE / 4K — PNG: home-1920 ═══════════ -->
    <div class="home-layout__top <?= $topClass ?>">

        <!-- Sol: Now Playing -->
        <div class="home-layout__top-left--wide">
<?php $loader->display('now-playing', $variant); ?>
        </div>

        <!-- Orta: Hoş Geldin Banner -->
        <div class="home-layout__top-center">
<?php $loader->display('welcome-banner', $variant); ?>
        </div>

        <!-- Sağ: Widget'lar 2×2 (PNG: Hoparlör, Hava, Saat, Kitaplığım) -->
        <div class="home-layout__top-right--wide">
<?php $loader->display('home-widgets', $variant); ?>
        </div>
    </div>

    <!-- Alt: tam genişlik kart bölümleri (PNG home-1920: iki bölüm alt alta) -->
    <div class="home-cards-wrapper--wide">
<?php $loader->display('recent-tracks', $variant); ?>
<?php $loader->display('playlists', $variant); ?>
    </div>

<?php else: ?>
    <!-- ═══════════ EMBEDDED 1024×600 — PNG: home-1024 (Split 42/58) ═══════════ -->
    <div class="home-layout__top home-layout__top--embedded">

        <!-- Sol 42%: Now Playing -->
        <div class="home-layout__top-left">
<?php $loader->display('now-playing', $variant); ?>
        </div>

        <!-- Sağ 58%: Widget grid 2×2 -->
        <div class="home-layout__top-right">
<?php $loader->display('home-widgets', $variant); ?>
        </div>
    </div>

    <!-- Alt: 3 kolon (PNG: En Son 2×2 | Playlister 2×2 + buton | Sıradaki) -->
    <div class="home-layout__bottom home-layout__bottom--embedded">

        <div class="home-layout__bottom-left">
<?php $loader->display('recent-tracks', $variant); ?>
        </div>

        <div class="home-layout__bottom-center">
<?php $loader->display('playlists', $variant); ?>
        </div>

        <div class="home-layout__bottom-right">
<?php $loader->display('up-next', $variant); ?>
        </div>

    </div>
<?php endif; ?>

</main>

<?php if (!$dm->isPhone()): ?>
<?php $loader->display('welcome-modal', $variant); ?>
<?php endif; ?>

<?php require __DIR__ . '/../footer.php'; ?>
