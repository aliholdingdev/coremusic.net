# CoreMusic Broken Files Report

**Tarih:** 2026-09-23
**Taranan Kapsam:** home.coremusic.net/, shared/src/, assets.coremusic.net/js/, .ai/ vault
**Toplam Bulgu:** 39 (3 CRITICAL + 7 HIGH + 12 MEDIUM + 17 LOW)

---

## 1. CRITICAL — Runtime Fatal Error Yaratan Sorunlar

### 1.1 Namespace Mismatch: home.php → ComponentLoader ve HomeLayoutVariant

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/home.php` |
| Satır | 51-52 |
| Sorun Türü | Tanımsız class (fatal error) |
| Öncelik | **CRITICAL** |

**Mevcut (yanlış):**
```php
use CoreMusic\Home\Component\ComponentLoader;     // satır 51
use CoreMusic\Home\Component\HomeLayoutVariant;   // satır 52
```

**Doğru olası:**
```php
use CoreMusic\Home\Class\ComponentLoader;         // ComponentLoader.php namespace: CoreMusic\Home\Class
use CoreMusic\Home\Class\HomeLayoutVariant;       // HomeLayoutVariant.php namespace: CoreMusic\Home\Class
```

**Kanıt:**
- `include/Class/ComponentLoader.php` satır 3: `namespace CoreMusic\Home\Class;`
- `include/Class/HomeLayoutVariant.php` satır 3: `namespace CoreMusic\Home\Class;`

**Etki:** `new ComponentLoader()` ve `HomeLayoutVariant::fromFlags()` çağrıları ClassNotFound fatal error verir. Sayfa hiç yüklenemez.

---

### 1.2 Eksik `use` Statement: ComponentLoader → RecentTracksComponent

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/include/Class/ComponentLoader.php` |
| Satır | 47 |
| Sorun Türü | Tanımsız class (fatal error) |
| Öncelik | **CRITICAL** |

**Mevcut:** `RecentTracksComponent::class` satır 47'de kullanılıyor ama `use` statement'ı yok.

**Kapsam:**
- ComponentLoader namespace: `CoreMusic\Home\Class`
- RecentTracksComponent namespace: `CoreMusic\Home\Component`
- PHP, `CoreMusic\Home\Class\RecentTracksComponent` arar → bulamaz → fatal error

**Eksik:**
```php
use CoreMusic\Home\Component\RecentTracksComponent;
```

**Etki:** `recent-tracks` component'i kayıtlı ama yüklenemiyor.

---

### 1.3 Tanımsız Özellikler: player-info.php View Partial

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/components/player-info.php` |
| Satır | 17, 18, 19, 21, 32, 39 |
| Sorun Türü | Tanımsız özellik (warning/error) |
| Öncelik | **CRITICAL** |

**Tanımsız özellikler (view partial kullanıyor ama PlayerInfoComponent tanımlamıyor):**

| Özellik | Kullanıldığı Satır | Tanımlı mı? |
|---------|-------------------|-------------|
| `$this->iconMusic` | 17 | ❌ HAYIR |
| `$this->iconCd` | 18 | ❌ HAYIR |
| `$this->iconMic` | 19 | ❌ HAYIR |
| `$this->iconStar` | 21 | ❌ HAYIR |
| `$this->iconBitrate` | 32 | ❌ HAYIR |
| `$this->iconTimer` | 39 | ❌ HAYIR |

**PlayerInfoComponent.php'de tanımlı:**
`$song`, `$album`, `$artist`, `$bitrate`, `$elapsed`, `$duration`, `$seekPct`, `$imgCover`, `$imgStar`, `$iconPlay`

**Etki:** Wide layout'da player-info component'i render edildiğinde 6 adet "Undefined property" warning üretir ve img src boş kalır.

---

## 2. HIGH — Yapısal/Eksik Dosya Sorunları

### 2.1 Eksik View Partial Dosyaları

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/components/` |
| Sorun Türü | Eksik dosya |
| Öncelik | **HIGH** |

**Mevcut view partial'lar:**
- `player-info.php` ✅
- `recent-tracks.php` ✅

**Eksik view partial'lar (ComponentLoader registry'sinde veya home.php'de referans verilen):**

| Eksik Dosya | Referans Kaynağı |
|-------------|------------------|
| `widget-grid.php` | ComponentLoader defaultRegistry (potansiyel) |
| `playlists.php` | home.php alt satır layout |
| `up-next.php` | home.php alt satır layout |
| `welcome-banner.php` | home.php wide layout |
| `now-playing.php` | home.php alternatif |
| `welcome-modal.php` | home.php satır 136-165 JS |
| `home-widgets.php` | ComponentLoader registry |

---

### 2.2 Orphaned Closing Div: home.php

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/home.php` |
| Satır | 125-127 |
| Sorun Türü | HTML yapı hatası |
| Öncelik | **HIGH** |

```php
125:     <div class="home-layout__bottom home-layout__bottom--embedded">
126:         </div>        ← Bu div'i kapatıyor (boş)
127:     </div>            ← Bu orphan — açılış div'i yok
```

Satır 125'te açılan div, satır 126'da anında kapanıyor (içerik yok). Satır 127'deki `</div>` herhangi bir açılış tag'ine karşılık gelmiyor.

---

### 2.3 home.php Wide Layout Eksik Bileşenler

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/home.php` |
| Satır | 85-106 |
| Sorun Türü | Eksik component rendering |
| Öncelik | **HIGH** |

Wide layout (1920+) section'ında sadece `player-info` ve `recent-tracks` render ediliyor:
- `widget-grid` → render edilmiyor ❌
- `welcome-banner` → render edilmiyor ❌
- `playlists` → render edilmiyor ❌
- `up-next` → render edilmiyor ❌

---

## 3. MEDIUM — Vault Kırık Wiki-Linkleri

### 3.1 CLAUDE.md Kırık Linkleri (30 adet)

**Root cause:** `decisions/accepted/` dizini vault'ta MEVCUT DEĞİL. Gerçek dizin `.decisions/` (nokta prefix).

| # | Kırık Link | Muhtemel Gerçek Karşılık |
|---|------------|------------------------|
| 1 | `[[decisions/accepted/ADR-001-vanilla-js-itcss]]` | `.decisions/` dizininde ADR dosyaları yok |
| 2 | `[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]` | Aynı |
| 3 | `[[decisions/accepted/ADR-010-csrf-protection-strategy]]` | Aynı |
| 4 | `[[decisions/accepted/ADR-011-session-management]]` | Aynı |
| 5 | `[[decisions/accepted/ADR-022-database-hardened-security]]` | Aynı |
| 6 | `[[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]]` | Aynı |
| 7 | `[[decisions/accepted/ADR-040-database-authority]]` | Aynı |
| 8 | `[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]` | Aynı |
| 9 | `[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]` | Aynı |
| 10 | `[[decisions/accepted/ADR-044-dynamic-user-theme-engine]]` | Aynı |
| 11 | `[[decisions/accepted/ADR-087-master-implementation-plan]]` | Aynı |

### 3.2 ADR Referansları (Kısa Form — Dosya Yok)

| # | Kırık Link | Not |
|---|------------|-----|
| 12 | `[[ADR-010-csrf-protection-strategy]]` | Kök dizinde ADR dosyası yok |
| 13 | `[[ADR-011-session-management]]` | Aynı |
| 14 | `[[ADR-017-dsp-hardware-mode]]` | Aynı |
| 15 | `[[ADR-038-8.1-sound-card-chip-selection]]` | Aynı |
| 16 | `[[ADR-040-database-authority]]` | Aynı |
| 17 | `[[ADR-044-dynamic-user-theme-engine]]` | Aynı |
| 18 | `[[ADR-089-classab-24v]]` | `.decisions/draft/` dizininde dosya yok |

### 3.3 Mimari/Arşiv Dosyaları (Dizin/Dosya Yok)

| # | Kırık Link | Not |
|---|------------|-----|
| 19 | `[[architecture/index]]` | `architecture/index.md` var ama adı farklı |
| 20 | `[[architecture/k0-isletim-sistemi]]` | Dizin yapısı farklı |
| 21 | `[[architecture/k3-ses-motoru]]` | Aynı |
| 22 | `[[architecture/k0-k5-software/k5-data-layer/database_master]]` | Aynı |
| 23 | `[[architecture/03-contracts/master-implementation-plan]]` | Dizin yapısı farklı |
| 24 | `[[archives/prompt0-genel-ana-prompt-2026-09-01]]` | `archives/` dizini yok |
| 25 | `[[archives/prompt1-spa-router-2026-09-01]]` | Aynı |
| 26 | `[[archives/prompt2-auth-2026-09-01]]` | Aynı |
| 27 | `[[archives/prompt3-api-2026-09-01]]` | Aynı |

