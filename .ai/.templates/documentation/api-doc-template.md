---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — API Documentation Template"
type: api-doc-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — API Documentation Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic REST API dokümanlarını standartlaştırmaktır: Base URL, kimlik doğrulama (JWT Bearer), JSON formatı ve rate limit (60 req/60s · APCu) bilgisinden; endpoint listesine; Request/Response örneklerine; error code sözlüğü ve hata yanıt formatına kadar tek bir iskelet sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `docs/api/` altında üretilen API dokümanları | Controller / Service / Repository kodu |
| Endpoint, Request/Response ve Error Contract | Veritabanı şeması, migration |
| Auth başlıkları ve rate limit dokümanı | CI/CD pipeline tanımları |

- **Dosya tipi:** Markdown API dokümanı
- **Kullanan agent:** Backend Architect (birincil · AGENTS.md §6), Security Engineer / DevOps Engineer (ikincil)
- **Guardrail:** #16 (Template Mandatory) — yeni API dokümanı bu şablondan başlar

## 3. Mimari

Şablonun tam gövdesi aşağıdadır. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`, H3 → `#####`); tüm `{{PLACEHOLDER}}`, kod bloğu ve tablolar birebir korunmuştur. Hata kodu kuralları §4'tedir.

### {{API_NAME}} API

**Base URL:** `https://api.coremusic.net/v1`
**Authentication:** JWT Bearer Token
**Format:** JSON
**Rate Limit:** 60 req/60s (APCu)

---

#### 3.1 Endpoint Listesi

| Method | Endpoint | Açıklama | Auth |
|--------|----------|----------|------|
| GET | `/{{MODULE}}` | Tüm kayıtları listele | ✅ |
| GET | `/{{MODULE}}/:id` | Tek kayıt getir | ✅ |
| POST | `/{{MODULE}}` | Yeni kayıt oluştur | ✅ |
| PUT | `/{{MODULE}}/:id` | Kaydı güncelle | ✅ |
| DELETE | `/{{MODULE}}/:id` | Kaydı sil | ✅ |

---

#### 3.2 Request/Response Formatları

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

## 4. Kurallar

Zorunlu / yasak kurallar ve kod standartları:

- **Zorunlu:** her istekte `Authorization: Bearer <token>`, `X-CSRF-Token: <csrf_token>`, `X-Requested-With: XMLHttpRequest` başlıkları bulunur (§3.2 örnekleri).
- **Zorunlu:** tüm yanıtlar `{ "status": "success"|"error", ... }` sarmalayıcısı kullanır; liste yanıtlarında `meta` (`total`, `page`, `per_page`) vardır.
- **Zorunlu:** hata kodları §4.1 tablosundaki değerlerle yazılır — yeni/kendi kodunu uydurmak yasaktır.
- **Zorunlu:** hata yanıtları §4.2 formatını kullanır (`code`, `message`, gerekirse `details`).
- **Yasak:** `{{API_NAME}}`, `{{MODULE}}`, `{{COLUMN_1}}`, `{{COLUMN_2}}` placeholder'ları doldurulmadan doküman yayımlanamaz.
- **Rate limit:** 60 req/60s (APcu) — dokümanda her zaman belirtilir.
- **Uyarı:** doğrulanamayan herhangi bir gerçek değer `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

#### 4.1 Error Codes

| Code | HTTP Status | Açıklama |
|------|------------|----------|
| `VALIDATION_ERROR` | 400 | Geçersiz veri |
| `UNAUTHORIZED` | 401 | Kimlik doğrulama başarısız |
| `FORBIDDEN` | 403 | Erişim engellendi |
| `NOT_FOUND` | 404 | Kayıt bulunamadı |
| `CONFLICT` | 409 | Çakışma |
| `RATE_LIMITED` | 429 | Çok fazla istek |
| `SERVER_ERROR` | 500 | Sunucu hatası |

#### 4.2 Hata Formatı

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

---

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/documentation/api-doc-template.md` dosyasını seç (Guardrail #16).
2. **KOPYALA:** dosyayı `docs/api/` altına kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** `{{API_NAME}}` → API adı; `{{MODULE}}` → modül adı; `{{COLUMN_1}}`/`{{COLUMN_2}}` → gerçek sütun adları; §3.1 endpoint tablosunu gerçek route'lara göre güncelle.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu mu? Yanıt formatı §4.2'ye uyuyor mu?
5. **COMMIT:** `docs/api/` altına commit et; `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] Yanıt sarmalayıcısı ve error code'ları §4.1/§4.2 ile tutarlı

**REFACTOR REPORT:** FILE: api-doc-template.md · PURPOSE: API Documentation Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — agent routing (§6: API → Backend Architect) ve Guardrail #16
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*API Documentation Template v2.0.0 — CoreMusic API Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
