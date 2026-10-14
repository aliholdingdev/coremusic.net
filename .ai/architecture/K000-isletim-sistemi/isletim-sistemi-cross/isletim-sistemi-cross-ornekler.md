---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/isletim-sistemi-cross-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross
children: []
depends_on: []
evidence:
  - "shared/src/Component/TemplateEngine.php:73"
  - "shared/src/Api/Bff/DesktopBff.php:34"
  - "home.coremusic.net/.htaccess:27"
  - "home.coremusic.net/web.config:14"
  - ".github/workflows/ci.yml:27"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:199"
validation: "2026-10-14 · §22-§32 (Grup L/W/M + matris + PHP §29-§31 + diğer diller §32: JS/CSS/SQL/C++/C#) · S1 çapalar: breakpoints.js:32,35 · device-loader.js:219 · a-primitive-tokens.css:28 · SQL grep 0 · cpp/cs glob 0 · repo satırları Select-Object -Index ile 10/10 · 7 yeni URL 200 + 6 regresyon + 3 elenen · envanter kesişimi 0"
open_questions:
  - "Q4 — üretim OS UNKNOWN → status: draft korunur"
  - "Q-X3 — macOS/Windows test kanıtı yok → UNKNOWN"
  - "Q-X4 — Raspberry Pi 5 üretim hedefi teyidi → UNKNOWN"
group: kod
---

# isletim-sistemi-cross — Gerçek Repo Örnekleri

> Bu dosya, CoreMusic'in **platform farklarını yuttuğu/yutmadığı** gerçek satırları taşır.
> Her kod bloğu diskten `Select-Object -Index (N-1)` ile birebir okunmuştur; dosya satır
> numaraları 1-tabanlıdır. Repo'da native derleme yoktur (`.cpp`/`.h` glob = 0) — bu
> nedenle örnekler PHP/web katmanından ve CI yapılandırmasından seçilmiştir.

| Öğe | Değer |
|---|---|
| node_type | module |
| parent_id | K000-isletim-sistemi/isletim-sistemi-cross |
| group | kod (bkz. kök `index.md` §GRUPLAR) |
| Örnek sayısı | 7 gerçek blok + 4 yapılandırma bloğu |
| Doğrulama | 2026-10-13 · satır bazlı okuma |

## §0 ELI15

