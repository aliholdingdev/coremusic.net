---
title: "CoreMusic — .ai/architecture/l3-presentation Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/l3-presentation"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# l3-presentation — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
L3 presentation katmanı: ITCSS mimarisi, JS modül yapısı, responsive/device CSS, dark-light tema.

## 2. İçerik Envanteri
`index.md` + `itcss-architecture.md` + `js-module-architecture.md` + `device-css.md` + `device-breakpoint-guide.md` + `responsive-frontend-architecture.md` + `scale-router-css-frontend-guide.md` + `dark-light-mode-architecture.md` + `components.md` + `ai-instructions.md` + 2 ek dosya

## 3. Agent Kuralları
1. UI kod değişikliği öncesi ilgili doküman + `.ai/ui-design/` mockup index okunur (Guardrail #11)
2. Kod karşılığı: `assets.coremusic.net/Css/` (ITCSS 9-layer) + `assets.coremusic.net/js/`
3. ITCSS katman sırası değiştirilemez

## 5. İlgili Kaynaklar
[[../../../assets.coremusic.net/AGENTS.md]] · [[../../../.ai/ui-design/00-mockup-index.md]] · [[../../../decisions/accepted/ADR-001-vanilla-js-itcss.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
