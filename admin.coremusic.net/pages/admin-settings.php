<?php

declare(strict_types=1);

/**
 * Ayarlar — .adm-panel__title "Ayarlar".
 *
 * Görev D kapsamı: içerik "yapım aşamasında" boş-durumudur; uydurma
 * ayar listesi YOK.
 */

$pageTitle = 'Ayarlar';
$activeNav = 'admin-settings';

ob_start();
?>
<section class="is-empty" aria-labelledby="settings-empty-title">
    <h2 id="settings-empty-title">Bu sayfa yapım aşamasındadır.</h2>
    <p>Sistem ayarları arayüzü henüz tamamlanmamıştır; ekran onaylandığında burada yer alacaktır.</p>
</section>
<?php
$content = ob_get_clean();

require __DIR__ . '/layout.php';
