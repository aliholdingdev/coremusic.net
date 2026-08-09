---
type: electronic
category: amplifier-class-ab
title: "CoreMusic — Class AB Amplifier Topology"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Class AB Amplifier

**See also:** [[electronic/amplifier/index]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

Class AB Amplifier, CoreMusic ELECTRONICS platformunun profesyonel ses amfilerinin temel topolojisini tanımlar. 100W @ 8Ω, THD+N <0.01%.

---

## 2. Temel Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class AB push-pull |
| Çıkış Gücü | 100W @ 8Ω, 200W @ 4Ω |
| THD+N | <0.01% (1kHz, 1W) |
| SNR | >100dB (A-wtd) |
| Frekans Yanıtı | 20Hz - 20kHz (±0.5dB) |
| Damping Factor | >200 (8Ω) |
| Giriş Empedansı | 47kΩ |
| Çıkış Empedansı | <0.1Ω |

---

## 3. Devre Topolojisi

```
Input → Differential Pair (VAS) → Driver Stage → Output Stage → Feedback
                                    │
                              Bias Adjustment
                                    │
                              Thermal Tracking
```

### Aşamalar

| Aşama | Görev | Bileşenler |
|-------|-------|-----------|
| Input | Sinyal kabulü | Differential pair (BJT) |
| VAS | Gerilim kazancı | Tek端emitter follower |
| Driver | Güç artırma | Darlington pair |
| Output | Güç çıkışi | Complementary BJTs |
| Feedback | Bozulma azaltma | Negatif geri besleme |

---

## 4. Bias Ayarı

| Parametre | Değer |
|-----------|-------|
| Bias Current | 50-100mA (quiescent) |
| Thermal Tracking | NTC termistör ile |
| Crossover Distortion | Minimize edilmiş |
| Class B→AB geçiş | Smooth transition |

---

## 5. DC Offset Koruması

| Parametre | Değer |
|-----------|-------|
| DC Offset Limit | ±0.5V |
| Koruma | Röle ile hoparlör ayırma |
| Tetikleme | 1 saniye gecikme |
| Kurtarma | Otomatik reset |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A, XMOS |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
