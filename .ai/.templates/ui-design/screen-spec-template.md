---
reference_doc: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
title: "CoreMusic — UI Design Screen Specification Şablonu (Kalıp D)"
type: template
category: ui-design
pattern: D
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/.templates/ui-design/screen-spec-template.md"
  source_of_truth: ".ai/ui-design/reference/legacy-inventory.md §Şablon kalıpları (Kalıp D) · .ai/ui-design/screens/00-ascii-art-index.md"
---

# CoreMusic — UI Design Screen Specification Şablonu (Kalıp D)

> **NE ZAMAN OKUNUR:** `.ai/ui-design/screens/` altında **her yeni veya güncellenen ekran spec dosyası** (`screens/<tier>/<screen>.md` ve `screens/shared/*.md`) üretilirken bu şablon **ZORUNLU** okunur — tipik görevler: yeni tier ekranı (T01…T31), `shared/` auth akışı ekranı (login, register-step1-3, select-gender), mevcut ekranın ASCII Layout / token / WCAG bölümünün güncellenmesi. Ayrıca `screens/00-ascii-art-index.md`'e yeni satır eklenirken §3.10 (PNG Referansı) kuralları esas alınır. **Dosya yoksa veya 9 bölüm (`ASCII Layout → BEM → Token → Touch Target → WCAG → Glassmorphism → PNG Referansı → Responsive → State`) sırasını taşımıyorsa üretim DURAR.** Prompt için `[[prompt-template]]`, flow için `[[flow-template]]`, referans/tokens dokümanı için `[[reference-template]]` geçerlidir.

**Zorunlu Bağlantılar:** [[../index]] · [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../WORKFLOW.md]] · [[../../ui-design/00-device-matrix]] · [[../../ui-design/01-mockup-index]]

**Kalıp kaynağı:** `.ai/ui-design/reference/legacy-inventory.md` → "Şablon kalıpları" → **Kalıp D** (`screens/<tier>/*.md`): frontmatter + `reference.source_of_truth` **birebir PNG dosya adı** → H1 → `## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)` → `## 2. BEM Sınıfları` → `## 3. Token Referansları` → `## 4. Touch Target` → `## 5. WCAG Uyumu` → `## 6. Glassmorphism Stili` → `## 7. PNG Referansı` → `## 8. Responsive Davranış` → `## 9. State Durumları` → Authority footer.

---

## 1. Amaç

Bu şablon, CoreMusic ui-design vault'unda **bir ekranın piksel düzeyinde spesifikasyonunu** tanımlar: nerede hangi kutunun durduğu (ASCII Layout), hangi BEM sınıflarının açıldığı, hangi token'ların kullanıldığı, dokunma hedefi/erişilebilirlik/glassmorphism kuralları, hangi PNG'nin kaynak olduğu, responsive davranış ve durumlar. **Guardrail #16:** `screens/` altında yeni bir `.md` bu şablondan üretilmek ZORUNLUDUR.

**Disk gerçeği (2026-09-24):** `screens/` altında **23 md** vardır — **1 indeks** (`00-ascii-art-index.md`) + **5 shared** ekran + **17 tier ekranı**, **8 tier dizini**:

| Tier dizini | Ekran adı (md) |
|-------------|----------------|
| `T01-phone-hd/` | 2 (`auth-login`, `home-dashboard`) |
| `T02-phone-fhd/` | 1 (`home-dashboard`) |
| `T03-phone-qhd/` | 1 (`home-dashboard`) |
| `T08-embedded/` | 9 (`album-detail`, `albums`, `artists`, `bluetooth-modal`, `file-browser`, `home-dashboard`, `now-playing`, `welcome-popup`, `wifi-modal`) |
| `T17-monitor-22fhd/` | 1 (`home-dashboard`) |
| `T25-tv-43fhd/` | 1 (`home-dashboard`) |
| `T29-car-android-auto/` | 1 (`home-dashboard`) |
| `T31-watch-apple-40mm/` | 1 (`now-playing`) |
| `shared/` | 5 (`login`, `register-step1`, `register-step2`, `register-step3`, `select-gender`) |

**PNG kanıtı:** `.ai/.png/` altında **19 PNG** vardır — `home-1024/` 12 · `home-1920/` 1 (`Linux - 1920 - Home.png`) · `shared-1024/` 6. `reference.source_of_truth` yalnızca bu 19 dosyadan birini gösterebilir.

| Karar | Kaynak | Şablona gömülü karşılığı |
|-------|--------|--------------------------|
| 9 bölüm sırası sabit | `legacy-inventory.md` Kalıp D | §3.3 – §3.11 |
| `source_of_truth` = birebir PNG adı | Kalıp D + `.ai/.png/` envanteri | §3.12 |
| ASCII ekseni tier viewport'una göre | `00-device-matrix.md` | §3.4 |
| BEM/C01-C16 adları bağlayıcı | `02-component-inventory.md` | §3.5, §4 #3 |
| Token'lar master dosyadan | `tokens/design-tokens-master.md` | §3.6 |
| Tier kuralı ihlali → RED | AGENTS.md §7.2 (45-tier) | §4 #8, §6 #7 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `screens/<tier>/<ekran>.md` — tier ekran spec'i | Ekran akışı/hata senaryosu → `[[flow-template]]` |
| `screens/shared/<ekran>.md` — cihazdan bağımsız ekran | Kod üretim promptu → `[[prompt-template]]` |
| `screens/00-ascii-art-index.md` — ekran indeksi satırı | Bileşenin tek başına spec'i → `[[reference-template]]` |
| Mevcut spec'te ASCII/token/WCAG/state güncelleme | Üretilen HTML/CSS dosyası → `../frontend/js-template.md`, `../frontend/css-template.md` |
| Yeni tier dizini açma talebi (`T<NN>-<device>/`) | `.ai/.png/**` içine yazma (salt-okunur) |

