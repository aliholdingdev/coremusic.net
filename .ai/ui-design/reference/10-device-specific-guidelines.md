---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Device-Specific Guidelines (10 Categories)"
type: reference
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/10-device-specific-guidelines.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md · .ai/ui-design/tokens/platform-tokens.md"
---

# CoreMusic — Device-Specific Guidelines (10 Categories)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[tokens/platform-tokens]] · [[04-accessibility-gaps]]

---

## 1. Amaç

10 cihaz kategorisi için **UI kılavuzları ve özel kurallar**dır. Tier bazlı geliştirme yaparken bu dosya okunur.

---

## 2. Cihaz Kılavuzları

### 2.1 Phone (T01-T04)

| Kural | Değer |
|-------|-------|
| Layout | Tek sütun, dikey scroll |
| Nav | Bottom tab bar (3-4 item) |
| Touch target | 48px minimum |
| Font scale | 0.875-1 |
| Sidebar | Yok |
| Header | Yok (bottom nav) |
| Footer | Kompakt player (80px) |
| Gesture | Swipe, pull-to-refresh |
| Orientation | Portrait (varsayılan) |

### 2.2 Tablet (T05-T06)

| Kural | Değer |
|-------|-------|
| Layout | 2 sütun grid |
| Nav | Top nav bar (5-6 item) |
| Touch target | 48px minimum |
| Font scale | 1 |
| Sidebar | Yok |
| Header | 56-60px |
| Footer | Player (96px) |
| Gesture | Touch + stylus |
| Orientation | Portrait + Landscape |

### 2.3 Embedded (T07-T08) — RPi5

| Kural | Değer |
|-------|-------|
| Layout | 2-3 sütun, sidebar yok |
| Nav | Top nav (4-5 item) |
| Touch target | 48px minimum |
| Font scale | 1 |
| Sidebar | Yok |
| Header | 60-64px |
| Footer | Player (90-96px) |
| Welcome | Popup (T07 only) |
|特殊 | Offline-first, minimal chrome |

### 2.4 Laptop (T09-T10)

| Kural | Değer |
|-------|-------|
| Layout | 3 sütun + sidebar |
| Nav | Sidebar nav (8 item) |
| Touch target | 32px |
| Font scale | 1 |
| Sidebar | 220-240px persistent |
| Header | 64-68px |
| Footer | Player (100-104px) |
| Hover | Aktif |
| Keyboard | Tab navigation |

### 2.5 Desktop (T11-T12)

| Kural | Değer |
|-------|-------|
| Layout | 3-4 sütun + sidebar |
| Nav | Sidebar nav (8 item) |
| Touch target | 28-32px |
| Font scale | 1-1.25 |
| Sidebar | 260-300px persistent |
| Header | 70-80px |
| Footer | Player (108-120px) |
| Hover | Aktif |
| Multi-window | Destekli |

### 2.6 Ultrawide (T13-T14)

| Kural | Değer |
|-------|-------|
| Layout | 5-6 sütun, center-aligned |
| Nav | Sidebar nav (8 item) |
| Touch target | 24-28px |
| Font scale | 1-1.125 |
| Sidebar | 280-300px |
| Header | 72-76px |
| Footer | Player (112-116px) |
|特殊 | Max content width: 1440px |

### 2.7 Smart TV (T15-T18)

| Kural | Değer |
|-------|-------|
| Layout | 4-5 sütun, large cards |
| Nav | D-pad navigation |
| Touch target | 80-120px |
| Font scale | 1.5-2.5 |
| Sidebar | Yok |
| Header | 80-104px |
| Footer | Player (120-150px) |
|特殊 | 10-foot UI, high contrast |
| Focus | Large focus indicators |

### 2.8 Automotive (T19)

| Kural | Değer |
|-------|-------|
| Layout | 2 sütun, minimal |
| Nav | Large buttons (4 item) |
| Touch target | 80px minimum |
| Font scale | 1.125 |
| Sidebar | Yok |
| Header | Yok |
| Footer | Player (100px) |
|特殊 | Safety-first, driver mode |
| Voice | Voice command support |

### 2.9 Smart Watch (T20-T22)

| Kural | Değer |
|-------|-------|
| Layout | Tek sütun, micro UI |
| Nav | Crown + touch |
| Touch target | 44px |
| Font scale | 0.75-0.875 |
| Sidebar | Yok |
| Header | Yok |
| Footer | Yok |
|特殊 | OLED power save, haptic |
| Battery | Low power mode |

### 2.10 Console (T23-T26)

| Kural | Değer |
|-------|-------|
| Layout | 2-4 sütun, large cards |
| Nav | Controller D-pad |
| Touch target | 48-96px |
| Font scale | 1-1.75 |
| Sidebar | Yok |
| Header | 56-88px |
| Footer | Player (88-130px) |
|特殊 | 10-foot UI, controller nav |
| Focus | Large focus ring |

---

## 3. Orientation Rules

| Cihaz | Portrait | Landscape | Special |
|-------|----------|-----------|---------|
| Phone | ✅ Varsayılan | ✅ Destekli | Rotation lock |
| Tablet | ✅ | ✅ | Layout adapt |
| Embedded | ✅ (7") | ✅ (10") | Sabit orientasyon |
| Laptop | ❌ | ✅ Varsayılan | — |
| Desktop | ❌ | ✅ Varsayılan | — |
| TV | ❌ | ✅ Varsayılan | — |
| Car | ❌ | ✅ Varsayılan | Landscape only |
| Watch | ✅ Varsayılan | ❌ | — |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Device Categories | 10 |
| Rules per Category | 8-10 |
| Orientation Rules | 8 |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
