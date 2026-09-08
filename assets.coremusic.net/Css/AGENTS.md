---
title: "CoreMusic — assets.coremusic.net/Css Agent Talimatları"
type: agent-registry
folder: "assets.coremusic.net/Css"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# assets.coremusic.net/Css — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç
ITCSS katman yapısının kökü: giriş dosyaları (`main.css`, `auth-bundled.css`) + 10 katman klasörü (01_Abstracts → 11_OOAuth) — ADR-001 uygulaması.

## 2. İçerik Envanteri
`main.css`, `auth-bundled.css` + katman klasörleri: 01_Abstracts (10 token), 02_Base, 03_Layout, 04_Components, 05_Pages, 06_Utilities, 07_Vendors, 08_Devices (13), 09_ViewModes (4), 11_OAuth

## 3. Kurallar
1. `main.css` import sırası ITCSS katman sırasına uymak zorundadır — değişiklik `l3-presentation/itcss-architecture.md` ile doğrulanır
2. Katman dışına CSS yazılmaz; token'lar yalnız 01_Abstracts'ta
3. BEM adlandırma zorunlu

## 5. İlgili Kaynaklar
[[../../.ai/architecture/l3-presentation/itcss-architecture.md]] · [[../../.ai/ui-design/tokens/design-tokens-master.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
