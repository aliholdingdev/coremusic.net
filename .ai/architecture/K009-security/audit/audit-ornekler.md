---
node_id: K009-security/audit/audit-ornekler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/audit/audit-anayasa
evidence: "9 gerçek kod alıntısı — diskten okundu 2026-10-15 (Redactor·FileHandler·StructuredLogger·LoggerFactory·Events·SecurityHelper)"
validation: "Alıntılar Dosya·Satır; uydurma snippet 0; satır numaraları dosyalardan ölçüldü"
open_questions: "authz_denied üreten middleware okunmadı ⚠️ · secret_access olay kodu YOK ⚠️"
group: kod
---

# K009 · audit · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · parent: K009-security/audit · gerçek PHP alıntıları (filesystem read).

## §1 Alıntı Listesi

| # | Dosya | Satır | Konu |
|---|---|---|---|
| A-Ö-01 | shared/src/Log/Redactor.php | 28-31 | MASK + hassas anahtar listesi |
| A-Ö-02 | shared/src/Log/Redactor.php | 65-93 | redact() üçlü maskeleme |
| A-Ö-03 | shared/src/PageRouter/StructuredLogger.php | 61-72 | normalize + redact + error_log |
| A-Ö-04 | shared/src/Log/FileHandler.php | 207-235 | satır kurulumu + ikiz stream |
| A-Ö-05 | shared/src/Log/FileHandler.php | 169-184 | securityEvent (authz_denied vb.) |
| A-Ö-06 | shared/src/Log/FileHandler.php | 248-252 | append-only yazım |
| A-Ö-07 | shared/src/Events/Domain/UserLoggedInEvent.php | 26-31 / 55-60 | olay + payload (zaman damgası) |
| A-Ö-08 | shared/src/Events/Domain/UserLoggedOutEvent.php | 52-59 | sessionId payload uyarısı ⚠️ |
| A-Ö-09 | shared/src/Security/SecurityHelper.php | 42-55 | TEST_BYPASS audit kaydı |
| A-Ö-10 | shared/src/Log/LoggerFactory.php | 15-22 | logger singleton |

## §2 A-Ö-01 — REDACTOR: MASK VE HASSAS ANAHTARLAR

```php
// shared/src/Log/Redactor.php · satır 28-31
/** Maskeleme değeri. */
public const MASK = '[REDACTED]';

/** Hassas anahtar listesi. */
private const SENSITIVE_KEYS = 'auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie';
```

| Not | |
|---|---|
| Anahtar listesi case-insensitive (`/i`, satır 40) | anayasa §3 ile birebir |
| 11 anahtar | sızıntı sınıfı kapsanmış — `session` alt dizesi `session_id`'yi de kapsar |
| `sessionId` (camelCase) `\b` sınırına GİRMEZ | ⚠️ anayasa U-4 — Uyarı: UserLoggedOutEvent·56 |

## §3 A-Ö-02 — REDACTOR::REDACT (ÜÇLÜ MASKELEME)

```php
// shared/src/Log/Redactor.php · satır 65-93 (özet alıntı)
public static function redact(string $line): string
{
    if ($line === '') {
        return $line;
    }
    // 1) Anahtar-adı maskesi (key=value / key: value / "key":"value")
    $redacted = preg_replace_callback(
        self::KEY_VALUE_PATTERN,
        static fn (array $m): string => self::maskValue($m[1], $m[2]),
        $line
    );
    if ($redacted === null) {
        return $line;
    }
    // 2) 64-char hex/base64 hash maskesi — UUID'ler (8-4-4-4-12) korunur.
    $replaced = preg_replace(self::LONG_HASH_PATTERN, self::MASK, $redacted);
    // 3) E-posta maskesi — yerel kısım gizlenir, alan adı kalır (B-F-17).
    $withEmail = preg_replace_callback(
        self::EMAIL_PATTERN,
        static fn (array $m): string => '***@' . $m[1],
        $replaced ?? $redacted
    );
    return $withEmail ?? ($replaced ?? $redacted);
}
```

| Not | |
|---|---|
| `preg` null → ham satır (kayıp yok) | anayasa H-01 · surucu §9 |
| 3 katman: anahtar · hash · e-posta | anayasa §5 |
| Maskeleme yalnız YAZILACAK satırı etkiler | `Redactor.php·22` — geçmişe dokunulmaz |

## §4 A-Ö-03 — STRUCTUREDLOGGER: NORMALIZE → MASKELE → YAZ

```php
// shared/src/PageRouter/StructuredLogger.php · satır 61-72
$record = array_merge([
    'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
    'level'     => $level,
    'module'    => $module,
    'event'     => $event,
    'traceId'   => $this->traceId,
], $extra);

$payload = '[CoreMusic] ' . json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur.
error_log(Redactor::redact($payload));
```

| Not | |
|---|---|
| Zaman damgası ATOM (ISO-8601) | anayasa §7-3 |
| Maskeleme yazım ÖNCESİ | surucu §5 W-4 |
| `error_log` hedefi PHP ayarına bağlı (stderr/log) | ⚠️ hedef dosya bilinmiyor (UNKNOWN) |

## §5 A-Ö-04 — FILEHANDER::WRITE — İKİZ STREAM

```php
// shared/src/Log/FileHandler.php · satır 209-235 (çekirdek)
$timestamp = date('Y-m-d H:i:s');
$uri = $_SERVER['REQUEST_URI'] ?? '-';
$method = $_SERVER['REQUEST_METHOD'] ?? '-';
$ip = $_SERVER['REMOTE_ADDR'] ?? '-';
$trace = self::$traceId !== '' ? ' [trace=' . self::$traceId . ']' : '';
// ...
$line = "[{$timestamp}] [{$logLevel}] [{$method} {$uri}] [{$ip}]{$trace} {$message}{$contextStr}" . PHP_EOL;

// Merkezi redaction (§17 Edge Case 3): hassas alanlar [REDACTED] olur;
// e-postalar maskelenir (B-F-17: PII logda düz metin durmasın).
$line = Redactor::redact($line);

$this->appendToFile('coremusic_php_errors.log', $line);

// GÜVENLİK AKIŞI (yeni dosya — kullanıcı talebi 2026-10-07): auth + security
// olayları AYRİ stream'e de yazılır; prod'da dâhil her zaman aktif
if ($securityStream) {
    $this->appendToFile('coremusic_php_security.log', $line);
}
```

| Not | |
|---|---|
| ip + uri + method + trace: kim/neye/nereden | denetim izi çekirdeği |
| security.log prod'da dâhil aktif | audit tail'i (FileHandler.php·24-25 dokümanı) |
| Aynı satır 2 dosyada | olay sayımı bozulmaz (anayasa §6) |

## §6 A-Ö-05 — SECURITYEVENT (YETKİ REDDİ / CSRF / BYPASS)

```php
// shared/src/Log/FileHandler.php · satır 169-184
public function securityEvent(string $event, array $context = []): void
{
    $message = "[SECURITY] {$event}";
    foreach (['ip', 'uri', 'origin', 'role', 'code', 'reason', 'path'] as $key) {
        if (!empty($context[$key])) {
            $message .= " {$key}={$context[$key]}";
        }
    }
    $level = match ($event) {
        'origin_blocked', 'csrf_invalid', 'authz_denied', 'rate_limited', 'bypass_activated' => 'warning',
        default => 'warning',
    };
    $this->logAt($level, $message, $context, securityStream: true);
}
```

| Not | |
|---|---|
| authz_denied: uri + role + code | yetki reddi izi — surucu §6 |
| Tüm securityEvent'ler `securityStream: true` | ikiz stream |
| Çağrıran middleware kodu okunmadı | ⚠️ KANIT YOK (surucu U-3) |

## §7 A-Ö-06 — APPEND-ONLY YAZIM (LOCK_EX)

```php
// shared/src/Log/FileHandler.php · satır 248-252
private function appendToFile(string $filename, string $content): void
{
    $filepath = $this->logDir . '/' . $filename;
    @file_put_contents($filepath, $content, FILE_APPEND | LOCK_EX);
}
```

| Not | |
|---|---|
| `FILE_APPEND` — append-only kanıtı | anayasa §4-1 |
| `LOCK_EX` — eşzamanlı bozulma önlenir | anayasa §4-2/§9-5 |
| `@` suppress — yazma hatası sessiz | ⚠️ anayasa H-02 / surucu U-4 |

## §8 A-Ö-07 — USERLOGGEDINEVENT: OLAY + ZAMAN DAMGASI

```php
// shared/src/Events/Domain/UserLoggedInEvent.php · satır 26-31
public function __construct(
    private readonly string $userId,
    private readonly string $ipAddress,
    private readonly string $userAgent
) {
    $this->occurredOn = new \DateTimeImmutable();
}

// · satır 55-60 (payload)
return [
    'userId' => $this->userId,
    'ipAddress' => $this->ipAddress,
    'userAgent' => $this->userAgent,
    'timestamp' => $this->occurredOn->format('c'),
];
```

| Not | |
|---|---|
| occurredOn constructor'da — olay ANI | anayasa A-09 |
| `format('c')` ISO-8601 | anayasa §7-2 |
| payload'da parola/sır YOK | anayasa §3 |

## §9 A-Ö-08 — USERLOGGEDEVENT: SESSIONID UYARISI ⚠️

```php
// shared/src/Events/Domain/UserLoggedOutEvent.php · satır 52-58 (payload)
return [
    'userId' => $this->userId,
    'sessionId' => $this->sessionId,
    'timestamp' => $this->occurredOn->format('c'),
];
```

