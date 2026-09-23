---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Wiki Page Template"
type: wiki-template
category: template
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-09-23
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# CoreMusic — Wiki Page Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]]

## 1. Amaç

CoreMusic vault wiki sayfaları için standart şablonu sağlamaktır: başlık/kategori/durum/meta bilgisini; genel bakış ve detay bölümlerini; ilgili sayfalar tablosunu ve değişiklik geçmişini tek iskelette toplar. Kaynak: `reference_doc: Freelancer Technical Documentation v1.0`.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/` vault içindeki genel wiki sayfaları | ADR metinleri (frozen — bkz. ADR-042) |
| Genel bakış + detay + ilişkiler + değişiklik geçmişi | Kaynak kod dosyaları |
| Wiki-link ile çapraz referans | Denetim/rapor şablonları (bkz. security-audit, api-doc) |

- **Dosya tipi:** Markdown wiki sayfası
- **Kullanan agent:** Master Orchestrator / vault-updater (birincil · AGENTS.md §6: vault, documentation, wiki-link, index), tüm ajanlar okuyabilir
- **Guardrail:** #16 (Template Mandatory) — yeni wiki sayfası bu şablondan başlar

## 3. Mimari

Şablonun tam gövdesi. Not: gömme nedeniyle şablon başlıkları iki seviye derinleştirilmiştir (H1 → `###`, H2 → `####`); tüm `{{PLACEHOLDER}}` ve tablolar birebir korunmuştur.

### {{TITLE}}

**Kategori:** {{CATEGORY}}
**Durum:** {{STATUS}}
**Son Güncelleme:** {{DATE}}

---

#### 3.1 Genel Bakış

{{OVERVIEW_DESCRIPTION}}

---

#### 3.2 Detay

{{DETAIL_CONTENT}}

---

#### 3.3 İlgili Sayfalar

| Sayfa | İlişki |
|-------|--------|
| [[{{RELATED_PAGE_1}}]] | {{RELATION_1}} |
| [[{{RELATED_PAGE_2}}]] | {{RELATION_2}} |

---

#### 3.4 Değişiklik Geçmişi

| Tarih | Değişiklik | Sorumlu |
|-------|-----------|---------|
| {{DATE}} | İlk oluşturma | {{AUTHOR}} |

---

## 4. Kurallar

Zorunlu / yasak kurallar:

- **Zorunlu:** iç bağlantılar wiki-link formatında yazılır: `[[hedef]]` (§3.3 örnekleri); `.ai/` dışı kaynaklar için `[[../...]]` göreli biçim kullanılır.
- **Zorunlu:** §3.3 İlgili Sayfalar tablosu en az 2 satır içerir; her satırda ilişki (`{{RELATION_*}}`) açıklanır.
- **Zorunlu:** §3.4 Değişiklik Geçmişi append-only'dir — mevcut satıra dokunulmaz, yeni satır eklenir (AGENTS.md §25.3).
- **Zorunlu:** frontmatter 7 alan (title, type, category, version, status, authority, updated) eksiksiz yazılır.
- **Yasak:** `{{TITLE}}`, `{{CATEGORY}}`, `{{STATUS}}`, `{{DATE}}`, `{{OVERVIEW_DESCRIPTION}}`, `{{DETAIL_CONTENT}}`, `[[{{RELATED_PAGE_1}}]]`…`{{AUTHOR}}` placeholder'ları doldurulmadan sayfa commit edilemez.
- **Yasak:** kırık/wiki-link hedefi olmayan bağlantı bırakmak; hedef bilinmiyorsa `⚠️ VERIFICATION REQUIRED` yazılır.
- **Uyarı:** ADR metinleri frozen'dır — bu şablonla ADR içeriği düzenlenmez, yalnızca referanslanır (ADR-042).

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/documentation/WikiPage-Template.md` (Guardrail #16).
2. **KOPYALA:** dosyayı hedef wiki konumuna (`.ai/` altı) kopyala.
3. **{{PLACEHOLDER}} DOLDUR:** `{{TITLE}}`, `{{CATEGORY}}`, `{{STATUS}}`, `{{DATE}}`; §3.1 `{{OVERVIEW_DESCRIPTION}}`; §3.2 `{{DETAIL_CONTENT}}`; §3.3 `[[{{RELATED_PAGE_1}}]]`/`[[{{RELATED_PAGE_2}}]]` + ilişkiler; §3.4 `{{DATE}}`/`{{AUTHOR}}`.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm placeholder'lar doldu + tüm wiki-link'ler hedefe ulaşıyor.
5. **COMMIT:** sayfayı commit et; yeni sayfa ise `.templates/index`/vault indeksine ekle ve `log.md`'ye giriş yaz.

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {{PLACEHOLDER}}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] tüm wiki-link'ler geçerli hedeflere işaret ediyor

**REFACTOR REPORT:** FILE: WikiPage-Template.md · PURPOSE: Wiki Page Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../../AGENTS.md]] — routing (§6: wiki-link/vault → MO vault-updater), append-only kuralı §25.3
- `.ai/CLAUDE.md` · `.ai/AGENTS.md` · `.ai/brain.md` (frontmatter `reference`)
- `reference_doc: Freelancer Technical Documentation v1.0`

---

*Wiki Page Template v2.0.0 — CoreMusic Documentation Standards*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: {{DATE}}*
*Mode: Red Team · Human Mode · Truth Mode*
