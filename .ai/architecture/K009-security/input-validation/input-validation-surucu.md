---
node_id: K009-security/input-validation/input-validation-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/input-validation/input-validation-anayasa
evidence:
  - "shared/src/Api/Middleware/RequestValidationMiddleware.php:46-99 — gövde/query sürüşü (415/400/422)"
  - "shared/src/Middleware/ValidationMiddleware.php:17-83 — PageRouter alan sürüşü (422 halt)"
  - "shared/src/Security/ReturnUrlPolicy.php:127-183 — güvenli URL sürüşü ('/' varsayılanı)"
validation:
  - "2026-10-15 — sürüş akışları + red haritası + hata kolları; kanıt Dosya·Satır"
open_questions:
  - "PageRouter GET/query yüzeyinde doğrulama kanıtı yok ⚠️ VERIFICATION REQUIRED"
  - "Doğrulama kuralı olmayan uçların gerçek listesi okunmadı ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · input-validation · Sürücü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · mimari (sürüş akışları).
> Kurallar `input-validation-anayasa.md` · gerçek kod `input-validation-ornekler.md`.
> Bu dosya: girdinin **nasıl aktığını** ve **hangi hatanın hangi yanıta** dönüştüğünü gösterir.

## §1 AMAÇ VE KAPSAM

| Alan | Değer |
|---|---|
| node_id | K009-security/input-validation/input-validation-surucu |
| Grup | mimari |
| Kapsam | girdi → tip → format → iş kuralı → temizleme/red · red haritası · hata kolları |
| Kapsam dışı | kural tanımının sahibi (rota/şema) · yetki/kota kararları |

## §2 SÜRÜŞ SÖZLÜĞÜ

| Türkçe | English | Açıklama |
|---|---|---|
| Sürüş | Drive / execution path | Bir isteğin doğrulamadan geçme akışı |
| Red haritası | Rejection map | Hangi hata hangi HTTP yanıtına dönüşür |
| Hata kolu | Error branch | Red/yakalama yolunun sürülmesi |
| Temizleme | Sanitization | Girdinin biçimini düzeltme (doğrulamanın yerine geçmez) |
| Duraklatma | Halt | Zinciri durduran red (`halt: true`) |
| Pas geç | Pass-through | Kural yoksa/uygunsa devam |
| Sarmalayıcı | Wrapper | Middleware'in `$next` çağrısı |

## §3 ANA SÜRÜŞ AKIŞI (HAPPY PATH)

| Adım | Eylem | Sözleşme / Kanıt |
|---|---|---|
| 1 | Ham girdi alınır (body/query/redirect parametresi) | `RequestValidationMiddleware.php:81` (query) · `:72` (body) |
| 2 | İçerik biçimi kontrolü (API: Content-Type) | `RequestValidationMiddleware.php:60-70` → 415 |
| 3 | Gövde ayrıştırma (JSON/form) | `RequestValidationMiddleware.php:133-174` → 400 |
| 4 | Kural seti yüklenir (`_route['validation']` / `_route_meta['required_fields']`) | `RequestValidationMiddleware.php:48-49` · `ValidationMiddleware.php:26-27` |
| 5 | Tip kontrolü | `Validator.php:126-139` |
| 6 | Format kontrolü (regex / `filter_var`) | `Validator.php:78-82,141-144` · `ValidationMiddleware.php:59` |
| 7 | Değer (enum/host) kontrolü | `Validator.php:84-86` · `ValidationMiddleware.php:65-69` |
| 8 | Uzunluk / sınır kontrolü | `Validator.php:88-94` · `ValidationMiddleware.php:47-56` |
| 9 | İş kuralı katmanı (domain/DTO — bu çocukta değil) | `K011-application` / `K008-domain` |
| 10 | Temizleme (varsa) → geçerli akış `$next($request)` | `RequestNormalizer.php:31,49` (host/URI) · `:98` |
| 11 | Red varsa → harita (§4) → zincir durur | `RequestValidationMiddleware.php:88-96` · `ValidationMiddleware.php:72-79` |

| Durum | Sonraki |
|---|---|
| Kural yok / hepsi geçti | → adım 5 (veya `$next`) |
| Herhangi bir ihlal | → §4 red haritası (duraklat) |

## §4 RED HARİTASI (HANGİ HATA → HANGİ YANIT)

| # | Tetikleyici | HTTP | Kod | Gövde | Dosya · Satır |
|---|---|---|---|---|---|
| R-1 | Zorunlu alan eksik (PageRouter) | 422 | `validation_failed` | `{error, errors{alan: mesaj}}` + `halt` | `ValidationMiddleware.php:41-43,72-79` |
| R-2 | Uzunluk/email/enum ihlali (PageRouter) | 422 | `validation_failed` | aynı | `ValidationMiddleware.php:47-69,72-79` |
| R-3 | Kural ihlali (API) | 422 | `VALIDATION_ERROR` | `{error.fields{alan: mesaj}, error.details}` | `RequestValidationMiddleware.php:88-95` |
| R-4 | Bozuk JSON gövde | 400 | `INVALID_JSON` | `{error.reason}` | `RequestValidationMiddleware.php:73-77` |
| R-5 | Content-Type JSON değil (gövde metodu) | 415 | `UNSUPPORTED_MEDIA_TYPE` | `{error.content_type}` | `RequestValidationMiddleware.php:63-70` |
| R-6 | Zorunlu alan eksik (istisna yolu) | 422 | `EMPTY_FIELDS` | sabit Türkçe mesaj | `ValidationException.php:21-24` |
| R-7 | Geçersiz e-posta (istisna yolu) | 422 | `INVALID_EMAIL` | sabit mesaj | `ValidationException.php:32-35` |
| R-8 | Parola kısa (istisna yolu) | 422 | `PASSWORD_TOO_SHORT` | `min` değeri mesajda | `ValidationException.php:47-50` |
| R-9 | Geçersiz/kısa token (istisna yolu) | 422 | `INVALID_TOKEN` / `TOKEN_EXPIRED` | sabit mesaj | `ValidationException.php:37-45` |
| R-10 | Güvensiz return URL | 302 (redirect) | — | `Location: '/'` | `ReturnUrlPolicy.php:129-131,157-159,183` |
| R-11 | API anahtarı formatı uymuyor | — (repo ret) | — | `preg_match` 1 ≠ 1 → reddet | `ApiKeyRepository.php:233` |
| R-12 | Aşırı istek | 429 | (rate-limit) | — | **bu çocukta değil** → `[[rate-limiting-index]]` |
| R-13 | Yetki yetersiz | 403/401 | (auth/authz) | — | **bu çocukta değil** → `[[authorization-kapsam]]` · `[[authentication-kapsam]]` |

| Sıra Kuralı | |
|---|---|
| Önce içerik biçimi (415/400), sonra kural (422) | `RequestValidationMiddleware.php:60-96` sırası |
| Birden fazla alan ihlali tek yanıtta toplanır | `Validator::validate()` döngüsü (`:39-54`) · `ValidationException::multiple()` (`:26-30`) |
| İlk hata öncelikli (dizin döngüsü kuralı) | `ValidationMiddleware.php:41-69` `continue` zinciri |

## §5 HATA KOLLU SÜRÜŞ (FAILURE BRANCHES)

| # | Kolu | Tetik | Sonuç |
|---|---|---|---|
| H-1 | Kural yoksa pas geç | `!is_array($rules) \|\| $rules === []` | `$next` (`RequestValidationMiddleware.php:53-55`) ⚠️ U-3 |
| H-2 | Meta boşsa pas geç | `empty($requiredFields)` | `$next` (`ValidationMiddleware.php:29-31`) |
| H-3 | Salt okunur metodda pas geç | GET/HEAD/OPTIONS | `$next` (`ValidationMiddleware.php:22-24`) |
| H-4 | Gövde tipi array/string değil | test/önceden çözülmüş gövde | `unsupported_body_type` → 400 (`RequestValidationMiddleware.php:147`) |
| H-5 | `json_decode` başarısız | `!is_array($decoded)` | 400 (`:165-174` → `:73-77`) |
| H-6 | Sorgu verisi array değil | `!is_array($data)` | `[]`'e çekilir → kurallar veri yokluğunu görür (`:82-85`) |
| H-7 | `filter_var` false | host/URI/URL geçersiz | `''` / `/` (güvenli varsayılan) (`RequestNormalizer.php:169-176`, `ReturnUrlPolicy.php:157-159`) |
| H-8 | `parse_url` false | URL ayrıştırılamaz | `false` → red (`ReturnUrlPolicy.php:78-80`) |
| H-9 | Regex geçersiz/çökme | `@preg_match !== 1` | "invalid format" (hata yutulur → U-6) (`Validator.php:78-82`) |
| H-10 | `ValidationException` yükseltilirse | domain/controller yolu | `422` + `getErrors()` haritası (`ValidationException.php:10-19`) |

