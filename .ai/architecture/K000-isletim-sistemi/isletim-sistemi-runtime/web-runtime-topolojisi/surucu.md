---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi
evidence:
  - https://httpd.apache.org/docs/2.4/howto/htaccess.html
  - https://www.php.net/manual/en/install.fpm.php
  - https://learn.microsoft.com/en-us/windows/win32/procthread/creating-processes
  - home.coremusic.net/.htaccess:1-37
  - home.coremusic.net/web.config:1-43
  - shared/config/domain.php:3-19
  - https://nginx.org/en/docs/control.html
  - https://nginx.org/en/docs/beginners_guide.html
validation: "2026-10-13 · 3 URL HTTP 200 (howto/htaccess · install.fpm bu tur envantere eklendi · creating-processes bu ağacın kök index.md'inden devralındı) · repo satırları Read ile birebir · içerik 380-460 satır"
open_questions:
  - "R1: Hedef runtime topolojisi kesin değil — Apache (mod_php/FPM) mı, IIS mi, ikisi de mi? [UNKNOWN]"
  - "R3: PHP Apache üzerinde mod_php mi, FPM mi çalışıyor? Repo'da FPM pool config'i yok. [⚠️ VERIFICATION REQUIRED]"
  - "R7: Sunucu MPM'i veya FastCGI havuzu hangi kapasitede? [⚠️ VERIFICATION REQUIRED]"
group: mimari
---

# web-runtime-topolojisi — Sürücü (Kademe-3)

## §0 ELI15

Bu dosya, web sunucusu topolojisinin **repo'daki yürütme yüzeyini** adım adım
gösterir: `.htaccess` üç katmanlı rewrite zincirini, `web.config` beş kuralını ve
`domain.php` 7 alt alan eşlemesini yürütür. Aynı zamanda her yapılandırmanın
**sunucu tarafı ön koşulunu** (AllowOverride, URL Rewrite modülü, FPM havuzu,
app-pool) ve bu koşullar sağlanmazsa doğacak hata modlarını kaydeder.

## §1 Uygulama Katmanı — topoloji kullanım haritası

| Yüzey | Dosya | Sunucu ile ilişkisi | Durum |
|---|---|---|---|
| Apache yapılandırması | `home.coremusic.net/.htaccess` (37 satır) | php_flag + headers + FilesMatch + rewrite | ✅ IMPLEMENTED |
| IIS yapılandırması | `home.coremusic.net/web.config` (43 satır) | customHeaders + 5 rewrite kuralı | ✅ IMPLEMENTED |
| Alan haritası | `shared/config/domain.php` (19 satır) | 7 subdomain + `home` → 81 | ✅ IMPLEMENTED |
| Uygulama kapısı | `home.coremusic.net/index.php:10-29` | front controller → boot | ✅ IMPLEMENTED |
| Kernel kurulumu | `home.coremusic.net/config/bootstrap.php:97-106` | route dosyası + `handle` | ✅ IMPLEMENTED |
| FPM havuzu | — | repo'da `www.conf` yok | ❌ YOK (R3 [⚠️]) |
| nginx betiği | — | repo'da yok | ❌ YOK (çapraz hedef) |
| MPM seçimi | — | sunucu kurulumunda, depoda değil | ❌ YOK (R7 [⚠️]) |

> Harita kuralı: `✅` yalnız disk satırı okunarak yazıldı; `❌` repo taramasının
> negatif kanıtıdır, tahmin değildir.

### §1.1 Yüzey başına notlar

| Yüzey | Kural | Dikkat |
|---|---|---|
| `.htaccess` | Apache okur; `AllowOverride` kapalıysa **tümü** etkisiz | howto/htaccess (§7 № 1) |
| `web.config` | IIS okur; URL Rewrite modülü kurulu değilse kurallar çalışmaz | `system.webServer` (kök `index.md`) |
| `domain.php` | Uygulama katmanı — sunucu vhost'unu **tanimlamaz**, eşler | Domain listesi onaysız değişmez |
| Front controller | İki yapılandırma da aynı kapıya varır | Çift hedef drift riski (§3 madde 2) |
| PHP devri | mod_php / FPM / FastCGI handler — repo'da ayrım yok | R3 |

## §2 Akış — katmanlar

### §2.1 Akış 1 — Apache rewrite zinciri (.htaccess:26-37)

| # | Kural | Koşul | Sonuç |
|---|---|---|---|
| 1 | Motor | `RewriteEngine On` + `RewriteBase /` | rewrite aktif |
| 2 | Gerçek dosya/dizin | `-f [OR]` / `-d` → `^ - [L]` | PHP'ye hiç girmez |
| 3 | Statik uzantı | `css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot` → `- [L]` | doğrudan servis |
| 4 | Yasaklı dizin | `^(config|include|pages)` → `[F,L]` | 403 |
| 5 | Front controller | `^(.*)$ index.php [QSA,L]` | her şey `index.php`'ye |

### §2.2 Akış 2 — IIS rewrite zinciri (web.config:14-41)

| # | Kural | Eylem | Sonuç |
|---|---|---|---|
| 1 | Static files | `action None` | statik pas |
| 2 | Block config/include/pages | `CustomResponse 403` | yasaklı dizin |
| 3 | Block sensitive files | `CustomResponse 403` | env/log/sql vb. |
| 4 | SPA Router | `IsFile`/`IsDirectory` → `None` | gerçek dosya pas |
| 5 | Rewrite to index.php | `Rewrite` + `appendQueryString` | front controller |

