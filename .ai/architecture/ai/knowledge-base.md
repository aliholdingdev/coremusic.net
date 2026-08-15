---
title: "CoreMusic — Knowledge Base"
type: architecture
category: knowledge-base
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Knowledge Base

**Zorunlu Bağlantılar:** [[index]] · [[brain.md]] · [[CLAUDE.md]]

---

## 1. Amaç

CoreMusic bilgi bankasının yönetimini tanımlar. Semantic search, RAG (Retrieval Augmented Generation), knowledge lifecycle, vault yapısı ve AI knowledge engineering süreçleri.

---

## 2. Knowledge Base Mimarisi

```
┌─────────────────────────────────────────────────────┐
│                Knowledge Base                        │
├─────────────────────────────────────────────────────┤
│  Vault (SSOT) → Vector DB → Semantic Search          │
├─────────────────────────────────────────────────────┤
│  RAG Pipeline → Embeddings → Query Engine            │
├─────────────────────────────────────────────────────┤
│  Metadata DB → Index → Knowledge Lifecycle           │
└─────────────────────────────────────────────────────┘
```

---

## 3. Semantic Search

| Motor | Kullanım | Teknoloji | Boyut |
|-------|----------|-----------|-------|
| Vector DB | ADR/Doc search | pgvector / ChromaDB | — |
| Full-text | Metadata search | MySQL FULLTEXT | — |
| Fuzzy | Approximate match | Levenshtein | — |
| Hybrid | Vektör + anahtar kelime | Combined ranking | — |

### 3.1 Semantic Search Akışı

```
Kullanıcı Sorgusu → Intent Recognition → Query Expansion → Vector Search → Ranking → Sonuç
       ↓                   ↓                    ↓                ↓            ↓         ↓
   "EQ ayarla"       EQ-related ADR      synonym ekle       cosine sim   BM25+Vec   Top-5
```

---

## 4. RAG (Retrieval Augmented Generation)

| Aşama | Açıklama | Çıktı |
|-------|----------|-------|
| **Retrieval** | İlgili belgeleri Vector DB'den çek | Top-k documents |
| **Augmentation** | Prompt'a bağlam ekle | Enhanced prompt |
| **Generation** | LLM ile yanıt üret | Yanıt + kaynak |

### 4.1 RAG Pipeline

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  User Query  │───▶│  Retriever   │───▶│  Augmenter   │
│              │    │  (Vector DB) │    │  (Context)   │
└──────────────┘    └──────────────┘    └──────────────┘
                                                │
                                                ▼
┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Response    │◀──│  Generator   │◀──│  Prompt      │
│  (Answer)    │    │  (LLM)       │    │  (Template)  │
└──────────────┘    └──────────────┘    └──────────────┘
```

### 4.2 RAG Kaynakları

| Kaynak | Tip | Kullanım |
|--------|-----|----------|
| ADR'ler | Karar kaydı | Mimari karar sorgulama |
| Architecture dosyaları | Teknik belge | Sistem tasarımı sorgulama |
| Kod kaynakları | Kaynak kodu | Fonksiyon/imza sorgulama |
| Dokümantasyon | Genel belge | Kullanım kılavuzu sorgulama |
| Test dosyaları | Test senaryosu | Test Coverage sorgulama |
| Electronic dosyaları | Donanım belgesi | HW/FW sorgulama |

---

## 5. Knowledge Sources

| Kaynak | Kategori | Doğrulama Durumu |
|--------|----------|-----------------|
| [[decisions/accepted/*]] | ADR (Karar Kaydı) | ✅ Verified |
| [[architecture/*]] | Mimari Belge | ✅ Verified |
| `src/**/*.php` | PHP Kaynak Kodu | ✅ Verified |
| `assets/**/*.js` | JS Kaynak Kodu | ✅ Verified |
| `tests/**/*.php` | Test Dosyası | ✅ Verified |
| `.sql/*.sql` | Veritabanı Şeması | ✅ Verified |
| `electronic/*` | Donanım Belgesi | ✅ Verified |
| Web araştırma sonuçları | Dış Kaynak | ⚠️ Unverified → `VERIFICATION REQUIRED` |
| Kullanıcı girdisi | İnsan girdisi | ⚠️ Unverified → Doğrulama gerekli |

---

## 6. Knowledge Types

| Tür | Tanım | Doğrulama | Vault Konumu |
|-----|-------|-----------|-------------|
| **Verified** | Doğrulanmış, kanıtlanmış bilgi | ✅ ADR + kaynak kodu | `knowledge/verified/` |
| **Unverified** | Henüz doğrulanmamış bilgi | ⚠️ Doğrulama bekliyor | `knowledge/unverified/` |
| **Rejected** | Yanlış/red edilmiş bilgi | ❌ Doğrulanamadı | `knowledge/rejected/` |

### 6.1 Knowledge Doğrulama Akışı

```
Yeni Bilgi → Kaynak Kontrolü → Doğrulama → Sınıflandırma → İndeksleme
     ↓              ↓               ↓             ↓              ↓
   Input       ADR/Kod/Doc     Verified?     Type assign    Vector DB
```

---

## 7. Indexing

| Index Tipi | Teknoloji | Kullanım |
|------------|-----------|----------|
| **TF-IDF** | Term frequency-inverse document frequency | Anahtar kelime arama |
| **Embeddings** | Vector embeddings (OpenAI / local) | Semantik arama |
| **BM25** | Best Matching 25 | Full-text arama |
| **Inverted Index** | Token → Document mapping | Hızlı arama |

### 7.1 Embedding Stratejisi

| Model | Boyut | Kullanım | Latency |
|-------|-------|----------|---------|
| text-embedding-3-small | 1536 | ADR, doc search | <50ms |
| Local embedding | 382 | Offline search | <10ms |
| Hybrid | Combined | En iyi sonuç | <100ms |

---

## 8. Query Processing

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Intent      │───▶│  Retrieval   │───▶│  Ranking     │───▶│  Response    │
│  Recognition │    │  (Vector)    │    │  (BM25+Vec)  │    │  Generation  │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘
       │                   │                   │                   │
   NLU/NER           Top-k documents      Score fusion       LLM + Sources
   Query parse       Cosine similarity   Re-ranking          Answer + Ref
```

### 8.1 Query Processing Adımları

| Adım | Açıklama | Çıktı |
|------|----------|-------|
| 1. Intent Recognition | Sorgu niyetini anla | Query type |
| 2. Query Expansion | Synonym ve ilgili terimler ekle | Expanded query |
| 3. Retrieval | Vector DB'den ilgili belgeleri çek | Top-k results |
| 4. Ranking | BM25 + cosine similarity ile sırala | Ranked results |
| 5. Response Generation | LLM ile yanıt üret | Answer + sources |

---

## 9. Knowledge Lifecycle

```
┌────────┐    ┌──────────┐    ┌─────────┐    ┌────────┐    ┌──────────┐    ┌─────────┐
│ Create │───▶│  Verify  │───▶│  Index  │───▶│  Serve │───▶│  Update  │───▶│ Archive │
└────────┘    └──────────┘    └─────────┘    └────────┘    └──────────┘    └─────────┘
     │              │              │              │              │               │
  Input         Doğrulama     Vector DB     Query API     Versiyonlama    Backup
  Yeni bilgi    Kaynak kontrol  İndeksleme   RAG pipeline  Değişiklik takibi  Saklama
```

### 9.1 Lifecycle Aşamaları

| Aşama | Açıklama | Sorumlu |
|-------|----------|---------|
| **Create** | Yeni bilgi oluşturulur | İlgili ajan |
| **Verify** | Kaynak kodu veya ADR ile doğrulama | QA Engineer |
| **Index** | Vector DB'ye indeksleme | Knowledge Service |
| **Serve** | RAG pipeline üzerinden sunma | AI Engine |
| **Update** | Bilgi güncellendiğinde yeniden indeksleme | vault-updater |
| **Archive** | Eski/Artık kullanılmayan bilgiyi arşivleme | MO |

---

## 10. Vault Yapısı

| Katman | İçerik | Boyut |
|--------|--------|-------|
| SSOT Core | CLAUDE.md, AGENTS.md, WORKFLOW.md | 9 dosya |
| ADR | decisions/accepted/* | 63+ ADR |
| Architecture | architecture/L0-L6/* | 50+ dosya |
| Projects | projects/NevaEngine/* | 20+ dosya |
| Electronic | electronic/* | 34+ dosya |
| Templates | .templates/* | 25+ template |
| AI Architecture | architecture/ai/* | 12 dosya |

---

## 11. Metadata Yönetimi

### 11.1 Müzik Metadata

| Alan | Tip | Kaynak |
|------|-----|--------|
| title | string | ID3/Vorbis |
| artist | string | ID3/Vorbis |
| album | string | ID3/Vorbis |
| genre | string | ID3/Vorbis |
| year | integer | ID3/Vorbis |
| bpm | float | Audio analysis |
| key | string | Audio analysis |
| energy | float | Audio analysis |
| cover_art | blob | Embedded/URL |

### 11.2 Sistem Metadata

| Alan | Tip | Kaynak |
|------|-----|--------|
| file_path | string | System |
| file_size | integer | System |
| format | string | FFprobe |
| sample_rate | integer | FFprobe |
| bit_depth | integer | FFprobe |
| channels | integer | FFprobe |

---

## 12. Cache Strategy

| Seviye | İçerik | TTL | Güncelleme |
|--------|--------|-----|------------|
| L1 Hot | SSOT dosyaları | Oturum sonu | Otomatik |
| L2 Warm | ADR, architecture | Görev sonu | Dosya değişikliği |
| L3 Cool | Testing, UI design | İsteğe bağlı | Manuel |

---

## 13. AI Knowledge Engineering

| Süreç | Açıklama | Araç |
|-------|----------|------|
| **Doğrulama** | Bilginin kaynak kodu veya ADR ile eşleşme kontrolü | `vault-integrity-check.ps1` |
| **Güncelleme** | Eski bilginin yenisiyle değiştirilmesi | In-place update + log |
| **Arşivleme** | Kullanılmayan bilginin arşive taşınması | `vault-archive.ps1` |
| **Temsil** | Bilginin vektörel temsili (embedding) | Vector DB pipeline |
| **Sorgulama** | RAG ile bilgi sorgulama | Query engine |

---

## 14. Security

| Kural | Açıklama |
|-------|----------|
| SSOT Principle | Bilgi sadece .ai/ vault'tan |
| No Secrets | Hassas veri vault'a yazılmaz |
| Access Control | Agent bazlı erişim |
| Audit Trail | Tüm erişimler log.md'ye |
| Redaction | Hassas veri `[REDACTED]` ile maskelenir |
| Knowledge Classification | Verified/Unverified/Rejected ayrımı |

---

## 15. Backup & Recovery

| Yöntem | Sıklık | Saklama |
|--------|--------|---------|
| Git History | Her commit | Sonsuz |
| Manuel Snapshot | Haftalık | 3 ay |
| Tam Vault Yedegi | Aylık | 1 yıl |

---

## 16. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 RAG | [[ai-engine]] | AI Engine entegrasyonu |
| § 5 Sources | [[ADR-040-database-authority]] | 18 BCNF DB |
| § 9 Lifecycle | [[MEMORY.md]] | Session hafızası |
| § 10 Vault | [[index.md]] | Master katalog |
| § 14 Security | [[ADR-022-database-hardened-security]] | Güvenlik |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
