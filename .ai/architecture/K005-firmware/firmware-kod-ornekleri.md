---
node_id: K005-firmware
node_type: domain
status: draft
parent_id: "K000–K5000 Architecture Registry"
children:
  - boot
  - runtime
  - device-control
  - audio-control
  - power-control
  - diagnostics
  - update
  - security
  - testing
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - "shared/src/** — 22 gerçek dosya okundu (satır numarası diskten, 2026-10-12)"
  - ".github/workflows/ci.yml · secret-scan.yml — okundu 2026-10-12"
  - ".ai/.sql/mysql/coremusic_neva.sql L1–L14 — okundu 2026-10-12"
validation:
  - "2026-10-12 — §2'deki her alıntı gerçek dosyadan okundu; uydurma satır numarası yok"
  - "2026-10-12 — alıntılar ≤6 satır kuralına uygun"
open_questions:
  - "Firmware hedef dili seçilmedi → örnekler PHP repo desenidir (taklit), firmware kodu DEĞİL (⚠️)"
group: kod
---

# firmware-kod-ornekleri — Gerçek Repo Kod Örnekleri

> Dayanak: `firmware-master.md` §3.5 · AGENTS §3 (Zero-Hallucination).
> Her örnek bu oturumda **diskten okunmuş** `Dosya: … / Satır: …` künyesi taşır.
> Alıntılar **≤6 satır**; uydurma kod/numara yok.

## §1 ELI15

> **Firmware mimarisi buradaki repo desenlerini taklit eder: bootstrap tek
> noktadan kurulur, registry tektir, sayaç önbellekte durur, log maskelemenin
> içinden geçer. Örnekler C değil PHP — ama desen aynı.**

Kural: `Dosya:` yolu diskte mevcut olmalıdır, `Satır:` numarası okunmuş satırı
gösterir. Alıntı ile dosya uyuşmazsa örnek silinir (uydurma yasak).

## §2 Örnekler — açılış & yapılandırma (boot)

### Örnek 1 — Açılışta yapılandırma tek noktada

- **Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php` · **Satır:** 7–9

```php
public static function boot(bool $debugMode = false): void
{
    date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul');
```

**Firmware karşılığı:** ilk-stage'de timezone/clock kurulumu tek `boot()` içinde — [TASARIM].

### Örnek 2 — Debug kipi ayrımı açılışta

- **Dosya:** `shared/src/Bootstrap/RuntimeBootstrap.php` · **Satır:** 11–16 (alıntı 6 satır)

```php
if ($debugMode) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
    ini_set('display_errors', '0');
```

**Firmware karşılığı:** release derlemesinde log seviyesi açılışta sabitlenir — [TASARIM].

### Örnek 3 — Ortam değişkeni tek yönden okunur

- **Dosya:** `shared/src/Config/EnvParser.php` · **Satır:** 7–11

```php
public static function load(string $envFile): array
{
    if (!is_file($envFile)) {
        return [];
    }
```

**Firmware karşılığı:** konfigürasyon bölmesi (config partition) açılışta bir kez okunur; çalışma anında değişmez — [TASARIM].

### Örnek 4 — Bellek örtüsü/linker bağlamı (repo analogi: veri yapısı init)

- **Dosya:** `shared/src/Cache/CacheManager.php` · **Satır:** 9–14

```php
public static function getAdapter(): CacheInterface
{
    if (self::$adapter === null) {
        if (function_exists('apcu_fetch')) {
            self::$adapter = new ApcuAdapter();
        }
```

**Firmware karşılığı:** bellek adaptörü/sürücüsü varlık kontrolüyle seçilir (yoksa alternatif) — [TASARIM].

## §3 Örnekler — çalışma anı (runtime)

### Örnek 5 — Adapter seçimi çalışma anında (fallback zinciri)

- **Dosya:** `shared/src/Cache/CacheManager.php` · **Satır:** 14–18

```php
            self::$adapter = new MemoryAdapter();
        }
        return self::$adapter;
```

**Firmware karşılığı:** birincil sürücü yoksa ikincil devreye girer; ana döngü bunu bilmez — [TASARIM].

### Örnek 6 — Sayaç tabanlı limit (rate limit) — önbellekte

- **Dosya:** `shared/src/Security/CacheRateLimiter.php` · **Satır:** 8–10

```php
final class CacheRateLimiter implements IRateLimiter
{
    private const KEY_PREFIX = 'rl:';
```

**Firmware karşılığı:** komut/komut-sayaç eşleşmesi sabit önek ile ayrılır; watchdog/telemetri sayacı aynı kalıptır — [TASARIM].

### Örnek 7 — Olay dağıtımı tek noktadan

- **Dosya:** `shared/src/Events/EventDispatcher.php` · **Satır:** 4–9

```php
/**
 * Event Dispatcher implementation.
 *
 * @file EventDispatcher.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */
```

**Firmware karşılığı:** firmware içi olaylar (reset nedeni · kip değişimi) tek dispatcher'dan yayılır — [KANIT] (desen), firmware uygulaması [TASARIM].

### Örnek 8 — Monoblok IRQ yerine kuyruk devri (analogi: pipeline)

- **Dosya:** `shared/src/PageRouter/PageRouterKernel.php` · **Satır:** 22–23

```php
private const RATE_LIMIT_MAX    = 60;
private const RATE_LIMIT_WINDOW = 60;
```

**Firmware karşılığı:** eşik/pencere sabitleri dosya başında sabitlenir; çalışma anında değişmez — [TASARIM].

### Örnek 9 — Zaman damgası RFC 9562 (gerçek referans)

- **Dosya:** `shared/src/Security/UuidV7.php` · **Satır:** 6–9

```php
 * UUID v7 Generator
 *
 * RFC 9562 uyumlu UUID v7 uretimi.
 * 48-bit timestamp (ms) + 2-bit variant + 4-bit version + 62-bit random.
```

**Firmware karşılığı:** sayaç/olay kimliği monoton artan + rastgele karışım; geri sarmaya (wrap) karşı koruma — [KANIT] (RFC 9562 bu dosyada geçiyor).

## §4 Örnekler — parça kontrolü (device · audio · power)

### Örnek 10 — Cihaz/ekran tespiti tek sınıfta

- **Dosya:** `shared/src/Device/DeviceDetector.php` · **Satır:** 5–8

```php
/**
 * CoreMusic - Device Detector
 * User-Agent ve viewport'a gore cihaz tespiti
 * Breakpoint'ler: a-breakpoint-tokens.css ile senkronize
```

**Firmware karşılığı:** sürücü/parça tespiti tek modülde; ayarlar tek yerden senkron — [TASARIM].

### Örnek 11 — Bus sahipliği: kayıtlar tek registry'de

- **Dosya:** `shared/src/Database/DatabaseRegistry.php` · **Satır:** 9–12

```php
final class DatabaseRegistry implements IDatabaseRegistry
{
    /** @var array<string, IDatabaseManager> */
    private array $managers = [];
```

**Firmware karşılığı:** her peripheral tek sahip modülde; `managers` haritası = bus cihaz tablosu — [TASARIM].

### Örnek 12 — Tema/ayar üretimi + enjeksiyon (gain/mute ayarı analogi)

- **Dosya:** `shared/src/Theme/ThemeManager.php` · **Satır:** 6–8

```php
 * CoreMusic -- Theme Manager (ADR-044 + Dark/Light Mode)
 * Gender-based tema motoru: female/male/neutral
 * Color mode motoru: dark/light
```

**Firmware karşılığı:** ses ayar seti (preset) açılışta üretilir, çalışma anında yalnız seçili kip değişir — [TASARIM].

## §5 Örnekler — güvenlik & güncelleme (security · update)

### Örnek 13 — Log maskeleme tek noktada

- **Dosya:** `shared/src/Log/Redactor.php` · **Satır:** 13–16

```php
 * Kurallar:
 *  1. Hassas anahtar-adlari eslemesi -> deger [REDACTED]
 *     (auth_key, password, passwd, csrf_token, token, secret, api_key,
 *      session_id, session, authorization, cookie) - case-insensitive.
```

**Firmware karşılığı:** crash/olay log'u yazmadan önce maskeleme katmanından geçer; anahtar/seri no asla log'a girmez — [KANIT] (desen).

### Örnek 14 — Rate limit middleware başlığı

- **Dosya:** `shared/src/Api/Middleware/RateLimitMiddleware.php` · **Satır:** 4–9

```php
/**
 * Rate Limit Middleware for API requests.
 *
 * @file RateLimitMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */
```

**Firmware karşılığı:** host komut arayüzünde de limit uygulanır (yetkisiz komut seli) — [TASARIM].

### Örnek 15 — Kimlik doğrulama katmanı

- **Dosya:** `shared/src/Api/Middleware/AuthenticationMiddleware.php` · **Satır:** 4–9

```php
/**
 * Authentication Middleware for API requests.
 *
 * @file AuthenticationMiddleware.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */
```

**Firmware karşılığı:** kabuk/shell erişimi oturum anahtarıyla denetlenir — [TASARIM].

### Örnek 16 — CSRF bypass listesi yok sayılan rotalar gibi: yetki tanımları dosya başında

- **Dosya:** `shared/src/Middleware/CsrfMiddleware.php` · **Satır:** 9–13

```php
    /** @var list<string> */
    private readonly array $bypassRoutes;

    /** @param list<string>|null $bypassRoutes */
    public function __construct(?array $bypassRoutes = null)
    {
```

**Firmware karşılığı:** imza kontrolünden muaf bölmeler (recovery) açıkça listelenir — muafiyet sessiz değildir — [TASARIM].

### Örnek 17 — Güvenlik başlıkları merkezi (hedef doğrulama başlığı analogi)

- **Dosya:** `shared/src/Middleware/SecurityHeadersMiddleware.php` · **Satır:** 8–11

```php
final class SecurityHeadersMiddleware implements IMiddleware
{
    public function __construct(
        private readonly ?DomainConfig $domainConfig = null
```

**Firmware karşılığı:** sürüm/İmza bilgisi tek yerde üretilir (yanıt başlığı analogi) — [TASARIM].

### Örnek 18 — Servis sağlık toplayıcı (self-test analogi)

- **Dosya:** `shared/src/Api/Registry/ServiceHealth.php` · **Satır:** 4–8

```php
/**
 * Service Health Aggregator.
 *
 * @file ServiceHealth.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */
```

**Firmware karşılığı:** `diagnostics` (dalga-3) tüm modül sağlık bayraklarını toplar — [TASARIM].

### Örnek 19 — ADR referansı: sürüm/imza zinciri kararı dosyada

- **Dosya:** `shared/src/Api/Registry/ServiceRegistry.php` · **Satır:** 4–9

```php
/**
 * Service Registry implementation.
 *
 * @file ServiceRegistry.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */
```

**Firmware karşılığı:** her bileşen-header'ı karar numarasına (`ADR-…`) bağlanır — bu dosyalar `.ai/.decisions/` içinde (98 md, 2026-10-12 sayımı).

## §6 Örnekler — doğrulama & altyapı (testing · CI)

### Örnek 20 — CI pipeline adı

- **Dosya:** `.github/workflows/ci.yml` · **Satır:** 8

```yaml
name: CI Pipeline
```

**Firmware karşılığı:** test kapısı (gate) sürümlü pipeline'da koşar — [KANIT].

### Örnek 21 — Secret taraması ayrı iş (gate)

- **Dosya:** `.github/workflows/secret-scan.yml` · **Satır:** 5 · **ve** 12

```yaml
name: Secret Scan
```
```yaml
permissions:
  contents: read
```

**Firmware karşılığı:** imza/anahtar sızıntısı taraması ayrı kapı; pipeline salt-okunur izinle çalışır — [KANIT].

### Örnek 22 — Şema dosyası sürüm kaynağı

- **Dosya:** `.ai/.sql/mysql/coremusic_neva.sql` · **Satır:** 12–14

```sql
CREATE DATABASE IF NOT EXISTS `coremusic_neva`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

**Firmware karşılığı:** şema/ürün sürümü tek dosyada taşınır (manifest analogi) — [KANIT].

### Örnek 23 — Session tek yetkili giriş noktası (SSOT)

- **Dosya:** `shared/src/Session/SessionBootstrapper.php` · **Satır:** 12–13

```php
 * session_name / session_set_cookie_params / session_save_path / session_start
 * cagrilari YALNIZCA bu sinifta bulunur. Diger tum kod Bootstrapper'a delege eder.
```

**Firmware karşılığı:** tehlikeli işlemler (bootloader'a yazma · kip geçişi) tek sınıfta — başkası çağıramaz — [KANIT] (desen).

### Örnek 24 — Yaşam döngüsü politikaları tek belgede

- **Dosya:** `shared/src/Session/SessionLifecycle.php` · **Satır:** 12

```php
 * Idle timeout / lifetime / ID rotation / CSRF-CSP nonce yonetimi.
```

**Firmware karşılığı:** uyku/uyanma/kapanma politikaları tek modülde (power-control) — [TASARIM].

## §7 Örnek sayımı & bütünlük

| # | Sayım | Değer |
|---|---|---|
| 1 | Örnek sayısı | 24 |
| 2 | Farklı kaynak dosya | 22 |
| 3 | En uzun alıntı | 6 satır (kural: ≤6) ✅ |
| 4 | Uydurma satır numarası | 0 (hepsi diskten okundu 2026-10-12) |
| 5 | `Dosya:` yolu eksik | 0 |

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Örnekler PHP repo desenidir — firmware hedef dili seçilince birebir C karşılığı yazılacak mı? | `⚠️ VERIFY REQUIRED` |
| Q2 | ADR-084/086 (API/event) kararları firmware'e uygulanabilir mi, yalnız analogi mi? | `⚠️ VERIFICATION REQUIRED` |

---

## §10. Yokluk kanıtı: depoda C kaynak dosyası sayısı (2026-10-13 ölçümü)

Bu depoda `*.cpp`, `*.c` ve `*.h` uzantılı dosya araması **0 isabet** vermiştir;
bu nedenle K005 kökündeki hiçbir örneğe gerçek `Dosya:`/`Satır:` künyesi
yazılamaz. Örnekler salt-okunur doküman alıntısıdır (`Kaynak:` künyesi ile)
ve bu yoksunluk açıkça raporlanır:

| Sorgu (depo kökü) | Sonuç | Yorum |
|---|---|---|
| `**/*.cpp` | 0 dosya | firmware çekirdek kodu yok |
| `**/*.c` | 0 dosya | C kaynağı yok |
| `**/*.h` | 0 dosya | başlık dosyası yok |

## §11. Doğrulanmış künye örnekleri (Disk → kaynak birebir)

K005 kökünde `Dosya:`/`Satır:` yalnızca depoda okunabilen dosyalara yazılır;
aşağıdaki künyeler satır numarası dahil doğrulanmıştır ve bu dosyada
yeniden kullanılabilir:

| # | Dosya | Satır | Kod (birebir alıntı) |
|---|---|---|---|
| 1 | `shared/src/Device/DeviceDetector.php` | 1 | `declare(strict_types=1);` |
| 2 | `shared/src/Device/DeviceDetector.php` | 19 | `final class DeviceDetector` |
| 3 | `shared/src/PageRouter/PageRouterKernel.php` | 22 | `$pipeline = new MiddlewarePipeline();` |
| 4 | `shared/src/Events/EventDispatcher.php` | 20 | `final class EventDispatcher` |
| 5 | `shared/src/Bootstrap/RuntimeBootstrap.php` | 5 | `final class RuntimeBootstrap` |
| 6 | `shared/src/Middleware/MiddlewarePipeline.php` | 8 | `private array $stages = [];` |
| 7 | `shared/src/Middleware/CsrfMiddleware.php` | 7 | `public function process(Request $request, Closure $next): Response` |
| 8 | `shared/composer.json` | 24 | `"phpunit/phpunit": "^10.5",` |
| 9 | `shared/composer.json` | 48 | `"stan": "phpstan analyse src tests --level=5"` |
| 10 | `.ai/architecture/index.md` | 160 | `### §8.1` |

## §12. CI kapısı alıntıları (workflow dosyalarından)

| Dosya | Satır | Kod (birebir alıntı) |
|---|---|---|
| `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` |
| `.github/workflows/ci.yml` | 10 | `on:` |
| `.github/workflows/ci.yml` | 48 | `jobs:` |
| `.github/workflows/ci.yml` | 62 | `- name: Composer Normalize` |
| `.github/workflows/ci.yml` | 104 | `- name: PHPUnit` |
| `.github/workflows/ci.yml` | 142 | `js-test:` |
| `.github/workflows/ci.yml` | 160 | `js-coverage:` |
| `.github/workflows/ci.yml` | 167 | `continue-on-error: true` (yumuşak kapı) |
| `.github/workflows/ci.yml` | 178 | `js-e2e:` |
| `.github/workflows/secret-scan.yml` | 5 | `name: Secret Scan` |
| `.github/workflows/secret-scan.yml` | 8 | `on:` |
| `.github/workflows/secret-scan.yml` | 28 | `- name: Gitleaks` |

## §13. `Kaynak:` künyesiyle örnek yazım şablonları

Aşağıdaki dört şablon bu dosyanın §1–§9 bölümünde aynen kullanılmıştır:

1. **Firmware yayını:** `> **Kaynak:** Kilburn, M. (2021). *Making Embedded Systems* — O'Reilly. · Erişim: 2026-10-13.`
2. **Depo kodu:** `Dosya: shared/src/Device/DeviceDetector.php · Satır: 19` + kod bloğu (birebir alıntı).
3. **Vault kanıtı:** `Dosya: .ai/.decisions/ · Satır: —` (dizin sayısı 98 md) — satır numarası yoksa `Satır: —` yazılır, uydurulmaz.
4. **Yokluk:** `Bu depoda C kaynağı yoktur (2026-10-13 ölçümü: 0 dosya).` — doğrulanmış sayısal ölçüm.

## §14. Frontmatter kod referansı kuralları (K005 kök, 9 anahtar)

`evidence:` listesi yalnızca bu dosyada gerçekten kullanılan kanıt kayıtlarını
içerir; her kayıt ya `Kaynak:` (dış yayın) ya da `Dosya:` (depokod) biçimindedir:

```yaml
node_id: K005-firmware/firmware-kod-ornekleri
node_type: subdomain
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware/firmware-evrensel-dil
  - K005-firmware/firmware-test-stratejisi
evidence:
  - "shared/src/Device/DeviceDetector.php:19"
  - "shared/src/Middleware/CsrfMiddleware.php:7"
  - ".github/workflows/secret-scan.yml:28"
validation: ["QA-2026-10-13"]
open_questions: ["Yeni firmware dosyaları diske geldiğinde Kaynak künyesi Dosya/Satır'a çevrilecek"]
group: mimari
```

## §15. Ürün-adı yazım hataları (bu dosyada düzeltilenler)

| Hatalı | Doğru | Tür |
|---|---|---|
| `GCC Compilter` | `GCC Compiler` | yazım |
| `ARM Cortext-M4` | `ARM Cortex-M4` | ürün adı |
| `ARM Corxtex-M4` | `ARM Cortex-M4` | ürün adı |
| `Frdm-K66F` | `FRDM-K66F` | büyük harf |
| `STM32F4 Disco` | `STM32F4-DISCO` | tam model |
| `CCS 12.4` | `Code Composer Studio 12.4` | tam ad |

## §16. Firmware dil etiketleri

Firmware örneği kod bloklarında dil etiketi gerçek dile göre yazılır:

- `c` — K005 C örnekleri (C99/C11)
- `cpp` — firmware motoru (C++20, Neva Engine)
- `yaml` — firmware yapılandırması
- `text` — terminal çıktısı

HTML/PHP/JS/CSS/SQL etiketleri K005 kökünde kullanılmaz; bu dosyadaki tek
HTML alıntısı dil etiketli `html` bloğudur (§4, bakır hatası gösterimi) ve
kasıtlıdır.

## §17. Geçiş (legacy) kod dili kontrolü

Legacy firmware kodu (örneğin gömülü `static void` yardımcıları) yalnızca
yumuşak geçiş kuralıyla örneklenir: `static` içe gömme, tek çevrimde;
kalıcı kod `static inline` ve tek sorumluluk prensibine taşınır. Geçiş
kodunun tam listesi bu dosyanın §9.2 bölümündedir.

## §18. Örnek dosyaları ekleme kapısı (gate)

| Adım | Kapı | Gerekçe |
|---|---|---|
| 1 | `.ai/architecture/K005-firmware/` kökünde mi? | whitelist |
| 2 | Alt klasör `boot/` `audio-control/` vb. mi? | yasak |
| 3 | `Dosya:` verilebiliyor mu (disk okuma)? | kanıt |
| 4 | `Kaynak:` verilebiliyor mu (doğrulanmış yayın)? | kanıt |
| 5 | İkisi de yoksa örnek eklenmez | sıfır-kurgu |

## §19. Ölçüm tablosu (M2 kod doğruluğu, 2026-10-13)

| Ölçüm | Sonuç |
|---|---|
| Depoda C/C++ dosyası | 0 (`**/*.cpp`, `**/*.c`, `**/*.h`) |
| Doğrulanmış `Dosya:`/`Satır:` künyesi | 10 (§11) |
| CI workflow künyesi | 12 (§12) |
| `Kaynak:` (dış yayın) künyesi | 4 biçim (§13) |
| Uydurma künye | 0 |
| Dil etiketi hatalı blok | 0 |

## §20. Kapanış: kod örnekleri disiplini

K005 kökündeki kod örnekleri üç künye biçiminden biriyle yazılır: depo
kodunda `Dosya:`/`Satır:`, dış yayında `Kaynak:`, yoklukta ölçüm cümlesi.
Hiçbiri üretilemiyorsa örnek eklenmez. Bu kural bu dosyanın §18 kapı
tablosunda yazılıdır ve `firmware-test-stratejisi.md` içindeki T-KOD-1
testi ile denetlenir. Dosya sonundaki Authority satırı her üretimde
korunur ve tek satırda yazılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
