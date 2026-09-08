---
title: "CoreMusic — .ai/decisions/accepted Agent Talimatları"
type: agent-registry
folder: ".ai/decisions/accepted"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .ai/decisions/accepted — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[../index.md]]

## 1. Amaç

Onaylanmış 66 ADR (ADR-001 → ADR-087). Frozen bölge (001-037) değiştirilemez; active bölge (038-087) yeni ADR ile güncellenebilir.

## 2. İçerik Envanteri (Özet)

| Bölge | ADR'ler | Konu |
|-------|---------|------|
| Frozen 001-005 | Vanilla JS+ITCSS, PDO, Multi-DB 9, Multi-Domain SPA, Ultrathink | Temel mimari |
| Frozen 006-015 | Performans, Cache NS, BypassAuth, CleanURL, CSRF, Session, CSP, RateLimit, Migration, EnvParser | Platform |
| Frozen 016-025 | URL Norm, DSP HW, Footer Player, Neva Player, API Security, SPA Router, DB Security, Persona Test, Modular Docs, EQ 31-band | Routing/Audio/Security |
| Frozen 026-037 | Download Service, Dual-Mode Storage, Anti-Ban, Listening Rooms, AI Strategy, PWA/Flutter, IPC, SQL Norm, Credential Vault, System Prompt, Multi-Project Prompt, WirelessConnect | Servisler/AI |
| Active 038-064 | 8.1 Sound Card, 7-Service, DB Authority 18-BCNF, Norm Suppl., Vault Restructuring, Auth Consolidation, Theme Engine, View Mode, Cross-View State, Startup Prompt, DB Sync, Electronics×4 | Güncel kararlar |
| Active 072-087 | DB şemaları (Social/Podcast/Radio/AI/Video/Studio/CMS/i18n), SPA Router, API Gateway, Composer Packages, Event-Driven, Master Plan | Şema + plan |

## 3. Agent Kuralları

### Zorunlu
1. Dosya adı formatı: `ADR-<num>-<slug>.md`
2. Yeni ADR → `[[../index.md]]` + `[[../../brain.md]]` + `[[../../log.md]]` üçlü güncelleme

### Yasak
1. Frozen dosyayı düzenlemek
2. ADR silmek (süpersede mekanizması kullanılır)

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Dizin | [[../index.md]] |
| Şablon | [[../../.templates/adr/adr-template.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
