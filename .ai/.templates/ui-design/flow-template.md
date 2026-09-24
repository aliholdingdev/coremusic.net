---
reference_doc: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
title: "CoreMusic — UI Design Flow Dokümanı Şablonu (Kalıp B)"
type: template
category: ui-design
pattern: B
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/.templates/ui-design/flow-template.md"
  source_of_truth: ".ai/ui-design/reference/legacy-inventory.md §Şablon kalıpları (Kalıp B) · .ai/ui-design/flow/00-flow-index.md §8"
---

# CoreMusic — UI Design Flow Dokümanı Şablonu (Kalıp B)

> **NE ZAMAN OKUNUR:** `.ai/ui-design/flow/` altında **her yeni veya güncellenen akış dosyası** (`flow/<kategori>/NN-*.md`) üretilirken bu şablon **ZORUNLU** okunur — tipik görevler: login/register/forgot-password/select-gender/logout (auth), playback/playlist-queue/album-browse/artist-browse/search (music), spa-routing/header-nav/footer-player (navigation), wifi-connect/bluetooth-connect/equalizer/general (settings), android-auto/carplay (automotive), watch now-playing. Ayrıca `flow/00-flow-index.md` gibi flow indeks dosyaları da bu kalıbı kullanır. **Dosya yoksa veya metin "Akış Diyagramı → Ekran Akışı → Hata Senaryoları → Tier Varyasyonları → BEM → Adımlar" sırasını taşımıyorsa üretim DURAR.** Referans/envanter tipi için `[[reference-template]]`, prompt için `[[prompt-template]]`, ekran spec'i için `[[screen-spec-template]]` geçerlidir.

**Zorunlu Bağlantılar:** [[../index]] · [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../WORKFLOW.md]] · [[../../ui-design/flow/00-flow-index]] · [[../../ui-design/reference/legacy-inventory]]

**Kalıp kaynağı:** `.ai/ui-design/reference/legacy-inventory.md` → "Şablon kalıpları" → **Kalıp B** + ASCII kuralı `flow/00-flow-index.md §8`.

---

## 1. Amaç

Bu şablon, CoreMusic ui-design vault'undaki **akış (flow) dokümanlarının** altı zorunlu bölümünü, ASCII box-arrow diyagram yazım kuralını ve tier/hata/BEM entegrasyonunu tanımlar. **Guardrail #16:** `flow/` altında yeni bir `.md` bu şablondan üretilmek ZORUNLUDUR; sırası bozulmuş bir flow dosyası geçersizdir ve yeniden yazılır.

**Disk gerçeği (2026-09-24):** `.ai/ui-design/flow/` altında **21 md** vardır — 1 master indeks + 20 flow, **6 kategori** (auth 5 · music 5 · navigation 3 · settings 4 · automotive 2 · watch 1). `00-flow-index.md` "17 flow / 4 kategori" iddiası disk gerçeğiyle ÇELİŞİR; yeni yazılan dosyalarda sayı iddiaları glob ile doğrulanır.

| Karar | Kaynak | Şablona gömülü karşılığı |
|-------|--------|--------------------------|
| ASCII box-arrow tek diyagram stili | `flow/00-flow-index.md §8` | §3.4 sabit karakter seti |
| Flow dosyası 6 zorunlu bölüm | Kalıp B | §3.3 |
| 45-tier cihaz matrisi bağlayıcı | AGENTS.md §7.2 | §3.7 7 tier satırı |
| Guardrail #17 — tek component + responsive | CLAUDE.md §7 | §4 #5 |
| Zero Hallucination | Guardrail #3 | §4 #4 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `.ai/ui-design/flow/<kategori>/NN-*.md` (20 dosya) | `flow/00-flow-index.md` içindeki indeks tabloları → bu kalıp + `[[reference-template]]` imkânı |
| Yeni kategori/floy eklenmesi (NN sırası) | Ekranın piksel layout'u → `[[screen-spec-template]]` |
| Flow'a ait hata senaryoları, tier varyasyonları, BEM sınıfları, adım listesi | AI üretim promptu içeriği → `[[prompt-template]]` |
| State machine (STOPPED/PLAYING/PAUSED, WiFi/BT durumları) | Backend controller akışı → `.ai/.templates/backend/php-template.md` |

- **Kullananlar:** UI Designer (birincil), QA Engineer (hata senaryosu/test), Embedded/UI (automotive/watch varyantları).
- **Katman:** L3 (sunum) akış dokümantasyonu.
- **Ön koşul:** Flow'un dayandığı ekran gerçekten var (`[[../../ui-design/01-mockup-index]]` veya `screens/<tier>/` altında karşılığı).

---

## 3. Mimari

### 3.1 Frontmatter (flow dosyası)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — <Akış Adı> Flow"
type: flow
category: ui-design
date: YYYY-MM-DD
updated: YYYY-MM-DD
status: active
version: X.Y.Z
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

**10 alan zorunludur** ve registry'nin 7 zorunlu alanını (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`) içerir. Flow dosyalarında `reference:` bloğu **kullanılmaz** (PNG bağımlılığı screen-spec'e aittir); varsa `tier:` alanı eklenir.

**Şablon dosyasının kendi frontmatter'i** (bu dosya) ise registry yetkili değerini taşır: `authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"`.

### 3.2 Örnek — Doldurulmuş frontmatter (gerçek dosyadan)

```yaml
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Auth Login Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---
```

### 3.3 Zorunlu Bölüm Sırası (6 + footer)

| § | Başlık (birebir) | Zorunlu içerik |
|---|------------------|----------------|
| H1 | `<Akış Adı Flow` | `# Auth Login Flow` biçiminde, "CoreMusic —" öneki YOK (gerçek dosya kanıtı) |
| 1 | `## 1. Akış Diyagramı (Decision Flow)` | ASCII box-arrow — §3.4 seti |
| 2 | `## 2. Ekran Akışı` | Kullanıcının gördüğü ekran/sahne sırası (madde veya tablo) |
| 3 | `## 3. Hata Senaryoları` | Tablo: `Hata · Tetikleyici · Çözüm · Max Retry` |
| 4 | `## 4. Tier-Bazlı Varyasyonlar` | 7 tier satırı — §3.7 |
| 5 | `## 5. BEM Sınıfları` | Flow boyunca kullanılan `block__element--modifier` listesi |
| 6 | `## 6. Adımlar` | Numaralı, test edilebilir adım listesi |
| — | `---` + Authority footer | §3.10 |

**Sıra değişmez.** Bölüm eklemek için araya `## 3A.` gibi ara numara konur; `1` her zaman Akış Diyagramı'dır.

### 3.4 ASCII Box-Arrow Karakter Seti (sabit)

`flow/00-flow-index.md §8` ile karakter seti **kilitlidir** — başka karakter kullanılmaz:

```
Kullanılan karakterler:
  Yatay çizgi:    ─
  Dikey çizgi:    │
  Köşe:           ┌ ┐ └ ┘
  Kavşak:         ┬ ┤ ├ ┴
  Ok:             ▼
  Karar dalı:     ─┤─ Evet / Hayır
```

**Yasak karakterler:** `+ - | v >` ASCII alternatifleri, `→ ← ↑ ↓` unicode oklar, `╭╮╰╯` yuvarlak köşeler, emoji oklar. Diyagram bloğu daima üç-tırnaklı koddur (dil etiketi YOK).

**Temel kalıp — dikey zincir:**

```
┌─────────────┐
│    BAŞLA    │
└──────┬──────┘
       │
┌──────▼──────┐
│ <Eylem>     │
└──────┬──────┘
       │
┌──────▼──────┐
│ <Sonuç>     │
└─────────────┘
```

**Karar kalıbı (Evet/Hayır):**

```
┌──────────────────┐
│ <Koşul Sorgusu>  │
└────────┬─────────┘
    ─────┤─ Evet
    │
    ┌───▼──────┐      ┌──────────────┐
    │ <Yol A>  │      │   <Yol B>    │
    └──────────┘      └──────▲───────┘
                             │ Hayır
```

**State machine kalıbı (WiFi/Bluetooth/playback):**

```
┌──────────┐   bağlan   ┌───────────┐
│ DISCONN. ├───────────▶│ CONNECTING│
└────▲─────┘            └─────┬─────┘
     │ hata / timeout         │ başarılı
     │                  ┌─────▼─────┐
     └──────────────────┤ CONNECTED │
                        └───────────┘
```

**Hizalama kuralları:** her kutu içi metin kutu genişliğine göre `-` ile doldurulur; dikey `│` ve `▼` sütunları üst üste birebir denk gelir; bloklar arası tek satır boşluk yoktur (düz zincir görünümü korunur).

### 3.5 `## 2. Ekran Akışı` kalıbı

Ekran akışı **görsel sahne sırasıdır**, diyagramın tekrarı değildir. İki biçimden biri kullanılır:

**(a) Madde biçimi (kısa akışlar):**

```markdown
## 2. Ekran Akışı

1. **Cinsiyet Seçimi** (`shared/select-gender`) — ilk adım, zorunlu.
2. **Login Formu** (`shared/login`) — e-posta + şifre + "Beni Hatırla".
3. **Hata kartı** — form üstünde satır-içi uyarı; ekran değişmez.
4. **Başarı** → `home-dashboard` (spa-routing ile DOM patch).
```

**(b) Tablo biçimi (uzun/çok dallı akışlar):**

```markdown
## 2. Ekran Akışı

| # | Ekran / Sahne | Kaynak dosya | Geçiş koşulu |
|---|---------------|--------------|--------------|
| 1 | Login formu   | `screens/shared/login.md` | açılış |
| 2 | Hata kartı    | (form içi)   | doğrulama başarısız |
| 3 | Home dashboard| `screens/T08-embedded/home-dashboard.md` | oturum kuruldu |
```

**Kural:** her satır `screens/` altında bir karşılık göstermeli ya da `(form içi)` gibi bileşen-içi olduğunu belirtmelidir. Karşılığı olmayan sahne `⚠️ VERIFICATION REQUIRED` işaretlenir.

### 3.6 `## 3. Hata Senaryoları` tablo kalıbı

Dört sütun **zorunlu ve sabit sıradadır**:

```markdown
## 3. Hata Senaryoları

| Hata | Tetikleyici | Çözüm | Max Retry |
|------|-------------|-------|-----------|
| Geçersiz kimlik | Sunucu 401 döndü | Form hatası göster, ekranda kal | 3 |
| Ağ yok | `fetch` reddedildi | Offline uyarı + tekrar dene butonu | 3 |
| CSRF reddi | Sunucu 403 döndü | Token yenile, isteği tekrar gönder | 1 |
| Zaman aşımı | 15s üstü yanıt yok | `Degraded` uyarısı + iptal | 2 |
```

**Kurallar:**
1. `Max Retry` dolu sayıdır (AGENTS.md §8 Priority tablosuyla uyumlu: CRITICAL=1, HIGH/MEDIUM=3, LOW=2).
2. Çözüm sütunu **davranış** söyler, kod yazmaz.
3. Her hata satırı ya UI'da görünür bir durumdur ya da `log.md`'ye yazılır — ikisi de yoksa satır uydurmadır, silinir.
4. En az 1, en fazla 8 satır; boş tablo (`| Hata |...|` + çizgi) kabul edilmez.

### 3.7 `## 4. Tier-Bazlı Varyasyonlar` (7 tier)

| Tier | Cihaz | Varyasyon tipi | Örnek davranış |
|------|-------|----------------|----------------|
| Embedded (1024×600) | RPi5 7" touch | **Varsayılan** — dokunma | Buton ≥44px, hover yok |
| Phone (720p/1080p/1440p) | T01-T03 | Dikey akış + alt bar | Ekran klavyesi formu öne çıkarır |
| Laptop/Desktop (1366-1920) | T15-T17 | Hover + klavye | `focus-visible` outline görünür |
| Desktop 4K (3840) | T18 | Ölçek istisnası | **§7.4 4K No-Center — 4K'da ortalamama YASAK** |
| TV (1920×1080) | T25 | Uzaktan kumanda odağı | Ok tuşu ile odak sırası zorunlu |
| Car (1280×720) | T29 | Büyük dokunma alanı | Satır başına ≤2 aksiyon |
| Watch (396×484) | T31 | Mikro ekran | Adım başına 1 aksiyon, metin ≤2 satır |

**Zorunlu satır sayısı: 7.** Tier adları `[[../../ui-design/00-device-matrix]]` ile birebir aynı yazılır. Tier yoksa "Bu akış tüm tier'larda aynıdır" satırı 7 satırın hepsinin yerine geçer ve bunu izleyen cümlede gerekçesi yazılır.

### 3.8 `## 5. BEM Sınıfları` kalıbı

```markdown
## 5. BEM Sınıfları

| BEM sınıfı | Rol | Durumlar |
|------------|-----|----------|
| `.login-form` | blok — form kökü | `--error`, `--pending` |
| `.login-form__email` | eleman — e-posta alanı | `:focus`, `:invalid` |
| `.login-form__submit` | eleman — gönder butonu | `:hover`, `:disabled` |
| `.form-error` | blok — hata kartı | `--visible` |
```

**Kurallar:** format `block__element--modifier` (AGENTS.md / assets AGENTS.md §4); her sınıf `02-component-inventory.md` içindeki C01-C16 ile çakışıyorsa envanterdeki ad aynen kullanılır; envanterde olmayan yeni sınıf ekleniyorsa `⚠️ VERIFICATION REQUIRED` ile "envantere eklenecek" notu düşülür.

### 3.9 `## 6. Adımlar` kalıbı

```markdown
## 6. Adımlar

1. Kullanıcı cinsiyet seçimi ekranını açar.
2. Bir seçenek seçer → "Devam" etkinleşir.
3. Login formuna yönlenir; e-posta alanı odaklanır.
4. Geçersiz kimlik girilirse hata kartı açılır, ekran değişmez (Max Retry 3).
5. Geçerli kimlikte oturum kurulur ve `home-dashboard` DOM patch ile açılır.
```

**Kurallar:** numaralı ve sıralı; her adım **tek gözlemlenebilir eylemdir** ("...ve stili günceller" gibi birden çok eylem tek adıma yazılmaz); başlangıç ve bitiş adımı zorunludur; E2E testi bu adımlardan yazılabilir olmalıdır.

### 3.10 Authority footer

```markdown
---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** YYYY-MM-DD
**Mode:** Red Team · Human Mode · Truth Mode
```

### 3.11 Kopyala-Yapıştır — Tam Kalıp B İskeleti

````markdown
---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — {{AKIŞ_ADI}} Flow"
type: flow
category: ui-design
date: {{YYYY-MM-DD}}
updated: {{YYYY-MM-DD}}
status: active
version: {{X.Y.Z}}
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# {{AKIŞ_ADI}} Flow

## 1. Akış Diyagramı (Decision Flow)

┌─────────────┐
│    BAŞLA    │
└──────┬──────┘
       │
┌──────▼──────┐
│ {{EYLEM}}   │
└──────┬──────┘
       │
┌──────▼──────┐
│ {{SONUÇ}}   │
└─────────────┘

## 2. Ekran Akışı

| # | Ekran / Sahne | Kaynak dosya | Geçiş koşulu |
|---|---------------|--------------|--------------|
| 1 | {{sahne}}     | `{{screens/yol}}` | {{kosul}} |

## 3. Hata Senaryoları

| Hata | Tetikleyici | Çözüm | Max Retry |
|------|-------------|-------|-----------|
| {{hata}} | {{tetikleyici}} | {{davranış}} | {{n}} |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Cihaz | Varyasyon tipi | Örnek davranış |
|------|-------|----------------|----------------|
| Embedded (1024×600) | RPi5 7" touch | Varsayılan — dokunma | Buton ≥44px, hover yok |
| Phone | T01-T03 | Dikey akış + alt bar | Klavye formu öne çıkarır |
| Laptop/Desktop | T15-T17 | Hover + klavye | `focus-visible` outline |
| Desktop 4K | T18 | Ölçek istisnası | 4K'da ortalamama YASAK |
| TV | T25 | Uzaktan kumanda odağı | Ok tuşu odak sırası |
| Car | T29 | Büyük dokunma alanı | Satır başına ≤2 aksiyon |
| Watch | T31 | Mikro ekran | Adım başına 1 aksiyon |

## 5. BEM Sınıfları

| BEM sınıfı | Rol | Durumlar |
|------------|-----|----------|
| `{{block}}` | blok | `--{{modifier}}` |

## 6. Adımlar

1. {{başlangıç}}
2. {{davranış}}
3. {{bitiş}}

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
| 1 | Guardrail #16 — bu şablonsuz flow dosyası üretilmez | Dosya geçersiz; yeniden yaz |
| 2 | Bölüm sırası 1→6 sabit; `## 1. Akış Diyagramı` her zaman birinci | Dosya geçersiz |
| 3 | ASCII seti yalnız `─ │ ┌ ┐ └ ┘ ┬ ┤ ├ ┴ ▼`; alternatif karakter yasak | Diyagram yeniden yazılır |
| 4 | Zero Hallucination — `screens/` karşılığı olmayan sahne/ekran uydurulmaz | `⚠️ VERIFICATION REQUIRED` |
| 5 | Guardrail #17 — tek component + responsive; akışa özel ayrı HTML/branch yasak | Kod revert |
| 6 | `Max Retry` değerleri AGENTS.md §8 öncelik tablosuyla tutarlı | Tablo düzeltilir |
| 7 | BEM sınıfı envanterde (C01-C16) varsa aynı ad kullanılır | Sınıf adı düzeltilir |
| 8 | Frontmatter 10 alan eksiksiz; `type: flow` | Dosya geçersiz |
| 9 | Secret/token/`figd_...` yazımı yasak | `[REDACTED]` + log |
| 10 | `.ai/ui-design/**` içine bu şablonla YAZMAZ — yazım yalnız ilgili agent domaini ve vault-utf8-writer ile | Revert + log |

### 4.2 Ek Kurallar

- **Zorunlu:** diyagramda geçen her düğüm §2 veya §6 ile ilişkilendirilir (dangling düğüm yasak).
- **Zorunlu:** dosya adı `NN-<kebab-flow-adı>.md`, `NN` kategorideki sıradaki numaradır; mevcut dosya adı değişmez (In-Place Refactoring).
- **Zorunlu:** Türkçe içerik UTF-8 (BOM'suz); yazım `.ai/scripts/vault-utf8-writer.mjs` ile.
- **Yasak:** 6 bölümden birini "gereksiz" diye silmek.
- **Yasak:** Diyagramı mermaid/ PlantUML'e çevirmek — bu vault ASCII box-arrow kullanır.
- **Yasak:** §4 Tier tablosunu 5 veya 6 satıra indirmek.

---

## 5. Workflow

```text
EKRANLARI DOĞRULA → ŞABLONU SEÇ → İSKELETİ KOPYALA → DOLDUR → §6 DOĞRULA → VAULT-YAZ → REGISTRY + LOG
```

| # | Adım | Aksiyon | Çıktı |
|---|------|---------|-------|
| 1 | Ekran doğrulama | Akışın dayandığı ekran(lar) `01-mockup-index` / `screens/` altında var mı? | Kanıt |
| 2 | Şablon seçimi | Flow → bu dosya; referans/prompt/spec değil | Karar |
| 3 | İskelet | §3.11 bloğunu staging'e kopyala | İskelet |
| 4 | Diyagram | §3.4 setiyle §1'i çiz; düğümleri hizala | §1 |
| 5 | Ekran akışı + hata | §3.5/§3.6 — kaynak dosya yolları diskten | §2, §3 |
| 6 | Tier + BEM + adım | §3.7 (7 satır) / §3.8 (envanter eşleşmesi) / §3.9 | §4-§6 |
| 7 | Doğrula | §6 listesi | Rapor |
| 8 | Yaz + kayıt | `vault-utf8-writer write` + `verify`; `flow/00-flow-index.md` ve `.templates/index.md` güncellenir; `log.md` append | Kayıt |

### 5.1 Adım Bazlı Hata Modları

| Adım | Tipik hata | Belirti | Düzeltme |
|------|-----------|---------|----------|
| 1 | Ekran yok | Akış ekranı `screens/` altında yok | `⚠️ VERIFICATION REQUIRED` koy; ekranı üretmeden flow'u kapatma |
| 2 | Karakter ihlali | `+ - |` veya `→` görüldü | Diyagramı §3.4 setiyle yeniden çiz |
| 3 | Hizasız diyagram | `│`/`▼` sütunları kaymış | Kutu genişliklerini `─` ile eşitle |
| 4 | Eksik bölüm | 6 bölümden biri yok | §3.3 sırasına göre ekle |
| 5 | Tier 7'den az | Tablo 5-6 satır | §3.7 satırlarını tamamla |
| 6 | Çelişen flow indeksi | `00-flow-index` sayı iddiası disk ile tutmuyor | İndeksi düzelt (yeni flow eklendiğinde zorunlu), `log.md`'ye not düş |
| 7 | Encoding | `verify` mojibake/BOM | `repair` → `verify` |

### 5.2 Hızlı Komut Referansı

```powershell
# flow dosyalarını say (disk kanıtı)
(Get-ChildItem .ai/ui-design/flow -Recurse -Filter *.md).Count
# bölüm sırasını doğrula
Select-String -Path <flow-dosyası> -Pattern '^## '
# yaz + doğrula
node .ai/scripts/vault-utf8-writer.mjs write --file <vault-yolu> --text "@<staging>"
node .ai/scripts/vault-utf8-writer.mjs verify --file <vault-yolu>
```

---

## 6. Doğrulama

### 6.1 Kontrol Listesi

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 10 alan; `type: flow`; 7 registry zorunlu alan içeride |
| 2 | Bölüm sırası | `## 1. Akış Diyagramı (Decision Flow)` → `## 6. Adımlar` arası 6/6, doğru sıra |
| 3 | ASCII seti | Yalnız `─ │ ┌ ┐ └ ┘ ┬ ┤ ├ ┴ ▼`; yasaklı karakter yok |
| 4 | Diyagram bütünlüğü | Her düğüm §2/§6'da referanslı; dangling yok |
| 5 | Hata tablosu | 4 sütun sabit; `Max Retry` sayısal; en az 1 satır |
| 6 | Tier tablosu | 7/7 tier satırı (veya "hepsi aynı" gerekçe satırı) |
| 7 | BEM | `block__element--modifier` biçimi; envanter adlarıyla çakışma yok |
| 8 | Adımlar | Numaralı, tek-eylem, başlangıç+bitiş var |
| 9 | Wiki-link / halüsinasyon | Hedefler diskte; uydurma ekran yok |
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
| Vault anayasası | [[../../CLAUDE.md]] | Guardrail #3/#4/#11/#16/#17 |
| Agent registry | [[../../AGENTS.md]] | §7.2 pre-flight — UI Design/45-tier/Responsive kontrolü |
| Süreç | [[../../WORKFLOW.md]] | §8.1 UI Design + Verification Gate |
| Flow master indeks | [[../../ui-design/flow/00-flow-index]] | §8 ASCII stili, kategori/flow tabloları |
| Cihaz matrisi | [[../../ui-design/00-device-matrix]] | Tier adları ve viewport'lar |
| Mockup indeksi | [[../../ui-design/01-mockup-index]] | 19 PNG — ekran doğrulaması |
| Bileşen envanteri | [[../../ui-design/02-component-inventory]] | C01-C16 BEM adları |
| Referans şablonu | [[reference-template]] | Kalıp A |
| Prompt şablonu | [[prompt-template]] | Kalıp C |
| Screen spec şablonu | [[screen-spec-template]] | Kalıp D |

### 7.1 Kategori → Mevcut Flow Dosyaları (disk kanıtı, 2026-09-24)

Yeni flow eklenirken önce bu tabloya bakılır; `NN` kategorinin en büyük numarasının devamıdır.

| Kategori | Adet | Dosyalar |
|----------|------|----------|
| `auth/` | 5 | `01-login` · `02-register` · `03-forgot-password` · `04-select-gender` · `05-logout` |
| `music/` | 5 | `01-playback` · `02-playlist-queue` · `03-album-browse` · `04-artist-browse` · `05-search` |
| `navigation/` | 3 | `01-spa-routing` · `02-header-nav` · `03-footer-player` |
| `settings/` | 4 | `01-wifi-connect` · `02-bluetooth-connect` · `03-equalizer` · `04-general` |
| `automotive/` | 2 | `01-android-auto-layout` · `02-carplay-layout` |
| `watch/` | 1 | `01-now-playing` |
| **Kök** | 1 | `00-flow-index.md` (master indeks — §8 ASCII stili + kategori/flow tabloları) |
| **TOPLAM** | **21 md** | 6 kategori + kök indeks |

> ⚠️ **Truth Mode notu:** `00-flow-index.md` "17 flow / 4 kategori" iddiası ile bu tablo (20 flow / 6 kategori) çelişir. Disk kanıtı bu tabloyu destekler; indeksin düzeltilmesi `.ai/log.md`'ye not düşülerek yapılır.

**Yeni kategori açma kuralı:** yeni bir cihaz sınıfı/akış ailesi geldiğinde önce klasör + `NN-` sırası, sonra `00-flow-index.md` kategorisine satır, en son `.ai/index.md` / `keys.md` keyword kaydı eklenir — tersi sıra kırık indeks üretir.

### 7.2 Red Team — Sık Yapılan Hatalar

| # | Hata | Neden yanlış | Doğru |
|---|------|--------------|-------|
| 1 | Diyagramı `A -> B` ile yazmak | Karakter seti kilitli (`flow/00-flow-index.md §8`) | `┌─┐ └┬┘ ▼` box-arrow |
| 2 | `## 2. Ekran Akışı`'nı diyagramın tekrarı yapmak | Bölümün işlevi görsel sahne listesi | Ekran/sahne tablosu + `screens/` kaynağı |
| 3 | Hata tablosunu 3 sütuna indirmek | `Max Retry` AGENTS.md §8 ile eşleşmeli | 4 sütun sabit |
| 4 | Tier satırını "hepsi aynı" diye başsız bırakmak | Gerekçe yazılmadan kabul edilmez | 7 satır **veya** 7 satır yerine geçen gerekçeli tek cümle |
| 5 | `## 6. Adımlar`'ı paragraf yazmak | Adımlar E2E testine çevrilebilir olmalı | Numaralı, tek-eylem maddeler |
| 6 | Flow'u ekran spec'iyle karıştırmak | Piksel layout bu kalıba ait değil | `[[screen-spec-template]]` kullan |
| 7 | Indeks sayılarını elle güncellememek | `00-flow-index` disk gerçeğiyle çelişir | Yeni flow eklendiğinde indeks + log birlikte güncellenir |

---

*UI Design Flow Template (Kalıp B) v1.0.0 — CoreMusic Template System*
**Template Version:** 1.0.0
**Last Updated:** 2026-09-24
**Mode:** Red Team · Human Mode · Truth Mode
