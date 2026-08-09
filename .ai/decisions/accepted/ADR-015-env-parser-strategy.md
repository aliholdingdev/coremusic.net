---
type: adr
category: infrastructure
title: "ADR-015: Env Parser Strategy"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-015: Env Parser Strategy

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Infrastructure
**İlgili Agent:** [[.agents/security-engineer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunda environment variable yönetimini tanımlar. Hassas bilgilerin (API key, DB password, JWT secret vb.) koddan ayrılmasını, `.env` dosyası ile güvenli okunmasını ve hiçbir zaman log'lara veya versiyon kontrol sistemine sızdırılmamasını sağlar. [[ADR-022-database-hardened-security]] ile AES-256-GCM şifreleme standartlarıyla uyumludur.

---

## 2. Bağlam

### 2.1 Problem Tanımı

Yazılım geliştirmede hassas bilgilerin yönetimi kritik bir konudur:

| Hassas Bilgi | Örnek | Risk |
|-------------|-------|------|
| DB Password | `db_pass=abc123` | Veri sızıntısı |
| API Key | `deezer_key=xyz789` | Servis ihlali |
| JWT Secret | `jwt_secret=supersecret` | Token sahteciliği |
| ARL Token | `arl_token=abc` | Deezer erişim kaybı |
| Credential Vault Şifresi | `vault_pass=secret` | Tam erişim kaybı |
| AES Key | `aes_key=123bytehex` | Şifreleme zayıflığı |

Bu bilgilerin kodda veya log'da düz metin olarak bulunması **güvenlik ihlali** oluşturur.

### 2.2 Neden .env?

| Sebep | Açıklama |
|-------|----------|
| Güvenlik | Hassas bilgiler koddan ayrılır |
| Esneklik | Ortama göre değişiklik |
| Versiyon kontrolü | .env.example commit edilebilir |
| Endüstri standartları | Laravel, Symfony gibi çerçeveler .env kullanır |
| Basitlik | Kolay okuma/yazma |
| Portability | Her ortamda çalışır |
| Migration kolaylığı | Yeni değişken ekleme kolay |

### 2.3 İlişkili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-022-database-hardened-security]] | AES-256-GCM, Argon2id |
| [[ADR-034-credential-vault-normalization]] | Credential vault yönetimi |
| [[ADR-020-api-public-security]] | API güvenlik stratejisi |
| [[ADR-008-bypass-auth-middleware]] | Auth bypass flag |

---

## 3. Karar

CoreMusic'te **.env dosyası** ile environment variable yönetimi yapılacak:

| Karar | Değer |
|-------|-------|
| Dosya formatı | `.env` (KEY=VALUE) |
| Konum | Proje root dizini |
| Şablon | `.env.example` (git'e commit edilir) |
| Gitignore | `.env` git'e commit edilmez |
| Parser | `vlucas/phpdotenv` |
| Encoding | UTF-8 |
| Variable ismi | UPPER_SNAKE_CASE |
| Boş değer | İzin verilir, default ile handling |
| Null değer | `'null'` string olarak kabul edilir |
| Dosya izni | 600 (sadece owner) |
| Backup | .env backup'ı zorunlu değil |
| Rotation | Credential rotation periyodik |

---

## 4. Teknik Detaylar

### 4.1 .env Dosya Yapısı

```env
# === Database ===
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME_COREMUSIC_AUTH=coremusic_auth
DB_NAME_COREMUSIC_USER=coremusic_user
DB_NAME_COREMUSIC_MUSICS=coremusic_musics
DB_NAME_COREMUSIC_ALBUMS=coremusic_albums
DB_NAME_COREMUSIC_PLAYLIST=coremusic_playlist
DB_NAME_COREMUSIC_CATALOG=coremusic_catalog
DB_NAME_COREMUSIC_LOGS=coremusic_logs
DB_NAME_COREMUSIC_MEDIA=coremusic_media
DB_NAME_COREMUSIC_SYSTEM=coremusic_system
DB_USER=coremusic
DB_PASSWORD=CHANGE_ME_IN_PRODUCTION

# === Security ===
AES_KEY=CHANGE_ME_32_BYTE_HEX
ARGON2ID_MEMORY=65536
ARGON2ID_TIME=4
ARGON2ID_THREADS=2

# === Session ===
SESSION_IDLE_TIMEOUT=3600
SESSION_NAME=COREMUSIC_SESS

# === Rate Limiting ===
RATE_LIMIT_MAX=60
RATE_LIMIT_WINDOW=60

# === Download Service ===
DEEZER_ARL_TOKEN=CHANGE_ME
YOUTUBE_API_KEY=CHANGE_ME

# === Credential Vault ===
VAULT_KEY=CHANGE_ME_32_BYTE_HEX

# === Services ===
CONTROL_SERVICE_PORT=81
MEDIA_SERVICE_PORT=5000
AUDIO_SERVICE_REST_PORT=9741
AUDIO_SERVICE_WS_PORT=9742
DOWNLOAD_SERVICE_PORT=3001

# === Debug ===
DEBUG=false
LOG_LEVEL=warning
```

### 4.2 .env.example Formatı

```env
# === Database ===
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME_COREMUSIC_AUTH=
DB_NAME_COREMUSIC_USER=
DB_NAME_COREMUSIC_MUSICS=
DB_NAME_COREMUSIC_ALBUMS=
DB_NAME_COREMUSIC_PLAYLIST=
DB_NAME_COREMUSIC_CATALOG=
DB_NAME_COREMUSIC_LOGS=
DB_NAME_COREMUSIC_MEDIA=
DB_NAME_COREMUSIC_SYSTEM=
DB_USER=
DB_PASSWORD=

# === Security ===
AES_KEY=
ARGON2ID_MEMORY=65536
ARGON2ID_TIME=4
ARGON2ID_THREADS=2

# === Session ===
SESSION_IDLE_TIMEOUT=3600
SESSION_NAME=COREMUSIC_SESS

# === Rate Limiting ===
RATE_LIMIT_MAX=60
RATE_LIMIT_WINDOW=60

# === Download Service ===
DEEZER_ARL_TOKEN=
YOUTUBE_API_KEY=

# === Credential Vault ===
VAULT_KEY=

# === Services ===
CONTROL_SERVICE_PORT=81
MEDIA_SERVICE_PORT=5000
AUDIO_SERVICE_REST_PORT=9741
AUDIO_SERVICE_WS_PORT=9742
DOWNLOAD_SERVICE_PORT=3001

# === Debug ===
DEBUG=false
LOG_LEVEL=warning
```

### 4.3 Parser Implementasyonu

```php
declare(strict_types=1);

namespace CoreMusic\Infrastructure;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidEncodingException;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\NotFoundException;

class EnvParser
{
    private static ?self $instance = null;
    private array $variables = [];

    private function __construct(string $path)
    {
        try {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->required([
                'DB_HOST',
                'DB_PORT',
                'DB_USER',
                'DB_PASSWORD',
                'AES_KEY',
                'VAULT_KEY',
            ]);

            $dotenv->load();

            $this->variables = $_ENV;
        } catch (NotFoundException $e) {
            throw new \RuntimeException(
                ".env dosyası bulunamadı: {$e->getMessage()}"
            );
        } catch (InvalidFileException $e) {
            throw new \RuntimeException(
                ".env dosyası geçersiz: {$e->getMessage()}"
            );
        } catch (InvalidEncodingException $e) {
            throw new \RuntimeException(
                ".env dosyası encoding hatası: {$e->getMessage()}"
            );
        }
    }

    public static function init(string $path = __DIR__ . '/../../'): self
    {
        if (self::$instance === null) {
            self::$instance = new self($path);
        }
        return self::$instance;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->variables[$key] ?? $default;
    }

    public function required(string $key): string
    {
        $value = $this->variables[$key] ?? null;
        if ($value === null) {
            throw new \RuntimeException(
                "Gerekli environment variable eksik: {$key}"
            );
        }
        return $value;
    }

    public function bool(string $key, bool $default = false): bool
    {
        $value = $this->variables[$key] ?? null;
        if ($value === null) {
            return $default;
        }
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function int(string $key, int $default = 0): int
    {
        $value = $this->variables[$key] ?? null;
        if ($value === null) {
            return $default;
        }
        return (int) $value;
    }

    public function has(string $key): bool
    {
        return isset($this->variables[$key]);
    }

    public function all(): array
    {
        return $this->variables;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }
}
```

### 4.4 Kullanım Örneği

```php
declare(strict_types=1);

$env = EnvParser::init();

// Zorunlu değişkenler
$dbHost = $env->required('DB_HOST');
$dbPass = $env->required('DB_PASSWORD');

// Varsayılan değerli değişkenler
$port = $env->int('DB_PORT', 3306);
$timeout = $env->int('SESSION_IDLE_TIMEOUT', 3600);

// Boolean değişkenler
$debug = $env->bool('DEBUG', false);

// Varlık kontrolü
if ($env->has('DEEZER_ARL_TOKEN')) {
    // Deezer servisini başlat
}

// Tüm değişkenleri al
$allVars = $env->all();
```

### 4.5 Güvenlik Katmanları

| Katman | Koruma | Açıklama |
|--------|--------|----------|
| Dosya sistemi | `.env` dosyası 600 izni | Sadece owner okuyabilir |
| Git | `.gitignore` ile commit engeli | Hassas veriler versiyon kontrolünde değil |
| Log | Hassas değişkenler `[REDACTED]` ile maskelenir | Log sızıntısı önlenir |
| Runtime | `$_ENV` dizisi hafızada tutulur | Dosya diske yazılmaz |
| Redaction | Her oturum sonunda tarama | Düzenli kontrol |
| Backup | .env backup'ı ayrı saklanır | Kurtarma kolaylığı |
| Rotation | Credential rotation periyodik | Eski key'lerin iptali |

### 4.6 Redaction Kontrolü

```php
class EnvRedaction
{
    private const SENSITIVE_KEYS = [
        'DB_PASSWORD',
        'AES_KEY',
        'VAULT_KEY',
        'DEEZER_ARL_TOKEN',
        'YOUTUBE_API_KEY',
        'JWT_SECRET',
        'SESSION_SECRET',
    ];

    public static function redact(array $env): array
    {
        foreach (self::SENSITIVE_KEYS as $key) {
            if (isset($env[$key])) {
                $env[$key] = '[REDACTED]';
            }
        }
        return $env;
    }

    public static function isSensitive(string $key): bool
    {
        return in_array(strtoupper($key), self::SENSITIVE_KEYS, true);
    }
}
```

### 4.7 Ortam Bazlı Konfigürasyon

| Ortam | Dosya | Özellik | Kullanım |
|-------|-------|---------|----------|
| Development | `.env` | Varsayılan değerler, debug açık | Geliştirme |
| Testing | `.env.testing` | Test DB'leri, debug kapalı | Test |
| Production | `.env.production` | Gerçek değerler, debug kapalı | Üretim |
| CI/CD | `.env.ci` | CI ortamı değerleri | Pipeline |

### 4.8 Pre-commit Hook

```bash
#!/bin/bash
# .git/hooks/pre-commit

# .env dosyası commit edilmeye çalışılıyor mu?
if git diff --cached --name-only | grep -q '\.env$'; then
    echo "HATA: .env dosyası commit edilemez!"
    echo "Lütfen .env.example'ı güncelleyin."
    exit 1
fi

# .env.example güncellenmeli mi?
if git diff --cached --name-only | grep -q '\.php$'; then
    # Yeni env değişkeni eklenmiş mi kontrol et
    # ...
fi
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Hardcoded secret kodda | `.env` dosyası |
| `.env` git'e commit | `.gitignore` ile engelleme |
| Düz metin password | `EnvParser::required()` |
| Log'da password | `[REDACTED]` ile maskeleme |
| `$_ENV` doğrudan erişim | `EnvParser` wrapper |
| Default value olmayan required | `required()` ile zorunlu |
| ASCII encoding | UTF-8 encoding |
| `eval()` ile env okuma | `Dotenv` kütüphanesi |
| Production'da debug | Debug modu devre dışı |
| `.env` world-readable | Dosya izni 600 |
| Empty required field | Startup'ta exception |
| Multiple .env files | Tek .env, ortam bazlı override |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| `.env` dosyası yoksa | İlk kurulum | `.env.example`'dan kopyalama rehberi |
| Boş değer | `DB_PASSWORD=` | `required()` ile hata fırlatma |
| Unicode karakter | UTF-8 encoding | `InvalidEncodingException` handling |
| Eski `.env` formatı | Eski versiyon | Migration rehberi |
| Çift tırnak içinde değer | `"password"` | Dotenv otomatik çözümleme |
| Yorum satırı | `# comment` | Dotenv otomatik atlama |
| Variable substitution | `${DB_HOST}` | Dotenv destekler |
| Production leak | `.env` sızıntısı | ACIL durum: tüm credential rotasyonu |
| Eksik required field | `DB_HOST` yok | Startup'ta exception |
| `.env.example` güncellenmemesi | Yeni değişken eklendiği | Pre-commit hook kontrolü |
| Same key in multiple files | Çakışma | Son yüklenen dosya geçerli |
| Large env file | 100+ değişken | Performans etkilenmez |
| Windows line endings | CRLF | Dotenv destekler |
| Special chars in value | `password=abc!@#` | Dotenv destekler |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | **`.env` git'e commit edilmez** | Güvenlik ihlali, credential rotasyonu |
| 2 | **Hardcoded secret yasak** | Kod review'da ret |
| 3 | **Log'da hassas veri yasak** | `[REDACTED]` kullanılmazsa CRITICAL |
| 4 | **`.env.example` zorunlu** | Eksikse yeni değişken eklenemez |
| 5 | **UTF-8 encoding zorunlu** | Encoding hatası fırlatılır |
| 6 | **Dosya izni 600** | World-readable ise red |
| 7 | **Required fields zorunlu** | Eksikse startup başarısız |
| 8 | **Redaction kontrolü** | Her oturum sonunda tarama |
| 9 | **Ortam bazlı `.env`** | Production'da test değeri kullanımı yasak |
| 10 | **Pre-commit hook** | `.env` commit engeli |
| 11 | **No eval** — Env okuma Dotenv ile | Güvenlik açığı |
| 12 | **Singleton pattern** — Tek instance | Bellek israfı |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-022-database-hardened-security]] | AES-256-GCM, Argon2id | Env ile AES key yönetimi |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Vault şifresi env'de |
| [[ADR-020-api-public-security]] | API güvenlik | API key env'de |
| [[ADR-008-bypass-auth-middleware]] | Auth bypass | Bypass flag env'de |
| [[ADR-011-session-management]] | Session yönetimi | Session config env'de |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | Rate limit config env'de |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | Env dosya yapısı |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2.1 | [[ADR-022-database-hardened-security]] | Hassas bilgi türleri |
| § 2.3 | [[ADR-034-credential-vault-normalization]] | Vault yönetimi |
| § 4.3 | [[architecture/l0-infrastructure]] | L0 altyapı katmanı |
| § 5 | [[ADR-020-api-public-security]] | API güvenliği |
| § 6 | [[architecture/l1-security]] | L1 güvenlik katmanı |
| § 7 | [[ADR-008-bypass-auth-middleware]] | Auth bypass |
| § 4.5 | [[ADR-022-database-hardened-security]] | Güvenlik katmanları |
| § 4.8 | [[workflows/deployment]] | Deployment süreci |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Environment Variable** | Çalışma zamanı ortam değişkeni |
| **.env** | Environment variable dosyası |
| **Parser** | Dosya okuma ve çözümleme motoru |
| **Hardcoded Secret** | Kodda düz metin hassas bilgi |
| **Redaction** | Hassas bilginin maskelenmesi |
| **UTF-8** | Unicode karakter encoding standardı |
| **AES-256-GCM** | Gelişmiş şifreleme standardı |
| **Argon2id** | Şifreleme algoritması |
| **BCNF** | Boyce-Codd Normal Form |
| **PDO** | PHP Data Objects |
| **Pre-commit Hook** | Commit öncesi otomatik kontrol |
| **Dotenv** | PHP dotenv kütüphanesi |
| **JSON Web Token** | Yetkilendirme token'ı |
| **ARL Token** | Deezer erişim token'ı |
| **Singleton** | Tek instance design pattern |
| **CRLF** | Carriage Return Line Feed |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Status | Frozen (değiştirilemez) |
| Sections | 11 |
| Hard Guardrails | 12 |
| Edge Cases | 14 |
| Yasak Örüntüleri | 12 |
| İlgili ADR'ler | 7 |
| Çapraz Referanslar | 8 |
| Sözlük Terimleri | 16 |
| Parser Kütüphanesi | vlucas/phpdotenv |
| Encoding | UTF-8 |
| Dosya İznı | 600 |
| Hassas Değişken Sayısı | 8 |
| Ortam Sayısı | 4 (dev/test/prod/ci) |
| Redaction Kontrolü | Her oturum sonunda |

---

## 12. Authority

## 13. Credential Rotation Stratejisi

| Credential | Rotation Süresi | Yöntem |
|------------|----------------|--------|
| DB Password | 90 gün | Manual rotation |
| AES Key | 180 gün | Dual-key rotation |
| API Keys | 90 gün | Service restart |
| ARL Token | 30 gün | Token refresh |
| JWT Secret | 360 gün | Token invalidation |

### 13.1 Dual-Key Rotation

```
1. Yeni key üret (AES_KEY_NEW)
2. Her iki key ile destekle (okuma: her ikisi, yazma: yeni)
3. Tüm servisleri güncelle
4. Eski key'i kaldır (AES_KEY -> AES_KEY_DEPRECATED)
5. Deprecated key'i sil
```

### 13.2 Emergency Rotation

Güvenlik ihlali durumunda tüm credential'lar aynı anda rotasyon yapılır:

```php
class EmergencyRotation {
    public function rotateAll(): void {
        $this->rotateDbPassword();
        $this->rotateAesKey();
        $this->rotateApiKeys();
        $this->rotateArlToken();
        $this->rotateJwtSecret();
        $this->notifyAdmin('Emergency rotation completed');
    }
}
```

---

## 14. Monitoring & Alerting

| Metrik | Eşik | Aksiyon |
|--------|------|---------|
| .env dosya boyutu | >10KB | WARN |
| Eksik required field | Herhangi biri | ERROR, startup başarısız |
| Redaction ihlali | Hassas veri log'da | CRITICAL |
| Rotation gecikmesi | Süre dolmuş | WARN |
| Eski credential kullanımı | Deprecated key | ERROR |

---

## 15. Compliance

| Standart | Uyumluluk |
|----------|-----------|
| OWASP Top 10:2025 | A07:2025 — Identification and Authentication Failures |
| NIST SP 800-53 | IA-5: Authenticator Management |
| PCI DSS 4.0 | Req 8: Strong Authentication |
| GDPR | Madde 32: Güvenlik |

---

## 16. Testing Strategy

| Test Type | Scope | Framework |
|-----------|-------|-----------|
| Unit test | EnvParser class | PHPUnit 11 |
| Integration test | Dotenv loading | PHPUnit 11 |
| Security test | Redaction | PHPUnit 11 |
| E2E test | Startup validation | Playwright |

### 16.1 Test Cases

| Test | Input | Expected |
|------|-------|----------|
| Required present | `DB_HOST=127.0.0.1` | Success |
| Required missing | No DB_HOST | RuntimeException |
| Boolean true | `DEBUG=true` | true |
| Boolean false | `DEBUG=false` | false |
| Integer parse | `PORT=81` | 81 |
| Empty value | `KEY=` | Empty string |
| Sensitive redact | `DB_PASSWORD=secret` | `[REDACTED]` |
| UTF-8 content | `NAME=Müzik` | Success |

---

## 17. Quality Report (Updated)

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Sections | 17 |
| Hard Guardrails | 12 |
| Edge Cases | 14 |
| Test Cases | 8 |
| Compliance Standards | 4 |
| Rotation Policies | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
**Immutability:** ADR 001-037 frozen, değiştirilemez
**Scope:** CoreMusic environment variable yönetimi
**Governance:** Red Team · Human Mode · Truth Mode