### §2.3 Akış 3 — PHP-FPM havuzu zinciri (kavram · repo negatifi)

| # | Adım | Beklenen | Repo durumu |
|---|---|---|---|
| 1 | FPM kurulumu | `install.fpm` dokümanına göre kurulum | sunucu tarafı — depoda değil |
| 2 | Havuz yapılandırması | process/parçacık sayısı, dinleyici soket | `www.conf` **yok** (R3) |
| 3 | Sunucu devri | mod_rewrite `ProxyPassMatch` **ya da** nginx `fastcgi_pass` | betik yok |
| 4 | Boot | PHP isteği `baslangic-ve-bootstrap` boot'una devreder | komşu dal |
| 5 | Sonuç | Kapasite (R7) hesaplanamaz | [⚠️ VERIFICATION REQUIRED] |

### §2.4 Akış 4 — IIS süreç zinciri (app-pool → worker process)

| # | Adım | Ne olur | Kaynak |
|---|---|---|---|
| 1 | İstek | HTTP.sys dinleyicisine ulaşır | IIS dokümantasyonu (kök `index.md`) |
| 2 | App-pool | İstek ait olduğu app-pool'a yönlendirilir | §2 M2 (anayasa) |
| 3 | Worker process | Windows süreç oluşturma API'si ile doğan process işler | creating-processW (§7 № 3) |
| 4 | `web.config` | `system.webServer` blokları uygulanır | §2.2 |
| 5 | PHP | FastCGI handler üzerinden PHP'ye devir | R3 |

### §2.5 Akış 5 — Subdomain eşleme (domain.php:3-19)

| # | Adım | Ne olur | Repo sargısı |
|---|---|---|---|
| 1 | Host başlığı | `X.coremusic.net` eşleşmesi | `:5-13` 7 subdomain |
| 2 | Port | `home` → 81 istisnası | `:15-17` |
| 3 | Dizin | Eşleşen uygulama dizinine gidilir | `music`/`admin` için dizin **yok** (R4) |
| 4 | Vhost | Sunucu tarafında name-based vhost karşılığı | kök `-ornekler.md` §21 |
| 5 | Kapı | `index.php` → boot zinciri | `index.php:10-29` |

## §3 Hata Modları

| # | Hata | Belirti | Kök neden | Sonuç |
|---|---|---|---|---|
| 1 | AllowOverride kapalı | `.htaccess` kuralları etkisiz; dizin listesi açık | sunucu ayarı | 500 ya da dizin sızıntısı |
| 2 | Çift hedef drift | Apache'de kural var, IIS'te yok (ya da tersi) | iki dosya bağımsız değişir | güvenlik katmanı asimetrik |
| 3 | URL Rewrite modülü yok | `web.config` kuralları çalışmaz | IIS modülü kurulu değil | front controller kırılır |
| 4 | FPM havuzu dolu | istekler kuyrukta / 502 | kapasite (R7) | [⚠️ VERIFICATION REQUIRED] |
| 5 | App-pool kimliği hatalı | 401/500 | havuz identitiy ayarı | `K018-devops` |
| 6 | Dizin boşluğu | 404 | `music`/`admin` dizini yok (R4) | runtime [UNKNOWN] |
| 7 | Port çakışması | `home` 81'de değil | vhost portu eşleşmiyor | `domain.php:16` kanıtı |

### §3.1 Teşhis — hatadan kata (şeka değil, şema)

| Kat | Soru | Araç |
|---|---|---|
| Sunucu | Hangi ürün aktif? (R1) | sunucu başlığı/log |
| Yapılandırma | `.htaccess` okunuyor mu? | AllowOverride denemesi |
| Modül | URL Rewrite / mod_rewrite kurulu mu? | sunucu modül listesi |
| Devir | PHP hangi SAPI'de? (R3) | `phpinfo` yüzeyi — repo'da yok |
| Kapasite | Havuz kaç process? (R7) | sunucu raporu |
| Uygulama | Kapı doğru mu? | `index.php:10-29` |

## §4 Test Stratejisi

| # | Katman | Durum | Kanıt |
|---|---|---|---|
| 1 | Rewrite davranışı (tarayıcı) | bu dalda incelenmedi | ⚠️ kapsam dışı (`isletim-sistemi-testing`) |
| 2 | 403 filtresi | denetlenebilir | `.htaccess:18-24` + `web.config:20-27` |
| 3 | Front controller | denetlenebilir | `.htaccess:36` + `web.config:36-39` |
| 4 | Subdomain eşlemesi | uygulama katmanı | `domain.php:7-13` |
| 5 | FPM havuzu | yok | R3/R7 |

### §4.1 Prosedür — D-1..D-3 (bu dalın yürütüm notları)

| # | Senaryo | Adım | Beklenen |
|---|---|---|---|
| D-1 | Statik pas | `/style.css` iste | rewrite'e girmeden servis |
| D-2 | Yasaklı dizin | `/config/` iste | 403 (iki hedefte de) |
| D-3 | Front controller | `/oyun/12` iste | `index.php`'ye rewrite |

## §5 Ölçüm

