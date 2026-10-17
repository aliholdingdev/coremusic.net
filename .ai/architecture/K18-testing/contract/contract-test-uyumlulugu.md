---
title: "K019-testing/contract — Contract Test Uyumluluğu: Consumer-Driven, Şema, Breaking Change, Provider Verification"
node_id: K019-testing/contract/contract-test-uyumlulugu
node_type: module
status: draft
parent_id: K019-testing/contract
children: []
depends_on:
  - K019-testing
  - K019-testing/contract    # parent subdomain (sibling deseni ile tutarlı — 2026-10-17)
evidence:
  - "api.coremusic.net/config/routes.php — Satır 36 implemented=false kaydı, Satır 42-48 /api/v1/auth/me, Satır 53-60 POST login + validation (2026-10-13, Select-Object -Index ile doğrulandı)"
  - "shared/tests/Api/RouteTableTest.php — Satır 67-76 405 + Allow, Satır 87-96 404, Satır 159-165 sorgu/slash normalizasyonu (2026-10-13)"
  - "shared/tests/Api/GatewayMethodRoutingTest.php — Satır 67-78 405 Allow başlığı, Satır 80-86 404 Allow yok, Satır 88-97 sözleşmeye göre dispatch (2026-10-13)"
  - "shared/tests/Api/ApiResponseTest.php — Satır 11-17 data anahtarı, Satır 19-25 meta, Satır 27-33 error yapısı, Satır 49-56 pagination (2026-10-13)"
  - "shared/tests/Api/RequestValidationMiddlewareTest.php — Satır 68-82 400 INVALID_JSON, Satır 84-97 415, Satır 114 422 (2026-10-13)"
  - "shared/tests/Api/ResponseNormalizationMiddlewareTest.php — Satır 44-57 ETag eşleşmesinde 304 halt, Satır 60-74 cacheable olmayan 304 üretmez (2026-10-13)"
  - "shared/tests/Api/VersioningTest.php — Satır 12-19 ApiVersion enum değerleri, Satır 41-49 sürüm yolu eşlemesi (2026-10-13)"
  - "shared/tests/Api/ApiPipelineSecurityTest.php — Satır 34-50 CORS izinli başlıklar, Satır 52-63 bilinmeyen origin'de fail-closed, Satır 161-170 donmuş pipeline sırası (2026-10-13)"
  - "shared/phpunit.xml — Satır 8-33 testsuite listesi (Unit, Api, Repository, Events, Security, Middleware, Component, OAuth) (2026-10-13)"
  - "shared/composer.json — Satır 24 phpunit/phpunit ^10.5, Satır 47 test scripti (2026-10-13)"
validation:
  - "Dosya satır sayısı [IO.File]::ReadAllLines ile ölçüldü (2026-10-13)"
  - "24 aday URL Invoke-WebRequest -Method Head ile HTTP 200 olarak doğrulandı (2026-10-13)"
  - "URL envanteri: 1441 benzersiz URL (2026-10-13 temel ölçüm), bu dosyada kesişim 0"
  - "Repo satır alıntıları Select-Object -Index (N-1) ile birebir doğrulandı (2026-10-13)"
  - "2026-10-17: depends_on parent satırı eklendi · group mimari → kod (tutarlılık) · §10.6 çok-dilli karşılıklar eklendi"
open_questions:
  - "Depoda consumer-driven contract aracı (Pact benzeri) yok — tüketici beklentisi dosyası görünmüyor (⚠️ VERIFICATION REQUIRED)"
  - "OpenAPI/JSON şema dosyası depoda görünmüyor; şema doğrulama el yazımı assertion'lara dayanıyor (⚠️ VERIFICATION REQUIRED)"
  - "CI'da sözleşme uyum ayrı bir kapı olarak tanımlı değil; API testleri genel php-test işinde koşuluyor (⚠️ VERIFICATION REQUIRED)"
  - "Breaking change kaydı (sözleşme sürüm geçmişi) depoda yok — sürüm numarası enum ile sınırlı (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K019-testing/contract/contract-test-uyumlulugu

> **ELI10:** Contract (sözleşme) testi, "API'nin verdiği cevap, tüketicinin beklediği cevapla
> aynı mı?" sorusunu sınar. Birim testi sağlayıcının içini, contract testi ise **iki tarafın
> anlaşmasını** sınar. Bu dosya dört soruyu yanıtlar: tüketici beklentisi nasıl yazılır
> (**consumer-driven**), cevap şekli nasıl doğrulanır (**şema doğrulama**), hangi değişiklik
> **breaking change** sayılır ve değişiklik **CI'da** nasıl durdurulur (**provider
> verification + gate**).

## §1 Amaç

Bu modül, CoreMusic'in **API sözleşmesi uyumluluk kurallarının tek yetkili tanımıdır**. Dört
karar bu dosyada verilir: consumer-driven beklenti biçimi · şema doğrulama katmanı · breaking
change sınıflandırması · provider verification ve CI kapısı. Sözleşme bozulursa tüketici
sessizce kırılır; bu dosyanın amacı kırılmayı **entegrasyon öncesinde** yakalamaktır.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Tüketici beklentisi nasıl yazılır? | Consumer-driven contract kalıbı | §4 |
| 2 | Cevap şekli nasıl doğrulanır? | Şema doğrulama katmanları | §5 |
| 3 | Hangi değişiklik kırıcıdır? | Breaking change tablosu | §6 |
| 4 | Sağlayıcı doğrulaması nasıl koşulur? | Provider verification | §7 |
| 5 | Değişiklik CI'da nasıl durur? | CI kapısı | §8 |
| 6 | Mevcut sözleşme neyi kapsıyor? | Repo mevcut durumu | §9 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | API sözleşme testi dosyası | dosya | `shared/tests/Api/` 8 dosya | mevcut (glob 2026-10-12 + 2026-10-13) |
| H2 | Sürüm numaralandırma | enum değeri | 5 (ApiVersion) | VersioningTest Satır 38 |
| H3 | 405 + Allow sözleşmesi | assertion | 3 test | RouteTableTest + GatewayMethodRoutingTest |
| H4 | Consumer-driven pact dosyası | dosya | yok | ⚠️ VERIFICATION REQUIRED |
| H5 | OpenAPI/JSON şema | dosya | yok (görünmüyor) | ⚠️ VERIFICATION REQUIRED |
| H6 | Ayrı sözleşmesi CI işi | job | yok (php-test içinde) | ⚠️ VERIFICATION REQUIRED |
| H7 | Breaking change kaydı | dosya | yok | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Consumer-driven contract | ✅ Beklenti biçimi ve sahiplik | ❌ Pact kurulumu → bağımlılık kararı |
| Şema doğrulama | ✅ Katman ve kural | ❌ JSON şeması üretimi → API tasarımı |
| Breaking change | ✅ Sınıflandırma ve sürüm kararı | ❌ Sürümleme stratejisi → K013-integraion |
| Provider verification | ✅ Sağlayıcı doğrulama koşusu | ❌ CI job tanımı → K018-devops/pipeline |
| CI kapısı | ✅ Kapı sertliği kuralı | ❌ Workflow YAML → K018-devops |
| Route/HTTP sözleşmesi | ✅ 200/404/405/422/304 davranışı | ❌ Routing implementasyonu → backend alanı |
| İç birim testi | ✅ Yalnız sözleşmeye değen kısmı | ❌ Test desenleri → `unit/` |
| Güvenlik başlıkları | ✅ Sözleşme olarak CORS/fail-closed | ❌ Güvenlik denetimi → K009-security |

**Sınır kararı:** kararın ta kendisi buradadır — **sözleşme, sağlayıcının testleriyle değil,
tüketici beklentisiyle korunur**; sağlayıcı testleri tek başına yeterli değildir, çünkü
sağlayıcı kendi hatalarını kendi beklentisiyle ölçer (§4.2).

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Contract | contract | İki tarafın yazılı anlaşması | §4 |
| Consumer | consumer | Çağrı yapan taraf (istemci/UI) | §4.1 |
| Provider | provider | Çağrıyan taraf (API sunucusu) | §7 |
| Consumer-driven | consumer-driven contract testing | Tüketici beklentisinin kaynağı olması | §4.2 |
| Şema doğrulama | schema validation | Cevap şeklinin kurala uyması | §5 |
| Breaking change | breaking change | Tüketiciyi kıran değişiklik | §6 |
| Provider verification | provider verification | Sağlayıcının tüketici beklentisini koşması | §7 |
| Allow başlığı | Allow header | 405'te izinli method listesi | RouteTableTest Satır 73-75 |
| Sürüm yolu | versioned path | `/api/v1` benzeri önek | VersioningTest Satır 44-48 |
| Normalizasyon | normalization | Sorgu/slash temizliği | RouteTableTest Satır 159-165 |
| ETag / 304 | conditional response | Önbellek doğrulama cevabı | ResponseNormalization Satır 44-57 |
| Fail-closed | fail-closed | Bilinmeyende reddetme | ApiPipelineSecurityTest Satır 52-63 |
| CI kapısı | CI gate | Değişikliği durduran koşul | §8 |

## §4 Consumer-driven contract

### §4.1 Sözleşme katmanları

| # | Katman | Neyi kapsar | Tüketici mi yazar? | Repo durumu |
|---|---|---|---|---|
| 1 | Yol (route) sözleşmesi | path + method + durum kodu | evet (beklenen) | RouteTableTest ile sağlayıcı tarafı var |
| 2 | Şekil (shape) sözleşmesi | cevap anahtarları (`data`, `meta`, `error`) | evet | ApiResponseTest ile doğrulanıyor |
| 3 | Hata sözleşmesi | `error.code` + `error.message` (+ `details`) | evet | ApiResponseTest Satır 27-41 |
| 4 | Sürüm sözleşmesi | `/api/v1` öneki + enum | evet | VersioningTest Satır 41-49 |
| 5 | Doğrulama sözleşmesi | 400/415/422 kural seti | evet | RequestValidationMiddlewareTest |
| 6 | Önbellek sözleşmesi | ETag / 304 / `halt` | evet | ResponseNormalizationMiddlewareTest |
| 7 | Güvenlik sözleşmesi | CORS başlıkları, fail-closed | evet | ApiPipelineSecurityTest Satır 34-63 |
| 8 | Pipeline sözleşmesi | middleware sırası (donmuş) | hayır (sağlayıcı iç) | ApiPipelineSecurityTest Satır 161-170 |

### §4.2 Consumer-driven kural

| # | Kural | Gerekçe | Uygulama |
|---|---|---|---|
| 1 | Beklenti tüketici tarafından yazılır | Sağlayıcı kendi testiyle kendi hatasını ölçemez | Tüketici beklenti dosyası (§4.3) |
| 2 | Beklenti, en küçük ortak noktayı içerir | Fazlası sağlayıcıyı gereksiz kısıtlar | Yalnız tüketiciye lazım alanlar |
| 3 | Yeni alan ekleme non-breaking'tir | Tüketici bilinmeyen alanı yok sayar | §6 sınıf 1 |
| 4 | Alan kaldırma/ yeniden adlandırma breaking'tir | Tüketici okuyamaz | §6 sınıf 2 |
| 5 | Sağlayıcı her PR'da beklentiyi koşar | Kırılma erken yakalanır | §7 |
| 6 | Beklenti dosyası sürümlenir | Zamanla değişir | §4.3 |

### §4.3 Consumer beklenti dosyası (şablon — depoda yok)

| # | Alan | Zorunlu | Açıklama |
|---|---|---|---|
| 1 | Sözleşme kimliği | evet | `<servis>.<uç>.<method>` |
| 2 | Tüketici | evet | hangi arayüz/servis tüketiyor |
| 3 | Yol + method | evet | `/api/v1/auth/me` + `GET` |
| 4 | Beklenen durum kodu | evet | 200 / 401 / 404 / 405 |
| 5 | Zorunlu alanlar | evet | `data`, `meta.timestamp` gibi |
| 6 | Hata kodu kümesi | evet | izinli `error.code` listesi |
| 7 | İzinli header'lar | evet | `Allow`, `ETag` gibi |
| 8 | Sürüm | evet | `v1` |
| 9 | Sahip | evet | rol |
| 10 | Durum | evet | aktif / kaldırılıyor |

**Depo durumu:** bu biçimde bir dosya **depoda yoktur**; mevcut sözleşmeler sağlayıcı tarafınca
PHPUnit testleriyle sabitlenmiştir (`shared/tests/Api/`, 8 dosya). Bkz. §13 açık soru 1.

### §4.4 Sağlayıcı tarafı sabitleme (repo gerçeği)

