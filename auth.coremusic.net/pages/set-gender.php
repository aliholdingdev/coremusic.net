<?php declare(strict_types=1);
// Session-based variables (shared renderer uyumu)
$genderEsc     = htmlspecialchars($_SESSION['cm_gender'] ?? $_SESSION['gender'] ?? 'neutral', ENT_QUOTES, 'UTF-8');
$csrfTokenEsc  = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
$cspNonce      = $_SESSION['csp_nonce'] ?? '';
?>
<section class="lgn-page" data-gender="neutral">
<div class="lgn-panel"><div class="lgn-panel__glass">
  <h2>Cinsiyet Seçimi</h2>
  <p class="lgn-panel__sub">Tema deneyiminizi kişiselleştirmek için cinsiyet seçin</p>
  <form id="gender-form" method="post" action="/set-gender">
    <input type="hidden" name="csrf_token" value="<?=$csrfTokenEsc?>">
    <input type="hidden" id="gender-input" name="gender" value="neutral">
    <div class="lgn-gender-select">
      <button type="button" class="lgn-gender-btn" data-gender="female">
        <span class="lgn-gender-btn__icon">👩</span>
        <span class="lgn-gender-btn__label">Kadın</span>
      </button>
      <button type="button" class="lgn-gender-btn" data-gender="male">
        <span class="lgn-gender-btn__icon">👨</span>
        <span class="lgn-gender-btn__label">Erkek</span>
      </button>
    </div>
    <button type="submit" id="continue-btn" class="lgn-btn" disabled>Devam Et</button>
  </form>
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
        if (!gender || gender === 'neutral') {
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
