---
title: "CoreMusic — .ai/architecture/04-decisions/os-adapters Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/04-decisions/os-adapters"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# os-adapters — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]]

## 1. Amaç
OS adaptör katmanı kararı (`os-adapter-decision.md`): Windows/Linux/macOS platform farklarının soyutlanması.

## 2. İçerik Envanteri
`os-adapter-decision.md`

## 3. Kurallar
1. Adaptör arayüzü platforma özgü kodu dışarı sızmaz (L4-L5 sınırı)
2. Platform özgül davranış → 10-network veya l0 dokümanlarıyla uyum

## 5. İlgili Kaynaklar
[[../CLAUDE.md]] · [[../../l0-infrastructure/AGENTS.md]] · [[../../l4-domain.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