| # | Dosya | Test | Sözleşmenin sabitletiği |
|---|---|---|---|
| 1 | RouteTableTest.php | `testPostLoginDeclaredButNotImplementedYields405WithAllow` (Satır 67) | 405 + `Allow` kümesi |
| 2 | RouteTableTest.php | `testUnknownPathYields404Not405` (Satır 87) | bilinmeyen yol → 404 |
| 3 | RouteTableTest.php | `testQueryStringAndSlashAreNormalized` (Satır 159) | normalizasyon |
| 4 | GatewayMethodRoutingTest.php | `testMethodMismatchReturns405WithAllowHeader` (Satır 67) | `Allow` başlığı |
| 5 | GatewayMethodRoutingTest.php | `testUnknownPathReturns404WithoutAllowHeader` (Satır 80) | 404'te `Allow` yok |
| 6 | GatewayMethodRoutingTest.php | `testGetMeDispatchesWhileGetLoginYields405PerContract` (Satır 88) | sözleşmeye göre dispatch |
| 7 | ApiResponseTest.php | `testOkReturnsDataKey` (Satır 11) | `data` anahtarı |
| 8 | ApiResponseTest.php | `testErrorReturnsErrorStructure` (Satır 27) | `error.code` + `error.message` |
| 9 | VersioningTest.php | `testApiVersionEnumValues` (Satır 12) | 5 sürüm değeri |
| 10 | ResponseNormalizationMiddlewareTest.php | `testCacheableRouteWithMatchingEtagReturnsHalt304` (Satır 44) | 304 + `halt` |

## §5 Şema doğrulama

### §5.1 Doğrulama katmanları

| # | Katman | Ne doğrular | Araç/kural | Repo durumu |
|---|---|---|---|---|
| 1 | Zorunlu anahtar | `data` / `meta` / `error` varlığı | `assertArrayHasKey` | mevcut (ApiResponseTest) |
| 2 | Tip | string/array/int beklenen tip | `assertEquals` + tip assertion | kısmen |
| 3 | Hata kodu kümesi | izinli `error.code` | sabit eşitleme | mevcut (400 INVALID_JSON, 415) |
| 4 | Durum kodu | 200/401/404/405/422/304 | `assertSame` | mevcut |
| 5 | Header sözleşmesi | `Allow`, `ETag`, CORS | başlık assertion'ı | mevcut |
| 6 | Dış şema (OpenAPI/JSON Schema) | yapı taşları | şema dosyası | **yok** — ⚠️ VERIFICATION REQUIRED |
| 7 | Tüketici şeması | consumer beklentisi | pact benzeri | **yok** — ⚠️ VERIFICATION REQUIRED |

### §5.2 Şema doğrulama kuralları

| # | Kural | Doğru | Yanlış |
|---|---|---|---|
| 1 | Zorunlu anahtarlar her testte kontrol edilir | `assertArrayHasKey('data', $result)` | yalnız status kodu |
| 2 | Hata cevabı `code` + `message` taşır | iki alan birlikte | yalnız mesaj |
| 3 | `details` yalnız doluysa döner | `testErrorExcludesDetailsWhenEmpty` (ApiResponseTest Satır 43) | boş `details` alanı |
| 4 | 404'te `Allow` başlığı olmaz | GatewayMethodRoutingTest Satır 85 | gereksiz başlık |
| 5 | 405'te `Allow` zorunlu | RouteTableTest Satır 73-74 | başlıksız 405 |
| 6 | Bilinmeyen JSON 400 döner | `INVALID_JSON` (RequestValidation Satır 80) | 500 |
| 7 | Desteklenmeyen tip 415 döner | `UNSUPPORTED_MEDIA_TYPE` (Satır 96) | sessiz kabul |
| 8 | Alan kuralı ihlali 422 döner | `testEmptyBodyWithRequiredFieldReturns422` (Satır 114) | 400 |
| 9 | Cacheable olmayan yol 304 üretmez | ResponseNormalization Satır 60 | yanlış 304 |
| 10 | Şema değişikliği §6'dan geçer | sınıf etiketi | sessiz değişiklik |

### §5.3 Durum kodu sözleşmesi (repo kanıtlı)

| # | Durum | Koşul | Kanıt |
|---|---|---|---|
| 1 | 200 | Geçerli public/get isteği | RouteTableTest Satır 38-46 |
| 2 | 400 | Hatalı JSON gövde | RequestValidationMiddlewareTest Satır 68-82 |
| 3 | 401 | Auth'suz private erişim | ApiPipelineSecurityTest Satır 143 |
| 4 | 404 | Bilinmeyen yol | RouteTableTest Satır 87-96 |
| 5 | 405 | Method uyuşmazlığı + `Allow` | GatewayMethodRoutingTest Satır 67-78 |
| 6 | 415 | `Content-Type` desteklenmiyor/eksik | RequestValidationMiddlewareTest Satır 84-99 |
| 7 | 422 | Alan kuralı ihlali | RequestValidationMiddlewareTest Satır 114-131 |
| 8 | 304 | ETag eşleşmesi + cacheable | ResponseNormalizationMiddlewareTest Satır 44-57 |
| 9 | 500 | Sağlayıcı hatası — **asla sızdırılmaz** | ApiPipelineSecurityTest Satır 77 |

## §6 Breaking change sınıflandırması

### §6.1 Sınıf tablosu

| # | Sınıf | Değişiklik | Tüketici etkisi | Breaking? | Sürüm kararı |
|---|---|---|---|---|---|
| 1 | Eklenen alan | Yeni anahtar cevaba | Bilinmeyeni yok sayar | hayır | aynı sürüm |
| 2 | Kaldırılan alan | Anahtar silinir | Okuma kırılır | evet | sürüm artır |
| 3 | Yeniden adlandırma | Anahtar adı değişir | Okuma kırılır | evet | sürüm artır |
| 4 | Tip değişikliği | string → object | Ayırma kırılır | evet | sürüm artır |
| 5 | Durum kodu değişimi | 200 → 204 vb. | Dallanma kırılır | evet | sürüm artır |
| 6 | Hata kodu değişimi | `error.code` değişimi | Ayıklama kırılır | evet | sürüm artır |
| 7 | Yeni hata kodu | Mevcut kodlara ek | Bilinmeyeni ele alır | hayır | aynı sürüm |
| 8 | `Allow` kümesi değişimi | Method eklenmesi/çıkarılması | Yönlendirme etkilenir | evet (çıkarırsa) | sürüm artır |
| 9 | Route yolu değişimi | `/api/v1/auth/me` → farklı | Çağrı 404 alır | evet | sürüm artır |
| 10 | Zorunluluk değişimi | Alan opsiyonelden zorunluya | Gönderim kırılır | evet | sürüm artır |
| 11 | Önbellek davranışı | ETag/304 mantığı değişimi | İstek paterni değişir | koşullu | değerlendirme gerekir |
| 12 | Yalnız sağlayıcı içi | Middleware sırası, log | Dışa yansıma yok | hayır | aynı sürüm |

### §6.2 Sürüm karar matrisi

| # | Durum | Karar | Kanıt |
|---|---|---|---|
| 1 | Sınıf 2-6, 8-10 değişikliği | Yeni sürüm (`v2`) açılır | §6.1 |
| 2 | Sınıf 1, 7 değişikliği | Aynı sürümde kalır | §6.1 |
| 3 | Sınıf 11 | Kırılma riski değerlendirilir; karar belgelenir | açık soru |
| 4 | Eski sürüm | Kademeli kaldırma (deprecation) | K013-integraion |
| 5 | Sürüm değeri | `ApiVersion` enum ile sabit (5 değer) | VersioningTest Satır 35-39 |
| 6 | Kayıtsız sürüm | `tryFrom` → `null` (reddedilir) | VersioningTest Satır 28-33 |

**Mevcut enum (repo kanıtı):** `v1`, `v2`, `internal`, `public`, `admin` — VersioningTest
Satır 14-18. Yeni sürüm ekleme, bu enum + sürüm yolu eşlemesini (Satır 41-49) birlikte
değiştirir; iki taraf birlikte güncellenmeden değişiklik yapılmaz.

### §6.3 Breaking change kaydı (şablon — depoda yok)

| # | Alan | Zorunlu | Açıklama |
|---|---|---|---|
| 1 | Kayıt kimliği | evet | ardışık numara |
| 2 | Tarih | evet | ISO 8601 |
| 3 | Etkilenen sözleşme | evet | yol + method |
| 4 | Sınıf (§6.1) | evet | 1-12 |
| 5 | Breaking? | evet | evet / hayır / koşullu |
| 6 | Eski → yeni | evet | somut fark |
| 7 | Sürüm kararı | evet | aynı sürüm / yeni sürüm |
| 8 | Tüketici etkisi | evet | hangi tüketici |
| 9 | Geçiş planı | evet | deprecation adımı |
| 10 | Kapanış kanıtı | kapanışta | provider verification yeşil |

## §7 Provider verification

### §7.1 Doğrulama koşusu

| # | Adım | Ne koşulur | Sıklık | Repo karşılığı |
|---|---|---|---|---|
| 1 | Yol sözleşmesi | 200/404/405 davranışı | her PR | RouteTableTest + GatewayMethodRoutingTest |
| 2 | Şekil sözleşmesi | `data`/`meta`/`error` | her PR | ApiResponseTest |
| 3 | Sürüm sözleşmesi | enum + yol | her PR | VersioningTest |
| 4 | Doğrulama sözleşmesi | 400/415/422 | her PR | RequestValidationMiddlewareTest |
| 5 | Önbellek sözleşmesi | ETag/304 | her PR | ResponseNormalizationMiddlewareTest |
| 6 | Güvenlik sözleşmesi | CORS/fail-closed | her PR | ApiPipelineSecurityTest |
| 7 | DTO sözleşmesi | `fromArray`/`toArray` | her PR | DtoTest Satır 28-37 |
| 8 | Tüketici beklentisi | pact dosyası | her PR | **yok** — ⚠️ VERIFICATION REQUIRED |

### §7.2 Provider verification kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Doğrulama sağlayıcıda, tüketici ortamında koşulur | Sözleşme iki tarafın kesişimidir |
| 2 | Sonuç CI kapısına bağlıdır | Koşulmayan doğrulama yok sayılır |
| 3 | Yeni tüketici beklentisi eklendiğinde sağlayıcı koşusu genişler | Asimetri önlenir |
| 4 | Beklenti ile sağlayıcı testi çelişirse | **Tüketici beklentisi** esastır; sağlayıcı testi revize edilir |
| 5 | Eski sürüm beklentileri | Kademeli kaldırma süresince koşulmaya devam eder |
| 6 | Sağlayıcı testi tek başına yeterli sayılmaz | §4.2 gerekçesi |

### §7.3 Repo doğrulama ortamı

| # | Kalem | Değer | Kaynak |
|---|---|---|---|
| 1 | Test çerçevesi | PHPUnit `^10.5` | shared/composer.json Satır 24 |
| 2 | Test komutu | `vendor/bin/phpunit` | ci.yml Satır 104-105 |
| 3 | Suite seçimi (shared) | Unit, Api, Repository, Events, Security, Middleware, Component, OAuth | shared/phpunit.xml Satır 8-33 |
| 4 | Suite seçimi (auth) | yalnız Unit | auth phpunit.xml Satır 13-17 |
| 5 | Durma davranışı | `stopOnFailure="false"` (tam rapor) | shared/phpunit.xml Satır 6 |
| 6 | Risky/warning | `failOnRisky` + `failOnWarning` (auth) | auth phpunit.xml Satır 6-7 |
| 7 | Sıra | `needs: php-lint` | ci.yml Satır 68 |
| 8 | Matris | `fail-fast: false` | ci.yml Satır 72 |

## §8 CI kapısı (gate)

### §8.1 Kapı karar matrisi

| # | Değişiklik türü | Kapı | Sertlik | Kaynak |
|---|---|---|---|---|
| 1 | API testi kırmızı | `php-test` işi | sert (job başarısız) | ci.yml Satır 66-108 |
| 2 | Lint kırmızı | `php-lint` | sert — `php-test` ona bağlı | ci.yml Satır 68 |
| 3 | Sözleşmesi ayrı iş | yok | — | ⚠️ VERIFICATION REQUIRED |
| 4 | Kapsam eşiği | `js-coverage` | soft (`continue-on-error`) | ci.yml Satır 167 |
| 5 | E2E | `js-e2e` | soft (`continue-on-error`) | ci.yml Satır 183 |
| 6 | Secret tarama | `gitleaks` işi | ayrı workflow | secret-scan.yml Satır 19-31 |

### §8.2 Kapı kuralı (bu dosyanın kararı)

| # | Kural | Değer |
|---|---|---|
| 1 | Sözleşme testi kırmızısı | asla soft yapılmaz — blocking kalır |
| 2 | Sözleşme testi | `php-test` içinde `Api` suite'i olarak koşulur (mevcut durum) |
| 3 | Yeni sözleşme işi açılırsa | `needs: php-lint` zinciri korunur |
| 4 | Soft kapı genişletme | yalnız kapsam/E2E; sözleşme hariç |
| 5 | Kapı değişikliği | K018-devops + bu dosya §8 kaydı birlikte |
| 6 | Kapı sertliği ölçümü | §8.4 |

### §8.3 Kapı – sınıf eşlemesi

| Sınıf (§6.1) | Kapı davranışı | Sonuç |
|---|---|---|
| 1 (ekleme) | Testler yeşil kalır | Birleştirilir |
| 2-6, 8-10 (breaking) | İlgili sözleşme testi kırmızıya döner | İş durur; sürüm kararı gerekir |
| 7 (yeni hata kodu) | Yeni assertion yoksa yeşil | Test genişletilir (§5.2 kural 2) |
| 11 (önbellek) | 304 testleri kırmızıya döner | Değerlendirme |
| 12 (içi) | Testler etkilenmez | Birleştirilir |

### §8.4 Mevcut kapı durumu (repo gerçeği — 2026-10-13)

| # | Kapı | Değer | Dosya / Satır | Durum |
|---|---|---|---|---|
| 1 | PHP lint → test | `needs: php-lint` | .github/workflows/ci.yml / Satır 68 | sert |
| 2 | API sözleşmesi testleri | `Api` suite | shared/phpunit.xml / Satır 12-14 | koşuluyor |
| 3 | Risky/warning (auth) | `failOnRisky=true` | auth.coremusic.net/phpunit.xml / Satır 6-7 | sert |
| 4 | Kapsam kapısı | `continue-on-error: true` | ci.yml / Satır 167 | soft |
| 5 | E2E kapısı | `continue-on-error: true` | ci.yml / Satır 183 | soft |
| 6 | Ayrı sözleşme işi | yok | — | ⚠️ VERIFICATION REQUIRED |
| 7 | Pact/Consumer beklenti koşusu | yok | — | ⚠️ VERIFICATION REQUIRED |
| 8 | Şema dosyası doğrulaması | yok | — | ⚠️ VERIFICATION REQUIRED |

## §9 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | api.coremusic.net/config/routes.php | 24 | `return [` — method-aware tablo kökü |
| 2 | api.coremusic.net/config/routes.php | 36 | `implemented => false` (GET login → 405 sözleşmesi) |
| 3 | api.coremusic.net/config/routes.php | 42-48 | `/api/v1/auth/me` GET + `implemented => true` |
| 4 | api.coremusic.net/config/routes.php | 53-60 | POST login + `validation` kural seti |
| 5 | shared/tests/Api/RouteTableTest.php | 67-76 | 405 + `Allow` (GET/OPTIONS var, POST yok) |
| 6 | shared/tests/Api/RouteTableTest.php | 87-96 | 404 + `Allow` boş |
| 7 | shared/tests/Api/RouteTableTest.php | 159-165 | sorgu/slash normalizasyonu |
| 8 | shared/tests/Api/GatewayMethodRoutingTest.php | 67-78 | 405 + `Allow` başlığı içeride |
| 9 | shared/tests/Api/GatewayMethodRoutingTest.php | 80-86 | 404 + `Allow` yok |
| 10 | shared/tests/Api/GatewayMethodRoutingTest.php | 88-97 | sözleşmeye göre dispatch (405 vs 200) |
| 11 | shared/tests/Api/ApiResponseTest.php | 11-17 | `data` anahtarı |
| 12 | shared/tests/Api/ApiResponseTest.php | 19-25 | `meta.timestamp` + `meta.version` |
| 13 | shared/tests/Api/ApiResponseTest.php | 27-33 | `error.code` + `error.message` |
| 14 | shared/tests/Api/ApiResponseTest.php | 49-56 | pagination `meta` yapısı |
| 15 | shared/tests/Api/RequestValidationMiddlewareTest.php | 68-82 | 400 `INVALID_JSON` |
| 16 | shared/tests/Api/RequestValidationMiddlewareTest.php | 84-97 | 415 `UNSUPPORTED_MEDIA_TYPE` |
| 17 | shared/tests/Api/RequestValidationMiddlewareTest.php | 114-131 | 422 zorunlu alan |
| 18 | shared/tests/Api/ResponseNormalizationMiddlewareTest.php | 44-57 | 304 + `halt` + boş gövde |
| 19 | shared/tests/Api/ResponseNormalizationMiddlewareTest.php | 60-74 | cacheable olmayan 304 üretmez |
| 20 | shared/tests/Api/ResponseNormalizationMiddlewareTest.php | 87-94 | hata cevabı şeffaf geçer |
| 21 | shared/tests/Api/VersioningTest.php | 12-19 | 5 sürüm değeri |
| 22 | shared/tests/Api/VersioningTest.php | 28-33 | geçersiz sürüm → `null` |
| 23 | shared/tests/Api/VersioningTest.php | 41-49 | sürüm yolu eşlemesi |
| 24 | shared/tests/Api/ApiPipelineSecurityTest.php | 34-50 | CORS izinli başlıklar (Authorization, X-Api-Key) |
| 25 | shared/tests/Api/ApiPipelineSecurityTest.php | 52-63 | bilinmeyen origin → fail-closed |
| 26 | shared/tests/Api/ApiPipelineSecurityTest.php | 161-170 | donmuş middleware sırası (dosyadan okuma) |
| 27 | shared/tests/Api/DtoTest.php | 14-20 | DTO getter sözleşmesi |
| 28 | shared/tests/Api/DtoTest.php | 28-37 | `fromArray` alan eşlemesi |
| 29 | shared/phpunit.xml | 8-33 | 8 testsuite listesi |
| 30 | shared/composer.json | 24 | `phpunit/phpunit: ^10.5` |
| 31 | shared/composer.json | 47 | `"test": "phpunit"` |
| 32 | .github/workflows/ci.yml | 104-105 | PHPUnit adımı + `$PHPUNIT_ARGS` |
| 33 | .github/workflows/ci.yml | 74-80 | matris: shared + auth (`--testsuite Unit`) |

## §10 Kod örnekleri — repo gerçek kodu

### §10.1 405 sözleşmesi — `Dosya: shared/tests/Api/RouteTableTest.php / Satır: 71-75`

```php
$this->assertSame(RouteTable::STATUS_METHOD_NOT_ALLOWED, $match['status']);
$this->assertIsArray($match['route'], 'Route tanınmalı (404 değil)');
$this->assertContains('GET', $match['allow']);
$this->assertContains('OPTIONS', $match['allow']);
$this->assertNotContains('POST', $match['allow'], 'Sunulmayan method Allow listesinde olmamalı');
```

Okuma: sözleşmenin iki yüzü birden sabitlenir — durum kodu (405) ve `Allow` kümesi.
`assertNotContains('POST', $match['allow'])` satırı, **eksen çıkarma** (breaking change sınıf 8)
yakalayan asimetrik kanıttır.

### §10.2 Hata şekli — `Dosya: shared/tests/Api/ApiResponseTest.php / Satır: 29-32`

```php
$result = ApiResponse::error('NOT_FOUND', 'Resource not found', 404);
$this->assertArrayHasKey('error', $result);
$this->assertEquals('NOT_FOUND', $result['error']['code']);
$this->assertEquals('Resource not found', $result['error']['message']);
```

Okuma: §5.2 kural 2'nin uygulaması — `code` ve `message` birlikte zorunlu; yalnız mesaj
değil, ayıklanabilir kod da sözleşme parçasıdır.

### §10.3 Şekil sözleşmesi — `Dosya: shared/tests/Api/ApiResponseTest.php / Satır: 21-24`

```php
$result = ApiResponse::ok();
$this->assertArrayHasKey('data', $result);
$this->assertArrayHasKey('meta', $result);
$this->assertArrayHasKey('timestamp', $result['meta']);
$this->assertArrayHasKey('version', $result['meta']);
```

Okuma: `meta.timestamp` + `meta.version` zorunlu anahtarlar; sınıf 2 (alan kaldırma)
bu satırlarda kırmızıya döner.

### §10.4 Doğrulama sözleşmesi — `Dosya: shared/tests/Api/RequestValidationMiddlewareTest.php / Satır: 74-81`

```php
'body'   => '{"email": ',
'_route' => ['validation' => ['email' => ['required' => true, 'type' => 'email']]],
];
$response = $middleware($request, $this->next());
$this->assertSame('INVALID_JSON', $response['error']['code'] ?? null);
$this->assertArrayNotHasKey('passthrough', $response);
```

Okuma: bozuk JSON → 400 + sabit kod; ikinci assertion, hatanın **pipeline dışına
sızmadığını** (halt) sözleşmesine bağlar.

### §10.5 Önbellek sözleşmesi — `Dosya: shared/tests/Api/ResponseNormalizationMiddlewareTest.php / Satır: 49-55`

```php
$_SERVER['HTTP_IF_NONE_MATCH'] = '"' . $etag . '"';
$out = $this->process(['_route' => ['cacheable' => true, 'cacheTtl' => 60]], $response);
$this->assertSame(304, $out['httpStatus']);
$this->assertTrue($out['halt'], 'Gövdesiz 304: halt bayrağı set edilmeli');
$this->assertSame('', $out['body']);
```

Okuma: 304 yalnız `cacheable` yolda ve ETag eşleşmesinde; `halt` + boş gövde birlikte
zorunlu — bu, §6.1 sınıf 11 (önbellek davranışı) için regresyon kanıtıdır.

### §10.6 Çok-dilli karşılıklar (12 dil)

