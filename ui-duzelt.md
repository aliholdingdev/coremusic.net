# CoreMusic — Frontend Revizyon Promptu (ui-duzelt.md)

**Versiyon:** 1.0.0  
**Tarih:** 2026-09-04  
**Kapsam:** home.coremusic.net frontend — CSS, PHP template, JS  
**Hedef:** Tüm tespit edilen eksikliklerin tek seferde uygulanabilir, modüler düzeltme planı  

> **⚠️ Durum Notu (2026-09-06):** Bu plan 2026-09-04 tarihli analize dayanır. 2026-09-06'da footer/header üzerinde coreplayer entegrasyonu v1.3.0 (seek/volume/ID sözleşmesi) ve volume zinciri düzeltmeleri **ayrı yoldan** uygulandı (bkz. `.ai/log.md` 15:32 / 15:42 / 16:41 kayıtları). Bu plandaki Adım 6 (footer) ve Adım 9 (inline JS → ES modül) adaylarının güncelliği **yeniden doğrulanmalıdır — DOĞRULANMADI**. Adım 8 zaten plan içinde "ATLA" işaretlidir.

---

## Bölüm A: Analiz Bulguları Özeti

### A.1 Kritik Eksiklikler (6 Adet)

| # | Dosya | Sorun | Etki |
|---|-------|-------|------|
| 1 | `_home-components.css` | Welcome "Başla" butonu 105×25px — WCAG min 44×48px | Touch usable yok |
| 2 | `_header.css` | Nav link touch target ~24×24px — WCAG min 48×48px | Touch usable yok |
| 3 | `_home-components.css` | Star rating ~20×20px — WCAG min 48×48px | Touch usable yok |
| 4 | `_home-components.css` | Genre tabs yüksekliği ~32px — WCAG min 48px | Touch usable yok |
| 5 | `_home-components.css` | Track row yüksekliği ~40px — WCAG min 48px | Touch usable yok |
| 6 | `home.php` | Wide layout bottom-row'da 3. sütun boş (içerik yok) | Eksik UI bölgesi |

### A.2 Orta Seviye Eksiklikler (5 Adet)

| # | Dosya | Sorun | Etki |
|---|-------|-------|------|
| 7 | `home.php` | Phone layout'da welcome modal yok | Tüm cihazlarda tutarsız |
| 8 | `home.php` | Phone layout'da "Sıradaki" kolonu yok | Eksik content |
| 9 | `header.php` | Inline `<style>` var (CSS custom property set) | CSP risk, ITCSS ihlali |
| 10 | `main.css` | `_footer.css` ve `_home-components.css` direkt import edilmiyor | Sadece bridge üzerinden |
| 11 | `footer.php` | Phone layout'da seek slider, utility icons, album art yok | Eksik player UI |

### A.3 Düşük Seviye Eksiklikler (4 Adet)

| # | Dosya | Sorun | Etki |
|---|-------|-------|------|
| 12 | `home.php` | Welcome modal inline JS — ES modülü değil | Modülerlik |
| 13 | `home.php` | Saat/tarih inline JS — ES modülü değil | Modülerlik |
| 14 | `d-embedded.css` + `d-phone.css` | Base dosyaları tekrar import ediliyor (ITCSS layering) | Performans |
| 15 | `home.php` | Wide layout bottom'da sadece 1'er kart var (embedded'de 4'er var) | Tutarsız content |

---

## Bölüm B: Uygulama Planı (Adım Adım)

### Adım 1: WCAG Touch Target Düzeltmeleri (Kritik — İlk Öncelik)

**Hedef dosya:** `assets.coremusic.net/Css/05_Pages/_home-components.css`

#### 1.1 Welcome "Başla" Butonu

Mevcut sorun: `welcome-modal__btn` yüksekliği 25px.

**Değişiklik:** `_home-components.css` dosyasında `welcome-modal__btn` kuralına şu ekleme yapılmalı:

```css
/* ============================================================
   WELCOME MODAL — Başla Butonu (WCAG Touch Target)
   Hedef: min-height 48px (RPi5 touch)
   ============================================================ */
.welcome-modal__btn {
    min-height: 48px;
    padding: 12px 32px;
    font-size: 14px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    width: 100%;
    max-width: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}
```

#### 1.2 Nav Link Touch Target

Mevcut sorun: `.nav-link` ~24×24px.

**Güncelleme (2026-09-07 — Uygulandı):** Statik `min-height: 48px` yerine **hibrit model** uygulandı. Nav görsel boyutu PNG mockup'larla birebir korunur (compact layout bozulmaz); dokunma hedefi yalnızca dokunmatik cihazlarda `::before` hit-area ile 48×48px'e genişletilir.

**Değişiklik:** `assets.coremusic.net/Css/03_Layout/_header.css` dosyasında, 1024px breakpoint'ten sonra:

```css
/* ============================================================
   TOUCH DEVICE — Nav Link Hit Area (WCAG 2.5.8 hibrit)
   Görsel boyut PNG'de olduğu gibi kalır (compact);
   dokunma hedefi ::before ile min 48×48px'e genişletilir.
   Sadece dokunmatik (coarse pointer) cihazlarda etkin.
   ============================================================ */
@media (pointer: coarse) {
    .nav-link::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        min-width: 48px;
        min-height: 48px;
        transform: translate(-50%, -50%);
    }
}
```

**Notlar:**
- `.nav-link` base kuralında `position: relative` mevcut (ön koşul sağlanıyor)
- `.nav-link.active::after` (underline) ile çakışmaz — farklı pseudo-element
- `@media (pointer: coarse)` phone, tablet ve RPi5 dokunmatik'i kapsar; mouse'lu desktop etkilenmez
- 1024px breakpoint'te compact padding (4px 8px) korunur — PNG sadakati sağlanır

#### 1.3 Star Rating Touch Target

**Değişiklik:** `_home-components.css` içinde `.now-playing__stars` ve `.star-gold` kuralı:

```css
/* ============================================================
   STAR RATING — WCAG Touch Target
   Tam satırı tıklanabilir yap (48px yükseklik)
   ============================================================ */
.now-playing__rating-line {
    display: flex;
    align-items: center;
    gap: var(--space-1, 4px);
    min-height: 48px;
}

.now-playing__stars {
    display: inline-flex;
    align-items: center;
    gap: 2px;
    min-height: 48px;
    padding: 4px 0;
    cursor: pointer;
}

.star-gold {
    font-size: 18px;
    line-height: 1;
    color: #FFD700;
    pointer-events: none;
}

/* Hit area genişlet — yıldızların tam satırı tıklanabilir */
.now-playing__rating-line .now-playing__stars {
    min-width: 120px;
}
```

#### 1.4 Genre Tabs Touch Target

**Değişiklik:** `_home-components.css` içinde `.genre-tabs` ve `.genre-tab` kuralı:

```css
/* ============================================================
   GENRE TABS — WCAG Touch Target (min 48px yükseklik)
   ============================================================ */
.genre-tabs {
    display: flex;
    gap: var(--space-1, 4px);
    overflow-x: auto;
    scrollbar-width: none;
    padding: var(--space-1, 4px) 0;
}

.genre-tabs::-webkit-scrollbar {
    display: none;
}

.genre-tab {
    min-height: 48px;
    padding: 10px 20px;
    font-family: var(--font-body, 'Arima', sans-serif);
    font-size: var(--text-xs, 11px);
    font-weight: var(--font-medium, 500);
    color: rgba(255, 255, 255, 0.7);
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid transparent;
    border-radius: 20px;
    cursor: pointer;
    white-space: nowrap;
    flex-shrink: 0;
    transition: background 250ms ease, color 250ms ease;
}

.genre-tab.active {
    background: var(--accent, #ff4fd8);
    color: #ffffff;
    border-color: var(--accent, #ff4fd8);
}

.genre-tab:focus-visible {
    outline: 2px solid var(--accent, #ff4fd8);
    outline-offset: 2px;
}
```

