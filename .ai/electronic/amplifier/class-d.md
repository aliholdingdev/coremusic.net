---
type: electronic
category: amplifier-class-d
title: "CoreMusic — Class D Amplifier Topology"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Class D Amplifier

**See also:** [[electronic/amplifier/index]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

Class D Amplifier, CoreMusic ELECTRONICS platformunun yüksek verimli, düşük güç tüketimli amfi topolojisini tanımlar.

---

## 2. Temel Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class D (PWM) |
| Verimlilik | >90% |
| Çıkış Gücü | 70W–185W @ 8Ω (çipe bağlı) |
| THD+N | <0.01% (1kHz, 1W) |
| SNR | >100dB (A-wtd) |
| Frekans Yanıtı | 20Hz - 20kHz (±0.5dB) |
| Damping Factor | >200 (8Ω) |

### 2.1 Class D Amplifikatör Çip Seçenekleri

| # | Çip (TI) | Güç@8Ω | Güç@4Ω | THD+N | SNR | Supply | Fiyat (1ku) | Fiyat (1-99) | Stok | Kullanım |
|---|----------|--------|--------|-------|-----|--------|-------------|--------------|------|----------|
| 1 | **TPA3255** | 185W | 315W | 0.006% | 112dB | 18-53.5V | ~$4.13 | ~$8.22 | ⚠️ Stok dışı olabilir | **Profesyonel** |
| 2 | **TPA3251** | 150W | 175W | 0.005% | 111dB | 12-38V | ~$3.35 | ~$7.41 | ✅ Mevcut | **Orta güç, en sessiz** |
| 3 | **TPA3250** | 70W | 130W | 0.005% | 110dB | 12-36V | ~$2.35 | ~$5.47 | ✅ Mevcut | **Bütçe, en ucuz** |
| 4 | **TPA3116D2** | — | 50W | 0.1% | >100dB | 4.5-26V | ~$0.70 | ~$1.50 | ✅ Mevcut | **35-50W ev** |
| 5 | **TPA3118D2** | 30W | 60W | 0.1% | >100dB | 4.5-26V | ~$0.60 | ~$1.20 | ✅ Mevcut | **15-30W ev** |
| 6 | **TPA3130D2** | 15W | 30W | 0.1% | >100dB | 4.5-26V | ~$0.50 | ~$1.00 | ✅ Mevcut | **5-15W masaüstü** |

### 2.2 Düşük Güç Class D Detayları

| Çip | Güç@8Ω | Güç@4Ω | Supply | Rds(on) | Fiyat | Özellik |
|-----|--------|--------|--------|---------|-------|---------|
| TPA3130D2 | 15W | 30W | 4.5-26V | 0.12Ω | ~$0.50 | Isıatsız çalışabilir (2 katman PCB) |
| TPA3118D2 | 30W | 60W | 4.5-26V | 0.12Ω | ~$0.60 | Isıatsız çalışabilir (2 katman PCB) |
| TPA3116D2 | — | 50W | 4.5-26V | 0.12Ω | ~$0.70 | Küçük heatsink gerekli |

**Ortak Özellikler (TPA31xx serisi):**
- Same footprint (aynı PCB)
- >90% verimlilik
- AM avoidance
- Master/Slave senkronizasyon
- Self-protection (UV, OT, short)
- Click-and-pop free

**Özellikler:**
- Closed-loop (PurePath Ultra-HD)
- Differential analog input
- 2 VRMS input (PCM5242 uyumlu)
- Click-and-pop free startup
- Self-protection (UV, OT, clip, short)
- 90% verimlilik (4Ω)
- Idle losses <2.5W (TPA3255), <1W (TPA3251/3250)

**Seçim Kılavuzu:**
- 100W+ güç gerekli → **TPA3255**
- 36V PSU ile çalışma → **TPA3251** (en sessiz)
- Düşük bütçe → **TPA3250**
- Battery operation → **TPA3250** (en düşük idle loss)

---

## 3. PWM Sinyal İşleme

```
Analog Input → Sigma-Delta Modulator → PWM → Output Filter → Hoparlör
                              │
                         Self-Oscillating
                         or External Clock
```

---

## 4. Çıkış Filtresi

| Parametre | Değer |
|-----------|-------|
| Filtre Tipi | 2. basamak LC low-pass |
| Kesme Frekansı | 50kHz |
| Indüktör | 10µH, high-current |
| Kapasitör | 0.47µF, film |

---

## 5. Class AB vs Class D

| Kriter | Class AB | Class D |
|--------|----------|---------|
| Verimlilik | ~50% | >90% |
| Isı | Yüksek | Düşük |
| Boyut | Büyük | Küçük |
| Ses Kalitesi | Mükemmel | Çok iyi |
| Maliyet | Yüksek | Orta |
| Kullanım | Stüdyo | Taşınabilir |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
