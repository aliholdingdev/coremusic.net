---
title: "CoreMusic — Color Palettes (3 Theme)"
type: reference
category: design-system
date: 2026-08-17
updated: 2026-08-17
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
themes: [female, male, neutral]
reference:
  authority: ".ai/ui-design/tokens/color-palettes.md"
  related:
    - ".ai/ui-design/tokens/design-tokens-master.md"
    - ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
---

# CoreMusic — Color Palettes (3 Theme)

**Her tema için tam renk paleti.** CSS custom properties ile değiştirilir.

> **⚠️ Tema motoru `data-gender` attribute'u ile çalışır.** `data-gender="female"` → pembe, `data-gender="male"` → mavi, `data-gender="neutral"` → nötr.

---

## 1. Female Teması (Pembe)

**PNG Kaynağı:** Tüm 18 PNG'miz bu tema ile tasarlanmıştır.
**Accent Renk:** `#ff4fd8` (Canlı Pembe)

### 1.1 — Ana Renkler

| Token | Değer | RGB | Kullanım |
|-------|-------|-----|----------|
| `--accent` | `#ff4fd8` | `255,79,216` | Ana vurgu rengi |
| `--accent-hover` | `#e63dc0` | `230,61,192` | Hover durumu |
| `--accent-active` | `#cc2ba8` | `204,43,168` | Active/tıklanmış |
| `--accent-light` | `#ff7fe6` | `255,127,230` | Açık vurgu |
| `--accent-dark` | `#cc3fad` | `204,63,173` | Koyu vurgu |
| `--accent-subtle` | `#fff0fb` | `255,240,251` | Çok açık pembe |

### 1.2 — Arka Plan Renkleri

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--accent-bg` | `rgba(255,79,216,0.15)` | Seçili kart arka planı |
| `--accent-bg-hover` | `rgba(255,79,216,0.25)` | Hover arka planı |
| `--accent-bg-active` | `rgba(255,79,216,0.35)` | Active arka planı |
| `--accent-bg-subtle` | `rgba(255,79,216,0.08)` | Hafif vurgu arka planı |
| `--accent-border` | `rgba(255,79,216,0.3)` | Border rengi |
| `--accent-border-hover` | `rgba(255,79,216,0.5)` | Hover border |
| `--accent-glow` | `0 0 20px rgba(255,79,216,0.4)` | Glow efekti |
| `--accent-glow-lg` | `0 0 40px rgba(255,79,216,0.6)` | Büyük glow |

### 1.3 — Gradient'ler

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--accent-gradient` | `linear-gradient(135deg, #ff4fd8, #ff7fe6)` | Buton, progress bar |
| `--accent-gradient-hover` | `linear-gradient(135deg, #e63dc0, #ff4fd8)` | Hover gradient |
| `--accent-gradient-vertical` | `linear-gradient(180deg, #ff4fd8, #cc3fad)` | Dikey gradient |
| `--accent-gradient-radial` | `radial-gradient(circle, #ff4fd8, #cc3fad)` | Dairesel gradient |

### 1.4 — Tam Renk Skalası (50-900)

| Skala | Değer | Kullanım |
|-------|-------|----------|
| `--pink-50` | `#fdf2f8` | En açık pembe |
| `--pink-100` | `#fce7f3` | Açık pembe |
| `--pink-200` | `#fbcfe8` | Orta açık pembe |
| `--pink-300` | `#f9a8d4` | Pembe |
| `--pink-400` | `#f472b6` | Canlı pembe |
| `--pink-500` | `#ec4899` | Orta pembe |
| `--pink-600` | `#db2777` | Koyu pembe |
| `--pink-700` | `#be185d` | Daha koyu pembe |
| `--pink-800` | `#9d174d` | En koyu pembe |
| `--pink-900` | `#831843` | Siyaha yakın pembe |

---

## 2. Male Teması (Mavi)

**PNG Kaynağı:** Henüz oluşturulmamıştır.
**Accent Renk:** `#4f9fff` (Canlı Mavi)

### 2.1 — Ana Renkler

