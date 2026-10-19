---
title: "CoreMusic — Dependency Graph (Kenar Okuma Kuralı)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §4.5 (path-ID) · §4.3 (tek verilmiş kenar) · §4.2 (depends_on alanı) · §5.2 (kanıt→doğrulama)"
group: mimari
---


# Dependency Graph — Bağımlılık Okuma Kuralı

> Bu dosya **kenar (edge) verisini** taşır: hangi K hangisine `depends_on`.
> v2 spec'te bugün **yalnızca tek bir kayıtlı kenar çifti var** (§4.3) — kalanı iskelettir.

## §A Okuma Kuralı — Kademe ve Adres

**Kenar tanımı (v2 §4.2, zorunlu alan):** `depends_on` = "Hangilerine muhtaç
(bağımlı olduğu düğümler)" · `evidence` = "gerçek dosya yolu, link, test kaydı (tahmin YASAK)".

**Yol adresi (v2 §4.5 — Yöntem A, önerilen):**

```text
  YÖNTEM A — YOL ADRESİ (path ID) — ÖNERİLEN, çakışma imkânsız

  K003-audio                        ← kademe 1 · domain
      └── /routing                  ← kademe 2 · subdomain
             └── /transport         ← kademe 3 · bounded context
                    └── /udp        ← kademe 4 · module
                           └── /jitter-buffer    ← kademe 5 · component
                                  └── /enqueue   ← kademe 6 · service
                                         └── enqueue.js  ← kademe 8 · implementation

  YÖNTEM B — NOKTALI ADRES (alternatif)

  K003.02.01.05.003     =  K003 → routing → transport → udp → bileşen #3
```

**Okuma kuralı:** bir ok `A → B` okunduğunda anlam `A, B'ye muhtaçtır` olur;
ok yönü = bağımlılık yönüdür. Kademe-1 ID `K0xx-<türkçe-ad>`, kademe-2+ ID =
üst ID + `/` + alt ad (v2 §4.5 tablosu).

## §B Bilinen Kenarlar (v2 §4.3 — tek VERİLMİŞ veri)

```yaml
# v2 §4.3 · K003-audio boş şablonundan (kaynaktaki hâli — dolgu değil)
depends_on:
  - K001-donanım
  - K004-dsp
```

```text
┌────────────────────────────────────────────────────────────┐
│ KENAR DURUMU — v2 §4.3 itibarıyla                          │
├────────────────────────────────────────────────────────────┤
│                                                            │
│ K003-audio ──depends_on──→ K001-donanım   ✅           §4.3 │
│      │                                                     │
│      └──depends_on──→ K004-dsp   ✅                    §4.3 │
│                                                            │
│ K000 · K001 · K004 … K019 · 6× K020                        │
│ (kalan 24 domain) → kenar verisi v2'de YOK                 │
│                                                            │
└────────────────────────────────────────────────────────────┘
```

## §C Kenar İskeleti — Boş Alanlar

| Durum | Domain | Kaynak |
|---|---|---|
| ✅ kenarlı | **K003-audio** → K001-donanım · → K004-dsp | v2 §4.3 |
| ⏳ iskelet (açık kenarı YOK) | K000-isletim-sistemi · K001-donanım · K004-dsp · K005-firmware · K006-yapay-zeka … K019-testing · 6× K020 | **toplam 24 domain** |

⚠️ **VERIFICATION REQUIRED: kenar verisi Band-1 üretiminden doldurulacak.**
Ayrıca v2 §4.3 açık sorusu: `K003 ile diskteki K003-ses-motoru aynı mı?
→ ID CONFLICT — VERIFY REQUIRED`. 24 domain için `depends_on` alanı v2'de
**henüz yazılmadı** → tahmin edilip grafiğe konmaz.

```text
   BAĞLANTI DURUMU — Band-1 (25 klasör · 19 tekil K + 6× K020)

   KENARLI (1 domain · 2 kenar)               KENARI OLMAYAN (24 domain)
   ────────────────────────────               ──────────────────────────
   K003-audio ──→ K001-donanım                K000-isletim-sistemi
   K003-audio ──→ K004-dsp                    K001-donanım
                                              K004-dsp · K005-firmware …
                                              K019-testing · 6× K020
                                              (band-1 üretiminde dolar)
```

## §D Kenar Doğrulama Zinciri (v2 §5.2)

```text
                     ┌──────────────────┐
                     │  ARCHITECTURE KG │
                     └────────┬─────────┘
                              │
       ┌──────────────────────┼──────────────────────┐
       ▼                      ▼                      ▼
   K-LAYERS                DOMAINS                  CODE
       │                      │                      │
       ▼                      ▼                      ▼
  COMPONENTS          BOUNDED CONTEXTS              FILES
       │                      │                      │
       └──────────────────────┼──────────────────────┘
                              ▼
                          EVIDENCE
                              │
                 ┌────────────┼────────────┐
                 ▼            ▼            ▼
              REPO          ADR          TEST
                 │            │            │
                 └────────────┼────────────┘
                              ▼
                          VALIDATION
```

## Lejant

| Sembol | Anlam |
|---|---|
| `A ──depends_on──→ B` | **kenar** — A, B'ye muhtaçtır (tek yön) |
| `──?── / ⏳` | **iskelet** — kenar henüz verilmemiş (uydurulmaz) |
| `✅ / ✓` | v2 spec'te açıkça yazılmış kenar |
| `← kademe n` | path-ID derinliği (v2 §4.5) |
| `EVIDENCE / VALIDATION` | kanıt ve doğrulama katmanı (v2 §5.2) |
| ⚠️ | `VERIFICATION REQUIRED` — doldurulmadan sayıma/kanıta katılmaz |

## Kaynak Atfı

- **v2 §4.5** "K5000+ İÇİN KİMLİK (ID) BİÇİMİ" → §A path-ID örneği (birebir).
- **v2 §4.3** "Örnek kayıt (boş şablon — K003-audio)" → §B tek verilmiş `depends_on` verisi.
- **v2 §4.2** "Her kayıt için ZORUNLU alanlar" → §A kenar tanımı (`depends_on` · `evidence`).
- **v2 §5.2** "Kanıt → Doğrulama Zinciri" → §D (birebir taşındı; enterprise setine taşınan bölüm).
- Eş kaynak: `.ai/architecture/index.md` §9 B fazı (Band-1 üretimi — kenarlar buradan gelecek).

## Durum Notu

- `status: draft` — **bu tamamlanmış bir graf değildir**; 2 kanıtlı kenar + iskelet taşır.
- **Kenarlar doldukça genişler:** her `index.md` `depends_on` alanı dolduğunda
  §C'deki ⏳ satırları bir kenar kazanır; **dolgu (uydurma kenar) yasaktır**.

---

## §E Dürüstlük Bloğu (Append-Only Derinleştirme Uyarısı — 2026-10-13)

> ⚠️ **KANIT ORANI DEĞİŞMEMİŞTİR.** Bu ek blok (`§E`–`§L`) append-only derinleme
> ile **sona** eklenmiştir; önceki hiçbir satır silinmemiş, değiştirilmemiştir.
>
> | Kanıt kalemi | Derinleme öncesi | Derinleme sonrası |
> |---|---|---|
> | Kayıtlı `depends_on` kenarı | **2** (K003→K001 · K003→K004 — v2 §4.3) | **2** (değişmedi) |
> | `depends_on` yazılmamış domain | **24** (iskelet — §C) | **24** (değişmedi) |
> | Bu derinlemede eklenen düğüm / kenar / kademe | — | **0** (grafik üretilmedi) |
> | Bu derinlemede eklenen URL / yüzde / ölçüm / benchmark | — | **0** (dış kaynak kullanılmadı) |
>
> **Uyarı:** `§E`–`§L` *yorum, yönerge ve okuma rehberi* katmanıdır — yeni kanıt
> **iddia etmez**. Bir cümle kanıt iddia ediyorsa dayanağı yalnız `§A–§D` içindeki
> v2 § referansıdır. Kaynakta olmayan her şey `⚠️ VERIFICATION REQUIRED` veya
> `UNKNOWN` ile işaretlidir; yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) **kullanılmamıştır**.
> §C'deki **dolgu kenar yasağı** bu derinlemede de aynen geçerlidir.

## §F §A Derinlemesine Okuma — Adresleme ve Kademe

### §F.1 Yöntem A ↔ Yöntem B karşılaştırması (yalnız §A'daki iki yöntem)

