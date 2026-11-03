<?php declare(strict_types=1);
/**
 * pages/kesfet.php — Keşfet / Explore & Discovery Portalı
 * ------------------------------------------------------------------
 * Layer    : L3 Presentation · 05_Pages (p-kesfet.css)
 * Route    : /kesfet
 * ------------------------------------------------------------------
 */

use CoreMusic\Device\DeviceManager;

if (!isset($dm)) {
    $dm = DeviceManager::instance([
        'viewportW' => (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
        'viewportH' => (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
    ]);
}

$h = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$assetsUrl = defined('ASSETS_URL') ? rtrim((string)ASSETS_URL, '/') : 'http://assets.coremusic.net';
$cspNonce = (string)($_SESSION['csp_nonce'] ?? '');
$nonceAttr = $cspNonce !== '' ? ' nonce="' . $h($cspNonce) . '"' : '';

// Hero Spotlight Track
$heroTrack = [
    'title'    => 'Sevil Neşelen',
    'artist'   => 'Göksel',
    'album'    => 'Hayat Rüya Gibi',
    'cover'    => $assetsUrl . '/Image/res-pink/album-goksel.png',
    'badge'    => 'Studio Master',
    'format'   => 'FLAC 24-bit / 96 kHz',
    'plays'    => '1.240.500 dinleme',
    'duration' => '03:45',
    'tagline'  => 'Haftanın Öne Çıkanı',
];

// Top 10 Charts Tracks
$topTracks = [
    [
        'rank' => '01',
        'title' => 'Baksana Talihe',
        'artist' => 'Göksel',
        'album' => 'Baksana Talihe (Single)',
        'cover' => $assetsUrl . '/Image/res-pink/album-goksel.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'pop',
        'duration' => '04:12',
    ],
    [
        'rank' => '02',
        'title' => 'Bazen Unutmak İstersin',
        'artist' => 'Kürşat Başar',
        'album' => 'Kaldığımız Yerden',
        'cover' => $assetsUrl . '/Image/res-pink/album-kursat.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'caz',
        'duration' => '03:45',
    ],
    [
        'rank' => '03',
        'title' => 'Golden Gate Sunset (Studio Mix)',
        'artist' => 'CoreMusic Ensemble',
        'album' => 'Audiophile Master Tape Vol. 1',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'format' => '32-bit Float',
        'genre' => 'elektronik',
        'duration' => '06:10',
    ],
    [
        'rank' => '04',
        'title' => 'Sen Ağlama',
        'artist' => 'Sezen Aksu',
        'album' => 'Sen Ağlama (Remastered)',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'nostalji',
        'duration' => '05:28',
    ],
    [
        'rank' => '05',
        'title' => 'Gülümse',
        'artist' => 'Sezen Aksu',
        'album' => 'Gülümse (Audiophile Edition)',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'pop',
        'duration' => '04:55',
    ],
    [
        'rank' => '06',
        'title' => 'Dua Ediyorum',
        'artist' => 'Gülşah Tütüncü',
        'album' => 'Dua Ediyorum',
        'cover' => $assetsUrl . '/Image/res-pink/album-goksel.png',
        'format' => 'MP3 320k',
        'genre' => 'pop',
        'duration' => '03:30',
    ],
    [
        'rank' => '07',
        'title' => 'Arada Sırada',
        'artist' => 'Ajda Pekkan',
        'album' => 'Farkın Bu',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'pop',
        'duration' => '04:02',
    ],
    [
        'rank' => '08',
        'title' => 'Istanbul Geceleri',
        'artist' => 'Mercan Dede',
        'album' => 'Su',
        'cover' => $assetsUrl . '/Image/res-pink/album-kursat.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'akustik',
        'duration' => '06:40',
    ],
    [
        'rank' => '09',
        'title' => 'Dönence',
        'artist' => 'Barış Manço',
        'album' => 'Sözüm Meclisten Dışarı',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'format' => 'FLAC 96kHz',
        'genre' => 'rock',
        'duration' => '06:05',
    ],
    [
        'rank' => '10',
        'title' => 'Gece Yolculuğu',
        'artist' => 'Anatolian Sessions',
        'album' => 'Ege Rüzgarı',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'format' => '32-bit Float',
        'genre' => 'elektronik',
        'duration' => '05:14',
    ],
];

// Featured Albums
$featuredAlbums = [
    [
        'name' => 'Baksana Talihe (Single)',
        'artist' => 'Göksel',
        'year' => '2024',
        'format' => 'FLAC 24-bit',
        'cover' => $assetsUrl . '/Image/res-pink/album-goksel.png',
        'url' => '/album-detail?id=1',
    ],
    [
        'name' => 'Kaldığımız Yerden',
        'artist' => 'Kürşat Başar',
        'year' => '2023',
        'format' => 'FLAC 24-bit',
        'cover' => $assetsUrl . '/Image/res-pink/album-kursat.png',
        'url' => '/album-detail?id=2',
    ],
    [
        'name' => 'Audiophile Master Tape Vol. 1',
        'artist' => 'CoreMusic Studio',
        'year' => '2026',
        'format' => 'Master DSD / 32-bit',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'url' => '/album-detail?id=3',
    ],
    [
        'name' => 'Sen Ağlama Remastered',
        'artist' => 'Sezen Aksu',
        'year' => '2025',
        'format' => 'FLAC 24-bit',
        'cover' => $assetsUrl . '/Image/res-pink/default-album.png',
        'url' => '/album-detail?id=4',
    ],
];
?>

<div class="kesfet-page page-layout <?= $h($dm->allClasses()) ?>" <?= $dm->dataAttributes() ?>>

    <!-- 1. HERO SPOTLIGHT BANNER -->
    <section class="kesfet-hero" aria-label="Haftanın Öne Çıkanı">
        <div class="kesfet-hero__cover-wrap">
            <img class="kesfet-hero__cover" src="<?= $h($heroTrack['cover']) ?>" alt="<?= $h($heroTrack['title']) ?>" loading="eager" width="180" height="180">
            <span class="kesfet-hero__badge"><?= $h($heroTrack['badge']) ?></span>
        </div>
        <div class="kesfet-hero__info">
            <span class="kesfet-hero__tagline"><?= $h($heroTrack['tagline']) ?></span>
            <h1 class="kesfet-hero__title"><?= $h($heroTrack['title']) ?></h1>
            <div class="kesfet-hero__artist"><?= $h($heroTrack['artist']) ?> • <?= $h($heroTrack['album']) ?></div>
            <div class="kesfet-hero__meta">
                <span><?= $h($heroTrack['format']) ?></span>
                <span>•</span>
                <span><?= $h($heroTrack['plays']) ?></span>
                <span>•</span>
                <span><?= $h($heroTrack['duration']) ?></span>
            </div>
            <div class="kesfet-hero__actions">
                <button type="button" class="kesfet-btn-play" id="hero-play-btn" data-title="<?= $h($heroTrack['title']) ?>" data-artist="<?= $h($heroTrack['artist']) ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                    <span>Şimdi Dinle</span>
                </button>
                <a href="/playlist" class="kesfet-btn-queue" style="text-decoration: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
                    </svg>
                    <span>Listeye Ekle</span>
                </a>
            </div>
        </div>
    </section>

    <!-- 2. KATEGORİ VE TÜR ÇİPLERİ -->
    <nav class="kesfet-chips-bar" aria-label="Müzik Türü Filtreleri">
        <button type="button" class="kesfet-chip is-active" data-genre="all">Tümü (Top 10)</button>
        <button type="button" class="kesfet-chip" data-genre="pop">Pop</button>
        <button type="button" class="kesfet-chip" data-genre="rock">Rock</button>
        <button type="button" class="kesfet-chip" data-genre="nostalji">Nostalji</button>
        <button type="button" class="kesfet-chip" data-genre="caz">Caz &amp; Blues</button>
        <button type="button" class="kesfet-chip" data-genre="elektronik">Elektronik</button>
        <button type="button" class="kesfet-chip" data-genre="akustik">Akustik</button>
    </nav>

    <!-- 3. RUH HALİ & KONSEPT KARTLARI -->
    <section class="kesfet-section" aria-labelledby="kesfet-moods-title">
        <div class="kesfet-section__header">
            <h2 class="kesfet-section__title" id="kesfet-moods-title">Ruh Hali &amp; Konsept Keşfi</h2>
        </div>
        <div class="kesfet-moods-grid">
            <a href="/playlist" class="kesfet-mood-card kesfet-mood-card--akustik">
                <h3 class="kesfet-mood-card__name">Akustik Huzur</h3>
                <p class="kesfet-mood-card__desc">Sakin, saf ve dinlendirici akustik teller</p>
            </a>
            <a href="/playlist" class="kesfet-mood-card kesfet-mood-card--gece">
                <h3 class="kesfet-mood-card__name">Gece Sürüşü</h3>
                <p class="kesfet-mood-card__desc">Derin baslar ve hipnotik synthwave akışı</p>
            </a>
            <a href="/playlist" class="kesfet-mood-card kesfet-mood-card--odak">
                <h3 class="kesfet-mood-card__name">Odaklanma &amp; İş</h3>
                <p class="kesfet-mood-card__desc">Lo-Fi, ambient ve zihin açıcı ritimler</p>
            </a>
            <a href="/playlist" class="kesfet-mood-card kesfet-mood-card--nostalji">
                <h3 class="kesfet-mood-card__name">Nostalji &amp; Türk Sanat</h3>
                <p class="kesfet-mood-card__desc">Unutulmayan 70'ler, 80'ler ve taş plaklar</p>
            </a>
            <a href="/playlist" class="kesfet-mood-card kesfet-mood-card--audiophile">
                <h3 class="kesfet-mood-card__name">Audiophile Studio</h3>
                <p class="kesfet-mood-card__desc">32-bit Float, DSD ve kayıpsız master kayıtlar</p>
            </a>
            <a href="/playlist" class="kesfet-mood-card kesfet-mood-card--elektronik">
                <h3 class="kesfet-mood-card__name">Elektronik &amp; Club</h3>
                <p class="kesfet-mood-card__desc">Yüksek enerjili progressive ve deep house</p>
            </a>
        </div>
    </section>

    <!-- 4. HAFTANIN EN İYİLERİ (SOL) & SEÇKİN ALBÜMLER (SAĞ) -->
    <div class="kesfet-split-grid">
        <!-- SOL: TOP 10 PARÇA LİSTESİ -->
        <section class="kesfet-charts-panel" aria-labelledby="kesfet-charts-title">
            <div class="kesfet-section__header">
                <h2 class="kesfet-section__title" id="kesfet-charts-title">Haftanın En Çok Dinlenenleri</h2>
                <span style="font-size: 12px; color: var(--cm-primary); font-weight: 600;">Canlı Sıralama</span>
            </div>

            <div class="kesfet-track-list" id="kesfet-track-list">
                <?php foreach ($topTracks as $track): ?>
                <div class="kesfet-track-row" data-genre="<?= $h($track['genre']) ?>" data-title="<?= $h($track['title']) ?>" data-artist="<?= $h($track['artist']) ?>">
                    <span class="kesfet-track-rank"><?= $h($track['rank']) ?></span>
                    <img class="kesfet-track-cover" src="<?= $h($track['cover']) ?>" alt="<?= $h($track['title']) ?>" loading="lazy" width="44" height="44">
                    <div class="kesfet-track-info">
                        <span class="kesfet-track-title"><?= $h($track['title']) ?></span>
                        <span class="kesfet-track-artist"><?= $h($track['artist']) ?> • <?= $h($track['album']) ?></span>
                    </div>
                    <span class="kesfet-track-format"><?= $h($track['format']) ?></span>
                    <span class="kesfet-track-duration"><?= $h($track['duration']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- SAĞ: ÖNE ÇIKAN ALBÜMLER -->
        <aside class="kesfet-sidebar-panel" aria-labelledby="kesfet-albums-title">
            <div class="kesfet-section__header">
                <h2 class="kesfet-section__title" id="kesfet-albums-title" style="font-size: 17px;">Öne Çıkan Albümler</h2>
                <a href="/albumler" style="font-size: 12px; color: var(--cm-primary); text-decoration: none; font-weight: 600;">Tümü &rarr;</a>
            </div>

            <?php foreach ($featuredAlbums as $alb): ?>
            <a href="<?= $h($alb['url']) ?>" class="kesfet-mini-album">
                <img class="kesfet-mini-album__cover" src="<?= $h($alb['cover']) ?>" alt="<?= $h($alb['name']) ?>" loading="lazy" width="60" height="60">
                <div class="kesfet-mini-album__info">
                    <div class="kesfet-mini-album__name"><?= $h($alb['name']) ?></div>
                    <div class="kesfet-mini-album__artist"><?= $h($alb['artist']) ?> • <?= $h($alb['year']) ?></div>
                    <div class="kesfet-mini-album__meta"><?= $h($alb['format']) ?></div>
                </div>
            </a>
            <?php endforeach; ?>
        </aside>
    </div>

</div>

<script<?= $nonceAttr ?>>
document.addEventListener('DOMContentLoaded', function() {
    // Kategori Filtreleme
    const chips = document.querySelectorAll('.kesfet-chip');
    const trackRows = document.querySelectorAll('.kesfet-track-row');

    chips.forEach(chip => {
        chip.addEventListener('click', function() {
            chips.forEach(c => c.classList.remove('is-active'));
            this.classList.add('is-active');

            const genre = this.getAttribute('data-genre') || 'all';

            trackRows.forEach(row => {
                const trackGenre = row.getAttribute('data-genre') || '';
                if (genre === 'all' || trackGenre === genre) {
                    row.style.display = 'flex';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Parça Tıklama / Çalma
    trackRows.forEach(row => {
        row.addEventListener('click', function() {
            const title = this.getAttribute('data-title');
            const artist = this.getAttribute('data-artist');
            console.log('[Keşfet] Oynatılıyor: ' + title + ' - ' + artist);
            trackRows.forEach(r => r.style.borderColor = 'transparent');
            this.style.borderColor = 'var(--cm-primary)';
        });
    });

    // Hero Play Butonu
    const heroBtn = document.getElementById('hero-play-btn');
    if (heroBtn) {
        heroBtn.addEventListener('click', function() {
            console.log('[Keşfet] Hero Parça Başlatıldı: ' + this.getAttribute('data-title'));
            this.innerHTML = '<span>Oynatılıyor...</span>';
            setTimeout(() => {
                this.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg><span>Şimdi Dinle</span>';
            }, 1500);
        });
    }
});
</script>
