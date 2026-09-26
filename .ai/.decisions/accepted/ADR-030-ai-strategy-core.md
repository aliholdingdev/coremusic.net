---
title: "CoreMusic — ADR-030: AI Strategy Core (Kullanım Alanları · API Birincil + Yerel/Edge Opsiyonel · Token Bütçesi/Kota · PII Gizlilik — Eğitime Veri Yok · Human-in-the-Loop)"
type: adr
category: ai
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-030 Karar Metni (SSOT)
governation: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-030: AI Strategy Core (Kullanım Alanları · API Birincil + Yerel/Edge · Token Bütçesi · PII Gizlilik · Human-in-the-Loop)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-030'u sıfırdan yaz. AI alanı."; kapsam **kullanıcı onaylı**: **(a) kullanım alanları** — müzik önerisi/keşif, asistan sohbet (LLM), transkript/etiketleme (çevrimdışı iş) · **(b) model seçimi** — **API birincil + yerel/edge opsiyonel** (büyük model API'de, küçük/hassas iş yerel — yerel model **PLANNED**) · **(c) maliyet/kota yönetimi** — token bütçesi + cache (ADR-007) + rate limit (ADR-013) · **(d) gizlilik** — PII (ADR-022): eğitime veri yok, redaksiyon · **(e) human-in-the-loop** — öneri açıklayabilirlik + asistan yanıtı doğrulama · **altyapı:** harici LLM API birincil (`shared/src/AI/ToolCalling.php` curl istemcisi kodda — dürüst etiket: **genel HTTP aracı, LLM API çağrısı değil**), yerel model opsiyonel **PLANNED**, RAG/embedding **PLANNED (kod 0)** · debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)**, Tech Lead **✅ (2026-09-25)**)
**İlgili ADR'ler:** [[ADR-022-database-hardened-security]] (PII — app-level AES-256-GCM **PLANNED**, OAuth sarmalayıcı `OAuthManager.php:244-291` IMPLEMENTED → bu ADR'nin "eğitime veri yok + redaksiyon" maddesi ADR-022'nin PII şifrelemesinin **üstünde** koşar: veri şifrelenmeden LLM'e çıkmaz; dosya diskte VAR ✅) · [[ADR-007-cache-namespace]] (cache katmanı — `shared/src/Cache/` = Apcu/Memory/PageCache, Redis adapter YOK → token/yanıt cache'i bu adapter'larla başlar, semantic cache PLANNED; dosya diskte VAR ✅) · [[ADR-013-rate-limiting-apcu]] (AI kotası — `RateLimiterMiddleware.php:30-32,48-56` fail-open + 429/`Retry-After` **IMPLEMENTED** → LLM istek/token limiti bu mekanizmanın üstünde; dosya diskte VAR ✅) · [[ADR-020-api-public-security]] (LLM API key'leri — prefix+hash+scope+rotasyon deseni; `getenv()` yerine ADR-015 env Config; dosya diskte VAR ✅) · [[ADR-028-anti-ban-system]] (outbound HTTP — `ToolCalling.php:253-262` 3. curl istemcisi olarak sayılır, UA/backoff kapsamı ADR-028'de; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · [[ADR-024-ecosystem-modular-docs]] (wiki-link disk kanıtı kuralı) · karar dizini [[../index]] **satır 67** `[[ADR-030-ai-strategy-core]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-030-ai-strategy-core` karar dizini `../index.md:67`'de **rezerve boş slottur** (ADR-026/027/028/029 aynı istisnayı `:63`/`:64`/`:65`/`:66`'da kaydetmişti). Ek kanıt: `keys.md:265` "ADR-030 | AI strategy, core | AI", `keys.md:186` "AI strategy, prompt engineering", `.ai/index.md:647` "decisions/accepted/ADR-030-ai-strategy-core | AI strategy core | AI", `brain.md:985` "ADR-030 | AI öneri motoru", `.templates/adr/adr-index.md:101` "30 | ADR-030 | AI öneri motoru | 🔵 ai", `architecture/index.md:99` "K4 | Yapay Zeka | 50 | 14 | ADR-030" — altı katalog kaydı bu numarayı bağlar.

---

## 1. Bağlam (Context)

CoreMusic'in K4 Yapay Zeka katmanı vault'ta **14 dosyalık spec** (50 bileşen) ve **6 tabloluk şema** olarak var; kod tarafında `shared/src/AI/` içinde **7 sınıf + 6 contract** yazılmış ama **hiçbiri instantiate edilmiyor** (`.ai/reports/unused-files-report.md:131-138` → hepsi PLANNED; `broken-code-scan.md:270` ToolCalling "DEAD CODE"). En kritik nokta: kodda **hiçbir LLM sağlayıcı çağrısı yok** — `openai|anthropic|gemini|chat/completions|gpt-4|claude-3` taraması tüm `*.php` dosyalarında **0 eşleşmedir**; `ToolCalling.php:253-262`'deki curl istemcisi **genel amaçlı `http-request` aracıdır** (LLM API'ye giden bir çağrı **değildir**). Aynı anda AI stratejisinin beş çerçevvesi — kullanım alanları, model seçimi, maliyet/kota, gizlilik, insan denetimi — hiçbir belgede tek karar altında toplanmamıştır. Bu ADR; beş çerçevenin tamamını, altyapı tercihini (API birincil / yerel opsiyonel) ve risk/fallback zincirini tek belgede bağlayıcılaştırır. Karar **mimari çerçevedir, kod taahhüdü değildir** (kod kanıtı §1.1).

### 1.1 Mevcut Durum

**A) KOD KATMANI — LLM entegrasyonu YOK; AI sınıf dosyaları var ama bağlı değil:**

| Tarama (grep) | Sonuç | Dosya:Ssatır |
|---|---|---|
| `openai\|anthropic\|gemini\|chat/completions\|gpt-4\|claude-3\|\bllm\b` (`*.php`) | **0 eşleşme** — hiçbir LLM API uç noktası çağrılmıyor | repo geneli PHP |
| `embedding\|vector_db` (`*.php`) | **0 eşleşme** (yorum satırı hariç) | repo geneli PHP |
| AI sınıf dosyaları | **7 sınıf + 6 contract DOSYA OLARAK VAR**: `AIEngine.php` (12.637 B), `AIOrchestrator.php`, `AIWorkflow.php`, `KnowledgeBase.php`, `MemorySystem.php`, `PromptEngine.php`, `ToolCalling.php` | `shared/src/AI/` |
| AI sınıflarını instantiate/import eden kod | **0** — yalnızca kendi iç importları (`use CoreMusic\AI\Contracts\…`) | repo geneli; `.ai/reports/unused-files-report.md:131-138` "Hiçbir instantiate yok → **PLANNED**" |
| `ToolCalling` curl istemcisi | **VAR** — `http-request` aracı: `curl_init($url):253`, `curl_setopt_array:254-259`, `POSTFIELDS:262`, `curl_exec:265` → **genel HTTP istemcisi; LLM API çağrısı değil** (uç nokta parametrik `url`, LLM modeli/anahtar referansı 0) | `shared/src/AI/ToolCalling.php:241-280` |
| LLM çağıran frontend | **0** — `openai\|anthropic\|llm\|assistant\|chatbot` (`*.js`) 0; `recommend\|suggest\|/ai/` (`assets.coremusic.net/js`) 0; `recommend\|AIEngine\|/ai/` (`home.coremusic.net`) 0 | assets + home |
| Öneri motoru gövdesi | `AIEngine::getRecommendations():56-83` hibrit iskelet (collaborative 0.6 + content 0.4, çeşitlilik filtresi %30, MIN_SCORE 0.7) **AMA** `getCollaborativeScores():219,224` ve `getContentBasedScores():241` **`return []` (boş stub)** → bugün öneri **boş döner** | `shared/src/AI/AIEngine.php` |
| Prompt/LLM hazırlık kodu | `PromptEngine.php` — `recommendation` şablonu `:195-198` ("Müzik öneri promptu"), token sayma `:75`, tahmini maliyet (`PromptEngineInterface.php:21` `estimatedCost`), prompt-injection tarama `:163-164`, hassas veri tarama `:177-178` → **kod var, hiçbir yere bağlı değil** | `shared/src/AI/PromptEngine.php` |
| RAG/augmentation | `KnowledgeBase.php:48` "Basit keyword matching (**gerçek implementasyonda vector cosine similarity**)", `:77` "Augmentation — prompt'a bağlam ekle" → **yorum = niyet, kod 0** | `shared/src/AI/KnowledgeBase.php` |

**B) ŞEMA KATMANI — AI tabloları IMPLEMENTED:**

| Nesne | İçerik | Durum | Dosya:Ssatır |
|---|---|---|---|
| `user_preference_profiles` | tercih profilleri | **IMPLEMENTED (şema)** | `.ai/.sql/mysql/coremusic_ai.sql:31` |
| `listening_features` | dinleme özellikleri | **IMPLEMENTED (şema)** | `coremusic_ai.sql:55` |
| `recommendation_history` | `algorithm IN ('collaborative','content_based','hybrid','ai_curated')` CHECK `:97` | **IMPLEMENTED (şema)** | `coremusic_ai.sql:80` |
| `audio_features` | ses özellikleri | **IMPLEMENTED (şema)** | `coremusic_ai.sql:109` |
| `model_versions` | `model_type IN ('recommendation','audio_analysis','nlp','classification')` `:157` | **IMPLEMENTED (şema)** | `coremusic_ai.sql:139` |
| `training_jobs` | eğitim işleri kuyruğu | **IMPLEMENTED (şema)** | `coremusic_ai.sql:166` |
| Öneri/LLM **yazan** Controller/Service/Repository | **0 dosya** | **PLANNED** | grep 0 |
| Embedding/vektör **tablosu** | **YOK** (6 tabloda embedding kolonu yok) | **PLANNED** | grep 0 |

**C) VAULT SPEC + KATALOG KATMANI:**

| İddia | Vault kanıtı | Kod karşılığı | Etiket |
|---|---|---|---|
| K4 katmanı: 50 bileşen / 14 dosya, "Sorumlu Agent: AI Engineer" | `architecture/k4-yapay-zeka/README.md:20-23`, bileşen haritası `:45-57` (K4-05 AI Theme = **LLM**, K4-06 Edge AI = **ONNX Runtime**, K4-08 Similarity = cosine) | ONNX/LLM/Whisper **0** | **PLANNED** |
| Öneri mimarisi: Collaborative + Content-Based + Hybrid + Real-time Learning | `k4-yapay-zeka/recommendation-engine.md:20,94,181,279`; `:373` bağımlılık "torch 2.1+ Neural embeddings" | `AIEngine` stub (`return []`) | **PLANNED (iskelet var, veri yok)** |
| Sesli asistan / NLU / TTS + Whisper STT | `k4-yapay-zeka/voice-assistant.md:120,263`, `speech-to-text.md:20` "Whisper Entegrasyonu" | Whisper/NLU **0** | **PLANNED** |
| Temel ilkeler: Offline-First, Edge AI, Privacy, **Explainable** | `k4-yapay-zeka/README.md:33-39` | — | **PLANNED (spec — bu ADR'ye bağlanır)** |
| AI katmanı envanteri (ADR-030 atıfı) | `shared/AGENTS.md:35` "src/AI/ + Contracts (ADR-030)", `shared/src/AI/CLAUDE.md:21` "Yapay zeka altyapısı (ADR-030)" | 7 sınıf var, instantiate 0 | **kod dosyaları mevcut / kullanım PLANNED** |
| Karar slotu | `decisions/index.md:67` `[[ADR-030-ai-strategy-core]]`, `index.md:647`, `keys.md:186,265`, `brain.md:985`, `adr-index.md:101`, `architecture/index.md:99` (K4 → ADR-030) | — | **IMPLEMENTED (kayıt)** |

**Sonuç etiketi:** **IMPLEMENTED:** `coremusic_ai.sql` 6 AI tablosu (öneri geçmişi, model versiyonları, eğitim işleri dahil), `ToolCalling.php:241-280` genel HTTP/curl aracı (LLM **değil**), `PromptEngine` prompt şablonu + token/maliyet sayacı + injection taraması (bağlı değil), `AIEngine` hibrit iskeleti (stub veri), 7 sınıf + 6 contract dosyası (instantiate 0), karar slotu (6 katalog kaydı). **PLANNED:** herhangi bir LLM API entegrasyonu (kod **0**), RAG/embedding/vektör (kod **0**), öneri veri akışı (stub → `return []`), asistan sohbet UI/API, transkript/etiketleme çevrimdışı işi, yerel/edge model (ONNX), PII redaksiyon pipeline'ı. **`⚠️ VERIFICATION REQUIRED`:** (i) `ToolCalling`'in curl'i **LLM çağrısı olarak sayılamaz** → "AI API çağrısı kodda var" iddiası bu yazıda **yoktur** (ADR-020'ye atfedilen bulgu aslında ADR-028:39'da 3 outbound istemciden biri olarak geçer; ADR-020 metninde `ToolCalling` geçmiyor — grep 0); (ii) `unused-files-report.md` PLANNED etiketi ile `broken-code-scan.md:270` "DEAD CODE" etiketi **çelişir** → hangisinin bağlayıcı olduğu §5.1/2'de; (iii) token fiyatı/birim maliyet sayıları ölçülmüş değil; (iv) hangi LLM sağlayıcısının seçildiği **bu ADR'de kararlı değil** (sağlayıcı seçimi §5.1/3, REDACTED: anahtarlar `.env`).

### 1.2 Sorun Tanımı

1. **LLM entegrasyonu yok:** kodda LLM API çağrısı **0** → asistan sohbeti ve LLM destekli öneri bugüne kadar **imkânsız**; `shared/src/AI/` sınıfları kendi kendine duruyor (instantiate 0).
2. **Model seçimi kararı yok:** "büyük model API'de mi, yerelde mi?" hiçbir belgede yazılmıyor; K4 spec'i hem LLM (K4-05) hem Edge AI (K4-06) diyor ama ikisini **ayırıyor değil**, önceliklendirmiyor.
3. **Maliyet/kota kuralı yok:** rate limit (ADR-013) istek sayar, **token saymaz**; LLM'de bir istek 200K token olabilir (§1.3-3) → bugünkü kota LLM faturasını **durdurmaz**; token bütçesi/yazılı limit hiçbir yerde yok.
4. **Gizlilik kuralı yok:** PII (ADR-022) şifrelemesi PLANNED; kullanıcı verisinin/filtrelenmiş metnin **eğitim için kullanılıp kullanılmayacağı**, LLM'e gönderilmeden önce **redaksiyon** yapılıp yapılmayacağı yazılmamış.
5. **İnsan denetimi yok:** önerinin **neden** üretildiği (açıklanabilirlik) ve asistan yanıtının **doğrulanmadan** kullanıcıya çıkması riski hiçbir kararda ele alınmamış (K4 "Explainable" ilkesi spec'te duruyor, karar değil).
6. **RAG/embedding kararı yok:** `KnowledgeBase` "vector cosine similarity"yi yoruma bırakmış; embeddings nerede üretilecek, nerede saklanacak, hangi model — 0 kod.
7. **Fallback yok:** LLM API'si çökerse/pahalılaşırsa/kısılanırsa ne yapılacağı (yerel model mi, klasik öneri mi?) bilinmiyor → özellik tamamen kapanır.
8. **Sağlayıcı bağımlılığı riski yazılmamış:** tek sağlayıcıya doğrudan bağlanılırsa maliyet/erişim/değişiklik riski denetimsiz kalır (§1.3-7).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/anahtar kaynak önce (OWASP GenAI, EDPB/ICO rehberleri, provider resmi dokümanı), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) LLM üretim 2025-26 + model routing, (b) RAG/retrieval kalıpları, (c) LLM maliyet yönetimi/kota, (d) AI gizlilik/GDPR, (e) sorumlu AI/HITL, (f) müzik önerisi, (g) vendor lock-in, (h) yerel/edge model, (i) OWASP LLM riskleri.** Erişim: **9 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "LLM in production 2025 best practices routing small vs large models cost management" · (2) "RAG retrieval augmented generation patterns 2025 embeddings vector search production" · (3) "LLM cost management token budget caching rate limiting production 2025" · (4) "GDPR AI personal data LLM privacy redaction training data opt-out 2025" · (5) "responsible AI human-in-the-loop LLM hallucination evaluation guardrails production 2025" · (6) "music recommendation system 2025 hybrid collaborative filtering LLM cold start" · (7) "LLM vendor lock-in abstraction layer multi-provider OpenRouter LiteLLM portability 2025" · (8) "small language model on-device edge inference 2025 vs cloud API privacy latency tradeoffs" · (9) "OWASP LLM top 10 2025 prompt injection sensitive information disclosure" |
| Web Search **Konusu** | (1) Model routing — ucuz/küçük model vs frontier model ayrımı ve maliyet tasarrufu; (2) RAG: chunking→embedding→vector search→rerank akışı ve üretim kalıpları; (3) token bütçesi, istek-vs-token rate limit, prompt/semantic cache, güvenli retry; (4) GDPR kapsamında LLM eğitim verisi, opt-out, redaksiyon/consent; (5) HITL, guardrails, halüsinasyon ölçümü ve değerlendirme; (6) müzik önerisinde hibrit sistem + cold-start + LLM kullanımı; (7) sağlayıcı soyutlama katmanı (gateway) ile lock-in önleme; (8) SLM/on-device vs bulut API — gecikme-özelik-maliyet üçgeni; (9) OWASP LLM Top 10 (2025): prompt injection, hassas bilgi sızıntısı. |
| Web Search **Bağlam** | **~78 benzersiz adlandırılmış kaynak / 9 sorgu**: **NeuralTrust**, **Inworld** (%70-80 alt görev küçük modelle), **Digital Applied**, **AWS Blog** (multi-LLM routing), **arXiv 2506.06579** (multi-LLM inference), **Delivering Data** (SLM/LLM karar çerçevesi), **Tianpan (RouteLLM %85 yönlendirme / %95 kalite)**, **Splunk**, **Alexander Thamm**, **LinkedIn** (10) · **Pinecone**, **IBM watsonx**, **Medium (embeddings+RAG)**, **Qdrant**, **SurrealDB** (vector), **VentureBeat** (graph-RAG), **Meilisearch** (14 RAG türü), **TowardsAI**, **CrateDB** (9) · **TrueFoundry** (API harcaması $3.5B→$8.4B), **dev.to** (token rate limit), **GetMaxim**, **Trident Ventures** (token budgeting+rate limiting), **Reintech**, **NeuralTrust** (cache %50), **Kong/medium** (token-aware limit), **PremAI** (10) · **IAPP** (EU Digital Omnibus), **Secure Privacy**, **Baker McKenzie (dataprotectionreport)**, **Termly**, **CMS** (2025 veri koruma), **tianpan (noyb cease-and-desist)**, **EDPB** (SP training PDF), **ICO** (AI & data protection rehberi) (8) · **Humans in the Loop**, **Blockchain Council** (RAG+guardrails+HITL), **MLOps community (AI in Production 2025)**, **Fiddler** (HITL evaluation), **Arthur AI** (guardrails), **JHU** (HITL course), **NHImg** (production hallucination), **arXiv RAIL Guard** (8) · **JISem** (cold-start meta-learning), **arXiv 2102.12369** (neural content-aware CF), **7universum** (PinSage hibrit), **aman.ai** (cold start), **ScienceDirect** (FCRA 2025), **Wikipedia** (cold start), **AUAruba capstone 2025**, **Medium** (LLM cold-start) (8) · **Reddit r/LLMDevs** (abstraction eleştirisi), **OpenRouter** (LLM gateway), **TrueFoundry** (lock-in), **GetMaxim**, **SWFTE** (Gartner %70/2028), **Tianpan** (LiteLLM switching costs), **Medium (LiteLLM)**, **Agenta** (gateway listesi), **Agile Monkeys** (5 katman lock-in) (9) · **arXiv 2505.16508** (edge-first inference), **PremAI** (SLM edge), **Tianpan** (hybrid cloud-edge), **ACM 3662006** (edge-cloud SLM/LLM), **Zenriotech** (SLM edge), **LinkedIn checklist**, **Spheron** (hybrid 2026), **Medium** (small LLM) (8) · **OWASP GenAI (LLM01/LLM02:2025)**, **OWASP proje sayfası**, **OWASP PDF v2025**, **Pomerium**, **Promptfoo**, **TrendMicro**, **Aembit**, **LinkedIn (LLM 2026)** (8) |
| Web Search **Kısa Açıklama** | **(1) Model routing:** basit iş küçük/ucuz modele, karmaşık iş büyük modele → faturalar **%40-85** düşer; tipik sohbetin **%70-80 alt işi** küçük modelle karşılanır, tur maliyeti **%80-95** iner (NeuralTrust, Inworld, Digital Applied); RouteLLM ile sorguların **%85'i** ucuz modele yönlendirilip frontier kalitesinin **%95'i** korunabilir (Tianpan). **(2) RAG:** sorgu → aynı embedding modeliyle vektör → similarity search → top-K → rerank → prompt'a bağlam (Pinecone, IBM, Qdrant, TowardsAI); graph-RAG çok-adımlı akışta vector search'ü tamamlar (VentureBeat). **(3) Maliyet/kota:** LLM API harcaması 2024 sonu $3.5B → orta 2025 $8.4B (TrueFoundry); **kota istekle değil tokenle sayılmalı** — tek 200K token'lık istek 50 normal isteğe bedeldir (dev.to); token bütçesi (kullanıcı başına) + rate limit **birlikte** gerekli (Trident); cache >1.024 token'lık promptlarda cache token maliyetini **%50** keser (NeuralTrust). **(4) Gizlilik:** GDPR eğitim verisinde kişisel veri varsa **her zaman** uygulanır; 2024-25'te **uygulama** sıkılaştı (Secure Privacy, Baker McKenzie); sağlayıcının kullanıcı içeriğini eğitmesi **opt-out** ile sınırlandırılmalı (IAPP, noyb/Meta vakası); EDPB "eğitim setinde kişisel veri yasal işlenebilir olmalı" (EDPB PDF); ICO rehberi adalet/dürüstlük şartını netleştirir. **(5) HITL:** üretim ekipleri deterministik doğrulama + semantik skorlama + gerçek-zamanlı guardrails katmanlar (NHImg); HITL değerlendirme rubrikleri ve üretim izleriyle halüsinasyon oranı ölçülür (Fiddler, Arthur, Blockchain Council). **(6) Müzik önerisi:** hibrit (collaborative + content-based) standart; cold-start en zayıf halka (Wikipedia, aman.ai); LLM cold-start'e veri sağlar (Medium); emotion-driven hibrit 2025 çalışması (ScienceDirect). **(7) Lock-in:** gateway/soyutlama katmanı doğrudan sağlayıcıya bağlanmayı kırar (OpenRouter, TrueFoundry); Gartner 2028'de çok-LLM uygulayanların **%70'inin** AI gateway kullanacağını öngörüyor (SWFTE); ama soyutlama katmanının kendisi de bağımlılık yaratır — LiteLLM kullanmak bile switching cost çıkarır (Tianpan, Reddit). **(8) Yerel/edge:** SLM edge'de gecikme-bant-özelik avantajı + **zero token cost** (PremAI, Zenriotech); hibrit karar "gizlilik+gecikme hassas ise yerel, kalite+ölçek ise bulut" (Tianpan, Spheron); arXiv edge-first çalışma maliyet+gecikme+güvenilirlik+gizlilik diye sayar. **(9) OWASP:** LLM01:2025 **prompt injection** birinci risk, LLM02 **hassas bilgi ifşası**; sistem promptunda veri tipi kısıtı azaltıcı ölçüdür (OWASP GenAI, Promptfoo, TrendMicro). |
| Web Search **Uzun Açıklama** | **(a) Model seçimi — API birincil, routing + yerel opsiyon (kaynak 1-10, 23-30):** 2025-26 pratik üç katmanlı: (i) **büyük/frontier model bulutta API** — geniş görev, kalite, hız; (ii) **routing** — gelen istek önce küçük modelle/şablonla denenir, eşik altındaysa büyüğe devredilir (cascade); literatür bu ayrımı "kayıpsız maliyet" sayar (%40-85 tasarruf, %95 kalite korunumu). CoreMusic kararı bu desenle birebir örtüşür: **asistan sohbeti gibi kalite-gerektiren iş API'de** (büyük model), **transkript/etiketleme gibi sınırlı tekrarlı iş küçük/hassas modelde** (yerel/edge opsiyonel — PLANNED). Yerel modelin kanonik gerekçesi gizlilik+gecikme+token sıfır maliyet (arXiv 2505.16508, ACM edge-cloud, Zenriotech); riski kalite/ölçek/bakım → bu yüzden **opsiyonel**, birincil değil. **(b) RAG/embedding (kaynak 11-19):** standart boru hattı chunking→embedding→index→retrieve→rerank→augment; embeddings **aynı modelle** üretilip sorgulanır (TowardsAI, Pinecone); üretimde RAG türleri (hybrid, graph, self-RAG) ile güçlendirilir (Meilisearch 14 tür, VentureBeat). CoreMusic'te bu boru hattının **tamamı 0 kod** → bu ADR'de **PLANNED** olarak işaretlenir; `KnowledgeBase:48` yorumu ("gerçek implementasyonda vector cosine similarity") bu planla doğrulanır. **(c) Maliyet/kota (kaynak 20-28):** iki ayrı mekanizma şart: **rate limit** (istek hızı — ADR-013 bugün bunu yapar) + **token bütçesi** (harcanan token tavanı — yok); token-aware limit sağlayıcıdan dönen token sayımıyla sayılır (Kong, dev.to); cache hem tekrar eden bağlamı keser (%50 cache token indirimi) hem gecikmeyi düşürür (NeuralTrust, Reintech) → ADR-007 cache katmanı üzerine oturur. Güvenli retry/backoff LLM'de de zorunlu (üretim rehberleri) — ADR-028 ruhu. **(d) Gizlilik (kaynak 29-36):** kişisel veri eğitim setindeyse GDPR her zaman uygulanır; sağlayıcıya veri gönderimi **işleme (processor)** ilişkisi demektir → sözleşme/DPAA + **eğitime veri yok** tercihi (opt-out değil, **kapalı varsayılan**) (IAPP, EDPB, ICO, Baker McKenzie); noyb/Meta örneği opt-out mekanizmasının bile yeterince sağlam sayılmadığını gösterir → CoreMusic'in "eğitime veri yok" kuralı bilinçli olarak **opt-out'tan daha katıdır**. Gönderim öncesi **redaksiyon** (PII çıkarımı) OWASP LLM02'nin de önerdiği sistem-promptu kısıtıyla birleşir. **(e) Human-in-the-loop (kaynak 37-44):** halüsinasyon tek seferlik düzeltilmez; **ölçüm** (halüsinasyon oranı, factual accuracy) + **guardrails** (input/output kontrol, RAG grounding, self-correction) + **HITL gözden geçirme** katmanlanır (NHImg, Arthur, Fiddler, Blockchain Council). Öneri tarafında explainability "neden bu öneri" (skor bileşenleri) ile, asistan tarafında "yanıt doğrulama" (kaynak/track) ile sağlanır → K4 README:39 "Explainable" ilkesi bu ADR'de karara dönüşür. **(f) Müzik önerisi (kaynak 45-52):** hibrit CF + content-based standart, cold-start kişisel veri/AI'la aşılır (aman.ai, JISem, arXiv 2102.12369); LLM öneriyi **açıklama ve sohbetle keşif** katmanında tamamlar, tek başına skor üreticisi değildir → CoreMusic "öneri skoru klasik hibrit (kodda iskelet), LLM açıklama/keşif" ayrımını yapar. **(g) Lock-in (kaynak 53-61):** doğrudan SDK/bağlam(prompt formatı)/fine-tune bağlanması lock-in'in 5 katmanıdır (Agile Monkeys); soyutlama katmanı (gateway) bunu kırar ama kendisi yeni bağımlılık olur (Tianpan) → CoreMusic **kendi ince soyutlama katmanını** yazar (tek arayüz, sağlayıcı config'i ADR-015 env), üçüncü-party gateway'i **karşılaştırma** olarak kullanır. |
| Web Search **Paragraf Veri Uzun** | CoreMusic AI stratejisi 2025-26 literatürüyle üç katmanda kuruluyor: **kullanım** — asistan sohbeti kalite gerektirdiğinden büyük model API'de çalışır, tekrarlı/dar işler (transkript, etiketleme) küçük modelde; bu ayrımı **model routing** yapar ve faturayı %40-85 düşürür (NeuralTrust, Inworld, RouteLLM/Tianpan); **maliyet** — istek bazlı rate limit (ADR-013) tek başına yetmez, **token bütçesi** + cache (ADR-007) birlikte gerekir; LLM API harcaması yarı yılda $3.5B'den $8.4B'a çıktı, kota tokenle sayılmalı (TrueFoundry, dev.to, Trident, NeuralTrust); **gizlilik** — kişisel veri eğitime asla verilmez (kapalı varsayılan; opt-out'tan katı — IAPP, EDPB, noyb vakası), gönderim öncesi redaksiyon (OWASP LLM02), PII şifreleme ADR-022 ile hizalı; **denetim** — halüsinasyon ölçümü + guardrails + HITL doğrulama katmanlanır, öneri **açıklanabilir** olur (Fiddler, Arthur, NHImg; K4 "Explainable"); **altyapı** — RAG/embedding boru hattı (chunk→embed→index→retrieve→rerank) bugün CoreMusic'te 0 kod → PLANNED; sağlayıcı kilidine karşı kendi ince soyutlama katmanı (gateway Gartner %70/2028), yerel/edge model opsiyonel (gizlilik+gecikme+zero token — SLM/edge literatürü) ve API çökerse **klasik hibrit öneriye fallback** yazılır. Karar bu beş çerçeveyi tek belgede bağlar; sayısal eşikler (token tavanı, fiyat) ölçümle kalibre edilir. |
| Web Search **Sonucu** | 1) **Model routing / API-büyük-yerel-küçük ayrımı doğrulandı** (NeuralTrust, Inworld, Digital Applied, AWS, RouteLLM/Tianpan, Splunk, arXiv) → **§2 madde (b)**. 2) **RAG boru hattı kanonu doğrulandı** (Pinecone, IBM, Qdrant, Meilisearch, TowardsAI, VentureBeat) → **§2.2 (g) RAG PLANNED**. 3) **Token bütçesi + token-aware rate limit + cache üçlüsü doğrulandı** (TrueFoundry, dev.to, Trident, NeuralTrust, Kong, Reintech) → **§2 madde (c)** ADR-007/013'e bağlandı. 4) **"Eğitime veri yok" + redaksiyon doğrulandı** (EDPB, ICO, IAPP, Baker McKenzie, noyb vakası) → **§2 madde (d)**. 5) **HITL + guardrails + halüsinasyon ölçümü doğrulandı** (Fiddler, Arthur, NHImg, Blockchain Council, JHU) → **§2 madde (e)**. 6) **Hibrit müzik önerisi + cold-start + LLM'in açıklama rolü doğrulandı** (aman.ai, JISem, arXiv, ScienceDirect, Wikipedia) → **§2 madde (a)**. 7) **Soyutlama katmanı lock-in'i kırar ama kendisi de bağımlılık yaratır** doğrulandı (OpenRouter, TrueFoundry, Gartner/SWFTE, Tianpan, Reddit) → **§3-4 red + §2.2 (h) kendi ince soyut katmanımız**. 8) **SLM/edge gizlilik-gecikme-maliyet avantajı doğrulandı** (arXiv 2505.16508, ACM, PremAI, Zenriotech, Spheron) → **§2 madde (b) yerel opsiyonel PLANNED**. 9) **OWASP LLM01/LLM02 doğrulandı** (OWASP GenAI ×3, Promptfoo, TrendMicro) → **§4.3 risk 4 + §2 (d) redaksiyon**. **Toplam ~78 benzersiz adlandırılmış kaynak, 9 sorgu**; çapraz doğrulama ≥2 kaynak dokuz ana iddiada da karşılanır. **⚠️ iki sınır:** (i) sayfa-içi tur yapılmadı → OWASP LLM01 mitigasyon detayları, EDPB işleme şartları ve sağlayıcı fiyat/token tabloları üretim öncesi derin okunur (§5.1/10); (ii) token birim fiyatı/somut bütçe rakamı **hiçbir kaynaktan bu yazıya alınmadı** (ölçülmemiş → §1.1 `⚠️`). **Vault tarafı aynı resmi verdi:** AI **şeması** (6 tablo) + prompt/token iskeleti + HTTP aracı **mevcut** ama instantiate 0; LLM API/RAG/embedding/öneri verisi/yerel model **0 → PLANNED** → bu ADR **mimari karardır**; "AI özellikleri çalışıyor" iddiası bugün **yalandır** (§1.1). **Kaynak listesi (~78):** 1) neuraltrust.ai — LLM model routing · 2) inworld.ai — AI model routing cost (2026) · 3) digitalapplied.com — LLM routing cost-quality · 4) aws.amazon.com — multi-LLM routing strategies · 5) arxiv.org/2506.06579 — multi-LLM inference · 6) deliveringdataanalytics.com — SLM vs LLM decision · 7) tianpan.co — routing + cascades (RouteLLM %85/%95) · 8) splunk.com — model routing for agents · 9) alexanderthamm.com — LLM cost optimization · 10) linkedin.com — LLM routing cost · 11) pinecone.io — RAG · 12) ibm.com — RAG pattern · 13) medium — embeddings and RAG guide · 14) qdrant.tech — what is RAG · 15) surrealdb.com — RAG vector patterns · 16) venturebeat.com — graph-enhanced RAG · 17) meilisearch.com — 14 RAG types · 18) towardsai.net — RAG guide · 19) cratedb.com — RAG vector search · 20) truefoundry.com — LLM cost optimization (API spend $3.5B→$8.4B) · 21) dev.to — rate limiting in LLM apps (token, istek değil) · 22) getmaxim.ai — cost/latency guide 2026 · 23) tridentventures.org — token budgeting + rate limiting · 24) reintech.io — LLM rate limiting quota · 25) neuraltrust.ai — cost reduction (cache %50) · 26) medium (kong AI rate limiting) — token-aware limit · 27) premai.io — cost optimization 8 strateji · 28) unmeshed.io — 9 token teknikleri · 29) iapp.org — EU Digital Omnibus + GDPR AI training · 30) secureprivacy.ai — consent management training data · 31) dataprotectionreport.com — GDPR training AI models · 32) termly.io — AI training data privacy · 33) cms.law — 2025 data protection · 34) tianpan.co — AI telemetry GDPR (noyb) · 35) edpb.europa.eu — SP training AI & data protection · 36) ico.org.uk — AI and data protection guidance · 37) humansintheloop.org — HITL responsible AI 2025 · 38) blockchain-council.org — hallucination RAG guardrails HITL · 39) mlops.community — AI in Production 2025 · 40) fiddler.ai — HITL evaluation for LLM apps · 41) arthur.ai — guardrails reduce hallucinations · 42) ep.jhu.edu — HITL AI course · 43) nhimg.org — hallucination detection in production · 44) arxiv.org/2607.16215 — RAIL Guard responsible AI loop · 45) jisem-journal.com — cold-start music meta-learning · 46) arxiv.org/2102.12369 — neural content-aware CF · 47) 7universum.com — hybrid music rec PinSage · 48) aman.ai — recsys cold start · 49) sciencedirect.com — emotion-driven music rec 2025 · 50) en.wikipedia.org — cold start · 51) cse.aua.am — personalized music rec 2025 · 52) medium — LLM cold start · 53) reddit.com/r/LLMDevs — multi-model abstraction eleştirisi · 54) openrouter.ai — LLM gateway (lock-in) · 55) truefoundry.com — vendor lock-in prevention · 56) getmaxim.ai — avoid LLM vendor lock-in · 57) swfte.com — Gartner %70 (2028) AI gateway · 58) tianpan.co — switching costs (LiteLLM) · 59) medium — What is LiteLLM · 60) agenta.ai — top LLM gateways 2025 · 61) theagilemonkeys.com — five layers of lock-in · 62) arxiv.org/2505.16508 — edge-first LM inference · 63) premai.io — SLM edge · 64) tianpan.co — hybrid cloud-edge latency-privacy · 65) dl.acm.org/3662006 — edge-cloud SLM/LLM · 66) zenriotech.com — SLM edge privacy · 67) linkedin.com — on-device vs cloud checklist · 68) spheron.network — hybrid cloud-edge 2026 · 69) medium — small LLMs on-device · 70) genai.owasp.org — LLM01:2025 prompt injection · 71) genai.owasp.org — LLM02 sensitive info disclosure · 72) owasp.org — Top 10 for LLM applications · 73) owasp.github.io — OWASP Top 10 for LLMs PDF 2025 · 74) pomerium.com — OWASP LLM top 10 · 75) promptfoo.dev — OWASP LLM TLDR · 76) trendmicro.com — OWASP Top 10 LLM 2025 · 77) aembit.io — OWASP LLM risks · 78) linkedin.com — OWASP LLM 2026 |
| Web Search **Alınan Karar** | **ADR-030 KABUL EDİLİR — BEŞ ÇERÇEVE + ALTYAPI KARARI:** **(a) Kullanım alanları (üç):** **müzik önerisi/keşif** (skor = klasik hibrit CF+content-based — kod iskeleti var, veri stub; LLM açıklama + sohbetle keşif katmanı ekler), **asistan sohbeti (LLM — birincil API kullanıcısı)**, **transkript/etiketleme (çevrimdışı iş — kuyruk; `.ai/.sql/mysql/coremusic_ai.sql:166` `training_jobs` ruhu)**; **(b) Model seçimi — API birincil + yerel/edge opsiyonel (PLANNED):** kalite-gerektiren geniş iş **harici LLM API**; dar/tekrarlı/hassas iş ** küçük yerel/edge model** (ONNX/K4-06) — routing deseniyle (§1.3-1); **(c) Maliyet/kota:** **token bütçesi** (sağlayıcı yanıtlarından token sayımı, kullanıcı/günlük tavan) + **istek rate limit ADR-013** (429/`Retry-After`, fail-open korunur) + **cache ADR-007** (yanıt/prompt cache, TTL zorunlu); bütçe aşımında **klasik önteleme + uyarı**, özellik sessizce pahalılamaz; **(d) Gizlilik — PII ADR-022 üstünde:** **eğitime veri yok (kapalı varsayılan — opt-out değil)**, LLM'e gönderilmeden önce **redaksiyon** (PII çıkarımı; OWASP LLM02), PII şifreleme ADR-022 ile hizalı; ham dinleme/geçmiş verisi LLM'e **çıkmaz**; **(e) Human-in-the-loop:** öneri **açıklanabilir** (skor bileşenleri + "neden bu öneri"), asistan yanıtı **doğrulama akışından** geçer (kaynak/şablon + riskli yanıtta insan onayı), halüsinasyon/guardrail ölçümü §5.1/8. **Altyapı:** **harici LLM API birincil** — `ToolCalling.php:241-280`'daki curl istemcisi **hazır ama genel HTTP aracıdır (LLM çağrısı değil — dürüst etiket)**, LLM sağlayıcı çağrısı **0 → PLANNED**; **kendi ince soyutlama katmanı** (tek arayüz + sağlayıcı config — ADR-015 env, REDACTED anahtarlar `.env`); **yerel model opsiyonel PLANNED**; **RAG/embedding PLANNED (kod 0)** — vektör tablosu + embedding modeli §5.1/6'da seçilir. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: model routing (10), RAG kalıpları (9), maliyet/kota (9), gizlilik/GDPR (8), HITL/guardrails (8), müzik önerisi (8), lock-in (9), edge/SLM (8), OWASP LLM (8) → **~78 benzersiz adlandırılmış kaynak, 9 sorgu**; çapraz doğrulama ≥2 kaynak dokuz ana iddiada da karşılanır. **Vault tarafı aynı resmi verdi:** AI **şeması** (6 tablo) + prompt/token/maliyet iskeleti + genel HTTP aracı **mevcut** (instantiate 0 → PLANNED); **LLM API çağrısı 0, embedding 0, öneri verisi stub (`return []`), yerel model 0** → bu ADR **mimari karardır**; "AI çalışıyor" iddiası bugün **yalandır** (§1.1). Uygulama §5.1 adımlarına, sayısal eşikler (token tavanı, fiyat) ölçüme bağlıdır. |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-022-database-hardened-security]] | **PII bu ADR'nin dışına çıkmaz:** LLM'e gönderilecek metin şifreleme (ADR-022 app-level AES-256-GCM **PLANNED**) ve redaksiyon katmanından **geçmeden** iletilmez; ham PII (ad/telefon/adres/ödeme) prompt'a **konmaz**. ADR-022'nin PII alan şifrelemesi henüz PLANNED olduğu için bu ADR'de **redaksiyon önce gelir** (şifre yoksa veri hiç çıkmaz). |
| [[ADR-007-cache-namespace]] | **Cache olmadan token bütçesi tutmaz:** LLM yanıt/prompt cache'i bugün yalnız Apcu/Memory/PageCache üzerindedir (Redis adapter YOK → ADR-007 bulgusu); TTL zorunludur; semantic cache **PLANNED**. Cache kapalıysa kota yalnızca rate limit + token sayacıyla korunur. |
| [[ADR-013-rate-limiting-apcu]] | **AI kotası mevcut limiterin üstünde koşar:** LLM uçları `RateLimiterMiddleware`'ten geçer (429 + `Retry-After`, **fail-open korunur**); eklenen **token sayacı** istek limitini **değiştirmez**, tamamlar — ADR-013'e dokunulmaz. Fail-open: kota bilgisi yoksa istek durdurulmaz, yalnız işaretlenir (süreklilik). |
| [[ADR-020-api-public-security]] | **LLM API anahtarları normal API anahtarıdır:** prefix+hash+scope+rotasyon deseni geçerlidir; anahtarlar REDACTED — bu ADR'ye/yazılıma **yazılmaz**, yalnız ADR-015 env Config'ten okunur (`getenv()` ihlali yasak — ADR-020 §1.4). |
| [[ADR-028-anti-ban-system]] | **Outbound HTTP bu ADR'yi kapsamaz:** `ToolCalling.php:241-280` dahil 3 curl istemcisinin UA/backoff/circuit-breaker zorunluluğu ADR-028'dedir; LLM çağrısı eklendiğinde aynı kurallar geçerli olur (ADR-028 dokunulmaz). |
| [[ADR-005-ultrathink-protocol]] | Kod kanıtı olmayan her iddia etiketli: LLM API **0**, embedding **0**, öneri verisi **stub (`return []`)**, yerel model **0**, RAG **0** → `⚠️ VERIFICATION REQUIRED` / **PLANNED**. Token fiyat/tavan sayıları ölçülmediği için bu ADR'de **yazılmaz**. |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı: bu ADR'deki her wiki-link diskte var (§6'da doğrulanır); şablon zorunluluğu (Guardrail #16) — `.templates/adr/adr-template.md` iskeleti (7 bölüm + §1.3 9 alan). |
| In-Place Refactoring | Dosya adları **değiştirilmez**: `ToolCalling.php`, `AIEngine.php`, `PromptEngine.php`, `KnowledgeBase.php`, `coremusic_ai.sql`, `k4-yapay-zeka/*` yalnız okunur; mevcut satırlara **ekleme** yazılır (§5.1), silme yok; sınıflar **yeniden adlandırılmaz/birleştirilmez**. |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil**. |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (`vault-utf8-writer append`). |
| REDACTED | LLM API anahtarları, sağlayıcı hesap bilgileri, endpoint anahtar query'leri bu ADR'ye **kopyalanmaz**; tüm sırlar yalnız `.env`'de yaşar (ADR-015). |
| Ölçüm-göre-kalibrasyon | Token tavanı, bütçe limiti, cache TTL, routing eşikleri ve fiyat sayıları **ölçümle** belirlenir — bu ADR'de kesin rakam **yazılmaz** (`⚠️ VERIFICATION REQUIRED`). |

---

## 2. Karar (Decision)

**CoreMusic AI stratejisi BEŞ çerçeve ve BİR altyapı kararıyla bağlayıcı ilan edilir:**

**(a) KULLANIM ALANLARI (üç):**

| # | Alan | Ne yapar | Model/Altyapı | Durum |
|---|---|---|---|---|
| 1 | **Müzik önerisi / keşif** | Skor = **klasik hibrit** (collaborative + content-based + çeşitlilik) — kod iskeleti var (`AIEngine.php:56-83`) ama veri stub (`:219,224,241` `return []`); **LLM** öneri **açıklamasını** ve sohbetle keşfi üretir, skoru **tek başına değiştirmez** | Hibrit = yerel kod; açıklama = API | **Şema IMPLEMENTED / kod stub → PLANNED** |
| 2 | **Asistan sohbeti (LLM)** | Kullanıcıyla doğal dilde müzik/ürün sohbeti; **birincil büyük-model kullanıcısı** | **Harici LLM API (birincil)** | **PLANNED (kod 0)** |
| 3 | **Transkript / etiketleme (çevrimdışı iş)** | Söz transkripti, etiket/özellik çıkarma — **kuyrukta**, gecikme önemli değil, maliyet duyarlı | Küçük model API **veya** yerel/edge (routing ile) | **PLANNED (kod 0; `coremusic_ai.sql:166` `training_jobs` şeması hazır)** |

> **Sınır:** Üç alandan başkası bu ADR ile AI'ya **girmez** (ör. arama sıralaması, moderasyon otomasyonu ayrı karar ister).

**(b) MODEL SEÇİMİ — API BİRİNCİL + YEREL/EDGE OPSİYONEL:**

```
[İstek] → [Sınıflandırma/routing] → basit/dar/hassas?  → YEREL/EDGE model (opsiyonel, PLANNED)
                                   → geniş/kalite-gerek? → HARİCİ LLM API (birincil)
[API çökerse/kısılsa/ase aşarsa] → klasik hibrit öneri + şablon yanıtlar (fallback, §4.3/5)
```

| Karar | Değer | Durum |
|---|---|---|
| **Birincil** | **Harici LLM API** — asistan sohbeti ve geniş işler; routing deseni (ucuz/küçük → pahalı/büyük cascade) ile maliyet korunur (§1.3-1) | **PLANNED** (LLM çağrısı kod **0**) |
| **Opsiyonel** | **Yerel/edge model** (ONNX Runtime — `k4-yapay-zeka/README.md:52` K4-06) — dar, tekrarlı veya **hassas** işler; gizlilik + gecikme + token sıfır maliyet | **PLANNED** |
| **Soyutlama** | **Kendi ince soyutlama katmanımız** (tek arayüz; sağlayıcı config'de — ADR-015 env) — üçüncü-party gateway **karşılaştırma** olarak kullanılır | Karar (kod PLANNED) |
| **Sağlayıcı** | Tek sağlayıcı **sabitlenmez**; sağlayıcı seçimi + anahtarlar §5.1/3'te, REDACTED `.env` | ⚠️ `VERIFICATION REQUIRED` |
| **Red** | Tek-sağlayıcıya doğrudan SDK bağlama; kendi yazımımızda üçüncü-party gateway'i zorunlu kılma (§3-4) | Reddedildi |

**(c) MALİYET / KOTA YÖNETİMİ (üç katman):**

| Katman | Mekanizma | Dayanak | Durum |
|---|---|---|---|
| İstek hızı | **Rate limit** — 429 + `Retry-After`, fail-open korunur | [[ADR-013-rate-limiting-apcu]] `RateLimiterMiddleware.php:30-32,48-56` | **IMPLEMENTED** (kullanılır) |
| **Token bütçesi (yeni)** | Sağlayıcı yanıtındaki **token sayımı** ile kullanıcı/günlük/görev tavanı; aşımda istek **durdurulmaz → uyarı + klasik moda düşüş** (fail-open ruhu) | §1.3-3 (token-aware limit) | **PLANNED** |
| Tekrar eden bağlam | **Cache** — prompt/yanıt cache'i, **TTL zorunlu**; semantic cache PLANNED | [[ADR-007-cache-namespace]] (Apcu/Memory/PageCache; Redis YOK) | **IMPLEMENTED taban / PLANNED genişleme** |

- **Bütçe aşım zinciri:** token tavanı aşıldı → routing küçük modele/şablona → klasik hibrit öneri → kullanıcıya **açık durum mesajı** (sessiz kalite düşüşü **yasak**).
- **Ölçüm:** her çağrıda token/maliyet olayı log'a (ADR-005 ruhu) — sayısal tavan ölçümle belirlenir (`⚠️`).

**(d) GİZLİLİK — PII ADR-022 ÜSTÜNDE (iki kural):**

| Kural | Karar |
|---|---|
| **Eğitime veri yok** | Kullanıcı verisi/dinleme geçmişi **asla** model eğitimi için kullanılmaz — **kapalı varsayılan**, opt-out **değil** (§1.3-4: opt-out'tan daha katı bilinçli tercih). Sağlayıcı sözleşmesi/ayarında eğitim **kapalı** tutulur. |
| **Redaksiyon** | LLM'e gönderilmeden önce metin **filtrelenir**: PII (ad, e-posta, telefon, adres, ödeme) çıkarılır/maskelenir; ham ses/dinleme verisi prompt'a **girmez**; OWASP LLM02 (hassas bilgi ifşası) ile hizalı. |
| **Şifreleme** | Kalan verinin saklanışı ADR-022'ye tabidir (app-level AES-256-GCM — PLANNED); şifre hazır değilken **redaksiyon = birincil savunma**. |

**(e) HUMAN-IN-THE-LOOP (iki mekanizma):**

| Mekanizma | Karar |
|---|---|
| **Öneri açıklanabilirliği** | Her öneri **"neden bu"** taşır (skor bileşenleri: collaborative/content/ağırlık + çeşitlilik gerekçesi) — `AIEngine` hibrit skorlaması bunu **veri yapısı olarak** üretir (kod iskeleti var); K4 "Explainable" ilkesi (`k4-yapay-zeka/README.md:39`) burada karara dönüşür. |
| **Asistan yanıtı doğrulama** | Asistan yanıtında **kaynak/şablon bağlama** + riskli sınıf yanıtta (öneri-ötesi talimat, hukuki/sağlık benzeri iddia) **insan onayı**; üretimde halüsinasyon oranı + guardrails ölçümü (§1.3-5) — ölçüm olmadan asistan **genel kullanıcıya açılmaz** (§5.1/8). |

### 2.1 Neden Bu Seçenek?

- **Kullanım alanları üçle sınırlandı çünkü** AI yüzeyi büyüdükçe denetimsizlik artar (OWASP LLM01/LLM02 — prompt injection ve hassas bilgi); dar tanımlı üç alan test/ölçüm/gizlilik açısından kapatılabilir, onuncu özellik eklemek yeni ADR ister.
- **API birincil + yerel opsiyonel çünkü** literatür ikisini ikilem değil **routing/sıralama** olarak ele alır: kalite-gerektiren geniş iş API'de verimli, dar/hassas iş yerelde gizli+ucuz (§1.3-1, §1.3-8: %40-85 tasarruf, token sıfır, PII çıkmaz). Yerel modelin bugün kodu/öneri seçeneği **0** → dürüstçe **PLANNED**.
- **Token bütçesi rate limit'e ek çünkü** ikisi farklı şeyi sayar: istek sayısı vs harcanan token (§1.3-3 — tek 200K token'lık istek 50 normal isteğe bedel). ADR-013 yalnız ilkinı tutar; ikincisiz fatura **kontrolsüz** büyür. Cache (ADR-007) aynı tekrar eden bağlamı ikinci kez ödemeyi keser.
- **"Eğitime veri yok" + redaksiyon çünkü** GDPR tarafında kişisel verinin işlenmesi her koşulda şart koşulur ve sağlayıcıya veri çıkışı bir işleme ilişkisidir (§1.3-4); opt-out mekanizmaları pratikte yetersiz bulunur (noyb/Meta) → CoreMusic **kapalı varsayılan** seçer. Redaksiyon, PII şifrelemesi (ADR-022) PLANNED olduğu için **bugünün** birincil savunmasıdır.
- **HITL çünkü** halüsinasyon tek seferlik düzeltilmez — ölçüm + guardrails + insan doğrulaması katmanlanır (§1.3-5); öneri açıklaması hem güven hem ürün değeri (K4 "Explainable") üretir.
- **Kendi ince soyut katmanımız çünkü** doğrudan sağlayıcıya bağlanmak lock-in'in 5 katmanını açar, üçüncü-party gateway ise yeni bağımlılık yaratır (§1.3-7) → en ince orta yol: **kendi tek arayüzümüz + config'de sağlayıcı**; Gartner %70 gateway öngörüsü (2028) doğru yöne işaret eder ama bağımlılık başkasına verilmez.

### 2.2 Teknik Detaylar

**a) Kullanım alanı → iş akışı (PLANNED):**

| Alan | Akış | Çıkış |
|---|---|---|
| Öneri/keşif | `recommendationHistory` + `listeningFeatures` (şema **IMPLEMENTED**) → hibrit skor (`AIEngine` stub → doldurulacak) → **LLM açıklama** (PromptEngine `recommendation` şablonu `:195`) → UI | skor + "neden" gerekçesi |
| Asistan sohbeti | istek → PII redaksiyon → routing → LLM API → guardrail/HITL kontrolü → yanıt (+kaynak) | doğrulanmış yanıt |
| Transkript/etiketleme | çevrimdışı kuyruk (`training_jobs` şeması) → küçük model/yerel → DB'ye yazım | metin/etiket |

**b) Routing + fallback zinciri (sırayla):**

