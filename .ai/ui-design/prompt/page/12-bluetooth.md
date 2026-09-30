---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 12 Bluetooth Page Prompt"
type: prompt
category: ui-design
page_id: "12"
route: "overlay"
layout: "modal"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 12 — Bluetooth Modal Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | overlay (modal) |
| **Layout** | Modal Overlay |
| **Purpose** | Bluetooth ayarları, cihaz listesi |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C14 Modal | 1 | Center (380px) |
| C15 Toggle | 1 | Modal top |
| C16 Device Row | N | Modal body |
| Close Button | 1 | Modal header |

### Required Inputs

- `viewport`: 1024×600 (RPi5 7" Touch) · 2 sütun · sidebar yok (kaynak: 00-device-matrix.md L111)
- `touch`: 48px minimum dokunma hedefi (kaynak: 00-device-matrix.md L111)
- `modal`: padding 24px · radius 20px · max-w 480px · overlay 60% black · durumlar closed/opening/open/closing · Tier: Phone full-width / Desktop centered (kaynak: 02-component-inventory.md L95-L99)
- `toggle`: w 44px · h 24px · knob 18px · radius 9999px · durumlar off/on/disabled (kaynak: 02-component-inventory.md L105-L108)

### ASCII Reference

```
┌─── C14 Modal ──────────────────────────┐
│  Bluetooth Ayarları           [×]      │
├────────────────────────────────────────┤
│  Bluetooth: [ON/OFF] C15 Toggle       │
├────────────────────────────────────────┤
│  ┌──────────────────────────────────┐  │
│  │ 🎧 AirPods Pro      ✓ Bağlı    │  │
│  ├──────────────────────────────────┤  │
│  │ 📱 Galaxy S23       [Bağlan]    │  │
│  ├──────────────────────────────────┤  │
│  │ 🔊 JBL Flip 5       [Bağlan]    │  │
│  └──────────────────────────────────┘  │
└────────────────────────────────────────┘
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<div class="modal-overlay is-open" aria-hidden="false">
  <div class="modal" role="dialog" aria-labelledby="bt-title" aria-modal="true">
    <div class="modal__header">
      <h2 class="modal__title" id="bt-title">Bluetooth Ayarları</h2>
      <button class="modal__close" aria-label="Kapat">✕</button>
    </div>
    <div class="modal__body">
      <div class="settings-item">
        <span class="settings-item__label">Bluetooth</span>
        <label class="toggle">
          <input type="checkbox" class="toggle__input" role="switch" aria-checked="true" checked>
          <span class="toggle__track"><span class="toggle__thumb"></span></span>
        </label>
      </div>
      <div class="network-list" role="list" aria-label="Bluetooth cihazları">
        <div class="network-row is-connected" role="listitem">
          <span class="network-row__icon" aria-hidden="true">🎧</span>
          <span class="network-row__name">AirPods Pro</span>
          <span class="network-row__status network-row__status--connected">✓ Bağlı</span>
        </div>
        <div class="network-row" role="listitem">
          <span class="network-row__icon" aria-hidden="true">📱</span>
          <span class="network-row__name">Galaxy S23</span>
          <button class="network-row__connect" aria-label="Galaxy S23'e bağlan">Bağlan</button>
        </div>
      </div>
    </div>
  </div>
</div>
```

### Validation

- [ ] Dokunma hedefleri 48px proje zeminine göre ölçülü (AA SC 2.5.8 = 24px); Toggle 48×48px, Slider 48px alan, Dropdown item 48px düzeltmeleri uygulanmış (kanıt: 04-accessibility-gaps.md L27, L39, L64-L96)
- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] Modal focus trap korunmuş (Tab döngüsü ve içerik odaklaması); Dropdown ok tuşları çalışır (kanıt: 04-accessibility-gaps.md L135-L136, L154-L160)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*12 Bluetooth Modal v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
### Device Types

| Icon | Type | Description |
|------|------|-------------|
| 🎧 | Kulaklık | Bluetooth kulaklık |
| 📱 | Telefon | Akıllı telefon |
| 🔊 | Hoparlör | Bluetooth hoparlör |
| ⌨ | Klavye | Bluetooth klavye |
| 🖱 | Fare | Bluetooth fare |

---

*12 Bluetooth Modal v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
