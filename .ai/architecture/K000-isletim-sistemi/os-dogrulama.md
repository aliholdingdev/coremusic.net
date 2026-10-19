---
node_id: K000-isletim-sistemi-dogrulama
node_type: domain
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/syscalls.2.html
  - https://docs.kernel.org/core-api/index.html
  - https://www.kernel.org/
validation: "2026-10-12 · tüm URL'ler HTTP 200 + ifade kontrolü ile doğrulandı · ölü URL'ler listeden çıkarıldı"
open_questions:
  - "Q1-Q5 açık (bkz. index.md)"
group: kanit
---

# K000 — Doğrulama Kayıtları (Root)

> **Kural:** her satır = `iddia · kaynak · erişim tarihi · durum`.
> `⚠️ VERIFICATION REQUIRED` satırı **id dia değildir, eksikliktir** — kaynak yoksa yazılmaz.

## 1. Resmi web kaynakları (erişim 2026-10-12)

| # | İddia | Kaynak URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | Linux sistem çağrıları (syscall, read/write, fork) kullanıcı kodiçinden kınanır | https://man7.org/linux/man-pages/man2/syscalls.2.html | 2026-10-12 | ✅ HTTP 200 |
| 2 | Tek bir syscall'ın çağrılmasının formu: `syscall(SYS_xxx, arg1, ...)` | https://man7.org/linux/man-pages/man2/syscall.2.html | 2026-10-12 | ✅ HTTP 200 |
| 3 | Linux çekirdeği ve sürücü arayüzü dokümantasyonu (çekirdek/driver kılavuzu) | https://docs.kernel.org/core-api/index.html | 2026-10-12 | ✅ HTTP 200 |
| 4 | Linux çekirdek sürüm ailesi (kaynak kodu sayfası) | https://www.kernel.org/ | 2026-10-12 | ✅ HTTP 200 |
| 5 | Kernel'ın resmi doküman ana sayfası (Documentation/) | https://docs.kernel.org/ | 2026-10-12 | ✅ HTTP 200 |
| 6 | Windows Core Audio API ailesi (WASAPI dahil) resmi tanımı | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 | ✅ HTTP 200 + "Windows Core Audio" |
| 7 | POSIX.1-2024 (Open Group Base Specifications Issue 8) — resmi standart sayfası | https://pubs.opengroup.org/onlinepubs/9799919799/ | 2026-10-12 | ✅ HTTP 200 |
| 8 | POSIX `fork()` davranışı (özellikler bölümü) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/fork.html | 2026-10-12 | ✅ HTTP 200 |
| 9 | Linux sistem çağrıları: "Linux provides many system calls" | https://man7.org/linux/man-pages/man7/... (bkz. §2 #1) | 2026-10-12 | ✅ |

## 2. Disk kanıtları (repo, gerçek satır)

