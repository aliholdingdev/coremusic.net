---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/cekirdek-test
  - K000-isletim-sistemi/isletim-sistemi-testing/uygulama-testi
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
evidence:
  - "shared/phpunit.xml:8-33"
  - "shared/phpunit.xml:34-38"
  - ".github/workflows/ci.yml:8"
  - ".github/workflows/ci.yml:24-108"
  - ".github/workflows/secret-scan.yml:5-31"
  - "shared/composer.json:46-49"
  - "package.json:4-9"
  - "auth.coremusic.net/phpunit.xml:13-32"
validation:
  - "2026-10-13 · disk okuması: phpunit.xml (39 satır), ci.yml (203 satır), secret-scan.yml (31 satır), composer.json scripts, package.json scripts, auth phpunit.xml (34 satır)"
  - "2026-10-13 · alt-dal dosya adları bu turda kesinleşti (anayasa · surucu · ornekler) — §B tablosu disk gerçeğiyle hizalandı"
open_questions:
  - "T1: OS/çekirdek test (KUnit/kselftest) hedef mimaride kapsam mı, yalnız referans mı? [UNKNOWN]"
  - "T2: Coverage hedefi tanımlı mı? phpunit.xml L34-38 <source> var, eşik yok. [UNKNOWN]"
group: mimari
---

# isletim-sistemi-testing — Kapsam

> Bu dosya bu subdomainin **sınırlarını** çizer: neyi tutar, neyi tutmaz, üç alt-dalda ne vardır,
> hangi node'lara bağımlıdır ve komşularla ayrımı nerededir. İçerik (hangi senaryonun test
> edileceği) ilgili alt-dalların kendi dosyalarındadır — buraya kopyalanmaz.

## §A — Kapsam

### §A.1 Kapsam içi

| # | Kapsam maddesi | Neden bu dalda | Kanıt |
|---|---|---|---|
| A-01 | OS düzeyi test araçlarının tanımlanması (KUnit) | Çekirdek içi birim testi — uygulama testi değil | `docs.kernel.org/dev-tools/kunit` |
| A-02 | OS düzeyi test araçlarının tanımlanması (kselftest) | Kullanıcı uzayı test paketleri | `docs.kernel.org/kselftest` |
| A-03 | Boot testi (kexec / bozma testleri) | OS katmanının doğrulanması | `man7.org kexec_file_load.2` |
| A-04 | Test ortamı altyapısı (QEMU / VM) | OS testinin çalıştığı ortam | `qemu.org/docs/master/system` |
| A-05 | Fault injection (hata enjeksiyonu) | OS test metodolojisinin parçası | `docs.kernel.org/fault-injection` |
| A-06 | Kapsama ölçüm altyapısı (KUnit coverage bağlamı) | Ölçüm bölümünün girdisi | `docs.kernel.org/dev-tools/kunit/usage` |
| A-07 | Kullanıcı uzayı test iskeleti (kselftest_harness) | Test seviyeleri bölümünün girdisi | `docs.kernel.org/kselftest` |
| A-08 | Uyumluluk testi çerçevesi (POSIX / Austin Group) | Sistem testinin referans kaynağı | `opengroup.org/austin` |
| A-09 | Emülatör / sandbox stratejisi | Sanal ortamda test güvenliği | `qemu.org/docs/master/system` |
| A-10 | Sanal donanım testi (virtio · disk · ağ) | Sanal makine üzeri sistem testi | `qemu.org/docs/master/system/targets` |
| A-11 | PHPUnit yapılandırmasının diskten okunması | Uygulama test altyapısının kanıtı | `shared/phpunit.xml:1-39` |
| A-12 | Test suite envanteri (8 suite) | Uygulama testinin fiili kapsamı | `shared/phpunit.xml:8-33` |
| A-13 | Test dizini ve dosya sayımları | Kapsam büyüklüğünün ölçümü | `shared/tests/` glob |
| A-14 | Coverage kaynak tanımı (`<source>`) | Ölçüm bölümünün disk kanıtı | `shared/phpunit.xml:34-38` |
| A-15 | Bootstrap ve önbellek ayarı | Test çalıştırma ön koşulu | `shared/phpunit.xml:4,7` |
| A-16 | CI job/step matrisi | Testin nerede çalıştığı | `.github/workflows/ci.yml:24-203` |
| A-17 | Statik analiz kapısı (PHPStan) | Kalite kapısı bileşeni | `.github/workflows/ci.yml:62-64` |
| A-18 | Test kapısı (PHPUnit matrisi) | Kalite kapısı bileşeni | `.github/workflows/ci.yml:66-108` |
| A-19 | Bağımlılık denetimi kapısı (composer audit) | Güvenlik kapısı bileşeni | `.github/workflows/ci.yml:110-139` |
| A-20 | JS test kapısı (Vitest) | Uygulama test altyapısı | `package.json:5` |
| A-21 | JS coverage kapısı (yumuşak) | Eşik davranışı kanıtı | `.github/workflows/ci.yml:160-176` |
| A-22 | E2E kapısı (Playwright, yumuşak) | Eşik davranışı kanıtı | `.github/workflows/ci.yml:178-203` |
| A-23 | Secret tarama (GitLeaks) | Ayrı workflow, kalite kapısı komşusu | `.github/workflows/secret-scan.yml:5-31` |
| A-24 | Workflow tetikleyici ve eşzamanlılık kuralları | Kapı davranışı | `.github/workflows/ci.yml:10-22` |
| A-25 | Workflow izinleri (contents: read) | Asgari yetki kanıtı | `.github/workflows/ci.yml:14-15` |
| A-26 | Runner zaman aşımı değerleri (timeout-minutes) | Kapı dayanıklılık kanıtı | `.github/workflows/ci.yml:28,70,113,145,164,181` |
| A-27 | Composer script eşlemesi (test · stan) | Test komutunun kaynağı | `shared/composer.json:46-49` |
| A-28 | Node script eşlemesi (test:js · test:e2e) | JS test komutunun kaynağı | `package.json:4-9` |
| A-29 | Smoke / regression / servis-entegrasyon testi tanımı | Alt-dal kapsamı (uygulama-testi) | `uygulama-testi/uygulama-testi-surucu.md` |
| A-30 | Konfigürasyon doğrulama adımları | Alt-dal kapsamı (uygulama-testi) | `uygulama-testi/uygulama-testi-surucu.md` |
| A-31 | Test verisi ve sıfırlama kuralı | Alt-dal kapsamı (uygulama-testi) | `uygulama-testi/uygulama-testi-surucu.md` |
| A-32 | CI pipeline aşaması sırası (lint → test → build → gate) | Alt-dal kapsamı (ci-ve-kalite-kapisi) | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md` |
| A-33 | Gate koşulu ve eşiği | Alt-dal kapsamı (ci-ve-kalite-kapisi) | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md` |
| A-34 | Otomatik geri alma (revert) kuralı | Alt-dal kapsamı (ci-ve-kalite-kapisi) | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md` |
| A-35 | Artifact / kanıt saklama süresi | Alt-dal kapsamı (ci-ve-kalite-kapisi) | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md` |
| A-36 | Doğrulama yöntemleri (web + disk) | Kanıt zinciri | `-dogrulama.md` §1-§5 |
| A-37 | Örnek yapılandırmalar | Kalıp gösterimi | `-ornekler.md` §1-§8 |
| A-38 | Ayrım matrisi (OS test ↔ uygulama test ↔ K019) | Sınırın çizilmesi | bu dosya §C |
| A-39 | Bağımlılık grafiği | Node'lar arası yön | bu dosya §D |
| A-40 | Sınır komşuları | kernel, core, plan, security, observability, turu, K018, K019 | bu dosya §E |
| A-41 | Windows/macOS/Linux taşınabilir test kodu (10 dil gruplu · P-01..P-10 · E-01..E-13) | Hedef OS = Linux, test kodu 3 OS'ta çalışır (kullanıcı kuralı 2026-10-14) | `isletim-sistemi-testing-cross-platform-ornekleri.md` §1-§5 |

