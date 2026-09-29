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

> ⚠️ VERIFICATION REQUIRED — dosyada Required Inputs kaynağı (Tier Sizes / Variants / States / Requirements) yok

### ASCII Reference

```
LEFT (60%): Genre Tabs → Circular Card Grid (3×3, 140px circles)
RIGHT (40%): Artist Detail (circular photo + bio + stats)
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

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

> ⚠️ VERIFICATION REQUIRED — dosyada Validation kaynağı (Accessibility kontrol listesi) yok

---

*04 Artists Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
