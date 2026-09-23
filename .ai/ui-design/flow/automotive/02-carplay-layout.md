---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Apple CarPlay Layout Flow"
type: flow
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T30
authority: Single Source of Truth (SSOT)
reference:
  authority: ".ai/ui-design/flow/automotive/02-carplay-layout.md"
---

# CoreMusic — Apple CarPlay Layout Flow

## 1. Amaç

Apple CarPlay ortamında CoreMusic UI'ının render edilmesi ve etkileşim akışı.

---

## 2. Akış Şeması

```
Kullanıcı → CarPlay ekranı
  → CoreMusic ikonuna tıklama
    → Uygulama açılır
      → Son durum state'ini yükle
        → Now Playing ekranı
```

---

## 3. Layout Kuralları

| Özellik | Değer |
|---------|-------|
| Viewport | 800×480 veya 1920×720 |
| Grid | 2-3 sütun |
| Touch target | min 80×80px |
| Font | min 16px |
| Navigation | Tab bar (alt) |
| Voice | Siri entegrasyonu |

---

## 4. Ekran Akışları

### 4.1 Now Playing
```
┌──────────────────────────────┐
│ [◀] CoreMusic          [⋯]  │
├──────────────────────────────┤
│                              │
│    ┌──────────────────┐      │
│    │   Album Art       │      │
│    │   300×300px       │      │
│    └──────────────────┘      │
│                              │
│    Şarkı Adı                 │
│    Sanatçı · Albüm           │
│                              │
│    ═══════════○═══════════   │
│    01:23          04:56      │
│                              │
│    [◀◀]  [▶]  [▶▶]          │
│    [🔀]  [♥]  [🔊]          │
│                              │
├──────────────────────────────┤
│ [📂] [🔍] [📻] [⚙️]         │
└──────────────────────────────┘
```

### 4.2 Siri Sesli Komut
```
Kullanıcı: "Hey Siri, CoreMusic'te devam et"
  → Siri → CoreMusic API
  → Playback resume
  → Geri bildirim: "Devam ediliyor"
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Touch Target | 80px min |
| Siri Support | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
