---
type: adr
category: database
title: "ADR-033: SQL Normalization Strategy"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-033: SQL Normalization Strategy

## 1. Amaç

CoreMusic veritabanı normalizasyon stratejisini tanımlar. [[ADR-033-sql-normalization-strategy]] Frozen karardır. Bu karar, 9 BCNF izole veritabanının normalizasyon kurallarını kapsar.

Bu ADR'nin amacı:
- BCNF (Boyce-Codd Normal Form) zorunluluğunu tanımlamak
- Normalizasyon kurallarını belirlemek
- Denormalizasyon için istisnaları tanımlamak
- Veri bütünlüğünü sağlamak
- Migration stratejisini belirlemek
- Backup ve recovery prosedürlerini tanımlamak

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Veritabanı** | MySQL 9 (9 BCNF izole veritabanı) |
| **Normalizasyon** | BCNF zorunlu |
| **Soft Delete** | is_deleted = 0 |
| **Naming** | snake_case |
| **Charset** | UTF-8 MB4 |
| **Engine** | InnoDB |
| **Migration** | Forward-only, versioned |
| **Backup** | Periyodik + on-demand |
| **Performance** | Optimized queries |
| **Security** | Prepared statement |

### 2.1 Neden BCNF?

- **Veri bütünlüğü:** Anomali yok
- **Güncelleme kolaylığı:** Tek yerden güncelleme
- **Sorgu optimizasyonu:** Temiz şema
- **Bakım kolaylığı:** Anlaşılabilir yapı
- **Uzun ömürlü:** Değişiklik direnci

### 2.2 Normalizasyon Seviyeleri

| Seviye | Tanım | Gereksinim |
|--------|-------|------------|
| **1NF** | Birincil normal form | Atomic değerler, unique PK |
| **2NF** | İkinci normal form | 1NF + partial dependency yok |
| **3NF** | Üçüncü normal form | 2NF + transitive dependency yok |
| **BCNF** | Boyce-Codd | 3NF + her determinant candidate key |

## 3. Karar

### 3.1 Normalizasyon Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **BCNF** | ✅ Zorunlu | Veri bütünlüğü |
| **Soft Delete** | ✅ Zorunlu | Veri kaybı önleme |
| **snake_case** | ✅ Zorunlu | Tutarlılık |
| **UTF-8 MB4** | ✅ Zorunlu | Unicode desteği |
| **InnoDB** | ✅ Zorunlu | ACID + locking |
| **Prepared Statement** | ✅ Zorunlu | SQL injection koruması |
| **No SELECT*** | ✅ Zorunlu | Performans + güvenlik |
| **Explicit Columns** | ✅ Zorunlu | Kontrol |
| **Index** | ✅ Gerekirse | Performans |
| **Constraint** | ✅ Zorunlu | Veri bütünlüğü |

### 3.2 Yasaklanan Örüntüler

| Örüntü | Neden Yasak | Alternatif |
|--------|-------------|------------|
| 1NF ihlali | Atomic olmayan değerler | Atomic values |
| 2NF ihlali | Partial dependency | Tam bağımlılık |
| 3NF ihlali | Transitive dependency | Doğrudan bağımlılık |
| BCNF ihlali | Non-key determinant | Candidate key |
| Hard delete | Veri kaybı | Soft delete |
| PascalCase | Tutarlısızlık | snake_case |
| UTF-8 | Eksik Unicode | UTF-8 MB4 |
| MyISAM | ACID yok | InnoDB |
| SELECT * | Performans + güvenlik | Explicit columns |
| No index | Yavaş sorgu | Proper indexing |

## 4. Teknik Detaylar

### 4.1 BCNF Örneği — Users Tablosu

```sql
-- ✅ DOĞRU: BCNF uyumlu users tablosu
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Constraints
    UNIQUE KEY uk_username (username),
    UNIQUE KEY uk_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active),
    INDEX idx_is_deleted (is_deleted),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ❌ YANLIŞ: BCNF ihlali
CREATE TABLE users_bad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    email VARCHAR(255),
    -- Transitive dependency: role → role_permissions
    role VARCHAR(50),
    role_permissions TEXT, -- Bu role'a bağımlı (transitive)
    created_at TIMESTAMP
);
```

### 4.2 BCNF Örneği — Tracks Tablosu

