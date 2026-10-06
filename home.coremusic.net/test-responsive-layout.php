<?php declare(strict_types=1);
/**
 * test-responsive-layout.php — Responsive Layout Test (1024/1920/3840px)
 * Mockup validation against Figma designs
 */

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/config/constants.php';

use CoreMusic\Bootstrap\RuntimeBootstrap;
use CoreMusic\Device\DeviceManager;
use CoreMusic\Home\Class\HomeLayoutVariant;

RuntimeBootstrap::boot(DEBUG_MODE);
require_once __DIR__ . '/config/config.php';

// Simulate session
$_SESSION['MM_UserID'] = 'test_user';
$_SESSION['csp_nonce'] = bin2hex(random_bytes(16));

// Get breakpoint from query param
$breakpoint = $_GET['bp'] ?? '1024';
$breakpoints = [1024, 1440, 1920, 3840];

if (!in_array((int)$breakpoint, $breakpoints, true)) {
    $breakpoint = 1024;
}

$dm = DeviceManager::instance([
    'viewportW' => (int)$breakpoint,
    'viewportH' => 1080,
]);

$variant = HomeLayoutVariant::fromFlags($dm->shouldRenderWideLayout(), $dm->shouldRender4kLayout());

$layoutClass = $dm->shouldRender4kLayout()
    ? 'home-layout--4k'
    : ($dm->shouldRenderWideLayout() ? 'home-layout--wide' : 'home-layout--embedded');

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=<?= $breakpoint ?>, initial-scale=1.0">
    <title>Responsive Test <?= $breakpoint ?>px</title>
    <!-- normalize/reboot/a-layout-tokens diskte yok, Faz 1 tespiti (00_Reset klasörü + a-layout-tokens.css 0 isabet) — link satırları kaldırıldı -->
    <link rel="stylesheet" href="http://assets.coremusic.net:81/Css/08_Devices/d-embedded.css">
    <link rel="stylesheet" href="http://assets.coremusic.net:81/Css/04_Components/_player-info.css">
    <style>
        body { margin: 0; padding: 20px; background: #1a1a1a; color: #fff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto; }
        .test-header { position: fixed; top: 0; left: 0; right: 0; background: rgba(255,0,0,0.8); padding: 15px; text-align: center; z-index: 9999; }
        .test-header h1 { margin: 0; font-size: 18px; }
        .test-nav { margin-top: 70px; text-align: center; padding-bottom: 20px; border-bottom: 1px solid #555; }
        .test-nav a { margin: 0 10px; padding: 8px 16px; background: #333; color: #fff; text-decoration: none; border-radius: 4px; display: inline-block; }
        .test-nav a.active { background: #ff65e9; }
        .layout-info { background: #222; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .layout-info strong { color: #ff65e9; }
    </style>
</head>
<body>

<div class="test-header">
    <h1>CoreMusic Responsive Test — <?= $breakpoint ?>px</h1>
    <p style="margin: 5px 0; font-size: 12px;">Layout: <strong><?= $layoutClass ?></strong> | Variant: <strong><?= $variant->isWide() ? 'WIDE' : 'EMBEDDED' ?></strong></p>
</div>

<div class="test-nav">
    <a href="?bp=1024" class="<?= $breakpoint === '1024' ? 'active' : '' ?>">1024px (Embedded)</a>
    <a href="?bp=1440" class="<?= $breakpoint === '1440' ? 'active' : '' ?>">1440px (Laptop)</a>
    <a href="?bp=1920" class="<?= $breakpoint === '1920' ? 'active' : '' ?>">1920px (Desktop)</a>
    <a href="?bp=3840" class="<?= $breakpoint === '3840' ? 'active' : '' ?>">3840px (4K)</a>
</div>

<div class="layout-info">
    <strong>Device Decision:</strong><br>
    shouldRenderWideLayout(): <?= $dm->shouldRenderWideLayout() ? 'TRUE' : 'FALSE' ?><br>
    shouldRender4kLayout(): <?= $dm->shouldRender4kLayout() ? 'TRUE' : 'FALSE' ?><br>
    viewport: <?= $breakpoint ?>×1080px
</div>

<!-- Render Player Info in both variants -->
<h2>Player Info Component Test</h2>

<?php if ($variant->isWide()): ?>
<div class="player-info player-info--wide" style="background: #222; padding: 20px; border-radius: 8px;">
    <div class="player-info__cover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 150px; height: 150px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px;">
        Cover 150×150
    </div>
    <div class="player-info__info-panel" style="padding-left: 20px;">
        <p style="margin: 0; font-size: 13px;">🎵 Şarkı: Göksel - Sevil Neşelen</p>
        <p style="margin: 5px 0; font-size: 13px;">⭕ Albüm: Hayat Rüya Gibi</p>
        <p style="margin: 5px 0; font-size: 13px;">🎤 Sanatçı: Göksel</p>
        <p style="margin: 5px 0; font-size: 13px;">⭐ Yıldız: ★★★★★</p>
        <p style="margin: 5px 0; font-size: 13px;">📊 Bit rate: 350 kbps [MP3]</p>
        <p style="margin: 5px 0; font-size: 13px;">⏱️ Süre: 00:00:00 / 05:45:00</p>
    </div>
</div>
<?php else: ?>
<div class="now-playing now-playing--embedded" style="background: #222; padding: 16px; border-radius: 8px; display: grid; grid-template-columns: 72px 1fr; gap: 16px; max-width: 392px;">
    <div class="now-playing__art now-playing__art--embedded" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 72px; height: 72px; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 11px;">
        72×72
    </div>
    <div class="now-playing__info now-playing__info--embedded">
        <h3 style="margin: 0; font-size: 13px;">Göksel - Sevil Neşelen</h3>
        <p style="margin: 5px 0 0 0; font-size: 12px;">Hayat Rüya Gibi</p>
        <p style="margin: 3px 0 0 0; font-size: 12px;">Göksel</p>
    </div>
</div>
<div style="margin-top: 10px; max-width: 392px; background: #222; padding: 8px; border-radius: 4px; font-size: 11px;">
    Progress: ▌▌░░░░░░░░ (00:00:00 / 05:45:00)
</div>
<?php endif; ?>

<h2>Mockup Comparison</h2>
<table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
    <thead>
        <tr style="background: #333;">
            <th style="padding: 10px; border: 1px solid #555; text-align: left;">Element</th>
            <th style="padding: 10px; border: 1px solid #555; text-align: center;">1024px</th>
            <th style="padding: 10px; border: 1px solid #555; text-align: center;">1920px</th>
            <th style="padding: 10px; border: 1px solid #555; text-align: center;">3840px</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 10px; border: 1px solid #555;">Player Cover</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">72×72</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">150×150</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">300×300</td>
        </tr>
        <tr style="background: #1a1a1a;">
            <td style="padding: 10px; border: 1px solid #555;">Player Container</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">392×131</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">469×184</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">938×368</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #555;">Layout Grid</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">2-col songs</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">3-col songs</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">4-5 col songs</td>
        </tr>
        <tr style="background: #1a1a1a;">
            <td style="padding: 10px; border: 1px solid #555;">Welcome Card</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">✗</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">✓</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">✓</td>
        </tr>
        <tr>
            <td style="padding: 10px; border: 1px solid #555;">Right Widgets</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">✗</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">✓</td>
            <td style="padding: 10px; border: 1px solid #555; text-align: center;">✓</td>
        </tr>
    </tbody>
</table>

</body>
</html>
