<?php declare(strict_types=1);
/**
 * pages/components/recent-tracks.php — Bileşen: En Son Dinlenen Şarkılar (v2)
 * PNG SSOT: .ai/.png/home-1920/ (tam genişlik, 9 kart) + .ai/.png/home-1024/ (bottom-left 2×2, 4 kart)
 * Yükleyen: CoreMusic\Home\Component\RecentTracksComponent
 * Erişim: $this->cards, $this->variant->isWide()
 */
?>
<?php if ($this->variant->isWide()): ?>
<section aria-label="En Son Dinlenen Şarkılar">
    <h2 class="section-title">En Son Dinlenen Şarkılar</h2>
    <div class="card-grid card-grid--scroll">
<?php foreach ($this->cards as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
    </div>
</section>
<?php else: ?>
<h2 class="section-title">En Son Dinlenen Şarkılar</h2>
<div class="embedded-card-grid-2x2">
<?php foreach ($this->cards as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
</div>
<?php endif; ?>
