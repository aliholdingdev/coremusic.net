---
node_id: K009-security/security-testing/security-testing-anayasa
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing
  - K009-security/security-test-stratejisi
  - K17-devops
evidence:
  - "shared/tests/ — 45 dosya / 41 *Test.php sınıfı (glob ölçümü 2026-10-15)"
  - ".github/workflows/ci.yml:62-64 PHPStan · :104-108 PHPUnit · :123-127 composer audit"
  - ".github/workflows/secret-scan.yml:28-31 gitleaks adımı"
validation:
  - "2026-10-15 — RED kriterleri §3 · WSTG kapsamı §5 · SAST/DAST/negatif ayrımı §6 · secret-scan §7"
open_questions:
  - "Statik analiz PHPStan ile sınırlı; ayrık SAST aracı seçimi → ⚠️ VERIFICATION REQUIRED"
  - "DAST aracı (dinamik tarama) CI'a bağlı değil → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · security-testing · Anayasa

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Rol: güvenlik testi KAPISI — red kriterleri ve üretim yasakları (mimari).

## §1 AMAÇ VE KAPSAM

| Alan | Değer |
|---|---|
| node_id | K009-security/security-testing/security-testing-anayasa |
| node_type | module |
| parent_id | K009-security |
| Grup | mimari |
| Kapsam | güvenlik testi kapısı · red kriterleri · WSTG kapsamı · SAST/DAST/negatif ayrımı · secret-scan · bypass testi |
| Kapsam dışı | genel QA test kapsamı → `K019-testing` · uygulama işlevsel testi → `K011-application` (bkz. `security-testing-kapsam.md`) |
| Test planı (vakalar) | `security-test-stratejisi.md` (kök) — bu dosya planı TEKRARLAMAZ, yalnız **kapıyı** tanımlar |
| Sürüş akışı | `security-testing-surucu.md` |
| Gerçek alıntılar | `security-testing-ornekler.md` |

## §2 TERİMLER (TR / EN)

| Türkçe | English | Tanım |
|---|---|---|
| Test kapısı | Quality gate | Bir aşamadan geçiş için zorunlu koşul seti |
| Red kriteri | Rejection criterion | Koşul sağlanmazsa değişikliğin geri çevrilmesi |
| Statik analiz | SAST (Static Application Security Testing) | Kod çalıştırılmadan yapılan tarama |
| Dinamik analiz | DAST (Dynamic Application Security Testing) | Çalışan uygulamaya istek atarak yapılan tarama |
| Negatif test | Negative test | "reddedilmesi gereken" girdinin gerçekten reddedildiğini sınar |
| Bypass testi | Bypass test | Atlatma bayrağının yanlışlıkla açık kalmasını sınar |
| Secret tarama | Secret scanning | Repo geçmişine sızan anahtar/parola avı |
| Regresyon | Regression | Bilinen bir açığın geri dönmediğini doğrular |
| Fail-closed | Fail-closed | Koşul belirsizse erişimin REDDİ |
| Fail-open | Fail-open | Koşul belirsizse erişimin verilmesi (güvenlik için yasak) |
| Kanıt | Evidence | Dosya + satır numarası ile gösterilebilir gözlem |
| Belirsizlik etiketi | Uncertainty flag | `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` |

## §3 TEST KAPISI — RED KRİTERLERİ

> **Kural:** Bir aşağıdaki koşuldan biri sağlanmazsa ilgili değişiklik **RED** edilir; istisna onayı yoktur.

| # | Red kriteri | Sağlanmazsa | Ölçüm |
|---|---|---|---|
| R-01 | Yeni/changed güvenlik yüzeyi için test **yazıldı** | RED | test dosyası var mı |
| R-02 | Mevcut güvenlik testleri yeşil | RED | PHPUnit çıktısı |
| R-03 | Secret taraması temiz | RED | `secret-scan.yml` job sonucu |
| R-04 | Statik analiz (PHPStan) hata vermiyor | RED | `composer stan` çıktısı |
| R-05 | Negatif vaka eklendi (reddedilen girdi) | RED | test adı + assertion |
| R-06 | Fail-closed davranışı test edilmiş | RED | test adı |
| R-07 | Bypass bayrağı değiştiyse bypass testi de değişti | RED | birlikte değişiklik |
| R-08 | Yeni bağımlılık `composer audit`'ten geçiyor | RED | audit job |
| R-09 | CI job'ları `continue-on-error: true` OLMADAN yeşil | RED | workflow satırı |
| R-10 | "güvenli" iddiası Dosya·Satır kanıtı taşıyor | `⚠️ VERIFICATION REQUIRED` | kod okuması |

| Gate Kuralı | |
|---|---|
| Kısmi geçiş | Bir kapı kırmızıysa sonraki kapıya geçilmez |
| Manuel atlatma | Yalnız Vault Steward onayı + log kaydı ile |
| Uydurma iddia | "test geçiyor" yazıp kanıt sunmayan satır red |

## §4 TEST EDİLMEYEN GÜVENLİK KODU — ÜRETİM YASAĞI

| # | Madde | Yaptırım |
|---|---|---|
| A-01 | Güvenlik davranışı üreten kod (auth, CSRF, rate-limit, redaction, bypass, header) **test edilmeden merge edilmez** | Code review RED |
| A-02 | Testi olmayan güvenlik davranışı `status: draft` kalır; "tamamlandı" yazılmaz | vault kuralı |
| A-03 | Yalnız mutlu yol (happy path) testi güvenlik için **yeterli değildir** — negatif vaka zorunlu (§6) | RED |
| A-04 | Kod değişti, test değişmedi → kapı kırmızı (regresyon riski) | CI RED |
| A-05 | Test yazımı kod yazımından **sonra değil**, aynı iş kaleminde yapılır | review |
| A-06 | "Güvenli" iddiası Dosya·Satır kanıtı olmadan dokümana yazılmaz | Zero-Hallucination |
| A-07 | Test çalıştırılmadıysa durum `mevcut, çalıştırılmadı ⚠️` yazılır; `PASS` yazılmaz | Truth Mode |
| A-08 | Test altyapısı yoksa (shell/CI erişimi) boşluk `UNKNOWN` işaretlenir | Truth Mode |

| Mevcut Durum (kanıt) | |
|---|---|
| `shared/tests/` | 45 dosya · 41 `*Test.php` sınıfı (glob, 2026-10-15) |
| Güvenlik odaklı alt kümeler | `Middleware/` 9 · `Security/` 2 · `Api/` 8 · `Unit/Security` 2 · `Unit/Log` 2 |
| Test çalıştırma | bu vault oturumunda shell erişimi YOK → `PASS` iddiası yazılmadı ⚠️ |

## §5 OWASP WSTG KAPSAMI

| # | WSTG test alanı (TR) | WSTG (EN) | Bu domainde karşılığı | Vaka |
|---|---|---|---|---|
| 1 | Bilgi toplama | Information Gathering | rota/uç envanteri | kısmi |
| 2 | Yapılandırma ve dağıtım | Configuration & Deployment Management | header + CI kapıları | §6 / `surucu` §5 |
| 3 | Kimlik yönetimi | Identity Management Testing | K010 sınırı (dışarıda) | devir |
| 4 | Kimlik doğrulama | Authentication Testing | parola/API key/JWT | T-04, T-06, T-18 |
| 5 | Yetkilendirme | Authorization Testing | rol/izin reddi | T-11, T-12 |
| 6 | Oturum yönetimi | Session Management Testing | timeout/rotation/cookie | T-14, T-15, T-20 |
| 7 | Girdi doğrulama | Input Validation Testing | injection + open redirect | T-13, T-19 |
| 8 | Hata işleme | Error Handling | hata sızıntısı | AD-31..AD-33 (auth) |
| 9 | Kriptografi | Cryptography | hash/imza/redaction | T-04, T-17 |
| 10 | İş mantığı | Business Logic Testing | kapsam dışı → `K011-application` | devir |
| 11 | İstemci tarafı | Client-side Testing | XSS/CSP | T-16 (kısmi) |
| 12 | Raporlama | Reporting | bulgu listesi | `dogrulama` |

| WSTG Kuralı | |
|---|---|
| Tam bölüm numaralandırması | kök `security-web-arastirma.md` + `security-testing/index.md` §2 havuzuna bağlı — bu dosyada URL TEKRARLANMAZ |
| Kapsam yüzdesi | repo'da ölçülmedi → `⚠️ VERIFICATION REQUIRED` |
| WSTG senaryosu uygulaması | `security-testing/index.md` §2: "OWASP WSTG senaryosu uygulaması | yok" |

## §6 SAST / DAST / NEGATİF TEST AYRIMI

| Sınıf | Ne yapar | Çalıştırma anı | Repo durumu | Kanıt |
|---|---|---|---|---|
| SAST — statik | Kodu okur, kalıp/aralık hataları bulur | commit / CI | VAR (PHPStan level 5) | `shared/composer.json:48` · `ci.yml:62-64` |
| DAST — dinamik | Çalışan uygulamaya istek atar | yayına yakın ortam | YOK | ⚠️ VERIFICATION REQUIRED |
| Negatif test | Reddedilmesi gereken girdiyi gönderir | lokal + CI | VAR (çok sayıda 403/429 assertion) | `ornekler` §2-§6 |
| Regresyon | Bilinen açık tekrar açılıyor mu | CI | VAR (ADR-010/013/020 testleri) | `ornekler` §2, §5 |
| Secret scan | Repo geçmişinde anahtar avı | push + PR | VAR (gitleaks) | `secret-scan.yml:28-31` |
| Bağımlılık taraması | Bilinen CVE'li paket | CI | VAR (`composer audit`) | `ci.yml:123-127` |

| Ayrım Kuralı | |
|---|---|
| SAST ≠ DAST | PHPStan dinamik çalışmaz; DAST yoksa "sızma testi yapıldı" DENMEZ |
| Negatif test ≠ DAST | Negatif test kod içi örnektir; ağdan istek atmaz |
| DAST boşluğu | `index.md` §2'de `DAST aracı | yok | ⚠️` olarak kayıtlıdır |

## §7 SECRET-SCAN ZORUNLULUĞU

| # | Kural | Yaptırım | Kanıt |
|---|---|---|---|
| S-01 | Secret taraması **her push ve PR'da** çalışır | CI gate | `secret-scan.yml:7-9` |
| S-02 | Tarama geçmişin tamamını görür (`fetch-depth: 0`) | zorunlu | `secret-scan.yml:26` |
| S-03 | Tarama başarısızsa merge kapıda durur | RED | job sonucu |
| S-04 | `.gitleaks.toml` diskte YOK → varsayılan config kullanılır | bilgi | `secret-scan.yml:4` yorum satırı |
| S-05 | Secret değeri `.md` / `.json` / `.log` içine yazılmaz | yasak | `.ai/AGENTS.md` §13.9-3 |
| S-06 | Sızan anahtar ihlalinde anahtar **iptal edilir**, yalnızca commit silinmez | güvenlik | `.github/AGENTS.md` §3.3-2 |
| S-07 | Yeni secret kuralı (config) eklemek Security onayı ister | onay | `.github/AGENTS.md` §3.1 |

| Secret-Scan Boşluğu | |
|---|---|
| `.gitleaks.toml` özel kural seti | yok → yalnız varsayılan imzalar ⚠️ |
| OSV/Dependabot düzeyi CVE taraması | bilinmiyor → `⚠️ VERIFICATION REQUIRED` (yalnız `composer audit`) |

## §8 BYPASS TESTLERİ

| # | Bypass senaryosu | Beklenen | Kanıt / Vaka |
|---|---|---|---|
| B-01 | Bypass credential'larından biri tanımsız | bypass yok (fail-closed) | `BypassAuthMiddlewareFailClosedTest.php` |
| B-02 | Whitelist dışı rota auth'suz erişilemez | 401/403 | T-18 (P0, plan) ⚠️ |
| B-03 | Bypass aktifse olay loglanır | log kaydı | `BypassAuthMiddleware` logTestBypass |
| B-04 | Prod'da test bypass kapalı | bayrak kapalı | ⚠️ KANIT YOK (runtime değeri okunmadı) |
| B-05 | CSRF bypass route'ları varsayılan boş | tüm state-changing istek 403 | `CsrfMiddlewareTest.php:192-216` |
| B-06 | CORS bilinmeyen origin'e başlık vermez | header yok | `ApiPipelineSecurityTest.php:52-63` |
| B-07 | Origin allowlist boşken prod reddeder | 403 | `OriginCheckMiddlewareTest.php:67-74` |

| Bypass Testi Kuralı | |
|---|---|
| Zorunluluk | Bypass yüzeyi olan her middleware'de **deny-by-default** testi bulunur |
| Kapsam genişletme | `shared/AGENTS.md` Yasak-2 — bypass kapsam değişikliği yalnız ADR ile |
| Mevcut test dosyaları | `BypassAuthMiddlewareTest.php` · `BypassAuthMiddlewareFailClosedTest.php` (2/2) |

## §9 BAĞLAYICI MADDELER (ÖZET)

| Madde | Kural | Kapı |
|---|---|---|
| M-01 | Güvenlik yüzeyi değişikliği = test değişikliği | R-01 |
| M-02 | Negatif vaka yoksa test sayılmaz | R-05 |
| M-03 | Fail-open davranışı güvenlik kodunda yasak | R-06 |
| M-04 | Secret taraması atlanamaz | R-03 |
| M-05 | SAST hata verirse geçmez | R-04 |
| M-06 | Bypass değişikliği bypass testi ile birlikte değişir | R-07 |
| M-07 | Yeni bağımlılık audit'ten geçer | R-08 |
| M-08 | Kanıtsız "güvenli" iddiası yazılmaz | R-10 |

## §10 YASAKLAR

| # | Yasak | Sınıf |
|---|---|---|
| Y-01 | Test yazılmadan güvenlik kodunu merge etmek | kapı |
| Y-02 | Testi çalıştırılmadan `PASS` yazmak | truth |
| Y-03 | Fail-open (koşul belirsizken erişim verme) | güvenlik |
| Y-04 | Secret değerini metin dosyasına yazmak | sızıntı |
| Y-05 | Testleri atlayarak CI'ı yeşil göstermek (`continue-on-error` ile güvenlik job'ı) | kapı |
| Y-06 | Uydurma satır numarası / uydurma snippet | zero-hallucination |
| Y-07 | Negatif testi "çalışan yol" ile değiştirmek | kapsam |
| Y-08 | Genel QA test kapsamını bu çocukta tekrar yazmak | kapsam sınırı (§1) |
| Y-09 | DAST yokken "sızma testi tamamlandı" demek | iddia |
| Y-10 | Bypass testini silmek (kapsam daraltma) | regresyon |

## §11 KATMAN × GATE EŞLEMESİ

| Katman | Kapı | Red koşulu | Sahip |
|---|---|---|---|
| Lokal unit | `composer test` | kırmızı test | QA Engineer |
| Lokal statik | `composer stan` | hata | QA Engineer |
| CI lint | `php-lint` job | hata | DevOps Engineer |
| CI test | `php-test` job | hata | DevOps Engineer |
| CI bağımlılık | `composer-audit` job | CVE | Security Engineer |
| CI secret | `Secret Scan` job | bulgu | Security Engineer |
| Regresyon | ADR-010/013/020 testleri | hata | Security Engineer |
| DAST | — | — | YOK ⚠️ |

## §12 KANIT ENVANTERİ (Dosya · Satır)

| # | Dosya · Satır | Ne gösteriyor |
|---|---|---|
| 1 | `shared/composer.json:23-26` | `require-dev`: phpunit `^10.5`, phpstan `^1.10` |
| 2 | `shared/composer.json:46-49` | scriptler: `test`, `stan` (level 5) |
| 3 | `.github/workflows/ci.yml:62-64` | PHPStan adımı (`composer stan`) |
| 4 | `.github/workflows/ci.yml:104-108` | PHPUnit adımı (matrix: shared · auth `--testsuite Unit`) |
| 5 | `.github/workflows/ci.yml:123-127` | `composer audit` (bağımlılık taraması) |
| 6 | `.github/workflows/ci.yml:14-16` | `permissions: contents: read` (en az yetki) |
| 7 | `.github/workflows/secret-scan.yml:28-31` | gitleaks adımı |
| 8 | `.github/workflows/secret-scan.yml:26` | `fetch-depth: 0` |
| 9 | `shared/tests/` (glob) | 45 dosya / 41 test sınıfı |
| 10 | `security-testing/index.md:44-52` | §2 kapsam özeti (SAST/DAST/WSTG satırları) |

## §13 RED SENARYOLARI

| # | Senaryo | Kapı | Sonuç |
|---|---|---|---|
| S-1 | CSRF davranışı değişti, test değişmedi | R-01/R-04 | RED |
| S-2 | Yeni header eklendi, negatif test yok | R-05 | RED |
| S-3 | `.env` anahtarı commit'lendi | R-03 | RED + iptal |
| S-4 | Paket CVE'si çıktı | R-08 | RED |
| S-5 | Bypass prefix'i genişletildi, test yok | R-07 | RED |
| S-6 | Test yeşil ama çalıştırılmadı | R-02/R-10 | `⚠️` — PASS yazılmaz |
| S-7 | DAST yapılmadan "güvenli" yayını | R-10 | iddia RED |

## §14 UYUM MATRİSİ

