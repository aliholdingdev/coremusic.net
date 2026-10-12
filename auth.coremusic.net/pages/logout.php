<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
$nonceEsc      = htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8');

// API tabanı (Faz 3a): backend sözleşmesi değişmez — config'te tanımlı değilse varsayılan.
// Sözleşme varsayılanı http://api.coremusic.net — sayfa HTTPS ise mixed-content engeli olmaması için scheme
// AUTH_URL/MUSIC_URL ile aynı kuralda (COREMUSIC_SCHEME, ortam http ise sonuç aynen http olur).
$apiUrl    = (defined('API_URL') && API_URL)
    ? API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? COREMUSIC_SCHEME : 'http') . '://api.coremusic.net';
$apiUrlEsc = htmlspecialchars(rtrim((string)$apiUrl, '/'), ENT_QUOTES, 'UTF-8');

// Session başlatılamadıysa logout POST'u CSRF reddiyle düşer — JS catch'i bunu loglar, kök neden burada loglanır.
if ($csrfTokenEsc === '') {
    error_log(json_encode(['level' => 'error', 'service' => 'auth.coremusic.net', 'message' => 'logout.php: csrf_token missing in session - POST /logout will be rejected']));
}
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
  <h1 class="lgn-hero__title">Görüşürüz<br><em>tekrar!</em></h1>
  <p class="lgn-hero__text">Oturumun güvenli bir şekilde kapatıldı. Müzik her zaman seninle.</p>
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
      <h2 class="lgn-panel__title">Çıkış Yapıldı</h2>
      <p class="lgn-panel__sub">Başarıyla çıkış yaptınız</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <div class="lgn-error" id="lgn-err" role="alert" aria-live="polite"></div>
    <p style="text-align:center;margin-bottom:16px;">Oturumunuz kapatıldı. Tekrar hoş geldiniz!</p>
    <a href="/login" class="lgn-btn" style="display:block;text-align:center;text-decoration:none;">Tekrar Giriş Yap</a>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link">Hesabın yok mu? <a href="/register" data-no-spa>Kayıt Ol</a></p>
  </div>
</div></div>
</section>
<script nonce="<?=$nonceEsc?>">
(function(){
    const showLogoutError = function(message){
        const el = document.getElementById('lgn-err');
        if (el) { el.textContent = message; el.classList.add('lgn-error--on'); }
    };
    var API_URL='<?=$apiUrlEsc?>';
    /* HTTP koduna göre Türkçe mesaj — API sözleşmesi (Faz 3a) */
    const logoutMessage = function(status, d){
        const e = (d && d.error) || {};
        switch (status) {
            case 429: return 'Çok fazla deneme. Lütfen bekleyin.';
            case 500: return 'Sistem hatası.';
            case 503: return 'Bağlantı hatası, tekrar deneyin.';
        }
        return e.message || 'Çıkış işlemi tamamlanamadı. Lütfen tekrar deneyin.';
    };
    fetch(API_URL+'/v1/auth/logout',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':'<?= $csrfTokenEsc ?>'},credentials:'include'})
    .then(function(r){return r.json().catch(function(){return null;}).then(function(b){return{status:r.status,body:b};});})
    .then(function(res){
        const d = res.body || {};
        const data = (d && d.data) || d || {};
        const target = data.redirect || (d && d.redirect);
        if (res.status >= 200 && res.status < 300) {
            if (target) { window.location.href = target; return; }
            console.error('[auth] logout: unexpected response', d);
            showLogoutError('Çıkış işlemi tamamlanamadı. Lütfen tekrar deneyin.');
            return;
        }
        // 401: oturum zaten kapatılmış — kullanıcıyı giriş sayfasına götür.
        if (res.status === 401) { window.location.href = '/login'; return; }
        console.error('[auth] logout: api error', res.status, d);
        showLogoutError(logoutMessage(res.status, d));
    }).catch(function(cause){
        console.error('[auth] logout: request failed',cause);
        showLogoutError('Çıkış işlemi tamamlanamadı. Lütfen tekrar deneyin.');
    });
})();
</script>
