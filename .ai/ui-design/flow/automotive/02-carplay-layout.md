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

| Tier | Cihaz | Varyasyon tipi | Örnek davranış | Kaynak |
|------|-------|----------------|----------------|--------|
| T30 (AU-T30) | Apple CarPlay (Küçük) — 800×480 | Touch+Voice, 2 sütun | viewport 800×480 (bu dosya L41), grid 2-3 sütun (L42), touch 80px (L43) | 00-device-matrix.md L178 |
| T30 (AU-T30) | Apple CarPlay (Büyük) — 1920×720 | Touch+Voice, 3 sütun | viewport 1920×720 (bu dosya L41); alt tab bar (L45) | 00-device-matrix.md L179 |

> **Kaynak:** [[../../00-device-matrix]] §3 "🚗 Automotive (T29-T30)" (L172-L182); bu dosyanın frontmatter `tier: T30` (L10) ile eşleşir. Matrix AU-T30 üçüncü cihaz satırı — Tesla Model 3/Y 1920×1200 (L180) — bu dosyanın viewport listesinde (L41) geçmiyor → kapsam dışı. Android Auto (AU-T29) bu akışın değildir → `flow/automotive/01-android-auto-layout.md`.

---

## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.tab`, `.tab-list` | eleman — Navigation: "Tab bar (alt)" (bu dosya L45) | default, hover, active, disabled | 02-component-inventory.md L85 (C06) |
| `.btn`, `.btn--lg` | blok — [◀◀] [▶] [▶▶] / [🔀] [♥] [🔊] kontrol butonları (bu dosya L69-L70) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.slider`, `.slider__thumb` | blok — seek çubuğu "══════════○═══════════" (bu dosya L66) | default, dragging, disabled | 02-component-inventory.md L115 (C09) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`). Geri/ileri [◀] [⋯] ikonları (bu dosya L55) navigasyon ikonudur, envanterde ayrı sınıfı yoktur.

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Uygulama Aç | CarPlay ekranı | CoreMusic ikonuna tıkla |
| 2 | Oynatma Kontrolü | Now Playing | [▶] butonuna tıkla |
| 3 | Ses Ayarla | Now Playing | [🔊] butonuna tıkla |
| 4 | Sesli Komut Ver | Siri Sesli Komut | "Hey Siri, CoreMusic'te devam et" sesli komutu ver |

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
