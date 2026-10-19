# ADR-003 — Veritabanı: SQLite (dev) + MySQL (prod)

- **Durum:** Kabul (önceki "PostgreSQL" kaydının yerine geçer)
- **Tarih:** 2026-10-16
- **Alan:** Veri

## Bağlam

Üç aday: PostgreSQL (eski ADR), MySQL (kod tarafı `start-api-mysql.bat`), SQLite
(mevcut geliştirme verisi). Kullanıcı kararı: **SQLite dev + MySQL prod**.

## Karar

- Geliştirme/test: **SQLite** (`node:sqlite`, gömülü, sıfır bağımlılık)
- Üretim: **MySQL 8** (`mysql2` havuzu, prepared statement)
- Tek sözleşmesi: `DbAdapter` (`all/get/run/exec/transaction`) — `packages/database/src/adapter.ts`
- Şema tek kaynak: `schema.ts` (iki dialekt DDL) + idempotent `migrate()`

## Gerekçe

- SQLite: kurulum yok, tek dosya, yerel/kişisel kullanım için ideal
- MySQL: üretim ölçeği + olgun ekosistem
- Adapter sayesinde uygulama kodu dialekten bağımsız kalır

## Sonuçlar

- Upsert dedup: `ON CONFLICT DO UPDATE` / `ON DUPLICATE KEY UPDATE`
- MySQL canlı bağlantı henüz test EDİLMEDİ (açık konu — bkz. MEMORY.md)
- PostgreSQL seçeneği rafa kalktı; geri dönüş ADR güncellemesi gerektirir
