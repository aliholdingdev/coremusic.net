---
title: "CoreMusic — Gemini / Antigravity Boot Instruction (Pointer)"
type: pointer
category: boot
version: 3.5.0
status: active
authority: "SSOT: .ai/CLAUDE.md (tam anayasa) · bu dosya §Skill Registry (Skill Registry SSOT)"
updated: 2026-11-02
---

# CoreMusic — GEMINI.md (Gemini / Antigravity Boot Instruction Pointer)

> **Kanonik kök dosyalar:**  
> - **OpenCode V2:** [`AGENTS.md`](AGENTS.md) dosyasını kök boot dosyası olarak okur (CLAUDE.md V2 tarafından yok sayılır).  
> - **Claude Code:** [`CLAUDE.md`](CLAUDE.md) dosyasını kök boot dosyası olarak okur.  
> - **Gemini / Antigravity:** Bu dosyayı (`GEMINI.md`) okur ve [`AGENTS.md`](AGENTS.md) / [`CLAUDE.md`](CLAUDE.md) boot zincirine bağlanır.  
> **BOOT (her zaman):** [`AGENTS.md`](AGENTS.md) ve [`CLAUDE.md`](CLAUDE.md) çift yönlü boot zinciriyle birbirine bağlıdır — iki dosya birlikte okunur, atlanmaz.  
> Vault (`.ai/` — anayasa, ADR, kurallar) **yalnız ihtiyaç anında** `@` ile okunur:  
> `@.ai/CLAUDE.md` · `@.ai/AGENTS.md` · `@.ai/WORKFLOW.md` · `@.ai/CONTEXT.md` · `@.ai/RAG.md` — boot'ta toplu okuma yok.  
> **Skill Registry SSOT:** Bu dosyanın §Skill Registry bölümüdür — başka dosyada liste kopyalanmaz.

⚠️ **VERIFICATION REQUIRED**: `.ai/CLAUDE.md` v27.6.0 diskte MEVCUTTUR — bu dosyayı okumadan kod yazılmaz (Guardrail #2).

---

## Master Engineering System (15 Aşama)

```text
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure → Verify → Document
```

- **Kural:** Analiz tamamlanmadan uygulama (implementation) başlatılmaz. Kod yazmadan önce repo incelenir: dosya yapısı, mimari katmanlar, bağımlılıklar, pattern'ler, build/test/config/logging/security/veritabanı/API/frontend yüzeyleri, duplicate analizleri ve teknik borç ([`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) §3).
- **YASAK:** Keyfi dosya/klasör açma, mevcut dosyayı incelemeden yeniden yazma, mimariyi bozma, duplicate abstraction, doğrulanmamış API, olmayan sınıf/paket/fonksiyon varsayma, test/build çalıştırmadan "tamam" deme.
- **Zero-Hallucination:** Repository, URL, API, sınıf, metod, versiyon, konfigürasyon, MCP, skill, agent asla uydurulmaz → doğrulanamayan = `⚠️ VERIFICATION REQUIRED` · bilinmeyen = `UNKNOWN`.
- **Otonom Akış:** `Understand → Research → Resolve → Decide → Implement → Track → Verify`.
- **Kullanıcı Kararları (2026-11-01):**
  - ADR-112: Canlı DB şeması dump SSOT'a migration ile hizalanır.
  - ADR-113: API V1/V2 paralel tek rota tablosunda `{version}` placeholder'ı ile yönetilir.
  - ADR-114: Repository Pattern standardı yalnız YENİ kod için geçerlidir; mevcut kod dondurulmuştur.
  - ADR-115: Medya yönetimi `admin.coremusic.net` üzerinde toplanır (media Faz 3 web paneli iptal).

---

## Behavioral Guidelines (Andrej Karpathy İlkeleri)

> **Skill:** [`.claude/skills/karpathy-guidelines/SKILL.md`](.claude/skills/karpathy-guidelines/SKILL.md) · **Cursor:** [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) · [`CURSOR.md`](CURSOR.md)  
> 1. **Think Before Coding:** Varsayım yapma. Şüpheyi ve belirsizliği gizleme. Seçenekleri ve ödünleşimleri sun. Basit bir yol varsa itiraz et (push back).  
> 2. **Simplicity First:** Sorunu çözen minimum kod. Spekülatif hiçbir şey yok. İstenmeyen soyutlama üretme. 200 satırlık iş 50 satırda çözülebiliyorsa yeniden yaz.  
> 3. **Surgical Changes:** Yalnızca dokunman gereken yere dokun. İlgisiz kodlara, formatlara ve yorumlara dokunma. Kendi izini temizle (yetim import/değişkenleri sil).  
> 4. **Goal-Driven Execution:** Başarı kriterini testlerle tanımla. Doğrulanana kadar döngüde kal. Çok adımlı işlerde kısa plan belirt (`[Step] → verify: [check]`).

---

## Skill Kullanım Zorunluluğu (Skill Usage Mandate)

1. Her görev başlangıcında aşağıdaki Skill Registry + available-skills listesi taranır ve eşleşme KONTROL EDİLİR.
2. Görev bir skill'in "Use when..." tanımıyla eşleşiyorsa: İLK işlem Skill tool ile o skill'i yüklemektir. Eşleşme varken skill'siz işlem başlatmak YASAKTIR.
3. Birden fazla eşleşen varsa en dar (domain-specific) olan önce yüklenir.
4. İstisna: (a) kullanıcı açıkça "skill kullanma" derse; (b) görev skill'lerle ilgisizse (sohbet, aritmetik). Belirsizse 1 kısa soru sorulur.
5. Yetenek yoksa kopyala-yapıştır YAPILMAZ; `skill-maker` ile skill ÜRETİLİR (üretim kuralı: Guardrail #16 + v3.0 şeması).

---

## Skill Registry (SSOT)

**Tek kayıt defteri bu bölümdür** — format otoritesi: `.claude/skills/skill-maker/` (şablon + kurallar).

| # | Skill | Scope | Path | Use when (1 satır, TR) | State |
|---|---|---|---|---|---|
| 1 | `prompt-maker` | project | `.claude/skills/prompt-maker/SKILL.md` | Ham prompt'u yapısal prompt'a çevirir (P0–P3 soru ağacı) | upgraded |
| 2 | `skill-maker` | project | `.claude/skills/skill-maker/SKILL.md` | Skill üretir / günceller (format otoritesi) | upgraded |
| 3 | `ui-analyzer` | project | `.claude/skills/ui-analyzer/SKILL.md` | UI denetimi / tasarım incelemesi / mockup kontrolü | upgraded |
| 4 | `ui-code-generator` | project | `.claude/skills/ui-code-generator/SKILL.md` | Frontend (HTML/CSS/JS) kod üretimi (ITCSS/BEM/token) | upgraded |
| 5 | `agent-orchestrator` | project | `.claude/skills/agent-orchestrator/SKILL.md` | Çok-ajan görev dağıtımı, handover, koordinasyon | restored/merged |
| 6 | `database-normalize-maker` | project | `.claude/skills/database-normalize-maker/SKILL.md` | BCNF / şema tasarımı, normalizasyon, migration | restored/merged |
| 7 | `composer-sync` | project | `.claude/skills/composer-sync/SKILL.md` | Composer / vendor senkronu, autoload onarımı | restored/merged |
| 8 | `vault-sync-post` | project | `.claude/skills/vault-sync-post/SKILL.md` | İşlem sonrası vault senkronu ve loglama | restored/merged |
| 9 | `php-backend-standards` | project | `.claude/skills/php-backend-standards/SKILL.md` | PHP 8.4 / strict_types / middleware / raw PDO | new |
| 10 | `audio-engine-cpp` | project | `.claude/skills/audio-engine-cpp/SKILL.md` | C++20 / JUCE / ASIO / DSP / Neva Engine | new |
| 11 | `hardware-electronics` | project | `.claude/skills/hardware-electronics/SKILL.md` | Class AB amfi / PSU / termal / PCB / BOM | new |
| 12 | `security-hardening` | project | `.claude/skills/security-hardening/SKILL.md` | Auth / CSRF / CSP / şifreleme / OWASP 2025 | new |
| 13 | `learning-prompts` | project | `.claude/skills/learning-prompts/SKILL.md` | Sokratik / Feynman / aktif hatırlama teknikleri | new |
| 14 | `karpathy-guidelines` | project | `.claude/skills/karpathy-guidelines/SKILL.md` | LLM kodlama zaaflarını önleme, Andrej Karpathy 4 ilkesi | new |

✅ **DISK ÖLÇÜMÜ (2026-11-02):** `.claude/skills/` = **14 dizin** (yukarıdaki 14 satır birebir eşleşir).  
Global envanter `C:\.claude\skills` makinede mevcuttur (348 dizin / 502 SKILL.md). Proje SSOT'u bu 14 skill'dir.

---

## 🔗 Bağlantılar (Bu dosyanın ilişkileri)

| Bu Dosya (Root) | .ai/ SSOT | Açıklama |
|:---|:---|:---|
| `CLAUDE.md` | [`@.ai/CLAUDE.md`](.ai/CLAUDE.md) | Claude Code boot pointer — tam anayasa burada · **§Skill Registry (SSOT)** |
| `AGENTS.md` | [`@.ai/AGENTS.md`](.ai/AGENTS.md) | OpenCode V2 root boot — tam agent registry burada |
| `CURSOR.md` | [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) | Cursor IDE konfigürasyonu ve Karpathy kuralları |
| `TODO.md` | [`.workflows/tasks.md`](.workflows/tasks.md) · [`.workflows/design.md`](.workflows/design.md) | Kiro Tasks Spec standardında aşamalandırılmış görevler |
| `RAG-KILAVUZU.md` | [`RAG.md`](RAG.md) · [`.ai/RAG.md`](.ai/RAG.md) | DOGE DOC — Kullanıcı RAG sorgulama ve token tasarruf kılavuzu |
| — | [`@.ai/.rules/kurallar.md`](.ai/.rules/kurallar.md) | `KURAL-01..11` Temel direktifler (Kurucu kimliği, Light Mode, amfi, Karpathy) |
| — | [`@.ai/.rules/hata-listesi.md`](.ai/.rules/hata-listesi.md) | `HL-01..12` Hata ve tuzak önleme kataloğu |
| — | [`@.ai/.rules/karar-kurallari.md`](.ai/.rules/karar-kurallari.md) | Onaylı ADR kararlarının (ADR-112..115) kalıcı kural türevleri |
| — | [`@.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) | Bütün proje kuralları, Senior Mode, 16 analiz adımı |
| — | [`@.ai/.rules/RULES.md`](.ai/.rules/RULES.md) | Kapsamlı aktivite ve guardrails defteri |
| — | [`.ai/.env.figma`](.ai/.env.figma) | Figma API Token SSOT (`FIGMA_TOKEN` / `FIGMA_FILE_KEY`) |
| `WORKFLOW.md` | [`.ai/WORKFLOW.md`](.ai/WORKFLOW.md) · [`.workflows/workflow.md`](.workflows/workflow.md) | Kiro 6-adımlı spec akışı ve faz kapıları |
| `README.md` | [`.ai/VISION.md`](.ai/VISION.md) · [`.ai/PROJECTS.md`](.ai/PROJECTS.md) | Giriş — vizyon ve proje tanımı |
| `CONTEXT.md` | [`.ai/CONTEXT.md`](.ai/CONTEXT.md) | Vault klasör yapısı ve envanter |
| `RAG.md` | [`.ai/RAG.md`](.ai/RAG.md) | Retrieval indeksi + pipeline |
| — | [`.ai/brain.md`](.ai/brain.md) + [`.ai/.decisions/`](.ai/.decisions/) | Mimari kararlar (ADR-001..115) |
| — | [`.ai/durum.md`](.ai/durum.md) | Tek sayfalık oturum durum devri ve kalıcı hafıza |
| — | [`app-cpp-projects/neva-engine/`](app-cpp-projects/neva-engine/) | C++20 Neva Engine ses motoru ve C ABI DLL (`neva_session.dll`) |

> **Boot okuma sırası**: [[AGENTS.md]] → [[CLAUDE.md]] → [[.ai/durum.md]] → [[README.md]] → [[WORKFLOW.md]] → ihtiyaç anında `@.ai/CLAUDE.md` + `@.ai/AGENTS.md` + `@.ai/.rules/kurallar.md` + `@.ai/.rules/hata-listesi.md` + `@.ai/.rules/senior-mode.md` + `@.ai/WORKFLOW.md` + `@.ai/CONTEXT.md` + `@.ai/RAG.md`.

**Değiştirilemez Sabitler:**
1. **Tema Zorunluluğu (YALNIZCA LIGHT MODE — `HL-01`):** CoreMusic'te ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR. Dark mode henüz yapılmamıştır/yoktur! Tüm CSS/UI tasarımları istisnasız Light Mode'a göre tasarlanır; dark mode varsayımı veya dark mode kodu yazmak KESİNLİKLE YASAKTIR.
2. **Amplifikatör Topolojisi (CLASS AB — `HL-02`):** Kesinlikle discrete **Class AB** (Class D ve PWM yasaktır; AK4458 DAC / PCM3168A codec; LM5122 boost; >0.5V DC offset röle koruması; SNR >105dB, THD+N <0.003%).
3. **Kalıcı Oturum Hafızası & Devir Protokolü (`KURAL-05` & `HL-11`):** AI her oturum başında ilgili modülün `durum.md` dosyasını okur; oturum sırasında veya kapanışta `durum.md`, [`.ai/log.md`](.ai/log.md) ve [`.ai/MEMORY.md`](.ai/MEMORY.md) dosyalarına durum devrini işler.
4. **Subagent Commit Yasağı (`HL-03`):** Subagent `git commit` ve `git add` ATAMAZ; commit orkestratöre/kullanıcıya aittir.
5. **Veri Erişimi & Repository Standardı (`HG-09` & ADR-114):** ORM kesinlikle yasaktır; `SELECT *` kesinlikle yasaktır; raw PDO prepared statement kullanılır; yeni repository'lerde `I*` interface + `final class` standardı zorunludur.
6. **Karpathy Davranışsal İlkeleri (`KURAL-11`):** Think Before Coding (varsayım yapma, seçenek sun), Simplicity First (minimum kod, sıfır spekülasyon), Surgical Changes (yalnızca istenen yere dokun, yetimleri temizle), Goal-Driven Execution (test-first doğrulanabilir hedefler).

---

## 🔍 Web Verification Status (2026-10-07)

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

*CoreMusic Gemini Pointer v3.5.0 — SSOT: `.ai/` — Last Updated: 2026-11-02*
