<?php declare(strict_types=1);
/**
 * pages/home.php — Ana Sayfa
 * Layer: L3 Presentation · 05_Pages (_home-layout.css, _home-components.css)
 * SSOT: .ai/.png/home-1024/ (Embedded 42/58 split) + .ai/.png/home-1920/ (3-sütun wide)
 * Layout: PNG sadakatli koşullu render (Guardrail #11)
 *   - Embedded (≤1024): top 42/58 + bottom 3 kolon (2×2 kart grid)
 *   - Wide (≥1920): top 3-sütun (Now Playing | Welcome Banner | Widgets) + tam genişlik kart satırları
 *   - 4K (≥2561): wide markup, 4K ölçek token'ları (CSS otomatik ölçekler)
 * Bileşenler: now-playing, home-widget, mini-card, home-welcome-banner, card-grid
 * Version: 1.0.0 — 2026-09-06 (sıfırdan yeniden yazım)
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

$h = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
};

$assetsUrl = defined('ASSETS_URL') ? ASSETS_URL : 'http://assets.coremusic.net';

/* ── Layout kararları ── */
$is4k        = $dm->shouldRender4kLayout();
$isWide      = $dm->shouldRenderWideLayout() || $is4k;
$layoutClass = $is4k ? 'home-layout--4k' : ($isWide ? 'home-layout--wide' : 'home-layout--embedded');
$topClass    = $is4k ? 'home-layout__top--4k' : 'home-layout__top--wide';

/* ── Şu an çalan (PNG metinleri varsayılan) ── */
$username      = $h((string)($_SESSION['MM_Username'] ?? 'Bayram Ali'));
$song          = $h((string)($_SESSION['current_song'] ?? 'Göksel - Sevil Neşelen'));
$album         = $h((string)($_SESSION['current_album'] ?? 'Hayat Rüya Gibi'));
$artist        = $h((string)($_SESSION['current_artist'] ?? 'Göksel'));
$elapsed       = $h((string)($_SESSION['current_elapsed'] ?? '00:00:00'));
$duration      = $h((string)($_SESSION['current_duration'] ?? '00:05:00'));
$bitrate       = $h((string)($_SESSION['current_bitrate'] ?? '350 kbps'));
$seekPct       = (int)($_SESSION['progress_pct'] ?? 30);
$imgNowArt     = $_SESSION['current_art'] ?? ($assetsUrl . '/Image/res-pink/album-goksel.png');

/* ── Doğrulanmış varlıklar (assets.coremusic.net/Image/) ── */
$imgArt1    = $assetsUrl . '/Image/res-pink/album-goksel.png';
$imgArt2    = $assetsUrl . '/Image/res-pink/album-kursat.png';
$imgBg      = $assetsUrl . '/Image/background/login-bg-female.png';
$imgStar    = $assetsUrl . '/Image/res-pink/star-filled.png';
$imgBt      = $assetsUrl . '/Image/res-pink/bluethoot-1-connected.png';
$imgSaat    = $assetsUrl . '/Image/res-pink/saat.png';
$imgDate    = $assetsUrl . '/Image/res-pink/date.png';
$imgEq      = $assetsUrl . '/Image/res-pink/equalizer.png';
$imgFolder  = $assetsUrl . '/Image/res-pink/folder.png';
$imgYoutube = $assetsUrl . '/Image/res-pink/app/youtube.png';
$imgKaristir = $assetsUrl . '/Image/res-pink/karistir.png';

