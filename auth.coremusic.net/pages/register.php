<?php declare(strict_types=1);

/**
 * CoreMusic — Register Page (3-Step Wizard)
 * Target: .ai/.png/shared-1024/Linux  1024 - Register Girl.png (step 1-3)
 * Layout: Pattern 5 — Auth Screen (72/28 split)
 * Social buttons: PNG mockup set
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

// Session başlatılamadıysa register POST'u CSRF reddiyle düşer — JS catch'i "Bağlantı hatası" gösterir, kök neden loglanır.
if ($csrf === '') {
    error_log(json_encode(['level' => 'error', 'service' => 'auth.coremusic.net', 'message' => 'register.php: csrf_token missing in session - POST /register will be rejected']));
}
?>
<section class="lgn-page" data-gender="<?=$genderAttr?>">
<script nonce="<?=$nonce?>">(function(){var s=document.currentScript.parentElement;if(s.dataset.gender==='neutral'){try{var g=localStorage.getItem('cm_gender');if(g&&['female','male','neutral'].indexOf(g)!==-1)s.dataset.gender=g;}catch(e){console.error('[auth] register: localStorage gender read failed',e);document.addEventListener('DOMContentLoaded',function(){const el=document.getElementById('lgn-err');if(el){el.textContent='Tema tercihi yüklenemedi, varsayılan tema kullanılıyor.';el.classList.add('lgn-error--on');}});}}})();</script>
<div class="lgn-bg" aria-hidden="true"></div>
<div class="lgn-particles" aria-hidden="true"><?php for($i=0;$i<8;$i++)echo'<div class="lgn-particle"></div>'; ?></div>

<!-- HERO (left 749px) -->
<div class="lgn-hero">
  <div class="lgn-hero__brand">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-img.png" alt="" width="30" height="30">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-text.png" alt="CoreMusic" class="logo-text-png">
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
      <h2 class="lgn-panel__title">Hesap Oluştur</h2>
      <p class="lgn-panel__sub">CoreMusic ailesine katıl, müziğin keyfini çıkar</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <!-- 4-step wizard indicator -->
  <div class="lgn-wizard"><div class="lgn-wizard__step lgn-wizard__step--active" data-step="1"></div><div class="lgn-wizard__step" data-step="2"></div><div class="lgn-wizard__step" data-step="3"></div><div class="lgn-wizard__step" data-step="4"></div></div>
  <div class="lgn-error" id="lgn-err" role="alert" aria-live="polite"></div>
  <form class="lgn-form" id="lgn-form" method="post" action="/register" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrf?>">
    <input type="hidden" name="client_id" value="<?=htmlspecialchars($clientId, ENT_QUOTES, 'UTF-8')?>">
    <input type="hidden" name="response_type" value="<?=htmlspecialchars($responseType, ENT_QUOTES, 'UTF-8')?>">
    <?php if (!empty($redirectUri)): ?>
    <input type="hidden" name="redirect_uri" value="<?=htmlspecialchars($redirectUri, ENT_QUOTES, 'UTF-8')?>">
    <?php endif; ?>

    <!-- STEP 1: Kullanıcı Adı + E-posta -->
    <div class="lgn-form__step" data-step="1">
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-usr">Kullanıcı Adı</label>
        <input class="lgn-form__input" type="text" id="lgn-usr" name="username" placeholder="Equalzier Presents 5" autocomplete="username" required minlength="3" maxlength="32">
      </div>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-email">Eposta</label>
        <input class="lgn-form__input" type="email" id="lgn-email" name="email" placeholder="Equalzier Presents 5" autocomplete="email" required>
      </div>
      <button type="button" class="lgn-btn" id="step1-next">Devam Et</button>
    </div>

    <!-- STEP 2: Şifre + Şifre Tekrar -->
    <div class="lgn-form__step" data-step="2" hidden>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-pw1">Şifre</label>
        <div class="lgn-form__pw-wrap">
          <input class="lgn-form__input" type="password" id="lgn-pw1" name="password" placeholder="Equalzier Presents 5" autocomplete="new-password" required minlength="8">
          <button type="button" class="lgn-form__pw-eye" aria-label="Şifreyi göster"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11 7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
        </div>
      </div>
      <div class="lgn-strength" id="pw-strength" hidden><div class="lgn-strength__bar"><div class="lgn-strength__fill" id="pw-strength-fill"></div></div><span class="lgn-strength__text" id="pw-strength-text"></span></div>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-pw2">Şifre Tekrar</label>
        <input class="lgn-form__input" type="password" id="lgn-pw2" name="password_confirm" placeholder="Equalzier Presents 5" autocomplete="new-password" required minlength="8">
      </div>
      <button type="button" class="lgn-btn" id="step2-next">Devam Et</button>
    </div>

    <!-- STEP 3: Cinsiyet Seçimi -->
    <div class="lgn-form__step" data-step="3" hidden>
      <input type="hidden" id="reg-gender-input" name="gender" value="">
      <p class="lgn-form__label" style="text-align:center;margin-bottom:12px;">Cinsiyetini seç</p>
      <button type="button" class="lgn-gender-btn" data-gender="female" aria-label="Kadın">
        <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/kız-gender-select.png" alt="" width="28" height="28"></span>
        <span class="lgn-gender-btn__info"><strong>Kadın</strong></span>
      </button>
      <button type="button" class="lgn-gender-btn" data-gender="male" aria-label="Erkek">
        <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/erkek-gender-select.png" alt="" width="28" height="28"></span>
        <span class="lgn-gender-btn__info"><strong>Erkek</strong></span>
      </button>
      <button type="button" class="lgn-gender-btn" data-gender="neutral" aria-label="Belirtmek istemiyorum">
        <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/notur-gender-select.png" alt="" width="28" height="28"></span>
        <span class="lgn-gender-btn__info"><strong>Belirtmek istemiyorum</strong></span>
      </button>
      <button type="button" class="lgn-btn" id="step3-next" disabled>Devam Et</button>
    </div>

    <!-- STEP 4: Telefon + Hizmet Şartları -->
    <div class="lgn-form__step" data-step="4" hidden>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-phone">Telefon</label>
        <input class="lgn-form__input" type="tel" id="lgn-phone" name="phone" placeholder="+90 555 123 45 67" autocomplete="tel" required pattern="^\+?[0-9\s\-\(\)]{10,20}$">
      </div>
      <div class="lgn-terms">
        <input type="checkbox" id="lgn-terms" name="agree_terms" value="1" required>
        <label for="lgn-terms">Hizmet Şartlarını okudum ve kabul ediyorum</label>
      </div>
      <button type="submit" class="lgn-btn" id="lgn-submit">Kayıt Ol</button>
    </div>
  </form>
  </div>
  <div class="lgn-panel__inner-social">
  <div class="lgn-divider"><span>veya şununla devam et</span></div>
  <div class="lgn-social">
    <!-- PNG mockup social buttons (3x3 grid) -->
    <button type="button" class="lgn-social__btn lgn-social__btn--apple" data-provider="apple" aria-label="Apple ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/apple.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--google" data-provider="google" aria-label="Google ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/google.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--facebook" data-provider="facebook" aria-label="Facebook ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/facebook.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <!-- Row 2 -->
    <button type="button" class="lgn-social__btn lgn-social__btn--spotify" data-provider="spotify" aria-label="Spotify ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/spotify.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--instagram" data-provider="instagram" aria-label="Instagram ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/instagram.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--tiktok" data-provider="tiktok" aria-label="TikTok ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/tik-tok.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
    <!-- Row 3 -->
    <button type="button" class="lgn-social__btn lgn-social__btn--github" data-provider="github" aria-label="GitHub ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/github.png" alt="" width="16" height="16" aria-hidden="true">
    </button>
  </div>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link">Zaten hesabın var mı? <a href="/login" data-no-spa>Giriş Yap</a></p>
  </div>
</div></div>
</section>
<script nonce="<?=$nonce?>">
(function(){
    var currentStep=1;
    var steps=document.querySelectorAll('.lgn-wizard__step');
    var formSteps=document.querySelectorAll('.lgn-form__step');
    var errEl=document.getElementById('lgn-err');
    function showStep(n){
        currentStep=n;
        formSteps.forEach(function(s){s.hidden=parseInt(s.dataset.step)!==n;});
        steps.forEach(function(s){var sn=parseInt(s.dataset.step);s.classList.toggle('lgn-wizard__step--active',sn===n);s.classList.toggle('lgn-wizard__step--done',sn<n);});
    }
    document.querySelectorAll('.lgn-form__pw-eye').forEach(function(eyeBtn){
        var pwInput=eyeBtn.parentElement.querySelector('input[type="password"]');
        if(pwInput){eyeBtn.addEventListener('click',function(){var t=pwInput.type==='password'?'text':'password';pwInput.type=t;eyeBtn.setAttribute('aria-label',t==='password'?'Şifreyi göster':'Şifreyi gizle');});}
    });
    var pwInput=document.getElementById('lgn-pw1');
    var strEl=document.getElementById('pw-strength');
    var fillEl=document.getElementById('pw-strength-fill');
    var txtEl=document.getElementById('pw-strength-text');
    if(pwInput){pwInput.addEventListener('input',function(){var v=pwInput.value;if(!v.length){strEl.hidden=true;return;}strEl.hidden=false;var s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;fillEl.className='lgn-strength__fill';if(s<=1){fillEl.classList.add('lgn-strength__fill--weak');txtEl.textContent='Zayıf';}else if(s<=2){fillEl.classList.add('lgn-strength__fill--medium');txtEl.textContent='Orta';}else{fillEl.classList.add('lgn-strength__fill--strong');txtEl.textContent='Güçlü';}});}
    document.getElementById('step1-next').addEventListener('click',function(){
        var usr=document.getElementById('lgn-usr').value.trim();
        var email=document.getElementById('lgn-email').value.trim();
        if(usr.length<3){errEl.textContent='Kullanıcı adı en az 3 karakter olmalı.';errEl.classList.add('lgn-error--on');return;}
        if(!/^[a-zA-Z0-9_]+$/.test(usr)){errEl.textContent='Kullanıcı adı sadece harf, rakam ve alt çizgi içerebilir.';errEl.classList.add('lgn-error--on');return;}
        if(!email||!email.includes('@')){errEl.textContent='Geçerli bir e-posta adresi girin.';errEl.classList.add('lgn-error--on');return;}
        errEl.classList.remove('lgn-error--on');showStep(2);
    });
    document.getElementById('step2-next').addEventListener('click',function(){
        var pw=pwInput.value;
        var pw2=document.getElementById('lgn-pw2').value;
        if(pw.length<8){errEl.textContent='Şifre en az 8 karakter olmalı.';errEl.classList.add('lgn-error--on');return;}
        if(pw!==pw2){errEl.textContent='Şifreler eşleşmiyor.';errEl.classList.add('lgn-error--on');return;}
        errEl.classList.remove('lgn-error--on');showStep(3);
    });
    var regGenderInput=document.getElementById('reg-gender-input');
    var regGenderBtns=document.querySelectorAll('[data-step="3"] .lgn-gender-btn');
    var step3Next=document.getElementById('step3-next');
    regGenderBtns.forEach(function(b){b.addEventListener('click',function(){
        regGenderBtns.forEach(function(x){x.classList.remove('selected');});
        b.classList.add('selected');
        regGenderInput.value=b.dataset.gender;
        document.querySelector('.lgn-page').dataset.gender=b.dataset.gender;
        try{localStorage.setItem('cm_gender',b.dataset.gender);}catch(e){console.error('[auth] register: localStorage gender write failed',e);errEl.textContent='Tema tercihi bu tarayıcıda kaydedilemedi.';errEl.classList.add('lgn-error--on');}
        step3Next.disabled=false;
    });});
    step3Next.addEventListener('click',function(){errEl.classList.remove('lgn-error--on');showStep(4);});
    var form=document.getElementById('lgn-form');
    var submitBtn=document.getElementById('lgn-submit');
    var API_URL='<?=$apiUrlEsc?>';
    var FIELD_FOCUS={username:'lgn-usr',email:'lgn-email',password:'lgn-pw1',phone:'lgn-phone',agree_terms:'lgn-terms'};
    function resetSubmit(){submitBtn.disabled=false;submitBtn.classList.remove('lgn-btn--loading');submitBtn.textContent='Kayıt Ol';}
    function showError(msg,focusField){
        errEl.textContent=msg;errEl.classList.add('lgn-error--on');
        if(focusField==='gender'){
            showStep(3);
            var gb=document.querySelector('[data-step="3"] .lgn-gender-btn');
            if(gb)gb.focus();
            return;
        }
        // Alan hatası hangi adımdaysa o adıma dön, sonra ilk hatalı alana odaklan.
        if(focusField==='username'||focusField==='email'){showStep(1);}
        else if(focusField==='password'){showStep(2);}
        else if(focusField==='phone'||focusField==='agree_terms'){showStep(4);}
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
            case 409:return{msg:e.code==='USERNAME_TAKEN'?'Bu kullanıcı adı kullanılıyor.':'Bu e-posta kullanılıyor.',first:e.code==='USERNAME_TAKEN'?'username':'email'};
            case 429:return{msg:'Çok fazla deneme. Lütfen bekleyin.'};
            case 500:return{msg:'Sistem hatası.'};
            case 503:return{msg:'Bağlantı hatası, tekrar deneyin.'};
        }
        return{msg:e.message||(d&&d.message)||'Kayıt başarısız.'};
    }
    form.addEventListener('submit',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        // Cinsiyet zorunlu (UX kontrolü — asıl güvenlik backend'de)
        var chosenGender=regGenderInput.value;
        if(chosenGender!=='female'&&chosenGender!=='male'&&chosenGender!=='neutral'){
            showStep(3);
            errEl.textContent='Lütfen cinsiyet seçin.';errEl.classList.add('lgn-error--on');
            var firstGenderBtn=document.querySelector('[data-step="3"] .lgn-gender-btn');
            if(firstGenderBtn)firstGenderBtn.focus();
            return;
        }
        var phone=document.getElementById('lgn-phone').value.trim();
        if(!/^\+?[0-9\s\-\(\)]{10,20}$/.test(phone)){errEl.textContent='Geçerli bir telefon numarası girin.';errEl.classList.add('lgn-error--on');document.getElementById('lgn-phone').focus();return;}
        if(!document.getElementById('lgn-terms').checked){errEl.textContent='Hizmet Şartları\u0027nı kabul etmelisiniz.';errEl.classList.add('lgn-error--on');document.getElementById('lgn-terms').focus();return;}
        submitBtn.disabled=true;submitBtn.classList.add('lgn-btn--loading');submitBtn.textContent='Kayıt olunuyor...';errEl.classList.remove('lgn-error--on');
        fetch(API_URL+'/v1/auth/register',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':document.querySelector('[name=csrf_token]').value},
            body:JSON.stringify({username:document.getElementById('lgn-usr').value,email:document.getElementById('lgn-email').value,password:pwInput.value,phone:phone,agree_terms:true,gender:chosenGender}),
            credentials:'include'
        }).then(function(r){return r.json().catch(function(){return null;}).then(function(b){return{status:r.status,body:b};});})
        .then(function(res){
            var d=res.body||{};
            if(res.status>=200&&res.status<300){
                var data=d.data||d;
                var target=data.redirect||d.redirect;
                if(target){window.location.href=target;return;}
                showError((d.error&&d.error.message)||d.message||'Kayıt başarısız.');
                resetSubmit();
                return;
            }
            console.error('[auth] register: api error',res.status,d);
            var m=messageFor(res.status,d);
            showError(m.msg,m.first);
            resetSubmit();
            if(d.csrf_token){var ci=document.querySelector('[name=csrf_token]');if(ci)ci.value=d.csrf_token;}
        }).catch(function(cause){
            console.error('[auth] register: request failed',cause);
            showError('Bağlantı hatası. Lütfen tekrar deneyin.');
            resetSubmit();
        });
    });
})();
</script>
