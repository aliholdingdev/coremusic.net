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

> ⚠️ VERIFICATION REQUIRED — dosyada Required Inputs kaynağı (Tier Sizes / Variants / States / Requirements) yok

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

> ⚠️ VERIFICATION REQUIRED — dosyada Validation kaynağı (Accessibility kontrol listesi) yok

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
