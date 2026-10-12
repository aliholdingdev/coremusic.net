# 10. QA Kontrol Listesi (25 Madde)

## Genel Bakış

SQL çıktısı üretilmeden önce bu kontrol listesi çalıştırılır. Herhangi bir madde başarısız olursa çıktı üretilmez.

## Tablo Yapısı (5 madde)

- [ ] Her tablonun PRIMARY KEY'i var mı? (`id BIGINT UNSIGNED AUTO_INCREMENT`)
- [ ] Tüm BIGINT UNSIGNED mi? (INT, SMALLINT yerine)
- [ ] Charset ve Collation tanımlı mı? (`utf8mb4_unicode_ci`)
- [ ] Engine InnoDB mi? (`ENGINE=InnoDB`)
- [ ] Timestamp kolonları var mı? (`created_at`, `updated_at`, `deleted_at`)

## İlişkiler (4 madde)

- [ ] Tüm Foreign Key'ler doğru tanımlı mı? (`REFERENCES tablo(id)`)
- [ ] ON DELETE stratejisi seçilmiş mi? (`CASCADE`, `RESTRICT`, `SET NULL`)
- [ ] FK'lara index eklendi mi? (`INDEX idx_{tablo}_id ({tablo}_id)`)
- [ ] Circular dependency yok mu? (FK zinciri döngüde olmamalı)

## Normalizasyon (3 madde)

- [ ] Tüm tablolar BCNF uyumlu mu? (1NF → 2NF → 3NF → BCNF)
- [ ] `SELECT *` kullanılmamış mı? (açık kolon listesi)
- [ ] Denormalizasyon varsa ADR ile gerekçelendirilmiş mi?

## Güvenlik (3 madde)

- [ ] PII alanları şifreli mi? (`ssn`, `credit_card` → AES-256-GCM)
- [ ] Audit tabloları var mı? (kritik tablolar için `_audit` tablosu)
- [ ] Hard delete yerine soft delete kullanılıyor mu? (`deleted_at`)

## Performans (3 madde)

- [ ] Sık sorgulanan kolonlara index eklendi mi?
- [ ] Composite index sıralaması doğru mu? (equality primero)
- [ ] Over-indexing yok mu? (her index yazma yavaşlatır)

## Migration (3 madde)

- [ ] Geri dönüş (down) migration var mı?
- [ ] Expand-contract pattern uygulandı mı? (tehlikeli değişikliklerde)
- [ ] Veri kaybı riski yok mu?

## Genel (4 madde)

- [ ] Tüm tabloların adı doğru mu? (snake_case, çoğul)
- [ ] Tüm kolonların adı doğru mu? (snake_case)
- [ ] Constraint adlandırma tutarlı mı? (`uk_`, `idx_`, `fk_`)
- [ ] Tüm SQL komutları MySQL 9 uyumlu mu?

## Toplam: 25 Madde

| Kategori | Madde Sayısı |
|----------|-------------|
| Tablo Yapısı | 5 |
| İlişkiler | 4 |
| Normalizasyon | 3 |
| Güvenlik | 3 |
| Performans | 3 |
| Migration | 3 |
| Genel | 4 |
| **Toplam** | **25** |