## §6 TEMİZLEME (SANITIZATION) SÜRÜŞÜ

| Girdi | İşlem | Dosya · Satır | Not |
|---|---|---|---|
| `HTTP_HOST` | `FILTER_SANITIZE_FULL_SPECIAL_CHARS` | `RequestNormalizer.php:31` | biçim temizliği — doğrulama DEĞİL |
| `REQUEST_URI` | `preg_replace` (index.php tekrarı) + `FILTER_SANITIZE_URL` | `RequestNormalizer.php:42-49` | katman temizliği |
| `REMOTE_ADDR` / XFF | `FILTER_VALIDATE_IP` + güvenilir proxy zinciri | `RequestNormalizer.php:103-131` | spoof koruması |
| `X-Forwarded-For` hop'ı | `preg_match` (IPv6 köşeli) + port aralığı | `RequestNormalizer.php:133-152` | format kontrolü |
| Return URL | `urldecode` + kontrol karakteri temizliği | `ReturnUrlPolicy.php:133,104-106` | yalnız ret kararı ÖNCESİNDE |
| API anahtarı | regex ile format ayrıştırma | `ApiKeyRepository.php:36,233` | uyumsuzsa ret |

| Kural | |
|---|---|
| Temizleme **doğrulamanın yerine geçmez** (anayasa IV-03) | temizle → sonra doğrula → geçerse kullan |
| `FILTER_SANITIZE_*` tek başına savunma sayılmaz | anayasa Y-02 |

## §7 İŞ KURALI ADIMI (KAPSAM SINIRI)

| Adım | Sahi | Durum |
|---|---|---|
| Alan kuralı (tip/format/değer) | bu çocuk | ✅ kanıtlı |
| İş kuralı (ör. "e-posta kayıtlı mı", "aynı kullanıcıya ait mi") | `K008-domain` / `K011-application` / repository | sınır referansı |
| Yetki ("bu alanları değiştirebilir mi") | `[[authorization-kapsam]]` | bu çocukta DEĞİL |
| Doğrulanmış girdi üzerinden sorgu (parametreli) | `K007-data` | anayasa §6 IV-16 |

## §8 SÜRÜŞ SINIFLARI

| Sınıf | Tanım | Örnek | Kanıt |
|---|---|---|---|
| K1 — Alan kurallı POST | route `validation` | register (5 alan) | `api.coremusic.net/config/routes.php:72-78` |
| K2 — Meta kurallı POST | `_route_meta.required_fields` | test senaryosu | `ValidationMiddlewareTest.php:42-57` |
| K3 — Salt okuma GET | doğrulama yok (PageRouter) | sayfa isteği | `ValidationMiddleware.php:22-24` ⚠️ U-4 |
| K4 — API GET query | query doğrulama | `?page=` int | `RequestValidationMiddleware.php:81` + `RequestValidationMiddlewareTest.php:151-163` |
| K5 — URL/redirect girdisi | host whitelist | login `return` | `ReturnUrlPolicy.php:25-89` |
| K6 — Girdi yok / boş | pas geç | — | `RequestValidationMiddleware.php:53-55` |

## §9 ÇAĞRI ZİNCİRİ (CALL CHAIN)

| Katman | Rol | Kanıt |
|---|---|---|
| Route tanımı | kural kaynağı | `api.coremusic.net/config/routes.php:59-64,72-78` |
| `PageRouterKernel::resolveRouteMeta()` | `required_fields` aktarımı | `PageRouterKernel.php:161-167` |
| `ValidationMiddleware` | zorunlu alan + format (PageRouter) | `ValidationMiddleware.php:17-83` |
| `Gateway` → `RequestValidationMiddleware` | gövde/query doğrulama (API) | `RequestValidationMiddleware.php:46-99` |
| `Validator` | kural motoru | `Validator.php:35-144` |
| `ValidationException` | istisna yolu (422) | `ValidationException.php:10-50` |
| `ReturnUrlPolicy` | redirect girdisi | `ReturnUrlPolicy.php:25-183` |
| Controller / `$next($request)` | iş kurduna geçiş | `RequestValidationMiddleware.php:98` |

