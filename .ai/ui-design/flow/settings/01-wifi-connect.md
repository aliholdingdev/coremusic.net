---
title: CoreMusic — Settings Flow: WiFi Connect (Detaylı)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/F-quickpanel/wifi]]
  - [[screens/F-quickpanel/wifi-connect]]
  - [[screens/00-ascii-art-views]] §10-11
  - [[01-component-inventory]] C14, C15, C16, C06, C04
---

# Settings Flow: WiFi Connect — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        WiFi CONNECT AKIŞI                                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Header'da    │ →  │ WiFi Modal   │ →  │ Ağ Listesi   │                  │
│  │ WiFi ikonu   │    │ Açılır       │    │ Yüklenir     │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Bağlı   │  │ Kullanılabilir│     │
│                                         │ Ağ var  │  │ Ağlar     │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                              └──────┬───────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Ağ Seçilir  │               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Bağlan      │               │
│                                              │ butonu      │               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Şifre       │               │
│                                              │ sub-dialog  │               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Bağlantı    │               │
│                                              │ kurulur     │               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Modal       │               │
│                                              │ kapatılır   │               │
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

## 2. WIFI MODAL YAPISI (PNG Layout)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:320                                                        x:704         │
│ y:130 ┌─────────────────────────────────────────────────────────────────┐  │
│        │ [📶 icon 24×24px]                                              │  │
│        │ Wi-Fi                                                           │  │
│        │ Ağ Bağlantıları                                               │  │
│        │                                                                 │  │
│        │ Wi-Fi  [━━━━━━○] (C15 toggle, pembe, aktif)                   │  │
│        │                                                                 │  │
│        │ ── Bağlı Olan Ağ ──                                           │  │
│        │ ┌─────────────────────────────────────────────────────────┐    │  │
│        │ │[📶] Bayram Ali Home [Güçlü][5GHz]                       │    │  │
│        │ │    5GHz · Mükemmel sinyal · 100%           [Bağlantıyı Kes]│  │  │
│        │ └─────────────────────────────────────────────────────────┘    │  │
│        │                                                                 │  │
│        │ ── Kullanılabilir Ağlar ──                                    │  │
│        │ ┌─────────────────────────────────────────────────────────┐    │  │
│        │ │[📶] Bayram Ali Home [Güçlü]  5GHz·Mükemmel·-55BS [Bağlan]│   │  │
│        │ │[📶] Bayram Ali Home [Orta]   5GHz·İyi·-70BS    [Bağlan] │    │  │
│        │ │[📶] Bayram Ali Home [Zayıf]  2.4GHz·Orta·-85BS [Bağlan] │    │  │
│        │ └─────────────────────────────────────────────────────────┘    │  │
│ y:470 └─────────────────────────────────────────────────────────────────┘  │
│                                                                             │
│ Glass: backdrop-filter: blur(20px) saturate(180%)                          │
│ Border-radius: 16px                                                        │
│ Border: 1px solid rgba(255,255,255,0.1)                                   │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. AĞ SATIRLARI (C16) DETAY

### 3.1 — Bağlı Olan Ağ

| Özellik | Değer |
|---------|-------|
| İkon | 📶 (24×24px) |
| İsim | Bayram Ali Home |
| Badge'ler | [Güçlü] (yeşil), [5GHz] (pembe) |
| Detay | 5GHz · Mükemmel sinyal · 100% |
| Buton | [Bağlantıyı Kes] (C05, pembe) |
| Yükseklik | ~48px |
| Background | `rgba(255,255,255,0.05)` |
| Border-radius | 8px |

### 3.2 — Kullanılabilir Ağlar

| Özellik | Değer |
|---------|-------|
| İsim | Bayram Ali Home |
| Badge'ler | [Güçlü/Orta/Zayıf] + [5GHz/2.4GHz] |
| Sinyal gücü | -55BS / -70BS / -85BS |
| Buton | [Bağlan] (C05, sınır) |
| Yükseklik | ~48px |

### 3.3 — Badge Renkleri

| Badge | Background | Anlam |
|-------|-----------|-------|
| Güçlü | `#22c55e` (yeşil) | Sinyal > -50BS |
| Orta | `#eab308` (sarı) | Sinyal -50 ~ -70BS |
| Zayıf | `#ef4444` (kırmızı) | Sinyal < -70BS |
| 5GHz | `var(--theme-primary)` | 5GHz bandı |
| 2.4GHz | `rgba(255,255,255,0.5)` | 2.4GHz bandı |

---

## 4. ŞİFRE SUB-DIALOG (PNG Layout)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:340                                                        x:684         │
│ y:250 ┌─────────────────────────────────────────────────────────────────┐  │
│        │ Bayram Ali - WiFi  [📶]                                        │  │
│        │ 5GHz · Mükemmel sinyal · 100% · Güvenli Bağlantı             │  │
│        │                                                                 │  │
│        │ Kablosuz Ağ Şifresi                                            │  │
│        │ ┌─────────────────────────────────────────────────────────┐    │  │
│        │ │ ●●●●●●●●                                                  │    │  │
│        │ │ (C06 input, pembe border, şifre gizli)                   │    │  │
│        │ └─────────────────────────────────────────────────────────┘    │  │
│        │                                                                 │  │
│        │ ☑ Kablosuz ağa her zaman otomatik bağlan                      │  │
│        │                                                                 │  │
│        │ [İptal] (C05, sınır, ~80×48px)  [Bağlan] (C04, pembe, ~120×56px)│ │
│ y:400 └─────────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. DAVRANIŞ DETAYLARI

### 5.1 — Modal Açılışı

```
Kullanıcı header'daki WiFi ikonuna tıklar
  → WiFi modal açılır (fade-in: 200ms)
  → Backdrop blur uygulanır: rgba(0,0,0,0.5) + blur(4px)
  → Mevcut ağ durumu yüklenir (async)
  → Bağlı olan ağ üstte gösterilir
  → Kullanılabilir ağlar listelenir
  → Toggle durumu okunur (açık/kapalı)
```

### 5.2 — Toggle Değişikliği

```
Kullanıcı WiFi toggle'ını kapatır
  → Toggle animasyonu (250ms)
  → WiFi donanımı kapatılır
  → Tüm ağ listesi griye döner
  → "Bağlı Olan Ağ" bölümü gizlenir
  → "Kullanılabilir Ağlar" bölümü gizlenir

Kullanıcı WiFi toggle'ını açar
  → Toggle animasyonu (250ms)
  → WiFi donanımı açılır
  → Ağ taraması başlatılır
  → Ağlar listelenir (yükleme animasyonu ile)
```

### 5.3 — Ağ Seçimi

```
Kullanıcı bir ağa tıklar
  → Seçili ağ vurgulanır (border: 2px solid var(--theme-primary))
  → "Bağlan" butonu aktif olur
  → Diğer satırlar default'a döner
```

### 5.4 — "Bağlan" Tıklama

```
Kullanıcı "Bağlan"'a tıklar
  → Şifre sub-dialog'u açılır (slide-up: 200ms)
  → Ağ bilgileri gösterilir (adı, sinyal gücü, band)
  → Şifre input'a otomatik focus
  → "Otomatik bağlan" checkbox'ı işaretli (varsayılan)
```

### 5.5 — Şifre Girişi

```
Kullanıcı şifre girer → "Bağlan" tıklar
  → Backend: WiFi bağlantısı kurulur
    → wpa_supplicant ile bağlantı
    → DHCP ile IP adresi alınır
    → DNS çözümlenir
  → Başarılı:
    → Modal kapatılır (fade-out: 200ms)
    → Header'daki WiFi ikonu güncellenir (sinyal gücü)
    → "Bağlantı kuruldu" bildirimi (toast, 3s)
  → Başarısız:
    → Hata mesajı göster (şifre altında)
    → Şifre alanı temizlenir
    → Şifre alanı focuslanır
    → "Bağlantı başarısız" bildirimi
```

### 5.6 — "Bağlantıyı Kes" Tıklama

```
Kullanıcı bağlı ağdaki "Bağlantıyı Kes" butonuna tıklar
  → Onay dialog'u gösterilir
  → Onay → Bağlantı kesilir
  → Header'daki WiFi ikonu güncellenir (bağlantı yok)
  → "Bağlantı kesildi" bildirimi
```

### 5.7 — Modal Kapatma

```
Kullanıcı backdrop'a tıklar veya ✕ butonuna basar
  → Modal kapatılır (fade-out: 150ms)
  → Backdrop blur kaldırılır
  → Hiçbir değişiklik yapılmaz (bağlantı korunur)
```

---

## 6. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (toggle) | ⚠️ ~28px → 32px olmalı |
| Touch target (buton) | ✅ 48px, 56px |
| Touch target (satır) | ✅ 48px |
| Touch target (kapat) | ✅ 44px |
| Focus indicator | ✅ |
| Escape ile kapatma | ✅ |
| ARIA | ✅ `role="dialog"` |
| Screen reader | ⚠️ eksik |

---

## 7. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/F-quickpanel/wifi]] | WiFi screen spec |
| [[screens/F-quickpanel/wifi-connect]] | WiFi connect spec |
| [[screens/00-ascii-art-views]] §10-11 | ASCII art'lar |
| [[01-component-inventory]] C14, C15, C16 | Bileşenler |
| [[screens/_layout-patterns/04-modal]] | Modal pattern |

---

*WiFi Connect Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
