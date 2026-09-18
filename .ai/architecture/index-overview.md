---
type: index
category: overview
title: "Mimari Genel Bakış"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Mimari Genel Bakış

## 21 Katmanlı Sistem (K0-K20)

```
┌─────────────────────────────────────────────────────────────────────┐
│                    COREMUSIC 21 KATMANLI MİMARİ                      │
│                    1020+ BİLEŞEN                                     │
│                                                                     │
│  YAZILIM KATMANLARI (K0-K15)          ELEKTRONİK (K16-K20)         │
│  ┌───────────────────────────┐        ┌───────────────────────┐    │
│  │ K0-K5: Altyapı (222)     │◄──────►│ K16-K20: Elektronik   │    │
│  │ K6-K7: Güvenlik (63)     │        │ (340 bileşen)         │    │
│  │ K8-K9: Servis (74)       │        └───────────────────────┘    │
│  │ K10-K15: Uygulama (193)  │                                     │
│  └───────────────────────────┘        TOPLAM: 1020+ BİLEŞEN       │
└─────────────────────────────────────────────────────────────────────┘
```

## Grup İndeksleri

| # | İndeks | Kapsam | Dosya |
|---|--------|--------|-------|
| 1 | [[electronics/index]] | K16-K20 | Elektronik |
| 2 | [[k0-k5-software/index]] | K0-K5 | Yazılım Altyapı |
| 3 | [[k6-k7-security/index]] | K6-K7 | Güvenlik |
| 4 | [[k8-k9-services/index]] | K8-K9 | Servis & API |
| 5 | [[k10-k15-application/index]] | K10-K15 | Uygulama |
| 6 | [[firmware/index]] | Firmware | Firmware |
| 7 | [[master-architecture-index]] | Tümü | Master İndeks |

## Hızlı Erişim

| İhtiyaç | Dosya |
|---------|-------|
| Class AB devresi | [[electronics/amplifier-classab-circuit]] |
| Güç kaynağı | [[electronics/power-supply-classab]] |
| Auth sistemi | [[k6-k7-security/k06-auth-layer]] |
| 18 BCNF DB | [[k0-k5-software/k05-data-detail]] |
| DSP pipeline | [[k0-k5-software/k3-audio-engine]] |
| API Gateway | [[k8-k9-services/k9-api-routing]] |
| ADR-089 | [[ADR-089-classab-24v]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
