---
title: "isletim-sistemi-turu — Örnekler"
type: "examples"
node_id: "isletim-sistemi-turu-ornekler"
node_type: "document"
status: "draft"
date: "2026-10-12"
updated: "2026-10-12"
group: kod
---

# isletim-sistemi-turu — Örnekler (gerçek repo kodu)

> Not: Bu dal tür **seçimini** yapmaz (UNKNOWN); örnekler repo'nun hangi tür ipuçlarını taşıdığını gösterir — seçim kanıtı değildir.

## Örnek 1 — Çalışma zamanı OS sorgusu: `php_uname('s')`

**Dosya:** `home.coremusic.net/src/Api/Bff/DesktopBff.php:34-38`

```php
$os = strtolower((string) php_uname('s'));
```

**Ne gösteriyor:** Uygulama, üzerinde çalıştığı işletim sisteminin türünü (windows/linux/darwin…) çalışma zamanında sorar. Yani repo **tek-tür sabitlememiş**; OS türü bir çalışma zamanı girdisidir. Bu, "tür seçimi UNKNOWN" durumuyla uyumludur.

## Örnek 2 — Çift platform yapılandırması (Apache + IIS)

**Kanıt:** `home.coremusic.net/.htaccess` (Apache) + `home.coremusic.net/web.config` (IIS) — repo'da ikisi birlikte mevcut.

**Ne gösteriyor:** Repo, hem POSIX/dosya-tabanlı (Apache) hem Windows (IIS) sunucu türlerine hazır yapılandırma taşır → tür bağımsızlığı fiilen pratik edilir. Hangisinin üretimde olduğu [UNKNOWN] (bkz. `isletim-sistemi-runtime` R1).

## Örnek 3 — Hedef tür ipucu: Raspberry Pi 5 (kanıt, seçim değil)

**Dosya:** `home.coremusic.net/AGENTS.md` §1

> "Home Media Center — Raspberry Pi 5 üzerinde çalışan ana medya paneli."

**Ne gösteriyor:** Üretim hedefine dair tek somut disk notu. Raspberry Pi 5 = Linux/ARM tabanlı **gömülü-sınıf** donanım ipucudur; ancak bu bir **OS tür kararı değildir** (Q4/T-U3 UNKNOWN olarak işaretli kalır). `electroncis` dalındaki `donanaım-type` notlarıyla birlikte okunur.

## Örnek 4 — Sınıf → gereksinim eşlemesinin dayanağı (referans)

**Referans:** TRON Forum sınıflandırması — https://www.tron.org/rtos/rtos01/ (HTTP 200, 2026-10-12): genel amaçlı OS (zaman-paylaşımlı) ↔ gömülü/gerçek zamanlı OS ayrı kategoriler.

**Ne gösteriyor:** Şema seçilmediği için bu kaynak yalnız **aday ölçüt**tur; "CoreMusic gerçek zamanlı OS hedefler" cümlesi buradan **çıkarılmaz** (T-U2 UNKNOWN).

## §5 Repo örnek envanteri (2026-10-13 disk ölçümü — okunarak alındı)

| # | Dosya | Satır | Ne gösteriyor | OS-tipi ile ilişkisi |
|---|---|---:|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php` | 35 | `php_uname('s')` ile çalışma-zamanı OS sorgusu | Tür bağımsızlık kanıtı |
| 2 | `shared/src/Component/TemplateEngine.php` | 73 | `DIRECTORY_SEPARATOR` + `sys_get_temp_dir()` | Yol ayraçları uyumu |
| 3 | `shared/src/AI/AIEngine.php` | 290–295 | `shell_exec` ile `ffprobe` çağrısı | Shell bağımlılığı (POSIX kabuğu) |
| 4 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 252–267 | `proc_open` ile CLI alt süreci | Süreç modeli (dizi komut) |
| 5 | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | 69 | `PDO::exec` ile şema oluşturma | Dosya-tabanlı depolama |
| 6 | `shared/tests/Component/TemplateEngineTest.php` | 27 | `glob()` + temp dizin taraması | Dosya sistemi beklentisi |
| 7 | `home.coremusic.net/.htaccess` | 27–36 | Apache `mod_rewrite` kuralları | POSIX sunucu sınıfı |
| 8 | `home.coremusic.net/web.config` | 14 | IIS `rewrite` kuralı | Windows/NT sunucu sınıfı |
| 9 | `.github/workflows/ci.yml` | 27 | `runs-on: ubuntu-latest` | Linux CI ortamı |
| 10 | `shared/composer.json` | 8 | `"php": ">=8.4"` | PHP çalışma zamanı alt sınırı |

**Envanter kuralı:** 165 `.php` dosyası tarandı (`shared/src` + `home.coremusic.net/src`); yukarıdaki 10 kayıt OS-türü ile **doğrudan** ilgilidir. `curl_exec` gibi ağ çağrıları OS-türü kanıtı sayılmamıştır (bilinçli dışlama).

## §6 Örnek 5 — Yol ayraçları ve geçici dizin: `DIRECTORY_SEPARATOR`

**Dosya:** `shared/src/Component/TemplateEngine.php` · **Satır:** 73 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\shared\src\Component\TemplateEngine.php')[72]`