### §A.2 Kapsam dışı

| # | Kapsam dışı madde | Nereye ait | Not |
|---|---|---|---|
| X-01 | Uygulama test **stratejisi** | `K18-testing` | Test seçimi, önceliklendirme |
| X-02 | Coverage **hedefi / eşiği** politikası | `K019-testing/coverage/` | Bu dal yalnız diskteki `<source>` tanımını raporlar |
| X-03 | Flaky test tespiti ve karantinası | `K18-testing/flaky/` | Bu dala girilmez |
| X-04 | Contract test uyumluluğu | `K18-testing/contract/` | Bu dala girilmez |
| X-05 | Performance test ölçüm protokolü | `K18-testing/performance/` | Ağır ölçüm işi |
| X-06 | Regression test seçım kuralları | `K18-testing/regression/` | Strateji kararı — bu dal yalnız tanımı taşır |
| X-07 | Unit test tasarım desenleri | `K18-testing/unit/` | Desen kataloğu |
| X-08 | Integration test ortamı (uygulama) | `K18-testing/integration/` | Uygulama katmanı |
| X-09 | E2E tarayıcı testi detayı | `K18-testing/e2e/` | Tarayıcı senaryoları |
| X-10 | Deploy ve release pipeline'ı | `K17-devops` | Kapı sonrası aşama |
| X-11 | Runner/altyapı yönetimi, saklama stratejisi | `K17-devops` | CI altyapısı sahipliği |
| X-12 | Kodun kendisi (`*.php`, `*.js`, `*.css`, `*.sql`) | ilgili sahip ajanlar | Bu dal yalnız okur |
| X-13 | `phpunit.xml` ve `ci.yml` değişikliği | QA Engineer / DevOps Engineer | Bu dal dosyaları değiştirmez |
| X-14 | Donanım testi (DSP, PCB) | `K001-donanım` | Fiziksel ölçüm |
| X-15 | Çekirdek kodunun kendisi (Linux kernel kaynağı) | repo dışında | CoreMusic'te çekirdek kodu yok |
| X-16 | Ses kalitesi doğrulaması | `K18-testing/audio-quality/` | Ölçüm domeni |
| X-17 | Build doğrulama stratejisi | `K019-testing/build-verification/` | Derleme kapısı |
| X-18 | Vault boot dosyaları (`CLAUDE.md`, `AGENTS.md`) | `.ai/` kök | Bu dalda dokunulmaz |
| X-19 | `.decisions/` ADR metinleri | `.ai/.decisions/` | Frozen |
| X-20 | `.ai/architecture/index.md` | vault kataloğu | Yalnız orkestratör |
| X-21 | `K019-testing/**` dosyalarının yazımı | ayrı ajan | DOKUNMA |
| X-22 | `K000-isletim-sistemi/index.md` ve `os-*.md` | ayrı ajan | DOKUNMA |
| X-23 | Diğer K-kütüphaneleri (K001..K018, K020) | sahipleri | DOKUNMA |
| X-24 | `secret-scan.yml` içeriğinin değişimi | DevOps Engineer | Yalnızca okundu |
| X-25 | `isletim-sistemi-kernel/**` | paralel iskelet ajanı | DOKUNMA — sınır komşusu |
| X-26 | `isletim-sistemi-core/**` | paralel iskelet ajanı | DOKUNMA — süreç testi sınırı |
| X-27 | `isletim-sistemi-plan/**` | paralel iskelet ajanı | DOKUNMA — zamanlama testi sınırı |
| X-28 | `isletim-sistemi-security/**` | paralel iskelet ajanı | DOKUNMA — güvenlik testi sınırı |
| X-29 | `isletim-sistemi-observability/**` | paralel iskelet ajanı | DOKUNMA — gözlem doğrulama sınırı |
| X-30 | `isletim-sistemi-turu/**` | paralel iskelet ajanı | DOKUNMA — platform test matrisi sınırı |
| X-31 | `isletim-sistemi-cross/**`, `-runtime/**`, `-api/**`, `-audio/**` | paralel ajanlar | DOKUNMA |
| X-32 | `electroncis/**` ve `.ai/electronic/**` | elektronik ajanları | DOKUNMA |
| X-33 | `K018-devops/**` | devops ajanı | DOKUNMA |
| X-34 | ADR yazımı | orkestratör | Bu dal yalnız okur |

### §A.3 Sınır kararı özeti

| Soru | Karar | Sahip | Kanıt |
|---|---|---|---|
| PHPUnit testleri bu dalda mı? | Evet — altyapı ve kapı olarak | bu dal | `shared/phpunit.xml:8-33` |
| Test **stratejisi** bu dalda mı? | Hayır | `K18-testing` | görev tanımı |
| KUnit/kselftest bu dalda mı? | Evet — hedef referans olarak | bu dal | §A.1 A-01, A-02 |
| Çekirdek kodu yazımı | Hayır — repo'da çekirdek kodu yok | repo dışı | §A.2 X-15 |
| CI workflow dosyası değişikliği | Hayır — yalnız okuma | DevOps Engineer | §A.2 X-13 |
| Coverage eşiği | Hayır — yalnız diskte ne varsa raporlanır | `K019-testing/coverage/` | §F.2 |
| Secret tarama kapsamı | Evet — kapı bileşeni olarak | bu dal | `secret-scan.yml:5-31` |
| Deploy/rollback pipeline'ı | Hayır | `K17-devops` | §A.2 X-10 |
| Platform test matrisi | Hayır | `isletim-sistemi-turu` | §E E-07 |
| Donanım (fiziksel) test | Hayır | `K001-donanım` | §A.2 X-14 |

## §B — 3 alt-dal envanteri

