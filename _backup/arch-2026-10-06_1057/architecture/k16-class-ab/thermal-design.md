---
title: "Thermal Design & Heatsink"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Thermal Design & Heatsink

## Genel Bakış

Isı tasarımı, Class AB amplifikatörün sürekli çalışmasını sağlamak için junction sıcaklıklarını 150°C sınırının altında tutar. Heatsink boyutlandırma, termal direnç zinciri analizi ve aktif soğutma (fan) seçimi bu dokümanda ele alınır.

## Termal Direnç Zinciri

```
Junction (Tj)
    │ R_θJC = 0.41°C/W
    ▼
Case (Tc)
    │ R_θCS = 0.50°C/W (thermal compound)
    ▼
Heatsink (Ts)
    │ R_θSA = 1.50°C/W (heatsink-ambient)
    ▼
Ambient (Ta = 25°C)

Tj = Ta + P × (R_θJC + R_θCS + R_θSA)
Tj = 25 + 50 × (0.41 + 0.50 + 1.50)
Tj = 25 + 50 × 2.41 = 145.5°C (< 150°C) ✓
```

## Heatsink Spesifikasyonları

| Parametre | Değer | Not |
|---|---|---|
| Heatsink tipi | Extruded aluminum | 6063-T5 alüminyum |
| Boyut | 200 × 100 × 50mm | (U × G × Y) |
| Ağırlık | 0.8 kg | |
| Termal direnç | ≤1.5°C/W | natural convection |
| Fin sayısı | 12 | |
| Fin aralığı | 7.5mm | |
| Fin kalınlığı | 1.5mm | |
| Taban kalınlığı | 6mm | |
| Yüzey | Anodized black | Isı yayılımı için |
| Montaj | M3 vidalar × 4 | Torque: 0.5 N·m |

## Hesaplamalar

### Isı Yükü
```
Her kanal için (100W @ 8Ω, Class AB):
P_total = V_cc × I_avg
P_total = 35V × (2 × I_peak / π)
P_total = 35V × (2 × 4.05 / π) = 35 × 2.58 = 90.3W

Verimlilik: η = P_out / P_total = 65.6 / 90.3 = 72.7%
Isı kaybı: P_heat = 90.3 - 65.6 = 24.7W (per channel)

8 kanal toplam: P_heat_total = 8 × 24.7 = 197.6W
```

### Heatsink Sıcaklığı
```
T_s = T_a + P_total × R_θSA
T_s = 25 + 197.6 × 1.5 = 25 + 296.4 = 321.4°C

SORUN: Tek heatsink yetersiz!
Çözüm: 8 ayrı heatsink veya aktif soğutma
```

### Ayrı Heatsink Tasarımı (Kanat başına)
```
Her kanal için tek heatsink:
P_heat = 24.7W
R_θSA_required = (Tj_max - T_a) / P_heat - R_θJC - R_θCS
R_θSA_required = (150 - 25) / 24.7 - 0.41 - 0.50
R_θSA_required = 125 / 24.7 - 0.91 = 5.06 - 0.91 = 4.15°C/W

Seçim: R_θSA = 3.5°C/W (güvenli margin ile)
Boyut: 100 × 60 × 40mm (daha küçük, kanal başına)
```

### Fan ile Aktif Soğutma
```
Fan seçimi (her kanal):
├── 60mm axial fan
├── Airflow: 20 CFM
├── Static pressure: 0.15 inH₂O
├── Noise: 28 dBA
├── Güç: 12V DC, 100mA
└── Ömür: 50,000 saat

Fan ile R_θSA: 1.0°C/W (forced convection)
T_s = 25 + 24.7 × 1.0 = 49.7°C (< 85°C) ✓
```

## Termal Ped ve Compound

| Malzeme | Kalınlık | W/(m·K) | Uygulama |
|---|---|---|---|
| Arctic MX-6 | 0.05mm | 6.0 | Transistör-case |
| Sil-Pad 1500 | 0.18mm | 1.4 | Isolated mount |
| Bergquist HPLS | 0.23mm | 3.0 | High performance |
| Mica | 0.05mm | 0.7 | Electrical isolation |

### Termal Direnç Hesabı (Compound)
```
R_θCS = L / (k × A)
L = 0.05mm = 5×10⁻⁵m
k = 6.0 W/(m·K)
A = 1.5cm² = 1.5×10⁻⁴m²

R_θCS = 5×10⁻⁵ / (6.0 × 1.5×10⁻⁴)
R_θCS = 5×10⁻⁵ / 9×10⁻⁴ = 0.056°C/W

Ancak: Contact resistance dahil → 0.50°C/W (pratik)
```

## Isı Dağılım Diyagramı

```
           MJL21194          MJL21193
           ┌─────┐           ┌─────┐
           │250W │           │250W │
           │ Pcb │           │ Pcb │
           └──┬──┘           └──┬──┘
              │ Thermal compound│
         ┌────┴────────────────┴────┐
         │     Heatsink (per channel)│
         │     R_θ = 3.5°C/W        │
         │     100×60×40mm          │
         └────────────┬─────────────┘
                      │
              ┌───────┴───────┐
              │   Fan (60mm)  │
              │   20 CFM      │
              └───────┬───────┘
                      │
                   Ambient
```

## Termal Zaman Sabitleri

```
Heatsink termal kapasitesi:
C_θ = m × c_p = 0.2kg × 900J/(kg·K) = 180J/K

Termal zaman sabiti:
τ = C_θ × R_θ = 180J/K × 3.5°C/W = 630 saniye ≈ 10.5 dakika

Operasyonel sonuç:
- 10.5 dakika tam yükte çalışma → termal denge
- Load step değişimi için 10.5dk settling time
- Fan failure durumunda 5dk safe shutdown window
```

## Montaj Prosedürü

1. Heatsink yüzeyini temizle (isopropil alkol)
2. Transistör pad'lerine ince tabaka MX-6 uygula
3. Transistörleri heatsink'e M3 vidalar ile sabitle
4. Torque: 0.5 N·m (aşırı sıkmamak)
5. Thermal compound simetrik dağılmalı
6. Fan mount,lastik shock absorber ile
7. Power-on sonrası 30dk burn-in test

## Durum: Implementasyon
