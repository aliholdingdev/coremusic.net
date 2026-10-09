---
type: architecture
category: layer
title: "K011 — UX"
date: 2026-10-09
status: active
version: 2.0.0
authority: SSOT
---

# K011 — UX

## §1 Kimlik
- Katman: K011 · Alan: **A3** (K10-K11 — Presentation/Gösterim).
- Kapsam: sunum katmanı — vanilla JS SPA + ITCSS token sistemi, widget grid, ViewModes.

## §2 Sorumluluk
1. Vanilla JS ES6+; framework YASAK (ADR-001 frozen; R-001/003/004 reddedilenler).
2. ITCSS 11 katman + `--cm-*` token tek kaynak; sabit değer yalnız `01_Abstracts` (kök AGENTS.md §10).
3. ViewModes tek yükleme yolu `<link id="cm-view-css">` (ADR-093).
4. Widget grid kanonik: 1024 → satır 2×2+1×5+1×5 = 12 slot · 1920 → 4×4+1×8+1×8 = 20 slot (kullanıcı kuralı, bağlayıcı — AGENTS §13.9.1).
5. WCAG 2.2 AA ve ui-design C01-C16 uyumu (AGENTS §16).
6. MockupBefore Frontend: `.ai/ui-design/` görseli okunmadan kod yazılmaz (AGENTS §13).

## §3 Bağlantılar (Dependency Rule)
- **Alt:** K009 API (JSON sözleşmesi) · K014 ağ (subdomain/statik varlık) — kod importu YASAK, yalnız HTTP/API (plan §3).
- **Üst:** tarayıcı (istemci — en üst katman; bu katmanın üstünde K katmanı yoktur).

## §4 ADR Bağlantıları
ADR-001 (vanilla JS+ITCSS) · ADR-093 (ViewModes tek yükleme yolu) · ADR-018 (footer player) · ADR-044 (theme engine) ·
ADR-045/046 (view mode, cross-view state) · ADR-048 (View Transition) · ADR-004 (multi-domain SPA).

## §5 Durum
**IMPLEMENTED** — kanıt (2026-10-09): `assets.coremusic.net/Css/01_Abstracts/*-tokens.css` (`a-widget-grid-tokens.css` dahil) · `assets.coremusic.net/Css/09_ViewModes/v-{home,pro,studio,car}.css` · `.ai/ui-design/` 21 ekran (AGENTS §13.8).

## §6 Risk / Not
- `tokens-3840.json` / `tokens-tv.json` boş → 3840/TV katmanları PLANNED (AGENTS §24.3 veri bütünlüğü notu).
- Widget grid kuralına aykırı sayım/render revize edilir, kural değil (AGENTS §13.9.1).