> Sözleşme doğrulama kalıbı (durum kodu + JSON şekil assertion'ı) 12 dilde — repo standardı
> PHP/PHPUnit'tur (§10.1-§10.5). Her örnek 405+Allow sözleşmesini (§10.1) aynı şekilde sınar.
> Sürüm iddiası yazılmaz (bilgi amaçlı).

| # | Dil | Test çalıştırıcısı | Komut |
|---|---|---|---|
| 1 | PHP | PHPUnit | `vendor/bin/phpunit --testsuite Api --filter RouteTable` |
| 2 | TypeScript | Vitest | `npx vitest run sozlesme.test.ts` |
| 3 | Python | pytest | `pytest test_sozlesme.py -v` |
| 4 | C# | xUnit | `dotnet test --filter SozlesmeTesti` |
| 5 | Java | JUnit 5 | `mvn test -Dtest=SozlesmeTesti` |
| 6 | Go | `testing` + `httptest` | `go test -run TestSozlesme ./...` |
| 7 | C++ | Google Test + nlohmann::json | `./testler --gtest_filter=SozlesmeTesti.*` |
| 8 | Rust | cargo test + `serde_json` | `cargo test sozlesme` |
| 9 | Ruby | RSpec | `bundle exec rspec spec/sozlesme_spec.rb` |
| 10 | Kotlin | JUnit 5 | `gradle test --tests SozlesmeTesti` |
| 11 | Swift | XCTest + XCTestExpectation | `swift test --filter SozlesmeTesti` |
| 12 | C | Unity + cJSON | `./testler` (unity_runner) |

**PHP (PHPUnit — repo deseni §10.1):**

```php
public function testGecersizYontemSozlesmeyeUyar(): void
{
    $yanit = $this->istemci->delete('/api/v1/fatura');
    $this->assertSame(405, $yanit->durumKodu());     // Durum kodu sözleşmesi
    $this->assertSame('GET', $yanit->baslik('Allow')); // Yöntem sözleşmesi
}
```

**TypeScript (Vitest):**

```ts
it('geçersiz yöntem sözleşmeye uyar', async () => {
  const yanit = await fetch(ASAL + '/api/v1/fatura', { method: 'DELETE' });
  expect(yanit.status).toBe(405);                     // Durum kodu sözleşmesi
  expect(yanit.headers.get('allow')).toBe('GET');     // Yöntem sözleşmesi
});
```

**Python (pytest):**

```python
def test_gecersiz_yontem_sozlesmeye_uyar(asal_url):
    yanit = requests.delete(f"{asal_url}/api/v1/fatura")
    assert yanit.status_code == 405                   # Durum kodu sözleşmesi
    assert yanit.headers["Allow"] == "GET"            # Yöntem sözleşmesi
```

**C# (xUnit):**

```csharp
[Fact]
public async Task GecersizYontemSozlesmeyeUyar()
{
    var yanit = await _istemci.DeleteAsync("/api/v1/fatura");
    Assert.Equal(405, (int)yanit.StatusCode);                  // Durum kodu sözleşmesi
    Assert.Equal("GET", yanit.Headers.Allow?.ToString());      // Yöntem sözleşmesi
}
```

**Java (JUnit 5):**

```java
@Test
void gecersizYontemSozlesmeyeUyar() throws Exception {
    var yanit = istemci.send(HttpRequest.newBuilder()
        .DELETE().uri(URI.create(ASAL + "/api/v1/fatura")).build(), BodyHandlers.discarding());
    assertEquals(405, yanit.statusCode());                      // Durum kodu sözleşmesi
    assertEquals("GET", yanit.headers().firstValue("Allow").orElseThrow()); // Yöntem sözleşmesi
}
```

**Go (httptest):**

```go
func TestGecersizYontemSozlesmeyeUyar(t *testing.T) {
    ro := httptest.NewRecorder()
    api.Gateway(ro, httptest.NewRequest(http.MethodDelete, "/api/v1/fatura", nil))
    if ro.Code != 405 {                                        // Durum kodu sözleşmesi
        t.Fatalf("Code = %d, want 405", ro.Code)
    }
    if got := ro.Header().Get("Allow"); got != "GET" {         // Yöntem sözleşmesi
        t.Fatalf("Allow = %q, want GET", got)
    }
}
```

**C++ (Google Test + JSON):**

```cpp
TEST(SozlesmeTesti, HataSekliSozlesmeyeUyar) {
    auto yanit = istemci->Delete("/api/v1/fatura");
    EXPECT_EQ(yanit.kod, 405);                                // Durum kodu sözleşmesi
    auto govde = nlohmann::json::parse(yanit.govde);
    ASSERT_TRUE(govde.contains("error"));                     // Şekil sözleşmesi (§10.2)
    EXPECT_FALSE(govde["error"]["code"].get<std::string>().empty());
}
```

**Rust (serde_json):**

```rust
#[tokio::test]
async fn hata_sekli_sozlesmeye_uyar() {
    let yanit = istemci().delete(format!("{ASAL}/api/v1/fatura")).send().await.unwrap();
    assert_eq!(yanit.status().as_u16(), 405);                 // Durum kodu sözleşmesi
    let govde: serde_json::Value = yanit.json().await.unwrap();
    assert!(govde.get("error").is_some());                     // Şekil sözleşmesi
}
```

**Ruby (RSpec):**

```ruby
it 'geçersiz yöntem sözleşmeye uyar' do
  delete '/api/v1/fatura'
  expect(response).to have_http_status(405)                   # Durum kodu sözleşmesi
  expect(response.headers['Allow']).to eq('GET')              # Yöntem sözleşmesi
end
```

**Kotlin (JUnit 5):**

```kotlin
@Test
fun `hata sekli sozlesmeye uyar`() = testApplication {
    val yanit = client.delete("/api/v1/fatura")
    assertEquals(405, yanit.status.value)                                  // Durum kodu sözleşmesi
    val govde = Json.parseToJsonElement(yanit.bodyAsText())
    assertTrue(govde.jsonObject.containsKey("error"))                      // Şekil sözleşmesi
}
```

**Swift (XCTest):**

```swift
func testHataSekliSozlesmeyeUyar() throws {
    var ist = URLRequest(url: URL(string: "\(asal)/api/v1/fatura")!)
    ist.httpMethod = "DELETE"
    let yanit = try await URLSession.shared.data(for: ist)
    XCTAssertEqual(yanit.statusCode, 405)                                 // Durum kodu sözleşmesi
    XCTAssertEqual(yanit.value(forHTTPHeaderField: "Allow"), "GET")       // Yöntem sözleşmesi
}
```

**C (Unity + cJSON):**

```c
void test_hata_sekli_sozlesmeye_uyar(void) {
    long kod = http_delete(ASAL "/api/v1/fatura");         /* Act */
    TEST_ASSERT_EQUAL_INT(405, kod);                       /* Durum kodu sözleşmesi */
    const char *govde = sonGovde();
    cJSON *kok = cJSON_Parse(govde);
    TEST_ASSERT_NOT_NULL(cJSON_GetObjectItem(kok, "error")); /* Şekil sözleşmesi */
}
```

## §11 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Sağlayıcı testini tek sözleşme saymak | Asimetrik ölçüm | §4.2 denetimi |
| 2 | Sözleşme testini soft kapıya koymak | Kırılma sessiz geçer | §8.2 kural 1 |
| 3 | Yeni alanı eski test zorunlu kılarsa | Gereksiz kırılma | Şema incelemesi |
| 4 | Alanı sessizce kaldırmak | Tüketici kırılır | §6 sınıf 2 denetimi |
| 5 | `error.code` değişimini docsuz yapmak | Ayıklama kırılır | §6.3 kaydı |
| 6 | 405'i `Allow` başlığısız döndürmek | Sözleşme ihlali | RouteTableTest Satır 73 |
| 7 | Bilinmeyen yola 405 döndürmek | Yanlış sinyal | RouteTableTest Satır 87 |
| 8 | Hata mesajını istemciye sızdırmak | Güvenlik + sözleşme | ApiPipelineSecurityTest Satır 77 |
| 9 | Sürüm enum'ünü tek başına değiştirmek | Yol eşlemesi kalır | VersioningTest Satır 41-49 |
| 10 | Sözleşmesiz yeni uç eklemek | Beklenti yazılmaz | §4.3 şablonu |
| 11 | Bilinmeyen origin'de açık bırakmak | Fail-open | ApiPipelineSecurityTest Satır 52 |

## §12 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K019-testing/integration | HTTP davranışı | Sözleşme yeşili ön koşul | §5.3 | uyumlu |
| 2 | K019-testing/unit | Assertion gücü | Şekil doğrulama | §5.2 | uyumlu |
| 3 | K019-testing/regression | Sözleşme regresyonu | Seçim sinyali | §8.3 | uyumlu |
| 4 | K019-testing/coverage | API suite kapsamı | Eşik etkisi | shared/phpunit.xml Satır 12-14 | ayrık rapor |
| 5 | K019-testing/flaky | Sözleşme testi kararlılığı | Karantina girdisi | tekrar oranı | ⚠️ VERIFICATION REQUIRED |
| 6 | K018-devops/pipeline | Kapı sertliği | `needs` + `continue-on-error` | ci.yml Satır 68/167/183 | açık |
| 7 | K013-integraion | Dış API sözleşmesi | Sürümleme kararı | §6.2 | tasarım iddiası |
| 8 | K009-security | CORS/fail-closed sözleşmesi | Başlık seti | ApiPipelineSecurityTest | uyumlu |
| 9 | K0100-identitiy | Auth durum kodları | 401 sözleşmesi | §5.3 satır 3 | ayrık |

## §13 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Consumer-driven pact yok | Sözleşme tek taraflı | yüksek | Beklenti dosyası (§4.3) | ⚠️ VERIFICATION REQUIRED |
| 2 | OpenAPI/JSON şema yok | Şekil kuralı el yazımı | yüksek | Şema üretimi | ⚠️ VERIFICATION REQUIRED |
| 3 | Ayrı sözleşme CI işi yok | Kapı görünmez | orta | Job kararı (K018) | ⚠️ VERIFICATION REQUIRED |
| 4 | Breaking change kaydı yok | Geçmiş okunamaz | orta | §6.3 kaydı | ⚠️ VERIFICATION REQUIRED |
| 5 | auth'ta yalnız `Unit` suite | API testleri auth'ta koşulmuyor | orta | Suite genişletme | auth phpunit.xml Satır 13-17 |
| 6 | E2E soft gate | Sözleşme kırılması E2E'de görünmeyebilir | orta | Kapı sertliği | ci.yml Satır 183 |
| 7 | Sınıf 11 (önbellek) kararsız | 304 davranışı tartışmalı kalır | düşük | Değerlendirme kaydı | §6.1 |
| 8 | Dış API sözleşmeleri tanımsız | K013 yüzeyi açık | orta | Dış kontrat envanteri | K013-integraion |
| 9 | Sürüm enum 5 ile sınırlı | Yeni sürüm unutulabilir | düşük | Checklist | §6.2 satır 5 |

## §14 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Durum kodu | 405 + `Allow` = 100% | GatewayMethodRoutingTest | her PR | §9 satır 8 |
| 2 | Şekil | `data`/`meta`/`error` zorunlu | ApiResponseTest | her PR | §9 satır 11-14 |
| 3 | Doğrulama | 400/415/422 testi | RequestValidationMiddlewareTest | her PR | §9 satır 15-17 |
| 4 | Önbellek | 304 yalnız cacheable | ResponseNormalization testi | her PR | §9 satır 18-19 |
| 5 | Sürüm | 5 enum değeri | VersioningTest | her PR | §9 satır 21 |
| 6 | Kapı | Sözleşme testi blocking | ci.yml `needs` + suite | her PR | §8.4 satır 1-2 |
| 7 | Tüketici | ≥ 1 beklenti dosyası | Dosya sayımı | çeyrek | §4.3 |
| 8 | Breaking kaydı | Her breaking için 1 kayıt | §6.3 denetimi | her breaking | §6.3 |

## §15 Sözleşme – sınıf – kapı matrisi

| # | Sözleşme yüzeyi | Sınıf 1 riski | Sınıf 2-6 riski | Kapı |
|---|---|---|---|---|
| 1 | Yol + method | düşük | yüksek (404/405) | RouteTableTest |
| 2 | Cevap şekli | sıfır | yüksek (alan kaybı) | ApiResponseTest |
| 3 | Hata kodları | düşük | yüksek | RequestValidation + ApiResponse |
| 4 | Sürüm yolu | sıfır | yüksek | VersioningTest |
| 5 | `Allow` başlığı | orta | yüksek | GatewayMethodRoutingTest |
| 6 | ETag/304 | düşük | orta | ResponseNormalization testi |
| 7 | CORS başlıkları | düşük | yüksek | ApiPipelineSecurityTest |
| 8 | DTO `fromArray` | sıfır | yüksek | DtoTest |

## §16 Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Pact ana sayfası, consumer-driven contract testing yaklaşımının tanıtım kaydırır | https://docs.pact.io/ | 2026-10-13 | HTTP 200 doğrulandı |
| Pact tüketici (consumer) belgeleri, beklentinin nasıl oluşturulduğunu tanımlar | https://docs.pact.io/consumer/ | 2026-10-13 | HTTP 200 doğrulandı |
| Pact sağlayıcı (provider) belgeleri, doğrulama (verification) sürecini tanımlar | https://docs.pact.io/provider/ | 2026-10-13 | HTTP 200 doğrulandı |
| Pact Broker belgeleri, sözleşmelerin saklanıp doğrulandığı aracın tanımıdır | https://docs.pact.io/pact_broker | 2026-10-13 | HTTP 200 doğrulandı |
| Pact Specification deposu, sözleşme biçimlendirme spesifikasyonunun kaynağıdır | https://github.com/pact-foundation/pact-specification | 2026-10-13 | HTTP 200 doğrulandı |
| Pact kurumsal sayfası, proje tanıtımı ve doküman bağlantılarını içerir | https://pact.io/ | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 409 Conflict sayfası, çakışma durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/409 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 412 Precondition Failed sayfası, ön koşul hatası durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/412 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 400 Bad Request sayfası, istek hatası durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 404 Not Found sayfası, bulunamama durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/404 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 429 Too Many Requests sayfası, istek sınırı durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/429 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 500 Internal Server Error sayfası, sunucu hatası durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/500 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 200 OK sayfası, başarılı yanıt durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/200 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 415 Unsupported Media Type sayfası, desteklenmeyen tip durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/415 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 304 Not Modified sayfası, koşullu yanıt durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/304 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN 406 Not Acceptable sayfası, kabul edilememe durum kodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/406 | 2026-10-13 | HTTP 200 doğrulandı |
| MDN Allow başlığı sayfası, 405 yanıtındaki izinli method listesinin tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Allow | 2026-10-13 | HTTP 200 doğrulandı |
| MDN ETag başlığı sayfası, kaynak etiketinin tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/ETag | 2026-10-13 | HTTP 200 doğrulandı |
| MDN Content-Type başlığı sayfası, medya tipinin tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Type | 2026-10-13 | HTTP 200 doğrulandı |
| MDN koşullu istekler sayfası, If-None-Match / 304 akışının tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Conditional_requests | 2026-10-13 | HTTP 200 doğrulandı |
| MDN HEAD metodu sayfası, başlık-getirme istek metodunun tanımıdır | https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD | 2026-10-13 | HTTP 200 doğrulandı |
| IETF RFC 9110, HTTP semantiği (durum kodları ve methodlar) spesifikasyonudur | https://datatracker.ietf.org/doc/html/rfc9110 | 2026-10-13 | HTTP 200 doğrulandı |
| IETF RFC 7231, HTTP/1.1 semantik ve içerik spesifikasyonudur (tarihsel referans) | https://datatracker.ietf.org/doc/html/rfc7231 | 2026-10-13 | HTTP 200 doğrulandı |
| Microsoft Learn API tasarımı en iyi uygulamaları, API sözleşmesi ve sürümleme önerilerini açıklar | https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design | 2026-10-13 | HTTP 200 doğrulandı |

**Not:** 24 URL `.ai/architecture/**` envanterinde (1441 benzersiz URL) bulunmaz; başka bir bu
görevde üretilen dosyayla da kesişmez (kesişim 0).

## §17 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1441 benzersiz URL (temel ölçüm) | 2026-10-13 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest -Method Head` | 24/24 HTTP 200 | 2026-10-13 |
| 3 | Envantere kesişim | küme karşılaştırması | 0 | 2026-10-13 |
| 4 | Repo satırları | `Select-Object -Index (N-1)` | 33 kanıt satırı doğrulandı | 2026-10-13 |
| 5 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-13 |
| 6 | Mojibake | UTF-8 okuma | 0 | 2026-10-13 |
| 7 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-13 |
| 8 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-13 |
| 9 | `parent:` alanı | frontmatter taraması | 0 (yalnız `parent_id`) | 2026-10-13 |
| 10 | group alanı | frontmatter | `mimari` (yazılmış, tırnaksız) | 2026-10-13 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode