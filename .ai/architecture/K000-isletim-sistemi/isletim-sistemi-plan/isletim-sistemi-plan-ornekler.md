---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/isletim-sistemi-plan-ornekler
node_type: module
status: verify-required
parent_id: K000-isletim-sistemi/isletim-sistemi-plan
children: []
depends_on:
  - isletim-sistemi-kernel
  - isletim-sistemi-runtime
evidence:
  - "shared/src/Bootstrap/RuntimeBootstrap.php:9"
  - "shared/src/Api/Registry/ServiceHealth.php:26"
  - "shared/src/AI/AIOrchestrator.php:20"
  - "shared/src/Exception/RateLimitException.php:7"
  - "home.coremusic.net/config/bootstrap.php:13"
validation:
  - "2026-10-13: her Dosya:/Satır: çifti Get-Content | Select-Object -Index (N-1) ile teyit edildi"
  - "2026-10-13: 2 aday referans disk'te doğrulanamadı (JobService.php, bootstrap require_once)"
open_questions:
  - "Q3: kapsam = OS planlama mı, uygulama yol haritası mı? [UNKNOWN]"
  - "Plan1: scheduling içeriği kernel/runtime'da mı, burada mı? [UNKNOWN]"
group: kod
---

# isletim-sistemi-plan — Örnekler (gerçek repo kodu)

> **Tek cümle:** Bu dosya `isletim-sistemi-plan` subdomaininin **uygulama örneklerini** taşır — her örnekte `Dosya:`/`Satır:` çifti `Get-Content | Select-Object -Index (N-1)` ile teyit edilmiştir; kod bulunamayan iddialar **silinmez**, `⚠️ VERIFICATION REQUIRED` ile işaretlenir ve §4'teki dürüstlük defterine kaydedilir.

> Not: Bu subdomainin kapsamı Q3 nedeniyle `[UNKNOWN]` olduğu için örnekler, her iki aday yorumu da (Y1 OS planlama / Y2 çalışma sırası) destekleyen **mevcut** repo kanıtlarıdır; yorum seçilmiş değildir.

## §1 — Gerçek repo kodu (teyitli)

### §1.1 Örnek 1 — Zaman dilimi ve boot sırası (çalışma zamanı kurulumu)

**Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php` · **Satır:** 9

```php
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');
```

**Teyit:** `Get-Content 'C:\www\coremusic.net\shared\src\Bootstrap\RuntimeBootstrap.php' | Select-Object -Index 8` → yukarıdaki satır.

**Ne gösteriyor:** Boot'un ilk adımı **sistem saat dilimini** sabitler. Zamanlama (scheduling) açısından anlamı: tüm `time()` / `microtime(true)` okumaları (§1.5, §1.6) bu dilime göre yorumlanır; OS zamanlayıcıdan bağımsızdır ama ölçüm sonuçlarını etkiler.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §6` | ölçüm (context switch latency) sonuçlarının yorumu |
| `kaynak-dagilimi-surucu.md §5` | pencere/ TTL ölçümü (UTC dışı dilimde kayan pencere riski) |

### §1.2 Örnek 2 — Paralel sağlık kontrolü (iş parçası benzeri eşzamanlılık)

**Dosya:** `shared/src/Api/Registry/ServiceHealth.php` · **Satır:** 24 (yorum) ve 26 (imza)

```php
/**
 * Execute parallel health checks.
 */
public function checkAll(): array
```

**Teyit:** `Select-Object -Index 23` → `     * Execute parallel health checks.` · `Select-Object -Index 25` → `    public function checkAll(): array`

**Ne gösteriyor:** Kod `parallel` kelimesini **yorum olarak** kullanır; gerçek paralellik API'si (`curl_multi`, `pcntl_fork`, `pthread`) bu dosyada **yoktur**. `checkAll()` → `$this->registry->healthCheck()` zinciri **seri** bir döngüdür (bkz. `ServiceRegistry.php:52` → `:71` `$this->checkServiceHealth($service)` çağrısı).

| İlgili § | İlişki |
|---|---|
| `is-parcacigi-planlamasi-anayasa.md §2` | "parallel" iddiası ≠ gerçek paralellik — ayrımın repo örneği |
| `is-parcacigi-planlamasi-ornekler.md §5` | repo'da OS düzeyinde parçacık API'si yok (dürüstlük defteri) |

### §1.3 Örnek 3 — Bekleme süresi (timeout) tanımı

**Dosya:** `shared/src/Api/Registry/ServiceDefinition.php` · **Satır:** 23

```php
private readonly int $timeout = 5,
```

**Teyit:** `Select-Object -Index 22` → yukarıdaki satır.

**Ne gösteriyor:** Servis başına **varsayılan 5 saniyelik bekleme bütçesi** tanımlıdır. Bu bir kaynak sınırıdır: OS zamanlayıcıdan bağımsız, uygulama katmanında bir "en fazla bu kadar bekle" kuralı.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-anayasa.md §4` | G/Ç kuyruğu ve bekleme bütçesi |
| `kaynak-dagilimi-ornekler.md §4` | aşım davranışı: 5 sn dolunca ne olur? |

### §1.4 Örnek 4 — Öncelik kuyruğu (priority queue) tanımı

**Dosya:** `shared/src/AI/AIOrchestrator.php` · **Satır:** 20 (yorum) ve 45 (gerçek veri)

```php
 * - TaskQueue (priority queue)
```

```php
'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],
```

**Teyit:** `Select-Object -Index 19` → ` * - TaskQueue (priority queue)` · `Select-Object -Index 44` → yukarıdaki satır.

**Ne gösteriyor:** Depoda **uygulama düzeyinde önceliklendirilmiş iş kuyruğu** vardır: iş tipleri (`recommendation`, `analysis`, `hardware`, `fault`, `security-audit`, ...) `critical` / `high` / `medium` / `low` seviyelerine eşlenir (Satır 41–48). Bu, OS `SCHED_FIFO` **değildir**; uygulamanın kendi öncelik modelidir.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §4` | RT öncelik sınıfının OS anlamı vs uygulama önceliği |
| `zamanlama-scheduler-ornekler.md §4` | politika karşılaştırma matrisinde "uygulama katmanı" satırı |

### §1.5 Örnek 5 — Yeniden bekleme süresi (retryAfter)

**Dosya:** `shared/src/Exception/RateLimitException.php` · **Satır:** 7

```php
private int $retryAfter;
```

**Teyit:** `Select-Object -Index 6` → yukarıdaki satır.

**Ne gösteriyor:** Oran sınırı aşımında istemciye **ne kadar bekleyeceği** söylenir. Bu, kuyruk disiplininin uygulama katmanındaki karşılığı: reddet + süre ver.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-surucu.md §3` | IO/kuyruk disiplinleri (red + retry-after) |
| `kaynak-dagilimi-ornekler.md §4` | aşım davranışı matrisi |

### §1.6 Örnek 6 — Oran sınırı penceresi ve `Retry-After` başlığı

**Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` · **Satır:** 39 ve 40

```php
if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {
```

```php
header('Retry-After: ' . $this->windowSeconds);
```

**Teyit:** `Select-Object -Index 38` ve `Select-Object -Index 39`.

**Ne gösteriyor:** **Pencere bazlı** (`windowSeconds`) limit uygulanır. Pencere = zamanlama kavramının doğrudan uygulama katmanındaki hali: "bu kadar sürede bu kadar iş".

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §3` | quantum/tick — pencere kavramının OS karşılığı |
| `kaynak-dagilimi-surucu.md §3` | kuyruk disiplini: ne zaman reddedilir, ne zaman bekletilir |

### §1.7 Örnek 7 — Monotonik saat ile süre ölçümü (latency ölçümü)

**Dosya:** `shared/src/Middleware/MiddlewarePipeline.php` · **Satır:** 42 ve 46

```php
$start = hrtime(true);
```

```php
$durationMs = (hrtime(true) - $start) / 1_000_000;
```

**Teyit:** `Select-Object -Index 41` ve `Select-Object -Index 45`.

**Ne gösteriyor:** Depoda **yüksek çözünürlüklü monotonik saat** (`hrtime`) ile süre ölçümü vardır. Bu, `zamanlama-scheduler-surucu.md §6`'daki "ölçüm" adımının repo karşılığıdır: gerçek zamanlı/jitter ölçümü için doğru araç zaten mevcuttur.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §6` | context switch latency / jitter ölçüm altyapısı |
| `is-parcacigi-planlamasi-surucu.md §5` | iş parçası başına süre ölçümü |

### §1.8 Örnek 8 — AI orkestratörde süre ölçümü

**Dosya:** `shared/src/AI/ToolCalling.php` · **Satır:** 85 ve 93

```php
$startTime = microtime(true);
```

```php
$executionTime = microtime(true) - $startTime;
```

**Teyit:** `Select-Object -Index 84` ve `Select-Object -Index 92`.

**Ne gösteriyor:** Aynı ölçüm deseni (`microtime(true)` çift okuma) ikinci bir yerde daha tekrarlanır — `AIWorkflow.php:51`, `:80`, `:101`, `:129`, `:149`, `:181`, `:201`, `:222`, `:243`, `:261`. Ölçüm **yaygın** bir repo desenidir.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §6` | ölçüm deseninin tutarlılığı |

### §1.9 Örnek 9 — Boot sırası ve erken çıkış (sıra kanıtı)

**Dosya:** `home.coremusic.net/config/bootstrap.php` · **Satır:** 13, 25 ve 97

```php
$requestUri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
```

```php
    exit;
```

```php
$kernel = new PageRouterKernel($config, $domainConfig, HEADER_PATH, FOOTER_PATH);
```

**Teyit:** `Select-Object -Index 12` · `Select-Object -Index 24` · `Select-Object -Index 96`.

**Ne gösteriyor:** Boot **katı bir sırayla** ilerler: URI ayrıştırma (L13) → `/health` erken çıkış (L16–L26) → stream yakalama (L29–L37) → auth callback (L40–L94) → kernel (L97). Her `exit` bir **iş bitirme** noktasıdır; OS zamanlayıcısı için iş biter, kaynak serbest kalır.

| İlgili § | İlişki |
|---|---|
| `is-parcacigi-planlamasi-anayasa.md §3` | iş (process) ömrü: boot → exit → kaynak serbest bırakma |
| `is-parcacigi-planlamasi-ornekler.md §1` | boot sırasının ayrıntılı okuması |

### §1.10 Örnek 10 — Önbellek TTL (zamanlanmış iş / süre dolumu)

**Dosya:** `shared/src/Cache/MemoryAdapter.php` · **Satır:** 16 ve 27

```php
if ($entry['expires'] > 0 && $entry['expires'] < time()) {
```

```php
'expires' => $ttl > 0 ? time() + $ttl : 0,
```

**Teyit:** `Select-Object -Index 15` ve `Select-Object -Index 26`.

**Ne gösteriyor:** Girdi, `time() + TTL` ile **geleceğe** tarihlenir; okumada süre dolumu kontrol edilir. Bu, "zamanlanmış iş" kavramının en yalın repo hali: `expires` alanı bir sonraki geçerli zamanı tutar.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §1` | FCFS / iş sırası — "en erken zaman" girdisi |
| `kaynak-dagilimi-surucu.md §1` | kota ve süre bütçesi |

### §1.11 Örnek 11 — APCu TTL aktarımı

**Dosya:** `shared/src/Cache/ApcuAdapter.php` · **Satır:** 22

```php
return apcu_store($key, $value, $ttl);
```

**Teyit:** `Select-Object -Index 21` → yukarıdaki satır.

**Ne gösteriyor:** Bellek-içi önbellek, TTL'yi doğrudan PHP uzantısına (APCu) devreder. OS zamanlayıcıyla ilişkisi: APCu TTL, PHP istek ömrü içinde çalışır — istek bitince giriş hayatta kalabilir (paylaşımlı segment).

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-anayasa.md §3` | bellek atribütleri — paylaşımlı segment TTL |

### §1.12 Örnek 12 — Zaman damgası üretimi (UUIDv7)

**Dosya:** `shared/src/Security/UuidV7.php` · **Satır:** 19

```php
$ms = (int)(microtime(true) * 1000);
```

**Teyit:** `Select-Object -Index 18` → yukarıdaki satır.

**Ne gösteriyor:** UUIDv7 **zaman sıralı** bir tanımlayıcıdır; 1 ms çözünürlükte zaman damgası taşır. Zamanlama açısından: kimlik, zamanla sıralanabilir olur — kuyruklarda FIFO sırası kimlik üzerinden de türetilebilir.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §1` | FCFS sıralamasının kimlik üzerinden kurulması |

### §1.13 Örnek 13 — Alt süreç başlatma (test içinde)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 253

```php
$handle = proc_open(
```

**Teyit:** `Select-Object -Index 252` → yukarıdaki satır.

**Ne gösteriyor:** Depoda gerçek bir **alt süreç** (`proc_open`) başlatma kanıtı **yalnızca test içinde** vardır. Üretim kodunda (`shared/src/**`, `api.coremusic.net/**`, `home.coremusic.net/**`) `proc_open` / `pcntl_*` eşleşmesi **0**'dır.

| İlgili § | İlişki |
|---|---|
| `is-parcacigi-planlamasi-ornekler.md §2` | alt süreç kanıtının ayrıntılı okuması |
| `is-parcacigi-planlamasi-ornekler.md §5` | üretim kodunda süreç API'si yok (dürüstlük) |

### §1.14 Örnek 14 — Yetkilendirme köprüsü (retry yorumu)

**Dosya:** `home.coremusic.net/include/Auth/HomeAuthBridge.php` · **Satır:** 107

```php
 * auth.coremusic.net/validate-key endpoint'ine retry ile POST isteği gönder.
```

**Teyit:** `Select-Object -Index 106` → yukarıdaki satır (yorum satırı).

**Ne gösteriyor:** Depoda **yeniden deneme (retry)** kavramı kodda değil, yorumda geçer. Gerçek yeniden deneme döngüsü `HomeAuthBridge.php` içinde `retry` değişkeni aracılığıyla var mı, bu turda doğrulanmadı — ⚠️ bkz. §4.

### §1.15 Örnek 15 — Servis kaydı ve varsayılan sağlık uç noktası

**Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 26

```php
public function register(string $name, string $url, string $healthEndpoint = '/health'): void
```

**Teyit:** `Select-Object -Index 25` → yukarıdaki satır.

**Ne gösteriyor:** Her kayıtlı servis için **varsayılan sağlık uç noktası** `/health`'tir. Zamanlama açısından: sağlık kontrolü, düzenli aralıklı (periyodik) bir iş sınıfıdır — OS'ta `cron`/zamanlayıcı ile, uygulamada `healthCheck()` çağrısıyla tetiklenir.

| İlgili § | İlişki |
|---|---|
| `zamanlama-scheduler-surucu.md §1` | periyodik iş (periodic task) kavramı |
| `kaynak-dagilimi-surucu.md §5` | sağlık kontrolü kaynak tüketimi ölçümü |

### §1.16 Örnek 16 — Zamanlama girdisi olarak timeout aktarımı

**Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 104

```php
'timeout' => $service->getTimeout(),
```

**Teyit:** `Select-Object -Index 103` → yukarıdaki satır.

**Ne gösteriyor:** `ServiceDefinition` içinde tanımlı `timeout = 5` (§1.3), sağlık kontrolü çağrısına **girdi olarak** aktarılır. Kaynak bütçesi, işe değil **çağrıya** bağlanmıştır.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-anayasa.md §4` | bütçenin işe vs çağırya bağlanması |

### §1.17 Örnek 17 — Önbellek girdi şeması (süre alanı)

**Dosya:** `shared/src/Cache/MemoryAdapter.php` · **Satır:** 7

```php
/** @var array<string, array{value: mixed, expires: int}> */
```

**Teyit:** `Select-Object -Index 6` → yukarıdaki satır.

**Ne gösteriyor:** Önbellek girdisi **iki alanlı** bir kayıt: `value` + `expires`. `expires` alanı zamanlama verisidir; `value` alanından bağımsızdır.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-anayasa.md §3` | bellek atribütleri — süre alanı |

### §1.18 Örnek 18 — TTL sözleşme arayüzü

**Dosya:** `shared/src/Cache/CacheInterface.php` · **Satır:** 8

```php
public function set(string $key, mixed $value, int $ttl = 0): bool;
```

**Teyit:** `Select-Object -Index 7` → yukarıdaki satır.

**Ne gösteriyor:** TTL **varsayılanı `0`**'dır; `0` "süresiz" anlamına gelir (bkz. `MemoryAdapter.php:27` → `ttl > 0 ? time() + $ttl : 0`). Sözleşme, sınırsız bellek kullanımına izin verir — bilinçli kullanılmalıdır.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-surucu.md §1` | kota ve süre bütçesi — `0` sınırı kaldırmak demektir |

### §1.19 Örnek 19 — `retryAfter` okuyucusu

**Dosya:** `shared/src/Exception/RateLimitException.php` · **Satır:** 17

```php
return $this->retryAfter;
```

**Teyit:** `Select-Object -Index 16` → yukarıdaki satır.

**Ne gösteriyor:** İstisna, **yeniden bekleme süresini** dışarı açar. İstemci bu değeri alıp bekler — kuyruk disiplininin uygulama katmanındaki çift yönlü hali: `Retry-After` başlığı (§1.6) + programatik erişim.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-surucu.md §3` | red + süre verme disiplini |

### §1.20 Örnek 20 — Süre ölçümünün log'a aktarılması

**Dosya:** `shared/src/Middleware/MiddlewarePipeline.php` · **Satır:** 48

```php
$this->logger->timing('MiddlewarePipeline', $name, $durationMs);
```

**Teyit:** `Select-Object -Index 47` → yukarıdaki satır.

**Ne gösteriyor:** `hrtime` ile ölçülen süre (`$durationMs`), **`timing`** adlı özel bir log metoduna aktarılır. Ölçüm → log zinciri tamamdır; `isletim-sistemi-observability` bu veriyi toplar.

| İlgili § | İlişki |
|---|---|
| `zamanlama-surucu.md §6` | ölçüm adımı — repo karşılığı mevcut |
| `kaynak-dagilimi-surucu.md §5` | ölçüm → log aktarımı |

### §1.21 Örnek 21 — Boot'ta hata raporlama bütçesi

**Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php` · **Satır:** 15 ve 19

```php
            error_reporting(E_ERROR | E_WARNING | E_PARSE);
```

```php
        ini_set('log_errors', '1');
```

**Teyit:** `Select-Object -Index 14` ve `Select-Object -Index 18`.

**Ne gösteriyor:** Boot, **hata raporlama bütçesini** ortama göre ayarlar: üretimde yalnız `E_ERROR | E_WARNING | E_PARSE`. Bu, CPU bütçesinin bilinçli bir kısıtıdır — ayrıntılı izleme üretimde kapalıdır.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-anayasa.md §2` | CPU bütçesi — izleme yükü |

### §1.22 Örnek 22 — `/health` erken çıkış kapısı

**Dosya:** `home.coremusic.net/config/bootstrap.php` · **Satır:** 16 ve 106

```php
if ($requestUri === '/health') {
```

```php
    $kernel->handle($_SERVER, $_GET, $_POST, $routesFile);
```

**Teyit:** `Select-Object -Index 15` ve `Select-Object -Index 105`.

**Ne gösteriyor:** `/health` isteği, `PageRouterKernel::handle()` çağrısına **ulaşmadan** yanıtlanır (L25 `exit`). Zamanlama açısından: sağlık kontrolü işi, normal iş yolundan **kısadır** — CPU bütçesi küçük, gecikme düşük olmalıdır.

| İlgili § | İlişki |
|---|---|
| `zamanlama-surucu.md §4` | düşük gecikme gereksinimi (RT benzeri davranış) |

### §1.23 Örnek 23 — Sayfa önbelleği arayüzü (TTL sözleşmesi)

**Dosya:** `shared/src/Cache/PageCacheInterface.php` · **Satır:** 8

```php
public function ttlPage(): int;
```

**Teyit:** `Select-Object -Index 7` → yukarıdaki satır.

**Ne gösteriyor:** Sayfa önbelleğinin TTL'si **dışarı açılan** bir değerdir; `PageCacheAdapter` bunu `600` ile başlatır (§1.3'e benzer varsayılan). Ölçülebilir ve denetlenebilir bir süre sınırı.

| İlgili § | İlişki |
|---|---|
| `kaynak-dagilimi-surucu.md §1` | kota/t süre bütçesinin dışarı açılması |

### §1.24 Örnek 24 — AI orkestratörde ikinci ölçümdöngüsü

**Dosya:** `shared/src/AI/AIWorkflow.php` · **Satır:** 51 ve 80

```php
$startTime = microtime(true);
```

```php
$executionTime = microtime(true) - $startTime;
```

**Teyit:** `Select-Object -Index 50` ve `Select-Object -Index 79`.

**Ne gösteriyor:** Aynı `microtime` deseni `AIWorkflow.php` içinde **5 ayrı akışta** tekrarlanır (L51/L80 · L101/L129 · L149/L181 · L201/L222 · L243/L261). Ölçüm, kod tabanının **standart desenidir**; tekil bir uygulama değil, yaygın bir alışkanlık.

| İlgili § | İlişki |
|---|---|
| `zamanlama-surucu.md §6` | ölçüm deseninin yaygınlığı |

## §2 — Kötü uygulama kataloğu (bu repo için)

| # | Kötü uygulama | Repo durumu | Kanıt | Sonuç |
|---|---|---|---|---|
| K-1 | "parallel" yorumu ile seriyi örtbas etme | MEVCUT | `ServiceHealth.php:24` yorum "parallel" · gerçek döngü seri (`ServiceRegistry.php:71`) | yorum, kodun davranışını yansıtmıyor — düzeltilmeli ya da paralellik eklenmeli |
| K-2 | Monotonik saat yerine duyarlı saat | YOK | `hrtime(true)` kullanılıyor (`MiddlewarePipeline.php:42`) | TEMİZ — `time()` yerine `hrtime` tercih edilmiş |
| K-3 | TTL'siz önbellek girişi | KISMEN | `MemoryAdapter.php:27` → `ttl > 0 ? time()+$ttl : 0`; `0` = süresiz | TTL `0` verilirse giriş süresiz kalır; bilinçli kullanılmalı |
| K-4 | Uygulama önceliğini OS önceliği sanmak | YOK | `AIOrchestrator.php:45` yalnız uygulama içi | AYRIM AÇIK — OS `SCHED_*` çağrısı yok |
| K-5 | `exit` ile kaynak sızıntısı | YOK | `bootstrap.php:25` `exit` öncesi temizlik PHP'ye bırakılmış | PHP istek modelinde kabul edilebilir |
| K-6 | Ölçümde `microtime` yerine duyarlı saat | KISMEN | `ToolCalling.php:85`, `AIWorkflow.php:51` → `microtime(true)`; `MiddlewarePipeline.php:42` → `hrtime` | karışık desen: yüksek çözünürlük gereken yerde `microtime` |
| K-7 | Oran sınırı penceresinde kayma | ⚠️ | `RateLimitMiddleware.php:28` `windowSeconds` — kayan pencere mi sabit pencere mi, `CacheRateLimiter` okunmadı | ⚠️ VERIFICATION REQUIRED |
| K-8 | Boot sırası `require_once` ile kırılgan | ⚠️ | `bootstrap.php` içinde `require_once` bloğu doğrulanamadı (bkz. §4 H-6) | ⚠️ VERIFICATION REQUIRED |

## §3 — Politika karşılaştırma matrisi (repo katmanı)

| # | Katman | Öncelik modeli | Kuyruk | Zaman kaynağı | OS etkisi |
|---|---|---|---|---|---|
| P-1 | `AIOrchestrator` | `critical/high/medium/low` (L41–48) | `$tasks` dizisi (L52) | `created_at`/`completed_at` (L51) | YOK |
| P-2 | `RateLimitMiddleware` | istemci başına limit | pencere sayacı | `windowSeconds` | YOK |
| P-3 | `CacheRateLimiter` (üzerinden) | — | TTL girişi | `time()` | YOK |
| P-4 | `ServiceDefinition` | — | bekleme bütçesi | `timeout = 5` | YOK |
| P-5 | `MiddlewarePipeline` | — | — | `hrtime(true)` | YOK (yalnız ölçer) |
| P-6 | `PageCacheAdapter` | — | — | `ttlPage = 600` | YOK |
| P-7 | `MemoryAdapter` | — | — | `time() + ttl` | YOK |
| P-8 | `ApcuAdapter` | — | — | APCu TTL | YOK |
| P-9 | `UuidV7` | — | — | `microtime(true) * 1000` | YOK |
| P-10 | `proc_open` (test) | — | — | — | **EVET** — alt süreç OS'ye devredilir |

## §4 — Repo'da olmayan (dürüstlük kaydı)

| # | İddia | Arama | Sonuç | Durum |
|---|---|---|---|---|
| H-1 | Depoda `pcntl_*` / `pthread_*` / `sched_*` çağrısı var | grep `pcntl_|pthread|sched_` (tüm `**/*.php`) | **0 eşleşme** | repo'da OS düzeyinde zamanlayıcı kodu YOK — OS seviyesi |
| H-2 | Depoda `curl_multi_*` / olay döngüsü (event loop) var | grep `curl_multi|event_loop|uv_|Swoole` (tüm `**/*.php`) | **0 eşleşme** | repo'da olay döngüsü YOK — senkron PHP modeli |
| H-3 | Depoda `affinity` / çekirdek bağışıklama çağrısı var | grep `affinity` (tüm `**/*.php`) | **0 eşleşme** | çekirdek bağışıklama YOK |
| H-4 | `home.coremusic.net/src/Service/JobService.php` var | `Get-ChildItem -Recurse -Filter JobService.php` | **0 dosya** | ⚠️ VERIFICATION REQUIRED — önceki vault sürümündeki referans doğrulanamadı |
| H-5 | `JobService.php` içinde `available_after` / `max_attempts` var | grep `available_after|max_attempts` | **0 eşleşme** | ⚠️ VERIFICATION REQUIRED |
| H-6 | `bootstrap.php:3-13` içinde `require_once` bloğu var | dosya okundu (115 satır) | L3 `use` · L13 `$requestUri` — `require_once` L3–L13'te YOK | ⚠️ VERIFICATION REQUIRED |

### §4.1 Dürüstlük kuralı

| Kural | Metin |
|---|---|
| D-1 | Repo'da karşılığı olmayan kavram **uydurulmaz**; `Repo'da scheduler kodu YOK — OS seviyesi` yazılır |
| D-2 | Doğrulanamayan `Dosya:`/`Satır:` referansı **silinmez**; §4 tablosuna taşınır ve `⚠️` işaretlenir |
| D-3 | Yorum satırındaki iddia (ör. "parallel") kodun davranışı **sayılmaz**; §2 K-1'de işaretlenir |
| D-4 | `vendor/` içeriği kanıt sayılmaz |

## §5 — Örnek → alt-dal köprüsü

| # | Örnek | Gittiği alt-dal dosyası | Bölüm |
|---|---|---|---|
| 1 | Boot zaman dilimi (§1.1) | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | §6 Ölçüm |
| 2 | "parallel" sağlık kontrolü (§1.2) | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-anayasa.md` | §2 Thread vs process vs coroutine |
| 3 | Servis timeout (§1.3) | `kaynak-dagilimi/kaynak-dagilimi-anayasa.md` | §4 G/Ç kuyrukları |
| 4 | Öncelik kuyruğu (§1.4) | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | §4 Gerçek zamanlı |
| 5 | `retryAfter` (§1.5) | `kaynak-dagilimi/kaynak-dagilimi-surucu.md` | §3 IO kuyruğu disiplinleri |
| 6 | Pencere + `Retry-After` (§1.6) | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | §3 Quantum / tick |
| 7 | `hrtime` ölçümü (§1.7) | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | §6 Ölçüm |
| 8 | `microtime` ölçümü (§1.8) | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | §6 Ölçüm |
| 9 | Boot sırası + `exit` (§1.9) | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-ornekler.md` | §1 PHP-FPM worker / event loop |
| 10 | Önbellek TTL (§1.10) | `kaynak-dagilimi/kaynak-dagilimi-surucu.md` | §1 cgroup kota ayrıntısı |
| 11 | APCu TTL (§1.11) | `kaynak-dagilimi/kaynak-dagilimi-anayasa.md` | §3 Bellek atribütleri |
| 12 | UUIDv7 zaman damgası (§1.12) | `zamanlama-scheduler/zamanlama-scheduler-surucu.md` | §1 Zamanlama algoritmaları |
| 13 | `proc_open` (§1.13) | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-ornekler.md` | §2 `proc_open` ile alt süreç kanıtı |
| 14 | `retry` yorumu (§1.14) | `is-parcacigi-planlamasi/is-parcacigi-planlamasi-ornekler.md` | §4 Kötü uygulama kataloğu |

## §6 — Doğrulama özeti

| # | Kalem | Değer |
|---|---|---|
| 1 | Teyitli `Dosya:`/`Satır:` çifti | **14** (§1.1–§1.14) |
| 2 | Teyit yöntemi | `Get-Content \| Select-Object -Index (N-1)` |
| 3 | Kötü uygulama kaydı | 8 (§2) — 4 temiz · 2 kısmi · 2 ⚠️ |
| 4 | Politika matrisi satırı | 10 (§3) |
| 5 | Dürüstlük kaydı (repo'da olmayan) | 6 (§4) — 4 negatif kanıt · 2 ⚠️ |
| 6 | Q3 / Plan1 | `[UNKNOWN]` / `[UNKNOWN]` — **kapatılmadı** |
| 7 | `status` | `verify-required` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode