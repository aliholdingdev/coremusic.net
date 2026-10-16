---
title: "K011 · local-runtime — Bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: local-runtime
status: active
updated: 2026-10-15
group: mimari
---

# K011 · local-runtime — Bileşenler

> **Amaç:** local-runtime katmanının **dizin/dosya envanterini** (ölçülmüş sayımlar),
> bileşen → akış iz düğümü eşlemesini, bağımlılık grafiğini ve envanterden çıkan
> **boşlukları** belgeler. Tüm sayımlar 2026-10-15 repo ölçümüdür; doğrulanamayan
> iddia `⚠️ VERIFICATION REQUIRED`, repo'da olmayan varlık `PLANNED` ile işaretlenir.

## §GRUPLAR

> İki-eksenli gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın
> frontmatter'ında **son alan `group`**.

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Dizin envanteri | mimari | envanter | K011-application + local-runtime sayımı |
| 2 | §2 CLI betik envanteri | mimari | envanter | 4 betik · satır/exit/argv sayımları |
| 3 | §3 Paylaşılan kaynak envanteri | mimari | envanter | shared/src · tests · Media src |
| 4 | §4 Araç & gate envanteri | mimari | araç | scripts · workflows · composer.json |
| 5 | §5 Yüzey envanteri (CSS/SQL/JS) | mimari | envanter | üç yüzey sayımı + ilgisizlik kanıtı |
| 6 | §6 Ölçüm PowerShell blokları | mimari | yöntem | sayım komutları (metin) + sonuçlar |
| 7 | §7 Bileşen → akış iz düğümü | mimari | izleme | 5 düğümlük akış ↔ bileşen eşlemesi |
| 8 | §8 Bağımlılık grafiği | mimari | graf | operatör → betik → sınıf → çıktı |
| 9 | §9 Dış gate'ler | mimari | kapı | composer autoload · CI jobs |
| 10 | §10 Manuel ölçüm ↔ script ölçümü | mimari | karşılaştırma | ne ölçüldü / ne script'e ait |
| 11 | §11 Boşluklar | mimari | boşluk | PLANNED + ⚠️ VR listesi |
| 12 | §12 Açık sorular & gate özeti | mimari | belirsizlik | üst kapılara devir |

---

## §1 — Dizin envanteri (K011-application + local-runtime)

**Ölçüm 2026-10-15** (repo kökü, subagent shell):

| Hedef | Sayım | Kanıt |
|---|---|---|
| `.ai/architecture/K011-application/` alt dizin | **16** | `Get-ChildItem -Directory` sayımı |
| `.ai/architecture/K011-application/` üst düzey `.md` | **12** | `Get-ChildItem -File` sayımı |
| `local-runtime/` içeriği | **5 dosya** (tümü `.md`) | aşağıdaki tablo |
| `.ai/architecture/` kökünde `K011*` dizini | **1** (`K011-application`) | `-Filter "K011*"` tek isabet |

`local-runtime/` dosya envanteri (ad · bayt):

| Dosya | Bayt | Rol | Durum |
|---|---|---|---|
| `index.md` | 1.668 | çocuk indeksi (40–100 satır korunur) | mevcut · dokunulmadı |
| `local-runtime-kapsam.md` | 24.416 | kapsam (kapsam yerine geçer) | mevcut · **dokunulmadı** |
| `local-runtime-dogrulama.md` | 20.931 | kanıt/doğrulama | mevcut · **dokunulmadı** |
| `local-runtime-web-arastirma.md` | 20.943 | kanıt · havuz araştırması | bu görevde üretildi |
| `local-runtime-ornekler.md` | 26.012 | kod · dil blokları | bu görevde üretildi |
| `local-runtime-bilesenler.md` | (bu dosya) | mimari · envanter | bu görevde üretildi |

K011-application alt dizin adları (16): `application-services` · `application-testing` ·
`caching` · `commands` · `desktop` · `dto` · `dto-ve-mapper` · `electron` ·
`local-runtime` · `queries` · `routing` · `server-runtime` · `usecase` ·
`usecase-akislari` · `web` · `workflows`.

