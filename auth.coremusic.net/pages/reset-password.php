<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
?>
<section class="lgn-page" data-gender="<?=$genderEsc?>">
<div class="lgn-panel"><div class="lgn-panel__glass">
  <h2>Şifre Sıfırlama</h2>
  <div class="lgn-error" id="rp-err"></div>
  <form class="lgn-form" id="rp-form" method="post" action="/reset-password" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <input type="hidden" name="token" value="">
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="rp-pw">Yeni Şifre</label>
      <input class="lgn-form__input" type="password" id="rp-pw" name="password" placeholder="••••••••" required>
    </div>
    <button type="submit" class="lgn-btn">Şifremi Güncelle</button>
  </form>
</div></div>
</section>
