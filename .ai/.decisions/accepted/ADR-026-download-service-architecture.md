---
title: "CoreMusic — ADR-026: Download Service Architecture (İmzalı Tek Kullanımlık URL · Cihaz Limiti · Hız/Kota (ADR-013) · Byte-Range Resume · Abuse Önleme · Uygulama Stream + CDN Opsiyonel)"
type: adr
category: architecture
date: 2026-09-25
updated: 2026-09-25
version: 1.1.0
status: accepted
authority: ADR-026 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-026: Download Service Architecture (İmzalı Tek Kullanımlık URL · Cihaz Limiti · Hız/Kota · Byte-Range Resume · Abuse Önleme · Uygulama Stream + CDN Opsiyonel)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-026'yı sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı: **(a) imzalı tek kullanımlık URL** (HMAC, kısa TTL 5–15 dk, tek sefer) · **(b) cihaz limiti** (kullanıcı başına aktif cihaz sınırı) · **(c) hız/kota limiti** (ADR-013 rate limit'e bağla) · **(d) resume** — byte-range + chunk · **(e) abuse önleme** (hotlink, referer/origin kontrolü ADR-009 uyum, bot tespiti) · **(f) teslim yolu — uygulama stream (Content-Disposition: attachment, 206) + CDN opsiyonel (statik/bedava varlıklar için signed CDN URL — PLANNED, dürüst etiket)** · **(g) erişim kontrolü — satın alma/lisans doğrulama → imza üret; depo read-only (indirme yalnız okur)** · debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)**, Tech Lead **✅**)
**İlgili ADR'ler:** [[ADR-013-rate-limiting-apcu]] (hız/kota — indirme endpoint'i bu ADR'nin APCu penceresine bağlanır; `RateLimiterMiddleware.php:19` `windowSeconds = 60` IMPLEMENTED — dosya diskte VAR ✅) · [[ADR-009-clean-url-redirect]] (referer/origin/redirect disiplini — indirme zincirinde tek-atış redirect ve imzalı query koruması bu kararın uyum kapısı; dosya diskte VAR ✅) · [[ADR-020-api-public-security]] (APIสาธาร güvenliği — indirme endpoint'i public yüzeyde değil, oturum+kimlik doğrulamalı; dosya diskte VAR ✅) · [[ADR-002-pdo-mandatory-no-orm]] (erişim katmanı — satın alma/lisans/lisans-kontrol sorguları PDO prepared; dosya diskte VAR ✅) · [[ADR-022-database-hardened-security]] (şema sertleştirme — `coremusic_download` 4 tablo sorguları bu kurala uyar; dosya diskte VAR ✅) · [[ADR-081-multi-provider-data-sync]] (veri senkronu — indirme olayları outbox ile akar; **düzeltme (şart 1b — uygulandı):** "media depo read-only" ilkesinin doğru kaynağı **content-delivery.md (read-only ilkesi)**'dir; ADR-081 yalnız outbox/olay akışı içindir (§1.1-D) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · [[ADR-024-ecosystem-modular-docs]] (wiki-link disk kanıtı kuralı) · karar dizini [[../index]] **satır 63** `[[ADR-026-download-service-architecture]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-026-download-service-architecture` karar dizini `../index.md:63`'te **rezerve boş slottur** (ADR-024/025 aynı istisnayı kaydetmişti). Ek kanıt: `brain.md:981` "ADR-026 | Node.js indirme servisi", `keys.md:261` "ADR-026 | download service, architecture", `.ai/index.md:392` "I/O servisi | Node.js 20+ | **PLANNED** | ADR-026", `.ai/index.md:643` kısa biçim link kaydı — dört kayıt bu numarayı bağlar.

---

## 1. Bağlam (Context)

CoreMusic'in indirme hedefi vault'ta **çoktan yazılmış** ama kodu **hiç var olmamış** bir servistir: `coremusic_download` veritabanı 4 tabloyla şemalanmış (`coremusic_download.sql:39,76,109,141`), `user_downloads` tablosu indirme durumu/expiry alanlarını taşır (`coremusic_user.sql:196-206`), medya erişim izni ve denetimi ayrı tablolarda (`coremusic_media.sql:181` `media_access`, `:204` `media_audit` — `action` enum'u `download` içerir), API gateway indirme rotesini kaydetmiş (`Gateway.php:87`), sürüm registry'si `download` servisini tanır (`VersionRegistry.php:98`), config `download.coremusic.net :3001 Node.js + TS` der (`shared/src/Config/CLAUDE.md:50`) — ama repo kökünde `download.coremusic.net/` dizini **yok** (`Test-Path = False`), `DownloadController` sınıfı **0 dosya**, PHP'de `Content-Disposition` / `readfile` / `fpassthru` / `Range` başlığı / `X-Sendfile` **0 eşleşme**. Yani: **şema var, rota var, servis yok.** Üstelik indirme güvenliğinin dört ayrı parçası (imza, cihaz limiti, kota, abuse önleme) hiçbir tek belgede toplanmamış. Bu ADR o kararı sabitler: **erişim kontrolü → imza → stream → resume → abuse önleme** tek zincir altında, ADR-013/009/020/002 ile kenetlenerek.

### 1.1 Mevcut Durum

**A) ŞEMA / VAULT KATMANI — IMPLEMENTED (dosya var, kod yok):**

| Kayıt | Satır | İçerik |
|---|---|---|
| `.ai/.sql/mysql/coremusic_download.sql` | `:21, :39, :76, :109, :141` | `coremusic_download` DB + 4 tablo: **download_queue** (status: `queued → processing → completed \| failed`, `:64` CHECK), **download_history** (`file_size_bytes`, `:82`), **download_cache** (`file_hash`, `size_bytes`, `:109-115`), **download_sources** (kaynak başına API kimlik bilgileri — **REDACTED: credential içeriği bu ADR'ye kopyalanmaz**) |
| `.ai/.sql/mysql/coremusic_user.sql` | `:68, :196-206` | `download_over_wifi_only` tercihi; **user_downloads** — `download_status ENUM('pending','downloading','completed','failed','expired')`, `expires_at`, `file_format ENUM('mp3','flac','wav','aac','ogg')` |
| `.ai/.sql/mysql/coremusic_media.sql` | `:153-157, :181-197, :204-217` | `media_metadata` (`file_path VARCHAR(500)`), **media_access** (dosya bazlı erişim izni — paylaşım/işbirliği), **media_audit** (`action ENUM('upload','download','delete','play','share','move','copy')` — **indirme denetim izi zaten şemada**) |
| `.ai/.sql/mysql/coremusic_musics.sql` | `:108, :256-259` | `download_count`, `daily/weekly/monthly/total_downloads` sayaçları |
| `.ai/.sql/mysql/coremusic_albums.sql` | `:90-91` | `daily_downloads`, `total_downloads` |
| `.ai/.sql/mysql/coremusic_logs.sql` | `:52, :656-658` | `activity_type` enum `download`; event isimleri `DOWNLOAD_START / DOWNLOAD_COMPLETE / DOWNLOAD_FAILED` |
| `.ai/.sql/mysql/coremusic_auth.sql` | `:30` | `account_type ENUM('free','premium','studio','admin')` — **tek erişim kademe sinyali** |
| `.ai/.architecture/k15-medya-streaming/content-delivery.md` | `:8-14, :116-121, :155-157, :233, :291` | CDN mimarisi, `public → CDN'de cache'lenebilir`, **Range/slice** nginx örneği (`slice $slice_range`), `CDN_TOKEN_SECRET` token auth, "CDN entegrasyonu **aktif geliştirme aşamasında**" → **spec IMPLEMENTED, entegrasyon PLANNED** |
| `.ai/archives/prompt2-auth-2026-09-01.md` | `:91` | "medya deposu … doğrudan dosya yollarına erişim engellenmiş, **auth'tan gelen yetki anahtarlarıyla** medya akışı" — **kapalı depo + key ilkesi (spec)** |

**B) KOD KATMANI — KISMEN IMPLEMENTED (araç var, indirme yok):**

| Tarama | Sonuç | Dosya:Ssatır |
|---|---|---|
| `download` rotası | **VAR** — `'/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController']` | `shared/src/Api/Gateway.php:87` |
| `download` servis kaydı | **VAR** — `$services = ['auth','user','music','playlist','media','download']` | `shared/src/Api/Versioning/VersionRegistry.php:98` |
| `DownloadController` sınıfı | **YOK** — 0 dosya (route yalnız string kaydı) | glob `*Download*.php` → 0 |
| `download.coremusic.net` dizini (Node.js/TS servisi) | **YOK** — `Test-Path = False` (config iddiası PLANNED) | `shared/src/Config/CLAUDE.md:50` (yalnız iddia) |
| Rate limit middleware | **IMPLEMENTED** — `final class RateLimiterMiddleware`, `windowSeconds = 60`, `Retry-After` başlığı, `rate_limit_exceeded` gövdesi | `shared/src/Middleware/RateLimiterMiddleware.php:9,19,52-53` |
| Origin/referer kontrolü | **IMPLEMENTED** — `OriginCheckMiddleware` (L1, whitelist), `CorsMiddleware` ("ADR-010/022 uyumlu. Frozen sıra: OriginCheck → Cors → RateLimiter"), `SecurityHeadersMiddleware:30` `Referrer-Policy: strict-origin-when-cross-origin` | `shared/src/Middleware/OriginCheckMiddleware.php:8-15`, `CorsMiddleware.php:13,38-48`, `SecurityHeadersMiddleware.php:30` |
| RBAC / yetki | **IMPLEMENTED** — `PermissionMiddleware` (route meta `requiredRole/requiredPermission`) | `shared/src/Middleware/PermissionMiddleware.php:8-30` |
| HMAC primitifi | **IMPLEMENTED (farklı amaç)** — `hash_hmac('sha256', …)` pepper; günlük bypass key | `auth.coremusic.net/include/Domain/ValueObject/Password.php:37,53`, `auth.coremusic.net/index.php:100` |
| **İmzalı indirme URL üretimi** | **YOK** — imza üretimi/doğrulaması 0 eşleşme | `hmac|signed|signature` taraması → yalnız JWT publicKey doğrulaması (`Api/Middleware/AuthenticationMiddleware.php:101`) |
| **Content-Disposition / readfile / fpassthru / byte-range / X-Sendfile** | **YOK — 0 eşleşme** (tek `Range` eşleşmesi UI `<input type="range">`, `footer.php:70,154`) | `hmac\|readfile\|Content-Disposition\|stream_…` taraması |
| `streamUrl` alanı | **IMPLEMENTED (DTO)** — `MusicResponse` `streamUrl` üretir/taşır ama kaynağı kodlanmamış | `shared/src/Api/Dto/Response/MusicResponse.php:26,80,97,113` |
| Olay tipi | **IMPLEMENTED** — `MediaAccessedEvent` `getAccessType()` "stream, download, etc." | `shared/src/Events/Domain/MediaAccessedEvent.php:80` |
| Cihaz envanteri | **IMPLEMENTED (envanter)** — 8 cihaz tipi sabiti | `shared/src/Device/DeviceManager.php:29-44` |
| **Cihaz limiti (aktif cihaz sayısı)** | **YOK** — `maxDevices`/`MAX_DEVICES`/limit sabiti 0 eşleşme | `device.*limit\|MAX_DEVICES\|maxDevices` → 0 |
| **Satın alma / lisans tablosu** | **YOK** — `CREATE TABLE …(license\|purchase\|entitlement)` 0 eşleşme; tek kademe `account_type` enum (`free/premium/studio/admin`); `purchase_date/price` yalnız stüdyo **ekipman** kaydı (`coremusic_studio.sql:124-125`); `podcast_subscriptions` abonelik değil içerik listesi | `.ai/.sql/mysql/*.sql` taraması |

**C) DEPO / KATMAN İLİŞKİSİ — IMPLEMENTED (kural olarak):**

