---
node_id: K007-data/migrations
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - migrations-anayasa
  - migrations-surucu
  - migrations-ornekler
  - migrations-kapsam
  - migrations-web-arastirma
depends_on:
  - K007-data/schema
  - K007-data/backup
evidence:
  - "shared/database/migrations/CLAUDE.md — forward-only (ADR-014)"
  - "shared/database/migrations/ — 3 PHP dosya (2026-10-15)"
  - "dev.mysql.com 8.4 online DDL — 4 URL (2026-10-15)"
validation:
  - "2026-10-15 — 6 dosyaya tamamlandı (4→6); children disk enumerate 5/5"
open_questions:
  - "Migration CLI aracı → UNKNOWN"
group: mimari
---

# migrations — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | K007-data/migrations |
| parent_id | K007-data |
| Durum | draft |

## Ne işe yarar?

Şemanın ileriye taşınması: forward-only (ADR-014), yıkıcı adımda yedek ön-koşulu
(backup), online DDL ile kilitsiz uygulama (MySQL 8.4 INPLACE).

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [migrations-anayasa.md](migrations-anayasa.md) | anayasa | mimari |
| 2 | [migrations-surucu.md](migrations-surucu.md) | uygulama mekanizması | mimari |
| 3 | [migrations-ornekler.md](migrations-ornekler.md) | repo örnekleri | kod |
| 4 | [migrations-kapsam.md](migrations-kapsam.md) | sınır | mimari |
| 5 | [migrations-web-arastirma.md](migrations-web-arastirma.md) | DDL + framework taraması | kanit |

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | index.md | Künye | Kademe-2 |
| mimari | migrations-anayasa.md | Anayasa | Kademe-2 |
| mimari | migrations-surucu.md | Sürücü | Kademe-2 |
| kod | migrations-ornekler.md | Örnekler | Kademe-2 |
| mimari | migrations-kapsam.md | Kapsam | Kademe-2 |
| kanit | migrations-web-arastirma.md | Kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward