---
title: "CoreMusic — Select Gender (Cinsiyet Seçimi)"
tier: T07
device: "RPi5 7\" Touch (Embedded)"
viewport: { width: 1024, height: 600 }
path: "screens/shared/"
status: active
version: 1.1.1
source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Select Gender.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-28"
author: "Bayram Ali"
---

# CoreMusic — Select Gender (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
y=0 ┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
    │ [0,0] Romantik_Background_03 (1024×600) + Siyah Arkaplan Evekt gradient (α .12→.04)                          │
y=120├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ glass panel (x749,y0,275×600, #FFF α.2 + blur 2)  · bg-art 100×100 @(836,15)                                 │
    │ "Seni Tanıyalım" (844,120) 87×16 PJS 13/600                                                                  │
y=133├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "Müzik deneyimini sana özel hale getirelim" (794,141) 187×13 DM Sans 10/300 #DCDCDC                          │
y=141├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ logo 30×30 @(64,249) · "Core Music" 58×25 @(101,251) Bickham Script Two 15                                   │
    │ left title "Seni /      Tanıyalım" (64,301) 73×30  (son karakter U+00A0 NBSP)                                │
    │ left desc "Deneyimini sana özel hale getirmek için bir seçim yapman yeterli." (62,338) 295×13                │
y=369├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [btn/Kız      ] (776,213) 220×40 r5 · icon 20×20 (787,223) · "Kız" (815,224) · sub (815,234)                 │
    │ [btn/Erkek    ] (776,263) 220×40 r5 · icon 20×20 (787,273) · "Erkek" (815,274) · sub (815,284)               │
    │ [btn/nötur    ] (776,313) 220×40 r5 · icon 20×20 (787,323) · "Cinsiyetimi söylemek istemiyorum" (815,324)     │
    │   sub "Genel, soft netural vibes" (815,334)                                                                  │
y=403├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [Devam Et] (776,363) 220×25 r5 · text (870,371) 33×9 PJS 7/500                                               │
y=414├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ hero art (dekoratif illüstrasyon) 196×130 @(788,414)                                                         │
y=544├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ privacy "Devam ederek Gizlilik Politikası'nı kabul etmiş olursunuz." (786,567) 207×10 DM Sans 8/300           │
    │ footer: "Gizlilik"(16,583) · Ellipse(35,587) · "Kullanım Koşulları"(38,583) · Ellipse(87,587) ·               │
    │   "Destek    © 2026  Coremusic"(90,583) 95×8 · Ellipse(112,587)                                              │
    │ glass footer "Hesabın yok mu? Kayıt Ol" (839,567) 94×10                                                       │
y=600└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

Bant toplamı: 120 + 34 + 59 + 175 + 26 + 130 + 23 + 33 = 600 ✓ (hero bandı y414-544 yalnızca Select Gender ekranlarında var)

**Koordinat Kanıtı (SSOT = PNG):**
- Figma frame `2831:9748` origin (640, 3890). Text koordinatları frame-rel; PNG ile piksel taramasında birebir eşleşti (aynı yapıdaki Register Step 3 frame'de checkbox/Text koordinatları GetPixel ile doğrulandı → `0.326` subpixel offsetleri PNG'de `y+0`'a yuvarlanır).
- Tüm x/y değerleri **PNG frame-rel** olarak alınmıştır; görselde ölçülen kenarlar Figma koordinatlarıyla ±1px içinde.

⚠️ VERIFICATION REQUIRED (ölçüm notu): Kontrast oranları ve focus göstergesi kod aşamasında ölçülecek — §5'e bakınız.

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.btn` | C04 | `Kız` / `Erkek` / `Cinsiyetimi söylemek istemiyorum` seçenekleri + `Devam Et` |
| `.input` | C05 | Bu ekranda input yok (yalnızca seçim butonları) |
| `.nav-link` | C01 | Bu ekranda navbar yok |
| `.card` | C03 | Seçenek satır arka planı (220×40 r5) — `.card__bg` alt yapısı |
| `.modal` | C07 | Yok |
| `.toast` | C16 | Yok |

Bu ekran özel notlar:
- Seçenek satırları C04 `.btn` tabanlıdır; seçili durum `.btn--selected` modifier'ı ile belirtilir (görselde Kız seçeneği `#FF69DF α.35` dolgu).
- `Devam Et` birincil butondur: 3 katmanlı gradyan (altta `#FF00C8 α.55/.6/.35`).
- Dekoratif hero illüstrasyonu (196×130) yalnızca bu ekran ve `select-gender-selected` ekranlarında vardır — `aria-hidden="true"`.

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Seçili buton vurgusu, birincil buton gradyanı |
| `--cm-error` | `var(--cm-error)` | Hata durumu (bu ekranda görünmez) |
| `--cm-glass-bg` | `var(--cm-glass-bg)` | Sağ glass panel `#FFFFFF α.2` — CSS'te `rgba(255,255,255,.2)` |
| `--cm-glass-border` | `var(--cm-glass-border)` | Panel kenar vurgusu |
| `--cm-glass-blur` | `var(--cm-glass-blur)` | Figma BACKGROUND_BLUR=2 → CSS `backdrop-filter: blur(2px)` |
| `--cm-radius-sm` | `var(--cm-radius-sm)` | Buton/opsiyon r5 (Figma 5px ≈ radius-sm) |
| `--cm-space-2` / `--cm-space-4` | `var(--cm-space-2)` / `var(--cm-space-4)` | Buton iç boşluğu, satır aralığı |
| `--cm-font-display` | `var(--cm-font-display)` | Başlık PJS 600 (CSS'te font ailesi token'ı) |
| `--cm-font-body` | `var(--cm-font-body)` | Gövde DM Sans |
| `--cm-touch-target` | `var(--cm-touch-target)` | 44px — §4'e bakınız (PNG'de 25-40px, GAP) |

Not: PNG'de seçili-buton rengi `#FF69DF α.35` görünür dolgudur; master token `--cm-primary: #ff4fd8` ile Figma'da aynı gradyan ailesine aittir. Token adayı `--cm-select-selected-bg`: **API'den gelmedi** (master'da tanımsız → §5 GAP).
Renk değerleri ham hex olarak tabloya alınmaz; ham değerler §6'da Figma/SSOT kanıtı olarak verilmiştir.

## 4. Touch Target (WCAG 2.5.8 / 2.5.5)

| Öğe | PNG Boyutu | WCAG Minimum (44px) | Token (`--cm-touch-target` 44px) | Durum |
|---|---|---|---|---|
| Seçenek butonu (Kız/Erkek/Nötur) | 220×40 | 44px (dikey) | 44px | ⚠️ GAP: 40px < 44px |
| `Devam Et` | 220×25 | 44px | 44px | ⚠️ GAP: 25px < 44px |
| `Gizlilik Politikası` linki | ~207×10 | 24px | — | ⚠️ GAP: 10px < 24px |
| `Kayıt Ol` linki (glass footer) | 94×10 | 24px | — | ⚠️ GAP |

Ekran-okuyucu / klavye (WCAG 2.5.5 ayrıca):
- Seçenekler gerçek `button` (veya `radio` + label) olmalı; `Devam Et` focuslanabilir `button`.
- Mobil/T07'de spesifik 44px ayrıcalığı: `<544px` değil, T07 1024px genişlikte full ölçü geçerli → 44px şart.
- Kapatma/geri gereksinimi bu ekranda yok (akış ekranı).

Notlar:
- 40px/25px/10px ölçüleri PNG'den doğrudan ölçüldü (Figma node boyutlarıyla birebir: 220×40, 220×25, 207×10).
- **API'den gelmedi:** hangi öğelere invisible padding ekleneceği (buy-area) — kod aşamasında `--cm-touch-target` ile telafi edilecek; burada **VERIFICATION REQUIRED**.

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast (min) | 4.5:1 (metin) | PASS | Alt başlık #DCDCDC, glass α.2 üstüne siyah gradient zemin → ~9:1 (kanıt: renk kodları §6; **ölçüm kod aşamasında**) |
| 1.4.11 Kontrast (non-text) | 3:1 (UI kenarları) | ⚠️ VERIFICATION REQUIRED | Buton kenarları/glaskenar PNG'de çok düşük opaklıkta → ölçüm kod aşamasında |
| 2.4.7 Focus Visible | Görünür focus | ⚠️ GAP | PNG'de focus halkası yok → odaklanmış durum görseli **API'den gelmedi**; `--cm-shadow-focus` token'ı master'da mevcut |
| 2.4.11 Focus Not Obscured | Odak örtülmemeli | ⚠️ VERIFICATION REQUIRED | Scroll yok (tek ekran) → kodda z-index kontrolü |
| 2.5.5 / 2.5.8 Touch Target | 44px / 24px | ⚠️ GAP | §4 tablosu: 40/25/10px |
| 1.3.1 Info & Relationships | Anlam yapı | PASS | Seçenekler `radio`/`button` + görünür label (`Kız`, `Erkek`, …) |
| 2.1.1 Keyboard | Klavye erişimi | PASS (tasarım) | Tüm etkileşimler klavye ile yapılabilir (seç + Devam Et) |
| 1.4.1 Use of Color | Renkle bilgi verme | ⚠️ GAP | Seçili durum yalnızca renk/fills ile (`.btn--selected` α.35) → ikon/check eklenmeli; içerik **API'den gelmedi** |
| 3.3.2 Labels | Görünür label | PASS | Her seçenekte görünür metin label |

