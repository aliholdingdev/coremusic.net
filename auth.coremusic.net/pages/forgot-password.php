<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
?>
<section class="lgn-page" data-gender="<?=$genderEsc?>">
<div class="lgn-panel"><div class="lgn-panel__glass">
  <h2>Şifremi Unuttum</h2>
  <div class="lgn-error" id="fp-err"></div>
  <form class="lgn-form" id="fp-form" method="post" action="/forgot-password" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="fp-email">E-posta adresinizi girin</label>
      <input class="lgn-form__input" type="email" id="fp-email" name="email" placeholder="ornek@email.com" required>
    </div>
    <button type="submit" class="lgn-btn">Sıfırlama Bağlantısı Gönder</button>
  </form>
</div></div>
</section>
