---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Apple Watch Now Playing Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
tier: T31
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Apple Watch Now Playing Flow

## 1. Akış Diyagramı (Decision Flow)

### Amaç

Apple Watch'ta müzik oynatma kontrolü ve micro UI akışı.

---

### Akış Şeması

```
Kullanıcı → Watch ekranı
  → CoreMusic complication'ına tıklama
    → Now Playing ekranı
      → Crown ile ses kontrolü
      → Touch ile oynat/duraklat
```

---

## 1A. Layout Kuralları

| Özellik | Değer |
|---------|-------|
| Viewport | 396×484 (40mm) / 502×410 (Ultra) |
| Grid | Tek sütun |
| Touch target | min 44×44px |
| Font | min 12px |
| OLED | Always-on display desteği |
| Crown | Digital Crown ile ses/seek |

---

## 2. Ekran Akışı

### 2.1 Now Playing (Mikro)
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

### 2.2 Quick Controls
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
| OLED Optimized | ✅ |
| Crown Support | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