| # | Alt-dal / dosya | Kademe | group | İçerik yüzeyi | Kanıt türü |
|---|---|---|---|---|---|
| B-01 | `cekirdek-test/index.md` | Kademe-3 | mimari | Alt-dal künyesi, çocuk kataloğu, komşu matrisi | — |
| B-02 | `cekirdek-test/cekirdek-test-anayasa.md` | Kademe-4 | mimari | §0 ELI15, test seviyeleri, çekirdek test çerçeveleri, terimler, T1 | web + disk |
| B-03 | `cekirdek-test/cekirdek-test-surucu.md` | Kademe-4 | mimari | QEMU/KVM sistemsel test, emülatör/sandbox, sanal donanım, ölçüm | web + disk |
| B-04 | `cekirdek-test/cekirdek-test-ornekler.md` | Kademe-4 | kod | Komut/konfigürasyon örnekleri, repo disk kanıtı, riskler | web + disk |
| B-05 | `uygulama-testi/index.md` | Kademe-3 | mimari | Alt-dal künyesi, çocuk kataloğu, disk kanıtı özeti | — |
| B-06 | `uygulama-testi/uygulama-testi-anayasa.md` | Kademe-4 | mimari | §0 ELI15, OS↔uygulama testi ayrımı, smoke/regression, T2 | disk + web |
| B-07 | `uygulama-testi/uygulama-testi-surucu.md` | Kademe-4 | mimari | Servis/entegrasyon testi, konfigürasyon doğrulama, test verisi/sıfırlama, ölçüm | disk + web |
| B-08 | `uygulama-testi/uygulama-testi-ornekler.md` | Kademe-4 | kod | `phpunit.xml` gerçek kod, test dosyası örneği, repo kanıtları | disk |
| B-09 | `ci-ve-kalite-kapisi/index.md` | Kademe-3 | mimari | Alt-dal künyesi, çocuk kataloğu, job özeti | — |
| B-10 | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa.md` | Kademe-4 | mimari | §0 ELI15, pipeline aşamaları, gate koşulları, geri alma, K018 sınırı | disk + web |
| B-11 | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md` | Kademe-4 | mimari | Gate eşikleri, secret taraması, artifact/kanıt saklama, ölçüm | disk + web |
| B-12 | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-ornekler.md` | Kademe-4 | kod | `ci.yml` gerçek kod, `secret-scan.yml`, composer/package scriptleri | disk |

Toplam: **12 dosya** (3 `index.md` + 9 içerik) + bu dosyanın bulunduğu kök 4 dosya = **16 dosya**.

*2026-10-14 ölçüm notu (tahmin değil, disk sayımı):* kök dosya sayısı 4 değil **10**'dur (`index` · `kapsam` · `anayasa` · `dogrulama` · `surucu` · `riskler` · `web-arastirma` · `kod-ornekleri` · `ornekler` · `cross-platform-ornekleri`); bu dalın gerçek toplamı 12 + 10 = **22 dosya**. Üstteki satır tarihsel (2026-10-13) hedef sayımı olarak korunmuştur; §H matrisi güncel envanterin kaynağıdır.

Ad kuralı: alt-dal içerik dosyalarının adları **değiştirilemez** — `*-anayasa.md` · `*-surucu.md` · `*-ornekler.md`.

## §C — OS test ↔ uygulama test ↔ K019 ayrım matrisi

| # | Test türü | OS test (cekirdek-test) | Uygulama test (uygulama-testi) | K18-testing | Repo durumu |
|---|---|---|---|---|---|
| C-01 | Birim test — çekirdek içi | KUnit | bu dalda değil | bu dalda değil | yok |
| C-02 | Birim test — PHP sınıfı | bu dalda değil | PHPUnit `tests/Unit` | desenler `K019/unit` | var (15 dosya) |
| C-03 | Kullanıcı uzayı testi | kselftest | bu dalda değil | bu dalda değil | yok |
| C-04 | Entegrasyon testi (uygulama) | bu dalda değil | PHPUnit `tests/Repository`, `tests/Api` | `K019/integration` | var (12 dosya) |
| C-05 | API/Uç nokta testi | bu dalda değil | PHPUnit `tests/Api` | `K019/contract` | var (8 dosya) |
| C-06 | Middleware testi | bu dalda değil | PHPUnit `tests/Middleware` | bu dalda değil | var (9 dosya) |
| C-07 | Güvenlik testi (uygulama) | bu dalda değil | PHPUnit `tests/Security`, `tests/OAuth` | `K019/security` | var (3 dosya) |
| C-08 | Event testi | bu dalda değil | PHPUnit `tests/Events` | bu dalda değil | var (2 dosya) |
| C-09 | Component testi | bu dalda değil | PHPUnit `tests/Component` | bu dalda değil | var (1 dosya) |
| C-10 | Boot testi | kexec + VM | bu dalda değil | bu dalda değil | yok |
| C-11 | Fault injection | kernel fault injection | bu dalda değil | bu dalda değil | yok |
| C-12 | E2E tarayıcı testi | bu dalda değil | Playwright `test:e2e` | `K019/e2e` | var (CI'da yumuşak) |
| C-13 | JS birim testi | bu dalda değil | Vitest `test:js` | `K019/unit` | var (CI'da sert) |
| C-14 | Coverage ölçümü (PHP) | bu dalda değil | PHPUnit `<source>` | `K019/coverage` | tanım var, eşik yok |
| C-15 | Coverage ölçümü (JS) | bu dalda değil | Vitest `--coverage` | `K019/coverage` | var (CI'da yumuşak) |
| C-16 | Flaky tespiti | bu dalda değil | bu dalda değil | `K019/flaky` | K019'da |
| C-17 | Contract uyumluluk | bu dalda değil | bu dalda değil | `K019/contract` | K019'da |
| C-18 | Performance ölçümü | bu dalda değil | bu dalda değil | `K019/performance` | K019'da |
| C-19 | Regression seçimi | bu dalda değil | tanım bu dalda, kural K019 | `K019/regression` | K019'da |
| C-20 | Statik analiz (PHPStan) | bu dalda değil | kapı → `ci-ve-kalite-kapisi` | bu dalda değil | var (level 5) |
| C-21 | Lint (checkpatch) | hedef: `cekirdek-test` | bu dalda değil | bu dalda değil | yok |
| C-22 | Secret tarama (GitLeaks) | bu dalda değil | kapı → `ci-ve-kalite-kapisi` | bu dalda değil | var |
| C-23 | Bağımlılık denetimi (audit) | bu dalda değil | kapı → `ci-ve-kalite-kapisi` | bu dalda değil | var |
| C-24 | Donanım doğrulama | bu dalda değil | bu dalda değil | `K019/hardware-validation` | `K001-donanım` sınırı |
| C-25 | Ses kalitesi doğrulama | bu dalda değil | bu dalda değil | `K019/audio-quality` | K019'da |
| C-26 | Smoke test | bu dalda değil | `uygulama-testi-surucu` §2 | bu dalda değil | CI'da ayrı adım yok |
| C-27 | Konfigürasyon doğrulama | bu dalda değil | `uygulama-testi-surucu` §4 | bu dalda değil | kısmen (composer validate yok) |
| C-28 | Test verisi / sıfırlama | bu dalda değil | `uygulama-testi-surucu` §5 | bu dalda değil | Fixtures dizini var |
| C-29 | Gate eşik tanımı | bu dalda değil | kapı → `ci-ve-kalite-kapisi` §2 | eşik politikası K019 | kısmen (yumuşak joblar) |
| C-30 | Otomatik geri alma | bu dalda değil | kapı → `ci-ve-kalite-kapisi` §3 | bu dalda değil | repo'da tanım yok |
| C-31 | Artifact saklama | bu dalda değil | kapı → `ci-ve-kalite-kapisi` §5 | bu dalda değil | repo'da tanım yok |
| C-32 | Platform test matrisi | bu dalda değil | bu dalda değil | bu dalda değil | `isletim-sistemi-turu` |
| C-33 | Zamanlama testi | bu dalda değil | bu dalda değil | bu dalda değil | `isletim-sistemi-plan` |
| C-34 | Süreç testi | bu dalda değil | bu dalda değil | bu dalda değil | `isletim-sistemi-core` |
| C-35 | Gözlem doğrulama | bu dalda değil | bu dalda değil | bu dalda değil | `isletim-sistemi-observability` |
| C-36 | Sürücü testi | `cekirdek-test` | bu dalda değil | bu dalda değil | repo'da sürücü kodu yok |
| C-37 | QEMU/emülatör testi | `cekirdek-test-surucu` §3 | bu dalda değil | bu dalda değil | repo'da karşılığı yok |
| C-38 | Sistem testi (OS) | `cekirdek-test` §1 | bu dalda değil | bu dalda değil | repo'da karşılığı yok |
| C-39 | Sistem testi (uygulama) | bu dalda değil | `uygulama-testi-surucu` §3 | `K019/integration` | var |
| C-40 | CI kalite kapısı | bu dalda değil | kapı → `ci-ve-kalite-kapisi` | bu dalda değil | var (5 job) |

## §D — Bağımlılık grafiği

| # | Node (kaynak) | Yön | Node (hedef) | Bağımlılık türü |
|---|---|---|---|---|
| D-01 | `isletim-sistemi-testing` | → | `isletim-sistemi-core` | alt-dal anahtarı, çekirdek test hedefi |
| D-02 | `isletim-sistemi-testing` | → | `isletim-sistemi-observability` | log/metric ile test doğrulama |
| D-03 | `isletim-sistemi-testing` | → | `cekirdek-test` | kapsam içerir |
| D-04 | `isletim-sistemi-testing` | → | `uygulama-testi` | kapsam içerir |
| D-05 | `isletim-sistemi-testing` | → | `ci-ve-kalite-kapisi` | kapsam içerir |
| D-06 | `cekirdek-test` | → | `isletim-sistemi-kernel` | sürücü/çekirdek testi sınırı |
| D-07 | `cekirdek-test` | → | `isletim-sistemi-plan` | zamanlama testi sınırı |
| D-08 | `cekirdek-test` | → | `K001-donanım` | donanım testi sınırı |
| D-09 | `uygulama-testi` | → | `shared/phpunit.xml` | disk kanıtı (okuma) |
| D-10 | `uygulama-testi` | → | `shared/tests/` | disk kanıtı (okuma) |
| D-11 | `uygulama-testi` | → | `shared/composer.json` | require-dev kanıtı |
| D-12 | `uygulama-testi` | → | `K18-testing` | strateji/flaky/contract sınırı |
| D-13 | `uygulama-testi` | → | `isletim-sistemi-runtime` | bootstrap ve çalışma zamanı |
| D-14 | `uygulama-testi` | → | `isletim-sistemi-api` | API testinin hedefi |
| D-15 | `ci-ve-kalite-kapisi` | → | `.github/workflows/ci.yml` | disk kanıtı (okuma) |
| D-16 | `ci-ve-kalite-kapisi` | → | `.github/workflows/secret-scan.yml` | disk kanıtı (okuma) |
| D-17 | `ci-ve-kalite-kapisi` | → | `K17-devops` | deploy/runner sınırı |
| D-18 | `ci-ve-kalite-kapisi` | → | `isletim-sistemi-security` | secret tarama kavramı |
| D-19 | `ci-ve-kalite-kapisi` | → | `package.json` | JS test scriptleri |
| D-20 | `ci-ve-kalite-kapisi` | → | `auth.coremusic.net/phpunit.xml` | matris args kanıtı |
| D-21 | `ci-ve-kalite-kapisi` | → | `shared/composer.json` | `stan` script kanıtı |
| D-22 | `-kapsam` | → | 3 alt-dal `index.md` | bölüm §B envanteri |
| D-23 | `-dogrulama` | → | tüm iddia kaynakları | kanıt zinciri |
| D-24 | `-ornekler` | → | `phpunit.xml`, `ci.yml` | örnek kod (okuma) |
| D-25 | `cekirdek-test-surucu` | → | `qemu.org` · `docs.kernel.org` | hedef referans (web) |
| D-26 | `uygulama-testi-surucu` | → | `docs.phpunit.de` | yapılandırma referansı (web) |
| D-27 | `ci-ve-kalite-kapisi-surucu` | → | `docs.github.com/en/actions` | pipeline referansı (web) |
| D-28 | `isletim-sistemi-testing` | → | `K000-isletim-sistemi` | üst node (parent_id) |
| D-29 | `isletim-sistemi-turu` | ← | `cekirdek-test` | platform test matrisi komşusu |
| D-30 | `isletim-sistemi-security` | ← | `ci-ve-kalite-kapisi` | güvenlik testi komşusu |

## §E — Sınır komşuları

| # | Komşu | İlişki | Ne paylaşılır | Ne paylaşılmaz | Kaynak |
|---|---|---|---|---|---|
| E-01 | `isletim-sistemi-core` | Bağımlılık | Test edilecek çekirdek/süreç davranış tanımı | Test senaryosu içeriği | kök FM `depends_on` |
| E-02 | `isletim-sistemi-observability` | Bağımlılık | Log/metric ile doğrulama sinyali | Alarm politikası | kök FM `depends_on` |
| E-03 | `K18-testing` | Sınır | Test terminolojisi (unit/integration/e2e) | flaky, contract, coverage eşiği, strateji | görev tanımı |
| E-04 | `K17-devops` | Sınır | `ci.yml` dosya referansı | deploy, rollback, runner yönetimi | görev tanımı |
| E-05 | `K001-donanım` | Sınır | Fiziksel test kavramı | Ölçüm protokolü, PCB testi | `cekirdek-test-surucu` §5 |
| E-06 | `isletim-sistemi-runtime` | Sınır | Bootstrap bilgisi (test öncesi kurulum) | Runtime mimarisi | `uygulama-testi-surucu` §4 |
| E-07 | `isletim-sistemi-turu` | Sınır | Platform test matrisi kavramı | Platform mimarisi | görev tanımı |
| E-08 | `isletim-sistemi-kernel` | Komşu | Çekirdek/sürücü test hedefi | Çekirdek tasarımı | görev tanımı |
| E-09 | `isletim-sistemi-plan` | Komşu | Zamanlama testi kavramı | Zamanlayıcı tasarımı | görev tanımı |
| E-10 | `isletim-sistemi-security` | Komşu | Secret tarama ve güvenlik testi kavramı | Güvenlik mimarisi | `ci-ve-kalite-kapisi-anayasa` §7 |
| E-11 | `isletim-sistemi-api` | Komşu | API testinin hedefi (suite `Api`) | API tasarımı | `uygulama-testi-anayasa` §7 |
| E-12 | `K18-testing/coverage` | Komşu | `<source>` tanımı (disk kanıtı) | Eşik politikası | `-kapsam` §F.2 |
| E-13 | `K18-testing/flaky` | Komşu | bu dalda paylaşılan yok | Her şey | `-kapsam` §A.2 X-03 |
| E-14 | `K18-testing/contract` | Komşu | bu dalda paylaşılan yok | Her şey | `-kapsam` §A.2 X-04 |
| E-15 | `K013-integraion` | Komşu | CI/entegrasyon testi adı benzerliği | Tasarım | K-uzay |
| E-16 | `K016-observability` | Komşu | Gözlemlenebilirlik testi kavramı | Metrik politikası | K-uzay |
| E-17 | `isletim-sistemi-cross` | Komşu | çapraz-platform test kavramı | Platform tasarımı | görev yasağı |
| E-18 | Vault `.ai/.decisions` | Dokunulmaz | ADR okuma | Yazma | görev yasağı |
| E-19 | `*.php`, `*.js`, `*.css`, `*.sql` | Dokunulmaz | Okuma (kanıt) | Yazma | görev yasağı |
| E-20 | `.ai/architecture/index.md` | Dokunulmaz | bu dalda erişim yok | Yazma | görev yasağı |

### §E.2 Komşu sınırı (testing →) — bağlayıcı matris

| Komşu | Test sorumluluğu bu dalda mı? | Bu dalın paylaştığı | Bu dalın **paylaşmadığı** |
|---|---|---|---|
| `K18-testing` | Hayır — uygulama test **stratejisi** orada | test adlarının sözlüğü | strateji, öncelik, flaky, contract, eşik politikası |
| `isletim-sistemi-kernel` | Kısmen — çekirdek/sürücü testi bu dalın `cekirdek-test` kolu | test yöntemi (KUnit, kselftest) | çekirdek tasarımı, sürücü kodu |
| `isletim-sistemi-core` | Hayır — süreç testi orada | test edilecek süreç tanımı | süreç davranışı, zamanlama ayrıntısı |
| `isletim-sistemi-plan` | Hayır — zamanlama testi orada | doğrulama sinyali (metric) | zamanlayıcı politikası |
| `isletim-sistemi-security` | Hayır — güvenlik testi orada | secret tarama kapısı kavramı | güvenlik mimarisi, OWASP kontrolleri |
| `isletim-sistemi-observability` | Hayır — gözlem doğrulama orada | log/metric okuma sinyali | alarm eşiği, retention |
| `isletim-sistemi-turu` | Hayır — platform test matrisi orada | platform etiketi | platform API ayrıntısı |
| `K17-devops` | Hayır — CI altyapısı orada | workflow dosyası referansı | runner, deploy, rollback, saklama |

## §F — Açık sorular

### §F.1 T1 — OS/çekirdek test kapsamı

| Alan | Değer |
|---|---|
| Soru | KUnit/kselftest hedef mimaride kapsam mı, yoksa yalnız uygulama testi mi? |
| Disk kanıtı (olumsuz) | Repo genelinde `kunit` / `kselftest` araması → yazılım kodunda 0 isabet |
| Web kanıtı | `docs.kernel.org/dev-tools/kunit` · `docs.kernel.org/kselftest` erişilebilir (hedef referans) |
| CoreMusic bağlamı | CoreMusic'te Linux çekirdek kodu **yok**; T1 bu yüzden "hedef mimari referansı mı, kapsam mı?" sorusudur |
| Bağlantılı alt-dal | `cekirdek-test/` — bu dalın tek sahibi |
| Durum | **[UNKNOWN] — kapatılmaz** |
| Kapanış koşulu | Hedef mimari kararı (ADR) ile OS testinin kapsama alınacağına dair açık kayıt |
| Sahibi | ADR yazarı (orkestratör) — bu dal kararı veremez |

### §F.2 T2 — Coverage hedefi (phpunit.xml disk kanıtıyla daraltıldı)

| Alan | Diskteki değer | Satır |
|---|---|---|
| Testsuite tanımları | Unit, Api, Repository, Events, Security, Middleware, Component, OAuth = 8 | `shared/phpunit.xml:9-32` |
| Testsuites bloğu kapanışı | `</testsuites>` | `shared/phpunit.xml:33` |
| Coverage kaynak dizini | `<source><include><directory suffix=".php">src</directory></include></source>` | `shared/phpunit.xml:34-38` |
| Coverage **eşik** (`min` / `coverage` bayrağı) | **YOK** — dosyada `<coverage>` bloğu bulunmuyor | `shared/phpunit.xml:1-39` tam okuma |
| Bootstrap | `vendor/autoload.php` | `shared/phpunit.xml:4` |
| Önbellek | `.phpunit.cache` | `shared/phpunit.xml:7` |
| stopOnFailure | `false` | `shared/phpunit.xml:6` |
| CI'da coverage çalıştırılıyor mu? | Hayır — `ci.yml` PHPUnit adımında coverage bayrağı yok | `.github/workflows/ci.yml:104-108` |
| JS tarafı coverage | Var, fakat `continue-on-error: true` (yumuşak) | `.github/workflows/ci.yml:167,175-176` |
| Karşılaştırma | `auth.coremusic.net/phpunit.xml:28-32` `<coverage><report><text outputFile="php://stdout"/>` tanımlı; eşik yine yok | disk |
| failOnRisky / failOnWarning | auth phpunit.xml'de `true` | `auth.coremusic.net/phpunit.xml:6-7` |
| Soru | Hedef coverage yüzdesi (ör. %80) bağlayıcı mı? | — |
| Durum | **[UNKNOWN] — açık kalır**; bu dosya yalnızca diskte **ne olduğunu** raporlar, hedef **uydurulmaz** |
| Not | Eşik politikasının sahibi `K18-testing/coverage/`; buraya taşınmaz |

### §F.3 Kapanış kuralları

| Kural | Ayrıntı |
|---|---|
| Uydurma yasak | T1/T2 için sayısal hedef yazılmaz; `UNKNOWN` etiketi korunur |
| Daraltma serbest | Disk kanıtı eklendikçe soru daralır ama `open_questions` alanı kalır |
| Sahiplik | Eşik kararı → `K18-testing`; OS test kapsamı kararı → ADR |
| Yeniden ölçüm | Her vault revizyonunda `phpunit.xml` ve `ci.yml` yeniden okunur |
| Çelişki | Disk ile vault çelişirse disk kazanır, soru yeniden açılır |
| Etiket | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`, bilinmeyen `UNKNOWN` |

## §G — Ölçüm ve yeniden ölçüm

| # | Ölçüm | 2026-10-13 değeri | Yeniden ölçüm koşulu |
|---|---|---|---|
| G-01 | Kök testing dosya sayısı | 4 | alt-dal yazımı tamamlanınca 16 |
| G-02 | `shared/phpunit.xml` satır sayısı | 39 | dosya değişince |
| G-03 | Testsuite sayısı | 8 | `phpunit.xml` değişince |
| G-04 | `shared/tests` dosya sayısı | 44 (22 `.php` + 6 `CLAUDE.md`/`README.md` + 6 Fixtures + 10 alt dizin girdisi) | test eklenince |
| G-05 | Test dizini sayısı | 9 (Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit) | dizin eklenince |
| G-06 | Workflow dosyası sayısı | 2 (`ci.yml` 203 satır · `secret-scan.yml` 31 satır) | workflow eklenince |
| G-07 | CI job sayısı | 5 (php-lint, php-test, composer-audit, js-test, js-coverage, js-e2e = 6 iş tanımı, 5 isimli job + 1 matrisli) | `ci.yml` değişince |
| G-08 | Sert kapı (fail bloke eder) | php-lint, php-test, composer-audit, js-test | `continue-on-error` değişince |
| G-09 | Yumuşak kapı (continue-on-error) | js-coverage, js-e2e | eşik kararı ile sertleşebilir |
| G-10 | OS test aracı isabeti | 0 (`kunit`, `kselftest`) | çekirdek kodu eklenirse |
| G-11 | URL envanteri (öncesi) | 2097 benzersiz URL | yeni revizyon başında |
| G-12 | K18-testing'e girilen dosya | 0 | her zaman 0 |

