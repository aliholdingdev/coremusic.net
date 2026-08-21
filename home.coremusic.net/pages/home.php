<?php declare(strict_types=1);
/**
 * CoreMusic Home — Ana Sayfa
 * Layout: Split Home (42/58) — Sol: Now Playing + kartlar / Sağ: Widget'lar
 * Design: 02-home-screens.md, 01-component-inventory.md
 * Components: C09 (Media Card), Now Playing Card, Widget Area, Mini Card
 * Version: 2.0.0 — 2026-08-21
 */

$username = htmlspecialchars($_SESSION['MM_Username'] ?? '', ENT_QUOTES, 'UTF-8');
$email    = htmlspecialchars($_SESSION['MM_Email'] ?? '', ENT_QUOTES, 'UTF-8');
$gender   = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['MM_Gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$isAuth   = $username !== '';

$currentSong   = htmlspecialchars($_SESSION['current_song'] ?? 'Sevil Neşelen', ENT_QUOTES, 'UTF-8');
$currentAlbum  = htmlspecialchars($_SESSION['current_album'] ?? 'Hayat Rüya Gibi', ENT_QUOTES, 'UTF-8');
$currentArtist = htmlspecialchars($_SESSION['current_artist'] ?? 'Göksel', ENT_QUOTES, 'UTF-8');
$currentArt    = $_SESSION['current_art'] ?? '/assets.coremusic.net/Image/res-pink/default-album.png';

require __DIR__ . '/../header.php';
?>
<main class="page-home home-layout" role="main" aria-label="Ana Sayfa">
    <!-- SOL PANEL — %42: Now Playing + Kartlar -->
    <div class="home-layout__left">

        <!-- Now Playing Card -->
        <div class="now-playing" aria-label="Şu an çalınan şarkı">
            <div class="now-playing__art">
                <img src="<?= $currentArt ?>" alt="<?= $currentSong ?> albüm kapağı" width="100" height="100" loading="lazy">
            </div>
            <div class="now-playing__info">
                <p class="now-playing__title"><?= $currentSong ?></p>
                <p class="now-playing__subtitle"><?= $currentAlbum ?></p>
                <p class="now-playing__artist"><?= $currentArtist ?></p>
                <div class="now-playing__seek">
                    <div class="now-playing__seek-fill" style="width:50%;"></div>
                </div>
                <div class="now-playing__times">
                    <span>00:05:00</span>
                    <span>00:05:00</span>
                </div>
            </div>
        </div>

        <!-- En Son Dinlenen -->
        <section aria-label="En Son Dinlenen Şarkılar">
            <h2 class="section-title">En Son Dinlenen Şarkılar</h2>
            <div class="card-grid card-grid--scroll" role="list">
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Göksel - Sevil Neşelen</p>
                    <p class="media-card__meta">Göksel</p>
                    <p class="media-card__duration">00:05:05</p>
                </article>
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Göksel - Kadınım Je Suis</p>
                    <p class="media-card__meta">Göksel</p>
                    <p class="media-card__duration">00:07:05</p>
                </article>
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Barış Manço - Dönence</p>
                    <p class="media-card__meta">Barış Manço</p>
                    <p class="media-card__duration">00:05:05</p>
                </article>
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Erkin Koray - Çöpçüler</p>
                    <p class="media-card__meta">Erkin Koray</p>
                    <p class="media-card__duration">00:09:05</p>
                </article>
            </div>
        </section>

        <!-- Oynatma Listeleri -->
        <section aria-label="Oynatma Listeleri">
            <h2 class="section-title">Oynatma Listeleri</h2>
            <div class="card-grid card-grid--scroll" role="list">
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">En Sevilen Şarkılar</p>
                    <p class="media-card__meta">Göksel</p>
                    <p class="media-card__duration">00:05:05</p>
                </article>
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Müzikal Enstrümanlar</p>
                    <p class="media-card__meta">Orçun Benli</p>
                    <p class="media-card__duration">00:05:05</p>
                </article>
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Popüler Arap Şarkıları</p>
                    <p class="media-card__meta">Türlüden Seçmeler</p>
                    <p class="media-card__duration">00:05:05</p>
                </article>
                <article class="media-card" role="listitem">
                    <div class="media-card__thumb"><img src="<?= $currentArt ?>" alt="" width="140" height="140" loading="lazy"></div>
                    <p class="media-card__title">Son Oluşturulan Listeler</p>
                    <p class="media-card__meta">Sistem</p>
                    <p class="media-card__duration">00:05:05</p>
                </article>
            </div>
            <label class="toggle-row">
                <input type="checkbox" class="toggle" aria-label="Oynatma listesini göster">
                <span>Oynatma listesini göster</span>
            </label>
        </section>

        <!-- Sıradaki Şarkılar + Mini Card -->
        <section aria-label="Sıradaki Şarkılar">
            <h2 class="section-title">Sıradaki Şarkılar</h2>
            <div class="mini-card" aria-label="Şu an çalınan şarkı kartı">
                <div class="mini-card__art">
                    <img src="<?= $currentArt ?>" alt="" width="50" height="50" loading="lazy">
                </div>
                <div class="mini-card__info">
                    <p class="mini-card__title"><?= $currentSong ?></p>
                    <p class="mini-card__artist"><?= $currentArtist ?></p>
                    <p class="mini-card__album"><?= $currentAlbum ?></p>
                </div>
            </div>
        </section>
    </div>

    <!-- SAĞ PANEL — %58: Widget'lar -->
    <div class="home-layout__right">

        <!-- Hoparlörler Widget -->
        <div class="home-widget">
            <div class="home-widget__header">
                <span class="home-widget__icon" aria-hidden="true">&#9835;</span>
                <h3 class="home-widget__title">Hoparlörler</h3>
            </div>
            <p class="home-widget__subtitle">Core Music - Hoparlör</p>
            <div class="home-widget__glass">
                <p class="home-widget__info">Bağlı hoparlör bulunamadı</p>
            </div>
        </div>

        <!-- Hava Durumu Widget -->
        <div class="home-widget">
            <div class="home-widget__header">
                <span class="home-widget__icon" aria-hidden="true">&#9729;</span>
                <h3 class="home-widget__title">Hava Durumu</h3>
            </div>
            <p class="home-widget__subtitle">İzmir, TR</p>
            <div class="home-widget__glass">
                <p class="home-widget__info">--°C</p>
            </div>
        </div>

        <!-- Tarih Widget -->
        <div class="home-widget">
            <div class="home-widget__header">
                <span class="home-widget__icon" aria-hidden="true">&#9787;</span>
                <h3 class="home-widget__title">07:00</h3>
            </div>
            <p class="home-widget__subtitle">5 Ağustos 2026</p>
            <div class="home-widget__glass">
                <p class="home-widget__info"></p>
            </div>
        </div>

        <!-- Klasörlerim Widget -->
        <div class="home-widget">
            <div class="home-widget__header">
                <span class="home-widget__icon" aria-hidden="true">&#128193;</span>
                <h3 class="home-widget__title">Klasörlerim</h3>
            </div>
            <div class="home-widget__folders">
                <button class="home-widget__folder-btn" aria-label="Oynat">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                </button>
                <button class="home-widget__folder-btn" aria-label="YouTube">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#FF0000"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2 31.4 31.4 0 0 0 0 12a31.4 31.4 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1A31.4 31.4 0 0 0 24 12a31.4 31.4 0 0 0-.5-5.8zM9.5 15.5V8.5l6.4 3.5-6.4 3.5z"/></svg>
                </button>
                <button class="home-widget__folder-btn" aria-label="Beğeni">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#FF69B4"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </button>
                <button class="home-widget__folder-btn" aria-label="Müzik">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#9B59B6"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg>
                </button>
                <button class="home-widget__folder-btn" aria-label="Beğeni 2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#E91E63"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </button>
            </div>
            <div class="home-widget__glass">
                <p class="home-widget__info"></p>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../footer.php'; ?>
