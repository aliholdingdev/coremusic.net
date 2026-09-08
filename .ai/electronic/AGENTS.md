---
title: "CoreMusic — .ai/electronic Agent Talimatları"
type: agent-registry
folder: ".ai/electronic"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/electronic — AGENTS.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]] · [[./amplifier/AGENTS.md]]

## 1. Amaç
Electronics mimarisi (L6, ADR-061-064): genel overview, audio/device/driver/firmware/hardware mimarileri, development workflow.

## 2. İçerik Envanteri
15 kök dosya: `core-music-electronics-overview.md`, `audio-architecture.md`, `amplifier-architecture.md`, `device-architecture.md`, `device-ecosystem.md`, `driver-framework.md`, `dsp-engine-architecture.md`, `firmware-architecture.md`, `hardware-design.md`, `development-workflow.md` + 5 ek dosya
Alt klasörler: `amplifier/`, `drivers/`, `dsp/`, `firmware/`, `hardware/`

## 3. Kurallar
1. Donanım capability bilgisi doğrulanmadan yazılmaz (CLAUDE.md §8.1)
2. ADR-061-064 çelişkisi → ADR kazanır
3. Şema/PCB detayları alt klasörlerde; kökte üst mimari kalır

## 5. İlgili Kaynaklar
[[../architecture/l6-electronics.md]] · [[../architecture/06-audio/AGENTS.md]] · [[../../.templates/hardware/AGENTS.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