| Sıra | Yol | Koşul |
|---|---|---|
| 1 | Yerel/edge model (opsiyonel) | dar/hassas iş **ve** yerel model kurulu (PLANNED) |
| 2 | Harici LLM API (birincil) | geniş/kalite-gereken iş, API kullanılabilir |
| 3 | Küçük API modeli (cascade) | büyük model eşik/kalite altında (§1.3-1) |
| 4 | **Klasik hibrit öneri + şablon yanıt** | API yok/aşıldı → özellik **kapanmaz**, kalite düşer ve **söylenir** |

**c) Token bütçesi (PLANNED, ADR-013'e dokunmadan):** yanıt `usage` alanından token oku → sayaç (kullanıcı/gün/görev, APCu TTL zorunlu — ADR-007) → tavan aşımında routing'i küçült → 3. basamağa → olay log'u; **fail-open**: sayaç okunamazsa istek durmaz, işaretlenir (ADR-013 ruhu).

**d) Redaksiyon (PLANNED):** prompt öncesi katman → PII desenleri (ad/e-posta/telefon/adres/ödeme) çıkar/maskele → `PromptEngine::validatePrompt():156-178` zaten **prompt-injection + hassas veri taraması** kodu taşır (bağlı değil → §5.1/5'te boru hattına takılır) → temiz metin LLM'e.

**e) Soyutlama katmanı (PLANNED):** tek arayüz (`complete(prompt, options): {text, usage, model}`), sağlayıcı **config** (ADR-015 env, REDACTED), yanıt şeması sağlayıcıdan **bağımsız** → sağlayıcı değişim kodda tek noktadan (§3-4 gerekçesi).

**f) RAG/embedding (PLANNED — bugün 0):** vektör tablosu yok (`coremusic_ai.sql` embedding kolonu 0), `KnowledgeBase.php:48` yalnız keyword matching → boru hattı (chunk→embed→index→retrieve→rerank, §1.3-2) §5.1/6'da tek ADR adımı olarak seçilir; **bu ADR'de uygulama taahhüdü yoktur**.

**g) Ölçüm/ortak olaylar:** her LLM çağrısında `{alan, model, token_in/out, süre, maliyet_tahmini, cache_hit}` olayı → log (ADR-005) — dashboard/bütçe raporunun veri kaynağı (`⚠️` maliyet tahmini katsayısı ölçülür).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız yerel/edge model (API yok)** | Tam gizlilik (veri hiç çıkmaz); token maliyeti 0; çevrimdışı (K4 Offline-First) | Küçük modeller geniş sohbet/kalitede geride (§1.3-8); bakım/on-prem maliyeti; güncelleme bizde; transkript gibi işlerde kalite riski | **Geniş asistan sohbeti kalite-gerektirir** → API birincil, yerel **opsiyonel** (§2-b); tek seçenek maddiyetli ve bugün kod 0 |
| 2 | **Yalnız harici API (yerel hiç yok)** | En hızlı teslim; ölçek sağlayıcının sorunu; tek bakım | PII her istekte dışarı çıkar; token faturası doğrusal büyür; gecikme/erişim sağlayıcıya bağlı; gizlilik-duyarlı iş (transkript) riskli | Gizlilik + hassas iş gereksinimi (§1.3-4, §1.3-8) → **yerel opsiyonel** bırakıldı; "yalnız API" hassas işi kapatırdı |
| 3 | **Kendi modelimizi fine-tune / eğitmek** | Tam kontrol; domain uyumu; veri eğitimin **bizde** kalması | Eğitim verisi/etiket işi büyük (kod 0, `training_jobs` şema boş); maliyet+bakım; hata geri alınamaz; ADR kapsamı aşar | Bugün **hiçbir eğitim altyapısı yok** (kod 0) → strateji çekirdeğine alınmadı; ileride ayrı ADR (yeni numara **ADR-088+**) ile değerlendirilir |
| 4 | **Hazır LLM gateway / BaaS (üçüncü-party yönetilen katman)** | Hızlı başlangıç; çok-sağlayıcı tek arayüz hazır; Gartner %70/2028 eğilimi | **Yeni vendor lock-in** — fiyat/erişim/şema bir başkasının elinde (§1.3-7: soyutlama katmanı kendisi bağımlılık yaratır); veri/sözleşme yüzeyi büyür; ADR-020/015 env ilkesiyle gerilim | Kendi ince soyut katmanımız (§2-b) benimsendi; gateway'ler **yalnız karşılaştırma/benchmark** referansı (reddedildi, gerekçe §1.3-7) |
| 5 | **Maliyet kontrolü olmadan "sınırsız" AI** (tek klasik rate limit ile) | Sıfır başlangıç işi; ürün hızlı açılır | Token faturası kontrolsüz (§1.3-3: API harcaması yarı yılda iki katına çıktı); kötü aktör istismarı; kalite düşüşü görünmez | **Token bütçesi + cache + aşım zinciri** (§2-c) zorunlu kılındı; yalnız ADR-013 istek limiti LLM faturasını **durdurmaz** |
| 6 | **Öneriyi de tamamen LLM'e bırakmak** (skor+keşif tek modelde) | Tek sistem; "akıllı" his; kod iskeletine dokunmadan vaat | Halüsinasyon/kanıt sorunu (öneri yanlış olabilir); pahalı; **açıklanabilirlik kaybolur** (skor gizemli); cold-start'ta LLM klasik sinyalleri **yeniden icat eder** | **Skor = klasik hibrit, LLM = açıklama/keşif** (§2-a) ayrımı yapıldı (§1.3-6: literatür de hibriti standart sayar) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek belgede beş çerçeve:** kullanım, model, maliyet, gizlilik, denetim — K4 spec'inde dağınık 14 dosyalık iddia (`k4-yapay-zeka/*`, `README.md:33-39`) ilk kez **karar** altında toplanır; `shared/AGENTS.md:35` ve `shared/src/AI/CLAUDE.md:21`'deki "ADR-030" atıfları karşılığını bulur.
- **Mevcut güvenceler yeniden yazılmaz:** rate limit (ADR-013), cache (ADR-007), API key deseni (ADR-020), PII çerçevesi (ADR-022), outbound HTTP kuralları (ADR-028) **değiştirilmeden** AI'ya uygulanır → kota/gizlilik mevcut güvenlik katmanının üstünde koşar.
- **Kod iskeleti değerlendirilir:** `PromptEngine` (token/maliyet/injection taraması) ve `AIEngine` (açıklanabilir hibrit skor) zaten kodda → sıfırdan yazılmaz, **boru hatta bağlanır** (In-Place).
- **Bütçe önceden tanımlı:** token bütçesi + cache + aşım zinciri ile fatura **kapalı devre** izlenir; sessiz kalite/maliyet sürprizi önlenir (§1.3-3).
- **Gizlilik savunması iki katman:** eğitime veri yok (kapalı varsayılan) + redaksiyon — PII şifrelemesi (ADR-022 PLANNED) gelene kadar bile veri korunur.
- **Lock-in yönetilebilir:** tek soyut arayüz + config sağlayıcısı → sağlayıcı değişimi tek noktadan; fallback zinciri (§2.2b) API kesintisinde özelliği **kapatmaz**.

### 4.2 Olumsuz Sonuçlar

- **Kod işi tamamen önümüzde:** LLM API **0**, embedding **0**, RAG **0**, öneri verisi **stub (`return []`)**, yerel model **0**, asistan UI **0** → bu ADR **mimari karardır**; "AI özellikleri çalışıyor" iddiası bugün **yalandır** (§1.1).
- **İki yeni çalışma yüzeyi:** soyutlama katmanı + redaksiyon/bütçe boru hattı yazılmalı; `shared/src/AI/` 7 sınıf ya **bağlanacak** ya da ölü kod olarak kalacak (§4.3/6 — `unused-files-report` vs `broken-code-scan` çelişkisi).
- **Routing karmaşıklığı:** iki model yolu + cascade + fallback → test yüzeyi büyür; yanlış eşik pahalı ya da kalitesiz yöne kaçırır (§4.3/2).
- **İdari yük:** sağlayıcı sözleşmesi (eğitime veri yok), anahtar rotasyonu (ADR-020), REDACTED disiplini sürekli denetim ister.
- **Maliyet öngörüsü ölçüme bağlı:** token tavanı/fiyat bu ADR'de yok → doğru rakam çıkmadan bütçe **yaklaşık** kalır (`⚠️`).
- **Çifte standart riski:** yerel model PLANNED olduğu için "hassas iş yerelde" bugünden **vaat edilemez** → hassas iş ya ertelenir ya redaksiyon ile API'ye verilir (§4.3/7).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **Halüsinasyon / yanlış içerik:** asistan yanlış bilgi üretir, kullanıcı güveni kaybolur | 4 (Çok olası) | 3 (Orta) | HITL doğrulama + guardrails + halüsinasyon ölçümü (§2-e); ölçüm olmadan genel açılış **yok** (§5.1/8); öneri skoru LLM'e **devredilmez** (§3-6) |
| 2 | **Maliyet patlaması:** token faturası kontrolsüz büyür | 3 (Olası) | 4 (Yüksek) | Token bütçesi + token-aware sayaç + cache (§2-c); aşım zinciri küçük modele/klasik moda düşer (§2.2b); olay log'u ile izleme (§2.2g) |
| 3 | **Gizlilik ihlali / GDPR:** PII LLM'e çıkar veya veri eğitime gider | 3 (Olası) | 4 (Yüksek) | Eğitime veri yok (**kapalı varsayılan**) + redaksiyon (§2-d); PII şifreleme ADR-022 ile hizalı; gönderim öncesi `validatePrompt` taraması (§2.2d); sözleşme/DPAA §5.1/9 |
| 4 | **Prompt injection / LLM01:** kullanıcı girdisi sistemi manipüle eder, aşırı yetki (excessive agency) | 4 (Çok olası) | 3 (Orta) | OWASP LLM01/02 (§1.3-9); `ToolCalling` araçları **en az yetki** (zaten `SELECT` kısıtı `:230-234`); riskli yanıtta insan onayı (§2-e); sistem promptu kısıtları |
| 5 | **API kesintisi / kısıtlama:** sağlayıcı çöker, kota dolu veya fiyat değişir | 3 (Olası) | 3 (Orta) | Fallback zinciri (§2.2b): küçük model → **klasik hibrit öneri + şablon yanıt**; klasik öneri yolu her zaman çalışır tutulur (`AIEngine` iskeleti); rate limit 429 (`Retry-After`) ADR-013 ile öngörülü |
| 6 | **Ölü kod / yarım bağlı katman:** 7 AI sınıfı instantiate 0 → strateji kâğıtta kalır, kod gövdesiz | 4 (Çok olası) | 3 (Orta) | §5.1/2'de `unused-files-report` (PLANNED) vs `broken-code-scan` (DEAD CODE) etiketi tek değere bağlanır; sınıflar §5.1 adımlarıyla **boru hatta bağlanır** ya da bakımı üstlenilir — "bağlı" iddiası bağlanmadan yazılmaz |
| 7 | **Hassas işin API'ye çıkması:** yerel model PLANNED olduğu için "hassas iş yerelde" bugünden sağlanamaz | 3 (Olası) | 3 (Orta) | Redaksiyon **her koşulda** zorunlu (§2-d) — şifre/yerel model gelmeden önce PII çıkmaz; hassas iş ya redaksiyonla yürür ya §5.1/7'de ertelenir (açık durum mesajı) |
| 8 | **Sağlayıcı bağımlılığı (lock-in):** doğrudan SDK/bağlam/fine-tune bağlanması | 3 (Olası) | 3 (Orta) | Kendi ince soyut katmanı (§2-b); sağlayıcı config'de (ADR-015), yanıt şeması sağlayıcıdan bağımsız (§2.2e); üçüncü-party gateway yalnız karşılaştırma (§3-4) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Kullanım alanı envanteri + API sözleşmesi:** üç alanın (öneri/keşif, asistan, transkript) girdi-çıktıları, PII sınıfları ve hangi alanın hangi modele gittiği yazılı; OpenAPI (ADR-084) ile asistan uçları | Backend Architect + Vault Steward | 2 gün |
| 2 | **`shared/src/AI/` gerçeklik etiketi:** 7 sınıf + 6 contract için instantiate 0 bulgusu tek değere bağlanır (`unused-files-report.md:131-138` **PLANNED** ↔ `broken-code-scan.md:270` **DEAD CODE**) → sınıflar §5.1/4-6'da **bağlanacak** (etiket: IMPLEMENTED-when-wired) ya da bakımı üstlenilir; dosya adları **değiştirilmez** (In-Place) | Vault Steward + Backend Architect | 0.5 gün |
| 3 | **Sağlayıcı seçimi + anahtar (REDACTED):** aday LLM API'lerin kalite/fiyat/gizlilik (eğitime veri yok ayarı) karşılaştırması → sağlayıcı config'de (ADR-015 env, `getenv()` yasak — ADR-020); anahtarlar `.env`'de, **bu ADR'ye/yazılıma yazılmaz**; rotasyon ADR-020 deseni | Security Engineer + Backend Architect | 1.5 gün |
| 4 | **Soyutlama katmanı + ilk LLM çağrısı:** tek arayüz `complete(prompt, options) → {text, usage, model}` (§2.2e), sağlayıcı ardışık düzeni config; `ToolCalling`/`http-request` üzerinden **değil**, ayrı istemci ile (ADR-028 UA/backoff kuralları geçerli) → asistan sohbeti ilk PoC | Backend Architect | 3 gün |
| 5 | **Token bütçesi + redaksiyon boru hattı:** yanıt `usage` token sayacı (kullanıcı/gün/görev tavanı, APCu TTL — ADR-007), aşım zinciri (§2.2b); prompt öncesi PII redaksiyonu + `PromptEngine::validatePrompt():156-178`'i boru hattına takma (bağlama); olay log'u (§2.2g) | Backend Architect + Security Engineer | 3 gün |
| 6 | **RAG/embedding kararı (tek adım):** vektör tablosu/şeması (yeni — bugün **0**), embedding modeli, chunk/rendeleme politikası → **ayrı karar/uygulama adımı**; bu ADR'de taahhüt yok (`⚠️`) | Data Engineer + Backend Architect | 1 gün (karar) |
| 7 | **Yerel/edge model PoC (opsiyonel):** ONNX Runtime (K4-06) ile dar iş (transkript/etiketleme) benchmark'ı — gecikme/ölçek/kalite ölçümü → routing eşiği gerçek veriye bağlanır; başarısızsa alan API'de kalır | Embedded Engineer + Backend Architect | 2 gün (PoC) |
| 8 | **HITL + guardrail ölçümü:** halüsinasyon/factual accuracy sayaçları, riskli yanıt sınıfı ve insan onayı akışı; **ölçüm yoksa asistan genel kullanıcıya açılmaz** (§2-e) | QA Engineer + Security Engineer | 2 gün |
| 9 | **Öneri stub'unu doldurma:** `AIEngine::getCollaborativeScores():219,224` / `getContentBasedScores():241` `return []` → gerçek skor (şema `recommendation_history`/`listening_features` **IMPLEMENTED**); LLM yalnız **açıklama** üretir (§2-a) | Data Engineer + Backend Architect | 3 gün |
| 10 | **Ölçüm + derin doğrulama:** token fiyat/tavanı, routing eşikleri, cache TTL, halüsinasyon oranı ölçümler; OWASP LLM01 mitigasyon + EDPB işleme şartları + sağlayıcı fiyat tablosu **sayfa-içi** derin okunur (§1.3 sınır i) | QA Engineer + Security Engineer | 1.5 gün |
| 11 | **Sağlayıcı sözleşmesi / gizlilik şartı:** "eğitime veri yok" (kapalı varsayılan) + veri işleme şartı sağlayıcı sözleşmesine/yazılı ayara yazılır; redaksiyon kapsamı test edilir (PII sızıntısı fixture'ı) | Security Engineer | 1 gün |
| 12 | **Debate + onay:** 3 tur / 20 persona debate (§5.3) → şartlar §5.5'e; Tech Lead/Arch Lead §7 | Vault Steward | 1 gün |
| 13 | **Doğrulama:** şablon tutamağı (`ADR-030`, hedef 0) · wiki-link disk kontrolü (§6) · `vault-utf8-writer verify/scan` (BOM 0 / mojibake 0) · `index.md:67` slug eşleşmesi · placeholder 0 · `log.md` append 1 satır | Vault Steward | 0.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (a) asistan sohbeti kapanırsa → sohbet ucu kapatılır, **öneri/keşif ve klasik hibrit öneri çalışmayı sürdürür** (LLM açıklama yoksa skor + kural tabanlı gerekçe kalır — hiçbir dosya adı değişmez); (b) token bütçesi kapatılırsa → rate limit (ADR-013) + cache (ADR-007) **dokunulmadan** kalır, fatura izlemeye devam eder ama tavan durdurucu kapanır (bilinçli risk kaydı); (c) redaksiyon kapatılırsa → **PII şifrelemesi (ADR-022) gelmeden kapatılamaz** (§2-d — durdurucu kural); (d) yerel model PoC başarısızsa → alan API'ye taşınır, PLANNED kalem kapalı kalır (dosya eklenmedi); (e) RAG kararı olumsuzsa → `KnowledgeBase` keyword matching (bugünkü hâli) **dokunulmadan** kalır; (f) soyut katman terk edilirse → sağlayıcı istemcisi doğrudan çağrılır (lock-in riski §4.3/8'e geri döner, gerekçe loglanır).

**Tam geri dönüş:** dosya adları/şema değiştirilmediği için (In-Place korundu) geri dönüş = (1) LLM istemcisi/boru hattı **kaldırılır** (yeni eklenen dosyalar; `shared/src/AI/` 7 sınıf **eski hâline** döner — instantiate 0 zaten durum buysa geri dönüş = dosyalara dokunmamak), (2) token sayacı bayrakla kapanır, (3) redaksiyon **yalnız ADR-022 PII şifrelemesi devreye girdikten sonra** tartışılır (aksi halde kapanamaz), (4) asistan ucu kapatılır → özellik yüzeyi bugünkü hâline (AI yok), (5) `coremusic_ai.sql` şeması **dokunulmaz** (veri kalır), (6) `vault-utf8-writer` yedeği (`<file>.bak`) eski içeriği verir, (7) `.ai/log.md`'ye tek satır revert append'i, (8) `.ai/.decisions/index.md:67` satırı `status: reverted` olur, (9) **bu ADR düzenlenmez** — `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** AI **şeması** (6 tablo), rate limit (ADR-013), cache (ADR-007), API key deseni (ADR-020), PII çerçevesi (ADR-022), outbound HTTP kuralları (ADR-028) ve `log.md` append-only geçmiş **bozulmaz**; geri döndürülecek olan **LLM/routing/bütçe/redaksiyon katmanıdır**.

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| 1 | 20 persona (kod kanıtı) | ✅ | `ToolCalling.php:241-280` genel http-request aracı (LLM sağlayıcı çağrısı PHP'de **0**), embedding/vector **0**, `AIEngine.php:219,224,241` stub `return []`, `coremusic_ai.sql` 6 tablo **IMPLEMENTED** (`:31,55,80,109,139,166`), `shared/src/AI/` 7 sınıf **PLANNED** (stub) — ~78 kaynak. Oy: **15 kabul/neutral, 4 uyarı** (QA: HITL testi; Critic: stub etiketi + sessiz boş dönüş şartı) |
| 2 | İtiraz → çözüm | ✅ | (1) AI 7 sınıf stub = görünümlü PLANNED → sınıf bazlı dürüst etiket → **şart 1a** · (2) `AIEngine` `return []` sessiz başarısızlık → açık hata/uzaklaştırma modu → **şart 1b** · (3) HITL testi yok → halüsinasyon/doğrulama + maliyet bütçe testi → **şart 2** · (4) RAG PLANNED → embedding+RAG yol haritası → **şart 3** |
| 3 | Oy | ✅ | **18 kabul / 2 çekimser / 0 red → KABUL** (2026-09-25) |
| Sonuç | 3 tur / 20 persona | **✅ TAMAMLANDI (18/2/0 KABUL)** | 3 bağlayıcı şart §5.5'e · Tech Lead §7 ✅ · sonuç frontmatter `debate` alanına işlendi · `.ai/log.md` append 1 satır (mevcut satırlara dokunulmaz) |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| LLM API entegrasyonu (asistan sohbeti) | ❌ PLANNED (kod **0** eşleşme) | §5.1/4 |
| Token bütçesi + aşım zinciri | ❌ PLANNED (ADR-013 yalnız istek sayar) | §5.1/5 |
| PII redaksiyon boru hattı | ❌ PLANNED (`validatePrompt` kodda, bağlı değil) | §5.1/5 |
| RAG / embedding / vektör | ❌ PLANNED (**kod 0, tablo 0**) | §5.1/6 |
| Yerel/edge model (ONNX) | ❌ PLANNED (kod 0) | §5.1/7 |
| HITL + halüsinasyon ölçümü | ❌ PLANNED (ölçüm 0) | §5.1/8 |
| Öneri stub doldurma (`return []`) | ❌ PLANNED (iskelet var, veri yok) | §5.1/9 |
| `shared/src/AI/` etiket çelişkisi (PLANNED ↔ DEAD CODE) | ⚠️ `VERIFICATION REQUIRED` | §5.1/2 |
| Sağlayıcı + token fiyatı/tavanı | ⚠️ `VERIFICATION REQUIRED` (seçilmedi/ölçülmedi) | §5.1/3, §5.1/10 |
| Şema 6 tablo + prompt/token/injection iskeleti + HTTP aracı | ✅ IMPLEMENTED (dosya/şema) | §1.1 |
| Debate / Tech Lead | ✅ debate 3/20 KABUL (18/2/0, 2026-09-25) + Tech Lead ✅ — 3 şart §5.5 | §5.3, §5.5, §7 |

### 5.5 Debate Şartları (Kabul Koşulları — 3/3)

| # | Şart | Kapsam | Sorumlu | Durum | Kanıt |
|---|------|--------|---------|-------|-------|
| 1 | **Stub dürüst etiketi + sessiz fail kuralı (1a-1b)** | **1a)** `shared/src/AI/` 7 sınıf için sınıf bazlı dürüst etiket — görünümlü PLANNED ↔ stub `return []` ayrımı yazılır (§1.1-A, §5.1/2) · **1b)** `AIEngine::getCollaborativeScores():219,224` / `getContentBasedScores():241` `return []` **sessiz boş dönüş sayılmaz** → açık hata/uzaklaştırma modu (açık durum mesajı — §2-c aşım zinciri ruhu: "sessiz kalite düşüşü yasak") | Vault Steward + Backend Architect | ⏳ PLANNED → §5.1/2, §5.1/9 | Debate Tur 1 (QA: HITL testi · Critic: stub etiketi + sessiz boş dönüş şart) → Tur 2 itiraz 1-2 |
| 2 | **HITL + maliyet bütçe testi** | Halüsinasyon/doğrulama testi (riskli yanıt sınıfı + insan onayı akışı) + token bütçesi/aşım zinciri testi (tavan aşımında klasik moda açık mesajla düşüş); ölçüm yoksa asistan genel kullanıcıya açılmaz (§2-e, §5.1/8) | QA Engineer + Security Engineer | ⏳ PLANNED → §5.1/5, §5.1/8 | Debate Tur 1 (QA HITL uyarısı) → Tur 2 itiraz 3 |
| 3 | **RAG/embedding yol haritası** | embedding + RAG boru hattı (chunk→embed→index→retrieve→rerank, §1.3-2 / §2.2f) yol haritası maddesi olarak yazılır; vektör tablosu + embedding modeli seçimi §5.1/6'da ayrı karar adımı | Data Engineer + Backend Architect | ⏳ PLANNED → §5.1/6 | Debate Tur 2 itiraz 4 → §2.2f |

**Tur 3 oylaması:** 18 kabul / 2 çekimser / 0 red → **KABUL**; 3 şart bağlayıcıdır (kabul ≠ tamamlandı — kapanış §5.1 adımlarındadır).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail'ler (bu ADR'nin yazım usulü) |
| [[../../AGENTS.md]] | Agent registry — §5 domain (`*.php` → Backend, `*.sql` → Data), §25.3 kural 2/3 (frozen + log append-only) |
| [[../../WORKFLOW.md]] | Süreçler — uygulama adımlarının faz bağlamı |
| [[../index]] | Karar dizini — **satır 67** `[[ADR-030-ai-strategy-core]]` (slug ✅) |
| [[../../index]] | Master katalog — `:647` "decisions/accepted/ADR-030-ai-strategy-core \| AI strategy core \| AI" |
| [[../../brain]] | Mimari karar özeti — `:985` "ADR-030 \| AI öneri motoru" |
| [[../../keys]] | Keyword haritası — `:186` "AI strategy, prompt engineering", `:265` "ADR-030 \| AI strategy, core" |
| [[ADR-022-database-hardened-security]] | PII — app-level AES-256-GCM **PLANNED**; bu ADR'nin gizlilik maddesi onun üstünde koşar, redaksiyon birincil savunma (§1.4, §2-d) |
| [[ADR-007-cache-namespace]] | Cache — Apcu/Memory/PageCache (Redis adapter YOK) → token/yanıt cache'i bu tabanda; TTL zorunlu (§1.4, §2-c) |
| [[ADR-013-rate-limiting-apcu]] | İstek limiti — 429/`Retry-After` + fail-open **IMPLEMENTED** (`RateLimiterMiddleware.php:30-32,48-56`); token bütçesi bunun **üstünde** (§1.4, §2-c) |
| [[ADR-020-api-public-security]] | LLM API anahtarı — prefix+hash+scope+rotasyon + env Config (`getenv()` yasak) (§1.4, §5.1/3) |
| [[ADR-028-anti-ban-system]] | Outbound HTTP — `ToolCalling.php:241-280` 3. curl istemcisi (UA/backoff kapsamı orada) (§1.4, §2.2e) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1, §1.3, §4.3) |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı + şablon zorunluluğu + UTF-8 tek arayüz (§1.4, §5.1/13) |
| [[../../architecture/k4-yapay-zeka/index]] | K4 AI katmanı indeksi — 14 dosya / 50 bileşen (PLANNED spec) |
| [[../../architecture/k4-yapay-zeka/README]] | K4 ilkeleri `:33-39` (Offline-First, Edge AI, Privacy, **Explainable**) + bileşen haritası `:45-57` (K4-05 LLM, K4-06 ONNX) → bu ADR'nin spec dayanağı |
| [[../../architecture/k4-yapay-zeka/recommendation-engine]] | Öneri spec'i — Collaborative `:20`, Content-Based `:94`, Hybrid `:181`, Real-time `:279`, torch `:373` (PLANNED) |
| [[../../architecture/k4-yapay-zeka/edge-ai]] | Yerel/edge inference spec'i (§2-b opsiyonel yolun dayanağı) |
| [[../../architecture/k4-yapay-zeka/voice-assistant]] | Asistan spec'i — NLU `:120`, TTS `:263` (PLANNED — §2-a alan 2) |
| [[../../.templates/adr/adr-template]] | İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16) |
| [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol]] | §1.3 web araştırma protokolü (diskte VAR ✅) |
| `.ai/.sql/mysql/coremusic_ai.sql` (kod yolu, wiki-link değil) | `user_preference_profiles:31` · `listening_features:55` · `recommendation_history:80` (`algorithm` CHECK `:97`) · `audio_features:109` · `model_versions:139` (`model_type:157`) · `training_jobs:166` (**IMPLEMENTED şema**) |
| `shared/src/AI/` (kod yolu) | 7 sınıf + 6 contract — instantiate **0** (`unused-files-report.md:131-138`); `ToolCalling.php:241-280` genel HTTP aracı (`curl_init:253` — **LLM çağrısı değil**) · `AIEngine.php:56-83` hibrit iskelet (`:219,224,241` `return []`) · `PromptEngine.php:195` öneri şablonu, `:163` injection, `:177` hassas veri taraması · `KnowledgeBase.php:48` keyword matching (vektör yorumu) |
| `.ai/reports/unused-files-report.md` + `.ai/reports/broken-code-scan.md` (kod yolu) | `:131-138` "Hiçbir instantiate yok → PLANNED" ↔ `:270` "ToolCalling → DEAD CODE" — **etiket çelişkisi** (§1.1-ii, §5.1/2) |
| Debate (✅ TAMAMLANDI) | §5.3/§5.5/§7.1 — 3 tur / 20 persona, 18/2/0 KABUL (2026-09-25) + 3 şart (stub dürüst etiketi + sessiz fail · HITL+maliyet testi · RAG yol haritası) · sonuç frontmatter `debate` alanına işlendi |

> **Durum özeti:** debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — §5.3/§5.5)** · Tech Lead **✅ (§7, 2026-09-25)** · Arch Lead **⏳ PENDING** · frozen **YOK** · kod: AI **şeması** (6 tablo) + prompt/token/injection iskeleti + genel HTTP aracı **mevcut** (instantiate 0 → PLANNED), **LLM API/RAG/embedding/yerel model/öneri verisi 0** (§1.1) · altyapı: **harici LLM API birincil (PLANNED), yerel/edge opsiyonel (PLANNED), RAG/embedding PLANNED**.

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar içeriği — 5 çerçeve + altyapı) |
| Tech Lead | — | 2026-09-25 | ✅ (debate KABUL — 3 tur / 20 persona, 18/2/0) |
| Arch Lead | — | 2026-09-25 | ⏳ PENDING |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | 3 tur / 20 persona debate — ADR-004/008/010-029 formatı (uygulandı) |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-25 (frontmatter `debate` alanı ile aynı) |
| Tur 1 | **20 persona** — kod: `ToolCalling.php:241-280` genel http-request aracı (LLM sağlayıcı çağrısı PHP'de **0**), embedding/vector **0**, `AIEngine.php:219,224,241` stub `return []`, `coremusic_ai.sql` 6 tablo **IMPLEMENTED** (`:31,55,80,109,139,166`), `shared/src/AI/` 7 sınıf **PLANNED** (stub) — ~78 kaynak. Oy: **15 kabul/neutral, 4 uyarı** (QA: HITL testi; Critic: stub etiketi + sessiz boş dönüş şartı). |
| Tur 2 | **İtiraz → çözüm:** (1) AI 7 sınıf stub = görünümlü PLANNED → sınıf bazlı dürüst etiket → **şart 1a**; (2) `AIEngine` `return []` sessiz başarısızlık → açık hata/uzaklaştırma modu → **şart 1b**; (3) HITL testi yok → halüsinasyon/doğrulama + maliyet bütçe testi → **şart 2**; (4) RAG PLANNED → embedding+RAG yol haritası → **şart 3**. |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL — 3 şart (§5.5):** (1) stub dürüst etiketi + sessiz fail kuralı (1a-1b), (2) HITL + maliyet testi, (3) RAG yol haritası |
| Tech Lead | **✅** (2026-09-25) — debate sonrası onay |
| Frozen | Bu ADR **frozen değildir**; debate ✅ + Tech Lead ✅ ama **Arch Lead ⏳** → §7 satır 3 tamamlanmadan `frozen` yapılmaz |
| Kural | Debate sonucu §5.3/§5.5/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` **append** ile kaydedilir |

---

**1.0.0 | 2026-09-25 | Created**
*ADR-030 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead ✅ · 3 şart (§5.5) · frozen YOK (Arch Lead ⏳)*

*ADR-030 — AI Strategy Core (Kullanım Alanları · API Birincil + Yerel/Edge Opsiyonel · Token Bütçesi/Kota · PII Gizlilik — Eğitime Veri Yok · Human-in-the-Loop)*
*Authority: ADR-030 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*