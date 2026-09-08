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

- Frontend kodlama (HTML, CSS, Vanilla JS)
- CSS mimarisi (ITCSS 9-layer + BEM)
- `.ai/ui-design/` 18 PNG Mockup ve C01–C16 bileşen sistemine tam uyum
- Responsive tasarım (1024×600 kanonik temel referans)
- Accessibility (WCAG 2.2 AA, min 48px touch target)

## 3. Dosya Erişimi

| Erişim | Kapsam |
|--------|--------|
| Okuma/Yazma | `*.js`, `*.css`, `*.html`, `*.svg` |
| Okuma (Zorunlu SSOT) | `.ai/ui-design/**`, `.ai/.png/**` |

## 4. Zorunlu Kurallar

- **Mockup Before Frontend (Guardrail #11):** Herhangi bir UI/CSS/JS kodu yazmadan önce ilgili ekranın `.ai/ui-design/00-mockup-index.md` ve `.ai/.png/` mockup'ını okumak ZORUNLU.
- **Kanonik C01–C16 Uyumu:** Tüm bileşenler `.ai/ui-design/01-component-inventory.md`'deki BEM sınıfları ve ölçümleriyle birebir kodlanmalı.
- **Kanonik Boyut Referansı:** 1024×600 piksel referansı (Header: 60px y:0-60, İçerik: 450px y:60-510, Footer: 90px y:510-600).
- `innerHTML` yasak (DOMParser + TrustedTypes)
- Framework yasak (ADR-001)
- `var` yasak, `eval()` yasak
- BEM format zorunlu

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Mockup İndeksi (SSOT) | [[ui-design/00-mockup-index]] (18 PNG Mockup) |
| Bileşen Envanteri | [[ui-design/01-component-inventory]] (C01–C16) |
| Uygulama Planı | [[ui-design/02-implementation-plan]] (15-step CSS) |
| ASCII Art Wireframes | [[ui-design/screens/00-ascii-art-index]] |
| Design Tokens Master | [[ui-design/tokens/design-tokens-master]] |
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