| Token | Değer | RGB | Kullanım |
|-------|-------|-----|----------|
| `--accent` | `#4f9fff` | `79,159,255` | Ana vurgu rengi |
| `--accent-hover` | `#3d8ae6` | `61,138,230` | Hover durumu |
| `--accent-active` | `#2c79cc` | `44,121,204` | Active/tıklanmış |
| `--accent-light` | `#7fbfff` | `127,191,255` | Açık vurgu |
| `--accent-dark` | `#3f80cc` | `63,128,204` | Koyu vurgu |
| `--accent-subtle` | `#eff6ff` | `239,246,255` | Çok açık mavi |

### 2.2 — Arka Plan Renkleri

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--accent-bg` | `rgba(79,159,255,0.15)` | Seçili kart arka planı |
| `--accent-bg-hover` | `rgba(79,159,255,0.25)` | Hover arka planı |
| `--accent-bg-active` | `rgba(79,159,255,0.35)` | Active arka planı |
| `--accent-bg-subtle` | `rgba(79,159,255,0.08)` | Hafif vurgu arka planı |
| `--accent-border` | `rgba(79,159,255,0.3)` | Border rengi |
| `--accent-border-hover` | `rgba(79,159,255,0.5)` | Hover border |
| `--accent-glow` | `0 0 20px rgba(79,159,255,0.4)` | Glow efekti |
| `--accent-glow-lg` | `0 0 40px rgba(79,159,255,0.6)` | Büyük glow |

### 2.3 — Gradient'ler

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--accent-gradient` | `linear-gradient(135deg, #4f9fff, #7fbfff)` | Buton, progress bar |
| `--accent-gradient-hover` | `linear-gradient(135deg, #3d8ae6, #4f9fff)` | Hover gradient |
| `--accent-gradient-vertical` | `linear-gradient(180deg, #4f9fff, #3f80cc)` | Dikey gradient |
| `--accent-gradient-radial` | `radial-gradient(circle, #4f9fff, #3f80cc)` | Dairesel gradient |

### 2.4 — Tam Renk Skalası (50-900)

| Skala | Değer | Kullanım |
|-------|-------|----------|
| `--blue-50` | `#eff6ff` | En açık mavi |
| `--blue-100` | `#dbeafe` | Açık mavi |
| `--blue-200` | `#bfdbfe` | Orta açık mavi |
| `--blue-300` | `#93c5fd` | Mavi |
| `--blue-400` | `#60a5fa` | Canlı mavi |
| `--blue-500` | `#3b82f6` | Orta mavi |
| `--blue-600` | `#2563eb` | Koyu mavi |
| `--blue-700` | `#1d4ed8` | Daha koyu mavi |
| `--blue-800` | `#1e40af` | En koyu mavi |
| `--blue-900` | `#1e3a8a` | Siyaha yakın mavi |

---

## 3. Neutral Teması (Nötr)

**PNG Kaynağı:** Henüz oluşturulmamıştır.
**Accent Renk:** `#a0a0b0` (Nötr Gri-Mor)

### 3.1 — Ana Renkler

| Token | Değer | RGB | Kullanım |
|-------|-------|-----|----------|
| `--accent` | `#a0a0b0` | `160,160,176` | Ana vurgu rengi |
| `--accent-hover` | `#8a8a9a` | `138,138,154` | Hover durumu |
| `--accent-active` | `#74747f` | `116,116,127` | Active/tıklanmış |
| `--accent-light` | `#b8b8c4` | `184,184,196` | Açık vurgu |
| `--accent-dark` | `#808089` | `128,128,137` | Koyu vurgu |
| `--accent-subtle` | `#f5f5f7` | `245,245,247` | Çok açık nötr |

### 3.2 — Arka Plan Renkleri

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--accent-bg` | `rgba(160,160,176,0.15)` | Seçili kart arka planı |
| `--accent-bg-hover` | `rgba(160,160,176,0.25)` | Hover arka planı |
| `--accent-bg-active` | `rgba(160,160,176,0.35)` | Active arka planı |
| `--accent-bg-subtle` | `rgba(160,160,176,0.08)` | Hafif vurgu arka planı |
| `--accent-border` | `rgba(160,160,176,0.3)` | Border rengi |
| `--accent-border-hover` | `rgba(160,160,176,0.5)` | Hover border |
| `--accent-glow` | `0 0 20px rgba(160,160,176,0.4)` | Glow efekti |
| `--accent-glow-lg` | `0 0 40px rgba(160,160,176,0.6)` | Büyük glow |

### 3.3 — Gradient'ler

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--accent-gradient` | `linear-gradient(135deg, #a0a0b0, #b8b8c4)` | Buton, progress bar |
| `--accent-gradient-hover` | `linear-gradient(135deg, #8a8a9a, #a0a0b0)` | Hover gradient |
| `--accent-gradient-vertical` | `linear-gradient(180deg, #a0a0b0, #808089)` | Dikey gradient |
| `--accent-gradient-radial` | `radial-gradient(circle, #a0a0b0, #808089)` | Dairesel gradient |

