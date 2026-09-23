---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — File Browser Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/file-browser.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Göz At Page.png"
---

# CoreMusic — File Browser (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─── HEADER (h:60) ────────────────────────────────────────────────────────────────────────────────┐  │
│     │ ← Geri   Dosya Yöneticisi / Disk                                                  🔍 Dosya Ara  │  │
│ y:60 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:60 ┌─── CONTENT (h:450) ──────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                    │  │
│     │  ┌─── LEFT PANEL (60%, w:614) ──────────────────┐  ┌─── RIGHT PANEL (40%, w:410) ────────────┐   │  │
│     │  │                                                │  │  System Disk                             │   │  │
│     │  │  Sistem Diskleri                              │  │  Hard Disk · Dahili Disk                 │   │  │
│     │  │  ┌────────────────────┐ ┌────────────────┐   │  │                                          │   │  │
│     │  │  │ 💾 System Disk     │ │ 📁 NAS Drive   │   │  │  ┌─── Capacities ────────────────────┐   │   │  │
│     │  │  │ ━━━━━━━━━━ 50%    │ │ ━━━━━━━━ 40%  │   │  │  │ 🎵 Müzikler     88 GB   20.000    │   │   │  │
│     │  │  └────────────────────┘ └────────────────┘   │  │  │ 🎬 Videolar     25 GB    5.000    │   │   │  │
│     │  │                                                │  │  │ 📁 Klasörler     15 GB    5.000   │   │   │  │
│     │  │  Harici / Taşınabilir Diskler                 │  │  │ 📀 Disklerisiz   1 GB     1.000   │   │   │  │
│     │  │  ┌──────────┐ ┌──────────┐ ┌──────────┐     │  │  │ 📁 Paylaşımlı    1 GB     1.000   │   │   │  │
│     │  │  │ 💾 HDD    │ │ 💾 SSD   │ │ 💾 SSD   │     │  │  └──────────────────────────────────┘   │   │  │
│     │  │  │ Drive    │ │ Nvme-2   │ │ Drive    │     │  │                                          │   │  │
│     │  │  │ ━━━━ 30% │ │ ━━━━ 60% │ │ ━━━━ 45% │     │  │  ┌─── Disk Visual ───────────────────┐   │   │  │
│     │  │  └──────────┘ └──────────┘ └──────────┘     │  │  │ 📊 Pie Chart (dairesel)            │   │   │  │
│     │  │                                                │  │  │    Müzik: 1500   Video: 500       │   │   │  │
│     │  │  Çıkarılabilir Diskleri                       │  │  │    Klasör: 1400   Paylaşımlı: 100 │   │   │  │
│     │  │  ┌──────────┐ ┌──────────┐ ┌──────────┐     │  │  └──────────────────────────────────┘   │   │  │
│     │  │  │ 💾 USB    │ │ 💾 USB    │ │ 💿 CD    │     │  │                                          │   │  │
│     │  │  │ Drive    │ │ Drive    │ │ DVD      │     │  │  [▶ Göz At]                             │   │  │
│     │  │  │ ━━━━ 20% │ │ ━━━━ 55% │ │ Drive    │     │  │  [♫ Tüm Şarkıları Çal]                 │   │  │
│     │  │  └──────────┘ └──────────┘ │ ━━━━ 10% │     │  │  [🔍 Şarkıları Göz At]                 │   │  │
│     │  │                             └──────────┘     │  │  [🎵 Şarkıları Göz At]                 │   │  │
│     │  │                                                │  │  [🎬 Videoları Göz At]                 │   │  │
│     │  └────────────────────────────────────────────────┘  └──────────────────────────────────────────┘   │  │
│     │                                                                                                    │  │
│     └────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│ y:510 ┌─── FOOTER PLAYER (h:90) ───────────────────────────────────────────────────────────────────────┐  │
│ y:600 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.file-browser` | Ana sayfa container |
| `.file-browser__disks` | Sol panel: Disk listesi |
| `.file-browser__info` | Sağ panel: Disk bilgisi |
| `.disk-section` | Disk bölümü (Sistem, Harici, Çıkarılabilir) |
| `.disk-section__title` | Bölüm başlığı |
| `.disk-card` | Disk kartı (glass) |
| `.disk-card__icon` | Disk ikonu |
| `.disk-card__name` | Disk adı |
| `.disk-card__progress` | Kullanım çubuğu |
| `.disk-card__percent` | Yüzde |
| `.disk-info__name` | Disk adı |
| `.disk-info__type` | Disk türü |
| `.disk-info__capacities` | Kapasite listesi |
| `.disk-info__chart` | Dairesel grafik |
| `.disk-info__actions` | Aksiyon butonları |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-progress-bg` | rgba(255,255,255,0.2) |
| `--cm-progress-fill` | linear-gradient(90deg, #ff4fd8, #a855f7) |
| `--cm-radius-md` | 12px |
| `--cm-spacing-md` | 16px |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Göz At Page.png` | 1024×600 |
| `Linux 1024 - Göz At - Tıklama Clicked.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
