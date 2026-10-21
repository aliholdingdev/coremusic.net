---
title: "ui-analyzer Example — PNG Mockup ↔ Canlı Sayfa Tam Döngü"
type: example
version: 4.0.0
updated: 2026-10-21
---

# Örnek: Ham Giriş → FACT/INFERENCE/UNKNOWN → Puanlı Rapor (TAM DÖNGÜ)

> **Kurgusal ama gerçekçi örnek (eğitim amaçlı).** Dosya/satır/ölçü değerleri bu örnek
> için üretilmiştir; gerçek disk kanıtı değildir. Gerçek denetimde her satır okunan
> dosya + satır veya DOM ölçüsüyle kanıtlanır. `examples/` içindeki bu işaretleme
> şeması N5'in zorunlu tam döngüsüdür.

**Senaryo:** `music.coremusic.net` ana sayfa footer player — mockup ile canlı sayfa
karşılaştırılır; 6 adımlı protokol (`SKILL.md` §5) uygulanır; çıktı `SKILL.md` §12
rapor formatıyla puanlı kapanır.

---

## 0. HAM GİRİŞ (ham çıktı, henüz işlenmemiş)

```text
Kullanıcı isteği: "music ana sayfayı mockup ile karşılaştır, onay öncesi rapor ver"

Girdi #1 (PNG):      .ai/.png/home-1024/  (indeks: .ai/ui-design/01-mockup-index.md)
Girdi #2 (canlı):    music.coremusic.net (Chrome DevTools MCP, read-only)
Girdi #3 (kod):      assets.coremusic.net CSS katmanları
```

---

## 1. ADIM 1 — Girdi türü belirleme

```text
→ PNG Mockup: .ai/.png/home-1024/ (kaynak: 01-mockup-index.md)
→ Canlı sayfa: music.coremusic.net (Chrome DevTools MCP)
→ Mevcut kod: assets.coremusic.net CSS katmanları
Tetik: "mockup karşılaştırma" → mockup protokolü aktif
```

## 2. ADIM 2 — Referansları oku (bağlayıcı sıra)

```text
01-mockup-index → 02-component-inventory (C07 footer player) → design-tokens-master → hedef kod
Çelişki sırası (Guardrail #11): PNG > ASCII > Inventory > Tokens > Implementation Plan
DUR kontrolü: PNG okunabiliyor EVET · 3 referans diskte mevcut EVET → devam
```

## 3. ADIM 3-4 — Ölçüm + kural denetimi (4'lü kanıt)

| Ölçüm | Mockup (kaynak) | Gerçek (kanıt) | Sapma | İşaret |
|-------|-----------------|----------------|-------|--------|
| Footer yükseklik | ASCII: 90px (y:510-600 @1024×600) | DOM: `offsetHeight` = 90px | 0 | **FACT** |
| Primary renk (buton) | Token `--cm-accent-primary` | computed `#ff69b4` — CSS'te ham hex (a-button.css:42) | token değil | **FACT** |
| Gövde fontu | PNG+inventory: Inter 400 / 16px / 1.5 | computed: Inter 400 16px/1.5 | 0 | **FACT** |
| BEM sınıfı | inventory C07: `cm-footer__progress` | DOM: `cm-progress-bar` | yanlış ad | **FACT** |
| Grid (kartlar) | ASCII: 12 sütun @1024 | `repeat(12,1fr)` gutter 32px | 0 | **FACT** |
| `@media` içinde `var()` | kural: YASAK (kök AGENTS §10) | `902.css:12` → `@media(min-width:902px){--gap:var(--cm-space-3)}` | kural ihlali | **FACT** |
| Tooltip kopyası (icon button) | PNG'de okunamadı (ikon üstü metin belirsiz) | — | bilinmiyor | **UNKNOWN** |
| Focus outline rengi | inventory'de yazmıyor | token dosyasında `--cm-focus-ring` var mı kontrol edilmedi | belirsiz | **INFERENCE** (token adı aynı olmalı → doğrulanacak) |

**Kural denetimi girdisi:** `token-itcss-audit.md` §2 kriter 3 ve §4 kriter 3.

```text
[SAPMA-01] a-button.css:42 — ham değer: #ff69b4; beklenen: var(--cm-accent-primary)
  Severity: HIGH · Kaynak: kök AGENTS.md §10
[SAPMA-02] 902.css:12 — @media içinde var() okuma
  Severity: HIGH · Kaynak: kök AGENTS.md §10
```

