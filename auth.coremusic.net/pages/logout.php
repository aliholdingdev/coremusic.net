<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
?>
<section class="lgn-page" data-gender="<?=$genderEsc?>">
<div class="lgn-panel"><div class="lgn-panel__glass">
  <h2>Çıkış Yapıldı</h2>
  <p>Başarıyla çıkış yaptınız.</p>
  <a href="/login" class="lgn-btn">Tekrar Giriş Yap</a>
</div></div>
</section>
<script nonce="<?=$cspNonce?>">
(function(){
    // Otomatik POST ile session'ı yık
    fetch('/logout',{method:'POST',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':'<?=$csrfTokenEsc?>'},credentials:'include'}).then(function(r){return r.json();}).then(function(d){
        if(d.redirect){window.location.href=d.redirect;}
    }).catch(function(){});
})();
</script>
