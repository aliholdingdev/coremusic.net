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

- `viewport`: 1920×1080 · 1x · Mouse+KB · sidebar 240px · 3 sütun (kaynak: 00-device-matrix.md L138)
- `button`: `.btn--primary`/`.btn--secondary` · padding 8px 16px · min-h 36px · radius 12px · font 14px/600 · durumlar default/hover/active/disabled/loading (kaynak: 02-component-inventory.md L65-L68)
- `button_tier`: Phone 44px min-h · TV 56px min-h (tier uyarlaması) (kaynak: 02-component-inventory.md L69)

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

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*03 Album Detail Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
