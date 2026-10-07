---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — UI Verification Protocol"
type: protocol
category: ui-design
date: 2026-09-20
updated: 2026-09-30
status: active
version: 3.1.4
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/reference/04-verification.md"
  source_of_truth: ".ai/ui-design/01-mockup-index.md · .ai/ui-design/02-component-inventory.md"
---

# CoreMusic — UI Verification Protocol

**Zorunlu Bağlantılar:** [[ui-design/01-mockup-index]] · [[ui-design/02-component-inventory]] · [[ui-design/04-accessibility-gaps]]

---

## 1. Amaç

Her frontend görevinden sonra uygulanacak **UI doğrulama protokolüdür**. PNG karşılaştırma, tier bazlı validasyon ve kalite kontrol adımları burada tanımlanır.

---

## 2. Doğrulama Adımları

### Adım 1: PNG Karşılaştırma

```
1. İlgili PNG mockup'ı aç (.ai/.png/)
2. Kod çıktısını tarayıcıda göster
3. Piksel düzeyinde karşılaştır:
   - Header yüksekliği
   - Footer yüksekliği
   - İçerik padding'i
   - Kart boyutları
   - Boşluklar
4. Fark varsa düzelt
```

### Adım 2: Tier Validasyonu

```
1. Hedef tier'ı belirle (phone/embedded/laptop/desktop/4k/tv)
2. Token değerlerini kontrol et:
   --cm-header-h
   --cm-footer-h
   --cm-sidebar-w
   --cm-touch-target
   --cm-font-scale
3. Media query'inin doğru çalıştığını doğrula
4. Cihaz CSS override'ının sadece behavioral olduğunu kontrol et
```

### Adım 3: BEM Kontrolü

```
1. Tüm sınıflar BEM formatında mı?
   .block__element--modifier
2. Namespace korunuyor mu?
   .cm-card, .cm-btn, .cm-input
3. Hardcoded stil var mı?
   ❌ margin: 16px → ✅ margin: var(--cm-space-4)
```

### Adım 4: Token Kullanım Kontrolü

```
1. Hardcoded renk var mı?
   ❌ color: #ffffff → ✅ color: var(--cm-text-primary)
2. Hardcoded boyut var mı?
   ❌ width: 240px → ✅ width: var(--cm-sidebar-w)
3. Hardcoded radius var mı?
   ❌ border-radius: 12px → ✅ border-radius: var(--cm-radius-lg)
```

### Adım 5: Erişilebilirlik Kontrolü

```
1. Touch target ≥ 44px (phone/embedded)
2. Focus visible görünür mü?
3. Contrast oranı ≥ 4.5:1 (text)
4. Screen reader uyumlu mu? (aria-label)
5. prefers-reduced-motion destekleniyor mu?
```

### Adım 6: Cross-Browser Kontrol

```
1. Chrome (latest) ✅
2. Firefox (latest) ✅
3. Safari (latest) ✅
4. Edge (latest) ✅
5. Samsung Internet (mobile) ✅
```

---

## 3. Doğrulama Matrisi

| Tier | PNG Referans | Token Doğrulama | Touch Target | Contrast |
|------|-------------|-----------------|--------------|----------|
| Phone | T01-T04 | ≤767px tokens | 48px ✅ | 4.5:1 ✅ |
| Embedded | T07-T08 | 1024×600 tokens | 48px ✅ | 4.5:1 ✅ |
| Laptop | T09-T10 | 1366-1920px tokens | 32px ✅ | 4.5:1 ✅ |
| Desktop | T11-T12 | 2560-3840px tokens | 28px ✅ | 4.5:1 ✅ |
| 4K | T12 | ≥3840px tokens | 24px ✅ | 4.5:1 ✅ |
| TV | T15-T18 | TV UA tokens | 80-120px ✅ | 4.5:1 ✅ |

---

## 4. Red Flags (Düzeltme Gerekli)

| Flag | Açıklama | Aksiyon |
|------|----------|---------|
| Hardcoded value | CSS'de hardcoded pixel/renk | Token'a çevir |
| Missing BEM | ClassName BEM formatında değil | BEM'e çevir |
| Missing token | Token tanımlı ama kullanılmamış | Token'ı ekle |
| Low contrast | Text contrast < 4.5:1 | Renk düzelt |
| Small touch | Touch target < 44px | Büyüt |
| No focus ring | Focus visible görünmüyor | Focus ring ekle |
| No reduced motion | prefers-reduced-motion destek yok | Ekle |

---

## 5. Faz Tablosu ve Commit Kanıtları

`.ai/ui-design` yeniden inşasının faz kaydı. Her hash bu denetimde `git cat-file -e <hash>` ile **varlık**, `git log --format='%h %s' -1 <hash>` ile **subject** olarak ölçülmüştür (7/7 başarılı, 2026-09-29).

| Faz | İçerik | Commit | Ölçüm |
|---|---|---|---|
| 0 | baseline + `.env.figma` gitignore | `cdd5665` | ✅ doğrulandı |
| 1+2 | Figma 15 sayfa + token üretimi | `f02d02b` | ✅ doğrulandı |
| 3 | 5 hata düzeltmesi (CatID + şablon disk gerçeği) | `04764f9` | ✅ doğrulandı |
| 4 | root 6 md + PNG 149/151 + env SSOT *(commit başlığı alıntısı — düzeltme: Faz 8b, §7.1)* | `21f357d` | ✅ doğrulandı |
| 5 | screens Kalıp D (13 dosya) | `1042cf4` | ✅ doğrulandı |
| 6 | reference+flow+prompt Kalıp A/B/C (70 dosya) | `02a98b9` | ✅ doğrulandı |
| 7 | guardrail 6 dosya | `546e983` | ✅ doğrulandı |
| 8 | **bu denetim (QA kapısı)** | — | commit **orkestratörde** — subagent atmaz |

---

## 6. Kapı Çıktıları (Gerçek Ölçüm — 2026-09-29, repo kökünden)

| # | Kapı | Beklenen | Gerçek çıktı | Durum |
|---|------|----------|--------------|-------|
| 1 | `kalip-abc-check.ps1` | A:0 B:0 C:0 | `A:0 B:0 C:0  (0/0/0 = GECTI)` · A **21**/0 · B **21**/0 · C **51**/0 | ✅ |
| 2 | `screens-frontmatter-check.ps1` | 21 / 0 | `dosya: 21 \| sorunlu: 0` | ✅ |
| 3 | `wiki-link-check.ps1` | 227 link · gerçek kırık 0 | `kontrol edilen link: 227` · `kirik link: 6` · 6'sı da yanlış pozitif (§6.3) | ✅ |
| 4 | `device-matrix-catid.ps1` | CatID sütunu · T07 çözümlü | `Tablo: 0 \| CatID eklenen satir: 0` + belge ölçümü §6.4 | ✅ (açıklama §6.4) |
| 5 | `figma-tokens.ps1` | 7 breakpoint · **kapı: `DURUM` + `SONUC` + exit kodu** | `SONUC -> toplam=7 \| pass=5 \| bos=2 \| fail=0 \| bos_bp=3840,tv` + `BITTI` · **exit 0** (§6.5, Faz 9 ölçümü) | ✅ |

### 6.1 `kalip-abc-check.ps1`

```
===== KALIP A (reference / root md / tokens) =====
  A: toplam 21 | sorunlu 0

===== KALIP B (flow) =====
  B: toplam 21 | sorunlu 0

===== KALIP C (prompt) =====
  C: toplam 51 | sorunlu 0

SONUC -> A:0 B:0 C:0  (0/0/0 = GECTI)
```

A kapsamı betikten okundu: kök `*.md` (6) + `reference/` üst düzey (11) + `tokens/*.md` (4) = **21** — `reference/figma/` alt dizini (6 md) A'ya dahil değildir. **Bu dosya A kapsamındadır ve `sorunlu 0` içinde kalır.**

### 6.2 `screens-frontmatter-check.ps1`

```
dosya: 21 | sorunlu: 0
```

### 6.3 `wiki-link-check.ps1`

```
kontrol edilen link: 227
kirik link: 6
```

> **Alıntı notu:** Betik 6 detay satırını da döndürür (`<dosya> -> <hedef>` biçiminde). O satırları olduğu gibi bu rapora yazmak, hedefin çift köşeli parantez sarmalayıcısı sayacı yapay şişirir (ölçüldü: **227 → 238 link**, **6 → 17 kırık** — ikisi de sahte). Bu yüzden **sarmalayıcı yazılmadı**, hedef tek sözcük olarak ve satır numarasıyla verildi; sayılar betiğin kendi çıktısıdır.

6 kaydın tamamı **dosya-var + şablon değişkeni/ASCII art** olarak tek tek ölçüldü — gerçek kırık **0**; **hiçbiri değiştirilmez**:

| Kaynak dosya | Satır | Hedef (sarmalayıcısız) | Doğalama |
|---|---|---|---|
| `flow/auth/01-login.md` | 98 | `C` | ASCII ikon satırı |
| `flow/auth/04-select-gender.md` | 99 | `V` | renk/ton tablosu sembolü |
| `flow/music/01-playback.md` | 141 | `V` | oynatıcı ASCII art |
| `flow/settings/04-general.md` | 114 | `V` | ASCII art |
| `reference/legacy-inventory.md` | 225 | `link` ×2 | kaçış dizgesi (2 rapor) |

5 hedef dosyanın `Test-Path` sonucu: **True / True / True / True / True**.

### 6.4 `device-matrix-catid.ps1` + belge ölçümü

Betik gerçek çıktısı: `Tablo: 0 | CatID eklenen satir: 0`. **Neden 0:** `00-device-matrix.md` birinci satırı `---` (frontmatter) olduğu için "zaten var" atlanma kontrolü (betik L35) tetiklenmiyor; tablo başlığı ise `| Tier |` değil `| CatID | Tier …` olduğundan (L57 regex) hiç eşleşmiyor. Dosya yeniden yazıldı, içerik **bayt olarak değişmedi** → `git status` temiz. Yani betik bu durumda **no-op**; CatID varlığı belgeden ölçüldü:

| Ölçüm | Değer |
|---|---|
| `CatID` başlık satırı | **15** |
| CatID veri satırı (`\| XX-Tnn \|`) | **82** |
| §2.1 kanonik CatID öneki | **11** (PH · TB · LP · DM · TV · AU · WS · CS · DA · MO · WB) |
| `EM-T07` geçen satır | **10** |
| Farklı önek altında tekrar eden tier | **T07 · T08 · T30** (çözüm CatID ile — §2.2, §3B.4 kayıtlı) |
| §3 tablolarında görülen önek | **12** = 11 kanonik − WB + **EM** + **AR** (Tablet alt kümesi `EM-T07/T08`, tarihsel AR/VR satırı `AR-T30`) |

**T07 çakışması çözümlü:** `EM-T07` ↔ `TB-T07` ayrımı §2.2 tablosunda ve §3B.4 kaydında; `screens/T07-embedded/` dizin adı korunmuş.

### 6.5 `figma-tokens.ps1`

```
mobile -> tokens-mobile.json | node=40 | c=21 t=7 s=2 r=11 sp=2 sz=35 | korunan eski=0 | DURUM: PASS
1024 -> tokens-1024.json | node=13901 | c=3678 t=777 s=866 r=1364 sp=117 sz=8228 | korunan eski=340 | DURUM: PASS
3840 -> tokens-3840.json | node=1 | c=0 t=0 s=0 r=0 sp=0 sz=0 | korunan eski=0 | BOS: tasarim yok | DURUM: BOS
plan -> tokens-plan.json | node=2619 | c=1212 t=172 s=304 r=234 sp=10 sz=1982 | korunan eski=0 | DURUM: PASS
tv -> tokens-tv.json | node=1 | c=0 t=0 s=0 r=0 sp=0 sz=0 | korunan eski=0 | BOS: tasarim yok | DURUM: BOS
system -> tokens-system.json | node=21768 | c=9492 t=724 s=401 r=360 sp=14 sz=17804 | korunan eski=0 | DURUM: PASS
1920 -> tokens-1920.json | node=25979 | c=7220 t=3040 s=1517 r=2115 sp=191 sz=17855 | korunan eski=48 | DURUM: PASS

Cakisma logu: C:\www\coremusic.net\.ai\ui-design\reference\figma\token-conflicts.md (400 satir)
SONUC -> toplam=7 | pass=5 | bos=2 | fail=0 | bos_bp=3840,tv

BITTI
```

**7 breakpoint** üretildi ✅ · çakışma logu **406 satır** (400 veri satırı, betik sınırı 400) · **Faz 9 doğrulama kapısı eklendi (2026-09-29):** her `$bp` için R1–R6 kontrolü (eksik kaynak/JSON okuma · token yazma + geri okuma + `_meta` / `_meta.counts` · `_meta.sections` ↔ disk sayımı · 6 haneli hex · boş üretim) → satır sonu `DURUM: PASS|BOS|FAIL`, sonda `SONUC -> toplam=7 | pass=5 | bos=2 | fail=0 | bos_bp=3840,tv` + `BITTI` ve **exit 0**. Round-trip `FAIL` ölçümü artık **betik içi kanıttır** (§8-#6 kapatıldı).

### 6.6 layoutGrids sayımı (`.ai/ui-design/reference/figma/raw/*.json`)

`"layoutGrids"` geçiş sayısı, dosya bazında (toplam **48**):

| Dosya | Sayı | Karşılık gelen iddia |
|---|---|---|
| `page-1047-15802.json` | **1** | 1024 → 1 ✅ |
| `page-2161-12438.json` | **2** | 1920 → 2 ✅ |
| `page-18-2907.json` | **21** | system → 21 ✅ |
| `page-326-3386.json` | **0** | 3840 → 0 ✅ |
| `page-16-106.json` | **0** | tv → 0 ✅ |
| `nodes-user-12.json` | 21 | (1024/1920 ortak node havuzu) |
| `page-15-403.json` / `page-1988-14156.json` | 1 / 1 | 1920 kaynağı |
| `page-2003-24752.json` (mobile) / `page-1991-12056.json` (plan) | 0 / 0 | — |
| `page-2135-19832` · `page-319-2789` · `page-462-5874` · `page-608-11052` · `page-1801-12472` · `page-1801-12473` · `images-1024-1920` · `nodes-1024-1920` | 0 | — |

---

## 7. Disk Envanteri (Sayaç Kapıları — 2026-09-29)

| Öğe | Ölçüm | Not |
|---|---|---|
| `.ai/ui-design` kök `*.md` | **6** | `00-device-matrix` … `05-responsive-architecture` |
| `screens/` | **21** | indeks 1 + spec 20: `T07-embedded` **12** · `T17-monitor-22fhd` **2** · `shared` **6** |
| `flow/` | **21** | indeks 1 + auth **5** · music **5** · settings **4** · navigation **3** · automotive **2** · watch **1** |
| `prompt/` | **51** | **48 içerik**: component **16** · page **12** · layout **10** · screen içerik **10** + indeks **2** (`prompt/00-prompt-index`, `prompt/screen/00-prompt-index`) + `web-research` |
| `reference/` | **17** | üst düzey **11** + `figma/` **6** |
| `tokens/` | **4 md + 7 json** | — |
| `.ai/ui-design` toplam | **120 md · 27 json** | — |
| `.ai/.png` | **19 PNG** | salt-okunur SSOT |
| `reference/figma/png` | **151 hedef · 136 indirilen · 15 gizli node (`visible: false`) → API NULL · 13 legacy · dizin toplamı 149** | Faz 8b ölçümü — §7.1 |
| `reference/figma/raw` | **19 JSON · 79.7 MB** | — |

**Git durumu (kapanış ölçümü — 2026-09-29):**

```
git status --porcelain -- .ai/ui-design .ai/scripts
 M .ai/ui-design/reference/04-verification.md
 M .ai/ui-design/reference/figma/_extraction-notes.md

git status --porcelain -- .ai
 M .ai/ui-design/reference/04-verification.md
 M .ai/ui-design/reference/figma/_extraction-notes.md
?? .ai/.decisions/accepted/ADR-048-view-transition-api-integration.md
?? .ai/.decisions/accepted/ADR-049-startup-prompt-loader.md
?? .ai/.decisions/accepted/ADR-050-multi-db-sync-strategy.md
?? .ai/.decisions/accepted/ADR-052-hybrid-auth-session-jwt.md
```

- **Bu denetimin tek yazdığı dosya:** `reference/04-verification.md` ✅
- `_extraction-notes.md` değişikliği denetim **öncesinde** mevcuttu (eklenen blok: `## Extract: tam cekim (2026-09-29 19:16:45)` → `149 dosya indirildi`); bu denetimde yeniden yazılmadı.
- `.ai/index.md` / `.ai/log.md` / `CHECKLIST.md` / `TODO.md` denetim **sırasında** orkestratör tarafından commit'lendi (`e9a93cb`); kapanışta working tree'de değiller.
- Untracked 4 ADR bu denetimin **kapsamı dışındadır** — hiçbirine dokunulmadı. `git commit` **atılmadı**.

> **Faz 8b eki (2026-09-29):** Üstteki `git status` bloğu **Faz 8 kapanış anının** ölçümüdür. Faz 8b'de (PNG envanter gerçeği — §7.1) **6 dosya** yazılmıştır: `reference/04-verification.md` · `reference/figma/_extraction-notes.md` (yalnız append) · `AGENTS.md` · `CLAUDE.md` · `index.md` · `WORKFLOW.md`. `log.md` ve 5 untracked ADR bu denetimin **kapsamı dışıdır**, dokunulmadı. Yine `git commit` **atılmadı**.

```
git log --oneline -7
4bacc22 docs(media): P0-4 kapandi -- deep statik yeniden denetim 8 PASS/0 FAIL
e9a93cb chore(vault): session checklist + todo + lifecycle links
546e983 ui-design: Faz 7 guardrail guncellemesi (6 dosya) - AGENTS/CLAUDE/WORKFLOW/index/keys/log
6d93859 feat(api): method-aware routing + ApiKeyRepository + api-key CLI (Faz 1a)
1ca639b docs(media): checklist + todos - Faz1-3 denetim ve is listesi
4b5a5ca media: Faz 3 GUI on-spec (8 ekran + wireframe + ITCSS/BEM + API)
c6b07be refactor(auth): login/register/forgot/reset/gender/logout sayfalari API'ye gecis (Faz 3a)
```

Faz 0–7 commit'leri bu listenin **dışında (daha eski)** — §5'te `git cat-file -e` ile 7/7 doğrulandı.

---

### 7.1 PNG hedef/envanter gerçeği (Faz 8b ölçümü — 2026-09-29)

> ⛔ **YASAKLI İDDİA:** "`reference/figma/png` → **149/151, yani 2 eksik**" okuması **yanlıştır** ve bu ölçümden sonra **hiçbir belgede kullanılamaz**. 149 sayısı 151'nin 2 eksiği değildir. Doğru ifade:
>
> `reference/figma/png` → **151 hedef · 136 indirilen · 15 gizli node (`visible: false`) Figma images API'den NULL → indirilemez** · 13 legacy dosya (eski adlandırma, fazladan) · dizin toplamı **149**.

**Aritmetik (üç rakam da ölçülmüştür, tahmin yok):**

```
151 hedef = 136 indirilen + 15 indirilemeyen (gizli node)
149 dizin = 136 id-prefixed + 13 legacy
```

- **Hedef = 151** — 11 kullanıcı node'u + 15 sayfanın top-level export edilebilir child'ları; `figma-extract.ps1` çıktısındaki "PNG hedef sayisi: 151" ile aynı.
- **İndirilen = 136** — `^\d+-\d+-` id-prefixed dosya sayısı, dizinden elle sayıldı.
- **İndirilemeyen = 15 node** — Figma `/v1/images` endpoint'i **NULL** döndürüyor. Kanıt: **scale=1 ve scale=2**, **20'lik parti** ve **tek tek** sorgu → **15/15 NULL** (HTTP 200, `images.<id> = null`).
- **Kök neden: 15/15 node `visible: false` (gizli)** — ham JSON'dan geometri okunarak kanıtlandı. Figma export API **gizli node'lara PNG vermez**; bu yüzden yeniden denemek de sonucu değiştirmez.
- **136 + 15 = 151** → hedefin tamamı hesaplanıyor; **eksik node yok**, indirilemeyen node var.

**15 gizli node (id · tip · W×H · sayfa · ad):**

| id | type | W×H | page | name |
|---|---|---|---|---|
| `1491:37281` | GROUP | 4456×1476 | page-1047-15802 | Pink - Dark |
| `1491:37282` | GROUP | 4503×1569 | page-1047-15802 | other mavi |
| `1047:29966` | FRAME | 2170×60 | page-1047-15802 | Frame 4 |
| `1976:11757` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1976:12013` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1980:13448` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1980:13692` | FRAME | 1024×600 | page-1047-15802 | Linux 1024 - Göz At - Tıklama Clikced |
| `1491:37718` | GROUP | 3880×1080 | page-462-5874 | mavi |
| `2831:13458` | INSTANCE | 506.2×198.2 | page-462-5874 | Playlist Status Div |
| `2161:12439` | GROUP | 3880×1080 | page-2161-12438 | mavi |
| `1988:15821` | GROUP | 4456×1476 | page-1988-14156 | Pink - Dark |
| `1988:16433` | GROUP | 4503×1569 | page-1988-14156 | other mavi |
| `1988:17767` | FRAME | 2170×60 | page-1988-14156 | Frame 4 |
| `1988:18007` | FRAME | 1024×600 | page-1988-14156 | Linux 1024 - Singer Page Serach = Dilso'z |
| `1988:18031` | FRAME | 1024×600 | page-1988-14156 | Linux 1024 - Singer Page Serach = S |

(15/15 ham JSON'da bulundu; **hepsi `visible: false`**.)

**13 legacy dosya** (id-prefixed olmayan ad — önceki çekim adlandırmasından; çoğu için id-prefixed karşılık diskte mevcut, bu yüzden bu 13 fazladandır):

1. `1024 - Diiv2 Button.png`
2. `1024 - Footer.png`
3. `1024 - Menu En Son Şarkılar.png`
4. `1024 - Menu Oynatma Listesi.png`
5. `1024 - Player Info.png`
6. `1024 - Sıradaki Şarkı.png`
7. `1024 - Welcome Div.png`
8. `1920 - Div2 Button.png`
9. `1920 - Player Info.png`
10. `1920 - Welcome Div.png`
11. `Core Music - Linux Pi.png`
12. `Linux  1024 - Home Page.png`
13. `Linux - 1920 - Home.png`

**Sonuç:** envanter kapısı bu ölçümden sonra **"eksik" iddiası kabul etmez**; gizli node'lar Figma tarafında `visible: false` olduğu için API'den PNG alınamaz ve bu bir **veri kaybı değil, Figma davranışı**dır. İlgili ham kayıt: `[[ui-design/reference/figma/_extraction-notes]]` § "PNG hedef kırılımı (Faz 8b)".

---

## 8. Açık Borçlar ve Veri Bütünlüğü

> Bu bölüm **uydurulmaz**: her satır ölçümün kendisidir. Tamamlanmış gibi gösterilen, eksik işaretlenmiştir.

| # | Borç | Ölçüm (2026-09-29) | Davranış |
|---|---|---|---|
| 1 | `flow/**` eksik zorunlu bölüm stub'u | Y5 (`2f7959c`, 2026-09-30) sonrası: **6 `⚠️ VERIFICATION REQUIRED` / 21 dosyanın 6'sında** — 3 `## 3. Hata Senaryoları` stub'u (`flow/watch/01` · `flow/automotive/01` · `flow/automotive/02`) + 3 gerekçeli (`flow/auth/03` kod çelişkisi · `flow/navigation/03` tetikleyici · `flow/settings/03` kontrol); `flow/navigation/01` Y5'te nav/01 §3 kod kanıtlı tabloyla kapandı; akış toplamı Y1 (`dc700cf`) + Y4 (`5aa2c45`) + Y5 (`2f7959c`) ile **44 → 7 → 6** (ayrı sayaç: `flow/**` toplam **9 `⚠` ikonu / 8 dosya** — kalan 3'ü `00-flow-index` şablon notu ×2 + `auth/05` ASCII art) | İlgili alan tamamlanmış sayılmaz |
| 2 | `prompt/**` işaretli eksik girdi | Y5 (`2f7959c`, 2026-09-30) sonrası: **0 `⚠️` / 0 dosya** — `prompt/screen/T9-car.md` Voice control kanıtla kapatıldı (`00-device-matrix` L182 + AU-T29/T30 + L304); Y2 (`194dc93`) + Y3 (`12a008d`) ile **93 → 1 → 0**; parantez: Y2'nin bilinçli bıraktığı **9 `⚠️ KAYNAK YOK`** ASCII gerekçesi ayrı sayaçtır, hâlâ 9 dosyada (`prompt/page/07-settings` + `prompt/screen/` T1 · T2 · T3 · T5 · T7 · T8 · T9 · T10 — 9 dosya; bunlar `VERIFICATION` kelimesi içermez) (ayrı sayaç: `prompt/**` toplam **16 `⚠` ikonu / 12 dosya** — kalanı `00-prompt-index` Truth Mode notu ×5 · `web-research` kod örneği ×1 · `C06-form-input` ASCII art ×1 + 9 `KAYNAK YOK`) | Uydurma token/JSON/ölçü yazılmaz; girdi kullanıcıdan istenir |
| 3 | `screens/T17-monitor-22fhd/welcome-popup.md` | `status: draft` + `source_of_truth: ⚠️ VERIFICATION REQUIRED — PNG bekleniyor` (20 spec'in tek draft'ı) | Draft ekran frontend kanıtı yapılmaz |
| 4 | `tokens/tokens-3840.json` (**1367 bayt**) · `tokens/tokens-tv.json` (**1363 bayt**) | `_meta.counts` → `nodes:1`, `sections` tamamı **0** (c/t/s/r/sp/sz = 0) → tasarım yok | mobile/tablet/TV/4K katmanları `status: planlanmış`; token Figma'dan gelmez, **uydurulmaz** |
| 5 | `reference/figma/png` envanter okuması | Önceki okuma **yanlıştı** (Faz 8b ölçümü): dizin **149** = **136** id-prefixed + **13** legacy; hedef **151** = 136 indirilen + **15 indirilemeyen node** (`/v1/images` → NULL, hepsi `visible: false`). Ham çekim logu korunur: `_extraction-notes.md` → `Extract: tam cekim (2026-09-29 19:16:45) = 149 dosya indirildi` (log yalanlanmaz, §7.1'de açıklanır). `figma-extract.ps1 -ImagesOnly` bu denetimde **yeniden çalıştırılmadı** (başka dosyaya yazar → yazma kapsamı ihlali); API tarafı scale 1/2 + 20'lik parti + tek tek sorgu ile **15/15 NULL** ölçüldü (§7.1) | İddia kapatıldı: "151 var" denmeyeceği gibi **"eksik" de denmez** — doğru ifade §7.1'dedir |
| 6 | ~~Token round-trip **FAIL=0**~~ — **KAPATILDI (Faz 9, 2026-09-29)** | Kapı betiğe eklendi; gerçek ölçüm: `SONUC -> toplam=7 \| pass=5 \| bos=2 \| fail=0 \| bos_bp=3840,tv` · `BITTI` · **exit 0** (§6.5) | **Kapatıldı** — iddia artık betik çıktısıyla kanıtlanır; commit subject (`f02d02b`) tek dayanak olmaktan çıktı |
| 7 | PS 5.1 `.ps1` BOM kuralı (keys.md §15 madde 6 / Guardrail #16) | `.ai/scripts/*.ps1` → **6 dosyanın 2'sinde** BOM var (`kalip-abc-check.ps1` · `figma-tokens.ps1` — Faz 9'da eklendi); **4'ünde yok** (`device-matrix-catid`, `figma-extract`, `screens-frontmatter-check`, `wiki-link-check`) | `figma-tokens.ps1` ihlali kapatıldı; kalan 4 dosya açık borç — bu denetimde kapılar Türkçe mojibake üretmeden çalıştı |
| 8 | `00-device-matrix.md` §3 `Web & Özel (T41-T45)` tablosu | `CatID` başlığı **yok** (tablo `| Tier |` ile başlıyor) → §2.1'de `WB` öneki tanımlı, §3'te `WB-*` veri satırı **0** | Açık borç; T07 çakışmasını etkilemez |
| 9 | Figma anahtarı md/json içine yazılmaz (Guardrail #3) | Gizli token (`FIGMA_TOKEN` değeri / `figd_`) ui-design genelinde **0 eşleşme** ✅ · Figma **file key** ise 7 token JSON'un `_meta.source` satırında + `tokens-1024.json` içinde 2 figma.com URL anahtarı | Token yokluğu doğrulandı; file key yayını **açık borç** (orkestratör kararı) |
| 10 | `.env.figma` SSOT | `.gitignore:68` = `.ai/.env.figma` · `:69` = `.ai/.env.*` ✅ | Token hiçbir `.md`'ye yazılmaz; ayrıca `.md` = BOM'suz, `.ps1` = BOM'lu |
| 11 | `screens/**` kanıt-boşluğu işaretleri | **100 eksik-kanıt `⚠️` işareti / 21 dosyanın 21'inde** (ayrı sayaç: toplam `⚠` ikonu 171) — sınıflandırma: PNG yok · API'den gelmedi · state PNG'leri yok · ölçüm kod aşamasında → **kanıt gerektirir, vault'tan doldurulamaz** | Yeni ölçüm (PNG/Figma/kod) gelmeden işaret korunur; ilgili ekran tamamlanmış sayılmaz |
| 12 | kök `*.md` işaretleri (`.ai/ui-design` kök, 6 dosya) | **24 `⚠️` işareti / 6 dosyanın 6'sında** — `00-device-matrix` **12** = tasarımı olmayan tier'lar (`status: planlanmış`; Guardrail: Figma tasarımı olmayan tier → `⚠️`) · `03-implementation-plan` **4** = kapsam tanımsız pending · `05-responsive-architecture` **3** = 2560/3840 tanımlı değil · `01-mockup-index` **3** · `02-component-inventory` **1** (Figma↔C eşlemesi yapılmadı) · `04-accessibility-gaps` **1** | İlgili alan tamamlanmış sayılmaz; tasarımsız tier için token/PNG uydurulmaz |
| 13 | `reference/**` işaretleri | **5 `⚠️` işareti / 17 dosyanın 2'sinde** — `reference/04-verification.md` **2** (bu tablonun #1 ölçüm alıntısı + #3 draft satırı) · `reference/figma/grid-rules.md` **3** (tablet/2560 tasarımı yok notları) | Yeni ölçüm (PNG/Figma/kod) gelmeden işaret korunur |
| 14 | Y5 — QA/kod kaynağı olmayan kalan işaret kümesi | **3 işareti**: yalnız `flow/` `## 3. Hata Senaryoları` stub'u **3** (`watch/01` · `automotive/01` · `automotive/02`) — gerekçe: §1/§2'de hata dalı **0** (0 eşleşme), bu tier'da repoda **0 kod dosyası**, QA kaydı **0** (`.ai/reports` 0 eşleşme); kanıt (kod veya QA) gelmeden tablo üretilmez. Y5 (`2f7959c`) ile kapatılanlar: `prompt/screen/T9-car.md` **Voice control** (`00-device-matrix` L182 + AU-T29/T30 + L304) ve `flow/navigation/01` **nav/01 §3 kod kanıtlı 4 sütunlu tablo** | Y5: QA/kod kaynağı yok → işaretli kalır; ilgili alan tamamlanmış sayılmaz |

**Faz 6 kayıp kontrolü (okuma sırasında):** `screens/` ve `flow/` yeniden yazımlarında silinen kume eklenen kumenin alt kümesi — **veri kaybı 0**; eksik olan **işaretli eksikliktir** (yukarıdaki #1–#3).

---

## 9. Guardrails (Değiştirilemez Kullanıcı Kuralları)

1. **Widget grid kanonik kuralı** — Figma API çıktısından **önceliklidir**: `1024` → 2×2 + 1×5 + 1×5 (**12 slot**); `1920` → 4×4 + 1×8 + 1×8 (**20 slot**). Aykırı sayım/render revize edilir, kural değil.
2. **`screens/T07-embedded/` dizin adı DEĞİŞTİRİLMEZ** (~489 wiki-link); tier/ID ayrıştırması dosya yoluyla değil **`CatID` sütunuyla** yapılır (§6.4).
3. **Figma token yalnız `.ai/.env.figma`** (`.gitignore:68-69`); `FIGMA_TOKEN` / `FIGMA_FILE_KEY` hiçbir `.md` / `.json` / `.log` içine yazılmaz; betiklerde sabit anahtar yoktur (§8-#9).
4. **Dokunulmaz yüzeyler:** `.ai/.png/**` (salt-okunur) · frozen ADR'ler · `*.php` / `*.js` / `*.css` / `*.sql` kaynak kod — bu denetimde **hiçbiri yazılmadı**.
5. **Üretim ve kapanış:** yeni `.md` `.ai/.templates/ui-design/` Kalıp A–D'ye uyar (Guardrail #16); **`git commit` subagent tarafından ATILMAZ** — orkestratöre aittir.

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.1.4 |
| Status | QA denetim tamamlandı — kapı 1-5 GECTI (A:0 B:0 C:0 · frontmatter 21/0 · wiki-link gerçek kırık 0 · CatID T07 çözümlü · **figma-tokens: `SONUC -> toplam=7 \| pass=5 \| bos=2 \| fail=0 \| bos_bp=3840,tv` + exit 0 — Faz 9 kapısı**); **PNG envanter gerçeği işlendi (Faz 8b §7.1 — 151/136/15/13/149)**; **§8 ölçüm Y5 sonrası yenilendi (`2f7959c` · 2026-09-30): flow 44→7→6 · prompt 93→1→0 · toplam 137→135 · Y5 işareti 5→3** |
| Verification Steps | 6 |
| Tier Matrix | 6 rows |
| Red Flags | 7 |
| Cross References | 3 |
| Gate Scripts | 5 (§6 — birebir çıktı) |
| Faz Kaydı | 8 faz (§5 — Faz 0-7 commit kanıtlı 7/7, Faz 8 commit'i orkestratörde) |
| Disk Envanteri | §7 — 120 md · 27 json · 19 PNG · PNG **151 hedef / 136 indirilen / 15 gizli node** (§7.1) · raw 79.7 MB |
| Açık Borç | 13 satır açık + 1 kapatıldı (§8 — flow 6 · prompt 0 + 9 KAYNAK YOK · screens 100 · kök md 24 · reference 5 · Y5 3 · draft 1 · boş token 2 · PNG envanter 15 gizli node (§7.1) · BOM 4 · file key 7; **#6 Faz 9 kapısıyla kapatıldı**) |
| Last Updated | 2026-09-30 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-30
**Mode:** Red Team · Human Mode · Truth Mode
