---
title: "Session Log Template"
type: template
category: session
version: 2.0.1
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-29
date: 2026-08-23
template_id: TPL-SESSION-LOG
risk: "Düşük — salt metin şablon; {PLACEHOLDER} kalırsa yanlış oturum kaydı riski"
reuse_note: "Oturum kapanışında kopyalanır; §3 alanları doldurulur (Guardrail #16)"
---

# Session Log Template

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

---

## 1. Amaç

CoreMusic agent oturum kaydını standartlaştırmaktır: oturum bilgisi (Session Info), özet, görevler, kararlar, sorunlar, değişen dosyalar, sonraki adımlar, vault güncellemeleri ve notlar alanlarını tek formatta toplar — böylece oturumlar arası devamlılık ve audit trail korunur.

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Her oturum (session) kapanışında yazılan Markdown kayıt | Kod/shema değişikliklerinin kendisi |
| Görev, karar, sorun, dosya değişikliği tabloları | ADR metinleri (frozen — ADR-042) |
| Vault Updates kontrol listesi (log.md, MEMORY.md, ADR) | CI/CD pipeline kayıtları |

- **Dosya tipi:** Markdown oturum kaydı
- **Kullanan agent:** Tüm ajanlar (oturum kapanışında); vault-sync/vault güncellemesi Master Orchestrator koordinasyonunda (AGENTS.md §7.7, §14)
- **Guardrail:** #16 (Template Mandatory) — yeni oturum kaydı bu şablondan başlar

## 3. Mimari

Oturum kaydının tam alan yapısı. Not: bu şablonun placeholder'ları **tek süslü parantez** formatındadır (`2026-10-25-faz-part-toplu-calistirma-plani-yazildi-ai-todo-4a-7-part-ta`, `{{...}}` vault formatından farklıdır) ve birebir korunmuştur.

### 3.1 Session Info

| Field | Value |
|-------|-------|
| Session ID | 2026-10-25-faz-part-toplu-calistirma-plani-yazildi-ai-todo-4a-7-part-ta |
| Date | 2026-10-25 |
| Start Time | ⚠️ VERIFICATION REQUIRED (elle girilecek) |
| End Time | 15:24 |
| Duration | ⚠️ VERIFICATION REQUIRED (elle girilecek) |
| Status | completed |

### 3.2 Summary

⚠️ VERIFICATION REQUIRED — `--summary` ile eklenmedi, elle doldurulacak.

### 3.3 Tasks

| # | Task | Status | Notes |
|---|------|--------|-------|
| 1 | Faz/Part toplu calistirma plani yazildi (.ai TODO §4A = 7 PART tam metin + kok TODO §ozet; A||B-C-D-E-F sira, G buyuk isler) | completed | agent: mo |
| 2 | — | — | (ikinci gorev verilmedi) |

### 3.4 Decisions

| Decision | Rationale |
|----------|-----------|
| — | (karar verilmedi — gerekiyorsa elle ekle) |

### 3.5 Issues

| Issue | Resolution |
|-------|------------|
| — | (sorun yok — gerekiyorsa elle ekle) |

### 3.6 Files Changed

| File | Action |
|------|--------|
| — | (dosya degisikligi verilmedi — `--files` ile eklenmeli) |

### 3.7 Next Steps

- [ ] (sonraki adim verilmedi — `--next` ile eklenmeli)
- [ ] —

### 3.8 Vault Updates

- [ ] log.md updated
- [ ] MEMORY.md updated
- [ ] ADR created/updated

### 3.9 Notes

Additional notes about the session.

---

## 4. Kurallar

Zorunlu / yasak kurallar:

- **Zorunlu:** §3.1-§3.9 alanlarının tamamı bulunur; boş bırakılan alan `—` ile "dolduruldu" gibi gösterilmez, gerçek içerik yazılır.
- **Zorunlu:** tüm `{...}` placeholder'ları (`2026-10-25-faz-part-toplu-calistirma-plani-yazildi-ai-todo-4a-7-part-ta`, `2026-10-25`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `15:24`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `—`, `Faz/Part toplu calistirma plani yazildi (.ai TODO §4A = 7 PART tam metin + kok TODO §ozet; A||B-C-D-E-F sira, G buyuk isler)`, `—`, `(ikinci gorev verilmedi)`, `—`, `(karar verilmedi — gerekiyorsa elle ekle)`, `—`, `(sorun yok — gerekiyorsa elle ekle)`, `—`, `(dosya degisikligi verilmedi — `--files` ile eklenmeli)`, `(sonraki adim verilmedi — `--next` ile eklenmeli)`, `—`) doldurulmadan oturum kapatılamaz.
- **Zorunlu:** §3.8 Vault Updates — oturum sonunda `log.md`'ye giriş eklenir ve `MEMORY.md` session state güncellenir (AGENTS.md §7.7); ADR gerektiğinde oluşturulur/güncellenir.
- **Zorunlu:** `log.md`'ye yazım append-only'dir; geçmiş satıra dokunulmaz (AGENTS.md §25.3).
- **Zorunlu:** §3.6 Files Changed gerçek dosya yollarını ve action'ı (added/modified/deleted) içerir.
- **Yasak:** hassas veri (secret, token, credential) kayda yazılır — `log.md` içinde `[REDACTED]` ile maskele (AGENTS.md §17.3).
- **Uyarı:** doğrulanamayan durum/karar `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

## 5. Workflow

```
ŞABLONU SEÇ → KOPYALA → {PLACEHOLDER} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/session-log-template.md` (Guardrail #16).
2. **KOPYALA:** oturum kapanışında (vault-sync öncesi, AGENTS.md §14) yeni kayıt oluştur.
3. **{PLACEHOLDER} DOLDUR:** §3.1 Session Info (`2026-10-25-faz-part-toplu-calistirma-plani-yazildi-ai-todo-4a-7-part-ta`, `2026-10-25`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `15:24`, `⚠️ VERIFICATION REQUIRED (elle girilecek)`, `completed`); §3.2 Summary; §3.3 Tasks; §3.4 Decisions; §3.5 Issues; §3.6 Files Changed; §3.7 Next Steps.
4. **GUARDRAIL #16 DOĞRULA:** 7 alanlı frontmatter + §1-§7 + tüm `{...}` placeholder'lar doldu + §3.8 Vault Updates işaretli.
5. **COMMIT:** kaydı commit et; `log.md` append-only giriş + `MEMORY.md` session state güncelle (AGENTS.md §7.7).

## 6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, version, status, authority, updated)
- [ ] §1-§7 var
- [ ] tüm {PLACEHOLDER}'lar dolduruldu
- [ ] dosya bu şablona uygun
- [ ] §3.8 Vault Updates işaretli; log.md append-only; hassas veri `[REDACTED]`

**REFACTOR REPORT:** FILE: session-log-template.md · PURPOSE: Session Log Template · VALIDATION: 7 alan + §1-§7 + bilgi korunumu · RELATED: [[.templates/index]] · [[../CLAUDE.md]]

## 7. Referanslar

- [[.templates/index]] — şablon registry (`.ai/.templates/index.md`)
- [[../CLAUDE.md]] — AI anayasası, 16 Hard Guardrail
- [[../AGENTS.md]] — §7.7 tamamlanma (log.md + MEMORY.md), §14 vault-sync, §17.3 `[REDACTED]`, §25.3 append-only
- `.ai/log.md` · `.ai/MEMORY.md` — §3.8 Vault Updates hedefleri

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
