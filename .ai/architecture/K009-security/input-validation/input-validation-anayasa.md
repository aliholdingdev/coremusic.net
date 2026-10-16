---
node_id: K009-security/input-validation/input-validation-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/input-validation
evidence:
  - "shared/src/Security/ReturnUrlPolicy.php:7-15 — ALLOWED_HOSTS beyaz listesi (7 host)"
  - "shared/src/Middleware/ValidationMiddleware.php:59/65 — filter_var(FILTER_VALIDATE_EMAIL) + in_array(strict) enum"
  - "shared/src/Api/Middleware/Validator.php:78-86 — regex + in whitelist kural motoru"
  - "api.coremusic.net/config/routes.php:72-78 — register ucu route validation kuralları"
validation:
  - "2026-10-15 — disk okuması; anayasa ≥350 satır · kanıt Dosya·Satır · uydurma 0"
open_questions:
  - "Doğrulama kurallarının tek kaynaktan (schema) yönetimi kanıtlanmadı ⚠️ VERIFICATION REQUIRED (index §2)"
  - "Çıktı kaçışı (escaping) katmanının tam kapsamı okunmadı ⚠️ VERIFICATION REQUIRED (index §2)"
  - "SSRF dış çağrı denetimi kodda kanıtlanmadı ⚠️ VERIFICATION REQUIRED (R-18 / D-31)"
group: mimari
---

# K009 · input-validation · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · mimari (bağlayıcı kurallar).
> Sürüş akışı `input-validation-surucu.md` · gerçek kod `input-validation-ornekler.md` ·
> sınır `input-validation-kapsam.md` · kanıt `input-validation-dogrulama.md`.

## §1 AMAÇ VE KAPSAM

| Alan | Değer |
|---|---|
| node_id | K009-security/input-validation/input-validation-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | whitelist ilkesi · tip/uzunluk/format/takvim kontrolü · sunucu tarafı zorunluluk · reddet-varsaylan · hata sızdırmazlığı · redirect/SSRF girdi yüzeyi |
| Kapsam dışı | yetki kararı (`[[authorization-kapsam]]`) · kota (`[[rate-limiting-index]]`) · kimlik modeli (K010) · SQL injection testi (`security-testing` — index §2) |

## §2 ROL — İLK SAVUNMA HATTI (index §1'e sadık)

| Adım | Ne olur | Kaçınma |
|---|---|---|
| 1 | Ham girdi alınır | Kaynağa güvenmek yasak |
| 2 | Tür/uzunluk/biçim kontrolü | `filter_var` / şema |
| 3 | İzin listesi (whitelist) uygulanır | Denetleme listesi (blacklist) yetersiz |
| 4 | Parametreli sorgu | SQL birleştirme yasak (`SELECT *` yasak) |
| 5 | Çıktı kaçışı | HTML/JS bağlamına göre escape |
| 6 | Redde | Geçersizse 4xx, ayrıntı sızdırmadan |

## §3 TERİMLER (TR / EN)

| Türkçe | English | Tanım |
|---|---|---|
| Beyaz liste | Allowlist / whitelist | Yalnız açıkça izin verilen değerler kabul edilir |
| Kırmızı liste | Denylist / blacklist | Bilinen kötü değerler elenir — yeni varyant hep kaçar |
| Girdi doğrulama | Input validation | Girdi, kullanılmadan ÖNCE kurala uyması için denetlenir |
| Reddet-varsaylan | Fail-closed / fail-safe | Geçerliliği kanıtlanamayan girdi REDDEDİLİR |
| Tip kontrolü | Type checking | `string`/`int`/`bool`/`array` beklenen tip denetimi |
| Uzunluk kontrolü | Length check | Min/maks karakter sınırı |
| Format kontrolü | Format check | Regex / `filter_var` / şema ile biçim denetimi |
| Takvim kontrolü | Calendar check | Tarih/gün/ay/yıl mantık denetimi (ör. 02-30 yok) |
| Kural motoru | Rule engine | Rota/alan kurallarını uygulayan sınıf (`Validator`) |
| Katı eşleşme | Strict comparison | `in_array(..., true)` — tip kayması olmadan |
| Açık redirect | Open redirect | Güvensiz `return` URL'si ile dış kaynağa yönlendirme |
| Sunucu taraflı istek sahteciliği | SSRF | Uygulamayı iç/dış adrese istek yaptırmaya zorlama |
| Çıktı kaçışı | Output escaping | Verinin bağlamına göre kaçırılması (XSS savunması) |

## §4 TEMEL İLKE — WHITELIST ZORUNLU (madde A)

| # | İlke | Neden | Repo Karşılığı |
|---|---|---|---|
| P-1 | Kabul edilecek her değer **açıkça** tanımlanır | Tanımsız = geçersiz | `ReturnUrlPolicy.php:7-15` (7 host) |
| P-2 | Red-list (blacklist) tek başına **yetersizdir** | Yeni varyant kaçar | `Validator.php:84-86` (`in` enum) · `ValidationMiddleware.php:65` |
| P-3 | Format regex'i **yakalama** değil, **izin verme** yönünde yazılır | `/[^<>]+/ gibi kaçak regex'ler XSS'i durdurmaz | `api.coremusic.net/config/routes.php:73` (`/^[a-zA-Z0-9_]{3,30}$/`) |
| P-4 | Listede olmayan her şey otomatik reddedilir | Varsayılan güven değil, varsayılan ret | `ReturnUrlPolicy.php:183` (`return '/'`) |
| P-5 | Şema/scheme için de liste + ayrı ret kuralı gerekir | `javascript:`/`data:` host listesine girmez ama tehlikelidir | `ReturnUrlPolicy.php:169-171` |

| İddia | Kanıt | Durum |
|---|---|---|
| Host bazlı beyaz liste uygulanıyor | `shared/src/Security/ReturnUrlPolicy.php:7-15` + `:177-181` | ✅ KANITLI |
| Enum/rol beyaz listesi uygulanıyor | `shared/src/Middleware/ValidationMiddleware.php:65-69` | ✅ KANITLI |
| Route kural listesi uygulanıyor | `shared/src/Api/Middleware/Validator.php:39-54` | ✅ KANITLI |
| Tüm rotalar için tek şema kaynağı | — | ⚠️ VERIFICATION REQUIRED (index §2) |

## §5 KATMANLI DOĞRULAMA (4 ZORUNLU KONTROL)

| Katman | Soru | Aracı | Kanıt |
|---|---|---|---|
| 1 — Tip | Beklenen tip mi? | `typeError()` match | `Validator.php:126-139` |
| 2 — Uzunluk | Sınır içinde mi? | `min_length`/`max_length`/`min`/`max` | `ValidationMiddleware.php:47-56` · `Validator.php:88-94` |
| 3 — Format | Biçim geçerli mi? | `filter_var` / `regex` | `ValidationMiddleware.php:59` · `Validator.php:78-82` · `:141-144` |
| 4 — İzin (değer) | Tanımlı değerlerden mi? | `in` / `enum` / host allowlist | `Validator.php:84-86` · `ReturnUrlPolicy.php:177-181` |
| + Takvim | Tarih mantıklı mı? | **repo kanıtı yok** | ⚠️ KANIT YOK (§15 U-4) |

| Kural | |
|---|---|
| Katmanlar **atlanamaz** — 1 geçerse 2'ye, 2 geçerse 3'e bakılır | anayasa kuralı |
| Katman sırası koddaki sıra ile aynıdır | `Validator::fieldErrors()` sırası: tip → regex → in → min/max (`Validator.php:63-97`) |
| Temizleme (sanitization) **doğrulamanın yerine geçmez** | surucu §6 |

## §6 BAĞLAYICI KURALLAR (IV-01..IV-16)

| Madde | Kural | Yaptırım |
|---|---|---|
| IV-01 | Kabul edilecek her değer bir **beyaz liste** ile tanımlanır | Kod incelemesinde RED |
| IV-02 | Kırmızı liste (blacklist) tek savunma olarak **kullanılmaz** | RED |
| IV-03 | Doğrulama **sunucu tarafında** yapılır; istemci tarafı kontrolü yalnız UX'tir | BLOCK |
| IV-04 | Sunucu tarafı doğrulama, istemci tarafının **yerine GEÇMEZ** — ikisi de vardır | BLOCK |
| IV-05 | Geçerliliği kanıtlanamayan girdi **reddedilir** (reddet-varsaylan) | RED |
| IV-06 | Tip · uzunluk · format · değer (enum/host) kontrollerinin **dördü de** uygulanır | RED |
| IV-07 | Tarih/saat girdilerinde takvim doğrulaması gerekir (gün 1-31, ay 1-12, geçerli tarih) | ⚠️ repo kanıtı yok → ADR/kod (U-4) |
| IV-08 | Doğrulama hataları **bilgi sızdırmaz**: hangi alan hatalı söylenir, sistem/joint detayı söylenmez | RED |
| IV-09 | Hata mesajı saldırganın girdisini **geri yansıtmez** (refleks XSS / log enjeksiyonu) | BLOCK |
| IV-10 | Redirect/return URL yüzeyi **host beyaz listesi** ile korunur | RED |
| IV-11 | SSRF yüzeyi: dış çağrı hedefi girdiden **türetilmez** ya da host/şema/scheme beyaz listesinden geçer | ⚠️ repo kanıtı yok (R-18) |
| IV-12 | Doğrulama kuralı **rote** değil, kural motoru/rota tanımı üzerinden tanımlanır | BLOCK |
| IV-13 | Regex kuralı **ReDoS** taşıyorsa (geri dönüşlü, üstel) reddedilir | ⚠️ sınır değeri yok (U-5) |
| IV-14 | `@preg_match` gibi hata yutan çağrılar hata yolunu gizlemez — hata da reddettir | ⚠️ mevcut kod `@` kullanıyor (`Validator.php:79`) → U-6 |
| IV-15 | Doğrulama reddi, **4xx** ve sakin bir gövde ile döner; 200 + hata gövdesi yasak | RED |
| IV-16 | Doğrulama katmanı geçmeden **parametreli sorgu/çıkış kaçışı** adımı da atlanmaz (index §2 adımları 4-5) | BLOCK |

## §7 YASAKLAR (FORBIDDEN)

| # | Yasak | Sınıf |
|---|---|---|
| Y-01 | Yalnız istemcide doğrulama (JS `if` + gönder) | bypass |
| Y-02 | Yalnız `trim`/`strip_tags` ile "temizleme" yapıp geçmek | yanlış savunma |
| Y-03 | `preg_match('/[^<>]/')` gibi kaçak regex'i tek XSS savunması saymak | yanlış savunma |
| Y-04 | Bilinen-kötü-dize listesi (blacklist) ile giriş doğrulama | yetersiz |
| Y-05 | Doğrulama hatasında SQL/join/dosya yolu gibi iç detayı kullanıcıya vermek | bilgi sızıntısı |
| Y-06 | Girdiyi hata mesajında veya log'a ham yazmak | sızıntı / log enjeksiyonu |
| Y-07 | Host'u `str_contains` ile kontrol (evil-coremusic.net kaçar) | whitelist bozma |
| Y-08 | `javascript:`/`data:` şemasına yalnız host listesiyle karşı koymak | scheme bypass |
| Y-09 | Redirect URL'de `//`, `/\`, `%2F` varyantlarını reddetmemek | açık redirect |
| Y-10 | Doğrulama reddini 200 OK + HTML ile döndürmek (cache/istemci yanlış anlar) | sözleşme |
| Y-11 | Doğrulamayı yalnız GET/POST'ta yapıp PUT/PATCH/DELETE'te atlamak | yüzey boşluğu |
| Y-12 | Shadow'lanmış alan adlarıyla çift gönderim (`user` vs ` User`) — PHP zırhı dışında kalan katmanda yok sayma | yarış/boyut |

## §8 SUNUCU TARAFI ZORUNLULUK (IV-03 / IV-04)

