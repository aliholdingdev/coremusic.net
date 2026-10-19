<?php

declare(strict_types=1);

/**
 * Playlist — .adm-panel__title "Playlist".
 *
 * Görev D kapsamı: içerik "yapım aşamasında" boş-durumudur; uydurma
 * playlist verisi YOK.
 */

$pageTitle = 'Playlist';
$activeNav = 'admin-playlist';

ob_start();
?>
<section class="is-empty" aria-labelledby="playlist-empty-title">
    <h2 id="playlist-empty-title">Bu sayfa yapım aşamasındadır.</h2>
    <p>Playlist yönetimi arayüzü henüz tamamlanmamıştır; içerik yayınlandığında burada listelenecektir.</p>
</section>
<?php
$content = ob_get_clean();

require __DIR__ . '/layout.php';
