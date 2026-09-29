---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 06 Browse Page Prompt"
type: prompt
category: ui-design
page_id: "06"
route: "/browse"
layout: "04-laptop-sidebar"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 06 — Browse Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/browse` |
| **Layout** | 04-laptop-sidebar + 3-column content |
| **Purpose** | Dosya tarayıcı, disk/kategori listesi |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| Disk List | 1 | Sidebar (240px) |
| File Browser | 1 | Content left |
| Info Panel | 1 | Content right (220px) |

### Required Inputs

> ⚠️ VERIFICATION REQUIRED — dosyada Required Inputs kaynağı (Tier Sizes / Variants / States / Requirements) yok

### ASCII Reference

```
SIDEBAR: Disk/kategori listesi (scrollable)
CONTENT: 3-column (Disk List | File Browser | Info Panel)
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<main class="layout--laptop__content" style="display:grid;grid-template-columns:167px 1fr 220px;gap:16px">
  <nav class="disk-list" aria-label="Diskler"><!-- disk items --></nav>
  <section class="file-browser">
    <nav aria-label="Breadcrumb"><!-- breadcrumb --></nav>
    <div class="file-list" role="list"><!-- file rows --></div>
  </section>
  <aside class="info-panel"><!-- selected file info --></aside>
</main>
```

### Validation

> ⚠️ VERIFICATION REQUIRED — dosyada Validation kaynağı (Accessibility kontrol listesi) yok

---

*06 Browse Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
