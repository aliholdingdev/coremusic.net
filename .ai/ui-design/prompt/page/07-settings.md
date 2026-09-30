---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 07 Settings Page Prompt"
type: prompt
category: ui-design
page_id: "07"
route: "/settings"
layout: "04-laptop-sidebar"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 07 — Settings Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/settings` |
| **Layout** | 04-laptop-sidebar |
| **Purpose** | Ayarlar, settings list |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| Settings List | 1 | Content area |
| C15 Toggle | N | Settings items |
| C06 Form Input | N | Settings forms |

### Required Inputs

- `viewport`: 1920×1080 (13" Ultrabook FHD) · 1x · Mouse+KB · sidebar 220px · 3 sütun (kaynak: 00-device-matrix.md L122)
- `font_scale`: 1× (kaynak: 00-device-matrix.md L122)
- `toggle`: w 44px · h 24px · knob 18px · radius 9999px · durumlar off/on/disabled · Tier: all tiers same structure (kaynak: 02-component-inventory.md L105-L109)
- `input`: padding 10px 12px · min-h 40px · radius 8px · font 14px/400 · durumlar default/focus/error/success/disabled · varyantlar `--error`/`--success` (kaynak: 02-component-inventory.md L75-L78)

### ASCII Reference

> ⚠️ KAYNAK YOK — screens/ içinde bu tier'a (veya bu sayfaya) ait ASCII karşılığı bulunamadı. Gerekçe: 00-ascii-art-index.md §3–§5 listesi yalnız T07-embedded (12 dosya, 1024×600) + shared/auth (6 dosya, 1024×600) + T17-monitor-22fhd (2 dosya, 1920×1080) kapsar; 01-mockup-index.md L221 viewport kapsamı da 2 (1024×600, 1920×1080) ile sınırlıdır. Kutu ölçüsü kaynağı olmadığından ölçüsüz wireframe üretilmedi.

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<main class="layout--laptop__content">
  <h1>Ayarlar</h1>
  <div class="settings-list">
    <section class="settings-group">
      <h2>Genel</h2>
      <div class="settings-item">
        <span class="settings-item__label">Tema</span>
        <select class="form-input__field" aria-label="Tema seçimi">
          <option>Otomatik</option><option>Açık</option><option>Karanlık</option>
        </select>
      </div>
      <div class="settings-item">
        <span class="settings-item__label">Bildirimler</span>
        <label class="toggle">
          <input type="checkbox" class="toggle__input" role="switch" aria-checked="true">
          <span class="toggle__track"><span class="toggle__thumb"></span></span>
        </label>
      </div>
    </section>
  </div>
</main>
```

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*07 Settings Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
### Settings Sections

| Section | Items |
|---------|-------|
| Genel | Dil, Tema, Bildirimler |
| Ses | EQ, Çıkış cihazı, Ses seviyesi |
| Ağ | WiFi, Bluetooth, Proxy |
| Güvenlik | Şifre, 2FA, Oturumlar |
| Medya | İndirme klasörü, Önbellek, Kalite |
| Hakkında | Versiyon, Lisans,Destek |

---

*07 Settings Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