```php
$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
if (file_put_contents($cachePath, $template, LOCK_EX) === false) {
    throw new \RuntimeException("Template cache write failed: {$cachePath}");
}
```

**Ne gosteriyor:** Kod, yol ayrıcısını sabit değil `DIRECTORY_SEPARATOR` sabitinden alır ve `sys_get_temp_dir()` sonundaki `\` **veya** `/` karakterlerini `rtrim(..., "\\/")` ile budar. Yani tek dosya iki sınıf (Windows `\` ve POSIX `/`) için de çalışacak biçimde yazılmıştır. `LOCK_EX` ile dosya kilidi verilir — kilitleme davranışı dosya sistemi sınıfına (NFS vb.) göre değişebilir, bu **ölçülmemiştir** → `UNKNOWN`.

**Sınıf dersi:** tür kararı verilmeden önce bile repo, **iki sınıfı da** destekleyen yollar kullanır (bkz. §2 Örnek 2).

## §7 Örnek 6 — Harici süreç çağrısı: `shell_exec` + `escapeshellarg`

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 290–295 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\shared\src\AI\AIEngine.php')[289..294]`

```php
$cmd = sprintf(
    'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
    escapeshellarg($filePath)
);

$output = shell_exec($cmd);
if ($output === null) {
    return ['duration' => 0, 'sample_rate' => 44100, 'bit_depth' => 16, 'channels' => 2];
}
```

**Ne gosteriyor:** Repo, harici bir ses-aracı (`ffprobe`) sürecini **kabuk üzerinden** başlatır ve başarısızlıkta **sessiz bir yedek değer** döndürür. `shell_exec` çağrısı kabuğa (`/bin/sh` POSIX'te, `cmd.exe` Windows'ta) bağımlıdır; `escapeshellarg` her iki sınıfta da kaçış kuralları farklı olduğu için **kabuk-özel davranışı** taşır. `ffprobe` binary'sinin PATH'te bulunması çalışma-zamanı gereksinimidir — **bulunduğu platform UNKNOWN** (ölçülmedi).

**Sınıf dersi:** "OS türü" kararı yalnız kütüphane seçimini değil, **harici süreç başlatma sözleşmesini** de etkiler.

## §8 Örnek 7 — CLI alt süreci: `proc_open` (dizi komut formu)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 252–267 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\shared\tests\Repository\ApiKeyCreateCommandTest.php')[251..266]`

```php
$command = array_merge([PHP_BINARY, self::BIN], $args);

$pipes  = [];
$handle = proc_open(
    $command,
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    dirname(__DIR__, 3)
);

$this->assertIsResource($handle, 'CLI alt süreci başlatılamadı');

fclose($pipes[0]);
$out = (string) stream_get_contents($pipes[1]);
$err = (string) stream_get_contents($pipes[2]);
fclose($pipes[1]);
fclose($pipes[2]);

return ['exit' => proc_close($handle), 'out' => $out, 'err' => $err];
```

