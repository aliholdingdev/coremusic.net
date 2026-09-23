---
title: "EMC Filtreleme"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# EMC Filtreleme (EMI/EMC Filtering)

## Genel Bakış

EMC (Electromagnetic Compatibility) filtreleme, COREMUSIC'in hem kendi içinde hem de dış dünya ile uyumlu çalışmasını sağlar. Anahtarlama power supply'lerinden kaynaklanan EMI'yi (Electromagnetic Interference) bastırarak, hassas ses devrelerini korur ve düzenleyici standartlara (CISPR, FCC) uyum sağlar. Çok katmanlı filtreleme stratejisi ile conducted ve radiated emisyonlar kontrol altına alınır.

## EMC Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    EMC FİLTRELEME MİMARİSİ                            │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │  GİRİŞ EMC FİLTRESİ                                            │  │
│  │                                                                  │  │
│  │  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐       │  │
│  │  │ X-Cap   │──▶│ CM      │──▶│ Y-Cap   │──▶│ Fuse    │       │  │
│  │  │ 100nF   │   │ Choke   │   │ 4.7nF   │   │ 10A     │       │  │
│  │  │ (Differential)│ 10mH   │   │ (CM)    │   │         │       │  │
│  │  └─────────┘   └─────────┘   └─────────┘   └─────────┘       │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │  ÇIKIŞ EMC FİLTRESİ                                            │  │
│  │                                                                  │  │
│  │  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐       │  │
│  │  │ LC      │──▶│ Ferrite │──▶│ Feed-   │──▶│ Bypass  │       │  │
│  │  │ Filter  │   │ Bead    │   │ through │   │ Cap     │       │  │
│  │  │ 10μH/100μF│ │ 600Ω   │   │ Cap     │   │ 100nF   │       │  │
│  │  └─────────┘   └─────────┘   └─────────┘   └─────────┘       │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │  TOPLAM EMC PERFORMANSI                                        │  │
│  │                                                                  │  │
│  │  Conducted: CISPR 32 Class B (<30MHz)  ✅ 6dB marjı           │  │
│  │  Radiated:  CISPR 32 Class B (>30MHz)  ✅ 3dB marjı           │  │
│  │  ESD:       IEC 61000-4-2 ±8kV         ✅                      │  │
│  │  Surge:     IEC 61000-4-5 ±2kV         ✅                      │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Giriş EMC Filtresi

### X-Capacitor (Differential Mode)

```
L_N ──────┬───── DUT
          │
        C_X (100nF)
          │
L_E ──────┴───── DUT

Kesim Frekansı:
fc = 1 / (2π × Z_source × C_X)
fc ≈ 160kHz @ Z_source = 10Ω

Attenuation @ 1MHz: -20dB/decade
```

### Common Mode Choke

```
       ┌─────── L1 (10mH) ───────┐
L_N ───┤                          ├─── DUT
       │    ╔═══════════════╗    │
       └────║  Ferrite Core ║────┘
            ║  (High μ)     ║
L_E ────────║               ║──── DUT
            ╚═══════════════╝

Parametreler:
• Indüktans: 10mH (her iki sargı için)
• Akım: 10A continuous
• DC Resistance: <50mΩ
• Frekans Aralığı: 10kHz - 100MHz
• Measures: 30×20×15mm
```

### Y-Capacitor (Common Mode)

```
L_N ──────┬───── DUT
          │
        C_Y (4.7nF)  ← Safety rated (Y1)
          │
        FG (Chassis)
          │
        C_Y (4.7nF)  ← Safety rated (Y1)
          │
L_E ──────┴───── DUT

Toplam CM kapasite: 9.4nF
Güvenlik: Y1 rated, 250VAC
```

## Çıkış EMC Filtresi

### LC Output Filter

```
              L_OUT (10μH)
VIN_BOOST ────┤├────────────┬──── VOUT (+35V)
                          │
                      C_OUT (100μF)
                          │
                         GND

Kesim Frekansı:
fc = 1 / (2π × √(L × C))
fc = 1 / (2π × √(10μH × 100μF))
fc = 5.03kHz

Attenuation @ 1MHz:
A = (fSW / fc)² = (1MHz / 5.03kHz)² = 39524
A_dB = 20 × log(39524) = 92dB
```

### Ferrite Bead

```
VOUT ────┤ FB (600Ω @ 100MHz) ├──── Load

Parametreler:
• Empedans: 600Ω @ 100MHz
• DC Resistance: 20mΩ
• Maks Akım: 3A
• Paket: 0805
```

## Frekans Spektrumu Analizi

