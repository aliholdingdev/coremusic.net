---
title: "Topraklama ve Güç Dağıtımı - k068-guc-kaynagi-analog"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - verbatim aktarım: _backup/arch-2026-10-06_1057/architecture/"
updated: 2026-10-06
---

# Topraklama ve Güç Dağıtımı

> Klasör: `k068-guc-kaynagi-analog` · Dosya: `topraklama-ve-guc-dagilimi.md`
> Sorumlu persona: `audio-hardware-engineer` (CoreMusic Audio Hardware Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/` — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.

## Genel Bakış

Güç kaynağı koruma devreleri, decoupling stratejisi (kapasitör tipleri, yerleşim, çok katmanlı kart kuralları) ve power sequencing (sıra, kontrol, reset/mikrodenetleyici senaryoları).

## Kapsam ve Sınırlar

- **Kapsam:** güç kaynağı koruma bloğu, decoupling, güç sıralaması (power sequencing).
- **Kapsam dışı:** EMC filtreleme (→ [[regulasyon-ve-filtreleme.md]]); star grounding PCB (→ [[../k070-pcb-tasarim/index]]).
- **Bağlı olduğu klasör:** [[index.md]]
- **Çapraz referanslar:** [[../k070-pcb-tasarim/index]] · [[../k067-koruma-devreleri/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi ve içerik kaynaktan değiştirilmeden kopyalanmıştır; her bloğun üstündeki `> Aktarım:` satırı kaynak dosyanın disk satır aralığını gösterir.
### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` (164 satır)

#### 1. guc-kaynagi-analog.md — koruma devreleri, bağımlılıklar, durum

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` - L138-L164

## Koruma Devreleri

| Koruma | Tip | Değer | Açıklama |
|--------|-----|-------|----------|
| Overcurrent | Cycle-by-cycle | 10A | LM5122 OCP |
| Overvoltage | Zener clamp | 38V | FB pin |
| Thermal | NTC sensor | 85°C shutdown | Soğutucu |
| Soft-start | Capacitor | 100nF | 10ms start-up |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | PCB layout, thermal |
| K1 Amplifikatör | Çıkış | ±35V rail supply |
| K1 Analog Sinyal | Bağlantı | Analog circuit power |
| K2 OS/Sürücüler | Üst | Standby control |

## Durum: Implementasyon

**Durum**: 🟡 Devam Ediyor

- LM5122 evaluation board test edildi, verimlilik %91 doğrulandı
- EMI filter: Pre-compliance test geçildi
- Thermal: Soğutucu tasarımı devam ediyor
- Output ripple: 1.8mVpp (hedef < 10mVpp) ✅
- PCB layout: 4-layer power board planlandı

### `_backup/arch-2026-10-06_1057/architecture/k17-guc-kaynagi/decoupling-strategy.md` (309 satır)

#### 2. decoupling-strategy.md — decoupling stratejisi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k17-guc-kaynagi/decoupling-strategy.md` - L1-L234

---
title: "Decoupling Stratejisi"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Decoupling Stratejisi

## Genel Bakış

Decoupling kapasitörleri, COREMUSIC'in tüm aktif bileşenlerinin (MCU, FPGA, Op-Amp, DAC) güç pin'lerindeki anlık akım taleplerini karşılar. Yüksek frekanslı gürültüyü bastırır, güç rail'lerindeki voltage droop'u önler ve EMI kaynaklarını minimize eder. Çoklu frekans aralığında optimize edilmiş decoupling ağı ile stabil çalışma sağlanır.

## Decoupling Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    DECOUPLING MİMARİSİ                                │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  Katman 1: Bulk Decoupling (10-100μF)                              │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  • Rail başına 1-2 adet                                      │   │
│  │  • Güç kaynağı çıkışına yakın                                │   │
│  │  • Düşük frekans gürültüsünü absorbe eder                   │   │
│  │  • ESR: 10-50mΩ                                              │   │
│  │  • Frequency: DC - 100kHz                                    │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  Katman 2: Local Decoupling (1-10μF)                               │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  • Her IC power pin'ine yakın                                │   │
│  │  • Orta frekans gürültüsünü absorbe eder                    │   │
│  │  • ESR: 5-20mΩ                                               │   │
│  │  • Frequency: 100kHz - 10MHz                                 │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  Katman 3: Bypass Decoupling (10-100nF)                            │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  • Her IC power pin'ine 500mil içinde                        │   │
│  │  • Yüksek frekans gürültüsünü absorbe eder                  │   │
│  │  • ESR: 1-5mΩ                                                │   │
│  │  • Frequency: 1MHz - 100MHz                                  │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  Katman 4: HF Bypass (10-100pF)                                   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  • Kritik high-speed IC'ler için (FPGA, ADC)                │   │
│  │  • Çok yüksek frekans gürültüsünü absorbe eder              │   │
│  │  • ESR: <1mΩ                                                 │   │
│  │  • Frequency: 100MHz - 1GHz                                  │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Frekans Tepkisi

### Impedance vs Frekans

```
Impedance (mΩ)
  1000│ ●                              ● (Bulk only)
      │   ●                          ●
  100 │     ●                      ●
      │       ●                  ●
   10 │         ●──────────────● (Bulk + Local)
      │           ●          ●
    1 │             ●──────● (Bulk + Local + Bypass)
      │               ●──● (All 4 layers)
  0.1 │                 ●
      └──┬────┬────┬────┬────┬────┬────┬────┬──
        1k  10k  100k  1M  10M 100M  1G  10G  Hz

Hedef: Tüm frekanslarda <100mΩ impedans
```

### Kapasitör Tepki Süresi

```
┌───────────────────────────────────────────────────────────────┐
│  KAPASİTÖR TEPKİ SÜRELERİ                                      │
├───────────────────────────────────────────────────────────────┤
│                                                                 │
│  Bulk (470μF):                                                  │
│  • Rise time: 1-10μs                                          │
│  • Peak current: 1-5A                                          │
│  • Uygulama: Rail voltage sag prevention                       │
│                                                                 │
│  Local (10μF):                                                  │
│  • Rise time: 100ns-1μs                                       │
│  • Peak current: 100mA-1A                                      │
│  • Uygulama: IC switching current                              │
│                                                                 │
│  Bypass (100nF):                                                │
│  • Rise time: 10-100ns                                         │
│  • Peak current: 10-100mA                                      │
│  • Uygulama: High-frequency noise                              │
│                                                                 │
│  HF Bypass (100pF):                                             │
│  • Rise time: 1-10ns                                           │
│  • Peak current: 1-10mA                                        │
│  • Uygulama: RF noise, EMI                                     │
│                                                                 │
└───────────────────────────────────────────────────────────────┘
```

## Bileşen Bazlı Decoupling

### STM32L4 MCU

```
┌─────────────────────────────────────────────────────────┐
│  STM32L4 Decoupling Haritası                              │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  VDD (3.3V) - 4 adet power pin:                          │
│  ┌─────────────────────────────────────────────────────┐ │
│  │  Pin 1 (VDD_1):                                     │ │
│  │  • 100nF ceramic (0402) - 200mil içinde             │ │
│  │  • 10μF ceramic (0805) - 500mil içinde              │ │
│  │                                                     │ │
│  │  Pin 24 (VDD_2):                                    │ │
│  │  • 100nF ceramic (0402) - 200mil içinde             │ │
│  │  • 10μF ceramic (0805) - 500mil içinde              │ │
│  │                                                     │ │
│  │  Pin 35 (VDD_3):                                    │ │
│  │  • 100nF ceramic (0402) - 200mil içinde             │ │
│  │  • 10μF ceramic (0805) - 500mil içinde              │ │
│  │                                                     │ │
│  │  Pin 48 (VDD_4):                                    │ │
│  │  • 100nF ceramic (0402) - 200mil içinde             │ │
│  │  • 10μF ceramic (0805) - 500mil içinde              │ │
│  └─────────────────────────────────────────────────────┘ │
│                                                           │
│  VDDA (3.3V analog):                                     │
│  • 1μF ceramic + 10nF ceramic (star connection)         │
│  • Ferrite bead from VDD (600Ω @ 100MHz)                │
│                                                           │
│  VBAT (3.3V):                                            │
│  • 100nF ceramic (0402)                                  │
│                                                           │
│  Total: 8× 100nF + 4× 10μF + 1× 1μF + 1× 10nF         │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

### FPGA (Xilinx Spartan-7)

```
┌─────────────────────────────────────────────────────────┐
│  FPGA Decoupling Haritası                                 │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  VCCINT (1.0V core):                                     │
│  • 22μF × 4 (0805) - Her bank için                       │
│  • 100nF × 8 (0402) - Her 8 pin için                    │
│  • 10nF × 16 (0201) - Her 4 pin için                    │
│  • Total: 88μF + 800nF + 160nF                          │
│                                                           │
│  VCCAUX (1.8V):                                          │
│  • 10μF × 2 (0805)                                       │
│  • 100nF × 4 (0402)                                      │
│                                                           │
│  VCCO (3.3V, 2.5V, 1.8V bank'):                         │
│  • Her bank: 10μF + 100nF                                │
│  • 6 bank × (10μF + 100nF)                               │
│                                                           │
│  Toplam: 88μF + 82μF + 1.2μF + 600nF                   │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

### Op-Amp (LM4562)

```
┌─────────────────────────────────────────────────────────┐
│  Op-Amp Decoupling (Her kanal için)                       │
├─────────────────────────────────────────────────────────┤
│                                                           │
│  V+ (15V):                                               │
│  • 100nF ceramic (0402) - 100mil içinde                  │
│  • 10μF tantal (A-case) - 300mil içinde                 │
│                                                           │
│  V- (-15V):                                              │
│  • 100nF ceramic (0402) - 100mil içinde                  │
│  • 10μF tantal (A-case) - 300mil içinde                 │
│                                                           │
│  Toplam (4 kanal): 8× 100nF + 4× 10μF                   │
│                                                           │
└─────────────────────────────────────────────────────────┘
```

## PCB Decoupling Kuralları

```
┌───────────────────────────────────────────────────────────────┐
│  PCB DECOUPLING LAYOUT KURALLARI                                │
├───────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. MESAFE KURALLARI                                           │
│     ┌─────────────────────────────────────────────────────┐   │
│     │  100nF bypass → IC pin: ≤ 200mil trace             │   │
│     │  10μF local  → IC pin: ≤ 500mil trace             │   │
│     │  470μF bulk  → Rail: ≤ 1000mil trace              │   │
│     │  Via count: ≤ 2 (bypass → GND plane)              │   │
│     └─────────────────────────────────────────────────────┘   │
│                                                                 │
│  2. VIA PLACEMENT                                              │
│     ┌─────────────────────────────────────────────────────┐   │
│     │  Her kapasitör pad'ine en az 1 via                 │   │
│     │  Via çapı: ≥ 10mil (power), ≥ 8mil (signal)       │   │
│     │  Via aralığı: Pad merkezinden ≥ 2× via çapı            │   │
│     │  Thermal relief: GND plane için                    │   │
│     └─────────────────────────────────────────────────────┘   │
│                                                                 │
│  3. TRACE GENİŞLİĞİ                                           │
│     ┌─────────────────────────────────────────────────────┐   │
│     │  Power trace (1A): ≥ 10mil                         │   │
│     │  Power trace (3A): ≥ 30mil                         │   │
│     │  GND return: ≥ 50mil veya copper pour              │   │
│     │  Kelvin sense: 4-wire connection                    │   │
│     └─────────────────────────────────────────────────────┘   │
│                                                                 │
│  4. YERLEŞİM SIRASI                                            │
│     ┌─────────────────────────────────────────────────────┐   │
│     │  Sıra: Bulk → Local → Bypass → IC                  │   │
│     │  Direction: Güç kaynağı → Yük                      │   │
│     │  Orientation: Bypass cap, power trace'ye paralel   │   │
│     └─────────────────────────────────────────────────────┘   │
│                                                                 │
└───────────────────────────────────────────────────────────────┘
```


### `_backup/arch-2026-10-06_1057/architecture/k17-guc-kaynagi/power-sequencing.md` (161 satır)

#### 3. power-sequencing.md — güç sıralaması

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k17-guc-kaynagi/power-sequencing.md` - L1-L161

---
title: "Güç Sıralama Devresi"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Güç Sıralama Devresi (Power Sequencing)

## Genel Bakış

Güç sıralama devresi, COREMUSIC'in tüm güç rail'lerinin doğru zamanda açılmasını ve kapanmasını sağlar. Yanlış sırada açılan rail'ler, latch-up, aşırı akım veya bileşen hasarına neden olabilir. STM32L4 MCU tabanlı aktif sıralama ile her rail için enable/disable zamanlaması kontrol edilir.

## Sıralama Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    GÜÇ SIRALAMA MİMARİSİ                              │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  BAT_EN ────────────────────────────────────────────── HIGH         │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +5V_EN ─────────────────────────────────────── HIGH                │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +3V3_EN ───────────────────────────────── HIGH                    │
│       │                                                              │
│       ▼ +50ms                                                        │
│  MCU_BOOT ──────────────────────────── HIGH                        │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +15V_EN ────────────────────── HIGH                               │
│       │                                                              │
│       ▼ +50ms                                                        │
│  -15V_EN ──────────────── HIGH                                     │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +12V_EN ────────── HIGH                                           │
│       │                                                              │
│       ▼ +50ms                                                        │
│  -12V_EN ──── HIGH                                                 │
│       │                                                              │
│       ▼ +50ms                                                        │
│  +35V_EN ── HIGH                                                   │
│       │                                                              │
│       ▼ +50ms                                                        │
│  -35V_EN HIGH                                                       │
│       │                                                              │
│       ▼ +50ms                                                        │
│  PGOOD ──────── HIGH (tüm rail'ler stabil)                         │
│       │                                                              │
│       ▼ +50ms                                                        │
│  MCU_START HIGH (sistem başlatılabilir)                            │
│                                                                      │
│  Toplam Başlama Süresi: 600ms                                      │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Enable Devresi

### P-MOSFET Enable

```
MCU GPIO (3.3V) ────┬──── Gate
                    │
                  R (10kΩ)
                    │
              VCC (rail)

P-MOSFET (IRLML6402):
• MCU HIGH → VGS = 0V → MOSFET OFF → Rail OFF
• MCU LOW → VGS = -VCC → MOSFET ON → Rail ON
```

### Optocoupler Enable (Yüksek Gerilim Rail'ler)

```
MCU GPIO ──── R ──── Optocoupler LED
                         │
                    Phototransistor
                         │
                    VCC Rail ── Gate

Avantaj: Galvanik izolasyon (MCU vs 35V)
```

## PGOOD Mantığı

```
┌──────────────────────────────────────────────────────┐
│  PGOOD = AND(V_5V_OK, V_3V3_OK, V_15P_OK,          │
│              V_15N_OK, V_12P_OK, V_12N_OK,          │
│              V_35P_OK, V_35N_OK)                    │
│                                                      │
│  V_Rail_OK = (|V measured - V nominal| < 10%)       │
│                                                      │
│  Debounce: 10ms (tüm rail'ler stabil olduktan sonra) │
└──────────────────────────────────────────────────────┘
```

## UVLO (Under-Voltage Lock-Out)

| Rail | Turn-on | Turn-off | Hysteresis |
|------|---------|----------|------------|
| Batarya | 20V | 16.8V | 3.2V |
| +5V | 4.5V | 4.0V | 0.5V |
| +3.3V | 3.0V | 2.7V | 0.3V |
| ±35V | ±32V | ±28V | ±4V |

## Kapanma Sıralaması

```
t=0ms:    +35V, -35V disable (ilk kapanan)
t=50ms:   +12V, -12V disable
t=100ms:  +15V, -15V disable
t=200ms:  +3.3V disable
t=250ms:  +5V disable (son kapanan)
```

## MCU State Machine

```c
typedef enum {
    SEQ_IDLE, SEQ_BAT, SEQ_5V, SEQ_3V3,
    SEQ_MCU_BOOT, SEQ_15V, SEQ_N15V,
    SEQ_12V, SEQ_N12V, SEQ_35V, SEQ_N35V,
    SEQ_PGOOD, SEQ_RUN, SEQ_ERROR
} SeqState;
```

## Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Başlama Süresi | 600ms |
| Kapanma Süresi | 300ms |
| Adım Süresi | 50ms |
| PGOOD Gecikmesi | 10ms |
| Emergency Response | <1μs |

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-LM5122 | ±35V enable |
| K17-VoltageReg | ±15V, ±12V enable |
| K17-BMS | Batarya bağlantısı |
| K1-MCU | GPIO kontrolü |

## Durum: Implementasyon

✅ Sıralama zamanlaması belirlendi (50ms adım)  
✅ Enable lojik devresi tasarlandı  
✅ PGOOD mantığı uygulandı  
✅ UVLO eşikleri ayarlandı  
✅ State machine firmware yazıldı  
✅ Kapanma sıralaması dokümante edildi  
⚠️ Tüm rail sweep testi bekleniyor  
⚠️ Sıcaklık altında timing testi devam ediyor
## İlgili Dosyalar

[[index.md]] · [[regulasyon-ve-filtreleme.md]] · [[../k070-pcb-tasarim/index]] · [[../k067-koruma-devreleri/index]]
