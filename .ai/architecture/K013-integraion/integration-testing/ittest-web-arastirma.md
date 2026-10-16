---
title: "Integration Testing — Web Araştırması (Kanıt Dosyası)"
node_id: K013-integraion/integration-testing/ittest-web-arastirma
group: kanit
series: integration-testing
parent: K013-integraion/integration-testing
siblings:
  - ittest-anayasa.md
  - ittest-sozlesme.md
  - ittest-dogrulama.md
  - ittest-kod-ornekleri.md
  - ittest-web-arastirma.md
date: 2026-10-16
authority: "Bayram Ali / Vault Steward"
mode: "Red Team · Human Mode · Truth Mode"
status: active
evidence: "17 benzersiz URL 2026-10-16'da fetch ile doğrulandı; 12 reddedilen 404/boş yanıt gerekçesiyle kayıtlı; 4 repo dosyası satır numarasıyla"
validation: "Her iddia tablosunda URL + tarih + Durum; doğrulanamayan tek iddia [X1]/[X2] etiketiyle VERIFICATION REQUIRED"
open_questions: "Q1 (K013↔K019 sınır sahibi), Q2 (test edilen canlı ortam adresi), Q3 (pact broker), Q4 (Swift/Elixir kaynak erişimi), Q5 (CI kapısı sertliği)"
---

# Integration Testing — Web Araştırması (Kanıt Dosyası)

> **Bu dosya ne?** 5 dosyalık `ittest-` serisinin **kanıt** katmanı. Diğer dört dosyada
> öne sürülen her dış iddianın kaynak URL'si, erişim tarihi ve doğrulama durumu burada
> tek yerde toplanır. Repo içi (disk) kanıtlar ayrı tabloda, `file + satır` ile verilir.
>
> **Kural (Zero-Hallucination):** Doğrulanamayan iddia yazılmaz; yazılmışsa
> `⚠️ VERIFICATION REQUIRED` etiketi taşır ve §Açık Sorular'a düşer.

---

## §0 ELI15 — Bu dosyanın tek cümlelik özü

"İddia + kaynak + tarih + durum" dörtlüsünü her satırda görmek zorundasın; bir satırda
URL yoksa o satır kanıt değildir, fikirdir — fikirleri diğer dört dosyada okursun.

---

## §1 Doğrulama Yöntemi (nasıl kontrol edildi)

| Adım | Ne yapıldı | Sonuç |
|---|---|---|
| 1 | Belirlenen her URL `fetch` ile 2026-10-16 tarihinde çağrıldı | Yanıt alındı → aday kaynak |
| 2 | Yanıt gövdesi içerik olarak okundu (boş mu, 404 sayfası mı?) | İçerik var + konu ile ilgili → **doğrulandı** |
| 3 | Boş yanıt / 404 / taşınmış sayfa | **Reddedildi** — gerekçesi §5 tablosunda |
| 4 | Doğrulanamayan ama örneğe ihtiyaç duyulan dil (Swift, Elixir) | `[X1]`/`[X2]` etiketi + `⚠️ VERIFICATION REQUIRED` |
| 5 | Repo içi iddialar | Dosya yolu + satır numarası ile §4 tablosunda |

**Doğrulama tarihi (tüm satırlar için ortak):** `2026-10-16`

---

## §2 İddia Tabloları — Kaynaklı Dış İddialar (asıl kanıt)

> Her tablo: `| İddia | URL | 2026-10-16 | Durum |`
> "Durum" = `✔ Doğrulandı` (fetch + içerik okundu) · `⚠️ VERIFICATION REQUIRED` (erişilemedi)

