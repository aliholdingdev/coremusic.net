---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/home-dashboard.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page.png"
---

# CoreMusic — Home Dashboard (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                              x:1024    │
│ y:0 ┌─── HEADER (h:60) ────────────────────────────────────────────────────────────────────────────────┐  │
│     │ Logo(120×40)   Ana Sayfa · Keşfet · Albümler · Sanatçılar · Göz At · Geçmiş · Ayarlar · Hakkımızda│  │
│     │                                                    👤 Bayram Ali ▼   🔊  🌐  🔍                  │  │
│ y:60 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:60 ┌─── CONTENT (h:450) ──────────────────────────────────────────────────────────────────────────────┐  │
│     │                                                                                                    │  │
│     │  ┌─── NOW PLAYING (42%, w:430) ──────┐  ┌─── WIDGETS (58%, w:594) ──────────────────────────────┐ │  │
│     │  │                                    │  │                                                        │ │  │
│     │  │  🎵 Album Art (120×120)            │  │  ┌─── Widget Row 1 (h:100) ────────────────────────┐  │ │  │
│     │  │  ┌──────────┐                      │  │  │ 🔵 Hoparlörler    │ 📅 07:00    │ ☁️ Hava      │  │ │  │
│     │  │  │          │  Şarkı: Göksel -    │  │  │    Core Music     │ 5 Haziran   │ Güneşli 13°  │  │ │  │
│     │  │  │  JRŞAT   │  Sevil Neşelenen    │  │  │    Hoparlör       │ 2026        │ İstanbul    │  │ │  │
│     │  │  │  GÜÜR    │                      │  │  └────────────────────────────────────────────────┘  │ │  │
│     │  │  │          │  🎵 Hayat Rüya Gibi │  │                                                        │ │  │
│     │  │  └──────────┘  🎤 Göksel          │  │  ┌─── Widget Row 2 (h:100) ────────────────────────┐  │ │  │
│     │  │                                    │  │  │ 🎵 Kültür페스티벌리 │ ❤️ 💜  │  🔴 YT  │ 🟣 SP │  │ │  │
│     │  │  ──────────────────────────────    │  │  │    2.104 Item     │          │  📺      │ 🎵    │  │ │  │
│     │  │  ⏱ 00:05:00 ━━━━━━━━━━━━━ 100%   │  │  └────────────────────────────────────────────────┘  │ │  │
│     │  │                                    │  │                                                        │ │  │
│     │  │  [⏮] [▶] [⏭]   🔀 🔁            │  │  ┌─── Widget Row 3 (h:100) ────────────────────────┐  │ │  │
│     │  │                                    │  │  │ 📊 İstatistikler  │ 🎵 Popüler  │ 📁 Son      │  │ │  │
│     │  └────────────────────────────────────┘  │  │    156 Şarkı      │  87 Dinlenme│  Eklenenler │  │ │  │
│     │                                          │  │    42 Albüm       │  42 Sanatçı │  1.250 Item │  │ │  │
│     │                                          │  └────────────────────────────────────────────────┘  │ │  │
│     │                                          └────────────────────────────────────────────────────────┘ │  │
│     │                                                                                                    │  │
│     │  ┌─── EN SON DİNLENEN ŞARKILAR (33%, w:340) ─┐ ┌─── PLAYLIST'LER (34%, w:340) ──────────────┐  │  │
│     │  │ 🎵 Göksel - Sevil Neşelenen   00:05:00    │ │ 🎵 İLK-10 Listesi       00:55:22          │  │  │
│     │  │ 🎵 Göksel - Kabahat Sensin    00:05:00    │ │ 🎵 Haftalık MIX         00:55:22          │  │  │
│     │  │ 🎵 Gangsta - Çubuklar          00:07:19    │ │ 🎵 Ruh Haline Göre Mix  00:55:22          │  │  │
│     │  │ 🎵 Keyifli Enstrümantal        00:05:13    │ │ 🎵 Sabah Pop Listesi    00:55:16          │  │  │
│     │  │ 🎵 Erkin Koray - Fantastik     00:10:00    │ │ ▶️ Playlist listesini göster               │  │  │
│     │  └─────────────────────────────────────────────┘ └─────────────────────────────────────────────┘  │  │
│     │                                                                                                    │  │
│     └────────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│ y:510 ┌─── FOOTER PLAYER (h:90) ───────────────────────────────────────────────────────────────────────┐  │
│     │ 🎵 Şarkı Adı: Göksel - Sevil Neşelenen    [⏮] [▶] [⏹] [⏭]    🔊 ━━━━━━━━━━━━━━━━━━━━━━━━ % 100 │  │
│     │ 💿 Albüm: Hayat Rüya Gibi                  ● ● ● ● ●                                              │  │
│     │ 🎤 Sanatçı: Göksel                         ⏱ 00:00:00 / 00:05:00  |  Bit rate: 350 kbps          │  │
│ y:600 └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama | Piksel |
|------------|----------|--------|
| `.home-header` | Header container | h:60, bg: glass |
| `.home-header__logo` | Core Music logosu | 120×40 |
| `.home-header__nav` | Navigasyon linkleri | gap:24px |
| `.home-header__user` | Kullanıcı avatar + dropdown | 40×40 avatar |
| `.home-content` | Ana içerik alanı | h:450, y:60-510 |
| `.home-content__now-playing` | Sol panel: Now Playing | w:42%, glass |
| `.home-content__widgets` | Sağ panel: Widget'lar | w:58% |
| `.home-now-playing__art` | Album art görseli | 80×80, radius:8px |
| `.home-now-playing__info` | Şarkı, albüm, sanatçı bilgisi | font:14px/500 |
| `.home-now-playing__progress` | İlerleme çubuğu | h:4px, pembe gradient |
| `.home-footer` | Footer player | h:90, bg: glass |
| `.home-footer__info` | Şarkı bilgisi | sol taraf |
| `.home-footer__controls` | Oynatma butonları (⏮ ▶ ⏹ ⏭) | orta |
| `.home-footer__volume` | Ses kontrolü | sağ taraf |

---

## 3. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | Pembe vurgu, progress bar, butonlar |
| `--cm-bg-primary` | #0a0a0f | Koyu arka plan |
| `--cm-bg-glass` | rgba(255,255,255,0.15) | Cam efekti |
| `--cm-blur` | blur(20px) | Backdrop filter |
| `--cm-radius-sm` | 8px | Küçük radius |
| `--cm-radius-md` | 12px | Orta radius |
| `--cm-radius-lg` | 16px | Büyük radius |
| `--cm-spacing-xs` | 4px | En küçük boşluk |
| `--cm-spacing-sm` | 8px | Küçük boşluk |
| `--cm-spacing-md` | 16px | Orta boşluk |
| `--cm-spacing-lg` | 24px | Büyük boşluk |
| `--cm-spacing-xl` | 32px | En büyük boşluk |
| `--cm-text-primary` | #ffffff | Ana metin |
| `--cm-text-secondary` | #b0b0c0 | İkincil metin |
| `--cm-font-size-xs` | 10px | Metadata |
| `--cm-font-size-sm` | 12px | Küçük metin |
| `--cm-font-size-base` | 14px | Gövde metni |
| `--cm-font-size-lg` | 16px | Başlık |
| `--cm-font-size-xl` | 20px | Büyük başlık |
| `--cm-font-size-2xl` | 24px | Ana başlık |

---

## 4. Touch Target

| Cihaz | Min Touch | Not |
|-------|-----------|-----|
| T08 RPi5 7" | 48×48px | Tüm butonlar ve linkler |
| T01 Phone | 48×48px | Minimum |
| T25 TV | 80×80px | D-pad navigation |
| T34 Console | 80×80px | Gamepad navigation |

---

## 5. WCAG Uyumu

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Kontrast 4.5:1 | ✅ | Beyaz metin koyu arka plan üzerinde |
| Kontrast 3:1 | ✅ | İkincil metin (#b0b0c0) koyu arka plan |
| Keyboard Tab Order | ✅ | Logo → Nav → Content → Footer |
| Focus Visible | ✅ | `:focus-visible` outline (2px solid #ff4fd8) |
| Screen Reader | ✅ | ARIA labels: `aria-label="Now Playing"`, `aria-label="Oynat"` |
| Touch Target | ✅ | Min 48×48px tüm interaktif elemanlar |
| Alt Text | ✅ | Album art için `alt="Şarkı adı - Sanatçı"` |
| Semantic HTML | ✅ | `<header>`, `<main>`, `<footer>`, `<nav>` |

---

## 6. Glassmorphism Stili

```css
/* ═══ Glass Panel ═══ */
.home-content__now-playing {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 16px;
  padding: 24px;
}

/* ═══ Widget Card ═══ */
.home-widget {
  background: rgba(255, 255, 255, 0.10);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 12px;
  padding: 16px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.home-widget:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 32px rgba(255, 79, 216, 0.15);
}

/* ═══ Progress Bar ═══ */
.home-now-playing__progress {
  height: 4px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 2px;
  overflow: hidden;
}

.home-now-playing__progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #ff4fd8, #a855f7);
  border-radius: 2px;
  transition: width 0.3s ease;
}

/* ═══ Footer Player ═══ */
.home-footer {
  background: rgba(10, 10, 15, 0.85);
  backdrop-filter: blur(20px);
  border-top: 1px solid rgba(255, 79, 216, 0.3);
}

/* ═══ Button Gradient ═══ */
.home-now-playing__play-btn {
  background: linear-gradient(135deg, #ff4fd8, #a855f7);
  border: none;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  color: white;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.home-now-playing__play-btn:hover {
  transform: scale(1.1);
  box-shadow: 0 0 20px rgba(255, 79, 216, 0.4);
}
```

---

## 7. PNG Referansı

| PNG | Viewport | Dosya Yolu |
|-----|----------|------------|
| `Linux 1024 - Home Page.png` | 1024×600 | `.ai/.png/home-1024/` |

---

## 8. Responsive Davranış

| Tier | Viewport | Layout Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tek sütun, bottom tab nav |
| T06-T07 (Tablet) | 768-1024px | 2 sütun, sidebar yok |
| T08 (Embedded) | 1024×600 | Split 42/58, sidebar yok |
| T12-T16 (Laptop) | 1025-2560px | 3 sütun, sidebar var |
| T17-T24 (Desktop) | 2561-3840px | 3-4 sütun, sidebar var |
| T25-T28 (TV) | ≥3840px | 4 sütun, D-pad nav |

---

## 9. State Durumları

| Durum | Görsel Değişiklik |
|-------|-------------------|
| Default | Glass panel, pembe vurgu |
| Hover | translateY(-2px), box-shadow |
| Active | Scale(0.98), darkened bg |
| Disabled | opacity:0.5, cursor:not-allowed |
| Loading | Skeleton animation (shimmer) |
| Playing | Progress bar animasyonu, equalizer ikonu |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