| Not | |
|---|---|
| `sessionId` alanı Redactor key-pattern'e girmez (`\b session \b` sınırı, Redactor.php·40) | ⚠️ VERIFICATION REQUIRED — anayasa U-4 |
| Bu payload Redactor'dan geçiyor mu? | ⚠️ KANIT YOK — event dispatcher akışı okunmadı |
| Olay adı `user.logged_out` | `UserLoggedOutEvent.php·38` |

## §10 A-Ö-09 — TEST BYPASS AUDIT KAYDI

```php
// shared/src/Security/SecurityHelper.php · satır 42-54
public static function logTestBypass(string $context, string $file, int $line): void
{
    // Düzeltme (2026-10-07): dirname(...,4) C:/www'a (repo DIŞINA) yazıyordu
    // — 3841 satır yanlış yere birikti. Repo kökü = dirname(...,3).
    $logFile = dirname(__DIR__, 3) . '/coremusic_php_errors.log';
    $message = sprintf(
        "[%s] [TEST_BYPASS] context=%s file=%s line=%d\n",
        date('d-M-Y H:i:s e'),
        $context,
        basename($file),
        $line
    );
    @file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
}
```

| Not | |
|---|---|
| Her aktive edilişte mutlak kayıt | anayasa §12-8 |
| append + lock | anayasa §4-3 |
| `date('... e')` timezone adı dâhil | anayasa §7-5 |
| Redactor'dan GEÇMEZ (doğrudan yazım) | ⚠️ — içerikte sır yok (context=file/line) |

## §11 A-Ö-10 — LOGGERFACTORY (SINGLETON)

```php
// shared/src/Log/LoggerFactory.php · satır 15-22
public static function getInstance(string $logDir = '', string $level = 'info'): FileHandler
{
    if (self::$logger === null) {
        $logDir = $logDir !== '' ? $logDir : self::resolveLogDir();
        self::$logger = new FileHandler($logDir, $level);
    }
    return self::$logger;
}
```

| Not | |
|---|---|
| varsayılan logDir = repo kökü | `LoggerFactory.php·29-33` |
| Tek FileHandler = tek yazıcı noktası | tutarlı append |
| Production'da yalnız error.log aktif (doküman) | `LoggerFactory.php·8-9` |

## §12 AUTH EVENT SEVİYE EŞLEMESİ

```php
// shared/src/Log/FileHandler.php · satır 155-160
$level = match ($event) {
    'login_success', 'register_success', 'logout' => 'info',
    'login_failed', 'register_failed', 'rate_limited' => 'warning',
    'login_error', 'register_error' => 'error',
    default => 'info',
};
```

| Not | |
|---|---|
| Olay sınıfı → seviye (anayasa §18) | başarı info, başarısız warning |
| `[AUTH]` öneki + email/ip/user_id/reason alanları | `FileHandler.php·141-153` |
| email alanı Redactor e-posta maskesinden geçer | `Redactor.php·56` |

## §13 ANTI-PATTERN TARAMASI (BU ÇOCUK)

| Anti-pattern | Sonuç |
|---|---|
| Ham parola loglama | 0 (Redactor `password`, satır 31) |
| Token loglama | 0 (`token` + çok-kelime auth, satır 40) |
| Log satırını düzenleme/silme | 0 (yalnız append, satır 251) |
| Redactor atlayan yazıcı | 1 ⚠️ (logTestBypass doğrudan yazıyor — §10 notu; içerikte sır yok) |
| Düz metin e-posta | 0 (maske, satır 56/88) |
| Eşzamanlı bozulma | 0 (LOCK_EX, satır 251/54) |

## §14 URL DOĞRULAMA KAYDI (BU DOSYANIN HAVUZU)

| # | URL | Tarih | Durum |
|---|---|---|---|
| 1 | https://cwe.mitre.org/data/definitions/778.html | 2026-10-15 | HTTP 200 |
| 2 | https://cwe.mitre.org/data/definitions/532.html | 2026-10-15 | HTTP 200 |

> Tam 9 URL'lük havuz `audit-web-arastirma.md` §1'de; bu dosyada yalnız kod-xref'i yapılan 2 CWE.

## §15 Ölçüm ve Kapanış

| Kalem | Değer |
|---|---|
| Alıntı | 10 (Dosya·Satır, diskten 2026-10-15) |
| Okunan kanıt dosyası | 7 (Redactor, FileHandler, LoggerFactory, StructuredLogger, UserLoggedInEvent, UserLoggedOutEvent, SecurityHelper) |
| Uydurma satır no | 0 |
| ⚠️ işaretli bulgu | 4 (sessionId maskesi · logTestBypass Redactor bypass · authz üretici kod · event payload akışı) |
| git commit | ATILMADI (subagent yasağı) |

## §16 Bağlantılar

| Bağ | Yön |
|---|---|
| [[audit-anayasa]] | altındaki maddeleri kanıtlar |
| [[audit-surucu]] | her adımın kod karşılığı |
| [[audit-dogrulama]] | AD-xx satırlarının kaynağı |
| [[index]] | künye |
| [[security-master]] | K009 üst düğüm |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