| Kriter | Yöntem A — yol adresi (path ID) | Yöntem B — noktalı adres |
|---|---|---|
| Örnek (§A, birebir) | `K003-audio` → `/routing` → `/transport` → `/udp` → `/jitter-buffer` → `/enqueue` → `enqueue.js` | `K003.02.01.05.003` |
| Kademe okunabilirliği | `/` ayracı ile okunur | `.` ayracı ile okunur |
| v2 §4.5'teki durum | **önerilen** (Yöntem A) | alternatif (Yöntem B) |
| Çakışma iddiası | kaynakta: "çakışma imkânsız" (v2'nin kendi ifadesi) | kaynakta çakışma yorumu **okunmadı** → `UNKNOWN` |
| Kademe-2+ adlandırma | üst ID + `/` + alt ad | bu dosyaya alınan bölümde **geçmiyor** → `⚠️ VERIFICATION REQUIRED` |
| Bu dosyadaki yeri | §A'da birebir taşındı | §A'da birebir taşındı |

> Not: "çakışma imkânsız" ifadesi **v2 §4.5'in iddiasıdır**; bu dosya o iddiayı
> devralır, bağımsız doğrulama **iddia etmez**.

### §F.2 §A zincirinin kademe satırları (kaynaktaki hâli — düzeltilmedi)

| Kademe (§A yazımı) | Örnek öge | §A'nın verdiği tür |
|---|---|---|
| kademe 1 | `K003-audio` | domain |
| kademe 2 | `/routing` | subdomain |
| kademe 3 | `/transport` | bounded context |
| kademe 4 | `/udp` | module |
| kademe 5 | `/jitter-buffer` | component |
| kademe 6 | `/enqueue` | service |
| kademe 7 | — (kaynakta **atlanmış**) | `⚠️ VERIFICATION REQUIRED` |
| kademe 8 | `enqueue.js` | implementation |

⚠️ **VERIFICATION REQUIRED:** §A zincirinde kademe 6'dan doğrudan **kademe 8'e**
geçilir; kademe 7'nin adı v2 §4.5'ten bu dosyaya alınan bölümde **yoktur**.
Ara kademe uydurulmaz (ne `database`, ne `repository`, ne `class` — kaynakta
yazmadığı yazılmaz).

### §F.3 Doğru okuma örnekleri (yalnız §B'deki verilmiş kenar üzerinden)

| Grafikteki | Doğru okuma | Dayanak |
|---|---|---|
| `K003-audio ──depends_on──→ K001-donanım` | "K003-audio, K001-donanıma muhtaçtır" | v2 §4.3 + §A okuma kuralı |
| `K003-audio ──depends_on──→ K004-dsp` | "K003-audio, K004-dsp'e muhtaçtır" | v2 §4.3 + §A okuma kuralı |
| her iki ok | yön = bağımlılık yönü; **tektir** | §A: "ok yönü = bağımlılık yönüdür" |

### §F.4 Yaygın yanlış okuma biçimleri (bu dosyada YASAK)

| # | Yanlış okuma | Neden yanlış | Doğrusu |
|---|---|---|---|
| 1 | "K001, K003'e muhtaçtır" (oku ters çevirmek) | §A: ok yönü = bağımlılık yönü | `K003 → K001` |
| 2 | "K004, K003'ü çağırıyor/besliyor" | kenar `depends_on`'dur, çağrı zinciri **değildir** | muhtaçlık: K003 → K004 |
| 3 | "K000 de K003'e bağlı, çizelim" | §C: 24 domainin kenarı v2'de **yok** | ⏳ iskelet — çizilmez |
| 4 | "24 iskelet domain için makul kenar yazalım" | §C: **dolgu (uydurma kenar) yasaktır** | Band-1 üretimine kadar beklenir |
| 5 | "path-ID'de kademe 7 şudur" | v2 §4.5'te bu kademe bu dosyaya alınmadı | `⚠️ VERIFICATION REQUIRED` |
| 6 | "K003-audio = diskteki K003-ses-motoru" | §C: bu soru v2 §4.3'te açık → ID CONFLICT | `⚠️ VERIFICATION REQUIRED` |
| 7 | "24 iskelet + 1 kenarlı = 25 domain, hepsinin kenarı var" | kenarlı **yalnız 1** domain (§B) | 1 kenarlı · 24 kenarsız |
| 8 | "Grafikte üç kol var, üçü de kenardır" | kol akışı ≠ `depends_on` (bkz. §I enterprise notu) | gerçek kenarlar bu dosyadadır |

## §G §B–§C Derinlemesine — Bilinen Kenarların Profili

### §G.1 Tek kenarlı domain: K003-audio bağımlılık profili

| Alan | Değer | Kaynak |
|---|---|---|
| Çıkan kenar sayısı | **2** | v2 §4.3 |
| Hedef 1 | `K001-donanım` | v2 §4.3 |
| Hedef 2 | `K004-dsp` | v2 §4.3 |
| Giren kenar (K003'e muhtaç olanlar) | `UNKNOWN` — ters kenar v2 §4.3'te **yazılmadı** | §B · §C |
| K003'ün kademe-2+ path-ID kaydı | `UNKNOWN` — bu dosyada kademe-2+ kenar **yok** | §A |
| Kendi `depends_on` alanı dolu başka domain | **0** (diğer 24 domainde alan yazılmadı) | §C |

### §G.2 Ters kenar (inbound) sorusu

v2 §4.3 yalnız **K003'ün** `depends_on` alanını verir; başka bir domainin
`depends_on` içinde K003'ü andığı bir kayıt bu dosyaya alınmamıştır:

- **K003'e kim muhtaçtır?** → `UNKNOWN` (kaynakta yok — tahmin yasak).
- **K003 kime muhtaçtır?** → `K001-donanım` · `K004-dsp` (2 kenar · ✅).
- Grafikte giren ok çizmek = uydurma kenar → **yasak** (§C).
- `K001` ve `K004` "hedef" olarak görünür, ama **kendi** `depends_on` alanları
  v2'de yazılmadı → bu iki domain için de giden ok **yoktur** (⏳).

### §G.3 Kenarsız 24 domain envanteri (iskelet dökümü — tek tek)

> Bu 24 satır §C tablosunun dökümüdür. Domain adları set içindeki
> `domain-architecture.md` §A ağacıyla **aynıdır** (set içi çapraz okuma);
> her satırın kenar durumu tek ve aynıdır: v2 spec'te `depends_on` **yazılmadı**.

| # | Domain | Kenar durumu | İşaret |
|---|---|---|---|
| 1 | K000-isletim-sistemi | kendi kenarı yazılmadı | ⏳ |
| 2 | K001-donanım | yalnız **hedef** (K003→K001); kendi kenarı yok | ⏳ |
| 3 | K004-dsp | yalnız **hedef** (K003→K004); kendi kenarı yok | ⏳ |
| 4 | K005-firmware | kendi kenarı yazılmadı | ⏳ |
| 5 | K006-yapay-zeka | kendi kenarı yazılmadı | ⏳ |
| 6 | K007-data | kendi kenarı yazılmadı | ⏳ |
| 7 | K008-domain | kendi kenarı yazılmadı | ⏳ |
| 8 | K009-security | kendi kenarı yazılmadı | ⏳ |
| 9 | K0100-identitiy | kendi kenarı yazılmadı | ⏳ |
| 10 | K011-application | kendi kenarı yazılmadı | ⏳ |
| 11 | K012-services | kendi kenarı yazılmadı | ⏳ |
| 12 | K013-integraion | kendi kenarı yazılmadı | ⏳ |
| 13 | K010-network | kendi kenarı yazılmadı | ⏳ |
| 14 | K014-media | kendi kenarı yazılmadı | ⏳ |
| 15 | K015-AI | kendi kenarı yazılmadı | ⏳ |
| 16 | K017-observability | kendi kenarı yazılmadı | ⏳ |
| 17 | K018-devops | kendi kenarı yazılmadı | ⏳ |
| 18 | K019-testing | kendi kenarı yazılmadı | ⏳ |
| 19 | K020-adr | kendi kenarı yazılmadı | ⏳ |
| 20 | K020-comilance | kendi kenarı yazılmadı | ⏳ |
| 21 | K020-comere | kendi kenarı yazılmadı (içerik de `UNKNOWN` — domain-architecture §A) | ⏳ |
| 22 | K020-licence | kendi kenarı yazılmadı | ⏳ |
| 23 | K020-discovery | kendi kenarı yazılmadı | ⏳ |
| 24 | K020-recomenataion | kendi kenarı yazılmadı | ⏳ |

> Not: `K002` bu listede **yoktur** — ağaçta hiç yok (ID GAP, domain-architecture
> §A); var olmayan klasöre kenar **atanmaz**.

### §G.4 Kenar ekleme protokolü (Band-1'de bir `depends_on` dolduğunda)

Bu dosyanın genişleme kuralı — sırası zorunlu, atlanan adım = kanıtsız kenar:

1. **Kaynak bul:** hedef domainin kendi `index.md` `depends_on` alanı (Band-1 üretimi).
2. **Yolu doğrula:** dosya yolu / link / test kaydı (`evidence` alanı — v2 §4.2; tahmin yasak).
3. **Yönü yaz:** `A → B` = "A, B'ye muhtaç"; yönü ters çevirmek yok (§A).
4. **ID'yi adresle:** kademe-1 `K0xx-<türkçe-ad>`; alt kademe üst ID + `/` + alt ad (v2 §4.5 Yöntem A).
5. **§B'ye ekle:** kenar satırını `Bilinen Kenarlar` bloğuna **append** et.
6. **§C'yi güncelle:** ilgili domainin ⏳ satırı ✅'ye döner; tablo sayımı değişir.
7. **Çelişki çıkarsa dur:** `K003-audio ↔ K003-ses-motoru` gibi ID CONFLICT →
   `⚠️ VERIFICATION REQUIRED` yaz, grafikte **birleştirme**, üst karar iste.
8. **Doğrulama zincirini işlet:** §D — EVIDENCE → REPO/ADR/TEST → VALIDATION.
9. **Sayıyı ölç:** kenar sayısı yalnız **dolu alan** üzerinden sayılır; iskelet sayılmaz.
10. **Commit atma:** bu dosya değişikliğinin commit'i orkestratöre aittir.

## §H §D Derinlemesine — Doğrulama Zinciri Düğümleri

§D şemasındaki her kutunun bu dosyadaki anlamı (yorum katmanı — yeni kenar iddiası yok):

| Düğüm | Bu dosyadaki anlamı | Anlamı **değil** |
|---|---|---|
| ARCHITECTURE KG | mimari bilgi kümesi — bu dosyanın da içinde bulunduğu set | tek bir çizim dosyası |
| K-LAYERS | K kademe yapısı (K000–K5000 band cetveli) | kenar verisi kaynağı |
| DOMAINS | K domainleri (Band-1: 25 klasör) | dosya sistemi taraması sonucu |
| CODE | gerçek kaynak kod yüzeyi | tahmini kod yapısı |
| COMPONENTS | kademe-5 bileşenler (path-ID ile) | klasör adları |
| BOUNDED CONTEXTS | kademe-3 sınırlı bağlam (path-ID ile) | mikroservis sayısı |
| FILES | kanıt dosyaları (`evidence` gerçek yolu) | var olmayan yollar |
| EVIDENCE | v2 §4.2'nin zorunlu alanı: gerçek dosya yolu · link · test kaydı | yorum/varsayım |
| REPO | depo içi kanıt (dosya yolu ile) | dış web sayfası |
| ADR | karar kaydı kanıtı (`.ai/.decisions/` — set dışı yüzey, **bu derinlemede okunmadı**) | kanıt yerine geçmez, kanıtı **destekler** |
| TEST | test kaydı kanıtı | geçtiği iddia edilen test |
| VALIDATION | üç kanıt kolunun birleşimiyle doğrulama | tek başına "doğru" garantisi |

**§D zincirinin bu dosyadaki durumu:** K-LAYERS → DOMAINS → CODE kollarının
**henüz dolu olduğu iddia edilemez** — v2 §4.3 itibarıyla kanıtlı kenar **2**'dir;
kalan her şey Band-1 üretimine kadar `⏳` / `⚠️ VERIFICATION REQUIRED`.

## §I Diyagram Seti Çapraz Referans (5 dosya)

> Set içi çapraz okuma — beş dosya `domain-architecture.md` §A ağacında listelenen
> `diagrams/` klasörünün tamamıdır.

| Dosya | Setteki rolü | Bu dosyayla ilişkisi |
|---|---|---|
| `enterprise-architecture.md` | canonical graf + hiyerarşi (§5.1 · §1.2) | grafik akışı ≠ kenar; **gerçek kenarlar buradadır** (o dosyanın Lejant notu ile aynı) |
| `domain-architecture.md` | 25 klasör ağacı + band cetveli | 24 iskelet domainin **adları** oradadır (§G.3 adları oradan) |
| `runtime-architecture.md` | RUNTIME/PROCESS kolu + K1–K9 okuma sırası | okuma sırası ≠ bağımlılık yönü — kapılar kenar **değildir** |
| `hardware-audio-architecture.md` | K000/K001/K003/K004/K005 blokları | §B'deki 2 kenarın **blok-görseli** oradadır |
| **bu dosya** | kenar (edge) verisi + okuma kuralı + doğrulama zinciri | setin tek `depends_on` taşıyıcısı |

**Önerilen okuma sırası (yorum katmanı — v2 iddiası değildir):** enterprise →
domain → runtime → hardware-audio → dependency-graph. Gerekçe: önce şekil, sonra
birim, sonra hareket, sonra somut blok, en sonda kanıtlı kenar.

## §J Yorumlama & Anti-Fabrication Kuralları (bağlayıcı)

1. Kenar **yalnız** v2 §4.3 veya dolu `index.md depends_on` alanından gelir.
2. Ok yönü = muhtaçlık yönü; **ters çevrilmez**.
3. `depends_on` ≠ çağrı ≠ sıra ≠ veri akışı — üçü birbirine **karıştırılmaz**.
4. 24 iskelet domain ⏳ kalır; "geçerli görünen" kenar da yasaktır.
5. Kademe numarası kaynakta yoksa `⚠️ VERIFICATION REQUIRED` — ara kademe **uydurulmaz**.
6. `K003` ID çakışması çözülmeden grafikte **eşleştirilmez**.
7. Yüzde/istatistik yalnız **sayılmış** veriden yazılır (bugün: 2 kenar · 24 iskelet).
8. Dış URL eklenmez; eklenirse `Invoke-WebRequest -Method Head` ile 200 doğrulanır —
   bu derinlemede **hiç URL kullanılmadı** (0/0).
9. Yeni § eklemek serbesttir; **eski satır silmek/düzenlemek yasaktır** (append-only).
10. Yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) kullanılmaz; belirsizlik = `UNKNOWN`.
11. Set içi çelişkide `.ai/architecture/index.md` (bu derinlemede **okunmadı**) üst
    karara kadar bekletilir — çelişki **sessizce** çözülmez.
12. `git commit` bu dosyada **atılmaz** (orkestratöre aittir).

## §K SSS — Sık Sorulan Sorular

**S1: Graphviz/mermaid için hazır çıktı var mı?**
Yok. Bu dosya kenar **verisini** taşır; biçimsel grafiğe dönüştürme Band-1'de
kenarlar dolduktan sonra yapılır. Şu an döndürmek = iskeleti kanıt gibi göstermek.

**S2: 24 domain için "yaklaşık" kenar yazsak olur mu?**
Hayır — §C dolgu (uydurma) kenarı yasaklar; tahmin grafiğe **konmaz**.

**S3: `K001-donanım` neden hem hedef hem iskelet?**
Çünkü K003 ona muhtaç (✅ hedef), ama K001'in **kendi** `depends_on` alanı
v2'de yazılmadı (⏳ iskelet). İkisi farklı alanlardır, çelişki değildir.

**S4: Doğrulama zinciri şimdiden işletilebilir mi?**
Yalnız **2 kenar** için: v2 §4.3 kaydı zaten `evidence` içermektedir (kayıt =
kanıt zincirinin kaynağı). Kalan 24 domain için zincirin ilk halkası (EVIDENCE)
boştur → `⚠️ VERIFICATION REQUIRED`.

**S5: Kademe 7 neden yok?**
Kaynak bu dosyaya alınan bölümde atlanmıştır; adı `UNKNOWN`. Uydurmak yerine
işaretli bırakmak doğru davranıştır.

**S6: Yöntem B (noktalı) neden önerilmiyor?**
v2 §4.5 Yöntem A'yı **önerilen** ilan eder; bu dosya kaynağa sadıktır. Yöntem B
hakkında ek iddia (ör. çakışma analizi) `UNKNOWN`'dur.

**S7: K020'ler tek domain mi, altı domain mi?**
Klasör olarak **6 ayrı K020** vardır (domain-architecture §A); hepsi tekil K
sayımına dahil **olmadığından** sayım "19 tekil K + 6× K020 = 25"tir. İçerik
`K020-comere` için `UNKNOWN` işaretlidir.

**S8: Bu dosya `index.md` ile aynı bilgiyi mi taşıyor?**
Hayır — bu dosya kenar **okuma kuralını** ve kanıtlı kenar **kaydını** taşır;
envanter/band cetveli `domain-architecture.md`'dedir. `index.md` bu derinlemede
**okunmadı** (beyazliste dışı yüzey).

**S9: Eklenen bölümler kanıt sayısını artırdı mı?**
Hayır — §E tablosu kanıt oranını **0/0 değişiklik** ile kayıt altına alır:
2 kenar → 2 kenar, 24 iskelet → 24 iskelet.

## §L Terim ve Sembol Ek Açıklamaları (Lejant devamı)

| Terim | Bu dosyadaki anlamı | Belirsiz tarafı |
|---|---|---|
| `depends_on` | v2 §4.2 zorunlu alan: "hangilerine muhtaç" | yalnız dolu alan geçerli |
| `evidence` | gerçek dosya yolu · link · test kaydı (tahmin YASAK) | kaynakta olmayan = yok |
| kenar (edge) | tek yön: `A → B` | yön tersine çevrilemez |
| iskelet (⏳) | `depends_on` alanı henüz yazılmamış domain | "yakında dolar" garantisi **verilmez** |
| path-ID (Yöntem A) | üst ID + `/` + alt ad adreslemesi | kademe 7 `UNKNOWN` |
| noktalı-ID (Yöntem B) | `K003.02.01.05.003` biçimi | alternatiftir, bu dosyada kullanılmaz |
| ID GAP | ağaçta olmayan numara (K002) | `⚠️ VERIFICATION REQUIRED` |
| ID CONFLICT | aynı numarada çakışan klasör (K003-audio ↔ K003-ses-motoru) | `⚠️ VERIFICATION REQUIRED` |
| VALIDATION | REPO + ADR + TEST birleşimiyle doğrulama | tek başına garanti değil |
| dolgu kenar | kaynaksız üretilmiş kenar | **yasak** (§C) |

## §M Band-1 Kenar Geldiğinde — Beklenen Değişim Kaydı (şablon; içerik UYDURULMAZ)

> Bu tablo **boş bir beklenti kaydıdır**: satırlar "ne güncellenecek"i tarif eder,
> **değerler henüz dolmamıştır** (doldurulmuş değer görünürse uydurmadır).

| Tetikleyici | Etkilenen bölüm | Beklenen değişim | Değer (bugün) |
|---|---|---|---|
| Bir domainin `index.md depends_on` alanı doldu | §B | yeni kenar satırı append edilir | **boş — kenar yok** |
| Aynı domain | §C | ⏳ satırı ✅'ye döner | **24 ⏳ · 1 ✅** |
| Aynı domain | §E tablosu | kanıt oranı satırı güncellenir | **2 → 2** |
| K003'ün ters kenarı yazılır | §G.2 | `UNKNOWN` bir değere döner | **UNKNOWN** |
| Kademe-2+ path-ID ilk kez kullanılır | §F.2 | kademe tablosu yeni satır alır | **yalnız 1–6 · 8** |
| ID CONFLICT çözülür | §G.3 | `⚠️` işareti kalkar | **açık — çözülmedi** |

**Kural:** "Değer (bugün)" sütunu ölçüm **değildir**; dosyanın kendi mevcut
durumunun **özetidir** (2 kenar · 24 iskelet · 1 GAP · 1 CONFLICT — §B/§C ile aynı).

## §N Append-Only Doğrulama Kontrol Listesi (bu dosya için — her değişimde)

1. [ ] Eski bir satır silinmiş/değiştirilmiş mi? → **yasak** (yalnız append).
2. [ ] Yeni kenar var mı? → varsa `evidence` (gerçek yolu) **zorunlu** (v2 §4.2).
3. [ ] Ok yönü `A → B` = "A, B'ye muhtaç" olarak mı yazıldı? → ters çevrildiyse RED.
4. [ ] Kademe numarası kaynakta var mı? → yoksa `⚠️ VERIFICATION REQUIRED`.
5. [ ] Yüzde/ölçüm/istatistik eklendi mi? → eklendiyse **sayılmış** mı? (bu derinlemede 0).
6. [ ] Yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) var mı? → varsa RED.
7. [ ] Yeni URL eklendi mi? → eklendiyse `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ile **HTTP 200** doğrulanır (bu derinlemede **0 URL**).
8. [ ] Dosyanın **son satırı** tek Authority satırı mı?
9. [ ] Dürüstlük bloğu (§E) hâlâ geçerli mi — kanıt oranı **değişmedi** mi?
10. [ ] `git commit` atıldı mı? → **ATILMAYACAK** (orkestratöre aittir).

## §O Set İçi Kenar Aktarım Haritası (bu derinlemede okunan set gerçeği)

| Dosya | Kenar taşır mı? | Bu dosyanın kenarla ilişkisi |
|---|---|---|
| **bu dosya** (`dependency-graph.md`) | **EVET** — setin tek `depends_on` taşıyıcısı | 2 kenar · 24 iskelet (§B/§C) |
| `enterprise-architecture.md` | hayır — o dosyanın Lejant'ı da bunu **söyler** | grafik akışı ≠ kenar |
| `domain-architecture.md` | hayır — envanter + band | adlar burada, kenarlar burada değil |
| `runtime-architecture.md` | hayır — kapılar ≠ kenar | K2 kapısı "Dependencies"i işaret eder |
| `hardware-audio-architecture.md` | 2 kenarın **blok-görseli** — veri kaynağı değil | §A'da oklar var; kayıt bu dosyadadır |

**Sonuç:** sette tek doğrulama kaynağı = **bu dosya §B**; diğer dördü ya
gösterir ya yorumlar. Çelişki çıkarsa kenar **sayısı** bu dosyada,
**biçimi** kaynakta (v2 §4.3) aranır.

## §P Ek SSS (§K devamı)

**S10: Yeni bölüm eklemek kanıt sayısını değiştirir mi?**
Hayır — §E tablosu bunu **0/0** ile kilitler; eklenenler yorum katmanıdır.

**S11: Bu dosyayı okuyan biri grafiği nereden çizer?**
Band-1'de `depends_on` alanları dolduktan **sonra**; bugün çizmek iskeleti
kanıt gibi göstermek olur (§K S1).

**S12: `evidence` alanı boş kenar sayılır mı?**
Hayır — `depends_on` + `evidence` birliktedir (v2 §4.2 zorunlu alanlar);
yolusuz kenar **yarım kayıttır**, grafikte ✅ **olmaz**.

**S13: 24 iskeletten biri dolarsa diğerleri de dolmuş sayılır mı?**
Hayır — her domain **kendi** alanıyla değerlendirilir; sayım satır satırdır (§G.3).

**S14: Bu derinlemenin toplam katkısı nedir?**
Yorum + rehber + kontrol listesi; **0 kenar · 0 düğüm · 0 URL · 0 ölçüm**.

## §Q Bu Derinlemenin Kaydı (append-only günlüğü)

| # | İşlem | Sonuç / kanıt |
|---|---|---|
| 1 | Dosya okundu (önce) | 144 satır |
| 2 | `§E–§P` blokları **sona** eklendi | eski satır silinmedi/değiştirilmedi |
| 3 | Dürüstlük bloğu (§E) yazıldı | kanıt oranı **2 kenar · 24 iskelet** — değişmedi |
| 4 | Yeni kenar/URL/ölçüm üretilmedi | **0 / 0 / 0** |
| 5 | Dış kaynak (web) kullanılmadı | doğrulanacak URL **0** |
| 6 | Dosya ölçüldü (`Get-Content -Encoding UTF8`) | son satır = tek Authority satırı |
| 7 | `git commit` | **ATILMADI** (orkestratöre aittir) |

**Değişiklik yüzeyi:** yalnız bu dosya (beyazliste #1). `index.md` ·
`AGENTS.md` · `CLAUDE.md` · `wasapi/` ve diğer dosyalara **dokunulmadı**.

## §R Kenar Okuma Mini-Tutorial (bu dosyayı ilk kez okuyan için)

**Adım 1 — Dosyanın ne olduğu:** bu dosya bir **grafik değil**, kenar **verisi +
okuma kuralıdır** (giriş notu). Grafik çizmek Band-1'e bırakılmıştır (§K S1).

**Adım 2 — Tek gerçek veriyi bul:** §B — v2 §4.3'ün verdiği **tek**
`depends_on` örneği:

```yaml
depends_on:
  - K001-donanım
  - K004-dsp
```

**Adım 3 — Yönü uygula:** §A kuralı — `A → B` okunduğunda anlam
"`A`, `B'ye muhtaçtır`". Dolayısıyla bu iki satır **tek bir** muhtaçlığın
iki hedefidir: muhtaç = `K003-audio`.

**Adım 4 — Kapsamı sınırla:** 24 diğer domain için alan **boştur** (§C) —
"şunlar da bağlıdır" cümlesi bu dosyada **kurulamaz**.

**Adım 5 — Adresle (varsa):** kademe-2+ düğüm gelecekse §F.1 Yöntem A:
üst ID + `/` + alt ad. Kademe numarası kaynakta yoksa uydurma (§F.2 uyarısı).

**Adım 6 — Doğrula:** §H — EVIDENCE → REPO/ADR/TEST → VALIDATION zinciri
işletilir; kanıtsız kenar ✅ **olmaz**.

**Adım 7 — Şüphe durumunda:** §J kuralları — `⚠️ VERIFICATION REQUIRED` veya
`UNKNOWN` yaz, üst kararı bekle; **tahminle doldurma**.

**Mini-tutorial'ın kanıt katkısı:** **0** — yalnız §A–§D'dekilerin
**sıralı okunma** tarifidir.

**Authority:** Vault Steward — `.ai/architecture/diagrams/dependency-graph.md` (kaynak sadakati: v2 spec §4.2 · §4.3 · §4.5 · §5.2 · append-only derinleştirme 2026-10-13)