| Sınır | Kural |
|---|---|
| Doğrulama katmanı iş kuralı **çözmez** | anayasa §7 |
| Controller doğrulamayı **tekrarlamaz** (kural rota/DTO'da) | anayasa IV-12 |

## §10 HATA TÜRÜ × SÜRÜŞ MATRİSİ

| # | Hata türü | Sürüş | Sonuç |
|---|---|---|---|
| 1 | Eksik zorunlu alan | kural döngüsü → `required` ihlali | 422 |
| 2 | Yanlış tip (`int` yerine string) | `typeError()` | 422 (`Validator.php:130-131`) |
| 3 | Yanlış format (regex/e-posta) | `preg_match` / `filter_var` | 422 |
| 4 | İzinsiz değer (enum/host) | `in_array(strict)` / host listesi | 422 / `'/'` |
| 5 | Uzunluk ihlali | `min`/`max` karşılaştırması | 422 |
| 6 | Bozuk JSON | `decodeJson` | 400 |
| 7 | Yanlış content-type | `isAcceptedContentType` | 415 |
| 8 | Açık redirect varyantı | `isProtocolRelative` / `hasControlChars` | `'/'` (`ReturnUrlPolicy.php:51-56`) |
| 9 | Tehlikeli şema (`javascript:`) | şema ret | `'/'` (`:169-171`) |
| 10 | Eksik route kuralı | pas geç | 200 (⚠️ U-3) |
| 11 | Regex kuralı bozuksa | `@preg_match` | 422 "invalid format" (U-6) |
| 12 | Aşırı istek | rate limit | 429 (sınır çocuk) |

## §11 DURUM GEÇİŞ SÜRÜCÜSÜ

| Geçiş | Koşul | Kanıt |
|---|---|---|
| RAW → AYRIŞTIRILDI | JSON/form ayrıştırma başarılı | `RequestValidationMiddleware.php:165-174` |
| AYRIŞTIRILDI → RED (400) | ayrıştırma başarısız | `:73-77` |
| AYRIŞTIRILDI → RED (415) | content-type kabul edilmedi | `:63-70` |
| AYRIŞTIRILDI → DENETLENDİ | `$errors === []` | `:88,98` |
| DENETLENDİ → RED (422) | `$errors !== []` | `:88-96` |
| DENETLENDİ → AKIŞ | `$next($request)` | `:98` |
| URL → GÜVENLİ | host listede + parse tutarlı | `ReturnUrlPolicy.php:76-88` |
| URL → RET (`'/'`) | herhangi bir ret kuralı | `ReturnUrlPolicy.php:129-183` |

## §12 GÖZLEMLENEBİLİR SÜRÜŞ (OBSERVABILITY)

| Sinyal | Nerede | Durum |
|---|---|---|
| Red yanıt kodu (400/415/422) | yanıt gövdesi (`error.code`) | ✅ kanıtlı (`RequestValidationMiddleware.php:66,74,92`) |
| Alan bazlı hata haritası | `errors` / `error.fields` | ✅ kanıtlı (`:93`, `ValidationMiddleware.php:76`) |
| Doğrulama redlerinin log'u | — | ⚠️ KANIT YOK (log çağrısı bu iki middleware'de yok) |
| Red metriği/sayaç | — | UNKNOWN |
| Güvensiz redirect denemesi log'u | — | ⚠️ KANIT YOK (`ReturnUrlPolicy` log çağırmaz) |

## §13 TEST SÜRÜŞÜ (MEVCUT KANIT)

| Test | Sürüş | Beklenen | Dosya · Satır |
|---|---|---|---|
| Eksik alan | POST + boş body | 422 + `validation_failed` | `shared/tests/ValidationMiddlewareTest.php:42-57` |
| Geçerli gövde | POST + geçerli alanlar | pas geç | `ValidationMiddlewareTest.php:59-74` |
| GET pas geç | GET + kural tanımlı | pas geç | `ValidationMiddlewareTest.php:76-86` |
| enum+email | bozuk değerler | iki hata anahtarı | `ValidationMiddlewareTest.php:88-105` |
| Bozuk JSON | `'{invalid'` | 400 `INVALID_JSON` | `RequestValidationMiddlewareTest.php:68-81` |
| Content-Type | `text/plain` | 415 `UNSUPPORTED_MEDIA_TYPE` | `RequestValidationMiddlewareTest.php:84-97` |
| GET query kuralı | `?page` int | 422 `VALIDATION_ERROR` | `RequestValidationMiddlewareTest.php:151-164` |
| Açık redirect | `//evil.com`, `/%2Fevil.com` | `false` | `Unit/Security/ReturnUrlPolicyTest.php:82-89` |
| Host reddi | `evil-coremusic.net` | `false` | `ReturnUrlPolicyTest.php:55-60` |
| Şema reddi | `javascript:` / `data:` | `'/'` | `Security/ReturnUrlPolicyEdgeCaseTest.php:20-33` |

## §14 SÜRÜŞ SENARYOLARI

| # | Senaryo | Adımlar | Sonuç |
|---|---|---|---|
| D-1 | Register, 5 alan geçerli | 415 yok → JSON ok → 5 kural geç | `$next` (200) |
| D-2 | Register, `username` 3 karakter altı | regex kuralı ihlal | 422 + `username` mesajı |
| D-3 | Register, `email` bozuk | `type=email` ihlal | 422 + `email` mesajı |
| D-4 | Register, `gender` izinsiz | `in` ihlal | 422 + değer listesi (`Validator.php:85`) |
| D-5 | `Content-Type: text/plain` | 415 red, kurala varılmaz | 415 |
| D-6 | Gövde `{broken` | 400 red | 400 |
| D-7 | Login `return=//evil.com` | protokol-relative ret | `Location: /` |
| D-8 | Login `return=https://evil.com/` | host listede değil | `Location: /` |
| D-9 | Login `return=https://auth.coremusic.net/x?r=%2Fhome` | host listede + decode tutarlı | izin |
| D-10 | PageRouter POST, meta boş | pas geç | `$next` ⚠️ U-2 |
| D-11 | PageRouter POST, zorunlu alan yok | kural ihlali | 422 + `halt` |
| D-12 | API GET, kural tanımlı | query doğrula | 422 (varsa) |

## §15 PERFORMANS SÜRÜŞÜ

| Faktör | Etki | Kural |
|---|---|---|
| Kural döngüsü alan sayısı × istek | CPU | kural listeleri rota ile sabit (`routes.php`) |
| İçerik biçimi kontrolü kural ÖNCESİNDE | gereksiz işi erken kes | `RequestValidationMiddleware.php:60-77` sırası |
| `urldecode` tekrarları (redirect) | küçük maliyet | bilinçli: ham + tek katman (`ReturnUrlPolicy.php:104`) |
| Regex (kullanıcı kuralı) | ReDoS riski | anayasa IV-13 (⚠️ sınır yok — U-5) |
| `mb_strlen` uzunlukta | çok baytlı string | `Validator.php:116` (Unicode doğru sayım) |

## §16 ORTAM SÜRÜŞÜ

| Ortam | Not | Etiket |
|---|---|---|
| Geliştirme | route kuralı tanımlı mı → davranışı belirler | ⚠️ U-3 |
| Test | PHPUnit ile 4xx sürüşleri kanıtlanmış | §13 ✅ |
| Production | hata gövdesi yayın mı — config okunmadı | UNKNOWN |
| CI | `composer test` kapısı — çalışma durumu doğrulanmadı | `AGENTS.md` §25.2 notu |

## §17 DENETİM SÜRÜŞÜ (SORULAR)

| # | Soru | Kaynak |
|---|---|---|
| 1 | Her uçta kural seti yükleniyor mu? | §3-4 adım 4 (⚠️ U-3) |
| 2 | Red haritası 4xx'te mi? | §4 → IV-15 |
| 3 | Temizleme doğrulamadan ÖNCE/SONRA mı sıralı? | §6 |
| 4 | GET yüzeyi doğrulanıyor mu? | §8 K3 ⚠️ U-4 |
| 5 | Redler loglanıyor/gözleniyor mu? | §12 ⚠️ |
| 6 | Redirect girdisi tek politikadan mı geçiyor? | `SecurityHelper.php:13-20` (SSOT) ✅ |

## §18 ÖNCELİKLİ MÜDAHALE

| Öncelik | Durum | Müdahale | Kaynak |
|---|---|---|---|
| HIGH | `required_fields` üreten route yoksa middleware pasif | rota kuralı tanımını doğrula | U-2 |
| HIGH | Doğrulama reddi log'u yok | gözlem ekle (plan) | §12 |
| MEDIUM | GET/query PageRouter yüzeyi doğrulanmamış | kapsam kararı | U-4 |
| MEDIUM | `@preg_match` hata yutması | hata yolunu netleştir | U-6 |
| LOW | ReDoS sınırı | kural inceleme kuralı | U-5 |

## §19 SÜRÜŞ ŞABLONLARI (PSEUDOCODE)

