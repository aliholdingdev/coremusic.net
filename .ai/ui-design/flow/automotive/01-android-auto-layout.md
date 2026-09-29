---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Android Auto Layout Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
tier: T29
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Android Auto Layout Flow

## 1. Akış Diyagramı (Decision Flow)

### Amaç

Android Auto ortamında CoreMusic UI'ının nasıl render edileceği ve etkileşim akışı.

---

### Akış Şeması

```
Kullanıcı → Android Auto ekranı
  → CoreMusic uygulaması açılır
    → Safety check: araç hareket halinde mi?
      → EVET: Minimal UI (sadece ses kontrolü)
      → HAYIR: Tam UI (2-sütun layout)
```

---

## 1A. Layout Kuralları

| Özellik | Değer |
|---------|-------|
| Viewport | 800×480 veya 1280×720 |
| Grid | 2 sütun |
| Touch target | min 80×80px |
| Font | min 16px |
| Renk | High contrast (WCAG AAA) |
| Animasyon | Devre dışı (güvenlik) |

---

## 2. Ekran Akışı

### 2.1 Ana Sayfa
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

### 2.2 Sesli Komut
```
Kullanıcı: "Hey Google, CoreMusic'te [şarkı adı] çal"
  → Voice recognition
  → Şarkı bulma
  → Oynatma başlatma
  → Geri bildirim: "[Şarkı adı] çalınıyor"
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
| Voice Support | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
