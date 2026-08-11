---
type: plan
category: ui-design
title: "CoreMusic — Implementation Plan (15-Step CSS, v3.1.0)"
date: 2026-08-11
updated: 2026-08-11
status: active
version: 3.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[00-ascii-art-views]]
  - [[decisions/accepted/ADR-001-vanilla-js-itcss]]
  - [[decisions/accepted/ADR-044-dynamic-user-theme-engine]]
---

# CoreMusic — Implementation Plan (v3.1.0)

15 adımlık CSS uygulama planı. **home-1024 (Linux Embedded RPi5, 1024×600)** için hazırlanmıştır.

> **⚠️ Mockup Before Frontend:** Bu planı uygularken ilgili screen spec dosyası ve ASCII art view okunmadan kod yazılamaz.

---

## 1. Genel Bakış

### Uygulama Sırası

```
Faz 1: Token Updates (a-layout-tokens.css)
  ↓
Faz 2: Header (_header.css)
  ↓
Faz 3: Footer (_footer.css)
  ↓
Faz 4: Ana Sayfa (_home.css — Split Layout)
  ↓
Faz 5: Albümler (Standard 60/40)
  ↓
Faz 6: Albüm Detayı (Track List + Detail Panel)
  ↓
Faz 7: Sanatçılar (Standard 60/40, dairesel kartlar)
  ↓
Faz 8: Playlist/Now Playing
  ↓
Faz 9: Göz At (3 Sütun)
  ↓
Faz 10: Dosya Yöneticisi
  ↓
Faz 11: WiFi Modal (Pattern 4)
  ↓
Faz 12: Bluetooth Modal (Pattern 4)
  ↓
Faz 13: Hoş Geldin Modalı
  ↓
Faz 14: Auth Ekranları (Pattern 5) — Select Gender İLK → Login → Register 1→2→3
  ↓
Faz 15: View Mode CSS'leri (v-home, v-pro, v-studio)
```

### Bağımlılık Haritası

```
Token Updates (Faz 1)
├── Header (Faz 2) — bağımsız
├── Footer (Faz 3) — bağımsız
│   └── Ana Sayfa (Faz 4) — header + footer gerektirir
│       ├── Albums (Faz 5) — home layout'tan bağımsız
│       │   ├── Album Detail (Faz 6) — albums grid gerektirir
│       │   └── Artists (Faz 7) — albums ile aynı pattern
│       ├── Playlist (Faz 8) — home layout'tan bağımsız
│       ├── Göz At (Faz 9) — özel 3-sütun layout
│       └── Dosya Yöneticisi (Faz 10) — standard 60/40
├── WiFi Modal (Faz 11) — header gerektirir
│   └── Bluetooth Modal (Faz 12) — wifi modal ile aynı pattern
├── Hoş Geldin Modalı (Faz 13) — ana sayfa gerektirir
└── Auth Ekranları (Faz 14) — bağımsız (header/footer yok), Select Gender → Login → Register sırası zorunlu
    └── View Modes (Faz 15) — tüm sayfaları etkiler
```

---

## 2. Adım Detayları

### Adım 1: Token Updates

| Özellik | Değer |
|---------|-------|
| **Dosya** | `01_Abstracts/a-layout-tokens.css` |
| **Mockup** | Tüm PNG'ler (00-mockup-index.md §2) |
| **Bileşenler** | — |
| **Bağımlılık** | Yok (ilk adım) |
| **Zorluk** | Düşük |
| **Süre** | 15 dk |
| **Screen Spec** | — |

**Aksiyonlar:**
1. `--footer-h-compact: 104px` → `--footer-h-compact: 90px` (mockup ölçümü)
2. `--sidebar-w-browse: 167px` ekle (sadece Göz At)
3. `--touch-min: 44px` ve `--touch-recommended: 48px` doğrula
4. `--header-h: 60px` (1024px için) doğrula
5. `--content-padding-top: 11px` ekle
6. `--content-padding-bottom: 15px` ekle
7. `--glass-bg: rgba(255,255,255,0.1)` ekle
8. `--blur-lg: blur(20px) saturate(180%)` ekle
9. `--radius-pill: 50px` ekle
10. `--radius-full: 50%` ekle

**Çıktı:** `00-mockup-index.md` §2'deki tablolarla eşleşme

---

### Adım 2: Header

