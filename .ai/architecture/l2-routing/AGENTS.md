---
title: "CoreMusic — .ai/architecture/l2-routing Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/l2-routing"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# l2-routing — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
L2 yönlendirme katmanı: SPA router (ADR-021/083), guard pipeline, middleware pipeline, subdomain yönlendirme, URL normalizasyon (ADR-016/009).

## 2. İçerik Envanteri
`index.md` + `spa-router.md` + `js-router.md` + `guard-pipeline.md` + `middleware-pipeline.md` + `subdomain-routing.md` + `url-normalization.md` + `route-config.md` + `html-shell-renderer.md` + `service-discovery.md`

## 3. Agent Kuralları
1. SPA Router sözleşmesi immutable (ADR-021); değişiklik = yeni ADR
2. Kod karşılığı: `shared/src/PageRouter/` + `assets.coremusic.net/js/router/`
3. HTML shell renderer → PHP tarafı ile JS router devri noktası

## 5. İlgili Kaynaklar
[[../../../shared/src/PageRouter/]] · [[../../../assets.coremusic.net/AGENTS.md]] · [[../../../decisions/accepted/ADR-083-spa-router.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
