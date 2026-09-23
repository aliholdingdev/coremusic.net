---
title: "Current Mirror Bias"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Current Mirror Bias

## Genel Bakış

Akım aynası (current mirror), diferansiyel giriş katının aktif yükü olarak görev yapar. Tek bir referans akımını çoğaltarak her iki kolun akımını eşitler. Widlar kaynağı, düşük akım referansı için ideal gerilim düşümü sağlar.

## Devre Şeması

```
         +Vcc (+35V)
          │
         [R_ref] 33kΩ  (referans direnci)
          │
          ├─────────────── Q3 Kol Çıkışı (to VAS)
          │
     ┌────┴────┐
     │   Q3    │  Q3: 2N5551 (NPN)
     │  diode  │  Vbe = 0.65V
     ├─────────┤
     │   Q4    │  Q4: 2N5551 (NPN)
     └────┬────┘
          │
          ├─────────────── Q4 Kol Çıkışı (active load)
          │
     ┌────┴────┐
     │   Q5    │  Q5: Widlar source
     │  diode  │
     ├─────────┤
     │   Q6    │  Q6: Widlar mirror
     └────┬────┘
          │
         [R_W] 2.2kΩ  (Widlar resistor)
          │
        -Vee (-35V)

Q3/Q4: Referans aynası (I_ref = 1mA)
Q5/Q6: Widlar aynası (I_out = 100μA)
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Topoloji | Basic + Widlar kombine | İki aşamalı |
| Referans akımı (I_ref) | 1mA | Vcc-Vee / R_ref |
| Widlar çıkışı (I_out) | 100μA | Vbe farkı ile |
| Transistör | 2N5551 / BC550C | NPN, hFE ≥ 200 |
| Output empedansı | ≥500kΩ | Aktif yük avantajı |
| PSRR | ≥90dB | Akım kaynağı doğrusallığı |
| Termal drift | ≤0.1%/°C | Widlar stabilitesi |

## Hesaplamalar

### Referans Akımı
```
I_ref = (Vcc - Vbe_Q3) / R_ref
I_ref = (35 - 0.65) / 33kΩ = 1.04mA ≈ 1mA
```

### Widlar Çıkış Akımı
```
I_out = (Vt / R_W) × ln(I_ref / I_out)
I_out = (26mV / 2.2kΩ) × ln(1mA / 100μA)
I_out = 11.8μA × ln(10) = 11.8μA × 2.303
I_out ≈ 27.2μA

Not: Widlar posta pulu (postage stamp) etkisi nedeniyle
 gerçek değer料理 ayarlanır. Hedef: 100μA
```

### Ayna Hata Analizi
```
ΔI_out / I_out = ΔVbe / Vt + ΔI_ref / I_ref
ΔI_out / I_out ≤ %1 (eşlenmiş transistörler)

Nedeni: Vbe eşleştirme ±2mV → ΔI/I ≈ %7.7/°C
Çözüm: Thermally coupled layout
```

### Çıkış Empedansı (Aktif Yük)
```
r_o = V_A / I_C  (Early Voltage / Collector Current)
r_o = 100V / 1mA = 100kΩ (2N5551 için)

V_A ≈ 100-150V (2N5551)
```

## Widlar Kaynağı Detayı

Widlar aynası, standart aynaya göre avantajları:
- Yüksek çıkış empedansı (500kΩ+)
- Düşük akım (@100μA) doğrudan üretir
- R_refโดยไม่ต้อง >100kΩ
- Termal olarak stabilite

```
ΔVbe = Vt × ln(I_ref / I_out) = 26mV × ln(10) ≈ 60mV
```

## Transistör Eşleştirme

| Parametre | Q3 | Q4 | Tolerans |
|---|---|---|---|
| Vbe @ 1mA | 0.65V | 0.65V | ≤2mV |
| hFE | 200-600 | 200-600 | ≤%10 |
| fT | ≥100MHz | ≥100MHz | - |
| Paket | TO-92 | TO-92 | Çift paket tercih |

## Layout Kuralları

1. Q3 ve Q4 termal olarak yakın (1mm max)
2. R_ref ve R_W düşük tolerance metal film
3. Akım yolları simetrik
4. Vcc ve Vee bypass kapasitörlerine yakın

## Durum: Implementasyon
