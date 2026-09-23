---
title: "CoreMusic — include/ Dizin Yapısı Haritası"
type: report
category: code-structure
date: 2026-09-23
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# CoreMusic — include/ Dizin Yapısı Haritası

**Tarih:** 2026-09-23  
**Kapsam:** `home.coremusic.net/include/` dizininin tam haritası  
**Amaç:** Hangi dosya nerede, ne yapıyor, ne implemente ediyor?

---

## 1. Dizin Yapısı

```
home.coremusic.net/include/
├── Auth/
│   └── HomeAuthBridge.php          ← Auth köprüsü
├── Class/
│   ├── AbstractComponent.php       ← Soyut sınıf (temel)
│   ├── ComponentLoader.php         ← Bileşen yükleyici
│   └── HomeLayoutVariant.php       ← Layout varyantı (enum)
├── Component/
│   ├── HomeSongButton.php          ← Mini kart HTML üretici (helper)
│   ├── PlayerInfoComponent.php     ← Player bilgi paneli
│   └── RecentTracksComponent.php   ← Son şarkılar kartları
├── Container/
│   └── HomeContainer.php           ← DI container
├── Interfaces/
│   └── ComponentInterface.php      ← Bileşen arayüzü
├── Session/
│   └── HomeSessionManager.php      ← Session yönetimi
└── CLAUDE.md                       ← AI talimatı
```

---

## 2. Sınıf Haritası

### 2.1 `Class/` — Soyut Sınıflar & Altyapı

| Dosya | Tür | Namespace | Ne Yapıyor | Interface/Extend |
|-------|-----|-----------|------------|-----------------|
| `AbstractComponent.php` | abstract class | `CoreMusic\Home\Class` | Tüm bileşenlerin base sınıfı. `render()`, `display()`, `h()`, `asset()`, `sessionString()`, `tierClass()` methodları | `ComponentInterface` implemente |
| `ComponentLoader.php` | final class | `CoreMusic\Home\Class` | Bileşen registry + yükleme. `display('key', $variant)` ile çağırılır | `ComponentInterface` kullanır |
| `HomeLayoutVariant.php` | enum | `CoreMusic\Home\Class` | 3 tier: `Embedded` / `Wide` / `FourK`. `isWide()`, `fromFlags()` methodları | Bağımsız |

### 2.2 `Component/` — Somut Bileşenler

| Dosya | Tür | Namespace | Ne Yapıyor | Extend/Implement |
|-------|-----|-----------|------------|-----------------|
| `RecentTracksComponent.php` | final class | `CoreMusic\Home\Component` | Son dinlenen şarkılar kartları (9 kart wide, 3 kart embedded) | `AbstractComponent` extend |
| `PlayerInfoComponent.php` | final class | `CoreMusic\Home\Component` | Player bilgi paneli (şarkı, albüm, sanatçı, bitrate, süre) | `AbstractComponent` extend |
| `HomeSongButton.php` | final class (static) | `CoreMusic\Home\Component` | Mini kart HTML üretici (helper). `html()` static methodu | **Hiçbirini implemente etmez** |

### 2.3 `Interfaces/` — Sözleşme

| Dosya | Tür | Namespace | Ne Yapıyor |
|-------|-----|-----------|------------|
| `ComponentInterface.php` | interface | `CoreMusic\Home\Interfaces` | `key()` ve `render()` sözleşmesi. ComponentLoader sadece bu interface'i tanır |

### 2.4 `Auth/` — Kimlik Doğrulama

| Dosya | Tür | Namespace | Ne Yapıyor |
|-------|-----|-----------|------------|
| `HomeAuthBridge.php` | class | `CoreMusic\Home\Auth` | auth.coremusic.net ile bridge |

### 2.5 `Session/` — Oturum

| Dosya | Tür | Namespace | Ne Yapıyor |
|-------|-----|-----------|------------|
| `HomeSessionManager.php` | class | `CoreMusic\Home\Session` | Oturum yönetimi |

### 2.6 `Container/` — Bağımlılık Enjeksiyonu

| Dosya | Tür | Namespace | Ne Yapıyor |
|-------|-----|-----------|------------|
| `HomeContainer.php` | class | `CoreMusic\Home\Container` | DI container |

---

## 3. Bağımlılık Zinciri

```
ComponentInterface (interface)
    ↑ implemente
AbstractComponent (abstract class)
    ↑ extend
RecentTracksComponent (final class)  ← Component/ klasöründe
PlayerInfoComponent (final class)    ← Component/ klasöründe

ComponentLoader (final class) ← Class/ klasöründe
    → ComponentInterface kullanır
    → defaultRegistry() → 2 bileşen kayıtlı

HomeLayoutVariant (enum) ← Class/ klasöründe
    → AbstractComponent'a bağımlı (constructor)
```

---

## 4. Component Registry Durumu

| Anahtar | Sınıf | Durum |
|---------|-------|-------|
| `player-info` | `PlayerInfoComponent` | ✅ Kayıtlı |
| `recent-tracks` | `RecentTracksComponent` | ✅ Kayıtlı |
| `widget-grid` | ? | ❌ Eksik |
| `welcome-banner` | ? | ❌ Eksik |
| `playlists` | ? | ❌ Eksik |
| `up-next` | ? | ❌ Eksik |
| `now-playing` | ? | ❌ Eksik |
| `welcome-modal` | ? | ❌ Eksik |
| `home-widgets` | ? | ❌ Eksik |

---

## 5. Tespit Edilen Sorunlar

| # | Sorun | Tür | Açıklama |
|---|-------|-----|----------|
| 1 | **ComponentLoader'da eksik use statement** | BUG | `RecentTracksComponent` use statement'ı eksik (satır 46'da kullanılıyor ama import yok) |
| 2 | **ComponentLoader'da 2/3 bileşen kayıtlı** | EKSİK | `recent-tracks` ve `player-info` var ama `HomeSongButton` kayıtsız (o zaten helper) |
| 3 | **HomeSongButton interface implemente etmez** | UYUMLULUK | `ComponentInterface`'i implemente etmiyor. Helper olduğu için normal |
| 4 | **Eksik bileşenler** | EKSİK | 7 bileşen kayıtsız: widget-grid, welcome-banner, playlists, up-next, now-playing, welcome-modal, home-widgets |
| 5 | **Namespace Class/ altında** | YAPI | `CoreMusic\Home\Class` namespace'i Java benzeri ama PHP'de nadir kullanılır |

---

## 6. Özet Tablo

| Kategori | Dosya Sayısı | Kullanım |
|----------|-------------|----------|
| Soyut Sınıf | 1 | `AbstractComponent` — tüm bileşenlerin base'i |
| Enum | 1 | `HomeLayoutVariant` — tier seçimi |
| Loader | 1 | `ComponentLoader` — registry + yükleme |
| Somut Bileşen | 2 | `RecentTracksComponent`, `PlayerInfoComponent` |
| Helper | 1 | `HomeSongButton` — static HTML üretici |
| Interface | 1 | `ComponentInterface` — sözleşmesi |
| Auth | 1 | `HomeAuthBridge` — kimlik |
| Session | 1 | `HomeSessionManager` — oturum |
| Container | 1 | `HomeContainer` — DI |
| **Toplam** | **10** | |

---

## 7. Önerilen İyileştirmeler

| # | İyileştirme | Öncelik | Süre |
|---|-------------|---------|------|
| 1 | ComponentLoader'a eksik use statement ekle | CRITICAL | 2 dk |
| 2 | Eksik 7 bileşeni ComponentLoader'a kaydet | YÜKSEK | 15 dk |
| 3 | HomeSongButton'a interface ekle (opsiyonel) | DÜŞÜK | 5 dk |
| 4 | Namespace'i yeniden düzenle (opsiyonel) | DÜŞÜK | 30 dk |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-09-23  
**Mode:** Red Team · Human Mode · Truth Mode
