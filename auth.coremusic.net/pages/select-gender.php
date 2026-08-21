<?php declare(strict_types=1);

/**
 * CoreMusic — Select Gender Page
 * Target: .ai/.png/shared-1024/Linux  1024 - Select Gender.png
 * Layout: Pattern 5 — Auth Screen (72/28 split)
 * Uses lgn-* class system from reference project
 */

$csrf      = htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
$gender    = $_SESSION['cm_gender'] ?? 'female';
$genderAttr = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
$redirectUri = $_GET['redirect_uri'] ?? '';
$clientId    = $_GET['client_id'] ?? 'coremusic-web';
$responseType = $_GET['response_type'] ?? 'session';
?>
<section class="lgn-page" data-gender="<?=$genderAttr?>">
<div class="lgn-bg" aria-hidden="true"></div>
<div class="lgn-particles" aria-hidden="true"><?php for($i=0;$i<8;$i++)echo'<div class="lgn-particle"></div>'; ?></div>

<!-- HERO (left 78%) -->
<div class="lgn-hero">
  <div class="lgn-hero__brand">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-img.png" alt="" width="40" height="40">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-text.png" alt="CoreMusic" class="logo-text-png">
  </div>
  <h1 class="lgn-hero__title"><em class="hero-title--sm">Seni</em><br><em>Tanıyalım</em></h1>
  <p class="lgn-hero__text">Deneyimini sana özel hale getirmek için bir şarkı seçmeni yeterli.</p>

  <div class="lgn-hero__poem" aria-hidden="true">
    <span>Hayatın ritmini</span>
    <span><em>sende gizli</em></span>
    <span><em>Müziğinle parla!</em> ♥</span>
  </div>

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
      <h2 class="lgn-panel__title">Seni Tanıyalım</h2>
      <p class="lgn-panel__sub">Müzik deneyimini sana özel hale getirelim</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <div class="lgn-error" id="lgn-err"></div>
  <form id="gender-form" method="post" action="/set-gender" class="lgn-gender-form">
    <input type="hidden" name="csrf_token" value="<?=$csrf?>">
    <input type="hidden" name="client_id" value="<?=htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="response_type" value="<?=htmlspecialchars($responseType, ENT_QUOTES, 'UTF-8')?>">
    <?php if (!empty($redirectUri)): ?>
    <input type="hidden" name="redirect_uri" value="<?=htmlspecialchars($redirectUri, ENT_QUOTES, 'UTF-8')?>">
    <?php endif; ?>
    <input type="hidden" name="gender" id="gender-input" value="">

    <button type="button" class="lgn-gender-btn" data-gender="female" aria-label="Kız seçeneğini seç">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/kız-gender-select.png" alt="Kız" width="28" height="28"></span>
      <span class="lgn-gender-btn__info"><strong>Kız</strong><small>Romantik ruh hali</small></span>
      <span class="lgn-gender-btn__check" aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
    </button>

    <button type="button" class="lgn-gender-btn" data-gender="male" aria-label="Erkek seçeneğini seç">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/erkek-gender-select.png" alt="Erkek" width="28" height="28"></span>
      <span class="lgn-gender-btn__info"><strong>Erkek</strong><small>Modern tarz öner</small></span>
      <span class="lgn-gender-btn__check" aria-hidden="true"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span>
    </button>

    <button type="submit" class="lgn-btn" id="continue-btn" disabled>Devam Et</button>
  </form>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link">Devam ederek <a href="/privacy" data-no-spa>Gizlilik Politikası</a>'nı kabul etmiş olursunuz.</p>
  </div>
</div></div>
</section>
<!-- gender-select.js + auth-gender-bg.js loaded by HtmlShellRenderer -->
