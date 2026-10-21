# 10 — QA Kontrol Listesi

> Teslim öncesi detaylı denetim. Kalite kapısı ile birlikte çalışır:
> [checklist.md](checklist.md) (PASS/BLOCK) buradaki maddelerin hızlı sürümüdür.

## Tablo yapısı

- [ ] Her tablonun PRIMARY KEY'i var.
- [ ] PK `BIGINT UNSIGNED AUTO_INCREMENT` (veya gerekçeli natural key).
- [ ] Engine `InnoDB`, charset `utf8mb4`, collation tutarlı.
- [ ] `created_at`, `updated_at`, `deleted_at` var (varlık tabloları).
- [ ] Kolon tipleri doğru: `DECIMAL` para, `DATE/DATETIME/TIMESTAMP` tarih.

## İlişkiler

- [ ] Her ilişki için `FOREIGN KEY` + `ON DELETE/UPDATE` stratejisi seçili.
- [ ] Her FK'ya karşılık index var (`idx_{tablo}_{col}`).
- [ ] Circular dependency yok (veya fazlandırılmış).
- [ ] Junction'da `UNIQUE(a,b)` tekrarı engelliyor.

## Normalizasyon

- [ ] 1NF: kolonlar atomik (liste/array yok).
- [ ] 2NF: composite PK'da kısmi bağımlılık yok.
- [ ] 3NF: transitive bağımlılık yok.
- [ ] BCNF: her determinant superkey.
- [ ] Denormalizasyon varsa ADR gerekçeli + source-of-truth belli.

## Güvenlik

- [ ] PII şifreli/AEAD; anahtar şemada değil.
- [ ] Kritik tablo için `_audit` + trigger.
- [ ] Hard delete yok (soft delete).
- [ ] `SELECT *` yok, sabit secret yok.

## Performans

- [ ] Sık WHERE/ORDER kolonları index'li.
- [ ] Composite index sıralaması equality → range/sort.
- [ ] `EXPLAIN`/`EXPLAIN ANALYZE` (8.0.18+) ile kanıtlanmış.
- [ ] Over-indexing yok; N+1 yok.

## Migration

- [ ] Tehlikeli değişiklik expand-contract.
- [ ] Backfill batch (lock kısa).
- [ ] Forward-only; geri dönüş planı yazılı.
- [ ] Prod DDL için root onayı.

## Genel

- [ ] Tablo/kolon `snake_case`; constraint adları tutarlı (`uk_`, `idx_`, `fk_`).
- [ ] Seed veri gerçek PII içermiyor.
- [ ] ER diyagramı + veri sözlüğü güncel.