- **Medya deposu read-only ilkesi:** indirme servisi depoya **yazmaz, okur** — ilke `prompt2-auth-2026-09-01.md:91`'de ("kapalı depo, key ile akış") ve `content-delivery.md` CDN spec'inde yazılı; `media_audit` indirme olayını **denetim satırı** olarak ayrı DB'ye yazar (depo dosyasına değil).
- **Katman:** indirme = K14/K15 (ağ/teslim) + K8/K9 (rota/controller) sınırı; `AGENTS.md` §5 `*.php` → Backend Architect, `*.sql` → Data Engineer.

**D) KARŞILAŞTIRMA / BOŞLUKLAR — dürüst etiket:**

| İddia (görev bağlamı) | Disk kanıtı | Etiket |
|---|---|---|
| "media depo read-only" (**önce ADR-081'e atfedilmişti → şart 1b düzeltildi**) | doğru kaynak: `.ai/architecture/k15-medya-streaming/content-delivery.md` (read-only ilkesi — §1.1-A satırı); `ADR-081-multi-provider-data-sync.md` = **Multi-Provider Data Sync (Outbox+WAL)**, "media depo/read-only" başlıkta **geçmez** | ✅ **düzeltildi (şart 1b)** — referans `content-delivery.md (read-only ilkesi)` oldu; ikinci kaynak `prompt2-auth:91`; ADR-081 yalnız outbox/olay akışı (§1.4) |
| "coremusic_media.sql ADR-022'de görüldü" | ADR-022 `database-hardened-security`; medya şeması `.ai/.sql/mysql/coremusic_media.sql` diskte VAR (yukarıda satır kanıtlı) | ✅ şema VAR / ADR-022 bağlantısı `⚠️` (ADR-022 metninde bu dosyanın geçtiği satır taranmadı) |
| Lisans/erişim tabloları | `license/purchase/entitlement` tablosu **0**; `account_type` + `media_access` + `user_downloads` mevcut | **PLANNED** — lisans modeli bu ADR'nin önkoşulu §5.1/1 |
| Teslim kodu (stream/206/imza) | PHP'de **0 eşleşme**; CDN spec dosyada, entegrasyon "aktif geliştirme" | **PLANNED** |
| Cihaz limiti | `DeviceManager` envanteri var, limit **yok** | **PLANNED** |

**Sonuç etiketi:** **IMPLEMENTED:** indirme rotası/servis kaydı (Gateway:87, VersionRegistry:98), 7 indirme-ilişkili SQL şeması (download 4 tablo + user_downloads + media_access/audit + sayaçlar), rate limit (RateLimiterMiddleware:19), origin/CORS/referrer başlıkları (OriginCheck/Cors/SecurityHeaders), RBAC (PermissionMiddleware), HMAC primitifi (Password.php:37), streamUrl DTO (MusicResponse:26), CDN/Range spec (`content-delivery.md:155-157`), karar dizini slotu (`index.md:63`) + 4 katalog kaydı. **PLANNED:** `DownloadController` sınıfı, `download.coremusic.net` Node.js servisi, imzalı tek kullanımlık URL üretimi/doğrulaması, cihaz limiti, satın alma/lisans tablosu, byte-range resume mantığı, `Content-Disposition`/206 stream yanıtı, CDN entegrasyonu, hotlink/bot önleme kuralları. **`⚠️ VERIFICATION REQUIRED`:** (i) "ADR-081 = medya depo read-only" atfı → **düzeltildi (şart 1b): content-delivery.md (read-only ilkesi)** (kapandı); (ii) ADR-022 ↔ `coremusic_media.sql` bağlantısı; (iii) `download_sources` credential içerikleri (REDACTED); (iv) `content-delivery.md:291` "temel edge caching tamamlanmıştır" iddiasının kod karşılığı **0** (repo nginx/CDN config dosyası taranmadı → ölçüm §5.1/9); (v) `brain.md:981` "Node.js indirme servisi" ile bu ADR'nin "uygulama stream (PHP) + Node opsiyonel" çerçevesi §5.1/8'de hizalanır.

### 1.2 Sorun Tanımı

1. **Rota var, controller yok:** `Gateway.php:87` `downloadController`'a yönlendiriyor, sınıf 0 dosya → istek `handler` bulamaz (yanlış/eksik route).
2. **İmza yok:** doğrudan dosya yolu/kalıcı link paylaşımı mümkün; süre sınırı, tek kullanım, revokasyon yok → link sızıntısı = süresiz erişim.
3. **Cihaz limiti yok:** `DeviceManager` 8 tipi sayar ama **kaç cihazın eşzamanlı aktif** olacağı yazılı değil → hesap paylaşımı/yeniden indirme kötüye kullanımı kontrolsüz.
4. **Kota/rate limit indirme rotesine bağlı değil:** `RateLimiterMiddleware` var (`windowSeconds=60`) ama indirme endpoint'ine uygulanıp uygulanmadığı **kodlanmamış**; `daily/monthly_downloads` sayaçları var, **eşik** yok.
5. **Resume yok:** `Range`/`206`/chunk mantığı PHP'de 0 → büyük dosyalarda (FLAC/WAV) kesinti = baştan indirme; CDN spec'inde `slice` örneği var ama entegre değil.
6. **Abuse yüzeyi açık:** referer/origin kontrolü middleware'de var ama **indirme akışına özel** hotlink/bot kuralı yok; `media_audit` indirme olayını kaydeder ama **engellemez**.
7. **Lisans/erişim modeli eksik:** `account_type` enum'u var, "satın alma → indirme hakkı" tablosu yok → imza **neye göre** üretileceği belirsiz.
8. **Teslim yolu kararı yok:** uygulama stream mi, CDN mi, ikisi birden mi — `content-delivery.md` CDN der, `brain.md:981` "Node.js indirme servisi" der, kod ortada yok.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/anahtar kaynak önce (AWS S3/CloudFront docs, Google Cloud signed URL, MDN, RFC 9110, php.net, OWASP), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) secure file delivery 2025-26, (b) signed URL expiry/HMAC best practice, (c) byte-range/resume, (d) abuse/hotlink prevention, (e) CDN signed download.** Erişim: **7 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "secure file download signed URL HMAC expiry best practice 2025 short TTL single use" · (2) "HTTP byte-range resume download 206 partial content best practices large file streaming PHP memory safe chunked" · (3) "hotlink protection referer origin check download links best practice bypass proof 2025" · (4) "CDN signed URL download CloudFront signed cookies vs signed URL large file download best practice 2025" · (5) "PHP stream large file download memory safe readfile fpassthru X-Sendfile chunked buffer 2025" · (6) "concurrent device limit per account streaming service download device activation limit enforcement" · (7) "bot detection automated file download abuse prevention rate limiting fingerprint 2025 OWASP" (+8. tur: "signed download URL leakage referer header logs security token in URL short expiry mitigation") |
| Web Search **Konusu** | (1) İmzalı URL/HMAC süre sınırı, tek kullanım; (2) HTTP 206/Range ile resume ve PHP'de bellek-güvenli stream; (3) hotlink koruması — referer/origin güvenilirliği ve modern alternatifler; (4) CDN signed URL vs signed cookie, özel politika; (5) `readfile()` chunk davranışı, `fpassthru` OOM, X-Sendfile; (6) cihaz aktivasyon/eşzamanlı stream limiti endüstri standardı; (7) OWASP otomatik tehditler + bot tespiti + rate limit; (8) URL'de token sızıntısı (Referer, log). |
| Web Search **Bağlam** | **~34 adlandırılmış kaynak / 8 sorgu**: **AWS S3 presigned URL docs** (expiry 1 dk–12 sa) + **AWS Prescriptive Guidance** presigned best practices + **Google Cloud signed URLs** + security.stackexchange (single-use) + advancedweb.hu (short expiry) + StackOverflow/Reddit uygulama tartışmaları (7) · **MDN 206 Partial Content** + **RFC 9110 §15.3.7** + apidog/resultfirst/abstractapi + serverfault (mod_xsendfile 206) (6) · **AWS Security blog hotlink** (WAF+CloudFront+referer) + apeleg (modern hotlink: referer yetersiz → CORS) + TencentCloud + Cloudflare community + Netlify answers (5) · **AWS CloudFront signed URLs docs** + CloudThat + blazingcdn + Medium (signed URL vs cookie) + tutorialsdojo (5) · **php.net readfile manual** + garfieldtech + sitepoint + ronanberder + iwader (2025) + StackOverflow/Reddit/Laravel OOM issue (7) · **vdocipher** (2 streams + 5–6 device standard) + Adobe device activation limit (helpx/UVU/TeamDynamix) (4) · **OWASP Automated Threats** + fingerprint.com bot detection + F5 API bot/OWASP + Imperva + WWT (5) · PortSwigger (session token in URL) + MDN Referer + infosecwriteups/NTA leaky URLs (4). |
| Web Search **Kısa Açıklama** | **(1) İmzalı URL:** AWS S3 presigned URL **expiration 1 dakika–12 saat** aralığında ayarlanır ve süre dolunca erişim biter (AWS docs); Google Cloud signed URL aynı "time-limited access" modelini verir (GCS docs); en iyi uygulama **kısa süre + imza yalnızca gerekli alanları kapsar** (AWS Prescriptive Guidance; advancedweb.hu "short expiration helps — kalan pencere kaza yüzeyidir"); **tek kullanım** doğrudan desteklenmez → uygulama katmanında nonce/tek-use kaydı gerekir (security.stackexchange: S3 presigned single-use için "first version" yaklaşımı tartışılır). **(2) Byte-range/resume:** `Range: bytes=…` isteği **206 Partial Content** ile yanıtlanır (MDN; RFC 9110 §15.3.7) — resume ve parçalı indirme standart budur; `Content-Range` + `Content-Length` birlikte yazılır (serverfault 206 + `mod_xsendfile` örneği). **(3) Hotlink:** referer denetimi hâlâ yaygın (AWS blog: WAF+CloudFront+referer; TencentCloud) ama **referer yalnızca kliptir ve kolayca sahtelenir/boşaltılır** → modern çözüm **CORS/CORP + token** (apeleg: "Referer-based protection largely ineffective"; Netlify/Cloudflare tartışmaları) — yani referer **tek savunma değil, katman** olmalı. **(4) CDN:** CloudFront **signed URL** tek-dosya erişiminde, **signed cookie** aynı politikadaki çok-dosyalı/prefix erişiminde kullanılır (AWS CloudFront docs; CloudThat; blazingcdn); birkaç dosya → signed URL, geniş politika → cookie (Medium). **(5) PHP stream:** `readfile()` dosyayı **belleğe almaz, chunk chunk yazar** (php.net manual: "will not present any memory issues, even when sending large files"; garfieldtech; reddit r/PHP); **`fpassthru()` tüm dosyayı belleğe yükleyip OOM yapabilir** (laravel/framework#31159) → büyük dosyada kaçınılır; büyük dosyada `fopen` + chunk döngüsü / stream wrapper (sitepoint, iwader 2025) ve **X-Sendfile/nginx-acceleration** ile iş sunucuya devredilir (serverfault, StackOverflow). **(6) Cihaz limiti:** endüstri standardı **"2 eşzamanlı stream + 5–6 kayıtlı cihaz"** (vdocipher); Adobe **device activation limit** cihaz başına oturum açmayı kilitler, kullanıcı eski cihazı deactivate eder (helpx) — limit **sunucuda** uygulanır, istemcide değil (TeamDynamix: "device limit is set and enforced by Adobe"). **(7) Abuse/bot:** OWASP **Automated Threats to Web Applications** projesi geçerli işlevselliğin kötüye kullanımını kategori olarak tanımlar; tespit sonrası **rate limiting + IP engelleme** otomatik uygulanır (fingerprint.com), bot yönetimi API güvenliğinin parçasıdır (F5/Imperva); davranışsal/biometrik sinyaller cihaz parmak iziyle desteklenir (crossclassify, PMC 2025). **(8) URL sızıntısı:** URL'de taşınan token **Referer header'ında üçüncü taraf sitelere sızar** (PortSwigger; infosecwriteups; MDN) → `Referrer-Policy` + kısa TTL şart (mevcut `SecurityHeadersMiddleware:30` bunu zaten uygular). |
| Web Search **Uzun Açıklama** | **(a) İmzalı URL/HMAC (kaynak 1-7):** AWS S3 presigned URL, imzalı query (`X-Amz-Expires`, `X-Amz-Signature`) ile **zaman sınırlı** tek erişim üretir; süre 1 dk–12 sa aralığındadır ve süre dolunca istek reddedilir — "expiration between 1 minute and 12 hours". Google Cloud signed URL aynı modeli "time-limited access to a specific resource" diye tanımlar. AWS Prescriptive Guidance **presigned URL best practices** özetinde: imza **yalnız gerekli işlemi** kapsamalı, süre **en kısa işi karşılayacak** kadar tutulmalı. advancedweb.hu "short expiration helps to shorten the downtime" diyerek kısa TTL'i ana savunma sayar; Reddit/SO tartışmaları 30 dakikalık pratik pencereyi örnekler. **Tek kullanım** için sağlayıcı anahtarı yeterli değildir — security.stackexchange "single use only" sorusunda çözüm **uygulamanın kendisinin tek-use state'i** tutmasıdır (nonce/tek-seferlik id). **(b) Byte-range/resume (kaynak 8-13):** MDN, 206'nın "yalnız istenen byte aralığını" içerdiğini, `Range: bytes=0-1023` isteğine karşılık geldiğini; RFC 9110 §15.3.7 bu durum kodunu standartlaştırır. apidog/resultfirst aynı tanımı tekrarlar (≥2 çapraz ✅). serverfault'ta büyük ses dosyaları `mod_xsendfile` ile 206 verilirken `Content-Length: 0` tuzağı tartışılır → **206'da `Content-Range` + doğru `Content-Length` birlikte zorunlu**. **(c) Hotlink (kaynak 14-18):** AWS Security blog, referer kontrolünü WAF+CloudFront ile katmanlar (WAF Classic EOL Eylül 2025 — güncel WAF'a geçiş notu). apeleg 2022'den beri "referer largely ineffective" der ve **CORS/CORP header'larını** önerir; TencentCloud/Bluehost referer denetimini standart panel özelliği olarak anlatır; Cloudflare topluluğunda Google gibi üçüncü tarafların erişimi için allowlist tartışılır → **referer tek başına değil, origin/CORS + token + kota üçlüsü**. **(d) CDN (kaynak 19-23):** CloudFront signed URL tek-resource, signed cookie prefix/politika erişimidir; "few unique files → signed URL; many files same policy → signed cookie" (Medium/CloudThat) → CoreMusic'te **tek dosya indirme = signed URL**; çoklu statik varyant = cookie (opsiyonel, PLANNED). **(e) PHP stream (kaynak 24-30):** php.net `readfile()` notu büyük dosyada bellek sorunu **yaratmadığını**; ancak Laravel #31159'da `fpassthru` ile **OOM** gerçek vakası vardır → "büyük dosyada `fpassthru` yasak, chunk döngüsü/`readfile`/sendfile". ronanberder "readfile buffer'lanır ama chunk mantığı daha öngörülebilir" der; sitepoint/iwader `fopen` + döngü ve stream wrapper önerir. **(f) Cihaz limiti (kaynak 31-33):** vdocipher "industry-standard: 2 concurrent streams + 5–6 registered devices"; Adobe limiti **sunucu tarafında** uygulanır ve deactivate akışı kullanıcıya sunulur (helpx/UVU/TeamDynamix) → limit + "eski cihazı düşür" akışı. **(g) Abuse/bot (kaynak 34-38):** OWASP Automated Threats projesi (otomatik kötüye kullanım sınıflandırması), fingerprint.com "detect → rate-limit + IP block otomatik", F5/Imperva bot management OWASP Top-10 API ile ilişkilendirilir, WWT "rate limiting + anomaly detection + behavioral analysis" üçlüsünü önerir. **(h) URL sızıntısı (kaynak 39-42):** PortSwigger "URL'de token → Referer ile sızar"; infosecwriteups üçüncü taraf log örneği; MDN Referer gizlilik rehberi → `Referrer-Policy` (mevcut `SecurityHeadersMiddleware:30`) + **kısa TTL** birlikte şart. |
| Web Search **Paragraf Veri Uzun** | Secure file delivery 2025-26'da **imza + süre + kapsam** üçlüsü üzerine kuruludur: presigned/signed URL sağlayıcı anahtarıyla üretilir, süre **dakikalarla** ölçülür (AWS 1 dk–12 sa aralığı; pratik öneriler 5-30 dk), imza **yalnız hedef dosya ve işlemi** kapsar, süre dolunca geçersizdir; **tek kullanım** sağlayıcıda yoktur → uygulama **nonce/tek-use kaydı** eklemelidir (security.stackexchange). Büyük dosya teslimi **HTTP Range + 206** ile yapılır (MDN, RFC 9110 §15.3.7) — resume bu sayede mümkündür ve `Content-Range`/`Content-Length` birlikte yazılır (serverfault); PHP tarafında **`readfile()` chunk-chunk yazdığı için bellek güvenlidir** (php.net, garfieldtech, reddit), **`fpassthru()` tam dosyayı yükleyip OOM yapabilir** (Laravel #31159) → büyük FLAC/WAV'da ya chunk döngüsü ya da **X-Sendfile/nginx accel** ile sunucu-kernel devri kullanılır (serverfault, SO). Hotlink koruması **referer tek başına yetersiz** (apeleg: sahtelenebilir/boşaltılabilir) → **origin/CORS + token + kota** katmanı gerekir (AWS blog, Cloudflare); zaten `OriginCheckMiddleware` + `CorsMiddleware` + `Referrer-Policy` repo'da mevcut, indirme akışına **özel** kural eklenmelidir. CDN katmanı **opsiyoneldir**: statik/bedava varlıklar için **signed URL** (tek dosya) veya **signed cookie** (çoklu politika) (AWS CloudFront, CloudThat) — özel/ücretli içerikte imzayı **uygulama üretmeye devam** eder (imza yetkisi uygulamada kalır). Cihaz limiti endüstri standardı **~2 eşzamanlı + 5-6 kayıtlı cihaz** (vdocipher) ve **sunucuda** uygulanır (Adobe modeli: limit + eski cihazı deactivate etme). Abuse önlemi OWASP Automated Threats çerçevesinde: **rate limit (ADR-013) + anomaly + bot sinyali** (fingerprint, F5, WWT) → indirme başına kota ve sıra dışı örüntüde otomatik yavaşlatma. URL'de taşınan imza **Referer ile sızabilir** (PortSwigger, MDN) → `Referrer-Policy` + kısa TTL birlikte. |
| Web Search **Sonucu** | 1) **İmzalı URL + kısa TTL doğrulandı** (AWS S3, GCS, AWS Prescriptive Guidance, advancedweb, security.stackexchange) → **§2.2a madde 1-2**; **tek kullanım sağlayıcı anahtarı değil, uygulama nonce'ı** gerektirir → **§2.2a madde 3**. 2) **Byte-range/206 resume doğrulandı** (MDN, RFC 9110, apidog, serverfault) → **§2.2d**; `Content-Range`+`Content-Length` zorunlu. 3) **PHP bellek-güvenli stream doğrulandı** (php.net, garfieldtech, reddit vs Laravel OOM) → **§2.2b**: `readfile`/chunk döngüsü, `fpassthru` büyük dosyada yasak, X-Sendfile devri opsiyonu. 4) **Hotlink: referer tek başına yetersiz** (apeleg vs AWS/TencentCloud — çapraz gerilim işaretlendi) → **§2.2e**: referer **katman**, asıl savunma origin/CORS + token + kota (repo middleware'leri mevcut). 5) **CDN signed URL/cookie ayrımı doğrulandı** (AWS CloudFront, CloudThat, blazingcdn, Medium) → **§2.2b CDN opsiyonel PLANNED**; imza yetkisi uygulamada. 6) **Cihaz limiti endüstri standardı doğrulandı** (vdocipher 2+5-6; Adobe sunucu taraflı limit + deactivate) → **§2.2c**. 7) **Abuse/bot OWASP çerçevesi doğrulandı** (OWASP Automated Threats, fingerprint, F5, Imperva, WWT) → **§2.2e bot sinyali + ADR-013 kotası**. 8) **URL token sızıntısı doğrulandı** (PortSwigger, MDN, infosecwriteups) → **§4.3 risk 1** (kısa TTL + Referrer-Policy). **Toplam ~34 adlandırılmış kaynak, 8 sorgu**; iki çıkarım açıkça işaretlendi: **⚠️** (i) "tek kullanımlık" için **sağlayıcı mekanizması yok** (uygulama nonce'ı tasarlanacak, §5.1/3); (ii) sayfa-içi tur yapılmadığından **CDN sağlayıcı spesifik imza algoritması** ve **RateLimiterMiddleware'in indirme rotasına gerçekten bağlı olduğu** kod ölçümü olmadan doğrulanmaz. |
| Web Search **Alınan Karar** | **ADR-026 KABUL EDİLİR — İNDİRME SERVİSİ 7 MADDE:** **(a) İmzalı tek kullanımlık URL:** HMAC-SHA256 imzası, **kısa TTL 5–15 dk (varsayılan 10)**, **tek sefer** (nonce/tek-use kaydı uygulamada), imza **yalnız** `user_id + file_key + expires + nonce` kapsar; süre/nonce dolunca **410**. **(b) Cihaz limiti:** kullanıcı başına **aktif cihaz sınırı** (varsayılan **5 kayıtlı / 2 eşzamanlı aktif** — §1.3-6 endüstri standardı; `DeviceManager` envanterine bağlanır), limit sunucuda uygulanır, eski cihaz deactivate akışı. **(c) Hız/kota:** indirme endpoint'i **ADR-013 rate limit**'e bağlanır (`RateLimiterMiddleware` penceresi) + kullanıcı/dosya bazlı **günlük/aylık kota** (`daily/monthly_downloads` sayaçları eşikle çalışır). **(d) Resume:** **HTTP Range → 206** (`Content-Range`, `Content-Length`), chunk chunk **memory-safe** akış (`readfile`/chunk döngüsü; `fpassthru` büyük dosyada yasak); kesinti sonrası kaldığı yerden devam. **(e) Abuse önleme:** **origin/CORS (mevcut middleware) + referer kontrolü (katman olarak) + hotlink token'ı + bot/oran-örüntü tespiti** (OWASP Automated Threats; ADR-013 ile birlikte). **(f) Teslim yolu — uygulama stream + CDN opsiyonel:** imza **uygulama üretir** → **sunucu stream eder** (`Content-Disposition: attachment`, 206); **CDN opsiyonel ve PLANNED** — statik/bedava varlıklar için signed CDN URL; büyük dosya için sabit buffer'lı chunked akış. **(g) Erişim kontrolü:** satın alma/lisans doğrulama → imza üret (şu an yalnız `account_type` + `media_access` var → lisans tablosu önkoşul); **depo read-only** — indirme yalnız okur, denetim `media_audit`/outbox'a yazılır. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: imzalı URL/süre (8), Range/206 (6), hotlink (5), CDN imza (5), PHP stream (7), cihaz limiti (4), bot/OWASP (5), URL sızıntısı (4) → **~34 adlandırılmış kaynak, 8 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada karşılanır (tek istisna işaretlendi: hotlink'te "referer yetersiz" (apeleg) ile "referer etkili" (AWS/TencentCloud) gerilimi → **referer katman** olarak çözüldü). **Vault tarafı aynı resmi verdi:** şema + rota + ara katmanlar **IMPLEMENTED**, imza/cihaz limiti/lisans/resume/controller **PLANNED** → bu ADR **mimari karardır, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (34):** 1) docs.aws.amazon.com — S3 presigned URL (expiry 1 dk–12 sa) · 2) docs.aws.amazon.com — presigned URL best practices · 3) docs.cloud.google.com — signed URLs · 4) security.stackexchange — presigned single use · 5) advancedweb.hu — S3 signed URL security (kısa expiry) · 6) community.forwardnetworks — presigned 30 dk · 7) docsie — expiring download links · 8) **developer.mozilla.org — HTTP 206** · 9) **thestatuscode.com — RFC 9110 §15.3.7** · 10) apidog — 206 Partial Content · 11) resultfirst/abstractapi — 206 · 12) serverfault — 206 + mod_xsendfile · 13) linkedin/stackoverflow — Range bytes=0-1023 · 14) **aws.amazon.com/blogs/security — hotlink (WAF+CloudFront+referer)** · 15) apeleg — modern hotlink (referer ineffective → CORS) · 16) tencentcloud — hotlink referer check · 17) cloudflare community — hotlink prevention · 18) answers.netlify — hotlink 2025 · 19) **docs.aws.amazon.com — CloudFront signed URLs** · 20) cloudthat — signed URL vs cookie · 21) blazingcdn — signed URL/token auth · 22) medium — signed URL vs cookies pratik · 23) tutorialsdojo — CloudFront signed URL/cookie · 24) **php.net — readfile() manual** · 25) garfieldtech — readfile not harmful · 26) sitepoint — big files PHP · 27) ronanberder — serve big files · 28) **github laravel#31159 — fpassthru OOM** · 29) reddit r/PHP — readfile chunk · 30) iwader — file streams (2025) · 31) **vdocipher — concurrent stream limit (2 + 5-6 device)** · 32) **helpx.adobe.com — device activation limit** · 33) UVU/TeamDynamix — Adobe limit sunucu taraflı · 34) **owasp.org — Automated Threats** · 35) fingerprint.com — bot detection 2026 · 36) f5.com — API bot/OWASP · 37) imperva — bot protection · 38) wwt — OWASP automated threats · 39) **portswigger.net — session token in URL (Referer)** · 40) **mdn — Referer header privacy** · 41) infosecwriteups — token leakage via referer · 42) nta — leaky URLs. *(Liste 42 girdi sayılır çünkü aynı sorgu grubundaki çapraz aynalar ayrı adlandırılmıştır; benzersiz domain ~34.)* |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-013-rate-limiting-apcu]] | **Hız/kota bağlayıcısı:** indirme endpoint'i bu ADR'nin APCu penceresine (`RateLimiterMiddleware.php:19` `windowSeconds=60`) bağlanır; ayrı/kopya rate limit **kurulmaz**. Kota sayaçları (`daily/monthly_downloads`) bu mekanizmanın üstü eşiği taşır. |
| [[ADR-009-clean-url-redirect]] | **Redirect/referer uyumu:** indirme zincirinde redirect tek atış (ADR-009 "1 hop = güvenli"); imzalı query **normalize edilmez/sıralanmaz** (ADR-016 ruhu — imza bozulmasın); referer/origin kontrolü ADR-009'un temiz URL/kanonik disiplinine uyar. |
| [[ADR-020-api-public-security]] | **Public yüzey:** indirme endpoint'i public/API-açık katmanda değil; oturum + CSRF + origin doğrulaması zorunlu (frozen sıra: OriginCheck → Cors → RateLimiter, `CorsMiddleware.php:13`). |
| [[ADR-002-pdo-mandatory-no-orm]] | **Erişim katmanı:** satın alma/lisans/kota/nonce sorguları PDO prepared statement; ORM, `SELECT *`, string concat yasak. |
| [[ADR-022-database-hardened-security]] | **Şema sertliği:** `coremusic_download` 4 tablo + `user_downloads` sorguları hardened kurallarına uyar; migration `ADR-014` (expand-contract, forward-only) ile. |
| [[ADR-081-multi-provider-data-sync]] | **Olay akışı:** `DOWNLOAD_START/COMPLETE/FAILED` (`coremusic_logs.sql:656-658`) olayları outbox ile akar; indirme **depo dosyasına yazmaz**, denetim satırını outbox/audit'e yazar. **Düzeltme (şart 1b — uygulandı):** "media depo read-only" ilkesi **content-delivery.md (read-only ilkesi)** + `prompt2-auth:91` kaynağından alınır; ADR-081 yalnız outbox/olay akışı içindir (§1.1-D). |
| Depo read-only (medya) | İndirme işlemi medya deposundan **yalnız okur**; yazma yalnız `media_audit`/`user_downloads`/sayaç DB'lerine (ayrı domain — ADR-003/081 ruhu). |
| [[ADR-005-ultrathink-protocol]] | Kod/vault kanıtı olmayan her iddia etiketli: controller **0 dosya**, imza **0 eşleşme**, cihaz limiti **0**, lisans tablosu **0**, `download.coremusic.net` **dizin yok** → `⚠️ VERIFICATION REQUIRED`. |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı: bu ADR'deki her `[[…]]` diskte var (§6'da doğrulanır); şablon zorunluluğu (Guardrail #16) — `.templates/adr/adr-template.md` iskeleti (7 bölüm + §1.3 9 alan). |
| In-Place Refactoring | Dosya adları **değiştirilmez**: `content-delivery.md`, `coremusic_download.sql`, `Gateway.php` vb. yalnız okunur/ekleme yapılır; `Gateway.php:87`'ye yeni rota yazımı §5.1/2'de ayrı adım. |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil**. |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi, `vault-utf8-writer append`). |
| REDACTED | `download_sources` tablosundaki API credential'ları, `CDN_TOKEN_SECRET` değeri, `.ai/keys.md` içeriği bu ADR'ye **kopyalanmaz**; imza anahtarı yalnız `.env`'de yaşar. |

