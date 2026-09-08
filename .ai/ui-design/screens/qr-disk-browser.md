---
title: "Disk Browser — Quick Reference"
type: ascii-qr
screen_id: "S09"
resolution: "1024x600"
layout_pattern: "3-column"
components: [C01, C04, C16]
png: "home-1024/Linux  1024 - Göz At Page.png"
full_spec: "E-filemanager/disk-browser.md"
---

# Disk Browser (Göz At)

## Layout Wireframe

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
│  │  ● System Disk ▓▓▓▓░░░░ 50%          │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00: │   │
│  │  ● NAS Drive   ▓▓▓░░░░░ 40%          │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00: │   │
│  │                                       │  │ [♪] Göksel - Sevil Neş. | Hayat Rüya | 00: │   │
│  │ Harici / Taşınabilir Diskler          │  │                                            │   │
│  │  ● HDD Drive    ▓▓▓▓▓░░░ 70%         │  │                                            │   │
│  │  ● SSD Nvme 2   ▓▓▓▓░░░░ 55%         │  │                                            │   │
│  │  ● SSD Drive    ▓▓▓▓▓▓░░ 80%         │  │                                            │   │
│  │                                       │  │                                            │   │
│  │ Çıkarılabilir Diskleri                │  │                                            │   │
│  │  ● USB Drive    ▓▓░░░░░░ 25%         │  │                                            │   │
│  │  ● USB Drive    ▓░░░░░░░ 15%         │  │                                            │   │
│  │  ● CD DVD Drive ░░░░░░░░  0%         │  │                                            │   │
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
│  │ [♫][🎬][📷][📄][⋯]  (dosya türü ikonları)                                             │   │
│  │                                                                                         │   │
│  │ [Göz At] (C04, pembe buton)                                                            │   │
│  │ [Bütün Şarkıları Çal]                                                                  │   │
│  │ [Şarkıları Göz At]                                                                     │   │
│  │ [Şarkılarını Göz At]                                                                   │   │
│  │ [Videoları Göz At]                                                                     │   │
│  └─────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                  │
│ [FOOTER — ortak]                                                                                │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘

Sidebar: SADECE bu sayfaya özel, global değil
Sidebar satır yüksekliği: ~21px (WCAG İHLALİ — 48px olmalı)
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Header | top | 60px | `--header-h` |
| Footer | bottom | 90px | `--footer-h` |
| İçerik | middle | 450px | `--content-h` |
| Sol sidebar | x:16-183 | 167px | — |
| Orta liste | x:186-759 | 573px | — |
| Sağ panel | x:784-1004 | 220px | — |
| Disk satır yüksekliği | — | ~40px | — |
| Disk ikonu | — | ~24×24px | — |
| Donut chart | sağ panel | 100×100px | — |
| Progress bar | disk satırı içinde | 4px yükseklik | — |
| Touch target | — | ≥48px | `--touch-min` |
| Glass blur | — | blur(8px) | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C01 | `.header` | top | 100%×60px |
| C04 | `.footer` | bottom | 100%×90px |
| C16 | `.disk-row` | sol sidebar satırları | 100%×40px min |
| C16 | `.disk-info` | sağ panel | 220px wide |
| C16 | `.disk-info__donut` | sağ panel | 100×100px |

## Sidebar Sections

| Bölüm | İçerik |
|-------|--------|
| Sistem Diskleri | System Disk, NAS Drive |
| Harici Diskler | HDD, SSD Nvme 2, SSD |
| Çıkarılabilir | USB Drive ×2, CD DVD Drive |

## Right Panel

| İçerik | Boyut |
|--------|-------|
| Disk adı + türü | Başlık |
| Donut chart | ~100×100px |
| Bilgi | 32 GB, 16 GB Kullanılabilir, %50 |
| Dosya türü ikonları | 5× ikon (müzik, video, resim, belge, diğer) |
| Butonlar | Göz At (pembe), Bütün Şarkıları Çal, Şarkıları Göz At, Şarkılarını Göz At, Videoları Göz At |

## CSS Hints

```css
.disk-layout {
  display: grid;
  grid-template-columns: 224px 1fr 224px;
  height: var(--content-h);
}
.disk-row.is-selected {
  background: var(--accent-bg);
  border-left: 3px solid var(--accent);
}
.disk-info__donut {
  width: 100px; height: 100px;
}
.disk-row__progress {
  height: 4px;
  background: rgba(255,255,255,0.2);
}
```
