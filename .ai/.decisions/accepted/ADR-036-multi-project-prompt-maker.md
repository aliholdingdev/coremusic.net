---
id: ADR-036
title: Multi-Project Prompt Maker — Proje Profili + Ortak Çekirdek + Adapter Katmanı ve Paylaşılabilir Prompt Seti (prompt-maker çekirdeğinin proje arası taşınabilir formu)
type: adr
category: ai
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-036 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Vault Steward", "Master Orchestrator"]
consulted: ["Backend Architect", "UI Designer", "DevOps Engineer"]
informed: ["QA Engineer", "Security Engineer"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-035-system-prompt-engineering.md]]"
  - "[[.ai/.decisions/accepted/ADR-024-ecosystem-modular-docs.md]]"
  - "[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]"
  - "[[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]]"
---

# ADR-036: Multi-Project Prompt Maker — Proje Profili + Ortak Çekirdek + Adapter + Paylaşılabilir Prompt Seti

**Durum:** accepted (Draft → Review → Active → **Active**; frozen YOK)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı karar kapsamı) + Master Orchestrator (AI domain)
**İlgili ADR'ler:** [[.ai/.decisions/accepted/ADR-035-system-prompt-engineering.md]] (prompt SSOT + 5 katmanlı iskelet — bu kararın çekirdek hattı) · [[.ai/.decisions/accepted/ADR-024-ecosystem-modular-docs.md]] (doküman sahipliği + 178 CLAUDE.md katman bulgusu) · [[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]] (kapılı/değişmez sözleşme + sürüm disiplini) · [[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]] (AI stratejisi, prompt kod içi gömme yasağı)

---

## 1. Bağlam ve Kod Kanıtı

Bu ADR, CoreMusic **prompt-maker** varlığının çoklu proje çağında nasıl yapılandırılacağını tek kararla yazar: her projenin kendi kuralları/şablonları/teknolojisi olduğu bir **proje profili**, prompt-maker'ın **sabit çekirdeği** (workflow + research protocol + şablon iskeleti), proje farklarını karşılayan **adapter katmanı** ve proje arası taşınabilir bir **paylaşılabilir prompt seti**. Kanıt 2026-09-25'te kod + vault taramasıyla derlendi ve **IMPLEMENTED** (diskte/kodda var) / **PLANNED** (karar olarak kurulan, karşılığı henüz yok) olarak etiketlendi.

### 1.1 Mevcut Durum (kod + vault kanıtı)

**A) prompt-maker skill — IMPLEMENTED (ama tek proje bağlı):**

| Varlık | Kanıt (dosya / boyut) |
|--------|----------------------|
| `.claude/skills/prompt-maker/SKILL.md` | **20679 bayt**; kök dizin 2 dosya (`SKILL.md` + `CLAUDE.md`) |
| `references/` klasörü | **30 dosya** — `00-agentic-orchestrator-layer.md` … `25-fintech-payment-patterns.md` + `10-web-research-protocol.md` (5642 b) + `INDEX-SYNC.md` + `CLAUDE.md` + `multi-agent-patterns.md` + `validation-engine.md` |
| `.archive/` klasörü | **31 dosya** (eski soru-blok raporları, COMPLETION/VALIDATION raporları) |
| SKILL.md bölüm yapısı | `§0 IDENTITY & SCOPE` `:78` · `§1 PICCO FRAMEWORK` `:120` · `§2 WORKFLOW (10 Steps)` `:175` · `§3 MASTER PROMPT TEMPLATE (15 Sections)` `:280` · `§4 TECHNIQUE CATALOG (2026)` `:356` · `§5 COREMUSIC RULES` `:413` · `§6 HALLUCINATION CONTROL` `:455` · `§7 SECURITY & INJECTION DEFENSE` `:493` · `§8 QUALITY CONTROL` `:547` · `§9 TROUBLESHOOTING` `:582` · `§10 REFERENCES & VERSION` `:608` |

→ Yapı gerçeği: **progressive disclosure** düzeni (kök = iskelet, `references/` = 3. seviye kaynak) IMPLEMENTED; fakat `§5 COREMUSIC RULES` `:413` ve `§11-coremusic-deep-rules.md` gibi referanslar çekirdeği **CoreMusic'e gömmüş** durumda — çekirdek/proje ayrımı yok. Bu, ADR-035'in "30 referanslı skill" bulgusuyla aynı varlıktır.

**B) Proje profili katmanı — IMPLEMENTED (CoreMusic için, çoklu proje değil):**

| Katman | Dosya | Kanıt |
|--------|-------|-------|
| Repo kökü | `CLAUDE.md`, `WORKFLOW.md`, `README.md` | kök dosya listesi |
| Vault kökü | [[.ai/CLAUDE.md]] (ana sözleşme), [[.ai/AGENTS.md]] (**v22.0.3 — agent registry SSOT**), [[.ai/WORKFLOW.md]] | dosya varlığı + frontmatter |
| Yaygın CLAUDE.md ağı | repo geneli **178 `CLAUDE.md`** | recursive sayım = 178 (ADR-024 bulgusu **doğrulandı**) |
| Proje envanteri | [[.ai/PROJECTS.md]] §7.1 yazılım (NevaEngine, NevaPlayer…) · §7.2 donanım · §7.3 altyapı | envanter kataloğu |

→ Proje profili bugün **tek proje (CoreMusic) için var**: kurallar, şablonlar ve teknoloji stack'i aynı vault içinde yaşıyor; ayrı bir "profil config" dosyası yok (PLANNED).

**C) Çekirdek denemesi kanıtı — `.ai/archives/prompt*` (IMPLEMENTED dosya, tek proje içi):**

| Dosya | İçerik | Yorum |
|-------|--------|-------|
| `prompt0-genel-ana-prompt-{2026-08-15,2026-09-01}.md` · `prompt1-spa-router-*` · `prompt2-auth-*` · `prompt3-api-*` | 8 dosya (4 prompt × 2 tarih) | proje-içi prompt seti (AGENTS.md §14.1 eşlemesi) |
| [[.ai/archives/prompt-unified-2026-08-15.md]] | `title: "CoreMusic - Unified Prompt (Birleşik Prompt)"`, `*CoreMusic Unified Prompt v1.0.0*` | **çekirdek = evet**: 4 prompt'un tek gövdede birleştirilmesi denemesi (tek seferlik, tek proje) |
| [[.ai/archives/prompt-shared-base.md]] | `title: "CoreMusic — Prompt Shared Base (Ortak Temel)"`, `type: prompt-base`, `category: shared`, `*Prompt Shared Base v2.0.0*`, `:39` ADR-035 referansı, taban yasak/kural tabloları (ADRsiz "shared" kural listesi) | **çekirdek = evet**: ortak taban + üstüne proje kuralı ekleme modelinin ilk denemesi (tek proje) |

→ `unified`/`shared-base` = **mevcut çekirdek denemesi kanıtı (IMPLEMENTED)**; ikisi de CoreMusic adını taşıdığı için **çoklu proje çekirdeği değildir** — çoklu proje için çekirdek çıkarımı bu ADR ile kurulur (PLANNED).

**D) Çoklu proje dizini — YOK (dürüst bulgu):**

| İddia | Durum | Kanıt |
|-------|-------|-------|
| Repo içinde ikinci bir proje dizini | ❌ **YOK** | kök dizin listesi: `.ai`, `.claude`, `.github`, `.opencode`, `.workflows`, `shared`, `assets.coremusic.net`, `auth.coremusic.net`, `home.coremusic.net` → üç "alt alan adı" klasörü de **aynı projenin (CoreMusic) parçaları** |
| Çoklu proje envanteri | 🟡 **HEDEF (PLANNED)** | [[.ai/PROJECTS.md]] §7 kataloğu (yazılım + donanım + altyapı) — envanter, ayrı repo/değil |
| Cross-proje hafıza | 🟡 **PLANNED** | [[.ai/AGENTS.md]] §19 yol haritası: `v23.0 — Cross-Project Memory (WirelessConnect)` |

→ Bu ADR'nin "çoklu proje" iddiası bu nedenle **mimari karar olarak IMPLEMENTED, uygulama olarak PLANNED**'dir; ikinci bir proje repoya geldiğinde profil + adapter ile bağlanır.

**E) ADR-036 slotu — numara ayrılmış (boşluk-doldurma kanıtı):**

- [[.ai/.decisions/index.md]] `:73` → `ADR-036-multi-project-prompt-maker | Multi-Project Prompt Maker | AI`
- Aynı slot 5 dosyada daha: [[.ai/index.md]] `:653` · [[.ai/keys.md]] `:188` (keyword satırı) + `:271` · [[.ai/brain.md]] `:991` ("Çoklu proje prompt üretimi") · [[.ai/.templates/adr/adr-index.md]] `:107` (`🟠 process`, `adr-template.md ⚠️`)
- Aynı "ayrılmış numara" usulü ADR-030/033/034/035'te de uygulanmıştı; bu nedenle "yeni ADR'ler 088+" kuralının istisnasıdır (Aynı numara-boşluğu-doldurma usulü: ADR-035 §1.1-F).

### 1.2 Sorun Tanımı

1. **Çekirdek proje bağlı:** prompt-maker çekirdek işlevleri (workflow, research protocol, iskelet) ile CoreMusic kuralları aynı dosyada (`SKILL.md §5` `:413`) duruyor — çekirdek başka projeye taşınınca CoreMusic kuralları da taşınıyor (yanlış) ya da çekirdek kirletiliyor (yanlış).
2. **Profil yok:** her projenin kendi kuralları/şablonları/teknolojisi için tanımlı config dosyası yok — proje bilgisi `CLAUDE.md` ağına (178 dosya) dağılmış, tek noktadan okunamıyor.
3. **Farklar için katman yok:** teknoloji stack, dil ve kural farkları "ya her şey ortak ya da tamamen ayrı" ikileminde; bu, fork (bakım iki katı) veya tek merkez (proje özgüllüğü kaybı) demek.
4. **Taşınabilir prompt seti yok:** `.ai/archives/prompt*` (10 dosya) tek projeye ait; proje arası kopyalanabilir, sürümlenmiş bir prompt seti tanımı yok (ADR-035 §1.1-E konsolidasyon şartı bu ADR'nin hedefiyle kesişir).
5. **Çoklu proje uygulaması kanıtsız:** repo'da ikinci proje dizini yok (§1.1-D) — karar, ikinci proje gelmeden **iskeleti kurmak** zorunda; yoksa proje geldiğinde fork başlar.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | shared prompt library reuse across multiple projects 2025 best practices · agent skills plugin architecture project-specific configuration layering 2026 · AGENTS.md CLAUDE.md project-specific instruction files layered global-to-project configuration 2026 · configuration layering base profile project overrides inheritance pattern monorepo shared core per-project adapter · reusable prompt templates portability across repositories "cross-project" AI agent 2026 |
| Web Search **Konusu** | Paylaşılan prompt kütüphanesi ve prompt reuse; skill/plugin mimarisi (SKILL.md + progressive disclosure); proje bağlam config dosyaları (CLAUDE.md/AGENTS.md katmanları); config layering/inheritance (base → override → adapter); proje arası taşınabilirlik (canonical-then-fan-out, shared base + per-project seed) |
| Web Search **Bağlam** | prompt-maker `SKILL.md` (20679 b) + 30 referans çekirdeği CoreMusic'e gömlü (§1.1-A); proje profili 178 CLAUDE.md ağına dağılmış (§1.1-B); `.ai/archives/prompt-unified` + `prompt-shared-base` tek proje içi çekirdek denemesi (§1.1-C); repo'da ikinci proje dizini yok (§1.1-D) → çekirdek/proje ayrımı kurulmadan çoklu proje başlayamaz |
| Web Search **Kısa Açıklama** | 5 sorgu 2026-09-25'te çalıştırıldı; **40 kaynak** derlendi (arXiv agent-skills survey, Anthropic Agent Skills ekosistem yazıları, AGENTS.md/CLAUDE.md katman dokümanları, prompt library mimarileri, monorepo config package'ları, cross-proje base-layer repoları); tümü "ortak çekirdek + proje katmanı + sürümlü paylaşım" yönünde oybirliği |
| Web Search **Uzun Açıklama** | (a) Prompt reuse: prompt library = organize + tag + version + governance; "prompt'ları kod gibi yönet" (registry, semver, changelog, eval) — flat dağınık kütüphaneler "graveyard" ilan ediliyor; paylaşılan kütüphane git-backed olup prompt drift'i önlüyor. (b) Skill/plugin mimarisi: SKILL.md frontmatter (routing) + gövde (payload) ayrımı, 3 seviyeli progressive disclosure (metadata → talimat → `references/`), skill = bağımsız dizin; skill'ler "her şey bir plugin" ve hub-and-spoke paylaşımıyla çoğaltılıyor. (c) Proje context config: CLAUDE.md katmanlı (managed → user global → project root → local → subdirectory → path-scoped rules) ve `@import` ile birleştirme; AGENTS.md açık standart (60+ araç) ve "en yakın dosya kazanır" kuralı — evrensel olan ortada, proje özgü olan yerelde. (d) Config layering: base config + `extends` (turborepo/zap-studio/layer-pack), katmanlı precedence (defaults → app → host → user), "tek merkez + yerel override" monorepo kalıbı. (e) Proje arası taşıma: canonical-then-fan-out (tek kanonik kaynak → araç-özel çıktı), global engine + per-project seed (agentic-base-layer), core → organization → project üç katmanı (Rosetta), shared base + adapter (playbook template: `rules/global`, `rules/domain`, `project/`, öncelik Project → Domain → Global). |
| Web Search **Paragraf Veri Uzun** | Araştırma dört karar zeminini besledi: (1) **Proje profili** zorunlu — her proje context dosyası kendi kurallarını tutar, aksi halde araçlar birbirine çelişen kurallar uygular (pnpm/npm örneği); (2) **Ortak çekirdek** zorunlu — paylaşılan standartlar (workflow, research protocol, iskelet, güvenlik kuralları) merkezde durur, "değişiklik bir projede değişiyorsa her yerde değişmeli" sınırı; (3) **Adapter katmanı** zorunlu — fork yerine inheritance/override: çekirdek değişmez, farklar ayrı dosyada üstüne biner; (4) **Paylaşılabilir prompt seti** zorunlu — sürüm + changelog + eval olmadan kopyalama/drift kaçınılmaz; ters yönde (fork, tek merkez, dağınık kopya) hiçbir kaynak önermiyor. |
| Web Search **Sonucu** | 40 kaynağın tamamı aynı yönde: ortak çekirdek + proje/proje-özel katman + adapter/override + sürümlü paylaşım; "fork et", "tek dosyaya göm", "her proje kendi kopyasını yazsın" diyen ters kaynak yok; çelişkili iddia tespit edilmedi. |
| Web Search **Alınan Karar** | §2 (a)-(d): (a) proje profili config dosyası · (b) sabit prompt-maker çekirdeği (workflow + research protocol + şablon iskeleti) · (c) adapter katmanı (teknoloji stack, dil, kurallar) · (d) paylaşılabilir prompt seti (proje arası taşınabilir) — mimari: **common ancestor = çekirdek; fork DEĞİL, tek merkez DEĞİL** |
| Web Search **Sonuç** | Araştırma ile karar uyumu: **9/9 alan tek yönlü, 40 kaynak / 5 sorgu (2026-09-25)**; §4.3 riskleri bu kaynakların da vurguladığı risklerle (profil drift, çekirdek çatışması, sürüm/fork şişmesi, kullanılmayan kütüphane) örtüşüyor |

**Kaynaklar (40):** arXiv "Agent Skills for Large Language Models: Architecture…" (2602.12430) · callsphere.ai "Inside Claude Code Skills" · agentpatterns.ai "Architecting a Central Repo for Shared Agent Standards" · agentpatterns.ai "Project Instruction File Ecosystem" · agentpatterns.ai "CLAUDE.md Convention" · opspresso/agent-plugins · ivanzwb/agent-skills · richfrem/agent-plugins-skills · KonstantinData/skill-centric-agent-system · a2aprotocol.ai "DeepSeek Harness 2026" · thepromptshelf.dev "AGENTS.md vs CLAUDE.md (2026)" · thepromptshelf.dev "AGENTS.md Best Practices" · agentscli.com "Rules" · tianpan.co "CLAUDE.md and AGENTS.md: The Configuration Layer" · docs.kanaries.net "Claude Code Reads AGENTS.md Now" · agentguides.dev "Configuring AI Agents with CLAUDE.md" · itbrew.com "Best practices for building a prompt library" · blog.ergonis.com "Prompt management guide" · blog.paulserban.eu "Architecting a Scalable Prompt Library" · blog.paulserban.eu "Versioned, Testable, Model-Agnostic Prompt Library" · amitkoth.com "The prompt library that changed our productivity" · theneuralbase.com "Shared prompt library" · nucamp.co "How to Use Prompt Libraries Effectively in 2025" · Gainsight "Manage Prompts Using the Prompt Library" · microsoft/amplifier-profiles `docs/DESIGN.md` (profiles + inheritance + layering) · DeepWiki zap-studio/monorepo "Shared Configuration Packages" · turborepo.dev "Package Configurations" · layer-pack/layer-pack (DOC + README) · dev.to "Sharing Configurations Within a Monorepo" · bitranox/lib_layered_config (6 katmanlı precedence) · lorion-org/lorion · griddynamics/rosetta (core → organization → project) · Elad73/agentic-base-layer (global engine + project-seed) · screenleon/agent-playbook-template (`rules/global` + `rules/domain` + `project/`) · fending/context-engineering (cascading files: global > project > subdirectory) · yelmuratoff/agent_sync (tek kaynak → 13 araç, `shared:` inheritance) · HelloWorldSungin/AI_agents (cross-proje tool selector) · gosha70/code-copilot-team (`shared/` → adapters/ generate) · Alaslani/AI-Framework (copy templates + cross-project patterns)

> Protokol: [[.claude/skills/prompt-maker/references/10-web-research-protocol.md]] (5642 b) — 5 sorgu bu protokolle çalıştırıldı.

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Vault SSOT | Çekirdek ve profil tanımları `.ai/` altında yaşar; kod içi gömülü prompt/kural yasak — ADR-030/035 hattı |
| In-Place Refactoring | Dosya adları onaysız değişmez (`.claude/skills/prompt-maker/*` ve `.ai/archives/prompt*` yerinde kalır; refactor = içerik taşıma, yeniden adlandırma yok) |
| Frozen dokunulmazlık | ADR-001…037 frozen metinleri okunur/referanslanır, değiştirilmez; bu dosya frozen değil |
| Tek proje gerçeği | Repo'da ikinci proje dizini yok (§1.1-D) — çoklu proje maddeleri PLANNED etiketi taşır; uydurma proje yolu yazılmaz |
| ADR-035 hattı | Çekirdek iskelet ADR-035 §2a ile çelişemez; profil/adapter katmanı 5 katmanlı iskeleti bypass edemez |
| Yazım usulü | Vault dosyaları yalnız `vault-utf8-writer.mjs` ile yazılır; şablon zorunlu (Guardrail #16); `log.md` yalnız append |
| REDACTED | Secret/credential hiçbir koşulda profil config'e veya prompt setine yazılmaz |

---

## 2. Karar (Decision)

CoreMusic'te prompt üretimi **dört maddelik** yapı ile standartlaştırılır (kullanıcı onaylı kapsam); hepsi `.ai/` Vault SSOT altındadır:

- **(a) Proje profili:** her proje kendi kurallarını, şablonlarını ve teknolojisini tek bir profil config dosyasında tanımlar (stack, dil, kurallar, şablon referansları, sahipler).
- **(b) Ortak çekirdek:** prompt-maker çekirdeği — workflow (10 adım), research protocol (5 sorgu, §1.3 usulü) ve şablon iskeleti (master prompt 15 bölüm / ADR-035 5 katman) — **proje bağımsız ve sabittir**.
- **(c) Adapter katmanı:** proje farkları (teknoloji stack, dil, proje kuralları) çekirdeğin **üstüne binen ayrı bir katman** olarak yaşar; çekirdek bunları bilmez.
- **(d) Paylaşılabilir prompt seti:** çekirdek + adapter'dan üretilen prompt seti **proje arası taşınabilir**dır (sürümlü, changelog'lu, ADR kapılı).

**Mimari:** common ancestor = workflow + research protocol (sabit). **Fork DEĞİL** (iki kopya = bakım iki kat), **tek merkez DEĞİL** (proje özgüllüğü kaybolur) — ikisi de §3'te reddedildi.

### 2.1 Neden Bu Seçenek?

Çünkü bugün eksik olan prompt üretimi değil **sınırlar**: 30 referanslı skill, 10 arşiv promptu ve 178 CLAUDE.md var — ama hangisinin çekirdek, hangisinin proje-özel olduğu yazılı değil. Web araştırması (§1.3, 40 kaynak) üç şeyde oybirliği veriyor: evrensel standart merkezde, proje bilgisi yerelde, ikisini bağlayan katman inheritance/override olarak sürümle yönetilir. ADR-024 doküman sahipliği bu ayrımı vault için kurmuştu; ADR-035 prompt iskeletini standartlaştırmıştı — bu ADR eksik halkayı (proje sınırı) tamamlar. Seçenek, mevcut dosyaları yeniden adlandırmadan, sıfır yeni altyapıyla kurulan bir **katman kuralıdır**; ikinci proje geldiğinde iskelet hazır olur.

### 2.2 Teknik Detaylar

**2.2-a Proje profili (§2a):**

- Hedef dosya: `.ai/profiles/<proje>/project-profile.md` (PLANNED — repo'da `profiles/` dizini bugün YOK); ilk profil CoreMusic için `project-profile-coremusic.md` olur.
- Zorunlu alanlar (7): `stack` (teknoloji listesi), `language` (dil/kod standardı), `rules` (proje kuralları — CLAUDE.md/AGENTS.md karşılığı), `templates` (şablon referansları, Guardrail #16), `owners` (sahip agent — ADR-024 eşlemesi), `prompts` (bu projenin prompt seti), `version` (semver).
- Okuma sırası: çekirdek → profil → adapter (§24.2 boot listesine profil dosyası eklenir); çelişkide **daha özgü olan kazanır** (web bulgusu: CLAUDE.md katman önceliği).
- Kanıt bağlantısı: bugün bu bilgi [[.ai/CLAUDE.md]] + [[.ai/AGENTS.md]] (v22.0.3) + [[.ai/WORKFLOW.md]] + 178 `CLAUDE.md` ağına dağılmış (§1.1-B) — profil bunların **giriş kartıdır**, yerini almaz (SRP).

**2.2-b Ortak çekirdek (§2b):**

| Çekirdek parça | Kaynak (bugün) | Durum |
|----------------|----------------|-------|
| Workflow (10 adım: Load Context → Research → … → Save/Log) | `SKILL.md §2` `:175-276` | IMPLEMENTED (çekirdek sayılmıştır) |
| Research protocol (5 sorgu, 9 alan §1.3, kaynak listesi) | `references/10-web-research-protocol.md` (5642 b) | IMPLEMENTED |
| Şablon iskeleti (master prompt 15 bölüm ↔ ADR-035 5 katman) | `SKILL.md §3` `:280-352` | IMPLEMENTED |
| Kalite/hallucination/enjeksiyon kural katmanı | `SKILL.md §6-§8` `:455-580` | IMPLEMENTED |
| CoreMusic'e özgü kurallar | `SKILL.md §5` `:413` + `references/11-coremusic-deep-rules.md` | **PROJE KATMANINA TAŞINACAK (PLANNED refactor)** |

Kural: çekirdek dosyalarında proje adı, proje teknolojisi ve proje kuralları **geçmez**; geçen her satır adapter'a taşınır. Çekirdek sürümü ayrı semver taşır (`core v<major>.<minor>`).

**2.2-c Adapter katmanı (§2c):**

- Adapter = profil config'ten türeyen **fark bloğu**: `stack`, `language`, `rules` alanları çekirdeğe üstüne eklenir; `overrides` (proje kurallarının çekirdek kuralı ezdiği satırlar) açıkça listelenir.
- Öncelik (web bulgusuyla hizalı): **proje > domain > global (çekirdek)** — skill/playbook template kalıbı ile aynı.
- Mevcut kanıt: [[.ai/archives/prompt-shared-base.md]] (`type: prompt-base`, `category: shared`, v2.0.0) = "ortak taban + proje kuralı" adapter'ının ilk provası (IMPLEMENTED, tek proje); `shared-base` içindeki yasak/kural tabloları adapter bloğu olarak okunur.
- Adapter dosyası: `.ai/profiles/<proje>/adapter.md` (PLANNED) — çekirdek dosyalara gömülmez (In-Place Refactoring: dosya adı değişmez, içerik katmanlanır).

**2.2-d Paylaşılabilir prompt seti (§2d):**

| Alan | Değer |
|------|-------|
| İçerik | çekirdek iskelet + profil adapter'ı ile üretilmiş prompt'lar (ADR-035 domain seti ile hizalı: müzik/destek/etiketleme) |
| Adlandırma | `.ai/prompts/prompt-<proje>-<alan>-v<major>.<minor>.md` (ADR-035 §2.2-b kuralının proje ekiyle genişletilmesi) |
| Taşıma | proje arası kopya = dosya + profil eki; `shared:` inheritance (web bulgusu: yalmatık ebeveyn→çocuk) — fork yasak |
| Kapı | major = yeni ADR veya bu ADR'ye revizyon; minor = `log.md` + eval (ADR-021 ruhu, ADR-035 §2.2-b) |
| Durum | PLANNED (`.ai/prompts/` bugün 1 dosya + arşiv 10 dosya; set birleştirme işi ADR-035 §5.1/10 ile kesişir) |

**2.2-e Mimari şema:**

```
[ÇEKİRDEK — sabit, proje bağımsız]     [PROJE KATMANI — profil config]
  workflow (10 adım)                     stack / language / rules
  research protocol (5 sorgu)    +       templates / owners / prompts
  şablon iskeleti (15 bölüm)             overrides (proje > global)
  kalite + güvenlik katmanı
        \                                   /
         \______ [ADAPTER — fark bloğu] ___/
                        |
              [PAYLAŞILABİLİR PROMPT SETİ — sürüm + changelog + eval]
```

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Fork** — her proje için prompt-maker'ın kendi kopyası | Sıfır başlangıç maliyeti, proje tam bağımsız | Bug fix/artış iki kat iş; güvenlik ve kalite katmanı projeler arasında sapar; 30 referans × N proje = sürüm korkusu | Bakım yükü (web: "iki kopya = iki drift"); ADR-024 sahipliği ve ADR-035 iskeleti fork'ta kırılır |
| 2 | **Tek merkez** — tek prompt-maker, tüm projelere aynı kurallar | Tek güncelleme, en düşük maliyet | Proje özgüllüğü kaybolur (C++20/ASIO kuralı PHP projesine girer); proje kuralları birbirine karışır | Proje profili ihtiyacını (§1.2-2) karşılamaz; web: "centralizing everything = unnecessary coupling" |
| 3 | **Mevcut dağınık düzenin korunması** (çekirdek/proje ayrımı yapmadan) | Sıfır ek iş | `§5 COREMUSIC RULES` çekirdeğe gömülü kalır; ikinci proje gelince kaos başlar | §1.2-1 ve §1.1-D: ayrım yazılmadan çoklu proje mümkün değil |
| 4 | **Proje profili + sabit çekirdek + adapter + paylaşılabilir set (SEÇİLEN)** | Fork yok, tek merkez yok; mevcut dosyalar yerinde kalır; ikinci projeye hazır; sürüm/eval ile yönetilir | Katman disiplini (her değişiklik "çekirdek mi, adapter mı?" sorusu ister); ilk kurulum işi | ✓ Seçildi — §2 (a)-(d) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Çekirdek ile proje kuralları **ayrıldığı için** `SKILL.md` başka projeye taşınabilir hale gelir (CoreMusic kuralları geride kalmaz, çekirdek kirletilmez).
- İkinci bir proje geldiğinde **fork başlatılmaz** — yalnız profil config + adapter yazılır (iskelet hazır).
- Prompt seti **sürümlü ve taşınabilir** olur; kopya sürüklenmesi yerine inheritance ile paylaşım (ADR-021/035 kapıları devrede).
- 178 CLAUDE.md ve `.ai/archives/prompt*` (10 dosya) için **hangisi nereye ait** sorusunun cevabı yazılmış olur (ADR-024 hattı ile uyum).
- Proje envanteri ([[.ai/PROJECTS.md]] §7) ile vault yapısı arasında **profil köprüsü** kurulur.

### 4.2 Olumsuz Sonuçlar

- **Katman disiplini yükü:** her kural "çekirdek mi, adapter mı?" etiketi ister; etiketsiz eklenen satır sessiz drift üretir.
- **İlk kurulum işi:** profil dosyaları + `SKILL.md §5` taşıması refactor gerektirir (dosya adı sabit kalacağı için içerik hareketi dikkat ister).
- **Tek proje gerçekliği:** bugün yalnız CoreMusic var — profil/adapter katmanı bir süre **boş çalışır** (PLANNED alanlar); ikinci proje gelmezse maliyet karşılıksız kalabilir.
- **Çoklu kaynak:** çekirdek + profil + adapter = okuma sırası uzar (boot listesine 1-2 dosya ekler).
- **`unified`/`shared-base` ile yeniden karşılaşma riski:** arşiv dosyaları ile yeni çekirdek tanımı kısa süre ikili kalır (ADR-035 §5.1/10 konsolidasyonu ile kapanır).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon | Fallback |
|------|---------|------|------------|----------|
| R1 — **Profil drift** (proje kuralları çekirdeğe sızar) | Yüksek | Orta | `references/` + `SKILL.md` taraması: proje adı/teknoloji geçen satır = adapter'a taşınır; çekirdek sürüm notunda "proje içeriği yasak" kuralı | Sızan satır adapter'a geri taşınır; çekirdek son iyi sürümüne revert (dosya adı değişmediği için kolay) |
| R2 — **Çekirdek çatışması** (iki proje çekirdeğe farklı kural istedi) | Orta | Yüksek | Çekirdek değişmez; fark her zaman profil/adapter'da çözülür (öncelik: proje > global); çekirdek değişikliği = yeni ADR | Çatışan kural adapter'da `overrides` ile iki proje için ayrı ayrı tanımlanır; çekirdek sabit kalır |
| R3 — **Sürüm sapması** (çekirdek sürümü ile prompt seti sürümü uyuşmaz) | Orta | Orta | Prompt seti header'ında `core v…` + `profile v…` ikilisi zorunlu; ADR kapısı (major/minor) + `log.md` | Prompt seti son iyi sürüme sabitlenir; uyuşmazlık seti üretime alınmaz |
| R4 — **Çoklu proje iddiasının kanıtsızlığı** (repo'da ikinci proje yok) | Yüksek | Düşük | Her PLANNED madde açık etiketli (§1.1-D); profil kurulumu CoreMusic'e uygulanarak denenir | İkinci proje gelmezse çekirdek/profil ayrımı tek proje içinde de kazanç sağlar (178 CLAUDE.md sadeleşir) |
| R5 — **Adapter şişmesi** (adapter çekirdeği geçer büyüklüğe ulaşır) | Orta | Orta | Adapter boyutu ölçülebilir (satır/bayt eşiği, review); taşıması gereken her şey aslında çekirdek iyileştirmesidir | Şişen blok incelenir: ortak olan çekirdeğe taşınır (çekirdek sürüm artışıyla), proje-özel kalır |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Çekirdek/proje envanteri: `SKILL.md` §0-§10 ve 30 referans için "çekirdek mi, CoreMusic mi?" etiket tablosu | Master Orchestrator + Vault Steward | 1 gün |
| 2 | `SKILL.md §5 COREMUSIC RULES` (`:413`) + `references/11-coremusic-deep-rules.md` için taşıma planı (dosya adı sabit, içerik adapter'a) | Vault Steward | 0,5 gün |
| 3 | Profil config şablonu: `.ai/.templates/profile/project-profile-template.md` (7 zorunlu alan) + ilk profil `project-profile-coremusic.md` | Vault Steward (Guardrail #16) | 1 gün |
| 4 | Adapter şablonu + öncelik kuralı (proje > domain > çekirdek) — `overrides` bölümü zorunlu | Master Orchestrator | 0,5 gün |
| 5 | Çekirdek sürümleme: `core v<major>.<minor>` + changelog satırı (ADR-035 §2.2-b ile hizalı) | Vault Steward | 0,5 gün |
| 6 | Paylaşılabilir prompt seti adlandırması: `.ai/prompts/prompt-<proje>-<alan>-v…` + `core/profile` sürüm ikilisi | Master Orchestrator | 0,5 gün |
| 7 | `unified`/`shared-base` arşiv dosyalarına "çekirdek denemesi — superseded by ADR-036" notu (dosya adı değişmez) | Vault Steward | 0,5 gün |
| 8 | Boot listesi: `.ai/AGENTS.md` §24.2 ve [[.ai/WORKFLOW.md]] §8.7A okuma sırasına profil dosyası eklenir (çekirdek → profil → adapter) | Master Orchestrator (vault-updater) | 0,5 gün |
| 9 | İndeks güncellemesi: [[.ai/.templates/adr/adr-index.md]] `:107` `🟠 process` → `✅ implemented` (adım 3-4 sonrası); `brain.md:991`, `keys.md:188/:271` durum satırları | Master Orchestrator | 0,5 gün |
| 10 | **Şart 1 — Çekirdek testi:** profil adapter'ı devre dışıyken çekirdek tek başına CoreMusic prompt'u üretmeli (yalnız `references/` + `§2-§4` ile) | QA Engineer | 1 gün |
| 11 | **Şart 2 — Taşıma provası:** ikinci bir örnek profil (ör. NevaEngine — C++20/ASIO) yazılarak adapter'ın çekirdeği kirletmediği doğrulanır (dosya adı yok, içerik var) | Master Orchestrator + Embedded Engineer | 1 gün |
| 12 | **Şart 3 — Kapılı sürüm:** profil/adapter/çekirdek her değişikliği ADR kapısı + `log.md` kaydı ile (major → ADR, minor → log + eval) | Vault Steward | sürekli |
| 13 | **Debate Şart 1a — Pilot doğrulama:** ikinci profil (veya CoreMusic alt birimi — örn. `auth.coremusic.net`) ile çok-proje iddiası pilot olarak denenir; profil + adapter çekirdeksiz üretime uygunluğu kanıtlanır | Master Orchestrator + Vault Steward | 1 gün |
| 14 | **Debate Şart 1b — Katman ayrık eval testi:** çekirdek / profil / adapter ayrı ayrı devreye alınarak ayrık eval testi (adapter kapalı → çekirdek; profil ekli → öncelik: proje > global) | QA Engineer | 1 gün |
| 15 | **Debate Şart 2 — Versiyon pinleme:** prompt seti header'ında `core v…` + `profile v…` ikilisi pinlenir; çekirdek changelog satırı zorunlu (drift önleme — R1/R3) | Vault Steward | sürekli |
| 16 | **Debate Şart 3 — Paylaşım izolasyonu:** çapraz-proje prompt paylaşımında secret/taint izolasyonu doğrulanır; profil/prompt setine secret yazılmaz (REDACTED — §1.4 kısıt) | Security Engineer | sürekli |

### 5.2 Geri Dönüş Planı

1. **Adım 3-4 (profil/adapter) işe yaramazsa:** profil dosyaları arşive taşınır, boot listesinden satır geri alınır; çekirdek `SKILL.md` hiçbir değişiklik görmediği için revert kod/dosya adı olmadan tamamlanır (`log.md`'ye tek revert satırı).
2. **Adım 2 (§5 taşıması) riskliyse:** CoreMusic kuralları `SKILL.md §5` içinde kalır, adapter katmanı yalnız **yeni** projeler için devreye girer; çekirdek/proje ayrımı kademeli geçiş olarak işaretlenir.
3. **Şart 2 (ikinci profil) başarısızsa:** adapter katmanı `⚠️ VERIFICATION REQUIRED` ile işaretlenir, paylaşılabilir prompt seti üretime alınmaz (ADR-035 §5.1/10 konsolidasyonu ile mevcut düzen korunur).
4. **Tam geri alma:** profil + adapter dosyaları `.ai/archives/`'e taşınır, çekirdek envanter tablosu (`§1.1-A` karşılığı) kaldırılır; `SKILL.md` ve 30 referans **dokunulmadığı** için geri dönüş bilgi kaybısızdır.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[.ai/.decisions/accepted/ADR-035-system-prompt-engineering.md]] | Prompt SSOT + 5 katmanlı iskelet; bu ADR'nin çekirdek hattı (§2b) ve prompt seti adlandırması (§2.2-d) |
| [[.ai/.decisions/accepted/ADR-024-ecosystem-modular-docs.md]] | Doküman sahipliği; 178 CLAUDE.md katman bulgusu bu ADR'de doğrulandı (§1.1-B) |
| [[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]] | Kapılı/değişmez sözleşme disiplini — çekirdek sürüm + adapter override kuralının ruhu |
| [[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]] | AI stratejisi; prompt kod içi gömme yasağı (§1.4 kısıt) |
| [[.claude/skills/prompt-maker/SKILL.md]] | Çekirdek varlık (20679 b, §0-§10) — §5 taşınacak |
| [[.claude/skills/prompt-maker/references/10-web-research-protocol.md]] | §1.3 araştırma protokolü (5642 b) |
| [[.ai/archives/prompt-unified-2026-08-15.md]] | Çekirdek denemesi 1 — tek gövde birleştirme (IMPLEMENTED, tek proje) |
| [[.ai/archives/prompt-shared-base.md]] | Çekirdek denemesi 2 — ortak taban + proje kuralı (IMPLEMENTED, `type: prompt-base`) |
| [[.ai/CLAUDE.md]] · [[.ai/AGENTS.md]] · [[.ai/WORKFLOW.md]] | Proje profili bugün bunlarda (§1.1-B); boot sırasına profil eklenecek |
| [[.ai/PROJECTS.md]] | Proje envanteri §7 — profil köprüsü |
| [[.ai/.decisions/index.md]] | ADR-036 slotu (`:73`) |
| [[.ai/index.md]] · [[.ai/keys.md]] · [[.ai/brain.md]] | Katalog kayıtları (`:653` / `:188,:271` / `:991`) |
| [[.ai/.templates/adr/adr-index.md]] | Şablon indeksi (`:107`, 🟠 process → ✅) |
| [[.ai/.templates/adr/adr-template.md]] | Bu dosyanın zorunlu iskeleti (Guardrail #16) |
| Debate şart 1 — çekirdek testi (§5.1/10) | Çekirdek, adaptersız CoreMusic prompt'u üretmeli |
| Debate şart 2 — ikinci profil provası (§5.1/11) | NevaEngine profili ile adapter saflığı doğrulanır |
| Debate şart 3 — kapılı sürüm (§5.1/12) | ADR kapısı + `log.md` (major/minor) |
| Debate 2026-09-25 Şart 1a — pilot doğrulama (§5.1/13) | 2. profil veya CoreMusic alt birimi ile çok-proje iddiası pilot olarak doğrulanır |
| Debate 2026-09-25 Şart 1b — katman ayrık eval testi (§5.1/14) | Çekirdek/profil/adapter sınırları QA eval testi ile kanıtlanır |
| Debate 2026-09-25 Şart 2 — versiyon pinleme + çekirdek changelog (§5.1/15) | `core v…`/`profile v…` pinlemesi drift'i önler (ADR-021/035 kapıları) |
| Debate 2026-09-25 Şart 3 — paylaşım izolasyonu (§5.1/16) | Çapraz-proje paylaşımda secret/taint izolasyonu (REDACTED, §1.4 kısıt) |
| [[.ai/log.md]] | Audit trail — bu ADR'nin yazım ve debate kayıtları |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar kapsamı — (a) proje profili · (b) ortak çekirdek · (c) adapter katmanı · (d) paylaşılabilir prompt seti + sonuç/risk/fallback) |
| Tech Lead | — | 2026-09-25 | ✅ (debate 3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → KABUL; 3 şart bağlayıcı: pilot + katman testi · versiyon pinleme · paylaşım izolasyonu) |
| Arch Lead | — | — | ⏳ (Tech Lead sonrası) |

**Statü özeti:** debate `✅ TAMAMLANDI` (3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → KABUL; Tech Lead ✅) → Arch Lead `⏳` bekleniyor → **frozen YOK** (Arch Lead ✅ satırı tamamlanmadan `frozen` yapılmaz). Status `accepted` = karar metni SSOT olarak yayında.

### 7.1 Debate Kaydı

**Durum: ✅ TAMAMLANDI — 3 tur / 20 persona (2026-09-25) · Oy: 18 kabul / 2 çekimser / 0 red → KABUL**

**Tur 1 — Kanıt gözden geçirme (20 persona):**

- Kanıt sunumu: `.claude/skills/prompt-maker/SKILL.md` **20679 b** + `references/` **30 dosya** + `.archive/` **31 dosya** + `.ai/archives/prompt*` **10 dosya**; [[.ai/archives/prompt-unified-2026-08-15.md]] ve [[.ai/archives/prompt-shared-base.md]] = tek-proje birleştirme **IMPLEMENTED**; ikinci proje dizini **YOK** (§1.1-D) → çok-proje **PLANNED**.
- Araştırma kanıtı (§1.3): **40 kaynak / 5 sorgu**, **9/9 alan tek yönlü**, ters (fork/tek-merkez) kaynak yok.
- Oy dağılımı: **15 kabul/neutral, 4 uyarı** — QA: katman ayrık testi · Critic: 2. proje yok → pilot şart · Security: paylaşım izolasyonu · 1 neutral (kanıt bütünlüğü).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Bağlayıcı şart |
|---|--------|-------|----------------|
| 1 | İkinci proje dizini yok → "çok-proje" iddiası kanıtsız | pilot madde: 2. profil veya CoreMusic alt birimi ile doğrulama | **Şart 1a** |
| 2 | Katman ayrık testi yok → çekirdek/profil/adapter sınırları doğrulanmamış | adapter + profil ayrık eval testi | **Şart 1b** |
| 3 | Drift riski → çekirdek ile profil/prompt seti sürümü uyuşmaz | versiyon pinleme + çekirdek changelog | **Şart 2** |
| 4 | Paylaşım sızıntısı → çapraz-proje prompt paylaşımında secret/taint sızabilir | secret/taint izolasyonu (REDACTED hattı) | **Şart 3** |

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL.**

**Bağlayıcı 3 şart:** (1) **pilot + katman testi** (1a-1b) · (2) **versiyon pinleme** · (3) **paylaşım izolasyonu** — uygulama karşılıkları §5.1 adımları 13-16 ve §6 debate şart satırlarıdır; sağlanmadan paylaşılabilir prompt seti (§2.2-d) üretime alınmaz.

---

**1.0.0 | 2026-09-25 | Created**
*Authority: ADR-036 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
