---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Database Migration Template"
type: migration-template
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

# CoreMusic — Database Migration Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic veritabanı migration ve seed işlerini standartlaştırmaktır: Phinx tabanlı migration dosya yapısı, `declare(strict_types=1)` migration sınıfı, seed sınıfı, BCNF kontrol listesi ve çalıştırma komutlarını tek iskelette sunar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `database/migrations/`, `database/seeds/`, `schema.sql` | Uygulama sorguları (bkz. Query-Template) |
| MySQL 9 + Phinx migration/seed | CI/CD pipeline (bkz. github-actions-template) |
| BCNF şema değişiklikleri (L0 katmanı) | Donanım/firmware dokümanları |

- **Dosya tipi:** PHP migration/seed sınıfı + Markdown şablon dokümanı
- **Kullanan agent:** Data Engineer (birincil · AGENTS.md §6: database, SQL, BCNF, migration), Backend Architect (ikincil)
- **Migration Tool:** Phinx · **Guardrail:** #16 (Template Mandatory)

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}`, PHP/bloğa ait `//` yorum satırları ve bash komutları birebir korunmuştur. Hard Guardrails ve BCNF kontrol listesi §4.1-§4.2'dedir.

### {{TITLE}}

**Veritabanı:** {{DATABASE_NAME}} (MySQL 9)
**BCNF:** Zorunlu (ADR-040)
**Migration Tool:** Phinx

---

#### 3.1 Migration Dosya Yapısı

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

#### 3.2 Migration Şablonu

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

#### 3.3 Seed Şablonu

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

#### 3.4 Çalıştırma

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

## 4. Kurallar

Zorunlu / yasak kurallar:

#### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Forward-only migration (ADR-014) | Migration revert yasak |
| 2 | BCNF zorunlu (ADR-040) | Normalizasyon hatası |
| 3 | Soft delete (`is_deleted = 0`) zorunlu | Veri kaybı |
| 4 | Timestamp (`created_at`, `updated_at`) zorunlu | Audit trail eksik |
| 5 | Snake_case naming zorunlu | Tutarsızlık |
| 6 | Prepared statement zorunlu | SQL injection |

#### 4.2 BCNF Kontrol Listesi

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

Ek kurallar:

- **Yasak:** migration revert (§4.1 #1) — hata varsa yeni forward migration yazılır (ADR-014).
- **Zorunlu:** migration sınıfı `declare(strict_types=1)` + `final class` ile yazılır; `// BCNF alanları`, `// Soft delete`, `// Timestamps`, `// Indexes` yorum satırları korunur (§3.2).
- **Zorunlu:** her migration'da soft delete + timestamp + snake_case sütunları §3.2 şablonundaki gibi bulunur.
- **Yasak:** `{{TABLE}}`, `{{COLUMN}}`, `{{COLUMN_1..3}}`, `{{DATABASE_NAME}}` placeholder'ları doldurulmadan commit edilmez.
- **Uyarı:** doğrulanamayan şema gerçeği `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/infrastructure/migration-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1 dosya yapısına göre `database/migrations/` altına yeni dosya oluştur (`vendor/bin/phinx create`).
3. **{{PLACEHOLDER}} DOLDUR:** `{{TITLE}}`, `{{DATABASE_NAME}}`, `{{TABLE}}`, `{{COLUMN}}`, `{{COLUMN_1}}`, `{{COLUMN_2}}`, `{{COLUMN_3}}`; seed verilerini gerçekle değiştir.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + §4.1/§4.2 (BCNF, soft delete, timestamp, snake_case, prepared) geçti.
5. **COMMIT:** `vendor/bin/phinx migrate` + `seed:run` + `status` ile doğrula; migration CI'da (`php-test` job) geçmeli; `log.md`'ye giriş ekle.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §4.1 Hard Guardrails + §4.2 BCNF kontrol listesi geçti; revert yok (ADR-014)

**REFACTOR REPORT:** FILE: migration-template.md · PURPOSE: Database Migration Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: migration → Data Engineer), kalite standardı §16 (BCNF, no ORM, no SELECT *, prepared)
- ADR-014 (forward-only migration), ADR-040 (BCNF zorunlu) — §4.1 içinde referanslanır
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)

---

*Database Migration Template v2.0.0 — CoreMusic Data Layer Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
