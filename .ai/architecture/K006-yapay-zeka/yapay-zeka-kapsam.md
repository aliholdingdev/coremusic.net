---
node_id: K006-yapay-zeka-kapsam
node_type: domain
status: verified
parent_id: "K000–K5000 Architecture Registry"
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - K004-shared
  - K016-AI
evidence:
  - "v2 §2.6 K006 tanımı — 9 alt düğüm, 8 kademe zorunlu"
  - "ADR-097 §5.1 K006 kök dosya listesi — 12 dosya"
  - "repo shared/src/AI/* — 8 dosya (7 sınıf + CLAUDE.md)"
validation: "2026-10-12 · v2 §2.6 + ADR-097 §5.1 + repo dosya listesi çapraz okundu · status=verified"
open_questions:
  - "Öneri sahipliği 3-yollu: K006-recommendation / K008-recommendation / K020-recomenataion — kapsam sınırı netleşmedi (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# K006 — Yapay Zeka · Kapsam

> v2 §2.6 kök kapsamının K006 çapraz bağlantılı dosyasıdır. Kapsam sınırı ADR-097 §5.1
> kök dosya listesi + v2 §2.6 tanımı ile sabittir. Kapsam Dışı (Out of Scope) bölümü bağlayıcıdır.

---

## 1. K006 Nedir (v2 §2.6 — birebir kapsam)

v2 §2.6, K006'yı "Ürün tarafı yapay zekâ" olarak tanımlar. K006'nın ürün tarafı işlevi:

| Kapsam içi | K006 alt düğümü | v2 §2.6 satır kaydı |
|---|---|---|
| Öneri motoru (recommendation) | K006.8-recommendation | v2 §2.6 satır 1 |
| Kişiselleştirme (personalization) | K006.7-personalization | v2 §2.6 satır 2 |
| Müzik analizi (music analysis) | K006.6-music-analysis | v2 §2.6 satır 3 |
| Ses zekâsı (audio intelligence) | K006.1-audio-intelligence | v2 §2.6 satır 4 |
| Model yönetimi (model management) | K006.5-model-management | v2 §2.6 satır 5 |
| Tahmin (inference) | K006.3-inference | v2 §2.6 satır 6 |
| Yerel çalışma (local inference) | K006.4-local-inference | v2 §2.6 satır 7 |
| Değerlendirme (evaluation) | K006.2-evaluation | v2 §2.6 satır 8 |
| Test | K006.9-testing | v2 §2.6 satır 9 |

9 alt düğüm, v2 §2.6 satır sırasıyla diske yazıldı (`.ai/architecture/K006-yapay-zeka/`
dizin listesi 9/9 eşleşir — 2026-10-12 keşif kaydı, ai-master.md §4).

---

## 2. Kapsam İçi / Kapsam Dışı (bağlayıcı)

### 2.1 Kapsam İçi (IN)

1. Öneri algoritmaları (içerik, işbirliği, hibrit) ve kişiselleştirme mantığı — K006.8 / K006.7.
2. Müzik/ses analizi özellikleri (tempo, tür, embedding) — K006.6 / K006.1.
3. Model yükleme, versioning, registry — K006.5.
4. Tahmin servisi ve yerel çalışma (ONNX, llama.cpp, ggml) — K006.3 / K006.4.
5. Offline/online değerlendirme metrikleri ve drift — K006.2.
6. ML test stratejisi (pipeline, seed determinizmi, veri sözleşmeleri) — K006.9.
7. Karar veri: `AIEngine`, `contract`, `AIOrchestrator::getRecommendations` K006-tarafı çağrıları.

### 2.2 Kapsam Dışı (OUT) — bağlayıcı

| Kapsam Dışı | Sahibi | Kaynak |
|---|---|---|
| Agent altyapısı, A2A, tool-calling, MCP, RAG | **K016-AI** | registry §6 satır 94 |
| `AIOrchestrator::dispatchTask`, `AIWorkflow` orchestration (agent akışı) | K016-AI | ai-master.md §2.3 |
| `KnowledgeBase` (RAG) | K016-AI | ai-master.md §2.3 |
| `MemorySystem`, `PromptEngine`, `ToolCalling` | K016-AI | ai-master.md §2.3 |
| `Shared/Memory` (prompt cache) | K016-AI (deplasman) | ai-master.md §2.3 |
| Öneri sahipliği (K008 / K020) | **⚠️ karar bekliyor** | ai-master.md §8-Q1 |

> **Kapsam kuralı (bağlayıcı):** agent/MCP/RAG altyapısı K006'ya taşınmaz. K006 yalnız
> ürün tarafı ML işlevini kapsar (v2 §2.6 + ADR-097 §5.1).

---

## 3. Sınır Kararları (karar metni)

| # | Karar | Dayanak |
|---|---|---|
| S1 | K006 kök = `K006-yapay-zeka` dizini, kök dosya = 12 adet Türkçe rol sonlu | ADR-097 §5.1 |
| S2 | 9 çocuk = v2 §2.6 satır sırası, dizin adı İngilizce, dosya adı Türkçe sonlu | v2 §4.5 |
| S3 | K006.8-recommendation'ın 3-yollu sahipliği çözülene kadar dosyada `⚠️` | ai-master.md §8-Q1 |
| S4 | `shared/src/AI/*` içindeki K016 sınıfları K006 kapsamına **alınmaz** (deplasman kaydı) | registry §6 satır 94 |
| S5 | Yerel çalışma (llama.cpp / ggml / whisper.cpp) K006.4 kapsamındadır; agent çalışma zamanı K016'da | v2 §2.6 satır 7 |
| S6 | `local-inference` ile `inference` ayrımı: K006.4 = çalışma zamanı/rt, K006.3 = tahmin sözleşmesi | v2 §2.6 satır 6–7 |

---

## 4. K006 İçinde Sınır Bozanlar (open questions → kök dosyaya taşındı)

| # | Soru | Kök kayıt |
|---|---|---|
| OQ-1 | Öneri sahipliği 3-yollu (K006 / K008 / K020) | ai-master.md §8-Q1 · riskler.md R-05 |
| OQ-2 | `shared/src/AI/CLAUDE.md` K016 sınıflarını listeliyor | ai-master.md §2.3 · riskler.md R-07 |
| OQ-3 | `AIOrchestrator::dispatchTask` K006 mu K016 mı | ai-master.md §8-Q3 |

---

## 5. Kaynaklar

1. v2 §2.6 K006 — `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md` (satır 251–268) — okundu 2026-10-12.
2. ADR-097 §5.1 — `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` — okundu 2026-10-12.
3. `.ai/architecture/index.md` §6 satır 94 (K006↔K016) — okundu 2026-10-12.
4. `shared/src/AI/` — 8 dosya (7 sınıf + CLAUDE.md), 1.988 satır — okundu 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · v2 §2.6 + ADR-097 §5.1 + repo dosya listesi çapraz okundu`