---

## 2. Karar (Decision)

**CoreMusic indirme servisi YEDİ maddeyle bağlayıcı ilan edilir:**

**(a) İMZALI TEK KULLANIMLIK URL:** her indirme isteği, erişim kontrolü geçtikten sonra **HMAC-SHA256 ile imzalanmış** bir URL üretir. İmza **yalnız** `user_id + file_key + expires + nonce` kapsar (dosya yolu/query dışı alan imzaya girmez). **TTL kısa: 5–15 dk (varsayılan 10 dk)** — §1.3-1 (AWS 1 dk–12 sa aralığı; kısa expiry en iyi uygulama). **Tek sefer:** sağlayıcı anahtarı tek-use sağlamaz → **nonce/tek-use kaydı uygulamada** tutulur (APCu, ADR-013 ile aynı katman); ikinci kullanım **410 Gone**. Süre dolunca/nonce kullanınca imza geçersiz; yenisi ancak erişim kontrolü tekrar geçilerek üretilir.

**(b) CİHAZ LİMİTİ:** kullanıcı başına **kayıtlı cihaz sınırı = 5**, **eşzamanlı aktif indirme = 2** (varsayılan; §1.3-6 endüstri standardı "2 concurrent + 5–6 device"). Sınır **sunucuda** uygulanır (istemci beyanı sayılmaz); `DeviceManager` cihaz envanterine (`DeviceManager.php:29-44`) bağlanır, `devices` tablosu kayıt tutar. Limit dolunca kullanıcı **en eski/pasif cihazı deactivate** ederek yeni cihaz ekler (Adobe modeli, §1.3-6).

