---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/index
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/anayasa
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/surucu
evidence: https://httpd.apache.org/docs/2.4/rewrite/rules.html · https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html · https://httpd.apache.org/docs/2.4/vhosts/name-based.html · shared/src/Cache/ApcuAdapter.php · .github/workflows/ci.yml · home.coremusic.net/.htaccess · home.coremusic.net/web.config · api.coremusic.net/index.php · https://httpd.apache.org/docs/2.4/mod/core.html · https://raw.githubusercontent.com/php/php-src/master/sapi/fpm/www.conf.in · https://www.php.net/manual/en/install.apache.php · https://www.php.net/manual/en/install.windows.iis.php
validation: 2026-10-13 · 3 URL HTTP 200 (rewrite/rules · ngx_http_fastcgi_module · name-based) · repo satırları Read ile birebir · içerik 380-460 satır
open_questions: R3 mod_php ile PHP-FPM geçişi · R7 MPM ile havuz kapasitesi eşlemesi
group: kod
---

# web-runtime-topolojisi-ornekler — Çalışan Örnekler ve Anti-Pattern'ler (Kademe-3)

## §0 ELI15

Bu sayfa, web runtime topolojisinin **çalışan kod örneklerini** gösterir: Apache
`.htaccess` yönlendirme bloğu, `web.config` karşılığı, FastCGI/proxy davranışı ve
çok-site (vhost) eşlemesi. Her örnek repo dosyalarından birebir alıntılanır; ardından
"ne gösterdiği" ve **anti-pattern** (yanlış kullanım) listesi gelir. Amaç: doğru
örneği kopyalanabilir halde görmek, yaygın hataları tanımak.

## §1 — Harita

| Bölüm | İçerik | Kaynak |
|---|---|---|
| §2.1 | Apache `.htaccess` — dosya sistemi HTTP'ye nasıl çevrilir | `home.coremusic.net/.htaccess:5-7` |
| §2.2 | Apache rewrite zinciri — iç URL → front-controller | `home.coremusic.net/.htaccess:26-37` |
| §2.3 | IIS `web.config` — aynı kuralın Windows karşılığı | `home.coremusic.net/web.config:36-39` |
| §2.4 | Apache Response Header — runtime başlık enjeksiyonu | `home.coremusic.net/web.config:4-13` |
| §2.5 | Nginx FastCGI yönlendirmesi — `$uri` → `index.php` | nginx `ngx_http_fastcgi_module` |
| §2.6 | Name-based vhost — Host başlığı → site topolojisi | Apache name-based vhosts |
| §3 | Anti-pattern'ler (AP-1..AP-6) | repo + dokümantasyon |
| §4 | Ölçüm / test kapıları | repo + dokümantasyon |

## §2 — Çalışan Örnekler

### §2.1 — Apache `.htaccess`: dosya sistemi HTTP'ye çevrilir

```apache
DirectoryIndex index.php index.html
Options -Indexes +FollowSymLinks
```
*(kaynak: `home.coremusic.net/.htaccess:5-7` — birebir)*

**Ne gösteriyor:** dizin listeleme kapatılır (`-Indexes`), dizin isteğinde ilk
`index.php` seçilir. Bu, dosya sistemi→HTTP köprüsünün en küçük parçasıdır:
"klasör isteği" → "tek entry script".

### §2.2 — Apache rewrite zinciri: iç URL → front-controller

```apache
RewriteEngine on
RewriteBase /

# www olmayan istekleri www'ye yönlendir
RewriteCond %{HTTP_HOST} ^coremusic\.net [NC]
RewriteRule ^(.*)$ https://www.coremusic.net/$1 [R=301,L]

# mevcut dosya/dizin değilse front-controller'a
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```
*(kaynak: `home.coremusic.net/.htaccess:26-37` — birebir; `!-f`/`!-d` koşulları
`docs/apache-virtualhost-setup.md:62` köprüsüyle aynı kuralı doğrular)*

