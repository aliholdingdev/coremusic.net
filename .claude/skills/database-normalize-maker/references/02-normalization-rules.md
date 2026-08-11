# 02. Normalization Rules & Truth Mode Controls

## Sıkı Normalizasyon (Strict Normalization)

Agentic Orchestrator sistemi, oluşturulan tabloları otonom olarak (Data Engineer ajanı rolüyle) BCNF (Boyce-Codd Normal Form) seviyesine kadar denetler. **Normalizasyon ihlalleri sessizce geçiştirilemez.** Eğer performansı artırmak için bilinçli bir "denormalizasyon" yapılacaksa, bu mutlaka açıkça gerekçelendirilmeli ve belgelenmelidir.

## Normalizasyon Aşamaları ve Doğrulama Kilitleri (Truth Mode)

### 1NF (First Normal Form) - Atomik Veri
- **Kural:** Her kolon (sütun) bölünemez tek bir (atomik) değer tutmalıdır. Virgülle ayrılmış değerler (`tags`, `hobbies` vb. string içinde) YASAKTIR.
- **Truth Mode Denetimi:** JSON kolonları kullanılıyorsa, JSON'ın *ilişkisel (relational)* bir veriyi mi (hata) yoksa tamamen esnek/şemasız bir meta-veriyi mi (doğru) tuttuğu çapraz kontrol edilir.

### 2NF (Second Normal Form) - Kısmi Bağımlılık (Partial Dependency)
- **Kural:** 1NF sağlanmış olmalı. Ek olarak, eğer tabloda kompozit (birden fazla kolondan oluşan) bir Primary Key varsa, Primary Key olmayan hiçbir kolon, Primary Key'in sadece bir kısmına bağımlı Olamaz.
- **Truth Mode Denetimi:** Her tablonun ideal olarak *tekil (surrogate)* bir Primary Key'i (Örn: `id BIGINT UNSIGNED` veya `UUID`) olması sağlanarak bu sorun temelden çözülür.

### 3NF (Third Normal Form) - Geçişli Bağımlılık (Transitive Dependency)
- **Kural:** 2NF sağlanmış olmalı. Anahtar (Key) olmayan hiçbir kolon, anahtar olmayan başka bir kolona bağımlı olamaz (Örn: `user_id`, `department_id`, `department_name` aynı tabloda olamaz. `department_name` kendi tablosuna çıkarılmalıdır).
- **Truth Mode Denetimi:** AI, kendi tasarladığı tabloda "Bir kolonun değeri diğerini değiştiriyor mu?" mantıksal testini kendi içinde çalıştırır.

### BCNF (Boyce-Codd Normal Form)
- **Kural:** 3NF sağlanmış olmalı. Her "Belirleyici" (Determinant) bir Aday Anahtar (Candidate Key) olmak zorundadır. Birden fazla kesişen aday anahtara sahip karmaşık join tablolarında BCNF ihlalleri aranır.

## Denormalizasyon Ne Zaman Kabul Edilir? (When is Denormalization Acceptable?)

Bir AI Orkestratörü katı bir akademisyen değildir; üretim (production) performansını düşünür. Denormalizasyona (Örn: Bir faturanın içine o anki ürün fiyatını sert-kopyalamak - hard copy) şu şartlarda izin verilir:
1. Verinin tarihsel (historical) olarak dondurulması gerekiyorsa.
2. Web araştırması (Mandatory Web Search), hedeflenen yoğun okunma (Read-Heavy) senaryosunda Join maliyetinin sistemi kilitleyeceğini gösteriyorsa.
3. Bu karar kodun içine yorum satırı (ADR - Architecture Decision Record) olarak eklenmişse.

Örnek Yorum:
`-- ADR: 'total_price' kolonu denormalize edildi (Normalde items tablosundan toplanabilir). Gerekçe: Raporlama ekranında çok ağır Join işlemlerini engellemek (Truth Mode Onaylı).`
