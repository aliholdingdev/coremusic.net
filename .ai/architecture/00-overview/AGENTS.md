---
title: "CoreMusic — .ai/architecture/00-overview Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/00-overview"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/00-overview — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]]

## 1. Amaç

Mimari üst görünüm: sistem master dokümanı, servisler arası bağımlılık grafiği ve başlangıç stratejisi.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `architecture-master.md` | Genel mimari master dokümanı |
| `dependency-graph.md` | Servis/paket bağımlılık grafiği |
| `startup-strategy.md` | Başlangıç (boot) stratejisi |

## 3. Agent Kuralları

### Zorunlu
1. Yeni servis eklenirken dependency-graph güncellenir
2. Startup değişikliği → RuntimeBootstrap koduyla senkron (shared/src/Bootstrap)

### Yasak
1. Master dokümanda secret/endpoint token bilgisi
2. Bağımlılık eklemeyi graph güncellemeden bırakmak

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Deployment | [[../02-deployment/index.md]] |
| Ecosystem | [[../../ecosystem/7-service-integration.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
