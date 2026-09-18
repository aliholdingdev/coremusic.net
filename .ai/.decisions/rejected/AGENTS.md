---
title: "CoreMusic — .ai/decisions/rejected Agent Talimatları"
type: agent-registry
folder: ".ai/decisions/rejected"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .ai/decisions/rejected — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç

Reddedilen 12 mimari öneri (R-001 → R-012). Aynı önerinin tekrar gündeme gelmesini önleyen karşıt kayıt deposu.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Red dizini + nedenler |
| `R-001` → `R-012` | Redux, MongoDB, jQuery, Webpack, REST-Only, Eloquent, Firebase Auth, MyISAM, Single-DB, Node Fullstack, GraphQL, Microservices |

## 3. Agent Kuralları

### Zorunlu
1. Red nedeni + reddeden ADR referansı her dosyada bulunur; yeni red dosyası bu formata uyar
2. Yeni red → `index.md` güncellemesi

### Yasak
1. Red nedenini silerek dosyayı yeniden canlandırmak (yeni ADR önerisi olarak gitmelidir)
2. `accepted/` ile numara çakışması

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Red dizini | [[./index.md]] |
| Kabul bölgesi | [[../accepted/AGENTS.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
