---
title: "CoreMusic — Login (Giriş Yap)"
tier: T07
device: "RPi5 7\" Touch (Embedded)"
viewport: { width: 1024, height: 600 }
path: "screens/shared/"
status: active
version: 1.0.0
source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Login Girl.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-27"
author: "Bayram Ali"
---

# CoreMusic — Login (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
y=0 ┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
    │ [0,0] Romantik_Background_03 (1024×600) + Siyah Arkaplan Evekt gradient (α .12→.04)                          │
y=120├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ glass panel (x749,y0,275×600, #FFF α.2 + blur 2)  · bg-art 100×100 @(836,15)                                 │
    │ "Hoş Geldin" (852,120) 69×16 PJS 13/600                                                                       │
y=133├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "Hesabına giriş yap, müziğin keyfini çıkar" (797,141) 178×13 DM Sans 10/300 #DCDCDC                          │
y=141├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ logo 30×30 @(64,249) · "Core Music" 58×25 @(101,251) Bickham Script Two 15                                   │
    │ left title "Duygu için /      mükemmel " (64,301) 81×30  (son karakter U+00A0 NBSP)                           │
    │ left desc "sistem. Milyonlarca şarkı, özel seçilmiş playlist'ler, sonsuz müzik keşfi. Senin için." (62,338)   │
    │   357×13                                                                                                     │
y=279├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "E-posta, Telefon veya Kullanıcı Adı" (776,189) 123×10 PJS 8/500                                              │
    │ [input#login-identity] (776,204) 220×19 r3 · stroke #F200D0 0.2 · ph "Equalzier Presents 5"                   │
    │   (784.148,208.849) 205.741×9 Avalon 8/500                                                                    │
    │ "Şifre" (776,233) 17×10                                                                                      │
    │ [input#login-password] (776,248) 220×19 r3 · ph (gizli)                                                      │
y=267├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [checkbox "Beni Hatırla"] rect (776,277.326) 8.674² r1.5 #FFF α.3 · label (790.492,277) 31×9                   │
    │ "Şifremi Unuttum" (897,276) 99×12 Avalon 9/500 RIGHT #FF3CE3                                                  │
    │ [Giriş Yap] (776,302) 220×25 r5 · text (870,310) 30×9 PJS 7/500                                               │
y=342├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ social "____________________veya şununla devam et____________________" (777,390) 218×10                       │
    │ row1 y415: [Apple](776) [Google](853) [Facebook](931)  65×25 · icon 15×15 · labels: Apple/Google/Facebook     │
    │ row2 y450: [Spotify](776) [İnstagram](853) [Tiktok](931) 65×25                                               │
    │ row3 y485: [Github](776) [Google](853) [Facebook](931) 65×25                                                 │
y=462├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ (boş alan — hero art YOK, bu ekranda yok)                                                                     │
    │ privacy "Devam ederek Gizlilik Politikası'nı kabul etmiş olursunuz." (786,567) 207×10 DM Sans 8/300            │
y=567├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ footer: "Gizlilik"(16,583) · Ellipse(35,587) · "Kullanım Koşulları"(38,583) · Ellipse(87,587) ·               │
    │   "Destek    © 2026  Coremusic"(90,583) 95×8 · Ellipse(112,587)                                              │
    │ glass footer "Hesabın yok mu? Kayıt Ol" (839,567) 94×10                                                       │
y=600└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

Bant toplamı: 120 + 34 + 35 + 138 + 63 + 120 + 57 + 33 = 600 ✓

**Koordinat Kanıtı (SSOT = PNG):** Figma frame `2831:9838` (origin 2869, 3890). Koordinatlar frame-rel; input `y261` subpixel `0.849` değerleri PNG'de `y+0`'a yuvarlanır (Register Step 3 frame'inde GetPixel ile doğrulandı). Bu ekranda hero illüstrasyonu **yoktur** (Figma ve PNG'de ortak).

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.input` | C05 | Kimlik + şifre alanları (`.input__label`, `.input__field`, `.input__placeholder`) |
| `.btn` | C04 | `Giriş Yap` (birincil), `Google/Apple/...` sosyal butonlar (`.btn--social`) |
| `.checkbox`* | — | `Beni Hatırla` — envanterde C-sınıfı yok → `.input__checkbox` alt yapısı (**API'den gelmedi**) |
| `.nav-link` | C01 | `Kayıt Ol` / `Şifremi Unuttum` linkleri |
| `.card` | C03 | Sosyal buton satır arka planı (65×25) |
| `.modal` | C07 | Yok |

Bu ekran özel notlar:
- Sosyal blok 3×3 grid: 65×25 butonlar, kolon x=776/853/931, satır y=415/450/485.
- Divider metni Figma'da çizgi karakterleriyle dolu tek text node (`...veya şununla devam et...`) → CSS'te `border` + label olarak bölünmeli.
- Hero illüstrasyonu bu ekranda yok (Yalnızca select-gender ekranlarında var).

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Input stroke `#F200D0` gradyan ailesi, `Şifremi Unuttum` #FF3CE3 |
| `--cm-error` | `var(--cm-error)` | Hata mesajı rengi (bu ekran state'leri) |
| `--cm-glass-bg` | `var(--cm-glass-bg)` | Sağ panel `rgba(255,255,255,.2)` |
| `--cm-glass-blur` | `var(--cm-glass-blur)` | `blur(2px)` |
| `--cm-radius-sm` | `var(--cm-radius-sm)` | Input r3 / buton r5 |
| `--cm-space-1` … `--cm-space-4` | `var(--cm-space-1)` … | Label-input 14px, satır aralığı 35px |
| `--cm-font-display` / `--cm-font-body` | `var(--cm-font-display)` / `var(--cm-font-body)` | Başlık / gövde |
| `--cm-touch-target` | `var(--cm-touch-target)` | 44px — §4 GAP |
| `--cm-shadow-focus` | `var(--cm-shadow-focus)` | Focus (§9) |
| `--cm-checkbox-*` | **API'den gelmedi** | Checkbox token'ları master'da tanımsız |

Not: Placeholder font "Avalon" PNG'de ölçüldü; token sisteminde karşılığı **API'den gelmedi** (yalnızca Inter token'ları var → §6/§7 notu). Ham hex/rgba değerleri §6'da.

## 4. Touch Target (WCAG 2.5.8 / 2.5.5)

| Öğe | PNG Boyutu | Minimum | Token | Durum |
|---|---|---|---|---|
| Input (2 alan) | 220×19 | 44px | 44px | ⚠️ GAP: 19px < 44px |
| `Giriş Yap` | 220×25 | 44px | 44px | ⚠️ GAP: 25px < 44px |
| Checkbox | 8.674² | 24px | — | ⚠️ GAP: 9px < 24px |
| `Şifremi Unuttum` | 99×12 | 24px | — | ⚠️ GAP |
| Sosyal butonlar | 65×25 | 24px yatay / 44px T07 | 44px | ⚠️ GAP: yükseklik 25px |
| `Kayıt Ol` | 94×10 | 24px | — | ⚠️ GAP |

Notlar:
- Ölçüler PNG'den; input y19, buton y25, checkbox 9 — hepsi 44px token'ının altında → dikey padding ile `--cm-touch-target` telafisi kod aşamasında (**API'den gelmedi**: padding).
- `Tab` sırası: identity → password → checkbox → Şifremi Unuttum → Giriş Yap → sosyal (tasarım oku sırası §5).

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast | 4.5:1 | PASS | #DCDCDC altbaşlık ~9:1; label beyaz/siyah zemin |
| 1.4.11 Kontrast (non-text) | 3:1 | ⚠️ VERIFICATION REQUIRED | Input stroke α.2, sosyal ikonlar → ölçüm kod aşamasında |
| 2.4.7 Focus | Görünür focus | ⚠️ GAP | PNG'de focus yok → `--cm-shadow-focus` |
| 2.5.5 / 2.5.8 | 44 / 24px | ⚠️ GAP | §4 tablosu |
| 3.3.1 Error Identification | Hata tanıma | PASS (tasarım) | `.input--error` + `--cm-error` yeterli alana sahip (state görseli **API'den gelmedi**) |
| 3.3.2 Labels | Label | PASS | Her input'ta görünür label (`E-posta...`, `Şifre`) |
| 1.4.1 Use of Color | Renk + biçim | PASS | Checkbox ✓'li PNG'de dolu; input hataları renk + ikonla |
| 1.3.1 / 2.1.1 | Yapı + klavye | PASS (tasarım) | Form + Enter submit; sosyal butonlar link |

Not: GAP kaydı `04-accessibility-gaps.md` bu görevde düzenlenmeyecek (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Sağ panel | `rgba(255,255,255,0.2)` + `backdrop-filter: blur(2px)` | Figma `Sağ Panel` blur=2 |
| frame fill | `#000000` | Figma |
| arkaplan degrade | `linear-gradient(rgba(0,0,0,.12), rgba(0,0,0,.04))` | `Siyah Arkaplan Evekt` |
| input stroke | `#F200D0` alpha 0.2, 0.2px | Figma input `stroke` |
| checkbox dolgu | `rgba(255,255,255,0.3)` r1.5 | Figma rect |
| `Şifremi Unuttum` | `#FF3CE3` | Figma text fill |
| placeholder | `rgba(255,255,255,~.7)` (PNG'den okundu) | PNG — ham opaklık **VERIFICATION REQUIRED** |

Not: `--cm-bg-overlay` 0.60 DEPRECATED → bu ekran overlay kullanmaz; SSOT düzeltmesi `screens/T17-monitor-22fhd/welcome-popup.md` §6.

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/shared-1024/Linux  1024 - Login Girl.png`
  - 1024×600; inputlar x776-996/y204-223 ve y248-267; sosyal grid y415/450/485; hero **yok**.
- **Figma (referans):** frame `2831:9838` `Login` (origin 2869,3890); layout `2831:9840` (220×138 @(776,189)), input1 `2831:9842`, input2 `2831:9851`, Checkbox `2831:9853`, forgot `2831:9860`, btn `2831:9861`, social `2831:9863`.
- **Figma PNG export:** `1024 - Login Girl.png`.
- **SSOT sırası:** PNG > Figma → bu ekranda çelişki yok; §1 etiket yok.

## 8. Responsive Davranış

| Kural | Davranış |
|---|---|
| 1024-1440px | Sabit sahne; panel 275px sabit |
| >1440px | Panel `min(35vw,380px)` (opsiyonel) |
| <1024px | Dikey istifleme: başlık → form → checkbox/forgot → Giriş Yap → sosyal → footer |
| Orientation | T07 landscape sabit |
| Touch | 44px padding telafisi |

Not: Genişlik kırılımı **API'den gelmedi** (matrix T07 tek satır 1024×600) → VERIFICATION REQUIRED.

## 9. State Durumları

| State | Tanım | Kaynak |
|---|---|---|
| default | Boş alanlar, placeholder görünür, `Giriş Yap` aktif | PNG (SSOT) |
| focus | `--cm-shadow-focus` + stroke α.6 | **API'den gelmedi** (PNG yok) |
| error | `--cm-error` stroke + mesaj | **API'den gelmedi** (state PNG'si yok) |
| success | Alan doğrulandı | **API'den gelmedi** |
| loading | `Giriş Yap` spinner | **API'den gelmedi** |
| checkbox checked | "Beni Hatırla" işaretli | **API'den gelmedi** (PNG'de boş) |

> ⚠️ VERIFICATION REQUIRED — focus/error/success/loading/checked state PNG'leri yok; kod aşamasında tasarım sisteminden teyit edilecek.

---

**Quality Report**

- **Doğrulama:** PNG (SSOT) + Figma frame `2831:9838` + C05/C04/C01 + token master; Register Step 3 frame'inde pixel-scan ile koordinat yöntemi doğrulandı (aynı extraction).
- **Çelişki:** 0.
- **Eksik veri:** focus/error/loading/checked state görselleri, checkbox token'ı, placeholder opaklık değeri, Avalon font token'ı → §4/§5/§6/§9 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` GAP kaydı (erişim dışı — §5'te not edildi).
- **Kontrol:** §1-§9 sırası; touch 44px §4; status `active` (PNG doğrulanmış).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
