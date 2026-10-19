<?php

declare(strict_types=1);

/**
 * Şarkı Ekle — .adm-panel__title "Şarkı Ekle".
 *
 * Görev D kapsamı: içerik "yapım aşamasında" boş-durumudur; figma'da bu
 * ekran için tasarım YOK — uydurma form alanı üretilmez (ZERO-HALLUCINATION).
 */

$pageTitle = 'Şarkı Ekle';
$activeNav = 'admin-song-add';

ob_start();
?>
<section class="is-empty" aria-labelledby="song-add-empty-title">
    <h2 id="song-add-empty-title">Bu sayfa yapım aşamasındadır.</h2>
    <p>Şarkı ekleme formu henüz tasarlanmamıştır; ekran onaylandığında burada yer alacaktır.</p>
</section>
<?php
$content = ob_get_clean();

require __DIR__ . '/layout.php';
