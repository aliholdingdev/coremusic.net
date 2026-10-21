---
name: ui-analyzer
description: "Use when auditing existing UI/CSS against CoreMusic design system, mockups, tokens, or WCAG before approving UI work — Tetikleyiciler: 'UI analiz', 'arayüz denetimi', 'tasarım sistemi kontrol', 'mockup karşılaştırma'."
license: MIT
metadata:
  version: 4.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: ui-analysis
  tags: [ui-analysis, design-system, wcag, truth-mode, read-only]
  updated: 2026-10-21
  previous-version: "3.0.0"
---

# ui-analyzer — UI Analiz Motoru (READ-ONLY)

> **Format `claude-skill-v3`:** tek `SKILL.md` (bu dosya) + `references/` (5 dosya) +
> `examples/` (3 örnek). Kural: derinlik references/'a dağıtılır; bu dosya **Katman-2
> orkestrasyon** tutar — akış + bağlayıcı kural + § indeks. Şablon: skill-maker v3.0
> (N1-N10); N3 revizyonu 2026-10-21: SKILL.md 500-2000 satır, dizin toplamı ≥1000.

**Nihai kimlik: salt-okunur (READ-ONLY) UI denetim motoru.** Mevcut UI/CSS'i CoreMusic
tasarım sistemi, mockup'lar, design token'lar ve **WCAG 2.2 AA** ile **onay öncesi**
denetler; bulguları kanıt + severity + puan ile raporlar.

```text
YAPAR   : okur · ölçer · karşılaştırır · sınıflandırır · işaretler (FACT/INFERENCE/UNKNOWN) · puanlar · raporlar
YAPMAZ  : kod üretmez · CSS/JS/HTML değiştirmez · dosya silmez/taşımaz · vault'a yazmaz · git commit atar · hallüsinasyon yapar
```

---

## §1. Tablo — Bölüm İndeksi (orphan denetimi bu tabloyla yapılır)

| § | Bölüm | Ne verir |
|---|-------|----------|
| §2 | Tetikleyiciler | Ne zaman bu skill |
| §3 | Boot + Mockup Gate | Zorunlu okuma sırası, DUR koşulları |
| §4 | Referans Sırası + Girdi Türleri | Guardrail #11, girdi sınıflandırması |
| §5 | 6 Adımlı Analiz Protokolü | Ana akış (Katman-2 orkestrasyon) |
| §6 | Ölçüm Protokolleri Özeti | Font/renk/grid/breakpoint/touch → `references/deep-dive.md` |
| §7 | Kanıt İşaretleri | FACT / INFERENCE / UNKNOWN |
| §8 | WCAG 2.2 AA Eşikleri | Doğrulanmış özet → `references/wcag-criteria.md` |
| §9 | Tasarım Sistemi Denetimi | ITCSS · BEM · `--cm-*` → `references/token-itcss-audit.md` |
| §10 | Severity + İhlal/Öneri | CRITICAL/HIGH/MEDIUM/LOW |
| §11 | Puanlama Rubrik | 100 − cezalar → A/B/C/D |
| §12 | Rapor Formatı | Zorunlu 8 bölüm + sapma tablosu + kapanış |
| §13 | Component State Matrix | 10 durum denetimi |
| §14 | Onay Akışı + Devir | §17 gate + hangi skill'e ne gider |
| §15 | Hard Limits (Yasaklar) | READ-ONLY ihlal listesi |
| §16 | Sık Yapılan Hatalar | Anti-pattern'ler |
| §17 | Referans & Örnek İndeksi | 5 + 3 dosya, orphan=0 |
| §18 | Kaynaklar (Truth Mode) | Web research zorunluluğu → `references/sources.md` |

---

## §2. Tetikleyiciler (Activation)

```text
UI analiz · arayüz denetimi · tasarım sistemi kontrol · mockup karşılaştırma
ui analiz · mockup oku · font bul · renk paleti · sayfa analizi · design analiz
layout analiz · png oku · görsel analiz · screenshot analiz
```

**Aktivasyon karar şeması:**

```text
Görev "mevcut UI/CSS'i denetle/onayla/karşılaştır" mı?
  EVET → bu skill'i yükle (Skill Usage Mandate §2: ilk işlem skill yüklemek)
  "düzelt/beni de / kod yaz" mı?      → ui-code-generator (analiz SONRASI devir)
  "erişilebilirliği derinlemesine denetle" mi → accessibility + ui-code-generator/wcag checklist
  "canlı sayfayı test et" mi           → browser-testing-with-devtools (ölçüm aracı, read-only)
```

