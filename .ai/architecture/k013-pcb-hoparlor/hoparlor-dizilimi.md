---
title: "Hoparlör Dizilimi - k013-pcb-hoparlor"
type: architecture
category: mimari
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı"
updated: 2026-10-06
---

# Hoparlör Dizilimi (8.1 Surround)

- **Amaç:** 8.1 surround hoparlör konumları, kanal haritası (FL/C/FR/SL/SR/SBL/SBR + LFE), kablo/bağlantı, binding post çıktıları; sinyal kaynağı olarak Class AB amplifikatör detayı.
- **Persona:** `audio-hardware-engineer`
- **Wiki-link:** [[index.md]]
- **Çapraz referanslar:** [[../k010-class-ab-cikis/index]] · [[../k012-dijital-arayuz/index]]

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md` | tüm başlıklar (H1 + ##/###) | L8-L133 | verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` | 4. Class AB Amplifikatör Detayı | L136-L183 | verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` | Binding Posts (Hoparlör Çıktıları) | L136-L150 | verbatim |

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md - L8-L133

# Hoparlör Dizilimi - 8.1 Surround

## Genel Bakış

COREMUSIC 8.1 Surround hoparlör sistemi, 8 full-range hoparlör ve 1 subwoofer'dan oluşan tam surround ses konfigürasyonudur. ITU-R BS.775-1 standardına uygun olarak yerleştirilir. Her hoparlör bağımsız Class AB amplifikatör kanalı tarafından sürülür.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Hoparlör Sayısı | 8 + 1 subwoofer |
| Toplam Çıkış Gücü | 2.250W RMS |
| Frekans Aralığı | 20Hz – 20kHz (full-range) |
| Subwoofer Aralığı | 20Hz – 120kHz |
| Empedans | 8Ω (tüm hoparlörler) |
| Hassasiyet | 89dB/W/m |
| Maks. Basınç Seviyesi | 115dB SPL |
| THD | < %1 (maks. güçte) |

## Hoparlör Konumları

```
                    ÖN (FRONT)
                        │
            ┌───────────┼───────────┐
            │           │           │
            ▼           ▼           ▼
         ┌─────┐    ┌─────┐    ┌─────┐
         │ FL  │    │  C  │    │ FR  │
         │Front│    │Center│    │Front│
         │Left │    │      │    │Right│
         └─────┘    └─────┘    └─────┘
           -30°        0°        +30°

SOL (LEFT)                            SAĞ (RIGHT)
    │                                      │
    ▼                                      ▼
┌─────┐                              ┌─────┐
│ SL  │                              │ SR  │
│Side │                              │Side │
│Left │                              │Right│
└─────┘                              └─────┘
  -90°                                 +90°

        ┌─────┐              ┌─────┐
        │ SBL │              │ SBR │
        │Surround          │Surround
        │Back L│           │Back R│
        └─────┘              └─────┘
          -135°                +135°

                    ARKA (REAR)
                        │
                   ┌────┴────┐
                   │   SUB   │
                   │Subwoofer│
                   └─────────┘
                    (Front-Left alt)
