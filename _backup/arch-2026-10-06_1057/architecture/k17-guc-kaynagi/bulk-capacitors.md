---
title: "Bulk Kapasitör Bankı"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# Bulk Kapasitör Bankı

## Genel Bakış

Bulk kapasitör bankı, COREMUSIC'in tüm güç rail'lerindeki enerji depolama ve ripple akımı absorpsiyonunu sağlar. Anahtarlama converter'ların çıkış filtrelemesinde, dinamik yük değişimlerinde gerilim düşüşünü önler ve EMI performansını iyileştirir. Her rail için optimize edilmiş kapasite, ESR ve ESL değerleri seçilmiştir.

## Kapasitör Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    BULK KAPASİTÖR BANKI MİMARİSİ                      │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  +35V Rail (Boost Output)                                           │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  470μF/50V × 2 (Electrolytic, Low ESR)                     │   │
│  │  + 10μF/50V × 4 (Ceramic, X7R)                              │   │
│  │  + 100nF/50V × 4 (Ceramic, X7R)                             │   │
│  │  Toplam C: 980μF + ESR < 10mΩ                               │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  -35V Rail (Boost Output)                                           │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  Aynı yapı: 470μF×2 + 10μF×4 + 100nF×4                     │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  +15V Rail (LDO Output)                                             │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  100μF/25V × 2 (Electrolytic)                               │   │
│  │  + 10μF/25V × 2 (Ceramic)                                   │   │
│  │  + 100nF/25V × 2 (Ceramic)                                  │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  +5V Rail (Buck Output)                                             │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  470μF/10V × 2 (Electrolytic, Polymer)                      │   │
│  │  + 22μF/10V × 4 (Ceramic, X5R)                              │   │
│  │  + 100nF/10V × 4 (Ceramic)                                  │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
│  +3.3V Rail (Buck Output)                                           │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │  220μF/6.3V × 2 (Polymer)                                   │   │
│  │  + 10μF/6.3V × 4 (Ceramic, X5R)                             │   │
│  │  + 100nF/6.3V × 4 (Ceramic)                                 │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Kapasitör Seçim Kriterleri

### Elektrolitik vs Ceramic vs Polymer

```
┌───────────────────────────────────────────────────────────────────┐
│  KAPASİTÖR TİPİ KARŞILAŞTIRMASI                                    │
├───────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Parametre          Elektrolitik   Ceramic      Polymer           │
│  ─────────────────  ─────────────  ──────────   ──────────        │
│  Kapasite           Yüksek         Düşük-Orta   Yüksek            │
│  ESR                Yüksek         Çok Düşük    Düşük             │
│  ESL                Yüksek         Düşük        Orta              │
│  DC Bias Etkisi     Yok            Yüksek       Yok               │
│  Sıcaklık           -55 to 105°C   -55 to 125°C -55 to 105°C    │
│  Ömür               2000-5000saat  10+ yıl      5000+ saat       │
│  Maliyet            Düşük           Orta-Yüksek  Orta             │
│  Boyut              Orta            Küçük        Orta              │
│  Ripple Akımı       Orta            Yüksek       Yüksek           │
│                                                                     │
│  Kullanım: Bulk + Filtrleme   Decoupling   Bulk + High Freq       │
│                                                                     │
└───────────────────────────────────────────────────────────────────┘
```

## Ripple Akımı Hesaplamaları

### +35V Boost Çıkış Ripple

```
Boost Converter Ripple Akımı:
ΔIL = (VIN × D) / (L × fSW)
ΔIL = (22.2V × 0.366) / (10μH × 1MHz)
ΔIL = 812.5mAp-p

Kapasitör Seçimi:
C_RMS = ΔIL / (8 × fSW × ΔVOUT)
C_RMS = 0.8125A / (8 × 1MHz × 0.35V)
C_RMS = 290μF

Seçim: 470μF × 2 = 940μF (marj ile)

ESR Kaybı:
P_ESR = I²_RMS × ESR
P_ESR = (0.8125A / √12)² × 10mΩ
P_ESR = 0.055mW (çok düşük)
```

### +5V Buck Çıkış Ripple

```
Buck Converter Ripple Akımı:
ΔIL = (VIN - VOUT) × D / (L × fSW)
ΔIL = (22.2V - 5V) × 0.225 / (10μH × 500kHz)
ΔIL = 774mAp-p

Kapasitör Seçimi:
C_OUT = ΔIL / (8 × fSW × ΔVOUT)
C_OUT = 0.774A / (8 × 500kHz × 0.05V)
C_OUT = 387μF

Seçim: 470μF × 2 = 940μF (polymer + ceramic)
```

## ESR/ESL Etkisi

### ESR (Equivalent Series Resistance)

```
ESR'nin Çıkış Ripple Üzerindeki Etkisi:

ΔVOUT = ΔIL × ESR + ΔIL / (8 × fSW × C)

ESR = 50mΩ (eski tip elektrolitik):
ΔV_ESR = 0.8125A × 50mΩ = 40.6mV
ΔV_C = 0.8125A / (8 × 1MHz × 470μF) = 0.22mV
Toplam ΔV = 40.8mV (ESR dominant!)

ESR = 10mΩ (low ESR):
ΔV_ESR = 0.8125A × 10mΩ = 8.1mV
Toplam ΔV = 8.3mV (5× iyileşme)

ESR = 5mΩ (polymer):
ΔV_ESR = 0.8125A × 5mΩ = 4.1mV
Toplam ΔV = 4.3mV (10× iyileşme)
```

### ESL (Equivalent Series Inductance)

```
ESL'nin High-Frequency Davranışı:

X_ESL = 2π × f × ESL

f = 100kHz, ESL = 5nH:
X_ESL = 3.14mΩ (ihmal edilebilir)

f = 10MHz, ESL = 5nH:
X_ESL = 314mΩ (önemli!)

f = 100MHz, ESL = 5nH:
X_ESL = 3.14Ω (dominant!)

Çözüm: Ceramic kapasitörler (ESL < 1nH)
```

## Kapasitör Yerleşim Diyagramı

```
┌───────────────────────────────────────────────────────────────┐
│  PCB KAPASİTÖR YERLEŞİMİ                                       │
├───────────────────────────────────────────────────────────────┤
│                                                                 │
│  +35V Boost Output:                                            │
│  ┌─────────────────────────────────────────────────────────┐  │
│  │                                                           │  │
│  │  [C1] [C2]  ← Bulk (470μF, radial, close to inductor)  │  │
│  │                                                           │  │
│  │  [C3] [C4] [C5] [C6] ← Ceramic (10μF, 0805, at pin)    │  │
│  │                                                           │  │
│  │  [C7] [C8] [C9] [C10] ← Ceramic (100nF, 0402, at pin)  │  │
│  │                                                           │  │
│  │  Kurallar:                                                │  │
│  │  • Bulk caps: Indüktör'e 500mil içinde                   │  │
│  │  • Ceramic: LM5122 VOUT pin'ine 200mil içinde           │  │
│  │  • Bypass: Her power pin'e 100mil içinde                 │  │
│  │  • Ground via: Kapasitör pad'lerine direkt               │  │
│  │                                                           │  │
│  └─────────────────────────────────────────────────────────┘  │
│                                                                 │
└───────────────────────────────────────────────────────────────┘
```

## Sıcaklık ve Ömür Analizi

### Capacitance vs Sıcaklık

```
Kapasite Değişimi (%):
  +30│           ●
     │         ●   ●
  +20│       ●       ●
     │     ●           ●
  +10│   ●               ●
     │ ●                   ●
    0│●───────────────────────●─────────────
     │                        ●
  -10│                          ●
     │                            ●
  -20│                              ●
     │
  -30│
     └──┬────┬────┬────┬────┬────┬────┬──
       -55  -40  -25    0   25   50   85  105°C

X7R Ceramic: ±15% over range
X5R Ceramic: ±22% over range
Electrolytic: -40% @ -25°C, nominal @ 25°C
```

### DC Bias Etkisi (Ceramic)

```
Kapasite Azalımı (%):
  100│●
     │  ●
   80│    ●
     │      ●
   60│        ●
     │          ●
   40│            ●
     │              ●
   20│                ●
     │                  ●
    0│                    ●─────────────────
     └──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
       0  1  2  3  4  5  6  7  8  9  10 11V

X7R 10μF/16V: %50 kayıp @ 5V bias
Çözüm: 2× rated voltage seç (10μF/16V → 5V'ta 5μF)
```

## Spesifikasyonlar

| Rail | Bulk Cap | Ceramic | ESR | ESL | Ripple |
|------|----------|---------|-----|-----|--------|
| +35V | 940μF | 80μF | <10mΩ | <2nH | <50mV |
| -35V | 940μF | 80μF | <10mΩ | <2nH | <50mV |
| +15V | 200μF | 30μF | <20mΩ | <3nH | <5mV |
| -15V | 200μF | 30μF | <20mΩ | <3nH | <5mV |
| +12V | 100μF | 20μF | <30mΩ | <3nH | <10mV |
| +5V | 940μF | 108μF | <10mΩ | <2nH | <50mV |
| +3.3V | 440μF | 56μF | <15mΩ | <2nH | <30mV |

## Bileşen Listesi (Toplam)

| Tip | Değer | Voltaj | Adet | Paket | Kullanım |
|-----|-------|--------|------|-------|----------|
| Elektrolitik | 470μF | 50V | 4 | 10×16mm | +35V/-35V bulk |
| Polymer | 470μF | 10V | 2 | 8×10mm | +5V bulk |
| Polymer | 220μF | 6.3V | 2 | 6.3×8mm | +3.3V bulk |
| Elektrolitik | 100μF | 25V | 4 | 8×12mm | +15V/-15V bulk |
| Ceramic X7R | 10μF | 16V | 20 | 0805 | Decoupling |
| Ceramic X5R | 22μF | 10V | 8 | 0805 | +5V/+3.3V |
| Ceramic | 100nF | 16V | 24 | 0402 | Bypass |

## Bağımlılıklar

| Katman | Bağımlılık |
|--------|------------|
| K17-LM5122 | Boost çıkış filtreleme |
| K17-VoltageReg | LDO/Buck çıkış filtreleme |
| K17-EMC | EMI.performansı |
| K17-Decoupling | MCU/FPGA bypass |

## Durum: Implementasyon

✅ Bulk kapasitör boyutlandırma hesapları tamamlandı  
✅ ESR/ESL analizi yapıldı  
✅ Ripple akım hesaplamaları doğrulandı  
✅ Sıcaklık ve DC bias etkisi analiz edildi  
✅ PCB yerleşim kuralları belirlendi  
✅ Bileşen listesi ve seçim tamamlandı  
⚠️ Uzun vadeli kapasite fading testi bekleniyor  
⚠️ Yüksek sıcaklık yaşam testi devam ediyor
