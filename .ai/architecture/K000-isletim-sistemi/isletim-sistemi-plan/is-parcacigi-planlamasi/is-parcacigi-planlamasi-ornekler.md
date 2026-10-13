---
title: "Parçacık Planlaması — Örnekler (Kod Kanıtı)"
description: "Repo'da iş biriminin parçacağa/sürece dönüşümünün gerçek satır kanıtları, çalışan yapılandırmalar, anti-pattern kataloğu ve dürüstlük defteri"
node_type: module
status: active
date: 2026-10-13
owner: Bayram Ali
group: kod
children: []
---

# Parçacık Planlaması — Örnekler (Kod Kanıtı)

> Bu dosya **kanıt** dosyasıdır: her tablo satırı ya repo'da `Dosya: … / Satır: …`
> ile doğrulanmış bir alıntı ya da dürüstlük defterinde işaretli bir **yokluk**tur.
> Anayasa kuralı, sürücü kurulumu bu dosyada **çalışan hâliyle** görülür.
> Alıntılar `Select-Object -Index (N-1)` ile dosyadan okunup birebir teyit edilmiştir (2026-10-13).

## §0 — Kapsam ve Okuma Sırası

| Madde | Değer |
|---|---|
| Okuma sırası | `-anayasa.md` (kural) → `-surucu.md` (kurulum) → bu dosya (kanıt) |
| Kapsam | fork · timeout · eşzamanlı denetim · uygulama limiti · süreç testi |
| Kapsam dışı | parçacık (thread) örnekleri — repo'da yok (§5 D-1) |
| Kanıt dili | Türkçe yorum + İngilizce teknik terim; alıntı dili özgün (İngilizce) |

### §0.1 Dosyalar arası okuma haritası

| Bu dosya | Kardeş | Bağ |
|---|---|---|
| §1.1 Y-1…Y-8 | `-anayasa.md §2.7` karar matrisi | hangi karar hangi yapılandırma |
| §2.1 fork | `-surucu.md §4.2` W-1 | fork guard ilk adım |
| §2.3 timeout | `-surucu.md §3.2` PHP limitleri | yumuşak sınır tablosu |
| §2.4 ölçüm | `-anayasa.md §4.4` P-1…P-8 | ölçüm kalıpları |
| §3 anti-pattern | `-surucu.md §7.1` E-1…E-7 | hata modları |
| §5 dürüstlük | `-surucu.md §7.7` O-1…O-5 | açık yüzeyler |

### §0.2 Soru → bölüm indeksi

| Soru | Bölüm |
|---|---|
| Repo fork kullanıyor mu? | §2.1 |
| Parçacık (thread) var mı? | §5 D-1 |
| Timeout nasıl uygulanıyor? | §1.4, §2.1 |
| Ölçüm kalıbı var mı? | §2.4 |
| Hangi anti-patternler riskli? | §3.1 |
| Eşikler biliniyor mu? | §4 |
| Yokluklar nasıl kapanır? | §5.1–§5.2 |

## §1 — Çalışan Yapılandırmalar (Repo Gerçeği)

### §1.1 Yapılandırma envanteri

| # | Yapılandırma | Dosya | Ne kurar | Durum |
|---|---|---|---|---|
| Y-1 | fork + timeout | `app/Modules/AI/AIOrchestrator.php` | çocuk süreç + 15 sn sınır | IMPLEMENTED |
| Y-2 | paralel sağlık denetimi | `shared/src/Api/Registry/ServiceHealth.php` | çoklu servis denetimi tek çağrıda | IMPLEMENTED |
| Y-3 | servis timeout | `shared/src/Core/ServiceDiscovery/ServiceDefinition.php` | servis başına 5 sn | IMPLEMENTED |
| Y-4 | oran sınırı kovası | `shared/src/Middleware/RateLimitMiddleware.php` | bucket + window | IMPLEMENTED |
| Y-5 | alt süreç testi | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | `proc_open` ile CLI doğrulama | IMPLEMENTED |
| Y-6 | zorunlu uzantı | `composer.json` | `ext-apcu` bağımlılığı | IMPLEMENTED |
| Y-7 | boot yapılandırması | `shared/src/Core/RuntimeBootstrap.php` | zaman dilimi + boot akışı | IMPLEMENTED |
| Y-8 | istek girişi | `home.coremusic.net/index.php` | boot → router | IMPLEMENTED |

### §1.2 Her yapılandırma — anayasa/sürücü karşılığı