**Onaysız üretim yasak (global §8 zinciri):** Analiz raporu üretilir → kullanıcı/kural
onayı → ancak onaydan sonra kod üretimi (`ui-code-generator`) başlar. Bu skill
onay **kapısıdır**, üretici değil.

---

## §3. Boot + Mockup Gate (Zorunlu Okumalar)

**Sıra (bağlayıcı, atlanamaz):**

```text
1. Bu SKILL.md (§5 protokolü + §17 indeks)
2. .ai/ui-design/01-mockup-index.md          → mockup envanteri + tier matrisi
3. .ai/.png/ altındaki İLGİLİ PNG             → GÖRSEL KAYNAK (PNG her şeyi ezer)
4. .ai/ui-design/screens/00-ascii-art-index.md → piksel layout (gerekirse)
5. .ai/ui-design/02-component-inventory.md    → C01-C16 BEM + ölçüler
6. .ai/ui-design/tokens/design-tokens-master.md → --cm-* token'ları
7. Hedef kod / canlı sayfa                    → YALNIZ 2-6 OKUNDUKTAN SONRA
```

> `.ai/` gizli dizindir — glob/grep'te `hidden: true` (kök `AGENTS.md` §7).
> "Başta tüm vault'u oku" yasak; yalnız görevin gerektirdiği dosyalar.

**Mockup Gate (kök `AGENTS.md` §13):** CSS/HTML/JS/layout/bileşen görevinde ilgili
`.ai/ui-design/**` görseli okunmadan analiz başlamaz.

**DUR koşulları (hiçbiri atlanamaz):**

| # | Koşul | Aksiyon |
|---|-------|---------|
| 1 | PNG okunamıyor / dosya yok / görsel bozuk | DUR + Guardrail #11 ihlal prosedürü (revert varsa → `log.md` CRITICAL → Vault Steward bildirimi) |
| 2 | Referans dosyalardan hiçbiri diskte yoksa | `⚠️ VERIFICATION REQUIRED` + DUR |
| 3 | Tier/matris hedefi belirsizse | Tier **uydurulmaz**; kullanıcıya sorulur |
| 4 | WCAG eşik/emniyetli iddia doğrulanamıyorsa | `⚠️ VERIFICATION REQUIRED`; `sources.md`'e eklenmeden yazılmaz |

---

## §4. Referans Sırası + Girdi Türleri

**Guardrail #11 — çelişkide bağlayıcı sıra (`.ai/CLAUDE.md` §7.1):**

```text
PNG (ilk) > ASCII art > Component Inventory > Tokens > Implementation Plan
```

1. **PNG** — `.ai/.png/` onaylı görsel. "Ben gördüm" iddiası PNG'siz yazılmaz.
2. **ASCII art** — piksel layout: 1024×600 → Header 60px (y:0-60) · İçerik 450px
   (y:60-510) · Footer 90px (y:510-600).
3. **Component Inventory** — C01-C16 BEM + ölçüler.
4. **Tokens** — design token tabloları.
5. **Implementation Plan** — en düşük öncelik.

**Girdi türleri → okuma yolu:**

| Girdi | Yol | Not |
|-------|-----|-----|
| PNG mockup | `.ai/.png/` + `01-mockup-index.md` | görseli oku, token'la doğrula |
| ASCII layout | `screens/00-ascii-art-index.md` | piksel bölge koordinatları |
| Canlı sayfa | Chrome DevTools MCP (**read-only**) | `getComputedStyle` = canlı + salt-okunur (`sources.md` S4) |
| Kod | CSS/JS dosyaları | §9 denetimleri; tarama komutları salt-okunur |
| Font dosyası | .ttf/.otf/.woff/.woff2 | §6.1 |
| Belge/spec | `.ai/ui-design/**` md | spec ≠ görsel ise **PNG kazanır** |

---

## §5. 6 Adımlı Analiz Protokolü (ana akış)

Her adımın **çıkışı bir sonrakinin girdisidir**; adım atlanmaz, DUR koşulu çıkarsa durulur.

### ADIM 1 — Girdi türünü belirle

- Yukarıdaki girdi tablosundan satır(lar) seçilir; hangi yöntem kullanıldığı rapor
  başlığına yazılır: `[PNG mockup | Canlı sayfa | Kod | Karışık]`.
- Tetik "mockup karşılaştırma" ise mockup protokolü (`deep-dive.md` §5) zorunlu.

### ADIM 2 — Referansları oku (§3 sıra + §4 hiyerarşi)

- `01-mockup-index` → `02-component-inventory` → `design-tokens-master` → (gerekirse ASCII) → hedef.
- DUR kontrolü yapılır ve rapora tek satır olarak yazılır:
  `DUR kontrolü: PNG okundu EVET · 3 referans diskte EVET → devam`.
- Eksik referans → §3/DUR-2.

### ADIM 3 — Ölç (ölçüm protokolü §6)

- Her ölçüm için **4'lü kanıt** üretilir: **Ölçüm + Kaynak (beklenen) + Kanıt (gerçek) + Sapma**.
- Ölçüm kaynağı: PNG/ASCII/inventory/token (beklenen) · kanıt: `dosya:satır` VEYA DOM
  ölçüsü VEYA okunmuş piksel değeri.
- **Kanıtsız satır rapora girmez**; giriyorsa `UNKNOWN` işaretlenir (§7).
- Uyumlu ölçüler (sapma = 0) de rapora girer: "bulunamadı" kanıttır, sessiz geçme yok.
- Toleranslar: ölçü ±2px LOW / ±2-4px MEDIUM / >±4px HIGH · font ağırlığı birebir ·
  HEX birebir (token) · sınıf adı birebir (detay: `deep-dive.md` §3).

### ADIM 4 — Kural denetimi (§9 + §8)

- ITCSS 11 katman · token `--cm-*` · `@media` içi `var()` yasağı · BEM biçimi ·
  inline style yasağı → `token-itcss-audit.md` kriter tabloları.
- WCAG ön-hesabı: kontrast, touch target, focus outline → `wcag-criteria.md` eşikleri.
- Sonuç: her kural ihlali `[SAPMA-NN]` bloğuyla kanıtlanır:
  `[SAPMA-NN] {dosya}:{satır} — ham değer: {x}; beklenen: {y} · Severity: {…} · Kaynak: kök AGENTS.md §10`.

### ADIM 5 — Sınıflandır (§7 işaret + §10 severity)

- Her satıra **işaret** (FACT/INFERENCE/UNKNOWN) ve varsa **severity** (§10) verilir.
- Sayımlar: `n HIGH · n MEDIUM · n LOW · n ⚠️` — puanlama girdisi.
- İhlal ile öneri ayrılır (§10.2); **öneri puanı düşürmez**.

### ADIM 6 — Raporla (§12 formatı + §11 puanlama)

- §12'deki **8 bölüm sırası değişmez**; başlık bloğu + sapma tablosu + kapanış eklenir.
- Kapanışta puan, sınıf ve **devir satırı** (`Sonraki adım: {skill}`) zorunlu (§14).

**Protokol şematı:**

```text
[1 girdi] → [2 referans+DUR] → [3 ölçüm 4'lü] → [4 kural denetimi]
   → [5 işaret+severity] → [6 rapor+puan+devir] → ONAY KAPISI (§14) → ui-code-generator
```

---

## §6. Ölçüm Protokolleri Özeti (derinlik: `references/deep-dive.md`)

### §6.1 Font Analizi (rapor §6)

```text
Font ailesi · weight aralığı (100-900) + style · glyph kapsamı (Latin Ext = TR karakter)
· OpenType (liga, calt, tabular-nums) · web formatı (WOFF2 > WOFF > TTF/OTF)
· dosya boyutu · variable font axis (opsiyonel) · Google Fonts doğrulaması (yoksa UNKNOWN)
```

Canlı: `getComputedStyle(el).fontFamily|fontSize|fontWeight|LineHeight` — resolved
value; okunamayan `UNKNOWN` (`sources.md` S4).

### §6.2 Renk Paleti + Kontrast Ön-Hesabı (rapor §2)

```text
Ana renkler · nötrler · durum renkleri · her renk için KAYNAK (token | ham hex | PNG-only)
· kontrast ön-hesabı · renk uyumu · dark mode / forced colors kontrolü
```

**Kontrast formülü (W3C — `sources.md` S2):**

```text
oran = (L1 + 0.05) / (L2 + 0.05)      L = 0.2126R + 0.7152G + 0.0722B (linear sRGB)
Normal metin ≥ 4.5:1 (yuvarlanmaz, 4.499 geçersiz)
Large text ≥ 3:1  (≥18pt veya ≥14pt bold ≈ 24px / 18.5px)
UI bileşeni/grafik ≥ 3:1 (SC 1.4.11)
```

Ön-hesap **yalnız bulgu üretir**; tam denetim devri: `accessibility` +
`ui-code-generator/references/wcag-2.2-checklist.md`.

### §6.3 Grid & Boşluk (rapor §4-5)

- Grid: sütun/gutter/margin, ASCII ±2px; örnek ölçek 320→4 · 768→8 · 1024→12 sütun.
- Boşluk token skalası: xs 4 · sm 8 · md 16 · lg 24 · xl 32 · 2xl 48 · 3xl 64 px.
  Skalaya oturmayan değer (ör. 20px) = token sapması adayı.

### §6.4 Responsive Breakpoint (rapor §7)

- Media query listesi çıkar → tier matrisi ile karşılaştır → eksik tier işaretle.
- Mobile-First `min-width` akışı · `clamp()` fluid typography.
- Breakpoint değeri **px sabit**; `@media` içinde `var()` **YASAK**.

### §6.5 Dokunma Hedefi / Touch (rapor §8)

- İki eşik **ayrı ayrı** raporlanır (ikisi de doğrulanmış — `wcag-criteria.md` §2):
  - **WCAG 2.5.8 (AA): 24×24 CSS px** + 5 istisna; spacing testi = 24px çaplı daire.
  - **Proje zemini: `--touch-min: 48px`** (css-template C2) — WCAG'dan katıdır;
    24px'e düşürme **YASAK**; bu ihlal WCAG ihlali değil, tasarım sistemi ihlali sayılır.
- Ölçüm: `getBoundingClientRect()` (salt-okunur).

---

## §7. Kanıt İşaretleri (FACT / INFERENCE / UNKNOWN)

Her ölçüm/bulgu satırına **bir işaret** zorunlu:

| İşaret | Tanım | Kanıt biçimi | Puana etkisi |
|--------|-------|--------------|--------------|
| **FACT** | Doğrudan gözlenen/ölçülen | `dosya:satır` VEYA DOM ölçüsü VEYA okunmuş PNG/ASCII değeri | Severity uygulanır |
| **INFERENCE** | Kanıttan çıkarım; henüz doğrulanmadı | çıkarımın dayanağı yazılır | Doğrulanana kadar **puan DEĞİL**; doğrulanınca FACT olur |
| **UNKNOWN** | Bilgi yok / okunamadı / diskte değil | — | Bulgu sayılmaz; raporda `⚠️ VERIFICATION REQUIRED` satırı = **−5** |

**Zorunlu 4'lü (FACT satırı için):** `Ölçüm + Beklenen (kaynak) + Gerçek (kanıt) + Sapma`.

**Kurallar:**

1. Kanıtsız iddia FACT yapılmaz — UNKNOWN işaretlenir.
2. INFERENCE'i FACT'e çevirmek için ölçüm/okuma gerekir; erken FACT = halüsinasyon.
3. UNKNOWN, "ihlal" olarak yazılmaz; sadece belirsizlik cezası (−5) alır.
4. İşaret, rapor tablolarında ve §8 "Sorunlar" listesinde görünür kalır
   (örn. `[HIGH·FACT]`, `[⚠️ UNKNOWN]`).

---

## §8. WCAG 2.2 AA Eşikleri (Doğrulanmış Özet)

> Tam matris + kriter bazlı notlar: **`references/wcag-criteria.md`** · doğrulama
> kayıtları (URL + erişim 2026-10-21): **`references/sources.md` S1-S3**.
> WCAG 2.2 = **W3C Recommendation, 12 December 2024** (S1).

| Alan | Level | Eşik | Kaynak |
|------|-------|------|--------|
| Metin kontrastı (1.4.3) | AA | **4.5:1** normal · **3:1** large (≥18pt/≥14pt bold ≈ 24px/18.5px) | S1, S2 |
| Non-text contrast (1.4.11) | AA | **3:1** UI bileşeni/grafik | S1 |
| Reflow (1.4.10) | AA | **320 CSS px** (256px dikey okuma) | S1 |
| Focus Visible (2.4.7) | **AA** | Klavye odak göstergesi görünür · proje: outline 3px token'lı | S1 |
| Focus Not Obscured (2.4.11) | **AA (2.2 yeni)** | Odak tamamen kapatılmamalı | S1 |
| Dragging (2.5.7) | AA (2.2 yeni) | Sürükleme alternatifi zorunlu | S1 |
| Target Size (2.5.8) | **AA (2.2 yeni)** | **24×24 CSS px** + 5 istisna | S1, S3 |
| Target Size Enhanced (2.5.5) | AAA | 44×44 — **AA'da bağlayıcı değil** | S1 |
| 4.1.1 Parsing | — | **WCAG 2.2'de silindi** — listelenmez | S1 |

**Analizde WCAG kullanımı (ön-hesap + sınıflandırma):**

- Eşik açıkça ihlal + ölçüm kanıtı → **HIGH** → devir `accessibility` + wcag checklist.
- Eşik adayı ama ölçüm tam değil → `⚠️ VERIFICATION REQUIRED` (−5); varsayım yazılmaz.
- Proje zemini (48px / outline 3px) ihlali, WCAG AA uygun → **MEDIUM** (ayrı satır).
- AAA kaçırıldı → **LOW** (bilgi; bağlayıcı değil).
- Yeni kriter/sürüm iddiası → önce `sources.md` §1'e kaynak ekle (Truth Mode).

---

## §9. Tasarım Sistemi Denetimi (ITCSS · BEM · Token)

> Kriter tabloları + tarama komutları: **`references/token-itcss-audit.md`**.
> Kaynak: kök `AGENTS.md` §10 + `.ai/.templates/frontend/css-template.md` (v4.0.0).

**ITCSS = 11 katman (bağlayıcı sıra):**

```text
01_Abstracts → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities
→ 07_Vendors → 08_Devices → 09_ViewModes → 10_Helpers → 11_OAuth (+ auth-bundled.css ayrı root)
```

> "9 katman" iddiası vault'ta `⚠️ VERIFICATION REQUIRED`; sayı yalnız şablondan okunur.

**Bağlayıcı 5 kural:**

1. Sabit değer **yalnız** `01_Abstracts` içinde `--cm-*` olarak tanımlanır.
2. `02_Base`…`11_OAuth` dosyalarına ham hex/px **yazılmaz**; her değer `var(--cm-*)`.
3. **`@media` içinde `var()` okuma YASAK** — kırılım px sabit, yalnız gövde `var()`.
4. **Taşıma/silme YOK** — yanlış yerleşim yalnız **raporlanır** (onay + ADR).
5. Inline style **YASAK**; sınıf adları inventory C01-C16 ile birebir.

**Tarama (salt-okunur):** ham hex (01_Abstracts dışı) · ham px · `--cm-` tanımı ·
`@media` içi `var(` · inline style — komutlar `token-itcss-audit.md` §5'te.
CSS dizini diskte yoksa **DUR + UNKNOWN** (yol uydurulmaz).

---

## §10. Severity + İhlal/Öneri Ayrımı

### §10.1 Severity merdiveni

| Severity | Kriter | Puan | Aksiyon |
|----------|--------|------|---------|
| **CRITICAL** | DUR koşulu ihlali: mockup okunamadan kod/analiz üretilmesi, Guardrail #11 ihlali, referansların tamamı yok, salt-okunur sınır ihlali | **Puanlama yapılmaz** — rapor durdurulur | DUR + bildir (log/üst karar); iş durur |
| **HIGH** | Layout/ölçü kırıldı (pixel reference ihlali) · token ihlali (ham hex/px, `@media var()`) · WCAG AA eşiği ölçülmüş ihlal · inline style · Guardrail/kural ihlali | **−15** | Düzeltme planı zorunlu → `ui-code-generator` + onay |
| **MEDIUM** | Tolerans dışı ama işlevsel (±2-4px, yanlış BEM adı, konvansiyon dışı token, eksik state, proje zemini 48px ihlali) | **−7** | Sprint planına |
| **LOW** | Kozmetik / tolerans içi (yer tutucu metin, eksik breakpoint, AAA kaçırımı) | **−3** | Backlog |
| **⚠️ VERIFICATION REQUIRED** | Belirsizlik (UNKNOWN) — ayrı satır, "bulgu" sayılmaz | **−5** | Ölçüm/okuma tamamlanır |

### §10.2 İhlal (violation) vs Öneri (suggestion)

| | İHLAL | ÖNERİ |
|---|-------|-------|
| Tanım | Bağlayıcı kural/şablon/mockup ölçüsü çiğnendi | Bağlayıcı olmayan iyileştirme |
| Kaynak | kök AGENTS §10 · Guardrail #11 · şablon · token/ASCII ölçüsü · doğrulanmış WCAG AA | modern CSS önerileri, perf ipuçları |
| Rapor yeri | §12 "Tespit Edilen Sorunlar" | §12 "Öneriler" |
| Puan | **Düşürür** | **Düşürmez** |
| Aksiyon | `ui-code-generator` + onay | İstek bağlı, backlog |

---

## §11. Puanlama Rubrik

```text
Başlangıç: 100
  her HIGH   → −15
  her MEDIUM → −7
  her LOW    → −3
  her ⚠️ VERIFICATION REQUIRED → −5   (ayrı satır; belirsizlik cezası)
Skor = max(0, 100 − Σ cezalar)
```

| Skor | Sınıf | Anlamı |
|------|-------|--------|
| 90–100 | **A** | Onaya hazır — yalnız LOW/backlog |
| 75–89 | **B** | Küçük düzeltmelerle onaylanabilir |
| 60–74 | **C** | Düzeltme turu gerekli; otomatik onay yok |
| 0–59 | **D** | Onaylanmaz; HIGH'lar kapanana kadar UI işi durur |

**Kurallar:**

1. CRITICAL varsa puanlama **yapılmaz** (rapor durur, §10.1).
2. Kanıtsız satır puana girmez; `⚠️` cezası ayrı uygulanır.
3. Öneriler puanı **düşürmez** — yalnız İHLAL'ler düşürür.
4. Sayımlar §5/ADIM-5'te; formül §12 kapanışında görünür yazılır.

---

## §12. Rapor Formatı (ADIM 6 çıktısı — sıra değişmez)

### §12.1 Zorunlu başlık bloğu

```markdown
# {SAYFA/BİLEŞEN ADI} — UI Analiz Raporu
- Analiz tarihi: {YYYY-MM-DD}
- Analiz yöntemi: [PNG mockup | Canlı sayfa (Chrome DevTools) | Kod (CSS/JS) | Karışık]
- Referans kaynağı: [PNG yolu / inventory / token dosyası]
- Yetki: Guardrail #11 referans sırası (PNG > ASCII > Inventory > Tokens)
```

### §12.2 Zorunlu 8 bölüm (sıra değişmez)

1. **Genel Bakış** — ne analiz edildi, hangi yöntemle (+ DUR kontrolü satırı)
2. **Renk Paleti** — `| Renk | HEX | RGB | Kullanım | Kaynak (token var mı?) |`
3. **Tipografi** — `| Font | Weight | Boyut | Satır Yüksekliği | Kullanım |`
4. **Grid Yapısı** — `| Breakpoint | Sütun | Gutter | Margin | Düzen |`
5. **Boşluk Hiyerarşisi** — `| Seviye | Değer | Kullanım |`
6. **Font Analizi** — `| Özellik | Değer | Not |`
7. **Responsive Breakpoint Analizi** — `| Breakpoint | Durum | Not |`
8. **Sorunlar ve Öneriler** — (a) Tespit Edilen Sorunlar (b) Öneriler

### §12.3 Sapma tablosu (mockup karşılaştırması varsa zorunlu)

```markdown
| Ölçüm | Mockup (kaynak+değer) | Gerçek (kanıt) | Sapma | İşaret | Severity |
|-------|------------------------|----------------|-------|--------|----------|
| Footer yüksekliği | ASCII: 90px (y:510-600) | DOM: 92px | +2px | FACT | LOW (±2px tolerans içi) |
| Primary renk | token: --cm-accent-primary | computed: ham #ff69b4 | token değil | FACT | HIGH |
```

### §12.4 Sorun satırı biçimi + sapma bloğu

```markdown
1. [HIGH·FACT] {bulgu} — {kanıt dosya:satır / piksel} — sapma-NN
2. [⚠️ UNKNOWN] {belirsizlik} — VERIFICATION REQUIRED
```

```text
[SAPMA-NN] {dosya}:{satır} — ham değer: {değer}; beklenen: var(--cm-...)
  Severity: {…} · Kaynak kural: kök AGENTS.md §10
```

### §12.5 Zorunlu kapanış (puanlı + devirli)

```markdown
---
Özet: {n} HIGH · {n} MEDIUM · {n} LOW · {n} ⚠️ VERIFICATION REQUIRED
Puanlama: 100 − (15·HIGH + 7·MEDIUM + 3·LOW + 5·⚠️) = {skor} → Sınıf {A/B/C/D}
Sonraki adım: {skill/devir}
```

---

## §13. Component State Matrix Denetimi

Bileşen başına **10 durum** ayrı ayrı denetlenir; durum yoksa `UNKNOWN` (durum yok
sayılmaz, sorulur):

```text
DEFAULT · HOVER · ACTIVE · FOCUSED · DISABLED · ERROR · LOADING · EMPTY · SELECTED · OPEN
```

| Soru | Kaynak | Bulgu |
|------|--------|-------|
| Bu durum PNG/inventory'de tanımlı mı? | PNG + inventory | Yoksa MEDIUM (tutarlılık) |
| CSS'te tanımı var mı? | katman dosyası (salt-okunur) | Yoksa MEDIUM |
| `:focus-visible` outline görünür mü (3px token)? | CSS + DOM | Yoksa/görünmezse **HIGH** (SC 2.4.7 adayı) |
| DISABLED erişilebilir renk kontrastı? | hesap | AA ihlal adayı → §8 |
| ERROR durumu rengi token mı? | token tablosu | Ham hex → HIGH (§9) |

Rapor çıktısı: `| Bileşen | Durum | Tanımlı (PNG) | CSS | Not |` + §12.8'e eksikler.

---

## §14. Onay Akışı + Devir (§17 Gate)

```text
ANALİZ (bu skill) → RAPOR + PUAN → ONAY KAPISI
   ├─ A/B sınıfı + HIGH yok   → kullanıcı/kural onayı → ui-code-generator (düzeltme/üretim)
   ├─ C/D sınıfı veya HIGH    → iş DURUR; düzeltme planı + onay zorunlu
   └─ CRITICAL                → DUR + bildir (§10.1); rapor bile üretilmez
```

**Onay gate'i (görsel üretim öncesi):** UI'ın görsel/üretim tarafı
`elite-uiux-architect` §17 onay gate'inden geçmeden kod katmanına (HTML renderer /
`ui-code-generator`) geçilmez. Bu skill'in raporu onay gate'inin **girdisidir**.

**Devir tablosu (nereden ne gelir):**

| İş | Giden skill/kaynak |
|----|--------------------|
| Düzeltme kodu / yeni bileşen üretimi | `ui-code-generator` (satır edit; taşıma/silme **onay + ADR**) |
| Derin WCAG denetimi | `accessibility` + `ui-code-generator/references/wcag-2.2-checklist.md` |
| Canlı DOM/consol ölçümü | `browser-testing-with-devtools` (read-only) |
| Görsel/onay gate'i | `elite-uiux-architect` §17 |
| Görsel üretim (prompt) | `visual-render-studio` / `image-analysis-engineer` (analiz değil, üretim) |
| Vault/registry güncelleme | `vault-updater` / MO — bu skill **`.ai/`'ye yazmaz** |

**Devir satırı zorunlu:** her raporun kapanışında `Sonraki adım: {skill}` yazar;
öneri her zaman hangi skill'e gideceğini söyler ve **eldeki token'lardan** türetilir
(yeni tasarım uydurmaz).

---

## §15. Hard Limits (Yasaklar)

| # | Yasak | İhlalde |
|---|-------|---------|
| 1 | **Kod üretme/yazma** (CSS/JS/HTML/MD proje kaynak kodu) — devir: `ui-code-generator` | READ-ONLY kimlik ihlali → geri al + bildir |
| 2 | Dosya **silme / taşıma / ad değiştirme** (CSS dahil; yanlış yerleşim yalnız raporlanır) | Onay + ADR yoksa yasak eylem |
| 3 | **`.ai/` vault'a yazma** (salt oku; `hidden: true`) | SSOT kirlenmesi |
| 4 | **git commit / add** — commit orkestratöre ait | Yetkisiz commit |
| 5 | **`CLAUDE.md` sidecar** oluşturma | Dokunulmazlık ihlali |
| 6 | Klasör adı / frontmatter `name: ui-analyzer` değiştirme · description tetikleyicilerini kaldırma | N1 ihlali |
| 7 | **Hallüsinasyon**: doğrulanamayan WCAG sürümü/eşik, token, dosya, satır, Figma API değeri uydurma | `⚠️ VERIFICATION REQUIRED` + −5 |
| 8 | `innerHTML` / `var` / hash routing / emoji ile mockup uydurma | yasak (kök kurallar) |
| 9 | Kanıtsız satırı FACT olarak raporlamak | Zero-Hallucination ihlali |
| 10 | WCAG eşiklerini hafızadan, `sources.md` doğrulaması olmadan değiştirmek | N8/Truth Mode ihlali |

---

## §16. Sık Yapılan Hatalar (Anti-Pattern)

| # | Hatalı | Doğru |
|---|--------|-------|
| 1 | "Bence burada 4px fazla" (kanıtsız) | 4'lü: Ölçüm + Beklenen + Kanıt + Sapma; yoksa UNKNOWN |
| 2 | PNG okumadan kod incelemek | §3 Mockup Gate; PNG okunamıyorsa **DUR** |
| 3 | Ham hex bulup "hemen düzeltmek" | Raporla → devir + onay; taşıma/silme yasak |
| 4 | `@media` içindeki `var()`'ı normal görmek | HIGH kural ihlali (kök AGENTS §10) |
| 5 | Inventory'deki adı "gördüğüm gibi" sanmak | Çelişkide sıra: PNG > ASCII > Inventory > Tokens |
| 6 | Öneriyi de puana katmak | Öneri puanı düşürmez (§10.2) |
| 7 | UNKNOWN'ı ihlal gibi yazmak | Belirsizlik = −5 + soru/devir; ihlal sayılmaz |
| 8 | WCAG eşiklerini hafızadan yazmak | `sources.md` + `wcag-criteria.md` (Truth Mode) |
| 9 | Touch için tek eşik söylemek | 24px (WCAG AA) ve 48px (proje) **ayrı satır** |
| 10 | Raporu devirsiz kapatmak | `Sonraki adım: {skill}` zorunlu |

---

## §17. Referans & Örnek İndeksi (orphan = 0)

**`references/` (5 dosya — her biri bu SKILL.md'den linklidir):**

| Dosya | Bağlandığı § | İçerik |
|-------|--------------|--------|
| `references/sources.md` | §18 | Truth Mode kaynak defteri: 4 web kaynağı (URL + erişim 2026-10-21 + ne doğrulandı) + yerel vault kaynakları + yeniden doğrulama tetikleyicileri |
| `references/deep-dive.md` | §4, §5, §6 | Analiz sırası · mockup↔kod ölçüm protokolü (7 satır) · font/renk/grid/breakpoint/state ölçüm detayı · DUR koşulları · salt-okunur sınır |
| `references/wcag-criteria.md` | §8 | Doğrulanmış WCAG 2.2 AA kriter matrisi (kontrast, touch, focus, reflow, 2.2 yeni/çıkan kriterler) + severity eşlemesi + kanıt formatı |
| `references/token-itcss-audit.md` | §9 | İhlal/öneri ayrımı · ITCSS 11 katman kriterleri · BEM kriterleri · `--cm-*` token kriterleri · salt-okunur tarama komutları · devir |
| `references/changelog.md` | §19 | Sürüm geçmişi 1.0 → 4.0.0 (reset kaydı) |

**`examples/` (3 örnek — her biri §5-§12 akışının uygulaması):**

| Dosya | Bağlandığı § | İçerik |
|-------|--------------|--------|
| `examples/mockup-vs-live-analysis.md` | §5, §7, §11, §12 | **TAM DÖNGÜ**: ham giriş → 6 adım → FACT/INFERENCE/UNKNOWN → puanlı rapor (N5 zorunlu örneği) |
| `examples/home-panel-audit.md` | §11, §12, §13 | Puanlı tam rapor örneği (2 HIGH · 1 MEDIUM · 1 LOW · 1 ⚠️ → sınıf D) |
| `examples/mockup-vs-code-mismatch.md` | §5, §10 | Dar sapma yürüyüşü (sınıflandırma odaklı, işaretli) |

---

## §18. Kaynaklar & Truth Mode (Web Research Zorunlu)

**Kural:** WCAG kriteri/eşiği, WAI-ARIA rolü veya ölçüm yöntemi gibi normatif bir
iddia **ilk kez** yazılacaksa önce resmi kaynak fetch edilir, doğrulanır, sonra
`references/sources.md` §1'e eklenir (Ad + URL + tür + erişim tarihi + ne doğrulandı).

```text
1. websearch / webfetch ile resmi kaynak al (W3C / WAI / MDN)
2. sources.md §1'e kaydı ekle (erişim tarihi güncelle)
3. wcag-criteria.md tablosunu senkronla
4. SKILL.md §8 özet eşiklerini senkronla
Doğrulanamayan iddia → ⚠️ VERIFICATION REQUIRED (−5). Eşik hafızadan yazılmaz.
```

**Doğrulanmış kaynaklar (erişim: 2026-10-21) — özet:**

| # | Kaynak | Ne doğrulandı |
|---|--------|---------------|
| S1 | https://www.w3.org/TR/WCAG22/ | WCAG 2.2 = W3C Recommendation **12 December 2024**; AA kriter listesi; 4.1.1 silindi |
| S2 | https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html | 4.5:1 yuvarlanmaz · large ≈ 24px/18.5px · kontrast formülü · placeholder/tooltip kapsamı · pasif bileşen muaf |
| S3 | https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html | 24×24 CSS px + 5 istisna · 24px daire spacing testi · teknik C42 |
| S4 | https://developer.mozilla.org/en-US/docs/Web/API/Window/getComputedStyle() | canlı salt-okunur resolved value · `:visited` düzeltmesi · CSSOM |

Tam kayıt (URL + tür + erişim + kapsam): `references/sources.md`.

---

**İlgili skiller:** `ui-code-generator` (düzeltme kodu) · `accessibility` (WCAG derin
denetim) · `browser-testing-with-devtools` (canlı sayfa) · `elite-uiux-architect`
(analiz/onay gate'i) · `pixel-perfect-verification` (kod↔PNG doğrulaması).

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
