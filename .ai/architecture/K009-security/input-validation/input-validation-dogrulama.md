---
node_id: K009-security/input-validation/input-validation-dogrulama
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/input-validation/input-validation-anayasa
  - K009-security/input-validation/input-validation-ornekler
evidence:
  - "shared/src/Middleware/ValidationMiddleware.php:41-80 (5 kural + 422)"
  - "shared/src/Api/Middleware/Validator.php:43-94,126-144 (kural motoru)"
  - "shared/src/Security/ReturnUrlPolicy.php:7-15,157-183 (whitelist)"
  - "shared/tests/Unit/Security/ReturnUrlPolicyEdgeCaseTest.php:163-193 (bütünlük testi)"
validation:
  - "2026-10-15 — ≥150 satır · ≥10 AD-xx satırı (Kontrol|Beklenen|Dosya·Satır|Durum) · diskten okuma"
open_questions:
  - "⚠️ AD-11: DTO doğrulama yok (LoginRequest:52-59) — ADR kararı bekliyor."
  - "⚠️ AD-12: required_fields üreten production route grep 0."
  - "⚠️ AD-14/15: SSRF outbound + takvim kanıtı yok (anayasa U-7/U-4)."
group: kanit
---

# K009 · input-validation · Doğrulama

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · rol: qa-engineer (kanıt satırı).
> Her AD-xx satırı **Kontrol | Beklenen | Dosya·Satır | Durum** şemasındadır; Durum
> ✅ (diskten kanıtlandı) · ⚠️ (kanıt yok/açık) · ❌ (ihlal) olarak işaretlenir.
> Beklenen değer anayasa maddesinden türetilir; Dosya·Satır 2026-10-15 `read`/`grep` ile doğrulandı.

## §1 DOĞRULAMA ADIMLARI (AD-01..AD-18)

### AD-01 — Host whitelist (redirect yüzeyi)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| `ALLOWED_HOSTS` sabit liste mi? | 7 host (coremusic + 4 subdomain + localhost + 127.0.0.1) | `shared/src/Security/ReturnUrlPolicy.php:7-15` | ✅ |
| Liste dışı host reddediliyor mu? | `getSafeUrl` → `'/'` | `ReturnUrlPolicy.php:177-183` | ✅ |
| Alt alan adı eşleşmesi nokta-prefiksli mi? (`evil-coremusic.net` kaçmaz) | `str_ends_with($host, '.' . $allowed)` | `ReturnUrlPolicy.php:178` | ✅ |
| Davranış testi var mı? | whitelist tamı + non-whitelist bloğu | `shared/tests/Security/ReturnUrlPolicyEdgeCaseTest.php:163-193` | ✅ |

### AD-02 — Enum / değer listesi

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Mirror enum strict `in_array` mi? | `in_array($value, $rules['enum'], true)` | `ValidationMiddleware.php:65` | ✅ |
| API `in` kuralı strict mi? | `in_array($value, $rule['in'], true)` | `Validator.php:84` | ✅ |
| İhlalde izinli değerler dönüyor mu? | `implode(', ', ...)` mesajı | `ValidationMiddleware.php:66-67` · `Validator.php:85` | ✅ (⚠️ surucu D-08: kural bilgisi istemciye açık) |

### AD-03 — E-posta formatı (`filter_var`)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Mirror email kontrolü | `filter_var($value, FILTER_VALIDATE_EMAIL)` | `ValidationMiddleware.php:59` | ✅ |
| Kural motoru email tipi | `isEmail` → `filter_var(...)` | `Validator.php:141-144` (çağrı `:136`) | ✅ |
| Test kanıtı | `email: 'bozuk'` → errors'ta | `shared/tests/Middleware/validationMiddlewareTest.php` yerine: `shared/tests/Middleware/ValidationMiddlewareTest.php:88-105` | ✅ |

### AD-04 — Tip denetimi

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| `match` ile tip kontrolü | string/int/float/bool/array/email + default | `Validator.php:126-139` | ✅ |
| Tanımsız tip ne yapıyor? | `default => null` → **sessiz geç** | `Validator.php:137` | ⚠️ (surucu D-04: fail-closed değil — kural tanımsızsa kısıt yok) |
| Obje/array value ret ediliyor mu? | mirror'da `is_string`/`is_scalar` şartları devrede | `ValidationMiddleware.php:47,53,59` | ✅ (yalnız string kurallar; obje value `required` geçerse enum'a düşer — surucu D-03) |

### AD-05 — Uzunluk (min/max)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Mirror min/max | `strlen < / >` + `is_string` | `ValidationMiddleware.php:47,53` | ✅ |
| Motor min/max | sayısal → değer, string → `mb_strlen` | `Validator.php:88-94,102-124` | ✅ |
| Route'ta uygulama | `password min => 8` | `api.coremusic.net/config/routes.php:62,75,98` | ✅ |

### AD-06 — 415 Unsupported Media Type

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Body methodunda content-type kontrolü | POST/PUT/PATCH → 415 | `RequestValidationMiddleware.php:58-70` | ✅ |
| Kabul listesi | problem+json · x-www-form-urlencoded (`str_contains`) | `RequestValidationMiddleware.php:116-123` | ✅ |
| GET'te 415 tetiklenir mi? | hayır (yalnız body methodları) | `RequestValidationMiddleware.php:58,60` | ✅ (beklenen davranış) |

### AD-07 — 400 bozuk JSON

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| `json_decode` başarısızsa | 400 `INVALID_JSON` + `reason` | `RequestValidationMiddleware.php:72-77,165-174` | ✅ |
| `reason` sızıntısı | yalnız `json_last_error_msg()` (PHP mesajı) | `RequestValidationMiddleware.php:170` | ⚠️ (içerik ayrıntısı istemciye gidiyor — surucu D-09, hata seviyesi düşük) |

### AD-08 — 422 + `error.fields`

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Kural ihlali → 422 + alan haritası | `ApiResponse::error('VALIDATION_ERROR', ..., 422, $errors)` + `error.fields` | `RequestValidationMiddleware.php:87-96` | ✅ |
| Mirror ihlal → 422 + `halt` | `httpStatus 422`, `body.errors` | `ValidationMiddleware.php:72-80` | ✅ |
| İstisna yolu → 422 | `parent::__construct(..., 422)` | `ValidationException.php:12` | ✅ |
| Test kilidi | `assertSame(422, ...)`, `halt` true | `ValidationMiddlewareTest.php:42-57` | ✅ |

### AD-09 — Redirect yüzeyi: scheme ret

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| `javascript`/`data`/`vbscript` ret | `in_array` → `'/'` | `ReturnUrlPolicy.php:169-171` | ✅ |
| Test kanıtı | 3 scheme → `'/'` | `ReturnUrlPolicyEdgeCaseTest.php:20-33` | ✅ |

