---
title: "ui-analyzer Reference — Analiz Metodolojisi"
type: reference
version: 3.0.0
updated: 2026-10-07
---

# Analiz Metodolojisi (ADIM 1-3 detayı)

Bu dosya analiz **sırasını**, mockup↔kod **ölçüm protokolünü** ve font/renk/grid/
boşluk/breakpoint **çıkarma bölümlerini** içerir. Kaynaklar: `.ai/CLAUDE.md` §7.1
(Guardrail #11) · `.ai/ui-design/` · kök `AGENTS.md` §10.

---

## 1. Analiz Sırası (ADIM 2 — bağlayıcı sıra)

```text
.ai/ui-design/01-mockup-index.md      → hangi mockup'lar var? (19 PNG indeksi + 45-tier matris)
.ai/ui-design/02-component-inventory.md → C01-C16 BEM sınıfları, pixel ölçümleri
.ai/ui-design/tokens/design-tokens-master.md → renk/boşluk/typo/cam token'ları
        ↓
Hedef kod (CSS/JS) veya canlı sayfa (Chrome DevTools) — ancak bu 3 referans OKUNDUKTAN sonra
```

**Çelişki durumunda referans sırası (Guardrail #11, bağlayıcı):**

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

## 2. Girdi Türü (ADIM 1)

| Girdi | Okuma yolu | Not |
|-------|-----------|-----|
| PNG mockup | `.ai/.png/` + `01-mockup-index.md` | görseli oku, token tablosuyla doğrula |
| Font dosyası | .ttf / .otf / .woff / .woff2 | §4 font analizi |
| Canlı sayfa | Chrome DevTools MCP (read-only) | computed style, DOM, @layer sırası |
| Mevcut kod | CSS/JS dosyaları | §3, §5, §6, §7 denetimleri |

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
| 7 | Cihaz davranışı | 45-tier matrisi (`01-mockup-index.md`) | media query'ler | tier kuralına uyum |

**Her satır 4'lüsü** (Ölçüm + Kaynak + Kanıt + Sapma) olmadan rapora girmez —
kanıtsız bulgu yok. Severity dağıtımı: `design-system-criteria.md` §1 ·
rapora giriş formatı: `scoring-rubric.md`.

**DUR koşulları:** PNG okunamıyor → DUR + Guardrail #11 prosedürü · referans dosyalardan
hiçbiri diskte yoksa → `⚠️ VERIFICATION REQUIRED` + DUR · hedef 45-tier belirsizse →
tier uydurulmaz, kullanıcıya sorulur.

---

## 4. Font Analizi (rapor §6)

```text
✅ Font ailesi adı (family name)
✅ Weight aralığı (100-900) + style (normal/italic/oblique)
✅ Glyph kapsamı (Latin, Latin Ext — Türkçe karakter, Cyrillic, CJK)
✅ OpenType özellikleri (liga, calt, ss01, tabular-nums)
✅ Web font formatı (WOFF2 tercih, WOFF fallback, TTF/OTF kaynak)
✅ Dosya boyutu ve optimize edilebilirlik
✅ Variable font desteği (opsiyonel: wght/wdth/slnt axis)
✅ Google Fonts API doğrulaması (varsa — yoksa UNKNOWN, tahmin yok)
```

---

## 5. Renk Paleti Çıkarımı (rapor §2)

```text
✅ Ana renkler (primary, secondary, accent)
✅ Nötr renkler (gray scale) + durum renkleri (success, warning, error, info)
✅ Her renk için KAYNAK: token var mı? (var(--cm-*) | ham hex | PNG-only)
✅ Kontrast oranı ön-hesabı (yalnız bulgu üretir; tam denetim → wcag-2.2-checklist.md)
   → Normal metin min 4.5:1 · Büyük metin (18px+ bold / 24px+) min 3:1 · UI bileşeni min 3:1
✅ Renk uyumu analizi (analog, complementary, triadic)
✅ Dark mode paleti (light-dark() desteği) · Forced colors (high contrast) kontrolü
```

Renk çelişkisinde bağlayıcı: token tablosu < PNG. Ham hex bulunduğunda → token sapması
ihlal adayı (`design-system-criteria.md` §4).

---

## 6. Grid & Boşluk Hiyerarşisi (rapor §4-5)

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

Ölçüm bu ölçeğe oturmuyorsa (ör. 20px) → token sapması adayı.

---

## 7. Responsive Breakpoint Analizi (rapor §7)

```text
✅ Mevcut CSS'teki media query'leri çıkar → breakpoint değerlerini listele
✅ 45-tier cihaz matrisi (01-mockup-index.md) ile karşılaştır → eksik tier'ları işaretle
✅ Mobile-First sıralamayı kontrol et (min-width akışı)
✅ clamp() ile fluid typography kontrolü
✅ Breakpoint değeri px sabit olmalı; @media içinde var() YASAK (kök AGENTS §10)
```

Rapor §7 çıktısı: `| Breakpoint | Durum (✅/❌) | Not |` — eksik breakpoint LOW/MEDIUM,
tier kırılımı (yanlış sütun düzeni) HIGH sayılır.