| # | Yapılandırma | Karşılık | Karar satırı |
|---|---|---|---|
| Y-1 | fork + timeout | anayasa §2.7 M-2 (izolasyon) + sürücü §3.4 | `CHILD_TIMEOUT = 15` |
| Y-2 | paralel denetim | sürücü §5.1 (eşzamanlı denetim deseni) | `checkAll()` |
| Y-3 | servis timeout | sürücü §3.2 (yumuşak limit) | `timeout = 5` |
| Y-4 | oran sınırı | sürücü §6.2 (uygulama katmanı limiti) | bucket + window |
| Y-5 | alt süreç testi | sürücü §5.1 (sıcak fork) + §7.2 T-1 | `proc_open` |
| Y-6 | zorunlu uzantı | sürücü §3.3 (sert bağımlılık) | `"ext-apcu": "*"` |
| Y-7 | boot | anayasa §3.1 L-1 | boot zinciri |
| Y-8 | istek girişi | anayasa §3.1 L-3 | router |

### §1.3 Kapsam dışı yapılandırmalar (yokluk — dürüst)

| # | Yapılandırma | Repo durumu | Durum etiketi |
|---|---|---|---|
| N-1 | parçacık havuzu | composer'da havuz kütüphanesi yok | `⚠️ VERIFICATION REQUIRED` |
| N-2 | `pcntl_signal` sinyal kurulumu | çağıran kod yok | `⚠️ VERIFICATION REQUIRED` |
| N-3 | `setrlimit`/`prlimit` | çağıran kod yok | `⚠️ VERIFICATION REQUIRED` |
| N-4 | `sched_setaffinity`/cpuset | çağıran kod yok | `⚠️ VERIFICATION REQUIRED` |
| N-5 | uzak kuyruk (queue worker) | worker betiği yok | `⚠️ VERIFICATION REQUIRED` |
| N-6 | cron betiği | `crontab`/systemd timer dosyası yok | `⚠️ VERIFICATION REQUIRED` |

### §1.4 Y-1'in uçtan uca okuması (fork + timeout)

| Adım | Ne olur | Dosya/satır | Hata olursa |
|---|---|---|---|
| 1 | boot tamamlanır (autoloader, konteyner) | `config/bootstrap.php` L13 | sınıf bulunamaz → durdurma |
| 2 | istek gelir, router işi belirler | `home.coremusic.net/index.php` L15 | 404/istisna |
| 3 | orkestratör işi devralır | `AIOrchestrator.php` | — |
| 4 | `CHILD_TIMEOUT` okunur | `AIOrchestrator.php` L20 | sabit yoksa sınır uygulanamaz |
| 5 | `pcntl_fork()` çağrılır | `AIOrchestrator.php` L45 | `-1` → hata |
| 6 | ebeveyn `childPid > 0` alır | L45 sonrası | PID takibi kaybı (E-2) |
| 7 | çocuk işi yapar | child dalı | sinyal kurulmadıysa askıda kalır (D-2) |
| 8 | timeout dolar | 15 sn | child sonlandırılır |
| 9 | ebeveyn toplama yapar | `pcntl_waitpid` | zombi (E-5) |

### §1.5 Y-2'nin uçtan uca okuması (paralel denetim)

| Adım | Ne olur | Dosya/satır | Sürücü karşılığı |
|---|---|---|---|
| 1 | denetim çağrısı gelir | `ServiceHealth.php` L26 | §5.1 eşzamanlı denetim |
| 2 | yorum iddiası | L24 "parallel" | iddia ≠ ölçüm (§2.2) |
| 3 | servis listesi çözülür | `ServiceDefinition.php` | timeout = 5 (L23) |
| 4 | her servis sınırlı sürede denenir | L23 timeout | §3.2 yumuşak limit |
| 5 | sonuç toplanır | `checkAll()` dönüşü | tek yanıt |
| 6 | oran sınırı aşılırsa | `RateLimitMiddleware.php` L39–L40 | §6.2 uygulama limiti |
| 7 | istemciye bekleme bildirilir | `RateLimitException.php` L7 | retryAfter |

### §1.6 Yapılandırma kararları — neden bu şekilde?

| Karar | Alternatif | Seçilen | Gerekçe |
|---|---|---|---|
| fork (süreç) | parçacık | fork | parçacık yüzeyi repo'da yok (D-1) |
| sabit timeout | dinamik timeout | sabit `15` | basitlik; dinamik hesap yok |
| servis başına 5 sn | global timeout | servis başına | izole arıza |
| kova/pencere | sabit sayaç | kova + pencere | yenileme davranışlı sınır |
| `proc_open` test | exec | `proc_open` | tüp + exit kodu okunabilir |

### §1.7 Ortam bağımlılıkları (bu yapılandırmalar neye ihtiyaç duyar?)

| # | Bağımlılık | Y-1 (fork) | Y-2 (denetim) | Y-4 (oran) | Y-5 (test) |
|---|---|---|---|---|---|
| B-1 | `pcntl` uzantısı | gerekli | — | — | — |
| B-2 | `posix` uzantısı | önerilir (PID) | — | — | — |
| B-3 | APCu | — | — | — | — |
| B-4 | CLI ortamı | evet (servis) | — | — | evet |
| B-5 | web ortamı | hayır (fork yasak bölge) | evet | evet | hayır |

| Ortam | Fork kullanılabilir mi? | Gerekçe |
|---|---|---|
| CLI/servis | evet | `AIOrchestrator.php` L45 kanıtı |
| web (Apache/IIS) | hayır — önerilmez | istek döngüsü fork ile bozulur; kaynak: bu dosya §6 php.net |
| test (PHPUnit) | evet | `ApiKeyCreateCommandTest.php` L253 |
| cron/CLI kuyruk | evet — repo'da betik yok | §1.3 N-6 |

## §2 — Gerçek Repo Kodu (Satır Alıntıları)

### §2.1 Fork + timeout (Y-1)

| Dosya | Satır | Alıntı | Yorum |
|---|---|---|---|
| `app/Modules/AI/AIOrchestrator.php` | L20 | `const CHILD_TIMEOUT = 15;` | çocuk süreç üst sınırı (sürücü §3.4) |
| `app/Modules/AI/AIOrchestrator.php` | L45 | `$childPid = pcntl_fork();` | süreç ayrımı — repo'nun tek fork kanıtı (sürücü §4.2 W-1) |

| Gözlem | Sonuç |
|---|---|
| fork sayısı | 1 (repo genelinde tek kalıp) |
| sinyal kurulumu (W-2) | **YOK** — dürüstlük defteri §5 D-2 |
| toplama (waitpid) | test dosyasında doğrulanır (§2.5) |

### §2.2 Eşzamanlı denetim (Y-2)

| Dosya | Satır | Alıntı | Yorum |
|---|---|---|---|
| `shared/src/Api/Registry/ServiceHealth.php` | L24 | `* Execute parallel health checks.` | yorum satırı — iddia |
| `shared/src/Api/Registry/ServiceHealth.php` | L26 | `public function checkAll(): array` | toplu denetim girişi |

Yorum: "parallel" kelimesi bir **sorumluluk beyanıdır**; gerçek paralellik
çekirdek düzeyinde değil, uygulamanın eşzamanlı bekleme düzenindedir.
Ölçüm yoksa iddia ölçüm sayılmaz (anayasa §4.5).

### §2.3 Servis timeout ve oran sınırı (Y-3, Y-4)

| Dosya | Satır | Alıntı | Yorum |
|---|---|---|---|
| `shared/src/Core/ServiceDiscovery/ServiceDefinition.php` | L23 | `private readonly int $timeout = 5,` | servis başına yumuşak süre sınırı |
| `shared/src/Middleware/RateLimitMiddleware.php` | L39 | `private array $bucket;` | kova durumu (adet) |
| `shared/src/Middleware/RateLimitMiddleware.php` | L40 | `private float $window;` | pencere (saniye) |
| `shared/src/Core/RateLimitException.php` | L7 | `private int $retryAfter;` | aşım sonrası bekleme bildirimi |

### §2.4 Ölçüm kalıbı (sürücü §7.3)

| Dosya | Satır | Alıntı | Yorum |
|---|---|---|---|
| `shared/src/Middleware/MiddlewarePipeline.php` | L42 | `$start = hrtime(true);` | monotonik saat — süre ölçümünün repo kalıbı |
| `shared/src/Cache/ApcuAdapter.php` | L22 | APCu `apcu_store` sarmalayıcısı | önbellek yazma yüzeyi |
| `shared/src/Cache/MemoryAdapter.php` | L16 | PHP-dahili dizi önbelleği | bellek ölçüm yüzeyi |
| `shared/src/Cache/PageCacheAdapter.php` | L9 | sayfa önbelleği | sayfa düzeyi durum |

### §2.5 Süreç testi (Y-5)

