---
title: "CoreMusic — assets.coremusic.net/js Agent Talimatları"
type: agent-registry
folder: "assets.coremusic.net/js"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# assets.coremusic.net/js — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç
Vanilla JS katmanı kökü: giriş noktaları (main.js, device-loader, devices.config, device-layout-updater, oauth-manager) + alt modül klasörleri (auth, core, features, managers, router).

## 2. İçerik Envanteri
5 giriş dosyası + 6 modül klasörü (auth 2, core 4, features 5, managers 5, router 28+config 7)

## 3. Kurallar
1. Vanilla ES6+ (ADR-001): `var`, `eval`, `innerHTML` yasak
2. Modüller EventBus üzerinden haberleşir; global scope kirletilmez
3. `devices.config.js` ↔ `DeviceCssMap.php` senkron zorunlu

## 5. İlgili Kaynaklar
[[../../.ai/architecture/l3-presentation/js-module-architecture.md]] · [[../../.ai/architecture/l2-routing/AGENTS.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