/* ── En Son Dinlenen Şarkılar (PNG home-1920 satır sırası, 9 kart) ── */
$recentTracks = [
    ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00', 'art' => $imgArt1],
    ['t' => 'Göksel - Kabahat Senin Se', 'a' => 'Göksel', 'd' => '00:04:12', 'art' => $imgArt2],
    ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $imgArt1],
    ['t' => 'Göksel - Donbil Neşeler', 'a' => 'Göksel', 'd' => '00:04:37', 'art' => $imgArt2],
    ['t' => 'Göksel - Sevil Neşelen', 'a' => 'Göksel', 'd' => '00:05:00', 'art' => $imgArt1],
    ['t' => 'Göksel - Kabahat Senin Se', 'a' => 'Göksel', 'd' => '00:04:12', 'art' => $imgArt2],
    ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $imgArt1],
    ['t' => 'Göksel - Donbil Neşeler', 'a' => 'Göksel', 'd' => '00:04:37', 'art' => $imgArt2],
    ['t' => 'Bengü Manco - Gülbamege', 'a' => 'Bengü Manco', 'd' => '00:03:45', 'art' => $imgArt1],
];

/* ── Playlister kartları (PNG home-1920, 6 kart) ── */
$playlists = [
    ['t' => 'En Sevilen Şarkılarım', 's' => 'Slow ve Türküler', 'd' => '01:04:57', 'art' => $imgArt1],
    ['t' => 'Yeni Çıkan Türk Halk Müziği', 's' => 'Eskiler ve Türküler', 'd' => '00:48:12', 'art' => $imgArt2],
    ['t' => 'En Sevilen Şarkılarım', 's' => 'Akustik Seçkiler', 'd' => '01:12:30', 'art' => $imgArt1],
    ['t' => 'Yeni Çıkan Türk Halk Müziği', 's' => 'Oyun Havaları', 'd' => '00:56:04', 'art' => $imgArt2],
    ['t' => 'En Sevilen Şarkılarım', 's' => 'Nostaljik Slow', 'd' => '01:04:57', 'art' => $imgArt1],
    ['t' => 'Yeni Çıkan Türk Halk Müziği', 's' => 'Damar Şarkılar', 'd' => '00:52:19', 'art' => $imgArt2],
];

/* ── Mini card şablonu ── */
$miniCard = static function (array $item, string $metaClass, string $metaLabel) use ($h, $assetsUrl): string {
    $fallback = $assetsUrl . '/Image/res-pink/default-album.png';
    $art = is_string($item['art'] ?? null) && $item['art'] !== '' ? $item['art'] : $fallback;
    return '<a href="/playlist" class="mini-card" data-no-spa>'
        . '<div class="mini-card__art"><img src="' . $h($art) . '" alt="" width="50" height="50" loading="lazy"/></div>'
        . '<div class="mini-card__info">'
        . '<h3 class="mini-card__title">' . $h((string)$item['t']) . '</h3>'
        . '<p class="mini-card__subtitle">' . $h((string)($item['s'] ?? '')) . '</p>'
        . '<span class="' . $metaClass . '">' . $h($metaLabel) . '</span>'
        . '</div></a>';
};

/** @var array<int, string> — son dinlenen kart htmalleri */
$recentCards = array_map(
    static fn (array $t): string => $miniCard(
        ['t' => $t['t'], 's' => $t['a'], 'art' => $t['art']],
        'mini-card__subtitle',
        (string)$t['d']
    ),
    $recentTracks
);

/** @var array<int, string> — playlist kart htmalleri */
$playlistCards = array_map(
    static fn (array $p): string => $miniCard($p, 'mini-card__album', (string)$p['d']),
    $playlists
);
?>
<?php require __DIR__ . '/../header.php'; ?>

<main class="page-home home-layout <?= $layoutClass ?> <?= $dm->allClasses() ?>" role="main" aria-label="Ana Sayfa" <?= $dm->dataAttributes() ?>>

