---
title: "ui-analyzer Reference — Kaynaklar (Truth Mode)"
type: reference
version: 4.0.0
updated: 2026-10-21
---

# Kaynaklar — Web Research Kayıt Defteri (Truth Mode)

Bu dosya `SKILL.md` §18'den linklenir (orphan=0). **Web research ZORUNLU**dur: WCAG
eşiği, WAI-ARIA rolü, ölçüm yöntemi gibi normatif bir iddia ilk kez yazılacaksa önce
resmi kaynak doğrulanır, sonra bu listeye eklenir.

Kayıt formatı (her madde zorunlu 5 alan):

```text
- Ad: <kaynak başlığı>
- URL: <tam URL>
- Tür: [W3C Recommendation | WAI Note/Understanding | MDN | resmi doküman]
- Erişim: {YYYY-MM-DD}
- Ne doğrulandı: <iddia + doğrulanan değer/ölçü>
```

**Kural:** Doğrulanmayan iddia bu dosyaya eklenmez; raporda `⚠️ VERIFICATION REQUIRED`
olarak işaretlenir (kök `AGENTS.md` §3 Zero-Hallucination).

---

## 1. Doğrulanmış Web Kaynakları (erişim: 2026-10-21)

### S1 — W3C Web Content Accessibility Guidelines (WCAG) 2.2

- Ad: Web Content Accessibility Guidelines (WCAG) 2.2
- URL: https://www.w3.org/TR/WCAG22/
- Tür: W3C Recommendation
- Erişim: 2026-10-21
- Ne doğrulandı: Dokümanın statüsü **W3C Recommendation, 12 December 2024**. Kriter
  envanteri: 1.4.3 Contrast (Minimum) Level AA = **4.5:1** (large text 3:1) ·
  1.4.11 Non-text Contrast Level AA = **3:1** · 1.4.10 Reflow Level AA = **320 CSS px**
  (256 CSS px dikey okuma için) · 2.4.7 Focus Visible = **Level AA** · 2.4.11 Focus Not
  Obscured (Min) = Level AA (2.2 yeni) · 2.5.7 Dragging Movements = Level AA (2.2 yeni) ·
  2.5.8 Target Size (Minimum) = **Level AA** (2.2 yeni) · 3.2.6 Consistent Help = Level A ·
  3.3.7 Redundant Entry = Level A · 3.3.8 Accessible Authentication (Minimum) = Level AA ·
  **4.1.1 Parsing, WCAG 2.2'de silinmiştir.**

### S2 — Understanding SC 1.4.3 Contrast (Minimum)

- Ad: Understanding Success Criterion 1.4.3: Contrast (Minimum)
- URL: https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html
- Tür: WAI Understanding Dokümanı (WCAG 2.2'ye bağlı)
- Erişim: 2026-10-21
- Ne doğrulandı: Normal metin eşiği **4.5:1 — yuvarlanmaz** (4.499 geçersizdir).
  Large text = **≥18pt** veya **≥14pt bold**; 1pt = 1.333px olduğundan yaklaşık
  **24px / 18.5px**. Kontrast oranı formülü **(L1 + 0.05) / (L2 + 0.05)**; göreli
  parlaklık **L = 0.2126R + 0.7152G + 0.0722B** (linearized sRGB). Placeholder metni ve
  tooltip metni de bu kritere tabidir. **Pasif (inactive) bileşenler muhaftır** — ancak
  kullanıcının etkinleştirdiği durum tekrar kritere girer.

### S3 — Understanding SC 2.5.8 Target Size (Minimum)

- Ad: Understanding Success Criterion 2.5.8: Target Size (Minimum)
- URL: https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html
- Tür: WAI Understanding Dokümanı (WCAG 2.2'ye bağlı)
- Erişim: 2026-10-21
- Ne doğrulandı: AA eşiği **24×24 CSS px**. Beş istisna: Spacing (hedefin kendisi
  24px değilse, çevresinde **24px çaplı daire** içinde başka hedef yoksa geçerli) ·
  Equivalent · Inline (metin akışındaki hedef) · User Agent Control · Essential.
  Onaylı teknik **C42**: `min-height` / `min-width` ile 24px garantisi.
  (SC 2.5.5 Target Size Enhanced = **44×44, Level AAA** — AA denetiminde bağlayıcı
  değildir; proje zemini 48px bunun da üzerindedir.)

### S4 — MDN: Window.getComputedStyle()

- Ad: Window.getComputedStyle() — Web APIs | MDN
- URL: https://developer.mozilla.org/en-US/docs/Web/API/Window/getComputedStyle()
- Tür: MDN Web Docs (resmi olmayan, ölçüm tekniği kaynağı)
- Erişim: 2026-10-21
- Ne doğrulandı: `getComputedStyle(el)` **canlı, salt-okunur** bir `CSSStyleProperties`
  döndürür; dönen değer **resolved value**dur (animasyon/`transition` sırasında anlık
  değer, `:visited` durumunda kasıtlı olarak yanlış değer — CSS history leak koruması).
  Spesifikasyon: CSSOM View. **Denetim nesnesi değiştirmez** → READ-ONLY kimliğiyle uyumlu.

---

## 2. Yerel Vault Kaynakları (web değil, disk kanıtı)

| Kaynak | Disk yolu | Ne verir |
|--------|-----------|----------|
| Vault anayasası (Guardrail #11) | `.ai/CLAUDE.md` §7.1 | Referans sırası + DUR prosedürü |
| CSS yazım kuralları | kök `AGENTS.md` §10 | ITCSS katman, token, `@media` kuralları |
| Mockup indeksi + tier matrisi | `.ai/ui-design/01-mockup-index.md` | PNG envanteri + cihaz tier matrisi |
| BEM + ölçüm envanteri | `.ai/ui-design/02-component-inventory.md` | C01-C16 sınıf ve piksel ölçümleri |
| Token tabloları | `.ai/ui-design/tokens/design-tokens-master.md` | `--cm-*` değerleri |
| ASCII layout | `.ai/ui-design/screens/00-ascii-art-index.md` | Piksel bölge koordinatları |
| Erişilebilirlik gap kaydı | `.ai/ui-design/04-accessibility-gaps.md` | 24/44/48px uzlaştırması, mevcut gap'ler |
| ITCSS şablonu (11 katman) | `.ai/.templates/frontend/css-template.md` | Katman sırası, token kuralı, C1-C6 |

> `.ai/` gizli dizindir — glob/grep aramasında `hidden: true` kullanılır (kök `AGENTS.md` §7).

---

## 3. Cross-Skill Kaynaklar (kopyalanmaz, linklenir)

| Kaynak | Yol | Amaç |
|--------|-----|------|
| WCAG 2.2 AA checklist | `../ui-code-generator/references/wcag-2.2-checklist.md` | Derin WCAG denetimi |
| Kod üretim kuralları | `../ui-code-generator/SKILL.md` | Düzeltme kodunun sahibi |
| Onay akışı (§17) | `../elite-uiux-architect/SKILL.md` (varsa) | Görsel üretim öncesi onay gate'i |

**Kopyalama yasağı:** Bu dosyalardan metin kopyalanmaz; linklenir. Sürüm/eşik
değişince önce buradaki web kaynakları yeniden doğrulanır.

---

## 4. Yeniden Doğrulama Tetikleyicileri

| Tetikleyici | Ne doğrulanır | Ne yapılırsa |
|-------------|---------------|--------------|
| WCAG sürümü iddiası ("2.2 = ...") | S1 | Erişim tarihi güncellenir |
| Kontrast/target-size eşiği | S2 / S3 | SKILL.md §8 + `wcag-criteria.md` senkron |
| Ölçüm/raporlama tekniği | S4 | `deep-dive.md` §4 senkron |
| Yeni kriter (Level AA) iddiası | S1 + Understanding sayfası | `wcag-criteria.md`'ye satır eklenir |

Erişim tarihi alanı **her oturumda güncellenir**; tarihi eskimiş kaynak yeni iddia
için kullanılmaz (yeniden fetch edilir).

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
