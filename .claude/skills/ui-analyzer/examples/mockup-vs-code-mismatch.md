---
title: "ui-analyzer Example — Mockup ↔ Uygulama Sapması Yürüyüşü"
type: example
version: 4.0.0
updated: 2026-10-21
---

# Örnek: Mockup ↔ Uygulama Sapması Yürüyüşü (dar yol)

> **Kurgusal ama gerçekçi örnek (eğitim amaçlı).** Dosya/satır/ölçü değerleri bu örnek
> için üretilmiştir; gerçek denetimde her satır okunan kaynakla kanıtlanır.

**Senaryo:** Tek bir bileşen (footer player) üzerinde **sapma bulma yürüyüşü** —
tam rapor formatı değil, sınıflandırma odaklı. Tam döngü için
`mockup-vs-live-analysis.md`, puanlı tam rapor için `home-panel-audit.md`.

---

## ADIM 1 — Girdi türü belirle

```text
→ PNG Mockup: .ai/.png/home-1024/ (indeks: 01-mockup-index.md)
→ Mevcut kod: CSS katmanları (assets.coremusic.net/Css)
→ Canlı DOM: gerekmedi (kod katmanı yeterli — işaret: FACT·kod)
```

## ADIM 2 — Referansları oku (bağlayıcı sıra)

```text
01-mockup-index → 02-component-inventory (C07 footer player) → design-tokens-master → hedef kod
Çelişki sırası: PNG > ASCII > Inventory > Tokens > Implementation Plan
DUR kontrolü: PNG okunabiliyor → devam
```

## ADIM 3-4 — Ölçüm + kural denetimi

| Ölçüm | Mockup (kaynak) | Gerçek (kanıt) | Sapma | İşaret | Severity |
|-------|-----------------|----------------|-------|--------|----------|
| Footer yükseklik | ASCII: 90px (y:510-600 @1024×600) | CSS: `height: 90px` (footer.css:31) | 0 | FACT | OK |
| Primary renk (buton) | Token `--cm-accent-primary` | `a-button.css:42` → ham `#ff69b4` | token değil | FACT | **HIGH** |
| Gövde fontu | PNG+inventory: Inter 400/16/1.5 | `base.css:14` → `font: 400 16px/1.5 Inter` | 0 | FACT | OK |
| BEM sınıfı | inventory C07: `cm-footer__progress` | `player.css:9` → `.cm-progress-bar` | yanlış ad | FACT | **MEDIUM** |
| Grid (kartlar) | ASCII: 12 sütun @1024 | `repeat(12,1fr)` gutter 32px | 0 | FACT | OK |
| Breakpoint gövdesi | kural: @media içinde var() YASAK | `902.css:12` → `@media(min-width:902px){--gap:var(--cm-space-3)}` | kural ihlali | FACT | **HIGH** |
| Hover rengi (buton) | inventory'de belirtilmemiş | — | bilinmiyor | UNKNOWN | ⚠️ |

**Kural denetimi girdisi:** `references/token-itcss-audit.md` §2 kriter 3, §4 kriter 3.

```text
[SAPMA-01] a-button.css:42 — ham değer: #ff69b4; beklenen: var(--cm-accent-primary)
  Severity: HIGH · Kaynak: kök AGENTS.md §10
[SAPMA-02] 902.css:12 — @media içinde var() okuma
  Severity: HIGH · Kaynak: kök AGENTS.md §10
```

## ADIM 5 — Sınıflandırma

```text
FACT · HIGH: 2 (sapma-01 token, sapma-02 @media var())  → −30
FACT · MEDIUM: 1 (BEM adı C07)                          → −7
FACT · LOW: 0                                           → 0
UNKNOWN: 1 (hover rengi — inventory'de yok)             → −5 (⚠️)
Puanlama: 100 − (30 + 7 + 0 + 5) = 58 → Sınıf D
```

## ADIM 6 — Rapor kapanışı (dar)

```markdown
## 8. Sorunlar ve Öneriler
### Tespit Edilen Sorunlar
1. [HIGH·FACT] Ham hex token yerine (a-button.css:42) — sapma-01
2. [HIGH·FACT] @media içinde var() (902.css:12) — sapma-02
3. [MEDIUM·FACT] BEM adı inventory'den farklı (cm-progress-bar → cm-footer__progress)
4. [⚠️ UNKNOWN] Hover rengi inventory'de tanımlı değil — VERIFICATION REQUIRED
### Öneriler
1. Token'a geçiş → devir: ui-code-generator (TAŞIMA YOK, satır edit)
2. @media kuralı → düzeltme onayı gerektirir (kök AGENTS §10)
3. BEM adı → envantere uydurma (ad değişikliği = DOM/wiki etkisi → onay)
4. Hover rengi → inventory'ye sorulur; uydurulmaz

---
Özet: 2 HIGH · 1 MEDIUM · 0 LOW · 1 ⚠️ VERIFICATION REQUIRED
Sonraki adım: ui-code-generator (+ onay gerektirenler için kullanıcı onayı)
```

---

## Öğrenilecek dersler

1. **Her satırda 4'lü** (Ölçüm + Kaynak + Kanıt + Sapma) + **işaret** (FACT/INFERENCE/UNKNOWN).
2. **Uyumlu ölçüler (0 sapma) de rapora girer** — "bulunamadı" kanıttır, sessiz geçme yok.
3. **UNKNOWN olan ölçüm "ihlal" yapılmaz** — belirsizlik cezası (−5) ayrı uygulanır.
4. **Token sapması + @media var() = 2 HIGH** → skor 58 (D): onay yok, iş durur.
5. **BEM adı envantere bağlanır** — "gördüğüm gibi" değil, inventory esas.
6. **Analiz kod üretmez** — devir `ui-code-generator` (READ-ONLY hard limit).

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
