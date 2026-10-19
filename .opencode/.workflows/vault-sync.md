---
title: "CoreMusic — Vault Sync Workflow"
type: workflow
date: 2026-09-20
status: active
version: 1.0.0
---

# Vault Sync Workflow

## Başlangıç (5 Soru)

| # | Soru | Kaynak |
|---|------|--------|
| 1 | Son session'dan bu yana ne değişti? | git log, log.md |
| 2 | Yeni ADR var mı? | decisions/accepted/ |
| 3 | Kod değişikliği oldu mu? | git diff |
| 4 | Vault'ta eski bilgi var mı? | VERIFICATION REQUIRED |
| 5 | Skills durumu nedir? | .opencode/skills/ |

## Bitiş (6 Adım)

| # | Adım | Kontrol |
|---|------|---------|
| 1 | Değişiklikleri vault'a yaz (in-place) | Dosya boyutu |
| 2 | `log.md`'ye timestamp ekle | Format |
| 3 | MEMORY.md session state güncelle | Session index |
| 4 | Wiki-link'leri doğrula | Regex |
| 5 | Hallüsinasyon sweep | VERIFICATION REQUIRED |
| 6 | Root MD'leri güncelle | Cross-reference |

## Root MD Güncelleme Sırası

```
Session Sonunda:
  MEMORY.md → log.md → brain.md → index.md → keys.md → engine.md →
  AGENTS.md → WORKFLOW.md → CLAUDE.md → ROLE.md → ULTRA-THINKING.md → glossary.md
```

---

*Vault Sync Workflow v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
