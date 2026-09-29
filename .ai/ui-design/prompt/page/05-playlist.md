---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 05 Playlist Page Prompt"
type: prompt
category: ui-design
page_id: "05"
route: "/playlist/:id"
layout: "05-desktop-3col"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 05 — Playlist Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/playlist/:id` |
| **Layout** | 05-desktop-3col (60/40 split) |
| **Purpose** | Çalma listesi, tablo görünümü |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C13 Track Table | 1 | Content left |
| C12 Star Rating | 1 | Below table |
| C10 Detail Panel | 1 | Content right |
| Transport Icons | 3 | Above table (Play, Shuffle, Repeat) |

### Required Inputs

> ⚠️ VERIFICATION REQUIRED — dosyada Required Inputs kaynağı (Tier Sizes / Variants / States / Requirements) yok

### ASCII Reference

```
LEFT (60%): Transport icons → Track Table (C13) → Star Rating (C12)
RIGHT (40%): Playlist Detail (Cover + Name + Creator + Stats)
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<section class="content-left">
  <div class="playlist-actions">
    <button class="btn-primary btn-primary--icon" aria-label="Oynat">▶</button>
    <button class="btn-secondary btn-secondary--icon" aria-label="Karıştır">🔀</button>
    <button class="btn-secondary btn-secondary--icon" aria-label="Tekrarla">🔁</button>
  </div>
  <div class="track-list" role="list"><!-- C13 rows --></div>
  <div class="star-rating"><!-- C12 --></div>
</section>
```

### Validation

> ⚠️ VERIFICATION REQUIRED — dosyada Validation kaynağı (Accessibility kontrol listesi) yok

---

*05 Playlist Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
