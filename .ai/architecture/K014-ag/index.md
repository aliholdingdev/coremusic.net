---
type: architecture
category: layer
title: "K014 — Ağ"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K014 — Ağ

## §1 Kimlik
- Katman: K014 · Alan: **A4** (K12-K15).
- Kapsam: ağ/topoloji — subdomain düzeni, başlık dağıtımı (CSP), URL/routing kenarı.

## §2 Sorumluluk
1. 5 subdomain topolojisi: `home.` · `auth.` · `api.` · `media.` · `assets.coremusic.net` (plan §3; disk kanıtı kök dizin).
2. Subdomain'ler birbirine kod IMPORT etmez; yalnız `shared/` + API sözleşmesi (plan §3 — Parnas'72).
3. CSP nonce/strict-dynamic başlığının uçlarda dağıtımı (ADR-012).
4. Clean URL redirect + URL normalization (ADR-009/016), SPA router sözleşmesi (ADR-021).
5. Auth subdomain konsolidasyonu + login redirect session bridge (ADR-043/047).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K007 (edge middleware) → K006 → K000.
- **Üst (çağıran):** K011 UX (istemci) · K009 API (uçlar).

## §4 ADR Bağlantıları
ADR-004 (Multi-Domain SPA) · ADR-009 (Clean URL) · ADR-012 (CSP nonce) · ADR-016 (URL Normalization) ·
ADR-021 (SPA Router Contract) · ADR-043 (Auth Subdomain Consolidation) · ADR-047 (Login Redirect & Session Bridge).

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): kök dizin `api.coremusic.net/` `auth.coremusic.net/` `home.coremusic.net/` `media.coremusic.net/` `assets.coremusic.net/` · subdomain composer.json ×4 (api/auth/home/media).

## §6 Risk / Not
- DNS/CDN/yük dengeleme yapılandırması diskte yok → **UNKNOWN**.
- `assets.coremusic.net/` composer.json'sız (yalnız statik varlık) — kasıt doğrulanmadı.