**İşaret sözlüğü (bu örnekte bağlayıcı):**

| İşaret | Anlamı | Puana etkisi |
|--------|--------|--------------|
| **FACT** | Kanıt var: dosya:satır VEYA DOM ölçüsü VEYA okunmuş PNG/ASCII değeri | Severity uygulanır |
| **INFERENCE** | Kanıttan çıkarım (henüz doğrulanmadı) | Doğrulanana kadar puan DEĞİL; doğrulanırsa FACT'e döner |
| **UNKNOWN** | Bilgi yok / okunamadı / diskte değil | Bulgu sayılmaz; raporda `⚠️ VERIFICATION REQUIRED` satırı (−5) |

## 4. ADIM 5 — Sınıflandırma

```text
FACT · HIGH: 2 (sapma-01 token, sapma-02 @media var())        → −30
FACT · MEDIUM: 1 (BEM adı C07)                                 → −7
FACT · LOW: 0                                                  → 0
INFERENCE: 1 (focus ring token eşlemesi) → doğrulanana kadar 0  → 0
UNKNOWN: 1 (tooltip kopyası) → ⚠️ VERIFICATION REQUIRED         → −5
```

## 5. ADIM 6 — Rapor (SKILL.md §12 formatı)

```markdown
# FOOTER PLAYER — UI Analiz Raporu
- Analiz tarihi: 2026-10-21 · Yöntem: PNG mockup + Canlı sayfa · Referans: inventory C07 + token tablosu
- Yetki: Guardrail #11 referans sırası (PNG > ASCII > Inventory > Tokens)

## 1. Genel Bakış — music.coremusic.net footer player, mockup+kod karşılaştırmalı denetim
## 2. Renk Paleti — Primary token; accent: ham hex (HIGH, sapma-01)
## 3. Tipografi — Inter 400/16/1.5 (inventory ile birebir)
## 4. Grid Yapısı — 1024px: 12 sütun, gutter 32px (uyum)
## 5. Boşluk Hiyerarşisi — token ölçeğine oturuyor (uyum)
## 6. Font Analizi — Inter, 100-900, Latin Ext (TR) (uyum)
## 7. Responsive Breakpoint — @media var() ihlali (902.css:12, HIGH)
## 8. Sorunlar ve Öneriler

### Tespit Edilen Sorunlar
1. [HIGH·FACT] Ham hex token yerine (a-button.css:42) — sapma-01
2. [HIGH·FACT] @media içinde var() (902.css:12) — sapma-02
3. [MEDIUM·FACT] BEM adı inventory'den farklı (cm-progress-bar → cm-footer__progress)
4. [⚠️ UNKNOWN] Tooltip kopyası PNG'de okunamadı — VERIFICATION REQUIRED
### Öneriler
1. Token'a geçiş → devir: ui-code-generator (TAŞIMA YOK, satır edit)
2. @media kuralı → düzeltme onayı gerektirir (kök AGENTS §10)
3. BEM adı → envantere uydurma (ad değişikliği = DOM/wiki etkisi → onay)
4. Tooltip kopyası → PNG yeniden okunur; okunamazsa kullanıcıya sorulur

---
Özet: 2 HIGH · 1 MEDIUM · 0 LOW · 1 ⚠️ VERIFICATION REQUIRED
Puanlama: 100 − (15·2 + 7·1 + 3·0 + 5·1) = 100 − 42 = 58 → Sınıf D
Sonraki adım: ui-code-generator (+ onay gerektirenler için kullanıcı onayı)
```

---

## Öğrenilecek dersler

1. **Üç işaret zorunlu** — FACT/INFERENCE/UNKNOWN her ölçüm satırında belirgin olmalı;
   UNKNOWN puanı düşürür (−5) ama "bulgu" sayılmaz.
2. **INFERENCE erken puanlanmaz** — çıkarım doğrulanınca FACT olur; aksi halde
   halüsinasyon riski (Zero-Hallucination).
3. **Her satırda 4'lü** (Ölçüm + Kaynak + Kanıt + Sapma) — kanıtsız bulgu yok.
4. **Uyumlu ölçüler (0 sapma) de rapora girer** — "bulunamadı" kanıttır.
5. **Analiz kod üretmez** — devir `ui-code-generator` (READ-ONLY hard limit).

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