*2026-10-14 yeniden ölçümü (G-01 · disk sayımı):* kök testing dosya sayısı **4 → 10**; bu dal toplamı **22 dosya** (12 alt-dal + 10 kök). G-01'in "16" hedefi güncellenmiştir: yeni kök dosya `isletim-sistemi-testing-cross-platform-ornekleri.md` eklendi (bkz. §H H-17). G-02..G-12 değerleri bu tarihte yeniden ölçülmemiştir — değişiklik yok varsayılmaz, yalnız G-01 tazelendi.

## §H — Dosya-kademe-group matrisi

| # | Dosya | Kademe | group | node_type | Sahip |
|---|---|---|---|---|---|
| H-01 | `index.md` | Kademe-2 | mimari | subdomain | bu dal |
| H-02 | `isletim-sistemi-testing-kapsam.md` | Kademe-2 | mimari | module | bu dal |
| H-03 | `isletim-sistemi-testing-dogrulama.md` | Kademe-2 | kanit | module | bu dal |
| H-04 | `isletim-sistemi-testing-ornekler.md` | Kademe-2 | kod | module | bu dal |
| H-05 | `cekirdek-test/index.md` | Kademe-3 | mimari | subdomain | bu dal |
| H-06 | `cekirdek-test/cekirdek-test-anayasa.md` | Kademe-4 | mimari | module | bu dal |
| H-07 | `cekirdek-test/cekirdek-test-surucu.md` | Kademe-4 | mimari | module | bu dal |
| H-08 | `cekirdek-test/cekirdek-test-ornekler.md` | Kademe-4 | kod | module | bu dal |
| H-09 | `uygulama-testi/index.md` | Kademe-3 | mimari | subdomain | bu dal |
| H-10 | `uygulama-testi/uygulama-testi-anayasa.md` | Kademe-4 | mimari | module | bu dal |
| H-11 | `uygulama-testi/uygulama-testi-surucu.md` | Kademe-4 | mimari | module | bu dal |
| H-12 | `uygulama-testi/uygulama-testi-ornekler.md` | Kademe-4 | kod | module | bu dal |
| H-13 | `ci-ve-kalite-kapisi/index.md` | Kademe-3 | mimari | subdomain | bu dal |
| H-14 | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa.md` | Kademe-4 | mimari | module | bu dal |
| H-15 | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu.md` | Kademe-4 | mimari | module | bu dal |
| H-16 | `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-ornekler.md` | Kademe-4 | kod | module | bu dal |
| H-17 | `isletim-sistemi-testing-cross-platform-ornekleri.md` | Kademe-2 | kod | module | bu dal (2026-10-14 eklendi) |

