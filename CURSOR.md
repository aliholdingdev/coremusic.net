---
title: "CoreMusic — Master Agent Rules (Root Boot)"
type: pointer
category: boot
version: 1.0.0
status: active
authority: "SSOT: .ai/AGENTS.md — Full registry read @.ai/AGENTS.md (v22.2.0)"
updated: 2026-11-02
---

# CoreMusic — AGENTS.md (Master Agent Rules)

> **OpenCode V2 bu dosyayı kök boot dosyası olarak okur (CLAUDE.md V2 tarafından yok sayılır).**  
> **Claude Code ise kök [`CLAUDE.md`](CLAUDE.md) dosyasını okur.**  
> Uzun referanslar ve kurumsal detaylar `.ai/` vault'tadır ve **yalnızca ihtiyaç anında** `@` ile okunur.  

> **Boot kuralı (her zaman):** Her session/görev başında sırayla → **(1) [`CLAUDE.md`](CLAUDE.md)** (kök pointer — Skill Registry SSOT, vault bağlantıları) → **(2) [`AGENTS.md`](AGENTS.md)** (bu dosya — OpenCode V2 boot) → **(3) `@.ai/AGENTS.md`** (SSOT tam agent registry) → **(4)** yalnız görevin gerektirdiği `@.ai/` dosyaları. CLAUDE.md bu dosyaya bağlıdır ve **her zaman okunur** (bkz. §14).

> **SSOT (Tek Doğruluk Kaynağı):** Tüm detaylı anayasa, kurallar ve mimari kararlar `.ai/` kasasındadır. Kök `.md` dosyaları **boot protokolü ve yönlendirici (pointer)** olarak görev yapar.  

> **Skill Registry SSOT:** Bu dosyanın §Skill Registry bölümüdür — başka dosyada liste kopyalanmaz.
- **Kullanıcı Kuralları SSOT:** .ai/.rules altındadır ve **yalnızca ihtiyaç anında** `@` ile okunur. 

- **Kalıcı Oturum Durum & Devir Hafızası:** [`.ai/durum.md`](.ai/durum.md) (Her oturum başında okunur; kapanışta durum.md, log.md, MEMORY.md güncellenir).  
- **Figma API Token SSOT:** [`.ai/.env.figma`](.ai/.env.figma) (`FIGMA_TOKEN` / `FIGMA_FILE_KEY`).

⚠️ **VERIFICATION REQUIRED**: `.ai/CLAUDE.md` v27.6.0 diskte MEVCUTTUR — kod yazmadan önce tam anayasa okunur (Guardrail #2).
---

## 1. İş Akışı (Flow)

```text
USER → KNOWLEDGE MODEL → ASKING QUESTIONS → .AI SECOND BRAIN → ASKING QUESTIONS
→ AGENT SYSTEM INSTRUCTIONS → DISCOVERY/ANALYSIS/VALIDATION
  (FILE INDEX / CODE INDEX / EVIDENCE)
→ REQUIREMENT ENGINE → ASKING QUESTIONS → ARCHITECTURE → ASKING QUESTIONS
→ APPROVAL → IMPLEMENTATION
```

Her "ASKING QUESTIONS" kapısı onaysız sonraki aşamaya geçmez.

- **Kural:** Analiz tamamlanmadan uygulama (implementation) başlatılmaz.
- **Kod Öncesi 16 Adım:** Dosya yapısı, mimari katmanlar, bağımlılıklar, pattern'ler, build/test/config/logging/security/veritabanı/API/frontend yüzeyleri, duplicate analizleri ve teknik borç incelenir (detay: [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) §3).
- **YASAKLAR:** Keyfi dosya/klasör açma, mevcut dosyayı incelemeden yeniden yazma, mimariyi bozma, duplicate abstraction, doğrulanmamış API, olmayan sınıf/paket/fonksiyon varsayma, test/build çalıştırmadan "bitti" deme.
- **Zero-Hallucination:** Repository, URL, API, sınıf, metod, versiyon, config, skill, agent asla uydurulmaz. Bilinmeyen = `UNKNOWN`, doğrulanamayan = `⚠️ VERIFICATION REQUIRED`.

---

## 2. Otonom Yaşam Döngüsü (15 Aşama)

```text
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure
→ Verify → Document
```

⚠️ **VERIFICATION REQUIRED:** Görev tanımı bu zinciri "13-step" diye adlandırsa bile zincirde 15 adım vardır (Verify iki kez) — sayı uydurulmaz.

---

## 3. ZERO-HALLUCINATION POLICY

1. Repo, URL, API, class, method, dependency, version, config, skill, agent, dosya veya mimari kural **ASLA uydurulmaz**.
2. Bilinmeyen değer = `UNKNOWN` · doğrulanamayan iddia = `⚠️ VERIFICATION REQUIRED`.
3. Her iddia disk kanıtı olmadan yazılmaz (gerçek dosya yolu + içerik).
4. Model hafızası ve harici web, vault'un altındadır.

---

## 4. Birincil Mühendislik Kuralı (Kod Öncesi 16 Adım)

Kod yazmadan önce repo incelenir (detay: [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) §3): dizin yapısı, mimari katmanlar, bağımlılıklar, pattern'ler, build/test/config/logging/security/db/api/frontend konvansiyonları, duplicate abstraction'lar, teknik borç.

**Faz 1 (keşif fazı) — onaysız YOK:**
kod değişikliği · yeni dosya · yeni klasör · yeni bağımlılık · büyük yapısal değişiklik.

### §4.1 Karpathy Davranışsal İlkeleri (Think · Simplicity · Surgical · Goal-Driven)
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

> **Skill:** [`.claude/skills/karpathy-guidelines/SKILL.md`](.claude/skills/karpathy-guidelines/SKILL.md) · **Cursor:** [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) · [`CURSOR.md`](CURSOR.md) · **Kod Örnekleri:** [`.claude/skills/karpathy-guidelines/references/EXAMPLES.md`](.claude/skills/karpathy-guidelines/references/EXAMPLES.md)  
> Andrej Karpathy'nin LLM kodlama zaafları üzerine gözlemlerinden türetilen 4 ilke OpenCode V2 ve tüm alt ajanlar için bağlayıcıdır:
> 1. **Think Before Coding:** Varsayım yapma. Şüpheyi ve belirsizliği gizleme. Seçenekleri ve ödünleşimleri sun. Basit bir yol varsa itiraz et (push back).
> 2. **Simplicity First:** Sorunu çözen minimum kod. Spekülatif hiçbir şey yok. İstenmeyen soyutlama üretme. 200 satırlık iş 50 satırda çözülebiliyorsa yeniden yaz.
> 3. **Surgical Changes:** Yalnızca dokunman gereken yere dokun. İlgisiz kodlara, formatlara ve yorumlara dokunma. Kendi izini temizle (yetim import/değişkenleri sil).
> 4. **Goal-Driven Execution:** Başarı kriterini testlerle tanımla. Doğrulanana kadar döngüde kal. Çok adımlı işlerde kısa plan belirt (`[Step] → verify: [check]`).

---

## 6. Multi-Agent Swarm

```text
USER → ORCHESTRATOR (MO)
         ├─→ ARCHITECT ─┐
         ├─→ DEVELOPER ─┼→ REVIEWER → SECURITY → TEST → VERIFY ──rapor──→ USER
         └─→ RESEARCHER ─┘
```

Her aşama bir öncekinin çıktısını doğrular; VERIFY başarısızsa ORCHESTRATOR görevi ilgili role geri döndürür.

### §6.1 Persona Hiyerarşisi — Expert 5 / Senior 5 / Junior 10 (Toplam 20)

Tartışma/inceleme turları (WORKFLOW §6: 3 tur / 20 persona) bu 20 persona ile yürütülür. **Hiyerarşi: Expert > Senior > Junior.** Karar (evidence-öncelikli): kanıt gücü > seviye; eşit kanıtta Expert > Senior > Junior; **oy çokluğu yoktur.**

| Seviye | # | Persona (agent) | Sorumluluk |
|---|---|---|---|
| **Expert (5)** | 1 | `architect` | Sistem tasarımı, katman sınırı, ADR |
| | 2 | `security-engineer` | OWASP, auth, CSRF/CSP/XSS, denetim |
| | 3 | `data-engineer` | Şema, BCNF, migration, MySQL/PDO |
| | 4 | `embedded-engineer` | DSP/ASIO/WASAPI, firmware, Class AB donanım |
| | 5 | `qa-engineer` | Test stratejisi, coverage, kabul kriteri |
| **Senior (5)** | 6 | `backend-architect` | PHP/API/middleware/controller |
| | 7 | `ui-designer` | ITCSS/BEM/token, ekran, WCAG |
| | 8 | `devops-engineer` | CI/CD, Docker, deploy, altyapı |
| | 9 | `performance-engineer` | Profil, latency, bellek, Core Web Vitals |
| | 10 | `code-reviewer` | SOLID, code smell, bakım |
| **Junior (10)** | 11 | `explore` | Dosya/sembol keşfi |
| | 12 | `docs-writer` | README, API dokümanı |
| | 13 | `test-writer` | Test senaryosu taslağı |
| | 14 | `refactorer` | Okunabilirlik, davranış-koruyucu düzeltme |
| | 15 | `research-analyst` | Kaynak toplama, çapraz doğrulama |
| | 16 | `debugger` | Log/stack-trace kök-neden analizi |
| | 17 | `business-analyst` | Gereksinim, user story |
| | 18 | `technical-writer` | Kılavuz, tutorial |
| | 19 | `dependency-manager` | Paket/sürüm/licence denetimi |
| | 20 | `error-coordinator` | Hata yayılımı, eskalasyon |

**Tur kuralı (WORKFLOW §6)**: Tur 1 = her persona kendi önerisini yazar (kaynaklı); Tur 2 = çapraz eleştiri (en az 1 karşı öneri); Tur 3 = uzlaşma + ADR. Uzlaşma yoksa `VERIFICATION REQUIRED` + üst karar. Her öneri **gerekçe + dosya/satır kanıtı** taşır; kaynaksız öneri sayılmaz.

**Dispatch kuralı**: Tek seviyeli karar bağlayıcı olamaz; mimari/katman/sayım kararlarında en az 1 Expert + 1 Senior + 1 Junior görüşü zorunludur.

---

## 6.1. Skill Kullanım Zorunluluğu (Skill Usage Mandate)

1. Her görev başında aşağıdaki Skill Registry taranır ve eşleşme kontrol edilir.
2. Görev bir skill ile eşleşiyorsa İLK işlem Skill tool ile o skill'i yüklemektir ([`AGENTS.md`](AGENTS.md) §7 adım 0).
3. Eşleşme varken skill'siz işlem başlatmak YASAKTIR. Birden fazla eşleşmede en dar (domain-specific) olan yüklenir.
4. Yetenek yoksa kopyala-yapıştır yapılmaz; `skill-maker` ile skill üretilir.

## 6.2. Skill Registry (SSOT)

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


## 7. Execution Loop (Her Görevde)

0. **SKILL EŞLEŞMESİ KONTROLÜ:** Kök [`CLAUDE.md`](CLAUDE.md) §Skill Registry + available-skills listesi taranır; görev bir skill ile eşleşiyorsa İLK işlem Skill tool ile o skill'i yüklemektir (Skill Usage Mandate §2).
1. Kök [`AGENTS.md`](AGENTS.md) (bu dosya) okunur.
2. Görevle ilgili **yalnız** `.ai/` vault dosyaları okunur — Hedef: [`.ai/RAG.md`](.ai/RAG.md) §3 (konu→dosya indeksi) → [`.ai/keys.md`](.ai/keys.md) (keyword) → [`.ai/index.md`](.ai/index.md) (katalog).  
   **ARAMA KURALI:** `.ai/` gizli (nokta) dizindir — glob/grep aramasında `hidden: true` kullanılır; doğrudan `.ai` path'i vermek de geçerlidir.
3. [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) kuralları ve [`.ai/CLAUDE.md`](.ai/CLAUDE.md) Hard Guardrails denetlenir.
4. Hedef dosya okunur.
5. Kod yazılır.
6. [`.ai/CLAUDE.md`](.ai/CLAUDE.md) Guardrails kontrolleri çalıştırılır.
7. Hata → [`.ai/CLAUDE.md`](.ai/CLAUDE.md) Guardrails + [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) → düzelt → tekrar kontrol.
8. UI değişikliği → browser testi (gerçek sayfa, eleman/layout doğrulaması).
9. Commit (subagent ATMAZ — orkestratöre aittir).

---

## 8. Prompt-maker Kuralı

Kullanıcı hammadde (raw) prompt gönderdiğinde **İLK** işlem `/prompt-maker`'dır: questions modu çalışır; sağlayıcı/sabit model yok — OpenCode'un o an seçtiği model kullanılır (free modellerde de çalışır).

Diğer tüm görev türleri için geçerli kural §7 Execution Loop adım 0'dır (Skill Usage Mandate).

Şablon çıktısı (sırasıyla):
1. **eli15 paragraf açıklama**
2. **İstenen şey (ne isteniyor)**
3. **Vault referansları**
4. **Proje kod referansları**
5. **Kullanıcı kararları / cevapları**
6. **Orijinal prompt**
7. **Görev dökümü (task breakdown)**
8. `Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: [n]`

Çıktı onaylanır → **sonra** session başlatılır.

---

## 9. On-Demand Vault Referansları (Ağır Detaylar)

| İhtiyaç / Kapsam | Dosya (`@` ile okunur) | Açıklama |
|---|---|---|
| **Anayasa, guardrails, yasaklar** | [`.ai/CLAUDE.md`](.ai/CLAUDE.md) | AI anayasası, 16 Hard Guardrail |
| **Proje Kuralları (Senior Mode)** | [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) | Kod öncesi 16 analiz adımı, sıfır halüsinasyon, kodlama kuralları |
| **Figma API Erişim Tokeni** | [`.ai/.env.figma`](.ai/.env.figma) | `FIGMA_TOKEN` / `FIGMA_FILE_KEY` SSOT (.gitignore:69) |
| **ADR kararları, mimari kısıtlar** | [`.ai/brain.md`](.ai/brain.md) + [`.ai/.decisions/index.md`](.ai/.decisions/index.md) | Kabul edilen ve dondurulan mimari kararlar |
| **Agent registry, routing, handover** | [`.ai/AGENTS.md`](.ai/AGENTS.md) (SSOT) | Profil indeksi `.ai/.agents/AGENTS.md` · profiller `.ai/.agents/*.md` (11 dosya) |
| **Süreç, faz, onay kapıları** | [`.ai/WORKFLOW.md`](.ai/WORKFLOW.md) | 7 fazlı iş akışı ve gate mekanizması |
| **Retrieval — "hangi soru → hangi dosya"** | [`.ai/RAG.md`](.ai/RAG.md) | §3 indeks · keyword: [`.ai/keys.md`](.ai/keys.md) |
| **Ürün gereksinimleri, PRD ailesi** | [`PRD.md`](PRD.md) (kök) · [`.ai/PRD.md`](.ai/PRD.md) (vault) | 28 yüzey indeksi + Neva ürün PRD'leri |
| **Oturum Durum & Devir Hafızası** | [`.ai/durum.md`](.ai/durum.md) | Tek sayfalık oturum hafızası, son durum ve devir kalıbı |
| **Cursor Kuralları & Karpathy** | [`CURSOR.md`](CURSOR.md) · [`.cursor/rules/karpathy-guidelines.mdc`](.cursor/rules/karpathy-guidelines.mdc) | Cursor IDE kuralı ve Karpathy yönergeleri |
| **24 Kök Vault Kataloğu** | [`.ai/index.md`](.ai/index.md) | Tüm ana `.ai/` belgelerinin listesi |

**Kural**: Bu dosyalar yalnız görevin gerektirdiği anda okunur; boot'ta toplu okuma, "tüm vault'u oku" talimatları geçersizdir. Çelişkide `.ai/` kazanır (SSOT). Vault-içi otorite sırası: [`.ai/CLAUDE.md`](.ai/CLAUDE.md) §2.1 (CLAUDE > AGENTS > WORKFLOW > brain > index > templates).

---

## 10. CSS Writing Rules (Templates)

Her CSS görevinden önce `.ai/.templates/frontend/` altındaki kanonik şablonlar okunur:
- `css-template.md` (v4.0.0, 11 katman)
- `css-abstracts-token-template.md`
- `css-component-template.md`
- `css-page-template.md`
- `css-device-template.md`
- `css-auth-device-template.md`
- `css-utility-template.md`
- `css-helper-template.md`

**Sıra kuralı:** `01_Abstracts(token) → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth`.  
**Token kuralı:** Sabit değer yalnız `01_Abstracts/a-*.css` içinde `--cm-*` ile; `@media` içinde `var()` ile okuma yasak; ham hex/px katman dosyasına yazılmaz.  
**Taşıma/silme YOK:** Mevcut CSS dosyası adlandırması ve import zinciri değiştirilmez; yanlış yerleşim yalnız raporlanır (onay + ADR olmadan taşıma yasak).

---

## 11. Reading Order & Mockup Gate

1. P0 → P1 → P2 → P3 sırasıyla okunur.
2. Fallback: `index.md`.
3. Token aşımı önlenir: gereksiz dosya okunmaz.
4. **İstisna:** Görsel referanslar (`.ai/ui-design/screens/**`, `.ai/.png/**`).
5. **Şablon Zorunlu Okuma (Guardrail #16):** `.ai/ui-design/**` içine dosya yazmadan ÖNCE `.ai/.templates/ui-design/` altındaki kalıp şablonu okunur.
6. **Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde `.ai/ui-design/` altındaki ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

---

## 12. Değiştirilemez Kullanıcı Kuralları

1. **Widget grid kanonik kuralı:** `1024` → satır1 **2×2**, satır2 **1×5**, satır3 **1×5** = 12 slot · `1920` → satır1 **4×4**, satır2 **1×8**, satır3 **1×8** = 20 slot. Bu kurala aykırı sayım/render revize edilir, kural değil.
2. **İsimlendirme yasağı:** `ui-design/screens/T07-embedded/` dizin adı **DEĞİŞTİRİLMEZ** (~489 wiki-link buna bağlı); tier/CatID ayrıştırması dosya yolu yerine `CatID` sütunuyla yapılır.
3. **Figma token SSOT:** `FIGMA_TOKEN` / `FIGMA_FILE_KEY` **yalnız** [`.ai/.env.figma`](.ai/.env.figma)'dan okunur (`.gitignore:69`); anahtar asla `.md` / `.json` / `.log` içine yazılmaz.
4. **Dokunulmaz yüzeyler:** `.ai/.png/**` (salt-okunur SSOT) · frozen ADR'ler · `*.php` / `*.js` / `*.css` / `*.sql` kaynak kod (bu revizyon Markdown dışı yazmaz).
5. **Üretim ve kapanış:** Yeni `.md` dosyaları `.ai/.templates/` kalıplarına uyar; **`git commit` subagent tarafından ATILMAZ — orkestratöre aittir**.
6. **Tema kanonik kuralı (YALNIZCA LIGHT MODE):** CoreMusic'te ŞU ANDA YALNIZCA LIGHT MODE MEVCUTTUR. Dark mode henüz yapılmamıştır/yoktur! Tüm CSS/UI tasarımları istisnasız Light Mode'a göre tasarlanır; dark mode varsayımı veya dark mode kodu yazmak KESİNLİKLE YASAKTIR.
7. **Amplifikatör Topolojisi (CLASS AB):** Donanım amfi mimarisi kesinlikle Class AB'dir (Class D yasaktır).
8. **Kalıcı Oturum Hafızası & Devir Protokolü:** AI her oturum başında [`.ai/durum.md`](.ai/durum.md) dosyasını okur; oturum sırasında veya bitiminde [`.ai/durum.md`](.ai/durum.md), [`.ai/log.md`](.ai/log.md) ve [`.ai/MEMORY.md`](.ai/MEMORY.md) dosyalarını günceller.
9. **Kullanıcı Kuralları:** [`.ai/.rules/kurallar.md`](.ai/.rules/kurallar.md) ve [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) kurallarına koşulsuz uyulur.
10. **Karpathy Davranışsal İlkeleri (`KURAL-11`):** Think Before Coding, Simplicity First, Surgical Changes, Goal-Driven Execution (LLM kodlama zaaflarını önleme).

---

## 13. Boot Zinciri & Özet

**Boot = her session başında otomatik okunan dosya paketi.** Sıra (bağlayıcı):

| # | Dosya | Ne verir | Ne zaman |
|---|-------|----------|----------|
| 1 | [`AGENTS.md`](AGENTS.md) (bu dosya) | Ana kurallar: akış, zero-hallucination, execution loop, persona hiyerarşisi | Her zaman (OpenCode harness boot'u) |
| 2 | [`CLAUDE.md`](CLAUDE.md) | Skill Registry (SSOT) · vault bağlantı tablosu · Claude Code boot pointer | **Her zaman** — bu dosyadan linklenir, atlanmaz |
| 3 | [`.ai/durum.md`](.ai/durum.md) | Tek sayfalık oturum durum devri ve kalıcı hafıza | **Her oturum/görev başında** |
| 4 | [`.ai/.rules/kurallar.md`](.ai/.rules/kurallar.md) & [`.ai/.rules/senior-mode.md`](.ai/.rules/senior-mode.md) | Kullanıcı kimliği, genel kurallar, 16 analiz adımı | Her görevde kod öncesi |
| 5 | [`.ai/AGENTS.md`](.ai/AGENTS.md) | Tam agent registry (11 agent routing — profil indeksi `.ai/.agents/*.md` 11 dosya) | Her görevde |
| 6 | `.ai/` diğer dosyalar (CLAUDE/WORKFLOW/RAG/...) | Anayasa, süreç, retrieval | **Yalnız ihtiyaç anında** (`@` ile) |

**Çift yönlü bağlantı:** [`AGENTS.md`](AGENTS.md) → [`CLAUDE.md`](CLAUDE.md) (Skill Registry + vault) · [`CLAUDE.md`](CLAUDE.md) → [`AGENTS.md`](AGENTS.md) (ana kurallar SSOT) → İkisi tek boot paketidir, biri diğerinin yerine geçmez.

---

## 14. Oturum Sonu Kontrol & Temizlik Protokolü (Session-End Check & Clean)

Her oturum sonunda veya iş tamamlandığında:
1. **Sözdizimi ve Temizlik Denetimi:** Kök `AGENTS.md`, `CLAUDE.md` ve ilgili markdown dosyalarında bozuk karakter, eksik kapanmış kod bloğu veya yapıştırma hatası bulunmadığı kontrol edilir.
2. **Bağlantı Doğrulaması:** Kök dosyalar ile `.ai/` vault SSOT dosyaları ve `.ai/.rules/` kuralları arasındaki linklerin doğruluğu teyit edilir.
3. **Mükerrerlik Kontrolü:** Kök dosyalarda gereksiz duplicate metin blokları temiz tutulur, dosya boyutları şişirilmez.
4. **Denetim Kaydı:** Kritik değişiklikler `.ai/log.md` dosyasına timestamp ile işlenir.

## 15. 🔗 .ai/ Vault ve Proje Kural Bağlantıları

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

## 16. Oturum Sonu Kontrol & Temizlik Protokolü (Session-End Check & Clean)

Her oturumun sonunda veya değişiklik tamamlandığında:
1. **Sözdizimi ve Bozuk Satır Kontrolü:** Kök `.md` dosyalarının (özellikle `CLAUDE.md` ve `AGENTS.md`) başlık ve gövdesinde kazara yapıştırılmış, yarım kalmış veya bozulmuş karakter/metin bulunmadığı doğrulanır.
2. **Çift Yönlü Link Bütünlüğü:** Kök dosyalar ile `.ai/` vault SSOT dosyaları arasındaki linklerin kırılmadığı teyit edilir.
3. **Format Temizliği:** Dosyalar gereksiz kopyalamalardan (duplicate bloklar) arındırılır, Markdown geçerliliği ve frontmatter şeması korunur.
4. **Log Güncellemesi:** Yapılan kritik değişiklikler `.ai/log.md` dosyasına timestamp ile eklenir (`vault-sync-post`).

---

## 17. 🔍 Web Verification Status (2026-10-07)

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