---
title: "CoreMusic — .ai/architecture/l1-security Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/l1-security"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# l1-security — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
L1 güvenlik katmanı: auth, CSP, CSRF, middleware, session davranış standardı.

## 2. İçerik Envanteri
`index.md` + `auth.md` + `csp.md` + `csrf.md` + `middleware.md` + `session.md`

## 3. Agent Kuralları
1. Frozen güvenlik ADR'leri (008/010/011/012/013) bu katmandan değiştirilemez
2. Kod karşılığı: `shared/src/Middleware/` + `shared/src/Security/` + `shared/src/Session/`
3. CSP kuralı: nonce + strict-dynamic (ADR-012)

## 5. İlgili Kaynaklar
[[../07-security/AGENTS.md]] · [[../../../shared/src/Middleware/]] · [[../../../shared/src/Session/]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
