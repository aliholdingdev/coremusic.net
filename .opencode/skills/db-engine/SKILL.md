---
title: "CoreMusic — DB Engine"
type: skill-instruction
version: 1.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - BCNF Database Design
  - Schema Generation
  - Normalization (1NF→2NF→3NF→BCNF)
  - Migration Strategy
  - Index Design
triggers:
  - "database oluştur"
  - "veritabanı oluştur"
  - "tablo oluştur"
  - "sql yaz"
  - "schema"
  - "normalize"
  - "bcnf"
  - "migration"
  - "index oluştur"
  - "foreign key"
  - "veri modeli"
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/brain.md"
    - ".ai/.sql/mysql/"
changelog:
  - version: 1.0
    date: 2026-09-20
    changes:
      - database-normalize-maker'dan türetildi
      - SQL referansları kaldırıldı (MySQL docs'tan okunur)
      - Anti-pattern katalogu kaldırıldı (H001-H039'da)
---

# DB ENGINE — CoreMusic

## 1. Kimlik

Bu skill, CoreMusic veritabanı şemasını **tasarlar, normalizasyon kurallarına göre düzenler ve SQL komutlarını üretir.**

**Yasaklar:** ORM (ADR-002), SELECT *, FLOAT para birimi, Framework (ADR-001).

---

## 2. 18 BCNF Veritabanı (ADR-040)

| # | Veritabanı | Amaç |
|---|-----------|------|
| 1 | coremusic_auth | Kimlik doğrulama, session, token |
| 2 | coremusic_user | Profiller, tercihler |
| 3 | coremusic_musics | Şarkılar, sanatçılar, dosyalar |
| 4 | coremusic_albums | Albüm koleksiyonları |
| 5 | coremusic_playlist | Çalma listeleri |
| 6 | coremusic_catalog | Referans verileri |
| 7 | coremusic_logs | Audit trail, analitik |
| 8 | coremusic_media | Cihaz senkronizasyonu |
| 9 | coremusic_system | Ayarlar, config |
| 10 | coremusic_social | Yorumlar, paylaşımlar |
| 11 | coremusic_wireless | WiFi + Bluetooth |
| 12 | coremusic_ai | Öneri profilleri |
| 13 | coremusic_api | API anahtarları |
| 14 | coremusic_cms | Sayfalar, blog |
| 15 | coremusic_download | İndirme kuyruğu |
| 16 | coremusic_neva | EQ, DSP ayarları |
| 17 | coremusic_studio | Stüdyo oturumları |
| 18 | coremusic_patch | Schema versiyonları |

---

## 3. MySQL 9 Zorunlu Kuralları

| Kural | Değer |
|-------|-------|
| Motor | `ENGINE=InnoDB` |
| Charset | `utf8mb4_unicode_ci` |
| PK | `id BIGINT UNSIGNED AUTO_INCREMENT` |
| Timestamp | `created_at`, `updated_at`, `deleted_at` (her tabloda) |
| Para | `DECIMAL(10,2)` — FLOAT yasak |
| ORM | Yasak — PDO prepared only |
| SELECT * | Yasak — açık kolon listesi |
| İsimlendirme | snake_case, çoğul tablolar |

### Zorunlu Kolon Şablonu

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL DEFAULT NULL
```

---

## 4. Normalizasyon Kuralları

| Form | Kural | Kontrol |
|------|-------|---------|
| **1NF** | Atomik değer, liste yok | Her kolonda tek değer var mı? |
| **2NF** | Kısmi bağımlılık yok | Composite PK'da tüm non-key kolonlar tam bağımlı mı? |
| **3NF** | Geçici bağımlılık yok | A→B→C zinciri varsa ayrı tabloya taşı |
| **BCNF** | Her determinant key | Belirleyici candidate key mi? |

### İlişki Şablonları

**One-to-Many:**
```sql
CREATE TABLE parent (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE child (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (parent_id) REFERENCES parent(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Many-to-Many (Junction):**
```sql
CREATE TABLE parent_child (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NOT NULL,
    child_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES parent(id) ON DELETE CASCADE,
    FOREIGN KEY (child_id) REFERENCES child(id) ON DELETE CASCADE,
    UNIQUE KEY uk_parent_child (parent_id, child_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 5. İndeks Stratejisi

| Sorgu Kalıbı | Index Tipi |
|---------------|------------|
| Eşleşme (WHERE a = ?) | B-tree |
| Aralık (WHERE a > ?) | B-tree |
| Sıralama (ORDER BY a) | B-tree |
| Full-text arama | FULLTEXT |
| Çoklu WHERE | Composite index |

**Composite index kuralı:** Equality primero, sonra range:
```sql
-- WHERE user_id = ? AND status = ? ORDER BY created_at DESC
CREATE INDEX idx_user_status_date ON orders(user_id, status, created_at);
```

**Her FK'ya index zorunlu:**
```sql
FOREIGN KEY (user_id) REFERENCES users(id) → INDEX idx_user_id (user_id)
```

---

## 6. Migration Stratejisi

### Expand-Contract (Zero-Downtime)

```sql
-- FAZ 1: EXPAND — yeni yapı ekle, eskisini koru
ALTER TABLE users ADD COLUMN user_name VARCHAR(100) NOT NULL DEFAULT '';

-- FAZ 2: MIGRATE — veriyi kopyala
UPDATE users SET user_name = username WHERE user_name = '';

-- FAZ 3: CONTRACT — eskisini kaldır (onay sonrası)
ALTER TABLE users DROP COLUMN username;
```

### Geri Dönüş
```sql
-- UP
CREATE TABLE products (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ...);
-- DOWN
DROP TABLE IF EXISTS products;
```

---

## 7. Workflow: Model → Design → Validate

### Adım 1: Model
Kullanıcıdan topla: tablolar, ilişkiler, sensitive alanlar, veri miktarı, sorgu kalıpları.

### Adım 2: Design
Kolonları listele, veri tipini seç, PK/FK/Constraint/Index tanımla.

### Adım 3: Normalize
1NF → 2NF → 3NF → BCNF kontrolü yap.

### Adım 4: Generate
SQL komutlarını üret: schema.sql, seed_data.sql, er_diagram.md, migration/.

### Adım 5: Validate
Kontrol listesi:
- [ ] PK var mı? BIGINT UNSIGNED mi?
- [ ] Charset utf8mb4_unicode_ci mi?
- [ ] Engine InnoDB mi?
- [ ] Timestamp kolonları var mı?
- [ ] FK'lar doğru tanımlı mı?
- [ ] ON DELETE stratejisi seçilmiş mi?
- [ ] FK'lara index eklendi mi?
- [ ] BCNF uyumlu mu?
- [ ] SELECT * kullanılmamış mı?
- [ ] Soft delete kullanılıyor mu?

---

## 8. Yasaklar

| Yasaklı | Neden |
|---------|-------|
| ORM kullanımı | ADR-002 — PDO prepared only |
| SELECT * | Açık kolon listesi zorunlu |
| FLOAT para birimi | DECIMAL(10,2) |
| Cross-database FK | 9 DB BCNF izolasyonu |
| Hard delete | Soft delete (deleted_at) |
| Stored procedure (iş mantığı) | PHP'de yapılmalı |
| ENUM sabitleri | Lookup tablosu kullan |

---

## 9. Quick Reference

| İhtiyaç | Bölüm |
|---------|-------|
| Hangi DB'ler var? | §2 |
| MySQL kuralları? | §3 |
| Normalizasyon? | §4 |
| Index nasıl yazılır? | §5 |
| Migration? | §6 |

---

*DB Engine v1.0 — CoreMusic*
*Authority: Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
