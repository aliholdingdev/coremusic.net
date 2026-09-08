---
title: "CoreMusic — .ai/architecture/03-contracts Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/03-contracts"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/03-contracts — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./ports/AGENTS.md]]

## 1. Amaç

API sözleşme merkezi: endpoint kataloğu, tasarım kuralları, hata kodları, olay sistemi, filtreleme, 40 günlük uygulama planı. ADR-084 (API Gateway) ve ADR-032 (IPC versioning) uygulama referansı.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| 36 kök dosya | api-architecture(-master), api-authentication, api-design-rules, api-endpoints, api-error-codes, api-event-system, api-filtering, ai-workflow-standards, 40-day-implementation-plan vb. |
| `ports/` | Port kayıt defteri (port-registry.md) |
| `protocols/` | Protokol kararları (protocol-decision.md) |
| `roles/` | Teknoloji rolleri (technology-roles.md) |

## 3. Agent Kuralları

### Zorunlu
1. Yeni endpoint → api-endpoints.md + api-error-codes.md senkron güncelleme
2. Sözleşme değişikliği → BffLayer/DTO kod tarafıyla aynı iş biriminde (shared/src/Api)

### Yasak
1. Endpoint'i koddan dokümante etmeden (gerçeklik sapması) — kod doğrulanmadan yazılan endpoint tanımı yasak
2. Versiyon kurallarını (ADR-032) ihlal eden sözleşme

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| ADR-084 | [[../../decisions/accepted/ADR-084-api-gateway-architecture.md]] |
| Kod | [[../../../shared/src/Api/]] (Bff, Middleware, Versioning) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