**Ne gösteriyor:** üç katmanlı zincir — (1) Host canonical (301), (2) fiziksel
dosya yoksa (`!-f`, `!-d`) front-controller'a geç, (3) `[QSA]` sorgu dizesini koru.
Rewrite kuralları dizin başına bir kez değerlendirilir (docs:72) — her istekte
yeniden derlenmez.

### §2.3 — IIS `web.config`: aynı kuralın Windows karşılığı

```xml
<rules>
  <rule name="FrontController" stopProcessing="true">
    <match url="^(.*)$" />
    <conditions logicalGrouping="MatchAll">
      <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
      <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
    </conditions>
    <action type="Rewrite" url="index.php/{R:1}" appendQueryString="true" />
  </rule>
</rules>
```
*(kaynak: `home.coremusic.net/web.config:36-39` — birebir; `<conditions>` bloğu
`:26-35` arasında uzanır ve `IsFile`/`IsDirectory` negate = Apache `!-f`/`!-d`)*

**Ne gösteriyor:** aynı topoloji, farklı doğrudan yapılandırma: IIS'de `.htaccess`
yerine XML; koşul mantığı birebir aynıdır. Kök `.htaccess` `:35-37` bu dosyanın
"NOT: IIS karşılığı web.config" notunu taşır — iki yapı aynı kuralı kodlar.

### §2.4 — Response Header enjeksiyonu (runtime)

```xml
<httpProtocol>
  <customHeaders>
    <add name="X-Content-Type-Options" value="nosniff" />
    <add name="X-Frame-Options" value="SAMEORIGIN" />
    <add name="Referrer-Policy" value="strict-origin-when-cross-origin" />
  </customHeaders>
</httpProtocol>
```
*(kaynak: `home.coremusic.net/web.config:4-13` — yapı birebir; satır 6-8 üç
güvenlik başlığını içerir)*

**Ne gösteriyor:** güvenlik başlıkları web sunucusu katmanında, PHP'ye uğramadan
eklenir. Apache karşılığı `.htaccess:16-19` `<IfModule mod_headers.c>` bloğudur
— yapı iki sunucuda aynı niyeti kodlar.

### §2.5 — Nginx FastCGI yönlendirmesi: `$uri` → `index.php`

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
location ~ \.php$ {
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```
*(kaynak: nginx `ngx_http_fastcgi_module` dokümantasyonu — `fastcgi_pass` ve
`SCRIPT_FILENAME` parametresi HTTP 200 ile doğrulanmıştır)*

**Ne gösteriyor:** Nginx'te "dizin yoksa front-controller" kuralı `try_files`
ile yazılır; `.php` istekleri FastCGI protokolüyle `127.0.0.1:9000`'deki PHP-FPM
worker'larına iletilir (anayasa §2.3 köprüsü). `SCRIPT_FILENAME`, FPM'in
çalıştıracağı dosyayı belirler — bu, web sunucusu↔FPM sözleşmesinin çekirdeğidir.

### §2.6 — Name-based vhost: Host başlığı → site topolojisi

```apache
<VirtualHost *:443>
    ServerName www.coremusic.net
    DocumentRoot /www/coremusic.net/home.coremusic.net
    <Directory /www/coremusic.net/home.coremusic.net>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

<VirtualHost *:443>
    ServerName api.coremusic.net
    DocumentRoot /www/coremusic.net/api.coremusic.net
    <Directory /www/coremusic.net/api.coremusic.net>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
*(kaynak: Apache name-based vhosts dokümantasyonu + repo dizin yapısı
`home.coremusic.net/` · `api.coremusic.net/` — `docs/apache-virtualhost-setup.md`
kural 1-4 adımlarıyla aynı düzen)*

**Ne gösteriyor:** aynı IP:port üzerinde `Host` başlığına göre farklı
`DocumentRoot` seçilir; her sitenin kendi `.htaccess`/rewrite zinciri vardır.
API vhost'u anayasa §2.4'teki pipeline'a, site vhost'u §2.6 front-controller'a
gider — iki topoloji tek IP'de yaşar.

## §3 — Anti-Pattern'ler

| ID | Anti-pattern | Neden kötü | Doğrusu | Kanıt |
|---|---|---|---|---|
| AP-1 | `.htaccess` içinde `AllowOverride All` + her dizinde tekrar kurallar | Her istekte (önbellek yoksa) dosya sistemi taranır | Kuralları vhost `<Directory>` bloğuna taşı, `.htaccess`'i kapat | Apache docs:72-77 (izin yalnız gerektiğinde verilir) |
| AP-2 | Rewrite'te `!-f`/`!-d` koşulsuz front-controller | Statik dosyalar PHP'ye düşer, her istek worker yakar | Koşullar zorunlu: `RewriteCond %{REQUEST_FILENAME} !-f` | repo `.htaccess:33-34` |
| AP-3 | Nginx'te `location ~ \.php$` içinde `try_files $uri =404` yok | Var olmayan `.php` dosyaları PHP'ye iletilir (CVE sınıfı) | `try_files $uri =404;` ekle; `SCRIPT_FILENAME`'i `$document_root` ile kur | nginx fastcgi module |
| AP-4 | Host canonical 301'i `<VirtualHost>` dışında yazma | HTTPS/HTTP ve www/non-www iki kez çözülür; çift 301 döngüsü | Canonical kuralı en üst düzey vhost'a yaz, tek R=301 | repo `.htaccess:28-31` |
| AP-5 | `php_admin_value open_basedir` gibi direktifleri `.htaccess`'te değiştirme | `open_basedir`/`disable_functions` runtime'da değiştirilemez; `.htaccess` fataL | Kısıtı sunucu/FPM pool yapılandırmasında uygula | PHP `ini_set` docs direktif listesi |
| AP-6 | FastCGI'de `SCRIPT_FILENAME`'i `alias`/değişkenle yanlış kurma | FPM "File not found." döner; 404 yerine 500 sınıfı hata | `SCRIPT_FILENAME $document_root$fastcgi_script_name` | nginx fastcgi module |

## §4 — Ölçüm / Test Kapıları

| Kapı | Test | Beklenen | Sıklık |
|---|---|---|---|
| Rewrite zinciri | `curl -I https://www.coremusic.net/olmayan-sayfa` | istek `index.php`'ye rewrite edilir; 200/404 uygulama düzeyi | deploy |
| Canonical | `curl -I http://coremusic.net/` | tek `301` → `https://www...` | deploy |
| Dosya koruma | `curl -I https://www.coremusic.net/.htaccess` | 403/404, asla 200 | deploy |
| FPM köprüsü | `curl -I https://api.coremusic.net/health` | 200, `Content-Type: application/json` | CI + deploy |
| Header enjeksiyonu | `curl -I ... | findstr /C:"X-Content-Type-Options"` | `nosniff` mevcut | deploy |
| Vhost eşlemesi | `curl -H "Host: api.coremusic.net" ...` | API dokümanı, site HTML değil | deploy |

## §5 — Komşu Sınırı

Bu dosya web sunucusu ↔ PHP köprüsünün **örneklerini** verir; MPM worker/FPM
havuz eşlemesi `surucu.md` §2.2-§2.4 içindedir (R7 açık). Boot katmanı
(opcache/app.php) `baslangic-ve-bootstrap/` alt dalının konusudur — buraya
taşıma yok. PHP hata yönetimi örnekleri (`set_error_handler` vb.)
`baslangic-ve-bootstrap/ornekler.md` içinde; tekrar edilmez.

## §6 — Terimler ve Kanıt Cümleleri

| Terim | Anlam | Kanıt cümlesi |
|---|---|---|
| `RewriteCond !-f` | Fiziksel dosya yoksa kural uygulanır | "İstek edilen dosya yoksa front-controller'a yaz" (repo `.htaccess:33-37`) |
| `QSA` | Query String Append — mevcut sorgu eklenir | "`[QSA,L]` zinciri kapatır, sorguyu korur" (repo `.htaccess:37`) |
| `try_files` | Nginx'te dizin yoksa fallback URI | "Son argüan fallback: `/index.php?$query_string`" (nginx docs) |
| `fastcgi_pass` | FPM'e giden TCP/unix socket adresi | "PHP-FPM `127.0.0.1:9000` FastCGI'ye konuşur" (anayasa §2.3) |
| `DocumentRoot` | Vhost kök dizini | "Host başlığına göre farklı kök seçilir" (name-based docs) |
| `AllowOverride` | `.htaccess`'in yetkisi | "Yalnız gerektiğinde verilir" (Apache docs:72) |

## §7 — Kapsam Dışı

MPM seçimi ve havuz kapasitesi hesabı (`surucu.md`) · boot/opcache ısıtma
(`baslangic-ve-bootstrap/`) · hata yönetimi PHP fonksiyonları
(`baslangic-ve-bootstrap/ornekler.md`) · CI/CD pipeline akışı
(`.github/workflows/ci.yml` yalnız test kapısı olarak kullanıldı) ·
TLS sertifika yönetimi · CDN/önbellek katmanı.

## §8 — Açık Sorular

| # | Soru | Durum |
|---|---|---|
| R3 | mod_php ile PHP-FPM geçişi | `[UNKNOWN]` — repo `.htaccess` düzeyinde geçiş kanıtı yok |
| R7 | MPM (prefork/worker/event) ile FPM havuz kapasitesinin eşlemesi | `[UNKNOWN]` — `pm.max_children` hesabı için ölçek gerekli |

## §9 — Kaynakça

1. Apache HTTP Server — Mod Rewrite Rules
   https://httpd.apache.org/docs/2.4/rewrite/rules.html
2. Nginx — ngx_http_fastcgi_module
   https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html
3. Apache HTTP Server — Name-based Virtual Hosts
   https://httpd.apache.org/docs/2.4/vhosts/name-based.html
   (Bu üç URL bu dalın `index.md`'sinden devralınmıştır; tekrar yazılmamıştır.)

## §10 - Repo Örnekleri (E-1 … E-8) — birebir bloklar

> Tüm bloklar 2026-10-13'te dosyalardan `ReadAllLines` ile kopyalanmıştır; satır
> numaraları kaynak dosyaya aittir.

### §10.1 - E-1 SPA rewrite (`home.coremusic.net/.htaccess:26-37`)

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

### §10.2 - E-2 IIS karşılığı (`api.coremusic.net/web.config:26-37`)

```xml
<rule name="Passthrough" stopProcessing="true">
    <match url="^" ignoreCase="false" />
    <conditions logicalGrouping="MatchAny">
        <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" />
        <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" />
    </conditions>
    <action type="None" />
</rule>
<rule name="Rewrite to index.php" stopProcessing="true">
    <match url="^" ignoreCase="false" />
    <action type="Rewrite" url="index.php" appendQueryString="true" />
</rule>
```

### §10.3 - E-3 Topoloji tablosu (`shared/config/domain.php:1-19`)

```php
<?php declare(strict_types=1);

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

### §10.4 - E-4 Varsayılan middleware dizisi (`PageRouterKernel.php:330-341`)

```php
return [
    new OriginCheckMiddleware($isProduction, $corsCfg),
    new CorsMiddleware($corsCfg),
    new RateLimiterMiddleware(self::RATE_LIMIT_MAX, self::RATE_LIMIT_WINDOW),
    new SecurityHeadersMiddleware($this->domainConfig),
    new SessionManagerMiddleware($sessionInit),
    new CsrfMiddleware(),
    new BypassAuthMiddleware($this->config),
    new AuthMiddleware(),
    new PermissionMiddleware(),
    new ValidationMiddleware(),
];
```

### §10.5 - E-5 API pipeline (`api.coremusic.net/index.php:147-171`)

```php
$pipeline = (new ApiMiddlewarePipeline())
    ->pipe(new ResponseNormalizationMiddleware())
    ->pipe(static fn (array $req, callable $next): array => $originCheckMiddleware->handle($req, $next))
    ->pipe(static fn (array $req, callable $next): array => $corsMiddleware->handle($req, $next))
    ->pipe(new RateLimitMiddleware(new CacheRateLimiter($apiCache)))
    ->pipe(new AuthenticationMiddleware(
        new ApiSessionManager(),
        \CoreMusic\Api\Container\ApiAuthContainer::jwtService(),
        static fn (string $jti): bool =>
            \CoreMusic\Api\Container\ApiAuthContainer::userRepository()->isValidAccessToken($jti),
    ))
    ->pipe(static function (array $req, callable $next) use ($csrfMiddleware): array {
        if (($req['_auth_user']['method'] ?? '') !== 'session') {
            return $next($req);
        }
        return $csrfMiddleware->handle($req, $next);
    })
    ->pipe(new RequestValidationMiddleware())
    ->pipe(new AuthorizationMiddleware());
```

### §10.6 - E-6 Pipeline öncesi OriginCheck + RateLimit (`auth.coremusic.net/index.php:75-105`)

```php
$originChecked = (new \CoreMusic\Middleware\OriginCheckMiddleware($isProductionEnv, $corsConfig))->handle(
    ['server' => $_SERVER],
    static fn (array $req): array => ['httpStatus' => 0, 'type' => 'json'],
);
if (($originChecked['halt'] ?? false) === true) {
    http_response_code((int)$originChecked['httpStatus']);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($originChecked['body'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

$rateLimited = (new \CoreMusic\Middleware\RateLimiterMiddleware())->handle(
    ['method' => $method, 'server' => $_SERVER],
    static fn (array $req): array => ['httpStatus' => 0, 'type' => 'json'],
);
```

### §10.7 - E-7 Varlık önbellek blokları (`assets.coremusic.net/.htaccess:28-39`)

```apache
<FilesMatch "\.js$">
    Header set Access-Control-Allow-Origin "*"
    Header set Cache-Control "no-cache"
</FilesMatch>

<FilesMatch "\.(ttf|otf|woff|woff2|eot)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
</FilesMatch>

<FilesMatch "\.(png|jpg|jpeg|gif|webp|svg|ico)$">
    Header set Cache-Control "public, max-age=604800"
</FilesMatch>
```

### §10.8 - E-8 API hata JSON'u (`api.coremusic.net/index.php:201-223`)

```php
} catch (\Throwable $e) {
    $logger->error("Unhandled: {$e->getMessage()}", [...]);
    $jsonResponse([
        'error' => [
            'code'    => 'SERVER_INTERNAL_ERROR',
            'message' => 'Sunucu hatası.',
        ],
    ], 500);
}

/* --- 3. 404 --- */
$jsonResponse([
    'error' => [
        'code'    => 'NOT_FOUND',
        'message' => 'Uç bulunamadı.',
    ],
], 404);
```

## §11 - Örnek ↔ Anayasa/Sürücü Eşlemesi

| Örnek | Anayasa karşılığı | Sürücü karşılığı |
|---|---|---|
| E-1 SPA rewrite | §10.1 (Apache) | §8/2 |
| E-2 IIS rewrite | §10.1 (IIS) | §8 |
| E-3 domain topolojisi | §11 | §12 |
| E-4 middleware dizisi | §13 | §10 (fark notu) |
| E-5 API pipeline | §13 | §10 |
| E-6 auth öncesi kontroller | §12 (istek yaşam döngüsü) | §16.2 |
| E-7 varlık önbelleği | — | §12 |
| E-8 hata JSON'u | §14 (durum kodları) | §11 |

## §12 - Anti-Pattern Eki (bu tur gözlemleri)

| # | Gözlem | Kanıt | Aksiyon |
|---|---|---|---|
| 1 | IIS `api/web.config` başlık listesi eksik (`X-Content-Type-Options`, `X-XSS-Protection`) | `web.config` satır 6-10 vs `.htaccess` 10-15 | rapor |
| 2 | `debug.php` izni yalnız home'da | `home/.htaccess:22` vs `auth/.htaccess:22` | rapor |
| 3 | IIS `api` kuralı `tests\|vendor` engellerken Apache yalnız `config\|include` | `.htaccess:38` vs `web.config:23` | rapor |
| 4 | hata JSON sözleşmesi iki farklı biçimde | `api` `{error:{...}}` vs `auth` `{success:false,...}` | rapor |
| 5 | `assets/.htaccess` rewrite/front-controller taşımaz | dosya 47 satır, 0 rewrite kuralı | bilgi |
| 6 | JS `no-cache` kararı yorumla gerekçelendirilmiş | `.htaccess:23-27` | iyi örnek |

> Bu altı satır **rapordur**; hiçbirine bu dalda düzeltme uygulanmamıştır (onaysız kod değişikliği yasak).

## §13 - Ölçüm / Test Kapıları

| # | Amaç | Komut (repo kökünden) | Beklenen |
|---|---|---|---|
| 1 | `.htaccess` sayısı | `Get-ChildItem -Recurse -Filter .htaccess` (vendor hariç) | 4 |
| 2 | `web.config` sayısı | `Get-ChildItem -Recurse -Filter web.config` (vendor hariç) | 4 |
| 3 | SPA rewrite varlığı | desen `index\.php \[QSA,L\]` | 3 (`home` · `api` · `auth`) |
| 4 | `immutable` kullanımı | desen `'immutable'` | 1 (`assets:35`) |
| 5 | dolu satır | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |

## §14 - URL Durumu (bu tur)

| # | Konu | Kullanılan kaynak | Durum |
|---|---|---|---|
| 1 | Apache/Nginx/IIS kaynakları | §9 (devralınan 3 URL) | tekrar yok |
| 2 | durum kodu tanımları | `web-runtime-topolojisi/anayasa.md` §14 (RFC 9110) | burada tekrarlanmadı |
| 3 | yeni URL | bu dosyaya eklenmedi | **0** |

## §15 - Bu Tur Okunan Dosya Envanteri

| # | Dosya | Satır | Örnek |
|---|---|---|---|
| 1 | `home.coremusic.net/.htaccess` | 37 | E-1 |
| 2 | `home.coremusic.net/web.config` | 43 | §10.2 eşlemesi |
| 3 | `api.coremusic.net/.htaccess` | 42 | §12/3 |
| 4 | `api.coremusic.net/web.config` | 41 | E-2 |
| 5 | `auth.coremusic.net/.htaccess` | 37 | §12/2 |
| 6 | `auth.coremusic.net/index.php` | 238 | E-6 |
| 7 | `assets.coremusic.net/.htaccess` | 47 | E-7 |
| 8 | `shared/config/domain.php` | 19 | E-3 |
| 9 | `shared/src/PageRouter/PageRouterKernel.php` | 351 | E-4 |
| 10 | `api.coremusic.net/index.php` | 223 | E-5 · E-8 |

## §16 - Kanıt Bütünlüğü (bu tur)

| # | Kural | Sonuç |
|---|---|---|
| 1 | Satır referansları `[IO.File]::ReadAllLines()` ile doğrulandı | 10/10 dosya okundu |
| 2 | Uydurma satır no / istatistik | 0 |
| 3 | Yeni eklenen URL | 0 (§14) |
| 4 | Kod değişikliği | 0 — yalnız `.md` yazıldı |

## § OS GRUPLAR — sekiz işletim sistemi grubu: örnek yüzeyi karşılaştırması

> Bu bölüm, §2/§10'daki repo örneklerini işletim sistemi grubu ekseniyle karşılaştırır.
> Depoda yalnız Windows/IIS (`web.config`) ve POSIX/Apache (`.htaccess`) örnekleri vardır
> (§15 envanter); diğer gruplarda repo karşılığı olmadığı için örnek "(bu projede
> kullanılmıyor)" etiketiyle resmi dokümandan verilir.

### OS.1 — Karşılaştırma tablosu

| # | OS | Web sunucu/runtime topolojisi | Bu dosyadaki karşılığı | Kaynak |
|---|---|---|---|---|
| 1 | Windows | IIS: HTTP.sys → app-pool → worker process; PHP FastCGI handler | §2.3 IIS `web.config` · §10.2 E-2 | repo `home.coremusic.net/web.config:1-43` |
| 2 | macOS | gömülü Apache httpd — MPM + `.htaccess` | §2.1 `.htaccess` · §10.1 E-1 | `https://httpd.apache.org/docs/2.4/mod/core.html` (bu tur eklendi) |
| 3 | iOS | ayrı web sunucusu topolojisi bu projede tanımlı değil | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 4 | Android | ayrı web sunucusu topolojisi bu projede tanımlı değil | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 5 | Linux | nginx veya Apache + PHP-FPM FastCGI zinciri | §2.5 Nginx FastCGI yönlendirmesi | `https://raw.githubusercontent.com/php/php-src/master/sapi/fpm/www.conf.in` (bu tur eklendi) |
| 6 | FreeBSD/BSD | ports/paket Apache veya nginx — POSIX süreç modeli | §2.6 name-based vhost | `https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html` (mevcut kanıt) |
| 7 | Tizen | ayrı sunucu topolojisi yok (yerleşik web runtime) | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |
| 8 | webOS | ayrı sunucu topolojisi yok (yerleşik web runtime) | bu dosyada karşılığı yok (kapsam dışı) | repo taraması: bu projede kullanılmıyor |

### OS.2 — OS bazlı kısa açıklamalar (2-3 cümle)

**Windows.** Windows yüzeyinin örneği `web.config` bloklarıdır: beş rewrite kuralı ve
`customHeaders` katmanı §2.3/§10.2'de birebir taşınır. Aynı PHP uygulaması IIS FastCGI
handler'ında çalıştığı için örnekteki `.php` eşlemesi Windows'a özgüdür. Repo'da bu örneğin
dört kopyası vardır (§15 envanter).

**macOS.** macOS, gömülü Apache üzerinden aynı `.htaccess` örneklerini yürütebilir; çekirdek
direktifler (`Options`, `ServerSignature`) platformdan bağımsızdır (§2.1 · §10.1 E-1).
Depoda macOS'a özgü örnek yoktur → bu grup yalnız Apache örneğiyle temsil edilir.

**iOS.** Bu dosyada iOS için örnek yoktur — §15 envanterinde iOS'e ait dosya bulunmaz.
"(Bu projede kullanılmıyor)" etiketi geçerlidir; resmi platform dokümanı bu tur eklenmedi (⚠️).

**Android.** Android için de örnek yoktur; §15 envanteri yalnız sunucu config'i ve PHP
dosyalarını sayar. Depoda Android kanıtı yok → "(bu projede kullanılmıyor)"; resmi platform
dokümanı bu tur eklenmedi (⚠️).

**Linux.** Linux örneği nginx FastCGI yönlendirmesiyle (§2.5) ve PHP-FPM havuz config'iyle
temsil edilir; repo'da FPM havuz dosyası bulunmadığından havuz örneği "(bu projede
kullanılmıyor)" etikilidir. PHP'ye devir `fastcgi_pass` üzerinden yapılır (§2.5).

**FreeBSD/BSD.** FreeBSD ports Apache/nginx aynı name-based vhost örneğini (§2.6) ve FastCGI
modülünü kullanır — süreç modeli Linux ile aynıdır. Depoda BSD'ye özgü örnek yok →
"(bu projede kullanılmıyor)".

**Tizen.** Tizen'de web uygulamaları yerleşik web runtime'ında çalışır; bu dosyadaki sunucu
örnekleri Tizen'de geçerli değildir. Depoda Tizen kanıtı yok → "(bu projede kullanılmıyor)";
platform dokümanı bu tur eklenmedi (⚠️).

**webOS.** webOS'ta uygulamalar yerleşik web runtime'ında çalışır; `.htaccess`/`web.config`
örnekleri bu platformda kullanılmaz. Depoda webOS kanıtı yok → "(bu projede kullanılmıyor)";
platform dokümanı bu tur eklenmedi (⚠️).

### OS.3 — OS bazlı config örnekleri

> Blok 1 repo'da karşılığı olan tek örnektir ve `[IO.File]::ReadAllLines` ile 2026-10-14
> doğrulanmıştır; kalan üçü "(bu projede kullanılmıyor)" etiketiyle resmi dokümandan alınır.
> Not: §14/§16 sayımları önceki tur (2026-10-13) içindir — bu tur eklenen URL'ler frontmatter
> `evidence` satırına eklendi.

**1) POSIX/Apache — çekirdek direktifler (repo karşılığı VAR) ·
künye `home.coremusic.net/.htaccess:2-3` — `[IO.File]::ReadAllLines` ile 2026-10-14 doğrulandı ·
kaynak `https://httpd.apache.org/docs/2.4/mod/core.html` (2026-10-14 HTTP 200)**

```apache
Options -Indexes
ServerSignature Off
```

**2) Linux/FreeBSD — PHP-FPM havuzu (bu projede kullanılmıyor: repo'da havuz dosyası yok, R3) ·
kaynak `https://raw.githubusercontent.com/php/php-src/master/sapi/fpm/www.conf.in` (2026-10-14
HTTP 200 · php-src resmi pool template'i — `[www]` · `user =` · `group =` · `listen = 127.0.0.1:9000` ·
`pm = dynamic` · `pm.max_children = 5` satırları şablonda birebir mevcut; örnekteki socket yolu ve
`pm.max_children = 10` bu projeye uyarlanmış temsilî değerdir)**

```ini
[www]
user = ${USER_NAME:-www-data}
group = ${USER_NAME:-www-data}
listen = /var/run/php-fpm-${POOL_NAME}.sock
pm = dynamic                ; static | ondemand | dynamic (zorunlu alan)
pm.max_children = 10        ; örnek değer — dinamik havuz üst sınırı
```

**3) POSIX/Apache — PHP modülü kurulumu (bu projede kullanılmıyor: R3 belirsiz — mod_php mi FPM mi) ·
kaynak `https://www.php.net/manual/en/install.apache.php` (2026-10-14 HTTP 200 · "Apache 2.x on
Unix systems" bölümü)**

```apache
LoadModule php_module modules/libphp.so
<FilesMatch "\.php$">
    SetHandler application/x-httpd-php
</FilesMatch>
```

> Repo bağlantısı: `.htaccess:5-7` `php_flag` direktifleri yalnız mod_php'de etkilidir
> (R3 [⚠️ VERIFICATION REQUIRED]) — bu blok mod_php kurulumunun sunucu-yüzeyini gösterir.

**4) Windows — PHP + IIS FastCGI kurulumu (bu projede kullanılmıyor) ·
kaynak `https://www.php.net/manual/en/install.windows.iis.php` (2026-10-14 HTTP 200)**

```bat
rem 1) IIS FastCGI modülü (CGI rol servisi) kurulur
rem 2) .php icin handler mapping php-cgi.exe'ye yapilir (php.exe DEGIL)
rem 3) FastCGI handler icin NTS (Non-Thread Safe) PHP surumu kullanilir
rem 4) IIS yapilandirmasi appcmd araciyla betiklenir
rem NOT: bu projede kullanilmiyor — repo yuzeyi .htaccess (Apache) + web.config (IIS)
```

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