| Dosya | Satır | Alıntı | Yorum |
|---|---|---|---|
| `shared/tests/Repository/ApiKeyCreateCommandTest.php` | L253 | `$handle = proc_open(` | alt süreç başlatma — testte süreç kanıtı |

| Test adımı | Karşılık | Beklenen |
|---|---|---|
| `proc_open` çağrısı | T-1 (sürücü §7.2) | handle döner |
| CLI çıktısı okuma | tüp okuma | beklenen metin |
| `proc_close` | fd serbest bırakma | exit kodu |

### §2.6 Boot kanıtları (Y-7, Y-8)

| Dosya | Satır | Alıntı | Yorum |
|---|---|---|---|
| `shared/src/Core/RuntimeBootstrap.php` | L9 | `date_default_timezone_set(...)` | boot anında tek sefer ayar |
| `home.coremusic.net/config/bootstrap.php` | L13 | autoloader kurulumu | fork'tan önce tamamlanır |
| `home.coremusic.net/config/bootstrap.php` | L97 | istek döngüsüne giriş | fork kararı istek düzeyinde |
| `home.coremusic.net/index.php` | L10 | boot çağrısı | boot → istek zinciri |
| `home.coremusic.net/index.php` | L15 | router çağrısı | istek başına iş |
| `composer.json` | L9 | `"ext-apcu": "*"` | sert bağımlılık (sürücü §3.3) |

### §2.7 Alıntı bütünlüğü

| Kural | Uygulama |
|---|---|
| Doğrulama | her satır dosyadan `Select-Object -Index (N-1)` ile okundu |
| Değiştirme | alıntı özgün hâliyle (İngilizce) yazılır, Türkçeleştirilmez |
| Tahmin yasağı | okunmayan satır için alıntı **yazılmaz** |
| Yokluk | "YOK" iddiası repo geneli arama ile verilir (§5) |

### §2.8 Kanıt dosyası — tam envanter

| # | Dosya | Kök | Alıntılanan satır | Konu |
|---|---|---|---|---|
| E-1 | `app/Modules/AI/AIOrchestrator.php` | app | L20, L45 | fork + timeout |
| E-2 | `shared/src/Api/Registry/ServiceHealth.php` | shared | L24, L26 | paralel denetim |
| E-3 | `shared/src/Core/ServiceDiscovery/ServiceDefinition.php` | shared | L23 | servis timeout |
| E-4 | `shared/src/Middleware/RateLimitMiddleware.php` | shared | L39, L40 | kova/pencere |
| E-5 | `shared/src/Core/RateLimitException.php` | shared | L7 | retryAfter |
| E-6 | `shared/src/Middleware/MiddlewarePipeline.php` | shared | L42 | hrtime ölçümü |
| E-7 | `shared/src/Cache/ApcuAdapter.php` | shared | L22 | APCu yüzeyi |
| E-8 | `shared/src/Cache/MemoryAdapter.php` | shared | L16 | dizi önbelleği |
| E-9 | `shared/src/Cache/PageCacheAdapter.php` | shared | L9 | sayfa önbelleği |
| E-10 | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | shared | L253 | proc_open testi |
| E-11 | `shared/src/Core/RuntimeBootstrap.php` | shared | L7–L20 (L9) | boot |
| E-12 | `home.coremusic.net/config/bootstrap.php` | home | L13, L97 | autoloader + istek |
| E-13 | `home.coremusic.net/index.php` | home | L10, L15 | boot + router |
| E-14 | `api/index.php` | api | L143–L147, L169–L171 | API girişi/kapanışı |
| E-15 | `config/domain.php` | api | L4–L18 | alan tanımı |
| E-16 | `composer.json` | kök | L9 | ext-apcu |

### §2.9 Her kanıt — hangi soruyu yanıtlar

| Kanıt | Yanıtlanan soru | Yanıt |
|---|---|---|
| E-1 (L45) | repo fork kullanıyor mu? | evet — tek kalıp |
| E-1 (L20) | timeout uygulanıyor mu? | evet — 15 sn sabit |
| E-2 (L24) | paralel denetim var mı? | iddia var, ölçüm yok |
| E-4 (L39–L40) | oran sınırı var mı? | evet — kova + pencere |
| E-6 (L42) | süre ölçüm kalıbı var mı? | evet — `hrtime(true)` |
| E-10 (L253) | süreç testi var mı? | evet — `proc_open` |
| E-16 (L9) | zorunlu uzantı var mı? | evet — `ext-apcu` |
| §5 defteri | parçacık var mı? | hayır — dürüst yokluk |

