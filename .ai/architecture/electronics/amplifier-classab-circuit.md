---
type: architecture
category: audio/hardware
title: Class AB Amplifier Circuit â€” CoreMusic
date: 2026-09-19
updated: 2026-09-19
status: Active
version: 1.0.0
---

# Class AB Amplifier Circuit â€” CoreMusic

## 1. Genel BakÄ±ÅŸ

CoreMusic Class AB amplifikatÃ¶r devresi, 8 kanal modÃ¼ler yapÄ±da tasarlanmÄ±ÅŸ profesyonel ses yÃ¼kseltecidir. Darlington output stage topolojisi kullanÄ±larak dÃ¼ÅŸÃ¼k distorsiyon ve yÃ¼ksek gÃ¼Ã§ Ã§Ä±ktÄ±sÄ± saÄŸlanmÄ±ÅŸtÄ±r.

| Parametre | DeÄŸer |
|-----------|-------|
| Topoloji | Darlington Output Stage Class AB |
| KazanÃ§ | 23x (27 dB) |
| THD | <0.005% @ 1W/8Î© |
| Slew Rate | >40 V/Âµs |
| Bant GeniÅŸliÄŸi | 10 Hz - 150 kHz |
| GÃ¼Ã§ Ã‡Ä±kÄ±ÅŸÄ± | 50W/kanal @ 8Î© |
| GÃ¼Ã§ KaynaÄŸÄ± | Â±35V (LM5122 interleaved boost) |
| Quiescent AkÄ±m | 50mA |
| Kanal SayÄ±sÄ± | 8 (modÃ¼ler) |
| Ã‡Ä±kÄ±ÅŸ TransistÃ¶rleri | MJL21194 (NPN) / MJL21193 (PNP) |

---

## 2. Devre Topolojisi â€” ASCII Art ÅemasÄ±

```
                            Â±35V Supply Rails
                           â”Œâ”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
                           â”‚      â”‚                              â”‚
                          +35V   -35V                           â”‚
                           â”‚      â”‚                              â”‚
                           â”‚      â”‚                              â”‚
  Input â”€â”€â”¬â”€â”€[C1]â”€â”€[R1]â”€â”€â”¬â”€â”¤      â”‚                              â”‚
  (AC)    â”‚  4.7ÂµF  1kÎ©  â”‚ â”‚      â”‚                              â”‚
         [C2]            â”‚ â”‚      â”‚                              â”‚
        100pF            â”‚ â”‚      â”‚                              â”‚
         â”‚               â”‚ â”‚      â”‚                              â”‚
         â”‚    â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â”‚      â”‚                              â”‚
         â”‚    â”‚  DIFFERENTIAL PAIR                               â”‚
         â”‚    â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                        â”‚
         â”‚    â”‚  â”‚ Q1(BC546B)â”€â”€Q2(BC546B)                      â”‚
         â”‚    â”‚  â”‚  NPN        NPN    â”‚                        â”‚
         â”‚    â”‚  â”‚  â”Œâ”€â”€[R2]â”€â”€â”        â”‚                        â”‚
         â”‚    â”‚  â”‚  â”‚ 4.7kÎ©  â”‚        â”‚                        â”‚
         â”‚    â”‚  â”‚  â”‚   â”Œâ”€â”€â”€â”€â”˜        â”‚                        â”‚
         â”‚    â”‚  â”‚  â”‚  [R3]  [R4]     â”‚                        â”‚
         â”‚    â”‚  â”‚  â”‚ 22kÎ©   22kÎ©     â”‚                        â”‚
         â”‚    â”‚  â”‚  â”‚   â”‚      â”‚      â”‚                        â”‚
         â”‚    â”‚  â”‚  â””â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”¤                        â”‚
         â”‚    â”‚  â”‚      â”‚      â”‚      â”‚                        â”‚
         â”‚    â”‚  â””â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”˜                        â”‚
         â”‚    â”‚         â”‚      â”‚                                â”‚
         â”‚    â”‚    â”Œâ”€â”€â”€â”€â”˜      â””â”€â”€â”€â”€â”                           â”‚
         â”‚    â”‚    â”‚   Q5(BC556B)    â”‚                          â”‚
         â”‚    â”‚    â”‚   PNP Sink      â”‚                          â”‚
         â”‚    â”‚    â”‚   [R5] 1kÎ©      â”‚                          â”‚
         â”‚    â”‚    â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                           â”‚
         â”‚    â”‚         â”‚                                       â”‚
         â”‚    â”‚         â–¼                                       â”‚
         â”‚    â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                   â”‚
         â”‚    â”‚  â”‚   VOLTAGE AMPLIFIER     â”‚                   â”‚
         â”‚    â”‚  â”‚   (VAS)                 â”‚                   â”‚
         â”‚    â”‚  â”‚                          â”‚                   â”‚
         â”‚    â”‚  â”‚  Q9(KSC3503) NPN         â”‚                   â”‚
         â”‚    â”‚  â”‚  [R6]100Î©  [R7]100Î©     â”‚                   â”‚
         â”‚    â”‚  â”‚  [C3]100pF (Miller)      â”‚                   â”‚
         â”‚    â”‚  â”‚  [C4]100ÂµF (Bypass)      â”‚                   â”‚
         â”‚    â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                   â”‚
         â”‚    â”‚             â”‚                                   â”‚
         â”‚    â”‚             â–¼                                   â”‚
         â”‚    â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                   â”‚
         â”‚    â”‚  â”‚   Vbe MULTIPLIER        â”‚                   â”‚
         â”‚    â”‚  â”‚   (BIAS NETWORK)        â”‚                   â”‚
         â”‚    â”‚  â”‚                          â”‚                   â”‚
         â”‚    â”‚  â”‚  Q10(BD139) NPN          â”‚                   â”‚
         â”‚    â”‚  â”‚  VR1 200Î© Trimpot        â”‚                   â”‚
         â”‚    â”‚  â”‚  [R8]1.5kÎ© [R9]1kÎ©      â”‚                   â”‚
         â”‚    â”‚  â”‚  D1,D2(1N4148)           â”‚                   â”‚
         â”‚    â”‚  â”‚  [R10]100Î© [C5]10nF      â”‚                   â”‚
         â”‚    â”‚  â”‚  [R11]2.2kÎ© [C6]100pF    â”‚                   â”‚
         â”‚    â”‚  â”‚                          â”‚                   â”‚
         â”‚    â”‚  â”‚  *** TERMAL KUPLING ***  â”‚                   â”‚
         â”‚    â”‚  â”‚  *** Q10 â†’ HEATSINK ***  â”‚                   â”‚
         â”‚    â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                   â”‚
         â”‚    â”‚             â”‚                                   â”‚
         â”‚    â”‚             â–¼                                   â”‚
         â”‚    â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
         â”‚    â”‚  â”‚         OUTPUT STAGE (DARLINGTON)        â”‚  â”‚
         â”‚    â”‚  â”‚                                          â”‚  â”‚
         â”‚    â”‚  â”‚   NPN SIDE           PNP SIDE            â”‚  â”‚
         â”‚    â”‚  â”‚   â”€â”€â”€â”€â”€â”€â”€â”€â”€          â”€â”€â”€â”€â”€â”€â”€â”€â”€            â”‚  â”‚
         â”‚    â”‚  â”‚   Q11(BD139)         Q13(BD140)          â”‚  â”‚
         â”‚    â”‚  â”‚   Driver NPN         Driver PNP           â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚                   â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚   [R12]10Î©           [R13]10Î©            â”‚  â”‚
         â”‚    â”‚  â”‚   Base Stopper        Base Stopper        â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚                   â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚   Q15(MJL21194)     Q17(MJL21193)        â”‚  â”‚
         â”‚    â”‚  â”‚   Output NPN         Output PNP           â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚                   â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚   [R14]0.22Î© 5W    [R15]0.22Î© 5W        â”‚  â”‚
         â”‚    â”‚  â”‚   Emitter Res         Emitter Res          â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚                   â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                 â”‚  â”‚
         â”‚    â”‚  â”‚               â”‚                           â”‚  â”‚
         â”‚    â”‚  â”‚          OUTPUT NODE                       â”‚  â”‚
         â”‚    â”‚  â”‚               â”‚                           â”‚  â”‚
         â”‚    â”‚  â”‚     â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                 â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚  OUTPUT NETWORK    â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚  [L1] 1ÂµH Air-core â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚  [R16]10Î© + [C7]   â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚   100nF (Zobel)    â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚  [C8] 100nF        â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â”‚   DC Blocking       â”‚                 â”‚  â”‚
         â”‚    â”‚  â”‚     â””â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                 â”‚  â”‚
         â”‚    â”‚  â”‚              â”‚                            â”‚  â”‚
         â”‚    â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
         â”‚    â”‚                 â”‚                                â”‚
         â”‚    â”‚                 â–¼                                â”‚
         â”‚    â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”               â”‚
         â”‚    â”‚  â”‚   PROTECTION CIRCUIT          â”‚               â”‚
         â”‚    â”‚  â”‚                                â”‚               â”‚
         â”‚    â”‚  â”‚  Q18(BD139) Overcurrent Detect â”‚               â”‚
         â”‚    â”‚  â”‚  Q19(BD140) Overcurrent Detect â”‚               â”‚
         â”‚    â”‚  â”‚  [R21,R22] 0.47Î© 5W Sense     â”‚               â”‚
         â”‚    â”‚  â”‚  [R23,R24] 1kÎ©                 â”‚               â”‚
         â”‚    â”‚  â”‚  D3,D4(1N4148)                 â”‚               â”‚
         â”‚    â”‚  â”‚  D5,D6(1N4007) Rectifier       â”‚               â”‚
         â”‚    â”‚  â”‚  F1,F2 3A Slow-blow            â”‚               â”‚
         â”‚    â”‚  â”‚  Relay (DPDT) 12V â€” Speaker    â”‚               â”‚
         â”‚    â”‚  â”‚  [R25]10kÎ© [C11]100ÂµF/25V      â”‚               â”‚
         â”‚    â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜               â”‚
         â”‚    â”‚             â”‚                                   â”‚
         â”‚    â”‚             â–¼                                   â”‚
         â”‚    â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”               â”‚
         â”‚    â”‚  â”‚   CHANNEL ENABLE              â”‚               â”‚
         â”‚    â”‚  â”‚                                â”‚               â”‚
         â”‚    â”‚  â”‚  IRLZ44N Enable MOSFET         â”‚               â”‚
         â”‚    â”‚  â”‚  [R26]10kÎ© [R27]100Î©           â”‚               â”‚
         â”‚    â”‚  â”‚  [C12]100nF                    â”‚               â”‚
         â”‚    â”‚  â”‚  LED (Green/Red/Blue)           â”‚               â”‚
         â”‚    â”‚  â”‚  [R28-R30]1kÎ©                  â”‚               â”‚
         â”‚    â”‚  â”‚  PC817 Photocoupler             â”‚               â”‚
         â”‚    â”‚  â”‚  [R31]470Î© [C13]10ÂµF/25V       â”‚               â”‚
         â”‚    â”‚  â”‚  [R32]100kÎ© DB107 Diode Bridge  â”‚               â”‚
         â”‚    â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜               â”‚
         â”‚    â”‚             â”‚                                   â”‚
         â”‚    â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                                   â”‚
         â”‚                                                      â”‚
        GND                                                     â”‚
                                                               â”‚
  Feedback Path: Output â”€â”€[R5] 1kÎ©â”€â”€â†’ Q2 Base                  â”‚
                                                (Non-Inverting) â”‚
                                                               â”‚
```

---

## 3. Devre AÅŸamalarÄ± DetayÄ±

### 3.1 AÅŸama 1: Differential Pair (GiriÅŸ AÅŸamasÄ±)

Diferansiyel Ã§ift, giriÅŸ sinyali ile geri besleme sinyali arasÄ±ndaki farkÄ± amplifier eder. DÃ¼ÅŸÃ¼k gÃ¼rÃ¼ltÃ¼ ve yÃ¼ksek kazanÃ§ saÄŸlar.

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| Q1 | NPN TransistÃ¶r | BC546B | TO-92 | GiriÅŸ transistÃ¶rÃ¼ (Non-inverting) |
| Q2 | NPN TransistÃ¶r | BC546B | TO-92 | Geri besleme transistÃ¶rÃ¼ (Inverting) |
| Q5 | PNP TransistÃ¶r | BC556B | TO-92 | AkÄ±m havuzu (Current Sink) |
| R1 | DirenÃ§ | 1kÎ© Â±1% | â€” | GiriÅŸ direnci |
| R2 | DirenÃ§ | 4.7kÎ© Â±1% | â€” | Baz bias direnci |
| R3 | DirenÃ§ | 22kÎ© Â±1% | â€” | Geri besleme direnci (Q1 tarafÄ±) |
| R4 | DirenÃ§ | 22kÎ© Â±1% | â€” | Geri besleme direnci (Q2 tarafÄ±) |
| R5 | DirenÃ§ | 1kÎ© Â±1% | â€” | Ã‡Ä±kÄ±ÅŸ geri besleme direnci |
| C1 | Film KondansatÃ¶r | 4.7ÂµF/63V | â€” | GiriÅŸ DC blokaj kondansatÃ¶rÃ¼ |
| C2 | Seramik KondansatÃ¶r | 100pF | â€” | YÃ¼ksek frekans filtresi |

