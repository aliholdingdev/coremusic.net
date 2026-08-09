# 05. Performance Optimization & Data Engineer Role

## Data Engineer Ajanının Rolü

Veritabanı orkestrasyon sürecinde **Data Engineer** ajanı, şemanın sadece "doğru" olmasını değil, aynı zamanda milyonlarca satır veride "hızlı" çalışmasını sağlar. Bu ajan, indeksleme (indexing), bölümleme (partitioning) ve sorgu analizleri konularında yetkilidir.

## Performans Denetim Kuralları

### 1. Kapsayıcı İndeksler (Covering Indexes)
Yalnızca `Primary Key` indekslemek yetmez. Sık yapılan sorgularda (Örn: Bir kullanıcının aktif siparişlerini listelemek) veritabanının tabloya (table scan) gitmesini engellemek için **Kapsayıcı İndeksler** zorunludur.
- **Orkestratör Kuralı:** Yabancı anahtarlar (Foreign Keys) otomatik olarak indekslenmelidir.
- **Örnek:** `CREATE INDEX idx_user_status ON orders(user_id, status);`

### 2. Aşırı İndeksleme (Over-indexing) Tuzağı
Data Engineer, "her kolona index atma" halüsinasyonunu (Zero-Hallucination) engeller. İndeksler okumayı hızlandırır ancak yazmayı (INSERT/UPDATE) yavaşlatır. Sistem, "Bu tablo Write-Heavy mi yoksa Read-Heavy mi?" sorusunu sorarak indeks kararını vermelidir.

### 3. JSON ve Metin (Text) İndeksleme
MySQL ve PostgreSQL'de büyük metin veya JSON verileri doğrudan `B-Tree` olarak indekslenemez veya çok verimsiz olur.
- **PostgreSQL:** JSONB kolonları için `GIN` indeksleri zorunlu tutulmalıdır.
- **MySQL:** JSON içindeki kritik alanlar için "Virtual Column" oluşturulup indeks atılmalıdır. (Eğer doğrudan JSON indekslenmeye çalışılırsa, Truth Mode hata fırlatır).

### 4. Büyük Tablolar İçin Bölümleme (Partitioning)
Eğer Gereksinim Toplama (Requirements Gathering) aşamasında kullanıcı >100M satır veri belirttiyse:
- Log tabloları veya zaman serisi tabloları (Örn: `sensor_data`, `audit_logs`) tarihe (RANGE) veya bölgeye (LIST) göre partition'lara bölünmelidir.
- **Zorunlu Araştırma:** Data Engineer ajanı, güncel partition sintaksı için web taraması yapmadan SQL oluşturamaz.

### 5. N+1 Probleminden Kaçınma Mimari Tasarımı
Eğer Backend ORM kullanacaksa (Örn: Eloquent), veritabanı tasarımı ara (pivot) tabloları düzgün şekilde tanımlayarak Eager Loading yapılabilmesine olanak tanımalıdır. Eksik yabancı anahtar (FK) kısıtlamaları reddedilir.