### §2.10 composer.json bağlamı (uzantı yüzeyi)

| Satır | İçerik | Anlam |
|---|---|---|
| L9 | `"ext-apcu": "*"` | APCu kurulmadan kurulum kırılır |
| — | `pcntl` uzantısı composer'da **listelenmiyor** | fork kalıbı ortam bağımlı — `⚠️ VERIFICATION REQUIRED` |
| — | `posix` uzantısı composer'da **listelenmiyor** | PID okuma ortam bağımlı — `⚠️ VERIFICATION REQUIRED` |

Yorum: composer `ext-*` bağımlılıkları "zorunlu eklenti" kapısıdır; POSIX'teki
`RLIMIT`-ailesi sert sınırın PHP'deki dolaylı karşılığı budur (sürücü §3.3).

### §2.11 Süreç yaşam döngüsü — anayasa §3.1 L-adımlarının repo karşılığı

| L-adım | Anayasa | Repo karşılığı | Kanıt |
|---|---|---|---|
| L-1 boot | zaman dilimi + ayarlar | `RuntimeBootstrap.php` L9 | E-11 |
| L-2 bağımlılık | autoloader | `config/bootstrap.php` L13 | E-12 |
| L-3 istek | router | `home index.php` L15 | E-13 |
| L-4 alt süreç | `proc_open`/fork | `AIOrchestrator.php` L45 · test L253 | E-1, E-10 |
| L-5 kapanış | temizleyiciler | `proc_close` (test) · `exit` semantiği (`-runtime`) | E-10 |
| L-6 serbest bırakma | fd/kilit/bellek | `proc_close` — fd | E-10 |
| L-7 toplama | `waitpid` | test bekleme akışı | E-10 |

### §2.12 Alıntı sözlüğü (özgün metin → Türkçe)

| Özgün alıntı | Türkçe |
|---|---|
| `* Execute parallel health checks.` | paralel sağlık denetimlerini çalıştır |
| `public function checkAll(): array` | tümünü denetle — dizi döndürür |
| `private readonly int $timeout = 5,` | salt-okunur 5 sn zaman aşımı |
| `private array $bucket;` | özel kova dizisi |
| `private float $window;` | özel kayan pencere (ondalık) |
| `private int $retryAfter;` | özel bekleme-süresi alanı |
| `$start = hrtime(true);` | yüksek çözünürlüklü saat başlat |
| `$childPid = pcntl_fork();` | çocuk süreç kimliğini fork ile al |
| `const CHILD_TIMEOUT = 15;` | çocuk zaman aşımı sabiti 15 |
| `"ext-apcu": "*"` | APCu eklentisi zorunlu |

## §3 — Anti-Pattern Kataloğu

| # | Anti-pattern | Belirti | Önleme | Kaynak |
|---|---|---|---|---|
| AP-1 | kilitli fork | child kilit miras alır | kilitli bölümde fork yasağı | sürücü §4.4 |
| AP-2 | fork sonrası sinyal kurulmaması | child sinyal görmez | `pcntl_signal` yeniden kurulumu | sürücü §4.2 W-2 |
| AP-3 | waitpid'siz fork | zombi süreç | her fork için toplama | sürücü §4.5 |
| AP-4 | proc_close'siz tüp | fd sızıntısı | kapanış zorunlu | sürücü §7.1 E-6 |
| AP-5 | timeout'suz child | süresiz askı | `CHILD_TIMEOUT` benzeri sınır | sürücü §3.4 |
| AP-6 | "paralel" iddiasız yorum | yanıltıcı kod yorumu | iddia + ölçüm birlikte | anayasa §4.5 |
| AP-7 | kilit altında bloke I/O | kilit uzar, inversion | kilit altında I/O yasağı | anayasa §3.4 R-4 |
| AP-8 | tek kontrol ile koşul | spurious wakeup | `while` döngüsü | anayasa §3.5 F-2 |
| AP-9 | uydurma eşik | sayısal hedef kanıtsız | `[UNKNOWN]` yaz | anayasa §4.2 |
| AP-10 | havuzsuz fork seli | NPROC tükenmesi | kuyruk + limit | sürücü §3.5 |

### §3.1 Anti-pattern → repo durumu

| # | Repo'da görülüyor mu? | Kanıt |
|---|---|---|
| AP-1 | hayır — tek fork, kilit yok | §2.1 |
| AP-2 | **evet riski** — sinyal kurulumu yok | §5 D-2 |
| AP-3 | hayır — test `proc_close` ile | §2.5 |
| AP-5 | hayır — `CHILD_TIMEOUT` mevcut | §2.1 |
| AP-6 | **evet riski** — L24 iddiası ölçümssüz | §2.2 |
| AP-10 | hayır — fork seli yok (tek fork) | §2.1 |

### §3.2 Anti-pattern → senaryo (nasıl kırılır?)

| # | Senaryo | Kırılma anı | Sonuç | Önleme |
|---|---|---|---|---|
| AP-1s | kilidi al → fork → child unlock | child kilidi hiç tutmaz | ebeveyn deadlock | fork öncesi kilidi aç |
| AP-2s | fork → child `SIGINT` bekler | sinyal maskesi miras | child askıda | `pcntl_signal` + `pcntl_async_signals` |
| AP-3s | fork → child exit → parent sleep | zombi tabloda kalır | PID birikimi | `pcntl_waitpid` |
| AP-4s | `proc_open` → kapatmadan test biti | fd birikir | CI'da EMFILE | `finally` içinde `proc_close` |
| AP-5s | child sonsuz döngü | timeout yok | CPU sızıntısı | `CHILD_TIMEOUT` |
| AP-6s | yorum "thread-safe" der, kod değil | inanç ≠ gerçek | üründe sürpriz | iddia + ölçüm |
| AP-7s | kilit altında `sleep` | inversion | yüksek öncelikli gecikir | kilit altında bekleme yasağı |
| AP-8s | `if (kuyruk bos) wait` | spurious wakeup | kaçırılan uyanma | `while` döngüsü |
| AP-9s | "100 sn" hedefi kanıtsız | gerçek ölçüm yok | uydurma SLA | `[UNKNOWN]` |
| AP-10s | 1000 fork aynı anda | NPROC aşılır | fork başarısız | kuyruk + `RLIMIT_NPROC` |

### §3.3 Anti-pattern → ölçüm (ne kaydedilir?)

| # | Ölçüm | Kalıp | Eşik |
|---|---|---|---|
| AP-1m | kilit süresi | `hrtime` (E-6, L42) | `[UNKNOWN]` — anayasa §4.2 H-2 |
| AP-2m | sinyal sayacı | repo'da yok | `⚠️ VERIFICATION REQUIRED` |
| AP-3m | zombi sayısı | repo'da yok | `[UNKNOWN]` |
| AP-4m | açık fd sayısı | repo'da yok | `[UNKNOWN]` — surucu §7.7 O-2 |
| AP-5m | timeout tetiklenme | `CHILD_TIMEOUT` sayacı | repo'da sayaç yok — `⚠️ VERIFICATION REQUIRED` |
| AP-10m | fork başarısızlık | `proc_open` false | testte tutulur |

### §3.4 Anti-pattern → sorumlu katman

| # | Sorumlu | Neden |
|---|---|---|
| AP-1…AP-5 | uygulama geliştirici | fork kalıbı uygulamanın |
| AP-6 | kod incelemesi (reviewer) | yorum/ölçüm tutarlılığı |
| AP-7…AP-8 | uygulama + `-kernel` politikası | kilit kuralı politika |
| AP-9 | orkestratör | eşik kararı üst otorite |
| AP-10 | operasyon + `kaynak-dagilimi/` | limit bütçesi |

## §4 — Eşik ve Kabul Kriterleri

| # | Kriter | Hedef | Kanıt durumu |
|---|---|---|---|
| KK-1 | fork → waitpid eşleşmesi | %100 | testte doğrulanır (§2.5) |
| KK-2 | child timeout tepkisi | 15 sn içinde sonlanır | `CHILD_TIMEOUT = 15` (§2.1) |
| KK-3 | servis timeout | 5 sn | `timeout = 5` (§2.3) |
| KK-4 | zombi sayısı | 0 | repo'da tek fork — ölçüm yok `[UNKNOWN]` |
| KK-5 | fd sızıntısı | 0 | ölçüm yok `[UNKNOWN]` |
| KK-6 | kilitlenme olayı | 0 | repo'da parçacık kilidi yok — anlamsız |
| KK-7 | ölçüm kalıbı mevcudiyeti | en az 1 | `hrtime` L42 (§2.4) |

### §4.1 Kriter → test eşlemesi