K011-application üst düzey `.md` dosyaları (12):

| # | Dosya | Kardeş grubu |
|---|---|---|
| 1 | `application-bagimliliklar.md` | mimari |
| 2 | `application-bilesenler.md` | mimari |
| 3 | `application-dogrulama.md` | kanıt |
| 4 | `application-evrensel-dil.md` | mimari |
| 5 | `application-kapsam.md` | mimari |
| 6 | `application-kararlar.md` | mimari |
| 7 | `application-kod-ornekleri.md` | kod |
| 8 | `application-master.md` | mimari |
| 9 | `application-riskler.md` | mimari |
| 10 | `application-test-stratejisi.md` | mimari |
| 11 | `application-web-arastirma.md` | kanıt |
| 12 | `index.md` | indeks |

**Sınır notu:** bu görev yalnız `local-runtime/` klasörüne yazar; üst dizindeki
12 dosyanın hiçbiri değiştirilmez (K011 kök `index.md` de orkestratöre aittir).

**Sayım bloğu (metin — yeniden çalıştırma orkestratöre aittir):**

```powershell
(Get-ChildItem ".ai\architecture\K011-application" -Directory).Count   # 16
(Get-ChildItem ".ai\architecture\K011-application" -File).Count        # 12
Get-ChildItem ".ai\architecture\K011-application\local-runtime" | Select-Object Name,Length
```

**Dosya: `C:\www\coremusic.net\.ai\architecture\K011-application\local-runtime\local-runtime-kapsam.md` · Satır: 18** — Amaç bloğu bu dosyayı "kapsam belgesi; kod kanıtı `-ornekler.md`'de, sayımlar `-bilesenler.md`'dedir" diye tanımlar (yapısal çapraz referans).

---

## §2 — CLI betik envanteri (4 betik · satır/exit/argv)

local-runtime'ın tek gerçek çalışma zamanı yüzeyi 4 CLI betiğidir (kapsam §3 ile aynı kümefir).

| Betik | Dosya | Satır | `exit(` | `$_SERVER` | `$argv` | `fwrite(` |
|---|---|---|---|---|---|---|
| api-key-create | `bin/api-key-create.php` | **494** | 1 | 3 | 6 | 19 |
| scan | `media.coremusic.net/bin/scan.php` | **261** | 5 | 0 | 1 | 23 |
| audit | `media.coremusic.net/bin/audit.php` | **499** | 4 | 0 | 1 | 10 |
| ingest | `media.coremusic.net/bin/ingest.php` | **346** | 11 | 0 | 1 | 25 |
| **Toplam** | 4 dosya | **1.600** | **21** | **3** | **9** | **77** |

Satır sayıları `[System.IO.File]::ReadAllLines($p).Length` ile alındı; token
sayımları dosya metni üzerine regex (`(?<![\w])exit\(` · `\$_SERVER` · `\$argv` · `fwrite\(`).

Kilit satır kanıtları:

**Dosya: `C:\www\coremusic.net\bin\api-key-create.php` · Satır: 180–182**

```php
public const EXIT_OK    = 0;
public const EXIT_ERROR = 1;
public const EXIT_USAGE = 2;
```

**Dosya: `C:\www\coremusic.net\bin\api-key-create.php` · Satır: 490–492** (doğrudan-çalıştırma guard'ı + tek `exit`)

```php
&& realpath((string) $_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)
exit(\CoreMusic\Console\ApiKeyCreateCommand::main($_SERVER['argv'] ?? []));
```

**Dosya: `C:\www\coremusic.net\bin\api-key-create.php` · Satır: 43** (tek autoload bağımlılığı)

```php
require_once dirname(__DIR__) . '/shared/vendor/autoload.php';
```

Media betikleri kendi autoload'unu kurar (vendor'a bağlı değil):

| Betik | `spl_autoload_register` | `require_once` (iç) | Son `exit` (final semantik) |
|---|---|---|---|
| scan | Satır 32 | Satır 39 | Satır 261 `exit($hata > 0 ? 1 : 0)` |
| audit | Satır 29 | Satır 36 | Satır 499 `exit($hata > 0 ? 1 : 0)` |
| ingest | Satır 25 | Satır 32 | Satır 346 `exit(... ? 1 : 0)` · ayrıca 293, 303 |

