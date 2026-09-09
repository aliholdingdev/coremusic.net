<?php declare(strict_types=1);
/**
 * pages/components/playlists.php — Bileşen: Playlister (v2)
 * PNG SSOT: .ai/.png/home-1920/ (tam genişlik, 6 kart) + .ai/.png/home-1024/ (bottom-center 2×2, 4 kart + liste butonu)
 * Yükleyen: CoreMusic\Home\Component\PlaylistsComponent
 * Erişim: $this->cards, $this->variant->isWide()
 */
?>
<?php if ($this->variant->isWide()): ?>
<section aria-label="Son Oluşturan ve Sistem Tarafından Oluşturulan Playlister">
    <h2 class="section-title">Son Oluşturan &amp; Sistem Tarafından Oluşturulan Playlister</h2>
    <div class="card-grid card-grid--scroll">
<?php foreach ($this->cards as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
    </div>
</section>
<?php else: ?>
<h2 class="section-title">Son Oluşturan &amp; Sistem Tarafından Oluşturulan Playlister</h2>
<div class="embedded-card-grid-2x2">
<?php foreach ($this->cards as $card): ?>
                <?= $card ?>
<?php endforeach; ?>
</div>
<button type="button" class="toggle-row" data-action="show-playlist-list">Playlister Listesini Görüntüle</button>
<?php endif; ?>
