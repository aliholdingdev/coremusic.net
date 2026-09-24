---
reference_doc: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
title: "CoreMusic — UI Design AI Code Generation Prompt Şablonu (Kalıp C)"
type: template
category: ui-design
pattern: C
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/.templates/ui-design/prompt-template.md"
  source_of_truth: ".ai/ui-design/reference/legacy-inventory.md §Şablon kalıpları (Kalıp C) · .ai/ui-design/prompt/00-prompt-index.md §7"
---

# CoreMusic — UI Design AI Code Generation Prompt Şablonu (Kalıp C)

> **NE ZAMAN OKUNUR:** `.ai/ui-design/prompt/` altında **her yeni veya güncellenen üretim promptu** (`prompt/{component,page,screen,layout}/NN-*.md`) üretilirken bu şablon **ZORUNLU** okunur — tipik görevler: C01-C16 bileşen promptu, 12 sayfa promptu (`page/01-home` … `12-bluetooth`), 10 tier ekran promptu (`screen/T1-phone` … `10-swatch` → `T10-watch`), 10 layout pattern promptu (`layout/01-mobile-stack` … `10-spatial-ar`). Ayrıca `prompt/00-prompt-index.md §7` (Prompt Formatı) güncellenirken de bu kalıp esas alınır. **Dosya yoksa veya metin `Context → Required Inputs → ASCII Reference → Prompt Template (JSON) → Expected Output (HTML+CSS) → Validation` sırasını taşımıyorsa üretim DURAR.** Flow için `[[flow-template]]`, ekran spec'i için `[[screen-spec-template]]`, referans için `[[reference-template]]` geçerlidir.

**Zorunlu Bağlantılar:** [[../index]] · [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../WORKFLOW.md]] · [[../../ui-design/prompt/00-prompt-index]] · [[../../ui-design/02-component-inventory]]

**Kalıp kaynağı:** `.ai/ui-design/reference/legacy-inventory.md` → "Şablon kalıpları" → **Kalıp C**; idari tanım `prompt/00-prompt-index.md §7`: **Context / Required Inputs / Prompt Template / Expected Output / Validation** (bu şablon §7'ye `ASCII Reference`'ı da ekler — diskteki 16 bileşen promptunun tamamında vardır).

---

## 1. Amaç

Bu şablon, CoreMusic ui-design vault'unda **makineye (LLM/agent) verilen kod üretim promptlarının** tek biçimini tanımlar: bağlam, girdiler, ASCII referans, JSON prompt gövdesi, beklenen HTML+CSS çıktısı ve doğrulama listesi. **Guardrail #16:** `prompt/` altında yeni bir `.md` bu şablondan üretilmek ZORUNLUDUR.

**Disk gerçeği (2026-09-24):** `prompt/` altında **50 md** vardır — 1 master indeks + **49 prompt**, 4 alt kategori: `component/` 16 (C01-C16) · `page/` 12 · `screen/` 11 (indeks + T1-T10) · `layout/` 10. `00-prompt-index.md` içindeki "175 prompt", "14 page", `C02-search-bar`, `01-pattern-mobile-stack`, `page/02-library` iddiaları disk gerçeğiyle ÇELİŞİR — yeni prompt yazarken dosya adları glob ile doğrulanır.

| Karar | Kaynak | Şablona gömülü karşılığı |
|-------|--------|--------------------------|
| Prompt formatı 5 bileşen | `prompt/00-prompt-index.md §7` | §3.3 |
| C01-C16 envanter adları bağlayıcı | `02-component-inventory.md` | §3.5, §4 #6 |
| Token'lar master dosyadan | `tokens/design-tokens-master.md` | §3.7 `tokens` alanı |
| Guardrail #10 — framework yasak, Vanilla JS + ITCSS | ADR-001 | §4 #4 |
| Guardrail #11 — mockup okunmadan prompt yazılmaz | CLAUDE.md §7 | §5 adım 1 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `prompt/component/C01…C16-*.md` — bileşen promptu | Flow dokümanı → `[[flow-template]]` |
| `prompt/page/NN-<page>.md` — sayfa promptu | Ekranın piksel layout'u → `[[screen-spec-template]]` |
| `prompt/screen/TNN-<device>-*.md` — tier ekran promptu | Backend/PHP üretim promptu → `.ai/.templates/backend/php-template.md` |
| `prompt/layout/NN-<pattern>.md` — layout pattern promptu | Gerçek üretilen HTML/CSS dosyası → `../frontend/js-template.md`, `../frontend/css-template.md` |
| `prompt/00-prompt-index.md §7` format tanımı | Üretim sonrası kod incelemesi → `WORKFLOW.md §8.1` |

- **Kullananlar:** UI Designer (prompt yazar), herhangi bir üretim yapan agent (prompt'u okur), QA Engineer (§3.9 Validation).
- **Katman:** L3 (sunum) — prompt'un çıktısı L3 kodudur; L0/L1/L2'ye dokunulmaz.
- **Ön koşul:** Bileşen/sayfa gerçekten envanterde ve mockup'ta mevcut (`[[../../ui-design/01-mockup-index]]`).

---

## 3. Mimari

### 3.1 Frontmatter (prompt dosyası)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — {{Başlık}} Prompt"
type: prompt
category: ui-design
date: YYYY-MM-DD
status: active
version: X.Y.Z
component: C{{NN}}          # yalnız bileşen promptunda
tier: {{tier}}              # yalnız tier/ekran promptunda
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

**Kurallar:**
1. `type: prompt` her zaman.
2. `component:` alanı **yalnızca** `prompt/component/` dosyalarında bulunur (`C01`…`C16`).
3. `tier:` alanı **yalnızca** `prompt/screen/` (ve gerekiyorsa `layout/`) dosyalarında bulunur (`T1`…`T31` aralığındaki tier adı).
4. `governance` alanı diskte **bazı dosyalarda yok** (gerçek kanıt) — bu bir tutarsızlıktır; yeni yazılan dosyalarda `governance` **bulundurulur** (7 registry zorunlu alanına dokunulmaz).
5. `reference:` bloğu kullanılmaz.

### 3.2 Örnek — Doldurulmuş frontmatter (gerçek dosyadan)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Nav Link Component Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
component: C01
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

### 3.3 Zorunlu Bölüm Yapısı

```
# {{H1}}                                    ← H1: "<Ad> Component Prompt (Cxx)" / "NN — <Page> Page" / "T<N>: <Device> Screen Prompt"
## AI Code Generation Prompt                ← TEK H2, adı birebir budur
  ### Context                               ← 1
  ### Required Inputs                       ← 2
  ### ASCII Reference                       ← 3
  ### Prompt Template                       ← 4  (```json)
  ### Expected Output                       ← 5  (```html + ```css)
  ### Validation                            ← 6  (- [ ] checklist)
--- + Authority footer
```

**H1 kalıpları (kategoriye göre birebir):**

| Alt kategori | H1 kalıbı | Örnek (diskte) |
|--------------|-----------|----------------|
| `component/` | `<Ad> Component Prompt (Cxx)` | `Nav Link Component Prompt (C01)` |
| `page/` | `NN — <Page> Page` | `01 — Home Page` |
| `screen/` | `T<N>: <Device> Screen Prompt` | `T1: Phone Screen Prompt` |
| `layout/` | `<Pattern> Layout (<W>×<H>)` | `Mobile Stack Layout (720×1280)` |

**H2 tek, adı sabittir:** `## AI Code Generation Prompt`. Altındaki 6 H3 sırası değiştirilemez; `###` seviyesi 4'e inmez.

### 3.4 `### Context`

2-5 cümle: bu prompt ne üretir, hangi katmanda yaşar, hangi ADR/guardrail'e tabidir.

```markdown
### Context
CoreMusic navigasyon linki bileşeni. Header ve bottom tab'da kullanılır.
Vanilla JS ES6+ + ITCSS (ADR-001), BEM namespace zorunlu, `innerHTML` yasaktır.
```

**Kural:** Context'te sayısal iddia (px, token adı, tier) varsa o iddia `Required Inputs` veya `ASCII Reference` içinde de tekrarlanır — tek kaynaktan okunur, iki yerde farklı olmaz.

### 3.5 `### Required Inputs`

Madde listesi; her satır bir girdi: `` - `ad`: açıklama ``

```markdown
### Required Inputs
- `label`: Link metni
- `icon`: İkon (opsiyonel)
- `active`: Aktif durum (boolean)
- `href`: Yönlendirme URL'i
```

**Kurallar:**
1. Her satır `` - `isim`: açıklama `` biçimindedir (inline code zorunlu).
2. Token girdileri token ADIyla yazılır (`--color-primary` değil, `color-primary`) — değer `Prompt Template > tokens` alanındadır.
3. En az 2, en fazla 10 girdi. Zorunlu girdi `*(zorunlu)*` ile, opsiyonel `(opsiyonel)` ile işaretlenir.
4. `tier` ve `viewport` girdileri her promptta bulunur (tier promptu değilse default tier yazılır).

### 3.6 `### ASCII Reference`

```markdown
### ASCII Reference
​```
Default:     [Ana Sayfa]           opacity: 0.7, font-weight: 400
Hover:       [Ana Sayfa]           opacity: 1, color: #ff4fd8, underline
Active:      [Ana Sayfa]           opacity: 1, font-weight: 600, border-bottom
Focus:       [Ana Sayfa]           outline: 2px solid #ff4fd8
​```
```

**Kurallar:**
1. Kod bloğu, **dil etiketi olmadan** (yalnız ` ``` `).
2. Her satır: `Durum:` + boşlukla hizalanmış görsel temsil + 2+ boşluk + ölçü/token notu.
3. En az 4 durum: `Default`, `Hover` **veya** `Focus`, `Active`, `Disabled`. Dokunmatik tier'da `Hover` yerine `Pressed` yazılır.
4. ASCII referans, `tokens/design-tokens-master.md` ve `reference/09-interaction-states.md` (mouse vs touch, `hover: hover` media query) ile tutarlıdır.
5. Bu blok yoksa prompt eksiktir — §6 #4 ihlali.

### 3.7 `### Prompt Template` (JSON)

```markdown
### Prompt Template
​```json
{
  "task": "Create nav link component for CoreMusic",
  "component": "C01",
  "bem": ".nav-link",
  "states": ["default", "hover", "active", "focus", "disabled"],
  "tokens": ["color-primary", "space-2", "font-size-body", "radius-sm"],
  "constraints": [
    "Vanilla JS ES6+ — framework yasak (ADR-001)",
    "BEM: block__element--modifier",
    "innerHTML yasak — createElement + textContent",
    "touch target >= 44px (tier embedded/phone)"
  ]
}
​```
```

**JSON şeması — 5 anahtar zorunlu:**

| Anahtar | Tip | İçerik |
|---------|-----|--------|
| `task` | string | Tek cümlelik görev, "CoreMusic" adı geçer |
| `component` **veya** `page`/`screen`/`layout` | string | Kategoriye göre kimlik (`C01`, `01-home`, `T8-embedded`, `mobile-stack`) |
| `bem` | string | Kök BEM sınıfı, nokta ile başlar |
| `states` | string[] | En az 3 durum |
| `tokens` | string[] | Kullanılacak token adları (master dosyadan) |
| `constraints` | string[] | Guardrail/ADR kısıtları — en az 3 madde |

**Kurallar:** JSON geçerli olmalıdır (virgül/keyword hataları yasak); `tokens` içindeki her ad `tokens/design-tokens-master.md`'de bulunmalıdır; `constraints` içeriği en az ADR-001 (framework yasak) + BEM + `innerHTML` yasak maddelerini içerir.

### 3.8 `### Expected Output`

İki kod bloğu, **HTML önce CSS sonra**, dil etiketiyle (`html`, `css`):

```markdown
### Expected Output
​```html
<nav class="nav-link" data-component="nav-link">
  <a class="nav-link__item nav-link__item--active" href="/">Ana Sayfa</a>
</nav>
​```

​```css
.nav-link { display: flex; gap: var(--space-2); }
.nav-link__item { opacity: .7; text-decoration: none; }
.nav-link__item--active { opacity: 1; font-weight: 600; }
​```
```

**Kurallar:**
1. HTML: BEM sınıfları §3.7 `bem` ile başlar; `data-component` attr'ı vardır; `innerHTML` kalıbı YOK.
2. CSS: **yalnız custom property** (`var(--...)`) kullanılır; ham hex/px yasak (assets AGENTS.md §4 #2).
3. ITCSS katmanı: çıktının hangi katmanda yaşayacağı §3.7 `constraints` içinde belirtilir (ör. `Css/04_Components/`).
4. Çıktı ≤ 60 satır HTML + ≤ 60 satır CSS (prompt amaca hizmet eder, tam sayfa kodlamaz).
5. Çıktı **beklenti**tir, üretilmiş kod dosyası değildir — gerçek kod `assets.coremusic.net/` altına `../frontend/*` şablonlarıyla yazılır.

### 3.9 `### Validation`

```markdown
### Validation
- [ ] BEM formatı `block__element--modifier` uygun
- [ ] Tüm renk/boşluk `var(--token)` ile — ham hex/px yok
- [ ] `innerHTML` / `eval` / `var` kullanımı yok
- [ ] Touch target ≥ 44px (embedded/phone tier)
- [ ] WCAG 2.2 AA kontrast ≥ 4.5:1 (metin), ≥ 3:1 (büyük metin/border)
- [ ] Focus-visible outline mevcut (`2px solid var(--color-primary)`)
- [ ] Framework/import yasak ihlali yok
- [ ] `ASCII Reference`'taki 4+ durumun tamamı mevcut
```

**Kurallar:** biçim `- [ ]` (task list); en az 6, en fazla 12 madde; her madde **ölçülebilir** ("güzel görünür" yasak); maddeler §6 Doğrulama listesiyle çelişemez.

### 3.10 Authority footer

```markdown
---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** YYYY-MM-DD
**Mode:** Red Team · Human Mode · Truth Mode
```

### 3.11 Kopyala-Yapıştır — Tam Kalıp C İskeleti

````markdown
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — {{AD}} Prompt"
type: prompt
category: ui-design
date: {{YYYY-MM-DD}}
status: active
version: {{X.Y.Z}}
component: {{Cxx}}            # veya tier: — kategoriye göre
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# {{H1}}

## AI Code Generation Prompt

### Context
{{2-5 cümle — ne üretir, hangi katman/ADR}}

### Required Inputs
- `{{girdi}}`: {{açıklama}} *(zorunlu)*
- `{{girdi}}`: {{açıklama}} (opsiyonel)
- `tier`: {{tier}}
- `viewport`: {{W×H}}

### ASCII Reference
```
Default:  {{temsil}}   {{ölçü/token}}
Hover:    {{temsil}}   {{ölçü/token}}
Active:   {{temsil}}   {{ölçü/token}}
Focus:    {{temsil}}   {{ölçü/token}}
Disabled: {{temsil}}   {{ölçü/token}}
```

### Prompt Template
```json
{
  "task": "Create {{ne}} for CoreMusic",
  "component": "{{Cxx}}",
  "bem": ".{{block}}",
  "states": ["default", "hover", "active", "focus", "disabled"],
  "tokens": ["{{token}}", "{{token}}"],
  "constraints": [
    "Vanilla JS ES6+ — framework yasak (ADR-001)",
    "BEM: block__element--modifier",
    "innerHTML yasak — createElement + textContent",
    "touch target >= 44px"
  ]
}
```

### Expected Output
```html
{{html — BEM + data-component}}
```

```css
{{css — yalnız var(--token)}}
```

### Validation
- [ ] {{ölçülebilir kontrol}}
- [ ] {{ölçülebilir kontrol}}
- [ ] {{ölçülebilir kontrol}}

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
| 1 | Guardrail #16 — bu şablonsuz prompt dosyası üretilmez | Dosya geçersiz |
| 2 | H2 tek ve `## AI Code Generation Prompt`; 6 H3 sırası sabit | Dosya geçersiz |
| 3 | `Prompt Template` geçerli JSON; 5 zorunlu anahtar | Prompt reddedilir |
| 4 | ADR-001 — `constraints` içinde framework yasaklı; çıktı Vanilla JS/ITCSS | Prompt reddedilir |
| 5 | Guardrail #11 — mockup/envanter okunmadan prompt yazılmaz | Prompt + kod durur |
| 6 | `bem` ve sınıf adları `02-component-inventory.md` (C01-C16) ile aynı | Ad düzeltilir |
| 7 | `tokens` adları `tokens/design-tokens-master.md`'de var | Token reddedilir |
| 8 | CSS çıktısında ham hex/px yok; yalnız `var(--token)` | Çıktı reddedilir |
| 9 | `Validation` ≥ 6 ölçülebilir madde | Dosya eksik |
| 10 | Secret/token/`figd_...` yazımı yasak | `[REDACTED]` + log |

### 4.2 Ek Kurallar

- **Zorunlu:** dosya adı `prompt/<kategori>/NN-<kebab>.md`; `NN` kategorideki sıradaki numaradır; mevcut ad değişmez.
- **Zorunlu:** `ASCII Reference` durumları `reference/09-interaction-states.md` (hover capability media query) ile uyumlu — dokunmatik tier'da `Hover` değil `Pressed`.
- **Zorunlu:** Türkçe içerik UTF-8 (BOM'suz); yazım `.ai/scripts/vault-utf8-writer.mjs` ile.
- **Yasak:** `Expected Output`'u tam sayfa HTML'e çevirmek (prompt değil, kod dosyası üretirsin).
- **Yasak:** JSON içine yorum satırı (`//`) koymak.
- **Yasak:** 6 H3'ten birini atlamak veya yeniden adlandırmak.

---

## 5. Workflow

```text
MOCKUP + ENVANTER OKU → ŞABLONU SEÇ → İSKELET → CONTEXT/INPUTS → ASCII → JSON → EXPECTED → VALIDATION → §6 DOĞRULA → VAULT-YAZ → INDEX + LOG
```

| # | Adım | Aksiyon | Çıktı |
|---|------|---------|-------|
| 1 | Mockup + envanter | `[[../../ui-design/01-mockup-index]]` + `[[../../ui-design/02-component-inventory]]` oku; okunamıyorsa **DUR** | Kanıt |
| 2 | Şablon seç | Prompt → bu dosya | Karar |
| 3 | İskelet | §3.11'i staging'e kopyala; H1 kalıbını kategoriye göre seç (§3.3) | İskelet |
| 4 | Context + Inputs | §3.4/§3.5 — girdi adları inline code | §-Context, §-Inputs |
| 5 | ASCII | §3.6 — ≥4 durum, hizalı | §-ASCII |
| 6 | JSON | §3.7 — 5 anahtar, geçerli JSON, token'lar master'dan | §-Prompt Template |
| 7 | Expected | §3.8 — html + css, `var(--token)` | §-Expected Output |
| 8 | Validation | §3.9 — ≥6 ölçülebilir madde | §-Validation |
| 9 | Doğrula + yaz | §6 listesi; `vault-utf8-writer write` + `verify`; `prompt/00-prompt-index.md` + `.templates/index.md` + `log.md` | Kayıt |

### 5.1 Adım Bazlı Hata Modları

| Adım | Tipik hata | Belirti | Düzeltme |
|------|-----------|---------|----------|
| 1 | Mockup yok | İlgili ekran `screens/`/PNG'de yok | `⚠️ VERIFICATION REQUIRED`; promptu "planned" işaretle |
| 3 | Yanlış H1 | `page/` dosyasında component H1 | §3.3 tablosundan doğru kalıp |
| 6 | Geçersiz JSON | Virgül/`//` hatası | JSON doğrulayıcıdan geçir |
| 6 | Uydurma token | `tokens` adı master dosyada yok | Token adını master'dan al ya da satırı sil |
| 7 | Ham hex/px | `#ff4fd8` doğrudan CSS'te | `var(--color-primary)` |
| 8 | Validation sayılamaz | "Erişilebilir görünür" | Ölçülebilir maddeye çevir (≥4.5:1) |
| 10 | İndeks uyuşmazlığı | `00-prompt-index` disk adlarını yansıtmıyor | İndeks tablosunu glob ile tazele + log |

### 5.2 Hızlı Komut Referansı

```powershell
# prompt dosyalarını kategoriye göre say (disk kanıtı)
Get-ChildItem .ai/ui-design/prompt -Directory | ForEach-Object {
  [PSCustomObject]@{ Kategori=$_.Name; Adet=(Get-ChildItem $_.FullName -Filter *.md).Count } }
# JSON geçerliliği (prompt bloğunu çıkarıp doğrula) + bölüm sırası
Select-String -Path <prompt-dosyası> -Pattern '^#{2,3} '
# yaz + doğrula
node .ai/scripts/vault-utf8-writer.mjs write --file <vault-yolu> --text "@<staging>"
node .ai/scripts/vault-utf8-writer.mjs verify --file <vault-yolu>
```

### 5.3 Mevcut Promptu Güncelleme (In-Place Refactoring)

| Durum | İzin | Koşul |
|-------|------|-------|
| Aynı dosyanın içeriği (prompt metni, token listesi, Validation) | ✅ İzinli | Dosya adı DEĞİŞMEZ; `version` bump + `updated` tarih + `log.md` append |
| Dosya adı / `component:` / `tier:` kimliği | ❌ Yasak | Onay gerekir (Rule #1 — file names NEVER change without approval) |
| Yeni alt kategori (`prompt/<yeni>/`) | ❌ Yasak | Registry'e `.templates/index.md` + `00-prompt-index.md` üzerinden karar verilir |
| Prompt'un ürettiği HTML/CSS kodu | Bu şablonun kapsamı dışı | Kod → `../frontend/js-template.md` / `../frontend/css-template.md` |

**Güncelleme adımları (5 adım):**
1. Vault'taki mevcut dosyayı `verify` ile kontrol et (bozuksa önce `repair`).
2. Değişikliği staging dosyasına uygula — dosyanın §3.3 bölüm yapısını KORU.
3. §6.1 kontrol listesini yeniden çalıştır (10 madde).
4. `write --file <mevcut yol> --text "@<staging>" --force` ile yaz; hemen ardından `verify`.
5. `version`/`updated` alanlarını bump'la ve değişikliği `.ai/log.md` append ile kaydet.

---

## 6. Doğrulama

### 6.1 Kontrol Listesi

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | `type: prompt`; `component:`/`tier:` kategoriye uygun; 7 registry zorunlu alan içeride |
| 2 | H1 kalıbı | §3.3 tablosundaki kategori kalıbı |
| 3 | Bölüm yapısı | `## AI Code Generation Prompt` + 6 H3 doğru ad/sıra |
| 4 | ASCII Reference | ≥4 durum; hizalı; dil etiketsiz blok |
| 5 | JSON | Geçerli; `task`/`kimlik`/`bem`/`states`/`tokens`/`constraints` 5-6 anahtar |
| 6 | Token/plan | `tokens` adları master dosyada; `bem` envanterle aynı |
| 7 | Expected Output | `html` + `css` iki blok; CSS'te yalnız `var(--token)`; `innerHTML` yok |
| 8 | Validation | `- [ ]` biçimi, ≥6 ölçülebilir madde |
| 9 | Halüsinasyon | Uydurma sayfa/bileşen/token yok; belirsizlik işaretli |
| 10 | Mojibake | `verify` → `hasBom=false, mojibake=0, cjk=0` |

### 6.2 Quality Report (şablonun kendisi)

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 6 |
| Last Updated | 2026-09-24 |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[../index]] | Şablon envanteri (DRY) |
| Vault anayasası | [[../../CLAUDE.md]] | Guardrail #3/#4/#10/#11/#16/#17 |
| Agent registry | [[../../AGENTS.md]] | §6 routing: prompt → UI Designer |
| Süreç | [[../../WORKFLOW.md]] | §8.1 Code Review |
| Prompt master indeks | [[../../ui-design/prompt/00-prompt-index]] | §7 Prompt Formatı, kategori tabloları |
| Bileşen envanteri | [[../../ui-design/02-component-inventory]] | C01-C16 BEM/piksel/token |
| Master token'lar | [[../../ui-design/tokens/design-tokens-master]] | `tokens[]` adları |
| Etkileşim durumları | [[../../ui-design/reference/09-interaction-states]] | mouse vs touch, hover capability |
| Cihaz matrisi | [[../../ui-design/00-device-matrix]] | tier/viewport |
| Flow şablonu | [[flow-template]] | Kalıp B |
| Screen spec şablonu | [[screen-spec-template]] | Kalıp D |
| JS şablonu | [[../frontend/js-template]] | Üretilen JS'in gerçek şablonu |
| CSS şablonu | [[../frontend/css-template]] | Üretilen CSS'in gerçek şablonu |

### 7.1 Alt Kategori → Mevcut Prompt Dosyaları (disk kanıtı, 2026-09-24)

| Alt kategori | Adet | Dosya adı deseni | H1 kalıbı |
|--------------|------|------------------|-----------|
| `component/` | 16 | `C01-nav-link` … `C16-network-row` | `<Ad> Component Prompt (Cxx)` |
| `page/` | 12 | `01-home` … `12-bluetooth` | `NN — <Page> Page` |
| `screen/` | 11 | `00-prompt-index` + `T1-phone` … `T10-watch` | `T<N>: <Device> Screen Prompt` |
| `layout/` | 10 | `01-mobile-stack` … `10-spatial-ar` | `<Pattern> Layout (<W>×<H>)` |
| **kök** | 1 | `00-prompt-index.md` | Master Prompt Index (§7 format) |
| **TOPLAM** | **50 md** | 4 alt kategori + kök indeks | — |

> ⚠️ **Truth Mode notu:** `00-prompt-index.md` "175 prompt" / "14 page" iddiası, `C02-search-bar.md`, `01-pattern-mobile-stack.md`, `page/02-library.md` adları diskte YOKTUR (gerçek: `C02-status-widget.md`, `01-mobile-stack.md`, `page/02-albums.md`). İndeks düzeltmesi `.ai/log.md` append ile kayda alınır.

### 7.2 Red Team — Sık Yapılan Hatalar

| # | Hata | Neden yanlış | Doğru |
|---|------|--------------|-------|
| 1 | `### Prompt Template` yerine düz metin prompt | Makine okunur JSON gerekir | §3.7 şeması |
| 2 | CSS'te `#ff4fd8` yazmak | Token-First (Guardrail #11/#17) | `var(--color-primary)` |
| 3 | Tek durumlu ASCII (`Default`) | 4+ durum zorunlu | §3.6 |
| 4 | `Hover`'ı watch/car tier'ında kullanmak | `09-interaction-states` — hover capability yok | `Pressed` |
| 5 | Validation maddesi "iyi görünür" | Ölçülebilir olmalı | `≥4.5:1`, `≥44px` vb. |
| 6 | Prompt'u kod dosyası gibi yazmak | Prompt beklenti verir, dosya `.templates`'te yaşar | Gerçek kod `../frontend/*` şablonlarıyla yazılır |

---

*UI Design Prompt Template (Kalıp C) v1.0.0 — CoreMusic Template System*
**Template Version:** 1.0.0
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
