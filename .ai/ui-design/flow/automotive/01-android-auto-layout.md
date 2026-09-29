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

| Tier | Cihaz | Varyasyon tipi | Örnek davranış | Kaynak |
|------|-------|----------------|----------------|--------|
| T29 (AU-T29) | Android Auto (Küçük) — 800×480 | Touch+Voice, 2 sütun | viewport 800×480, grid 2 sütun, touch target 80px (bu dosya L41-L43) | 00-device-matrix.md L176 |
| T29 (AU-T29) | Android Auto (Orta) — 1280×720 | Touch+Voice, 2 sütun | viewport 1280×720 (bu dosya L41), font 1.125 (matrix) | 00-device-matrix.md L177 |

> **Kaynak:** [[../../00-device-matrix]] §3 "🚗 Automotive (T29-T30)" (L172-L182); bu dosyanın frontmatter `tier: T29` (L10) ile eşleşir. Matrix'in AU-T30 satırları (L178-L180) bu akışın kapsamı değildir → `flow/automotive/02-carplay-layout.md`.

---

## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.card`, `.card--compact` | blok — "Son Çalınan (2 kart)" / "Radyo (1 kart)" (bu dosya L60-L61) | default, hover, active, loading, skeleton | 02-component-inventory.md L55 (C03) |
| `.btn`, `.btn--lg` | blok — kontrol butonları [◀◀] [▶] [▶▶] [🔀] [🔊] (bu dosya L58) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.slider`, `.slider__thumb` | blok — seek çubuğu "══════○═══════════════════" (bu dosya L57) | default, dragging, disabled | 02-component-inventory.md L115 (C09) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`). Güvenlik kuralı "Animasyon devre dışı" (bu dosya L46) tier davranışıdır, sınıf değildir.

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
