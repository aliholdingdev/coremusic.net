---
title: "K019-testing — Master (Domain Anayasa)"
node_id: K019-testing
node_type: domain-master
status: draft
authority: "Derived — SSOT: v2 spec §2.19 + ADR-097 (draft)"
updated: 2026-10-17
version: 1.1.0
group: mimari
---


# K019-testing — testing-master.md

## §1 Rol (ELI15)

K019-testing, **her şeyin gerçekten çalıştığını kanıtlayan** alandır: kod yazıldıktan sonra
çalışıp çalışmadığını, bozulup bozulmadığını, güvenli olduğunu ve ses kalitesini ölçer.
Test, "yazayım da çalışır herhalde" değil; **çalıştığını kanıtlayan, tekrarlanabilir gözlemdir**.
Bu domain bir özellik değil, **güven kapısıdır** — CI (K018) bu domainin ölçtüğünü kapıya uygular.

## §2 Kapsam / Sınır (ne DEĞİL)

| Kapsam (bu domain) | Kapsam DIŞI |
|---|---|
| Test stratejisi, test pyramid, coverage hedefi | Testlerin hangi pipeline'da çalıştırıldığı, deploy → **K018-devops** (ci.yml jobları orada tanımlı) |
| Unit / integration / contract / E2E testleri | Ajan davranışının testi (agent-testing) → **K015-AI**/agent-testing |
| Regression / güvenlik / performans testi | Sistem auth/CSRF/CSP politikasının kendisi → **K009-security** (burada: o politikanın *sınanması*) |
| Audio-quality / hardware-validation ölçümü | Ses motorunun kendisi (DSP/ASIO) → **K003-audio** / **K004-dsp** (burada: ölçülen taraf) |
| Build-verification (derleme/test bütünlüğü) | Build sisteminin kurulumu, pipeline → **K018-devops**/build |

**KURAL (registry §6 — tescilli ayrım):** K018 = testleri **koşturan** altyapı (CI job) ·
K019 = testlerin **neyi, nasıl ölçtüğü** (strateji + senaryo). İkisi ayrı domaindir;
kapsam kayması tespit edilirse ADR ile düzeltilir, sessiz taşınmaz.

## §2.1 Test Pyramid (strateji — vault beyanı + disk uyumu)

> Pyramident yorumu bu turda **bağımsız ölçülmedi**; alttaki dağılım diskten **sayılan** test
> dosyalarından türetildi. Hedef oran (.ai/AGENTS.md §16 coverage ≥80%) vault beyanıdır.

| Kat | Test türü | Diskteki karşılığı | Sayı (2026-10-12) |
|---|---|---|---|
| Taban (çok) | Unit | shared/tests/Unit · tests/Component · tests/OAuth · tests/Security (bileşen altı) | 55 PHP dosyasının büyük kısmı (2026-10-17 düzeltmesi — §4.1) |
| Orta | Integration | tests/Api · tests/Middleware · tests/Repository · tests/Events | Api 8 + Middleware + Repository + Events |
| Tepe (az) | E2E (Playwright) | assets.coremusic.net/tests/e2e/ — 1 spec (player-info.spec.ts) + playwright-report/ + test-results/ izi | 1 spec (kapsam dar) |

## §3 Kademe-2 Çocuk Tablosu (11 çocuk — 2026-10-17; v2 spec diskte YOK ⚠️)

| # | Çocuk | Rol (1 cümle) | Kanıt durumu |
|---|---|---|---|
| 1 | unit/ | Tek bir birimin (sınıf/metot) izole davranışı | ✅ kanıtlı (40 *Test.php shared + 6 auth + 3 api + 6 home) |
| 2 | integration/ | Birimler arası / DB / middleware birlikte davranışı | ✅ kanıtlı (tests/Api · tests/Middleware · tests/Repository) |
| 3 | contract/ | API sözleşmesi (request/response şekli) uyumu | ✅ kanıtlı (tests/Api/ApiResponseTest · RouteTableTest · VersioningTest) |
| 4 | e2e/ | Tarayıcı akışı (Playwright) | 🔶 kısmi — 1 spec (assets.coremusic.net/tests/e2e/player-info.spec.ts) + config + report izi + modül dokümanı (513 satır) |
| 5 | flaky/ | Kararsız test tespiti, retry, karantina | ⚠️ kanıt bekliyor — retry kanıtı var (playwright.config.ts retry:2), karantina/flaky ölçümü YOK; modül dokümanı 603 satır |
| 6 | regression/ | Daha önce çalışanın bozulmadığını doğrulama | ⚠️ kanıt bekliyor (ayrı regression suite görünmüyor) |
| 7 | security/ | Güvenlik testi (auth bypass, injection, middleware) | ✅ kanıtlı (tests/Security · tests/Middleware · tests/Api/*SecurityTest) |
| 8 | performance/ | Yük/gecikme/bellek ölçümü | ⚠️ kanıt bekliyor (ayrı perf testi görünmüyor) |
| 9 | audio-quality/ | Ses kalitesi ölçümü (SNR/THD latency) | ⚠️ kanıt bekliyor (ölçüm yok; hedef §16 vault beyanı) |
| 10 | hardware-validation/ | Donanım üzerinde doğrulama (DAC/ADC/ASIO) | ⚠️ kanıt bekliyor (fiziksel test yok) |
| 11 | build-verification/ | Derleme + test bütünlüğü (lint/type/coverage) | ✅ kanıtlı (ci.yml php-lint · phpstan ^1.10 · js-coverage) |

## §4 Teknoloji / Ekosistem (disk kanıtları — ölçüm 2026-10-12)

| Ekosistem | Kanıt (gerçek dosya yolu) | Durum |
|---|---|---|
| PHP test runner | phpunit **^10.5** — 4 composer.json: shared · auth.coremusic.net · api.coremusic.net · home.coremusic.net | ✅ IMPLEMENTED |
| PHP static analysis | phpstan **^1.10** — yalnız shared/composer.json (subdomainlerde YOK); güncel sürüm PHPStan **2.x** (2.0: 2024-11-11 — testing-dogrulama.md §4) | 🔶 kısmi (sürüm geride) |
| PHPUnit yapılandırma | phpunit.xml — **4 dosya**: shared · auth.coremusic.net · api.coremusic.net · home.coremusic.net (media.coremusic.net'te YOK) | ✅ IMPLEMENTED |
| PHP test dosyaları | shared/tests/**/*Test.php = **40** · auth.coremusic.net/tests/ = **6** · api.coremusic.net/tests/ = **3** · home.coremusic.net/tests/ = **6** → **toplam 55** | ✅ IMPLEMENTED |
| JS test runner | vitest **^5.0.3** + @vitest/coverage-v8 ^5.0.3 + jsdom ^30.1.1 — root package.json | ✅ IMPLEMENTED |
| JS test dosyaları | assets.coremusic.net/ = **9 .spec.js + 1 .spec.ts** (10 dosya) | ✅ IMPLEMENTED |
| E2E test | @playwright/test **^1.63.0** + playwright ^1.63.0 + axe-playwright ^1.0.0 — root package.json | ✅ IMPLEMENTED (araç) |
| E2E kanıt izi | playwright-report/index.html + test-results/.last-run.json → bir kez koşturulduğu izlenimi | 🔶 iz var, spec dosyası e2e/ altında YOK |
| CI test kapıları | .github/workflows/ci.yml job: php-lint · php-test [Unit] · composer-audit · js-test · js-coverage (soft) · js-e2e (Playwright, soft) | ✅ IMPLEMENTED (dosya) |
| Secret tarama | .gitleaks.toml (kök) + .github/workflows/secret-scan.yml (gitleaks-action@v2) | ✅ IMPLEMENTED |
| Coverage hedefi | .ai/AGENTS.md §16: QA "Test coverage ≥80%, flaky test %0" → hedef ≥80% | 🔶 VAULT BEYANI (ci.yml'da coverage gate soft/continue-on-error — bkz. açık soru) |

### §4.1 Test Dosyası Envanteri (dizin bazlı — 2026-10-12 sayımı)

| Test dizini | Dosya uzantısı | Sayı | Not |
|---|---|---|---|
| shared/tests/Api | *Test.php | 8 | ApiResponse · RouteTable · Versioning · GatewayMethodRouting · RequestValidationMiddleware · ResponseNormalizationMiddleware · ApiPipelineSecurity · Dto |
| shared/tests/Component | *Test.php | (40 içinde) | birim/bileşen |
| shared/tests/Events | *Test.php | (40 içinde) | event |
| shared/tests/Fixtures | (fixture) | — | test veri kütüphanesi |
| shared/tests/Middleware | *Test.php | (40 içinde) | middleware entegrasyonu |
| shared/tests/OAuth | *Test.php | (40 içinde) | OAuth akışı |
| shared/tests/Repository | *Test.php | (40 içinde) | DB repository |
| shared/tests/Security | *Test.php | (40 içinde) | güvenlik |
| shared/tests/Unit | *Test.php | (40 içinde) | saf birim |
| auth.coremusic.net/tests | *Test.php | 6 | auth akış testi |
| api.coremusic.net/tests | *Test.php | 3 | api testi |
| home.coremusic.net/tests | *Test.php | 6 | home akış testi (2026-10-17 eklendi — testing-dogrulama.md §3-ç1) |
| assets.coremusic.net | .spec.js / .spec.ts | 10 | vitest JS testi |
| **Toplam PHP** | *Test.php | **55** | shared 40 + auth 6 + home 6 + api 3 (2026-10-17 düzeltmesi — 49 → 55) |
| **Toplam JS** | .spec.* | **10** | assets 9 .spec.js + 1 .spec.ts |

### §4.2 CI Kapı → Test Türü Eşlemesi (.github/workflows/ci.yml — disk kanıtı)

| CI job | Test türü (K019 karşılığı) | Kapı sertliği |
|---|---|---|
| php-lint | build-verification (sözdizimi) | sert |
| php-test [Unit] | unit (+ integration alt kümesi) | sert |
| composer-audit | security (bağımlılık açığı) | sert |
| js-test | unit (vitest) | sert |
| js-coverage | build-verification (coverage) | **soft** (continue-on-error) ⚠️ |
| js-e2e (Playwright) | e2e | **soft** (continue-on-error) ⚠️ |
| secret-scan.yml (gitleaks) | security (sır sızıntısı) | ayrı workflow |

> **Not:** regression/ · performance/ · audio-quality/ · hardware-validation/ için bu turda
> **hiçbir** disk kanıtı toplanmadı — uydurma yok, boş bırakıldı (bkz. §6/§7).

## §5 Bağımlılık Özeti (etiketli — detay: testing-bagimliliklar.md)

| Yön | Hedef | Etiket | Gerekçe |
|---|---|---|---|
| depends_on | K018-devops | kanıtlı | testler ci.yml joblarında koşturulur (ci.yml diskte) |
| depends_on | K003-audio · K004-dsp | planlanan | audio-quality/hardware-validation ölçtüğü sisteme bağımlı |
| depends_on | K009-security | kanıtlı | security testi auth/middleware yüzeyini yoklar (tests/Security, tests/Middleware) |
| depends_on | K007-data | kanıtlı | integration test DB/Repository katmanına dokunur (tests/Repository) |
| used_by | tüm üretim domainleri (K003–K015) | kanıtlı | 55 PHP test + 10 JS spec bu domainin senaryolarıdır (2026-10-17 düzeltmesi) |
| used_by | K015-AI/agent-testing | planlanan | ajan davranışı testi bu stratejinin uzantısı (kademe-3) |

## §6 Doğrulama Özeti (detay: testing-dogrulama.md)

- ✅ **Doğrulandı (disk):** unit · integration · contract · security · build-verification (§4 yolları).
- 🔶 **Kısmi:** e2e (1 spec kanıtlı — kapsam dar; modül dokümanı 513 satır) · flaky (retry kanıtı var, karantina YOK) · build-verification (coverage gate soft).
- ⚠️ **Kanıt bekliyor:** regression · performance · audio-quality · hardware-validation.
- **ÖNEMLİ:** "CI geçti" iddiası **doğrulanmadı** — .git/HEAD ENOENT (git deposu yok/bozuk),
  bu yüzden yeşil build kanıtı yok. Vault §25.2 de "CI'nın geçtiği doğrulanmadı" der.
- Web doğrulama: **2026-10-17 turunda yapıldı** — 3 bekleyen iddia sonuçlandırıldı (PHPUnit --testsuite · PHPStan 2.x · Playwright 1.63) → testing-dogrulama.md §4.

## §7 Açık Sorular (⚠️ VERIFICATION REQUIRED)

| # | Soru | Etkilenen çocuk |
|---|---|---|
| 1 | Coverage gate neden soft (continue-on-error)? ≥80% hedefi bağlayıcı mı? | build-verification/ |
| 2 | ~~E2E spec dosyaları nerede?~~ KAPANDI (2026-10-17): assets.coremusic.net/tests/e2e/player-info.spec.ts = 1 spec — açık kalan: kapsam dar, spec ailesi büyütülecek mi? | e2e/ |
| 3 | Regression suite ayrı mı, yoksa mevcut unit/integration mı kapsıyor? | regression/ |
| 4 | Performance testi için hedef metrik/ekipman tanımlı mı? | performance/ |
| 5 | Audio-quality ölçüm hedefi (SNR/THD+N) laboratuvar koşulu mu, CI'da mı? | audio-quality/ |
| 6 | hardware-validation hangi fiziksel rig üzerinde? (PCM3168A/AK4458/ASIO) | hardware-validation/ |
| 7 | media.coremusic.net neden phpunit.xml'siz? Test kapsamı dışında mı? | unit/ · integration/ |
| 8 | phpstan yalnız shared'de — subdomainler static analysis dışında mı? | build-verification/ |

## §8 Kaynaklar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.19 (çocuk listesi birebir) |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme · §6 açık sorular |
| Registry | .ai/architecture/index.md §5 satır 19 (K019-testing · testing-master.md) |
| Disk (2026-10-12) | shared/tests · *.coremusic.net/tests · phpunit.xml ×4 · package.json · ci.yml · .gitleaks.toml |
| Vault | .ai/AGENTS.md §16 (coverage ≥80% hedef — vault beyanı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode · 2026-10-12 (ilk) → 2026-10-17 (rev. 1.1.0: §2.1/§3/§4/§4.1/§5/§6/§7 — e2e/flaky kaydı · 49→55 · web doğrulama)