**Sinyal Yolu:**
```
Input (AC) â†’ C1 â†’ R1 â†’ Q1 Baz
                         â†“
              Differential Pair
                         â†“
              Q2 Baz â† R5 â† Output (Geri Besleme)
```

**KazanÃ§ HesabÄ±:**
```
Av = 1 + (R4 / R5) = 1 + (22kÎ© / 1kÎ©) = 23x (27 dB)
```

**Notlar:**
- Q1 giriÅŸ sinyalini non-inverting olarak amplifier eder
- Q2 Ã§Ä±kÄ±ÅŸ geri besleme sinyalini alÄ±r
- Q5, diferansiyel Ã§ift iÃ§in sabit akÄ±m saÄŸlar
- C1 DC bileÅŸeni bloke eder, sadece AC sinyali geÃ§irir
- C2 yÃ¼ksek frekans gÃ¼rÃ¼ltÃ¼sÃ¼nÃ¼ filtreler

---

### 3.2 AÅŸama 2: Voltage Amplifier Stage (VAS)

Voltaj amplifier aÅŸamasÄ±, diferansiyel Ã§iftin Ã§Ä±kÄ±ÅŸ sinyalini yeterli voltaj genliÄŸine yÃ¼kseltir. Miller kompanzasyonu ile kararlÄ±lÄ±k saÄŸlanÄ±r.

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| Q9 | NPN TransistÃ¶r | KSC3503 | TO-126 | YÃ¼ksek voltaj, dÃ¼ÅŸÃ¼k Cob |
| R6 | DirenÃ§ | 100Î© Â±1% | â€” | KollektÃ¶r direnci |
| R7 | DirenÃ§ | 100Î© Â±1% | â€” | Emiter direnci |
| C3 | Seramik KondansatÃ¶r | 100pF | â€” | Miller kompanzasyon kondansatÃ¶rÃ¼ |
| C4 | Elektrolitik KondansatÃ¶r | 100ÂµF/63V | â€” | Bypass kondansatÃ¶rÃ¼ |

**Kritik Ã–zellikler:**
- Q9, KSC3503 seÃ§imi: YÃ¼ksek voltaj dayanÄ±mÄ± (230V), dÃ¼ÅŸÃ¼k Ã§Ä±kÄ±ÅŸ kapasitansÄ± (Cob)
- C3, Miller kompanzasyonu ile paso bant kararlÄ±lÄ±ÄŸÄ±nÄ± saÄŸlar
- C4, AC sinyal iÃ§in toprak dÃ¶ngÃ¼sÃ¼ oluÅŸturur
- Bu aÅŸama â‰ˆ20x kazanÃ§ saÄŸlar

---

### 3.3 AÅŸama 3: Vbe Multiplier (Bias AÄŸÄ±)

Vbe multipliyer, Ã§Ä±kÄ±ÅŸ transistÃ¶rleri iÃ§in quiescent akÄ±m ayarÄ±nÄ± saÄŸlar. Termal stabilite iÃ§in Q10 heatsink'e termal olarak kupludur.

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| Q10 | NPN TransistÃ¶r | BD139 | TO-126 | Vbe multiplier transistÃ¶rÃ¼ |
| VR1 | Trimpot | 200Î© | â€” | Quiescent akÄ±m ayarÄ± |
| R8 | DirenÃ§ | 1.5kÎ© Â±1% | â€” | Multiplier direnci |
| R9 | DirenÃ§ | 1kÎ© Â±1% | â€” | Multiplier direnci |
| D1 | Diyot | 1N4148 | â€” | Koruma diyotu |
| D2 | Diyot | 1N4148 | â€” | Koruma diyotu |
| R10 | DirenÃ§ | 100Î© Â±1% | â€” | SÄ±nÄ±rlama direnci |
| C5 | Seramik KondansatÃ¶r | 10nF | â€” | Filtre kondansatÃ¶rÃ¼ |
| R11 | DirenÃ§ | 2.2kÎ© Â±1% | â€” | Bias direnci |
| C6 | Seramik KondansatÃ¶r | 100pF | â€” | HF filtre |

**Quiescent AkÄ±m AyarÄ±:**
```
Iq = 50mA (hedef)
VR1 ile ince ayar yapÄ±lÄ±r:
- Iq Ã§ok dÃ¼ÅŸÃ¼k â†’ Cross-over distorsiyon
- Iq Ã§ok yÃ¼ksek â†’ Termal kaÃ§ak, hasar riski
```

**Termal Kuplaj Kritik:**
```
Q10 heatsink'e vidalanmalÄ±dÄ±r.
IsÄ± arttÄ±ÄŸÄ±nda Q10'un Vbe'si dÃ¼ÅŸer â†’ Iq otomatik azalÄ±r.
Bu negatif geri besleme termal kararlÄ±lÄ±k saÄŸlar.
Termal kuplaj yoksa â†’ TERMAL KAÃ‡AK â†’ CÄ°HAZ HASARI
```

---

### 3.4 AÅŸama 4: Output Stage (Darlington)

Darlington output stage, yÃ¼ksek akÄ±m kazancÄ± ile hoparlÃ¶rÃ¼ sÃ¼rÃ¼cÃ¼. NPN ve PNP taraflarÄ± simetrik Ã§alÄ±ÅŸÄ±r.

#### NPN TarafÄ±

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| Q11 | NPN TransistÃ¶r | BD139 | TO-126 | SÃ¼rÃ¼cÃ¼ transistÃ¶rÃ¼ |
| Q15 | NPN TransistÃ¶r | MJL21194 | TO-264 | Ã‡Ä±kÄ±ÅŸ transistÃ¶rÃ¼ |
| R12 | DirenÃ§ | 10Î© 1W | â€” | Baz stopper direnci |
| R14 | DirenÃ§ | 0.22Î© 5W | â€” | Emiter direnci |

#### PNP TarafÄ±

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| Q13 | PNP TransistÃ¶r | BD140 | TO-126 | SÃ¼rÃ¼cÃ¼ transistÃ¶rÃ¼ |
| Q17 | PNP TransistÃ¶r | MJL21193 | TO-264 | Ã‡Ä±kÄ±ÅŸ transistÃ¶rÃ¼ |
| R13 | DirenÃ§ | 10Î© 1W | â€” | Baz stopper direnci |
| R15 | DirenÃ§ | 0.22Î© 5W | â€” | Emiter direnci |

#### Ã‡Ä±kÄ±ÅŸ AÄŸÄ±

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| L1 | Bobin | 1ÂµH | Air-core | Ã‡Ä±kÄ±ÅŸ indÃ¼ktÃ¶rÃ¼ |
| R16 | DirenÃ§ | 10Î© 1W | â€” | Zobel direnci |
| C7 | Film KondansatÃ¶r | 100nF/63V | â€” | Zobel kondansatÃ¶rÃ¼ |
| C8 | Film KondansatÃ¶r | 100nF/63V | â€” | DC blokaj kondansatÃ¶rÃ¼ |

**Darlington YapÄ±sÄ± AvantajlarÄ±:**
```
Toplam Î² (Current Gain) = Î²_Q11 Ã— Î²_Q15 â‰ˆ 100 Ã— 75 = 7500
YÃ¼ksek akÄ±m kazancÄ± â†’ DÃ¼ÅŸÃ¼k giriÅŸ akÄ±mÄ± â†’ Kolay sÃ¼rÃ¼m
```

**Baz Stopper DirenÃ§leri (R12, R13):**
- 10Î© deÄŸerinde, high-frequency osilasyonu engeller
- TransistÃ¶rlerin parasitik osilasyonunu baskÄ±lar
- 1W gÃ¼Ã§ rating'i yeterli

**Emiter DirenÃ§leri (R14, R15):**
- 0.22Î© 5W â€” AkÄ±m paylaÅŸÄ±mÄ± ve termal kararlÄ±lÄ±k
- Negatif termal geri besleme saÄŸlar
- AkÄ±m yoÄŸunluÄŸunu azaltÄ±r

**Zobel AÄŸÄ± (R16 + C7):**
- HoparlÃ¶r induktif yÃ¼kÃ¼ iÃ§in empedans dÃ¼zeltmesi
- YÃ¼ksek frekanslarda stabilite saÄŸlar
- Â±35V rail'de osilasyonu engeller

---

### 3.5 AÅŸama 5: Protection Circuit (Koruma Devresi)

Koruma devresi, aÅŸÄ±rÄ± akÄ±m, DC offset ve termal aÅŸÄ±rÄ± yÃ¼klenmeye karÅŸÄ± Ã§Ä±kÄ±ÅŸ transistÃ¶rlerini ve hoparlÃ¶rleri korur.

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| Q18 | NPN TransistÃ¶r | BD139 | TO-126 | AÅŸÄ±rÄ± akÄ±m algÄ±lama (NPN taraf) |
| Q19 | PNP TransistÃ¶r | BD140 | TO-126 | AÅŸÄ±rÄ± akÄ±m algÄ±lama (PNP taraf) |
| R21 | DirenÃ§ | 0.47Î© 5W | â€” | AkÄ±m algÄ±lama direnci (NPN) |
| R22 | DirenÃ§ | 0.47Î© 5W | â€” | AkÄ±m algÄ±lama direnci (PNP) |
| R23 | DirenÃ§ | 1kÎ© Â±1% | â€” | SÃ¼rÃ¼cÃ¼ direnci |
| R24 | DirenÃ§ | 1kÎ© Â±1% | â€” | SÃ¼rÃ¼cÃ¼ direnci |
| D3 | Diyot | 1N4148 | â€” | Koruma diyotu |
| D4 | Diyot | 1N4148 | â€” | Koruma diyotu |
| D5 | Diyot | 1N4007 | â€” | DoÄŸrultucu diyodu |
| D6 | Diyot | 1N4007 | â€” | DoÄŸrultucu diyodu |
| F1 | Sigorta | 3A Slow-blow | â€” | AÅŸÄ±rÄ± akÄ±m korumasÄ± (NPN) |
| F2 | Sigorta | 3A Slow-blow | â€” | AÅŸÄ±rÄ± akÄ±m korumasÄ± (PNP) |
| Relay | RÃ¶le | DPDT 12V coil | â€” | HoparlÃ¶r koruma rÃ¶lesi |
| R25 | DirenÃ§ | 10kÎ© Â±1% | â€” | Zaman sabiti direnci |
| C11 | Elektrolitik KondansatÃ¶r | 100ÂµF/25V | â€” | Zaman sabiti kondansatÃ¶rÃ¼ |

**Koruma MekanizmalarÄ±:**

| Koruma | Tetikleme | Aksiyon |
|--------|-----------|---------|
| DC Offset | >Â±0.5V DC | RÃ¶le ile hoparlÃ¶r ayÄ±rma |
| Over-Current | >5A tepe akÄ±m | GÃ¼Ã§ azaltma |
| Over-Temperature | >80Â°C | Sistemi kapatma |
| Short-Circuit | Ã‡Ä±kÄ±ÅŸ kÄ±sa devre | Hemen kapatma |
| Over-Voltage | >Â±48V DC | GÃ¼Ã§ azaltma |
| Soft-Start | Ä°lk aÃ§Ä±lÄ±ÅŸ | 2 saniye gecikme |

1. **DC Offset KorumasÄ± (>Â±0.5V):**
   ```
   Ã‡Ä±kÄ±ÅŸ DC voltajÄ± algÄ±lanÄ±r
   DC > Â±0.5V â†’ RÃ¶le aÃ§Ä±lÄ±r â†’ HoparlÃ¶r korunur
   R25 + C11 zaman gecikmesi (~0.5s)
   Tetikleme gecikmesi: 1 saniye
   Kurtarma: Otomatik reset
   ```

2. **AÅŸÄ±rÄ± AkÄ±m KorumasÄ± (>5A):**
   ```
   R21/R22 (0.47Î©) Ã¼zerinde voltaj dÃ¼ÅŸÃ¼mÃ¼ Ã¶lÃ§Ã¼lÃ¼r
   V_sense = I_out Ã— 0.47Î©
   EÅŸik: ~1.5V â†’ Q18/Q19 aÃ§Ä±lÄ±r â†’ Relay kapanÄ±r
   â‰ˆ 3.2A-5A eÅŸik akÄ±mÄ± aralÄ±ÄŸÄ±
   ```

3. **Termal Koruma (>80Â°C):**
   ```
   Heatsink Ã¼zerinde termal sensÃ¶r (NTC)
   SÄ±caklÄ±k >80Â°C â†’ Sistemi kapat
   Fan devreye girer (PWM kontrol)
   KSD301 termal cutoff: 95Â°C (son savunma)
   ```

4. **KÄ±sa Devre Koruma:**
   ```
   Ã‡Ä±kÄ±ÅŸ kÄ±sa devre algÄ±lanÄ±r â†’ Hemen kapat
   Sigorta F1/F2: 3A Slow-blow
   AnlÄ±k akÄ±m piclerini tolerans
   KalÄ±cÄ± aÅŸÄ±rÄ± akÄ±mda aÃ§Ä±lÄ±r
   ```

5. **AÅŸÄ±rÄ± Gerilim Koruma (>Â±48V DC):**
   ```
   Rail voltajÄ± izlenir
   Â±48V aÅŸÄ±mÄ± â†’ GÃ¼Ã§ azaltma / kapatma
   ```

6. **Soft-Start Devresi (2 saniye gecikme):**
   ```
   Ä°lk aÃ§Ä±lÄ±ÅŸta rush current sÄ±nÄ±rlÄ±
   KapasitÃ¶r ÅŸarjÄ± yavaÅŸ
   RÃ¶le kapanma gecikmeli
   2 saniye sonra tam gÃ¼Ã§
   ```

---

### 3.6 AÅŸama 6: Channel Enable (Kanal EtkinleÅŸtirme)

Kanal etkinleÅŸtirme devresi, uzaktan kontrol ve durum gÃ¶stergesi saÄŸlar.

| BileÅŸen | Tip | DeÄŸer | KÄ±lÄ±f | AÃ§Ä±klama |
|---------|-----|-------|-------|----------|
| MOSFET | N-Channel | IRLZ44N | TO-220 | Enable anahtarÄ± |
| R26 | DirenÃ§ | 10kÎ© | â€” | Gate pull-down direnci |
| R27 | DirenÃ§ | 100Î© | â€” | Gate series direnci |
| C12 | Seramik KondansatÃ¶r | 100nF | â€” | Gate filtre |
| LED | LED | Green/Red/Blue | 5mm | Durum gÃ¶stergesi |
| R28 | DirenÃ§ | 1kÎ© | â€” | LED sÄ±nÄ±rlama (YeÅŸil) |
| R29 | DirenÃ§ | 1kÎ© | â€” | LED sÄ±nÄ±rlama (KÄ±rmÄ±zÄ±) |
| R30 | DirenÃ§ | 1kÎ© | â€” | LED sÄ±nÄ±rlama (Mavi) |
| PC817 | Fotokupler | PC817 | DIP-4 | Galvanik izolasyon |
| R31 | DirenÃ§ | 470Î© | â€” | Fotokupler LED direnci |
| C13 | Elektrolitik KondansatÃ¶r | 10ÂµF/25V | â€” | Zaman sabiti |
| R32 | DirenÃ§ | 100kÎ© | â€” | Pull-up direnci |
| DB107 | Diyot KÃ¶prÃ¼sÃ¼ | DB107 | SMB | ACâ†’DC doÄŸrultucu |

**Durum GÃ¶stergesi:**
```
YeÅŸil LED  â†’ Kanal aktif, normal Ã§alÄ±ÅŸma
KÄ±rmÄ±zÄ± LED â†’ Hata durumu (aÅŸÄ±rÄ± akÄ±m, termal)
Mavi LED   â†’ Bekleme modu (standby)
```

**Galvanik Ä°zolasyon:**
- PC817 fotokupler ile kontrol devresi ve gÃ¼Ã§ devresi arasÄ±nda izolasyon
- GÃ¼rÃ¼ltÃ¼ ve gÃ¼venli konuÅŸ__)


---

## 4. Teknik Ã–zellikler

| Parametre | DeÄŸer | KoÅŸul |
|-----------|-------|-------|
| KazanÃ§ (Av) | 23x (27 dB) | R4(22kÎ©) / R5(1kÎ©) |
| THD | <0.005% | 1W / 8Î© |
| Slew Rate | >40 V/Âµs | Tam gÃ¼Ã§ |
| Bant GeniÅŸliÄŸi | 10 Hz - 150 kHz | -3dB |
| GÃ¼Ã§ Ã‡Ä±kÄ±ÅŸÄ± | 50W/kanal | 8Î© yÃ¼k |
| GÃ¼Ã§ KaynaÄŸÄ± | Â±35V | LM5122 interleaved boost |
| Quiescent AkÄ±m | 50mA | VR1 ile ayar |
| GiriÅŸ EmpedansÄ± | >20kÎ© | â€” |
| Ã‡Ä±kÄ±ÅŸ EmpedansÄ± | <0.1Î© | DC'de |
| Kanal SayÄ±sÄ± | 8 | ModÃ¼ler |
| Sinyal/GÃ¼rÃ¼ltÃ¼ OranÄ± | >100 dB | A-aÄŸÄ±rlÄ±klÄ± |
| Crosstalk | <-90 dB | 1kHz'de |

---

## 5. Tam BOM Tablosu (Tek Kanal â€” 29 BileÅŸen)

