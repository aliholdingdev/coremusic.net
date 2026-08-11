---
title: CoreMusic — Settings Flow: General Settings (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/00-ascii-art-views]] §1
  - [[01-component-inventory]] C15, C06
  - [[ADR-044-dynamic-user-theme-engine]]
---

# Settings Flow: General Settings — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    GENERAL SETTINGS AKIŞI                                    │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Ayarlar      │ →  │ Sol Menü     │ →  │ Kategori     │                  │
│  │ Sayfası      │    │ (4 bölüm)    │    │ Yüklenir     │                  │
│  │ (/settings)  │    │              │    │              │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                    ┌─────────┼─────────┐                   │
│                                    │         │         │                   │
│                               ┌────▼────┐ ┌──▼──┐ ┌───▼────┐             │
│                               │ Genel   │ │Ses  │ │Görünüm │             │
│                               └────┬────┘ └──┬──┘ └───┬────┘             │
│                                    │         │         │                   │
│                                    └─────────┼─────────┘                   │
│                                              │                             │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Değişiklikler│              │
│                                              │ Anında       │              │
│                                              │ Uygulanır    │              │
│                                              └─────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. AYARLAR SAYFASI YAPISI

### 2.1 — Sol Menü

```
┌── SOL MENÜ (~200px) ──┐  ┌── SAĞ İÇERİK (~800px) ──────────────────────┐
│                         │  │                                               │
│  [⚙] Ayarlar            │  │  Genel Ayarlar                               │
│                         │  │                                               │
│  ● Genel                │  │  Dil                                          │
│  ○ Ses                  │  │  ┌─────────────────────────────────────┐     │
│  ○ Görünüm              │  │  │ Türkçe ▾                              │     │
│  ○ Hakkımızda           │  │  └─────────────────────────────────────┘     │
│                         │  │                                               │
│                         │  │  Tema                                         │
│                         │  │  ┌─────────────────────────────────────┐     │
│                         │  │  │ Sistem ▾                             │     │
│                         │  │  └─────────────────────────────────────┘     │
│                         │  │                                               │
│                         │  │  Başlangıç sayfası                          │
│                         │  │  ┌─────────────────────────────────────┐     │
│                         │  │  │ Ana Sayfa ▾                          │     │
│                         │  │  └─────────────────────────────────────┘     │
│                         │  │                                               │
│                         │  │  Otomatik güncelleme  [━━━━━━○] (C15)       │
│                         │  │                                               │
└─────────────────────────┘  └───────────────────────────────────────────────┘
```

### 2.2 — Kategori Başlıkları

| Kategori | İkon | İçerik |
|----------|------|--------|
| Genel | ⚙ | Dil, tema, başlangıç, güncelleme |
| Ses | 🔊 | Çıkış, bit, örneklem, buffer, crossfade |
| Görünüm | 🎨 | Mod, kapak boyutu, animasyonlar, glass |
| Hakkımızda | ℹ | Versiyon, lisans, ekip |

---

## 3. GENEL AYARLAR DETAYI

### 3.1 — Dil

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | Türkçe |
| Seçenekler | Türkçe, English, Deutsch, Français, Español |
| Etki | Tüm arayüz dili değişir |
| Backend | `POST /api/settings/language` |
| Cookie | `language=tr` (365 gün) |

### 3.2 — Tema

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | Sistem |
| Seçenekler | Sistem, Aydınlık, Karanlık |
| Etki | CSS custom properties güncellenir |
| Backend | `POST /api/settings/theme` |
| Cookie | `theme=dark` (365 gün) |

### 3.3 — Başlangıç Sayfası

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | Ana Sayfa |
| Seçenekler | Ana Sayfa, Albümler, Sanatçılar, Göz At |
| Etki | İlk yüklenecek sayfa |
| Backend | `POST /api/settings/homepage` |
| Cookie | `homepage=albums` |

### 3.4 — Otomatik Güncelleme

| Özellik | Değer |
|---------|-------|
| Tip | Toggle (C15) |
| Varsayılan | Açık |
| Etki | Arka planda güncelleme kontrolü |
| Backend | `POST /api/settings/auto-update` |
| Cookie | `auto-update=1` |

---

## 4. SES AYARLARI DETAYI

### 4.1 — Varsayılan Çıkış

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | Sistem |
| Seçenekler | Sistem, HDMI, USB, Bluetooth, 3.5mm jack |
| Etki | Ses çıkış cihazı değişir |

### 4.2 — Bit Derinliği

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | 32-bit |
| Seçenekler | 16-bit, 24-bit, 32-bit |
| Etki | Ses kalitesi / performans dengesi |

### 4.3 — Örnekleme Hızı

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | 48kHz |
| Seçenekler | 44.1kHz, 48kHz, 96kHz, 192kHz |
| Etki | Ses kalitesi / CPU kullanımı |

### 4.4 — Buffer Boyutu

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | 512 sample |
| Seçenekler | 64, 128, 256, 512, 1024 |
| Etki | Gecikme / kararlılık dengesi |

| Buffer | Gecikme (48kHz) | Kararlılık |
|--------|----------------|-----------|
| 64 | ~1.3ms | Düşük |
| 128 | ~2.7ms | Orta |
| 256 | ~5.3ms | İyi |
| 512 | ~10.7ms | İyi (varsayılan) |
| 1024 | ~21.3ms | Yüksek |

### 4.5 — Crossfade

| Özellik | Değer |
|---------|-------|
| Tip | Slider (0-10s) |
| Varsayılan | 3s |
| Etki | Şarkılar arası geçiş süresi |

---

## 5. GÖRÜNÜM AYARLARI DETAYI

### 5.1 — Görünüm Modu

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | Home |
| Seçenekler | Home, Pro, Studio |
| Etki | CSS dosyası değişir (v-home.css, v-pro.css, v-studio.css) |
| Backend | `POST /api/settings/viewmode` |
| Cookie | `view-mode=home` |

### 5.2 — Albüm Kapak Boyutu

| Özellik | Değer |
|---------|-------|
| Tip | Select dropdown |
| Varsayılan | Orta |
| Seçenekler | Küçük (100×100), Orta (140×140), Büyük (200×200) |
| Etki | Kart grid'indeki thumb boyutu |

### 5.3 — Animasyonlar

| Özellik | Değer |
|---------|-------|
| Tip | Toggle (C15) |
| Varsayılan | Açık |
| Etki | Geçiş animasyonları açma/kapama |
| CSS | `prefers-reduced-motion` desteği |

### 5.4 — Glass Efekti

| Özellik | Değer |
|---------|-------|
| Tip | Toggle (C15) |
| Varsayılan | Açık |
| Etki | `backdrop-filter` açma/kapama |
| Performans | RPi5'te kapatılabilir (CPU tasarrufu) |

---

## 6. DAVRANIŞ DETAYLARI

### 6.1 — Değişiklik Uygulama

```
Kullanıcı herhangi bir ayarı değiştirir
  → JS: Değişiklik anında uygulanır (sayfa yenileme YOK)
  → JS: Backend'e bildirilir (async)
    → POST /api/settings/{key}
    → Request: { value: newValue }
  → JS: Cookie'ye kaydedilir
  → JS: Kullanıcıya bildirim gösterilir ("Ayarlar kaydedildi" toast, 2s)
```

### 6.2 — Tema Değişikliği

```
Kullanıcı tema değiştirir
  → JS: CSS custom properties güncellenir
  → JS: `data-theme` attribute güncellenir
  → JS: Tüm sayfa anında değişir (sayfa yenileme yok)
  → JS: Cookie'ye kaydedilir
```

### 6.3 — Dil Değişikliği

```
Kullanıcı dil değiştirir
  → JS: Tüm metin stringleri güncellenir
  → JS: `data-lang` attribute güncellenir
  → JS: Sayfa yenilenir (zorunlu — tüm metinler değişir)
  → Backend: Yeni dil dosyası yüklenir
```

---

## 7. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (select) | ✅ 56px |
| Touch target (toggle) | ⚠️ ~28px → 32px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |
| ARIA | ⚠️ eksik |

---

## 8. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[ADR-044-dynamic-user-theme-engine]] | Tema motoru |
| [[01-component-inventory]] C15, C06 | Bileşenler |
| [[flow/settings/01-wifi-connect]] | WiFi ayarları |
| [[flow/settings/03-equalizer]] | EQ ayarları |
| [[flow/navigation/01-spa-routing]] | SPA routing |

---

*General Settings Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
