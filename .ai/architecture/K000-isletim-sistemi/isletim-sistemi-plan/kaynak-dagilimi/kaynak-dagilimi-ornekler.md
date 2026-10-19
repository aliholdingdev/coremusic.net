---
node_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi/kaynak-dagilimi-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-plan/kaynak-dagilimi
children: []
depends_on:
  - kaynak-dagilimi-anayasa
  - kaynak-dagilimi-surucu
  - isletim-sistemi-api
evidence:
  - "shared/src/Cache/PageCacheAdapter.php:11"
  - "shared/src/Cache/MemoryAdapter.php:16"
  - "shared/src/Cache/ApcuAdapter.php:17"
  - "shared/src/Security/CacheRateLimiter.php:16"
  - "shared/src/Api/Middleware/RateLimitMiddleware.php:22"
  - "shared/src/Exception/RateLimitException.php:7"
  - "shared/src/Middleware/RateLimiterMiddleware.php:20"
  - ".github/workflows/ci.yml:28"
validation:
  - "2026-10-13: 16 satır Read ile birebir okundu · alıntı dili özgün korundu"
  - "2026-10-13: OS düzeyi kota API'si grep → 0 (§5)"
open_questions:
  - "Q-kd-1 — cgroup kotası üretimde var mı? [⚠️ VERIFICATION REQUIRED]"
  - "Q-kd-4 — iki rate-limit yüzeyi tek kasada birleştirilecek mi? [UNKNOWN]"
group: kod
---

# kaynak-dagilimi — Örnekler (Kod Kanıtı)

> Bu dosya **kanıt** dosyasıdır: her satır ya repo'da `Dosya: … / Satır: …` ile
> doğrulanmış bir alıntı ya da dürüstlük defterinde işaretli bir **yokluk**tur.
> **Kritik katman uyarısı:** buradaki her örnek **PHP uygulama katmanındadır** —
> cgroup/RLIMIT/IO sınıfı **değildir** (anayasa §4.4 #3).

## §0 ELI15

| Madde | Değer |
|---|---|
| Okuma sırası | `-anayasa.md` (kural) → `-surucu.md` (mekanizma) → bu dosya (kanıt) |
| Kapsam | cache TTL · rate-limit penceresi · retryAfter · kota envanteri · kötü uygulama |
| Kapsam dışı | OS kotası (cgroup/RLIMIT/NUMA) — repo'da yok, §5 defteri |
| Kanıt dili | Türkçe yorum + İngilizce teknik terim; alıntı dili özgün (İngilizce) |
| Doğrulama tarihi | 2026-10-13 · Read ile satır okuması |

### §0.1 Dosyalar arası okuma haritası

| Bu dosya | Kardeş | Bağ |
|---|---|---|
| §1 · §2 | `-anayasa §4.4` | uygulama katmanı ayrımı |
| §2 | `-surucu §1.5` | cgroup var mı → yok |
| §3 | `-surucu §6.4` | negatif kanıt |
| §5 | `-anayasa §8` | açık sorular |

### §0.2 Soru → bölüm indeksi

| Soru | Bölüm |
|---|---|
| Repo'da çalışan kota örneği var mı? | §1 · §2 |
| Kötü uygulama nedir? | §3 |
| Kota aşılırsa ne olur? | §4 |
| Repo'da **olmayan** ne? | §5 |

## §1 Gerçek repo kodu (satır alıntıları)

### §1.1 Sayfa önbelleği TTL — `PageCacheAdapter.php`

| Alan | Satır | Alıntı |
|---|---|---|
| Alan tanımı | 9 | `private int $ttlPage;` |
| Varsayılan | 11 | `int $ttlPage = 600` |
| Okuyucu | 23–25 | `public function ttlPage(): int` → `$this->ttlPage` |

| Soru | Yanıt |
|---|---|
| Ne kotalanır? | sayfa önbelleğinin **ömür süresi** (saniye) |
| Aşım davranışı? | süre dolunca kayıt yeniden üretilir (yenileme) |
| OS katmanı mı? | ❌ hayır — uygulama katmanı |

### §1.2 Bellek önbelleği süresi — `MemoryAdapter.php`

| Alan | Satır | Alıntı |
|---|---|---|
| Süre denetimi | 16 | `if ($entry['expires'] > 0 && $entry['expires'] < time()) {` |
| Temizleme | 17 | `unset($this->store[$key]);` |
| Yok sayma | 18 | `return false;` |

> **Not:** `expires > 0` koşulu → `0` değeri **sınırsız** ömür demektir; kota **kapatılabilir**.

### §1.3 APCu TTL — `ApcuAdapter.php`

| Alan | Satır | Alıntı |
|---|---|---|
| İmza | 17 | `public function set(string $key, mixed $value, int $ttl = 0): bool` |
| Yoksa | 19–21 | `if (!function_exists('apcu_store')) { return false; }` |
| Yazma | 22 | `return apcu_store($key, $value, $ttl);` |

### §1.4 Rate-limit penceresi — `CacheRateLimiter.php`

| Alan | Satır | Alıntı |
|---|---|---|
| Önek | 10 | `private const KEY_PREFIX = 'rl:';` |
| Sınır sorgusu | 16 | `public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool` |
| Karşılaştırma | 23 | `return (int)$count >= $maxAttempts;` |
| Pencere başlangıcı | 31 | `$this->cache->set($cacheKey, 1, $windowSeconds);` |
| Sayaç artışı | 33 | `$this->cache->increment($cacheKey, 1);` |
| Sıfırlama | 37–40 | `reset(...)` → `delete` |

| Gözlem | Açıklama |
|---|---|
| Pencere nerede? | **ilk yazımın** TTL'inde (L31) — sonraki artışlar TTL yenilemez (L33) |
| `isLimited` pencereyi hesaplar mı? | hayır — yalnız sayaç karşılaştırır (L19–L23) |
| Sonuç | pencere bitişi tamamen cache TTL'ine bağlıdır |

### §1.5 Rate-limit middleware (API) — `RateLimitMiddleware.php`

| Alan | Satır | Alıntı |
|---|---|---|
| Varsayılan hak | 22 | `private const DEFAULT_MAX_REQUESTS = 60;` |
| Varsayılan pencere | 23 | `private const DEFAULT_WINDOW_SECONDS = 60;` |
| Bağımlılık | 26 | `private readonly CacheRateLimiter $rateLimiter,` |
| Sınır testi | 39 | `if ($this->rateLimiter->isLimited($key, $this->maxRequests, $this->windowSeconds)) {` |
| Yanıt başlığı | 40 | `header('Retry-After: ' . $this->windowSeconds);` |
| Yanıt kodu | 41–45 | `ApiResponse::error('RATE_LIMIT_EXCEEDED', 'Too many requests', 429)` |
| Anahtar biçimi | 62 · 67 | `'api:' . hash('sha256', $apiKey)` / `'ip:' . $ip` |
| Sayaç artışı | 49 | `$this->rateLimiter->increment($key, $this->windowSeconds);` |

### §1.6 Retry-After — iki yüzey

| # | Dosya | Satır | Alıntı |
|---|---|---|---|
| 1 | `shared/src/Exception/RateLimitException.php` | 7 | `private int $retryAfter;` |
| 2 | aynı | 11 | `parent::__construct($message, $errorCode, 429);` |
| 3 | aynı | 22 | `"Çok fazla deneme. {$seconds} saniye bekleyin."` (`LOGIN_RATE_LIMITED`) |
| 4 | `shared/src/Middleware/RateLimiterMiddleware.php` | 62 | `'headers' => ['Retry-After' => (string)$this->windowSeconds],` |
| 5 | aynı | 59 | `'httpStatus' => 503,` (rate-limiter kullanılamazsa) |

### §1.7 Fail-closed / fail-open — `RateLimiterMiddleware.php`

| Alan | Satır | Alıntı |
|---|---|---|
| Varsayılanlar | 39–40 | `int $maxRequests = 60` · `int $windowSeconds = 60` |
| Fail-closed listesi | 20–30 | `AUTH_FAIL_CLOSED_PATHS` — **9 uç** (`/login`, `/register`, `/forgot-password`, `/reset-password`, `/logout`, `/set-gender`, `/select-gender`, `/session`, `/validate-key`) |
| Cache yoksa (auth) | 55–64 | `503` + `rate_limiter_unavailable` + `Retry-After` |
| Cache yoksa (genel) | 66–68 | fail-open + `securityEvent` logu |
| Anahtar | 72 | `$key = self::CACHE_KEY_PREFIX . md5($ip);` |

### §1.8 Alıntı sözlüğü (özgün → Türkçe)

| Özgün | Türkçe |
|---|---|
| `RATE_LIMIT_EXCEEDED` | istek sınırı aşıldı |
| `rate_limiter_unavailable` | hız sınırlayıcı kullanılamıyor |
| `fail-closed` / `fail-open` | hatada kapat / hatada aç |
| `Retry-After` | yeniden deneme öncesi beklenecek süre (sn) |
| `ttl` | ömür süresi (saniye) |

### §1.9 Diğer uygulama katmanı yüzey satırları (2026-10-13 Read ile birebir)

> **Amaç:** "Repo'daki kota/yüzey envanteri §2.1'de bitti" **iddiası yoktur** — bu bölüm,
> §2.1 havuzuna **girmeyen** ama kaynak yüzeyine (dosya yolu · sunucu kimliği · hata
> gösterimi) dokunan üç satırı kanıt olarak taşır. Hepsi **uygulama/sunucu katmanıdır**,
> OS kotası **değildir** (anayasa §4.4 #3).

| # | Dosya:Satır | Alıntı (özgün) | Ne yüzeyi | OS katmanı mı? |
|---|---|---|---|---|
| 1 | `shared/src/Component/TemplateEngine.php:73` | `$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';` | geçici dizin + **yol ayracı** (taşınabilirlik yüzeyi) | ❌ |
| 2 | `shared/src/Component/TemplateEngine.php:74–75` | `if (file_put_contents($cachePath, $template, LOCK_EX) === false) {` · `throw new \RuntimeException("Template cache write failed: {$cachePath}");` | dosya kilidi + hata yüzeyi | ❌ |
| 3 | `shared/src/Api/Bff/DesktopBff.php:35–37` | `'platform' => php_uname('s'),` · `'phpVersion' => PHP_VERSION,` · `'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',` | platform/sunucu **kimlik okuması** | ❌ (yalnız okuma) |
| 4 | `home.coremusic.net/.htaccess:5–7` | `php_flag  display_errors off` · `php_flag  log_errors on` · `php_value error_log ../../coremusic_php_errors.log` | Apache katmanında PHP davranışı kotası | ❌ (sunucu) |

| Soru | Yanıt | Kanıt |
|---|---|---|
| #1'deki `DIRECTORY_SEPARATOR` neyi kanıtlar? | yol ayracı çalışma zamanında **seçilir** — betik sabitlenmez | alıntı |
| #3 platform okuması bir kota mı? | hayır — yalnız raporlama; hiçbir davranışı **sınırlamaz** | satır 34–38 okundu |
| #4 Apache kotası bu dosyada sayılır mı? | **havuz dışı** — birim "istek/saniye" değil, "göster/gösterme" | §2.6 matrisine bak |

> **Sınır cümlesi:** bu dört satır **kanıt** olarak eklenmiştir; "repo'da kaynak yüzeyi
> yalnız §2.1'deki sekiz kotayla sınırlıdır" cümlesi bu bölümle **geçersizdir** —
> envanter açık uçludur ve §5 defteri gibi **kapatılmaz**.

## §2 Kota örneği

### §2.1 Havuz envanteri (repo'daki tüm kotalar)

| # | Kota | Değer | Dosya:Satır | Birim | Tür |
|---|---|---|---|---|---|
| 1 | API istek hakkı | 60 | `Api/Middleware/RateLimitMiddleware.php:22` | istek | sayı |
| 2 | API pencere | 60 | aynı:23 | saniye | süre |
| 3 | Genel HTTP istek hakkı | 60 | `Middleware/RateLimiterMiddleware.php:39` | istek | sayı |
| 4 | Genel HTTP pencere | 60 | aynı:40 | saniye | süre |
| 5 | Sayfa cache ömrü | 600 | `Cache/PageCacheAdapter.php:11` | saniye | süre |
| 6 | Sağlık denetimi timeout | 5 | `Api/Registry/ServiceDefinition.php:23` | saniye | süre |
| 7 | APCu/bellek cache ömrü | 0 (sınırsız) | `Cache/ApcuAdapter.php:17` | — | bayrak |
| 8 | Fail-closed uç sayısı | 9 | `Middleware/RateLimiterMiddleware.php:20–30` | uç | sayı |

### §2.2 CI zaman bütçesi — `ci.yml`

| İş | `runs-on` | `timeout-minutes` | Satır |
|---|---|---|---|
| `php-lint` | `ubuntu-latest` | 15 | 27 · 28 |
| `php-test` (matrix) | `ubuntu-latest` | 15 | 69 · 70 |
| iş 3 | `ubuntu-latest` | 10 | 112 · 113 |
| iş 4 | `ubuntu-latest` | 10 | 144 · 145 |
| iş 5 | `ubuntu-latest` | 15 | 163 · 164 |
| iş 6 | `ubuntu-latest` | 20 | 180 · 181 |

> **Ölçüm:** 6 iş · 6'sı da tek platform (`ubuntu-latest`) · toplam üst sınır 85 dakika.
> Cache anahtarı platforma bağlıdır: `${{ runner.os }}-composer-...` (L44 · L46 · L96 · L98).

### §2.3 Sağlık denetimi — kota zinciri

| # | Katman | Değer | Satır | Etki |
|---|---|---|---|---|
| 1 | Servis timeout | 5 sn | `ServiceDefinition.php:23` | tek istek bekleme tavanı |
| 2 | Kullanım | `stream_context_create(['http'=>['timeout'=>...]])` | `ServiceRegistry.php:104` | gerçek uygulama |
| 3 | Döngü | `foreach ($this->services ...)` | `ServiceRegistry.php:56` | **seri** — N servis × 5 sn |
| 4 | Üst sınır (formül) | N × timeout | türetilmiş | en kötü durum |

> **Formül kanıtlıdır, sayı yoktur:** N (kayıtlı servis sayısı) bu oturumda
> **okunmadığı için** toplam süre `UNKNOWN` bırakılmıştır — uydurulmaz.

### §2.4 Öncelik haritası — `AIOrchestrator.php`

| Satır | Görev | `agent` | `priority` |
|---|---|---|---|
| 41 | `recommendation` | `backend` | `medium` |
| 42 | `analysis` | `embedded` | `high` |
| 43 | `eq-optimization` | `embedded` | `medium` |
| 44 | `hardware` | `audio-hw` | `high` |
| 45 | `fault` | `dsp-fw` | `critical` |
| 46 | `security-audit` | `security` | `critical` |
| 47 | `documentation` | `mo` | `low` |
| 48 | `testing` | `qa` | `medium` |

| Satır | Alıntı |
|---|---|
| 20 | `* - TaskQueue (priority queue)` (docblock) |
| 71 | `public function dispatchTask(string $type, array $params, string $priority = 'medium'): string` |
| 84 | `'priority' => $priority,` |

> **Dürüstlük:** L20'de `TaskQueue (priority queue)` yazıyor; `TaskQueue` adında bir sınıf
> `*.php` taramasında **bulunamadı** (§5 D-5). Öncelik **haritası** var, öncelikli
> kuyruk **uygulaması** yok.

### §2.5 CI yapılandırma kotaları (`.github/workflows/` — 2026-10-13 Read)

> **Katman notu:** bunlar **CI** kotalarıdır; anayasa §4.4 tablosunda 3. satır (uygulama)
> ile de, OS ile de **karıştırılmaz** — ayrı bir katmandır.

| # | Dosya | Satır | İçerik |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | — | toplam **203 satır** · **6 iş** |
| 2 | `.github/workflows/secret-scan.yml` | — | toplam **31 satır** · **1 iş** (`gitleaks`) |
| 3 | `ci.yml` | 8 | `name: CI Pipeline` |
| 4 | `ci.yml` | 10–12 | `on` → `push` · `pull_request` |
| 5 | `ci.yml` | 14–15 | `permissions:` → `contents: read` |
| 6 | `ci.yml` | 17–19 | `concurrency:` → `cancel-in-progress: true` |
| 7 | `ci.yml` | 21–22 | `env:` → `PHP_VERSION: '8.4'` |
| 8 | `ci.yml` | 68 · 162 | `needs: php-lint` · `needs: js-test` |
| 9 | `secret-scan.yml` | 11–12 · 14–16 · 21–22 | `permissions contents: read` · `concurrency` · `runs-on: ubuntu-latest` + `timeout-minutes: 10` |

| # | Kota | Değer | Ne kanıtlar | Kanıtlamaz |
|---|---|---|---|---|
| 1 | İş başına üst süre | 15+15+10+10+15+20 = **85 dk** | işler en geç bu sürede kesilir | kritik yol süresi değil |
| 2 | İzinler | `contents: read` | workflow **yazma yetkisi taşımaz** | deploy yetkisi olduğu |
| 3 | Eşzamanlılık | `cancel-in-progress: true` | aynı dalda eski koşu **iptal** edilir | koşuların sıralı olduğu |
| 4 | Bağımlılık | `needs` ×2 | 4 iş **paralel**, 2 iş **sıralı** | geçiş süresi |
| 5 | Sızıntı taraması | 1 job · 10 dk | tarama **var** | taramanın **geçtiği** |

### §2.6 Kota türü sınıflandırma matrisi (birim × mekanizma × katman)

| # | Birim | Mekanizma (repo) | Katman | Aşım tepkisi | Bölüm |
|---|---|---|---|---|---|
| 1 | istek | `CacheRateLimiter` sayaç + cache TTL | uygulama | 429 / `Retry-After` | §1.4 · §4 #1–#2 |
| 2 | saniye (ömür) | `PageCacheAdapter` TTL 600 · `MemoryAdapter` `expires` | uygulama | kayıt düşer | §1.1–§1.2 · §4 #5–#6 |
| 3 | saniye (timeout) | `ServiceDefinition` timeout 5 | uygulama | `unhealthy` | §2.3 · §4 #7 |
| 4 | dakika (üst sınır) | `timeout-minutes` (`ci.yml`) | CI | iş kesilir | §2.2 · §2.5 · §4 #8 |
| 5 | uç (liste) | `AUTH_FAIL_CLOSED_PATHS` 9 uç | uygulama (güvenlik) | 503 fail-closed | §1.7 · §4 #3 |
| 6 | bayrak (sınırsız) | APCu `ttl = 0` | uygulama | kota **kapalı** | §1.3 |
| 7 | gösterme (on/off) | `.htaccess:5–7` `php_flag` | sunucu (Apache) | hata gösterilmez/loglanır | §1.9 #4 |
| 8 | — (sınır yok) | cgroup `cpu.max` · `memory.max` · `io.max` | OS | OOM / throttle | §4 #9 → §5 D-1 |
| 9 | — (sınır yok) | `RLIMIT_*` · `sched_*` · `MPOL_*` | OS | syscall hatası | §5 D-2 · D-6 |

> **Okuma kuralı:** satır 1–7 **repo'da çalışan** kotalardır (kanıt: §1 · §2); satır 8–9
> bu depoda **yoktur** (kanıt: §5 defteri). İki küme aynı tabloda **tek "kota" diye**
> sayılmaz — bu dosyanın asıl disiplini budur.

## §3 Kötü uygulama kataloğu

### §3.1 Katalog

| # | Anti-pattern | Kanıt | Etki | Düzeltme |
|---|---|---|---|---|
| 1 | Seri sağlık denetimi + "parallel" yorumu | `ServiceRegistry.php:56` ↔ `ServiceHealth.php:24` | N × 5 sn | `curl_multi` / paralel istek |
| 2 | Pencere bitişinin tamamen TTL'ye bırakılması | `CacheRateLimiter.php:23` + `:31` | cache TTL=0 olursa kota hiç sıfırlanmaz | pencere başlangıcını anahtarda tut |
| 3 | İki ayrı rate-limit yüzeyi | `Api/Middleware/RateLimitMiddleware.php` ↔ `Middleware/RateLimiterMiddleware.php` | iki farklı davranış (429 vs 503/fail-open) | tek kasada birleştir |
| 4 | Dış süreci `shell_exec` + timeout yok | `AIEngine.php:290–295` | iş kilitlenebilir | timeout + kill |
| 5 | Ölçmeden eşik koyma | `hrtime` var, çekirdek sayacı yok | kota kör | önce §5.4 protokolü |
| 6 | OS katmanını uygulama katmanıyla karıştırma | §0 uyarı satırı | yanlış mimari çıkarım | anayasa §4.4 |

### §3.2 #1 — seri denetim (detay)

| Satır | Alıntı | Yorum |
|---|---|---|
| `ServiceHealth.php:24` | `* Execute parallel health checks.` | iddia: paralel |
| `ServiceRegistry.php:56` | `foreach ($this->services as $name => $service) {` | gerçek: seri |
| `ServiceRegistry.php:57` | `$results[$name] = $this->checkServiceHealth($service);` | her adımda tamamlanır |
| `ServiceRegistry.php:109` | `$response = @file_get_contents($url, false, $context);` | **bloklayan** çağrı |

> **Sonuç:** doküman yorumu ile davranış **uyuşmuyor**; bu bir *bulgudur*, hata **değildir**
> — davranış doğru, yorum yanıltıcıdır.

### §3.3 #2 — pencere TTL (detay)

| Adım | Satır | Ne olur | Risk |
|---|---|---|---|
| 1 | L31 | ilk istekte `set(..., $windowSeconds)` | pencere başlar |
| 2 | L33 | sonraki isteklerde `increment(...)` | TTL **yenilenmez** (doğru davranış) |
| 3 | L19–L23 | `isLimited` yalnız sayıyı okur | pencere sonunu **bilmez** |
| 4 | TTL = 0 | `MemoryAdapter` L16: `expires > 0` koşulu → süresiz | kota **asla sıfırlanmaz** |

### §3.4 #3 — iki rate-limit yüzeyi (detay)

| Özellik | API yüzeyi | HTTP yüzeyi |
|---|---|---|
| Dosya | `src/Api/Middleware/RateLimitMiddleware.php` | `src/Middleware/RateLimiterMiddleware.php` |
| Anahtar | `rl:` + `ip:`/`api:` (CacheRateLimiter L10 + L62/L67) | `rl:` + `md5($ip)` (L11 + L72) |
| Aşım yanıtı | `429` + `Retry-After` (L40–L45) | `Retry-After` başlığı (L62/L97) |
| Cache yoksa | tanımlı değil | `503` (auth) / fail-open (genel) |
| Önek | `rl:` | `rl:` |

> **Kesişim notu:** iki anahtar biçimi farklı olduğu için **çakışma yok** (`rl:ip:…` vs
> `rl:<md5hex>`); risk, **iki ayrı davranış** üretmesidir. Kesişim/etki ölçümü
> `⚠️ VERIFICATION REQUIRED` (Q-kd-4).

### §3.5 #4 — `shell_exec` (detay)

| Satır | Alıntı |
|---|---|
| 290–293 | `sprintf('ffprobe -v quiet ... "%s"', escapeshellarg($filePath))` |
| 295 | `$output = shell_exec($cmd);` |
| 296–298 | `$output === null` → varsayılan metadata döner |

> `extractMetadata()` içinde **timeout/kill mekanizması görülmemiştir** (L288–L300 okundu).
> `⚠️ VERIFICATION REQUIRED`: dosyanın tamamı okunmadı, başka yerde sınır olabilir.

## §4 Aşım davranışı matrisi

| # | Hangi kota | Aşım belirtisi | Sistem davranışı | Kullanıcıya dönen | Kanıt |
|---|---|---|---|---|---|
| 1 | API istek hakkı (60/60) | sayaç ≥ 60 | istek yürütülmez | `429` + `Retry-After: 60` | `RateLimitMiddleware.php:39–45` |
| 2 | Genel HTTP (60/60) | sayaç ≥ 60 | istek durdurulur | `Retry-After` başlığı | `RateLimiterMiddleware.php:62/97` |
| 3 | Auth uçları + cache yok | cache çözülemez | **fail-closed** | `503` + `rate_limiter_unavailable` | aynı:55–64 |
| 4 | Genel uç + cache yok | cache çözülemez | **fail-open** (devam) | normal yanıt + log | aynı:66–68 |
| 5 | Sayfa cache TTL (600) | süre doldu | kayıt düşer, yeniden üretilir | normal yanıt | `PageCacheAdapter.php:11` |
| 6 | Bellek cache `expires` | `expires < time()` | `unset` + `false` | yeniden hesap | `MemoryAdapter.php:16–18` |
| 7 | Sağlık timeout (5 sn) | süre aşıldı | `unhealthy` | `Connection failed` | `ServiceRegistry.php:104–115` |
| 8 | CI `timeout-minutes` | bütçe aşıldı | iş **kesilir** | CI hatası | `ci.yml:28` vb. |
| 9 | cgroup `memory.max` | hard aşıldı | OOM davranışı | süreç sonu | **repo'da yok** → §5 D-1 |
| 10 | `RLIMIT_*` | çekirdek reddi | syscall hatası | — | **repo'da yok** → §5 D-2 |

### §4.1 Aşım → tepki yönü

| # | Tepki | Kullanım | Örnek |
|---|---|---|---|
| 1 | **Reddet** (429) | hak doldu | §4 #1 |
| 2 | **Beklet** (`Retry-After`) | ne zaman deneneceği | §4 #1–#2 |
| 3 | **Kapat** (fail-closed) | güvenlik kritik | §4 #3 |
| 4 | **Aç** (fail-open) | hizmet sürekliliği | §4 #4 |
| 5 | **Düşür** (cache TTL) | ömür doldu | §4 #5–#6 |
| 6 | **Kes** (timeout) | bütçe doldu | §4 #7–#8 |

> **Tasarım kuralı:** 3 ve 4 **aynı** sistemde **aynı anda** bulunabilir — hangi uçta hangisi
> **listelenmelidir** (repo'da bu liste §1.7'dedir).

### §4.2 429 / `Retry-After` — resmi metin ile repo satır eşlemesi

> Kaynak: https://www.rfc-editor.org/rfc/rfc6585.html · HTTP 200 · 2026-10-13
> (RFC 6585 §4 · Standards Track · Nisan 2012)

| # | Alıntı (özgün) | Repo satırı | Uyuşma |
|---|---|---|---|
| 1 | "The 429 status code indicates that the user has sent too many requests in a given amount of time ("rate limiting")." | `RateLimitMiddleware.php:39–45` → `429` | ✅ |
| 2 | "The response representations SHOULD include details explaining the condition, and MAY include a Retry-After header indicating how long to wait before making a new request." | `RateLimitMiddleware.php:40` → `header('Retry-After: ' . $this->windowSeconds);` | ✅ (RFC "MAY" der, repo **gönderir**) |
| 3 | Örnek yanıt başlığı: `Retry-After: 3600` | repo değeri **60** (`DEFAULT_WINDOW_SECONDS = 60`, L23) | ✅ biçim aynı, değer farklı |
| 4 | "this specification does not define how the origin server identifies the user, nor how it counts requests." | repo tanımlar: `api:` + `sha256($apiKey)` (L62) · `ip:` + `$ip` (L67) · `md5($ip)` (L72) | ✅ — tanımlama **implementasyona** bırakılmıştır |
| 5 | "Responses with the 429 status code MUST NOT be stored by a cache." | repo'da 429 yanıtı için önbelleğe alma **görülmedi** (L39–45 okundu) | ⚠️ VERIFICATION REQUIRED — tüm önbellek yolları okunmadı |
| 6 | "servers are not required to use the 429 status code; when limiting resource usage, it may be more appropriate to just drop connections" (§7.2) | repo 429 kullanır + auth uçlarında **fail-closed 503** (L55–64) | ✅ — RFC'nin izin verdiği ikinci yol ayrıca mevcut |

> **Sonuç (bağlayıcı):** repo'nun 429 davranışı RFC 6585 §4 ile **çelişmiyor**; RFC'nin
> "MUST NOT ... stored by a cache" maddesi için **eksik okuma** vardır (satır 5) — bu bir
> "hata" değil, **açık kanıt boşluğu** olarak §5 defterine taşınmaz, burada ⚠️ kalır.

## §5 Repo'da olmayan (dürüstlük defteri)

### §5.1 Defter kuralları

| # | Kural |
|---|---|
| 1 | Yokluk, **arama yöntemiyle** kanıtlanır (grep deseni + kapsam + sonuç) |
| 2 | Yokluk, "var" iddiasını **imkânsız kılmaz** — yalnız "ben okumadım" der |
| 3 | Kayıt **kapatılmaz**; yalnız kanıt gelince güncellenir |
| 4 | Komşu dal dosyalarındaki iddialar bu defterle **çelişiyorsa** ⚠️ işaretlenir |

### §5.2 Kayıtlar

| # | Kayıt | Aranan desen | Kapsam | Sonuç |
|---|---|---|---|---|
| D-1 | cgroup kotası | `cgroup` · `cpu.max` · `memory.max` · `io.max` | yapılandırma/PHP | **0 kanıt** |
| D-2 | RLIMIT | `setrlimit` · `RLIMIT_` | `*.php` | **0 eşleşme** |
| D-3 | NUMA | `numa` · `mbind` | `shared/src` | **0 eşleşme** |
| D-4 | IO öncelik sınıfı | `ioprio` · `ionice` | `*.php` | **0 eşleşme** |
| D-5 | Öncelikli kuyruk sınıfı | `TaskQueue` | `*.php` | **1 docblock atfı** (`AIOrchestrator.php:20`) · sınıf **yok** |
| D-6 | OS zamanlama/parçacık API'si | `pcntl_` · `pthread_` · `sched_` · `affinity` | `*.php` | **0 eşleşme** |
| D-7 | Ölçüm: çekirdek sayacı | `cpu.stat` · `/proc/` | PHP/CI | **0 eşleşme** |

### §5.3 Komşu dal devri

| # | Defter kaydı | Nereye devredilir | Not |
|---|---|---|---|
| D-1 · D-2 · D-4 | OS kota API'si | `isletim-sistemi-kernel` | mekanizma orada |
| D-3 | NUMA | `isletim-sistemi-kernel` + `K001-donanım` | topoloji kapsam dışı |
| D-5 · D-6 | öncelikli kuyruk / parçacık | `is-parcacigi-planlamasi` · `zamanlama-scheduler` | §5 defteri de bakıyor |
| D-7 | ölçüm | `isletim-sistemi-observability` | toplama orada |

### §5.4 Bu dosyanın kendi yüzeyi

| # | Yüzey | Durum |
|---|---|---|
| 1 | Alıntı satırları | ✅ 16 satır Read ile teyitli |
| 2 | Formüller (N × timeout) | ✅ kanıtlı, **sayı yok** |
| 3 | cgroup/RLIMIT uygulaması | ⚠️ **yok** (D-1 · D-2) |
| 4 | Ölçüm eşikleri | ⚠️ **sayı yok** — anayasa §6.3 |
| 5 | Envanter açıklığı | ✅ §1.9 ile "tüm kotalar §2.1'dedir" iddiası **kaldırıldı** — envanter kapatılmaz |
| 6 | RFC 6585 §4 "MUST NOT ... stored by a cache" | ⚠️ repo'daki önbellek yolları **tam okunmadı** (§4.2 satır 5) |

## §6 Kaynakça

### §6.1 Web doğrulama (2026-10-13 · GET · envanter kesişimi 0)

| # | İddia | URL | Durum |
|---|---|---|---|
| 1 | cgroup v2 kota dosyaları (§4 #9 karşılığı) | https://docs.kernel.org/admin-guide/cgroup-v2.html | ✅ HTTP 200 |
| 2 | `RLIMIT_*` kümesi (§4 #10 karşılığı) | https://man7.org/linux/man-pages/man2/getrlimit.2.html | ✅ HTTP 200 |
| 3 | nice öncelik girdisi (§2.4 bağlamı) | https://man7.org/linux/man-pages/man2/nice.2.html | ✅ HTTP 200 |
| 4 | IO öncelik sınıfları (D-4 bağlamı) | https://man7.org/linux/man-pages/man2/ioprio_set.2.html | ✅ HTTP 200 |
| 5 | `proc(5)` süreç sayaçları (D-7 bağlamı) | https://man7.org/linux/man-pages/man5/proc.5.html | ✅ HTTP 200 |
| 6 | 429 / `Retry-After` resmi tanımı (§4.2) | https://www.rfc-editor.org/rfc/rfc6585.html | ✅ HTTP 200 · içerik okundu (2026-10-13) |
| 7 | cgroup v2 hiyerarşi kuralları (§2.6 #8 bağlamı) | https://man7.org/linux/man-pages/man7/cgroups.7.html | ✅ HTTP 200 (kardeş R-12 teyidi) |

> **Alan adı notu (2026-10-13):** `rfc-editor.org` bu dosyada **ilk kez** kullanılmıştır;
> diğer alan adları (`docs.kernel.org` · `man7.org`) kardeş envanterlerle kesişir — yeni
> alan adı eklenmemiştir.

### §6.2 Repo kanıtları özeti

| # | Dosya | Satır |
|---|---|---|
| 1 | `shared/src/Cache/PageCacheAdapter.php` | 9 · 11 · 23–25 |
| 2 | `shared/src/Cache/MemoryAdapter.php` | 16–18 |
| 3 | `shared/src/Cache/ApcuAdapter.php` | 17 · 19–22 |
| 4 | `shared/src/Security/CacheRateLimiter.php` | 10 · 16 · 23 · 31 · 33 · 37–40 |
| 5 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 22 · 23 · 39 · 40 · 41–45 · 49 · 62 · 67 |
| 6 | `shared/src/Exception/RateLimitException.php` | 7 · 11 · 22 |
| 7 | `shared/src/Middleware/RateLimiterMiddleware.php` | 11 · 20–30 · 39 · 40 · 55–68 · 72 · 97 |
| 8 | `shared/src/Api/Registry/ServiceDefinition.php` | 23 |
| 9 | `shared/src/Api/Registry/ServiceRegistry.php` | 56 · 104 · 109 |
| 10 | `shared/src/Api/Registry/ServiceHealth.php` | 24 · 26 |
| 11 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 · 46 |
| 12 | `shared/src/AI/AIOrchestrator.php` | 20 · 41–48 · 71 · 84 |
| 13 | `shared/src/AI/AIEngine.php` | 290–298 |
| 14 | `.github/workflows/ci.yml` | 27 · 28 · 44 · 69 · 70 · 96 · 112 · 113 · 144 · 145 · 163 · 164 · 180 · 181 |
| 15 | `shared/src/Component/TemplateEngine.php` | 73 · 74–75 (§1.9) |
| 16 | `shared/src/Api/Bff/DesktopBff.php` | 34–38 (§1.9) |
| 17 | `home.coremusic.net/.htaccess` | 5 · 6 · 7 (§1.9) |
| 18 | `.github/workflows/ci.yml` | 8 · 10–12 · 14–15 · 17–19 · 21–22 · 68 · 162 (§2.5) |
| 19 | `.github/workflows/secret-scan.yml` | 11–12 · 14–16 · 21–22 (§2.5) |

### §6.3 İlişkili vault dosyaları

| Dosya | Bağ |
|---|---|
| `kaynak-dagilimi-anayasa.md` | kural + §4.4 katman ayrımı |
| `kaynak-dagilimi-surucu.md` | mekanizma + §6.4 negatif kanıt |
| `zamanlama-scheduler-ornekler.md` | uygulama katmanı kuyruk örnekleri |
| `is-parcacigi-planlamasi-ornekler.md` | kardeş dürüstlük defteri |
| `isletim-sistemi-api` | rate-limit API sözleşmesi |

## §7 OS bazlı kaynak/kota örnekleri (her OS kendi altında)

> **Dürüstlük notu:** Bu depoda cgroup · RLIMIT · ioprio · NUMA çağrısı **YOK** (§5 D-1–D-4) —
> her örnek **OS seviyesi** referanstır. Tüm URL'ler **2026-10-14** `Invoke-WebRequest GET`
> ile **HTTP 200** ölçülmüştür.

### §7.1 Linux / POSIX (hedef: Raspberry Pi 5 · CI `ubuntu-latest`)

| # | Kaynak örneği (OS API'si — depoda YOK) | Resmi doküman | 2026-10-14 |
|---|---|---|---|
| K-L1 | cgroup v2 kota dosyaları: `cpu.max` (bütçe/period), `memory.max`, `io.max` — süreç kumesine CPU/bellek/IO sınırı | https://docs.kernel.org/admin-guide/cgroup-v2.html | HTTP 200 |
| K-L2 | `prlimit(pid, resource, new_limit, old_limit)` — `RLIMIT_*` (fopen sayısı, stack, CPU süresi, RSS) süreç sınırı | https://man7.org/linux/man-pages/man2/prlimit.2.html | HTTP 200 |
| K-L3 | `ionice -c class -n level` — IO öncelik sınıfı: BEST EFFORT (varsayılan) · IDLE · REALTIME | https://man7.org/linux/man-pages/man1/ionice.1.html | HTTP 200 |
| K-L4 | cpuset — CPU kumesi/NUMA düğümüne bağlama; `set_mempolicy` ile bellek yerleşimi politikası | https://man7.org/linux/man-pages/man7/cpuset.7.html | HTTP 200 |
| K-L5 | RT grup bütçesi: `sched_rt_runtime_us` / `sched_rt_period_us`; kullanılmayan bütçe `SCHED_OTHER`'a düşer; çok küçük değer sistem kararsızlığı (kernel uyarısı) | https://docs.kernel.org/scheduler/sched-rt-group.html | HTTP 200 |
| K-L6 | `set_mempolicy` — MPOL_BIND/MPOL_PREFER ile düğüm bazlı bellek atanımı | https://man7.org/linux/man-pages/man2/set_mempolicy.2.html | HTTP 200 |

```bash
# OS seviyesi — depoda karşılığı YOK (kaynak: K-L1, 2026-10-14 HTTP 200)
echo "20000 100000" > /sys/fs/cgroup/audio/cpu.max   # 20ms/100ms bütçe
echo 268435456    > /sys/fs/cgroup/audio/memory.max   # 256 MB sınırı
```

### §7.2 Windows (hedef: `win-sw` — `.ai/AGENTS.md §4` #11)

| # | Kaynak örneği (OS API'si — depoda YOK) | Resmi doküman | 2026-10-14 |
|---|---|---|---|
| K-W1 | `SetInformationJobObject(hJob, JobObjectCpuRateControlInformation, …)` — job object ile CPU kotası (süreç grubuna `%` sınırı); `JobObjectBasicLimitInformation` (2) temel limitler | https://learn.microsoft.com/en-us/windows/win32/api/jobapi2/nf-jobapi2-setinformationjobobject | HTTP 200 |
| K-W2 | `SetProcessWorkingSetSize` — fiziksel bellek (çalışma kümesi) min/max sınırı | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-setprocessworkingsetsize | HTTP 200 |
| K-W3 | `GetLogicalProcessorInformation` — NUMA/çekirdek topolojisi okunur, kota yerleşimi öncesi | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getlogicalprocessorinformation | HTTP 200 |
| K-W4 | `SetPriorityClass(hProcess, PROCESS_MODE_BACKGROUND_BEGIN)` — "arka plan işleme modu" kaynak kullanımını düşürür | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-setpriorityclass | HTTP 200 |

### §7.3 macOS / iOS (aday platform)

| # | Kaynak örneği (OS API'si — depoda YOK) | Resmi doküman | 2026-10-14 |
|---|---|---|---|
| K-A1 | `DispatchQoS` — QoS sınıfı "daha fazla kaynak" demektir: yüksek sınıf = daha hızlı + daha çok enerji; yanlış sınıf = enerji israfı | https://developer.apple.com/documentation/dispatch/dispatchqos | HTTP 200 |
| K-A2 | `Process.qualityOfService` — süreç bazlı QoS ataması (kaynak paylaşımda öncelik) | https://developer.apple.com/documentation/foundation/process/qualityofservice | HTTP 200 |

### §7.4 RTOS — Zephyr (gömülü hedef)

| # | Kaynak örneği (OS API'si — depoda YOK) | Resmi doküman | 2026-10-14 |
|---|---|---|---|
| K-R1 | Zephyr zaman birimleri: gerçek zaman ms/µs; çevrim sayacı `k_cycle_get_32()`; frekans `sys_clock_hw_cycles_per_sec()` — sabit ya da çalışma zamanında güncellenen (`CONFIG_SYSTEM_CLOCK_HW_CYCLES_PER_SEC_RUNTIME_UPDATE`) | https://docs.zephyrproject.org/latest/kernel/services/timing/clocks.html | HTTP 200 |
| K-R2 | "Sistematik zamanlayıcı frekansı" notu: per-CPU frekans ölçeklemesi ile uyumsuz — kota/ölçüm hesabında frekans sabit varsayılmaz | https://docs.zephyrproject.org/latest/kernel/services/timing/clocks.html | HTTP 200 |

### §7.5 OS matrisi (kota yeteneği → OS)

| Yetenek | Linux/POSIX | Windows | macOS/iOS | Zephyr RTOS |
|---|---|---|---|---|
| CPU kotası | K-L1 (cgroup) · K-L5 (RT bütçe) | K-W1 (job object) | K-A1 (QoS dolaylı) | yok (sabit bütçe) |
| Bellek sınırı | K-L1 (`memory.max`) · K-L2 (RLIMIT) | K-W2 (working set) | QoS dolaylı | statik statik havuz |
| IO önceliği | K-L3 (`ionice`) | job object IO sınıfı ⚠️ (bu turda doğrulanmadı) | — | disk kuyruğu ⚠️ |
| NUMA/topoloji | K-L4 · K-L6 | K-W3 | — | tek çekirdek varsayılan |
| Süreç önceliği | §4.2 nice/`setpriority` | K-W4 (background mod) | K-A2 | — |

> `⚠️ VERIFICATION REQUIRED` hücreler: bu turda URL doğrulaması yapılmayan yetenekler;
> uydurulmadı, sonraki tura açık bırakıldı.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
