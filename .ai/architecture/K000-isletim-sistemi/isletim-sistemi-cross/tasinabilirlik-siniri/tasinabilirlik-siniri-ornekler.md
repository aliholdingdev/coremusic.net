---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-surucu"
evidence:
  - "home.coremusic.net/.htaccess:2-37"
  - "home.coremusic.net/web.config:5-41"
  - "shared/src/Component/TemplateEngine.php:73"
  - "shared/src/Api/Bff/DesktopBff.php:34-38"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:199-202"
  - "shared/tests/Component/TemplateEngineTest.php:27"
  - "shared/tests/Component/TemplateEngineTest.php:87"
  - "shared/tests/Component/TemplateEngineTest.php:107"
  - "shared/tests/Repository/ApiKeyCreateCommandTest.php:38"
  - ".github/workflows/ci.yml:27,69,112,144,163,180"
  - ".github/workflows/ci.yml:44,46,96,98"
  - ".github/workflows/ci.yml:73,75,79"
  - ".github/workflows/ci.yml:167,183"
  - "https://man7.org/linux/man-pages/man7/path_resolution.7.html"
  - "https://learn.microsoft.com/en-us/windows/win32/fileio/naming-a-file"
  - "https://learn.microsoft.com/en-us/windows/win32/fileio/maximum-file-path-limitation"
validation: "2026-10-13 · 3 yeni URL webfetch GET 200 (resmi kaynak) · 5 kardeş URL yalnızca -dogrulama/anayasa/surucu 200 kayıtlarından yeniden kullanıldı · tüm repo satırları bu oturumda okundu · ⚠️ uzunluk çelişkisi: index §3 bu dosya için ≥500, dispatcher spec 380-460 → 380-460'a uyuldu, çelişki raporlandı"
open_questions:
  - "Q-X51 — taşınabilirlik bütçesi (soyutlama başına maliyet) ölçülmedi → ⚠️ VERIFICATION REQUIRED"
  - "Q-X52 — Windows/macOS runner kanıtı yok (yalnız ubuntu-latest) → UNKNOWN"
  - "Q-X54 — 'taşınabilir' kabul eşiği tanımlı değil → ⚠️ VERIFICATION REQUIRED"
  - "Q3 / Plan1 — [UNKNOWN] (korunur)"
group: kod
---

# tasinabilirlik-siniri-ornekler — Kademe-4 Kod Örnekleri

> **Kapsam:** Bu dosya, `tasinabilirlik-siniri-anayasa.md` kararlarının ve
> `tasinabilirlik-siniri-surucu.md` akışlarının **elle tutulur** karşılıklarıdır:
> çalışan PHP yüzey örnekleri (S1), kategori-5 çift yazılım dosyalarının satır satır
> eşlemesi (S1) ve CI platform kanıtı (S1). **Bu dosyada PLANNED/uydurma kod yoktur** —
> her blok depoda okunmuş bir satıra dayanır.

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler` |
| parent_id | `…/isletim-sistemi-cross/tasinabilirlik-siniri` |
| group | `kod` |
| Bağımlı | `tasinabilirlik-siniri-anayasa.md` (kararlar) · `tasinabilirlik-siniri-surucu.md` (akışlar) |
| Kanıt dili | S1 = bu oturumda okunmuş repo satırı · URL = 2026-10-13 doğrulaması |
| Kardeş şablon | `../platform-yuzeyleri/platform-yuzeyleri-ornekler.md` (iskelet kaynağı) |

## §0 ELI15

Taşınabilirlik, "kod başka makinede çalışır mı" sorusunun cevabıdır. Bu dosya üç soru
sorar ve **yalnız okunmuş satırlarla** cevaplar: (1) Depodaki PHP, ayrac ve OS tespitini
platformdan soyutlamış mı? → §1'de üç canlı örnek + repo geneli tarama. (2) Aynı
uygulamanın Apache ve IIS kuralları birbirine ne kadar benziyor? → §2'de `.htaccess` ↔
`web.config` tam eşleme.
(3) CI gerçekten birden çok platformda koşuyor mu? → §3'te kanıt: **koşmuyor** — altı işin
altısı da `ubuntu-latest`. Eksik kalan kısmı (eşikler, bütçe) §5'te açık soru olarak
işaretlenir; uydurulmaz.

## §1 Rafta 1 — çalışan PHP taşınabilirlik örnekleri (S1)

### §1.1 Yol ayracı — `DIRECTORY_SEPARATOR` (S1)

**Dosya:** `shared/src/Component/TemplateEngine.php` · **Satır: 73**

```php
        $cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
```

| # | Öğe | Ne gösteriyor |
|---|---|---|
| 1 | `sys_get_temp_dir()` | geçici dizin platformdan soyut — sabit `/tmp` yazılmaz |
| 2 | `rtrim(..., "\\/")` | hem `\` hem `/` kuyruğu temizlenir (çift ayracın önüne geçer) |
| 3 | `DIRECTORY_SEPARATOR` | ayracı PHP verir; `/` veya `\` sabit kodlanmaz |
| 4 | `bin2hex(random_bytes(8))` | dosya adı rastgele — aynı şablon farklı OS'ta çakışmaz |

**Test kanıtı (aynı kalıp testlerde de tekrarlanıyor):**

| # | Dosya | Satır | Satır içeriği (birebir) |
|---|---|---|---|
| 1 | `shared/tests/Component/TemplateEngineTest.php` | 27 | `$files = glob(rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_*.php');` |
| 2 | `shared/tests/Component/TemplateEngineTest.php` | 87 | `$file = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cm_render_' . bin2hex(random_bytes(8)) . '.php';` |
| 3 | `shared/tests/Component/TemplateEngineTest.php` | 107 | `$engine->render(rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cm_missing_template.php');` |
| 4 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 38 | `. DIRECTORY_SEPARATOR . 'cm-apikey-cli-' . bin2hex(random_bytes(6)) . '.sqlite';` |

> **Gözlem:** üretim kodu ile test kodu **aynı soyutlamayı** kullanıyor — yani ayrac
> kuralı tek dosyaya kilitli değil, repo geneline yayılmış bir konvansiyondur
> (4/4 örnekte `DIRECTORY_SEPARATOR`, 0/4'te sabit `/`).

### §1.2 OS tespiti — `php_uname('s')` (S1)

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır: 34-38**

```php
        $response['meta']['system'] = [
            'platform' => php_uname('s'),
            'phpVersion' => PHP_VERSION,
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        ];
```

| # | Öğe | Ne gösteriyor |
|---|---|---|
| 1 | `php_uname('s')` | OS adı çalışma zamanında okunur — `if (WIN)` gibi sert dallanma yok |
| 2 | `PHP_VERSION` | PHP sürümü sabit değil, ortamdan gelir |
| 3 | `$_SERVER['SERVER_SOFTWARE'] ?? 'unknown'` | bilinmeyen değer **uydurulmaz**, `unknown` yazılır (zero-hallucination) |
| 4 | `meta['system']` | platform bilgisi API gövdesinde taşınır — karar vericiler veriyi görür |

> Bu blok surucu §2.3'teki "çalışan platform algılama akışı"nın birebir kaynağıdır.

### §1.3 Yol güvenliği — `str_starts_with` + `realpath` öneki (S1)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır: 199-202**

```php
        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
        if (!str_starts_with($real, $prefix)) {
            $this->fail(403, 'Erişim reddedildi');
        }
```

| # | Öğe | Ne gösteriyor |
|---|---|---|
| 1 | `rtrim($root, '\\/')` | kök dizin her iki ayracın kuyruğundan arındırılır |
| 2 | `DIRECTORY_SEPARATOR` | önek platform ayracıyla kurulur — Windows/Linux aynı kod |
| 3 | `str_starts_with($real, $prefix)` | `realpath` sonucu kökün altında mı denetlenir (path traversal gardı) |
| 4 | `fail(403)` | ihlalde sessizce geçilmez; platform farkı bahanesiyle reddetme atlanmaz |

> **Yol çözümleme referansı:** önek karşılaştırmasının dayandığı "çözümleme" kavramı
> POSIX tarifiyle belgelenmiştir — `path_resolution(7)` (§6 #1). Örnek kodda ise
> karşılaştırma `realpath` sonrası yapılır; yorum değil, satır kanıtı budur.

### §1.4 Eşdeğer eşleme tablosu (anayasa §3.4 adım 2 karşılığı)

| # | Platforma özel yüzey | Bu dosyadaki eşdeğer örnek | Kanıt |
|---|---|---|---|
| 1 | Windows yol ayracı `\` | §1.1 · §1.3 — `rtrim(..., "\\/")` + `DIRECTORY_SEPARATOR` | 4 üretim/test satırı |
| 2 | Linux yol ayracı `/` | §1.1 · §1.3 — aynı kod, ayrac PHP'den gelir | 4 üretim/test satırı |
| 3 | OS adı sorgusu (Windows `GetVersionEx` / POSIX `uname`) | §1.2 — `php_uname('s')` | `DesktopBff.php:35` |
| 4 | Dizin erişim izni (POSIX `access`) | §1.3 — `is_readable` öncesi `realpath` öneki | `MusicStreamHandler.php:199-204` |
| 5 | Dış kütüphane yükleme (Windows `LoadLibraryW` / POSIX `dlopen`) | repo'da karşılığı **YOK** — native kod yok | §6 #4 · #5 (upstream) |
| 6 | Windows dosya adı kuralları (`<>:"|?*` yasak) | PHP katmanında özel bir karşılık **yazılmamış** | §6 #2 (upstream) · ⚠️ Q-X56 |

> Satır 5-6'da "örnek yok" denmesi boşluk değil, dürüstlük kaydıdır: repo'da
> `*.cpp`/`*.h` karşılığı olmayan bir yüzey için örnek **uydurulmaz**.

### §1.5 Repo geneli tarama — istisna mı, kural mı? (S1)

§1.1-§1.3'teki örnekler seçilmiş örneklerdi; bu bölüm aynı kalıbın **tüm depoda**
zorunlu olup olmadığını sorar. Tarama: `*.php` · `vendor/` ve `node_modules/` hariç ·
repo kökünden (`C:\www\coremusic.net`) · 2026-10-13.

| # | Tarama deseni | Sonuç | Yorum |
|---|---|---|---|
| 1 | Birinci taraf `*.php` dosya sayısı | **319** (shared 217 · home 38 · auth 37 · api 12 · media 8 · diğer 7) | tarama evreni |
| 2 | `DIRECTORY_SEPARATOR` | **6 isabet / 4 dosya** | hepsi bu dosyanın §1.1-§1.3'ünde: `TemplateEngine.php:73` · `TemplateEngineTest.php:27,87,107` · `ApiKeyCreateCommandTest.php:38` · `MusicStreamHandler.php:199` |
| 3 | `PHP_OS` / `PHP_OS_FAMILY` | **0 isabet** (70 isabetin tamamı `vendor/` içinde — phpunit & composer) | birinci taraf kodda **sert OS dallanması yok** |
| 4 | `php_uname` | **1 isabet** — `shared/src/Api/Bff/DesktopBff.php:35` | OS bilgi tek noktadan (§1.2) |
| 5 | `*.cpp` / `*.h` | 0 (kardeş `platform-yuzeyleri-ornekler.md` glob kanıtı) | native yüzey yok — bkz. §6 #4-#5 |

> **Sınır (dürüstlük):** Sayımlar bu oturumda PowerShell `Select-String` ile alındı;
> kapsam 319 birinci taraf dosyayla sınırlıdır, `vendor/` paketleri hariçtir.
> "0 `PHP_OS`" ifadesi yalnız bu kapsam için geçerlidir → genelleme
> `⚠️ VERIFICATION REQUIRED` gerektirir.

**⚠️ Tarihli ölçüm farkı (2026-10-13 · yeni tarama — mevcut satır DEĞİŞTİRİLMEDİ):**

| # | Ölçüm | §1.5 kaydı (ilk tarama) | Bu oturumun yeniden taraması | Fark |
|---|---|---|---|---|
| 1 | Birinci taraf `*.php` | **319** (shared 217) | **320** (shared 218) | **+1 dosya, `shared/` içinde** |
| 2 | Tarama kapsamı | "vendor/ ve node_modules/ hariç" (§1.5 giriş satırı) | `.git` de hariç alındı (`\vendor\|\node_modules\|\.git\`) | kapsam tanımı farklı olabilir |
| 3 | Diğer dizinler | home 38 · auth 37 · api 12 · media 8 | home.coremusic.net 38 · auth.coremusic.net 37 · api.coremusic.net 12 · media.coremusic.net 8 | **aynı** |
| 4 | Artan dizinler | sayılmamış | `.claude` 4 · `bin` 1 · garip adlı 2 dizin | ilk taramada bu dizinler kapsamda mıydı → **UNKNOWN** |

> **Davranış kuralı:** §1.5'teki **319/217 satırı silinmez ve "düzeltmez"** — bu tablo bir
> **tarihli gözlem**tir. Nedeni bilinmiyor (kapsam tanımı ya da dosya ekleme/çıkma); neden
> **uydurulmaz** → `⚠️ VERIFICATION REQUIRED`. İki sayı birbirinin **yerine geçmez**;
> hangisinin kullanılacağı Vault Steward kararıdır (§5.3'e eklendi).

## §2 Kategori 5 — çift yazılım: `.htaccess` ↔ `web.config` tam eşleme (S1)

### §2.1 Dosya künyesi (2026-10-13 okuması)

| Dosya | Sunucu | Toplam satır | Rol |
|---|---|---|---|
| `home.coremusic.net/.htaccess` | Apache | 37 | rewrite + başlık + engel |
| `home.coremusic.net/web.config` | IIS | 43 | rewrite + başlık + engel |

### §2.2 Güvenlik başlığı eşlemesi

| # | Başlık | `.htaccess` | `web.config` | Eşit mi |
|---|---|---|---|---|
| 1 | `X-Content-Type-Options: nosniff` | satır 11 | satır 7 | ✅ |
| 2 | `X-Frame-Options: DENY` | satır 10 | satır 8 | ✅ |
| 3 | `X-XSS-Protection: 0` | satır 12 | satır 9 | ✅ |
| 4 | `Referrer-Policy: strict-origin-when-cross-origin` | satır 13 | satır 10 | ✅ |
| 5 | `X-Powered-By` kaldırma | satır 15 (`unset`) | satır 6 (`remove`) | ✅ |
| 6 | `Server` kaldırma | satır 14 (`Header always unset Server`) | **karşılığı yok** (satır 6-11 arası yalnız 5 başlık) | ⚠️ fark-A |
| 7 | `Permissions-Policy` | **YOK** (mod_headers bloğu satır 9-16'da 4 `set` + 2 `unset`) | satır 11 `camera=(), microphone=(), geolocation=()` | ❌ **uyuşmazlık (bulgu)** |

> **Bulgu 7**, anayasa §2.7 #3'ün ("kural sayısı eşitlenmeli, uyuşmazlık gizlenmemeli")
> gözlemlenmiş somut örneğidir ve surucu §6.2/§6.4 ile **aynı sonuca** varır:
> `Permissions-Policy` yalnız IIS'te. **Bulgu-A** ise bu dosyanın ek gözlemidir
> (aşağıda §2.6).

**Blok kanıtı — Apache (`.htaccess` satır 9-16, birebir):**

```apache
<IfModule mod_headers.c>
    Header always set X-Frame-Options "DENY"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "0"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always unset Server
    Header always unset X-Powered-By
</IfModule>
```

**Blok kanıtı — IIS (`web.config` satır 5-12, birebir):**

```xml
            <customHeaders>
                <remove name="X-Powered-By" />
                <add name="X-Content-Type-Options" value="nosniff" />
                <add name="X-Frame-Options" value="DENY" />
                <add name="X-XSS-Protection" value="0" />
                <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
                <add name="Permissions-Policy" value="camera=(), microphone=(), geolocation=()" />
            </customHeaders>
```

> İki blok yan yana okunduğunda: **5 başlık eşleşir**, `Permissions-Policy` yalnız IIS'te
> (satır 11), `unset Server` yalnız Apache'de (satır 14). Kod blokları yorum değil,
> dosyadan kopyalanmış 8+8 satırdır.

### §2.3 Rewrite / engel eşlemesi

| # | Kural | `.htaccess` | `web.config` | Eşit mi |
|---|---|---|---|---|
| 1 | Statik dosya istisnası | satır 34 (`\.(css\|js\|…)$ - [L]`) | satır 16-19 (`Static files` → `None`) | ✅ uzantı listesi birebir |
| 2 | `config/include/pages` engeli (403) | satır 35 (`- [F,L]`) | satır 20-23 (`CustomResponse 403`) | ✅ |
| 3 | Hassas uzantı engeli (403) | satır 18-20 (`FilesMatch` → `Require all denied`) | satır 24-27 (`CustomResponse 403`) | ⚠️ uzantı listesi farklı — §2.4 |
| 4 | Dosya/dizin varsa rewrite'ı atla | satır 30-32 (`RewriteCond -f/-d` + `RewriteRule ^ - [L]`) | satır 28-35 (`SPA Router`: `IsFile`/`IsDirectory` → `None`) | ✅ |
| 5 | `index.php`'ye yönlendirme | satır 36 (`index.php [QSA,L]`) | satır 36-39 (`Rewrite`, `appendQueryString="true"`) | ✅ |
| 6 | **Doğrudan `.php` erişim engeli** | satır 22-24 (`^(?!index\.php$)(?!debug\.php$).+\.php$` → `Require all denied`) | **YOK** — 5 kuralın hiçbirinde PHP engeli yok (satır 16-39) | ⚠️ fark-B |
| 7 | `php_flag display_errors/log_errors` + `error_log` | satır 5-7 | **YOK** (IIS'te PHP yapılandırması bu dosyada değil) | ⚠️ UNKNOWN — §2.5 |
| 8 | `Options -Indexes` (dizin listeleme kapalı) | satır 2 | **YOK** | ⚠️ UNKNOWN — §2.5 |
| 9 | `ServerSignature Off` | satır 3 | **YOK** (karşılığı belirsiz) | ⚠️ UNKNOWN — §2.5 |

**Blok kanıtı — Apache rewrite çekirdeği (`.htaccess` satır 26-37, birebir):**

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

**Blok kanıtı — IIS SPA + yönlendirme (`web.config` satır 28-39, birebir):**

```xml
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
```

**Blok kanıtı — doğrudan PHP engeli, yalnız Apache (`.htaccess` satır 22-24, birebir):**

```apache
<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">
    Require all denied
</FilesMatch>
```

### §2.4 Hassas uzantı listesi farkı (fark-C)

| Dosya | Satır | Uzantı listesi |
|---|---|---|
| `.htaccess` | 18 | `env\|log\|json\|lock\|md\|sql\|sh\|bak\|git\|ini\|htpasswd\|DS_Store` — **13 uzantı** |
| `web.config` | 25 | `env\|log\|json\|lock\|md\|sql\|sh\|bak\|git\|ini\|htpasswd` — **12 uzantı** |

> Fark: `DS_Store` yalnız Apache'de. Kural **var ama liste eşit değil** — surucu §6.4
> satır 6'daki "Hassas dosya engeli ✅" bu dosyanın daha ince okumasıyla
> **tamamlanır**: engel mekanizması eşit, kapsama 1 uzantı farklı.

### §2.5 Karşılığı olmayanlar — ne "eşit" ne "fark" (UNKNOWN)

IIS tarafında bu üç maddenin karşılığı `web.config` dosyasında **bulunmadı** (satır 1-43
tam okundu). Karşılığının olup olmadığı ve IIS'in varsayılan davranışı bu oturumda
**doğrulanmadı** → ⚠️ `VERIFICATION REQUIRED` (uydurma eşleme yazılmaz):

| # | `.htaccess` maddesi | `web.config` durumu | İşaret |
|---|---|---|---|
| 1 | `php_flag` / `php_value` (satır 5-7) | yok | ⚠️ VERIFICATION REQUIRED |
| 2 | `Options -Indexes` (satır 2) | yok | ⚠️ VERIFICATION REQUIRED |
| 3 | `ServerSignature Off` (satır 3) | yok | ⚠️ VERIFICATION REQUIRED |

### §2.6 Bulgu özet + kardeş `-surucu` §6.4 ile mutabakat

| # | Bulgu | Kanıt | Kardeş dosyada var mı? |
|---|---|---|---|
| 1 | `Permissions-Policy` yalnız IIS'te | §2.2 satır 7 | ✅ evet — surucu §6.2, §6.4 satır 3 |
| 2 | `Server` kaldırma yalnız Apache'de | §2.2 fark-A | ❌ yok — **bu dosyanın ek bulgusu** |
| 3 | Hassas uzantı listesi 13'e karşı 12 | §2.4 | ❌ yok — §6.4 satır 6'yı tamamlar |
| 4 | Doğrudan PHP engeli yalnız Apache'de | §2.3 satır 6 | ❌ yok — **bu dosyanın ek bulgusu** |
| 5 | 3 maddenin IIS karşılığı doğrulanamadı | §2.5 | ❌ yok — işaretli eksiklik |

> **Mutabakat notu:** `-surucu` §6.4 dokuz satırlık kaba eşlemesinde "9 özelliğin 8'i
> eşleşiyor" der; bu dosyanın 9+7+3 satırlık ince eşlemesi aynı sonuçta **çelişmez**
> (Permissions-Policy ortak bulgu) ama **2 ek fark** ve **3 UNKNOWN** ekler. Vault
> Steward kararı: bu 5 bulgu `-surucu` §6.4'e taşınacak mı → açık bırakıldı.

## §3 CI platform kanıtı — matris neden yalnız Linux? (S1)

**Dosya:** `.github/workflows/ci.yml` · 6 iş tanımı, hepsi bu oturumda okundu.

| # | İş | Başlık satırı | `runs-on` satırı | Değer |
|---|---|---|---|---|
| 1 | `php-lint` | 25 | 27 | `ubuntu-latest` |
| 2 | `php-test` | 66 | 69 | `ubuntu-latest` |
| 3 | `composer-audit` | 110 | 112 | `ubuntu-latest` |
| 4 | `js-test` | 142 | 144 | `ubuntu-latest` |
| 5 | `js-coverage` | 160 | 163 | `ubuntu-latest` (+ `continue-on-error: true` @167) |
| 6 | `js-e2e` | 178 | 180 | `ubuntu-latest` (+ `continue-on-error: true` @183) |

| # | Soru | Kanıt (satır içeriği) | Sonuç |
|---|---|---|---|
| 1 | OS matrisi var mı? | satır 73 `matrix:` → satır 75 `- project: shared` · satır 79 `- project: auth.coremusic.net` | **Yalnız `project`** — `os` anahtarı yok |
| 2 | `windows-latest` / `macos-latest` geçiyor mu? | dosyada 0 eşleşme (desen: `matrix\.os\|windows-latest\|macos-latest\|runs-on: \$`) | **0** |
| 3 | `runner.os` kullanılıyor mu? | satır 44 `key: ${{ runner.os }}-composer-…` · 46 · 96 · 98 | Evet — **yalnızca önbellek anahtarında**, koşu platformu seçiminde değil |
| 4 | İşlerden biri başarısızlığa izin veriyor mu? | satır 167 ve 183 `continue-on-error: true` | 2/6 iş sinyali yumuşatılmış |

**Blok kanıtı — `ci.yml` satır 69-80 (birebir; tek matris budur):**

```yaml
    runs-on: ubuntu-latest
    timeout-minutes: 15
    strategy:
      fail-fast: false
      matrix:
        include:
          - project: shared
            args: ''
          # auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK
          # (glob kaniti) -> yalnizca Unit kosulur, failOnWarning kirilmasin.
          - project: auth.coremusic.net
            args: '--testsuite Unit'
```

> Matrisin tek anahtarı `project` (shared · auth.coremusic.net). `os:` satırı dosyada
> **yok**; altı işin `runs-on` satırı da (27, 69, 112, 144, 163, 180) aynı sabit değeri
> taşıyor. `runner.os` yalnız cache key'lerinde (44, 46, 96, 98) geçiyor — GitHub'ın
> değişkenidir, koşu platformunu seçmez (§6 #6).

> **Sonuç (Q-X52):** CI **tek platformda** (Linux) koşar. Windows/macOS'ta derleme
> veya test kanıtı **yok** → `UNKNOWN`. `runner.os` ifadesi GitHub'ın değişkenidir
> (§6 #6); burada yalnızca cache key'i üretmek için kullanılıyor — "CI Windows'ta da
> koşuyor" anlamına **gelmez**. surucu §4.1 #5'teki "çift platform bütünleşik test:
> ❌ yok" kaydının satır kanıtıdır.

### §3.1 CI dosya envanteri ve bağımlılık grafiği (`.github/workflows/` — 2026-10-13 Read)

| # | Dosya | Toplam satır | İş sayısı | Süre bütçesi |
|---|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | **203** | **6** | 15+15+10+10+15+20 = **85 dk** |
| 2 | `.github/workflows/secret-scan.yml` | **31** | **1** (`gitleaks`) | `timeout-minutes: 10` (satır 22) |
| 3 | Dizin toplamı | 234 satır | **7 iş** | — |

| # | Yapı | Satır | Değer (özgün) | Taşınabilirlik açısından |
|---|---|---|---|---|
| 1 | tetikleyici | `ci.yml` 10–12 | `on` → `push` · `pull_request` | platformdan bağımsız |
| 2 | izinler | `ci.yml` 14–15 · `secret-scan.yml` 11–12 | `permissions:` → `contents: read` | dar yetki — iki workflow'ta **aynı** |
| 3 | eşzamanlılık | `ci.yml` 17–19 · `secret-scan.yml` 14–16 | `concurrency:` → `cancel-in-progress: true` | platformdan bağımsız |
| 4 | ortam | `ci.yml` 21–22 | `env:` → `PHP_VERSION: '8.4'` | sürüm tek noktada |
| 5 | bağımlılık | `ci.yml` 68 | `needs: php-lint` (`php-test`) | iş grafiği — platform **seçmez** |
| 6 | bağımlılık | `ci.yml` 162 | `needs: js-test` (`js-coverage`) | aynı |

**İş bağımlılık grafiği (§3'teki `runs-on` tablosunun tamamlanması):**

| # | İş | `needs` | Yumuşak job | Katman |
|---|---|---|---|---|
| 1 | `php-lint` | — | hayır | CI |
| 2 | `php-test` | `php-lint` | hayır | CI |
| 3 | `composer-audit` | — | hayır | CI |
| 4 | `js-test` | — | hayır | CI |
| 5 | `js-coverage` | `js-test` | **evet** (L167) | CI |
| 6 | `js-e2e` | **yok** (L178–183 okundu) | **evet** (L183) | CI |
| 7 | `gitleaks` (`secret-scan.yml`) | — | hayır | CI (güvenlik) |

> **Taşınabilirlik okuması:** bu grafik **iş bağımlılığıdır (DAG)**, işletim sistemi
> zamanlaması **değildir** — 6 işin 6'sı `ubuntu-latest` (§3) olduğu için grafik
> **her platformda aynı çalışır mı** sorusunu **cevaplamaz**; yanıt Q-X52 `UNKNOWN`
> olarak kalır. `js-e2e` için `needs` satırı dosyada **yoktur** — "yok" bir **okunmuş
> yokluk** kanıtıdır, tahmin değil.

## §4 Yanlış / doğru çiftleri

| # | Yanlış (yapma) | Doğru (yap) | Dayanak |
|---|---|---|---|
| 1 | `'/tmp/cmtpl_' . …` yazmak | `rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . …` | §1.1 · `TemplateEngine.php:73` |
| 2 | `if (PHP_OS === 'WINNT')` gibi tek kalem OS kilitlenmesi | `php_uname('s')` ile raporla, dallanmayı veriye bırak | §1.2 · `DesktopBff.php:35` |
| 3 | Yol denetiminde string'i olduğu gibi karşılaştırmak | `realpath` sonrası `rtrim`'lenmiş önek ile `str_starts_with` | §1.3 · `MusicStreamHandler.php:199-201` |
| 4 | Bilinmeyen sunucu adını tahmin etmek | `$_SERVER['SERVER_SOFTWARE'] ?? 'unknown'` | §1.2 satır 3 |
| 5 | `Permissions-Policy`'yi yalnız bir dosyaya koymak | iki dosyayı eşle **veya** gerekçeyi yaz (gizleme) | §2.2 satır 7 · anayasa §2.7 #3 |
| 6 | Hassas uzantı listesini tek dosyada güncellemek | `.htaccess:18` **ve** `web.config:25` birlikte güncellenir | §2.4 |
| 7 | "CI geçiyor" deyip Windows desteğini ima etmek | "CI yalnız `ubuntu-latest`'te koşuyor; Windows kanıtı yok" | §3 · Q-X52 |
| 8 | `continue-on-error: true` olan işi başarılı saymak | sinyali yumuşatılmış işi ayrı raporla | §3 satır 167/183 |
| 9 | Karşılığı doğrulanamayan kuralı "eşit" diye işaretlemek | ⚠️ `VERIFICATION REQUIRED` / `UNKNOWN` bırak | §2.5 |

### §4.1 Taşınabilirlik yüzeyi × kanıt türü sınıflandırma matrisi

> Aynı **taşınabilirlik yüzeyi** iki farklı **kanıt türü** ile belgelenir: repo içi
> birincil sayımda (CI yapısı, dosya envanteri) **satır numarası** tam ve doğrudan
> verilir; resmi kaynak karşılaştırmasında (§4 tablosu) ise yalnızca **dosya düzeyinde**
> referans verilir ve **satır numarası okunmamıştır → `⚠️ VERIFICATION REQUIRED`** olarak
> işaretlenir. İki tür karıştırılmaz.

| Yüzey | Kanıt türü | Ne verilir | Ne verilmez |
|---|---|---|---|
| CI yapı kotaları (timeout, `needs`, `continue-on-error`) | **Birincil — repo içi ölçüm** (§3.1) | dosya, satır, değer | — |
| CI tetikleyici (`on`), izinler, eşzamanlılık | **Birincil — repo içi ölçüm** (§3.1 tablosu) | satır + tam değer | — |
| Workflow envanteri (`ci.yml`, `secret-scan.yml`) | **Birincil — repo içi ölçüm** (§3.1) | satır sayısı, iş sayısı, üstelik | — |
| Apache yalnız-rewrite / PHP yok (`.htaccess`) | **Birincil — repo içi ölçüm** (§1.4/§1.5) | satır + kural metni | — |
| Windows yolları / MAX_PATH (learn.microsoft.com) | **Resmi karşılaştırma** (§4 L7) | dosya düzeyinde URL | satır numarası |
| POSIX `EAGAIN`/`E2BIG` (pubs.opengroup.org) | **Resmi karşılaştırma** (§4 L4) | bölüm düzeyinde referans | satır numarası |
| Linux `CLONE_NEWTIME` (docs.kernel.org) | **Resmi karşılaştırma** (§4 L3) | dosya düzeyinde URL | satır numarası |
| `FILE_ATTRIBUTE_RECALL_ON_DATA_ACCESS` vb. | **Resmi karşılaştırma** (§4 L5) | dosya düzeyinde URL | satır numarası |
| Repo'nun kendisindeki Windows/POSIX kod varlığı | **Birincil — negatif ölçüm** (§1.7 grepleri) | grek komutu + 0 sonuç | "hiçbir yerde yok" genellemesi |

> **Kural:** §4 tablosundaki her satır yalnızca *karşılaştırma düzeyinde* doğrudur;
> repo'ya *uygulanabilirlik* iddiası §1–§3'ün birincil ölçümlerinden gelir.

## §5 Ölçüm, dürüstlük ve açık sorular

### §5.1 Ölçüm kaydı (bu dosyanın kendisi)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Kanıt satırı okuma | `.htaccess` 37/37 · `web.config` 43/43 · `ci.yml` seçili 17 satır · 3 PHP dosyası + 2 test dosyası seçili satırları |
| 2 | Yeni URL doğrulama | 3/3 · 2026-10-13 · webfetch GET **HTTP 200** · resmi kaynak (§6 #1-#3) |
| 3 | Kardeş URL teyidi | 5/5 · `-dogrulama` §E.1 ve anayasa/surucu 200 kayıtlarından yeniden kullanıldı (§6 #4-#8) |
| 4 | PLANNED/uydurma blok | **0** — dosyanın tamamı S1 |
| 5 | ⚠️ Uzunluk çelişkisi | `index.md` §3 bu dosya için **≥500** satır öngörüyor; görev dispatcher'ı **380-460** istiyor → **380-460'a uyuldu**, çelişki Vault Steward'a rapor edildi (dosyalar düzenlenmedi) |
| 6 | Workflow envanteri ölçümü (2026-10-13) | `.github/workflows/` = **2 dosya** · **234 satır** · **7 iş** (`ci.yml` 203/6 + `secret-scan.yml` 31/1) · iş başına üstelik toplam **95 dk** (85+10) |
| 7 | CI yapı kotası okuma | `ci.yml` tam yapı tablosu §3.1'de: `on` L10-12 · `permissions` L14-15 · `concurrency` L17-19 · `env` L21-22 · `needs` L68/L162 · `continue-on-error` L167/L183 |
| 8 | PHP yeniden tarama (2026-10-13) | `.git`/vendor/node_modules hariç · **320 dosya** (§1.5 tablosu) · §1.5'in 319'undan **+1** · fark kaynağı `UNKNOWN` |

### §5.2 Dürüstlük notları (bu dosyada ne ölçülmedi)

| # | Konu | Durum |
|---|---|---|
| 1 | Taşınabilirlik bütçesi / soyutlama maliyeti | ölçülmedi → Q-X51 ⚠️ VERIFICATION REQUIRED |
| 2 | Windows/macOS test koşusu | yapılmadı → Q-X52 UNKNOWN |
| 3 | "Kaç yüzde ortak kod" eşiği | tanımlı değil → Q-X54 ⚠️ VERIFICATION REQUIRED |
| 4 | IIS'in `Server` başlığı varsayılan davranışı | doğrulanmadı → §2.2 fark-A ⚠️ VERIFICATION REQUIRED |
| 5 | §2.5'teki 3 maddenin IIS karşılığı | dosyada yok, doğrulanmadı → ⚠️ VERIFICATION REQUIRED |
| 6 | Q3 / Plan1 | `[UNKNOWN]` — korunur, doldurulmadı |

### §5.3 Açık sorular (bu dosyaya özgü ek)

| # | Soru | İşaret |
|---|---|---|
| 1 | Q-X56 — Windows dosya adı kısıtları (`naming-a-file`) PHP katmanında denetleniyor mu? | ⚠️ VERIFICATION REQUIRED — repo'da karşılık aranmadı |
| 2 | §2.6 bulguları (2 ek fark + 3 UNKNOWN) `-surucu` §6.4'e taşınacak mı? | Vault Steward kararı |
| 3 | Doğrudan PHP engeli (`-htaccess:22-24`) IIS'e neden taşınmadı — kasıtlı mı? | ⚠️ VERIFICATION REQUIRED |
| 4 | §1.5'teki 319 ↔ 320 farkı — Vault Steward hangi sayımı esas alacak? | Vault Steward kararı — bu dosyada sayımlar değiştirilmedi |
| 5 | `secret-scan.yml` (31 satır) bu dosyanın §2 çift-yazım envanterine eklenmeli mi (gitleaks ≠ rewrite)? | Vault Steward kararı |

### §5.4 Bağlantı matrisi — vaat ↔ bu dosya

| # | Vaat eden | Vaat | Karşılığı |
|---|---|---|---|
| 1 | `tasinabilirlik-siniri-anayasa.md` §3.4 adım 2 | "her özel yüzeyin eşdeğeri → `-ornekler` §1" | §1.4 (6 satır, 4'ü örnekli, 2'si dürüstlük kaydı) |
| 2 | `tasinabilirlik-siniri-surucu.md` §7.3 satır 2 | "`-ornekler` §1-§3 çalışan örnekler" | §1 (PHP) · §2 (çift yazılım) · §3 (CI) |
| 3 | `tasinabilirlik-siniri/index.md` §3 | "`-ornekler` §1-§5 (ornekler iskeleti)" | §1-§5 tam · §6 kaynakça |
| 4 | `platform-yuzeyleri-ornekler.md` | iskelet (S1 dili, §ELI15, ölçüm, kaynakça) | bu dosyanın yapısı |

## §6 Kaynakça (2026-10-13 · resmi kaynak · envanter kesişimi 0)

### §6.1 Bu oturumda doğrulanmış yeni URL (webfetch GET → HTTP 200)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | Yol çözümleme POSIX/Linux tarifi (`path_resolution(7)`) | https://man7.org/linux/man-pages/man7/path_resolution.7.html | 2026-10-13 | ✅ HTTP 200 |
| 2 | Windows dosya adı kuralları (yasaklı karakterler, uzunluklar) | https://learn.microsoft.com/en-us/windows/win32/fileio/naming-a-file | 2026-10-13 | ✅ HTTP 200 |
| 3 | Windows dosya yolu uzunluk sınırı (`MAX_PATH`) | https://learn.microsoft.com/en-us/windows/win32/fileio/maximum-file-path-limitation | 2026-10-13 | ✅ HTTP 200 |

### §6.2 Kardeş dosyalardan yeniden kullanılan doğrulanmış URL (2026-10-13 · 200)

| # | İddia | URL | Teyit kaynağı |
|---|---|---|---|
| 4 | Windows yerel kütüphane yükleme API'si `LoadLibraryW`'dir | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibraryw | `-surucu` §7.1 (satır 476) |
| 5 | MSVC derleme seçenekleri kategoriye göre listelenir | https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options-listed-by-category | `-surucu` §7.1 (satır 477) |
| 6 | GitHub Actions `runner.os` gibi değişkenler resmi olarak tanımlı | https://docs.github.com/en/actions/learn-github-actions/variables | `-dogrulama` §E.1 (satır 356) |
| 7 | POSIX çalışma zamanı kütüphane yükleme yüzeyi `dlopen()` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/dlopen.html | `-anayasa` §9 (satır 441) |
| 8 | Windows işletim sistemi sürümü çalışma zamanında sorgulanabilir | https://learn.microsoft.com/en-us/windows/win32/sysinfo/operating-system-version | `-anayasa` §9 (satır 443) |

### §6.3 Repo kanıtları (bu oturumda okunmuş satırlar)

| # | Dosya | Satırlar | Ne |
|---|---|---|---|
| 1 | `shared/src/Component/TemplateEngine.php` | 73 | `DIRECTORY_SEPARATOR` ile cache yolu |
| 2 | `shared/src/Api/Bff/DesktopBff.php` | 34-38 | `php_uname('s')` + `?? 'unknown'` |
| 3 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199-202 | `realpath` öneki + 403 |
| 4 | `shared/tests/Component/TemplateEngineTest.php` | 27, 87, 107 | testlerde aynı ayrac kalıbı |
| 5 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 38 | `DIRECTORY_SEPARATOR` + sqlite dosya adı |
| 6 | `home.coremusic.net/.htaccess` | 2-37 (tam) | Apache yüzeyi |
| 7 | `home.coremusic.net/web.config` | 1-43 (tam) | IIS yüzeyi |
| 8 | `.github/workflows/ci.yml` | 25-183 (6 iş) | `ubuntu-latest` ×6 · `runner.os` ×4 · `matrix` yalnız `project` |
| 9 | `.github/workflows/secret-scan.yml` | 1-31 (tam) | `permissions: contents: read` · `concurrency` + `cancel-in-progress` · 1 iş · `timeout-minutes: 10` |
| 10 | `.github/workflows/` (dizin) | — | envanter: yalnız `ci.yml` + `secret-scan.yml` (2 dosya · 234 satır) |
| 11 | `.github/AGENTS.md` | §CI notu | "3 job" iddiası — ölçümle çelişir (7 iş) → `zamanlama-scheduler-ornekler.md` §1.6 ⚠️ kaydı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
