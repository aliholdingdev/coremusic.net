---
title: "CoreMusic — 11 WiFi Page Prompt"
type: prompt
category: ui-design
page_id: "11"
route: "overlay"
layout: "modal"
date: 2026-09-20
version: 2.0.0
status: active
---

# 11 — WiFi Modal

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | overlay (modal) |
| **Layout** | Modal Overlay |
| **Purpose** | WiFi ayarları, ağ listesi |

## 2. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| C14 Modal | 1 | Center (380px) |
| C15 Toggle | 1 | Modal top |
| C16 Network Row | N | Modal body |
| Close Button | 1 | Modal header |

## 3. Modal Structure

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

## 4. Code Example

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

---

*11 WiFi Modal v2.0.0 — CoreMusic UI Design System*
