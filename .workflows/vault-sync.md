---
type: workflow
category: vault-sync
title: "Vault Sync Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Vault Sync Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

`.ai/` vault'unun senkronize edilmesini ve bütünlüğünün korunmasını sağlayan iş akışı.

## 2. Adımlar

### 2.1 Başlangıç (5 Soru)

| # | Soru | Kontrol Yöntemi |
|---|------|-----------------|
| 1 | Son session'dan bu yana ne değişti? | `git log --since="last session"` |
| 2 | Yeni ADR var mı? | `decisions/accepted/` dizin taraması |
| 3 | Kod değişikliği oldu mu? | `git diff --name-only` |
| 4 | Vault'ta eski bilgi var mı? | `VERIFICATION REQUIRED` taraması |
| 5 | Skills durumu nedir? | `.opencode/skills/` kontrolü |

### 2.2 Bitiş (6 Adım)

| # | Adım | Kontrol | Süre |
|---|------|---------|------|
| 1 | Değişiklikleri vault'a yaz | Dosya boyutu | 5s |
| 2 | `log.md`'ye timestamp ekle | Format doğrulama | 2s |
| 3 | `MEMORY.md` session state güncelle | Session indeks | 3s |
| 4 | Wiki-link'leri doğrula | Regex pattern | 5s |
| 5 | MSA limit kontrolü (15 dosya) | Dosya sayacı | 2s |
| 6 | Hallüsinasyon sweep | `VERIFICATION REQUIRED` taraması | 3s |

**Toplam:** Max 20 saniye.

## 3. Wiki-Link Doğrulama

```regex
\[\[([^\]]+)\]\]
```

## 4. Hallüsinasyon Sweep

| Kontrol | Yöntem |
|---------|--------|
| `VERIFICATION REQUIRED` | grep taraması |
| Uydurma API/endpoint | Doğrulama |
| Yanlış dosya yolu | Cross-reference |

## 5. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| Vault Sync | Tamamlandı |
| Wiki-link'ler | Geçerli |
| Hallüsinasyon | Tespit edilmedi |
| MSA Limit | ≤15 dosya |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