| Kural | Kaynak | Durum |
|---|---|---|
| Test coverage ≥ %80 | `.ai/AGENTS.md` §16 (QA) | ⚠️ ölçülmedi |
| CI/CD success ≥ %95 | `.ai/AGENTS.md` §16 (DevOps) | ⚠️ CI geçti mi doğrulanmadı |
| GitLeaks clean | `.ai/AGENTS.md` §16 | job mevcut, sonuç doğrulanmadı ⚠️ |
| OWASP Top 10 | `.ai/AGENTS.md` §16 (Security) | `security-kod-ornekleri.md` kapsamında |
| Subagent commit atmaz | kök `AGENTS.md` §7/§9 | ✅ bu dosyada commit YOK |
| Zero-Hallucination | kök `AGENTS.md` §3 | ✅ her iddia Dosya·Satır veya `⚠️` |

## §15 BİLİNMEYENLER

| # | Soru | Etiket |
|---|---|---|
| U-1 | CI job'ları gerçekten yeşil mi? (çalıştırma kanıtı yok) | ⚠️ VERIFICATION REQUIRED |
| U-2 | Ölçülen test kapsam yüzdesi nedir? | ⚠️ VERIFICATION REQUIRED |
| U-3 | DAST aracı seçimi ve zamanlaması | UNKNOWN |
| U-4 | Ayrık SAST (PHPStan ötesi) aracı | UNKNOWN |
| U-5 | `.gitleaks.toml` özel kural seti eklenecek mi | UNKNOWN |
| U-6 | JS tarafı için güvenlik testi (vitest) kapsamı | UNKNOWN |

## §16 R-KRİTER KANIT HARİTASI

> Her red kriterinin eldeki kanıtı (Dosya·Satır) ve güncel durumu. Kanıtı olmayan
> kriter `⚠️` kalır — kriter varlık değil, kapı anlamına gelir.

| Kriter | Kanıt (Dosya·Satır) | Durum |
|---|---|---|
| R-01 test yazıldı | `shared/tests/Middleware/` 9 dosya · `shared/tests/Security/` 2 dosya (glob) | ✅ envanter var |
| R-02 testler yeşil | test çalıştırma kanıtı **bu oturumda YOK** (shell erişimi yok) | ⚠️ koşu yok |
| R-03 secret tarama temiz | `.github/workflows/secret-scan.yml·24-31` (gitleaks job'ı) | ⚠️ job tanımı var, sonuç yok |
| R-04 PHPStan temiz | `shared/composer.json·46-49` (`stan` script, level 5) · `ci.yml·62-64` | ⚠️ koşu yok |
| R-05 negatif vaka | `CsrfMiddlewareTest.php·85-97` (419) · `OriginCheckMiddlewareTest.php·67-74` (403) · `RateLimiterMiddlewareTest.php·52-74` (429) | ✅ kod kanıtı |
| R-06 fail-closed test | `BypassAuthMiddlewareFailClosedTest.php·27` (sınıf) · `OriginCheckMiddlewareTest.php·67-74` | ⚠️ kısmi — R-06 ile çatışan fail-open testi var (§17 X-1) |
| R-07 bypass testi | `BypassAuthMiddlewareTest.php·10` · `BypassAuthMiddlewareFailClosedTest.php·27` (2/2 dosya) | ✅ kod kanıtı |
| R-08 composer audit | `.github/workflows/ci.yml·123-127` (`composer audit` adımı) | ⚠️ adım var, sonuç yok |
| R-09 sert kapı | `ci.yml·167` · `ci.yml·183` = `continue-on-error: true` **yalnızca soft job'larda** (js-coverage, js-e2e) | ✅ kod kanıtı — güvenlik job'ları soft değil |
| R-10 kanıtlı iddia | `security-testing-ornekler.md` §2-§12 (11 alıntı) · bu §16 (10 satır) | ✅ |

| Harita Kuralı | |
|---|---|
| Durum `✅` | Dosya·Satır diskten okunmuş, içerik iddiayla eşleşiyor |
| Durum `⚠️` | Tanım var, **çalıştırma/sonuç kanıtı yok** — PASS yazılmaz (§4 A-07) |
| Yenileme | Her kod değişikliğinde harita gözden geçirilir; kanıtı eskimeyen kriter = silinir değil, tekrar okunur |

### §16.1 Kriter × Uygulama Anı

| Kriter | Ne zaman uygulanır | Gecikirse |
|---|---|---|
| R-01 / R-05 | iş kaleminde, kodla birlikte (§4 A-05) | review RED |
| R-02 / R-04 | her lokal çalıştırma + her PR | merge RED |
| R-03 | her push + her PR (`secret-scan.yml·7-9`) | push RED |
| R-06 / R-07 | bypass/limit yüzeyi değiştiğinde anında | regresyon kapısı |
| R-08 | yeni bağımlılık eklendiğinde (`ci.yml·123-127`) | paket RED |
| R-09 | her `ci.yml` revizyonunda (§17 X-4) | kapı denetimi |
| R-10 | her doküman/rapor yazımında | satır `⚠️`'a döner |

| Harita Bütünlüğü | |
|---|---|
| Satır sayısı | 10 kriter = 10 satır (§16 başlık tablosu) |
| Kanıtsız kriter | R-02, R-03, R-04, R-08 yalnızca **tanım** kanıtlı — koşu/sonuç kanıtı yok ⚠️ |

## §17 ÇATIŞMA VE İSTİSNALAR (bilinçli açık kayıtlar)

| # | Çatışma / istisna | Kaynak A | Kaynak B | Karar |
|---|---|---|---|---|
| X-1 | Rate limit fail-open ↔ fail-closed | `RateLimiterMiddlewareTest.php·105-128` (cache hatasında istek geçer) | R-06 / M-03 (fail-open yasak) | **AÇIK** — üst karar bekliyor ⚠️ (bkz. `security-testing-dogrulama.md` §4.1) |
| X-2 | SecurityHeaders dedicated testi yok | glob: `shared/tests/**` içinde `SecurityHeadersTest` 0 | §4 A-01 (güvenlik yüzeyi test zorunlu) | **BOŞLUK** ⚠️ — pipeline testi kısmi kanıt (`MiddlewarePipelineTest.php·176-221`) |
| X-3 | DAST aracı yok | `index.md` §2 satır 49 (`SAST/DAST aracı \| yok`) | R-10 ("güvenli" iddiası) | Y-09 uyarınca DAST yokken sızma testi iddiası YAZILMAZ |
| X-4 | Soft kapılar (js-coverage, js-e2e) | `ci.yml·167`, `ci.yml·183` (`continue-on-error: true`) | R-09 (sert kapı) | Bilinçli istisna — yalnız JS job'ları; güvenlik job'ları kapsam dışı |
| X-5 | `.gitleaks.toml` yok | `secret-scan.yml·4` (varsayılan config yorumu) | S-04 | Bilinçli — özel kural seti talebi S-07 onayı ister |
| X-6 | Test kapsam yüzdesi ölçülmedi | `index.md` §2 satır 52 | `.ai/AGENTS.md` §16 (QA ≥ %80) | ⚠️ ölçüm yok — `K019-testing` sahibi |

| İstisna Kuralı | |
|---|---|
| İstisna = açık kayıt | Sessiz istisna yok; her istisna bu tabloda satır taşır |
| Karar üstte | X-1 gibi çatışmalarda subagent **karar vermez**, `⚠️` + üst karar |
| Tekrar gözden geçirme | Kod değişince tablo yeniden okunur; kapanan satır silinmez, durum güncellenir |

## §18 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[security-test-stratejisi]] | öncesi | T-01..T-20 vakaları — bu dosya tekrarlamaz |
| [[security-testing/index]] | üst | bu çocuğun künyesi ve §2 kapsam özeti |
| [[security-master]] | üst | K009 domain künyesi |
| [[security-kapsam]] | kardeş | domain geneli kapsam |
| [[security-dogrulama]] | kardeş | domain geneli kontrol satırları |
| [[security-riskler]] | öncesi | kapı kriterleri risklerden türetilir |
| [[security-web-arastirma]] | kaynak | OWASP/WSTG URL havuzu (tekrar yasağı) |
| [[session-security/session-security-kapsam]] | hedef | oturum testleri bu kapıdan geçer |
| [[authentication/authentication-dogrulama]] | hedef | auth kontrol satırları AD-01..AD-38 |
| [[secrets/secrets-kapsam]] | hedef | secret-scan kapsamı |
| [[rate-limiting/rate-limiting-kapsam]] | hedef | 429 kapı testleri |
| `K18-testing` | dışı | genel QA test kapsamı |
| `K17-devops` | sonrası | CI job sahipliği |

## §19 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — test kapısı + red kriterleri + 10 kanıt satırı | Vault Steward (subagent) |
| 2026-10-15 | `git commit` ATILMADI (yasak) | — |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
