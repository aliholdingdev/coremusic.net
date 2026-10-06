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

## DC Bias ve Sıcaklık Düzeltmeleri

### Ceramic DC Bias Kaybı

```
X7R 10μF/16V (0805):
┌─────────────────────────────────────────────┐
│  Bias Voltajı    Eff. Kapasite    Kayıp    │
│  ─────────────   ──────────────   ──────    │
│  0V              10.0μF           %0        │
│  1.8V (3.3V rail) 7.5μF          %25       │
│  3.3V            5.5μF            %45       │
│  5V              4.2μF            %58       │
│  10V             2.8μF            %72       │
│  16V             2.0μF            %80       │
└─────────────────────────────────────────────┘

Çözüm: Rated voltage'ı 2-3× seç
3.3V rail için: 10μF/16V → 3.3V'ta %25 kayıp = 7.5μF
```

### Sıcaklık Katsayısı

```
X7R: ±15% (-55°C to +125°C)
X5R: ±22% (-55°C to +85°C)
C0G: ±30ppm/°C (high precision)

Sıcaklık Telafisi:
C_eff = C_nominal × (1 + TC × ΔT)
C_eff = 10μF × (1 + 0.0015 × (-40°C - 25°C))
C_eff = 10μF × (1 - 0.0975)
C_eff = 9.025μF (%10 kayıp @ -40°C)
```

## Spesifikasyonlar

| Katman | Tip | Değer | Adet/IC | Frekans |
|--------|-----|-------|---------|---------|
| Bulk | Electrolytic/Polymer | 10-470μF | 1-2/rail | DC-100kHz |
| Local | Ceramic X7R | 1-10μF | 1-2/pin | 100k-10MHz |
| Bypass | Ceramic X7R | 100nF | 1/pin | 1M-100MHz |
| HF | Ceramic C0G | 10-100pF | 1/high-speed pin | 100M-1GHz |

## Toplam Decoupling Bileşen Sayısı

| Bileşen | Değer | Adet | Kullanım |
|---------|-------|------|----------|
| Ceramic | 100nF/0402 | 60+ | Tüm IC'ler |
| Ceramic | 10μF/0805 | 30+ | Local decoupling |
| Ceramic | 22μF/0805 | 8 | FPGA VCCINT |
| Ceramic | 1μF/0402 | 10 | VDDA,-sensitive |
| Ceramic | 10nF/0201 | 16 | FPGA high-speed |
| Ceramic | 100pF/0201 | 8 | HF bypass |

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-BulkCaps | Bulk decoupling |
| K17-VoltageReg | Rail stabilize |
| K4-FPGA | FPGA power pins |
| K1-MCU | STM32 power pins |
| K3-AudioEngine | Op-Amp power |

## Durum: Implementasyon

✅ Decoupling stratejisi (4 katman) belirlendi  
✅ Bileşen bazlı decoupling haritası oluşturuldu  
✅ PCB layout kuralları dokümante edildi  
✅ DC bias ve sıcaklık düzeltmeleri hesaplandı  
✅ Frekans tepkisi analiz edildi  
✅ Toplam bileşen sayısı belirlendi  
⚠️ Impedance measurement (VNA) bekleniyor  
⚠️ PCB sonrası validasyon testi gerekli
