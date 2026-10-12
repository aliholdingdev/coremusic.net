# 09. Anti-Pattern Kataloğu

## Genel Bakış

Aşağıdaki yaklaşımlar kesinlikle reddedilir. Her anti-pattern'in neden yanlış olduğu ve doğru çözümü açıklanmıştır.

## Veri Tipi Anti-Pattern'leri

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 1 | `VARCHAR(255)` her yerde | Gereksiz yer kaplar, niyet gizlenir | Gerçek boyuta göre seç: `VARCHAR(50)`, `VARCHAR(100)` |
| 2 | `FLOAT` para birimi | Yuvarlama hataları | `DECIMAL(10,2)` |
| 3 | Tarihler VARCHAR olarak | Sıralama/karşılaştırma bozulur | `DATE`, `TIMESTAMP`, `DATETIME` |
| 4 | MySQL'de PostgreSQL tipleri | `UUID[]`, `INET` desteklenmez | MySQL desteklenen tipler |
| 5 | `BOOLEAN` yerine `TINYINT(2)` | Anlamsız boyut | `TINYINT(1)` |

## İlişki Anti-Pattern'leri

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 6 | FK constraint yok | Yetim kayıtlar, veri bozulması | Her ilişkiye `FOREIGN KEY` + `ON DELETE` |
| 7 | FK'lara index yok | Çok yavaş JOIN'ler | `INDEX idx_{tablo}_id ({tablo}_id)` |
| 8 | Circular dependency | Populate edilemez, CASCADE kırılır | Dependency analizi |
| 9 | Polymorphic association | FK bütünlüğü yok | Ayrı tablolar veya `type` + `id` |

## Normalizasyon Anti-Pattern'leri

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 10 | Virgülle ayrılmış değerler | 1NF ihlali | Ayrı pivot tablosu |
| 11 | EAV (Entity-Attribute-Value) | Sorgu karmaşıklığı, tip güvenliği yok | Structured schema + JSON |
| 12 | Denormalizasyon (ADR'siz) | Veri tutarsızlığı | ADR ile gerekçelendir |
| 13 | `department_name` gibi kolonlar | 3NF/BCNF ihlali | Ayrı tabloya çıkar |

## Güvenlik Anti-Pattern'leri

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 14 | `SELECT *` kullanımı | Performans kaybı, API sözleşme kırılması | Açık kolon listesi |
| 15 | Audit trail yok | Değişiklik takibi yapılamaz | `_audit` tablosu + trigger |
| 16 | Soft delete yok | Hard delete geri alınamaz | `deleted_at TIMESTAMP NULL` |
| 17 | PII düz metin | Veri sızıntısı riski | AES-256-GCM şifreleme |

## Yapısal Anti-Pattern'leri

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 18 | `ENUM` sabitleri | Değişiklik zor, ALTER TABLE gerektirir | Lookup tablosu |
| 19 | 100+ kolonlu tablo | Bakım zorluğu | Dikey bölümleme |
| 20 | Gereksiz stored procedure | Bakım zor, PHP'de yapılabilir | İş mantığı PHP'de |
| 21 | Pivot tabloda `deleted_at` | Gereksiz complexity | Hard delete |
| 22 | Composite PK'da eksik index | Kısmi sorgular yavaş | Composite index ekle |
| 23 | Gereksiz UUID PK | InnoDB clustered index kırılır | BIGINT UNSIGNED |
| 24 | `CHAR(36)` UUID string | Yer israfı (36 byte) | `BINARY(16)` veya `BIGINT UNSIGNED` |

## Soft Delete Hataları

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 25 | Her tabloya körü körüne `deleted_at` | Gereksiz complexity | Pivot tablolar hard delete |
| 26 | Hard delete olmadan soft delete | Veri birikir | Zaman bazlı temizleme |
| 27 | `deleted_at` olmadan sorgulama | Silinmiş veriler görünür | Her sorguya `WHERE deleted_at IS NULL` |

## İsim Anti-Pattern'leri

| # | Anti-Pattern | Sorun | Çözüm |
|---|--------------|-------|-------|
| 28 | Tabloda `id` olmayan tablo | Standart dışı | Her tabloda `id BIGINT UNSIGNED` |
| 29 | Büyük harfli tablo adı | MySQL case sensitivity sorunu | snake_case |
| 30 | `order`, `user` gibi rezerve kelime | SQL keyword çatışması | `orders`, `users` |