#### 1.5 Track Row Touch Target

**Değişiklik:** `_home-components.css` içinde `.track-row` kuralı:

```css
/* ============================================================
   TRACK ROW — WCAG Touch Target (min 48px yükseklik)
   ============================================================ */
.track-row {
    display: flex;
    align-items: center;
    gap: var(--space-2, 8px);
    min-height: 48px;
    padding: 8px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    cursor: pointer;
    transition: background 250ms ease;
}

.track-row:hover {
    background: rgba(255, 255, 255, 0.05);
}

.track-row.active {
    background: rgba(255, 79, 216, 0.15);
    border-left: 3px solid var(--accent, #ff4fd8);
}

.track-row:focus-visible {
    outline: 2px solid var(--accent, #ff4fd8);
    outline-offset: 2px;
}
```

---

### Adım 2: Wide Layout Bottom Row Düzeltmesi

**Hedef dosya:** `home.coremusic.net/pages/home.php` (v10.0.0)

**Sorun:** Wide layout bottom-row'da 3. sütun boş. Embedded'de "Sıradaki Şarkılar" var, wide'da yok.

**Değişiklik:** `home.php` içinde `$isWide` bloğunda alt satır 3. sütun bölümü (yaklaşık satır 648-649):

```php
<!-- SÜTUN 3: Sıradaki Şarkılar (Wide layout) -->
<section class="home-layout__bottom-col home-layout__bottom-col--wide" aria-label="Sıradaki Şarkılar">
    <h2 class="home-column-header">Sıradaki Şarkılar</h2>
    <div class="home-cards-wrapper--wide">
        <a href="#" class="home-recent-card home-recent-card--desktop" role="button" aria-label="Göksel - Sevil Neşelen">
            <div class="home-recent-card__art">
                <img src="<?= $assetsUrl ?>/Image/res-pink/album-kursat.png" alt="" loading="lazy" width="64" height="64">
            </div>
            <div class="home-recent-card__info">
                <p class="home-recent-card__title">Şarkı Adı : <?= $currentSong ?></p>
                <p class="home-recent-card__sub"><?= $currentAlbum ?> &nbsp;•&nbsp; <?= $currentArtist ?></p>
            </div>
        </a>
    </div>
</section>
```

---

### Adım 3: Phone Layout Welcome Modal Ekleme

**Hedef dosya:** `home.coremusic.net/pages/home.php` (v10.0.0)

**Sorun:** Phone layout'da (`$isPhone`) welcome modal gösterilmiyor.

**Değişiklik:** `home.php` içinde `$isPhone` bloğunu `<?php endif; ?>` ile kapatmadan ÖNCE, embedded'dekiyle aynı welcome modal eklenmeli. Aşağıdaki bloğu `$isPhone` bloğunun sonuna ekle (yaklaşık satır 121'in hemen Öncesine):

```php
<!-- ============================================================
     HOŞ GELDİN POPUP / MODAL — Phone Layout
     ============================================================ -->
<?php if ($dm->shouldRenderWelcomePopup()): ?>
<div class="welcome-modal-overlay" id="welcomeModalOverlay" role="dialog" aria-modal="true" aria-labelledby="welcomeModalTitle">
    <div class="welcome-modal welcome-modal--phone">
        <button class="welcome-modal__close" id="welcomeModalClose" type="button" aria-label="Kapat">&times;</button>
        
        <div class="welcome-modal__emblem">
            <img src="<?= $assetsUrl ?>/Image/res-pink/logo/logo-img.png" class="welcome-modal__swirl-img" alt="" loading="eager">
            <span class="welcome-modal__logo-brand">CoreMusic</span>
        </div>

        <h2 class="welcome-modal__title" id="welcomeModalTitle">Hoş geldin</h2>
        <p class="welcome-modal__user-calligraphy"><?= $username ?></p>
        
        <p class="welcome-modal__desc">
            Sana özel seçilen melodiler burada başlıyor.
        </p>

        <button class="welcome-modal__btn" id="welcomeStartBtn" type="button">Başla</button>
    </div>
</div>
<?php endif; ?>
```

**CSS ekleme:** `_home-components.css` içine:

```css
/* ============================================================
   WELCOME MODAL — Phone Variant
   ============================================================ */
.welcome-modal--phone {
    max-width: 90vw;
    padding: 20px;
}

.welcome-modal--phone .welcome-modal__title {
    font-size: 18px;
}

.welcome-modal--phone .welcome-modal__desc {
    font-size: 12px;
}
```

---

### Adım 4: Phone Layout "Sıradaki" Kolonu Ekleme

**Hedef dosya:** `home.coremusic.net/pages/home.php` (v10.0.0)

**Sorun:** Phone layout'da "Sıradaki Şarkılar" bölümü yok.

**Değişiklik:** `$isPhone` bloğunda "Çalma Listeleri" section'ından sonra (yaklaşık satır 119) şu bölümü ekle:

```php
    <!-- Sıradaki Şarkılar -->
    <section class="phone-section" aria-label="Sıradaki Şarkılar">
        <h2 class="home-column-header">Sıradaki Şarkılar</h2>
        <div class="phone-card-list">
            <a href="#" class="mini-music-card" role="button" aria-label="Kış Masalı Enstrümental">
                <div class="mini-music-card__art">
                    <img src="<?= $assetsUrl ?>/Image/res-pink/album-kursat.png" alt="" loading="lazy" width="44" height="44">
                </div>
                <div class="mini-music-card__info">
                    <p class="mini-music-card__title">Kış Masalı Enstrümental</p>
                    <p class="mini-music-card__artist">Org Dersleri</p>
                </div>
                <span class="mini-music-card__dur">00:05:00</span>
            </a>
        </div>
    </section>
```

---

### Adım 5: Header Inline Style Temizliği

**Hedef dosya:** `home.coremusic.net/header.php` (v6.0.0)

**Sorun:** `header.php` içinde inline `<style>` bloğu var (satır 75-83). CSP riski ve ITCSS ihlali.

**Değişiklik:** Inline style'ı kaldır, CSS'e taşı:

**1. `header.php` dosyasından şu bloğu KALDIR:**
```php
<!-- BU BLOK SİL -->
<style nonce="<?= $nonce ?>">
:root {
    --body-bg-image: url('<?= $assetsUrl ?>/Image/background/bkimage1.png');
    --body-bg-attachment: fixed;
    --body-bg-size: cover;
    --body-bg-position: center;
    --body-bg-repeat: no-repeat;
}
</style>
```

**2. `assets.coremusic.net/Css/02_Base/b-base-core.css` dosyasına ekle:**
```css
/* ============================================================
   BODY BACKGROUND — Dynamic (Theme Engine ADR-044)
   ============================================================ */
:root {
    --body-bg-image: url('/Image/background/bkimage1.png');
    --body-bg-attachment: fixed;
    --body-bg-size: cover;
    --body-bg-position: center;
    --body-bg-repeat: no-repeat;
}

body {
    background-image: var(--body-bg-image);
    background-attachment: var(--body-bg-attachment);
    background-size: var(--body-bg-size);
    background-position: var(--body-bg-position);
    background-repeat: var(--body-bg-repeat);
}
```

---

### Adım 6: Footer Phone Layout İyileştirmesi

**Hedef dosya:** `home.coremusic.net/footer.php` (v8.0.0)

**Sorun:** Phone layout'da sadece 4 buton var (önceki/oynat/duraklat/ileri). Seek slider, utility icons, album art yok.

**Değişiklik:** `footer.php` içinde `$dm->isPhone()` bloğunu genişlet:

```php
<?php if ($dm->isPhone()): ?>
<!-- ============================================================
     MOBILE — Enhanced footer (progress + art + metadata + controls)
     ============================================================ -->
<footer class="footer footer--mobile <?= $dm->allClasses() ?>" role="region" aria-label="Müzik çalar (mobil)" <?= $dm->dataAttributes() ?>>
    <!-- Progress Bar -->
    <div class="player-progress"><div class="player-progress__fill" id="fp_fill" style="width:0;"></div></div>
    
    <div class="footer__inner footer__inner--mobile">
        <!-- Album Art + Metadata -->
        <section class="footer__meta-section footer__meta-section--mobile">
            <img class="footer__album-art footer__album-art--mobile"
                 id="footer_songimages"
                 src="<?= $h($assetsUrl . '/Image/res-pink/album-kursat.png') ?>"
                 alt="Albüm kapağı" loading="eager" width="48" height="48">
            <div class="footer__meta-stack footer__meta-stack--mobile">
                <span class="footer__text footer__song-name footer__song-name--mobile" id="footer_songname">Göksel - Sevil Neşelen</span>
                <span class="footer__text footer__album-name footer__album-name--mobile" id="footer_albumadi">Hayat Rüya Gibi</span>
            </div>
        </section>

        <!-- Controls -->
        <section class="footer__controls-section footer__controls-section--mobile">
            <div class="footer__controls footer__controls--mobile">
                <button class="player-btn player-btn--mobile" type="button" aria-label="Önceki şarkı" data-action="mgeri">
                    <svg width="14" height="14" viewBox="0 0 16 16"><polygon points="13,2 5,8 13,14" fill="currentColor"/><rect x="2" y="2" width="3" height="12" fill="currentColor"/></svg>
                </button>
                <button class="player-btn player-btn--play player-btn--mobile" type="button" aria-label="Oynat" data-action="mplay">
                    <svg width="18" height="18" viewBox="0 0 16 16"><polygon points="4,2 14,8 4,14" fill="currentColor"/></svg>
                </button>
                <button class="player-btn player-btn--mobile" type="button" aria-label="Duraklat" data-action="mpause">
                    <svg width="14" height="14" viewBox="0 0 16 16"><rect x="3" y="2" width="4" height="12" fill="currentColor"/><rect x="9" y="2" width="4" height="12" fill="currentColor"/></svg>
                </button>
                <button class="player-btn player-btn--mobile" type="button" aria-label="Sonraki şarkı" data-action="mileri">
                    <svg width="14" height="14" viewBox="0 0 16 16"><polygon points="3,2 11,8 3,14" fill="currentColor"/><rect x="13" y="2" width="2" height="12" fill="currentColor"/></svg>
                </button>
            </div>
        </section>
    </div>
</footer>
```

**CSS ekleme:** `_footer.css` içine:

```css
/* ============================================================
   FOOTER — Mobile Enhanced Layout
   ============================================================ */
.footer__inner--mobile {
    display: flex;
    align-items: center;
    gap: var(--space-2, 8px);
    padding: 6px 12px;
}

.footer__meta-section--mobile {
    display: flex;
    align-items: center;
    gap: var(--space-2, 8px);
    flex: 1;
    min-width: 0;
}

.footer__album-art--mobile {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-sm, 4px);
    object-fit: cover;
    flex-shrink: 0;
}

.footer__meta-stack--mobile {
    display: flex;
    flex-direction: column;
    min-width: 0;
    overflow: hidden;
}

.footer__song-name--mobile {
    font-size: 11px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.footer__album-name--mobile {
    font-size: 10px;
    color: rgba(255, 255, 255, 0.5);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.footer__controls--mobile {
    display: flex;
    align-items: center;
    gap: var(--space-1, 4px);
}

.player-btn--mobile {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: rgba(255, 255, 255, 0.9);
    cursor: pointer;
    border-radius: 50%;
    flex-shrink: 0;
}

.player-btn--play.player-btn--mobile {
    width: 48px;
    height: 48px;
    background: var(--accent, #ff4fd8);
    color: #ffffff;
}

.player-btn--mobile:focus-visible {
    outline: 2px solid var(--accent, #ff4fd8);
    outline-offset: 2px;
}
```

---

### Adım 7: Main CSS Import Zinciri Düzeltmesi

**Hedef dosya:** `assets.coremusic.net/Css/main.css`

**Sorun:** `_footer.css` ve `_home-components.css` sadece bridge üzerinden dolaylı import ediliyor. Doğrudan import eksik.

**Değişiklik:** `main.css` dosyasına şu satırları ekle (varsa mevcut bridge import'ların ALTINA):

```css
/* ═══ Direct imports (bridge after, ensures coverage) ═══ */
@import url("./03_Layout/_footer.css?v=5.0.0");           /* zaten var, kontrol et */
@import url("./05_Pages/_home-components.css?v=5.0.0");   /* bridge üzerinden de geliyor */
```

**Not:** `main.css`'te `_footer.css` zaten mevcut (satır 30). Sadece `_home-components.css` bridge üzerinden geliyor — bu zaten yeterli. Bu adımı atla, bridge yeterli.

---

### Adım 8: Device CSS Import Optimizasyonu

**Hedef dosyalar:** `d-embedded.css`, `d-phone.css`, `d-desktop.css`, `d-4k-tv.css`

**Sorun:** Tüm device CSS dosyaları base dosyaları tekrar import ediyor. Bu, ITCSS katmanlama prensibini zayıflatıyor.

**Öneri:** Device CSS dosyalarında tekrar eden import'ları kaldır, sadece device-specific override'ları bırak. Ancak bu büyük bir değişiklik ve mevcut sistemi bozabilir.

**Şimdilik:** Bu değişikliği ATLAMA — mevcut yapı çalışır durumda. İleride optimize edilebilir.

---

### Adım 9: Inline JS → ES Modülü Dönüşümü

**Hedef dosya:** `home.coremusic.net/pages/home.php`

**Sorun:** Welcome modal JS (satır 911-945) ve saat/tarih JS (satır 955-979) inline.

**Değişiklik:** Bu JS'leri ayrı dosyalara taşı:

**1. `assets.coremusic.net/js/features/WelcomeModal.js` oluştur:**

```javascript
/**
 * CoreMusic — WelcomeModal (ES Module)
 * Hoş Geldin popup yönetim modülü
 * @module WelcomeModal
 */
export default class WelcomeModal {
    constructor() {
        this.overlay = document.getElementById('welcomeModalOverlay');
        this.closeBtn = document.getElementById('welcomeModalClose');
        this.startBtn = document.getElementById('welcomeStartBtn');
        this.storageKey = 'cm_welcome_dismissed';
        
        if (!this.overlay) return;
        this.init();
    }

    init() {
        if (sessionStorage.getItem(this.storageKey)) {
            this.overlay.classList.add('is-hidden');
            return;
        }

        this.overlay.classList.remove('is-hidden');
        if (this.startBtn) this.startBtn.focus();

        this.closeBtn?.addEventListener('click', () => this.hide());
        this.startBtn?.addEventListener('click', () => this.hide());
        this.overlay.addEventListener('click', (e) => {
            if (e.target === this.overlay) this.hide();
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.overlay.classList.contains('is-hidden')) {
                this.hide();
            }
        });
    }

    hide() {
        this.overlay.classList.add('welcome-modal-overlay--closing');
        setTimeout(() => {
            this.overlay.classList.add('is-hidden');
            this.overlay.classList.remove('welcome-modal-overlay--closing');
        }, 220);
        try { sessionStorage.setItem(this.storageKey, '1'); } catch (e) {}
    }
}
```

**2. `assets.coremusic.net/js/features/ClockUpdater.js` oluştur:**

```javascript
/**
 * CoreMusic — ClockUpdater (ES Module)
 * Gerçek zamanlı saat ve tarih güncelleyici
 * @module ClockUpdater
 */
export default class ClockUpdater {
    constructor(intervalMs = 15000) {
        this.intervalMs = intervalMs;
        this.months = [
            'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
            'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'
        ];
        this.update();
        setInterval(() => this.update(), this.intervalMs);
    }

    update() {
        const now = new Date();
        const timeStr = String(now.getHours()).padStart(2, '0') + ':' + 
                        String(now.getMinutes()).padStart(2, '0');
        const dateStr = now.getDate() + ' ' + this.months[now.getMonth()] + ' ' + now.getFullYear();

        document.querySelectorAll('.homeClock').forEach(el => {
            el.textContent = timeStr;
        });
        document.querySelectorAll('.homeDate').forEach(el => {
            el.textContent = dateStr;
        });
    }
}
```

**3. `assets.coremusic.net/js/main.js`'e ekle:**

```javascript
import WelcomeModal from './features/WelcomeModal.js';
import ClockUpdater from './features/ClockUpdater.js';

// ... mevcut koddan sonra:
new WelcomeModal();
new ClockUpdater();
```

**4. `home.php`'den inline script'leri KALDIR:**
- Satır 911-945 (welcome modal inline JS) — sil
- Satır 955-979 (saat/tarih inline JS) — sil

---

### Adım 10: Wide Layout Bottom Row Card Sayısı Artırımı

**Hedef dosya:** `home.coremusic.net/pages/home.php` (v10.0.0)

**Sorun:** Wide layout bottom'da sadece 1'er kart var, embedded'de 4'er kart var.

**Değişiklik:** `$isWide` bloğunda bottom sütunlarına fazladan kart ekle:

```php
<!-- SÜTUN 1: En Son Dinlenen Şarkılar (Wide — en az 3 kart) -->
<section class="home-layout__bottom-col home-layout__bottom-col--wide" aria-label="En Son Dinlenen Şarkılar">
    <h2 class="home-column-header">En Son Dinlenen Şarkılar</h2>
    <div class="home-cards-wrapper--wide">
        <a href="#" class="home-recent-card home-recent-card--desktop" role="button" aria-label="Göksel - Sevil Neşelen">
            <div class="home-recent-card__art">
                <img src="<?= $assetsUrl ?>/Image/res-pink/album-kursat.png" alt="" loading="lazy" width="64" height="64">
            </div>
            <div class="home-recent-card__info">
                <p class="home-recent-card__title">Şarkı Adı : Göksel - Sevil Neşelen</p>
                <p class="home-recent-card__sub">Albüm : Hayat Rüya Gibi &nbsp;•&nbsp; Göksel</p>
            </div>
        </a>
        <a href="#" class="home-recent-card home-recent-card--desktop" role="button" aria-label="Barış Manço - Gülpembe">
            <div class="home-recent-card__art">
                <img src="<?= $assetsUrl ?>/Image/res-pink/album-kursat.png" alt="" loading="lazy" width="64" height="64">
            </div>
            <div class="home-recent-card__info">
                <p class="home-recent-card__title">Şarkı Adı : Barış Manço - Gülpembe</p>
                <p class="home-recent-card__sub">Albüm : Klasikler &nbsp;•&nbsp; Barış Manço</p>
            </div>
        </a>
    </div>
</section>

<!-- SÜTUN 2: Son Oluşturulan & Sistem Playlistleri (Wide — en az 3 kart) -->
<section class="home-layout__bottom-col home-layout__bottom-col--wide" aria-label="Son Oluşturulan ve Sistem Çalma Listeleri">
    <h2 class="home-column-header">Son Oluşturulan &amp; Sistem Playlistleri</h2>
    <div class="home-cards-wrapper--wide">
        <a href="#" class="home-recent-card home-recent-card--desktop" role="button" aria-label="En Sevilen Popmikslerim">
            <div class="home-recent-card__art">
                <img src="<?= $assetsUrl ?>/Image/res-pink/album-kursat.png" alt="" loading="lazy" width="64" height="64">
            </div>
            <div class="home-recent-card__info">
                <p class="home-recent-card__title">En Sevilen Popmikslerim</p>
                <p class="home-recent-card__sub">Sistem Tarafından Oluşturuldu &nbsp;•&nbsp; 00:03:06</p>
            </div>
        </a>
        <a href="#" class="home-recent-card home-recent-card--desktop" role="button" aria-label="Kendime Özel Pop Müziklerim">
            <div class="home-recent-card__art">
                <img src="<?= $assetsUrl ?>/Image/res-pink/album-kursat.png" alt="" loading="lazy" width="64" height="64">
            </div>
            <div class="home-recent-card__info">
                <p class="home-recent-card__title">Kendime Özel Pop Müziklerim</p>
                <p class="home-recent-card__sub">Sistem Tarafından Oluşturuldu &nbsp;•&nbsp; 01:05:07</p>
            </div>
        </a>
    </div>
</section>
```

---

## Bölüm C: Uygulama Sırası ve Bağımlılıklar

```
Adım 1 (WCAG Touch Target) — CSS only, bağımsız
    ↓
Adım 2 (Wide Layout Bottom) — PHP template değişikliği
    ↓
Adım 3 (Phone Welcome Modal) — PHP template + CSS
    ↓
Adım 4 (Phone Sıradaki) — PHP template
    ↓
Adım 5 (Header Inline Style) — PHP + CSS
    ↓
Adım 6 (Footer Phone) — PHP + CSS
    ↓
Adım 7 (Main CSS Import) — ATLA (zaten yeterli)
    ↓
Adım 8 (Device CSS Opt) — ATLA (şimdilik)
    ↓
Adım 9 (Inline JS → ES Module) — JS + PHP
    ↓
Adım 10 (Wide Layout Cards) — PHP template
```

**Toplam Etkilenen Dosya:** 7  
**Toplam Değişiklik:** 10 adım  

| Dosya | Değişiklik Tipi | Adımlar |
|-------|----------------|---------|
| `_home-components.css` | CSS ekle/değiştir | 1.1, 1.3, 1.4, 1.5, 3 |
| `_header.css` | CSS ekle/değiştir | 1.2 |
| `_footer.css` | CSS ekle | 6 |
| `b-base-core.css` | CSS ekle | 5 |
| `home.php` | PHP template değiştir | 2, 3, 4, 6, 9, 10 |
| `header.php` | PHP template değiştir (inline sil) | 5 |
| `main.js` | JS import ekle | 9 |

---

## Bölüm D: Doğrulama Kontrol Listesi

Uygulama tamamlandıktan sonra kontrol:

- [ ] Welcome "Başla" butonu en az 48px yüksekliğinde mi?
- [ ] Nav link'ler en az 48×48px touch target'e sahip mi?
- [ ] Star rating satırı en az 48px yükseklikte mi?
- [ ] Genre tabs en az 48px yükseklikte mi?
- [ ] Track row'lar en az 48px yükseklikte mi?
- [ ] Wide layout bottom'da 3 sütun dolu mu?
- [ ] Phone layout'da welcome modal görünüyor mu?
- [ ] Phone layout'da "Sıradaki" bölümü var mı?
- [ ] Header'da inline `<style>` kalktı mı?
- [ ] Phone footer'da album art + metadata görünüyor mu?
- [ ] Inline JS'ler ES modülüne dönüştürüldü mü?
- [ ] Tüm cihazlarda (Phone/Embedded/Wide/4K) test edildi mi?
- [ ] WCAG 2.2 AA touch target kriterleri sağlanıyor mu?
- [ ] CSP header ile uyumlu mu (inline style/script yok)?
- [ ] BEM naming convention korunuyor mu?

---

## Bölüm E: Risk Değerlendirmesi

| Risk | Olasılık | Etki | Önleme |
|------|----------|------|--------|
| CSS değişikliği mevcut layout'u bozar | Düşük | Yüksek | Token-based fallback kullan, `var(--token, fallback)` |
| Phone footer değişikliği JS hata verir | Orta | Orta | `getElementById` null kontrolü mevcut |
| Welcome modal JS modülü yüklenmezse | Düşük | Düşük | DOMContentLoaded wrapper korunur |
| Inline style kaldırma CSP bozar | Düşük | Yüksek | CSS'e taşıma zaten CSP-uyumlu |

---

*ui-duzelt.md v1.0.0 — CoreMusic Frontend Revision Guide*  
*Authority: UI Designer Agent*  
*Date: 2026-09-04*  
*Mode: Red Team · Human Mode · Truth Mode*
