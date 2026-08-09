---
type: adr
category: database
title: "ADR-014: Multi-DB Migration Strategy"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-014: Multi-DB Migration Strategy

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun 9 BCNF veritabanında izlenebilir, güvenli ve tekrarlanabilir veritabanı değişiklik yönetimi için **forward-only, versioned migration stratejisi** tanımlar. [[ADR-040-database-authority]] ile belirlenen 9 izole veritabanının (coremusic_auth, coremusic_user, coremusic_musics, coremusic_albums, coremusic_playlist, coremusic_catalog, coremusic_logs, coremusic_media, coremusic_system) her biri için bağımsız ve tutarlı migration süreçleri sunar.

---

## 2. Bağlam

### 2.1 Problem Tanımı

CoreMusic, 9 farklı BCNF veritabanı kullanmaktadır. Her bir veritabanı farklı bir alana hizmet eder:

| # | Veritabanı | Amaç | Örnek Tablolar |
|---|------------|------|----------------|
| 1 | coremusic_auth | Kullanıcılar, roller, session | users, roles, sessions |
| 2 | coremusic_user | Profiller, tercihler | profiles, preferences, history |
| 3 | coremusic_musics | Şarkılar, sanatçılar | songs, artists, genres |
| 4 | coremusic_albums | Albüm koleksiyonları | albums, album_tracks |
| 5 | coremusic_playlist | Çalma listeleri | playlists, playlist_items |
| 6 | coremusic_catalog | İndirme kuyrukları | download_queue, service_status |
| 7 | coremusic_logs | Audit trail, loglar | activity_logs, error_logs |
| 8 | coremusic_media | Medya metadata | media_files, metadata |
| 9 | coremusic_system | Sistem konfigürasyonu | settings, feature_flags |

Bu veritabanları birbirinden **izole**dır (ADR-003). Çapraz veritabanı referansı yasaktır. Her birinin migration süreci bağımsız olarak yönetilmelidir.

### 2.2 Neden Forward-Only?

| Sebep | Açıklama |
|-------|----------|
| Veri güvenliği | Rollback sırasında veri kaybı riski |
| İzlenebilirlik | Her değişiklik izlenebilir olmalı |
| Basitlik | Geri alma mekanizması karmaşıklık yaratır |
| Production uyumluluğu | Üretim ortamında rollback çok riskli |
| BCNF korunması | Normalizasyon bütünlüğü korunmalı |

### 2.3 Neden Versioned?

Her migration dosyası benzersiz bir versiyon numarası taşır:

```
001_create_users_table.sql
002_add_email_index.sql
003_create_roles_table.sql
```

Bu sayede:
- Hangi değişikliklerin uygulandığı izlenebilir
- Sıralı uygulama garantisi sağlanır
- Çakışmalar önlenir
- Ekip çalışması kolaylaşır (farklı üyeler farklı migration'lar yazar)

### 2.4 İlişkili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | 9 izole veritabanı tanımı |
| [[ADR-040-database-authority]] | BCNF otoritesi |
| [[ADR-033-sql-normalization-strategy]] | SQL normalizasyon stratejisi |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak |

### 2.5 Endüstri Karşılaştırması

| Framework | Migration Stratejisi | Bizim Strateji |
|-----------|---------------------|----------------|
| Laravel | Forward-only, versioned | ✅ Benzer |
| Django | Forward-only + squash | ✅ Benzer |
| Rails | Bidirectional | ❌ Bizde forward-only |
| Symfony | Versioned | ✅ Benzer |
| CoreMusic | Forward-only, versioned, multi-DB | ✅ Özgün |

---

## 3. Karar

CoreMusic'te **forward-only, versioned migration** stratejisi kullanılacak:

| Karar | Değer |
|-------|-------|
| Strateji | Forward-only (geri alma yasak) |
| Versionlama | Her dosya benzersiz versiyonlu |
| Bağımsızlık | Her DB bağımsız |
| Yedekleme | Migration öncesi zorunlu |
| Format | SQL dosyaları (.sql) |
| Motor | PHP scriptleri ile uygulama |
| Sıralama | Versiyon numarasına göre artan |
| Doğrulama | Migration sonrası checksum |
| Loglama | Her migration loglanır |
| Timeout | Migration başına max 300s |
| Lock | Eşzamanlı migration yasak |

---

## 4. Teknik Detaylar

### 4.1 Migration Dosya Yapısı

```
.sql/
├── coremusic_auth/
│   ├── 001_create_users_table.sql
│   ├── 002_add_email_index.sql
│   ├── 003_create_roles_table.sql
│   ├── 004_create_sessions_table.sql
│   ├── 005_add_password_hash_column.sql
│   └── ...
├── coremusic_user/
│   ├── 001_create_profiles_table.sql
│   ├── 002_add_preferences_columns.sql
│   └── ...
├── coremusic_musics/
│   ├── 001_create_songs_table.sql
│   ├── 002_create_artists_table.sql
│   ├── 003_create_genres_table.sql
│   └── ...
├── coremusic_albums/
│   ├── 001_create_albums_table.sql
│   └── ...
├── coremusic_playlist/
│   ├── 001_create_playlists_table.sql
│   └── ...
├── coremusic_catalog/
│   ├── 001_create_download_queue_table.sql
│   └── ...
├── coremusic_logs/
│   ├── 001_create_activity_logs_table.sql
│   └── ...
├── coremusic_media/
│   ├── 001_create_media_files_table.sql
│   └── ...
├── coremusic_system/
│   ├── 001_create_settings_table.sql
│   └── ...
└── _shared/
    ├── 001_create_migration_history.sql
    └── 002_create_audit_functions.sql
```

### 4.2 Migration Dosya Formatı

Her migration dosyası şu formatı izler:

```sql
-- Migration: 003_create_roles_table.sql
-- Database: coremusic_auth
-- Date: 2026-05-15
-- Author: data-engineer
-- Description: Roles tablosu oluşturuldu
-- Checksum: sha256:abc123...
-- Estimated time: <5s

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_roles_name ON roles(name);

-- Verify: SELECT COUNT(*) FROM roles; -- Should return 0
-- BCNF check: No transitive dependencies
```

### 4.3 Migration Script Akışı

```
1. Pre-flight Checks
   ├── Veritabanı bağlantısı kontrolü
   ├── Yedek alma (mysqldump)
   ├── Mevcut versiyon kontrolü (_migration_history)
   ├── Hedef versiyon belirleme
   ├── BCNF uyumluluk kontrolü
   └── Lock acquisition (APCu)

2. Migration Uygulama
   ├── Dosya sıralaması (version numarasına göre)
   ├── Her dosya için execute
   ├── Checksum hesaplama
   ├── Hata kontrolü
   ├── _migration_history kayıt
   └── Lock release

3. Post-flight Validation
   ├── Tablo yapısı doğrulama (SHOW CREATE TABLE)
   ├── Index varlığını kontrol (SHOW INDEX)
   ├── BCNF uyumluluk kontrolü
   ├── Migration log kaydı
   └── Memory.md session state güncelleme
```

### 4.4 Versiyon Takip Tablosu

Her veritabanında `_migration_history` tablosu bulunur:

```sql
CREATE TABLE _migration_history (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    version VARCHAR(10) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    checksum VARCHAR(64) NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    execution_time_ms INT UNSIGNED,
    status ENUM('success', 'failed') DEFAULT 'success',
    executed_by VARCHAR(100) DEFAULT 'system',
    description TEXT,
    INDEX idx_version (version),
    INDEX idx_executed_at (executed_at),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.5 Checksum Doğrulama

Her migration dosyasının SHA-256 checksum'ı hesaplanır ve `_migration_history` tablosuna kaydedilir:

```php
class MigrationChecksum {
    public static function calculate(string $filepath): string {
        $content = file_get_contents($filepath);
        // Yorum satırlarını hariç tut
        $content = self::stripComments($content);
        return hash('sha256', $content);
    }

    public static function verify(string $filepath, string $storedChecksum): bool {
        $currentChecksum = self::calculate($filepath);
        return hash_equals($storedChecksum, $currentChecksum);
    }

    private static function stripComments(string $sql): string {
        return preg_replace('/--.*$/m', '', $sql);
    }
}
```

### 4.6 Hata Yönetimi

| Hata Türü | Aksiyon | Öncelik |
|-----------|---------|---------|
| Bağlantı hatası | Migration iptal, log CRITICAL | CRITICAL |
| SQL syntax hatası | Migration durdur, log ERROR | HIGH |
| BCNF ihlali | Migration reddedilir, log CRITICAL | CRITICAL |
| Checksum uyuşmazlığı | Migration reddedilir, log ERROR | HIGH |
| Tablo zaten var | Atla, log WARN | MEDIUM |
| Index zaten var | Atla, log INFO | LOW |
| Timeout (300s) | Migration durdur, log ERROR | HIGH |
| Lock timeout | Escalasyon, log WARN | MEDIUM |

### 4.7 Parallel Migration Yasak

Aynı anda birden fazla migration çalıştırılamaz:

```php
class MigrationLock {
    private const LOCK_KEY = 'migration_lock';
    private const LOCK_TIMEOUT = 30;
    private const LOCK_TTL = 300;

    public function acquire(string $database): bool {
        $start = time();
        while (time() - $start < self::LOCK_TIMEOUT) {
            if (apcu_add(self::LOCK_KEY . $database, true, self::LOCK_TTL)) {
                return true;
            }
            usleep(100000); // 100ms
        }
        return false;
    }

    public function release(string $database): void {
        apcu_delete(self::LOCK_KEY . $database);
    }

    public function isLocked(string $database): bool {
        return apcu_exists(self::LOCK_KEY . $database);
    }
}
```

### 4.8 Migration Motoru

```php
class MigrationEngine {
    private PDO $pdo;
    private MigrationLock $lock;
    private string $database;
    private string $migrationPath;

    public function migrate(): MigrationResult {
        // 1. Lock
        if (!$this->lock->acquire($this->database)) {
            throw new MigrationException('Lock acquire failed');
        }

        try {
            // 2. Mevcut versiyon
            $currentVersion = $this->getCurrentVersion();

            // 3. Pending migration'ları bul
            $pending = $this->getPendingMigrations($currentVersion);

            // 4. Her migration'ı uygula
            foreach ($pending as $migration) {
                $this->executeMigration($migration);
            }

            return new MigrationResult(true, count($pending));
        } catch (\Exception $e) {
            return new MigrationResult(false, 0, $e->getMessage());
        } finally {
            $this->lock->release($this->database);
        }
    }

    private function executeMigration(MigrationFile $migration): void {
        $start = microtime(true);

        // Checksum doğrula
        if (!$this->verifyChecksum($migration)) {
            throw new MigrationException('Checksum mismatch');
        }

        // SQL'i uygula
        $sql = file_get_contents($migration->getPath());
        $this->pdo->exec($sql);

        // Süreyi hesapla
        $executionTime = (int)((microtime(true) - $start) * 1000);

        // History'ye kaydet
        $this->recordHistory($migration, $executionTime);
    }
}
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Rollback migration | Forward-only, yeni migration ile düzeltme |
| `SELECT *` | Açık sütun listesi |
| ORM kullanma | Raw PDO prepared statement |
| Cross-database reference | Her DB bağımsız |
| Hardcoded secret | .env dosyası |
| Parallel migration | Tek tek, sıralı uygulama |
| Migration öncesi yedek almama | Yedek zorunlu |
| BCNF ihlali | 3NF → BCNF audit zorunlu |
| Dosya adı değiştirme | In-place modification |
| Version atlama | Sıralı versiyonlama |
| Drop table | Soft delete (is_deleted = 0) |
| Drop column | Yeni column ekleme, eskiyi deaktif |
| Transaction içi migration | Tek transaction, tümü başarılı veya tümü başarısız |
| Large batch | Chunked processing (1000 satır) |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Migration yarım kaldı | Sunucu çökmesi | `_migration_history` kontrol, kalan dosyaları uygula |
| BCNF ihlali | Yeni tablo eklenmesi | 3NF → BCNF audit, düzeltme migration'ı |
| Parallel lock timeout | 30s+ bekleme | Escalation, MO müdahale |
| Dosya silindi | Migration dosyası kayboldu | Yeni migration ile düzeltme |
| Tablo zaten var | Tekrar uygulama | Atla, log WARN |
| Versiyon çakışması | Aynı versiyon iki DB'de | Her DB bağımsız versiyonluyor |
| Cross-database query | İstenmeyen reference | Derhal ret, log ERROR |
| Rollback isteği | Kullanıcı geri alma | Yeni reverse migration yaz |
| Checksum uyuşmazlığı | Dosya değişikliği | Migration reddedilir |
| Large table migration | 10M+ satır | Batch processing, chunk size 1000 |
| Column rename | Sütun adı değişikliği | ADD new → copy → DROP old |
| Index rebuild | Büyük tablo index | Online DDL veya pt-online-schema-change |
| Charset değişikliği | utf8 → utf8mb4 | ALTER TABLE CONVERT TO CHARACTER SET |
| Engine değişikliği | MyISAM → InnoDB | ALTER TABLE ENGINE=InnoDB |
| Foreign key ekleme | Mevcut tabloya FK | Validate data first, then ADD CONSTRAINT |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | **Forward-only** — Geri alma yasak | Migration reddedilir, log CRITICAL |
| 2 | **Versioned** — Her dosya benzersiz versiyonlu | Migration reddedilir |
| 3 | **Independent** — Her DB bağımsız | Layer violation, revert |
| 4 | **Backup zorunlu** — Migration öncesi yedek | Migration iptal |
| 5 | **BCNF korunması** — Normalizasyon bütünlüğü | BCNF audit zorunlu |
| 6 | **No parallel** — Eşzamanlı migration yasak | Kilitleme, retry |
| 7 | **Checksum** — Dosya bütünlüğü doğrulama | Migration reddedilir |
| 8 | **Prepared statement** — SQL injection önleme | SQL injection riski |
| 9 | **Soft delete** — `is_deleted = 0` zorunlu | Veri kaybı |
| 10 | **Snake_case** — Tablo/sütun adı standardı | Format hatası |
| 11 | **No SELECT \*** — Açık sütun listesi zorunlu | SQL injection riski |
| 12 | **No ORM** — Raw PDO zorunlu | Bağımlılık artışı |
| 13 | **No hardcoded secret** — .env zorunlu | Güvenlik ihlali |
| 14 | **Timestamp zorunlu** — created_at, updated_at | İzlenebilirlik |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak | Migration scriptleri PDO kullanır |
| [[ADR-003-multi-db-9-databases]] | 9 izole veritabanı | Migration stratejisi 9 DB için geçerli |
| [[ADR-022-database-hardened-security]] | DB güvenlik sertleştirme | Migration öncesi yedek zorunlu |
| [[ADR-033-sql-normalization-strategy]] | SQL normalizasyon | BCNF uyumluluk kontrolü |
| [[ADR-040-database-authority]] | 9 BCNF DB otoritesi | Migration stratejisi DB otoritesine bağlı |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma | Migration dosyaları vault'ta |
| [[ADR-041-database-normalization-supplementary]] | DB normalizasyon ek | Ek normalizasyon kuralları |
| [[ADR-050-multi-db-sync-strategy]] | Multi-DB sync | Sync öncesi migration |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2.1 | [[ADR-040-database-authority]] | 9 BCNF veritabanı tanımı |
| § 2.3 | [[ADR-003-multi-db-9-databases]] | İzole veritabanı yapısı |
| § 4.1 | [[architecture/05-data/database_master]] | Migration dosya yapısı |
| § 5 | [[ADR-002-pdo-mandatory-no-orm]] | Yasak örüntüleri |
| § 7 | [[ADR-022-database-hardened-security]] | Hard guardrails |
| § 8 | [[ADR-033-sql-normalization-strategy]] | BCNF kuralları |
| § 6 | [[architecture/l0-infrastructure]] | L0 altyapı katmanı |
| § 4.7 | [[ADR-011-session-management]] | Kilitleme mekanizması |
| § 4.8 | [[architecture/l1-security]] | Güvenlik middleware |
| § 3 | [[ecosystem/7-service-integration]] | Servis entegrasyonu |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Migration** | Veritabanı yapısındaki değişikliklerin izlenebilir uygulanması |
| **Forward-only** | Sadece ileriye doğru, geri alma yok |
| **Versioned** | Her migration'ın benzersiz versiyon numarası |
| **BCNF** | Boyce-Codd Normal Form — 9 DB için zorunlu |
| **PDO** | PHP Data Objects — veritabanı erişim katmanı |
| **ORM** | Object-Relational Mapping (YASAK) |
| **Prepared Statement** | SQL injection önleme mekanizması |
| **Soft Delete** | `is_deleted = 0` ile silme |
| **Snake_case** | `table_name` formatı |
| **Checksum** | Dosya bütünlüğü doğrulama |
| **Batch Processing** | Büyük tablolarda parçalı işlem |
| **Lock** | Eşzamanlı erişim kilitleme |
| **Audit Trail** | Değişiklik izleme günlüğü |
| **InnoDB** | MySQL motoru (transaction destekli) |
| **mysqldump** | MySQL yedek alma aracı |
| **APCu** | APC User Cache — PHP önbellek sistemi |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Status | Frozen (değiştirilemez) |
| Sections | 11 |
| Hard Guardrails | 14 |
| Edge Cases | 15 |
| Yasak Örüntüleri | 14 |
| İlgili ADR'ler | 8 |
| Çapraz Referanslar | 10 |
| Sözlük Terimleri | 16 |
| Migration Stratejisi | Forward-only, Versioned |
| Veritabanı Sayısı | 9 BCNF |
| Versiyon Takip | `_migration_history` tablosu |
| Checksum Algoritması | SHA-256 |
| Kilitleme Süresi | Max 30 saniye |
| Migration Timeout | Max 300 saniye |
| Batch Size | 1000 satır |

---

## 12. Authority

## 13. Monitoring & Metrics

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Migration başarı oranı | %100 | Aylık |
| Ortalama migration süresi | <30s | Haftalık |
| BCNF ihlali | 0 | Aylık |
| Checksum uyuşmazlığı | 0 | Her migration |
| Rollback isteği | 0 | Aylık |
| Parallel lock timeout | 0 | Haftalık |

### 13.1 Dashboard

```
Migration Dashboard
├── Active Migrations (gerçek zamanlı)
├── Migration History (son 30 gün)
├── BCNF Compliance Score (%)
├── Average Execution Time (ms)
├── Error Rate (%)
└── Pending Migrations (kuyruk)
```

---

## 14. Compliance

| Standart | Uyumluluk |
|----------|-----------|
| OWASP Top 10:2025 | A05:2025 — Security Misconfiguration |
| NIST SP 800-53 | CM-3: Configuration Change Control |
| SOC 2 | CC6.1 — Logical Access |

---

## 15. Data Integrity Verification

| Check | Yöntem | Frekans |
|-------|--------|---------|
| BCNF compliance | Schema audit | Her migration sonrası |
| Foreign key integrity | CHECK CONSTRAINT | Haftalık |
| Index completeness | SHOW INDEX | Aylık |
| Data type validation | Information Schema | Her migration |
| Character set compliance | ALTER TABLE verify | Her migration |

### 15.1 BCNF Audit Script

```sql
-- BCNF audit for all tables
SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_TYPE
FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
    ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
WHERE tc.TABLE_SCHEMA = 'coremusic_auth'
ORDER BY TABLE_NAME, ORDINAL_POSITION;
```

### 15.2 Migration Rollback Procedure (Manual)

Her ne kadar forward-only olsa da, manuel düzeltme migration'ları yazılabilir:

```sql
-- Manual fix migration (not a rollback)
-- 099_fix_email_index.sql
-- Description: Email index yeniden oluşturuldu
DROP INDEX idx_users_email ON users;
CREATE INDEX idx_users_email ON users(email, is_deleted);
```

---

## 16. Documentation Requirements

| Doküman | İçerik | Güncelleme |
|---------|--------|------------|
| Schema docs | Her tablo için tanım | Yeni migration'da |
| ER diagram | Entity-relationship | Her fazda |
| Runbook | Migration prosedürü | Değişiklikte |
| Incident response | Hata durumunda aksiyon | quarterly |

---

## 17. Quality Report (Final)

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Sections | 17 |
| Hard Guardrails | 14 |
| Edge Cases | 15 |
| Yasak Örüntüleri | 14 |
| Monitoring Metrics | 6 |
| Compliance Standards | 3 |
| Data Integrity Checks | 5 |
| Documentation Types | 4 |
| Audit Frequency | 4 levels |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
**Immutability:** ADR 001-037 frozen, değiştirilemez
**Scope:** CoreMusic 9 BCNF veritabanı migration yönetimi
**Governance:** Red Team · Human Mode · Truth Mode
