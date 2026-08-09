# 09. Anti-Patterns & Hallucination Prevention

## Halüsinasyon ve Anti-Pattern Engelleme

AI Agentic Orchestrator'ın varlık sebebi, klasik LLM'lerin veritabanı tasarlarken düştüğü hataları (Halüsinasyon) ve Anti-Pattern'leri engellemektir. Aşağıdaki yaklaşımlar sistem tarafından **kesinlikle reddedilir**.

### 1. Halüsinasyon Veri Tipleri (Syntax Hallucination)
- **Hata:** MySQL 8'de PostgreSQL'e özgü `UUID[]` veya `INET` tiplerini kullanmaya çalışmak.
- **Çözüm (Truth Mode):** Sistem, hedeflenen motorun desteklediği kolon tiplerini otonom olarak (gerekirse web araması yaparak) doğrular. Uydurma tip kullanılırsa kod üretimi durdurulur.

### 2. Virgülle Ayrılmış Değerler (Comma-Separated Values)
- **Hata:** `roles` kolonunun içine `"admin,editor,user"` şeklinde metin (String) kaydetmek (1NF İhlali).
- **Çözüm:** Orkestratör, bunun yerine otonom olarak bir `user_roles` veya `role_user` (Pivot Tablo) tasarlar.

### 3. Çok Amaçlı "Polymorphic" Çöplükler
- **Hata:** `entity_id` ve `entity_type` ile her tabloya bağlanabilen "yorumlar" (comments) veya "görseller" (images) tablosu yapmak (Dış anahtar - FK kısıtlaması atılamadığı için veri bütünlüğü kaybolur).
- **Çözüm (CoreMusic Kuralı):** BCNF ihlalidir. Bunun yerine `post_comments`, `product_comments` şeklinde ayrılmış veya ara tablolar (Join Tables) kullanılır.

### 4. Mantıksal Silme (Soft Delete) Halüsinasyonu
- **Hata:** Her tabloya, (pivot tablolar dahil) körü körüne `deleted_at` eklemek. 
- **Çözüm:** Pivot tablolar (Many-to-Many ilişkiler) `deleted_at` barındırmaz, ilişki koptuğunda veri gerçekten (Hard Delete) silinmelidir.

### 5. `SELECT *` Uyumlu Şema Tasarımı
- **Hata:** Uygulamanın her yere `SELECT *` atacağını varsayıp, aşırı büyük (100+ kolon) "Şişman Tablolar" (Fat Tables) oluşturmak.
- **Çözüm:** "Dikey Bölümleme" (Vertical Partitioning). Çok sık okunan ana veriler ile nadir okunan büyük veriler (Örn: `user_bio`, `long_description`) farklı tablolara bölünerek (1-to-1 ilişki) performans korunur.
