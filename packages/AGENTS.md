---
title: "CoreMusic — packages Agent Talimatları"
type: agent-registry
folder: "packages"
category: shared
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# packages — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç

Composer paket geliştirme kökü (ADR-085: Shared Library Hybrid). Tek gerçek paket `shared/`'dir; gelecekte eklenen paketler bu klasörde konumlanır.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `shared/` | CoreMusic shared PHP paketi (Contract, DTO, Enum, Event, Security, Validation, ValueObject, Helper) — detay: [[./shared/AGENTS.md]] |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Backend Architect | Paket sınırı kararları; ADR-085 namespace/PSR-4 kuralları |
| QA Engineer | Paket seviyesi phpunit.xml yapılandırması |

## 4. Kurallar

### Zorunlu
1. Yeni paket → ADR-085'e uyumlu namespace + kendi composer.json + kendi phpunit.xml
2. Paketler arası bağımlılık yönlü olmalı; döngü yasak

### Yasak
1. `packages/*/vendor/` commit etmek
2. Paket içinde domain'e özel (subdomain'e bağlı) kod yazmak — o kod ilgili subdomain klasörüne gider
3. Onaysız yeni paket klasörü açmak

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| ADR-085 | [[../.ai/decisions/accepted/ADR-085-modular-composer-packages.md]] |
| Paket içeriği | [[./shared/AGENTS.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
