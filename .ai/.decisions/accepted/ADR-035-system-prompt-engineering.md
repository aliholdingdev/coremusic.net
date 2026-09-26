---
id: ADR-035
title: System Prompt Engineering — Zorunlu 5 Katmanlı İskelet (Rol/Görev/Kısıt/Çıktı Formatı/Few-shot), ADR-021 Ruhunda Kapılı Prompt Versiyonlama + CI Regresyon Eval, OWASP LLM01 Üç Katmanlı Enjeksiyon Savunması, Token Bütçesi ve Domain Prompt Seti
type: adr
category: ai
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-035 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Vault Steward", "Master Orchestrator"]
consulted: ["Backend Architect", "Security Engineer", "QA Engineer"]
informed: ["DevOps Engineer", "Data Engineer", "UI Designer"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]]"
  - "[[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]]"
  - "[[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]]"
  - "[[.ai/.decisions/accepted/ADR-034-credential-vault-normalization.md]]"
  - "[[.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md]]"
---

# ADR-035: System Prompt Engineering — 5 Katmanlı İskelet, ADR Kapılı Versiyonlama + CI Regresyon Eval, OWASP LLM01 Enjeksiyon Savunması, Token Bütçesi ve Domain Prompt Seti

**Durum:** accepted (Draft → Review → Active → **Active**; frozen YOK)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı karar kapsamı) + Master Orchestrator (AI domain)
**İlgili ADR'ler:** [[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]] (AI stratejisi — PromptEngine bulguları, risk 4 enjeksiyon, §5.1/5 hookup) · [[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]] (kapılı/değişmez sözleşme disiplini — ruh kaynağı) · [[.ai/.decisions/accepted/ADR-034-credential-vault-normalization.md]] (SSOT + CI kapısı usulü) · [[.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md]] (limit/maliyet ruhu) · [[.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md]] (biçim referansı — aynı numara-boşluğu-doldurma usulü)

---

## 1. Bağlam ve Kod Kanıtı

Bu ADR, CoreMusic'te **sistem promptlarının nasıl yazılacağını, nasıl versiyonlanacağını, nasıl test edileceğini ve enjeksiyona nasıl karşı korunacağını** tek kararla yazar: 5 katmanlı zorunlu iskelet, ADR kapılı prompt versiyonlama + CI regresyon eval, OWASP LLM01 uyumlu üç katmanlı savunma, token bütçesi, tool-calling prompt standardı ve domain prompt seti. Kanıt 2026-09-25'te kod + vault taramasıyla derlendi ve **IMPLEMENTED** (kodda/diskte var) / **PLANNED** (karar olarak kurulan, karşılığı henüz yok) olarak etiketlendi.

### 1.1 Mevcut Durum (kod + vault kanıtı)

**A) PromptEngine — IMPLEMENTED çekirdek (fakat LLM çağrısına bağlı değil):**

| Dosya | Kanıt (dosya:satır) |
|-------|---------------------|
| `shared/src/AI/PromptEngine.php` (9546 bayt) | sabitler: `MAX_TOKENS_DEFAULT = 4096` `:25`, `COMPRESSION_THRESHOLD = 0.8` `:26`, `INJECTION_PATTERNS` = **7 desen** `:27-35` (`ignore previous`, `you are now`, `system:`, `act as`, `pretend you`, `disregard`, `override`); metotlar: `generatePrompt()` `:48-85` (şablon + context + token sayımı + maliyet tahmini), `countTokens()` `:90-96` (karakter/3.5 tahmini), `optimizePrompt()` `:101-135` (boşluk/liste/context kırpma), `validatePrompt()` `:156-186` (enjeksiyon döngüsü `:162` → `[FILTERED]`, token bütçesi `:170-174`, hassas veri regex `:177-179`) |
| `PromptEngine.php` yerleşik şablonlar (4 adet) | `registerDefaultTemplates()` `:193`; `recommendation` `:195-215`, `audio-analysis` `:220-231`, `eq-optimization` `:236-245`, `security-audit` `:250-260` |
| `shared/src/AI/Contracts/PromptEngineInterface.php` | arayüz: `generatePrompt()` `:23` + `optimizePrompt()` + `validatePrompt()` |
| `shared/src/AI/AIWorkflow.php` | canlı çağrılar: `:66` `generatePrompt('recommendation', …)`, `:176` `generatePrompt('eq-optimization', …)` |

→ **Şablon, token sayımı, enjeksiyon taraması kodda IMPLEMENTED**; ancak ADR-030'un bulgusu geçerli: `validatePrompt()` yanıt yoluna **bağlı değil** (hookup PLANNED — ADR-030 §5.1/5).

**B) ToolCalling — araç şemaları IMPLEMENTED, LLM çağrısı PLANNED:**

| Araç / metot | Kanıt (dosya:satır) |
|--------------|---------------------|
| `file-search` (name/description/parameters şeması) | `shared/src/AI/ToolCalling.php:206-219` |
| `db-query` + SELECT-only kapısı | `:222-234` |
| `http-request` (tam parametre şeması) | `:241-280` |
| `inferCategory()` / `validateParameters()` | `:286-296` / `:304+` |

