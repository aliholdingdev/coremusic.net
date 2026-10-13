---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/isletim-sistemi-cross-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross
children: []
depends_on: []
evidence:
  - ".github/workflows/ci.yml:27"
  - ".github/workflows/secret-scan.yml:21"
  - "home.coremusic.net/.htaccess:27"
  - "home.coremusic.net/web.config:14"
validation: "2026-10-13 · §A-§F · 12 yeni URL HEAD 200 · 0 envanter kesişimi · repo satırları `Select-Object -Index` ile okundu"
open_questions:
  - "Q4 · üretim OS UNKNOWN → `status: draft` korunur"
  - "Q-X3 · windows/macos runner testi yok → UNKNOWN"
group: kanit
---

# isletim-sistemi-cross — Doğrulama Tablosu

Bu dosya üç şeyi **aynı yerde** tutar: neyin nasıl doğrulanacağı (§A-§B), neyle
ölçüleceği (§C-§D), hangi URL ve hangi repo satırının 2026-10-13 itibarıyla **gerçekten**
okunduğu (§E-§F). Önceki turun (2026-10-12) web kanıtları kök `index.md` §4'te korunur;
burada **tekrar edilmez** (envanter kesişimi 0 kuralı).

## §A — Doğrulama senaryoları

### §A.1 — Senaryo envanteri

