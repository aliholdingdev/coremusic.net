# agent-orchestrator — Sürüm Geçmişi

> Kök `changelog:` alanı v3.0 formatında yasaktır (skill-maker N10) — geçmiş burada tutulur.

| Sürüm | Tarih | Değişiklik |
|-------|-------|-----------|
| 3.0.0 (format) | 2026-10-07 | claude-skill-v3 frontmatter'a yükseltildi (previous-version: 4.1); `references/workflow-dispatch.md` (orchestration merge) + `references/changelog.md` eklendi; anti-overthink tek satıra indirildi; CLAUDE.md sidecar kaldırıldı |
| 4.1 | 2026-09-29 | Faz2 içerik kalite denetimi: ölü referans ".ai/ADR/" → ".ai/.decisions/" (disk kanıtı), project_structure disk uyarısı eklendi |
| 4.0 | 2026-08-15 | Complete rewrite from scratch; standardize YAML frontmatter; 19 structured sections; routing table (11 agents); task analysis pipeline; handover protocol; validation pipeline; risk classification; security governance; hallucination prevention |

> Not: metadata.version = 3.0.0 format sürümüdür; içerik soyu `previous-version: 4.1`
> metadata alanında korunur.