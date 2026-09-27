---
title: "CoreMusic — Home Dashboard (Ana Ekran · 22\" FHD)"
tier: T17
device: "22\" FHD Monitor (Desktop)"
viewport: { width: 1920, height: 1080 }
path: "screens/T17-monitor-22fhd/"
status: active
version: 1.0.0
source_of_truth: ".ai/.png/home-1920/Linux - 1920 - Home.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-27"
author: "Bayram Ali"
---

# CoreMusic — Home Dashboard (T17 Desktop Monitor 1920×1080)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

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

Bant toplamı: 99 + 184 + 86 + 43 + 43 + 45 + 43 + 445 + 92 = 1080 ✓

**Lejant:** `[PNG]` = PNG'den okundu (SSOT) · `[Figma]` = Figma node ölçüsü (PNG ile karşılaştırıldı) · `[türetilmiş]` = çıkarım (kesin koordinat değil).

> ⚠️ ÇELİŞKI (§1-1): Bölüm başlığı — Figma'da **tek** başlık TEXT düğümü var: `2850:21627` (adı "En Son Dinlenen Şarkılar", **metni** "Son Oluşturlan & Sistem Taraından Oluşturlan Playlistler") @(53,455). **PNG'de iki başlık** görünüyor: (1) "En Son Dinlenen Şarkılar" y324-369 — Figma'da bu metinle bir TEXT düğümü yok, yalnızca `2850:21627`'nin node adı; (2) "Son Oluşturlan…" y455-500 = Figma metni ✓. Çözüm: **PNG > Figma** → Figma'ya 1. başlık için ayrı TEXT düğümü eklenmeli. Düzeltme `00-ascii-art-index.md` bu görevde yazılmayacak (erişim dışı — not edildi).

> ⚠️ ÇELİŞKI (§1-2): 1. kart satırı — Figma'da row1 için **tek** kart: `2856:22290` FRAME "Şarkılar Item Btn" 169×43 @(53,369). **PNG'de 10 kart** (x=53→1709, pitch 184; sol4 kart başlık kırpması + sağ6 kart kırpması görsel okuma, kart gölge/prob ile doğrulandı). Figma'da 9 kopya INSTANCE eksik (row2'de 6 INSTANCE doğru tanımlı). Çözüm: **PNG > Figma** → Figma row1'e9 INSTANCE eklenmeli (kaynak: extraction `169 h=43` taraması: yalnız `2856:22290` y-220'de).

**Koordinat Kanıtı (SSOT = PNG):** Figma frame origin (606, −589); koordinatlar frame-rel (canvas − origin). PNG 1920×1080; nav 8 link, iki başlık, 10+6 kart, footer y988-1080, iki bitrate metni — tüm metinler PNG'den **piksel bitmap/kırımla** okundu (read-tool stale beslemeleri piksel ortalamasıyla eleyip taze kırpmalarla doğrulandı). Kart pitch 184 (169+15) Figma row2 INSTANCE'ları ile PNG row1/row2'de aynı.

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.nav-link` | C01 | Navbar 8 link + Profile/Logout |
| `.home-widget-area` | C17 | Top bölge: Player Info + Banner + Div2 (Hava/Tarih/Butonlar) |
| `.home-quick-apps` | C18 | YT/YTMusic/Deezer 42×43 + Equalizer grubu |
| `.home-mini-card` | C19 | Row1 10× + Row2 6× kart (169×43) |
| `.card` | C03 | Banner / Hava kartları (glass) |
| `.progress` | C15 | Player Progressbar 246×9 + footer Progressbar 1921×4.8 |
| `.btn` | C04 | Dosya Yönetici (`Kütüphanelerim`), Equalizer, Player kontrolleri |
| `.slider` | C09 | Volume 353×14 |

> ⚠️ ÇELİŞKI (§2-1): `02-component-inventory.md` kendi içinde sürüm çelişkisi taşıyor — **frontmatter `version: 4.0.0`** (satır 9) vs aynı dosyanın **Quality Report `5.0.0`** (satır 227). Bu spec `version: 1.0.0` (Kalıp D) kullanır; envanter dosyası bu görevde **erişim dışı** → düzeltme not edildi.

Bu ekran özel notlar:
- Mini-carda thumbnail + 2 satır metin + süre (PNG okuma: "Göksel - Sevil Neşelen" vb.) — içerik örnekleri Figma INSTANCE props'larından (`Sure#880:5=00:03:05` vb.).
- Figma hygiene (çelişki değil): nav düğüm ADLARI iki kez "Göz At" (node `…20429` adı "Göz At", metni "Sanatcılar") — metinler PNG ile birebir ✓.

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Aktif nav-link, progress dolgu, vurgular |
| `--cm-error` | `var(--cm-error)` | Hata state (bu ekranda görünmez) |
| `--cm-glass-bg` / `-border` / `-blur` | `var(--cm-glass-bg)` / `var(--cm-glass-border)` / `var(--cm-glass-blur)` | Navbar, mini-card, Banner, Hava kartları |
| `--cm-card-radius` | `var(--cm-card-radius)` | Mini-card (Figma Container r3–r8 arası; radius-xl'e eşli) |
| `--cm-radius-md` | `var(--cm-radius-md)` | Banner r8 |
| `--cm-progressbar-value-fill` | **API'den gelmedi** | Progressbar dolgusu — master'da ad tanımlı, `:root` değeri **yok** → §9 |
| `--cm-space-*` | `var(--cm-space-*)` | Grid pitch 184 (169+15), satır arası 43 |
| `--cm-font-display` / `--cm-font-body` | `var(--cm-font-display)` / `var(--cm-font-body)` | Başlık / gövde |
| `--cm-shadow-md` / `--cm-shadow-lg` | `var(--cm-shadow-md)` / `var(--cm-shadow-lg)` | Kart gölgeleri (Figma: DROP_SHADOW 0/3/15 #373737 α.5) |
| `--cm-z-player` | `var(--cm-z-player)` (350) | Footer player / sticky katman |
| `--cm-touch-target` | `var(--cm-touch-target)` (44px) | T17 mouse tier → §4: 32×32 önerisi |

Not: Font PNG/Figma'da **Inter** (nav body Avalon = Figma yerel fontu → CSS karşılığı **API'den gelmedi**, Inter fallback). Ham hex/rgba §6'da.

## 4. Touch Target (WCAG 2.5.5 / 2.5.8 — T17 Mouse Tier)

| Öğe | PNG/Figma Boyutu | WCAG Zorunlu | Not |
|---|---|---|---|
| Navbar link | 11px font, h15 | Mouse 24px | ⚠️ PAD GEREKLİ (yatay/dikey padding ile ≥24×24) |
| Mini-card 169×43 | 169×43 | Mouse n/a | ✓ (dokunma için yeterli) |
| Player buttons | 126×27 grubu (~27px) | 24px (mouse) | ✓ 27 ≥ 24 |
| Volume | 353×14 | 24px | ⚠️ 14 < 24 yükseklik → dikey padding |
| YT/YTMusic/Deezer | 42×43 | 44 (dokunma) / 24 (mouse) | ✓ |
| Profile / Logout | 90×24 / ~24² | 24px | ✓ sınırda |

Notlar:
- T17 = **mouse tier** (matrix): hover serbest, 2.5.5 küçük hedef muafiyeti uygundur; dokunmatik senaryoya karşı **tavsiye min 32×32** — matrix'te T17 touch değeri boş → **API'den gelmedi** → VERIFICATION REQUIRED.
- Padding değerleri kod aşamasında (`--cm-touch-target` 44px T07 içindir; T17 önerisi 32×32 ayrı token → API'den gelmedi).

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast | 4.5:1 | ⚠️ VERIFICATION REQUIRED | Metin #FFF + DROP_SHADOW; zemin PNG'den örneklenip hesaplanacak (banner görseli üstü riskli) |
| 1.4.11 Kontrast (non-text) | 3:1 | ⚠️ VERIFICATION REQUIRED | Progress dolgu / ikon kenarları |
| 2.4.7 Focus | Görünür focus | ⚠️ GAP | PNG'de focus yok (mouse ekranı) → `--cm-shadow-focus` |
| 2.4.3 Focus Order | Anlamlı sıra | PASS (tasarım) | Navbar → top → başlık1 → row1 → başlık2 → row2 → footer |
| 1.3.1 Info & Relationships | Yapı | PASS | header/nav/main/footer landmark (kod aşaması) |
| 1.4.1 Use of Color | Renk tek değil | PASS (tasarım) | Nav aktiflik ikon+renk (**aktif state API'den gelmedi**) |
| 2.1.1 Keyboard | Klavye | PASS (tasarım) | Tüm kartlar link/button |
| 1.4.10 Reflow | 320px | N/A | T17 sabit monitor → §8 |

Not: GAP kaydı `04-accessibility-gaps.md` bu görevde düzenlenmeyecek (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Navbar | `rgba(255,255,255,0.05)` + `blur(12px)` + `border rgba(255,255,255,.08)` | `--cm-glass-*` master + Figma glass node |
| Mini-cards | `rgba(255,255,255,0.05)` + `blur(12px)` + `0 4px 30px rgba(0,0,0,.3)` | `--cm-glass-bg/blur/shadow` |
| Banner / Hava | `rgba(255,255,255,0.05)` + blur 12 | `--cm-glass-*` (PNG'de cam efekt ✓) |
| Kart stroke | `rgba(255,255,255,0.1)` INSIDE 1px | Figma `Stroke Effect` vektörleri |
| Zemin | `#000` + gradient (PNG: pembe- degrade fotoğraf zemini) | PNG — `--cm-bg-image` **API'den gelmedi** |
| Progress dolgu | `--cm-primary` gradyanı | Figma progress fill (ham: master §3) |

Not: `--cm-bg-overlay: rgba(0,0,0,0.60)` **DEPRECATED** (master §2.1.2) → SSOT `rgba(0,0,0,.35)` + blur 3px; overlay karşılığı yalnızca `screens/T17-monitor-22fhd/welcome-popup.md` §6'da.

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/home-1920/Linux - 1920 - Home.png`
  - 1920×1080; nav 8 link, "En Son Dinlenen Şarkılar" + "Son Oluşturlan…", 10+6 kart, üst "350 kbps", footer "350 kpps", footer y988-1080 — metinler taze kırımpa+piksel bitmap ile okundu (stale read'ler file-avg ile eledi).
- **Figma (referans):** frame Home 1920 (origin 606,−589); nav `2831:13748` (`I…;1314:20425-20433`), Player Info `2849:21489`, üst bitrate `2849:21479`, Banner, Div2 `2850:21494`, başlık `2850:21627`, row1 `2856:22290`, row2 `2890:7922/7923/7924/7966/7967/7968`, Footer `2831:13859` (bitrate `I2831:13859;1330:24174`).
- **Figma PNG export:** `.ai/ui-design/reference/figma/png/` (referans).
- **SSOT sırası:** PNG > Figma → 2 çelişki §1'de, 1 çelişki §2'de etiketlendi (toplam 3).
- **Not (etiketsiz, iç tutarsızlık):** üst "350 kbps" vs footer "350 kpps" — hem Figma hem PNG'de aynı şekilde → kaynak karşılaştırmasında çelişki değil; footer "kpps" yazım hatasıdır, tasarım sorumlusuna düzeltme notu.

## 8. Responsive Davranış

| Kural | Davranış |
|---|---|
| 1920-2560px | Sabit 1920 sahne; içerik max-width 1920, ortalanmış |
| 1600-1920px | Top bölge esner; kartlar 169 sabit, satır taşması yatay kaydırma |
| <1600px | T17 kapsam dışı (T07/T08'e geçiş) → **API'den gelmedi** (matrix kırılım yok) |
| Kart satırları | 10/6 kart; sığmayan kısım yatay kaydırma (PNG'de tam 10 kart görünür) |
| Orientation | T17 landscape sabit |

Not: Breakpoint değerleri matrix'te tanımlı değil → VERIFICATION REQUIRED (`--cm-bp-*` token'ı önerisi — API'den gelmedi).

## 9. State Durumları

| State | Tanım | Kaynak |
|---|---|---|
| default (bu PNG) | Ana ekran; player hazır; 10+6 kart | PNG (SSOT) |
| playing | Progress dolgulu, pause ikonu | **API'den gelmedi** (PNG statik) |
| nav-active | Aktif nav-link vurgusu | **API'den gelmedi** (PNG'de belirsiz → 1.4.1 notu) |
| hover | T17 serbest → card lift + `--cm-shadow-lg` | **API'den gelmedi** (mouse state'i) |
| focus | `--cm-shadow-focus` | **API'den gelmedi** |
| loading | Skeleton (C13) | **API'den gelmedi** |
| empty | Bölüm boşluğu | **API'den gelmedi** |

> ⚠️ VERIFICATION REQUIRED — playing/nav-active/hover/focus/loading/empty state görselleri yok; `--cm-progressbar-value-fill` token değeri master'da tanımsız (ad var, `:root` yok).

---

**Quality Report**

- **Doğrulama:** PNG (SSOT; nav/başlık/bitrate metinleri taze kırpım + piksel bitmap ile, kartlar görsel kırpım + gölge/prob ile) + Figma 1920 extraction (nav/Player/Div2/başlık/row1/row2/Footer node'ları) + C01/C17/C18/C19/C15/C04/C09 + token master + T17 matrix satırı. Read-tool stale beslemeleri file-avg piksel parmak iziyle saptanıp eleyendi.
- **Çelişki:** 3 — §1-1 (Figma tek başlık düğümü vs PNG iki başlık), §1-2 (Figma row1 1 kart vs PNG 10 kart), §2-1 (inventory frontmatter 4.0.0 vs Quality Report 5.0.0). Planlanan nav ve bitrate çelişkileri **piksel kanıtıyla çürütüldü** (metinler birebir aynı; node-adı farkları etiketsiz not).
- **Eksik veri:** kontrast/focus/hover ölçümleri, `--cm-progressbar-value-fill` değeri, breakpoint/touch token'ları → §4/§5/§8/§9 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` + `00-ascii-art-index.md` + `02-component-inventory.md` düzeltmeleri (erişim dışı — notlar §1/§2/§5/§7'de).
- **Kontrol:** §1-§9 sırası; bantlar 1080'e tam; `[PNG]/[Figma]/[türetilmiş]` lejantı §1'de; status `active` (PNG doğrulanmış); spec sürümü 1.0.0 (envanter çelişkisinden bağımsız).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
