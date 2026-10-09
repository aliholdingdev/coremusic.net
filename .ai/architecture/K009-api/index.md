---
type: architecture
category: layer
title: "K009 — API"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K009 — API

## §1 Kimlik
- Katman: K009 · Alan: **A2** (K8-K9).
- Kapsam: API Gateway/BFF — subdomain uçları, JSON birlik arayüzü, sözleşme yönetimi.

## §2 Sorumluluk
1. Gateway/BFF×4 (ADR-084): rate-limit, Origin/CSRF (ADR-094), public API güvenlik kuralları (ADR-020).
2. Stateless REST uçları; JSON birlik arayüzü; tutarlı hata modeli; cache başlıkları (plan §2-5, §4-8).
3. OpenAPI sözleşmesi üretimi (ADR-084 PLANNED; plan §6 Faz 5).
4. CQRS yalnızca okuma sorgusu düzeyinde; ayrı okuma DB'si/event-store KURULMAZ (plan §5.3).
5. Routing kenarı: clean URL/normalization/SPA router sözleşmesi (ADR-009/016/021).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K007 (middleware) → K006 → K010/K008 → K005 — plan §4 zinciri.
- **Üst (çağıran):** K011 UX — yalnız HTTP/API sözleşmesiyle; kod importu YASAK (plan §3 kuralı).

## §4 ADR Bağlantıları
| ADR | Başlık |
|---|---|
| ADR-084 | API Gateway Architecture (Gateway/BFF×4 IMPLEMENTED; CQRS+OpenAPI PLANNED) |
| ADR-020 / ADR-094 | API Public Security · Origin/CSRF pipeline |
| ADR-004 | Multi-Domain SPA Architecture |
| ADR-009 / ADR-016 / ADR-021 | Clean URL · URL Normalization · SPA Router Contract |

## §5 Durum
**IMPLEMENTED (Gateway/BFF)** — kanıt (2026-10-09): `api.coremusic.net/{index.php,composer.json,phpunit.xml}` + `auth.`/`home.`/`media.` composer.json. **PLANNED**: OpenAPI + CQRS-okuma sözleşmesi (ADR-084, plan §6 Faz 5).

## §6 Risk / Not
- "Gateway/BFF×4" sayısının tek kaynağı ADR-084'tür; kod içi sayaç doğrulanmadı → **UNKNOWN**.
- Uzak çağrılara (CDN/3. parti) timeout+retry+fallback zorunluluğu (plan §5.6) bu uçlarda denetlenmedi.
