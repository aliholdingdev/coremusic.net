---
title: "ui-analyzer Example — PNG Mockup ↔ Canlı Sayfa Karşılaştırması"
type: example
category: skills
version: 1.0.0
updated: 2026-10-07
---

# Örnek: PNG Mockup ↔ Canlı Sayfa Karşılaştırması

**Senaryo:** `music.coremusic.net` ana sayfa footer player — mockup ile canlı sayfa
karşılaştırılacak; rapor ADIM 6 formatıyla (§4 + `references/rapor-formati.md`) üretilecek.

---

## 1. Girdi türü belirleme (ADIM 1)

```text
→ PNG Mockup: .ai/.png/home-1024/ (kaynak: 01-mockup-index)
→ Canlı sayfa: music.coremusic.net (Chrome DevTools MCP)
→ Mevcut kod: assets.coremusic.net CSS katmanları
```

---

## 2. Karşılaştırma (protokol §2 satırları — Örnek çıktı)

| Ölçüm | Mockup (kaynak) | Gerçek (kanıt) | Sapma | Severity |
|-------|-----------------|----------------|-------|----------|
| Footer yükseklik | ASCII: 90px (y:510-600 @1024×600) | DOM: `offsetHeight` = 90px | 0 | ✅ |
| Primary renk (buton) | Token `--cm-accent-primary` (inventory) | computed: `#ff69b4` — **CSS'te ham hex** (a-button.css:42) | token DEĞİL | **HIGH** |
| Gövde fontu | PNG+inventory: Inter 400 / 16px / 1.5 | computed: Inter 400 16px/1.5 | 0 | ✅ |
| BEM sınıfı | inventory C07: `cm-footer__progress` | DOM: `cm-progress-bar` | yanlış ad | **MEDIUM** |
| Grid (kartlar) | ASCII: 12 sütun @1024 | CSS Grid: `repeat(12,1fr)` gutter 32px | 0 | ✅ |
| `@media` içinde `var()` | — | `902.css:12` → `@media(min-width:902px){--gap:var(--cm-space-3)}` | kural ihlali | **HIGH** |

**Kanıt notu:** HEX satırı `dosya:satır` ile; footer ölçüsü DOM'dan; mockup değeri
kaynak dosya adıyla — **kanıtsız satır yok**.

---

## 3. Token sapma özeti (`references/token-sapma-kontrolu.md` uygulaması)

```text
[SAPMA-01] a-button.css:42 — ham değer: #ff69b4; beklenen: var(--cm-accent-primary)
  Severity: HIGH · Kaynak: kök AGENTS.md §10
[SAPMA-02] 902.css:12 — @media içinde var() okuma
  Severity: HIGH · Kaynak: kök AGENTS.md §10
```

Tarama çıktısı: ham hex taraması **2 isabet** (01_Abstracts dışı); token tanımı
01_Abstracts'ta mevcut ✓.

---

## 4. Rapordan önce DUR kontrolü

```text
PNG okundu mu? → EVET (home-1024 mevcut)
Referans sırası uygulandı mı? → EVET (PNG > inventory > token)
Görsel okunamadı mı? → HAYIR → DUR koşulu YOK, devam
```

---

## 5. Rapor kapanışı (ADIM 6)

```markdown
## 8. Sorunlar ve Öneriler
### Tespit Edilen Sorunlar
1. [HIGH] Ham hex token yerine (a-button.css:42) — sapma-01
2. [HIGH] @media içinde var() (902.css:12) — sapma-02
3. [MEDIUM] BEM adı inventory'den farklı (cm-progress-bar → cm-footer__progress)

### Öneriler
1. Token'a geçiş düzeltmesi → devredilen: ui-code-generator (TAŞIMA YOK, satır edit)
2. @media kuralı → düzeltme onayı gerektirir (kural: kök AGENTS §10)
3. BEM adı → inventory'e uydurma (ad değişikliği = wiki/DOM etkisi → onay)

---
Özet: 2 HIGH · 1 MEDIUM · 0 LOW · 0 ⚠️ VERIFICATION REQUIRED
Sonraki adım: ui-code-generator (+ taşıma/onay gerektirenler için kullanıcı onayı)
```

---

## Öğrenilecek dersler

1. **Her satırda 4'lü** (Ölçüm + Kaynak + Kanıt + Sapma) — kanıtsız bulgu yok.
2. **Token sapması HIGH** — ham hex/red var, düzeltmesi onaylı (taşıma yasak).
3. **BEM adı inventory'ye bağlanır** — "gördüğüm gibi" değil, envanter esas.
4. **Analiz kod üretmez** — devir `ui-code-generator`'a (bu skill'in HARD LIMITS'i).
