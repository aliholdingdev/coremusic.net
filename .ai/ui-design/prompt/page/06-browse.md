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

- `viewport`: 1920×1080 (13" Ultrabook FHD) · 1x · Mouse+KB · sidebar 220px · 3 sütun (kaynak: 00-device-matrix.md L122)
- `layout_traits`: persistent sidebar · hover states · mouse cursor · keyboard nav (kaynak: 00-device-matrix.md L132)
- `navlink`: padding 12px 16px · gap 8px · icon 20px · font 14px/500 · Tier: Laptop+ sidebar · durumlar default/hover/active/disabled (kaynak: 02-component-inventory.md L35-L39)
- `card`: padding 16px · radius 16px · gap 16px · image 1:1 aspect · durumlar default/hover/active/loading/skeleton · varyantlar `--compact`/`--wide` (kaynak: 02-component-inventory.md L55-L58)

### ASCII Reference

```
SIDEBAR: Disk/kategori listesi (scrollable)
CONTENT: 3-column (Disk List | File Browser | Info Panel)
```

### Prompt Template

```json
{
  "task": "Create browse page for CoreMusic",
  "page": "06-browse",
  "viewport": "1920x1080",
  "components": [
    "disk-list",
    "file-browser",
    "info-panel"
  ],
  "tokens": {
    "--cm-sidebar-w-laptop": "220px",
    "--cm-radius-xl": "16px",
    "--cm-space-3": "0.75rem"
  }
}
```

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

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] Modal focus trap korunmuş (Tab döngüsü ve içerik odaklaması); Dropdown ok tuşları çalışır (kanıt: 04-accessibility-gaps.md L135-L136, L154-L160)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*06 Browse Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
