---
title: "ui-analyzer — Changelog"
type: reference
version: 4.0.0
updated: 2026-10-21
---

# ui-analyzer — Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-10-21 | **Komple reset (onaylı yeniden yazım).** SKILL.md 500-2000 satır hedefine göre yeniden yazıldı (N3 revizyonu). `references/` yeniden yapılandırıldı: `sources.md` (zorunlu Truth Mode kaynak defteri, 4 web kaynağı 2026-10-21'de doğrulandı), `deep-dive.md` (metodoloji + mockup protokolü + font/renk/grid/breakpoint ölçümü), `wcag-criteria.md` (WCAG 2.2 AA doğrulanmış kriter matrisi), `token-itcss-audit.md` (ITCSS 11 katman + BEM + `--cm-*` denetimi + tarama komutları), `changelog.md` (bu dosya) = 5 dosya. Eski 6 v2/v3 referansı silindi ve içerikleri yeni dosyalara taşındı: `analysis-methodology` → `deep-dive.md` · `mockup-karsilastirma-protokolu` → `deep-dive.md` §5 · `design-system-criteria` + `token-sapma-kontrolu` → `token-itcss-audit.md` · `scoring-rubric` + `rapor-formati` → SKILL.md §11-§12 (rapor şablonu ana dosyaya alındı). `examples/` 3 dosya yeniden yazıldı; hepsi FACT/INFERENCE/UNKNOWN işaretlemesi kazandı (N5). WCAG içeriği kopyalanmadı → cross-link korundu. READ-ONLY + "kod üretemez" kimliği, tetikleyiciler ve puanlama şeması korundu. `CLAUDE.md` sidecar yok (2026-10-07 kuralı). Not: 3.0.0'de "eski v2" olarak işaretlenen v2 dosyaları artık diskte YOK (bu resette silindi, içerikleri taşındı) |
| 3.0.0 | 2026-10-07 | **Claude Skill v3.0** multi-md yapısına rebuild: frontmatter sadeleştirildi (root: name/description/license/metadata; `mode[]`→`metadata.tags`, `triggers`→description, `reference{}`→§ Otorite, `title/type/authority`→dropped); `references/` 4 dosya (analysis-methodology, design-system-criteria, scoring-rubric, changelog) + `examples/` 2 örnek (home-panel-audit, mockup-vs-code-mismatch); puanlama şeması (A-D) eklendi; WCAG içeriği kopyalanmadı → `ui-code-generator/references/wcag-2.2-checklist.md` cross-link; stale `CLAUDE.md` sidecar silindi; READ-ONLY + "kod üretimi yasak" kimliği korundu |
| 2.0.0 | 2026-10-07 | claude-skill-v3.0 yapısına taşındı: `references/` (3 dosya) + `examples/` (1 örnek) eklendi; çekirdek analiz akışı korundu |
| 1.2 | 2026-09-29 | Faz2 içerik kalite denetimi — `.ai/ADR/` → `.ai/.decisions/`; cross-skill ref `.opencode` → `.claude/skills/ui-code-generator` (diskte `.opencode` altında yok) |
| 1.1 | 2026-08-15 | Standardized YAML frontmatter; triggers frontmatter'e eklendi |
| 1.0 | 2026-09-06 | İlk yayın (ui-analyzer skill) |

**Değişiklik kuralı:** Her sürüm satırında (a) ne eklendi/silindi, (b) hangi dosyaya
taşındı, (c) hangi kural değişti açıkça yazılır. Sürüm bump → `SKILL.md` frontmatter
`metadata.version` + footer ile senkron (N9).

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
