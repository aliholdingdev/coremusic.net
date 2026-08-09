---
type: adr
category: database
title: "ADR-003: Multi-Database 9 BCNF Architecture"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-003: Multi-Database 9 BCNF Architecture

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformunun veritabanı mimarisini tanımlar. 9 izole BCNF (Boyce-Codd Normal Form) veritabanı kararı, veri izolasyonu, güvenlik, performans ve ölçeklenebilirlik prensiplerine dayanır. Bu belge tek başına yeterli bilgi içermeli, başka bir kaynak okumadan CoreMusic'in veritabanı stratejisi tam olarak anlaşılmalıdır.

CoreMusic; araçta, evde ve profesyonel stüdyoda müzik dinlemek, müzik açmak ve müzik yönetmek için tasarlanmış bir medya platformudur. Bu platformun veritabanı mimarisi, 7 backend servis ve 10 panel tarafından kullanılan verilerin yüksek performansta, güvenli ve izole şekilde saklanmasını sağlamak zorundadır.

---

## 2. Bağlam

### 2.1 İş Problemi

CoreMusic platformu çeşitli veri türleri içerir:

| Veri Türü | Örnek | Hassasiyet |
|-----------|-------|------------|
| Kimlik doğrulama | Kullanıcı adı, şifre hash, roller | CRITICAL |
| Kullanıcı profilleri | İsim, tercihler, cinsiyet | HIGH |
| Müzik metadata | Şarkı adı, sanatçı, albüm, tür | MEDIUM |
| Albüm koleksiyonları | Albüm listeleri, kapak görselleri | MEDIUM |
| Çalma listeleri | Kullanıcı ve AI listeleri | MEDIUM |
| İndirme kuyrukları | Deezer/YouTube kuyrukları | LOW |
| Uygulama logları | Audit trail, hata logları | LOW |
| Medya metadata | Dosya boyutu, format, süre | MEDIUM |
| Sistem konfigürasyonu | Servis ayarları, portlar | HIGH |

### 2.2 Teknik Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ORM yasak | ADR-002 ile PDO prepared statement zorunlu |
| SELECT * yasak | Açık sütun listesi zorunlu |
| BCNF zorunlu | Her veritabanı BCNF formatında olmalı |
| Soft delete | `is_deleted = 0` ile silme |
| Prepared statement | SQL injection önleme |
| Snake_case naming | Tablo ve sütun adları snake_case |

### 2.3 Mevcut Durum

CoreMusic henüz üretim aşamasında değildir. MVP (Minimum Viable Product) fazındadır. Bu karar, gelecekteki tüm geliştirme çalışmalarını yönlendirecek temel mimari karardır.

### 2.4 İlişkili Kararlar

| ADR | İlişki |
|-----|--------|
| ADR-002 | PDO mandatory, ORM yasak |
| ADR-014 | Multi-DB migration stratejisi |
| ADR-022 | DB hardened security (Argon2id, AES-256-GCM) |
| ADR-033 | SQL normalization stratejisi |
| ADR-040 | 9 BCNF DB otoritesi |
| ADR-041 | DB normalization ek bilgi |
| ADR-050 | Multi-DB sync stratejisi |

---

## 3. Karar

CoreMusic'te **9 izole BCNF veritabanı** kullanılacak. Her veritabanı bağımsız bir MySQL/MariaDB instance'ında çalışacak. Veritabanları arası JOIN işlemi yapılmayacak; uygulama seviyesinde join uygulanacaktır.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| Veritabanı sayısı | 9 |
| Normalizasyon seviyesi | BCNF (Boyce-Codd Normal Form) |
| Veritabanı motoru | MySQL 9 / MariaDB 10.11+ |
| Bağlantı | PDO (prepared statement zorunlu) |
| ORM | ❌ YASAK (ADR-002) |
| Cross-DB JOIN | ❌ YASAK |
| Uygulama seviyesi join | ✅ Zorunlu |
| Soft delete | ✅ `is_deleted = 0` |
| Naming | snake_case (tablo ve sütun) |

