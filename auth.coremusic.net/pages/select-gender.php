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
  <h2>Cinsiyet Seçimi</h2>
  <form id="gender-form" method="post" action="/set-gender">
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <input type="hidden" name="client_id" value="<?=$clientIdEsc?>">
    <input type="hidden" name="response_type" value="<?=$responseTypeEsc?>">
    <?php if (!empty($redirectUriEsc)): ?>
    <input type="hidden" name="redirect_uri" value="<?=$redirectUriEsc?>">
    <?php endif; ?>
    <input type="hidden" id="gender-input" name="gender" value="neutral">
    <button type="button" class="lgn-gender-btn" data-gender="female">Kadın</button>
    <button type="button" class="lgn-gender-btn" data-gender="male">Erkek</button>
    <button type="submit" id="continue-btn" class="lgn-btn" disabled>Devam Et</button>
  </form>
</div></div>
</section>
<script nonce="<?=$cspNonce?>">
(function(){
    var form=document.getElementById('gender-form');
    var input=document.getElementById('gender-input');
    var btn=document.getElementById('continue-btn');
    var btns=document.querySelectorAll('.lgn-gender-btn');
    btns.forEach(function(b){b.addEventListener('click',function(){
        input.value=b.dataset.gender;btn.disabled=false;btns.forEach(function(x){x.classList.remove('active');});b.classList.add('active');
    });});
    form.addEventListener('submit',function(e){
        e.preventDefault();
        if(!input.value||input.value==='neutral')return;
        btn.disabled=true;btn.textContent='Devam ediliyor...';
        var payload={gender:input.value};
        var ci=form.querySelector('[name=client_id]');
        var rt=form.querySelector('[name=response_type]');
        var ru=form.querySelector('[name=redirect_uri]');
        if(ci)payload.client_id=ci.value;
        if(rt)payload.response_type=rt.value;
        if(ru)payload.redirect_uri=ru.value;
        fetch('/set-gender',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':form.querySelector('[name=csrf_token]').value},body:JSON.stringify(payload),credentials:'include'})
        .then(function(r){return r.json();}).then(function(d){
            if(d.redirect){window.location.href=d.redirect;}else{
                var qs=[];if(ci&&ci.value)qs.push('client_id='+encodeURIComponent(ci.value));if(rt&&rt.value)qs.push('response_type='+encodeURIComponent(rt.value));if(ru&&ru.value)qs.push('redirect_uri='+encodeURIComponent(ru.value));
                window.location.href='/login'+(qs.length?'?'+qs.join('&'):'');
            }
        }).catch(function(){
            var qs=[];if(ci&&ci.value)qs.push('client_id='+encodeURIComponent(ci.value));if(rt&&rt.value)qs.push('response_type='+encodeURIComponent(rt.value));if(ru&&ru.value)qs.push('redirect_uri='+encodeURIComponent(ru.value));
            window.location.href='/login'+(qs.length?'?'+qs.join('&'):'');
        });
    });
})();
</script>
