---
title: "l2-routing/service-discovery — Eski Service Discovery Dokümanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-routing
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# service-discovery — Service Discovery (stub)

**Durum:** `architecture/l2-routing/service-discovery(.md)` **diskte YOK** (eski ağaç silindi).

## Bugünkü Karşılığı (gerçek kanıt)

| Bileşen | Dosya | Durum |
|---------|-------|-------|
| Service registry (tanım + sağlık) | `shared/src/Api/Registry/{ServiceRegistry,ServiceDefinition,ServiceHealth}.php` | IMPLEMENTED |
| API route tablosu | `shared/src/Api/Routing/RouteTable.php` | IMPLEMENTED |
| BFF seçimi (istemci tipine göre) | `shared/src/Api/Bff/BffLayer.php` (+4 BFF) | IMPLEMENTED |
| mDNS / ağ service discovery | — | ⚠️ VERIFICATION REQUIRED (kod yok) |

**Domain tablosu:** [[architecture/15-domain-d06-servis-api]] (K259) · ağ tarafı d09 (K425 = PLANNED)
