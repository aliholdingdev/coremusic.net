---
title: "CoreMusic — Boot Instruction (Pointer)"
type: pointer
category: boot
version: 3.3.0
status: active
authority: "SSOT: .ai/CLAUDE.md (tam anayasa) · bu dosya §Skill Registry (Skill Registry SSOT)"
updated: 2026-10-11
---

# .ai gizli dizin, hidden: true ile tarıyorum:
## .ai/README.md okuması (gerekirse)

**Gerekirse oku:** Bu dosya, .ai içindeki tüm agent'lar, skill'ler ve konvansiyonlar için tek referans noktasıdır. Herhangi bir agent veya skill'in spesifik davranış kurallarını, dosya organizasyonunu veya özel çalışma prensiplerini anlamak için bu dosyayı oku.   

## CLAUDE.md okuması (gerekirse)

**Gerekirse oku:** Bu dosya, .ai içindeki tüm agent'lar, skill'ler ve konvansiyonlar için tek referans noktasıdır. Herhangi bir agent veya skill'in spesifik davranış kurallarını, dosya organizasyonunu veya özel çalışma prensiplerini anlamak için bu dosyayı oku.   

## .ai/CLAUDE.md okuması (gerekirse)

**Gerekirse oku:** Bu dosya, .ai içindeki tüm agent'lar, skill'ler ve konvansiyonlar için tek referans noktasıdır. Herhangi bir agent veya skill'in spesifik davranış kurallarını, dosya organizasyonunu veya özel çalışma prensiplerini anlamak için bu dosyayı oku.   

# CLAUDE.md

Behavioral guidelines to reduce common LLM coding mistakes. Merge with project-specific instructions as needed.

**Tradeoff:** These guidelines bias toward caution over speed. For trivial tasks, use judgment.

## 1. Think Before Coding

**Don't assume. Don't hide confusion. Surface tradeoffs.**

Before implementing:
- State your assumptions explicitly. If uncertain, ask.
- If multiple interpretations exist, present them - don't pick silently.
- If a simpler approach exists, say so. Push back when warranted.
- If something is unclear, stop. Name what's confusing. Ask.

## 2. Simplicity First

**Minimum code that solves the problem. Nothing speculative.**

- No features beyond what was asked.
- No abstractions for single-use code.
- No "flexibility" or "configurability" that wasn't requested.
- No error handling for impossible scenarios.
- If you write 200 lines and it could be 50, rewrite it.

Ask yourself: "Would a senior engineer say this is overcomplicated?" If yes, simplify.

## 3. Surgical Changes

**Touch only what you must. Clean up only your own mess.**

When editing existing code:
- Don't "improve" adjacent code, comments, or formatting.
- Don't refactor things that aren't broken.
- Match existing style, even if you'd do it differently.
- If you notice unrelated dead code, mention it - don't delete it.

When your changes create orphans:
- Remove imports/variables/functions that YOUR changes made unused.
- Don't remove pre-existing dead code unless asked.

The test: Every changed line should trace directly to the user's request.

## 4. Goal-Driven Execution

**Define success criteria. Loop until verified.**

Transform tasks into verifiable goals:
- "Add validation" → "Write tests for invalid inputs, then make them pass"
- "Fix the bug" → "Write a test that reproduces it, then make it pass"
- "Refactor X" → "Ensure tests pass before and after"

For multi-step tasks, state a brief plan:
```
1. [Step] → verify: [check]
2. [Step] → verify: [check]
3. [Step] → verify: [check]
```

Strong success criteria let you loop independently. Weak criteria ("make it work") require constant clarification.

---

**These guidelines are working if:** fewer unnecessary changes in diffs, fewer rewrites due to overcomplication, and clarifying questions come before implementation rather than after mistakes.

---

# CoreMusic — Skill Registry (SSOT)

> **Disk kanıtı (2026-10-23 sayımı — bu makine/dev):** `.claude/skills/` = **13 SKILL.md** · `C:\.claude\skills` **bu makinede YOK** (kullanıcı notu 2026-10-23: envanter **diğer PC/server'da mevcut** — bu yoldaki referanslar **silinmez**) · `.opencode/skills/` kapalı (2026-10-07 merge/port). Eski "12 proje + 5 global = 17" envanteri **bu makinede bayattı** (5 global dizin bu diskte hiç yoktu); bu tablo bu makine için tek kaynaktır.

| # | Skill | Tetikleyici (özet — tam tetikleyiciler skill'dedir) |
|---|-------|------|
| 1 | `agent-orchestrator` | görev ata · koordine et · handover · dispatch · routing |
| 2 | `audio-engine-cpp` | Neva Engine · JUCE 9 · ASIO · DSP chain · realtime C++ |
| 3 | `composer-sync` | composer · vendor · autoload · junction · class not found |
| 4 | `database-normalize-maker` | normalize · BCNF · schema · migration · SQL yaz |
| 5 | `hardware-electronics` | amfi tasarımı · güç kaynağı · termal · PCB/BOM |
| 6 | `learning-prompts` | sokratik · Feynman · şeytanın avukatı · aktif hatırlama |
| 7 | `php-backend-standards` | PHP kodla · middleware ekle · endpoint yaz · PDO sorgusu |
| 8 | `prompt-maker` | prompt oluştur/yaz · system prompt · MASTER PROMPT · rules yaz · CLAUDE.md yaz |
| 9 | `security-hardening` | güvenlik değişikliği · CSRF ekle · CSP ayarı · auth kodu |
| 10 | `skill-maker` | yeni skill · skill oluştur/güncelle · SKILL.md yaz · skill audit |
| 11 | `ui-analyzer` | UI analiz · arayüz denetimi · tasarım sistemi kontrol · mockup karşılaştırma |
| 12 | `ui-code-generator` | html css js · responsive design · bileşen oluştur · ITCSS/BEM/token |
| 13 | `vault-sync-post` | vault güncelle · session kapat · senkronize · log yaz |

**Skill Usage Mandate (bağlayıcı):** Görev bir skill ile eşleşiyorsa **İLK işlem Skill tool ile o skill'i yüklemektir** (kök `AGENTS.md` §7 adım 0). Skill üretimi → `skill-maker` · prompt üretimi → `prompt-maker` · `.ai/` yazımı yalnız orkestratörde · skill "kod yazar" ama domain kodu domain agent'ındır.


# CoreMusic — AGENTS.md (Master Agent Rules)

> **OpenCode V2 bu dosyayı kök boot dosyası olarak okur (CLAUDE.md V2 tarafından yok sayılır).**
> Uzun referanslar `.ai/` vault'tadır ve **yalnızca ihtiyaç anında** `@` ile okunur.
> "Başta tüm vault'u oku" YASAKTIR.

> **Boot kuralı (her zaman):** Her session/görev başında sırayla → **(1) [`CLAUDE.md`](CLAUDE.md)** (kök pointer — Skill Registry SSOT, vault bağlantıları) → **(2) `@.ai/AGENTS.md`** (SSOT tam agent registry) → **(3)** yalnız görevin gerektirdiği `@.ai/` dosyaları. CLAUDE.md bu dosyaya bağlıdır ve **her zaman okunur** (bkz. §14).

Skill Registry SSOT: kök `CLAUDE.md` §Skill Registry.

## 1. İş Akışı (Flow)

```
USER → KNOWLEDGE MODEL → ASKING QUESTIONS → .AI SECOND BRAIN → ASKING QUESTIONS
→ AGENT SYSTEM INSTRUCTIONS → DISCOVERY/ANALYSIS/VALIDATION
  (FILE INDEX / CODE INDEX / EVIDENCE)
→ REQUIREMENT ENGINE → ASKING QUESTIONS → ARCHITECTURE → ASKING QUESTIONS
→ APPROVAL → IMPLEMENTATION
```

Her "ASKING QUESTIONS" kapısı onaysız sonraki aşamaya geçmez.

## 2. Otonom Yaşam Döngüsü

```
Understand → Discover → Research → Verify → Resolve Context → Analyze
→ Architect → Plan → Implement → Track → Test → Review → Secure
→ Verify → Document
```

VERIFICATION REQUIRED: görev tanımı bu zinciri "13-step" diye adlandırır;
zincirde 15 adım vardır (Verify iki kez) — sayı uydurulmaz.

## 3. ZERO-HALLUCINATION POLICY

1. Repo, URL, API, class, method, dependency, version, config, skill, agent,
   dosya veya mimari kural **ASLA uydurulmaz**.
2. Bilinmeyen değer = `UNKNOWN` · doğrulanamayan iddia = `VERIFICATION REQUIRED`.
3. Her iddia disk kanıtı olmadan yazılmaz (gerçek dosya yolu + içerik).
4. Model hafızası ve web, vault'un altındadır.

## 4. Birincil Mühendislik Kuralı (Kod Öncesi)

Kod yazmadan önce repo'yu incele: dizin yapısı, mimari katmanlar, bağımlılıklar,
pattern'ler, build/test/config/logging/security/db/api/frontend konvansiyonları,
duplicate abstraction'lar, teknik borç.

**Faz 1 (keşif fazı) — onaysız YOK:**
kod değişikliği · yeni dosya · yeni klasör · yeni bağımlılık · büyük yapısal değişiklik.

## 6. Multi-Agent Swarm

```text
USER → ORCHESTRATOR (MO)
         ├─→ ARCHITECT ─┐
         ├─→ DEVELOPER ─┼→ REVIEWER → SECURITY → TEST → VERIFY ──rapor──→ USER
         └─→ RESEARCHER ─┘
```

Her aşama bir öncekinin çıktısını doğrular; VERIFY başarısızsa ORCHESTRATOR
görevi ilgili role geri döndürür.

### §6.1 Persona Hiyerarşisi — Expert 5 / Senior 5 / Junior 10 (toplam 20)

Tartışma/inceleme turları (WORKFLOW §6: 3 tur / 20 persona) bu 20 persona ile
yürütülür. **Hiyerarşi: Expert > Senior > Junior.** Karar (evidence-öncelikli):
kanıt gücü > seviye; eşit kanıtta Expert > Senior > Junior; **oy çokluğu yoktur.**

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

**Tur kuralı (WORKFLOW §6)**: Tur 1 = her persona kendi önerisini yazar (kaynaklı);
Tur 2 = çapraz eleştiri (en az 1 karşı öneri); Tur 3 = uzlaşma + ADR. Uzlaşma yoksa
`VERIFICATION REQUIRED` + üst karar. Her öneri **gerekçe + dosya/satır kanıtı**
taşıır; kaynaksız öneri sayılmaz.

**Dispatch kuralı**: tek seviyeli karar bağlayıcı olamaz; mimari/katman/sayım
kararlarında en az 1 Expert + 1 Senior + 1 Junior görüşü zorunludur.

## 7. Execution Loop (her görevde)

0. SKILL EŞLEŞMESİ KONTROLÜ — kök CLAUDE.md §Skill Registry + available-skills
   listesi taranır; görev bir skill ile eşleşiyorsa İLK işlem Skill tool ile
   o skill'i yüklemektir (Skill Usage Mandate §2).
1. Kök `AGENTS.md` (bu dosya) okunur.
2. Görevle ilgili **yalnız** `.ai/` vault dosyaları okunur — DÜZELTME
   (2026-10-10): eski "`62 `.ai/wiki/` sayfası`" yönergesi geçersiz —
   **`.ai/wiki/` diskte YOK** (0 dizin). Konuya göre hedef: `.ai/RAG.md` §3
   (konu→dosya indeksi) → `.ai/keys.md` (keyword) → `.ai/index.md` (katalog).
   **ARAMA KURALI (2026-10-10):** `.ai/` gizli (nokta) dizindir — glob/grep
   aramasında `hidden: true` kullanılır (`hidden:false` → `.ai/**` ve
   `.ai/.decisions/**` sahte "0 sonuç" üretir, ADR'ler "görünmez" görünür);
   doğrudan `.ai` path'i vermek de geçerlidir.
3. **Yalnız ilgili** `.ai/.rules/` YOK — `.ai/.rules/` altında diskte **yalnız
   `senior-mode.md`** vardır (`error-recovery.md` YOK — bkz §7 adım 7).
   İlgili kural `.ai/CLAUDE.md` §6 LINT'ten okunur.
4. Hedef dosya okunur.
5. Kod yazılır.
6. `.ai/CLAUDE.md` §6 LINT kontrolleri çalıştırılır.
7. Hata → `.ai/CLAUDE.md` §6 LINT + `.ai/.rules/senior-mode.md` → düzelt → tekrar kontrol (error-recovery.md diskte MEVCUT DEĞİL).
8. UI değişikliği → browser testi (gerçek sayfa, eleman/layout doğrulaması).
9. Commit (subagent ATMAZ — orkestratöre aittir).

## 8. Prompt-maker Kuralı

Kullanıcı hammadde (raw) prompt gönderdiğinde **İLK** işlem `/prompt-maker`'dır:
questions modu çalışır; sağlayıcı/sabit model yok — OpenCode'un o an seçtiği model
kullanılır (free modellerde de çalışır).

Diğer tüm görev türleri için geçerli kural §7 Execution Loop adım 0'dır (Skill Usage Mandate).

Şablon çıktısı (sırasıyla):

1. eli10 paragraf açıklama
2. İstenen şey (ne isteniyor)
3. Vault referansları
4. Proje kod referansları
5. Kullanıcı kararları / cevapları
6. Orijinal prompt
7. Görev dökümü (task breakdown)
8. `Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: [n]`

Çıktı onaylanır → **sonra** session başlatılır.

## 9. On-Demand Vault Referansları (Ağır Detaylar)

| İhtiyaç | Dosya (`@` ile okunur) |
|---|---|
| Anayasa, guardrails, yasaklar | `@.ai/CLAUDE.md` |
| ADR kararları, mühendislik kısıtları | `@.ai/.decisions/index.md` (`.ai/raw/` dizini diskte YOK — 2026-10-12) |
| Kural dosyaları, LINT | `@.ai/CLAUDE.md` §6 · not: .ai/.rules/ yeni vault'ta YOK; error-recovery.md diskte hiçbir yerde MEVCUT DEĞİL |
| Agent registry, routing, handover | `.ai/AGENTS.md` (SSOT) · profil indeksi `@.ai/.agents/AGENTS.md` · profiller `@.ai/.agents/*.md` (11 dosya) · eski `.ai/wiki/vault-agents.md` ve `.ai/raw/AGENTS.md` **diskte YOK** (2026-10-10) |
| Vault-içi otorite | `@.ai/CLAUDE.md` §2 · not: `.ai.OLD/` dizini diskte YOK (2026-10-12), otorite sırası yalnız `.ai/CLAUDE.md` içindedir |
| Süreç, faz, hard gate | `@.ai/WORKFLOW.md` · eski `.ai/wiki/vault-workflow.md` ve `.ai/raw/WORKFLOW.md` **diskte YOK** (2026-10-10) |
| Retrieval — "hangi soru → hangi dosya" | `@.ai/RAG.md` (§3 indeks · §4 pipeline PLANNED — ADR-030) · keyword: `@.ai/keys.md` |

**Kural**: bu dosyalar yalnız görevin gerektirdiği anda okunur; boot'ta toplu
okuma, "tüm vault'u oku", "önce tüm .ai/ oku" talimatları geçersizdir.
Çelişkide `.ai/` kazanır (SSOT). Vault-içi dosya otorite sırası:
`.ai/CLAUDE.md` §2.1 (CLAUDE > AGENTS > WORKFLOW > brain > index > templates).

---

## 10. CSS Writing Rules (Templates)

Her CSS görevinden önce `.ai/.templates/frontend/` altındaki kanonik şablonlar okunur: `css-template.md` (v3.0.0, 11 katman) · `css-abstracts-token-template.md` · `css-component-template.md` · `css-page-template.md` · `css-device-template.md` · `css-auth-device-template.md` · `css-utility-template.md` · `css-helper-template.md`.

**Ölü atıf düzeltildi (2026-10-06):** `.ai/.templates/` **kökündeki** özet dosyalar `css-structure.md` (01→11 klasör sırası) · `css-token.md` · `css-component.md` · `css-page.md` · `css-imports.md` **çalışma ağacında diskte YOK (0 glob isabeti)** → beş referans kaldırıldı; özet içerik `css-template.md` içindedir. (`.ai/.templates/` kökü: `CLAUDE.md` · `coremusic-vault-template.md` · `index.md` · `session-log-template.md`. Not: bu beş dosya HEAD'de var, çalışma ağacından silinmesi **commit edilmedi** — `git status: D`.)

Sıra kuralı: 01_Abstracts(token) → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth.
Token kuralı: sabit değer yalnız `01_Abstracts/a-*.css` içinde `--cm-*` ile; `@media` içinde `var()` ile okuma yasak; ham hex/px katman dosyasına yazılmaz.
**Taşıma/silme YOK:** mevcut CSS dosyası adlandırması ve import zinciri değiştirilmez; yanlış yerleşim yalnız raporlanır (onay + ADR olmadan taşıma yasak).

## 13. Reading Order & Mockup Gate

**Kurallar:**

1. P0 → P1 → P2 → P3 sırasıyla okunur
2. Fallback: `index.md`
3. Token aşımı önlenir: gereksiz dosya okunmaz
4. **İstisna**: Görsel referanslar (`.ai/ui-design/screens/**`, `.ai/.png/**`)
5. **Şablon Zorunlu Okuma (Guardrail #16)**: `.ai/ui-design/**` içine dosya yazmadan ÖNCE `.ai/.templates/ui-design/` altındaki kalıp şablonu okunur — referans/tokens/index → `[[.templates/ui-design/reference-template]]` (Kalıp A) · flow → `[[.templates/ui-design/flow-template]]` (Kalıp B) · prompt → `[[.templates/ui-design/prompt-template]]` (Kalıp C) · screens → `[[.templates/ui-design/screen-spec-template]]` (Kalıp D). Şablonsuz ui-design dosyası üretilmez (§7.3).
6. **Gate Script Çalıştırma Yeri (Faz 7)**: `.ai/scripts/*.ps1` doğrulama scriptleri yalnızca **repo kökünden** (`C:\www\coremusic.net`) çalıştırılır — `screens-frontmatter-check.ps1` göreli yolları `.ai/` içinden çözemediği için **19 adet sahte "PNG yok"** üretir. 2026-09-29 kök ölçümü: frontmatter `21 ekran / 0 sorunlu` · `kalip-abc-check` `A:21·0 / B:21·0 / C:51·0 → GECTI` · `wiki-link-check` `227 link / 6 raporlanan` = **gerçek kırık 0** (6'sı §13.7 sahte).
7. **Bilinen 6 Sahte Kırık Link (düzeltme YASAK)**: `flow/auth/01-login.md [[C]]` · `flow/auth/04-select-gender.md [[V]]` · `flow/music/01-playback.md [[V]]` · `flow/settings/04-general.md [[V]]` · `reference/legacy-inventory.md [[link]]` ×2 (L225, ```` ```yaml ```` bloğu içinde). Bunlar şablon değişkeni/kaçış dizgesidir, gerçek kırık link değildir; "onarmaya" kalkışmak şablonu bozar. Sayım 6'da kalır ve kusur sayılmaz.

**Mockup Before Frontend**: CSS/HTML/JS/layout/bileşen görevlerinde `.ai/ui-design/` altındaki ilgili görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve bildir.

## 16. Değiştirilemez Kullanıcı Kuralları (Faz 7 kaydı — bağlayıcı)

1. **Widget grid kanonik kuralı** (Figma API çıktısından **önceliklidir**): `1024` → satır1 **2×2**, satır2 **1×5**, satır3 **1×5** = 12 slot · `1920` → satır1 **4×4**, satır2 **1×8**, satır3 **1×8** = 20 slot. Bu kurala aykırı sayım/render revize edilir, kural değil.
2. **İsimlendirme yasağı**: `ui-design/screens/T07-embedded/` dizin adı **DEĞİŞTİRİLMEZ** (~489 wiki-link buna bağlı); tier/ CatID ayrıştırması dosya yolu yerine **`CatID` sütunuyla** yapılır.
3. **Figma token SSOT**: `FIGMA_TOKEN` / `FIGMA_FILE_KEY` **yalnız** `.ai/.env.figma`'dan okunur (`.gitignore:69`); anahtar asla `.md` / `.json` / `.log` içine yazılmaz. `figma-extract.ps1` ve `figma-tokens.ps1` bu kuraldadır — betikte sabit anahtar yoktur.
4. **Dokunulmaz yüzeyler**: `.ai/.png/**` (salt-okunur SSOT) · frozen ADR'ler · `*.php` / `*.js` / `*.css` / `*.sql` kaynak kod (bu revizyon Markdown dışı **yazmaz**).
5. **Üretim ve kapanış**: yeni `.md` dosyaları `.ai/.templates/ui-design/` Kalıp A–D'ye uyar (Guardrail #16); **`git commit` subagent tarafından ATILMAZ — orkestratöre aittir**.

## 14. Boot Zinciri & Özet

**Boot = her session başında otomatik okunan dosya paketi.** Sıra (bağlayıcı):

| # | Dosya | Ne verir | Ne zaman |
|---|-------|----------|----------|
| 1 | [`AGENTS.md`](AGENTS.md) (bu dosya) | Ana kurallar: akış, zero-hallucination, execution loop, persona hiyerarşisi | Her zaman (harness boot'u) |
| 2 | [`CLAUDE.md`](CLAUDE.md) | Skill Registry (SSOT) · vault bağlantı tablosu · mimari referans · web doğrulama | **Her zaman** — bu dosyadan linklenir, atlanmaz |
| 3 | `@.ai/AGENTS.md` | Tam agent registry (11 agent routing — profil indeksi `.ai/.agents/*.md` 11 dosya) | Her görevde |
| 4 | `@.ai/` diğer dosyalar (CLAUDE/WORKFLOW/RAG/...) | Anayasa, süreç, retrieval | **Yalnız ihtiyaç anında** (`@` ile) |

**Çift yönlü bağlantı (her iki dosya birbirini bağlar):**
`AGENTS.md` → `CLAUDE.md` (Skill Registry + vault) · `CLAUDE.md` → `AGENTS.md` (ana kurallar SSOT) → ikisi tek boot paketidir, biri diğerinin yerine geçmez.