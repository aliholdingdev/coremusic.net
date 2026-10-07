---
title: "ui-analyzer Example — Home Panel Denetimi (puanlı rapor)"
type: example
version: 3.0.0
updated: 2026-10-07
---

# Örnek: Home Panel Denetimi → Puanlı Rapor

> ⚠️ **Kurgusal ama gerçekçi örnek.** Aşağıdaki dosya/satır/ölçü değerleri eğitim
> amaçlı uydurulmuştur; gerçek disk kanıtı değildir. Gerçek denetimde her satır
> okunan dosya + satır veya DOM ölçüsüyle kanıtlanır.

**Senaryo:** `home.coremusic.net` paneli onay öncesi denetlenir — mockup + inventory +
token referansları okunduktan sonra hedef CSS/DOM taranır; çıktı `scoring-rubric.md`
şablonuyla puanlı rapordur.

---

## 1. ADIM 1-2 — Girdi ve referanslar

```text
Girdi: PNG mockup (.ai/.png/shared-1024/) + canlı DOM (Chrome DevTools, read-only)
Sıra uygulandı: 01-mockup-index → 02-component-inventory → tokens/design-tokens-master → hedef kod
DUR kontrolü: PNG okundu ✅ · 3 referans diskte ✅ → devam
```

## 2. ADIM 3-5 — Ölçüm tablosu (4'lü kanıt)

| Ölçüm | Mockup/Referans (kaynak) | Gerçek (kanıt) | Sapma | Severity |
|-------|--------------------------|----------------|-------|----------|
| Kart bileşen adı | Inventory C03: `cm-home__card` | DOM: `.home-card` (home.css:22) | yanlış BEM adı | **MEDIUM** |
| Accent rengi | Token `--cm-accent-primary` (01_Abstracts) | `07_Components/c-home-card.css:58` → ham `#ff69b4` | token sızıntısı | **HIGH** |
| 1024px grid | 45-tier: 1024 tier → 4 sütun kart düzeni | `@media(min-width:1024px){grid-template-columns:repeat(8,1fr)}` (home.css:74) | 8 sütun ≠ 4 | **HIGH** |
| Kart iç boşluğu | Token ölçek: 24px (lg) | computed: 24px | 0 | ✅ |
| Hero altbaşlık | PNG: gerçek kopya | DOM: "Lorem ipsum dolor sit" (index:148) | placeholder kalmış | **LOW** |
| Font | Inventory: Inter 400/16/1.5 | computed: Inter 400 16px/1.5 | 0 | ✅ |
| Footer yükseklik | ASCII: 90px (y:510-600) | DOM offsetHeight: 90px | 0 | ✅ |

**Token taraması (salt-okunur):** ham hex taraması 01_Abstracts dışı **1 isabet**
(c-home-card.css:58) · token tanımı 01_Abstracts'ta mevcut ✅.

```text
[SAPMA-01] c-home-card.css:58 — ham değer: #ff69b4; beklenen: var(--cm-accent-primary)
  Severity: HIGH · Kaynak kural: kök AGENTS.md §10
```

---

## 3. ADIM 6 — Rapor çıktısı (scoring-rubric.md uygulaması)

```markdown
# HOME PANEL — UI Analiz Raporu
- Analiz tarihi: 2026-10-07 · Yöntem: PNG mockup + Canlı sayfa · Referans: inventory C03 + token tablosu
- Yetki: Guardrail #11 referans sırası (PNG > ASCII > Inventory > Tokens)

## 1. Genel Bakış — home.coremusic.net paneli, mockup+kod karşılaştırmalı denetim
## 2. Renk Paleti — Primary/Neutral token'lardan; Accent: ham hex (HIGH, §8.1)
## 3. Tipografi — Inter 400/16/1.5 ✅ (inventory ile birebir)
## 4. Grid Yapısı — 1024px: beklenen 4 sütun / bulunan 8 sütun (HIGH, §8.3)
## 5. Boşluk Hiyerarşisi — kart içi 24px = lg ✅
## 6. Font Analizi — Inter, 100-900, Latin Ext (TR) ✅
## 7. Responsive Breakpoint — 320/768/1024 ✅ · 1440 ❌ eksik (LOW)
## 8. Sorunlar ve Öneriler

### Tespit Edilen Sorunlar
1. [HIGH] Token sızıntısı: c-home-card.css:58 ham #ff69b4 — sapma-01
2. [HIGH] Breakpoint tier ihlali: 1024px'te 8 sütun, mockup 4 sütun (home.css:74)
3. [MEDIUM] BEM ihlali: .home-card → inventory C03 `cm-home__card` (home.css:22)
4. [LOW] Placeholder kopya: hero altbaşlık "Lorem ipsum..." (index:148)

### Öneriler
1. Token'a geçiş (satır edit) → devir: ui-code-generator — TAŞIMA YOK, onay gerekli
2. Grid 4 sütuna çekme → ui-code-generator + onay (tier matrisi bağlayıcı)
3. BEM adı envantere uydurma → onay (ad değişikliği DOM/wiki etkisi taşır)
4. Placeholder → gerçek kopya → içerik görevi, backlog

---
Özet: 2 HIGH · 1 MEDIUM · 1 LOW · 0 ⚠️ VERIFICATION REQUIRED
Puanlama: 100 − (15·2 + 7·1 + 3·1 + 5·0) = 100 − 40 = 60 → Sınıf C
Sonraki adım: ui-code-generator (+ taşıma/onay gerektirenler için kullanıcı onayı)
```

---

## Öğrenilecek dersler

1. **HIGH = −15 ve onay kilidi** — 2 HIGH, skoru 60'a (Sınıf C: "düzeltme turu
   gerekli, otomatik onay yok") indirdi.
2. **Token sızıntısı HIGH** — token mevcutken ham hex kullanmak bağlayıcı §10 ihlali.
3. **Breakpoint HIGH** — 45-tier matrisi bağlayıcı; "gördüğüm gibi değil, matris esas".
4. **BEM adı MEDIUM** — envanter esas; düzeltilmez, onaylanır.
5. **Analiz kod üretmez** — tüm aksiyonlar devir satırında (READ-ONLY kimlik).