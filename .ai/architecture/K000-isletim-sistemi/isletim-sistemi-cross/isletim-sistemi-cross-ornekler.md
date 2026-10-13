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
validation: "2026-10-13 · repo satırları Select-Object -Index (N-1) ile okundu · 4 URL HEAD 200 · envanter kesişimi 0 · yeniden yazım (eski şema: parent/title/type/date → v2 §4.2 + group)"
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

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
