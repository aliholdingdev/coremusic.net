<?php declare(strict_types=1);

/**
 * CoreMusic — Select Gender Page
 * Target: .ai/.png/shared-1024/Linux  1024 - Select Gender.png
 * Layout: Pattern 5 — Auth Screen (72/28 split)
 * Uses lgn-* class system from reference project
 */

$csrf      = htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
$gender    = $_SESSION['cm_gender'] ?? $_COOKIE['cm_gender'] ?? 'female';
$genderAttr = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
$redirectUri = $_GET['redirect_uri'] ?? '';
$clientId    = $_GET['client_id'] ?? 'coremusic-web';
$responseType = $_GET['response_type'] ?? 'session';
$cspNonce   = $_SESSION['csp_nonce'] ?? '';
$nonceEsc   = htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8');

// API tabanı (Faz 3a): backend sözleşmesi değişmez — config'te tanımlı değilse varsayılan.
// Sözleşme varsayılanı http://api.coremusic.net — sayfa HTTPS ise mixed-content engeli olmaması için scheme
// AUTH_URL/MUSIC_URL ile aynı kuralda (COREMUSIC_SCHEME, ortam http ise sonuç aynen http olur).
$apiUrl    = (defined('API_URL') && API_URL)
    ? API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? COREMUSIC_SCHEME : 'http') . '://api.coremusic.net';
$apiUrlEsc = htmlspecialchars(rtrim((string)$apiUrl, '/'), ENT_QUOTES, 'UTF-8');

// Session başlatılamadıysa set-gender POST'u CSRF reddiyle düşer — JS catch'i bunu loglar, kök neden burada loglanır.
if ($csrf === '') {
    error_log(json_encode(['level' => 'error', 'service' => 'auth.coremusic.net', 'message' => 'select-gender.php: csrf_token missing in session - POST /v1/auth/set-gender will be rejected']));
}
?>
<section class="lgn-page" data-gender="<?=$genderAttr?>">
<div class="lgn-bg" aria-hidden="true"></div>
<div class="lgn-particles" aria-hidden="true"><?php for($i=0;$i<8;$i++)echo'<div class="lgn-particle"></div>'; ?></div>

<!-- HERO (left 749px) -->
<div class="lgn-hero">
  <div class="lgn-hero__brand">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-img.png" alt="" width="30" height="30">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-text.png" alt="CoreMusic" class="logo-text-png">
  </div>
  <h1 class="lgn-hero__title"><em class="hero-title--sm">Seni</em><br><em>Tanıyalım</em></h1>
  <p class="lgn-hero__text">Deneyimini sana özel hale getirmek için bir seçim yapman yeterli.</p>

  <div class="lgn-hero__foot">
    <a href="/privacy" data-no-spa>Gizlilik</a><span class="dot"></span>
    <a href="/terms" data-no-spa>Kullanım Koşulları</a><span class="dot"></span>
    <a href="/support" data-no-spa>Destek</a><span class="dot"></span>
    <span>&copy; 2026 Coremusic</span>
  </div>
</div>

<!-- GLASS PANEL (right 275px, fixed dock) -->
<div class="lgn-panel"><div class="lgn-panel__glass">
  <div class="lgn-panel__inner-top">
    <div class="lgn-panel__avatar" aria-hidden="true">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/girl-1.png" alt="" width="100" height="100">
    </div>
    <div class="lgn-panel__head">
      <h2 class="lgn-panel__title">Seni Tanıyalım</h2>
      <p class="lgn-panel__sub">Müzik deneyimini sana özel hale getirelim</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <div class="lgn-error" id="lgn-err" role="alert" aria-live="polite"></div>
  <form id="gender-form" method="post" action="/set-gender" class="lgn-gender-form">
    <input type="hidden" name="csrf_token" value="<?=$csrf?>">
    <input type="hidden" name="client_id" value="<?=htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="response_type" value="<?=htmlspecialchars($responseType, ENT_QUOTES, 'UTF-8')?>">
    <?php if (!empty($redirectUri)): ?>
    <input type="hidden" name="redirect_uri" value="<?=htmlspecialchars($redirectUri, ENT_QUOTES, 'UTF-8')?>">
    <?php endif; ?>
    <input type="hidden" name="gender" id="gender-input" value="">

    <button type="button" class="lgn-gender-btn" data-gender="female" aria-label="Kız seçeneğini seç">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/kız-gender-select.png" alt="Kız" width="20" height="20"></span>
      <span class="lgn-gender-btn__info"><strong>Kız</strong><small>Romantik, soft vibes</small></span>
    </button>

    <button type="button" class="lgn-gender-btn" data-gender="male" aria-label="Erkek seçeneğini seç">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/erkek-gender-select.png" alt="Erkek" width="20" height="20"></span>
      <span class="lgn-gender-btn__info"><strong>Erkek</strong><small>Modern , cool vibes</small></span>
    </button>

    <button type="button" class="lgn-gender-btn" data-gender="neutral" aria-label="Cinsiyetimi söylemek istemiyorum">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/notur-gender-select.png" alt="Nötr" width="20" height="20"></span>
      <span class="lgn-gender-btn__info"><strong>Cinsiyetimi söylemek istemiyorum</strong><small>Genel, soft netural vibes</small></span>
    </button>

    <button type="submit" class="lgn-btn" id="continue-btn" disabled>Devam Et</button>
  </form>
  </div>
  <div class="lgn-panel__art" aria-hidden="true">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/banner/login-text.png" alt="" width="196" height="130" class="lgn-panel__poem-img">
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link">Devam ederek <a href="/privacy" data-no-spa>Gizlilik Politikası</a>'nı kabul etmiş olursunuz.</p>
  </div>
</div></div>
</section>
<!-- gender-select.js + auth-gender-bg.js loaded by HtmlShellRenderer -->
<script nonce="<?=$nonceEsc?>">
(function(){
    var form=document.getElementById('gender-form');
    if(!form) return;
    var input=document.getElementById('gender-input');
    var btn=document.getElementById('continue-btn');
    var errEl=document.getElementById('lgn-err');
    var API_URL='<?=$apiUrlEsc?>';
    function showError(msg){
        errEl.textContent=msg;
        errEl.classList.add('lgn-error--on');
    }
    function resetBtn(){btn.disabled=false;btn.textContent='Devam Et';}
    function focusFirstGender(){
        var first=document.querySelector('.lgn-gender-btn');
        if(first) first.focus();
    }
    /* HTTP koduna göre Türkçe mesaj — API sözleşmesi (Faz 3a) */
    function messageFor(status,d){
        var e=(d&&d.error)||{};
        if(status===422){
            var f=e.fields||null;
            if(f){var ks=Object.keys(f);if(ks.length){var parts=[];ks.forEach(function(k){parts.push(f[k]);});return{msg:parts.join(' '),first:ks[0]};}}
            return{msg:'Alan hatası.'};
        }
        switch(status){
            case 429:return{msg:'Çok fazla deneme. Lütfen bekleyin.'};
            case 500:return{msg:'Sistem hatası.'};
            case 503:return{msg:'Bağlantı hatası, tekrar deneyin.'};
        }
        return{msg:e.message||'Seçimin kaydedilemedi. Lütfen tekrar deneyin.'};
    }
    // gender-select.js de submit dinler; aynı sayfada iki POST atılmaması için bu dinleyici
    // kayıt sırası gereği önce çalışır ve stopImmediatePropagation ile dış modülü kapatır.
    form.addEventListener('submit',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var gender=input.value;
        if(!gender){
            showError('Lütfen bir cinsiyet seçin.');
            focusFirstGender();
            return;
        }
        btn.disabled=true;
        btn.textContent='Kaydediliyor...';
        errEl.classList.remove('lgn-error--on');
        try{localStorage.setItem('cm_gender',gender);}catch(ex){console.error('[auth] select-gender: localStorage write failed',ex);}
        var redirectUri=(new URLSearchParams(window.location.search)).get('redirect_uri')||'';
        fetch(API_URL+'/v1/auth/set-gender',{
            method:'POST',
            headers:{
                'Content-Type':'application/json',
                'X-Requested-With':'XMLHttpRequest',
                'X-CSRF-Token':document.querySelector('[name=csrf_token]').value
            },
            body:JSON.stringify({gender:gender}),
            credentials:'include'
        }).then(function(r){return r.json().catch(function(){return null;}).then(function(b){return{status:r.status,body:b};});})
        .then(function(res){
            var d=res.body||{};
            if(res.status>=200&&res.status<300){
                try{localStorage.setItem('cm_gender',gender);}catch(ex){console.error('[auth] select-gender: localStorage write failed',ex);if(errEl){showError('Tema tercihi bu tarayıcıda kaydedilemedi.');}}
                var data=d.data||d;
                var target=data.redirect||d.redirect;
                if(target){window.location.href=target;return;}
                window.location.href=redirectUri?('/login?redirect_uri='+encodeURIComponent(redirectUri)):'/login';
                return;
            }
            console.error('[auth] select-gender: api error',res.status,d);
            var m=messageFor(res.status,d);
            showError(m.msg);
            resetBtn();
            if(m.first==='gender'){focusFirstGender();}
        }).catch(function(cause){
            console.error('[auth] select-gender: request failed',cause);
            showError('Bağlantı hatası. Lütfen tekrar deneyin.');
            resetBtn();
        });
    });
})();
</script>
