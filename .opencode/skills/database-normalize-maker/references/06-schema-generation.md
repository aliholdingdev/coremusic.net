# 06. Schema Generation & Verification Gates

## Çıktı Üretimi (Schema Generation)

`database-normalize-maker` Orkestratörü, tüm ajanların (Data, Security, Backend) onayını aldıktan sonra son aşama olan "Generation" (Üretim) aşamasına geçer. Ancak dosyaya yazmadan önce son bir **Verification Gate (Doğrulama Kilidi)** işletilir.

## Doğrulama Kilitleri (Verification Required)

Eğer sistem aşağıdaki durumlardan herhangi birine düşerse, kodu ÜRETMEZ ve ekrana `// ⚠️ VERIFICATION REQUIRED: [Sebep]` yazar:

1. **Desteklenmeyen Veri Tipi:** Örn: MySQL şemasında `ARRAY` tipi kullanmaya çalışılması (MySQL'de doğrudan array yoktur, JSON kullanılır).
2. **Kayıp İlişkiler (Orphaned Relations):** Yabancı anahtarın işaret ettiği ebeveyn tablonun (Parent Table) şemada bulunmaması.
3. **Eksik Audit Trail:** Kritik olarak işaretlenmiş (Örn: PII içeren) bir tablonun `_audit` tablosunun üretilmemiş olması.
4. **Sentaks Uyumsuzluğu:** PostgreSQL `uuid_generate_v4()` fonksiyonu çağrılıyorsa, "uuid-ossp" eklentisinin (extension) kurulduğunun script içinde (Örn: `CREATE EXTENSION IF NOT EXISTS "uuid-ossp";`) belirtilmemesi.

## Çıktı Dosyaları

Doğrulamadan geçen sistem aşağıdaki dosyaları otonom olarak üretir (Eğer klasör yoksa oluşturulur):

1. **`{proje_adi}-schema.sql`**
   - Tamamen temizlenmiş, yorumlanmış (ADR'ler dahil) DDL (Data Definition Language) scripti.
2. **`{proje_adi}-seed.sql`**
   - Şemayı test etmek için kurgusal (fictional) ama formata birebir uygun test verileri. (PII kuralı gereği gerçek isim veya email kullanılmaz).
3. **`{proje_adi}-dictionary.md`**
   - Her bir tablonun ve kolonun açıklamasını içeren "Veri Sözlüğü".
4. **`{proje_adi}-er.md`**
   - Mermaid.js formatında ER diyagramı.

## Migration (Göç) Stratejisi
Orkestratör, mevcut bir proje dizini tespit ederse (Örn: Laravel `database/migrations` veya Phinx dizini), tek bir `schema.sql` üretmek yerine, yapıyı ilgili framework'ün migration dosyalarına (PHP sınıflarına) dönüştürerek yazar. Bu işlem "Backend Architect" ajanı ile koordineli yapılır.
