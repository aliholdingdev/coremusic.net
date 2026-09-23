<?php declare(strict_types=1);
/**
 * pages/components/recent-tracks.php — Bileşen: En Son Dinlenen Şarkılar (v3.0.0)
 *
 * Figma pixel-perfect:
 *   Embedded (1024): 2-column grid with 169×43px mini cards
 *   Desktop (1920): Horizontal scroll with wider cards
 *
 * PNG SSOT: .ai/.png/home-1920/ (tam genişlik, 9 kart) + .ai/.png/home-1024/ (bottom-left 2×2, 4 kart)
 * Yükleyen: CoreMusic\Home\Component\RecentTracksComponent
 * Erişim: $this->cards, $this->variant->isWide()
 *
 * v3.0.0: Figma-based CSS classes (.home-mini-card-grid, .home-mini-card)
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
  
<?php endif; ?>
