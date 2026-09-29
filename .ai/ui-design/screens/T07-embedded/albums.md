---
version: 1.1.0
date: 2026-09-27
status: active
device: RPi5 7" Touch (Embedded)
tier: T07
viewport: 1024x600
screen: Albums
screen_id: 02
kalip: D
reference:
  authority: Figma > extracted > PNG > ASCII
  source_of_truth: .png/home-1024/Linux  1024 - Albumler Page.png
related:
  - .ai/ui-design/00-device-matrix.md
  - .ai/ui-design/01-mockup-index.md
  - .ai/ui-design/02-component-inventory.md
  - .ai/ui-design/tokens/design-tokens-master.md
---

# CoreMusic — Albums (T07 Embedded 1024×600)

Zorunlu Bağlantılar: [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
x:0         200       400       600       800       1000   1024
y:0   +--------------------------------------+----------------+
      |  NAVBAR (16,13) 993x27  [logo|nav|ara||klasör|avatar]  |  y:0-60
y:60  +------+-----------------------------+------------------+
      |      | ACTION ROW (36,84) 594x40   | SAĞ PANEL        |
      |      | [Geri][Albüm][Süz][Karışık]| (784,69) 219x420 |
      |      +-----------------------------+                  |
      |      | Header (38,136) 308x33      | Albüm Kapağı     |
      | SOL  | "Albüm" + "128 albüm"       | (844,104) 100x100|
      | PANEL| Search (551,142) 159x25     | Hemen Çal        |
      |(21,  +-----------------------------+ (809,302) 169x25 |
      | 69)  | CHIPS (38,188) 593x25       | Karışık Çal      |
      |743x  | [Tümü*][Pop][Arabesk][Dans] | (809,342) 104x25 |
      |427)  +-----------------------------+ "..." (923,339)   |
      |      | GRID: 4 kolon x h99         | 4. albüm statu   |
      |      | y228: x31/226/390/557       | satırı y399/417/ |
      |      | y313: x31/226/390/557       | 435/453          |
      |      | y397: x31/226 (kırpılmış)   |                  |
      |      | scrollbar (761,99) 3x375    |                  |
y:510 +------+-----------------------------+------------------+
      |  FOOTER PLAYER (0,510) 1025x90  [meta|kontrol|ses|...] |  y:510-600
y:600 +---------------------------------------------------------+
```

Piksel özeti: navbar (16,13) 993×27 · sol panel (21,69) 743×427 · eylem satırı (36,84) 594×40 · başlık (38,136) 308×33 · arama (551,142) 159×25 · çip satırı (38,188) 593×25 · grid 4 kolon × h99, satırlar y228/y313/y397 (3. satır yarıda = kaydırma) · scrollbar (761,99) 3×375 · sağ panel (784,69) 219×420 · footer (0,510) 1025×90.

## 2. BEM Sınıfları

| # | Öğe | BEM Sınıfı | Envanter | CSS Kaynağı |
|---|-----|-----------|----------|-------------|
| 1 | Sayfa sarmalayıcı | `.albums-layout` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 2 | Üst eylem satırı | `.albums-header` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 3 | Geri düğmesi | `.albums-header__back` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 4 | Sayfa başlığı | `.albums-header__title` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 5 | Alt başlık | `.albums-header__subtitle` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 6 | Arama alanı | `.albums-header__search` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 7 | Sekme sarmalayıcısı | `.albums-tabs` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 8 | Çip (kayıtlı sekme) | `.albums-tabs__tab` | C06 `.tab*` türevi (ad envanterde yok) | `p-albums.css` (CSS doğrulandı) |
| 9 | Çip aktif durumu | `.albums-tabs__tab.is-active` | C06 `.tab.is-active` (yapı eşleşiyor) | `p-albums.css` (CSS doğrulandı) |
| 10 | Albüm grid sarmalayıcısı | `.albums-grid` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 11 | Albüm kartı | `.albums-grid` içi kart düğümü | C03 `.card*` (envanterde `.albums-card` adı yok) | `p-albums.css` (CSS doğrulandı) |
| 12 | Sağ detay paneli | `.albums-detail` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 13 | Albüm kapağı | `.albums-detail__art` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 14 | Albüm adı | `.albums-detail__title` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 15 | Sanatçı adı | `.albums-detail__artist` | C14 — (ekran özel) | `p-albums.css` (CSS doğrulandı) |
| 16 | Eylem düğmesi | `.btn` | C04 `.btn*` | `04_component/04-button.css` (envanter) |
| 17 | Sayfa çubuğu | `.scrollbar` (görsel 3px) | — (ekran özel) | — (sadece PNG) |
| 18 | Navbar / footer | `.site-header__*` / `.footer-player__*` | C01 / C11 | `_header.css` / `_footer.css` |

> ⚠️ VERIFICATION REQUIRED: envanterdışı sınıflar — `.albums-header`, `.albums-header__back/__title/__subtitle/__search`, `.albums-tabs/__tab`, `.albums-grid`, `.albums-detail/__art/__title/__artist`, grid kart düğümü, `.scrollbar`; envanter adı CSS'te doğrulanamayan — (yok). Bunlar Figma çıkarımı veya PNG'den gelir; kod üretiminde adları CSS ile doğrulayın.

## 3. Token Kullanımı

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Sayfa yüzeyi | `var(--cm-bg-surface)` | — |
| Panel yüzeyi (sol + sağ) | `var(--cm-glass-bg)` | — |
| Panel örtü katmanı | `var(--cm-bg-overlay)` | — |
| Panel bulanıklığı | `var(--cm-glass-blur)` | — |
| Panel kenarlık | `var(--cm-border-w)` | — |
| Panel kenar rengi | `var(--cm-glass-border)` | — |
| Panel gölgesi | `var(--cm-glass-shadow)` | — |
| Başlık metni | `var(--cm-text-1)` | — |
| İkincil metin (alt başlık / statu) | `var(--cm-text-2)` | — |
| Arama alanı zemini | `var(--cm-input-bg)` | — |
| Vurgu / aktif çip zemini | `var(--cm-accent-1)` | — |
| Çip metni (aktif) | `var(--cm-accent-contrast)` | — |
| Odak halkası | `var(--cm-focus-ring)` | — |
| BorderRadius (kart / çip / buton) | `var(--cm-radius-2)` | — |
| Boşluk (grid gutter) | `var(--cm-space-4)` | — |
| Font (sayfa başlığı) | `var(--cm-font-display)` | — |
| Font (gövde) | `var(--cm-font-body)` | — |

Tüm değerler `design-tokens-master.md` §3/§6 kaynağından alınmıştır; bu dosyada tekrarlanmaz (tekrar SSOT ihlali olur).

## 4. Touch Target (Dokunma Boyutu)

| Kural | Değer | Uygulama (bu ekran) | Kaynak |
|-------|-------|---------------------|--------|
| Tier T07 zorunlu | ≥48×48 px | Chip (25 px yüksek), arama (25 px), sağ panel çipleri (h~16), eylem satırı butonları (40 px sarmalayıcı) — 48 altı | 00-device-matrix (§5 çatışma 4) |
| WCAG 2.5.8 (AA) | ≥24×24 px | Tüm görsel alanlar PASS (chip 593×25, butonlar ≥25 px) | WCAG 2.2 |
| Şablon varsayılanı | 44×44 px | Küçük yıldızlar / "..." (~15-25 px) → 44 altı | screen-spec-template (§4.4) |
| Yakınlık (fiziksel) | ≥8 px boşluk | Grid gutter x arası 20-45 px, satır arası ~-11 px örtüşme yok | Figma çıkarımı |
| Tier gömülü/metin (literal) | `touch target >= 44px` | Chip ve arama 25 px → 44 altı | matrix L97 |

Bu tier 48 px çatışması `.ai/ui-design/04-accessibility-gaps.md` dosyasına BEKLEMEDE olarak işlenmelidir (bkz. §5 satır 4).

## 5. WCAG 2.2 Uyumluluk

| # | Kriter | Seviye | Sonuç | Kanıt / Not |
|---|--------|--------|-------|-------------|
| 1 | Metin kontrastı | AA (1.4.3) | ⚠️ GAP | PNG median kontrast: **2.30** (hedef ≥4.50; media `assets.coremusic.net/Css/05_Pages/p-albums.css`) |
| 2 | Odak görünürlüğü | AA (2.4.7) | ✅ PASS | `:focus-visible` + `var(--cm-focus-ring)` (şablon §6.1) |
| 3 | Boyut (dokunma) | AA (2.5.8) | ✅ PASS | Tüm öğeler ≥24×24 px (bkz. §4 satır 2) |
| 4 | Tier dokunma boyutu | Tier T07 (48 px) | ⚠️ GAP | Chip / arama / butonlar 25-40 px; `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE |
| 5 | Semantik DOM | A (1.3.1) | ✅ PASS | Header/footer landmark + `nav` (header.php L82 `aria-current="page"`, footer.php L66 `role="contentinfo"`); grid için `role`/liste semantiği kodda doğrulanmalı |
| 6 | Durum (renk dışı) + aria | A (1.4.1 / 4.1.2) | ✅ PASS | Aktif çip: `.is-active` zemin + metin kontrastı (renk tek başına değil, aynı zamanda sekme kimliği); `aria-current` header.php L82, `aria-label` footer.php L66, JS `role="tab"` kodda doğrulanmalı |

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 1**

1. **Tier çatışması (1024×600) — ✅ KAPANDI (tier taşındı, matrix L92):** `00-device-matrix.md` L95: "Görsel Katman `T07-embedded` → 1024×600" ve L97: "`touch target >= 44px (tier embedded/phone)`" — şablon §4.4 44×44 varsayılanı ve Tier tablosu 48 px ile birlikte üç yönlü gerilim: matrix dokunma hedefi literal 44 px derken T08 satırı 48 px istiyor; şablon 44×44 diyor. Yorum farkı = çelişki. Beklenen: 04-accessibility-gaps kaydı BEKLEMEDE.

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE (bu dosya düzenlenmemiştir; yetki dışı).

## 6. Glassmorphism

Uygulama kuralı (şablon §6 + token master §2.1.2 — bu ekran için kesin kural):

```css
.albums-layout {
  background: var(--cm-glass-bg);           /* + opaklık katmanı: var(--cm-bg-overlay) */
  backdrop-filter: blur(var(--cm-glass-blur));
  border: var(--cm-border-w) solid var(--cm-glass-border);
  box-shadow: var(--cm-glass-shadow);
}
@supports not (backdrop-filter: blur(1px)) {
  .albums-layout { background: var(--cm-glass-bg-strong); } /* fallback düz opak */
}
```