```

## Kanal Haritası

| Kanal | Kısaltma | Konum | Açı | Amplifikatör |
|-------|----------|-------|-----|-------------|
| 1 | FL | Front Left | -30° | Amp Kanal 1 |
| 2 | C | Center | 0° | Amp Kanal 2 |
| 3 | FR | Front Right | +30° | Amp Kanal 3 |
| 4 | SL | Side Left | -90° | Amp Kanal 4 |
| 5 | SR | Side Right | +90° | Amp Kanal 5 |
| 6 | SBL | Surround Back Left | -135° | Amp Kanal 6 |
| 7 | SBR | Surround Back Right | +135° | Amp Kanal 7 |
| 8 | LFE | Subwoofer | Front-Left | Amp Kanal 8 |

## Hoparlör Özellikleri

### Full-Range Hoparlörler (FL, C, FR, SL, SR, SBL, SBR)

| Parametre | Değer |
|-----------|-------|
| Tip | 2-way (Woofer + Tweeter) |
| Woofer | 6.5" (165mm) |
| Tweeter | 1" (25mm) Dome |
| Frekans Crossover | 2.5kHz |
| Empedans | 8Ω |
| Güç Kapasitesi | 200W RMS |
| Hassasiyet | 89dB/W/m |

### Subwoofer (LFE)

| Parametre | Değer |
|-----------|-------|
| Tip | Passive radiator |
| Woofer | 10" (250mm) |
| Frekans Aralığı | 20Hz – 120kHz |
| Empedans | 8Ω |
| Güç Kapasitesi | 250W RMS |
| Hassasiyet | 87dB/W/m |

## Kablo ve Bağlantılar

| Kanal | Kablo Tipi | Uzunluk (Maks.) | Konnektör |
|-------|------------|-----------------|-----------|
| FL, FR | 12 AWG | 3m | Banana Plug |
| C | 12 AWG | 2m | Banana Plug |
| SL, SR | 12 AWG | 5m | Banana Plug |
| SBL, SBR | 12 AWG | 8m | Banana Plug |
| LFE | 10 AWG | 3m | Banana Plug |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Amplifikatör | Giriş | 8 kanallı Class AB amp |
| K1 Konnektörler | Bağlantı | Binding posts |
| K1 Koruma | Bağlantı | Speaker protection relay |
| K6 DSP | Üst | Crossover ve EQ ayarları |

## Durum: Implementasyon

**Durum**: 🟡 Tasarım Aşamasında

- Hoparlör seçimi: Faital Pro, Peerless, SB Acoustics değerlendiriliyor
- Crossover: 2.5kHz Linkwitz-Riley 4th order
- Subwoofer: 80kHz low-pass filter (K6 DSP'de)
- Yerleşim: ITU-R BS.775-1 standardına uygun
- Kablo yönetimi: Kanal içi twist, dış tuing shielded

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md - L136-L183

## 4. Class AB Amplifikatör Detayı

### 4.1 Tek Kanal Devre Şeması

```
                    +35V (PVDD)
                     │
                ┌────┴────┐
                │  Q15     │ MJL21194 (NPN Output)
                │  NPN     │
     Input ─────┤  Q16     ├──── Output → Hoparlör
     (Diff)     │  BD139   │
                │  VAS     │
                │  Q17     │ MJL21193 (PNP Output)
                │  PNP     │
                └────┬────┘
                     │
                    -35V (PVSS)

  Bias Network:
    Q1 (BC546B): Diferansiyel çift giriş
    Q2 (BC546B): Diferansiyel çift giriş
    Q5 (BC556B): Akım havuzu
    Q9 (KSC3503): VAS (Voltage Amplifier Stage)
    Q10 (BD139): Vbe çarpımı (bias spreader)
```

### 4.2 Bias Ayar Prosedürü

| Adım | İşlem | Değer |
|------|-------|-------|
| 1 | Güç kaynağı ayarla | ±35V DC |
| 2 | Multimetre çıkışa bağla | DC offset ölç |
| 3 | Bias potansiyometresi ayarla | 0V DC offset hedefle |
| 4 | Sıcaklık stabilizasyonu | 5-10 dk bekle |
| 5 | Son kontrol | <0.5V DC offset |

### 4.3 Termal Hesaplama

| Parametre | Değer |
|-----------|-------|
| Güç (kanal başına) | 50W @ 8Ω |
| Verimlilik | ~%65 (Class AB) |
| Isı (kanal başına) | ~17.5W |
| Toplam ısı (8 kanal) | ~140W |
| Heatsink gereksinimi | >140W/C° thermal resistance |
| Fan gereksinimi | 80mm PWM, >50 CFM |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md - L136-L150

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


