# 07. CoreMusic Integration & Backend Architect Role

## Backend Architect Ajanı ile Uyum

Veritabanı kendi başına izole bir sistem değildir. `database-normalize-maker` Orkestratörü, **Backend Architect** sanal ajanını kullanarak, ürettiği şemanın CoreMusic PHP standartlarıyla kusursuz şekilde konuşmasını güvence altına alır.

## Katmanlı Mimari (Layered Architecture) Kilitleri

### 1. Veritabanı Mantığı Sızıntısı (Logic Leakage)
Veritabanı katmanı (Infrastructure Layer), İş Mantığı (Domain Layer) katmanına sızmamalıdır.
- **Orkestratör Kuralı:** Veritabanında aşırı karmaşık Stored Procedure'ler veya Trigger'lar kullanmak yerine, iş mantığının PHP Domain Services içinde çözülmesi teşvik edilir. Sadece veri bütünlüğü kısıtlamaları (Foreign Key, Check Constraints) veritabanında bırakılır.

### 2. PHP PDO ve Tür Güvenliği (Type Safety) Uyumluluğu
Veritabanı şeması, PHP 8.x katı tipleri (`declare(strict_types=1)`) ile birebir uyumlu olacak netlikte tasarlanmalıdır.
- **Tamsayılar (Integers):** PHP'de `int` olarak karşılanabilmesi için `BIGINT` veya `INT` doğru boyutlandırılmalıdır.
- **Ondalıklar (Decimals):** Para (Currency) hesaplamaları KESİNLİKLE `FLOAT` veya `DOUBLE` olamaz (Halüsinasyon kontrolü bunu engeller). Her zaman `DECIMAL(10,2)` vb. gibi kesin (precise) tipler kullanılmalıdır.
- **Bool:** `TINYINT(1)` veya `BOOLEAN` (PostgreSQL).

### 3. CoreMusic `.ai/` Vault Entegrasyonu
Orkestratör, proje dizinindeki `.ai/brain.md` ve `.ai/decisions/` dosyalarını (ADR - Architecture Decision Records) okur.
- Eğer daha önce "Bu projede Soft Delete kullanılmayacaktır" şeklinde bir ADR alınmışsa, sistem hiçbir tabloya `deleted_at` ekleyemez.
- Alınan yeni veritabanı tasarımı kararları (Örn: Neden JSON sütunu kullanıldı?) otomatik olarak `.ai/decisions/` içine yeni bir markdown dosyası olarak raporlanmalıdır.

### 4. Dış Anahtar İsimlendirme Standartları (Naming Conventions)
Backend Architect ajanı, ORM'lerin (Örn: Eloquent, Doctrine) ilişkileri otomatik eşleştirebilmesi için standart isimlendirme kuralını dayatır:
- **Tablolar:** Çoğul, snake_case (`users`, `order_items`).
- **Dış Anahtarlar (Foreign Keys):** Tekil_tablo_adi + `_id` (`user_id`, `product_id`).
- **Pivot Tablolar:** Alfabetik sırada birleşik isim (`role_user` veya `product_tag`).

Eğer sistem bu kurallardan saparsa (Örn: `id_user` gibi), Orkestratör işlemi durdurur (Zero-Hallucination Gate) ve isimlendirmeyi düzeltir.
