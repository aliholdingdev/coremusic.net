---
version: 1.0.0
date: 2026-09-27
status: active
device: RPi5 7" Touch (Embedded)
tier: T08
viewport: 1024x600
screen: Playlist Video
screen_id: 06
kalip: D
reference:
  authority: Figma > extracted > PNG > ASCII
  source_of_truth: .png/home-1024/Linux  1024 - Playlist Page - Video Played.png
related:
  - .ai/ui-design/00-device-matrix.md
  - .ai/ui-design/01-mockup-index.md
  - .ai/ui-design/02-component-inventory.md
  - .ai/ui-design/tokens/design-tokens-master.md
---

# CoreMusic — Playlist Video (T08 Embedded 1024×600)

Zorunlu Bağlantılar: [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
x:0         200       400       600       800       1000   1024
y:0   +------+--------------------------+-------------------+
      | BACK |  VİDEO ALANI (tam boy)   | PLAYLİST PANELİ   |
      |(14,  |  başlık (63,27)          | (723,31) 276x538  |
      | 16)  |                          | başlık "Playlist" |
      |40x40 +                          | 10 satır 261x30   |
      |      |  video / boş alan        | y75 adım 45       |
      |      |  (navbar YOK,            | scrollbar         |
      |      |   footer YOK)            | (988,70) 3x490    |
      |      |                          | .is-active satır  |
      |      |                          | (border-left 3px) |
      |      |                          +-------------------+
      |      |  .player-mini            |
      |      |  (48,400) 290x120        |
      |      |  [kapak|başlık|kontroller|
      |      |   progress]              |
      |      +--------------------------+
      | mp3 (9,570) 30x15  ★★★★★ x44..100 y569 15x15
y:595 +-------------------------------------------------------+
      | PEMBE BAR (0,595) 1024x5                             |
y:600 +-------------------------------------------------------+
```

Piksel özeti: navbar/footer **yok** (tam ekran oynatıcı) · geri (14,16) 40×40 · başlık (63,27) · sağ playlist paneli (723,31) 276×538, 10 satır 261×30, y75, adım 45 · scrollbar (988,70) 3×490 · mini oynatıcı `.player-mini` (48,400) 290×120 · "mp3" etiketi (9,570) 30×15 + 5 yıldız x44..100, y569, 15×15 · pembe bar (0,595) 1024×5 → toplam y:600.

## 2. BEM Sınıfları

| # | Öğe | BEM Sınıfı | Envanter | CSS Kaynağı |
|---|-----|-----------|----------|-------------|
| 1 | Sayfa (oynatıcı) sarmalayıcı | `.page-player` + `.page-player--embedded` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 2 | Geri düğmesi | `.player-back` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 3 | Arka plan görseli | `.player-bg__image` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 4 | Arka plan örtüsü | `.player-bg__overlay` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 5 | Sağ playlist başlığı | `.player-playlist__header/__title/__count` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 6 | Playlist sarmalayıcısı | `.player-playlist__list` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 7 | Playlist satırı | `.player-playlist__item` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 8 | Aktif satır | `.player-playlist__item.is-active` | C06 `.is-active` deseni | `_player.css` (CSS doğrulandı: zemin + `border-left: 3px`) |
| 9 | Satır küçük resim | `.player-playlist__thumb` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 10 | Satır bilgi bloğu | `.player-playlist__info` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 11 | Satır başlık | `.player-playlist__track-title` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 12 | Satır sanatçı | `.player-playlist__track-artist` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 13 | Satır süre | `.player-playlist__duration` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 14 | Mini oynatıcı | `.player-mini` (+ `__*` ailesi) | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 15 | Görsel sanat alanı | `.player-visual__art/__image/__glow` | C14 — (ekran özel) | `_player.css` (CSS doğrulandı) |
| 16 | Sayfa çubuğu | `.scrollbar` (görsel 3px) | — (ekran özel) | — (sadece PNG) |
| 17 | Alt pembe bar | — (PNG (0,595) 1024×5) | — (ekran özel) | — (CSS doğrulanamadı) |
| 18 | "mp3" + yıldızlar | — (PNG (9,570) / (44,569)) | — (ekran özel) | — (CSS doğrulanamadı) |

> ⚠️ VERIFICATION REQUIRED: envanterdışı sınıflar — `.page-player(/--embedded)`, `.player-back`, `.player-bg__*`, `.player-playlist__(__header/__title/__count/__list/__item/__thumb/__info/__track-title/__track-artist/__duration)`, `.player-mini*`, `.player-visual__*`, `.scrollbar`; envanter adı CSS'te doğrulanamayan — alt pembe bar, "mp3"+yıldız bloğu (yalnız PNG; PHP/markup kanıtı yok: video sayfası PHP'si repoda yok, CSS-only). Bunlar Figma çıkarımı veya PNG'den gelir; kod üretiminde adları CSS ile doğrulayın.

## 3. Token Kullanımı

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Sayfa yüzeyi (tam ekran) | `var(--cm-bg-surface)` | — |
| Panel yüzeyi (sağ liste) | `var(--cm-glass-bg)` | — |
| Panel örtü katmanı | `var(--cm-bg-overlay)` | — |
| Panel bulanıklığı | `var(--cm-glass-blur)` | — |
| Panel kenarlık | `var(--cm-border-w)` | — |
| Panel kenar rengi | `var(--cm-glass-border)` | — |
| Panel gölgesi | `var(--cm-glass-shadow)` | — |
| Başlık metni | `var(--cm-text-1)` | — |
| İkincil metin (sanatçı / süre) | `var(--cm-text-2)` | — |
| Aktif satır zemini | `var(--cm-accent-1)` | — |
| Aktif satır metni | `var(--cm-accent-contrast)` | — |
| Vurgu / ilerleme çubuğu | `var(--cm-accent-1)` | — |
| Odak halkası | `var(--cm-focus-ring)` | — |
| BorderRadius (mini oynatıcı) | `var(--cm-radius-3)` | — |
| Boşluk (liste satır padding) | `var(--cm-space-3)` | — |
| Font (başlık) | `var(--cm-font-display)` | — |
| Font (gövde) | `var(--cm-font-body)` | — |

Tüm değerler `design-tokens-master.md` §3/§6 kaynağından alınmıştır; bu dosyada tekrarlanmaz (tekrar SSOT ihlali olur).

## 4. Touch Target (Dokunma Boyutu)

| Kural | Değer | Uygulama (bu ekran) | Kaynak |
|-------|-------|---------------------|--------|
| Tier T08 zorunlu | ≥48×48 px | Geri 40×40, liste satırı h30, yıldızlar 15 px, mini oynatıcı düğmeleri (~40 altı tahmin) | 00-device-matrix (§5 çatışma 4) |
| WCAG 2.5.8 (AA) | ≥24×24 px | ⚠️ 15 px yıldızlar (x44..100 y569) → GAP; geri 40 px PASS | WCAG 2.2 |
| Şablon varsayılanı | 44×44 px | Geri 40 px / satır 30 px / yıldız 15 px → 44 altı | screen-spec-template (§4.4) |
| Yakınlık (fiziksel) | ≥8 px boşluk | Liste satır arası 15 px boşluk (30+15 adım 45) ✓ | Figma çıkarımı |
| Tier gömülü/metin (literal) | `touch target >= 44px` | 40/30/15 px → 44 altı | matrix L97 |

Bu tier 48 px çatışması `.ai/ui-design/04-accessibility-gaps.md` dosyasına BEKLEMEDE olarak işlenmelidir (bkz. §5 satır 4).

## 5. WCAG 2.2 Uyumluluk

| # | Kriter | Seviye | Sonuç | Kanıt / Not |
|---|--------|--------|-------|-------------|
| 1 | Metin kontrastı | AA (1.4.3) | ⚠️ GAP | PNG median kontrast: **3.95** (hedef ≥4.50; media `assets.coremusic.net/Css/01_Core/` + `05_Pages/_player.css`) |
| 2 | Odak görünürlüğü | AA (2.4.7) | ✅ PASS | `:focus` outline (CSS doğrulandı) + `var(--cm-focus-ring)` (şablon §6.1) |
| 3 | Boyut (dokunma) | AA (2.5.8) | ⚠️ GAP | Yıldızlar ~15 px < 24 px (PNG ölçüleri) |
| 4 | Tier dokunma boyutu | Tier T08 (48 px) | ⚠️ GAP | Geri 40 / satır 30 / yıldız 15 px; `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE |
| 5 | Semantik DOM | A (1.3.1) | ✅ PASS (kısmi) | Oynatıcı landmark/markup PHP'de yok (repoda video sayfası markup'ı yok) — CSS var; HTML `aria-label` doğrulaması BEKLEMEDE |
| 6 | Durum (renk dışı) + aria | A (1.4.1 / 4.1.2) | ✅ PASS | Aktif satır: `.is-active` → `border-left: 3px` + zemin (renk dışı ikinci işaret, CSS kanıtı) + `:focus` outline; `aria-current` header.php L82 (bu ekranda header yok) / `aria-*` HTML BEKLEMEDE |