### 3.4 — Tam Renk Skalası (50-900)

| Skala | Değer | Kullanım |
|-------|-------|----------|
| `--neutral-50` | `#f9fafb` | En açık nötr |
| `--neutral-100` | `#f3f4f6` | Açık nötr |
| `--neutral-200` | `#e5e7eb` | Orta açık nötr |
| `--neutral-300` | `#d1d5db` | Nötr |
| `--neutral-400` | `#9ca3af` | Canlı nötr |
| `--neutral-500` | `#6b7280` | Orta nötr |
| `--neutral-600` | `#4b5563` | Koyu nötr |
| `--neutral-700` | `#374151` | Daha koyu nötr |
| `--neutral-800` | `#1f2937` | En koyu nötr |
| `--neutral-900` | `#111827` | Siyaha yakın nötr |

---

## 4. Semantik Renkler (Tema Bağımsız)

Bu renkler tüm temalarda aynıdır.

### 4.1 — Durum Renkleri

| Token | Değer | RGB | Kullanım |
|-------|-------|-----|----------|
| `--success` | `#22c55e` | `34,197,94` | Başarılı, bağlı |
| `--success-hover` | `#16a34a` | `22,163,74` | Başarılı hover |
| `--success-light` | `#4ade80` | `74,222,128` | Açık başarı |
| `--success-dark` | `#15803d` | `21,128,61` | Koyu başarı |
| `--warning` | `#eab308` | `234,179,8` | Uyarı, orta sinyal |
| `--warning-hover` | `#ca8a04` | `202,138,4` | Uyarı hover |
| `--warning-light` | `#facc15` | `250,204,21` | Açık uyarı |
| `--warning-dark` | `#a16207` | `161,98,7` | Koyu uyarı |
| `--error` | `#ef4444` | `239,68,68` | Hata, zayıf sinyal |
| `--error-hover` | `#dc2626` | `220,38,38` | Hata hover |
| `--error-light` | `#f87171` | `248,113,113` | Açık hata |
| `--error-dark` | `#b91c1c` | `185,28,28` | Koyu hata |
| `--info` | `#3b82f6` | `59,130,246` | Bilgi, link |
| `--info-hover` | `#2563eb` | `37,99,235` | Bilgi hover |
| `--info-light` | `#60a5fa` | `96,165,250` | Açık bilgi |
| `--info-dark` | `#1d4ed8` | `29,78,216` | Koyu bilgi |

### 4.2 — Arka Plan Durum Renkleri

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--success-bg` | `rgba(34,197,94,0.15)` | Başarılı arka plan |
| `--success-bg-hover` | `rgba(34,197,94,0.25)` | Başarılı hover arka plan |
| `--success-border` | `rgba(34,197,94,0.3)` | Başarılı border |
| `--warning-bg` | `rgba(234,179,8,0.15)` | Uyarı arka plan |
| `--warning-bg-hover` | `rgba(234,179,8,0.25)` | Uyarı hover arka plan |
| `--warning-border` | `rgba(234,179,8,0.3)` | Uyarı border |
| `--error-bg` | `rgba(239,68,68,0.15)` | Hata arka plan |
| `--error-bg-hover` | `rgba(239,68,68,0.25)` | Hata hover arka plan |
| `--error-border` | `rgba(239,68,68,0.3)` | Hata border |
| `--info-bg` | `rgba(59,130,246,0.15)` | Bilgi arka plan |
| `--info-bg-hover` | `rgba(59,130,246,0.25)` | Bilgi hover arka plan |
| `--info-border` | `rgba(59,130,246,0.3)` | Bilgi border |

---

## 5. Statik Renkler (Tema Bağımsız)

### 5.1 — Beyaz & Siyah

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--white` | `#ffffff` | Beyaz text, ikon |
| `--white-50` | `rgba(255,255,255,0.5)` | Pasif beyaz |
| `--white-70` | `rgba(255,255,255,0.7)` | İkincil beyaz |
| `--white-85` | `rgba(255,255,255,0.85)` | Aktif beyaz |
| `--black` | `#000000` | Siyah arka plan |
| `--black-50` | `rgba(0,0,0,0.5)` | Karartma |
| `--black-70` | `rgba(0,0,0,0.7)` | Koyu karartma |

