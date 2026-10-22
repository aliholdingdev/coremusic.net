<?php declare(strict_types=1);

/**
 * CoreMusic — Login Page
 * Target: .ai/.png/shared-1024/Linux  1024 - Login Girl.png
 * Layout: Pattern 5 — Auth Screen (72/28 split)
 * Social buttons: PNG mockup set (Apple, Google, Facebook, WhatsApp, Instagram, TikTok, Mikrofon)
 */

$csrf      = htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
$nonce     = htmlspecialchars((string)($_SESSION['csp_nonce'] ?? ''), ENT_QUOTES, 'UTF-8');
$gender    = $_SESSION['cm_gender'] ?? $_COOKIE['cm_gender'] ?? 'neutral';
$genderAttr = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
$redirectUri = $_GET['redirect_uri'] ?? '';
$clientId    = $_GET['client_id'] ?? 'coremusic-web';
$responseType = $_GET['response_type'] ?? 'session';

// API tabanı (Faz 3a): backend sözleşmesi değişmez — config'te tanımlı değilse varsayılan.
// Sözleşme varsayılanı http://api.coremusic.net — sayfa HTTPS ise mixed-content engeli olmaması için scheme
// AUTH_URL/MUSIC_URL ile aynı kuralda (COREMUSIC_SCHEME, ortam http ise sonuç aynen http olur).
$apiUrl    = (defined('API_URL') && API_URL)
    ? API_URL
    : ((defined('COREMUSIC_SCHEME') && COREMUSIC_SCHEME) ? COREMUSIC_SCHEME : 'http') . '://api.coremusic.net';
$apiUrlEsc = htmlspecialchars(rtrim((string)$apiUrl, '/'), ENT_QUOTES, 'UTF-8');

// Session başlatılamadıysa login POST'u CSRF reddiyle düşer — JS catch'i "Bağlantı hatası" gösterir, kök neden loglanır.
if ($csrf === '') {
    error_log(json_encode(['level' => 'error', 'service' => 'auth.coremusic.net', 'message' => 'login.php: csrf_token missing in session - POST /login will be rejected']));
}
?>
<section class="lgn-page" data-gender="<?=$genderAttr?>">
<script nonce="<?=$nonce?>">(function(){var s=document.currentScript.parentElement;if(s.dataset.gender==='neutral'){try{var g=localStorage.getItem('cm_gender');if(g&&['female','male','neutral'].indexOf(g)!==-1)s.dataset.gender=g;}catch(e){console.error('[auth] login: localStorage gender read failed',e);document.addEventListener('DOMContentLoaded',function(){const el=document.getElementById('lgn-err');if(el){el.textContent='Tema tercihi yüklenemedi, varsayılan tema kullanılıyor.';el.classList.add('lgn-error--on');}});}}})();</script>
<div class="lgn-bg" aria-hidden="true"></div>
<div class="lgn-particles" aria-hidden="true"><?php for($i=0;$i<8;$i++)echo'<div class="lgn-particle"></div>'; ?></div>

<!-- HERO (left 749px) -->
<div class="lgn-hero">
  <div class="lgn-hero__brand">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-img.png" alt="" width="30" height="30" class="lgn-hero__logo-img">
    <a href="/home" class="site-header__logo" aria-label="CoreMusic" data-no-spa>
      <span class="logo-core">Core</span> <span class="logo-music">Music</span>
    </a>
  </div>
  <h1 class="lgn-hero__title"><em class="hero-title--sm">Duygu için</em><br><em>mükemmel</em></h1>
  <p class="lgn-hero__text">sistem. Milyonlarca şarkı, özel seçilmiş playlist'ler, sonsuz müzik keşfi. Senin için.</p>
  <div class="lgn-hero__foot">
    <a href="/privacy" data-no-spa>Gizlilik</a><span class="dot"></span>
    <a href="/terms" data-no-spa>Kullanım Koşulları</a><span class="dot"></span>
    <a href="/support" data-no-spa>Destek</a><span class="dot"></span>
    <span>&copy; 2026 Coremusic</span>
  </div>
</div>

<!-- GLASS PANEL (right 22%, fixed) -->
<div class="lgn-panel"><div class="lgn-panel__glass">
  <div class="lgn-panel__inner-top">
    <div class="lgn-panel__avatar" aria-hidden="true">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/girl-1.png" alt="" width="100" height="100">
    </div>
    <div class="lgn-panel__head">
      <h2 class="lgn-panel__title">Hoş Geldin</h2>
      <p class="lgn-panel__sub">Hesabına giriş yap, müziğin keyfini çıkar</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <div class="lgn-error" id="lgn-err" role="alert" aria-live="polite"></div>
  <form class="lgn-form" id="lgn-form" method="post" action="/login" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrf?>">
    <input type="hidden" name="client_id" value="<?=htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="response_type" value="<?=htmlspecialchars($responseType, ENT_QUOTES, 'UTF-8')?>">
    <?php if (!empty($redirectUri)): ?>
    <input type="hidden" name="redirect_uri" value="<?=htmlspecialchars($redirectUri, ENT_QUOTES, 'UTF-8')?>">
    <?php endif; ?>
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="lgn-id">E-posta, Telefon veya Kullanıcı Adı</label>
      <input class="lgn-form__input" type="text" id="lgn-id" name="email" placeholder="ornek@mail.com" autocomplete="username" required>
    </div>
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="lgn-pw">Şifre</label>
      <div class="lgn-form__pw-wrap">
        <input class="lgn-form__input" type="password" id="lgn-pw" name="password" placeholder="Şifreniz" autocomplete="current-password" required>
        <button type="button" class="lgn-form__pw-eye" aria-label="Şifreyi göster"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11 7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
      </div>
    </div>
    <div class="lgn-form__row">
      <label class="lgn-form__check"><input type="checkbox" name="remember" value="1" checked><span class="lgn-form__check-box"></span><span class="lgn-form__check-text">Beni Hatırla</span></label>
      <a href="/forgot-password" class="lgn-form__link" data-no-spa>Şifremi Unuttum</a>
    </div>
    <button type="submit" class="lgn-btn" id="lgn-submit">Giriş Yap</button>
  </form>
  </div>
  <div class="lgn-panel__inner-social">
  <div class="lgn-divider"><span>veya şununla devam et</span></div>
  <div class="lgn-social">
    <!-- PNG mockup social buttons (3x3 grid) -->
    <button type="button" class="lgn-social__btn lgn-social__btn--apple" data-provider="apple" aria-label="Apple ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/apple.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--google" data-provider="google" aria-label="Google ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/google.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--facebook" data-provider="facebook" aria-label="Facebook ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/facebook.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <!-- Row 2 -->
    <button type="button" class="lgn-social__btn lgn-social__btn--spotify" data-provider="spotify" aria-label="Spotify ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/spotify.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--instagram" data-provider="instagram" aria-label="Instagram ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/instagram.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--tiktok" data-provider="tiktok" aria-label="TikTok ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/tik-tok.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <!-- Row 3 -->
    <button type="button" class="lgn-social__btn lgn-social__btn--github" data-provider="github" aria-label="GitHub ile giriş">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/github.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
  </div>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link">Hesabın yok mu? <a href="/register" data-no-spa>Kayıt Ol</a></p>
  </div>
</div></div>
</section>
<script nonce="<?=$nonce?>">
(function(){
    var form=document.getElementById('lgn-form');
    var btn=document.getElementById('lgn-submit');
    var errEl=document.getElementById('lgn-err');
    var eyeBtn=document.querySelector('.lgn-form__pw-eye');
    var pwInput=document.getElementById('lgn-pw');
    if(eyeBtn&&pwInput){eyeBtn.addEventListener('click',function(){var t=pwInput.type==='password'?'text':'password';pwInput.type=t;eyeBtn.setAttribute('aria-label',t==='password'?'Şifreyi göster':'Şifreyi gizle');});}
    var API_URL='<?=$apiUrlEsc?>';
    var FIELD_FOCUS={email:'lgn-id',username:'lgn-id',password:'lgn-pw'};
    function resetBtn(){btn.disabled=false;btn.classList.remove('lgn-btn--loading');btn.textContent='Giriş Yap';}
    function showError(msg,focusField){
        errEl.textContent=msg;errEl.classList.add('lgn-error--on');
        if(focusField&&FIELD_FOCUS[focusField]){var el=document.getElementById(FIELD_FOCUS[focusField]);if(el)el.focus();}
    }
    /* HTTP koduna göre Türkçe mesaj — API sözleşmesi (Faz 3a) */
    function messageFor(status,d){
        var e=(d&&d.error)||{};
        var f=e.fields||null;
        if(status===422){
            if(f){var ks=Object.keys(f);if(ks.length){var parts=[];ks.forEach(function(k){parts.push(f[k]);});return{msg:parts.join(' '),first:ks[0]};}}
            return{msg:'Alan hatası.'};
        }
        switch(status){
            case 400:return{msg:'Link geçersiz veya süresi dolmuş.'};
            case 401:return{msg:'E-posta veya şifre hatalı.'};
            case 403:return{msg:'Bu hesap giriş yapamaz.'};
            case 409:return{msg:e.code==='USERNAME_TAKEN'?'Bu kullanıcı adı kullanılıyor.':'Bu e-posta kullanılıyor.'};
            case 429:return{msg:'Çok fazla deneme. Lütfen bekleyin.'};
            case 500:return{msg:'Sistem hatası.'};
            case 503:return{msg:'Bağlantı hatası, tekrar deneyin.'};
        }
        return{msg:e.message||(d&&d.message)||'Giriş başarısız.'};
    }
    var urlParams=new URLSearchParams(window.location.search);
    var redirectUri=urlParams.get('redirect_uri')||'';
    form.addEventListener('submit',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        btn.disabled=true;btn.classList.add('lgn-btn--loading');btn.textContent='Giriş yapılıyor...';errEl.classList.remove('lgn-error--on');
        var postUrl=API_URL+'/v1/auth/login';
        if(redirectUri) postUrl+='?redirect_uri='+encodeURIComponent(redirectUri);
        fetch(postUrl,{
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':document.querySelector('[name=csrf_token]').value},
            body:JSON.stringify({email:document.getElementById('lgn-id').value,password:pwInput.value,redirect_uri:redirectUri}),
            credentials:'include'
        }).then(function(r){return r.json().catch(function(){return null;}).then(function(b){return{status:r.status,body:b};});})
        .then(function(res){
            var d=res.body||{};
            if(res.status>=200&&res.status<300){
                var data=d.data||d;
                var target=data.redirect||d.redirect;
                if(target){window.location.href=target;return;}
                showError((d.error&&d.error.message)||d.message||'Giriş başarısız.');
                resetBtn();
                return;
            }
            console.error('[auth] login: api error',res.status,d);
            var m=messageFor(res.status,d);
            showError(m.msg,m.first);
            resetBtn();
            if(d.csrf_token){var ci=document.querySelector('[name=csrf_token]');if(ci)ci.value=d.csrf_token;}
        }).catch(function(cause){
            console.error('[auth] login: request failed',cause);
            showError('Bağlantı hatası. Lütfen tekrar deneyin.');
            resetBtn();
        });
    });
})();
</script>
