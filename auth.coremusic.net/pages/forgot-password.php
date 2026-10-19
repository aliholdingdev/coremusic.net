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

// Session başlatılamadıysa forgot-password POST'u CSRF reddiyle düşer — JS catch'i bunu loglar, kök neden burada loglanır.
if ($csrfTokenEsc === '') {
    error_log(json_encode(['level' => 'error', 'service' => 'auth.coremusic.net', 'message' => 'forgot-password.php: csrf_token missing in session - POST /v1/auth/forgot-password will be rejected']));
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
  <div class="form-message" id="fp-err" role="alert" aria-live="polite"></div>
  <form class="lgn-form" id="fp-form" method="post" action="/forgot-password" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="fp-email">E-posta adresinizi girin</label>
      <input class="lgn-form__input" type="email" id="fp-email" name="email" placeholder="ornek@email.com" required>
    </div>
    <button type="submit" class="lgn-btn" id="fp-submit">Sıfırlama Bağlantısı Gönder</button>
  </form>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link"><a href="/login" data-no-spa>Giriş Yap'a Dön</a></p>
  </div>
</div></div>
</section>
<script nonce="<?=$nonceEsc?>">
(function(){
    var form=document.getElementById('fp-form');
    var btn=document.getElementById('fp-submit');
    var emailInput=document.getElementById('fp-email');
    var msgEl=document.getElementById('fp-err');
    var API_URL='<?=$apiUrlEsc?>';
    var SUCCESS_TEXT='Sıfırlama bağlantısı gönderildi.';
    function showMsg(text,ok){
        msgEl.className='form-message '+(ok?'form-message--success':'form-message--error');
        msgEl.textContent=text;
    }
    function resetBtn(){btn.disabled=false;btn.classList.remove('lgn-btn--loading');btn.textContent='Sıfırlama Bağlantısı Gönder';}
    /* HTTP koduna göre Türkçe mesaj — API sözleşmesi (Faz 3a).
       200 her zaman AYNI metindir: enumeration koruması (e-posta kayıtlı olmasa da aynı yanıt). */
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
        return{msg:e.message||'İşlem tamamlanamadı. Lütfen tekrar deneyin.'};
    }
    form.addEventListener('submit',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var email=emailInput.value.trim();
        if(!email||email.indexOf('@')===-1){
            showMsg('Geçerli bir e-posta adresi girin.',false);
            emailInput.focus();
            return;
        }
        btn.disabled=true;btn.classList.add('lgn-btn--loading');btn.textContent='Gönderiliyor...';
        msgEl.className='form-message';
        fetch(API_URL+'/v1/auth/forgot-password',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':document.querySelector('[name=csrf_token]').value},
            body:JSON.stringify({email:email}),
            credentials:'include'
        }).then(function(r){return r.json().catch(function(){return null;}).then(function(b){return{status:r.status,body:b};});})
        .then(function(res){
            var d=res.body||{};
            if(res.status>=200&&res.status<300){
                showMsg((d&&d.message)||SUCCESS_TEXT,true);
                resetBtn();
                return;
            }
            console.error('[auth] forgot-password: api error',res.status,d);
            var m=messageFor(res.status,d);
            showMsg(m.msg,false);
            resetBtn();
            if(m.first==='email'){emailInput.focus();}
        }).catch(function(cause){
            console.error('[auth] forgot-password: request failed',cause);
            showMsg('Bağlantı hatası. Lütfen tekrar deneyin.',false);
            resetBtn();
            emailInput.focus();
        });
    });
})();
</script>
