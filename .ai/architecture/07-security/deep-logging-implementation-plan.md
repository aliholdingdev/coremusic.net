---
type: implementation-plan
category: logging
title: "CoreMusic — Deep Logging System Implementation Plan"
date: 2026-08-10
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Deep Logging System Implementation Plan

**Zorunlu Baglantilar:** [[architecture/07-security/deep-logging-system]] · [[AGENTS.md]] · [[brain.md]]

---

## 1. Proje Ozeti

| Metrik | Deger |
|--------|-------|
| Proje Adi | Deep Logging System |
| Tur | Sifirdan yazim (new build) |
| Toplam Faz | 10 |
| Tahmini Sure | 23 saat (3-4 is gunu) |
| Bagimlilik | monolog/monolog ^3.0, psr/log ^2.0/^3.0 |

---

## 2. Oncelikli Dosya Yapisi

```
C:\www\coremusic.net\
+-- shared/shared/src/
|   +-- Logging/
|       +-- Logger.php
|       +-- LogProcessor.php
|       +-- Channel/
|       |   +-- SecurityLogger.php
|       |   +-- PerformanceLogger.php
|       |   +-- ActivityLogger.php
|       |   +-- SystemLogger.php
|       +-- Handler/
|       |   +-- DailyRotatingFileHandler.php
|       |   +-- MySqlHandler.php
|       |   +-- ErrorFileHandler.php
|       +-- Formatter/
|       |   +-- LineFormatter.php
|       +-- Middleware/
|       |   +-- RequestLoggingMiddleware.php
|       |   +-- SecurityLoggingMiddleware.php
|       +-- Decorator/
|       |   +-- TimingDecorator.php
|       +-- Helper/
|           +-- Log.php
+-- logs/                               # runtime dosyalari
|   +-- app/
|   +-- security/
|   +-- performance/
|   +-- activity/
|   +-- system/
|   +-- error/
|   +-- debug/
|   +-- archive/
+-- .ai/
|   +-- .sql/mysql/coremusic_logs.sql    # 5 yeni tablo eklenecek
|   +-- architecture/07-security/deep-logging-system.md  # mevcut
+-- config/
    +-- logging.php                     # loglama konfigurasyonu
```

---

## 3. MySQL Tablo Detaylari (Faz 1)

**Dosya:** `.ai/.sql/mysql/coremusic_logs.sql` icine eklenecek 5 tablo:

| Tablo | Baslik | Satir | Index | Fulltext |
|-------|--------|-------|-------|----------|
| log_events | Genel olay logu | 20 | 7 | 1 (message) |
| log_security | Guvenlik olay logu | 28 | 7 | 1 (request_uri) |
| log_performance | Performans metrik logu | 22 | 6 | 1 (metric_name) |
| log_system | Sistem olay logu | 22 | 6 | 1 (message) |
| log_activity | Kullanici aktivite logu | 28 | 7 | 1 (entity_name) |

**Toplam:** 120 yeni satir SQL, 33 index, 3 fulltext

**Tablo ozellikleri:**
- Engine: InnoDB
- Charset: utf8mb4_unicode_ci
- PK: BIGINT UNSIGNED AUTO_INCREMENT
- Timestamp: TIMESTAMP(3) (milisaniye)
- UUID: CHAR(36) (correlation_id)
- User ID: BINARY(16) (mevcut tablolar ile tutarli)

---

## 4. PHP Dosya Detaylari (Faz 2-8)

### Faz 2: LogProcessor + Redaction

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/LogProcessor.php` | ~80 | Redaction patternlari, correlation ID, context enrichment |

**Redaction Patternleri (8 adet):**
1. password -> [REDACTED]
2. api_key -> [REDACTED]
3. token -> [REDACTED]
4. secret -> [REDACTED]
5. authorization -> [REDACTED]
6. cookie -> [REDACTED]
7. kart numarasi -> [REDACTED_CARD]
8. email -> [REDACTED_EMAIL]

**Redaction Disi Alanlar:** user_id, ip_address, correlation_id, created_at, request_method, response_code

### Faz 3: Logger + Channel Yapisi

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/Logger.php` | ~120 | PSR-3 interface, kanal yonetimi, log dagitimi |

**PSR-3 Methodlari:** emergency, alert, critical, error, warning, notice, info, debug, log

**Kanallar:** app, security, performance, activity, system

### Faz 4: Dosya Handler'lari

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/Handler/DailyRotatingFileHandler.php` | ~60 | Gunluk dosya rotasyonu |
| `shared/src/Logging/Handler/ErrorFileHandler.php` | ~30 | Sadece ERROR+ dosyasi |
| `shared/src/Logging/Formatter/LineFormatter.php` | ~40 | `[timestamp] [level] [category] [corr_id] message {json}` |

### Faz 5: MySQL Handler

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/Handler/MySqlHandler.php` | ~80 | 5 tabloya yazma, prepared statement |

**Tablo eslesmesi:**
- category=app -> log_events
- category=security -> log_security
- category=performance -> log_performance
- category=system -> log_system
- category=activity -> log_activity

### Faz 6: Static Helper

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/Helper/Log.php` | ~60 | Log::info, Log::security, Log::performance, Log::activity, Log::system |

### Faz 7: Middleware'ler

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/Middleware/RequestLoggingMiddleware.php` | ~70 | Her istek otomatik log, sure olcumu |
| `shared/src/Logging/Middleware/SecurityLoggingMiddleware.php` | ~50 | Guvenlik olayi tespiti ve loglama |

### Faz 8: AOP Decorator

| Dosya | Satir | Sorumluluk |
|-------|-------|------------|
| `shared/src/Logging/Decorator/TimingDecorator.php` | ~50 | __call magic ile otomatik sure olcumu |

---

## 5. Faz Detay Plani

### Faz 1: MySQL Tablolari (1 saat)

**Gorev:**
1. `coremusic_logs.sql` dosyasina 5 yeni tablo ekle
2. Mevcut versiyonu v7.0.0 -> v8.0.0 guncelle
3. Tablo sayisini 17 -> 22 guncelle
4. SQL syntax dogrulama yap

**Cikti:** Guncellenmis `.ai/.sql/mysql/coremusic_logs.sql`

**Bagimlilik:** Yok

### Faz 2: LogProcessor + Redaction (2 saat)

**Gorev:**
1. `shared/src/Logging/LogProcessor.php` olustur
2. 8 redaction pattern'i tanimla
3. Redaction disi alanlari tanimla
4. Correlation ID uretimi (UUID v4)
5. Context enrichment (memory_usage, peak_memory)

**Cikti:** `shared/src/Logging/LogProcessor.php` (~80 satir)

**Bagimlilik:** Yok

### Faz 3: Logger + Channel Yapisi (3 saat)

**Gorev:**
1. `shared/src/Logging/Logger.php` olustur
2. PSR-3 interface'i uygula
3. 5 kanal olustur (app, security, performance, activity, system)
4. Kanal bazli dagitim mantigi
5. LogProcessor entegrasyonu

**Cikti:** `shared/src/Logging/Logger.php` (~120 satir)

**Bagimlilik:** Faz 2

### Faz 4: Dosya Handler'lari (2 saat)

**Gorev:**
1. `shared/src/Logging/Handler/DailyRotatingFileHandler.php` olustur
2. `shared/src/Logging/Handler/ErrorFileHandler.php` olustur
3. `shared/src/Logging/Formatter/LineFormatter.php` olustur
4. Dosya rotasyonu mantigi (gunluk)
5. Dizin olusturma (logs/app/, logs/security/, vb.)

**Cikti:** 3 dosya (~130 satir toplam)

**Bagimlilik:** Faz 3

### Faz 5: MySQL Handler (2 saat)

**Gorev:**
1. `shared/src/Logging/Handler/MySqlHandler.php` olustur
2. 5 tablo icin INSERT prepared statement
3. Tablo eslesme mantigi (category -> tablo)
4. PDO baglanti yonetimi
5. Hata yonetimi (DB basarisizsa dosyaya fallback)

**Cikti:** `shared/src/Logging/Handler/MySqlHandler.php` (~80 satir)

**Bagimlilik:** Faz 1, Faz 3

### Faz 6: Static Helper (1 saat)

**Gorev:**
1. `shared/src/Logging/Helper/Log.php` olustur
2. Static methodlar: info, error, security, performance, activity, system
3. Logger instance yonetimi (setLogger)
4. Null-safe calls (?->)

**Cikti:** `shared/src/Logging/Helper/Log.php` (~60 satir)

**Bagimlilik:** Faz 3

### Faz 7: Middleware'ler (3 saat)

**Gorev:**
1. `shared/src/Logging/Middleware/RequestLoggingMiddleware.php` olustur
2. `shared/src/Logging/Middleware/SecurityLoggingMiddleware.php` olustur
3. Request once/loglama (baslangic, bitis, sure)
4. Response code loglama
5. Guvenlik olayi tespiti (CSRF, auth, rate limit)

**Cikti:** 2 dosya (~120 satir toplam)

**Bagimlilik:** Faz 3, Faz 6

### Faz 8: AOP Decorator (2 saat)

**Gorev:**
1. `shared/src/Logging/Decorator/TimingDecorator.php` olustur
2. __call magic method ile proxy pattern
3. Baslangic/bitis suresi olcumu
4. Basarili/basarisiz durum loglama
5. Hata durumunda exception yeniden firlatma

**Cikti:** `shared/src/Logging/Decorator/TimingDecorator.php` (~50 satir)

**Bagimlilik:** Faz 6

### Faz 9: Real-time Izleme (4 saat)

**Gorev:**
1. WebSocket server kurulumu (port 9743)
2. Dosya izleme (inotify/fswatch)
3. MySQL polling (her 5 sn)
4. Dashboard sayfasi (admin.coremusic.net/log-monitor)
5. Alarm esikleri (hata orani >%5, TTFB >2000ms)

**Cikti:** WebSocket server + Dashboard

**Bagimlilik:** Faz 5

### Faz 10: Testler + Dokumantasyon (3 saat)

**Gorev:**
1. LogProcessor unit test (redaction coverage %100)
2. Logger unit test (PSR-3 compliance)
3. MySQL handler integration test
4. Middleware integration test
5. README.md guncelleme

**Cikti:** Test dosyalari + guncellenmis dokumantasyon

**Bagimlilik:** Tum fazlar

---

## 6. Dosya Boyut TAHMINLERI

| Dosya | Tahmini Satir |
|-------|---------------|
| LogProcessor.php | 80 |
| Logger.php | 120 |
| DailyRotatingFileHandler.php | 60 |
| ErrorFileHandler.php | 30 |
| LineFormatter.php | 40 |
| MySqlHandler.php | 80 |
| Log.php (Helper) | 60 |
| RequestLoggingMiddleware.php | 70 |
| SecurityLoggingMiddleware.php | 50 |
| TimingDecorator.php | 50 |
| **Toplam** | **~640 satir PHP** |

---

## 7. Bagimliliklar

| Paket | Versiyon | Tur | Neden |
|-------|---------|-----|-------|
| monolog/monolog | ^3.0 | require | PSR-3 referans implementasyonu |
| psr/log | ^2.0 OR ^3.0 | require | Logger interface |
| ext-pdo | - | ext | MySQL baglantisi |
| ext-json | - | ext | JSON context |
| ext-mbstring | - | ext | String isleme |
| vimeo/psalm | ^5.0 | require-dev | Static analysis |
| phpunit/phpunit | ^11.0 | require-dev | Unit test |

---

## 8. Konfigurasyon Dosyasi

**Dosya:** `config/logging.php`

```php
<?php
return [
    'level' => env('LOG_LEVEL', 'INFO'),
    'file' => [
        'enabled' => true,
        'path' => __DIR__ . '/../logs/',
        'rotation' => 'daily',
    ],
    'database' => [
        'enabled' => true,
        'connection' => 'mysql',
        'prefix' => 'log_',
    ],
    'redaction' => [
        'enabled' => true,
        'patterns' => ['password', 'api_key', 'token', 'secret'],
    ],
    'realtime' => [
        'enabled' => false,
        'websocket_port' => 9743,
    ],
    'thresholds' => [
        'slow_query_ms' => 1000,
        'slow_api_ms' => 500,
        'memory_warning_percent' => 80,
        'error_rate_percent' => 5,
    ],
];
```

---

## 9. Middleware Pipeline Entegrasyonu

Mevcut pipeline:
```
OriginCheck -> Cors -> RateLimiter -> SecurityHeaders -> SessionManager -> Csrf -> BypassAuth -> Auth -> Permission -> Validation
```

Yeni pipeline:
```
OriginCheck -> Cors -> RateLimiter -> SecurityHeaders -> SessionManager -> Csrf -> BypassAuth -> Auth -> Permission -> Validation -> RequestLogging -> SecurityLogging
```

**Not:** Middleware sirasi degistirilmez (ADR-010/011/012/013/022). Yeni middleware'ler sadece sona eklenir.

---

## 10. Test Stratejisi

| Test Turu | Dosya | Hedef |
|-----------|-------|-------|
| Unit | LogProcessorTest.php | %100 redaction coverage |
| Unit | LoggerTest.php | PSR-3 compliance |
| Unit | LineFormatterTest.php | Format dogrulama |
| Unit | LogHelperTest.php | Static method test |
| Unit | TimingDecoratorTest.php | Sure olcum dogrulama |
| Integration | MySqlHandlerTest.php | DB write/read |
| Integration | RequestLoggingMiddlewareTest.php | otomatik loglama |
| E2E | RealtimeMonitorTest.php | WebSocket + Dashboard |

---

## 11. Riskler

| Risk | Olasilik | Etki | Azaltma |
|------|----------|------|---------|
| Performans (log write) | Orta | Yuksek | Async write, batch insert |
| MySQL buyuklugu | Yuksek | Orta | Partition, rotation |
| Redaction eksik | Dusuk | Yuksek | %100 test coverage |
| Disk alani | Orta | Yuksek | Monitoring + alert |

---

## 12. Onay Matrisi

| Faz | Onaylayici | Onay Turu |
|-----|------------|-----------|
| Faz 1 (SQL) | Data Engineer | SQL syntax review |
| Faz 2-6 (PHP) | Backend Architect | Code review |
| Faz 7-8 (Middleware) | Security Engineer | Security review |
| Faz 9 (Realtime) | DevOps Engineer | Deploy review |
| Faz 10 (Test) | QA Engineer | Coverage review |
| Tum fazlar | Vault Steward | Final onay |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
**Status:** DRAFT — Onay bekliyor
