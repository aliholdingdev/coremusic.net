# 03. Provider Dialects & Official Verification

## Motor Diyalektleri ve Resmi Doğrulama

CoreMusic AI Agentic Orchestrator, varsayılan olarak **MySQL 8+** ve **PostgreSQL 15+** mimarilerine odaklanır. Diğer sistemler (MSSQL, Oracle, MongoDB) istendiğinde, "Zero-Hallucination" (Sıfır Halüsinasyon) kuralları gereği, sistem o motorun resmi dökümantasyonlarına başvurmak zorundadır.

## Zero-Hallucination (Sıfır Halüsinasyon) Kuralı

LLM'lerin veritabanı tasarlarken yaptığı en yaygın hata "sözdizimi uydurmasıdır" (Syntax Hallucination). Örneğin, PostgreSQL'e özel olan `UUID` tipini MySQL'de doğrudan sütun tipi olarak tanımlamak gibi hatalar YASAKTIR.

**Orkestratörün Doğrulama Akışı (Truth Mode):**
1. Hedef motor nedir? (Örn: MySQL 8)
2. İstenen veri tipi nedir? (Örn: JSON)
3. "Bu veri tipi MySQL 8'de yerel olarak destekleniyor mu?" (Cevap: Evet, ama indexleme mantığı farklıdır. Virtual Column üzerinden indexlenir).
4. Çıktıya yansıt.

## MySQL 8+ Özel Kuralları
- **Primary Key:** `BIGINT UNSIGNED AUTO_INCREMENT` kullanılmalıdır.
- **Tarih Saat:** `DATETIME` yerine `TIMESTAMP` (timezone farkındalığı için) tercih edilmelidir.
- **Karakter Seti:** Varsayılan olarak `utf8mb4` ve `utf8mb4_unicode_ci` kullanılmalıdır (utf8 değil!).
- **JSON:** JSON sütunlarına doğrudan indeks atılamaz, "Generated/Virtual" kolonlar oluşturulup onların üzerine indeks atılmalıdır.

## PostgreSQL 15+ Özel Kuralları
- **Primary Key:** `BIGINT GENERATED ALWAYS AS IDENTITY` veya UUID (`uuid_generate_v4()`) kullanılmalıdır (`SERIAL` tipi eski (legacy) kabul edilir).
- **Tarih Saat:** `TIMESTAMPTZ` (Time stamp with time zone) zorunludur. Sadece `TIMESTAMP` kullanmak anti-pattern'dir.
- **Metin:** `VARCHAR(255)` yerine genelde doğrudan `TEXT` veya sınırsız `VARCHAR` kullanılması (eğer özel bir kısıtlama yoksa) PostgreSQL'de performans açısından daha önerilir.

## YASAKLI EYLEMLER (Strictly Prohibited)
- Hangi veritabanı olursa olsun, o veritabanında var olmayan bir fonksiyon (Örn: MySQL'de olmayan hayali bir array fonksiyonu) çağrılamaz.
- Tüm `CREATE TABLE` scriptleri, hedef motorun güncel (2026+) standartlarına %100 uyumlu (syntax error vermeyen) yapıda olmalıdır. Sistem şüpheye düşerse, araştırma yapana kadar (Web Search) kodu üretmeyi reddeder.
