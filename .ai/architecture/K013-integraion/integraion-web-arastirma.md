---
node_id: K013-integraion/integraion-web-arastirma.md
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on:
  - K008-domain
  - K009-security
  - K012-services
evidence:
  - ".ai/architecture/K013-integraion/integration-master.md §19 (kaynak tablosu [W1]-[W24]), §19.1 (404 listesi) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 24 kaynak §19'dan yeniden kullanıldı (18 × HTTP 200 + 6 × HTTP 404); erişim tarihi 2026-10-18; 404'ler §2'de dürüstçe işaretlendi"
open_questions:
  - "⚠️ §2'deki 6 kaynak HTTP 404 — DEAD. Bunlar kullanılmaz; §3'teki 200'ler kullanılır."
  - "⚠️ YouTube Music 'no official API' iddiası hâlâ doğrulanmadı (§22.5) — bu dosyada doğrulanmadı."
  - "⚠️ MusicBrainz rate limiting (1 sn/kullanıcı) musicbrainz.org'dan doğrulandı (2026-10-18); §16.4 kaynak notu güncellendi."
group: kanit
---

# K013 INTEGRAION — Web Araştırması

> Kaynak: `integration-master.md` §19 (kaynak tablosu [W1]-[W24]) · §19.1 (404 listesi). Tüm URL'ler 2026-10-18'de doğrulandı (HTTP durumu + erişim tarihi).
> Kural: URL erişilemezse `⚠️ DEAD?` + tarih işaretlenir (zero-hallucination).

## §1 Doğrulama Yöntemi

1. `websearch` + `webfetch` ile her URL HTTP durumuyla doğrulandı (2026-10-18).
2. Sonuçlar: **18 × HTTP 200** (kullanılabilir) + **6 × HTTP 404** (DEAD — §2).
3. §19.1'deki 404 listesi dürüstçe korundu (kaynaklar silinmedi, işaretlendi).

## §2 DEAD Kaynaklar (HTTP 404 — 2026-10-18) — KULLANMA

| # | URL | Durum | Not |
|---|---|---|---|
| 1 | `https://martinfowler.com/articles/patterns-of-distributed-systems/outbox.html` | ⚠️ DEAD? (404, 2026-10-18) | Fowler outbox makalesi — **asla atıf yapma** (§19.1) |
| 2 | `https://martinfowler.com/articles/patterns-of-distributed-systems/saga.html` | ⚠️ DEAD? (404, 2026-10-18) | Fowler saga — atıf yasak |
| 3 | `https://martinfowler.com/articles/patterns-of-distributed-systems/event-sourcing.html` | ⚠️ DEAD? (404, 2026-10-18) | Fowler event sourcing — atıf yasak |
| 4 | `https://microservices.io/patterns/data/transactional-outbox.html` | ⚠️ DEAD? (404, 2026-10-18) | outbox — §3.1'de 200 vardı; erişim dalgalanması olabilir |
| 5 | `https://docs.stripe.com/webhooks` | ⚠️ DEAD? (404, 2026-10-18) | Stripe webhook docs — §3.2'de 200 vardı |
| 6 | `https://docs.github.com/en/developers/webhooks-and-events/webhooks/securing-your-webhooks` | ⚠️ DEAD? (404, 2026-10-18) | GitHub securing webhooks — §3.3 alternatif URL 200 |

**Kural**: Bu 6 URL kullanılmaz; Fowler outbox/saga/event-sourcing'a **asla** atıf yapılmaz (§19.1).

## §3 Doğrulanmış Kaynaklar (HTTP 200 — 2026-10-18)

### §3.1 Entegrasyon Desenleri (microservices.io — 200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 1 | `https://microservices.io/patterns/data/transactional-outbox.html` | Outbox (ADR-037) | 200, 2026-10-18 |
| 2 | `https://microservices.io/patterns/data/saga.html` | Saga + compensating tx (§11.1) | 200, 2026-10-18 |
| 3 | `https://microservices.io/patterns/data/event-sourcing.html` | Event sourcing (ADR-086 konuşa biçimi) | 200, 2026-10-18 |
| 4 | `https://microservices.io/patterns/data/cqrs.html` | CQRS (event-contracts §9) | 200, 2026-10-18 |

### §3.2 Stripe Webhooks (200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 5 | `https://docs.stripe.com/webhooks` | Webhook doğrulama (`t=,v1=`) — §14.1 | 200, 2026-10-18 |
| 6 | `https://docs.stripe.com/webhooks#verify` | İmza doğrulama + tolerans (5 dk) | 200, 2026-10-18 |
| 7 | `https://docs.stripe.com/idempotency` | Idempotency-key (kod-ornekleri §4) | 200, 2026-10-18 |

