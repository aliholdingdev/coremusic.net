---
type: architecture
category: ai
title: "CoreMusic — RAG (Retrieval Augmented Generation) System"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — RAG (Retrieval Augmented Generation) System

**Zorunlu Bağlantılar:** [[architecture/ai/index]] · [[architecture/ai/knowledge-base]] · [[architecture/ai/memory-system]] · [[architecture/ai/prompt-engine]] · [[ADR-030-ai-strategy-core]]

---

## 1. Amaç

CoreMusic RAG sistemi, kullanıcının sorgularını vault dokümanlarından, veritabanlarından ve bilgi bankasından alarak LLM'lere bağlam zenginleştirmesi sağlayan **Retrieval Augmented Generation** altyapısıdır. Zero Hallucination politikasını destekler.

---

## 2. RAG Mimarisi

```
┌─────────────────────────────────────────────────────────┐
│  Kullanıcı Sorgusu                                       │
└──────────────────────┬──────────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────────┐
│  Query Processing Layer                                  │
│  ├── Query Understanding (NLU)                          │
│  ├── Intent Classification                              │
│  ├── Entity Extraction                                  │
│  └── Query Expansion (Synonyms, Hyonyms)                │
└──────────────────────┬──────────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────────┐
│  Retrieval Layer                                         │
│  ├── Vector Search (Semantic)                            │
│  ├── Keyword Search (BM25)                               │
│  ├── Hybrid Search (Vector + Keyword)                    │
│  └── Metadata Filter (Category, Date, ADR)               │
└──────────────────────┬──────────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────────┐
│  Reranking Layer                                         │
│  ├── Cross-Encoder Reranking                            │
│  ├── Relevance Scoring                                  │
│  └── Deduplication                                       │
└──────────────────────┬──────────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────────┐
│  Generation Layer                                        │
│  ├── Context Injection                                  │
│  ├── Prompt Assembly                                    │
│  ├── LLM Call (Claude / GPT / Gemini)                   │
│  └── Response Validation                                │
└──────────────────────┬──────────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────────┤
│  Output: Doğrulanmış Yanıt + Kaynak Referansları        │
└─────────────────────────────────────────────────────────┘
```

---

## 3. Retrieval Yöntemleri

### 3.1 Vector Search (Semantic)

| Özellik | Değer |
|---------|-------|
| Embedding Model | `text-embedding-3-small` (1536 dim, $0.02/1M tokens) veya `text-embedding-3-large` (3072 dim, $0.13/1M tokens) |
| Vector DB | PostgreSQL pgvector v0.8.6 (Postgres 18 desteği) veya ChromaDB |
| Dimension | 1536 (small) / 3072 (large) — Matryoshka representation learning ile boyut kısaltma desteği: 256/512/1024 |
| Max Input | 8191 tokens (text-embedding-3-small/large) |
| Similarity Metric | Cosine Similarity |
| Chunk Size | 512 token |
| Chunk Overlap | 64 token |

**Kullanım Alanları:**
- Anlamsal benzerlik arama
- Soru-cevap eşleştirme
- Doküman önerisi

### 3.2 Keyword Search (BM25)

| Özellik | Değer |
|---------|-------|
| Algoritma | BM25 (Okapi) |
| Stop Words | Türkçe + İngilizce |
| Stemming | Porter Stemmer |
| Index | Inverted Index |

**Kullanım Alanları:**
- ADR numarası ile arama
- Dosya adı ile arama
- Teknik terim arama

### 3.3 Hybrid Search

| Ayar | Değer |
|------|-------|
| Vector Weight | 0.7 |
| Keyword Weight | 0.3 |
| Fallback | Keyword Search (vector bulunamazsa) |

---

## 4. Document Chunking

### 4.1 Chunking Stratejisi

| Doküman Tipi | Chunk Boyutu | Overlap | Yöntem |
|--------------|-------------|---------|--------|
| ADR Dosyaları | 512 token | 64 token | Section-based |
| Architecture Docs | 1024 token | 128 token | Heading-based |
| Code Blocks | 256 token | 32 token | Function-based |
| Wiki-Link'ler | Tam blok | — | Link-based |

### 4.2 Metadata Extraction

Her chunk için çıkarılan metadata:

```json
{
  "source": "ADR-064-electronics-platform-architecture.md",
  "category": "electronics",
  "section": "3.2 L0-L6 Katman Yapısı",
  "version": "1.0.0",
  "last_updated": "2026-08-09",
  "author": "Bayram Ali",
  "tags": ["electronics", "platform", "architecture"],
  "related_adrs": ["ADR-061", "ADR-062", "ADR-063"],
  "file_path": ".ai/decisions/accepted/ADR-064-*.md"
}
```

---

## 5. Embedding Pipeline

```text
Input Document
    │
    ▼
Text Extraction (Markdown → Plain Text)
    │
    ▼
Chunking (Section-based / Token-based)
    │
    ▼
Metadata Extraction (Auto + Manual)
    │
    ▼
Embedding Generation (text-embedding-3-small)
    │
    ▼
Vector Storage (pgvector / ChromaDB)
    │
    ▼
Index Update (Incremental / Full Reindex)
```

---

## 6. Query Processing

### 6.1 Intent Classification

| Intent | Örnek | Aksiyon |
|--------|-------|---------|
| `search_document` | "ADR-064 ne diyor?" | Doc retrieval |
| `explain_concept` | "RAG nedir?" | Concept + Doc |
| `compare` | "ASIO vs WASAPI" | Multi-doc retrieval |
| `troubleshoot` | "CSRF hatası alıyorum" | Error + Solution |
| `generate_code` | "PHP auth modülü yaz" | Doc + Code gen |

