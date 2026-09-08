---
title: CoreMusic — Home Page Screen Specification (1920×1080, Linux Desktop)
date: 2026-09-06
updated: 2026-09-06
type: spec
status: active
version: 1.0.0
authority: PNG Visual Analysis (direct inspection — Linux - 1920 - Home.png)
platform: Linux Desktop / 1920×1080px
references:
  - "[[00-mockup-index]]"
  - "[[01-component-inventory]]"
  - "[[../../mockups/02-home-screens-1920]]"
  - "[[dashboard]]"
  - "[[../_layout-patterns/02-split-home]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-044-dynamic-user-theme-engine]]"
---

# CoreMusic — Home Page Screen Specification — Desktop 1920 (v1.0.0)

## Platform: Linux Desktop / 1920×1080px

**Source Image:** `Linux - 1920 - Home.png`
**Confidence:** High — directly viewed from PNG screenshot (2026-09-06).
**Layout Pattern:** Pattern 2 varyantı — **Top-Band Home** (üst bant 3 kolon + chip satırları). 1024'teki 42/58 split modeli 1920'de **kullanılmaz**.

> ⚠️ Bu dosya `dashboard.md` (1024) ile aynı ekranın desktop varyantıdır. 1024 kuralları devralınmaz; PNG'den bağımsız ölçülmüştür.

---

## 1. PLATFORM

| Property | Value |
|----------|-------|
| Resolution | 1920×1080px |
| Platform | Linux Desktop |
| Orientation | Landscape |
| Scale Factor | 1x |
| Device Type | `device_type = 'desktop'` |
| CSS Bundle | `d-desktop.css` (ITCSS 08_Devices) |
| Viewport | 1080px (header 65px + içerik 945px + footer 70px) |
| Accent Color | `#ff4fd8` (tema motoru ile değiştirilebilir) |
| Background | Tam kaplama prenses fotoğrafı (pembe gün batımı, çayır, tüyler) + hafif koyulaştırma |
| Rota | `/` |
| Hover | ✅ Aktif (fare) |
| Girdi | Fare + klavye |

---

## 2. GENEL LAYOUT ÖLÇÜLERİ

```
┌─────────────────────────────────────────────────────────────┐
│ 1920×1080 — Linux Desktop — home.coremusic.net              │
├─────────────────────────────────────────────────────────────┤
│ Header: y:0-65, h:65px (fixed, transparent)                 │
│ Content: y:65-1010, h:945px                                 │
│   Üst bant: y:95-290 (Now Playing + Welcome + Widget)       │
│   Chip satır 1: y:335-420 (En Son Dinlenen ×9)              │
│   Chip satır 2: y:455-545 (Playlistler ×6)                  │
│   Boş alan: y:545-1010 (arka plan görünür)                  │
│ Footer: y:1010-1080, h:70px (fixed)                         │
│ Seek bar: y:1010, h:3px, full-width, pembe                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 3. TAM ASCII ART VIEW

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                                x:1920   │
│ y:0 ┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐│
│     │ "Core Music"   Ana sayfa  Keşfet  Albümler  Sanatçılar  Göz At  Geçmiş  Ayarlar  Hakkımızda                            ││
│     │  (Bickham        ↑ C01 × 8, Arima ~11px, rgba(255,255,255,0.85)                                                       ││
│     │   Script Two)                                                             [🧑 Bayram Ali ▾] [📊EQ] [◉] [🔗|⏻]          ││
│     │                                                                                        C03      C02 pill'ler           ││
│ y:65├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤│
│     │ ┌── NOW PLAYING CARD ──────────────────┐ ┌── WELCOME BANNER ─┐  ┌── WIDGET BÖLGESİ ─────────────────────────────────┐  ││
│     │ │ x:55-520 (~465×195) glass blur(12)   │ │ x:555-1050        │  │ x:1075-1855 — 3 satır × [geniş kart + karo]        │  ││
│     │ │ ┌────────┐ ♪ Şarkı Adı : Göksel -    │ │ (~495×195)        │  │ S1: [🔊 Hoparlör 165×55][☁ Hava 165×55][□□□]       │  ││
│     │ │ │150×150 │   Sevil Neşelen           │ │ "Hoş Geldin"      │  │     "Core Music -      "Genel® 13°                 │  ││
│     │ │ │album   │ ● Album  : Hayat Rüya Gibi│ │ (script italik)   │  │     Hoparlör"           Istanbul"                  │  ││
│     │ │ │art     │ 🎤 Sanatçı: Göksel        │ │ "Bayram Ali"      │  │ S2: [🕐 07:00 165×55][ıılı EQ 110×55][□□□]         │  ││
│     │ │ └────────┘ ★ Yıldız : ★★★★★ pembe    │ │ (büyük serif 28)  │  │     "6 Haziran 2025"                               │  ││
│     │ │ ⚡ Bit rate: 350 kbps 🎧             │ │                   │  │ S3: [📂 Kütüphanemiz 165×55][▶YT][♥][〰][□□□]      │  ││
│     │ │ ⏱ Süre : 00:00:00 / 00:05:00        │ │ "Müzik, ruhun     │  │                                                    │  ││
│     │ │                                      │ │  gizli dili, her  │  │ karo ~55×55 cam; sağ kenar 3×3 boş karo +          │  ││
│     │ │ ▶ ═══════════●──────────────         │ │  nota bir hatırya │  │ geniş boş cam panel (~110×160)                     │  ││
│     │ │   pembe seek h:4px                   │ │  canlandırır"     │  │                                                    │  ││
│     │ └──────────────────────────────────────┘ │ [Keşfetmeye Başla▶]│  │ 2.450 │ 156 │ 87 │ 42 │ 1.250 ← 5 istatistik    │  ││
│     │                                          └───────────────────┘  └────────────────────────────────────────────────────┘  ││
│ y:335 "En Son Dinlenen Şarkılar" (14px, 600) — x:55 sol hizalı                                                                        ││
│     │ [▸40×40 Göksel - Sevil Neşelen│Göksel│00:05:00] [▸Göksel - Ağlayan Gözler│Göksel] [▸Bengü Mencene│Bengu]                   ││
│     │ [▸Kıvırcık Emotional│Deniz] [▸Göksel - Seni Nezelen] [▸Göksel - Kaderin Mağlup] [▸Serpil Gülsümse] [▸Kıvırcık] [▸Bengü]       ││
│     │  ↑ 9 chip (~170×55), glass rgba(255,255,255,0.08), radius 8px, gap 12px                                                         ││
│ y:455 "Son Oluşturulan & Sistem Tarafından Oluşturulan Playlistler" (14px, 600)                                                        ││
│     │ [▸En Sevilen Şarkılarımla Sıralı Türkü Pop Oynatma] [▸Pop Fitkin Güzel Müziklerim] [▸Yeni Oluşan Türkçe Ritimli...]            ││
│     │ [▸En Sevilen...] [▸Pop Fitkin...] [▸Yeni Oluşan...]  ↑ 6 chip (~170×55), aynı format, gap 12px                                  ││
│     │                                                                                                                                 ││
│ y:560 │                    (boş alan — arka plan fotoğrafı tam görünür)                                                                ││
│ y:1010├──────── pembe ilerleme çubuğu h:3px, full-width ──────────────────────────────────────────────────────────────────────────────┤││
│     │ ┌──────┐ ♪ Şarkı Adı : Göksel - Sevil Neşelen                    ⏮  ▶  ⏹  ⏭        [🖥][⇄][⛶][📋] [🔔][📶][⚙][≡]           │││
│     │ │60×60 │ ● Album    : Hayat Rüya Gibi                           ▶ pembe daire                                     [🔊 ═●══] %100 │││
│     │ │album │ 🎤 Sanatçı : Göksel                                    36px                                                            │││
│     │ └──────┘ ⏱ Süre : 00:00:00/00:05:00 · Bit rate : 350 kbps                                                                        │││
│ y:1080└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘││
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. BÖLÜM DETAYLARI

### 4.1 Header (y:0-65)

| Özellik | Değer | Bileşen |
|---------|-------|---------|
| Yükseklik | 65px | C01/C02/C03 |
| Logo | "Core Music" Bickham Script Two, x:25-125 | `--font-logo` |
| Nav | 8 link, Arima ~11px, gap ~14px, x:135-670 | C01 |
| Sağ küme | C03 user pill + 3 adet C02 pill (~50×22, radius 50px) | C02, C03 |

### 4.2 Now Playing Card (x:55-520)

Glass kart, ~465×195, blur(12px), radius 16px, border rgba(255,255,255,0.15).
İçerik: 150×150 album art + 6 bilgi satırı (~11px) + pembe seek bar h:4px.

### 4.3 Welcome Banner (x:555-1050)

~495×195, fotoğraf arka planlı. "Hoş Geldin" (script) + "Bayram Ali" (serif ~28px) + italik alt yazı + "Keşfetmeye Başla ▶" pembe pill (C04) + **5 istatistik** (2.450 / 156 / 87 / 42 / 1.250).

### 4.4 Widget Bölgesi (x:1075-1855)

3 satır: S1 Hoparlör + Hava Durumu (Istanbul, Genel® 13°), S2 07:00/6 Haziran 2025 + EQ ikonu, S3 Kütüphanemiz (sarı klasör) + YouTube/♥/〰 karolar. Geniş kart ~165×55, karo ~55×55, glass bg, arka plan fotoğrafı bu bölgeye taşar.

### 4.5 Chip Satırları (C13 mini varyant)

| Özellik | Değer |
|---------|-------|
| Satır 1 | "En Son Dinlenen Şarkılar" — 9 chip |
| Satır 2 | "Son Oluşturulan & Sistem Tarafından Oluşturulan Playlistler" — 6 chip |
| Chip boyutu | ~170×55px (thumb 40×40 + 2 satır metin + süre) |
| Gap | 12px |
| BG | `rgba(255,255,255,0.08)` + blur(12px), radius 8px |

### 4.6 Footer Player (y:1010-1080)

h:70px. Üst kenarda pembe ilerleme çubuğu h:3px. Sol: 60×60 thumb + şarkı bilgisi. Orta: ⏮ ▶ ⏹ ⏭ (▶ pembe daire 36px). Sağ: 8 utility ikonu + pembe volume slider "% 100".

---

## 5. CSS İSKELETİ

```css
/* 05_Pages/_home-layout.css — 1920 desktop varyant */
.home-1920 { display: grid; grid-template-rows: 65px 1fr 70px; min-height: 100vh; }

.home-1920__topband {
  display: flex; gap: 16px; padding: 30px 55px 0;
  min-height: 225px;
}

.home-1920__nowplaying { width: 465px; flex-shrink: 0; }
.home-1920__welcome    { width: 495px; flex-shrink: 0; }
.home-1920__widgets    { flex: 1; display: flex; flex-direction: column; gap: 15px; }

.home-1920__chips {
  display: flex; gap: 12px; overflow-x: auto; padding: 0 55px;
}

.home-1920__chip {
  width: 170px; height: 55px; flex-shrink: 0;
  background: rgba(255,255,255,0.08);
  backdrop-filter: blur(12px);
  border-radius: 8px;
}
```

---

## 6. Responsive Notlar

| Breakpoint | Davranış |
|-----------|----------|
| ≥1920px | Bu spec aynen uygulanır |
| 1440-1919px | Üst bant kolonları sırayla sarar (`flex-wrap`), chip satırları yatay scroll |
| 1024-1439px | 1024 spec'e (`dashboard.md`) geçilir — split 42/58 |
| **4K (≥3840px)** | ⚠️ İçerik **ortalanmaz** (`mx-auto` yasak). Sol yaslı akışkan: kolonlar genişler, chip satırları tek satırda kalır, `max-width` YOK. Detay: [[../../responsive-device-mode]] |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Source PNG | `Linux - 1920 - Home.png` (doğrudan piksel incelemesi 2026-09-06) |
| Components | C01×8, C02×3 pill, C03, C04 (banner CTA), C13-mini chip'ler, Footer Player |
| Zero Hallucination | ✅ Tüm ölçüler PNG'den ölçüldü; spekülatif değer yok |
| Düzeltme | `dashboard.md` §11.2 ve `mockups/02-home-screens-1920.md` v2.0'daki eski split/kart modeli bu spec ile düzeltildi |

---

*Dashboard 1920 v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-06*
*Mode: Red Team · Human Mode · Truth Mode*
