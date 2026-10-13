---
node_id: "K007-schema-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — şema kanun dosyası (2026-10-07)"
  - "docs/database/01-entity-relationship-diagram.md:13-35 — semantik ER katmanı"
  - "shared/src/Database/DatabaseManager.php:14-67 — uygulama PDO kapısı"
validation: "9 bileşen (B-01..B-09); her biri kanıt+rol+K007 bağlantısı; 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: mimari
---

# K007-SHEMA — Bileşenler

> Kök bileşen envanteri: `../data-bilesenler.md` (B-01..B-08, P-01..P-07).
> Bu dosya şema alt alanına özgü 9 bileşeni (S-B01..S-B09) tanımlar.

| # | Bileşen | Kanıt | Rol |
|---|---|---|---|
| S-B01 | **Şema kanun dosyası** | `.ai/.sql/CLAUDE.md §1` | BCNF/LIKE/anahtarsız/JOIN kuralları — şema inceleme checklist'i |
| S-B02 | **Semantik ER katmanı** | `docs/database/01-entity-relationship-diagram.md:13-35` | 5 DB topolojisi + 30+ tablo ilişkisi (Markdown) |
| S-B03 | **MySQL dump arşivi** | `.ai/.sql/mysql/*.sql` (içerik `UNKNOWN` — okunmadı) | 171 tablonun fiziksel kaynağı (SSOT) |
| S-B04 | **`DatabaseManager`** | `shared/src/Database/DatabaseManager.php:12-67` | şemanın uygulamadaki tek PDO kapısı (ADR-002) |
| S-B05 | **`DatabaseConfig`** | `DatabaseManager.php:5` (`use CoreMusic\Database\Config\DatabaseConfig;`) | host/port/dbName/charset — DSN üretimi (içerik `UNKNOWN`) |
| S-B06 | **Şema-drift raporları** | `.ai/.sql/mysql/reports/` (diskte `UNKNOWN`) | database-review kanunu `.ai/.sql/CLAUDE.md §2` |
| S-B07 | **DB sözlüğü + sorgu sözlüğü** | `docs/database/README.md:9-13, 40` | primitif/çözüm/SQL/ORM — senkron TODO |
| S-B08 | **SQLite test çifti** | `shared/tests/Repository/Sqlite/*` | şema farklarını testte simüle et (T-06) |
| S-B09 | **Migration şablonu** | `.ai/.templates/infrastructure/migration-template.md` (referans, okunmadı `UNKNOWN`) | yeni şema değişikliği türetilmesi (shared AGENTS kuralı 4) |

## S-B01 — Şema Kanun Dosyası (SSOT)

`.ai/.sql/CLAUDE.md §1` içindeki 6 kanun bu alt alanın checklist'idir:
18 BCNF DB / LIKE yasağı / anahtarsız sorgu yasağı / `text`+`LIMIT` /
JOIN alternatifi / arama K007. Doğrulama satırları:
`schema-dogrulama.md` SÇ-01..SÇ-05.

## S-B02 — Semantik ER Katmanı

Markdown ER (`docs/database/01-*.md`) fiziksel dump'ın üstünde katmandır:
kaynak `README.md:9-13` ("pragmatic single source of truth"). Çekirdek 5 DB
(`schema-kapsam.md` §3 Blok A). Kayda değer: forum, JC助手, SEO, ekonomi,
oyun ekosistemleri — bu belgelerde `UNKNOWN`, yalnız varlık kaydedildi.

## S-B03 — MySQL Dump Arşivi

`.ai/.sql/mysql/` altı: çekirdek dump + klasörler (`ads/`, `novasearch/`,
`reports/` — `.gitignore:61-63` adresli). İçerik bu üretimde okunmadı →
sütun dökümü iddiaları `UNKNOWN`. Erişim: `SHOW CREATE TABLE`/dump incelemesi
(elle, `qa` kapsamında).

## S-B04 — DatabaseManager (Tek PDO Kapısı)

Kanıt `DatabaseManager.php:12-67`: DSN (14-20) · 4 seçenek (22-27) ·
`execute`/`write` (30-42) · `lastInsertId` (44-47) · transaction yüzeyi
(49-62) · `getPdo()` (64-67). Şema değişikliğinin koda etkisi bu sınıfın
dışına taşmaz (kural: DatabaseManager dışında bağlantı açılmaz —
`shared/AGENTS.md` Zorunlu 1).

## S-B05 — DatabaseConfig

`DatabaseManager.php:5` import'u; DSN'e `host/port/dbName/charset` geçer.
İçerik (varsayılan charset değeri, kaynak dosya) okunmadı → `UNKNOWN`.

## S-B06 — Şema-Drift Raporları

`.ai/.sql/mysql/reports/` — `.gitignore` adresli, çalışma ağacındaki
varlığı `UNKNOWN` (kök belgeler `git status: D` notu veriyor). Yoksa
drift ölçümü yapılamaz → `schema-dogrulama.md` §6.3.

## S-B07 — Sözlükler

`docs/database/README.md` sorgu sözlüğü/DB sözlüğü dizini (`:9-13`); senkron
zorunluluğu `:40` TODO. `K007-data` içindeki karşılığı `evrensel-dil` ile
örtüşür (M-11 `CLAUDE.md → DB.md` tekilleştirmesi).

## S-B08 — SQLite Test Çifti

`shared/tests/Repository/Sqlite/` — MySQL hedefli repository testlerinin
çift şema katmanı (T-06 kök). Şema tasarımı için anlamı: kısıt/tip farkları
(`ENUM`, auto-increment) testte yakalanmaz, `⚠️` ile işaretli
(`schema-dogrulama.md` §2).

## S-B09 — Migration Şablonu

`.ai/.templates/infrastructure/migration-template.md` — `shared/AGENTS.md`
kural 4 ("Migration → şablondan türetilir"). Bu belgede okunmadı → içerik
`UNKNOWN`; uygulama `../migrations/` alt alanının görevi.

## Bağlantılar

- **Yukarı:** `../data-bilesenler.md` (B-01 `DatabaseManager`, B-08 `PageCacheAdapter`).
- **Kardeş:** `migrations-bilesenler` (S-B09'un tüketeni) · `indexing-bilesenler` (S-B03'ün indeks okuyucusu).
- **Dış:** `K008-service-layer` · `.ai/.sql/CLAUDE.md`.

## Bileşen Olgunluk Matrisi

| Bileşen | Olgu | Kanıt | Risk |
|---|---|---|---|
| S-B01 | ✓ kanun dosyası aktif | `.ai/.sql/CLAUDE.md §1` | — |
| S-B02 | ✓ Markdown ER mevcut | `docs/database/01-*.md` | senkron (R-06) |
| S-B03 | `UNKNOWN` içeriği | `.ai/.sql/mysql/` okunmadı | erişim |
| S-B04 | ✓ üretim kodu | `DatabaseManager.php:12-67` | persistent bağlantı (R-10 kök) |
| S-B05 | `UNKNOWN` içeriği | import kanıtı var | — |
| S-B06 | `UNKNOWN` varlığı | `.gitignore:63` adresli | drift ölçümü yok |
| S-B07 | ⚠️ senkron TODO | `README.md:40` | R-06 |
| S-B08 | ✓ test çiftleri | `tests/Repository/Sqlite/` | ENUM farkı |
| S-B09 | `UNKNOWN` şablon içeriği | referans yol | — |

## Bağımlılık Grafiği (Yazılı)

```
S-B01 (kanun) ──→ inceleme checklist ──→ şema kararı
S-B02 (ER)    ──→ S-B03 (dump)          ──→ S-B04 (PDO)
S-B05 (config)─→ S-B04 (DSN üretimi)
S-B08 (test)  ──→ repository testleri (T-06/T-09)
S-B09 (şablon)─→ migrations alt alanı
```

## Sahiplik ve Kullanım (K007 Bağlamı)

| Bileşen | Kim okur/yazar | Sıklık |
|---|---|---|
| S-B01 kanun | `data-engineer` (inceleme) | her şema değişikliğinde |
| S-B02 ER | tüm K007 alt alanları | tasarım |
| S-B03 dump | `data-engineer` + DBA | sürümleme (`migrations/`) |
| S-B04 manager | uygulama (repository) | her istek |
| S-B06 raporlar | `qa-engineer`/review | periyodik (`UNKNOWN` sıklık) |
| S-B08 test çifti | `test-writer`/`qa-engineer` | test koşusu |

## Sonraki Adım Önerisi

1. FK kararını kapat (`docs/database/README.md:14-18` → ADR).
2. `.ai/.sql/mysql/reports/` varlığını tespit et → drift ölçümü (§6.3).
3. `DatabaseConfig` içeriğini oku → charset değeri `UNKNOWN`'ı kapat.

## S-B Kontrol Listesi (Kısa)

| # | Bileşen | Kanıt durumu | Aksiyon |
|---|---|---|---|
| 1 | S-B01 | ✓ | — |
| 2 | S-B02 | ✓ | — |
| 3 | S-B03 | `UNKNOWN` | dump oku (devir: migrations) |
| 4 | S-B04 | ✓ | — |
| 5 | S-B05 | `UNKNOWN` | config oku |
| 6 | S-B06 | `UNKNOWN` | rapor varlığını doğrula |
| 7 | S-B07 | ⚠️ | senkron TODO kapat |
| 8 | S-B08 | ✓ | — |
| 9 | S-B09 | `UNKNOWN` | şablon oku (devir: migrations) |