**(c) HIZ/KOTA LİMİTİ:** indirme endpoint'i **[[ADR-013-rate-limiting-apcu]]'ye bağlanır** — `RateLimiterMiddleware` penceresi (60 sn) indirme için de geçerlidir; ayrıca **kullanıcı + dosya bazlı günlük/aylık kota** (`coremusic_musics.sql:256-259` `daily/monthly_downloads` sayaçları eşikle çalışır; kullanıcı tercihi `download_over_wifi_only` (`coremusic_user.sql:68`) saygılıdır). Kota aşımında **429 + Retry-After**, sayaçlar `log.md`/DB'ye yazılır.

**(d) RESUME — BYTE-RANGE + CHUNK:** istemci `Range` başlığı gönderir, sunucu **206 Partial Content** ile `Content-Range` + `Content-Length` üretir (§1.3-2: MDN, RFC 9110 §15.3.7). Akış **memory-safe**: sabit boyutlu buffer ile chunk döngüsü / `readfile` (§1.3-5); **`fpassthru` büyük dosyada yasak** (OOM, Laravel #31159). Kesinti sonrası kaldığı yerden devam; `download_history` parçalı tamamlanmayı kaydeder.

**(e) ABUSE ÖNLEME:** dört katman — (1) **origin/CORS kontrolü** (mevcut `OriginCheckMiddleware` + `CorsMiddleware` — frozen sıra korunur), (2) **referer kontrolü katman olarak** (tek savunma değil — §1.3-3 gerilimi: apeleg "yetersiz" ↔ AWS "etkili" → ikisi de katman), (3) **hotlink koruması** — imzalı URL + `Content-Disposition: attachment` + kısa TTL hotlink'i zaten kırar; ek olarak `Referrer-Policy` (mevcut `SecurityHeadersMiddleware:30`) URL sızıntısını kapatır (§1.3-8), (4) **otomatik indirme botu tespiti** — OWASP Automated Threats çerçevesinde oran/örüntü sinyali (ardışık istek aralığı, kota hızı, eksik istemci sinyalleri) → ADR-013 rate limit ile **otomatik yavaşlatma/engelleme** (§1.3-7).

**(f) TESLİM YOLU — UYGULAMA STREAM + CDN OPSİYONEL:** **imzayı uygulama üretir** (imza yetkisi uygulamada kalır) → **sunucu stream eder**: `Content-Disposition: attachment; filename="…"` + byte-range **206** + sabit buffer'lı chunked akış (büyük dosya memory-safe). **CDN opsiyonel ve PLANNED (dürüst etiket):** `content-delivery.md` CDN mimarisini tarif eder (:8-14, :155-157 slice/Range) ve "aktif geliştirme" der (:291) ama repo'da **CDN/nginx entegrasyon kodu 0** → CDN yalnız **statik/bedava varlıklar** için (imzasız ya da uzun TTL'li publik içerik) **signed CDN URL** (CloudFront/Cloudflare benzeri, §1.3-4) ile; **özel/ücretli içerikte stream yolu asıldır** (§4.4 fallback 1).

**(g) ERİŞİM KONTROLÜ + DEPO READ-ONLY:** zincir: **oturum/CSRF/origin doğrulama → satın alma/lisans doğrulama → cihaz limiti → kota → imza üret → stream**. Lisans modeli şu an **yok** (`license/purchase` tablosu 0, yalnız `account_type` enum) → **önkoşul**: lisans/satın alma tablosu eklenir veya `account_type` + `media_access` (`coremusic_media.sql:181`) ile başlanır; **bu ADR imza mekanizmasını bağlar, lisans şemasını değil** (şema ADR-003/022 alanı, §5.1/1). Depo **read-only**: indirme medya deposundan **yalnız okur**; yazım yalnız `user_downloads`/`media_audit`/sayaç/outbox'a (ayrı domain).

### 2.1 Neden Bu Seçenek?

- **Kalıcı link = süresiz erişim:** depo "kapalı, key ile akış" ilkesi (`prompt2-auth:91`) kalıcı linkle çöker → **imza + kısa TTL** tek başına çözer (§1.3-1: AWS/GCS "time-limited access"; advancedweb "short expiration").
- **Tek kullanım uygulama işi:** sağlayıcı presigned URL'i tekrarlı kullanıma izin verir (security.stackexchange) → **nonce kaydı** şart; aksi halde "tek kullanımlık" iddiası yalan olur (ADR-005).
- **Cihaz limiti olmadan hesap paylaşımı denetlenemez:** endüstri standardı 2 eşzamanlı + 5-6 cihaz (vdocipher) ve limit **sunucuda** tutulur (Adobe) → `DeviceManager` envanteri + `devices` tablosu zaten var, **sayaç/sınır** eklenir.
- **Kota tek başına yetmez, rate limit tek başına yetmez:** `daily_downloads` sayacı biriktirir ama **hızı** durdurmaz; `RateLimiterMiddleware` hızı durdurur ama **toplam hakkı** bilmez → ikisi birlikte (ADR-013 + sayaç eşiği).
- **Resume zorunlu çünkü format:** FLAC/WAV büyük dosyalardır (`file_format` enum `coremusic_user.sql:203`); `Range/206` standart ve ücretsizdir (MDN, RFC 9110) — `download_cache.file_hash` (`coremusic_download.sql:109`) parçalı bütünlük kontrolüne hazır.
- **Bellek güvenliği kırılgan:** Laravel `fpassthru` OOM vakası gerçek (§1.3-5) → **chunked + sabit buffer** ve mümkünse **X-Sendfile/nginx accel** devri (serverfault) — PHP süreci CPU/bellek yerine kernel'e bırakır.
- **Referer tek savunma değil:** modern tespit referer'i atlatır (apeleg) → origin/CORS (repo'da **mevcut**) + imza + kota + bot sinyali katmanları birlikte; mevcut middleware'lere **yeni altyapı kurulmaz** (YAGNI).
- **CDN neden opsiyonel:** imza/yetki uygulamadadır; CDN'e özel içerik imzasını devretmek **güvenlik yetkisi devri** demektir (ek ADR ister). Statik/bedava varlıkta CDN ücretsiz ölçek sağlar (ADR-007 L3 HTTP cache ruhu) → **opsiyonel + PLANNED** etiketi dürüstlük şartı.
- **Depo read-only:** indirme bir **okuma** işlemidir; yazma denetim DB'lerine gider → depo bütünlüğü + ADR-003/081 tek-yazıcı ruhu korunur.

### 2.2 Teknik Detaylar

**a) İmza şeması (HMAC + TTL + tek-use):**