| # | Ne ölçülür | Araç / yöntem | Eşik | Durum |
|---|---|---|---|---|
| 1 | Rewrite katmanı sayısı | `.htaccess` + `web.config` | 2 (çift hedef) | OKU (2026-10-13) |
| 2 | IIS kural sayısı | `web.config:15-40` | 5 | OKU (2026-10-13) |
| 3 | 403 kuralı | FilesMatch ×2 + CustomResponse ×2 | her iki hedefte | OKU (2026-10-13) |
| 4 | Subdomain sayısı | `domain.php` | 7 | OKU (2026-10-13) |
| 5 | Port istisnası | `domain.php:16` | 1 (`home` → 81) | OKU (2026-10-13) |
| 6 | Aktif hedef (R1) | sunucu gözlemi | — | UNKNOWN |
| 7 | Havuz kapasitesi (R7) | sunucu raporu | — | UNKNOWN |

## §6 Repo Kanıtı

### §6.1 Apache üç katmanlı zincir (.htaccess:26-37)

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

### §6.2 IIS front controller (web.config:36-39)

```xml
<rule name="Rewrite to index.php">
    <match url="^" ignoreCase="false" />
    <action type="Rewrite" url="index.php" appendQueryString="true" />
</rule>
```

### §6.3 Alan haritası (shared/config/domain.php:3-19)

```php
return [
    'primary'         => 'coremusic.net',
    'primary_port'    => 80,
    'subdomain_port'  => 80,
    'subdomains' => [
        'auth'   => 'auth.coremusic.net',
        'home'   => 'home.coremusic.net',
        'assets' => 'assets.coremusic.net',
        'music'  => 'music.coremusic.net',
        'admin'  => 'admin.coremusic.net',
        'media'  => 'media.coremusic.net',
        'api'    => 'api.coremusic.net',
    ],
    'subdomain_ports' => [
        'home' => 81,
    ],
];
```

### §6.4 Apache güvenlik katmanı (.htaccess:9-24)

```apache
<IfModule mod_headers.c>
    Header always set X-Frame-Options "DENY"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "0"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always unset Server
    Header always unset X-Powered-By
</IfModule>

<FilesMatch "\.(env|log|json|lock|md|sql|sh|bak|git|ini|htpasswd|DS_Store)$">
    Require all denied
</FilesMatch>

<FilesMatch "^(?!index\.php$)(?!debug\.php$).+\.php$">
    Require all denied
</FilesMatch>
```

## §7 Kaynakça

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | Apache `.htaccess` How To — AllowOverride ve okuma kuralları | https://httpd.apache.org/docs/2.4/howto/htaccess.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 2 | PHP-FPM kurulumu — SAPI olarak FPM, havuz kavramı | https://www.php.net/manual/en/install.fpm.php | 2026-10-13 HTTP 200 · bu ağacın `baslangic-ve-bootstrap/index.md`'sinden devralındı |
| 3 | Microsoft CreateProcessW — Windows süreç oluşturma (app-pool worker) | https://learn.microsoft.com/en-us/windows/win32/procthread/creating-processes | 2026-10-13 HTTP 200 · bu ağacın kök `index.md`'sinden devralındı |

> `mod_rewrite` · `ngx_http_fastcgi_module` · name-based vhost kaynakları bu alt-dalın
> `-ornekler.md` dosyasındadır; MPM ana sayfası kök `index.md` Web Research'ündedir.

## §8 — Giriş Noktaları Karşılaştırması (repo disk kanıtı)

| # | Yüzey | `home.coremusic.net/index.php` | `api.coremusic.net/index.php` |
|---|---|---|---|
| 1 | satır sayısı | 29 | 223 |
| 2 | router | `PageRouterKernel` (SPA) | **kullanmaz** — JSON front controller (satır 6) |
| 3 | autoload | satır 10 | satır 14 |
| 4 | constants | satır 11 | satır 39 |
| 5 | opcache temizliği | satır 14-16 (`DEBUG_MODE` + `opcache_reset`) | **yok** |
| 6 | bootstrap çağrısı | satır 23 `RuntimeBootstrap::boot(DEBUG_MODE)` | satır 44 |
| 7 | config nesneleri | satır 21 · 26 · 29 | satır 40-42 · 64-69 |
| 8 | CORS config | — (kernel'e geçer) | satır 41 `config/cors.php` |
| 9 | rota tablosu | kernel `RouteRegistry` | satır 42 `RouteTable::fromArray(require ... routes.php)` |
| 10 | uçlar | SPA rotaları | `/health` · `/api/v1/*` · diğer → 404 JSON (satır 7-9) |

## §9 — API İstek Yaşam Döngüsü (`api/index.php`)

| # | Adım | Satır | Ne olur |
|---|---|---|---|
| 1 | HTTPS tespiti | 50-53 | `$_SERVER['HTTPS']` veya `HTTP_X_FORWARDED_PROTO` |
| 2 | host/port | 55-61 | `HTTP_HOST` → `:` varsa bölünür |
| 3 | domain config | 64-66 | `DomainConfig` + `setOverrides($scheme, $host, $port)` |
| 4 | cookie güvenliği | 68 | `session.cookie_secure = $isHttps` |
| 5 | istek gövdesi | 121-127 | `server/method/uri/headers/body` birleştirilir |
| 6 | pipeline kurulumu | 147-171 | sabit sırada 8 `pipe` |
| 7 | dispatch | 173-182 | `Gateway::dispatch` |
| 8 | header yayımı | 185-187 | `header($name . ': ' . $value)` |
| 9 | preflight kısa devre | 193-197 | `halt` → gövdesiz **204** + `exit` |
| 10 | yanıt | 199-200 | `$jsonResponse($result, $status >= 400 ? $status : 200)` |
| 11 | yakalanmayan hata | 201-214 | log + **500** `SERVER_INTERNAL_ERROR` |
| 12 | rota yok | 217-223 | **404** `NOT_FOUND` |

## §10 — API Middleware Sırası (`index.php:147-171` — sabit sıra, ADR-020 §1.1-B.1)

| # | Sıra | Middleware | Satır | Not |
|---|---|---|---|---|
| 1 | 1 | `ResponseNormalizationMiddleware` | 148 | gövde normalizasyonu |
| 2 | 2 | `OriginCheckMiddleware` (closure) | 149 | **frozen sıra**: OriginCheck → Cors (B-F-02/B-F-07) |
| 3 | 3 | `CorsMiddleware` (closure) | 150 | header yayıcısı (B-F-03) |
| 4 | 4 | `RateLimitMiddleware` | 151 | `CacheRateLimiter($apiCache)` |
| 5 | 5 | `AuthenticationMiddleware` | 154-159 | session → RS256 Bearer + jti revocation |
| 6 | 6 | `CsrfMiddleware` (closure) | 164-169 | yalnız `method === 'session'` |
| 7 | 7 | `RequestValidationMiddleware` | 170 | — |
| 8 | 8 | `AuthorizationMiddleware` | 171 | — |

> **Web karşılaştırması:** `PageRouterKernel::buildDefaultMiddlewares()` (10 middleware, satır 330-341)
> ile API pipeline'ı (8 `pipe`) **farklı** sırada ve farklı üyedir; ikisi birbirinin yerine geçmez.

## §11 — Hata Sözleşmesi (JSON)

| # | Durum | Gövde | Satır |
|---|---|---|---|
| 1 | 500 | `{"error":{"code":"SERVER_INTERNAL_ERROR","message":"Sunucu hatası."}}` | 208-213 |
| 2 | 404 | `{"error":{"code":"NOT_FOUND","message":"Uç bulunamadı."}}` | 218-223 |
| 3 | 204 (preflight) | gövde boş | 193-197 |
| 4 | başarılı | `{data, meta}` — header anahtarı gövdede yok | 189-190 yorumu |
| 5 | istisna gizleme | "exception mesajı ASLA istemciye dönmez" | satır 11 (dosya başlığı) |

> Durum kodu anayasası (200/404/500 tanımları) bu ağacın `anayasa.md` §14'ündedir —
> RFC 9110 kaynakları orada tekrarlanmaz.

## §12 — Önbellek ve Varlık Topolojisi

| # | Yüzey | Kanıt | Kural |
|---|---|---|---|
| 1 | APCu devri | `api/index.php:143-145` | `apcu_enabled()` → `ApcuAdapter` / `MemoryAdapter` |
| 2 | rate-limit önbelleği | `:151` | `CacheRateLimiter($apiCache)` |
| 3 | CSS | `assets/.htaccess:20` | `max-age=3600` |
| 4 | JS | `assets/.htaccess:30` | `no-cache` (yorum gerekçesi: hash/bundler yok) |
| 5 | font | `assets/.htaccess:35` | `max-age=31536000, immutable` |
| 6 | görsel | `assets/.htaccess:38` | `max-age=604800` |
| 7 | opcache | `home/index.php:14-16` | yalnız `DEBUG_MODE` |

## §13 — Negatif Kanıt Defteri (bu tur)

| # | Aranan | Sonuç |
|---|---|---|
| 1 | FPM havuz / MPM ayar dosyası | repo'da **yok** → R7 [⚠️ VERIFICATION REQUIRED] |
| 2 | `nginx.conf` / `httpd.conf` | repo'da **yok** → sunucu config'i bu repo'da taşınmıyor |
| 3 | `.env` içeriği | okunmadı (gizli · `.gitignore`) |
| 4 | DNS/hosts kaydı | repo'da **yok** |
| 5 | `subdomain_ports` dışındaki port tanımları | `domain.php` yalnız `home => 81` |
| 6 | `assets`/`auth` için `web.config` eşdeğeri | 4 `web.config` var (api · assets · auth · home) — assets/auth `.htaccess` CORS/cache odaklı |

## §14 — Tekrarlanabilir Ölçüm

| # | Amaç | Komut | Beklenen |
|---|---|---|---|
| 1 | giriş noktası satırları | `[IO.File]::ReadAllLines('home.coremusic.net\index.php').Count` | 29 |
| 2 | API satırları | `[IO.File]::ReadAllLines('api.coremusic.net\index.php').Count` | 223 |
| 3 | bootstrap tekliği | `Get-ChildItem -Recurse -Filter index.php` + desen `RuntimeBootstrap::boot` | 3 (`home:23` · `api:44` · `auth:32`) |
| 4 | pipe sayısı | `Select-String -Path 'api.coremusic.net\index.php' -Pattern '->pipe\('` | 8 |
| 5 | varlık kuralları | `Select-String -Path 'assets.coremusic.net\.htaccess' -Pattern 'Cache-Control'` | 4 |
| 6 | dolu satır | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |

## §15 — URL Durumu (bu tur)

| # | Kaynak | Kullanım | Durum |
|---|---|---|---|
| 1 | RFC 9110 (durum kodları) | bu dosyada **tekrarlanmadı** → `anayasa.md` §14 | — |
| 2 | RFC 9112 (HTTP/1.1, 7230 obsoletes) | `anayasa.md` §15.1 | — |
| 3 | yeni URL | bu dosyaya eklenmedi | **0** |

**Eklenen yeni doğrulanmış URL sayısı: 0** — bu dosyanın kanıtı tamamen repo disk okumasıdır.

## §16 — Üçüncü Giriş Noktası: `auth.coremusic.net/index.php` (238 satır)

> §8-§15 `home` ve `api`'yi kapsıyordu; `auth` üçüncü front controller'dır ve **en karmaşık
> erken rota zincirini** taşır. `assets`'in front controller'ı **yoktur** (yalnız `.htaccess`).

### §16.1 İstek sırası (satır 62-226)

| # | Adım | Satır | Ne olur |
|---|---|---|---|
| 1 | istek ayrıştırma | 63-65 | `REQUEST_URI` → `$requestUri` · `$method` · `$pageName` |
| 2 | özel JSON uçları | 68-126 | `/health` · `/session` · `/validate-key` |
| 3 | kök yönlendirme | 129-151 | 302 (bypass / auth_key / select-gender) |
| 4 | bypass yönlendirme | 154-158 | üretim dışı & `FORCE_AUTH_BYPASS` |
| 5 | gender gate | 161-170 | `/login` → `/select-gender` |
| 6 | OAuth varsayılan | 173-183 | `client_id` yoksa 302 |
| 7 | otomatik yönlendirme | 186-187 | `AutoRedirectHandler` |
| 8 | kernel çağrısı | 213-226 | `PageRouterKernel::handle(...)` |
| 9 | hata | 227-238 | 500 JSON |

### §16.2 Pipeline öncesi kontroller (satır 75-105)

| # | Kontrol | Satır | Davranış |
|---|---|---|---|
| 1 | `OriginCheckMiddleware` | 75-78 | `halt` ise doğrudan yanıt + `exit` (80-86) |
| 2 | yanıt başlıkları | 81-83 | `Content-Type: application/json` · CSP · `nosniff` |
| 3 | `RateLimiterMiddleware` | 91-94 | ADR-013 §5.4 şart 1d: **tek kaynak** sınıf |
| 4 | rate-limit yanıtı | 96-104 | `halt` → status + ek header + JSON + `exit` |
| 5 | oturum | 107 | `SessionBootstrapper::ensureStarted()` |
| 6 | uç eşlemesi | 109-118 | `match` → controller · `default` **404** |

### §16.3 Yönlendirme aileleri (302)

| # | Durum | Hedef | Satır |
|---|---|---|---|
| 1 | bypass aktif | `MUSIC_URL` (varsayılan `http://home.coremusic.net:81`) + `/auth/callback?auth_key=...` | 131-136 |
| 2 | `auth_key` | `AuthKeyRedirectHandler` | 139-140 |
| 3 | paramsız kök | `/select-gender?client_id=coremusic-web&response_type=session&...` | 143-149 |
| 4 | bypass + non-POST | `/auth/callback` | 154-157 |
| 5 | cinsiyet yok | `/select-gender?{$_GET}` | 166-167 |
| 6 | `client_id` yok | `/{pageName}?client_id=coremusic-web&...` | 174-182 |

> **Topoloji kanıtı:** satır 133 ve 155'teki varsayılan `http://home.coremusic.net:81`,
> `shared/config/domain.php:17` (`'home' => 81`) ile **aynı portu** verir — iki dosya
> birbirini doğrular.

### §16.4 Üç giriş noktası özeti

| # | Alt alan | Front controller | Router | Özel uçlar | Hata JSON'u |
|---|---|---|---|---|---|
| 1 | `home` | `index.php` (29) | `PageRouterKernel` (bootstrap üzerinden) | — | kernel |
| 2 | `api` | `index.php` (223) | `Gateway` + `RouteTable` | `/health` · `/api/v1/*` | `{error:{code,message}}` |
| 3 | `auth` | `index.php` (238) | `PageRouterKernel` (doğrudan) | `/health` · `/session` · `/validate-key` | `{success:false,error:{...}}` |
| 4 | `assets` | **yok** | — | — | — |

> Sözleşme farkı (rapor): `api` hata gövdesi `{error:{...}}`, `auth` hata gövdesi
> `{success:false,error:{...}}` — iki JSON sözleşmesi farklıdır (satır 208-213 vs 237).

## §17 — Başlık Matrisi: `.htaccess` (Apache) ↔ `web.config` (IIS)

> 4 alt alan × 2 sunucu yapılandırması = 8 dosya; hepsi bu turda satır satır okundu.
> Amaç: iki sunucu yüzeyinin aynı güvenlik başlıklarını verip vermediğini eşleştirmek.

### §17.1 Güvenlik başlıkları (dosya → satır kanıtı)

| # | Başlık | `home` (.htaccess / web.config) | `auth` (.htaccess / web.config) | `api` (.htaccess / web.config) | `assets` (.htaccess / web.config) |
|---|---|---|---|---|---|
| 1 | `X-Frame-Options` | DENY (L10) / DENY (L8) | DENY (L10) / DENY (L8) | DENY (L10) / DENY (L7) | **SAMEORIGIN** (L43) / **yok** |
| 2 | `X-Content-Type-Options` | nosniff (L11) / nosniff (L7) | nosniff (L11) / nosniff (L7) | nosniff (L11) / **yok** | nosniff (L42) / nosniff (L11) |
| 3 | `X-XSS-Protection` | 0 (L12) / 0 (L9) | 0 (L12) / 0 (L9) | 0 (L12) / **yok** | **yok** / **yok** |
| 4 | `Referrer-Policy` | strict-origin… (L13) / strict-origin… (L10) | strict-origin… (L13) / strict-origin… (L10) | **no-referrer** (L13) / **no-referrer** (L8) | **yok** / **yok** |
| 5 | `Permissions-Policy` | **yok** / camera=(),microphone=(),geolocation=() (L11) | **yok** / aynı (L11) | **yok** / aynı (L9) | **yok** / **yok** |
| 6 | `X-Powered-By` | — / `<remove>` (L6) | — / `<remove>` (L6) | — / `<remove>` (L6) | — / **yok** |
| 7 | CORS (`Access-Control-Allow-Origin`) | **yok** / **yok** | **yok** / **yok** | **yok** / **yok** | `*` (L7,L14,L19,L29) / `*` (L7) |

**Düzeltme kaydı:** evvelce `api` web.config'te `X-Content-Type-Options` var sanılıyordu —
satır satır okumada **yoktur** (yalnız `X-Frame-Options` L7, `Referrer-Policy` L8,
`Permissions-Policy` L9, `<remove X-Powered-By>` L6). IIS'te `api` için nosniff **eksiktir**;
Apache `.htaccess` L11 onu verir. Sunucu bağımsız tek başlık **yalnız** `X-Frame-Options: DENY`
(home/auth/api) + `<remove name="X-Powered-By">` (home/auth/api web.config).

### §17.2 IIS rewrite kuralları (web.config ↔ .htaccess karşılığı)

| # | Alan | Kural adı (web.config) | Karşılığı (.htaccess) | HTTP |
|---|---|---|---|---|
| 1 | home | `Static files` (L16-19) | statik dosya bloğu | `None` |
| 2 | home | `Block config/include/pages` (L20-23) | `config|include|pages` reddi | **403** |
| 3 | home | `Block sensitive files` (L24-27) | `.env\|log\|…` reddi | **403** |
| 4 | home | `SPA Router` (L28-35) | dosya/dizinse geçir | `None` |
| 5 | home | `Rewrite to index.php` (L36-39) | `RewriteRule ^ index.php` | rewrite |
| 6 | auth | `Block config/include` (L20-23) + `Block sensitive files` (L24-27) | aynı | **403** |
| 7 | api | `Block private dirs` (L22-25): `config|include|tests|vendor` | `.htaccess` reddi | **403** |
| 8 | api | `Block sensitive files` (L18-21) deseni `example` **dahil** (L19) | auth/home deseninden **farklı** | **403** |
| 9 | assets | rewrite **yok** (yalnız `customHeaders` + `staticContent`) | cache/CORS blokları | — |

> `assets/web.config`'te `<rewrite>` düğümü hiç bulunmaz (L1-37 arası okundu) — statik
> vhost'ta yönlendirme gerekmez; `.htaccess` tarafında ise 8 `Header set` + 4 `Cache-Control` vardır.

### §17.3 IIS farkları özeti (Apache'e kıyasla)

| # | Fark | Etki |
|---|---|---|
| 1 | `api` web.config: `X-Content-Type-Options` yok | IIS'te `api` yanıtı Apache'teki kadar MIME-sniff korumalı değil → **⚠️ VERIFICATION REQUIRED** (üretim IIS başlığı uygulama katmanından ekleniyor mu?) |
| 2 | `api` web.config: `X-XSS-Protection` yok | Eski IE koruması; modern tarayıcılarda etkisiz — düşük risk |
| 3 | `assets` web.config: `X-Frame-Options` yok | `.htaccess` L43 `SAMEORIGIN` verir; IIS'te embed koruması **yok** → clickjacking yüzeyi **⚠️ VERIFICATION REQUIRED** |
| 4 | `Permissions-Policy` yalnız IIS'te (home/auth/api) | Apache `.htaccess` tarafında tanımlı değil → sunucuya göre farklı politika |
| 5 | assets CORS başlıkları iki tarafta da `*` | `Access-Control-Allow-Headers` IIS'te `Content-Type, Range, Accept` (L9), `.htaccess` L9'da yalnız `Content-Type, Range` — **Accept farkı** |

## §18 — Port, MIME ve Günlük Envanteri

### §18.1 Port envanteri (disk kanıtı)

| # | Anahtar | Port | Kanıt |
|---|---|---|---|
| 1 | `home` | **81** | `shared/config/domain.php:17` → `'home' => 81` |
| 2 | `auth` | **80** | varsayılan HTTP (port kaydı `domain.php` içinde yok) |
| 3 | `api` | **80** | varsayılan HTTP (port kaydı yok) |
| 4 | `assets` | **80** | varsayılan HTTP (port kaydı yok) |
| 5 | `MUSIC_URL` varsayılanı | `http://home.coremusic.net:81` | `auth.coremusic.net/index.php:133` ve `:155` |

> `domain.php` içinde yalnız `home => 81` port eşlemesi vardır; diğer üç alt alan için
> port tanımı repo'da **yok** → 80 varsayımı **⚠️ VERIFICATION REQUIRED** (sunucu VHost/IIS binding'i repo dışı).