`$argv` okuma düğümleri: scan Satır 60 · audit Satır 53 · ingest Satır 52
(`array_slice($argv, 1)` deseni üçünde de aynı). `getenv(` **yalnız 1 dosyada**:
`bin/api-key-create.php` Satır 424 (`$value = getenv($key);`).

**Çekirdek yok sayım (323 vendor-dışı PHP dosyası, dosya bazlı):**

| İfade | İsabetli dosya |
|---|---|
| `putenv(` | **0** |
| `php_sapi_name` | **0** |
| `cli-server` | **0** |
| `php -S` | **0** |

→ Yerleşik sunucu/`putenv` yüzeyi bu repo'da **hiçbir dosyada yok** (havuz konusu
`features.commandline.webserver` ↔ repo boşluğu — kapsam §19 ile aynı sonuç).

---

## §3 — Paylaşılan kaynak envanteri (shared · tests · Media)

| Hedef | Sayım | Kanıt |
|---|---|---|
| `shared/src/` alt dizin | **20** | `Get-ChildItem -Directory` |
| `shared/src/` PHP dosyası | **165** | `-Recurse -Filter *.php` |
| `shared/tests/` PHP dosyası | **45** | `-Recurse -Filter *.php` |
| `media.coremusic.net/src/Media/` PHP | **5** | listeleme |

`shared/src/` 20 dizin: `AI` · `Api` · `Bootstrap` · `Cache` · `Component` ·
`Config` · `Contracts` · `Database` · `Device` · `Events` · `Exception` · `Log` ·
`Middleware` · `OAuth` · `PageRouter` · `Repository` · `Security` · `Session` ·
`Theme` · `ViewMode`.

`media.coremusic.net/src/Media/` 5 sınıf: `CatalogWriter.php` · `Slugger.php` ·
`Taxonomy.php` · `Ulid.php` · `Validator.php`.

**Uyarı — örtümsüz sayımlar (⚠️):** görev spec'i `shared/tests` için **47 dosya**
ve `assets.coremusic.net/js` için **98 dosya** öngörür; bu ölçüm **45** ve **95**
verir (fark sırasıyla 2 ve 3 dosya). İki sayı da doğrudan glob'dan gelir;
spec'teki 47/98 nereden geldiği **⚠️ VERIFICATION REQUIRED** (§11 madde 6).

Bileşen ↔ sınıf izi (betik → kullandığı sınıf kümesi; kapsam §11 ile aynı, bu
dönemde sınıf adları doğrulandı):

| Betik | Sınıf bağımlılığı | Nerede tanım |
|---|---|---|
| api-key-create | `CoreMusic\Contracts\Database\{IDatabaseManager,IDatabaseRegistry}` · `CoreMusic\Database\DatabaseRegistry` · `CoreMusic\Repository\ApiKeyRepository` · `CoreMusic\Security\UuidV7` | `use` bloğu Satır 48–52 |
| scan | `Media\CatalogWriter` · `Media\Ulid` · `Media\Validator` (+ `Media\Taxonomy` derin mod) | autoload L32–39 |
| audit | `Media\Ulid` · `Media\Validator` | autoload L29–36 |
| ingest | `Media\CatalogWriter` · `Media\Slugger` · `Media\Ulid` | autoload L25–32 |

**Dosya: `C:\www\coremusic.net\bin\api-key-create.php` · Satır: 48–52**

```php
use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Database\DatabaseRegistry;
use CoreMusic\Repository\ApiKeyRepository;
use CoreMusic\Security\UuidV7;
```

---

## §4 — Araç & gate envanteri (scripts · workflows · composer)