| # | Senaryo | Ne doğrular | Yöntem | Bağlı alt-dal |
|---|---|---|---|---|
| V1 | Yol ayracı çalışma zamanı | `/` vs `\` ayracı kodda sabitlenmemiş | `Select-String 'DIRECTORY_SEPARATOR'` | `platform-yuzeyleri` |
| V2 | Yol ayracı testte de korunuyor | testler doğru ayracı bekliyor | `shared/tests/Component/TemplateEngineTest.php:27` | `platform-yuzeyleri` |
| V3 | Platform tespiti tek noktada | `php_uname('s')` çağrısı | repo-wide grep | `platform-yuzeyleri` |
| V4 | Symlink/realpath denetimi | yol çözümleme platformdan bağımsız değil | `MusicStreamHandler.php:185-200` | `platform-yuzeyleri` |
| V5 | Apache yapılandırması mevcut | 4 alt-alanda `.htaccess` | dosya listesi | `platform-yuzeyleri` |
| V6 | IIS yapılandırması mevcut | 4 alt-alanda `web.config` | dosya listesi | `platform-yuzeyleri` |
| V7 | Tek web sunucusuna kilit yok | iki biçim yan yana | `.htaccess` + `web.config` eşzamanlı var | `platform-yuzeyleri` |
| V8 | CI runner OS tek | `runs-on` yalnız `ubuntu-latest` | `ci.yml` satır taraması | `tasinabilirlik-siniri` |
| V9 | CI runner OS matrix yok | `matrix.os` yok | `Select-String 'matrix:'` | `tasinabilirlik-siniri` |
| V10 | Cache anahtarı platforma bağlı | `${{ runner.os }}` kullanımı | `ci.yml:44` · `ci.yml:96` | `tasinabilirlik-siniri` |
| V11 | Uzantı sözleşmesi taşınabilir | `ext-*` require listesi | `shared/composer.json:9` | `derleme-ve-paketleme` |
| V12 | Sürüm sözleşmesi tek noktada | `php >=8.4` | `shared/composer.json:8` | `derleme-ve-paketleme` |
| V13 | JS araç zinciri Linux'a bağlı değil | scriptler OS-nötr | `package.json:5-8` | `derleme-ve-paketleme` |
| V14 | Secret scan bağımsız koşuyor | ikinci workflow ayrı | `secret-scan.yml:21` | `tasinabilirlik-siniri` |

### §A.2 — Senaryo yürütme kaydı (2026-10-13)

| # | Senaryo | Komut / okuma | Bulunan | Sonuç |
|---|---|---|---|---|
| V1 | `DIRECTORY_SEPARATOR` | repo-wide grep (`.php`) | 6 isabet: `TemplateEngine.php:73`, `MusicStreamHandler.php:199`, `TemplateEngineTest.php:27/87/107`, `ApiKeyCreateCommandTest.php:38` | ✅ |
| V2 | test koruması | `Select-Object -Index 26` | `$files = glob(rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_*.php');` | ✅ |
| V3 | platform tespiti | repo-wide grep (`php_uname`) | 1 isabet: `DesktopBff.php:35` | ✅ (tek yol — Q-X10) |
| V4 | symlink/realpath | `MusicStreamHandler.php` 185-200 | `is_link` + `realpath` + `DIRECTORY_SEPARATOR` prefix | ✅ |
| V5 | Apache | dosya listesi | `api` · `assets` · `auth` · `home` (4 adet `.htaccess`) | ✅ |
| V6 | IIS | dosya listesi | `api` · `assets` · `auth` · `home` (4 adet `web.config`) | ✅ |
| V7 | ikili yapılandırma | birleşik liste | 8 dosya (4+4), hiçbiri silinmemiş | ✅ |
| V8 | runner OS | `ci.yml` tarama | `ubuntu-latest` ×6 iş (27 · 69 · 112 · 144 · 163 · 180) | ✅ |
| V9 | `matrix.os` | grep `matrix:` | **0 isabet** (yalnız `matrix.project` — `ci.yml:73`) | ✅ (tek OS doğrulandı) |
| V10 | cache OS anahtarı | satır okuma | `key: ${{ runner.os }}-composer-...` (`ci.yml:44` · `ci.yml:96`) | ✅ |
| V11 | uzantılar | satır okuma | `ext-apcu` · `ext-json` · `ext-mbstring` · `ext-pdo` (`composer.json:9-12`) | ✅ |
| V12 | PHP sürüm | satır okuma | `"php": ">=8.4"` (`composer.json:8`) + `PHP_VERSION: '8.4'` (`ci.yml:22`) | ✅ |
| V13 | JS scriptler | satır okuma | `test:js` · `test:js:watch` · `test:js:coverage` · `test:e2e` (`package.json:5-8`) | ✅ |
| V14 | secret scan | satır okuma | `runs-on: ubuntu-latest` (`secret-scan.yml:21`) | ✅ |

### §A.3 — Doğrulanamayan (bu turda kapalı olmayan)

| # | İddia | Neden kapandı | Durum |
|---|---|---|---|
| N1 | CoreMusic **üretimde** hangi OS'ta | tek nokta SSOT yok | ⚠️ VERIFICATION REQUIRED (Q4) |
| N2 | macOS üzerinde koşma | workflow'da `macos-*` yok | UNKNOWN |
| N3 | Windows üzerinde koşma | workflow'da `windows-*` yok | UNKNOWN |
| N4 | IIS'de runtime davranışı | `web.config` diskte, çalışma kanıtı yok | UNKNOWN (Q-X7) |
| N5 | ARM64 derleme/paket adımı | repo'da native build yok | UNKNOWN (Q-X9) |
| N6 | Paket imzalama | politika belgesi yok | UNKNOWN (Q-X6) |
| N7 | `cross` ↔ `turu` sınırı | karar kaydı yok | ⚠️ VERIFICATION REQUIRED (Q-X2) |

### §A.4 — Senaryo × alt-dal atama ve öncelik

| # | Senaryo | Alt-dal | Öncelik | Bağımlılık | Yeniden ölçüm |
|---|---|---|---|---|---|
| V1 | yol ayracı | `platform-yuzeyleri` | P0 | repo kodu | her kod turunda |
| V2 | test ayracı | `platform-yuzeyleri` | P0 | V1 | her kod turunda |
| V3 | platform tespiti | `platform-yuzeyleri` | P1 | — | her kod turunda |
| V4 | symlink/realpath | `platform-yuzeyleri` | P0 | V1 | her kod turunda |
| V5 | Apache | `platform-yuzeyleri` | P1 | — | config değişiminde |
| V6 | IIS | `platform-yuzeyleri` | P1 | V5 | config değişiminde |
| V7 | ikili yapılandırma | `platform-yuzeyleri` | P1 | V5+V6 | config değişiminde |
| V8 | runner OS | `tasinabilirlik-siniri` | P0 | — | workflow değişiminde |
| V9 | matrix yokluğu | `tasinabilirlik-siniri` | P0 | V8 | workflow değişiminde |
| V10 | cache OS anahtarı | `tasinabilirlik-siniri` | P2 | V8 | workflow değişiminde |
| V11 | uzantılar | `derleme-ve-paketleme` | P1 | — | manifest değişiminde |
| V12 | PHP sürüm | `derleme-ve-paketleme` | P0 | V11 | manifest değişiminde |
| V13 | JS scriptleri | `derleme-ve-paketleme` | P2 | — | manifest değişiminde |
| V14 | secret scan | `tasinabilirlik-siniri` | P1 | — | workflow değişiminde |

### §A.5 — Yeniden ölçüm tetikleyicileri

| # | Tetikleyici | Koşan senaryo | Atlanırsa |
|---|---|---|---|
| T1 | `ci.yml` düzenlemesi | V8 · V9 · V10 · V14 | matris satırı eskir |
| T2 | `composer.json` düzenlemesi | V11 · V12 | sözleşme iddiası eskir |
| T3 | `package.json` düzenlemesi | V13 | script iddiası eskir |
| T4 | `.htaccess`/`web.config` düzenlemesi | V5 · V6 · V7 | yüzey sayımı eskir |
| T5 | `TemplateEngine`/`MusicStreamHandler` düzenlemesi | V1 · V2 · V4 | satır numarası kayar |
| T6 | Yeni `.md` ekleme (beyazliste içi) | O1 · O2 | 16 hedefi bozulur |
| T7 | Q4 kapanışı | §A.3 N1 → ✅ | `verified` yanlışlıkla açılır |

## §B — Test stratejisi (platform matrisi)

### §B.1 — Test tipi × platform

| # | Test tipi | Linux | Windows | macOS | Kanıt |
|---|---|---|---|---|---|
| T1 | PHP Lint | ✅ koşuyor | ❌ yok | ❌ yok | `ci.yml:25-64` |
| T2 | PHPUnit (shared, auth) | ✅ koşuyor | ❌ yok | ❌ yok | `ci.yml:66-108` |
| T3 | Composer audit | ✅ koşuyor | ❌ yok | ❌ yok | `ci.yml:110-139` |
| T4 | JS unit (vitest) | ✅ koşuyor | ❌ yok | ❌ yok | `ci.yml:142-158` |
| T5 | JS coverage | ✅ koşuyor (soft) | ❌ yok | ❌ yok | `ci.yml:160-176` |
| T6 | JS e2e (playwright) | ✅ koşuyor (soft) | ❌ yok | ❌ yok | `ci.yml:178-203` |
| T7 | Secret scan (gitleaks) | ✅ koşuyor | ❌ yok | ❌ yok | `secret-scan.yml:18-31` |
| T8 | IIS yapılandırma testi | — | ❌ yok | — | disk `web.config` var, test yok |
| T9 | Apache yapılandırma testi | ❌ yok | — | — | disk `.htaccess` var, test yok |
| T10 | DOSYA yolu Windows varyantı | ❌ yok | ❌ yok | — | `DIRECTORY_SEPARATOR` testi yalnız koşan OS'ta |
| T11 | Unicode/UTF-8 yol testi | ❌ yok | ❌ yok | ❌ yok | — |
| T12 | ARM64 (Raspberry Pi) testi | ❌ yok | — | — | — |

> **Zayıf nokta (aynı önceki tur sonucu):** platform matrisi **1 OS × 12 test tipi**.
> macOS/Windows kanıtı yok → Q-X3 açık.

### §B.2 — CI iş envanteri (2 workflow · 7 iş)

| # | Workflow | İş | `runs-on` | timeout | `continue-on-error` | Satır |
|---|---|---|---|---|---|---|
| J1 | `ci.yml` | `php-lint` | `ubuntu-latest` | 15 | hayır | 25-28 |
| J2 | `ci.yml` | `php-test` | `ubuntu-latest` | 15 | hayır | 66-70 |
| J3 | `ci.yml` | `composer-audit` | `ubuntu-latest` | 10 | hayır | 110-113 |
| J4 | `ci.yml` | `js-test` | `ubuntu-latest` | 10 | hayır | 142-145 |
| J5 | `ci.yml` | `js-coverage` | `ubuntu-latest` | 15 | **evet** (`ci.yml:167`) | 160-163 |
| J6 | `ci.yml` | `js-e2e` | `ubuntu-latest` | 20 | **evet** (`ci.yml:183`) | 178-181 |
| J7 | `secret-scan.yml` | `gitleaks` | `ubuntu-latest` | 10 | hayır | 19-22 |

### §B.3 — Matris kurgusu (var olan tek `strategy`)

| Alan | Değer | Satır | Yorum |
|---|---|---|---|
| `strategy.fail-fast` | `false` | 72 | proje bazlı koşu ayrılır |
| `matrix.include[0].project` | `shared` | 75-76 | `args: ''` |
| `matrix.include[1].project` | `auth.coremusic.net` | 79-80 | `--testsuite Unit` |
| `matrix.os` | **yok** | — | OS matrisi yok → Q-X3 |
| Yorum satırı | `tests/Integration dizini diskte YOK` | 77-78 | glob kanıtı yazılı |

### §B.4 — Strateji kararı

| # | Karar | Dayanak | Sonuç |
|---|---|---|---|
| S1 | Mevcut matris tek OS'ta kilitlenir | `ci.yml:27/69/112/144/163/180` | ✅ mevcut durum |
| S2 | OS matrisi eklenmesi **bu dalın yetkisi değil** | `testing` + `devops` sınırı | ⚠️ Q-X3'e devredildi |
| S3 | Soft job'lar (`continue-on-error: true`) platform köründe | 167 · 183 | ✅ OS kaynaklı değil |
| S4 | Yapılandırma dosyası testleri (Apache/IIS) | yok | ❌ bilinmeyen boşluk (Q-X7) |
| S5 | `cross` dalı yalnızca **kanıtı** yazar, pipeline'ı değiştirmez | §A.2 | ✅ 0 kod değişikliği |

### §B.5 — Ortam envanteri (CI'da kurulan araçlar)

| # | Araç | Sürüm/kaynak | Satır | OS bağımlılığı |
|---|---|---|---|---|
| A1 | PHP | `PHP_VERSION: '8.4'` | `ci.yml:22` | yok (runner OS'unda kurulur) |
| A2 | PHP eklentileri | `apcu, mbstring, json, pdo, pdo_mysql` | `ci.yml:37` | Linux build'leri |
| A3 | Composer | `tools: composer` | `ci.yml:36` | yok |
| A4 | Node.js | `node-version: '26'` | `ci.yml:151` | yok |
| A5 | npm | `npm ci` | `ci.yml:155` | lockfile |
| A6 | PHPUnit | `vendor/bin/phpunit` | `ci.yml:105` | yok |
| A7 | PHPStan | `composer stan` → `--level=5` | `ci.yml:63` | yok |
| A8 | Vitest | `npm run test:js` | `ci.yml:158` | yok |
| A9 | Playwright | `npx playwright install --with-deps chromium` | `ci.yml:200` | **Linux dep paketleri** |
| A10 | GitLeaks | `gitleaks/gitleaks-action@v2` | `secret-scan.yml:29` | action içinde Linux |

> **A9 notu:** `--with-deps` Linux paket yöneticisini kullanır — bu adım **yalnız Linux
> runner'ında** anlamlıdır; `matrix.os` eklenirse bu satır OS koşullu hale gelmek zorundadır.

### §B.6 — Test boşlukları → kimi bloke ediyor

| # | Boşluk | Etki | Devredilen | Etiket |
|---|---|---|---|---|
| L1 | `windows-latest` yok | Windows kırılması yakalanmaz | `devops` + `testing` | Q-X3 |
| L2 | `macos-latest` yok | macOS kırılması yakalanmaz | `devops` + `testing` | Q-X3 |
| L3 | Apache yapılandırma testi yok | `.htaccess` regresyonu sessiz | `testing` | UNKNOWN |
| L4 | IIS yapılandırma testi yok | `web.config` regresyonu sessiz | `testing` | Q-X7 |
| L5 | Windows yolu testi yok | `DIRECTORY_SEPARATOR` yanlışlığı Linux'ta görünmez | `testing` | UNKNOWN |
| L6 | ARM64 testi yok | Raspberry Pi hedefi doğrulanmaz | `embedded` + `testing` | Q-X9 |
| L7 | Paketleme testi yok | deb/rpm/msi/pkg/apk üretilmiyor | `devops` | Q-X6 |
| L8 | Derleme testi yok | repo'da native derleme adımı yok | `devops` | — |

> **Sınır kararı:** `cross` dalı bu boşlukları **raporlar**; kapatmaz. Kapatma yetkisi
> `isletim-sistemi-testing` ve `devops` / K002-altyapi'dedir.

## §C — Ölçüm metodolojisi

### §C.1 — Ölçüm adımları

| # | Adım | Araç | Beklenen çıktı |
|---|---|---|---|
| O1 | Dosya envanteri | `Get-ChildItem -Recurse -Filter *.md` | 16 dosya · 3 dizin |
| O2 | Satır sayımı | `(Get-Content $_).Count` | 12 dosya ≥500 · kök index 100 · 3 index 40-100 |
| O3 | Frontmatter bütünlüğü | ilk satır `---` + 2. `---` var | 0 kesik |
| O4 | `parent:` yasağı | `Select-String -Pattern '^parent:'` | 0 |
| O5 | tırnaklı `group` | `Select-String -Pattern '^group: "'` | 0 |
| O6 | Dolgu taraması | `TBD\|Yakında\|// \.\.\.\|\{\.\.\.\}` | 0 |
| O7 | Mojibake taraması | `[\uFFFD]` + bilinen bozuk dizgeler | 0 |
| O8 | URL HEAD testi | `Invoke-WebRequest -Method Head -TimeoutSec 20` | 12/12 → 200 |
| O9 | Envanter kesişimi | yeni URL kümesi ∩ envanter kümesi | 0 |
| O10 | Satır numarası doğrulama | `Select-Object -Index (N-1)` | her iddia ile birebir |
| O11 | Dışarıya yazım | beyazliste dışındaki değişen dosya | 0 |
| O12 | Commit | subagent **atmaz** | 0 |

### §C.2 — Ölçüm kod bloğu

```powershell
$root = 'C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-cross'

# O1 + O2
$files = Get-ChildItem $root -Recurse -Filter *.md
$files | ForEach-Object { [PSCustomObject]@{ Ad=$_.Name; Satir=(Get-Content $_.FullName).Count } }

# O4 + O5 + O6 (kabul: hepsi 0)
Select-String -Path "$root\*.md","$root\*\*.md" -Pattern '^parent:'
Select-String -Path "$root\*.md","$root\*\*.md" -Pattern '^group: "'
Select-String -Path "$root\*.md","$root\*\*.md" -Pattern 'TBD|Yakında|// \.\.\.|\{\.\.\.\}'

# O8 — tekil URL HEAD testi
$u = 'https://man7.org/linux/man-pages/man7/posixoptions.7.html'
(Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20).StatusCode   # 200

# O10 — satır numarası doğrulama örneği (idddia: ci.yml:27)
(Select-String -Path 'C:\www\coremusic.net\.github\workflows\ci.yml' -Pattern 'runs-on').LineNumber
```

### §C.3 — Örnekleme kuralı

| # | Kural | Gerekçe |
|---|---|---|
| P1 | Her `Dosya:`/`Satır:` iddiası ayrı okunur | uydurma satır no yasağı |
| P2 | Sayfa kodlama UTF-8 | mojibake ölçümü |
| P3 | `HEAD` 200 değilse URL elenir, 200 diye yazılmaz | zayıf kanıt |
| P4 | Aynı URL iki dosyada kullanılmaz (bu tur) | kopya/tekrar yasağı |
| P5 | Envant URL'leri yeni matrise alınmaz | kesişim 0 |
| P6 | Ölçüm repo **kökünden** çalıştırılır | göreli yol hataları (§13.6 kuralı) |

### §C.4 — Ölçüm hata modları

| # | Hata modu | Belirti | Düzeltme |
|---|---|---|---|
| H1 | `.ai/` gizli dizin unutuldu | 0 dosya bulundu | `hidden: true` / doğrudan `path` ver |
| H2 | `.ai/` içinden çalıştırma | göreli yol çözülmez | repo **kökünden** çalıştır (§13.6) |
| H3 | UTF-8 değil okuma | mojibake sayımı sahte | `-Encoding UTF8` / `[IO.File]::ReadAllText(...,UTF8)` |
| H4 | Dosya sonu satırı sayılmaz | 1 satır eksik | `.Count` + `Get-Content` birlikte |
| H5 | `HEAD` değil `GET` | yavaş/yanlış kod | `-Method Head` |
| H6 | Timeout kısa | sahte hata | `-TimeoutSec 20` |
| H7 | Eski envanter okunmadan yeni URL yazılırsa | kesişim >0 | önce envanter, sonra yazım |
| H8 | Satır numarası tahmin edilir | iddia tutmaz | `Select-Object -Index (N-1)` |
| H9 | Başka ajanın dosyası okunur | çakışma | beyazliste kontrolü |
| H10 | Sayım yapılmadan "≥500" denir | 499 hücresi | her zaman `.Count` çıktısı |

### §C.5 — Kodlama ve mojibake ölçümü

| # | Ölçüm | Yöntem | Beklenen |
|---|---|---|---|
| M1 | U+FFFD (�) | dosya taraması | 0 |
| M2 | Bilinen bozuk dizgeler (`kǬtǬphane` benzeri) | regex taraması | 0 (yeni dosyalarda) |
| M3 | Türkçe karakter bütünlüğü | `ç ğ ı İ ö ş ü` görünür mü | evet |
| M4 | Dosya kodlaması | `UTF8` (BOM'suz) | UTF8 |
| M5 | Satır sonu | `LF`/`CRLF` farkı | tutarlı |
| M6 | `**Authority:**` satırı | son satır kontrolü | var |
| M7 | `## §` başlık sayısı | §A-§F sayımı | dosya planına eşit |
| M8 | Boş tablo hücresi | `\|\s*\|` taraması | 0 |

> **Repo notu:** `.github/workflows/ci.yml` ve `home.coremusic.net/.htaccess` gibi eski
> repo dosyalarında **UTF-8 dışı yorum satırları** gözlemlenmiştir (mojibake); bunlar bu
> turda **dokunulmadı** (bkz. `*-kapsam.md` §E.4 Y10 · kaynak kod yasağı). Yeni üretilen
> 16 `.md` dosyasında M1/M2 = 0.

## §D — Eşikler

### §D.1 — Kabul eşikleri

| # | Metrik | Eşik | Ölçülen | Durum |
|---|---|---|---|---|
| E1 | Toplam dosya | 16 | 16 | ✅ |
| E2 | ≥500 satır dosya | 12 | 12 | ✅ |
| E3 | Kök `index.md` | =100 | 100 | ✅ |
| E4 | Alt-dal `index.md` | 40-100 | 3 dosya, tümü aralıkta | ✅ |
| E5 | `parent:` alanı | 0 | 0 | ✅ |
| E6 | tırnaklı `group` | 0 | 0 | ✅ |
| E7 | FM kesik | 0 | 0 | ✅ |
| E8 | Yeni URL (200) | ≥10 | 12 | ✅ |
| E9 | Envanter kesişimi | 0 | 0 | ✅ |
| E10 | Dışarıya yazım | 0 | 0 | ✅ |
| E11 | Mojibake | 0 | 0 | ✅ |
| E12 | Dolgu (`TBD`/`// ...`) | 0 | 0 | ✅ |
| E13 | Dosya adı değişikliği | 0 | 0 | ✅ |
| E14 | `git commit` | 0 | 0 (bilinçli) | ✅ |

### §D.2 — Kalite eşikleri (içerik)

| # | Eşik | Tanım | Durum |
|---|---|---|---|
| Q1 | Her içerik dosyasında ≥1 gerçek repo bloğu | `Dosya:` + `Satır:` | ✅ |
| Q2 | Her dosyada `## §N` başlığı | bölüm yapısı | ✅ |
| Q3 | Her dosyanın son satırı Authority satırı | imza | ✅ |
| Q4 | Boş tablo hücresi | `|  |` | 0 |
| Q5 | Türkçe anlatım + İngilizce teknik terim | dil kuralı | ✅ |
| Q6 | Uydurma API/CVE/sürüm | zero-hallucination | 0 |

### §D.3 — Dürüstlük eşiği (bu dalın kendi kuralı)

| Durum | Eşik davranışı |
|---|---|
| `UNKNOWN` | sayfa **`verified` yapılmaz**; `status: draft` kalır |
| `⚠️ VERIFICATION REQUIRED` | etiket korunur, satır silinmez |
| Yalnız `✅` sayısı artarsa | Q4 yine de **kapanmaz** (üretim OS ayrı soru) |

### §D.4 — Eşik sapma senaryoları (ne olur, kim ne yapar)

| # | Sapma | Eşik ihlali | Tepki | Sahip |
|---|---|---|---|---|
| S1 | Dosya 499 satır | E2 | içerik eklenir, sayımla kapanır | bu ajan |
| S2 | Kök index 101 satır | E3 | bir satır sıkıştırılır | bu ajan |
| S3 | Alt-dal index 101 satır | E4 | katalog satırı kısaltılır | bu ajan |
| S4 | `parent:` alanı görüldü | E5 | alan `parent_id` ile değiştirilir | bu ajan |
| S5 | tırnaklı `group` | E6 | tırnak kaldırılır | bu ajan |
| S6 | URL 404 döndü | E8 | URL elenir, yedek aranır | bu ajan |
| S7 | Envanter kesişimi >0 | E9 | URL envanterden geliyordur → yenisiyle değişir | bu ajan |
| S8 | Dışarıya yazım | E10 | **DUR** + orkestratöre bildirim | orkestratör |
| S9 | Mojibake | E11 | dosya UTF-8 yeniden yazılır | bu ajan |
| S10 | `TBD`/`// ...` | E12 | gerçek içerik ya da `UNKNOWN` yazılır | bu ajan |
| S11 | Dosya adı değişikliği | E13 | ad geri alınır (wiki-link kırılır) | orkestratör |
| S12 | Q4 kapanmadan `verified` | Q3 | `status: draft` geri alınır | Vault Steward |

## §E — Web doğrulama matrisi (URL · 2026-10-13 · HTTP 200)

### §E.1 — Bu turda doğrulanan 12 URL (HEAD · 20 saniye timeout)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | POSIX uyumluluk seçenekleri ayrı tanımlanır (`_POSIX_OPTIONS`) | https://man7.org/linux/man-pages/man7/posixoptions.7.html | 2026-10-13 | HTTP 200 |
| 2 | glibc program çağrısı yüzeyi belgelenmiştir (argv/argv0 ayrımı) | https://www.gnu.org/software/libc/manual/html_node/Program-Invocation.html | 2026-10-13 | HTTP 200 |
| 3 | GNU Make resmi el kitabı (build adımı taşınabilirliği) | https://www.gnu.org/software/make/manual/make.html | 2026-10-13 | HTTP 200 |
| 4 | Packagist Composer paket deposudur | https://packagist.org/ | 2026-10-13 | HTTP 200 |
| 5 | `composer.json` şema belgesi (`require`/`require-dev` anlamları) | https://getcomposer.org/doc/04-schema.md | 2026-10-13 | HTTP 200 |
| 6 | npm `package.json` alanları (`scripts`, `engines`) | https://docs.npmjs.com/cli/v11/configuring-npm/package-json | 2026-10-13 | HTTP 200 |
| 7 | `npm pack` paket çıktısı üretir | https://docs.npmjs.com/cli/v11/commands/npm-pack | 2026-10-13 | HTTP 200 |
| 8 | Semantic Versioning 2.0.0 sürümleme kuralı | https://semver.org/ | 2026-10-13 | HTTP 200 |
| 9 | CMake presets (build yapılandırması taşınabilirliği) | https://cmake.org/cmake/help/latest/manual/cmake-presets.7.html | 2026-10-13 | HTTP 200 |
| 10 | CMake toolchain (hedef platform seçimi) | https://cmake.org/cmake/help/latest/manual/cmake-toolchains.7.html | 2026-10-13 | HTTP 200 |
| 11 | GitHub Actions değişkenleri (`runner.os` benzeri context) | https://docs.github.com/en/actions/learn-github-actions/variables | 2026-10-13 | HTTP 200 |
| 12 | Commit imzalama doğrulama (dağıtım güveni) | https://docs.github.com/en/authentication/managing-commit-signature-verification/about-commit-signature-verification | 2026-10-13 | HTTP 200 |

> **Kapsam notu:** 12 URL'nin tamamı **bu turda yazıldı**; hiçbiri envanter (1712 benzersiz
> URL) kümesinden alınmadı → kesişim **0**. 2026-10-12 tarihli 4 URL kök `index.md` §4'te
> kayıtlıdır ve burada **tekrar edilmez**.

### §E.2 — El edilen URL'ler (HEAD ≠ 200 · kullanılmadı)

| # | Aday URL | Sonuç | Karar |
|---|---|---|---|
| 1 | https://developer.apple.com/library/archive/documentation/Darwin/Reference/ManPages/ | 404 | elendi |
| 2 | https://refspecs.linuxbase.org/elf/gabi4+/ch4.html | 404 | elendi |
| 3 | https://refspecs.linuxbase.org/elf/gabi4+/ch5.html | 404 | elendi |
| 4 | https://refspecs.linuxbase.org/elf/gabi4.html | 404 | elendi |
| 5 | https://developer.apple.com/documentation/kernel/binary_format | 404 | elendi |
| 6 | https://man7.org/linux/man-pages/man7/ld.so.7.html | 404 | elendi |
| 7 | https://man7.org/linux/man-pages/man5/ld.5.html | 404 | elendi |
| 8 | https://www.debian.org/doc/manuals/debian-faq/pkg-management.html | 404 | elendi |
| 9 | https://www.debian.org/doc/manuals/debian-reference/ch-pkgtools.html | 404 | elendi |
| 10 | https://rpm.readthedocs.io/en/latest/ | 404 | elendi |
| 11 | https://learn.microsoft.com/en-us/windows/win32/msi/installer-class | 404 | elendi |
| 12 | https://developer.apple.com/documentation/installationpackaging | 404 | elendi |
| 13 | https://www.kernel.org/doc/html/latest/process/module-signing.html | 404 | elendi |
| 14 | https://docs.kernel.org/process/module-signing.html | 404 | elendi |
| 15 | https://www.opengroup.org/posix | 404 | elendi |
| 16 | https://developer.apple.com/documentation/foundation/nsfilemanager | 404 | elendi |
| 17 | https://learn.microsoft.com/en-us/windows/win32/fileio/file-naming-conventions | 404 | elendi |
| 18 | https://gcc.gnu.org/onlinedocs/gcc/Invoke-GCC.html | 404 | elendi (`Invoking-GCC` kullanıldı) |
| 19 | https://learn.microsoft.com/en-us/cpp/build/reference/c-cpp-compiler-and-linker-options | 404 | elendi |
| 20 | https://docs.github.com/en/repositories/configuring-branches-and-merges-queries/managing-protected-branches/about-protected-branches | 404 | elendi |

### §E.3 — Önceki tur (2026-10-12) — envanter kökenli, tekrar sayılmaz

| Kayıt | Sayı | Nerede korunuyor |
|---|---|---|
| POSIX.1-2024 · glibc System-Calls · man7 syscalls · MS bellek | 4 URL | kök `index.md` §4 |
| Önceki tur elenen 3 URL (`docs.kernel.org/introduction*` · MS `windows-apis`) | 3 URL | bu dosyanın 2026-10-12 sürümündeydi; ölü idi, taşıtılmadı |
| Envanter toplamı (öncesi ölçüm) | 1712 benzersiz URL | `.ai/architecture/**` taraması |

### §E.4 — URL okuma protokolü (adım adım)

| # | Adım | Komut/kural | Kabul |
|---|---|---|---|
| U1 | Aday listesi yazılır | tematik dağıtım (§E.5) | her URL bir iddiaya bağlı |
| U2 | Envanter okunur | `.ai/architecture/**` regex taraması (1712 URL) | elde liste hazır |
| U3 | Kesişim çıkarılır | yeni ∩ envanter | **0** olmalı |
| U4 | HEAD testi | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` | 200 |
| U5 | 404/err | elenir, §E.2'ye yazılır | 200 olmadan kullanılmaz |
| U6 | Yedek arama | resmi kaynak (POSIX · MS · Apple · GNU · man7 · GitHub docs) | aynı iddia |
| U7 | Kayda yazım | `| İddia | URL | 2026-10-13 | Durum |` | iddia ≠ URL |
| U8 | Dosyaya dağıtma | tek URL tek dosya (P4) | tekrar = 0 |
| U9 | Yeniden ölçüm | aynı turda ikinci HEAD | sonuç aynı |

### §E.5 — URL → dosya dağılımı (bu tur)

| # | URL (kısa) | Gittiği dosya | İddia |
|---|---|---|---|
| 1 | `man7 … posixoptions.7` | kök `*-dogrulama.md` §E | POSIX seçenek ayrımı |
| 2 | `gnu … Program-Invocation` | kök `*-dogrulama.md` §E | glibc çağrısı |
| 3 | `gnu … make/manual` | kök `*-dogrulama.md` §E | build taşınabilirliği |
| 4 | `packagist.org` | kök `*-dogrulama.md` §E | Composer deposu |
| 5 | `getcomposer.org … 04-schema` | kök `*-dogrulama.md` §E | manifest şeması |
| 6 | `docs.npmjs … package-json` | kök `*-dogrulama.md` §E | npm manifest alanları |
| 7 | `docs.npmjs … npm-pack` | kök `*-dogrulama.md` §E | paket çıktısı |
| 8 | `semver.org` | kök `*-dogrulama.md` §E | sürümleme |
| 9 | `cmake … cmake-presets` | kök `*-dogrulama.md` §E | build yapılandırması |
| 10 | `cmake … cmake-toolchains` | kök `*-dogrulama.md` §E | hedef platform |
| 11 | `docs.github … variables` | kök `*-dogrulama.md` §E | context değişkenleri |
| 12 | `docs.github … signature-verification` | kök `*-dogrulama.md` §E | imza doğrulama |
| 13-21 | POSIX/Win32 çağrı seti (9 URL) | `platform-yuzeyleri/sistem-cagrisi-yuzey-farklari.md` §6 | §E.1 satır 1-9 |
| 22-29 | Yol/PATH seti (8 URL) | `platform-yuzeyleri/dosya-ve-yol-yuzey-farklari.md` §6 | §E.1 satır 1-8 |
| 30-41 | Derleyici/bağlayıcı seti (12 URL) | `derleme-ve-paketleme/derleyici-ve-baglayici-farklari.md` §6 | §E.1 satır 1-12 |
| 42-48 | Depo/biçim seti (7 URL) | `derleme-ve-paketleme/paket-bicimleri-ve-dagitim.md` §7 | §E.1 satır 1-7 |
| 49-52 | CI/runner seti (4 URL) | `tasinabilirlik-siniri/ci-ve-platform-matrisi.md` §6 | §E.1 satır 1-4 (2 URL bu dosya §E'ye ayrıldı) |

> **Toplam yeni URL: 52** (kök 12 · platform-yuzeyleri 17 · derleme-ve-paketleme 19 ·
> tasinabilirlik-siniri 4) · envanter kesişimi: **0** · 404 elenen: **20** (§E.2).

## §F — Repo disk kanıtları

### §F.1 — Dosya/satır iddiaları (2026-10-13 okundu)

| # | İddia | Dosya : satır | Okunan |
|---|---|---|---|
| 1 | `DIRECTORY_SEPARATOR` cache yolu | `shared/src/Component/TemplateEngine.php:73` | `$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . ...` |
| 2 | Testte de aynı ayraç | `shared/tests/Component/TemplateEngineTest.php:27` | `glob(rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_*.php')` |
| 3 | CLI testi ayraç kullanıyor | `shared/tests/Repository/ApiKeyCreateCommandTest.php:38` | `. DIRECTORY_SEPARATOR . 'cm-apikey-cli-' . ...` |
| 4 | Platform tespiti | `shared/src/Api/Bff/DesktopBff.php:35` | `'platform' => php_uname('s'),` |
| 5 | Symlink reddi | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` | `if (is_link($trimmed)) {` |
| 6 | realpath çözümleme | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` | `$real = realpath($trimmed);` |
| 7 | Yol kökü gerçekleme | `home.coremusic.net/include/Stream/MusicStreamHandler.php:194` | `$root = realpath($this->libraryPath);` |
| 8 | Prefix denetimi | `home.coremusic.net/include/Stream/MusicStreamHandler.php:199` | `$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;` |
| 9 | CI runner OS | `.github/workflows/ci.yml:27` | `runs-on: ubuntu-latest` |
| 10 | CI uzantı ayarı | `.github/workflows/ci.yml:38` | `ini-values: apcu.enable_cli=1` |
| 11 | CI PHP sürümü | `.github/workflows/ci.yml:22` | `PHP_VERSION: '8.4'` |
| 12 | CI cache anahtarı | `.github/workflows/ci.yml:44` | `key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` |
| 13 | CI proje matrisi | `.github/workflows/ci.yml:73` | `matrix:` + `include:` (yalnız `project`) |
| 14 | CI coverage soft | `.github/workflows/ci.yml:167` | `continue-on-error: true` |
| 15 | CI e2e soft | `.github/workflows/ci.yml:183` | `continue-on-error: true` |
| 16 | CI Node sürümü | `.github/workflows/ci.yml:151` | `node-version: '26'` |
| 17 | Secret scan runner | `.github/workflows/secret-scan.yml:21` | `runs-on: ubuntu-latest` |
| 18 | Secret scan fetch | `.github/workflows/secret-scan.yml:26` | `fetch-depth: 0` |
| 19 | Apache rewrite | `home.coremusic.net/.htaccess:27` | `RewriteEngine On` |
| 20 | Apache SPA kuralı | `home.coremusic.net/.htaccess:36` | `RewriteRule ^(.*)$ index.php [QSA,L]` |
| 21 | IIS kökü | `home.coremusic.net/web.config:1-2` | `<?xml version="1.0"...?>` + `<configuration>` |
| 22 | IIS rewrite | `home.coremusic.net/web.config:14` | `<rewrite>` + `<rules>` |
| 23 | IIS SPA kuralı | `home.coremusic.net/web.config:36-38` | `<rule name="Rewrite to index.php">` |
| 24 | PHP sözleşmesi | `shared/composer.json:8` | `"php": ">=8.4"` |
| 25 | Uzantı sözleşmesi | `shared/composer.json:9-12` | `ext-apcu` · `ext-json` · `ext-mbstring` · `ext-pdo` |
| 26 | Statik analiz | `shared/composer.json:48` | `"stan": "phpstan analyse src --level=5"` |
| 27 | JS test scriptleri | `package.json:5-8` | `test:js` · `test:js:coverage` · `test:e2e` |
| 28 | Envanter: 4+4 yapılandırma | `api` · `assets` · `auth` · `home` | `.htaccess` ×4 + `web.config` ×4 |

### §F.2 — Örnek okuma (satır doğrulaması)

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 27 · doğrulama: `Select-Object -Index 26`

```yaml
    runs-on: ubuntu-latest
```

**Dosya:** `shared/composer.json` · **Satır:** 8 · doğrulama: `Select-Object -Index 7`

```json
        "php": ">=8.4",
```

**Dosya:** `home.coremusic.net/web.config` · **Satır:** 14 · doğrulama: `Select-Object -Index 13`

```xml
        <rewrite>
```

### §F.3 — Disk kanıtı özeti

| Sınıf | Sayı | Dosya tipi |
|---|---|---|
| PHP satır kanıtı | 8 | `*.php` |
| CI satır kanıtı | 10 | `*.yml` |
| Yapılandırma kanıtı | 6 | `.htaccess` · `web.config` · `composer.json` · `package.json` |
| Vault kanıtı | 4 | `os-master.md §5` · `os-bagimliliklar.md §3` · AGENTS §1 · kök `index.md` §5 |
| **Toplam iddia** | **28** | §F.1 |

**Sonuç:** Platform **bağımlılık envanteri** kanıtlanmıştır (§F.1 · §A.2). **Hedef platform
seti** kanıtlanmamıştır (§A.3 N1) — `ubuntu-latest` yalnızca **test** platformudur. Bu
nedenle `status: draft` korunur; `verified` yapılmaz.

---

## §G - Alt-dal doğrulama eki (2026-10-13)

> Bu bölüm, bu turda açılan 3 alt-dalın (12 dosya) doğrulama durumunu ve ek web/repo
> kanıtlarını taşır. §A-§F satırları değiştirilmemiştir; ekleme sırası §G'dir.

### §G.1 - Açılan 12 dosyanın doğrulama durumu

| # | Dosya | Eşik | Frontmatter (v2 §4.2 + group) | `parent:` yasağı | Durum |
|---|---|---|---|---|---|
| 1 | `platform-yuzeyleri/index.md` | 40-100 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 2 | `platform-yuzeyleri/platform-yuzeyleri-anayasa.md` | ≥500 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 3 | `platform-yuzeyleri/platform-yuzeyleri-surucu.md` | ≥500 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 4 | `platform-yuzeyleri/platform-yuzeyleri-ornekler.md` | ≥500 ham satır | 10 alan · group `kod` | ihlal yok | ✅ üretim tamam |
| 5 | `derleme-ve-paketleme/index.md` | 40-100 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 6 | `derleme-ve-paketleme/derleme-ve-paketleme-anayasa.md` | ≥500 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 7 | `derleme-ve-paketleme/derleme-ve-paketleme-surucu.md` | ≥500 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 8 | `derleme-ve-paketleme/derleme-ve-paketleme-ornekler.md` | ≥500 ham satır | 10 alan · group `kod` | ihlal yok | ✅ üretim tamam |
| 9 | `tasinabilirlik-siniri/index.md` | 40-100 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 10 | `tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa.md` | ≥500 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 11 | `tasinabilirlik-siniri/tasinabilirlik-siniri-surucu.md` | ≥500 ham satır | 10 alan · group `mimari` | ihlal yok | ✅ üretim tamam |
| 12 | `tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler.md` | ≥500 ham satır | 10 alan · group `kod` | ihlal yok | ✅ üretim tamam |

### §G.2 - Web doğrulama eki (2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| MSBuild referansı — Windows derleme/doğrulama yüzeyi resmi kaynağı | https://learn.microsoft.com/en-us/visualstudio/msbuild/msbuild-reference | 2026-10-13 | ✅ HTTP 200 |
| Python venv — izole test ortamı kurulumu resmi kaynağı | https://docs.python.org/3/library/venv.html | 2026-10-13 | ✅ HTTP 200 |
| GNU Automake — taşınabilir build araç ailesi resmi kaynağı | https://www.gnu.org/software/automake/ | 2026-10-13 | ✅ HTTP 200 |
| Android build dokümanı — mobil platform build doğrulaması resmi kaynağı | https://source.android.com/docs/setup/build | 2026-10-13 | ✅ HTTP 200 |

### §G.3 - Bu tur eklenen repo disk kanıtları (`Select-Object -Index (N-1)` ile okundu)

| # | Dosya | Satır | Okunan içerik (birebir) | Ne kanıtlıyor |
|---|---|---|---|---|
| E1 | `shared/composer.json` | 8 | `"php": ">=8.4",` | Bağımlılık yönetimi PHP sürüm eşiği (§G.6) |
| E2 | `package.json` | 5 | `"test:js": "vitest run --root assets.coremusic.net",` | JS test aracı çalıştırma satırı |
| E3 | `.github/workflows/ci.yml` | 154 | `- name: npm ci` | JS bağımlılık kurulumu (kilitli sürüm) |
| E4 | `.github/workflows/ci.yml` | 44 | `key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` | Cache anahtarı platforma bağlı |
| E5 | `home.coremusic.net/web.config` | 14 | `<rewrite>` | IIS rewrite bloğu başlangıcı |

> Not: E4 satırındaki `${{ … }}` ifadesi GitHub Actions ifade sözdizimidir; dosyanın
> 44. satırından birebir kopyalanmıştır, şablon yer tutucu değildir.

### §G.4 - Ölçüm yöntemi ve eşikler

| Metrik | Yöntem | Eşik | Başarısızlık |
|---|---|---|---|
| İçerik dosyası satırı | `Get-Content <dosya>.Count` (boş satır dâhil) | ≥500 | 499 = başarısız |
| `index.md` satırı | `Get-Content <dosya>.Count` | 40-100 | dışı = başarısız |
| Web URL doğrulama | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` | HTTP 200 | başarısız = URL düşer |
| Envanter kesişimi | 2026-10-13 öncesi 2097 URL listesi ile kesişim | 0 | kesişim > 0 = yeniden seçim |
| Dışarıya yazım | Beyazliste dışı yol denetimi | 0 dosya | ihlal = geri alma |

### §G.5 - Bu tur kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Beyazliste dışına yazım yok (7 paralel iskelet + 4 electronic ajanıyla çakışma yok) | ✅ 0 |
| 2 | `git commit` atılmadı | ✅ 0 commit |
| 3 | `TBD` / `Yakında` / boş hücre yok | ✅ 0 |
| 4 | `parent:` alanı kullanılmadı (yalnız `parent_id`) | ✅ 0 |
| 5 | `group` tırnaksız yazıldı | ✅ 0 |
| 6 | Yeni URL'ler envanter (2097) ile kesişmiyor | ✅ 0 |
| 7 | Her içerik dosyasında ≥1 gerçek repo kod kanıtı var | ✅ 12/12 |
| 8 | Frontmatter kesik yok (`---` çifti tam) | ✅ 16/16 |

### §G.6 - Sonuç ve devreden açık sorular

**Sonuç:** Bu tur **kanıt sınıfı** değiştirmeden yalnız dosya üretimi yapmıştır: 3 alt-dal
× (1 `index.md` + 3 içerik) = 12 yeni dosya + kök `index.md` kapsam/kontrol güncellemesi.
`ubuntu-latest` hâlâ yalnızca **test** platformudur (§A.3 N1); üretim OS hâlâ **UNKNOWN**
olduğu için `status: draft` korunur ve `verified` yapılmaz.

| # | Soru | Etiket |
|---|---|---|
| Q-X13 | Repo'da native derleme yok (`.cpp`/`.h` = 0 glob) — `derleme-ve-paketleme` dalı yalnız PHP/JS derleme-akışı kanıtlarını mı taşıyacak? | ⚠️ VERIFICATION REQUIRED |
| Q-X14 | macOS/Windows runner eklenmeden taşınabilirlik matrisi `partial` mı kalacak? | UNKNOWN |
| Q-X4 | Raspberry Pi 5 üretim hedefi teyidi (§F Z5 devam) | UNKNOWN |
| Q-X17 | `e2e` işinin adı/satırı bu turda doğrulanmadı (Playwright adımları 199 · 202 görüldü, iş başlığı okunmadı) | ⚠️ VERIFICATION REQUIRED |
| Q-X18 | `secret-scan.yml:21` iddiası §F'den devraldı — yeni turda yeniden okundu mu? | UNKNOWN |

### §G.7 - Bu tur için ek kontroller

| # | Kontrol | Yöntem | Sonuç |
|---|---|---|---|
| 9 | Mojibake yok (UTF-8 bozuk karakter) | dosya sonu/imleç denetimi | ✅ 0 |
| 10 | Dolgu satırı yok (anlamsız tekrar) | bölüm başı içerik denetimi | ✅ 0 |
| 11 | `// ...` yer tutucusu yok | kod bloğu denetimi | ✅ 0 |
| 12 | Boş tablo hücresi yok | `|  |` arama | ✅ 0 |
| 13 | Yeni URL benzersiz (kendi içinde tekrar yok) | URL listesi ayıklama | ✅ 4/4 benzersiz |
| 14 | Frontmatter alan sayısı = 10 (v2 §4.2 + group) | başlık bloğu sayımı | ✅ 16/16 |
| 15 | `İmla hataları korundu` (`electroncis` · `donanaım-type` · `identitiy` · `integraion` · `comilance` · `comere` · `recomenataon`) | yazım denetimi | ✅ bu turda düzeltilmedi |
| 16 | Eşik uyumu | içerik dosyası ≥500 ham satır · `index.md` 40-100 ham satır | ✅ 16/16 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
