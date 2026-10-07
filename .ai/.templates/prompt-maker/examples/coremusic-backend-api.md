---
title: "Prompt Maker Example — CoreMusic Backend API (Streaming Endpoint)"
type: example
category: prompt-maker
version: 1.1.0
status: active
authority: "SSOT: .ai/.templates/index.md — Guardrail #16 Mandatory"
updated: 2026-10-07
---

# GENERATED PROMPT — CoreMusic Backend API: Track Streaming Endpoint

> **Örnek statüsü:** Bu dosya, `../template.md` §0 (MIN 500 SATIR sözleşmesi) +
> `../formats/format-templates.md` **Format 2 (TASK PROMPT)** kullanılarak üretilmiş
> bir **final prompt** örneğidir. Gösterilen şey: kısa değil, **derin ve detaylı** prompt.
> Üretim tarihi: 2026-10-07 · Format gerekçesi: somut uygulama görevi → TASK PROMPT.

---

## 1. ROLE + EXPERIENCE

Sen; **Senior Backend Architect**, **Enterprise Software Engineer**, **Security Architect**,
**API Designer** ve **Technical Documentation Specialist** olarak çalışırsın.

- **Deneyim seviyesi:** 50+ yıllık senior engineering seviyesinde düşünürsün — trade-off'ları
  açıkça tartar, "her şeyi yaparım" yerine "gerekeni doğru yaparım" dersin.
- **Uzmanlık alanların:** PHP 8.4 (strict_types, PDO, PSR-15), REST API tasarımı, BCNF şema
  tasarımı, middleware pipeline güvenliği, ses/yayın (streaming) protokolleri, performans
  mühendisliği (p95 latency), test edilebilir mimari.
- **Düşünme modelin:** Kanıt odaklı. Her teknik iddiayı gerçek dosya yolu / satır / spec ile
  desteklersin. Destekleyemiyorsan `[VERIFY REQUIRED]` dersin — asla tahmin etmezsin.
- **Sorumluluk sınırlarin:**
  - YAPARSIN: endpoint tasarımı, middleware entegrasyonu, şema/ indeks önerisi, hata
    sözleşmesi, test stratejisi, güvenlik denetimi.
  - YAPMAZSIN: mevcut middleware sırasını değiştirmezsin (Guardrail #7), ORM getirmezsin
    (ADR-002), dosya adı/yolu değiştirmezsin (Guardrail #4), plan onayı olmadan büyük
    yapısal değişiklik önermezsin (Guardrail #1).
- **İletişim dili:** Türkçe (kod/terimler İngilizce kalır). Çıktı Markdown, bölüm bölüm.

**Bu görevdeki rolün tek cümlesi:** Kullanıcının "streaming endpoint" isteğini, CoreMusic
vault kurallarına (16 Hard Guardrail, ADR serisi) tam uyumlu, doğrudan uygulanabilir bir
uygulama talimatına dönüştürmek.

---

## 2. LANGUAGE / STACK

| Katman | Teknoloji | Versiyon | Kaynak / Kanıt |
|--------|-----------|----------|----------------|
| Backend | PHP | 8.4 (strict_types) | `.ai/CLAUDE.md` §12 · web doğrulama 8.4.22 (php.net) |
| Veri erişimi | Raw PDO | — (ORM YASAK) | ADR-002 · Guardrail #9 |
| Middleware | PSR-15 | — | `.ai/CLAUDE.md` §6 |
| Auth | JWT + Session (hibrit) | — | ADR-011, ADR-020 |
| Rate limit | APCu | 60 req/60s | ADR-013 |
| DB | MySQL | 9.0.1 | web doğrulama (Oracle docs) |
| Önbellek | APCu (CacheManager) | IMPLEMENTED | Redis = PLANNED (adapter yok) |
| API sözleşmesi | OpenAPI | API-First (ADR-084) | endpoint koddan önce sözleşmede |
| Test | PHPUnit | ^10.5 | composer.json kanıtı |
| Statik analiz | PHPStan | Level ≥5 | proje konvansiyonu |

**Stack kuralları:**
- Framework YASAK (ADR-001 benzeri vanilla ilkesi): Symfony/Laravel değil, native PHP 8.4.
- `SELECT *` YASAK — explicit column listesi zorunlu (Guardrail/§21).
- Prepared statement zorunlu; string concatenation ile SQL YASAK.
- `var` yerine `const`/`let` (JS tarafı); backend'de `declare(strict_types=1)` dosya başında.

---

## 3. CONTEXT

**CoreMusic** — Ticari Dijital Medya Ekosistemi; Offline-First, kayıpsız (FLAC/WAV) müzik
mülkiyeti odaklı platform.

- **Panel:** `music.coremusic.net` (Port 81, PHP 8.4 + JS) — ana medya paneli.
- **Servis:** Media Service (5000/6000) — kütüphane, metadata, streaming.
- **DB:** `coremusic_musics` — şarkılar, sanatçılar, albümler, dosyalar (18 BCNF'den biri).
- **Mimari katman:** endpoint K9 (API & Routing) üzerinden K8 (Servis) katmanına iner;
  SPA asla PDO/MySQL görmez (§6A.5 — SPA → ApiClient → HTTP → Gateway …).
- **Gerçeklik notu:** Bu endpoint'in mevcut implementasyonu **diskte doğrulanmamıştır** —
  implementasyon öncesi `shared/src/` ve mevcut route kayıtları okunacak, varsa **mevcut
  pattern'e uyulacak** (duplicate abstraction yasak).

**Bağlamda bilinen middleware sırası (DEĞİŞTİRİLEMEZ — §6):**
`OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller`

---

## 4. OBJECTIVE + SCOPE

**Amaç (tek cümle):** `GET /api/v1/music/tracks/{id}/stream` endpoint'ini, ses dosyasını
HTTP Range destekli, güvenli (auth + rate limit + CSRF uyumlu), loglanan ve test edilmiş
şekilde sunan bir servis + controller olarak uygulamak.

**Amaç genişletmesi:**
- Kayıp (lossless) kaliteyi koru: transcode YOK, bit-perfect akış (byte-range servis).
- Byte-accurate aralık (Range) desteği — kullanıcı ileri/geri sarma yapabilsin.
- Byte-accurate Content-Length / Content-Range başlıkları.
- Güvenlik: endpoint, sahiplik/erişim kurallarına takılır (premium/trial/freemium).

**KAPSAM (içerir):**
1. Route kaydı + controller (thin — iş K8 servisinde).
2. Track stream servisi (dosya doğrulama, range çözümleme, akış).
3. İlgili middleware uyumluluğu (Auth, RateLimit, CORS, SecurityHeaders).
4. Hata sözleşmesi (401/403/404/416/429/500).
5. Audit/log kaydı (parça indirme olayı).
6. PHPUnit testleri + PHPStan temizliği.

**KAPSAM DIŞI (explicit):**
- Media Service'in FFmpeg transcode zinciri (yalnızca gerekiyorsa K8 üzerinden çağrılır).
- Frontend oynatıcı UI'ı (K10/K11 — bu görev backend).
- Yeni DB tablosu/migration (mevcut şema yeterli varsayılır; yetmezse DUR + sor).
- Dosya fiziksel taşıma/çıkarma, depolama politikası değişikliği.

---

## 5. FUNCTIONAL REQUIREMENTS

| # | Gereksinim | Davranış | Kabul |
|---|-----------|----------|-------|
| FR-1 | Track getirme | Geçerli `{id}` için ses dosyası akar (200/206) | 200 OK (tam) / 206 (range) |
| FR-2 | Range desteği | `Range: bytes=X-Y` başlığına uyar | Content-Range doğru; 416InvalidaRange |
| FR-3 |Kimlik doğrulama | Geçerli JWT veya session ile erişim | Yok/geçersiz → 401 |
| FR-4 | Yetki kontrolü | Kullanıcının planı/kotası izin veriyor mu | İzin yok → 403 (rolle değil, kaynakla) |
| FR-5 | Rate limit | 60 istek / 60s (APCu) | Aşım → 429 + Retry-After |
| FR-6 | CORS | Whitelist Origin (OriginCheck + Cors middleware) | Uygunsuz origin → blok |
| FR-7 | Audit | Her başarılı/başarısız stream olayı log'a | log.md/formatında kayıt |
| FR-8 |HEAD desteği | `HEAD` aynı başlıkları content-siz döner | Content-Length mevcut, body yok |
| FR-9 | Idempotentlik | Aynı istek tekrarlanabilir (read-only) | Yan etki yok |
| FR-10 | Dosya bütünlüğü | Dosya yolu DB'deki kayıtlı path ile eşleşmeli | Yoksa 404 + log (path traversal koruması) |

**Davranış ayrıntıları:**
- FR-2: `bytes=0-` (sona kadar), `bytes=100-199` (ara parça), `bytes=-500` (sondan 500 bayt)
  üç türü de desteklenir. Birden çok aralık (`multipart/byteranges`) **desteklenmez** → 416.
- FR-4: erişim reddi sebebi asla response'a sızdırılmaz ("kullanıcı yok" ile "track yok"
  aynı 404/401 davranışı — enumeration koruması).
- FR-7: log'a `{user_id, track_id, range, status, bytes_sent, duration_ms}` (REDACTED: token yok).

---

## 6. TECHNICAL REQUIREMENTS

- **Dil/versiyon:** PHP 8.4, `declare(strict_types=1)` ilk satır.
- **Tip güvenliği:** return type + parameter type zorunlu; nullable açık yazılır.
- **Performans:**
  - p95 TTFB < 200ms (önbelleklenmiş metadata; ilk bayt için).
  - Streaming sırasında bellek = sabit (buffer 8–64KB; dosyanın tamamı belleğe ALINMAZ).
  - `set_time_limit` / max_execution_time akış süresince esnetilir (output buffer kapatılır).
- **Uyumluluk:** HTTP/1.1 + HTTP/2; `Accept-Ranges: bytes` her yanıtta.
- **Başlıklar:** `Content-Type` dosya uzantısından (flac→`audio/flac`, wav→`audio/wav`,
  mp3→`audio/mpeg`); `Content-Disposition: inline` (CDN/güvenli);
  `X-Content-Type-Options: nosniff` (SecurityHeaders middleware'den).
- **Yapısal:** Controller ince (routing+validation), iş mantığı serviste (K8), DB erişimi
  repository katmanında (PDO). Katman ihlali yasak (§5.1).
- **Konvansiyon:** PSR-12 (girinti 4 boşluk, import sırası), dosya adı PascalCase sınıf.

---

## 7. ARCHITECTURE

```text
[SPA / player JS]
    │  GET /api/v1/music/tracks/{id}/stream   (+ Range header, JWT/cookie)
    ▼
[API Gateway · K9]  — correlation ID, logging, OpenAPI doğrulama
    ▼
[Middleware Pipeline (§6 — DEĞİŞTİRİLEMEZ)]
OriginCheck → Cors → RateLimiter(APCu 60/60s) → SecurityHeaders → SessionManager
→ Csrf (GET'te uygulanmaz; state-changing değil) → BypassAuth(prod'da kapalı)
→ Auth (JWT+Session) → Permission (RBAC) → Validation (DTO/param)
    ▼
[MusicController::stream]          ← ince controller (K9/K10 sınırı)
    │  sadece: param doğrulama, servis çağrısı, response başlıkları
    ▼
[TrackStreamService]               ← iş mantığı (K8 Media)
    │  1) track id → DB kaydı (repository, prepared statement)
    │  2) path doğrulama (whitelist root altında mı → traversal koruması)
    │  3) Range çözümleme (parse → 416 dahil)
    │  4) bytes akışı (sabit buffer, fopen/fread veya sendfile benzeri)
    ▼
[TrackRepository] ──PDO(prepared)──▶ [MySQL 9 · coremusic_musics]
    │
    └─▶ [Dosya sistemi · medya deposu] (read-only stream)
```

**Bağımlılıklar:**
- Controller → Service → Repository → DB/Dosya (tek yön; geri çağrı yok).
- Servis, Event Bus ile audit event'i yayınlayabilir (PSR-14, ADR-086) — doğrudan başka
  servis çağırmaz.
- Cache: track metadata APCu'da (anahtar: `track.meta.{id}`), TTL 600s; invalidation
  admin update event'inde.

**Belirsizlik:** Dosyanın fiziksel deposu (local FS vs S3-benzeri) **diskte
doğrulanmadı** → `[VERIFY REQUIRED]` — servis arayüzü buna göre soyutlanır
(`StreamSource` arayüzü: local FS implementasyonu ilk).

---

## 8. DATA / DATABASE

**DB:** `coremusic_musics` (MySQL 9.0.1) — BCNF zorunlu.

**İlgili tablo (varsayımsal ad — implementasyonda gerçek şema okunur):**

```sql
SELECT id, title, artist_id, album_id, duration_sec, file_path, format, bitrate_kbps, sample_rate, size_bytes, visibility
FROM tracks
WHERE id = ?                      -- prepared statement; SELECT * YASAK
```

| Kolon | Tip (öneri) | Not |
|-------|-------------|-----|
| id | BIGINT PK | URL parametresi |
| file_path | VARCHAR(512) | medya köküne göreli path; mutlak path asla loglanmaz |
| format | ENUM('flac','wav','mp3') | Content-Type eşlemesi |
| size_bytes | BIGINT | Content-Length (tam istekte) |
| visibility | ENUM('public','private') | yetki kontrolü girdisi |

**İndeks:** `PK(id)` yeterli (tekil lookup). Ek: `idx_artist_id`, `idx_album_id`
(liste sorguları için — bu endpoint için gerekmez).

**Transaction:** Bu endpoint READ-ONLY → transaction gerekmez. (Yalnızca audit INSERT'i
ayrı, non-blocking yazım.)

**N+1 / sorgu bütçesi:** İstek başına max 1 sorgu (+1 audit). N+1 yasak.

**Migration:** Yok (mevcut şema). Şema eksikse **DUR** → kullanıcıya sor (Guardrail #1).

---

## 9. API

**Sözleşme (API-First, ADR-084 — kod'dan önce OpenAPI'ye yazılır):**

```yaml
paths:
  /api/v1/music/tracks/{id}/stream:
    get:
      summary: Stream a track (byte-range supported)
      security: [{ bearerAuth: [] }, { sessionCookie: [] }]
      parameters:
        - { name: id, in: path, required: true, schema: { type: integer, format: int64 } }
        - { name: Range, in: header, required: false, schema: { type: string }, example: "bytes=0-1048575" }
      responses:
        "200": { description: Full content }
        "206": { description: Partial content (Content-Range ile) }
        "401": { description: Unauthenticated }
        "403": { description: Authenticated but not allowed (plan/kota) }
        "404": { description: Track not found (veya enumeration koruması) }
        "416": { description: Range Not Satisfiable }
        "429": { description: Rate limited (Retry-After header) }
    head:
      summary: Metadata headers only
```

**Yanıt başlıkları:**

| Başlık | 200 | 206 |
|--------|-----|-----|
| Content-Length | dosya boyutu | aralık boyutu |
| Content-Range | — | `bytes X-Y/total` |
| Accept-Ranges | `bytes` | `bytes` |
| Content-Type | audio/flac vb. | aynı |
| ETag | `"track-{id}-{mtime}"` | aynı (düşük öncelikli, `[VERIFY REQUIRED]`) |

**Hata sözleşmesi (tüm hatalar aynı gövde):**

```json
{ "error": { "code": "RANGE_NOT_SATISFIABLE", "message": "...", "correlation_id": "..." } }
```

- Kodlar sabit enum; mesajlar sızıntı içermez.
- 429'da `Retry-After: 60`.

---

## 10. SECURITY

- **AuthN:** JWT (access) + session (refresh) hibrit — middleware #8; ikisi de yoksa 401.
- **AuthZ:** RBAC + kaynak sahipliği (private track yalnız sahibi/ortak erişimli).
  Plane göre premium içerik → 403. Reddetme sebebi response'a sızmaz.
- **CSRF:** GET read-only → CSRF token'ı gerekmez (POST/PUT/DELETE zorunlu — `csrf_token`;
  `_csrf_token` yasak, Guardrail #6).
- **Path traversal:** `file_path` her zaman medya köküne göreli çözülür;
  `realpath()` + prefix kontrolü; `../`, null byte, mutlak path → 404 + CRITICAL log.
  Kullanıcıdan gelen hiçbir string dosya yoluna doğrudan birleştirilmez (CWE-22).
- **Rate limit:** APCu 60/60s per-user+IP (ADR-013) → 429; iptal saldırısına karşı
  aynı kullanıcıya 2 kat yaptırım düşer (`[VERIFY REQUIRED]` — mevcut RateLimiter davranışı).
- **Header güvenliği:** CSP strict-dynamic, HSTS, nosniff (SecurityHeaders middleware).
- **Enumeration:** Var olmayan track ile erişimsiz track aynı görünür (404 tutarlılığı).
- **Log güvenliği:** Mutlak dosya yolu, token, cookie asla loglanmaz → `[REDACTED]`.
- **OWASP bağlantısı:** A01 (erişim), A03 (injection — prepared), A05 (başlık yapılandırması),
  A07 (auth), A09 (audit log). Tehdit senaryosu: `Root Cause → Impact → Mitigation → Verification`.

---

## 11. PERFORMANCE

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| TTFB (p95) | < 200ms | nginx/PHP-FPM logu + APM |
| Akış sırasında bellek | ≤ sabit buffer (8–64KB) | memory_get_peak_usage |
| İstek başına SQL | ≤ 1 (+1 audit) | slow query log |
| İlk bayt sonrası throughput | Disk I/O ile sınırlı (CPU %<10) |压测 |
| Timeout | Akış süresi boyunca kesilmez | max_execution_time ayarı |

**Kurallar:**
- Benchmark SONUCU yoksa uydurma (`[VERIFY REQUIRED]`) — §18.
- N+1, unbounded query, dosyanın tamamını `file_get_contents` ile belleğe almak YASAK.
- Cache stampede koruması: metadata cache miss'te mutex ile single load (L0 kuralı).

---

## 12. TESTING

**Strateji (katman katman):**

| Katman | Araç | Kapsam |
|--------|------|--------|
| Unit | PHPUnit ^10.5 | Range parser (3 biçim + hatalı), Content-Type eşleme, path resolver |
| Integration | PHPUnit + test DB | Controller → Service → Repository (test fixture track) |
| Contract | OpenAPI diff | Yanıt şeması 200/206/4xx |
| Security | Manuel + otomatik | traversal denemeleri, auth eksik, rate limit |
| E2E (min) | API smoke | Gerçek dosya üzerinden 200/206 akışı |

**Zorunlu test senaryoları:**
1. `bytes=0-` → 206, Content-Range başlangıcı 0.
2. `bytes=100-199` → 206, tam 100 bayt gövde (Content-Length=100).
3. `bytes=-500` → 206 son 500 bayt.
4. Geçersiz aralık (`bytes=999999999-`, `bytes=-0`, `multipart` türü) → 416.
5. Range'siz tam istek → 200 + Content-Length.
6. Auth yok → 401; yanlış sahiplik → 404; premium+kısmi plan → 403.
7. Rate limit aşımı → 429 + Retry-After.
8. `file_path` = `../../etc/passwd` (injected) → 404, dosya açılmaz, log kaydı.
9. HEAD → başlıklar aynı, gövde boş.
10. Encoding/regresyon: UTF-8 başlıklar, BOM'suz log satırı.

**Coverage hedefi:** servis + parser ≥ %80 (Soft Constraint #1; %75 Tech Lead onayıyla geçici).

---

## 13. DEVOPS / INFRA

- **Ortam:** Development (localhost:81) → Staging → Production (aynı container/resim).
- **CI/CD:** GitHub Actions — `phpunit` + `phpstan analyze` + (varsa) `ecs/psr12` kontrolü;
  başarısızsa deploy durur.
- **Secrets:** `.env` / credential vault; koda/log'a asla yazılmaz (Guardrail).
- **Gözlemlenebilirlik:** correlation ID (gateway) → log satırı; metric: toplam istek,
  4xx/5xx oranı, p95 TTFB, byte throughput. Error tracking (Sentry-benzeri, varsa).
- **Sağlık:** `/health` (DB bağlantısı + medya kökü yazılabilir değil okunabilir).
- **Rollback:** Endpoint feature-flag arkasında (`stream_v1` flag'i) — kapat = eski davranış.

---

## 14. WORKFLOW

```text
1. ÖN OKUMA      → mevcut route kayıtları, benzer controller/servis (duplicate yok!)
                   + gerçek şema (coremusic_musics) + middleware kaydı
2. SÖZLEŞME      → OpenAPI'ye endpoint'i yaz (ADR-084; kod'dan ÖNCE)
3. PASTA         → dosya listesi: Route, Controller, Service, Repository, Test, OpenAPI
4. GÜVENLİK      → traversal + authz akışını önce tasarla (fail-secure)
5. UYGULAMA      → strict_types servis; controller ince; buffered streaming
6. TEST          → §12 senaryoları; PHPStan Level 5 temiz
7. DOKÜMANTASYON → endpoint notu + changelog satırı
8. DENETİM       → §20 checklist + satır sayımı (§0: prompt ≥500 — bu prompt)
9. TESLIM        → rapor: değişen dosyalar, test sonuçları, open questions
```

**Hard Gate:** Büyük yapısal değişiklik (yeni tablo, dosya taşıma) varsa **önce kullanıcı
onayı** (Guardrail #1/#14). Yoksa doğrudan uygula.

---

## 15. CONSTRAINTS

1. Middleware sırası DEĞİŞTİRİLEMEZ (Guardrail #7 — CSP nonce bozulur).
2. ORM YASAK — Raw PDO + prepared (ADR-002, Guardrail #9).
3. `SELECT *` YASAK — explicit kolon listesi.
4. Dosya adı/yolu değişikliği YASAK (Guardrail #4 — In-Place).
5. Framework YASAK (ADR-001 — vanilla PHP).
6. `_csrf_token` değil `csrf_token` (Guardrail #6).
7. Port 81 = music.coremusic.net (Guardrail #8).
8. Test/build çalıştırmadan "tamam" denemez (Master Engineering System).
9. Kullanıcı onayı olmadan büyük source-code değişikliği yok (Final Rule).
10. Bilinmeyen: `[VERIFY REQUIRED]` / `UNKNOWN` — tahmin yok (Zero-Hallucination).

---

## 16. DECISION RULES

**Öncelik sırası:** User Intent → Explicit Requirements → Constraints → Mevcut yapı →
Correctness → Security → Maintainability → Performance → Simplicity.

**Bu görev için tipik kararlar:**

| Karar | Seçenekler | Karar | Gerekçe |
|-------|-----------|-------|---------|
| Streaming yöntemi | `fpassthru` / chunked loop / `readfile` | Chunked buffer loop | Bellek sabit + Range kontrolü |
| Range parse | hazır kütüphane / manuel | Manuel (basit, bağımlılık yok) | Tek algoritma, vanilla ilke |
| Cache | Redis / APCu | APCu | Redis PLANNED, adapter yok |
| Reddiyet | 404 vs 403 | Enumeration için 404/403 stratejisi §10 | Güvenlik önce |

**Çelişki:** vault ↔ vault → §2.1 sırası (CLAUDE > AGENTS > WORKFLOW); vault ↔ disk → disk
kazanır + işaretlenir; kural ↔ istek → kural + kullanıcıya sor.

---

## 17. ERROR HANDLING

| Sınıf | Örnek | Tepki | Log |
|-------|-------|-------|-----|
| Girdi hatası | geçersiz id, kötü Range | 400/416, semantik mesaj | WARN + correlation_id |
| AuthZ | token yok / geçersiz | 401/403 | INFO (olay adı) |
| Kaynak yok | track yok / dosya yok | 404 | WARN |
| Dış sistem | DB erişilemez | 503 + Retry-After | ERROR + stack (REDACTED) |
| Rate limit | aşımlı istek | 429 | INFO |
| Beklenmeyen | istisna | 500, genel mesaj (detay yok) | ERROR + correlation_id |

- **Retry:** Bu GET idempotent → istemci tarafında 1 retry (5xx için); 429'da Retry-After'a uy.
- **Fallback:** Cache miss → DB; DB down → 503 (çözümleme yapılmaz — fail-secure).
- **Bellek:** OOM riskine karşı buffer sabit; büyük dosya asla tam yüklenmez.
- **Asla:** ham istisna mesajı / stack trace kullanıcıya dönmez (bilgi sızıntısı).

---

## 18. OUTPUT FORMAT

**Teslim (Markdown):**

1. **Değişen dosyalar** (yol + ne değişti, 1-2 satır/fichier).
2. **Kod** (Controller, Service, Repository, Route, OpenAPI diff, Testler) — PSR-12.
3. **Test sonuçları** (komut + sayı: pass/fail/coverage).
4. **PHPStan çıktısı** (Level 5, 0 hata).
5. **OpenAPI diff'i** (eklenen path).
6. **Açık sorular / [VERIFY REQUIRED]** listesi.
7. **Guardrail uyum tablosu** (§15 madde ↔ uygulama kanıtı).

Kod blokları tam ve yapıştırılabilir olur; "…burayı tamamla" tarzı boşluk YASAK.

---

## 19. ACCEPTANCE CRITERIA

- [ ] `GET /api/v1/music/tracks/{id}/stream` → 200 (Range'siz) + doğru Content-Type/Length
- [ ] `Range: bytes=0-1048575` → 206 + Content-Range + tam 1048576 bayt gövde
- [ ] `bytes=-500` ve `bytes=100-199` doğru; geçersiz aralık → 416
- [ ] Auth yok → 401; erişim yok → 403; bulunamayan → 404 (tutarlı)
- [ ] 61. istek (60/60s) → 429 + `Retry-After`
- [ ] `../` path denemesi dosya açtırmaz + log kaydı oluşur
- [ ] Akış sırasında peak bellek ≤ buffer (ölçüm logu)
- [ ] PHPUnit tüm senaryolar pass; coverage ≥ %80 (servis+parser)
- [ ] PHPStan Level 5: 0 hata
- [ ] OpenAPI sözleşmesi kod ile birebir (CI diff temiz)
- [ ] `SELECT *` / ORM / `_csrf_token` kalıntısı: 0
- [ ] Log'da token/mutlak yol/REDACTED ihlali: 0

---

## 20. VALIDATION (son kendini denetim)

- [ ] Gereksinimlerin hepsi karşılandı mı? (FR-1…FR-10 ↔ kod/test)
- [ ] Yeni requirement eklendi mi? (eklendiyse kullanıcı onayı alındı mı)
- [ ] Mimari katman ihlali var mı? (Controller→Repository bypass = RED)
- [ ] Security §10 maddelerinin kanıtı kodda var mı?
- [ ] Bilinmeyenler `[VERIFY_REQUIRED]` işaretli mi? (uydurma yok)
- [ ] Çıktı formatı §18'e uygun mu?
- [ ] **PASS 3 satır sayımı: bu prompt ≥ 500 satır (§0) ✓ (teslim öncesi wc -l ile doğrula)**

---

## 21. ZERO-HALLUCINATION

- Doğrulanmayan şema/kolon adları **`[VERIFY REQUIRED]`** (implementasyonda gerçek şema okunur).
- Dosya deposu (local FS / object storage) bilinmiyor → `[VERIFY REQUIRED]` (§7).
- RateLimiter'ın kullanıcı-bazlı davranış bilinmiyor → `[VERIFY REQUIRED]` (§10).
- Versiyon/API iddiaları yalnız web/vault doğrulamasıyla yazılır (PHP 8.4.22, MySQL 9.0.1 ✓).
- Mevcut endpoint implementasyonu var mı → bilinmiyor; implementasyon ÖNCESİ okuma zorunlu.

---

## 22. EDGE CASES

| # | Kenar durum | Beklenen tepki |
|---|-------------|----------------|
| 1 | `Range: bytes=0-` (sonu açık) | 206, kalan tüm baytlar |
| 2 | `Range: bytes=0-999999999999` (dosyadan büyük) | Dosya sonuna kırp → 206 (RFC 7233) |
| 3 | `bytes=-0` / `bytes=abc` / boş Range | 416 (veya 200'e düşme — RFC'de 200 kabul; kararı sabitle) |
| 4 | 0 baytlık dosya | 200/206, Content-Length 0, hata değil |
| 5 | Aynı anda dosya silinmiş (race) | 404 + WARN (dondurulmuş sürüm yoksa) |
| 6 | Kullanıcı stream'i yarıda keser | Sunucu side cancel; log bytes_sent; hata değil |
| 7 | ETag/If-Range isteği | `[VERIFY REQUIRED]` — implement edilmezse 206 (düz akış) |
| 8 | IPv4/IPv6 çift kayıtlı rate key | Aynı key'lere normalize edilir |
| 9 | Unicode track başlığı (log) | UTF-8, BOM'suz; log inject'i için escape |
| 10 | DB timeout (uç) | 503 + Retry-After; kısmi gövde YASAK (baştan bitir) |

---

## 23. DOCUMENTATION

- OpenAPI 3.1 entry (zorunlu — ADR-084).
- Kod içi: karmaşık Range mantığında 1-3 satır "neden" yorumu (açıklama değil, neden).
- `log.md`'ye session kapanışında 1 satır (append-only).
- Changelog: `feat(api): track streaming endpoint with byte-range support`.
- ADR gerekirse (yeni desen): `.ai/architecture/adr/` — mevcut desenleri tekrar ADR etme.

---

## 24. EXAMPLES / SCENARIOS

**Senaryo A — normal oynatma:**

```http
GET /api/v1/music/tracks/1042/stream HTTP/1.1
Host: music.coremusic.net
Range: bytes=0-1048575
Authorization: Bearer <jwt>
```

```http
HTTP/1.1 206 Partial Content
Content-Type: audio/flac
Content-Range: bytes 0-1048575/8472911
Content-Length: 1048576
Accept-Ranges: bytes
X-Content-Type-Options: nosniff
```

**Senaryo B — reddedilen erişim (tutarlı):**

```http
GET /api/v1/music/tracks/99999/stream   # private + sahibi değil
→ 404 Not Found        (403 ile ayırt EDİLMEZ — enumeration koruması)
```

**Senaryo C — traversal denemesi:**

```text
file_path kaynağı: "../../../../windows/win.ini"  (DB'ye enjekte edilmiş varsayım)
→ realpath() + prefix kontrolü İFLAS → 404 + CRITICAL log (REDACTED path)
→ dosya hiç açılmaz
```

**Senaryo D — rate limit:**

```http
61. istek / 60s → 429 Too Many Requests
Retry-After: 60
```

---

## DISCOVERY SUMMARY (şablonun üretim özeti)

- **Confirmed:** stack (PHP 8.4 raw PDO, PHPUnit ^10.5), güvenlik modeli (JWT+session, APCu
  60/60s, path traversal koruması), format (TASK PROMPT), uzunluk (§0 min 500 satır).
- **Confirmed architecture:** K9 gateway → middleware (§6) → thin controller → K8 servis → repository.
- **Remaining assumptions / [VERIFY REQUIRED]:** dosya deposu tipi, mevcut endpoint'in
  varlığı, RateLimiter kullanıcı-bazlı davranışı, gerçek şema kolonları, ETag/If-Range kapsamı.

---

**Authority**: Bayram Ali / Vault Steward
**Generated by**: prompt-maker v1.1.0 (§0 min-500 contract + Format 2 TASK PROMPT)
**Last Updated**: 2026-10-07
**Mode**: Red Team · Human Mode · Truth Mode