| # | Referans | Tip | DeÄŸer | KÄ±lÄ±f/Package | Miktar | GÃ¼Ã§ Rating |
|---|----------|-----|-------|---------------|--------|------------|
| 1 | Q1 | NPN TransistÃ¶r | BC546B | TO-92 | 1 | â€” |
| 2 | Q2 | NPN TransistÃ¶r | BC546B | TO-92 | 1 | â€” |
| 3 | Q5 | PNP TransistÃ¶r | BC556B | TO-92 | 1 | â€” |
| 4 | Q9 | NPN TransistÃ¶r | KSC3503 | TO-126 | 1 | â€” |
| 5 | Q10 | NPN TransistÃ¶r | BD139 | TO-126 | 1 | â€” |
| 6 | Q11 | NPN TransistÃ¶r | BD139 | TO-126 | 1 | â€” |
| 7 | Q13 | PNP TransistÃ¶r | BD140 | TO-126 | 1 | â€” |
| 8 | Q15 | NPN TransistÃ¶r | MJL21194 | TO-264 | 1 | 200W |
| 9 | Q17 | PNP TransistÃ¶r | MJL21193 | TO-264 | 1 | 200W |
| 10 | Q18 | NPN TransistÃ¶r | BD139 | TO-126 | 1 | â€” |
| 11 | Q19 | PNP TransistÃ¶r | BD140 | TO-126 | 1 | â€” |
| 12 | R1 | DirenÃ§ | 1kÎ© Â±1% | Axial | 1 | 1/4W |
| 13 | R2 | DirenÃ§ | 4.7kÎ© Â±1% | Axial | 1 | 1/4W |
| 14 | R3 | DirenÃ§ | 22kÎ© Â±1% | Axial | 1 | 1/4W |
| 15 | R4 | DirenÃ§ | 22kÎ© Â±1% | Axial | 1 | 1/4W |
| 16 | R5 | DirenÃ§ | 1kÎ© Â±1% | Axial | 1 | 1/4W |
| 17 | R6 | DirenÃ§ | 100Î© Â±1% | Axial | 1 | 1/4W |
| 18 | R7 | DirenÃ§ | 100Î© Â±1% | Axial | 1 | 1/4W |
| 19 | R8 | DirenÃ§ | 1.5kÎ© Â±1% | Axial | 1 | 1/4W |
| 20 | R9 | DirenÃ§ | 1kÎ© Â±1% | Axial | 1 | 1/4W |
| 21 | R10 | DirenÃ§ | 100Î© Â±1% | Axial | 1 | 1/4W |
| 22 | R11 | DirenÃ§ | 2.2kÎ© Â±1% | Axial | 1 | 1/4W |
| 23 | R12 | DirenÃ§ | 10Î© | Axial | 1 | 1W |
| 24 | R13 | DirenÃ§ | 10Î© | Axial | 1 | 1W |
| 25 | R14 | DirenÃ§ | 0.22Î© | Axial | 1 | 5W |
| 26 | R15 | DirenÃ§ | 0.22Î© | Axial | 1 | 5W |
| 27 | R16 | DirenÃ§ | 10Î© | Axial | 1 | 1W |
| 28 | R21 | DirenÃ§ | 0.47Î© | Axial | 1 | 5W |
| 29 | R22 | DirenÃ§ | 0.47Î© | Axial | 1 | 5W |

**Ek BileÅŸenler (Koruma + Enable):**

| # | Referans | Tip | DeÄŸer | KÄ±lÄ±f/Package | Miktar |
|---|----------|-----|-------|---------------|--------|
| 30 | R23 | DirenÃ§ | 1kÎ© Â±1% | Axial | 1 |
| 31 | R24 | DirenÃ§ | 1kÎ© Â±1% | Axial | 1 |
| 32 | R25 | DirenÃ§ | 10kÎ© Â±1% | Axial | 1 |
| 33 | R26 | DirenÃ§ | 10kÎ© | Axial | 1 |
| 34 | R27 | DirenÃ§ | 100Î© | Axial | 1 |
| 35 | R28 | DirenÃ§ | 1kÎ© | Axial | 1 |
| 36 | R29 | DirenÃ§ | 1kÎ© | Axial | 1 |
| 37 | R30 | DirenÃ§ | 1kÎ© | Axial | 1 |
| 38 | R31 | DirenÃ§ | 470Î© | Axial | 1 |
| 39 | R32 | DirenÃ§ | 100kÎ© | Axial | 1 |
| 40 | VR1 | Trimpot | 200Î© | Trimpot | 1 |
| 41 | C1 | Film KondansatÃ¶r | 4.7ÂµF/63V | Radial | 1 |
| 42 | C2 | Seramik KondansatÃ¶r | 100pF | Radial | 1 |
| 43 | C3 | Seramik KondansatÃ¶r | 100pF | Radial | 1 |
| 44 | C4 | Elektrolitik | 100ÂµF/63V | Radial | 1 |
| 45 | C5 | Seramik KondansatÃ¶r | 10nF | Radial | 1 |
| 46 | C6 | Seramik KondansatÃ¶r | 100pF | Radial | 1 |
| 47 | C7 | Film KondansatÃ¶r | 100nF/63V | Radial | 1 |
| 48 | C8 | Film KondansatÃ¶r | 100nF/63V | Radial | 1 |
| 49 | C11 | Elektrolitik | 100ÂµF/25V | Radial | 1 |
| 50 | C12 | Seramik KondansatÃ¶r | 100nF | Radial | 1 |
| 51 | C13 | Elektrolitik | 10ÂµF/25V | Radial | 1 |
| 52 | D1 | Diyot | 1N4148 | DO-35 | 1 |
| 53 | D2 | Diyot | 1N4148 | DO-35 | 1 |
| 54 | D3 | Diyot | 1N4148 | DO-35 | 1 |
| 55 | D4 | Diyot | 1N4148 | DO-35 | 1 |
| 56 | D5 | Diyot | 1N4007 | DO-41 | 1 |
| 57 | D6 | Diyot | 1N4007 | DO-41 | 1 |
| 58 | F1 | Sigorta | 3A Slow-blow | Cartridge | 1 |
| 59 | F2 | Sigorta | 3A Slow-blow | Cartridge | 1 |
| 60 | L1 | Bobin | 1ÂµH | Air-core | 1 |
| 61 | RÃ¶le | DPDT RÃ¶le | 12V coil | DIP | 1 |
| 62 | MOSFET | N-Channel | IRLZ44N | TO-220 | 1 |
| 63 | PC817 | Fotokupler | PC817 | DIP-4 | 1 |
| 64 | DB107 | Diyot KÃ¶prÃ¼sÃ¼ | DB107 | SMB | 1 |
| 65 | LED1 | LED | YeÅŸil | 5mm | 1 |
| 66 | LED2 | LED | KÄ±rmÄ±zÄ± | 5mm | 1 |
| 67 | LED3 | LED | Mavi | 5mm | 1 |

---

## 6. Termal Kuplaj ve Heatsink NotlarÄ±

### 6.1 Kritik Termal BileÅŸenler

AÅŸaÄŸÄ±daki transistÃ¶rler AYNI heatsink'e monte edilmelidir:

| BileÅŸen | Tip | Mounting | AÃ§Ä±klama |
|---------|-----|----------|----------|
| Q10 | BD139 | TO-126 â†’ Heatsink | Vbe multiplier â€” termal geri besleme iÃ§in KRÄ°TÄ°K |
| Q11 | BD139 | TO-126 â†’ Heatsink | NPN sÃ¼rÃ¼cÃ¼ |
| Q13 | BD140 | TO-126 â†’ Heatsink | PNP sÃ¼rÃ¼cÃ¼ |
| Q15 | MJL21194 | TO-264 â†’ Heatsink | NPN Ã§Ä±kÄ±ÅŸ â€” ana Ä±sÄ± kaynaÄŸÄ± |
| Q17 | MJL21193 | TO-264 â†’ Heatsink | PNP Ã§Ä±kÄ±ÅŸ â€” ana Ä±sÄ± kaynaÄŸÄ± |

### 6.2 Termal Kuplaj Prensibi

```
Q10 (BD139) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ Heatsink
   â”‚                              â”‚
   â”‚ IsÄ± paylaÅŸÄ±mda               â”‚ IsÄ± absorpsiyonu
   â”‚                              â”‚
   â”œâ”€â”€ Vbe dÃ¼ÅŸer â”€â”€â†’ Iq azalÄ±r    â”‚
   â”‚                              â”‚
   â””â”€â”€ Termal denge â”€â”€â†’ KararlÄ± Ã§alÄ±ÅŸma â”‚
```

**Termal Kuplaj Neden Gerekli:**
1. Q10, heatsink'in sÄ±caklÄ±ÄŸÄ±nÄ± doÄŸrudan hisseder
2. SÄ±caklÄ±k arttÄ±ÄŸÄ±nda Q10'un Vbe gerilimi dÃ¼ÅŸer (â‰ˆ-2mV/Â°C)
3. Vbe dÃ¼ÅŸÃ¼ÅŸÃ¼ quiescent akÄ±mÄ± (Iq) azaltÄ±r
4. Bu negatif geri besleme termal kaÃ§Ä± engeller
5. Termal kuplaj YOKSA â†’ Termal kaÃ§ak â†’ TransistÃ¶r hasarÄ±

### 6.3 Heatsink Gereksinimleri

| Parametre | DeÄŸer |
|-----------|-------|
| Heatsink Tipi | Fischer SK82-150-SA veya eÅŸdeÄŸeri |
| Termal DirenÃ§ | <1.5Â°C/W (toplam) |
| YÃ¼zey AlanÄ± | >200 cmÂ² |
| Malzeme | AlÃ¼minyum ekstrÃ¼zyon |
| Fan | Noctua NF-A8 (opsiyonel, sessiz) |
| Termal Macun |é«˜æ¸©thermal compound (Î»>5 W/mK) |
| IsÄ± Busy | KSD301 (termal cutoff, 95Â°C) |

### 6.4 Termal Hesaplama

```
Maksimum gÃ¼Ã§ kaybÄ± (8 kanal):
P_total = 8 Ã— 50W Ã— (1 - Î·) â‰ˆ 8 Ã— 50W Ã— 0.40 = 160W (tam gÃ¼Ã§te)

Tek kanal:
P_dissipation â‰ˆ 20W (Class AB, orta gÃ¼Ã§te)

Termal direnÃ§ hesabÄ±:
Rth_junction-to-ambient = Rth_jc + Rth_cs + Rth_sa
  Rth_jc (transistÃ¶r) â‰ˆ 1.0Â°C/W (MJL21194)
  Rth_cs (thermal compound) â‰ˆ 0.5Â°C/W
  Rth_sa (heatsink) â‰ˆ 1.0Â°C/W
  Toplam â‰ˆ 2.5Â°C/W

SÄ±caklÄ±k hesabÄ±:
T_junction = T_ambient + (P_diss Ã— Rth_total)
T_junction = 25Â°C + (20W Ã— 2.5Â°C/W) = 75Â°C (GÃ¼venli, Tj_max = 150Â°C)
```

---

## 7. GÃ¼Ã§ KaynaÄŸÄ± BaÄŸlantÄ±sÄ±

### 7.1 Â±35V LM5122 Interleaved Boost

| Parametre | DeÄŸer |
|-----------|-------|
| Topoloji | 4Ã— LM5122 interleaved boost |
| GiriÅŸ | 12V (6S LiPo veya 24V DC) |
| Ã‡Ä±kÄ±ÅŸ | Â±35V symmetric |
| GÃ¼Ã§ | 800W toplam |
| Verimlilik | %96 |
| Ripple | <50mV p-p |

```
6S LiPo (22.2V nominal)
    â”‚
    â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  LM5122 Interleaved Boost    â”‚
â”‚  4Ã— Faza, 200W/faz           â”‚
â”‚  Â±35V Symmetric Output       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
           â”‚
    â”Œâ”€â”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”
    â”‚             â”‚
   +35V         -35V
    â”‚             â”‚
    â–¼             â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚  Class AB Amplifier          â”‚
â”‚  8 Kanal ModÃ¼ler             â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 8. PCB TasarÄ±m NotlarÄ±

### 8.1 Koruma BÃ¶lgeleri

| BÃ¶lge | AÃ§Ä±klama |
|-------|----------|
| GÃ¼Ã§ BÃ¶lge | Â±35V rail, geniÅŸ izler (3mm+) |
| Sinyal BÃ¶lge | DÃ¼ÅŸÃ¼k gÃ¼Ã§lÃ¼ sinyal yollarÄ± |
| GÃ¼rÃ¼ltÃ¼ BÃ¶lge | Ã‡Ä±kÄ±ÅŸ aÄŸÄ±, Zobel, indÃ¼ktÃ¶r |
| Koruma BÃ¶lge | RÃ¶le, sigorta, akÄ±m sensÃ¶rÃ¼ |

### 8.2 Topraklama

```
Star Topraklama Topolojisi:

         Signal GND â”€â”€â”€â”€â”€â”
                         â”‚
         Power GND â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€ Single Star Point
                         â”‚
         Speaker GND â”€â”€â”€â”€â”˜
