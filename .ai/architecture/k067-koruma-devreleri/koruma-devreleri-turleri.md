---
title: "Koruma Devreleri Türleri - k067-koruma-devreleri"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - verbatim aktarım: _backup/arch-2026-10-06_1057/architecture/"
updated: 2026-10-06
---

# Koruma Devreleri Türleri

> Klasör: `k067-koruma-devreleri` · Dosya: `koruma-devreleri-turleri.md`
> Sorumlu persona: `audio-hardware-engineer` (CoreMusic Audio Hardware Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/` — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.

## Genel Bakış

Overcurrent, short circuit ve termal koruma türleri; güç kaynağı koruma devreleri, K1.4 Güç Kaynağı & Koruma bölümü, katman bağımlılıkları ve ilgili ADR'ler.

## Kapsam ve Sınırlar

- **Kapsam:** koruma devresi türleri, çalışma prensibi, bileşen değerleri, güç kaynağı koruma bloğu.
- **Kapsam dışı:** DC offset ve termal izleme (→ [[dc-offset-ve-termal-koruma.md]]); regulasyon/filtreleme (→ [[../k068-guc-kaynagi-analog/index]]).
- **Bağlı olduğu klasör:** [[index.md]]
- **Çapraz referanslar:** [[../k068-guc-kaynagi-analog/index]] · [[../k071-termal-yonetim/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi ve içerik kaynaktan değiştirilmeden kopyalanmıştır; her bloğun üstündeki `> Aktarım:` satırı kaynak dosyanın disk satır aralığını gösterir.
### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` (189 satır)

#### 1. koruma-devreleri.md — giriş ve teknik spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` - L1-L24

---
title: "Koruma Devreleri"
layer: K1
category: "Güvenlik Sistemleri"
date: 2026-09-20
---

# Koruma Devreleri

## Genel Bakış

Koruma devreleri, COREMUSIC amplifikatörünü ve bağlı hoparlörleri DC offset, overcurrent, thermal runaway ve short circuit gibi zararlı durumlardan korur. Her koruma katmanı bağımsız çalışır ve fail-safe prensibine göre tasarlanmıştır.

## Teknik Spesifikasyonlar

| Koruma Tipi | Tetikleme | Gecikme | Yanıt |
|-------------|-----------|---------|-------|
| DC Offset | > ±1V DC | 0.5s | Speaker disconnect |
| Overcurrent | > 10A peak | Anında | Current limiting |
| Thermal Shutdown | > 85°C | 5s | Speaker disconnect |
| Short Circuit | 0Ω load | Anında | Current limiting |
| Overvoltage | > ±40V | 1ms | PSU shutdown |
| Mains Fuse | > 3A AC | 10ms | Fuse blown |


#### 2. koruma-devreleri.md — overcurrent koruması ve current limiting profile

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` - L69-L106

## Overcurrent Koruması

### Devre Şeması

```
Current Sense Resistor (R_sense = 0.22Ω)
     │
     ├─ V_sense = I_load × R_sense
     │
     │   @ 10A: V_sense = 2.2V
     │
     └─▶ Current Sense Amplifier (INA213)
              │
              ├─ Gain = 50
              │   Vout = 50 × V_sense
              │
              │   @ 10A: Vout = 110V (exceeds comparator ref)
              │
              └─▶ Comparator (LM393)
                      │
                      ├─ Reference: 2.5V (5A limit)
                      │
                      └─▶ Current Limit Driver
                              │
                              ▼
                         MOSFET Gate
                         (reduce drive)
```

### Current Limiting Profile

| Akım Seviyesi | Davranış | Süre |
|---------------|----------|------|
| < 5A | Normal çalışma | Sürekli |
| 5-8A | Soft limiting | 10ms |
| 8-10A | Hard limiting | Anında |
| > 10A | Shutdown | 100µs |


#### 3. koruma-devreleri.md — short circuit koruması, bileşen değerleri, bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` - L141-L189

## Short Circuit Koruması

### Çift Koruma

```
1. Output Current Limiting:
   - R_sense = 0.22Ω
   - Max current: 10A
   - Response time: < 1µs

2. Foldback Current Limiting:
   - Vout < 5V: Full current allowed (10A)
   - Vout = 0V (short): Current reduced to 3A
   - Vout = 35V: Current = 0A (open circuit)
```

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Current Sense | 0.22Ω 5W | 2 | Output sensing |
| 2 | Current Amp | INA213 | 2 | Current sense amplifier |
| 3 | Comparator | LM393 | 4 | Dual comparator |
| 4 | Relay | Finder 40.52 | 2 | 30A DPDT |
| 5 | Relay Driver | BC337 | 2 | NPN driver |
| 6 | NTC Sensor | 10kΩ NTC | 2 | Temperature sensing |
| 7 | Fuse | 3A AC | 1 | Mains protection |
| 8 | TVS Diode | SMBJ36A | 2 | Overvoltage clamp |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Amplifikatör | Bağlantı | Output sensing |
| K1 Termal | Bağlantı | NTC sensor |
| K1 Güç Kaynağı | Bağlantı | PSU shutdown |
| K1 Hoparlör | Çıkış | Speaker relay |
| K2 OS/Sürücüler | Üst | Fault reporting |

## Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- DC offset koruması: Şematik hazır, layout yok
- Overcurrent: INA213 evaluation board test edildi
- Thermal shutdown: NTC sensor seçimi yapıldı
- Short circuit: Foldback design LTSpice'da simulate edildi
- Relay: Finder 40.52 (30A DPDT) seçildi
- PCB: Koruma devresi için ayrı area planlandı

### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` (164 satır)

#### 4. guc-kaynagi-analog.md — Koruma Devreleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` - L138-L146

## Koruma Devreleri

| Koruma | Tip | Değer | Açıklama |
|--------|-----|-------|----------|
| Overcurrent | Cycle-by-cycle | 10A | LM5122 OCP |
| Overvoltage | Zener clamp | 38V | FB pin |
| Thermal | NTC sensor | 85°C shutdown | Soğutucu |
| Soft-start | Capacitor | 100nF | 10ms start-up |


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır)

