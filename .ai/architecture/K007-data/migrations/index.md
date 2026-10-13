---
node_id: K007-data/migrations
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - migrations-anayasa
  - migrations-surucu
  - migrations-ornekler
depends_on:
  - "K007-data/schema"
evidence:
  - "shared/database/migrations/oauth_connections_migration.php — CREATE TABLE + INSERT"
  - "shared/database/migrations/oauth_states_migration.php — migration akışı"
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php — DROP (geri alma)"
validation:
  - "2026-10-13 — index yeniden yazıldı; children disk enumerate 3/3; mojibake temiz"
open_questions:
  - "Migration çalıştırma aracı/CLI → repo'da kanıt yok → ⚠️ VERIFICATION REQUIRED"
  - "Rollback (down) stratejisi → UNKNOWN"
group: mimari
---

# migrations — Alt-Konu İndeksi (Kademe-3)

| Alan | Değer |
|---|---|
| node_id | K007-data/migrations |
| node_type | subdomain |
| parent_id | K007-data |
| Rol | Şema değişikliğinin sürümlü, geri alınabilir taşınması |
| Durum | draft |

## §1 Ne işe yarar?

Bu alt konu K007'nin **zaman** ayağıdır: şema (tablolar, indeksler, constraint'ler)
değiştiğinde bunu **sürümlü ve tekrarlanabilir** biçimde uygulamak. İlgili üç
gerçek migration dosyası `shared/database/migrations/` altında disktedir;
en dikkat çekeni `2026-10_07_drop_coremusic_api_api_keys_migration.php`
(tablo düşürme — geri alınamaz sınıf). DDL'nin transaction ile ilişkisi
(implicit commit) `transactions-anayasa.md` Y-09'da işaretlenmiştir.

## §2 İçerik dosyaları (kademe-3)

| # | Dosya | Rol | Bütçe |
|---|-------|-----|-------|
| 1 | `migrations-anayasa.md` | Sürümleme, geri alma, DDL/transaction kuralı | ≥500 |
| 2 | `migrations-surucu.md` | Uygulama sırası, hata kolu, ortam geçişi | ≥500 |
| 3 | `migrations-ornekler.md` | Gerçek 3 migration dosyası + SQL DDL örnekleri | ≥500 |

## §3 Kanıt (repo)

| İddia | Kanıt |
|-------|-------|
| Migration dosyaları var | `shared/database/migrations/` = 3 PHP dosyası |
| CREATE TABLE + INSERT migration'ı | `oauth_connections_migration.php` |
| DROP migration'ı | `2026-10_07_drop_coremusic_api_api_keys_migration.php` |
| Şablon zorunlu | `shared/AGENTS.md §4.4` — migration `.ai/.templates/infrastructure/migration-template.md`'den türetilir |

## §4 Bağımlılık

| Yön | Düğüm | İlişki |
|-----|-------|--------|
| Üst | `../index.md` | K007-data künyesi |
| Üst | `../data-bagimliliklar.md` | Kök bağımlılık defteri |
| Alt | 3 içerik dosyası | `children` 3/3 |

## §5 Kapsam dışı

| Dış | Nereye |
|-----|--------|
| Tablo tasarımı | K007-data/schema |
| Migration'ın atomikliği | K007-data/transactions (Y-09) |
| Veri kalitesi kuralları | K007-data/data-quality |

## §6 Durum

| Ölçüt | Sonuç |
|-------|-------|
| Rol tanımı | ✅ bu dosyada yazıldı (kanıt: 3 migration dosyası) |
| Kademe-3 yapısı | ✅ children 3/3 |
| Migration aracı (CLI) | ❌ yok → ⚠️ VERIFICATION REQUIRED |
| status | draft |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
