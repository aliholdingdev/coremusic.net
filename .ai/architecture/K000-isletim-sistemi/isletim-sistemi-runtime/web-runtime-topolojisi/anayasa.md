---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi
evidence:
  - https://httpd.apache.org/docs/2.4/worker.html
  - https://httpd.apache.org/docs/2.4/event.html
  - https://httpd.apache.org/docs/2.4/prefork.html
  - home.coremusic.net/.htaccess:1-37
  - home.coremusic.net/web.config:1-43
  - shared/config/domain.php:3-19
validation: "2026-10-13 · 3 URL HTTP 200 (Apache 2.4 worker · event · prefork MPM dokümanları — bu tur envantere eklendi) · repo satırları Read ile birebir · içerik 380-460 satır"
open_questions:
  - "R1: Hedef runtime topolojisi kesin değil — Apache (mod_php/FPM) mı, IIS mi, ikisi de mi? Repo'da ikisinin de config'i var (çift hedef). [UNKNOWN]"
  - "R3: PHP Apache üzerinde mod_php mi, FPM mi çalışıyor? Repo'da FPM pool config'i yok; .htaccess php_flag yalnız mod_php'de etkilidir. [⚠️ VERIFICATION REQUIRED]"
  - "R7: Sunucu MPM'i (event/worker/prefork) veya FastCGI havuzu hangi kapasitede? [⚠️ VERIFICATION REQUIRED]"
group: mimari
---

# web-runtime-topolojisi-anayasa — Bağlayıcı Tanımlar ve İlkeler (Kademe-3)

## §0 ELI15

Web sunucusu, gelen isteği kabul eden **süreç modelini** kendisi seçer: Apache üç
MPM (prefork/worker/event) sunar, IIS app-pool içinde worker process çalıştırır,
nginx istekleri PHP-FPM'ye FastCGI ile devreder. CoreMusic deposunda **hem `.htaccess`
(Apache) hem `web.config` (IIS)** olduğu için hedef çifttir (R1). Bu anayasa altı
kavramı — **MPM · app-pool · FastCGI/FPM · çift hedef · vhost/topoloji · front
controller** — bağlayıcı biçimde tanımlar; yürütme ayrıntısı `-surucu`, repo yüzeyi
`-ornekler`dadır.

## §1 Kimlik

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi/anayasa` |
| node_type / status | `module` / `draft` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-runtime/web-runtime-topolojisi` |
| Kademe | Kademe-3 (bu dalın `index.md`'si ile aynı) |
| group | `mimari` (ADR-097 §5.5 · registry §8.1 — 9. FM alanı) |
| Bağımlılık | üst düğüm `web-runtime-topolojisi` → `isletim-sistemi-runtime` → `K000-isletim-sistemi` |
| Komşular | `surec-ve-parcacik` (süreç semantiği) · `baslangic-ve-bootstrap` (PHP boot) · `isletim-sistemi-api` (route) |
| Kapsam kaynağı | `isletim-sistemi-runtime-kapsam.md` §3 sunucu katmanı · §8 (R1/R3/R7) |
| Doğrulama kaynağı | `isletim-sistemi-runtime-dogrulama.md` §11 başlık eşleşme matrisi · §13 satır 4-6 |

## §2 Kavram modeli — altı omurga maddesi

### M1 — Apache MPM ailesi (prefork · worker · event)

| Alan | Tanım |
|---|---|
| Nedir | Apache httpd'in **süreç modeli** seçeneği: isteklerin süreç/parçacık ile nasıl eşlendiği |
| prefork | Çok süreç; her süreç tek bağlantıyla çalışır — parçacık yok, bellek izolasyonu yüksek |
| worker | Çok süreç + her süreçte çok parçacık; her parçacık bir istek işler |
| event | Worker gibi; keep-alive bağlantıları ayrı parçacıkta tutulur, iş parçacıkları serbest kalır |
| Kaynak | `https://httpd.apache.org/docs/2.4/worker.html` · `https://httpd.apache.org/docs/2.4/event.html` · `https://httpd.apache.org/docs/2.4/prefork.html` (2026-10-13 HTTP 200 · bu tur envantere eklendi) |
| Sınır | MPM **seçimi** sunucu kurulumundadır; repo'da kanıt yok — R7 [⚠️ VERIFICATION REQUIRED] |
| Bu repoda | `.htaccess` bu modelin üstünde çalışır; PHP hangi süreçte koşuyor R3'e bağlı |

### M2 — IIS app-pool ve worker process

| Alan | Tanım |
|---|---|
| Nedir | IIS'te uygulamaların çalıştığı **app-pool**; her app-pool bir ya da daha fazla worker process başlatır |
| Süreç modeli | Worker process'ler Windows süreç oluşturma API'si ile doğar; app-pool kimliği sürecin çalışma kimliğidir |
| Kilit olgu | `web.config` bu process'in içinde IIS katmanı için kurallar tanımlar |
| Kaynak | `https://learn.microsoft.com/en-us/windows/win32/procthread/creating-processes` (bu ağacın kök `index.md` Web Research bölümünde — 2026-10-13 HTTP 200) |
| Sınır | App-pool **kavramı** burada; havuz kimliği/identitiy ayarları `K018-devops` |
| Bu repoda | `web.config` 43 satır — çift hedefin IIS yüzeyi (R1) |

### M3 — nginx + PHP-FPM (FastCGI zinciri)

| Alan | Tanım |
|---|---|
| Nedir | nginx PHP'yi kendisi çalıştırmaz; istekleri FastCGI protokolüyle PHP-FPM havuzuna devreder |
| İstek sırası | vhost eşlemesi → rewrite → `fastcgi_pass` → FPM worker'ı |
| PATH_INFO | `fastcgi_split_path_info` regex'i ile yol ayrıştırılır (kök `-ornekler.md` §21) |
| Kaynak | `https://nginx.org/en/docs/http/request_processing.html` · `https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html` (bu ağacın kök `index.md` ve `-ornekler.md` Web Research bölümlerinde) |
| Sınır | FastCGI **kavramı** burada; nginx betiği `K018-devops` |
| Bu repoda | Repo'da nginx betiği yok; kavram çapraz hedef olarak taşınır |

### M4 — Çift hedef (.htaccess + web.config)

| Alan | Tanım |
|---|---|
| Nedir | Aynı uygulama dizininde **iki** sunucu yapılandırmasının birlikte bulunması |
| Repo kanıtı | `home.coremusic.net/.htaccess` (37 satır) + `home.coremusic.net/web.config` (43 satır) |
| İçerik eşlemesi | İkisi de: statik dosya pas · zararlı yollar 403 · her şey `index.php`'ye rewrite |
| Sonuç | Üretimde hangisinin aktif olduğu **okunamaz** → R1 [UNKNOWN] |
| Kural | "Yalnız Apache" ya da "yalnız IIS" iddiası bu ağaçta yazılmaz |
| Sınır | Tekilleştirme kararı `K018-devops` + kullanıcı onayı |

### M5 — Vhost ve subdomain topolojisi

| Alan | Tanım |
|---|---|
| Nedir | Sunucunun host başlığına göre hangi dizini servis ettiği (name-based vhost) |
| Repo kanıtı | `shared/config/domain.php:3-19` — 7 subdomain + `home` için port 81 istisnası |
| Apache karşılığı | Her subdomain `httpd.conf` içinde ayrı sanal sunucu bloğu; her sunucu yalnız kendi adresi/portuna cevap verir (kök `-ornekler.md` §21 name-based) |
| Dizin boşlukları | `music` ve `admin` için depoda karşılık gelen dizin yok → o uçlar için runtime [UNKNOWN] (R4) |
| Sınır | Domain listesi sahipliği `shared/AGENTS.md` kuralı — onaysız değişmez |
| Not | `assets` uç statik mi servis ediyor — R8 [UNKNOWN] |

### M6 — Front controller ve AllowOverride sınırı

| Alan | Tanım |
|---|---|
| Nedir | Tek giriş noktası deseni: gerçek dosya/dizin değilse her istek `index.php`'ye gider |
| Repo kanıtı | `.htaccess:26-37` rewrite bloğu · `web.config:14-41` beş kural |
| Apache sınırı | `.htaccess` yalnız `AllowOverride` izin verilen direktiflerde etkindir (kök `-kapsam.md` §12 · howto/htaccess bu dosyanın `-surucu.md` §7'sinde) |
| IIS sınırı | `web.config` URL Rewrite modülü kurulu değilse kurallar çalışmaz |
| Kural | Yapılandırma katmanı sunucu içidir; uygulama kodu bu katmana bağımlı yazılır ama **okumaz** |
| Ayrım | Uygulama davranışının tek sahibi `index.php` zinciridir |

### §2.1 — Bu dalın kavram → dosya dağılımı

| # | Kavram | Gittiği dosya | Bölüm |
|---|---|---|---|
| 1 | MPM üçlüsü · süreç/parçacık modeli | `-anayasa` (bu dosya) | §2 M1 · §3 A1 |
| 2 | IIS app-pool · worker process | `-anayasa` + `-surucu` | §2 M2 · §3 A2 |
| 3 | nginx/FastCGI/FPM | `-anayasa` + `-surucu` | §2 M3 · §3 A3 |
| 4 | Çift hedef kuralı | `-anayasa` + `-ornekler` | §2 M4 · §3 A4 |
| 5 | Vhost/subdomain topolojisi | `-anayasa` + `-ornekler` | §2 M5 · §3 A5 |
| 6 | Front controller / AllowOverride | `-anayasa` + `-ornekler` | §2 M6 · §3 A4 |

### §2.2 — Platform yansımaları (kavram sabit, ürün değişir)

| Kavram | Apache | IIS | nginx |
|---|---|---|---|
| Yapılandırma | `httpd.conf` + `.htaccess` | `web.config` | `nginx.conf` + vhost |
| Süreç modeli | MPM (prefork/worker/event) | app-pool + worker process | master + worker süreç |
| Rewrite | `mod_rewrite` | URL Rewrite | `rewrite` direktifleri |
| PHP devri | mod_php **ya da** FastCGI/FPM | FastCGI handler | `fastcgi_pass` → FPM |
| Güvenlik başlığı | `mod_headers` | `customHeaders` | `add_header` |
| Repo yüzeyi | `.htaccess` (37) | `web.config` (43) | yok |

## §3 Mekanizma akışı — beş olay zinciri (A1-A5)

### A1 — Apache istek akışı

| # | Adım | Ne olur | Katman |
|---|---|---|---|
| 1 | Bağlantı | MPM process/parçacığı bağlantıyı kabul eder | M1 |
| 2 | Vhost eşleme | Host başlığına göre sanal sunucu seçilir | M5 |
| 3 | `.htaccess` | AllowOverride kapsamında direktifler uygulanır | M6 |
| 4 | Rewrite | Üç katmanlı zincir: gerçek dosya → statik → 403 → `index.php` | `-ornekler` §2 E-2 |
| 5 | PHP devri | mod_php **ya da** FastCGI/FPM (R3) | M1 · M3 |
| 6 | Boot | `baslangic-ve-bootstrap` boot sırası | komşu dal |

### A2 — IIS istek akışı

| # | Adım | Ne olur | Katman |
|---|---|---|---|
| 1 | Dinleyici | HTTP.sys isteği app-pool'a yönlendirir | M2 |
| 2 | Worker process | App-pool'un worker process'i işler | M2 |
| 3 | `web.config` | `system.webServer` blokları uygulanır | M4 |
| 4 | Rewrite | Beş kural sırayla: statik → 403 ×2 → SPA Router → `index.php` | `-ornekler` §2 E-3 |
| 5 | Başlıklar | `customHeaders` yazılır (X-Powered-By kaldırılır) | `-ornekler` §2 E-4 |
| 6 | PHP devri | FastCGI handler (repo'da kanıt yok — R3) | M3 |

### A3 — nginx + FPM istek akışı

| # | Adım | Ne olur | Kaynak |
|---|---|---|---|
| 1 | Vhost eşleme | `server_name` ile sunucu bloğu seçilir | request_processing (kök `index.md`) |
| 2 | Rewrite | Uygulama kuralları uygulanır | nginx docs |
| 3 | PATH_INFO | `fastcgi_split_path_info` regex'i yol parçalar | ngx_http_fastcgi_module (kök `-ornekler.md` §21) |
| 4 | Devir | `fastcgi_pass` ile FPM havuzuna iletim | M3 |
| 5 | FPM worker | Havuz süreci PHP'yi çalıştırır | `install.fpm` (`-surucu.md` §7) |
| 6 | Sonuç | Yanıt geri akar | — |

### A4 — Çift hedef karşılaştırma zinciri (repo)

| # | Davranış | `.htaccess` | `web.config` |
|---|---|---|---|
| 1 | Statik dosya pas | `RewriteCond -f/-d` + `[L]` | kural 1 `action None` |
| 2 | Dizin listeleme | `Options -Indexes` (satır 2) | — (IIS varsayılanı) |
| 3 | Yasaklı dizin | `^(config|include|pages)` → `[F,L]` | kural 2 `403` |
| 4 | Hassas dosya | `<FilesMatch>` `Require all denied` (18-24) | kural 3 `403` |
| 5 | Front controller | `^(.*)$ index.php [QSA,L]` | kural 5 `Rewrite to index.php` |
| 6 | Başlıklar | `<IfModule mod_headers.c>` (9-16) | `<customHeaders>` (4-13) |

### A5 — Subdomain eşleme zinciri

| # | Adım | Ne olur | Repo sargısı |
|---|---|---|---|
| 1 | Host başlığı | İstek `X` .coremusic.net | `domain.php:5-13` |
| 2 | Eşleme | 7 subdomain tanımı | `domain.php:7-13` |
| 3 | Port | `home` → 81 istisnası | `domain.php:15-17` |
| 4 | Dizin | Subdomain dizinine git | dizin yoksa runtime [UNKNOWN] (R4) |
| 5 | Boot | `index.php` → boot zinciri | `baslangic-ve-bootstrap` |

## §4 Ölçüm / eşik — bu dalın ölçüm sözleşmesi

| # | Ne ölçülür | Araç / yöntem | Eşik | Durum |
|---|---|---|---|---|
| 1 | Aktif sunucu hedefi | sunucu log/başlığı (X-Powered-By yok) | tek hedef | UNKNOWN (R1) |
| 2 | MPM seçimi | `httpd -V` / sunucu raporu | R7 [⚠️] | UNKNOWN |
| 3 | FPM havuzu kapasitesi | `www.conf` (repo'da yok) | — | UNKNOWN (R3/R7) |
| 4 | Rewrite katmanı | `.htaccess:26-37` + `web.config:14-41` | iki katman | OKU (2026-10-13) |
| 5 | 403 filtresi | FilesMatch + 2 CustomResponse | her ikisinde de | OKU (2026-10-13) |
| 6 | Subdomain sayısı | `domain.php` | 7 | OKU (2026-10-13) |
| 7 | Port istisnası | `domain.php:16` | `home` → 81 | OKU (2026-10-13) |
| 8 | Dizin boşluğu | `music`/`admin` dizinleri | runtime [UNKNOWN] | OKU (2026-10-13) |

> Ölçüm kuralı: eşik değeri uydurulmaz; değer üretilemiyorsa hücre `UNKNOWN` kalır
> (`-dogrulama` §14 adım 9 satır indeksi ile doğrulanır).

## §5 Komşu sınırı

| Konu | Gittiği dal | Bu daldaki karşılığı |
|---|---|---|
| Süreç/parçacık semantiği (fork, pthread) | `surec-ve-parcacik` | süreç **modeli** kalır, semantik gitmez |
| PHP boot sırası, ini, OPcache | `baslangic-ve-bootstrap` | sunucu → PHP **devri** kalır, boot gitmez |
| Route tablosu ve dispatch | `isletim-sistemi-api` | front controller **kapısı** kalır |
| Güvenlik başlığı politikası | `isletim-sistemi-security` | başlık **yazımı** burada, politika orada |
| Erişim logu toplama | `isletim-sistemi-observability` | sunucu logu **çıkışı** burada |
| Sunucu kurulumu, deploy | `K018-devops` | topoloji **tanımı** kalır |
| Domain listesi değişikliği | `shared/AGENTS.md` (onay) | eşleme **kavramı** kalır |
| Test ortamı sunucu | `isletim-sistemi-testing` | senaryo doküman düzeyinde |

## §6 Terim sözlüğü

| Terim | Tanım |
|---|---|
| **MPM** | Apache'nin istek süreç/parçacık modeli (prefork/worker/event) |
| **prefork** | Parçaksız, çok süreçli MPM |
| **worker** | Çok süreç + çok parçaklı MPM |
| **event** | Keep-alive'ı ayrı parçacağa taşıyan MPM |
| **app-pool** | IIS'te uygulamanın çalıştığı süreç havuzu |
| **worker process** | IIS app-pool'unun iş süreci |
| **FastCGI** | Sunucu ↔ uygulama arası CGI'nin hızlı, süreç-içi bağlantıları yeniden kullanan sürümü |
| **PHP-FPM** | FastCGI konuşan PHP süreç yöneticisi |
| **FPM havuzu** | FPM'nin kaç process/parçacıkla çalıştığını tanımlayan yapılandırma |
| **Çift hedef** | Aynı dizinde hem `.htaccess` hem `web.config` bulunması (R1) |
| **name-based vhost** | Host başlığı ile eşleşen sanal sunucu bloğu |
| **Front controller** | Tüm dinamik isteklerin gittiği tek giriş noktası (`index.php`) |
| **AllowOverride** | `.htaccess` direktiflerinin okunup okunmayacağını belirleyen sunucu ayarı |
| **Rewrite zinciri** | Sıralı yönlendirme kuralları dizisi |
| **SPA Router** | `web.config` kural 4 — gerçek dosya/dizinse dokunmaz |
| **PATH_INFO** | `/index.php/alt/yol` biçimindeki yol artığı |
| **Subdomain portu** | Alt alanın farklı porttan servis edilmesi (`home` → 81) |
| **X-Powered-By** | PHP/sunucu kimlik başlığı — her iki katmanda da kaldırılır |
| **SAPI** | PHP'nin sunucu arayüz katmanı (R3 belirsizliği burada) |

## §7 Kapsam dışı — bu dosyaya yazılmaz

| # | Konu | Nereye |
|---|---|---|
| 1 | Süreç/parçacık semantiği | `surec-ve-parcacik` |
| 2 | Boot sırası, ini/OPcache uygulaması | `baslangic-ve-bootstrap` |
| 3 | Apache/nginx kurulum betikleri | `K018-devops` |
| 4 | Route/dispatch kodu | `isletim-sistemi-api` |
| 5 | Güvenlik başlığı politikası yazımı | `isletim-sistemi-security` |
| 6 | Log toplama/analiz | `isletim-sistemi-observability` |
| 7 | Domain listesi değişikliği onayı | `shared/AGENTS.md` kuralı |
| 8 | Repo somut örnekleri ve anti-pattern listesi | bu dalın `-ornekler.md` dosyası |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R1 | Üretim runtime'ı Apache mi, IIS mi? (her ikisinin config'i de var) | [UNKNOWN] |
| R3 | FPM mi, Apache mod_php mi? (repo'da FPM pool config'i yok) | [⚠️ VERIFICATION REQUIRED] |
| R7 | Sunucu MPM'i veya FastCGI havuzu hangi kapasitede? | [⚠️ VERIFICATION REQUIRED] |

## §9 Kaynakça — 3 URL, HTTP 200, 2026-10-13

| # | Kaynak | URL | Durum |
|---|---|---|---|
| 1 | worker MPM — süreç + parçacık modeli | https://httpd.apache.org/docs/2.4/worker.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 2 | event MPM — keep-alive ayrıştırması | https://httpd.apache.org/docs/2.4/event.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |
| 3 | prefork MPM — parçaksız çok süreçli model | https://httpd.apache.org/docs/2.4/prefork.html | 2026-10-13 HTTP 200 · **bu tur envantere eklendi** |

> MPM ana sayfası (`mpm.html`), CreateProcessW, nginx request_processing ve IIS
> system.webServer kaynakları bu ağacın kök `index.md` Web Research bölümünde;
> mod_rewrite ve fastcgi kaynakları kök `-ornekler.md` §21'indedir — tekrarlanmaz.

## §10 — Kural Eşlemesi: Apache `.htaccess` ↔ IIS `web.config` (repo disk kanıtı)

> Her iki dosya da 2026-10-13'te satır satır okundu (`home`: 37/43 satır · `api`: 42/41 satır).

### §10.1 `home.coremusic.net` eşlemesi

| # | Kural | `.htaccess` satır | `web.config` satır | Eşleşen davranış |
|---|---|---|---|---|
| 1 | dizin listeleme kapalı | 2 `Options -Indexes` | — | IIS'te karşılığı config düzeyinde |
| 2 | imza gizle | 3 `ServerSignature Off` | 6 `<remove name="X-Powered-By" />` | ikisi de teşhisi azaltır |
| 3 | hata günlüğü | 5-7 `display_errors off` · `log_errors on` · `error_log` | — | PHP INI katmanı |
| 4 | güvenlik başlıkları | 10-15 | 7-11 | `X-Content-Type-Options`, `X-Frame-Options`, `X-XSS-Protection`, `Referrer-Policy` |
| 5 | hassas dosya yasağı | 18-20 `FilesMatch` uzantı listesi | 24-27 `Block sensitive files` | uzantı listeleri **farklı**: IIS listesinde `DS_Store` yok |
| 6 | PHP doğrudan erişim yasağı | 22-24 `^(?!index\.php$)(?!debug\.php$).+\.php$` | — | `debug.php` izni yalnız Apache'de |
| 7 | statik dosya passthrough | 30-32 `-f/-d` → `[L]` | 30-35 `IsFile/IsDirectory` → `None` | aynı semantic |
| 8 | statik uzantı passthrough | 34 | 16-19 `Static files` | uzantı listesi birebir aynı |
| 9 | dizin engelleme | 35 `^(config\|include\|pages)` → `[F,L]` | 20-23 `403` | `[F]` = 403 |
| 10 | SPA rewrite | 36 `index.php [QSA,L]` | 36-39 `Rewrite` + `appendQueryString` | `QSA` ↔ `appendQueryString="true"` |

### §10.2 `api.coremusic.net` eşlemesi

| # | Kural | `.htaccess` satır | `web.config` satır | Fark (home ile karşılaştırma) |
|---|---|---|---|---|
| 1 | Referrer-Policy | 13 `no-referrer` | 8 `no-referrer` | home: `strict-origin-when-cross-origin` |
| 2 | `X-Content-Type-Options` | 11 | **yok** | IIS API kuralında başlık eksik |
| 3 | `X-XSS-Protection` | 12 | **yok** | IIS API kuralında başlık eksik |
| 4 | hassas dosya yasağı | 19-21 | 18-21 | IIS listesine `example` eklenmiş |
| 5 | PHP doğrudan erişim yasağı | 24 `^(?!index\.php$).+\.php$` | **yok** | Apache'de var, IIS'te yok |
| 6 | dizin engelleme | 38 `^(config\|include)` | 22-25 `config\|include\|tests\|vendor` | IIS daha geniş |
| 7 | statik passthrough | 33-35 | 14-17 | aynı |
| 8 | rewrite | 41 | 34-37 | `stopProcessing="true"` yalnız IIS'te |

> **Rapor (düzeltme yok):** sekiz satırın 2, 3, 5 ve 6'sında Apache/IIS kuralları **eşleşmiyor**.
> Bu bir güvenlik-topoloji gözlemidir; karar `isletim-sistemi-security` dalının yetkisindedir.

## §11 — Alt Alan Adı ve Port Topolojisi (`shared/config/domain.php` — 19 satır)

| # | Anahtar | Değer | Satır |
|---|---|---|---|
| 1 | `primary` | `coremusic.net` | 4 |
| 2 | `primary_port` | `80` | 5 |
| 3 | `subdomain_port` | `80` | 6 |
| 4 | `auth` | `auth.coremusic.net` | 8 |
| 5 | `home` | `home.coremusic.net` | 9 |
| 6 | `assets` | `assets.coremusic.net` | 10 |
| 7 | `music` | `music.coremusic.net` | 11 |
| 8 | `admin` | `admin.coremusic.net` | 12 |
| 9 | `media` | `media.coremusic.net` | 13 |
| 10 | `api` | `api.coremusic.net` | 14 |
| 11 | `subdomain_ports.home` | **81** | 16-18 |

**Topoloji anlamı:** yedi alt alan adı + birincil alan; `home` için **özel port (81)** tanımı var,
diğerleri varsayılan `80`'de. Dosya `return [...]` olarak döndürüldüğünden okunma anı PHP
bootstrap'idir (bkz. `baslangic-ve-bootstrap` dalı) — DNS/hosts düzeyi **bu dosyanın kanıtı değildir**.

## §12 — Erken İstek Yaşam Döngüsü (sunucu → kernel)

| # | Adım | Katman | Kanıt |
|---|---|---|---|
| 1 | istek gelir | Apache rewrite / IIS rewrite | §10 tabloları |
| 2 | statik ise doğrudan serve | sunucu | §10.1/7-8 |
| 3 | gizli dizin ise 403 | sunucu | §10.1/9 (`[F,L]` ↔ `403`) |
| 4 | geri kalan `index.php` | front controller | §10.1/10 |
| 5 | bootstrap + kernel çağrısı | PHP | `baslangic-ve-bootstrap` dalı |
| 6 | traceId üretilir | `PageRouterKernel.php:87-88` · `:344-350` | UUID biçiminde 16 bayt |
| 7 | middleware yığını kurulur | `:296-303` `MiddlewarePipeline` | `pipe` → `run` |
| 8 | rota çözümü + render | `:67-76` `PageRouter` | `PageCacheAdapter` dâhil |
| 9 | yanıt yayılır | `ResponseEmitter` (`:29`) | — |
| 10 | hata | `ErrorHandler` (`:30`, `:58`) | shell renderer ile |

## §13 — Varsayılan Middleware Sırası (`PageRouterKernel.php:322-342`)

| # | Sıra | Middleware | Satır | Görev |
|---|---|---|---|---|
| 1 | 1 | `OriginCheckMiddleware` | 331 | origin doğrulama (`$isProduction`) |
| 2 | 2 | `CorsMiddleware` | 332 | CORS |
| 3 | 3 | `RateLimiterMiddleware` | 333 | `RATE_LIMIT_MAX = 60` / `WINDOW = 60` (satır 24-25) |
| 4 | 4 | `SecurityHeadersMiddleware` | 334 | başlıklar |
| 5 | 5 | `SessionManagerMiddleware` | 335 | oturum |
| 6 | 6 | `CsrfMiddleware` | 336 | CSRF |
| 7 | 7 | `BypassAuthMiddleware` | 337 | bypass (ADR-010) |
| 8 | 8 | `AuthMiddleware` | 338 | kimlik |
| 9 | 9 | `PermissionMiddleware` | 339 | yetki |
| 10 | 10 | `ValidationMiddleware` | 340 | doğrulama |

> Sıra **bağlayıcıdır**: dıştan içe doğru daralır (önce ağ/origin, sonra oturum, sonra kimlik,
> en sonda doğrulama). `APP_ENV_MODE` tanımlı değilse `development` (satır 327-328).

## §14 — Durum Kodu Anayasası (RFC 9110 — 2026-10-13 doğrulandı)

| # | Kod | Bölüm | Doğrulanmış metin | Bu ağaçtaki karşılığı |
|---|---|---|---|---|
| 1 | 200 | §15.3.1 | "The 200 (OK) status code indicates that the request has succeeded." | başarılı rota |
| 2 | 301 | §15.4.2 | "the target resource has been assigned a new permanent URI" | kalıcı yönlendirme |
| 3 | 403 | §15.5.4 | "the server understood the request but refuses to fulfill it" | gizli dizin/uzantı engeli (§10.1/9) |
| 4 | 404 | §15.5.5 | "the origin server did not find a current representation for the target resource" | bilinmeyen rota |
| 5 | 500 | §15.6.1 | "the server encountered an unexpected condition that prevented it from fulfilling the request" | beklenmeyen hata |
| 6 | 501 | §15.6.2 | "the server does not support the functionality required to fulfill the request" | desteklenmeyen metod |
| 7 | 502 | §15.6.3 | "while acting as a gateway or proxy, received an invalid response from an inbound server" | upstream hatası |
| 8 | 503 | §15.6.4 | "currently unable to handle the request due to a temporary overload or scheduled maintenance" | aşırı yük / bakım |

## §15 — URL Envanti̇eri (bu tur eklenen)

| # | URL | Kullanım | Durum |
|---|---|---|---|
| 1 | https://www.rfc-editor.org/rfc/rfc9110 | §14 (sekiz durum kodu) | 2026-10-13 HTTP 200 · **bu tur eklendi** |
| 2 | https://www.rfc-editor.org/rfc/rfc9112 | §15.1 | 2026-10-13 HTTP 200 · **bu tur eklendi** |

### §15.1 HTTP/1.1 referans durumu (RFC 9112)

> "This document obsoletes portions of RFC 7230." — RFC 9112 başlık sayfası (2026-10-13 okundu).
> Bu nedenle eski 7230 referansı **ikincil** sayılır; bu ağaçta 7230 yalnız tarihsel not olarak geçer.

## §16 — Negatif Kanıt ve Ölçüm

| # | Aranan | Yöntem | Sonuç |
|---|---|---|---|
| 1 | FPM/MPM havuz dosyası (`.conf`) | repo taraması | **yok** → R7 [⚠️ VERIFICATION REQUIRED] |
| 2 | sunucu process sayısı değeri | repo taraması | **yok** → [UNKNOWN] |
| 3 | `PermitOverride`/`AllowOverride` | repo taraması | **yok** → Apache sunucu düzeyi |
| 4 | `.htaccess` sayısı | `Get-ChildItem -Recurse -Filter .htaccess` | **4** (`api` · `assets` · `auth` · `home`) |
| 5 | `web.config` sayısı | `Get-ChildItem -Recurse -Filter web.config` | **4** (aynı dört alt alan) |
| 6 | dolu satır | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |
| 7 | mojibake | `[IO.File]::ReadAllLines($f)` UTF-8 | 0 |

> **Düzeltme kaydı (2026-10-13):** bu tablo ilk yazımında "2 / 2" idi; sayım 4/4 çıktı —
> sayı uydurulmadı, düzeltildi. §10 yalnız `home` ve `api` çiftini kapsar; §17 diğer ikisini verir.

## §17 — Kalan İki Alt Alan (disk kanıtı)

### §17.1 `auth.coremusic.net` (`.htaccess` — 37 satır · `web.config` — mevcut)

| # | Kural | Satır | `home` ile fark |
|---|---|---|---|
| 1 | başlıklar (X-Frame/XCTO/XSS/Referrer) | 10-15 | birebir aynı |
| 2 | hassas uzantı yasağı | 18-20 | birebir aynı |
| 3 | PHP doğrudan erişim yasağı | 22 `^(?!index\.php$).+\.php$` | home'da `debug.php` izni **var**, auth'ta **yok** |
| 4 | dizin engelleme | 35 `^(config\|include)` | home'da `pages` de engelli |
| 5 | rewrite | 36 | aynı |

### §17.2 `assets.coremusic.net` (`.htaccess` — 47 satır)

| # | Kural | Satır | Değer |
|---|---|---|---|
| 1 | font CORS | 6-10 | `Access-Control-Allow-Origin: *` + Methods/Headers |
| 2 | görsel CORS | 13-15 | `*` |
| 3 | CSS CORS + cache | 18-21 | `max-age=3600` |
| 4 | JS no-cache | 23-31 | yorum gerekçesi dosyada: hash/bundler yok → bayat modül karışımı |
| 5 | font immutable cache | 34-36 | `max-age=31536000, immutable` |
| 6 | görsel cache | 37-39 | `max-age=604800` |
| 7 | güvenlik başlıkları | 42-43 | `nosniff` · `X-Frame-Options: SAMEORIGIN` |
| 8 | dizin listeleme | 47 | `Options -Indexes` |

**Topoloji sonucu:** dört alt alanın dördünde de `Options -Indexes` ve başlık/uzantı katmanı
vardır; `assets` tek başına CORS + cache odaklıdır ve **rewrite/front-controller kuralı taşımaz**
(036/041 gibi satırlar yok) — dosyanın 47 satırı tamamen başlık/cache yönetimine ayrılmıştır.

## §18 — Dosya Kaynak Özeti ve Doğrulama (bu tur)

| # | Kaynak | Tür | Satır/ölçü | Bu dosyada |
|---|---|---|---|---|
| 1 | `home.coremusic.net/.htaccess` | repo | 37 | §10.1 |
| 2 | `home.coremusic.net/web.config` | repo | 43 | §10.1 |
| 3 | `api.coremusic.net/.htaccess` | repo | 42 | §10.2 |
| 4 | `api.coremusic.net/web.config` | repo | 41 | §10.2 |
| 5 | `auth.coremusic.net/.htaccess` | repo | 37 | §17.1 |
| 6 | `assets.coremusic.net/.htaccess` | repo | 47 | §17.2 |
| 7 | `shared/config/domain.php` | repo | 19 | §11 |
| 8 | `shared/src/PageRouter/PageRouterKernel.php` | repo | 351 | §12-§13 |
| 9 | RFC 9110 (§15.3.1 · §15.4.2 · §15.5.4-5 · §15.6.1-4) | web | 2026-10-13 | §14 |
| 10 | RFC 9112 (obsoletes 7230) | web | 2026-10-13 | §15.1 |

**Tekrarlanabilir doğrulama (repo kökünden):**

| # | Komut | Beklenen |
|---|---|---|
| 1 | `Get-ChildItem -Recurse -Filter .htaccess` (vendor hariç) | 4 |
| 2 | `Get-ChildItem -Recurse -Filter web.config` (vendor hariç) | 4 |
| 3 | `Select-String -Path 'shared\config\domain.php' -Pattern '=>'` | 13 |
| 4 | `Select-String -Path 'shared\src\PageRouter\PageRouterKernel.php' -Pattern 'new \w+Middleware'` | 10 |
| 5 | `(Get-Content $f \| Measure-Object -Line).Lines` | ≥380 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode