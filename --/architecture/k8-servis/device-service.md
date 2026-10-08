---
title: "device-service — Device Servisi (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-services
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# device-service — Device Servisi (stub)

**Durum:** `architecture/k8-servis/device-service.md` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Konu | Kaynak | Durum |
|------|--------|-------|
| Servis tanımı (BLE/WiFi/USB) | `.claude/CLAUDE.md` §10 (4. satır) | PLANNED |
| Kod | ⚠️ VERIFICATION REQUIRED — `device.coremusic.net` / servis dizini yok (glob 2026-10-07) |
| Wireless şeması | `.ai/.sql/mysql/coremusic_wireless.sql` | IMPLEMENTED (veri) |
| Device sözleşmeleri | `shared/src/Contracts/` · `shared/src/Device/` (dizin — 2026-10-07 find) | PARTIAL (altyapı) |
| Bluetooth/WiFi kararları | ADR-037 (wirelessconnect-integration) | DESIGN |

**Domain:** d06 → [[architecture/15-domain-d06-servis-api]] (K272 = PLANNED)
