---
title: "CoreMusic — .ai/architecture/04-decisions Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/04-decisions"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/04-decisions — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]]

## 1. Amaç
Karar yaşam döngüsü, karar logu, domain kararı ve guardrails dokümantasyonu. `.ai/decisions/` ADR deposunun süreç karşılığıdır.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `adr-lifecycle.md` | ADR yaşam döngüsü (draft→accepted→frozen/superseeded) |
| `decision-log.md` | Karar kronolojisi |
| `domain-decision.md` | Domain sınırları kararı |
| `guardrails.md` | Mimari koruma kuralları |
| `device-agent/` | Cihaz ajanı kararı |
| `os-adapters/` | OS adaptör kararı |
| `service-architecture/` | Servis mimarisi kararı |

## 3. Agent Kuralları
1. ADR numaralı kayıtlar buraya yazılmaz (tek konum: `[[../../decisions/accepted/]]`)
2. Guardrail değişikliği → `.ai/CLAUDE.md` §7 ile senkron

## 5. İlgili Kaynaklar
[[../../decisions/AGENTS.md]] · [[../../CLAUDE.md]] · [[../../.workflows/adr-creation.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