```textc
// İmza girdisi (yalnız bu 4 alan — dosya yolu/sorgu dışı alan imzaya girmez)
payload = user_id | file_key | expires (unix ts) | nonce (16B base64url)
sig     = base64url( HMAC-SHA256( DOWNLOAD_SIGNING_KEY, payload ) )
url     = https://home.coremusic.net/dl/{file_key}?e={expires}&n={nonce}&s={sig}
```

| Alan | Değer | Kaynak |
|---|---|---|
| Algoritma | **HMAC-SHA256** | §1.3-1 (AWS/GCS imza modeli); repo HMAC primitifi mevcut (`Password.php:37`) |
| TTL | **5–15 dk, varsayılan 10 dk** | §1.3-1 (AWS 1 dk–12 sa; kısa expiry) |
| Tek kullanım | **nonce = tek sefer** (APCu'da `dl:nonce:{n}` → kullanıldı işareti; ikinci istek **410**) | §1.3-1 (tek-use uygulama işi) |
| Kapsam | `user_id + file_key + expires + nonce` | §1.3-1 (dar kapsama) |
| Anahtar | yalnız `.env` (REDACTED); rotasyon = yeni anahtar + 5 dk eski pencere | §1.4 REDACTED |
| Reddedilme | süre doldu → **410**; nonce tekrarı → **410**; imza uyuşmazlığı → **403 + ERROR log** | §1.3-8 (kısa TTL) |
| URL hijack | `Referrer-Policy: strict-origin-when-cross-origin` (mevcut) + asla GET log'a tam URL yazma (query maskelenir, ADR-016/005 ruhu) | §1.3-8 |

**b) Teslim yolu (uygulama stream — asıl; CDN opsiyonel — PLANNED):**

