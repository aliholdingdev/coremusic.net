---
reference_doc: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
title: "CoreMusic — UI Design Reference/Spec/Token Dokümanı Şablonu (Kalıp A)"
type: template
category: ui-design
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/.templates/ui-design/reference-template.md"
  source_of_truth: ".ai/ui-design/reference/legacy-inventory.md §Şablon kalıpları (Kalıp A) · .ai/.templates/index.md"
---

# CoreMusic — UI Design Reference/Spec/Token Dokümanı Şablonu (Kalıp A)

> **NE ZAMAN OKUNUR:** `.ai/ui-design/` altında **yeni bir referans, envanter, matris, plan, analiz, mimari veya token dokümanı** üretileceği/her güncelleneceği her görevde bu şablon **ZORUNLU** okunur — tipik görevler: `reference/NN-*.md`, `tokens/*.md`, kök çekirdek dosyalar (`00-device-matrix`, `01-mockup-index`, `02-component-inventory`, `03-implementation-plan`, `04-accessibility-gaps`, `05-responsive-architecture`), `flow/00-flow-index`, `prompt/00-prompt-index`, `screens/00-ascii-art-index` ve `reference/legacy-inventory` tipindeki indeks/envanter dosyaları. **Dosya yoksa veya metin Kalıp A'ya uymuyorsa üretim DURAR** (üretilecek dosya `.templates/ui-design/{flow,prompt,screen-spec}` şablonlarından birine ait değilse). Flow için `[[flow-template]]`, prompt için `[[prompt-template]]`, screen spec için `[[screen-spec-template]]` kullanılır — bu şablon onların kopyası DEĞİLDİR.

**Zorunlu Bağlantılar:** [[../index]] · [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../WORKFLOW.md]] · [[../../ui-design/reference/legacy-inventory]] · [[../frontend/css-template]]

**Kalıp kaynağı:** `.ai/ui-design/reference/legacy-inventory.md` → "Şablon kalıpları" → **Kalıp A** (114 md'nin 72'sinde `reference_doc:` frontmatter; ~26 dosya tam iskeleti uygular).

---

## 1. Amaç

Bu şablon, CoreMusic ui-design vault'unda **referans / envanter / matris / plan / analiz / mimari / token / index** tipindeki dokümanların tek dış yapısını (frontmatter 12 alan → `## 1. Amaç` → içerik bölümleri → `## N. Quality Report` → Authority footer) tanımlar. **Guardrail #16:** bu tipte yeni bir `.md` bu şablondan üretilmek ZORUNLUDUR; şablonsuz üretilen referans dosyası geçersiz sayılır ve revert edilir.

**Kapsadığı gerçek dosyalar (disk kanıtı, 2026-09-24):** kök 6 çekirdek md + `tokens/` 4 md + `reference/` 10 md + `legacy-inventory.md` + 4 indeks dosyası ≈ **25 doküman** bu kalıbı uygular.

| Karar | Kaynak | Şablona gömülü karşılığı |
|-------|--------|--------------------------|
| SSOT — bilgi yalnız `.ai/` vault'unda | Guardrail #5 | §4 #2, frontmatter `authority` |
| Zero Hallucination — doğrulanamayan `⚠️ VERIFICATION REQUIRED` | Guardrail #3 | §4 #4, §6 #7 |
| In-Place Refactoring — dosya adı/yolu değişmez | Guardrail #4 | §4 #5 |
| Template Mandatory | Guardrail #16 | §5 Workflow, §6 #1 |
| Mockup Before Frontend | Guardrail #11 | §4 #7 |
| Red Team · Human Mode · Truth Mode | Vault modu | frontmatter `governance` + footer `Mode` |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/ui-design/` kök çekirdek md (00-05), `tokens/*.md`, `reference/01-10.md`, `reference/legacy-inventory.md` | Flow dosyaları → `[[flow-template]]` (Kalıp B) |
| `flow/00-flow-index.md`, `prompt/00-prompt-index.md`, `screens/00-ascii-art-index.md` gibi indeks/maître dosyalar | Component/page/screen promptları → `[[prompt-template]]` (Kalıp C) |
| `.ai/.templates/ui-design/reference-template.md` (bu dosyanın kendisi) | Ekran spec dosyaları → `[[screen-spec-template]]` (Kalıp D) |
| Yeni token paleti, cihaz matrisi, envanter, uygulama planı, erişilebilirlik analizi | ADR'ler → `.ai/.templates/adr/adr-template.md` |

- **Kullananlar:** UI Designer (birincil), Vault Steward/MO (registry + indeks), QA Engineer (erişilebilirlik/matris doğrulaması).
- **Katman:** L3 (sunum) dokümantasyonu — kod katmanına (L0/L1/L2) dokunmaz.
- **Ön koşul:** İçerik iddiaları disk kanıtıyla (glob/grep/read) doğrulanmıştır; doğrulanamayan satır `⚠️ VERIFICATION REQUIRED` taşır.

---

## 3. Mimari

Şablonun gövdesi: 12 alanlı frontmatter, numaralı bölüm kalıbı, Quality Report ve Authority footer. Alt bölümler Kalıp A'nın gerçek uygulanışını örnekler.

### 3.1 Frontmatter — 12 Alan (zorunlu sıra)

| # | Alan | Tip | Zorunlu | Açıklama / Örnek değer |
|---|------|-----|---------|------------------------|
| 1 | `reference_doc` | string | ✅ | `"CoreMusic UI Design System"` — indeks dosyalarında: `"Freelancer Technical Documentation v1.0"` |
| 2 | `title` | string | ✅ | `"CoreMusic — <Başlık>"` — H1 ile birebir aynı metin |
| 3 | `type` | enum | ✅ | `matrix` \| `index` \| `inventory` \| `plan` \| `analysis` \| `architecture` \| `tokens` \| `reference` \| `spec` |
| 4 | `category` | string | ✅ | Bu dosyalarda hep `ui-design` |
| 5 | `date` | date | ✅ | `YYYY-MM-DD` — ilk üretim tarihi |
| 6 | `updated` | date | ✅ | `YYYY-MM-DD` — son içerik değişikliği (`date`'ten küçük OLAMAZ) |
| 7 | `status` | enum | ✅ | `active` \| `draft` \| `deprecated` |
| 8 | `version` | semver | ✅ | `X.Y.Z` — §6 Quality Report `Version` değeriyle BİREBİR aynı olmalı |
| 9 | `authority` | string | ✅ | Çıktı dokümanda: `Single Source of Truth (SSOT)` · **bu şablonda:** `Template (Guardrail #16) — Registry: .ai/.templates/index.md` |
| 10 | `governance` | string | ✅ | `Red Team · Human Mode · Truth Mode` |
| 11 | `reference.authority` | path | ✅ | Dosyanın kendi vault yolu: `".ai/ui-design/<göreli yol>"` |
| 12 | `reference.source_of_truth` | path list | ✅ | `"<yol> · <yol>"` — içerikte dayanılan birincil kaynaklar (PNG klasörü, master token dosyası vb.) |

> **7 zorunlu alan uyarlaması (registry §4 #4):** bu 12 alanın içeriğinde registry'nin 7 zorunlu alanının tamamı (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) bulunur — alan eksiltilemez.

### 3.2 Örnek — Doldurulmuş Frontmatter (çıktı dokümanı)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Master Design Tokens"
type: tokens
category: ui-design
date: 2026-09-20
updated: 2026-09-24
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/tokens/design-tokens-master.md"
  source_of_truth: ".ai/.png/home-1024/ · .ai/ui-design/tokens/color-palettes.md"
---
```

**Boş iskelet (kopyalanır, `{{...}}` doldurulur):**

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — {{TITLE}}"
type: {{matrix|index|inventory|plan|analysis|architecture|tokens|reference|spec}}
category: ui-design
date: {{YYYY-MM-DD}}
updated: {{YYYY-MM-DD}}
status: active
version: {{X.Y.Z}}
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/{{göreli/yol.md}}"
  source_of_truth: "{{kaynak/yol}} · {{kaynak/yol}}"
---
```

### 3.3 Bölüm Kalıbı (H1 → §1 Amaç → içerik → §N Quality Report)

````markdown
# CoreMusic — {{TITLE}}

**Zorunlu Bağlantılar:** [[link]] · [[link]]      # alt klasörde ../ öneki

---
## 1. Amaç
---
## 2. <İçerik (tablo / kod bloğu)>
---
## 3. <İçerik>
...
---
## N. Quality Report
---
**Authority:** Bayram Ali / Vault Steward
**Last Updated:** YYYY-MM-DD
**Mode:** Red Team · Human Mode · Truth Mode
````

**Numaralandırma kuralları:**
1. Bölüm başlıkları daima `## N. Başlık` biçimindedir; `###` en fazla 2 seviye derinlikte kullanılır.
2. `## 1. Amaç` **her dosyada zorunlu ve her zaman birinci bölümdür** — atlanamaz, taşınamaz.
3. İçerik bölümleri (§2…§N-1) dosyanın konusuna göre değişir; her biri ya bir tablo ya bir kod/ASCII bloğu ya da kısa madde listesi taşır.
4. `## N. Quality Report` **dosyanın son H2'sidir**; ardından yalnızca `---` ve Authority footer gelir.
5. Bölüm sayısının sabiti yoktur (gerçek dosyalarda 5-8 arası değişir); sabit olan §1 ve §N = Quality Report'tur.

### 3.4 Quality Report kalıbı

```markdown
## N. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | X.Y.Z |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | n |
| Last Updated | YYYY-MM-DD |
```

**Zorunlu 4 satır:** `Version` (frontmatter ile aynı), `Status`, `Cross References` (dosyadaki wiki-link sayısı — sayım diskten), `Last Updated` (frontmatter `updated` ile aynı). Konuya özgü satır eklenebilir (ör. token dosyalarında `Token Count`, matrislerde `Tier Count`, envanterlerde `Component Count`) ama 4 zorunlu satır SİLİNEMEZ.

### 3.5 Authority footer kalıbı

```markdown
---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** YYYY-MM-DD
**Mode:** Red Team · Human Mode · Truth Mode
```

Footer `**Authority:**` satırıyla başlar; `Mode` satırı governance değeriyle aynı üçlüyü taşır. Dosya sonu boş satır ile kapanır.

### 3.6 Örnek — tamamlanmış §1 Amaç + Quality Report (gerçek dosyadan kalıp)

```markdown
## 1. Amaç

CoreMusic UI tasarımının **görsel referanslarının tek indeksidir**. 19 PNG mockup
dosyası, tüm frontend geliştirme görevlerinde tartışmasız başlangıç noktasıdır.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili
> görsel okunmadan kod yazılamaz. Görsel okunamıyorsa DUR ve kullanıcıya bildir.

...

## 8. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 5.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 3 |
| Last Updated | 2026-09-20 |
```

### 3.7 Zorunlu Bağlantılar satırı

H1'in hemen altına tek satır olarak yazılır: `**Zorunlu Bağlantılar:** [[a]] · [[b]]`. Alt klasördeki dosyalarda göreli öneki `../` ile başlar (ör. `[[../index]]`, `[[../../ui-design/01-mockup-index]]`). En az 2, en fazla 6 bağlantı; hepsi diskte VAR olan hedeflere yönelmelidir (kırmızı bağlantıyı düzelt, uydurma).

### 3.8 `type` → Önerilen İçerik Bölüm Seti

Kalıp A'da §2..§N-1 serbesttir; yine de her `type` için diskte kanıtlanmış bölüm seti aşağıdadır. Yeni bir referans dosyası bu tablodaki setle açılır, konuya göre § ara bölümler eklenir.

| `type` | Kullanım alanı | Önerilen §2..§N-1 seti (gerçek dosya kanıtı) |
|--------|----------------|-----------------------------------------------|
| `matrix` | Cihaz/tier matrisi | `2. Device Tier Sistemi` · `3. Tüm Tiers` · `3A. Viewport Bazlı Özet Tablosu` · `4. Cihaz Tespit Önceliği` (`00-device-matrix.md`, 315 satır) |
| `index` | Mockup/PNG indeksi | `2. PNG Dizin Yapısı` · `3. Mockup Kategorileri (3.1-3.3)` · `4. Screen Spec Eşleştirmesi` · `5. Referans Sıralaması` · `6. Kullanım Protokolü` · `7. Quick Reference (qr-) Sistemi` (`01-mockup-index.md`, 183 satır) |
| `inventory` | Bileşen envanteri | `2. Bileşen Envanteri` (C01-C19 tek tek BEM/piksel/token tabloları) (`02-component-inventory.md`, 239 satır) |
| `plan` | Uygulama planı | `2. 15 Adımlık CSS Planı` · `3. Bağımlılık Grafisi` · `4. Toplam Süre Tahmini` (`03-implementation-plan.md`, 184 satır) |
| `analysis` | Erişilebilirlik/analiz | `2. Touch Target Analizi` · `3. Contrast Analizi` · `4. Keyboard Navigation` · `5. Screen Reader Desteği` · `6. Reduced Motion` · `7. Focus Management` (`04-accessibility-gaps.md`, 210 satır) |
| `architecture` | Responsive/CSS mimarisi | `2. Temel İlkeler` · `3. Token Hiyerarşisi` · `4. CSS Dosya Yapısı` · `5. Token-First CSS Örneği` · `6. Media Query Stratejisi` · `7. Yasak Örüntüleri` (`05-responsive-architecture.md`, 198 satır) |
| `tokens` | Token kümeleri | `2. Renk Paleti` / `2. Component Tokens` / `2. Platform Tokens` + kullanım tabloları (`tokens/*.md`, 4 dosya) |
| `reference` | Backend/frontend/metin referansı | Konuya özgü tablolar + örnek bloklar (`reference/01-10.md`, 10 dosya) |
| `spec` | Ekran spec'i | ⚠️ Bu tip için `[[screen-spec-template]]` (Kalıp D) kullanılır — bu şablon değil |
| `flow` | Akış | ⚠️ `[[flow-template]]` (Kalıp B) |
| `prompt` | AI üretim promptu | ⚠️ `[[prompt-template]]` (Kalıp C) |

**İstisna kuralı:** `type` alanı `flow`, `prompt` veya `spec` ise dosya bu şablona girmez; ilgili şablonla yeniden üretilir.

### 3.9 Bölüm İçi İçerik Kalıpları

Her içerik bölümü aşağıdaki dört biçimden birini kullanır; karma (hem tablo hem blok) serbesttir ama **saf düz metin paragraf yığını yasaktır** — referans dokümanı okunur, hikâye anlatmaz.

**(a) Karar/ölçü tablosu** — en sık kullanılan biçim:

```markdown
## 2. Device Tier Sistemi

| Tier | Viewport | Cihaz sınıfı | Ölçü önceliği |
|------|----------|--------------|---------------|
| T08  | 1024×600 | RPi5 7" touch | Touch 44px |
| T17  | 1920×1080 | Desktop monitor | Hover 40px |
```

**(b) Kod/ASCII bloğu** — yapı, hiyerarşi, layout:

```markdown
## 2. PNG Dizin Yapısı

.ai/.png/
├── home-1024/          ← 12 PNG (RPi5 1024×600 Embedded)
├── home-1920/          ← 1 PNG (1920×1080 desktop)
└── shared-1024/        ← 6 PNG (auth ekranları)
```

**(c) Sıralı madde listesi** — protokol/adım:

```markdown
## 6. Kullanım Protokolü

1. İlgili ekranın PNG'sini aç.
2. ASCII art ile piksel ölçüyü karşılaştır.
3. Çelişki varsa PNG kazanır; kodu düzelt.
```

**(d) Uyarı blockquote** — gate / zerokörlük:

```markdown
> **⚠️ Mockup Before Frontend:** ... okunmadan kod yazılamaz. Okunamıyorsa DUR.
```

**Bölüm kapanış kuralı:** her `##` bölümünün en az bir somut öğesi (tablo satırı, kod satırı, madde) olmalıdır; yalnızca başlık + jenerik cümleden oluşan bölüm §6 #5 ihlalidir.

### 3.10 Çapraz Referans Sayımı (Quality Report `Cross References`)

`Cross References` değeri **dosyanın kendisindeki** wiki-link sayısıdır; diskten şu komutla alınır:

```powershell
(Select-String -Path '<dosya>' -Pattern '\[\[[^\]]+\]\]' -AllMatches |
  ForEach-Object { $_.Matches.Count } | Measure-Object -Sum).Sum
```

`Zorunlu Bağlantılar` satırındaki bağlantılar da bu sayıya dahildir. Düz metin `yol.md` atıfları sayılmaz (onlar `Referanslar` tablosu içeriğidir).

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Guardrail #16 — bu şablonsuz referans/envanter/matris/token dosyası üretilmez | Dosya geçersiz; revert |
| 2 | Guardrail #5 — SSOT; bilgi yalnız `.ai/` vault'undan, harici kopya yapılmaz | Harici bilgi reddedilir |
| 3 | Guardrail #3 — doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` ile işaretlenir | İçerik silinir |
| 4 | Guardrail #4 — dosya adı/yolu değiştirilemez (In-Place Refactoring) | Dosya geri yüklenir |
| 5 | Frontmatter 12 alan eksiksiz; `authority` bu şablonda registry değeridir | Dosya geçersiz |
| 6 | `## 1. Amaç` + `## N. Quality Report` + Authority footer silinemez | Dosya geçersiz |
| 7 | Guardrail #11 — içeriğe dayanan frontend/kod kararı için `.ai/ui-design/` görseli okunmadan üretilmez | Kod derhal revert + CRITICAL log |
| 8 | `version` ↔ Quality Report `Version` ↔ footer sayısı tutarlı; uyuşmazlık yasak | Düzeltme zorunlu |
| 9 | Yasak: secret/token/`figd_...` gibi kimlik bilgisi bu dosyalara yazılmaz | `[REDACTED]` + log |

### 4.2 Ek Kurallar

- **Zorunlu:** her sayım (`19 PNG`, `C01-C16`, `45 tier`, `N md`) glob/grep ile diskten doğrulanır; tahmin kullanılmaz.
- **Zorunlu:** Türkçe içerik UTF-8 (BOM'suz) yazılır; yazım `.ai/scripts/vault-utf8-writer.mjs` üzerinden yapılır.
- **Zorunlu:** `.ai/ui-design/**` altındaki dosyalar bu şablonla DEĞİL, ilgili agent'ın domainiyle güncellenir — bu şablon yalnız `.templates/ui-design/` içinde yaşar.
- **Yasak:** §1 Amaç'ı pazarlama metnine çevirmek; Amaç = "bu dosya neye tek otoritedir" cümlesidir.
- **Yasak:** Quality Report içine diskte doğrulanmayan sayı koymak.
- **Yasak:** Wiki-link'i düz metinle (`` `yol` ``) Zorunlu Bağlantılar satırına yazmak — o satırda biçim `[[...]]`'dir.

### 4.3 Doğrulama Seviyeleri (Truth Mode)

Her içerik satırı aşağıdaki üç seviyeden birine işaretlenir; işaretsiz satır kabul edilmez:

| Seviye | Koşul | İşaret | Aksiyon |
|--------|-------|--------|---------|
| **Kanıtlı** | `glob`/`grep`/`read` ile diskte görüldü | — (işaret yok) | Yazılır |
| **Belirsiz** | Kaynak var ama içerik okunamadı/silinmiş | `⚠️ VERIFICATION REQUIRED` | Yazılır + işaretlenir, sayısal iddia yapılmaz |
| **Yasaklı** | Secret, token, parola, `figd_...`, `.env` içeriği | — | Hiç yazılmaz; `REDACTED` |

**Gerçek örnekteki tutarsızlık işleme kuralı (Truth Mode):** diskteki bir dosya kendi içinde çelişiyorsa (ör. frontmatter `version: 5.0.0` ama Quality Report `Version 6.0.0`, ya da tek satırda mojibake) dosya **sessizce düzeltilmez** — çelişki ya da not satırı olarak yazılır ve vault steward'a bildirilir. İstisna: mojibake onarımı `.ai/scripts/vault-utf8-writer.mjs repair` ile yapılır ve `log.md`'ye append edilir.

### 4.4 Dosya Adı ve Yerleşim Kalıbı

| Dosya tipi | Yerleşim | Ad kalıbı |
|------------|----------|-----------|
| Çekirdek matris/indeks/envanter/plan/analiz/mimari | `.ai/ui-design/` kökü | `NN-<kebab-name>.md` (00-05 arası sıralı) |
| Token seti | `.ai/ui-design/tokens/` | `<kebab-name>.md` + `.json` (varsa) |
| Kategori referansı | `.ai/ui-design/reference/` | `NN-<kebab-name>.md` (01-10 arası) |
| Kırık-envanter raporu | `.ai/ui-design/reference/` | `legacy-inventory.md` |
| İndeks/maître | ilgili klasör kökü | `00-<name>-index.md` |
| Bu şablon | `.ai/.templates/ui-design/` | `reference-template.md` |

**Yerleştirme kuralı:** aynı klasörde ikinci eşdeğer dosya açılmaz; sıradaki `NN` diskteki en büyük `NN`+1'dir. Dosya ADI değişmez (In-Place Refactoring) — içerik yerinde düzeltilir.

---

## 5. Workflow

```text
KANIT TOPLA → ŞABLONU SEÇ → KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16/§6 DOĞRULA → VAULT-YAZ → COMMIT
```

| # | Adım | Aksiyon | Çıktı |
|---|------|---------|-------|
| 1 | Kanıt topla | Hedef dosya için glob/grep/read; mevcut sürüm var mı? | Disk kanıtı |
| 2 | Şablonu seç | Bu dosya (Kalıp A) mı, yoksa `[[flow-template]]` / `[[prompt-template]]` / `[[screen-spec-template]]` mi? | Karar |
| 3 | Kopyala | Boş iskeleti (§3.2) panoya/staging'e al | İskelet |
| 4 | Doldur | `{{TITLE}}`, `{{type}}`, tarih, `version`, `reference.*`, §1 Amaç, içerik §2..§N-1 | İçerik |
| 5 | Quality Report | §3.4 kalıbı; `Cross References` sayımı diskten | §N |
| 6 | Doğrula | §6 kontrol listesi + registry `.templates/index.md` satır ekleme | Rapor |
| 7 | Vault'a yaz | `node .ai/scripts/vault-utf8-writer.mjs write --file <vault> --text @<staging>` + `verify` | Yazılı dosya |
| 8 | Registry + log | `.templates/index.md` tabloya ekle; `.ai/log.md` append (parent birleştirme) | Kayıt |

### 5.1 Adım Bazlı Hata Modları

| Adım | Tipik hata | Belirti | Düzeltme |
|------|-----------|---------|----------|
| 1 | Kanıt yok | Hedef dosya diskte yok | Yeni dosya ise sıradaki `NN` ile aç; `date` = bugün |
| 2 | Yanlış şablon | Flow/screen/prompt dosyası Kalıp A ile üretilmiş | İlgili şablona (`[[flow-template]]` vb.) göre yeniden yaz |
| 3 | Eksik frontmatter | `reference.*` bloğu yok ya da 12 alan eksik | §3.1 tablosundan tamamla; `updated` ≥ `date` |
| 4 | Kayıpsız bölüm | `## 1. Amaç` veya Quality Report yok | §3.3 kalıbına geri dön, ekle |
| 5 | Sayı uydurma | `19 PNG` / `45 tier` diskteki glob ile tutmuyor | Glob/grep ile yeniden say; uyuşmuyorsa yazma, `⚠️ VERIFICATION REQUIRED` koy |
| 6 | Kırık wiki-link | `[[...]]` hedefi yok | Hedefi diskte bul; yoksa linki kaldır, düz `yol` olarak `Referanslar` tablosuna taşı |
| 7 | Encoding bozulması | `verify` → `mojibake > 0` veya `hasBom: true` | `repair` modu (yedek alır); sonra `verify` temizlenene kadar tekrarla |
| 8 | Registry senkronsuz | `.templates/index.md` `total_*` sayıları değişmedi | §7.1.x tablosuna satır ekle + `total_*`/`total_lines` tazele |

### 5.2 Hızlı Komut Referansı

```powershell
# şablonu vault'a yaz (tek izinli yazım yolu)
node .ai/scripts/vault-utf8-writer.mjs write --file <vault-yolu> --text "@<staging-yolu>"
# doğrula
node .ai/scripts/vault-utf8-writer.mjs verify --file <vault-yolu>
# mojibake taraması
node .ai/scripts/vault-utf8-writer.mjs scan --dir .ai/ui-design
# satır/kalıp sayımı (salt-okunur)
(Get-Content <vault-yolu>).Count
Select-String -Path <vault-yolu> -Pattern '^## '
```

---

## 6. Doğrulama

### 6.1 Kontrol Listesi

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 12 alan var; 7 registry zorunlu alan içeriğinde mevcut |
| 2 | `## 1. Amaç` | Mevcut ve ilk H2 |
| 3 | Quality Report | Son H2; `Version`/`Status`/`Cross References`/`Last Updated` 4/4 |
| 4 | Authority footer | `Authority` + `Last Updated` + `Mode` 3/3 |
| 5 | Bölüm numarası | `## N.` deseni kırılmamış, §2..§N-1 içerik dolu (boş başlık yok) |
| 6 | Wiki-link | Her `[[...]]` hedefi diskte mevcut |
| 7 | Halüsinasyon | Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`; uydurma sayı yok |
| 8 | Mojibake | `vault-utf8-writer verify` temiz (BOM=0, mojibake=0, CJK=0) |
| 9 | Registry | `.templates/index.md` §7.1.x tablosunda bu şablon satırı var |
| 10 | Yasaklı içerik | `figd_...`, parola, API anahtarı yok |

### 6.2 Quality Report (şablonun kendisi)

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | 7 |
| Last Updated | 2026-09-24 |

### 6.3 Otomatik Doğrulama Komutları

```powershell
# 1) Frontmatter 12 alan + 7 registry zorunlu alan
Select-String -Path <dosya> -Pattern '^(reference_doc|title|type|category|date|updated|status|version|authority|governance):' |
  Measure-Object | Select-Object -ExpandProperty Count      # hedef: >= 10 (+ reference: bloğu 2)

# 2) §1 Amaç ve Quality Report var mı
Select-String -Path <dosya> -Pattern '^## 1\. Amaç$'        # 1 eşleşme
Select-String -Path <dosya> -Pattern 'Quality Report'        # >= 1
Select-String -Path <dosya> -Pattern '^\*\*Authority:\*\*'   # 1 eşleşme

# 3) Wiki-link hedefleri diskte mi (örnek)
Select-String -Path <dosya> -Pattern '\[\[([^\]]+)\]\]' -AllMatches |
  ForEach-Object { $_.Matches } | ForEach-Object { $_.Groups[1].Value }

# 4) Encoding
node .ai/scripts/vault-utf8-writer.mjs verify --file <dosya>   # hasBom=false, mojibake=0, cjk=0
```

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[../index]] | Şablon envanteri (DRY — burada tekrarlanmaz) |
| Vault anayasası | [[../../CLAUDE.md]] | Guardrail #3/#4/#5/#11/#16 |
| Agent registry | [[../../AGENTS.md]] | §6 routing: ui-design → UI Designer |
| Süreç | [[../../WORKFLOW.md]] | §8.1 Code Review + UI Design Gate |
| Kalıp kaynağı | [[../../ui-design/reference/legacy-inventory]] | §Şablon kalıpları — Kalıp A/B/C/D |
| Flow şablonu | [[flow-template]] | Kalıp B |
| Prompt şablonu | [[prompt-template]] | Kalıp C |
| Screen spec şablonu | [[screen-spec-template]] | Kalıp D |
| CSS şablonu | [[../frontend/css-template]] | ITCSS katman hizası |

### 7.1 Kopyala-Yapıştır — Tam Kalıp A İskeleti

Aşağıdaki blok yeni bir ui-design referans dosyasının baştan sona iskeletidir; `{{...}}` alanları doldurulur, §2..§N-1 bölümü §3.8 tablosundaki `type` setine göre seçilir.

````markdown
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — {{TITLE}}"
type: {{TYPE}}
category: ui-design
date: {{YYYY-MM-DD}}
updated: {{YYYY-MM-DD}}
status: active
version: {{X.Y.Z}}
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/{{göreli/yol.md}}"
  source_of_truth: "{{kaynak}} · {{kaynak}}"
---

# CoreMusic — {{TITLE}}

**Zorunlu Bağlantılar:** [[{{hedef-1}}]] · [[{{hedef-2}}]]

---

## 1. Amaç

{{Bu dosya neye TEK otoritedir — 1-3 cümle. Ardından varsa gate blockquote.}}

---

## 2. {{İÇERİK_BÖLÜMÜ_1}}

{{tablo / kod bloğu / sıralı liste — §3.9}}

---

## 3. {{İÇERİK_BÖLÜMÜ_2}}

{{...}}

---

## {{N}}. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | {{X.Y.Z}} |
| Status | Red Team · Human Mode · Truth Mode verified |
| Cross References | {{sayı}} |
| Last Updated | {{YYYY-MM-DD}} |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** {{YYYY-MM-DD}}
**Mode:** Red Team · Human Mode · Truth Mode
````

---

*UI Design Reference Template (Kalıp A) v1.0.0 — CoreMusic Template System*
**Template Version:** 1.0.0
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
