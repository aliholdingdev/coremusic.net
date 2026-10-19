---
node_id: K013-integraion/integraion-riskler.md
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
  - K015-AI
evidence:
  - ".ai/architecture/K013-integraion/integration-master.md §5.1-§5.5 (fictional), §14 (webhook), §16.4-§16.5 (doğrulanmayan kaynak), §22-§23 (açık konular) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-kararlar.md §9-§11 (ADR-094/095/037 — kod YOK), §13 (açık konular) — okundu 2026-10-18"
  - ".ai/.decisions/index.md ADR-094 · ADR-095 · ADR-037 · ADR-081 (draft) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 10 risk; her biri Probability × Impact × Mitigation × Owner; risk ID'leri bu dosyada üretilir (uydurma ADR yok)"
open_questions:
  - "⚠️ Risk-06 (event-contracts konuşa biçimi belirsiz) — sahiplik dağıtık (K013+K015+K012) — tek owner KARAR YOK."
  - "⚠️ Risk-09 (healthService aidiyeti K010 vs K012) — owner belirsiz (K010 vs K012)."
  - "⚠️ Owner'lar K012 master §20 rol atamasından türetildi — entegrasyon rolleri (integration-engineer) fictionel."
group: kanit
---

# K013 INTEGRAION — Riskler

> Format: Probability (düşük/orta/yüksek) × Impact (düşük/orta/yüksek) × Mitigation × Owner (K012 master §20 rollerinden türetildi — roller fictionel).
> Kaynak: `integration-master.md` §5.1-§5.5 · §14 · §16.4-§16.5 · §22-§23 · `integraion-kararlar.md` §9-§13.

## §1 Risk Kayıt Defteri (10 risk)

| # | Risk | Probability | Impact | Mitigation | Owner |
|---|---|---|---|---|---|
| R-INT-01 | **3. taraf API değişikliği/kapanma** (MusicBrainz/Jellyfin/Plex/YouTube Music) — adaptör kırılır | Yüksek | Yüksek | CircuitBreaker + Timeout + Retry (ADR-094/095 — **kod YOK**); domain fallback (K008 cache); adaptör izolasyonu (§16) | `integration-engineer` (fictional) |
| R-INT-02 | **YouTube Music resmi API yok** (§22.5: doğrulanmadı) — entegrasyon imkânsız olabilir | Yüksek | Orta | Invidious/Piped proxy alternatifi (§10.2); doğrulama görevi (researcher); fallback domain (K008) | `research-analyst` + `integration-engineer` |
| R-INT-03 | **Webhook imza doğrulama yok** (§14 motor YOK) — sahte webhook kabulü | Yüksek | Yüksek | İmza doğrulama zorunlu (Stripe `t=,v1=` / GitHub `sha256=` — §14.1-§14.2; doğrulandı 2026-10-18); raw body (§14.3); timestamp toleransı; **motor uygulaması YOK** → üretim öncesi kritik | `security-engineer` (K009) |
| R-INT-04 | **Outbox/Event konuşa biçiminde; kod yok** (ADR-037/086; §22.8) — olay kaybı/iki-adım tutarsızlık | Yüksek | Yüksek | ADR-037 (outbox + CDC) uygulanana kadar senkron fallback yasak; event store netleştirme (Q11); idempotent consumer (§4) | `backend-developer` + `data-engineer` |
| R-INT-05 | **Medya sunucu credential sızıntısı** (Jellyfin/Plex/Navidrome §5.1-§5.3) — API key düz metin | Orta | Yüksek | K009 secret store (AES-256-GCM); .env (kural: secret .md/.json/.log'a yazılmaz); scope minimizasyonu (ADR-081); rotasyon (ADR-032) | `security-engineer` |
| R-INT-06 | **Event-contracts 16 olay konuşa biçimi belirsiz** (§22.6) — uyumsuz consumer'lar | Yüksek | Orta | ADR-084 (adlandırma) + ADR-101 (Redis Streams) — uygulama YOK; event-contracts.md sahiplik netleştirme (K013 vs K015 vs K012) | `architect` + `backend-developer` |
| R-INT-07 | **Sync sahipliği çelişkisi** (§22.4: K013 playback-sync.md vs K015 media-master §21.4) — sahipsiz özellik | Orta | Orta | §22.4: "karar YOK" — ADR gerektirir; Q3 sync sahibi netleştirilene kadar uygulama yapılmaz | `architect` (karar) + `product-manager` |
| R-INT-08 | **API-Adapter kaynakları doğrulanmadı** (§16.4-§16.5) — kurgusal endpoint'ler | Yüksek | Orta | §16.4-§16.5: "kaynak/URL doğrulanmadı" — web-arastirma.md §5-§7 doğrulaması; doğrulanmayan adaptör uygulanmaz (zero-hallucination) | `research-analyst` |
| R-INT-09 | **healthService aidiyeti belirsiz** (§10.4 K010 vs K012 ServiceHealth) — çift sistem | Düşük | Orta | ADR gerektirir; tek health sistemi seçilene kadar iki tanım da durur (master §22 notu) | `architect` + `K010`/`K012` sahibi |
| R-INT-10 | **Webhook yeniden deneme duplikasyonu** (§14.6) — aynı event 2× işlenir | Orta | Orta | Idempotency kaydı (§4: event_id UNIQUE); Stripe idempotency-key (web-arastirma §6); consumer idempotent (ADR-086 konuşa biçimi) | `backend-developer` |

## §2 Risk Dağılımı

| Olasılık × Etki | Sayı | Riskler |
|---|---|---|
| Yüksek × Yüksek | 4 | R-INT-01, R-INT-03, R-INT-04, (R-INT-06 yüksek×orta) |
| Yüksek × Orta | 3 | R-INT-02, R-INT-06, R-INT-08 |
| Orta × Yüksek | 1 | R-INT-05 |
| Orta × Orta | 2 | R-INT-07, R-INT-10 |
| Düşük × Orta | 1 | R-INT-09 |

**En kritik 3**: R-INT-03 (webhook imza — motor YOK) · R-INT-04 (outbox kod YOK) · R-INT-01 (3. taraf kırılganlığı).

## §3 Azaltma Öncelik Sırası

1. **WebhookMotoru uygulaması** (R-INT-03): imza doğrulama + idempotency + retry — master §22.9 "motor YOK" → üretim öncesi kritik.
2. **Outbox uygulaması** (R-INT-04): ADR-037 — event kaybı/iki-adım riski.
3. **API doğrulama** (R-INT-02/R-INT-08): YouTube Music + API-Adapter kaynakları — web-arastirma §5-§7 ile doğrulanmış URL'ler kullanılmalı.
4. **Resilience4j uygulaması** (R-INT-01): CircuitBreaker/Retry kodu — §22.8 "konuşa biçimi var; kod YOK".

## §4 Risk → ADR Bağlantısı

| Risk | İlişkili Karar (integraion-kararlar.md) | Durum |
|---|---|---|
| R-INT-01 | ADR-094 (Resilience4j) · ADR-095 (Bulkhead/Timeout/Retry) | Konuşa biçimi — kod YOK |
| R-INT-03 | — (karar YOK; §14.1-§14.3 tasarım) | Motor uygulaması YOK |
| R-INT-04 | ADR-037 (Outbox + Debezium CDC) · ADR-086 (EDA) | Accepted — kod YOK |
| R-INT-05 | ADR-081 (OAuth 2.1 PKCE — draft) · ADR-032 (Refresh Token) | 081 DRAFT |
| R-INT-06 | ADR-084 (adlandırma) · ADR-101 (Redis Streams) | Accepted — uygulama YOK |
| R-INT-10 | — (idempotency — Stripe docs doğrulandı §6) | Kod YOK |

## §5 Erken Uyarı Tetikleyicileri

| Risk | Tetikleyici (izlenecek sinyal) | Eşik |
|---|---|---|
| R-INT-01 | 3. taraf HTTP 5xx/timeout oranı | %20 üstü → adaptör bypass (domain fallback) |
| R-INT-03 | Doğrulanmamış webhook sayfa sayısı | 1 → derhal imza doğrulama |
| R-INT-04 | outbox tablosunda yaşlı işlenmemiş satır | 5 dk üstü → DEAD LETTER alarm |
| R-INT-10 | Aynı event_id 2× işlenen consumer | 1 → idempotency kaydı |

## §6 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K013-integraion/integraion-kararlar.md` | ADR-094/095/037 + açık konular | integraion-kararlar |
| `.ai/architecture/K013-integraion/integraion-web-arastirma.md` | Doğrulanmış kaynaklar (24 URL) | integraion-web-arastirma |
| `.ai/architecture/K012-services/services-riskler.md` | K012 riskleri (sınır) | services-riskler |

## §7 Risk Sahipliği ve Gözden Geçirme Döngüsü

### §7.1 Sahiplik Matrisi

| Risk grubu | Sahip (rol) | Çözüm yetkisi | Escalation |
|---|---|---|---|
| R-INT-01, R-INT-02, R-INT-08 (3. taraf/API) | backend-architect + researcher | URL/limit doğrulama → research-analyst | Vault Steward |
| R-INT-03, R-INT-04, R-INT-10 (event/webhook) | architect (motor tasarımı) | ADR-037/086 kapsamı → Vault Steward onayı | Vault Steward |
| R-INT-05, R-INT-07 (auth/ölçekleme) | security-engineer (doğrulama) · devops-engineer (olay) | Yetki alanı içi karar | architect |
| R-INT-06 (adlandırma/kanal) | data-engineer + architect | Kanal şeması → ADR-101 | Vault Steward |
| R-INT-09 (UI asenkron) | backend-architect + code-reviewer | Hata sözleşmesi (error-*) → Vault Steward |

### §7.2 Gözden Geçirme Tetikleyicileri

| Tetikleyici | Aksiyon | Sıklık / Koşul |
|---|---|---|
| master.md §22 değişikliği (WebhookMotoru/Outbox kodu) | İlgili risk kapanır → Durum sütunu "kapatıldı" | Değişiklik başına |
| Yeni ADR draft'ı (integraion-kararlar §4) | Risk → ADR tablosu (§4) güncellenir | ADR taslağı açıldığında |
| Üretim olayı (R-INT-01/05/06 tetikleyicisi) | §5 eşik aşıldıysa 24 saat içinde azaltma planı | Olay başına |
| Kapsam değişikliği (K012/K013 sınırı) | Komşu risk dosyaları (services-riskler) çapraz okunur | Sınır kararında |

**Kural**: risk durumu yalnız kanıtla (kod/YOK, ADR/Durum, URL/erişilebilirlik) değişir;
"beklemede" yerine "konuşa biçimi — kod YOK" gibi somut durum yazılır (zero-hallucination).

## §8 Kaynak ve Kapsam Notu

Bu dosyadaki risk ID'leri (R-INT-01…10) bu dosyada üretilmiştir; ADR numaraları ise yalnız
`.ai/.decisions/index.md`'deki gerçek kayıtlardan alınmıştır (uydurma numara YOK).
Ana kanıt: `media-master.md` değil — `integration-master.md` §22 (bilinmeyenler) ve
§14 (tasarım boşlukları) ile `integraion-web-arastirma.md` (erişilebilir URL'ler).

Kapsam sınırı: yalnız K013-integraion iç riskleri. K012 (services-riskler), K014
(media-riskler §6 sınır satırı) ve K17-devops (olay/CI) riskleri bu dosyaya taşınmaz;
çapraz etki yalnız §7.2 tetikleyicileriyle izlenir.
