---
title: "CoreMusic — Karşılama (Welcome) Popup"
tier: T17-monitor-22fhd
device: "Desktop Monitor (Mouse Tier)"
viewport: { width: 1920, height: 1080 }
path: "/home (welcome-modal)"
status: draft
version: 1.1.1
source_of_truth: "⚠️ VERIFICATION REQUIRED — PNG bekleniyor"
related_tokens:
  - "--cm-font-family-heading"
  - "--cm-font-family-body"
  - "--cm-text-white"
  - "--cm-pink-primary-button"
  - "--cm-radius-md"
  - "--cm-radius-sm"
  - "--cm-shadow-focus"
  - "--cm-z-welcome"
related_components:
  - "C07 Modal (kategori — welcome-modal envanterde YOK)"
wcag_target: "WCAG 2.2 AA"
last_verified: "2026-09-28"
author: "Bayram Ali (Vault Steward)"
---

# CoreMusic — Welcome Popup (T17 Desktop Monitor 1920×1080)

**Zorunlu bağlantılar:**
- [[01-mockup-index.md]]
- [[02-component-inventory.md]]
- [[04-accessibility-gaps.md]]
- [[../../tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1920, y:0-1080)

> **Kaynak lejantı:** `[PNG]` = PNG piksel doğrulaması · `[Figma]` = Figma node verisi
> (`2831:10267` "Welcome Div" export'u) · `[türetilmiş]` = T17 1920×1080 yerleşimi iki
> kaynaktan türetilmiştir — **gerçek T17 PNG hâlâ yok** (`source_of_truth` §7).
> **Node etiketi düzeltildi (2026-09-28):** `2831:13747` "Welcome Div" yazımı → `2831:13747` = FRAME "Linux - 1920 - Home" (origin 606,−589); "Welcome Div" = GROUP `2831:10267` (1024×601). Kaynak: `reference/figma/extracted-1920.md` L9 (FRAME), L529 (GROUP).
>
> **Koordinat sistemi:** Her iki kutu da kendi sol-üst köşesi (0,0)'a göredir.
> Kutu A = T17 viewport (1920×1080), Kutu B = Figma asset (1024×601).

**Kutu A — T17 Viewport (1920×1080) — `[türetilmiş]` + `[PNG]` overlay alpha**

```
x:0         240       480       720       960      1200      1440      1680    1919
  ┌──────────┬─────────┬─────────┬─────────┬─────────┬─────────┬─────────┬───────┐ y:0
  │▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│
  │▓  OVERLAY — rgba(0,0,0,0.35) + backdrop blur(3px)  [PNG: A=89 → 0.35 ✓]  ▓│ y:24
  │▓  (full-frame, türetilmiş: asset 1024×601 → T17 viewport'a ölçeklendi)   ▓│
  │▓                                                                      ▓│
  │▓                       ┌─ ÜST BANT: y0…386 ─────────────────────────┐  ▓│
  │▓                       │  (overlay devam — modal yok)              │  ▓│ y:288
  │▓                       └───────────────────────────────────────────┘  ▓│
  │▓░░░░░░░░░░░░░░░░░░░░░┌─ MODAL BANDI: y386…694 (600×308, x660…1260)─┐░░░│ y:386
  │▓░░░░░░░░░░░░░░░░░░░░░│ ████ logo 195×130 @x863,y399 [Figma]      │░░░│
  │▓░░░░░░░░░░░░░░░░░░░░░│           ┌── portrait 171×242 ──┐        │░░░│ y:458
  │▓░░░░░░░░░░░░░░░░░░░░░│  "Hoş gelidn" 66×23 @x934,y479  │ @x1089, │░░░│
  │▓░░░░░░░░░░░░░░░░░░░░░│  [PNG+Figma: hata aynen "gelidn"]│ y452…694│░░░│ y:530
  │▓░░░░░░░░░░░░░░░░░░░░░│  "Prenses Işıl Peri" 118×25     │ [Figma] │░░░│
  │▓░░░░░░░░░░░░░░░░░░░░░│        @x901,y522                │         │░░░│
  │▓░░░░░░░░░░░░░░░░░░░░░│  "Merhaba, ben ışıl...» 365×26   │  α0.8   │░░░│ y:602
  │▓░░░░░░░░░░░░░░░░░░░░░│        @x778,y580                │  r[0,8, │░░░│
  │▓░░░░░░░░░░░░░░░░░░░░░│  [ Başla ] 105×25 @x908,y634    │  8,0]   │░░░│ y:674
  │▓░░░░░░░░░░░░░░░░░░░░░└─────────────────────────────────┴─────────┘░░░│
  │▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓ ALT BANT: y694…1080 (overlay devam) ▓▓▓▓▓▓▓▓▓▓│ y:746
  │▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓│ y:1032
  └───────────────────────────────────────────────────────────────────────┘ y:1079
```

**Kutu B — Figma Asset (1024×601) — `[PNG]` + `[Figma]` (kopya export, MD5 aynı)**

```
x:0        128       256       384       512       640       768       896  1023
  ┌─────────┬─────────┬─────────┬─────────┬─────────┬─────────┬─────────┬─────┐ y:0
  │▒▒▒▒▒▒▒▒▒▒▒▒▒▒ OVERLAY rgba(0,0,0,0.35) [PNG: A=89=0.35×255 ✓] ▒▒▒▒▒▒▒▒│
  │▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒▒│ y:150
  │▒▒▒▒▒▒▒▒▒┌─ MODAL 600×308 @x212,y146 [Figma 2876:6439] ──────────┐▒▒▒▒│
  │▒▒▒▒▒▒▒▒▒│        ████ logo 195×130 @x415,y159                    │▒▒▒▒│ y:300
  │▒▒▒▒▒▒▒▒▒│  "Hoş gelidn" 66×23 @x486,y239   ┌ portrait 171×242 ┐ │▒▒▒▒│
  │▒▒▒▒▒▒▒▒▒│  "Prenses Işıl Peri" 118×25 @x453 │ @x641,y212…454  │ │▒▒▒▒│ y:450
  │▒▒▒▒▒▒▒▒▒│  "Merhaba, ben ışıl...» 365×26    │   α0.8 r[0,8,   │ │▒▒▒▒│
  │▒▒▒▒▒▒▒▒▒│        @x330,y340                 │    8,0]         │ │▒▒▒▒│
  │▒▒▒▒▒▒▒▒▒│  [ Başla ] 105×25 @x460,y394      └─────────────────┘ │▒▒▒▒│ y:600
  │▒▒▒▒▒▒▒▒▒└──────────────────────────────────────────────────────┘▒▒▒▒│
  └─────────┴─────────┴─────────┴─────────┴─────────┴─────────┴─────────┴─────┘ y:600
```

**Mutlak koordinat tablosu (Kutu B — asset 1024×601, `[Figma]`):**

| Öğe | Node | x | y | w × h |
|---|---|---:|---:|---|
| Overlay | `2831:10268` | 0 | 0 | 1024 × 601 |
| Modal | `2876:6439` | 212 | 146 | 600 × 308 |
| Logo | `2831:10278` | 415 | 159 | 195 × 130 |
| Başlık "Hoş gelidn" | `2831:10280` | 486 | 239 | 66 × 23 |
| İsim | `2831:10281` | 453 | 282 | 118 × 25 |
| Paragraf | `2831:10279` | 330 | 340 | 365 × 26 |
| Başla butonu | `2831:10274` | 460 | 394 | 105 × 25 |
| Portrait | `2831:10277` | 641 | 212 | 171 × 242 |

> **⚠️ ÇELİŞKI §1-1 (yerleşim/boyut — en iyi bölüm: §1):**
> **Kırılım:** Figma'da popup, `2831:10267` "Welcome Div" (1024×601) GROUP'unda
> tanımlı (abs(1751,−339) → `2831:13747` FRAME'e göreli (1145,250)); bu 14 çocuklu
> T17 frame'inin çocuğu DEĞİL — 1145+1024 = 2169 > 1920 olduğu için çerçeveye
> sığmıyor (249px taşma). PNG export'u çerçeve bağlamı taşımaz.
> **SSOT sırası:** PNG > Figma > ASCII. **Etkilenen bölüm:** §1 (bu kutular).
> **Çözüm:** Kutu B (asset) PNG+Figma bir-eli-gerçek; Kutu A (T17 1920×1080
> yerleşimi) **türetilmiştir** (overlay tam viewport + modal ortalanmış @x660,y386).
> T17'ye gerçek yerleşim **`⚠️ VERIFICATION REQUIRED`** — gerçek T17 popup PNG'si
> gelince doğrulanacak (§7).
>
> **⚠️ ÇELİŞKI §1-2 (metin — en iyi bölüm: §1):**
> **Kırılım:** PNG piksel analizi başlığın "Hoş **gelidn**" olduğunu kanıtladı
> (`l` x100-102 · `i` x107-108 nokta y22-26 · `d` x114-126 · `n` x130-140,
> ascendersiz); Figma `2831:10280` metni de birebir `"Hoş gelidn   "` (3 boşluk).
> Buna karşın proje dokümanları "hoş geldin" yazar: `01-mockup-index.md` L68
> "Welcome Popup (hoş geldin)" ve `_home-components.css` L791 yorumu
> `PNG: "Hoş geldin"` — **PNG'nin aslında ne olduğunu yanlış söyleyen bir yorum.**
> **SSOT sırası:** PNG > Figma > indeks/yorum. **Etkilenen bölüm:** §1.
> **Çözüm:** Her iki birinci-eli kaynak da aynı yazımı ("gelidn") verdiği için bu
> bir PNG↔Figma çelişkisi değil, **tasarım hatası + doküman hatası**dır. Spec,
> SSOT gereği yazımı **aynen "Hoş gelidn"** belgeler; düzeltilmesi tasarım
> sorumlusuna aittir (metin düzeltilirse Figma+PNG birlikte güncellenmeli).

**Notlar (etiketsiz — çelişki değil):**
- Başlık ve logo kutuları üst üste biniyor (logo 159…289, başlık 239…262 dikey
  örtüşme) — her iki kaynakta da böyle; görsel olarak logo şeffaf alanıyla çakışıyor.
- Figma'da `welcome-modal__input` ve `__user` karşılığı node YOK (sadece CSS'te var)
  → API'den gelmedi / `⚠️ VERIFICATION REQUIRED`.
- Overlay alpha, PNG pikselinde A=89 = 0.349×255 olarak ölçüldü (0.35 ✓).

---

## 2. BEM Sınıfları

> **Kaynak:** `assets.coremusic.net/Css/05_Pages/_home-components.css` L710–L898 + `welcome-modal.js`
> (kod = çalışma zamanı gerçeği) · Kod sınırı: sadece CSS'te tanımlı sınıflar.

| # | BEM Sınıfı | HTML Rolü | Stil Kaynağı | Env. ID |
|---|---|---|---|---|
| 1 | `.welcome-modal-overlay` | Tam ekran karartma + blur (root, fixed) | `_home-components.css` L711 | — (ekran özel) |
| 2 | `.welcome-modal` | Modal kabuğu (600×308, r8) | `_home-components.css` L731 | — (ekran özel) |
| 3 | `.welcome-modal__logo-img` | Karşılama logosu (195×130) | `L748` | — (ekran özel) |
| 4 | `.welcome-modal__title` | Başlık "Hoş gelidn" | `L755` | — (ekran özel) |
| 5 | `.welcome-modal__user` | İsim "Prenses Işıl Peri" (CSS var, Figma node yok) | `L761` | — (ekran özel) |
| 6 | `.welcome-modal__desc` | Açıklama paragrafı | `L767` | — (ekran özel) |
| 7 | `.welcome-modal__input` | Hidden odak hedefi (JS focus trap) | `L768` | — (ekran özel) |
| 8 | `.welcome-modal__btn` | "Başla" CTA | `L788` | — (ekran özel) |

> **Envanter eşleşmesi:** Yok. `02-component-inventory.md` C07 "Modal" kategori
> kaydı (`02-component-inventory.md` L91–97) — bu ekranın sınıfları envantere
> **kaydedilmemiş** → `⚠️ VERIFICATION REQUIRED`. Kategori ID'si: **C07 Modal**.
>
> **Not:** T07-embedded task'ında `.welcome-modal__overlay` adı kullanılmış;
> CSS gerçeği `.welcome-modal-overlay` (**`__` yok, block-name tek parça**).
> Bu spec, kod gerçeğini esas alır → `⚠️ VERIFICATION REQUIRED` (T07 çıktısıyla
> çakışabilir).
>
> **Not:** CSS'te `__close`/`.welcome-modal__close` YOK — L774–775 yorumu
> "PNG'de görünmeyen öğe olarak kaldırıldı"; kapanış Başla tıklama + Escape.

---

## 3. Token Referansları

> **Kaynak:** `design-tokens-master.md` (token adları) + `_home-components.css`
> (çalışan değerler). Sadece token'a karşılık gelen değerler tabloya alındı;
> inline rgba'lar "Inline Değerler" sütununda.

| Token / Kullanım | CSS Değişkeni | Değer | Kaynak |
|---|---|---|---|
| Modal başlık fontu | `var(--cm-font-family-heading)` | Arima, serif | `welcome-popup` Figma + token |
| Gövde fontu | `var(--cm-font-family-body)` | DM Sans / PJS | token master |
| Başlık rengi (beyaz) | `var(--cm-text-white)` | `#FFFFFF` | token master |
| CTA gradient (pink) | `var(--cm-pink-primary-button)` | `rgba(255,0,200,…) → #FF00C8` | `design-tokens-master.md` L150 |
| Modal radius | `var(--cm-radius-md)` | `8px` | `design-tokens-master.md` L362 |
| Buton radius | `var(--cm-radius-sm)` | `3px` | token master |
| Focus halkası | `var(--cm-shadow-focus)` | `0 0 0 3px rgba(255,255,255,.5)` | `design-tokens-master.md` L414 |
| Z-index (popup) | `var(--cm-z-welcome)` | `900` | `design-tokens-master.md` L505 |

**Inline Değerler (token yok — kod içi):**

| Kullanım | Değer | Kaynak |
|---|---|---|
| Overlay karartma | `rgba(0,0,0,0.35)` | Figma `2831:10268` + PNG A=89 ✓ (master SSOT L162–169) |
| Overlay blur | `blur(3px)` | Figma BACKGROUND_BLUR 3 (master SSOT) |
| Modal z-index (kod) | `1050` | `_home-components.css` L747 — token `--cm-z-welcome:900` ile farklı; header `--cm-z-header:1000` üstünde kalması için 1050 gerekli (not, çelişki değil) |
| Overlay z-index (kod) | `1040` | `_home-components.css` L725 |
| Modal iç overlay | `rgba(255,255,255,.10)` (`::before`) | L737–746 |
| Modal border | `rgba(255,255,255,.21)` | L741; Figma `2831:10272` stroke `#FFF` α.45 (PNG: border ≈0.25 → kod 0.21 daha yakın) |
| Buton gölgesi | `0 1px 1px rgba(0,0,0,.8)` | Figma `2831:10274` |

---

## 4. Touch Target

| Öğe | Ölçü (x × y) | Minimum | Sonuç | Not |
|---|---|---:|---|---|
| Başla butonu | 105 × 25 px | 24×24 (AA) / 44×44 (AAA) | ✅ PASS (AA) | 25px yükseklik AA'nın 24px eşiğinin üstünde; AAA/ergonomi için dikey `padding` ile 32px'e çıkarılabilir (T17 önerisi 32×32) |
| Kapatma (×) | — | — | n/a | PNG'de yok; JS L13 `__close` kaldırıldı — kapanış Başla + Escape |

> **Tier:** T17 = mouse tier (`ui-design/tiers.md`) — hover etkileşimi serbest,
> dokunma hedefi zorunlu değil; yine de AA 2.5.8 (24px) karşılanıyor.
> Sadece `welcome-modal__btn` tıklanabilir öğe; `__input` gizli odak hedefi
> (erişilebilir ama görünür değil → §9 state 6).

---

## 5. WCAG Uyumu

| Kriter | Gereklilik | Uygulama (bu ekran) | Sonuç |
|---|---|---|---|
| 1.4.3 Kontrast (Min) | Metin ≥ 4.5:1 (≥3:1 large) | Başlık beyaz (255,255,255) üzerine PNG ölçümlü fon (235,190,140) → **≈1.7:1**; paragraf beyaz üzerine (88,144,139) → **≈3.7:1** | ❌ **GAP — 2 metin öğesi** (§7 doğrulama notu: overlay öncesi arka plan fotoğrafı; alpha 0.35 sonrasında ölçüm fon-fotoğraf konumuna göre değişir) |
| 2.1.1 Klavye | Tüm işlevler klavyeyle | Focus trap (JS L129), Escape kapanış (L12), Başla Enter/Space | ✅ PASS |
| 2.4.7 Odak (Görünür) | Odak göstergesi | `:focus-visible` → `var(--cm-shadow-focus)` | ✅ PASS |
| 1.4.11 Non-text Contrast | UI bileşenleri ≥3:1 | Buton gradient + beyaz metin; border 0.21 alpha fotoğraf üzerinde | ⚠️ `⚠️ VERIFICATION REQUIRED` (gradient konumu foto-ye bağlı) |
| 4.1.2 Ad/Rol/Değer | ARIA | `role="dialog"` `aria-modal="true"` (JS L16–17), buton `aria-label="Hoş geldin popupını kapat"` (L14) | ✅ PASS |

> Eksiklerin tümü → `04-accessibility-gaps.md` dosyasına işlenecek
> (**bu dosya düzenlenmeyecek — kapsam dışı**).

---

## 6. Glassmorphism Stili

> **Kaynak:** `design-tokens-master.md` L162–169 (SSOT: overlay 0.35 + blur 3) ·
> Figma `2831:10268` (0.35 + BACKGROUND_BLUR 3) · PNG ölçümü (A=89=0.35 ✓).

| Katman | Değer (SSOT) | Kaynak |
|---|---|---|
| Overlay karartma | `rgba(0,0,0,0.35)` | Figma `2831:10268` + PNG piksel (A=89 → 0.349) |
| Overlay blur | `blur(3px)` (background-blur) | Figma node efekti |
| Modal cam yüzey | Fotoğraf fonu (`welcome-popup-girl.png`) + `::before` `rgba(255,255,255,.10)` + border `rgba(255,255,255,.21)` | Kod L737–746; Figma: img α0.8 + beyaz α0.45 katman + stroke |

**⚠️ ÇELİŞKI §6-1 (glass overlay — en iyi bölüm: §6):**
- **Kırılım:** SSOT **0.35 + blur(3px)** (`design-tokens-master.md` L162–169;
  Figma `2831:10268`: `opacity 0.35`, `BACKGROUND_BLUR 3`; PNG: A=89 = 0.35×255 ✓)
  ↔ **deprecated** `--cm-bg-overlay: rgba(0,0,0,0.60)` (`design-tokens-master.md`
  L68 "60% siyah, DEPRECATED") + envanter `02-component-inventory.md` C07
  "overlay: 60% black" + kod `_home-components.css` L715 `backdrop-filter: blur(1.5px)` ≠ 3.
- **SSOT sırası:** PNG > Figma > kod/deprecated.
- **Etkilenen §:** §6.
- **Çözüm:** 0.35 + blur 3 esas (PNG+Figma bir-eli). `0.60` deprecated &
  envanter C07 kaydı yanlıştır; `blur(1.5px)` kod değeri tasarımla çelişir →
  master L169 bu düzeltmeyi zaten `screens/T17-monitor-22fhd/welcome-popup.md`
  §6'ya yönlendirmiş (bu spec = kayıt yeri).
  (`_home-components.css` L714 alpha'sı 0.35 ile SSOT'la uyumlu — sadece blur
  ve deprecated kayıtlar sorunlu.)

---

## 7. PNG Referansı

| # | Dosya | Boyut | Erişim |
|---|---|---|---|
| — | **T17 gerçek popup PNG'si** | — | **`⚠️ VERIFICATION REQUIRED — PNG bekleniyor`** (`home-1920/Linux - 1920 - Home.png` içinde popup YOK → `status: draft` gerekçesi) |
| 1 | `.ai/ui-design/reference/figma/png/1920 - Welcome Div.png` | 2048×1202 (2× export) | ✅ Mevcut — görsel SSOT (typo + alpha doğrulandı) |
| 2 | `.ai/ui-design/reference/figma/png/1024 - Welcome Div.png` | 2048×1202 | ✅ Mevcut — #1 ile **bayt-bayt aynı** (MD5 `82D18A759F6B4461242934CFECF19895`) |
| 3 | `.ai/.png/home-1024/Linux  1024 - Home Page Welcome Popup.png` | 1026×601 | ✅ Mevcut — gerçek uygulama görüntüsü (embedded tier) |

**Notlar:**
- `.ai/.png/home-1920/Linux - 1920 - Home.png` ekranında popup **yok** (T17'de
  karşılamama anı henüz yakalanmamış) → T17 yerleşimi §1'de türetilmiş.
- `01-mockup-index.md` popup'ı "1024×600" olarak listeler; gerçek asset
  **1026×601** (indleme PNG'si) — minör boyut farkı, not.
- Figma `2831:10267` "Welcome Div" grup abs(1751,−339), frame `2831:13747`
  origin (606,−589) → frame'e göreli (1145,250) — §1-1 çelişkisinin kaynağı.
> ⚠️ Node etiketi düzeltildi (2026-09-28): §1 lejantı (satır 39) ve §1-1 çelişki bloğu (satır 105-106) düzeltildi — `2831:13747` = FRAME "Linux - 1920 - Home" (1920×1080, origin 606,−589), "Welcome Div" = GROUP `2831:10267` (1024×601, abs(1751,−339)). Kaynak: `reference/figma/extracted-1920.md` L9/L529; L269-270 zaten doğrudur.

---

## 8. Responsive Davranış

| Aralık | Davranış | Kaynak |
|---|---|---|
| ≤ 767px (kompakt) | Modal `calc(100vw - 32px)`, logo 75% (≤360px: 65%), portrait **gizli**, overlay `rgba(0,0,0,.55)`, blur yok, padding 16px | `_home-components.css` L889+ |
| 768–1024px (base — PNG budur) | Modal 600×308, portrait 171×242 gösteriliyor | L847–865 + PNG |
| 1025–1440px | `max-width: 640px` | L874–876 |
| 1441–1919px | `max-width: 680px` | L877–879 |
| **1920–3839px (T17 dahil)** | **Kural YOK → base 600px geçerli** (not: `≥3840px` kuralı `max-width: 900px` — arada 1920–3839 boşluğu kasıtlı mı `⚠️ VERIFICATION REQUIRED`) | L884–887 |
| Görünür öğeler (T17) | Başlık + isim + paragraf + Başla + portrait — `__user`/`__input` Figma'da node'suz (§1 notu) | PNG + CSS |

**⚠️ ÇELİŞKI §8-1 (tier erişimi — en iyi bölüm: §8):**
- **Kırılım:** Popup tasarım + PNG export'u **her iki tier için** mevcut
  (`1024 - Welcome Div.png` = `1920 - Welcome Div.png`, bayt-bayt aynı) ↔
  `welcome-modal.js` L5 "YALNIZCA embedded (RPi5 1024)" ve gate'ler
  L128/L139/L142 `isEmbeddedDevice()` — T17 desktop'ta modal hiç açılmaz.
- **SSOT sırası:** PNG (tasarım) > kod (gate).
- **Etkilenen §:** §8.
- **Çözüm:** Tasarım spec'i T17 dahil herkese ait; kod gate'i genişletme/kapatma
  kararı kod katmanına aittir (tiers.md: bir tier'ın spec'i başka tier'ı
  bağlamaz — ama PNG zaten T17 ölçüsünde export edilmiş).

---

## 9. State Durumları

| # | State | Tetikleyici | Görünüm | Kaynak |
|---|---|---|---|---|
| 1 | `default` | Sayfa açılışı, günlük gösterim (03:00 reset) | Overlay 0.35+blur3, modal 600×308, başlık/isim/paragraf/Başla | PNG + JS L19–47 |
| 2 | `dismissed` (`.is-hidden`) | Başla tıklama veya Escape | `display:none`; `cm_welcome_dismissed=true` + `cm_welcome_dismiss_date` (günlük anahtar) | JS L7, L12, L49 |
| 3 | `hover` (Başla) | Mouse üstü | `filter: brightness(1.08)` | `_home-components.css` L878 |
| 4 | `pressed` (Başla) | `:active` | `brightness(0.95)`, `translateY(1px)` | L882–887 |
| 5 | `focus-visible` (Başla) | Klavye odak | `var(--cm-shadow-focus)` outline + `border-radius: 3px` | L880–881 |
| 6 | `input-focused` | JS focus trap `__input` hedefi | Figma'da node'suz, sadece CSS+JS → **`⚠️ VERIFICATION REQUIRED`** | JS L13, CSS L768 |

> Durumların CSS karşılığı: `.is-hidden`, `:hover`, `:active`, `:focus-visible`.
> İsim "Prenses Işıl Peri" kullanıcı adından JS ile doldurulur (statik değil).

---

**Quality Report (Kalıp D kontrolü):**
- ✅ Frontmatter mevcut (title, tier, device, viewport, path, status=`draft`, version, source_of_truth).
- ✅ §1–§9 başlıkları şablonla birebir aynı (9 başlık).
- ✅ ASCII Layout: iki kutu, gerçek koordinatlar, lejant `[PNG]/[Figma]/[türetilmiş]` §1 başında.
- ✅ BEM: 8 kod-sınıfı (envanter eşleşmesi yok → C07 kategori + VERIFICATION).
- ✅ Token: `var(--cm-*)` + Inline Değerler; kod 1050 vs token 900 notu ile.
- ✅ Touch Target: Başla 105×25 (AA PASS, AAA ergonomi notu).
- ✅ WCAG: 5 kriter; 1.4.3 **2 GAP** (ölçümlü).
- ✅ Glassmorphism: SSOT 0.35+blur3 (PNG A=89 doğrulamalı).
- ✅ PNG: 1/3 gerçek T17 PNG **VERIFICATION REQUIRED**; 3 mevcut referans.
- ✅ Responsive: 6 aralık + 1920–3839 boşluk notu.
- ✅ State: 6 durum (1 VERIFICATION).
- ✅ **Node etiketi düzeltmesi (2026-09-28):** `2831:13747` "Welcome Div" etiketi düzeltildi → `2831:13747` = FRAME "Linux - 1920 - Home", Welcome Div = GROUP `2831:10267` (§1 lejant + §1-1 çelişki bloğu + §7 notu). Kanıt: `reference/figma/extracted-1920.md` L9/L529. Versiyon 1.0.0 → 1.1.0.
- ✅ **Etiketli çelişki: 4** (§1-1 yerleşim, §1-2 metin, §6-1 overlay, §8-1 tier gate).
- ✅ Quality Report `## 10.` DEĞİL, §9 sonrası numarasız.
- ✅ Yetki footer'ı mevcut.

---

Authority: Bayram Ali · Vault Steward · 2026-09-27 · Red Team · Human Mode · Truth Mode
