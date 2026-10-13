---
node_id: "K007-schema-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — 18 BCNF veritabanı / 171 tablo (2026-10-07)"
  - "docs/database/01-entity-relationship-diagram.md:13-22 — 5 DB / 30+ tablo / JOIN yasağı"
  - "shared/src/Database/DatabaseManager.php:14-27 — DSN charset + PDO seçenekleri"
validation: "Kapsam tanımı tekilleştirildi (../data-kapsam.md K-SCOPE-1); 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: mimari
---

# K007-SHEMA — Kapsam

## 1. Kapsam İçi (In-Scope)

1. **Normalizasyon** — BCNF hedefi (proje kanunu); 3NF tespiti kayda geçer
   (`.ai/.sql/CLAUDE.md §1`: "18 BCNF veritabanı / 171 tablo").
2. **Tablo/alan tasarımı** — tipler (BIGINT, DATETIME, ENUM), `utf8mb4`
   (MySQL 8.4: varsayılan `utf8mb4_0900_ai_ci`), sabit uzunluklu `CHAR`.
3. **Kısıtlar** — PK, FK, UNIQUE, CHECK, `NOT NULL` (InnoDB'de `NULL`
   olmayan her alan için `IS NOT NULL` indeksi otomatik üretilir).
4. **Çok-DB topoloji kaydı** — `coremusicnet` (web) / `coremusicnet_ads`
   (reklam) / `coremusicnet_log` (log) / `coremusicnet_customer` (iş ortağı)
   / `coremusicnet_ml` (makine öğrenmesi): `docs/database/01-entity-relationship-diagram.md:13-16`.
5. **Semantik katman** — `docs/database/` Markdown (ER, sözlük, sorgu
   sözlüğü) + `.ai/.sql/CLAUDE.md` indeksi.
6. **Şema gözden geçirme** — yeni tablo/alan incelemesi; uygulama koduna
   dayalı LIKE sorgusu tespiti proje kanunu (`.ai/.sql/CLAUDE.md §1`).

## 2. Kapsam Dışı (Out-of-Scope)

| Dışarıda | Nerede |
|---|---|
| Migration dosyası yazımı/koordinasyonu | `../migrations/` |
| Çalışan kodda sorgu (PDO) | `../repositories/` |
| İndeks *kullanım* planı ve POC/slow-query triage | `../indexing/` |
| Şema değişikliğinin deployment'ı | `K018-deployment` |
| 171 tablonun tam sütun dökümü | `.ai/.sql/mysql/*.sql` |

## 3. Şema Yüzeyi (3 Blok)

| Blok | İçerik | Kanıt |
|---|---|---|
| **A — Çekirdek** | 5 DB topolojisi, anahtar tablolar (`users`, `tracks`, `playlists`, `albums`, `orders`, `categories`, `settings`) | `docs/database/01-entity-relationship-diagram.md:13-22` |
| **B — Uygulama bağlamı** | `oauth_connections` (content/ads DB), `api_keys` (AES-256-GCM anahtarları), test çiftleri (SQLite) | migration `shared/database/migrations/oauth_connections_migration.php:11` · Repository testleri |
| **C — Çapraz** | JC助手/SEO/ekonomi/oyun ekosistemleri; blog için MariaDB 11.4 hazır | `docs/database/01-entity-relationship-diagram.md:31-35` |

## 4. Kısıt ve Tip Kuralları (Kanonik)

| Kural | Kaynak |
|---|---|
| Kolonlar için sınır (limit) tanımla — `VARCHAR(255)` limiti yoksa MySQL hata verir | dev.mysql.com 8.4 Column Definitions |
| Anahtarsız `SELECT`/`DELETE`/`UPDATE` yasak | `.ai/.sql/CLAUDE.md §1` |
| `text` alanı **yok** — `LIMIT`'li sorgu zorunlu | `.ai/.sql/CLAUDE.md §1` |
| JOIN yasağı — JSON aggregate/ORM map; zorunlu JOIN raporlanır | `.ai/.sql/CLAUDE.md §1` |
| `FOR UPDATE` = pessimistic kilit; `FOR SHARE` = read lock (dev.mysql.com row-level) | MySQL row-level locking |
| Çakışan `UNIQUE` → `SQLSTATE[23000]`; `$e->errorInfo[1] = 1062` | MySQL error 1062 |

## 5. S-1 (Şema ↔ Arama)

Arama şeması (`novasearch`) proje kanunu olarak K007'de (`.ai/.sql/CLAUDE.md §1`:
"K007 Veri — Arama (novasearch/InnoDB)"). K-SCOPE-4 `⚠️ TASARIM` — bkz.
`../data-kapsam.md` §5.6 ve `../data-kararlar.md` K-06.

## 6. Karanlık Noktalar

1. `docs/database/README.md:40` — "TODO: Markdown sözlüğü MySQL sütun
   meta-verisiyle doğrulanacak; manuel senkron riski".
2. `.ai/.sql/mysql/reports/` — şema-drift raporları (database-review kanunu,
   `.ai/.sql/CLAUDE.md §2`): içerik bu belgede tekrar edilmez, `UNKNOWN`.
3. `database/README.md:14-18` — FK yönetim kararı (MySQL dump vs uygulama
   katmanı) **"İncelenecek"**.

## 7. Çekirdek DB Topolojisi Detayı

| # | DB | Rol | Kanıt |
|---|---|---|---|
| 1 | `coremusicnet` | ana web uygulaması | `docs/database/01-entity-relationship-diagram.md:13` |
| 2 | `coremusicnet_ads` | reklam/ads | aynı, `:13` |
| 3 | `coremusicnet_log` | log/olay | aynı, `:14` |
| 4 | `coremusicnet_customer` | iş ortağı | aynı, `:15` |
| 5 | `coremusicnet_ml` | makine öğrenmesi | aynı, `:16` |

Ek (K007 kanunu, `.ai/.sql/CLAUDE.md §1`): `novasearch` (arama, InnoDB) —
sahiplik K-06 `UNKNOWN`, bu belgede içeriği `UNKNOWN`.

Anahtar tablolar (ER belgesi `:22`): `users`, `tracks`, `playlists`,
`albums`, `orders`, `categories`, `settings` — sütun listeleri
`.ai/.sql/mysql/` dump'larında (`UNKNOWN` içerik).

## 8. Tip Seçimi Rehberi (Checklist)

| Alan ihtiyacı | Tip | Kaynak/kanun |
|---|---|---|
| Kimlik | `BIGINT UNSIGNED` (migration `oauth_connections_migration.php:14-15` kanıtı) | proje kanıtı |
| Kısa metin | `VARCHAR(n)` — **n zorunlu** | create-table #2 kanunu |
| Uzun metin | `text` **YOK**; `LIMIT`'li sorgu zorunlu | `.ai/.sql/CLAUDE.md §1` |
| Zaman | `DATETIME` (MySQL 8.4) | dev.mysql.com 8.4 |
| Karar/küçük liste | `ENUM` (SQLite çiftinde yok → test uyarısı) | `schema-dogrulama.md` §2 |
| Charset | `utf8mb4` + `utf8mb4_unicode_ci` (migration `:23-24` kanıtı) | mariadb.com unicode #8 |
| Boole/flag | `TINYINT(1)` veya `ENUM('0','1')` — proje kanıtı `UNKNOWN` | `⚠️ VERIFICATION REQUIRED` |

## 9. Normalizasyon Adımları (Proses)

1. Aday anahtarları çıkar (BCNF: her aday anahtar determinant — #10).
2. Fonksiyonel bağımlılıkları listele; `T-20` gibi `max(id)` anti-pattern
   ara (forum, K001 — `⚠️ VERIFICATION REQUIRED`).
3. Çapraz-DB tekrarları denetle (`docs/database/README.md:37` aşama 5).
4. Sonucu `.ai/.sql/mysql/reports/` raporuna yaz (yoksa `UNKNOWN`).
5. Sonucu `../data-dogrulama.md` §2 tablosuna satır olarak ekle.

## 10. Şema İnceleme (Review) Checklist

| # | Kontrol | Kanıt |
|---|---|---|
| 1 | Her `VARCHAR`/`CHAR` limitli | create-table #2 |
| 2 | `NOT NULL` veya açık `NULL` gerekçesi | InnoDB `IS NOT NULL` indeksi (#1 kanunu, 10.1) |
| 3 | PK mevcut ve tek | #2/#11 |
| 4 | `utf8mb4` | #3/#8 |
| 5 | `LIKE` yok (uygulama kodu) | `.ai/.sql/CLAUDE.md §1` |
| 6 | `SELECT *` yok | T-11 |
| 7 | `text` alanı yok | `.ai/.sql/CLAUDE.md §1` |
| 8 | Dump + doküman drift taraması | S-B06 |

## 11. Sınır Tablosu (Alt Alanlar Arası)

| Konu | `schema` | `migrations` | `indexing` | `transactions` |
|---|---|---|---|---|
| Alan tipi/kısıt | **✓ tasarlar** | yürütür | — | — |
| İndeks adı/fiziki | — | yaratır | **✓ tasarlar** | — |
| `FOR UPDATE` | — | — | — | **✓** |
| Şema sürümleme | — | **✓** | — | — |
| Çok-DB topoloji | **✓** | — | — | — |

## 12. Bağlantılar

- **Yukarı:** `../data-kapsam.md` §5 K-SCOPE-1 (tek referans).
- **Kardeş:** `migrations/` (değişiklik yürüten) · `indexing/` (indeks tasarımı
  ile örtüşür — sınır: `indexing` **fiziksel indeks**, `schema` **alan/kısıt**).
- **Dış:** `K008-service-layer` · `.ai/.sql/CLAUDE.md` (SSOT kanun dosyası).
