---
title: "CoreMusic — .ai/architecture/07-security Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/07-security"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/07-security — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
Güvenlik mimarisi: katmanlı şifreleme, JWT, cihaz kimlik doğrulama, driver imzalama, deep logging, middleware güvenliği.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Klasör dizini |
| `encryption.md`, `encryption-layers.md` | Şifreleme katmanları (AES-256-GCM) |
| `jwt-authentication.md` | JWT stratejisi |
| `device-auth.md` | Cihaz kimliği |
| `driver-signing.md` | Sürücü imzalama |
| `electronics-security.md` | Donanım güvenliği (ADR-061-064) |
| `deep-logging-system.md`, `deep-logging-implementation-plan.md` | Log sistemi + plan |
| `middleware-security.md` | Middleware güvenlik standardı |
| `api/api_security_master.md` | API güvenlik master |
| `security/csrf-protection.md`, `security/owasp-compliance.md` | CSRF (ADR-010) + OWASP |

## 3. Agent Kuralları
1. Güvenlik değişikliği → security-audit workflow tetikleme (`.workflows/security-audit.md`)
2. Secret/anahtar örnekleri dokümante edilmez (Guardrail #13-15)
3. Kod karşılığı `shared/src/Middleware` + `shared/src/Security` ile senkron

## 5. İlgili Kaynaklar
[[../../l1-security/AGENTS.md]] · [[../../../decisions/accepted/ADR-010-csrf-protection-strategy.md]] · [[../../../decisions/accepted/ADR-022-database-hardened-security.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
