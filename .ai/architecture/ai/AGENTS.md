---
title: "CoreMusic — .ai/architecture/ai Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/ai"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/ai — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
AI mimarisi (ADR-030): agent sistemi, orkestratör, bilgi tabanı, hafıza sistemi, MCP entegrasyonu, electronics AI motoru.

## 2. İçerik Envanteri
`index.md` + 12 dosya: agent-system, ai-engine, ai-orchestrator, ai-workflow, ai-workflow-electronics, ai-electronics-engine, knowledge-base, memory-system, mcp-integration vb.

## 3. Agent Kuralları
1. AI kod karşılığı `shared/src/AI/` ile senkron (interface'ler Contracts'ta)
2. MCP entegrasyon değişikliği → güvenlik denetimi şart

## 5. İlgili Kaynaklar
[[../../../decisions/accepted/ADR-030-ai-strategy-core.md]] · [[../../../shared/src/AI/]] · [[../../../.opencode/skills/prompt-maker/SKILL.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