### 3.2 İzolasyon Prensibi

Her veritabanı kendi bağlamında tamamen izoledir:

```
┌─────────────────────────────────────────────────┐
│ coremusic_auth                                  │
│ ├── users (id, username, password_hash, role)   │
│ ├── roles (id, name, permissions)               │
│ └── sessions (id, user_id, token, expires_at)   │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ coremusic_user                                  │
│ ├── profiles (id, user_id, display_name, gender)│
│ ├── preferences (id, user_id, theme, language)  │
│ └── history (id, user_id, action, timestamp)    │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│ coremusic_musics                                │
│ ├── songs (id, title, artist_id, genre_id)      │
│ ├── artists (id, name, bio)                     │
│ └── genres (id, name, description)              │
└─────────────────────────────────────────────────┘
```

---

## 4. Teknik Detaylar

### 4.1 Veritabanı Listesi

| # | Veritabanı | Amaç | Ana Tablolar | Tahmini Satır |
|---|------------|------|-------------|---------------|
| 1 | `coremusic_auth` | Kimlik doğrulama, yetkilendirme | users, roles, sessions, permissions | 10K+ |
| 2 | `coremusic_user` | Kullanıcı profilleri, tercihler | profiles, preferences, history | 10K+ |
| 3 | `coremusic_musics` | Müzik metadata | songs, artists, genres, albums_metadata | 1M+ |
| 4 | `coremusic_albums` | Albüm koleksiyonları | albums, album_tracks, album_art | 100K+ |
| 5 | `coremusic_playlist` | Çalma listeleri | playlists, playlist_tracks, ai_playlists | 500K+ |
| 6 | `coremusic_catalog` | İndirme kuyrukları | download_queues, service_status, catalog | 100K+ |
| 7 | `coremusic_logs` | Uygulama logları | app_logs, audit_trail, error_logs | 10M+ |
| 8 | `coremusic_media` | Medya dosyası metadata | media_files, media_metadata, thumbnails | 500K+ |
| 9 | `coremusic_system` | Sistem konfigürasyonu | settings, services, ports, feature_flags | 1K+ |

### 4.2 coremusic_auth Şeması

```sql
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    permissions JSON,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.3 coremusic_user Şeması

```sql
CREATE TABLE profiles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    display_name VARCHAR(100),
    gender ENUM('male', 'female', 'neutral') DEFAULT 'neutral',
    avatar_url VARCHAR(500),
    bio TEXT,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_gender (gender)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE preferences (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    device_type ENUM('desktop', 'mobile', 'tablet', 'car', 'studio') DEFAULT 'desktop',
    theme VARCHAR(50) DEFAULT 'default',
    language VARCHAR(10) DEFAULT 'tr',
    notifications_enabled TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_device_type (device_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.4 coremusic_musics Şeması

```sql
CREATE TABLE songs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_id INT UNSIGNED NOT NULL,
    genre_id INT UNSIGNED,
    album_id INT UNSIGNED,
    duration_ms INT UNSIGNED,
    file_path VARCHAR(500),
    file_format ENUM('flac', 'mp3', 'wav', 'aac') DEFAULT 'flac',
    bitrate INT UNSIGNED,
    sample_rate INT UNSIGNED DEFAULT 48000,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_title (title),
    INDEX idx_artist_id (artist_id),
    INDEX idx_genre_id (genre_id),
    INDEX idx_album_id (album_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE artists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    bio TEXT,
    image_url VARCHAR(500),
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE genres (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT UNSIGNED,
    is_deleted TINYINT(1) DEFAULT 0,
    INDEX idx_name (name),
    INDEX idx_parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.5 Uygulama Seviyesi Join Örneği

Cross-DB JOIN yasak olduğundan, uygulama seviyesinde join yapılmalıdır:

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Repository;

use PDO;

class MusicRepository
{
    private PDO $authDb;
    private PDO $userDb;
    private PDO $musicsDb;

    public function __construct(
        PDO $authDb,
        PDO $userDb,
        PDO $musicsDb
    ) {
        $this->authDb = $authDb;
        $this->userDb = $userDb;
        $this->musicsDb = $musicsDb;
    }

    public function getUserMusicPreferences(int $userId): array
    {
        $stmt = $this->authDb->prepare(
            'SELECT id, username, email FROM users WHERE id = :id AND is_deleted = 0'
        );
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [];
        }

        $stmt = $this->userDb->prepare(
            'SELECT theme, language, device_type FROM preferences WHERE user_id = :user_id AND is_deleted = 0'
        );
        $stmt->execute([':user_id' => $userId]);
        $preferences = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->musicsDb->prepare(
            'SELECT s.title, a.name as artist_name, g.name as genre_name
             FROM songs s
             JOIN artists a ON s.artist_id = a.id
             JOIN genres g ON s.genre_id = g.id
             WHERE s.is_deleted = 0'
        );
        $stmt->execute();
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'user' => $user,
            'preferences' => $preferences,
            'songs' => $songs,
        ];
    }
}
```

### 4.6 Bağlantı Havuzu Yönetimi

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Database;

use PDO;
use PDOException;

class ConnectionPool
{
    private array $connections = [];
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getConnection(string $dbName): PDO
    {
        if (isset($this->connections[$dbName])) {
            return $this->connections[$dbName];
        }

        $dbConfig = $this->config[$dbName] ?? null;
        if (!$dbConfig) {
            throw new \InvalidArgumentException("Database not configured: {$dbName}");
        }

        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                $dbConfig['host'],
                $dbConfig['port'],
                $dbName
            );

            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ]);

            $this->connections[$dbName] = $pdo;
            return $pdo;

        } catch (PDOException $e) {
            throw new \RuntimeException("Database connection failed: {$dbName}");
        }
    }

    public function closeAll(): void
    {
        $this->connections = [];
    }
}
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | İhlal Sonucu |
|----------|----------|-------------|
| Cross-DB JOIN | Uygulama seviyesinde join | Veri tutarsızlığı |
| ORM kullanımı (Eloquent, Doctrine) | Raw PDO prepared statement | SQL injection riski |
| `SELECT *` | Açık sütun listesi | Gereksiz veri transferi |
| Hardcoded password | `.env` dosyası | Güvenlik ihlali |
| Tek veritabanında saklama | 9 izole veritabanı | İzolasyon ihlali |
| Transaction across DBs | Uygulama seviyesinde transaction | Veri kaybı riski |
| Synchronous backup | Async backup + WAL | Performans düşüşü |
| Database view | Materialized view | Performans sorunu |
| Stored procedure | Uygulama mantığı | Bakım zorluğu |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Cross-DB join denemesi | Yanlış repository kullanımı | Runtime error + log CRITICAL |
| Veritabanı bağlantı kopması | Network sorunu | Retry (max 3) → circuit breaker |
| BCNF ihlali | Yeni tablo eklenmesi | BCNF audit → düzeltme |
| Backup çakışması | Eşzamanlı yedekleme | Mutex ile tek backup |
| Schema değişikliği | Migration sırasında erişim | Maintenance mode |
| Veri tutarsızlığı | Uygulama seviyesi join hatası | Reconciliation job |
| Connection pool exhaustion | Yüksek load | Pool size limit + queue |
| Character set uyumsuzluğu | Farklı charset'ler | UTF-8 mb4 zorunlu |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | 9 veritabanı zorunlu, eklenemez/silinemez | Mimari bozulma |
| 2 | Cross-DB JOIN yasak | Veri tutarsızlığı |
| 3 | ORM yasak (ADR-002) | SQL injection riski |
| 4 | SELECT * yasak | Gereksiz veri transferi |
| 5 | BCNF zorunlu | Normalizasyon ihlali |
| 6 | Prepared statement zorunlu | SQL injection |
| 7 | Soft delete zorunlu (`is_deleted = 0`) | Veri kaybı |
| 8 | snake_case naming zorunlu | Tutarsızlık |

---

## 8. Performans Hedefleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Bağlantı süresi | <50ms | Ortalama |
| Sorgu süresi | <100ms | 95. percentile |
| Write latency | <50ms | Ortalama |
| Read latency | <20ms | 95. percentile |
| Connection pool | max 100/pool | Per-DB |
| Backup süresi | <30dk/DB | Tam backup |
| Migration süresi | <5dk | Schema change |

---

## 9. Güvenlik

| Önlem | Değer | ADR |
|-------|-------|-----|
| Password hash | Argon2id (64MB/4/2) | ADR-022 |
| Credential encryption | AES-256-GCM | ADR-034 |
| Connection SSL | Zorunlu (prod) | ADR-022 |
| Audit logging | Her write işlemi | ADR-004 |
| Backup encryption | AES-256-GCM | ADR-022 |
| Access control | Role-based (RBAC) | ADR-010 |

---

## 10. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak | Temel veritabanı kuralı |
| [[ADR-014-multi-db-migration-strategy]] | Migration stratejisi | Schema yönetimi |
| [[ADR-022-database-hardened-security]] | Güvenlik sertleştirme | Şifreleme, auth |
| [[ADR-033-sql-normalization-strategy]] | SQL normalizasyon | BCNF uyumluluğu |
| [[ADR-040-database-authority]] | 9 BCNF DB otoritesi | Otorite tanımı |
| [[ADR-041-database-normalization-supplementary]] | DB normalizasyon ek bilgi | Ek kurallar |
| [[ADR-050-multi-db-sync-strategy]] | Multi-DB sync | Veri senkronizasyonu |

---

## 11. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-040-database-authority]] | 9 BCNF otoritesi |
| § 4 Şema | [[architecture/05-data/database_master]] | Master DB dokümanı |
| § 5 Yasaklar | [[ADR-002-pdo-mandatory-no-orm]] | ORM yasağı |
| § 6 Edge | [[ADR-014-multi-db-migration-strategy]] | Migration |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 Performans | [[ADR-006-performance-targets]] | Performans hedefleri |
| § 9 Güvenlik | [[ADR-022-database-hardened-security]] | DB güvenlik |
| § 10 ADR | [[ADR-050-multi-db-sync-strategy]] | Sync stratejisi |

---

## 12. Sözlük

| Terim | Tanım |
|-------|-------|
| **BCNF** | Boyce-Codd Normal Form — 3NF'in güçlendirilmiş hali |
| **PDO** | PHP Data Objects — PHP veritabanı bağlantı arayüzü |
| **Prepared Statement** | Önceden derlenmiş SQL sorgusu |
| **Cross-DB JOIN** | İki farklı veritabanı arasında JOIN işlemi |
| **Uygulama Seviyesi Join** | Kod içinde verileri birleştirme |
| **Soft Delete** | Kaydı silmek yerine `is_deleted` flag'i ekleme |
| **Connection Pool** | Bağlantı havuzu, tekrarlı bağlantıları önleme |
| **RBAC** | Role-Based Access Control |
| **Schema** | Veritabanı yapısı tanımı |
| **Migration** | Veritabanı şeması değişikliği |
| **Circuit Breaker** | Bağlantı hatası durumunda devre kesme |
| **WAL** | Write-Ahead Logging — journaled write |

---

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 13 |
| Frozen | 2026-05-15 |
| Database Count | 9 BCNF |
| Schema Examples | 3 (auth, user, musics) |
| Code Examples | 2 (repository, connection pool) |
| Yasak Örüntüleri | 9 |
| Edge Cases | 8 |
| Hard Guardrails | 8 |
| Performance Targets | 7 |
| Security Measures | 6 |
| ADR References | 7 |
| Cross References | 8 |
| Glossary Terms | 12 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