### §18.2 Font MIME haritası (IIS — `assets/web.config` L20-32)

| # | Uzantı | mimeType | Satır |
|---|---|---|---|
| 1 | `.ttf` | `font/ttf` | L22-23 |
| 2 | `.otf` | `font/otf` | L24-25 |
| 3 | `.woff` | `font/woff` | L26-27 |
| 4 | `.woff2` | `font/woff2` | L28-29 |
| 5 | `.eot` | `application/vnd.ms-fontobject` | L30-31 |

> Her madde önce `<remove fileExtension>` (önceki kaydı siler) sonra `<mimeMap>` ile gelir —
> IIS'in varsayılan MIME kayıtlarını **ezmeden** üzerine yazma deseni.

### §18.3 Dizin listeleme ve önbellek politikası (IIS)

| # | Kural | Değer | Kanıt |
|---|---|---|---|
| 1 | dizin listeleme | `directoryBrowse enabled="false"` | `assets/web.config:35` (yorum: "F-06: Apache .htaccess ile aynı politika") |
| 2 | `Cache-Control` (IIS assets) | `no-cache` — tek değer, tüm varlıklar | `assets/web.config:16` |
| 3 | IIS cache gerekçesi (yorum satırı) | "bundler olmadan bayat modül karışımını önlemek için her zaman doğrula; ETag 304 ucuz" + "IIS kuralı extension ayırt etmez — görseller de revalidate eder" | `assets/web.config:12-15` |
| 4 | `.htaccess` cache (Apache) | CSS 3600 · JS no-cache · font immutable · görsel 604800 | `assets/.htaccess:20,30,35,38` (§12) |

