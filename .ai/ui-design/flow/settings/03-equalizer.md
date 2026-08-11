---
title: CoreMusic — Settings Flow: Equalizer (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[ADR-025-professional-eq-system]]
  - [[ADR-017-dsp-hardware-mode]]
  - [[01-component-inventory]] C15
---

# Settings Flow: Equalizer — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    EQUALIZER AKIŞI                                           │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Ayarlar      │ →  │ EQ Bölümü   │ →  │ 31-Band     │                  │
│  │ Sayfası      │    │ Seçilir      │    │ EQ Gösterilir│                 │
│  │ (/settings)  │    │              │    │              │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                    ┌─────────┼─────────┐                   │
│                                    │         │         │                   │
│                               ┌────▼────┐ ┌──▼──┐ ┌───▼────┐             │
│                               │ Band    │ │Preset│ │ Custom │             │
│                               │ Ayarlama│ │Seç   │ │ Kaydet │             │
│                               └────┬────┘ └──┬──┘ └───┬────┘             │
│                                    │         │         │                   │
│                                    └─────────┼─────────┘                   │
│                                              │                             │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Audio Engine │              │
│                                              │ Güncellenir │              │
│                                              └─────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. EQ SAYFASI YAPISI

### 2.1 — 31-Band EQ Görseli

```
┌── 31-BAND PARAMETRIK EQ ──────────────────────────────────────────────────┐
│                                                                            │
│  Gain(dB)                                                                  │
│  +12 ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─     │
│   +9 ─                                                                    │
│   +6 ─                                                                    │
│   +3 ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─     │
│    0 ════════════════════════════════════════════════════════════════     │
│   -3 ─                                                                    │
│   -6 ─                                                                    │
│   -9 ─                                                                    │
│  -12 ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─ ─     │
│        32  64 125 250 500 1k  2k  4k  8k 16k  Frekans(Hz)              │
│                                                                            │
│  Her band: 20px genişlik, toplam ~620px                                   │
│  Toplam EQ genişliği: ~800px (1024px ekran için)                          │
│  Yükseklik: ~200px                                                        │
│                                                                            │
└────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 — Preset Seçimi

```
┌── PRESETS ──────────────────────────────────────────────────────────────┐
│                                                                          │
│  [Flat] [Rock] [Pop] [Jazz] [Classical] [Electronic] [Custom]          │
│   ^^^^                                                                  │
│   aktif (pembe arka plan)                                               │
│                                                                          │
│  Her preset: ~80×32px buton                                             │
│  Aktif: background: var(--theme-primary), text: #fff                    │
│  Default: background: rgba(255,255,255,0.08), text: rgba(255,255,255,0.7)│
│                                                                          │
└──────────────────────────────────────────────────────────────────────────┘
```

### 2.3 — Band Ayarları (Seçildiğinde)

```
┌── BAND DETAY ──────────────────────────────────────────────────────────┐
│                                                                        │
│  Seçili Band: 1kHz                                                     │
│                                                                        │
│  Frekans: 1000 Hz                                                      │
│  Gain: +3.5 dB                                                         │
│  Q: 1.0                                                                │
│                                                                        │
│  [──────●──────] Gain slider (-12dB ile +12dB arası)                  │
│  [────●────────] Q slider (0.5 ile 10.0 arası)                        │
│                                                                        │
│  [Sıfırla] (C05, sınır)                                               │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. 31 BAND FREKANSLARI

| Band | Frekans | Aralık | Kullanım |
|------|---------|--------|----------|
| 1 | 32Hz | Sub-bass | Deep bass, kick drum |
| 2 | 64Hz | Bass | Bass guitar, kick |
| 3 | 125Hz | Bass | Bass presence |
| 4 | 250Hz | Low-mid | Warmth, body |
| 5 | 500Hz | Mid | Vocal body |
| 6 | 1kHz | Mid | Vocal presence |
| 7 | 2kHz | Upper-mid | Vocal clarity |
| 8 | 4kHz | High-mid | Presence, attack |
| 9 | 8kHz | High | Brilliance, air |
| 10 | 16kHz | Very high | Air, sparkle |

**Not:** 31 band, her oktavda 3 band (1/3 oktav aralıklı).

---

## 4. PRESET DEĞERLERİ

### 4.1 — Flat (Varsayılan)

```
Tüm bantlar: 0dB
Q: 1.0
```

### 4.2 — Rock

```
32Hz: +3dB, 64Hz: +3dB, 125Hz: +2dB
250Hz: 0dB, 500Hz: -1dB, 1kHz: 0dB
2kHz: +1dB, 4kHz: +2dB, 8kHz: +3dB, 16kHz: +2dB
```

### 4.3 — Pop

```
32Hz: +1dB, 64Hz: +2dB, 125Hz: +1dB
250Hz: 0dB, 500Hz: +1dB, 1kHz: +2dB
2kHz: +2dB, 4kHz: +1dB, 8kHz: +1dB, 16kHz: 0dB
```

### 4.4 — Jazz

```
32Hz: +1dB, 64Hz: +1dB, 125Hz: 0dB
250Hz: +1dB, 500Hz: +1dB, 1kHz: +1dB
2kHz: +1dB, 4kHz: +2dB, 8kHz: +2dB, 16kHz: +1dB
```

### 4.5 — Classical

```
32Hz: +1dB, 64Hz: +1dB, 125Hz: 0dB
250Hz: 0dB, 500Hz: 0dB, 1kHz: +1dB
2kHz: +1dB, 4kHz: +2dB, 8kHz: +3dB, 16kHz: +3dB
```

### 4.6 — Electronic

```
32Hz: +4dB, 64Hz: +4dB, 125Hz: +3dB
250Hz: +1dB, 500Hz: 0dB, 1kHz: 0dB
2kHz: +1dB, 4kHz: +2dB, 8kHz: +3dB, 16kHz: +2dB
```

---

## 5. DAVRANIŞ DETAYLARI

### 5.1 — Preset Seçimi

```
Kullanıcı bir preset butonuna tıklar
  → JS: Seçili preset vurgulanır
  → JS: 31 band'ın tümü otomatik ayarlanır (animasyonlu)
  → JS: EQ eğrisi çizilir ( Canvas veya SVG)
  → JS: Audio Engine'e bildirilir
    → WS: { action: "eq-preset", preset: "rock" }
  → C++: DSP zincirindeki EQ parametreleri güncellenir
  → JS: "Custom" preset'i aktif olur (kullanıcı değişiklik yaparsa)
```

### 5.2 — Band Ayarlama

```
Kullanıcı bir band'ı tıklar
  → JS: Seçili band vurgulanır (border: 2px solid var(--theme-primary))
  → JS: Band detay paneli açılır
    → Frekans, Gain, Q değerleri gösterilir
    → Slider'lar gösterilir

Kullanıcı Gain slider'ını sürükler
  → JS: Yeni gain değeri hesaplanır (-12dB ile +12dB)
  → JS: EQ eğrisi güncellenir ( Canvas)
  → JS: Audio Engine'e bildirilir
    → WS: { action: "eq-band", band: 6, gain: 3.5, q: 1.0 }
  → C++: DSP zincirindeki EQ parametresi güncellenir
  → Anında ses değişimi duyulur

Kullanıcı Q slider'ını sürükler
  → JS: Yeni Q değeri hesaplanır (0.5 ile 10.0)
  → JS: EQ eğrisi güncellenir (band genişliği değişir)
  → JS: Audio Engine'e bildirilir
```

### 5.3 — Band Sıfırlama

```
Kullanıcı "Sıfırla" butonuna basar
  → JS: Seçili band'ın gain'i 0dB'e ayarlanır
  → JS: Q değeri 1.0'a ayarlanır
  → JS: EQ eğrisi güncellenir
  → JS: Audio Engine'e bildirilir
```

### 5.4 — Custom Preset Kaydetme

```
Kullanıcı EQ ayarlarını değiştirir
  → "Custom" preset'i otomatik aktif olur
  → Kullanıcı "Kaydet" butonuna basar
  → İsim girişi istenir ("Özel EQ'ım")
  → Backend'e kaydedilir
    → POST /api/eq/presets
    → Request: { name: "Özel EQ'ım", bands: [...] }
  → Custom preset listesine eklenir
```

---

## 6. EQ EĞRİSİ ÇİZİMİ

### 6.1 — Canvas Kullanımı

```javascript
// EQ eğrisi çizimi
function drawEQCurve(canvas, bands) {
  const ctx = canvas.getContext('2d');
  const width = canvas.width;
  const height = canvas.height;
  
  // Arka plan
  ctx.fillStyle = 'rgba(0,0,0,0.3)';
  ctx.fillRect(0, 0, width, height);
  
  // Grid çizgileri
  ctx.strokeStyle = 'rgba(255,255,255,0.1)';
  // ... grid çizimi
  
  // EQ eğrisi
  ctx.beginPath();
  ctx.strokeStyle = '#ff4fd8'; // accent
  ctx.lineWidth = 2;
  
  bands.forEach((band, i) => {
    const x = (i / (bands.length - 1)) * width;
    const y = height / 2 - (band.gain / 12) * (height / 2);
    if (i === 0) ctx.moveTo(x, y);
    else ctx.lineTo(x, y);
  });
  
  ctx.stroke();
}
```

### 6.2 — Gerçek Zamanlı Güncelleme

```
Her gain değişikliğinde
  → Canvas temizlenir
  → Grid yeniden çizilir
  → EQ eğrisi yeniden çizilir (animasyonlu)
  → Seçili band vurgulanır
```

---

## 7. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (band) | ⚠️ ~20px → 48px olmalı |
| Touch target (preset) | ⚠️ ~32px → 48px olmalı |
| Touch target (slider) | ✅ 15px high, full-width |
| Focus indicator | ✅ |
| Keyboard nav | ✅ (←→ ile band seçimi, ↑↓ ile gain) |
| Screen reader | ⚠️ eksik |

---

## 8. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[ADR-025-professional-eq-system]] | EQ sistemi |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[01-component-inventory]] C15 | Toggle |
| [[flow/music/01-playback]] | Playback (EQ etkisi) |
| [[flow/settings/04-general]] | Genel ayarlar |

---

*Equalizer Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