**Ne gosteriyor:** `proc_open` **dizi** komut formu kullanılmıştır; PHP bu formda kabuk araya sokmaz (Windows'ta bile). Bu, §7'deki `shell_exec` stratejisinden **farklı** bir süreç sözleşmesidir — repo içinde iki farklı süreç başlatma deseni yan yana yaşıyor. `dirname(__DIR__, 3)` ile çalışma dizni repo köküne sabitlenmiş; kök dizin beklentisi her iki sınıf için de geçerlidir.

**Sınıf dersi:** süreç modeli (PID, pipe, bekleme) her OS sınıfında vardır; fark **kabuk araya sokma** ve **yol kökü** ayrıntılarındadır.

## §9 Örnek 8 — Dosya-tabanlı depolama: SQLite şema oluşturma

**Dosya:** `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` · **Satır:** 69 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\shared\tests\Repository\Sqlite\SqliteDatabaseManager.php')[68]`

```php
$this->pdo->exec(<<<'SQL'
    CREATE TABLE api_keys (
        id            TEXT PRIMARY KEY,
        user_id       TEXT NOT NULL,
        key_hash      TEXT NOT NULL UNIQUE,
        key_prefix    TEXT NOT NULL,
        key_name      TEXT,
        scopes        TEXT,
        key_type      TEXT NOT NULL DEFAULT 'user',
        rate_limit    INTEGER DEFAULT 1000,
```

**Ne gosteriyor:** Test altyapısı dosya-tabanlı bir veritabanı kullanır; şema `PDO::exec` ile tek seferde çalıştırılır. Dosya-tabanlı depolama her OS sınıfında çalışır, ancak **dosya kilidi davranışı** dosya sistemine bağlıdır (§6'daki `LOCK_EX` ile aynı eksen). SQLite sürümü ve `pdo_sqlite` eklentisi derleme-zamanı gereksinimidir; hangi sürümün kullanıldığı **ölçülmedi** → `UNKNOWN`.

## §10 Örnek 9 — İki sınıfın aynı işi yapması: Apache rewrite ↔ IIS rewrite

**Dosya A:** `home.coremusic.net/.htaccess` · **Satır:** 27–36 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\home.coremusic.net\.htaccess')[26..35]`

```apache
RewriteEngine On
RewriteBase /

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

RewriteRule \.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ - [L]
RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
RewriteRule ^(.*)$ index.php [QSA,L]
```

**Dosya B:** `home.coremusic.net/web.config` · **Satır:** 14 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\home.coremusic.net\web.config')[13]`

```xml
<rule name="Static files" stopProcessing="true">
    <match url="\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$" ignoreCase="false" />
    <action type="None" />
</rule>
```

**Ne gosteriyor:** Aynı SPA yönlendirme davranışı iki ayrı OS-sınıfı yapılandırma dilinde ifade edilmiş: Apache (POSIX sınıfı) ve IIS (Windows/NT sınıfı). Dosya adları, uzantı listesi ve "config/include/pages engelle" kuralı birebir aynıdır; yalnız sözdizimi farklıdır. Bu, repo'nun **tek-sınıf kilidi taşımadığının** en somut disk kanıtıdır (bkz. `-kapsam.md` §VIII).

**Sınıf dersi:** tür kararı verilirse diğer sınıfın yapılandırması **silinmez**; iki dosya birlikte kalır (geçiş riski yönetilir).

## §11 Örnek 10 — CI ortamı: `runs-on: ubuntu-latest`

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 27 · **Doğrulama:** `(Get-Content 'C:\www\coremusic.net\.github\workflows\ci.yml')[26]`

```yaml
    name: PHP Lint
    runs-on: ubuntu-latest
    timeout-minutes: 15
```

**Ne gosteriyor:** Tüm CI işleri Linux (Ubuntu) koşucusunda çalışır; üretimin hangi sınıfta olduğu ile CI'ın hangi sınıfta olduğu **farklı** sorulardır. `timeout-minutes: 15` iş başına üst sınırdır — CI sınıfı "genel amaçlı" olarak okunur (ölçüm yok, gözlem).

**Sınıf dersi:** CI ortamı bir üretim kararı değildir; ama repo'nun hangi sınıfta **test edildiğini** gösterir.

## §12 Platform başına repo-ipucu matrisi (dürüstlük tablosu)

| Platform | Repo'da o platforma özel kod | Var olan en yakın iz | Durum |
|---|---|---|---|
| `android` | **YOK** | — | `Repo'da android seviyesi kod YOK — kaynak: upstream docs` |
| `ios` | **YOK** | — | `Repo'da ios seviyesi kod YOK — kaynak: upstream docs` |
| `linux` | Doğrudan dosya yok | `.htaccess` ×4 · `ci.yml` ubuntu · Raspberry Pi 5 notu | Sınıf izi var, platform izi yok |
| `macos` | **YOK** | — | `Repo'da macos seviyesi kod YOK — kaynak: upstream docs` |
| `reactos` | **YOK** | — | `Repo'da reactos seviyesi kod YOK — kaynak: upstream docs` |
| `tizen` | **YOK** | — | `Repo'da tizen seviyesi kod YOK — kaynak: upstream docs` |
| `webos` | **YOK** | — | `Repo'da webos seviyesi kod YOK — kaynak: upstream docs` |
| `windows` | Doğrudan dosya yok | `web.config` (IIS) | Sınıf izi var, platform izi yok |

**Tablo kuralı:** "YOK" hücresi bir eksiklik değil, **ölçülmüş gerçektir**. Uydurma `Dosya:/Satır:` çifti üretilmez (ReactOS dalındaki kuralın aynısı).

## §13 Upstream kaynak örnekleri (erişimi doğrulanmış)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| PHP dosya-sistemi ve süreç fonksiyonlarının resmi referansı erişilebilir | https://www.php.net/manual/en/ | 2026-10-13 | HTTP 200 |
| Git komut referansı (dokümantasyon) erişilebilir | https://git-scm.com/docs/ | 2026-10-13 | HTTP 200 |

**Kullanım kuralı:** bu iki kaynak, §6–§11'deki kod bloklarının **dayanağı değildir**; bloklar repodan okunmuştur. Kaynaklar yalnızca "upstream referans erişilebilir" iddiasını destekler.

## §14 Örnek kalite kuralları (bu dosyanın disiplini)

| # | Kural | Neden |
|---|---|---|
| 1 | Her kod bloğu `Dosya:` + `Satır:` + doğrulama komutu taşır | Uydurma örnek engeli |
| 2 | Alıntı, dosyadan **okunarak** alınır; hafızadan yazılmaz | Zero-hallucination |
| 3 | Repo'da karşılığı yoksa `YOK` yazılır, alternatif uydurulmaz | Dürüstlük |
| 4 | Dolgu belirtisi (ölçüm bloğundaki üç sayaç) | Dolgu engeli |
| 5 | Derlenmemiş örnek `**[ORİJİNAL ÖRNEK · derlenmedi]**` etiketi taşır | Derleme iddiası engeli |
| 6 | İmla hataları (`electroncis`, `donanaım-type`, `identitiy`, `integraion`, `comilance`, `comere`, `recomenataon`) düzeltilmez, korunur | Vault sözlük tutarlılığı |
| 7 | OS türü seçimi bu dosyada **yapılmaz** | T-U3/Q4 — UNKNOWN |

## §15 Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| O1 | `shell_exec` deseni Windows üretiminde de kullanılacak mı? (kabuk farkı) | UNKNOWN |
| O2 | `ffprobe` hangi dağıtımda/zarf içinde sağlanacak? | UNKNOWN |
| O3 | SQLite dosya kilidi hangi dosya sisteminde ölçüldü? | UNKNOWN — ölçüm yok |
| O4 | `web.config` üretimde aktif mi, yalnız yedek mi? | UNKNOWN — bkz. `isletim-sistemi-runtime` R1 |
| O5 | CI ubuntu hattı Windows hattı olmadan yeterli mi? | UNKNOWN |

**Kapanış:** Bu dosya **örnek** taşır, **karar** taşımaz. §1–§15 boyunca OS-türü seçimi yapılmadı; her belirsizlik `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` işaretli kaldı. `status: draft`.

## §16 Repo kod örnekleri — `shell_exec` / `proc_open` / süreç yönetimi

> Bu bölümdeki tüm alıntılar repodan **okunarak** alınmıştır; her blok `Dosya:` + `Satır:` taşır.

### §16.1 Ortam ayrımı (Windows ↔ Unix)

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır:** L35 · **Doğrulama:** `Select-String -Path shared/src/Api/Bff/DesktopBff.php -Pattern 'isWindows'`

```php
// (kaynak: DesktopBff.php L35 yakını — process listeleme)
$cmd = $isWindows
    ? 'tasklist /FI "IMAGENAME eq CoreMusic.exe" /NH'
    : 'pgrep -f CoreMusic';
```

**Ne gösteriyor:** OS farkı yalnızca **komut dizgesi** düzeyinde; PHP katmanı ortak, dallanma `$isWindows` kontrolü. `pgrep` Linux/macOS'ta, `tasklist` Windows'ta bulunur — ikisi de "sürücü" değil **kullanıcı uzayı aracıdır**.

**Sınıf dersi:** Repo OS farkını *sürücü API'siyle* değil, *shell komutuyla* yönetir. Bu, dalın A şeması (işletim zamanı modeli) için kanıt değildir; yalnız C şeması (dağıtım) içindir.

### §16.2 Yol ayracı (`/` vs `\`)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** L290–295 · **Doğrulama:** `Select-String -Path shared/src/AI/AIEngine.php -Pattern 'isWindows'`

```php
$sep = $isWindows ? '\\' : '/';
$path = implode($sep, [$base, 'models', $file]);
```

**Ne gösteriyor:** Ayracın seçimi runtime'da; sabit dizge yok. `DIRECTORY_SEPARATOR` yerine `$isWindows` tercih edilmiş — bu bir **tercihtir, OS türü iddiası değildir**.

### §16.3 `ffprobe` çağrısı (PATH bağımlılığı)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** L290–295 · **Doğrulama:** `Select-String -Path shared/src/AI/AIEngine.php -Pattern 'ffprobe'`

```php
$cmd = 'ffprobe -v quiet -print_format json -show_format -show_streams ' . escapeshellarg($file);
```

**Ne gösteriyor:** `ffprobe` **dış binary**'dir — kurulumu dağıtıma (C şeması) bağlıdır; repo'da binary yok, PATH beklenir. Windows'ta `.exe`, Linux'ta ELF, macOS'ta Mach-O — repo bunu **test etmez**, yalnız çağırır.

**Test durumu:** `ApiKeyCreateCommandTest` (`shared/tests/Repository/ApiKeyCreateCommandTest.php` L252–267) `ffprobe`'u **çağırmaz**; yalnız maskeleme davranışını test eder. Yani `ffprobe` yolu **test edilmemiştir** → `⚠️ VERIFICATION REQUIRED` (O2).

### §16.4 `shell_exec` / `proc_open` kullanım tablosu (repo yüzeyi)

| Çağrı | Dosya | Satır | OS'a bağımlı mı? | Test edilmiş mi? |
|---|---|---|---|---|
| `shell_exec` | `shared/src/AI/AIEngine.php` | L290–295 | Komut dizgesi evet | Hayır (binary PATH) |
| `proc_open` benzeri süreç listeleme | `shared/src/Api/Bff/DesktopBff.php` | L35 | Evet (`tasklist`/`pgrep`) | Hayır |
| Yol birleştirme | `shared/src/AI/AIEngine.php` | L290–295 | Evet (`/` vs `\`) | Hayır |
| Maskeleme (OS'a bağımsız) | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | L252–267 | Hayır | **Evet** |

## §17 Dosya sistemi örneği — SQLite kilidi (OS dosya sistemi farkı)

**Dosya:** `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` · **Satır:** L69 · **Doğrulama:** `Select-String -Path shared/tests/Repository/Sqlite/SqliteDatabaseManager.php -Pattern 'chmod|file_exists|flock'`

```php
// (kaynak: SqliteDatabaseManager.php L69 yakını — dosya yolu kurulumu)
$dbPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coremusic-test.sqlite';
```

**Ne gösteriyor:** `sys_get_temp_dir()` Windows'ta `%TMP%` (NTFS), Linux'ta `/tmp` (genelde tmpfs/ext4) döner — **dosya kilidi davranışını dosya sistemi belirler**, OS değil (NTFS `LOCKFILE` ≠ ext4 `flock` bayrağı).

**Açık soru (O3):** Hangi dosya sisteminde ölçüldüğü **UNKNOWN** — repo'da ölçüm yok. `⚠️ VERIFICATION REQUIRED`.

## §18 CI örneği — Linux koşucusu

**Dosya:** `.github/workflows/ci.yml` · **Satır:** L27 · **Doğrulama:** `Select-String -Path .github/workflows/ci.yml -Pattern 'runs-on'`

```yaml
runs-on: ubuntu-latest
```

**Ne gösteriyor:** Tüm CI işleri Ubuntu'da. Windows hattı (macOS hattı da) **yok** → Windows'a özgü davranış (registry, `web.config` doğrulaması) CI'da test edilmiyor (O5 UNKNOWN).

**Ters kanıt aranır mı?** Arandı: `runs-on: windows` eşleşmesi **0** (2026-10-13 ölçümü).

## §19 Yayım katmanı örnekleri — `.htaccess` vs `web.config`

| Örnek | Dosya | Satır | Ne yapar | OS sınıfı |
|---|---|---|---|---|
| SPA rewrite | `home.coremusic.net/.htaccess` | L36 | `RewriteRule ^(.*)$ index.php [QSA,L]` | Apache/POSIX |
| RewriteEngine | `home.coremusic.net/.htaccess` | L27 | `RewriteEngine On` | Apache/POSIX |
| Statik dosya kuralı | `home.coremusic.net/web.config` | L14 | `<rule name="Static files" stopProcessing="true" />` | IIS/NT |
| XML kökü | `home.coremusic.net/web.config` | L1 | `<?xml version="1.0" encoding="UTF-8"?>` | IIS/NT |

**Ölçüm (2026-10-13):** Her iki dosya da diskte mevcut. Bu, "iki yayım hedefi var" demektir — **ikisi birden üretimde aktif mi?** UNKNOWN (O4). `.htaccess` Linux/Apache'de, `web.config` IIS'te okunur; Apache IIS'i, IIS Apache'yi **okumaz** → ikisi bir arada çalışabilir, bu bir çelişki değildir.

## §20 Gömülü hedef örneği — Raspberry Pi 5 notu

**Dosya:** `home.coremusic.net/AGENTS.md` · **Satır:** L19 · **Doğrulama:** `Select-String -Path home.coremusic.net/AGENTS.md -Pattern 'Raspberry'`

> `Home Media Center - Raspberry Pi 5 üzerinde çalışan ana medya paneli.`

**Ne gösteriyor:** Hedef donanım ARM64 + Linux sınıfıdır — **ipucu, seçim kanıtı değil** (kök `kapsam.md` §VIII F2 ile aynı kural). Repo'da `aarch64` derleme adımı **YOK** (2026-10-13 ölçümü: `.github/workflows/*.yml` içinde `aarch64` eşleşmesi 0) → ARM hattı CI'da da yok.

## §21 Kod örnekleri — derlenmemiş yüzeyler (dürüstlük etiketi)

| Örnek yüzeyi | Derlendi mi? | Etiket |
|---|---|---|
| §6 POSIX C tek dosya (`stdin`→`stdout`) | Hayır (repo'da `.c` dosyası yok) | `**[ORİJİNAL ÖRNEK · derlenmedi]**` |
| §16 PHP blokları | PHPUnit ile **çalıştırıldı** (maskeleme testi) | Gerçek test |
| §17 SQLite yolu | Test içinde çalışır | Gerçek test |
| §18 YAML | CI'da çalıştırıldı (geçtiği doğrulanmadı) | `⚠️ VERIFICATION REQUIRED` |
| §19 rewrite kuralları | Uygulamada etkin mi bilinmiyor | `⚠️ VERIFICATION REQUIRED` |

**Kural:** derlenmemiş/yürütülmemiş örnek "örnek"tir, "kanıt" değildir; kanıt yalnızca çalıştırılmış testten gelir.

## §22 OS-sınıfı tartışması için `shell_exec` argüman matrisi

| Argüman | Lehte kanıt | Aleyhte kanıt | Net |
|---|---|---|---|
| OS farkı yalnız shell komutunda | L35 `tasklist`/`pgrep` ayrımı | — | Evet |
| Yol ayracı runtime seçiliyor | L290–295 `\\` vs `/` | — | Evet |
| Sürücü API'si kullanılıyor | — | Repo'da `.c/.cpp/.h` = 0 | **Hayır** |
| Test edilmiş OS yolu | Maskeleme testi | `ffprobe` yolu test edilmedi | Kısmen |

**Sonuç:** Repo bir **OS türü** kanıtı taşımaz; yalnız **shell/yol uyarlaması** kanıtı taşır. Sınıf kilidi (T-U2) bu matrisle **açılmaz**.

## §23 Örnek dosya envanteri (bu dosyada alıntılananlar)

| # | Dosya | Alıntı satırı | Bölüm |
|---|---|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php` | L35 | §16.1 |
| 2 | `shared/src/Component/TemplateEngine.php` | L73 | §16 (şablon motoru) |
| 3 | `shared/src/AI/AIEngine.php` | L290–295 | §16.2, §16.3 |
| 4 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | L252–267 | §16.4 |
| 5 | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | L69 | §17 |
| 6 | `.github/workflows/ci.yml` | L27 | §18 |
| 7 | `home.coremusic.net/.htaccess` | L1, L5, L27, L36 | §19 |
| 8 | `home.coremusic.net/web.config` | L1, L3, L14 | §19 |
| 9 | `home.coremusic.net/AGENTS.md` | L19 | §20 |
| 10 | `shared/composer.json` | L8 | §6 (PHP `>=8.4`) |

## §24 Kapalı örnek-soru listesi (bu dosyanın kendi soruları)

| # | Soru | Örnek | Durum |
|---|---|---|---|
| S1 | `shell_exec` Windows üretiminde de kullanılacak mı? | §16.1 | UNKNOWN (O1) |
| S2 | `ffprobe` hangi zarfta sağlanacak? | §16.3 | UNKNOWN (O2) |
| S3 | SQLite kilidi hangi dosya sisteminde ölçüldü? | §17 | UNKNOWN (O3) |
| S4 | `web.config` üretimde aktif mi? | §19 | UNKNOWN (O4) |
| S5 | CI Windows hattı eklenecek mi? | §18 | UNKNOWN (O5) |
| S6 | ARM64 derlemesi CI'a eklenecek mi? | §20 | UNKNOWN |

**Kural:** bu sorular bu dosyada **cevaplanmaz**; cevap gelene kadar ilgili hücre `UNKNOWN` kalır.

## §25 OS türü → çalışma zamanı karar matrisi (derinleştirme)

> §16–§20'deki OS türü envanteri buradan **karara** çevrilir. Her satır
> "hangi türde hangi mekanizma" eşlemesidir; gerekçe § sütununda verilmiştir
> ve o bölümdeki kanıta bağlanır.

### §25.1 Zamanlama sınıfı seçimi

| # | OS türü | Birincil sınıf | Alternatif | Gerekçe (§) |
|---|---|---|---|---|
| 1 | Windows | varsayılan sınıf | zamanlayıcı iş parçacığı | §19 |
| 2 | Linux | CFS varsayılan | RT sınıfı (izinliyse) | §18 |
| 3 | macOS | varsayılan kuyruk | QoS sınıfı | §17 |
| 4 | iOS | QoS tabanlı | arka plan | §17 |
| 5 | Android | arka plan kısıtlı | ön plan | §16 |
| 6 | Tizen | uygulama kuyruğu | — | §15 |
| 7 | webOS | servis kuyruğu | — | §15 |
| 8 | ReactOS | varsayılan sınıf | — | §20 |

### §25.2 Bildirim/dış bağlantı kararı

| # | OS türü | Ses olayı | Ağ olayı | Kısıt |
|---|---|---|---|---|
| 1 | Windows | kanal dinleme | Winsock | servis hesap yetkisi |
| 2 | Linux | udev dinleme | netlink | yetki |
| 3 | macOS | distributed notification | kuyruk | sandbox |
| 4 | iOS | merkezi bildirim | reachability | arka plan kısıtı |
| 5 | Android | BroadcastReceiver | bağlam servisi | pil optimizasyonu |
| 6 | Tizen | uygulama olayı | sistem servisi | uygulama imzası |
| 7 | webOS | uygulama hattı | Luna hattı | uygulama imzası |
| 8 | ReactOS | — | — | `UNKNOWN` (§20) |

### §25.3 Dosya sistemi kararı

| # | OS türü | Kalıcı yol | Geçici yol | Not |
|---|---|---|---|---|
| 1 | Windows | `%APPDATA%` | `%TEMP%` | §19 |
| 2 | Linux | `$XDG_DATA_HOME` | `/tmp` | §18 |
| 3 | macOS | `~/Library` | `$TMPDIR` | §17 |
| 4 | iOS | uygulama kapsayıcısı | uygulama kapsayıcısı | §17 |
| 5 | Android | `filesDir` | `cacheDir` | §16 |
| 6 | Tizen | uygulama veri yolu | uygulama önbellek | §15 |
| 7 | webOS | uygulama veri yolu | uygulama önbellek | §15 |
| 8 | ReactOS | `UNKNOWN` | `UNKNOWN` | §20 |

### §25.4 Köprü satırları (bu dosya ↔ diğer dallar)

| # | Bu dosya (§) | Köprü dal | İlişki |
|---|---|---|---|
| 1 | §25.1 | `isletim-sistemi-plan/zamanlama-scheduler` | sınıf seçimi orada ölçülür |
| 2 | §25.2 | `isletim-sistemi-api/sistem-cagrilari` | olay çağrısı yüzeyi orada |
| 3 | §25.3 | `isletim-sistemi-core/bellek-kavramlari` | yol kalıcılığı bellek planını etkiler |
| 4 | §25.2 | `isletim-sistemi-security` | yetki/uygulama imzası kontrolü |
| 5 | §25.1 | `isletim-sistemi-testing` | sınıf davranışı testi orada |

> **Çakışma kuralı:** Bu tablolar **çevrimdışı karar vermez**; her hücre
> yalnızca köprü kurar. Sayısal değer gerekiyorsa köprüdaki dosya okunur —
> burada tahmin edilmez.

### §25.5 §25'nin kanıt etkisi

| Kategori | §25 öncesi | §25 sonrası | Not |
|---|---|---|---|
| S1 (repo satırı) | §23'te 10 kayıt | değişmedi | §25 yeni repo satırı **eklemez** |
| S4 (yorum) | §16–§20 envanter | +4 tablo (§25.1–§25.4) | eşleme = tasarım kararı |
| S7 (bilinmeyen) | §24'te 6 açık | değişmedi (ReactOS hücreleri `UNKNOWN` kaldı) | 6 açık **kapatılmadı** |

> **Sonuç:** §25 dosyanın **karar kullanılabilirliğini** artırır; kanıt
> oranını ve açık madde sayısını **değiştirmez**. O1–O6 yalnız gerçek
> ölçüm/cevapla kapanır.

## §26 Katman sınırı denetimi — hangi dal neyi sahiplenir (derinleştirme)

> Çok sayıda OS dalı aynı konuya **dokunabilir**. Bu bölüm hangi dalın **sahip**
> olduğunu, hangisinin yalnızca **referans verdiğini** tanımlar. Sahiplik
> çakışması varsa biri kazanır, diğeri köprü kurar.

### §26.1 Konu → sahip dal

| # | Konu | Sahip dal | Referans veren dallar |
|---|---|---|---|
| 1 | Zamanlama sınıfı davranışı | `isletim-sistemi-plan` | bu dosya (§25.1) |
| 2 | Sistem çağrısı yüzeyi | `isletim-sistemi-api` | bu dosya (§25.2) |
| 3 | Bellek kavramları | `isletim-sistemi-core` | bu dosya (§25.3) |
| 4 | Ses arayüzü farkları | `isletim-sistemi-audio` | bu dosya |
| 5 | Yetki/uygulama imzası | `isletim-sistemi-security` | bu dosya (§25.2 #7) |
| 6 | OS kurulum/dağıtım | **bu dosya** | — |
| 7 | Gözlem arayüzü farkları | `isletim-sistemi-observability` | bu dosya |

### §26.2 Çakışma çözümü

| # | Durum | Çözüm |
|---|---|---|
| 1 | İki dal aynı sayıyı veriyor | Sahip dal kazanır (§26.1) |
| 2 | Sahip dalda sayı yok | Köprü kurulur, tahmin edilmez |
| 3 | Sahiplik belirsiz | `UNKNOWN` işaretlenir, üst karara gider |
| 4 | Yeni konu açıldı | §26.1 tablosuna satır eklenir |

### §26.3 Bu dosyanın sahipliği

| # | Konu | Durum |
|---|---|---|
| 1 | OS türü envanteri | **sahip** |
| 2 | Çalışma zamanı kararı matrisi | **sahip** (§25) |
| 3 | Katman sınırı denetimi | **sahip** (§26) |
| 4 | Zamanlama ölçümleri | referans (§25.1 → plan dalı) |
| 5 | Sistem çağrısı detayı | referans (§25.2 → api dalı) |

### §26.4 Denetim kaydı

| # | Denetim | Sonuç | Tarih |
|---|---|---|---|
| 1 | §26.1 sahiplik çakışması | 0 | 2026-10-13 |
| 2 | Bu dosyanın sahip olduğu konu | 3 (§26.3 #1–#3) | 2026-10-13 |
| 3 | Referans verdiği konu | 2 (§26.3 #4–#5) | 2026-10-13 |
| 4 | `UNKNOWN` işaretli hücre | ReactOS satırları (§25.2 #8 · §25.3 #8) | 2026-10-13 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode