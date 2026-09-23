---
title: "Short Circuit Protection"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# Short Circuit Protection

## Genel Bakış

Kısa devre koruması, çıkış doğrudan toprağa veya besleme gerilimine kısa devre edildiğinde output transistörlerini ve besleme kaynaklarını korur. SOA (Safe Operating Area) sınırlaması ileMJL21194/MJL21193'ün güvenli çalışma alanını korur. Akım foldback + thermal koruma ile kombine çalışır.

## Devre Şeması

```
       +Vcc (+35V)
        │
       [R_s] 0.22Ω  (current sense)
        │
        ├──────────────────────── To Output
        │
   ┌────┴────┐
   │  Q17    │  Q17: BD139 (NPN)
   │  SOA    │  SOA limiter
   │  Sense  │
   ├────┬────┤
   │  [R27]  │  R27: 220Ω (base threshold)
   │    │    │
   │  [R28]  │  R28: 47Ω (foldback)
   │    │    │
   │  [R29]  │  R29: 10Ω (output clamp)
   │    │    │
   └────┬────┘
        │
        ├─────────────── To Bias (Vbe multiplier)
        │
   ┌────┴────┐
   │  D2     │  D2: 1N4007 (reverse protection)
   │  +  D3  │  D3: 1N4007
   │  (back- │  Back-to-back Zener clamp
   │  to-back│
   │  zener) │
   └────┬────┘
        │
       GND

Short circuit senaryosu:
├── Output GND'ye kısa devre
├── Output +Vcc'ye kısa devre
└── Output -Vee'ye kısa devre
```

## Teknik Spesifikasyonlar

| Parametre | Değer | Not |
|---|---|---|
| Short circuit akımı | 5A (peak) | Clamp edilmiş |
| Foldback akımı | 1.5A | 50ms sonra |
| Tepki süresi | <10μs | Anlık koruma |
| SOA sınırlaması | P ≤ 250W @ 10ms | MJL21194 datasheet |
| Clamp voltajı | ±35V (Zener) | Back-to-back 33V |
| R_sense | 0.22Ω ± %5 | 5W wirewound |
| Koruma süresi | Süresiz | Short circuit kalıcı |

## Hesaplamalar

### Kısa Devre Akımı
```
Kısa devre durumunda (R_L = 0):
I_sc = V_cc / (R_s + R_CE(sat))
I_sc = 35V / (0.22Ω + 1.5Ω) = 23A (teorik)

Clamp ile sınırlandırma:
I_clamp = V_Zener / R_sense = 33V / 0.22Ω = 150A (çok yüksek)

Pratik: Q17 V_BE trigger ile akım sınırlama:
I_limit = V_BE(Q17) / R_sense = 0.65V / 0.22Ω = 2.95A

Q17 Collector akımı → Vbe multiplier'ı kısar
```

### SOA Sınırlaması (Safe Operating Area)
```
MJL21194 SOA sınırları:
├── 10ms pulse: I_C = 30A, V_CE = 250V
├── 100ms pulse: I_C = 16A, V_CE = 250V
├── DC: I_C = 16A, V_CE = 167V (P=250W)
└── Thermal limit: P = Tj_max / R_θJC = 125/0.41 = 305W

SOA storyline (kısa devre):
├── t=0-10μs: Anlık akım 2.95A
├── t=10μs-10ms: Akım 2.95A (pulse width)
├── t=10ms-100ms: Akım azalır (foldback)
└── t>100ms: Foldback tamamlandı, I ≤ 1.5A
```

### Foldback Dinamiği
```
Q17 base voltajı:
V_B = I_out × R_sense × (R28 / (R28 + R29))
V_B = I_out × 0.22 × (47 / 57)
V_B = I_out × 0.18

Trigger: V_B = 0.65V
I_foldback_start = 0.65 / 0.18 = 3.6A (peak)

Short circuit durumunda:
V_out = 0V
I_sc = V_out / R_sense (akım sınırlaması)
I_sc = 35V / (0.22Ω + foldback_impedance)
I_sc ≈ 1.5A (foldback tamamlandı)
```

### Güvenli Çalışma Süresi
```
SC pulse enerjisi (worst case):
E = P × t = 250W × 10ms = 2.5 Joule

MJL21194 enerji kapasitesi:
E_max = Cv × ΔV² = 200pF × 250² = 12.5mJ (kapasitif)
E_max = ½ × L × I² (indüktif)

Pratik: 2.5J > 12.5mJ → Active protection gerekli
```

## Koruma Senaryoları

### Senaryo 1: Çıkış → GND
```
1. R_L = 0Ω
2. I_out = V_cc / R_sense = 159A (theoretical)
3. Q17 V_BE trigger (0-10μs)
4. Vbe multiplier kısılır
5. I_out 2.95A'ya sınırlanır
6. Foldback başlar
7. 100ms sonra I_out = 1.5A
8. MJL21194 güç: 35V × 1.5A = 52.5W < 250W ✓
```

### Senaryo 2: Çıkış → +Vcc
```
1. R_L = 0Ω (short to rail)
2. MJL21194 reverse biased
3. MJL21193 forward biased (PNP)
4. Negative foldback circuit tetiklenir
5. Q16 (PNP sense) aktif
6. Akım sınırlanır
```

### Senaryo 3: Çıkış → -Vee
```
1. R_L = 0Ω (short to negatif rail)
2. MJL21193 reverse biased
3. MJL21194 forward biased (NPN)
4. Positive foldback circuit tetiklenir
5. Q15 (NPN sense) aktif
6. Akım sınırlanır
```

## Bileşen Seçimi

| Bileşen | Değer | Tip | Not |
|---|---|---|---|
| Q17 | BD139 | NPN, TO-126 | SOA sense |
| Q18 | BD140 | PNP, TO-126 | Negative SOA |
| R_sense | 0.22Ω | 5W wirewound | ±%5 |
| R27 | 220Ω | Metal film %1 | |
| R28 | 47Ω | Metal film %1 | |
| R29 | 10Ω | Metal film %1 | |
| D2 | 1N4007 | Si diode | Reverse protection |
| D3 | 1N4007 | Si diode | Back-to-back clamp |
| Zener | 33V | BZX55C33 | Clamp voltage |

## Layout Kuralları

1. R_sense kısa ve geniş trace (5A continuous)
2. Q17 heatsink'te mounting (termal koruma)
3. Zener diodes close to output terminal
4. Foldback traces short, symmetrical

## Durum: Implementasyon
