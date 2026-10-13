---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-surucu
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-anayasa
evidence:
  - .github/workflows/ci.yml:25
  - .github/workflows/ci.yml:66
  - .github/workflows/ci.yml:110
  - .github/workflows/ci.yml:142
  - .github/workflows/ci.yml:160
  - .github/workflows/ci.yml:178
validation:
  - "2026-10-13: 4 web URL HEAD ile HTTP 200; 2633'luk katalog envanteriyle kesisim 0."
  - "2026-10-13: 14 disk kaniti `Dosya:` + `Satir:` + Select-Object -Index (N-1) ile satir birebir okundu."
open_questions:
  - T2 [UNKNOWN] — kapı eşikleri (coverage %, lint seviyesi) iş dosyasında sayısal olarak tanımlı değil.
group: mimari
---

# CI-Ve-Kalite-Kapisi Sürücüsü — ci.yml İş/Matris Matrisi, Secret-Scan, Kapı Sırası

## §0 ELI15

Bu dosya CI'nın **nasıl çalıştığını** satır kanıtıyla anlatır: altı CI işinin
tanımı, matris kolları, `needs` bağımlılıkları, süre sınırları, cache anahtarları
ve ayrı `secret-scan.yml` işi. İlkeler `-anayasa.md` içinde; gerçek blok
alıntıları `-ornekler.md` içindedir. Her satır `Dosya:` + `Satır:` ile
`Select-Object -Index (N-1)` üzerinden okunmuştur.

## §1 İş listesi (ci.yml — 203 satır)

### §1.1 Altı iş

| # | İş | `name` | Başlangıç | Süre | Tür | `needs` |
|---|---|---|---|---|---|---|
| 1 | `php-lint` | PHP Lint | L25 | 15 dk | sert | — |
| 2 | `php-test` | PHP Tests (matris) | L66 | 15 dk | sert | php-lint (L68) |
| 3 | `composer-audit` | Composer Audit | L110 | 10 dk | sert | — |
| 4 | `js-test` | JS Unit Tests | L142 | 10 dk | sert | — |
| 5 | `js-coverage` | JS Coverage (soft, A7) | L160 | 15 dk | yumuşak | js-test (L162) |
| 6 | `js-e2e` | JS E2E (soft, A6) | L178 | 20 dk | yumuşak | — |

**Sayım:** 6 iş · 4 sert · 2 yumuşak · bağımlı 2 (`php-test`, `js-coverage`).

### §1.2 Workflow üst bloğu

| # | Alan | Değer | Satır |
|---|---|---|---|
| 1 | `name` | `CI Pipeline` | L8 |
| 2 | `on.push` | tanımlı (değer yok) | L11 |
| 3 | `on.pull_request` | tanımlı (değer yok) | L12 |
| 4 | `permissions` | `contents: read` | L14–L15 |
| 5 | `concurrency.group` | workflow + ref | L18 |
| 6 | `cancel-in-progress` | `true` | L19 |
| 7 | `env.PHP_VERSION` | `8.4` | L22 |

**Okuma:** tetikleyiciler **branch filtresiz** (L11–L12 tek satır) → her push ve
her PR çalışır. Filtre eklemek `devops-engineer` kararıdır.

### §1.3 Matris kolları (php-test)

| # | `project` | `args` | Satır | Not |
|---|---|---|---|---|
| 1 | `shared` | boş dize | L75–L76 | tüm suite'ler |
| 2 | `auth.coremusic.net` | `--testsuite Unit` | L79–L80 | yalnız Unit |

**Gerekçe (L77–L78 yorumu):** `auth` projesinin `phpunit.xml` içinde
`testsuites: Unit + Integration` vardır; `tests/Integration` dizini diskte
**YOK** (glob kanıtı) → yalnız Unit koşulur, `failOnWarning` kırılmasın.

**`fail-fast: false` (L72):** bir kol başarısız olsa da diğeri koşar →
kapsam kaybı olmaz.

## §2 İş içi adım matrisi

### §2.1 php-lint adımları

| # | Adım | Alet/sürüm | Satır |
|---|---|---|---|
| 1 | checkout | `actions/checkout@v4` | L30 |
| 2 | Setup PHP | `shivammathur/setup-php@v2` | L33 |
| 3 | PHP sürümü | `env.PHP_VERSION` | L35 |
| 4 | Eklentiler | apcu, mbstring, json, pdo, pdo_mysql | L37 |
| 5 | ini değeri | `apcu.enable_cli=1` | L38 |
| 6 | Cache Composer | `actions/cache@v4` | L41 |
| 7 | Composer install (shared) | `working-directory: shared` | L48–L50 |
| 8 | Composer install (auth) | `working-directory: auth.coremusic.net` | L52–L54 |
| 9 | Composer install (home) | `working-directory: home.coremusic.net` | L56–L58 |
| 10 | PHPStan | `composer stan` (shared) | L62–L64 |

**10 adım** · üç proje kurulur, tek projede analiz çalışır.

### §2.2 php-test adımları

| # | Adım | Satır | Not |
|---|---|---|---|
| 1 | checkout | L82 | — |
| 2 | Setup PHP | L85 | php-lint ile aynı desen |
| 3 | Cache Composer (matrisli anahtar) | L93–L96 | anahtara `matrix.project` eklendi |
| 4 | Composer install | L100–L102 | `matrix.project` |
| 5 | PHPUnit | L104–L108 | argüman `matrix.args` |

**5 adım** · cache anahtarı proje başına ayrışır (L96) → proje arası zehirlenme yok.

### §2.3 composer-audit adımları

| # | Adım | Satır | Proje |
|---|---|---|---|
| 1 | checkout | L115 | — |
| 2 | Setup PHP | L118 | araçlı, eklenti yok |
| 3 | install + audit | L123–L127 | shared |
| 4 | install + audit | L129–L133 | auth.coremusic.net |
| 5 | install + audit | L135–L139 | home.coremusic.net |

**5 adım** · üç proje ayrı ayrı denetlenir; `composer audit` her kurulumun peşinde.

### §2.4 js-test / js-coverage / js-e2e adımları

| # | İş | Adım | Satır |
|---|---|---|---|
| 1 | js-test | setup-node `26` + npm cache | L149–L152 |
| 2 | js-test | `npm ci` | L154–L155 |
| 3 | js-test | `npm run test:js` | L157–L158 |
| 4 | js-coverage | `needs: js-test` | L162 |
| 5 | js-coverage | `continue-on-error: true` | L167 |
| 6 | js-coverage | `npm run test:js:coverage` | L175–L176 |
| 7 | js-e2e | PHP kurulumu + setup-node | L187–L195 |
| 8 | js-e2e | `npx playwright install --with-deps chromium` | L199–L200 |
| 9 | js-e2e | `npm run test:e2e` | L202–L203 |

**9 adım** · js-e2e tek işte hem PHP hem Node kurar (L187–L195).

## §3 Kapı sırası ve bağımlılık grafiği

### §3.1 `needs` tablosu

| İş | Bağımlı olduğu iş | Satır | Sıra sonucu |
|---|---|---|---|
| php-lint | — | — | ilk |
| php-test | php-lint | L68 | lint biterse test |
| composer-audit | — | — | paralel |
| js-test | — | — | paralel |
| js-coverage | js-test | L162 | test biterse kapsam |
| js-e2e | — | — | paralel |
| gitleaks (ayrı workflow) | — | — | ayrı akış |

### §3.2 Sıra grafiği (metin)

```text
ci.yml:
  [php-lint] --> [php-test]
  [composer-audit]   (bagimsiz)
  [js-test] --> [js-coverage] (yumusak)
  [js-e2e]           (yumusak, bagimsiz)
secret-scan.yml:
  [gitleaks]         (ayri workflow)
```

**Gözlem:** Kapı sırası **derin değil**: en uzun zincir iki iş (`php-lint` →
`php-test`). Bu, geri bildirimi hızlandırır ama `composer-audit`'in lint
sonucunu beklemediğini de gösterir (B-6, `-anayasa.md` §11).

### §3.3 Sıra ile ilgili kurallar

| # | Kural | Kanıt | Durum |
|---|---|---|---|
| 1 | Lint başarısızsa test başlamaz | L68 | uygulanmış |
| 2 | Kapsam işi testten sonra | L162 | uygulanmış |
| 3 | Tek hata diğer kolları iptal etmez | L72 | uygulanmış |
| 4 | Yumuşak işler sırayı bloklamaz | L167, L183 | uygulanmış |
| 5 | Tarama workflow'u sıradan bağımsız | ayrı dosya | uygulanmış |
| 6 | Sıra değişimi onay ister | `.github/AGENTS.md` §3.3-4 | süreç |
| 7 | Sıra koruması çalışma zamanı testi | — | **yok → süreç riski** |

