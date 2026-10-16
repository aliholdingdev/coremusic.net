---
node_id: "K007-data/migrations-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data/migrations"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/backup"
evidence:
  - "shared/database/migrations/CLAUDE.md — forward-only (ADR-014), geri migration yasak (2026-10-15 okundu)"
  - "shared/database/migrations/ — 3 PHP migration dosyası (2026-10-15 enumerate)"
  - ".ai/.sql/migration/ — dizin diskte YOK (2026-10-15)"
validation: "2026-10-15 — kapsam yazıldı; CLAUDE.md + 3 dosya"
open_questions:
  - "Migration CLI (bin/console vb.) repo'da → enumerate yapılmadı → UNKNOWN"
group: mimari
---

# migrations — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Forward-only yazım kuralı (down migration yasak) | migrations/CLAUDE.md (ADR-014) |
| 2 | Yıkıcı DDL öncesi yedek ön-koşulu | 2026-10_07_drop_…migration.php:14-20 |
| 3 | Şema sürüm geçmişi kaydı | knex_migrations (novasearch dump) |
| 4 | Enum/veri migration'ları (①→② senkron) | master §4 satır 10 |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Şema tasarımı (yeni tablo) | schema | tasarım ≠ uygulama |
| Yedek alma operasyonu | backup | ön-koşul orada |
| Yedekleme hedefi/depolama | K018-devops | altyapı |
| Çevrimdışı şema kaydı (dump) | .ai/.sql/mysql/ | veri SSOT |

## §3 Sınır cümlesi

migrations, "şema ileriye doğru tek adımlarla nasıl taşınır ve geri dönüşü nasıl olmaz?"
sorusunun sahibidir — her adım geri döndürülemez ilan edilir (ADR-014), bu yüzden yedek
ön-koşul olarak bağlanır.

---

**Authority:** Bayram Ali / Vault Steward