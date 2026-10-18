---
node_id: K013-integraion/integraion-test-stratejisi.md
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on:
  - K007-data
  - K008-domain
  - K009-security
  - K010-network
  - K012-services
  - K18-testing
evidence:
  - ".ai/architecture/K013-integraion/integration-master.md §14 (webhook doğrulama), §22.7-§22.9 (uygulama/kod/motor YOK) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-kod-ornekleri.md §2-§15 (15 blok — test edilecek söylemler) — okundu 2026-10-18"
  - ".ai/architecture/K012-services/services-test-stratejisi.md §2-§6 (piramit — K012 sınırları) — okundu 2026-10-18"
validation:
  - "2026-10-18 — Piramit + 7 senaryo + kabul kriterleri; fictionel K013 durumu her senaryoda korundu"
open_questions:
  - "⚠️ K013 uygulamaları fictional (§22.7-§22.9) — entegrasyon testleri (pact/e2e) uygulanamaz; testler konuşa biçimine (kod-ornekleri) yöneliktir."
  - "⚠️ MusicBrainz/Jellyfin/Plex/YouTube Music canlı API testleri — kaynak doğrulanmadı (§16.4-§16.5)."
  - "⚠️ WebhookMotoru kodu YOK — §5.3 senaryosu motor uygulandığında geçerli."
  - "⚠️ Test sahipliği: K012 §7.3 (QA K18) entegrasyon için de geçerli — KARAR YOK."
group: kod
---

# K013 INTEGRAION — Test Stratejisi

> Kaynak: `integration-master.md` §14/§22.7-§22.9 · `integraion-kod-ornekleri.md` (söylemler) · `services-test-stratejisi.md` (K012 piramit paraleli).
> **Önemli**: K013 uygulamaları fictional — entegrasyon testleri (contract/e2e) ancak uygulama varsa anlamlıdır. Bu strateji **uygulama geldiğinde** uygulanır.

## §1 Test Piramidi (K013)

```text
                    ╱ E2E ╲                     Canlı API + webhook (K013 §14)
                  ╱─────────╲                   — UYGULAMA YOK (fictional)
              ╱ Integration (contract) ╲        API-Adapter/pact — UYGULAMA YOK
            ╱─────────────────────────╲
          ╱ Unit (imza/idempotency/retry) ╲     Kod söylemleri (kod-ornekleri) — ÇALIŞTIRILABİLİR
        ╱─────────────────────────────────╲
```

- **Unit**: imza doğrulama (§2.1-§2.3), idempotency (§4), retry/jitter (§5), normalize (§15) — söylem blokları test edilebilir (bağımsız fonksiyonlar).
- **Integration**: API-Adapter (§16) + outbox (§3) — uygulama geldiğinde.
- **E2E**: webhook → kuyruk → domain → event (§14.6) — uygulama + canlı API doğrulaması gerektirir.

## §2 Test Senaryoları (7)

### §2.1 Stripe Webhook İmza Doğrulama (§14.1 — kod-ornekleri §2.1)

- **Senaryo**: Doğru imza → kabul; yanlış imza → 401; zaman aşımı (>5 dk) → reddet.
- **Beklenen**: `hash_equals` zaman sabitli; `t=,v1=` ayrıştırma; tolerans 300 sn.
- **Kabul kriteri**: ✅ Doğru imza `ok=true` · ✅ Yanlış `ok=false` + sebep · ✅ >5 dk `ok=false` (zaman aşımı).

### §2.2 GitHub X-Hub-Signature-256 Doğrulama (§14.2 — kod-ornekleri §2.2)

- **Senaryo**: `sha256=<hex>` header'ı doğru/yanlış; prefix yoksa reddet.
- **Kabul kriteri**: ✅ `sha256=` yoksa `false` · ✅ Doğru hex → `true` · ✅ Farklı uzunluk → `false` (timingSafeEqual).

### §2.3 Outbox Atomikliği (ADR-037 — kod-ornekleri §3.1)

- **Senaryo**: Domain change + outbox aynı transaction; transaction başarısızsa outbox da yok.
- **Kabul kriteri**: ✅ Commit sonrası outbox satırı var · ✅ Rollback sonrası outbox satırı YOK · ✅ `processed_at` CDC işleyene kadar `null`.

### §2.4 Idempotency — Duplikasyon Koruması (kod-ornekleri §4)

- **Senaryo**: Aynı `event_id` 2× işlenir; ikincisi duplikasyon yaratmaz.
- **Kabul kriteri**: ✅ İlk işleme yapılır · ✅ İkincisi `ON CONFLICT DO NOTHING` ile atlanır · ✅ `(event_id, handler)` UNIQUE kısıtı DB'de var.

### §2.5 Retry + Jitter (ADR-095 — kod-ornekleri §5)

- **Senaryo**: İlk 3 deneme başarısız, 4. başarılı → toplam 4 çağrı; üst sınır aşılırsa hata.
- **Kabul kriteri**: ✅ Base 200/400/800 ms (exponential) · ✅ Jitter eklenir (0-100 ms) · ✅ `attempt >= max` → `Err`.

### §2.6 Circuit Breaker — Hasta Entegrasyon (ADR-094 — kod-ornekleri §6)

- **Senaryo**: MusicBrainz down (%50 başarısızlık) → CB open (30 sn) → domain fallback (K008 cache) devrede.
- **Kabul kriteri**: ✅ `failureRateThreshold=50` sonrası open · ✅ Open'da `CallNotPermittedException` → fallback · ✅ 30 sn sonra half-open (3 izinli çağrı).

### §2.7 Webhook Retry Kuyruğu (§14.6 — kod-ornekleri §14)

- **Senaryo**: Teslim 500 → exponential yeniden deneme (1,2,4,8,16 dk); 5. sonrası dead-letter.
- **Kabul kriteri**: ✅ 5xx → yeniden deneme (attempt+1) · ✅ 4xx → düşür (kuyruktan çıkar) · ✅ attempt>=5 → dead-letter (§4.4).

## §3 Kapsam Dışı (Bu Stratejide Yok)

| Alan | Sahip | Not |
|---|---|---|
| Domain event tüketici testleri (16 olay) | `K008-domain` | event-contracts.md sahipliği belirsiz (Q11) |
| Servis altyapısı testleri (registry/health/rate-limit) | `K012-services` | services-test-stratejisi.md §2-§6 |
| UI (IntegrationHub) testleri | `K011-application` | §7.7 K011'e devredilir |
| Ödeme motoru testleri | `K020-discovery` §23 | motor K020'de |
| Canlı API testleri (MusicBrainz/Jellyfin/YouTube) | 3. taraf (dış) | §16.4-§16.5: kaynak doğrulanmadı ⚠️ |

## §4 Test Edilebilirlik Engelleri (fictional K013)

1. **WebhookMotoru kodu YOK** (§22.9): §2.1-§2.2/§2.7 senaryoları motor uygulandığında geçerli; şu an yalnız fonksiyon söylemleri (kod-ornekleri §2) test edilebilir.
2. **API-Adapter kodu YOK** (§16): contract test (pact) uygulanamaz.
3. **Outbox kodu YOK** (ADR-037): §2.3 senaryosu uygulama geldiğinde.
4. **Canlı API doğrulanmadı** (§16.4-§16.5): e2e için önce web-arastirma.md §5-§7 doğrulaması.

## §6 Test Ortamı Gereksinimleri (uygulama geldiğinde)

| Gereksinim | Neden | Durum |
|---|---|---|
| WebhookMotoru kodu | §2.1-§2.2/§2.7 senaryoları | ⚠️ YOK (§22.9) |
| Outbox tablosu + CDC | §2.3 atomiklik | ⚠️ YOK (ADR-037) |
| 3. taraf sandbox hesap | canlı imza doğrulama (Stripe/GitHub) | ⚠️ ERİŞİM YOK |
| Contract test aracı (pact) | API-Adapter §16 | ⚠️ YOK (fictional) |

## §7 Kabul Kriteri Özeti

| Senaryo | Ana kabul kriteri | Blok (kod-ornekleri) |
|---|---|---|
| §2.1 Stripe imza | hash_equals + tolerans 300 sn | §2.1 |
| §2.2 GitHub imza | sha256= prefix + timingSafeEqual | §2.2 |
| §2.3 Outbox | commit→var / rollback→yok | §3.1 |
| §2.4 Idempotency | (event_id,handler) UNIQUE | §4.1 |
| §2.5 Retry | exponential 200/400/800 ms + jitter | §5.1 |
| §2.6 Circuit Breaker | %50 → open 30 sn → half-open 3 | §6.1 |
| §2.7 Webhook retry | 5× sonrası dead-letter | §14.1 |

## §8 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K013-integraion/integraion-kod-ornekleri.md` | Test edilecek söylemler (15 blok) | integraion-kod-ornekleri |
| `.ai/architecture/K013-integraion/integraion-web-arastirma.md` | Doğrulanmış kaynaklar | integraion-web-arastirma |
| `.ai/architecture/K18-testing/test-stratejisi.md` | Genel test stratejisi (K18) | test-stratejisi |