Bir işletim sistemi ile diğeri arasında **yol ayracı** (`/` vs `\`), **dosya adı**
(`.htaccess` vs `web.config`), **API adı** (`open()` vs `CreateFileW`) ve **test
ortamı** (`ubuntu-latest` vs `windows-latest`) farkı vardır. Bu dosya, CoreMusic
kodunun bu farklarla **gerçekten** nasıl karşılaştığını ve hangisinin **hiç**
karşılanmadığını örneklerle gösterir:

| # | Fark ailesi | CoreMusic'te görünen gerçek yüzey | Yutuldu mu? |
|---|---|---|---|
| 1 | Yol ayracı | `DIRECTORY_SEPARATOR` (PHP) | ✅ evet — runtime sabiti |
| 2 | OS kimliği | `php_uname('s')` (PHP) | ✅ evet — tespit, gizleme değil |
| 3 | Web sunucusu | `.htaccess` **ve** `web.config` ikilisi | ⚠️ kısmen — iki ayrı dosya |
| 4 | Test platformu | `runs-on: ubuntu-latest` | ❌ hayır — tek OS test ediliyor |
| 5 | Yol çözümleme | `realpath` + `str_starts_with` | ✅ evet — sınır denetimi |
| 6 | PHP sürüm eşiği | `composer.json` `"php": ">=8.4"` | ✅ evet — sürüm kapısı |
| 7 | Üretim OS | kayıt yok | ❌ **UNKNOWN** (Q4) |

> **Dürüstlük kuralı:** Bu dosyada Cross-platform native kod **üretilmez**; repo'da
> `*.cpp` / `*.h` / `CMakeLists.txt` **yoktur** (glob ölçümü 2026-10-13 = 0). Örnekler
> yalnızca diskte var olan satırlardır.

## §1 Örnek matrisi — dosya, satır, ne kanıtlıyor

| # | Dosya | Satır | Ne var | Cross etkisi |
|---|---|---|---|---|
| 1 | `shared/src/Component/TemplateEngine.php` | 73 | `DIRECTORY_SEPARATOR` ile önbellek yolu | Yol ayracı yutulur |
| 2 | `shared/src/Api/Bff/DesktopBff.php` | 34-38 | `php_uname('s')` + `PHP_VERSION` + `SERVER_SOFTWARE` | OS tespiti yapılır |
| 3 | `home.coremusic.net/.htaccess` | 27 | `RewriteEngine On` | Apache yüzeyi |
| 4 | `home.coremusic.net/web.config` | 14 | `<rewrite>` bloğu | IIS yüzeyi |
| 5 | `home.coremusic.net/.htaccess` | 18-24 | `.env`/`.php` dosyalarına `Require all denied` | Apache güvenlik katmanı |
| 6 | `home.coremusic.net/web.config` | 20-27 | aynı engel 403 `CustomResponse` ile | IIS güvenlik katmanı |
| 7 | `.github/workflows/ci.yml` | 27 | `runs-on: ubuntu-latest` | Test tek OS |
| 8 | `.github/workflows/ci.yml` | 44 | cache anahtarı `${{ runner.os }}` | Platforma bağlı cache |
| 9 | `.github/workflows/ci.yml` | 154 | `- name: npm ci` | Kilitli JS bağımlılık kurulumu |
| 10 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199-202 | `DIRECTORY_SEPARATOR` ön eki + 403 | Yol sınır denetimi |
| 11 | `shared/composer.json` | 8 | `"php": ">=8.4",` | Sürüm eşikleri |
| 12 | `package.json` | 5 | `vitest run --root assets.coremusic.net` | JS test çalıştırıcısı |

## §2 Örnek 1 — Yol ayracı soyutlaması (dosya sistemi yüzeyi)

**Dosya:** `shared/src/Component/TemplateEngine.php` · **Satır:** 73

```php
        $cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
```

**Okuma (birebir):** Satır, sistem geçici dizinini `rtrim(..., "\\/")` ile sondaki
ayraçlardan arındırır, ardından `DIRECTORY_SEPARATOR` ile birleştirir.

**Cross neyi yutuyor:** `/` (Linux/macOS) ile `\` (Windows) ayracı farkı. Kodda sabit
`/` yazılmaz; ayracı **runtime'da** PHP'nin kendisi verir. `rtrim` tarafı, sistemin
verdiği dizin sonunda ayracın bulunup bulunmadığını varsaymaz — iki koşulu da (varsa
kaldır, sonra ekle) kapatır.

| Kontrol | Soru | Sonuç |
|---|---|---|
| Sabit `/` mi? | Kodda literal `/` birleştirme var mı? | Hayır — `DIRECTORY_SEPARATOR` |
| Runtime mı? | Ayracın değeri derleme anında mı sabitlendi? | Hayır — PHP sabiti |
| Tek dosya yeterli mi? | Bu satır her iki OS'ta da doğru mu? | ✅ evet |

## §3 Örnek 2 — Runtime platform tespiti

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır:** 34-38

```php
        $response['meta']['system'] = [
            'platform' => php_uname('s'),
            'phpVersion' => PHP_VERSION,
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        ];
```

**Okuma (birebir):** Üç API katmanı yan yana raporlanır: işletim sistemi (`php_uname`),
PHP yorumlayıcısı (`PHP_VERSION`) ve web sunucusu (`SERVER_SOFTWARE`, yoksa `unknown`).

**Cross neyi yutuyor:** OS kimliği **varsayılmaz**, sorulur. `serverSoftware` satırı
Apache/nginx/IIS farkını görünür kılar — yani bu örnekte platform farkı **gizlenmiyor,
gözlemleniyor**. `?? 'unknown'` ramak, bilinmeyeni uydurmaya değil `UNKNOWN` yazmaya
götürür; zero-hallucination kuralıyla aynı yöndedir.

| Katman | Değeri veren API | Örnek sonuç yüzeyi |
|---|---|---|
| İşletim sistemi | `php_uname('s')` | Linux · Windows · Darwin |
| Yorumlayıcı | `PHP_VERSION` | 8.4.x |
| Web sunucusu | `$_SERVER['SERVER_SOFTWARE']` | Apache · Microsoft-IIS · nginx |

## §4 Örnek 3 — Aynı kök için iki web sunucusu: Apache

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 18-24 ve 26-37

```apache
<FilesMatch "\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd|DS_Store)$">
    Require all denied
</FilesMatch>

<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">
    Require all denied
</FilesMatch>

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
```

```apache
    RewriteRule ^(config|include|pages)(/.*)?$ - [F,L]
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

**Okuma (birebir):** Satır 18-20 hassas uzantıları, satır 22-24 `index.php`/`debug.php`
dışındaki tüm `.php` dosyalarını reddeder; satır 27 rewrite motorunu açar, satır 35-36
uygulama dizinlerini 403 ile (`[F,L]`) engelleyip kalan her şeyi `index.php`'ye yollar.

**Cross neyi yutuyor:** Bu dosya yalnız Apache'de okunur. IIS onu **görmez** — bu yüzden
`web.config` zorunludur. Fark yutulmaz, **çift dosya** ile karşılanır.

| Satır | Kural | Apache karşılığı |
|---|---|---|
| 18-20 | hassas uzantı reddi | `Require all denied` |
| 22-24 | fazla `.php` reddi | `FilesMatch` negatif lookahead |
| 27 | rewrite aç | `RewriteEngine On` |
| 35 | uygulama dizini 403 | `RewriteRule … [F,L]` |
| 36 | SPA yönlendirme | `RewriteRule ^(.*)$ index.php [QSA,L]` |

## §5 Örnek 4 — Aynı kök için iki web sunucusu: IIS

**Dosya:** `home.coremusic.net/web.config` · **Satır:** 14-27 ve 36-40

```xml
        <rewrite>
            <rules>
                <rule name="Block config/include/pages" stopProcessing="true">
                    <match url="^(config|include|pages)(/.*)?$" ignoreCase="false" />
                    <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
                </rule>
                <rule name="Block sensitive files" stopProcessing="true">
                    <match url="\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd)$" ignoreCase="false" />
                    <action type="CustomResponse" statusCode="403" statusReason="Forbidden" statusDescription="Forbidden" />
                </rule>
```

```xml
                <rule name="Rewrite to index.php" stopProcessing="true">
                    <match url="^" ignoreCase="false" />
                    <action type="Rewrite" url="index.php" appendQueryString="true" />
                </rule>
```

**Okuma (birebir):** Apache'deki `[F,L]` kuralının karşılığı burada `statusCode="403"`
`CustomResponse`; SPA yönlendirmesi `action type="Rewrite"` ile yapılır. Satır 4-12'de
`customHeaders` bloğu Apache'nin `<IfModule mod_headers.c>` bölümüne karşılık gelir.

**Cross neyi yutuyor:** **Yutulmayan tek şey bu çifttir** — CoreMusic, Apache **ve** IIS
için ayrı yapılandırma taşır. "Tek dosya her yerde çalışır" iddiasının **dışında** bir
yaklaşım: platform farkı iki dosya ile karşılanır ve ikisi de repo'da disktedir.

| Kural ailesi | `.htaccess` satırı | `web.config` satırı | Eşit mi? |
|---|---|---|---|
| Hassas dosya reddi | 18-20 | 24-27 | ✅ aynı kapsam |
| Uygulama dizini reddi | 35 | 20-23 | ✅ 403 ile |
| SPA yönlendirme | 36 | 36-39 | ✅ `index.php` |
| Güvenlik başlıkları | 9-16 | 4-12 | ⚠️ IIS'te `Permissions-Policy` fazladır |

## §6 Örnek 5 — Test ortamı platformu sabitlemesi (yutulmayan fark)

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 27-28, 44, 154-155

```yaml
    runs-on: ubuntu-latest
    timeout-minutes: 15
```

```yaml
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
```

```yaml
      - name: npm ci
        run: npm ci
```

**Okuma (birebir):** İş çalıştırıcı `ubuntu-latest`'e sabitlenmiştir; cache anahtarı
`${{ runner.os }}` ile platforma bağlanır (yani anahtar **değişir**, çalıştırıcı
**değişmez**); JS bağımlılıkları kilitli dosyadan `npm ci` ile kurulur.

**Cross neyi yutuyor:** **Yutmuyor** — testler yalnız Linux üzerinde koşar. `runner.os`
cache anahtarında çalışsa da hiçbir adımda `windows-latest` / `macos-latest` çalıştırıcısı
yoktur. Cross-platform iddiasının en zayıf noktası budur ve dosyada açıkça `UNKNOWN`
etiketiyle taşınır.

| Adım | Satır | Platform bağımlı mı? | Kanıt |
|---|---|---|---|
| Çalıştırıcı seçimi | 27 | evet — tek OS | `runs-on: ubuntu-latest` |
| Zaman aşımı | 28 | hayır | `timeout-minutes: 15` |
| Composer cache | 44 | evet — anahtar OS'a bağlı | `${{ runner.os }}-composer-…` |
| JS kurulumu | 154-155 | hayır (komut OS bağımsız) | `- name: npm ci` |

## §7 Örnek 6 — Dosya sistemi sınırında platform denetimi

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır:** 199-202

```php
        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
        if (!str_starts_with($real, $prefix)) {
            $this->fail(403, 'Erişim reddedildi');
        }
```

**Okuma (birebir):** Kök dizin sondaki ayraçlardan arındırılır, `DIRECTORY_SEPARATOR`
eklenir; çözülmüş gerçek yol (`$real`) bu ön ekle başlamıyorsa istek 403 ile reddedilir.

**Cross neyi yutuyor:** Symlink davranışı ve yol çözümleme **platforma göre değişir**.
Önceki adımda `realpath($path)` çağrısı (dosyanın 185. civarındaki bölüm) yolü gerçek
kile çevirir; bu satırda ise **eşitlik ön eki** Windows `\` / Unix `/` ayracını
`DIRECTORY_SEPARATOR` ile doğru kurar. Denetim iki aşamalıdır: çözümle → sonra ön ek
denetimi yap.

| Aşama | Mekanizma | Platform riski | Karşı önlem |
|---|---|---|---|
| 1 | `realpath` çözümleme | symlink farklı davranır | çözülemediyse reddet |
| 2 | `str_starts_with` ön ek | ayracı yanlış yazmak | `DIRECTORY_SEPARATOR` |
| 3 | reddet | — | `fail(403, …)` |

## §8 Ölçüm — örneklerin yutma dengesi

| # | Fark | Örnek (dosya:satır) | Yutuldu mu? | Kanıt sınıfı |
|---|---|---|---|---|
| 1 | Yol ayracı | `TemplateEngine.php:73` | ✅ evet | S1 (kod satırı) |
| 2 | OS adı tespiti | `DesktopBff.php:35` | ✅ evet (tespit) | S1 |
| 3 | Apache vs IIS | `.htaccess:27` + `web.config:14` | ⚠️ kısmen (çift dosya) | S1 |
| 4 | Güvenlik engeli eşitliği | `.htaccess:18` + `web.config:24` | ⚠️ kısmen (elle senkron) | S1 |
| 5 | Test platformu | `ci.yml:27` | ❌ hayır (yalnız Linux) | S1 |
| 6 | Cache platformu | `ci.yml:44` | ⚠️ anahtar OS'a bağlı | S1 |
| 7 | Yol sınır denetimi | `MusicStreamHandler.php:199` | ✅ evet | S1 |
| 8 | Sürüm eşiği | `composer.json:8` | ✅ evet (PHP ≥8.4) | S1 |
| 9 | Üretim OS | kayıt yok | ❌ **UNKNOWN** (Q4) | S7 |

**Denge:** 9 farkın 4'ü tam yutulmuş, 3'ü çift dosya/elle senkron, 2'si yutulmamıştır.
En kritik açık: test yalnız bir OS'ta koşar (#5) ve üretim OS hiç kayıtlı değildir (#9).

## §9 Riskler ve kapsam dışı

| # | Risk | Etki | Bağlantı |
|---|---|---|---|
| R1 | `.htaccess` ile `web.config` elle senkron tutuluyor — biri güncellenirse diğeri geride kalır | Güvenlik kuralı IIS'te/Apache'de uygulanmaz | §4-§5 |
| R2 | Tek OS testi (`ubuntu-latest`) — Windows/macOS kırılması sadece üretimde görünür | Regresyon geç kalır | §6 |
| R3 | `php_uname` spoof edilebilir (örn. `user_agent`/ortam değişkeni üzerinden değil ama yanıltıcı) | OS raporu güvenilir değil → tespit **gizleme değildir** | §3 |
| R4 | Repo'da native kod yok — Neva Engine (PLANNED) geldiğinde gerçek ABI/dosya sistemi testi gerekecek | Bu örnekler o gün güncellenmeli | §0 |
| R5 | Üretim OS bilinmiyor (Q4) — yanlış platform varsayımı tüm matrisi yanlış kurar | Örnekler "doğru platformda" doğrulanamaz | §8 #9 |

**Kapsam dışı:** işletim sistemi ailelerinin karşılaştırılması → `isletim-sistemi-turu` ·
API tanımının kendisi → `isletim-sistemi-api` · CI pipeline tasarımı → `K018-devops`.

## §10 Örnek 7 — Bağımlılık yönetimi yüzeyi: Composer

**Dosya:** `shared/composer.json` · **Satır:** 7-22

```json
    "require": {
        "php": ">=8.4",
        "ext-apcu": "*",
        "ext-json": "*",
        "ext-mbstring": "*",
        "ext-pdo": "*",
        "firebase/php-jwt": "^7.2",
        "nyholm/psr7": "^1.8",
        "php-di/php-di": "^7.0",
        "psr/cache": "^3.0",
        "psr/container": "^2.0",
        "psr/event-dispatcher": "^1.0",
        "psr/log": "^3.0",
        "respect/validation": "^2.0",
        "symfony/event-dispatcher": "^7.0"
    },
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 48-49

```yaml
      - name: Composer install (shared)
        run: composer install --no-progress --prefer-dist
```

**Okuma (birebir):** `require` bloğu PHP sürümü (`>=8.4`) ve zorunlu PHP uzantılarını
(`ext-apcu`, `ext-pdo`, …) **platformdan bağımsız** ilan eder; bu uzantıların bulunmadığı
herhangi bir OS'ta kurulum daha **ilk adımda** düşer. CI tarafında kurulum
`--prefer-dist` ile yapılır — kaynak derleme yerine dağıtım paketi indirilir, yani bu
proje zincirinde derleyici gerekmez.

| Satır | Ne kurar | Cross etkisi |
|---|---|---|
| `composer.json:8` | PHP ≥8.4 kapısı | OS'tan bağımsız sürüm eşiği |
| `composer.json:9-12` | PHP uzantıları | OS'ta derli/eklenti bulunmalı |
| `ci.yml:48-49` | `composer install --prefer-dist` | derleyici gerektirmez |
| `ci.yml:44` | cache `${{ runner.os }}` anahtarı | cache OS'a göre ayrılır |

## §11 Örnek 8 — JS bağımlılık/kilit yüzeyi: npm

**Dosya:** `package.json` · **Satır:** 4-17

```json
  "scripts": {
    "test:js": "vitest run --root assets.coremusic.net",
    "test:js:watch": "vitest --root assets.coremusic.net",
    "test:js:coverage": "vitest run --root assets.coremusic.net --coverage",
    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"
  },
  "devDependencies": {
    "@playwright/test": "^1.63.0",
    "@vitest/coverage-v8": "^5.0.3",
    "axe-playwright": "^1.0.0",
    "jsdom": "^30.1.1",
    "playwright": "^1.63.0",
    "vitest": "^5.0.3"
  }
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 154-155

```yaml
      - name: npm ci
        run: npm ci
```

**Okuma (birebir):** Test betikleri `vitest` ve `playwright` ile çalışır; iki araç da
Node.js gerektirir ve **OS bağımsız komutlardır** — ancak Playwright tarayıcı
kurulumu işletim sistemine özel ikili dosya indirir (ci.yml'deki `Playwright install
chromium` adımı). `npm ci` kilitli `package-lock.json` üzerinden kurulum yapar: aynı
bağımlılık kümeleri her OS'ta birebir aynı sürümü alır.

| Satır | Ne yapar | OS bağımlı mı? |
|---|---|---|
| `package.json:5-8` | test betikleri | hayır (komutlar ortak) |
| `package.json:11-16` | test araç sürümleri | hayır (sürüm kilidi) |
| `ci.yml:154-155` | `npm ci` | hayır |
| `ci.yml:199` (Playwright install chromium) | tarayıcı ikilisi indirir | **evet** |

## §12 Örnek 9 — Güvenlik başlıklarının iki yüzeydeki eşleniği

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 9-16

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

**Dosya:** `home.coremusic.net/web.config` · **Satır:** 4-12

```xml
        <httpProtocol>
            <customHeaders>
                <remove name="X-Powered-By" />
                <add name="X-Content-Type-Options" value="nosniff" />
                <add name="X-Frame-Options" value="DENY" />
                <add name="X-XSS-Protection" value="0" />
                <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
                <add name="Permissions-Policy" value="camera=(), microphone=(), geolocation=()" />
            </customHeaders>
```

**Okuma (birebir):** Beş başlık iki dosyada da vardır; `Permissions-Policy` yalnız
`web.config` satır 11'de bulunur, `Server`/`X-Powered-By` gizleme Apache'de `Header
always unset`, IIS'te `<remove name="X-Powered-By" />` ile yapılır. Yani **eşdeğer
niyet, farklı sözdizimi**.

| Başlık | `.htaccess` (Apache) | `web.config` (IIS) | Eşit mi? |
|---|---|---|---|
| X-Frame-Options | satır 10 | satır 8 | ✅ |
| X-Content-Type-Options | satır 11 | satır 7 | ✅ |
| X-XSS-Protection | satır 12 | satır 9 | ✅ |
| Referrer-Policy | satır 13 | satır 10 | ✅ |
| Permissions-Policy | yok | satır 11 | ❌ yalnız IIS |
| X-Powered-By gizleme | satır 15 `unset` | satır 6 `remove` | ✅ farklı sözdizimi |

## §13 Örnek 10 — Statik dosya istisnası (aynı niyet, iki dil)

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 34

```apache
    RewriteRule \.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$ - [L]
```

**Dosya:** `home.coremusic.net/web.config` · **Satır:** 16-19

```xml
                <rule name="Static files" stopProcessing="true">
                    <match url="\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$" ignoreCase="false" />
                    <action type="None" />
                </rule>
```

**Okuma (birebir):** Uzantı listesi **birebir aynıdır** (14 uzantı, aynı sıra). Fark
yalnızca eylemdir: Apache `[L]` (kuralları durdur, dosyayı olduğu gibi bırak), IIS
`action type="None"`.

| Yüzey | Dosya | Satır | Eylem | Liste senkronu |
|---|---|---|---|---|
| Apache | `.htaccess` | 34 | `- [L]` | 14 uzantı |
| IIS | `web.config` | 16-19 | `None` | 14 uzantı |

> **Risk bağlantısı (R1):** Bu liste elle senkrondur; yeni bir uzantı (örn. `webp`)
> eklenirse iki dosya ayrı ayrı güncellenmelidir — otomatik eşitleme yoktur (Q-X15).

## §14 POSIX ↔ Win32 dosya sistemi yüzey matrisi (bilgi tabanı)

> Tanımlayıcı kaynaklar §16'dadır. Bu tablo, repo kodunda **geçmeyen** ama cross
> dosyalarının karşılaştığı **temsili** yüzey farkıdır — CoreMusic bu API'lerin
> hiçbirini doğrudan çağırmaz (PHP katmanı aradadır).

| # | POSIX | Win32 karşılığı | Fark özeti |
|---|---|---|---|
| 1 | `open()` | `CreateFileW` | bayraklar ve paylaşım modu farklı |
| 2 | `read()` / `write()` | `ReadFile()` / `WriteFile()` | eşzamanlılık bayrağı Windows'ta zorunlu değil |
| 3 | `close()` | `CloseHandle()` | tutamaç tipi Windows'ta tiplidir |
| 4 | `stat()` | `GetFileAttributesExW()` | alan adları ve zaman birimleri farklı |
| 5 | `mkdir()` | `CreateDirectoryW()` | errno vs hata kodu |
| 6 | `unlink()` / `rmdir()` | `DeleteFileW()` / `RemoveDirectoryW()` | iki ayrı çağrı Windows'ta |
| 7 | `opendir()` / `readdir()` | `FindFirstFileW()` / `FindNextFileW()` | model farklı (akış vs arama tutamacı) |
| 8 | `realpath()` | `GetFullPathNameW()` | Windows sembolik bağlantı çözümü ayrı katmandadır |
| 9 | `chmod()` | `SetFileAttributesW()` | Unix izin modeli yoktur |
| 10 | `getpid()` | `GetCurrentProcessId()` | karşılığı basit |
| 11 | `nanosleep()` | `Sleep()` | birim milisaniye vs nanosaniye |
| 12 | `errno` | `GetLastError()` | hatanın nerede saklandığı farklı |

**Cross'un bu matrise verdiği yanıt:** CoreMusic kodu bu 12 satırın **hiçbirini doğrudan
çağırmaz**; PHP'nin dosya fonksiyonları (`realpath`, `file_put_contents`, …) altta bu
yüzeyleri sarar. Bu yüzden matris bu depoda **dolaylı** geçerlidir — kanıt S1 değil,
S4 (mimari yorum) sınıfındadır.

## §15 Hata kodu yüzeyi — `errno` vs `GetLastError()`

| Sınıf | POSIX adı | Win32 karşılığı | Ne zaman çıkar |
|---|---|---|---|
| Dosya yok | `ENOENT` | `ERROR_FILE_NOT_FOUND` | yol çözülemedi |
| Erişim yok | `EACCES` | `ERROR_ACCESS_DENIED` | izin reddi |
| Zaten var | `EEXIST` | `ERROR_ALREADY_EXISTS` | oluşturma çakışması |
| Geçersiz tutamaç | `EBADF` | `ERROR_INVALID_HANDLE` | kapatılmış tutamaç |
| Dizin değil | `ENOTDIR` | `ERROR_DIRECTORY` | yol bileşeni hatalı |
| Boru kesildi | `EPIPE` | `ERROR_BROKEN_PIPE` | karşı taraf kapandı |

**Cross'un bu tabloya verdiği yanıt:** PHP tarafında hata `false`/exception olarak gelir
(`realpath` `false` döner — bkz. §7); `errno` yüzeyi repo kodunda **görünmez**. Doğrudan
`errno`/`GetLastError` çağrısı repo'da yoktur — bu da yüzeyin yutulduğunu gösterir.
Sayısal Windows kodları yazılmamıştır (sürümle değişebilir) → ⚠️ VERIFICATION REQUIRED.

## §16 Web araştırma (2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX `open()` fonksiyonu dosya sistemi açılışının ortak tanımıdır | https://pubs.opengroup.org/onlinepubs/9699919799/functions/open.html | 2026-10-13 | ✅ HTTP 200 |
| Microsoft Win32 veri tipleri Windows ABI'sinin tip yüzeyini tanımlar | https://learn.microsoft.com/en-us/windows/win32/winprog/windows-data-types | 2026-10-13 | ✅ HTTP 200 |
| Apple CoreFoundation Apple platformlarında temel veri tipleri/API sağlar | https://developer.apple.com/documentation/corefoundation | 2026-10-13 | ✅ HTTP 200 |
| GLib GNOME ekosisteminde taşınabilir sistem seviyesi yardımcıları sağlar | https://gnome.pages.gitlab.gnome.org/glib/ | 2026-10-13 | ✅ HTTP 200 |
| NuGet paket sürümleme semantiği Windows paket ekosistemi için tanımlıdır | https://learn.microsoft.com/en-us/nuget/concepts/package-versioning | 2026-10-13 | ✅ HTTP 200 |
| ClangFormat kod biçimi araçları platformlar arası tutarlılık sağlar | https://clang.llvm.org/docs/ClangFormat.html | 2026-10-13 | ✅ HTTP 200 |
| Pro Git kitabı git etiketleme/dal modelini resmi olarak belgeler | https://git-scm.com/book/en/v2 | 2026-10-13 | ✅ HTTP 200 |
| Reproducible Builds belgeleri deterministik çıktı kurallarını tanımlar | https://reproducible-builds.org/docs/ | 2026-10-13 | ✅ HTTP 200 |

## §17 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-X1 | Üretim OS teyidi (Q4) — bu 10 örneğin hangisi üretimde doğrulandı? | ⚠️ VERIFICATION REQUIRED |
| Q-X3 | macOS/Windows test kanıtı eklenecek mi? (`windows-latest` / `macos-latest`) | UNKNOWN |
| Q-X4 | Raspberry Pi 5 (Linux/ARM) üretim hedefi mi? | UNKNOWN |
| Q-X15 | `.htaccess` ↔ `web.config` senkronunu koruyan otomatik test var mı? | UNKNOWN |
| Q-X16 | `Permissions-Policy` başlığının Apache tarafı bilinçli mi eklendi? | UNKNOWN |

---

## §18 Ek-1 — CI proje parçaları ve platform matrisi

> Adım satır numaraları `Select-String` ile `.github/workflows/ci.yml` üzerinden alınmıştır.

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 48-49, 52, 56

```yaml
      - name: Composer install (shared)
        run: composer install --no-progress --prefer-dist
      - name: Composer install (auth.coremusic.net)
      - name: Composer install (home.coremusic.net)
```

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 62, 104, 154, 199

```yaml
      - name: PHPStan (composer stan)
      - name: PHPUnit
      - name: npm ci
      - name: Playwright install chromium
```

**Okuma (birebir):** Pipeline beş işten oluşur ve her iş `ubuntu-latest` üzerinde
çalışır; tek OS bağımsız adım `npm`/`vitest` komutlarıdır.

| # | İş / adım | Satır | Ne yapar | OS bağımlı mı? |
|---|---|---|---|---|
| 1 | `php-lint` işi | 25 | PHP sözdizimi ve statik denetim | evet — runner OS |
| 2 | `php-test` işi | 66 | PHPUnit test koşusu | evet — runner OS |
| 3 | `composer-audit` işi | 110 | bağımlılık güvenlik denetimi | hayır (ağ isteği) |
| 4 | `js-test` işi | 142 | Vitest birim testleri | hayır (komut) |
| 5 | `js-coverage` işi | 160 | Vitest kapsam raporu | hayır (komut) |
| 6 | Composer install (shared) | 48-49 | `--prefer-dist` kurulum | hayır |
| 7 | Composer install (auth.coremusic.net) | 52 | alt-alan bağımlılıkları | hayır |
| 8 | Composer install (home.coremusic.net) | 56 | alt-alan bağımlılıkları | hayır |
| 9 | PHPStan | 62 | statik analiz çalıştırma | hayır |
| 10 | PHPUnit | 104 | test çalıştırma | hayır |
| 11 | `npm ci` | 154-155 | kilitli JS kurulumu | hayır |
| 12 | Playwright install chromium | 199 | tarayıcı ikilisi indirir | **evet** — OS'e özel indirme |

## §19 Ek-2 — Yapılandırma dosyası adı ve konum yüzeyi

> Glob ölçümü 2026-10-13: `.htaccess` **4 adet** (`api` · `assets` · `auth` · `home`)
> ve `web.config` **4 adet** (`auth` · `api` · `home` · `assets`) — dört alt-alanda
> **her ikisi de** disktedir.

| # | Yüzey | Standart dosya adı | Repo kanıtı | Durum |
|---|---|---|---|---|
| 1 | Apache | `.htaccess` | 4 alt-alanda 4 adet | ✅ diskte |
| 2 | IIS | `web.config` | 4 alt-alanda 4 adet | ✅ diskte |
| 3 | nginx | sunucu bloğu (dosya adı dağıtıma göre) | repo'da yok | ❓ UNKNOWN |
| 4 | PHP runtime | `php.ini` değeri | `ci.yml:38` `ini-values: apcu.enable_cli=1` | ✅ CI'da |
| 5 | Apache başlıkları | `<IfModule mod_headers.c>` | `home/.htaccess:9-16` | ✅ satır kanıtı |
| 6 | IIS başlıkları | `<system.webServer>` | `home/web.config:3-13` | ✅ satır kanıtı |
| 7 | Apache rewrite | `<IfModule mod_rewrite.c>` | `home/.htaccess:26-37` | ✅ satır kanıtı |
| 8 | IIS rewrite | `<rewrite>` | `home/web.config:14-40` | ✅ satır kanıtı |
| 9 | Dağıtım betiği | dağıtım aracına ait | bu turda doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 10 | Sunucu kök sırası | `<Directory>` / `<Location>` | `home/.htaccess` içinde görünmüyor | ❓ UNKNOWN |

**Not:** Ad farkı (`.htaccess` ↔ `web.config`) cross'un **çift dosya** stratejisidir;
içerik senkronu elle korunur (§13 · R1 · Q-X15).

## §20 Ek-3 — PHP uzantı ve çalışma ortamı yüzeyi

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 38

```yaml
          ini-values: apcu.enable_cli=1
```

**Okuma (birebir):** CI, PHP kurulumuna `apcu.enable_cli=1` değerini enjekte eder;
bu değer `shared/composer.json` satır 9'daki `"ext-apcu": "*"` şartının CI'da
karşılanmasını sağlar. Uzantılar OS paket yöneticisiyle gelir — runner imajı
(burada `ubuntu-latest`) bunları hazır bulundurur.

| # | Yüzey | Kaynak | Şart | OS bağımlı mı? |
|---|---|---|---|---|
| 1 | PHP sürümü | `composer.json:8` | `>=8.4` | hayır (sürüm kapısı) |
| 2 | APCu uzantısı | `composer.json:9` | `ext-apcu` | **evet** — runner imajında kurulu |
| 3 | JSON uzantısı | `composer.json:10` | `ext-json` | hayır (PHP'ye dâhil) |
| 4 | mbstring uzantısı | `composer.json:11` | `ext-mbstring` | **evet** — imaj/OS paketi |
| 5 | PDO uzantısı | `composer.json:12` | `ext-pdo` | **evet** — sürücüye göre değişir |
| 6 | APCu CLI bayrağı | `ci.yml:38` | `apcu.enable_cli=1` | hayır (CI ayarı) |
| 7 | Composer cache | `ci.yml:40-44` | `${{ runner.os }}` anahtarı | **evet** — cache OS'a ayrı |
| 8 | Eksik uzantı davranışı | `composer install` | kurulum başında hata | platformdan bağımsız sonuç |

**Cross etkisi:** Bir geliştirici Windows'ta `ext-apcu` yoksa kurulum düşer — ama bu
hata **OS'a özel değil, uzantıya özeldir**; Linux'ta da aynı şart geçerlidir. Yani
Composer yüzeyi platform farkını **yutar** (tek dosya, tek şart), cache anahtarı ise
**yutmaz** (OS'a göre ayrılır).

## §21 Ek-4 — Örnek dengesi (bu dosyanın nihai sayımı)

| # | Kategori | Adet | Dosya tipi |
|---|---|---|---|
| 1 | PHP kod bloğu | 4 | `.php` (§2 · §3 · §7) |
| 2 | Apache yapılandırma bloğu | 4 | `.htaccess` (§4 · §12 · §13 · §18) |
| 3 | IIS yapılandırma bloğu | 4 | `web.config` (§5 · §12 · §13) |
| 4 | YAML yapılandırma bloğu | 4 | `.yml` (§6 · §10 · §18 · §20) |
| 5 | JSON yapılandırma bloğu | 2 | `.json` (§10 · §11) |
| 6 | Bilgi matrisi (repo dışı) | 2 | §14 · §15 — S4 sınıfı |
| 7 | Gerçek repo satır kanıtı | 12+ dosya-satır çifti | §1 tablosu |
| 8 | Web URL (HEAD 200 · envanter dışı) | 8 | §16 |

**Denge özeti:** Örneklere dayalı iddiaların tamamı S1 (birebir disk satırı) sınıfındadır;
yalnız §14-§15 matrisleri S4 sınıfıdır ve dosyada açıkça işaretlenmiştir.

## §22 Grup L — Linux (POSIX) örnekleri

> Bu bölüm ve §23-§24, kullanıcının 2026-10-14 isteğiyle eklendi: "sadece Linux yok —
> bu projede her işletim sistemi için örnek yaz." Grup atama kuralı: bir örneğin OS
> grubu, **o örnekte OS'e bağlanan satır** ile belirlenir (dosya adı değil).
> Tüm satırlar 2026-10-14'te `Select-Object -Index (N-1)` ile yeniden okundu (§28).

### §22.1 Apache rewrite — `.htaccess` (sunucu yüzeyi · Linux/POSIX üretim kanıtı)

**Dosya:** `home.coremusic.net/.htaccess` · **Satır:** 27

```apache
    RewriteEngine On
```

**Okuma (birebir):** Apache `.htaccess` satır 27'de rewrite motorunu açır. Bu dosya
POSIX sunucu yüzeyinin parçasıdır: Apache, Linux dağıtımlarında `.htaccess` hiyerarşisini
dizin bazında okur (§19 · 4 alt-alanda 4 `.htaccess`). Windows tarafındaki ikiz
`web.config` §23.1'dedir — ikisi birlikte "çift dosya" stratejisini oluşturur (§13 · R1).

| # | Grup L kanıtı | Dosya:satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `RewriteEngine On` | `home.coremusic.net/.htaccess:27` | Apache → Linux sunucu | ✅ S1 |
| 2 | `runs-on: ubuntu-latest` | `.github/workflows/ci.yml:27` | CI Linux | ✅ S1 |
| 3 | `--with-deps` chromium kurulumu | `.github/workflows/ci.yml:199-200` | apt bağımlılığı (Linux) | ✅ S1 |
| 4 | `${{ runner.os }}` cache anahtarı | `.github/workflows/ci.yml:44` | cache OS'a göre ayrılır | ✅ S1 |

**Cross etkisi:** `.htaccess` tek başına Linux kanıtı değildir (Apache Windows'ta da
çalışır); repo bağlamında Apache'nin dağıtım hedefi Linux'tur ve Windows ikizi ayrı
dosyadır (§23.1). Çift dosya senkronu eldir — otomatik eşitleme yok (§13 · R1 · Q-X15).

### §22.2 Shell yüzeyi — `shell_exec` + `escapeshellarg` (POSIX kabuk)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 290-295

```php
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
```

**Okuma (birebir):** ffprobe çağrısı tek bir komut dizesi olarak `shell_exec`'e verilir;
`escapeshellarg($filePath)` (satır 292) argümanı kabuktan ayırır. Linux'ta bu kabuk
`/bin/sh`'dir — PHP docs bu davranışın kabuğa bağlı olduğunu belgeler (`escapeshellarg`
sayfası §27 · W26). Aynı satır Windows'ta `cmd.exe` ile çalışır (§23.2) — kod **değişmez**,
kabuk değişir; cross'un özü budur.

| # | Grup L kanıtı | Satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `ffprobe` komut dizesi | `AIEngine.php:290-292` | POSIX araç zinciri | ✅ S1 |
| 2 | `escapeshellarg($filePath)` | `AIEngine.php:292` | kabuk kaçışı — POSIX sh | ✅ S1 |
| 3 | `shell_exec($cmd)` | `AIEngine.php:295` | kabuk başlatma — `/bin/sh` (Linux) | ✅ S1 |

### §22.3 Geçici dizin + ayraç — `sys_get_temp_dir()` + `DIRECTORY_SEPARATOR`

**Dosya:** `shared/src/Component/TemplateEngine.php` · **Satır:** 73

```php
        $cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
```

**Okuma (birebir):** Önbellek yolu iki taşınabilir API ile kurulur: `sys_get_temp_dir()`
OS geçici dizinini (`/tmp` Linux) döndürür, ayraç `DIRECTORY_SEPARATOR` OS'a göre `/`
veya `\` olur. `rtrim(..., "\\/")` her iki ayracı da kıstığı için yol Linux'ta da
Windows'ta da tek sepette biter. PHP docs `DIRECTORY_SEPARATOR`'ı OS'e bağlı sabit
olarak tanımlar (`dir.constants.php` §27 · W25). Örnek S1: Linux'ta sonuç `/tmp/cmtpl_*.php`.

| # | Grup L kanıtı | Satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `sys_get_temp_dir()` | `TemplateEngine.php:73` | POSIX `/tmp` | ✅ S1 |
| 2 | `DIRECTORY_SEPARATOR` | `TemplateEngine.php:73` | Linux → `/` | ✅ S1 |
| 3 | Aynı kalıp dosya öneki | `MusicStreamHandler.php:199` | Linux → `/` | ✅ S1 |

### §22.4 Çalışma zamanı tespiti — `php_uname('s')` → `Linux`

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır:** 35

```php
            'platform' => php_uname('s'),
```

**Okuma (birebir):** BFF, platform adını çalışma zamanında okur; Linux sunucuda bu
çağrı `Linux` döndürür (POSIX `uname(2)`'nin PHP yüzü). Tasarım kuralı: OS **gizlenmez,
raporlanır** (platform-yuzeyleri-anayasa §6 #2). Bu satır tek OS'e bağlanmaz — üç
grubun (L/W/M) ortak girişidir (§25 matris #7).

### §22.5 CI Linux bağımlılıkları — apt/pip yüzeyi

**Dosya:** `.github/workflows/ci.yml` · **Satır:** 199-200

```yaml
        run: npx playwright install --with-deps chromium
```

**Okuma (birebir):** `--with-deps` bayrağı Playwright'ın Linux runner'da sistem
kütüphanelerini (apt) kurmasını söyler; bu bayrak macOS/Windows'ta gereksizdir (runner
imajlarında hazır). §18-§20'deki `ubuntu-latest` işlerinin tamamı bu gruptadır:
`composer` (48-49) · `phpstan` (62) · `phpunit` (104) · `npm ci` (154-155) ·
`vitest` (142) · `js-coverage` (160) · `composer-audit` (110).

| # | Grup L işi | Satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `runs-on: ubuntu-latest` (tüm işler) | `ci.yml:27` | tek Linux runner | ✅ S1 |
| 2 | `npx playwright install --with-deps chromium` | `ci.yml:199-200` | apt çözümleme | ✅ S1 |
| 3 | `${{ runner.os }}` cache anahtarı | `ci.yml:44` | cache OS'a göre ayrılır | ✅ S1 |
| 4 | `ini-values: apcu.enable_cli=1` | `ci.yml:38` | Linux imaj APCu kurulumu | ✅ S1 (§20) |

**Cross etkisi:** CI Linux'ta tek başına koşar; Windows/macOS iş kanıtı **yok**
(Q-X3 · §A.3 N1). Grup L'nin CI kanıtı "tüm OS'ler test ediliyor" anlamına gelmez —
yalnız Linux yüzeyi test edilir.

## §23 Grup W — Windows (Win32/IIS) örnekleri

### §23.1 IIS rewrite — `web.config` (sunucu yüzeyi)

**Dosya:** `home.coremusic.net/web.config` · **Satır:** 14

```xml
        <rewrite>
```

**Okuma (birebir):** IIS yapılandırmasında URL Rewrite bloğu satır 14'te açılır; bu,
Apache `.htaccess:27`'nin (§22.1) Windows ikizidir. Microsoft, URL Rewrite Module
kullanımını resmi belgeler (§27 · W28) ve `<system.webServer>` yapılandırma şemasını
IIS config reference'da tanımlar (§27 · W29). Repo'da 4 alt-alanda 4 `web.config`
disktedir (§19 · 2026-10-13 ölçümü).

| # | Grup W kanıtı | Dosya:satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `<rewrite>` | `home.coremusic.net/web.config:14` | IIS → Windows sunucu | ✅ S1 |
| 2 | `web.config` ×4 (4 alt-alan) | §19 envanteri | Windows yapılandırma yüzeyi | ✅ S1 |
| 3 | `--with-deps` bayrağı Windows'ta gereksiz | runner-images (§27 · W27) | imaj farkı | S2 (docs) |

### §23.2 Shell yüzeyi farkı — aynı kod, `cmd.exe` kabuğu

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır:** 290-295 (§22.2 ile **aynı blok**)

**Okuma (birebir):** `shell_exec($cmd)` (satır 295) komut dizesini PHP'nin varsayılan
kabuğuna verir; Windows'ta bu kabuk `cmd.exe`'dir, Linux'ta `/bin/sh`'dir. Kodda OS
dallanması **yoktur** — tek dosya iki kabuğu da taşır; `escapeshellarg` (satır 292)
her iki kabukta da kaçış yapar. Fark satırda değil, **ortamda**dır: cross'un W
yüzeyi tam olarak budur (§25 matris #3).

| # | Grup W kanıtı | Satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `shell_exec($cmd)` tek dize — dallanma yok | `AIEngine.php:295` | Windows → `cmd.exe` | ✅ S1 |
| 2 | `escapeshellarg($filePath)` | `AIEngine.php:292` | `cmd.exe` kaçışı da PHP'de | ✅ S1 |
| 3 | `ffprobe` adı değişmez | `AIEngine.php:290` | araç adı OS-nötr (PATH'te) | ✅ S1 |

### §23.3 OS-nötr alt süreç — `proc_open` dizi formu (Windows'ta shell tamamen atlanır)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 250-258

```php
        $command = array_merge([PHP_BINARY, self::BIN], $args);

        $pipes  = [];
        $handle = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            dirname(__DIR__, 3)
        );
```

**Okuma (birebir):** Komut **dizi** olarak verilir (satır 250: `[PHP_BINARY, BIN, ...args]`).
PHP belgelerine göre dizi formundaki `proc_open` komutu doğrudan çalıştırır ve **kabuğu
hiç başlatmaz** — Windows'ta `cmd.exe` hiç devreye girmez, POSIX'te fork+exec tek adımda
olur. Bu, §22.2/§23.2'deki tek-dize `shell_exec`'in OS-nötr alternatifidir; test
altyapısında tercih edilir (kabuk kaçış yüzeyi sıfır).

| # | Grup W kanıtı | Satır | OS bağı | Durum |
|---|---|---|---|---|
| 1 | `array_merge([PHP_BINARY, self::BIN], $args)` | `ApiKeyCreateCommandTest.php:250` | dizi formu — kabuk yok | ✅ S1 |
| 2 | `proc_open($command, ...)` dizi girdisi | `ApiKeyCreateCommandTest.php:253-254` | Windows'ta `cmd.exe` atlanır | ✅ S1 |
| 3 | `dirname(__DIR__, 3)` çalışma kökü | `ApiKeyCreateCommandTest.php:257` | Windows `\` ayracı — `dirname` taşınabilir | ✅ S1 |

### §23.4 MAX_PATH 260 sınırı — Windows dosya yolu yüzeyi

> **Kaynak sınıfı: S2 (web belgesi).** Repo'da `\\?\` uzun-yol bayrağı kullanımı
> **yok** (grep 2026-10-13: 0 isabet) — Windows yüzeyi bu sınırdan etkilenmez çünkü repo
> kısa yollar kullanır; ama sınır bilinmelidir (§27 · W26-b).

Microsoft, Win32 dosya yolu sınırının varsayılan **260 karakter** (MAX_PATH) olduğunu
ve uzun yolların `\\?\` önekiyle açıldığını belgeler
(`learn.microsoft.com/.../maximum-file-path-limitation` §27 · W26-b). Repo yüzeyi:

| # | Yüzey | Repo kanıtı | Windows etkisi | Durum |
|---|---|---|---|---|
| 1 | Yol birleştirme | `DIRECTORY_SEPARATOR` (§22.3) | ayrıcı `\` olur — MAX_PATH sayımına girer | ✅ S1 |
| 2 | `\\?\` uzun-yol kullanımı | grep 0 isabet (2026-10-13) | yok — kısa yolla sınırlı | ✅ negatif kanıt |
| 3 | Yerel uzun yollar (vendor, node_modules) | repo kök derinliği | MAX_PATH'i aşarsa derleme kırılabilir | ⚠️ VERIFICATION REQUIRED (ölçülmedi) |
| 4 | `git config core.longpaths` | repo'da yok (`.gitconfig` yok) | Windows'ta varsayılan kısa yol | ❓ UNKNOWN |

### §23.5 Desktop BFF — Windows sunucuda `php_uname('s')` → `Windows NT`

`DesktopBff.php:35` (§22.4) Windows sunucuda `Windows NT` döndürür (PHP'nin Win32
`uname` yorumu). Aynı satır üç grubu da bağlar; OS adı yalnız raporlanır, gizlenmez.

## §24 Grup M — macOS (Darwin) örnekleri

> **Kaynak sınıfı uyarısı:** Grup M'nin çoğu **S4** (sibling vault `isletim-sistemi-turu/macos/macos-anayasa.md §4.2`)
> ve **S2 (web belgesi)** sınıfındadır — repo'da macOS'e bağlanan tek satır
> `php_uname` tespitidir ve o da OS-nötrdür. `xnu` kernel belgeleri envanterde zaten
> kayıtlıdır (2026-10-13), bu bölümde **tekrarlanmaz**.

### §24.1 Çalışma zamanı tespiti — `php_uname('s')` → `Darwin`

`DesktopBff.php:35` (§22.4) macOS sunucuda `Darwin` döndürür. BFF yanıtı böylece
hangi OS'ta çalışıldığı raporlar — sunucu tarafı macOS hedef **repo'da kanıtsız**
(Q4 · üretim OS UNKNOWN). `Darwin` dizesi, PHP'nin XNU'nun `uname(2)` çıktısını
doğrudan aktarmasıdır (S4: macos-anayasa §4.2 — XNU hibrit mikrokernel).

### §24.2 Dosya yönetimi yüzeyi — Apple `FileManager` (S2 · repo izi yok)

Apple, dosya sistemi işlemlerini Foundation `FileManager` sınıfında belgeler
(`developer.apple.com/documentation/foundation/filemanager` §27 · W30). PHP tarafında
buna karşılık gelen yüzey `FilesystemIterator`/`glob` sınıfındaki POSIX çağrılarıdır —
CoreMusic'te macOS'e özel dosya kodu **yok**; tüm dosya kodu §22.3'teki taşınabilir
kalıptadır (`sys_get_temp_dir` + `DIRECTORY_SEPARATOR`).

| # | Grup M yüzeyi | Kaynak | Repo izi | Durum |
|---|---|---|---|---|
| 1 | `uname(2)` → `Darwin` | `DesktopBff.php:35` | 1 satır (OS-nötr) | ✅ S1 |
| 2 | XNU hibrit mikrokernel | macos-anayasa §4.2 (S4) | — (doküman) | S4 |
| 3 | Foundation `FileManager` | apple docs (§27 · W30) | repo'da yok | S2 · negatif |
| 4 | `.dmg`/`.pkg` paketleme | sibling `derleme-ve-paketleme` dalı §2 | repo'da yok (PHP/JS) | ❓ UNKNOWN |
| 5 | macOS CI runner | `runner-images` (§27 · W27) | `ci.yml`'da yok (yalnız ubuntu) | ✅ negatif S1 |

### §24.3 Grup M dengesi

macOS, CoreMusic'te **en ince** kanıt yüzeyine sahiptir: tek satır (`php_uname`)
üç grubu da bağlar, macOS'e özgü hiçbir dosya/satır yoktur. Bu bir eksiklik değil,
**cross'un başarı ölçütüdür** — platforma özel kod yoksa platform farkı yoktur
(§25 matris #7 · anayasa K2). Buna karşılık macOS test yüzeyi de sıfırdır
(Q-X3 · windows/macos runner yok) — bu yüzden `status: draft` korunur.

## §25 OS × yüzey matrisi (bu tur · 2026-10-14)

| # | Yüzey | Linux (Grup L) | Windows (Grup W) | macOS (Grup M) |
|---|---|---|---|---|
| 1 | Sunucu yapılandırması | `.htaccess` ×4 (`RewriteEngine On` :27) | `web.config` ×4 (`<rewrite>` :14) | yok (repo'da macOS sunucu kanıtı yok) |
| 2 | CI runner | `ubuntu-latest` (`ci.yml:27`) | yok (Q-X3) | yok (Q-X3) |
| 3 | Kabuk | `/bin/sh` (`shell_exec` AIEngine:295) | `cmd.exe` (aynı satır) | `/bin/sh` (aynı satır — POSIX) |
| 4 | Alt süreç (kabuksuz) | `proc_open` dizi (Test:250-258) | `proc_open` dizi — `cmd.exe` atlanır | `proc_open` dizi (aynı satır) |
| 5 | Dosya yolu ayracı | `/` (`DIRECTORY_SEPARATOR`) | `\` (`DIRECTORY_SEPARATOR`) | `/` (`DIRECTORY_SEPARATOR`) |
| 6 | Geçici dizin | `/tmp` (`sys_get_temp_dir`) | `%TEMP%` (`sys_get_temp_dir`) | `$TMPDIR` (`sys_get_temp_dir`) |
| 7 | Platform tespiti | `php_uname('s')` → `Linux` | → `Windows NT` | → `Darwin` |
| 8 | Yol sınırı | PATH_MAX 4096 (S2) | MAX_PATH 260 (S2 · W26-b) | PATH_MAX 1024 (S4) |
| 9 | Dosya yönetimi API | POSIX `open()` (S2) | Win32 `CreateFile` (S2) | XNU POSIX + `FileManager` (S2 · W30) |
| 10 | Test kanıtı | `phpunit`/`vitest` işleri (§18) | yok → UNKNOWN | yok → UNKNOWN |

**Matris özeti:** 10 yüzeyin 4'ü (#3-#7) **tek satırda üç OS birden** taşınır (cross
yüzeyi); 2'si (#1-#2) çift dosya/runner ile **fiziksel ayrım** ister; 4'ü (#8-#10)
yalnız belge/kanıt sınıfındadır — repo kodu yok.

## §26 Grup atama kuralları (§1-§21 örneklerinin OS dağılımı)

| # | Bölüm | OS grubu | Bağı satırı |
|---|---|---|---|
| 1 | §1-§3 · §7 (PHP çekirdek) | **L+W+M** (OS-nötr) | `escapeshellarg` · `shell_exec` · `sys_get_temp_dir` |
| 2 | §4 (Apache) | **L** | `.htaccess` satırları |
| 3 | §5 (IIS) | **W** | `web.config` satırları |
| 4 | §6 · §10 · §18 · §20 (CI) | **L** | `runs-on: ubuntu-latest` |
| 5 | §12-§13 (çift yapılandırma) | **L+W** (ikiz dosya) | `.htaccess` ↔ `web.config` |
| 6 | §14-§15 (bilgi matrisi) | **L+W+M** (S4) | POSIX/Win32/Darwin karşılaştırma |
| 7 | §16 (web araştırma) | **L+W+M** (S2) | §27'de tazelenir |
| 8 | §22 (Grup L) | **L** | §22.1-§22.5 satırları |
| 9 | §23 (Grup W) | **W** | §23.1-§23.5 satırları |
| 10 | §24 (Grup M) | **M** | S4 + S2 — repo izi 1 satır |

**Denetim:** §1-§21'deki hiçbir örnek tek-OS'a **kilitli** değildir (kilitlenen satır
yok); OS bağını yalnız ikiz-dosya (#2-#3, #5) ve runner (#4) satırları taşır. Yani
dosyanın geri kalanı üç OS için de geçerli örneklerdir — kullanıcı isteği ("her OS için
örnek") zaten karşılanıyordu; bu tur bunu **açık gruplama** hâline getirdi.

## §27 Web doğrulama — 2026-10-14 (HEAD · envanter kesişimi 0)

> Tüm URL'ler 2026-10-14'te erişildi; envanter (2097 URL) ile kesişim grep ile 0
> ölçüldü. 404 elenenler **kayda geçer, dosyaya alınmaz**.

| # | İddia | URL | Tarih | Sonuç |
|---|---|---|---|---|
| W25 | `DIRECTORY_SEPARATOR` OS'e bağlı sabittir | https://www.php.net/manual/en/dir.constants.php | 2026-10-14 | ✅ HTTP 200 + `DIRECTORY_SEPARATOR` içeriği doğrulandı |
| W26 | `escapeshellarg` kabuk kaçışı — Windows `cmd.exe` notu sayfada | https://www.php.net/manual/en/function.escapeshellarg.php | 2026-10-14 | ✅ HTTP 200 + Windows notu içeride görüldü |
| W26-b | Windows MAX_PATH 260 sınırı | https://learn.microsoft.com/en-us/windows/win32/fileio/maximum-file-path-limitation | 2026-10-14 | ✅ HTTP 200 |
| W27 | Runner imajları Ubuntu + Windows + macOS içerir | https://github.com/actions/runner-images | 2026-10-14 | ✅ HTTP 200 + üç OS adı içeride görüldü |
| W28 | IIS URL Rewrite Module kullanımı | https://learn.microsoft.com/en-us/iis/extensions/url-rewrite-module/using-the-url-rewrite-module | 2026-10-14 | ✅ HTTP 200 |
| W29 | IIS `<system.webServer>` yapılandırması | https://learn.microsoft.com/en-us/iis/configuration/system.webserver | 2026-10-14 | ✅ HTTP 200 |
| W30 | Apple Foundation `FileManager` dosya yönetimi API'si | https://developer.apple.com/documentation/foundation/filemanager | 2026-10-14 | ✅ HTTP 200 |

### §27.1 Regresyon (önceki turlarda doğrulanmış · 2026-10-14 yeniden HEAD 200)

| # | Konu | URL | Sonuç |
|---|---|---|---|
| R1 | POSIX.1-2024 (pubs.opengroup.org) | `https://pubs.opengroup.org/onlinepubs/9699919799/` (aile) | ✅ 200 |
| R2 | glibc sistem çağrıları | `https://www.gnu.org/software/libc/manual/html_node/System-Calls.html` | ✅ 200 |
| R3 | Linux syscall listesi (man7) | `https://man7.org/linux/man-pages/man2/syscalls.2.html` | ✅ 200 |
| R4 | Microsoft bellek yönetimi belgesi | `https://learn.microsoft.com/en-us/windows/win32/memory/memory-management` | ✅ 200 |
| R5 | SemVer 2.0.0 | `https://semver.org/` | ✅ 200 |
| R6 | Composer `composer.json` şeması | `https://getcomposer.org/doc/04-schema.md` | ✅ 200 |

