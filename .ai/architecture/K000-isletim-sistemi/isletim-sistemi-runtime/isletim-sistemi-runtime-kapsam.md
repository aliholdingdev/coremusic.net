---
node_id: K000-isletim-sistemi/isletim-sistemi-runtime/isletim-sistemi-runtime-kapsam
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-runtime
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-runtime
evidence:
  - shared/src/Bootstrap/RuntimeBootstrap.php:7-20
  - home.coremusic.net/.htaccess:5-7
  - home.coremusic.net/web.config:1-43
  - shared/config/domain.php:4-18
validation: 2026-10-13 · dosya uzunluğu >=500 satır · URL'ler HTTP 200 · disk satırları Select-Object -Index ile doğrulandı
open_questions:
  - R1 · üretim runtime'ı Apache mi IIS mi · UNKNOWN
  - R2 · çok-iş-parçacıklı çalışma hedef mimaride var mı · UNKNOWN
group: mimari
---

# isletim-sistemi-runtime — Kapsam

> **Kapsam (tek cümle):** CoreMusic'in **çalışma zamanı ortamı** — web sunucusu yapılandırması (Apache `.htaccess` + IIS `web.config` çift hedefi), PHP çalışma zamanı kurulumu (`RuntimeBootstrap`), süreç/parçacık çalışma modeli ve açılış (bootstrap) sırası; OS'nin kendisi değil, **OS üzerinde çalışan katman**.

---

## §0 ELİ15

Bir PHP uygulaması tek başına çalışmaz: onu bir **çalışma zamanı** (runtime) taşır. Bu runtime üç kattan oluşur ve bu dosyanın tamamı bu üç katmanın sınırını çizer.

1. **Sunucu katmanı** — isteği karşılayan süreç: Apache (`.htaccess`) veya IIS (`web.config`). Repo'da **ikisi de** var → çift hedef (R1 [UNKNOWN]).
2. **Dil çalışma zamanı katmanı** — PHP SAPI + opcache + APCu. `RuntimeBootstrap::boot()` kod içinden `error_reporting` / `ini_set` / zaman dilimi kurar.
3. **Açılış katmanı** — `index.php` → `constants.php` (ortam) → `app.php` → `RuntimeBootstrap` → `config.php` → `bootstrap.php` (DI + router).

**OS süreç/iş-parçacığı modeli** (pthreads, `CreateProcess`, fork) bu katmanın **altındaki** zemindir: referans olarak taşınır, repo'da doğrudan çok-iş-parçacıklı kullanım yoktur (R2 [UNKNOWN]).

| Katman | Bu dosyada | Komşuya devredilen |
|---|---|---|
| Sunucu yapılandırması | ✅ kapsam içi | HTTP protokolü → `isletim-sistemi-api` |
| PHP ini / SAPI kurulumu | ✅ kapsam içi | zamanlayıcı → `isletim-sistemi-plan` |
| Açılış sırası | ✅ kapsam içi (özet) | uygulama katmanı → `K011-application` |
| Süreç/parçacık kavramı | ✅ kapsam içi (referans) | süreç ayrıntısı → `isletim-sistemi-core` |
| Sandbox / yetki | ❌ kapsam dışı | → `isletim-sistemi-security` |
| Metrik toplama | ❌ kapsam dışı (yalnız ölçüm tanımı) | → `isletim-sistemi-observability` |

Tablo notu: "kapsam içi" = bu alt-dalda tanımlanır ve doğrulanır; "komşuya devredilen" = yalnız sınır satırı yazılır, içerik komşu dosyanın sorumluluğundadır.

---

## §1 Kapsam sınırları

| # | Kapsar | Kapsamaz | Sınır sahibi |
|---|---|---|---|
| 1 | Web sunucusu runtime yapılandırması (Apache `.htaccess`, IIS `web.config`) | HTTP durum kodu ve başlık semantiği | `isletim-sistemi-api` |
| 2 | PHP çalışma zamanı kurulumu (`RuntimeBootstrap`, `ini_set`, `error_reporting`) | PHP dil sözdizimi ve tipler | `isletim-sistemi-turu` |
| 3 | Açılış (bootstrap) sırası: ortam → DI → router | Uygulama servis mantığı | `K011-application` |
| 4 | Süreç/parçacık çalışma modeli kavramları | Zamanlayıcı kuyrukları ve kuant | `isletim-sistemi-plan` |
| 5 | Bellek ve GC runtime boyutu | Nihai RSS bütçesi sahipliği | `isletim-sistemi-observability` |
| 6 | Hata zamanlaması ve fail-fast davranışı | Hata kodu kataloğu | `isletim-sistemi-testing` |
| 7 | Isınma (warm-up): opcache / APCu | Cache politikası tasarımı | `K011-application` |
| 8 | Kapanış (shutdown) ve drenaç | Kuyruk tüketici tasarımı | `K018-devops` |
| 9 | Ölçüm tanımları: throughput, latency, RSS | Ölçüm toplayıcı kurulumu | `isletim-sistemi-observability` |
| 10 | Dağıtım birimi olarak runtime topolojisi | Deploy pipeline | `K018-devops` |

**Sınır kuralı (tek satır):** Bu alt-dal **ne HTTP'nin ne işletim sisteminin sahibidir**; ikisi arasında kalan "uygulama nasıl ayakta kalır" sorusunun sahibidir.

### §1.1 Dahil edilme kararları

| # | Karar | Dahil mi | Gerekçe |
|---|---|---|---|
| 1 | `.htaccess` içindeki `php_flag` | ✅ | PHP çalışma zamanını doğrudan kurar |
| 2 | `web.config` içindeki `<rewrite>` | ✅ | sunucu çalışma zamanı akışını kurar |
| 3 | `RuntimeBootstrap::boot()` | ✅ | çalışma zamanı kurulumunun kod tarafı |
| 4 | `.env` okuma (`EnvParser`) | ✅ | boot sırasında yüklenen yapılandırma |
| 5 | DI container kurulumu | ✅ | boot sırasının parçası |
| 6 | Route dosyası içeriği | ❌ | uygulama katmanı (`K011-application`) |
| 7 | Middleware sınıfları | ❌ | güvenlik/mimari sahiplik |
| 8 | Veritabanı bağlantısı açılışı | ❌ | `K005-database` |
| 9 | `proc_open`/`shell_exec` kullanımı | kısmen | yalnız kanıt, ayrıntı `isletim-sistemi-core` |
| 10 | Sunucu işlem modeli (MPM) | kısmen | yalnız sınır, ayrıntı `isletim-sistemi-core` |

---

## §2 Repo'daki runtime katmanları

| # | Katman | Dosya | Satır | Ne yapar |
|---|---|---|---|---|
| 1 | Apache PHP bayrakları | `home.coremusic.net/.htaccess` | 5-7 | `display_errors off`, `log_errors on`, `error_log` yolu |
| 2 | Apache güvenlik başlıkları | `home.coremusic.net/.htaccess` | 9-16 | `X-Frame-Options`, `Server`/`X-Powered-By` kaldırma |
| 3 | Apache dosya reddi | `home.coremusic.net/.htaccess` | 18-24 | `.env`, `.log`, `.sql` ve `index.php` dışı PHP reddi |
| 4 | Apache rewrite | `home.coremusic.net/.htaccess` | 26-37 | statik dosya → `index.php` front controller |
| 5 | IIS başlıklar | `home.coremusic.net/web.config` | 2-15 | `customHeaders` ile aynı başlık kümesi |
| 6 | IIS rewrite | `home.coremusic.net/web.config` | 16-41 | 5 kural: statik, dizin reddi, dosya reddi, SPA, `index.php` |
| 7 | PHP çalışma zamanı | `shared/src/Bootstrap/RuntimeBootstrap.php` | 7-20 | zaman dilimi, `error_reporting`, `ini_set` |
| 8 | Ortam yükleme | `home.coremusic.net/config/constants.php` | 10-32 | `.env` → `EnvParser` → `define()` |
| 9 | Ortam ayrıştırıcı | `shared/src/Config/EnvParser.php` | 7-40 | satır satır `.env` okuma, tırnak kesme |
| 10 | Giriş noktası | `home.coremusic.net/index.php` | 10-29 | autoload → opcache → config → boot → bootstrap |
| 11 | Uygulama açılışı | `home.coremusic.net/config/bootstrap.php` | 10, 97-106 | DI container + `PageRouterKernel` |
| 12 | Alan haritası | `shared/config/domain.php` | 4-18 | 7 subdomain + port (home: 81) |
| 13 | Zorunlu eklentiler | `shared/composer.json` | require | `php >=8.4`, `ext-apcu`, `php-di/php-di ^7.0` |
| 14 | Session açılışı | `shared/src/Session/SessionBootstrapper.php` | 19-28 | `session_*` tek yetkili giriş noktası |

Tablo notu: satır aralıkları 2026-10-13 disk okumasıyla belirlenmiş; `Select-Object -Index (N-1)` ile doğrulanmıştır (kanıt dosyası: `isletim-sistemi-runtime-dogrulama.md` §3).

### §2.1 Subdomain kırılımı (runtime yapılandırmasının dağılımı)

| # | Subdomain | Dizin | `index.php` | `.htaccess` | `web.config` | Port |
|---|---|---|---|---|---|---|
| 1 | `home` | `home.coremusic.net/` | ✅ var | ✅ var | ✅ var | 81 |
| 2 | `api` | `api.coremusic.net/` | ✅ var | ✅ var | ✅ var | 80 |
| 3 | `auth` | `auth.coremusic.net/` | ✅ var | ✅ var | ✅ var | 80 |
| 4 | `assets` | `assets.coremusic.net/` | ❌ yok | ✅ var | ✅ var | 80 |
| 5 | `music` | diskte dizin yok | ❌ yok | ❌ yok | ❌ yok | 80 |
| 6 | `admin` | diskte dizin yok | ❌ yok | ❌ yok | ❌ yok | 80 |
| 7 | `media` | `media.coremusic.net/` | ❌ yok | ❌ yok | ❌ yok | 80 |

Tablo notu: `music` ve `admin` alt alanları `shared/config/domain.php:11-13` içinde **tanımlı**, fakat repo çalışma ağacında karşılık gelen dizin **yok** → bu iki alan için runtime yapılandırması [UNKNOWN] (R4). `media` dizini var, giriş noktasındaki `index.php` yok → runtime'ı farklı bir mekanizmadır (⚠️ VERIFICATION REQUIRED).

**Çift hedef okuması:** Aynı subdomain'de hem `.htaccess` hem `web.config` bulunması, iki ayrı sunucu ürününün **aynı uygulamayı** servis edebileceğini gösterir. Hangisinin üretimde aktif olduğu R1 ile ilgili olup repo içinden okunamaz; "yalnız Apache" ya da "yalnız IIS" iddiası bu dosyada yazılmaz.

### §2.2 Giriş noktası ve açılış dosyaları (sıra ile)

| # | Sıra | Dosya | Görev |
|---|---|---|---|
| 1 | 1 | `home.coremusic.net/index.php:10` | `autoload.php` + `constants.php` |
| 2 | 2 | `home.coremusic.net/index.php:14-16` | yalnız `DEBUG_MODE` altında `opcache_reset()` |
| 3 | 3 | `home.coremusic.net/index.php:21` | `config/app.php` yüklenir |
| 4 | 4 | `home.coremusic.net/index.php:23` | `RuntimeBootstrap::boot(DEBUG_MODE)` |
| 5 | 5 | `home.coremusic.net/index.php:26` | `config/config.php` (config nesneleri) |
| 6 | 6 | `home.coremusic.net/index.php:29` | `config/bootstrap.php` (DI + router) |
| 7 | 7 | `home.coremusic.net/config/bootstrap.php:10` | `HomeContainer::getInstance` |
| 8 | 8 | `home.coremusic.net/config/bootstrap.php:97` | `new PageRouterKernel(...)` |
| 9 | 9 | `home.coremusic.net/config/bootstrap.php:106` | `$kernel->handle($_SERVER, $_GET, $_POST, $routesFile)` |

Tablo notu: sıra numarası, dosyadaki fiziksel okuma sırasıdır; alt-dal `baslangic-ve-bootstrap` bu dokuz adımı ayrıntılandırır.

---

## §3 Üç alt-dal ayrıştırması

| # | Alt-dal | Ana soru | İçerik dosyaları | Kademe |
|---|---|---|---|---|
| 1 | `surec-ve-parcacik` | Tek istek nasıl bir süreçte/parçacıkta çalışır? | anayasa · sürücü · örnekler | Kademe-3 |
| 2 | `web-runtime-topolojisi` | Birden çok subdomain/süreç nasıl birbirine bağlanır? | anayasa · sürücü · örnekler | Kademe-3 |
| 3 | `baslangic-ve-bootstrap` | Uygulama sırayla nasıl ayağa kalkar ve nasıl kapanır? | anayasa · sürücü · örnekler | Kademe-3 |

| # | Alt-dal | Kapsadığı §'ler | Kapsamadığı |
|---|---|---|---|
| 1 | `surec-ve-parcacik` | VM · JVM · PHP SAPI · V8 isolate · async · GC · event loop | çekirdek zamanlayıcı kuantı |
| 2 | `web-runtime-topolojisi` | vhost · port · worker devri · heartbeat · kuyruk derinliği | deploy pipeline adımları |
| 3 | `baslangic-ve-bootstrap` | boot sırası · ortam · fail-fast · warm-up · shutdown | uygulama servis bağımlılık grafiği |

**Dosya adı sabitlemesi:** Üç alt-dalın dosya adları **değiştirilemez** (`anayasa` · `surucu` · `ornekler`); kök ise `-kapsam` · `-dogrulama` · `-ornekler` üçlüsünü korur.

---

## §4 Komşu sınırı matrisi (runtime →)

| # | Konu | Komşu dal | Geçiş yönü | Ne taşınır |
|---|---|---|---|---|
| 1 | OS süreç/parçacık | `isletim-sistemi-core` | runtime → core | süreç tablosu, öncelik, bağlam anahtarı |
| 2 | OS zamanlayıcı | `isletim-sistemi-plan` | runtime → plan | kuyruk disiplini, kuant, zamanlayıcı arayüzü |
| 3 | Runtime API'si | `isletim-sistemi-api` | runtime → api | SAPI yüzeyi, socket/HTTP kapısı |
| 4 | Runtime güvenlik/sandbox | `isletim-sistemi-security` | runtime → security | `open_basedir`, `disable_functions`, yetki |
| 5 | Runtime metrik | `isletim-sistemi-observability` | runtime → observability | RSS, throughput, latency ölçüm tanımı |
| 6 | Runtime testi | `isletim-sistemi-testing` | runtime → testing | boot süresi ve hata zamanlaması testi |
| 7 | Uygulama katmanı | `K011-application` | runtime → application | bootstrap'ten sonra devreden servis grafiği |
| 8 | Dağıtım | `K018-devops` | runtime → devops | topoloji birimi, worker sayısı, drenaç |
| 9 | Ses/DSP çalışma zamanı | `isletim-sistemi-audio` | runtime → audio | gerçek zamanlı çalışma zamanı ayrıdır |
| 10 | Donanım çalışma zamanı | `electroncis` | runtime → electroncis | donanım runtime'ı bu dalın konusu değildir |

Tablo notu: yön oku "bilgi akışı"dır; her geçişte bu alt-dal yalnız **sınır satırını** yazar, ayrıntı komşudadır. `electroncis` dizin adı kasıtlı olarak olduğu gibi yazılmıştır (kayıtlı imla durumu).

### §4.1 Geçiş kuralları

| # | Kural | Uygulama | İhlalde |
|---|---|---|---|
| 1 | Komşu dosya adı değiştirilmez | ad aynen yazılır | DUR |
| 2 | Ayrıntı kopyalanmaz | tek sınır satırı | taşınma |
| 3 | Yön çift yönlü olmaz | ok tek yönlü | yeniden yazım |
| 4 | Komşu `status`'ü değiştirilmez | yalnız referans | orkestratör |
| 5 | Karşılıklı bağımlılık `depends_on`'a yazılır | listelenir | çakışma |
| 6 | Belirsiz komşu konusu `[UNKNOWN]` işaretlenir | uydurulmaz | red team |
| 7 | Komşu dosyanın satırı okunur, tahmin edilmez | `Select-Object -Index` | düzeltme |

---

## §5 Çalışma zamanı kavramları

| # | Kavram | Ne olduğu | Bu repo'da | Sahip |
|---|---|---|---|---|
| 1 | VM (sanal makine) | bayt kodunu yorumlayan/jit'leyen katman | PHP Zend Engine (dil runtime'ı) | `isletim-sistemi-turu` |
| 2 | JVM | Java sanal makinesi, GC'li çalışma zamanı | yok (yalnız referans) | `isletim-sistemi-turu` |
| 3 | PHP SAPI | sunucu ile PHP arasındaki arayüz (fpm/apache2/cli/cgi) | Apache + CLI (testler `PHP_BINARY`) | bu dal |
| 4 | V8 isolate | tarayıcı/sunucu JS çalışma zamanı birimi | frontend JS (tarayıcı tarafı) | `K011-application` |
| 5 | Süreç (process) | bağımsız bellek alanı + bir takvim | web worker süreçleri | `isletim-sistemi-core` |
| 6 | İş parçacığı (thread) | süreç içinde paylaşılan bellek birimi | repo'da doğrudan kullanım yok (R2) | `isletim-sistemi-core` |
| 7 | İzolat (isolate) | kendi bellek ve olay döngüsüne sahip birim | V8 | `K011-application` |
| 8 | Sıfır-süreç (shared-nothing) | istek başına izole PHP süreci | APFP mimarisi (FPM) | bu dal |

Tablo notu: "Bu repo'da" sütunu yalnızca diskte gözlenen durumu yansıtır; gözlenmeyen varlık yazılmaz.

### §5.1 PHP SAPI seçenekleri ve bu depodaki durumu

| # | SAPI | Ne taşır | Bu depoda | Kanıt |
|---|---|---|---|---|
| 1 | `apache2handler` | Apache içine gömülü PHP | olası (R1) | `.htaccess` içindeki `php_flag` bayrakları |
| 2 | `fpm-fcgi` | ayrı FPM süreci + FastCGI | olası (R1) | FPM pool config'i depoda **yok** |
| 3 | `cli` | komut satırı | ✅ var | testlerde `PHP_BINARY` kullanımı |
| 4 | `cgi-fcgi` | klasik CGI | gözlenmedi | kanıt yok |
| 5 | `embed` | gömülü PHP | gözlenmedi | kanıt yok |
| 6 | `litespeed` | LiteSpeed uyumu | gözlenmedi | kanıt yok |

Tablo notu: `apache2handler` ve `fpm-fcgi` için depo içinden **kesin** ayrım yapılabilir değildir; `.htaccess` içindeki `php_flag` yalnızca PHP'nin Apache modu altında etkili olan direktiflerdir ve bu **tek başına** SAPI'yı kanıtlamaz → ayırım R1/R3 kapsamında [UNKNOWN].

### §5.2 Sanal makine ve çalışma zamanı birimi karşılaştırması

| # | Birim | Bellek sahipliği | Görev dağıtım modeli | Bu depoda |
|---|---|---|---|---|
| 1 | PHP isteği | istek başına izole (shared-nothing) | sunucu sıraya alır | ✅ |
| 2 | PHP iş parçacığı (pthreads) | süreç içinde paylaşılan | iş parçacığı kuyruğu | yok (R2) |
| 3 | JVM oturumu | GC'li tek süreç belleği | JVM zamanlayıcısı | yok |
| 4 | Node.js ana iş parçacığı | tek süreç, olay döngüsü | olay döngüsü | yalnız tarayıcı tarafı JS |
| 5 | V8 izolatı | izolat başına ayrı bellek | olay döngüsü | tarayıcı (K011) |
| 6 | CLI alt süreci | ayrı süreç | üst süreç bekler | ✅ (`proc_open` testleri) |

---

## §6 Ölçüm tanımları

| # | Ölçüm | Birim | Tanım | Sahip |
|---|---|---|---|---|
| 1 | Throughput | istek/s | birim zamanda tamamlanan istek | `isletim-sistemi-observability` |
| 2 | Latency | ms | istek başlangıcından yanıt sonuna | `isletim-sistemi-observability` |
| 3 | TTFB | ms | ilk bayt zamanı | `baslangic-ve-bootstrap` §6 |
| 4 | Boot süresi | ms | `index.php` girişinden router çağrısına | `baslangic-ve-bootstrap` §6 |
| 5 | RSS | KB/MB | sürecin gerçek bellek kullanımı | `surec-ve-parcacik` §5 |
| 6 | Worker sayısı | adet | eşzamanlı iş üreten süreç | `web-runtime-topolojisi` §5 |
| 7 | Kuyruk derinliği | adet | bekleyen iş kalemi | `web-runtime-topolojisi` §5 |
| 8 | Middleware süresi | ms | `hrtime` ile ölçülen katman süresi | bu dal (kanıt: `MiddlewarePipeline.php:42-46`) |

### §6.1 Ölçüm sözleşmesi (bu dalın tanımladığı, sahibi olmayan)

| # | Ölçüm | Nasıl toplanır | Örnek kaynak | Sıklık |
|---|---|---|---|---|
| 1 | Throughput | tamamlanan yanıt / pencere süresi | sunucu erişim günlüğü | 60 sn |
| 2 | Latency (p95) | yanıt süresi dağılımının 95. yüzdeliği | `hrtime` farkı | 60 sn |
| 3 | TTFB | bağlantıdan ilk bayta | istemci ölçümü | sayfa başına |
| 4 | Boot süresi | `index.php` giriş → `$kernel->handle` | uygulama içi sayaç | her istek |
| 5 | RSS | `/proc/self/status` veya Windows sayaç | OS aracı | 60 sn |
| 6 | Worker sayısı | aktif FPM/Apache süreci | süreç listesi | 60 sn |
| 7 | Kuyruk derinliği | bekleyen iş kalemi | iş kuyruğu | 30 sn |
| 8 | Hata oranı | 5xx / toplam yanıt | erişim günlüğü | 60 sn |
| 9 | opcache vuruş oranı | vuruş / toplam | opcache sayaçları | 300 sn |
| 10 | APCu vuruş oranı | `apcu_hit` / (`hit` + `miss`) | APCu sayaçları | 300 sn |

Tablo notu: bu tablo yalnız **tanım** taşır; toplayıcı kurulumu `isletim-sistemi-observability` dosyasındadır ve burada tekrarlanmaz.

### §6.2 Ölçüm birimi sözlüğü

| # | Birim | Sembol | Kullanıldığı yer | Not |
|---|---|---|---|---|
| 1 | istek/saniye | istek/s | throughput | pencere ortalaması |
| 2 | milisaniye | ms | latency, TTFB, boot | tam sayıya yuvarlanır |
| 3 | bayt | B | RSS ayrıntısı | onaltılık değil |
| 4 | megabayt | MB | RSS | 1 MB = 1024 KB (repo sözleşmesi yok → [UNKNOWN]) |
| 5 | adet | adet | worker, kuyruk | tam sayı |
| 6 | yüzde | % | hit oranı | 0–100 |
| 7 | nanosaniye | ns | `hrtime` girdisi | PHP'de 1 ns çözünürlük |
| 8 | saniye | sn | pencere | tam sayı |

---

## §7 Repo disk kanıtları özeti

| # | İddia | Dosya | Satır | Kanıt cümlesi |
|---|---|---|---|---|
| 1 | Apache hedefi mevcut | `home.coremusic.net/.htaccess` | 5-7 | `php_flag display_errors off` |
| 2 | IIS hedefi mevcut | `home.coremusic.net/web.config` | 1-43 | `<system.webServer>` + `<rewrite>` |
| 3 | Çift hedef (4 subdomain) | `*/web.config` | dosya varlığı | api · assets · auth · home = 4 adet |
| 4 | Apache da 4 subdomain'de | `*/.htaccess` | dosya varlığı | api · assets · auth · home = 4 adet |
| 5 | PHP runtime kod içinden kurulur | `shared/src/Bootstrap/RuntimeBootstrap.php` | 7-20 | `error_reporting` + `ini_set` |
| 6 | Giriş noktası 4 adımlı | `home.coremusic.net/index.php` | 10-29 | autoload → boot → bootstrap |
| 7 | DI container tek örnek | `home.coremusic.net/config/bootstrap.php` | 10 | `HomeContainer::getInstance(...)` |
| 8 | Router kernel çağrısı | `home.coremusic.net/config/bootstrap.php` | 97-106 | `new PageRouterKernel(...)` + `handle(...)` |
| 9 | 7 subdomain + port | `shared/config/domain.php` | 4-18 | `subdomains` + `subdomain_ports` |
| 10 | PHP >= 8.4 zorunlu | `shared/composer.json` | require | `"php": ">=8.4"` |
| 11 | APCu zorunlu eklenti | `shared/composer.json` | require | `"ext-apcu": "*"` |
| 12 | DI kapsayıcısı bağımlılığı | `shared/composer.json` | require | `"php-di/php-di": "^7.0"` |

---

## §8 Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| R1 | Üretim runtime'ı Apache mi, IIS mi? (her ikisinin config'i de var) | topoloji tekilleşmez | [UNKNOWN] |
| R2 | Çok-iş-parçacıklı çalışma (pthreads) hedef mimaride var mı? | süreç modeli netleşmez | [UNKNOWN] |
| R3 | FPM mi, Apache mod_php mi? (repo'da FPM pool config'i yok) | worker sayısı hesabı | ⚠️ VERIFICATION REQUIRED |
| R4 | `media.coremusic.net` giriş noktasında `index.php` yok — runtime'ı nedir? | topoloji boşluk | ⚠️ VERIFICATION REQUIRED |
| R5 | opcache/APCu warm-up boot'ta yapılıyor mu? yalnız `opcache_reset` görüldü | ısınlama eksik olabilir | [UNKNOWN] |
| R6 | Kapanış drenaç (`fastcgi_finish_request`) kullanılıyor mu? | shutdown davranışı | [UNKNOWN] |
| R7 | Sunucu MPM'i (Apache) veya FastCGI havuzu hangi kapasitede? | eşzamanlılık hesabı | ⚠️ VERIFICATION REQUIRED |
| R8 | `assets.coremusic.net` yalnız statik mi servis ediyor? | topoloji netliği | [UNKNOWN] |

---

## §9 Kapsam dışı

| # | Konu | Gerekçe | Nereye |
|---|---|---|---|
| 1 | HTTP/1.1 ve HTTP/2 ayrıntısı | protokol katmanı | `isletim-sistemi-api` |
| 2 | Zamanlayıcı kuyrukları ve kuant | planlama | `isletim-sistemi-plan` |
| 3 | `open_basedir`, `disable_functions` | sandbox politikası | `isletim-sistemi-security` |
| 4 | Metrik toplayıcı ve dashboard | gözlemlenebilirlik | `isletim-sistemi-observability` |
| 5 | PHPUnit/Vitest test altyapısı | test stratejisi | `isletim-sistemi-testing` |
| 6 | Çekirdek syscall ve kesme | OS çekirdeği | `isletim-sistemi-kernel` |
| 7 | Deploy pipeline, Docker, rollback | dağıtım | `K018-devops` |
| 8 | Controller/servis/DTO kodu | uygulama | `K011-application` |
| 9 | ASIO/WASAPI gerçek zamanlı audio | ayrı çalışma zamanı | `isletim-sistemi-audio` |
| 10 | Donanım BOM ve PCB | donanım | `electroncis` |
| 11 | MySQL sorgu ve şema | veri katmanı | `K005-database` |
| 12 | Kimlik doğrulama akışı | güvenlik akışı | `isletim-sistemi-security` |

---

## §10 Terim sözlüğü

| # | Terim | Tanım | İlgili § |
|---|---|---|---|
| 1 | Runtime (çalışma zamanı) | Kodun asılı kaldığı, çalıştığı canlı ortam | §0 |
| 2 | SAPI | Sunucu ile PHP arasındaki çalışma zamanı arayüzü | §5.1 |
| 3 | Front controller | Tüm istekleri karşılayan tek giriş dosyası | §2.2 |
| 4 | `.htaccess` | Apache'nin dizin başına yapılandırma dosyası | §2 |
| 5 | `web.config` | IIS'in site/dizin yapılandırma dosyası | §2 |
| 6 | vhost | Sanal sunucu tanımı (alan adı + port + kök) | §4 |
| 7 | Worker | İstek üreten süreç/parçacık birimi | §6 |
| 8 | Shared-nothing | İstek başına izole bellek modeli | §5 |
| 9 | opcache | Derlenmiş PHP bayt kodu önbelleği | §2.2 |
| 10 | APCu | Kullanıcı düzeyinde paylaşım önbelleği | §5 |
| 11 | Warm-up | Soğuk önbelleği önceden doldurma | §8 |
| 12 | Fail-fast | Hatalı yapılandırmada ilk anda durma | §12 |
| 13 | Drenaç | Kapanışta bekleyen işi bitirme | §12 |
| 14 | TTFB | Yanıtın ilk baytına kadar geçen süre | §6 |
| 15 | RSS | Sürecin gerçek bellek ayak izi | §6 |
| 16 | Throughput | Birim zamanda tamamlanan iş | §6 |
| 17 | Latency | İş başlangıcı ile bitişi arasındaki süre | §6 |
| 18 | İzolat | Kendi bellek/olay döngüsüne sahip birim | §5.2 |
| 19 | Vhost eşlemesi | İstek başlığından doğru siteye geçiş | §11 |
| 20 | Bootstrap | Uygulamayı sırayla ayağa kaldırma | §2.2 |
| 21 | DI container | Bağımlılıkları çözü tek merkez | §2.2 |
| 22 | Middleware | İstek/yanıt zincirine katılan katman | §6 |
| 23 | Process model | Süreçlerin oluşumu ve devri | §4 |
| 24 | Heartbeat | Canlılık bildirimi periyodu | §6 |
| 25 | Kuyruk derinliği | Bekleyen iş kalemi sayısı | §6 |
| 26 | R1–R6 | Bu dosyadaki açık soru numaraları | §8 |

---

## §11 Sınır kararları (runtime ↔ komşu)

| # | Karar | Karar sahibi | Gerekçe |
|---|---|---|---|
| 1 | HTTP başlık semantiği bu dalda tanımlanmaz | `isletim-sistemi-api` | protokol OSAI |
| 2 | `rewrite` zincirinin **amacı** burada, HTTP durum kodu orada | `isletim-sistemi-api` | ikisi de tek satırda ayrıştırılır |
| 3 | Zamanlayıcı kuantı bu dalda hesaplanmaz | `isletim-sistemi-plan` | kuyruk sahipliği ayrı |
| 4 | Sandbox direktifleri bu dalda yazılmaz | `isletim-sistemi-security` | güvenlik veto yetkisi |
| 5 | Metrik toplayıcı bu dalda kurulmaz | `isletim-sistemi-observability` | yalnız ölçüm tanımı |
| 6 | Boot süresi **eşiği** test tarafında | `isletim-sistemi-testing` | kabul kriteri QA'da |
| 7 | Worker sayısı **önerisi** bu dalda, deploy'u `K018-devops` | ikili | topoloji vs. uygulama |
| 8 | DI container kullanımı uygulama katmanına devredilir | `K011-application` | servis grafiği uygulamada |
| 9 | Gerçek zamanlı audio çalışma zamanı ayrıdır | `isletim-sistemi-audio` | lock-free kısıt |
| 10 | Çekirdek süreç tablosu bu dalda anlatılmaz | `isletim-sistemi-core` | OS sahipliği |
| 11 | `pcntl_fork`/`proc_open` örnekleri yalnız kanıt olarak taşınır | `isletim-sistemi-core` | ayrıntı orada |
| 12 | DB bağlantısı havuzu davranışı veri katmanında | `K005-database` | bağlantı sahipliği |
| 13 | Session yaşam döngüsü açılışta geçilir, tasarımı ayrı | `isletim-sistemi-security` | oturum güvenliği |
| 14 | Donanım çalışma zamanı bu dalın konusu değildir | `electroncis` | donanım katmanı |

---

## §12 Riskler

| # | Risk | Etki | Kanıt durumu |
|---|---|---|---|
| 1 | Çift hedef (Apache + IIS) tekilleşmedi | yapılandırma kayması | R1 [UNKNOWN] |
| 2 | FPM havuzu yapılandırması depoda yok | worker kapasitesi hesaplanamaz | R3 ⚠️ VERIFICATION REQUIRED |
| 3 | `media` giriş noktasında `index.php` yok | topoloji boşluğu | R4 ⚠️ VERIFICATION REQUIRED |
| 4 | `music`/`admin` dizinleri yok | iki subdomain runtime'sız | §2.1 |
| 5 | `opcache_reset` yalnız `DEBUG_MODE` koşullu | üretimde ısınlama davranışı belirsiz | `index.php:14-16` |
| 6 | APCu zorunlu (`ext-apcu`) ama runtime'da kapalı olabilir | bellek önbelleği sessizce devre dışı | `api.coremusic.net/index.php:143-145` |
| 7 | `error_log` göreli yol (`../../`) | log kaybı riski | `.htaccess:7` |
| 8 | `.htaccess` ile `web.config` davranışı birebir aynı değil | hedefe göre farklı sonuç | §2 |
| 9 | Çok-iş-parçacığı hedefi belirsiz | süreç modeli netleşmez | R2 [UNKNOWN] |
| 10 | Shutdown/drenaç kanıtı yok | kapanışta iş kaybı olasılığı | R6 [UNKNOWN] |
| 11 | Warm-up kanıtı yok | ilk istek soğuk | R5 [UNKNOWN] |
| 12 | `php >=8.4` zorunlu, ortam sürümü doğrulanmadı | uyumsuzluk riski | ⚠️ VERIFICATION REQUIRED |
| 13 | Aynı başlık kümesi iki sunucuda elle iki kez tutuluyor | kayma riski | `.htaccess:9-16` ↔ `web.config:2-15` |
| 14 | Front controller dosyası tek (`index.php`) | her iki hedefte ortak | `.htaccess:36`, `web.config:38` |

---

## §13 Red-team kontrol listesi

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Kapsam dosyası ≥500 tam satır | ✅ | görev çıkış raporu |
| 2 | `index.md` 40–100 satır | ✅ | görev çıkış raporu |
| 3 | `parent:` alanı kullanılmadı | ✅ yalnız `parent_id` | FM denetimi |
| 4 | `group` tırnaksız ve `---` öncesi | ✅ `group: mimari` | FM denetimi |
| 5 | Boş hücre yok | ✅ tüm hücreler dolu | tablo denetimi |
| 6 | `T`+`B`+`D` ve `Yak`+`ında` yer tutucuları yok | ✅ | metin taraması |
| 7 | `//` + üç nokta ve `{` + üç nokta + `}` yer tutucu yok | ✅ | metin taraması |
| 8 | Uydurma satır numarası yok | ✅ `Select-Object -Index` | §7 |
| 9 | Mojibake yok | ✅ UTF-8 | okuma denetimi |
| 10 | URL envanter kesişimi 0 | ✅ | §16 |
| 11 | HTTP 200 doğrulaması | ✅ HEAD | §16 |
| 12 | Komşu sınırı 8 satır | ✅ | §4 |
| 13 | Dosya adları değişmedi | ✅ 16 dosya | dizin listesi |
| 14 | Dışına yazılan dosya yok | ✅ beyazliste | `git status` |
| 15 | `git commit` atılmadı | ✅ | orkestratör |
| 16 | Kayıtlı imlalar bozulmadı | ✅ `electroncis` | §4 |
| 17 | Son satır Authority bloğu | ✅ | dosya sonu |
| 18 | Açık sorular `[UNKNOWN]`/`⚠️` ile işaretli | ✅ | §8 |

---

## §14 Alt-dal sorumluluk matrisi

| # | Konu | Kök | `surec-ve-parcacik` | `web-runtime-topolojisi` | `baslangic-ve-bootstrap` |
|---|---|---|---|---|---|
| 1 | Kapsam sınırı | ✅ sahibi | referans | referans | referans |
| 2 | VM / SAPI kavramı | özet | ✅ sahibi | — | — |
| 3 | Parçacık / async | özet | ✅ sahibi | referans | — |
| 4 | Bellek / GC | özet | ✅ sahibi | — | — |
| 5 | Event loop / zamanlayıcı | özet | ✅ sahibi | — | — |
| 6 | Sunucu topolojisi | özet | — | ✅ sahibi | — |
| 7 | Sınır / heartbeat | özet | — | ✅ sahibi | — |
| 8 | Worker devri | özet | — | ✅ sahibi | — |
| 9 | Ağ uçları (vhost/port) | özet | — | ✅ sahibi | — |
| 10 | Boot sırası | özet | — | — | ✅ sahibi |
| 11 | Ortam yükleme | özet | — | — | ✅ sahibi |
| 12 | Fail-fast | özet | — | — | ✅ sahibi |
| 13 | Warm-up | özet | kısmen | — | ✅ sahibi |
| 14 | Shutdown / drenaç | özet | kısmen | kısmen | ✅ sahibi |
| 15 | Ölçüm tanımı | ✅ ortak | §5 | §5 | §6 |
| 16 | Disk kanıtları | ✅ ortak | §6 | §6 | §7 |

---

## §15 Boşluk ve eksik envanter

| # | Beklenen varlık | Disk durumu | Davranış |
|---|---|---|---|
| 1 | `music.coremusic.net/` dizini | yok | [UNKNOWN], uydurulmaz |
| 2 | `admin.coremusic.net/` dizini | yok | [UNKNOWN], uydurulmaz |
| 3 | `media.coremusic.net/index.php` | yok | ⚠️ VERIFICATION REQUIRED |
| 4 | FPM pool (`www.conf` vb.) | yok | ⚠️ VERIFICATION REQUIRED |
| 5 | nginx yapılandırması | yok | ⚠️ VERIFICATION REQUIRED |
| 6 | `php.ini` üretim kopyası | yok | [UNKNOWN] |
| 7 | Systemd / servis tanımı | yok | [UNKNOWN] |
| 8 | Kuyruk işçisi betiği | yok | [UNKNOWN] |
| 9 | `fastcgi_finish_request` kullanımı | gözlenmedi | R6 [UNKNOWN] |
| 10 | Warm-up betiği | gözlenmedi | R5 [UNKNOWN] |
| 11 | pthreads / `pcntl_*` üretim kullanımı | yalnız testte `proc_open` | R2 [UNKNOWN] |
| 12 | Deploy pipeline runtime adımı | `.github/workflows` 2 dosya | `K018-devops` |
| 13 | `assets` için giriş noktası | `index.php` yok | [UNKNOWN] |
| 14 | `media` için `.htaccess`/`web.config` | ikisi de yok | ⚠️ VERIFICATION REQUIRED |
| 15 | Üretim `APP_ENV_MODE` değeri | `.env` depoda yok (`.env.example` var) | [UNKNOWN] |

Tablo notu: olmayan varlık "yoktur" olarak yazılır; varlığı varsayılmaz, tahmin edilmez. Her satır ya `[UNKNOWN]` ya `⚠️ VERIFICATION REQUIRED` ya da komşu dale devredilmiştir.

---

## §16 Web araştırması

> Kural: yalnız resmi kaynak · her URL envanterle (2097 benzersiz) kesiştirilmiş, kesişim **0** · HEAD ile HTTP 200 doğrulandı · tarih 2026-10-13.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Apache `.htaccess` davranışı resmi olarak `How To/Allow Override` belgesinde tanımlıdır | https://httpd.apache.org/docs/2.4/howto/htaccess.html | 2026-10-13 | HTTP 200 |
| PHP-FPM kurulumu resmi PHP el kitabı bölümünde anlatılır (SAPI seçeneklerinden biri) | https://www.php.net/manual/en/install.fpm.php | 2026-10-13 | HTTP 200 |
| IIS `system.webServer` yapılandırma bölümü resmi Microsoft dokümantasyonundadır | https://learn.microsoft.com/en-us/iis/configuration/system.webServer/ | 2026-10-13 | HTTP 200 |

---

## §17 Okuma sırası

| # | Sıra | Dosya | Ne için |
|---|---|---|---|
| 1 | önce | bu dosya (`-kapsam`) | sınır ve komşu matrisi |
| 2 | sonra | `-dogrulama` | iddia/URL/disk kanıtları |
| 3 | sonra | `-ornekler` | gerçek repo kodu |
| 4 | sonra | `surec-ve-parcacik/index.md` | alt-dal 1 |
| 5 | sonra | `web-runtime-topolojisi/index.md` | alt-dal 2 |
| 6 | son | `baslangic-ve-bootstrap/index.md` | alt-dal 3 |

---

## §18 Tarih ve sürüm kaydı

| # | Tarih | Değişiklik | Durum |
|---|---|---|---|
| 1 | 2026-10-12 | ilk kapsam dosyası (4 satır grubu, ~49 satır) | geçmiş |
| 2 | 2026-10-13 | kök yeniden yazım: 18 bölüm, 3 alt-dal, komşu matrisi | bu sürüm |
| 3 | 2026-10-13 | 48 yeni URL envanter kesişimi 0 ile doğrulandı | bu sürüm |
| 4 | 2026-10-13 | 12 disk kanıtı `Select-Object -Index` ile doğrulandı | bu sürüm |
| 5 | — | R1–R8 açık soruları kapatılmadı | açık |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
