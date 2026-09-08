---
title: "CoreMusic — .ai/ecosystem Agent Talimatları"
type: agent-registry
folder: ".ai/ecosystem"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/ecosystem — AGENTS.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

## 1. Amaç
7 servis platformu ekosistemi: entegrasyon, servis iletişimi, sağlık kontrolü, hata kurtarma, durum makineleri, panel entegrasyonu, ağ mimarisi.

## 2. İçerik Envanteri
7 dosya: `7-service-integration.md`, `service-communication.md`, `service-health-check.md`, `error-recovery.md`, `state-machines.md`, `panel-integration.md`, `network-architecture.md`

## 3. Kurallar
1. Servis ekleme → 7-service-integration + dependency-graph ikili güncelleme
2. Durum makinesi değişimi → ADR-086 (event-driven) uyumu

## 5. İlgili Kaynaklar
[[../architecture/06-audio/AGENTS.md]] · [[../architecture/00-overview/AGENTS.md]] · [[../../decisions/accepted/ADR-039-7-service-platform-architecture.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
