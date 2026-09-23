---
title: "CoreMusic — 07 Settings Page Prompt"
type: prompt
category: ui-design
page_id: "07"
route: "/settings"
layout: "04-laptop-sidebar"
date: 2026-09-20
version: 2.0.0
status: active
---

# 07 — Settings Page

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/settings` |
| **Layout** | 04-laptop-sidebar |
| **Purpose** | Ayarlar, settings list |

## 2. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| Settings List | 1 | Content area |
| C15 Toggle | N | Settings items |
| C06 Form Input | N | Settings forms |

## 3. Settings Sections

| Section | Items |
|---------|-------|
| Genel | Dil, Tema, Bildirimler |
| Ses | EQ, Çıkış cihazı, Ses seviyesi |
| Ağ | WiFi, Bluetooth, Proxy |
| Güvenlik | Şifre, 2FA, Oturumlar |
| Medya | İndirme klasörü, Önbellek, Kalite |
| Hakkında | Versiyon, Lisans,Destek |

## 4. Code Example

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

---

*07 Settings Page v2.0.0 — CoreMusic UI Design System*
