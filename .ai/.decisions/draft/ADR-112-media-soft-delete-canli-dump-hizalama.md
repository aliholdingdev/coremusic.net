---
title: "CoreMusic — ADR-112: media_access/media_audit Soft-Delete — Canlı Şema Dump SSOT'a Hizalanır"
type: adr
category: database
date: 2026-11-01
status: accepted
authority: ADR-112 Karar Metni (SSOT)
related: "ADR-002 (raw PDO prepared statement) · ADR-092 (media ULID) · .ai/.rules/karar-kurallari.md §1"
---

# ADR-112: `media_access` / `media_audit` Soft-Delete Kolonları — Canlı → Dump Hizalaması

**Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-11-02 · **Önceki durum:** Draft → accepted terfi (2026-11-02)
**Karar Veren:** Bayram Ali
**Bulgu kaynağı:** backend-audit Faz 5 — stream 403 (2026-10-31)

## 1. Bağlam

Canlı `coremusic_media` DB'sinde `media_access` ve `media_audit` tablolarında `is_deleted` / `deleted_at` kolonları YOK; dump SSOT'ta VAR:

- Dump SSOT: `.ai/.sql/mysql/coremusic_media.sql` — `media_access` bloğu :157‑176 (`is_deleted` :167, `deleted_at` :168), `media_audit` bloğu :181‑201 (`is_deleted` :193, `deleted_at` :194)
- Kod: `media.coremusic.net/src/Media/MySqlMediaAccessRepository.php:48` — `hasReadGrant()` `AND is_deleted = 0` filtresiyle sorguluyor
- Şema farkı → PDOException (bilinmeyen kolon) → fail-closed 403 (2026-10-31 canlı stream kanıtı)
- Soft-delete pattern dump'taki TÜM coremusic_media tablolarında mevcut (2026-10-31 ölçümü)

## 2. Karar

**`.ai/.sql/mysql/*.sql` sözleşme SSOT'tur; canlı şema ona migration ile hizalanır.**

- `media_access` ve `media_audit` tablolarına `is_deleted` + `deleted_at` eklenir (expand migration; veri dönüşümü yok).
- Dump ve kod DEĞİŞMEZ.

```sql
-- Forward (önce idempotent pre-check: information_schema.COLUMNS ile kolon yokluğu teyit)
ALTER TABLE coremusic_media.media_access
  ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT '0' AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER is_deleted;

ALTER TABLE coremusic_media.media_audit
  ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at,
  ADD COLUMN is_deleted TINYINT(1) DEFAULT '0' AFTER updated_at,
  ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER is_deleted;

-- Rollback (ayrı dosya)
ALTER TABLE coremusic_media.media_access
  DROP COLUMN is_deleted, DROP COLUMN deleted_at;
ALTER TABLE coremusic_media.media_audit
  DROP COLUMN is_deleted, DROP COLUMN deleted_at, DROP COLUMN updated_at;
```

## 3. Gerekçe

| Seçenek | Sonuç | Değerlendirme |
|---|---|---|
| **A (kabul): canlıyı dump'a hizala** | migration ekler (2 tablo × 2 kolon); dump, kod değişmez | soft-delete pattern tüm media DB'de tutarlı; en küçük paket |
| B: dump'ı sadeleştir | dump revizyonu + `hasReadGrant` filtresi silme + kontrol | kod+dump iki yüzey dokunuşu; pattern bütünlüğü bozulur |
| C: kod esnetme (sorgudan `is_deleted` çıkar) | fail-open yönü | şema sözleşmesi sessizce maskelenir — YASAK |

- `media_audit`'te soft-delete kalmak zararsızdır: INSERT DEFAULT'lü yazıyor (`is_deleted`/`deleted_at` bilerek yazılmaz — repository :93‑95 kanıtı); audit append-only davranışı korunur.
- ADR-002 (raw PDO) ve fail-closed davranışı (metadata okunamıyorsa erişim yok) korunur.

## 4. Sonuçlar

- Migration yalnız kullanıcı onayıyla çalıştırılır: önce `mysqldump --single-transaction` yedeği, sonra forward, sonra doğrulama.
- Hizalama sonrası stream 403'ün şema kaynaklı hali çözülür; `.ai/log.md`'ye kanıt işlenir.
- `hasReadGrant` fail-closed kalır: kolon gizlice kaybolursa 403 geri döner — bu istenen fail-closed davranışıdır.

## 5. Gözden Geçirme Tetikleyicileri

1. Canlı DB'de yeni bir tabloda şema drift bulgusu çıkarsa (aynı kural hangi tablo için geçerli olursa olsun).
2. `media_audit` retention/arşivleme politikası çalışmalara girerse (soft-delete kolonun gereksizliği yeniden değerlendirilir).
3. `media_catalog.asset` ile `file_key` ULID sözleşmesi (ADR-092) revize edilirse.

## 6. Draft → Accepted Geçiş Şartı (tümü zorunlu)

- [x] Yedek dump alınmış ve dosyası mevcut (`shared/database/backups/20261102_media_pre_migration.sql`)
- [x] Forward migration canlıda çalıştırılmış; `SHOW COLUMNS` kanıtı 4 kolonu gösteriyor
- [x] Stream uç noktası beklenen çıktıyı veriyor (403/200 ayrımı; şema hatası kalmadı)
- [x] `.ai/log.md`'ye forward + doğrulama kaydı işlenmiş

## 7. Doğrulama Kanıtları

- Dump: `coremusic_media.sql:167`, `:168`, `:193`, `:194` (2026-11-01 okundu)
- Kod: `MySqlMediaAccessRepository.php:48` `is_deleted = 0` filtresi (2026-11-01 okundu)
- Canlı ölçüm: 2026-10-31 oturumu — canlıda iki kolon YOK (oturum kaydı + stream 403 kanıtı)
- Kural türevi: `.ai/.rules/karar-kurallari.md` §1