| Argüman | Çürütme | Kanıt |
|---|---|---|
| "Tarayıcı zaten `required` koyuyor" | İstemci kuralı ağ üzerinden atlanır (curl/direct) | — (temel gerçeği) |
| "JS hata veriyor" | JS kapatılabilir, sürüm önyüklü olmayabilir | — |
| "Sadece API" | API doğrudan tüketilir; UI yok | `api.coremusic.net/config/routes.php:53-110` |
| Doğru | Kural sunucuda tanımlı ve sunucuda uygulanır | `RequestValidationMiddleware.php:87-96` · `ValidationMiddleware.php:36-70` |

| İstemci Katmanı | Sunucu Karşılığı | Bu Repo'da | Durum |
|---|---|---|---|
| HTML `required` / `pattern` | route `validation` kuralları | `api.coremusic.net/config/routes.php:59-64,72-78` | ✅ KANITLI |
| JS form kontrolü | `ValidationMiddleware` (PageRouter) | `shared/src/Middleware/ValidationMiddleware.php:17-83` | ✅ KANITLI |
| — | API gövde/query doğrulama | `RequestValidationMiddleware.php:46-99` | ✅ KANITLI |
| İstemci katmanı | **tek savunma olarak** | — | ❌ YASAK (Y-01) |

## §9 REDDET-VARSAYLAN (FAIL-CLOSED)

| Durum | Davranış | Kanıt |
|---|---|---|
| Kural tanımsız (`_route['validation']` yok) | Bu uçta kısıt yok → pas geç (kural yoksa kural uygulanamaz) | `RequestValidationMiddleware.php:51-55` ⚠️ U-2 |
| Kural var, alan yok/zorunlu | 422 red | `Validator.php:43-48` |
| Format hatalı | 422 red | `Validator.php:78-82` |
| JSON bozuk | 400 red | `RequestValidationMiddleware.php:72-77` |
| Content-Type uyumsuz | 415 red | `RequestValidationMiddleware.php:63-70` |
| Redirect URL host listede değil | `/` (güvenli varsayılan) | `ReturnUrlPolicy.php:183` |
| Redirect URL'de kontrol karakteri | ret (false) | `ReturnUrlPolicy.php:39-41` |
| API anahtarı formatı uymuyor | ret | `ApiKeyRepository.php:36,233` |

| İlke | |
|---|---|
| "Emin değilsem reddederim" — geçerlilik **kanıtlanan** girdi akar | IV-05 |
| Fail-open (hata durumunda geçir) **yasak** | Y-05 / `.ai/AGENTS.md` fail-safe kuralı |

## §10 HATA MESAJLARI — BİLGİ SIZDIRMAZLIK

| ✅ Söylenebilir | ❌ Söylenmez |
|---|---|
| "E-posta geçerli bir e-posta olmalıdır." | "Kullanıcı adı bulunamadı" (kimlik ayrımı) |
| "Şifre en az 8 karakter olmalıdır." | "DB sorgusu başarısız: SELECT ... " (içerik) |
| "Geçersiz istek (validation_failed)" | Dosya yolu / sınıf adı / stack trace |
| Alan adı + kural ihlali | Diğer alanların değerleri |

| Kanıt | Dosya · Satır |
|---|---|
| Türkçe, kural bazlı alan mesajı (alan adı + kural) | `shared/src/Middleware/ValidationMiddleware.php:41-69` |
| Genel kod + alan haritası (iç detay yok) | `shared/src/Api/Middleware/RequestValidationMiddleware.php:92-93` |
| Fabrika mesajları (tek tip, kısa) | `shared/src/Exception/ValidationException.php:21-50` |
| Kural motoru mesajı (ingilizce, tek format) | `shared/src/Api/Middleware/Validator.php:45,80,85` |

| İddia | Durum |
|---|---|
| Alan bazlı hata üretiliyor | ✅ KANITLI |
| Hata gövdesi sistem/join/SQL detayı içermiyor | ✅ KANITLI (yalnız `{alan: mesaj}`) |
| Hata mesajı girdiyi geri yansıtıyor mu? | ⚠️ mesajlar sabit şablondur (`Validator.php:80` "invalid format") — girdi yansıtılmaz; `ValidationException::multiple()` ilk mesajı alır (`:26-30`) → kısmi |
| Kimlik hata ayrımı (kullanıcı vs parola) gizleniyor | bu çocukta DEĞİL → `[[authentication-kapsam]]` (AD-32) |

## §11 RED HARİTASI ÖZETİ (detay: surucu §4)

| Tetik | Yanıt | Dosya · Satır |
|---|---|---|
| Zorunlu alan eksik (PageRouter) | `422` + `validation_failed` + `halt` | `ValidationMiddleware.php:72-79` |
| Format/enum/uzunluk ihlali (API) | `422` + `VALIDATION_ERROR` + `error.fields` | `RequestValidationMiddleware.php:92-93` |
| Bozuk JSON gövde | `400` + `INVALID_JSON` | `RequestValidationMiddleware.php:74-77` |
| Uygun olmayan Content-Type | `415` + `UNSUPPORTED_MEDIA_TYPE` | `RequestValidationMiddleware.php:64-70` |
| Güvensiz return URL | redirect hedefi `/`'ye çekilir | `ReturnUrlPolicy.php:129-131,183` |
| Domain exception yolu | `422` (ValidationException) | `ValidationException.php:12` |
| Aşırı istek | `429` (kota — **bu çocukta değil**) | `rate-limiting` |

## §12 SSRF / REDIRECT GİRDİ YÜZEYİ (index §5 sınırı: `authorization` öncesi)

| Yüzey | Girdi | Koruma | Kanıt |
|---|---|---|---|
| Login/çıkış return URL | `return`, `redirect_uri`, `next` | Host allowlist + raw/decode eşitliği | `ReturnUrlPolicy.php:25-89` |
| Protokol-relative varyant | `//host`, `/\host`, `%2F` | iki katmanlı ret | `ReturnUrlPolicy.php:51-56,102-112` |
| Kontrol karakteri | `\t \n \r`, `%0A` | ham + tek katman decode taraması | `ReturnUrlPolicy.php:39-41,121-125` |
| Userinfo sızıntısı | `user:pass@host` | raw parse ret | `ReturnUrlPolicy.php:81-83,173-175` |
| Şema enjeksiyonu | `javascript:`/`data:`/`vbscript:` | şema ret | `ReturnUrlPolicy.php:169-171` |
| Helper girişi | tüm redirect kontrolleri | SSOT → `ReturnUrlPolicy::isAllowed()` | `SecurityHelper.php:13-20` |
| Dış çağrı hedefi (SSRF A10) | outbound URL | **kodda kanıt YOK** | ⚠️ VERIFICATION REQUIRED (R-18 · D-31) |

| SSRF Notu | |
|---|---|
| `shared/src` içinde `SSRF`/outbound allowlist araması | 0 isabet (2026-10-15 grep) |
| Bu çocuk yalnızca **girdi yüzeyini** kapsar; dış çağrı adaptörü | `K013` + `[[security-kapsam]]` A10 satırı |
| SSRF Prevention Cheat Sheet URL'si | kök havuza tahsisli (`[[security-web-arastirma]]` §17) → bu dosyada kullanılmaz |

## §13 ÇIKTI KAÇIŞI SINIRI (index §2 adımı 5)

| Konu | Bu çocukta | Sahibi |
|---|---|---|
| HTML/JS bağlamına göre escape | Yalnız sınır referansı | ⚠️ kapsam genişletme — tam katman okunmadı (index open_question) |
| Parametreli sorgu (SQL) | Kural olarak taşınır (§6 IV-16) | `K007-data` + `security-testing` |
| XSS testi | Yok | `security-testing` (index §2: "SQL injection testi — yok") |

## §14 DOĞRULAMA SORUSU SETİ (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Bu alanın kabul listesi tanımlı mı? | IV-01 ✅ | IV-01 ihlali → RED |
| 2 | Kural sunucuda mı? | IV-03 ✅ | Y-01 → BLOCK |
| 3 | Tip + uzunluk + format + değer kontrolü sırayla var mı? | IV-06 ✅ | eksik katman → RED |
| 4 | Tarih girdisi mi? | IV-07 (takvim) | — |
| 5 | Red mesajı iç detay içeriyor mu? | Y-05 → BLOCK | IV-08 ✅ |
| 6 | Redirect/URL girdisi mi? | §12 akışı | — |
| 7 | Dış çağrı hedefi girdiden mi türetildi? | IV-11 → ⚠️/ADR | — |
| 8 | Regex üstel geri dönüşlü mü? | IV-13 → RED | — |
| 9 | Red 4xx mi, 200 + hata mı? | IV-15 ✅ | RED |

