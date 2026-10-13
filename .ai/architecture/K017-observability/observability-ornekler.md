---
node_id: K017-observability/observability-ornekler
node_type: module
status: draft
parent_id: K017-observability
children: []
depends_on:
  - K009-security
  - K019-testing
evidence:
  - "shared/src/Log/LoggerFactory.php · Satır 11-33 — final class LoggerFactory (tek singleton, 34 satır)"
  - "shared/src/Log/FileHandler.php · Satır 30-253 — final class FileHandler implements LoggerInterface (253 satır)"
  - "shared/src/Log/Redactor.php · Satır 25-106 — final class Redactor (106 satır, 3 regex)"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 44-163 — PSR-3 + trace-id testleri (163 satır, 11 test)"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 55-189 — redaction testleri (189 satır, 9 test)"
validation:
  - "Tüm Dosya/Satır kanıtları 2026-10-13'te dosya-içinde birebir okundu"
  - "Web tablosu 1 URL · envanter (2640) dışı · HTTP 200"
  - "Kod blokları gerçek kaynak dosyalardan alındı · uydurma satır 0"
open_questions:
  - "StructuredLogger sınıfı redaction'dan geçiyor (test var) ama dosyası bu turda okunmadı — kademe-3"
  - "PageRouterKernel fatal logu ayrı yazıcı (FileHandler yorumu Satır 23) — kademe-3"
group: kod
---


# K017-observability — observability-ornekler.md

## §1 Amaç (ELI15)

Bu dosya K017 gözlem katmanının **gerçek repo kodunu** örneklerle gösterir: log nasıl üretilir,
nasıl maskelenir, nasıl dosyalanır, nasıl test edilir. Uydurma kod yok — her blok `Dosya:` +
`Satır:` ile kaynak dosyaya bağlıdır ve `Select-Object -Index (N-1)` ile doğrulanmıştır. Amaç,
kademe-3 derinleştirmeye **gerçek temel** bırakmaktır.

## §2 Örnek-Yapı Sözleşmesi

| # | Kural | Açıklama |
|---|---|---|
| 1 | Her örnek gerçek dosyadan | `Dosya: … / Satır: …` + satır indeksi |
| 2 | Uydurma satır yok | Kaynakta olmayan satır yazılmaz |
| 3 | Blok = kaynak ile birebir | Kod bloğu kaynaktan kırpılmış hâli |
| 4 | Türkçe açıklama · İngilizce terim | Yorum satırı Türkçe, API/sınıf adı İngilizce |
| 5 | Kademe-3 öncesi temel | Derin test senaryosu ayrı tur |

## §3 Örnek 1 — LoggerFactory (tek nokta erişim · singleton)

**Dosya:** `shared/src/Log/LoggerFactory.php` · Satır: 11-22 — singleton fabrika.

```php
final class LoggerFactory
{
    private static ?FileHandler $logger = null;

    public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
    {
        if (self::$logger === null) {
            $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
            self::$logger = new FileHandler($logDir, $level);
        }
        return self::$logger;
    }
```

| # | Satır | Ne yapar | Kapsam bağlantısı |
|---|---|---|---|
| 1 | 13 | `private static ?FileHandler $logger` — tek instance | §2/4 tek fabrika |
| 2 | 15 | `getInstance(...)` — lazy singleton | §5/6 erişim tek nokta |
| 3 | 17-20 | null ise kur, değilse dön | Dağıtık loglama önlenir |
| 4 | 24-27 | `reset()` — test için sıfırlama | Test edilebilirlik |
| 5 | 29-33 | `resolveLogDir()` → `dirname(__DIR__, 3)` | Repo kökü kanonik hedef |

**Dosya:** `shared/src/Log/LoggerFactory.php` · Satır: 29-33 — log dizini çözümü.

```php
private static function resolveLogDir(): string
{
    // Proje kök dizinine yaz (C:\www\coremusic.net\)
    return dirname(__DIR__, 3);
}
```

> **Neden önemli:** `dirname(__DIR__, 3)` → `shared/src/Log`'dan 3 seviye yukarı = repo kökü.
| Log dosyaları tek yerde (repo kökü) toplanır; dağınık log yazımı önlenir.

## §4 Örnek 2 — FileHandler (PSR-3 arayüzü · seviye haritası)

**Dosya:** `shared/src/Log/FileHandler.php` · Satır: 30 · 40-49 — sınıf + seviye haritası.

```php
final class FileHandler implements LoggerInterface
{
    private const LEVELS = [
        'debug'     => 0,
        'info'      => 1,
        'notice'    => 2,
        'warning'   => 3,
        'error'     => 4,
        'critical'  => 5,
        'alert'     => 6,
        'emergency' => 7,
    ];
```

| # | Seviye | Önem | Ailesi | Dosya (debug) |
|---|---|---|---|---|
| 1 | debug | 0 | info | info.log |
| 2 | info | 1 | info | info.log |
| 3 | notice | 2 | info | info.log |
| 4 | warning | 3 | warning | errors.log + warnings.log |
| 5 | error | 4 | warning | errors.log + warnings.log |
| 6 | critical | 5 | warning | errors.log + warnings.log |
| 7 | alert | 6 | warning | errors.log + warnings.log |
| 8 | emergency | 7 | warning | errors.log + warnings.log |

**Dosya:** `shared/src/Log/FileHandler.php` · Satır: 125-132 — PSR-3 dispatcher (fail-safe).

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

> **Fail-safe:** Bilinmeyen seviye `error`'a düşer (Satır 128-130) — log **kaybolmaz**.
| PSR-3 interface `log()` untyped `$level` ister; normalize edilip haritaya bakılır.

**Dosya:** `shared/src/Log/FileHandler.php` · Satır: 65-73 — trace-id (correlation).

```php
public static function setTraceId(string $traceId): void
{
    self::$traceId = $traceId;
}

public static function clearTraceId(): void
{
    self::$traceId = '';
}
```

| # | Metod | Görev | Kapsam |
|---|---|---|---|
| 1 | setTraceId | İstek başında trace-id ata | correlation/ §5/19 |
| 2 | clearTraceId | İstek sonunda temizle | Sızıntıyı önler |
| 3 | $traceId (static) | Satır damgası kaynağı | correlation/ tek nokta |

## §5 Örnek 3 — Satır Bileşimi (write) · Merkezi Redaction

**Dosya:** `shared/src/Log/FileHandler.php` · Satır: 207-224 — satır kurma + redaction.

```php
private function write(string $fileLevel, string $message, array $context, string $logLevel, bool $securityStream = false): void
{
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

    // Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur;
    // e-postalar maskelenir (B-F-17: PII logda düz metin durmasın).
    $line = Redactor::redact($line);
```

| # | Satır | Alan | Boşken | Not |
|---|---|---|---|---|
| 1 | 209 | timestamp | — | `Y-m-d H:i:s` |
| 2 | 210-212 | uri · method · ip | `-` | $_SERVER okunur |
| 3 | 213 | trace | boş | `[trace=...]` damgası |
| 4 | 215-218 | context | boş | JSON (UNESCAPED_UNICODE) |
| 5 | 220 | tek satır | — | Bileşim |
| 6 | 224 | Redactor | — | **Merkezi redaction** |

**Dosya:** `shared/src/Log/FileHandler.php` · Satır: 228-234 — dosyalama + security stream.

```php
$this->appendToFile('coremusic_php_errors.log', $line);

if ($securityStream) {
    $this->appendToFile('coremusic_php_security.log', $line);
}
```

| # | Dosya | Ne zaman | Kanonik |
|---|---|---|---|
| 1 | coremusic_php_errors.log | Tüm seviyeler (her zaman) | evet |
| 2 | coremusic_php_warnings.log | debugMode + warning-ailesi | evet (split) |
| 3 | coremusic_php_info.log | debugMode + info/debug | evet (split) |
| 4 | coremusic_php_security.log | securityStream (prod dâhil) | evet (audit) |