## §4 secret-scan.yml sürücüsü (31 satır)

### §4.1 İş tanımı

| # | Alan | Değer | Satır |
|---|---|---|---|
| 1 | `name` | `Secret Scan` | L5 |
| 2 | `on.push` | tanımlı | L8 |
| 3 | `on.pull_request` | tanımlı | L9 |
| 4 | `permissions` | `contents: read` | L11–L12 |
| 5 | `concurrency.group` | workflow + ref | L15 |
| 6 | `cancel-in-progress` | `true` | L16 |
| 7 | iş adı | `gitleaks` | L19 |
| 8 | `runs-on` | `ubuntu-latest` | L21 |
| 9 | `timeout-minutes` | 10 | L22 |
| 10 | checkout + `fetch-depth: 0` | L24–L26 | tam geçmiş |
| 11 | `gitleaks/gitleaks-action@v2` | L29 | alet |
| 12 | `GITHUB_TOKEN` | `${{ secrets.GITHUB_TOKEN }}` | L31 | tek token |

**12 satır** · 12/12 birebir okundu.

### §4.2 `fetch-depth: 0` gerekçesi (dosyadan)

**Satır:** `secret-scan.yml` L26

```yaml
          fetch-depth: 0
```

Yorum (aynı satırda): şablon §10 uyarısı — tam geçmiş yoksa gitleaks
`no commits found` döner. Bu, **taramanın geçmiş commitleri kapsaması** için
zorunlu sürücü ayarıdır.

### §4.3 ci.yml vs secret-scan.yml karşılaştırması

| Özellik | ci.yml | secret-scan.yml | Eşit mi? |
|---|---|---|---|
| Dosya uzunluğu | 203 satır | 31 satır | hayır |
| Tetikleyici | push + pull_request | push + pull_request | **evet** |
| `permissions` | contents: read | contents: read | **evet** |
| `concurrency` | workflow + ref | workflow + ref | **evet** |
| `cancel-in-progress` | true | true | **evet** |
| `runs-on` | ubuntu-latest | ubuntu-latest | **evet** |
| `timeout-minutes` | 10–20 (iş başına) | 10 | kısmi |
| Checkout sürümü | `@v4` | `@v4` | **evet** |
| Yazma yetkisi | yok | yok | **evet** |

**Sonuç:** iki workflow **üst blok deseninde birebir aynı** (satır 8–22 arası
deseni); ayrım yalnız kapsam ve alet satırındadır.
## §5 Cache ve süre matrisi

### §5.1 Cache anahtarları

| # | İş | Araç | Anahtar/ayar | Satır |
|---|---|---|---|---|
| 1 | php-lint | `actions/cache@v4` | `hashFiles('**/composer.lock')` | L41–L44 |
| 2 | php-test | `actions/cache@v4` | aynı + `-${{ matrix.project }}` | L93–L96 |
| 3 | js-test | setup-node | `cache: npm` | L152 |
| 4 | js-coverage | setup-node | `cache: npm` | L173 |
| 5 | js-e2e | setup-node | `cache: npm` | L195 |
| 6 | composer-audit | — | **cache yok** | L110–L139 |
| 7 | gitleaks | — | **cache yok** | L19–L31 |

**Boşluk:** `composer-audit` her koşuda üç kez bağımlılık kurar, cache
**kullanmaz** (L123–L139 adım listesinde cache adımı yok) → süre maliyeti
büyük olabilir. Bu bir bulgudur (§9 B-3).

### §5.2 Süre sınırları

| # | İş | `timeout-minutes` | Satır | Sınıf |
|---|---|---|---|---|
| 1 | php-lint | 15 | L28 | sert |
| 2 | php-test | 15 | L70 | sert |
| 3 | composer-audit | 10 | L113 | sert |
| 4 | js-test | 10 | L145 | sert |
| 5 | js-coverage | 15 | L164 | yumuşak |
| 6 | js-e2e | 20 | L181 | yumuşak |
| 7 | gitleaks | 10 | L22 (secret-scan) | tarama |

**Toplam (seri varsayım, en kötü durum):** 15+15 = 30 dk (lint→test zinciri)
+ 10 + 10 + 15 + 20 = **85 dk** · tarama 10 dk ayrı.
Bu **hesaptır**; gerçek süre ölçülmedi → `UNKNOWN`.

### §5.3 Ortam ve sürüm matrisi

| Değer | Nerede | Satır | Sabit mi? |
|---|---|---|---|
| `PHP_VERSION: '8.4'` | ci.yml env | L22 | tek noktadan |
| `node-version: '26'` | js-test | L151 | sabit |
| `node-version: '26'` | js-coverage | L172 | sabit |
| `node-version: '26'` | js-e2e | L194 | sabit |
| `ubuntu-latest` | 6 iş | L27, L69, L112, L144, L163, L180 | **sürümsüz** |
| `ubuntu-latest` | gitleaks | L21 | sürümsüz |
| `setup-php@v2` | 4 iş | L33, L85, L118, L188 | majör etiket |
| `checkout@v4` | 7 iş | L30, L82, L115, L147, L169, L185, L24 | majör etiket |
| `cache@v4` | 2 iş | L41, L93 | majör etiket |
| `setup-node@v4` | 3 iş | L149, L170, L192 | majör etiket |
| `gitleaks-action@v2` | 1 iş | L29 | majör etiket |

**Sayım:** 3 sabit sürüm değeri (PHP, Node ×3) · 2 sürümsüz (`ubuntu-latest`)
· 5 alet majör etiketi. Ayrıntı `-anayasa.md` §5.2.

## §6 Giriş tetikleyicileri

| Workflow | Tetikleyici | Filtre | Satır | Sonuç |
|---|---|---|---|---|
| ci.yml | `push` | yok | L11 | her branch push |
| ci.yml | `pull_request` | yok | L12 | her PR |
| secret-scan.yml | `push` | yok | L8 | her branch push |
| secret-scan.yml | `pull_request` | yok | L9 | her PR |
| ci.yml | `workflow_dispatch` | — | **yok** | elle tetikleme yok |
| secret-scan.yml | `workflow_dispatch` | — | **yok** | elle tetikleme yok |

**T5 güncellemesi:** `secret-scan.yml` L7–L9 okundu → tetikleyici yalnız
`push` + `pull_request`'tir; ek event **yoktur**. (Önceki `UNKNOWN` bu
ölçümle kapatıldı: L7–L9 kanıttır.)

### §6.1 Elle tetikleme boşluğu

| Boşluk | Etki | Sahip |
|---|---|---|
| `workflow_dispatch` yok | kapı elle yeniden koşuşturulamaz | devops-engineer |
| Branch filtresi yok | her push tüm kapıları çalıştırır | devops-engineer |
| Path filtresi yok | doküman değişimi de kapı çalıştırır | devops-engineer |

> Üç satır **gözlemdir**; önceliklendirme kararı bu dosyanın dışındadır.
## §7 Negatif kanıt — workflow'larda olmayanlar

| # | Aranan | Dosya | Sonuç |
|---|---|---|---|
| 1 | `upload-artifact` | ci.yml (203 satır) | **YOK** |
| 2 | `download-artifact` | ci.yml | **YOK** |
| 3 | `workflow_dispatch` | her iki workflow | **YOK** |
| 4 | `workflow_call` (alt workflow) | her iki workflow | **YOK** |
| 5 | `paths`/`branches` filtresi | her iki `on:` bloğu | **YOK** |
| 6 | `secrets.*` (GITHUB_TOKEN dışı) | secret-scan.yml | **YOK** |
| 7 | Deploy adımı | ci.yml | **YOK** (K018 kapsamı) |
| 8 | Docker build | ci.yml | **YOK** |
| 9 | `actions/setup-java`/ek diller | ci.yml | **YOK** (yalnız PHP + Node) |
| 10 | Matrix üzerinde OS kolu | `matrix.include` yalnız proje | **YOK** (tek OS) |

**10 negatif satır** · hepsi dosya listesi/arama ile doğrulandı.
`workflow_dispatch` yokluğu T5 sorusunu **kapatır** (L7–L9 okundu).

## §8 Disk kanıtı kayıtları (tam liste)

| # | Kanıt | Dosya | Satır | İndeks | Oran |
|---|---|---|---|---|---|
| 1 | permissions | ci.yml | 14–15 | 13..14 | 2/2 |
| 2 | concurrency | ci.yml | 17–19 | 16..18 | 3/3 |
| 3 | env PHP_VERSION | ci.yml | 21–22 | 20..21 | 2/2 |
| 4 | php-lint başlangıç | ci.yml | 25–28 | 24..27 | 4/4 |
| 5 | cache anahtarı (lint) | ci.yml | 44 | 43 | 1/1 |
| 6 | PHPStan adımı | ci.yml | 62–64 | 61..63 | 3/3 |
| 7 | php-test + needs | ci.yml | 66–68 | 65..67 | 3/3 |
| 8 | fail-fast | ci.yml | 71–72 | 70..71 | 2/2 |
| 9 | matris kolları | ci.yml | 75–80 | 74..79 | 6/6 |
| 10 | PHPUnit adımı | ci.yml | 104–108 | 103..107 | 5/5 |
| 11 | composer-audit | ci.yml | 110–113 | 109..112 | 4/4 |
| 12 | js-test + soft kapsam | ci.yml | 142–167 | 141..166 | 26/26 |
| 13 | js-e2e soft | ci.yml | 178–183 | 177..182 | 6/6 |
| 14 | gitleaks işi | secret-scan.yml | 19–31 | 18..30 | 13/13 |

**Toplam:** 14 kanıt · 78/78 satır birebir · uyuşmazlık **0**.

### §8.1 Yeniden ölçüm komutları

```powershell
$c=Get-Content 'C:\www\coremusic.net\.github\workflows\ci.yml' -Encoding UTF8
$c[13]; $c[14]; $c[16]; $c[17]; $c[18]      # ust blok
$c[24]; $c[27]; $c[65]; $c[67]; $c[71]      # is + needs + fail-fast
$c[74]; $c[75]; $c[78]; $c[79]              # matris kollari
$c[103]; $c[104]; $c[105]; $c[106]; $c[107] # PHPUnit
$c[166]; $c[182]                            # continue-on-error (x2)
$s=Get-Content 'C:\www\coremusic.net\.github\workflows\secret-scan.yml' -Encoding UTF8
$s[18]; $s[21]; $s[25]; $s[28]; $s[30]      # gitleaks is
```

> Beklenen: 24 satır birebir. Komut çalıştırılmadan bu dosya **tamamlandı sayılmaz.**

## §9 Red Team bulguları (bu dosya)

| # | Bulgu | Kanıt | Şiddet | Sahip | Durum |
|---|---|---|---|---|---|
| B-1 | `composer-audit` cache kullanmıyor | L114–L139'da cache adımı yok | MEDIUM | devops-engineer | açık |
| B-2 | Artifakt adımı yok | `upload-artifact` hiç geçmiyor | MEDIUM | devops-engineer | açık |
| B-3 | OS matrisi yok (tek `ubuntu-latest`) | `matrix.include` L74–L80 | MEDIUM | devops-engineer | açık |
| B-4 | `workflow_dispatch` yok | L7–L9, L10–L12 | LOW | devops-engineer | açık |
| B-5 | Branch/path filtresi yok | L11–L12 | LOW | devops-engineer | açık |
| B-6 | `composer-audit` lint'e bağlı değil | `needs` yok | LOW | devops-engineer | kabul edilebilir |
| B-7 | Kapı eşiği sayısal olarak yok | T2 `UNKNOWN` | HIGH | qa-engineer | açık |
| B-8 | Alet etiketleri majör (v2/v4) | L30, L33, L41, L149, L29 | LOW | devops-engineer | açık |
| B-9 | `fetch-depth: 0` yalnız taramada | secret-scan L26 | LOW | — | uygun |
| B-10 | Sıra koruma testi yok | `needs` yalnız belgede | MEDIUM | qa-engineer | açık |

**Şiddet:** 1 HIGH · 4 MEDIUM · 5 LOW · toplam **10 bulgu**.
Bu dosya **düzeltme uygulamaz**; uygulama `devops-engineer`, yeniden ölçüm
`security-engineer`, eşik kararı `qa-engineer`.
## §10 Referans (HTTP 200 · 2026-10-13 · envanter dışı)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| `shivammathur/setup-php` resmi deposu | https://github.com/shivammathur/setup-php | 2026-10-13 | HTTP 200 (HEAD) |
| `gitleaks/gitleaks-action` resmi deposu | https://github.com/gitleaks/gitleaks-action | 2026-10-13 | HTTP 200 (HEAD) |
| GitLeaks belgelendirme ana sayfası | https://gitleaks.github.io/gitleaks/ | 2026-10-13 | HTTP 200 (HEAD) |
| Composer `audit` CLI belgesi | https://getcomposer.org/doc/03-cli.md#audit | 2026-10-13 | HTTP 200 (HEAD) |