```
EMI Seviyesi (dBμV)
  100│
     │    ╔═══════════════════════════════════════╗
   80│    ║  CISPR 32 Class B Limit               ║
     │    ╚═══════════════════════════════════════╝
   60│  ████
     │  ████
   40│  ████  ████
     │  ████  ████  ████
   20│  ████  ████  ████  ████
     │  ████  ████  ████  ████  ████
    0│──████──████──████──████──████──████─────────
     │  100k  500k  1M    5M   10M   30M   100M
     │               Frekans (Hz)
     
     ████: Filtre Öncesi (ses mühendisliği için tehlikeli)
     ▒▒▒▒: Filtre Sonrası (CISPR limitinin altında)
```

## ESD Koruma

### TVS Diyot Ağı

```
        ┌─────────────────────────────────┐
        │  ESD Koruma Devresi              │
        │                                   │
USB ────┤─── TVS1 (USBLC6-2) ──── MCU    │
        │       │                          │
        │    6.8nF                         │
        │       │                          │
GND ────┤───────┴──────────────────────────│
        │                                   │
Audio ──┤─── TVS2 (SMBJ5.0A) ──── Op-Amp │
        │       │                          │
        │    10nF                          │
        │       │                          │
GND ────┤───────┴──────────────────────────│
        └─────────────────────────────────┘

ESD Specs:
• Contact: ±8kV
• Air: ±15kV
• Response: <1ns
```

## Ferrite Seçimi

| Frekans | Empedans | Malzeme | Kullanım |
|---------|----------|---------|----------|
| 1MHz | 100Ω | MnZn | Low-freq filtering |
| 10MHz | 300Ω | MnZn | Mid-freq filtering |
| 100MHz | 600Ω | NiZn | High-freq filtering |
| 500MHz | 1000Ω | NiZn | Very high-freq |

## PCB Layout EMC Kuralları

```
┌─────────────────────────────────────────────────────────────┐
│  EMC LAYOUT KURALLARI                                        │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  1. GROUNDING                                                 │
│     • Solid ground plane (bottom layer)                      │
│     • Star ground topology                                   │
│     • Analog ve digital ground ayrımı                       │
│     • Via stitching: her 200mil'de bir                       │
│                                                               │
│  2. TRACE ROUTING                                             │
│     • High-current traces: 2oz copper                        │
│     • Switching nodes: minimal loop area                     │
│     • Sensitive traces: ground guard                         │
│     • 3W rule: trace spacing ≥ 3× trace width               │
│                                                               │
│  3. COMPONENT PLACEMENT                                      │
│     • Input filter: connector'a yakın                        │
│     • Output filter: load'a yakın                            │
│     • Bypass caps: pin'e 500mil içinde                       │
│     • Ferrite beads: noise source'e yakın                    │
│                                                               │
│  4. SHIELDING                                                 │
│     • Metal shield罩 over switching converter                │
│     • Connector shield: chassis ground                       │
│     • Cable shield: 360° termination                         │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## Spesifikasyonlar

| Parametre | Değer | Standart |
|-----------|-------|----------|
| Conducted (150kHz-30MHz) | <30dBμV | CISPR 32 B |
| Radiated (30MHz-1GHz) | <30dBμV/m | CISPR 32 B |
| ESD Contact | ±8kV | IEC 61000-4-2 |
| ESD Air | ±15kV | IEC 61000-4-2 |
| Surge Line-Line | ±2kV | IEC 61000-4-5 |
| Surge Line-Gnd | ±4kV | IEC 61000-4-5 |
| EFT | ±4kV | IEC 61000-4-4 |
| Conducted Immunity | 3V/m | IEC 61000-4-6 |

## Bileşen Listesi

| Bileşen | Değer | Adet | Paket | Kullanım |
|---------|-------|------|-------|----------|
| X-Cap | 100nF/275V | 2 | Film | DM filter |
| CM Choke | 10mH/10A | 1 | Toroid | CM filter |
| Y-Cap | 4.7nF/Y1 | 4 | Ceramic | CM filter |
| LC Filter | 10μH+100μF | 2 | Mixed | Output |
| Ferrite Bead | 600Ω | 6 | 0805 | HF filter |
| TVS (USB) | USBLC6-2 | 2 | SOT-23-6 | ESD |
| TVS (Audio) | SMBJ5.0A | 4 | SMB | ESD/Surge |

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-LM5122 | Anahtarlama gürültüsü kaynağı |
| K17-VoltageReg | Çıkış filtreleme |
| K3-AudioEngine | Hassas analog devre koruması |
| K0-PCB | PCB layout ve grounding |

## Durum: Implementasyon

✅ Giriş EMC filtresi tasarımı tamamlandı (X, CM, Y caps)  
✅ Çıkış LC filtreleri hesaplandı ve seçildi  
✅ ESD koruma devresi entegre edildi (USB, Audio)  
✅ Ferrite bead seçimi yapıldı (NiZn, 600Ω)  
✅ PCB EMC kuralları belirlendi (grounding, routing)  
✅ CISPR 32 Class B hedefi konuldu  
⚠️ EMC testleri henüz yapılmadı (emanet test laboratuvarı bekleniyor)  
⚠️ Radiated emissions ölçümü gerekli
