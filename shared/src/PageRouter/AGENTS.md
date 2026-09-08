---
title: "CoreMusic - C:\www\coremusic.net\shared\src\PageRouter Agent Talimatlari"
type: agent-registry
folder: "C:\www\coremusic.net\shared\src\PageRouter"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team . Human Mode . Truth Mode
---

# PageRouter - AGENTS.md

**Zorunlu Baglantilar:** [[../AGENTS.md]] . [[../CLAUDE.md]] . [[./CLAUDE.md]]

## 1. Amac
SPA sayfa router cekirdegi (ADR-083/021)

## 2. Icerik Envanteri
`AuthGuard.php, AuthUrlBuilder.php, ErrorHandler.php, HtmlShellRenderer.php, PageRouter.php, PageRouterHelper.php, PageRouterKernel.php, RequestNormalizer.php`

Not: HtmlShellRenderer, device asset ciktilarini `CoreMusic\Device\DeviceRenderer` (SSOT) uzerinden uretir — ID'li CSS link'leri, main[data-tier], loader data-*.

## 3. Kurallar
1. Ust klasor talimatlarina ([[../AGENTS.md]]) ek olarak gecerlidir; celsiyorsa ust kazanir
2. Yeni dosya ilgili vault sablonundan turetilir (Template Mandatory)
3. ADR ile celisen degisiklik yapilmaz; once ADR

## 5. Ilgili Kaynaklar
[[../AGENTS.md]] . [[../../.ai/AGENTS.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
