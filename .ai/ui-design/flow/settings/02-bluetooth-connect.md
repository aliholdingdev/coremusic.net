---
title: CoreMusic — Settings Flow: Bluetooth Connect (Detaylı)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/F-quickpanel/bluetooth]]
  - [[screens/00-ascii-art-views]] §12
  - [[01-component-inventory]] C14, C15, C16
---

# Settings Flow: Bluetooth Connect — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    BLUETOOTH CONNECT AKIŞI                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Header'da    │ →  │ BT Modal     │ →  │ Cihaz        │                  │
│  │ BT ikonu     │    │ Açılır       │    │ Taraması     │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Bağlı   │  │ Kullanılabilir│     │
│                                         │ Cihaz   │  │ Cihazlar  │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                              └──────┬───────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Eşleşme     │               │
│                                              │ (pairing)   │               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Bağlantı    │               │
│                                              │ kurulur     │               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Header      │               │
│                                              │ güncellenir │               │
│                                              └─────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BLUETOOTH MODAL YAPISI (PNG Layout)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:320                                                        x:704         │
│ y:130 ┌─────────────────────────────────────────────────────────────────┐  │
│        │ [✳ icon 24×24px]                                               │  │
│        │ Bluetooth                                                       │  │
│        │ Cihaz Bağlantıları                                             │  │
│        │                                                                 │  │
│        │ Bluetooth  [━━━━━━○] (C15 toggle, pembe)                       │  │
│        │                                                                 │  │
│        │ ── Bağlı Olan Cihaz ──                                        │  │
│        │ ┌─────────────────────────────────────────────────────────┐    │  │
│        │ │[🎧] Kim - 50 [Güçlü][A2DP][HFP][Müzik]                 │    │  │
│        │ │    Tarayıcı · Mükemmel · 100%           [Bağlantıyı Kes]│   │  │
│        │ └─────────────────────────────────────────────────────────┘    │  │
│        │                                                                 │  │
│        │ ── Kullanılabilir Cihazlar ──                                 │  │
│        │ ┌─────────────────────────────────────────────────────────┐    │  │
│        │ │[🎧] Kim - 50 [Güçlü][A2DP][HFP]  Mükemmel·100% [Eşle] │    │  │
│        │ │[🚗] Car BT [Orta][A2DP][HFP]      İyi·-70BS    [Eşle]  │    │  │
│        │ │[📺] Samsung TV [Zayıf][A2DP]       Mükemmel·100% [Eşle] │    │  │
│        │ └─────────────────────────────────────────────────────────┘    │  │
│ y:470 └─────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. CİHAZ ROZETLERİ

| Badge | Background | Anlam |
|-------|-----------|-------|
| A2DP | `var(--theme-primary)` | Yüksek kalite ses profili |
| HFP | `#6366f1` (mor) | Hands-free profil |
| Müzik | `#22c55e` (yeşil) | Müzik servisi |
| Güçlü | `#22c55e` (yeşil) | Sinyal > -50BS |
| Orta | `#eab308` (sarı) | Sinyal -50 ~ -70BS |
| Zayıf | `#ef4444` (kırmızı) | Sinyal < -70BS |

---

## 4. DAVRANIŞ DETAYLARI

### 4.1 — Modal Açılışı

```
Kullanıcı header'daki BT ikonuna tıklar
  → BT modal açılır (fade-in: 200ms)
  → Backdrop blur uygulanır
  → Mevcut cihaz durumu yüklenir
  → Bağlı olan cihaz üstte
  → Kullanılabilir cihazlar listelenir (tarama animasyonu ile)
```

### 4.2 — Toggle Değişikliği

```
Kullanıcı Bluetooth toggle'ını kapatır
  → Toggle animasyonu (250ms)
  → Bluetooth donanımı kapatılır
  → Tüm cihaz listesi griye döner

Kullanıcı Bluetooth toggle'ını açar
  → Toggle animasyonu (250ms)
  → Bluetooth donanımı açılır
  → Cihaz taraması başlatılır (3s)
  → "Taranıyor..." gösterilir
  → Cihazlar listelenir
```

### 4.3 — Eşleşme (Pairing)

```
Kullanıcı bir cihaza "Eşle" tıklar
  → Eşleşme isteği gönderilir
  → Karşı cihazda eşleşme onayı istenir
  → Onay → Bağlantı kurulur
  → "Bağlantı kuruldu" bildirimi
  → Header'daki BT ikonu güncellenir
```

### 4.4 — "Bağlantıyı Kes" Tıklama

```
Kullanıcı bağlı cihazdaki "Bağlantıyı Kes" butonuna tıklar
  → Bağlantı kesilir
  → Eşleşme bilgisi korunur (yeniden eşleşmeye gerek yok)
  → Header'daki BT ikonu güncellenir
```

---

## 5. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (toggle) | ⚠️ ~28px |
| Touch target (buton) | ✅ 48px |
| Touch target (satır) | ✅ 48px |
| Focus indicator | ✅ |
| Escape ile kapatma | ✅ |
| ARIA | ✅ `role="dialog"` |

---

## 6. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/F-quickpanel/bluetooth]] | BT screen spec |
| [[screens/00-ascii-art-views]] §12 | ASCII art |
| [[01-component-inventory]] C14, C15, C16 | Bileşenler |
| [[flow/settings/01-wifi-connect]] | WiFi (aynı pattern) |

---

*Bluetooth Connect Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
