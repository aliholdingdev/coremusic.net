---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Select Gender Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Select Gender Flow

## 1. Akış Diyagramı (Decision Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│  "Seni Tanıyalım"│
│  Hoş geldin!    │
└────────┬────────┘
         │
┌────────▼────────┐
│  Seçim Yap      │
│  [👩 Kız]        │
│  [👨 Erkek]      │
│  [[V] Nötr]      │
└────────┬────────┘
         │
┌────────▼────────┐
│ Seçim Yapıldı?  │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Buton│ │  │Theme Uygula    │
│Disabled│  │                │
│"Devam" │  │ Kız → pembe    │
└─────┘ │  │ Erkek → mavi   │
        │  │ Nötr → default │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Preference Kaydet│
        │  │DB: user_prefs  │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │CSS Variables   │
        │  │Güncelle        │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Login Ekranına │
        │  │Geç            │
        │  └───────────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Seni Tanıyalım                | |
| |                   |  |                                  | |
| | [*] Core Music     |  | Hoş geldin!                     | |
| |                   |  | Temini seç, sana özel deneyim   | |
| |                   |  | hazırlayalım.                    | |
| |                   |  |                                  | |
| |                   |  | ┌──────────┐ ┌──────────┐       | |
| |                   |  │ │   👩     │ │   👨     │       | |
| |                   |  │ │  Kız     │ │  Erkek   │       | |
| |                   |  │ │ Pembe    │ │  Mavi    │       | |
| |                   |  │ │ theme    │ │  theme   │       | |
| |                   |  │ └──────────┘ └──────────┘       | |
| |                   |  │                                  | |
| |                   |  │ ┌──────────────────┐            | |
| |                   |  │ │ [[V] Belirtmek   │            | |
| |                   |  │ │ İstemiyorum]     │            | |
| |                   |  │ │ Nötr theme       │            | |
| |                   |  │ └──────────────────┘            | |
| |                   |  │                                  | |
| |                   |  | [▶ Devam Et]                     | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
```

## 3. Theme Mapping

| Seçim | Theme | Primary Color | Secondary | Öneri Tonu |
|-------|-------|---------------|-----------|------------|
| 👩 Kız | pembe | `#FF6B9D` | `#FFB3D1` | Pembe tonları |
| 👨 Erkek | mavi | `#4A90D9` | `#89C2F4` | Mavi tonları |
| [[V]] Nötr | default | `#6C757D` | `#ADB5BD` | Nötr tonları |

## 4. CSS Variable Güncellemesi

```css
/* Kız Theme */
--primary: #FF6B9D;
--primary-light: #FFB3D1;
--accent: #FF85B3;

/* Erkek Theme */
--primary: #4A90D9;
--primary-light: #89C2F4;
--accent: #5DA3E8;

/* Nötr Theme (Default) */
--primary: #6C757D;
--primary-light: #ADB5BD;
--accent: #868E96;
```

## 5. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Seçim yapılmadan devam | Buton disabled kalır |
| API hatası | "Bir hata oluştu, tekrar dene" |
| Network hatası | "Bağlantı yok" + retry |

## 6. Tier-Bazlı Varyasyonlar

| Tier | Seçim Tipi | Buton | Animasyon |
|------|------------|-------|-----------|
| **Phone** | Full-screen, dokunmatik | Büyük, thumb-area | Fade-in |
| **Tablet** | Split-panel | Orta boy | Slide-in |
| **Embedded** | Split 42/58 | Orta boy | Fade-in |
| **Desktop** | Inline selector | Normal boy | Hover effect |
| **TV** | Large cards, remote | Büyük, focus ring | Focus glow |
| **Car** | Voice-first | Dokunmatik, büyük | — |
| **Watch** | Crown scroll | Haptic tap | Haptic |

## 7. Preference Kaydı

| Alan | Değer | DB Tablosu |
|------|-------|------------|
| `user_id` | Mevcut kullanıcı | `coremusic_user` |
| `theme_gender` | female/male/neutral | `user_preferences` |
| `created_at` | Timestamp | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
