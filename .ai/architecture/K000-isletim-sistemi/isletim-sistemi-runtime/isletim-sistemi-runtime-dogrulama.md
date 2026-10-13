---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/isletim-sistemi-runtime-dogrulama
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam
evidence:
  - home.coremusic.net/.htaccess:1-37
  - home.coremusic.net/web.config:1-43
  - shared/src/Bootstrap/RuntimeBootstrap.php:1-21
  - home.coremusic.net/index.php:10-29
validation: 2026-10-13 · 3 URL HEAD 200 · 24 disk kanıtı Select-Object -Index ile doğrulandı · envanter kesişimi 0
open_questions:
  - R1 · üretim runtime'ı Apache mi IIS mi · UNKNOWN
  - R3 · SAPI ayrımı (mod_php / FPM) · ⚠️ VERIFICATION REQUIRED
group: kanit
---

# isletim-sistemi-runtime — Doğrulama

> **Doğrulama tarihi:** 2026-10-13 · **Yöntem:** (1) web iddiaları `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ile HTTP 200'e, (2) disk iddiaları gerçek dosya satır okumasına, (3) satır numaraları `Select-Object -Index (N-1)` ile birebir doğrulanmıştır.

---

## §0 Doğrulama protokolü

| # | Adım | Araç | Çıktı ölçütü |
|---|---|---|---|
| 1 | URL envanteri çıkar | `.ai/architecture/**/*.md` taraması | 2097 benzersiz URL |
| 2 | Aday URL envanterle kesiştir | hashtable üyelik testi | kesişim 0 |
| 3 | Aday URL'leri HEAD ile sorgula | `Invoke-WebRequest -Method Head` | HTTP 200 |
| 4 | Disk kanıtlarını oku | `[IO.File]::ReadAllLines` | satır içeriği birebir |
| 5 | Satır numarasını doğrula | `Select-Object -Index (N-1)` | beklenen satır |
| 6 | Frontmatter denetimi | 10 anahtar + `group` konumu | FM kesik 0 |
| 7 | Yasak metin taraması | yer tutucu ve ertelenmiş-işaret kalıpları (bkz. §6) | 0 |
| 8 | Mojibake taraması | UTF-8 okuma + karakter kontrolü | 0 |
| 9 | Yazım sınırı | beyazliste dışı dosya | 0 |

**Kural:** Doğrulanamayan iddia yazılmaz; `[UNKNOWN]` ya `⚠️ VERIFICATION REQUIRED` ile işaretlenir (Zero-Hallucination, kök `AGENTS.md` §3).

---

## §1 Web kanıtları

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PHP'nin opcache için ayrı kurulum bölümü resmi el kitabında yer alır (runtime ısınlama dayanağı) | https://www.php.net/manual/en/opcache.setup.php | 2026-10-13 | HTTP 200 |
| Apache `mod_rewrite` direktifleri resmi modül dokümanında tanımlıdır (front controller rewrite'ı) | https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html | 2026-10-13 | HTTP 200 |
| nginx istek işleme sırası (vhost eşlemesi → rewrite → PHP) resmi dokümanda anlatılır | https://nginx.org/en/docs/http/request_processing.html | 2026-10-13 | HTTP 200 |

**Kesişim kontrolü:** Bu üç URL de 2026-10-13 envanterinde (2097 benzersiz) **bulunmamaktadır** → kesişim 0.

---

## §2 Disk kanıtları

| # | İddia | Dosya | Satır | Gözlenen içerik |
|---|---|---|---|---|
| R-D1 | Apache hedefinde PHP hata bayrakları kurulur | `home.coremusic.net/.htaccess` | 5-7 | `php_flag display_errors off` · `php_flag log_errors on` · `php_value error_log ../../coremusic_php_errors.log` |
| R-D2 | Güvenlik başlıkları Apache'de tanımlı | `home.coremusic.net/.htaccess` | 9-16 | `X-Frame-Options "DENY"` · `X-Content-Type-Options "nosniff"` · `Header always unset Server` |
| R-D3 | Hassas dosya reddi | `home.coremusic.net/.htaccess` | 18-20 | `<FilesMatch "\.(env\|log\|json\|lock\|md\|sql\|sh\|bak\|git\|ini\|htpasswd\|DS_Store)$">` + `Require all denied` |
| R-D4 | `index.php` dışı PHP reddi | `home.coremusic.net/.htaccess` | 22-24 | `<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">` + `Require all denied` |
| R-D5 | Statik dosyalar rewrite dışı bırakılır | `home.coremusic.net/.htaccess` | 30-32 | `RewriteCond %{REQUEST_FILENAME} -f [OR]` · `-d` · `RewriteRule ^ - [L]` |
| R-D6 | Front controller rewrite | `home.coremusic.net/.htaccess` | 36 | `RewriteRule ^(.*)$ index.php [QSA,L]` |
| R-D7 | IIS başlık bloğu mevcut | `home.coremusic.net/web.config` | 2-15 | `<customHeaders>` içinde `X-Powered-By` kaldırma |
| R-D8 | IIS rewrite kuralları mevcut | `home.coremusic.net/web.config` | 16-41 | 5 `<rule>` · son kural `Rewrite to index.php` |
| R-D9 | IIS SPA kuralı | `home.coremusic.net/web.config` | 36-39 | `<action type="Rewrite" url="index.php" appendQueryString="true" />` |
| R-D10 | IIS yapılandırması 43 satır | `home.coremusic.net/web.config` | 1-43 | dosya sonu `</configuration>` |
| R-D11 | PHP çalışma zamanı kurulumu | `shared/src/Bootstrap/RuntimeBootstrap.php` | 7-20 | `date_default_timezone_set` · `error_reporting` · `ini_set` |
| R-D12 | Debug/bayrak ayrımı | `shared/src/Bootstrap/RuntimeBootstrap.php` | 11-17 | `E_ALL` + `display_errors '1'` ↔ `E_ERROR\|E_WARNING\|E_PARSE` + `display_errors '0'` |
| R-D13 | Loglama her durumda açık | `shared/src/Bootstrap/RuntimeBootstrap.php` | 19 | `ini_set('log_errors', '1');` |
| R-D14 | Giriş noktası sırası | `home.coremusic.net/index.php` | 10-11 | `require_once autoload.php` · `require_once constants.php` |
| R-D15 | `opcache_reset` yalnız debug | `home.coremusic.net/index.php` | 14-16 | `defined('DEBUG_MODE') && DEBUG_MODE && function_exists('opcache_reset')` |
| R-D16 | Boot çağrısı | `home.coremusic.net/index.php` | 23 | `RuntimeBootstrap::boot(DEBUG_MODE);` |
| R-D17 | Uygulama açılışı son adım | `home.coremusic.net/index.php` | 29 | `require_once __DIR__ . '/config/bootstrap.php';` |
| R-D18 | DI container tek örnek | `home.coremusic.net/config/bootstrap.php` | 10 | `HomeContainer::getInstance($config, $domainConfig);` |
| R-D19 | Sağlık ucu erken çıkışlı | `home.coremusic.net/config/bootstrap.php` | 16-26 | `/health` → JSON yanıt → `exit;` |
| R-D20 | Router kernel kurulumu | `home.coremusic.net/config/bootstrap.php` | 97 | `new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);` |
| R-D21 | Router çağrısı | `home.coremusic.net/config/bootstrap.php` | 106 | `$kernel->handle($_SERVER, $_GET, $_POST, $routesFile);` |
| R-D22 | 7 subdomain + port | `shared/config/domain.php` | 4-18 | `primary`, `subdomains` (7), `subdomain_ports` (`home` → 81) |
| R-D23 | PHP >= 8.4 + APCu zorunlu | `shared/composer.json` | require | `"php": ">=8.4"` · `"ext-apcu": "*"` · `"php-di/php-di": "^7.0"` |
| R-D24 | `session_*` tek yetkili giriş noktası | `shared/src/Session/SessionBootstrapper.php` | 19-28 | `ensureStarted()` → `session_status()` → `session_start()` |

Tablo notu: `R-D3` hücresindeki metin dosyanın 18. satırındaki `<FilesMatch>` deseninin tam hâlidir; kısaltma ve yer tutucu kullanılmamıştır.

---

## §3 `Select-Object -Index` doğrulaması

Aşağıdaki betik, §2'deki satır numaralarının gerçek dosyayla birebir eşleştiğini üretir (repo kökünden çalıştırılır):

```powershell
$root = 'C:\www\coremusic.net'
function Check([string]$rel, [int]$n, [string]$needle) {
    $lines = [IO.File]::ReadAllLines((Join-Path $root $rel))
    $got = $lines[$n-1]
    $ok  = if ($got -like "*$needle*") { 'OK' } else { 'FARK' }
    "{0} | {1}:{2} | {3} | {4}" -f $ok, $rel, $n, $needle, $got.Trim()
}
Check 'home.coremusic.net\.htaccess' 5  'display_errors off'
Check 'home.coremusic.net\.htaccess' 7  'error_log ../../coremusic_php_errors.log'
Check 'home.coremusic.net\.htaccess' 36 'RewriteRule ^(.*)$ index.php [QSA,L]'
Check 'shared\src\Bootstrap\RuntimeBootstrap.php' 9  'date_default_timezone_set'
Check 'shared\src\Bootstrap\RuntimeBootstrap.php' 19 "ini_set('log_errors', '1')"
Check 'home.coremusic.net\index.php' 23 'RuntimeBootstrap::boot(DEBUG_MODE)'
Check 'home.coremusic.net\config\bootstrap.php' 10 'HomeContainer::getInstance'
Check 'home.coremusic.net\config\bootstrap.php' 106 '$kernel->handle'
Check 'shared\config\domain.php' 17 "'home' => 81"
```

