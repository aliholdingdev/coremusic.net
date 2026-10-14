---
node_id: K000-isletim-sistemi/electroncis/electroncis-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/electroncis
children: []
depends_on: [K002-electroncis, K001-donanım, K000-isletim-sistemi/isletim-sistemi-audio, K000-isletim-sistemi/os-donanim-tespiti]
evidence:
  - "C:\www\coremusic.net\shared\src\Api\Bff\DesktopBff.php:35"
  - "C:\www\coremusic.net\shared\src\Cache\ApcuAdapter.php:9,13"
  - "C:\www\coremusic.net\shared\src\Security\UuidV7.php:22-23"
  - "https://docs.kernel.org/sound/index.html (200 OK · 2026-10-14)"
  - "https://docs.kernel.org/firmware-guide/acpi/index.html (200 OK · 2026-10-14)"
validation: "2026-10-14 · yazıldı (Vault Steward Q1 kararı) · repo: C/C++ = 0 glob → örnekler şartname/pseudocode · PHP satırları satır-kanıtlı · 2026-10-14 §8 gerçek PHP kod örnekleri eklendi (6 blok · repo karşılığı YOK dürüstlük kaydıyla)"
open_questions:
  - "⚠️ VERIFICATION REQUIRED: pseudocode blokları gerçek kernel kaynağından tırnaklanmadı — niyet {OS davranış şartnamesi} olarak okunmalı, kaynak kod iddiası taşımaz."
  - "repo'ya C/C++ girerse bu dosya gerçek kod bloklarına güncellenir (grep-0 bozulduğu an)."
group: kod
---

# Örnekler — K000 · electroncis (OS-elektronik arayüzü)

> **`electroncis` yazım hatası dokunulmaz** (v2 §3 KURAL 1).

## §0 ELI15

Bu dosya, OS'in elektronikle yüzleştiği noktaları **çalışma şekli örnekleriyle**
 gösterir. İki disiplin uygulanır: (1) **repo gerçeği** — CoreMusic kodundaki gerçek PHP
 satırları, satır numarasıyla referans; (2) **şartname/pseudocode** — repo'da C/C++
 bulunmadığı için gerçek OS kaynak kodu **uydurulmaz**; bunun yerine "şu arayüz şu sırayla
 kullanılır" düzeyinde davranış şartnameleri yazılır. Her pseudocode bloğu kasıtlı olarak
 `{niyet}` başlığı taşır ve kaynak kod iddiası **taşımaz**.

## §1 Dürüstlük tablosu

| Kaynak | Durum | Buna göre ne yazıldı |
|---|---|---|
| Repo'da C/C++ dosyası | **0 glob** (2026-10-14) | Gerçek driver kodu YOK → sahte C bloğu yazılmaz |
| Repo'da PHP | var (satır kanıtlı §3) | Gerçek satırlar referans olarak |
| Kernel/OS davranışı | web kanıtı (§5.1 200'ler) | Pseudocode şartname olarak, `{niyet}` etiketli |
| Windows WDK detayı | ⚠️ doğrulanmadı | IdDia olarak değil, açık soru olarak |

## §2 OS hizmeti → arayüz eşleme

| OS hizmeti | Elektronik arayüz | Alt dal |
|---|---|---|
| Ses yığını (ALSA/PulseAudio/CoreAudio) | I2S/TDM + codec register (I2C) + UAC | donanim-arayuz-protokolleri |
| Depolama/AGI | SPI + PCI/USB enumerasyon | donanim-arayuz-protokolleri |
| Ağ | PCIe/USB NIC + PHY (MDIO) | donanim-arayuz-protokolleri |
| Giriş (tuş/sensör) | GPIO hotplug/haberleşme | donanim-arayuz-protokolleri |
| Uyku/açılma (suspend/wake) | ACPI S-state ↔ PS_ON/PWR_OK | os-guc-elektronigi |
| Fan/sıcaklık | HWMON/EC register → thermal zone | os-guc-elektronigi |
| Kritik koruma | trip point → throttle → shutdown | os-guc-elektronigi |
| Pil/şarj | EC/ACPI battery object | os-guc-elektronigi |

## §3 Repo örnekleri — GERÇEK PHP (satır kanıtlı, 2026-10-14)

> Bunlar `electroncis-dogrulama.md` §4.2 ile aynı ölçümdür; burada **kullanım bağlamı**
> ile tekrarlanır (tam metin için dosya:satır referansı esastır).

| # | Referans | Bağlam | OS-arayüz dersi |
|---|---|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php:35` → `'platform' => php_uname('s'),` | BFF platform metası | OS kimliği uygulama katmanına sızar |
| 2 | `shared/src/AI/AIEngine.php:291,295` → ffprobe · `shell_exec($cmd)` | medya analiz süreci | PHP, OS sürecini doğrudan çağırır (komut-sınırı dikkat) |
| 3 | `shared/src/Cache/ApcuAdapter.php:9,13` → `function_exists('apcu_fetch')` · `apcu_fetch($key,$success)` | bellek katmanı | PHP eklenti (OS kaynağı) defansif kontrol ile |
| 4 | `shared/src/Session/SessionConfig.php:33,35` → `ini_get`/`sys_get_temp_dir` · `@mkdir($savePath,0750,true)` | oturum dizini | dizin izni (0750) = işletim sistemi politikası |
| 5 | `shared/src/Security/UuidV7.php:22-23` → `pack('N',...)` / `pack('n',...)` | UUIDv7 | bayt düzeyi işlem — I2C/SPI register paketleme mantığının PHP ayağı |
| 6 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185,189,199` → `is_link` · `realpath` · `DIRECTORY_SEPARATOR` | yayın dosyası çözümleme | yol ayracı/symlink güvenliği = FS-arayüz |
| 7 | `shared/src/Api/Middleware/RateLimitMiddleware.php:40` → `header('Retry-After: ...')` | rate limit | zamanlama politikası — OS sayaç/timer muadili |

## §4 Şartname örnekleri — donanim-arayuz-protokolleri

> **{niyet}** = davranış şartnamesi · kaynak kod **değildir** · gerçek ID'ler/registrer
> adresleri bu bloklarda **uydurulmaz** (biliniyorsa vault-atıfla yazılır).

### Örnek A — I2C codec register okuma (OS sürücü niyeti)

```text
{niyet} I2C register okuma sırası (OS sürücü gözünden)
1. bus_open(adres)                 → araç: i2c-dev / I2C controller driver
2. write(reg_addr_bytes)           → register adresi, protokole göre 1 veya 2 bayt
3. repeated-start + read(n)        → okuma, ACK/NACK durumları protokolde tanımlı
4. bus_close() / runtime-put       → yoksa bus aktif kalır (güç israfı)
Hata yolu: -ENXIO (cihaz yok) → hotplug bekleme listesi; -ETIMEDOUT → retry sınırı
```

### Örnek B — I2S/TDM akış başlatma (OS ses yığını niyeti)

```text
{niyet} Akış başlatma sırası
1. clock ağacı: MCLK/BCLK/FS oranı hesapla (Örn. 48 kHz · 2 kanal · 32 bit slot)
2. DMA kanalı tahsis et + buffer'ı başlatma ÖNCESİ ayır (real-time: tahsis yasak)
3. codec register (I2C): format, slot genişliği, master/slave, mute aç
4. veri yolu AÇ: DMA → I2S TX → DAI → codec → amplifikatör
Kapanış: mute → veri yolu dur → clock serbest (yalnız kullanılmıyorsa)
```

### Örnek C — USB UAC cihaz enumerasyonu (OS USB yığını niyeti)

```text
{niyet} Ses cihazı takma sırası
1. attach sinyali (VBUS + d+/d- ) → hub port durumu değişir
2. reset → adres atama → descriptor okuma (device / config / interface)
3. sınıf tanımı ses (UAC) → OS ses birimi keşfeder
4. alternatif 0 = yok say; gerçek akış için alternatif set seçilir
5. endpoint eşleştirme → buffer ve frame hattı kurulur
Ayırma: detach → önce akış dur → kaynaklar bırak → hotplug listesine not
```

### Örnek D — Hotplug/mechanical detect (GPIO niyeti)

```text
{niyet} GPIO hotplug
1. pin'i giriş + pull yapılandır (dokümantasyona uygun pull)
2. kenar (edge) kesmesi kaydet: takma/çıkarma olayı
3. olay → cihaz keşif akışı (§Örnek C benzeri)
4. sekmelenme (bounce) için donanım/OS gecikmesi — doğrudan olay yığmaz
Sınırlama: hotplug PİNi mekanik/haberleşme olayıdır; OS-E S0↔S5 GÜÇ durumu DEĞİLDİR
(vault içinde karıştırılmıştı — kapsam.md §G kaydı).
```

## §5 Şartname örnekleri — os-guc-elektronigi

### Örnek E — S3 uyanışa hazırlanma (ACPI/OS niyeti)

```text
{niyet} S3 (suspend-to-RAM) öncesi sıralama
1. kullanıcı/servis kararı → PM senaryosu
2. dosya sistemi boşalt · ağ kapat · sürücülere suspend bildir
3. wake kaynağıyı arm et: (OS) wakeup bit'leri — wake-on-LAN/USB/klavye
4. GPE/SCI ayarı → firmware'e geç · RAM self-refresh'e geç
Uyanış: wake kaynağı → firmware/OS ters sırayla · aygıtları resume et → akış devam
```

### Örnek F — PS_ON/PWR_OK el sıkışması (seviye mantığı — sayı yok)

```text
{niyet} Besleme durumu (mantık seviyeleri; voltaj eşikleri model/standart işidir)
PS_ON# asserted (düşük)  → ana besleme AÇIK
PWR_OK high              → çıkışlar stabil → anakart sıralamayı sürdürür
PWR_OK low (güç iyi değil) → OS hata/yeniden başlat senaryosu
Not: bu mantık seviyesi diyagramı tırnaklanmıştır; ölçü voltaj/eşik bu dosyada YAZILMAZ
(K002-electroncis / donanım dokümanı işidir — uydurma eşik yasak).
```

### Örnek G — Termal zincir (hwmon/ACPI niyeti)

```text
{niyet} Sıcaklık yönetim zinciri
1. sensör örneği → OS hwmon/ACPI thermal-zone nesnesi
2. sıcaklık > normal eşiği → aktif soğutma (fan) kuralı tetiklenir
3. sıcaklık > trip point → OS kısıtlama (throttle) — frekans/akım düşürme
4. kritik trip → güvenli kapanış (firmware/OS ortak kararı)
Bu dosyada sayısal eşik YOK: cihaza/standart aittir, uydurulursa zararlıdır.
```

## §6 Sınır örnekleri (bu dalın YAZMADIĞI)

| Örnek türü | Nerede | Bu dal |
|---|---|---|
| Amplifikatör/Class AB hesabı, PCB düzeltmesi | `K002-electroncis` | yalnız atıf |
| ACPI tablosu yazma / SSDT | firmware katmanı (K001/K002) | yalnız OS **okuması** |
| RTOS görev/queue kodu | `.ai/electronic/firmware/rtos.md` | atıf (541 satır) |
| PHP iş mantığı testi | repo testleri | bu vault node'u değil |

## §7 Açık sorular

1. Pseudocode'lar gerçek kernel tırnaklarına mı çevrilsin? (kaynak: docs.kernel.org §5.1
   200'ler — emek, `⚠️ VERIFICATION REQUIRED` taşımaya devam eder)
2. Repo'ya C/C++ girerse ilk güncellenecek dosya hangisi? (öneri: bu dosya + §1 tablosu)
3. Gerçek cihaz register adresleri vault'a girerse (batı@CoreMusic envanteri) hangi
   `depends_on` node'u sahiplenir? (`os-donanim-tespiti` mi, K002 mi — §8 madde 4)

## §8 Gerçek PHP kod örnekleri (OS-arayüzü · 2026-10-14 yazıldı)

> **Dürüstlük kaydı:** Bu bloklar **bu vault için yeni yazılmıştır** — repo'da karşılıkları
> YOKTUR, kanıt olarak kullanılmaz (§3'teki satır-kanıtlı örnekler kanıttır). Amaç: CoreMusic
> PHP katmanının OS↔donanım arayüzünü **nasıl görebileceğinin** çalışır düzeyde şartnamesi.
> Standartlar: `strict_types` · tipli imzalar · `escapeshellarg` zorunlu · hata yutma yok
> (defensive return) · uydurma API/register **yok** — yalnız gerçek PHP fonksiyonları ve
> gerçek OS arayüz yolları (Linux sysfs/procfs · Windows WMI/CIM).

### §8.1 Platform tespiti — arayüz seçimi tek noktadan

```php
<?php
declare(strict_types=1);

/**
 * OS↔donanım arayüzü platform anahtarı — tek nokta (§F F1: turu ≠ kernel).
 * Vault örneği — repo karşılığı YOK.
 */
final class OsBridge
{
    public const LINUX  = 'linux';
    public const WINDOWS = 'windows';
    public const MACOS  = 'macos';

    public static function family(): string
    {
        return match (true) {
            str_starts_with(PHP_OS_FAMILY, 'Linux')   => self::LINUX,
            PHP_OS_FAMILY === 'Windows'               => self::WINDOWS,
            PHP_OS_FAMILY === 'Darwin'                => self::MACOS,
            default                                   => 'unknown', // asla uydurulmaz
        };
    }
}
```

### §8.2 Termal okuma — Linux sysfs / Windows WMI (çift yol)

```php
<?php
declare(strict_types=1);

/**
 * Sıcaklık okuma: Linux sysfs (m°C) veya Windows WMI (1/10 kelvin).
 * Sensör yoksa/erişim yoksa null döner — DEĞER UYDURULMAZ.
 * Vault örneği — repo karşılığı YOK.
 */
final class ThermalProbe
{
    private const LINUX_ZONE = '/sys/class/thermal/thermal_zone0/temp';

    /** @return int|null milidegC (örn. 45210 = 45.21 °C) */
    public static function readLinuxMilliCelsius(string $zone = self::LINUX_ZONE): ?int
    {
        $raw = @file_get_contents($zone); // sysfs: tek satır, m°C
        if ($raw === false) {
            return null; // thermal zone yok / yetki yok → UNKNOWN
        }
        $v = trim($raw);
        return ctype_digit($v) ? (int) $v : null;
    }

    /** @return float|null °C — WMI MSAcpi_ThermalZoneTemperature (root yetkisi gerekir) */
    public static function readWindowsCelsius(): ?float
    {
        $ps = 'Get-CimInstance -ClassName MSAcpi_ThermalZoneTemperature'
            . ' -ErrorAction Stop | Select-Object -First 1 -ExpandProperty CurrentTemperature';
        $out = self::runPowerShell($ps);
        if ($out === null || !is_numeric($out)) {
            return null; // yetki yok / ACPI desteklenmiyor
        }
        return ((int) $out) / 10.0 - 273.15; // WMI 1/10 K → °C
    }

    private static function runPowerShell(string $script): ?string
    {
        $cmd = ['powershell', '-NoProfile', '-NonInteractive', '-Command', $script];
        $spec = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if (!is_resource($spec)) {
            return null;
        }
        $out = trim((string) stream_get_contents($pipes[1]));
        fclose($pipes[1]);
        fclose($pipes[2]);
        return proc_close($spec) === 0 && $out !== '' ? $out : null;
    }
}
```

### §8.3 Güç/pil durumu — Linux sysfs power_supply

```php
<?php
declare(strict_types=1);

/**
 * AC bağlı mı, pil yüzdesi kaç? — Linux power_supply sınıfı.
 * Windows karşılığı (Win32_Battery CIM) bu örneğe eklenmedi → UNKNOWN/PLANNED.
 * Vault örneği — repo karşılığı YOK.
 */
final class PowerStatus
{
    public static function isOnAcPower(): ?bool
    {
        $raw = @file_get_contents('/sys/class/power_supply/AC/online');
        if ($raw === false) {
            return null; // AC nesnesi yok (masaüstü/batı farklı adlandırabilir)
        }
        return trim($raw) === '1';
    }

    /** @return int|null yüzde 0–100 */
    public static function batteryPercent(): ?int
    {
        $raw = @file_get_contents('/sys/class/power_supply/BAT0/capacity');
        if ($raw === false) {
            return null; // pil yok
        }
        $v = trim($raw);
        return ctype_digit($v) ? min(100, max(0, (int) $v)) : null;
    }
}
```

### §8.4 Ses cihazı enumerasyonu — ALSA /proc/asound

```php
<?php
declare(strict_types=1);

/**
 * Takılı ses kartları — Linux /proc/asound/cards (ALSA'nın sunduğu OS görünümü).
 * Çıktı biçimi çekirdek ABI'sidir (card id · isim satırları) — ayrıştırma defansif.
 * Vault örneği — repo karşılığı YOK.
 */
final class AsoundCards
{
    /** @return list<array{id:int, name:string}> */
    public static function list(): array
    {
        $raw = @file_get_contents('/proc/asound/cards');
        if ($raw === false) {
            return []; // ALSA yok / ses aygıtı yok
        }
        $cards = [];
        foreach (explode("\n", $raw) as $line) {
            // tipik satır: " 0 [PCH            ]: HDA-Intel - HDA Intel PCH"
            if (preg_match('/^\s*(\d+)\s+\[([^\]]+)\]:\s*(.+)$/', $line, $m) === 1) {
                $cards[] = ['id' => (int) $m[1], 'name' => trim($m[3])];
            }
        }
        return $cards;
    }
}
```

### §8.5 Cihaz erişim kilidi — flock (ASIO Exclusive muadili)

```php
<?php
declare(strict_types=1);

/**
 * Tek-uygulama cihaz kilidi — L0 kuralı 9 / ADR-017 ruhu: aynı aygıta
 * eşzamanlı ikinci erişim reddedilir. PHP'de karşılığı dosya kilidi.
 * Vault örneği — repo karşılığı YOK.
 */
final class DeviceLock
{
    /** @return resource|null null = cihaz zaten meşgul */
    public static function acquire(string $lockFile)
    {
        $fh = fopen($lockFile, 'c'); // yarat yoksa, ekleme yapma
        if ($fh === false) {
            return null;
        }
        if (!flock($fh, LOCK_EX | LOCK_NB)) { // LOCK_NB: asla bloklanma
            fclose($fh);
            return null; // ikinci uygulama → reddet
        }
        return $fh; // çağırıcı fclose/flock(UN) ile serbest bırakır
    }
}
```

### §8.6 Güvenli dış süreç — escapeshellarg (AIEngine dersinin şartname hali)

```php
<?php
declare(strict_types=1);

/**
 * Dış araç çağrısı: girdi kullanıcıdan geliyorsa kaçış ZORUNLU.
 * Repo dersi: AIEngine.php:291,295 (shell_exec) — burada savunma hali.
 * Vault örneği — repo karşılığı YOK.
 */
final class ExternalProbe
{
    /** @return string|null null = araç yok / başarısız (hata yutulmaz, log'a düşer) */
    public static function probe(string $binary, string $mediaPath): ?string
    {
        if (!preg_match('#^[A-Za-z0-9._-]+$#', $binary)) {
            return null; // binary adı whitelist dışı → çağrılma
        }
        $cmd = escapeshellarg($binary) . ' -version 2>&1';
        $out = shell_exec($cmd);
        if ($out === null) {
            error_log(sprintf('ExternalProbe failed: binary=%s', $binary)); // log-only
        }
        return $out;
    }
}
```

**§8 kapanış kuralı:** Bu altı blok **şartnamedir, kanıt değildir** — repo'ya giren ilk gerçek
OS-arayüzü PHP kodu bu dosyayı günceller (§7 madde 2 ile aynı kapı). Uydurma fonksiyon,
uydurma sysfs yolu, uydurma WMI sınıfı **kullanılmamıştır**; her çağrı PHP 8.4 standart
fonksiyon/projesi veya belgelenmiş Linux/Windows arayüzüdür.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
