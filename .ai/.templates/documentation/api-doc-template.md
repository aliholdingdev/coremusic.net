---
title: "CoreMusic — API Documentation Template"
type: template
category: template
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: reference
---

# CoreMusic — API Documentation Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

---

## §1 Amaç

CoreMusic REST API dokümanlarını standartlaştırmaktır: Base URL, kimlik doğrulama (JWT Bearer), JSON formatı ve rate limit (60 req/60s · APCu) bilgisinden; endpoint listesine; Request/Response örneklerine; error code sözlüğü ve hata yanıt formatına kadar tek bir iskelet sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`. **Guardrail #16:** yeni API dokümanı bu şablondan başlar; şablonsuz API dokümanı yayımlanamaz.

| Alan | Değer |
|------|-------|
| Template Name | `api-doc-template.md` |
| Template Path | `.ai/.templates/documentation/api-doc-template.md` |
| Hedef Dosya Tipi | Markdown API dokümanı |
| Hedef Konum | `docs/api/` altı |
| Guardrail | #16 (Template Mandatory) |
| Birincil Yazar | Backend Architect |
| İkincil Yazarlar | Security Engineer (auth başlıkları), DevOps Engineer (rate limit /gateway) |
| Base URL | `https://api.coremusic.net/v1` |
| Authentication | JWT Bearer Token |
| Format | JSON (`{ "status": "success"|"error", ... }`) |
| Rate Limit | 60 req/60s (APCu) — ADR-013 |
| Zorunlu Başlıklar | `Authorization: Bearer <token>`, `X-CSRF-Token: <csrf_token>`, `X-Requested-With: XMLHttpRequest` |
| CSRF Anahtarı | `csrf_token` — ADR-010 |
| Error Code Sayısı | 7 (`VALIDATION_ERROR` … `SERVER_ERROR`) |
| Kanıt — Router | `assets.coremusic.net/js/router/*` (glob kanıtı) |
| ⚠️ Doğrulanamayan | `openapi*.{yaml,yml,json}` diskte YOK → OpenAPI spec iddiası yazılmaz |
| Korunan İskelet | H1 + §1 Amaç → §7 Referanslar; error code tabloları birebir korunur |
| Değişken Formatı | `{{VARIABLE}}` (API_NAME, MODULE, COLUMN_1, COLUMN_2, TITLE, DATE, VERSION) |
| Dil | Türkçe (ç ğ ı İ ö ş ü doğru; mojibake YASAK); kod/endpoint adı İngilizce |
| Versiyon | 2.0.0 (Vault Refactor Engine yeniden yazımı) |
| Authority | SSOT (bu dosya); üretilen doküman kendi authority değerini taşır |
| Governance | Red Team · Human Mode · Truth Mode |
| Kayıt Defteri | `.ai/.templates/index.md` (envanter SRP — bu şablonda tekrarlanmaz) |
| Son Kontrol | 2026-09-23 |

### §1.1 API Dokümanı Neden Bir Sözleşmedir?

API dokümanı, istemci ile sunucu arasındaki **sözleşmedir**: kod değişmez, yalnızca contract tanımlanır. Sözleşme bozulursa tüm istemciler (client router, mobil, üçüncü taraf) aynı anda kırılır. Bu yüzden error code'ları ve yanıt sarmalayıcısı şablonda sabitlenmiştir; endpoint eklemek serbesttir ama **format değiştirmek onay gerektirir**.

| Sözleşme Parçası | Sabit Değişmez | Özgür Değişen |
|------------------|----------------|---------------|
| Yanıt sarmalayıcısı | `{ "status": "success"\|"error", ... }` | `data` içeriği |
| Hata kodu kümesi | §4.1'deki 7 kod | Hangi kodun hangi endpoint'te kullanıldığı |
| Hata formatı | `code` + `message` (+ `details`) | `message` metni |
| Auth başlıkları | Bearer + `csrf_token` + X-Requested-With | Token'ın kendisi |
| Rate limit | 60 req/60s (APCu) | — |
| Liste `meta`'sı | `total`, `page`, `per_page` | Sayfa boyutu |
| Endpoint path'i | — | Modül bazında (`/{{MODULE}}`) |

### §1.2 Kimler Bu Şablonu Kullanır?

| Rol | Kullanım | Çıktı |
|-----|----------|-------|
| Backend Architect | Birincil yazar | Endpoint + Request/Response contract |
| Security Engineer | Auth başlığı doğrulaması | Bearer + CSRF + X-Requested-With |
| DevOps Engineer | Rate limit / gateway notu | 60 req/60s (APCu) teyidi |
| UI Designer / Client | İstemci olarak okur | Router uyumu (`assets.coremusic.net/js/router/*`) |
| QA Engineer | Contract testi | §4.1/§4.2 doğrulaması |
| Vault Steward | Kayıt + commit | Registry + log |

---

## §2 Kapsam

API dokümanının kapsadığı ve kapsam dışında bıraktığı alanlar. Doküman bir **sözleşmedir**: kod değişmez, yalnızca contract tanımlanır.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `docs/api/` altında üretilen API dokümanları | Controller / Service / Repository kodu (→ `[[../backend/php-template]]`) |
| Endpoint listesi (Method, Path, Açıklama, Auth) | Veritabanı şeması, migration (→ Data Engineer, `[[../infrastructure/migration-template]]`) |
| Request/Response JSON format örnekleri | CI/CD pipeline tanımları (→ `[[../infrastructure/github-actions-template]]`) |
| Error Contract: 7 error code + hata formatı | Client-side router implementasyonu (`assets.coremusic.net/js/router/*`) |
| Auth başlıkları (Bearer + CSRF + X-Requested-With) | Rate limit kod implementasyonu (ADR-013 uygulaması) |
| Rate limit dokümanı (60 req/60s · APCu) | Güvenlik denetim raporu (→ `[[./security-audit-template]]`) |
| `{{PLACEHOLDER}}` doldurma + Guardrail #16 doğrulaması | Envanter listesi (→ `[[.templates/index]]`, SRP) |
| Türkçe doğruluk + mojibake denetimi | `.ai/log.md` append-only kayıt (üst görevin işi) |

**Dosya tipi:** Markdown API dokümanı · **Uzantı:** `.md` · **Konum:** `docs/api/` · **Guardrail:** #16

### §2.1 Dosya Tipi → Sorumlu Eşlemesi

| Dosya Tipi | Sorumlu | Bu Şablondan mı? |
|------------|---------|-------------------|
| `docs/api/*.md` | Backend Architect | ✅ (bu şablon) |
| `src/Controller/*.php` | Backend Architect | ❌ → `[[../backend/php-template]]` |
| `src/Middleware/*.php` | Security Engineer | ❌ → `[[./security-audit-template]]` (denetim) |
| `.ai/.sql/mysql/*.sql` | Data Engineer | ❌ → `[[../infrastructure/migration-template]]` |
| `.github/workflows/*.yml` | DevOps Engineer | ❌ → `[[../infrastructure/github-actions-template]]` |
| `assets.coremusic.net/js/router/*` | UI Designer | ❌ → `[[../frontend/js-template]]` |

### §2.2 Disk Kanıtları ve Doğrulanamayanlar (YAGNI)

| İddia | Durum | Kanıt / Aksiyon |
|-------|-------|-----------------|
| `assets.coremusic.net/js/router/*` var | ✅ DOĞRULANDI | glob → router dosyaları |
| `.ai/.sql/mysql/*.sql` (18 dosya) var | ✅ DOĞRULANDI | gerçek sütun adları için kanıt |
| `shared/src/**` backend kodu var | ✅ DOĞRULANDI | contract'ın üreticisi |
| `openapi.yaml` / `openapi.json` var | ❌ DOĞRULANAMADI | glob boş → OpenAPI iddiası yazılmaz |
| `docs/api/` dizini var | ⚠️ HEDEF KONUM | şablonun hedefidir; oluşmadan önce iddia edilmez |
| Belirli endpoint path'leri | ⚠️ DOĞRULANACAK | kanıt gelene kadar `{{MODULE}}` placeholder'ı kalır |

---

## §3 Mimari

Şablonun tam gövdesi. Gömme nedeniyle başlıklar iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`, H3 → `#####`); tüm `{{PLACEHOLDER}}`, kod bloğu ve tablolar birebir korunmuştur. Hata kodu kuralları §4'tedir.

### {{API_NAME}} API

**Base URL:** `https://api.coremusic.net/v1`
**Authentication:** JWT Bearer Token
**Format:** JSON
**Rate Limit:** 60 req/60s (APCu)

---

#### §3.1 Endpoint Listesi

| Method | Endpoint | Açıklama | Auth |
|--------|----------|----------|------|
| GET | `/{{MODULE}}` | Tüm kayıtları listele | ✅ |
| GET | `/{{MODULE}}/:id` | Tek kayıt getir | ✅ |
| POST | `/{{MODULE}}` | Yeni kayıt oluştur | ✅ |
| PUT | `/{{MODULE}}/:id` | Kaydı güncelle | ✅ |
| DELETE | `/{{MODULE}}/:id` | Kaydı sil | ✅ |
| GET | `/health` | Servis sağlık kontrolü | ❌ |

---

#### §3.2 Request/Response Formatları

##### GET /api/v1/{{MODULE}}

**Request:**

```http
GET /api/v1/{{MODULE}} HTTP/1.1
Host: api.coremusic.net
Authorization: Bearer <token>
X-CSRF-Token: <csrf_token>
X-Requested-With: XMLHttpRequest
```

**Response (200 OK):**

```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "{{COLUMN_1}}": "value1",
            "{{COLUMN_2}}": "value2",
            "created_at": "2026-09-20T10:00:00Z",
            "updated_at": "2026-09-20T10:00:00Z"
        }
    ],
    "meta": {
        "total": 100,
        "page": 1,
        "per_page": 20
    }
}
```

##### GET /api/v1/{{MODULE}}/:id

**Response (200 OK):**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "{{COLUMN_1}}": "value1",
        "{{COLUMN_2}}": "value2",
        "created_at": "2026-09-20T10:00:00Z"
    }
}
```

**Response (404 Not Found):**

```json
{
    "status": "error",
    "error": {
        "code": "NOT_FOUND",
        "message": "{{MODULE}} not found"
    }
}
```

##### POST /api/v1/{{MODULE}}

**Request:**

```http
POST /api/v1/{{MODULE}} HTTP/1.1
Content-Type: application/json
Authorization: Bearer <token>
X-CSRF-Token: <csrf_token>
```

```json
{
    "{{COLUMN_1}}": "new value",
    "{{COLUMN_2}}": 123
}
```

**Response (201 Created):**

```json
{
    "status": "success",
    "data": {
        "id": 1,
        "{{COLUMN_1}}": "new value",
        "{{COLUMN_2}}": 123,
        "created_at": "2026-09-20T10:00:00Z"
    }
}
```

---

#### §3.3 HTTP Durum Kodları

| Kod | Kullanım | Yanıt Gövdesi |
|-----|----------|---------------|
| 200 | Başarılı GET / PUT / DELETE | `{ "status": "success", "data": ... }` |
| 201 | Başarılı POST (kayıt oluşturuldu) | `{ "status": "success", "data": ... }` |
| 400 | Geçersiz veri | `{ "status": "error", "error": { code: "VALIDATION_ERROR" } }` |
| 401 | Kimlik doğrulama başarısız | `{ "status": "error", "error": { code: "UNAUTHORIZED" } }` |
| 403 | Erişim engellendi | `{ "status": "error", "error": { code: "FORBIDDEN" } }` |
| 404 | Kayıt bulunamadı | `{ "status": "error", "error": { code: "NOT_FOUND" } }` |
| 409 | Çakışma | `{ "status": "error", "error": { code: "CONFLICT" } }` |
| 429 | Rate limit aşıldı | `{ "status": "error", "error": { code: "RATE_LIMITED" } }` |
| 500 | Sunucu hatası | `{ "status": "error", "error": { code: "SERVER_ERROR" } }` |

---

## §4 Kurallar

Zorunlu / yasak kurallar ve kod standartları. Error code tabloları (§4.1, §4.2) birebir korunur; yeni/kendi kodunu uydurmak yasaktır.

| # | Kural | Tür | İhlal Sonucu |
|---|-------|-----|--------------|
| 1 | Her istekte `Authorization: Bearer <token>`, `X-CSRF-Token: <csrf_token>`, `X-Requested-With: XMLHttpRequest` başlıkları bulunur | Zorunlu | Auth hatası |
| 2 | Tüm yanıtlar `{ "status": "success"|"error", ... }` sarmalayıcısı kullanır | Zorunlu | Tutarlılık bozulması |
| 3 | Liste yanıtlarında `meta` (`total`, `page`, `per_page`) vardır | Zorunlu | Sayfalama kırılır |
| 4 | Hata kodları §4.1 tablosundaki 7 değerle yazılır — yeni kod uydurulmaz | Zorunlu | Contract ihlali |
| 5 | Hata yanıtları §4.2 formatını kullanır (`code`, `message`, gerekirse `details`) | Zorunlu | İstemci kırılır |
| 6 | Rate limit 60 req/60s (APCu) her dokümanda belirtilir | Zorunlu | ADR-013 ihlali |
| 7 | CSRF anahtarı yalnız `csrf_token` yazılır | Zorunlu | ADR-010 ihlali |
| 8 | `{{API_NAME}}`, `{{MODULE}}`, `{{COLUMN_1}}`, `{{COLUMN_2}}` doldurulmadan yayımlanamaz | Yasak | Yarım doküman |
| 9 | Doğrulanamayan gerçek değer `⚠️ VERIFICATION REQUIRED` ile işaretlenir | Zorunlu | Hallucination |
| 10 | `openapi*.{yaml,yml/json}` diskte yok → OpenAPI/Specification dosyası iddia edilmez | Yasak | YAGNI ihlali |
| 11 | Endpoint path'leri uydurulmaz; kanıt client router (`assets.coremusic.net/js/router/*`) ve backend route tanımlarıdır | Zorunlu | Kırık contract |
| 12 | Frontmatter 7 zorunlu alan eksiksiz yazılır | Zorunlu | Frontmatter hatası |
| 13 | Wiki-link formatı `[[relative/path/to/file]]` | Zorunlu | Kırık çapraz referans |
| 14 | Türkçe karakterler (ç ğ ı İ ö ş ü) doğru; mojibake YASAK; kod/endpoint adı İngilizce | Zorunlu | Mojibake → onarım |
| 15 | Envanter listesi bu şablonda tekrarlanmaz (SRP) → `[[.templates/index]]` | Yasak | İkincil kaynak çelişkisi |
| 16 | Secret / token / credential yazılmaz; token yerine `<token>` placeholder'ı kullanılır (REDACTED) | Yasak | Güvenlik ihlali |

### §4.0 Kural Grupları

| Grup | Kurallar | Ağırlık | İhlalde |
|------|----------|---------|---------|
| Auth Contract | 1, 7 | CRITICAL | DUR — eskalasyon (§10) |
| Sarmalayıcı & Format | 2, 3, 5 | HIGH | DUR — contract kırılır |
| Error Code Disiplini | 4 | HIGH | DUR — uydurma kod |
| Rate Limit | 6 | MEDIUM | ADR-013 sapması |
| Placeholder Bütünlüğü | 8 | MEDIUM | Yarım doküman |
| Kanıt & Hallucination | 9, 10, 11 | HIGH | `⚠️ VERIFICATION REQUIRED` |
| Vault Protokolü | 12, 13, 14, 15 | LOW | Vault onarımı |
| REDACTED | 16 | CRITICAL | DUR — maskele |

### §4.0.1 Neden Yeni Error Code Uydurulmaz?

Yedi kod, tüm istemcilerin aynı davranışı göstermesini sağlar. Sekizinci bir kod eklendiğinde eski istemciler onu tanımaz ve sessizce düşer. Yeni bir durum gerektiğinde:

1. Mevcut 7 koddan biriyle ifade edilebilir mi? → Evet ise onu kullan.
2. Hayır ise → `[[../../AGENTS.md]]` §10 üzerinden Tech Lead'e (L2) eskale et.
3. Onay gelmeden kod eklenmez; dokümanda `⚠️ VERIFICATION REQUIRED` notu düşülür.

### §4.1 Error Codes

| Code | HTTP Status | Açıklama |
|------|------------|----------|
| `VALIDATION_ERROR` | 400 | Geçersiz veri |
| `UNAUTHORIZED` | 401 | Kimlik doğrulama başarısız |
| `FORBIDDEN` | 403 | Erişim engellendi |
| `NOT_FOUND` | 404 | Kayıt bulunamadı |
| `CONFLICT` | 409 | Çakışma |
| `RATE_LIMITED` | 429 | Çok fazla istek |
| `SERVER_ERROR` | 500 | Sunucu hatası |

### §4.2 Hata Formatı

```json
{
    "status": "error",
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Validation failed",
        "details": {
            "{{COLUMN_1}}": "This field is required",
            "{{COLUMN_2}}": "Must be a positive integer"
        }
    }
}
```

**Alan kuralları:**

| Alan | Zorunluluk | Kural |
|------|-----------|-------|
| `status` | Zorunlu | Her yanıtta `"success"` veya `"error"` |
| `error.code` | Zorunlu (hata) | §4.1 tablosundaki 7 değerden biri |
| `error.message` | Zorunlu (hata) | İnsan-okunur kısa mesaj; stack trace YASAK |
| `error.details` | İsteğe bağlı | Alan-bazlı doğrulama hataları (VALIDATION_ERROR) |
| `data` | Zorunlu (başarı) | Tek kayıt objesi veya dizi |
| `meta` | Zorunlu (liste) | `total`, `page`, `per_page` |

---

## §5 Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

| Adım | Eylem | Detay | Çıktı |
|------|-------|-------|-------|
| 1 | ŞABLONU SEÇ | `.ai/.templates/documentation/api-doc-template.md` (Guardrail #16) | Şablon kopyası |
| 2 | KOPYALA | `docs/api/` altına kopyala | Yeni doküman iskeleti |
| 3 | DOLDUR | `{{API_NAME}}` → API adı; `{{MODULE}}` → modül adı; `{{COLUMN_1}}`/`{{COLUMN_2}}` → gerçek sütun adları; §3.1 endpoint tablosunu gerçek route'lara göre güncelle | Dolu API dokümanı |
| 4 | DOĞRULA | §6 kontrol listesi; yanıt sarmalayıcısı §4.2 ile tutarlı mı? | 8/8 gate |
| 5 | COMMIT | `docs/api/` altına commit et; registry (`[[.templates/index]]`) + `log.md`'ye giriş ekle | Vault senkronu |

**Adım 3 detayı — doldurma sırası:** (a) künye (`{{API_NAME}}`, Base URL, Auth, Format, Rate Limit), (b) §3.1 endpoint listesi gerçek route'lardan, (c) §3.2 her endpoint için Request + Response örnekleri, (d) `{{COLUMN_*}}` → gerçek BCNF sütun adları (`.ai/.sql/mysql/*.sql` kanıtı), (e) §3.3 HTTP durum kodları dokümana göre, (f) §4.1/§4.2 error contract kontrolü, (g) `{{DATE}}` güncellemesi.

**Adım 4 doğrulama soruları:**

| # | Soru | Evet koşulu |
|---|------|-------------|
| 1 | Her yanıtta `status` sarmalayıcısı var mı? | success/error dışında değer yok |
| 2 | Liste yanıtında `meta` var mı? | total/page/per_page üçlüsü tam |
| 3 | Hata kodları §4.1 ile mi? | 7 kod dışına çıkılmamış |
| 4 | Auth başlıkları eksiksiz mi? | Bearer + CSRF + X-Requested-With |
| 5 | Rate limit yazıldı mı? | 60 req/60s (APCu) |

### §5.1 Endpoint Bazlı Doldurma Rehberi

Her endpoint için doldurulacak alanlar sabittir; bu tablo sırayı ve sorumluyu tanımlar.

| Endpoint Tipi | Zorunlu Alanlar | Boş Bırakılamaz | Sorumlu |
|---------------|-----------------|------------------|---------|
| GET (liste) | path, açıklama, auth, Request başlıkları, 200 örneği, `meta` | `meta.total/page/per_page` | Backend Architect |
| GET (tek) | path, açıklama, auth, 200 örneği, 404 örneği | 404 error contract | Backend Architect |
| POST | path, açıklama, auth, Request gövdesi, 201 örneği | CSRF başlığı | Backend Architect |
| PUT | path, açıklama, auth, Request gövdesi, 200 örneği | id parametresi | Backend Architect |
| DELETE | path, açıklama, auth, 204/200 örneği | auth başlıkları | Backend Architect |
| Health | `/health`, auth = ❌, 200 örneği | `status` + `timestamp` | DevOps Engineer |
| Hata senaryosu | İlgili error code (§4.1), §4.2 formatı | `code` + `message` | Backend Architect |

### §5.2 Doküman Boyutu ve Parçalama

| Durum | Aksiyon |
|-------|---------|
| Tek modül, ≤15 endpoint | Tek dosya: `docs/api/{{MODULE}}.md` |
| Tek modül, >15 endpoint | Parçala: liste + detay ayrı bölümler |
| Çok modül | Modül başına bir dosya; indeks `docs/api/` ana sayfasında |
| Contract değişikliği | Yeni bölüm ekle, eskiyi silme (append-only benzeri) |

### §5.3 Handover Tetikleyicileri

| Tetikleyici | Kaynak | Hedef | Öncelik |
|-------------|--------|-------|---------|
| Yeni error code ihtiyacı (§4.0.1) | Backend Architect | Tech Lead (L2) | HIGH |
| Auth başlığı değişikliği | Backend Architect | Security Engineer | HIGH |
| Rate limit değişikliği | Backend Architect | DevOps Engineer | MEDIUM |
| BCNF sütun adı uyuşmazlığı | Backend Architect | Data Engineer | MEDIUM |
| Contract testi başarısız | QA Engineer | Backend Architect | HIGH |

---

## §6 Doğrulama

Doküman commit edilmeden önce kalite kapıları sırayla kontrol edilir; tek bir madde bile ✅ değilse commit yapılmaz.

| # | Kontrol | Beklenen | Durum |
|---|---------|----------|-------|
| 1 | Frontmatter 7 zorunlu alan var mı? | title, type, category, date, updated, version, status, authority | ✅/❌ |
| 2 | H1 + §1-§7 iskeleti eksiksiz mi? | 7 bölüm silinmemiş | ✅/❌ |
| 3 | Tüm `{{PLACEHOLDER}}`'lar dolduruldu mu? | API_NAME, MODULE, COLUMN_*, TITLE, DATE | ✅/❌ |
| 4 | Yanıt sarmalayıcısı tutarlı mı? | `{ "status": "success"|"error", ... }` | ✅/❌ |
| 5 | Liste yanıtında `meta` var mı? | total, page, per_page | ✅/❌ |
| 6 | Error code'ları §4.1 ile tutarlı mı? | 7 kod, uydurma yok | ✅/❌ |
| 7 | Hata formatı §4.2 ile tutarlı mı? | code + message (+ details) | ✅/❌ |
| 8 | Auth başlıkları tam mı? | Bearer + `csrf_token` + X-Requested-With | ✅/❌ |
| 9 | Rate limit 60 req/60s yazıldı mı? | ADR-013 | ✅/❌ |
| 10 | Stack trace / secret sızıntısı yok mu? | REDACTED + §4.2 message kuralı | ✅/❌ |
| 11 | OpenAPI iddiası yapılmadı mı? | `openapi*` diskte yok → iddia yok | ✅/❌ |
| 12 | Wiki-link'ler hedefe ulaşıyor mu? | `[[relative/path]]` formatı | ✅/❌ |
| 13 | Türkçe doğruluk + mojibake yok mu? | ç ğ ı İ ö ş ü doğru | ✅/❌ |
| 14 | Doğrulanamayan alan işaretlendi mi? | `⚠️ VERIFICATION REQUIRED` | ✅/❌ |
| 15 | Envanter SRP ihlali yok mu? | Envanter `[[.templates/index]]`'de | ✅/❌ |
| 16 | § başlıkları kategori tutarlılığına uygun mu? | §1 Amaç → §7 Referanslar (DRY) | ✅/❌ |

### §6.1 Doğrulama Aşamaları

| Aşama | Kontrol Grubu | Kapsadığı Maddeler | Geçme Koşulu |
|-------|---------------|--------------------|--------------|
| A | Yapı | 1, 2 | Frontmatter + iskelet sağlam |
| B | İçerik | 3 | Placeholder kalmamış |
| C | Contract | 4, 5, 6, 7 | Sarmalayıcı + meta + error code + format |
| D | Auth & Limit | 8, 9 | Başlıklar + 60 req/60s |
| E | Kanıt | 10, 11, 13, 14 | OpenAPI iddiası yok, link/dil doğru |
| F | Protokol | 16, 15, 14, 13 | REDACTED + SRP + DIP + DRY |

### §6.1.1 Aşama Kanıtları

| Aşama | Kanıt | Alınan Çıktı |
|-------|-------|--------------|
| A | Frontmatter okuma | 7 alan + §1-§7 |
| B | `grep '{{'` | 0 placeholder |
| C | Sarmalayıcı + meta + code + format | `{ status }`, `meta`, 7 kod, §4.2 |
| D | Header + rate limit | 3 başlık + 60/60s |
| E | iddia + link + dil | OpenAPI iddiası 0, link 0 kırık |
| F | REDACTED + SRP + DRY | `<token>`, envanter yok, § eşleşmesi |

### §6.2 Red Sinyalleri (Otomatik DUR)

| Red Sinyali | Neden | Aksiyon |
|-------------|-------|---------|
| `status` sarmalayıcısı yok | Contract ihlali | DUR — §3.2 formatını uygula |
| §4.1 dışı error code | Uydurma kod | DUR — 7 koddan birine çek veya eskale et |
| `meta` alanı eksik (liste yanıtı) | Sayfalama kırık | DUR — `total`/`page`/`per_page` ekle |
| Auth başlıklarından biri eksik | Auth contract | DUR — üç başlığı ekle |
| Rate limit yazılmamış | ADR-013 | DUR — 60 req/60s (APCu) ekle |
| Token/secret gerçek değeri yazıldı | REDACTED | DUR — `<token>` placeholder'ına çevir |
| `openapi.*` iddiası (dosya yok) | YAGNI | DUR — iddiayı sil |
| Placeholder `{{...}}` kalmış | Yarım doküman | DUR — doldur |
| Mojibake tespiti | Encoding | `vault-utf8-writer.mjs repair` |

### §6.3 Sürüm Geçmişi Kuralları

| Kural | Değer |
|-------|-------|
| İlk satır | `1.0.0 | {{DATE}} | Created` |
| Revizyon | Her onaylı değişiklikte yeni satır |
| Düzenleme | Mevcut satıra dokunulmaz (append-only) |
| Major bump | Sarmalayıcı/error code değişikliği → L2 onayı |

### §6.4 Read-Only Doğrulama Komutları

Yazma yasaklı kontroller (salt okunur); biri bile beklenen çıktıyı vermezse commit durur.

```bash
# 1) Placeholder kontrolü — 0 olmalı (tüm {{VARIABLE}} doldu)
grep -c '{{' <API-DOC-FILE>

# 2) Error code tekrarı — her kod tek satırda olmalı
grep -E '^\| [A-Z]+[0-9]+' <API-DOC-FILE>

# 3) Secret taraması — sonuç 0 olmalı (REDACTED)
grep -Ei 'api[_-]?key|password|secret' <API-DOC-FILE>
```

*(`<API-DOC-FILE>` gerçek dosya yolu ile değiştirilir; token değerleri `<token>` placeholder'ıdır.)*

---

**REFACTOR REPORT:** FILE: api-doc-template.md · PURPOSE: API Documentation Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu (7 error code, 9 HTTP kod, 16 kural, 16 doğrulama) · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

---

## §7 Referanslar

| Kaynak | Wiki-Link | Rol |
|--------|-----------|-----|
| Template Registry | [[.templates/index]] | Bu şablonun kaydı (envanter SRP) |
| Vault ana sözleşmesi | [[../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16 kaynağı |
| Agent Registry (SSOT) | [[../../AGENTS.md]] | Routing §6 (API → Backend Architect), Guardrail #16 |
| Backend şablonu | [[../backend/php-template]] | Controller/Service kodu (contract'ın üreticisi) |
| Güvenlik denetimi | [[./security-audit-template]] | Auth/CSRF/CSP başlıklarının denetimi |
| Mimari kararlar | [[../../brain.md]] | ADR-010 (csrf_token), ADR-013 (rate limit) özetleri |
| BCNF şema kanıtı | `.ai/.sql/mysql/*.sql` | Gerçek sütun adları (18 dosya) |
| Router kanıtı | `assets.coremusic.net/js/router/*` | Client route dosyaları (glob kanıtı) |
| Şablon kuralı | [[../CLAUDE.md]] | Guardrail #16 |
| Süreçler | [[../../WORKFLOW.md]] | Fazlar, session protokolü |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
