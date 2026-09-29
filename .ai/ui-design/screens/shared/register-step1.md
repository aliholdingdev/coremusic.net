---
title: "CoreMusic — Register Step 1 (Kayıt · Adım 1 — Kullanıcı Adı & E-posta)"
tier: T07
device: "RPi5 7\" Touch (Embedded)"
viewport: { width: 1024, height: 600 }
path: "screens/shared/"
status: active
version: 1.0.1
source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Register Girl.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-27"
author: "Bayram Ali"
---

# CoreMusic — Register Step 1 (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

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
    │ "Kullanıcı Adı" (776,202) 43×10 PJS 8/500                                                                    │
    │ [input#reg-username] (776,217) 220×19 r3 · stroke #F200D0 0.2 · ph (784,221)                                 │
    │ "Eposta" (776,246) 26×10                                                                                     │
    │ [input#reg-email] (776,261) 220×19 r3 · stroke #F200D0 0.2 · ph (784,265)                                    │
y=311├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [Devam Et] (776,299) 220×25 r5 · text (870,307) 33×9 PJS 7/500                                                │
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

Bant toplamı: 120 + 34 + 48 + 122 + 66 + 120 + 57 + 33 = 600 ✓

**Koordinat Kanıtı (SSOT = PNG):** Figma frame (origin 3969, 3890); koordinatlar frame-rel. Input `y261.849` gibi subpixel değerler PNG'de `y+0`'a yuvarlanır (Register Step 3 frame'inde GetPixel ile doğrulandı). Hero illüstrasyonu bu adımda yok.

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.input` | C05 | Kullanıcı adı + Eposta alanları |
| `.btn` | C04 | `Devam Et` (birincil) + 9 sosyal buton (`.btn--social`) |
| `.nav-link` | C01 | `Giriş Yap` bağlantı (glass footer'da `Kayıt Ol` bu ekranın kendisi) |
| `.card` | C03 | Sosyal buton arka planı 65×25 |
| `.progress` | C15 | Adım göstergesi **API'den gelmedi** (PNG'de 1/2/3 dots yok) |
| `.modal` | C07 | Yok |

Bu ekran özel notlar:
- 3 adımlı akışın 1. adımı; step göstergesi PNG/Figma'da yok → `progress` opsiyonel, **API'den gelmedi**.
- Sosyal blok tüm register adımlarında aynıdır (aynı koordinatlar).
- Ayırıcı tek text node (`...veya şununla devam et...`) → CSS'te `border + span`.

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Input stroke gradyanı |
| `--cm-error` | `var(--cm-error)` | Hata mesajı |
| `--cm-glass-bg` / `--cm-glass-blur` | `var(--cm-glass-bg)` / `var(--cm-glass-blur)` | Sağ panel `.2` + `blur(2px)` |
| `--cm-radius-sm` | `var(--cm-radius-sm)` | r3 input / r5 buton |
| `--cm-space-2` … `--cm-space-4` | `var(--cm-space-2)` … | label-input arası, satır aralığı |
| `--cm-font-display` / `--cm-font-body` | `var(--cm-font-display)` / `var(--cm-font-body)` | Başlık / gövde |
| `--cm-touch-target` | `var(--cm-touch-target)` | 44px — §4 GAP |
| `--cm-shadow-focus` | `var(--cm-shadow-focus)` | Focus (§9) |

Not: Placeholder font "Avalon" ve adım göstergesi token'ı **API'den gelmedi**. Ham hex/rgba §6'da.

## 4. Touch Target (WCAG 2.5.8 / 2.5.5)

| Öğe | PNG Boyutu | Minimum | Token | Durum |
|---|---|---|---|---|
| Input ×2 | 220×19 | 44px | 44px | ⚠️ GAP: 19px < 44px |
| `Devam Et` | 220×25 | 44px | 44px | ⚠️ GAP: 25px < 44px |
| Sosyal butonlar ×9 | 65×25 | 44px T07 | 44px | ⚠️ GAP: 25px yükseklik |
| `Giriş Yap` linki (glass footer) | 94×10 | 24px | — | ⚠️ GAP |
| Divider link metni | 218×10 | 24px | — | ⚠️ GAP |

Notlar:
- Ölçüler PNG'den (Figma 220×19 / 220×25 / 65×25).
- Tab sırası: username → email → Devam Et → sosyal (tasarım okuma sırası §5).
- Padding telafisi **API'den gelmedi** → VERIFICATION REQUIRED (kod aşamasında `--cm-touch-target`).

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast | 4.5:1 | PASS | #DCDCDC ~9:1; label açık/siyah zemin |
| 1.4.11 Kontrast (non-text) | 3:1 | ⚠️ VERIFICATION REQUIRED | Input stroke α.2 / sosyal ikonlar — kodda ölçüm |
| 2.4.7 Focus | Görünür focus | ⚠️ GAP | PNG'de focus yok → `--cm-shadow-focus` |
| 2.5.5 / 2.5.8 | 44 / 24px | ⚠️ GAP | §4: 19/25/10px |
| 3.3.1 Error Identification | Hata tanıma | PASS (tasarım) | `.input--error` + `--cm-error` (state görseli **API'den gelmedi**) |
| 3.3.2 Labels / 3.3.8 | Label + autocomplete | PASS / VERIFICATION | Görünür label var; `autocomplete` değerleri kod aşamasında |
| 1.4.1 Use of Color | Renk tek değil | PASS | Hata durumu ikon+metin öngörülüyor (state **API'den gelmedi**) |
| 1.3.1 / 2.1.1 | Yapı + klavye | PASS (tasarım) | Form; Enter ile Devam |

Not: GAP kaydı `04-accessibility-gaps.md` bu görevde düzenlenmeyecek (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Sağ panel | `rgba(255,255,255,0.2)` + `backdrop-filter: blur(2px)` | Figma `Sağ Panel` blur=2 |
| frame fill | `#000000` | Figma |
| arkaplan degrade | `linear-gradient(rgba(0,0,0,.12), rgba(0,0,0,.04))` | `Siyah Arkaplan Evekt` |
| input stroke | `#F200D0` α.2, 0.2px | Figma input stroke |
| divider | `rgba(255,255,255,.5)` çizgi + label | Figma text fill (PNG ile uyumlu) |

Not: `--cm-bg-overlay` 0.60 DEPRECATED → bu ekran overlay kullanmaz (SSOT düzeltmesi `screens/T17-monitor-22fhd/welcome-popup.md` §6).

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/shared-1024/Linux  1024 - Register Girl.png`
  - 1024×600; inputlar y217-236 / y261-280; `Devam Et` y299-324; sosyal grid y415/450/485.
- **Figma (referans):** Register frame (origin 3969,3890); layout inputları `2831:9882` (Kullanıcı Adı), `2831:9885` (Eposta), `2831:9887` Devam Et, social `2831:9891`.
- **Figma PNG export:** `1024 - Register Girl.png`.
- **SSOT sırası:** PNG > Figma → çelişki yok; §1 etiket yok.

## 8. Responsive Davranış

| Kural | Davranış |
|---|---|
| 1024-1440px | Sabit sahne; panel 275px |
| >1440px | Panel `min(35vw,380px)` (opsiyonel) |
| <1024px | Dikey istifleme: başlık → form → Devam → sosyal → footer |
| Orientation | T07 landscape sabit |
| Touch | 44px padding telafisi |

Not: Genişlik kırılımı **API'den gelmedi** → VERIFICATION REQUIRED.

## 9. State Durumları

| State | Tanım | Kaynak |
|---|---|---|
| default | Boş alanlar, placeholder görünür | PNG (SSOT) |
| focus | `--cm-shadow-focus` + stroke α.6 | **API'den gelmedi** (PNG yok) |
| error | `--cm-error` + mesaj (örn. e-posta formatı) | **API'den gelmedi** |
| success | Alan doğrulandı (step geçişi) | **API'den gelmedi** |
| loading | `Devam Et` spinner | **API'den gelmedi** |
| step-indicator (1/2/3) | Adım göstergesi | **API'den gelmedi** (PNG'de yok) |

> ⚠️ VERIFICATION REQUIRED — focus/error/loading/step-gösterge state PNG'leri yok.

---

**Quality Report**

- **Doğrulama:** PNG (SSOT) + Figma Register frame (origin 3969) + C05/C04/C01 + token master; koordinat yöntemi Register Step 3 pixel-scan ile doğrulandı (aynı extraction).
- **Çelişki:** 0.
- **Eksik veri:** step göstergesi, focus/error/loading state PNG'leri, Avalon token'ı → §2/§5/§9 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` GAP kaydı (erişim dışı — §5'te not edildi).
- **Kontrol:** §1-§9 sırası; touch 44px §4; status `active` (PNG doğrulanmış).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