**C) AIEngine — stub (PLANNED):** `shared/src/AI/AIEngine.php:219`, `:224`, `:241` → üçü de `return [];` (collaborative/content skorları). LLM API çağrısı, RAG, embedding: **0**.

**D) CI kapısı — prompt eval YOK (PLANNED):**

| Ölçüm | Değer | Kanıt |
|-------|-------|-------|
| `.github/workflows/` dosyaları | **2**: `ci.yml` (4367 b) + `secret-scan.yml` (938 b) | dizin listesi |
| Prompt eval / regresyon workflow | **0** | `.github/workflows/*` = 2 dosya, ikisi de prompt ile ilgisiz |
| Vault'ta eval politikası | **0** | `.ai/*.md` içinde `prompt regression`, `prompt eval`, `regression eval`, `prompt test` eşleşmesi → 0 |

⚠️ Cross-ref notu: `.ai/AGENTS.md` §25.2 "`.github/workflows/` = 0 dosya" satırı **güncel değil** (bugün 2 dosya var) — bu ADR §5.1/8 ile o satırın düzeltilmesini de sahiplenir.

**E) Vault prompt envanteri — IMPLEMENTED (ama dağınık):**

| Varlık | Kanıt (dosya / boyut) |
|--------|----------------------|
| `.ai/prompts/` | **1 dosya**: `2026-09-23-vault-refactor-engine.md` (10140 b); enjeksiyon savunma metni `:74` |
| `.ai/archives/prompt*` (arşiv serisi) | `prompt0-genel-ana-prompt-2026-08-15.md` · `prompt0-…-2026-09-01.md` · `prompt1-spa-router-*` · `prompt2-auth-*` · `prompt3-api-*` · `prompt-unified-2026-08-15.md` · `prompt-shared-base.md:39` (ADR-035 yolu rezerve) · `archives/AGENTS.md:27` |
| `.claude/skills/prompt-maker/` | `SKILL.md` (20679 b; PPRRROCEE/2026 tekniği, few-shot fallback, enjeksiyon bölümleri `:500-510`) + `references/` **30 dosya** (`10-web-research-protocol.md` 5642 b, `17-prompt-engineering-deep.md`) |
| UI prompt kalıbı | `.ai/.templates/ui-design/prompt-template.md` (Kalıp C) + `.ai/ui-design/prompt/00-prompt-index.md` |
| AI mimarisi dokümanları | `.ai/architecture/k4-yapay-zeka/ai-generation.md` · `edge-ai.md` · `.ai/architecture/k8-servis/ai-service.md` |

→ Prompt metinleri bugün **üç ayrı yerde** duruyor: (1) kod içi sabit şablonlar (`PromptEngine.php:195-260`), (2) `.ai/prompts/` + `.ai/archives/prompt*`, (3) skill referansları. **Tek politika, tek versiyon çizelgesi, tek eval kapısı yok.**

**F) ADR-035 slotu — numara ayrılmış (boşluk-doldurma kanıtı):**

- `.ai/.decisions/index.md:72` → `ADR-035-system-prompt-engineering | System Prompt Engineering | AI` (dizin satırı; slug hedefi bu dosyadır).
- Aynı slot 7 dosyada daha: `.ai/index.md:652` · `.ai/keys.md:187` (keyword satırı) ve `:270` · `.ai/brain.md:990` ("Prompt engineering standartları") · `.ai/.templates/adr/adr-index.md:106` ("🟠 process") · `.ai/archives/prompt-shared-base.md:39` · `.ai/archives/AGENTS.md:27`.
- `.ai/keys.md:186` → "AI strategy, prompt engineering" anahtarı şimdilik ADR-030'a bağlı — bu ADR ile prompt kelime ailesi kendi satırına kavuşur.
- Aynı "ayrılmış numara" usulü ADR-030/033/034 için de uygulanmıştı (bkz. ADR-034 §1.1-G); bu nedenle "yeni ADR'ler 088+" kuralının istisnasıdır.

### 1.2 Sorun Tanımı

1. **Standart yok:** 4 yerleşik şablon (`:195-260`) ve onlarca vault promptu aynı iskeleti paylaşmıyor — rol/görev/kısıt/çıktı formatı/few-shot sırası ve sorumluluk alanı dosyadan dosyaya değişiyor.
2. **Kapı yok:** Prompt değişikliği ADR'siz, eval'siz, CI'sız yapılabilir; sürüm geriye dönük izlenemiyor (`.ai/*.md` içinde eval politikası 0 eşleşme).
3. **Savunma yarım:** 7 desenli tarama (`:27-35`) ve hassas veri regex (`:177-179`) kodda, fakat `validatePrompt()` yanıt yoluna bağlı değil → OWASP LLM01/LLM07 için **giriş filtresi var, çıkış filtresi yok**.
4. **Maliyet öngörüsü dağınık:** token sayımı (`:90-96`) ve 4096 eşiği (`:25`) tek, ama promptlar arası bütçe politikası yazılı değil (ADR-030 cost-control hattı açık).
5. **Tool-calling promptu yok:** şemalar (`ToolCalling.php:206-280`) hazır, fakat model'e "şu araçlardan nasıl seç" talimatını veren prompt sözleşmesi tanımlı değil (LLM çağrısı zaten PLANNED).

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | system prompt engineering best practices 2025 2026 · LLM system prompt injection defense OWASP LLM01 · prompt versioning golden dataset CI regression eval · LLM tool / function calling prompt design · prompt token optimization caching cost |
| Web Search **Konusu** | System prompt iskeleti (rol → görev → kısıt → çıktı formatı → few-shot), OWASP LLM01 enjeksiyon savunması ve veri hiyerarşisi, prompt versiyonlama + golden set + CI regresyon eval, tool-calling prompt şeması, token bütçesi ve prompt caching |
| Web Search **Bağlam** | PromptEngine kodda var ama LLM çağrısına bağlı değil (ADR-030); CI'da prompt eval workflow'ı 0; prompt metinleri `.ai/prompts/` + `.ai/archives/prompt*` + skill + kod içinde dağınık; ADR-035 slotu 8 dosyada rezerve — standart + kapısı yok |
| Web Search **Kısa Açıklama** | 5 sorgu 2026-09-25'te çalıştırıldı; **33 kaynak** derlendi (OWASP, promptfoo, Langfuse, Martin Fowler, OpenAI docs, AWS Bedrock, arXiv); tümü "yapılandırılmış prompt + kapılı değişiklik + test" yönünde oybirliği |
| Web Search **Uzun Açıklama** | (a) Yapı: system prompt tek system kanalında, sabit sıralı katmanlar — rol (karakter/sınırlar), görev (tek net amaç), kısıtlar (yasaklar), çıktı formatı (şema/uzunluk), few-shot (örnekler veri katmanında); çoklu rol çakışması önlenir. (b) Savunma: system/user ayrımı, kullanıcının verdiği her şeyin "veri" olduğu veri hiyerarşisi, talimat-veri ayracı bloklama, çıktı filtresi ve prompt sızıntısı (LLM07) engeli — OWASP LLM01 cheat sheet üç savunma katmanını öneriyor. (c) Versiyonlama: prompt = kod; golden set üzerinde CI regresyon (promptfoo/Langfuse/Martin Fowler: LLM çıktısı için "evolutionary regression test"), değişiklik ADR ile kapalı. (d) Tool-calling: araç adı + tek cümle açıklama + kısıtlı parametre şeması, "tek araç seç / yetmiyorsa söyle" kuralı, şema ile prompt birlikte versiyonlanır (OpenAI function calling belgeleri). (e) Token: sabit önek önce değişken sonra düzeni prompt caching dostu, sıkıştırma + bütçe + maliyet tahmini (AWS caching, arXiv cache-aware makaleleri). |
| Web Search **Paragraf Veri Uzun** | Araştırma beş karar zeminini besledi: (1) 5 katmanlı iskelet tek standart olur çünkü yapılandırılmış system prompt üretkenlik ve tutarlılığı birlikte iyileştiriyor; (2) savunma üç katman (giriş ayracı + desen taraması + çıktı filtresi) OWASP'ın açık önerisi; (3) "prompt değişikliği = ADR'li kapılı değişiklik + CI regresyon" modeli ADR-021'in sözleşme disiplininin prompt alanına birebir taşınması; (4) tool-calling promptu şemanın ayrılmaz parçası; (5) token bütçesi ve caching, ADR-030 cost-control ile aynı amaca hizmet ediyor; eval yoksa savunmanın ve maliyetin doğruluğu ölçülemiyor. |
| Web Search **Sonucu** | 33 kaynağın tamamı aynı yönde: yapılandırılmış system prompt + veri hiyerarşisi + kapılı versiyonlama + golden set regresyon eval + şema-beraber tool promptu + bütçeli token kullanımı; ters yönde (eval'siz, kapısız, dağınık prompt) hiçbir kaynak yok; çelişkili iddia tespit edilmedi. |
| Web Search **Alınan Karar** | §2 (a)–(f): 5 katmanlı zorunlu iskelet · ADR kapılı versiyonlama + CI regresyon eval · OWASP LLM01 üç katmanlı enjeksiyon savunması · token bütçesi (ADR-030 ile hizalı) · tool-calling prompt standardı · domain prompt seti (müzik/destek/etiketleme) — hepsi `.ai/` Vault SSOT altında |
| Web Search **Sonuç** | Araştırma ile karar uyumu: **9/9 alan tek yönlü, 33 kaynak / 5 sorgu (2026-09-25)**; §4.3 riskleri bu kaynakların da vurguladığı risklerle (injection kaçışı, drift, eval maliyeti, sürüm şişmesi) örtüşüyor |

**Kaynaklar (33):** OWASP LLM01:2025 Prompt Injection (owasp.org) · OWASP LLM07:2025 system prompt leakage · OWASP Prompt Injection Cheat Sheet · Snyk "system prompt protection" · Huntr "prompt injection detection rules" · caseyfenton "10 prompt injection detection rules" · contextpatterns.com system prompt best practices · llmbestpractices.com system prompt structure · dataaihub.co system prompt design guide · fieldguidetoai.com system prompt master class · Exa highlight kaynakları (programmablethought, webarchive, medium) · promptfoo.dev "prompt regression testing" · promptfoo.dev "LLM regression testing guide" · PromptForge "prompt versioning best practices" · Langfuse prompt management · martinfowler.com "evolutionary regression tests for LLM prompts" · tianpan.co "ship AI features with confidence" · GitHub henkey/prompt-versioning · IBM "prompt engineering version control" · LLM Commons "prompt versioning with git" · OpenAI function calling docs · Dhi.io tool calling best practices · runcoder.dev LLM tool calling guide · datature.io function calling guide · IBM function calling guide · Wikipedia Toolformer · decodingml tool calling · SD Times LLM token efficiency · Civo LLM token optimization · AWS Bedrock prompt caching · arXiv cache-aware prompt token optimization (2604.21564) · compresr.ai prompt optimization techniques · GitHub llm-token-efficiency

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (5642 b) — 5 sorgu bu protokolle çalıştırıldı.

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Vault SSOT | Prompt metinleri `.ai/` altında yaşar; kalıcı prompt gömme (kod içi sabit) yasak — ADR-030 hattı |
| ADR kapısı | Prompt iskeleti/semantiği değişikliği ADR ile kayıtlı; frozen ADR'ler (001-037) dokunulmaz, bu dosya frozen değil |
| Token bütçesi | Varsayılan 4096 token (`PromptEngine.php:25`), sıkıştırma eşiği 0.8 (`:26`) aşılamaz |
| OWASP LLM01 | Kullanıcı verisi asla talimat olarak okunamaz; system prompt içeriği user'a geri dönmez (LLM07) |
| Eval maliyeti | CI eval kotasi limitli; API anahtarı REDACTED, gitleaks kapsamındadır (ADR-034 kapısı) |
| Yazım usulü | Vault dosyaları yalnız `vault-utf8-writer.mjs` ile yazılır; şablon zorunlu (Guardrail #16) |

---

## 2. Karar (Decision)

CoreMusic'te system prompt mühendisliği aşağıdaki **altı madde** ile standartlaştırılır; hepsi `.ai/` Vault SSOT altındadır ve ADR kapısına bağlıdır:

- **(a) Zorunlu 5 katmanlı şablon iskeleti:** `[ROL] → [GÖREV] → [KISITLAR] → [ÇIKTI FORMATI] → [FEW-SHOT]` sırası sabit, tek system prompt'ta tek rol sahipliği.
- **(b) Prompt versiyonlama + CI regresyon eval + ADR kapısı (ADR-021 ruhu):** prompt = kod; golden set üzerinde CI regresyonu; değişiklikler ADR ile kayıtlı.
- **(c) OWASP LLM01 üç katmanlı enjeksiyon savunması:** system/user ayrımı + veri hiyerarşisi + çıktı filtresi.
- **(d) Token bütçesi:** PromptEngine'in 4096/0.8 eşikleri prompt politikasının da tavanıdır; caching dostu düzen.
- **(e) Tool-calling prompt standardı:** araç şeması (`ToolCalling.php`) ile prompt birlikte versiyonlanır.
- **(f) Domain prompt seti:** müzik (öneri/analiz/EQ), destek, etiketleme — her alanın kendi system prompt'u ve sahibi.

### 2.1 Neden Bu Seçenek?

Çünkü bugün eksik olan şey prompt üretimi değil **disiplin**: 4 şablon + 30 referanslı skill + onlarca arşiv promptu var, ama iskelet, versiyon, eval ve kapı yok. Web araştırması (§1.3, 33 kaynak) gösteriyor ki üretim kalitesi ancak *ölçülen* değişiklikle sürdürülebilir; OWASP ise savunmanın yalnız girişte değil çıktıda da olmasını şart koşuyor. ADR-021'in "değişmez sözleşme + kapılı değişiklik" deneyimi aynı problemi routing'te çözmüştü — aynı usul prompt alanına taşınıyor. Seçenek, sıfır kod maliyetiyle mevcut `PromptEngine` altyapısının üzerine yazılı kural katmanıdır.

### 2.2 Teknik Detaylar

**2.2-a İskelet (§2a):**

- Her system prompt beş katmanı **aynı sırayla** ve **aynı başlıklarla** taşır: `[ROL]` (kimlik + sınırlar), `[GÖREV]` (tek amaç), `[KISITLAR]` (yasaklar + güvenlik), `[ÇIKTI FORMATI]` (şema/uzunluk/dil), `[FEW-SHOT]` (2-3 örnek; örneklerin içeriği veri katmanıdır, kalıbı talimat katmanı).
- Kural: system kanalında yalnız talimat; user mesajı her zaman `VERİ:` bloğu içinde ve ayracı ile gelir (veri hiyerarşisi).
- Yerleşik 4 şablon (`PromptEngine.php:195-260`) iskelete göre envanterlenir ve kademeli revize edilir (§5.1/2) — iskelet yeni prompt'lara anında, mevcutlara geçişle uygulanır.
- Şablon metni tek dosyada yaşar: `.ai/prompts/system-prompt-skeleton.md` (SSOT); skill (`prompt-maker`) ve kod bu dosyayı referans alır.

**2.2-b Versiyonlama + eval + kapı (§2b):**

- Adlandırma: `.ai/prompts/prompt-<alan>-v<major>.<minor>.md` + dosya sonunda `Sürüm geçmişi` satırı (ADR-030 §1.3 usulü).
- Golden set: her domain için ≥10 örnek (girdi → beklenen çıktı) + ≥5 enjeksiyon negatif örneği → `.ai/prompts/eval/` (PLANNED).
- CI: `.github/workflows/prompt-eval.yml` (PLANNED) — PR'da çalışır; skor eşiği altına düşerse PR engellenir (secret-scan.yml ile aynı kapı usulü).
- Kapı kuralı: **major** (iskelet/semantik değişikliği) → yeni ADR veya bu ADR'ye revizyon; **minor** (kelime/örnek) → `log.md` kaydı + eval zorunlu; eval'siz prompt birimi üretime girmez.
- Rollback: son iyi sürüm (eval'yi geçen) arşivde saklanır; sürüm şişmesi `.ai/archives/` politikasıyla budanır.

**2.2-c Enjeksiyon savunması (§2c — OWASP LLM01/LLM07):**

| Katman | Mekanizma | Kanıt / Durum |
|--------|-----------|---------------|
| 1 — Giriş ayracı | Talimatlar system kanalında; user verisi `VERİ:` bloğunda, veri hiyerarşisi kuralı | İskelet kuralı (§2.2-a) — PLANNED politika |
| 2 — Desen taraması | 7 desen (`:27-35`) + hassas veri regex (`:177-179`) → `[FILTERED]` | `validatePrompt()` `:156-186` — IMPLEMENTED (kod); yanıt yoluna hookup **PLANNED** (ADR-030 §5.1/5 ile aynı kalem) |
| 3 — Çıktı filtresi | Yanıt gitmeden: prompt sızıntısı (system prompt leak) + hassas veri taraması; fail-closed | PLANNED — bu ADR §5.1/6 |
| Test | Enjeksiyon negatif örnekleri golden set'te; eval geçmeden kapı açılmaz | PLANNED (§2.2-b) |

**2.2-d Token bütçesi (§2d):**

- Tavan: `MAX_TOKENS_DEFAULT = 4096` (`:25`); sıkıştırma eşiği `0.8` → `optimizePrompt()` `:101-135` devreye girer.
- Düzen: **sabit önek önce, değişken sonra** (context/veri sonda) — prompt caching dostu (AWS/arXiv bulgusu §1.3).
- Her `generatePrompt()` çıktısı token sayısı + maliyet tahmini üretir (`:48-85`); eval raporunda domain başına ortalama token bütçesi de eşiğe takılır.
- İlişki: ADR-030 cost-control ve ADR-013 limit ruhu ile hizalı — prompt şişirmesi maliyet olarak görünür olur.

**2.2-e Tool-calling promptu (§2e):**

- Şema kaynağı: `ToolCalling.php` — `file-search` `:206-219`, `db-query` (SELECT-only) `:222-234`, `http-request` `:241-280`.
- Prompt sözleşmesi: araç listesi + "en fazla 1 araç seç, yetmiyorsa araç yok de" kuralı + parametre doğrulama hatası dönüş formatı + db-query'de yalnız SELECT talimatı (şemadaki `:230-234` kapısının prompt aynası).
- Şema ve prompt **aynı sürümü** paylaşır (aynı ADR kapısı); `validateParameters()` `:304+` ihlali prompt'a hata olarak geri döner.
- LLM çağrısı PLANNED (AIEngine stub `:219/:224/:241`) → bu madde yalnız **sözleşmeyi** kurar; entegrasyon ADR-030'un hattıdır.

**2.2-f Domain prompt seti (§2f):**

| Domain | Kapsam | Mevcut dayanak | Prompt dosyası (hedef) |
|--------|--------|----------------|------------------------|
| Müzik | öneri, ses analizi, EQ, güvenlik-audit şablonları | `PromptEngine.php:195-260`, `AIWorkflow.php:66,176` | `.ai/prompts/prompt-muzik-v1.0.0.md` |
| Destek | yardım/SSO/iletişim | `.ai/archives/prompt0-*` + `prompt2-auth-*` arşivleri | `.ai/prompts/prompt-destek-v1.0.0.md` |
| Etiketleme | tag/metadata üretimi | `.ai/architecture/k4-yapay-zeka/ai-generation.md` | `.ai/prompts/prompt-etiketleme-v1.0.0.md` |

Sahiplik: prompt0 (genel) → Master Orchestrator; prompt1-3 eşlemesi `.ai/AGENTS.md` §14.1 ile hizalı; üretim örneği olarak `.claude/skills/prompt-maker/SKILL.md` (30 referanslı) cite edilir ve iskelete bağlanır.

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | Mevcut dağınık düzenin korunması (promptsuz standart) | Sıfır ek iş | Değişiklik takibi yok, savunma yarım (çıkış filtresi 0), maliyet öngörüsü yok, drift görünmez | ADR-030 risk 4 açık kalır; web araştırması 33/33 kaynak tersini öneriyor |
| 2 | Prompt'ları doğrudan koda gömmek (hardcoded string) | Tek dosyada değişiklik, kolay debug | Her değişiklik deploy ister; Vault SSOT ihlali; eval'ye kapalı; ADR kapısı atlanır | `.ai/` SSOT kuralı (guardrail #2) + ADR-030 hattı |
| 3 | Hazır SaaS prompt platformu (Langfuse vb.) | Hazır UI, versiyon ve metrik | Harici bağımlılık, veri dışarı çıkma riski, maliyet + vendor lock; self-host kurulum yükü | Güvenlik + maliyet (ADR-034/013 ruhu); `.ai/` + CI yeterli |
| 4 | **Vault SSOT iskelet + ADR kapılı versiyonlama + CI regresyon eval (SEÇİLEN)** | Sıfır harici bağımlılık; mevcut PromptEngine + GitHub Actions üstünde kurulur; kapılı, ölçülebilir, OWASP uyumlu | Süreç yükü (her değişiklik kapıdan geçer); eval CI maliyeti | ✓ Seçildi — §2 (a)-(f) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Prompt değişiklikleri **izlenebilir ve geri alınabilir** hale gelir (sürüm geçmişi + son iyi sürüm arşivi).
- Regresyonlar CI'da **yakalanır** — "prompt değişti, kalite düştü, kimse fark etmedi" senaryosu kapanır.
- Enjeksiyon savunması **üç katmana** çıkar; OWASP LLM01/LLM07 ile uyum yazılı hale gelir.
- Tool şeması (`ToolCalling.php:206-280`) ile prompt **tek sürümde** yaşar; LLM çağrısı geldiğinde entegrasyon hazır olur.
- Token/maliyet her prompt çıktısında görünür (ADR-030 cost-control ile hizalı).
- `.claude/skills/prompt-maker/` gibi dağınık üretim varlıkları tek iskelette toplanır; ADR-035 slotu (8 katalog satırı) dolmuş olur.

### 4.2 Olumsuz Sonuçlar

- **Süreç yükü:** her prompt değişimi kapıdan (eval + kayıt) geçmek zorunda — küçük kelime düzeltmeleri bile maliyetlenir (minor kuralı ile hafifletilir).
- **Eval maliyeti:** golden set CI'da LLM çağrısı gerektirir; API kotası/pulu tüketir.
- **Şablon kısıtı:** 5 katman zorunluluğu yaratıcı/serbest promptları sınırlar; domain'e özgü sapmalar revizyon ister.
- **Sürüm şişmesi:** dosya başına sürüm artışı `.ai/prompts/` dizinini hızla büyütür (bkz. §4.3-R4).
- **CI bağımlılığı:** prompt-eval workflow'ı kırılırsa kapı devre dışı kalır (bkz. §5.2).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon | Fallback |
|------|---------|------|------------|----------|
| R1 — Prompt injection kaçışı (yeni desen) | Orta | Yüksek | 3 katman + golden set'e negatif örnek + periyodik OWASP taraması | Fail-closed: `[FILTERED]` → istek reddedilir, AIWorkflow çağrısı durur |
| R2 — Prompt drift (eval'siz sessiz değişim) | Orta | Orta | CI eval kapısı zorunlu; minor/major ayrımı + log.md kaydı | Eval'yi geçemeyen prompt üretime alınmaz; son iyi sürüm kalır |
| R3 — Eval maliyeti/kota aşımı | Orta | Orta | PR'da küçük set, main'de tam set; domain başına token eşiği | `eval-atla` etiketi yalnız Vault Steward onayıyla; tam local deterministik skorlayıcıya geçiş |
| R4 — Versiyon şişmesi (dosya patlaması) | Yüksek | Düşük | Major yalnız ADR ile; minor'lar dosya içi sürüm satırında | `.ai/archives/` politikası: eski major'lar arşive taşınır, indeks temizlenir |
| R5 — CI'da prompt/anahtar sızıntısı | Düşük | Yüksek | `secret-scan.yml` + gitleaks; eval promptu/anahtarı REDACTED, vault'a yazılmaz | Eval tamamen devre dışı bırakılır (§5.2), kapı kapanır ama repo temiz kalır |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | İskelet SSOT dosyası: `.ai/prompts/system-prompt-skeleton.md` (5 katman + veri hiyerarşisi kuralları) | Vault Steward + Master Orchestrator | 1 gün |
| 2 | Yerleşik 4 şablonun (`PromptEngine.php:195-260`) iskelet envanteri + kademeli revizyon planı | Backend Architect | 2 gün |
| 3 | `.ai/prompts/` dizin yapısı, adlandırma (`prompt-<alan>-v<major>.<minor>.md`) + sürüm geçmişi kuralı | Vault Steward | 1 gün |
| 4 | Golden set ilk sürüm: 3 domain × ≥10 örnek + ≥5 enjeksiyon negatifi → `.ai/prompts/eval/` | Master Orchestrator + QA Engineer | 3 gün |
| 5 | `.github/workflows/prompt-eval.yml` (PR kapısı, eşik + rapor) | DevOps Engineer | 2 gün |
| 6 | Çıktı filtresi + `validatePrompt()` yanıt yoluna hookup (ADR-030 §5.1/5 ile aynı kalem) | Backend Architect | 2 gün |
| 7 | Tool-calling prompt sözleşmesi (şema + tek-seçim kuralı + SELECT-only aynası) | Backend Architect + Master Orchestrator | 2 gün |
| 8 | İndeks güncellemesi: `keys.md:187/:270`, `brain.md:990`, `adr-index.md:106` durumu 🟠 → ✅; `.ai/AGENTS.md` §25.2 "0 dosya" satırı → 2 dosya düzeltmesi + `log.md` kaydı | Master Orchestrator (vault-updater) | 0,5 gün |
| 9 | **Şart 1a — Zincir netleştirme:** AIWorkflow canlı çağrı (`AIWorkflow.php:66,176`) ↔ AIEngine boş (`AIEngine.php:219,224,241`) ucu açık PLANNED etiketiyle işaretlenir; sessiz boş kuralı ADR-030 1b'ye bağlanır | Vault Steward + Backend Architect | 0,5 gün |
| 10 | **Şart 1b — Konsolidasyon:** 8 parçalı prompt envanteri (`.ai/prompts/` 1 + `.ai/archives/prompt*` 7) tek domain seti `.ai/prompts/` altına taşınır; arşiv dosyalarına "arşiv" notu düşülür | Vault Steward | 1 gün |
| 11 | **Şart 2 — Eval workflow:** golden set (§5.1/4) + `.github/workflows/prompt-eval.yml` prompt regression eval iş akışı (§5.1/5) üretime girer; kapı açık değilken ADR tamamlanmış sayılmaz | QA Engineer + DevOps Engineer | 3 gün |
| 12 | **Şart 3 — ADR kapılı versioning:** her prompt sürüm değişikliği ADR kapısı + ADR-021 versioning disipliniyle kayıtlı (major → ADR, minor → `log.md` + eval); versioning şablonu `.ai/prompts/` adlandırma kuralıyla (§2.2-b) uygulanır | Vault Steward | 1 gün |

### 5.2 Geri Dönüş Planı

1. **Adım 5 (CI) başarısızsa:** `prompt-eval.yml` `if:` bayrağıyla devre dışı bırakılır; prompt dosyaları ve iskelet kalır — kapı geçici kapanır, bilgi kaybı olmaz.
2. **İskelet domain'e uymazsa:** bu ADR 1.1.0'a revize edilir (frozen'a geçmeden revizyon serbesttir); mevcut 4 şablon aynen çalışmaya devam eder.
3. **Adım 6 (çıktı filtresi) riskliyse:** yalnız giriş katmanları (1-2) açık bırakılır, fail-closed yerine log-only moda alınır ve ADR-030'a not düşülür.
4. **Tam geri alma:** `.ai/prompts/` içeriği `.ai/archives/`'e taşınır, eval workflow'u silinir, `log.md`'ye tek satır revert kaydı yazılır; kod tarafında (`PromptEngine`) hiçbir değişiklik yapılmadığı için revert kod dokunmadan tamamlanır.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[.ai/.decisions/accepted/ADR-030-ai-strategy-core.md]] | AI stratejisi; PromptEngine "bağlı değil" bulgusu, risk 4 enjeksiyon, §5.1/5 hookup bu ADR ile kapanır |
| [[.ai/.decisions/accepted/ADR-021-spa-router-immutable-contract.md]] | Kapılı/değişmez sözleşme disiplini — §2b'nin ruh kaynağı |
| [[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]] | Güvenlik katmanı hizası (savunma derinliği) |
| [[.ai/.decisions/accepted/ADR-034-credential-vault-normalization.md]] | SSOT + CI kapısı usulü (secret-scan → prompt-eval aynı kalıp) |
| [[.ai/.decisions/accepted/ADR-013-rate-limiting-apcu.md]] | Limit/maliyet ruhu — token bütçesi ile hizalı |
| [[.ai/.decisions/accepted/ADR-033-sql-normalization-strategy.md]] | Biçim referansı — aynı numara-boşluğu-doldurma usulü |
| [[.ai/.decisions/index.md]] | ADR-035 slotu (`:72`) |
| [[.ai/index.md]] · [[.ai/keys.md]] · [[.ai/brain.md]] | Katalog kayıtları (`:652` / `:187,:270` / `:990`) |
| [[.ai/.templates/adr/adr-index.md]] | Şablon indeksi (`:106`, 🟠 process → ✅) |
| [[.ai/.templates/adr/adr-template.md]] | Bu dosyanın zorunlu iskeleti (Guardrail #16) |
| [[.ai/log.md]] | Audit trail — bu ADR'nin yazım ve debate kayıtları |
| Debate şart 1a — zincir netleştirme (§5.1/9) | AIWorkflow canlı ↔ AIEngine boş ucu: PLANNED etiketi + sessiz boş kuralı → ADR-030 1b (§1.1-C, §2.2-e) |
| Debate şart 1b — konsolidasyon (§5.1/10) | 8 prompt dosyası → tek domain seti `.ai/prompts/` + arşiv notu (§1.1-E, §2.2-f) |
| Debate şart 2 — eval workflow (§5.1/11) | Golden set + `prompt-eval.yml` regresyon eval (§2.2-b, §5.1/4-5) |
| Debate şart 3 — ADR kapılı versioning (§5.1/12) | Prompt sürüm değişikliği ADR kapısı + ADR-021 versioning (§2b, §2.2-b) |
| `.claude/skills/prompt-maker/SKILL.md` (30 referans) | Mevcut prompt üretim örneği — iskelete bağlanacak |
| `.ai/archives/prompt-shared-base.md` | ADR-035 yolu 39. satırdan rezerve edilmiş |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar kapsamı — (a) 5 katmanlı iskelet · (b) ADR kapılı versiyonlama + CI eval · (c) OWASP LLM01 üç katman · (d) token bütçesi · (e) tool-calling · (f) domain seti + sonuç/risk/fallback) |
| Tech Lead | — | 2026-09-25 | ✅ (debate tamamlandı — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → KABUL; 3 şart §5.1/9-12 ve §6'ya işlendi) |
| Arch Lead | — | — | ⏳ (Tech Lead sonrası) |

**Statü özeti:** debate `✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` → **frozen YOK** (Arch Lead ✅ satırı tamamlanmadan `frozen` yapılmaz). Status `accepted` = karar metni SSOT olarak yayında.

### 7.1 Debate Kaydı (3 tur / 20 persona)

**Tur 1 — Kanıt gözden geçirme (20 persona, 33 kaynak):** Kod `PromptEngine.php` IMPLEMENTED (MAX_TOKENS 4096 `:25`, 7 enjeksiyon deseni `:27-35`, `generatePrompt()` `:48-85`, `validatePrompt()` `:156-186`, 4 şablon `:195-260`), `PromptEngineInterface.php:23`, `AIWorkflow.php:66,176` canlı çağrı; `ToolCalling.php` file-search `:206-219` + db-query SELECT-only `:222-234` IMPLEMENTED; `AIEngine.php:219,224,241` → `return [];` ve workflow 2 dosya (eval 0) PLANNED; vault prompt envanteri 8 dosya (`.ai/prompts/` 1 + `.ai/archives/prompt*` 7 + prompt-maker skill). Oy dağılımı: 15 kabul/neutral, 4 uyarı (QA: golden set şart · Data: konsolidasyon · Critic: canlı-ama-boş zincir + dağınıklık şartı).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | AIWorkflow canlı ↔ AIEngine boş (`:219/:224/:241`) — uç PLANNED etiketi + sessiz boş kuralı yok | Uç fark açık PLANNED etiketiyle işaretlenir; sessiz boş kuralı ADR-030 1b'ye bağlanır → **şart 1a** | §5.1/9 |
| 2 | Prompt 8 dosya dağınık (kod + `.ai/prompts/` + arşiv 7) — tek politika yok | Tek domain seti `.ai/prompts/` altına konsolide edilir, arşiv dosyalarına "arşiv" notu → **şart 1b** | §5.1/10 |
| 3 | Eval CI 0 (`prompt-eval.yml` yok; `.ai/*.md` eval politikası 0 eşleşme) | Golden set + prompt regression eval workflow şartı → **şart 2** | §5.1/11 |
| 4 | Prompt değişiklik kapısı yok (ADR'siz, sürüm geriye dönük izlenemiyor) | ADR kapısı + ADR-021 ruhunda versioning şartı → **şart 3** | §5.1/12 |

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL.** Çekimser oy gerekçeleri (eval CI maliyeti, süreç yükü) §4.2/§4.3-R3 kapsamında mitigasyonlu kabul edildi.

**3 şart (bağlayıcı):** (1) zincir netleştirme + konsolidasyon (1a-1b) · (2) eval workflow · (3) ADR kapılı versioning — §5.1/9-12 maddeleri ve §6 satırları olarak işlendi.

---

**1.0.0 | 2026-09-25 | Created**
*Authority: ADR-035 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
