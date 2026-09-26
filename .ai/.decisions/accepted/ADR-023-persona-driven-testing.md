---
title: "CoreMusic — ADR-023: Persona-Driven Testing (20 Persona Test Matrisi · %90 Satır + Şart/Branş Coverage · Kritik Yollar %100 Branş · PR'da Coverage Gate)"
type: adr
category: testing
date: 2026-09-25
updated: 2026-09-26
version: 1.1.0
status: accepted
authority: ADR-023 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-023: Persona-Driven Testing (20 Persona Test Matrisi · %90 Satır + Şart/Branş Coverage · Kritik Yollar %100 Branş · PR'da Coverage Gate)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-023'ü sıfırdan yaz"; karar içeriği kullanıcı onaylı: **(a) 20 persona test matrisi** (her persona için birim + entegrasyon senaryosu, happy/error/boundary) · **(b) coverage %90 satır + şart (branch)** · **(c) kritik yollar (auth/payment/ADR-020 API) %100 branş** · **(d) PR'da coverage gate — CI'da düşerse merge yok** · **(e) debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅ · Arch Lead: ⏳**)
**İlgili ADR'ler:** [[ADR-002-pdo-mandatory-no-orm]] (PDO/ORM yasağı — testler de raw sorgu yazamaz; dosya diskte VAR ✅) · [[ADR-004-multi-domain-spa]] (çoklu domain SPA — 20 persona matrisi bu domain yapısına göre yazılır; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı — coverage/kişilik her iddia etiketli; dosya diskte VAR ✅) · [[ADR-017-dsp-hardware-mode]] (`.github/workflows/` = 0 bulgusunun kaynağı — bu ADR §1.1-B'de güncelleniyor; dosya diskte VAR ✅) · [[ADR-020-api-public-security]] (API güvenlik seti — "kritik yol %100 branş" kapsamındadır; dosya diskte VAR ✅) · [[ADR-021-spa-router-immutable-contract]] (PageRouter 4 dosya/21 test bulgusu — §1.1-A'da TEYİT edildi; dosya diskte VAR ✅) · [[ADR-022-database-hardened-security]] (DB güvenliği — audit/PII testleri bu matrisin kritik yoludur; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 60** `[[ADR-023-persona-driven-testing]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in test altyapısı **var ama ölçüsüz**: PHPUnit kurulu, 32 test dosyası / ~243 test metodu diskte, CI'da test koşuluyor — ama **hiçbir coverage ölçümü, hiçbir coverage eşiği ve hiçbir "kim için test yazılıyor" tanımı yok**. Testler funcsiyonel/proje bazlı yazılmış; kullanıcı profillerine (persona) bağlanmamış. Bu ADR **kod üretmez** — test stratejisini sözleşmeye çevirir: **20 kişilik persona matrisi** (kimin senaryosu ne), **ölçülebilir coverage eşiği** (%90 satır + şart; kritik yolda %100 branş) ve **CI'da bağlayıcı kalite kapısı** (PR'da düşerse merge yok). Eski vault'ta 68 kişilik hazır persona sistemi duruyor (§1.1-C) — bu ADR onları **özetler ve test matrisinin temeli yapar**, eski klasöre dosya kopyalamaz (AIU: disk olduğu gibi korunur).

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) TEST ENVANTERİ — IMPLEMENTED (ama boşluklu):**

- **`composer.json` ×3 PHPUnit `^10.5`:** `shared/composer.json:23` (ayrıca `phpstan/phpstan ^1.10` `:24`; scriptler `test: phpunit` `:46`, `stan: phpstan analyse src --level=5` `:47`), `auth.coremusic.net/composer.json:26`, `home.coremusic.net/composer.json:22`.
- **`phpunit.xml` ×2:**
  - `shared/phpunit.xml` → testsuites **Unit, Api, Events, Security, Middleware** (`:10-25`); `<source>` include `src` (`:26-30`); **coverage raporu YOK** (`<coverage>` bloğu yok → `%` çıktısı üretilmiyor).
  - `auth.coremusic.net/phpunit.xml` → testsuites **Unit, Integration**; `<coverage><report><text outputFile="php://stdout"/></report></coverage>` **VAR** (rapor ayarı mevcut → **PLANNED: CI'da hiç çağrılmıyor**); `failOnRisky` + `failOnWarning` **true** (`:8-9`).
- **Test dosyaları:** `shared/tests/` = **22 PHP dosyası** (Api 3 · Events 2 · Middleware 5 · OAuth 1 · Security 2 · Unit 9), `auth.coremusic.net/tests/` = **10 PHP dosyası** (bootstrap + Domain 4 + Unit 5) → **toplam 32 PHP test dosyası, ~243 test metodu** (grep sayımı: `function test*` **212** + `#[Test]` **31**).
- **PageRouter — ADR-021 bulgusu TEYİT:** `shared/tests/Unit/PageRouter/` = **4 dosya / 21 test** (AuthGuardTest 8 · AuthUrlBuilderTest 6 · RouteRegistryTest 5 · SpaRouteTest 2).
- **testsuite BOŞLUKLARI (1. bulgu):** `shared/tests/OAuth/OAuthManagerTest.php` (**8 test**) hiçbir testsuite'te değil → `vendor/bin/phpunit` (ci.yml `:110` argümansız çağırıyor) bu testi **koşmuyor**. `auth.coremusic.net/tests/Domain/**` (**4 dosya / 20 test**: LoginRequest 4 · User 2 · Email 8 · Password 6) de hiçbir testsuite'te değil (auth testsuite = Unit + Integration; `tests/Integration/` **diskte YOK** — ci.yml `:84-85` yorumu bunu zaten yazıyor) → **28 test metodu CI'da görünmüyor** (243 − 215 = 28).
- **Kopya test şüphesi (2. bulgu):** `tests/Domain/**` ile `tests/Unit/Domain/**` aynı 4 testi farklı isimlendirmeyle taşıyor (MD5 farklı, içerik benzer — örn. `EmailTest` 8/8 method, `testCreateValidEmail` vs `create_with_valid_email`); `Unit` tarafı `#[Test]` attribute kullanıyor (31 adet) — **⚠️ VERIFICATION REQUIRED: iki takımın assert kapsamı birebir karşılaştırılmadı.**
- **JS/E2E test = 0:** `*.test.js` / `*.spec.js` / `*.test.ts` (vendor/node_modules dışı) → **0 dosya**; Vitest/Playwright test dosyası yok (Playwright yalnız dependency — `.github/workflows/ci.yml:9` yorumu) → **PLANNED.**
- **`home.coremusic.net`:** `composer.json` autoload'unda `"CoreMusic\\Home\\Test\\": "tests/"` tanımlı ama **`tests/` dizini diskte YOK** (dizin: `config, include, pages`) → **0 test, PLANNED.**

**B) CI / COVERAGE — CI IMPLEMENTED, GATE PLANNED:**

- **`.github/workflows/` = 2 dosya** (ADR-017 bulgusu `.github/workflows/ = 0` **artık geçersiz** — 2026-09-24'te eklendi, bu ADR §1.1'de düzeltiyor):
  - `ci.yml` (2026-09-24): `php-lint` (PHPStan level 5, yalnız `shared`) → `php-test` matrix (`shared` tam, `auth.coremusic.net --testsuite Unit`) → `composer-audit` (3 proje).
  - `secret-scan.yml` (2026-09-24): GitLeaks (`fetch-depth: 0`).
- **Coverage gate YOK (3. bulgu):** `ci.yml` içinde `--coverage-*` **0**, coverage raporu/adımı **0**, coverage sürücüsü (xdebug/pcov) kurulmuyor, eşik yok, **PR merge kuralı yok** → "PR'da gate" bugün **PLANNED**.
- **Coverage değeri: bilinmiyor** → `⚠️ VERIFICATION REQUIRED` (hiçbir yerde coverage % raporu yok; ölçüm ilk adım §5.1/1).
- **Vault standardı bugün ≥%80:** `[[../../AGENTS.md]]` §16 (QA: "Test coverage ≥80%, flaky test %0") + §10.1 eskalasyon ("Test coverage %80 altı → L1 QA") → bu ADR %90'a **sıkılaştırır** (§1.4 kısıt 9).

**C) PERSONA ENVANTERİ — eski vault (diskde okundu, BU ADR'ye ÖZETLENDİ):**

- **Kaynak dizin (kullanıcı onaylı okuma):** `C:\Users\MARHAN\Desktop\Yeni klasör (6)\coremusic.net.old\.ai\personas` → **80 markdown dosyası** (68 persona + 4 kök doküman + 6 test senaryosu + 2 rapor şablonu).
- **68 persona / 6 grup** (`index.md`, total: 68): **kız çocuk 17** (4-11) · **genç kız 17** (12-17) · **erkek çocuk 12** (4-11) · **genç erkek 12** (12-17) · **yetişkin kadın 5** (25-45) · **yetişkin erkek 5** (25-45); her persona: rol kartı + Big Five + browser test senaryosu (index.md §Grup Dağılımı).
- **Kök dokümanlar:** `index.md`, `methodology.md`, `mood-taxonomy.md`, `test-scenarios-mapping.md`.
- **6 test senaryosu:** `a11y-erisilebilirlik.md` · `arabesk-dans-mood-gecis.md` · `browser-navigasyon.md` · `muzik-kesfi.md` · `playlist-olusturma.md` · `sosyal-paylasim.md`.
- **2 rapor şablonu:** `rapor-sablonlari/excel-sablon.md`, `rapor-sablonlari/test-rapor-sablonu.md`.
- **Bu vault'ta personas YOK:** `Test-Path .ai/.personas` → **false**, `Test-Path .ai/personas` → **false** → persona envanteri yalnız eski vaultta; kopya yapılmadı (yalnız özet — AIU).
- **Kullanıcının andığı 8 kişilik (2026-09-25, karar girdisi):** **senior · junior · uzman · normal son kullanıcı · profesyonel kullanıcı · ev kullanıcısı · studio kullanıcısı · yazılım kullanıcısı.**

**Sonuç etiketi:** **IMPLEMENTED:** 32 test dosyası / ~243 test metodu, PHPUnit `^10.5` ×3, `phpunit.xml` ×2, CI'da lint+test+audit koşusu (2 workflow), PageRouter 4/21 test, auth `failOnRisky/failOnWarning`. **PLANNED:** coverage ölçümü + eşiği, PR merge gate, testsuite boşluklarının kapatılması (28 test), JS/E2E (0 dosya), `home.coremusic.net` testleri (0), persona matrisinin testlere bağlanması. `⚠️ VERIFICATION REQUIRED`: coverage % değeri (0 rapor), Domain/Unit kopya setinin assert eşitliği, payment testinin hiç var olmaması (payment kodu/kapsamı ADR-023'e dahil değil — ayrı karar).

### 1.2 Sorun Tanımı

1. **"Kim için test yazıyoruz?" sorusu cevapsız:** 243 test fonksiyonel; hiçbiri bir persona/role bağlanmamış → erişilebilirlik, çocuk-güvenlik, studio/ev senaryoları test edilmiyor (eski vault'taki 6 test senaryosu kod'a hiç taşınmamış: `*.test.php` içinde `a11y|playlist|browser` grep'i **0**).
2. **Ölçüm yok:** coverage raporu **0** → "%80 standardı" (AGENTS.md §16) bile **denetlenemiyor**; kağıt üstünde kural, kapı yok.
3. **Kapı yok:** `ci.yml` testi koşuyor ama **eşik/merge kuralı yok** → düşen coverage bile merge edilir.
4. **Görünmeyen testler:** 28 test metodu (OAuth 8 + auth Domain 20) hiçbir testsuite'te değil → **yanlış güven** (CI yeşil, testler koşmuyor).
5. **Kritik yol kapsamı belirsiz:** auth (`auth.coremusic.net/include/**`), API (`shared/src/Api/**`), middleware (`shared/src/Middleware/**`) için branş kapsamı **bilinmiyor**; ADR-020/022'nin güvenlik kapıları test edilmiş ama **%100 branş taahhüdü yok**.
6. **JS/E2E sıfır:** SPA router (ADR-021) + 45-tier cihaz matrisi için tek bir browser testi yok → persona senaryolarının çoğu (browser navigasyon, a11y) **kodlanamaz durumda**.
7. **Kişilik şişirme riski:** 68 persona + 8 rol = 76 aday; hepsine test yazmak imkânsız → **20 ile sınırlandırma** gerekli (aksi halde matris kendini bakıma boğar).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (docs.phpunit.de, phpunit.de, docs.github.com, infection.github.io, owasp benzeri otorite), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) persona-driven testing 2025-26, (b) coverage best practices — line vs branch, kritik kod %100, (c) mutation testing, (d) PHPUnit modern patterns, (e) CI quality gate / PR merge kuralı, (f) flaky test stabilizasyonu.** Erişim: **6 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "persona-driven testing 2025 user personas test scenario matrix best practices" · (2) "code coverage best practices 2025 line vs branch coverage 100% critical code mutation testing" · (3) "CI quality gate coverage threshold pull request block merge 2025 GitHub Actions PHPUnit coverage" · (4) "PHPUnit 11 12 modern testing patterns attributes data providers 2025 deprecations" · (5) "Infection mutation testing PHP PHPUnit 2025 mutation score threshold best practice" · (6) "flaky test rate 2025 CI quarantine retry stabilization best practices percentage" |
| Web Search **Konusu** | (1) Persona'ya bağlı test senaryosu üretimi, persona → user story eşleme, persona sayısı optimizasyonu (3-5 ile başla, %80 trafiği kapsa); (2) satır (statement) vs branş (branch) coverage farkı, %100 satırın yol/blok hatalarını kaçırdığı, kritik/yüksek risk kodda ~%100 hedefi, mutation testing'in "test kalitesi" ölçümü olması; (3) GitHub PR'da coverage eşikli merge block, GitHub Actions'ta quality gate, coverage delta/patch coverage talebi; (4) PHPUnit 10/11/12: static data provider zorunluluğu, `#[Test]`/`#[DataProvider]`/`#[TestWith]` attribute'ları, 12'de kaldırılan/deprikatelenen API'ler; (5) Infection (PHP) MSI (Mutation Score Indicator), `--min-msi` eşiği, CI'da kademeli eşik, yalnız değişen kodda çalıştırma; (6) flaky test oranı, retry (2), quarantine, ilk-failure+retry kaydı, izleme/raporlama. |
| Web Search **Bağlam** | **~45 adlandırılmış kaynak / 6 sorgu**: testscenario + testriq + testmuai + testrigor + applause + sciencedirect + arXiv (81 makalelik scoping review) + reddit UXResearch (8) · testlio + graphite + launchdarkly + codacy + qt.io + harness + browserstack + reddit ExperiencedDevs (8) · **docs.github.com (PR threshold + coverage threshold)** + graphite + stackoverflow + github community #194833 + medium (Melnychuk) + reddit r/devops + sonar community (7) · **docs.phpunit.de 12.5 attributes** + **phpunit.de announcements (11/12 tarihleri)** + cspray + drupal.org + wordpress trac #62004 + symfony bridge + github issue #6279 (7) · **infection.github.io (CLI)** + dev.to (Rubio) + stanza + tsh.io + medium (Rafalko) + pestphp docs + reddit r/PHP + github infection #2418 (8) · testdino benchmark 2026 + testgrid + testrail + datadog + slack.engineering + maestro + minware + getautonoma (7) |
| Web Search **Kısa Açıklama** | **(1) Persona:** testRigor/testScenario "persona = gerçek kullanıcı davranışı + hedef + engel", testriq "**3-5 persona ile başla, trafiğin %80'ini kapsar**", Applause "persona attributes → key test scenarios", testmuai "high-impact persona'ları öncelikle" → **persona sayısı bilinçli sınırlanır (şişirme riski).** **(2) Coverage:** Qt "100% statement coverage yine de exception/branch yollarını kaçırabilir", LaunchDarkly "90%+ iyidir; **yüksek kritiklikte ~%100** istenir", Harness "line = baseline+gate, **mutation = test kalitesi**" → **satır + branş birlikte ölçülür; kritik kodda hedef ~%100 branş.** **(3) Gate:** GitHub Docs "PR'ları coverage eşiğinin altında **block edebilirsin**" + Graphite "GitHub Actions'ta quality gate" → **PR'da eşik + merge block mümkün ve standart.** **(4) PHPUnit:** data provider'lar **static** olmalı (PHPUnit 10+), attribute tabanlı API (`#[Test]`, `#[DataProvider]`, `#[TestWith]`) modern standart, PHPUnit 12 (Şubat 2025) `RunClassInSeparateProcess` gibi API'leri kaldırdı → **attribute'lı yazım + static provider şart.** **(5) Mutation:** Infection `--min-msi` CI'da kullanılır; topluluk "değişen kodda çalıştır + ~%65-70 eşikle başla" (reddit, stanza **`--min-msi=70`**) → **mutation gate kademeli.** **(6) Flaky:** retry (2) + quarantine + ilk hata/retry kaydı (testrail, maestro, minware); izleme yapan ekiplerde **%25 daha az flaky rerun** (testdino/Bitrise) → **flaky = gate'in en büyük sahtelik kaynağı.** |
| Web Search **Uzun Açıklama** | **(a) Persona-driven testing (kaynak 1-8):** testscenario persona test adımlarını (persona → user story → senaryo → test verisi → öncelik) veriyor; testriq açıkça "begin with three to five personas that represent 80% of your traffic" diyor ve persona şişirmeyi en yaygın hata sayıyor; testrigor "multitasking professional" örneğiyle persona başına senaryo yazımını; Applause persona attribute'larının (davranış, hedef, engel) test senaryosuna birebir çevrildiğini; arXiv 2504.04927 (81 makale) sentetik/persona değerlendirme uyarıları (insan denetimi, şablon/test edilebilirlik) veriyor; sciencedirect + reddit UXResearch persona'nın araştırma/test kalitesine etkisini tartışıyor. Çapraz: testriq + testscenario + testrigor + applause aynı hükmü veriyor (**sınırlı, senaryoya bağlı persona**). **(b) Coverage (kaynak 9-16):** Qt iki coverage tipini ayırıyor (statement satırı, branch koşulu/exception yolu) ve "100% statement = hâlâ test edilmemiş karar yolları" uyarısını yapıyor; LaunchDarkly endüstri benchmark'ında "90%+ iyi, kritik sistemlerde ~%100" ve "yeni satırlarda ~%100 daha gerçekçi" diyor; Graphite/Codacy/BrowserStack line-branch ayrımını, Harness ise mutation'ı "critical code test kalitesi" aracı olarak tablolaştırıyor; reddit ExperiencedDevs "line coverage ≠ branch coverage" pratik uyarısı. Çapraz: Qt + LaunchDarkly + Harness + BrowserStack aynı. **(c) CI gate (kaynak 17-24):** GitHub Docs iki ayrı how-to yayımlamış — PR quality threshold ve **"Setting a code coverage threshold → block pull requests that fall below"**; Graphite GitHub Actions'ta quality gate'leri enforce etme rehberi; stackoverflow + github community #194833 "PR'ın değişen satırları coverage'ı (patch coverage)" talebini, medium (Melnychuk) Actions'ta eşik aşımında job'ı fail etmeyi anlatıyor; reddit r/devops "coverage delta izle ama sabit eşiği zorla" karşıt görüşü → **eşik + block mümkün, ama sabit eşik tartışmalı (§4.3 risk 3).** **(d) PHPUnit (kaynak 25-31):** docs.phpunit.de 12.5 attribute sayfası `#[DataProvider]`, `#[TestWith]`, `#[TestWithJson]`, `#[RunTestsInSeparateProcesses]` vb. veriyor ve `RunClassInSeparateProcess`'in 12.4'te hard-deprecated olduğunu; phpunit.de duyuruları PHPUnit 11'in (Şubat 2024) ve 12'nin (7 Şubat 2025) takvimini; cspray "static data provider'a geçiş"i ve drupal/wordpress upgrade biletleri "PHPUnit 12'de kaldırılanlar" listesini doğruluyor → **repo `^10.5` kullanıyor (AGENTS.md §25.2), attribute'lu testler zaten var (31 `#[Test]`) → uyumlu ama 12'ye geçiş planlanmalı.** **(e) Mutation (kaynak 32-39):** Infection CLI `--min-msi` "CI'da test kalitesini otomatik kontrol eder" diyor; dev.to (Rubio) MSI tanımını (öldürülen mutant/üretilen), stanza "CI'da `--min-msi=70` ile başla, kademeli artır", tsh.io pratik PHP tutorial'ı, medium (Rafalko — Infection yazarı) MSI örneği, pestphp `--min=40/80` eşikleri, reddit r/PHP "yalnız changed sources + ~%65 relaxed" pragmatiği → **mutation gate kademeli ve dar kapsamlı.** **(f) Flaky (kaynak 40-47):** testdino 2026 benchmark (flakiness **yükseliyor**: 2025'te ekiplerin %26'sı — **⚠️ tek kaynak, sayfa-içi doğrulanmadı**), testgrid/maestro retry=2, minware quarantine (kararsız testi CI'dan çıkarıp izole etme), testrail "ilk hata + retry sonucunu birlikte kaydet (yoksa pass rate iyileşir ama istikrarsızlık gizlenir)", datadog "%70+ flaky test ilk eklendiğinde flaky'tir", slack.engineering otomatik suppression ile ana dal istikrarı, getautonoma "%95'te geçen testler" → **retry + quarantine + raporlama üçlüsü standart.** |
| Web Search **Paragraf Veri Uzun** | Persona-driven testing 2025-26: persona → senaryo eşlemesi + **3-5 persona ile başla (trafiğin %80'i)**, persona şişirmeyi en yaygın hata say (testriq, testscenario, testrigor, applause); sentetik persona'larda insan denetimi/şablon testi şart (arXiv 2504.04927, sciencedirect). Coverage: statement≠branch, "100% statement yine de branch/exception kaçırır" (Qt); "90%+ iyi, kritik kod ~%100" (LaunchDarkly); gate için line+branch, kalite için mutation (Harness, Codacy, BrowserStack, Graphite, reddit). PR kapısı: GitHub Docs coverage eşikli PR block + Actions quality gate (docs.github.com, Graphite, stackoverflow, medium, github community #194833); patch/changed-code coverage talebi açık (artık istek). PHPUnit: static data provider + `#[Test]`/`#[DataProvider]`/`#[TestWith]` modern standart (docs.phpunit.de 12.5, phpunit.de 11/12 duyuruları, cspray, drupal, wordpress #62004); repo `^10.5` → attribute'larla uyumlu. Mutation: Infection `--min-msi` CI standardı; kademeli %65-70 → %80 (infection.github.io, stanza, tsh.io, dev.to, medium/Rafalko, pestphp, reddit). Flaky: retry 2 + quarantine + ilk hata/retry kaydı + izleme (testrail, maestro, minware, datadog, slack.engineering, testgrid, testdino). |
| Web Search **Sonucu** | 1) **Persona-driven testing doğrulandı** (kaynak 1-8, ≥2 çapraz: testriq + testscenario + testrigor + applause): persona bazlı senaryo standart, **ama sayı 3-5'e yakın tutulmalı** → bu ADR 68+8 adayı **20 persona**da sınırlar (§2.2a), **persona şişirme riski §4.3/1.** 2) **Coverage ikilisi (satır+branş) doğrulandı** (kaynak 9-16, ≥2 çapraz: Qt + LaunchDarkly + Harness + BrowserStack): "100% satır yetmez" + "kritik kod ~%100" → **karar: %90 satır + şart (branş), kritik yollar %100 branş (§2.2b).** 3) **PR coverage gate uygulanabilir doğrulandı** (kaynak 17-24, ≥2 çapraz: docs.github.com + Graphite + stackoverflow + github community): GitHub eşikli PR block + Actions gate → **karar: PR'da eşik düşerse merge yok (§2.2d); sabit eşik tartışması §4.3/3.** 4) **PHPUnit modern pattern doğrulandı** (kaynak 25-31, ≥2 çapraz: docs.phpunit.de + phpunit.de + drupal/wordpress): attribute + static provider; repo `^10.5` ve 31 `#[Test]` ile uyumlu → **yeni testler attribute ile yazılır, `^12` geçişi ayrı adım (§5.1/9).** 5) **Mutation testing doğrulandı** (kaynak 32-39, ≥2 çapraz: infection.github.io + stanza + tsh.io + dev.to): `--min-msi` kademeli → **karar: kritik dizinlerde Infection, %65'ten başlayıp %80'e (§5.1/7).** 6) **Flaky stabilizasyonu doğrulandı** (kaynak 40-47, ≥2 çapraz: testrail + maestro + minware + datadog): retry 2 + quarantine + raporlama → **kapıyı flaky'e karşı korur (§2.2e, §4.3/2).** **Toplam ~45 adlandırılmış kaynak, 6 sorgu**; iki çıkarım açıkça işaretlendi: **⚠️** tek kaynaklık flaky oranı rakamı (testdino) ve sayfa-içi derin tur yapılmadığı için eşik sayımları başlık/özet düzeyindedir. |
| Web Search **Alınan Karar** | **ADR-023 KABUL EDİLİR — PERSONA-DRIVEN TESTING (4 madde):** **(a) 20 persona test matrisi:** 8 kullanıcı rolü (senior, junior, uzman, normal son kullanıcı, profesyonel kullanıcı, ev kullanıcısı, studio kullanıcısı, yazılım kullanıcısı) + 6 eski vault demografik grup + 6 eski vault test senaryosu kişisi = 20 satır; **her satır için 1 birim + 1 entegrasyon senaryosu, üç yol zorunlu: happy + error + boundary**; yeni persona eklemek = yeni ADR (§4.3/1). **(b) Coverage:** **%90 satır + şart (branch)** genelde; **kritik yollar** (`auth.coremusic.net/include/**`, `shared/src/Api/**`, `shared/src/Middleware/**` = auth/payment/ADR-020 API yüzeyi) **%100 branş** — ödeme testi kapsamı bugün yok → ayrı karar (`⚠️ VERIFICATION REQUIRED`). **(c) Mutation:** kritik dizinlerde Infection `--min-msi` %65 → %80 kademeli. **(d) PR'da kalite kapısı:** `ci.yml`'e coverage raporu + eşik; **eşik altındaysa PR merge EDİLMEZ** (GitHub Docs + Graphite); gate **kademeli devreye girer** (baseline ölç → %70 → %80 → %90) ki ilk gün pipeline kırılmasın; flaky için retry 2 + quarantine. **PHPUnit yeni testler `#[Test]`/`#[DataProvider]` attribute ile; `^12` geçişi ayrı adım.** |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: persona-driven testing (8 kaynak), coverage line/branch + kritik %100 (8), CI/PR quality gate (7), PHPUnit modern patterns (7), mutation testing (8), flaky stabilizasyon (8) → **~45 adlandırılmış kaynak, 6 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada da karşılanır, iki çıkarım (`⚠️` flaky oran rakamı; eşik sayımları sayfa-içi değil) açıkça işaretlendi. **Kod tarafı aynı resmi verdi:** testler **IMPLEMENTED** (32 dosya/243 metot, CI test koşuyor), **coverage ölçümü/PR gate/testsuite boşlukları PLANNED** → bu ADR **sözleşme, kod taahhüdü değil**; uygulaması §5.1 adımlarına bağlıdır. **Kaynak listesi (40):** 1) testscenario.com — What is Persona Testing · 2) testriq.com — Persona-Based Testing (3-5 persona %80 kuralı) · 3) testmuai.com — Persona Testing hub · 4) testrigor.com — Persona Testing examples · 5) applause.com — Role of Personas in User-Centric Testing · 6) sciencedirect.com — User personas, ideation and LLMs · 7) arxiv.org/2504.04927 — GenAI Personas scoping review (81 makale) · 8) reddit r/UXResearch — Synthetic personas · 9) qt.io — 70/80/90/100% coverage yeterli mi · 10) launchdarkly.com — On Code Coverage (benchmarks) · 11) graphite.com — Code coverage best practices · 12) codacy.com — What is Code Coverage · 13) harness.io — Measure, Improve, Scale Quality in CI (mutation tablosu) · 14) browserstack.com — Coverage techniques · 15) testlio.com — Code coverage 6 tip · 16) reddit r/ExperiencedDevs — "line ≠ branch" · 17) **docs.github.com — Setting code quality thresholds for PRs** · 18) **docs.github.com — Setting a code coverage threshold** · 19) graphite.com — Enforce quality gates in GitHub Actions · 20) stackoverflow.com — Restrict PR merge if coverage less · 21) github.com/orgs/community/discussion/194833 — PR coverage preview · 22) medium (Vitalii Melnychuk) — Coverage reports in PRs · 23) reddit r/devops — CI/CD quality gates otomasyonu · 24) community.sonarsource.com — Quality gates for changed code · 25) **docs.phpunit.de/en/12.5/attributes.html** · 26) **phpunit.de/announcements/phpunit-11** (PHPUnit 12: 7 Şub 2025) · 27) cspray.io — Thoughts on PHPUnit 11 (static data provider) · 28) drupal.org — Modernize tests for PHPUnit 12 · 29) core.trac.wordpress.org #62004 — PHPUnit 10/11/12 test güncellemesi · 30) symfony.com — PHPUnit Bridge · 31) github.com/sebastianbergmann/phpunit #6279 — data provider deprecation · 32) **infection.github.io — Command Line Options (`--min-msi`)** · 33) dev.to (rubenrubiob) — Mutation testing with Infection (MSI) · 34) stanza.dev — Infection CI `--min-msi=70` · 35) tsh.io — Mutation testing in a PHP application · 36) medium (Maks Rafalkk) — Infection Mutation Testing Framework · 37) pestphp.com — Mutation testing thresholds · 38) reddit r/PHP — Mutation testing with Infection (changed sources, ~%65) · 39) github.com/infection/infection #2418 — lokal vs playground farkı · 40) testdino.com — Flaky Test Benchmark 2026 (**⚠️ tek kaynak**) · (41) testgrid.io · 42) testrail.com · 43) datadoghq.com · 44) slack.engineering · 45) maestro.dev · 46) minware.com · 47) getautonoma.com — flaky retry/quarantine pratikleri. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-005-ultrathink-protocol]] | Kod kanıtı olmayan her iddia etiketli: coverage % değeri **0 rapor**, payment testi **0**, Domain/Unit kopya eşitliği **karşılaştırılmadı**, JS/E2E **0 dosya** → `⚠️ VERIFICATION REQUIRED` |
| [[ADR-002-pdo-mandatory-no-orm]] | Testler veriye `DatabaseManager` prepare üzerinden erişir; test fixtures'ta raw query/ORM yasağı geçerli (CI grep kapısı ADR-022 §5.1/1 ile aynı) |
| [[ADR-004-multi-domain-spa]] | Persona matrisi 3 subdomain (home/auth/shared) + SPA router yapısına göre yazılır; `dev.coremusic.net` **eklentidir** (§2.2f düz metin) |
| [[ADR-020-api-public-security]] | **Kritik yol kapsamı:** `shared/src/Api/**` + Gateway yüzeyi %100 branş bu ADR'nin bağlayıcısı; ADR-020 test paketleri (`shared/tests/Api/**`, `Middleware/**`) matrisin "API/uzman" satırlarını besler |
| [[ADR-021-spa-router-immutable-contract]] | PageRouter 4 dosya/21 test (TEYİT §1.1-A) — route sözleşmesi testleri değiştirilemez sözleşmedir; matrisin "senior/yazılımcı" senaryoları buradan devralınır |
| [[ADR-022-database-hardened-security]] | DB audit/PII/GRANT testleri (§5.1 adım 1-6'sı) bu matrisin "güvenlik + uzman" senaryolarına bağlanır; test fixtures'ta `[REDACTED]` |
| [[../../AGENTS.md]] §16 / §10.1 | Bugünkü vault standardı **≥%80 coverage** + "%80 altı → L1 QA eskalasyonu" → bu ADR **%90 + %100 branş (kritik)** ile sıkılaştırır; çelişkide bu ADR geçerli, AGENTS.md güncellemesi §5.1/8'de zorunlu |
| Domain boundary (`shared/AGENTS.md` §5) | `tests/**/*.php` → **QA Engineer**; `src/**` testleri yazan kişi kodu değiştirmez (A2/A1 sahipliği korunur) |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) |
| In-Place Refactoring | Dosya adları (`phpunit.xml`, `ci.yml`, `shared/tests/**`, `.ai/personas` — eski vault —) **onaysız değiştirilemez/taşınmaz**; bu ADR yalnız karar yazar (eski persona dosyaları DISKTE KALIR) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (bayt-seviyesi) |
| REDACTED | Test fixture'larında gerçek DB/API/şifre anahtarı yazılmaz; `.env` değerleri `[REDACTED]` |
| Numara kuralı | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-023` `.ai/.decisions/index.md:60`'da **rezerve boş slottur** (doldurma — ADR-019/020/021/022 aynı istisnayı kaydetmişti) |

---

## 2. Karar (Decision)

**CoreMusic test stratejisi DÖRT maddeyle bağlayıcı ilan edilir: (a) 20 persona test matrisi — 8 kullanıcı rolü + 6 eski vault demografik grup + 6 eski vault test senaryosu kişisi; her persona için 1 birim + 1 entegrasyon senaryosu ve üç yol zorunlu (happy / error / boundary); yeni persona = yeni ADR. (b) Coverage eşiği — genelde %90 satır + şart (branch); kritik yollar (`auth.coremusic.net/include/**`, `shared/src/Api/**`, `shared/src/Middleware/**` = auth / payment-surface / ADR-020 API) %100 branş. (c) PR'da kalite kapısı — `ci.yml` coverage raporu + eşik çalıştırır; eşik altındaysa PR merge EDİLMEZ; gate kademeli devreye girer (baseline → %70 → %80 → %90) ve flaky'e karşı retry(2) + quarantine ile korunur. (d) Mutation doğrulama — kritik dizinlerde Infection `--min-msi` %65'ten %80'e kademeli; yeni testler PHPUnit attribute (`#[Test]`, `#[DataProvider]`) ile yazılır.**

### 2.1 Neden Bu Seçenek?

- **Ölçülmeyen kural korumaz:** AGENTS.md ≥%80 kuralı 0 coverage raporuyla yaşıyor (§1.1-B) → eşik + gate olmadan hiçbir coverage hedefi denetlenebilir değil; GitHub Docs eşikli PR block'u doğrudan destekliyor (§1.3 kaynak 17-18).
- **Satır tek başına yanıltır:** "100% statement coverage" hâlâ branch/exception hatalarını kaçırır (Qt, LaunchDarkly — §1.3 kaynak 9-10) → **satır + şart (branch)** birlikte; kritik kodda ~%100 branş hedefi literatürle birebir.
- **Persona = önceliklendirme:** 68+8 persona adayı içinde hepsine test yazılamaz; 3-5 persona ile %80 trafik kuralı (testriq) → **20 satır** hem tartışmayı (3 tur / 20 persona) hem test matrisini besler, şişirmeyi engeller.
- **28 testin görünmezliği acil:** OAuth 8 + auth Domain 20 testi CI'da koşmuyor (§1.1-A) → bu ADR'siz "yeşil CI" yanıltıcı; gate önce bu boşluğu kapatır.
- **Mutation = testin testi:** coverage'ı şişirmek kolay, mutant öldürmek zor (Harness, Infection — §1.3 kaynak 13, 32) → coverage gate'ini mutation ile dengelemek coverage oyununu kırar (§4.3/3).
- **Flaky kapıya zarar:** retry/quarantine olmadan sabit eşik, flaky test yüzünden sürekli merge bloğu yaratır (testrail, minware — §1.3 kaynak 42, 46) → gate tasarımı flaky'i içerir.
- **Yazılımcı personası = yeni domain:** `dev.coremusic.net` (2026-09-25 kullanıcı duyurusu) için test kişisi "yazılımcı" matriste hazır (§2.2f) — kararı ADR-082'ye bırakıyoruz, bu ADR yalnız etkiyi yazar.

### 2.2 Teknik Detaylar

**a) 20 Persona Test Matrisi (bağlayıcı — her satır 1 birim + 1 entegrasyon, üç yol: H/E/B):**

| # | Persona | Kaynak | Birim senaryosu (H/E/B) | Entegrasyon senaryosu (H/E/B) |
|---|---------|--------|--------------------------|-------------------------------|
| 1 | **Senior geliştirici** | kullanıcı listesi | API DTO doğrulama + versiyon yükseltme (`Api/Dto`, `Versioning`) | Gateway → BFF → DB akışında hata sözleşmesi (ADR-020 hata paketi) |
| 2 | **Junior geliştirici** | kullanıcı listesi | Hatalı form girdisi → anlamlı 422 mesajı | Kayıt → e-posta doğrulama → login (yanlış şifre: error, sınır: boundary) |
| 3 | **Uzman (domain uzmanı)** | kullanıcı listesi | Domain event tetikleme + sıralama (`DomainEvent`) | Karmaşık sorgu/filtre sınırı (limit offset boundary) |
| 4 | **Normal son kullanıcı** | kullanıcı listesi | Şifre değiştirme (eski yanlış → error) | Login → oturum → sayfa geçişi (ADR-011/016 ile URL normalizasyonu) |
| 5 | **Profesyonel kullanıcı** | kullanıcı listesi | Toplu playlist işlemleri + yetki kontrolü (`requiredPermission`) | Rate limit altında (ADR-013) ardışık istekler: H (limit içi) / B (limit eşiği) / E (429) |
| 6 | **Ev kullanıcısı** | kullanıcı listesi | Cihaz/tarayıcı algılama (`DeviceDetector`) | Çoklu oturum + cihaz değiştirince session yenileme |
| 7 | **Studio kullanıcısı** | kullanıcı listesi | Uzun oturum + kalıcılık (cache TTL boundary — `SpaRoute.cacheTtl`) | Footer player (ADR-018) + sayfa geçişinde çalma durumunun korunması |
| 8 | **Yazılımcı** | kullanıcı listesi (+ §2.2f) | Router sözleşmesi (`RouteRegistry`/`SpaRoute` — ADR-021) | Public API key + versiyon kabulü/reddi (ADR-020: H `v1` / E geçersiz key / B `v2` sınırı) |
| 9 | **Kız çocuk (4-11)** | eski vault `personas/` (17 persona) | İçerik filtresi + yaş uygunluğu | Ebeveyn kontrolü akışı (aile hesabı: H / E yetkisiz / B yaş sınırı) |
| 10 | **Genç kız (12-17)** | eski vault (17 persona) | Sosyal/gizlilik ayarı kaydetme | Paylaşım izni: H açık / E reddedilmiş / B gizlilik sınırı |
| 11 | **Erkek çocuk (4-11)** | eski vault (12 persona) | Basit arama (kısa sorgu) | Arama sonuç sayfası + filtre (boş sonuç = error, 0 karakter = boundary) |
| 12 | **Genç erkek (12-17)** | eski vault (12 persona) | Gamer/akış tercihi kaydetme | Hızlı navigasyon + geri tuşu (SPA history — ADR-004/021) |
| 13 | **Yetişkin kadın (25-45)** | eski vault (5 persona) | Profil + ödeme formu doğrulama | Ödeme yüzeyi **`⚠️ VERIFICATION REQUIRED`** — ödeme kodu bugün envanterde yok → satır **PLANNED**, ayrı karar |
| 14 | **Yetişkin erkek (25-45)** | eski vault (5 persona) | Abonelik/tercih güncelleme | Çoklu cihazda aynı hesap (session çakışması: H / E çakışma / B eşzamanlı sınır) |
| 15 | **Erişilebilirlik kişisi** | eski vault `test-senaryolari/a11y-erisilebilirlik.md` | Klavye ile form gönderimi (focus/enter) | Ekran okuyucu etiketleri + hata duyuruluşu (WCAG 2.2 AA) |
| 16 | **Mood-geçiş kişisi** | `arabesk-dans-mood-gecis.md` | Mood değişiminde tercih yazma + BPM filtresi | Geçiş sırası (H) / geçersiz mood değeri (E) / eşik BPM (B) |
| 17 | **Tarayıcı navigasyon kişisi** | `browser-navigasyon.md` | History API geri/ileri çözümleme | Yer imi → derin link → 404 fallback (ADR-009/016) |
| 18 | **Müzik keşif kişisi** | `muzik-kesfi.md` | Arama/öneri sorgusu (prepare bind) | Boş/uzun/karakter-sınırı sorgular: B/E |
| 19 | **Playlist kişisi** | `playlist-olusturma.md` | Playlist CRUD (yetki: sahip vs değil) | Aynı anda iki istemciden aynı playlist (B: yarış durumu, E: yetki ihlali) |
| 20 | **Sosyal paylaşım kişisi** | `sosyal-paylasim.md` | Paylaşım metni doğrulama + filtre | Paylaşım → haber akışı yayılımı (H) / gizli hesap (E) / 280 karakter sınırı (B) |

> **Kural:** satır 9-14 (demografik gruplar) **grup temsilcisi** olarak yazılır — her grup için tek senaryo paketi, 68 dosyanın teker teker testlenmesi **DEĞİL** (persona şişirme yasağı, §4.3/1). Satır 15-20 eski vault'un 6 test senaryosundan **türetilmiştir** (dosya adı kanıttır); yeni persona eklemek = **yeni ADR**.

**b) Coverage politikası (bağlayıcı):**

| Kalem | Eşik | Ölçüm | Not |
|-------|------|-------|-----|
| Genel (shared + auth) | **%90 satır** + **%90 şart (branch)** | PHPUnit clover/text raporu | AGENTS.md §16 %80 → **%90'a sıkılaştırma** (§5.1/8) |
| Kritik yollar | **%100 branş** | `auth.coremusic.net/include/**`, `shared/src/Api/**`, `shared/src/Middleware/**` (auth / payment-surface / ADR-020 API) | Payment gerçek kodu yoksa kapsam **yüzeyle** sınırlı (`⚠️ VERIFICATION REQUIRED`) |
| Ölçüm kaynağı | `shared/phpunit.xml` + `auth phpunit.xml` coverage bloğu | `--coverage-clover` CI'da | Bugün **0 rapor** → baseline önce (§5.1/1) |
| Yeni/değişen kod | Eşiğin altına düşürülemez | patch coverage (PR) | §1.3 kaynak 21-22 talebi |

**c) Test paketi → persona eşlemesi:** `shared/tests/Api|Middleware|Security` → persona 1, 5, 8 · `shared/tests/Unit/PageRouter` (4 dosya/21 test) → persona 8, 17 · `auth.coremusic.net/tests/**` → persona 2, 4, 9-14 · `tests/OAuth` + `tests/Domain` → **testsuite'a eklenir** (§5.1/2).

**d) CI kalite kapısı (bağlayıcı — akış):**

```
PR açılır → php-lint (PHPStan L5) → php-test (matrix) → coverage raporu (--coverage-clover)
  → eşik kontrolü: genel ≥ %90 satır+branch VE kritik dizin ≥ %100 branch
      ✅ geçerse → merge açık
      ❌ düşerse → job FAIL → PR merge EDİLMEZ (branch protection / ruleset)
  → flaky koruması: retry(2) + quarantine listesi + failOnRisky (auth'ta zaten true)
  → (kademeli) Infection --min-msi: kritik dizinler, %65 → %80
```

- Eşik **kademeli** bağlanır: baseline (0) → %70 → %80 → %90 (ilk gün pipeline kırılmasın — §4.3/4).
- Gate **yalnız PR'da** koşulur; doğrudan `main` push'u (varsa) aynı kapıdan geçer (opsiyonel, §5.1/6).

**e) PHPUnit yazım standardı:** yeni testler `#[Test]`, `#[DataProvider]`, `#[TestWith]` attribute ile; data provider'lar **static** (§1.3 kaynak 25-27); mevcut `^10.5` korunur, **PHPUnit `^12` geçişi ayrı adım** (§5.1/9); `auth`'daki `failOnRisky/failOnWarning` **shared'a da** yayılır.

**f) `dev.coremusic.net` düz metin notu (yeni mimari — BU ADR'DE KARAR DEĞİL):** Kullanıcı 2026-09-25'te duyurdu: **yeni subdomain `dev.coremusic.net` = yazılımcılar için işletim sistemi benzeri arayüz.** Bu, ADR-004 domain listesine **eklemedir** ve **ayrı karar olarak ADR-082'de yazılacaktır** (ADR-082 dosyası diskte **YOK** → **düz metin, wiki-link kurulmadı**). Bu ADR'de yalnızca **test kişisi #8 "yazılımcı"** için senaryo etkisi vardır: derin/CLI-vari arayüz, API/router sözleşmesi ve hata mesajı netliği senaryolarını kapsar (matris satır 8).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Statü quo (gate'siz CI)** — testler koşsun, eşik olmasın | Sıfır iş; pipeline kırmaz | Coverage bilinmiyor (0 rapor), 28 test görünmüyor, ADR-020/022 kapıları denetlenmiyor; "PR'da gate" hiç başlamaz | Bu ADR'nin doğuş sebebi tam da bu boşluk; GitHub Docs eşikli block'u standart sunuyor (§1.3 kaynak 17-18) |
| 2 | **Yalnız %80 satır coverage** (mevcut AGENTS.md §16) | Uygulama kolay, düşük eşik | Satır branşı kaçırır (§1.3 kaynak 9-10), kritik güvenlik kodu için yetersiz; persona bağı yok | Literatür "kritik kod ~%100 branş" diyor (LaunchDarkly/Harness); kullanıcı onayı %90 + kritik %100 |
| 3 | **Persona yazmadan klasik katman testi** (feature/bileşen bazlı) | Test isimleri basit kalır | "Kim için" sorusu kalır; a11y/çocuk/studio senaryoları yine yazılmaz; 68 persona emeği boşa gider | Kararın çekirdeği persona bağlamı; eski vault envanteri (§1.1-C) kullanılmadan matris anlamsız |
| 4 | **Yalnız mutation gate (Infection), coverage'sız** | Test kalitesini ölçer, şişirmeyi kırar | Yavaş; tam kapsam fotoğrafı vermez; eşik + PR block için coverage metriği gerekli | Mutation **ek** katman olarak §2.2d'de tutuldu; tek başına gate maliyetli (§1.3 kaynak 38 pratik) |
| 5 | **Yalnız E2E (Playwright) ile persona testi** | Persona senaryosuna en yakın | Bugün JS/E2E dosyası **0** (§1.1-A) → aylarca kurulum; yavaş, flaky (§1.3 kaynak 40-47) | Hibrit: birim+entegrasyon her persona için zorunlu, E2E §5.1/5'te kademeli |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Ölçülebilirlik kapıya bağlanır:** coverage raporu + eşik + merge block → ≥%80 kuralı (AGENTS.md §16) artık **denetlenebilir** hale gelir; ADR-020/021/022 test paketleri aynı kapıdan geçer.
- **Yanlış güven kapanır:** 28 görünmez test (OAuth 8, auth Domain 20) testsuite'a eklenir → yeşil CI gerçekten her şeyi koşar.
- **Öncelik netleşir:** 20 satır × (1 birim + 1 entegrasyon + 3 yol) = takip edilebilir iş listesi; 68 persona şişirmesi önlenir.
- **Kritik yol güvencesi:** auth/API/middleware %100 branş → argon2id, CSRF/CSP, rate limit, API key/versiyon (ADR-020/022) yolları istisnasız test edilir.
- **Literatür uyumu:** %90 satır+branch, kritik ~%100, PR block, mutation, retry/quarantine — hepsi 2025-26 kaynaklarıyla çaprazlı (§1.3 ~45 kaynak).
- **Yeni domain'e hazır:** `dev.coremusic.net` için "yazılımcı" personası matriste (satır 8) — ADR-082 yazıldığında test planı beklemez.

### 4.2 Olumsuz Sonuçlar

- **CI süresi artar:** coverage + Infection + retry maliyeti (mutation özellikle yavaş) → kritik dizinlerle sınırlandı (§2.2d).
- **Bakım yükü:** 40 senaryo (20×2) + eşik takibi → QA Engineer sahipliği şart; persona değişirse matris güncellenir (yeni ADR).
- **Gate erken kapanışı riski:** bugün 0 rapor → %90'a anında bağlamak PR'ları kilitler → kademeli geçiş zorunlu (§4.3/4).
- **Kritik %100 branş:** dallanma yoğun kodda (middleware/DTO) her satır için ek test → zaman maliyeti; kapsam `include/Api/Middleware` ile daraltıldı.
- **Arch Lead onayı bekliyor:** debate ✅ **TAMAMLANDI** (3 tur / 20 persona, 18/2/0 KABUL — §5.3) + Tech Lead ✅ (§7) → **Arch Lead ⏳** (üçüncü satır) ✅ olmadan karar **Active/Frozen olmaz** (şablon §4.2); **frozen YOK**; 3 bağlayıcı şart §5.4.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Persona şişirme** (68+8 adaydan yeni satırlar açılması, matris bakıma boğulur) | 3 (olası) | 3 (orta) | Matris **20 satırda sabit**; yeni persona = **yeni ADR** (§2.2a kuralı); demografik gruplar tek temsilci ile yazılır |
| **Flaky test → gate sahteliği** (retry ile geçip gerçek hatayı gizleme / sürekli merge bloğu) | 3 (olası) | 4 (yüksek) | retry **2** + **quarantine** listesi + ilk hata/retry birlikte raporlanır (§1.3 kaynak 41-42); quarantine testleri eşiğe sayılmaz, 14 günde kapanma şartı |
| **Coverage oyunu / taşma** (eşiğe ulaşmak için assertion'sız/özdeş test, "kod çalışsın" testleri) | 3 (olası) | 3 (orta) | **Mutation gate** (`--min-msi` %65→%80, §2.2d) + patch coverage (değişen satır) + assertion-density denetimi; coverage ≠ kalite (§1.3 kaynak 13) |
| **Gate'in ilk gün kilitlenmesi** (0 rapordan %90'a anında geçiş → tüm PR'lar red) | 4 (çok olası) | 3 (orta) | Kademeli: baseline ölç → **%70 → %80 → %90** (§2.2d); kritik %100 branş **shared/auth testleri yeşilken** açılır |
| **Kritik kapsam genişlemesi** (payment kodu yokken "payment" etiketiyle yanlış güvenlik hissi) | 3 (olası) | 3 (orta) | Ödeme satırı (13) **PLANNED + `⚠️ VERIFICATION REQUIRED`**; kapsam dosya yoluyla sabit (`include/`, `Api/`, `Middleware/`) — etiket değil dizin |
| **Testsuite düzeltmesi eski testleri kırar** (28 test ilk kez koşunca fail çıkması) | 4 (çok olası) | 3 (orta) | §5.1/2'de **önce** eklenir ve tek başına koşulur; kırmızı çıkan test ya düzeltilir ya quarantine'a alınır (kapatma = `log.md` append + neden) |
| **Eski persona diske taşınırsa/bozulursa** (AIU/SSOT ihlali, mojibake) | 2 (mümkün) | 4 (yüksek) | Bu ADR yalnız **özetledi** (§1.1-C); eski dizine **yazma yok**; In-Place yasağı + `vault-utf8-writer.mjs verify` |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Baseline coverage ölçümü:** `vendor/bin/phpunit --coverage-text` (shared) + `--testsuite Unit` (auth) → mevcut satır/branş % kayda girer (`⚠️ VERIFICATION REQUIRED` bugün) | QA Engineer | 0.5 oturum |
| 2 | **Testsuite boşluklarını kapat:** `shared/phpunit.xml`'e `OAuth` suite'i (+ `oauth` klasörü), `auth/phpunit.xml`'e `tests/Domain` (veya `Integration` yerine gerçek dizin); 28 test tek başına koşulur → çıkan hatalar düzeltilir | QA Engineer | 1 oturum |
| 3 | **20 persona matrisini testlere bağla:** §2.2a satırlarına göre 40 senaryo (20 × birim + entegrasyon), her birinde H/E/B; `#[Test]` + `@group persona-NN` etiketi; eski vault `test-senaryolari/*` kod'a taşınır (disk kaynağı okunur, **kopyalanmaz**) | QA Engineer + Backend Architect | 3 oturum |
| 4 | **Coverage config:** `shared/phpunit.xml` + `auth/phpunit.xml` coverage raporu (clover + text) standart hale getirilir; `composer.json`'a `test:coverage` scripti | QA Engineer | 0.5 oturum |
| 5 | **CI gate:** `.github/workflows/ci.yml`'e coverage adımı + eşik kontrolü (genel %90 satır+branch, kritik dizinler %100 branch) + **PR merge kuralı** (branch protection / ruleset: gate yeşil değilse merge yok); eşik kademeli (baseline → 70 → 80 → 90) | DevOps Engineer + QA Engineer | 1.5 oturum |
| 6 | **Flaky politikası:** retry(2), quarantine listesi (`.github` config), ilk hata + retry raporu; `failOnRisky/failOnWarning` shared'a yayılır | QA Engineer + DevOps Engineer | 0.5 oturum |
| 7 | **Mutation gate:** Infection kurulumu + `infection.json` (kapsam: `auth.coremusic.net/include/**`, `shared/src/Api/**`, `shared/src/Middleware/**`) + `--min-msi 65` → %80'e kademeli; CI'da yalnız PR'da | QA Engineer | 1 oturum |
| 8 | **Vault senkronu:** `AGENTS.md` §16 "≥%80" → "**%90 satır+branch; kritik %100 branş**" + §10.1 eskalasyon eşiği; `.ai/.decisions/index.md:60` satırı bu dosya ile canlanır; `brain.md`/`keys.md`/`index.md` kayıtları | Vault Steward | 0.5 oturum |
| 9 | **PHPUnit `^12` geçiş planı:** attribute/static provider denetimi (31 `#[Test]` zaten uyumlu), deprikasyon raporu `composer stan` ile; geçiş ayrı adım/ADR gerektirir | QA Engineer + Backend Architect | 0.5 oturum |
| 10 | **JS/E2E iskeleti (PLANNED):** Vitest + Playwright kurulumu, persona 8/12/17 (SPA navigasyon) senaryolarıyla başlar; `home.coremusic.net/tests/` açılır | QA Engineer + UI Designer | 2 oturum |
| 11 | **Debate:** 3 tur / 20 persona (ADR-003/020/021/022 formatı) → sonuç §5.3'e + şartlar §5.4'e + frontmatter `debate` alanına + `log.md` append — **✅ TAMAMLANDI (2026-09-25, 18/2/0 KABUL)** | Vault Steward + adr-debate | 1 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** (`ADR-088+` serisi), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı). **Frozen YOK** olduğu için düzeltme, Arch Lead onayıyla `version` artırarak yapılır (`log.md` append).
2. **CI gate (adım 5):** eşik job'u tek commit ile devre dışı bırakılabilir → koruma kalkar, **ama** coverage raporu çalışmaya devam eder (ölçüm gate'den bağımsız); geri alma `log.md`'ye neden ile yazılır.
3. **Kademeli eşik geri alınabilir:** %90 → %80 → %70'e düşürülebilir (pipeline'ı kurtarmak) → **düşürme = geçici, `log.md` kayıtlı**; kalıcı düşürme = yeni ADR.
4. **Testsuite düzeltmesi (adım 2):** suite satırı kaldırılarak 28 test yeniden "görünmez" olur → kırık testi kapatmak yerine **quarantine** (adım 6); kalıcı silme = yeni ADR.
5. **Mutation gate (adım 7):** `infection.json` silinerek kapatılabilir (yalnız test kalitesi ölçümü kaybolur, coverage gate durur) → anında geri dönüş.
6. **Persona matrisi (adım 3):** test dosyaları `@group persona-NN` ile ayrıldığı için grup tek komutla dışlanabilir (`--exclude-group`) → matris devre dışı, testler durur; geri alma = grup.
7. **Flaky quarantine:** kararsız test listeden çıkarılarak normal koşuya döner (retry kalkar, sinyal bozulabilir) → 14 gün kuralı ihlal edilirse otomatik geri al (adım 6).
8. **Eski persona dizini:** hiçbir adım o diske yazmaz → geri alınacak bir değişiklik **yok** (yalnız okundu).
9. **Kural ihlali / layer violation:** derhal revert + log ERROR (`AGENTS.md` §17.7); frozen ADR-001-037'e dokunulduysa `git checkout` (§17.10).
10. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (geçmiş satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona tamamlandı; sonuç **18 kabul / 2 çekimser / 0 red → KABUL**; 3 bağlayıcı şart §5.4'e işlendi |
| Biçim | ADR-003/020/021/022 formatı — 3 tur / 20 persona |
| Karar içeriği | Kullanıcı onaylı kapsam: **(a) 20 persona test matrisi (birim + entegrasyon, happy/error/boundary) · (b) coverage %90 satır + şart · (c) kritik yollar %100 branş (auth/payment/ADR-020 API) · (d) PR'da coverage gate (CI düşerse merge yok)** |
| Beklenen tartışma eksenleri | Persona şişirme sınırı (20 sabiti) · eşik kademelenmesi (0 → %90) · flaky quarantine kuralı · mutation maliyeti · AGENTS.md %80 ile çelişki · `dev.coremusic.net` kapsamı (ADR-082'ye devir) |
| Tur 1 (20 persona — kanıt paketi) | Envante: 32 test dosyası / ~243 metot · **28 test CI'da koşulmuyor** · coverage raporu **0** · workflows **2** (ADR-017 bulgusu güncellendi) · JS/E2E **0** · eski vault **68 persona + 6 senaryo** · ev/inventory bulguları: `home` 0 test · auth **Domain↔Unit kopya test şüphesi** · eski vault 80 dosyalık persona paketi bu vault'a taşınmadı · ADR-017 "workflows = 0" **düzeltilmiş (2 dosya)** · oy eğilimi **15 kabul/neutral + 4 uyarı** · Critic: **ölü persona referansı + kapsam boşluğu** → şart 1 ve 2'ye bağlandı |
| Tur 2 (İtiraz → çözüm) | **4 itiraz → 4 çözüm → şart:** **(1)** 28 test testsuite dışı (OAuth 8 + auth Domain 20) → **CI testsuite tamamlama** → **şart 1a** · **(2)** auth Domain↔Unit kopya test şüphesi → **tekilleştirme** (assert kapsamı karşılaştırılır) → **şart 1b** · **(3)** eski persona vault'ta yok (dead reference) → **`.ai/.personas/` taşıma** → **şart 1c** · **(4)** E2E/JS/home 0 → **kritik akış E2E** ([[ADR-018-footer-player-vaporwave]] player + [[ADR-020-api-public-security]] API) → **şart 2** |
| Tur 3 (Oy) | **18 kabul / 2 çekimser / 0 red → KABUL** (20 persona oy kullandı) |
| Bağlayıcı şartlar | **3 şart (bağlayıcı — §5.4):** **(1)** CI testsuite + kopya test tekilleştirme + persona taşıma (1a-1c) · **(2)** E2E kapsamı + coverage gate (PR'da) · **(3)** kritik %100 branch doğrulama + Infection kademeli MSI (65 → 80) |
| Debate sonrası | **✅ İşlendi:** §5.4 (şartlar) + §6 (bağ) + §7/§7.1 (Tech Lead ✅) + frontmatter `debate` alanına + `.ai/log.md` append |
| Kural | Debate **✅ TAMAMLANDI** + Tech Lead **✅** → **Arch Lead ⏳** (§7 üçüncü satır) ✅ olmadan Active/Frozen olmaz (şablon §4.2); **frozen YOK** |

### 5.4 Bağlayıcı Şartlar (debate 3/20 — KABUL koşulları)

| # | Şart | Kapsam | Sorumlu | Bağ |
|---|------|--------|---------|-----|
| 1 | **CI testsuite + kopya test + persona taşıma** | **1a** `shared/phpunit.xml`'e `OAuth` + `auth.coremusic.net/phpunit.xml`'e `tests/Domain` suite'i eklenir → **28 test CI'da koşulur** · **1b** auth `Domain ↔ Unit` kopya seti assert kapsamı karşılaştırılıp **tekilleştirilir** (`⚠️ VERIFICATION REQUIRED` 1b'de kapanır) · **1c** eski vault persona envanteri **`.ai/.personas/` altına taşınır** (eski disk korunur; dosya adı değişmez — In-Place istisnası debate onayıyla) | QA Engineer + Vault Steward | §5.1/2 · §5.1/3 |
| 2 | **E2E kapsamı + coverage gate (PR'da)** | JS/E2E **0 → kritik akış E2E**: [[ADR-018-footer-player-vaporwave]] footer player + [[ADR-020-api-public-security]] API akışları (persona 7/8/17); `home.coremusic.net/tests/` açılır · coverage gate **PR merge kuralına** bağlanır, eşik kademeli (baseline → 70 → 80 → 90) | QA Engineer + DevOps Engineer | §5.1/5 · §5.1/10 |
| 3 | **Kritik %100 branch doğrulama + Infection kademeli MSI** | Kritik dizinler (`auth.coremusic.net/include/**`, `shared/src/Api/**`, `shared/src/Middleware/**`) **%100 branş ölçülür ve doğrulanır** (baseline §5.1/1) · Infection `--min-msi` **kademeli 65 → 80** | QA Engineer | §2.2b · §5.1/7 |

**Şart 1c uygulama notu (2026-09-26, v1.1.0):** Kullanıcı onayıyla "taşıma" yerine **sıfırdan yeniden yazım** uygulanmıştır:
`.ai/.personas/` altında `index.md`, `methodology.md`, `mood-taxonomy.md`, `test-scenarios-mapping.md`,
`research-bank.md` + `test-senaryolari/` (6 dosya) + 6 grup temsilcisi persona `.templates/personas/persona-template.md`
(Registry #37) iskeletiyle üretilmiştir. Eski vault (`coremusic.net.old/.ai/personas/`, 80 dosya / 24.904 satır)
salt okunur korunur; dosya adları değişmemiştir (In-Place korunur). Kalan 62 persona kademeli dalga olarak planlanmıştır.
Doğrulama: her gerçek-dünya iddia ≥2 bağımsız kaynak — `.ai/.personas/research-bank.md` (58 VERIFIED / 2 CONFLICT / 3 DERIVED / 10 EXCLUDED).

**Koşul:** 3 şart da kapanmadan karar **Active/Frozen olmaz** (Arch Lead ⏳ dahil — §7); her şart kapanışı `.ai/log.md` append ile kaydedilir; şart kapsamı dışı değişiklik = **yeni ADR** (ADR-088+).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 60** `[[ADR-023-persona-driven-testing]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | §6 routing (`test, coverage, PHPUnit, E2E → QA Engineer`), §16 kalite (≥%80 → bu ADR %90'a sıkılaştırır), §10.1 eskalasyon, §25.2 stack kanıtı (`shared/tests/ 22 dosya`, `.github/workflows/ = 0` → §1.1-B'de güncellendi: **2 dosya**) |
| [[../../WORKFLOW.md]] | Test/QA fazları, debate/onay akışı |
| [[../../brain.md]] | ADR özetleri (bu ADR kaydı debate sonrası eklenir) |
| [[../../keys.md]] | Keyword haritası: `test, coverage, persona, PHPUnit, CI gate` → bu ADR |
| [[../../index.md]] | Karar kataloğu |
| [[../../log.md]] | Audit trail — bu işlem iki append: "ADR-023 yazıldı (debate PENDING)" + "ADR-023 debate 3/20 kaydedildi (18/2/0 KABUL) + Tech Lead ✅ + 3 şart" |
| [[ADR-005-ultrathink-protocol]] | `⚠️ VERIFICATION REQUIRED` standardı (§1.1/§4.3 etiketleri) |
| [[ADR-020-api-public-security]] | Kritik yol kapsamı: API/middleware %100 branş |
| [[ADR-021-spa-router-immutable-contract]] | PageRouter 4 dosya/21 test (TEYİT §1.1-A) |
| [[ADR-002-pdo-mandatory-no-orm]] | Test fixtures prepare-only |
| Karar alt registry: [[../CLAUDE.md]] (`.ai/.decisions/CLAUDE.md`) | accepted/ dizin sözleşmesi |
| **Bağlayıcı şartlar (debate 3/20 → §5.4)** | 3 şart: **(1)** CI testsuite + kopya test tekilleştirme + persona taşıma (1a-1c) · **(2)** E2E kapsamı + coverage gate (PR'da) · **(3)** kritik %100 branch doğrulama + Infection MSI 65 → 80 — **18/2/0 KABUL** koşulu |
| **Düz metin (link YOK):** `dev.coremusic.net` · ADR-082 | Yeni subdomain (2026-09-25 kullanıcı duyurusu) ayrı kararda yazılacak — **ADR-082 dosyası diskte YOK → wiki-link kurulmadı** |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-023'ü sıfırdan yaz"; karar içeriği onaylı: 20 persona test matrisi · %90 satır+branch · kritik %100 branş · PR'da coverage gate) | 2026-09-25 | ✅ |
| Tech Lead | Debate 3/20 onaylı — 18 kabul / 2 çekimser / 0 red → KABUL (§5.3); 3 bağlayıcı şart §5.4 | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate ve Onay Notu

| Alan | Değer |
|------|-------|
| Debate | **✅ TAMAMLANDI** — 3 tur / 20 persona: **18 kabul / 2 çekimser / 0 red → KABUL** (§5.3); sonuç §5.3'e + **3 bağlayıcı şart §5.4'e** + §6'ya + frontmatter `debate` alanına işlendi, `.ai/log.md` append ile kaydedildi |
| Kanıt durumu | §1.1: **IMPLEMENTED** — 32 test dosyası / ~243 test metodu, PHPUnit ^10.5 ×3, phpunit.xml ×2, 2 CI workflow (lint+test+audit), PageRouter 4 dosya/21 test (ADR-021 TEYİT) · **PLANNED** — coverage ölçümü+eşik, PR merge gate, 28 testin testsuite'a eklenmesi, JS/E2E (0 dosya), home testleri (0), ödeme senaryosu · `⚠️ VERIFICATION REQUIRED`: coverage % değeri (0 rapor), Domain/Unit kopya assert eşitliği, ödeme kapsamı |
| Bulgu düzeltmeleri | `.github/workflows/ = 0` (ADR-017) → **bugün 2 dosya** (ci.yml, secret-scan.yml — 2026-09-24) · PageRouter **4/21** (ADR-021) → **TEYİT** · AGENTS.md §25.2 "`shared/tests/ 22 dosya`" → **TEYİT** |
| Frozen | debate ✅ (18/2/0 KABUL) + Tech Lead ✅ + **Arch Lead ⏳** → §7'nin üçüncü satırı ✅ olmadan Active/Frozen olmaz (şablon §4.2); **frozen YOK**; 3 şart §5.4 kapanmadan karar uygulanamaz |
| Kural | Debate ✅ TAMAMLANDI; `.ai/log.md`'ye debate kaydı tek satır append yapıldı; eski persona dizinine **yazma yok** (şart 1c yalnız bu vault'a kopya/taşıma — eski disk korunur) |

---

*ADR-023 v1.1.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-023 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
*Last Updated: 2026-09-26*

*ADR-023 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead ✅ · Arch Lead ⏳ · 3 şart §5.4 · frozen YOK*
