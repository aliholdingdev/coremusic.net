---
title: "ui-analyzer — Changelog"
type: reference
version: 3.0.0
updated: 2026-10-07
---

# ui-analyzer — Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 3.0.0 | 2026-10-07 | **Claude Skill v3.0** multi-md yapısına rebuild: frontmatter sadeleştirildi (root: name/description/license/metadata; `mode[]`→`metadata.tags`, `triggers`→description, `reference{}`→§8 Otorite, `title/type/authority`→dropped); `references/` 4 yeni dosya (analysis-methodology, design-system-criteria, scoring-rubric, changelog) + `examples/` 2 yeni örnek (home-panel-audit, mockup-vs-code-mismatch); puanlama şeması (A-D) eklendi; WCAG içeriği kopyalanmadı → `ui-code-generator/references/wcag-2.2-checklist.md` cross-link; stale `CLAUDE.md` sidecar silindi; READ-ONLY + "kod üretimi yasak" kimliği korundu. Not: v2 reference/example dosyaları silinmedi (onay gerektirir) — SKILL.md §4-5'te "eski v2" olarak işaretli ve içerikleri yeni dosyalara taşındı |
| 2.0.0 | 2026-10-07 | claude-skill-v3.0 yapısına taşındı: references/ (3 dosya) + examples/ (1 örnek) eklendi; çekirdek analiz akışı korundu |
| 1.2 | 2026-09-29 | Faz2 içerik kalite denetimi — `.ai/ADR/` → `.ai/.decisions/`; cross-skill ref `.opencode` → `.claude/skills/ui-code-generator` (diskte `.opencode` altında yok) |
| 1.1 | 2026-08-15 | Standardized YAML frontmatter; triggers frontmatter'e eklendi |
| 1.0 | 2026-09-06 | İlk yayın (ui-analyzer skill) |