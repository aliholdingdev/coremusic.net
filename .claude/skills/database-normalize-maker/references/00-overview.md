# 00. Overview: AI Agentic Orchestrator

## Genel Bakış

`database-normalize-maker` artık basit bir "SQL Üretici (Generator)" değil, yetkilendirilmiş bir **AI Agentic Orchestrator** (Yapay Zeka Ajan Orkestratörü) olarak çalışmaktadır. Bu sistem, girilen veritabanı gereksinimlerini doğrudan SQL'e dönüştürmek yerine, CoreMusic ekosistemindeki çeşitli sanal uzman ajanları (Data Engineer, Security Engineer, Backend Architect) devreye sokarak, doğrulanmış (Zero-Hallucination) ve üretime hazır (Production-grade) veritabanı mimarileri üretir.

## Neden Orkestratör? (Why Orchestrator?)

Geleneksel LLM'ler veritabanı tasarımı yaparken genellikle varsayımlarda bulunur, uydurma veri tipleri (hallucination) kullanır veya güvenlik açıklarını (SQLi, şifrelenmemiş PII) gözden kaçırır. Bu Orkestratör modeli:
1. **Zorunlu Web Araması (Mandatory Web Search)** ile en güncel endüstri standartlarını araştırır.
2. **Zero-Hallucination Kilitleri (Hard Gates)** ile her kararı resmi dökümantasyonlarla doğrular.
3. **Rol Dağılımı (Delegation)** ile güvenliği Security Engineer'a, performansı Data Engineer'a emanet eder.

## 5-Aşamalı İş Akışı (The 5-Phase Workflow)

1. **Aşama 1: Keşif & Zorunlu Web Araştırması (Discovery)**
   - Proje bağlamı (CLAUDE.md, .ai/brain.md vb.) okunur.
   - İstenilen sistem (Örn: E-ticaret sepet yönetimi) için "2026 Database Schema Best Practices" araştırması ZORUNLU olarak yapılır.

2. **Aşama 2: Ajanlara Görev Dağıtımı (Delegation)**
   - Orkestratör, kendi içindeki alt kimlikleri (sanal ajanları) uyandırır.
   - Tasarımın BCNF kurallarına uyumu, güvenlik (OWASP) ve kod tarafındaki (PHP/PDO) yansımaları bu aşamada tasarlanır.

3. **Aşama 3: Halüsinasyon Kontrolü (Truth Mode)**
   - Her yabancı anahtar (Foreign Key), her veri tipi (Data Type) ve her kısıt (Constraint) doğrulanır.
   - Eğer sistem bir özellikten emin değilse (Güven skoru < %95), tahmin etmek yerine `// ⚠️ VERIFICATION REQUIRED` diyerek işlemi durdurur ve tekrar araştırır.

4. **Aşama 4: Çıktı Üretimi (Output Generation)**
   - Tüm doğrulama testlerinden geçen şema, SQL dosyası (`schema.sql`), Test Verisi (`seed_data.sql`) ve Veri Sözlüğü (`dictionary.md`) olarak `.ai/.sql/` dizinine yazılır.

5. **Aşama 5: QA & Doğrulama Denetimi (Validation Audit)**
   - BCNF uyumluluğu, OWASP standartları ve performans kriterleri son bir kontrol listesi (Checklist) ile kullanıcıya sunulur.

## Beklenen Davranış
Sistemi çağırdığınızda size hemen bir kod bloğu VERMEZ. Önce bir "Onaylıyorum, Orkestratör Devrede" mesajı verir, araştırma yapar, ajanları koordine eder ve en son "Truth Mode"dan geçmiş nihai SQL'i üretir.
