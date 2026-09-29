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

| Tier | Cihaz | Varyasyon tipi | Örnek davranış | Kaynak |
|------|-------|----------------|----------------|--------|
| T31 (WS-T31) | Apple Watch SE (40mm) / Series 9 (41mm) — 396×484 | Crown+Touch, tek sütun | viewport 396×484 (bu dosya L41), grid tek sütun (L42) | 00-device-matrix.md L188, L189 |
| T32 (WS-T32) | Apple Watch Ultra 2 (49mm) — 502×410 | Crown+Touch, tek sütun | viewport 502×410 "Ultra" (bu dosya L41); crown ile ses/seek (L46) | 00-device-matrix.md L190 |

> **Kaynak:** [[../../00-device-matrix]] §3 "⌚ Smart Watch (T31-T33)" (L184-L194); bu dosyanın frontmatter `tier: T31` (L10) ile eşleşir. Matrix WS-T32'nin Samsung Galaxy Watch 6 (44mm) satırı (L191) ve WS-T33 (L192, 480×480 dairesel) bu dosyada viewport olarak geçmiyor → kapsam dışı. Yinelenen §"Smart Watch Ek" bölümü (L230+) kullanılmadı — birincil bölüm SSG'dir.

---

## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.btn`, `.btn--sm` | blok — [◀◀] [▶] [▶▶] (bu dosya L65) ve [Durdur] (L83) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.slider`, `.slider__fill` | blok — ses "════════○════" (bu dosya L77) ve seek "═══○═══════════" (L63) | default, dragging, disabled | 02-component-inventory.md L115 (C09) |
| `.toggle`, `.toggle--active` | blok — 🔀 Karışık / 🔁 Tekrarla / ♡ Favori (bu dosya L79-L81) | off, on, disabled | 02-component-inventory.md L105 (C08) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`). Crown/Press kontrolleri (bu dosya L68-L69) donanım girdisidir, BEM sınıfı değildir.

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
