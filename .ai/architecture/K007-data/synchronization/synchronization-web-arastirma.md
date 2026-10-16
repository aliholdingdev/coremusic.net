---
node_id: "K007-synchronization-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "microservices.io · debezium.io (2026-10-12 havuz §8)"
validation: "2026-10-15 — mevcut havuz derlendi"
open_questions:
  - "K-07 kararı → UNKNOWN"
group: kanit
---

# synchronization — Web Araştırması (K007)

| # | URL | Ne verdi | Tür | Durum |
|---|-----|----------|-----|-------|
| WY-1 | microservices.io/patterns/data/saga.html | saga (data yönetimi) | T1 | HTTP 200 · 2026-10-12 |
| WY-2 | microservices.io/patterns/transactions/saga.html | orchestration vs choreography | T1 | HTTP 200 |
| WY-3 | microservices.io/patterns/data/database-per-service.jpg/.html | servis-başına DB | T1 | monolit → uygulanmaz |
| WY-4 | microservices.io/patterns/data/api-composition.html | çok kaynak okuma | T1 | HTTP 200 |
| WY-5 | debezium.io/documentation/ | CDC akışı | T1 | HTTP 200 |
| WY-6 | martinfowler.com/microservices/ | decentralized data management | T1 | HTTP 200 |

**Not:** K-Space tek-deploy-unit monolit (ADR-097 K2) → database-per-service uygulanmaz;
outbox/CDC/saga **gelecek adayıdır** (K-07 · S-9).

---

**Authority:** Bayram Ali / Vault Steward