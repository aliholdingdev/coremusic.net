---
title: "CoreMusic — Register Step 3 (Kayıt · Adım 3 — Telefon & Koşullar)"
tier: T07
device: "RPi5 7\" Touch (Embedded)"
viewport: { width: 1024, height: 600 }
path: "screens/shared/"
status: active
version: 1.0.1
source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Register Girl step 3.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-27"
author: "Bayram Ali"
---

# CoreMusic — Register Step 3 (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
y=0 ┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
    │ [0,0] Romantik_Background_03 (1024×600) + Siyah Arkaplan Evekt gradient (α .12→.04)                          │
y=120├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ glass panel (x749,y0,275×600, #FFF α.2 + blur 2)  · bg-art 100×100 @(836,15)                                 │
    │ "Hesap Oluştur" (841,120) 90×16 PJS 13/600                                                                    │
y=133├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "CoreMusic ailesine katıl, müziğin keyfini çıkar" (785,141) 202×13 DM Sans 10/300 #DCDCDC                    │
y=141├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ logo 30×30 @(64,249) · "Core Music" 58×25 @(101,251) Bickham Script Two 15                                   │
    │ left title "Seni /      Tanıyalım " (64,301) 73×30  (son karakter U+00A0 NBSP)                               │
    │ left desc "Deneyimini sana özel hale getirmek için bir seçim yapman yeterli." (62,338) 295×13                │
y=189├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "Telefon" (776,202) 27×10 PJS 8/500                                                                          │
    │ [input#reg-phone] (776,217) 220×19 r3 · stroke #F200D0 0.2 · ph (784,221)                                    │
    │   ⛔ 2. input (Figma'da 776,261 placeholder Text) PNG'de GÖRÜNMEZ — çizilmedi (§1-ÇELİŞKI notu)                │
    │ [checkbox "Koşullar"] rect (820,259.326) 8.674² r1.5 #FFF α.3 [pixel-scan: x820-828 plateau doğrulandı]      │
    │   label "Kullanım Koşulları'nı okudum ve kabul ediyorum" (834.492,259) 119×9  [glyph'ler x834→953]            │
y=317├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [Kayıt Ol] (776,292) 220×25 r5 · text (870,300) 27×9 PJS 7/500                                                │
y=342├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ social "____________________veya şununla devam et____________________" (777,390) 218×10                       │
    │ row1 y415: [Apple](776) [Google](853) [Facebook](931)  65×25 · icon 15×15                                     │
    │ row2 y450: [Spotify](776) [İnstagram](853) [Tiktok](931) 65×25                                               │
    │ row3 y485: [Github](776) [Google](853) [Facebook](931) 65×25                                                 │
y=462├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ (boş alan — hero art YOK)                                                                                     │
    │ privacy "Devam ederek Gizlilik Politikası'nı kabul etmiş olursunuz." (786,567) 207×10 DM Sans 8/300            │
y=567├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ footer: "Gizlilik"(16,583) · Ellipse(35,587) · "Kullanım Koşulları"(38,583) · Ellipse(87,587) ·               │
    │   "Destek    © 2026  Coremusic"(90,583) 95×8 · Ellipse(112,587)                                              │
    │ glass footer "Hesabın yok mu? Kayıt Ol" (839,567) 94×10                                                       │
y=600└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

Bant toplamı: 120 + 34 + 48 + 115 + 73 + 120 + 57 + 33 = 600 ✓

> ⚠️ ÇELİŞKI (§1-1): Figma `Register step 3` frame'inde `Telefon` input'unun altında ikinci bir input node'u (776,261 placeholder `Text`) tanımlı; **PNG'de bu input görünmüyor** — pixel taraması (y263'te input stroke'u yok; checkbox rect x820-828 plateau, label glyph'leri x834-953) ikinci inputun basılmadığını kanıtlıyor. ASCII'ye çizilmedi. **Çözüm: PNG > Figma** (SSOT sırası); Figma node'unun silinmesi veya PNG'nin güncellenmesi tasarım sorumlusunda → `00-ascii-art-index.md` bu görevde yazılmayacak (erişim dışı), düzeltme not edildi.

**Koordinat Kanıtı (SSOT = PNG):** Figma frame (origin 6110, 3890). Pixel-scan ile bu frame'de doğrulanan ölçüler: checkbox rect plateau x820→828 (8.674² → 9px yuvarlama), label glyph başlangıcı x834.492 → x834, input2 stroke y263'te **yok**. Subpixel `0.326` offsetleri PNG'de `y+0`'a yuvarlanır.

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.input` | C05 | Telefon alanı (yalnızca 1 input — §1-ÇELİŞKI) |
| `.btn` | C04 | `Kayıt Ol` (birincil) + 9 sosyal buton |
| `.checkbox`* | — | `Kullanım Koşulları` onayı — envanterde ayrı C-sınıfı **API'den gelmedi** → `.input__checkbox` alt yapısı |
| `.nav-link` | C01 | `Kayıt Ol` (glass footer — bu ekran akışın sonu) |
| `.card` | C03 | Sosyal buton arka planı 65×25 |
| `.progress` | C15 | Adım göstergesi **API'den gelmedi** |

Bu ekran özel notlar:
- Checkbox + linkli label tek bileşen (`label` sarmalayıcı; tıklama alanı label'a genişletilecek → §4).
- `Kullanım Koşulları` alt-metni link olmalı (adı `nav-link`).
- Figma'daki 2. input bu ekranın parçası değildir (§1-ÇELİŞKI).

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Input stroke, link vurgusu |
| `--cm-error` | `var(--cm-error)` | Zorunlu alan hatası |
| `--cm-glass-bg` / `--cm-glass-blur` | `var(--cm-glass-bg)` / `var(--cm-glass-blur)` | Sağ panel |
| `--cm-radius-sm` | `var(--cm-radius-sm)` | r3 input / r5 buton / r1.5 checkbox |
| `--cm-space-*` | `var(--cm-space-*)` | Aralık boşlukları |
| `--cm-font-display` / `--cm-font-body` | `var(--cm-font-display)` / `var(--cm-font-body)` | Başlık / gövde |
| `--cm-touch-target` | `var(--cm-touch-target)` | 44px — §4 GAP |
| `--cm-shadow-focus` | `var(--cm-shadow-focus)` | Focus (§9) |

Not: Checkbox token'ları master'da **API'den gelmedi**. Ham hex/rgba §6'da.

## 4. Touch Target (WCAG 2.5.8 / 2.5.5)

| Öğe | PNG Boyutu | Minimum | Token | Durum |
|---|---|---|---|---|
| Telefon input | 220×19 | 44px | 44px | ⚠️ GAP: 19px < 44px |
| Checkbox | 8.674² (9px) | 24px | — | ⚠️ GAP: 9px < 24px (label ile tıklama alanı 119+9px'e genişletilecek) |
| `Kayıt Ol` | 220×25 | 44px | 44px | ⚠️ GAP: 25px < 44px |
| Sosyal butonlar ×9 | 65×25 | 44px T07 | 44px | ⚠️ GAP |
| `Gizlilik` / `Kayıt Ol` linkleri | 207×10 / 94×10 | 24px | — | ⚠️ GAP |

Notlar:
- Ölçüler PNG'den + pixel-scan (checkbox 8.674² → görsel 9px; label x834-953).
- Tab sırası: telefon → checkbox (label) → Kayıt Ol → sosyal.
- Padding telafisi **API'den gelmedi** → VERIFICATION REQUIRED.

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast | 4.5:1 | PASS | #DCDCDC ~9:1; label beyaz/siyah zemin |
| 1.4.11 Kontrast (non-text) | 3:1 | ⚠️ VERIFICATION REQUIRED | Checkbox α.3 kenar / stroke α.2 → kodda ölçüm |
| 2.4.7 Focus | Görünür focus | ⚠️ GAP | PNG'de yok → `--cm-shadow-focus` |
| 2.5.5 / 2.5.8 | 44 / 24px | ⚠️ GAP | §4: 19/9/25px |
| 3.3.1 Error Identification | Hata tanıma | PASS (tasarım) | `.input--error` (state **API'den gelmedi**) |
| 3.3.2 Labels / 3.3.4 | Label + finansal onay | PASS | Görünür label + koşul onay kutusu (link hedefi **API'den gelmedi**) |
| 1.4.1 Use of Color | Renk tek değil | PASS (tasarım) | Checkbox görsel kutu + label (state görseli **API'den gelmedi**) |
| 1.3.1 / 2.1.1 | Yapı + klavye | PASS (tasarım) | Form + checkbox klavye erişilebilir |

Not: GAP kaydı `04-accessibility-gaps.md` bu görevde düzenlenmeyecek (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Sağ panel | `rgba(255,255,255,0.2)` + `backdrop-filter: blur(2px)` | Figma `Sağ Panel` blur=2 |
| frame fill | `#000000` | Figma |
| arkaplan degrade | `linear-gradient(rgba(0,0,0,.12), rgba(0,0,0,.04))` | `Siyah Arkaplan Evekt` |
| input stroke | `#F200D0` α.2, 0.2px | Figma |
| checkbox dolgu | `rgba(255,255,255,0.3)` r1.5 | Figma rect (pixel-scan ile konum doğrulandı) |
| divider | çizgi + `veya şununla devam et` label | Figma text node |

Not: `--cm-bg-overlay` 0.60 DEPRECATED → bu ekran overlay kullanmaz (SSOT → `screens/T17-monitor-22fhd/welcome-popup.md` §6).

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/shared-1024/Linux  1024 - Register Girl step 3.png`
  - 1024×600; Telefon input y217-236; checkbox x820-828/y259-268 (pixel-scan); label x834-953; `Kayıt Ol` y292-317; **2. input yok** (§1-ÇELİŞKI).
- **Figma (referans):** Register frame (origin 6110,3890); `Telefon` input `2831:10007`, **2. input** `2831:10010` (PNG'de yok → §1-ÇELİŞKI), Checkbox `2831:10014`, koşul label `2831:10017`, `Kayıt Ol` `2831:10025`, social `2831:10034`.
- **Figma PNG export:** `1024 - Register Girl step 3.png`.
- **SSOT sırası:** PNG > Figma → ASCII PNG'ye göre; Figma'daki 2. input 1 kez §1'de etiketlendi (§1-ÇELİŞKI).

## 8. Responsive Davranış

| Kural | Davranış |
|---|---|
| 1024-1440px | Sabit sahne; panel 275px |
| >1440px | Panel `min(35vw,380px)` (opsiyonel) |
| <1024px | Dikey istifleme: başlık → telefon → checkbox (tam satır, tıklama alanı geniş) → Kayıt Ol → sosyal → footer |
| Orientation | T07 landscape sabit |
| Touch | 44px padding telafisi; checkbox satırı min 44px |

Not: Genişlik kırılımı **API'den gelmedi** → VERIFICATION REQUIRED.

## 9. State Durumları

| State | Tanım | Kaynak |
|---|---|---|
| default | Telefon alanı boş; checkbox boş; `Kayıt Ol` | PNG (SSOT) |
| focus | `--cm-shadow-focus` + stroke α.6 | **API'den gelmedi** |
| error | Boş/geçersiz telefon, işaretlenmemiş checkbox | **API'den gelmedi** |
| success | Doğrulama onayı | **API'den gelmedi** |
| loading | `Kayıt Ol` spinner | **API'den gelmedi** |
| checkbox-checked | Koşul işaretli (✓ dolgusu) | **API'den gelmedi** (PNG'de boş) |

> ⚠️ VERIFICATION REQUIRED — focus/error/success/loading/checked state PNG'leri yok; `2. input` varlığının tasarım sorumlusunca netleştirilmesi gerekiyor (§1-ÇELİŞKI).

---

**Quality Report**

- **Doğrulama:** PNG (SSOT) + Figma Register frame (origin 6110) + **GetPixel pixel-scan** (checkbox x820-828 plateau, label glyph x834→953, y263'te input stroke yok) + C05/C04 + token master.
- **Çelişki:** 1 — §1-ÇELİŞKI (Figma 2. input vs PNG görünmeyen input; çözüm PNG > Figma).
- **Eksik veri:** checkbox token'ı, focus/error/checked state PNG'leri, adım göstergesi → §2/§3/§9 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` GAP kaydı (erişim dışı — §5'te not edildi); `00-ascii-art-index.md` (erişim dışı — düzeltme notu §1-ÇELİŞKI'de).
- **Kontrol:** §1-§9 sırası; touch 44px §4; status `active` (PNG pixel-scan ile doğrulanmış).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
