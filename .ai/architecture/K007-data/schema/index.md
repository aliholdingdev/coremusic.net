---
node_id: "K007-schema"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — 18 BCNF veritabanı / 171 tablo / JOIN yasağı (2026-10-07)"
  - "shared/src/Database/DatabaseManager.php:14-27 — parametreli DSN + PDO seçenekleri"
  - "docs/database/01-entity-relationship-diagram.md §2 — 5 veritabanı, 30+ tablo (2026-10-10)"
validation: "Kapsam/dogrulama/ornekler/bilesenler/web-arastirma dosyaları yazıldı; 8/8 zorunlu alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi (semantik şema parçası olarak)?"
  - "K-001 — forum veritabanı şeması kesinleştirmede T-11/T-20 anti-pattern kapsamı UNKNOWN"
group: mimari
---

# K007-SHEMA — Şema Tasarımı (Alt Alan)

## 1. Rol

RDBMS şeması tasarımı: normalizasyon (BCNF), tablo/alan tipleri, kısıtlar
(constraint), birincil/ikincil anahtar ve şema gözden geçirme (schema review).

Kapsam dışı (kardeş alt alanlar): migration yürütme → `migrations/` ·
indeks derinliği → `indexing/` · eşzamanlılık → `transactions/`.

## 2. Sorumluluklar

| # | Sorumluluk | Ana dayanak |
|---|---|---|
| 1 | BCNF/3NF normalizasyon değerlendirmesi | `.ai/.sql/CLAUDE.md §1` |
| 2 | Alan tipi seçimi (BIGINT/DATETIME/utf8mb4) | MySQL 8.4 InnoDB dokümanı |
| 3 | Kısıt tasarımı (PK/FK/UNIQUE/CHECK) | MySQL 8.4 Constraints |
| 4 | Çok-DB topoloji kaydı (18 DB / 171 tablo) | `.ai/.sql/CLAUDE.md §1` |
| 5 | Şema inceleme (review) — LIKE sorgusu yasağı | `.ai/.sql/CLAUDE.md §1` |

> Bu alt alanın rolü, `K007-data` içindeki ilk sürümde **tasarım kararlarının
> belgelenmesidir** — şema kendisi `.ai/.sql/mysql/` altında MySQL dump'ları
> olarak sürülür (`schema/` alt klasörü; içeriği bu belgelerde tekrar edilmez).

## 3. Anahtar Bileşenler

- **`schemata`** — mevcut veritabanı envanteri (MySQL `information_schema`).
- **`DatabaseManager::prepare()`** — şemanın uygulamadaki tek PDO kapısı
  (kanıt: `shared/src/Database/DatabaseManager.php:30-42`).
- **Semantik katman** — Markdown `docs/database/*.md` (ER diyagramı, sorgu
  sözlüğü, sözlük) + `.ai/.sql/CLAUDE.md` indeksi; `database/README.md:9-13`
  bu katmanı "pragmatic, single source of truth" olarak tanımlar.

## 4. Bağımlılıklar

- **Yukarı:** `../data-master.md`, `../data-kapsam.md` (K-SCOPE-1).
- **Dış:** `K008-service-layer` (şema tüketen katman), `K011/K012` (repository).
- **Aşağı:** `migrations/` (şema değişikliğinin yürütme yüzeyi).

## 5. Açık Sorular

1. **S-1** — arama şeması semantik katmanında mı (bkz. `../data-kararlar.md` K-06)?
2. **K-001** — forum şeması T-11/T-20 anti-pattern kapsamı `⚠️ VERIFICATION REQUIRED`.

## 6. Dosyalar

| Dosya | Satır |
|---|---|
| `schema-kapsam.md` | 120 |
| `schema-dogrulama.md` | 153 |
| `schema-web-arastirma.md` | 202 |
| `schema-ornekler.md` | 155 |
| `schema-bilesenler.md` | 121 |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `schema-bilesenler.md` | Bileşen planı ve kademe ipuçları | Kademe-2 |
| mimari | `schema-kapsam.md` | Kapsam sınırı ve kapsam dışı listesi | Kademe-2 |
| kod | `schema-ornekler.md` | Uygulama örnekleri ve kalıp gösterimi | Kademe-2 |
| kanit | `schema-dogrulama.md` | İddia, URL ve tarih doğrulama matrisi | Kademe-2 |
| kanit | `schema-web-arastirma.md` | Sistemli resmi kaynak tarama kaydı | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward

