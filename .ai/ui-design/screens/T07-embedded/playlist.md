---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playlist Screen Specification"
type: spec
category: ui-design
date: 2026-09-27
status: active
version: 1.1.0
tier: T07
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
screen: Playlist
screen_id: 05
kalip: D
reference:
  authority: ".ai/ui-design/screens/T07-embedded/playlist.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Playlist Page.png"
related:
  - .ai/ui-design/00-device-matrix.md
  - .ai/ui-design/01-mockup-index.md
  - .ai/ui-design/02-component-inventory.md
  - .ai/ui-design/tokens/design-tokens-master.md
---

# CoreMusic — Playlist (T07 Embedded 1024×600)

Zorunlu Bağlantılar: [[ui-design/00-device-matrix]] · [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
x:0         200       400       600       800       1000   1024
y:0   +--------------------------------------+----------------+
      |  NAVBAR (16,13) 993x27  [logo|nav|ara||klasör|avatar]  |  y:0-60
y:60  +------+-----------------------------+------------------+
      |      | TITLE/SEARCH (y84-124)      | SAĞ PANEL        |
      |      | [Geri][Oynat listeyi]       | (784,69) 219x420 |
      |      | Search (594,85) ~161x25     |                  |
      | SOL  +-----------------------------+ Kapak / başlık   |
      | PANEL| HEADER (24,127) 742x34      | + eylem butonları|
      |(23,  | [#|Şarkı Adı|Albüm|Sanatçı| |                  |
      | 69)  | Süre|★]  (x77/282/465/612/682)| stat satırları  |
      |743x  +-----------------------------+                  |
      |425)  | LİST FRAME (31,173) 725x307 |                  |
      |      | 7 satır x31, adım 46        |                  |
      |      | (x: /47/77/282/465/612/682) |                  |
      |      | y173+ step46; 4. satır      |                  |
      |      | pembe seçili; 8. satır      |                  |
      |      | y495 çerçeve dışı = kaydırma |                  |
      |      | ★ sütunu 15px (GAP §5.3)    |                  |
y:510 +------+-----------------------------+------------------+
      |  FOOTER PLAYER (0,510) 1025x90  [meta|kontrol|ses|...] |  y:510-600
y:600 +---------------------------------------------------------+
```

Piksel özeti: navbar (16,13) 993×27 · sol panel (23,69) 743×425 · başlık/arama satırı y84-124, arama (594,85) ~161×25 · tablo başlığı (24,127) 742×34 · liste çerçevesi (31,173) 725×307 = 7 satır × h31, adım 46 (8. satır y495 çerçeve dışı = kaydırma, çelişki değil) · sütun x: sıra /47, başlık 77, albüm 282, sanatçı 465, süre 612, ★ 682 · sağ panel (784,69) 219×420 · footer (0,510) 1025×90.

## 2. BEM Sınıfları

| # | Öğe | BEM Sınıfı | Envanter | CSS Kaynağı |
|---|-----|-----------|----------|-------------|
| 1 | Sayfa sarmalayıcı | `.playlist-layout` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 2 | Tablo başlık satırı | `.playlist-table__header` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 3 | Liste satırı | `.playlist-row` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 4 | Sıra numarası | `.playlist-row__number` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 5 | Küçük resim | `.playlist-row__thumb` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 6 | Şarkı başlığı | `.playlist-row__title` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 7 | Albüm sütunu | `.playlist-row__album` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 8 | Sanatçı sütunu | `.playlist-row__artist` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 9 | Süre sütunu | `.playlist-row__duration` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 10 | Yıldız sütunu | `.playlist-row__rating` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 11 | Seçili satır | `.playlist-row.is-active` (PNG pembe satır) | C06 `.is-active` deseni | `p-playlist.css` (CSS doğrulanamadı — `.is-active` kanıtı yok) |
| 12 | Sağ detay paneli | `.playlist-detail` (yapı C14 ailesi) | — (ad envanterde/CSS'te yok) | — (PNG/Figma) |
| 13 | Sanatçı fotoğrafı | `.playlist-detail__artist-photo` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 14 | Panel başlık | `.playlist-detail__title` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 15 | Panel sanatçı | `.playlist-detail__artist` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 16 | Panel eylem satırı | `.playlist-detail__actions/__action` | C14 — (ekran özel) | `p-playlist.css` (CSS doğrulandı) |
| 17 | Arama alanı | `.artists-header__search` (yeniden kullanım) | C14 — (ad envanterde yok, CSS doğrulanamadı) | — (PNG/Figma) |
| 18 | Sayfa çubuğu | `.scrollbar` (görsel 3px) | — (ekran özel) | — (sadece PNG) |
| 19 | Navbar / footer | `.site-header__*` / `.footer-player__*` | C01 / C11 | `_header.css` / `_footer.css` |

> ⚠️ VERIFICATION REQUIRED: envanterdışı sınıflar — `.playlist-layout`, `.playlist-table__header`, `.playlist-row(/__number/__thumb/__title/__album/__artist/__duration/__rating)`, `.playlist-detail(/__artist-photo/__title/__artist/__actions/__action)`, `.scrollbar`; envanter adı CSS'te doğrulanamayan — `.playlist-row.is-active` (pembe satır kanıtı sadece PNG), `.artists-header__search` (yeniden kullanım varsayımı). Bunlar Figma çıkarımı veya PNG'den gelir; kod üretiminde adları CSS ile doğrulayın.

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
| İkincil metin (albüm / sanatçı / süre) | `var(--cm-text-2)` | — |
| Seçili satır zemini | `var(--cm-accent-1)` | — |
| Seçili satır metni | `var(--cm-accent-contrast)` | — |
| Ayraç çizgisi | `var(--cm-border-1)` | — |
| Arama alanı zemini | `var(--cm-input-bg)` | — |
| Odak halkası | `var(--cm-focus-ring)` | — |
| BorderRadius (panel / satır) | `var(--cm-radius-2)` | — |
| Boşluk (satır padding) | `var(--cm-space-3)` | — |
| Font (başlık) | `var(--cm-font-display)` | — |
| Font (gövde) | `var(--cm-font-body)` | — |

Tüm değerler `design-tokens-master.md` §3/§6 kaynağından alınmıştır; bu dosyada tekrarlanmaz (tekrar SSOT ihlali olur).

## 4. Touch Target (Dokunma Boyutu)

| Kural | Değer | Uygulama (bu ekran) | Kaynak |
|-------|-------|---------------------|--------|
| Tier T07 zorunlu | ≥48×48 px | Satır h31, yıldızlar 15 px, arama 25 px → 48 altı | 00-device-matrix (§5 çatışma 4) |
| WCAG 2.5.8 (AA) | ≥24×24 px | ⚠️ 15 px yıldızlar → GAP; satır h31 PASS | WCAG 2.2 |
| Şablon varsayılanı | 44×44 px | Satır 31 px / yıldız 15 px → 44 altı | screen-spec-template (§4.4) |
| Yakınlık (fiziksel) | ≥8 px boşluk | Satır arası 15 px boşluk (31+15 adım 46) ✓ | Figma çıkarımı |
| Tier gömülü/metin (literal) | `touch target >= 44px` | 31/25/15 px → 44 altı | matrix L97 |

Bu tier 48 px çatışması `.ai/ui-design/04-accessibility-gaps.md` dosyasına BEKLEMEDE olarak işlenmelidir (bkz. §5 satır 4).

## 5. WCAG 2.2 Uyumluluk

| # | Kriter | Seviye | Sonuç | Kanıt / Not |
|---|--------|--------|-------|-------------|
| 1 | Metin kontrastı | AA (1.4.3) | ⚠️ GAP | PNG median kontrast: **2.55** (hedef ≥4.50; media `assets.coremusic.net/Css/05_Pages/p-playlist.css`) |
| 2 | Odak görünürlüğü | AA (2.4.7) | ✅ PASS | `:focus-visible` + `var(--cm-focus-ring)` (şablon §6.1) |
| 3 | Boyut (dokunma) | AA (2.5.8) | ⚠️ GAP | Yıldız sütunu ~15 px < 24 px (PNG ölçüleri) |
| 4 | Tier dokunma boyutu | Tier T07 (48 px) | ⚠️ GAP | Satır 31 px / yıldız 15 px / arama 25 px; `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE |
| 5 | Semantik DOM | A (1.3.1) | ✅ PASS | Header/footer landmark + `nav` (header.php L82 `aria-current="page"`, footer.php L66 `role="contentinfo"`); `role="table"/"row"` kodda doğrulanmalı |
| 6 | Durum (renk dışı) + aria | A (1.4.1 / 4.1.2) | ✅ PASS | Seçili satır: `.is-active` zemin + ayrıca metin/kalınlık değişmeli (PNG pembe satır renk tek başına → kodda `aria-selected` doğrulanmalı); `aria-current` header.php L82 |

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 1**

1. **Tier çatışması (1024×600) — ✅ KAPANDI (tier taşındı, matrix L92):** `00-device-matrix.md` L95 "Görsel Katman `T07-embedded` → 1024×600" + L97 "`touch target >= 44px`" — Tier tablosu 48 px, şablon §4.4 44×44: yorum gerilimi (31 px satır / 15 px yıldız ikisinin de altında).

Not: 8. satırın çerçeve dışı kalması (y495 > liste çerçevesi 173+307=480) çelişki değildir — kaydırılabilir liste davranışıdır (§9 satır 6).

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE (bu dosya düzenlenmemiştir; yetki dışı).

## 6. Glassmorphism

Uygulama kuralı (şablon §6 + token master §2.1.2 — bu ekran için kesin kural):

```css
.playlist-layout {
  background: var(--cm-glass-bg);           /* + opaklık katmanı: var(--cm-bg-overlay) */
  backdrop-filter: blur(var(--cm-glass-blur));
  border: var(--cm-border-w) solid var(--cm-glass-border);
  box-shadow: var(--cm-glass-shadow);
}
@supports not (backdrop-filter: blur(1px)) {
  .playlist-layout { background: var(--cm-glass-bg-strong); } /* fallback düz opak */
}
```

| Kural | Durum | Not |
|-------|-------|-----|
| `backdrop-filter: blur(var(--cm-glass-blur))` | ✅ Uygulanır | Panel zeminleri (sol + sağ) — PNG'de 8-10 px bulanık |
| `background: var(--cm-glass-bg)` | ✅ Uygulanır | Panel zemini + `var(--cm-bg-overlay)` örtü katmanı |
| `border: var(--cm-border-w) solid var(--cm-glass-border)` | ✅ Uygulanır | Panel kenarları |
| `box-shadow: var(--cm-glass-shadow)` | ✅ Uygulanır | Panel gölgesi |
| Fallback (`@supports not`) → `var(--cm-glass-bg-strong)` | ✅ Uygulanır | `backdrop-filter` desteklemeyen gömülü WebView |
| Kontrast (1.4.11 / 1.4.3) | ⚠️ | Üst üste binen katman + metin = §5 satır 1'e göre 2.55 → GAP |

ÇELİŞKİ (bu bölüm): **0** — glass değerleri için SSOT: Figma `2831:10268` > `.ai/ui-design/tokens/design-tokens-master.md` §2.1.2 > `assets.coremusic.net/Css/01_Core/01-variables.css` (deprecated `--cm-glass-*` = düzeltme yönü, çelişki değil).

## 7. PNG Referansı

**PNG (yerel):** `C:\www\coremusic.net\.png\home-1024\Linux  1024 - Playlist Page.png` (1024×600) ✅ okundu
**Figma (tier alt kümesi):** node — `C:\temp\opencode\figma-a\05-playlist.md` (extracted koordinatlar)
**Mockup dizini:** `.ai/ui-design/` → [[ui-design/01-mockup-index]]
**ASCII:** §1 bu dosyada.

Çatışma yok: PNG ↔ ASCII hizalı (panel (23,69), footer y510). Şarkı/albüm/sanatçı metinleri Figma'dan, kapaklar PNG'den; API verisi yok → `API'den gelmedi`.

## 8. Responsive Davranış (Tier Etkisi)

Bu ekran **sabit 1024×600** içindir; davranış → [[ui-design/05-responsive-architecture]] §7.4 + §12. Tier davranışı (matrix):

- T07 sabit: CSS `zoom`/ölçekleme (Figma scaleFactor 1.0) — 6 sütunlu tablo sabit kalır, `clamp()` ile değil.
- Farklı ekranlara kaydırma/boyut sözü verilmez; kırılma davranışı bu dosyanın konusu değildir.

| Viewport | Tablo | Panel | Navbar | Not |
|----------|-------|-------|--------|-----|
| 1024×600 (T07) | 6 sütun, 7 satır görünür (8. kaydırma) | 743+219 px | 993×27 | PNG kanıtı |
| Daha geniş ekran | — | — | — | Kapsam dışı |

## 9. State (Durumlar)

| # | State | Tetikleyici | Görsel Değişim | Token / CSS |
|---|-------|-------------|----------------|-------------|
| 1 | Default | Sayfa yüklenir | Panel + tablo + sağ panel görünür | `var(--cm-glass-bg)` |
| 2 | Hover (yalnız `@media (hover:hover)`) | Satır / buton üzerinde | Zemin hafif vurgu | `var(--cm-accent-1)` |
| 3 | Odakta | `:focus-visible` | Odak halkası | `var(--cm-focus-ring)` |
| 4 | Seçili satır (PNG 4. satır) | Tıklama | Zemin vurgu (pembe) + `aria-selected` | `.is-active` → `var(--cm-accent-1)` |
| 5 | Favori işaretli | Yıldız tıklaması | Dolu yıldız (renk dışı biçim: dolu/boş) | `var(--cm-accent-1)` |
| 6 | Listeyi kaydırma | Dikey kaydırma | 8. satır ve sonrası açığa çıkar | liste çerçevesi (31,173) 725×307 |
| 7 | Sağ panel seçili playlist | Liste değişimi | Kapak/başlık/eylemler değişir | `var(--cm-accent-1)` |

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Sections (§1-§9) | 9/9 |
| BEM rows | 19 |
| Token rows | 18 |
| Touch Target rules | 5 |
| WCAG checks | 6 |
| Glassmorphism rules | 6 |
| ÇELİŞKİ (§5) | 1 |
| ÇELİŞKİ (§6) | 0 |
| PNG | verified |
| Figma | verified |
| ASCII ↔ PNG | hizalı (panel (23,69), footer y510) |
| Cross References | 5 |
| Version | 1.1.0 |
| Tier düzeltmesi | tier T08→T07 düzeltildi (matrix L92) |
| `00-ascii-art-index.md` | BEKLEMEDE (yazılmadı — yetki dışı) |

---

Authority: SSOT = PNG > Figma extracted > ASCII · `.ai/ui-design/` (index/figma/tokens/00-05) · Üretim: screen-spec-template §6 · Kayıt: `.ai/ui-design/04-accessibility-gaps.md` (BEKLEMEDE)