| Hedef | Sayım | İçerik |
|---|---|---|
| `.ai/scripts/*.ps1` | **6** | vault doğrulama betikleri (bu görevde **çalıştırılmadı** — yasak) |
| `.ai/scripts/*.mjs` | **2** | `validate.mjs` · `vault-utf8-writer.mjs` |
| `.github/workflows/` | **2** | `ci.yml` · `secret-scan.yml` |
| `composer.json` (vendor hariç) | **5** | api · auth · home · media · shared |
| kök `composer.json` | **YOK** | `Test-Path` → false |

**`ci.yml` job envanteri (6 job):**

| Job | Satır | Ne çalıştırır |
|---|---|---|
| `php-lint` | 25 | PHPStan (`composer stan`) |
| `php-test` | 66 | PHPUnit (`vendor/bin/phpunit`, Satır 105) |
| `composer-audit` | 110 | composer audit ×3 (shared/auth/home) |
| `js-test` | 142 | `npm ci` + Vitest unit |
| `js-coverage` | 160 | Vitest coverage |
| `js-e2e` | 178 | Playwright chromium e2e |

`secret-scan.yml`: tek job `gitleaks` (Satır 19, `Run GitLeaks` Satır 28).

**Gate boşluğu (kanıt):** `ci.yml` + `secret-scan.yml` içinde `scan.php`,
`audit.php`, `ingest.php`, `api-key-create`, `media.coremusic.net` araması
**0 isabet** — workflows'te `bin/` geçen tek satır `vendor/bin/phpunit`
(`ci.yml` Satır 105). Yani **4 CLI betiği CI tarafından hiç çağrılmaz**;
exit kodu sözleşmesi (kapsam §4) hiçbir pipeline'da test edilmez → §11 madde 1.

**`media.coremusic.net/composer.json` envanteri:**

```json
"bin": ["bin/scan.php", "bin/audit.php", "bin/ingest.php"],
"autoload": { "psr-4": { "Media\\": "src/Media/" } },
"require": { "php": "^8.4", "ext-json": "*", "ext-mbstring": "*", "ext-pdo": "*" }
```

`scripts.lint` 8 dosyaya `php -l` koşar (3 bin + 5 src). **Dosya:
`C:\www\coremusic.net\media.coremusic.net\composer.json` · Satır: `bin` bloğu.**

---

## §5 — Yüzey envanteri (CSS · SQL · JS)

### §5.1 CSS

| Hedef | Sayım |
|---|---|
| `assets.coremusic.net/Css/` | **94** dosya (11 katman; `01_Abstracts` 19) |
| `--/Css copy 2/` | **87** dosya |
| `--/Css copy 3/` | **98** dosya |
| `.claude/skills/` | **1** |
| **Toplam (vendor/node_modules hariç)** | **280** |

Not: `--/` dizini **git tarafından izlenen** bir klasördür (`git ls-files` altıda
`--/` ön eki = **297 dosya**, çalışma ağacında değişiklik yok). `01_Abstracts`
dışında terminal token'ı tüketimi **0** → `--bg-terminal`/`--text-terminal`
tanımlı ama kullanılmıyor → PLANNED (§11 madde 3).

### §5.2 SQL

| Hedef | Sayım |
|---|---|
| `.ai/.sql/**/*.sql` | **20** (`.ai` gizli — `hidden: true` ile sayıldı) |

`media_catalog.sql` içinde `ingest_batch` tablosu mevcuttur (**Satır: 406–421**);
buna **PHP tarafında yazan dosya 0** → yazar yok → PLANNED (§11 madde 4).

### §5.3 JS (ilgisizlik kanıtı dahil)

| Hedef | Sayım |
|---|---|
| vendor/node_modules hariç toplam JS ailesi | **104** (99 `.js` + 2 `.mjs` + 3 `.cjs`) |
| `assets.coremusic.net/` `.js` | **99** (`js/` 95 · `tests/` 3 · `vitest.config.js` 1) |
| `.spec.js` (assets) | **9** (`js/` içinde 7 · `tests/` içinde 2) |
| `.mjs` | 2 (`.ai/scripts/`) · `.cjs` 3 (`.claude/hooks/`) |

**local-runtime ↔ JS eşleşmesi = 0:** assets JS (95 dosya) üzerinde `\bCLI\b`,
`argv`, `getenv`, `exit(`, `terminal` aramaları **0 isabet** → JS yüzeyinde
local-runtime bileşeni **YOK** → PLANNED (§11 madde 2).

