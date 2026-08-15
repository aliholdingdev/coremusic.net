---
type: template
category: adr-database
title: "CoreMusic — ADR Database Template (SQL/BCNF/Migration/Query)"
version: 1.0.0
created: 2026-08-07
updated: 2026-08-07
authority: Vault Steward
governance: Red Team • Human Mode • Truth Mode
usage: "Database/SQL/BCNF/Migration ile ilgili ADR oluştururken bu dosyayı kopyalayın"
related:
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-003-multi-db-9-databases]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
tags: [template, adr, database, sql, bcnf, migration, query, pdo]
---

# CoreMusic — ADR Database Template

**Bu dosya bir şablondur.** Database, SQL, BCNF veya Migration ile ilgili ADR oluştururken bu dosyayı kopyalayın.

**Kullanım:** `cp .ai/.templates/adr-database-template.md .ai/decisions/accepted/ADR-NNN-baslik.md`

---

## 📋 Database ADR Kullanım Kılavuzu

### Database ADR Ne Zaman Yazılır?

| Durum | Gerekli mi? | Açıklama |
|-------|-------------|----------|
| Yeni tablo ekleme | ✅ Evet | BCNF normalization gerekli |
| Yeni veritabanı ekleme | ✅ Evet | 18 BCNF DB yapısını etkiliyor |
| Migration stratejisi | ✅ Evet | Forward-only kuralı |
| Query optimizasyonu | ✅ Evet | Performans etkisi |
| Index değişikliği | ✅ Evet | Query planını etkiliyor |
| Schema değişikliği | ✅ Evet | BCNF uyumluluğu |
| Küçük SQL düzeltmesi | ❌ Hayır | Rutin değişiklik |

### Database ADR Yazarken Dikkat

1. **ADR-002:** PDO mandatory, ORM YASAK
2. **ADR-003:** 9 ayrı BCNF veritabanı
3. **ADR-040:** Database Authority — 18 BCNF canonical
4. **ADR-022:** Database Hardened Security
5. **ADR-033:** SQL Normalization Strategy
6. **SELECT * YASAK:** Explicit column seçimi zorunlu

---

## 📄 DATABASE ADR ŞABLONU

---

```yaml
---
type: decision
id: "NNN"
title: "ADR-NNN: [Database Karar Başlığı]"
category: "database"
status: "draft|active|frozen"
date: "YYYY-MM-DD"
updated: "YYYY-MM-DD"
authority: "Data Engineer / Backend Architect"
governance: "Red Team • Human Mode • Truth Mode"
supersedes: null
version: 1.0.0
tags: [database, sql, bcnf, migration, query, pdo]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-003-multi-db-9-databases]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
  - "[[decisions/accepted/ADR-022-database-hardened-security]]"
  - "[[architecture/05-data/database_master]]"
---
```

---

## 1. Executive Summary

[Database kararının kısa özeti. Ne değişiyor? Hangi DB etkileniyor?]

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | draft / active / frozen |
| **Versiyon** | 1.0.0 |
| **Oluşturma** | YYYY-MM-DD |
| **Son Güncelleme** | YYYY-MM-DD |
| **Otorite** | Data Engineer / Backend Architect |
| **Onay** | Red Team • Human Mode • Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

[Database ile ilgili hangi sorun çözülüyor?]

### 3.2 Mevcut Database Durumu

#### 3.2.1 18 BCNF Veritabanı Listesi

| # | Veritabanı | Amaç | Tablo Sayısı | Durum |
|---|------------|------|-------------|-------|
| 1 | coremusic_auth | Kimlik doğrulama | [sayı] | ✅ |
| 2 | coremusic_user | Kullanıcı yönetimi | [sayı] | ✅ |
| 3 | coremusic_musics | Müzik kataloğu | [sayı] | ✅ |
| 4 | coremusic_catalog | Medya kataloğu | [sayı] | ✅ |
| 5 | coremusic_albums | Albüm yönetimi | [sayı] | ✅ |
| 6 | coremusic_playlist | Çalma listesi | [sayı] | ✅ |
| 7 | coremusic_media | Medya dosyaları | [sayı] | ✅ |
| 8 | coremusic_download | İndirme yönetimi | [sayı] | ✅ |
| 9 | coremusic_logs | Log yönetimi | [sayı] | ✅ |

#### 3.2.2 Mevcut Schema Durumu

| Veritabanı | Tablo | Mevcut Durum | Değişiklik |
|------------|-------|-------------|------------|
| [DB adı] | [Tablo adı] | [Durum] | [Değişiklik] |