### 5.2 — Gri Skalası

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--gray-50` | `#f9fafb` | En açık gri |
| `--gray-100` | `#f3f4f6` | Açık gri arka plan |
| `--gray-200` | `#e5e7eb` | Border rengi |
| `--gray-300` | `#d1d5db` | Placeholder text |
| `--gray-400` | `#9ca3af` | İkincil text |
| `--gray-500` | `#6b7280` | Pasif text |
| `--gray-600` | `#4b5563` | Ana text |
| `--gray-700` | `#374151` | Koyu text |
| `--gray-800` | `#1f2937` | En koyu text |
| `--gray-900` | `#111827` | Siyaha yakın |

---

## 6. Glass Efekt Renkleri (Tema Bağımsız)

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--glass-bg` | `rgba(255,255,255,0.08)` | Glass arka plan |
| `--glass-bg-hover` | `rgba(255,255,255,0.12)` | Glass hover |
| `--glass-bg-active` | `rgba(255,255,255,0.16)` | Glass active |
| `--glass-border` | `rgba(255,255,255,0.1)` | Glass border |
| `--glass-border-hover` | `rgba(255,255,255,0.2)` | Glass border hover |
| `--glass-blur` | `blur(20px)` | Glass bulanıklık |
| `--glass-saturate` | `saturate(180%)` | Glass doygunluk |
| `--glass-blur-light` | `blur(8px)` | Hafif bulanıklık |
| `--glass-blur-heavy` | `blur(40px)` | Ağır bulanıklık |
| `--overlay-bg` | `rgba(0,0,0,0.5)` | Modal overlay |
| `--overlay-blur` | `blur(4px)` | Overlay bulanıklık |

---

## 7. Tema Değiştirme CSS'i

```css
/* === FEMALE TEMA (Varsayılan) === */
[data-gender="female"] {
  --accent: #ff4fd8;
  --accent-hover: #e63dc0;
  --accent-active: #cc2ba8;
  --accent-light: #ff7fe6;
  --accent-dark: #cc3fad;
  --accent-bg: rgba(255,79,216,0.15);
  --accent-bg-hover: rgba(255,79,216,0.25);
  --accent-border: rgba(255,79,216,0.3);
  --accent-glow: 0 0 20px rgba(255,79,216,0.4);
  --accent-gradient: linear-gradient(135deg, #ff4fd8, #ff7fe6);
}

/* === MALE TEMA === */
[data-gender="male"] {
  --accent: #4f9fff;
  --accent-hover: #3d8ae6;
  --accent-active: #2c79cc;
  --accent-light: #7fbfff;
  --accent-dark: #3f80cc;
  --accent-bg: rgba(79,159,255,0.15);
  --accent-bg-hover: rgba(79,159,255,0.25);
  --accent-border: rgba(79,159,255,0.3);
  --accent-glow: 0 0 20px rgba(79,159,255,0.4);
  --accent-gradient: linear-gradient(135deg, #4f9fff, #7fbfff);
}

/* === NEUTRAL TEMA === */
[data-gender="neutral"] {
  --accent: #a0a0b0;
  --accent-hover: #8a8a9a;
  --accent-active: #74747f;
  --accent-light: #b8b8c4;
  --accent-dark: #808089;
  --accent-bg: rgba(160,160,176,0.15);
  --accent-bg-hover: rgba(160,160,176,0.25);
  --accent-border: rgba(160,160,176,0.3);
  --accent-glow: 0 0 20px rgba(160,160,176,0.4);
  --accent-gradient: linear-gradient(135deg, #a0a0b0, #b8b8c4);
}
```

---

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Themes | 3 |
| Colors Per Theme | 20+ |
| Semantic Colors | 16 |
| Static Colors | 20+ |
| Glass Colors | 11 |
| CSS Variables | 100+ |

---

*Color Palettes v1.0.0 — CoreMusic Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-17*
*Mode: Red Team · Human Mode · Truth Mode*
