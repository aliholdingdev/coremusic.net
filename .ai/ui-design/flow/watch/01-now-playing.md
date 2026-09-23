---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Apple Watch Now Playing Flow"
type: flow
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T31
authority: Single Source of Truth (SSOT)
reference:
  authority: ".ai/ui-design/flow/watch/01-now-playing.md"
---

# CoreMusic — Apple Watch Now Playing Flow

## 1. Amaç

Apple Watch'ta müzik oynatma kontrolü ve micro UI akışı.

---

## 2. Akış Şeması

```
Kullanıcı → Watch ekranı
  → CoreMusic complication'ına tıklama
    → Now Playing ekranı
      → Crown ile ses kontrolü
      → Touch ile oynat/duraklat
```

---

## 3. Layout Kuralları

| Özellik | Değer |
|---------|-------|
| Viewport | 396×484 (40mm) / 502×410 (Ultra) |
| Grid | Tek sütun |
| Touch target | min 44×44px |
| Font | min 12px |
| OLED | Always-on display desteği |
| Crown | Digital Crown ile ses/seek |

---

## 4. Ekran Akışları

### 4.1 Now Playing (Mikro)
```
┌─────────────────┐
│  ┌───────────┐  │
│  │ Album Art │  │
│  │  80×80px  │  │
│  └───────────┘  │
│                  │
│  Şarkı Adı      │
│  Sanatçı        │
│                  │
│  ═══○══════════  │
│                  │
│  [◀◀] [▶] [▶▶]  │
│  [🔊]            │
│                  │
│  Crown: Seek     │
│  Press: Play/Pause│
└─────────────────┘
```

### 4.2 Quick Controls
```
┌─────────────────┐
│  🔊 Ses: 75%    │
│  ════════○════  │
│                  │
│  🔀 Karışık     │
│  🔁 Tekrarla    │
│  ♡ Favori       │
│                  │
│  [Durdur]       │
└─────────────────┘
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| OLED Optimized | ✅ |
| Crown Support | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
