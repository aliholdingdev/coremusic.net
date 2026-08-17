<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
?>
<section class="lgn-page" data-gender="<?=$genderEsc?>">
<div class="lgn-panel"><div class="lgn-panel__glass">
  <h2>CoreMusic — Kayıt</h2>
  <div class="lgn-error" id="reg-err"></div>
  <form class="lgn-form" id="reg-form" method="post" action="/register" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="reg-user">Kullanıcı Adı</label>
      <input class="lgn-form__input" type="text" id="reg-user" name="username" placeholder="kullanici_adi" required>
    </div>
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="reg-email">E-posta</label>
      <input class="lgn-form__input" type="email" id="reg-email" name="email" placeholder="ornek@email.com" required>
    </div>
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="reg-pw">Şifre</label>
      <input class="lgn-form__input" type="password" id="reg-pw" name="password" placeholder="••••••••" required>
    </div>
    <div class="lgn-form__field">
      <label class="lgn-form__check"><input type="checkbox" name="agree_terms" value="1" required><span>Kullanım Koşulları'nı kabul ediyorum</span></label>
    </div>
    <button type="submit" class="lgn-btn">Kayıt Ol</button>
  </form>
  <p class="lgn-foot-link">Zaten hesabın var mı? <a href="/login">Giriş Yap</a></p>
</div></div>
</section>
<script nonce="<?=$cspNonce?>">
(function(){
    var form=document.getElementById('reg-form');
    var errEl=document.getElementById('reg-err');
    form.addEventListener('submit',function(e){
        e.preventDefault();
        var btn=form.querySelector('button[type=submit]');btn.disabled=true;btn.textContent='Kayıt olunuyor...';errEl.style.display='none';
        fetch('/register',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':form.querySelector('[name=csrf_token]').value},body:JSON.stringify({username:document.getElementById('reg-user').value,email:document.getElementById('reg-email').value,password:document.getElementById('reg-pw').value,agree_terms:true}),credentials:'include'})
        .then(function(r){return r.json();}).then(function(d){
            if((d.success||d.redirect)&&d.redirect){window.location.href=d.redirect;return;}
            var msg=(d.error&&d.error.message)||d.message||'Kayıt başarısız.';errEl.textContent=msg;errEl.style.display='block';
            btn.disabled=false;btn.textContent='Kayıt Ol';
            if(d.csrf_token){var ci=form.querySelector('[name=csrf_token]');if(ci)ci.value=d.csrf_token;}
        }).catch(function(){errEl.textContent='Bağlantı hatası.';errEl.style.display='block';btn.disabled=false;btn.textContent='Kayıt Ol';});
    });
})();
</script>
