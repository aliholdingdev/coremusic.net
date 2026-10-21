# Değişiklik Günlüğü — database-normalize-maker

> Bu dosya skill'in sürüm geçmişi. Kök `changelog` yerine (N10) bu dizinde tutulur.

## 4.0.0 — 2026-10-21

**Kapsamlı yeniden yazım (sıfırdan, mojibake temiz).**

- SKILL.md Claude Skill v3.0 N1–N10'a göre yeniden yapılandırıldı (frontmatter yalnız
  name/description/license/metadata; ≥6 bölüm; footer = metadata.version + Updated 2026-10-21).
- Hedef motor netleştirildi: **MySQL 8.0 (LTS, InnoDB, utf8mb4)**. Vault'taki "MySQL 9"
  (Innovation) ifadesiyle fark `db-engine-notes.md` + `sources.md` içinde `VERIFICATION REQUIRED`.
- **Düzeltme (zero-hallucination):** eski içerikteki “partial index (MySQL 8+)”
  (`CREATE INDEX ... WHERE`) MySQL'de **desteklenmez** — kaldırıldı, yerine composite index /
  generated kolon çözümleri eklendi (03-provider-dialects.md).
- Fonksiyonel bağımlılık + aday anahtar çıkarımı (Armstrong, kapanım) eklendi (§3, 02).
- BCNF/3NF/2NF/1NF sıkı tanımlar + lossless/dependency-preserving ayrıştırma eklendi.
- Expand-and-contract migration, forward-only, batch backfill, EXPLAIN/EXPLAIN ANALYZE,
  N+1, soft-delete + composite-unique, type seçimi (INT/BIGINT, DECIMAL, VARCHAR, TIMESTAMP/DATETIME).
- references/ 13 → 15 dosya: `sources.md` (resmi kaynak, URL + erişim tarihi) ve
  `checklist.md` (PASS/BLOCK kalite kapısı) eklendi.
- scripts/ 3 PHP sıfırdan yazıldı (strict_types, saf analiz, `--help`, mojibake yok).
- Eski “11 DB / 68 tablo” bayat envanter listesi kaldırıldı; sayı iddiası yok, SSOT vault.

## 3.0.0 — 2026-10-07

- v3.0 format revizyonu; referans dosyaları ve script'ler eklendi.
- db-engine-notes merge (18 DB envanter iddiası — sonraki sürümde doğrulandı/bayat işaretlendi).

## 5.1 (önceki) — tarih arşivde

- İlk geniş SQL komut referansı sürümü.
