---
title: "ui-analyzer Reference — Analiz Derinliği (Metodoloji & Ölçüm)"
type: reference
version: 4.0.0
updated: 2026-10-21
---

# Analiz Derinliği — Metodoloji, Mockup Protokolü, Ölçüm

`SKILL.md` §4-§7'nin derinlik dosyası (orphan=0). Kaynaklar: `.ai/CLAUDE.md` §7.1
(Guardrail #11) · `.ai/ui-design/**` · kök `AGENTS.md` §10 · web kaynakları
(`sources.md` S1-S4).

---

## 1. Analiz Sırası (bağlayıcı — ADIM 2)

```text
.ai/ui-design/01-mockup-index.md       → hangi mockup'lar var? (PNG indeksi + tier matrisi)
.ai/ui-design/02-component-inventory.md → C01-C16 BEM sınıfları, pixel ölçümleri
.ai/ui-design/tokens/design-tokens-master.md → renk/boşluk/typo/cam token'ları
        ↓
Hedef kod (CSS/JS) veya canlı sayfa (Chrome DevTools) — ancak bu 3 referans OKUNDUKTAN sonra
```

**Çelişkide referans sırası (Guardrail #11, bağlayıcı):**

```text
PNG (ilk) > ASCII art > Component Inventory > Tokens > Implementation Plan
```

1. **PNG** — `.ai/.png/` onaylı görsel. PNG her şeyi ezer; "ben gördüm" iddiası PNG'siz yazılmaz.
2. **ASCII art** — `.ai/ui-design/screens/00-ascii-art-index.md` (piksel layout: 1024×600 →
   Header 60px y:0-60 · İçerik 450px y:60-510 · Footer 90px y:510-600).
3. **Component Inventory** — C01-C16 BEM + ölçüler.
4. **Tokens** — design token tabloları.
5. **Implementation Plan** — en düşük öncelik.

**Kural:** Görsel okunamıyorsa → **DUR** + kullanıcıya bildir (Guardrail #11 ihlal
prosedürü). Okunmayan değer `⚠️ VERIFICATION REQUIRED` — uydurma piksel/renk/ölçü YASAK.

---

## 2. Girdi Türleri (ADIM 1)

| Girdi | Okuma yolu | Not |
|-------|-----------|-----|
| PNG mockup | `.ai/.png/` + `01-mockup-index.md` | görseli oku, token tablosuyla doğrula |
| Font dosyası | .ttf / .otf / .woff / .woff2 | §4 font analizi |
| Canlı sayfa | Chrome DevTools MCP (read-only) | computed style, DOM, @layer sırası |
| Mevcut kod | CSS/JS dosyaları | `token-itcss-audit.md` denetimleri |
| Belge/spec | `.ai/ui-design/**` markdown | Salt okunur; spec ≠ görsel ise PNG kazanır |

> `getComputedStyle` canlı ve salt-okunurdur (`sources.md` S4) → denetim nesnesi
> değiştirmez; READ-ONLY kimlikle uyumlu.

---

## 3. Mockup ↔ Kod Ölçüm Protokolü (ADIM 2-3)

| # | Ölçüm | Kaynak (mockup) | Kaynak (canlı/kod) | Tolerans |
|---|-------|-----------------|---------------------|----------|
| 1 | Layout bölgeleri | ASCII: Header 60 / İçerik 450 / Footer 90 (1024×600) | DOM ölçüleri (DevTools) | ±2px |
| 2 | Renk paleti | PNG + token tablosu | computed styles / custom props | HEX birebir (token) |
| 3 | Tipografi | PNG + inventory (font, boyut, weight) | computed font | ±0.5px / ağırlık birebir |
| 4 | Grid | PNG (sütun/gutter) | CSS grid/flex ölçüleri | ±2px |
| 5 | Boşluk hiyerarşisi | token spacing scale (4→64px) | hesaplanmış padding/margin | token katmanına oturmalı |
| 6 | BEM sınıfları | inventory C01-C16 | DOM class'ları | birebir eşleşme |
| 7 | Cihaz davranışı | tier matrisi (`01-mockup-index.md`) | media query'ler | tier kuralına uyum |

**Her satır 4'lüsü** (Ölçüm + Kaynak + Kanıt + Sapma) olmadan rapora girmez — kanıtsız
bulgu yok. Uyumlu ölçüler (sapma=0) de rapora girer: "bulunamadı" kanıttır, sessiz geçme yok.

**Tolerans yorumu:**

| Sapma | Değerlendirme |
|-------|---------------|
| ≤ ±2px (ölçü) | LOW — kozmetik/tolerans içi |
| ±2-4px (ölçü) | MEDIUM — tolerans dışı ama işlevsel |
| > ±4px veya oran kırıldı | HIGH — layout/ölçü kırıldı |
| HEX farkı (token) | HIGH — token ihlali (ham değer kullanımı) |
| Font ağırlığı farkı | HIGH — birebir eşleşme kuralı |

---

## 4. Ölçüm Teknikleri (rapor §2-§7 girdisi)

### 4.1 Font Analizi (rapor §6)

```text
- Font ailesi adı (family name)
- Weight aralığı (100-900) + style (normal/italic/oblique)
- Glyph kapsamı (Latin, Latin Ext — Türkçe karakter, Cyrillic, CJK)
- OpenType özellikleri (liga, calt, ss01, tabular-nums)
- Web font formatı (WOFF2 tercih, WOFF fallback, TTF/OTF kaynak)
- Dosya boyutu ve optimize edilebilirlik
- Variable font desteği (opsiyonel: wght/wdth/slnt axis)
- Google Fonts API doğrulaması (varsa — yoksa UNKNOWN, tahmin yok)
```

Canlı sayfada: `getComputedStyle(el).fontFamily / fontSize / fontWeight / lineHeight`
(`sources.md` S4 — resolved value). Font ailesi yoksa veya `font-display: block` ile
gizleniyorsa `UNKNOWN` yazılır.

### 4.2 Renk Paleti Çıkarımı (rapor §2)

```text
- Ana renkler (primary, secondary, accent)
- Nötr renkler (gray scale) + durum renkleri (success, warning, error, info)
- Her renk için KAYNAK: token var mı? (var(--cm-*) | ham hex | PNG-only)
- Kontrast oranı ön-hesabı (yalnız bulgu üretir; tam denetim → wcag-criteria.md)
- Renk uyumu analizi (analog, complementary, triadic)
- Dark mode paleti · Forced colors (high contrast) kontrolü
```

**Kontrast ön-hesabı (W3C kaynağı — `sources.md` S2):**

```text
oran = (L1 + 0.05) / (L2 + 0.05)      L1 = daha parlak rengin göreli parlaklığı
L = 0.2126R + 0.7152G + 0.0722B        (linearize edilmiş sRGB kanalları)
Normal metin ≥ 4.5:1 · Large text ≥ 3:1 (≥18pt / ≥14pt bold ≈ 24px / 18.5px)
UI bileşeni / grafik ≥ 3:1 (SC 1.4.11)
```

Sonuç yuvarlanmaz (4.499 = geçersiz). Tam kriter listesi: `wcag-criteria.md`.
Renk çelişkisinde bağlayıcı: token tablosu < PNG. Ham hex → token sapması ihlal adayı.

### 4.3 Grid & Boşluk Hiyerarşisi (rapor §4-5)

**Grid:** breakpoint kırılımında sütun / gutter / margin çıkar; ASCII 1024×600 referansı
ile ±2px karşılaştır. Örnek beklenen ölçek: 320px+ → 4 sütun · 768px+ → 8 sütun ·
1024px+ → 12 sütun.

**Boşluk ölçeği (token spacing):**

| Seviye | Değer | Kullanım |
|--------|-------|----------|
| xs | 4px | iç padding |
| sm | 8px | eleman arası |
| md | 16px | bileşen içi |
| lg | 24px | bölüm arası |
| xl | 32px | büyük bölüm arası |
| 2xl | 48px | sayfa üst boşluğu |
| 3xl | 64px | ana bölüm başlığı |

Ölçüm bu ölçeğe oturmuyorsa (ör. 20px) → token sapması adayı. Proje zemini:
`--touch-min: 48px` (`.ai/.templates/frontend/css-template.md` C2) — dokunma hedefi
ölçümünde hem WCAG 2.5.8 (24px) hem proje eşiği (48px) ayrı ayrı raporlanır.

### 4.4 Responsive Breakpoint Analizi (rapor §7)

```text
- Mevcut CSS'teki media query'leri çıkar → breakpoint değerlerini listele
- Tier cihaz matrisi (01-mockup-index.md) ile karşılaştır → eksik tier'ları işaretle
- Mobile-First sıralamayı kontrol et (min-width akışı)
- clamp() ile fluid typography kontrolü
- Breakpoint değeri px sabit olmalı; @media içinde var() YASAK (kök AGENTS §10)
```

Rapor §7 çıktısı: `| Breakpoint | Durum | Not |` — eksik breakpoint LOW/MEDIUM,
tier kırılımı (yanlış sütun düzeni) HIGH sayılır.

### 4.5 Component State Matrix Denetimi

Bileşen başına 10 durum ayrı ayrı kontrol edilir (kayıt: `UNKNOWN` ise durum yok sayılır):

```text
DEFAULT · HOVER · ACTIVE · FOCUSED · DISABLED · ERROR · LOADING · EMPTY · SELECTED · OPEN(collapsed/açık)
```

Her durum için soru: (a) PNG/inventory'de var mı? (b) CSS'te tanımı var mı? (c) focus
görünür mü (SC 2.4.7 — outline 3px, token'lı)? Eksik durum = MEDIUM (tasarım sistemi
tutarlılığı); FOCUSED eksikliği veya görünmez focus = HIGH (WCAG ihlal adayı →
`wcag-criteria.md`).

---

## 5. Durdurucu Koşullar (DUR + bildir)

| # | Koşul | Aksiyon |
|---|-------|---------|
| 1 | PNG okunamıyor (dosya yok / görsel bozuk) | DUR: 1) kod revert (varsa), 2) `log.md` CRITICAL, 3) Vault Steward bildirimi |
| 2 | Referans dosyalardan hiçbiri diskte yoksa | `⚠️ VERIFICATION REQUIRED` + DUR |
| 3 | Tier/matris hedefi belirsizse | Tier uydurulmaz, kullanıcıya sorulur |
| 4 | WCAG eşiği/emniyetli iddia doğrulanamıyorsa | `⚠️ VERIFICATION REQUIRED` — `sources.md`'e eklenmeden yazılmaz |
| 5 | `.ai/.png/` dizini görünmüyor (araç `hidden:false`) | `hidden: true` ile yeniden dene; hâlâ yoksa DUR |

---

## 6. Salt-Okunur Sınır (bu dosyanın dışına taşmaz)

- Kod üretmez · dosya değiştirmez/silmez/taşımaz · CSS/JS/HTML düzenlemez.
- Düzeltme önerileri yalnız **devir satırı** olarak yazılır → `ui-code-generator`.
- Taşıma/silme gerektiren bulgular → **onay + ADR** (onaysız eylem yasak).

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
