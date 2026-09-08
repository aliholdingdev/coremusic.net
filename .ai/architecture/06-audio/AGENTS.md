---
title: "CoreMusic — .ai/architecture/06-audio Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/06-audio"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/06-audio — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
Audio pipeline ve 6 çekirdek servis tanımı: media, audio, control, device, network-audio, ai servisleri.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Klasör dizini |
| `audio-pipeline.md` | Ses işleme hattı |
| `audio-platform-decision.md` | Platform kararı (ADR-017 bağlamı) |
| `coremusic-media-service.md` | Media servisi |
| `coremusic-audio-service.md` | Audio servisi |
| `coremusic-control-service.md` | Control servisi |
| `coremusic-device-service.md` | Device servisi |
| `coremusic-network-audio-service.md` | Network audio servisi |
| `coremusic-ai-service.md` | AI audio servisi (ADR-030 bağlamı) |
| `ai-auto-download.md` | AI otomatik indirme |

## 3. Agent Kuralları
1. Servis tanımı değişikliği → `04-decisions/service-architecture/` + dependency-graph senkronu
2. DSP detayları `[[../../electronic/dsp/]]`'te; buraya kopyalanmaz

## 5. İlgili Kaynaklar
[[../../electronic/AGENTS.md]] · [[../../../decisions/accepted/ADR-017-dsp-hardware-mode.md]] · [[../../../decisions/accepted/ADR-025-professional-eq-system.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
