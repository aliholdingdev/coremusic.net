---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Screen Prompt T6 Desktop FHD"
type: prompt
category: ui-design
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
tier: T6-desktop
---

# T6: Desktop FHD Screen Prompt

## AI Code Generation Prompt

### Context

| Özellik | Değer |
|---------|-------|
| Tier | T6-desktop |
| Viewport | 1920px (Full HD) |
| Cihaz | 24" masaüstü monitör, 15.6" FHD laptop |
| PPI | 93-141 |
| Input | Mouse + Klavye |
| Layout Pattern | 3-column |
| Orientation | Landscape |
| Safe Area | Yok |
| CSS Media Query | `@media (min-width: 1920px) and (max-width: 2559px)` |

---

### Required Inputs

| Kural | Değer |
|-------|-------|
| Interactive target min | 44×44px |
| Interactive target rec | 48×48px |
| Spacing | ≥4px |
| Font scale | 1.2× |
| Base size | 19.2px |
| Min font size | 14.4px |
| Max font size | 32px |
| Spacing scale | xs:4 sm:8 md:12 lg:16 xl:24 2xl:32 3xl:48 |
| Border radius | sm:6 md:10 lg:16 xl:24 full:9999 |
| Glass blur | `blur(20px) saturate(180%)` |
| Hover | VAR (150ms ease) |
| Max content | 1440px centered |

### Renk Paleti

```css
:root {
  --bg-primary: #0a0a0f;
  --bg-secondary: #12121a;
  --bg-elevated: #1a1a24;
  --bg-glass: rgba(18, 18, 26, 0.72);
  --text-primary: #f0f0f5;
  --text-secondary: #a0a0b0;
  --text-muted: #606070;
  --accent: #7c5cff;
  --accent-hover: #9070ff;
  --border-subtle: rgba(255,255,255,0.08);
  --border-default: rgba(255,255,255,0.12);
}
```

---

### ASCII Reference

```
y=0 ┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
    │ [PNG] HEADER y0-99                                                                                            │
    │   [PNG] Navbar 993×27 @(12,17) [Figma 2831:13748]: logo "Core Music" 108×29 @(20,20) Respective 20px          │
    │     · linkler (Avalon 11/500, y33-48): x139 Ana Sayfa · x219 Keşfet · x272 Albumler · x342 Sanatcılar        │
    │     · x421 Göz At · x480 Geçmiş · x539 Ayarlar · x604 Hakkımızda  [PNG okuma ✓ = Figma metin ✓]               │
    │   [PNG] Sağ küme: Profile 90×24 @(1649,21) · Wifi/BT @(1744,21) · Battery @(1793,21) · Logout @(1845,21)      │
y=99 ├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG] TOP y99-283 (h184)                                                                                      │
    │   [Figma 2849:21489] Player Info 469×184 @(53,99): album 150×150 @(69,115) · text col x255.556                 │
    │     · Progressbar 246×9 @(256,250) · "Bit rate : 350 kbps" [2849:21479] @(255.6,204.2) [PNG bitmap ✓ kbps]     │
    │   [Figma] Banner 491×184 @(552,99) r8 [PNG: görsel banner]                                                    │
    │   [Figma 2850:21494] Div2 752×184.439 @(1073,99): Hava 172×54.877 @(1266,99) & @(1458,99) · date 109×43        │
    │     @(1073,175.4) · Dosya Yönetici Btn 129×43 @(1073,240) [PNG: "Kütüphanelerim"] · YT/YTMusic/Deezer 42×43    │
    │     @(1219/1278/1337,240) · Equalizer @(1203,175.4)                                                            │
y=283├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG] TITLE1 band y283-369 (h86): başlık "En Son Dinlenen Şarkılar" glyph'leri y324-369  [PNG okuma ✓]         │
    │   → Figma'da bu metne karşılık TEXT düğümü YOK  → ÇELİŞKİ §1-1                                                 │
y=369├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG] ROW1 y369-412 (h43): 10× mini-card 169×43, x=53,237,421,605,789,973,1157,1341,1525,1709 (pitch 184)     │
    │   [PNG görsel+gölge kanıtı ✓] · [Figma'da 1 kart: 2856:22290 @(53,369)]  → ÇELİŞKİ §1-2                        │
y=412├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG] GAP y412-455 (h43) — satır arası boşluk                                                                │
y=455├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG+Figma 2850:21627] TITLE2 y455-500 (h45): "Son Oluşturlan & Sistem Taraından Oluşturlan Playlistler"       │
    │   407×45 @(53,455) — PNG metni Figma metni ile birebir ✓ [PNG okuma ✓]                                         │
y=500├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG] ROW2 y500-543 (h43): 6× mini-card 169×43, x=53,237,421,605,789,973                                       │
    │   [Figma: 6 INSTANCE 2890:7966/7967/7968/7924/7923/7922 @(53/237/421/605/789/973, y500)] ✓ PNG piksel ✓        │
y=543├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG] EMPTY y543-988 (h445) — dekoratif boş zemin (bg + gradient; ort. R190 G138 B143)                         │
y=988├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [PNG+Figma 2831:13859] FOOTER y988-1080 (h92) — Footer INSTANCE 1923×92 @(0,988)                              │
    │   MediaInfo @(91.5,1000.6) · footer Progressbar 1921×4.8 @(0,988.6) · Player Buttons 126×27 @(899,1020.6)      │
    │   · Volume 353×14 @(1533,1025.6) · "Bit rate : 350 kpps" [I2831:13859;1330:24174] @(308.6,1058.4)             │
    │     [PNG bitmap ✓ kpps — üstteki "kbps" ile İÇ tutarsızlık, kaynaklar arası çelişki DEĞİL → not, §7]           │
y=1080└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

> Kaynak: [[screens/T17-monitor-22fhd/home-dashboard]] — L34-L69

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

```css
@media (min-width: 1920px) and (max-width: 2559px) {
  :root {
    --header-h: 70px;
    --footer-h: 104px;
    --sidebar-w: 280px;
    --grid-gap: 16px;
    --font-scale: 1.2;
  }

  .main-content {
    padding-top: var(--header-h);
    padding-bottom: var(--footer-h);
    min-height: 100dvh;
    display: grid;
    grid-template-columns: var(--sidebar-w) 1fr;
    max-width: 1440px;
    margin: 0 auto;
    padding-left: 24px;
    padding-right: 24px;
  }

  .sidebar {
    position: sticky;
    top: var(--header-h);
    height: calc(100vh - var(--header-h) - var(--footer-h));
    overflow-y: auto;
    padding: 16px;
    border-right: 1px solid var(--border-subtle);
  }

  .content-area {
    padding: 24px;
  }

  .home-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--grid-gap);
  }

  .header {
    position: sticky;
    top: 0;
    height: var(--header-h);
    display: flex;
    align-items: center;
    padding: 0 24px;
    max-width: 1440px;
    margin: 0 auto;
    background: var(--bg-secondary);
    border-bottom: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: var(--footer-h);
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 0 24px;
    max-width: 1440px;
    margin: 0 auto;
    background: var(--bg-elevated);
    border-top: 1px solid var(--border-subtle);
    z-index: 1000;
  }

  .footer-player__cover {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-md);
  }

  .interactive:hover {
    background: var(--bg-elevated);
    border-color: var(--border-strong);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    transition: all 150ms ease;
    cursor: pointer;
  }

  .card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: var(--accent);
  }

  .glass {
    background: var(--bg-glass);
    backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--border-subtle);
  }

  *:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
  }
}
```

---

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Primary button kontrastı 3.1:1 ❌ → koyu text `#1a1a2e` veya açık pink `#ff6ee4` düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L110, L120-L123)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] Modal focus trap korunmuş (Tab döngüsü ve içerik odaklaması); Dropdown ok tuşları çalışır (kanıt: 04-accessibility-gaps.md L135-L136, L154-L160)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

### Ekran Promptları

### 3.1 Home

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky top 70px | `--header-h: 70px` |
| Sidebar | 280px, sol | `--sidebar-w: 280px` |
| Content | 3-column grid | `--grid-gap: 16px` |
| Footer Player | Fixed bottom 104px | `--footer-h: 104px` |
| Max Content | 1440px centered | `max-width: 1440px; margin: 0 auto` |

### 3.2 Auth Login

| Bileşen | Konum | Token |
|---------|-------|-------|
| Logo | Center, 96px | `--font-size-2xl: 28.8px` |
| Form | Max-width 480px, centered | `margin: 0 auto` |
| Input | Full-width, 48px | `min-height: 48px` |
| Button | Full-width, 48px | `min-height: 48px` |

### 3.3 Albums

| Bileşen | Konum | Token |
|---------|-------|-------|
| Header | Sticky, 70px | `--header-h: 70px` |
| Sidebar | 280px | `--sidebar-w: 280px` |
| Album Grid | 4 sütun | `grid-template-columns: repeat(4, 1fr)` |

### 3.4 Player

| Bileşen | Konum | Token |
|---------|-------|-------|
| Footer Player | Fixed bottom 104px | `--footer-h: 104px` |
| Cover Art | 80×80px | `--radius-md: 10px` |
| Seek Bar | Full-width | `--accent` |
| Controls | Play 64px, others 48px | `min-width: 44px` |
| Volume | Slider | `display: block` |
| Queue | 280px panel | Sidebar'da |

---

### Yasaklar

| Yasak | Doğru |
|-------|-------|
| `vw/vh` header/footer | `px` + `max-width: 1440px` |
| 5+ sütun grid | Max 4 sütun |
| Font < 14.4px | Min 14.4px (1.2×) |
| Hover olmayan eleman | Tümüne hover |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