## §I — Kapsam içi riskler

| # | Risk | Etki | Azaltma | Durum |
|---|---|---|---|---|
| R-01 | Kapsam kayması: strateji bu dala sızmaya çalışır | K019 ile çakışma | §A.2 X-01..X-09 bağlayıcı | izleniyor |
| R-02 | Uydurma coverage eşiği yazılması | Zero-hallucination ihlali | T2 `UNKNOWN` olarak açık kalır | izleniyor |
| R-03 | `ci.yml` okunurken mojibake'nin örnek olarak kopyalanması | Kirli örnek | ASCII temiz satırlar seçilir | azaltıldı |
| R-04 | Satır numarası uydurma | Kanıt zinciri kırılır | `Select-Object -Index (N-1)` ile doğrulanır | azaltıldı |
| R-05 | Alt-dal adının değiştirilmesi | Link kırıkları | Adlar sabit (§B) | kapatıldı |
| R-06 | `K019-testing` dosyasına yazma | yetki ihlali | beyazliste kısıtı | kapatıldı |
| R-07 | Paralel 7 iskelet ajanıyla çakışma | dosya yarışı | beyazliste dışı yazım yok | kapatıldı |
| R-08 | `parent:` alanı kullanımı (eski şema) | frontmatter tutarsızlığı | tüm dosyalarda `parent_id` | azaltıldı |
| R-09 | URL envanteri ile kesişen kaynak kullanımı | kanıt tekrarı | envanter öncesi çıkarılır | azaltıldı |
| R-10 | QEMU/KUnit hedefinin repo'da karşılığı sanılması | Yanlış envanter | T1 `UNKNOWN` + §C'de "yok" | izleniyor |

## §J - Etkileşim ve geçiş kaydı (detay)

### §J.1 Artefakt girişi/çıkışı

| # | Artefakt | Kaynak | Hedef | Biçim |
|---|---|---|---|---|
| A-01 | Test stratejisi | bu dal | K019-test (Faz-A) | markdown |
| A-02 | Kalite kapısı tanımı | bu dal | K17-devops | workflow referansı |
| A-03 | Test komutu | uygulama-testi | ci-ve-kalite-kapisi | komut |
| A-04 | QEMU boot komutu | cekirdek-test | ci-ve-kalite-kapisi | komut |
| A-05 | Doğrulama kanıtı | dogrulama | bu dal `validation` | `[]` (henüz yok) |
| A-06 | Risk kaydı | riskler | bu dal `open_questions` | `[UNKNOWN]` |
| A-07 | Web iddiası | web-arastirma | dogrulama §1 | URL + tarih + durum |
| A-08 | Kod örneği | kod-ornekleri/ornekler | surucu | Dosya + Satır |
| A-09 | Sürücü örneği | surucu | ornekler §1 | Dosya + Satır |
| A-10 | Sınır matrisi | kapsam §E.2 | 3 alt-dal `index.md` | tablo |

### §J.2 Test seviyesi - sorumluluk eşlemesi

| Seviye | Sahip alt-dal | Örnek | Çıktı |
|---|---|---|---|
| Unit (PHP) | uygulama-testi | RouteRegistryTest | assert |
| Integration (PHP) | uygulama-testi | DB izolasyonlu test | assert |
| Contract | uygulama-testi | API sözleşmesi | assert |
| Unit (kernel) | cekirdek-test | KUnit case | KUnit report |
| Integration (kernel) | cekirdek-test | QEMU + modül | dmesg/log |
| System | cekirdek-test | QEMU boot senaryosu | exit code |
| Driver | cekirdek-test | sanal cihaz | dmesg/log |
| Perf ölçüm | cekirdek-test | latency ölçümü | sayısal rapor |
| Lint | ci-ve-kalite-kapisi | PHPStan | exit code |
| Type check | ci-ve-kalite-kapisi | PHPStan baseline | exit code |
| Security gate | ci-ve-kalite-kapisi | gitleaks | exit code |
| Secret scan | ci-ve-kalite-kapisi | secret-scan.yml | exit code |

### §J.3 Kapı giriş/çıkışları

| Kapı | Giriş | Çıkış başarılı | Çıkış başarısız | Sahip |
|---|---|---|---|---|
| G-1 lint | kaynak | exit 0 | job fail | ci-ve-kalite-kapisi |
| G-2 type | kaynak + baseline | exit 0 | job fail | ci-ve-kalite-kapisi |
| G-3 unit | phpunit.xml | exit 0 | job fail | uygulama-testi |
| G-4 contract | API sözleşmesi | exit 0 | job fail | uygulama-testi |
| G-5 secret | repo | clean | job fail | ci-ve-kalite-kapisi |
| G-6 kernel unit | KUnit | pass report | boot fail | cekirdek-test |
| G-7 boot | QEMU | init reached | timeout | cekirdek-test |
| G-8 perf | ölçümler | rapor üretilir | UNKNOWN | cekirdek-test |
| G-9 coverage | ağaca göre | UNKNOWN | UNKNOWN | K019 (erişim yok) |
| G-10 artefakt | job çıktısı | artifact | kayıp | ci-ve-kalite-kapisi |

### §J.4 Geçiş senaryoları

| Senaryo | Tetik | Geçiş | Sonuç |
|---|---|---|---|
| S-01 | yeni test ekle | uygulama-testi → kapı | G-3 |
| S-02 | yeni workflow ekle | kapı → K17-devops | devralınır |
| S-03 | kernel testi ekle | cekirdek-test → kapı | G-6/G-7 |
| S-04 | secret sızıntısı | kapı → security dalı | eskalasyon |
| S-05 | coverage sorusu | herhangi → K019 | erişim yok |
| S-06 | KASAN bulgusu | cekirdek-test → kernel | eskalasyon |
| S-07 | performans regresyonu | cekirdek-test → plan | eskalasyon |
| S-08 | web iddiası değişimi | web-arastirma → dogrulama | yeniden ölçüm |
| S-09 | dosya yeniden adlandırma | yasak (§B) | durdurulur |
| S-10 | mojibake örneği | ornekler §1 notu | ASCII satıra düşülür |

## §K - Doğrulama durumu tablosu (2026-10-13)

| Bölüm | Beklenen kanıt | Durum |
|---|---|---|
| §A kapsam | dal envanteri | FAIL — disk kanıtı yok |
| §A.2 sınır | K019 ayrımı | FAIL — disk kanıtı yok |
| §B alt-dallar | 3 dal × 4 dosya | FAIL — üretim anında kanıt yok |
| §C ayrım matrisi | OS/uygulama/K019 | FAIL — disk kanıtı yok |
| §D bağımlılık | grafik | FAIL — disk kanıtı yok |
| §E komşular | 8 dal sınırı | FAIL — disk kanıtı yok |
| §E.2 matris | komşu sınır matrisi | FAIL — zorunlu matris, kanıt yok |
| §E.3 doküman | başlıklar | FAIL — yok |
| §F açık sorular | T1/T2/T3/T5 | UNKNOWN — cevap yok |
| §G ölçüm | tekrar ölçüm | FAIL — ölçüm altyapısı yok |
| §G.2 eşik | coverage | UNKNOWN — uydurmak yasak |
| §H group matrisi | dosya - group | FAIL — disk kanıtı yok |
| §I riskler | R-01..R-10 | izleniyor — kanıt yok |
| §J.1 artefakt | 10 geçiş | FAIL — doğrulanmadı |
| §J.2 seviye | 12 eşleme | FAIL — doğrulanmadı |
| §J.3 kapı | 10 kapı | FAIL — disk kanıtı yok |
| §J.4 senaryo | 10 senaryo | FAIL — doğrulanmadı |
| T1 kapsam | cevap | UNKNOWN |
| T2 eşik | sayısal değer | UNKNOWN |
| Alt-dal toplamı | 9 dosya | üretimde — doğrulanmadı |

## §L - Alt-dal bazında sorumluluk ve risk kaydı (45 satır)

| # | Alt-dal | Dosya | Sahip olduğu sorumluluk | Ana risk |
|---|---|---|---|---|
| L-01 | cekirdek-test | index | dal kökü ve sınır | kapsam kayması |
| L-02 | cekirdek-test | anayasa | seviye ve strateji | çerçeve uydurma |
| L-03 | cekirdek-test | surucu | QEMU/KUnit sürücüsü | repo karşılığı sanılması |
| L-04 | cekirdek-test | ornekler | kalıp ve anti-pattern | kurgu kod |
| L-05 | uygulama-testi | index | dal kökü ve sınır | K019 ile karışma |
| L-06 | uygulama-testi | anayasa | piramit ve PHPUnit | eşik uydurma |
| L-07 | uygulama-testi | surucu | phpunit.xml sürücüsü | satır numarası uydurma |
| L-08 | uygulama-testi | ornekler | gerçek repo örneği | mojibake örneği |
| L-09 | ci-ve-kalite-kapisi | index | dal kökü ve sınır | K018 ile karışma |
| L-10 | ci-ve-kalite-kapisi | anayasa | workflow yapısı | tetikleyici uydurma |
| L-11 | ci-ve-kalite-kapisi | surucu | ci.yml sürücüsü | mojibake bloklama |
| L-12 | ci-ve-kalite-kapisi | ornekler | kapı kalıpları | örnek sahteliği |
| L-13 | kök | kapsam | sınır ve soru | T1/T2 uydurma |
| L-14 | kök | dogrulama | kanıt protokolü | doğrulanmamış PASS |
| L-15 | kök | ornekler | senaryo | dolgu |
| L-16 | kök | anayasa | test anayasası | çelişki |
| L-17 | kök | surucu | orkestrasyon | tekrar |
| L-18 | kök | riskler | risk kaydı | eksik risk |
| L-19 | kök | web-arastirma | URL iddiaları | envanter kesişimi |
| L-20 | kök | kod-ornekleri | kod referansı | uydurma satır |
| L-21 | kök | index | dal kökü | yanlış children |
| L-22 | hepsi | frontmatter | 10 anahtar | `parent:` sızıntısı |
| L-23 | hepsi | group | 3 değer | alıntılı group |
| L-24 | hepsi | children | dosya sayısı | eksik çocuk |
| L-25 | hepsi | depends_on | boş | gereksiz bağımlılık |
| L-26 | hepsi | evidence | boş | kanıtsız iddia |
| L-27 | hepsi | validation | boş | kanıtsız onay |
| L-28 | hepsi | open_questions | `[UNKNOWN]` | cevap uydurma |
| L-29 | hepsi | Authority satırı | son satır | eksik kapanış |
| L-30 | hepsi | §N başlıkları | sıra | atlanmış bölüm |
| L-31 | hepsi | tablolar | boş hücre yok | boş hücre |
| L-32 | hepsi | kod blokları | ASCII | mojibake |
| L-33 | hepsi | URL | yeni | envanter kesişimi |
| L-34 | hepsi | tarih | 2026-10-13 | eski tarih |
| L-35 | hepsi | dosya adı | sabit | yeniden adlandırma |
| L-36 | hepsi | uzantı | `.md` | `.php` yazımı |
| L-37 | hepsi | K019 | 0 satır | yetki ihlali |
| L-38 | hepsi | beyazliste | 16 yol | dışı yazım |
| L-39 | hepsi | commit | yok | yetkisiz commit |
| L-40 | hepsi | imla | 7 token | imla düzeltmesi |
| L-41 | hepsi | satır indeksi | birebir | uydurma numara |
| L-42 | hepsi | dosya uzunluğu | ≥500 / 40-100 | kısa dosya |
| L-43 | hepsi | §E.2 matrisi | 4 dosya | eksik matris |
| L-44 | hepsi | komşu sınır | 8 satır | eksik sınır |
| L-45 | hepsi | dolgu | 0 | dolgu cümle |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
