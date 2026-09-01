# 01. Requirements Gathering (Gereksinim Toplama)

## Genel Bakış

Veritabanı motoru, şema oluşturmadan önce katı bir gereksinim toplama sürecinden geçer. Eksik veya belirsiz kullanıcı taleplerine tahmin yürütülmez.

## Adım 1: Etkileşimli Protokol

Kullanıcı talebi çok kısa veya belirsiz ise (Örn: "Bana bir e-ticaret veritabanı yap"), motor derhal kodu yazmayı durdurur ve aşağıdaki soruları sorar:

| Soru | Amaç | Örnek |
|------|------|-------|
| Domain (İş Alanı) | Uygulamanın temel iş mantığı | Müzik platformu, e-ticaret |
| Tablolar | Hangi varlıklar gerekli? | users, products, orders |
| İlişkiler | Tablolar arası bağlantı | One-to-many, many-to-many |
| Ölçek | Beklenen veri büyüklüğü | <1M, 1M-100M, >100M satır |
| Motor | Hangi veritabanı motoru? | MySQL 9 (varsayılan) |
| Güvenlik | Şifrelenmesi gereken PII? | TC Kimlik, kredi kartı |
| Sorgu Kalıpları | Ana sorgular hangileri? | JOIN, aggregate, filtreleme |
| Mevcut Durum | Sıfırdan mı, taşıma mı? | Yeni proje, migration |

## Adım 2: Eksik Bilgi Şablonu

Eksik bilgi varsa kullanıcıya şu şablonu göster:

```markdown
Veritabanını tasarlayabilmem için şu bilgilere ihtiyacım var:

1. **Tablolar:** Hangi tabloları oluşturmak istiyorsunuz?
2. **İlişkiler:** Tablolar arası nasıl ilişkiler var?
3. **Alanlar:** Her tabloda hangi alanlar olacak?
4. **Güvenlik:** Şifrelenmesi gereken alanlar var mı?
5. **Ölçek:** Yaklaşık ne kadar veri bekleniyor?
6. **Sorgular:** En sık çalıştırılacak sorgular hangileri?
```

## Adım 3: Kanıt Tabanlı Çıktı

Talepler alındıktan sonra oluşturulacak şemadaki kritik kararlar kanıtlarla desteklenmelidir.

```sql
-- ADR-XXX: TIMESTAMP kullanıldı çünkü timezone çakışmalarını önlemek için en iyi yol bu.
-- ADR-XXX: total_price denormalize edildi çünkü raporlama ekranında çok ağır Join işlemlerini engellemek.
```

## Adım 4: CoreMusic Özel Gereksinimler

CoreMusic projesi için ek gereksinimler:

| Gereksinim | Detay |
|------------|-------|
| 11 BCNF DB | auth, users, musics, albums, playlist, catalog, logs, media, system, social, wireless |
| MySQL 9 | `utf8mb4_unicode_ci`, `ENGINE=InnoDB` |
| BIGINT UNSIGNED | Her PK `id BIGINT UNSIGNED AUTO_INCREMENT` |
| Audit Trail | Kritik tablolar için `_audit` tablosu |
| Soft Delete | `deleted_at TIMESTAMP NULL DEFAULT NULL` |
| PDO Only | ORM yasak, sadece PDO prepared statements |
| Snake Case | Tablo ve kolon isimleri snake_case |
