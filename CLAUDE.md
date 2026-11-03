---
title: "CoreMusic — Claude Code Boot Instruction (Pointer)"
type: pointer
category: boot
version: 3.5.0
status: active
authority: "SSOT: .ai/CLAUDE.md (tam anayasa) · bu dosya §Skill Registry (Skill Registry SSOT)"
updated: 2026-11-02
---

# CoreMusic — CLAUDE.md (Claude Code Boot Instruction Pointer)

> **Kanonik eşik:** Claude Code bu dosyayı kök boot dosyası olarak okur.  
> **OpenCode V2:** [`AGENTS.md`](AGENTS.md) dosyasını kök boot dosyası olarak okur (CLAUDE.md V2 tarafından yok sayılır).  
> **Çift Yönlü Boot Paketi:** Her session/görev başında [`CLAUDE.md`](CLAUDE.md) ve [`AGENTS.md`](AGENTS.md) birlikte okunur, bu iki dosya ayrılmaz bir çifttir.  
> **SSOT (Tek Doğruluk Kaynağı):** Tüm detaylı anayasa, kurallar ve mimari kararlar `.ai/` kasasındadır. Kök `.md` dosyaları **boot protokolü ve yönlendirici (pointer)** olarak görev yapar.  
> **Skill Registry SSOT:** Bu dosyanın §Skill Registry bölümüdür — başka dosyada liste kopyalanmaz.

⚠️ **VERIFICATION REQUIRED**: `.ai/CLAUDE.md` v27.6.0 diskte MEVCUTTUR — kod yazmadan önce tam anayasa okunur (Guardrail #2).

---

## 1. Master Engineering System (15 Aşama)

```text
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document
```

- **Kural:** Analiz tamamlanmadan uygulama (implementation) başlatılmaz.
- **Kod Öncesi 16 Adım:** Dosya yapısı, mimari katmanlar, bağımlılıklar, pattern'ler, build/test/config/logging/security/veritabanı/API/frontend yüzeyleri, duplicate analizleri ve teknik borç incelenir (detay: [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) §3).
- **YASAKLAR:** Keyfi dosya/klasör açma, mevcut dosyayı incelemeden yeniden yazma, mimariyi bozma, duplicate abstraction, doğrulanmamış API, olmayan sınıf/paket/fonksiyon varsayma, test/build çalıştırmadan "bitti" deme.
- **Zero-Hallucination:** Repository, URL, API, sınıf, metod, versiyon, config, skill, agent asla uydurulmaz. Bilinmeyen = `UNKNOWN`, doğrulanamayan = `⚠️ VERIFICATION REQUIRED`.

---

## 2. Behavioral Guidelines (Claude Code & Karpathy Standartları)

> **Resmi Skill:** [`.claude/skills/karpathy-guidelines/SKILL.md`](.claude/skills/karpathy-guidelines/SKILL.md) · **Cursor Rule:** [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) · [`CURSOR.md`](CURSOR.md) · **Kod Örnekleri:** [`.claude/skills/karpathy-guidelines/references/EXAMPLES.md`](.claude/skills/karpathy-guidelines/references/EXAMPLES.md)  
> Andrej Karpathy'nin LLM kodlama zaafları üzerine gözlemlerinden türetilmiş 4 temel davranış ilkesi CoreMusic'te bağlayıcıdır:

### 2.1 Think Before Coding
- **Varsayım yapma, kafa karışıklığını gizleme, ödünleşimleri açıkla.**
- Başlamadan önce varsayımlarını açıkça belirt. Şüphe varsa sor.
- Birden fazla yorumlama varsa sessizce seçme, seçenekleri sun.
- Daha basit bir çözüm varsa belirt. Gerekirse itiraz et (push back).

### 2.2 Simplicity First
- **Sorunu çözen minimum kod. Spekülatif hiçbir şey yok.**
- İstenenden fazlasını ekleme. Tek kullanımlık kod için soyutlama (abstraction) üretme.
- İstenmeyen esneklik veya konfigürasyon ekleme.
- 200 satır yazıp 50 satırda çözülebiliyorsa yeniden yaz ve sadeleştir.

### 2.3 Surgical Changes
- **Yalnızca dokunman gereken yere dokun. Kendi izini temizle.**
- İlgisiz çevre kodları, yorumları veya formatları "geliştirmeye" kalkma.
- Bozuk olmayan şeyi refactor etme. Mevcut koda ve stile birebir uy.
- İlgisiz ölü kod görürsen silme, raporla. Kendi değişikliklerinden kalan yetim import ve değişkenleri temizle.

### 2.4 Goal-Driven Execution
- **Başarı kriterini tanımla. Doğrulanana kadar döngüde kal.**
- Görevleri doğrulanabilir hedeflere dönüştür. Çok adımlı işlerde kısa plan belirt (`[Step] → verify: [check]`).

### 2.5 Core Project Mandates (Değiştirilemez Kurallar)
- **Tema Kuralı (YALNIZCA LIGHT MODE):** CoreMusic'te ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR. Dark mode henüz yapılmamıştır/yoktur! Tüm CSS/UI tasarımları istisnasız Light Mode'a göre tasarlanır; dark mode varsayımı veya dark mode kodu yazmak KESİNLİKLE YASAKTIR.
- **Kalıcı Hafıza Devri (.ai/durum.md):** AI her oturum/görev başında [`.ai/durum.md`](.ai/durum.md) dosyasını okur; oturum boyunca ve kapanışta [`.ai/durum.md`](.ai/durum.md), [`.ai/log.md`](.ai/log.md) ve [`.ai/MEMORY.md`](.ai/MEMORY.md) dosyalarını günceller.
- **Kullanıcı Kuralları:** [`.ai/.rules/kurallar.md`](.ai/.rules/kurallar.md) (Kendini Bir Kere Anlat) ve [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) kurallarına eksiksiz uyulur.
- **Donanım Amfi Topolojisi:** Amplifikatör mimarisi kesinlikle Class AB'dir (Class D yasaktır).
- **Subagent Commit Yasağı:** `git commit` subagent tarafından ASLA ATILMAZ — orkestratöre aittir.

---

## 3. Skill Kullanım Zorunluluğu (Skill Usage Mandate)

1. Her görev başında aşağıdaki Skill Registry taranır ve eşleşme kontrol edilir.
2. Görev bir skill ile eşleşiyorsa İLK işlem Skill tool ile o skill'i yüklemektir ([`AGENTS.md`](AGENTS.md) §7 adım 0).
3. Eşleşme varken skill'siz işlem başlatmak YASAKTIR. Birden fazla eşleşmede en dar (domain-specific) olan yüklenir.
4. Yetenek yoksa kopyala-yapıştır yapılmaz; `skill-maker` ile skill üretilir.

---

## 4. Skill Registry (SSOT)

> **Disk kanıtı (2026-11-02 sayımı):** `.claude/skills/` = **14 SKILL.md** (bu makinede doğrulanmış proje skill seti).  
> Global envanter `C:\.claude\skills` makinede mevcuttur (348 dizin / 502 SKILL.md), ancak proje SSOT'u bu 14 skill'dir.

| # | Skill | Kapsam | Yol | Kullanım Amacı (Use when) | Durum |
|---|-------|--------|-----|---------------------------|-------|
| 1 | `prompt-maker` | project | `.claude/skills/prompt-maker/SKILL.md` | Ham prompt'u yapısal prompt'a çevirir (P0–P3 soru ağacı) | upgraded |
| 2 | `skill-maker` | project | `.claude/skills/skill-maker/SKILL.md` | Skill üretir / günceller (format otoritesi) | upgraded |
| 3 | `ui-analyzer` | project | `.claude/skills/ui-analyzer/SKILL.md` | UI denetimi / tasarım incelemesi / mockup kontrolü | upgraded |
| 4 | `ui-code-generator` | project | `.claude/skills/ui-code-generator/SKILL.md` | Frontend (HTML/CSS/JS) kod üretimi (ITCSS/BEM/token) | upgraded |
| 5 | `agent-orchestrator` | project | `.claude/skills/agent-orchestrator/SKILL.md` | Çok-ajan görev dağıtımı, handover, koordinasyon | restored/merged |
| 6 | `database-normalize-maker` | project | `.claude/skills/database-normalize-maker/SKILL.md` | BCNF şema tasarımı, normalizasyon, migration | restored/merged |
| 7 | `composer-sync` | project | `.claude/skills/composer-sync/SKILL.md` | Composer / vendor senkronu, autoload onarımı | restored/merged |
| 8 | `vault-sync-post` | project | `.claude/skills/vault-sync-post/SKILL.md` | İşlem sonrası vault senkronu ve loglama | restored/merged |
| 9 | `php-backend-standards` | project | `.claude/skills/php-backend-standards/SKILL.md` | PHP 8.4 / strict_types / middleware / raw PDO | new |
| 10 | `audio-engine-cpp` | project | `.claude/skills/audio-engine-cpp/SKILL.md` | C++20 / JUCE 9 / ASIO / DSP / Neva Engine | new |
| 11 | `hardware-electronics` | project | `.claude/skills/hardware-electronics/SKILL.md` | Class AB amfi / PSU / termal / PCB / BOM | new |
| 12 | `security-hardening` | project | `.claude/skills/security-hardening/SKILL.md` | Auth / CSRF / CSP / şifreleme / OWASP 2025 | new |
| 13 | `learning-prompts` | project | `.claude/skills/learning-prompts/SKILL.md` | Sokratik / Feynman / aktif hatırlama teknikleri | new |
| 14 | `karpathy-guidelines` | project | `.claude/skills/karpathy-guidelines/SKILL.md` | LLM kodlama zaaflarını önleme, Andrej Karpathy 4 ilkesi | new |

---

## 5. 🔗 .ai/ Vault ve Proje Kural Bağlantıları

Kök dosyalar yalnızca yönlendiricidir; tüm iş mantığı ve mimari kararlar `.ai/` kasasındadır:

| İhtiyaç / Kapsam | Dosya Yolu | Açıklama |
|---|---|---|
| **Ana AI Anayasası & Guardrails** | [`.ai/CLAUDE.md`](.ai/CLAUDE.md) | 16 Hard Guardrail, LINT, güvenlik kuralları |
| **Agent Registry & Koordinasyon** | [`.ai/AGENTS.md`](.ai/AGENTS.md) | 11 agent, routing, swarm, yetki sınırları |
| **Kullanıcı Kuralları (Genel & Kimlik)** | [`.ai/.rules/kurallar.md`](.ai/.rules/kurallar.md) | Kullanıcı kimliği (Bayram Ali), hybrid dil, Light Mode ve amfi kuralları |
| **Bütün Proje Kuralları (Senior Mode)** | [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) | Kod öncesi 16 analiz adımı, sıfır halüsinasyon, kodlama kuralları |
| **Cursor IDE Kuralları & Karpathy** | [`CURSOR.md`](CURSOR.md) · [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) | Cursor kuralı ve Karpathy ilkeleri |
| **Figma API Erişim Tokeni** | [`.ai/.env.figma`](.ai/.env.figma) | `FIGMA_TOKEN` / `FIGMA_FILE_KEY` SSOT (.gitignore:69) |
| **İş Akışları & Fazlar** | [`.ai/WORKFLOW.md`](.ai/WORKFLOW.md) | 7 fazlı teslimat döngüsü, onay kapıları |
| **Dizin & Dosya Envanteri** | [`.ai/CONTEXT.md`](.ai/CONTEXT.md) | Depo envanteri, dosya haritası |
| **Retrieval & Konu İndeksi** | [`.ai/RAG.md`](.ai/RAG.md) | Hangi soru → hangi dosya rehberi |
| **Mimari Kararlar (ADR)** | [`.ai/brain.md`](.ai/brain.md) + [`.ai/.decisions/`](.ai/.decisions/) | ADR-001..ADR-089 kayıtları |
| **Platform Tanımı & Yetenekler** | [`.ai/PROJECTS.md`](.ai/PROJECTS.md) | 10 temel yetenek, 6 hedef kitle |
| **Vizyon & Mülkiyet Felsefesi** | [`.ai/VISION.md`](.ai/VISION.md) | "Aynı Müzik Her Yerde Seninle" manifestosu |
| **İcra & Orkestrasyon Matrisi** | [`.ai/engine.md`](.ai/engine.md) | Faz yürütme motoru |
| **Kolektif Hafıza & Notlar** | [`.ai/MEMORY.md`](.ai/MEMORY.md) | Oturumlar arası kalıcı hafıza |
| **Oturum Günlüğü & Denetim** | [`.ai/log.md`](.ai/log.md) | Değişiklik ve işlem denetim kayıtları |
| **Oturum Durum & Devir Hafızası** | [`.ai/durum.md`](.ai/durum.md) | Tek sayfalık oturum hafızası, son durum ve devir kalıbı |
| **24 Kök Vault Dosyası İndeksi** | [`.ai/index.md`](.ai/index.md) | `.ai/` kökündeki 24 ana belgenin kataloğu |
| **Kök Boot Eşi (OpenCode V2)** | [`AGENTS.md`](AGENTS.md) | OpenCode V2 kök master agent kuralları |

---

## 6. Oturum Sonu Kontrol & Temizlik Protokolü (Session-End Check & Clean)

Her oturumun sonunda veya değişiklik tamamlandığında:
1. **Sözdizimi ve Bozuk Satır Kontrolü:** Kök `.md` dosyalarının (özellikle `CLAUDE.md` ve `AGENTS.md`) başlık ve gövdesinde kazara yapıştırılmış, yarım kalmış veya bozulmuş karakter/metin bulunmadığı doğrulanır.
2. **Çift Yönlü Link Bütünlüğü:** Kök dosyalar ile `.ai/` vault SSOT dosyaları arasındaki linklerin kırılmadığı teyit edilir.
3. **Format Temizliği:** Dosyalar gereksiz kopyalamalardan (duplicate bloklar) arındırılır, Markdown geçerliliği ve frontmatter şeması korunur.
4. **Log Güncellemesi:** Yapılan kritik değişiklikler `.ai/log.md` dosyasına timestamp ile eklenir (`vault-sync-post`).

---

## 7. 🔍 Web Verification Status (2026-10-07)

| Teknoloji | Versiyon | Web Doğrulama | Kaynak |
|---|---|:---:|---|
| PHP | 8.4.22 | ✅ | [php.net](https://php.net) |
| C++ | C++20 (ISO/IEC 14882:2020) | ✅ | [cppreference](https://en.cppreference.com/w/cpp/20) |
| MySQL | 9.0.1 | ✅ | [Oracle docs](https://dev.mysql.com/doc/relnotes/mysql/9.7/en/news-9-0-0.html) |
| JUCE | 9.0.0 | ✅ | [forum.juce.com](https://forum.juce.com/t/juce-9-is-available-now/69175) |
| ASIO SDK | 2.3.4 | ✅ | [steinberg.net](https://www.steinberg.net/developers/asiosdk-open/) |
| LM5122 | Boost Converter | ✅ | [ti.com](https://www.ti.com/product/LM5122) |
| AK4458 | 32-bit 8ch DAC | ✅ | [akm.com](https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/) |
| PCM3168A | Codec (ADC+DAC) | ✅ | [ti.com](https://www.ti.com/product/PCM3168A) |
| MJL21194/93 | Power Audio Trans. | ✅ | [onsemi.com](https://www.onsemi.com) |
| XMOS XU316 | USB Audio SoC | ✅ | [xmos.com](https://www.xmos.com/processors/xu316) |

*CoreMusic Claude Code Boot Instruction Pointer v3.4.0 — SSOT: `.ai/` — Last Updated: 2026-11-01*