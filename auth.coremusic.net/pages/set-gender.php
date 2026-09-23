<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
$nonceEsc      = htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8');
?>
<section class="lgn-page" data-gender="neutral">
<div class="lgn-bg" aria-hidden="true"></div>
<div class="lgn-particles" aria-hidden="true"><?php for($i=0;$i<8;$i++)echo'<div class="lgn-particle"></div>'; ?></div>

<!-- HERO (left 78%) -->
<div class="lgn-hero">
  <div class="lgn-hero__brand">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-img.png" alt="" width="40" height="40">
    <img src="<?= ASSETS_URL ?>/Image/res-pink/logo/logo-text.png" alt="CoreMusic" class="logo-text-png">
  </div>
  <h1 class="lgn-hero__title"><em class="hero-title--sm">Seni</em><br><em>Tanıyalım</em></h1>
  <p class="lgn-hero__text">Deneyimini sana özel hale getirmek için bir seçim yapman yeterli.</p>
  <div class="lgn-hero__poem" aria-hidden="true">
    <span>Hayatın ritmini</span>
    <span><em>sende gizli</em></span>
    <span><em>Müziğinle parla!</em> ♥</span>
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
      <h2 class="lgn-panel__title">Cinsiyet Seçimi</h2>
      <p class="lgn-panel__sub">Tema deneyiminizi kişiselleştirmek için cinsiyet seçin</p>
    </div>
  </div>
  <div class="lgn-panel__inner-mid">
  <div class="lgn-error" id="gender-err"></div>
  <form id="gender-form" method="post" action="/set-gender" class="lgn-gender-form">
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <input type="hidden" id="gender-input" name="gender" value="neutral">
    <button type="button" class="lgn-gender-btn" data-gender="female" aria-label="Kız seçeneğini seç">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/kız-gender-select.png" alt="Kız" width="28" height="28"></span>
      <span class="lgn-gender-btn__info"><strong>Kız</strong><small>Romantik ruh hali</small></span>
    </button>
    <button type="button" class="lgn-gender-btn" data-gender="male" aria-label="Erkek seçeneğini seç">
      <span class="lgn-gender-btn__icon"><img src="<?= ASSETS_URL ?>/Image/res-pink/erkek-gender-select.png" alt="Erkek" width="28" height="28"></span>
      <span class="lgn-gender-btn__info"><strong>Erkek</strong><small>Modern tarz öner</small></span>
    </button>
    <button type="submit" id="continue-btn" class="lgn-btn" disabled>Devam Et</button>
  </form>
  </div>
  <div class="lgn-panel__inner-bot">
    <p class="lgn-foot-link">Devam ederek <a href="/privacy" data-no-spa>Gizlilik Politikası</a>'nı kabul etmiş olursunuz.</p>
  </div>
</div></div>
</section>
<script nonce="<?=$cspNonce?>">
(function(){
    var form = document.getElementById('gender-form');
    var input = document.getElementById('gender-input');
    var btn = document.getElementById('continue-btn');
    var btns = document.querySelectorAll('.lgn-gender-btn');
    var errEl = document.createElement('div');
    errEl.className = 'lgn-error';
    errEl.id = 'gender-err';
    form.insertBefore(errEl, form.firstChild);

    btns.forEach(function(b){
        b.addEventListener('click', function(){
            input.value = b.dataset.gender;
            btn.disabled = false;
            btns.forEach(function(x){ x.classList.remove('active'); });
            b.classList.add('active');
            errEl.style.display = 'none';
        });
    });

    form.addEventListener('submit', function(e){
        e.preventDefault();
        var gender = input.value;
        if (!gender) {
            errEl.textContent = 'Lütfen bir cinsiyet seçin.';
            errEl.style.display = 'block';
            return;
        }
        btn.disabled = true;
        btn.textContent = 'Devam ediliyor...';
        errEl.style.display = 'none';

        try { localStorage.setItem('cm_gender', gender); } catch(ex) {}

        fetch('/set-gender', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-Token': document.getElementById('gender-form').querySelector('[name=csrf_token]').value
            },
            body: JSON.stringify({ gender: gender }),
            credentials: 'include'
        })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (d.redirect) { window.location.href = d.redirect; }
            else { window.location.href = '/login'; }
        })
        .catch(function(){
            window.location.href = '/login';
        });
    });
})();
</script>