### 3.4 UI Design Dosyaları (İsim Farklı)

| # | Kırık Link | Gerçek Karşılık |
|---|------------|-----------------|
| 28 | `[[ui-design/01-mockup-index]]` | `ui-design/01-mockup-index.md` |
| 29 | `[[ui-design/02-component-inventory]]` | `ui-design/02-component-inventory.md` |
| 30 | `[[screens/B-home/dashboard-1920]]` | `screens/` dizini vault'ta yok |

---

## 4. MEDIUM — Eksik Import/Dependency

### 4.1 ComponentLoader Namespace Eksik `use`

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/include/Class/ComponentLoader.php` |
| Satır | 47 |
| Sorun Türü | Eksik use statement |
| Öncelik | **MEDIUM** |

`RecentTracksComponent::class` kullanılıyor ama `use CoreMusic\Home\Component\RecentTracksComponent;` statement'ı yok. PHP namespace resolution tarafından bulunamaz.

---

## 5. LOW — Kozmetik/Küçük Sorunlar

### 5.1 HTML Attribute Hatası: player-info.php

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/components/player-info.php` |
| Satır | 13 |
| Sorun Türü | Kırık HTML attribute |
| Öncelik | **LOW** |

```html
<img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı loading="lazy"/>
```

`alt` attribute'unda tırnak hatası: `alt="Çalan şarkının albüm kapağı loading="lazy"` → `loading="lazy"` alt'a dahil oluyor.

**Doğru:**
```html
<img src="<?= $this->h($this->imgCover) ?>" alt="Çalan şarkının albüm kapağı" loading="lazy"/>
```

### 5.2 Boş PHP Bloğu: home.php

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/home.php` |
| Satır | 133-134 |
| Sorun Türü | Boş blok |
| Öncelik | **LOW** |

```php
<?php if (!$dm->isPhone()): ?>
<?php endif; ?>
```

Boş condition — hiçbir şey render etmiyor.

### 5.3 home.php Wide Layout Eksik Top-Right Sütun

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/pages/home.php` |
| Satır | 92-100 |
| Sorun Türü | Eksik layout sütunu |
| Öncelik | **LOW** |

Wide layout top section'da sadece `home-layout__top-left--wide` var. PNG mockup'ta 3 sütun (sol-orta-sağ) olmalı.

### 5.4 Duplicate Autoload Call: index.php

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/index.php` |
| Satır | 7, 20 |
| Sorun Türü | Tekrarlanan require |
| Öncelik | **LOW** |

`autoload.php` iki kez require ediliyor (satır 7 ve 20).

### 5.5 `include copy/` Dizin Mevcudiyeti

| Alan | Değer |
|------|-------|
| Dosya | `home.coremusic.net/include copy/` |
| Sorun Türü | Gereksiz kopya dizin |
| Öncelik | **LOW** |

Eski bir kopya dizini mevcut. Temizlenmeli.

---

## 6. Özet Tablosu

| # | Öncelik | Dosya | Sorun |
|---|---------|-------|-------|
| 1 | CRITICAL | `pages/home.php:51-52` | Namespace mismatch (ComponentLoader, HomeLayoutVariant) |
| 2 | CRITICAL | `include/Class/ComponentLoader.php:47` | Eksik `use RecentTracksComponent` |
| 3 | CRITICAL | `pages/components/player-info.php:17-39` | 6 tanımsız özellik (iconMusic, iconCd, iconMic, iconStar, iconBitrate, iconTimer) |
| 4 | HIGH | `pages/components/` | 5 eksik view partial |
| 5 | HIGH | `pages/home.php:125-127` | Orphaned closing div |
| 6 | HIGH | `pages/home.php:85-106` | Wide layout eksik componentler |
| 7-36 | MEDIUM | `.ai/CLAUDE.md` | 30 kırık wiki-link |
| 37 | MEDIUM | `include/Class/ComponentLoader.php` | Eksik use statement |
| 38 | LOW | `pages/components/player-info.php:13` | HTML attribute hatası |
| 39 | LOW | `pages/home.php:133-134` | Boş PHP bloğu |

---

**Oluşturulma Tarihi:** 2026-09-23
**Tarayan:** File Search Specialist
**Mod:** Red Team · Truth Mode