### §2.1 Test pyramid — üç katman ayrımı (`ittest-anayasa.md` §2-§3)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| "Practical Test Pyramid" yazısı birim/entegrasyon/UUI katmanlarını ve oran önerisini anlatır | https://martinfowler.com/articles/practical-test-pyramid.html | 2026-10-16 | ✔ Doğrulandı |
| Fowler "IntegrationTest" bliki'si: entegrasyon testi "ara birimlerin davranışını doğrular, birimden büyük, UUI'den küçük" der | https://martinfowler.com/bliki/IntegrationTest.html | 2026-10-16 | ✔ Doğrulandı |
| Fowler "ContractTest" bliki'si: sözleşme testinin sağlayıcı-penceresinden yazıldığını ve consumer-driven olduğunu anlatır | https://martinfowler.com/bliki/ContractTest.html | 2026-10-16 | ✔ Doğrulandı |
| Dokümantasyon "contract testing" ile "integration testing" kavramlarını birbirinden ayırır | https://docs.pact.io/ | 2026-10-16 | ✔ Doğrulandı |

**Seriye etkisi:** `ittest-anayasa.md` §2'deki katman tanımları ve §3'teki
"canlı vs mock" tartışması bu üç kaynak + Pact dokümanı üzerine kuruludur.

### §2.2 Çekirdek kütüphane / araç davranışı (12 dilin resmi kaynağı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| PHPUnit 10.5 `@group` anotasyonu ve test gruplama davranışı resmi olarak belgelenmiştir | https://docs.phpunit.de/en/10.5/ | 2026-10-16 | ✔ Doğrulandı |
| Jest "Mock Functions": `jest.mock` + mock return değeri ile modül taklidi resmi akıştır | https://jestjs.io/docs/mock-functions | 2026-10-16 | ✔ Doğrulandı |
| Vitest mock yetenekleri (`vi.mock`, modül taklidi) "Features" sayfasında tanımlıdır | https://vitest.dev/guide/features | 2026-10-16 | ✔ Doğrulandı |
| pytest fixture + `pytest.raises` ile hem düzelti hem hata yolu testi resmi akıştır | https://docs.pytest.org/en/stable/ | 2026-10-16 | ✔ Doğrulandı |
| Go `testing` paketi: tablo destekli test, `t.Run` alt testleri resmi anlatımıdır | https://go.dev/testing | 2026-10-16 | ✔ Doğrulandı |
| Go `net/http/httptest`: sahte HTTP sunucusu ile istemci testi resmi paketidir | https://pkg.go.dev/net/http/httptest | 2026-10-16 | ✔ Doğrulandı |
| JUnit 5 Kullanıcı Kılavuzu: `@Tag` (grup), `@DisplayName`, Extension Model resmi referanstır | https://junit.org/junit5/docs/current/user-guide/ | 2026-10-16 | ✔ Doğrulandı |
| xUnit.net ana sayfası: .NET için test çerçevesi konumlandırması ve belgeleri | https://xunit.net/ | 2026-10-16 | ✔ Doğrulandı |
| WireMock (Rust): HTTP servis sahteleme (stub + scenario) kütüphanesidir | https://docs.rs/wiremock/latest/wiremock/ | 2026-10-16 | ✔ Doğrulandı |
| GoogleTest: C++ test çerçevesi + `TEST`/fixture yapısı resmi dokümantasyonudur | https://google.github.io/googletest/ | 2026-10-16 | ✔ Doğrulandı |
| RSpec: Ruby test/dokümantasyon çerçevesi; `let`, matcher, mock doğrulaması resmi kılavuzdadır | https://rspec.info/documentation/ | 2026-10-16 | ✔ Doğrulandı |
| Kotest: Kotlin test çerçevesi; stil/format ve davranış odaklı test ana kaynaktır | https://kotest.io/docs/ | 2026-10-16 | ✔ Doğrulandı |
| Playwright "Mock" sayfası: `page.route` ile ağ isteklerini sahteleme resmi yoludur | https://playwright.dev/docs/mock | 2026-10-16 | ✔ Doğrulandı |

**Seriye etkisi:** `ittest-kod-ornekleri.md` içindeki 12 dil örneğinin her biri bu
tablodaki **tek bir resmi kaynakla** eşleşir; örneklerde uydurma API adı yoktur.

### §2.3 Doğrulanamayan erişimler (etiketli — unutulmamalı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Swift `Testing` / XCTest örneği ([X1]) — dil kanıtı bu turda erişilemedi | https://developer.apple.com/documentation/xctest | 2026-10-16 | ⚠️ VERIFICATION REQUIRED |
| Elixir `ExUnit` örneği ([X2]) — dil kanıtı bu turda erişilemedi | https://hexdocs.pm/exunit | 2026-10-16 | ⚠️ VERIFICATION REQUIRED |
| xUnit ".NET Core başlangıç" sayfası (adres taşınmış olabilir) | https://xunit.net/docs/getting-started/netcore | 2026-10-16 | ⚠️ VERIFICATION REQUIRED |
| Microsoft .NET birim test genel bakış sayfası (adres taşınmış olabilir) | https://learn.microsoft.com/en-us/dotnet/core/testing/unit-testing-overview | 2026-10-16 | ⚠️ VERIFICATION REQUIRED |

**Not:** Bu dört satır `ittest-kod-ornekleri.md` §6'daki [X1]/[X2] etiketlerinin
karşılığıdır. Seri bunları "biliniyor" gibi sunmaz — **bilinmiyor** der.

### §2.4 İddia → Seride Kullanıldığı Yer (cross-reference)

> Her dış kaynağın hangi dosyada, hangi bölümde kullanıldığı. Satır numaraları
> 2026-10-16 disk okumasıyla tespit edildi (dosya değişirse yeniden sayılmalıdır).

| Kaynak | Kullanan dosya | Bölüm / satır | Kullanım şekli |
|---|---|---|---|
| Fowler IntegrationTest | `ittest-anayasa.md` | §Kaynak S1, satır 337 | Dar/geniş entegrasyon tanımı |
| Fowler Practical Test Pyramid | `ittest-anayasa.md` | §Kaynak S2, satır 338 | Piramit katmanları + oran kuralı |
| docs.pact.io | `ittest-anayasa.md` | §Kaynak S3, satır 339 | Contract test tanımı |
| Fowler ContractTest | `ittest-anayasa.md` | §Kaynak S4, satır 340 | Ritim + canlıya dikkat |
| Fowler Practical Test Pyramid | `ittest-sozlesme.md` | §Kaynak S1, satır 334 | Üretimde test yasağı + WireMock uç |
| Fowler ContractTest | `ittest-sozlesme.md` | §Kaynak S2, satır 335 | Günde 1 contract ritmi |
| docs.pact.io | `ittest-sozlesme.md` | §Kaynak S3, satır 336 | "Contract by example" ≠ statik şema |
| Playwright Mock | `ittest-sozlesme.md` | §Kaynak S4, satır 337 | HAR kayıt/replay + route engelleme |
| docs.pact.io | `ittest-dogrulama.md` | §Kaynak S1, satır 332 | Consumer/provider + sağlayıcı testi |
| Fowler ContractTest | `ittest-dogrulama.md` | §Kaynak S2, satır 333 | Snapshot/stub biçimi, başarısızlık yönetimi |
| Playwright Mock | `ittest-dogrulama.md` | §Kaynak S3, satır 334 | HAR `update` ile yenileme |
| PHPUnit 10.5 | `ittest-dogrulama.md` | §Kaynak S4, satır 335 | Çalıştırma/raporlama referansı |
| Fowler Practical Test Pyramid | `ittest-dogrulama.md` | §Kaynak S5, satır 336 | Taklit uç + konfigürasyonla uç değiştirme |
| PHPUnit 10.5 | `ittest-kod-ornekleri.md` | §Kaynak S1, satır 573 | PHP örneği |
| Jest Mock Functions | `ittest-kod-ornekleri.md` | §Kaynak S2, satır 574 | JS örneği |
| Vitest Features | `ittest-kod-ornekleri.md` | §Kaynak S3, satır 575 | JS/TS (Vitest) örneği |
| pytest | `ittest-kod-ornekleri.md` | §Kaynak S4, satır 576 | Python örneği |
| Go testing | `ittest-kod-ornekleri.md` | §Kaynak S5, satır 577 | Go örneği |
| net/http/httptest | `ittest-kod-ornekleri.md` | §Kaynak S6, satır 578 | Go sahte sunucu |
| JUnit 5 User Guide | `ittest-kod-ornekleri.md` | §Kaynak S7, satır 579 | Java örneği |
| xUnit.net | `ittest-kod-ornekleri.md` | §Kaynak S8, satır 580 | C# örneği |
| Fowler Practical Test Pyramid | `ittest-kod-ornekleri.md` | §Kaynak S9, satır 581 | Arrange/Act/Assert + WireMock Java |
| docs.rs/wiremock | `ittest-kod-ornekleri.md` | §Kaynak S10, satır 582 | Rust örneği |
| GoogleTest | `ittest-kod-ornekleri.md` | §Kaynak S11, satır 583 | C++ örneği |
| RSpec | `ittest-kod-ornekleri.md` | §Kaynak S12, satır 584 | Ruby örneği |
| Kotest | `ittest-kod-ornekleri.md` | §Kaynak S13, satır 585 | Kotlin örneği |
| XCTest (Apple) | `ittest-kod-ornekleri.md` | §Kaynak X1, satır 586 | ❌ çekilemedi → [X1] |
| ExUnit (hexdocs) | `ittest-kod-ornekleri.md` | §Kaynak X2, satır 587 | ❌ 404 → [X2] |

**Toplam kullanım satırı: 28** (17 doğrulanmış kaynak × en az bir kullanım).

---

## §3 Kaynak Erişim Günlüğü (erişim sırası ve sonuç)

| # | URL (kısa) | Sonuç | Not |
|---|---|---|---|
| 1 | docs.phpunit.de/en/10.5 | ✔ | Ana sayfa + gruplama içeriği okundu |
| 2 | jestjs.io/docs/mock-functions | ✔ | Mock fonksiyon akışı okundu |
| 3 | vitest.dev/guide/features | ✔ | `vi.mock` bölümü okundu |
| 4 | docs.pytest.org/en/stable | ✔ | Fixture/raises akışı okundu |
| 5 | go.dev/testing | ✔ | Paket tanımı okundu |
| 6 | pkg.go.dev/net/http/httptest | ✔ | Paket tanımı okundu |
| 7 | junit.org/junit5/docs/current/user-guide | ✔ | Tag/Extension bölümleri okundu |
| 8 | xunit.net/ | ✔ | Ana belgeler okundu |
| 9 | docs.rs/wiremock/latest/wiremock | ✔ | Crate açıklaması okundu |
| 10 | google.github.io/googletest/ | ✔ | Ana doküman okundu |
| 11 | rspec.info/documentation | ✔ | Dizin okundu |
| 12 | kotest.io/docs | ✔ | Dizin okundu |
| 13 | docs.pact.io | ✔ | Ana sayfa okundu |
| 14 | martinfowler.com/bliki/ContractTest | ✔ | Bliki metni okundu |
| 15 | martinfowler.com/bliki/IntegrationTest | ✔ | Bliki metni okundu |
| 16 | martinfowler.com/articles/practical-test-pyramid | ✔ | Makale metni okundu |
| 17 | playwright.dev/docs/mock | ✔ | Mock sayfası okundu |

**Toplam doğrulanmış benzersiz URL: 17** (gerekçe ≥12 idi — §2.2'de 13, §2.1'de 4).

---

## §4 Repo (Disk) Kanıtları — dosya + satır

> Dış kaynaklar "neyin doğru olduğunu" anlatır; **bu depoda ne olduğu** burada kanıtlanır.
> Tüm satır numaraları 2026-10-16 disk okumasıyla doğrulandı.

| # | İddia (bu depoda) | Dosya | Satır | Durum |
|---|---|---|---|---|
| R1 | Integration test suite **kaldırıldı**; `tests/Integration/` dizini diskte YOK | `auth.coremusic.net/phpunit.xml` | 10-12 | ✔ Doğrulandı |
| R2 | CI yalnız `--testsuite Unit` çalıştırır; "tests/Integration dizini diskte YOK" yorumu vardır | `.github/workflows/ci.yml` | 77, 80 | ✔ Doğrulandı |
| R3 | Secret taraması `gitleaks/gitleaks-action@v2` ile push + PR'da çalışır | `.github/workflows/secret-scan.yml` | 29 | ✔ Doğrulandı |
| R4 | `.gitleaks.toml` diskte YOK → varsayılan gitleaks konfigürasyonu kullanılır | `.github/workflows/secret-scan.yml` | 4 | ✔ Doğrulandı |
| R5 | Tek mock kaynağı: `createMockFetch` (başarı/hata senaryoları) | `assets.coremusic.net/tests/mocks/player-api.mock.js` | 1-184 | ✔ Doğrulandı |
| R6 | api tarafında tek suite: Unit (`tests/Unit/...` RouteConfigTest 208 satır) | `api.coremusic.net/phpunit.xml` · `api.coremusic.net/tests/Unit/RouteConfigTest.php` | suite bloğu · 1-208 | ✔ Doğrulandı |
| R7 | shared phpunit config'te 8 suite vardır; **Integration yok** | `shared/phpunit.xml` | suite listesi | ✔ Doğrulandı |
| R8 | Kapsam sınırı sahibi: K019 entegrasyon test ortamı dokümanı (525 satır) | `.ai/architecture/K019-testing/integration/integration-test-ortami.md` | 1-525 | ✔ Doğrulandı |
| R9 | js-coverage ve js-e2e job'ları `continue-on-error: true` (yumuşak kapı) | `.github/workflows/ci.yml` | 167, 183 | ✔ Doğrulandı |

**Toplam repo kanıtı: 9 madde, hepsi `dosya + satır` ile.**

### §4.1 Repo Kanıtı — Satır İçi Alıntılar (okunan metnin kendisi)

> Yukarıdaki tablodaki iddiaların dayandığı **gerçek satır metinleri** (kısaltılmış; `…` geri
> kalanı temsil eder). Alıntılar dosyalardan birebir kopyalandır.

**R1 — `auth.coremusic.net/phpunit.xml`, satır 10-12**

```xml
<!-- Integration suite KALDIRILDI (2026-09-29): tests/Integration/ dizini diskte YOK
     -> PHPUnit 10 ... exit 2 -->
```

**R2 — `.github/workflows/ci.yml`, satır 77 ve 80**

```yaml
# tests/Integration dizini diskte YOK
      - run: ... --testsuite Unit
```

**R3 — `.github/workflows/secret-scan.yml`, satır 29**

```yaml
        uses: gitleaks/gitleaks-action@v2
```

**R4 — `.github/workflows/secret-scan.yml`, satır 4**

```yaml
# diskte .gitleaks.toml YOK -> varsayilan gitleaks config
```

**R5 — `assets.coremusic.net/tests/mocks/player-api.mock.js`, satır 1-184**

```js
// createMockFetch — senaryolar: success / notfound / unauthorized / servererror / network
```

**R9 — `.github/workflows/ci.yml`, satır 167 ve 183**

```yaml
        continue-on-error: true   # js-coverage (167) · js-e2e (183)
```

**Yorum (kanıt → çıkarım ayrımı):** R1+R2 **gözlem** = "bu depoda entegrasyon suite'i
yoktur"; "entegrasyon testi yazılmalıdır" ise **öneri**dir ve `ittest-anayasa.md` §6'da
Q1 olarak açıktır. Bu dosya öneriyi kanıt diye sunmaz.

