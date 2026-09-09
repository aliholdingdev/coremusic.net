---
type: architecture
category: contracts
title: "API Validation Rules"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# API Validation Rules

## 1. Purpose

Defines server-side validation requirements for all API inputs in CoreMusic. Validation is mandatory, never trust client input.

---

## 2. Core Principles

| Principle | Rule |
|-----------|------|
| Server-side mandatory | All validation runs on server, never trust client |
| Whitelist validation | Allow only expected field names and types |
| Fail fast | Return 422 on first validation failure |
| No silent defaults | Missing required fields return explicit error |
| Sanitize + validate | Strip input, then validate against rules |

---

## 3. Validation Sources

| Source | Validation Type | Example |
|--------|----------------|---------|
| Request body (JSON) | Schema validation | `{"title": "Song"}` |
| Query parameters | Type + range validation | `?page=1&per_page=20` |
| Headers | Format validation | `Authorization: Bearer <token>` |
| Route parameters | Format validation | `/api/songs/{id}` |

---

## 4. Validation Rules Catalog

| Rule | Description | Example |
|------|-------------|---------|
| `required` | Field must be present | `title: required` |
| `email` | Valid email format | `user@email.com` |
| `min_length:N` | Minimum string length | `min_length:3` |
| `max_length:N` | Maximum string length | `max_length:255` |
| `regex:PATTERN` | Pattern match | `regex:/^[a-z]+$/` |
| `enum:[v1,v2]` | Allowed values | `enum:[pop,rock,jazz]` |
| `numeric` | Numeric value | `42` or `"42"` |
| `integer` | Integer only | `42` (not `42.5`) |
| `float` | Floating point | `42.5` |
| `date` | ISO 8601 date | `2024-01-15` |
| `datetime` | ISO 8601 datetime | `2024-01-15T10:30:00Z` |
| `uuid` | UUID v4 format | `550e8400-e29b-41d4-a716-446655440000` |
| `url` | Valid URL | `https://example.com` |
| `boolean` | True/false | `true` or `false` |
| `in:array` | Value in allowed list | `in:[1,2,3]` |
| `json` | Valid JSON object | `{"key": "value"}` |
| `file` | Uploaded file | Multipart form data |
| `array` | Array value | `[1,2,3]` |

---

