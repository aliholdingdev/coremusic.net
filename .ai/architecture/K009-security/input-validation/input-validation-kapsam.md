---
node_id: K009-security/input-validation/input-validation-kapsam
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/input-validation/input-validation-anayasa
evidence:
  - "index.md §4 (sınır: K010-identitiy · K000-isletim-sistemi) — 2026-10-15 okundu"
  - "shared/src/Middleware/ValidationMiddleware.php:17-83 (PageRouter yüzeyi)"
  - "shared/src/Api/Middleware/RequestValidationMiddleware.php:46-96 (API yüzeyi)"
  - "shared/src/Security/ReturnUrlPolicy.php:25-89 (redirect yüzeyi)"
validation:
  - "2026-10-15 — ≥150 satır · sınır matrisi index §4'e sadık · kesişen kardeş 0"
open_questions:
  - "⚠️ U-3: Çıktı kaçışı (escaping) katmanı bu çocuğun KAPSAMINDA MI, sınırında mı? — index open_question, çözülmedi."
  - "⚠️ Dosya yükleme (multipart) yüzeyi kapsam içeri mi? RFC 7578 kanıtı okunmadı."
  - "⚠️ GET/query yüzeyi: PageRouter tarafında query doğrulama kanıtı yok (anayasa U-8)."
group: mimari
---

# K009 · input-validation · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · rol: docs-writer (sınır tanımı).
> Bu dosya **nein bu çocukta olduğunu ve nein olmadığını** bağlar; içerik kuralları
> [[input-validation-anayasa]], sürüş [[input-validation-surucu]], kanıt
> [[input-validation-dogrulama]] içindedir.

## §1 AMAÇ

Girdi doğrulama çocuğunun kapsamı, CoreMusic'in **güvenilmeyen veriyle temas ettiği tüm
giriş noktalarını** kapsar: HTTP gövdesi, query string, route parametresi, header'dan
türeyen host/URI, redirect hedefi ve istisna yoluyla API anahtarı formatı. Kapsamın
dışında kalan her şey başka bir K-space çocuğuna **devredilir**; devirler §6'da
bağlayıcıdır.

## §2 KAPSAM İÇİ YÜZEYLER (envanter)

| # | Yüzey | Girdi kaynağı | Uygulayan bileşen | Dosya·Satır |
|---|---|---|---|---|
| W-1 | PageRouter state-changing istek | `_route_meta['required_fields']` + `body` | `ValidationMiddleware` | `shared/src/Middleware/ValidationMiddleware.php:17-83` |
| W-2 | API body (POST/PUT/PATCH) | `Content-Type` + JSON/body | `RequestValidationMiddleware` | `shared/src/Api/Middleware/RequestValidationMiddleware.php:60-79` |
| W-3 | API query (GET) | `$request['query']` / `$_GET` | `RequestValidationMiddleware` + `Validator` | `RequestValidationMiddleware.php:80-85` |
| W-4 | Kural motoru (ortak) | route `validation` şeması | `Validator` | `shared/src/Api/Middleware/Validator.php:35-97,126-144` |
| W-5 | Redirect/return URL | `return`, `redirect_uri`, `next` | `ReturnUrlPolicy` | `shared/src/Security/ReturnUrlPolicy.php:25-89,127-184` |
| W-6 | Host/URI normalizasyonu | `HTTP_HOST`, URI | `RequestNormalizer` | `shared/src/PageRouter/RequestNormalizer.php:31,49` |
| W-7 | Route meta köprüsü | route registry meta | `PageRouterKernel::resolveRouteMeta` | `shared/src/PageRouter/PageRouterKernel.php:152-169` (351 satır) |
| W-8 | Uç bazlı kural tanımı | `config/routes.php` `validation` | route tablosu (5 uç) | `api.coremusic.net/config/routes.php:59,72,86,96,108` |
| W-9 | Doğrulama istisnası (422) | kural ihlali | `ValidationException` | `shared/src/Exception/ValidationException.php:10-50` |
| W-10 | API anahtarı formatı | ham anahtar | `ApiKeyRepository::RAW_KEY_PATTERN` | `shared/src/Repository/ApiKeyRepository.php:36,233` |
| W-11 | Helper SSOT yönlendirmesi | redirect kontrolü çağrısı | `SecurityHelper::isRedirectUriSafe` | `shared/src/Security/SecurityHelper.php:18-20` |

