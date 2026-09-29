---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 11 WiFi Page Prompt"
type: prompt
category: ui-design
page_id: "11"
route: "overlay"
layout: "modal"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 11 — WiFi Modal Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | overlay (modal) |
| **Layout** | Modal Overlay |
| **Purpose** | WiFi ayarları, ağ listesi |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C14 Modal | 1 | Center (380px) |
| C15 Toggle | 1 | Modal top |
| C16 Network Row | N | Modal body |
| Close Button | 1 | Modal header |

### Required Inputs

> ⚠️ VERIFICATION REQUIRED — dosyada Required Inputs kaynağı (Tier Sizes / Variants / States / Requirements) yok

### ASCII Reference

```
┌─── C14 Modal ──────────────────────────┐
│  WiFi Ayarları                [×]      │
├────────────────────────────────────────┤
│  WiFi: [ON/OFF] C15 Toggle            │
├────────────────────────────────────────┤
│  ┌──────────────────────────────────┐  │
│  │ 📶 MyHomeWiFi    ▮▮▮▯  🔒 [Bağlan]│  │
│  ├──────────────────────────────────┤  │
│  │ 📶 NeighborWiFi  ▮▮▯▯  🔒 [Bağlan]│  │
│  ├──────────────────────────────────┤  │
│  │ 📶 GuestNetwork  ▮▯▯▯  🔓 [Bağlan]│  │
│  └──────────────────────────────────┘  │
└────────────────────────────────────────┘
```

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```html
<div class="modal-overlay is-open" aria-hidden="false">
  <div class="modal" role="dialog" aria-labelledby="wifi-title" aria-modal="true">
    <div class="modal__header">
      <h2 class="modal__title" id="wifi-title">WiFi Ayarları</h2>
      <button class="modal__close" aria-label="Kapat">✕</button>
    </div>
    <div class="modal__body">
      <div class="settings-item">
        <span class="settings-item__label">WiFi</span>
        <label class="toggle">
          <input type="checkbox" class="toggle__input" role="switch" aria-checked="true" checked>
          <span class="toggle__track"><span class="toggle__thumb"></span></span>
        </label>
      </div>
      <div class="network-list" role="list" aria-label="WiFi ağları">
        <!-- C16 network rows -->
      </div>
    </div>
  </div>
</div>
```

### Validation

> ⚠️ VERIFICATION REQUIRED — dosyada Validation kaynağı (Accessibility kontrol listesi) yok

---

*11 WiFi Modal v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
