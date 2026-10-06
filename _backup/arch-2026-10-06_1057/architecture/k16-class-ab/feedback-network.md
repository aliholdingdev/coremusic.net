---
title: "Negative Feedback Network"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Negative Feedback Network

## Genel Bakış

Negatif geri besleme ağı, amplifikatörün kazancını, distorsiyonunu, bant genişliğini ve empedans özelliklerini kontrol eder. Kapalı döngü kazancı, geri besleme oranı ile belirlenir. Global negatif geri besleme (NFB), çıkıştan diferansiyel giriş katına uygulanır.

## Devre Şeması

```
                          Rf = 26kΩ (feedback resistor)
                     ┌────┤
                     │    │
                     │  [Rf] 26kΩ ± %1
                     │    │
                     │    ├──────────────────────────────┐
                     │    │                              │
Output ──────────────┤    │                              │
                     │    │                              │
                   [Rg]   │                            [R_in]
                   1kΩ    │                            47kΩ
                     │    │                              │
                     │    │                              │
GND ─────────────────┘    │                              │
                          │                              │
                          └──▶ Diff Pair (-) Input       │
                                                     Diff Pair (+) Input
                                                        │
Input Signal ───[Rin 47kΩ]──▶ Diff Pair (+) ──────────┘

Gain = 1 + Rf / Rg = 1 + 26kΩ / 1kΩ = 27 (28.6dB)
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Topoloji | Global voltage-series NFB | Seri geri besleme |
| Geri besleme direnci (Rf) | 26kΩ ± %1 | Metal film |
| Toprak direnci (Rg) | 1kΩ ± %1 | Metal film |
| Kapalı döngü kazancı | 27 (28.6dB) | 1 + Rf/Rg |
| Açık döngü kazancı | 113,850 (101dB) | |
| Kazanç hata payı | %0.008 | 1/Aβ |
| Giriş empedansı (CL) | 1.27MΩ | R_in × Aβ |
| Çıkış empedansı (CL) | 0.002Ω | R_out / Aβ |
| Bant genişliği (CL) | 1.8MHz | |
| Phase margin | ≥60° | Kararlılık için |

## Hesaplamalar

### Kapalı Döngü Kazançı
```
A_CL = 1 + Rf / Rg
A_CL = 1 + 26,000 / 1,000 = 27 (28.6dB)
```

### Kazanç Hatası (Gain Error)
```
A_CL(nominal) = 27.000
A_CL(tolerans): ΔA/A = ΔRf/Rf × Rf/(Rf+Rg) + ΔRg/Rg × Rg/(Rf+Rg)
ΔA/A = 0.01 × (26/27) + 0.01 × (1/27)
ΔA/A = 0.00963 + 0.00037 = 0.01 = %1

Gerçek kazanç: 27 ± %1
```

### Loop Gain (Aβ)
```
β = Rg / (Rf + Rg) = 1kΩ / (27kΩ) = 0.037

Aβ = A_OL × β = 113,850 × 0.037 = 4,212

Distorsiyon azaltma: THD_CL = THD_OL / (1 + Aβ)
THD_CL = %1 / 4,212 = 0.00024% (teorik)
```

### Giriş Empedansı (Kapalı Döngü)
```
R_in(CL) = R_in(OL) × (1 + Aβ)
R_in(CL) = 30kΩ × 4,212 = 126MΩ (teorik)

Pratik: R_in(CL) = 1.27MΩ (bias direnci sınırlar)
```

### Çıkış Empedansı (Kapalı Döngü)
```
R_out(CL) = R_out(OL) / (1 + Aβ)
R_out(CL) = 0.22Ω / 4,212 = 0.000052Ω = 52μΩ (teorik)

Pratik: ≤0.01Ω (kablo ve connector dahil)
```

## THD Azaltma Analizi

```
Açık döngü THD: THD_OL = %1.0 (Class AB, bias aimsız)

Kapalı döngü THD: THD_CL = THD_OL / (1 + Aβ)
THD_CL = 1.0% / 4,212 = 0.000237%

Pratik THD: %0.008 (diğer kaynaklar dahil)
├── Bias nokta hatası: %0.003
├── Termal modülasyon: %0.002
├── PSRR kaynaklı: %0.001
└── Toplam: %0.008
```

## Frekans Tepkisi

```
Open-loop response:
├── f_p1 = 15.9Hz (dominant, Miller compensated)
├── f_p2 = 60MHz (non-dominant)
└── f_unity = 60.5MHz

Closed-loop response:
├── f_L = 1 / (2π × Rg × C_in) = 1 / (2π × 1kΩ × 10μF) = 16Hz
├── f_H = f_unity / A_CL = 60.5MHz / 27 = 2.24MHz
└── Bandwidth: 16Hz – 2.24MHz (-3dB)

Input coupling capacitor: 10μF (DC blocking)
```

## Faz Marjini Analizi

```
@ f_unity = 60.5MHz:
├── Pole 1 (15.9Hz): -90° faz
├── Pole 2 (60MHz): -45° faz
├── Pole 3 (10MHz): faz katkısı minimal
└── Toplam faz: -135°

Faz marjini: 180° - 135° = 45° (minimum 60° hedef)

Çözüm: Lead compensation (Cf = 10pF seri Rf ile)
Faz marjini optimizasyonu: ≥65°
```

## Bileşen Seçimi

| Bileşen | Değer | Tip | Not |
|---|---|---|---|
| Rf | 26kΩ | Metal film %1 | Düşük termal drift |
| Rg | 1kΩ | Metal film %1 | |
| Cf | 10pF | C0G/NP0 | Lead compensation |
| Rin | 47kΩ | Metal film %1 | |
| C_in | 10μF | Nichicon Muse | Audio grade |

## Durum: Implementasyon
