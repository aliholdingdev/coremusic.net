---
title: "CoreMusic — Session Init Workflow"
type: workflow
date: 2026-09-20
status: active
version: 1.0.0
---

# Session Init Workflow

## Aşama 1: Boot Protokolü (Max 36s)

| # | Dosya | Amaç | Max Süre |
|---|-------|------|----------|
| 1 | `.ai/CLAUDE.md` | AI anayasası | 3s |
| 2 | `.ai/AGENTS.md` | Agent sınırları | 3s |
| 3 | `.ai/WORKFLOW.md` | Süreçler | 3s |
| 4 | `.ai/brain.md` | Mimari kararlar | 3s |
| 5 | `.ai/index.md` | Master katalog | 3s |
| 6 | `.ai/keys.md` | Keyword haritası | 2s |
| 7 | `.ai/MEMORY.md` | Session hafızası | 2s |
| 8 | `.ai/log.md` | Audit trail | 2s |
| 9 | `.ai/engine.md` | Orkestrasyon | 3s |
| 10 | `.ai/ROLE.md` | Rol tanımı | 3s |
| 11 | `.ai/ULTRA-THINKING.md` | Düşünme protokolü | 2s |
| 12 | `.ai/glossary.md` | Terim sözlüğü | 2s |
| 13 | `.ai/PROJECTS.md` | Proje tanımı | 3s |

## Aşama 2: Prompt Entegrasyonu (Max 14s)

| # | Prompt | Dosya | Max Süre |
|---|--------|-------|----------|
| 1 | prompt0 (Genel Ana) | `archives/prompt0-genel-ana-prompt-2026-09-01` | 5s |
| 2 | prompt1 (SPA Router) | `archives/prompt1-spa-router-2026-09-01` | 3s |
| 3 | prompt2 (Auth) | `archives/prompt2-auth-2026-09-01` | 3s |
| 4 | prompt3 (API) | `archives/prompt3-api-2026-09-01` | 3s |

## Aşama 3: Session Vault Sync (Max 10s)

| # | Soru | Kaynak |
|---|------|--------|
| 1 | Son session'dan bu yana ne değişti? | git log, log.md |
| 2 | Yeni ADR var mı? | decisions/accepted/ |
| 3 | Kod değişikliği oldu mu? | git diff |
| 4 | Vault'ta eski bilgi var mı? | VERIFICATION REQUIRED |
| 5 | Skills durumu nedir? | .opencode/skills/ |

## Aşama 4: Session Başlangıç Kaydı

```markdown
## Session {{SESSION_ID}}
- Tarih: {{DATE}}
- Agent: {{AGENT}}
- Model: {{MODEL}}
- Görev: {{TASK}}
```

---

*Session Init Workflow v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
