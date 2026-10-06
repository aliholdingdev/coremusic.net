---
title: "Bağlantı ve Etiketleme - k066-konnektorler"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - verbatim aktarım: _backup/arch-2026-10-06_1057/architecture/"
updated: 2026-10-06
---

# Bağlantı ve Etiketleme

> Klasör: `k066-konnektorler` · Dosya: `baglanti-ve-etiketleme.md`
> Sorumlu persona: `audio-hardware-engineer` (CoreMusic Audio Hardware Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/` — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.

## Genel Bakış

Konnektör devre bağlantıları (XLR), binding post ve panel düzeni, katman bağımlılıkları/teknik özet, hoparlör matrisi bağlantısı, kablo-bağlantı bölümü ve dosya/ADR referans listeleri.

## Kapsam ve Sınırlar

- **Kapsam:** devre bağlantıları, panel düzeni ve etiketleme, kablo/bağlantı, referans listeleri.
- **Kapsam dışı:** pin konfigürasyonları (→ [[konnektor-cesitleri.md]]); güç dağıtımı (→ [[../k068-guc-kaynagi-analog/index]]).
- **Bağlı olduğu klasör:** [[index.md]]
- **Çapraz referanslar:** [[../k069-hoparlor-dizilimi/index]] · [[../k068-guc-kaynagi-analog/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi ve içerik kaynaktan değiştirilmeden kopyalanmıştır; her bloğun üstündeki `> Aktarım:` satırı kaynak dosyanın disk satır aralığını gösterir.
### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` (194 satır)

#### 1. konnektorler.md — XLR devre bağlantısı

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` - L45-L59

### XLR Devre Bağlantısı

```
XLR Input (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──▶ 100Ω ──▶ ADC AINL+ (PCM3168A)
     └─ Pin 3 (Cold) ─▶ 100Ω ──▶ ADC AINL- (PCM3168A)

XLR Output (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──◀ DAC OUTL+ (AK4458)
     └─ Pin 3 (Cold) ─◀ DAC OUTL- (AK4458)
```

#### 2. konnektorler.md — binding posts, panel düzeni, bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` - L136-L194

## Binding Posts (Hoparlör Çıkışları)

### Pin Konfigürasyonu

```
Binding Post (4mm Banana Compatible)
     │
     ├─ Red (+) ──▶ Amplifikatör Output (+)
     ├─ Black (-) ──▶ Amplifikatör Output (-)
     └─ Banana Plug / Spade / Bare Wire uyumlu

Torque: 1.5 Nm (tightening)
Maks. Kablo Kesiti: 4 AWG (21.2mm²)
```

## Panel Düzeni

```
┌─────────────────────────────────────────────────────────┐
│                     ARKA PANEL                          │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 1│ │XLR 2│ │XLR 3│ │XLR 4│ │XLR 5│ │XLR 6│      │
│  │FL   │ │FR   │ │C    │ │SL   │ │SR   │ │SBL  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 7│ │RCA 1│ │RCA 2│ │USB-C│ │TosLk│ │HDMI │      │
│  │SBR  │ │L    │ │R    │ │In   │ │In   │ │ARC  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS                       │    │
│  │  (+FL) (-FL) (+FR) (-FR) (+C) (-C) (+SL) (-SL) │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS (Devam)               │    │
│  │  (+SR) (-SR) (+SBL)(-SBL)(+SBR)(-SBR)(+LFE)(-LFE)│   │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐                               │
│  │IEC │ │Fuse │ │SWITCH│                               │
│  │Inlet│ │     │ │     │                               │
│  └─────┘ └─────┘ └─────┘                               │
└─────────────────────────────────────────────────────────┘
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

## Durum: Implementasyon

### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` (96 satır)

#### 3. index.md — katman bağımlılıkları, teknik özet, durum

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` - L65-L96

## Katman Bağımlılıkları

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | PCB, BOM, mekanik çizimler |
| K2 OS/Sürücüler | Üst | USB sürücü, ALSA/PulseAudio |
| K3 Temel Yazılım | Üst | XMOS firmware, I2S kontrol |

## Teknik Özet

- **Toplam Bileşen Sayısı**: 1.775 (tüm K1 alt dosyaları)
- **PCB Katman Sayısı**: 6 (4 Signal + 2 Power)
- **Güç Topolojisi**: ±35V analog, +5V/+3.3V dijital
- **Maksimum Çıkış Gücü**: 8 × 250W = 2.000W RMS
- **Frekans Aralığı**: 5Hz – 80kHz (±0.5dB)
- **THD+N**: < %0.001 (1kHz, 1W)
- **Sinyal/Gürültü Oranı**: > 120dB (A-Weighted)

## Durum: Implementasyon

**K1 Katman Durumu**: 🟡 Tasarım Aşamasında

| Alt Modül | Durum | Not |
|-----------|-------|-----|
| XMOS XU316 | 🟢 Hazır | USB Audio Class 2.0 firmare mevcut |
| PCM3168A ADC | 🟢 Hazır | Pin konfigürasyonu belirlendi |
| AK4458 DAC | 🟢 Hazır | DSD modu yapılandırıldı |
| Class AB Amp | 🟡 Devam | Simülasyon aşamasında |
| Güç Kaynağı | 🟡 Devam | LM5122 layout çalışıyor |
| PCB | 🔴 Başlamadı | 6-katman stackup planlandı |
| Soğutma | 🔴 Başlamadı | Termal simülasyon bekliyor |
| Koruma Devreleri | 🔴 Başlamadı | Şematiği hazır, layout yok |

### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır)

#### 4. README.md — §2.5 Hoparlör Matrisi (8.1 Surround)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L79-L93

### 2.5 Hoparlör Matrisi (8.1 Surround)

| Kanal | Hoparlör | Frekans | Konum |
|-------|----------|---------|-------|
| CH1 | Front Left | 20Hz-20kHz | Ön sol |
| CH2 | Front Right | 20Hz-20kHz | Ön sağ |
| CH3 | Center | 100Hz-8kHz | Merkez |
| CH4 | LFE (Sub) | 20Hz-120Hz | Subwoofer |
| CH5 | Surround Left | 100Hz-16kHz | Arka sol |
| CH6 | Surround Right | 100Hz-16kHz | Arka sağ |
| CH7 | Rear Left | 100Hz-16kHz | Arka sol |
| CH8 | Rear Right | 100Hz-16kHz | Arka sağ |

---


#### 5. README.md — §8 İlgili Dosyalar + §9 İlgili ADR'ler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L254-L277

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `architecture/k1-donanim/README.md` | Bu dosya |
| `architecture/k1-donanim/xmos-xu316.md` | XMOS detayı |
| `architecture/k1-donanim/pcm3168a.md` | DAC detayı |
| `architecture/k1-donanim/ak4458.md` | High-end DAC |
| `architecture/k1-donanim/class-ab-amplifier.md` | Amplifikatör devresi |
| `architecture/k1-donanim/speaker-matrix.md` | Hoparlör konfigürasyonu |
| `architecture/k1-donanim/bom-cost.md` | BOM maliyet |
| `architecture/k16-class-ab/README.md` | K16 Class AB |
| `architecture/k17-guc-kaynagi/README.md` | K17 Güç kaynağı |

---

## 9. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md` (133 satır)

#### 6. hoparlor-dizilimi.md — Kablo ve Bağlantılar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md` - L106-L115

## Kablo ve Bağlantılar

| Kanal | Kablo Tipi | Uzunluk (Maks.) | Konnektör |
|-------|------------|-----------------|-----------|
| FL, FR | 12 AWG | 3m | Banana Plug |
| C | 12 AWG | 2m | Banana Plug |
| SL, SR | 12 AWG | 5m | Banana Plug |
| SBL, SBR | 12 AWG | 8m | Banana Plug |
| LFE | 10 AWG | 3m | Banana Plug |

## İlgili Dosyalar

[[index.md]] · [[konnektor-cesitleri.md]] · [[../k069-hoparlor-dizilimi/index]] · [[../k068-guc-kaynagi-analog/index]]
