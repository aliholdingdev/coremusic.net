---
type: electronic
category: power-supply
title: "CoreMusic — Amplifier Power Supply Design"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Power Supply Design

**See also:** [[electronic/amplifier/index]] · [[electronic/hardware/index]]

---

## 1. Amaç

Power Supply Design, CoreMusic amfi güç besleme tasarımını tanımlar.

---

## 2. Güç Besleme Topolojisi

```
AC Mains → Transformer → Bridge Rectifier → Smoothing → Regulator → Output
                                              │
                                         Ripple Filter
```

---

## 3. Güç Kaynağı Parametreleri

| Parametre | Değer |
|-----------|-------|
| AC Giriş | 115V/230V (±10%) |
| DC Çıkış | ±42V DC |
| Akım | 5A (tepe) |
| Ripple | <5mV RMS |
| Kapasitör | 10,000µF per rail |
| Regülasyon | <%1 |

---

## 4. Toroidal Transformer

| Parametre | Değer |
|-----------|-------|
| Tip | Toroidal |
| Güç | 500VA (8+1 kanal için) |
| Çıkış | ±42V AC |
| Manyetik Yayılım | Düşük |
| Verimlilik | >95% |

---

## 5. Güç Filtresi

| Aşama | Kapasitör | Amaç |
|-------|-----------|------|
| Smoothing | 10,000µF | Ripple azaltma |
| Decoupling | 100nF | Yüksek frekans temizliği |
| Bypass | 10µF | Orta frekans temizliği |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
