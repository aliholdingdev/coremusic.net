---
version: 1.0.0
date: 2026-09-27
status: active
device: RPi5 7" Touch (Embedded)
tier: T08
viewport: 1024x600
screen: Album Detail
screen_id: 03
kalip: D
reference:
  authority: Figma > extracted > PNG > ASCII
  source_of_truth: .png/home-1024/Linux  1024 - Albumler Details Detay Page.png
related:
  - .ai/ui-design/00-device-matrix.md
  - .ai/ui-design/01-mockup-index.md
  - .ai/ui-design/02-component-inventory.md
  - .ai/ui-design/tokens/design-tokens-master.md
---

# CoreMusic — Album Detail (T08 Embedded 1024×600)

Zorunlu Bağlantılar: [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
x:0         200       400       600       800       1000   1024
y:0   +--------------------------------------+----------------+
      |  NAVBAR (16,13) 993x27  [logo|nav|ara||klasör|avatar]  |  y:0-60
y:60  +------+-----------------------------+------------------+
      |      | ACTION ROW (33,77) 481x40   | SAĞ PANEL        |
      |      | [Geri][Albüm][Süz][Karışık]| (784,69) 219x420 |
      |      +-----------------------------+                  |
      |      | Search (597,85) 159x25      | Albüm Kapağı     |
      | SOL  +-----------------------------+ (844,114) 100x100|
      | TRACK| LABEL (23,127) 742x34       | "Oynat" düğmesi  |
      | LİST | [Albüm|Şarkı Adı|Süre|Fav] | + durum satırları |
      |      +-----------------------------+ stat y403/421/   |
      | (228 | 7 satır: 498x30, x247,      | 439/457          |
      |  boş) | y175 step45 (2. satır      |                  |
      |      | aktif)                     |                  |
      |      | divider (228,175) 1x300     |                  |
      |      | "Albümlerim" (58,200) 135x224 | scrollbar      |
      |      | (sol küçük liste)          | (763,168) 3x308  |
y:510 +------+-----------------------------+------------------+
      |  FOOTER PLAYER (0,510) 1025x90  [meta|kontrol|ses|...] |  y:510-600
y:600 +---------------------------------------------------------+
```

Piksel özeti: navbar (16,13) 993×27 · eylem satırı (33,77) 481×40 · arama (597,85) 159×25 · tablo başlığı (23,127) 742×34 · 7 şarkı satırı 498×30, x247, y175, adım 45 · dikey ayraç (228,175) 1×300 · "Albümlerim" sol liste (58,200) 135×224 · scrollbar (763,168) 3×308 · sağ panel (784,69) 219×420 · footer (0,510) 1025×90.

Not (frame): Figma extracted frame h612 ve footer y522 — PNG footer'ı y510 → §5 ÇELİŞKİ 2. "Navigate Div" (Figma node) extracted'ta var, PNG/CSS'te karşılığı yok → §5 ÇELİŞKİ 3.

## 2. BEM Sınıfları

| # | Öğe | BEM Sınıfı | Envanter | CSS Kaynağı |
|---|-----|-----------|----------|-------------|
| 1 | Sayfa sarmalayıcı | `.album-detail-layout` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 2 | Üst eylem satırı | `.albums-header` (yeniden kullanım) | C14 — (ad envanterde yok, CSS doğrulanamadı) | — (PNG/Figma) |
| 3 | Geri düğmesi | `.albums-header__back` | C14 — (ad envanterde yok) | — (PNG/Figma) |
| 4 | Arama alanı | `.albums-header__search` | C14 — (ad envanterde yok) | — (PNG/Figma) |
| 5 | Şarkı listesi | `.track-list` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 6 | Liste başlık satırı | `.track-list__header` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 7 | Şarkı satırı | `.track-row` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 8 | Aktif şarkı satırı | `.track-row.is-active` | C06 `.is-active` deseni | `p-album-detail.css` (CSS doğrulandı) |
| 9 | Satır numarası | `.track-row__number` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 10 | Satır küçük resim | `.track-row__thumb` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 11 | Satır başlık | `.track-row__title` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 12 | Satır süre | `.track-row__duration` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 13 | Satır yıldız | `.track-row__rating` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 14 | Albüm bilgi kartı | `.album-detail-panel` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 15 | Albüm kapağı | `.album-detail-panel__art` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 16 | Albüm adı | `.album-detail-panel__title` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 17 | Sanatçı adı | `.album-detail-panel__artist` | C14 — (ekran özel) | `p-album-detail.css` (CSS doğrulandı) |
| 18 | Sol liste bloğu | — (PNG "Albümlerim" bloğu) | — (ekran özel) | — (CSS doğrulanamadı) |
| 19 | Sayfa çubuğu | `.scrollbar` (görsel 3px) | — (ekran özel) | — (sadece PNG) |
| 20 | Navbar / footer | `.site-header__*` / `.footer-player__*` | C01 / C11 | `_header.css` / `_footer.css` |

> ⚠️ VERIFICATION REQUIRED: envanterdışı sınıflar — `.album-detail-layout`, `.track-list/__header`, `.track-row(/__number/__thumb/__title/__duration/__rating)`, `.album-detail-panel(/__art/__title/__artist)`, `.scrollbar`, sol liste bloğu; envanter adı CSS'te doğrulanamayan — `.albums-header`, `.albums-header__back`, `.albums-header__search` (yeniden kullanım varsayımı, CSS kanıtı yok). Bunlar Figma çıkarımı veya PNG'den gelir; kod üretiminde adları CSS ile doğrulayın.

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
| İkincil metin (süre / statu) | `var(--cm-text-2)` | — |
| Aktif satır zemini | `var(--cm-accent-1)` | — |
| Aktif satır metni | `var(--cm-accent-contrast)` | — |
| Ayraç çizgisi | `var(--cm-border-1)` | — |
| Arama alanı zemini | `var(--cm-input-bg)` | — |
| Odak halkası | `var(--cm-focus-ring)` | — |
| BorderRadius (kart / satır) | `var(--cm-radius-2)` | — |
| Boşluk (satır padding) | `var(--cm-space-3)` | — |
| Font (başlık) | `var(--cm-font-display)` | — |
| Font (gövde) | `var(--cm-font-body)` | — |

Tüm değerler `design-tokens-master.md` §3/§6 kaynağından alınmıştır; bu dosyada tekrarlanmaz (tekrar SSOT ihlali olur).

## 4. Touch Target (Dokunma Boyutu)

| Kural | Değer | Uygulama (bu ekran) | Kaynak |
|-------|-------|---------------------|--------|
| Tier T08 zorunlu | ≥48×48 px | Şarkı satırı h30, yıldızlar ~15 px, arama 25 px, eylem satırı butonları 40 px sarmalayıcı | 00-device-matrix (§5 çatışma 4) |
| WCAG 2.5.8 (AA) | ≥24×24 px | ⚠️ 15 px yıldızlar (favori sütunu) → GAP | WCAG 2.2 |
| Şablon varsayılanı | 44×44 px | Satır h30 ve yıldızlar 15 px → 44 altı | screen-spec-template (§4.4) |
| Yakınlık (fiziksel) | ≥8 px boşluk | Satır arası 15 px boşluk (30+15 adım 45) ✓ | Figma çıkarımı |
| Tier gömülü/metin (literal) | `touch target >= 44px` | 30/25/15 px → 44 altı | matrix L97 |

Bu tier 48 px çatışması `.ai/ui-design/04-accessibility-gaps.md` dosyasına BEKLEMEDE olarak işlenmelidir (bkz. §5 satır 4).

## 5. WCAG 2.2 Uyumluluk

| # | Kriter | Seviye | Sonuç | Kanıt / Not |
|---|--------|--------|-------|-------------|
| 1 | Metin kontrastı | AA (1.4.3) | ⚠️ GAP | PNG median kontrast: **3.69** (hedef ≥4.50; media `assets.coremusic.net/Css/05_Pages/p-album-detail.css`) |
| 2 | Odak görünürlüğü | AA (2.4.7) | ✅ PASS | `:focus-visible` + `var(--cm-focus-ring)` (şablon §6.1) |
| 3 | Boyut (dokunma) | AA (2.5.8) | ⚠️ GAP | Favori yıldızları ~15 px < 24 px (PNG ölçüleri) |
| 4 | Tier dokunma boyutu | Tier T08 (48 px) | ⚠️ GAP | Satır h30 / yıldız 15 px / arama 25 px; `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE |
| 5 | Semantik DOM | A (1.3.1) | ✅ PASS | Header/footer landmark + `nav` (header.php L82 `aria-current="page"`, footer.php L66 `role="contentinfo"`); tablo semantiği kodda doğrulanmalı |
| 6 | Durum (renk dışı) + aria | A (1.4.1 / 4.1.2) | ✅ PASS | Aktif satır: `.is-active` zemin + `border-left` (bkz. §9 satır 4) — renk tek başına değil, ayrıca `aria-current` header.php L82 |

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 3**

1. **Tier çatışması (1024×600):** `00-device-matrix.md` L95 "Görsel Katman `T08-embedded` → 1024×600" + L97 "`touch target >= 44px`" — Tier tablosu 48 px, şablon §4.4 44×44, matrix literal 44 px: üç yönlü yorum gerilimi (satır 3'te 15 px yıldız = hepsinin altında).
2. **Frame yüksekliği:** Figma extracted frame h612 ve footer Figma y522 — PNG footer y510 ve viewport 600. Frame 612 > 600: taşma/çelişki. SSOT = PNG (footer y510, §1 hizalı).
3. **Navigate Div:** Figma extracted'ta "Navigate Div" node'u var; PNG'de görünür karşılığı yok, CSS'te sınıf kanıtı yok → `API'den gelmedi` / uydurulmaz; §2'ye satır yazılmadı.

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE (bu dosya düzenlenmemiştir; yetki dışı).

## 6. Glassmorphism

Uygulama kuralı (şablon §6 + token master §2.1.2 — bu ekran için kesin kural):

```css
.album-detail-layout {
  background: var(--cm-glass-bg);           /* + opaklık katmanı: var(--cm-bg-overlay) */
  backdrop-filter: blur(var(--cm-glass-blur));
  border: var(--cm-border-w) solid var(--cm-glass-border);
  box-shadow: var(--cm-glass-shadow);
}
@supports not (backdrop-filter: blur(1px)) {
  .album-detail-layout { background: var(--cm-glass-bg-strong); } /* fallback düz opak */
}
```

| Kural | Durum | Not |
|-------|-------|-----|
| `backdrop-filter: blur(var(--cm-glass-blur))` | ✅ Uygulanır | Panel zeminleri — PNG'de 8-10 px bulanık |
| `background: var(--cm-glass-bg)` | ✅ Uygulanır | Panel zemini + `var(--cm-bg-overlay)` örtü katmanı |
| `border: var(--cm-border-w) solid var(--cm-glass-border)` | ✅ Uygulanır | Panel kenarları |
| `box-shadow: var(--cm-glass-shadow)` | ✅ Uygulanır | Panel gölgesi |
| Fallback (`@supports not`) → `var(--cm-glass-bg-strong)` | ✅ Uygulanır | `backdrop-filter` desteklemeyen gömülü WebView |
| Kontrast (1.4.11 / 1.4.3) | ⚠️ | Üst üste binen katman + metin = §5 satır 1'e göre 3.69 → GAP |

ÇELİŞKİ (bu bölüm): **0** — glass değerleri için SSOT: Figma `2831:10268` > `.ai/ui-design/tokens/design-tokens-master.md` §2.1.2 > `assets.coremusic.net/Css/01_Core/01-variables.css` (deprecated `--cm-glass-*` = düzeltme yönü, çelişki değil).

## 7. PNG Referansı

**PNG (yerel):** `C:\www\coremusic.net\.png\home-1024\Linux  1024 - Albumler Details Detay Page.png` (1024×600) ✅ okundu
**Figma (tier alt kümesi):** extracted node — `C:\temp\opencode\figma-a\03-album-detail.md` (frame h612, Navigate Div)
**Mockup dizini:** `.ai/ui-design/` → [[01-mockup-index]]
**ASCII:** §1 bu dosyada.

Çatışma: frame h612/footer y522 (Figma) ↔ 600/y510 (PNG) → §5 ÇELİŞKİ 2. Şarkı adı/süre metinleri Figma'dan, kapak PNG'den; API verisi yok → `API'den gelmedi`.

## 8. Responsive Davranış (Tier Etkisi)

Bu ekran **sabit 1024×600** içindir; davranış → [[05-responsive-architecture]] §7.4 + §12. Tier davranışı (matrix):

- T08 sabit: CSS `zoom`/ölçekleme (Figma scaleFactor 1.0) — tablo 7 satır ve iki panel sabit kalır, `clamp()` ile değil.
- Farklı ekranlara kaydırma/boyut sözü verilmez; kırılma davranışı bu dosyanın konusu değildir.

| Viewport | Liste | Panel | Navbar | Not |
|----------|-------|-------|--------|-----|
| 1024×600 (T08) | 7 satır (h30, adım 45) | 219 px sağ + 135 px sol | 993×27 | PNG kanıtı |
| Daha geniş ekran | — | — | — | Kapsam dışı |

## 9. State (Durumlar)

| # | State | Tetikleyici | Görsel Değişim | Token / CSS |
|---|-------|-------------|----------------|-------------|
| 1 | Default | Sayfa yüklenir | Liste + paneller görünür | `var(--cm-glass-bg)` |
| 2 | Hover (yalnız `@media (hover:hover)`) | Satır / buton üzerinde | Zemin hafif vurgu | `var(--cm-accent-1)` |
| 3 | Odakta | `:focus-visible` | Odak halkası | `var(--cm-focus-ring)` |
| 4 | Aktif şarkı satırı | Çalınan satır | Zemin vurgu + sol kenarlık (renk dışı işaret) | `.is-active` → `var(--cm-accent-1)` + `border-left: 3px` |
| 5 | Favori işaretli | Yıldız tıklaması | Dolu yıldız (renk dışı biçim: dolu/boş) | `var(--cm-accent-1)` |
| 6 | Listeyi kaydırma | Dikey kaydırma | 7 satır sonrası devamı açığa çıkar | scrollbar (PNG (763,168) 3×308) |
| 7 | Sağ panel seçili albüm | Albüm değişimi | Kapak/başlık/satır değişir | `var(--cm-accent-1)` |

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Sections (§1-§9) | 9/9 |
| BEM rows | 20 |
| Token rows | 18 |
| Touch Target rules | 5 |
| WCAG checks | 6 |
| Glassmorphism rules | 6 |
| ÇELİŞKİ (§5) | 3 |
| ÇELİŞKİ (§6) | 0 |
| PNG | verified |
| Figma | verified (h612 çelişkisi §5.2) |
| ASCII ↔ PNG | hizalı (footer y510; frame çelişkisi §5.2) |
| Cross References | 5 |
| `00-ascii-art-index.md` | BEKLEMEDE (yazılmadı — yetki dışı) |

---

Authority: SSOT = PNG > Figma extracted > ASCII · `.ai/ui-design/` (index/figma/tokens/00-05) · Üretim: screen-spec-template §6 · Kayıt: `.ai/ui-design/04-accessibility-gaps.md` (BEKLEMEDE)