### ÇELİŞKİLER

**§5 ÇELİŞKİ sayısı: 1**

1. **Tier çatışması (1024×600):** `00-device-matrix.md` L95 "Görsel Katman `T08-embedded` → 1024×600" + L97 "`touch target >= 44px`" — Tier tablosu 48 px, şablon §4.4 44×44: yorum gerilimi (geri 40 / satır 30 / yıldız 15 px ikisinin de altında).

Not: bu ekranda navbar/footer yok (tam ekran oynatıcı) — standart 60+450+90 iskeleti uygulanmaz; §1'de tüm yükseklik video/liste/mini oynatıcı ile 600'e tamamlanır (pembe bar (0,595) 1024×5). Çelişki değil, ekran tipi farkı.

> GAP kayıtları → `.ai/ui-design/04-accessibility-gaps.md`: BEKLEMEDE (bu dosya düzenlenmemiştir; yetki dışı).

## 6. Glassmorphism

Uygulama kuralı (şablon §6 + token master §2.1.2 — bu ekran için kesin kural; sağ playlist paneli + mini oynatıcı):

```css
.player-playlist__list,
.player-mini {
  background: var(--cm-glass-bg);           /* + opaklık katmanı: var(--cm-bg-overlay) */
  backdrop-filter: blur(var(--cm-glass-blur));
  border: var(--cm-border-w) solid var(--cm-glass-border);
  box-shadow: var(--cm-glass-shadow);
}
@supports not (backdrop-filter: blur(1px)) {
  .player-playlist__list,
  .player-mini { background: var(--cm-glass-bg-strong); } /* fallback düz opak */
}
```

| Kural | Durum | Not |
|-------|-------|-----|
| `backdrop-filter: blur(var(--cm-glass-blur))` | ✅ Uygulanır | Sağ liste + mini oynatıcı zeminleri (PNG'de bulanık katman) |
| `background: var(--cm-glass-bg)` | ✅ Uygulanır | Zemin + `var(--cm-bg-overlay)` örtü katmanı |
| `border: var(--cm-border-w) solid var(--cm-glass-border)` | ✅ Uygulanır | Panel kenarları |
| `box-shadow: var(--cm-glass-shadow)` | ✅ Uygulanır | Panel gölgesi |
| Fallback (`@supports not`) → `var(--cm-glass-bg-strong)` | ✅ Uygulanır | `backdrop-filter` desteklemeyen gömülü WebView |
| Kontrast (1.4.11 / 1.4.3) | ⚠️ | Video üzeri katman + metin = §5 satır 1'e göre 3.95 → GAP |

ÇELİŞKİ (bu bölüm): **0** — glass değerleri için SSOT: Figma `2831:10268` > `.ai/ui-design/tokens/design-tokens-master.md` §2.1.2 > `assets.coremusic.net/Css/01_Core/01-variables.css` (deprecated `--cm-glass-*` = düzeltme yönü, çelişki değil).

## 7. PNG Referansı

**PNG (yerel):** `C:\www\coremusic.net\.png\home-1024\Linux  1024 - Playlist Page - Video Played.png` (1024×600) ✅ okundu
**Figma (tier alt kümesi):** node — `C:\temp\opencode\figma-a\07-playlist-video.md` (extracted koordinatlar)
**Mockup dizini:** `.ai/ui-design/` → [[01-mockup-index]]
**ASCII:** §1 bu dosyada.

Çatışma yok: PNG ↔ ASCII hizalı (panel (723,31), pembe bar y595, toplam 600). Video kaynağı/API verisi yok → `API'den gelmedi`; başlık/süre metinleri Figma/PNG'den.

## 8. Responsive Davranış (Tier Etkesi)

Bu ekran **sabit 1024×600** içindir; davranış → [[05-responsive-architecture]] §7.4 + §12. Tier davranışı (matrix):

- T08 sabit: CSS `zoom`/ölçekleme (Figma scaleFactor 1.0) — video/liste bölünmesi (696+276 px) sabit kalır, `clamp()` ile değil.
- Farklı ekranlara kaydırma/boyut sözü verilmez; kırılma davranışı bu dosyanın konusu değildir.

| Viewport | Video alanı | Playlist | Öğeler | Not |
|----------|-------------|----------|--------|-----|
| 1024×600 (T08) | ~x0-696 (tam boy) | (723,31) 276×538, 10 satır | geri 40×40, mini (48,400) 290×120 | PNG kanıtı |
| Daha geniş ekran | — | — | — | Kapsam dışı |

## 9. State (Durumlar)

| # | State | Tetikleyici | Görsel Değişim | Token / CSS |
|---|-------|-------------|----------------|-------------|
| 1 | Default (çalıyor) | Video oynatılır | Sağ listede aktif satır + ilerleme | `var(--cm-glass-bg)` |
| 2 | Hover (yalnız `@media (hover:hover)`) | Satır / düğme üzerinde | Zemin hafif vurgu | `var(--cm-accent-1)` |
| 3 | Odakta | `:focus` / `:focus-visible` | Odak halkası (CSS `:focus` outline doğrulandı) | `var(--cm-focus-ring)` |
| 4 | Aktif satır (çalınan) | Oynatma sırası değişir | Zemin vurgu + sol kenarlık 3px (renk dışı işaret) | `.is-active` → `var(--cm-accent-1)` + `border-left: 3px` |
| 5 | Duraklatma | Oynat/duraklat | İlerleme çubuğu donar, ikon değişir | `var(--cm-accent-1)` |
| 6 | Liste kaydırma | Dikey kaydırma | 10 satır arasında gezinme | scrollbar (PNG (988,70) 3×490) |
| 7 | Geri dönüş | `.player-back` tıklama | Playlist ekranına dönüş | `:focus` outline |

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
| ASCII ↔ PNG | hizalı (pembe bar y595 → 600) |
| Cross References | 5 |
| `00-ascii-art-index.md` | BEKLEMEDE (yazılmadı — yetki dışı) |

---

Authority: SSOT = PNG > Figma extracted > ASCII · `.ai/ui-design/` (index/figma/tokens/00-05) · Üretim: screen-spec-template §6 · Kayıt: `.ai/ui-design/04-accessibility-gaps.md` (BEKLEMEDE)
