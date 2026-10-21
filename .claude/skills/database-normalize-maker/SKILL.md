---
name: database-normalize-maker
description: "Use when normalizing a table to BCNF, deriving candidate keys, detecting functional dependencies, auditing a schema, or writing migration SQL for MySQL 8.0 / CoreMusic databases — Tetikleyiciler: 'normalize', 'bcnf', 'tablo oluştur', 'schema', 'migration', 'sql yaz', 'index oluştur', 'normalizasyon', 'aday anahtar', 'fonksiyonel bagimlilik'."
license: MIT
metadata:
  version: 4.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: database
  tags: [bcnf, 3nf, normalization, mysql, innodb, schema, migration, index, explain]
  updated: 2026-10-21
  previous-version: "3.0.0"
---

# Veritabanı Normalizasyon & Şema Üretim Motoru

> **Hedef motor:** MySQL 8.0 (InnoDB, utf8mb4).
> **Domain sahibi:** Data Engineer (Expert #3). Şema/normalizasyon kararları bu skill'den üretilir.
> **SSOT:** `.ai/.sql/mysql/*.sql` + ADR-040 (database authority). Bu skill şablon/jenerik üretim yapar; gerçek envanter sayıları vault'tadır.

---

## §1. Bu Skill Ne Yapar (Kapsam)

Girdi: bir gereksinim, ham bir tablo tanımı veya mevcut bir `CREATE TABLE` bloğu.
Çıktı: BCNF uyumlu şema + index stratejisi + güvenli migration + doğrulama raporu.

**Yapar:**
- Fonksiyonel bağımlılık çıkarımı ve **aday anahtar (candidate key)** tespiti.
- 1NF → 2NF → 3NF → BCNF (gerekiyorsa 4NF) normalizasyonu.
- MySQL 8.0 uyumlu `CREATE TABLE` / `ALTER TABLE` / index üretimi.
- **Expand-and-contract** (zero-downtime) migration planı.
- Sorgu planı denetimi: `EXPLAIN`, `EXPLAIN ANALYZE`, N+1 tespiti.
- Soft-delete (paranoid) ve audit-trail desenleri.
- Şema → Mermaid ER diyagramı dönüşümü.

**Yapmaz (domain yasakları — ADR):**
- ORM üretmez (ADR-002: ORM yasak — yalnız PDO prepared statements).
- Framework şablonu üretmez (ADR-001).
- `SELECT *` üretmez (ADR-014: açık kolon listesi zorunlu).
- Hard delete üretmez (soft delete zorunlu).
- `FLOAT` para birimi üretmez (`DECIMAL` zorunlu).
- Kritik prod DDL'yi onaysız uygulamaz (root onayı gerekir).

**Temel zihniyet (50 yıllık senior):** Kod yazmak en ucuz adımdır. Önce doğru problemi bul — hangi tablo fazlalığı, hangi bağımlılık bozuk, hangi sorgu yavaş. Varsayımını yaz, kanıtla, sonra SQL üret. Tahmin = hata.

---

## §2. Zero-Hallucination & Versiyon Disiplini

1. Doğrulanamayan iddia → `VERIFICATION REQUIRED`. Uydurulan kolon/tablo/ADR/sürüm yasak.
2. **Sürüm netliği:** Bu skill **MySQL 8.0 (LTS)** hedefler. CoreMusic vault'ta bazı metinler "MySQL 9" (Innovation hattı) geçebilir — **deploy edilen tam sürüm `VERIFICATION REQUIRED`**. 8.0 feature'ları için min sürüm notu: `EXPLAIN ANALYZE` = 8.0.18+, çok-değerli JSON index = 8.0.17+, CHECK enforce = 8.0.16+, descending index = 8.0.0+.
3. **Bilinen yanlış inanış (bu skill düzeltir):** MySQL'de PostgreSQL tarzı **partial/filtered index (`CREATE INDEX ... WHERE`) YOKTUR**. Yerine: composite index + sorgu sıralaması, veya `GENERATED` kolon + index, veya `INDEX` ile `WHERE` sargısını eşleme. Eklerken sürüm feature'ını doğrula.
4. Her üretim, §11 kalite kapısından (`references/checklist.md`) geçmeden "tamam" sayılmaz.

---

## §3. Fonksiyonel Bağımlılık & Aday Anahtar

### 3.1 Temel tanımlar

| Terim | Tanım |
|-------|-------|
| Fonksiyonel bağımlılık (FD) | `X → Y`: X değeri belirlendiğinde Y tek değer alır. |
| Armstrong aksiomları | reflexivity, augmentation, transitivity (+ türetilen: union, decomposition). |
| Kapanım (closure) `X+` | X'ten türetilebilir tüm attribute kümesi. |
| Superkey | Kapanımı tüm attribute'ları içeren attribute kümesi. |
| Aday anahtar | **Minimal** superkey — alt kümesi superkey olmayan. Birden fazla olabilir. |
| Prime attribute | Bir aday anahtarın üyesi olan attribute. Non-prime: hiçbir aday anahtarın üyesi değil. |

### 3.2 Aday anahtar bulma yöntemi (deterministik)

1. Tüm attribute kümesini `U` al.
2. Her aday `X ⊆ U` için `X+` hesapla (Armstrong ile).
3. `X+ = U` ve hiçbir alt kümesinin `X+ = U` olmadığı `X`'leri aday anahtar say.
4. `PRIMARY KEY` bir aday anahtar seçilir; kalanlar `UNIQUE` constraint olarak korunur (bilgi kaybını önler).

```text
Örnek FD: {student_id, course_id} → instructor_id, instructor_name, room
          instructor_id → instructor_name, room   (transitive / BCNF ihlali)
Aday anahtar: (student_id, course_id)   — minimal superkey
instructor_id determinant ama superkey DEĞİL → BCNF ihlali → instructors tablosuna ayır.
```

### 3.3 Normal formlar (sıkı tanımlar)

| NF | Koşul | İhlal tipi | Çözüm |
|----|-------|-----------|-------|
| 1NF | Attribute'lar atomik; tekrarlayan grup yok; PK var | Array/virgülle liste, `phone1,phone2,...` | Ayırıcı tablo (pivot) |
| 2NF | 1NF + non-prime attribute'lar **tamamen** PK'ya bağımlı | Kısmi bağımlılık (composite PK'da bir key parçasına bağımlı) | Parça + tümünü ayrı tabloya |
| 3NF | 2NF + non-prime attribute'lar **yalnız** aday anahtara bağımlı | Transitive bağımlılık (`A → B → C`, B non-prime) | `B → C`'yi ayrı tabloya |
| BCNF | Her non-trivial FD `X → Y` için **X superkey** | Determinant superkey değil | Determinant'ı aday anahtar yap veya bö |
| 4NF | BCNF + non-trivial multivalued dependency `X ↠ Y` yok | Bağımsız çok-değerli attribute tek tabloda | Her `X ↠ Y` için ayrı tablo |

#### 3.3.1 Denetim sırası (pratik — tabloyu yukarıdan aşağı uygula)

1. PK var mı? → yoksa **1NF ihlali**.
2. Composite PK'da non-prime bir attribute yalnız bir key parçasına mı bağımlı? → **2NF ihlali**.
3. Non-prime attribute, PK **dışı** bir determinant'a mı bağımlı (`A → B → C`)? → **3NF ihlali**.
4. Determinant superkey değil mi? → **BCNF ihlali**.
5. Birbirinden bağımsız çok-değerli attribute tek tabloda mı? → **4NF ihlali**.

Sıra önemlidir: 3NF ihlali BCNF'yi de ihlal eder; önce 3NF'yi düzelt, BCNF'yi sonra doğrula.

```sql
-- BCNF İHLALİ (transitive): department_id → department_name, department_head
-- Aday anahtar: (emp_id) ; FD: emp_id → dept_id → dept_name
CREATE TABLE employees_bcnf_violation (
    emp_id       BIGINT UNSIGNED NOT NULL,
    dept_id      BIGINT UNSIGNED NOT NULL,
    dept_name    VARCHAR(100) NOT NULL,   -- non-prime, dept_id'ye bağımlı (transitive)
    dept_head    VARCHAR(100) NOT NULL,   -- non-prime, dept_id'ye bağımlı (transitive)
    PRIMARY KEY (emp_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- BCNF UYUMLU: dept_id → dept_name, dept_head bağımsız tabloya taşındı
CREATE TABLE departments (
    id      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name    VARCHAR(100) NOT NULL,
    head    VARCHAR(100) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_departments_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE employees (
    id        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    dept_id   BIGINT UNSIGNED NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name  VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_employees_dept_id (dept_id),
    CONSTRAINT fk_employees_departments FOREIGN KEY (dept_id) REFERENCES departments (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

> **Collation notu:** MySQL 8.0 varsayılan collation `utf8mb4_0900_ai_ci`'dir (8.0.1+). `utf8mb4_unicode_ci` da geçerlidir. CoreMusic'te hangisinin kullanıldığı vault'ta — **tutarlılık tek collation'da olmalı, karıştırma `VERIFICATION REQUIRED`.**

### 3.4 Ne zaman bilinçli denormalizasyon?

Yalnız üç şartta ve **ADR ile gerekçelendirilerek**:

| Şart | Gerekçe | Zorunlu eşlik |
|------|---------|---------------|
| Tarihsel dondurma | Audit snapshot, log bütünlüğü | `_audit` tablosu |
| Ölçülmüş ağır okuma | Join maliyeti `EXPLAIN` ile kanıtlanmış | Covering index veya materialized özet |
| Raporlama önbelleği | Okuma/yazma oranı çok yüksek | `updated_at` ile senkron trigger/job |

Denormalize kolon eklenirken `CHECK` veya uygulama katmanı ile tutarlılık zorlanır; tek gerçek kaynak (source of truth) normalize tablo kalır.

---

## §4. MySQL 8.0 Şema Standartları (CoreMusic)

### 4.1 Zorunlu tablo iskeleti

```sql
CREATE TABLE example_entity (
    id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    -- ... iş kolonları (snake_case, atomic) ...
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_example_entity_natural (natural_key_col),
    KEY idx_example_entity_lookup (lookup_col),
    CONSTRAINT fk_example_child_parent FOREIGN KEY (parent_id) REFERENCES example_parent (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='...';
```

### 4.2 İsimlendirme (snake_case, tutarlı)

| Öğe | Kalıp | Örnek |
|-----|-------|-------|
| Tablo | çoğul / varlık, snake_case | `users`, `order_items` |
| Kolon | snake_case | `first_name`, `created_at` |
| PK | `id` | `id BIGINT UNSIGNED` |
| FK | `{tablo}_id` | `user_id` |
| Unique | `uk_{tablo}_{kolon}` | `uk_users_email` |
| Index | `idx_{tablo}_{kolon}` | `idx_orders_created_at` |
| FK constraint | `fk_{çocuk}_{ana}` | `fk_orders_users` |
| View | `v_{amaç}` | `v_active_users` |
| Audit | `{tablo}_audit` | `users_audit` |

### 4.3 Veri tipi seçimi (ölçülmüş, tahminsiz)

| Amaç | Tip | Not (8.0) |
|------|-----|-----------|
| Surrogate PK | `BIGINT UNSIGNED AUTO_INCREMENT` | 8 byte; UUID istenirse `BINARY(16)` (ama PK olarak değil, natural key destek) |
| Sayı / sayaç | `INT UNSIGNED` (4B), `SMALLINT` (2B), `TINYINT` (1B) | Aralık hesabıyla seç |
| Boolean | `TINYINT(1)` | MySQL `BOOLEAN` = `TINYINT(1)` alias'ı |
| Para | `DECIMAL(p,s)` | **Kesin** numeric. FLOAT/DOUBLE yasak (yuvarlama hatası) |
| Kısa metin | `VARCHAR(n)` | `n` karakter (utf8mb4'te 4 byte/char'e kadar) — gerçek ihtiyaca göre |
| Uzun metin | `TEXT` / `MEDIUMTEXT` / `LONGTEXT` | Satır boyunu şişirir; büyük blob'lar ayrı tablo |
| Tarih-saat (tz) | `TIMESTAMP` | 1970–2038 arası, UTC saklar, session tz ile gösterir |
| Tarih-saat (geniş) | `DATETIME` | 1000–9999, tz yok, daha geniş aralık |
| Sadece tarih | `DATE` | Doğum tarihi, sipariş günü |
| Esnek/JSON | `JSON` | 5.7+; `JSON_EXTRACT`, 8.0.17+ çok-değerli index |
| Sabit-küme | Lookup tablosu | ENUM yerine (ALTER kolaylığı) |

**TIMESTAMP vs DATETIME kararı:** “Oluşturuldu/anında” için `TIMESTAMP` (tz-duyarlı, 4B); “iş tarihi/gelecek tarih” için `DATETIME` (2038 sorunu yok). 2038 sonrası oluşum kayıtları gerekiyorsa `DATETIME` tercih edilir — bu proje kararına bağlı, **`VERIFICATION REQUIRED`.**

#### VARCHAR uzunluk kılavuzu (keyfi `255` yasak)

Uzunluk **gereksinimden** türetilir; “her yere 255” niyeti gizler ve utf8mb4'te satır/içindekiler index'ini şişirir.

| Alan | Önerilen uzunluk | Gerekçe |
|------|------------------|---------|
| `email` | `VARCHAR(255)` | Tek yerel-part sınırı; 254 giriş + güvenlik payı |
| `username` | `VARCHAR(32)` | Uygulama kuralı (min 3 / max 32) ile eşleşmeli |
| `slug` | `VARCHAR(80)` | URL parçası; uzun slug SEO'ya zararlı |
| Başlık (`title`) | `VARCHAR(160)` | Türkçe karakter + arama motoru snippet sınırı |
| ISO kod (ISRC, ülke) | `CHAR(n)` | Sabit uzunluk — VARCHAR israfı |
| `password_hash` | `VARCHAR(255)` | Argon2id/bcrypt hash şişmesi için pay bırak |

**Karar akışı:** gerçek gereksinim var mı? → evet: ölçüyü yaz (kural, standart, DB limiti). yok → tahmin etme, **soru sor**.

### 4.4 Soft delete (paranoid) & audit

```sql
-- Soft delete: hard delete YASAK. Tüm sorgular deleted_at IS NULL filtresi taşır.
UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = 1;
SELECT id, email FROM users WHERE deleted_at IS NULL AND is_active = 1;

-- Unique dikkati: soft delete + UNIQUE çakışır. Çözüm: composite unique (deleted_at NULL).
-- MySQL'de NULL'lar unique'de çakışmaz → (email, deleted_at) unique iki "silinmiş" aynı email'e izin verir.
```

Audit deseni ve trigger örneği: [references/04-security-audit.md](references/04-security-audit.md).

---

## §5. İlişki Kalıpları

| Kalıp | SQL deseni | Not |
|-------|-----------|-----|
| One-to-Many | Çocukta `parent_id` + FK + `idx` | `ON DELETE` stratejisi seç (RESTRICT/CASCADE/SET NULL) |
| Many-to-Many | Junction tablo + iki FK + composite unique | `UNIQUE(child_a, child_b)` tekrarı engeller |
| One-to-One | Çocukta `parent_id UNIQUE` | Genelde profil/ayar genişletme |
| Self-referencing | `parent_id NULL` + `ON DELETE SET NULL` | Kategori ağacı, comment thread |
| Polymorphic | **ÖNERİLMEZ** — FK bütünlüğü yok | Ayrı tablalar veya `type + id` + uygulama katmanı denetimi |

```sql
-- Many-to-Many (junction) — tekrarsız
CREATE TABLE playlist_track (
    playlist_id BIGINT UNSIGNED NOT NULL,
    track_id    BIGINT UNSIGNED NOT NULL,
    position    INT UNSIGNED NOT NULL DEFAULT 0,
    added_at    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (playlist_id, track_id),
    KEY idx_playlist_track_track (track_id),
    CONSTRAINT fk_playlist_track_playlist FOREIGN KEY (playlist_id) REFERENCES playlists (id) ON DELETE CASCADE,
    CONSTRAINT fk_playlist_track_track    FOREIGN KEY (track_id)    REFERENCES tracks (id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

Detay: [references/06-schema-generation.md](references/06-schema-generation.md).

### 5.1 ON DELETE strateji seçimi (karar tablosu)

FK `ON DELETE` davranışı **bilinçli seçilir; varsayılan (RESTRICT) sessiz uygulanmaz.**

| Durum | Strateji | Gerekçe | Uygulama etkisi |
|-------|----------|---------|-----------------|
| Silme yasak, kayıt referanslı (sipariş→kullanıcı) | `RESTRICT` | Veri bütünlüğü | Sorgularda `deleted_at IS NULL` şart |
| Tam hiyerarşi birlikte yaşar (kategori altları) | `CASCADE` | Yetim kayıt kalmasın | Sorgu yazan taraf cascade'i bilmeli |
| Silinince değer “boş” kalmalı (yorum→ebeveyn) | `SET NULL` | Referans korunur | Kolon `NULL` olabilmeli + `idx` şart |
| Sayaç/denetim kolonu | `CASCADE` (FK) + uygulama adımı | Sayacın düşmesi gerekmez | Trigger yerine uygulama katmanı |

```sql
-- RESTRICT (varsayılan niyetli seçim): silinen kullanıcıyı sipariş bağımlı kılar
CONSTRAINT fk_orders_users FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE;
```

> MySQL'de `ON UPDATE CASCADE` yalnız PK/unique kolon değişince tetiklenir; `AUTO_INCREMENT` id'lerde pratikte tetiklenmez.

---

## §6. İndeks Stratejisi & Sorgu Planı

### 6.1 Composite index sıralama kuralı

`EQUALITY → EQUALITY → RANGE/SORT` (soldan sağa):

```sql
-- Sorgu: WHERE user_id = ? AND status = ? ORDER BY created_at DESC
CREATE INDEX idx_orders_user_status_created ON orders (user_id, status, created_at);
```

### 6.2 Temel kurallar

| Kural | Gerekçe |
|-------|---------|
| Her FK'ya index | InnoDB JOIN/DELETE için index ister; yetim sorgu yavaşlar |
| Covering index | `SELECT` kolonları index'te ise table access yok (Using index) |
| Eşitlik önce, aralık sonra | Range sonrası kolonlar index'de verimli kullanılmaz |
| Over-indexing kaçın | Her index yazma (INSERT/UPDATE) maliyetini artırır |
| Prefix index yalnız uzun string'de | `KEY idx_name (name(30))` — tam unique için değil |
| Sorgu sırasını index'e uydur | `ORDER BY`/`GROUP BY` index sırasıyla eşleşirse filesort önlenir |

### 6.3 EXPLAIN & N+1

```sql
EXPLAIN SELECT u.id, u.email, o.total
FROM users u JOIN orders o ON o.user_id = u.id
WHERE u.deleted_at IS NULL AND o.status = 'paid';

-- 8.0.18+: gerçek metrik (execution time, actual rows)
EXPLAIN ANALYZE SELECT ... ;
```

| İşaret | Anlamı | Aksiyon |
|-------|--------|---------|
| `type: ALL` | Full table scan | İlgili kolona/composite index ekle |
| `Using filesort` | Sıralama index dışı | ORDER BY'ı index sırasına uydur |
| `Using temporary` | Geçici tablo (GROUP BY/UNJOIN) | Composite index ile group'u önle |
| `rows` çok yüksek | Tahmini tarama genişliği | Index kapsamını daralt |
| `Using index` | Covering index | İyi |

N+1: döngü içinde tekrarlanan tekil SELECT → `WHERE id IN (...)` veya JOIN ile toplu çek. Ayrıntı: [references/05-performance-optimization.md](references/05-performance-optimization.md).

### 6.4 N+1 tespit ve çözüm (somut)

```sql
-- N+1 HATALI: döngü başına 1 sorgu (N şarkı → N+1 SELECT)
--   foreach ($trackIds as $id) { $stmt = $pdo->prepare('SELECT title, duration_ms FROM tracks WHERE id = ?'); ... }

-- DOĞRU: tek prepared statement (IN listesi placeholder sayısı kadar)
SELECT id, title, duration_ms FROM tracks WHERE id IN (?, ?, ..., ?) AND deleted_at IS NULL;
```

| İşaret | Anlamı |
|--------|--------|
| Ayrıntı yükü ayrı sorgu (track → artist) | `JOIN artists` ile tek sorguda çek |
| N+1'de `id IN (...)` | Placeholder sayısını batch'e böl (ör. 500) |
| Uzun IN listesi | `IN (SELECT ... FROM temp)` veya junction JOIN |

---

## §7. Migration Güvenliği (Expand-and-Contract)

Tehlikeli değişiklik (kolon drop/rename, tip değişimi, NOT NULL ekleme, backfill) **3 faz**:

```sql
-- FAZ 1 EXPAND: yeni yapı ekle, eskiyi koru (uyumluluk)
ALTER TABLE users ADD COLUMN email_canonical VARCHAR(255) NULL;
-- uygulama artık hem eski hem yeni kolona yazar (dual-write), backfill job'ı hazırla.

-- FAZ 2 MIGRATE: chunk'lı backfill (lock kısa tutulur)
-- UPDATE users SET email_canonical = LOWER(email) WHERE id BETWEEN ? AND ? ... (batch)

-- FAZ 3 CONTRACT: doğrulama + onay SONRASI eskiyi kaldır
-- root onayı ile: ALTER TABLE users DROP COLUMN email, CHANGE email_canonical email ...
```

| Kural | Ayrıntı |
|-------|---------|
| Forward-only | Prod'da down migration riskli; geri dönüş yeni bir forward migration'dır. SSOT down() çelişkisi varsa `VERIFICATION REQUIRED` |
| Backfill batch | Tek büyük UPDATE lock üretir; `id` aralığıyla parçala |
| Lock süresi | `LOCK_WAIT_TIMEOUT` kısa; uzun transaction yasak |
| Prod DDL | root onayı şart (bu skill onaysız prod DDL uygulamaz) |
| Rollback planı | Her fazın geri alması yazılı olmalı |

Detay: [references/06-schema-generation.md](references/06-schema-generation.md) + [references/10-checklist.md](references/10-checklist.md).

### 7.1 Backfill chunk kalıbı (somut)

```sql
-- Chunk'lı backfill — tek büyük UPDATE YASAK (lock üretir, replication geciktirir)
-- Her batch kısa transaction; id aralığıyla ilerle; hedef: saniyede <500ms lock
UPDATE users
SET email_canonical = LOWER(email)
WHERE id > @last_id AND id <= @last_id + 1000
  AND email_canonical IS NULL;
SET @last_id = @last_id + 1000;   -- döngü; @last_id persist edilir (kesintiye dayanıklı)
```

| Faz | Geri alması |
|-----|-------------|
| 1 EXPAND | `ALTER TABLE ... DROP COLUMN email_canonical` — veri kaybı yok (eski kolon duruyor) |
| 2 MIGRATE | Backfill durdur + yeni kolonu sıfırla (`SET email_canonical = NULL`) |
| 3 CONTRACT | En riskli — yalnız doğrulama + root onayı sonrası |

---

## §8. Workflow: Model → Design → Normalize → Migrate → Validate → Document

| Adım | Eylem | Referans |
|------|-------|----------|
| 1 MODEL | Gereksinim topla: varlıklar, ilişkiler, sensitive alan, ölçek, sorgu kalıpları | [references/01-requirements-gathering.md](references/01-requirements-gathering.md) |
| 2 DESIGN | Kolon + tip + PK/FK + constraint + index planla | [references/06-schema-generation.md](references/06-schema-generation.md) |
| 3 NORMALIZE | Aday anahtar çıkar → 1NF/2NF/3NF/BCNF denetle → gerekirse böl | [references/02-normalization-rules.md](references/02-normalization-rules.md) |
| 4 MIGRATE | Expand-contract + backfill + forward-only plan | §7 |
| 5 VALIDATE | Checklist + script'lerle denetim (normalize-checker, security-audit) | [references/checklist.md](references/checklist.md) |
| 6 DOCUMENT | `schema.sql`, ER diyagramı, veri sözlüğü, migration | [references/08-examples.md](references/08-examples.md) |

Eksik gereksinimde **soru sor**; tahminle şema üretme.

### 8.1 Kapı kuralları (her adımın çıkışı)

| Kapı | Koşul | İhlalde |
|------|-------|---------|
| MODEL → DESIGN | Varlıklar, ilişkiler ve sorgu kalıpları yazılı | Eksikse DUR, soru sor |
| DESIGN → NORMALIZE | Aday anahtar (birden fazla dahil) açıkça listelendi | Yoksa tahmin yasak |
| NORMALIZE → MIGRATE | Her tablo NF denetiminden geçti; ihlal varsa bölündü | İhlalle geçiş yasak |
| MIGRATE → VALIDATE | 3 faz planı + chunk'lı backfill + rollback yazıldı | Tek büyük UPDATE yasak |
| VALIDATE → DOCUMENT | §11 checklist `PASS` | `BLOCK` varsa teslim yok |

Kapı mantığı: her adım bir öncekinin **çıktısını** doğrular; zincirde zayıf halka varsa ilerlemez.

---

## §9. Anti-Pattern Kataloğu (özet)

| Anti-pattern | Sorun | Çözüm |
|--------------|-------|-------|
| `FLOAT` para | Yuvarlama kaybı | `DECIMAL(p,s)` |
| `VARCHAR(255)` her yerde | Niyet gizlenir | Gerçek boyut |
| Partial index (`... WHERE`) | MySQL'de **desteklenmez** | Composite index / generated kolon |
| Polymorphic FK | Bütümlülük yok | Ayrı tablalar |
| ENUM sabit | Değişiklik zor | Lookup tablosu |
| FK'ya index yok | Yavaş JOIN/DELETE | `idx_{tablo}_id` |
| `SELECT *` | Performans + sözleşme kırılır | Açık kolon listesi |
| Hard delete | Geri alınamaz | `deleted_at` (soft delete) |
| Tarih VARCHAR | Sıralama/karşılaştırma bozuk | `DATE/DATETIME/TIMESTAMP` |
| Tek büyük backfill | Lock | Batch chunk |

Detay: [references/09-anti-patterns.md](references/09-anti-patterns.md).

---

## §10. Hata Tablosu

| Hata | Kök neden | Çözüm |
|------|-----------|-------|
| `Duplicate entry` | UNIQUE ihlali | Veri çakışmasını çöz |
| `Cannot add foreign key` | Tip/kolon uyuşmazlığı | BIGINT UNSIGNED eşleşmesi + önce ana tablo |
| `Row size too large` | Satır limiti | TEXT/BLOB ayrı tabloya |
| `Lock wait timeout` | Uzun transaction | Transaction'ı kısalt, batch |
| `Incorrect table definition` | Syntax/sürüm | 8.0 syntax + sürüm feature kontrolü |
| `Key column doesn't exist` | FK sırası | Önce ana tablo, sonra çocuk |
| `Data too long for column` | VARCHAR/DECIMAL sınırı | Gerçek gereksinimle eşleşen uzunluk |
| `Incorrect string value` | UTF8MB4 olmayan bağlantı | `SET NAMES utf8mb4` (PDO charset) |
| `BLOB/TEXT column used in key without length` | TEXT'te index prefix yok | `col(191)` prefix veya ayrı kısa kolon |
| `Deadlock found` | Çapraz transaction | Aynı tablo/kolon sırası + kısa transaction |
| `Check constraint violates` | 8.0.16+ CHECK veri uymadı | Veriyi düzelt, constraint'i gevşetme |

---

## §11. Kalite Kapısı & Script'ler

Çıktı bu kapıdan geçmeden teslim EDİLMEZ: [references/checklist.md](references/checklist.md) (PASS/BLOCK maddeleri).

| Script | Amaç |
|--------|------|
| `scripts/normalize-checker.php` | 1NF/BCNF proxy denetimi (id/uuid PK, transitive `_name`, array/list tespiti) |
| `scripts/security-audit.php` | PII/encryption, audit tablo, naming (SQLi yüzeyi) denetimi |
| `scripts/schema-to-diagram.php` | `CREATE TABLE` → Mermaid ER diyagramı |

> **Çalıştırma durumu (2026-10-21):** Bu ortamda `php` binary'si PATH'te ve yaygın kurulum yollarında YOK (`php -v` → CommandNotFoundException). Script'ler yazıldı ancak **canlı çalıştırma kanıtı `VERIFICATION REQUIRED`** — PHP kurulu bir ortamda `php scripts/<script>.sql <schema.sql>` ile doğrulanmalı.

---

## §12. Referans Dosyaları (orphan=0 — hepsi bu SKILL.md'den linkli)

| Dosya | Amaç |
|-------|------|
| [references/00-overview.md](references/00-overview.md) | Genel bakış & navigasyon |
| [references/01-requirements-gathering.md](references/01-requirements-gathering.md) | Gereksinim toplama soruları |
| [references/02-normalization-rules.md](references/02-normalization-rules.md) | 1NF→BCNF derin kurallar + aday anahtar |
| [references/03-provider-dialects.md](references/03-provider-dialects.md) | Motor diyalektleri (MySQL 8.0 odaklı) |
| [references/04-security-audit.md](references/04-security-audit.md) | PII, şifreleme, audit trail, OWASP |
| [references/05-performance-optimization.md](references/05-performance-optimization.md) | Index, EXPLAIN, N+1, sorgu planı |
| [references/06-schema-generation.md](references/06-schema-generation.md) | Şema + migration üretim kalıpları |
| [references/07-coremusic-integration.md](references/07-coremusic-integration.md) | CoreMusic entegrasyon & routing |
| [references/08-examples.md](references/08-examples.md) | Uçtan uca örnek çıktılar |
| [references/09-anti-patterns.md](references/09-anti-patterns.md) | Anti-pattern kataloğu (tam) |
| [references/10-checklist.md](references/10-checklist.md) | QA kontrol listesi |
| [references/changelog.md](references/changelog.md) | Sürüm geçmişi |
| [references/db-engine-notes.md](references/db-engine-notes.md) | Motor/versiyon notları |
| [references/sources.md](references/sources.md) | Resmi kaynaklar (URL + erişim tarihi) |
| [references/checklist.md](references/checklist.md) | Kalite kapısı (PASS/BLOCK) |
| [templates/schema-template.sql](templates/schema-template.sql) | Şema şablonu |
| [templates/migration-template.sql](templates/migration-template.sql) | Migration şablonu |
| [templates/seed-template.sql](templates/seed-template.sql) | Seed veri şablonu |

---

## §13. Bağlantılar & Routing

- **Vault:** `.ai/brain.md` — veritabanı mimarisi kararları.
- **ADR:** `.ai/.decisions/` — ADR-002 (no ORM), ADR-014 (no SELECT *), ADR-040 (database authority).
- **SQL SSOT:** `.ai/.sql/mysql/*.sql` — gerçek şema dosyaları.
- **Agent:** `data-engineer` (Expert, şema/BCNF) · `security-engineer` (veto, OWASP) · `sql-pro`/`postgres-pro` (gelişmiş sorgu).
- **Yönlendirme:** `agent-orchestrator` — “database/normalize/bcnf” keyword eşleşmesi.

---

*CoreMusic Skill v3.0 (format: claude-skill-v3) — metadata.version: 4.0.0 — Updated 2026-10-21*
