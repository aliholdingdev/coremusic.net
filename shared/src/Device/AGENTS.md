---
title: "CoreMusic - C:\www\coremusic.net\shared\src\Device Agent Talimatlari"
type: agent-registry
folder: "C:\www\coremusic.net\shared\src\Device"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team . Human Mode . Truth Mode
---

# Device - AGENTS.md

**Zorunlu Baglantilar:** [[../AGENTS.md]] . [[../CLAUDE.md]] . [[./CLAUDE.md]]

## 1. Amac
Cihaz tespiti, yonetimi ve hybrid rendering'in sunucu tarafi ciktilari (server initial render + client hydrate sozlesmesi)

## 2. Icerik Envanteri
`DeviceCssMap.php` (CSS haritasi SSOT), `DeviceDetector.php` (tespit), `DeviceManager.php` (v2.0.0 per-request singleton), `DeviceRenderer.php` (v1.0.0 — ID'li CSS link'leri, data-tier, loader attribute'lari)

## 3. Kurallar
1. Ust klasor talimatlarina ([[../AGENTS.md]]) ek olarak gecerlidir; celsiyorsa ust kazanir
2. Yeni dosya ilgili vault sablonundan turetilir (Template Mandatory)
3. ADR ile celisen degisiklik yapilmaz; once ADR
4. DeviceRenderer link ID'leri client sozlesmesidir (cm-auth-bundled, cm-device-css, cm-view-css) — device-loader.js ile es zamanli degisir; DeviceCssMap ↔ devices.config.js senkronu bozulmaz

## 5. Ilgili Kaynaklar
[[../AGENTS.md]] . [[../../.ai/AGENTS.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