### §4.2 Repo Kanıtı — Sayılar (2026-10-16 disk ölçümü)

| Ölçüm | Değer | Kaynak |
|---|---|---|
| `api.coremusic.net/tests/Unit/RouteConfigTest.php` | 208 içerik satırı | disk okuma |
| `assets.coremusic.net/tests/mocks/player-api.mock.js` | 184 içerik satırı | disk okuma |
| `.github/workflows/ci.yml` | 203 içerik satırı | disk okuma |
| `.github/workflows/secret-scan.yml` | 32 satır | disk okuma |
| `shared/phpunit.xml` suite sayısı | 8 (Integration dahil değil) | disk okuma |
| K019 sınır dokümanı | 525 satır | disk okuma |
| Bu serideki dosya sayısı | 5 içerik + 1 index | disk okuma |

---

## §5 Reddedilen Kaynaklar (404 / boş yanıt — gerekçeli)

> **Kural:** erişilemeyen kaynak "kaynak" sayılmaz. Hangi adrese neden güvenir
> güvenirmez — hepsi burada. Yüzde 404 listesi uydurma değildir; 2026-10-16 taramasının çıktısıdır.

| # | Reddedilen URL | Gerekçe | Sonuç |
|---|---|---|---|
| 1 | https://google.github.io/googletest/gmock-cheatsheet.html | Erişilemedi (gmock cheat-sheet adresi bu turda yanıt vermedi) | ✘ Reddedildi |
| 2 | https://xunit.net/docs/getting-started/netcore | Erişilemedi (sayfa taşınmış olabilir) | ✘ Reddedildi |
| 3 | https://xunit.net/docs/getting-started/intro | Erişilemedi (giriş sayfası bu turda yanıt vermedi) | ✘ Reddedildi |
| 4 | https://hexdocs.pm/exunit | Erişilemedi (boş yanıt) | ✘ Reddedildi — [X2] etiketi taşındı |
| 5 | https://hexdocs.pm/exunit/ExUnit.html | Erişilemedi (boş yanıt) | ✘ Reddedildi |
| 6 | https://hexdocs.pm/exunit/ExUnit.Case.html | Erişilemedi (boş yanıt) | ✘ Reddedildi |
| 7 | https://hexdocs.pm/exunit/1.19/ExUnit.html | Erişilemedi (boş yanıt) | ✘ Reddedildi |
| 8 | https://elixir-lang.org/docs/stable/exunit/ExUnit.html | Erişilemedi | ✘ Reddedildi |
| 9 | https://kotest.io/docs/framework/mocking.html | Erişilemedi (alt sayfa bu turda yanıt vermedi; kök `/docs` ✔ doğrulandı) | ✘ Reddedildi |
| 10 | https://learn.microsoft.com/en-us/dotnet/core/testing/unit-testing-overview | Erişilemedi (sayfa taşınmış olabilir) | ✘ Reddedildi |
| 11 | https://developer.apple.com/tutorials/data/documentation/swift/testing/ | Erişilemedi (adres biçimi hatalı olabilir) | ✘ Reddedildi |
| 12 | https://developer.apple.com/documentation/xctest | Yanıt **boş** geldi (içerik okunamadı) | ✘ Reddedildi — [X1] etiketi taşındı |

**Reddedilen: 12** · **Doğrulanmış: 17** · Toplam denenmiş: 29.

### §5.1 Reddedilen Kaynakların Seriye Etkisi (hangi iddia etkilendi?)

| Reddedilen kaynak | Etkilenen iddia | Serideki karşılığı | Tavır |
|---|---|---|---|
| gmock-cheatsheet (GoogleTest) | "gmock ile mock davranışı" ayrıntısı | `ittest-kod-ornekleri.md` C++ örneği yalnız ana `google.github.io/googletest/` §S11'e dayanır | Cheatsheet iddiası **kullanılmadı** |
| xUnit başlangıç sayfaları (×2) | ".NET Core'a özel kurulum adımları" | C# örneği yalnız `xunit.net/` §S8'e dayanır | Kurulum adımı **yazılmadı** |
| hexdocs.pm/exunit (×4) + elixir-lang.org | [X2] Elixir `ExUnit` örneği | `ittest-kod-ornekleri.md` §6 [X2] etiketi | Örnek **taslak**, `⚠️ VERIFICATION REQUIRED` |
| Kotest mocking alt sayfası | "Kotest mock kütüphanesi" adı/USE'si | Kotlin örneği yalnız `/docs` §S13'e dayanır; mock API adı **kullanılmadı** | Mock API adı yazılmadı |
| MS .NET unit-testing-overview | "Microsoft'un önerdiği test düzeni" | Yazıda MS'e atıf **yok** | Atıf kullanılmadı |
| Apple Swift testing tutorials | [X1] Swift `Testing`/XCTest örneği | `ittest-kod-ornekleri.md` §6 [X1] etiketi | Örnek **taslak** |
| developer.apple.com/documentation/xctest (boş yanıt) | XCTest API adları | XCTest API adı seride **yalnız taslakta**, doğrulanmış sayılmaz | `[X1]` ile işaretli |

**Kural:** reddedilen kaynak → ilgili iddia ya kaldırıldı ya da `⚠️ VERIFICATION REQUIRED`
etiketi aldı. Kaynaksız iddia seride bırakılmadı.

---

## §6 Doğrulama Matrisi — hangi dosya hangi kaynağa dayanıyor

| Dosya | Dayandığı dış kaynaklar (§2.1 / §2.2) | Dayandığı repo kanıtları (§4) |
|---|---|---|
| `ittest-anayasa.md` | Fowler ×3, docs.pact.io, PHPUnit, JUnit 5 | R1, R2, R6, R7, R8 |
| `ittest-sozlesme.md` | docs.pact.io, Fowler ContractTest, Playwright mock | R5, R8 |
| `ittest-dogrulama.md` | docs.pact.io, Fowler ContractTest, Jest/Vitest mock | R1, R2, R3, R4, R5, R9 |
| `ittest-kod-ornekleri.md` | §2.2 içindeki 13 resmi dil kaynağı (12 dil) | R5, R6 |
| `ittest-web-arastirma.md` (bu dosya) | 17 doğrulanmış URL | R1-R9 |

---

## §7 Kapsam Dışı (bu dosyada işlenmez)

- **Test kodu yazımı** → `ittest-kod-ornekleri.md`
- **Sözleşmenin metni** → `ittest-sozlesme.md`
- **CI/secret kapısının tasarımı** → `ittest-dogrulama.md`
- **Canlı ortam testinin prosedürü ve sahipliği** → K019 `integration-test-ortami.md` (Q1)
- **Kurulum/kurallar** → `ittest-anayasa.md`

---

## §8 Açık Sorular (serinin tam listesi — bu dosya toplar)

| # | Soru | Nerede açıldı | Durum |
|---|---|---|---|
| Q1 | K013 (bu seri) ↔ K019 (`integration-test-ortami.md`) **sınır sahibi** kim? İki doküman da canlı entegrasyon testinden söz ediyor | `ittest-anayasa.md` §6 | AÇIK |
| Q2 | Live-test onay gate'i **hangi adrese** istek atacak? Bu depoda test edilen canlı ortam adresi tanımlı değil | `ittest-sozlesme.md` §4.4, `ittest-dogrulama.md` §7 | AÇIK |
| Q3 | Pact broker kurulacak mı? Kurulmazsa consumer-driven sözleşme nerede saklanacak? | `ittest-sozlesme.md` §3.4 | AÇIK |
| Q4 | Swift [X1] ve Elixir [X2] kaynakları erişilemedi — bu iki dil örneği doğrulanana kadar "taslak" sayılacak | `ittest-kod-ornekleri.md` §6 | AÇIK |
| Q5 | `ci.yml` job sertliği: js-coverage/js-e2e şu an `continue-on-error: true` (R9); entegrasyon kapısı sert mi yumuşak mı olacak? | `ittest-dogrulama.md` §5.5 | AÇIK |

---

## § Okuma Haritası — 5 Dosyalık Seri

| Sıra | Dosya | Grup | Ne verir |
|---|---|---|---|
| 1 | `ittest-anayasa.md` | mimari | Kavramlar, pyramid, canlı vs mock, K019 sınırı |
| 2 | `ittest-sozlesme.md` | mimari | Ortam sözleşmesi, stub sözleşmesi, canlı-test onay kapısı |
| 3 | `ittest-dogrulama.md` | kod | Sözleşme doğrulama, mock tutarlılığı, eski kayıt, secret, CI |
| 4 | `ittest-kod-ornekleri.md` | kod | 12 dilde entegrasyon testi örneği |
| 5 | `ittest-web-arastirma.md` | kanit | **Bu dosya** — tüm iddiaların kaynağı ve durumu |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | Kaynak | URL | Erişim | Durum |
|---|---|---|---|---|
| 1 | PHPUnit 10.5 Manual | https://docs.phpunit.de/en/10.5/ | 2026-10-16 | ✔ Doğrulandı |
| 2 | Jest — Mock Functions | https://jestjs.io/docs/mock-functions | 2026-10-16 | ✔ Doğrulandı |
| 3 | Vitest — Features (mock) | https://vitest.dev/guide/features | 2026-10-16 | ✔ Doğrulandı |
| 4 | pytest Documentation | https://docs.pytest.org/en/stable/ | 2026-10-16 | ✔ Doğrulandı |
| 5 | Go — testing package | https://go.dev/testing | 2026-10-16 | ✔ Doğrulandı |
| 6 | Go — net/http/httptest | https://pkg.go.dev/net/http/httptest | 2026-10-16 | ✔ Doğrulandı |
| 7 | JUnit 5 User Guide | https://junit.org/junit5/docs/current/user-guide/ | 2026-10-16 | ✔ Doğrulandı |
| 8 | xUnit.net | https://xunit.net/ | 2026-10-16 | ✔ Doğrulandı |
| 9 | WireMock (Rust) — docs.rs | https://docs.rs/wiremock/latest/wiremock/ | 2026-10-16 | ✔ Doğrulandı |
| 10 | GoogleTest | https://google.github.io/googletest/ | 2026-10-16 | ✔ Doğrulandı |
| 11 | RSpec — Documentation | https://rspec.info/documentation/ | 2026-10-16 | ✔ Doğrulandı |
| 12 | Kotest — Docs | https://kotest.io/docs/ | 2026-10-16 | ✔ Doğrulandı |
| 13 | Pact Documentation | https://docs.pact.io/ | 2026-10-16 | ✔ Doğrulandı |
| 14 | Fowler — ContractTest bliki | https://martinfowler.com/bliki/ContractTest.html | 2026-10-16 | ✔ Doğrulandı |
| 15 | Fowler — IntegrationTest bliki | https://martinfowler.com/bliki/IntegrationTest.html | 2026-10-16 | ✔ Doğrulandı |
| 16 | Fowler — Practical Test Pyramid | https://martinfowler.com/articles/practical-test-pyramid.html | 2026-10-16 | ✔ Doğrulandı |
| 17 | Playwright — Mock | https://playwright.dev/docs/mock | 2026-10-16 | ✔ Doğrulandı |
| 18 | XCTest (Apple) | https://developer.apple.com/documentation/xctest | 2026-10-16 | ✘ Reddedildi (boş yanıt) — [X1] |
| 19 | ExUnit (hexdocs) | https://hexdocs.pm/exunit | 2026-10-16 | ✘ Reddedildi — [X2] |
| 20 | Diğer 10 reddedilen adres | §5 tablosu | 2026-10-16 | ✘ Reddedildi (gerekçe §5) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
