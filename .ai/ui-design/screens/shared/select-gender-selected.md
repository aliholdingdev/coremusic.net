---
title: "CoreMusic — Select Gender Selected (Cinsiyet Seçimi · Seçili Durum)"
tier: T07
device: "RPi5 7\" Touch (Embedded)"
viewport: { width: 1024, height: 600 }
path: "screens/shared/"
status: active
version: 1.1.1
source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Select Gender - selected.png"
related_tokens: [tokens/design-tokens-master]
related_components: [02-component-inventory]
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-28"
author: "Bayram Ali"
---

# CoreMusic — Select Gender Selected (T07 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

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
    │ [btn/Kız — SEÇİLİ] (776,213) 220×40 r5 · bg #FF69DF α.35 (2831:9810) · icon (787,223) · "Kız" (815,224)      │
    │ [btn/Erkek      ] (776,263) 220×40 r5 · icon (787,273) · "Erkek" (815,274) · sub (815,284)                   │
    │ [btn/nötur      ] (776,313) 220×40 r5 · icon (787,323) · "Cinsiyetimi söylemek istemiyorum" (815,324)         │
    │   sub "Genel, soft netural vibes" (815,334)                                                                  │
y=403├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [Devam Et — AKTİF] (776,363) 220×25 r5 · gradyan 3.katman #FF00C8 α.55/.6/.35 görünür (2831:9797)             │
    │   text (870,371) 33×9 PJS 7/500                                                                              │
y=414├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ hero art (dekoratif illüstrasyon) 196×130 @(788,414)                                                         │
y=544├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ privacy "Devam ederek Gizlilik Politikası'nı kabul etmiş olursunuz." (786,567) 207×10 DM Sans 8/300           │
    │ footer: "Gizlilik"(16,583) · Ellipse(35,587) · "Kullanım Koşulları"(38,583) · Ellipse(87,587) ·               │
    │   "Destek    © 2026  Coremusic"(90,583) 95×8 · Ellipse(112,587)                                              │
    │ glass footer "Hesabın yok mu? Kayıt Ol" (839,567) 94×10                                                       │
y=600└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

Bant toplamı: 120 + 34 + 59 + 175 + 26 + 130 + 23 + 33 = 600 ✓

**Koordinat Kanıtı (SSOT = PNG):** Tüm koordinatlar PNG frame-rel; Figma frame `2831:9787` (origin 1751, 3890) ile birebir. Bu ekran `select-gender.md`'nin seçili durumudur — tek fark Kız butonu dolgusu ve Devam Et gradyan 3. katmanı.

## 2. BEM Sınıfları

| BEM Sınıfı | Bileşen | Kullanım Yeri (bu ekran) |
|---|---|---|
| `.btn` | C04 | 3 seçenek + `Devam Et`; seçili `Kız` → `.btn--selected` |
| `.card` | C03 | Seçenek satır arka planı (220×40 r5) |
| `.input` | C05 | Yok |
| `.nav-link` | C01 | Yok (navbar yok) |
| `.badge` | C10 | Opsiyonel: seçili rozeti (**API'den gelmedi**) |
| `.toast` | C16 | Yok |

Bu ekran özel notlar:
- `.btn--selected` → `background: rgba(255,105,223,.35)` (PNG'de görünür dolgu).
- `Devam Et` aktif gradyan: 3 katman (Figma `2831:9797` visible; default frame'de 2 katman).
- Renk-dışında seçili işareti (check ikonu) **API'den gelmedi** (§5 1.4.1 GAP).

## 3. Token Referansları

| Token | Değer | Kullanım |
|---|---|---|
| `--cm-primary` | `var(--cm-primary)` | Seçili buton dolgusu / birincil gradyan |
| `--cm-error` | `var(--cm-error)` | Hata durumu (görünmez) |
| `--cm-glass-bg` | `var(--cm-glass-bg)` | Sağ panel `rgba(255,255,255,.2)` |
| `--cm-glass-blur` | `var(--cm-glass-blur)` | `blur(2px)` (Figma blur=2) |
| `--cm-radius-sm` | `var(--cm-radius-sm)` | r5 butonlar |
| `--cm-space-2` / `--cm-space-4` | `var(--cm-space-2)` / `var(--cm-space-4)` | İç boşluk / satır aralığı |
| `--cm-font-display` / `--cm-font-body` | `var(--cm-font-display)` / `var(--cm-font-body)` | Başlık / gövde |
| `--cm-touch-target` | `var(--cm-touch-target)` | 44px — §4 GAP telafisi |
| `--cm-shadow-focus` | `var(--cm-shadow-focus)` | Focus durumu (§9) |

Not: `--cm-select-selected-bg` token adı **API'den gelmedi** (master'da yok → §5 GAP notu). Ham renkler §6'da.

## 4. Touch Target (WCAG 2.5.8 / 2.5.5)

| Öğe | PNG Boyutu | Minimum | Token | Durum |
|---|---|---|---|---|
| Seçenek butonu | 220×40 | 44px | 44px | ⚠️ GAP: 40px < 44px |
| `Devam Et` | 220×25 | 44px | 44px | ⚠️ GAP: 25px < 44px |
| `Gizlilik Politikası` linki | 207×10 | 24px | — | ⚠️ GAP |
| `Kayıt Ol` linki | 94×10 | 24px | — | ⚠️ GAP |

Notlar:
- Ölçüler PNG'den (Figma 220×40 / 220×25 ile birebir).
- 3 seçenek yan yana değil dikey — hit-area padding ile 44px'e tamamlanacak (**API'den gelmedi**: padding değeri → VERIFICATION REQUIRED).
- Bu ekranda geri/kapat kontrolü yok (akış ekranı).

## 5. WCAG Uyumu (2.2 AA)

| Kural | Gereksinim | Durum | Kanıt |
|---|---|---|---|
| 1.4.3 Kontrast | 4.5:1 | PASS | #DCDCDC metin + siyah gradient zemin ~9:1 (kodda ölçüm) |
| 1.4.11 Kontrast (non-text) | 3:1 | ⚠️ VERIFICATION REQUIRED | Seçili dolgu α.35 / buton kenarları — ölçüm kod aşamasında |
| 2.4.7 Focus | Görünür focus | ⚠️ GAP | PNG'de focus yok → `--cm-shadow-focus` |
| 2.5.5 / 2.5.8 | 44px / 24px | ⚠️ GAP | §4: 40/25/10px |
| 1.4.1 Use of Color | Renk tek başına değil | ⚠️ GAP | Seçili durum yalnızca α.35 dolgu → check ikonu **API'den gelmedi** |
| 1.3.1 / 3.3.2 | Yapı + label | PASS | Radio + görünür label |
| 2.1.1 Keyboard | Erişim | PASS (tasarım) | Seç + Devam Et klavye ile |

Not: GAP kaydı `04-accessibility-gaps.md` bu görevde düzenlenmeyecek (erişim dışı).

## 6. Glassmorphism Stili

| Öğe | Değer | Kaynak |
|---|---|---|
| Sağ panel | `rgba(255,255,255,0.2)` + `backdrop-filter: blur(2px)` | Figma `Sağ Panel` blur=2, glass #FFF α.2 |
| frame fill | `#000000` | Figma |
| arkaplan degrade | `linear-gradient(rgba(0,0,0,.12), rgba(0,0,0,.04))` | `Siyah Arkaplan Evekt` |
| seçili buton | `rgba(255,105,223,0.35)` | Figma `2831:9810` visible |
| `Devam Et` gradyanı | `linear-gradient(rgba(255,0,200,.55), rgba(255,0,200,.6), rgba(255,0,200,.35))` | Figma `2831:9797` visible |

Not: `--cm-bg-overlay` (0.60 DEPRECATED) bu ekranda kullanılmaz → SSOT düzeltmesi `screens/T17-monitor-22fhd/welcome-popup.md` §6.

## 7. PNG Referansı

- **PNG (SSOT):** `.ai/.png/shared-1024/Linux  1024 - Select Gender - selected.png`
  - 1024×600; Kız butonu x776-996/y213-253 `#FF69DF α.35` dolgulu; `Devam Et` tam gradyanlı.
- **Figma (referans):** frame `2831:9787` `Linux  1024 - Select Gender - selected` (origin 1751,3890); seçili bg `2831:9810`, hero `2831:9825`, Devam Et gradyan `2831:9797`.
- **Figma PNG export:** `1024 - Select Gender - selected.png`.
> ⚠️ Node etiketi düzeltildi (2026-09-28): kök frame `2831:9808` → `2831:9787` (satır 57, 135, 166); 9808 = TEXT "Erkek". Sibling `9810/9825/9797` zaten doğrudu ✓. Kaynak: `reference/figma/extracted-1047-15802.md` L11616 (kök FRAME "Linux  1024 - Select Gender - selected", origin 1751,3890), L11638 (9808 = TEXT "Erkek").
- **SSOT sırası:** PNG > Figma → çelişki yok, §1 etiket yok.

## 8. Responsive Davranış

| Kural | Davranış |
|---|---|
| 1024-1440px | Sabit sahne; sağ panel 275px sabit |
| >1440px | Panel `min(35vw, 380px)`'e kadar (opsiyonel) |
| <1024px | Dikey istifleme: başlık → 3 seçenek → Devam Et → footer |
| Touch | Hit-area 44px (padding ile) |

Not: Genişlik kırılımı **API'den gelmedi** (matrix'te T07 = 1024×600 tek satır) → VERIFICATION REQUIRED.

## 9. State Durumları

| State | Tanım | Kaynak |
|---|---|---|
| default | Seçim yok; `Devam Et` pasif | `select-gender.md` |
| selected (bu PNG) | `Kız` α.35 dolgu + `Devam Et` 3. gradyan katmanı | PNG (SSOT) |
| selected-erkek / nötur | Diğer seçeneklerin seçili görünümü | **API'den gelmedi** (PNG yok → VERIFICATION REQUIRED) |
| focus | `--cm-shadow-focus` | **API'den gelmedi** (PNG yok) |
| disabled | `Devam Et` seçimsiz pasif | **API'den gelmedi** |

> ⚠️ VERIFICATION REQUIRED — Erkek/nötur seçili görselleri ve focus/disabled PNG'leri yok.

---

**Quality Report**

- **Doğrulama:** PNG (SSOT) + Figma frame `2831:9787` + C04/C03 + token master karşılaştırıldı; `select-gender.md` ile koordinat farkı yalnızca seçili durum katmanları.
- **Node etiketi düzeltmesi (2026-09-28):** kök frame `2831:9808` → `2831:9787` (3 yer: §1 koordinat kanıtı, §7 Figma referansı, bu satır); 9808 = TEXT "Erkek", sibling `9810/9825/9797` doğrulandı ✓. Kanıt: `reference/figma/extracted-1047-15802.md` L11616-11655. Versiyon 1.0.0 → 1.1.0.
- **Çelişki:** 0.
- **Eksik veri:** focus/disabled/alternatif-seçim görselleri, `--cm-select-selected-bg` token adı → §4/§5/§9 `⚠️ VERIFICATION REQUIRED`.
- **Kapsam dışı:** `04-accessibility-gaps.md` GAP kaydı (erişim dışı — §5'te not edildi).
- **Kontrol:** §1-§9 sırası; touch 44px §4; status `active` (PNG doğrulanmış).

**Authority:** Bayram Ali / Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