```
İstemci → GET /dl/{file_key}?e&n&s
  → [1] OriginCheck → Cors → RateLimiter (ADR-013/020 frozen sıra)      (IMPLEMENTED)
  → [2] Oturum + CSRF + lisans/satın alma doğrulama + cihaz limiti + kota  (lisans: PLANNED §5.1/1)
  → [3] İmza + nonce doğrulama (süre/tek-use)                              (PLANNED §5.1/3)
  → [4] Sunucu stream: Content-Disposition: attachment; Range → 206         (PLANNED §5.1/4)
        chunk döngüsü, sabit buffer (ör. 64 KB); fpassthru YASAK
        (opsiyonel: X-Sendfile / nginx accel ile kernel'e devir)
  → [5] Sayaç + media_audit + DOWNLOAD_COMPLETE olayı (outbox)             (şema IMPLEMENTED, yazan kod PLANNED)
```

| Öğe | Karar | Durum |
|---|---|---|
| İmza üretimi | **Uygulama** (PHP controller/service) | PLANNED |
| Stream | **Uygulama sunucusu** — 206 + `Content-Disposition: attachment` + `Accept-Ranges: bytes` | PLANNED |
| Buffer | **Sabit boy chunk döngüsü** (memory-safe; `readfile`/fread döngüsü) | PLANNED |
| Büyük dosya | `fpassthru` yasak; mümkünse **X-Sendfile/nginx accel** devri | PLANNED (§1.3-5) |
| CDN | **Opsiyonel, PLANNED** — yalnız **statik/bedava varlık**: signed CDN URL (uzun TTL yalnız imzasız publik içerikte); özel içerikte CDN yalnız **byte-proxy** (imza uygulamada doğrulanır, CDN imzayı üretmez) | PLANNED (`content-delivery.md` spec var, kod 0) |
| ETag/bütünlük | `download_cache.file_hash` ile chunk bütünlüğü doğrulaması | Şema var / kod PLANNED |

**c) Cihaz limiti + kota:**

| Kural | Varsayılan | Uygulama yeri | Durum |
|---|---|---|---|
| Kayıtlı cihaz / kullanıcı | **5** | `devices` tablosu + `DeviceManager` envanteri | Envanter IMPLEMENTED / **sınır PLANNED** |
| Eşzamanlı aktif indirme | **2** | oturum-bazlı sayaç (APCu) | PLANNED |
| Limit aşımı | 429 + "cihaz sınırı — eski cihazı kaldır" akışı | controller | PLANNED |
| Günlük/aylık kota | eşiği `account_type` belirler (free/premium/studio/admin — `coremusic_auth.sql:30`) | `daily/monthly_downloads` sayaçları | Şema IMPLEMENTED / **eşik PLANNED** |
| Rate limit | 60 sn pencere, `Retry-After` | `RateLimiterMiddleware.php:19,53` | **IMPLEMENTED** (indirme rotasına bağlama PLANNED) |
| WiFi-only tercih | `download_over_wifi_only` saygılıdır (istemci bildirimi) | `coremusic_user.sql:68` | Şema var |

**d) Resume (byte-range + chunk):**

| İstek | Yanıt | Not |
|---|---|---|
| `Range: bytes=N-` | **206** + `Content-Range: bytes N-{size-1}/{size}` + `Content-Length` | §1.3-2 zorunlu ikili |
| `Range: bytes=0-0` | 206 + 1 bayt (`Accept-Ranges` keşfi) | MDN |
| Range yok | **200** tam gövde (chunked stream) | RFC 9110 |
| Geçersiz aralık | **416** + `Content-Range: bytes */{size}` | standart |
| Şema | `download_history` + `download_cache.file_hash` | `coremusic_download.sql:76,109` |
| Bellek | sabit buffer chunk; `fpassthru` yasak (OOM) | §1.3-5 |