> **Sunucu farkı:** Apache'te varlık tipine göre 4 ayrı `max-age` vardır; IIS'te **tek**
> `no-cache` her şeyi kapsar → aynı dosya sunucuya göre farklı cache davranışı gösterir.

### §18.4 Günlük yolu

| # | Kaynak | Yol | Kanıt |
|---|---|---|---|
| 1 | PHP hata günlüğü | `../../coremusic_php_errors.log` | `auth/.htaccess` hata günlüğü yönlendirmesi (repo içi göreli yol) |
| 2 | `.env` içeriği | okunmadı (gizli · `.gitignore`) | Guardrail: Secret Yok |

### §18.5 Ek tekrarlanabilir ölçüm komutları

| # | Amaç | Komut (repo kökü) | Beklenen |
|---|---|---|---|
| 1 | başlık satırı sayısı (auth web.config) | `Select-String -Path 'auth.coremusic.net\web.config' -Pattern '<add name='` | 5 |
| 2 | assets web.config başlıkları | `Select-String -Path 'assets.coremusic.net\web.config' -Pattern '<add name='` | 7 |
| 3 | IIS rewrite kuralı sayısı (api) | `Select-String -Path 'api.coremusic.net\web.config' -Pattern '<rule name='` | 5 |
| 4 | font MIME kaydı | `Select-String -Path 'assets.coremusic.net\web.config' -Pattern 'mimeMap'` | 5 |
| 5 | Apache başlığı (api .htaccess) | `Select-String -Path 'api.coremusic.net\.htaccess' -Pattern 'Header always set'` | 4 |

## § OS GRUPLAR — sekiz işletim sistemi grubu: yürütme topolojisi karşılaştırması

> Bu bölüm, bu dosyanın yürütme yüzeyini (§1 uygulama katmanı · §2 akış · §17 başlık
> matrisi) işletim sistemi grubu ekseniyle karşılaştırır. Depoda yalnız Windows/IIS ve
> POSIX/Apache yüzeyi yürütülür (§13 negatif kanıt defteri); diğer gruplarda repo'da
> yürütülecek config olmadığı için örnekler "(bu projede kullanılmıyor)" etiketiyle verilir.

### OS.1 — Karşılaştırma tablosu

| # | OS | Web sunucu/runtime topolojisi | Bu dosyadaki karşılığı | Kaynak |
|---|---|---|---|---|
| 1 | Windows | IIS: HTTP.sys → app-pool → worker process; PHP FastCGI handler | §1 IIS satırı (`web.config` 43) · §17 başlık matrisi (IIS) | repo `home.coremusic.net/web.config:1-43` (evidence) |
| 2 | macOS | gömülü Apache httpd — MPM + `.htaccess` yürütme zinciri | §1 Apache satırı · §2 akış (Apache katmanı) | `https://httpd.apache.org/docs/2.4/howto/htaccess.html` (evidence) |
| 3 | iOS | ayrı web sunucusu topolojisi bu projede tanımlı değil | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 4 | Android | ayrı web sunucusu topolojisi bu projede tanımlı değil | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 5 | Linux | nginx veya Apache + PHP-FPM FastCGI zinciri | §1 FPM havuzu satırı (❌ YOK, R3) · §7 Kaynakça (install.fpm) | `https://www.php.net/manual/en/install.fpm.php` (evidence) |
| 6 | FreeBSD/BSD | ports/paket Apache veya nginx — POSIX süreç + sinyal kontrolü | §13 negatif kanıt defteri (POSIX betik repo'da yok) | `https://nginx.org/en/docs/control.html` (bu tur eklendi) |
| 7 | Tizen | ayrı sunucu topolojisi yok (yerleşik web runtime) | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 8 | webOS | ayrı sunucu topolojisi yok (yerleşik web runtime) | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |

### OS.2 — OS bazlı kısa açıklamalar (2-3 cümle)

**Windows.** IIS'te yürütme HTTP.sys'ten app-pool'a, oradan worker process'e akar; bu
süreç `web.config` kurallarını (§17 başlık matrisi) ve URL Rewrite'ı uygular. PHP aynı
worker süreci içinde FastCGI handler ile devreye girer. Repo'da bu yüzey `web.config` ×4
ile ✅ IMPLEMENTED (§1).

**macOS.** Gömülü Apache (httpd) Linux Apache ile aynı yürütme zincirini izler: MPM
süreci → `.htaccess` → rewrite → PHP devri (§2 akış). Bu dosyada macOS'a özgü hiçbir
yürütme adımı yoktur; `.htaccess` kuralları platformdan bağımsızdır (§1 Apache satırı).

**iOS.** Yürütülecek web sunucusu config'i bu projede yoktur — iOS satırı yalnız
topoloji karşılaştırması için taşınır. Depoda iOS'e ait dosya kanıtı bulunmadığından
"(bu projede kullanılmıyor)" etiketi geçerlidir; platform dokümanı bu tur eklenmedi (⚠️).

**Android.** Android için de yürütülecek sunucu config'i yoktur; proje yürütme yüzeyi
Apache ve IIS'e indirgenir (§1 tablosu). Depoda Android kanıtı yok → "(bu projede
kullanılmıyor)"; resmi platform dokümanı bu tur eklenmedi (⚠️).

**Linux.** Linux topolojisinin yürütme karşılığı nginx/Apache + PHP-FPM zinciridir; repo'da
`www.conf` gibi bir FPM havuz dosyası bulunmadığından bu zincir henüz yürütülmez (§1 ❌ YOK).
Yalnız çapraz hedef tasarımı taşınır — kapasite/havuz değeri R3/R7 ile [⚠️].

**FreeBSD/BSD.** FreeBSD'de Apache/nginx ports üzerinden kurulur ve POSIX sinyalleriyle
kontrol edilir (HUP/USR1/QUIT); süreç modeli Linux ile aynıdır. Repo'da BSD'ye özgü betik
yok (§13 negatif kanıt defteri) → "(bu projede kullanılmıyor)".

**Tizen.** Tizen'de web uygulamaları yerleşik web runtime'ında çalışır; Apache/nginx
yürütme zinciri bu projede tanımlı değildir. Depoda Tizen kanıtı yok → "(bu projede
kullanılmıyor)"; platform dokümanı bu tur eklenmedi (⚠️).

**webOS.** webOS'ta uygulamalar yerleşik web runtime'ında çalışır; `.htaccess`/`web.config`
gibi yürütme katmanları bu platformda geçerli değildir. Depoda webOS kanıtı yok →
"(bu projede kullanılmıyor)"; platform dokümanı bu tur eklenmedi (⚠️).

### OS.3 — OS bazlı config örnekleri (ikisi de bu projede kullanılmıyor)

**1) POSIX sunucu (Linux/FreeBSD) — nginx master süreç kontrolü ·
kaynak `https://nginx.org/en/docs/control.html` (2026-10-14 HTTP 200)**

```bash
# pid dosyası varsayılanı: /usr/local/nginx/logs/nginx.pid (nginx.conf pid directive)
NGINX_PID=/usr/local/nginx/logs/nginx.pid
kill -HUP  "$NGINX_PID"   # konfigürasyonu yeniden oku + yeni worker başlat
kill -USR1 "$NGINX_PID"   # log dosyalarını yeniden aç (log rotation)
kill -QUIT "$NGINX_PID"   # graceful shutdown — çalışan istekler tamamlanır
# TERM/INT hızlı kapanış · USR2 canlı binary yükseltme · WINCH worker kapanışı
```

**2) POSIX sunucu (Linux/FreeBSD) — nginx çekirdek sunucu bloğu ·
kaynak `https://nginx.org/en/docs/beginners_guide.html` (2026-10-14 HTTP 200 — `listen` · `root` ·
`location` örnekleri bu rehberde) · `server_name` · `client_max_body_size` · `try_files` satırları nginx
`ngx_http_core_module` direktif listesinden doğrulandı (2026-10-14 HTTP 200, anchor `#server_name` ·
`#client_max_body_size` · `#try_files` — ngx_http_core_module sayfasının URL'si bu ağaçta başka bir
dosyada kullanıldığı için bu dosyada yinelenmedi)**

```nginx
# örnek yollar temsilîdir — repo'da nginx betiği yok (§1 "nginx betiği ❌ YOK")
server {
    listen 80;                                     # core: listen address[:port] ...
    server_name home.coremusic.net;                # core: server_name name ...
    root /var/www/coremusic/home.coremusic.net;     # core: root path ...
    client_max_body_size 1m;                       # core varsayılanı: 1m
    try_files $uri $uri/ /index.php$is_args$args;  # core: try_files file ... uri;
}
```

> **Doğrulama notu:** `index` direktifi `ngx_http_core_module` direktif listesinde **yoktur**
> (2026-10-14 sayfa kontrolü) — bu yüzden bloğa yazılmadı; PHP'ye devir `try_files` ile
> `index.php`'ye yapılır ve repo'daki `.htaccess:36` karşılığıyla aynı semantiği taşır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode