---
title: "Yeniden Kullanılabilir UI Bileşen Kütüphanesi"
layer: K11
category: "Kullanıcı Deneyimi"
date: 2026-09-20
---

# UI Bileşen Kütüphanesi

## Genel Bakış

COREMUSIC UI Kütüphanesi, reusable ve composable bileşenlerden oluşan bir design system'dir. Her bileşen BEM isimlendirme, erişilebilirlik, tema desteği ve responsive tasarım kurallarına uyar. Vanilla JS veya Alpine.js ile interaktif davranışlar sağlanır.

## Bileşen Kataloğu

```
ui-components/
├── primitives/          ← Atom bileşenler
│   ├── _button.scss
│   ├── _input.scss
│   ├── _badge.scss
│   ├── _avatar.scss
│   ├── _icon.scss
│   └── _tooltip.scss
├── molecules/           ← Bileşik bileşenler
│   ├── _card.scss
│   ├── _form-group.scss
│   ├── _search.scss
│   ├── _dropdown.scss
│   ├── _modal.scss
│   └── _toast.scss
├── organisms/           ← Karmaşık bileşenler
│   ├── _header.scss
│   ├── _sidebar.scss
│   ├── _player.scss
│   ├── _playlist.scss
│   ├── _tracklist.scss
│   └── _album-grid.scss
└── templates/           ← Sayfa düzenleri
    ├── _home.scss
    ├── _library.scss
    └── _player-page.scss
```

## Primitives (Atom Bileşenler)

### Button

```html
<!-- Button varyantları -->
<button class="btn btn--primary">Oynat</button>
<button class="btn btn--secondary">Kaydet</button>
<button class="btn btn--ghost">İptal</button>
<button class="btn btn--danger">Sil</button>
<button class="btn btn--icon" aria-label="Ayarlar">
  <svg aria-hidden="true"><!-- icon --></svg>
</button>
<button class="btn btn--primary btn--sm">Küçük</button>
<button class="btn btn--primary btn--lg">Büyük</button>
<button class="btn btn--primary btn--block">Tam Genişlik</button>
<button class="btn btn--primary" disabled>Devre Dışı</button>
<button class="btn btn--primary btn--loading">
  <span class="btn__spinner"></span>
  Yükleniyor...
</button>
```

```scss
// primitives/_button.scss
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: $spacing-2;
  padding: $spacing-3 $spacing-6;
  font-size: $font-size-sm;
  font-weight: 600;
  line-height: 1;
  border: 1px solid transparent;
  border-radius: $radius-md;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
  user-select: none;

  @include focus-visible;

  // Sizes
  &--sm {
    padding: $spacing-2 $spacing-4;
    font-size: $font-size-xs;
    border-radius: $radius-sm;
  }

  &--lg {
    padding: $spacing-4 $spacing-8;
    font-size: $font-size-base;
    border-radius: $radius-lg;
  }

  // Variants
  &--primary {
    background: var(--color-primary);
    color: var(--color-text-inverse);

    &:hover { background: var(--color-primary-hover); }
  }

  &--secondary {
    background: transparent;
    color: var(--color-primary);
    border-color: var(--color-primary);

    &:hover {
      background: var(--color-primary);
      color: var(--color-text-inverse);
    }
  }

  &--ghost {
    background: transparent;
    color: var(--color-text);

    &:hover { background: var(--color-bg-subtle); }
  }

  &--danger {
    background: var(--color-error);
    color: var(--color-text-inverse);

    &:hover { background: darken(#EF4444, 8%); }
  }

  // Icon button
  &--icon {
    padding: $spacing-2;
    aspect-ratio: 1;
    border-radius: $radius-full;
  }

  // Block
  &--block {
    width: 100%;
  }

  // Loading
  &--loading {
    pointer-events: none;
    opacity: 0.8;
  }

  // Disabled
  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
  }

  // Spinner
  &__spinner {
    width: 1em;
    height: 1em;
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
```

### Input

```html
<!-- Input varyantları -->
<div class="input-group">
  <label class="input-group__label" for="name">Ad</label>
  <input class="input-group__input" type="text" id="name" placeholder="Adınızı girin" />
  <span class="input-group__hint">Zorunlu alan</span>
</div>

<div class="input-group input-group--error">
  <label class="input-group__label" for="email">E-posta</label>
  <input class="input-group__input" type="email" id="email" aria-invalid="true" aria-describedby="email-error" />
  <span class="input-group__error" id="email-error">Geçerli bir e-posta girin</span>
</div>

<div class="input-group input-group--success">
  <label class="input-group__label" for="username">Kullanıcı adı</label>
  <input class="input-group__input" type="text" id="username" aria-invalid="false" />
  <span class="input-group__success">Kullanılabilir!</span>
</div>

<!-- Search input -->
<div class="search">
  <svg class="search__icon" aria-hidden="true"><!-- search icon --></svg>
  <input class="search__input" type="search" placeholder="Şarkı, sanatçı veya albüm ara..." aria-label="Ara" />
  <kbd class="search__shortcut">⌘K</kbd>
</div>
```

```scss
// primitives/_input.scss
.input-group {
  display: flex;
  flex-direction: column;
  gap: $spacing-1;

  &__label {
    font-size: $font-size-sm;
    font-weight: 500;
    color: var(--color-text);
  }

  &__input {
    width: 100%;
    padding: $spacing-3 $spacing-4;
    font-size: $font-size-base;
    color: var(--color-text);
    background: var(--color-bg);
    border: 1px solid var(--color-border);
    border-radius: $radius-md;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;

    &::placeholder {
      color: var(--color-text-muted);
    }

    &:hover {
      border-color: var(--color-border-strong);
    }

    &:focus {
      outline: none;
      border-color: var(--color-primary);
      box-shadow: 0 0 0 3px var(--color-primary-light);
    }

    // Error state
    .input-group--error & {
      border-color: var(--color-error);

      &:focus {
        box-shadow: 0 0 0 3px rgba(#EF4444, 0.1);
      }
    }

    // Success state
    .input-group--success & {
      border-color: var(--color-success);

      &:focus {
        box-shadow: 0 0 0 3px rgba(#22C55E, 0.1);
      }
    }
  }

  &__hint {
    font-size: $font-size-xs;
    color: var(--color-text-muted);
  }

  &__error {
    font-size: $font-size-xs;
    color: var(--color-error);
  }

  &__success {
    font-size: $font-size-xs;
    color: var(--color-success);
  }
}
```

## Molecules (Bileşik Bileşenler)

### Card

```html
<div class="card card--interactive" tabindex="0">
  <div class="card__media">
    <img class="card__image" src="cover.webp" alt="Albüm kapağı" loading="lazy" />
    <button class="card__play" aria-label="Oynat">
      <svg aria-hidden="true"><!-- play icon --></svg>
    </button>
  </div>
  <div class="card__content">
    <h3 class="card__title">Albüm Adı</h3>
    <p class="card__subtitle">Sanatçı</p>
    <p class="card__meta">2026 · 12 şarkı · 45 dakika</p>
  </div>
  <div class="card__actions">
    <button class="btn btn--icon btn--ghost" aria-label="Favorilere ekle">
      <svg aria-hidden="true"><!-- heart icon --></svg>
    </button>
    <button class="btn btn--icon btn--ghost" aria-label="Daha fazla">
      <svg aria-hidden="true"><!-- more icon --></svg>
    </button>
  </div>
</div>
```

### Modal

```html
<div class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal">
    <div class="modal__header">
      <h2 class="modal__title" id="modal-title">Şarkı Ekle</h2>
      <button class="modal__close" aria-label="Kapat">
        <svg aria-hidden="true"><!-- close icon --></svg>
      </button>
    </div>
    <div class="modal__body">
      <p>İçerik buraya gelecek.</p>
    </div>
    <div class="modal__footer">
      <button class="btn btn--ghost" data-dismiss="modal">İptal</button>
      <button class="btn btn--primary">Ekle</button>
    </div>
  </div>
</div>
```

### Toast

```html
<div class="toast-container" aria-live="polite">
  <div class="toast toast--success" role="alert">
    <svg class="toast__icon" aria-hidden="true"><!-- check icon --></svg>
    <div class="toast__content">
      <p class="toast__title">Başarılı</p>
      <p class="toast__message">Şarkı favorilere eklendi.</p>
    </div>
    <button class="toast__close" aria-label="Kapat">
      <svg aria-hidden="true"><!-- close icon --></svg>
    </button>
  </div>
</div>
```

### Dropdown

```html
<div class="dropdown">
  <button class="dropdown__trigger" aria-haspopup="true" aria-expanded="false">
    Sırala
    <svg aria-hidden="true"><!-- chevron icon --></svg>
  </button>
  <div class="dropdown__menu" role="menu">
    <button class="dropdown__item" role="menuitem">Başlığa göre</button>
    <button class="dropdown__item" role="menuitem">Sanatçıya göre</button>
    <button class="dropdown__item" role="menuitem">Tarihe göre</button>
    <div class="dropdown__divider" role="separator"></div>
    <button class="dropdown__item" role="menuitem">Özel sıralama</button>
  </div>
</div>
```

## Organisms (Karmaşık Bileşenler)

### Player Bar

```html
<footer class="player" role="region" aria-label="Müzik player">
  <div class="player__track">
    <img class="player__artwork" src="cover.webp" alt="" />
    <div class="player__info">
      <span class="player__title">Şarkı Adı</span>
      <span class="player__artist">Sanatçı</span>
    </div>
    <button class="btn btn--icon btn--ghost" aria-label="Favorilere ekle">
      <svg aria-hidden="true"><!-- heart icon --></svg>
    </button>
  </div>

  <div class="player__controls">
    <button class="player__btn" aria-label="Karıştır">
      <svg aria-hidden="true"><!-- shuffle icon --></svg>
    </button>
    <button class="player__btn" aria-label="Önceki">
      <svg aria-hidden="true"><!-- prev icon --></svg>
    </button>
    <button class="player__btn player__btn--play" aria-label="Oynat">
      <svg aria-hidden="true"><!-- play icon --></svg>
    </button>
    <button class="player__btn" aria-label="Sonraki">
      <svg aria-hidden="true"><!-- next icon --></svg>
    </button>
    <button class="player__btn" aria-label="Tekrarla">
      <svg aria-hidden="true"><!-- repeat icon --></svg>
    </button>
  </div>

  <div class="player__progress">
    <span class="player__time">1:23</span>
    <div class="player__bar" role="slider" aria-label="İlerleme" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
      <div class="player__bar-fill" style="width: 45%"></div>
    </div>
    <span class="player__time">3:45</span>
  </div>

  <div class="player__volume">
    <button class="player__btn" aria-label="Ses">
      <svg aria-hidden="true"><!-- volume icon --></svg>
    </button>
    <div class="player__bar player__bar--volume" role="slider" aria-label="Ses seviyesi" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
      <div class="player__bar-fill" style="width: 75%"></div>
    </div>
  </div>
</footer>
```

## Bileşen Kullanım Kuralları

| Kural | Açıklama |
|---|---|
| Primitives | Tek başına kullanılabilir |
| Molecules | 2-3 primitive bileşerden oluşur |
| Organisms | Sayfa düzeyinde karmaşık bileşenler |
| Template | Sayfa düzeni tanımlar |
| Composition | Bileşenler birbiriyle compose edilir |
| Props | BEM modifier ile varyantlar oluşturulur |

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|---|---|---|
| dart-sass | 1.77+ | SCSS compilation |
| Alpine.js | 3.13+ | Hafif reaktivite (opsiyonel) |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Sorumlu**: K11 UX Team
**Başlangıç**: 2026-Q4
**Hedef Bitiş**: 2027-Q1
**Öncelik**: Yüksek (UI standardization)
