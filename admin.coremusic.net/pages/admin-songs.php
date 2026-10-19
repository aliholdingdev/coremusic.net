<?php

declare(strict_types=1);

/**
 * Şarkılar — .adm-panel__title "Şarkılar".
 *
 * Görev D kapsamı: içerik "yapım aşamasında" boş-durumudur; uydurma şarkı
 * listesi YOK (yalnız veritabanından gelen gerçek veri gösterilebilir —
 * bu sayfa henüz veri katmanına bağlanmadı).
 */

$pageTitle = 'Şarkılar';
$activeNav = 'admin-songs';

ob_start();
?>
<section class="is-empty" aria-labelledby="songs-empty-title">
    <h2 id="songs-empty-title">Bu sayfa yapım aşamasındadır.</h2>
    <p>Şarkı yönetimi arayüzü henüz tamamlanmamıştır; içerik yayınlandığında burada listelenecektir.</p>
</section>
<?php
$content = ob_get_clean();

require __DIR__ . '/layout.php';
