---
title: "engineering-rules-ssot — Eski Mühendislik Kuralları SSOT Hedefi (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-contracts
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# engineering-rules-ssot — Mühendislik Kuralları SSOT (stub)

**Durum:** `architecture/03-contracts/engineering-rules-ssot` **diskte YOK** (eski ağaç silindi).
Link: `.ai/index.md`.

## Bugünkü Karşılığı (gerçek kanıt)

| Kural katmanı | SSOT dosyası |
|---------------|--------------|
| Anayasa (16 Hard Guardrail, yasaklar) | `.ai/CLAUDE.md` §7 · §21 |
| Master kurallar (agent sınırları, routing) | `.ai/AGENTS.md` |
| `.ai/architecture/**` mimari kurallar | [[architecture/rules]] (bu ağacın kural SSOT'u) |
| Skill kuralı | Kök `CLAUDE.md` §Skill Usage Mandate |
| Doğrulama kuralları (zero-hallucination) | Skill: `C:\.claude\skills\truth-engine\SKILL.md` |

Çelişki sırası (`.ai/CLAUDE.md` §2.1): `.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` > `.ai/brain.md` > `.ai/index.md`.
