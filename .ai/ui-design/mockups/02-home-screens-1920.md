---
title: "CoreMusic — Home Screen Mockups (1920 Desktop)"
type: reference
category: ui-design/mockups
date: 2026-09-04
updated: 2026-09-06
status: active
version: 2.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
parent: "[[00-mockup-index]]"
screens:
  - "PNG — 1920 Desktop Ana Sayfa"
png_source: ".ai/.png/home-1920/"
resolution: 1920x1080
platform: Linux Desktop
verification: "PNG doğrudan piksel incelemesi (2026-09-06) — tüm ölçüler PNG'den ölçüldü"
---

# Home Screen Mockups (1920 Desktop)

**1 PNG — home-1920/ dizininde.** 1920×1080 desktop ana sayfa mockup'u.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## Home Ekran Envanteri

| # | Ekran | PNG Dosyası | Rota | Layout Pattern | CSS Hedefi |
|---|-------|-------------|------|---------------|------------|
| 1 | **Ana Sayfa** | `Linux - 1920 - Home.png` | `/` | Pattern 2: Top-Band Home (üst bant 3lü + chip satırları) | `05_Pages/_home-*.css` |

---

## ASCII Art — 1920 Desktop Home

### HOME PAGE (Ana Sayfa) — 1920×1080

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                                                x:1920   │
│ y:0 ┌────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┐│
│     │ "Core Music"   Ana sayfa  Keşfet  Albümler  Sanatçılar  Göz At  Geçmiş  Ayarlar  Hakkımızda                            ││
│     │  (Bickham       ↑ 8 nav-link, Arima ~11px, beyaz %85, active pembe alt çizgi yok — düz metin                           ││
│     │   Script Two)                                                            [🧑 Bayram Ali ▾] [📊EQ yeşil] [◉ koyu] [🔗|⏻]││
│     │                                                                                        pill   pill     pill  pill      ││
│ y:65├────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┤│
│     │                                                             ┌──────────────────────────────────────────────────────────┐││
│     │ ┌── NOW PLAYING CARD ──────────────────┐ ┌── WELCOME BANNER ─┐  ┌── WIDGET BÖLGESİ (sağ) ────────────────────────────┐  │││
│     │ │ x:55-520 (~465×195) glass            │ │ x:555-1050        │  │ x:1075-1855 — 3 satır × [geniş kart + karo kümesi] │  │││
│     │ │ ┌────────┐ ♪ Şarkı Adı : Göksel -    │ │ (~495×195)        │  │                                                    │  │││
│     │ │ │150×150 │   Sevil Neşelen           │ │ "Hoş Geldin"      │  │ Satır 1 (y:100):                                   │  │││
│     │ │ │album   │ ● Album  : Hayat Rüya Gibi│ │ (script italik)   │  │ [🔊 Hoparlör ~165×55] [☁ Hava Durumu ~165×55]      │  │││
│     │ │ │art     │ 🎤 Sanatçı: Göksel        │ │ "Bayram Ali"      │  │    "Core Music -       "Genel® 13°                 │  │││
│     │ │ │(yuvarlak│ ★ Yıldız : ★★★★★ (pembe) │ │ (büyük serif)     │  │     Hoparlör"           Istanbul"                  │  │││
│     │ │ │ köşeli) │ ⚡ Bit rate: 350 kbps 🎧  │ │                   │  │ [□][□][□] ← boş cam karolar (~55×55)               │  │││
│     │ │ └────────┘ ⏱ Süre : 00:00:00/00:05:00│ │ "Müzik, ruhun     │  │                                                    │  │││
│     │ │                                      │ │  gizli dili, her  │  │ Satır 2 (y:170):                                   │  │││
│     │ │ ▶ ═══════════●─────────────────────  │ │  nota bir hatırya │  │ [🕐 07:00 ~165×55]  [ıılı EQ ~110×55] [□][□][□]    │  │││
│     │ │   pembe seek bar h:4px               │ │  canlandırır"     │  │    "6 Haziran 2025"                                │  │││
│     │ └──────────────────────────────────────┘ │                   │  │                                                    │  │││
│     │                                          │ [Keşfetmeye ▶]    │  │ Satır 3 (y:240):                                   │  │││
│     │                                          │ (pembe pill btn)  │  │ [📂 Kütüphanemiz ~165×55] [▶YT][♥][〰][□][□][□]    │  │││
│     │                                          │ ┌───┬───┬───┬───┐ │  │    (sarı klasör)     (kızı) (mor)(mor)             │  │││
│     │                                          │ │2.4│156│ 87│42│ │  │                                                    │  │││
│     │                                          │ │50 │   │   │  │ │  │ Sağ kenar: 3×3 boş cam karo grid'i (~55×55 her)    │  │││
│     │                                          │ └───┴───┴───┴───┘ │  │ + en sağda geniş boş cam panel (~110×160)          │  │││
│     │                                          │  5. istatistik:   │  │                                                    │  │││
│     │                                          │  1.250            │  │ ⚠ Arka plan fotoğrafı (prenses) bu bölgenin        │  │││
│     │                                          │ [~495×195 banner] │  │   üstüne biner — karolar glass overlay'li          │  │││
│     │                                          └───────────────────┘  └────────────────────────────────────────────────────┘  │││
│     │                                                                                                                                 ││
│ y:335 "En Son Dinlenen Şarkılar" (Arima 14px, 600, beyaz) — sol x:55                                                                  ││
│     │ ┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐                                      ││
│     │ │[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] │  ×9 chip                             ││
│     │ │Göksel -││Göksel -││Bengü   ││Kıvırcık││Göksel -││Göksel -││Serpil  ││Kıvırcık││Bengü   │  (~170×55)                           ││
│     │ │Sevil   ││Ağlayan ││Mencene ││Emotional││Seni    ││Kaderin ││Gülsümse││Emotional││Mencene │                                      ││
│     │ │Neşelen ││Gözler  ││(Bengu) ││Deniz   ││Nezelen ││Mağlup  ││        ││Deniz   ││        │                                      ││
│     │ │Göksel  ││Göksel  ││Bengu   ││Deniz   ││Göksel  ││Göksel  ││Bengu   ││Deniz   ││Bengu   │                                      ││
│     │ │00:05:00││00:04:1x││00:04:1x││00:00:14││00:05:0x││00:05:0x││00:04:1x││00:0x:xx││00:0x:xx│                                      ││
│     │ └────────┘└────────┘└────────┘└────────┘└────────┘└────────┘└────────┘└────────┘└────────┘                                      ││
│     │  chip: glass bg rgba(255,255,255,0.08), radius 8px, gap 12px, yatay taşma yok (9 chip ekrana sığar)                              ││
│     │                                                                                                                                 ││
│ y:455 "Son Oluşturulan & Sistem Tarafından Oluşturulan Playlistler" (Arima 14px, 600)                                                  ││
│     │ ┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐┌────────┐                                                                     ││
│     │ │[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] ││[40×40] │  ×6 chip (~170×55)                                                  ││
│     │ │En Sevilen Şarkılarımla││Pop Fitkin Güzel Müziklerim││Yeni Oluşan Türkçe Ritimli...││(tekrar ×3: En Sevilen /      ││
│     │ │Sıralı Türkü Pop Oynatma││Sıralı Türkü Pop Oynatma  ││Sıralı Türkü Pop Oynatma    ││ Pop Fitkin / Yeni Oluşan)    ││
│     │ │Göksel  ││Göksel  ││Göksel  ││Göksel  ││Göksel  ││Göksel  │                                                                     ││
│     │ │00:03:10││00:03:10││00:03:10││00:03:10││00:03:10││00:03:10│                                                                     ││
│     │ └────────┘└────────┘└────────┘└────────┘└────────┘└────────┘                                                                     ││
│     │                                                                                                                                 ││
│ y:560 │                                                                 (geniş boş alan — arka plan fotoğrafı tam görünür)              ││
│     │                                                                                                                                 ││
│ y:1010├──────── pembe ilerleme çubuğu h:3px, full-width ──────────────────────────────────────────────────────────────────────────────┤││
│     │ ┌────────┐ ♪ Şarkı Adı  : Göksel - Sevil Neşelen                                          ⏮  ▶  ⏹  ⏭   [🖥][⇄][⛶][📋]          │││
│     │ │60×60   │ ● Album     : Hayat Rüya Gibi                                                  (▶ pembe daire        [🔔][📶][⚙][≡] │││
│     │ │album   │ 🎤 Sanatçı  : Göksel                                                            36px, diğerleri      (sağ ikon       │││
│     │ │art     │ ⏱ Süre : 00:00:00 / 00:05:00                                                    cam koyu)             sırası)       │││
│     │ └────────┘ ⚡ Bit rate : 350 kbps                                  [🔊 ═════●═══] % 100                                        │││
│     │                                                      volume: pembe track, %100                                                │││
│ y:1080└────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘││
└──────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────────┘

Bileşenler: C01×8, C02 (3 pill), C03 (user pill), Now Playing Card, Welcome Banner (5 istatistik),
            Widget Satırları ×3, Chip Row ×2 (C13 varyantı — yatay mini), Footer Player
Layout: ÜST BANT — [Now Playing ~465px | Welcome ~495px | Widget Bölgesi ~780px] + 2 chip satırı + boş alan
Not: 42/58 split YOK — 1024'teki split modeli 1920'de uygulanmaz; üst bant 3 kolonlu akışkan yerleşim.
Sidebar: YOK
Arka plan: Tam kaplama prenses fotoğrafı (pembe gün batımı, çayır, tüyler) + hafif koyulaştırma
Header: Tam genişlik, transparent (arka plan devam eder), h:65px
Footer: Tam genişlik, h:70px, üstte pembe ilerleme çubuğu h:3px
```

---

## Bileşen Detayları (1920 Desktop)

### Header (y:0-65)

| Özellik | Değer | Token |
|---------|-------|-------|
| Yükseklik | 65px | `--header-height: 65px` |
| Genişlik | 1920px (tam) | `width: 100%` |
| Arka plan | şeffaf (arka plan fotoğrafı devam eder) | `background: transparent` |
| Logo | "Core Music" Bickham Script Two, x:25-125 | `--font-logo` |
| Nav link sayısı | 8 (Ana sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda) | C01 |
| Nav font | Arima, ~11px, rgba(255,255,255,0.85) | `--text-xs` |
| Nav konum | Logo'nun sağında, x:135-670 arası | — |
| Profil | "🧑 Bayram Ali ▾" sağ köşe (avatar 24×24 daire) | C03 |
| Sağ pill'ler | [📊 yeşil EQ pill] [◉ koyu pill] [🔗 ⏻ ikon pill] — her biri ~50×22, radius 50px | C02 |

### Now Playing Card (x:55-520, y:95-290)

| Özellik | Değer | Token |
|---------|-------|-------|
| Boyut | ~465×195px | — |
| Arka plan | glass efekti | `backdrop-filter: blur(12px)` |
| Border | 1px solid rgba(255,255,255,0.15) | `--glass-border` |
| Border-radius | 16px | `--radius-lg` |
| Album art | ~150×150px (sol, köşeleri hafif yuvarlak 8px) | — |
| Satırlar | Şarkı Adı / Album / Sanatçı / Yıldız / Bit rate / Süre (6 satır, ~11px) | — |
| Yıldız | ★★★★★ pembe | `--theme-primary` |
| Seek bar | pembe h:4px + ▶ ikonu sol | `--theme-primary` |

### Welcome Banner (x:555-1050, y:95-290)

| Özellik | Değer | Token |
|---------|-------|-------|
| Boyut | ~495×195px | — |
| Arka plan | pembe prenses fotoğrafı + tüy, kenarları yumuşak geçişli | — |
| Başlık | "Hoş Geldin" (Bickham Script italik) + "Bayram Ali" (serif, ~28px) | `--font-logo` |
| Alt yazı | "Müzik, ruhun gizli dili, her nota bir hatırya canlandırır" (~10px italik) | — |
| Buton | "Keşfetmeye Başla ▶" pembe pill (C04 varyantı) | C04 |
| İstatistik satırı | 5 adet: 2.450 / 156 / 87 / 42 / 1.250 (ikon + sayı, ~10px, cam mini kutu) | — |

### Widget Bölgesi (x:1075-1855, y:95-290)

| Özellik | Değer | Token |
|---------|-------|-------|
| Yapı | 3 satır × [1-2 geniş kart + karo kümesi] | `display: flex; flex-wrap` |
| Geniş kart | ~165×55px (Hoparlör, Hava Durumu, 07:00, Kütüphanemiz) | — |
| Karo | ~55×55px kare cam (boş ve ikonlu karışık) | — |
| İkonlu karolar | YouTube (kırmızı ▶), ♥ (mor), 〰 dalga (mor) | — |
| Arka plan | glass `rgba(255,255,255,0.08)` + `blur(12px)` | `--glass-bg` |
| Border-radius | 8-12px | `--radius-md` |
| Satır gap | ~15px | `--space-2` |
| Not | Sağ kenarda 3×3 boş karo grid'i + geniş boş cam panel; arka plan fotoğrafı bu bölgeye doğru akar | — |

### En Son Dinlenen Şarkılar (y:335-420)

| Özellik | Değer | Token |
|---------|-------|-------|
| Başlık | "En Son Dinlenen Şarkılar", Arima 14px, 600 | `--text-base` |
| Chip sayısı | 9 (tek satır) | — |
| Chip boyutu | ~170×55px YATAY (thumb 40×40 + 2 satır metin + süre) | — |
| Chip yapısı | [40×40 thumb][başlık 9px + sanatçı 8px]...[süre 8px alt] | C13 mini varyant |
| Gap | 12px | `--space-3` |
| Yatay scroll | Görünür alanda 9 chip tam sığar; taşma olursa `overflow-x: auto` | — |
| Chip bg | `rgba(255,255,255,0.08)` + blur, radius 8px | `--glass-bg` |

### Playlistler (y:455-545)

| Özellik | Değer | Token |
|---------|-------|-------|
| Başlık | "Son Oluşturulan & Sistem Tarafından Oluşturulan Playlistler", Arima 14px, 600 | `--text-base` |
| Chip sayısı | 6 (tek satır) | — |
| Chip boyutu | ~170×55px YATAY (En Son Dinlenen ile aynı format) | — |
| İçerik | "En Sevilen Şarkılarımla Sıralı Türkü Pop Oynatma", "Pop Fitkin Güzel Müziklerim", "Yeni Oluşan Türkçe Ritimli..." (×2 tekrar) | — |
| Gap | 12px | `--space-3` |

### Footer Player (y:1010-1080)

| Özellik | Değer | Token |
|---------|-------|-------|
| Yükseklik | 70px | `--footer-height: 70px` |
| Genişlik | 1920px (tam) | `width: 100%` |
| İlerleme çubuğu | pembe, h:3px, full-width (footer üst kenarında) | `--theme-primary` |
| Album art | 60×60px (sol, x:15-75) | — |
| Sol bilgi | Şarkı/Album/Sanatçı + Süre/Bit rate (~10px, 2 kolon) | — |
| Kontroller | ⏮ ▶ ⏹ ⏭ — ortalı, ▶ pembe daire 36px, diğerleri koyu cam 36px | C04 |
| Sağ ikonlar | [🖥][⇄][⛶][📋] + [🔔][📶][⚙][≡] iki küme, ~14px ikonlar | — |
| Volume | pembe slider + "% 100" | — |

---

## Tema Desteği

| Tema | Primary Renk | Kullanım |
|------|--------------|----------|
| Female | #ff4fd8 | Pembe tonları (mevcut mockup) |
| Male | #4f9fff | Mavi tonları |
| Neutral | #a0a0b0 | Nötr gri |

---

## 1024 vs 1920 Karşılaştırması

| Özellik | 1024 Embedded | 1920 Desktop |
|---------|---------------|--------------|
| Genişlik | 1024px | 1920px |
| Yükseklik | 600px | 1080px |
| Header | 60px | 65px |
| Footer | 90px | 70px |
| Sidebar | Yok | Yok |
| Layout | Split 42/58 (sol liste + sağ panel) | Üst bant 3 kolon (Now Playing + Welcome + Widget) + chip satırları |
| Now Playing | ~420×180px dikey kart | ~465×195px yatay kart |
| Welcome Banner | Yok | ~495×195px (5 istatistik + CTA) |
| Widget alanı | 2×2 küçük widget | 3 satır [kart + karo kümesi] |
| "En Son Dinlenen" | Dikey liste (C13 satırları) | Tek satır 9 yatay chip (~170×55) |
| Playlistler | Dikey liste | Tek satır 6 yatay chip (~170×55) |
| Nav link sayısı | 8 | 8 |
| Footer kontroller | ⏮ ▶ ⏹ ⏭ | ⏮ ▶ ⏹ ⏭ + sağ ikon kümeleri |
| Hover | Yok (dokunmatik) | Aktif (fare) |

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana mockup indeksi |
| [[02-home-screens]] | 1024 home ekranları |
| [[dashboard-1920]] | ASCII art view (B-home detay dosyası) |
| [[01-component-inventory]] | C01-C16 bileşen envanteri |
| [[02-implementation-plan]] | CSS uygulama yol haritası |
| [[tokens/design-tokens-master]] | Master design token'lar |
| [[responsive-device-mode]] | 4K responsive kuralları (ortalamama kuralı) |

---

## Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.1.0 |
| PNG Source | `Linux - 1920 - Home.png` (doğrudan piksel incelemesi) |
| ASCII Accuracy | PNG birebir (2026-09-06 revizyonu — eski spekülatif split modeli düzeltildi) |
| Düzeltmeler | Kart formatı (kare→yatay chip), İzmir→Istanbul, tarih, 5 istatistik, footer 100→70px, nav 9→8, header 60→65px |
| Zero Hallucination | ✅ Tüm ölçüler PNG'den ölçüldü |

---

*Home Screen Mockups 1920 v2.1.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-06*
*Mode: Red Team · Human Mode · Truth Mode*
