---
title: "CoreMusic — Skill Oluşturucu Meta-Skill"
type: skill-instruction
version: 3.1
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - New Skill Creation
  - Agentic Workflow Generation
  - Truth Mode Enforcement
  - Anti-Hallucination Integration
  - OWASP Compliance
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
  templates:
    - ".ai/.templates/index.md"
  agents:
    - ".ai/.agents/AGENTS.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "skill format change"
      - "agentic pattern change"
triggers:
  - "skill oluştur"
  - "yeni skill"
  - "skill maker"
  - "beceri oluştur"
  - "beceri yap"
  - "agentic skill üret"
  - "orchestration skill"
changelog:
  - version: 3.1
    date: 2026-08-15
    changes:
      - Standardized YAML frontmatter
      - Added triggers to frontmatter
---

# 🌐 skill-maker — AI Agentic Orchestration Meta-Skill

## 1. Genel Bakış
Bu meta-skill, CoreMusic ekosistemi için yeni skill'lerin, agentic workflow'ların ve otonom araçların **tam doğruluk (Truth Mode)** ve **sıfır halüsinasyon (Zero Hallucination)** ilkeleriyle üretilmesini sağlayan bir orkestrasyon sistemidir. 

**Temel Hedef:** Geliştiricinin ihtiyacı olan her yeni AI skill'ini; web doğrulaması, güvenlik denetimi ve Kiro agentic standartlarına uygun olarak en baştan (max 2000 satır kuralıyla) inşa etmektir.

---

## 2. Aktivasyon Koşulları
Bu skill aşağıdaki anahtar kelimelerden biri kullanıldığında devreye girer:
- `skill oluştur`, `yeni skill`, `skill maker`, `beceri oluştur`, `beceri yap`
- `agentic skill üret`, `orchestration skill`

---

## 3. Mandatory Agentic Protocols (Zorunlu Protokoller)

Yeni bir skill oluşturulurken AI, aşağıdaki protokolleri **ŞART KOŞARAK** uygular:

### 3.1. Web Arama & Doğrulama Zorunluluğu (Mandatory Web Research)
Yeni skill'in kullanacağı kütüphaneler, API'ler, terminal komutları veya domain spesifik standartlar **kesinlikle uydurulamaz**.
1. **Araştır:** AI, yetenek için gereken araçları (örn. Playwright komutları, PHPUnit flag'leri, x86 architecture) web'den aramak zorundadır.
2. **Çapraz Doğrula:** En az 2-3 resmi kaynaktan (MDN, OWASP, vendor docs) teyit al.
3. **Reddet (H001):** Geçerliliğini yitirmiş (deprecated) veya desteklenmeyen teknolojiler (örn. PCM5122 for 8.1 surround) reddedilir.

### 3.2. Otomatik Halüsinasyon Kontrolü (Zero Hallucination Policy)
Skill'in içeriğinde yer alan her bir prompt, komut ve yapılandırma:
- **Verified (Score 90-100):** Sadece kesin ve doğrulanmış yapılar skill içerisine aktarılabilir.
- **Unverified (Score <90):** `// ⚠️ VERIFICATION REQUIRED` etiketi eklenerek doğrulanması istenir. Tahmin yürütmek YASAKTIR.

### 3.3. Max 2000 Satır Kısıtı
Üretilen her yeni SKILL.md dosyası, kısa, öz, doğrudan ve spesifik olmalıdır. Maksimum limit 2000 satırdır. Gerekli durumlarda karmaşıklık `references/` veya `scripts/` klasörlerine dağıtılır.

---

## 4. Skill Üretim İş Akışı (Orchestration Pipeline)

Yeni bir skill oluşturulması istendiğinde, AI otonom olarak şu adımları izler:

### ADIM 1: Gereksinim Analizi ve Doğrulama (Soru-Cevap)
AI, kullanıcıdan skill'in amacını almak için kısa ve net sorular sorar:
1. **Skill'in Adı:** (Kural: lowercase-hyphen)
2. **Ana Görevi ve Domain'i:** (PHP, Frontend, Audio DSP, DevOps vb.)
3. **Kullanacağı Araçlar/API'ler:** (Hangi spesifik teknolojiler kullanılacak?)

### ADIM 2: Web Research & Truth Mode Aktivasyonu
Kullanıcı cevap verdikten sonra, AI hemen ilgili domain için web'de araştırma yapar.
- *Örnek:* Kullanıcı "React testing skill'i" istiyorsa, AI güncel Vitest/Playwright dokümanlarını okur. Varsayım yapmaz.

### ADIM 3: Klasör ve Dosya Yapısı Üretimi
Onaylandıktan sonra, AI aşağıdaki yapıyı tam olarak oluşturur (veya mevcut yapıyı update eder):

```text
.claude/skills/{skill-adi}/
├── SKILL.md                    ← Zorunlu: Ana orkestrasyon dosyası, frontmatter ile.
├── references/
│   ├── overview.md             ← Zorunlu: Detaylı mimari ve kısıtlar.
│   └── rules.md                ← Kural ve güvenlik sınırları.
├── scripts/
│   └── (Varsa CLI veya otomasyon araçları)
└── templates/
    └── (Varsa kod üretim şablonları)
```

### ADIM 4: Kodlama & CoreMusic Kurallarını Enjekte Etme
Üretilen `SKILL.md` içerisine proje kuralları gömülür:
- **SOLID & Clean Code:** Bütün skill çıktıları bu ilkelere uymak zorundadır.
- **Security:** OWASP Top 10:2025 zorunlu tutulur.
- **Testing:** Üretilecek skill, test edilmemiş hiçbir işlemi (browser testi, E2E vb.) kabul etmez.

### ADIM 5: Otonom Geri Bildirim ve Kalite Geçişi
AI, yeni skill'i yazdıktan sonra şu check-list'i çalıştırır ve kullanıcıya raporlar:
- [ ] Web'den minimum N kaynak ile bilgi doğrulandı mı?
- [ ] Halüsinasyon / tahmini bilgi var mı? (Yok: ✅)
- [ ] Otonom agentic orchestration (açık, net komutlar) sağlandı mı?
- [ ] 2000 satır kısıtına uyuldu mu?
- [ ] CoreMusic güvenlik / domain standartları eklendi mi?

---

## 5. Security & Isolation Kuralları
- Yeni üretilen skill'ler, sistem dışına çıkacak (network call yapacak) eylemlerde açıkça kullanıcı onayı istemelidir.
- API Key, Secret ve şifre gibi credential'lar ASLA skill içinde sabit koda (hardcoded) yazılmaz. Çevre değişkenleri (`.env`) veya secure vault gösterilir.

---

## 6. Örnek Kullanım
**Kullanıcı:** `Yeni bir veritabanı analiz skill'i oluştur, adı sql-optimizer olsun.`
**AI (skill-maker):**
1. Hedefleri belirler.
2. Webden güncel MySQL 8 BCNF optimizasyon metriklerini arar (Truth Mode).
3. `sql-optimizer` klasörünü ve `SKILL.md`'yi oluşturur.
4. "Bu skill, tüm sorgularda SELECT * kullanımını reddeder ve EXPLAIN ile analiz yapar" kuralını ekler.
5. Kullanıcıya "Skill kullanıma hazır, otonom orchestration ayarlandı." raporu verir.