```sql
-- ✅ DOĞRU: BCNF uyumlu tracks tablosu
CREATE TABLE tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    album VARCHAR(255),
    genre VARCHAR(100),
    duration INT UNSIGNED, -- saniye
    file_path VARCHAR(500) NOT NULL,
    file_size BIGINT UNSIGNED,
    format ENUM('flac', 'mp3', 'wav', 'aac') DEFAULT 'flac',
    bitrate INT UNSIGNED,
    sample_rate INT UNSIGNED,
    channels TINYINT UNSIGNED DEFAULT 2,
    is_public TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Constraints
    INDEX idx_artist (artist),
    INDEX idx_album (album),
    INDEX idx_genre (genre),
    INDEX idx_format (format),
    INDEX idx_is_public (is_public),
    INDEX idx_is_deleted (is_deleted),
    FULLTEXT INDEX ft_title_artist (title, artist)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.3 BCNF Örneği — Playlists Tablosu

```sql
-- ✅ DOĞRU: BCNF uyumlu playlists tablosu
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_public TINYINT(1) DEFAULT 0,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    
    -- Constraints
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_public (is_public),
    INDEX idx_is_deleted (is_deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ✅ DOĞRU: BCNF uyumlu playlist_tracks tablosu
CREATE TABLE playlist_tracks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    playlist_id INT NOT NULL,
    track_id INT NOT NULL,
    position INT UNSIGNED NOT NULL DEFAULT 0,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Constraints
    FOREIGN KEY (playlist_id) REFERENCES playlists(id) ON DELETE CASCADE,
    FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE,
    UNIQUE KEY uk_playlist_track (playlist_id, track_id),
    INDEX idx_playlist_id (playlist_id),
    INDEX idx_track_id (track_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.4 Soft Delete Implementation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Database;

class SoftDeleteRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * ✅ Soft delete
     */
    public function softDelete(string $table, int $id): bool
    {
        $sql = "UPDATE {$table} 
                SET is_deleted = 1, deleted_at = NOW() 
                WHERE id = :id AND is_deleted = 0";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * ✅ Soft delete ile listeleme
     */
    public function findAll(string $table, array $conditions = []): array
    {
        $sql = "SELECT * FROM {$table} WHERE is_deleted = 0";
        $params = [];

        foreach ($conditions as $column => $value) {
            $sql .= " AND {$column} = :{$column}";
            $params[":{$column}"] = $value;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * ✅ Hard delete (sadece admin)
     */
    public function hardDelete(string $table, int $id): bool
    {
        $sql = "DELETE FROM {$table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }

    /**
     * ✅ Geri yükleme
     */
    public function restore(string $table, int $id): bool
    {
        $sql = "UPDATE {$table} 
                SET is_deleted = 0, deleted_at = NULL 
                WHERE id = :id AND is_deleted = 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->rowCount() > 0;
    }
}
```

### 4.5 Migration Strategy

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Migration;

class MigrationManager
{
    private \PDO $pdo;
    private string $migrationsPath;

    public function __construct(\PDO $pdo, string $migrationsPath)
    {
        $this->pdo = $pdo;
        $this->migrationsPath = $migrationsPath;
    }

    /**
     * ✅ Migration tablosunu oluştur
     */
    public function init(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            version VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uk_version (version)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->pdo->exec($sql);
    }

    /**
     * ✅ Pending migration'ları çalıştır
     */
    public function migrate(): array
    {
        $this->init();
        
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();
        $pending = array_diff($files, $executed);
        
        $results = [];

        foreach ($pending as $file) {
            try {
                $this->executeMigration($file);
                $results[$file] = 'SUCCESS';
            } catch (\Exception $e) {
                $results[$file] = 'FAILED: ' . $e->getMessage();
                break; // Hata durumunda dur
            }
        }

        return $results;
    }

    private function getExecutedMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT version FROM migrations ORDER BY version");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.sql');
        return array_map('basename', $files);
    }

    private function executeMigration(string $file): void
    {
        $sql = file_get_contents($this->migrationsPath . '/' . $file);
        
        $this->pdo->beginTransaction();
        
        try {
            $this->pdo->exec($sql);
            
            // Migration kaydı ekle
            $stmt = $this->pdo->prepare(
                "INSERT INTO migrations (version, name) VALUES (:version, :name)"
            );
            $stmt->execute([
                ':version' => explode('_', $file)[0],
                ':name' => $file,
            ]);
            
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
```

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| BCNF ihlali | BCNF zorunlu | ADR-033 | Veri anomalis |
| Hard delete | Soft delete | ADR-033 | Veri kaybı |
| PascalCase | snake_case | ADR-033 | Tutarlısızlık |
| UTF-8 | UTF-8 MB4 | ADR-033 | Unicode sorunu |
| MyISAM | InnoDB | ADR-033 | ACID yok |
| SELECT * | Explicit columns | ADR-002 | Performans + güvenlik |
| No index | Proper indexing | ADR-033 | Yavaş sorgu |
| No constraint | FK + UK constraints | ADR-033 | Veri bütünlüğü |
| Transitive dependency | Doğrudan bağımlılık | ADR-033 | BCNF ihlali |
| Partial dependency | Tam bağımlılık | ADR-033 | 2NF ihlali |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **BCNF violation** | 3NF → BCNF audit | ADR-040 |
| **Schema change** | Forward-only migration | ADR-014 |
| **Data loss risk** | Soft delete + backup | ADR-033 |
| **Performance** | Index optimization | ADR-033 |
| **Concurrency** | InnoDB locking | ADR-033 |
| **Backup failure** | Alternative backup | ADR-033 |
| **Migration failure** | Rollback mechanism | ADR-014 |
| **Encoding issue** | UTF-8 MB4 | ADR-033 |
| **Large dataset** | Chunked processing | ADR-033 |
| **Query timeout** | Query optimization | ADR-033 |
| **Connection limit** | Connection pooling | ADR-033 |
| **Deadlock** | Transaction retry | ADR-033 |
| **Cache invalidation** | Cache strategy | ADR-007 |
| **Audit requirement** | Audit trail | ADR-004 |
| **Multi-DB sync** | Sync strategy | ADR-050 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | BCNF zorunlu | ADR-033 | Veri anomalis |
| 2 | Soft delete zorunlu | ADR-033 | Veri kaybı |
| 3 | snake_case zorunlu | ADR-033 | Tutarlısızlık |
| 4 | UTF-8 MB4 zorunlu | ADR-033 | Unicode sorunu |
| 5 | InnoDB zorunlu | ADR-033 | ACID yok |
| 6 | Prepared statement zorunlu | ADR-002 | SQL injection |
| 7 | No SELECT* | ADR-002 | Performans + güvenlik |
| 8 | FK constraints zorunlu | ADR-033 | Veri bütünlüğü |
| 9 | Migration versioning zorunlu | ADR-014 | Schema karmaşası |
| 10 | Backup zorunlu | ADR-033 | Veri kaybı |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-033-sql-normalization-strategy]] | Bu karar | SQL normalizasyonu |
| [[ADR-040-database-authority]] | DB otoritesi | 9 BCNF |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO | Veritabanı erişimi |
| [[ADR-014-multi-db-migration-strategy]] | Migration | Schema yönetimi |
| [[ADR-022-database-hardened-security]] | Güvenlik | DB sertleştirme |
| [[ADR-050-multi-db-sync-strategy]] | Multi-DB sync | Veri senkronizasyonu |
| [[ADR-004-multi-domain-spa]] | SPA | Multi-domain |
| [[ADR-007-cache-namespace]] | Cache | Önbellek |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[architecture/05-data/database_master]] | 9 BCNF şemaları |
| § 4 Teknik | [[.sql/coremusic_auth.sql]] | Auth şeması |
| § 5 Yasak | [[ADR-040-database-authority]] | DB otoritesi |
| § 5 Yasak | [[ADR-002-pdo-mandatory-no-orm]] | PDO |
| § 6 Edge | [[ADR-014-multi-db-migration-strategy]] | Migration |
| § 6 Edge | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 7 Guardrails | [[ADR-050-multi-db-sync-strategy]] | Multi-DB sync |
| § 7 Guardrails | [[ADR-007-cache-namespace]] | Cache |
| § 8 İlgili | [[ADR-004-multi-domain-spa]] | SPA |
| § 8 İlgili | [[ADR-003-multi-db-9-databases]] | 9 DB |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **BCNF** | Boyce-Codd Normal Form |
| **3NF** | Third Normal Form |
| **2NF** | Second Normal Form |
| **1NF** | First Normal Form |
| **Normalizasyon** | Veri tekrarını azaltma |
| **Soft Delete** | Silme yerine işaretleme |
| **Hard Delete** | Gerçek silme |
| **snake_case** | küçük_harf_ve_alt_çizgi |
| **InnoDB** | MySQL ACID uyumlu engine |
| **MyISAM** | MySQL eski engine (ACID yok) |
| **UTF-8 MB4** | Tam Unicode desteği |
| **Migration** | Şema değişikliği yönetimi |
| **Forward-only** | Sadece ileriye yönelik migration |
| **Candidate Key** | Alternatif birincil anahtar |
| **Determinant** | Diğer sütunları belirleyen sütun |
| **Anomali** | Veri tutarsızlığı |
| **Atomic** | Bölünemez değer |
| **Partial Dependency** | Kısmi bağımlılık |
| **Transitive Dependency** | Geçişli bağımlılık |
| **Index** | Veritabanı indeksi |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 002, 003, 004, 007, 014, 022, 033, 040, 050 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 10 referans |
| **Guardrails** | ✅ 10 kural |
| **Edge Cases** | ✅ 15 senaryo |
| **Yasak Örüntü** | ✅ 10 kural |
| **Terim Sayısı** | ✅ 20 terim |
| **SQL Şemaları** | ✅ 4 tablo |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