**e) Abuse önleme katmanları (repo middleware'leri üzerine):**

| # | Katman | Mekanizma | Durum |
|---|---|---|---|
| 1 | Origin/CORS | `OriginCheckMiddleware` (L1 whitelist) → `CorsMiddleware` → `RateLimiter` frozen sıra | **IMPLEMENTED** |
| 2 | Referer (katman) | indirme GET'inde beklenen referer aralığı; boş/anomali → **sinyal, tek başına block değil** | PLANNED |
| 3 | Hotlink | imza + kısa TTL + `Content-Disposition: attachment` + `Referrer-Policy` (mevcut header) | Karar (a)/(f); header IMPLEMENTED |
| 4 | Bot/oran-örüntü | ardışık indirme aralığı, kota hızı, eksik istemci imzaları → ADR-013 yavaşlatma + `log.md` ERROR | PLANNED (OWASP Automated Threats, §1.3-7) |
| 5 | Denetim | `media_audit` (`action='download'`) + `DOWNLOAD_*` olayları → inceleme/geri alma | Şema IMPLEMENTED |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Kalıcı (imzasız) indirme linki** | En basit, önbelleklenebilir, paylaşıma açık | Süresiz erişim; link sızıntısı = dosya kaybı; revokasyon yok; "kapalı depo + key" ilkesini (`prompt2-auth:91`) çiğner | §1.3-1 süre sınırı/krallık: kısa TTL + imza endüstri standardı (AWS/GCS); depo read-only ilkesi kalıcı linki reddeder |
| 2 | **Yalnız oturum doğrulaması (imza yok, cookie ile stream)** | URL'de token yok, Referer sızıntısı riski sıfır | Kesintili oturumda resume kırılır; proxy/CDN/istek paylaşımı zor; API/BFF dışı doğrudan erişim kapalı; çoklu-sekme/kuyruk zor | Imza, oturumdan **bağımsız** kısa ömür erişim sağlar (§1.3-1); ikisi birlikte kullanılır — bu alternatif **tek başına** elenir |
| 3 | **Tam CDN-öncelikli teslim** (imza dahil CDN'e devredilir) | Global ölçek, origin yükü düşük | İmza yetkisi/süre politikası **CDN sağlayıcısına** devredilir; imza algoritması + lisans kontrolü CDN'e kilitlenir; repo'da CDN entegrasyonu **0 kod** (`content-delivery.md:291` iddiası kanıtsız) | Güvenlik yetkisi uygulamada kalmalı (§2.2b); statik/bedava varlıkta CDN **opsiyonel** kalır — tam bağımlılık erken |
| 4 | **Cihaz limiti yok / yalnız istemci tarafı sayım** | Sıfır ek kod, kullanıcı dostu görünüm | Hesap paylaşımı ve toplu indirme engellenemez; istemci beyanı sahtelenebilir (§1.3-6: limit "enforced by Adobe" = sunucu) | Endüstri standardı sunucu-taraflı limit (vdocipher, Adobe) — kullanıcı onayı (b) |
| 5 | **Sadece rate limit, kota/cihaz yok** | Mevcut `RateLimiterMiddleware` ile hızlı başlar | Hızı durdurur ama **toplam hakkı** bilmez; günlük indirme patlaması (`daily_downloads` sayaçları boşa) | Kota + cihaz limiti ayrı haklar; ikisi de kullanıcı onaylı karar (b)/(c) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek karar beş boşluğu kapatır:** imza, cihaz limiti, kota, resume, abuse — daha önce hiçbir belgede birlikte yoktu (§1.2).
- **Mevcut altyapı yeniden kullanılır:** `OriginCheck/Cors/RateLimiter/Permission` (IMPLEMENTED) + HMAC primitifi + 7 SQL şeması + `streamUrl` DTO → **yeni kurulum yok**, bağlama işi.
- **Büyük dosya dayanıklılığı:** Range/206 resume ile FLAC/WAV kesintisi kaldığı yerden devam; memory-safe chunk sunucuyu OOM'den korur (§1.3-5).
- **Denetim izi şemada hazır:** `media_audit(action='download')` + `DOWNLOAD_START/COMPLETE/FAILED` olayları + `download_history` → kötüye kullanım incelemesi mümkün.
- **Depo bütünlüğü:** read-only indirme + denetim yazımı ayrı DB'ye → medya dosyaları değişmez (ADR-003/081 tek-yazıcı ruhu).
- **CDN'e kilitlenmedik:** imza uygulamada kaldığı için CDN sonradan eklenebilir (PLANNED) — erken bağımlılık yok.
- **Ölçülebilir:** kota/cihaz/kota-429 sayaçları `log.md`'ye sayıyla yazılır (ADR-005).

### 4.2 Olumsuz Sonuçlar

- **Kod yazımı tamamen önümüzde:** `DownloadController` **0 dosya**, `download.coremusic.net` **dizin yok**, imza **0 eşleşme** → bu ADR **mimari karardır**; servis "var" sayılmaz (§1.1-B).
- **Lisans şeması önkoşul olarak açık:** `license/purchase` tablosu yok → imza **neye göre** üretilecek belirsiz; `account_type` geçici çözüm, "satın alma → hak" modeli ayrı şema işi (§5.1/1).
- **Ek state yükü:** nonce (tek-use) + cihaz sayacı + kota sayacı = 3 yeni sayaç → APCu/DB yazım yükü ve TTL kırılganlığı (ADR-007 TTL kuralları).
- **Uygulama stream'i CPU/bant genişliği taşır:** CDN yokken origin her baytı taşır → büyük eşzamanlılıkta origin şişer (§4.3/3).
- **CDN PLANNED →** statik varyantlarda performans avantajı henüz yok; `content-delivery.md` "tamamlandı" satırları kanıtsız kalır (§1.1-D iv).
- **Debate tamamlandı ama şartlar açık:** 3 tur / 20 persona → **18/2/0 KABUL** (§5.3); 3 şarttan **1a/1b uygulandı**, şart **2** (controller/imza/resume) ve şart **3** (cihaz limiti + lisans + test) **PLANNED** kaldı → kod işi §5.1'de duruyor.

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **URL sızıntısı:** imza URL Referer/log/paylaşım ile üçüncü taraflara sızar (§1.3-8 PortSwigger/MDN) | 3 (Olası) | 4 (Yüksek) | **Kısa TTL 5–15 dk** + tek-use nonce (§2.2a) + mevcut `Referrer-Policy` (`SecurityHeadersMiddleware:30`) + log'da query **maskelenir** (ADR-016/005) |
| 2 | **İmza süresi kötü ayar:** TTL uzarsa (saatler) sızıntı penceresi açılır; çok kısarsa (alt 1 dk) meşru indirme kırılır (§1.3-1 AWS 1 dk–12 sa aralığı) | 3 (Olası) | 3 (Orta) | **5–15 dk bandı** sabit; süre doldu → yeniden imza akışı (410 + yeniden istek); TTL'i konfigürasyonda ADR-007 config TTL (300 sn) ile **ayrı** tut |
| 3 | **Resume abuse / origin şişmesi:** çoklu Range kuyruğu + eşzamanlı 206 akışları origin CPU/bantini tüketir; chunk zinciri bellek değil ama **bağlantı** tutar | 3 (Olası) | 4 (Yüksek) | Cihaz limiti (2 eşzamanlı, §2.2c) + ADR-013 rate limit + eşzamanlı stream sayacı; origin şişerse **§4.4 fallback 1** (CDN statik varyantı) |
| 4 | **Stream CPU/bellek (fpassthru OOM):** yanlış API ile büyük FLAC'ta PHP süreci çöker (§1.3-5 Laravel #31159) | 2 (Mümkün) | 4 (Yüksek) | **`fpassthru` yasak** (§2.2b) + sabit buffer chunk + mümkünse X-Sendfile/nginx accel devri + kademeli yükleme testi (§5.1/5) |
| 5 | **Bot kötüye kullanımı:** otomatik toplu indirme kota doldurur / kaynağı boşaltır (§1.3-7 OWASP Automated Threats) | 4 (Çok olası) | 3 (Orta) | Dört katmanlı abuse (§2.2e) + dosya/kullanıcı kotası (§2.2c) + `DOWNLOAD_*` olay örütü incelemesi + ADR-013 otomatik yavaşlatma |
| 6 | **Lisans modeli olmadan imza:** "herkes premium'a indirir" — `account_type`/`media_access` yetersiz kalırsa hatalı yetkilendirme (OWASP A01 benzeri) | 3 (Olası) | 4 (Yüksek) | **Önkoşul §5.1/1** (lisans/satın alma şeması veya `media_access` ile başlangıç) + `PermissionMiddleware` RBAC + **fail-closed**: lisans belirsizse imza **üretilmez** |
| 7 | **CDN PLANNED iddiasının kanıtsızlığı:** spec "tamamlandı" der, kod 0 → yanlış "üretimde" varsayımı | 4 (Çok olası) | 2 (Düşük) | Etiket disiplini: CDN **PLANNED** (§2.2b, §1.1-D); ölçüm §5.1/9; `content-delivery.md` durum satırı In-Place notla düzeltilir |
| 8 | **ADR-081 "read-only depo" eşleşmez:** yanlış çapraz referans gelecekte yanlış güven üretir | 3 (Olası) | 2 (Düşük) | `⚠️ VERIFICATION REQUIRED` (§1.1-D) + ilke `prompt2-auth:91`/`content-delivery.md` kaynağına bağlandı; **düzeltme UYGULANDI (şart 1b): content-delivery.md (read-only ilkesi)** (§1.1-D, §5.5) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Erişim modelini yaz:** `license/satın alma` şeması **veya** başlangıç olarak `account_type` + `media_access` (`coremusic_media.sql:181`) ile yetkilendirme matrisi (hangi kademe kaç indirme) — **fail-closed** kuralı (`ADR-022`/`ADR-002` PDO prepared) | Data Engineer + Security Engineer | 3 gün |
| 2 | **`DownloadController` yaz** (rota `Gateway.php:87`'de hazır): oturum + CSRF + origin + lisans + cihaz + kota kontrolü → imza üret → yanıt (`PLANNED` — sınıf 0 dosya) | Backend Architect | 1 hafta |
| 3 | **İmza modülü:** HMAC-SHA256, TTL 5–15 dk, **nonce tek-use kaydı** (APCu, `dl:nonce:{n}`), 410/403 davranışı, `.env` anahtarı (REDACTED) — §2.2a | Security Engineer + Backend Architect | 3 gün |
| 4 | **Stream/206 yazımı:** `Content-Disposition: attachment`, `Accept-Ranges`, `Range → 206/416`, `Content-Range`, **sabit buffer chunk döngüsü**; `fpassthru` kod incelemesinde **yasaklı örüntü** (§2.2b) | Backend Architect | 3 gün |
| 5 | **Büyük dosya testi:** ≥1 GB WAV/FLAC üzerinde eşzamanlı 2 indirme → bellek profili sabit (limit aşımı yok); mümkünse nginx `X-Accel-Redirect`/X-Sendfile devri değerlendirilir | QA Engineer + DevOps | 2 gün |
| 6 | **Cihaz limiti + kota:** `DeviceManager` envanterine `maxDevices=5` / `maxConcurrent=2` sınırı; `daily/monthly_downloads` eşiği + 429/Retry-After; WiFi-only tercihine saygı | Backend Architect + Data Engineer | 3 gün |
| 7 | **Abuse katmanları:** indirme rotesine ADR-013 rate limit **bağlantısı** (frozen sıra korunur), referer katmanı (sinyal), bot örüt sinyali → `log.md` ERROR; `media_audit` + `DOWNLOAD_*` olay yazımı (outbox — ADR-081 ruhu) | Security Engineer + Backend Architect | 3 gün |
| 8 | **Katalog senkronu:** `brain.md:981` "ADR-026 \| Node.js indirme servisi" → bu ADR'nin "uygulama stream + Node/CDN opsiyonel" çerçevesiyle hizalanır; `.ai/index.md:392` PLANNED satırı korunur; `keys.md:261` anahtarları güncellenir; **read-only depo referansı** bu ADR içinde **content-delivery.md (read-only ilkesi)** olarak düzeltildi (şart 1b — UYGULANDI, 2026-09-25) | MO (vault-updater) | 0.5 gün |
| 9 | **CDN kapısı (opsiyonel):** statik/bedava varlıklar için signed CDN URL pilotu — ancak §5.1/2-4 kapanınca; `content-delivery.md` durum satırı In-Place notla hizalanır | DevOps + Backend Architect | 2 gün (PLANNED) |
| 10 | **Doğrulama:** şablon tutamağı taraması (`ADR-026`, hedef 0) · wiki-link disk kontrolü (§6) · `vault-utf8-writer scan` (mojibake 0) · `index.md:63` slug eşleşmesi · placeholder 0 · `log.md` append 1 satır | Vault Steward | 0.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (a) imza vazgeçilirse → **oturum + lisans doğrulamalı düz stream** (referer/origin/kota katmanları kalır) — ama "kapalı depo" ilkesi zayıflar, yalnız bilinçli kararla; (b) cihaz limiti kalkarsa → sayaçlar pasif kalır, kod silinmez (`maxDevices = PHP_INT_MAX` konfigürasyonu ile kapatılır); (c) kota kalkarsa → yalnız rate limit kalır (§4.2'de açıkça belirtilen toplam-hak kaybı); (d) resume kalkarsa → `Accept-Ranges: none`, tam gövde 200 (istemci baştan indirir); (e) CDN pilotu vazgeçilirse → §2.2b'de zaten **opsiyonel**, hiçbir yol buna bağlı değil.

**Tam geri dönüş:** dosya adları/rota değişmediği için (In-Place Refactoring korundu) geri dönüş = (1) `DownloadController` rota kaydı `Gateway.php:87`'de **korumalı kalır** ama handler boş/düşürülür → 501 (dosya adı silinmez); (2) imza modülü devre dışı (`DOWNLOAD_SIGNING_ENABLED=false`) → oturum-yolu stream'e düşer; (3) nonce/cihaz/kota sayaçları APCu TTL ile kendiliğinden söner (ADR-007); (4) `vault-utf8-writer` yedeği (`<file>.bak`) eski içeriği verir; (5) `.ai/log.md`'ye tek satır revert append'i; (6) `.ai/.decisions/index.md:63` satırı `status: reverted` olur; (7) **bu ADR düzenlenmez** — `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** `log.md` append-only geçmiş, karar dizini satırı ve 7 SQL şeması **bozulmaz**; kod yok olduğu için şu an geri döndürülecek tek şey **mimari çerçevedir**.

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| 1 | 20 persona — kanıt denetimi (kod/şema/middleware) | ✅ | **IMPLEMENTED:** rota (`Gateway.php:87`), 4 tablo şema (`coremusic_download.sql`), rate/origin/cors middleware · **PLANNED (=0):** `DownloadController`, imzalı URL, resume, `readfile`/`Range`, cihaz limiti, lisans tablosu · `download.coremusic.net:3001` iddiası = **dizin yok** · ADR-081 "read-only" iddiası diskteki ADR-081 ile **eşleşmedi** → **15 kabul/neutral, 4 uyarı; Critic: 2 sahte iddias şartı** |
| 2 | 20 persona — itiraz → çözüm | ✅ | (1) `download.coremusic.net` sahte iddia → claim-code düzeltmesi (`Config/CLAUDE.md:50`) → **şart 1a** · (2) ADR-081 eşleşmedi → referans düzeltmesi (`content-delivery.md`) → **şart 1b** · (3) Controller 0 → `DownloadController` + imzalı URL + resume → **şart 2** · (4) Cihaz limiti 0 → device limit sabitleri + lisans tablosu → **şart 3** |
| 3 | 20 persona — nihai oy | ✅ | **18 kabul / 2 çekimser / 0 red → KABUL** (3 şart bağlayıcı — §5.5) |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| `DownloadController` sınıfı | ❌ PLANNED (0 dosya) | §5.1/2 |
| `download.coremusic.net` Node.js/TS servisi (`Config/CLAUDE.md:50` iddiası; dizin yok) | ❌ PLANNED (şart 1a: iddia `⚠️ PLANNED (dizin yok — ADR-026 şart 1a)` olarak işaretlendi) | §5.1/9 + üst karar (bu ADR stream yolunu PHP'de kurar; Node rolü `brain.md:981` hizalaması §5.1/8) |
| İmza üretimi + nonce tek-use | ❌ PLANNED (0 eşleşme) | §5.1/3 |
| 206/Range/Content-Disposition stream | ❌ PLANNED (0 eşleşme) | §5.1/4 |
| Cihaz limiti (5/2) | ❌ PLANNED (limit sabiti 0) | §5.1/6 |
| Lisans/satın alma şeması | ❌ PLANNED (tablo 0) | §5.1/1 |
| CDN signed URL (statik/bedava) | ❌ PLANNED (spec var, kod 0) | §5.1/9 |
| Rate limit'in indirme rotasına bağlanması | ⚠️ PLANNED (middleware IMPLEMENTED, bağlantı kodlanmadı) | §5.1/7 |
| Debate / Tech Lead | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) — 3 şart §5.5 (1a/1b uygulandı) | §5.3, §5.5, §7 |
| `⚠️` ADR-081 "read-only depo" eşleşmesi | ✅ düzeltildi (şart 1b → content-delivery.md (read-only ilkesi)) | §1.1-D, §5.1/8, §5.5 |

### 5.5 Debate Şartları (Kabul Koşulları — bağlayıcı)

| # | Şart | Kapsam (dosya:satır) | Durum |
|---|------|----------------------|-------|
| 1a | `download.coremusic.net` claim-code düzeltmesi | `shared/src/Config/CLAUDE.md:50` — iddia **silinmedi**, `⚠️ PLANNED (dizin yok — ADR-026 şart 1a)` işareti eklendi | ✅ UYGULANDI (2026-09-25) |
| 1b | ADR-081 "read-only" referans düzeltmesi | Bu ADR içinde §1.1-D, §1.4, §5.1/8, §6 — "ADR-081 = media depo read-only" atfı → **content-delivery.md (read-only ilkesi)**; ADR-081 dosyasına **dokunulmadı** | ✅ UYGULANDI (2026-09-25) |
| 2 | Controller + imzalı URL + resume | `DownloadController` (rota `Gateway.php:87` hazır), HMAC-SHA256 imzalı tek kullanımlık URL (nonce tek-use), Range → 206 + `Content-Disposition` akışı | ❌ PLANNED → §5.1/2, §5.1/3, §5.1/4 |
| 3 | Cihaz limiti + lisans tablosu + test | `maxDevices=5` / `maxConcurrent=2` sabitleri (`DeviceManager`), lisans/satın alma tablosu, ≥1 GB büyük dosya testi | ❌ PLANNED → §5.1/6, §5.1/1, §5.1/5 |

> **Kural:** 1a/1b kapandı; şart **2** ve **3** kapanmadan bu ADR'nin servis iddiası "üretimde" sayılmaz (§5.4 · ADR-005 etiket disiplini).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail'ler (bu ADR'nin yazım usulü) |
| [[../../AGENTS.md]] | Agent registry — §5 domain (`*.php` → Backend Architect, `*.sql` → Data Engineer), §16 kalite standartları, §25.3 kural 2/3 (frozen + log append-only) |
| [[../../WORKFLOW.md]] | Süreçler — uygulama adımlarının faz bağlamı |
| [[../index]] | Karar dizini — **satır 63** `[[ADR-026-download-service-architecture]]` (slug ✅) |
| [[../../index]] | Master katalog — `:392` "I/O servisi \| Node.js 20+ \| PLANNED \| ADR-026", `:643` kısa biçim link kaydı |
| [[../../brain]] | Mimari karar özeti — `:981` "ADR-026 \| Node.js indirme servisi" (§5.1/8 hizalama) |
| [[../../keys]] | Keyword haritası — `:261` "ADR-026 \| download service, architecture" |
| [[ADR-013-rate-limiting-apcu]] | **Hız/kota bağlayıcısı** — `RateLimiterMiddleware.php:19` 60 sn pencere (§1.4, §2.2c) |
| [[ADR-009-clean-url-redirect]] | Redirect/referer/kanonik disiplini — tek-atış redirect + imzalı query koruması (§1.4) |
| [[ADR-020-api-public-security]] | Public yüzey güvenliği — oturum/CSRF/origin kapısı (§1.4) |
| [[ADR-002-pdo-mandatory-no-orm]] | Erişim katmanı — lisans/kota/nonce sorguları PDO prepared (§1.4) |
| [[ADR-022-database-hardened-security]] | Şema sertliği — `coremusic_download` 4 tablo (§1.4) |
| [[ADR-081-multi-provider-data-sync]] | Olay akışı — `DOWNLOAD_*` outbox ile akar; read-only ilkesi için doğru kaynak **content-delivery.md (read-only ilkesi)** (şart 1b, §1.1-D) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1-D, §5.4) |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı + şablon zorunluluğu + UTF-8 tek arayüz (§1.4, §10) |
| [[../../architecture/k15-medya-streaming/content-delivery]] | CDN spec + Range/slice nginx örneği (`:155-157`) + token auth (`:233`) + "aktif geliştirme" (`:291`) — §2.2b CDN opsiyonelinin kaynağı |
| [[../../.sql/mysql/coremusic_download]] | İndirme DB'si — 4 tablo (`:39,:76,:109,:141`) |
| [[../../.sql/mysql/coremusic_user]] | `user_downloads` (`:196-206`) + `download_over_wifi_only` (`:68`) |
| [[../../.sql/mysql/coremusic_media]] | `media_metadata/file_path` (`:153`), `media_access` (`:181`), `media_audit` (`:204`) |
| [[../../.sql/mysql/coremusic_auth]] | `account_type` kademesi (`:30`) — lisans öncesi tek sinyal |
| [[../../.templates/adr/adr-template]] | İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16) |
| [[../../.templates/adr/adr-index]] | ADR şablon envanteri |
| [[../../../shared/src/Config/CLAUDE]] | Domain/port tablosu `:50` — `download.coremusic.net` iddiası `⚠️ PLANNED (dizin yok — ADR-026 şart 1a)` olarak düzeltildi (şart 1a uygulandı) |

> **Durum özeti:** debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · Tech Lead **✅** · Arch Lead **⏳** · şartlar **§5.5** (1a/1b uygulandı, 2-3 PLANNED) · frozen **YOK** · kod: controller/imza/206/cihaz limiti/lisans **PLANNED**, şema + middleware + rota **IMPLEMENTED** (§1.1).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar içeriği) |
| Tech Lead | — | 2026-09-25 | ✅ (debate 3 tur / 20 persona — 18/2/0 KABUL, §5.3 + §5.5) |
| Arch Lead | — | 2026-09-25 | ⏳ PENDING |

---

**1.0.0 | 2026-09-25 | Created**
**1.1.0 | 2026-09-25 | Debate 3/20 (18/2/0 KABUL) + Tech Lead ✅ + 3 şart (§5.5); şart 1a/1b uygulandı**

*ADR-026 — Download Service Architecture (İmzalı Tek Kullanımlık URL · Cihaz Limiti · Kota · Resume · Abuse Önleme)*
*Authority: ADR-026 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