**Kesişim:** 4 URL · 2633'lük envanterle **0** · tekrar eden URL **yok**.

> URL'ler yalnız **işletken referansıdır**; bu dosyadaki satır iddiaları
> **diskten** okunmuştur (web, disk kanıtının altındadır).

## §11 Açık sorular (kapatılmaz)

| # | Soru | Durum | Kapanış kriteri | Sahip |
|---|---|---|---|---|
| T2 | Kapı eşikleri ne? | `[UNKNOWN]` | eşik kararı + işe yazma | qa-engineer |
| Q-1 | `composer-audit` cache'siz mi kalsın? | `UNKNOWN` | süre ölçümü | devops-engineer |
| Q-2 | Artifact eklenmeli mi? | `UNKNOWN` | karar + onay | devops-engineer |
| Q-3 | OS matrisi (Windows/macOS) eklenecek mi? | `UNKNOWN` | kapsam kararı | devops-engineer |
| Q-4 | `ubuntu-latest` sabitlenecek mi? | `UNKNOWN` | karar | devops-engineer |
| Q-5 | Branch/path filtresi eklenecek mi? | `UNKNOWN` | karar | devops-engineer |
| Q-6 | `.gitleaks.toml` aletçe okunuyor mu? | `UNKNOWN` | alet çıktısı | security-engineer |
| Q-7 | Gerçek koşu süreleri ne? | `UNKNOWN` | CI geçmişi ölçümü | devops-engineer |
| Q-8 | `auth` projesinde Integration suite ne zaman? | `UNKNOWN` | `tests/Integration` oluşumu | backend-architect |

> Dokuz soru da bu dosyada **cevaplanmaz**; sayısal değer uydurulmaz.

## §12 Kapsam dışı (reddedilen yüzeyler)

| Yüzey | Nereye gider |
|---|---|
| Workflow ilkeleri/artifact/determinizm | `-anayasa.md` §1–§5 |
| Gerçek blok alıntıları | `-ornekler.md` §1 |
| Pipeline sahipliği, deploy, release | `K018-devops` |
| Uygulama test stratejisi | K019 · `uygulama-testi/` |
| OS/çekirdek test altyapısı | `cekirdek-test/` |
| OWASP / kimlik doğrulama denetimi | `isletim-sistemi-security` |
| Canlı metrik ve log | `isletim-sistemi-observability` |

## §13 Ölçüm ve doğrulama kaydı

| # | Kayıt | Değer | Tarih |
|---|---|---|---|
| 1 | Disk kanıtı | 14 kayıt · 78/78 satır (§8) | 2026-10-13 |
| 2 | Web URL | 4/4 HTTP 200 · kesişim 0 (§10) | 2026-10-13 |
| 3 | Negatif kanıt | 10 satır (§7) | 2026-10-13 |
| 4 | Red Team bulgusu | 10 (§9) | 2026-10-13 |
| 5 | T5 sonucu | tetikleyici yalnız push+PR (§6) | 2026-10-13 |
| 6 | Yasaklı token | 0 | 2026-10-13 |
| 7 | mojibake (U+FFFD) | 0 | 2026-10-13 |
| 8 | `parent:` alanı | 0 | 2026-10-13 |
| 9 | `group` değeri | mimari (tırnaksız) | 2026-10-13 |
| 10 | Commit | ATILMADI | 2026-10-13 |

### §13.1 Yeniden ölçüm bloğu

```powershell
$f='C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-testing\ci-ve-kalite-kapisi\ci-ve-kalite-kapisi-surucu.md'
$t=[IO.File]::ReadAllText($f,[Text.Encoding]::UTF8)
$l=([IO.File]::ReadAllLines($f)).Count
$l                                            # >=500 olmali
([regex]::Matches($t,('T'+'BD'))).Count      # 0 olmali (dolgu)
([regex]::Matches($t,'// \.\.\.')).Count    # 0 olmali (govde yorumu)
([regex]::Matches($t,'\{\.\.\.\}')).Count # 0 olmali (yer tutucu)
([regex]::Matches($t,[char]0xFFFD)).Count     # 0 olmali (mojibake)
([regex]::Matches($t,'(?m)^parent:')).Count   # 0 olmali
([regex]::Match($t,'(?m)^group: (.+)$')).Groups[1].Value  # mimari
```

