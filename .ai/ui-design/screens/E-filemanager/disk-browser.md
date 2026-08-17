---
title: CoreMusic — Disk Browser Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux  1024 - Göz At Page.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/01-standard-60-40]]
---

# CoreMusic — Disk Browser Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux  1024 - Göz At Page.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** 3 Sütun — Sol sidebar (167px) + Orta liste (573px) + Sağ panel (220px)
**Rota:** `/browse`

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [HEADER — ortak]                                                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ← Dosya Yöneticisi / Disk                                        [c:\users\...\Music] 🔍   │
│                                                                                                  │
│  ┌─ SOL SIDEBAR (x:16-183, w:167px) ───┐  ┌─ ORTA LİSTE (x:186-759, w:573px) ──────────┐   │
│  │                                       │  │ Şarkı Adı      | Albüm Adı | Sanatçı | Süre│   │
│  │ Sistem Diskleri                       │  │ [♪] Pop Şarkıları Ali                      │   │
│  │  ● System Disk                        │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00: │   │
│  │  ● NAS Drive                          │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00: │   │
│  │                                       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00: │   │
│  │ Harici / Taşınabilir Diskler          │  │                                            │   │
│  │  ● HDD Drive                          │  │                                            │   │
│  │  ● SSD Nvme 2 Drive                   │  │                                            │   │
│  │  ● SSD Drive                          │  │                                            │   │
│  │                                       │  │                                            │   │
│  │ Çıkarılabilir Diskleri                │  │                                            │   │
│  │  ● USB Drive                          │  │                                            │   │
│  │  ● USB Drive                          │  │                                            │   │
│  │  ● CD DVD Drive                       │  │                                            │   │
│  └───────────────────────────────────────┘  └────────────────────────────────────────────┘   │
│                                                                                                  │
│  ┌─ SAĞ BİLGİ PANELİ (x:784-1004, w:220px) ─────────────────────────────────────────────┐   │
│  │ System Disk                                                                             │   │
│  │ Hard Disk · Dahili Disk                                                                 │   │
│  │ ┌──────────┐                                                                           │   │
│  │ │Donut Chart│  32 GB — 16 GB Kullanılabilir  %50                                       │   │
│  │ │(pie chart)│                                                                           │   │
│  │ └──────────┘                                                                           │   │
│  │                                                                                         │   │
│  │ [Göz At] (C04, pembe buton)                                                            │   │
│  │ [Bütün Şarkıları Çal]                                                                  │   │
│  │ [Şarkıları Göz At]                                                                     │   │
│  │ [Şarkılarını Göz At]                                                                   │   │
│  │ [Videoları Göz At]                                                                     │   │
│  │ [...][...][...] (alt butonlar)                                                          │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

Sidebar: SADECE bu sayfaya özel, global değil
Sidebar satır yüksekliği: ~21px (WCAG İHLALİ — 48px olmalı)
```

---

## 2. 3 SÜTUN DETAY

### 2.1 — Sol Sidebar (167px)

| Bölüm | İçerik |
|-------|--------|
| Sistem Diskleri | System Disk, NAS Drive |
| Harici Diskler | HDD, SSD Nvme 2, SSD |
| Çıkarılabilir | USB Drive ×2, CD DVD Drive |

**Her disk satırı:**
- İkon: ~16×16px
- İsim: 12px, 500
- Yükseklik: ~21px (WCAG İHLALİ)
- Seçili: pembe arka plan

### 2.2 — Orta Liste (573px)

| Sütun | Genişlik |
|-------|----------|
| Şarkı Adı | ~%40 |
| Albüm Adı | ~%25 |
| Sanatçı | ~%20 |
| Süre | ~%15 |

**Tablo başlığı:** Sabit üstte, sıralanabilir
**Satır yüksekliği:** ~40px

### 2.3 — Sağ Panel (220px)

| İçerik | Boyut |
|--------|-------|
| Disk adı + türü | Başlık |
| Donut chart | ~100×100px |
| Bilgi | 32 GB, 16 GB Kullanılabilir, %50 |
| Butonlar | Göz At, Bütün Şarkıları Çal, vb. |

---

## 3. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (sidebar satır) | ❌ ~21px → 48px |
| Touch target (buton) | ✅ 48px+ |
| Touch target (liste satırı) | ⚠️ ~40px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

*Disk Browser Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
