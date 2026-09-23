---
title: "Session Log Template"
type: template
category: session
version: 2.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-23
date: 2026-08-23
---

# Session Log Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

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

Oturum kaydının tam alan yapısı. Not: bu şablonun placeholder'ları **tek süslü parantez** formatındadır (`{SESSION_ID}`, `{{...}}` vault formatından farklıdır) ve birebir korunmuştur.

### 3.1 Session Info

| Field | Value |
|-------|-------|
| Session ID | {SESSION_ID} |
| Date | {DATE} |
| Start Time | {START_TIME} |
| End Time | {END_TIME} |
| Duration | {DURATION} |
| Status | {STATUS} |

### 3.2 Summary

Brief description of what was accomplished.

### 3.3 Tasks

| # | Task | Status | Notes |
|---|------|--------|-------|
| 1 | {TASK_1} | {STATUS} | {NOTES} |
| 2 | {TASK_2} | {STATUS} | {NOTES} |

### 3.4 Decisions

| Decision | Rationale |
|----------|-----------|
| {DECISION_1} | {RATIONALE} |

### 3.5 Issues

| Issue | Resolution |
|-------|------------|
| {ISSUE_1} | {RESOLUTION} |

### 3.6 Files Changed

| File | Action |
|------|--------|
| {FILE_PATH} | {ACTION} |

### 3.7 Next Steps

- [ ] {NEXT_STEP_1}
- [ ] {NEXT_STEP_2}

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
- **Zorunlu:** tüm `{...}` placeholder'ları (`{SESSION_ID}`, `{DATE}`, `{START_TIME}`, `{END_TIME}`, `{DURATION}`, `{STATUS}`, `{TASK_1}`, `{TASK_2}`, `{NOTES}`, `{DECISION_1}`, `{RATIONALE}`, `{ISSUE_1}`, `{RESOLUTION}`, `{FILE_PATH}`, `{ACTION}`, `{NEXT_STEP_1}`, `{NEXT_STEP_2}`) doldurulmadan oturum kapatılamaz.
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
3. **{PLACEHOLDER} DOLDUR:** §3.1 Session Info (`{SESSION_ID}`, `{DATE}`, `{START_TIME}`, `{END_TIME}`, `{DURATION}`, `{STATUS}`); §3.2 Summary; §3.3 Tasks; §3.4 Decisions; §3.5 Issues; §3.6 Files Changed; §3.7 Next Steps.
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

**Template Version:** 2.0.0
**Created:** 2026-08-23
**Last Updated:** 2026-09-23
