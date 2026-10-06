---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 02 Albums Page Prompt"
type: prompt
category: ui-design
page_id: "02"
route: "/albums"
layout: "05-desktop-3col"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 02 — Albums Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/albums` |
| **Layout** | 05-desktop-3col (60/40 split) |
| **Purpose** | Albüm listesi, card grid, detail panel |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C01 Nav Link | 8 | Header |
| C11 Genre Tabs | 1 | Content top |
| C09 Media Card | 9+ | Content left (60%) |
| C10 Detail Panel | 1 | Content right (40%) |

**Interactions**

| Element | Action |
|---------|--------|
| Genre tabs | Filter albums by genre |
| Card click | Select album, show detail |
| Play button | Start playing album |
| Shuffle button | Shuffle play |

### Required Inputs

- `viewport`: 1920×1080 · 1x · Mouse+KB · sidebar 240px · 3 sütun (kaynak: 00-device-matrix.md L138)
- `layout_traits`: geniş content · yüksek çözünürlük · çoklu widget (kaynak: 00-device-matrix.md L150)
- `navlink`: padding 12px 16px · gap 8px · icon 20px · Tier: Laptop+ sidebar · durumlar default/hover/active/disabled (kaynak: 02-component-inventory.md L35-L39)
- `card`: padding 16px · radius 16px · gap 16px · image 1:1 aspect · durumlar default/hover/active/loading/skeleton · varyantlar `--compact`/`--wide` (kaynak: 02-component-inventory.md L55-L58)
- `tab`: padding 8px 16px · gap 4px · radius 8px · font 13px/500 · durumlar default/hover/active/disabled (kaynak: 02-component-inventory.md L85-L88)

### ASCII Reference

```
LEFT (60%): Genre Tabs → Card Grid (3×3, 140px cards)
RIGHT (40%): Detail Panel (album art + metadata + actions)
```

### Prompt Template

```json
{
  "task": "Create albums page for CoreMusic",
  "page": "02-albums",
  "viewport": "1920x1080",
  "components": [
    "nav-link",
    "genre-tabs",
    "media-card",
    "detail-panel"
  ],
  "tokens": {
    "--cm-sidebar-w": "240px",
    "--cm-radius-xl": "16px",
    "--cm-radius-md": "8px",
    "--cm-space-2": "0.5rem"
  }
}
```

### Expected Output

```html
<div class="layout layout--desktop">
  <header class="site-header">...</header>
  <aside class="layout--desktop__sidebar"><!-- C01 nav --></aside>
  <main class="layout--desktop__content">
    <section class="content-left">
      <div class="genre-tabs" role="tablist"><!-- C11 --></div>
      <div class="media-card-grid"><!-- C09 cards --></div>
    </section>
    <aside class="content-right">
      <div class="detail-panel"><!-- C10 --></div>
    </aside>
  </main>
  <footer class="site-footer">...</footer>
</div>
```

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Primary button kontrastı 3.1:1 ❌ → koyu text `#1a1a2e` veya açık pink `#ff6ee4` düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L110, L120-L123)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] Modal focus trap korunmuş (Tab döngüsü ve içerik odaklaması); Dropdown ok tuşları çalışır (kanıt: 04-accessibility-gaps.md L135-L136, L154-L160)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*02 Albums Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