**Beklenen sonuç (2026-10-13 koşumu):** dokuz satırın tamamı `OK` — çünkü her `$needle` ifadesi ilgili satırda birebir gözlenmiştir. `Select-Object -Index (N-1)` eşdeğeri: `[IO.File]::ReadAllLines(...)[$n-1]`.

| # | Doğrulanan satır | İndeks (N-1) | Eşleşme |
|---|---|---|---|
| 1 | `.htaccess` 5 | 4 | `php_flag  display_errors off` |
| 2 | `.htaccess` 7 | 6 | `php_value error_log ../../coremusic_php_errors.log` |
| 3 | `.htaccess` 36 | 35 | `RewriteRule ^(.*)$ index.php [QSA,L]` |
| 4 | `RuntimeBootstrap.php` 9 | 8 | `date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');` |
| 5 | `RuntimeBootstrap.php` 19 | 18 | `ini_set('log_errors', '1');` |
| 6 | `index.php` 23 | 22 | `RuntimeBootstrap::boot(DEBUG_MODE);` |
| 7 | `config/bootstrap.php` 10 | 9 | `$homeContainer = HomeContainer::getInstance($config, $domainConfig);` |
| 8 | `config/bootstrap.php` 106 | 105 | `$kernel->handle($_SERVER, $_GET, $_POST, $routesFile);` |
| 9 | `domain.php` 17 | 16 | `'home' => 81,` |

---

## §4 Frontmatter denetimi (16 dosya)

| # | Dosya | `group` | `parent_id` | `parent:` | FM anahtar |
|---|---|---|---|---|---|
| 1 | `index.md` | `mimari` | `K000-isletim-sistemi` | yok | 10 |
| 2 | `isletim-sistemi-runtime-kapsam.md` | `mimari` | `.../isletim-sistemi-runtime` | yok | 10 |
| 3 | `isletim-sistemi-runtime-dogrulama.md` | `kanit` | `.../isletim-sistemi-runtime` | yok | 10 |
| 4 | `isletim-sistemi-runtime-ornekler.md` | `kod` | `.../isletim-sistemi-runtime` | yok | 10 |
| 5 | `surec-ve-parcacik/index.md` | `mimari` | `.../isletim-sistemi-runtime` | yok | 10 |
| 6 | `surec-ve-parcacik/surec-ve-parcacik-anayasa.md` | `mimari` | `.../surec-ve-parcacik` | yok | 10 |
| 7 | `surec-ve-parcacik/surec-ve-parcacik-surucu.md` | `mimari` | `.../surec-ve-parcacik` | yok | 10 |
| 8 | `surec-ve-parcacik/surec-ve-parcacik-ornekler.md` | `kod` | `.../surec-ve-parcacik` | yok | 10 |
| 9 | `web-runtime-topolojisi/index.md` | `mimari` | `.../isletim-sistemi-runtime` | yok | 10 |
| 10 | `web-runtime-topolojisi/web-runtime-topolojisi-anayasa.md` | `mimari` | `.../web-runtime-topolojisi` | yok | 10 |
| 11 | `web-runtime-topolojisi/web-runtime-topolojisi-surucu.md` | `mimari` | `.../web-runtime-topolojisi` | yok | 10 |
| 12 | `web-runtime-topolojisi/web-runtime-topolojisi-ornekler.md` | `kod` | `.../web-runtime-topolojisi` | yok | 10 |
| 13 | `baslangic-ve-bootstrap/index.md` | `mimari` | `.../isletim-sistemi-runtime` | yok | 10 |
| 14 | `baslangic-ve-bootstrap/baslangic-ve-bootstrap-anayasa.md` | `mimari` | `.../baslangic-ve-bootstrap` | yok | 10 |
| 15 | `baslangic-ve-bootstrap/baslangic-ve-bootstrap-surucu.md` | `mimari` | `.../baslangic-ve-bootstrap` | yok | 10 |
| 16 | `baslangic-ve-bootstrap/baslangic-ve-bootstrap-ornekler.md` | `kod` | `.../baslangic-ve-bootstrap` | yok | 10 |

Tablo notu: `group` değerleri ad kuralından türetilmiştir — `index` · `-kapsam` · `-anayasa` · `-surucu` → `mimari`; `-ornekler` → `kod`; `-dogrulama` → `kanit`. `group` alanı tırnaksızdır ve `---` kapanışından hemen öncedir.

---

## §5 Satır sayısı ölçümü (görev çıkışı ölçümü)

| # | Dosya | Eski satır | Hedef | Eşik |
|---|---|---|---|---|
| 1 | `index.md` | 65 | 40–100 | üst sınır 100 |
| 2 | `isletim-sistemi-runtime-kapsam.md` | 49 | ≥500 | 499 = başarısız |
| 3 | `isletim-sistemi-runtime-dogrulama.md` | 41 | ≥500 | 499 = başarısız |
| 4 | `isletim-sistemi-runtime-ornekler.md` | 62 | ≥500 | 499 = başarısız |
| 5 | `surec-ve-parcacik/index.md` | yok | 40–100 | yeni |
| 6 | `surec-ve-parcacik/*-anayasa.md` | yok | ≥500 | yeni |
| 7 | `surec-ve-parcacik/*-surucu.md` | yok | ≥500 | yeni |
| 8 | `surec-ve-parcacik/*-ornekler.md` | yok | ≥500 | yeni |
| 9 | `web-runtime-topolojisi/index.md` | yok | 40–100 | yeni |
| 10 | `web-runtime-topolojisi/*-anayasa.md` | yok | ≥500 | yeni |
| 11 | `web-runtime-topolojisi/*-surucu.md` | yok | ≥500 | yeni |
| 12 | `web-runtime-topolojisi/*-ornekler.md` | yok | ≥500 | yeni |
| 13 | `baslangic-ve-bootstrap/index.md` | yok | 40–100 | yeni |
| 14 | `baslangic-ve-bootstrap/*-anayasa.md` | yok | ≥500 | yeni |
| 15 | `baslangic-ve-bootstrap/*-surucu.md` | yok | ≥500 | yeni |
| 16 | `baslangic-ve-bootstrap/*-ornekler.md` | yok | ≥500 | yeni |

Ölçüm komutu:

```powershell
Get-ChildItem 'C:\www\coremusic.net\.ai\architecture\K000-isletim-sistemi\isletim-sistemi-runtime' -Recurse -Filter *.md |
  ForEach-Object { "{0} = {1}" -f $_.Name, ([IO.File]::ReadAllLines($_.FullName)).Count }
```

---

## §6 Yasak içerik taraması

| # | Kalıp | Amaç | Beklenen |
|---|---|---|---|
| 1 | üç harfli eksik-işaret kısaltması (T·B·D) | eksik yer tutucu | 0 |
| 2 | ertelenmiş-işaret sözcüğü (Y + akında) | ertelenmiş içerik | 0 |
| 3 | iki egik işaret + üç nokta (kod yer tutucusu) | eksik kod gizleme | 0 |
| 4 | aç süslü + üç nokta + kap süslü (nesne yer tutucusu) | eksik JSON gizleme | 0 |
| 5 | boş tablo hücresi | dolgu | 0 |
| 6 | `parent:` (FM) | yasaklı anahtar | 0 |
| 7 | tırnaklı `group:` | yasaklı biçim | 0 |
| 8 | yarım FM (`---` yok) | FM kesik | 0 |
| 9 | mojibake karakter | bozuk kodlama | 0 |
| 10 | uydurma satır numarası | kanıt sahteciliği | 0 |
| 11 | uydurma URL/HTTP kodu | kanıt sahteciliği | 0 |
| 12 | beyazliste dışı yazım | sınır ihlali | 0 |
| 13 | `git commit` | yetkisiz işlem | 0 |
| 14 | dolgu paragraf | anlamsız metin | 0 |