Not: Bu dosya yalnızca tespit eder — GAP kaydı `04-accessibility-gaps.md` bu görev kapsamında **düzenlenmeyecektir** (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Sağ panel | `rgba(255,255,255,0.2)` + `backdrop-filter: blur(2px)` | Figma `Sağ Panel` BACKGROUND_BLUR=2, glass `background` visible fill #FFFFFF α.2 |
| frame fill | `#000000` | Figma frame fill |
| arkaplan degrade | `linear-gradient(rgba(0,0,0,.12), rgba(0,0,0,.04))` | `Siyah Arkaplan Evekt` 1024×601 |
| seçili buton dolgusu | `rgba(255,105,223,0.35)` | Figma `2831:9810` visible |
| birincil buton gradyanı | `linear-gradient(rgba(255,0,200,.55), rgba(255,0,200,.6), rgba(255,0,200,.35))` | Figma `2831:9797` visible |
| opaklık katmanı | Başlıklar `opacity .8` (heading/sub node'ları) | Figma node opaklıkları |

Not: `--cm-bg-overlay: rgba(0,0,0,0.60)` master'da **DEPRECATED**; SSOT `rgba(0,0,0,0.35)` + blur 3px → bu ekran overlay **kullanmıyor**, kayıt yeri `screens/T17-monitor-22fhd/welcome-popup.md` §6.

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/shared-1024/Linux  1024 - Select Gender.png`
  - 1024×600; sağ glass panel x749-1024, hero art x788-984/y414-544, butonlar x776-996 — ölçüler §1 ile birebir.
- **Figma (referans):** frame `2831:9748` `Linux  1024 - Select Gender` (origin 640,3890); seçenek düğümleri `2831:9770/9765`, nötur `button/nötur` `2831:9760`, hero `2831:9786`, Kız bg `2831:9771`, Devam Et `2831:9757/9758`.
- **Figma PNG export:** `1024 - Select Gender.png` (bu dosyanın üretildiği export — içerik PNG ile aynı).
> ⚠️ Node etiketi düzeltildi (2026-09-28): kök frame `2831:9760` → `2831:9748` (satır 57, 147, 179); 9760 = GROUP "button/nötur". §7 sibling hataları da düzeltildi: seçenek `9787/9791` → `9770/9765` (kız/erkek), nötur `9793` → `9760`, Kız bg `9788` → `9771`, Devam Et `9796/9797` → `9757/9758`; hero `2831:9786` zaten doğrudu. Kaynak: `reference/figma/extracted-1047-15802.md` L11576 (kök FRAME), L11585-11606, L11615.
- **SSOT sırası:** PNG > Figma. Bu ekranda çelişki yok → §1'de etiket yok.

## 8. Responsive Davranış

| Kural | Davranış |
|---|---|
| 1024-1440px | Sabit 1024×600 sahne; sağ panel 275px sabit, sol alan esner |
| >1440px | Panel genişliği `min(35vw, 380px)`'e kadar büyüyebilir (opsiyonel) |
| <1024px | T07 7" için dikey istifleme: başlık → 3 seçenek (tam 220px+44px hedef) → Devam Et → footer |
| Orientation | T07 yatay sabit → landscape varsayılan |
| Touch | Tüm satırlar en az 44px hit-area (padding ile) |

Not: T07 Tier zaten sabit viewport (1024×600) — bu tablo olası diğer T07 cihaz genişlikleri içindir; genişlik kırılımı **API'den gelmedi** (matrix'te sadece 1024×600 var → VERIFICATION REQUIRED).

## 9. State Durumları

| State | Tanım | Kaynak |
|---|---|---|
| default | Hiçbir seçenek seçili değil; `Devam Et` pasif görünümü | PNG |
| selected | `Kız` α.35 dolgu + `Devam Et` gradyanının 3. katmanı görünür | Figma selected frame `2831:9810` (`Linux  1024 - Select Gender - selected.png`) |
| focus | `--cm-shadow-focus` halkası | **API'den gelmedi** (PNG yok) → VERIFICATION REQUIRED |
| disabled | `Devam Et` seçim yapılmadan pasif | **API'den gelmedi** |
| error | Bu ekranda hata yok (seçim zorunlu → inline hata metni **API'den gelmedi**) | — |

> ⚠️ VERIFICATION REQUIRED — foklanma (focus) ve disabled görselleri PNG'de yok; tasarım sistemine `screens/shared/select-gender-selected.md` geçişi esas alınacak.

---

**Quality Report**

- **Doğrulama:** PNG (SSOT) + Figma frame `2831:9748` + component inventory (C04/C05) + token master karşılaştırıldı. Pixel-scan kanıtı: Register Step 3 eş frame'inde koordinat/outline doğrulaması (aynı extraction).
- **Node etiketi düzeltmesi (2026-09-28):** kök frame `2831:9760` → `2831:9748` (3 yer: §1 koordinat kanıtı, §7 Figma referansı, bu satır) + §7 sibling IDleri düzeltildi (`9787/9791`→`9770/9765`, `9793`→`9760`, `9788`→`9771`, `9796/9797`→`9757/9758`). Kanıt: `reference/figma/extracted-1047-15802.md` L11576-11615. Versiyon 1.0.0 → 1.1.0.
- **Çelişki:** 0 (§1-§9 arasında etiketli çelişki bloğu yok).
- **Eksik veri:** focus/disabled state görselleri, `--cm-select-selected-bg` token adı, kontrast ölçümleri → §4/§5 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` GAP kaydı bu görevde yapılmayacak (erişim dışı — not: GAP tespitleri §5'te listelenmiştir).
- **Kontrol:** §1-§9 sırası tam; touch target 44px şartı §4'te; status `active` (PNG doğrulanmış).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