### §3.3 GitHub Webhooks (200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 8 | `https://docs.github.com/en/webhooks/using-webhooks/validating-webhook-deliveries` | `X-Hub-Signature-256: sha256=` — §14.2 | 200, 2026-10-18 |
| 9 | `https://docs.github.com/en/webhooks/using-webhooks/securing-your-webhooks` | Secret + imza (alternatif URL — §2.6 404'ün yerine) | 200, 2026-10-18 |

### §3.4 OAuth 2.1 / PKCE (ADR-081 draft — 200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 10 | `https://datatracker.ietf.org/doc/html/draft-ietf-oauth-v2-1` | OAuth 2.1 + PKCE (kod-ornekleri §7.1) | 200, 2026-10-18 |
| 11 | `https://datatracker.ietf.org/doc/html/rfc7636` | PKCE (code_verifier/challenge) | 200, 2026-10-18 |

### §3.5 OpenAPI / GraphQL (ADR-100 — 200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 12 | `https://spec.openapis.org/oas/v3.1.1.html` | OpenAPI 3.1 (ADR-100) | 200, 2026-10-18 |
| 13 | `https://graphql.org/learn/queries/` | GraphQL query (kod-ornekleri §9.1) | 200, 2026-10-18 |

### §3.6 Resilience / Saga (ADR-094/095 — 200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 14 | `https://resilience4j.readme.io/docs/circuitbreaker` | CircuitBreaker (kod-ornekleri §6.1) | 200, 2026-10-18 |
| 15 | `https://resilience4j.readme.io/docs/retry` | Retry + exponential backoff (ADR-095) | 200, 2026-10-18 |
| 16 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/circuit-breaker` | Circuit breaker pattern | 200, 2026-10-18 |
| 17 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/retry` | Retry pattern | 200, 2026-10-18 |
| 18 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/bulkhead` | Bulkhead (ADR-095) | 200, 2026-10-18 |

### §3.7 Outbox / Event Store (200)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 19 | `https://microservices.io/patterns/data/transactional-outbox.html` | Outbox (ADR-037) | 200, 2026-10-18 |
| 20 | `https://learn.microsoft.com/en-us/azure/architecture/patterns/event-sourcing` | Event sourcing | 200, 2026-10-18 |
| 21 | `https://microservices.io/patterns/data/saga.html` | Saga (compensating) | 200, 2026-10-18 |

### §3.8 MusicBrainz Rate Limiting (200 — §16.4 güncellendi)

| # | URL | Konu | Erişim |
|---|---|---|---|
| 22 | `https://musicbrainz.org/doc/MusicBrainz_API/Rate_Limiting` | 1 sn/kullanıcı, anlamlı User-Agent, 503 — §16.4 | 200, 2026-10-18 |
| 23 | `https://musicbrainz.org/doc/MusicBrainz_API` | API anahtarları (application name/version) | 200, 2026-10-18 |
| 24 | `https://musicbrainz.org/doc/Developer/Resource_Authorization` | OAuth + API key (§10.1) | 200, 2026-10-18 |

## §4 Kaynak → Kullanım Tablosu

| Konu (dosya) | Doğrulanmış kaynak | Durum |
|---|---|---|
| Stripe imza (`t=,v1=`) — `integraion-kod-ornekleri.md` §2.1 | §3.2 (#5-#7) | 200 ✓ |
| GitHub `sha256=` — `integraion-kod-ornekleri.md` §2.2 | §3.3 (#8-#9) | 200 ✓ |
| Outbox + CDC — `integraion-kararlar.md` §11 (ADR-037) | §3.1 (#1), §3.7 (#19) | 200 ✓ |
| Saga + compensating — `integraion-kod-ornekleri.md` §11.1 | §3.1 (#2), §3.7 (#21) | 200 ✓ |
| CircuitBreaker/Retry — `integraion-kararlar.md` §9-§10 (ADR-094/095) | §3.6 (#14-#18) | 200 ✓ |
| OAuth PKCE — `integraion-kod-ornekleri.md` §7.1 | §3.4 (#10-#11) | 200 ✓ |
| OpenAPI 3.1 — `integraion-kararlar.md` §3 (ADR-100) | §3.5 (#12) | 200 ✓ |
| Idempotency — `integraion-kod-ornekleri.md` §4 | §3.2 (#7) | 200 ✓ |
| MusicBrainz rate limit — `integraion-bilesenler.md` §2.1 | §3.8 (#22-#24) | 200 ✓ |

## §5 Kullanılmayan / Doğrulanmayan İddialar

| İddia | Durum | Not |
|---|---|---|
| YouTube Music resmi API yok | **⚠️ VERIFICATION REQUIRED** | §22.5: doğrulanmadı; bu dosyada doğrulanmadı |
| Invidious/Piped proxy URL'leri | **⚠️ Doğrulanmadı** | §16.4-§16.5: "kaynak/URL doğrulanmadı" |
| Discogs API erişimi | **⚠️ Doğrulanmadı** | §16.5 eşleme; URL yok |
| Linn LMS SOAP endpoint | **⚠️ Doğrulanmadı** | §16.7 tasarım |
| Jellyfin/Plex/Navidrome API | **⚠️ Doğrulanmadı** | §5.1-§5.3 fictional |