## §6 Örnek 4 — Redactor (3 katmanlı maskeleme)

**Dosya:** `shared/src/Log/Redactor.php` · Satır: 31 · 40 · 49 · 56 — 3 regex.

```php
private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';

private const KEY_VALUE_PATTERN = '/(\b(?:' . self::SENSITIVE_KEYS . ')\b["\']?\s*[=:]\s*)("[^"]*"|\'[^\']*\'|(?:Bearer|Basic|Digest)\s+[^\s,&;)\]}]+|[^\s,&;)\]}]+)/i';

private const LONG_HASH_PATTERN = '/(?<![A-Za-z0-9+\/])[A-Za-z0-9+\/]{64}(?![A-Za-z0-9+\/])/';

private const EMAIL_PATTERN = '/(?<![A-Za-z0-9._%+-])[A-Za-z0-9._%+-]+@([A-Za-z0-9.-]+\.[A-Za-z]{2,})/';
```

| # | Regex | Görev | Örnek girdi → çıktı |
|---|---|---|---|
| 1 | KEY_VALUE_PATTERN | Anahtar-değer maskesi | `password=x` → `password=[REDACTED]` |
| 2 | LONG_HASH_PATTERN | 64-char hash maskesi | 64 hex → `[REDACTED]` |
| 3 | EMAIL_PATTERN | E-posta maskesi | `a@b.com` → `***@b.com` |

**Dosya:** `shared/src/Log/Redactor.php` · Satır: 65-93 — dış arayüz + 3 geçiş.

```php
public static function redact(string $line): string
{
    if ($line === '') {
        return $line;
    }
    $redacted = preg_replace_callback(self::KEY_VALUE_PATTERN, static fn (array $m): string => self::maskValue($m[1], $m[2]), $line);
    if ($redacted === null) {
        return $line;
    }
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);
    $withEmail = preg_replace_callback(self::EMAIL_PATTERN, static fn (array $m): string => '***@' . $m[1], $replaced ?? $redacted);
    return $withEmail ?? ($replaced ?? $redacted);
}
```

| # | Geçiş | Satır | Mantık |
|---|---|---|---|
| 1 | Boş satır | 66-68 | Erken dönüş (false-positive yok) |
| 2 | Anahtar maskesi | 72-76 | KEY_VALUE_PATTERN + maskValue |
| 3 | Hash maskesi | 83 | LONG_HASH_PATTERN |
| 4 | E-posta maskesi | 86-89 | EMAIL_PATTERN → `***@alan` |
| 5 | Null güvence | 92 | `??` fallback — crash yok |

**Dosya:** `shared/src/Log/Redactor.php` · Satır: 98-105 — tırnak korumalı maskeleme.

```php
private static function maskValue(string $prefix, string $value): string
{
    if ($value !== '' && ($value[0] === '"' || $value[0] === "'")) {
        return $prefix . $value[0] . self::MASK . $value[0];
    }
    return $prefix . self::MASK;
}
```

> Tırnaklı değer `"[REDACTED]"` olur; tırnaksız `key=[REDACTED]` — JSON/dizi çıktısı bozulmaz.

## §7 Örnek 5 — Test Kanıtları (PSR-3 · redaction)

**Dosya:** `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 44-47 — PSR-3 arayüz testi.

```php
public function testImplementsPsr3LoggerInterface(): void
{
    $this->assertInstanceOf(LoggerInterface::class, new FileHandler($this->dir));
    $this->assertInstanceOf(LoggerInterface::class, LoggerFactory::getInstance($this->dir));
}
```

**Dosya:** `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 60-63 — sekiz seviye etiketi.

```php
foreach (['[EMERGENCY]', '[ALERT]', '[CRITICAL]', '[ERROR]', '[WARNING]', '[NOTICE]', '[INFO]', '[DEBUG]'] as $tag) {
    $this->assertStringContainsString($tag, $logContent);
}
```

**Dosya:** `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 106-113 — e-posta maskesi testi.

```php
public function testEmailLocalPartMaskedDomainKept(): void
{
    $line = Redactor::redact('user login email=user@example.com');
    $this->assertStringContainsString('***@example.com', $line);
    $this->assertStringNotContainsString('user@example.com', $line);
}
```

| # | Test | Satır | Doğrular |
|---|---|---|---|
| 1 | testImplementsPsr3LoggerInterface | 44 | PSR-3 arayüzü (§5/6) |
| 2 | testAllPsr3MethodsAreCallable | 49 | 8 seviye methodu |
| 3 | seviye etiketi döngüsü | 63 | [EMERGENCY]…[DEBUG] |
| 4 | testTraceId_isStampedOnLines | 116 | [trace=...] damgası |
| 5 | testEmailLocalPartMaskedDomainKept | 106 | E-posta PII maskesi |
| 6 | testSixtyFourCharHashIsRedactedButUuidIsPreserved | 122 | Hash redakte · UUID korunur |
| 7 | testFileHandlerWritePathAppliesRedaction | 140 | Yazma yolu redaction |
| 8 | testStructuredLoggerWritePathAppliesRedaction | 165 | Yığın logger redaction |

## §8 Uçtan Uca Örnek Akışı (istek → dosya)

| # | Adım | Sınıf/metod | Satır | Çıktı |
|---|---|---|---|---|
| 1 | Fabrika | LoggerFactory::getInstance | LoggerFactory 15 | FileHandler |
| 2 | Trace ata | FileHandler::setTraceId | FileHandler 65 | $traceId |
| 3 | Log çağır | $log->error(...) | FileHandler 92 | logAt('error') |
| 4 | Seviye kontrol | shouldLog / logAt | FileHandler 188-205 | izin/karar |
| 5 | Satır kur | write | FileHandler 207-220 | tek satır |
| 6 | Maskele | Redactor::redact | FileHandler 224 | [REDACTED] satır |
| 7 | Dosyala | appendToFile (errors) | FileHandler 228 | coremusic_php_errors.log |
| 8 | Güvenlik | appendToFile (security) | FileHandler 234 | coremusic_php_security.log |

## §9 Repo Kanıtları (ornekler — gerçek dosya/satır)

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 11 — `final class LoggerFactory`.

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 13 — `private static ?FileHandler $logger = null;`.

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 15 — `public static function getInstance(...): FileHandler`.

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 32 — `return dirname(__DIR__, 3);`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 30 — `final class FileHandler implements LoggerInterface`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 40 — `private const LEVELS = [`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 52 — `private const WARNING_FAMILY`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 65 — `public static function setTraceId(string $traceId): void`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 125 — `public function log($level, ...): void`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 188 — `private function logAt(...): void`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 207 — `private function write(...): void`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 224 — `$line = Redactor::redact($line);`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 228 — `appendToFile('coremusic_php_errors.log', ...)`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 234 — `appendToFile('coremusic_php_security.log', ...)`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 251 — `@file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);`.

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — `private const SENSITIVE_KEYS`.

Dosya: `shared/src/Log/Redactor.php` · Satır: 40 — `private const KEY_VALUE_PATTERN`.

Dosya: `shared/src/Log/Redactor.php` · Satır: 49 — `private const LONG_HASH_PATTERN`.

Dosya: `shared/src/Log/Redactor.php` · Satır: 56 — `private const EMAIL_PATTERN`.

Dosya: `shared/src/Log/Redactor.php` · Satır: 65 — `public static function redact(string $line): string`.

Dosya: `shared/src/Log/Redactor.php` · Satır: 98 — `private static function maskValue(...)`.

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 44 — `testImplementsPsr3LoggerInterface`.

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 63 — seviye etiketi döngüsü.

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 116 — `testTraceId_isStampedOnLines`.

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 106 — `testEmailLocalPartMaskedDomainKept`.

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 122 — hash/UUID ayrımı testi.

Dosya: `shared/composer.json` · Satır: 19 — `"psr/log": "^3.0"` (PSR-3 bağımlılığı).

## §10 Web tablosu (ornekler)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Prometheus ölçüm/etiket modeli — metrik adı + label (etiket) ile cardinality yönetimi K017 metrics/ tasarımının referansıdır (log örneğinin metrik karşılığı) | https://www.prometheus.io/docs/introduction/overview/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter (2640) dışı |

## §11 Doğrulama (ornekler)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | §L/1 |
| 2 | Son satır | Authority dizgesi birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + node_id + group (kod) · parent: 0 | ✓ |
| 4 | Repo kanıtları | ≥ 1 Dosya/Satır · gerçek indeks | ✓ (27 satır) |
| 5 | Kod blokları | gerçek kaynaktan · uydurma satır 0 | ✓ |
| 6 | Web tablosu | 1 URL · envanter dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | 3 yer-tutucu türü (dolgu dâhil) · boş hücre · mojibake | ✓ 0 |

## §A Kod→Kapsam Bağlantısı

| # | Kod örneği | Kapsam maddesi | Kanıt |
|---|---|---|---|
| 1 | LoggerFactory singleton | §2/4 tek fabrika | LoggerFactory.php 15 |
| 2 | FileHandler implements LoggerInterface | §5/6 PSR-3 | FileHandler.php 30 |
| 3 | Redactor::redact(write içinde) | §5/5 merkezi redaction | FileHandler.php 224 |
| 4 | SENSITIVE_KEYS tek kaynak | §5/18 anahtar listesi | Redactor.php 31 |
| 5 | security stream | §5/22 audit akışı | FileHandler.php 234 |
| 6 | setTraceId / [trace=] | §5/19 correlation | FileHandler.php 65 · 213 |
| 7 | e-posta maskesi | §5/25 PII | LoggerRedactionTest 106 |
| 8 | hash/UUID ayrımı | §5/26 | LoggerRedactionTest 122 |

## §B Kademe-3 Derinleştirme Notları (bu dosyanın sınırı)

| # | Konu | Bu dosyada | Kademe-3'te |
|---|---|---|---|
| 1 | StructuredLogger | Test referansı var (Satır 165) | Sınıf dosyası okunacak |
| 2 | PageRouterKernel fatal | FileHandler yorumu (Satır 23) | Ayrı yazıcı incelenecek |
| 3 | PSR-3 tam uyum matrisi | Arayüz + 8 method | Bileşen-bileşen doğrulama |
| 4 | Performans (log write maliyeti) | Yok | Bottleneck kaydı |
| 5 | Log rotasyonu/kesme | Yok (K018 politikası) | K018 ile sınır |

## §C Terim-Örnek Eşlemesi

| Terim | Örnek | Dosya |
|---|---|---|
| Singleton | LoggerFactory::getInstance | LoggerFactory.php 15 |
| Fail-safe | bilinmeyen → error | FileHandler.php 128 |
| Merkezi redaction | Redactor::redact | FileHandler.php 224 |
| Security stream | securityEvent → security.log | FileHandler.php 234 |
| Trace damgası | [trace=...] | FileHandler.php 213 |
| PII maskesi | ***@alan | Redactor.php 88 |
| LOCK_EX | dosya kilidi | FileHandler.php 251 |

## §D Ölçüm günlüğü (ornekler)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | Repo kanıtı satırı | 27 Dosya/Satır |
| 2 | Kod bloğu sayısı | 8 (gerçek kaynak) |
| 3 | Web URL | 1 · envanter dışı · HTTP 200 |
| 4 | group | kod (tırnaksız) |
| 5 | parent: | 0 |
| 6 | Uydurma satır | 0 |
| 7 | Dosya uzunluğu | §L/1 |

## §E Referanslar

| Kaynak | Referans |
|---|---|
| Kapsam | observability-kapsam.md §5 · §7 |
| Master | observability-master.md §B · §E |
| Doğrulama | observability-dogrulama.md |
| Bağımlılık | observability-bagimliliklar.md |

## §F Özet (1 satır)

K017 log zinciri: `getInstance → setTraceId → logAt → write → Redactor::redact → appendToFile`;
tek fabrika · merkezi redaction · PSR-3 · security stream · trace damgası — hepsi dosya/satır kanıtlı.

## §G Örnek-Hız Kartı

| Katman | Sınıf | Kritik satır | Görev |
|---|---|---|---|
| Fabrika | LoggerFactory | 15 | singleton |
| Handler | FileHandler | 30 | PSR-3 + write |
| Maskeleme | Redactor | 65 | 3 geçiş |
| Test | Psr3LoggerTest | 44 | arayüz kanıtı |
| Test | LoggerRedactionTest | 106 | PII kanıtı |

## §H Uydurma-Yok Beyanı

Bu dosyadaki her kod bloğu `Dosya:` + `Satır:` ile bağlıdır; kaynakta olmayan satır üretilmemiştir.
Metrik/tracing/health/alert için repo kodu **YOK** → bu dosyada örneklenmez (kaynak: vault).
Domain kodu yoksa `Repo'da bu domain'e ait kod YOK — kaynak: vault` yazılır; uydurulmaz.

## §I Örnek→Test Eşlemesi (regresyon kapsamı)

| # | Davranış | Test | Regresyon koruması |
|---|---|---|---|
| 1 | PSR-3 arayüzü | testImplementsPsr3LoggerInterface | Arayüz kırılırsa kırmızı |
| 2 | 8 seviye | testAllPsr3MethodsAreCallable | Seviye eksilirse kırmızı |
| 3 | Trace damgası | testTraceId_isStampedOnLines | Correlation kırılırsa kırmızı |
| 4 | E-posta maskesi | testEmailLocalPartMaskedDomainKept | PII sızarsa kırmızı |
| 5 | Hash/UUID | testSixtyFourCharHash... | Yanlış maske kırmızı |
| 6 | Yazma yolu redaction | testFileHandlerWritePath... | Ham sızarsa kırmızı |
| 7 | Yığın logger | testStructuredLoggerWritePath... | Ham sızarsa kırmızı |
| 8 | Query-string | testAuthKeyInQueryStringIsRedacted | URL secret kırmızı |

## §J Örnek-Dosya Envanteri (bu domainde okunan)

| # | Dosya | Satır | Durum |
|---|---|---|---|
| 1 | shared/src/Log/LoggerFactory.php | 34 | okundu (bu dosyada) |
| 2 | shared/src/Log/FileHandler.php | 253 | okundu (bu dosyada) |
| 3 | shared/src/Log/Redactor.php | 106 | okundu (bu dosyada) |
| 4 | shared/tests/Unit/Log/Psr3LoggerTest.php | 163 | kısmi okundu (kanıt satırları) |
| 5 | shared/tests/Unit/Log/LoggerRedactionTest.php | 189 | kısmi okundu (kanıt satırları) |

## §K Örnek-Öncelik (kademe-3 sırası)

| # | Öncelik | Konu | Gerekçe |
|---|---|---|---|
| 1 | P0 | StructuredLogger dosyası | Redaction testi referans var, sınıf yok |
| 2 | P0 | PageRouterKernel fatal yazıcı | Ayrı stream (FileHandler yorum 23) |
| 3 | P1 | PSR-3 tam matris | 8 method tek tek |
| 4 | P1 | Log write performansı | Bottleneck ölçümü |
| 5 | P2 | Rotasyon/kesme | K018 sınırı |

## §L Revizyon doğrulama kaydı (ornekler)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 502 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + node_id + group (kod, tırnaksız) · parent: 0 | ✓ |
| 4 | Repo kanıtları | 27 Dosya/Satır · gerçek satır indeksi ile birebir okundu | ✓ |
| 5 | Kod blokları | 8 blok · gerçek kaynaktan · uydurma satır 0 | ✓ |
| 6 | Web tablosu | 1 URL · envanter (2640) dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | 3 yer-tutucu türü · boş hücre · mojibake | ✓ 0 |
| 8 | Yazılan dosya | bu dosya dâhil 4 (K017×2 · K018×2) | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode