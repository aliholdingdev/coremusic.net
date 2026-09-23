---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — SQL Query Template"
type: query-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — SQL Query Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic veritabanı sorgularını standartlaştırmaktır: SELECT/INSERT/UPDATE iskeletlerini, soft delete koşullarını, sayfalama/filtreleme kalıplarını ve PDO prepared statement kurallarını tek şablonda sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| MySQL 9 sorgu iskeletleri (SELECT/INSERT/UPDATE/DELETE) | Şema/migration değişikliği (bkz. migration-template) |
| PDO prepared statement kod standartları | ORM sorguları (yasak — §4.1) |
| Soft delete + BCNF uyumlu sorgular | CI/CD, test konfigürasyonları |

- **Dosya tipi:** SQL sorgu + PDO PHP örneği (Markdown şablon içinde)
- **Kullanan agent:** Data Engineer (birincil · AGENTS.md §6: SQL, query, index), Backend Architect (ikincil)
- **Guardrail:** #16 (Template Mandatory) — yeni sorgu işi bu şablondan başlar

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}`, SQL/PHP kod blokları ve `--` yorum satırları birebir korunmuştur. Hard Guardrails ve prepared statement kuralları §4.1-§4.2'dedir.

### {{QUERY_TITLE}}

**Veritabanı:** {{DATABASE}}
**Tablo:** {{TABLE}}
**Tip:** SELECT/INSERT/UPDATE/DELETE

---

#### 3.1 SELECT Şablonu

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

#### 3.2 INSERT Şablonu

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

#### 3.3 UPDATE Şablonu

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

## 4. Kurallar

Zorunlu / yasak kurallar ve kod standartları:

#### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `SELECT *` yasak — Açık sütun listesi | SQL injection |
| 2 | Prepared statement zorunlu | SQL injection |
| 3 | Soft delete koşulu (`is_deleted = 0`) zorunlu | Silinmiş veri |
| 4 | ORM yasak — PDO only | Bağımlılık |
| 5 | BCNF uyumlu sorgu | Normalizasyon hatası |

#### 4.2 PDO Prepared Statement Örneği (zorunlu kalıp)

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

Ek kurallar:

- **Zorunlu:** her sorgu named parameter (`:id`, `:search`, `:limit`…) ile prepared statement üzerinden çalışır (§4.2); string birleştirme (`"..." . $var`) yasaktır.
- **Zorunlu:** tüm okuma sorgularında `is_deleted = 0` koşulu ve açık sütun listesi vardır (§3.1); `SELECT *` kullanılmaz (§4.1 #1).
- **Zorunlu:** yazma sorgularında `updated_at = NOW()`, soft delete'de `is_deleted = 1` + `deleted_at = NOW()` (§3.3).
- **Yasak:** ORM; doğrudan PDO only (§4.1 #4, AGENTS.md §16: no ORM, no SELECT *, prepared %100).
- **Yasak:** `{{QUERY_TITLE}}`, `{{DATABASE}}`, `{{TABLE}}`, `{{COLUMN_1..3}}` placeholder'ları doldurulmadan sorgu production'a alınamaz.
- **Uyarı:** doğrulanamayan kolon/tablo adı `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/query/Query-Template.md` (Guardrail #16).
2. **KOPYALA:** ihtiyaca göre §3.1 (SELECT), §3.2 (INSERT), §3.3 (UPDATE) iskeletini kopyala; §4.2 PDO kalıbını kullanıc koduna ekle.
3. **{{PLACEHOLDER}} DOLDUR:** `{{QUERY_TITLE}}`, `{{DATABASE}}`, `{{TABLE}}`, `{{COLUMN_1}}`, `{{COLUMN_2}}`, `{{COLUMN_3}}`; `:limit/:offset/:search/:status` parametrelerini bağla.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + §4.1 guardrails (SELECT * yok, prepared var, soft delete var, ORM yok, BCNF) geçti.
5. **COMMIT:** sorguyu ilgili Repository dosyasıyla commit et; migration gerekiyorsa Data Engineer'a handover yap (AGENTS.md §9.3), `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §4.1 guardrails geçti; prepared statement + soft delete + açık sütun listesi mevcut

**REFACTOR REPORT:** FILE: Query-Template.md · PURPOSE: SQL Query Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: SQL/query → Data Engineer), kalite standardı §16 (BCNF, no ORM, no SELECT *, prepared %100)
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*SQL Query Template v2.0.0 — CoreMusic Data Layer Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