<?php if ($isWide): ?>
    <!-- ═══════════ WIDE / 4K — PNG: home-1920 ═══════════ -->
    <div class="home-layout__top <?= $topClass ?>">

        <!-- Sol: Now Playing (etiket:değer satırları — PNG birebir) -->
        <div class="home-layout__top-left--wide">
            <section class="now-playing" aria-label="Şu an çalan">
                <div class="now-playing__art"><img src="<?= $h((string)$imgNowArt) ?>" alt="" width="100" height="100" loading="lazy"/></div>
                <div class="now-playing__info">
                    <h1 class="now-playing__title">Şarkı Adı : <?= $song ?></h1>
                    <p class="now-playing__subtitle">Album : <?= $album ?></p>
                    <p class="now-playing__artist">Sanatçı : <?= $artist ?></p>
                    <p class="now-playing__subtitle">Yıldız : <img src="<?= $h($imgStar) ?>" alt="5/5 yıldız" width="14" height="14"/><img src="<?= $h($imgStar) ?>" alt="" width="14" height="14"/><img src="<?= $h($imgStar) ?>" alt="" width="14" height="14"/><img src="<?= $h($imgStar) ?>" alt="" width="14" height="14"/><img src="<?= $h($imgStar) ?>" alt="" width="14" height="14"/></p>
                    <p class="now-playing__subtitle">Bit rate : <?= $bitrate ?></p>
                    <p class="now-playing__subtitle">Süre : <?= $elapsed ?> / <?= $duration ?></p>
                </div>
            </section>
        </div>

        <!-- Orta: Hoş Geldin Banner -->
        <div class="home-layout__top-center">
            <section class="home-welcome-banner" aria-label="Hoş geldin">
                <div class="home-welcome-banner__bg"><img src="<?= $h($imgBg) ?>" alt="" loading="lazy"/></div>
                <div class="home-welcome-banner__content">
                    <p class="home-welcome-banner__title">Hoş Geldin</p>
                    <h1 class="home-welcome-banner__user"><?= $username ?></h1>
                    <p class="home-welcome-banner__tagline">"Müzik, ruhun gıdasıdır; her nota, bir hatırayı canlandırır."</p>
                    <p class="home-welcome-banner__brand">Core Music</p>
                    <a class="home-welcome-banner__btn" href="/kesfet" data-no-spa>Keşfetmeye Başla
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                    </a>
                    <div class="home-welcome-banner__stats">
                        <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg></span><span class="home-welcome-banner__stat-value">2.450</span></div>
                        <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 5h2v14H3zm4 0h2v14H7zm4 0h2v14h-2zm4 0h6v14h-6z"/></svg></span><span class="home-welcome-banner__stat-value">156</span></div>
                        <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14.5c-2.49 0-4.5-2.01-4.5-4.5S9.51 7.5 12 7.5s4.5 2.01 4.5 4.5-2.01 4.5-4.5 4.5z"/></svg></span><span class="home-welcome-banner__stat-value">87</span></div>
                        <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg></span><span class="home-welcome-banner__stat-value">42</span></div>
                        <div class="home-welcome-banner__stat"><span class="home-welcome-banner__stat-icon"><svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54z"/></svg></span><span class="home-welcome-banner__stat-value">1.250</span></div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sağ: Widget'lar 2×2 (PNG: Hoparlör, Hava, Saat, Kitaplığım) -->
        <div class="home-layout__top-right--wide">
            <div class="home-widgets-cluster--wide">
                <div class="home-widget-grid">

                    <article class="home-widget">
                        <header class="home-widget__header">
                            <span class="home-widget__icon"><img src="<?= $h($imgBt) ?>" alt="" width="20" height="20" loading="lazy"/></span>
                            <h2 class="home-widget__title">Hoparlör</h2>
                        </header>
                        <div class="home-widget__glass home-widget__info">Core Music - Hoparlör</div>
                    </article>

                    <article class="home-widget">
                        <header class="home-widget__header">
                            <span class="home-widget__icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="14" r="4"/><path d="M16 18a4 4 0 0 0 0-8 6 6 0 0 0-11.3 2"/></svg></span>
                            <h2 class="home-widget__title">Hava Durumu</h2>
                        </header>
                        <div class="home-widget__glass home-widget__info">
                            <span class="home-widget__subtitle">Genelya 13°</span>
                            Istanbul
                        </div>
                    </article>

                    <article class="home-widget">
                        <header class="home-widget__header">
                            <span class="home-widget__icon"><img src="<?= $h($imgSaat) ?>" alt="" width="20" height="20" loading="lazy"/></span>
                            <h2 class="home-widget__title">07:00</h2>
                        </header>
                        <div class="home-widget__glass home-widget__info">
                            <img src="<?= $h($imgDate) ?>" alt="" width="14" height="14" loading="lazy"/>
                            <span class="home-widget__subtitle">5 Haziran 2025</span>
                        </div>
                    </article>

                    <article class="home-widget">
                        <header class="home-widget__header">
                            <span class="home-widget__icon"><img src="<?= $h($imgFolder) ?>" alt="" width="20" height="20" loading="lazy"/></span>
                            <h2 class="home-widget__title">Kitaplığım</h2>
                        </header>
                        <div class="home-widget__glass home-widget__folders">
                            <button type="button" class="home-widget__folder-btn" aria-label="Kitaplık" data-action="open-library"><img src="<?= $h($imgFolder) ?>" alt="" width="18" height="18" loading="lazy"/></button>
                            <button type="button" class="home-widget__folder-btn" aria-label="YouTube" data-action="open-youtube"><img src="<?= $h($imgYoutube) ?>" alt="" width="18" height="18" loading="lazy"/></button>
                            <button type="button" class="home-widget__folder-btn" aria-label="Favoriler" data-action="open-favorites"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54z"/></svg></button>
                            <button type="button" class="home-widget__folder-btn" aria-label="Karıştır" data-action="shuffle"><img src="<?= $h($imgKaristir) ?>" alt="" width="18" height="18" loading="lazy"/></button>
                        </div>
                    </article>

                </div>
            </div>
        </div>
    </div>

    <!-- Alt: tam genişlik kart bölümleri (PNG home-1920: iki bölüm alt alta) -->
    <div class="home-cards-wrapper--wide">
        <section aria-label="En Son Dinlenen Şarkılar">
            <h2 class="section-title">En Son Dinlenen Şarkılar</h2>
            <div class="card-grid card-grid--scroll">
<?php foreach ($recentCards as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
            </div>
        </section>
        <section aria-label="Son Oluşturan ve Sistem Tarafından Oluşturulan Playlister">
            <h2 class="section-title">Son Oluşturan &amp; Sistem Tarafından Oluşturulan Playlister</h2>
            <div class="card-grid card-grid--scroll">
<?php foreach ($playlistCards as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
            </div>
        </section>
    </div>

<?php else: ?>
    <!-- ═══════════ EMBEDDED 1024×600 — PNG: home-1024 (Split 42/58) ═══════════ -->
    <div class="home-layout__top home-layout__top--embedded">

        <!-- Sol 42%: Now Playing (PNG: art + başlık/album/sanatçı + pembe seek) -->
        <div class="home-layout__top-left">
            <section class="now-playing" aria-label="Şu an çalan">
                <div class="now-playing__art"><img src="<?= $h((string)$imgNowArt) ?>" alt="" width="100" height="100" loading="lazy"/></div>
                <div class="now-playing__info">
                    <h1 class="now-playing__title"><?= $song ?></h1>
                    <p class="now-playing__subtitle"><?= $album ?></p>
                    <p class="now-playing__artist"><?= $artist ?></p>
                    <div class="now-playing__seek"><div class="now-playing__seek-fill" style="width: <?= $seekPct ?>%;"></div></div>
                    <div class="now-playing__times"><span><?= $elapsed ?></span><span><?= $duration ?></span></div>
                </div>
            </section>
        </div>

        <!-- Sağ 58%: Widget grid 2×2 -->
        <div class="home-layout__top-right">
            <div class="home-widget-grid">

                <article class="home-widget">
                    <header class="home-widget__header">
                        <span class="home-widget__icon"><img src="<?= $h($imgBt) ?>" alt="" width="20" height="20" loading="lazy"/></span>
                        <h2 class="home-widget__title">Hoparlör</h2>
                    </header>
                    <div class="home-widget__glass home-widget__info">Core Music - Hoparlör</div>
                </article>

                <article class="home-widget">
                    <header class="home-widget__header">
                        <span class="home-widget__icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="8" cy="14" r="4"/><path d="M16 18a4 4 0 0 0 0-8 6 6 0 0 0-11.3 2"/></svg></span>
                        <h2 class="home-widget__title">Hava Durumu</h2>
                    </header>
                    <div class="home-widget__glass home-widget__info">
                        <span class="home-widget__subtitle">Genelya 13°</span>
                        Istanbul
                    </div>
                </article>

                <article class="home-widget">
                    <header class="home-widget__header">
                        <span class="home-widget__icon"><img src="<?= $h($imgSaat) ?>" alt="" width="20" height="20" loading="lazy"/></span>
                        <h2 class="home-widget__title">07:00</h2>
                    </header>
                    <div class="home-widget__glass home-widget__info">
                        <img src="<?= $h($imgDate) ?>" alt="" width="14" height="14" loading="lazy"/>
                        <span class="home-widget__subtitle">5 Haziran 2026</span>
                    </div>
                </article>

                <article class="home-widget">
                    <header class="home-widget__header">
                        <span class="home-widget__icon"><img src="<?= $h($imgFolder) ?>" alt="" width="20" height="20" loading="lazy"/></span>
                        <h2 class="home-widget__title">Kitaplığım</h2>
                    </header>
                    <div class="home-widget__glass home-widget__folders">
                        <button type="button" class="home-widget__folder-btn" aria-label="Kitaplık" data-action="open-library"><img src="<?= $h($imgFolder) ?>" alt="" width="18" height="18" loading="lazy"/></button>
                        <button type="button" class="home-widget__folder-btn" aria-label="YouTube" data-action="open-youtube"><img src="<?= $h($imgYoutube) ?>" alt="" width="18" height="18" loading="lazy"/></button>
                        <button type="button" class="home-widget__folder-btn" aria-label="Favoriler" data-action="open-favorites"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54z"/></svg></button>
                        <button type="button" class="home-widget__folder-btn" aria-label="Karıştır" data-action="shuffle"><img src="<?= $h($imgKaristir) ?>" alt="" width="18" height="18" loading="lazy"/></button>
                    </div>
                </article>

            </div>
        </div>
    </div>

    <!-- Alt: 3 kolon (PNG: En Son 2×2 | Playlister 2×2 + buton | Sıradaki) -->
    <div class="home-layout__bottom home-layout__bottom--embedded">

        <div class="home-layout__bottom-left">
            <h2 class="section-title">En Son Dinlenen Şarkılar</h2>
            <div class="embedded-card-grid-2x2">
<?php foreach (array_slice($recentCards, 0, 4) as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
            </div>
        </div>

        <div class="home-layout__bottom-center">
            <h2 class="section-title">Son Oluşturan &amp; Sistem Tarafından Oluşturulan Playlister</h2>
            <div class="embedded-card-grid-2x2">
<?php foreach (array_slice($playlistCards, 0, 4) as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
            </div>
            <button type="button" class="toggle-row" data-action="show-playlist-list">Playlister Listesini Görüntüle</button>
        </div>

        <div class="home-layout__bottom-right">
            <h2 class="section-title">Sıradaki Şarkılar</h2>
            <a href="/playlist" class="mini-card" data-no-spa>
                <div class="mini-card__art"><img src="<?= $h($imgArt1) ?>" alt="" width="50" height="50" loading="lazy"/></div>
                <div class="mini-card__info">
                    <h3 class="mini-card__title"><?= $song ?></h3>
                    <p class="mini-card__subtitle"><?= $album ?></p>
                    <span class="mini-card__album">236 kez</span>
                </div>
            </a>
        </div>

    </div>
<?php endif; ?>

</main>
<?php require __DIR__ . '/../footer.php'; ?>
