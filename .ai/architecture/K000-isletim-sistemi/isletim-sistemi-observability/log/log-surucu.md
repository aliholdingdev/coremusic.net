---
id: isletim-sistemi-observability-log-surucu
title: "Günlük (Log) Sürücüsü — Halka Tamponu, Taşma ve Kural Dosyaları"
node_type: subdomain
group: mimari
status: active
tags: [observability, log, ring-buffer, logrotate, rate-limit]
children: []
---
# §0 ELI15 — Sürücü neyi taşır

Anayasa günlükün **ne** olduğunu kurar; bu dosya günlük akışının
**mühendisliğini** taşır: şiddet kapısı, halka tamponunun taşması, kural
dosyalarının rotasyonu, log fırtınası (storm) freni, biçim sabitleme ve
istek izi (`traceId`) akışı. Hepsi OS düzeyindedir — uygulama tarafı
kuyruk/rotasyon/collector `K017-observability` sınırının ötesindedir.

Üç kural burada bağlayıcı:

1. Halka dolunca **en eski kayıt düşer**; düşen kayıt sayısını bilmiyorsan
   tahmin etme, `UNKNOWN` yaz.
2. Rotasyon diske yazmadan **önce** dosya tanımını değiştirir, yazıldıktan
   sonra değil; sinyal gitmezse süreç eski inode'a yazar.
3. Eşik değeri envanterde/mevcut ölçümdе yoksa uydurulmaz — sayı ölçülür,
   eşik yazılmaz.

## §1 Şiddet kapısı — yazmadan önce el

| Kapı | Nerede | Etki | Kanıt |
| --- | --- | --- | --- |
| `setlogmask` bitmask | süreç içi (`syslog(3)` öncesi) | OS kuyruğuna hiç düşmez | L-W5 |
| journald `SyslogLevel` | journald yapılandırması | düzey altında olanlar alınmaz | L-W6 |
| çekirdek `console_loglevel` | `/proc/sys/kernel/printk` | konsola çıkmaz (halkada kalır) | L-W9 |
| Windows olay **level** | kayıt çağrısı | kanal filtresi | L-W10 |
| Repo: `shouldLog` | `FileHandler.php:202–205` | min seviye altı yazılmaz | disk |
| Repo: debug guard | `FileHandler.php:190–193` | debug yalnız debug modda | disk |

Önemli ayrım: `console_loglevel` konsolu durdurur, **halkayı durdurmaz**;
`setlogmask` ise halkaya girişi durdurur. Aynı etkiyi veren iki farklı kapı
karıştırılmaz.

```c
setlogmask(LOG_MASK(LOG_ERR));          /* yalnız ERR+ halkaya girer */
setlogmask(LOG_UPTO(LOG_INFO));         /* emerg..INFO halkaya girer */
```

Repo karşılığı — Dosya: `shared/src/Log/FileHandler.php` · Satır: 202–205:

```php
    private function shouldLog(string $level): bool
    {
        return (self::LEVELS[$level] ?? 0) >= (self::LEVELS[$this->minLevel] ?? 0);
    }
```

Bilinmeyen seviye `error`'a düşer (fail-safe: kayıt kaybolmaz) — Dosya:
`shared/src/Log/FileHandler.php` · Satır: 125–132:

```php
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $normalized = strtolower((string) $level);
        if (!isset(self::LEVELS[$normalized])) {
            $normalized = 'error';
        }
        $this->logAt($normalized, (string) $message, $context);
    }
```

## §2 Halka tamponu (ring buffer) ve taşma

| Alan | Davranış | Kanıt |
| --- | --- | --- |
| Çekirdek `printk` halkası | Sabit boy; taşınca en eski mesaj düşer | L-W8 · L-W9 |
| `dmesg` okuması | Halkanın anlık görüntüsü; yazma yapmaz | L-W8 |
| Journald varlıklar | `SystemMaxUse` kapasite üst sınırı | L-W6 · L-W13 |
| Journal flush | `FlushIntervalSec` periyodik boşaltma | L-W6 |
| Repo dosya kuyruğu | `FILE_APPEND \| LOCK_EX` — boyut/taşma yönetimi yok | `FileHandler.php:251` |

Taşma kaybı ölçülebilir mi? Çekirdek halkasında `dmesg` sequence numaraları
arasındaki atlama bakılabilir; **ancak** repo'da bu okuma yapan kod yoktur
(§7 olumsuz kanıt) — bu dalda taşma kaybı değeri `UNKNOWN` olarak yazılır,
uydurulmaz.

```bash
dmesg -T | tail -n 40
journalctl -b -p err --no-pager
```

Dosya sınırı repo'da **tanımlı değildir**: `appendToFile` boyut kontrolü
yapmaz, dolayısıyla uygulama logu sınırsız büyür — bu OS rotasyonuna (§3)
bırakılmış demektir. Dosyanın mevcut boyutu `UNKNOWN` (ölçülmedi).

## §3 Rotasyon kural dosyaları — logrotate

| Yön | Ne kurar | Kanıt |
| --- | --- | --- |
| Periyot/döngü | `daily` / `weekly` / `size` eşiği | L-W14 |
| Saklama | `rotate N` eski dosya sayısı | L-W14 |
| Yazım kırılması | `postrotate` ile sinyal/yeniden aç | L-W14 |
| Sıkıştırma | `compress` (+ `delaycompress`) | L-W14 |
| Paylaşılan devir | `sharedscripts` | L-W14 |
| Eksik dosya | `missingok` | L-W14 |

Rotasyonun **OS tarafı** üç adımdır: (1) dosya yeniden adlandırılır,
(2) sağlayıcıya yeniden açma sinyali gider, (3) eski dosyalar saklama
sayısına göre silinir. Sinyal gitmezse süreç eski inode'a **yazmaya devam
eder** — bu klasik kayıp senaryosudur; tespiti `lsof`/`dmesg` katmanındadır.

Aşağıdaki blok **örnek kural dosyası**dır (repo'da mevcut dosya adı
`UNKNOWN` — O2); hedef dosya adları §5'teki kanonik dört dosyadır:

```text
/var/log/coremusic/*.log {
    daily
    rotate 14
    compress
    delaycompress
    missingok
    notifempty
    sharedscripts
    postrotate
        /usr/bin/systemctl reload-or-restart coremusic-router >/dev/null 2>&1 || true
    endscript
}
```

```bash
logrotate -d /etc/logrotate.d/coremusic
```

`-d` (dry-run) kural dosyasını **yazmadan** değerlendirir; üretimde kural
dosyası bulunamazsa bu komut 404 benzeri hata verir — hata kabul edilir,
kural uydurulmaz.

## §4 journald yapılandırması — taşma ve oran

| Alan | Etki | Kanıt |
| --- | --- | --- |
| `SystemMaxUse=` | Jurnal toplam üst sınırı | L-W6 |
| `SystemMaxFileSize=` | Tek dosya üst sınırı | L-W6 |
| `RateLimitIntervalSec=` / `RateLimitBurst=` | Kaynak başına oran kapısı | L-W6 |
| `FlushIntervalSec=` | Periyodik flush | L-W6 |
| `ForwardToSyslog=` | syslog kuyruğuna devir | L-W13 |
| `SyslogLevel=` | Düşük şiddetli kayıtların elenmesi | L-W6 |

Oran kapısı (rate limit) **reddeder** — düşürmez: limit aşan kayıt
`Suppressed: N msg` ile özetlenir ve içerik kaybolur. Log fırtınası
freninin bu yanı, halka taşmasıyla aynı sonuca (kayıp) farklı yoldan
ulaştırır; hangisinin devrede olduğu `journalctl` özetiyle görülür.

```bash
journalctl --since "10 min ago" | grep -c Suppressed
journalctl _SYSTEMD_UNIT=coremusic-router.service --since today --no-pager
```

## §5 Dosya tanımı ve kanal ayrımı — repo gerçeği

| Tanım | Davranış | Kanıt |
| --- | --- | --- |
| Kanonik 4 dosya | errors / warnings / info / kernel_debug | `FileHandler.php:19–23` (yorum) |
| Güvenlik akışı | `coremusic_php_security.log` (prod dâhil) | `FileHandler.php:24` · `233–235` |
| Uyarı ailesi split | Yalnız debug modda | `FileHandler.php:238–245` |
| Tek yazıcı fabrikası | `LoggerFactory` singleton | `LoggerFactory.php:15–22` |
| Dizini çözüm | `dirname(__DIR__, 3)` → repo kökü | `LoggerFactory.php:29–33` |
| Ekleme atomikliği | `FILE_APPEND \| LOCK_EX` | `FileHandler.php:251` |
| Fatal yolu | Çift yazım: errors + kernel_debug | `PageRouterKernel.php:133–134` |

Dosya: `shared/src/Log/FileHandler.php` · Satır: 248–252 — yazıcı
kapatma/temizlik yüzeyi yoktur; tek yazım noktası eklemedir:

```php
    private function appendToFile(string $filename, string $content): void
    {
        $filepath = $this->logDir . '/' . $filename;
        @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
    }
```

Aynı süreçte iki yazıcının aynı dosyayı ayrı `fopen` moduyla açması
kilitleme yaratır; bu dalda çözümlenmiş bir kilit **repo kanıtı yok** →
`UNKNOWN` (O3'e ek). `@` operatorü hata bastırır — disk dolu/izin hatası
sessizce yutulur; bu, sürücü seviyesinde bilinen bir gözlemlenmezlik
kaynağıdır ve raporlanır, gizlenmez.

## §6 Şekil sabitleme — tek satır, tek JSON

| Kural | Neden | Repo kanıtı |
| --- | --- | --- |
| Tek satır = tek olay | Satır kırılmadan ayrıştırılır | `FileHandler.php:220` · `StructuredLogger.php:69` |
| `JSON_UNESCAPED_SLASHES\|UNICODE` | Unicode/ `/` kaçışı okunurluğu bozar | `FileHandler.php:217` · `StructuredLogger.php:69` |
| Encode başarısızsa düşme metni | `false` halkaya gitmez | `StructuredLogger.php:69` (payload `string`) + `json_encode` hâli |
| `declare(strict_types=1)` | Kanal/seviye tipleri sıkı | `StructuredLogger.php:1` · `FileHandler.php:1` |
| Tarih `date('Y-m-d H:i:s')` | Yerel saat damgası (dosya akışı) | `FileHandler.php:209` |
| Tarih ATOM (ISO 8601) | JSON akışı | `StructuredLogger.php:62` |

Dosya: `shared/src/Log/FileHandler.php` · Satır: 209–220 — biçim tek
yerde kurulur:

```php
        $timestamp = date('Y-m-d H:i:s');
        $uri = $_SERVER['REQUEST_URI'] ?? '-';
        $method = $_SERVER['REQUEST_METHOD'] ?? '-';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '-';
        $trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';

        $contextStr = '';
        if (!empty($context)) {
            $contextStr = ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        $line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;
```

Biçim değişirse ayrıştırıcı kırılır; bu satır biçimi **sözleşmedir** ve
`isletim-sistemi-api` dalıyla çakışmaz (API yanıt gövdesi ayrıdır).

## §7 İstek izi (traceId) — log–trace köprüsü

Dosya: `shared/src/PageRouter/PageRouterKernel.php` · Satır: 87–91 — her
isteğin başında tek `traceId` üretilir ve **iki** yazıcıya birden yazılır:

```php
        $traceId = self::generateTraceId();
        $this->logger->setTraceId($traceId);
        // P5: FileHandler satırları da aynı traceId ile damgalansın —
        // StructuredLogger (JSON) ile dosya log'ları tek istekte birleşir.
        \CoreMusic\Log\FileHandler::setTraceId($traceId);
```

Dosya: `shared/src/Log/FileHandler.php` · Satır: 36–37 · 65–73 — statik
iz alanı ve setter/clearer:

```php
    /** Request correlation — PageRouterKernel her istekte setTraceId() çağırır. */
    private static string $traceId = '';

    public static function setTraceId(string $traceId): void
    {
        self::$traceId = $traceId;
    }

    public static function clearTraceId(): void
    {
        self::$traceId = '';
    }
```

Aynı `traceId` `trace/` alt-dalındaki span kavramına karşılık gelir; ancak
bu dalda **span üretimi yoktur** — yalnızca log satırına yazılan kimlik.
Dağıtık korelasyon (W3C `traceparent`, sampler) `K017-observability`
kapsamındadır.

## §8 Ölçüm ve doğrulama adımları

1. `dmesg -T | tail -n 20` → çekirdek halkası canlı mı, seviye dağılımı nedir.
2. `journalctl -b -p warning --no-pager | wc -l` → boot'tan beri uyarı sayısı
   (envanterde eşik yok → sayı ölçülür, **eşik uydurulmaz**).
3. `logrotate -d /etc/logrotate.d/coremusic` → kural dosyası kuru çalışma
   (dosya adı `UNKNOWN` — §3 dipnotu).
4. `journalctl _SYSTEMD_UNIT=coremusic-router.service --since today` →
   birim filtresi.
5. Repo derlemesi/incelemesi → `StructuredLogger`/`FileHandler` yolu bütünlüğü (§6).
6. `ls -la coremusic_php_*.log` → dört kanonik dosyanın varlığı ve boyutu
   (boyut envantere yazılmaz, yalnız gözlemlenir).

Adım 2–6 çıktıları **kanıt** olarak kaydedilir; içlerinden eşik türetilmez.
Ölçüm yapılmadan eşik yazmak bu dalda yasaktır.

## §9 Olumsuz kanıt — repo'da olmayanlar

| Aranan yüzey | Sonuç | Yorum |
| --- | --- | --- |
| `logrotate` çağıran kod | 0 isabet | Rotasyon tamamen OS'e bırakılmış |
| Boyut/dönüş (size-based rotate) kodu | 0 isabet | `appendToFile` sınırsız |
| Syslog socket / `syslog(` çağrısı | 0 isabet | Uygulama dosyaya yazar, OS kuyruğuna değil |
| `dmesg` / `/dev/kmsg` okuyan kod | 0 isabet | Çekirdek halkası repo'da okunmaz |
| Log gönderici (agent/collector) | 0 isabet | Toplama katmanı `UNKNOWN` |

Bu 0 isabet **olumsuz kanıttır**: uygulama günlüğü OS kuyruğuna bağlanmamış,
dosya kuyruğunda kalmıştır. Toplayıcı (vector/fluent-bit/windows-service)
var mı → `[UNKNOWN]` (O1/O3 ile birlikte).

## §10 Log fırtınası freni — OS + süreç katmanı

| Katman | Mekanizma | Kayıp tipi | Kanıt |
| --- | --- | --- | --- |
| Uygulama çıkışı | `shouldLog` min seviye | Yok (bilinçli) | `FileHandler.php:202–205` |
| Uygulama debug guard | debug + debugMode ikilisi | Yok | `FileHandler.php:190–193` |
| journald | RateLimit burst | İçerik (özetlenir) | L-W6 |
| Halka | Taşma | En eski kayıt | L-W8 |
| Dosya | Rotasyon + saklama | Eski dosya silinir | L-W14 |
| Uygulama hız sınırlayıcı | Saniyede N satır | Düşürme | kanıt yok → `UNKNOWN` |

Dosya: `shared/src/Log/FileHandler.php` · Satır: 188–200 — iki kapı tek
gövdede:

```php
    private function logAt(string $level, string $message, array $context, bool $securityStream = false): void
    {
        if ($level === 'debug') {
            if (!$this->debugMode || !$this->shouldLog('debug')) {
                return;
            }
        } elseif (!$this->shouldLog($level)) {
            return;
        }

        $fileLevel = in_array($level, self::WARNING_FAMILY, true) ? 'error' : 'info';
        $this->write($fileLevel, $message, $context, strtoupper($level), $securityStream);
    }
```

`timing` çağrısı (middleware süre kaydı) `debug` seviyesindedir — Dosya:
`shared/src/PageRouter/StructuredLogger.php` · Satır: 47–53 — üretimde
`minLevel=info` iken bu satırlar **yazılmaz**; süre verisi log akışında
`UNKNOWN`, ölçüm yolu `metrik/` alt-dalındadır.

## §11 K017 sınırı ve komşu haritalar

| Burada (OS) | Karşıda (K017 / komşu) |
| --- | --- |
| Halka/rotasyon/journald | Uygulama log collector, OTel exporter → `K017-observability` |
| `setlogmask` kapısı | Uygulama seviye filtresi → `K017-observability` |
| Log metriği üretimi | `metrik/` alt-dalı |
| `traceId` satır etiketi | Span/sampler → `trace/` alt-dalı (üretim) + K017 (dağıtık) |
| Log bozulma testi | `isletim-sistemi-testing` |
| Audit log güvenliği | `isletim-sistemi-security` |
| Çekirdek halkası içi | `isletim-sistemi-kernel` |
| `/proc` ile log dizini | `isletim-sistemi-core` |
| Syslog uçları / API sözleşmesi | `isletim-sistemi-api` |

## §12 Tam yazım yolu — hatadan güvenliğe tek gövde

Dosya: `shared/src/Log/FileHandler.php` · Satır: 220–246 — satırın
halkaya düşmeden önce geçtiği tüm dallanma (güvenlik akışı + debug split):

```php
        $line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;

        // Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur;
        // e-postalar maskelenir (B-F-17: PII logda düz metin durmasın).
        $line = Redactor::redact($line);

        // Tüm seviyeler errors.log'a yazılır (mevcut davranış korunur —
        // kullanici onayli kanonik 4 dosya: errors/warnings/info/kernel_debug).
        $this->appendToFile('coremusic_php_errors.log', $line);

        // GÜVENLİK AKIŞI (yeni dosya — kullanıcı talebi 2026-10-07): auth + security
        // olayları AYRİ stream'e de yazılır; prod'da dâhil her zaman aktif
        // (audit/SIEM tail'i errors.log kalabalığından ayrışır).
        if ($securityStream) {
            $this->appendToFile('coremusic_php_security.log', $line);
        }

        // Debug modda uyarı ve info da ayrı dosyalara
        if ($this->debugMode) {
            if ($fileLevel === 'warning' || $fileLevel === 'error') {
                $this->appendToFile('coremusic_php_warnings.log', $line);
            }
            if ($fileLevel === 'info' || $fileLevel === 'debug') {
                $this->appendToFile('coremusic_php_info.log', $line);
            }
        }
    }
```

Okuma sırası (bağlayıcı): **zaman/kaynak/iz → redaksiyon → birincil dosya →
(gerekirse) güvenlik akışı → (gerekirse) debug split**. Redaksiyon,
birincil dosyadan **önce** gelir; tersine çevrilen bu sıra hassas veri sızıntısı
demektir ve bu dalda yasaktır.

## §13 Fatal yolu — logger'ın dışında kalan tek yazıcı

Dosya: `shared/src/PageRouter/PageRouterKernel.php` · Satır: 128–136 —
istek işlenemezse logger bağımlılığı olmadan doğrudan yazım; yine de
redaksiyondan geçer:

```php
        } catch (\Throwable $e) {
            $errorMsg = date('c') . ' [' . $traceId . '] ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString() . "\n";
            // Merkezi redaction (§17 Edge Case 3): trace argümanlarındaki hassas alanlar maskelenir.
            $errorMsg = Redactor::redact($errorMsg);
            $logDir = $this->config->get('app.log_dir', dirname(__DIR__, 3));
            @file_put_contents($logDir . '/coremusic_php_errors.log', $errorMsg, FILE_APPEND | LOCK_EX);
            @file_put_contents($logDir . '/coremusic_php_kernel_debug.log', $errorMsg, FILE_APPEND | LOCK_EX);
            error_log(Redactor::redact('[PageRouterKernel] FATAL traceId=' . $traceId . ' ' . $e->getMessage()
                . ' in ' . $e->getFile() . ':' . $e->getLine()));
```

| Gözlem | Değer | Kanıt |
| --- | --- | --- |
| `getTraceAsString` hassas taşır | Redaksiyon zorunlu | `PageRouterKernel.php:129–131` |
| Çift yazım | errors + kernel_debug | `PageRouterKernel.php:133–134` |
| `error_log` sızıntısı | Üçüncü çıkış, PHP hata kanalı | `PageRouterKernel.php:135–136` |
| `@` operatorü | Hata sessiz yutulur | `PageRouterKernel.php:133–134` |
| Fatal'ta `clearTraceId` | Çağrı yok → statik iz sonraki isteğe taşınabilir | `FileHandler.php:70–73` (tanım var, çağrı 0) |

Son satırdaki **çağrı yokluğu** olumsuz kanıttır: `clearTraceId()` tanımlı
ancak `PageRouterKernel` fatal yolunda çağrılmıyor — istek başına `setTraceId`
üzerinden eziliyor (Satır: 87–91), bu yüzden sızma riski ortalama istek
uzunluğuyla sınırlıdır; **ölçülmedi** → `UNKNOWN`.

## §14 Platform surucu karşılaştırması — OS araç farkları

| İş | Linux | Windows | Apple | Kanıt |
| --- | --- | --- | --- | --- |
| Çekirdek okuma | `dmesg` | Olay kanalı (SCHANNEL/driver) | `log show` | L-W8 · L-W10 · L-W12 |
| Servis günlüğü | `journalctl` | Event Viewer | unified log | L-W7 · L-W11 · L-W12 |
| Oran kapısı | journald RateLimit | Kanal taşıma ayarı | — (envanter dışı) | L-W6 |
| Rotasyon | `logrotate` | Kanal dönüştürme | depo boyutu politikası | L-W14 |
| Seviye filtresi | `setlogmask` / `SyslogLevel` | Level/keyword | Type/fault | L-W5 · L-W6 · L-W10 |

Windows/Apple satırlarında bu dalın envanteri **yalnız resmi belgedir**;
repo kanıtı yok → ilgili hücrelere `UNKNOWN` işareti §8'de konmuştur.
Bu tablo `isletim-sistemi-turu` dalıyla çakışmaz: orada OS **kurulum/dağıtım**
farkları, burada **gözlеm arayüzü** farkları anlatılır.

## §15 Kaynakça (envanter dışı, HTTP 200 — 2026-10-13)

| # | İddia | URL | Tarih | Durum |
| --- | --- | --- | --- | --- |
| L-W5 | `setlogmask(3)` bitmask kapıları | https://man7.org/linux/man-pages/man3/setlogmask.3.html | 2026-10-13 | 200 |
| L-W6 | `journald.conf(5)` RateLimit/SystemMaxUse/Flush | https://www.freedesktop.org/software/systemd/man/latest/journald.conf.html | 2026-10-13 | 200 |
| L-W7 | `journalctl(1)` filtre bayrakları | https://www.freedesktop.org/software/systemd/man/latest/journalctl.html | 2026-10-13 | 200 |
| L-W8 | `dmesg(1)` halka okuması | https://man7.org/linux/man-pages/man1/dmesg.1.html | 2026-10-13 | 200 |
| L-W9 | `printk` level/format kuralları | https://www.kernel.org/doc/html/latest/core-api/printk-formats.html | 2026-10-13 | 200 |
| L-W10 | Windows Event Log kaydı | https://learn.microsoft.com/en-us/windows/win32/wes/windows-event-log | 2026-10-13 | 200 |
| L-W11 | Windows Event Log kullanım akışı | https://learn.microsoft.com/en-us/windows/win32/wes/using-windows-event-log | 2026-10-13 | 200 |
| L-W12 | Apple os_log arayüzü | https://developer.apple.com/documentation/os/os_log | 2026-10-14 | 200 |
| L-W13 | `systemd-journald(8)` servis/forward | https://www.freedesktop.org/software/systemd/man/latest/systemd-journald.html | 2026-10-13 | 200 |
| L-W14 | `logrotate(8)` rotate/schedule/postrotate | https://man7.org/linux/man-pages/man8/logrotate.8.html | 2026-10-13 | 200 |

**Açık sorular:** O1 `[UNKNOWN]` üretim log hacmi (GB/gün) — ölçülmedi.
O2 `[UNKNOWN]` mevcut rotasyon dosyasının adı/konumu — repo/envanter 0 isabet.
O3 `[UNKNOWN]` süreç içi hız sınırlayıcı / log toplayıcı varlığı — kanıt yok.

## §16 Seviye eşlemesi — OS arayüzleri arası karar tablosu (derinleştirme)

> §14'teki envanter tablosu **hangi arayüzün hangi seviyeyi taşıdığı**nı
> veriyordu. Bu bölüm **aynı olayın üç OS'ta hangi seviyeye karşılık geldiğini**
> eşler. Karar, uygulamanın tek bir log çağrısının üç OS'ta nasıl yorumlanacağını
> belirler.

### §16.1 Olay → seviye eşlemesi

| # | Olay | Linux (journald) | Windows (Event Log) | Apple (os_log) |
|---|---|---|---|---|
| 1 | Servis başlatma | `LOG_INFO` | Information | `os_log_info` |
| 2 | Servis durdurma | `LOG_INFO` | Information | `os_log_info` |
| 3 | Bağlantı reddi | `LOG_WARNING` | Warning | `os_log_error` |
| 4 | Kimlik doğrulama hatası | `LOG_AUTHPRIV` + Warning | Failure Audit | `os_log_error` |
| 5 | Kaynak tükenmesi eşiği | `LOG_CRIT` | Critical | `os_log_fault` |
| 6 | Veri kaybı riski | `LOG_ERR` | Error | `os_log_error` |
| 7 | Zaman aşımı (yeniden denenebilir) | `LOG_NOTICE` | Information | `os_log_info` |
| 8 | Yeniden deneme başarısız | `LOG_WARNING` | Warning | `os_log_error` |

> **Dürüstlük:** Bu eşleme **şema kararıdır, ölçüm değildir**. Üç OS'un seviye
> kümeleri birebir aynı değildir (bkz. §14 satır 4–6); eşleme
> `UNKNOWN`/`⚠️ VERIFICATION REQUIRED` üretmez çünkü hiçbir sayısal iddia
> içermez — yalnızca yönlendirme içerir.

### §16.2 Taşıma sınırı kararları

| # | Karar | Linux | Windows | Apple |
|---|---|---|---|---|
| 1 | Sınır nerede? | journald `RateLimitBurst` | Kanal taşıma politikası | Unified logging akışı |
| 2 | Sınır aşılırsa | sessizce düşür | kanal politikasına bağlı | taşma işaretlenir |
| 3 | Sınır ayarı kalıcı mı? | `journald.conf` | kayıt defteri / GPO | yapılandırma profili |
| 4 | Sınır geçici mi? | servis yeniden başlatma | yeniden başlatma | yeniden başlatma |
| 5 | Etki alanı | sistem geneli | kanal bazlı | subsystem bazlı |

### §16.3 Rotasyon kararı (Linux odaklı)

| # | Ayar | Etki | Uyarı |
|---|---|---|---|
| 1 | `SystemMaxUse` | journald üst sınırı | disk doluluğuna göre ayarlanır |
| 2 | `RuntimeMaxUse` | RAM üst sınırı | yeniden başlatmada sıfırlanır |
| 3 | `logrotate` `rotate` | dosya sayısı | postrotate zinciri kırılmamalı |
| 4 | `logrotate` `size` | dosya boyutu eşiği | `rotate` ile birlikte tanımlanır |
| 5 | `logrotate` `compress` | arşivleme | postrotate sırasında I/O yükü |

> **Sorumluluk sınırı:** §16.3 **yalnız dosya isimlerini/ayarlarını tarif eder**;
> gerçek `logrotate` yapılandırması veya `journald.conf` değişikliği
> DevOps Engineer + kullanıcı onayı gerektirir (`.github/AGENTS.md` §3.3-4).

### §16.4 Uygulama tarafı çağrı sırası

| # | Adım | Not |
|---|---|---|
| 1 | Olayı sınıflandır (§16.1) | Seviye seçimi burada yapılır |
| 2 | Sabit alt yapıyı ekle | subsystem, kod, sürüm |
| 3 | Bağlam değişkenlerini ekle | kullanıcı ID, istek ID (kişisel veri yok) |
| 4 | Mesajı formatla | şablon sabit, değerler parametrik |
| 5 | Arayüze gönder | §16.1'deki eşlemeye göre |
| 6 | Sınır aşımını işle | §16.2'deki politika |

### §16.5 §16'nın kanıt etkisi

| Kategori | §16 öncesi | §16 sonrası | Not |
|---|---|---|---|
| S5 (URL) | §15'te 10 URL / 200 | değişmedi | §16 yeni URL **eklemez** |
| S4 (yorum) | §14'te envanter | +3 tablo (§16.1–§16.3) | eşleme = tasarım kararı |
| S7 (bilinmeyen) | §O1–O3 açık | değişmedi | 3 açık madde **kapatılmadı** |

> **Sonuç:** §16 dosyanın **karar kullanılabilirliğini** artırır; kanıt
> oranını ve açık madde sayısını **değiştirmez**. O1–O3 yalnız gerçek ölçümle
> kapanır.

## §17 OS sürücü turu — günlük okuma/yazma araçları (2026-10-14 genişletme)

> §14 **farkları** çiziyordu; bu bölüm her OS'un sürücü (araç) setini
> **komut düzeyinde** verir. Kanıtlar 2. tur doğrulamasından gelir
> (L-W18–L-W27, tam liste dogrulama §1.1) — tümü HTTP 200.

### §17.1 Linux — journald / syslog / auditd

| Araç | İş | Örnek | Kanıt |
| --- | --- | --- | --- |
| `journalctl` | jurnal okuma/filtreleme | `journalctl -u php-fpm --since "10 min ago"` | L-W7 |
| `dmesg` | çekirdek halkası okuma | `dmesg --level=err,warn -T` | L-W8 |
| `logger` | satır içi syslog üretimi | `logger -p local0.err "route rejected"` | L-W1 |
| `ausearch` | audit kaydı sorgu | `ausearch -m USER_LOGIN -ts today` | L-W19 |
| `auditd` | audit daemon (disk kaydı) | yapılandırma `/etc/audit/` | L-W18 |

`auditd` journald'ın **yanında** çalışır: journald servis yaşam döngüsünü,
auditd güvenlik olaylarını (dosya erişimi, kimlik doğrulama) diske tutarlı
yazar. Audit kuralları `auditctl` ile belirlenir; bu dosyada kural yazımı
**tarif amaçlıdır**, repo'da audit kuralı yoktur (`UNKNOWN`, §15 O3).

### §17.2 Windows — Event Log

| Araç | İş | Örnek | Kanıt |
| --- | --- | --- | --- |
| `wevtutil` | olay okuma/yazma | `wevtutil qe Application /c:10 /f:text` | L-W20 |
| `Get-WinEvent` | PowerShell okuma | `Get-WinEvent -FilterHashtable @{LogName='Application'}` | L-W21 |
| `eventcreate` | satır içi olay yazma | `eventcreate /T ERROR /ID 101 /L APPLICATION /D "msg"` | L-W22 |
| `ReportEventW` | Win32 API kayıt | C/C++ `ReportEvent(h, ...)` | L-W23 |
| XPath sorgu | olay filtreleme | `*[System[Level=2 and TimeCreated[timediff(...)` | L-W24 |

`ReportEventW` (L-W23) üretim API'sidir — `.NET` tarafı `EventLog.WriteEntry`
ile aynı kanala yazar; `eventcreate` (L-W22) komut satırı köprüsüdür.
Sorgulama iki yoldan yapılır: `wevtutil` (L-W20) veya PowerShell
`Get-WinEvent` (L-W21); ikisi de XPath filtresi kabul eder (L-W24).

### §17.3 macOS — unified logging

| Araç | İş | Örnek | Kanıt |
| --- | --- | --- | --- |
| `log(1) show` | kayıt okuma/filtreleme | `log show --last 10m --predicate 'subsystem == "net.coremusic"'` | L-W25 |
| `log(1) emit` | kayıt üretme | `log emit --level error --message "route rejected"` | L-W25 |
| `os_log` | Objective-C/C arayüzü | `os_log(os_log_default, "route %{public}@", name)` | L-W12r |
| `Logger` | Swift arayüzü | `Logger(subsystem: "net.coremusic", category: "router")` | L-W26 · L-W27 |

`Logger(subsystem:category:)` (L-W27) kayıt kaynağı tanımıdır; subsystem
alan adı formatında, category serbest metindir — iki alan da `log show`
filtreleme anahtarıdır (L-W25). Hassas alanlar `%{private}` belirteciyle
gizlenir (L-W12); bu, repo'daki `Redactor::redact`'in OS tarafındaki
karşılığıdır — ikisi de **halkadan önce** çalışır.

> **Dipnot (ayna):** `log.1` (L-W25) üçüncü taraf aynasından
> (keith.github.io) doğrulanmıştır; `log(1)` sayfası Apple'ın kendi
> sitesinde ayrıca yer alır ama bu turda HTTP 200 **yalnız aynadan**
> alınmıştır — belge sürümü `UNKNOWN` (dogrulama §1.1).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
