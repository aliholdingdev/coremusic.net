# 04. Security Audit & Security Engineer Role

## Security Engineer Ajanının Devreye Girmesi

`database-normalize-maker` Orkestratörü, Aşama 2 (Delegasyon) adımında **Security Engineer** sanal ajanını uyandırır. Bu ajan, üretilen şemayı OWASP Top 10 ve CoreMusic güvenlik standartlarına göre katı bir şekilde denetler.

## Güvenlik Denetim Kuralları (Hard Gates)

### 1. PII (Kişisel Veri) Şifrelemesi (Encryption at Rest)
Kullanıcının TC Kimlik No, Kredi Kartı (Token), Sağlık Verisi gibi hassas verileri **kesinlikle** düz metin (plain-text) olarak saklanamaz.
- **Orkestratör Kuralı:** Eğer tabloda `ssn`, `national_id`, `credit_card`, `medical_record` gibi kolonlar varsa, bu kolonların veritabanı seviyesinde (Örn: MySQL `AES_ENCRYPT`) veya uygulama seviyesinde şifreleneceği (AES-256-GCM) açıkça belirtilmeli ve kolon boyutları şifrelenmiş veriyi (Base64 vb.) alacak kadar büyük olmalıdır. (Örn: `VARCHAR(255)` yerine `TEXT`).

### 2. Denetim İzi (Audit Logging)
Finans, e-ticaret, sağlık gibi kritik domainlerde "Kim, neyi, ne zaman sildi?" sorusu hayati önem taşır.
- **Orkestratör Kuralı:** `users`, `payments`, `orders` gibi tablolar için OTOMATİK olarak bir `_audit` tablosu üretilmelidir.
- **Örnek Yapı:**
  ```sql
  CREATE TABLE payments_audit (
      audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      payment_id BIGINT UNSIGNED NOT NULL,
      action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
      old_values JSON,
      new_values JSON,
      acted_by BIGINT UNSIGNED,
      action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  );
  ```

### 3. Satır Düzeyi Güvenlik (Row-Level Security - RLS)
Eğer hedef motor PostgreSQL ise, Security Engineer ajanı çok kiracılı (multi-tenant) sistemler için RLS politikalarının (Policies) SQL çıktısına eklenip eklenmediğini kontrol eder. MySQL için benzer yapı View'ler veya uygulama katmanı kısıtlamaları (tenant_id) olarak tasarlanmalıdır.

### 4. SQL Injection Koruması
Orkestratör, çıktılarında KESİNLİKLE "dinamik SQL birleştirme" gerektirecek zayıf tablolar tasarlamaz. Sütun isimleri, tablo isimleri güvenli, standart (snake_case) yapıda olmalı; PHP/PDO tarafında parametrik sorgularla uyumlu olacak şekilde net tiplere (Integer, String, JSON) sahip olmalıdır. Uydurma veya tanımsız bir tip kullanılması Truth Mode (Halüsinasyon kontrolü) tarafından anında reddedilir.