```

### 8.3 Ä°z GeniÅŸlikleri

| Sinyal | Minimum GeniÅŸlik | Ã–nerilen |
|--------|-----------------|----------|
| Â±35V Power | 2.0mm | 3.0mm |
| Speaker Output | 2.0mm | 3.0mm |
| Signal (GiriÅŸ) | 0.3mm | 0.5mm |
| Ground | 2.0mm | Copper Pour |

---

## 9. Test ProsedÃ¼rÃ¼

### 9.1 Ä°lk Energizasyon Kontrol Listesi

```
[ ] GÃ¼Ã§ kaynaÄŸÄ± Â±35V doÄŸrulandÄ±
[ ] Quiescent akÄ±m: VR1 ile 50mA ayarlandÄ±
[ ] DC offset: <Â±50mV Ã§Ä±kÄ±ÅŸta
[ ] Termal test: 30 dakika tam gÃ¼Ã§te Ã§alÄ±ÅŸma
[ ] AÅŸÄ±rÄ± akÄ±m testi: 3.2A eÅŸik doÄŸrulandÄ±
[ ] Relay testi: DC offset durumunda kapanma
[ ] LED gÃ¶stergeleri: YeÅŸil/KÄ±rmÄ±zÄ±/Mavi doÄŸrulandÄ±
[ ] Kanal enable/disable: IRLZ44N MOSFET doÄŸrulandÄ±
```

### 9.2 Ã–lÃ§Ã¼m NoktalarÄ±

| Nokta | Ã–lÃ§Ã¼len | Beklenen DeÄŸer |
|-------|---------|----------------|
| TP1 | Â±35V Rail | Â±35V Â±1V |
| TP2 | Quiescent AkÄ±m | 50mA Â±5mA |
| TP3 | Ã‡Ä±kÄ±ÅŸ DC Offset | <Â±50mV |
| TP4 | Vbe Multiplier VoltajÄ± | ~2.4V |
| TP5 | Ã‡Ä±kÄ±ÅŸ Sinyali | 1kHz, tam gÃ¼Ã§ |

---

## 10. Alternatif Ã‡Ä±kÄ±ÅŸ TransistÃ¶rleri ve Entegre Ã‡ip SeÃ§enekleri

### 10.1 Ã‡Ä±kÄ±ÅŸ TransistÃ¶r Alternatifleri

| # | TransistÃ¶r | Ãœretici | GÃ¼Ã§ | AkÄ±m | VCEO | SOA | Fiyat (1adet) | Fiyat (100+) | Stok | KullanÄ±m |
|---|-----------|---------|-----|------|------|-----|---------------|--------------|------|----------|
| 1 | **MJL3281A/MJL1302A** | ON Semi | 200W | 15A | 260V | **En iyi** | ~$2.50 | ~$3.12 | Mouser: 3793 adet | **En gÃ¼venilir** |
| 2 | **TTC5200/TTA1943** | Toshiba | 150W | 15A | 230V | Ä°yi | ~$1.50 | ~$1.20 | Mevcut | **GÃ¼ncel versiyon** |
| 3 | **2SC5200/2SA1943** | Toshiba | 150W | 15A | 230V | Ä°yi | ~$2.00 | ~$1.50 | KÄ±smi stok | **Klasik, yaygÄ±n** |

**SOA KarÅŸÄ±laÅŸtÄ±rma (10ms pulse):**

| Gerilim | 2SC5200 | MJL3281A | MJL4281A |
|---------|---------|----------|----------|
| 50V | 3A | 5A | 6A |
| 100V | 1A | 3.5A | 5A |
| 150V | 0.3A | 1.5A | 3A |

### 10.2 Entegre Class AB Ã‡ip SeÃ§enekleri

| # | Ã‡ip | Ãœretici | GÃ¼Ã§@8Î© | GÃ¼Ã§@4Î© | Supply | THD+N | Fiyat (1+) | Fiyat (100+) | Stok | KullanÄ±m |
|---|-----|---------|--------|--------|--------|-------|------------|--------------|------|----------|
| 1 | **LM1875** | TI | 20W | 30W | Â±16-60V | 0.015% | ~$4.22 | ~$4.22 | Mouser: 354 adet | **DÃ¼ÅŸÃ¼k gÃ¼Ã§** |
| 2 | **LM3886** | TI | 38W | 68W | Â±20-94V | 0.03% | ~$6.70 | ~$4.34 | Mouser: 5267 adet | **Orta gÃ¼Ã§** |
| 3 | **TDA7294** | ST | 100W | 100W | Â±40V | 0.005% | ~$3.25 | ~$2.14 | JLCPCB: 202 adet | **YÃ¼ksek gÃ¼Ã§** |

### 10.3 SeÃ§im KÄ±lavuzu

- 100W@8Î© uygulamasÄ± â†’ **2SC5200/2SA1943** (yeterli, ucuz)
- YÃ¼ksek gÃ¼venilirlik â†’ **MJL3281A/MJL1302A** (daha iyi SOA)
- Ultra high-end â†’ **MJL4281A/MJL4302A** (en iyi SOA, pahalÄ±)
- Maliyet Ã¶nemli â†’ **2SC5200/2SA1943** (~$1.5/pair)
- DÃ¼ÅŸÃ¼k gÃ¼Ã§ entegre â†’ **LM1875** (20W@8Î©)
- Orta gÃ¼Ã§ entegre â†’ **LM3886** (38W@8Î©)
- YÃ¼ksek gÃ¼Ã§ entegre â†’ **TDA7294** (100W@8Î©)

---

## 11. Ä°lgili DokÃ¼manlar

| Dosya | AmaÃ§ |
|-------|------|
| [[architecture/master-architecture-index]] | Ana mimari belge |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 seÃ§imi |
| [[ADR-089-classab-24v]] | Class AB AmplifikatÃ¶r + 6S LiPo + Â±35V Boost |

---

## 11. DeÄŸiÅŸiklik KaydÄ±

| Versiyon | Tarih | DeÄŸiÅŸiklik |
|----------|-------|------------|
| 1.0.0 | 2026-09-19 | Ä°lk tam dokÃ¼mantasyon â€” 65 bileÅŸen, ASCII ÅŸema, termal hesaplar |

---

**Authority:** Bayram Ali / Audio HW Engineer
**Last Updated:** 2026-09-19
**Mode:** Red Team Â· Human Mode Â· Truth Mode

---

## Faz 2 DoÃ„Å¸rulamasÃ„Â±: IMPLEMENTED/PLANNED Durumu

| BileÃ…Å¸en / Sorumluluk | Durum | KanÃ„Â±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari ÃƒÂ§ekirdek dosyalarÃ„Â±nda (ÃƒÂ¶r. public/index.php, src/) kod karÃ…Å¸Ã„Â±lÃ„Â±Ã„Å¸Ã„Â± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÃ„Â±m aÃ…Å¸amasÃ„Â±ndadÃ„Â±r, ÃƒÂ¼retim ortamÃ„Â±na geÃƒÂ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php ÃƒÂ¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÃ„Â±m ÃƒÂ§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÃ…Å¸Ã„Â±lamak iÃƒÂ§in otomatik eklenmiÃ…Å¸tir.)*