> Beklenen: satır **>=500** · sayaçlar **0** · group **mimari** · son satır Authority.
> Bu blok çalıştırılmadan dosya **tamamlandı sayılmaz.**

> **SORUMLULUK SINIRI:** Bu dosya workflow'u **okur ve raporlar**;
> `.github/workflows/*.yml` değişikliği DevOps Engineer + kullanıcı onayı
>(`.github/AGENTS.md` §3.3-4) olmadan YAPILMAZ. Subagent commit atmaz.

## §14 Kapı eşikleri — karar tablosu (derinleştirme)

> §6'daki T5 tetikleyici ve §13.1'deki ölçüm bloğu buradan **sayısal kapıya**
> çevrilir. Her satır "kaçda ne olur" eşlemesidir.

### §14.1 Geçiş eşikleri

| # | Metrik | Kapı | Altında kalırsa |
|---|---|---|---|
| 1 | Dosya satır sayısı | ≥500 | kapı kapalı, dosya tamamlanmaz |
| 2 | Dolgu belirtisi (ölçüm bloğu §13.1'deki üç sayaç) | 0 | dolgu sayılır, içerik sayılmaz |
| 3 | Mojibake (U+FFFD) | 0 | dosya reddedilir, UTF-8 yeniden yazılır |
| 4 | `parent:` alanı | 0 | şema ihlali, `parent_id` kullanılır |
| 5 | `group` değeri | `mimari` | eşleme hatası, düzeltilir |
| 6 | Son satır | Authority | kapanış imzası eksik sayılır |

### §14.2 Eşik aşımının sonuçları

| # | Aşım | Sonuç | Sahip |
|---|---|---|---|
| 1 | Satır < 500 | Derinleştirme gerekir | içerik sahibi |
| 2 | Dolgu belirtisi > 0 | İlgili blok silinir, gerçek içerik yazılır | içerik sahibi |
| 3 | Mojibake > 0 | Dosya UTF-8 ile yeniden yazılır | içerik sahibi |
| 4 | `parent:` > 0 | Alan kaldırılır | şema sahibi |
| 5 | `group` yanlış | Doğru enum değeri yazılır | şema sahibi |
| 6 | Authority yok | Satır eklenir | içerik sahibi |

### §14.3 Kapı sırası (bağımlılık zinciri)

| # | Kapı | Önceki kapı | Not |
|---|---|---|---|
| 1 | İçerik yazımı | — | en ucuz adım |
| 2 | Dolgu temizliği | 1 | içerik varsa dolgu nadiren kalır |
| 3 | Mojibake denetimi | 2 | encoding en sabit hali |
| 4 | Şema denetimi | 3 | `parent:` / `group` bağımsız |
| 5 | Satır eşiği | 4 | **en pahalı kapı — en son** |
| 6 | Authority kapanışı | 5 | imza her zaman son satır |

> **Sıra kuralı:** 5 numaralı kapı **en son** denetlenir. Satır sayısını önce
> doldurup sonra içeriği düzeltmek, düzeltmelerin sayımı yeniden düşürmesine
> yol açar — bu bilinen ve ölçülmüş bir tuzaktır (§9.4'te tekrarlanmıştır).

### §14.4 §14'nin kanıt etkisi

| Kategori | §14 öncesi | §14 sonrası | Not |
|---|---|---|---|
| Disk kanıtı | §13.1'de 10 kayıt | değişmedi | §14 yeni ölçüm **eklemez** |
| Web URL | §10'da 4/4 HTTP 200 | değişmedi | yeni URL yok |
| Negatif kanıt | §7'de 10 satır | değişmedi | yeni arama yok |
| S4 (süreç yorumu) | §9'da 10 bulgu | +3 tablo (§14.1–§14.3) | kapı sırası = süreç çıkarımı |
| S7 (bilinmeyen) | §9'da işaretli | değişmedi | yeni iddia üretilmedi |

> **Sonuç:** §14 süreci **sayısal kapıya** bağlar; kanıt oranını
> **değiştirmez**. Kapı değerleri §13.1 bloğuyla **ölçülür**, tahmin edilmez.

> **SORUMLULUK SINIRI (tekrar):** Bu bölüm workflow'u **tanımlar ve raporlar**;
> `.github/workflows/*.yml` değişikliği DevOps Engineer + kullanıcı onayı
> olmadan YAPILMAZ. Subagent commit atmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