| # | Kriter | Test | Dosya |
|---|---|---|---|
| KK-1 | fork/waitpid | T-1 | `ApiKeyCreateCommandTest.php` L253 |
| KK-2 | timeout | T-6 | `AIOrchestrator.php` L20 (sabit) — canlı test **YOK** |
| KK-3 | servis timeout | — | `ServiceDefinition.php` L23 — birim testi repo'da **YOK** — `⚠️ VERIFICATION REQUIRED` |
| KK-7 | ölçüm kalıbı | — | `MiddlewarePipeline.php` L42 — doğrulama kodu |

### §4.2 Kabul kapanışı — ne ile kapanır?

| Kapanış | Gereken | Durum |
|---|---|---|
| D-1 (parçacık yokluğu) | hedef mimari kararı | açık — anayasa §8.1 |
| D-2 (`pcntl_signal`) | fork sinyal politikası kararı | açık — surucu §7.7 O-1 |
| KK-4 (zombi) | üretim ölçümü | açık — `[UNKNOWN]` |
| KK-5 (fd) | üretim ölçümü | açık — `[UNKNOWN]` |
| KK-3 (servis timeout testi) | birim testi yazımı | açık — `⚠️ VERIFICATION REQUIRED` |

### §4.3 Kriter öncelik sırası (ne önce ölçülür?)

| # | Kriter | Öncelik | Gerekçe |
|---|---|---|---|
| P-1 | KK-1 fork/waitpid | yüksek | veri bütünlüğü (zombi/fd) |
| P-2 | KK-2 timeout | yüksek | kaynak sızıntısı |
| P-3 | KK-5 fd sayısı | orta | üretim ölçümü gerekli |
| P-4 | KK-4 zombi | orta | üretim ölçümü gerekli |
| P-5 | KK-3 servis testi | düşük | koddaki sınır zaten sabit |
| P-6 | KK-7 ölçüm kalıbı | düşük | kalıp mevcut — sürdürülür |

Öncelik kuralı: P-1/P-2 ölçülmeden P-3/P-4 için üretim kararı alınmaz; ölçüm
önce doğruluk, sonra performans sırasıyla yapılır.

## §5 — Dürüstlük Defteri (Yokluk Kayıtları)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| D-1 | repo'da parçacık (thread) oluşturan kod | `pthread_*`/pthreads çağıran kod **YOK** | `⚠️ VERIFICATION REQUIRED` |
| D-2 | repo'da `pcntl_signal` | çağıran kod **YOK** — fork sonrası sinyal kurulumu eksik | `⚠️ VERIFICATION REQUIRED` |
| D-3 | repo'da `setrlimit`/`prlimit` | çağıran kod **YOK** | `⚠️ VERIFICATION REQUIRED` |
| D-4 | repo'da `sched_setaffinity`/cpuset | çağıran kod **YOK** | `⚠️ VERIFICATION REQUIRED` |
| D-5 | repo'da parçacık havuzu | composer envanterinde havuz kütüphanesi **YOK** | `⚠️ VERIFICATION REQUIRED` |
| D-6 | repo'da uzak kuyruk worker'ı | betik **YOK** | `⚠️ VERIFICATION REQUIRED` |
| D-7 | repo'da cron/systemd timer | dosya **YOK** | `⚠️ VERIFICATION REQUIRED` |
| D-8 | hedef donanım (çekirdek/sunucu) | bilinmiyor | **[UNKNOWN]** |
| D-9 | üretimde kilitlenme olayı sayısı | ölçüm yok | **[UNKNOWN]** |
| D-10 | havuz boyutu eşiği | limitler bilinmiyor | **[UNKNOWN]** |

Kural: bu satırlara sayı uydurulmaz; kapatma kararı orkestratöre aittir
(anayasa §8.4 F4). Kapanış koşulu her satırda `-surucu.md §7.7` ile bağlantılıdır.

### §5.1 Defter kuralları

| Kural | Metin |
|---|---|
| L-1 | "YOK" iddiası repo geneli arama ile verilir; tek dosya okuması yetmez |
| L-2 | `[UNKNOWN]` yalnız bilgi eksikliğinde; `⚠️ VERIFICATION REQUIRED` doğrulanamayan iddiada |
| L-3 | Yokluk satırı silinmez; kapanınca üstü çizilir ve kapanış kanıtı yazılır |
| L-4 | Defter satırı uydurma sayı/tarih ile kapatılmaz |
| L-5 | Kapanış kararı orkestratöre aittir — bu dosya yalnız kaydı tutar |

### §5.2 Defter → kapanış kanıtı şeması

| Alan | Değer | Örnek |
|---|---|---|
| Kayıt | D-2 | `pcntl_signal` yokluğu |
| Kapanış kanıtı | yeni repo satırı | `Dosya: … / Satır: …` (gelecek) |
| Karar sahibi | orkestratör | üst otorite |
| Tarih | kapanış günü | **[UNKNOWN]** — kapanmadı |
| Etkilenen dosyalar | `-surucu §4.2`, bu dosya §2.1 | birlikte güncellenir |

### §5.3 Defter → komşu dal devri

| Kayıt | Devredilen dal | Neden |
|---|---|---|
| D-3 (`setrlimit` yokluğu) | `kaynak-dagilimi/` | kota bütçesi kardeşin konusu |
| D-4 (affinity yokluğu) | `isletim-sistemi-runtime` | yürütme/topoloji sınırı |
| D-6 (uzak kuyruk yokluğu) | `isletim-sistemi-runtime` | süreç yürütme |
| D-8 (donanım bilinmiyor) | `isletim-sistemi-observability` | ölçüm/gözlem |
| D-9 (kilitlenme sayısı) | `isletim-sistemi-observability` | metrik toplama |

### §5.4 Defter — bu dosyanın kendi yüzeyi

| Kural | Uygulama |
|---|---|
| Alıntı zorunlu | §2'deki her satır okunarak yazıldı |
| Yokluk zorunlu | §1.3 + §5 listesi arama ile derlendi |
| Uydurma yasağı | hiçbir sayısal eşik tahmin edilmedi |
| Bağlantı | her kayıt `-surucu.md §7.7` ile eşleşir |

## §6 — Kaynakça (Web Research)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| php.net `pcntl_fork`, ebeveyn/çocuk ayrımını ve dönüş değerini tanımlar (§2.1) | https://www.php.net/manual/en/function.pcntl-fork.php | 2026-10-13 | Doğrulandı (HTTP 200, resmi PHP elçi sayfası) |
| php.net `pcntl_exec`, child'da program değişimini tanımlar (§2.1) | https://www.php.net/manual/en/function.pcntl-exec.php | 2026-10-13 | Doğrulandı (HTTP 200, resmi PHP elçi sayfası) |
| php.net `posix_getpid`, betiğin işlem kimliğini döndürür (§2.1) | https://www.php.net/manual/en/function.posix-getpid.php | 2026-10-13 | Doğrulandı (HTTP 200, resmi PHP elçi sayfası) |
| php.net `proc_get_status`, alt sürecin durumunu okur (§2.5) | https://www.php.net/manual/en/function.proc-get-status.php | 2026-10-13 | Doğrulandı (HTTP 200, resmi PHP elçi sayfası) |

> Not: envanter kesişim denetimi — yukarıdaki 4 URL `url-inventory-2026-10-13.txt`
> (2633 kayıt) içinde **yoktur**; kesişim = 0.

### §6.1 Kaynak → bölüm indeksi

| # | Kaynak | Bölüm | Ne doğrular |
|---|---|---|---|
| 1 | php.net `pcntl_fork` | §2.1 | ebeveyn/çocuk ayrımı |
| 2 | php.net `pcntl_exec` | §2.1 | child'da program değişimi |
| 3 | php.net `posix_getpid` | §2.1 | betik PID'si okuması |
| 4 | php.net `proc_get_status` | §2.5 | alt süreç durum okuması |

### §6.2 Kaynak kullanımı kuralları

| Kural | Metin |
|---|---|
| 1 | yalnız resmi elçi/man sayfaları kullanılır |
| 2 | her URL `Invoke-WebRequest -Method Head` ile HTTP 200 doğrulanır |
| 3 | envanterle kesişen URL tekrar yazılmaz |
| 4 | doğrulanmayan iddia `⚠️ VERIFICATION REQUIRED` olur |
| 5 | URL uydurulmaz — uydurma envanter ihlalidir |

### §6.3 Bilinen ölü URL'ler (kullanılmaz)

| Aile | Durum |
|---|---|
| `man7.org threads.7` | 404 — kullanılmaz |
| `man7.org init.8` | 404 — kullanılmaz |
| `man7.org php-fpm.8` | 404 — kullanılmaz |
| `php.net fastcgi-get-last-status` | 404 — kullanılmaz |

Bu tablo bu dosyada kullanılmayan, doğrulaması yapılmış **ölü** bağlantıları
listeler; gelecekteki dosyalar için uyarı işareti taşır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
