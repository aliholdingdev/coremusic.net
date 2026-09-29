---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Apple CarPlay Layout Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
tier: T30
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Apple CarPlay Layout Flow

## 1. Akış Diyagramı (Decision Flow)

### Amaç

Apple CarPlay ortamında CoreMusic UI'ının render edilmesi ve etkileşim akışı.

---

### Akış Şeması

```
Kullanıcı → CarPlay ekranı
  → CoreMusic ikonuna tıklama
    → Uygulama açılır
      → Son durum state'ini yükle
        → Now Playing ekranı
```

---

## 1A. Layout Kuralları

| Özellik | Değer |
|---------|-------|
| Viewport | 800×480 veya 1920×720 |
| Grid | 2-3 sütun |
| Touch target | min 80×80px |
| Font | min 16px |
| Navigation | Tab bar (alt) |
| Voice | Siri entegrasyonu |

---

## 2. Ekran Akışı

### 2.1 Now Playing
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

### 2.2 Siri Sesli Komut
```
Kullanıcı: "Hey Siri, CoreMusic'te devam et"
  → Siri → CoreMusic API
  → Playback resume
  → Geri bildirim: "Devam ediliyor"
```

---

## 3. Hata Senaryoları

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; hata senaryoları QA doğrulamasından sonra 4 sütunlu tablo (Hata · Tetikleyici · Çözüm · Max Retry) olarak doldurulacak.

---

## 4. Tier-Bazlı Varyasyonlar

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; 7 tier satırı 00-device-matrix.md ile eşleştirilerek doldurulacak.

---

## 5. BEM Sınıfları

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; sınıflar 02-component-inventory.md (C01-C16) ile eşleştirilerek doldurulacak.

---

## 6. Adımlar

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; numaralı, tek-eylem adım listesi akış doğrulamasından sonra doldurulacak.

---

## 6A. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Touch Target | 80px min |
| Siri Support | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
