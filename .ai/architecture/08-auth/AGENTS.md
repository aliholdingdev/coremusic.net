---
title: "CoreMusic — .ai/architecture/08-auth Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/08-auth"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/08-auth — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
Auth domain mimarisi: domain tanımı, uygulama akışı, API sözleşmesi, cross-domain köprü, embedded auth, medya güvenliği, altyapı.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Klasör dizini |
| `auth-domain.md` | Auth domain sınırları (ADR-043) |
| `auth-flow.md` | Login/register/redirect akışları |
| `auth-application.md` | Uygulama katmanı |
| `auth-api.md` | Auth API sözleşmesi |
| `auth-cross-domain.md` | Çapraz domain köprüsü (HomeAuthBridge) |
| `auth-embedded.md` | Gömülü (RPi5) auth |
| `auth-infrastructure.md` | Altyapı bileşenleri |
| `auth-media-security.md` | Medya erişim güvenliği |

## 3. Agent Kuralları
1. Auth kod değişikliği öncesi `auth-flow.md` okunur; çelişki → ADR
2. `auth.coremusic.net/include/` hexagonal katmanlarıyla birebir uyum korunur

## 5. İlgili Kaynaklar
[[../../../auth.coremusic.net/AGENTS.md]] · [[../../../decisions/accepted/ADR-043-auth-subdomain-consolidation.md]] · [[../../l1-security/auth.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
