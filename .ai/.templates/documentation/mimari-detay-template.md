---
title: "CoreMusic — Mimari Dosya Şablonu (README + Detail, K0-K20 genel)"
type: template
category: template
version: 1.0.0
status: active
date: 2026-10-10
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
---

# Katman Dosya Şablonu — README + Detail (K0-K20 genel)

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[architecture/adlandirma-kurali]] · [[architecture/katman-baglilik-matrisi]] · [[architecture/katman-sayim-rehberi]]

> **Kullanım:** Bu tek şablon iki katman dosyası türünü kapsar — **Part A: Katman README'si**
> (`K{n}-<slug>/index.md` ya da `README.md`) ve **Part B: Kırılma/Detail dosyaları**
> (`K{n}-<slug>/<alt-klasör>/*.md`). İlkeler **K0-K20 tüm katmanlar için geçerlidir**;
> katman bazlı istisnalar §3.4'te yerel not olarak yazılır. Şablonsuz katman dosyası
> üretilmez (Guardrail #16).

---

## §1 Amaç

Bir katmanı okunabilir, denetlenebilir ve sayılabilir hâle getirmek: README katmanın
**kimliğini + haritasını** taşır, detail dosyaları katmanın **derinliğini** taşır; ikisi de
tek kanıt disiplinine bağlanır ve katman ihlali denetiminin yerel kaynağını oluşturur.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| K0-K20 katman README iskeleti (Part A) | ADR metinleri (→ `.ai/.decisions/`) |
| Katman kırılma/detail dosyaları (Part B) | ui-design / frontend / hardware / prompt şablonları |
| Frontmatter 7 alan, § sırası, kanıt etiketleri | Uç kullanıcı dokümanı |
| Wiki-link, sayım, stub disiplini | CI/CD çalıştırma talimatları (→ DevOps) |
| Web araştırmasının **yalnız metodoloji** kullanımı | Proje gerçeği kaynakları (yalnız disk kanıtı) |

## §2 Kaynak Politikası (iki katmanlı — bağlayıcı)

1. **Proje verisi (olgu kaynağı):** Yalnız repo diski. Her dosya/sayı/satır/ADR iddiası
   glob/grep/read ile **o görev içinde yeniden doğrulanır**; eski vault ölçümü kopyalanmaz.
   Doğrulanamayan → `UNKNOWN` · kısmi → `⚠️ VERIFICATION REQUIRED` · hedef → `PLANNED`.
2. **Web araştırması (yöntem kaynağı):** Yalnız bölüm yapısı ve tutarlılık metodolojisi için
   kullanılır — arc42 (12 bölümlük pratik iskelet) ve ISO/IEC/IEEE 42010 (viewpoint = paydaş +
   ilgi alanı → model türü; view'lar arası correspondence; karar gerekçesi).
   **Web'den gelen hiçbir teknik iddia proje dosyasına olgu olarak yazılmaz.**

## §3 Genel Kurallar (her iki parça için bağlayıcı)

### §3.1 Dosya = düğüm = tek sorumluluk

1. Her dosya tek bir sorumluluk sahiplenir; iki bağımsız konu için ikinci dosya açılır.
2. Dosya adı slug'ı, içeriğin düğüm adıyla birebir uyumlu olmalıdır.
3. Dosya **adı yasağı** olan yüzeyler (kasıtlı yazım hatası, ~yüzlerce wiki-link'e bağlı dizin
   adları) **değiştirilmez**; yasağın gerekçesi dosyanın §1'inde yazılır.
4. Taşıma/silme/üç nokta-değişiklik **yalnız onaylı ADR ile**; onaysız taşıma yasak.
5. Kaynak kod yüzeyleri (`*.php` `*.js` `*.css` `*.sql`) bu şablon kapsamında **yazılmaz**.
6. `git commit` subagent tarafından **atılmaz** — commit orkestratöre aittir.

### §3.2 Kanıt türü sınıflandırması (sayım girdisi)

| Tür | Anlamı | Örnek |
|-----|--------|-------|
| (i) | Diskte doğrudan kanıt — dosyanın kendisi | `shared/src/Database/DatabaseManager.php` |
| (ii) | Vault referans kanıtı — başka bir vault dosyasındaki satır | `plan §2-1`, `index §6.2-8` |
| (iii) | Hedef/plan kanıtı — diskte henüz YOK | ADR kaydı, yol haritası satırı |

**Kural:** kanıt yoksa satır yazılmaz; yalnız (iii) ise satır `PLANNED` zorunlu; kanıt
kaybolursa satır bir sonraki turda düşer.

### §3.3 Etiket üçlüsü (Zero-Hallucination)

- `UNKNOWN` — bilinmiyor, tahmin yazılmaz.
- `⚠️ VERIFICATION REQUIRED` — kısmi kanıt var, tam eşleşme okunmadı.
- `PLANNED` — diskte yok, hedef olarak kayıtlı.
- `REDACTED` — `.env`, key, token, parola hiçbir dosyaya yazılmaz.

### §3.4 Katman yerel istisnaları

Katmana özgü yasak/istisna (ör. `firmware/` = K1.f taşınmaz; yanlış yerleşimli klasör
taşınmaz, sınır notu yazılır) o katman README'sinin **Yerel Kurallar** bölümünde tek tek
listelenir; genel şablonu **değiştirmez**.

---

## §5 Frontmatter Standardı (7 zorunlu alan)

```markdown
---
type: architecture            # README: architecture-readme · detail: architecture · şablon: template
category: layer               # kök: layer · detay: layer-detail · makro: layer-macro · platform: platform
title: "<K{n} — <Ad>"
date: {{YYYY-MM-DD}}
status: active                # içerik: active · stub: planned
version: 1.0.0
authority: SSOT
---
```

| Dosya türü | `type` | `category` | `status` |
|-----------|--------|-----------|----------|
| Katman README (Part A) | `architecture-readme` | `layer` | `active` |
| Detail içerik (Part B) | `architecture` | `layer-detail` | `active` |
| Detail stub (Part B) | `architecture` | `layer-detail` | `planned` |
| Makro türev (`*-master.md`) | `architecture` | `layer-macro` | `active` |
| Bu şablonun kendisi | `template` | `template` | `active` |

> ⚠️ **Açık karar (P2):** Bu seri `date:` alanı kullanır; eski `.templates/documentation/*`
> şablonları `updated:` kullanır. Karar §12'de.

---

## §6 PART A — Katman README Şablonu

### §6.1 Ne işe yarar (üç iş, tek dosya)

1. Katmanın **kimliğini** (K{n}, A grubu, klasör) tek satırda söyler.
2. **Bileşen haritasını** (`K{n}-NN ↔ K{n}.a.b` eşlemesi) kanıt türü (ii) olarak sağlar.
3. **Bağımlılık oklarını** matristen kopyalayarak katman ihlali denetiminin yerel kaynağını
   oluşturur.

### §6.2 Künye bloğu (zorunlu — H1'den hemen sonra)

```markdown
# K{n} — <Katman Adı>

**Künye:** K{n} · Alan: A{0-5} (K{a}-K{b}) · Klasör: `k{n}-<slug>/` · Durum: active
**Bağımlılık (matris kanonik):** K{n} → <hedefler> · <kaynaklar> → K{n}
**İlgili ADR:** [[...]] (varsa — diskte doğrulanmış)
```

### §6.3 Bölüm sırası (silinemez — README)

| # | Bölüm | İçerik |
|---|-------|--------|
| 1 | Künye | K{n}, A grubu, klasör, durum |
| 2 | Amaç | Bu katman neyi sahiplenir (2-4 cümle) |
| 3 | Alt Katmanlar | K{n}.a listesi (2. seviye, 1'den başlar) |
| 4 | Bileşen Haritası | `K{n}-NN ↔ K{n}.a.b` tablosu — **kanıt (ii)** |
| 5 | Bağımlılık Okları | Matristen kopya + ok türü etiketi |
| 6 | Dosya Haritası | Klasördeki md'ler (ad + 1 satır amaç) — **kanıt (i)** |
| 7 | Yerel Kurallar | Sınır/istisna notları (ADR bağlantılı) |
| 8 | Bilinmeyenler | Katman geneli `UNKNOWN/⚠️/PLANNED` özeti |
| 9 | İlgili Dosyalar | Wiki-link'ler `[[relative/path]]` |

### §6.4 Bileşen haritası formatı (kanonik)

```markdown
| Kimlik | Bileşen | Kapsam Kanıtı | Karşılık gelen düğüm | Kanıt türü |
|--------|---------|---------------|----------------------|------------|
| K{n}-01 | <bileşen adı> | <plan §2.x / index §y / CLAUDE §z> | K{n}.a.b | (i)|(ii)|(iii) |
```

**Kural:** her `K{n}-NN` satırında karşılık gelen düğüm yazılır; karşılığı bilinmiyorsa hücreye
`⚠️ VERIFICATION REQUIRED` yazılır — **uydurulmaz**. Sayım birimi **düğüm**dür, satır/satır
sayısı değil.

### §6.5 Bağımlılık okları formatı

```markdown
| Ok | Tür | Kaynak |
|----|-----|--------|
| K{n} → K{m} | bağımlılık \| çağrı \| gösterim | matris §2.x |
```

**Kural:** oklar **yalnız** `katman-baglilik-matrisi` §2'den kopyalanır; matriste olmayan ok
yazılmaz (yeni ok → önce matris satırı, gerekirse ADR). Her ok türü etiketlidir.

### §6.6 README kuralları

1. Adlandırma: düğüm regex'i `^K([0-9]|1[0-9]|20)(\.[1-9][0-9]*){0,3}$`; ara segment 0 yok;
   5. seviye (`K…x.y`) yazım yasağı; küçük-k `k21+` yasak.
2. A etiketi (A0-A5) README'de **raporlamadır**; denetim K düzeyinde yapılır.
3. Dosya silinemez — dosya haritası güncellenir, dosya yok edilmez (onaysız).
4. Yeni/tümdüzeltilen README hedefi **≥500 satır** (bu şablonun kendisi kapsam dışındadır).
5. README, kendi alt katman detail dosyalarından **önce** yazılır (harita önce, derinlik sonra).

### §6.7 README — dolu örnek (iskelet gösterim)

```markdown
---
type: architecture-readme
category: layer
title: "K000 — İşletim Sistemi README"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# K000 — İşletim Sistemi

**Künye:** K000 · Alan: A0 (K0-K5) · Klasör: `K000-isletim-sistemi/` · Durum: active
**Bağımlılık (matris kanonik):** K000 → (en dip — çağırmaz) · K001…K020 → K000 zemini
**İlgili ADR:** ADR-015 (env parser) · ADR-085 (shared hybrid) — `.ai/.decisions/accepted/`

## Amaç
Web platformunun çalışma-zemini: subdomain yerleşimi, PHP 8.4 runtime, composer autoload,
PDO bağlantı zemini. Üst katmanlar K000'ı çağırır; K000 hiçbir katmanı çağırmaz.

## Alt Katmanlar
| Alt | Ad | Not |
|-----|----|-----|
| K000.1 | API barındırma zemini | subdomain + giriş noktaları |
| K000.2 | Core (shared çekirdek) | PSR-4 paket çekirdeği |

## Bileşen Haritası (kanıt ii)
| Kimlik | Bileşen | Kapsam Kanıtı | Karşılık gelen düğüm | Kanıt türü |
|--------|---------|---------------|----------------------|------------|
| K000-01 | Subdomain barındırma | index §2-1 | K000.1 | (i) |
| K000-02 | composer autoload zemini | ADR-085 | K000.2 | (ii) |

## Bağımlılık Okları
| Ok | Tür | Kaynak |
|----|-----|--------|
| K005 → K000 | bağımlılık (skip-edge) | matris §2 |

## Dosya Haritası
| Dosya | Amaç |
|-------|------|
| os-master.md | K0 = K000+K001 makro türevi |

## Yerel Kurallar
- `electroncis/` adı değiştirilmez; içerik K001'e aittir, taşınmaz (onaysız taşıma yasak).

## Bilinmeyenler
- assets.coremusic.net altında composer.json yok — kasıt `UNKNOWN`.

## İlgili Dosyalar
[[../katman-baglilik-matrisi]] · [[isletim-sistemi-core/index]]
```

*(Örnek kurgusaldır; gerçek içerik diskten türetilir ve uydurma satır içermez.)*

---

## §7 PART B — Kırılma/Detail Dosyaları Şablonu

### §7.1 Dosya ağacı (manifest — alt klasör başına 12 dosya)

| # | Dosya | Tür | Band | Bakış açısı (arc42 karşılığı) |
|---|-------|-----|------|-------------------------------|
| 1 | `index.md` | içerik | 90-140 satır | Dizin + klasör özeti (§1-§9) — arc42 §1+§4 |
| 2 | `kapsam.md` | içerik | 80-140 | Sınır: girer/girmez (arc42 §2+§3) |
| 3 | `arayuz.md` | içerik | 80-140 | Girdi/çıktı sözleşmeleri (arc42 §5) |
| 4 | `senaryolar.md` | içerik | 80-140 | Çalışma-zamanı akışları (arc42 §6) |
| 5 | `riskler.md` | içerik | 80-140 | Risk + failure mode + sahip (arc42 §11) |
| 6 | `adr.md` | içerik | 80-140 | Karar envanteri + gerekçe (arc42 §9 / ISO 42010 rationale) |
| 7 | `bilinmeyenler.md` | içerik | 80-140 | UNKNOWN/⚠️/PLANNED envanteri (ISO 42010: inconsistencies) |
| 8 | `yasam-dongusu.md` | içerik | 80-140 | Durum/evre geçişleri (arc42 §6 ikinci görünüm) |
| 9 | `olcum-kriterleri.md` | stub | 30-50 | Kalite senaryoları (arc42 §10) — PLANNED |
| 10 | `test-plani.md` | stub | 30-50 | Test dalı iskeleti — PLANNED |
| 11 | `performans.md` | stub | 30-50 | Ölçüm/eşik — PLANNED, sayı UYDURULMAZ |
| 12 | `entegrasyon.md` | stub | 30-50 | Üst katman köprüleri (arc42 §5 dış sınır) — PLANNED |

**Parametrik istisnalar (yerel not olarak yazılır):**
- Katman kökü: `index.md` (+ varsa makro türev `*-master.md`) §7.1 ağacının dışındadır.
- Alt klasör genişlemesi (ör. platform/tür klasörü: 1 dizin + 8 içerik + karşılaştırma + 4 stub)
  katman yerel notuyla ilan edilir; dosya adı yasağı olanlar **değiştirilmez**.
- Yanlış yerleşim: klasör konumu yanlışsa **taşınmaz**; sınır notu yazılır (yalnız onaylı ADR
  ile taşınır).

### §7.2 Bölüm sırası (silinemez — detail içerik dosyaları 2-8)

| # | Bölüm | İçerik |
|---|-------|--------|
| §1 | Kimlik | 1-2 satır: bu dosya neyi sahiplenir; üst/kardeş wiki-link'ler |
| §2 | Kapsam & Sorumluluk | İÇİN/DIŞI tablosu — her satır disk kanıtlı |
| §3 | Bağımlılık Kuralları | Dependency Rule: alt/üst katman, yasaklar, ihlal → revert + log ERROR |
| §4 | Arayüz | İmza/varlık tablosu `file:line` kanıtıyla; okunmayan imza `UNKNOWN` |
| §5 | Senaryolar | 5-8 akış; her adım `dosya:satır` ile kanıtlı |
| §6 | Risk & Failure Modes | R1…Rn; tetik → etki → sahip; puan/ölçü YOKSA yazılmaz |
| §7 | ADR Bağlantıları | ADR no · başlık (slug'tan) · durum (dosya frontmatter'ından) |
| §8 | Bilinmeyenler | B1…Bn; `UNKNOWN`/`⚠️`/`PLANNED` etiketi + kapanış koşulu |

**Kural:** § adları ve sırası **katmanlar arası değişmez** (aranabilirlik bunun üzerine kuruludur);
içeriği dosya diske göre doldurur.

### §7.3 Kök index.md iskeleti (katman kökü)

§1 Kimlik · §2 Sorumluluk · §3 Bağlantılar (Dependency Rule) · §4 Alt Klasör Dizini
(12 dosyalık tablo) · §5 Okuma Sırası · §6 ADR Bağlantıları · §7 Risk / Not · §8 Bilinmeyenler
· §9 Manifest (dosya listesi — sayım kanıtı).

### §7.4 Stub dosya formatı (9-12)

```markdown
---
type: architecture
category: layer-detail
title: "<klasör>/<dosya>"
date: {{YYYY-MM-DD}}
status: planned
version: 1.0.0
authority: SSOT
---

# <Dosya Adı>

> **PLANNED — içerik YOK ({{DATE}})**

## §1 Kimlik
1-2 satır + üst/kardeş wiki-link.

## §2 Amaç
Bu dosya kapandığında hangi soruyu cevaplayacağı (2-3 madde).

## §3 Planlanan İçerik Dalları
- <dal başlığı> — kanıt kaynağı: <dosya/ADR> (giriş varsa; yoksa yalnız başlık)
- ...

## §4 Bağlantılar
[[../index]] · [[../<kardeş>/index]]
```

**Stub yasağı:** eşik/sayı/yüzde/ölçüm **uydurulmaz**; dal başlıkları kod/ADR kanıtından
türetilebilir.

### §7.5 Detail — dolu örnek (kapsam.md, iskelet gösterim)

```markdown
---
type: architecture
category: layer-detail
title: "K000 — API Barındırma / kapsam"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# Kapsam & Sorumluluk — API Barındırma Zemini

## §1 Kimlik
Bu dosya K000 API subdomain barındırma zemininin sınırını sahiplenir.
Üst: [[../index]] · Kardeş: [[../isletim-sistemi-core/index]]

## §2 Kapsam & Sorumluluk
| Kapsam (İÇİN) | Kanıt | Kapsam Dışı (DIŞI) |
|---------------|-------|--------------------|
| 5 subdomain kökü | disk glob (i) | HTTP yanıt gövdesi üretimi (→ K8-K9) |
| Giriş noktaları index.php/autoload.php | dosya satır (i) | Auth politikası (→ K006) |

## §3 Bağımlılık Kuralları
- Alt katman: yok (en dip zemin).
- Üst katman: K001-K020 bu zemine oturur; ihlal → revert + log ERROR.

## §4 Arayüz
| Varlık | Kanıt | Rol |
|--------|-------|-----|
| `shared/composer.json` | (i) dosya | Paylaşımlı autoload sözleşmesi |
| EnvParser | `shared/src/Config/EnvParser.php:7` (i) | Ortam okuma |

## §5 Senaryolar
S1: İstek → subdomain kökü → index.php (kanıt: dosya:satır) …

## §6 Risk & Failure Modes
R1: .htaccess yokluğu → statik dosya sızıntısı (tetik → etki → sahip)

## §7 ADR Bağlantıları
| ADR | Başlık | Durum |
|-----|--------|-------|
| ADR-015 | Env Parser Strategy | accepted (dosya frontmatter'ı) |

## §8 Bilinmeyenler
- B1: assets kökünde composer.json yok — kasıt `UNKNOWN`.
```

*(Örnek kurgusaldır; gerçek satırlar görev içinde glob/grep ile doğrulanır.)*

### §7.6 Pilot yerel uygulama notu (K000-isletim-sistemi — 2026-10-10)

Bu şablonun ilk uygulaması `K000-isletim-sistemi` klasörüdür; yerel istisnalar (§3.4) orada
şöyle ilan edilmiştir:

| Yerel not | Karar |
|-----------|-------|
| Kök iskelet | `index.md` + `os-master.md` (K0 = K000+K001 makro türevi) = **2 dosya** |
| 7 alt klasör | `electroncis` · `isletim-sistemi-api` · `isletim-sistemi-core` · `isletim-sistemi-kernel` · `isletim-sistemi-cross` · `isletim-sistemi-security` · `isletim-sistemi-plan` — her biri **12 dosya** (§7.1 manifesti) |
| `isletim-sistemi-turu/` | **14 dosya**: `index.md` (dizin) + 8 platform (`windows` `linux` `macos` `ios` `andorid`* `tizen` `rasberry-pi-6`→`raspberry-pi-6` `web`) + `platform-karsilastirma.md` + 4 stub (`ses-yollari` `donanim-uyumluluk` `oturum-ses-yollari` `kurulum-ortamlari`) |
| Dosya adı yasakları | `electroncis/` (yazım hatası kasıtlı) ve `andorid.md` **değiştirilmez** — yüzlerce wiki-link buna bağlı; taşıma onaysız yasak |
| Yanlış yerleşim | Elektronik içeriği K001'e aittir (ADR-061 L6) ama dizin K000 altında kalır → sınır notu olarak yazılır |
| Kapsam toplamı | 2 (kök) + 7×12 (alt klasör) + 14 (turu) = **100 dosya** (manifest — sayım kanıtı, 2026-10-10) |

\* `andorid.md` yazım hatası bilinçli korunur (referans 0 iken adlandırılmadı; rapor edilir,
düzeltme yapılmaz).

---

## §8 Wiki-Link Kuralları

1. Biçim: göreli `[[...]]` — ör. `[[../index]]`, `[[../<kardeş-klasör>/index]]`,
   `[[../katman-baglilik-matrisi]]`.
2. Yalnız **diskte var olduğu doğrulanmış** hedeflere link verilir; doğrulanmayan hedefe link
   = kırık link üretmek = kusur.
3. Şablon değişkeni görünümü (`[[C]]`, `[[V]]`) içerik dosyasına **konmaz** — bilinen 6 sahte
   kırık link kuralına dokunulmaz, sayı 6'da kalır.
4. Bağlantı kırılırsa: dosya adı değiştirilmez, link düzeltilir; ad yasağı olan hedefler için
   link değil düz metin kullanılır.
5. Her toplu yazımdan sonra wiki-link denetimi çalıştırılır; raporlanan kırık ≠ gerçek kırık —
   sahte kırıklar §13.7 listesiyle elle ayrıştırılır.

## §9 Workflow (üretim sırası)

```text
[1] ŞABLONU SEÇ (bu dosya — Part A / Part B)
[2] DOSYA MANIFESTİNİ KUR (klasörde ne var? ls/glob — uydurma dosya yok)
[3] ÖN-ÖLÇÜM (glob/grep: dosya, satır, ADR durumları — bugünün sayısı)
[4] README ÖNCE (Part A): künye → bileşen haritası → oklar → dosya haritası
[5] DETAIL SONRA (Part B): index → kapsam → arayuz → senaryolar → riskler → adr
    → bilinmeyenler → yasam-dongusu → 4 stub
[6] DENETİM (§10 checklist) — wiki-link glob, frontmatter 7 alan, band, manifest
[7] KAPANIŞ (append-only): log.md 1 satır · session kaydı
[8] COMMIT — yalnız orkestratörde; subagent ATMAZ
```

**Sıra kuralı:** README (harita) detail'den (derinlik) önce yazılır; detail dosyaları kendi
README'sinden sonra. Eşzamanlı yazım yasak (context lock).

## §10 Doğrulama Checklist'leri

### §10.1 README (Part A)

- [ ] Frontmatter 7 alan · `type: architecture-readme`
- [ ] Künye: K{n} + A grubu + klasör birebir eşleşiyor (A0=K0-K5 … A5=K16-K20)
- [ ] §6.3'teki 9 bölüm eksiksiz, sıra korunmuş
- [ ] Bileşen haritası: her satırda `K{n}-NN ↔ K{n}.a.b`; uydurma düğüm 0
- [ ] Bağımlılık okları matriste birebir mevcut, ok türü etiketli, matriste olmayan ok 0
- [ ] Regex: `K\d+(\.\d+){4,}` = 0 · ara-segment-0 = 0 · küçük-k = 0
- [ ] Dosya haritası = gerçek ls çıktısı (başlık ≠ içerik 0)
- [ ] Wiki-link hedefleri glob'landı → gerçek kırık 0 (sahte kırıklar §13.7 dahil değil)
- [ ] ≥500 satır (yeni/tümdüzeltilen) · dosya silinmedi

### §10.2 Detail (Part B)

- [ ] Frontmatter 7 alan · `status` içerik=active / stub=planned tutarlı
- [ ] §7.2 sırası silinmedi · § adları katmanlar arası aynı
- [ ] Her olgu iddiasında `file:line` kanıtı · etiketler yerinde
- [ ] Stub'da tek bir sayı/eşik yok
- [ ] Satır bandı: içerik 80-140 · stub 30-50 · kök index 90-140
- [ ] Manifest dışı dosya üretimi 0 · kaynak kod 0 değişiklik · commit 0
- [ ] Klasör toplamı = manifest (12; parametrik istisna §7.1 ilanlı)

## §11 Sık Hatalar (bu şablon bunları engeller)

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Eski vault ölçümünü kopyalamak | Bayat sayı, denetim sahteliği | §9 adım-3 ön-ölçüm |
| 2 | Okunmayan method imzasını yazmak | Uydurma API | §3.3 etiket üçlüsü |
| 3 | Matriste olmayan bağımlılık oku eklemek | İhlal denetimi bulanıklaşır | §6.5 yalnız matristen kopya |
| 4 | Stub'a hedef sayı koymak | Ölçülmemiş hedef gerçek görünür | §7.4 stub yasağı |
| 5 | Var olmayana wiki-link vermek | Kırık link üretimi | §8.2 doğrulama zorunlu |
| 6 | README/detail eşzamanlı yazmak | Dosya yarışması, çelişen harita | §9 context lock |
| 7 | Subagent'ın commit atması | Onaysız tarihçe | §3.1-6 |
| 8 | Klasör adını "düzeltmek" | Yüzlerce link kırılır | §3.1-3 ad yasağı |

## §12 Karar Noktaları (kullanıcı onayı bekliyor)

| # | Karar | Seçenekler | Öneri |
|---|-------|-----------|-------|
| D1 | Frontmatter tarih alanı | `date:` (bu seri) vs `updated:` (eski şablonlar) | `date:` |
| D2 | README satır bandı | ≥500 (bu şablon) vs ≥500 + detail 80-140 (uygulanan) | İkisi birlikte (§6.6-4 + §7.1) |
| D3 | Bölüm seti | 8 § detail (uygulanan) vs arc42 12 § birebir | 8 § + arc42 eşlemesi §7.1'de |
| D4 | Eski şablonlar | `katman-readme-template.md` + `alt-katman-template.md` ile çakışma → birleştirilip arşivlensin mi? | Evet — bu dosya tek SSOT olsun |

## §13 Sürüm & Kayıt

- v1.0.0 — 2026-10-10 — sıfırdan yazım (README + detail birleşik genel şablon);
  yöntem referansı: arc42 + ISO/IEC/IEEE 42010 (yalnız yöntem, olgu değil).
- Registry: kararlar (D1-D4) onaylanınca `.ai/.templates/index.md`'ye 1 satır eklenir.

---

## §14 Sözlük (terimler — tek satır tanımlar)

| Terim | Tanım |
|-------|-------|
| Katman (K{n}) | K0-K20 numaralı mimari katman; bağımlılık zincirinin bir halkası |
| Alan (A0-A5) | K katmanlarının grup etiketi (A0=K0-K5 … A5=K16-K20) — raporlama etiketi |
| Düğüm | Sayım birimi: bir dosyanın sahiplendiği mimari varlık (satır değil) |
| Künye | README'nin H1 sonrası kimlik bloğu (K{n}, A, klasör, durum) |
| Bileşen haritası | `K{n}-NN ↔ K{n}.a.b` eşlemesi — sayım kanıtı (ii) |
| Bağımlılık oku | Matristen kopyalanan, türü etiketli katman yönü (bağımlılık/çağrı/gösterim) |
| skip-edge | Üst katmanın bir üstü atlayıp dip katmana bağlandığı kenar (istisna kaydı) |
| Kanıt (i)/(ii)/(iii) | Disk / vault referans / hedef-plan kanıt türü |
| Zero-Hallucination | Doğrulanmayan iddia yazılmaz; `UNKNOWN`/`⚠️`/`PLANNED` ile işaretlenir |
| Manifest | Bir görevin üretmesi izinli dosya listesi; dışı üretim yasak |
| Stub | `status: planned`, içeriği henüz yazılmamış, bandı 30-50 satır dosya |
| Kırılma/Detail | Katmanı dallandıran 12 dosyalık alt dosya seti (Part B) |
| README | Katman kimliği + haritası + okları taşıyan üst dosya (Part A) |
| Guardrail #16 | Şablon zorunluluğu kuralı: şablonsuz üretilen dosya geçersizdir |
| Context lock | Eşzamanlı dosya yazımını engelleyen kilit (README önce, detail sonra) |
| Viewpoint (ISO 42010) | Paydaş + ilgi alanı → hangi model türüyle bakılacağı (yöntem terimi) |
| Correspondence (ISO 42010) | View'lar arası karşılıklılık/kayıt — çelişkiler kaydedilir |
| arc42 | 12 bölümlük pratik mimari dokümantasyon iskeleti (yöntem referansı) |
| SSOT | Single Source of Truth — çelişkide tek otorite dosya kazanır |
| frozen ADR | Değiştirilemez ADR — okunur, referans edilir, düzenlenmez |
| onaysız taşıma | ADR/onay olmadan dosya/dizin adı veya konumu değiştirme — yasak |
| REDACTED | Sır/anahtar/parolanın dosyaya yazılmama politikası |
| Parametrik istisna | Şablonu değil, yalnız o katmanı bağlayan yerel kural (§3.4) |
| Sayım girdisi | Denetimde dosya/düğüm sayısına giren kanıt satırı |

---

**Registry notu:** Bu şablon `.ai/.templates/index.md`'ye kayıtlıdır; şablonsuz katman
README/detail dosyası üretilmez (Guardrail #16).

*Katman Dosya Şablonu v1.0.0 — Authority: Bayram Ali / Vault Steward — 2026-10-10*
*Mode: Red Team · Human Mode · Truth Mode*
