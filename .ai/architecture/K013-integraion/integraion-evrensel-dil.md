---
node_id: K013-integraion/integraion-evrensel-dil.md
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on:
  - K007-data
  - K008-domain
  - K009-security
  - K012-services
evidence:
  - ".ai/architecture/K013-integraion/integration-master.md §1 (bileşen adları), §14 (webhook), §16 (API-Adapter), §22-§23 — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/event-contracts.md 16 domain event'i (ADR-084 adlandırma) — okundu 2026-10-18"
  - ".ai/architecture/K012-services/services-evrensel-dil.md §2-§3 (K012 sözlüğü — sınır) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 28 terim EN/TR + bağlam; kod örnekleri: integraion-kod-ornekleri.md §2-§15"
open_questions:
  - "⚠️ 'IntegrationHub' fictional (tasarım + ADR; uygulama YOK)."
  - "⚠️ 'WebhookMotoru' fictional (motor YOK)."
  - "⚠️ 'API-Adapter' fictional (kod YOK; §16.4-§16.5 kaynak doğrulanmadı)."
  - "⚠️ Event-contracts 16 olay konuşa biçimi belirsiz (Q11)."
  - "⚠️ 'YouTube Music' — resmi API iddiası doğrulanmadı (§22.5)."
  - "⚠️ 'sync' sahipliği çelişkisi (K013 vs K015) — KARAR YOK."
group: mimari
---

# K013 INTEGRAION — Evrensel Dil (Sözlük)

> **Kaynak**: `integration-master.md` §1/§14/§16 · `event-contracts.md` (16 olay) · `services-evrensel-dil.md` (K012 sınırı).
> **Dizin adı `integraion` yazım hatası — donduruldu** (dizin/dosya adları değiştirilemez). Bu sözlükte EN terimler kod/ADR adlarıdır; TR arayüz metnidir.

## §1 Kullanım Kuralları

1. Kodda EN (`CircuitBreaker`, `WebhookSignature`), arayüzde TR (entegrasyon, devre kesici) kullanılır.
2. Yeni terim: `integration-master.md` §1 + bu dosya birlikte güncellenir.
3. `integration-master.md` İngilizce master — onaylı istisnadır.

## §2 Entegrasyon Temel Terimleri (14)

| # | EN Terim | TR Karşılık | Bağlam |
|---|---|---|---|
| 1 | Integration | Entegrasyon | K013-integraion (dizin adı — yazım hatası donduruldu) |
| 2 | Integration Hub | Entegrasyon Merkezi | §7 — `⚠️ Fictional (tasarım + ADR; uygulama YOK)` |
| 3 | Integration Point | Entegrasyon Noktası | 11 nokta (katalog §4) |
| 4 | Integration Health | Entegrasyon Sağlığı | §8 — `⚠️ Fictional` |
| 5 | Integration Telemetri | Entegrasyon Telemetrisi | §9 — `⚠️ Fictional` |
| 6 | Integration Data | Entegrasyon Verisi | §1.6 — veri modeli K007-data |
| 7 | API Adapter | API Uyarlayıcı | §16 — `⚠️ Fictional (kod YOK)` |
| 8 | Webhook | Web Kancası | §14 — `⚠️ Motor YOK` |
| 9 | Webhook Signature | Webhook İmzası | §14.1 (Stripe `t=,v1=`) / §14.2 (GitHub `sha256=`) |
| 10 | Webhook Retry | Webhook Yeniden Deneme | §14.6 — `⚠️ Motor YOK` |
| 11 | Idempotency | Teyekullanılamazlık | Stripe docs (web-arastirma §3.2) — `⚠️ Kod YOK` |
| 12 | Outbox | Çıkış Kutusu | ADR-037 — `⚠️ Kod YOK` |
| 13 | Saga | Saga (Dağıtık İşlem) | microservices.io (web-arastirma §3.1) — `⚠️ Kod YOK` |
| 14 | Compensation | Tazmin (Geri Alma) | saga compensating tx — `⚠️ Kod YOK` |

## §3 Dayanıklılık / Olay Terimleri (14)

| # | EN Terim | TR Karşılık | Bağlam |
|---|---|---|---|
| 15 | Circuit Breaker | Devre Kesici | ADR-094 (Resilience4j) — `⚠️ Konuşa biçimi; kod YOK` |
| 16 | Bulkhead | Bölme (İzolasyon Havuzu) | ADR-095 — `⚠️ Kod YOK` |
| 17 | Timeout | Zaman Aşımı | ADR-095; §16 adapter'lar |
| 18 | Retry | Yeniden Deneme | ADR-095 (exponential backoff + jitter) — `⚠️ Kod YOK` |
| 19 | Dead Letter | Ölü Mektup Kuyruğu | §4.4 (retry 5× sonrası) — `⚠️ Kod YOK` |
| 20 | Domain Event | Alan Olayı | event-contracts.md (16 olay) — ADR-084 (`entity.action.pastTense`) |
| 21 | Event Bus / Transport | Olay Veriyolu / Taşıma | ADR-101 (Redis Streams) — `⚠️ Kod YOK` |
| 22 | Event Store | Olay Deposu | ADR-086 konuşa biçimi — `⚠️ Belirsiz` |
| 23 | CDC (Change Data Capture) | Değişiklik Yakalama | ADR-037 (Debezium) — `⚠️ Kod YOK` |
| 24 | Idempotent Consumer | Teyekullanılamaz Tüketici | §4 — `⚠️ Kod YOK` |
| 25 | Rate Limit | Hız Sınırı | MusicBrainz 1 sn/kullanıcı (web-arastirma §3.8) |
| 26 | Fallback | Geri Düşme (Yedek) | domain fallback (K008 cache) — R-INT-01 |
| 27 | Proxy | Vekil Sunucu | Invidious/Piped (§10.2) — `⚠️ Doğrulanmadı` |
| 28 | PKCE | PKCE (Kod Değiştirme) | ADR-081 draft — OAuth 2.1 |

## §4 Bileşen Adları (fictional — §4 kapsam)

MusicBrainz · Jellyfin · Plex · Navidrome · YouTube Music (`⚠️ API doğrulanmadı`) · Pocketnet · Invidious · Piped · ReadYou · Linn · Stripe · API-Adapter · IntegrationHub · WebhookMotoru.

## §5 Eşanlamlı / Kaçınılacak Terimler

| Yanlış / Eski | Doğru | Not |
|---|---|---|
| `integration-*.md` (dosya adı) | `integraion-*.md` | Dosya adları `integraion-` önekli; **yalnız istisna**: `integration-master.md` |
| K019-testing / K018-devops / K017-observability | K18-testing / K17-devops / K016-observability | Eski adlar — yeni atıf yasak |
| K0100-identitiy | K009-security | Eski ad — yeni atıf yasak (⚠️ master §5.1'de duruyor) |
| Fowler outbox/saga/event-sourcing | microservices.io (web-arastirma §3.1) | Fowler 404 — **asla atıf yapma** (§19.1) |
| integration-point-catalog / event-contracts / adr-integration | — | Mevcut dosya adları (değiştirilemez) |

## §7 Sık Yapılan Hatalar

| Hata | Doğru | Neden |
|---|---|---|
| "integration-kapsam.md" yazmak | "integraion-kapsam.md" | Dizin/dosya adları donduruldu; tek istisna integration-master.md |
| CircuitBreaker'i "çalışıyor" sanmak | "konuşa biçimi var; kod YOK" | §22.8 — ADR-094 yalnız desen |
| Fowler outbox'a atıf yapmak | microservices.io (§3.1) | Fowler URL'leri 404 (§19.1) — asla atıf |
| sync'ı K013'ün sanmak | "sahiplik karar YOK (K013 vs K015)" | §22.4 — çelişki çözülmedi |
| WebhookMotoru'nu mevcut sanmak | "tasarım — motor YOK" | §22.9 |

## §8 Terim → Dosya Eşlemesi

| Terim (EN) | Birincil dosya | Konum |
|---|---|---|
| Outbox / Saga / Compensation | integraion-kararlar.md | §11 / §2 (ADR-037/086) |
| Circuit Breaker / Bulkhead | integraion-kararlar.md | §9-§10 (ADR-094/095) |
| Webhook Signature / Retry | integraion-kod-ornekleri.md | §2 / §14 |
| Idempotency / Outbox writer | integraion-kod-ornekleri.md | §4 / §3 |
| PKCE / API Adapter | integraion-kod-ornekleri.md | §7 / §8-§9, §15 |
| Event (16 olay) | event-contracts.md (mevcut) | ADR-084 adlandırma |

## §9 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K013-integraion/integration-master.md` | İngilizce master (onaylı istisna) | integration-master |
| `.ai/architecture/K013-integraion/event-contracts.md` | 16 domain event'i | event-contracts |
| `.ai/architecture/K013-integraion/adr-integration.md` | 17 entegrasyon kararı | adr-integration |
| `.ai/architecture/K012-services/services-evrensel-dil.md` | K012 sözlüğü (sınır) | services-evrensel-dil |