### AD-10 — Protokol-relative + userinfo + authority

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| `//` ve `/\` ret | iki aday (ham + tek decode), `\t\n\r` temizliği kontrolün önünde | `ReturnUrlPolicy.php:102-112` | ✅ |
| Kontrol karakteri ret (ham + decode) | `strpbrk` iki kez | `ReturnUrlPolicy.php:39-41,121-125` | ✅ |
| Userinfo ret | `user`/`pass` parse'da varsa false | `ReturnUrlPolicy.php:81-83` | ✅ |
| Authority sapması ret | ham vs decode host eşitliği | `ReturnUrlPolicy.php:84-86` | ✅ |
| Test kanıtı | `/%09/evil.com` · `%23@evil.com` varyantları | `ReturnUrlPolicyTest.php:74-93,107-133` | ✅ |

### AD-11 — DTO doğrulama (NEGATİF kanıt)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| `LoginRequest::fromArray` doğrulama üretiyor mu? | anayasa IV-03'e göre üretmeli **veya** katman üstünde uygulanmalı | `shared/src/Api/Dto/Request/LoginRequest.php:52-59` (`?? ''` fallback, `$errors` YOK) | ⚠️ DTO'da doğrulama yok — doğrulama route `validation` katmanında (`routes.php:59-64`); DTO tek başına güvensiz → ADR kararı bekliyor |

### AD-12 — `required_fields` üreten route (ÜRETİM)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Production route `required_fields` tanımlıyor mu? | en az 1 route | grep 2026-10-15: yalnız `shared/tests/Middleware/ValidationMiddlewareTest.php` (4 isabet) + `PageRouterKernel.php:162-166` (taşıyıcı) + `ValidationMiddleware.php:11,27` (tüketici) | ⚠️ KANIT YOK — mirror pratikte meta boşsa pasif (`ValidationMiddleware.php:29-31`) |
| Kernel taşıyıcısı var mı? | `resolveRouteMeta` `required_fields` üretir | `PageRouterKernel.php:152-169` (ata `:109`) | ✅ (kanal hazır, kaynak boş — anayasa U-2) |

### AD-13 — API anahtarı formatı

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Sabit desen (whitelist regex) | `cm_live\|cm_test` + 8 prefix + ≥16 gövde | `shared/src/Repository/ApiKeyRepository.php:36` | ✅ |
| Uygulama | `preg_match(RAW_KEY_PATTERN, ...) !== 1` → ret | `ApiKeyRepository.php:233` | ✅ |

### AD-14 — SSRF outbound denetimi

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Dış çağrı hedefi girdiden türetiliyorsa host/şema listesi | anayasa IV-11 | grep `SSRF` in `shared/src` → **0 isabet** (2026-10-15) | ⚠️ KANIT YOK (anayasa U-7 · R-18 · kök `security-dogrulama` D-31 UNKNOWN) |

### AD-15 — Takvim (calendar) doğrulaması

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Tarih girdisi geçerli gün/ay/yıl mı? | anayasa IV-07 | `Validator`'da `date` kuralı **YOK** (`Validator.php:63-97` yalnız type/regex/in/min/max) | ⚠️ KANIT YOK (anayasa U-4) |

### AD-16 — ReDoS / regex güvenliği

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Route regex'leri lineer mi (şimdilik)? | üstel geri dönüşlü olmamalı (IV-13) | `routes.php:73` `/^[a-zA-Z0-9_]{3,30}$/` | ✅ (bu desen için) |
| Genel ReDoS taraması / sınır kuralı | repo kuralı | — | ⚠️ KANIT YOK (anayasa U-5) |
| `@preg_match` hata yutması | hata da reddettirmeli (IV-14) | `Validator.php:79` (`@` + `!== 1`) | ❌ kısmi ihlal → U-6; `false` (desen bozuksa) hata üretir ama uyarı yutulur |

### AD-17 — GET/query yüzeyi

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Mirror GET atlıyor mu? | `GET/HEAD/OPTIONS` → pas geç | `ValidationMiddleware.php:22-24` | ✅ (beklenen davranış, surucu D-10 — ama anayasa U-8 olarak açık) |
| API GET query doğrulanıyor mu? | route `validation` varsa evet | `RequestValidationMiddleware.php:80-85,46-55` | ✅ |
| PageRouter'da query doğrulama kanıtı | zorunluluk tartışması | — (kodda yok) | ⚠️ KANIT YOK (anayasa U-8) |

### AD-18 — Test kapsamı (davranış kilitleri)

| Kontrol | Beklenen | Dosya·Satır | Durum |
|---|---|---|---|
| Mirror 5 davranış testi | boş meta · 422 · geçerli gövde · GET atla · enum+email | `ValidationMiddlewareTest.php:30-105` | ✅ |
| Return URL testleri (2 dosya) | scheme/userinfo/protokol-relative/kontrol char/whitelist tamı | `ReturnUrlPolicyTest.php:10-141` · `ReturnUrlPolicyEdgeCaseTest.php:20-193` | ✅ |
| API 415/400/422 yolu testi | 415 (2 test) · 400 · 422 (2 test) · GET query · pas geç | `shared/tests/Api/RequestValidationMiddlewareTest.php:41-164` (214 satır, 9 test) | ✅ |
| Kural motoru (`Validator`) testi | tip mismatch + min standalone + non-builtin return type pin'i | `RequestValidationMiddlewareTest.php:132-149,171-213` | ✅ (aynı dosyada; ayrı `ValidatorTest` yok → YETERLİ, ek `regex/in` testleri ⚠️ açık) |
| Regex/enum kural testleri | `regex`/`in` yolları için doğrudan test | — | ⚠️ KAPSAM AÇIĞI (dolaylı: enum mirror'da `ValidationMiddlewareTest.php:88-105`) |
| `@preg_match` bozuk desen testi | U-6 davranış kilidi | — | ⚠️ KAPSAM AÇIĞI |

## §2 ÖZET MATRİSİ

| Durum | Adım sayısı | Adımlar |
|---|---|---|
| ✅ KANITLI | 12 | AD-01, AD-02, AD-03, AD-05, AD-06, AD-08, AD-09, AD-10, AD-13, AD-17(API) · AD-18 (9+5 test dosyası) · AD-04/AD-07 alt satırları ✅ |
| ⚠️ AÇIK / KAPSAM DIŞI | 6 | AD-04(default geç), AD-07(reason sızıntısı), AD-11(DTO), AD-12(route kaynağı), AD-14(SSRF), AD-15(takvim), AD-16(ReDoS), AD-18(regex/`@preg_match` açığı) |
| ❌ İHLAL | 1 | AD-16 `@preg_match` (kısmi — IV-14) |

> Sayım notu: AD-04/AD-07/AD-17 satırları hem ✅ hem ⚠️ taşır (alt satır bazında);
> üstel tablo alt satır Durum'larını sayar.

## §3 ADAY TESTLER (surucu §7 adım 4 için — yazılmadı, görev tanımı gereği)

| # | Test | Hedef | Öncelik |
|---|---|---|---|
| T-1 | `regex`/`in` kural yolları + `@preg_match` bozuk desen kilidi | `Validator.php:78-86` (doğrudan test yok — AD-18) | HIGH |
| T-2 | Route `required_fields` meta tanımı (üretim kaynağı) | mirror'ı aktive eder (AD-12) | HIGH |
| T-3 | ReDoS taraması (route regex envanteri) | anayasa U-5 (AD-16) | MEDIUM |

## §4 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[input-validation-anayasa]] | üst | Beklenen değerlerin kaynağı (IV maddeleri) |
| [[input-validation-ornekler]] | kardeş | Dosya·Satır alıntılarının tam metni |
| [[input-validation-surucu]] | kardeş | §7 doğrulama adımları bu dosyanın girdisidir |
| [[security-dogrulama]] | üst | kök denetim satırları (D-31 SSRF UNKNOWN) |

## §5 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — AD-01..AD-18 · ✅11 / ⚠️6 / ❌1 · 4 aday test | Vault Steward (subagent) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