| Özellik | Değer |
|---------|-------|
| **Dosya** | `03_Layout/_header.css` |
| **Mockup** | Tüm uygulama PNG'leri |
| **Bileşenler** | C01 Nav, C02 Status, C03 User Pill |
| **Bağımlılık** | Adım 1 (token'lar) |
| **Zorluk** | Orta |
| **Süre** | 30 dk |
| **Screen Spec** | `screens/00-ascii-art-views.md` §1 (Header bölgesi) |

**Aksiyonlar:**
1. `.site-header` BEM yapısını doğrula
2. Logo font'u: Bickham Script Two + Respective
3. Nav link'ler: 8 item, gap 4px (1024px)
4. Actions alanı: WiFi+BT pill (65×37.4px), Battery pill (100px), User Pill
5. Touch target'leri 48px'e çıkar (nav link padding artır)
6. Responsive: 1024/1920/3840 breakpoint'leri

**Kaynak:** `screens/00-ascii-art-views.md` §1 + backup `header.md`

---

### Adım 3: Footer

| Özellik | Değer |
|---------|-------|
| **Dosya** | `03_Layout/_footer.css` |
| **Mockup** | Tüm uygulama PNG'leri |
| **Bileşenler** | Seek Slider, Transport Controls, Metadata, Volume |
| **Bağımlılık** | Adım 1 (token'lar) |
| **Zorluk** | Yüksek |
| **Süre** | 45 dk |
| **Screen Spec** | `screens/00-ascii-art-views.md` §1 (Footer bölgesi) |

**Aksiyonlar:**
1. `.footer` container: `height: 90px` (1024px) — **MOCKUP ÖLÇÜMÜ**
2. Album art: 120×120px
3. Transport butonları: 30×30px (normal), 38×38px (play)
4. Seek slider: top:0, full-width, 15px height
5. Volume slider: 145px (1024px)
6. Touch target: play 58×58px, others 54×54px (1024px)
7. Font: Avalon Medium, 10px, letter-spacing 0.135em
8. Accent: `var(--theme-primary)`
9. Glass: `backdrop-filter: blur(1px)`
10. İlerleme çubuğu: y=0'da, full-width, h:3px, pembe

**Kaynak:** `screens/00-ascii-art-views.md` §1 (Footer bölgesi) + backup `footer.md`

---

### Adım 4: Ana Sayfa (Split Layout)

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css`, `_home-layout.css`, `_home-components.css` |
| **Mockup** | `Linux 1024 - Home Page.png` |
| **Bileşenler** | C09 Card, Now Playing, Widgets, Mini Card |
| **Bağımlılık** | Adım 2 (header) + Adım 3 (footer) |
| **Zorluk** | Yüksek |
| **Süre** | 45 dk |
| **Screen Spec** | `screens/B-home/dashboard.md` + `screens/00-ascii-art-views.md` §1 |

**Aksiyonlar:**
1. Split layout: Sol %42 (Now Playing) + Sağ %58 (Widget)
2. Now Playing Card: Album art 100×100 + meta + seek bar
3. Widget Area: 4 glass panel (Bluetooth, Hava, Takvim, Klasörler)
4. Alt kısım: "En Son Dinlenen" (4 kart) + "Oynatma Listeleri" (5 kart) grid
5. "Sıradaki Şarkılar" + Mini Card
6. İçerik yüksekliği: 600 - 60 - 90 = 450px
7. Sidebar YOK (global sidebar token'ı kullanılmaz)
8. Glass paneller: `backdrop-filter: blur(8px)`

**Kaynak:** `screens/B-home/dashboard.md` (500 satır)

---

### Adım 5: Albümler (Standard 60/40)

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Albumler Page.png` |
| **Bileşenler** | C09 Card, C11 Tabs, C10 Panel |
| **Bağımlılık** | Adım 2 + 3 |
| **Zorluk** | Orta |
| **Süre** | 30 dk |
| **Screen Spec** | `screens/C-music/albums.md` |

**Aksiyonlar:**
1. 60/40 split: Sol 614px (card grid) + Sağ 390px (detail panel)
2. Genre tabs: Yatay scroll, ~13 sekme, h:32px (WCAG: 48px)
3. Card grid: 3 sütun, ~140×140px kartlar, gap:8px
4. Detail panel: Album art (300×300, dairesel) + metadata + aksiyonlar
5. Geri ok: sol üst, 44×44px

**Kaynak:** `screens/C-music/albums.md` (400 satır)

---

### Adım 6: Albüm Detayı

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Albumler Details Detay Page.png` |
| **Bileşenler** | C13 Track, C12 Stars, C10 Panel, C04/C05 Buttons |
| **Bağımlılık** | Adım 5 |
| **Zorluk** | Orta |
| **Süre** | 30 dk |
| **Screen Spec** | `screens/C-music/album-detail.md` |

**Aksiyonlar:**
1. Sol panel: Track list (C13) — satır başına thumb + title + duration + stars
2. Sağ panel: Album art (300×圆形) + metadata + "Hemen Çal" / "Karışık Çal"
3. Aktif satır: Pembe arka plan (`rgba(255,79,216,0.15)`)
4. Yıldız derecelendirme: 5 yıldız, interaktif
5. Tablo başlığı: Sabit, sıralanabilir

**Kaynak:** `screens/C-music/album-detail.md` (350 satır)

---

### Adım 7: Sanatçılar

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Singer Page.png` |
| **Bileşenler** | C09 Card (dairesel), C11 Tabs, C10 Panel |
| **Bağımlılık** | Adım 5 |
| **Zorluk** | Düşük |
| **Süre** | 20 dk |
| **Screen Spec** | `screens/C-music/artists.md` |

**Aksiyonlar:**
1. Albums ile aynı pattern (60/40)
2. Fark: Kartlar DAİRESEL (border-radius: 50%)
3. Genre tabs: Albums ile aynı
4. Detail panel: Artist photo (300×圆形) + bio + istatistikler

**Kaynak:** `screens/C-music/artists.md` (300 satır)

---

### Adım 8: Playlist/Now Playing

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Playlist Page.png` |
| **Bileşenler** | C13 Track, C12 Stars |
| **Bağımlılık** | Adım 2 + 3 |
| **Zorluk** | Düşük |
| **Süre** | 20 dk |
| **Screen Spec** | `screens/D-player/playlist.md` |

**Aksiyonlar:**
1. Standard 60/40 pattern
2. Sol: Track list (C13) — tablo formatında
3. Sağ: Playlist bilgileri + aksiyonlar + önerilen sanatçılar
4. Aktif satır: Pembe vurgu

**Kaynak:** `screens/D-player/playlist.md` (300 satır)

---

### Adım 9: Göz At (3 Sütun)

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Göz At Page.png`, `Göz At - Tıklama Clikced.png` |
| **Bileşenler** | C16 Row, C10 Panel |
| **Bağımlılık** | Adım 1 (sidebar token) + Adım 2 + 3 |
| **Zorluk** | Yüksek |
| **Süre** | 40 dk |
| **Screen Spec** | `screens/E-filemanager/disk-browser.md` + `file-list.md` |

**Aksiyonlar:**
1. 3 sütun layout: Sol 167px + Orta 573px + Sağ 224px
2. Sol sidebar: Dosya/ağaç yapısı (`.browse-layout`)
3. Orta liste: Dosya/sarkı listesi (tablo)
4. Sağ panel: Seçili öğe metadata + grafikler (donut, bar, pie)
5. `--sidebar-w-browse: 167px` token'ı kullan
6. **NOT:** Global sidebar YOK, sadece bu sayfaya özel
7. Sidebar satır yüksekliği: CSS ile 48px'e çıkar (WCAG)

**Kaynak:** `screens/E-filemanager/disk-browser.md` + `file-list.md` (650 satır toplam)

---

### Adım 10: Dosya Yöneticisi (Tıklama)

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Göz At - Tıklama Clikced.png` |
| **Bileşenler** | C10 Panel, grafikler |
| **Bağımlılık** | Adım 9 |
| **Zorluk** | Orta |
| **Süre** | 30 dk |
| **Screen Spec** | `screens/E-filemanager/file-list.md` |

**Aksiyonlar:**
1. Sol sidebar: Kategori listesi (Tüm Şarkılar, Son Eklenenler, vb.) + sayılar
2. Orta liste: Şarkı listesi (tablo)
3. Sağ panel: Donut chart + bar charts + pie chart + aksiyon butonları
4. Üst bilgi: Dosya yolu + arama

**Kaynak:** `screens/E-filemanager/file-list.md` (300 satır)

---

### Adım 11: WiFi Modal

| Özellik | Değer |
|---------|-------|
| **Dosya** | `04_Components/c-modal.css` |
| **Mockup** | `Linux 1024 - Wifi Qucik Page Base.png`, `Wifi Coonect Light.png` |
| **Bileşenler** | C14 Modal, C15 Toggle, C16 Network Row |
| **Bağımlılık** | Adım 2 (header) |
| **Zorluk** | Orta |
| **Süre** | 30 dk |
| **Screen Spec** | `screens/F-quickpanel/wifi.md` + `wifi-connect.md` |

**Aksiyonlar:**
1. Modal overlay: `backdrop-filter: blur(4px)`, `rgba(0,0,0,0.5)`
2. Modal header: Başlık + toggle + kapat butonu (44×44px)
3. "Bağlı Olan Ağ" bölümü: C16 Row listesi
4. "Kullanılabilir Ağlar" bölümü: C16 Row listesi
5. WiFi connect sub-dialog: C06 Form + C04 Button
6. Glass efekti: `blur(20px) saturate(180%)`

**Kaynak:** `screens/F-quickpanel/wifi.md` + `wifi-connect.md` (500 satır toplam)

---

### Adım 12: Bluetooth Modal

| Özellik | Değer |
|---------|-------|
| **Dosya** | `04_Components/c-modal.css` |
| **Mockup** | `Linux 1024 - Bluethoot Qucik Page Base.png` |
| **Bileşenler** | C14 Modal, C15 Toggle, C16 Device Row |
| **Bağımlılık** | Adım 11 (WiFi modal ile aynı pattern) |
| **Zorluk** | Düşük |
| **Süre** | 15 dk |
| **Screen Spec** | `screens/F-quickpanel/bluetooth.md` |

**Aksiyonlar:**
1. WiFi modal ile aynı pattern
2. Fark: Cihaz rozetleri (A2DP, HFP, Müzik)
3. Badge renkleri: A2DP=pembe, HFP=mor, Müzik=yeşil

**Kaynak:** `screens/F-quickpanel/bluetooth.md` (250 satır)

---

### Adım 13: Hoş Geldin Modalı

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/_home.css` |
| **Mockup** | `Linux 1024 - Home Page Welcome Popup.png` |
| **Bileşenler** | C14 Modal, C04 Button |
| **Bağımlılık** | Adım 4 (ana sayfa) |
| **Zorluk** | Düşük |
| **Süre** | 15 dk |
| **Screen Spec** | `screens/B-home/welcome-popup.md` |

**Aksiyonlar:**
1. Modal boyutu: 600×308px
2. Tam ortalanmış: merkez x=512, y=299.5
3. "Başla" butonu: 105×25px → **48px'e büyütülmeli** (WCAG)
4. Logo + başlık + açıklama + buton
5. Backdrop click ile kapatma

**Kaynak:** `screens/B-home/welcome-popup.md` (300 satır)

---

### Adım 14: Auth Ekranları

| Özellik | Değer |
|---------|-------|
| **Dosya** | `05_Pages/p-login-view.css`, `p-select-gender.css` |
| **Mockup** | shared-1024/ (6 PNG) |
| **Bileşenler** | C04, C05, C06, C07, C08 |
| **Bağımlılık** | Yok (header/footer yok) |
| **Zorluk** | Yüksek |
| **Süre** | 45 dk |
| **Screen Spec** | `screens/A-auth/` (4 dosya) |

**Aksiyonlar:**
1. Pattern B: Hero image (78%) + Glass panel (22%)
2. Select Gender: 3 büyük buton (Kız/Erkek/Diğer) + Devam Et
3. Login: Email + Şifre + Social buttons (7 adet)
4. Register: 3 adım (Kullanıcı Adı+Email → Şifre → Telefon+KVKK)
5. Auth sayfalarında header/footer YOK
6. Glass panel: `backdrop-filter: blur(20px) saturate(180%)`
7. Gender seçimi tema rengini değiştirir

**Kaynak:** `screens/A-auth/` (4 dosya, 1350 satır toplam)

> ⚠️ **AUTH AKIŞ SIRASI:** Select Gender İLK adımdır. Cinsiyet seçimi tema rengini belirler (female→pink, male→blue, neutral→default). Bu sıra değiştirilemez.
> 
> **Doğru akış:** Select Gender → Login → Register Step 1 → Register Step 2 → Register Step 3

---

### Adım 15: View Mode CSS'leri

| Özellik | Değer |
|---------|-------|
| **Dosya** | `09_ViewModes/v-home.css`, `v-pro.css`, `v-studio.css` |
| **Mockup** | — (gelecek platformlar) |
| **Bileşenler** | — |
| **Bağımlılık** | Tüm önceki adımlar |
| **Zorluk** | Orta |
| **Süre** | 30 dk |
| **Screen Spec** | — |

**Aksiyonlar:**
1. `v-home.css`: Ev medya merkezi görünümü
2. `v-pro.css`: Profesyonel görünüm (ek bilgi panelleri)
3. `v-studio.css`: Stüdyo görünümü (8.1 surround kontrolleri)
4. Cookie: `view-mode=home|pro|studio`
5. JS: `device-loader.js` tarafından dinamik yüklenir

---

## 3. Süre Özeti

| Adım | Dosya | Süre | Zorluk | Screen Spec |
|------|-------|------|--------|-------------|
| 1 | a-layout-tokens.css | 15 dk | Düşük | — |
| 2 | _header.css | 30 dk | Orta | ASCII §1 |
| 3 | _footer.css | 45 dk | Yüksek | ASCII §1 |
| 4 | _home.css | 45 dk | Yüksek | dashboard.md |
| 5 | _home.css (albums) | 30 dk | Orta | albums.md |
| 6 | _home.css (album detail) | 30 dk | Orta | album-detail.md |
| 7 | _home.css (artists) | 20 dk | Düşük | artists.md |
| 8 | _home.css (playlist) | 20 dk | Düşük | playlist.md |
| 9 | _home.css (göz at) | 40 dk | Yüksek | disk-browser.md |
| 10 | _home.css (file manager) | 30 dk | Orta | file-list.md |
| 11 | c-modal.css (wifi) | 30 dk | Orta | wifi.md |
| 12 | c-modal.css (bluetooth) | 15 dk | Düşük | bluetooth.md |
| 13 | _home.css (welcome) | 15 dk | Düşük | welcome-popup.md |
| 14 | p-login-view.css | 45 dk | Yüksek | A-auth/ (4 dosya) |
| 15 | v-*.css | 30 dk | Orta | — |
| **Toplam** | | **~7.5 saat** | | |

---

## 4. Kritik Kontrol Noktaları

| # | Kontrol | Neden |
|---|---------|-------|
| 1 | Token çakışması | Yeni `--sidebar-w-browse` mevcut `--sidebar-w` ile çakışmamalı |
| 2 | Footer yüksekliği | **90px** (mockup) — CSS'teki 104px güncellenmeli |
| 3 | Touch target | Tüm interaktif elementler ≥48px (RPi5) |
| 4 | Sidebar | Sadece Göz At sayfasına özel, global değil |
| 5 | Auth header/footer | Auth sayfalarında header/footer render edilmez |
| 6 | Glass effect | `backdrop-filter` desteği (RPi5 Chromium) |
| 7 | Font loading | Google Fonts + local @font-face uyumu |
| 8 | Theme tokens | `var(--theme-primary)` ile tema değiştirme |
| 9 | ASCII Art Reference | Kod yazmadan önce `00-ascii-art-views.md` okunmalı |
| 10 | Screen Spec | Her adım için ilgili `screens/*.md` okunmalı |
| 11 | Auth akış sırası | Select Gender İLK, Login之后, Register 1→2→3 (değiştirilemez) |

---

## 5. Risk Matrisi

| Risk | Seviye | Olasılık | Etki | Mitigasyon |
|------|--------|----------|------|------------|
| Footer yüksekliği yanlış | MEDIUM | Düşük | Yüksek | CSS token'ı 90px olarak ayarla |
| Sidebar global uygulanırsa | MEDIUM | Orta | Yüksek | Sadece `.browse-layout` class'ı |
| Touch target yetersiz | HIGH | Yüksek | Orta | Her adımda 48px kontrolü |
| Glass blur performans | LOW | Düşük | Düşük | RPi5'te test et |
| Font loading gecikmesi | LOW | Orta | Düşük | FOUT stratejisi |
| Token çakışması | MEDIUM | Düşük | Yüksek | Namespace prefix kullan |
| Auth akış sırası yanlış | MEDIUM | Orta | Yüksek | PNG'lerden doğrulandı |

---

## 6. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | PNG master kataloğu |
| [[01-component-inventory]] | C01-C16 detayları |
| [[00-ascii-art-views]] | Piksel düzeyinde ASCII art'lar |
| [[03-accessibility-gaps]] | WCAG gap analizi |
| [[04-vault-registration]] | Vault kalıcı kayıt |
| [[architecture/03-css-device-loading-plan]] | Mevcut CSS planı |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.1.0 |
| Step Count | 15 |
| Total Duration | ~7.5 saat |
| High Risk Steps | 4 (Footer, Home, Göz At, Auth) |
| Token Updates | 10 yeni token |
| Touch Target Fixes | 5 bileşen (C01, C11, C12, C13, C15) |
| Screen Spec References | 18 dosya |
| ASCII Art Reference | 18 PNG |
| ADR Uyumlu | ✅ ADR-001, ADR-044 |
| Platform | home-1024 (Linux Embedded RPi5) |

---

*Implementation Plan v3.1.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