### 3.3 İtici Güçler

| # | Güç | Açıklama | Kritiklik |
|---|-----|----------|-----------|
| 1 | [Güç 1] | [Açıklama] | Yüksek/Orta/Düşük |
| 2 | [Güç 2] | [Açıklama] | Yüksek/Orta/Düşük |

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Açıklama | İlgili ADR |
|-----------|----------|------------|
| ADR-002 | PDO mandatory, ORM yasak | ADR-002 |
| ADR-003 | 9 ayrı BCNF veritabanı | ADR-003 |
| ADR-040 | Database Authority | ADR-040 |
| ADR-022 | Hardened Security | ADR-022 |
| ADR-033 | SQL Normalization | ADR-033 |
| BCNF | Boyce-Codd Normal Form | ADR-040 |

### 3.5 BCNF Normalizasyon Kontrolü

| Normal Form | Durum | Açıklama |
|-------------|-------|----------|
| **1NF** | ✅ | Atomic değerler, tekrarlayan grup yok |
| **2NF** | ✅ | Partial dependency yok |
| **3NF** | ✅ | Transitive dependency yok |
| **BCNF** | ✅ | Determinant'lar candidate key |

### 3.6 Ekosistem Etkileşimi

| Etkilenen Alan | Etki | Açıklama |
|---------------|------|----------|
| **L0 Infrastructure** | Doğrudan | DB connection, cache |
| **L2 Routing** | Doğrudan | API endpoint'ler |
| **Backend Services** | Doğrudan | Tüm servisler |
| **Download Service** | Endirekt | Download queue |

---

## 4. Decision

### 4.1 Karar Bildirimi

**[Net database karar cümlesi]**

### 4.2 Database Kuralları

| # | Kural | Durum | İlgili ADR |
|---|-------|-------|------------|
| 1 | PDO mandatory, ORM yasak | ✅ Zorunlu | ADR-002 |
| 2 | SELECT * yasak | ❌ Yasak | ADR-002 |
| 3 | Prepared Statement zorunlu | ✅ Zorunlu | ADR-002 |
| 4 | 18 BCNF veritabanı | ✅ Zorunlu | ADR-003 |
| 5 | BCNF normalizasyon | ✅ Zorunlu | ADR-040 |
| 6 | Soft-delete zorunlu | ✅ Zorunlu | ADR-022 |
| 7 | Audit trail zorunlu | ✅ Zorunlu | ADR-022 |
| 8 | Forward-only migration | ✅ Zorunlu | ADR-014 |

### 4.3 Tablo Tasarımı

```sql
-- Tablo: [tablo_adı]
-- Veritabanı: [veritabanı_adı]
-- ADR: ADR-NNN
-- BCNF: ✅ Doğrulandı

CREATE TABLE [tablo_adı] (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    [kolon_adi] [veri_tipi] [NOT NULL/NULL] [DEFAULT deger],
    [kolon_adi] [veri_tipi] [NOT NULL/NULL] [DEFAULT deger],
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    INDEX idx_[kolon] ([kolon_adi]),
    INDEX idx_created ([created_at]),
    INDEX idx_deleted ([is_deleted])
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.4 BCNF Normalizasyon Adımları

| Adım | Açıklama | Durum |
|------|----------|-------|
| **1. Functional Dependencies** | Fonksiyonel bağımlılıkları belirle | ✅ |
| **2. Candidate Keys** | Aday anahtarları belirle | ✅ |
| **3. Prime Attributes** | Primer attribute'ları belirle | ✅ |
| **4. BCNF Check** | Her determinant candidate key mi? | ✅ |
| **5. Decomposition** | Gerekirse tabloyu böl | ✅ |

### 4.5 Foreign Key İlişkileri

```sql
-- Foreign Key: [ilişki_adı]
ALTER TABLE [tablo_1]
    ADD CONSTRAINT fk_[tablo_1]_[tablo_2]
    FOREIGN KEY ([kolon]) REFERENCES [tablo_2]([kolon])
    ON DELETE CASCADE
    ON UPDATE CASCADE;
```

### 4.6 Index Stratejisi

| Index Tipi | Kullanım Alanı | Örnek |
|-----------|---------------|-------|
| **PRIMARY KEY** | Birincil anahtar | `id` |
| **UNIQUE** | Benzersiz değerler | `email` |
| **INDEX** | Sık sorgulanan kolonlar | `created_at` |
| **COMPOSITE** | Çoklu kolon sorguları | `(user_id, created_at)` |
| **FULLTEXT** | Metin arama | `title, description` |

### 4.7 Query Örnekleri

```php
<?php
// PDO Prepared Statement (ADR-002 uyumlu)
// Dosya: [dosya yolu]
// ADR: ADR-NNN

$stmt = $this->pdo->prepare(
    'SELECT id, title, artist_id 
     FROM songs 
     WHERE is_deleted = 0 
     AND id = :id'
);
$stmt->execute([':id' => $songId]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
```

```sql
-- ❌ YANLIŞ (SELECT * yasak, SQL injection riski)
SELECT * FROM songs WHERE id = $songId;

-- ✅ DOĞRU (Explicit column, prepared statement)
SELECT id, title, artist_id 
FROM songs 
WHERE is_deleted = 0 
AND id = :id;
```

---

## 5. Architecture

### 5.1 Database Mimarisi

```
┌─────────────────────────────────────────────────┐
│              Application Layer (PHP 8.4)         │
│  ┌─────────────────────────────────────────────┐ │
│  │           PDO Connection Layer              │ │
│  └─────────────────────────────────────────────┘ │
└─────────────────────┬───────────────────────────┘
                      │
┌─────────────────────▼───────────────────────────┐
│              Database Cluster (MySQL 9)          │
│  ┌──────────┬──────────┬──────────┬──────────┐  │
│  │ auth     │ user     │ musics   │ catalog  │  │
│  │ (BCNF)   │ (BCNF)   │ (BCNF)   │ (BCNF)   │  │
│  └──────────┴──────────┴──────────┴──────────┘  │
│  ┌──────────┬──────────┬──────────┬──────────┐  │
│  │ albums   │ playlist │ media    │ download │  │
│  │ (BCNF)   │ (BCNF)   │ (BCNF)   │ (BCNF)   │  │
│  └──────────┴──────────┴──────────┴──────────┘  │
│  ┌──────────┐                                   │
│  │ logs     │                                   │
│  │ (BCNF)   │                                   │
│  └──────────┘                                   │
└─────────────────────────────────────────────────┘
```

### 5.2 Connection Pool

| Parametre | Değer | Açıklama |
|-----------|-------|----------|
| **Driver** | PDO MySQL | ADR-002 zorunlu |
| **Charset** | utf8mb4 | Unicode desteği |
| **Collation** | utf8mb4_unicode_ci | Türkçe karakter desteği |
| ** persistent** | false | Güvenlik için |
| **Emulate** | false | Prepared statement |

### 5.3 Cache Stratejisi

| Cache Tipi | TTL | Kullanım |
|-----------|-----|---------|
| **Query Cache** | 600s | Sık sorgulanan veriler |
| **Metadata Cache** | 1200s | Tablo metadata |
| **Session Cache** | 3600s | User session |

---

## 6. Alternatives Considered

### 6.1 Alternatif 1: ORM Kullanımı (Reddedilen)

**Açıklama:** Doctrine veya Eloquent ORM kullanımı

**Avantajlar:**
- Hızlı geliştirme
- Type safety
- Migration desteği

**Dezavantajlar:**
- ADR-002 ile çelişiyor
- Performans düşüşü
- Control loss

**Neden Reddedildi:** ADR-002 PDO mandatory, ORM yasak

### 6.2 Alternatif 2: Monolitik Veritabanı (Reddedilen)

**Açıklama:** Tek bir veritabanında tüm tablolar

**Avantajlar:**
- Basit join işlemleri
- Tek connection

**Dezavantajlar:**
- ADR-003 ile çelişiyor
- Isolation eksik
- Scaling zorluğu

**Neden Reddedildi:** ADR-003 9 ayrı BCNF veritabanı

### 6.3 Karar Matrisi

| Kriter | Ağırlık | ORM | PDO | Monolitik | 18 BCNF |
|--------|---------|-----|-----|-----------|--------|
| ADR Uyumu | %30 | ❌ | ✅ | ❌ | ✅ |
| Performans | %25 | Orta | Yüksek | Yüksek | Yüksek |
| Güvenlik | %25 | Orta | Yüksek | Düşük | Yüksek |
| Bakım | %20 | Yüksek | Orta | Orta | Yüksek |

---

## 7. Consequences

### 7.1 Olumlu Sonuçlar

| # | Sonuç | Etki |
|---|-------|------|
| 1 | [Olumlu sonuç 1] | Yüksek/Orta/Düşük |
| 2 | [Olumlu sonuç 2] | Yüksek/Orta/Düşük |

### 7.2 Olumsuz Sonuçlar

| # | Sonuç | Risk | Mitigation |
|---|-------|------|------------|
| 1 | [Olumsuz sonuç 1] | Yüksek/Orta/Düşük | [Çözüm] |
| 2 | [Olumsuz sonuç 2] | Yüksek/Orta/Düşük | [Çözüm] |

### 7.3 BCNF Uyumluluk Kontrolü

| Tablo | 1NF | 2NF | 3NF | BCNF | Durum |
|-------|-----|-----|-----|------|-------|
| [tablo 1] | ✅ | ✅ | ✅ | ✅ | Uyumlu |
| [tablo 2] | ✅ | ✅ | ✅ | ✅ | Uyumlu |

---

## 8. Migration Stratejisi

### 8.1 Migration Kuralları

| Kural | Açıklama | İlgili ADR |
|-------|----------|------------|
| **Forward-only** | Geri dönüş yok | ADR-014 |
| **Versioned** | Her migration versiyonlu | ADR-014 |
| **Atomic** | Her migration atomik | ADR-014 |
| **Testable** | Her migration test edilebilir | ADR-014 |

### 8.2 Migration Dosyası

```php
<?php
// Migration: [migration_adı]
// ADR: ADR-NNN
// Tarih: YYYY-MM-DD

use CoreMusic\Database\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $this->pdo->exec("
            CREATE TABLE [tablo_adı] (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                [kolon] [tip] NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                is_deleted TINYINT(1) DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    public function down(): void
    {
        $this->pdo->exec("DROP TABLE IF EXISTS [tablo_adı]");
    }
};
```

### 8.3 Migration Sırası

```
1. auth → user → musics → catalog → albums → playlist → media → download → logs
```

---

## 9. Testing Strategy

### 9.1 Database Test Kapsamı

| Test Türü | Hedef | Araç |
|-----------|-------|------|
| **Unit Test** | %80+ | PHPUnit |
| **Integration Test** | %70+ | PHPUnit |
| **Schema Test** | %100 | PHPUnit |
| **Migration Test** | %100 | PHPUnit |

### 9.2 Test Senaryoları

| # | Senaryo | Türü | Beklenen Sonuç |
|---|---------|------|----------------|
| 1 | Tablo oluşturma | Schema | Başarılı |
| 2 | CRUD işlemleri | Unit | Başarılı |
| 3 | BCNF uyumluluk | Schema | Uyumlu |
| 4 | Migration çalıştırma | Migration | Başarılı |

### 9.3 Test Komutları

```bash
# Database Testler
cd music.coremusic.net && vendor/bin/phpunit --testsuite database

# Schema Validation
php bin/console doctrine:schema:validate

# Migration Status
php bin/console doctrine:migrations:status
```

---

## 10. Security Considerations

### 10.1 SQL Injection Koruması

| Kontrol | Durum | Detay |
|---------|-------|-------|
| **Prepared Statement** | ✅ Zorunlu | PDO parametrized queries |
| **Input Validation** | ✅ Zorunlu | Filter input |
| **Output Encoding** | ✅ Zorunlu | Escape output |
| **Least Privilege** | ✅ Zorunlu | DB user permissions |

### 10.2 Credential Yönetimi

| Veri | Saklama | Erişim |
|------|---------|--------|
| **DB Password** | AES-256-GCM | Credential Vault |
| **API Key** | AES-256-GCM | Credential Vault |
| **JWT Secret** | AES-256-GCM | Credential Vault |

### 10.3 Audit Trail

```sql
-- Audit log tablosu
CREATE TABLE audit_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    table_name VARCHAR(100) NOT NULL,
    record_id INT UNSIGNED NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON,
    new_values JSON,
    user_id INT UNSIGNED,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 11. Performance Impact

### 11.1 Query Performansı

| Query | Mevcut | Hedef | İndeks |
|-------|--------|-------|--------|
| [Query 1] | [ms] | < [ms] | [Index] |
| [Query 2] | [ms] | < [ms] | [Index] |

### 11.2 Connection Pool

| Parametre | Mevcut | Hedef |
|-----------|--------|-------|
| **Max Connections** | [Sayı] | [Sayı] |
| **Timeout** | [saniye] | [saniye] |
| **Retry** | [sayı] | [sayı] |

### 11.3 Cache Hit Ratio

| Cache | Hit Ratio | Hedef |
|-------|-----------|-------|
| **Query Cache** | [%] | > %90 |
| **Metadata Cache** | [%] | > %95 |

---

## 12. Rollback Plan

| Senaryo | Tetikleyici | Geri Alma Adımları |
|---------|-------------|-------------------|
| Migration hatası | DB hatası | 1. Migration'ı geri al 2. DB'yi restore et |
| Performance düşüşü | Query yavaşlama | 1. Index ekle 2. Query'yi optimize et |
| Data corruption | Veri bozulması | 1. Backup'tan restore 2. Audit trail'den kurtar |

---

## 13. Related Decisions

| ADR | Başlık | İlişki |
|-----|--------|--------|
| ADR-002 | PDO Mandatory, ORM Yasak | Ana kural |
| ADR-003 | 18 BCNF Veritabanı | Yapı |
| ADR-014 | Multi-DB Migration | Migration |
| ADR-022 | DB Hardened Security | Güvenlik |
| ADR-033 | SQL Normalization | Normalizasyon |
| ADR-040 | Database Authority | Otorite |

---

## 14. Glossary

| Terim | Tanım |
|-------|-------|
| **BCNF** | Boyce-Codd Normal Form |
| **PDO** | PHP Data Objects |
| **ORM** | Object-Relational Mapping (YASAK) |
| **Migration** | Veritabanı değişiklik yönetimi |
| **Forward-only** | Geri dönüşsüz migration |
| **Prepared Statement** | Parametreli sorgu |
| **Soft-delete** | Hard delete yerine is_deleted flag |

---

## 15. Edge Cases

| Durum | Belirti | Çözüm |
|-------|---------|-------|
| Connection timeout | DB bağlantı hatası | Retry + fallback |
| Deadlock | Transaction kilidi | Timeout + retry |
| Data inconsistency | Tutarsız veri | Transaction + audit |
| Migration conflict | Çakışan migration | Lock + serialized execution |

---

## 16. Warnings

> [!WARNING]
> **ORM Yasağı:** Doctrine, Eloquent veya herhangi bir ORM KESİNLİKLE yasaktır (ADR-002).

> [!WARNING]
> **SELECT * Yasağı:** Explicit column seçimi zorunludur (ADR-002).

> [!WARNING]
> **Hard Delete Yasağı:** Soft-delete zorunludur (ADR-022).

---

## 17. Limitations

| # | Sınırlama | Etki | Gelecek Çözüm |
|---|-----------|------|---------------|
| 1 | MySQL bağımlılığı | Orta | Multi-DB sync (ADR-050) |
| 2 | Sharding yok | Orta | Gelecekte düşünülebilir |
| 3 | Read replica yok | Düşük | Gelecekte düşünülebilir |

---

## 18. Dependencies

| Bağımlılık | Versiyon | Kullanım |
|------------|---------|---------|
| MySQL/MariaDB | 9+ | Ana DB |
| PHP PDO | 8.4+ | DB connection |
| AES-256-GCM | — | Credential şifreleme |

---

## 19. Future Roadmap

| Versiyon | Hedef | Tahmini |
|----------|-------|---------|
| v1.1 | Multi-DB sync (MSSQL, MongoDB) | 2026-Q4 |
| v2.0 | Read replica desteği | 2027-Q1 |
| v2.1 | Sharding stratejisi | 2027-Q2 |

---

## 20. Related Documents

| Dosya | Amaç |
|-------|------|
| [[architecture/05-data/database_master]] | Database master doc |
| [[.sql/coremusic_musics.sql]] | Schema dosyası |
| [[decisions/accepted/ADR-040-database-authority]] | DB otoritesi |

---

## 21. Cross References

```
ADR-NNN (Database)
    │
    ├─► decisions/accepted/ADR-002-pdo-mandatory-no-orm (PDO kuralı)
    │
    ├─► decisions/accepted/ADR-003-multi-db-9-databases (18 BCNF DB)
    │
    ├─► decisions/accepted/ADR-040-database-authority (BCNF)
    │
    ├─► architecture/05-data/database_master (DB master)
    │
    └─► .sql/ (SQL dosyaları)
```

---

## 22. Approval

| Rol | Kişi | Onay | Tarih |
|-----|------|------|-------|
| Data Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Backend Architect | [İsim] | ✅/❌ | YYYY-MM-DD |
| Security Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Vault Steward | [İsim] | ✅/❌ | YYYY-MM-DD |

---

*CoreMusic ADR Database Template v1.0.0 — 2026-08-07*
*Authority: Vault Steward*
*Governance: Red Team • Human Mode • Truth Mode*
