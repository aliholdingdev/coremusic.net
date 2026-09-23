---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Android Auto Layout Flow"
type: flow
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T29
authority: Single Source of Truth (SSOT)
reference:
  authority: ".ai/ui-design/flow/automotive/01-android-auto-layout.md"
---

# CoreMusic — Android Auto Layout Flow

## 1. Amaç

Android Auto ortamında CoreMusic UI'ının nasıl render edileceği ve etkileşim akışı.

---

## 2. Akış Şeması

```
Kullanıcı → Android Auto ekranı
  → CoreMusic uygulaması açılır
    → Safety check: araç hareket halinde mi?
      → EVET: Minimal UI (sadece ses kontrolü)
      → HAYIR: Tam UI (2-sütun layout)
```

---

## 3. Layout Kuralları

| Özellik | Değer |
|---------|-------|
| Viewport | 800×480 veya 1280×720 |
| Grid | 2 sütun |
| Touch target | min 80×80px |
| Font | min 16px |
| Renk | High contrast (WCAG AAA) |
| Animasyon | Devre dışı (güvenlik) |

---

## 4. Ekran Akışları

### 4.1 Ana Sayfa
```
┌──────────────────────────────┐
│ Now Playing (büyük)          │
│ [Art 120×120] Şarkı · Sanatçı│
│ ═══════○═══════════════════  │
│ [◀◀] [▶] [▶▶] [🔀] [🔊]   │
├──────────────┬───────────────┤
│ Son Çalınan  │ Radyo         │
│ (2 kart)     │ (1 kart)      │
└──────────────┴───────────────┘
```

### 4.2 Sesli Komut
```
Kullanıcı: "Hey Google, CoreMusic'te [şarkı adı] çal"
  → Voice recognition
  → Şarkı bulma
  → Oynatma başlatma
  → Geri bildirim: "[Şarkı adı] çalınıyor"
```

---

## 5. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Touch Target | 80px min |
| Voice Support | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
