<?php declare(strict_types=1);
/**
 * pages/components/recent-tracks.php — Bileşen: En Son Dinlenen Şarkılar (v3.0.0)
 *
 * Figma pixel-perfect:
 *   Embedded (1024, node 1639:9904): "En Son" kolonu — 2×2 grid, 169×43px mini cards, 4 kart
 *   Desktop (1920): Horizontal scroll with wider cards
 *
 * PNG SSOT: .ai/.png/home-1920/ (tam genişlik, 9 kart) + .ai/.png/home-1024/ (bottom-left 2×2, 4 kart)
 * Figma SSOT: .ai/ui-design/reference/figma/raw/nodes-1024-1920.json → node 1639:10160
 *   "Menu En Son Şarkılar" (1639:9904): başlık + 4× "Şarkılar Item Btn" (2×2)
 * Yükleyen: CoreMusic\Home\Component\RecentTracksComponent
 * Erişim: $this->cards, $this->variant->isWide()
 *
 * v3.1.0: Embedded (1024) 3-kolon alt satırın "En Son" kolonu için 2×2 grid şablonu eklendi.
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
<?php else: ?>
    <section class="home_song__btn-dashboard home_song__btn-dashboard--embedded" aria-label="En Son Dinlenen Şarkılar">
        <h2 class="home_song__btn-title home_song__btn-title--embedded">En Son Dinlenen Şarkılar</h2>
        <div class="home_song__btn-card-grid home_song__btn-card-grid--embedded">
    <?php foreach ($this->cards as $card): ?>
                    <?= $card ?>
    <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