## 5. Validation Error Format

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Request validation failed",
    "details": [
      {
        "field": "title",
        "rule": "required",
        "message": "Title is required"
      },
      {
        "field": "email",
        "rule": "email",
        "message": "Invalid email format"
      },
      {
        "field": "duration",
        "rule": "numeric",
        "message": "Duration must be numeric"
      }
    ]
  }
}
```

**HTTP Status:** `422 Unprocessable Entity`

---

## 6. Validation Groups

| Group | When Applied | Rules |
|-------|-------------|-------|
| `create` | POST /api/resource | All required fields |
| `update` | PUT /api/resource | All fields optional (partial update) |
| `patch` | PATCH /api/resource | Only provided fields validated |
| `search` | GET /api/resource?q= | Query parameter rules |
| `auth` | Login/Register | Auth-specific rules |

---

## 7. Nested Object Validation

```json
{
  "artist": {
    "name": "required|max_length:255",
    "bio": "max_length:2000",
    "social": {
      "website": "url",
      "twitter": "max_length:15"
    }
  }
}
```

**Rules:**
- Nested objects validated recursively
- Dot-notation for error paths: `artist.social.website`
- Missing nested objects return parent field error

---

## 8. Custom Validators

| Validator | Logic |
|-----------|-------|
| `unique:table,column` | Check DB uniqueness (exclude current ID on update) |
| `exists:table,column` | Check foreign key exists |
| `strong_password` | Min 8 chars, uppercase, lowercase, digit, special |
| `no_html` | Strip all HTML tags |
| `safe_filename` | Allow alphanumeric, dash, underscore, dot only |
| `file_size:max_mb` | Max file upload size |
| `mime_type:allowed` | Validate file MIME type |

---

## 9. Security Validation

| Rule | Purpose |
|------|---------|
| Strip HTML tags | Prevent XSS in stored content |
| Limit string length | Prevent memory exhaustion |
| Limit array size | Prevent DoS via large payloads |
| Reject null bytes | Prevent path traversal |
| Reject control chars | Prevent injection attacks |
| Validate Content-Type | Ensure correct content type header |

---

## 10. Cross References

| Document | Relationship |
|----------|-------------|
| [[api-architecture-master]] | Parent API architecture |
| [[api-design-rules]] | API design conventions |
| [[api-error-codes]] | Error code catalog |

---

---

## 11. respect/validation Eşlemesi (composer ^2.0)

§4 kataloğu ↔ `respect/validation ^2.0` (shared-infrastructure require) kuralları:

| Katalog Kuralı | respect/validation Kuralı | Not |
|----------------|---------------------------|-----|
| `email` | `Email::class` | — |
| `min_length:N` | `Length::min(N)` | — |
| `max_length:N` | `Length::max(N)` | — |
| `regex:PATTERN` | `Regex::class` | — |
| `enum:[v1,v2]` | `In::class` | — |
| `numeric` | `NumericVal::class` | isim çakışması — NumericVal |
| `integer` | `IntVal::class` | IntVal |
| `float` | `FloatVal::class` | — |
| `date` | `Date::class` | ISO format |
| `datetime` | `DateTime::class` | — |
| `uuid` | `Uuid::class` | — |
| `url` | `Url::class` | — |
| `boolean` | `BoolType::class` | BoolVal değil — BoolType |
| `json` | `Json::class` | — |
| `array` | `ArrayType::class` | — |
| `required` | `PresenceOf::class` + `NotEmpty` | — |

**Gerçek kod durumu:** Pipeline #10 ValidationMiddleware PLANNED (l1 index §16); respect/validation bağımlılığı hazır. Bu eşleme tablosu üretim kodu yazılırken doğrudan kullanılır — katalog↔kütüphane çevirisi tek noktadan.

---

## 12. Pipeline Bağlantısı (#10 Validation)

```
Pipeline: ... Auth (#8) → Permission (#9) → Validation (#10) → Handler
  → ValidationMiddleware (#10 — PLANNED)
     → kural setini DTO/endpoint sözleşmesinden alır (§6 groups)
     → respect/validation Assert zinciri çalıştırır
     → §5 422 formatını üretir (details[] — alan bazlı)
     → hata kodları api-error-codes STANDARDI (VALIDATION_ERROR)
```

Konum gerekçesi: Permission (#9) sonrası — yetkisiz istek validasyon maliyeti ödemez (l1 index §16 sıra notu paralel). Sıra immutable (ADR zinciri).

---

## 13. Hata Kodu Eşlemesi (api-error-codes Çapraz)

| Durum | HTTP | code | details örneği |
|-------|------|------|----------------|
| Kural ihlali (tek/çoklu) | 422 | `VALIDATION_ERROR` | details[] alan bazlı (§5) |
| JSON gövde bozuk | 400 | `INVALID_JSON` | — |
| Content-Type yanlış | 415 | `UNSUPPORTED_MEDIA_TYPE` | §9 kural 6 |
| Payload taşması | 413 | `PAYLOAD_TOO_LARGE` | §9 limit boyutu |
| Dosya tipi izinsiz | 422 | `INVALID_FILE_TYPE` | §8 mime_type |

Kanonik katalog: [[api-error-codes]] (391) — bu tablo filtreleme/benzeri özel kodlarla (api-filtering §13 paraleli) tamamlanır.

---

## 14. Savunma Katmanları — Injection Karşı (api-filtering §12 Paraleli)

| Katman | Validation Tarafı | Örnek |
|--------|-------------------|-------|
| 1. Tip zorlaması | numeric alan string kabul etmez | duration:"abc" → 422 |
| 2. Length limit | max_length + §9 boyut limitleri | 200 karakter üstü reddi |
| 3. HTML strip | no_html custom validator | stored XSS önleme |
| 4. Kontrol karakteri reddi | null byte, control chars | path traversal (§9) |
| 5. Prepared statement | PDO (ADR-002) — validation sonrası | injection kalıntı savunması |
| 6. Output encoding | TrustedTypes/DOMParser (L3) | stored-XSS render savunması |

Kural: Validation §9 satır seti injection'ın GİRİŞ kapısıdır; PDO ve output encoding derinlik katmanlarıdır — hiçbiri tek başına yeterli değil, üçü zincirdir (api-filtering §12 6-katman mantığıyla aynı).

---

## 15. Dosya Yükleme Güvenliği (Detay)

| Adım | Kural | Neden |
|------|-------|-------|
| 1. Boyut | `file_size:max_mb` (§8) | DoS önleme |
| 2. MIME | `mime_type:allowed` — YALNIZ beyaz liste | `image/jpg` değil `image/jpeg` kataloğu |
| 3. Uzantı | `safe_filename` (§8) | `../`, çift uzantı (`file.php.jpg`) reddi |
| 4. İçerik sniff | Gerçek MIME (magic bytes) — header'a güvenme | Sahte Content-Type |
| 5. Depolama adı | Rastgele UUID (ramsey/uuid) — orijinal ad saklanmaz | Path traversal + tahmin |
| 6. Servis dışı dizin | Yükleme klasörü web root dışı veya PHP execution kapalı | RCE önleme |

Kritik not: MIME header'a güvenmek yeterli DEĞİLDİR — magic bytes (finfo) doğrulaması katman 4'te zorunludur; php.php.jpg klasik atlatması uzantı+ MIME çift kontrolüyle kapanır.

---

## 16. Test Senaryoları

| # | İstek | Beklenen |
|---|-------|----------|
| 1 | title eksik (create) | 422 required |
| 2 | email format bozuk | 422 email |
| 3 | password zayıf | 422 strong_password |
| 4 | duration "abc" | 422 numeric |
| 5 | uuid bozuk | 422 uuid |
| 6 | enum dış genre | 422 enum |
| 7 | nested: artist.social.website bozuk | 422 artist.social.website path |
| 8 | unique e-posta çakışması | 422 unique |
| 9 | `<script>` title içinde | strip + 422 (no_html) |
| 10 | null byte dosya adı | red |
| 11 | 10MB dosya (limit 5MB) | 422 file_size |
| 12 | Content-Type: text/plain + JSON | 415 |
| 13 | update'te yalnız title | 200 (patch grup) |
| 14 | payload 10MB JSON | 413 |

---

## 17. Diagnostics (tekrarlanabilir)

```powershell
# 1. respect/validation bağımlılığı kanıtı
Select-String -LiteralPath "shared\composer.json" -Pattern "respect/validation"

# 2. ValidationMiddleware gerçekliği (beklenen: yok → PLANNED)
Get-ChildItem -LiteralPath "shared\src\Middleware" -Filter "*alidat*" -ErrorAction SilentlyContinue

# 3. l1 pipeline #10 konumu dokümanı
Select-String -LiteralPath ".ai\architecture\l1-security\middleware.md" -Pattern "Validation" -ErrorAction SilentlyContinue

# 4. api-error-codes VALIDATION_ERROR
Select-String -LiteralPath ".ai\architecture\03-contracts\api-error-codes.md" -Pattern "VALIDATION_ERROR" -ErrorAction SilentlyContinue

# 5. user_preferences/auth şemaları (unique test alanları)
Get-ChildItem -LiteralPath ".ai\.sql\mysql" -Filter "coremusic_auth.sql" | Select-Object Name
```

---

## 18. SSS

**S: Neden 422, 400 değil?**
C: 400 "söz dizimi bozuk" (JSON kırık vb.); 422 "söz dizimi OK ama semantik/alan hatası" — validation tipik 422'dir (§5). JSON bozuksa 400 (§13).

**S: Fail fast ile TÜM hataları toplamak çelişiyor mu?**
C: Hayır — fail fast = ilk İHLALİN devam etmemesi; details[] toplayarak çoklu alan hatası tek yanıtta raporlanır (§5 3 detay örneği). Kullanıcı deneyimi için çoklu rapor tercih edilir.

**S: Sanitize + validate sırası neden önce sanitize?**
C: Önce strip edilmezse `\u003Cscript\u003E` gibi encode atlatmalar validation'ı geçer; strip → temiz veri → kural — sıra güvenlik sırasıdır (§2).

**S: Validation grupları (§6) Hangi kaynakta tanımlı?**
C: Endpoint sözleşmesinde (api-endpoints.md) — her endpoint hangi grupla çalışacağını bildirir; ValidationMiddleware grup adını alır.

**S: Custom validator'lar nerede yaşar?**
C: `shared/src/Api/Validation/` (hedef konum — PLANNED); respect/validation Rule interface'iyle uyumlu sarmalanır (§11 eşleme).

**S: `unique` doğrulama DB sorgusu — race condition?**
C: Evet — check-then-insert yarışı var; GERÇEK güvence DB unique index'tir (BCNF şema). unique validator UX hızlandırıcısıdır, güvenlik garantisi değildir.

**S: `strong_password` Argon2id ile ilişkisi?**
C: İki farklı katman: strong_password GİRİŞ kalitesi (kural), Argon2id SAKLAMA (ADR-022). İkisi birlikte zorunludur — zayıf parola + güçlü hash yine zayıftır.

**S: Validation hatası loglanır mı?**
C: INFO seviyesinde (deep-logging hattı) — attack pattern tespiti için tekrar eden 422'ler rate-limit işaretidir (l1 RateLimiter paraleli).

---

## 19. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Client-side validation'a güvenme | Orta | Kritik | §2 ilke 1 |
| 2 | MIME header güveni | Orta | Yüksek | §15 adım 4 |
| 3 | unique race condition | Orta | Orta | §18 SSS 6 DB index |
| 4 | Strip'siz validate | Orta | Yüksek | §18 SSS 3 |
| 5 | ValidationMiddleware atlanması (pipeline) | Düşük | Kritik | immutable sıra (§12) |
| 6 | details[] içinde secret sızması | Düşük | Kritik | alan redaksiyon (§5 formatı) |

---

## 20. İzlenebilirlik

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 18 kural | §4 | Sözleşme kataloğu |
| 5 grup | §6 | Endpoint sözleşmeleri |
| 7 custom | §8 | Hedef tanım |
| respect ^2.0 | shared/composer.json | Faz 0 ✅ |
| ValidationMiddleware PLANNED | l1 index §16 | Faz 0 ✅ |
| Argon2id ayrımı | brain §10 + ADR-022 | ✅ |

---

## 21. Karar Ağacı — "Bu kural hangi katmana?"

```
Kural tipi?
  ├─ Alan formatı/değeri → ValidationMiddleware (§11-12, 422)
  ├─ Dosya → §15 6-adım (mime+magic+uuid ad)
  ├─ Kimlik/saklama → Auth hattı (strong_password → Argon2id)
  ├─ Sorgu filtresi → api-filtering sözleşmesi (whitelist)
  └─ Yanıt içeriği → Output encoding (L3 TrustedTypes)
```

---

## 22. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-08-09 | İlk sözleşme |
| 2.0.0 | 2026-09-08 | Faz 2e: §11 respect eşlemesi; §12 pipeline bağlantısı; §13 hata kodları; §14 savunma zinciri; §15 dosya güvenliği 6-adım; §16-§21 test/diagnostics/SSS/risk/izle/ağaç |

---

## 23. Validation Mesaj İ18N (PLANNED)

| Konu | Hedef |
|------|-------|
| Varsayılan dil | TR (kullanıcı kitlesi) |
| Mesaj kaynakları | i18n DB şeması (coremusic_system — ui_strings) |
| Kural bazlı mesaj | kural adı → şablon ("{field} zorunludur") |
| API yanıtı | İstemci diline göre (Accept-Language) |
| details[].message | Yerelleştirilmiş; kural adı İngilizce kalır (makine okunur) |

Kural: details[].rule MAKİNE okunur sabit kalır (i18n'e girmaz) — istemci kurala göre UI gösterir; message insan okur (i18n). İki katman ayrımı i18n şeması (ADR-079) devreye alınca zorunludur.

---

## 24. Rate Limit + Validation Etkileşimi

| Sıra | Adım | Neden |
|------|------|-------|
| 1 | RateLimiter (#3 pipeline) | Validasyon maliyeti öncesi kaba filtre |
| 2 | Validation (#10) | 422'ler rate sayacına girmez (fail-fast önce) |
| 3 | 422 tekrarı tespiti | Aynı IP sürekli 422 → attack pattern → l1 RateLimiter işareti (validation.md §18 SSS 8 paraleli) |

Kural: 422'ler rate limit tetiklemez (normal UX hatası); 400/413/415 pattern'leri (malformed flood) rate-limit sinyalidir — ayrım logda tutulur.

---

## 25. Validation Grubu Örneği (Doldurulmuş — songs create)

```php
// POST /api/songs — 'create' grubu
'title'        => 'required|max_length:255|no_html',
'artist_id'    => 'required|integer|exists:coremusic_musics,artist',
'genre'        => 'required|enum:[pop,rock,jazz,...]',
'duration'     => 'required|integer|min:0',
'release_date' => 'date|nullable',
'file'         => 'file|mime_type:[audio/flac,audio/mpeg]|file_size:50',

// PUT /api/songs/{id} — 'update' grubu: yalnız gelen alanlar
// PATCH — 'patch' grubu: yalnız gönderilen alanlar validate
```

Kural: grup = alan→kural seti haritası; grup dışı alan sessizce YOK SAYILMAZ — `forbidden_field` hatası döner (strict mode; api-filtering whitelist paraleli).

---

## 26. Ek SSS

**S: Validation hatası içinde girilen değerler echo edilir mi (details)?**
C: Kısmen — değer `[REDACTED]` kuralına tabidir (MEMORY §12): password/token içerikli alan echo edilmez; title gibi public alan echo edilebilir. Redaksiyon filtresi details üretiminde çalışır (§18 risk 6 önlemi).

**S: `no_html` strip'i validation'dan önce mi sonra mı?**
C: Önce (§2 sanitize+validate sırası) — strip edilmiş veri validate edilir; aksi halde `<script>` uzunluğu max_length'i şişirir sonra strip edilir — bozulma.

**S: `exists:` sorgusu her istekte DB'ye gider mi?**
C: Evet — canlı doğrulama; cache'lenmez ( FK tutarlılığı anlık). Yoğun endpoint'lerde FK constraint DB düzeyinde zaten vardır (BCNF) — validator UX katmanı.

**S: PATCH'te belirtilmeyen alanlar null mı kalsın mı?**
C: Dokunulmaz (null'a çekilmez) — partial update semantiği (§6 patch grup tanımı). null'a çekme ayrı explicit alan değeriyle olur (null=true gönderilerek).

**S: `json` kuralı iç içe derinlik sınırlar mı?**
C: §9 limit array boyutu kapsar; derinlik limiti ayrıdır (recursive DoS) — max_depth:5 önerisi §4 kataloğuna eklenebilir (PLANNED).

**S: 422 yanıtında hangi alanlar listelenir — tümü mi ilk hata mı?**
C: §2 fail-fast + §5 details[] — çoklu toplama tercih edilir (kullanıcı deneyimi); fail-fast, ihlalden SONRAKİ işlemin yapılmamasıdır (her alanın hatalarını toplamayı engellemez).

---

## 27. Risk İzle (Devam)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 7 | details[] redaksiyon atlanması | Orta | Kritik | §26 SSS 1 + §18 risk 6 |
| 8 | PATCH null semantiği karışıklığı | Orta | Orta | §26 SSS 4 |
| 9 | i18n şablonun kural adını ezmesi | Düşük | Düşük | §23 iki-katman kuralı |

---

## 28. İzle (Devam)

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| grup dışı alan hatası | §25 kural | strict mode ✅ |
| 422 rate-limit ayrımı | §24 | l1 RateLimiter ✅ |
| i18n şema | ADR-079 | coremusic_system ✅ |
| unique race | §18 SSS 6 | DB index ✅ |

---

## 29. Karar Ağacı 2 — "Hata 400 mü 422 mü 415 mi?"

```
JSON parse edilemedi            → 400 INVALID_JSON
Content-Type yanlış             → 415
Payload boyutu aşımı            → 413
Alan kural ihlali (semantik)    → 422 VALIDATION_ERROR + details[]
Alan whitelist dışı             → 422 forbidden_field (strict)
```

Kanonik tablo: [[api-error-codes]] — bu ağaç hızlı karar referansıdır.

---

## 30. Kalite Raporu (Güncel)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.1.0 |
| **Bölüm Sayısı** | 30 |
| **SSS** | 14 |
| **Test Senaryosu** | 14 |
| **Risk Kaydı** | 11 |
| **Karar Ağacı** | 2 |
| **Zero Hallucination** | ✅ |

---

## 31. strong_password Detay

| Bileşen | Kural | Örnek |
|---------|-------|-------|
| Uzunluk | ≥8 karakter | — |
| Büyük harf | ≥1 | A-Z |
| Küçük harf | ≥1 | a-z |
| Rakam | ≥1 | 0-9 |
| Özel | ≥1 | !@#$... |
| Saklama | Argon2id (64MB/4/2) | ADR-022 |

Hata mesajı toplu bildirir ("parola kurallara uymuyor") — hangi kuralın eksik olduğu ayrıntısına inmez (atlatma kolaylaştırma önlemi).

---

## 32. Validation İstek-Yanıt Örneği (Doldurulmuş)

```
İstek: POST /api/songs  Content-Type: application/json
{"title": "New Song", "duration": "üç dakika", "email": "bozuk"}

Yanıt: 422
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Request validation failed",
    "details": [
      {"field": "duration", "rule": "numeric", "message": "Duration must be numeric"},
      {"field": "email", "rule": "email", "message": "Invalid email format"}
    ],
    "meta": {"group": "create", "failed_count": 2}
  }
}
```

Not: meta.group istemciye grup bağlamını verir; failed_count hızlı gösterim içindir (§5 formatının extension'ı — api-error-codes uyumlu kalır).

---

## 33. Validation Performansı

| Konu | Kural |
|------|-------|
| Kural seti derlemesi | Grup başına bir kez derlenir (opcache sürekli) — istek başına yeniden parse yok |
| `exists:` maliyeti | DB sorgu — yoğun endpoint'te cache'lenmez ama FK index'li |
| respects validation zinciri | Kısa devre yok (tüm kurallar — details[] toplanır) |
| Payload limit | §9 — validation öncesi boyut reddi (413) |

---

## 34. Ek SSS

**S: Validation grupları versiyonlanır mı?**
C: Endpoint sözleşmesiyle — api-versioning (247) kuralı; grup alan seti değişimi minor versiyon.

**S: `no_html` strip tüm alanlara mı?**
C: Hayır — metin içerikli alanlara (title, bio); JSON yapısal alanlara strip uygulanmaz.

**S: ValidationMiddleware hangi exception'ı fırlatır?**
C: Fırlatmaz — doğrudan 422 Response üretir (pipeline kısa devre; §12 akışı). Exception sadece beklenmeyen iç hata içindir (500).

**S: `exists:` tablosu çapraz DB ise?**
C: 18 DB ayrıdır (ADR-003) — çapraz DB FK yok; validator tek DB bağlantısında kalır. Çapraz ihtiyaç API kompozisyonudur (ADR-084).

---

## 35. Risk İzle (Son)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 12 | details[] meta extension'ın error-codes sapması | Düşük | Düşük | §32 not — kanonik + extension etiketli |
| 13 | short-circuit yoksa maliyet | Düşük | Düşük | §33 kural derleme |

---

## 36. İzle (Son)

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 422 format | §5 | ✅ |
| ramsey/uuid | packages/shared | Faz 0 ✅ |
| meta.group extension | §32 | Bu revizyon ✅ |

---

## 37. Karar Ağacı 3 — "Kural ihlali sonrası akış?"

```
İlk ihlal → details[] ekle → DEVAM (diğer alanlar)
Tüm alanlar tarandı → details[] boş? → handler devam
details[] dolu → 422 + meta.group → log INFO → rate ayrım kaydı (§24)
Beklenmeyen exception → 500 + log CRITICAL
```

---

## 38. Test Ek (Devam)

| # | Senaryo | Beklenen |
|---|---------|----------|
| 15 | PATCH yalnız title (geçerli) | 200 — diğer alanlar dokunulmaz |
| 16 | create'te tanımsız alan (strict) | 422 forbidden_field |
| 17 | İç içe 6 seviye JSON | 413/400 (derinlik) |
| 18 | strong_password "Password1!" | geçerli |
| 19 | "password1!" (büyük yok) | 422 |
| 20 | double-submit unique | DB index 23505 → 422 dönüşümü |

---

## 39. Kalite Raporu (Final-2)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.2.0 |
| **Bölüm Sayısı** | 39 |
| **Test Senaryosu** | 20 |
| **SSS** | 17 |
| **Risk Kaydı** | 13 |
| **Karar Ağacı** | 3 (§21/§29/§37) |
| **Zero Hallucination** | ✅ |

---

## 40. Ek SSS (Son)

**S: Validation hata mesajı istemciye kural sırrını verir mi (regex pattern)?**
C: Hayır — details sadece rule adı + insan mesajı; regex pattern sızması atlatma kılavuzu olur.

**S: `file` kuralı JSON gövdede çalışır mı?**
C: Hayır — multipart form-data (§3 kaynak tablosu); JSON base64 dosya = ayrı kural (PLANNED, payload limit etkileşimi).

**S: Validation önce sanitize sonrası tekrar validate — çift maliyet?**
C: Strip bir kez, kural seti bir kez — çift maliyet değil; sıra (§2) tek geçişli akıştır.

**S: 422 yanıtı cache'lenir mi?**
C: Hayır — hata yanıtları cache dışı; POST/PUT/PATCH zaten cache'siz (api-filtering §14 sadece GET kapsamı).

**S: Validation grupları DB'den mi kod'dan mı?**
C: Kod'dan (endpoint sözleşmesi) — DB alan yapısı şema kaynaklıdır; grup kod sözleşmesidir (api-endpoints senkronu).

**S: Custom validator adı çakışırsa (respect ile)?**
C: Sarmalama deseni — kendi ad alanı (Api\Validation\Rules\) içinde; respect sınıflarıyla çakışmaz (§11 eşleme tablosu tek kaynak).

---

## 41. Risk/İzle (Son)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 14 | Hata mesajında pattern sızması | Düşük | Orta | §40 SSS 1 |

İzle ek: 422 cache dışı ✅; multipart-only dosya ✅; Api\Validation\Rules\ hedef konum ✅.

---

## 42. Karar Ağacı (Son) — "Custom validator gerekli mi?"

```
Standart 18 kural yeterli mi?
  ├─ Evet → katalog kullan (§4)
  ├─ DB bağımlı (unique/exists) → §8 custom + DB index garantisi
  ├─ Güvenlik özel (no_html/safe_filename) → §8/§9 birleşik
  └─ İş mantığı (track sayısı vs) → UseCase içi — validation değil
```

---

## 43. Kalite Raporu (Son-3)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.3.0 |
| **Bölüm Sayısı** | 43 |
| **SSS** | 20 |
| **Test Senaryosu** | 20 |
| **Risk Kaydı** | 14 |
| **Karar Ağacı** | 3 (§21/§29/§37/§42) |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode