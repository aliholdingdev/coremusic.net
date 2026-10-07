---
title: "CoreMusic — ADR-049: Startup Prompt Loader (prompt yükleme zinciri · service→prompt yükümlülük haritası · hata davranışı — sessiz başarısızlık yasağı · cache preload/lazy ADR-007 hizası · prompt konsolidasyonu ADR-035 hizası)"
type: "architecture-decision"
category: "ai"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic startup prompt loader: (a) yükleme zinciri = boot'ta AI/prompt adımı YOK, ilk çağrıda lazy PromptEngine (constructor `registerDefaultTemplates()` ile 4 yerleşik şablonu kaydeder), (b) service→prompt yükümlülük haritası ADR-039 servis sınırları + AGENTS.md §14.1 prompt-agent eşlemesi üzerinden kurulur, (c) hata = sessiz başarısızlık YASAK (ADR-030): eksik prompt → işaretli fallback stub + log kaydı, (d) cache = lazy/cache-aside varsayılan + opsiyonel boot ısıtması (ADR-007 L1/L2/L3 + zorunlu TTL), (e) konsolidasyon = tek domain prompt seti (ADR-035)"
kaynak: "Disk/kod kanıt taraması (2026-09-29: `new PromptEngine(` ve `new AIWorkflow(` non-vendor PHP'de 0 isabet — shared/src, shared/public, shared/bin · RuntimeBootstrap.php 21 satır = timezone + error_reporting, AI/prompt init 0 · PHP genelinde prompt dosyası yükleyen file_get_contents/require/include 0 · PromptEngine.php yerleşik şablon 4 (registerDefaultTemplates :193-264) · AIWorkflow 5 workflow, prompt çağrıları :66 ve :176 · AIEngine getCollaborativeScores :216-225 / getContentBasedScores :230-242 = `return [];` stub · MemorySystem CACHE_TTL :33-36 · .ai/prompts 2 dosya (10140 b + 18158 b) · .ai/archives/prompt* 10 · .ai/ui-design/prompt/** 51 · index.md:90 ADR-049 satırı var) + web araştırması (6 sorgu / 11 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-049: Startup Prompt Loader (Başlangıç Prompt Yükleme Zinciri)

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-09-29 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-049-startup-prompt-loader`
> **İlgili kararlar:** [[ADR-030-ai-strategy-core]] (API-birincil + sessiz başarısızlık yasağı — karar (c) zemini) · [[ADR-035-system-prompt-engineering]] (6 maddelik standart + konsolidasyon adımı — karar (e)) · [[ADR-007-cache-namespace]] (L1/L2/L3 + zorunlu TTL + cache-aside — karar (d)) · [[ADR-039-7-service-platform-architecture]] (11 servis tablosu + PSR-14 + servis↔servis HTTP yasağı — karar (b)) · [[ADR-036-multi-project-prompt-maker]] (prompt-maker skill üretimi — §1.1-B envanter bağlamı) · [[ADR-048-view-transition-api-integration]] (önceki ADR — biçim/kanıt etiketi referansı) · [[../index.md]] · [[../../raw/brain.md]]
> **Ad gerekçesi:** slug `ADR-049-startup-prompt-loader` **disk kanıtından** alınmıştır — `.ai/.decisions/index.md:90` bu adı taşır (`| [[../../raw/brain.md]] ADR-049-startup-prompt-loader | Startup Prompt Loader | AI |`); bu dosya o boşluğu doldurur. **Numara istisnası:** güncelleme kuralı yeni ADR'lerin ADR-088+ aralığından başlamasını söyler (vault katalogunda ADR-085..089 referansları `brain.md:179,284,843` ve `index.md:688`'de vardır); 049 slotu `index.md:90`'da **rezerve edilmiş** olduğu için (ADR-030/035 gibi) bu slot doldurulur — yeni numara tüketilmez.
> **Index durumu:** `.ai/.decisions/index.md:90` satırı **vardır** ve satır 88/89'daki ADR-046/048 satırlarıyla **aynı biçimdedir**; `[[../../raw/brain.md]]` bağlantısının slug'ı doğru hedefe bağlayan düzeltmesi **bir sonraki vault reset'ine ertelenmiştir** (bu işlemde index.md'ye dokunulmadı — report-only).
> **Frozen notu:** ADR-001–037 **dokunulmamıştır** (yalnız atıf; index.md:28 frozen aralığı 001→036). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıt — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **STUB** = kod var ama gövde boş/dönüş değerleri sahte · **ÇELİŞKİ** = iki kayıt uyuşmuyor · **KAPSAM FARKI** = ölçüm kapsamı/tarih farklılığı (çelişki değil).

#### A) Prompt yükleme zinciri — kodda **0** (hiçbir prompt dosyası PHP tarafından yüklenmiyor)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `new PromptEngine(` | non-vendor PHP (`shared/src`, `shared/public`, `shared/bin`) **0 isabet** — yalnız `shared/vendor/composer/autoload_*` classmap kayıtları var | **PLANNED** (hiç örneklenmiyor) |
| `new AIWorkflow(` | **0 isabet** (aynı kapsam) | **PLANNED** |
| `file_get_contents` / `require` / `include` (PHP geneli) | **prompt dosyası** okuyan hiçbir çağrı **0** — `.ai/prompts/*` kod tarafından hiç açılmıyor | **PLANNED** (dosya→kod köprüsü yok) |
| `shared/src/Bootstrap/RuntimeBootstrap.php` (21 satır) | yalnız `date_default_timezone_set` + `error_reporting`; AI/prompt init **0 satır** | **PLANNED** (boot'ta AI adımı yok) |
| `.ai/.decisions/accepted/ADR-030-ai-strategy-core.md:38` | "AI sınıflarını instantiate/import eden kod **0** — yalnız kendi iç importları" | DOĞRULANDI (aynı bulgu 2026-09-29'da teyit edildi) |

**Yükleme zinciri bugün yoktur:** promptlar ya PHP içinde **heredoc** olarak gömülüdür ya da vault'ta **okunmayan dosya** olarak durur.

#### B) Prompt envanteri (2026-09-29 sayımı — 3 ayrı yerde dağınık)

| Yer | Sayı / Boyut | İçerik | Etiket |
|---|---|---|---|
| `.ai/prompts/` | **2 dosya** (10140 b + 18158 b) | `2026-09-23-vault-refactor-engine.md`, `2026-09-27-component-system-master-prompt.md` | IMPLEMENTED (dosya var) / **yüklü değil** |
| `.ai/archives/prompt*` | **10 dosya** | prompt0–prompt3 serisi + birleşik promptlar (arşiv) | IMPLEMENTED (arşiv) |
| `.ai/ui-design/prompt/**` | **51 dosya** | UI design promptları — **farklı kategori** (ekran/tasarım promptu, sistem promptu değil) | IMPLEMENTED (kapsam dışı bu ADR'nin LLM zinciri) |
| `shared/src/AI/PromptEngine.php` yerleşik şablon | **4** | `recommendation`, `audio-analysis`, `eq-optimization`, `security-audit` — `registerDefaultTemplates()` `:193-264` (heredoc) | **IMPLEMENTED** (kod içinde) |
| `.claude/skills/prompt-maker/` | SKILL.md + `references/*` | prompt üretim skill'i (üretim aracı, runtime promptu değil) | IMPLEMENTED |
| `.ai/.decisions/accepted/ADR-035-system-prompt-engineering.md:76-77` | 1 + 7 (kayıtlı eski sayı) | `.ai/prompts/` = 1 dosya, arşiv = 7 dosya | **KAPSAM FARKI** → bugün 2 + 10 (yeni dosyalar eklenmiş; çelişki değil, tarih farkı) |

> **Dağınıklık bulgusu (ADR-035 teyidi):** prompt metinleri bugün **üç ayrı yerdedir** — (1) kod içi heredoc (PromptEngine), (2) `.ai/prompts/` + `.ai/archives/prompt*`, (3) skill referansları. Runtime'a ulaşan tek kanal (1)'dir, çünkü (2) ve (3) hiçbir kod tarafından yüklenmiyor (§1.1-A).

#### C) Kod durumu — sınıflar ve stub yüzeyi

| Sınıf / Dosya | Kanıt | Etiket |
|---|---|---|
| `PromptEngine.php` | `generatePrompt():48-85`, `optimizePrompt():101-135`, `validatePrompt():156-186`, `registerDefaultTemplates():193-264`, `MAX_TOKENS_DEFAULT 4096 :25`, 7 enjeksiyon pattern `:27-35` | **IMPLEMENTED** (gerçek kod) — ama örneklenmiyor |
| `AIWorkflow.php` | 5 workflow: `:49` recommendation, `:99` audioAnalysis, `:146` autoEQ, `:198` hardwareAnalysis, `:240` faultPrediction; constructor 6 bağımlılık (AIEngine, AIOrchestrator, KnowledgeBase, PromptEngine, MemorySystem, ToolCalling); prompt çağrıları `:66`, `:176`; EQ sonucu `$this->memory->remember(..., 'l2', 3600)` `:172-173` | **IMPLEMENTED** — ama örneklenmiyor |
| `AIEngine.php` | `getCollaborativeScores():216-225` → `return [];` · `getContentBasedScores():230-242` → `return [];` · `analyzeHardware():145-163` placeholder · `extractFeatures():322-334` sabit değerler (bpm 120.0, key 'C') · hibrit iskelet `:56-83` gerçek kod | **STUB** (öneri veri gövdesi boş) |
| `MemorySystem.php` | L1/L2/L3 · `CACHE_TTL :33-36` (`l1`=0, `l2`=3600, `l3`=86400) · `remember() :129` | **IMPLEMENTED** |
| `shared/src/Events/` | 9 domain + 3 integration event | **IMPLEMENTED** (ADR-039 "9+3" kaydı ile aynı) |
| `.github/workflows/` | `ci.yml`, `secret-scan.yml` = 2 dosya (ölçüm 2026-09-27) | **IMPLEMENTED** — prompt eval workflow **0** (ADR-035 bulgusu) |

#### D) Vault kayıtları (bu ADR'den önceki iddialar)

| Kayıt | İçerik | Etiket |
|---|---|---|
| `.ai/.decisions/index.md:90` | `| [[../../raw/brain.md]] ADR-049-startup-prompt-loader | Startup Prompt Loader | AI |` — satır **var**, 88/89 (ADR-046/048) ile aynı biçim | DOĞRULANDI (düzeltme reset'e ertelendi — report-only) |
| [[ADR-039-7-service-platform-architecture]] `:117-127` | 11 servis tablosu; servis ayrım sırası download→media→auth; **servis↔servis HTTP yasağı**; servisler arası iletişim **PSR-14 event** ile | DOĞRULANDI (frozen — okundu, değişmedi) |
| `AGENTS.md §14.1` | prompt→agent eşleme tablosu: prompt0 her görev başında zorunlu; prompt1 (SPA Router) / prompt2 (Auth) / prompt3 (API) yalnız ilkili domain görevinde — **yüklülük haritasının vault'taki tek kaydı** | DOĞRULANDI |
| [[ADR-030-ai-strategy-core]] | "AI sınıfları 7 sınıf + 6 contract, instantiate 0" · API-birincil/yerel-opsiyonel · **sessiz başarısızlık yasağı** | DOĞRULANDI (frozen) |
| [[ADR-035-system-prompt-engineering]] | 6 maddelik standart · konsolidasyon adımı "8 parçalık envanter → tek domain seti" · eval workflow 0 · CI 2 dosya | DOĞRULANDI (frozen) |
| `AGENTS.md §25.2` | "workflows = 0 dosya" kaydı | **ÇELİŞKİ** (stale): `.ai/.workflows/` bugün 2 dosya vardır — bu ADR'de düzeltilmez, raporlanır |
| Üst görev yönergesi | "Yeni ADR'ler ADR-088+'dan başlar" | **KURAL + İSTİSNA**: 049 slotu `index.md:90`'da rezerve (§ künye — ad gerekçesi) |

> **Bulgu özeti:** Prompt **üretimi** vardır (4 heredoc şablon + 2 vault promptu + 10 arşiv + skill), ama prompt **yükleme zinciri yoktur**: boot'ta AI adımı yok, PromptEngine/AIWorkflow hiç örneklenmiyor, hiçbir PHP dosyası prompt dosyası açmıyor, AIEngine'in öneri gövdesi `return []` stub. Yükümlülük haritası yalnız vaultta (`AGENTS.md §14.1` + ADR-039 servis tablosu) yazılı, kodda karşılığı yok. Bu ADR bu dört boşluğu (a)–(e) kalemleriyle sabitler.

### §1.2 Sorun Tanımı (Problem)

Dört sorun üst üste biniyor. **(1) Zincir yok:** prompt dosyaları diskte duruyor ama hiçbir çalışma anında okunmuyor; runtime'a ulaşan tek prompt kanalı `PromptEngine`'in içine gömülü 4 heredoc — yani "prompt dosyada, kodda değil" ikiliği var ve dosya tarafı ölü ağırlık. **(2) Yükümlülük belirsiz:** ADR-039 11 servisi tarif ediyor, `AGENTS.md §14.1` hangi agent'ın hangi promptu okuyacağını yazıyor; ama "hangi servis hangi promptu **zorunlu** olarak ister, prompt yoksa ne olur" sorusunun kod karşılığı yok — loader yazılınca bu harita olmadan her servis kendi promptunu kendi yoluyla çeker. **(3) Hata davranışı boş:** `AIEngine` bugün `return []` ile **sessizce** boş dönüyor; bu ADR-030'un sessiz başarısızlık yasağıyla yüzleşir ve loader da aynı tuzağa düşebilir (prompt bulunamazsa sessiz boş string). **(4) Cache kararı verilmedi:** ilk çağrıda prompt okunacaksa bunun cache'i ADR-007'in L1/L2/L3 + zorunlu TTL şemasına oturmalı; preload mı lazy mı belirsiz — preload boot'u yavaşlatır, lazy ilk istekte gecikme yaratır. Beşinci sorun **dağınıklık**: üç yerdeki prompt envanteri (ADR-035 bulgusu) konsolide edilmeden loader hangi kaynaktan okuyacağını bilemez. Karar; zincirin **sırasını**, yükümlülük **haritasını**, hata **davranışını**, cache **stratejisini** ve konsolidasyon **hedefini** sabitler.

### §1.3 Web'den Araştırma Raporu & Sonuçları

Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (birincil/resmî kaynak öncelikli; her iddia çapraz kaynaklı). Tarih: 2026-09-29.

| Alan | Değer |
|------|-------|
| Web Search **Query** | 6 sorgu: (1) "prompt loading strategy eager lazy progressive dynamic LLM agents theory" (2) "MLflow prompt registry immutable version alias prompt management" (3) "LangGraph pre-load prompts at startup cache avoid runtime fetch" (4) "Langfuse prompt cache 60 seconds stale-while-revalidate fallback prompt client SDK" (5) "Anthropic prompt caching prefix invalidation cache breakpoints documentation" (6) "PromptLayer prompt management production deployment versioning rollback CI evaluation" |
| Web Search **Konusu** | Prompt yükleme/initialization kalıpları 2025-26 (eager / lazy / progressive / dynamic), LLM uygulama bootstrap'inde model-prompt preload'ı, prompt template yönetimi (immutable version + mutable alias registry'leri), lazy-vs-preload karar gerekçeleri, AI config/prompt cache TTL + stale-while-revalidate + fallback, sağlayıcı tarafı prefix-cache invalidation kuralları, üretim promptu için version/label/rollback/eval kapısı |
| Web Search **Bağlam** | Karar disk gerçeğiyle yüzleşiyor: yükleme zinciri 0 (§1.1-A), envanter 3 yerde dağınık (§1.1-B), AIEngine `return []` stub (§1.1-C), cache şeması ADR-007'de yazılı ama prompt için uygulanmamış. Araştırma 2026-09-29'da yapıldı; 6 sorgu / 11 adlandırılmış kaynak; CoreMusic'e taşınan sonuçlar §2 (a)–(e) kalemlerine bağlanmıştır |
| Web Search **Kısa Açıklama** | Prompt yükleme dört stratejiye ayrılıyor: **eager** (assembly anında, her zaman hazır), **lazy** (istemci gerektiğinde çeker — skill/katalog göstergesi promptta, içerik diskte), **progressive** (minimal başlangıç, talep üzerine genişleme), **dynamic** (tur başına enjeksiyon). Bootstrap'te **tek seferlik preload** kuralı: başlangıçta bir kez başlat, her istekte tekrar kurma (aksi halde her çağrıya 3–4 sn ekleniyor). Prompt registry'leri **immutable version + mutable alias** modelinde uzlaşıyor: sürüm değişmez, ortam etiketi (production/staging) hareketlidir — böylece rollback kod değişikliği gerektirmez. Cache tarafında **client-side TTL + stale-while-revalidate + fallback prompt** kalıbı hâkim; sağlayıcı tarafında ise **prefix byte-for-byte eşleşmezse cache düşer** (tek bayt farkı tüm sonraki breakpoint'leri geçersiz kılar) |
| Web Search **Uzun Açıklama** | Kaynaklar beş eksende buluşuyor. (i) **Yükleme stratejileri:** serejke/ai-agents-theory `patterns/prompt-loading.md` dört stratejiyi `PromptLoader { source, strategy, slot }` tipiyle tanımlar — eager "garantili mevcut", lazy "yüzlerce skill promptu şişirmez ama tarif zayıfsa hiç yüklenmez", progressive "önce indeks sonra içerik", dynamic "tur bazlı". Aynı sayfa eager'ın "masa içeriği", lazy'ın "bölüm" metaforunu verir. MindStudio progressive disclosure yazısı tetik mantığı olmadan ya aşırı yüklediğini ya da kaçırdığını belirtir. (ii) **Bootstrap preload:** LangChain forum reçetesi LLM/embedding/store'ü **import anında bir kez** kurup derlenmiş grafiği/paylaşılan nesneyi yeniden kullanmayı, FastAPI lifespan ile açılışta bağlantıyı başlatmayı önerir — "her invocation'da kurmak 3–4 sn ekliyor" gerekçesiyle. Langfuse "Guaranteed Availability" sayfası iki seçenek verir: **ya** startup'ta promptu ön-getir ve alınamazsa uygulamadan çık, **ya da** fallback prompt tanımla — ikisi de **sessiz devam etmez**. (iii) **Template yönetimi:** MLflow Prompt Registry immutable version (Git benzeri commit/diff) + **mutable alias** (`production`, `staging`) sunar; alias sayesinde uygulama kodu sürüm numarasını bilmez, etiket taşıyarak rollback/A-B yapılır. MLflow cache dokümanı net kural verir: **immutable version sonsuz TTL ile cache'lenir, alias tabanlı prompt 60 sn TTL** alır ve alias değişince cache invalidation olur. PromptLayer aynı modeli release label + eval-gated deploy ile üretimiştir: registry'deki onaylı sürüme label ile geçilir, kod deploy'u beklemez, geri alma anlıktır. (iv) **Lazy vs preload (cache):** Langfuse SDK cache'i **client-side, 60 sn TTL, stale-while-revalidate** çalışır — bayat prompt anında döner, yenileme arka planda asenkron yürür; **startup pre-fetch opsiyoneldir** ("genellikle gereksizdir"), hot path'te ağ isteği yoktur; ağ/yerel önbellek ikisi de düşerse **fallback prompt** devreye girer. Arize benzer şekilde hot path'ten uzakta çalışan lokal cache + uzak registry anlatısını savunur. (v) **Prefix cache geçersizliği:** Anthropic prompt-caching rehberi "cache bir prefix eşleşmesidir; prefix içindeki tek bayt farkı sonrasının tamamını geçersiz kılar" der ve sessiz bozucuları listeler (system prompt'a gömülü saat/uuid, sırasız JSON, koşullu sistem bölümleri, kullanıcı bazlı araç listesi) — render sırası `tools → system → messages`. OpenAI dokümanı breakpoint mantığını aynı doğrultuda verir (en uzun eşleşen prefix'e kadar geriye doğru okuma, dinamik içeriğin sona konulması). |
| Web Search **Paragraf Veri Uzun** | CoreMusic'in prompt sorunu aslında "prompt yok" değil, **"prompt nerede ve ne zaman yükleniyor" sorusunun cevapsız** olması: metinler üç yerde duruyor, runtime'a ulaşan tek kanal kod içine gömülü dört heredoc ve bu kanal hiç örneklenmiyor. Web bu boşluğa beş dersle giriyor. Birincisi **tek yükleme stratejisi yok**: eager güvenilir ama şişirir, lazy ölçeklenir ama tarif zayıfsa içerik hiç gelmez — bu yüzden loader'ın her prompt için `eager|lazy` etiketi taşıması ve "gösterge promptta, içerik kaynakta" kuralına uyması gerekiyor. İkincisi **boot tembel olmalı, ilk çağrı hazır bulmalı**: import/boot anında bir kez kurma ilkesi (LangChain forum) ile Langfuse'un "pre-fetch opsiyoneldir, hot path'te ağ yok" diyen client-cache kalıbı aynı yere çıkıyor — CoreMusic'te karşılığı, boot'un AI'ya hiç dokunmaması (bugünkü RuntimeBootstrap davranışı korunur) ama ilk prompt çağrısında PromptEngine'in tek seferlik kurulup sonuçlarının ADR-007 şemasıyla cache'lenmesidir. Üçüncüsü **hata asla sessiz olmaz**: Langfuse iki kesin seçenek sunuyor — startup'ta ön-getirip alınamazsa çık, ya da fallback prompt tanımla; "boş dönüp devam et" seçeneği yok. Bu, ADR-030'un sessiz başarısızlık yasağıyla birebir örtüşüyor ve `return []` stub'un kabul edilemez olduğunu doğruluyor. Dördüncüsü **version + alias ayrımı** immutable gövdeyi değişmez, ortam etiketini hareketli tutar; cache kuralı da bundan türer (immutable → uzun TTL, alias → kısa TTL + invalidation). Prompt tarafında karşılığı: prompt dosyası değişmez bir sürüm taşır, "hangisi prod" etiketi kısa TTL ile okunur. Beşincisi **prefix determinizmi**: LLM sağlayıcısı promptun başındaki her baytı ölçtüğü için loader'ın ürettiği sistem promptu saat/uuid gibi dinamik parçalar içermemeli, dinamik veri en sonda toplanmalı — aksi halde cache her turda düşer ve maliyet/latans şişer. Sonuç olarak web araştırması, "önce preload edelim" veya "her şeyi dosyadan okuyalım" gibi tek başına reçeteleri reddediyor; karar **iki kademeli (boot tembel / ilk çağrı hazırlı), etiketli (eager-lazy), sürümlü (immutable+alias), sessiz olmayan (fallback+log)** bir loader lehine netleşiyor. |
| Web Search **Sonucu** | 6 sorgu / **11 adlandırılmış kaynak**: (1) serejke/ai-agents-theory `patterns/prompt-loading.md` (eager/lazy/progressive/dynamic + PromptLoader tipi), (2) arXiv 2604.21816 "Tool Attention" (eager şema enjeksiyonu MCP vergisi 10k–60k token; iki fazlı lazy loading ile ölçülen %95 token azaltımı), (3) MindStudio progressive disclosure (tetik mantığı + indeks zorunluluğu), (4) MLflow Prompt Registry (`mlflow.org/docs/latest/prompts` — immutable version + alias), (5) MLflow cache dokümanı (`mlflow.github.io/.../prompt-registry` — immutable sonsuz TTL / alias 60 sn TTL + invalidation), (6) LangChain forum 1838 (import anında tek seferlik preload, lifespan hook'ları), (7) Langfuse Caching (client-side 60 sn TTL + stale-while-revalidate + opsiyonel pre-fetch + fallback), (8) Langfuse Guaranteed Availability (startup pre-fetch **veya** fallback — sessiz devam yok), (9) Anthropic skills prompt-caching (prefix byte eşleşmesi + sessiz bozucu listesi), (10) OpenAI prompt caching (breakpoint + dinamik içerik sona), (11) PromptLayer (release label, eval-gated promote, anlık rollback). **Olumsuz/negatif bulgular da var:** lazy yükleme tarif zayıfsa içeriği hiç getirmez; Langfuse bayat-cache davranışı etiket kaldırıldığında eski promptu servis edebilen bilinen SDK sınırlaması taşır (GitHub issue 10138); prefix-cache'te saat/uuid/koşullu bölüm cache'i her turda düşürür; sağlayıcı cache'i model/kurulum bazlıdır ve fiyat/TTL koşulları değişkendir. |
| Web Search **Alınan Karar** | Karar a-e kalemleri bu bulgularla sabitlendi: (a) **Yükleme zinciri = boot tembel, ilk çağrı hazırlı** — `RuntimeBootstrap` AI'ya dokunmaz (bugünkü davranış korunur), ilk prompt çağrısında `PromptEngine` örneklenir ve `registerDefaultTemplates()` 4 yerleşik şablonu kaydeder; dosya tabanlı vault promptları runtime'a **bağlanmaz** (yalnızca kod içi şablonlar erişilebilir) — LangChain preload ilkesi + Langfuse "pre-fetch opsiyoneldir" ile uyumlu, eager/lazy etiketi her prompt için yazılır (serejke dört strateji). (b) **Yükümlülük haritası** ADR-039 servis sınırları (PSR-14, servis↔servis HTTP yasağı) + `AGENTS.md §14.1` prompt-agent eşlemesi üzerinden kurulur — her servis yalnız kendi promptunu ister, event ile yayınlar (MLflow/PromptLayer registry mantığının iç servis karşılığı). (c) **Hata = sessiz başarısızlık yasak** (ADR-030 + Langfuse Guaranteed Availability): prompt bulunamazsa işaretli fallback stub + `log.md` kaydı; `return []`-tarzı sessiz boş dönüş loader'da **yasak**. (d) **Cache = lazy/cache-aside varsayılan** (ADR-007) + opsiyonel ilk-çağrı ısıtması; key `app:v{version}:{domain}:{prompt}`, TTL zorunlu; immutable şablon içeriği uzun TTL, "hangi sürüm prod" etiketi kısa TTL (MLflow kuralının ADR-007 karşılığı); hot path'te ağ isteği yok (Langfuse). (e) **Konsolidasyon** tek domain prompt setine (ADR-035) — loader konsolide edilmiş tek kaynaktan okur, üç yerde dağınıklık loader yazımından **önce** kapatılır. |
| Web Search **Sonuç** | Araştırma kararı **destekledi ve iki şartı netleştirdi**: (1) **tek reçete yok** — "hepsini preload et" de "her şeyi dosyadan lazy oku" da tek başına yanlış; doğru kalıp **boot'ta dokunma + ilk çağrıda kur + cache'le + etiketle**; (2) **hata yolu zorunlu** — startup pre-fetch veya açık fallback, ikisinden biri olmadan loader sessiz başarısızlık üretir (ADR-030 ihlali). Ek üç doğrulama: immutable-version/alias cache ayrımı ADR-007 TTL şemasıyla uyumludur; prefix determinizmi gereği loader çıktısı **deterministik** olmalıdır (saat/uuid yok, dinamik veri sonda); lazy stratejinin ürünü olan katalog/gösterge ayrı bir dosyada yaşamalı (AI'da prompt kataloğu = §2.5 konsolide set). Yeni bir harici prompt registry servisi (MLflow/Langfuse/PromptLayer tarzı) **getirilmez** — CoreMusic tek süreçli PHP'dir, registry işi ADR-007 cache + dosya envanteriyle karşılanır. |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-030-ai-strategy-core]] API-birincil + sessiz başarısızlık yasağı | LLM çağrısı API üzerinden; **hiçbir** loader/akış `return []`-tarzı sessiz boş dönüş yapamaz — eksik prompt işaretli fallback + log kaydı üretir |
| [[ADR-007-cache-namespace]] katmanlı cache | Key `app:v{version}:{domain}:{key}`, **TTL zorunlu** (zaman testi yasak), L1 APCu / L2 file / L3 HTTP; cache-aside/lazy varsayılan — loader cache'i bu şemanın dışına **çıkamaz** |
| [[ADR-039-7-service-platform-architecture]] servis sınırları | Servis↔servis HTTP **yasak**; servisler arası tek kanal PSR-14 event (9 domain + 3 integration); yükümlülük haritası bu sınırlar içinde kurulur — prompt "başka servisten çekilmez" |
| [[ADR-035-system-prompt-engineering]] standart + konsolidasyon | Prompt tek domain setinde toplanır; iskelet/eval/CI kuralları korunur; envanter sayıları (2 + 10 + 51) bu ADR'de **raporlanır, düzeltilmez** (SRP — envanter ADR-035'in işi) |
| ADR-001–037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz (index.md:28) |
| In-Place Refactoring | Dosya adları onaysız **değiştirilmez**; `.ai/.decisions/index.md:90` slug düzeltmesi bu işlemde **eklenmez** (reset'e erteli — report-only) |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs` (log.md = append-only); PowerShell write cmdlet'leri yasak |
| REDACTED | Token, anahtar, API key, cookie, kullanıcı verisi bu ADR'ye yazılmaz |
| Hallucination disiplini | Diskte olmayan hedefe wiki-link **yazılmaz**; kanıtsız satır `⚠️ VERIFICATION REQUIRED` ile işaretlenir |
| Boot performansı | `RuntimeBootstrap`'e ağır iş eklenemez — boot'un AI'ya dokunmaması bu kararın **ön koşulu**dur |

---

## §2 Karar (Decision)

CoreMusic, prompt yönetimini **iki kademeli loader** ile kurar: **boot'ta prompt işi yoktur**, ilk prompt çağrısında `PromptEngine` örneklenir ve yerleşik şablonları kaydeder; yükümlülükler ADR-039 servis sınırları içinde **event** ile taşınır; hata **sessiz olamaz**; cache ADR-007 şemasında **lazy** kurulur; kaynak **tek konsolide prompt setidir**.

### §2.1 (a) Yükleme Zinciri — Boot Tembel, İlk Çağrı Hazırlı (eager/lazy etiketli)

| Kademe | Ne olur | Kanıt/zemin |
|---|---|---|
| **Boot (RuntimeBootstrap)** | Yalnız `date_default_timezone_set` + `error_reporting` — **AI/prompt satırı eklenmez** | Bugünkü 21 satırlık dosya korunur (§1.1-A) |
| **İlk prompt çağrısı** | `PromptEngine` örneklenir → constructor `registerDefaultTemplates()` (`:193-264`) 4 şablonu kaydeder → sonuç **ADR-007 cache'ine** yazılır (sonraki çağrılar cache'den) | `new PromptEngine(` = 0 → bu adımla 1'e çıkar (§5.1 adım 2) |
| **Çağrı zinciri** | Çağrı eden katman → `AIWorkflow` (workflow seçimi) → `PromptEngine::generatePrompt()` (`:48-85`) → şablon + değişkenler → `ToolCalling` (API çağrısı) | `AIWorkflow` 5 workflow + 6 bağımlılık (`:49,99,146,198,240`) |
| **Dosya promptları (`.ai/prompts/`, arşiv)** | **Runtime'a bağlı değildir** — bunlar vault dokümanıdır; runtime'a yalnız kod içi (veya konsolide setten üretilip koda gömülen) şablonlar girer | `file_get_contents` ile prompt yükleyen PHP = 0 (§1.1-A) |
| **Etiket** | Her prompt kaydında `eager \| lazy` etiketi zorunlu: `eager` = çağrı anında hazır, `lazy` = katalog/kısa gösterge promptta, içerik talep üzerine | serejke `prompt-loading.md` dört strateji (§1.3) |

- **İlk çağrı gecikmesi** tek seferliktir ve ADR-007 cache'i ile emilir; her çağrıda tekrar kurma **yok** (LangChain preload ilkesi).
- Loader, PromptEngine'i **ilk kez** kurduğunda kurulumu `log.md`'ye bir satır olarak yazar (kurulum gözlemlenebilir, sessiz değil).

### §2.2 (b) Service→Prompt Yükümlülük Haritası (ADR-039 hizası)

| Yükümlü | Promptu | Kanal | Şart |
|---|---|---|---|
| Çağrı eden servis/Workflow (`AIWorkflow`) | İlgili workflow promptu (`recommendation`, `audio-analysis`, `eq-optimization`, `hardwareAnalysis`, `faultPrediction` — `:49,99,146,198,240`) | Doğrudan `PromptEngine` çağrısı (aynı süreç içi) | Prompt yoksa §2.3 (hata) — **sessiz boş dönülmez** |
| Servisler arası bildirim | Prompt hazır/hata olayı | **PSR-14 event** (9 domain + 3 integration) | Servis↔servis HTTP **yasak** (ADR-039) |
| Agent tarafı | prompt0 her görev başında; prompt1/2/3 yalnız ilkili domain görevinde | `AGENTS.md §14.1` eşlemesi | Vault kuralı — kod loader'ı bu eşlemeyi **ihlal edemez** |
| Veri sağlayıcı | AIEngine skorları | **STUB** (`return []` `:216-225`, `:230-242`) | Loader bu stub'u **prompt yok gibi** değerlendirmez; stub bilinçli olarak işaretli kalır (§2.3) |

- Harita bu tabloyla **başlar**; her yeni servis/prompt eşleşmesi bu ADR'ye değil, `AGENTS.md`/ADR-039 kapsamına eklenir (SRP) — bu ADR yalnız **kuralı** koyar: *bir servis yalnız kendi promptunu ister, başkasınınkini HTTP ile çekmez, prompt durumunu event ile yayınlar.*

### §2.3 (c) Hata Davranışı — Sessiz Başarısızlık Yasak (ADR-030 + Langfuse)

| Durum | Davranış |
|---|---|
| Şablon bulunamadı / kayıt yok | **Açık hata**: işaretli fallback stub (`⚠️ FALLBACK …`) + `log.md` append kaydı; prompt `is_fallback` benzeri bayrakla işaretlenir |
| Değişken eksik | `validatePrompt()` (`:156-186`) devreye girer → hata döner (sessiz tamamlanmaz) |
| Enjeksiyon denemesi | 7 pattern (`:27-35`) reddeder → hata + log |
| API/ağ hatası | ADR-030 API-birincil kuralı: hata çağırana iletilir; `return []`-tarzı **sessiz boş dönüş loader'da yasak** |
| AIEngine skor stub'u | Mevcut `return []` **korunur ama işaretlidir** (mevcut davranış, bu ADR kapsam dışı) — loader bunu başarısızlık saymaz |

> Bu tablo Langfuse "Guaranteed Availability" seçeneğiyle (startup pre-fetch **veya** fallback; sessiz devam yok) ve ADR-030 yasağıyla **aynı** sonuca varır.

### §2.4 (d) Cache — Lazy/Cache-Aside Varsayılan + Opsiyonel Isıtma (ADR-007)

| Karar | Değer |
|---|---|
| Varsayılan | **Lazy (cache-aside)**: ilk okuma kaynaktan, sonrası cache — ADR-007 varsayılanı |
| Key | `app:v{version}:{domain}:{prompt-adi}` (ADR-007 namespace) |
| TTL | **Zorunlu**; şablon içeriği (immutable) uzun TTL, "hangi sürüm prod?" etiketi **kısa TTL** (MLflow immutable/alias ayrımının ADR-007 karşılığı) |
| Katman | Mevcut `MemorySystem` L1/L2/L3 (`CACHE_TTL :33-36`: 0 / 3600 / 86400) — **aynı değerler** kullanılır, yeni katman eklenmez |
| Hot path | Ağ isteği **yok** (Langfuse: pre-fetch opsiyoneldir); boot'a zorunlu preload **konmaz** |
| Isıtma | Opsiyonel ilk-çağrı ısıtması (§2.1 kademe 2) — başarısız olursa **loader çalışmaya devam eder** (lazy fallback), boot engellenmez |

### §2.5 (e) Prompt Konsolidasyonu — Tek Domain Seti (ADR-035)

- Loader'ın kaynağı **tek konsolide prompt setidir** (ADR-035 adımı: "8 parçalık envanter → tek domain seti"); bugün 3 yerde dağınık (§1.1-B) — **konsolidasyon loader yazımından önce/paralel kapatılır**, loader dağınık üç kaynağı ayrı ayrı okumaz.
- Kapsam dışı bırakılanlar: `.ai/ui-design/prompt/**` 51 dosya (UI tasarım promptu, LLM sistem promptu **değildir**), `.claude/skills/prompt-maker/` (üretim aracı), `.ai/archives/prompt*` 10 dosya (arşiv — kaynak değil, geçmiş).
- Envanter sayıları bu ADR'de **raporlanır** (2 + 10 + 51 + 4); envanterin kendisi ve eval/CI işleri ADR-035'in tekelindedir (SRP).

### §2.6 Neden Bu Seçenek?

Zincirin tek doğal ucu zaten **belli**: runtime'a ulaşan tek kanal `PromptEngine` ve o hiç örneklenmiyor — yani "loader" aslında mevcut sınıfı **ilk çağrıda ayağa kaldırmaktır**, yeni bir alt sistem kurmak değil. Boot'a eklenen her iş (preload) bu kodda karşılığı olmayan bir yavaşlatma getirirken (Langfuse: pre-fetch opsiyoneldir; LangChain: kurulum bir kez yapılır), `return []` stub'u ve eksik prompt senaryosu ADR-030 yasağıyla **çatışıyor** — hata yolu olmadan loader, sessiz başarısızlık fabrikasına döner. Cache tarafında ADR-007'in L1/L2/L3 + TTL şeması **zaten yazılı**, yeni şema gerekmez. Yükümlülük için ADR-039'un PSR-14 kanalı ve `AGENTS.md §14.1` eşlemesi hazırdır — haritayı sıfırdan icat etmek yerine mevcut iki kayda bağlamak SSOT'u korur. Konsolidasyon (e) olmadan (a) yalnızca birinci kanalı bağlar, iki ölü kanalı bırakır.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Status quo (hiç dokunmama)** — 4 heredoc + okunmayan dosyalar | Sıfır iş, sıfır risk | Yükümlülük/hata/cache soruları cevapsız kalır; `return []` sessizliği sürer; dosya promptları ölü ağırlık olarak kalır (3 yerde dağınıklık) | §1.2'deki dört sorun çözülmez; ADR-030 yasağı ihlal edilmiş hâlde durur |
| 2 | **Tam preload (boot'ta tüm promptlar + ısıtma)** | İlk çağrı sıfır gecikme, hata erken yakalanır | `RuntimeBootstrap`'e ağır iş ekler (boot yavaşlar), boot'a AI bağımlılığı girer; dosya promptlarının runtime'a bağlanması gerekir → `file_get_contents` yolu açılır | Langfuse: pre-fetch **opsiyoneldir**, hot path dışındadır; boot ön koşulu (§1.4) ihlal edilir |
| 3 | **Harici prompt registry servisi** (MLflow/Langfuse/PromptLayer tarzı uzak registry) | Sürüm/label/rollback/eval olgunluğu, ekip iş birliği | CoreMusic tek süreçli PHP; yeni ağ bağımlılığı + yeni servis + yeni keyvurgusu; ADR-039 servis sayısını büyütür; hot path'e ağ getirir | Web araştırması bu kalıbı **büyük/dağıtık ekip** için öneriyor; CoreMusic'te karşılığı ADR-007 cache + dosya envanteri — bağımlılık maliyeti gereksiz (YAGNI) |
| 4 | **Dosyadan runtime okuma** (`.ai/prompts/*` `file_get_contents` ile) | Prompt metni kod dışı, düzenlenebilir | Bugün hiçbir PHP bu dosyaları açmıyor → yeni okuma yolu + I/O hatası yüzeyi; vault dosyası değişirse kod sessizce farklı prompt üretir (güvenilmez sürüm); REDACTED/vault içeriği runtime'a taşınır | §1.1-A'daki 0 kanıt bu yolu hiç kurmamış; sürüm/tam determinizm (prefix cache) için kod içi/üretilmiş şablon daha güvenli |
| 5 | **Servis↔servis prompt HTTP çekme** (loader başka servisten prompt ister) | Merkezi tek nokta görünümü | ADR-039 servis↔servis HTTP **yasak**; ağ hatası + gecikme hot path'e girer | Frozen ADR ihlali → doğrudan reddedildi (§1.4 kısıt 3) |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Prompt yükleme zinciri **tanımlı ve gözlemlenebilir** hâle gelir: boot tembel, ilk çağrı hazır, kurulum log'da — "prompt nereden geliyor" sorusunun cevabı tek satırda.
- **Hata yolu kapanır:** eksik prompt/eksik değişken/artı enjeksiyon artık açık hata + log üretir; ADR-030 yasağı loader'da da geçerli olur.
- **Cache ADR-007'e oturur** — yeni şema yok, `MemorySystem` L1/L2/L3 + zorunlu TTL aynen kullanılır; ilk çağrı maliyeti tek seferli emilir.
- Yükümlülük haritası mevcut iki SSOT'a (ADR-039 + `AGENTS.md §14.1`) bağlanır — **ikinci bir harita kaynağı doğmaz**.
- Konsolidasyon (e) ile üç yerdeki dağınıklık tek sete iner; loader tek kaynaktan okur (ADR-035 hedefi ilerler).
- Harici bağımlılık **yok** (ADR-001): mevcut PromptEngine/MemorySystem/ToolCalling üçlüsü üzerine kurulur.

### 4.2 Olumsuz Sonuçlar

- **İlk çağrı gecikmesi** vardır (tek seferlik kurulum + ilk cache yazımı) — ölçülmezse kullanıcıya görünür.
- Loader davranışı **PromptEngine'in iç yapısına bağlıdır** (`registerDefaultTemplates` heredoc'ları) — şablonlar konsolide edilene kadar "tek kaynak" iddiası **kısmidir** (dosya tarafı runtime dışı kalır).
- **Hata yüzeyi büyür:** açık hata + log yazımı, sessiz boş dönüsten daha fazla dal ve test gerektirir.
- `AIEngine` stub'u (`return []`) **duruyor** — loader kurulsa bile öneri çıktısı boş kalmaya devam eder (bu ADR'yi bekleyen dış bağımlılık).
- Envanter drift riski: ADR-035 ile bugünün sayıları farklı (1+7 → 2+10) — **KAPSAM FARKI** olarak izlenir, kapanması envanter işidir.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **Sessiz stub sızıntısı**: loader `return []`/boş string alışkanlığını sürdürür → ADR-030 ihlali | 3 (Olası) | Yüksek (gizli arıza) | §2.3 hata tablosu zorunlu + test: eksik şablonda **boş dönüş yok**, log satırı **var** (§5.1 adım 4) |
| 2 **Boot'a sızma**: preload işi `RuntimeBootstrap`'e eklenir → boot yavaşlar/bağımlılık girer | 2 (Mümkün) | Orta (başlangıç gecikmesi + kırılganlık) | §1.4 boot ön koşulu + §2.1 kademe 1: RuntimeBootstrap'e AI satırı **eklenmez**; kod incelemesi kapısı |
| 3 **Cache şeması ihlali**: TTL'siz key / namespace'siz key → çakışma, bayat prompt | 3 (Olası) | Orta-Yüksek (yanlış prompt servisi) | ADR-007 key + **zorunlu TTL**; `MemorySystem` `CACHE_TTL :33-36` değerleri aynen kullanılır; key denetimi §5.1 adım 3 |
| 4 **Dağınıklık/loader çatışması**: loader tek seti beklemez, üç kaynağı karışık okur | 3 (Olası) | Orta (çift prompt, sürüm şaşkınlığı) | §2.5: konsolidasyon loader ile **paralel/önce**; loader'ın kaynağı tek set — üç kaynak okunmaz |
| 5 **İlk çağrı gecikmesi** kullanıcıya yansır (özellikle ilk AI çağrısı) | 3 (Olası) | Düşük-Orta (latans) | Tek seferlik kurulum + cache (§2.4); ölçüm §5.1 adım 5; gerekirse opsiyonel ısıtma |
| 6 **Vault drift**: index.md:90 `[[../../raw/brain.md]]` slug düzeltmesi + `.ai/raw/AGENTS.md §25.2` "workflows = 0" stale kaydı | 4 (Çok olası — zaten mevcut) | Düşük (link/katalog kirliliği) | Report-only kayıt (§5.1 adım 8) + bir sonraki vault reset'i; bu ADR'de **düzeltilmez** (In-Place Refactoring) |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **Loader kapatılırsa** sistem bugünkü davranışına döner: 4 heredoc şablon koda gömülü kalır, `PromptEngine` ilk çağrıda örneklenmese de `registerDefaultTemplates()` erişilebilir durumdadır — davranış **aynı** (§2.1 kademe 1'e geri dönüş).
2. **Hata yolu gevşetilirse** (sessiz dönüşe izin verilirse) karar (c) ihlal edilmiş sayılır → **revert edilir**, loader kapatılır (yarı-uygulama tam uygulamadan kötüdür).
3. **Cache açılmazsa** yalnız ilk çağrı gecikmesi yaşanır; doğruluk etkilenmez (lazy okuma zaten kaynaktan doğrudur).
4. **Konsolidasyon ertelenirse** loader yalnız kod içi 4 şablonu bağlar, dosya promptları runtime dışı kalır — bu **kapsam daralmasıdır**, hata değildir (§2.5).
5. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler (001–037) etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Zincir envanteri:** §1.1-A/B/C tabloları disk kanıtıyla teyit edilir (`new PromptEngine(` = 0, dosya yükleyen 0, envanter 2/10/51/4) — sayılar ADR-035 ile karşılaştırılır, fark **KAPSAM FARKI** olarak işaretlenir | Vault Steward | 0.25 gün ✅ (2026-09-29 taraması yapıldı) |
| 2 | **İlk çağrı kurulumu:** `PromptEngine` ilk kullanımda örneklenir + `registerDefaultTemplates()` (`:193-264`) çağrılır; kurulum tek satır `log.md` append ile kaydedilir; **`RuntimeBootstrap`'e satır eklenmez** | Backend Architect | 0.5 gün |
| 3 | **Cache bağlama:** ilk kurulum sonucu ADR-007 key'i (`app:v{version}:{domain}:{prompt}`) + **zorunlu TTL** ile `MemorySystem` L1/L2/L3'e (`CACHE_TTL :33-36` değerleri) yazılır; TTL'siz key **kod incelemesinde reddedilir** | Backend + Data | 0.5 gün |
| 4 | **Hata yolu:** eksik şablon → işaretli fallback stub + `log.md` append; `validatePrompt()` (`:156-186`) eksik değişken hatası üretir; **test:** senaryoda sessiz boş dönüş **0**, log satırı **1** (ADR-030 kapısı) | Backend + QA | 1 gün |
| 5 | **Yükümlülük haritası denetimi:** `AIWorkflow` 5 workflow (`:49,99,146,198,240`) → PromptEngine bağımlılıkları `AGENTS.md §14.1` + ADR-039 (`:117-127`) ile karşılaştırılır; servis↔servis HTTP **0** teyit edilir | Backend + MO | 0.5 gün |
| 6 | **Ölçüm:** ilk çağrı gecikmesi (kurulum + ilk cache yazımı) ölçülür ve §4.2-1'e karşı kaydedilir; **ölçümsüz adım kapanmaz** | QA | 0.25 gün |
| 7 | **Konsolidasyon hattı (paralel):** tek domain prompt seti hazırlanır (ADR-035 §5.1 adımı) — loader bu sete bağlanır, üç kaynak ayrı okunmaz | Vault Steward | ADR-035 kapsamı (bu ADR'de yalnız kural: §2.5) |
| 8 | **Ertelemeler (report-only):** (i) `.ai/.decisions/index.md:90` slug wiki-link düzeltmesi → **bir sonraki vault reset'i** (bu işlemde dokunulmadı); (ii) `.ai/raw/AGENTS.md §25.2` "workflows = 0 dosya" stale kaydı → vault reset'te gözden geçirilir; (iii) `AIEngine` stub (`return []`) → ayrı AI kararı (ADR-030 kapsamı) | Vault Steward + Tech Lead | 0.2 gün |
| 9 | **Debate 3 tur** tamamlanır → §7 Debate/Tech Lead satırları güncellenir (⏳ → ✅) | Vault Steward | 0.5 gün ✅ (2026-09-29 debate 3/20 tamamlandı — 18/2/0 KABUL) |

**Toplam ≈ 3.7 gün** (adım 4 + 6 kapı — bu ikisiz loader yayına alınmaz; adım 9 debate'i bekler).

### §5.2 Geri Dönüş Planı

1. **Adım 2 tersi:** ilk-çağrı kurulumu kaldırılır → `PromptEngine` yalnız ihtiyaç anında `new` edilir/edilmez; sistem bugünkü "hiç örneklenmeme" durumuna döner — **davranış farkı yok** (4 heredoc zaten kodda).
2. **Adım 3 tersi:** cache yazımı tek bayrakla kapatılır → okuma doğrudan kaynaktan yürür; yalnız ilk çağrı gecikmesi geri gelir, doğruluk **etkilenmez**.
3. **Adım 4 (geri alınamaz şart):** hata yolu **kaldırılmaz** — kaldırılırsa karar (c) ve ADR-030 ihlal edilir; yalnız log detayının seviyesi gevşetilebilir (maskeleme/REDACTED korunur), açık hata **kalır**.
4. **Adım 5/6 geri alma yoktur:** bu adımlar yalnız **denetim/ölçüm** içerir — geçici pasifleştirme mümkün, kalıcı silme **yasak** (kanıt kaybı).
5. **Adım 7 konsolidasyon geri alınırsa** loader yalnız kod içi 4 şablona döner (§4.4-4 kapsam daralması) — kırık zincir yok.
6. **Adım 8 geri alma yoktur:** ertelenen işler henüz **yapılmadı** (index.md/AGENTS.md/AIEngine stub'a dokunulmadı).
7. **Adım 9 debate reddederse** karar **Draft/Review**'a döner: (a)–(e) kalemleri uygulanmaz sayılır, adımlar 2–7 durdurulur (dosya adı değişmez, In-Place Refactoring).
8. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; frozen ADR'ler (001–037) etkilenmez.

### §5.3 Debate Şartları (Kabul Koşulu — 3 şart)

| Şart | İçerik | Bağlantı |
|---|---|---|
| **1a** | **Bootstrap lazy kayıt:** ilk prompt çağrısında `PromptEngine` örneklenir + `registerDefaultTemplates()` (`:193-264`) çağrılır; **ilk çağrı testi** ile `new PromptEngine(` 0 → 1 kanıtlanır; `RuntimeBootstrap`'e satır **eklenmez** | §5.1 adım 2 · §2.1 |
| **1b** | **Konsolidasyon:** envanter 3 yerden tek klasöre iner (ADR-035 hizası) + **gerçek sayı SSOT** olarak yazılır (bugün 2 + 10 + 51 + 4); loader dağınık üç kaynağı ayrı okumaz | §5.1 adım 7 · §2.5 · [[ADR-035-system-prompt-engineering]] |
| **2** | **Stub dürüst etiketi:** `AIEngine::getCollaborativeScores():216-225` / `getContentBasedScores():230-242` `return []` kalır ama **STUB etiketi** + **ADR-030'a dönüş kapısı** (açık hata/log) yazılı olur; loader bu stub'u başarı saymaz | §2.3 · §5.1 adım 8-iii · [[ADR-030-ai-strategy-core]] |
| **3** | **Hata yolu testi:** prompt yok / fallback senaryosu test edilir — sessiz boş dönüş **0**, `log.md` satırı **1** (sessiz fail'i yakalar) | §5.1 adım 4 · §4.3 risk 1 |

> **Kabul koşulu:** 3 şart (1a–1b, 2, 3) kapanmadan loader **yayına alınmaz**; debate 3. turu **18/2/0 → KABUL** (2026-09-29).

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../raw/AGENTS.md]] | Agent registry — §14.1 prompt→agent eşlemesi (karar (b) kaynağı), §25.2 stale "workflows = 0" kaydı (§5.1 adım 8-ii) |
| [[../../raw/WORKFLOW.md]] | Süreçler, fazlar |
| [[../../raw/brain.md]] | Mimari karar özeti |
| [[../../index.md]] | Master katalog |
| [[../../raw/keys.md]] | Keyword haritası |
| [[../../raw/MEMORY.md]] | Session hafızası |
| [[../../log.md]] | Audit trail (append-only — bu ADR için tek satır append) |
| [[../../raw/glossary.md]] | Terimler (Prompt Loader, cache-aside, fallback stub, prefix cache) |
| [[../index.md]] | Karar dizini — **ADR-049 satırı VAR (satır 90); slug wiki-link düzeltmesi reset'e ertelendi (§5.1 adım 8-i)** |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[ADR-001-vanilla-js-itcss]] | Framework yasağı — harici registry/alt sistem reddi (§3-3) |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination — §1.1 dürüst etiket + `⚠️ VERIFICATION REQUIRED` kullanımı |

| Dosya | İlişki |
|-------|--------|
| [[ADR-007-cache-namespace]] | Karar (d): L1/L2/L3 + key namespace + zorunlu TTL + cache-aside |
| [[ADR-030-ai-strategy-core]] | Karar (c): sessiz başarısızlık yasağı + API-birincil; `AIEngine` `return []` stub bulgusu (`:38,41`) |
| [[ADR-035-system-prompt-engineering]] | Karar (e): tek domain seti + 6 standart; envanter KAPSAM FARKI (`:76-77` → bugün 2 + 10) |
| [[ADR-036-multi-project-prompt-maker]] | prompt-maker skill — §1.1-B envanter 3. yer (üretim aracı) |
| [[ADR-039-7-service-platform-architecture]] | Karar (b): 11 servis tablosu `:117-127` + PSR-14 + servis↔servis HTTP yasağı |
| [[ADR-048-view-transition-api-integration]] | Önceki ADR — biçim/kanıt etiketi + index.md satır formatı referansı |
| `shared/src/AI/PromptEngine.php:25,27-35,48-85,101-135,156-186,193-264` | Yüklenecek 4 şablon + doğrulama/enjeksiyon katmanı — **kod kanıtı (düz metin, wiki-link değil)** |
| `shared/src/AI/AIWorkflow.php:49,66,99,146,172-176,198,240` · `AIEngine.php:145-163,216-242,322-334` · `MemorySystem.php:33-36,129` | Çağrı zinciri + stub + cache TTL — **kod kanıtı (düz metin)** |
| `shared/src/Bootstrap/RuntimeBootstrap.php` (21 satır) | Boot'ta AI adımı **0** kanıtı — **kod kanıtı (düz metin)** |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü — **düz metin** (vault dışı) |
| `.ai/.workflows/` (2 dosya) | §1.1-D `AGENTS.md §25.2` "0 dosya" staleness kanıtı — **düz metin** |

> **Wiki-link doğrulaması:** Yazımdan önce `Test-Path` ile diskte doğrulandı (2026-09-29) — kod bloğu/alıntı içi occurrence'lar hariç **43 bağlantı örneği / 21 benzersiz hedef: 21/21 diskte mevcut, eksik 0** (2026-09-29 debate eki sonrası yeniden sayıldı). (Ham metinde 48 örnek/22 hedef görünür; fark, `index.md:90` satırının **düz alıntısı** olan 5 `[[../../raw/brain.md]]` örneğidir — hepsi kod alıntısı içindedir, bağlantı sayılmaz.) Diskte **olmayan** hedefe wiki-link **yazılmamış**; doğrulanamayan iddialar (ADR-033/ADR-040 için slug'lar `Test-Path` **False**) düz metin bırakılmıştır. `ADR-049-*` hedefi bu dosyanın kendisidir. `index.md:90` içindeki `[[../../raw/brain.md]]` biçim hatası **raporlanır, düzeltilmez** (report-only — §5.1 adım 8-i).

### §6.1 Debate Şartı Bağlantıları

| Şart | İlgili Doküman | İlişki |
|---|---|---|
| 1a | [[ADR-007-cache-namespace]] · §5.1 adım 2 | İlk çağrıda lazy kayıt + ADR-007 cache; boot'a satır eklenmez |
| 1b | [[ADR-035-system-prompt-engineering]] · §5.1 adım 7 | Tek klasör konsolidasyonu + gerçek envanter sayısı SSOT |
| 2 | [[ADR-030-ai-strategy-core]] · §2.3 | Stub dürüst etiketi + sessiz fail → açık hata/log dönüş kapısı |
| 3 | [[../../log.md]] · §5.1 adım 4 | Hata yolu testi kanıtı (append-only audit trail) |

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI** — 3 tur / 20 persona · Sonuç: **18 kabul / 2 çekimser / 0 red → KABUL** (2026-09-29).

| Tur | Tür | Sonuç |
|---|---|---|
| 1 | 20 persona bulgu turu (11 kaynak / 6 sorgu) | 15 kabul/neutral + 4 uyarı: DevOps (örneklenme 0 şart), QA (hata yolu), Critic (envanter + stub şart) |
| 2 | İtiraz → çözüm (4 itiraz → 4 şart) | ① `new PromptEngine(` = 0 → bootstrap lazy kayıt + ilk çağrı testi (**1a**); ② envanter 3 yer + ADR-035 **KAPSAM FARKI** → tek klasör konsolidasyonu + gerçek sayı SSOT (**1b**); ③ `AIEngine` `return []` → stub dürüst etiketi + ADR-030 dönüş kapısı (**2**); ④ hata yolu testi yok → prompt yok/fallback testi (**3**) |
| 3 | Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |

**Tur 1 bulgu özeti:** yükleme zinciri **0** (`RuntimeBootstrap` 21 satır = timezone + error_reporting, boot'ta AI adımı yok; `new PromptEngine(` = 0, `new AIWorkflow(` = 0 — vendor hariç); prompt envanteri **3 yer** (`.ai/prompts/` 2 dosya 10140 + 18158 b, `.ai/archives/prompt*` 10, `.ai/ui-design/prompt/**` 51, PromptEngine gömülü 4); `AIEngine::getCollaborativeScores():216-225` + `getContentBasedScores():230-242` → `return []` (**ADR-030 sessiz fail ihlali riski**); `PromptEngine::registerDefaultTemplates():193-264` 4 heredoc — PHP dosya promptunu okumuyor, **hiç örneklenmiyor**; ADR-035 "1+7" vs bugünkü sayı **KAPSAM FARKI**; `index.md:90` report-only; `AGENTS.md §25.2` "workflows = 0" stale.

**3 şart (kabul koşulu):** (1) bootstrap kaydı + konsolidasyon → **§5.3 1a–1b** · (2) stub dürüst etiketi → **§5.3-2** · (3) hata yolu testi → **§5.3-3**. Tech Lead **✅** (2026-09-29); Arch Lead ⏳ (bu işlemde güncellenmedi).

---

*1.0.0 | 2026-09-29 | Created*
*Authority: SSOT — CoreMusic Startup Prompt Loader (ADR-049)*
*Mode: Red Team · Human Mode · Truth Mode*
