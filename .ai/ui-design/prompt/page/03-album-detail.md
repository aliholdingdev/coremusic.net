---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 03 Album Detail Page Prompt"
type: prompt
category: ui-design
page_id: "03"
route: "/album/:id"
layout: "05-desktop-3col"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 03 — Album Detail Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/album/:id` |
| **Layout** | 05-desktop-3col (60/40 split) |
| **Purpose** | Albüm detayı, track list, info panel |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C13 Track List | 1 | Content left (60%) |
| C12 Star Rating | 1 | Below track list |
| C10 Detail Panel | 1 | Content right (40%) |
| C04 Primary Button | 1 | Detail panel |
| C05 Secondary Button | 1 | Detail panel |

### Required Inputs

> ⚠️ VERIFICATION REQUIRED — dosyada Required Inputs kaynağı (Tier Sizes / Variants / States / Requirements) yok

### ASCII Reference

```
LEFT (60%): Track List (C13) + Star Rating (C12)
RIGHT (40%): Detail Panel (C10) — Art + Title + Meta + Actions
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<main class="layout--desktop__content">
  <section class="content-left">
    <div class="track-list" role="list"><!-- C13 rows --></div>
    <div class="star-rating"><!-- C12 --></div>
  </section>
  <aside class="content-right">
    <div class="detail-panel detail-panel--album">
      <img class="detail-panel__art" src="/cover.jpg" alt="Album kapağı">
      <h1 class="detail-panel__title">Hayat Rüya Gibi</h1>
      <div class="detail-panel__meta">Göksel · 2024 · 12 şarkı</div>
      <div class="detail-panel__actions">
        <button class="btn-primary btn-primary--sm">Hemen Çal</button>
        <button class="btn-secondary btn-secondary--sm">Karışık Çal</button>
      </div>
    </div>
  </aside>
</main>
```

### Validation

> ⚠️ VERIFICATION REQUIRED — dosyada Validation kaynağı (Accessibility kontrol listesi) yok

---

*03 Album Detail Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
