---
title: CoreMusic — Modal Overlay Layout Pattern
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
---

# CoreMusic — Modal Overlay Layout Pattern

## Kullanım

WiFi, Bluetooth, Hoş Geldin, EQ, Settings

## ASCII Wireframe

```
┌── OVERLAY (tam ekran) ──────────────────────────────────────────────────────┐
│                                                                              │
│  backdrop-filter: blur(4px)                                                  │
│  rgba(0,0,0,0.5)                                                            │
│                                                                              │
│      ┌── MODAL CONTENT ──────────────────────────────────┐                  │
│      │ [✕ kapat] (44×44px, sağ üst)                      │                  │
│      │                                                     │                  │
│      │  [Başlık + İkon]                                   │                  │
│      │                                                     │                  │
│      │  [İçerik — form, liste, bilgi]                     │                  │
│      │                                                     │                  │
│      │  [Aksiyon butonları]                                │                  │
│      │                                                     │                  │
│      └─────────────────────────────────────────────────────┘                  │
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Modal Boyutları

| Modal | Genişlik | Yükseklik | Konum |
|-------|----------|-----------|-------|
| WiFi | ~400px | ~350px | Merkez |
| Bluetooth | ~400px | ~350px | Merkez |
| Hoş Geldin | 600px | 308px | Merkez |
| WiFi Bağlan | ~350px | ~200px | Modal içinde |

## Glass Efekti

```css
.modal {
  background: rgba(255,255,255,0.1);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 16px;
}
```

## Kurallar

1. Backdrop click ile kapatma
2. Escape tuşu ile kapatma
3. ✕ butonu: 44×44px minimum
4. Focus trap: Modal içinde kal
5. ARIA: `role="dialog"`, `aria-modal="true"`

---

*Modal Overlay Layout v2.0.0 — CoreMusic UI Design System*
