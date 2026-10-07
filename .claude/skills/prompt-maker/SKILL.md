---
name: prompt-maker
description: "Use when the user asks to create, expand, or improve a prompt — turns a rough request into a structured multi-part prompt with P0/P1/P2 clarification questions, PICCO framing, and quality/security checks. — Tetikleyiciler: 'prompt oluştur', 'prompt yaz', 'sistem promptu', 'system prompt', 'steering yaz', 'hook yaz', 'kural seti oluştur', 'MASTER PROMPT', 'cursor rules', 'claude rules', 'CLAUDE.md yaz', 'rules yaz', 'context engineering', 'PICCO', 'prompt template'."
license: MIT
metadata:
  version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: prompt-engineering
  tags: [red-team, truth-mode, human-mode, master-prompt-generation, picco-framework, context-engineering, executable-prompt-production, hallucination-prevention, coremusic-rule-compliance]
  updated: 2026-10-07
  previous-version: "11.2.0"
---

# PROMPT ENGINEERING MOTORU — Machine Instruction Manual

**Bu skill, CoreMusic'in ana prompt üretim motorudur.** Dağınık istekleri çalıştırılabilir,
üretime hazır master prompt'lara dönüştürür.

> **Format: `claude-skill-v3`** — tek `SKILL.md` (kurallar + akış) · derinlik `references/`
> (30 dosya, §6 indeksi) · çalışmış örnekler `examples/` (§7 indeksi). Boot'ta toplu okuma YASAK;
> references/ yalnız ihtiyaç anında okunur.

---

## 1. Genel Bakış

| Alan | Değer |
|------|-------|
| Girdi | Dağınık fikir, belirsiz istek, ham/raw prompt |
| Süreç | Araştırma → PICCO → P0/P1/P2 sorular → kalite + güvenlik kontrolü |
| Çıktı | Çalıştırılabilir MASTER PROMPT (5.000–50.000 karakter) veya 8 bölümlü cevap paketi |
| Çerçeve | PICCO (Persona, Instructions, Context, Constraints, Output) + Context Engineering |
| Otorite | SSOT: `.ai/CLAUDE.md` |

### 1.1 Zorunlu Akış — Raw Prompt Geldiğinde (2026-10-01)

1. Ham istek **HEMEN işlenmez** → önce questions modu: **P0** (engelleyici) / **P1** (kalite
   belirleyici) / **P2** (tercih). Sorular **proje bağlamına göre** üretilir — generic YASAK.
2. O an seçili sağlayıcı/model kullanılır — hard-coded model/sağlayıcı YASAK.
3. Çıktı şablonu (sıra zorunlu, 8 bölüm): `/eli10` analiz → istenen şey → vault referansları →
   proje kod referansları → kullanıcı kararları + soru/cevaplar → orijinal prompt (değiştirilmeden)
   → görevlere bölme → kapanış
   `✅ Prompt Cevaplarla İşlendi | Dil: Türkçe | Görev Sayısı: [n] | Cevap: [n]`
4. **Kapı:** Çıktı **onaylanır → SONRA** session başlar. Onaysız session start YASAK.
5. **Akış zinciri:** Kullanıcı Promptu → [1] Exploration Gate → [2] Exploration Context →
   [3] Prompt Maker (P0/P1/P2) → [4] Intent Router (tech stack confidence + vault filtreleme) →
   [5] Instruction (yalnız ilgili vault dosyaları, 50K budget) → [6] System Prompt.

---

## 2. Ne Zaman Kullanılır

| Kullan (tetikleyiciler) | Kullanma — yönlendir |
|--------------------------|----------------------|
| "prompt oluştur" / "prompt yaz" / "MASTER PROMPT" | Basit kod yazma isteği → ilgili domain agent |
| "system prompt" / "sistem promptu" / "prompt template" | Mevcut prompt okuma/açıklama → doğrudan cevap |
| "CLAUDE.md yaz" / "rules yaz" / "cursor rules" / "claude rules" | Tek dosya düzeltmesi → doğrudan aksiyon |
| "steering yaz" / "hook yaz" / "kural seti oluştur" | Prompt üretimi yoksa skill yükleme |
| "context engineering" / "PICCO" | |

---

## 3. PICCO Çerçevesi

| Element | Ne tanımlar | Örnek |
|---------|-------------|-------|
| **P**ersona | KİM — rol, uzmanlık, ton | "You are a senior PHP architect..." |
| **I**nstructions | NE YAPILACAK — görev, adımlar, gereksinimler | "Analyze this code and suggest..." |
| **C**ontext | ARKA PLAN — amaç, kitle, alan, örnekler | "This is for a music streaming platform..." |
| **C**onstraints | SINIRLAR — hard + soft kural, sınırlar | "Never use ORM, always PDO..." |
| **O**utput | FORMAT — yapı, uzunluk, stil, şema | "Return as JSON with these fields..." |

- **Çatışma çözüm sırası (öncelik):** Constraints (asla ihlal edilmez) > Instructions > Persona >
  Context > Output.
- **Context rolleri (Context Engineering 2026):** Authority (bağlamda ilk) → Exemplar (hedef
  veriden önce) → Constraint (görevle birlikte) → Rubric (görev tanımından sonra) → Metadata (son).
- **Prompt yapısı:** SYSTEM PROMPT (PICCO) → USER INPUT (görev verisi) → OUTPUT EXPECTATION.

---

## 4. 10 Adımlık Workflow (özet)

1. **Load Context** — vault dosyalarına yalnız ihtiyaç anında `@` ile başvur.
2. **Research** — web araştırması: basit 5 / orta 20 / karmaşık 50 kaynak → kanıt + güven skoru
   (protokol: `references/10-web-research-protocol.md`).
3. **Sorular** — asla varsayma; %95 emin olana dek döngü: sor → bekle → doğrula → tekrar.
   Soru kaynağı: `references/02-question-bank.md` + §1.1 P0/P1/P2.
4. **Intent Analizi** — kullanıcının GERÇEK isteği (söylenen ≠ istenen), domain, risk, karmaşıklık.
5. **Constraint Doğrulama** — hard rules: PHP strict_types · Vanilla JS (ADR-001) · OWASP Top 10
   2025 · ITCSS + `--cm-*` · PDO parameterized (ADR-002) · ADR zorunlu. Soft: performans hedefleri,
   ölçeklenebilirlik, kod stili (müzakere edilebilir).
6. **Mimari Karar** — stack/pattern/araç; gerekirse ADR → `.ai/.decisions/accepted/`.
7. **Üretim** — 15 bölümlük şablon (§5), 5.000–50.000 karakter; her satır sistem davranışını
   tanımlar. Referans: `references/19-master-prompt-full-example.md`.
8. **Kalite Kontrol** — 8 kategori (Completeness/Consistency/Production-Ready/Security/
   Scalability/Clarity/Depth/Documentation); ağırlıklı toplam **≥85/100**, Security **≥90/100**.
   Rubrik: `references/09-quality-scoring-rubric.md` · motor: `references/validation-engine.md`.
9. **Kaydet** — `.ai/prompts/{date}-{slug}.md` + `.ai/brain.md` karar kaydı.
10. **Log** — `.ai/log.md` (timestamp + aksiyon + sonuç).

---

## 5. 15 Bölümü Master Prompt Şablonu (özet)

| # | Bölüm | # | Bölüm | # | Bölüm |
|:-:|-------|:-:|-------|:-:|-------|
| 1 | Persona & Role | 6 | Soft Rules (Guidelines) | 11 | Quality Standards |
| 2 | Activation Conditions | 7 | Workflow & Process | 12 | Examples & Exemplars |
| 3 | Instructions & Task | 8 | Domain Rules | 13 | Edge Cases |
| 4 | Context & Background | 9 | Security Rules | 14 | Troubleshooting |
| 5 | Hard Rules (Constraints) | 10 | Output Format | 15 | Version & Approval |

Tam şablon: `references/19-master-prompt-full-example.md` · Çıktı/şekil kararları:
`references/05-output-templates.md` · 2026 teknik katalogu (yaşayan/ölen teknikler, model
diyalektleri): `references/17-prompt-engineering-deep.md`.

---

## 6. Zorunlu Okumalar (references/ — 30 dosya)

> Yalnız ihtiyaç anında oku; boot'ta toplu okuma YASAK. Gruplar tematiktir.

| Dosya | Ne zaman okunur |
|-------|-----------------|
| **▸ Temel Çerçeve** | |
| `references/01-prompt-types-deep.md` | Prompt türü seçimi belirsizse (16 tür ayrımı) |
| `references/02-question-bank.md` | Questions modu — P0/P1/P2 soru bankası (3.820+ soru) |
| `references/17-prompt-engineering-deep.md` | Teknik derinlik — 2026 katalog, meta-prompting |
| `references/21-glossary-and-references.md` | Terim/kaynak belirsizse |
| `references/INDEX-SYNC.md` | references/ senkronizasyon/PICCO eşleşmesi kontrolü |
| **▸ Güvenlik & Kalite** | |
| `references/03-security-owasp-full.md` | Güvenlik prompt'unda — OWASP tam liste |
| `references/09-quality-scoring-rubric.md` | §4.8 kalite puanlama rubriği |
| `references/18-security-deep-dive.md` | Injection derin dalma — güvenli prompt tasarımı |
| `references/validation-engine.md` | Step 8 doğrulama motoru — eşik/akış kontrolleri |
| **▸ Alan Kuralları** | |
| `references/04-language-standards-full.md` | Polyglot prompt'ta — dil/dilbiçim standartları |
| `references/06-deep-domain-rules.md` | Domain prompt'unda derin kurallar |
| `references/11-coremusic-deep-rules.md` | CoreMusic hard rules derinlemesine |
| `references/12-performance-testing-devops.md` | Performans/test/DevOps prompt'unda |
| `references/13-uiux-accessibility.md` | Frontend prompt'unda — UI/UX + WCAG erişilebilirlik |
| `references/14-embedded-audio-electronics.md` | Donanım/ses prompt'unda — gömülü elektronik |
| `references/15-api-design-patterns.md` | API prompt'unda — tasarım kalıpları |
| `references/16-database-design-patterns.md` | DB prompt'unda — şema/BCNF kalıpları |
| `references/22-nodejs-typescript-patterns.md` | Download service (Node/TS) prompt'unda |
| `references/23-csharp-dotnet-patterns.md` | C#/.NET domain prompt'unda |
| `references/24-ml-ai-patterns.md` | AI service (ML/AI) prompt'unda |
| `references/25-fintech-payment-patterns.md` | Ödeme/fintech prompt'unda |
| **▸ Şablon & Çıktı** | |
| `references/05-output-templates.md` | Format/şema kararı — JSON/XML çıktı standartları |
| `references/08-full-example-sessions.md` | Kalibrasyon — tam örnek oturumlar |
| `references/19-master-prompt-full-example.md` | Step 7 üretim referansı — tam master prompt |
| **▸ Derinlik** | |
| `references/07-architecture-patterns.md` | Mimari karar prompt'unda — katman/pattern derinliği |
| `references/20-kiro-hooks-steering-deep.md` | "steering yaz" / "hook yaz" — Kiro hooks derinlemesine |
| **▸ Motor** | |
| `references/00-agentic-orchestrator-layer.md` | Multi-agent prompt akışında — orkestratör katmanı |
| `references/10-web-research-protocol.md` | Step 2 Research — kaynak/eşik protokolü |
| `references/multi-agent-patterns.md` | Orkestrasyon prompt'unda — multi-agent kalıpları |
| `references/changelog.md` | Sürüm geçmişi (v1.0.0 → v3.0.0) |

---

## 7. Örnekler (examples/)

| Dosya | Ne gösterir |
|-------|-------------|
| `examples/raw-to-structured-prompt.md` | **Tam çalışmış örnek:** ham Türkçe istek → P0/P1/P2 → 8 bölümlü yapılandırılmış prompt (PHP 8.4, music panel, `coremusic_social`) |
| `examples/question-bank-in-use.md` | Soru bankasının (`references/02`) örnek görev üzerinde süzülmesi: Block A → P0/P1/P2 |
| `examples/raw-to-master-prompt.md` | Ham istek → questions modu → 8 bölümlü çıktı (§1.1 Zorunlu Akış uygulaması) |
| `examples/ddd-cqrs-discovery.md` | DDD/CQRS dallı keşif — adaptif soru ağacı, [UNKNOWN] yönetimi |

---

## 8. Otorite & Vault Bağlantıları

- **Otorite (SSOT):** `.ai/CLAUDE.md` · Vault: `.ai/AGENTS.md` · `.ai/WORKFLOW.md` ·
  `.ai/brain.md` · `.ai/index.md`
- **Şablon/ADR/Agent:** `.ai/.templates/index.md` · `.ai/.decisions/` · `.ai/.agents/AGENTS.md`
- **İlgili skill'ler:** `truth-engine` (global, doğrulama hattı) · `human-mode` · `vault-sync-post`
  (`.claude/skills/` altında; `.opencode/skills/` 2026-10-07'de boş)
- **Güncelleme politikası:** mevcut yapı korunur; prompt formatı / PICCO elementi / teknik katalog
  değişikliği kullanıcı onayı ister (Human Approval Gate).

---

## 9. Güvenlik & Truth Mode

- **Prompt injection savunması:** direct / role-play / indirect / context overflow türlerine karşı
  — XML etiketleriyle bölüm ayırma, kullanıcı verisini system prompt'a gömme, çıktı şema
  doğrulama, en az yetki (prompt kod/credential/gerçekleştirme açığı vermez). Derinlik:
  `references/18-security-deep-dive.md` + `references/03-security-owasp-full.md`.
- **Zero-Hallucination skorlama:** her teknik iddia atomik iddiaya ayrılır — 90–100 VERIFIED ·
  60–89 `⚠️ VERIFICATION REQUIRED` · <60 REJECTED. Pipeline: `truth-engine` skill (global)
  (Validator → Auditor → Integrator → Red Team 3-way).
- **CoreMusic hard rules:** PHP `strict_types` · Vanilla JS (ADR-001) · PDO, ORM yasak (ADR-002)
  · `SELECT *` yasak · hardcoded credential yasak · `csrf_token` · ITCSS + `--cm-*`.
  Derinlik: `references/11-coremusic-deep-rules.md`.
- **Modlar:** Red Team · Truth Mode · Human Mode aktif; bilinmeyen = UNKNOWN.

**Anti-overthink: kök `AGENTS.md` §5 (MAX THINKING 7 madde) geçerlidir.**

---

*Framework: PICCO (Persona, Instructions, Context, Constraints, Output) · Authority: Vault Steward / AI Orchestrator*
*Mandatory for all prompt generation — No exceptions*
*CoreMusic Skill v3.0 — metadata.version: 3.0.0 — Updated: 2026-10-07*