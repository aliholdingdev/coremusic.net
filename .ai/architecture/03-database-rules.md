---
title: "Database Rules — Veritabanı Kuralları ve Geliştirme Standartları"
type: architecture
category: data
date: 2026-09-03
updated: 2026-09-03
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
reference:
  authority: ".ai/architecture/03-database-rules.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md · .ai/architecture/05-data/database_master.md"
---

# Database Rules — Veritabanı Kuralları ve Geliştirme Standartları

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[brain.md]] · [[02-database-design.md]] · [[05-data/database_master.md]]

---

## 1. Amaç

CoreMusic veritabanı geliştirme süreçlerinde uyulması zorunlu kurallar, standartlar ve en iyi uygulamalar. Bu belge tüm backend geliştiricileri ve DBA'lar için SSOT niteliğindedir.

---

## 2. Normalizasyon İlkeleri

### 2.1 BCNF (Boyce-Codd Normal Form) Zorunluluğu

**Kural:** Tüm tablolar BCNF formunda olmalıdır.

| Kural | Açıklama |
|-------|----------|
| **1NF** | Her sütun atomik değer tutmalı, tekrar eden gruplar olmamalı |
| **2NF** | Tablo 1NF'de olmalı ve tüm BC alanları PK'ya tam bağımlı olmalı |
| **3NF** | Tablo 2NF'de olmalı ve transetif bağımlılık olmamalı |
| **BCNF** | Her belirleyici (determinant) candidate key olmalı |

### 2.2 Anomali Önleme Yöntemleri

| Anomali Türü | Önleme | Örnek |
|--------------|--------|-------|
| **Ekleme Anomalisi** | Referans tabloları ayır | `genres` tablosu ayrı tutulur |
| **Silme Anomalisi** | Soft delete + cascade stratejisi | `is_deleted = 0` ile silme |
| **Güncelleme Anomalisi** | Tekrarlanan verileri normalleştir | `roles` tablosu ayrı tutulur |

### 2.3 İlişkisel Modelleme Prensipleri

| Prensip | Açıklama |
|---------|----------|
| **Atomic Değerler** | Her sütun tek bir değer tutmalı |
| **Benzersiz Tanımlayıcı** | Her satır benzersiz PK ile tanımlanmalı |
| **Referans Bütünlüğü** | FK'lar mevcut PK'lara işaret etmeli |
| **Normal Form** | En az 3NF, tercihen BCNF |

---

## 3. İsimlendirme Standartları

### 3.1 Genel Kurallar

| Öğe | Format | Örnek | Yasak |
|-----|--------|-------|-------|
| **Veritabanı** | `coremusic_[alan]` | `coremusic_auth` | `CoreMusicAuth`, `core_music_auth` |
| **Tablo** | snake_case, çoğul | `user_roles` | `UserRoles`, `user_role` |
| **Sütun** | snake_case | `created_at` | `createdAt`, `CreatedAt` |
| **PK** | `id` | `id CHAR(36)` | `userId`, `ID`, `pk_id` |
| **FK** | `[tablo]_id` | `user_id`, `role_id` | `userId`, `user_Id` |
| **Boolean** | `is_[durum]` | `is_deleted`, `is_active` | `deleted`, `active` |
| **Timestamp** | `[eylem]_at` | `created_at`, `updated_at` | `createDate`, `timestamp` |

### 3.2 Constraint İsimlendirme

| Constraint Türü | Format | Örnek |
|-----------------|--------|-------|
| **Primary Key** | `pk_[tablo]` | `pk_users` (otomatik oluşturulur) |
| **Foreign Key** | `fk_[tablo]_[hedef_tablo]` | `fk_user_roles_users` |
| **Unique** | `uk_[tablo]_[sütunlar]` | `uk_users_email` |
| **Check** | `ck_[tablo]_[kosul]` | `ck_users_status` |

### 3.3 İndeks İsimlendirme

| İndeks Türü | Format | Örnek |
|-------------|--------|-------|
| **Tek Sütun** | `idx_[tablo]_[sütun]` | `idx_users_email` |
| **Bileşik** | `idx_[tablo]_[sütun1]_[sütun2]` | `idx_user_roles_user_role` |
| **Full-Text** | `ft_[tablo]_[sütunlar]` | `ft_search_index_title` |
| **Partial** | `idx_[tablo]_[sütun]_partial` | `idx_users_email_partial` |

### 3.4 Trigger İsimlendirme

| Trigger Türü | Format | Örnek |
|-------------|--------|-------|
| **Before Insert** | `trg_[tablo]_before_insert` | `trg_users_before_insert` |
| **Before Update** | `trg_[tablo]_before_update` | `trg_users_before_update` |
| **After Insert** | `trg_[tablo]_after_insert` | `trg_users_after_insert` |
| **After Update** | `trg_[tablo]_after_update` | `trg_users_after_update` |

---

## 4. Veri Güvenliği ve Silme Politikası

### 4.1 Soft Delete Kuralları

**Kural:** Hard delete kesinlikle yasaktır. Tüm silme işlemleri `is_deleted` bayrağı ile yapılır.

```sql
-- Yasak: Hard delete
DELETE FROM users WHERE id = '...';

-- Doğru: Soft delete
UPDATE users SET is_deleted = 1, deleted_at = CURRENT_TIMESTAMP WHERE id = '...';

-- Doğru: Soft delete ile sorgulama
SELECT id, email FROM users WHERE is_deleted = 0;
```

### 4.2 is_deleted Bayrağı Kullanımı

| Durum | Değer | Açıklama |
|-------|-------|----------|
| **Aktif** | `is_deleted = 0` | Varsayılan durum |
| **Silinmiş** | `is_deleted = 1` | Soft delete uygulanmış |
| **Null** | `is_deleted IS NULL` | Eski veriler için (migration) |

### 4.3 Unique Index Çakışma Önleme

Soft delete ile unique constraint çakışmalarını önleme stratejileri:

| Stratejim | Açıklama | Örnek |
|-----------|----------|-------|
| **Partial Index** | Sadece aktif kayıtları içerir | `UNIQUE KEY uk_users_email (email) WHERE is_deleted = 0` |
| **Composite Unique** | `is_deleted`'i dahil eder | `UNIQUE KEY uk_users_email_active (email, is_deleted)` |
| **Soft Delete Awareness** | INSERT/UPDATE'te kontrol | Uygulama katmanında kontrol |

**Önerilen Yaklaşım:** MySQL 8.0+ ile partial index desteği sınırlı olduğu için **composite unique** yaklaşımı tercih edilir:

```sql
-- Doğru: Composite unique ile soft delete awareness
UNIQUE KEY `uk_users_email_is_deleted` (`email`, `is_deleted`)

-- INSERT'te kontrol:
-- 1. Mevcut aktif kayıt var mı? → Hata
-- 2. Silinmiş kayıt var mı? → Güncelle (reactivate)
-- 3. Hiç kayıt yok? → Ekle
```

### 4.4 Cascade Stratejisi

| Cascade Türü | Kullanım | Açıklama |
|--------------|----------|----------|
| **CASCADE** | Sadece assistive tablolar | Örn: `playlist_items` → `playlists` |
| **SET NULL** | Opsiyonel ilişkiler | Örn: `user_sessions.user_id` |
| **RESTRICT** | Kritik ilişkiler | Örn: `users` → `user_roles` |
| **NO ACTION** | Varsayılan | Güvenli alternatif |

**Kural:** Cross-DB FK'lar uygulama katmanında yönetilir (MySQL farklı DB'ler arası FK desteklemez).

---

## 5. Kimlik Doğrulama ve Anahtar Yönetimi

### 5.1 Hibrit UUID Kullanımı

| Katman | Format | Üretim | Kullanım |
|--------|--------|--------|----------|
| **Uygulama (PHP)** | `CHAR(36)` | `Ramsey\Uuid\Uuid::uuid7()` | Tüm yeni kayıtlar |
| **MySQL (DB)** | `CHAR(36)` | `UUID()` | Fallback |
| **Eski Tablolar** | `BINARY(16)` | `UUID_TO_BIN()` | Geriye uyumluluk |

### 5.2 UUID Üretim Mekanizması

```php
// PHP tarafında UUID v7 üretimi
use Ramsey\Uuid\Uuid;

$uuid = Uuid::uuid7()->toString();
// Sonuç: '550e8400-e29b-71d4-a716-446655440000'
```

```sql
-- MySQL tarafında UUID üretimi (fallback)
SELECT UUID();
-- Sonuç: '550e8400-e29b-41d4-a716-446655440000'
```

### 5.3 Çakışma Önleme Prensipleri

| Prensip | Açıklama |
|---------|----------|
| **UUID v7** | Time-based, sıralı, çakışma riski yok |
| **UNIQUE Constraint** | Ek garanti katmanı |
| **Application-Level Check** | INSERT öncesi kontrol |
| **Retry Mechanism** | Çakışma durumunda yeniden deneme |

### 5.4 Güvenlik Kuralları

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|--------------|
| **Secret Management** | Hassas veriler `.env`'da | Güvenlik açığı |
| **Hashing** | Şifreler Argon2id ile hashlenmiş | Veri sızıntısı |
| **No Hardcoded Secrets** | API key, password kodda yok | Güvenlik ihlali |
| **Audit Trail** | Kritik işlemler log'lanır | İzlenebilirlik |

---

## 6. Hareket Yönetimi (Transactions)

### 6.1 Varsayılan İzolasyon Seviyesi

**Kural:** Sistem genelinde `REPEATABLE READ` izolasyon seviyesi kullanılır.

```sql
-- Varsayılan izolasyon seviyesi
SET SESSION transaction_isolation = 'REPEATABLE-READ';

-- Tekrar okunabilirlik izolasyonu
START TRANSACTION WITH CONSISTENT SNAPSHOT;
-- ... SQL işlemleri ...
COMMIT;
```

### 6.2 Transaction Kuralları

| Kural | Açıklama |
|-------|----------|
| **ACID Uyumlu** | Atomiklik, Tutarlılık, İzolasyon, Dayanıklılık |
| **Kısa Süreli** | Transaction mümkün olduğunca kısa tutulmalı |
| **Deadlock Önleme** | Tablo sırası tutarlı olmalı |
| **Retry Mechanism** | Deadlock durumunda yeniden deneme |

### 6.3 Lock Stratejisi

| Lock Türü | Kullanım | Açıklama |
|-----------|----------|----------|
| **Row Lock** | Tek satır işlemleri | Varsayılan, önerilen |
| **Table Lock** | Toplu güncellemeler | Dikkatli kullanılmalı |
| **Advisory Lock** | Uygulama kilidi | Dağıtılmış sistemler için |

### 6.4 Hata Yönetimi

```sql
-- Transaction ile hata yönetimi
DECLARE EXIT HANDLER FOR SQLEXCEPTION
BEGIN
  ROLLBACK;
  RESIGNAL;
END;

START TRANSACTION;
-- ... SQL işlemleri ...
COMMIT;
```

---

## 7. Sürüm Takibi ve Geçişler

### 7.1 Forward-Only Migration

**Kural:** Yalnızca ileri yönlü çalışan veritabanı şema yönetim prensipleri.

| Kural | Açıklama |
|-------|----------|
| **Forward-Only** | Geri alma (rollback) yok |
| **Versioned** | Her migration versiyonlu |
| **Idempotent** | Tekrar çalıştırılabilir |
| **Test Edilmiş** | Production öncesi test edilmeli |

### 7.2 Migration Dosya Formatı

```
[YEAR]-[MONTH]-[DAY]-[HH]-[MM]-[SEQUENCE]-[DESCRIPTION].sql

Örnekler:
2026-09-03-15-00-01-create-users-table.sql
2026-09-03-15-00-02-add-email-index.sql
2026-09-03-15-00-03-create-roles-table.sql
```

### 7.3 Migration Kuralları

| Kural | Açıklama |
|-------|----------|
| **Her Değişiklik Ayrı** | Tek migration dosyası = tek değişiklik |
| **Backward Compatible** | Eski kod hala çalışmalı |
| **Data Migration** | Veri taşıma ayrı migration |
| **Rollback Yok** | Geri alma yerine düzeltme migration'ı |

### 7.4 Migration Sırası

```
1. Schema oluşturma (CREATE DATABASE/TABLE)
2. Index oluşturma
3. Constraint oluşturma
4. Trigger oluşturma
5. Veri taşıma (data migration)
6. View oluşturma
7. Stored procedure oluşturma
```

---

## 8. Performans Standartları

### 8.1 İndeksleme Stratejisi

| Alan Türü | İndeks Stratejisi |
|-----------|-------------------|
| **FK Alanları** | Zorunlu index |
| **WHERE koşulu** | Zorunlu index |
| **ORDER BY** | İndeks ile desteklenmeli |
| **GROUP BY** | İndeks ile desteklenmeli |
| **UNIQUE alanlar** | Unique index |
| **Full-text arama** | Full-text index |

### 8.2 Sorgu Optimizasyonu

| Teknik | Açıklama |
|--------|----------|
| **EXPLAIN** | Sorgu planı analizi |
| **覆盖 Index** | Covering index ile表transferi önleme |
| **Pagination** | OFFSET yerine cursor-based pagination |
| **Batch Operations** | Toplu INSERT/UPDATE |

### 8.3 Ölçekleme Stratejisi

| Teknik | Kullanım Alanı |
|--------|----------------|
| **Read Replica** | Okuma yoğun sorgular |
| **Partitioning** | Büyük tablolar (tarih bazlı) |
| **Sharding** | Coğrafi dağılım |
| **Cache Layer** | Sık sorgulanan veriler |

---

## 9. Güvenlik Standartları

### 9.1 SQL Injection Önleme

| Önlem | Açıklama |
|-------|----------|
| **Prepared Statement** | Tüm sorgular prepared statement |
| **Input Validation** | Girdi doğrulama |
| **Parameter Binding** | Parametre bağlama |
| **Stored Procedures** | Hassas işlemler için |

```php
// Doğru: Prepared statement
$stmt = $pdo->prepare('SELECT id, email FROM users WHERE email = :email AND is_deleted = 0');
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();

// Yasak: String concatenation
$pdo->query("SELECT * FROM users WHERE email = '$email'");
```

### 9.2 Erişim Kontrolü

| Katman | Kontrol |
|--------|---------|
| **DB Level** | MySQL user/grant sistemi |
| **Uygulama Level** | RBAC + Permission |
| **Query Level** | Row-level security |
| **Column Level** | Sütun bazlı erişim |

### 9.3 Veri Şifreleme

| Veri Türü | Şifreleme | Açıklama |
|-----------|-----------|----------|
| **Şifreler** | Argon2id | One-way hash |
| **Credential'lar** | AES-256-GCM | Two-way encryption |
| ** Hassas Veriler** | AES-256-GCM | Uygulama katmanında |
| **At Rest** | MySQL Encryption | Disk düzeyinde |

---

## 10. Kod İnceleme Kontrol Listesi

### 10.1 Veritabanı Kod İncelemesi

```
[ ] Prepared statement kullanıldı mı?
[ ] SELECT * kullanıldı mı? (YASAK)
[ ] Explicit column listesi var mı?
[ ] Soft delete (is_deleted) kontrol edildi mi?
[ ] BCNF uyumu sağlandı mı?
[ ] snake_case naming kullanıldı mı?
[ ] Index'ler tanımlandı mı?
[ ] Transaction kullanıldı mı (gerekirse)?
[ ] Error handling var mı?
[ ] Audit log kaydı yapılıyor mu?
```

### 10.2 Migration İncelemesi

```
[ ] Forward-only mi?
[ ] Idempotent mi? (Tekrar çalıştırılabilir)
[ ] Backward compatible mi?
[ ] Test edildi mi?
[ ] Rollback planı var mı? (Düzeltme migration'ı)
[ ] Documentasyon eklendi mi?
```

---

## 11. Hatalı Örüntüler

| Yasak | Doğru | Neden |
|-------|-------|-------|
| `SELECT * FROM users` | `SELECT id, email FROM users` | SQL injection riski, performans |
| `DELETE FROM users WHERE id = 1` | `UPDATE users SET is_deleted = 1 WHERE id = 1` | Veri kaybı |
| `INSERT INTO users (email, name) VALUES ('$email', '$name')` | Prepared statement | SQL injection |
| `CREATE TABLE users (userId INT)` | `CREATE TABLE users (id INT)` | Naming standardı |
| `ALTER TABLE users ADD INDEX idx_email (email)` | `CREATE INDEX idx_users_email ON users (email)` | Naming standardı |
| `UPDATE users SET name = 'test'` (WHERE yok) | `UPDATE users SET name = 'test' WHERE id = 1` | Tüm tabloyu güncelleme |

---

## 12. Acil Durum Prosedürleri

### 12.1 Veri Kaybı Durumu

1. **DUR** — Hiçbir işlem yapma
2. **Log'ları Kontrol Et** — Ne olduğunu anla
3. **Backup'tan Geri Yükle** — En son backup'ı kullan
4. **Audit Trail** — `audit_logs`'dan değişiklikleri takip et
5. **Raporla** — Durumu raporla

### 12.2 Performance Düşüşü

1. **Slow Query Log** — Yavaş sorguları tespit et
2. **EXPLAIN** — Sorgu planını analiz et
3. **Index Eksikliği** — Eksik index'leri tespit et
4. **Lock Analizi** — Lock beklemelerini kontrol et
5. **Optimizasyon** — Gerekli optimizasyonları uygula

### 12.3 Güvenlik İhlali

1. **DUR** — Hiçbir işlem yapma
2. **Erişimi Kısıtla** — DB erişimini kısıtla
3. **Log'ları Koru** — Logları silme
4. **Raporla** — Güvenlik ekibine bildir
5. **Düzeltme Uygula** — Güvenlik yaması uygula

---

## 13. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 Normalizasyon | [[ADR-040-database-authority]] | BCNF kuralları |
| § 3 İsimlendirme | [[brain.md]] §18 | Coding Standards |
| § 4 Soft Delete | [[ADR-022-database-hardened-security]] | DB güvenlik |
| § 5 UUID | [[brain.md]] §10 | PHP Security |
| § 6 Transaction | [[ADR-040-database-authority]] | İzolasyon seviyesi |
| § 7 Migration | [[ADR-014-forward-only-migration]] | Migration stratejisi |
| § 9 Güvenlik | [[ADR-022-database-hardened-security]] | Şifreleme |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-03
**Mode:** Red Team · Human Mode · Truth Mode
