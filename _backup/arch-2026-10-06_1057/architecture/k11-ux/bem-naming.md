---
title: "BEM Naming Convention"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# BEM Naming Convention

## Genel Bakış

BEM (Block-Element-Modifier), CSS selector'larının maintainability ve readability için standardize edildiği bir metodolojidir. COREMUSIC'te tüm CSS sınıfları BEM kurallarına uyar. Bu, large-scale projelerde CSS conflict'lerini önler ve component-based geliştirme sağlar.

## Temel Kavramlar

```
.block                    ← Bağımsız bileşen
.block__element           ← Bileşen içindeki alt parça
.block--modifier          ← Bileşen varyantı
.block__element--modifier ← Alt parçanın varyantı
```

## Kurallar

### 1. Block

Bir blok, bağımsız olarak yeniden kullanılabilen bileşendir. Hiçbir dış bağımlılığı yoktur.

```scss
// ✅ DOĞRU: Bağımsız blok isimleri
.card { }
.button { }
.player { }
.playlist { }
.sidebar { }
.modal { }

// ❌ YANLIŞ: Block isimleri specifity artırmaz
.card-container-wrapper { }  // Çok uzun
.card__title__link { }       // Triple nesting yasak
```

### 2. Element

Bir element, bloğun parçasıdır ve yalnızca bulunduğu bloğun içinde anlamlıdır.

```scss
// ✅ DOĞRU: Tek alt çizgi (__) ile ayrılır
.card { }
.card__header { }
.card__body { }
.card__footer { }
.card__image { }
.card__title { }
.card__meta { }

// ❌ YANLIŞ: Element zinciri yasak
.card__header__title { }  // Triple nesting yasak
.card__header__title__link { }  // Anlam kaybı
```

### 3. Modifier

Bir modifier, bloğun veya elementin varyantıdır.

```scss
// ✅ DOĞRU: İki tire (--) ile ayrılır
.card--dark { }
.card--featured { }
.card--horizontal { }
.btn--primary { }
.btn--secondary { }
.btn--ghost { }
.btn--sm { }
.btn--lg { }
.input--error { }
.input--success { }

// ❌ YANLIŞ: Modifier nesting
.card--dark__title { }  // Block ve modifier ayrı tutulmalı
```

## İsimlendirme Kuralları

### Kurallar

| Kural | Doğru | Yanlış |
|---|---|---|
| Block ismi lowercase | `card` | `Card`, `CARD` |
| Kelimeler tire ile | `audio-player` | `audioPlayer`, `audio_player` |
| Element alt çizgi | `card__title` | `card-title`, `card-title` |
| Modifier tire-tire | `btn--primary` | `btn-primary`, `btn_primary` |
| Bileşik isimler | `now-playing` | `nowPlaying` |
| Short ve semantic | `nav` | `navigation-container` |

### Semantic İsimlendirme

```scss
// ✅ DOĞRU: Anlam bilinçli isimler
.player { }           // Müzik player
.player__controls { } // Kontrol butonları
.player__progress { } // Progress bar
.player__volume { }   // Ses kontrolü
.player__time { }     // Zaman gösterimi

// ❌ YANLIŞ: Görsel isimler
.box { }              // "box" ne anlama geliyor?
.big-box { }          // Boyut modifier değil
.blue-box { }         // Renk modifier değil
```

## Kod Örnekleri

### Temel BEM Yapısı

```html
<!-- Card Component -->
<div class="card card--dark card--featured">
  <div class="card__header">
    <img class="card__image" src="cover.webp" alt="Albüm kapağı" />
    <span class="card__badge card__badge--new">Yeni</span>
  </div>
  <div class="card__body">
    <h3 class="card__title">Album Title</h3>
    <p class="card__meta">
      <span class="card__artist">Artist Name</span>
      <span class="card__separator">·</span>
      <span class="card__year">2026</span>
    </p>
  </div>
  <div class="card__footer">
    <button class="btn btn--primary btn--sm">Dinle</button>
    <button class="btn btn--ghost btn--sm">Kaydet</button>
  </div>
</div>
```

### Player Component

```html
<!-- Audio Player -->
<div class="player" role="region" aria-label="Müzik player">
  <div class="player__artwork">
    <img class="player__image" src="cover.webp" alt="Cover" />
  </div>
  <div class="player__info">
    <h3 class="player__title">Şarkı Adı</h3>
    <p class="player__artist">Sanatçı</p>
  </div>
  <div class="player__controls">
    <button class="player__btn player__btn--prev" aria="Önceki">
      <!-- icon -->
    </button>
    <button class="player__btn player__btn--play" aria="Oynat">
      <!-- icon -->
    </button>
    <button class="player__btn player__btn--next" aria="Sonraki">
      <!-- icon -->
    </button>
  </div>
  <div class="player__progress">
    <div class="player__progress-bar">
      <div class="player__progress-fill" style="width: 45%"></div>
    </div>
    <div class="player__time">
      <span class="player__time-current">1:23</span>
      <span class="player__time-total">3:45</span>
    </div>
  </div>
  <div class="player__volume">
    <button class="player__btn player__btn--mute" aria="Sessiz">
      <!-- icon -->
    </button>
    <input class="player__volume-slider" type="range" min="0" max="100" value="75" />
  </div>
</div>
```

### Form Component

```html
<!-- Form Elements -->
<form class="form" novalidate>
  <div class="form__group">
    <label class="form__label" for="email">E-posta</label>
    <input class="form__input form__input--error" type="email" id="email" aria-invalid="true" aria-describedby="email-error" />
    <span class="form__error" id="email-error" role="alert">
      Geçerli bir e-posta girin.
    </span>
  </div>

  <div class="form__group">
    <label class="form__label" for="password">Şifre</label>
    <div class="form__input-wrapper">
      <input class="form__input" type="password" id="password" />
      <button class="form__toggle-password" aria-label="Şifreyi göster">
        <!-- icon -->
      </button>
    </div>
    <span class="form__hint">En az 8 karakter olmalı.</span>
  </div>

  <div class="form__group">
    <div class="form__checkbox">
      <input class="form__checkbox-input" type="checkbox" id="remember" />
      <label class="form__checkbox-label" for="remember">Beni hatırla</label>
    </div>
  </div>

  <button class="btn btn--primary btn--block" type="submit">Giriş Yap</button>
</form>
```

### SCSS BEM Yapısı

```scss
// components/_card.scss

.card {
  // Base styles
  display: flex;
  flex-direction: column;
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: $border-radius;
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;

  // Elements
  &__header {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
  }

  &__image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  &__badge {
    position: absolute;
    top: $spacing-unit;
    right: $spacing-unit;
    padding: $spacing-unit * 0.5 $spacing-unit;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-radius: $border-radius;

    &--new {
      background: $color-success;
      color: $color-white;
    }

    &--premium {
      background: $color-gold;
      color: $color-dark;
    }
  }

  &__body {
    padding: $spacing-unit * 2;
    flex: 1;
  }

  &__title {
    font-size: 1rem;
    font-weight: 600;
    @include truncate(1);
  }

  &__meta {
    font-size: 0.75rem;
    color: var(--color-text-muted);
    margin-top: $spacing-unit * 0.5;
  }

  &__separator {
    margin: 0 $spacing-unit * 0.5;
  }

  &__footer {
    padding: $spacing-unit * 2;
    border-top: 1px solid var(--color-border);
    display: flex;
    gap: $spacing-unit;
  }

  // Modifiers
  &--dark {
    background: var(--color-bg-elevated);
  }

  &--featured {
    border-color: $color-primary;

    .card__image {
      transform: scale(1.05);
    }
  }

  &--horizontal {
    flex-direction: row;

    .card__header {
      width: 120px;
      aspect-ratio: 1;
    }

    .card__body {
      flex: 1;
    }
  }

  // Hover states
  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px var(--color-shadow);
  }

  &:hover &__image {
    transform: scale(1.08);
  }

  @include focus-visible {
    outline: 2px solid $color-primary;
    outline-offset: 2px;
  }
}
```

## Anti-Patternler

### ❌ Kodicals

```scss
// ❌ YANLIŞ: Kodical kullanma
.card-title { }      // Kodical = BEM'e aykırı
.card-title-large { } // Kodical + modifier kafa karıştırıcı

// ✅ DOĞRU: BEM kullan
.card__title { }
.card__title--large { }
```

### ❌ Namespace

```scss
// ❌ YANLIŞ: Namespace ekleme
.c-card { }
.c-button { }
.o-grid { }
.u-text-center { }

// ✅ DOĞRU: Namespace yok
.card { }
.button { }
.grid { }
.text-center { }
```

### ❌ Hungarian Notation

```scss
// ❌ YANLIŞ: Tibiharian notation
.bg-card { }    // Renk namespace
.btn-primary { } // Renk modifier ama BEM'de '--' olmalı
.fs-14 { }      // Font size

// ✅ DOĞRU: BEM'e sadık kal
.card { }
.btn--primary { }
.text-lg { }
```

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (CSS isimlendirme standardı)
