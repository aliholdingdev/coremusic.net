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

### 2.1 Output Transistör Seçenekleri

| # | Transistör | Üretici | Güç | Akım | VCEO | SOA | Fiyat (1adet) | Fiyat (100+) | Stok | Kullanım |
|---|-----------|---------|-----|------|------|-----|---------------|--------------|------|----------|
| 1 | **MJL3281A/MJL1302A** | ON Semi | 200W | 15A | 260V | **En iyi** | ~$2.50 | ~$3.12 | ✅ Mouser: 3793 adet | **En güvenilir** |
| 2 | **TTC5200/TTA1943** | Toshiba | 150W | 15A | 230V | İyi | ~$1.50 | ~$1.20 | ✅ Mevcut | **Güncel versiyon** |
| 3 | **2SC5200/2SA1943** | Toshiba | 150W | 15A | 230V | İyi | ~$2.00 | ~$1.50 | ⚠️ Kısmi stok | **Klasik, yaygın** |

### 2.2 Entegre Class AB Çip Seçenekleri

| # | Çip | Üretici | Güç@8Ω | Güç@4Ω | Supply | THD+N | Fiyat (1+) | Fiyat (100+) | Stok | Kullanım |
|---|-----|---------|--------|--------|--------|-------|------------|--------------|------|----------|
| 1 | **LM1875** | TI | 20W | 30W | ±16-60V | 0.015% | ~$4.22 | ~$4.22 | ✅ Mouser: 354 adet | **Düşük güç** |
| 2 | **LM3886** | TI | 38W | 68W | ±20-94V | 0.03% | ~$6.70 | ~$4.34 | ✅ Mouser: 5267 adet | **Orta güç** |
| 3 | **TDA7294** | ST | 100W | 100W | ±40V | 0.005% | ~$3.25 | ~$2.14 | ✅ JLCPCB: 202 adet | **Yüksek güç** |

**Seçim Kılavuzu:**
- 100W@8Ω uygulaması → **2SC5200/2SA1943** (yeterli, ucuz)
- Yüksek güvenilirlik → **MJL3281A/MJL1302A** (daha iyi SOA)
- Ultra high-end → **MJL4281A/MJL4302A** (en iyi SOA, pahalı)
- Maliyet önemli → **2SC5200/2SA1943** (~$1.5/pair)

**SOA Karşılaştırma (10ms pulse):**
| Gerilim | 2SC5200 | MJL3281A | MJL4281A |
|---------|---------|----------|----------|
| 50V | 3A | 5A | 6A |
| 100V | 1A | 3.5A | 5A |
| 150V | 0.3A | 1.5A | 3A |

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
