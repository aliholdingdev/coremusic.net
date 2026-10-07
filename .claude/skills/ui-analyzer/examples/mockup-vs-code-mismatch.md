---
title: "ui-analyzer Example — Mockup ↔ Uygulama Sapması Yürüyüşü"
type: example
version: 3.0.0
updated: 2026-10-07
---

# Örnek: Mockup ↔ Uygulama Sapması Yürüyüşü

> ⚠️ **Kurgusal ama gerçekçi örnek.** Dosya/satır/ölçü değerleri eğitim amaçlıdır;
> gerçek denetimde her satır okunan kaynakla kanıtlanır.

**Senaryo:** `music.coremusic.net` footer player — PNG mockup ile canlı sayfa/CSS
karşılaştırılır; 6 adımlık protokol (`SKILL.md` §3) + ölçüm protokolü
(`analysis-methodology.md` §3) uygulanır; çıktı `scoring-rubric.md` formatındadır.

---

## ADIM 1 — Girdi türü belirle

```text
→ PNG Mockup: .ai/.png/home-1024/ (indeks: 01-mockup-index.md)
→ Canlı sayfa: music.coremusic.net (Chrome DevTools, read-only)
→ Mevcut kod: CSS katmanları (assets.coremusic.net/Css)
```

## ADIM 2 — Referansları oku (bağlayıcı sıra)

```text
01-mockup-index → 02-component-inventory (C07 footer player) → design-tokens-master → hedef kod
Çelişki sırası: PNG > ASCII > Inventory > Tokens > Implementation Plan
DUR kontrolü: PNG okunabiliyor ✅ → devam
```

## ADIM 3-4 — Ölçüm + kural denetimi

| Ölçüm | Mockup (kaynak) | Gerçek (kanıt) | Sapma | Severity |
|-------|-----------------|----------------|-------|----------|
| Footer yükseklik | ASCII: 90px (y:510-600 @1024×600) | DOM: offsetHeight = 90px | 0 | ✅ |
| Primary renk (buton) | Token `--cm-accent-primary` | computed `#ff69b4` — CSS'te ham hex (a-button.css:42) | token değil | **HIGH** |
| Gövde fontu | PNG+inventory: Inter 400/16/1.5 | computed: Inter 400 16px/1.5 | 0 | ✅ |
| BEM sınıfı | inventory C07: `cm-footer__progress` | DOM: `cm-progress-bar` | yanlış ad | **MEDIUM** |
| Grid (kartlar) | ASCII: 12 sütun @1024 | `repeat(12,1fr)` gutter 32px | 0 | ✅ |
| Breakpoint gövdesi | kural: @media içinde var() YASAK | `902.css:12` → `@media(min-width:902px){--gap:var(--cm-space-3)}` | kural ihlali | **HIGH** |

**Kural denetimi girdisi:** `design-system-criteria.md` §2 kriter 3 ve §4 kriter 3.

```text
[SAPMA-01] a-button.css:42 — ham değer: #ff69b4; beklenen: var(--cm-accent-primary)
  Severity: HIGH · Kaynak: kök AGENTS.md §10
[SAPMA-02] 902.css:12 — @media içinde var() okuma
  Severity: HIGH · Kaynak: kök AGENTS.md §10
```

## ADIM 5 — Sınıflandırma

```text
HIGH: 2 (sapma-01 token, sapma-02 @media var())   → −30
MEDIUM: 1 (BEM adı C07)                            → −7
LOW: 0                                              → 0
⚠️ VERIFICATION REQUIRED: 0 (her satırda kanıt var) → 0
```

## ADIM 6 — Rapor kapanışı

```markdown
## 8. Sorunlar ve Öneriler
### Tespit Edilen Sorunlar
1. [HIGH] Ham hex token yerine (a-button.css:42) — sapma-01
2. [HIGH] @media içinde var() (902.css:12) — sapma-02
3. [MEDIUM] BEM adı inventory'den farklı (cm-progress-bar → cm-footer__progress)

### Öneriler
1. Token'a geçiş → devir: ui-code-generator (TAŞIMA YOK, satır edit)
2. @media kuralı → düzeltme onayı gerektirir (kök AGENTS §10)
3. BEM adı → envantere uydurma (ad değişikliği = DOM/wiki etkisi → onay)

---
Özet: 2 HIGH · 1 MEDIUM · 0 LOW · 0 ⚠️ VERIFICATION REQUIRED
Puanlama: 100 − (15·2 + 7·1 + 3·0 + 5·0) = 63 → Sınıf C
Sonraki adım: ui-code-generator (+ onay gerektirenler için kullanıcı onayı)
```

---

## Öğrenilecek dersler

1. **Her satırda 4'lü** (Ölçüm + Kaynak + Kanıt + Sapma) — kanıtsız bulgu yok.
2. **Uyumlu ölçüler (0 sapma) de rapora girer** — "bulunamadı" kanıttır, sessiz geçme.
3. **Token sapması + @media var() = 2 HIGH** → skor 63 (C): düzeltme turu zorunlu.
4. **BEM adı envantere bağlanır** — "gördüğüm gibi" değil, inventory esas.
5. **Analiz kod üretmez** — devir `ui-code-generator` (READ-ONLY hard limit).