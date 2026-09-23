<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
?>
<section class="lgn-page" data-gender="<?=$genderEsc?>">
<div class="lgn-bg" aria-hidden="true"></div>
<div class="lgn-particles" aria-hidden="true"><?php for($i=0;$i<8;$i++)echo'<div class="lgn-particle"></div>'; ?></div>

<!-- HERO (left 78%) -->
<div class="lgn-hero">
  <div class="lgn-hero__brand">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-img.png" alt="" width="40" height="40">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-text.png" alt="CoreMusic" class="logo-text-png">
  </div>
  <h1 class="lgn-hero__title">Şifreni<br><em>sıfırla</em></h1>
  <p class="lgn-hero__text">E-posta adresini gir, sana sıfırlama bağlantısı gönderelim.</p>
  <div class="lgn-hero__foot">
    <a href="/privacy" data-no-spa>Gizlilik</a><span class="dot"></span>
    <a href="/about" data-no-spa>Hakkımızda</a><span class="dot"></span>
    <a href="/help" data-no-spa>Yardım</a><span class="dot"></span>
    <a href="/faq" data-no-spa>SSS</a>
  </div>
</div>

<!-- GLASS PANEL (right 22%, fixed) -->
<div class="lgn-panel"><div class="lgn-panel__glass">
  <div class="lgn-panel__inner-top">
    <div class="lgn-panel__avatar" aria-hidden="true">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/kız-gender-select.png" alt="" width="64" height="64">
    </div>
    <div class="lgn-panel__head">
      <h2 class="lgn-panel__title">Şifremi Unuttum</h2>
      <p class="lgn-panel__sub">E-posta adresinizi girin</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <div class="lgn-error" id="fp-err"></div>
  <form class="lgn-form" id="fp-form" method="post" action="/forgot-password" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="fp-email">E-posta adresinizi girin</label>
      <input class="lgn-form__input" type="email" id="fp-email" name="email" placeholder="ornek@email.com" required>
    </div>
    <button type="submit" class="lgn-btn">Sıfırlama Bağlantısı Gönder</button>
  </form>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link"><a href="/login" data-no-spa>Giriş Yap'a Dön</a></p>
  </div>
</div></div>
</section>