Tek **gerçek ama konu dışı** Node↔PHP dokunuş noktası:

**Dosya: `C:\www\coremusic.net\assets.coremusic.net\js\core\breakpoints.spec.js`
· Satır: 110–124** — test, `shared/src/Device/DeviceDetector.php` dosyasını okur
(Vitest köprüsü). Bu bir local-runtime (CLI) davranışı değildir; **konu
uydurularak kanıt sayılmaz**, yalnız gerçek-dokunuş olarak kayıtlıdır.

---

## §6 — Ölçüm PowerShell blokları (metin)

> Aşağıdaki bloklar bu dosyanın **kanıt metnidir**; yeniden çalıştırma ve gate
> doğrulaması orkestratöre aittir (subagent `.ai/scripts/*.ps1` çalıştırmaz).

```powershell
# 1) CLI betik satır sayımları
$files = @(
  "C:\www\coremusic.net\bin\api-key-create.php",
  "C:\www\coremusic.net\media.coremusic.net\bin\scan.php",
  "C:\www\coremusic.net\media.coremusic.net\bin\audit.php",
  "C:\www\coremusic.net\media.coremusic.net\bin\ingest.php")
foreach ($f in $files) { [System.IO.File]::ReadAllLines($f).Length }
# SONUÇ (2026-10-15): 494 · 261 · 499 · 346  → toplam 1600
```

```powershell
# 2) Vendor-dışı PHP envanteri (üst düzey kırılım)
$php = Get-ChildItem "C:\www\coremusic.net" -Recurse -Filter *.php -File |
  Where-Object { $_.FullName -notmatch '\\vendor\\|\\node_modules\\' }
$php.Count    # 323
$php | ForEach-Object { ($_.FullName.Substring(22) -split '\\')[0] } |
  Group-Object | Sort-Object Count -Descending
# SONUÇ: shared=218 · home.coremusic.net=41 · auth.coremusic.net=37 ·
#        api.coremusic.net=12 · media.coremusic.net=8 · .claude=4 · --=2 · bin=1
```

```powershell
# 3) Çekirdek yüzey taraması (323 dosya, dosya bazlı)
# putenv( → 0 · php_sapi_name → 0 · cli-server → 0 · php -S → 0
```

```powershell
# 4) Yüzey sayım/lari
(Get-ChildItem ".ai\scripts" -Filter *.ps1).Count          # 6
(Get-ChildItem ".github\workflows").Count                  # 2
(Get-ChildItem ".ai\.sql" -Recurse -Filter *.sql).Count    # 20
(Get-ChildItem "assets.coremusic.net\Css" -Recurse -Filter *.css).Count  # 94
(Get-ChildItem "shared\src" -Recurse -Filter *.php).Count  # 165
(Get-ChildItem "shared\tests" -Recurse -Filter *.php).Count # 45
```

```powershell
# 5) CI ↔ CLI kesişimi (workflow içinde betik adı araması)
Select-String -Path ".github\workflows\*.yml" -Pattern `
  'scan\.php','audit\.php','ingest\.php','api-key-create','media.coremusic.net'
# SONUÇ: 0 isabet (betik adları hiç geçmiyor)
```

---

## §7 — Bileşen → akış iz düğümü

Akış kapsamtan alınmıştır (`local-runtime-kapsam.md` §12 — üretim → denetim →
taşıma); her düğüm bu dosyada **ölçülmüş** bileşene bağlanır.

| Düğüm | Akış | Bileşen (dosya) | Giriş düğümü | Çıkış / exit | Kanıt |
|---|---|---|---|---|---|
| `[1]` | `ingest --source "DIZIN"` (dry-run) | `ingest.php` (346 satır) | `$argv` L52 | `reports/*.csv` · exit 0/1/2 | L46/60/68/72/78/84 usage-exitleri |
| `[2]` | `ingest --commit` + STDIN "evet" | `ingest.php` + `Media\CatalogWriter` | STDIN onay | medya ağacına kopyalama | onay kapısi: kapsam §13 madde 3 |
| `[3]` | `scan [--deep]` | `scan.php` (261 satır) + `Media\{CatalogWriter,Ulid,Validator}` | `$argv` L60 | `catalog/catalog.jsonl` + MySQL | final exit L261 |
| `[4]` | `audit [--deep]` (salt okunur) | `audit.php` (499 satır) + `Media\{Ulid,Validator}` | `$argv` L53 | `SEVIYE\|yol\|kural\|neden` raporu | final exit L499 |
| `[5]` | CI/CD gate | **yok** — workflows 4 betiği çağırmıyor | exit kodu bekleniyor | PASS/BLOCK **tanımsız** | §4 gate boşluğu → §11 madde 1 |
| `[0]` | operatör: API anahtarı üretimi | `api-key-create.php` (494 satır) | `$_SERVER['argv']` L492 · `getenv` L424 | stdout anahtar + DB hash | exit sabitleri L180–182 |

**Kritik iz notu:** `[5]` düğümü envanterde ** bileşenle eşleşmiyor** — akış
şemasındaki CI kapısı repo'da bir "CLI çalıştırıcı" olarak yok; yalnız job'lar
var (§4). Bu, akış ile envanter arasındaki tek kırık bağdır ve **kapatılması
gerekmez, belgelenmesi gerekir** (PLANNED değil; mevcut_durum).

`[0]` düğümü `[1]–[4]` medya akışının parçası değildir (farklı namespace
`CoreMusic\Console`, farklı autoload zinciri) — kapsam §11 notu ile aynı.

---

## §8 — Bağımlılık grafiği

```text
Operatör (konsol)
  │
  ├─> bin/api-key-create.php ── require L43 ──> shared/vendor/autoload.php
  │       ├─> $_SERVER['argv'] (L492) · getenv() (L424) · DB_* çevresel
  │       ├─> CoreMusic\Contracts\Database\* · DatabaseRegistry
  │       ├─> CoreMusic\Repository\ApiKeyRepository · Security\UuidV7
  │       └─> guard: realpath(SCRIPT_FILENAME)===realpath(__FILE__) (L490)
  │
  ├─> media/bin/scan.php ── spl_autoload_register (L32) ──> src/Media/*
  │       ├─> $argv (L60) · Media\{CatalogWriter,Ulid,Validator}
  │       └─> catalog/catalog.jsonl + MySQL (opsiyonel PDO)
  │
  ├─> media/bin/audit.php ── spl_autoload_register (L29) ──> src/Media/*
  │       ├─> $argv (L53) · Media\{Ulid,Validator}
  │       └─> STDOUT/STDERR rapor (salt okunur · yazma YOK)
  │
  └─> media/bin/ingest.php ── spl_autoload_register (L25) ──> src/Media/*
          ├─> $argv (L52) · Media\{CatalogWriter,Slugger,Ulid}
          ├─> STDIN "evet" onayı (--commit)
          └─> reports/ingest-*.csv (dry-run) · medya ağacı (--commit)

Ayrı yığın (local-runtime DIŞI ama betik bağımlılığı):
  shared/vendor/autoload.php  <──  shared/composer.json  <──  CI "Composer install (shared)"
  media/composer.json (psr-4 Media\) ── vendor/ (media kökü) ── LINT adımı (php -l ×8)
```

**Graf özellikleri (ölçüm):** iki bağımsız autoload ailesi (`CoreMusic\` vendor
· `Media\` local `spl_autoload_register`) — bir betik bile diğerinin ailesine
sınır geçmez. Kök `composer.json` olmadığı için shared autoload'u **yalnız**
`shared/` içinden çözülür (api-key L43 bunu zorunlu kılar).

---

## §9 — Dış gate'ler

| Gate | Dosya | local-runtime ile ilişki | Durum |
|---|---|---|---|
| Composer install (shared/auth/home) | `ci.yml` Satır 48/52/56 | api-key-create autoload'u için **ön koşul** | IMPLEMENTED (CI) |
| PHPStan | `ci.yml` Satır 62 | 4 betik dahil repo geneli statik analiz | IMPLEMENTED |
| PHPUnit | `ci.yml` Satır 66/105 | `shared/tests/` 45 test — CLI betik testi **yok** | KISMEN |
| composer audit | `ci.yml` Satır 110+ | bağımlılık CVE taraması | IMPLEMENTED |
| Vitest / Playwright | `ci.yml` Satır 142/160/178 | JS yüzeyi — local-runtime ile **ilgisiz** | IMPLEMENTED (ilgisiz) |
| GitLeaks | `secret-scan.yml` | secret sızıntısı | IMPLEMENTED |
| `php -l` (8 dosya) | `media/composer.json` scripts.lint | yalnız media bin+src | IMPLEMENTED (lokal, CI'da değil) |
| Exit kodu doğrulaması (0/1/2) | **yok** | kapsam §4 sözleşmesi testsiz | **GAP → §11 madde 1** |

`⚠️ VERIFICATION REQUIRED`: CI'nin **gerçekten geçtiği** (green build) hiçbir
ölçümde kanıtlanmadı — workflow dosyaları mevcut, koşum durumu bilinmiyor
(AGENTS.md §25.2 ile aynı kayıtlı belirsizlik).

**Gate ↔ betik kapsama matrisi:**

| Betik | CI'da çağrılıyor mu? | `php -l` lint | Unit test | Smoke/run |
|---|---|---|---|---|
| `bin/api-key-create.php` | Hayır (0 isabet) | repo geneli PHPStan dolaylı | Yok | Yok → GAP |
| `media/bin/scan.php` | Hayır (0 isabet) | `composer lint` (lokal) | Yok | Yok → GAP |
| `media/bin/audit.php` | Hayır (0 isabet) | `composer lint` (lokal) | Yok | Yok → GAP |
| `media/bin/ingest.php` | Hayır (0 isabet) | `composer lint` (lokal) | Yok | Yok → GAP |

**Toplam:** 4/4 betik CI smoke'suz · lokal lint 3/4 (api-key-create `composer lint`
dışında; PHPStan'a takılır) · exit kodu testi **0/4**.

Ayrıca `.ai/scripts/` içindeki 6 PowerShell betiği **vault doğrulama** betiğidir
(ekran/frontmatter/link kontrolleri) — local-runtime CLI betikleriyle **ilgisi
yoktur**; bu görevde hiçbiri çalıştırılmadı (§10).

---

## §10 — Manuel ölçüm ↔ script ölçümü

| Kalem | Bu dosyada | Orkestratör gate'i |
|---|---|---|
| Satır sayımları (`ReadAllLines`) | ölçüldü (494/261/499/346) | yeniden sayar |
| Glob sayımları (PHP/JS/CSS/SQL/ps1) | ölçüldü (§1–§5) | yeniden sayar |
| Workflow ↔ CLI kesişimi | ölçüldü (0 isabet) | yeniden sayar |
| `.ai/scripts/*.ps1` çalıştırma | **YAPILMADI (yasak)** | çalıştırır |
| URL curl 200 doğrulaması | web-arastirma.md'de (5/5) | tekrar doğrular |
| `git commit` | **YAPILMADI (yasak)** | karar/orkestratör |

---

## §11 — Boşluklar (envanterden çıkan PLANNED / ⚠️ listesi)

| # | Boşluk | İşaret | Ölçüm |
|---|---|---|---|
| 1 | 4 CLI betiği CI'da hiç çağrılmıyor; exit 0/1/2 sözleşmesi testsiz | **GAP (mevcut durum)** | workflows araması 0 isabet |
| 2 | JS yüzeyinde local-runtime bileşeni yok (`CLI`/`argv`/`getenv`/`exit(` = 0) | **PLANNED** | 95 assets JS taraması |
| 3 | `--bg-terminal`/`--text-terminal` token'ları yalnız `01_Abstracts`'te tanımlı; tüketim 0 | **PLANNED** | 5/5 tanımlı · dışarıda 0 |
| 4 | `ingest_batch` tablosu var (media_catalog.sql L406–421) ama PHP yazıcısı 0 | **PLANNED** | grep 0 |
| 5 | `php_sapi_name` guard'ı hiç kullanılmıyor; guard `realpath` (api-key L490) | **⚠️ VERIFICATION REQUIRED** | 323 dosyada 0 |
| 6 | spec 47 test / 98 JS derken ölçüm 45 / 95 | **⚠️ VERIFICATION REQUIRED** | iki taraf da glob |
| 7 | 4 betik PHP 8.4'te hiç çalıştırılmadı (shell yok) — exit kodları **koşulmamış** | **⚠️ VERIFICATION REQUIRED** | yalnız kod okuması |
| 8 | `putenv` havuz konusu: repo'da `putenv(` = 0, `getenv` = 1 (yalnız okuma) | **PLANNED** (yazma yüzeyi yok) | 323 dosya taraması |
| 9 | `redirect chain` uzunluğu (`curl -L`) sayılmadı — tek atışta 200 alındı | **⚠️ VERIFICATION REQUIRED** | zincir atlanmadı |
| 10 | Havuz envanter dosyası adı: `k011-urls.txt` (spec) diskte YOK; `k011-url-inventory.txt` (1687 satır) kullanıldı — her iki ölçümde de kesişim 0 | **⚠️ VERIFICATION REQUIRED** | web-arastirma §4 |
| 11 | Kök `composer.json` yok — autoload yalnız `shared/` ve `media/` içinden | envanter gerçeği (kusur değil) | `Test-Path` false |
| 12 | `--/` (Css copy 2/3) 297 izlenen dosya — canlı CSS katmanının kopyası | envanter gerçeği (taşınmaz, silinmez) | `git ls-files` |

---

## §12 — Açık sorular, çapraz referanslar & gate özeti

**Açık sorular:**

1. Exit kodu sözleşmesi CI'a taşınacak mı (`php bin/*.php` smoke job)? — Sahipsiz.
2. `--/` kopya dizini bilinçli yedek mi, unutulmuş kopya mı? — Sahipsiz (dokunma yasak).
3. Spec'teki 47 test / 98 JS sayısı hangi ölçümün sonucu? — **⚠️ VERIFICATION REQUIRED**.
4. `ingest_batch` yazıcısı hangi fazda geliyor (ADR-092 dry-run hattı mı)? — PLANNED.
5. `putenv` politikası: environment yazma ihtiyacı olursa nereye konur? — Sahipsiz.

**Çapraz referanslar:**

| Dosya | İlişki |
|---|---|
| `local-runtime-kapsam.md` | §3 (4 betik) · §11 (graf) · §12 (akış) bu dosyayla aynı kümefir |
| `local-runtime-ornekler.md` | dil bloklarının `Dosya/Satır` kanıtları §2 ile kilitli |
| `local-runtime-web-arastirma.md` | havuz ↔ repo boşlukları §11 madde 8/10 ile örtüşür |
| `local-runtime-dogrulama.md` | gate PASS/BLOCK tabloları bu dosyanın sayımlarını kullanır |

**Gate özeti (bu dosya):**

- **Bölümler:** §1–§12 (§GRUPLAR ile birebir) ✓
- **Ölçülmüş sayımlar:** 16/12 dizin-dosya · 1.600 satır CLI · 323 PHP ·
  165+45 shared · 94 CSS · 20 SQL · 99 JS · 6 ps1 · 2 workflow · 5 composer ✓
- **Kanıt bloğu (`Dosya · Satır`):** 5 (kapsam L18 · api-key L180–182 ·
  L490–492 · L43 · L48–52) ✓
- **Boşluk listesi:** 12 madde (4 PLANNED · 5 ⚠️ VR · 3 envanter gerçeği) ✓
- **Dış havuz URL:** **0** (URL'ler yalnız `local-runtime-web-arastirma.md`'de) ✓
- **`.ai/scripts/*.ps1` çalıştırma:** YOK ✓ · **`git commit`:** YOK ✓
- **Kaynak kod değişikliği (`*.php|*.js|*.css|*.sql`):** 0 ✓

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
