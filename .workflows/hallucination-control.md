---
type: workflow
title: "Hallucination Control & Truth Mode Workflow (Zero Hallucination Policy)"
date: 2026-07-31
updated: 2026-07-31
status: active
authority: Security Engineer / Red Team Agent
references: [[index]], [[brain]], [[AGENTS]], [[CLAUDE]]
---

# Hallucination Control Workflow (Zero Hallucination Policy)

Bu doküman, CoreMusic ekosistemindeki tüm yapay zeka ajanlarının (Claude, Gemini, Cursor vb.) kod üretirken, doküman yazarken veya sistem mimarisine karar verirken **Zero Hallucination Policy (Sıfır Hallüsinasyon Politikası)** kurallarına uyması için tasarlanmış ana iş akışıdır. 

Yapay zeka modellerinin emin olmadıkları API'leri, kütüphane fonksiyonlarını veya sistem durumlarını "uydurmasını" (hallucinate) kesin olarak engeller. Tüm veri çıktıları **Red Team • Truth Mode** sürecinden geçirilmek zorundadır.

---

## 1. Temel Prensipler (Core Principles)

1. **Varsayım Yasaktır:** Bilinmeyen veya eksik bilgi uydurulamaz. Bulunamıyorsa açıkça `VERIFICATION REQUIRED` (Doğrulama Gerekiyor) etiketi ile işaretlenir.
2. **Kapsamlı Doğrulama (Bounded Verification):** Her teknik bilgi (özellikle PHP, C++ ASIO, MySQL, ITCSS sınırları) kendi spesifikasyonu veya resmi dökümantasyonu ile doğrulanmalıdır.
3. **SSOT (Single Source of Truth):** Proje içi kurallar için yegane doğruluk kaynağı `.ai/` dizinidir (`index.md`, `brain.md`, `CLAUDE.md`).
4. **Çift Kontrol (Cross-Reference):** Kritik kararlar (ör. güvenlik algoritmaları, DSP buffer boyutları) minimum 2 bağımsız kaynaktan çapraz doğrulanmalıdır.

---

## 2. Hallucination Control Süreci (5 Aşamalı Akış)

Her teknik çıktı, kod parçası veya dokümantasyon bilgisi aşağıdaki 5 aşamalı puanlama ve filtreleme sürecinden geçmelidir.

### 2.1 Adım 1: Source Identification (Kaynak Tespiti)
Bilginin kaynağı nedir ve ne kadar güvenilirdir?

```text
Bilgi Çıktısı Geldi
  │
  ▼
Kaynak tespit edilebiliyor mu?
  ├── Evet -> Kaynak türü nedir?
  │     ├── Resmi Üretici Datasheet / RFC / W3C (Örn: OWASP, PHP.net) -> +30 Puan
  │     ├── CoreMusic Codebase / .ai Vault (Kendi kodumuz) -> +30 Puan
  │     ├── Güvenilir 1. Parti Kaynak (Örn: MDN Web Docs) -> +15 Puan
  │     ├── Topluluk / StackOverflow / Forum -> +5 Puan
  │     └── Yalnızca Modelin İç Hafızası (Eğitim Verisi) -> 0 Puan
  │
  └── Hayır -> Skor = 0, ANINDA RED (REJECTED)
```

### 2.2 Adım 2: Cross-Reference (Çapraz Doğrulama)
Bilgi birden fazla kaynak tarafından doğrulanıyor mu?

```text
İkinci bir bağımsız kaynak var mı?
  ├── Evet (2 ve üzeri ek kaynak) -> +20 Puan
  ├── Evet (1 ek kaynak) -> +10 Puan
  └── Hayır (Tek kaynak riski) -> -10 Puan
```

### 2.3 Adım 3: Conflict Check (Çelişki Kontrolü)
Bilgi, bilinen doğrularla veya sistemin mevcut kurallarıyla çelişiyor mu?

```text
Çelişki tespit edildi mi?
  ├── Fizik/Matematik/Standart Kanunu ile çelişki (Örn: 256-bit AES'i MD5 ile kırma) -> -50 Puan (REJECTED)
  ├── Üretici Datasheet ile çelişki -> -30 Puan (REJECTED)
  ├── Mevcut Codebase / CoreMusic Mimarisi ile çelişki (Örn: ORM kullanma önerisi) -> -20 Puan (REVIEW)
  └── Hiçbir çelişki yok -> +0 Puan
```

### 2.4 Adım 4: Güvenlik ve Mimari Etki (Impact Analysis)
Eğer bilgi hatalıysa, sisteme etkisi nedir?

```text
Risk seviyesi nedir?
  ├── Güvenlik / Şifreleme / Auth / Veritabanı -> Katı Doğrulama (+20 puan eşik artışı)
  ├── DSP / Donanım / L0 Altyapı -> Kritik Doğrulama (+15 puan eşik artışı)
  ├── UI / CSS / Frontend -> Standart Doğrulama
  └── Yorum satırı / README dokümanı -> Düşük Risk
```

### 2.5 Adım 5: Final Score Hesaplama ve Kategorizasyon
```text
Total = Base Puan (50) + Bonuslar - Cezalar

Kategorizasyon:
  90 ve üzeri -> VERIFIED (Doğrulanmış) -> Kodlanabilir veya .ai/knowledge/verified/ dizinine eklenebilir.
  60 - 89 -> UNVERIFIED (Doğrulanmamış) -> Koda eklenemez! Sadece .ai/knowledge/unverified/ dizinine not düşülür ve kullanıcı onayı (VERIFICATION REQUIRED) istenir.
  60 altı -> REJECTED (Reddedilmiş) -> Halüsinasyon kabul edilir. İşlem durdurulur ve alternatif üretilir. (.ai/knowledge/rejected/)
```

---

## 3. Otomatik Tetiklenme Koşulları (Triggers)

Bu iş akışı, ajanların çalışması sırasında aşağıdaki durumlarda **otomatik olarak** devreye girer:
1. **Yeni Dosya / Kod Üretimi (Write):** Herhangi bir `write_to_file` aracı çağrılmadan hemen önce.
2. **Mimari Karar (ADR):** Yeni bir `decisions/` belgesi tasarlanırken.
3. **Bilinmeyen Bir API İstenmesi:** Kullanıcı, ajanın bilmediği veya model eğitiminde olmayan bir kütüphane kullanımını talep ettiğinde.
4. **Agent-to-Agent İletişim:** Örneğin Backend Architect'in UI Designer'a ilettiği veriler Red Team Agent tarafından kontrol edilir.

---

## 4. İhlal Durumunda Eylem (VERIFICATION REQUIRED)

Ajan, bir bilginin doğruluğundan %100 emin olamazsa (Skor < 90), hiçbir şekilde kodu yazıp "çalışıyor" gibi sunamaz. Bunun yerine kod içinde veya planda şu formatı uygulamak ZORUNDADIR:

```markdown
// ⚠️ VERIFICATION REQUIRED: [Eksik/Emin Olunmayan Bilginin Tanımı]
// Beklenen Davranış: [Ne olması bekleniyor]
// Doğrulama Yöntemi: [İnsan mühendis bunu nasıl test etmeli]
```

**Örnek (Kabul Edilemez - Hallüsinasyon):**
```php
// Uydurma bir kütüphane fonksiyonu çağırma
$result = CoreMusic_AudioAnalyzer::detectBPM($file); 
```

**Örnek (Kabul Edilebilir - Doğrulama İstenen):**
```php
// ⚠️ VERIFICATION REQUIRED: CoreMusic_AudioAnalyzer sınıfında detectBPM metodunun varlığı doğrulanamadı.
// Eğer böyle bir metod yoksa, FFmpeg exec çağrısı veya farklı bir C++ bağlamı kullanılmalıdır.
// Şimdilik buraya placeholder bırakılmıştır.
$result = null; 
```

---

## 5. "Truth Mode" ve "Red Team" Entegrasyonu

CoreMusic `.ai` kurallarına göre, ajan her çıktısında içsel olarak iki mod çalıştırır:

- **Truth Mode:** Yazılan bilginin objektif gerçekliğini (Resmi Docs, standartlar) denetler. (Adım 1 ve 2).
- **Red Team:** Yazılan bilginin CoreMusic'e özel kısıtlara (Örn: "PDO Mandatory", "No ORM", "L0->L2 erişim yasak") uyup uymadığını düşmanca bir bakış açısıyla (adversarial) sorgular. (Adım 3).

---

## 6. Referanslar ve İlgili Dizinler

*   `knowledge/verified/README.md` - Doğrulanmış sistem gerçekleri.
*   `knowledge/unverified/README.md` - Araştırılması veya kullanıcı onayı bekleyen teknolojiler/yaklaşımlar.
*   `knowledge/rejected/README.md` - Geçmişte denenip reddedilmiş veya halüsinasyon olarak etiketlenmiş anti-pattern'ler.

*CoreMusic Hallucination Control Workflow — AEOS v2.0*

## Güvenlik Denetimi

- **Tarih:** 2026-08-04
- **Kapsam:** Hallucination control workflow (AEOS, 3 katmanlı doğrulama)
- **Bulunan Sorunlar:** Yok (H001-H039 reddedilen pattern'ler doğru tanımlanmış, doğrulama kaynakları sıralanmış)
- **Doğrulanan kapsam:**
  - Web araması yasak (system integration ile doğrulama) — H001-H039 doğru
  - Score sistemi (90+ verified, 60-89 unverified, <60 rejected) kanonik
  - [[.ai/decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] ile uyumlu
- **Cross-references eklendi:** [[.ai/CLAUDE]], [[ADR-042]], [[.claude/rules/hallucination-control]]
