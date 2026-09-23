---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — SQL Query Template"
type: query-template
category: template
date: {{DATE}}
updated: {{DATE}}
status: draft
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# {{QUERY_TITLE}}

**Veritabanı:** {{DATABASE}}
**Tablo:** {{TABLE}}
**Tip:** SELECT/INSERT/UPDATE/DELETE

---

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `SELECT *` yasak — Açık sütun listesi | SQL injection |
| 2 | Prepared statement zorunlu | SQL injection |
| 3 | Soft delete koşulu (`is_deleted = 0`) zorunlu | Silinmiş veri |
| 4 | ORM yasak — PDO only | Bağımlılık |
| 5 | BCNF uyumlu sorgu | Normalizasyon hatası |

---

## 2. SELECT Şablonu

```sql
-- Tüm kayıtları listele (soft delete hariç)
SELECT
    id,
    {{COLUMN_1}},
    {{COLUMN_2}},
    {{COLUMN_3}},
    created_at,
    updated_at
FROM {{TABLE}}
WHERE is_deleted = 0
ORDER BY created_at DESC
LIMIT :limit OFFSET :offset;

-- ID ile tek kayıt getir
SELECT
    id,
    {{COLUMN_1}},
    {{COLUMN_2}},
    {{COLUMN_3}},
    created_at,
    updated_at
FROM {{TABLE}}
WHERE id = :id
    AND is_deleted = 0;

-- Filtreleme ile listeleme
SELECT
    id,
    {{COLUMN_1}},
    {{COLUMN_2}},
    created_at
FROM {{TABLE}}
WHERE is_deleted = 0
    AND {{COLUMN_1}} LIKE :search
    AND {{COLUMN_2}} = :status
ORDER BY {{COLUMN_1}} ASC
LIMIT :limit OFFSET :offset;

-- Sayfalama ile toplam say
SELECT COUNT(*) as total
FROM {{TABLE}}
WHERE is_deleted = 0
    AND {{COLUMN_1}} LIKE :search;
```

---

## 3. INSERT Şablonu

```sql
-- Tek kayıt ekle
INSERT INTO {{TABLE}} (
    {{COLUMN_1}},
    {{COLUMN_2}},
    {{COLUMN_3}},
    created_at
) VALUES (
    :{{COLUMN_1}},
    :{{COLUMN_2}},
    :{{COLUMN_3}},
    NOW()
);

-- Toplu kayıt ekleme
INSERT INTO {{TABLE}} (
    {{COLUMN_1}},
    {{COLUMN_2}},
    created_at
) VALUES
    (:{{COLUMN_1}}_1, :{{COLUMN_2}}_1, NOW()),
    (:{{COLUMN_1}}_2, :{{COLUMN_2}}_2, NOW()),
    (:{{COLUMN_1}}_3, :{{COLUMN_2}}_3, NOW());
```

---

## 4. UPDATE Şablonu

```sql
-- Kaydı güncelle
UPDATE {{TABLE}}
SET
    {{COLUMN_1}} = :{{COLUMN_1}},
    {{COLUMN_2}} = :{{COLUMN_2}},
    updated_at = NOW()
WHERE id = :id
    AND is_deleted = 0;

-- Soft delete
UPDATE {{TABLE}}
SET
    is_deleted = 1,
    deleted_at = NOW()
WHERE id = :id
    AND is_deleted = 0;
```

---

## 5. PDO Prepared Statement Örneği

```php
<?php
declare(strict_types=1);

// SELECT example
$sql = 'SELECT id, name, status FROM users WHERE is_deleted = 0 AND id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// INSERT example
$sql = 'INSERT INTO users (name, email, created_at) VALUES (:name, :email, NOW())';
$stmt = $pdo->prepare($sql);
$stmt->execute(['name' => $name, 'email' => $email]);
$userId = (int) $pdo->lastInsertId();

// UPDATE example
$sql = 'UPDATE users SET name = :name, updated_at = NOW() WHERE id = :id AND is_deleted = 0';
$stmt = $pdo->prepare($sql);
$stmt->execute(['name' => $newName, 'id' => $userId]);
$affected = $stmt->rowCount();
```

---

*SQL Query Template v1.0.0 — CoreMusic Data Layer Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
