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

- `viewport`: 1920×1080 · 1x · Mouse+KB · sidebar 240px · 3 sütun (kaynak: 00-device-matrix.md L138)
- `layout_traits`: geniş content · yüksek çözünürlük · çoklu widget (kaynak: 00-device-matrix.md L150)
- `button`: `.btn--primary`/`.btn--secondary` · padding 8px 16px · min-h 36px · radius 12px · durumlar default/hover/active/disabled/loading (kaynak: 02-component-inventory.md L65-L68)

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

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] Modal focus trap korunmuş (Tab döngüsü ve içerik odaklaması); Dropdown ok tuşları çalışır (kanıt: 04-accessibility-gaps.md L135-L136, L154-L160)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*05 Playlist Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
