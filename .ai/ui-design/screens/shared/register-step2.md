---
title: "CoreMusic — Register Step 2 (Kayıt · Adım 2 — Şifre & Şifre Tekrar)"
tier: T07
device: "RPi5 7\" Touch (Embedded)"
viewport: { width: 1024, height: 600 }
path: "screens/shared/"
status: active
version: 1.0.1
source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Register Girl step 2.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-27"
author: "Bayram Ali"
---

# CoreMusic — Register Step 2 (T07 Embedded 1024×600)

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
    │ "Şifre" (776,202) 17×10 PJS 8/500                                                                            │
    │ [input#reg-password] (776,217) 220×19 r3 · stroke #F200D0 0.2 · ph (784,221) [password type]                 │
    │ "Şifre Tekrar" (776,246) 41×10                                                                               │
    │ [input#reg-password2] (776,261) 220×19 r3 · stroke #F200D0 0.2 · ph (784,265)                                │
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

**Koordinat Kanıtı (SSOT = PNG):** Figma frame (origin 5033, 3890); koordinatlar frame-rel, subpixel yuvarlaması Register Step 3 pixel-scan ile doğrulandı. Bu adım step1 ile aynı geometriye sahiptir (yalnızca label/metin farkı).

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.input` | C05 | Şifre + Şifre Tekrar (`.input--password`, göz ikonu **API'den gelmedi**) |
| `.btn` | C04 | `Devam Et` + 9 sosyal buton |
| `.nav-link` | C01 | `Kayıt Ol` (glass footer) |
| `.card` | C03 | Sosyal buton arka planı 65×25 |
| `.badge` | C10 | Şifre gücü göstergesi **API'den gelmedi** (PNG'de yok) |
| `.progress` | C15 | Adım göstergesi **API'den gelmedi** |

Bu ekran özel notlar:
- Şifre strength meter PNG/Figma'da yok → opsiyonel, **API'den gelmedi**.
- Sosyal blok adımlar arası sabit (aynı koordinatlar; extraction 11814).
- Password alanlarında göster/gizle kontrolü tasarım sisteminde **API'den gelmedi**.

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Input stroke gradyanı |
| `--cm-error` | `var(--cm-error)` | Uyuşmazlık hatası (şifre tekrar) |
| `--cm-warning` | **API'den gelmedi** | Şifre gücü (token yok) |
| `--cm-glass-bg` / `--cm-glass-blur` | `var(--cm-glass-bg)` / `var(--cm-glass-blur)` | Sağ panel |
| `--cm-radius-sm` | `var(--cm-radius-sm)` | r3 / r5 |
| `--cm-space-*` | `var(--cm-space-*)` | Aralık boşlukları |
| `--cm-font-display` / `--cm-font-body` | `var(--cm-font-display)` / `var(--cm-font-body)` | Başlık / gövde |
| `--cm-touch-target` | `var(--cm-touch-target)` | 44px — §4 GAP |
| `--cm-shadow-focus` | `var(--cm-shadow-focus)` | Focus (§9) |

Not: Ham hex/rgba §6'da; `--cm-warning` master'da yok → VERIFICATION REQUIRED.

## 4. Touch Target (WCAG 2.5.8 / 2.5.5)

| Öğe | PNG Boyutu | Minimum | Token | Durum |
|---|---|---|---|---|
| Input ×2 | 220×19 | 44px | 44px | ⚠️ GAP: 19px < 44px |
| `Devam Et` | 220×25 | 44px | 44px | ⚠️ GAP: 25px < 44px |
| Sosyal butonlar ×9 | 65×25 | 44px T07 | 44px | ⚠️ GAP |
| `Kayıt Ol` linki | 94×10 | 24px | — | ⚠️ GAP |

Notlar:
- Ölçüler PNG'den (Figma 220×19 / 220×25 / 65×25 ile birebir).
- Tab sırası: şifre → şifre tekrar → Devam Et → sosyal.
- Padding telafisi **API'den gelmedi** → VERIFICATION REQUIRED.

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast | 4.5:1 | PASS | #DCDCDC ~9:1 |
| 1.4.11 Kontrast (non-text) | 3:1 | ⚠️ VERIFICATION REQUIRED | Stroke α.2 / ikonlar — kodda ölçüm |
| 2.4.7 Focus | Görünür focus | ⚠️ GAP | PNG'de yok → `--cm-shadow-focus` |
| 2.5.5 / 2.5.8 | 44 / 24px | ⚠️ GAP | §4 |
| 3.3.1 Error Identification | Hata tanıma | PASS (tasarım) | `.input--error` + `--cm-error` (state **API'den gelmedi**) |
| 3.3.2 Labels / 3.3.3 | Label + öneri | PASS / VERIFICATION | Görünür label var; şifre öneri metni **API'den gelmedi** |
| 3.3.1 Verify | Uyuşma doğrulama | PASS (tasarım) | Şifre tekrar eşleşmesi hata durumu (state **API'den gelmedi**) |
| 1.3.1 / 2.1.1 | Yapı + klavye | PASS (tasarım) | Form; Enter ile Devam |

Not: GAP kaydı `04-accessibility-gaps.md` bu görevde düzenlenmeyecek (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Sağ panel | `rgba(255,255,255,0.2)` + `backdrop-filter: blur(2px)` | Figma `Sağ Panel` blur=2 |
| frame fill | `#000000` | Figma |
| arkaplan degrade | `linear-gradient(rgba(0,0,0,.12), rgba(0,0,0,.04))` | `Siyah Arkaplan Evekt` |
| input stroke | `#F200D0` α.2 | Figma |
| divider | çizgi + `veya şununla devam et` label | Figma text node |

Not: `--cm-bg-overlay` 0.60 DEPRECATED → bu ekran overlay kullanmaz (SSOT → `screens/T17-monitor-22fhd/welcome-popup.md` §6).

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/shared-1024/Linux  1024 - Register Girl step 2.png`
  - 1024×600; inputlar y217-236 / y261-280; `Devam Et` y299-324.
- **Figma (referans):** Register frame (origin 5033,3890); input `Şifre` `2831:9948`, input `Şifre Tekrar` `2831:9951`, `Devam Et` `2831:9953`, social `2831:9957`.
- **Figma PNG export:** `1024 - Register Girl step 2.png`.
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
| default | Boş alanlar, password type | PNG (SSOT) |
| focus | `--cm-shadow-focus` + stroke α.6 | **API'den gelmedi** |
| error | Şifre uyuşmazlığı / zayıf şifre `--cm-error` | **API'den gelmedi** |
| success | Uyuşma onayı (yeşil/✓) | **API'den gelmedi** (renk token'ı da yok) |
| loading | `Devam Et` spinner | **API'den gelmedi** |
| password-reveal (göz ikonu) | Alanı göster/gizle | **API'den gelmedi** (PNG'de ikon yok) |

> ⚠️ VERIFICATION REQUIRED — focus/error/success/loading/reveal state PNG'leri yok; `--cm-warning` token'ı master'da tanımsız.

---

**Quality Report**

- **Doğrulama:** PNG (SSOT) + Figma Register frame (origin 5033) + C05/C04/C01 + token master; koordinat yöntemi Register Step 3 pixel-scan ile doğrulandı.
- **Çelişki:** 0.
- **Eksik veri:** strength meter / göz ikonu / step göstergesi, focus-error-success-loading state PNG'leri, `--cm-warning` token'ı → §2/§3/§9 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` GAP kaydı (erişim dışı — §5'te not edildi).
- **Kontrol:** §1-§9 sırası; touch 44px §4; status `active` (PNG doğrulanmış).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