## §15 KANIT ENVANTERİ (Dosya · Satır)

| # | Dosya : Satır | Ne gösteriyor |
|---|---|---|
| 1 | `shared/src/Security/ReturnUrlPolicy.php:7-15` | 7 host'luk beyaz liste (ALLOWED_HOSTS) |
| 2 | `shared/src/Security/ReturnUrlPolicy.php:39-41` | kontrol karakteri ret (ham + decode) |
| 3 | `shared/src/Security/ReturnUrlPolicy.php:169-171` | tehlikeli şema ret listesi |
| 4 | `shared/src/Security/ReturnUrlPolicy.php:177-181` | host + alt-alan adı eşleşmesi (`str_ends_with`) |
| 5 | `shared/src/Middleware/ValidationMiddleware.php:41-69` | required/min/max/email/enum kuralları |
| 6 | `shared/src/Middleware/ValidationMiddleware.php:72-79` | 422 + `validation_failed` reddi |
| 7 | `shared/src/Api/Middleware/Validator.php:63-97` | kural motoru: tip→regex→in→min/max |
| 8 | `shared/src/Api/Middleware/Validator.php:126-139` | tip denetimi (`match`) |
| 9 | `shared/src/Api/Middleware/Validator.php:141-144` | e-posta formatı (`FILTER_VALIDATE_EMAIL`) |
| 10 | `shared/src/Api/Middleware/RequestValidationMiddleware.php:63-77` | 415 / 400 redleri |
| 11 | `shared/src/Api/Middleware/RequestValidationMiddleware.php:87-96` | 422 + `error.fields` |
| 12 | `shared/src/Exception/ValidationException.php:12,21-50` | 422 istisna fabrikaları |
| 13 | `shared/src/PageRouter/PageRouterKernel.php:161-167` | `required_fields` üreten tek anahtar |
| 14 | `api.coremusic.net/config/routes.php:59-64,72-78` | uç bazlı `validation` kuralları |
| 15 | `shared/src/Security/SecurityHelper.php:13-20` | helper → `ReturnUrlPolicy` yönlendirmesi (SSOT) |
| 16 | `shared/src/PageRouter/RequestNormalizer.php:31,49` | host/URI sanitization (`FILTER_SANITIZE_*`) |
| 17 | `shared/src/Repository/ApiKeyRepository.php:36,233` | API anahtarı format beyaz listesi (regex) |

## §16 BİLİNMEYENLER DEFTERİ

| # | Soru | Etiket |
|---|---|---|
| U-1 | Doğrulama kuralları tek şemadan mı yönetiliyor? | ⚠️ VERIFICATION REQUIRED (index §2) |
| U-2 | `required_fields` üreten route tanımı repo'da var mı? (grep: yalnız test + kernel + middleware) | ⚠️ VERIFICATION REQUIRED |
| U-3 | Çıktı kaçışı (escaping) katmanının tam kapsamı | ⚠️ VERIFICATION REQUIRED (index §2) |
| U-4 | Takvim (calendar) doğrulaması kodda hiçbir yerde yok | ⚠️ KANIT YOK |
| U-5 | Regex'ler için ReDoS sınırı/kural taraması yok | ⚠️ KANIT YOK |
| U-6 | `@preg_match` hata yutması (`Validator.php:79`) — kural bozuksa davranış | UNKNOWN |
| U-7 | SSRF outbound URL denetimi | ⚠️ VERIFICATION REQUIRED (R-18) |
| U-8 | GET/query yüzeyi: PageRouter tarafında query doğrulama kanıtı | ⚠️ KANIT YOK (API katmanı kanıtlı: `RequestValidationMiddleware.php:81`) |

## §17 KAPSAM DIŞI (index §4'e sadık)

| Konu | Sahibi | Bu çocukta |
|---|---|---|
| Kimlik alanlarının doğrulama modeli (kimlik deposu) | `K010-identitiy` | Yalnız sınır referansı |
| OS girdi yüzeyi (komut satırı, dosya sistemi izni) | `K000-isletim-sistemi/security` | Yalnız sınır referansı |
| Yetki kararı | `[[authorization-kapsam]]` | Doğrulanmış girdi üzerinden çalışır |
| Kota / anti-automation | `[[rate-limiting-index]]` | Girdi hacmi sınırı — öncesi |
| Injection testleri | `security-testing` | Doğrulama sonrası |
| Uygulama katmanı DTO'ları | `K011-application` | kardeş |

## §18 UYUM MATRİSİ (standard'a aykırılık)

| Kural | Kaynak | Uyum |
|---|---|---|
| Whitelist zorunlu / blacklist yetersiz | OWASP Input Validation CS (web-arastirma §1) | ✅ `ReturnUrlPolicy` + `in`/`enum` |
| Injection A03 kapsamı | index §5 (SQL Injection CS) | ✅ doğrulama + parametreli sorgu (§6 IV-16) |
| XSS bağlam kaçışı | OWASP XSS Prevention CS (web-arastirma §1) | ⚠️ U-3 (katman okunmadı) |
| URI biçimi standardı | RFC 3986 (web-arastirma §1) | ⚠️ `parse_url` kullanımı kanıtlı; tam uri karşılaştırması U-7 |
| PDO prepared statement yalnız | `shared/AGENTS.md` §4.1 | ✅ (ilgili, K007) |
| `SELECT *` yasak | `.ai/AGENTS.md` §16 | ✅ (bu çocukta sorgu yok) |
| Fail-safe | `.ai/AGENTS.md` | ✅ §9 (`'/'` varsayılanı, 4xx redleri) |
| Takvim doğrulaması | IV-07 | ❌ eksik → U-4 |

## §19 DOĞRULAMA LİSTESİ (checklist)

| # | Kontrol | Kriter |
|---|---|---|
| D-1 | Her kabul edilen değerin bir listesi/kuralı var mı? | IV-01 |
| D-2 | Kural sunucuda mı tanımlı ve uygulanıyor mu? | IV-03/IV-04 |
| D-3 | Tip+uzunluk+format+değer sırası korunuyor mu? | §5 |
| D-4 | Tarih girdisinde takvim kontrolü var mı? | IV-07 (şu an YOK) |
| D-5 | Hata gövdesi iç detay/girdi yansıtması içeriyor mu? | IV-08/IV-09 |
| D-6 | Redirect girdisi host listesinden geçiyor mu? | §12 |
| D-7 | Dış çağrı hedefi girdiden türetiliyor mu? | IV-11 (⚠️) |
| D-8 | Red 4xx + sakin gövde mi? | IV-15 |
| D-9 | Testler red yollarını kapsıyor mu? | `input-validation-dogrulama.md` §4 |

## §20 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[index]] | üst | bu çocuk künyesi (whitelist ilkesi §1-§2) |
| [[authentication-kapsam]] | sonrası | kimlik alanlarının doğrulama modeli ayrı çocuk |
| [[authorization-kapsam]] | sonrası | doğrulanmış girdi üzerinden yetki |
| [[rate-limiting-index]] | öncesi | aşırı girdi hacmi sınırı |
| [[security-kapsam]] | üst | K009 kapsamı (A03/A10 eşlemesi) |
| [[security-riskler]] | referans | R-05 açık redirect · R-18 SSRF |
| [[security-dogrulama]] | referans | D-31 SSRF (UNKNOWN) |
| [[security-web-arastirma]] | havuz | kök URL tahsisleri (SSRF CS dahil) |
| [[security-bilesenler]] | referans | B-07 ValidationMiddleware |

## §21 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 17 kanıt satırı + 8 açık soru; whitelist anayasası | Vault Steward (subagent) |

## §22 SÜRÜM

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-15 |
| Durum | draft (U-1..U-8 açık) |
| Erişim | `.ai/architecture/K009-security/input-validation/input-validation-anayasa.md` |
| `git commit` | ATILMADI (subagent yetkisi yok) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