#### 5. README.md — K1.4 Güç Kaynağı & Koruma

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L432-L461

### K1.4 — Güç Kaynağı & Koruma

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.4.1** | Analog Güç Kaynağı (3. katman) | guc-kaynagi-analog.md — 9 yaprak |
| K1.4.1.1 | Genel Bakış | guc-kaynagi-analog.md L10 |
| K1.4.1.2 | Teknik Spesifikasyonlar | guc-kaynagi-analog.md L14 |
| K1.4.1.3 | Güç Topolojisi | guc-kaynagi-analog.md L28 |
| K1.4.1.4 | Devre Tasarımı | guc-kaynagi-analog.md L71 |
| K1.4.1.5 | Bileşen Listesi | guc-kaynagi-analog.md L110 |
| K1.4.1.6 | Ripple Analizi | guc-kaynagi-analog.md L123 |
| K1.4.1.7 | Koruma Devreleri | guc-kaynagi-analog.md L138 |
| K1.4.1.8 | LM5122 Dual Boost Converter | guc-kaynagi-analog.md L73 |
| K1.4.1.9 | Voltaj Ayarı | guc-kaynagi-analog.md L95 |
| **K1.4.2** | Koruma Devreleri (3. katman) | koruma-devreleri.md — 14 yaprak |
| K1.4.2.1 | Genel Bakış | koruma-devreleri.md L10 |
| K1.4.2.2 | Teknik Spesifikasyonlar | koruma-devreleri.md L14 |
| K1.4.2.3 | DC Offset Koruması | koruma-devreleri.md L25 |
| K1.4.2.4 | Overcurrent Koruması | koruma-devreleri.md L69 |
| K1.4.2.5 | Thermal Shutdown | koruma-devreleri.md L107 |
| K1.4.2.6 | Short Circuit Koruması | koruma-devreleri.md L141 |
| K1.4.2.7 | Bileşen Değerleri | koruma-devreleri.md L157 |
| K1.4.2.8 | Devre Şeması (DC Offset) | koruma-devreleri.md L27 |
| K1.4.2.9 | Çalışma Prensibi | koruma-devreleri.md L58 |
| K1.4.2.10 | Devre Şeması (Overcurrent) | koruma-devreleri.md L71 |
| K1.4.2.11 | Current Limiting Profile | koruma-devreleri.md L98 |
| K1.4.2.12 | Devre Şeması (Thermal) | koruma-devreleri.md L109 |
| K1.4.2.13 | Thermal Profile | koruma-devreleri.md L131 |
| K1.4.2.14 | Çift Koruma | koruma-devreleri.md L143 |


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` (96 satır)

#### 6. index.md — katman bağımlılıkları ve teknik özet

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` - L65-L82

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

## İlgili Dosyalar

[[index.md]] · [[dc-offset-ve-termal-koruma.md]] · [[../k068-guc-kaynagi-analog/index]] · [[../k071-termal-yonetim/index]]
