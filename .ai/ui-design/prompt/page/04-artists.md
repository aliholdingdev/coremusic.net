---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 04 Artists Page Prompt"
type: prompt
category: ui-design
page_id: "04"
route: "/artists"
layout: "05-desktop-3col"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 04 — Artists Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/artists` |
| **Layout** | 05-desktop-3col (60/40 split) |
| **Purpose** | Sanatçı listesi, circular cards |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C11 Genre Tabs | 1 | Content top |
| C09 Media Card (circular) | 9+ | Content left |
| C10 Detail Panel (artist) | 1 | Content right |

### Required Inputs

- `viewport`: 1920×1080 · 1x · Mouse+KB · sidebar 240px · 3 sütun (kaynak: 00-device-matrix.md L138)
- `tab`: padding 8px 16px · gap 4px · radius 8px · font 13px/500 · durumlar default/hover/active/disabled · Tier: all tiers same structure (kaynak: 02-component-inventory.md L85-L89)
- `card`: padding 16px · radius 16px · gap 16px · image 1:1 aspect · durumlar default/hover/active/loading/skeleton (kaynak: 02-component-inventory.md L55-L58)
- `avatar`: sm 24px · md 32px · lg 40px · xl 56px · radius 9999px · durumlar default/with-image/with-initials/online/offline (kaynak: 02-component-inventory.md L135-L138)

### ASCII Reference

```
LEFT (60%): Genre Tabs → Circular Card Grid (3×3, 140px circles)
RIGHT (40%): Artist Detail (circular photo + bio + stats)
```

### Prompt Template

```json
{
  "task": "Create artists page for CoreMusic",
  "page": "04-artists",
  "viewport": "1920x1080",
  "components": [
    "genre-tabs",
    "media-card",
    "detail-panel"
  ],
  "tokens": {
    "--cm-radius-md": "8px",
    "--cm-radius-xl": "16px",
    "--cm-radius-full": "9999px"
  }
}
```

### Expected Output

```html
<section class="content-left">
  <div class="genre-tabs" role="tablist"><!-- C11 --></div>
  <div class="media-card-grid">
    <a href="/artist/1" class="media-card media-card--circle">
      <div class="media-card__thumb">
        <img src="/artist.jpg" alt="Göksel" style="border-radius:50%">
      </div>
      <div class="media-card__info">
        <div class="media-card__title">Göksel</div>
        <div class="media-card__subtitle">12 albüm</div>
      </div>
    </a>
  </div>
</section>
```

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*04 Artists Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
