---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Database Migration Template"
type: migration-template
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

# {{TITLE}}

**Veritabanı:** {{DATABASE_NAME}} (MySQL 9)
**BCNF:** Zorunlu (ADR-040)
**Migration Tool:** Phinx

---

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Forward-only migration (ADR-014) | Migration revert yasak |
| 2 | BCNF zorunlu (ADR-040) | Normalizasyon hatası |
| 3 | Soft delete (`is_deleted = 0`) zorunlu | Veri kaybı |
| 4 | Timestamp (`created_at`, `updated_at`) zorunlu | Audit trail eksik |
| 5 | Snake_case naming zorunlu | Tutarsızlık |
| 6 | Prepared statement zorunlu | SQL injection |

---

## 2. Migration Dosya Yapısı

```
database/
├── migrations/
│   ├── 20260920_000001_create_{{TABLE}}.php
│   └── 20260920_000002_add_{{COLUMN}}_to_{{TABLE}}.php
├── seeds/
│   └── {{TABLE}}Seeder.php
└── schema.sql
```

---

## 3. Migration Şablonu

```php
<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Create{{TABLE}} extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('{{TABLE}}', [
            'id' => false,
            'primary_key' => ['id'],
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci',
        ]);

        $table
            ->addColumn('id', 'biginteger', [
                'identity' => true,
                'signed' => false,
            ])
            // BCNF alanları
            ->addColumn('{{COLUMN_1}}', 'string', [
                'limit' => 255,
                'null' => false,
                'collation' => 'utf8mb4_unicode_ci',
            ])
            ->addColumn('{{COLUMN_2}}', 'integer', [
                'signed' => false,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('{{COLUMN_3}}', 'decimal', [
                'precision' => 10,
                'scale' => 2,
                'null' => false,
                'default' => '0.00',
            ])
            // Soft delete
            ->addColumn('is_deleted', 'boolean', [
                'null' => false,
                'default' => false,
            ])
            // Timestamps
            ->addColumn('created_at', 'datetime', [
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('updated_at', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addColumn('deleted_at', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            // Indexes
            ->addIndex(['{{COLUMN_1}}'], ['unique' => true])
            ->addIndex(['is_deleted'])
            ->addIndex(['created_at'])
            ->addIndex(['updated_at'])
            ->create();
    }
}
```

---

## 4. Seed Şablonu

```php
<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class {{TABLE}}Seeder extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                '{{COLUMN_1}}' => 'Example 1',
                '{{COLUMN_2}}' => 100,
                '{{COLUMN_3}}' => '99.99',
                'is_deleted' => false,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                '{{COLUMN_1}}' => 'Example 2',
                '{{COLUMN_2}}' => 200,
                '{{COLUMN_3}}' => '149.99',
                'is_deleted' => false,
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table('{{TABLE}}');
        $table->insert($data)->save();
    }
}
```

---

## 5. BCNF Kontrol Listesi

| Kontrol | Açıklama |
|---------|----------|
| ✅ Her tablonun birincil anahtarı var mı? | `id` biginteger |
| ✅ BCNF formunda mı? | Functional dependency kontrolü |
| ✅ Soft delete var mı? | `is_deleted` boolean |
| ✅ Timestamp'ler var mı? | `created_at`, `updated_at`, `deleted_at` |
| ✅ Snake case mi? | Tablo ve sütun isimleri |
| ✅ Prepared statement kullanılıyor mu? | PDO prepared |
| ✅ `SELECT *` yok mu? | Açık sütun listesi |
| ✅ ORM kullanılmıyor mu? | PDO only |

---

## 6. Çalıştırma

```bash
# Migration oluştur
vendor/bin/phinx create Create{{TABLE}}

# Migration'ı çalıştır
vendor/bin/phinx migrate

# Seed çalıştır
vendor/bin/phinx seed:run -s {{TABLE}}Seeder

# Durum kontrolü
vendor/bin/phinx status
```

---

*Database Migration Template v1.0.0 — CoreMusic Data Layer Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
