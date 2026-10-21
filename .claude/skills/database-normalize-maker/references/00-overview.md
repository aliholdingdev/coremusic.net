# 00 — Genel Bakış & Navigasyon

> Bu referans `database-normalize-maker` skill'inin haritasıdır. Her konu için doğru
> alt dosyaya yönlendirir. Ana akış: SKILL.md §8 (Model → Design → Normalize → Migrate → Validate → Document).

## Amaç

CoreMusic veritabanı işlerinde tek doğrulanmış akışı sağlamak: gereksinimden
BCNF uyumlu şemaya, güvenli migration'a ve doğrulanmış çıktıya kadar. Zero-hallucination
ilkesi bağlayıcıdır — doğrulanamayan iddia `VERIFICATION REQUIRED` ile işaretlenir.

## Hedef kitle & rol

- **Birincil:** Data Engineer (Expert #3) — şema, BCNF, migration, MySQL/PDO.
- **İkincil:** security-engineer (OWASP/PII veto), sql-pro/postgres-pro (gelişmiş sorgu), backend-architect (repository katmanı).

## Konu → Dosya indeksi

| İhtiyaç | Aç |
|---------|-----|
| Gereksinimleri nasıl toplarım? | [01-requirements-gathering.md](01-requirements-gathering.md) |
| 1NF/2NF/3NF/BCNF kuralı, aday anahtar, bağımlılık | [02-normalization-rules.md](02-normalization-rules.md) |
| Farklı motor/MySQL 8.0 diyalekt notları | [03-provider-dialects.md](03-provider-dialects.md) |
| PII, şifreleme, audit trail, OWASP | [04-security-audit.md](04-security-audit.md) |
| Index, EXPLAIN, N+1, sorgu planı | [05-performance-optimization.md](05-performance-optimization.md) |
| Şema + migration üretim kalıpları | [06-schema-generation.md](06-schema-generation.md) |
| CoreMusic entegrasyonu, vault/ADR/agent routing | [07-coremusic-integration.md](07-coremusic-integration.md) |
| Uçtan uca örnek çıktılar | [08-examples.md](08-examples.md) |
| Anti-pattern kataloğu (tam) | [09-anti-patterns.md](09-anti-patterns.md) |
| QA kontrol listesi | [10-checklist.md](10-checklist.md) |
| Sürüm geçmişi | [changelog.md](changelog.md) |
| Motor/versiyon notları | [db-engine-notes.md](db-engine-notes.md) |
| Resmi kaynaklar (URL + tarih) | [sources.md](sources.md) |
| Kalite kapısı (PASS/BLOCK) | [checklist.md](checklist.md) |

## Script'ler

| Script | Amaç |
|--------|------|
| `scripts/normalize-checker.php` | 1NF/BCNF proxy denetimi |
| `scripts/security-audit.php` | PII/audit/naming denetimi |
| `scripts/schema-to-diagram.php` | CREATE TABLE → Mermaid ER |

## Şablonlar

- `templates/schema-template.sql` — BCNF uyumlu tablo + audit iskeleti.
- `templates/migration-template.sql` — expand-contract migration iskeleti.
- `templates/seed-template.sql` — gerçekçi PII içermeyen seed veri.

## Temel yasaklar (özet)

`SELECT *` · ORM · hard delete · `FLOAT` para · onaysız prod DDL · kaynaksız iddia.