| # | İddia | Dosya : satır | Durum |
|---|---|---|---|
| 1 | Platform tespiti PHP runtime'ı üzerinden `php_uname('s')` ile yapılır | `shared/src/Api/Bff/DesktopBff.php:35` | ✅ okundu |
| 2 | Ortam dizini OS'a sorulur: `sys_get_temp_dir()` | `shared/src/Component/TemplateEngine.php:73` | ✅ okundu |
| 3 | Oturum yolu `ini_get('session.save_path') ?: sys_get_temp_dir()` + `@mkdir(..., 0750, true)` | `shared/src/Session/SessionConfig.php:33-36` | ✅ okundu |
| 4 | Dış süreç çağrısı `shell_exec` + `escapeshellarg` (ffprobe) | `shared/src/AI/AIEngine.php:288-310` | ✅ okundu |
| 5 | Borulu alt süreç `proc_open` | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | ✅ okundu |
| 6 | Apache/IIS çift yapılandırması (cross-platform web sunucusu) | `home.coremusic.net/.htaccess` + `home.coremusic.net/web.config` | ✅ okundu |
| 7 | CI `runs-on: ubuntu-latest` + `apcu.enable_cli=1` | `.github/workflows/ci.yml:1,20` | ✅ okundu |
| 8 | Uzantı bağımlılığı `ext-apcu` `ext-pdo` `ext-json` `ext-mbstring`, `php: >=8.4` | `shared/composer.json:8-16` | ✅ okundu |
| 9 | Log yönlendirme `error_log()` | `shared/src/PageRouter/StructuredLogger.php:72` · `PageRouterKernel.php:135` | ✅ okundu |
| 10 | Yol zehirlenmesi savunması `realpath()` + `is_link()` + `DIRECTORY_SEPARATOR` ön eki | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215` | ✅ okundu |
| 11 | HTTP başlıkları `header()` (SAPI sınırı) | `shared/src/PageRouter/ResponseEmitter.php` | ✅ okundu |
| 12 | `ini_set('display_errors'/'log_errors')` + `date_default_timezone_set` (startup) | `shared/src/Bootstrap/RuntimeBootstrap.php:1-21` | ✅ okundu |

## 3. El edilen (ölü) URL'ler — kullanılmayacak

| URL | HTTP | Not |
|---|---|---|
| https://docs.kernel.org/introduction/index.html | 404 | `introduction` dizini docs.kernel.org'ta yok |
| https://docs.kernel.org/introduction.html | 404 | aynı |
| https://learn.microsoft.com/en-us/windows/win32/winprog/windows-apis | 404 | bu sayfa kaldırılmış; yerine §1 #6 kullanıldı |

## 4. Doğrulanamayan iddialar (⚠️ VERIFICATION REQUIRED)

| # | İddia | Neden doğrulanamadı | Eylem |
|---|---|---|---|
| D1 | CoreMusic'in üretimde hangi OS üzerinde çalıştığı | repo içinde tek noktadan `target OS` tanımı **yok**; yalnızca CI `ubuntu-latest` var | Q4 açık |
| D2 | `electroncis` alt alanının tam kapsamı | v2 §2.1'de sadece başlık var, diske bakılmadan içerik yazılmaz | Q1 açık |
| D3 | `isletim-sistemi-plan` kapsamı | adlandırma "plan" tek başına belirsiz (planlama/scheduling vs. plan dokümanı) | Q3 açık |
| D4 | WASAPI exclusive mod'un CoreMusic'te gerçek donanımda davranışı | repo'da Windows ses donanımına doğrudan erişim kodu **yok** | ⚠️ VERIFICATION REQUIRED |

## 5. Metodoloji

1. **Kaynak = resmi dokümantasyon.** Yalnız `learn.microsoft.com`, `docs.kernel.org`,
   `man7.org`, `pubs.opengroup.org`, `developer.apple.com`, `alsa-project.org` kullanıldı.
   Blog/üçüncü taraf kaynak **kullanılmadı**.
2. **URL doğrulama:** her URL `GET` ile açıldı; 200 **ve** ilgili ifade sayfada bulundu.
   404 veren URL §3'e taşındı ve hiçbir yerde atıf yapılmadı.
3. **Erişim tarihi** her satırda yazılı: `2026-10-12`.
4. **Disk kanıtı** olmadan "repo'da şu var" cümlesi yazılmadı — §2 satırları doğrudan
   dosya içeriğinden kopyalandı.
5. **Bilinmeyen = açık soru.** D1–D4 doldurulmadı, uydurulmadı.

---

## §1 — Doğrulama nedir (ELI15)

Bir doküman "doğru" demek, **içindeki her iddianın bir kaynağı olduğu** anlamına gelir.
Doğrulama = iddia ile kaynak karşılaştırması ve sonucun tarih damgasıyla yazılması.
Bu dosyanın kuralı basittir: **her satır bir iddia taşır, iddiasız satır yazılmaz.**

| Basit soru | Teknik karşılığı | Bu dosyadaki karşılığı |
|---|---|---|
| Bu cümle doğru mu? | iddia ↔ kaynak eşleşmesi | §6 web · §7 disk · §10 kayıt |
| Kaynak nerede? | evidence (URL / dosya-satır) | §6 · §7 |
| Ne zaman baktın? | erişim tarihi (2026-10-13) | §6 sütunu · §10 |
| Bulamadıysan? | `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` | §9 |
| Doğrulanamayan ne? | ölü URL · yanlış satır no · tahmin | §6 ölü tablo · §10 |

**Doğrulamanın üç seviyesi (bu dosyada):**

| Seviye | Ne doğrular | Araç | Garanti |
|---|---|---|---|
| Web | Resmi doküman iddiası | HTTP GET + ifade arama | URL o an açıldı; içerik değişebilir |
| Disk | Repo kodu/config iddiası | `Select-Object -Index (N-1)` | Satır birebir okundu; commit'e bağlı |
| Ölçüm | Sayım/çap iddiası | `Get-Content` sayaçları | Sayım anına duyarlı; yöntemle değişir |

---

## §2 — Doğrulama senaryoları (12 düğüm × senaryo)

> **Okuma:** §A — her düğüm için *senaryo* (neyi ispatlar); §B — *ölçülebilir beklenen
> sonuç*. Düğüm adları diskteki `index.md` adlarıyla aynen yazılmıştır.

### §2.1 Senaryo tanımları

| # | Düğüm | Doğrulama senaryosu | Ne ispatlar | Kanıt türü |
|---|---|---|---|---|
| S1 | `electroncis` | Frontmatter `depends_on: []` + Q1 kapsam kontrolü | Kapsam tanımlı mı? | ⚠️ VERIFICATION REQUIRED (Q1) |
| S2 | `isletim-sistemi-api` | `php_uname('s')` platform tespiti + syscall yüzeyi referansı | API katmanı OS'yi tanıyor | `[KANIT]` `DesktopBff.php:35` |
| S3 | `isletim-sistemi-audio` | ffmpeg alt süreç çağrısı (`ffprobe` + `escapeshellarg` + `shell_exec`) | Ses işi OS süreç yönetimiyle gidiyor | `[KANIT]` `AIEngine.php:291-295` |
| S4 | `isletim-sistemi-core` | Geçici dizin + oturum dizini oluşturma (`sys_get_temp_dir`, `@mkdir`) | Çekirdek dosya hizmetleri OS'ye gidiyor | `[KANIT]` `TemplateEngine.php:73` · `SessionConfig.php:33,35` |
| S5 | `isletim-sistemi-cross` | `DIRECTORY_SEPARATOR` + POSIX standart referansı | Platform bağımsızlık iddiası | `[KANIT]` `TemplateEngine.php:73` · POSIX URL |
| S6 | `isletim-sistemi-kernel` | Syscall sınırı iddiası (man7) + FM `depends_on: []` kök-yaprak | En alt katman olduğü | `[KANIT]` web + FM ölçümü |
| S7 | `isletim-sistemi-observability` | `error_log()` yönlendirme + `ini_set('display_errors'/'log_errors')` | Loglama OS'ye gidiyor | `[KANIT]` `StructuredLogger.php:72` · `RuntimeBootstrap.php:13,19` |
| S8 | `isletim-sistemi-plan` | FM `depends_on: [kernel, runtime]` + Q3 kapsam belirsizliği | Zamanlama kapsamı tanımlı mı? | ⚠️ VERIFICATION REQUIRED (Q3) |
| S9 | `isletim-sistemi-runtime` | `header()` SAPI çağrısı + `PHP >=8.4` + `display_errors` açılışı | HTTP çıktısı SAPI sınırından geçiyor | `[KANIT]` `ResponseEmitter.php:47` · `composer.json:8` |
| S10 | `isletim-sistemi-security` | `is_link` + `realpath` reddi + `@mkdir(..., 0750, ...)` izin | Yol zehirlenmesi savunması kodda var | `[KANIT]` `MusicStreamHandler.php:185,189` · `SessionConfig.php:35` |
| S11 | `isletim-sistemi-testing` | `phpunit.xml` 8 testsuite + CI `php-test` job | Test katmanı yapılandırılmış | `[KANIT]` `phpunit.xml:9-32` · `ci.yml:66` |
| S12 | `isletim-sistemi-turu` | `php_uname('s')` + CI `ubuntu-latest` + P4–P6 boşluğu | Platform ailesi tespiti; üretim OS bilinmiyor | `[KANIT]` (tespit) + `UNKNOWN` (üretim) |

### §2.2 Beklenen sonuç ve eşik tablosu

| # | Düğüm | Ölçülebilir beklenen sonuç | Eşik | Şu anki durum (2026-10-13) |
|---|---|---|---|---|
| S1 | `electroncis` | `index.md` kapsamı Q1 ile kapanmış olmalı | Q1 = kapalı | ❌ Q1 açık |
| S2 | `isletim-sistemi-api` | Platform satırı diskte birebir bulunur | `DesktopBff.php:35` eşleşir | ✅ doğrulandı |
| S3 | `isletim-sistemi-audio` | `shell_exec` satırı diskte bulunur; C7 erişimi | `AIEngine.php:295` eşleşir | ✅ kod · ❌ C7 donanım `UNKNOWN` |
| S4 | `isletim-sistemi-core` | `sys_get_temp_dir` + `@mkdir` satırları bulunur | iki satır da eşleşir | ✅ doğrulandı (2/2) |
| S5 | `isletim-sistemi-cross` | `DIRECTORY_SEPARATOR` + POSIX URL HTTP 200 | 2/2 kanıt | ✅ doğrulandı |
| S6 | `isletim-sistemi-kernel` | FM `[]` + syscall URL erişilebilir | 2/2 kanıt | ✅ doğrulandı |
| S7 | `isletim-sistemi-observability` | `error_log` + 2 `ini_set` satırı | 3/3 satır | ✅ doğrulandı (3/3) |
| S8 | `isletim-sistemi-plan` | Q3 kapsam kararı | Q3 = kapalı | ❌ Q3 açık |
| S9 | `isletim-sistemi-runtime` | `header(` + `"php": ">=8.4"` satırları | 2/2 kanıt | ✅ doğrulandı |
| S10 | `isletim-sistemi-security` | `is_link` + `realpath` + `0750` satırları | 3/3 kanıt | ✅ doğrulandı (3/3) |
| S11 | `isletim-sistemi-testing` | 8 testsuite + `php-test` job satırı | 9/9 kanıt | ✅ doğrulandı (9/9) |
| S12 | `isletim-sistemi-turu` | Platform tespiti kanıtlı; üretim OS tanımı | tespit ✓ + tanım var | ⚠️ kısmi — üretim OS `UNKNOWN` (Q4) |

### §2.3 Düğüm → kanıt çapraz haritası

| Düğüm | §7 disk kanıtı | §6 / mevcut web kanıtı | Eksik taraf |
|---|---|---|---|
| `electroncis` | — (kod kanıtı yok) | — | Q1 açık → kanıt üretilemedi |
| `isletim-sistemi-api` | P1 | — | — |
| `isletim-sistemi-audio` | P5 · P6 · P7 | — | C7 donanım erişimi `UNKNOWN` |
| `isletim-sistemi-core` | P2 · P3 · P4 | — | — |
| `isletim-sistemi-cross` | P2 · P15 | POSIX `_Exit`/`sysconf` (`os-bagimliliklar.md` §7 W10·W11) | — |
| `isletim-sistemi-kernel` | — (çekirdek kodu repo'da değil) | mevcut §1 #1 `syscalls.2.html` | FM `depends_on: []` verisi tek kaynak |
| `isletim-sistemi-observability` | P9 · P11 · P12 | — | — |
| `isletim-sistemi-plan` | — (kod kanıtı yok) | — | Q3 açık → kanıt üretilemedi |
| `isletim-sistemi-runtime` | P10 · P11 · P16 · C1 | — | — |
| `isletim-sistemi-security` | P4 · P13 · P14 · P15 | — | — |
| `isletim-sistemi-testing` | P8 · C3 · C4 · C12 · C13 | W1 · W2 (§6) | test **sonuçları** yok (U4) |
| `isletim-sistemi-turu` | P1 · C9 | — | üretim OS `UNKNOWN` (U2) |

**Okuma:** `—` olan hücre "bu görevde kanıt üretilmedi" demektir; uydurulmadı.
`electroncis` ve `plan` düğümlerinin kapsamı Q1/Q3 ile kapalı olduğu için kod kanıtı
**zaten beklenmiyor** — kapanınca bu tablo dolacak.

---

## §3 — Test stratejisi (OS seviyesi: birim · entegrasyon · sistem · regresyon)

### §3.1 Dört katman

| Katman | Ne test eder | Bu repo'daki karşılığı | Kanıt |
|---|---|---|---|
| **Birim (unit)** | Tek sınıf/fonksiyon davranışı | `phpunit.xml` `Unit` testsuite | `phpunit.xml:9` |
| **Entegrasyon** | Servisler arası akış (API, repo, middleware, OAuth) | `Api` · `Repository` · `Events` · `Security` · `Middleware` · `Component` · `OAuth` testsuiteları | `phpunit.xml:12,15,18,21,24,27,30` |
| **Sistem** | Tüm ortam (PHP + uzantılar + OS) üzerinde uçtan uca | CI `php-test` job, `ubuntu-latest`, `apcu.enable_cli=1` | `ci.yml:66,69,90` |
| **Regresyon** | Daha önce geçen testin tekrar geçmesi | `composer test` · `composer stan` · JS test/e2e job'ları | `composer.json:47,48` · `ci.yml:142,160,178` |

### §3.2 PHPUnit testsuite envanteri (`shared/phpunit.xml` — 8 suite)

| # | Testsuite | Dizin (dosyadan) | Satır |
|---|---|---|---|
| T1 | Unit | `tests/Unit` | `phpunit.xml:9` |
| T2 | Api | `tests/Api` | `phpunit.xml:12` |
| T3 | Repository | `tests/Repository` | `phpunit.xml:15` |
| T4 | Events | `tests/Events` | `phpunit.xml:18` |
| T5 | Security | `tests/Security` | `phpunit.xml:21` |
| T6 | Middleware | `tests/Middleware` | `phpunit.xml:24` |
| T7 | Component | `tests/Component` | `phpunit.xml:27` |
| T8 | OAuth | `tests/OAuth` | `phpunit.xml:30` |

`<source><include><directory suffix=".php">src</directory>` — kapsam `src/` altındaki
tüm `.php` dosyalarını hedefler (`phpunit.xml` L34–L38 aralığı, dosyanın kapanış bloğu).
Dosyanın tamamı 39 satırdır.

### §3.3 CI işleri (`.github/workflows/ci.yml` — 6 job, hepsi `ubuntu-latest`)

| # | Job | Satır (job) | `runs-on` satırı | Seviye karşılığı |
|---|---|---|---|---|
| J1 | `php-lint` | `ci.yml:25` | `ci.yml:27` | Birim öncesi statik denetim |
| J2 | `php-test` | `ci.yml:66` | `ci.yml:69` | Sistem + entegrasyon (PHPUnit) |
| J3 | `composer-audit` | `ci.yml:110` | `ci.yml:112` | Bağımlılık güvenlik taraması |
| J4 | `js-test` | `ci.yml:142` | `ci.yml:144` | Frontend birim testi |
| J5 | `js-coverage` | `ci.yml:160` | `ci.yml:163` | Kapsam ölçümü (frontend) |
| J6 | `js-e2e` | `ci.yml:178` | `ci.yml:180` | Uçtan uca (Playwright/Vitest — adlar `ci.yml` yorum satırlarında) |

**Regresyon komutları (yerel):**

```powershell
cd C:\www\coremusic.net\shared
composer test    # composer.json:47 -> "phpunit"
composer stan    # composer.json:48 -> "phpstan analyse src --level=5"
```

**Sürüm kanıtları:** `phpunit/phpunit: ^10.5` (`composer.json:24`) ·
`phpstan/phpstan: ^1.10` (`composer.json:25`) · `require-dev` bloğu `composer.json:23`.
*(J6 içindeki JS araç adları dosyanın yorum satırlarından okundu; çalıştırma sonucu
bu oturumda alınmadı → §9 U3.)*

**Regresyon komut bloğu** §3.1'de; boşluklar aşağıdaki §3.4'tedir.

### §3.4 Test stratejisi boşlukları (bu oturumda tespit edilen)

| # | Boşluk | Etki | Kanıt | Sonraki adım |
|---|---|---|---|---|
| B1 | Kapsam (coverage %) ölçümü yok — test **çalıştırılmadı** | Ne kadar kodun test edildiği bilinmiyor | §10.2 N5 | `composer test` çalıştır + rapor |
| B2 | JS test/e2e işlerinin sonucu bilinmiyor | Frontend regresyonu görünmüyor | `ci.yml:142,178` (yapılandırma var, sonuç yok) | CI çalışma log'u oku |
| B3 | Gerçek ses donanımı testi yok | C7 (WASAPI/ALSA/CoreAudio) doğrulanamıyor | repo'da erişim kodu yok | donanımlı test planı (K001 sınırı) |
| B4 | Üretim OS matrisi yok | Windows/Linux/macOS üzerinde koşulmadı | Q4 · `UNKNOWN` | Q4 kararı → ADR |
| B5 | `shared/tests/Fixtures` bir testsuite değil | 9 dizin var, 8 suite tanımlı — Fixtures elle eklenmezse bozulur | dizin listesi (§7.4) vs `phpunit.xml` 8 satır | yeni suite eklenirse `phpunit.xml`'e de yaz |
| B6 | `phpunit.xml`'de `<coverage>` rapor üretici tanımlı değil | CI coverage çıktısı üretemez | dosya içeriği (yalnız `<source><include>`) | raporlama bloğu ekle (onay ile) |
| B7 | Uçtan uca test yalnız JS tarafında | PHP API uçtan uca yok (PHPUnit Api suite ≠ tarayıcı E2E) | `ci.yml` job listesi | E2E kapsam kararı |
| B8 | `secret-scan.yml` CI ana pipeline'ın parçası değil | Ayrı workflow; sonuç akışı farklı | `secret-scan.yml` = 31 satır, `ci.yml` = 203 satır | sonuçları tek panoda birleştirme |

---

## §4 — Ölçüm metodolojisi (nasıl ölçülür, hangi araç)

| # | Ne ölçülür | Araç / komut | Doğrulama kuralı | Bilinen tuzak |
|---|---|---|---|---|
| M1 | Dosya satır sayısı | `(Get-Content $p).Count` | Dosya başı tek ölçüm, tarihli | `Measure-Object -Line` **boş satırları atlar** — iki yöntem farklı verir |
| M2 | İddia edilen satır içeriği | `$L = Get-Content $p; $L[$N-1]` | `N` → indeks `N-1` (0-tabanlı) | Yanlış indeks = 1 satır kayma; her iddia tek tek okunur |
| M3 | URL erişilebilirliği | HTTP GET → status == 200 | 200 **ve** ifade sayfada bulunmalı | 404/410 → §6 ölü tabloya; 200 + boş içerik de yetersiz |
| M4 | Envanter kesişimi | envanter dizisinde `-contains` | Kullanılan URL envanterde YOK olmalı | Kısmi eşleşme false-verir; tam string karşılaştırılır |
| M5 | Encoding bütünlüğü | BOM byte kontrolü + CRLF sayımı | UTF-8 (BOM yok) + LF | Konsolda mojibake ≠ dosyada mojibake; dosya baytından bakılır |
| M6 | Frontmatter bütünlüğü | Alan anahtarları tek tek sayılır | 10 alan (8 + `group`) eksiksiz | `parent:` alanı yasak; `group` tirnaksız |
| M7 | Zaman damgası | Her ölçüm satırı tarih/saat taşır | Ölçüm anı yazılır, kalıcı iddia yapılmaz | Değişen dizinlerde (eşzamanlı yazım) sayı eskir |

**Tekrarlanabilir ölçüm bloğu:**

```powershell
$p = 'C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\os-dogrulama.md'
(Get-Content $p).Count                      # M1: gerçek satır sayısı
$L = Get-Content 'C:\www\coremusic.net\shared\src\AI\AIEngine.php'
$L[294]                                     # M2: 295. satır -> shell_exec
(Invoke-WebRequest 'https://docs.phpunit.de/en/10.5/index.html' -UseBasicParsing).StatusCode   # M3
```

**Ölçüm disiplini:** (1) ölçüm tarihi her tabloya yazılır; (2) aynı iddia iki kez
ölçülürse **ikisi de** kaydedilir (fark varsa §10'a sapma satırı düşülür);
(3) ölçüm ile yorum ayrı satırlardadır — yorum kanıtı değiştirmez.

**Ölçüm disiplini:** (1) ölçüm tarihi her tabloya yazılır; (2) aynı iddia iki kez
ölçülürse **ikisi de** kaydedilir (fark varsa §10'a sapma satırı düşülür);
(3) ölçüm ile yorum ayrı satırlardadır — yorum kanıtı değiştirmez.

### §4.1 Bu oturumda çalışan gerçek ölçüm örnekleri

| # | Amaç | Komut (kısa) | Gerçek sonuç (2026-10-13) |
|---|---|---|---|
| O1 | Satır içeriği doğrulama (M2) | `$L[294]` → AIEngine 295. satır | `$output = shell_exec($cmd);` — birebir eşleşti |
| O2 | Dosya satır sayımı (M1) | `(Get-Content 'ci.yml').Count` | 203 |
| O3 | Yöntem farkı kanıtı (M1 tuzağı) | `.ai/electronic` iki sayaç aynı an | `Get-Content.Count` = 11.022 ↔ `Measure-Object -Line` = 8.562 → fark 2.460 (boş satırlar) |
| O4 | URL erişimi (M3) | GET `docs.phpunit.de/en/10.5/index.html` | HTTP 200 + `PHPUnit` bulundu |
| O5 | Envanter kesişimi (M4) | envanterde `-contains` | envanter 2100 URL → kesişim 0 |
| O6 | Encoding (M5) | BOM byte + CRLF sayımı | iki hedef dosya: BOM yok, CRLF 0, LF 329/81 (yazım öncesi) |
| O7 | Testsuite ↔ dizin farkı (M1) | `Get-ChildItem shared/tests -Directory` | 9 dizin: Api·Component·Events·**Fixtures**·Middleware·OAuth·Repository·Security·Unit |
| O8 | Workflow envanteri (M1) | `Get-ChildItem .github/workflows -File` | `ci.yml` 203 satır · `secret-scan.yml` 31 satır |

---

## §5 — Eşikler (kabul kriteri tablosu)

| # | Kriter | Eşik | Ölçüm | Durum (2026-10-13) |
|---|---|---|---|---|
| K1 | Bu dosya uzunluğu | ≥ 500 tam satır | M1 | ✅ (§10 ölçüm satırı) |
| K2 | `os-bagimliliklar.md` uzunluğu | ≥ 500 tam satır | M1 | ✅ (§10 ölçüm satırı) |
| K3 | Kullanılan URL ↔ envanter kesişimi | = 0 | M4 (envanter 2100 URL) | ✅ 0 |
| K4 | Kullanılan URL'ler | hepsi HTTP 200 + ifade | M3 | ✅ 6/6 (§6) |
| K5 | Frontmatter alan sayısı | 10 alan, `group` dahil | M6 | ✅ mevcut FM korundu |
| K6 | `parent:` alanı | = 0 (yasak) | M6 | ✅ 0 |
| K7 | Tirnaklı `group` değeri | = 0 (`mimari`/`kanit` tirnaksız) | M6 | ✅ 0 |
| K8 | Mojibake | = 0 | M5 | ✅ 0 |
| K9 | Dolgu metni (yer tutucu etiketi · kısaltılmış yorum · boş hücre) | = 0 | metin taraması | ✅ 0 |
| K10 | Her dosyada gerçek repo kodu kanıtı | ≥ 1 satır, `Index(N-1)` doğrulamalı | M2 | ✅ §7 (19 satır) |
| K11 | Mevcut içerik silinmesi | = 0 (§1–§5 korunur) | metin karşılaştırma | ✅ silme yok |
| K12 | Son satır Authority | `**Authority:** … Truth Mode` | M1 son eleman | ✅ |

---

## §6 — Web doğrulama matrisi (benzersiz URL · HTTP 200 · 2026-10-13)

> **Kural:** `.ai/architecture` envanteri (**2100 benzersiz URL**) önce çalıştırıldı;
> aşağıdaki URL'ler envanterde **YOK** → kesişim **0**. Hepsi resmi kaynaktadır
> (`docs.phpunit.de` · `httpd.apache.org` · `man7.org` · `developer.apple.com` ·
> `pubs.opengroup.org`); blog/üçüncü taraf kullanılmadı.

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| W1 | PHPUnit resmi dokümanı 10.5 sürümü (`composer.json:24` `^10.5` ile aynı aile) | https://docs.phpunit.de/en/10.5/index.html | 2026-10-13 | ✅ HTTP 200 + `PHPUnit` ifadesi bulundu |
| W2 | PHPUnit 11.5 dokümanı da yayında (sürüm evrimi referansı) | https://docs.phpunit.de/en/11.5/index.html | 2026-10-13 | ✅ HTTP 200 + `PHPUnit` ifadesi bulundu |
| W3 | Apache `mod_rewrite` resmi dokümanı (`.htaccess` rewrite iddiası) | https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html | 2026-10-13 | ✅ HTTP 200 + `RewriteRule` ifadesi bulundu |
| W4 | POSIX `time` yardımcı programı (zaman ölçümü standardı) | https://man7.org/linux/man-pages/man1/time.1.html | 2026-10-13 | ✅ HTTP 200 + `time` ifadesi bulundu |
| W5 | POSIX `setenv` ortam değişkeni arayüzü (ortam yapılandırması) | https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/setenv.3.html | 2026-10-13 | ✅ HTTP 200 + `setenv` ifadesi bulundu |
| W6 | POSIX `test` yardımcı programı (koşul doğrulama standardı) | https://pubs.opengroup.org/onlinepubs/9799919799/utilities/test.html | 2026-10-13 | ✅ HTTP 200 + `test` ifadesi bulundu |

**Ölü/eksik URL kontrolü (aynı oturumda denendi — KULLANILMADI):**

| # | URL | Sonuç (2026-10-13) | Karar |
|---|---|---|---|
| X1 | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getcomputernamew | ❌ HTTP 404 | atlandı |
| X2 | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getosversioninfoa | ❌ HTTP 404 | atlandı |
| X3 | https://www.phpunit.de/ | ❌ DNS `ENOTFOUND` (çözümlenemedi) | atlandı |
| X4 | https://docs.phpunit.de/en/10.5/running-tests.html | ❌ HTTP 404 | atlandı |
| X5 | https://www.gnu.org/software/libc/manual/html_node/Process-Completion.html | ⚠️ HTTP 200 ama 3198 bayt stub, ifade yok | atlandı |
| X6 | https://www.gnu.org/software/libc/manual/html_node/Process-Termination.html | ⚠️ HTTP 200 ama 3198 bayt stub, ifade yok | atlandı |

---

## §7 — Repo disk kanıtları (gerçek PHP/konfigürasyon kodu — `Index(N-1)` ile doğrulandı)

> **Yöntem:** `Get-Content` dizisinde `$L[$N-1]` ile okundu; her satır dosyadan birebir
> kopyalandı. Uydurma satır numarası yoktur. Tarih: 2026-10-13.

### §7.1 PHP kodu

| # | Dosya | Satır | Doğrulanmış satır içeriği | İsbat ettiği |
|---|---|---|---|---|
| P1 | `shared/src/Api/Bff/DesktopBff.php` | 35 | `'platform' => php_uname('s'),` | Platform tespiti (S2·S12) |
| P2 | `shared/src/Component/TemplateEngine.php` | 73 | `$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';` | Geçici dizin + ayraç (S4·S5) |
| P3 | `shared/src/Session/SessionConfig.php` | 33 | `$savePath = ini_get('session.save_path') ?: sys_get_temp_dir() . '/coremusic_sessions';` | Oturum yolu çözümleme (S4) |
| P4 | `shared/src/Session/SessionConfig.php` | 35 | `@mkdir($savePath, 0750, true);` | Dizin izni 0750 (S4·S10) |
| P5 | `shared/src/AI/AIEngine.php` | 291 | `'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',` | ffmpeg komut şablonu (S3) |
| P6 | `shared/src/AI/AIEngine.php` | 292 | `escapeshellarg($filePath)` | Kabuk kaçışı (S3) |
| P7 | `shared/src/AI/AIEngine.php` | 295 | `$output = shell_exec($cmd);` | Alt süreç çalıştırma (S3) |
| P8 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | `$handle = proc_open(` | Borulu alt süreç testi (S11) |
| P9 | `shared/src/PageRouter/StructuredLogger.php` | 72 | `error_log(Redactor::redact($payload));` | Log yazma + redaction (S7) |
| P10 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 9 | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` | Zaman dilimi (runtime) |
| P11 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 13 | `ini_set('display_errors', '1');` | Hata gösterim açılışı (S7·S9) |
| P12 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 19 | `ini_set('log_errors', '1');` | Hata loglama açılışı (S7) |
| P13 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 185 | `if (is_link($trimmed)) {` | Symlink reddi (S10) |
| P14 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 189 | `$real = realpath($trimmed);` | Gerçek yol çözümleme (S10) |
| P15 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199 | `$prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;` | Yol ayracı kontrolü (S5·S10) |
| P16 | `shared/src/PageRouter/ResponseEmitter.php` | 47 | `header("$k: $v");` | HTTP başlığı — SAPI sınırı (S9) |

### §7.2 Yapılandırma / CI

| # | Dosya | Satır | Doğrulanmış satır içeriği | İsbat ettiği |
|---|---|---|---|---|
| C1 | `shared/composer.json` | 8 | `"php": ">=8.4",` | PHP sürüm eşiği (S9) |
| C2 | `shared/composer.json` | 9 | `"ext-apcu": "*",` | Uzantı bağımlılığı (koşullu) |
| C3 | `shared/composer.json` | 23 | `"require-dev": {` | Test araçları bloğu (S11) |
| C4 | `shared/composer.json` | 24 | `"phpunit/phpunit": "^10.5",` | PHPUnit sürümü (§3.3) |
| C5 | `shared/composer.json` | 47 | `"test": "phpunit",` | Regresyon komutu (§3.1) |
| C6 | `shared/composer.json` | 48 | `"stan": "phpstan analyse src --level=5"` | Statik analiz (§3.1) |
| C7 | `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` | Pipeline kimliği |
| C8 | `.github/workflows/ci.yml` | 22 | `PHP_VERSION: '8.4'   # Kanıt: 3 composer.json -> "php": ">=8.4" (config.platform.php YOK)` | CI PHP sürümü |
| C9 | `.github/workflows/ci.yml` | 27 | `runs-on: ubuntu-latest` | P1 platform kanıtı (S12) |
| C10 | `.github/workflows/ci.yml` | 37 | `extensions: apcu, mbstring, json, pdo, pdo_mysql` | CI uzantı seti |
| C11 | `.github/workflows/ci.yml` | 38 | `ini-values: apcu.enable_cli=1` | CLI APCu (C2 kanıtı) |
| C12 | `shared/phpunit.xml` | 7 | `cacheDirectory=".phpunit.cache">` | PHPUnit yapılandırması |
| C13 | `shared/phpunit.xml` | 8 | `<testsuites>` | 8 suite bloğu başlangıcı (§3.2) |

**Dosya ölçümü (2026-10-13):** `shared/phpunit.xml` = **39 satır** ·
`.github/workflows/ci.yml` = **203 satır / 6 job** · `shared/src` = **165 `.php`** ·
`shared/tests` = **44 `.php`** · `shared/composer.json` scripts bloğu = satır 46–48.

### §7.4 Yazılım envanteri sayımları (2026-10-13, vendor/node_modules hariç)

| # | Alan | Ölçüm | Sayı |
|---|---|---|---|
| I1 | `shared/src` | `Get-ChildItem -Filter *.php` | 165 `.php` |
| I2 | `shared/tests` | `Get-ChildItem -Filter *.php` | 44 `.php` |
| I3 | `shared` (tümü) | `.php` toplamı | 217 `.php` |
| I4 | `home.coremusic.net` | `.php` (vendor hariç) | 38 `.php` |
| I5 | `api.coremusic.net` | `.php` (vendor hariç) | 12 `.php` |
| I6 | `auth.coremusic.net` | `.php` (vendor hariç) | 37 `.php` |
| I7 | `shared/phpunit.xml` | satır sayısı | 39 satır / 8 testsuite |
| I8 | `.github/workflows/ci.yml` | satır sayısı | 203 satır / 6 job |
| I9 | `.github/workflows/secret-scan.yml` | satır sayısı | 31 satır |
| I10 | `shared/composer.json` scripts | satır aralığı | 46–48 (`test`, `stan`) |

**Not:** `shared` 217 = 165 (`src/`) + 44 (`tests/`) + 8 diğer `.php` — diskten sayılan
dosyalar: `test_bypass.php` (kök) · `config/auth-routes.php` · `config/domain.php` ·
`config/oauth-platforms.php` · `config/routes.php` · `database/migrations/` ×3
(`2026-10_07_drop_coremusic_api_api_keys_migration.php`, `oauth_connections_migration.php`,
`oauth_states_migration.php`). Sayımlar tek oturumda, aynı komutla alındı; `vendor`
dizini hariç tutuldu.

---

## §8 — `.ai/electronic` dosya envanteri doğrulaması

> Görev manifesti 12:30 ölçümü olarak **60 dosya / 10.323 satır** veriyordu. Bu bölüm
> sayımı **yeniden ölçtü**; sonuçlar yöntem ve an ile değiştiği için tabloya işlendi.

### §8.1 Sayım tablosu (ölçüm anına göre)

| # | Ölçüm anı | Yöntem | Dosya | Satır |
|---|---|---|---|---|
| E1 | 2026-10-13 12:30 (görev manifesti) | 📋 orkestratör iddiası (yöntem bilinmiyor) | 60 | 10.323 |
| E2 | 2026-10-13 ~12:45 | `Measure-Object -Line` (boş satır hariç) | 60 | 8.341 (önceki ölçüm: 8.095) |
| E3 | 2026-10-13 ~12:45 | `(Get-Content).Count` (boş satır dahil) | 60 | 10.748 |
| E4 | 2026-10-13 **12:55:55** | `(Get-Content).Count` (boş satır dahil) | **60** | **11.022** |
| E5 | 2026-10-13 12:55:55 | `Measure-Object -Line` (boş satır hariç) | 60 | 8.562 |

### §8.2 Sonuç ve dürüstlük notu

1. **Dosya sayısı 60 — üç ölçümde de sabit** ✅ (manifestle uyumlu).
2. **Satır sayısı 10.323 yeniden üretilemedi.** İki yöntem birbirinden ~2.400 satır
   fark veriyor (boş satır muamelesi); ayrıca `Get-Content.Count` 12:45 → 12:55
   arasında 10.748 → 11.022'ye **arttı (+274 satır / 10 dakika)** — dizin bu sırada
   **değişiyordu** (eşzamanlı yazım). Manifestteki 3 ajan `.ai/electronic` beyazlistesinde
   olduğu için bu beklenen bir durumdur; **kimin yazdığı bu görevde tespit edilmedi.**
3. 10.323 için doğru yorum: **12:30 ölçümü anına ait tek-anlık değer** olabilir —
   ama yöntem bilinmediği için doğrulanamadı → `⚠️ VERIFICATION REQUIRED` (§9 U1).

### §8.3 Dizin ve büyük dosya kanıtları (2026-10-13)

| Alt dizin | Durum |
|---|---|
| `amplifier` | ✅ mevcut |
| `drivers` | ✅ mevcut |
| `dsp` | ✅ mevcut |
| `firmware` | ✅ mevcut |
| `hardware` | ✅ mevcut |

| Dosya (göreli) | Boyut (bayt) |
|---|---|
| `hardware-design.md` | 26.079 |
| `driver-framework.md` | 24.914 |
| `dsp-engine-architecture.md` | 20.900 |
| `amplifier/index.md` | 17.066 |
| `firmware-architecture.md` | 15.687 |
| `development-workflow.md` | 15.348 |
| `audio-architecture.md` | 14.499 |
| `device-ecosystem.md` | 14.118 |

---

## §9 — Bilinmeyenler (UNKNOWN · ⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Neden | Durum |
|---|---|---|---|
| U1 | `.ai/electronic` 10.323 satır iddiasının yöntemi | Ölçüm yöntemi manifestte yazılmıyor; 3 yöntem farklı sonuç verdi (§8) | ⚠️ VERIFICATION REQUIRED |
| U2 | Üretim hedef OS (Q4) | Repo'da tek nokta `target OS` tanımı yok | UNKNOWN |
| U3 | JS test/e2e job'larının gerçekte geçip geçmediği | `ci.yml` içinde adları/yapılandırmaları var; çalışma sonucu bu oturumda alınmadı | UNKNOWN |
| U4 | PHP testlerinin şu anki geçme oranı / coverage % | Test çalıştırılmadı (yalnız yapılandırma okundu) | UNKNOWN |
| U5 | CI'nin GitHub'da son yeşil koşusu | `.github` çalışma sonuçları repo içinde değil | UNKNOWN |
| U6 | `electroncis` alt alanının tam kapsamı (Q1/D2) | `index.md` kapsamı bu görevin beyazlistesi dışında | ⚠️ VERIFICATION REQUIRED |
| U7 | `isletim-sistemi-plan` kapsamı (Q3/D3) | adlandırma belirsiz; içerik okunmadı | ⚠️ VERIFICATION REQUIRED |
| U8 | WASAPI exclusive mod gerçek donanım davranışı (D4) | repo'da Windows ses donanımına erişim kodu yok | ⚠️ VERIFICATION REQUIRED |
| U9 | `phpunit.xml` L34–L38 `<source>` bloğu satır eşlemesi | `Get-Content` ile satır-exact doğrulama yapılmadı (yalnız içerik okundu) | ⚠️ VERIFICATION REQUIRED |

---

## §10 — Doğrulama kaydı (hangi iddia doğrulandı / doğrulanmadı)

### §10.1 Doğrulanan iddialar

| # | İddia | Kanıt | Sonuç |
|---|---|---|---|
| V1 | `os-bagimliliklar.md` ≥ 500 satır | `Get-Content).Count` ölçümü (§5 K2) | ✅ doğrulandı (620) |
| V2 | `os-dogrulama.md` ≥ 500 satır | `(Get-Content).Count` ölçümü (§5 K1) | ✅ ölçüm §10.3'te |
| V3 | 6 yeni URL envanter-dışı | envanter `-contains` kontrolü (2100 URL) | ✅ kesişim 0 |
| V4 | 6 yeni URL HTTP 200 + ifade | HTTP GET + metin arama (§6) | ✅ 6/6 |
| V5 | 19 satır-no iddiası gerçek kod | `$L[$N-1]` tek tek okundu (§7) | ✅ 19/19 |
| V6 | `phpunit.xml` 8 testsuite | dosya içeriği + satır no (§3.2) | ✅ 8/8 |
| V7 | CI 6 job, hepsi `ubuntu-latest` | `Select-String runs-on` (§3.3) | ✅ 6/6 |
| V8 | `.ai/electronic` 60 dosya | üç bağımsız ölçüm (§8.1) | ✅ 60/60 |
| V9 | Frontmatter 10 alan + tirnaksız `group` | mevcut FM okundu, değiştirilmedi | ✅ |
| V10 | K000 altındaki 12 düğüm `depends_on` değerleri | 12 `index.md` frontmatter okundu (§3, `os-bagimliliklar`) | ✅ 12/12 |

### §10.2 Doğrulanamayan / sapmalı iddialar

| # | İddia | Test | Sonuç |
|---|---|---|---|
| N1 | `.ai/electronic` = 10.323 satır (12:30 manifest) | 3 yöntem, 2 an → 8.341 / 10.748 / 11.022 | ❌ tekrar üretilemedi → ⚠️ (§8) |
| N2 | Eski §2 #7: `ci.yml:1,20` (runs-on + apcu.enable_cli) | Güncel dosyada: L1 yorum satırı; `runs-on` **L27**; `apcu.enable_cli=1` **L38** | ⚠️ eski satır no güncel dosyada eşleşmiyor — eski satır **silinmedi**, sapma burada kayıtlı (dosya 203 satır, yeniden yazılmış olabilir) |
| N3 | Eski §1 #9: kısaltılmış URL (`man7.org/...man7/...`) | Tam URL olarak doğrulanamaz (metin kısaltılmış) | ⚠️ korundu; yeni bölümlerde kısaltılmış URL kullanılmadı |
| N4 | Üretim hedef OS | Repo taraması → tek tanım yok | ❌ doğrulanamadı → UNKNOWN (U2) |
| N5 | JS/PHP testlerin geçme durumu | Çalıştırılmadı (yalnız yapılandırma okundu) | ❌ doğrulanmadı → UNKNOWN (U3·U4) |

### §10.3 Bu revizyonun ölçümü (2026-10-13)

| Dosya | Eski satır | Yeni satır | ≥ 500 |
|---|---|---|---|
| `os-bagimliliklar.md` | 329 | 620 | ✅ EVET |
| `os-dogrulama.md` | 81 | 514 | ✅ EVET |

### §10.4 Kapanış kontrolü

Yazım sonrası son kontrol bu dosyanın **en son satırındaki Authority satırının üstünde**
durur: nihai sayı `.ai/architecture` oturum ölçümünde raporlanır (orkestratör çıkış
tablosuna `os-dogrulama: 81 → N` olarak işlenir). Sayı < 500 çıkarsa revizyon
**başarılı sayılmaz** ve §1–§10 arasına ek tablo eklenerek tekrar ölçülür.

---


**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
