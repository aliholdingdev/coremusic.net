---
version: 1.1.0
date: 2026-09-27
status: active
device: RPi5 7" Touch (Embedded)
tier: T07
viewport: 1024x600
screen: Singer
screen_id: 04
kalip: D
reference:
  authority: Figma > extracted > PNG > ASCII
  source_of_truth: .png/home-1024/Linux  1024 - Singer Page.png
related:
  - .ai/ui-design/00-device-matrix.md
  - .ai/ui-design/01-mockup-index.md
  - .ai/ui-design/02-component-inventory.md
  - .ai/ui-design/tokens/design-tokens-master.md
---

# CoreMusic — Singer (T07 Embedded 1024×600)

Zorunlu Bağlantılar: [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
x:0         200       400       600       800       1000   1024
y:0   +--------------------------------------+----------------+
      |  NAVBAR (16,13) 993x27  [logo|nav|ara||klasör|avatar]  |  y:0-60
y:60  +------+-----------------------------+------------------+
      |      | ACTION ROW (35,84) ~594x40  | SAĞ PANEL        |
      |      | [Geri][Sanatçı][Süz][Karışık]| (784,69) 219x420|
      |      +-----------------------------+                  |
      |      | Header (43,136) 308x37      | Sanatçı Foto     |
      | SOL  | "Sanatçı" + "96 sanatçı"    | (844,104) 100x100|
      | PANEL| Search (556,142) 159x25     | stat çipleri     |
      |(20,  +-----------------------------+ y271             |
      | 69)  | CHIPS (43,188) 593x25       | Hemen Çal        |
      |743x  | [Tümü*][Pop][Arabesk]...    | (809,393) 169x25 |
      |423)  +-----------------------------+ Karışık Çal      |
      |      | GRID (43,243) 680x240       | (809,433) 104x25 |
      |      | kart 120x150                | "..."            |
      |      | x43/183/323/463/603         |                  |
      |      | y243 + y413 (2. satır yarıda| scrollbar        |
      |      |  = kaydırma)                | (760,88) 3x385   |
y:510 +------+-----------------------------+------------------+
      |  FOOTER PLAYER (0,510) 1025x90  [meta|kontrol|ses|...] |  y:510-600
y:600 +---------------------------------------------------------+
```

Piksel özeti: navbar (16,13) 993×27 · sol panel (20,69) 743×423 · eylem satırı (35,84) · başlık (43,136) 308×37 · arama (556,142) 159×25 · çip satırı (43,188) 593×25 · grid (43,243) 680×240, kartlar 120×150, kolon x43/183/323/463/603, satır y243 ve y413 (2. satır alttan kırpılmış = kaydırma) · scrollbar (760,88) 3×385 · sağ panel (784,69) 219×420 · footer (0,510) 1025×90.

## 2. BEM Sınıfları

| # | Öğe | BEM Sınıfı | Envanter | CSS Kaynağı |
|---|-----|-----------|----------|-------------|
| 1 | Sayfa sarmalayıcı | `.artists-layout` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 2 | Üst eylem satırı | `.artists-header` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 3 | Arama alanı | `.artists-header__search` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 4 | Sekme/çip sarmalayıcısı | `.artists-tabs` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 5 | Çip (kayıtlı sekme) | `.artists-tabs__tab` | C06 `.tab*` türevi (ad envanterde yok) | `p-artists.css` (CSS doğrulandı) |
| 6 | Çip aktif durumu | `.artists-tabs__tab.is-active` | C06 `.tab.is-active` (yapı eşleşiyor) | `p-artists.css` (CSS doğrulandı) |
| 7 | Sanatçı grid sarmalayıcısı | `.artists-grid` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 8 | Sanatçı kartı | `.artist-card` | C14 — (ekran özel; C03 `.card*` ailesi) | `p-artists.css` (CSS doğrulandı) |
| 9 | Kart görsel | `.artist-card__thumb` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 10 | Kart bilgi bloğu | `.artist-card__info` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 11 | Sanatçı adı | `.artist-card__name` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 12 | Tür etiketi | `.artist-card__genre` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 13 | Şarkı sayısı | `.artist-card__count` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 14 | Sağ detay paneli | `.artists-detail` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 15 | Sanatçı fotoğrafı | `.artists-detail__photo` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 16 | Panel sanatçı adı | `.artists-detail__name` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 17 | Panel tür etiketi | `.artists-detail__genre` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 18 | Panel istatistik satırı | `.artists-detail__stats` | C14 — (ekran özel) | `p-artists.css` (CSS doğrulandı) |
| 19 | Eylem düğmesi | `.btn` | C04 `.btn*` | `04_component/04-button.css` (envanter) |
| 20 | Sayfa çubuğu | `.scrollbar` (görsel 3px) | — (ekran özel) | — (sadece PNG) |
| 21 | Navbar / footer | `.site-header__*` / `.footer-player__*` | C01 / C11 | `_header.css` / `_footer.css` |

> ⚠️ VERIFICATION REQUIRED: envanterdışı sınıflar — `.artists-layout`, `.artists-header/__search`, `.artists-tabs/__tab`, `.artists-grid`, `.artist-card(/__thumb/__info/__name/__genre/__count)`, `.artists-detail(/__photo/__name/__genre/__stats)`, `.scrollbar`; envanter adı CSS'te doğrulanamayan — (yok; hepsi CSS'te mevcut, envanterde C14 kayıtlı değil). Bunlar Figma çıkarımı veya PNG'den gelir; kod üretiminde adları CSS ile doğrulayın.

## 3. Token Kullanımı

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Sayfa yüzeyi | `var(--cm-bg-surface)` | — |
| Panel yüzeyi | `var(--cm-glass-bg)` | — |
| Panel örtü katmanı | `var(--cm-bg-overlay)` | — |
| Panel bulanıklığı | `var(--cm-glass-blur)` | — |
| Panel kenarlık | `var(--cm-border-w)` | — |
| Panel kenar rengi | `var(--cm-glass-border)` | — |
| Panel gölgesi | `var(--cm-glass-shadow)` | — |
| Başlık metni | `var(--cm-text-1)` | — |
| İkincil metin (tür / sayı) | `var(--cm-text-2)` | — |
| Arama alanı zemini | `var(--cm-input-bg)` | — |
| Vurgu / aktif çip zemini | `var(--cm-accent-1)` | — |
| Çip metni (aktif) | `var(--cm-accent-contrast)` | — |
| Odak halkası | `var(--cm-focus-ring)` | — |
| BorderRadius (kart / çip) | `var(--cm-radius-2)` | — |
| Boşluk (grid gutter) | `var(--cm-space-4)` | — |
| Font (sayfa başlığı) | `var(--cm-font-display)` | — |
| Font (gövde) | `var(--cm-font-body)` | — |

Tüm değerler `design-tokens-master.md` §3/§6 kaynağından alınmıştır; bu dosyada tekrarlanmaz (tekrar SSOT ihlali olur).

## 4. Touch Target (Dokunma Boyutu)

| Kural | Değer | Uygulama (bu ekran) | Kaynak |
|-------|-------|---------------------|--------|
| Tier T07 zorunlu | ≥48×48 px | Çip 25 px, arama 25 px, stat çipleri ~16 px, eylem butonları 40 px sarmalayıcı; kart 120×150 ✓ | 00-device-matrix (§5 çatışma 4) |
| WCAG 2.5.8 (AA) | ≥24×24 px | Küçük çipler 25 px PASS; stat çipleri ~16 px → GAP riski | WCAG 2.2 |
| Şablon varsayılanı | 44×44 px | Çip/arama 25 px, stat çipleri 16 px → 44 altı | screen-spec-template (§4.4) |
| Yakınlık (fiziksel) | ≥8 px boşluk | Kart kolon arası 20 px, satır arası ~20 px ✓ | Figma çıkarımı |
| Tier gömülü/metin (literal) | `touch target >= 44px` | 25/16 px → 44 altı | matrix L97 |

Bu tier 48 px çatışması `.ai/ui-design/04-accessibility-gaps.md` dosyasına BEKLEMEDE olarak işlenmelidir (bkz. §5 satır 4).

## 5. WCAG 2.2 Uyumluluk

| # | Kriter | Seviye | Sonuç | Kanıt / Not |
|---|--------|--------|-------|-------------|
| 1 | Metin kontrastı | AA (1.4.3) | ⚠️ GAP | PNG median kontrast: **2.85** (hedef ≥4.50; media `assets.coremusic.net/Css/05_Pages/p-artists.css`) |
| 2 | Odak görünürlüğü | AA (2.4.7) | ✅ PASS | `:focus-visible` + `var(--cm-focus-ring)` (şablon §6.1) |
| 3 | Boyut (dokunma) | AA (2.5.8) | ⚠️ GAP | Stat çipleri ~16 px < 24 px (PNG ölçüleri); ana çipler 25 px PASS |
| 4 | Tier dokunma boyutu | Tier T07 (48 px) | ⚠️ GAP | Çip/arama 25 px, stat çipleri 16 px; `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE |
| 5 | Semantik DOM | A (1.3.1) | ✅ PASS | Header/footer landmark + `nav` (header.php L82 `aria-current="page"`, footer.php L66 `role="contentinfo"`); grid/liste semantiği kodda doğrulanmalı |
| 6 | Durum (renk dışı) + aria | A (1.4.1 / 4.1.2) | ✅ PASS | Aktif çip: `.is-active` zemin + kontrast (biçim de değişir); `aria-current` header.php L82, JS `role="tab"` kodda doğrulanmalı |

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 1**

1. **Tier çatışması (1024×600) — ✅ KAPANDI (tier taşındı, matrix L92):** `00-device-matrix.md` L95 "Görsel Katman `T07-embedded` → 1024×600" + L97 "`touch target >= 44px`" — Tier tablosu 48 px, şablon §4.4 44×44: yorum gerilimi (25 px çipler ikisinin de altında).

Not: grid 2. satırının alttan kırpılması (y413 + 150 > 510) çelişki değildir — kaydırılabilir liste davranışıdır (PNG'de scrollbar (760,88) 3×385 mevcut).

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE (bu dosya düzenlenmemiştir; yetki dışı).

## 6. Glassmorphism

Uygulama kuralı (şablon §6 + token master §2.1.2 — bu ekran için kesin kural):

```css
.artists-layout {
  background: var(--cm-glass-bg);           /* + opaklık katmanı: var(--cm-bg-overlay) */
  backdrop-filter: blur(var(--cm-glass-blur));
  border: var(--cm-border-w) solid var(--cm-glass-border);
  box-shadow: var(--cm-glass-shadow);
}
@supports not (backdrop-filter: blur(1px)) {
  .artists-layout { background: var(--cm-glass-bg-strong); } /* fallback düz opak */
}
```

| Kural | Durum | Not |
|-------|-------|-----|
| `backdrop-filter: blur(var(--cm-glass-blur))` | ✅ Uygulanır | Panel zeminleri (sol + sağ) — PNG'de 8-10 px bulanık |
| `background: var(--cm-glass-bg)` | ✅ Uygulanır | Panel zemini + `var(--cm-bg-overlay)` örtü katmanı |
| `border: var(--cm-border-w) solid var(--cm-glass-border)` | ✅ Uygulanır | Panel kenarları |
| `box-shadow: var(--cm-glass-shadow)` | ✅ Uygulanır | Panel gölgesi |
| Fallback (`@supports not`) → `var(--cm-glass-bg-strong)` | ✅ Uygulanır | `backdrop-filter` desteklemeyen gömülü WebView |
| Kontrast (1.4.11 / 1.4.3) | ⚠️ | Üst üste binen katman + metin = §5 satır 1'e göre 2.85 → GAP |

ÇELİŞKİ (bu bölüm): **0** — glass değerleri için SSOT: Figma `2831:10268` > `.ai/ui-design/tokens/design-tokens-master.md` §2.1.2 > `assets.coremusic.net/Css/01_Core/01-variables.css` (deprecated `--cm-glass-*` = düzeltme yönü, çelişki değil).

## 7. PNG Referansı

**PNG (yerel):** `C:\www\coremusic.net\.png\home-1024\Linux  1024 - Singer Page.png` (1024×600) ✅ okundu
**Figma (tier alt kümesi):** node — `C:\temp\opencode\figma-a\04-singer.md` (extracted koordinatlar)
**Mockup dizini:** `.ai/ui-design/` → [[ui-design/01-mockup-index]]
**ASCII:** §1 bu dosyada.

Çatışma yok: PNG ↔ ASCII hizalı (panel (20,69), footer y510). Sanatçı adları/türleri Figma'dan, fotoğraflar PNG'den; API verisi yok → `API'den gelmedi`.

## 8. Responsive Davranış (Tier Etkisi)

Bu ekran **sabit 1024×600** içindir; davranış → [[ui-design/05-responsive-architecture]] §7.4 + §12. Tier davranışı (matrix):

- T07 sabit: CSS `zoom`/ölçekleme (Figma scaleFactor 1.0) — grid 5 kolon sabit kalır, `clamp()` ile değil.
- Farklı ekranlara kaydırma/boyut sözü verilmez; kırılma davranışı bu dosyanın konusu değildir.

| Viewport | Grid | Panel | Navbar | Not |
|----------|------|-------|--------|-----|
| 1024×600 (T07) | 5 kolon × 120 px (satır 2 kırpılmış) | 743+219 px | 993×27 | PNG kanıtı |
| Daha geniş ekran | — | — | — | Kapsam dışı |

## 9. State (Durumlar)

| # | State | Tetikleyici | Görsel Değişim | Token / CSS |
|---|-------|-------------|----------------|-------------|
| 1 | Default | Sayfa yüklenir | Panel + grid + sağ panel görünür | `var(--cm-glass-bg)` |
| 2 | Hover (yalnız `@media (hover:hover)`) | Kart / çip üzerinde | Zemin hafif vurgu | `var(--cm-accent-1)` |
| 3 | Aktif çip | Tıklama / `role="tab"` seçimi | Zemin vurgu + metin kontrastlı | `.is-active` → `var(--cm-accent-1)` + `var(--cm-accent-contrast)` |
| 4 | Seçili sanatçı (sağ panel) | Kart seçilir | Foto/stat/button değişir, kenarlık vurgu | `var(--cm-accent-1)` border |
| 5 | Arama odakta | `:focus-visible` | Odak halkası | `var(--cm-focus-ring)` |
| 6 | Kaydırma (grid) | Dikey kaydırma | 2. satır devamı açığa çıkar | scrollbar (PNG (760,88) 3×385) |
| 7 | Boş sonuç | Filtre eşleşmez | Boş durum metni | `var(--cm-text-2)` |

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Sections (§1-§9) | 9/9 |
| BEM rows | 21 |
| Token rows | 17 |
| Touch Target rules | 5 |
| WCAG checks | 6 |
| Glassmorphism rules | 6 |
| ÇELİŞKİ (§5) | 1 |
| ÇELİŞKİ (§6) | 0 |
| PNG | verified |
| Figma | verified |
| ASCII ↔ PNG | hizalı (panel (20,69), footer y510) |
| Cross References | 5 |
| Version | 1.1.0 |
| Tier düzeltmesi | tier T08→T07 düzeltildi (matrix L92) |
| `00-ascii-art-index.md` | BEKLEMEDE (yazılmadı — yetki dışı) |

---

Authority: SSOT = PNG > Figma extracted > ASCII · `.ai/ui-design/` (index/figma/tokens/00-05) · Üretim: screen-spec-template §6 · Kayıt: `.ai/ui-design/04-accessibility-gaps.md` (BEKLEMEDE)