**Kapsam içi kural (bağlayıcı):** Bu 11 yüzeydeki her girdi, [[input-validation-anayasa]]
§6'daki IV-01..IV-16 maddelerine tabidir; yüzey listede yoksa kapsam genişletme
**ADR + onay** gerektirir (yeni dosya üretmez, anayasa §17'ye eklenir).

## §3 KAPSAM İÇİ KONTROL TÜRLERİ

| Kontrol | Kapsamda | Kapsamda değil |
|---|---|---|
| Tip (string/int/bool/array/email) | ✅ `Validator::typeError` · `Validator.php:126-139` | — |
| Uzunluk (min/max) | ✅ `ValidationMiddleware.php:47-56` · `Validator.php:88-94,115-123` | dizi öğe sayısı ölçümü ⚠️ (yok) |
| Format (regex / `filter_var`) | ✅ `Validator.php:78-82` · `ValidationMiddleware.php:59` · `ReturnUrlPolicy.php:157` | — |
| Değer listesi (enum/whitelist) | ✅ `Validator.php:84-86` · `ValidationMiddleware.php:65-69` · `ReturnUrlPolicy.php:7-15,177-181` | — |
| Şema (scheme) ret | ✅ `ReturnUrlPolicy.php:169-171` | — |
| Takvim (tarih mantığı) | ❌ **kapsam gereği ama kanıt yok** → anayasa IV-07 / U-4 | — |
| Medya tipi (Content-Type) | ✅ `RequestValidationMiddleware.php:60-70,116-123` | — |
| Gövde sökme (JSON bozuk → 400) | ✅ `RequestValidationMiddleware.php:72-77,165-174` | — |

## §4 SINIR MATRİSİ — KAPSAM DIŞI (index §4'e sadık)

| # | Konu | Sahibi (K-space) | Neden bu çocukta değil | Bağ |
|---|---|---|---|---|
| X-1 | Kimlik alanlarının doğrulama modeli (kimlik deposu, parola kuralı) | `K010-identitiy` | kimlik modeli ayrı anayasa | index §4 |
| X-2 | OS girdi yüzeyi (komut satırı, dosya sistemi izni) | `K000-isletim-sistemi/security` | işletim sistemi katmanı | index §4 |
| X-3 | Yetki kararı (kim, neye erişir) | `[[authorization-kapsam]]` | doğrulanmış girdi ÜZERİNDE çalışır | index §5 |
| X-4 | Kota / anti-automation (girdi hacmi) | `[[rate-limiting-index]]` | hacim sınırı doğrulamanın ÖNCEsi | index §5 |
| X-5 | Injection testleri (SQLi/XSS test senaryoları) | `security-testing` | test, üretim kuralı değil | index §5 |
| X-6 | Uygulama katmanı DTO'ları | `K011-application` | kardeş; DTO doğrulama yapmaz (⚠️ U-8) | index §5 |
| X-7 | SSRF dış çağrı adaptörü (outbound HTTP client) | `K013` + `[[security-kapsam]]` A10 | bu çocuk yalnız **girdi yüzeyini** kapsar | anayasa §12 |
| X-8 | Çıktı kaçışı (XSS bağlam escape) tam katman | ⚠️ sınır belirsiz — index open_question | okunmadı; kapsam genişletme ADR ister | anayasa §13 |
| X-9 | Rate limit 429, CSRF token, session | `[[rate-limiting-index]]` · kök middleware | ayrı savunma katmanları | surucu §8 |

**Sınır kuralı (bağlayıcı):** X-1..X-9'daki bir konuya bu çocukta **karar verilmez**;
yalnızca sınır referansı ve girdi doğrulama açısından kesişim notu yazılır.

## §5 YÜZEY × KATMAN MATRİSİ (hangi yüzey hangi katmanı görüyor)

| Yüzey \ Katman | Tip | Uzunluk | Format | Değer | Şema/host | Medya |
|---|---|---|---|---|---|---|
| W-1 PageRouter body | — | ✅ | ✅ (email) | ✅ (enum) | — | — |
| W-2 API body | ✅ | ✅ | ✅ | ✅ | — | ✅ (415) |
| W-3 API query | ✅ | ✅ | ✅ | ✅ | — | — |
| W-5 Redirect URL | — | — | ✅ (`FILTER_VALIDATE_URL`) | ✅ (host whitelist) | ✅ | — |
| W-6 Host/URI | — | — | ✅ (`FILTER_SANITIZE_*`) | — | — | — |
| W-10 API key | — | ✅ (regex `{16,}`) | ✅ (prefix deseni) | ✅ (`cm_live|cm_test`) | — | — |

**Boş hücre = o yüzeyde o katman uygulanmıyor** (kapsam kararı değil, kod gerçeği —
kanıt [[input-validation-dogrulama]] §2'de). W-1'de tip katmanının olmaması ve W-2'de
host katmanının olmaması **beklenen** sınır davranışıdır; W-1'in GET/query'yi
tamamen atlaması bir **açık** olarak işaretlidir (anayasa U-8, surucu D-10).

## §6 DEVİR TABLOSU (bu çocuktan çıkarken)

| Tetik | Kaynak bölüm | Hedef | Öncelik |
|---|---|---|---|
| Doğrulama kuralı kimlik alanına değdi (email/parola) | §4 X-1 | `K010-identitiy` | HIGH |
| Doğrulama reddi yetki kararıyla çelişti | §4 X-3 | `[[authorization-kapsam]]` | HIGH |
| Girdi hacmi/bot şüphesi | §4 X-4 | `[[rate-limiting-index]]` | MEDIUM |
| Injection testi talebi | §4 X-5 | `security-testing` | MEDIUM |
| SSRF outbound hedefi girdiden türetildi | §4 X-7 + anayasa U-7 | `[[security-kapsam]]` + ADR | CRITICAL |
| Yeni yüzey (dosya yükleme, WebSocket, CLI) | §2 kuralı | MO → ADR → bu dosya §2'ye ek | HIGH |

## §7 ÖN KOŞULLAR VE BAĞIMLILIKLAR

| Bağımlılık | Yön | Not |
|---|---|---|
| `[[index]]` | üst | whitelist ilkesi + kapsam özeti (§2/§4) |
| `[[input-validation-anayasa]]` | alt | bağlayıcı kurallar IV-01..IV-16 |
| `K011-application` | kardeşe bağımlı | route/DTO sözleşmesi (index frontmatter `depends_on`) |
| `PageRouterKernel` route meta | altyapı | `required_fields` taşıyıcısı (`:152-169`) |
| route `validation` tablosu | altyapı | kural kaynağı (5 uç) |

## §8 KAPSAM KARARLARI (ADR bekleyenler)

| # | Karar sorusu | Şu anki durum | Etiket |
|---|---|---|---|
| K-1 | Dosya yükleme (multipart/RFC 7578) yüzeyi kapsamda mı? | okunmadı | ⚠️ VERIFICATION REQUIRED |
| K-2 | Çıktı kaçışı tam katman bu çocukta mı? | index open_question — açık | ⚠️ VERIFICATION REQUIRED |
| K-3 | GET/query doğrulama PageRouter'da zorunlu olmalı mı? | kanıt yok (anayasa U-8) | ⚠️ KANIT YOK |
| K-4 | DTO'lar doğrulama yapmalı mı? | şu an yapmıyor (LoginRequest:52-59); mimari tercih ADR'siz değişmez | UNKNOWN (ADR gerekli) |
| K-5 | `filter_input` filtreli okumaya geçiş | kullanılmıyor (web-arastirma §2) | opsiyonel — ADR gerektirir |

## §9 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[index]] | üst | künye + kapsam özeti (§2/§4 bu dosyada genişletilir) |
| [[input-validation-anayasa]] | alt | kurallar (bu dosyanın bağlayıcısı) |
| [[input-validation-surucu]] | alt | sürüş akışı |
| [[input-validation-dogrulama]] | alt | kanıt satırları |
| [[authentication-kapsam]] | kardeş | kimlik doğrulama ayrı çocuk |
| [[authorization-kapsam]] | sonrası | doğrulanmış girdi üzerinden yetki |
| [[rate-limiting-index]] | öncesi | girdi hacmi sınırı |
| [[security-kapsam]] | üst | K009 kapsamı |

## §10 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 11 yüzey · 9 devir · 5 açık kapsam kararı | Vault Steward (subagent) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