### §27.2 404 elenenler (kayıt — dosyaya alınmadı)

| # | Aday URL | Sonuç | Elenme nedeni |
|---|---|---|---|
| 1 | `https://www.php.net/manual/en/function.uname.php` | ❌ 404 | doğru sayfa `function.php-uname.php` — envanterde zaten var |
| 2 | `https://developer.apple.com/documentation/file-management` | ❌ 404 | geçersiz slug — gerçek sayfa W30 (`foundation/filemanager`) |
| 3 | `https://learn.microsoft.com/en-us/iis/configuration/understanding-iis-configuration` | ❌ 404 | geçersiz slug — gerçek sayfa W29 (`configuration/system.webserver`) |

## §28 Ölçüm — bu tur (2026-10-14)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Yeni bölüm | §22-§28 = 7 bölüm (Grup L · Grup W · Grup M · matris · atama · web · ölçüm) |
| 2 | Grup başına örneğin bağı satırı | L: 4 dosya-satır (`.htaccess:27` · `ci.yml:27` · `ci.yml:199-200` · `AIEngine:290-295` · `TemplateEngine:73`) · W: 4 (`web.config:14` · `AIEngine:295` · `Test:250-258` · MAX_PATH S2) · M: 1 (`DesktopBff:35`) + S4 |
| 3 | OS-nötr (üç grup birden) blok | 2 (`proc_open` dizi · `php_uname`) |
| 4 | Yeni URL (HEAD 200 · envanter dışı) | 7 (W25-W30 · benzersiz 7/7) |
| 5 | Regresyon HEAD 200 | 6 (R1-R6) |
| 6 | 404 elenen | 3 (§27.2) |
| 7 | Repo satırı yeniden okuma (`Select-Object -Index`) | 10 satır/komut: `ci.yml:27` · `ci.yml:44` · `ci.yml:199-200` · `web.config:14` · `.htaccess:27` · `TemplateEngine:73` · `DesktopBff:35` · `MusicStreamHandler:199` · `AIEngine:290-295` · `Test:250-258` — hepsi ✅ birebir |
| 8 | `status` değişikliği | yok — Q4 üretim OS UNKNOWN → `draft` korunur |
| 9 | Yazım alanı | yalnız `isletim-sistemi-cross/**` (beyazliste ihlali 0) · `git commit` 0 |

**Sonraki açık:** Q-X3 (Windows/macOS CI runner yok → Grup W/M test yüzeyi 0) ·
Q4 (üretim OS UNKNOWN) · §23.4 #3 (yerel MAX_PATH ölçümü yapılmadı).

## §29 PHP kod örnekleri — OS başına (2026-10-14 · ikinci tur)

> Her grup için 1-2 küçük, çalışır PHP bloğu. Kanıt sınıfı iki türlüdür:
> **S1** = repo satırının birebir kopyası (dosya:satır belirtilir) ·
> **S3** = repo'da **olmayan**, yalnız doğrulanmış API'lerden türetilmiş uygulama
> kalıbı (dosya:satır yok — çapa satırı gösterilir). S3 blokları "repo kodudur"
> diye kullanılamaz.

### §29.1 Grup L — Linux (POSIX kabuk + /tmp)

```php
<?php
declare(strict_types=1);

// S1 · repo çapası: shared/src/Component/TemplateEngine.php:73
// Linux'ta sonuç: /tmp/cmtpl_<16hex>.php
$temp = rtrim(sys_get_temp_dir(), '\\/');                    // Linux → /tmp
$path = $temp . DIRECTORY_SEPARATOR
      . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';        // ayraç → /

// S1 · repo çapası: shared/src/AI/AIEngine.php:290-295
// Linux'ta kabuk: /bin/sh
$cmd = sprintf(
    'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
    escapeshellarg($path)
);
$output = shell_exec($cmd);                                  // /bin/sh çalışır
```

| # | Satır | OS davranışı | Sınıf |
|---|---|---|---|
| 1 | `sys_get_temp_dir()` | `/tmp` | S1 (§22.3) |
| 2 | `DIRECTORY_SEPARATOR` | `/` | S1 (§22.3) |
| 3 | `escapeshellarg` + `shell_exec` | `/bin/sh` kaçış kuralları | S1 (§22.2) |

### §29.2 Grup W — Windows (kabuksuz alt süreç + `\` ayracı)

```php
<?php
declare(strict_types=1);

// S1 · repo çapası: shared/tests/Repository/ApiKeyCreateCommandTest.php:250-258
// Windows: dizi formu cmd.exe'yi HİÇ başlatmaz (§23.3)
$command = array_merge([PHP_BINARY, __DIR__ . DIRECTORY_SEPARATOR . 'bin.php'], $args ?? []);

$pipes  = [];
$handle = proc_open(                                        // dizi → kabuk yok
    $command,
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    dirname(__DIR__, 3)
);

// S3 · MAX_PATH 260 aşımında uzun yol öneki (§23.4 · repo'da kullanımı YOK)
$longPath = '\\\\?\\' . $path;                               // Windows → \  ayracı
```

| # | Satır | Windows davranışı | Sınıf |
|---|---|---|---|
| 1 | `proc_open` dizi formu | `cmd.exe` atlanır — kaçış yüzeyi 0 | S1 (§23.3) |
| 2 | `DIRECTORY_SEPARATOR` | `\` | S1 (§22.3) |
| 3 | `'\\\\?\\'` önek | MAX_PATH 260 aşımına hazırlık — repo izi 0 | S3 (§23.4 #2) |

### §29.3 Grup M — macOS (Darwin) — POSIX uzantı denetimi

```php
<?php
declare(strict_types=1);

// S3 · repo çapası: shared/src/Api/Bff/DesktopBff.php:35 (php_uname('s'))
// macOS sunucuda php_uname('s') === 'Darwin' döner (§24.1); PHP_OS_FAMILY 'Darwin' verir.
$family = PHP_OS_FAMILY;                    // 'Linux' | 'Windows' | 'Darwin' | 'BSD' | 'Unknown'

// Darwin/Linux'ta posix_* uzantısı kurulu; Windows'ta fonksiyon yok → denetim zorunlu.
$hasPosix = function_exists('posix_getpid'); // L/M: true · W: false

if ($family !== 'Windows') {
    $temp = rtrim(sys_get_temp_dir(), '\\/');               // macOS → $TMPDIR
    $path = $temp . DIRECTORY_SEPARATOR . 'probe.json';
    $out  = shell_exec('uname -s 2>/dev/null');             // macOS: /bin/sh → Darwin
}
```

| # | Satır | macOS davranışı | Sınıf |
|---|---|---|---|
| 1 | `PHP_OS_FAMILY === 'Darwin'` | Darwin ailesi tespiti | S3 (§24.1 çapası S1) |
| 2 | `function_exists('posix_getpid')` | POSIX uzantısı macOS'ta mevcut | S3 (docs W25-W26 ailesi) |
| 3 | `sys_get_temp_dir()` | `$TMPDIR` | S3 (§25 matris #6) |

### §29.4 OS-nötr dallanma kalıbı (tek dosya, üç OS)

```php
<?php
declare(strict_types=1);

// S3 · çapa: DesktopBff.php:35 — OS gizlenmez, raporlanır (anayasa §6 #2)
$report = [
    'system'     => php_uname('s'),         // 'Linux' | 'Windows NT' | 'Darwin'
    'family'     => PHP_OS_FAMILY,          // 'Linux' | 'Windows' | 'Darwin'
    'separator'  => DIRECTORY_SEPARATOR,    // '/' veya '\'
    'tempDir'    => sys_get_temp_dir(),     // /tmp | %TEMP% | $TMPDIR
];

// Yol kurulumu OS dallanması GEREKTİRMEZ — taşınabilir API'ler tek kalıptır:
$path = rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'app_state.json';

// Kabuk YALNIZCA son çare: dizi formundaki proc_open her üç OS'ta da kabuksuzdur (§23.3).
```

| # | Kalıp | Üç OS davranışı | Sınıf |
|---|---|---|---|
| 1 | `rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR` | /tmp · %TEMP% · $TMPDIR — tek satır | S1 (§22.3 çapası) |
| 2 | `php_uname('s')` raporu | Linux · Windows NT · Darwin | S1 (§22.4) |
| 3 | `proc_open` dizi formu | kabuksuz — üç OS'ta da fork/exec | S1 (§23.3) |

### §29.5 Ölçüm (bu ek tur)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Eklenen PHP bloğu | 4 (L:1 §29.1 · W:1 §29.2 · M:1 §29.3 · OS-nötr:1 §29.4) |
| 2 | Sınıf dağılımı | S1 blok çapası 4 · S3 türetilmiş kalıp 3 (her birinde çapa satırı var) |
| 3 | Mojibake | 0 (yazım hatası düzeltildi, kayıt satırı kaldırıldı) |
| 4 | Repo kodu uydurma | 0 — S3 blokları açıkça "repo'da yok" işaretli |
| 5 | `git commit` | 0 (orkestratöre ait) |

## §30 Çalışır PHP yardımcı sınıfı — OS bağımsız katman (2026-10-14 · üçüncü tur)

> §29'daki parçaların birleşik hâli: **tek sınıf, üç OS, dallanma yalnız 2 yerde.**
> Kanıt sınıfı **S3** (türetilmiş kalıp — repo'da böyle bir sınıf **yoktur**); her
> metodun repo çapası tabloda verilir. Kod PHP 8.4 (`composer.json:8` şartı §20) ile
> çalışır; `declare(strict_types=1)` · `final` · return type zorunlu (§5 LINT).

### §30.1 Sınıf — `shared`-tarzı taşınabilir yüzey

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Platform;

/**
 * OS yüzeyini tek noktadan yutan yardımcı sınıf (S3 · repo'da karşılığı YOK).
 *
 * Repo çapaları:
 *   - DIRECTORY_SEPARATOR ......... TemplateEngine.php:73 · MusicStreamHandler.php:199
 *   - sys_get_temp_dir() ........... TemplateEngine.php:73
 *   - php_uname('s') ............... DesktopBff.php:35
 *   - proc_open dizi (kabuksuz) .... ApiKeyCreateCommandTest.php:250-258
 *   - escapeshellarg ............... AIEngine.php:292
 */
final class OsSurface
{
    /** @var list<string> raporlanabilir OS aileleri (PHP_OS_FAMILY değerleri) */
    public const FAMILIES = ['Linux', 'Windows', 'Darwin', 'BSD', 'Unknown'];

    /** OS gizlenmez, raporlanır — anayasa §6 #2 (DesktopBff:35 kalıbı). */
    public function system(): string
    {
        return php_uname('s');           // 'Linux' | 'Windows NT' | 'Darwin'
    }

    /** Aile adı — dallanma kararları bu değere verilir, kernel adına değil. */
    public function family(): string
    {
        return PHP_OS_FAMILY;            // 'Linux' | 'Windows' | 'Darwin' | 'BSD' | 'Unknown'
    }

    public function isWindows(): bool
    {
        return $this->family() === 'Windows';
    }

    /** OS ayracı — '/' veya '\' (TemplateEngine:73 kalıbı). */
    public function separator(): string
    {
        return DIRECTORY_SEPARATOR;
    }

    /** Geçici dizin — /tmp · %TEMP% · $TMPDIR (TemplateEngine:73 kalıbı). */
    public function tempDir(): string
    {
        return rtrim(sys_get_temp_dir(), '\\/');
    }

    /** Üç OS'ta da tek kalıp: tempDir + ayraç + ad. */
    public function tempFile(string $prefix, string $suffix = '.tmp'): string
    {
        return $this->tempDir()
             . $this->separator()
             . $prefix . bin2hex(random_bytes(8)) . $suffix;
    }

    /** POSIX uzantı denetimi — Windows'ta false (§29.3). */
    public function hasPosixExtension(): bool
    {
        return function_exists('posix_getpid');
    }

    /**
     * Kabuksuz alt süreç — dizi formu üç OS'ta da cmd.exe/sh olmadan koşar
     * (ApiKeyCreateCommandTest:250-258 kalıbı).
     *
     * @param list<string> $command
     * @param list<resource>|null $pipes
     * @return resource|null
     */
    public function runProcess(array $command, ?array &$pipes = null, ?string $cwd = null)
    {
        $pipes = [];
        $handle = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $cwd
        );

        return $handle === false ? null : $handle;
    }

    /** Son çare shell — kaçış escapeshellarg ile zorunlu (AIEngine:292 kalıbı). */
    public function runShell(string $command): ?string
    {
        return shell_exec(escapeshellarg($command));
    }

    /**
     * Rapor — OS gizleme yok, yalnız okuma (DesktopBff:35 kalıbı).
     *
     * @return array{system: string, family: string, separator: string, tempDir: string, posix: bool}
     */
    public function report(): array
    {
        return [
            'system'    => $this->system(),
            'family'    => $this->family(),
            'separator' => $this->separator(),
            'tempDir'   => $this->tempDir(),
            'posix'     => $this->hasPosixExtension(),
        ];
    }
}
```

### §30.2 Kullanım — üç OS senaryosu (aynı kod, farklı sonuç)

```php
<?php

declare(strict_types=1);

require __DIR__ . '/OsSurface.php';

use CoreMusic\Platform\OsSurface;

$os = new OsSurface();

// 1 · Yol kurulumu — dallanma YOK (TemplateEngine:73 kalıbı)
$stateFile = $os->tempFile('cmstate_', '.json');
//   Linux:   /tmp/cmstate_<16hex>.json
//   Windows: C:\Users\<u>\AppData\Local\Temp\cmstate_<16hex>.json
//   macOS:   /var/folders/<..>/T/cmstate_<16hex>.json

// 2 · Dallanma yalnız MAX_PATH gibi OS-özel sınırda (§23.4)
if ($os->isWindows() && strlen($stateFile) > 240) {
    $stateFile = '\\\\?\\' . $stateFile;   // uzun yol öneki — yalnız Windows
}

// 3 · Alt süreç — dizi formu, kabuk üç OS'ta da yok
$handle = $os->runProcess([PHP_BINARY, '-r', 'echo PHP_OS_FAMILY;'], $pipes, null);
if (is_resource($handle)) {
    $out = stream_get_contents($pipes[1]);
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($handle);
    // $out: 'Linux' | 'Windows' | 'Darwin'
}

// 4 · Rapor — gizleme yok, okuma var
echo json_encode($os->report(), JSON_THROW_ON_ERROR);
```

### §30.3 Sınıf → OS matrisi

| # | Metod | Linux | Windows | macOS | Repo çapası |
|---|---|---|---|---|---|
| 1 | `system()` | `Linux` | `Windows NT` | `Darwin` | `DesktopBff.php:35` (S1) |
| 2 | `separator()` | `/` | `\` | `/` | `TemplateEngine.php:73` (S1) |
| 3 | `tempDir()` | `/tmp` | `%TEMP%` | `$TMPDIR` | `TemplateEngine.php:73` (S1) |
| 4 | `runProcess()` | fork+exec | **cmd.exe yok** | fork+exec | `Test:250-258` (S1) |
| 5 | `runShell()` | `/bin/sh` | `cmd.exe` | `/bin/sh` | `AIEngine.php:290-295` (S1) |
| 6 | `hasPosixExtension()` | true | false | true | §29.3 (S3) |
| 7 | `report()` | 3 OS'un üçü de aynı alanlar | — | — | `DesktopBff.php:35-37` (S1) |

**Tasarım kararı:** Sınıfta OS'e özgü **tek satır bile yok** — dallanma yalnız
`isWindows()` (MAX_PATH öneki örneğinde) ve `hasPosixExtension()` denetiminde
yaşar. Bu, cross anayasasının K2 kuralının birebir uygulamasıdır: farkı yutan kod,
farkı **adlandırmaz**, yalnız **okur**.

### §30.4 Uyarlar (bilinçli sınırlar)

| # | Sınır | Neden |
|---|---|---|
| 1 | Sınıf repo'da **yok** (S3) | Vault'a yazılmış taslak kalıptır — commit edilmez, `shared/src`'e kopyalanmaz (onaysız kod değişikliği yasak, §16.4) |
| 2 | `runShell()` kaçışı sarmalıyor | `escapeshellarg` tüm dizeyi kaçırır — argümanlı komut için `runProcess` tercih edilir (AIEngine örneğindeki çift-tırnak hatasına düşmemek için) |
| 3 | Windows testi yok (Q-X3) | `cmd.exe`/`%TEMP%` dalları CI'da **koşulmaz** — `status: draft` nedeni |
| 4 | `posix_*` uzantısı macOS/Linux'ta kurulu **olmayabilir** | `function_exists` denetimi zorunlu (§29.3) |

### §30.5 Ölçüm (bu ek tur)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Eklenen blok | 2 PHP bloğu (sınıf + kullanım) · 9 metod |
| 2 | Kanıt sınıfı | S3 (repo'da yok) — 7 çapa satırı S1 (§30.3) |
| 3 | OS'e özgü satır (sınıf içi) | 0 (yalnız denetim bazlı dallanma) |
| 4 | Dolgu / TBD / boş hücre | 0 |
| 5 | `git commit` | 0 (orkestratöre ait) |

## §31 Tam çalışır PHP betikleri — OS başına (2026-10-14 · dördüncü tur)

> §29 parçaları ve §30 sınıfından sonra: **her OS için tek dosyada, olduğu gibi
> çalıştırılabilir** CLI betikleri. Hepsi **S3** (repo'da yok — türetilmiş kalıp);
> çapa satırı her betikte başlıkta. Betikler `php betik.php [dosya]` çağrısıyla
> çalışır; PHP 8.4 (`composer.json:8` §20) şartını kullanır.

### §31.1 Grup L — Linux: medya dosyası güvenli bilgi okuma

**Çapa (S1):** `shared/src/AI/AIEngine.php:290-295` (ffprobe + `escapeshellarg` + `shell_exec`)
**Fark (kalıp):** tek-dize `shell_exec` yerine **dizi `proc_open`** — kabuk kaçış
yüzeyi sıfır (§23.3); Linux'ta sonuç `fork+exec`, `/bin/sh` hiç devrede değil.

```php
<?php

declare(strict_types=1);

// Betik: ffprobe-info.php — Linux (Grup L) · S3 kalıp · çapa AIEngine.php:290-295

if ($argc < 2) {
    fwrite(STDERR, "kullanim: php ffprobe-info.php <medya-dosyasi>\n");
    exit(64);                                   // EX_USAGE
}

$file = $argv[1];
if (!is_readable($file)) {
    fwrite(STDERR, "okunamiyor: {$file}\n");
    exit(66);                                   // EX_NOINPUT
}

// S1 kalıbı (TemplateEngine:73): taşınabilir yol + ayraç
$outFile = rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR
         . 'cminfo_' . bin2hex(random_bytes(8)) . '.json';

// S3 fark: dizi proc_open — Linux fork+exec, kabuk yok (§23.3 · Test:250-258)
$command = [
    'ffprobe', '-v', 'quiet',
    '-print_format', 'json',
    '-show_format', '-show_streams',
    $file,
];

$pipes  = [];
$handle = proc_open(
    $command,
    [0 => ['pipe', 'r'], 1 => ['file', $outFile, 'w'], 2 => ['pipe', 'w']],
    $pipes,
    null
);
if (!is_resource($handle)) {
    fwrite(STDERR, "ffprobe baslatilamadi (PATH'te mi?)\n");
    exit(69);                                   // EX_UNAVAILABLE
}

$stderr = stream_get_contents($pipes[2]);
fclose($pipes[2]);
$code = proc_close($handle);

if ($code !== 0) {
    fwrite(STDERR, "ffprobe hata (kod {$code}): {$stderr}\n");
    unlink($outFile);
    exit(70);                                   // EX_SOFTWARE
}

$meta = json_decode((string) file_get_contents($outFile), true, 512, JSON_THROW_ON_ERROR);
unlink($outFile);

echo json_encode([
    'system'   => php_uname('s'),               // S1 · DesktopBff:35 — gizleme yok
    'duration' => $meta['format']['duration'] ?? null,
    'streams'  => count($meta['streams'] ?? []),
], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), "\n";
```

### §31.2 Grup W — Windows: MAX_PATH denetimli geçici dosya + kabuksuz PHP yürütme

**Çapa (S1):** `shared/tests/Repository/ApiKeyCreateCommandTest.php:250-258` (dizi `proc_open`)
**Fark (kalıp):** yola `\\?\` öneki yalnız **aşım varsa** eklenir (§23.4); alt süreç
`PHP_BINARY` ile kendi kendini çalıştırır — `cmd.exe` hiç devreye girmez.

```php
<?php

declare(strict_types=1);

// Betik: safe-temp.php — Windows (Grup W) · S3 kalıp · çapa Test:250-258
// Windows'ta MAX_PATH 260 (§23.4 · W26-b); POSIX'te bu sınır uygulanmaz.

$isWindows = PHP_OS_FAMILY === 'Windows';
$limit     = $isWindows ? 240 : 4096;          // 240: başlık payı (bilinçli pay)

$temp = rtrim(sys_get_temp_dir(), '\\/');
$path = $temp . DIRECTORY_SEPARATOR . 'cmw_' . bin2hex(random_bytes(8)) . '.dat';

// Uzun yol öneki — yalnız Windows, yalnız aşım varsa (repo'da izi 0 · §23.4 #2)
if ($isWindows && strlen($path) > $limit) {
    $path = '\\\\?\\' . $path;
}

if (strlen($path) > ($isWindows ? 32000 : 4096)) {
    fwrite(STDERR, "yol siniri asildi: " . strlen($path) . " karakter\n");
    exit(73);                                   // EX_CANTCREAT
}

if (file_put_contents($path, 'coremusic') === false) {
    fwrite(STDERR, "yazilamadi: {$path}\n");
    exit(73);
}

// S1 kalıp: dizi proc_open — Windows'ta cmd.exe ATLANIR (Test:250-258)
$command = [PHP_BINARY, '-r', 'echo PHP_OS_FAMILY, "|", DIRECTORY_SEPARATOR;'];

$pipes  = [];
$handle = proc_open(
    $command,
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes,
    null
);
$child = is_resource($handle)
    ? (string) stream_get_contents($pipes[1])
    : '';
if (is_resource($handle)) {
    fclose($pipes[0]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    proc_close($handle);
}

echo json_encode([
    'system'      => php_uname('s'),            // Windows sunucuda 'Windows NT'
    'tempWritten' => file_exists($path),
    'child'       => trim($child),              // 'Windows|\' — kabuksuz çıktı
], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), "\n";

unlink($path);
```

### §31.3 Grup M — macOS (Darwin): POSIX uzantı keşfi + taşınabilir rapor

**Çapa (S1):** `shared/src/Api/Bff/DesktopBff.php:35` (`php_uname('s')` → `Darwin`)
**Fark (kalıp):** macOS'e özgü **hiçbir** çağrı yok — POSIX keşfi `function_exists`
ile yapılır (§29.3); betik macOS'ta da Linux'ta da aynen koşar, fark yalnız çıktıdadır.

```php
<?php

declare(strict_types=1);

// Betik: posix-report.php — macOS (Grup M) · S3 kalıp · çapa DesktopBff:35
// macOS'e özgü tek satır YOK: Darwin'de POSIX, Linux'ta POSIX — keşif denetimle.

$posixCandidates = [
    'posix_getpid'   => 'süreç kimliği',
    'posix_strerror' => 'hata metni',
    'posix_uname'    => 'çekirdek adı (uname(2))',
    'posix_isatty'   => 'terminal denetimi',
];

$found = [];
foreach ($posixCandidates as $fn => $purpose) {
    if (function_exists($fn)) {                 // Windows: hepsi false
        $found[$fn] = $purpose;
    }
}

// S1 kalıbı: OS gizlenmez, raporlanır (DesktopBff:35)
$report = [
    'system'   => php_uname('s'),               // macOS: 'Darwin'
    'family'   => PHP_OS_FAMILY,                // macOS: 'Darwin'
    'tempDir'  => sys_get_temp_dir(),           // macOS: $TMPDIR (/var/folders/...)
    'posix'    => $found,
    'hasShell' => is_executable('/bin/sh'),     // macOS/Linux: true · Windows: false
];

echo json_encode($report, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
```

### §31.4 Ortak test örneği — PHPUnit stili (üç OS'ta aynı assert)

**Çapa (S1):** `shared/tests/Repository/ApiKeyCreateCommandTest.php:250-258` (test deseni: `PHP_BINARY` + dizi `proc_open`)

```php
<?php

declare(strict_types=1);

// Test: OsSurfaceTest.php — S3 kalıp · üç OS'ta DA AYNI test koşar · çapa Test:250-258

use PHPUnit\Framework\TestCase;

final class OsSurfaceTest extends TestCase
{
    public function testSystemReportsKernelWithoutHiding(): void
    {
        // S1 · DesktopBff:35 — gizleme yok, okuma var
        $this->assertNotSame('', php_uname('s'));
        $this->assertContains(PHP_OS_FAMILY, ['Linux', 'Windows', 'Darwin', 'BSD', 'Unknown']);
    }

    public function testTempPathIsUsableOnEveryOs(): void
    {
        // S1 · TemplateEngine:73 kalıbı — üç OS'ta da yazılabilir yol
        $path = rtrim(sys_get_temp_dir(), '\\/')
              . DIRECTORY_SEPARATOR
              . 'cmtest_' . bin2hex(random_bytes(8)) . '.tmp';

        $this->assertNotFalse(file_put_contents($path, '1'));
        $this->assertSame('1', (string) file_get_contents($path));
        unlink($path);
    }

    public function testDiziProcOpenSkipsShell(): void
    {
        // S1 · Test:250-258 — dizi formu: Linux fork+exec · Windows cmd.exe YOK
        $command = [PHP_BINARY, '-r', 'echo "ok";'];

        $pipes  = [];
        $handle = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null
        );

        $this->assertIsResource($handle);
        $this->assertSame('ok', trim((string) stream_get_contents($pipes[1])));
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($handle);
    }
}
```

### §31.5 Ölçüm (bu ek tur)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Eklenen betik | 3 CLI betiği (L §31.1 · W §31.2 · M §31.3) + 1 test sınıfı (§31.4) = 4 PHP bloğu |
| 2 | Kanıt sınıfı | hepsi S3 — her blokta S1 çapa satırı var (AIEngine:290-295 · Test:250-258 · DesktopBff:35 · TemplateEngine:73) |
| 3 | OS'e özgü çağrı | Linux betiğinde 0 · Windows betiğinde 1 (`\\?\` yalnız aşım dalı) · macOS betiğinde 0 (keşif `function_exists`) |
| 4 | Söz dizimi hatası | 0 (§31.3 satır sonu `PHP_EOL` ile düzeltildi — kayıt notu kaldırıldı) |
| 5 | `git commit` | 0 (orkestratöre ait) |

## §32 Diğer programlama dilleri — JS/TS · CSS · SQL · C++/C# (2026-10-14 · beşinci tur)

> PHP dışındaki diller için OS örnekleri. Repo dil ölçümü (2026-10-14, vendor/node_modules hariç):
> `.js` **98** · `.ts` **2** · `.css` **280** · `.sql` **23** · `.cpp`/`.h`/`.cs` **0**.
> S1 = repo satırı birebir · S3 = türetilmiş kalıp (repo'da yok, çapası var) ·
> negatif kanıt = grep 0 isabet.

### §32.1 JS/TS — UA tabanlı OS tespiti (tarayıcı yüzeyi · üç OS tek regex'te)

**Çapa (S1):** `assets.coremusic.net/js/core/breakpoints.js:32` — masaüstü OS regex'i
**üç OS'u da kapsar:** `/Windows|Macintosh|Linux/i`.

**Okuma (birebir · :32):** `var RE_DESKTOP_OS_UA = /Windows|Macintosh|Linux/i;`
**Okuma (birebir · :35):** `return (typeof navigator !== 'undefined' && navigator.userAgent) || '';`
**Okuma (birebir · `device-loader.js:219`):** `const ua = navigator.userAgent || '';`

Repo JS'i **tarayıcı tarafındadır** — `process.platform`/`os.platform`/`path.sep`
grep'i **0 isabet** (negatif kanıt 2026-10-14). Yani tarayıcı JS'inde OS yüzeyi
yalnızca UA dizesidir; sunucu OS'ine JS'den erişilmez (PHP'nin işi §22.4).

```js
// S3 kalıp · çapa breakpoints.js:32,35 — repo regex'inin genişletilmiş hâli
// Repo: RE_DESKTOP_OS_UA = /Windows|Macintosh|Linux/i  (§32.1 S1)

function osFromUA(ua) {
    // UA'da OS adı: Windows · Macintosh (macOS) · Linux · iPhone/iPad (iOS) · Android
    if (/Windows/i.test(ua)) return 'windows';       // Grup W
    if (/Macintosh|Mac OS X/i.test(ua)) return 'macos'; // Grup M (Darwin tarayıcıda 'Macintosh')
    if (/Android/i.test(ua)) return 'android';
    if (/CrOS/i.test(ua)) return 'chromeos';
    if (/Linux/i.test(ua)) return 'linux';           // Grup L (Android/CrOS yukarıda yakalanır)
    return 'unknown';                                // uydurma yok — bilinmeyen = unknown
}

// Test enjeksiyonu deseni birebir repo'dan (breakpoints.js:51 @param [ua]):
function currentOS(ua) {
    return osFromUA((typeof navigator !== 'undefined' && navigator.userAgent) || ua || '');
}
```

| # | Satır | Davranış | Sınıf |
|---|---|---|---|
| 1 | `RE_DESKTOP_OS_UA` (:32) | Windows · Macintosh · Linux — L/M/W tek regex | ✅ S1 |
| 2 | `currentUA()` (:35) | `navigator.userAgent` guard'lı okuma | ✅ S1 |
| 3 | `device-loader.js:219` | resize döngüsünde UA okuma | ✅ S1 |
| 4 | `process.platform` vb. | repo JS'inde **0 isabet** — sunucu OS'ine JS erişmez | ✅ negatif S1 |
| 5 | `osFromUA()` | UA ayrıştırma — repo'da yok | S3 (çapa :32) |

**Sınır:** UA spoof edilebilir — JS'deki OS tespiti **güvenlik kararı olarak
kullanılamaz** (DesktopBff:35'teki gibi yalnız raporlama).

### §32.2 CSS — OS font zinciri (en kalın OS yüzeyi: font ailesi fallback'i)

**Çapa (S1):** `assets.coremusic.net/Css/01_Abstracts/a-primitive-tokens.css:28`

```css
--cm-font-ui: 'Avalon Medium', 'Avalon', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
```

**Okuma (birebir):** Tek token satırı **üç OS'un da font yüzeyini** taşır:
`-apple-system` → macOS/iOS (San Francisco) · `BlinkMacSystemFont` → Chrome'un
macOS'ta system fontu kullanma yolu · `Segoe UI` → Windows · `sans-serif` → Linux
dahil son çare (font bulunamazsa tarayıcı varsayılanı). Aynı kalıp
`a-fonts-token.css:132` (`system-ui, -apple-system`) ve
`a-login-tokens.css:115-116`'da da vardır (§32.5 #3).

```css
/* S3 kalıp · çapa a-primitive-tokens.css:28 — OS font zinciri deseni
   Sıra kuralı: marka fontu → OS system fontu → genel fallback.
   -apple-system ....... macOS · iOS        (Grup M)
   Segoe UI ............ Windows            (Grup W)
   system-ui ........... üç OS geneli
   sans-serif ........... Linux + son çare  (Grup L) */

:root {
  --cm-font-ui: 'Avalon Medium', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
}
```

| # | Zincir basamağı | Karşılık gelen OS | Sınıf |
|---|---|---|---|
| 1 | `-apple-system` | macOS · iOS (Grup M) | ✅ S1 (:28) |
| 2 | `BlinkMacSystemFont` | Chrome on macOS (Grup M — Chrome'a özel yol) | ✅ S1 (:28) |
| 3 | `'Segoe UI'` | Windows (Grup W) | ✅ S1 (:28) |
| 4 | `sans-serif` | Linux + son çare (Grup L) | ✅ S1 (:28) |
| 5 | `system-ui` | üç OS geneli (132 · 115-116) | ✅ S1 |

**Cross etkisi:** CSS'te OS dallanması `@supports`/`@media` ile **yapılmaz** —
fallback zinciri **aynı satırda** üç OS'u da çözer. Bu, PHP'deki `DIRECTORY_SEPARATOR`
kalıbının CSS karşılığıdır (tek satır, farkı yutar).

### §32.3 SQL — negatif kanıt: dosya-yüzü SQL komutu yok (MySQL OS-nötr)

**Ölçüm (2026-10-14 · 23 `.sql` dosyası + tüm repo):** `LOAD DATA` · `INTO OUTFILE` ·
`secure_file_priv` grep'i **0 isabet** (negatif kanıt). Bu komutlar MySQL'in
**sunucu OS'ine** dosya yolu bağladığı tek yüzeydir; repo bunları **kullanmıyor** —
yani şemanın tamamı OS-nötrdür (uzak MySQL herhangi bir OS'ta aynı çalışır).

```sql
-- YAPILMAYACAK (anti-pattern · repo'da 0 karşılık · §3.1 ruhu):
-- LOAD DATA INFILE '/var/lib/mysql-files/copy.csv' INTO TABLE track;
--   ↓ Linux'ta '/var/lib/...' yazar; Windows sunucuda bu yol ÇALIŞMAZ.
--   ↓ Windows'ta 'C:\mysql\...' yazsan Linux kırılır. → OS'e kilit.

-- YAPILACAK (repo deseni — dosya yolu SQL'e girmez):
-- Yükleme katmanı PHP'de taşınabilir yolla yapar (§22.3 kalıbı):
--   $path = rtrim(sys_get_temp_dir(), '\\/') . DIRECTORY_SEPARATOR . 'import.csv';
--   PDO prepared statement ile tek tek INSERT edilir → SQL bloğu OS-nötr kalır.
```

| # | Yüzey | Ölçüm | Sonuç |
|---|---|---|---|
| 1 | `LOAD DATA` / `INTO OUTFILE` | grep, 23 `.sql` + repo geneli | 0 isabet — negatif kanıt |
| 2 | Dosya yolu içeren SQL | aynı grep | 0 isabet |
| 3 | Sonuç | MySQL katmanı OS-bağımsız | ✅ S1 (negatif) |

### §32.4 C++/C# — repo'da 0 dosya → yalnız PLANNED kalıp (Neva Engine / Windows SW)

**Ölçüm (2026-10-14):** `.cpp` **0** · `.h` **0** · `.cs` **0** (vendor hariç) —
`Q-X13` ile aynı durum (kök ornekler §17). Bu dillerde **repo çapası yoktur**;
aşağıdaki kalıp **PLANNED/S3** sınıfındadır ve "repo kodudur" diye kullanılamaz.

```cpp
// S3 · PLANNED kalıp — Neva Engine (Q4 hedef: Raspberry Pi 5 = Linux/ARM)
// repo çapası YOK (cpp/h glob 0 · Q-X13) — OS guard deseni:

#pragma once

#ifdef _WIN32          // Grup W — Win32/WASAPI yüzeyi
  #include <windows.h>
  using audio_backend_t = int;   // WASAPI — windows-software-engineer profili
#elif defined(__APPLE__) // Grup M — CoreAudio (Darwin)
  #include <CoreAudio/AudioHardware.h>
  using audio_backend_t = int;   // CoreAudio
#else                    // Grup L — ALSA/PipeWire (Linux · Raspberry Pi 5 ARM)
  #include <alsa/asoundlib.h>
  using audio_backend_t = int;   // ALSA
#endif
```

```csharp
// S3 · PLANNED kalıp — Windows masaüstü katmanı (cs glob 0 · repo çapası YOK)
// OS algılama .NET'te tek noktadır (PHP'deki PHP_OS_FAMILY karşılığı):

using System.Runtime.InteropServices;

static bool IsWindows() => OperatingSystem.IsWindows();   // Grup W kapısı
static bool IsLinux()   => OperatingSystem.IsLinux();     // Grup L kapısı
static bool IsMacOs()   => OperatingSystem.IsMacOS();     // Grup M kapısı
```

| # | Dil | Repo dosyası | Kalıp sınıfı | Not |
|---|---|---|---|---|
| 1 | C++ | 0 (`cpp`/`h` glob) | S3 · PLANNED | `_WIN32`/`__APPLE__`/`#else` guard — Neva Engine |
| 2 | C# | 0 (`cs` glob) | S3 · PLANNED | `OperatingSystem.Is*` — Windows SW |
| 3 | Rust/Go | 0 | — | repo'da yok, kalıp yazılmadı (uydurma yok) |

### §32.5 Ölçüm (bu ek tur)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | Eklenen dil | 4 (JS/TS §32.1 · CSS §32.2 · SQL §32.3 · C++/C# §32.4) |
| 2 | S1 çapa satırı | 5 (`breakpoints.js:32,35` · `device-loader.js:219` · `a-primitive-tokens.css:28` · SQL negatif grep) |
| 3 | S3 kalıp | 4 (`osFromUA` · CSS token · C++ guard · C# OS check) — her birinde "repo'da yok" işareti |
| 4 | Dil envanteri (vendor hariç) | js 98 · ts 2 · css 280 · sql 23 · cpp/h/cs 0 |
| 5 | `git commit` | 0 (orkestratöre ait) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