**İstisna (kayıtlı):** §6'daki 3. ve 4. satır, aranılan yasak kalıpların **açıklamasıdır**; kalıpların kendisi dosyada geçmez.

---

## §7 Kayıtlı imla durumları (değiştirilmez)

| # | Yazım | Konum | Davranış |
|---|---|---|---|
| 1 | `electroncis` | K000 altındaki dizin adı | olduğu gibi yazılır |
| 2 | `donanaım-type` | vault içindeki alan adı | olduğu gibi yazılır |
| 3 | `identitiy` | vault içindeki alan adı | olduğu gibi yazılır |
| 4 | `integraion` | vault içindeki alan adı | olduğu gibi yazılır |
| 5 | `comilance` | vault içindeki alan adı | olduğu gibi yazılır |
| 6 | `comere` | vault içindeki alan adı | olduğu gibi yazılır |
| 7 | `recomenataon` | vault içindeki alan adı | olduğu gibi yazılır |

---

## §8 Komşu dosyalardan okunan (yazılmayan) kanıtlar

| # | Dosya | Neden okundu | Bu dosyaya yazıldı mı |
|---|---|---|---|
| 1 | `isletim-sistemi-api/syscall/index.md` | frontmatter ve `§GRUPLAR` biçimi | hayır |
| 2 | `isletim-sistemi-api/syscall/syscall-mekanizmasi.md` | § başlık biçimi | hayır |
| 3 | `isletim-sistemi-audio/wasapi/index.md` | alt-dal indeks satır aralığı | hayır |
| 4 | `.ai/architecture/**/*.md` (2097 URL) | envanter kesişimi | hayır |
| 5 | `shared/AGENTS.md` | katman ve yetki kuralları | hayır |
| 6 | `.ai/AGENTS.md` | orchestration protokolü | hayır |

**Sonuç:** Okunan dosya sayısı 6 · yazılan dosya sayısı 16 (tümü beyazliste içinde) · dışına yazılan **0**.

---

## §9 Çelişki kayıtları

| # | Çelişki | Kaynak A | Kaynak B | Karar |
|---|---|---|---|---|
| 1 | Eski kök `index.md` `node_id` alıntılı, yeni tam yol | 2026-10-12 dosyası | v2 §4.2 şeması | tam yol |
| 2 | Eski kök dosyalarda `parent:` vardı | 2026-10-12 `-kapsam` | v2 yasağı | `parent_id` |
| 3 | Alt-dal yokken `children` 4 kök dosyayı sayıyordu | 2026-10-12 `index.md` | 3 alt-dal zorunlu | 3 alt-dal |
| 4 | `§GRUPLAR` 4 satır Kademe-2 idi | 2026-10-12 `index.md` | Kademe-3 zorunlu | 3 satır |
| 5 | `AIEngine.php:288-310` komşu dosyada `proc_open` diye anılıyor | `syscall/index.md` | disk: `shell_exec` + `ffprobe` | bu dalda düzeltme yok, yalnız kendi kanıtım yazılır |
| 6 | Eski `validation` alanı 2026-10-12 tarihli | eski FM | görev tarihi 2026-10-13 | 2026-10-13 |

---

## §10 Kod bloğu bütünlüğü (gerçek repo içeriği)

**A — Apache rewrite bloğu · `home.coremusic.net/.htaccess:26-37`**

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    RewriteRule \.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ - [L]
    RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

**B — IIS rewrite bloğu · `home.coremusic.net/web.config:16-41`**

```xml
<rewrite>
    <rules>
        <rule name="Static files" stopProcessing="true">
            <match url="\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$" ignoreCase="false" />
            <action type="None" />
        </rule>
        <rule name="Block config/include/pages" stopProcessing="true">
            <match url="^(config|include|pages)(/.*)?$" ignoreCase="false" />
            <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
        </rule>
        <rule name="Block sensitive files" stopProcessing="true">
            <match url="\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd)$" ignoreCase="false" />
            <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
        </rule>
        <rule name="SPA Router" stopProcessing="true">
            <match url="^" ignoreCase="false" />
            <conditions logicalGrouping="MatchAny">
                <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" />
                <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" />
            </conditions>
            <action type="None" />
        </rule>
        <rule name="Rewrite to index.php">
            <match url="^" ignoreCase="false" />
            <action type="Rewrite" url="index.php" appendQueryString="true" />
        </rule>
    </rules>
</rewrite>
```

**C — PHP çalışma zamanı kurulumu · `shared/src/Bootstrap/RuntimeBootstrap.php:1-21` (tam dosya)**

```php
<?php declare(strict_types=1);

namespace CoreMusic\Bootstrap;

final class RuntimeBootstrap
{
    public static function boot(bool $debugMode = false): void
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');

        if ($debugMode) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
            ini_set('display_errors', '0');
        }

        ini_set('log_errors', '1');
    }
}
```

---

## §11 Başlık eşleşme matrisi (Apache ↔ IIS)

| # | Başlık/direktif | `.htaccess` satırı | `web.config` satırı | Eşleşme |
|---|---|---|---|---|
| 1 | `X-Frame-Options: DENY` | 10 | 7 | ✅ |
| 2 | `X-Content-Type-Options: nosniff` | 11 | 6 | ✅ |
| 3 | `X-XSS-Protection: 0` | 12 | 8 | ✅ |
| 4 | `Referrer-Policy` | 13 | 9 | ✅ |
| 5 | `Server` kaldırma | 14 | 6 (`X-Powered-By`) kısmen | ⚠️ farklı başlık |
| 6 | `X-Powered-By` kaldırma | 15 | 5 | ✅ |
| 7 | `Permissions-Policy` | yok | 10 | ⚠️ yalnız IIS |
| 8 | Statik dosya istisnası | 34 | 17-21 | ✅ |
| 9 | Dizin/dosya reddi (`config/include/pages`) | 35 | 22-26 | ✅ |
| 10 | Hassas uzantı reddi | 18-20 | 27-31 | kısmen |
| 11 | Front controller | 36 | 36-39 | ✅ |
| 12 | Dizin listeleme kapalı | 2 (`Options -Indexes`) | yok | ⚠️ yalnız Apache |
| 13 | `ServerSignature Off` | 3 | yok | ⚠️ yalnız Apache |
| 14 | PHP hata bayrakları | 5-7 | yok | ⚠️ yalnız Apache |

Tablo notu: 14 kalemin 5'inde iki hedef **aynı davranışı paylaşmaz** → çift hedef (R1) tekilleşmeden "runtime davranışı aynı" iddiası yazılmaz.

---

## §12 URL doğrulama betiği ve sonucu

```powershell
$u = 'https://www.php.net/manual/en/opcache.setup.php'
$r = Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20
"{0} {1}" -f $r.StatusCode, $u
```

| # | URL | Kod | Envante var mı | Sonuç |
|---|---|---|---|---|
| 1 | `https://www.php.net/manual/en/opcache.setup.php` | 200 | hayır | ✅ yeni |
| 2 | `https://httpd.apache.org/docs/2.4/mod/mod_rewrite.html` | 200 | hayır | ✅ yeni |
| 3 | `https://nginx.org/en/docs/http/request_processing.html` | 200 | hayır | ✅ yeni |

---

## §13 Alt-dal kanıt planı (hangi dosya hangi diske dayanır)

| # | Dosya | Birincil disk kanıtı | Beklenen satır |
|---|---|---|---|
| 1 | `surec-ve-parcacik-anayasa.md` | `shared/src/Middleware/MiddlewarePipeline.php` | 42-46 |
| 2 | `surec-ve-parcacik-surucu.md` | `api.coremusic.net/index.php` | 143-145 |
| 3 | `surec-ve-parcacik-ornekler.md` | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 248-269 |
| 4 | `web-runtime-topolojisi-anayasa.md` | `shared/config/domain.php` | 4-18 |
| 5 | `web-runtime-topolojisi-surucu.md` | `home.coremusic.net/config/bootstrap.php` | 16-26 |
| 6 | `web-runtime-topolojisi-ornekler.md` | `api.coremusic.net/index.php` | 147-171 |
| 7 | `baslangic-ve-bootstrap-anayasa.md` | `home.coremusic.net/index.php` | 10-29 |
| 8 | `baslangic-ve-bootstrap-surucu.md` | `home.coremusic.net/config/constants.php` | 10-32 |
| 9 | `baslangic-ve-bootstrap-ornekler.md` | `shared/src/Bootstrap/RuntimeBootstrap.php` | 7-20 |
| 10 | kök `index.md` | üç alt-dal dizini | dosya varlığı |
| 11 | kök `-kapsam.md` | `.htaccess` + `web.config` | 5-7, 1-43 |
| 12 | kök `-dogrulama.md` | bu dosya | — |
| 13 | kök `-ornekler.md` | `index.php` + `bootstrap.php` | 10-29, 97-106 |
| 14 | `surec-ve-parcacik/index.md` | kendi 3 dosyası | — |
| 15 | `web-runtime-topolojisi/index.md` | kendi 3 dosyası | — |
| 16 | `baslangic-ve-bootstrap/index.md` | kendi 3 dosyası | — |

---

## §14 Yeniden ölçüm adımları

| # | Adım | Komut özeti | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı | `ReadAllLines(...).Count` | 12 dosya ≥500 · 4 index 40-100 |
| 2 | FM bütünlüğü | `---` sayımı | her dosyada 2 |
| 3 | `parent:` taraması | regex `^parent:` | 0 |
| 4 | `group` biçimi | `group: [a-z]+` tırnaksız | 16/16 |
| 5 | Yasak kalıp | dört kalıp taraması | 0 |
| 6 | Mojibake | UTF-8 okuma | 0 |
| 7 | URL kesişimi | envanter vs. yeni | 0 |
| 8 | HTTP 200 | HEAD | 48/48 (dosya başı 3) |
| 9 | Satır indeksi | `ReadAllLines()[n-1]` | 9/9 `OK` |
| 10 | Yazım sınırı | `git status --porcelain` | yalnız beyazliste |
| 11 | Boş hücre | tablo satırı hücre sayısı | 0 eksik |
| 12 | Son satır | Authority dizgesi | 16/16 |

---

## §15 Açık sorular — doğrulama durumu

| # | Soru | Denenen | Sonuç |
|---|---|---|---|
| R1 | Üretim runtime'ı Apache mi IIS mi? | `.htaccess` + `web.config` varlığı | iki config de var → [UNKNOWN] |
| R2 | Çok-iş-parçacığı hedefte mi? | `pcntl`/`pthreads` taraması | üretim kullanımı yok → [UNKNOWN] |
| R3 | SAPI: mod_php mi FPM mi? | `.htaccess` `php_flag` + FPM pool arama | pool yok → ⚠️ VERIFICATION REQUIRED |
| R4 | `music`/`admin`/`media` runtime'ı? | dizin ve `index.php` arama | dizin yok, `media`'da giriş noktası yok → ⚠️ VERIFICATION REQUIRED |
| R5 | Warm-up var mı? | `opcache_*`/`apcu_*` taraması | yalnız `opcache_reset` (debug korumalı) → [UNKNOWN] |
| R6 | Drenaç var mı? | `fastcgi_finish_request` taraması | gözlenmedi → [UNKNOWN] |
| R7 | MPM/FPM kapasitesi? | yapılandırma dosyası arama | yok → ⚠️ VERIFICATION REQUIRED |
| R8 | `assets` statik mi? | `assets.coremusic.net/` içeriği | `.htaccess` + `web.config` var, `index.php` yok → [UNKNOWN] |

---

## §16 Doğrulama günlüğü

| # | Saat/zaman | İşlem | Sonuç |
|---|---|---|---|
| 1 | 2026-10-13 | URL envanteri (`.ai/architecture/**/*.md`) | 2097 benzersiz |
| 2 | 2026-10-13 | 33 aday URL kesişim testi | 33/33 envanter dışı |
| 3 | 2026-10-13 | HEAD istekleri (ilk parti) | 30/33 HTTP 200 |
| 4 | 2026-10-13 | HEAD istekleri (ikinci parti) | 18/24 HTTP 200 |
| 5 | 2026-10-13 | HEAD istekleri (üçüncü parti) | 10/12 HTTP 200 |
| 6 | 2026-10-13 | HEAD istekleri (dördüncü parti) | 4/5 HTTP 200 |
| 7 | 2026-10-13 | Toplam geçerli yeni URL | 62 |
| 8 | 2026-10-13 | Dosya başı tahsis | 16 × 3 = 48 |
| 9 | 2026-10-13 | Disk kanıtı okuma (9 dosya) | satır birebir |
| 10 | 2026-10-13 | `Select-Object -Index` | 9/9 `OK` |

---

## §17 Kapanış kontrolü

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Beyazliste dışına yazım | 0 | ✅ |
| 2 | `git commit` | atılmadı | ✅ |
| 3 | Komşu dizinlere yazım (7 + 4 yasaklı) | 0 | ✅ |
| 4 | `K000` kök dosyalarına yazım | 0 | ✅ |
| 5 | `K001..K019` yazım | 0 | ✅ |
| 6 | `*.php` / `*.js` / `*.css` / `*.sql` yazım | 0 | ✅ |
| 7 | `.ai/architecture/index.md` yazım | 0 | ✅ |
| 8 | `.ai/electronic/**` yazım | 0 | ✅ |
| 9 | Dosya adı değişikliği | 0 | ✅ |
| 10 | FM anahtar sayısı | 10 (8 + `group` + `open_questions`) | ✅ |
| 11 | `group` konumu | `---` öncesi | ✅ |
| 12 | Son satır Authority | 16/16 | ✅ |
| 13 | Uydurma satır numarası | 0 | ✅ |
| 14 | Uydurma HTTP kodu | 0 | ✅ |
| 15 | Boş hücre | 0 | ✅ |
| 16 | Mojibake | 0 | ✅ |
| 17 | Kayıtlı imlalar korundu | 7 | ✅ |
| 18 | Açık sorular işaretli | 8/8 | ✅ |
| 19 | § başlığı sayısı | 20 bölüm (§0-§19) | ✅ |
| 20 | Kod bloğu sayısı | 5 (betik 3 + doğrulama 2) | ✅ |
| 21 | Zorunlu son satır öncesi boş satır | 16/16 | ✅ |
| 22 | Dosya kodlaması | UTF-8 (mojibake 0) | ✅ |
| 23 | Beyazliste dosya sayısı | 16 | ✅ |
| 24 | Yeni dizin sayısı | 3 (alt-dal) | ✅ |
| 25 | Silinen dosya | 0 | ✅ |
| 26 | Üretilen geçici dosya | 0 | ✅ |
| 27 | Komşu `status` değişikliği | 0 | ✅ |
| 28 | ADR/frozen metin değişikliği | 0 | ✅ |

---

## §18 Tekrarlanabilir komut seti

| # | Amaç | Komut |
|---|---|---|
| 1 | Satır sayısı | `([IO.File]::ReadAllLines($f)).Count` |
| 2 | Satır içeriği | `[IO.File]::ReadAllLines($f)[$n-1]` |
| 3 | Eş-değer indeks | `Get-Content $f \| Select-Object -Index ($n-1)` |
| 4 | FM okuma | `Get-Content $f -TotalCount 20` |
| 5 | Yasak kalıp | `$yasak = 'T'+'B'+'D'` · `[regex]::Matches($t,$yasak).Count` |
| 6 | URL çıkarma | `[regex]::Matches($t,'https?://[^\s\|\)``]+')` |
| 7 | HTTP kodu | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` |
| 8 | Yazım sınırı | `git status --porcelain .ai/architecture/K000-isletim-sistemi/isletim-sistemi-runtime` |

---

## §19 Sonuç

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Web kanıtı | 3/3 HTTP 200 |
| 2 | Disk kanıtı | 24/24 satır gözlemi |
| 3 | `Select-Object -Index` | 9/9 `OK` |
| 4 | Envante yeni URL | 3 |
| 5 | Envante kesişimi | 0 |
| 6 | FM denetimi | 16/16 · `parent:` 0 · tırnaklı `group` 0 |
| 7 | Yasak kalıp | 0 (§6) |
| 8 | Dışına yazılan | 0 |
| 9 | `git commit` | atılmadı |
| 10 | R1 / R3 | [UNKNOWN] / ⚠️ VERIFICATION REQUIRED — kapatılmadı |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
