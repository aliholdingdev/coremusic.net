---
type: workflow
category: session-init
title: "Session Init Workflow"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Session Init Workflow

**Zorunlu Bağlantılar:** [[WORKFLOW.md]] · [[CLAUDE.md]] · [[AGENTS.md]]

## 1. Amaç

Her oturumun başlatılmasını sağlayan 10 adımlık boot protokolü.

## 2. Adımlar (10-Step Boot Protocol)

| # | Dosya | Amaç | Öncelik | Timeout |
|---|-------|------|---------|---------|
| 1 | `.ai/CLAUDE.md` | Kanonik AI talimatı | P0 | 3s |
| 2 | `.ai/AGENTS.md` | Agent kayıt defteri | P0 | 3s |
| 3 | `.ai/WORKFLOW.md` | Süreçler | P0 | 3s |
| 4 | `.ai/index.md` | Master katalog | P1 | 4s |
| 5 | `.ai/keys.md` | Anahtar kelime haritası | P1 | 3s |
| 6 | `.ai/AGENTS.md` | Agent yetkileri (tekrar) | P1 | 3s |
| 7 | `.ai/brain.md` | Mimari kararlar | P1 | 4s |
| 8 | `.ai/MEMORY.md` | Oturum hafızası | P1 | 3s |
| 9 | `.ai/log.md` | Aktivite günlüğü (son 20 satır) | P1 | 2s |
| 10 | `.claude/rules/*` | Tüm kurallar | P2 | 5s |

**Toplam boot süresi:** Max 25 saniye.

## 3. Boot Akışı

```text
Oturum Başlat
  → P0 Dosyaları Oku (CLAUDE, AGENTS, WORKFLOW)
    → P1 Dosyaları Oku (index, keys, brain, MEMORY, log)
      → P2 Dosyaları Oku (rules)
        → Session Vault Sync Başlat
```

## 4. Vault Sync (5 Soru)

| # | Soru |
|---|------|
| 1 | Son session'dan bu yana ne değişti? |
| 2 | Yeni ADR var mı? |
| 3 | Kod değişikliği oldu mu? |
| 4 | Vault'ta eski bilgi var mı? |
| 5 | Skills durumu nedir? |

## 5. Başarı Kriterleri

| Kriter | Değer |
|--------|-------|
| Boot Süresi | ≤25 saniye |
| Dosya Sayısı | 10 dosya |
| Vault Sync | Tamamlandı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