### 6.2 Query Expansion

```text
Orijinal: "CoreMusic electronics mimarisi"
    │
    ▼
Expanded:
  - "CoreMusic electronics architecture"
  - "CoreMusic elektronik platform"
  - "CoreMusic hardware firmware driver"
  - "ADR-064 electronics"
```

---

## 7. Reranking

### 7.1 Cross-Encoder Reranking

| Model | Kullanım | Hız |
|-------|----------|-----|
| `cross-encoder/ms-marco-MiniLM-L-6-v2` | Genel | Hızlı |
| `cross-encoder/ms-marco-MiniLM-L-12-v2` | Hassas | Yavaş |

### 7.2 Relevance Scoring

```text
Final Score = (Vector Similarity × 0.4) +
              (BM25 Score × 0.2) +
              (Metadata Match × 0.2) +
              (Recency Score × 0.1) +
              (Authority Score × 0.1)
```

---

## 8. Generation Pipeline

### 8.1 Context Assembly

```text
System Prompt (CoreMusic Rules)
    +
Retrieved Documents (Top-K chunks)
    +
User Query
    +
Chat History (son 5 mesaj)
    │
    ▼
Final Prompt
```

### 8.2 Response Validation

| Kontrol | Yöntem | Hata |
|---------|--------|------|
| Hallucination | Source check | `⚠️ VERIFICATION REQUIRED` |
| ADR Compliance | ADR cross-ref | Uyumsuzluk uyarısı |
| Freshness | Date check | Eski bilgi uyarısı |
| Completeness | Coverage check | Eksik bilgi uyarısı |

---

## 9. Knowledge Sources

| Kaynak | Tür | Güncelleme | Öncelik |
|--------|-----|-----------|---------|
| `.ai/` vault | Markdown | Manuel | P0 |
| `.sql/` schemas | SQL | Manuel | P1 |
| `.claude/rules/` | Markdown | Manuel | P0 |
| `decisions/accepted/` | ADR | Manuel | P0 |
| `architecture/` | Markdown | Manuel | P1 |
| `electronic/` | Markdown | Manuel | P1 |
| `projects/` | Markdown | Manuel | P2 |

---

## 10. Zero Hallucination Desteği

### 10.1 Kaynak Zorunluluğu

Her yanıt aşağıdaki kaynakları içermelidir:

```text
Yanıt:
  CoreMusic Electronics, 5 cihaz ailesini destekler...
  
Kaynaklar:
  1. [[ADR-064-electronics-platform-architecture]] §3.2
  2. [[electronic/device-architecture]] §4.1
  3. [[electronic/platform-architecture]] §2
```

### 10.2 Doğrulama Akışı

```text
LLM Yanıtı
    │
    ▼
Kaynak Doğrulama (Her claim için)
    │
    ├──✅ Kaynak bulundu → Yanıt geçerli
    │
    └──❌ Kaynak bulunamadı →
            │
            ▼
        `⚠️ VERIFICATION REQUIRED` etiketi ekle
```

---

## 11. Performance Hedefleri

| Metrik | Hedef | Max |
|--------|-------|-----|
| Query Latency | <500ms | 2s |
| Retrieval Latency | <200ms | 500ms |
| Generation Latency | <2s | 5s |
| Embedding Latency | <100ms | 300ms |
| Accuracy (MRR@10) | >0.8 | — |
| Hallucination Rate | <5% | 10% |

---

## 12. Cache Stratejisi

| Seviye | Tür | Süre | Kullanım |
|--------|-----|------|----------|
| L1 (Hot) | Query Result | 5 dk | Sık sorulan sorular |
| L2 (Warm) | Embedding | 24 saat | Document embeddings |
| L3 (Cool) | Full Response | 1 saat | Uzun yanıtlar |

---

## 13. API Sözleşmesi

### POST /api/v1/rag/query

```json
{
  "query": "CoreMusic electronics mimarisi nasıl?",
  "options": {
    "max_results": 5,
    "search_type": "hybrid",
    "include_sources": true,
    "language": "tr"
  }
}
```

### Yanıt:

```json
{
  "answer": "CoreMusic Electronics, L0-L6 katman yapısıyla...",
  "sources": [
    {
      "file": "ADR-064-electronics-platform-architecture.md",
      "section": "3.2",
      "relevance": 0.95
    }
  ],
  "confidence": 0.92,
  "hallucination_check": "passed"
}
```

---

## 14. Entegrasyon Noktaları

| Sistem | Entegrasyon | ADR |
|--------|------------|-----|
| AI Engine | Query → RAG → Response | [[ADR-030-ai-strategy-core]] |
| Knowledge Base | Embedding → Vector DB | [[architecture/ai/knowledge-base]] |
| Prompt Engine | Context injection | [[architecture/ai/prompt-engine]] |
| Tool Calling | Doc retrieval tool | [[architecture/ai/tool-calling]] |
| MCP Integration | Resource exposure | [[architecture/ai/mcp-integration]] |

---

## 15. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| RAG | [[architecture/ai/knowledge-base]] | Bilgi kaynağı |
| RAG | [[architecture/ai/memory-system]] | Session hafızası |
| RAG | [[architecture/ai/prompt-engine]] | Prompt entegrasyonu |
| RAG | [[ADR-030-ai-strategy-core]] | AI stratejisi |
| RAG | [[ADR-005-ultrathink-protocol]] | Zero Hallucination |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Active |
| Sections | 16 |
| Retrieval Methods | 3 (Vector, Keyword, Hybrid) |
| Chunking Strategies | 4 |
| Intent Types | 5 |
| Performance Targets | 6 |
| Cache Levels | 3 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
