---
node_id: K009-security/input-validation/input-validation-bilesenler
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/input-validation/input-validation-kapsam
  - K009-security/input-validation/input-validation-anayasa
evidence:
  - "shared/src/Middleware/ValidationMiddleware.php (84 satır — 2026-10-15 read)"
  - "shared/src/Api/Middleware/RequestValidationMiddleware.php (175 satır) + Validator.php (145 satır)"
  - "shared/src/Security/ReturnUrlPolicy.php (185 satır) + SecurityHelper.php (56 satır)"
  - "shared/src/PageRouter/PageRouterKernel.php:109,152-169 + RequestNormalizer.php:31,49"
validation:
  - "2026-10-15 — ≥150 satır · 10 bileşen envanteri · satır sayıları diskten"
open_questions:
  - "⚠️ Validator `regex/in` kural yolları + `@preg_match` bozuk desen için doğrudan test yok (dogrulama AD-18, T-1)."
  - "⚠️ B-10 routes.php: 5 uçta validation, 7 uçta yok — kapsam kararı bekliyor (kapsam K-4/§8)."
  - "⚠️ B-4/B-6/B-7/B-9 test kapsamı bu görevde aranmadı (UNKNOWN)."
group: mimari
---

# K009 · input-validation · Bileşenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · rol: docs-writer (envanter).
> Bu dosya girdi doğrulama zincirinin **bileşen envanteri** ve bağlantı diyagramıdır;
> davranış kuralları [[input-validation-anayasa]], kanıt satırları
> [[input-validation-dogrulama]] içindedir.

## §1 BİLEŞEN ENVANTERİ (10 bileşen)

| # | Bileşen | Dosya | Satır | Rol | Katman |
|---|---|---|---|---|---|
| B-1 | `ValidationMiddleware` | `shared/src/Middleware/ValidationMiddleware.php` | 84 | PageRouter pipeline doğrulayıcı (`required_fields`) | A1 middleware |
| B-2 | `RequestValidationMiddleware` | `shared/src/Api/Middleware/RequestValidationMiddleware.php` | 175 | API uç doğrulayıcı: 415/400/422 zinciri | A2 API gateway |
| B-3 | `Validator` | `shared/src/Api/Middleware/Validator.php` | 145 | Bağımlılıksız kural motoru (type/regex/in/min/max) | A2 kural |
| B-4 | `ValidationException` | `shared/src/Exception/ValidationException.php` | 51 | 422 istisna + fabrika metodları | A2 istisna |
| B-5 | `ReturnUrlPolicy` | `shared/src/Security/ReturnUrlPolicy.php` | 185 | Redirect URL host/şema/kontrol-char politikası (SSOT) | A1 güvenlik |
| B-6 | `SecurityHelper` | `shared/src/Security/SecurityHelper.php` | 56 | `isRedirectUriSafe` → B-5'e yönlendirme (SSOT kapısı) | A1 güvenlik |
| B-7 | `RequestNormalizer` | `shared/src/PageRouter/RequestNormalizer.php` | 177 | Host/URI/IP sanitization (`FILTER_SANITIZE_*`) | A2 router |
| B-8 | `PageRouterKernel` | `shared/src/PageRouter/PageRouterKernel.php` | 351 (kanıt: 109, 150-184) | `_route_meta` üreteci (`required_fields` taşıyıcısı) | A2 router |
| B-9 | `ApiKeyRepository` | `shared/src/Repository/ApiKeyRepository.php` | 317 (kanıt: 36, 233) | API anahtarı format regex'i (`RAW_KEY_PATTERN`) | A0 repository |
| B-10 | Route validation tablosu | `api.coremusic.net/config/routes.php` | 120 (validation: 59,72,86,96,108) | Uç bazlı kural kaynağı (5 uç) | A2 config |

> Satır notu: tüm satır sayıları 2026-10-15 disk ölçümüdür (`read`/`grep ^}$`).

## §2 ÇALIŞMA ZAMANI BAĞLANTI DİYAGRAMI

```text
HTTP istek
  │
  ├─ PageRouter yolu ──────────────────────────────────────────────┐
  │   PageRouterKernel (:109)                                      │
  │     └─ resolveRouteMeta (:152-169) → _route_meta               │
  │          ├─ requiredRole / requiredPermission  (yetki — B X-3) │
  │          └─ required_fields  ← route meta (⚠️ üretimde boş)     │
  │                 │                                              │
  │   ValidationMiddleware (B-1) <─────────────────────────────────┤
  │     ├─ GET/HEAD/OPTIONS → pas geç (:22-24)                     │
  │     ├─ required_fields boş → pas geç (:29-31)  ⚠️ U-2          │
  │     ├─ 5 kural (:36-70)                                        │
  │     └─ ihlal → 422 + halt (:72-80)                             │
  │                                                                │
  ├─ API yolu ─────────────────────────────────────────────────────┤
  │   routes.php (B-10) → route['validation']                      │
  │     │                                                          │
  │   RequestValidationMiddleware (B-2)                            │
  │     ├─ kural yok → pas geç (:51-55)  ⚠️ U-2                    │
  │     ├─ body: 415 (:63-70) → 400 (:72-77)                       │
  │     ├─ Validator (B-3) çağrısı (:87)                           │
  │     │    ├─ type (:126-139) · regex (:78-82) · in (:84-86)     │
  │     │    └─ min/max (:88-94) · @preg_match ⚠️ (:79)            │
  │     └─ ihlal → 422 + error.fields (:87-96)                     │
  │           └─ (domain yolu) ValidationException (B-4) → 422     │
  │                                                                │
  ├─ Redirect yolu ────────────────────────────────────────────────┤
  │   SecurityHelper (B-6) :18-20                                  │
  │     └─ ReturnUrlPolicy (B-5)                                   │
  │          ├─ isAllowed (:25-89): char/host/raw-decode           │
  │          └─ getSafeUrl (:127-184): scheme ret + whitelist      │
  │                                                                │
  ├─ Host/URI normalizasyonu: RequestNormalizer (B-7) :31,:49      │
  └─ API anahtarı: ApiKeyRepository (B-9) :36,:233                 │
```

## §3 BİLEŞEN × SORUMLULUK MATRİSİ

| Bileşen | Tip | Uzunluk | Format | Değer | Şema/host | Medya tipi | Hata kodu |
|---|---|---|---|---|---|---|---|
| B-1 ValidationMiddleware | — | ✅ | ✅ (email) | ✅ | — | — | 422 |
| B-2 RequestValidationMiddleware | — | — | — | — | — | ✅ | 415/400/422 |
| B-3 Validator | ✅ | ✅ | ✅ (regex) | ✅ (in) | — | — | 422 (hata haritası) |
| B-5 ReturnUrlPolicy | — | — | ✅ (URL) | ✅ (host) | ✅ | — | fail-safe `'/'` / false |
| B-7 RequestNormalizer | — | — | ✅ (SANITIZE) | — | ✅ (host) | — | fallback (`'localhost'`/`'/'`) |
| B-9 ApiKeyRepository | — | ✅ (regex `{16,}`) | ✅ | ✅ (prefix) | — | — | ret (istisna/değer) |

## §4 BAĞIMLILIK YÖNLERİ (katman kuralları)

| Bağımlılık | Yönlü | Not |
|---|---|---|
| B-1 → `_route_meta` (B-8) | altyapı → middleware | kernel üretir, middleware tüketir |
| B-2 → B-3 (Validator) | enjeksiyon (`__construct(?Validator)`) | `RequestValidationMiddleware.php:36-41` |
| B-2 → B-10 (routes.php) | config → middleware | `$request['_route']` üzerinden (`:48-49`) |
| B-6 → B-5 | facade → SSOT | `SecurityHelper.php:20` (`return ReturnUrlPolicy::isAllowed(...)`) |
| B-4 → BaseCoreMusicException | istisna hiyerarşisi | `ValidationException.php:5,12` |
| B-3 → B-4 | **yok** | Validator hata döndürür, fırlatmaz (`Validator.php:33,56`) — bilinçli ayrım |
| B-1 → B-4 | **yok** | Mirror istisna fırlatmaz, array döndürür (`ValidationMiddleware.php:73-79`) |

