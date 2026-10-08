---
title: "CoreMusic — RAG.md Üretim Şablonu"
type: template
category: documentation
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-10-07
---

# CoreMusic — RAG.md Üretim Şablonu

**Zorunlu Bağlantılar:** [[.templates/index]] · [[./context-md-template]] · [[../claude-md-template]] · [[../../RAG]]

---

## §1 Amaç

Bu şablon, CoreMusic ekosisteminde bir **RAG.md (Retrieval Index + Pipeline) dosyası** üretmek için dış iskeleti, zorunlu tablo formatlarını ve doğrulama listesini tanımlar. RAG.md'nin işi: AI'ın "bu soru hangi dosyayı okumalı?" sorusunu **tek tablodan** çözmesi (retrieval indeksi) + gelecekteki embedding/retrieval sisteminin tasarımını dürüst durum etiketiyle taşıması (pipeline, genelde PLANNED). Dosya `[[.templates/index]]` registry'sine kayıtlı bu şablondan üretilir (Guardrail #16).

| Alan | Değer |
|------|-------|
| Template Name | `rag-md-template.md` |
| Template Path | `.ai/.templates/documentation/rag-md-template.md` |
| Hedef Dosya Tipi | `RAG.md` (retrieval indeksi + pipeline tasarımı) |
| Hedef Konum | `.ai/` (SSOT) + kök (pointer) |
| Guardrail | #16 (Template Mandatory) + #3 (Zero-Hallucination) |
| Birincil Yazar | Master Orchestrator (vault-updater) |
| İskelet | H1 + §1 Amaç → §7 Referanslar (7 bölüm + frontmatter) |
| Zorunlu Blok | §3.1 Retrieval indeks tablosu + §3.2 Pipeline tablosu (durum sütunu!) |
| Kanıt — Vault | `.ai/RAG.md`, `.ai/index.md`, `.ai/keys.md`, `.ai/.decisions/accepted/ADR-030-*` |
| Kanıt — Kural | Guardrail #3 (durum = kanıt), ADR-030 (RAG/embedding durumu) |
| Korunan Bilgi | 7 alanlı frontmatter, **durum sütunu (IMPLEMENTED/PLANNED)**, glob ile kanıtlanan sayılar |
| Değişken Formatı | `{{VARIABLE}}` (PROJECT_NAME, DATE, VERSION, INDEX_ROWS, PIPELINE_STATUS, MEASURE_CMD) |
| Dil | Türkçe (mojibake YASAK); dosya adı `RAG.md` (İngilizce) |
| Authority | Template (Guardrail #16) — Registry: `.ai/.templates/index.md` |
| Governance | Red Team · Human Mode · Truth Mode |

### §1.1 Bu Şablon Ne İçin — Ne İçin Değil

| İhtiyaç | Doğru Şablon | Bu Şablon Kullanılmaz |
|---------|--------------|------------------------|
| RAG.md (indeks + pipeline) | ✅ Bu şablon | — |
| Vault envanteri (CONTEXT.md) | `[[./context-md-template]]` | ❌ |
| Embedding kodu (PHP/Python) | `[[../backend/php-template]]` | ❌ — bu şablon yalnız .md tasarımı |
| AI stratejisi karar kaydı | `[[../adr/adr-template]]` (ADR-030) | ❌ — ADR ≠ indeks |

---

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `RAG.md` dış iskeleti (frontmatter + §1-§7) | Gerçek embedding/vektör kodu (pipeline = tasarımı, kod ayrı) |
| Retrieval indeks tablosu (konu → dosya → keyword) | Dosya içeriği özeti (yalnız yol + rol + keyword) |
| Pipeline tasarım tablosu (chunk/embedding/store/arama) | ADR metinleri (yalnız wiki-link) |
| Durum disiplini (IMPLEMENTED / PLANNED / UNKNOWN) | Sürücü/servis envanteri |

- **Ön koşul:** İndeks satırları **glob/ls ile diskten** üretilir; diskte olmayan dosya satıra girmez (Guardrail #3).
- **Durum kuralı:** Pipeline bileşenlerinin kodu yoksa durum `PLANNED` (kanıt: ilgili ADR, ör. ADR-030 "RAG/embedding: kod 0, tablo 0"). `IMPLEMENTED` iddiası için dosya kanıtı zorunlu.

---

## §3 Mimari — Üretilen RAG.md İskeleti

### §3.1 Dış İskelet (Frontmatter)

````markdown
---
title: "CoreMusic — RAG Retrieval Index & Pipeline"
type: docs
category: docs
docType: rag
version: {{VERSION}}
status: active
authority: "SSOT: .ai/RAG.md"
updated: {{DATE}}
date: {{DATE}}
---

# CoreMusic — RAG Retrieval Index & Pipeline

**Zorunlu Bağlantılar:** [[.templates/index]] · [[CLAUDE.md]] · [[index]] · [[keys]]
````

### §3.2 § Bölümleri ve Tablo Kalıpları

| Bölüm | İçerik | Kalıp |
|-------|--------|-------|
| §1 Amaç | "AI analiz hızı için: hangi soru → hangi dosya" 1 paragraf + ADR tablosu | serbest |
| §2 Kapsam | Kapsam / Kapsam Dışı tablosu | standart |
| §3 Mimari — **Retrieval İndeksi** | Ana tablo | §3.3 |
| §4 Mimari — **Pipeline Tasarımı** | Bileşen tablosu + durum | §3.4 |
| §5 Kurallar | Zero-Hallucination + güncelleme tetikleyicileri | §3.5 |
| §6 Doğrulama | Tekrarlanabilir sayım/ölçüm komutu | §3.6 |
| §7 Referanslar | wiki-link tablosu | standart |

### §3.3 Retrieval İndeks Tablosu (Zorunlu Sütunlar)

| # | Konu / Soru Sınıfı | Hedef Dosya(lar) | Keyword'ler | Durum |
|---|--------------------|------------------|-------------|-------|
| 1 | `{{ör. Guardrail/yasak nedir?}}` | `{{.ai/CLAUDE.md}} §7 | {{guardrail, yasak, revert}}` | ✅ diskte |

- **Sütunlar sabittir** (5). Dosya yolu glob ile doğrulanır.
- En az bu gruplar kapsanır: boot/okuma sırası · anayasa/guardrail · agent routing · süreç/faz · envanter · şablonlar · ADR · mimari katmanlar · UI/mockup · DB/şema · skill'ler · RAG'in kendisi.

### §3.4 Pipeline Tasarım Tablosu (Zorunlu Durum Sütunu)

| # | Bileşen | Tasarım (karar) | Durum | Kanıt |
|---|---------|----------------|-------|-------|
| 1 | Chunk stratejisi | `{{ör. §-bazlı, ~500 token}}` | PLANNED/IMPLEMENTED | `{{ADR-030 / dosya}}` |
| 2 | Embedding adayı | `{{model/servis}}` | … | … |
| 3 | Vektör store | `{{ör. SQLite-vss / MySQL / harici}}` | … | … |
| 4 | Arama sırası | `{{keyword → BM25 → vektör}}` | … | … |
| 5 | Güncelleme tetikleyicisi | `{{ör. commit hook / vault-sync-post}}` | … | … |
| 6 | Kaynak kapsamı | `{{.ai/**/*.md + kod?}}` | … | … |

**Durum sözlüğü (tutarlı):** `IMPLEMENTED` (kod/dosya diskte) · `PLANNED` (ADR/plan var, kod yok) · `UNKNOWN` (kanıt yok — uydurulmaz).

### §3.5 Kurallar Bloğu

1. İndeks satırı = disk kanıtı (glob); olmayan dosya yazılmaz.
2. Pipeline durumu kanıtsız `IMPLEMENTED` yapılmaz (ADR-030 vb. ile hizalanır).
3. Her vault yeniden yapısında (dosya ekleme/silme) indeks satırı güncellenir → tetikleyici: `vault-sync-post`.
4. Sayı/istatistik iddiası ölçümle (`{{MEASURE_CMD}}`).

### §3.6 Doğrulama Bloğu (tekrarlanabilir komut)

```bash
# İndeks kapsadığı dosyalar gerçekten diskte mi?
node .ai/scripts/validate.mjs --check
```

---

## §4 Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Frontmatter 7 alan | Doküman reddedilir |
| 2 | İndeks satırları glob ile doğrulanır; hayalet dosya yasak | Zero-Hallucination ihlali |
| 3 | Durum sütunu her satırda dolu (IMPLEMENTED/PLANNED/UNKNOWN) | Belirsizlik — revert |
| 4 | `IMPLEMENTED` iddiası dosya kanıtlı değilse PLANNED'a düşürülür | Yalancı durum |
| 5 | Pipeline = tasarım; kod dosyası bu şablonla üretilmez | Kapsam ihlali |
| 6 | Placeholder `{{VARIABLE}}` kalamaz | Doğrulama ihlali |
| 7 | Wiki-link `[[...]]` formatı | Link check kırmızı |
| 8 | Boot listesi (13 kanonik) bu dosyayla DEĞİŞTİRİLMEZ | Sayım zinciri bozulur |

---

## §5 Workflow

```
DISK ÖLÇ (glob: .ai/**/*.md + kök + .templates + decisions)
→ KONU GRUPLARINI BELİRLE → İNDEKS TABLOSU (konu→dosya→keyword→durum)
→ ADR'LERDEN PIPELINE DURUMU OKU (ADR-030 vb.) → PIPELINE TABLOSU
→ GÜNCELLEME TETİKLEYİCİSİNİ YAZ → GUARDRAIL #16 + #3 DOĞRULA
→ INDEX/keys/CONTEXT'E 1'ER SATIR EKLE → COMMIT (subagent atmaz)
```

---

## §6 Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 alan |
| 2 | İndeks tablosu | 5 sütun; her hedef dosya diskte (Test-Path) |
| 3 | Pipeline tablosu | Durum sütunu her satırda dolu |
| 4 | Durum tutarlılığı | ADR-030 ile çelişen IMPLEMENTED yok |
| 5 | Placeholder | `{{` kalmadı |
| 6 | Wiki-link | Hedefler var |
| 7 | Sayılar | Ölçüm komutu ile kanıtlı |
| 8 | Boot listesi | 13 kanonik sayı değişmedi |
| 9 | Çift katman | Kök `RAG.md` = pointer (≤80 satır), `.ai/RAG.md` = SSOT |
| 10 | Registry | `.templates/index.md`'e 1 satır eklendi |

---

## §7 Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter + kayıt satırı |
| Context şablonu | [[./context-md-template]] | Envanter/iskelet kuralları |
| Vault anayasası | [[../CLAUDE.md]] | Guardrail #3 / #16 |
| RAG strateji ADR'si | [[../.decisions/accepted/ADR-030-ai-strategy-core]] | Pipeline durum kanıtı |
| Master katalog | [[../../index.md]] | İndeks satırlarının kaynağı |
| Keyword haritası | [[../../keys.md]] | Retrieval keyword eşlemesi |
| Doğrulama betiği | `.ai/scripts/validate.mjs` | 8 check |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-10-07
