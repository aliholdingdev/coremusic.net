# 01. Requirements Gathering & Mandatory Web Search

## Zorunlu Araştırma ve Gereksinim Toplama

Bir Agentic Orchestrator'ın en büyük hatası, eksik veya belirsiz kullanıcı taleplerine "tahmin (hallucination) yürüterek" cevap vermesidir. Bu nedenle `database-normalize-maker`, şema oluşturmadan önce katı bir gereksinim toplama ve zorunlu web araştırması (Mandatory Web Search) sürecinden geçmek ZORUNDADIR.

## Adım 1: Etkileşimli Protokol (Interactive Protocol)

Eğer kullanıcı talebi çok kısa veya belirsiz ise (Örn: "Bana bir e-ticaret veritabanı yap"), Orkestratör derhal kodu yazmayı durdurur ve aşağıdaki 5 soruyu sorar:

1. **Domain (İş Alanı):** Uygulamanın temel iş mantığı nedir? Alt sistemler nelerdir? (Envanter, Ödeme, Kargo)
2. **Ölçek (Scale):** Beklenen veri büyüklüğü nedir? (<1M, 1M-100M, >100M satır)
3. **Motor (Engine):** MySQL 8+ mı, PostgreSQL 15+ mi, yoksa başka bir hedef mi?
4. **Güvenlik (Security):** Sistemde AES-256-GCM düzeyinde şifreleme gerektiren PII (Kişisel Tanımlanabilir Bilgiler) verileri var mı?
5. **Mevcut Durum (Existing):** Eski bir veritabanını mı modernize ediyoruz, yoksa sıfırdan (greenfield) mi başlıyoruz?

## Adım 2: Zorunlu Web Araştırması (Mandatory Web Search) (GATE 1)

Kullanıcıdan gereksinimler alındıktan sonra, Orkestratör **KENDİ BAŞINA (AI otonomisiyle)** web araştırması yapmakla yükümlüdür.

**Neden Zorunlu?**
Çünkü endüstri standartları, anti-pattern'ler ve veritabanı motoru özellikleri sürekli değişir. Eski bilgi ile yeni nesil projeler yapılamaz.

**Arama Kriterleri:**
- `{Kullanıcı_Sektörü} database schema best practices 2026`
- `{Seçilen_DB_Motoru} performance tuning and indexing best practices`
- `{Sektör_İsmi} known database anti-patterns`

**Örnek Uygulama:**
- Kullanıcı: "Bir Randevu sistemi veritabanı lazım."
- AI (İç Ses): *Kullanıcı Randevu (Booking) dedi. Web'de "Booking system database schema overlapping time prevention" diye aratmalıyım, aksi takdirde çakışan randevulara sebep olan zayıf bir tablo üretirim.*

## Adım 3: Kanıt Tabanlı Çıktı (Evidence-Based Output)

Talepler alındıktan ve araştırmalar yapıldıktan sonra oluşturulacak şemadaki kritik kararlar, araştırmadan gelen **kanıtlarla** desteklenmelidir. 

Örnek:
`// ADR: Rezervasyon saatleri için TIMESTAMP kullanıldı çünkü arama sonuçları saat dilimi (timezone) çakışmalarını önlemenin en iyi yolunun bu olduğunu gösteriyor (Kaynak: PostgreSQL Docs).`
