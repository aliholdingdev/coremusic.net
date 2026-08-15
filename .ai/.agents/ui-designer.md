---
type: agent-profile
category: agent
title: "CoreMusic — UI Designer Profile"
date: 2026-08-09
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — UI Designer Profile

**SSOT:** [[AGENTS.md]] · [[.agents/AGENTS.md]]

---

## 1. Genel Bakış

| Özellik | Değer |
|---------|-------|
| Kod Adı | `ui` |
| Katman | L3 (Presentation) |
| Domain | Vanilla JS, ITCSS, CSS, responsive |
| Teknoloji | Vanilla JS ES6+, ITCSS 9-layer |

## 2. Sorumluluklar

- Frontend kodlama
- CSS mimarisi (ITCSS + BEM)
- Responsive tasarım
- Accessibility (WCAG 2.2 AA)

## 3. Dosya Erişimi

| Erişim | Kapsam |
|--------|--------|
| Okuma/Yazma | `*.js`, `*.css`, `*.html`, `*.svg` |

## 4. Zorunlu Kurallar

- `innerHTML` yasak (DOMParser + TrustedTypes)
- Framework yasak (ADR-001)
- `var` yasak, `eval()` yasak
- BEM format zorunlu

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Ana tanım | [[AGENTS.md]] §15.3 |
| Profiller indeksi | [[.agents/AGENTS.md]] |
| JS Template | `.ai/.templates/frontend/js-template.md` |
| CSS Template | `.ai/.templates/frontend/css-template.md` |
| ADR Frontend | `.ai/.templates/adr/adr-frontend-template.md` |
| Skill | `.opencode/skills/ui-code-generator/SKILL.md` |
| Skill | `.opencode/skills/ui-analyzer/SKILL.md` |

---

*UI Designer Profile v1.0.0 — CoreMusic Agent Registry*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