- **Kullananlar:** UI Designer (spec yazar), herhangi bir frontend kodu üreten agent (spec'i okur), QA Engineer (§6 erişilebilirlik/touch kontrolü).
- **Katman:** L3 (sunum) — spec'in çıktısı L3 kodudur; L0/L1/L2'ye dokunulmaz.
- **Ön koşul:** Ekran gerçekten mockup'ta/PNG'de mevcut (`[[../../ui-design/01-mockup-index]]`); PNG yoksa spec **planlanmış** işaretlenir ve `⚠️ VERIFICATION REQUIRED` konur (Guardrail #11).

---

## 3. Mimari

### 3.1 Frontmatter (screen spec dosyası)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — <Screen> Screen Specification"
type: spec
category: ui-design
date: YYYY-MM-DD
status: active
version: X.Y.Z
tier: T{{NN}}
viewport: {{WxH}}
device: {{ciaz adı}}
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/<tier>/<screen>.md"
  source_of_truth: ".ai/.png/<klasör>/<birebir PNG dosya adı>.png"
---
```

**Kurallar:**
1. `type: spec` her zaman (prompt değil, spec).
2. `tier:` alanı `00-device-matrix.md`'deki tier adıyla aynı (`T08`, `T31` …).
3. `viewport:` **`x` yerine `x`** biçiminde yazılır (`1024x600`) — H1'de ise çarpan işareti `×` kullanılır (`1024×600`).
4. `device:` tek satırlık insan-okur cihaz adı (`RPi5 7" Touch (Embedded)`).
5. `reference.source_of_truth` **birebir** diskteki PNG adıdır (iki boşluk dâhil: `Linux  1024 - Home Page.png`); PNG yoksa alan `⚠️ VERIFICATION REQUIRED — PNG bekleniyor` değeri alır.
6. `reference:` bloğu bu dosyada **zorunludur** (Kalıp A'daki gibi opsiyonel değildir).

### 3.2 Örnek — Doldurulmuş frontmatter (gerçek dosyadan, `screens/T08-embedded/home-dashboard.md`)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/home-dashboard.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Home Page.png"
---
```

### 3.3 H1 + Zorunlu Bağlantılar

```
# CoreMusic — <Screen> (<Tier> <viewport>)
**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]
```

**Örnek (diskte):** `# CoreMusic — Home Dashboard (T08 Embedded 1024×600)`

**Kurallar:**
1. H1 kalıbı birebir: `CoreMusic — <Screen> (<Tier> <viewport>)`; tier adı harfli uzun form (`T08 Embedded`), viewport `×` ile.
2. Alt klasördeki dosyalarda wiki-link'ler **klasör adı olmadan**, kök içindekiler `tokens/` önekiyle yazılır (mevcut dosyalarda kalıp: `[[00-device-matrix]]`, `[[tokens/design-tokens-master]]`).
3. Zorunlu Bağlantılar tek satır, ` · ` ile ayrık; 4 bağlantı sabittir.
4. H1 ile `## 1.` arasında tek `---` ayracı vardır.

### 3.4 `## 1. ASCII Layout (Piksel Düzeyinde — x:0-<W>, y:0-<H>)`

Bölüm adı **sabittir**; parantezdeki eksen aralığı tier viewport'una göre yazılır (`x:0-1024, y:0-600`).

````markdown
## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────┐
│ x:0                                              x:1024   │
│ y:0 ┌─── HEADER (h:60) ─────────────────────────────────┐  │
│     │ Logo(120×40)   Ana Sayfa · Keşfet · Albümler      │  │
│ y:60└───────────────────────────────────────────────────┘  │
│ y:60┌─── CONTENT (h:450) ───────────────────────────────┐  │
│     │  ┌─── NOW PLAYING (42%, w:430) ───┐               │  │
│     │  │  🎵 Album Art (120×120)        │               │  │
│     │  └────────────────────────────────┘               │  │
│ y:510└──────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────┘
```
````

**Kurallar:**
1. Dil etiketsiz kod bloğu (yalnız ` ``` `).
2. Kutu karakterleri sabittir: `─ │ ┌ ┐ └ ┘ ├ ┤ ┬ ┴` (`flow/00-flow-index.md §8` ile aynı aile).
3. Her blokta `x:<sol>-<sağ>` ve `y:<üst>` köşe işaretleri + `(w:… / h:… / %…)` ölçü bulunur.
4. Yükseklikler toplam viewport'a eşittir (T08: `60 + 450 + … = 600`) — toplam tutmuyorsa spec hatalıdır.
5. Emoji/görsel simgeler yalnızca içerik yer tutucusudur; gerçek asset adı §7'dedir.
6. `y` ekseninde artış her satırda **azalan koordinat** olarak tekrarlanır (soldaki cetvel).

### 3.5 `## 2. BEM Sınıfları`

```markdown
## 2. BEM Sınıfları

| Bölüm | Block | Element / Modifier | Envantel |
|-------|-------|--------------------|----------|
| Header | `.screen-header` | `__logo`, `__nav`, `__nav--active` | C01 |
| Now Playing | `.now-playing` | `__art`, `__title`, `__controls`, `--playing` | C07 |
| Widget | `.widget-card` | `__icon`, `__value`, `--speaker` | C09 |
```

**Kurallar:**
1. 4 sütun sabit: Bölüm · Block · Element/Modifier · Envantel (`Cxx`).
2. Sınıf adları ve `Cxx` numaraları `02-component-inventory.md` ile **aynı olmalıdır** — envanterde olmayan `Cxx` yazılmaz (`—` konur + `⚠️ VERIFICATION REQUIRED`).
3. Nokta ile yazılır (`.block`), `block__element--modifier` biçimi zorunlu.
4. Ekran özelinde türetilen ve envanterde karşılığı olmayan block için son satır `| (ekran özel) | … | — | — |` olarak eklenir.

### 3.6 `## 3. Token Referansları`

```markdown
## 3. Token Referansları

| Kullanım | Token | Değer (master) |
|----------|-------|----------------|
| Vurgu rengi | `color-primary` | (master dosyadan okunur) |
| Başlık boşluğu | `space-3` | (master dosyadan okunur) |
| Kart yarıçapı | `radius-md` | (master dosyadan okunur) |
| Glass arka plan | `glass-bg` | (master dosyadan okunur) |
```

**Kurallar:**
1. Token **adı** yazılır; değer sütunu `tokens/design-tokens-master.md`'den okunur ve **bu şablonda uydurulmaz** (master'da yoksa satır silinir).
2. Ham hex/px tabloya **yazılmaz** — değer gösterilecekse `var(--token)` biçiminde verilir.
3. En az 4, en fazla 15 satır; her satır §1–§9'da gerçekten kullanılan token'ı temsil eder.

### 3.7 `## 4. Touch Target`

| Sınıf | Minimum | Not |
|-------|---------|-----|
| Dokunmatik tier (T01-T08, T29, T31) | **44×44 px** | WCAG 2.2 AA 2.5.8 alt sınırı — ihlal `04-accessibility-gaps.md`'e işlenir |
| Mouse tier (T17, T25) | 32×32 px (öneri) | Hover affordans mevcut |
| Yakınlık kuralı | ≥ 8 px boşluk | Yanlış basma önleme |
| Komut satırı | `touch target >= 44px (tier embedded/phone)` | Prompt `constraints` ile aynı ifade |

**Kurallar:** 3-5 satır; tier yazılır; `00-device-matrix.md`'deki tier sınıfıyla çelişen değer yazılmaz.

### 3.8 `## 5. WCAG Uyumu`

```markdown
## 5. WCAG Uyumu

| # | Kontrol | Kriter | Durum |
|---|---------|--------|-------|
| 1 | Metin kontrastı | ≥ 4.5:1 (normal), ≥ 3:1 (≥24px / 18.66px bold) | PASS / GAP |
| 2 | Odak (focus) görünür | 2px outline, kontrast ≥ 3:1 | PASS / GAP |
| 3 | Dokunma hedefi | ≥ 44×44 px | PASS / GAP |
| 4 | Okuma sırası / DOM sırası | Görsel sıra = DOM sırası | PASS / GAP |
| 5 | Durum yalnız renkle anlatılmıyor | İkon/metin + aria | PASS / GAP |
```

**Kurallar:**
1. `Durum` sütunu yalnız `PASS` veya `GAP` (GAP satırı `.ai/ui-design/04-accessibility-gaps.md`'e de işlenir).
2. Kriterler **ölçülüdür** ("iyi kontrast" yazmak yasak).
3. `aria-*` gereklilikleri ayrı satırda belirtilir (`aria-label`, `aria-current="page"`).

### 3.9 `## 6. Glassmorphism Stili`

```markdown
## 6. Glassmorphism Stili

| Öğe | Değer |
|-----|-------|
| `backdrop-filter` | `blur(20px)` |
| Arka plan | `rgba(255,255,255,.08)` (token `glass-bg`) |
| Kenarlık | `1px solid rgba(255,255,255,.18)` |
| Gölge | `0 8px 32px rgba(0,0,0,.25)` |
| Kontrast etkisi | Blur üstü metin ≥ 4.5:1 — değilse katman opaklığı artırılır |
```

**Kurallar:**
1. 4-6 satır; değerler token'a bağlanır (`var(--glass-…)`).
2. `backdrop-filter` desteklenmeyen tarayıcı için **fallback** satırı zorunlu: `background: rgba(…)` (solid).
3. Glass katman `§8 Responsive` ile çelişemez (tier'da blur kapalıysa bu tabloda da kapalı olmalı).

### 3.10 `## 7. PNG Referansı`

```markdown
## 7. PNG Referansı

- **Dosya:** `.ai/.png/home-1024/Linux  1024 - Home Page.png`
- **Klasör:** `home-1024/` (12 PNG) · Alternatif açı: `.ai/.png/home-1920/Linux - 1920 - Home.png`
- **Mockup indeksi:** [[../../ui-design/01-mockup-index]]
- **Kullanım sırası:** PNG > ASCII art > Inventory > Tokens > Reference (AGENTS.md §7.2)
```

**Kurallar:**
1. Dosya yolu **birebir** kopyalanır (adında çift boşluk olan `Linux  1024 - …` biçimleri dâhil) — tahmin edilmez, dizin listelenerek yazılır.
2. PNG `.ai/.png/` altında yoksa: `⚠️ VERIFICATION REQUIRED — PNG bulunamadı; spec görsel doğrulama olmadan TAMAMLANAMAZ`.
3. `.ai/.png/**` altına **yazma yapılmaz** (salt-okunur referans).
4. İlgili `screens/00-ascii-art-index.md` satırı güncellenir.

### 3.11 `## 8. Responsive Davranış`

| Davranış | Kural | Kaynak |
|----------|-------|--------|
| Kırılma davranışı | Tier aralığında yeniden akış / sabit grid kararı | `05-responsive-architecture` §7.4 (4K'da ortalamama) + §12 (fallback zorunlu) |
| Tier sıçraması | `T01 → T03 → T08 → T17 → T25 → T29 → T31` | `00-device-matrix.md` |
| Görsel ölçek | Piksel ölçüler `rem`/token'a çevrilir; ham px yalnız ASCII Layout'ta | Token-First |
| Fallback | Tier'a ait spec yoksa bir üst/alt tier spec'i + `§12` fallback kuralı | `05-responsive-architecture` §12 |
| Portre/Dikey | Tier desteklemiyorsa `N/A (landscape-only)` yazılır | `reference/10-device-specific-guidelines` |

**Not:** `[[../../ui-design/05-responsive-architecture]] §7.4 + §12` referansları bu bölümde de **korunur** — o bölümler ui-designer tarafından eklenmektedir; bağlantı silinmez.

### 3.12 `## 9. State Durumları`

```markdown
## 9. State Durumları

| State | Tetikleyici | Görsel | ARIA |
|-------|-------------|--------|------|
| Default | ilk render | `opacity:.7` | — |
| Hover | `@media (hover:hover)` | `opacity:1` + vurgu | `aria-describedby` |
| Pressed | dokunma | `scale(.98)` | — |
| Focus | klavye | `outline: 2px solid var(--color-primary)` | `:focus-visible` |
| Active | seçim | `font-weight:600` + işaretçi | `aria-current="page"` |
| Disabled | yetki/veri yok | `opacity:.4` + `not-allowed` | `aria-disabled="true"` |
| Loading | veri bekleniyor | skeleton/spinner | `aria-busy="true"` |
```

**Kurallar:**
1. En az 5, en fazla 8 state; `Hover` **yalnızca** `hover:hover` capability olan tier'larda — T29/T31 gibi dokunmatik odaklı tier'larda `Hover` yerine `Pressed` yazılır (`reference/09-interaction-states.md`).
2. `Görsel` sütunu token/var içerir (ham hex yasak).
3. `ARIA` sütunu `-` ile bırakılabilir; boş hücre yazılmaz.
4. State listesi §7.4'teki `states[]` (prompt) ile çelişemez.

### 3.13 Authority footer

```markdown
---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** YYYY-MM-DD
**Mode:** Red Team · Human Mode · Truth Mode
```

### 3.14 Kopyala-Yapıştır — Tam Kalıp D İskeleti

````markdown
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — {{Screen}} Screen Specification"
type: spec
category: ui-design
date: {{YYYY-MM-DD}}
status: active
version: {{X.Y.Z}}
tier: {{TNN}}
viewport: {{WxH}}
device: {{cihaz}}
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/{{tier}}/{{screen}}.md"
  source_of_truth: "{{birebir PNG yolu veya VERIFICATION REQUIRED}}"
---

# CoreMusic — {{Screen}} ({{Tier}} {{W}}×{{H}})

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-{{W}}, y:0-{{H}})
{{dil etiketsiz kutu — ölçü + köşe koordinatları}}

## 2. BEM Sınıfları
{{tablo: Bölüm | Block | Element/Modifier | Envantel}}

## 3. Token Referansları
{{tablo: Kullanım | Token | Değer (master)}}

## 4. Touch Target
{{tablo: 44px / yakınlık / tier notu}}

## 5. WCAG Uyumu
{{tablo: # | Kontrol | Kriter | PASS/GAP}}

## 6. Glassmorphism Stili
{{tablo: blur / bg / border / shadow / fallback}}

## 7. PNG Referansı
{{birebir PNG yolu + mockup indeksi + kullanım sırası}}

## 8. Responsive Davranış
{{tablo: kırılma | tier sıçraması | ölçek | fallback | portre}}

## 9. State Durumları
{{tablo: State | Tetikleyici | Görsel | ARIA}}

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** {{YYYY-MM-DD}}
**Mode:** Red Team · Human Mode · Truth Mode
````

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Guardrail #16 — bu şablonsuz screen spec dosyası üretilmez | Dosya geçersiz |
| 2 | 9 bölüm sırası ve adları sabit (`## 1.` … `## 9.`) | Dosya geçersiz |
| 3 | BEM/Cxx adları `02-component-inventory.md` ile aynı | Ad düzeltilir |
| 4 | Token adları `tokens/design-tokens-master.md`'de var; ham hex/px yok | Satır silinir |
| 5 | Guardrail #11 — mockup/PNG okunmadan spec yazılmaz | Spec "planned" + DUR |
| 6 | `reference.source_of_truth` birebir PNG adı (veya `⚠️ VERIFICATION REQUIRED`) | Halüsinasyon — alan temizlenir |
| 7 | ASCII Layout yükseklikleri viewport toplamına eşit | Layout reddedilir |
| 8 | Tier viewport/tier adı `00-device-matrix.md` ile tutarlı | Tier kuralı ihlali → RED (AGENTS.md §7.2) |
| 9 | Touch target ≥ 44px (T01-T08, T29, T31) — ihlal GAP | Spec + `04-accessibility-gaps.md` kaydı |
| 10 | Secret/token/`figd_...` yazımı yasak | `[REDACTED]` + log |

### 4.2 Ek Kurallar

- **Zorunlu:** dosya adı `screens/<tier-dizini>/<kebab-ekran>.md`; tier dizini `T<NN>-<device>` biçiminde; mevcut ad **değişmez** (Rule #1 — onay gerekir).
- **Zorunlu:** Türkçe içerik UTF-8 (BOM'suz); yazım `.ai/scripts/vault-utf8-writer.mjs` ile (`write` + hemen ardından `verify`).
- **Zorunlu:** yeni ekran `screens/00-ascii-art-index.md`'e satır olarak eklenir.
- **Yasak:** `.ai/.png/**` içine yazmak (salt-okunur).
- **Yasak:** 9 bölümden birini atlamak, yeniden adlandırmak veya sırayı değiştirmek.
- **Yasak:** `viewport`/`tier` değerini tahmin etmek — `00-device-matrix.md`'den okunur.
- **Yasak:** bu şablonu `prompt/` veya `flow/` dosyasına uygulamak (yanlış kalıp).

---

## 5. Workflow

```text
PNG + MOCKUP OKU → DEVICE-MATRIX OKU → ŞABLONU SEÇ → İSKELET → 1-ASCII → 2-BEM → 3-TOKEN → 4-TOUCH → 5-WCAG → 6-GLASS → 7-PNG → 8-RESPONSIVE → 9-STATE → §6 DOĞRULA → VAULT-YAZ → INDEX + LOG
```

| # | Adım | Aksiyon | Çıktı |
|---|------|---------|-------|
| 1 | PNG + mockup | `.ai/.png/<klasör>/` listesi + `[[../../ui-design/01-mockup-index]]` oku; PNG yoksa **DUR** (`⚠️ VERIFICATION REQUIRED`) | Kanıt |
| 2 | Device matrix | `[[../../ui-design/00-device-matrix]]` → tier + viewport + device | Kanıt |
| 3 | Şablon seç | Screen spec → bu dosya (`Kalıp D`) | Karar |
| 4 | İskelet | §3.14'ü staging'e kopyala; H1'i §3.3 kalıbıyla yaz | İskelet |
| 5 | §1 ASCII | §3.4 — eksen, ölçü, toplam = viewport | §-ASCII Layout |
| 6 | §2 BEM | §3.5 — envanter `Cxx` ile birebir | §-BEM |
| 7 | §3 Token | §3.6 — master dosyadan ad/değer | §-Token |
| 8 | §4-§6 | §3.7-§3.9 — 44px, PASS/GAP, glass + fallback | 3 bölüm |
| 9 | §7 PNG | §3.10 — birebir yol (çift boşluk dâhil) | §-PNG |
| 10 | §8-§9 | §3.11-§3.12 — `§7.4 + §12`, tier uygun state listesi | 2 bölüm |
| 11 | Doğrula + yaz | §6 listesi; `vault-utf8-writer write` + `verify`; `00-ascii-art-index.md` + `.templates/index.md` + `log.md` | Kayıt |

### 5.1 Adım Bazlı Hata Modları

| Adım | Tipik hata | Belirti | Düzeltme |
|------|-----------|---------|----------|
| 1 | PNG yok | İlgili ekran `.ai/.png/`'de bulunmuyor | `⚠️ VERIFICATION REQUIRED`; spec "planned" |
| 2 | Uydurma viewport | `00-device-matrix.md`'de olmayan `1440x900` | Matrix'ten oku; yoksa tier talebi olarak logla |
| 5 | Yükseklik toplamı tutmuyor | `60+450+… ≠ 600` | Kutu ölçülarını yeniden hesapla |
| 6 | Uydurma `Cxx` | Envanterde olmayan `C22` | `02-component-inventory.md`'den al veya `—` + VERIFICATION |
| 7 | Ham hex | `#ff4fd8` tabloda | `var(--color-primary)` / token adı |
| 10 | Hover dokunmatik tier'da | T29/T31'de `Hover` satırı | `Pressed` + `09-interaction-states.md` |
| 11 | İndeks uyuşmazlığı | `00-ascii-art-index.md` yeni ekranı içermiyor | İndeks satırını ekle + log |

### 5.2 Hızlı Komut Referansı

```powershell
# tier/ekran envanteri (disk kanıtı)
Get-ChildItem .ai/ui-design/screens -Recurse -Filter *.md | Select-Object -ExpandProperty FullName
# PNG adlarını birebir al (ad tahmini yasak)
Get-ChildItem .ai/.png -Recurse -Filter *.png | Select-Object -ExpandProperty Name
# bölüm sırası kontrolü
Select-String -Path <spec-dosyası> -Pattern '^## \d\. '
# yaz + doğrula
node .ai/scripts/vault-utf8-writer.mjs write --file <vault-yolu> --text "@<staging>"
node .ai/scripts/vault-utf8-writer.mjs verify --file <vault-yolu>
```

---

## 6. Doğrulama

### 6.1 Kontrol Listesi

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | `type: spec`; `tier`/`viewport`/`device` dolu; `reference.source_of_truth` birebir PNG veya VERIFICATION; 7 registry zorunlu alan içeride |
| 2 | H1 | `CoreMusic — <Screen> (<Tier> <W>×<H>)` kalıbı |
| 3 | Bölüm yapısı | `## 1.` … `## 9.` tam, doğru ad, doğru sıra + Authority footer |
| 4 | ASCII Layout | Dil etiketsiz blok; köşe koordinatları; toplam yükseklik = viewport |
| 5 | BEM | Sınıflar nokta/blok/element--modifier; `Cxx` envanterle aynı |
| 6 | Token | Adlar master dosyada; ham hex/px yok |
| 7 | Touch + WCAG | ≥44px (ilgili tier); `PASS/GAP` biçimi; ölçülebilir kriterler |
| 8 | PNG | Yol diskteki adla birebir (çift boşluk dâhil); `.png` uzantılı |
| 9 | Responsive/State | `§7.4 + §12` bağlantıları korunmuş; tier uygun state listesi |
| 10 | Mojibake | `verify` → `hasBom=false, mojibake=0, cjk=0` |

### 6.2 Quality Report (şablonun kendisi)

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 8 |
| Last Updated | 2026-09-24 |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[../index]] | Şablon envanteri (DRY) |
| Vault anayasası | [[../../CLAUDE.md]] | Guardrail #3/#4/#10/#11/#16/#17 |
| Agent registry | [[../../AGENTS.md]] | §7.2 pre-flight: mockup + tier + responsive kontrolü |
| Süreç | [[../../WORKFLOW.md]] | UI Design Gate / Verification Gate |
| Cihaz matrisi | [[../../ui-design/00-device-matrix]] | tier / viewport / device |
| Mockup indeksi | [[../../ui-design/01-mockup-index]] | PNG envanteri (19 PNG) |
| Bileşen envanteri | [[../../ui-design/02-component-inventory]] | C01-C16 BEM/piksel/token |
| Master token'lar | [[../../ui-design/tokens/design-tokens-master]] | §3 token adları |
| Erişilebilirlik boşlukları | [[../../ui-design/04-accessibility-gaps]] | §5 PASS/GAP kaydı |
| Responsive mimarisi | [[../../ui-design/05-responsive-architecture]] | §8 — §7.4 (4K ortalamama) + §12 (fallback) |
| ASCII indeksi | [[../../ui-design/screens/00-ascii-art-index]] | Yeni ekran satırı |
| Etkileşim durumları | [[../../ui-design/reference/09-interaction-states]] | §9 hover capability |
| Tier rehberi | [[../../ui-design/reference/10-device-specific-guidelines]] | §8 portre/tier kuralları |
| Reference şablonu | [[reference-template]] | Kalıp A |
| Flow şablonu | [[flow-template]] | Kalıp B |
| Prompt şablonu | [[prompt-template]] | Kalıp C |
| CSS şablonu | [[../frontend/css-template]] | Üretilen CSS'in gerçek şablonu |

### 7.1 Tier → Ekran Eşleşmesi (disk kanıtı, 2026-09-24)

| Tier | Dizin | Ekranlar |
|------|-------|----------|
| T01 Phone HD | `T01-phone-hd/` | `auth-login`, `home-dashboard` |
| T02 Phone FHD | `T02-phone-fhd/` | `home-dashboard` |
| T03 Phone QHD | `T03-phone-qhd/` | `home-dashboard` |
| T08 Embedded | `T08-embedded/` | `album-detail`, `albums`, `artists`, `bluetooth-modal`, `file-browser`, `home-dashboard`, `now-playing`, `welcome-popup`, `wifi-modal` |
| T17 Monitor 22" FHD | `T17-monitor-22fhd/` | `home-dashboard` |
| T25 TV 43" FHD | `T25-tv-43fhd/` | `home-dashboard` |
| T29 Car Android Auto | `T29-car-android-auto/` | `home-dashboard` |
| T31 Watch Apple 40mm | `T31-watch-apple-40mm/` | `now-playing` |
| Shared | `shared/` | `login`, `register-step1`, `register-step2`, `register-step3`, `select-gender` |
| **TOPLAM** | 9 dizin | **22 ekran + 1 indeks = 23 md** |

> ⚠️ **Truth Mode notu:** `00-ascii-art-index.md` ve `01-mockup-index.md` içindeki tier/PNG sayıları dönem dönem disk gerçeğiyle (23 md / 19 PNG) çelişebilir — yeni spec yazarken sayılar **glob ile tazelenir**, eski sayı kopyalanmaz.

### 7.2 Red Team — Sık Yapılan Hatalar

| # | Hata | Neden yanlış | Doğru |
|---|------|--------------|-------|
| 1 | PNG adını tahmin etmek (`linux-home.png`) | `source_of_truth` birebir ad ister | `Get-ChildItem .ai/.png -Recurse` ile kopyala |
| 2 | ASCII Layout'ta ölçü yok | Piksel düzeyinde spec şartı | `x:`/`y:` + `w:`/`h:` işaretleri |
| 3 | Token değerini uydurmak | Master SSOT (Token-First) | Adı yaz, değeri master'dan oku |
| 4 | Tier'ı tahmin etmek | 45-tier matrisi bağlayıcı | `00-device-matrix.md` |
| 5 | Bölüm sırasını değiştirmek | Kalıp D sabit | `## 1.` … `## 9.` aynen |
| 6 | `.png/` klasörüne görsel kaydetmek | `.png/**` salt-okunur (başka agent'ın alanı) | Referans ver, yazma |
| 7 | `§7.4 + §12` bağlantısını silmek | ui-designer bölümleri ekliyor | Bağlantıyı KORU |

---

*UI Design Screen Specification Template (Kalıp D) v1.0.0 — CoreMusic Template System*
**Template Version:** 1.0.0
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