```text
ŞABLON-1 (API gövde)
  content-type?  yok/uygunsuz -> 415
  decode(json)?  bozuk         -> 400
  validate(data, rules)?
    hata var -> 422 {fields}
    yok     -> $next(request)

ŞABLON-2 (PageRouter alan)
  metod GET/HEAD/OPTIONS -> pas geç
  meta.required_fields boş -> pas geç
  alanlar: required -> min/max -> email -> enum
  hata var -> 422 halt
  yok -> $next(request)

ŞABLON-3 (redirect girdisi)
  boş/'/'          -> izin
  kontrol karakteri -> ret
  '/' başlı        -> protokol-relative ret, değilse izin
  decode != safe    -> ret
  raw/decode host ayrışıyor -> ret
  host listede      -> izin, değilse '/'
```

> Pseudocod'dur; gerçek kod `input-validation-ornekler.md` §-ilerisindedir.

## §20 DOĞRULAMA ADIMLARI (VERIFICATION DRIVE)

| # | Adım | Yöntem | Kriter |
|---|---|---|---|
| V-1 | Red haritası eşleşmesi | §4 satırları kodla okundu | 11/11 Dosya·Satır |
| V-2 | Sıra kuralı | content-type → decode → validate | `:60-96` ✅ |
| V-3 | Pas geç koşulları | 3 ayrı pas geç dalı | H-1/H-2/H-3 |
| V-4 | Güvenli varsayılan | `'/'` geri dönüşleri | `ReturnUrlPolicy.php:129-183` |
| V-5 | GET yüzeyi | PageRouter query doğrulama taraması | 0 → U-4 |
| V-6 | Log taraması | iki middleware'de log çağrısı | 0 → §12 |
| V-7 | Kural üreteci | `required_fields` route tanımı taraması | yalnız test+kernel → U-2 |

## §21 BİLİNMEYENLER

| # | Soru | Etiket |
|---|---|---|
| U-1 | Red logu/metrik toplanıyor mu | UNKNOWN |
| U-2 | Üreten route tanımı var mı (grep 0) | ⚠️ VERIFICATION REQUIRED |
| U-3 | Kural tanımsız uçların gerçek listesi | ⚠️ VERIFICATION REQUIRED |
| U-4 | PageRouter GET/query doğrulaması | ⚠️ KANIT YOK |
| U-5 | Kural motoru sırası ile anayasa §5 sırası birebir mi? (tip→regex→in→min/max vs tip→uzunluk→format→değer) | ⚠️ sıralama farkı — `Validator.php:63-97` |

## §22 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[input-validation-anayasa]] | üst | kurallar (IV-01..IV-16) |
| [[input-validation-kapsam]] | yan | yüzey × katman matrisi (§5) |
| [[input-validation-ornekler]] | yan | gerçek kod alıntıları |
| [[input-validation-dogrulama]] | yan | AD-xx kanıt tablosu |
| [[input-validation-bilesenler]] | yan | bileşen envanteri + diyagram |
| [[input-validation-web-arastirma]] | yan | 8 URL kaynak tablosu |
| [[rate-limiting-index]] | öncesi | 429 kotası |
| [[authorization-kapsam]] | sonrası | 403/401 kararı |
| [[security-bilesenler]] | referans | B-07 ValidationMiddleware |

## §22.1 SÜRÜŞ — KARAR ÖZETİ (her turda önce oku)

| # | Karar | Seçim | Gerekçe (kanıt) |
|---|---|---|---|
| C-1 | Hata kodu ailesi | 415 (medya) · 400 (JSON) · 422 (kural) · `/` (redirect fail-safe) | `RequestValidationMiddleware.php:63-96` · `ReturnUrlPolicy.php:183` |
| C-2 | Kural kaynağı | route `validation` (API) + `_route_meta.required_fields` (PageRouter) — **ikili** | `routes.php:59-108` · `PageRouterKernel.php:165-167` |
| C-3 | Mirror çifti | korunur (kasıtlı); tekilleşme ADR ister | anayasa U-1 · bilesenler §5 |
| C-4 | Red detayı | alan adı + kural mesajı döner; iç detay dönmez | `Validator.php:45,80,85` · anayasa §10 |
| C-5 | Fail-safe yönü | reddet-varsaylan (`'/'`, `false`, 4xx) — fail-open yasak | anayasa §9 |

## §23 REVİZYON / SÜRÜM

| Alan | Değer |
|---|---|
| 2026-10-15 | İlk yazım — red haritası 11 satır + 7 doğrulama adımı |
| Versiyon | 1.0.0 |
| Durum | draft (U-1..U-5 açık) |
| Erişim | `.ai/architecture/K009-security/input-validation/input-validation-surucu.md` |
| `git commit` | ATILMADI (subagent yetkisi yok) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