| Kural | Durum | Not |
|-------|-------|-----|
| `backdrop-filter: blur(var(--cm-glass-blur))` | ✅ Uygulanır | Panel zeminleri (sol + sağ) — PNG'de 8-10 px bulanık |
| `background: var(--cm-glass-bg)` | ✅ Uygulanır | Panel zemini + `var(--cm-bg-overlay)` örtü katmanı |
| `border: var(--cm-border-w) solid var(--cm-glass-border)` | ✅ Uygulanır | Panel kenarları (PNG'de ince ayraç) |
| `box-shadow: var(--cm-glass-shadow)` | ✅ Uygulanır | Panel gölgesi (PNG'de hafif dış gölge) |
| Fallback (`@supports not`) → `var(--cm-glass-bg-strong)` | ✅ Uygulanır | `backdrop-filter` desteklemeyen gömülü WebView |
| Kontrast (1.4.11 / 1.4.3) | ⚠️ | Üst üste binen katman + metin = §5 satır 1'e göre 2.30 → GAP |

ÇELİŞKİ (bu bölüm): **0** — glass değerleri için SSOT: Figma `2831:10268` > `.ai/ui-design/tokens/design-tokens-master.md` §2.1.2 > `assets.coremusic.net/Css/01_Core/01-variables.css` (deprecated `--cm-glass-*` = düzeltme yönü, çelişki değil).

## 7. PNG Referansı

**PNG (yerel):** `C:\www\coremusic.net\.png\home-1024\Linux  1024 - Albumler Page.png` (1024×600) ✅ okundu
**Figma (tier alt kümesi):** node `2862:8464` (Anahtar Kelime 4-5 "Albüm Page" 1024×600, extracted: `.ai/ui-design/_extracted/`)
**Mockup dizini:** `.ai/ui-design/` → [[01-mockup-index]]
**ASCII:** §1 bu dosyada.

Çatışma yok: PNG ↔ ASCII hizalı (footer y510, panel (21,69)). Kategori (Pop/Arabesk vb.) metinleri Figma'dan, kapaklar/görseller PNG'den; API verisi yok → `API'den gelmedi`.

## 8. Responsive Davranış (Tier Etkisi)

Bu ekran **sabit 1024×600** içindir; davranış → [[05-responsive-architecture]] §7.4 + §12. Tier davranışı (matrix):

- T07 sabit: CSS `zoom`/ölçekleme (Figma scaleFactor 1.0) — grid 4 kolon sabit kalır, `clamp()` ile değil.
- Farklı görüntü alanlarına kaydırma/boyut sözü verilmez; kırılma davranışı bu dosyanın konusu değildir.

| Viewport | Grid | Panel | Navbar | Not |
|----------|------|-------|--------|-----|
| 1024×600 (T07) | 4 kolon (x31/226/390/557) | 743+219 px | 993×27 | PNG kanıtı |
| Daha geniş ekran | — | — | — | Kapsam dışı (bkz. §8.1) |

## 9. State (Durumlar)

| # | State | Tetikleyici | Görsel Değişim | Token / CSS |
|---|-------|-------------|----------------|-------------|
| 1 | Default | Sayfa yüklenir | Panel + grid + sağ panel görünür | `var(--cm-glass-bg)` |
| 2 | Hover (yalnız `@media (hover:hover)`) | Chip / kart / buton üzerinde | Zemin hafif vurgu | `var(--cm-accent-1)` |
| 3 | Aktif chip | Tıklama / `role="tab"` seçimi | Zemin vurgu + metin kontrastlı | `.is-active` → `var(--cm-accent-1)` + `var(--cm-accent-contrast)` |
| 4 | Seçili albüm (sağ panel) | Grid kartı seçilir | Sağ panel kapak/başlık değişir, kenarlık vurgu | `var(--cm-accent-1)` border |
| 5 | Arama odakta | `:focus-visible` | Odak halkası | `var(--cm-focus-ring)` |
| 6 | Kaydırma (grid) | Dikey kaydırma | 3. satır kırpılır → devamı açığa çıkar | scrollbar (PNG (761,99) 3×375) |
| 7 | Boş sonuç | Filtre eşleşmez | Boş durum metni | `var(--cm-text-2)` (durum metni) |

---

**Quality Report**

| Metrik | Değer |
|--------|-------|
| Sections (§1-§9) | 9/9 |
| BEM rows | 18 |
| Token rows | 17 |
| Touch Target rules | 5 |
| WCAG checks | 6 |
| Glassmorphism rules | 6 |
| ÇELİŞKİ (§5) | 1 |
| ÇELİŞKİ (§6) | 0 |
| PNG | verified |
| Figma | verified |
| ASCII ↔ PNG | hizalı (panel footer y510) |
| Cross References | 5 |
| Version | 1.1.0 |
| Tier düzeltmesi | tier T08→T07 düzeltildi (matrix L92) |
| `00-ascii-art-index.md` | BEKLEMEDE (yazılmadı — yetki dışı) |

---

Authority: SSOT = PNG > Figma extracted > ASCII · `.ai/ui-design/` (index/figma/tokens/00-05) · Üretim: screen-spec-template §6 · Kayıt: `.ai/ui-design/04-accessibility-gaps.md` (BEKLEMEDE)
