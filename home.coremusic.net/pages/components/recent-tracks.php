<?php declare(strict_types=1);
/**
 * pages/components/recent-tracks.php — Alt satır bölümleri (v4.0.0)
 *
 * home.php her varyansta 'recent-tracks''i TEK ÇAĞIRIR; bu view tek başına
 * mockup'taki TÜM alt-satır bölümlerini üretir:
 *
 *   Wide/4K (1920): 2 kardeş <section> — En Son (10 kart) + Playlistler (6 kart)
 *                   [Sıradaki bölüm mockup'ta YOK]
 *   Embedded (1024): .home-layout__bottom--embedded grid'inin 3 DOĞRUDAN çocuğu:
 *                   kolon1 En Son 2×2 (4 kart) · kolon2 Playlist 2×2 (3 kart +
 *                   "Playlist listesini görüntüle" link kartı) · kolon3
 *                   up-next-panel > 1 mini-card (186×109)
 *
 * Figma SSOT: extracted-1024.md 1639:9904 / 1639:9892(+9893) / 1639:9910
 *             extracted-1920.md 2856:22290 / 2850:21627 / 2890:7922-7927
 * CSS: c-home-song-btn.css · _home-components.css (playlist-list-card, up-next-panel, mini-card)
 * Yükleyen: CoreMusic\Home\Component\RecentTracksComponent
 */
?>
<?php if ($this->variant->isWide()): ?>
    <section class="home_song__btn-dashboard" aria-label="En Son Dinlenen Şarkılar" data-cm-component="cm-infinite-scroll">
        <h2 class="home_song__btn-title">En Son Dinlenen Şarkılar</h2>
        <div class="home_song__btn-card home_song__btn-card-grid home_song__btn-card-grid--scroll">
    <?php foreach ($this->cards as $card): ?>
            <?= $card ?>
    <?php endforeach; ?>
        </div>
    </section>
    <section class="home_song__btn-dashboard" aria-label="Son Oluşturlan Playlistler">
        <h2 class="home_song__btn-title">Son Oluşturlan &amp; Sistem Taraından Oluşturlan Playlistler</h2>
        <div class="home_song__btn-card home_song__btn-card-grid home_song__btn-card-grid--scroll">
    <?php foreach ($this->playlistCards as $card): ?>
            <?= $card ?>
    <?php endforeach; ?>
        </div>
    </section>
<?php else: ?>
    <!-- Kolon 1 — En Son (Figma 1639:9904, 2×2 = 4 kart) -->
    <section class="home_song__btn-dashboard home_song__btn-dashboard--embedded" aria-label="En Son Dinlenen Şarkılar">
        <h2 class="home_song__btn-title home_song__btn-title--embedded">En Son Dinlenen Şarkılar</h2>
        <div class="home_song__btn-card-grid home_song__btn-card-grid--embedded">
    <?php foreach ($this->cards as $card): ?>
            <?= $card ?>
    <?php endforeach; ?>
        </div>
    </section>
    <!-- Kolon 2 — Playlistler (Figma 1639:9892 + 1639:9893 link kartı, 2×2 = 3+1) -->
    <section class="home_song__btn-dashboard home_song__btn-dashboard--embedded" aria-label="Son Oluşturlan Playlistler">
        <h2 class="home_song__btn-title home_song__btn-title--embedded">Son Oluşturlan &amp; Sistem Taraından Oluşturlan Playlistler</h2>
        <div class="home_song__btn-card-grid home_song__btn-card-grid--embedded">
    <?php foreach ($this->playlistCards as $card): ?>
            <?= $card ?>
    <?php endforeach; ?>
            <a href="/playlist" class="playlist-list-card" data-no-spa>
                <img class="playlist-list-card__icon" src="<?= $this->h($this->playlistLinkIcon) ?>" alt="" width="10" height="10" loading="lazy"/>
                <span class="playlist-list-card__text">Playlist listesini görüntüle</span>
            </a>
        </div>
    </section>
    <!-- Kolon 3 — Sıradaki (Figma 1639:9910, cam panel 186×109) -->
    <section class="home_song__btn-dashboard home_song__btn-dashboard--embedded" aria-label="Sıradaki Şarkılar">
        <h2 class="home_song__btn-title home_song__btn-title--embedded">Sıradaki Şarkılar</h2>
    <?php if ($this->nextCard !== null): ?>
        <div class="up-next-panel">
            <a href="/playlist" class="mini-card" data-no-spa>
                <div class="mini-card__art"><img src="<?= $this->h($this->nextCard['art']) ?>" alt="" width="46" height="46" loading="lazy"/></div>
                <div class="mini-card__info">
                    <p class="mini-card__title"><?= $this->h($this->nextCard['t']) ?></p>
                    <p class="mini-card__album"><?= $this->h($this->nextCard['album']) ?></p>
                    <p class="mini-card__artist"><?= $this->h($this->nextCard['a']) ?></p>
                </div>
            </a>
        </div>
    <?php endif; ?>
    </section>
<?php endif; ?>