**Yasak bağımlılık (anayasa §6 IV-12):** B-1/B-3 içinde rota başına hardcoded kural
`match`/`if` bloğu **eklenemez** — kural kaynağı B-10 (route tablosu) veya B-8 (meta)
olmalıdır.

## §5 AYNI İŞİ YAPAN ÇİFTLER (mirror — kasıtlı, ama izlenmeli)

| Çift | Fark | Risk |
|---|---|---|
| B-1 (mirror) vs B-2 (API) | B-1 `_route_meta` okur, B-2 `_route` `validation` okur; B-1 GET'i atlar, B-2 GET query'yi de doğrular | **İki ayrı kural seti** — bir uç iki kez veya hiç doğrulanabilir (anayasa U-1/U-2) |
| B-3 (motor) vs B-1 kural döngüsü | B-3 associative `['required'=>..]` şeması; B-1 `['required','min_length','label']` şeması | **Farklı kural sözdizimi** — şema tek kaynaktan değil (anayasa U-1) |
| B-5 (SSOT redirect) vs B-6 (facade) | B-6 yalnız delegasyon | kasıtlı — çakışma yok ✅ |

## §6 TEST KAPSAMI (bileşen bazında)

| Bileşen | Test dosyası | Durum |
|---|---|---|
| B-1 | `shared/tests/Middleware/ValidationMiddlewareTest.php` (106 satır, 5 test) | ✅ |
| B-2 | `shared/tests/Api/RequestValidationMiddlewareTest.php` (214 satır, 9 test — 415/400/422 + GET query) | ✅ |
| B-3 | aynı dosyada (`testTypeMismatch...:132` · `testValidatorStandalone...:202` · return type pin `:171`) | ✅ (ayrı dosya yok; `regex/in` doğrudan testi ⚠️ eksik) |
| B-5 | `shared/tests/Unit/Security/ReturnUrlPolicyTest.php` (141 satır) + `shared/tests/Security/ReturnUrlPolicyEdgeCaseTest.php` (194 satır) | ✅ |
| B-4/B-6/B-7/B-9 | — (dolaylı kapsam) | ⚠️ UNKNOWN — bu görevde aranmadı |

## §7 DEĞİŞİKLİK KURALLARI (bu bileşenlere dokunurken)

| # | Kural | Kaynak |
|---|---|---|
| 1 | B-5 whitelist'e host ekleme/çıkarma → ADR + test (B-5 testleri kilitli) | `.ai/AGENTS.md` frozen/ADR kuralı |
| 2 | Yeni kural eklemek → B-3 `fieldErrors` + B-1 döngüsü **aynı anda** (ikisi değilse drift) | anayasa IV-06/IV-12 |
| 3 | Middleware sırası değişikliği → ADR-020 (Pipeline sabit) | `api.coremusic.net/AGENTS.md` §4 |
| 4 | B-10'a yeni uç → `validation` + test üçlüsü | `api.coremusic.net/AGENTS.md` §5 (Backend↔QA) |
| 5 | B-8 meta anahtarı değişikliği → B-1 sözleşmesi kırılır (B-F-13 regresyonu) | `ValidationMiddlewareTest.php:9-18` |

## §8 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[index]] | üst | künye |
| [[input-validation-kapsam]] | üst | hangi bileşen hangi yüzeyde (§2 W-1..W-11) |
| [[input-validation-anayasa]] | kardeş | davranış kuralları |
| [[input-validation-ornekler]] | kardeş | bileşen alıntıları (tam metin) |
| [[input-validation-dogrulama]] | kardeş | kanıt satırları |
| [[security-bilesenler]] | üst | kök envanter (B-07 ValidationMiddleware) |

## §9 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 10 bileşen · diyagram · mirror çifti analizi | Vault Steward (subagent) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
