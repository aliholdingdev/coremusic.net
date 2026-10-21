# 09 — Anti-Pattern Kataloğu (Tam)

> Her anti-pattern: sorun → çözüm. SKILL.md §9 özeti burada genişletilir.

| # | Anti-pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 1 | `FLOAT`/`DOUBLE` para | Yuvarlama hatası, tutarsız toplam | `DECIMAL(p,s)` |
| 2 | `VARCHAR(255)` her yerde | Niyet gizlenir, alan israfı | Gerçek boyut: 20/50/100/255 |
| 3 | Partial index (`... WHERE`) | MySQL'de **desteklenmez** | Composite index / generated kolon |
| 4 | Polymorphic FK (`type`+`id`) | FK bütümlülüğü yok | Ayrı tablalar veya uygulama denetimi |
| 5 | ENUM sabit | Değişiklik zor, ALTER gerekir | Lookup tablosu (audit aksiyon ENUM hariç) |
| 6 | FK'ya index yok | Yavaş JOIN/DELETE, lock | `KEY idx_{tablo}_id ({tablo}_id)` |
| 7 | `SELECT *` | Performans + sözleşme kırılır | Açık kolon listesi (ADR-014) |
| 8 | Hard delete | Geri alınamaz | `deleted_at` (soft delete) |
| 9 | Tarih VARCHAR | Sıralama/karşılaştırma bozuk | `DATE/DATETIME/TIMESTAMP` |
| 10 | Tek büyük backfill UPDATE | Uzun lock | id aralıklı batch |
| 11 | Transitive dependency (3NF ihlali) | Anomali | Non-key bağımlılığı ayrı tabloya |
| 12 | Kısmi bağımlılık (2NF ihlali) | Anomali | Parçalı key'i ayır |
| 13 | Denormalize toplam (ADR'siz) | Tutarlılık kaybı | ADR + senkron; source-of-truth normalize |
| 14 | Circular FK dependency | Populate/zincirleme kırılır | Dependency analizi, nullable FK veya faz |
| 15 | EAV (entity-attribute-value) | Sorgu karmaşık, tip yok | Structured schema + JSON |
| 16 | Audit trail yok | Değişiklik izlenemez | `{tablo}_audit` + trigger |
| 17 | Soft delete + naive unique | Silinmiş kayıt çakışır | `(kolon, deleted_at)` composite unique |
| 18 | Aşırı index | Yazma yavaşlar, disk | EXPLAIN ile gereksiz index kaldır |
| 19 | Collation karışımı | Implicit conversion, index kaybı | Tek collation (join/unique kolonlar aynı) |
| 20 | Gereksiz stored procedure | Bakım zor | İş mantığı PHP'de; SP minimum |

## Sık karşılaşılan 3 acil düzeltme

1. Para kolonu `FLOAT` → `DECIMAL(10,2)` (migration: expand-contract).
2. FK'ya `KEY` eklemediysen → `ALTER TABLE ... ADD KEY idx_{tablo}_{col} ({col})`.
3. Hard delete kodu → `UPDATE ... SET deleted_at = NOW()`.

## Denetim

`scripts/normalize-checker.php` + `scripts/security-audit.php` bu anti-pattern'lerin bir
alt kümesini statik tarar. Canlı çalıştırma bu ortamda `VERIFICATION REQUIRED` (PHP yok).
