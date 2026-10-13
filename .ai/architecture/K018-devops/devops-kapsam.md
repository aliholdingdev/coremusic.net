---
node_id: K018-devops/devops-kapsam
node_type: module
status: draft
parent_id: K018-devops
children: []
depends_on:
  - K019-testing
  - K009-security
  - K017-observability
evidence:
  - ".github/workflows/ci.yml · Satır 8-203 — name: CI Pipeline (6 job, 203 satır)"
  - ".github/workflows/secret-scan.yml · Satır 5-31 — name: Secret Scan (gitleaks job, 31 satır)"
  - ".gitleaks.toml · Satır 6-18 — title = CoreMusic gitleaks (allowlist, 18 satır)"
  - ".github/AGENTS.md · Satır 55-63 — 3.1 Dosya Sahipliği Tablosu (5 dosya sahipliği)"
  - ".workflows/deployment.md · Satır 126-199 — Aşama 1-8 dağıtım onay akışı (281 satır)"
  - "K018-devops dizin ölçümü (2026-10-13): 11 dizin · 19 dosya · 3.866 satır"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde dosya-içinde birebir okunarak doğrulandı"
  - "Web tablosu 1 URL · envanter (2640) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç · boş hücre 0 · mojibake 0"
open_questions:
  - "CI gerçekten geçti mi? GitHub run kanıtı diskte değil (master §7/1)"
  - "build/ v2 çocuğu diskte yok; karşılığı pipeline/ mı? (master §A.1/2)"
  - "cd/ environments/ deploy job + environment: bloğu yok — model ne? (master §7/3)"
group: mimari
---


# K018-devops — devops-kapsam.md

## §1 Kapsam Amacı (ELI15)

Bu dosya K018-devops domaininin **sınırlarını** çizer: kodun depoya girişinden yayına kadarki
yol (pipeline · ortam · yayın · geri alma) bu domaindedir; testlerin **kendisi** K019'dadır,
K018 onları **koşar**. Kapsam koddan önce yazılır; kapsam dışı yüzeye dokunma revert sebebidir
(kök AGENTS.md §3). Bu dosya yalnız **tanım** taşır — uygulama master'da.

## §2 Kapsam Sınırı (bu domain · bu domain DEĞİL)

| # | Kapsam (K018 üretir/yürütür) | Kapsam DIŞI (domain · gerekçe) |
|---|---|---|
| 1 | CI kapısı: lint · test · audit koşumu | Testlerin **tasarımı/içeriği** → **K019-testing** (K018 koşar) |
| 2 | CD · ortam · deployment · rollback | Sistem logu/metrik/alert → **K017-observability** (uyarı K017, aksiyon K018) |
| 3 | Secret taraması (GitLeaks) | Auth/encryption **politikası** → **K009-security** (iş birliği) |
| 4 | Workflow dosyaları sahipliği (*.yml) | Ajan boot/hooks → **K016-AI** (hook'lar harness'da) |
| 5 | Artifact saklama · cache · konteyner imaj | Artifact **içeriği/ürünü** → ilgili üretim domaini |
| 6 | Sürüm notu · release akışı | Sürüm **kararı/numarası** politikası → kullanıcı + ADR |
| 7 | Altyapı testi (infrastructure-testing) | Altyapı **kurulumu** → K000-isletim-sistemi / cloud |
| 8 | Branch/commit hijyeni (source-control kuralı) | Depo **fiziksel** barındırma/yedek → K000 / GitHub |
| 9 | build: composer/npm kurulum + statik analiz | Kod **derleme mantığı** (PHP derlenmez; JIT yok) → production |
| 10 | Pipeline hızlandırma · cache key | Pipeline **çalıştırma ortamı** (runner donanımı) → GitHub |

**Sınır ilkeleri:**

| # | İlke | Açıklama | Sonuç |
|---|---|---|---|
| 1 | Yol ≠ içerik | K018 yolu (pipeline) kurar, test içeriği K019 | Test ekleme K019 yetkisi |
| 2 | Uyarı ≠ aksiyon | Uyarı K017, aksiyon (deploy/rollback) K018 | Sınır çekilmiş entegrasyon |
| 3 | Tarama ≠ politika | GitLeaks K018'de koşar, kural Security | Config eklemek ADR ister |
| 4 | Dosya sahipliği tek el | ci.yml = DevOps + QA; secret-scan = Security + DevOps | Onaysız değişiklik yasak |
| 5 | Onay kapısı | Yeni .yml taslak → kullanıcı onayı → uygulama | Sürpriz CI kapısı önlenir |
| 6 | Subagent commit atmaz | Commit orkestratörde | Kör commit önlenir |
| 7 | En az yetki | permissions: contents: read | Token genişlemesi önlenir |
| 8 | Disk > beyan | Çelişkide disk ölçümü kazanır | Bayat sayı düzeltilir |

## §3 Kademe-2 Kapsam Kataloğu (v2 §2.18 — 10 çocuk + 1 disk fazlası)

| # | Çocuk | Kapsam rolü | Kapsam durumu | Disk (2026-10-13) |
|---|---|---|---|---|
| 1 | source-control/ | Depo · branch · commit kuralları | UYARAN (depo VAR — çelişki) | 1 dosya · 52 satır |
| 2 | build/ | Derleme · bağımlılık kurulumu | YOK (diskte build/ yok) | 0 dosya (pipeline/ karşılık) |
| 3 | ci/ | Push/PR kapıları (lint/test/audit) | TAM (kanıtlı) | 2 dosya · 561 satır |
| 4 | cd/ | Yayına sürme otomasyonu | İSKELET (deploy job YOK) | 1 dosya · 50 satır |
| 5 | environments/ | Ortam tanımları · değişkenler | İSKELET (environment: blok YOK) | 1 dosya · 50 satır |
| 6 | deployment/ | Yayın mekaniği · onay akışı | KISMİ (deployment.md VAR) | 3 dosya · 1.086 satır |
| 7 | rollback/ | Geri alma | İSKELET (index tek) | 1 dosya · 51 satır |
| 8 | release-management/ | Sürüm · release notu | İSKELET (index tek) | 1 dosya · 50 satır |
| 9 | artifact-management/ | Artifact saklama · imaj | KISMİ (cache var, upload YOK) | 2 dosya · 562 satır |
| 10 | infrastructure-testing/ | Altyapı testi | İSKELET (index tek) | 1 dosya · 50 satır |
| 11 | pipeline/ (fazla) | CI/CD aşamaları · hızlandırma | AÇIK KARAR (v2'de YOK) | 2 dosya · 1.024 satır |

**Kapsam dengesi:** 10 çocuğun 1'i TAM (ci) · 2'si KISMİ (deployment · artifact) · 1 UYARAN
(source-control, depo var) · 1 YOK (build → pipeline/) · 5 İSKELET · 1 disk fazlası (pipeline).
Domain **kanıt merkezli** büyüyor; eksikler işaretlendi, uydurulmadı.

### §3.1 Kapsam durumu etiketlerinin anlamı

| Etiket | Anlamı | Kapanış şartı | K018 çocuğunda sayı |
|---|---|---|---|
| TAM | Workflow + davranış diskte kanıtlı | IMPLEMENTED + run kanıtı | 1 (ci) |
| KISMİ | Bazı kanıt var, eksik adım | Eksik adımın kanıtı | 2 (deployment · artifact) |
| UYARAN | Kanıt var ama çelişki/uyarı | Çelişkinin çözülmesi | 1 (source-control) |
| YOK | v2 çocuğu diskte yok | Karşılığın eşleşmesi | 1 (build) |
| İSKELET | Yalnız index.md | 3 içerik ≥500 | 5 (cd · environments · rollback · release · infra) |
| AÇIK KARAR | Disk fazlası | ADR ya da kullanıcı | 1 (pipeline) |

### §3.2 Her çocuk için kapsam-sınır cümlesi (kademe-3 öncesi)

| # | Çocuk | Kapsam cümlesi (bu domainde) | Sınır cümlesi (DEĞİL) |
|---|---|---|---|
| 1 | source-control/ | Branch · commit · PR kuralları | Depo barındırma/yedek → K000/GitHub |
| 2 | build/ | composer install · npm ci · statik analiz | Kod derleme mantığı → production |
| 3 | ci/ | lint · test · audit job kapıları | Test içeriği → K019 |
| 4 | cd/ | Yayına sürme otomasyonu | Yayın **kararı** → kullanıcı |
| 5 | environments/ | dev/staging/prod tanımları · secret değişkenleri | Secret değeri → K009 / GitHub secrets |
| 6 | deployment/ | Aşama 1-8 onay akışı · sağlık | Uygulama içi health mantığı → K012/K017 |
| 7 | rollback/ | Geri alma planı · tetikleme | Geri alma sonrası olay → K017 |
| 8 | release-management/ | Sürüm notu akışı | Sürüm numarası politikası → ADR |
| 9 | artifact-management/ | Cache · imaj · upload-artifact | Artifact içeriği → üretim domaini |
| 10 | infrastructure-testing/ | Altyapı testi | Altyapı kurulumu → K000/cloud |

## §4 Komşu Domain Kapsam Sınırı (K000 · K009 · K017 · K019 · K016)

| # | Komşu | Sınır (kesişim) | K018 ne yapar | Komşu ne yapar |
|---|---|---|---|---|
| 1 | K019-testing | Test job'ları | Testi **koşar** (ci.yml php-test/js-test) | Testi **yazar** (tasarım/coverage) |
| 2 | K009-security | Secret taraması | GitLeaks job'u koşar | Kural · allowlist · crypto politikası |
| 3 | K017-observability | Alert → pipeline | Deploy/rollback **aksiyonu** | Uyarı **üretimi** · log/metrik |
| 4 | K016-AI | Ajan hook'ları | Workflow yml (GitHub) | Ajan boot/hooks (harness) |
| 5 | K000-isletim-sistemi | Runner/OS | Workflow ortamı | Fiziksel OS/boot |
| 6 | K012-api | Servis dağıtımı | Servis **yayın yolu** | Servis **kodu** |
| 7 | K011-application | Uygulama yayını | Uygulama **deploy mekaniği** | Uygulama **kodu** |
| 8 | K002-electroncis | Donanım yayını | Yalnız sınır (kapsam dışı) | Donanım tasarımı |
| 9 | K005-firmware | Firmware OTA | Yalnız sınır (kapsam dışı) | Firmware OTA mekaniği |
| 10 | K003-audio | Ses motoru yayını | K018 yolu | Neva Engine kodu |

**Sınır ihlali davranışı:** domain sınırı ihlalinde sistem durur, MO müdahale eder
(.ai/AGENTS.md §18 kural 1); halüsinasyon `VERIFICATION REQUIRED` (.ai/AGENTS.md §18 kural 3).
İhlal → DUR + rapor (kök AGENTS.md §3). Bayat sayım ile disk çelişince **disk kazanır**.

### §4.1 Kesişim-sınırı davranış matrisi

| # | Kesişim senaryosu | Doğru davranış | Yanlış davranış (yasak) |
|---|---|---|---|
| 1 | Yeni test job | QA matrix onayı + DevOps yml | Onaysız job eklenir |
| 2 | Yeni workflow | Taslak → kullanıcı onayı → uygulama | Sürpriz workflow push |
| 3 | .gitleaks.toml değişikliği | Security/ADR onayı | DevOps tek başına allowlist |
| 4 | Deploy tetiklenmesi | deployment.md Aşama 4 onay | Onaysız prod deploy |
| 5 | Rollback | deployment.md §4 | Manuel, izsiz geri alma |
| 6 | Secret değeri | GitHub secrets (K009) | .md/.log/.json'a yazma |
| 7 | Commit | Orkestratör atar | Subagent commit |
| 8 | CI geçiş iddiası | GitHub run kanıtı | Kanıtsız "CI geçti" |

## §5 Kapsam Kararları (defter)

| # | Karar | Dayanak | Etkilenen çocuk | Durum |
|---|---|---|---|---|
| 1 | CI testleri koşar, tasarımı K019'da | v2 §2.18/§2.19 · ci.yml php-test | ci/ | tescilli — kanıtlı |
| 2 | Uyarı K017, aksiyon K018 | v2 §2.17/§2.18 | cd/ · deployment/ · rollback/ | tescilli |
| 3 | ci.yml sahibi DevOps + QA (test kapsamı) | .github/AGENTS.md satır 59 | ci/ | tescilli — kanıtlı |
| 4 | secret-scan.yml sahibi Security (kapsam) + DevOps (yml) | .github/AGENTS.md satır 60 | ci/ (secret) | tescilli — kanıtlı |
| 5 | Yeni .yml taslak → kullanıcı onayı → uygulama | .github/AGENTS.md satır 63 | tümü | tescilli — kanıtlı |
| 6 | Subagent git commit atmaz | .github/AGENTS.md satır 85 | source-control/ | tescilli — kanıtlı |
| 7 | PHP sürümü composer.json'dan (8.4) | ci.yml satır 22 | ci/ · build/ | tescilli — kanıtlı |
| 8 | Statik analiz composer script (level=5) | ci.yml satır 62-63 · composer.json 48 | build/ · ci/ | tescilli — kanıtlı |
| 9 | auth testleri yalnız Unit (Integration dizini YOK) | ci.yml satır 77-80 | ci/ | tescilli — kanıtlı |
| 10 | js-coverage soft gate (A7) | ci.yml satır 167 continue-on-error | ci/ | tescilli — kanıtlı |
| 11 | js-e2e soft gate (A6) | ci.yml satır 183 continue-on-error | ci/ | tescilli — kanıtlı |
| 12 | Node 26 toolchain | ci.yml satır 151 | ci/ · build/ | tescilli — kanıtlı |
| 13 | Workflow permissions en az yetkide | ci.yml satır 14-15 · secret-scan 11-12 | tümü | tescilli — kanıtlı |
| 14 | Aynı ref eşzamanlılığı iptal edilir | ci.yml satır 17-19 · secret-scan 14-16 | tümü | tescilli — kanıtlı |
| 15 | GitLeaks tam geçmiş taranır (fetch-depth 0) | secret-scan.yml satır 26 | ci/ (secret) | tescilli — kanıtlı |
| 16 | GitLeaks config kök .gitleaks.toml | .gitleaks.toml satır 2 | ci/ (secret) | tescilli — kanıtlı |
| 17 | Allowlist yalnız test sahte şifreleri | .gitleaks.toml satır 3-4 (ADR-034) | ci/ (secret) | tescilli — kanıtlı |
| 18 | Deploy onayı 8 aşamalı | .workflows/deployment.md 126-199 | deployment/ | tescilli — kanıtlı |
| 19 | Rollback deployment.md §4 | .workflows/deployment.md 212 | rollback/ | tescilli — kanıtlı |
| 20 | Yeni workflow şablondan (Guardrail #16) | ci.yml 2 · secret-scan 2 · şablon 30 | tümü | tescilli — kanıtlı |
| 21 | npm ci kilitli kurulum | ci.yml 155 · 174 · 197 | build/ · ci/ | tescilli — kanıtlı |
| 22 | composer audit 3 hedefte | ci.yml 126 · 132 · 138 | ci/ | tescilli — kanıtlı |
| 23 | k13-cicd plan referansı diskte YOK | ci.yml satır 3 · Test-Path False | ci/ | tescilli — negatif |
| 24 | Depo durumu: main dalı VAR (çelişki) | .git/HEAD satır 1 | source-control/ | **çelişki** (§G) |
| 25 | .gitleaks.toml VAR (secret-scan:4 YANLIŞ) | .gitleaks.toml satır 1 | ci/ (secret) | **çelişki** (§G) |
| 26 | build/ yerine pipeline/ diskte | dizin listesi | pipeline/ | açık karar |
| 27 | pipeline/ v2'de YOK — fazladan | dizin listesi | pipeline/ | açık karar |
| 28 | Bu revizyon commit atılmaz | .github/AGENTS.md satır 85 | tümü | uygulandı |
| 29 | Kapsam dışı yüzeye dokunma revert | kök AGENTS.md §3 | tümü | tescilli |
| 30 | CI geçiş iddiası run kanıtı ister | .ai/AGENTS.md §25.2 | ci/ | tescilli |

## §6 Kapsam-Dışı Yüzey Beyanı (dürüstlük)

Aşağıdaki yüzeyler bu domainin **kapsamı dışındadır**; ilgili domain dosyasına bakılır. Kapsam
dışı yüzey için kod/doküman **üretilmez**.

| # | Yüzey | Sahip domain | Bu dosyada |
|---|---|---|---|
| 1 | İşletim sistemi / boot / runner donanımı | K000-isletim-sistemi | tanımlanmaz |
| 2 | Donanım / DAC-ADC / PCB | K001-donanım | tanımlanmaz |
| 3 | Elektronik devre / BOM | K002-electroncis | tanımlanmaz |
| 4 | Firmware / OTA mekaniği | K005-firmware | tanımlanmaz |
| 5 | Güvenlik politikası / crypto / allowlist kuralı | K009-security | yalnız sınır (§4/2) |
| 6 | Identity doğrulama mantığı | K010-identitiy | tanımlanmaz |
| 7 | Uygulama iş mantığı | K011-application | yalnız sınır (§4/7) |
| 8 | API gateway / servis kodu | K012-api / K012-services | yalnız sınır (§4/6) |
| 9 | Ses motoru (Neva Engine) | K003-audio | yalnız sınır (§4/10) |
| 10 | DSP algoritması | K004-dsp | tanımlanmaz |
| 11 | Test stratejisi / coverage hedefi | K019-testing | yalnız sınır (§4/1) |
| 12 | Log/metrik/alert üretimi | K017-observability | yalnız sınır (§4/3) |

> **Dürüstlük notu:** Repo'da K018'e ait **gerçek workflow kodu** vardır: ci.yml (203 satır) ·
> secret-scan.yml (31 satır) · .gitleaks.toml (18 satır) · deployment.md (281 satır). cd/ environments/
> rollback/ release/ infrastructure-testing için **deploy/otomasyon kodu YOK** — kaynak: vault
> (uydurma yok). CI'nin **çalışıp geçtiğine** dair GitHub run kanıtı diskte DEĞİL → doğrulanmadı.

### §6.1 Repo'da kapsam karşılığı olan/yolmayan yüzeyler

| # | Yüzey | Repo durumu | Kapsam etiketi |
|---|---|---|---|
| 1 | CI workflow | .github/workflows/ci.yml VAR (203 satır, 6 job) | IMPLEMENTED (dosya) |
| 2 | Secret tarama | .github/workflows/secret-scan.yml VAR (31 satır) | IMPLEMENTED (dosya) |
| 3 | GitLeaks config | .gitleaks.toml VAR (18 satır) | IMPLEMENTED (dosya) |
| 4 | Deploy onay akışı | .workflows/deployment.md VAR (281 satır) | IMPLEMENTED (doküman) |
| 5 | Şablon | .ai/.templates/infrastructure/github-actions-template.md VAR | IMPLEMENTED (şablon) |
| 6 | JS test komutu | package.json VAR (18 satır) | IMPLEMENTED (dosya) |
| 7 | PHP test/analiz | shared/composer.json VAR (50 satır) | IMPLEMENTED (dosya) |
| 8 | CD deploy job | workflows'da deploy job YOK | İSKELET |
| 9 | environment: bloğu | yml'lerde YOK | İSKELET |
| 10 | upload-artifact | ci.yml'da YOK | KISMİ |
| 11 | CI geçiş (run kanıtı) | diskte DEĞİL | doğrulanmadı |
| 12 | infrastructure-testing kodu | YOK | İSKELET |

## §7 Repo Kanıtları (kapsam — gerçek dosya/satır)

Dosya: `.github/workflows/ci.yml` · Satır: 8 — `name: CI Pipeline` (workflow adı, 203 satır).

Dosya: `.github/workflows/ci.yml` · Satır: 14-15 — `permissions:` / `contents: read` (en az yetki — §5/13).

Dosya: `.github/workflows/ci.yml` · Satır: 17-19 — `concurrency:` / `cancel-in-progress: true` (§5/14).

Dosya: `.github/workflows/ci.yml` · Satır: 22 — `PHP_VERSION: '8.4'` (§5/7).

Dosya: `.github/workflows/ci.yml` · Satır: 25 — `php-lint:` (job 1/6).

Dosya: `.github/workflows/ci.yml` · Satır: 66 — `php-test:` (job 2/6 — K019 entegrasyonu).

Dosya: `.github/workflows/ci.yml` · Satır: 68 — `needs: php-lint` (kapı sırası).

Dosya: `.github/workflows/ci.yml` · Satır: 110 — `composer-audit:` (job 3/6).

Dosya: `.github/workflows/ci.yml` · Satır: 142 — `js-test:` (job 4/6).

Dosya: `.github/workflows/ci.yml` · Satır: 160 — `js-coverage:` (job 5/6 — A7 soft).

Dosya: `.github/workflows/ci.yml` · Satır: 178 — `js-e2e:` (job 6/6 — A6 soft).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 19 — `gitleaks:` (tek job, 31 satır).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 26 — `fetch-depth: 0` (tam geçmiş — §5/15).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 29 — `uses: gitleaks/gitleaks-action@v2`.

Dosya: `.gitleaks.toml` · Satır: 6 — `title = "CoreMusic gitleaks"` (18 satır).

Dosya: `.gitleaks.toml` · Satır: 9 — `useDefault = true` (varsayılan kurallar).

Dosya: `.gitleaks.toml` · Satır: 12-17 — allowlist yalnız test dosyaları (§5/17).

Dosya: `.github/AGENTS.md` · Satır: 59 — ci.yml sahipliği: DevOps · QA (§5/3).

Dosya: `.github/AGENTS.md` · Satır: 60 — secret-scan.yml sahipliği: Security · DevOps (§5/4).

Dosya: `.github/AGENTS.md` · Satır: 63 — Yeni .yml: Taslak → onay → uygulama (§5/5).

Dosya: `.github/AGENTS.md` · Satır: 85 — Subagent git commit ATMAZ (§5/6).

Dosya: `.workflows/deployment.md` · Satır: 126 — `### Aşama 1` başlangıcı (281 satır).

Dosya: `.workflows/deployment.md` · Satır: 156 — `### Aşama 4: Onay` (insan kapısı — §5/18).

Dosya: `.workflows/deployment.md` · Satır: 182 — `### Aşama 6: Sağlık Kontrolü`.

Dosya: `.workflows/deployment.md` · Satır: 212 — `## 4. Geri Dönüş (Rollback)` (§5/19).

Dosya: `package.json` · Satır: 5 — `"test:js": "vitest run --root assets.coremusic.net"`.

Dosya: `package.json` · Satır: 8 — `"test:e2e": "playwright test ..."`.

Dosya: `shared/composer.json` · Satır: 8 — `"php": ">=8.4"` (§5/7).

Dosya: `shared/composer.json` · Satır: 48 — `"stan": "phpstan analyse src --level=5"` (§5/8).

Dosya: `.git/HEAD` · Satır: 1 — `ref: refs/heads/main` (depo VAR — §5/24 çelişki).

## §8 Web tablosu (kapsam)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| GitLab CI/CD pipeline, `stages` ile job'ların sıralı/diş bağımlı çalıştığı tanımlanır — `needs`/`dependsOn` ile K018 kapı sırası (ci.yml `needs: php-lint`) modelinin standart dayanağı | https://docs.gitlab.com/ee/ci/yaml/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter (2640) dışı |

## §9 Doğrulama (kapsam)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | §G/1 |
| 2 | Son satır | Authority dizgesi birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + node_id + group (mimari) · parent: 0 | ✓ |
| 4 | Kapsam sınırı | §2 · §4 · §6 tabloları dolu | ✓ |
| 5 | Repo kanıtları | ≥ 1 Dosya/Satır · gerçek indeks | ✓ (30 satır) |
| 6 | Web tablosu | 1 URL · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | 3 yer-tutucu türü · boş hücre · mojibake | ✓ 0 |
| 8 | Kapsam dışı beyanı | K000/K001/K002/K005/K009/K010/K011/K012/K017/K019 sınırı | ✓ |

## §A Ölçüm günlüğü (kapsam)

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi durum | kapsam dosyası YOK | kök 6 MD şemasında eksik |
| 2 | Yazım sonrası uzunluk | §G/1 hücresinde | ≥ 500 hedefi |
| 3 | Frontmatter `group` | mimari (tırnaksız) | -kapsam → mimari |
| 4 | `parent:` sayısı | 0 | yalnız parent_id |
| 5 | Boş hücre | 0 | tüm hücreler dolu |
| 6 | Mojibake | 0 | UTF-8, Türkçe sağlam |
| 7 | Repo kanıtı satırı | 30 Dosya/Satır | gerçek dosya indeksi |
| 8 | Web URL | 1 · envanter dışı · HTTP 200 | benzersiz |
| 9 | Yazılan dosya (bu görev) | 4 (K017×2 · K018×2) | beyazliste sınırlı |
| 10 | Dışarıya yazım | 0 | beyazliste ihlali yok |

## §B Kapsam→Dosya Eşlemesi (bu domain)

| # | Kök dosya | group | Kapsam rolü | Durum |
|---|---|---|---|---|
| 1 | index.md | mimari | Klasör künyesi · çocuk kataloğu | mevcut (85 satır) |
| 2 | devops-master.md | mimari | Domain anayasası · karar defteri | mevcut (528 satır) |
| 3 | devops-kapsam.md | mimari | Sınır tanımı (bu dosya) | bu görevde yazıldı |
| 4 | devops-bagimliliklar.md | mimari | Katman bağı · used_by | mevcut (511 satır) |
| 5 | devops-dogrulama.md | kanit | İddia/URL doğrulama matrisi | mevcut (500 satır) |
| 6 | devops-ornekler.md | kod | Gerçek workflow/config örnekleri | bu görevde yazıldı |

## §C Alt-Konu Kapsam Planı (kademe-3)

| # | Alt-konu | Gerekli iskelet | Mevcut | Eksik |
|---|---|---|---|---|
| 1 | ci/ | index + 3 içerik | index + 1 içerik | 2 içerik |
| 2 | cd/ | index + 3 içerik | index | 3 içerik |
| 3 | deployment/ | index + 3 içerik | index + 2 içerik | 1 içerik |
| 4 | environments/ | index + 3 içerik | index | 3 içerik |
| 5 | rollback/ | index + 3 içerik | index | 3 içerik |
| 6 | release-management/ | index + 3 içerik | index | 3 içerik |
| 7 | artifact-management/ | index + 3 içerik | index + 1 içerik | 2 içerik |
| 8 | infrastructure-testing/ | index + 3 içerik | index | 3 içerik |
| 9 | source-control/ | index + 3 içerik | index | 3 içerik |
| 10 | pipeline/ | index + 3 içerik | 2 içerik | index + 1 içerik |

> Alt-konu derinleştirme Band-1 kök iskeletinin **sonrasında** ayrı turda yapılır (master §D.1).

## §D Terimler

| Terim | Tanım |
|---|---|
| CI (continuous integration) | Push/PR'da otomatik kapı: lint · test · audit |
| CD (continuous delivery/deployment) | Yayına sürme otomasyonu |
| Pipeline | Job'ların sıralı/diş bağımlı akışı |
| Gate | Bir işi bloklayan kapı (hard) veya uyaran (soft, continue-on-error) |
| Soft gate | Kırmızı görünsün ama bloklamayan job (A6/A7) |
| Artifact | Derleme/kurulum çıktısı saklama birimi |
| Rollback | Yayını geri alma |
| Runner | Workflow'un çalıştığı sanal/donanım ortam |

## §E Referanslar

| Kaynak | Referans |
|---|---|
| v2 spec | coremusic-katmanli-mimari-agaci-v2.md §2.18 (çocuk listesi birebir) |
| ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme |
| Master | devops-master.md §1-§8 · §A-§G |
| Bağımlılık | devops-bagimliliklar.md |
| Doğrulama | devops-dogrulama.md |
| Sahiplik | .github/AGENTS.md §3.1 |
| Deployment | .workflows/deployment.md |

## §F Sınır Özeti (1 satır)

K018 = kodun **üretilip yayılma yolu**: depo · build · CI · CD · ortam · deployment · rollback ·
release · artifact · infra-test; testlerin kendisi K019'da, K018 onları koşar; uyarı K017, aksiyon K018.

## §G Kapsam İhlali Senaryoları (sınır-dışı sızma tablosu)

> Bu bölüm, K018 kapsamının **ihlal edilme** yollarını önceden kaydeder: hangi iş ne
> dediğinde kapsam-dışı sızma olmuştur ve tepki ne olmalıdır. Kayıt ürettikçe §5 karar
> defterine satır eklenir; silme yok, yalnız genişletme vardır.

### §G.1 İhlal senaryosu tablosu

| # | Tetikleyici cümle (kullanıcı/agent) | İhlal türü | Doğru sahip | Tepki |
|---|---|---|---|---|
| S1 | Testleri de sen yaz, hepsini geçirelim | K019'a sızma (test tasarımı) | K019 (QA) | Reddet · K018 yalnız koşar |
| S2 | Alert eşiğini sen belirle, kafa karışıklığı olmasın | K017'ye sızma (uyarı eşiği) | K017 (observability) | Yönlendir · eşik K017'de |
| S3 | Bu .yml'yi direct commit et, onay sonra | Sahiplik ihlali (.github/AGENTS.md 63) | Orkestratör + kullanıcı | DUR · taslak → onay → uygulama |
| S4 | Subagent sen commit'le, hızlı olsun | Yetki ihlali (.github/AGENTS.md 85) | Orkestratör | Reddet · commit yetkisi yok |
| S5 | php-fpm ayarını da düzelt, deployment bozuk | Sunucu yapılandırması | K018 · kullanıcı onayı | Taslak → onay → uygulama |
| S6 | K011'deki component'i de bağla pipeline'a | K011'e sızma (uygulama kodu) | K011 (application) | Reddet · sınır §4/§6 |
| S7 | Rollback kodunu sen yaz | K018 kapsam içi kod üretimi | K018 · taslak onayı | Kapsam içi · onay kapısı §5/5 |
| S8 | içinde .env olan klasörü commit et | Secret sızıntısı (§5/15 · .gitignore) | Security + DevOps | Reddet · allowlist path dışlama |
| S9 | workflow permissions'ı write yap | Yetki genişletme (§5/13) | DevOps + güvenlik denetiği | Reddet · en az yetki ilkesi |
| S10 | concurrency iptalini kapat, işler bekliyor | Eşzamanlılık ihlali (§5/14) | DevOps | Reddet · aynı ref iptal edilir |
| S11 | K019 testleri kırmızı, continue-on-error yapalım | Kapıyı soft'a çevirme | QA + DevOps | DUR · eşik sahibi K019 |
| S12 | artifact'leri 90 gün sakla | Saklama politikası değişikliği | DevOps + maliyet sahibi | Taslak → onay (retention-days) |
| S13 | index.md'yi de sen güncelle | Orchestration yüzeyi | Orkestratör | Reddet · beyazliste dışı |
| S14 | K001 donanım dosyasına da göz at, düzelt | Yasaklı dizin (K001) | Hardware ekibi | Reddet · yazım yasağı |

### §G.2 Seçili senaryoların gerekçesi

**S1 — test tasarımı:** .github/AGENTS.md ci.yml sahipliğini DevOps + QA olarak
verir (§5/3); QA'nın payı test **kapsamı**dır. K018 subagent'i bir test case üretirse
sınır K019'a taşar. Doğru davranış: pipeline'ı koşturur, testin ne olduğunu sormaz.

**S2 — uyarı eşiği:** master §3 kararının (§5/2) yansımasıdır: Uyarı K017, aksiyon
K018. Eşik belirlemek uyarı tasarımıdır; K018 yalnız eşik verildiğinde onu kodlar.

**S3 — onay kapısı:** .github/AGENTS.md satır 63 (§5/5), yeni .yml için zorunlu
taslak→onay→uygulama zinciridir. Hız endişesi bu kapıyı atlamaz; atlamak isteyen
talep sahibine döner.

**S4 — commit yetkisi:** satır 85 (§5/6) metni bağlayıcıdır; bu dosyanın kendisi de
buna bağlıdır (§H revizyon kaydındaki dışarıya yazım 0 satırı).

**S11 — soft gate'e çevirme:** A6/A7 zaten soft'tur (§5/10-11); yeni bir hard gate'i
soft yapmak, eşik sahibinin (K019) yetkisini K018'in rızasıyla değiştirmek demektir —
rıza yetki vermez; kapı değişikliği QA karar defterine işlenir.

### §G.3 Sınır kontrol matrisi (ne yazılabilir özeti)

| Yüzey | K018 subagent yazabilir mi? | Dayanak |
|---|---|---|
| .ai/architecture/K018-devops/ altındaki .md dosyaları | Evet (beyazliste) | Task brief §1 |
| .ai/architecture/K017-observability/ altındaki .md dosyaları | Evet (beyazliste) | Task brief §1 |
| .ai/architecture/index.md | Hayır | Orkestratöre ait |
| .github altındaki .yml dosyaları | Hayır (yalnız taslak önerisi) | §5/5 onay kapısı |
| php · js · css · sql uzantılı kaynak dosyalar | Hayır | Task brief §4 yazım yasağı |
| K000 · K001 · K002 · K005 · K009 · K010 · K011 · K012 dizinleri | Hayır | Task brief §4 |
| .ai/electronic/ dizini | Hayır | Task brief §4 |
| .git içine yazım (staging/commit) | Hayır | .github/AGENTS.md satır 85 |

### §G.4 İhlal kaydı akışı

1. İhlal fark edilir (agent veya insan) — kayıt anında §G.1 tablosuna satır eklenir.
2. Satır dört alanı da taşır: tetikleyici cümle · türü · sahibi · tepki — boş hücre yok.
3. Sahip farklı domain ise (K017/K019/K011) talep yönlendirilir, cevap K018'de üretilmez.
4. Sahip K018 ise taslak hazırlanır; onay beklenebilir, uygulama onaysız başlamaz.
5. Tepki Reddet ise gerekçe dayanağıyla (satır no) yazılır; gerekçesiz ret de ihlaldir.
6. Kayıt silinmez; yanlış kayıt yeni satırla düzeltilir (append-only defter).

### §G.5 Bu bölümün dürüstlük sınırı

| İddia | Durum |
|---|---|
| Tablodaki 14 senaryonun gerçekliği | S3 · S4 · S8 · S9 · S10 · S13 dosya-dayanaklı; S1 · S2 · S5 · S6 · S7 · S11 · S12 · S14 olası tetikleyici olarak listelendi (henüz yaşanmadı) |
| Gerçek senaryoların tarihi | 2026-10-13 görev notları |
| Olası senaryoların tarihi | YOK — yaşanmadı |
| Bu dosyadan önce ayrı ihlal defteri | UNKNOWN |
| §G'ye sonradan satır eklenebilir | Evet — append-only, mevcut satır numaraları değişmez |
| §G.1'deki tepki sütunu bağlayıcı mı | Evet — Task brief + .github/AGENTS.md dayanaklı satırlar bağlayıcı |

### §G.6 Domainler arası sınır cümleleri (hazır cevaplar)

| Soru türü | K018'in verdiği cevap sınırı |
|---|---|
| Test neden geçmedi? | Hangi job'un kırıldığını söyler; testin doğruluğuna karar vermez (K019) |
| Uyarı neden tetiklendi? | K017 eşiklerini gösterir; eşiği değiştirmez (K017) |
| Kod neden deploy edilmiyor? | Kapı durumunu listeler; kapı sahibi dışında kapıyı kapatmaz |
| .yml'ye satır ekle? | Taslak üretir; commit etmez, onay bekler (§5/5) |
| Branch neden kapatıldı? | ci.yml tetikleyicilerini okur; tetikleyiciyi kendisi değiştirmez |
| Build neden yavaş? | Job sürelerini raporlar; runner boyutlandırma kararı onay ister |
| Rollback yap | Kapsam içi · deployment.md §4 zinciri uygulanır, insan kapısı §5/18 korunur |
| Secret sızdı mı? | Gitleaks raporunu okur; allowlist'e ekleme Security onayı ister |

### §G.7 Çelişki-ile-yaşama kaydı (bu bölümün hafızası)

| # | Çelişki | Kayıt yeri | Durum |
|---|---|---|---|
| C1 | .git/HEAD depo var diyor; önceki görev notu depo yok diyor | §5/24 · master §10 | Açık — ikisi de kayıtlı |
| C2 | secret-scan.yml:4 .gitleaks.toml yok diyor; dosya diskte var | §5/25 · master §10 | Açık — ikisi de kayıtlı |
| C3 | v2 §2.18 build/ ister; diskte pipeline/ var | §5/26-27 | Açık — yeniden adlandırma onayı yok |
| C4 | pipeline/ v2 çocuk listesinde yok (fazladan dizin) | §5/27 | Açık — fazlalık, silinmedi |
| C5 | deployment.md kapı sayısı 7 vs 8 | K018-ornekler.md §6.4 | Açık — sayım çelişkisi |

Çelişkiler bu bölümde tekrar edilmez; tek kayıt yeri §5 karar defteri ve master §10'dur.
Bu tablo yalnız hangi çelişkinin hangi dosyada olduğunu gösterir (gösterge, kopya değil).

### §G.8 Bu bölümün gerçekleşme kaydı

| # | Kayıt | Değer |
|---|---|---|
| 1 | Bölüm türü | Ön-kayıt (proaktif ihlal senaryosu) — henüz ihlal yaşanmadı |
| 2 | Eklenme tarihi | 2026-10-13 |
| 3 | Ekleyen | K018-devops subagent (task brief §1 beyazliste) |
| 4 | Silme kuralı | Yok — yalnız append |
| 5 | Güncelleyen | Sahiplik: .github/AGENTS.md §3.1 (DevOps) |

## §H Revizyon doğrulama kaydı (kapsam)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 505 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + node_id + group (mimari, tırnaksız) · parent: 0 | ✓ |
| 4 | Zorunlu bölümler | §1-§9 + §A-§H · tümü `## §` | ✓ |
| 5 | Repo kanıtları | 30 Dosya/Satır · gerçek satır indeksi ile birebir okundu | ✓ |
| 6 | Web tablosu | 1 URL · envanter (2640) dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | 3 yer-tutucu türü · boş hücre · mojibake · dolgu | ✓ 0 |
| 8 | Kapsam dışı beyanı | K000/K001/K002/K005/K009/K010/K011/K012/K017/K019 §6'da sınır | ✓ |
| 9 | Yazılan dosya | bu dosya dâhil 4 (K017×2 · K018×2) | ✓ |
| 10 | Dışarıya yazım | 0 (beyazliste K017*/K018* *.md) | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode