<?php declare(strict_types=1);
/**
 * pages/components/welcome-modal.php — Bileşen: Welcome Modal (v2, PNG S02: qr-welcome-popup)
 * PNG'de × close butonu YOK — kapanış: Başla butonu / Escape.
 * Görünürlük JS'e aittir (welcome-modal.js + device-layout-updater.js);
 * markup phone hariç tüm cihazlarda render edilir.
 * Yükleyen: CoreMusic\Home\Component\WelcomeModalComponent
 * Erişim: $this->{logoImg, username}
 */
?>
<div class="welcome-modal-overlay is-hidden" id="welcomeModalOverlay" role="dialog" aria-modal="true" aria-labelledby="welcomeModalTitle" aria-describedby="welcomeModalDesc">
    <div class="welcome-modal">
        <div class="welcome-modal__emblem">
            <img class="welcome-modal__swirl-img" src="<?= $this->h($this->logoImg) ?>" alt="" width="42" height="42" loading="lazy"/>
            <span class="welcome-modal__logo-brand"><span class="welcome-modal__brand-core">Core</span><span class="welcome-modal__brand-music">Music</span></span>
        </div>
        <h2 class="welcome-modal__title" id="welcomeModalTitle">Hoş geldin</h2>
        <input class="welcome-modal__input" type="text" name="welcome_name" value="<?= $this->username ?>" placeholder="İsminizi Girin Buraya" autocomplete="given-name" aria-label="İsminizi girin"/>
        <p class="welcome-modal__desc" id="welcomeModalDesc">Sana özel seçimler, müzik deneyimlerini ve sunumları tamamen sana özel hale getirir. CoreMusic ile rüyalarındaki müziğin keyfine dal <span class="heart-purple" aria-hidden="true">💜</span></p>
        <button type="button" class="welcome-modal__btn">Başla</button>
    </div>
</div>
