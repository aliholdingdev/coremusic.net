<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc      = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc   = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce       = $_SESSION['csp_nonce'] ?? '';
$redirectUriEsc = htmlspecialchars($_GET['redirect_uri'] ?? '', ENT_QUOTES, 'UTF-8');
$clientIdEsc    = htmlspecialchars($_GET['client_id'] ?? 'coremusic-web', ENT_QUOTES, 'UTF-8');
$responseTypeEsc = htmlspecialchars($_GET['response_type'] ?? 'session', ENT_QUOTES, 'UTF-8');
?>
<section class="lgn-page" data-gender="<?=$genderEsc?>">
<div class="lgn-panel"><div class="lgn-panel__glass">
  <h2>CoreMusic — Giriş</h2>
  <div class="lgn-error" id="lgn-err"></div>
  <form class="lgn-form" id="lgn-form" method="post" action="/login" novalidate>
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <input type="hidden" name="client_id" value="<?=$clientIdEsc?>">
    <input type="hidden" name="response_type" value="<?=$responseTypeEsc?>">
    <?php if (!empty($redirectUriEsc)): ?>
    <input type="hidden" name="redirect_uri" value="<?=$redirectUriEsc?>">
    <?php endif; ?>
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="lgn-id">E-posta veya Kullanıcı Adı</label>
      <input class="lgn-form__input" type="text" id="lgn-id" name="email" placeholder="ornek@email.com" autocomplete="username" required>
    </div>
    <div class="lgn-form__field">
      <label class="lgn-form__label" for="lgn-pw">Şifre</label>
      <input class="lgn-form__input" type="password" id="lgn-pw" name="password" placeholder="••••••••" autocomplete="current-password" required>
    </div>
    <button type="submit" class="lgn-btn" id="lgn-submit">Giriş Yap</button>
  </form>
  <p class="lgn-foot-link">Hesabın yok mu? <a href="/register">Kayıt Ol</a></p>
</div></div>
</section>
<script nonce="<?=$cspNonce?>">
(function(){
    var form=document.getElementById('lgn-form');
    var btn=document.getElementById('lgn-submit');
    var errEl=document.getElementById('lgn-err');
    form.addEventListener('submit',function(e){
        e.preventDefault();
        btn.disabled=true;btn.textContent='Giriş yapılıyor...';errEl.style.display='none';
        fetch('/login',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':form.querySelector('[name=csrf_token]').value},body:JSON.stringify({email:document.getElementById('lgn-id').value,password:document.getElementById('lgn-pw').value}),credentials:'include'})
        .then(function(r){return r.json();}).then(function(d){
            if((d.success||d.redirect)&&d.redirect){window.location.href=d.redirect;return;}
            var msg=(d.error&&d.error.message)||d.message||'Giriş başarısız.';errEl.textContent=msg;errEl.style.display='block';
            btn.disabled=false;btn.textContent='Giriş Yap';
            if(d.csrf_token){var ci=form.querySelector('[name=csrf_token]');if(ci)ci.value=d.csrf_token;}
        }).catch(function(){errEl.textContent='Bağlantı hatası.';errEl.style.display='block';btn.disabled=false;btn.textContent='Giriş Yap';});
    });
})();
</script>
