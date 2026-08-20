<?php declare(strict_types=1);

/**
 * CoreMusic — Register Page (3-Step Wizard)
 * Target: .ai/.png/shared-1024/Linux  1024 - Register Girl.png (step 1-3)
 * Layout: Pattern 5 — Auth Screen (72/28 split)
 * Social buttons: PNG mockup set
 */

$csrf      = htmlspecialchars((string)($_SESSION['csrf_token'] ?? ''), ENT_QUOTES, 'UTF-8');
$nonce     = htmlspecialchars((string)($_SESSION['csp_nonce'] ?? ''), ENT_QUOTES, 'UTF-8');
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
  <h1 class="lgn-hero__title">Hayatın<br><em>milyonlarca</em></h1>
  <p class="lgn-hero__text">Sistem, Milyarlarca şarkı, özel seçilmiş playlistler, sonsuz müzik keyfi. Senin için.</p>
  <div class="lgn-hero__cards">
    <div class="lgn-hero__card">
      <div class="lgn-hero__card-icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/music.png" alt="Ücretsiz" width="22" height="22"></div>
      <span class="lgn-hero__card-text">Ücretsiz Başla</span>
    </div>
    <div class="lgn-hero__card">
      <div class="lgn-hero__card-icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/cd-ico.png" alt="Sınırsız" width="22" height="22"></div>
      <span class="lgn-hero__card-text">Sınırsız Müzik</span>
    </div>
    <div class="lgn-hero__card">
      <div class="lgn-hero__card-icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/music-2.png" alt="Hızlı" width="22" height="22"></div>
      <span class="lgn-hero__card-text">30 Saniyede Kayıt</span>
    </div>
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
      <h2 class="lgn-panel__title">Hesap Oluştur</h2>
      <p class="lgn-panel__sub">CoreMusic dünyasına katıl, müziğin keyfini çıkar</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <!-- 3-step wizard indicator -->
  <div class="lgn-wizard"><div class="lgn-wizard__step lgn-wizard__step--active" data-step="1"></div><div class="lgn-wizard__step" data-step="2"></div><div class="lgn-wizard__step" data-step="3"></div></div>
  <div class="lgn-error" id="lgn-err"></div>
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
        <input class="lgn-form__input" type="text" id="lgn-usr" name="username" placeholder="kullanici_adin" autocomplete="username" required minlength="3" maxlength="32">
      </div>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-email">E-posta</label>
        <input class="lgn-form__input" type="email" id="lgn-email" name="email" placeholder="ornek@email.com" autocomplete="email" required>
      </div>
      <button type="button" class="lgn-btn" id="step1-next">Devam Et</button>
    </div>

    <!-- STEP 2: Şifre + Şifre Tekrar -->
    <div class="lgn-form__step" data-step="2" hidden>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-pw1">Şifre</label>
        <div class="lgn-form__pw-wrap">
          <input class="lgn-form__input" type="password" id="lgn-pw1" name="password" placeholder="En az 8 karakter" autocomplete="new-password" required minlength="8">
          <button type="button" class="lgn-form__pw-eye" aria-label="Şifreyi göster"><svg viewBox="0 0 24 24"><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11 7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg></button>
        </div>
      </div>
      <div class="lgn-strength" id="pw-strength" hidden><div class="lgn-strength__bar"><div class="lgn-strength__fill" id="pw-strength-fill"></div></div><span class="lgn-strength__text" id="pw-strength-text"></span></div>
      <div class="lgn-form__field">
        <label class="lgn-form__label" for="lgn-pw2">Şifre Tekrar</label>
        <input class="lgn-form__input" type="password" id="lgn-pw2" name="password_confirm" placeholder="Şifreni tekrar gir" autocomplete="new-password" required minlength="8">
      </div>
      <button type="button" class="lgn-btn" id="step2-next">Devam Et</button>
    </div>

    <!-- STEP 3: Telefon + Hizmet Şartları -->
    <div class="lgn-form__step" data-step="3" hidden>
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
  <div class="lgn-divider"><span>veya alternatif ile devam et</span></div>
  <div class="lgn-social">
    <button type="button" class="lgn-social__btn lgn-social__btn--apple" data-provider="apple" aria-label="Apple ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/apple.png" alt="" width="18" height="18" aria-hidden="true">
      <span>Apple</span>
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--google" data-provider="google" aria-label="Google ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/google.png" alt="" width="18" height="18" aria-hidden="true">
      <span>Google</span>
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--facebook" data-provider="facebook" aria-label="Facebook ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/facebook.png" alt="" width="18" height="18" aria-hidden="true">
      <span>Facebook</span>
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--whatsapp" data-provider="whatsapp" aria-label="WhatsApp ile kayıt">
      <svg viewBox="0 0 24 24" width="18" height="18"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.126.553 4.12 1.52 5.855L0 24l6.335-1.652A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.82c-1.96 0-3.82-.52-5.42-1.44l-.387-.23-4.008 1.046 1.068-3.912-.254-.402A9.786 9.786 0 012.18 12c0-5.42 4.4-9.82 9.82-9.82S21.82 6.58 21.82 12s-4.4 9.82-9.82 9.82z" fill="currentColor"/></svg>
      <span>WhatsApp</span>
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--instagram" data-provider="instagram" aria-label="Instagram ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/instagram.png" alt="" width="18" height="18" aria-hidden="true">
      <span>Instagram</span>
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--tiktok" data-provider="tiktok" aria-label="TikTok ile kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/app/tik-tok.png" alt="" width="18" height="18" aria-hidden="true">
      <span>TikTok</span>
    </button>
    <button type="button" class="lgn-social__btn lgn-social__btn--mic" data-provider="microphone" aria-label="Sesli kayıt">
      <img src="<?= ASSETS_URL ?>/Image/res-pink/mic-1.png" alt="" width="18" height="18" aria-hidden="true">
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
    var form=document.getElementById('lgn-form');
    var submitBtn=document.getElementById('lgn-submit');
    form.addEventListener('submit',function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        var phone=document.getElementById('lgn-phone').value.trim();
        if(!/^\+?[0-9\s\-\(\)]{10,20}$/.test(phone)){errEl.textContent='Geçerli bir telefon numarası girin.';errEl.classList.add('lgn-error--on');return;}
        if(!document.getElementById('lgn-terms').checked){errEl.textContent='Hizmet Şartları\u0027nı kabul etmelisiniz.';errEl.classList.add('lgn-error--on');return;}
        submitBtn.disabled=true;submitBtn.classList.add('lgn-btn--loading');submitBtn.textContent='Kayıt olunuyor...';errEl.classList.remove('lgn-error--on');
        fetch('/register',{
            method:'POST',
            headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':document.querySelector('[name=csrf_token]').value},
            body:JSON.stringify({username:document.getElementById('lgn-usr').value,email:document.getElementById('lgn-email').value,password:pwInput.value,phone:phone,agree_terms:true}),
            credentials:'include'
        }).then(function(r){return r.json();}).then(function(d){
            if(d.success&&d.redirect){window.location.href=d.redirect;return;}
            var msg=(d.error&&d.error.message)||d.message||'Kayıt başarısız.';
            if(d.error&&d.error.details){var det=d.error.details;var msgs=[];for(var k in det)msgs.push(det[k]);msg=msgs.join(' ');}
            errEl.textContent=msg;errEl.classList.add('lgn-error--on');
            submitBtn.disabled=false;submitBtn.classList.remove('lgn-btn--loading');submitBtn.textContent='Kayıt Ol';
        }).catch(function(){
            errEl.textContent='Bağlantı hatası.';errEl.classList.add('lgn-error--on');
            submitBtn.disabled=false;submitBtn.classList.remove('lgn-btn--loading');submitBtn.textContent='Kayıt Ol';
        });
    });
})();
</script>
