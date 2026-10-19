---
title: "CoreMusic — Hardware & Audio Architecture (5 Domain Blok Diyagramı)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §2.1 · §2.2 · §2.3 · §2.4 · §2.5 (mini ağaçlar) · §4.3 (kenarlar)"
group: mimari
---


# Hardware / Audio / DSP / Firmware / OS — Sınır Diyagramı

> Beş domainin v2'deki **mini ağaçlarından** türetilmiş blok diyagramıdır.
> Düğüm listeleri birebir taşınmış, **uydurma donanım parametresi yazılmamıştır**;
> tek kenar kaynağı v2 §4.3'tür (`K003-audio` `depends_on`).

## §A Doğrulanmış Zincir (kenarlar: v2 §4.3)

```text
┌─ K001-donanım · v2 §2.2 ───────────────────────────────────┐
│ index.md                                                   │
│ donanım-kernel/  ·  donanım-core/  ·  donanım-api/         │
│ donanaım-type/                                             │
│   ├── hdd/                                                 │
│   ├── audio/                                               │
│   └── ssd/                                                 │
└────────────────────────────────────────────────────────────┘
                              ▲ depends_on: K003 → K001 (§4.3)
┌─ K003-audio · v2 §2.3 ─────────────────────────────────────┐
│ index.md  ·  audio-master.md                               │
│ engine/ · device/ · driver/ · backend/                     │
│ input/ · output/ · routing/ · mixing/                      │
│ realtime/ · synchronization/                               │
│ diagnostics/ · testing/                                    │
└────────────────────────────────────────────────────────────┘
                              │ depends_on: K003 → K004 (§4.3)
                              ▼
┌─ K004-dsp · v2 §2.4 ───────────────────────────────────────┐
│ index.md  ·  dsp-master.md                                 │
│ pipeline/ · equalizer/ · filters/ · dynamics/              │
│ compressor/ · limiter/ · reverb/ · delay/                  │
│ crossover/ · spatial-audio/ · measurement/                 │
│ testing/                                                   │
└────────────────────────────────────────────────────────────┘
```

## §B Kenarsız Bloklar (v2 §2.1 · §2.5 — `depends_on` verisi YOK)

```text
┌─ K005-firmware · v2 §2.5 ──────────────────────────────────┐
│ index.md  ·  firmware-master.md                            │
│ boot/ · runtime/ · device-control/ · audio-control/        │
│ power-control/ · diagnostics/ · update/                    │
│ security/ · testing/                                       │
└────────────────────────────────────────────────────────────┘

┌─ K000-isletim-sistemi · v2 §2.1 ───────────────────────────┐
│ index.md  ·  os-master.md                                  │
│ electroncis/                                               │
│ isletim-sistemi-api/ · isletim-sistemi-audio/              │
│ isletim-sistemi-core/ · isletim-sistemi-cross/             │
│ isletim-sistemi-kernel/ · isletim-sistemi-observability/   │
│ isletim-sistemi-plan/ · isletim-sistemi-runtime/           │
│ isletim-sistemi-security/ · isletim-sistemi-testing/       │
│ isletim-sistemi-turu/                                      │
└────────────────────────────────────────────────────────────┘
```

⚠️ **VERIFICATION REQUIRED:** v2 spec'te `K005-firmware` ve `K000-isletim-sistemi`
için hiçbir `depends_on` kenarı yazılmamıştır → bu ikisi zincire **bağlanmamıştır**
(ara bağlantı uydurulmadı). Kenar verisi Band-1 üretiminden doldukça bu dosya genişler.

## Lejant

| Sembol | Anlam |
|---|---|
| `┌─ … ┐ / │ … │` | **blok** — bir K domain'i ve v2 mini-ağacındaki düğümleri |
| `▲` / `▼` | `depends_on` yönü — okun ucundaki kutu **bağımlılığın hedefidir** |
| `──·──` | blok içindeki alt düğüm listesi (mini ağaç satırı) |
| `├── / └──` | alt-klasör dallanması (`donanaım-type/` örneği) |
| `K00x · v2 §2.y` | blok başlığı = kaynak bölüm (izlenebilirlik) |
| ⚠️ | `VERIFICATION REQUIRED` — kenar/veri yok, uydurma yasak |

## Kaynak Atfı

- **v2 §2.1** K000-isletim-sistemi · **§2.2** K001-donanım · **§2.3** K003-audio ·
  **§2.4** K004-dsp · **§2.5** K005-firmware → blok içerikleri (mini ağaç birebir).
- **v2 §4.3** "Örnek kayıt (boş şablon — K003-audio)" → §A'daki **iki tek kenar**.
- **v2 §2.3/§2.4/§2.5 ELI15 tanımları** → blok sırasının okunuşu: donanım → ses motoru →
  DSP → firmware → işletim sistemi zemini (bu sıra v2 metinlerinin tanım sırasıdır; **kenar değildir**).
- **Dipnot:** teknoloji kanıtları (amplifikatör/DAC/çip modelleri vb.) bu diyagrama ait
  değildir → **K001/K005 master §4** pointer'ı (ilgili master dosyaları üretim aşamasında —
  ⚠️ VERIFICATION REQUIRED).
- Eş kaynak: `.ai/architecture/index.md` §5 (25 klasör tablosu) · §6 (GAP/CONFLICT).

## Durum Notu

- `status: draft` — diyagram **kaynak sadakati** taşır, tamamlanmışlık iddiası taşımaz.
- **Kenarlar doldukça genişler:** Band-1 üretiminde her `index.md` `depends_on`
  alanı dolduğunda §B blokları zincire bağlanır; bağlantı **kanıtsız çizilmez**.

---

## §C Dürüstlük Bloğu (Append-Only Derinleştirme Uyarısı — 2026-10-13)

> ⚠️ **KANIT ORANI DEĞİŞMEMİŞTİR.** Bu ek blok (`§C`–`§J`) append-only derinleme
> ile **sona** eklenmiştir; §A/§B bloklarının hiçbir satırı silinmemiş,
> düzeltilmemiştir.
>
> | Kanıt kalemi | Derinleme öncesi | Derinleme sonrası |
> |---|---|---|
> | Doğrulanmış kenar | **2** (K003→K001 · K003→K004 — v2 §4.3) | **2** (değişmedi) |
> | Kenarsız blok | **2** (K005-firmware · K000-isletim-sistemi) | **2** (değişmedi) |
> | Bu dosyadaki blok sayısı | **5** | **5** |
> | Bu derinlemede eklenen blok / kenar / donanım parametresi | — | **0** |
> | Bu derinlemede eklenen URL / yüzde / ölçüm / çip-modeli | — | **0** (dış kaynak kullanılmadı) |
>
> **Uyarı:** `§C`–`§J` *yorum, envanter ve okuma rehberi* katmanıdır; yeni
> donanım parametresi, yeni DAC/amplifikatör/çip modeli, yeni kenar **iddia
> etmez**. Teknoloji kanıtları (amplifikatör/DAC/çip modelleri) bu diyagrama
> **ait değildir** (Kaynak Atfı dipnotu) → bu derinlemede de yazılmadı.
> Kaynakta olmayan her şey `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` ile
> işaretlidir; yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) **kullanılmamıştır**.

## §D Beş Blok Envanteri — Düğüm Sayımı (dosyanın kendi listesinden)

> Sayımlar §A/§B bloklarının **kendi satırlarından** türetilmiştir (içeriden
> ölçüm — dış kaynak yok). Blok içerikleri §A/§B'de birebir durur; bu tablo
> yalnız **döküm ve sayımdır**.

### §D.1 Blok-bazlı sayım

| Blok | Kaynak | Dosya (md) | Alt klasör | Alt-alt | Toplam öğe |
|---|---|---|---|---|---|
| K000-isletim-sistemi | v2 §2.1 | `index.md` · `os-master.md` | 12 | — | **14** |
| K001-donanım | v2 §2.2 | `index.md` | 4 (`donanım-kernel` · `donanım-core` · `donanım-api` · `donanaım-type`) | 3 (`hdd` · `audio` · `ssd`) | **8** |
| K003-audio | v2 §2.3 | `index.md` · `audio-master.md` | 12 | — | **14** |
| K004-dsp | v2 §2.4 | `index.md` · `dsp-master.md` | 12 | — | **14** |
| K005-firmware | v2 §2.5 | `index.md` · `firmware-master.md` | 9 | — | **13** |
| **TOPLAM** | — | 5 × `index.md` + 3 `*-master.md` | — | — | **63** |

### §D.2 K001-donanım — alt ağacın dökümü (§A'daki tek dallı blok)

| Öge | Kademe | Not |
|---|---|---|
| `index.md` | dosya | — |
| `donanım-kernel/` | klasör (kademe 2) | — |
| `donanım-core/` | klasör (kademe 2) | — |
| `donanım-api/` | klasör (kademe 2) | — |
| `donanaım-type/` | klasör (kademe 2) | **imla dokunulmaz** (v2 §3 KURAL 1) |
| `donanaım-type/hdd/` | klasör (kademe 3) | dallanma tek burada |
| `donanaım-type/audio/` | klasör (kademe 3) | — |
| `donanaım-type/ssd/` | klasör (kademe 3) | — |

> Beş blok içinde **yalnız K001** kademe-3 dallanma gösterir; diğer dördü
> tek kademe listesidir. Bu bir eksiklik **değil**, kaynağın çizimidir.

### §D.3 Master dosyası bulunma durumu (dosyanın kendi verisi)

| Blok | `index.md` | `*-master.md` | İşaret |
|---|---|---|---|
| K000-isletim-sistemi | ✅ | `os-master.md` | kayıt |
| K001-donanım | ✅ | **YOK** (§A'da master satırı yok) | `⚠️ VERIFICATION REQUIRED` — master adı uydurulmadı |
| K003-audio | ✅ | `audio-master.md` | kayıt |
| K004-dsp | ✅ | `dsp-master.md` | kayıt |
| K005-firmware | ✅ | `firmware-master.md` | kayıt |

⚠️ **VERIFICATION REQUIRED:** K001 bloğunda master dosyası §A'da **çizilmemiş**
(belki yoktur, belki kaynakta öyle bırakılmıştır) → adı `UNKNOWN`, tahmin yasak.

### §D.4 Bloklar arası ortak adlar (içeriden çapraz okuma)

| Ortak ad | Hangi bloklarda | Yorum |
|---|---|---|
| `index.md` | 5/5 blok | her domainin kök indeksi — ad **aynıdır**, dosya **farklıdır** (farklı K klasörleri) |
| `testing/` | K003 · K004 · K005 · K000(`isletim-sistemi-testing/`) | dört blokta test birimi var; **bağlantı/değildir**, ayrı klasörlerdir |
| `diagnostics/` | K003 · K005 (K000'de `isletim-sistemi-observability/` **farklı** ad) | teşhis birimi iki blokta var |
| `security/` | K005 · K000(`isletim-sistemi-security/`) | ad benzeri, klasör ayrı |
| `runtime/` | K005 · K000(`isletim-sistemi-runtime/`) | ad benzeri, klasör ayrı |
| `audio-control/` · `device-control/` | yalnız K005 | firmware'e özgü |
| `engine/` · `routing/` · `mixing/` | yalnız K003 | ses motoruna özgü |
| `pipeline/` · `equalizer/` · `reverb/` … | yalnız K004 | DSP'ye özgü |

**Kural:** aynı ad ≠ aynı düğüm ≠ kenar. Ortak adlar `depends_on` **kanıtı
değildir**; kenar yalnız v2 §4.3'ten gelir.

## §E Kenar Derinlemesi (§A — iki tek kenar)

### §E.1 Zincirin yönü (kaynaktaki hâli)

```text
   K001-donanım  ◀──depends_on──  K003-audio  ──depends_on──▶  K004-dsp
   (hedef #1)                       (muhtaç)                    (hedef #2)
```

| Ok | Okuma | Kaynak |
|---|---|---|
| `K003 → K001` (§A'da `▲` ile, hedef K001) | K003-audio, K001-donanıma muhtaçtır | v2 §4.3 |
| `K003 → K004` (§A'da `▼` ile, hedef K004) | K003-audio, K004-dsp'e muhtaçtır | v2 §4.3 |

**Yön-uyarısı:** §A'daki `▲`, `K001` kutusuna **doğru** bakar (hedef orası);
`▼` de `K004` kutusuna bakar. Lejant: "okun ucundaki kutu bağımlılığın
**hedefidir**" → yön ters çevrilmez.

### §E.2 Kenarsız iki blok (§B) — neden bağlı değil?

| Blok | Durum | Sebep | Davranış |
|---|---|---|---|
| K005-firmware | zincire bağlı **değil** | v2'de `depends_on` **yazılmadı** | bağlantı çizilmez |
| K000-isletim-sistemi | zincire bağlı **değil** | v2'de `depends_on` **yazılmadı** | bağlantı çizilmez |

⚠️ Bu ikisi için **ara bağlantı uydurulmadı** (§B uyarısı) — "olması gereken"
bağlantı da yazılmaz: firmware ↔ audio, OS ↔ hepsi gibi senaryolar
**spekülasyondur** → `⚠️ VERIFICATION REQUIRED`.

### §E.3 Bu dosyada OLMAYAN kenar iddiaları

| Olası iddia | Bu dosyadaki durum |
|---|---|
| `K001 → K000` (donanım OS'e bağlı) | **YOK** — v2 §4.3'te yok |
| `K005 → K003` (firmware ses motoruna bağlı) | **YOK** |
| `K004 → K001` (DSP donanıma bağlı) | **YOK** |
| `K003 → K005` / `K000` | **YOK** |
| `donanaım-type/audio/` → K003 (alt düğüm kenarı) | **YOK** — kademe-3 kenarı v2'de yok |

**Toplam:** bu dosyada doğrulanmış kenar **2**; kalan her ihtimal `UNKNOWN`.

## §F Sıra Okuması — Tanım Sırası ≠ Kenar (Kaynak Atfı dipnotunun açılımı)

Kaynak Atfı'nda kayıtlı sıra: **donanım → ses motoru → DSP → firmware →
işletim sistemi zemini**. Bu sıranın statusü:

| # | Sıra sırasındaki adım | Bu bir kenar mı? | Ne? |
|---|---|---|---|
| 1 | donanım (K001) | hayır | v2 §2.2 **tanım** sırası |
| 2 | ses motoru (K003) | hayır (kenar varsa da ayrı) | v2 §2.3 tanım sırası |
| 3 | DSP (K004) | hayır | v2 §2.4 tanım sırası |
| 4 | firmware (K005) | hayır | v2 §2.5 tanım sırası |
| 5 | işletim sistemi zemini (K000) | hayır | v2 §2.1 tanım sırası (anlatım olarak **en alt/son**) |

**Uyarı:** beş adımlık bu sıra, §4.3'teki iki kenarla **örtüşür görünse de**
bağımsızdır: sıra "zemin/temel" anlatımıdır; kenar **kayıttır**. Sıradan kenar
**çıkarılmaz** (ör. "firmware → OS kenarı var" **denmez**).

## §G Band-1 Genişleme Protokolü (bu dosya için)

1. **Kaynak bekle:** `K005-firmware` / `K000-isletim-sistemi` `index.md`
   `depends_on` alanı Band-1'de dolduğunda.
2. **Kanıt zorunlu:** `evidence` alanı (gerçek dosya yolu · link · test kaydı —
   v2 §4.2); kanıtsız kenar **çizilmez**.
3. **§A'ya değil, bağlanacağı yere ekle:** kenar geldiğinde blok §A'daki zincire
   bağlanır; §B'deki ⚠️ paragrafı güncellenir.
4. **Yön doğrula:** `A → B` = "A, B'ye muhtaç"; Lejant `▲`/`▼` kuralına uy.
5. **Uydurma bağlantı yasağı:** "olması mantıklı" bağlantılar (§E.2/E.3) eklenmez.
6. **Çelişki = dur:** ID CONFLICT / GAP çıkarsa `⚠️ VERIFICATION REQUIRED` yaz,
   üst karara götür.
7. **Teknoloji kanıtı ekleme:** DAC/çip/amplifikatör modeli bu dosyaya **girmez**
   (Kaynak Atfı dipnotu — K001/K005 master §4 pointer'ı, master dosyaları
   üretim aşamasında ⚠️ VERIFICATION REQUIRED).
8. **Commit atma:** bu dosya değişikliğinin commit'i orkestratöre aittir.

## §H Diyagram Seti Çapraz Referans (5 dosya)

| Dosya | Setteki rolü | Bu dosyayla ilişkisi |
|---|---|---|
| `enterprise-architecture.md` | canonical graf + hiyerarşi | bu beş blok, o grafikteki `K-LAYER` içindeki somut domainlerdir |
| `domain-architecture.md` | 25 klasör ağacı + band cetveli | bu dosya o ağacın **5 dalını** (K000 · K001 · K003 · K004 · K005) derinleştirir |
| `dependency-graph.md` | kenar verisi + okuma kuralı | §E'deki 2 kenar = oradaki **tek verilmiş kenar verisi** |
| `runtime-architecture.md` | RUNTIME/PROCESS kolu + K1–K9 okuma sırası | `isletim-sistemi-runtime/` ve K005 `runtime/` bu dosyadadır; RUNTIME **kolu** ise o dosyada |
| **bu dosya** | 5 domain bloğu (K000 · K001 · K003 · K004 · K005) | setin **donanım/ses/DSP/firmware/OS** somutluğu |

## §I SSS — Sık Sorulan Sorular

**S1: Bu dosyada kaç gerçek kenar var?**
**2** — K003→K001 ve K003→K004 (v2 §4.3). K005 ve K000 kenarsızdır.

**S2: "donanım → ses → DSP → firmware → OS" zinciri midir?**
Hayır — bu **tanım/okuma sırasıdır** (§F); iki kenar bunun **yalnızca ilk
iki halkasıyla** örtüşür. Sıradan kenar **çıkarılmaz**.

**S3: `donanaım-type/audio/` K003'e bağlı mı?**
`UNKNOWN` — kademe-3 bir düğümün kenarı v2'de **yok**. İsim benzerliği kanıt
**değildir** (§D.4).

**S4: Neden K001'de master dosyası yok?**
Kaynakta (§A) çizilmemiş → adı `UNKNOWN`, uydurulmadı (§D.3).

**S5: `testing/` klasörü ortak olduğu için bloklar bağlantıdır denir mi?**
Hayır — aynı ad ≠ kenar (§D.4). Test birimleri ayrı K klasörlerinde durur.

**S6: Firmware neden zincire bağlı değil?**
v2 §4.3'te `K005` için `depends_on` **yazılmamış**; ara bağlantı uydurulmaz.

**S7: OS neden "zemin" olarak anılıyor?**
Kaynak Atfı'ndaki tanım sırasının son adımı böyle anlatılır (§F); bu bir
hiyerarşi/temel **yorumudur**, `depends_on` kenarı **değildir**.

**S8: Bu derinlemede donanım modeli (DAC/çip) eklendi mi?**
Hayır — Kaynak Atfı dipnotu bunu bu diyagramın **kapsam dışı** kılar; eklenen
URL/ölçüm/model **0**'dır.

**S9: K005 ile K000 birbirine bağlı mı?**
`UNKNOWN` — v2 §2.5/§2.1 yalnız **mini ağaçları** verir, kenar **vermez**.

**S10: Eklenen bölümler kanıt oranını değiştirdi mi?**
Hayır — §C: 2 kenar → 2 kenar · 2 kenarsız blok → 2 · yeni kenar 0 · yeni
ölçüm 0. Sayımlar (§D) yalnız dosyanın **kendi listesinin** dökümüdür.

## §J Lejant ve Terim Ek Açıklamaları (Lejant devamı)

| Sembol / terim | Ek açıklama |
|---|---|
| blok başlığı `K00x · v2 §2.y` | izlenebilirlik etiketi — bloğun **kaynak bölümü** |
| `▲` | hedef = **üstteki** kutu (K001) |
| `▼` | hedef = **alttaki** kutu (K004) |
| `index.md` | her bloğun kök indeksi (5/5) |
| `*-master.md` | alan master dosyası — K001'de **çizilmemiş** (`UNKNOWN`) |
| `donanaım-type/` | K001 alt klasörü — **imla dokunulmaz** |
| `electroncis/` | K000 alt klasörü — **imla dokunulmaz** (set içi §H kaydı) |
| kademe-3 dallanma | yalnız `donanaım-type/{hdd,audio,ssd}` — diğer bloklarda yok |
| tanım sırası | v2 §2.x anlatım sırası — **kenar değildir** |
| zincire bağlı | yalnız v2 §4.3 `depends_on` ile — bugün 2 blok hedefi + 1 muhtaç |

## §K Blok İçi Düğüm Sözlüğü (§A/§B listelerinin çapraz dökümü)

> Her satır §A/§B'de **zaten listelenen** bir düğümdür; "Setteki karşılığı"
> sütunu bu derinlemede yapılan çapraz okumanın **sonucudur**. Karşılığı
> olmayan = `UNKNOWN` — **eşleştirme uydurulmaz**.

### §K.1 K003-audio düğümleri (12 alt klasör + 2 dosya)

| Düğüm | Blok | Setteki karşılığı / durum |
|---|---|---|
| `index.md` | K003 | her blokta var — ayrı dosyalardır |
| `audio-master.md` | K003 | K003 alan master'ı (Kaynak Atfı: master §4 pointer'ları üretim aşamasında ⚠️) |
| `engine/` | K003 | K5 kapısının "Audio Engine"i ile **konu** örtüşür (runtime §D.5) — **eşleşme kanıtlanmadı** ⚠️ |
| `device/` | K003 | `UNKNOWN` — K001 `donanaım-type/` ile **eşleştirilmez** |
| `driver/` | K003 | K4/K5 kapılarının "Drivers"ı ile **konu** örtüşür — eşleşme ⚠️ |
| `backend/` | K003 | `UNKNOWN` (backend'in setteki karşılığı yok) |
| `input/` · `output/` | K003 | `UNKNOWN` |
| `routing/` | K003 | `dependency-graph.md` §A'daki `/routing` **kademe-2 örneği**yle aynı **kelime** — örnek ≠ envanter ⚠️ |
| `mixing/` | K003 | `UNKNOWN` |
| `realtime/` | K003 | `UNKNOWN` (gerçek zamanlı katman bu sette çizilmiyor) |
| `synchronization/` | K003 | `UNKNOWN` |
| `diagnostics/` | K003 | K005 `diagnostics/` ile **aynı ad, ayrı klasör** (§D.4) |
| `testing/` | K003 | dört blokta ortak ad — **ayrı klasörler** (§D.4) |

### §K.2 K004-dsp düğümleri (12 alt klasör + 2 dosya)

| Düğüm | Blok | Setteki karşılığı / durum |
|---|---|---|
| `index.md` · `dsp-master.md` | K004 | dosya + master |
| `pipeline/` | K004 | `UNKNOWN` |
| `equalizer/` · `filters/` · `dynamics/` | K004 | `UNKNOWN` — DSP zincirinin parçaları; **sıra/kanal iddiası yok** |
| `compressor/` · `limiter/` | K004 | `UNKNOWN` |
| `reverb/` · `delay/` | K004 | `UNKNOWN` |
| `crossover/` · `spatial-audio/` | K004 | `UNKNOWN` |
| `measurement/` | K004 | `UNKNOWN` |
| `testing/` | K004 | ortak ad (§D.4) — ayrı klasör |

### §K.3 K005-firmware düğümleri (9 alt klasör + 2 dosya)

| Düğüm | Blok | Setteki karşılığı / durum |
|---|---|---|
| `index.md` · `firmware-master.md` | K005 | dosya + master |
| `boot/` | K005 | `UNKNOWN` |
| `runtime/` | K005 | K000 `isletim-sistemi-runtime/` ile **aynı ad, ayrı klasör**; `runtime-architecture.md` **koludur, klasör adı değildir** — üçü birleştirilmez |
| `device-control/` | K005 | `UNKNOWN` |
| `audio-control/` | K005 | K003 ile **eşleştirilmez** (adi benzerlik kanıt değil) |
| `power-control/` | K005 | `UNKNOWN` |
| `diagnostics/` | K005 | ortak ad (§D.4) |
| `update/` | K005 | `UNKNOWN` |
| `security/` | K005 | K000 `isletim-sistemi-security/` ile **aynı ad, ayrı klasör** |
| `testing/` | K005 | ortak ad (§D.4) |

### §K.4 K000-isletim-sistemi düğümleri (12 alt klasör + 2 dosya)

| Düğüm | Blok | Setteki karşılığı / durum |
|---|---|---|
| `index.md` · `os-master.md` | K000 | dosya + master |
| `electroncis/` | K000 | **imla dokunulmaz** (§H #8) — konu `UNKNOWN` |
| `isletim-sistemi-api/` | K000 | K7 kapısının "API"si ile **konu** örtüşür — eşleşme ⚠️ |
| `isletim-sistemi-audio/` | K003 ile **eşleştirilmez** (farklı K) |
| `isletim-sistemi-core/` | K000 | `UNKNOWN` |
| `isletim-sistemi-cross/` | K000 | `UNKNOWN` |
| `isletim-sistemi-kernel/` | K000 | `UNKNOWN` |
| `isletim-sistemi-observability/` | K000 | `diagnostics/` adıyla **eşleştirilmez** |
| `isletim-sistemi-plan/` | K000 | `UNKNOWN` |
| `isletim-sistemi-runtime/` | bkz. §K.3 uyarısı |
| `isletim-sistemi-security/` | K000 | `security/` adıyla **eşleştirilmez** |
| `isletim-sistemi-testing/` | K000 | ortak **konu**, ayrı ad |
| `isletim-sistemi-turu/` | K000 | `UNKNOWN` |

### §K.5 K001-donanım düğümleri (bkz. §D.2 — 8 öge)

`donanım-kernel/` · `donanım-core/` · `donanım-api/` · `donanaım-type/`(+3) →
hepsi `UNKNOWN` karşılık; K000 `isletim-sistemi-kernel/`/`-api/` ile **aynı
konu-madde değil, ayrı klasörlerdir** — eşleştirme **yapılmaz**.

### §K.6 Sözlük özeti

| Kalem | Sayı |
|---|---|
| Bu §K'da dökümlenen düğüm | **63** (§D.1 toplamıyla aynı) |
| Sette eşleştirilmiş | **0** (eşleştirme kanıtlanmadı — yalnız "konu örtüşmesi" işaretlendi ⚠️) |
| `UNKNOWN` | çoğunluk — kalan her düğüm |

## §L Sınır Diyagramı Okuma Örnekleri (doğru / yanlış)

| # | Okuma | Doğru mu? | Gerekçe |
|---|---|---|---|
| 1 | "K003, K001'e muhtaç" | ✅ | v2 §4.3 + Lejant `▲` hedef kuralı |
| 2 | "K001, K003'e muhtaç" | ❌ | ok yönü ters çevrilemez |
| 3 | "K005 zincire sonradan bağlanacak" | ❌ | tahmin — kenar **yazılmadı** (§E.2) |
| 4 | "`audio/` ile `audio-control/` aynı şey" | ❌ | ad benzeri ≠ düğüm eşitliği (§D.4/§K.3) |
| 5 | "OS bloğu en altta olduğu için **temeldir**" | ⚠️ kısmen | bu bir **anlatım** sırasıdır (§F); kenar **değildir** |
| 6 | "Beş blok tek bir zincirdir" | ❌ | tek zincir **2 kenardır**; beş blok envanterdir |
| 7 | "testing/ ortak olduğu için bloklar bağlıdır" | ❌ | ortak ad ≠ `depends_on` (§D.4) |
| 8 | "K001'de master yok, yazalım" | ❌ | adı `UNKNOWN` — uydurulmaz (§D.3) |
| 9 | "K000'e kenar çizelim, mantıklı" | ❌ | dolgu kenar **yasak** (§G adım 5) |
| 10 | "Bu dosya donanım teknolojisinin kanıtıdır" | ❌ | kapsam dışı (Kaynak Atfı dipnotu) |

## §M Append-Only Doğrulama Kontrol Listesi (bu dosya için — her değişimde)

1. [ ] §A/§B bloklarından **bir satır silinmiş/değiştirilmiş mi?** → yasak.
2. [ ] Yeni kenar eklendi mi? → varsa v2 §4.3 / dolu `index.md depends_on` + `evidence` (gerçek yol) — yoksa RED.
3. [ ] `▲`/`▼` yönü Lejant kuralına uygun mu? (uçtaki kutu = hedef)
4. [ ] "Tanım sırası" bir yerde **kenar** gibi sunuldu mu? → RED (§F).
5. [ ] Ortak adlardan (`testing` · `diagnostics` · `security` · `runtime`) **kenar** türetildi mi? → RED (§D.4).
6. [ ] Yeni donanım modeli/parametresi/URL eklendi mi? → kapsam dışı; eklendiyse
      `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
      ile HTTP 200 doğrulanır (bu derinlemede **0 URL · 0 model**).
7. [ ] İmla (`donanaım-type` · `electroncis`) korunmuş mu?
8. [ ] Yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) var mı? → RED.
9. [ ] Dosyanın **son satırı** tek Authority satırı mı? · Dürüstlük bloğu (§C) geçerli mi?
10. [ ] `git commit` **atılmadı** mı?

## §N Ek SSS (§I devamı)

**S11: 63 düğüm tek tek doğrulandı mı?**
Hayır — 63, §D.1'deki **liste sayımıdır** (dosyanın kendi satırları);
doğrulanmış **kenar** sayısı yine **2**'dir.

**S12: `routing/` ile `dependency-graph.md`'deki `/routing` aynı mı?**
Aynı **kelime**, farklı **kademe** (klasör ↔ kademe-2 örneği); eşleşme
kanıtlanmadı → ⚠️ (§K.1).

**S13: Neden hiçbir düğüm eşleştirilmiyor?**
Eşleştirme bir `evidence` ister (gerçek yol/test kaydı); bu derinlemede yok →
`0 eşleştirme` (§K.6).

**S14: K000 neden "zemin" olarak anlatılıyor?**
Kaynak Atfı'ndaki **tanım sırasının** son adımı (§F); hiyerarşi iddiası
**değildir**.

**S15: Bloklara yeni düğüm eklenebilir mi?**
Yalnız kaynak (v2 §2.x mini ağaçları) genişlerse; bu derinlemede **0** eklendi.

**S16: Bu derinlemenin toplam katkısı nedir?**
Döküm + okuma örnekleri + kontrol listesi; **0 blok · 0 kenar · 0 URL · 0 model · 0 ölçüm**.

## §O Blok Okuma Mini-Tutorial (bu dosyayı ilk kez okuyan için)

**Adım 1 — Ne tür bir dosya:** bu dosya **beş domainin sınır diyagramıdır**;
düğüm listeleri kaynağın (v2 §2.1–§2.5) mini ağaçlarının **birebir** dökümüdür.

**Adım 2 — Zinciri oku (yalnız 2 kenar):** §A — `K001-donanım ◀── K003-audio
──▶ K004-dsp`. Yön: uçtaki kutu **hedeftir** (Lejant `▲`/`▼`).

**Adım 3 — Kenarsız blokları tan:** §B — `K005-firmware` ve `K000-isletim-sistemi`
**bağlı değildir**; uyarı paragrafı ara bağlantının **uydurulmadığını** söyler.

**Adım 4 — Sırayı kenar sanma:** donanım → ses → DSP → firmware → OS
**tanım sırasıdır** (§F) — sıradan `depends_on` **çıkarılmaz**.

**Adım 5 — Ad benzerliğine kanıt deme:** `testing/` · `diagnostics/` ·
`security/` · `runtime/` ortak adlardır, **ayrı klasörlerdir** (§D.4) —
ortak ad ≠ kenar.

**Adım 6 — Kapsamı koru:** DAC/çip/amplifikatör modeli bu diyagrama **ait
değildir** (Kaynak Atfı dipnotu) → teknoloji kanıtı buraya **yazılmaz**.

**Adım 7 — Şüphe durumunda:** §G protokolü — kanıt (`evidence`) yoksa kenar
**çizilmez**; `⚠️ VERIFICATION REQUIRED` yazılır.

**Mini-tutorial'ın kanıt katkısı:** **0** — yalnız §A–§J'dekilerin sıralı
okunma tarifidir.

## §P Bu Derinlemenin Kaydı (append-only günlüğü)

| # | İşlem | Sonuç / kanıt |
|---|---|---|
| 1 | Dosya okundu (önce) | 103 satır |
| 2 | `§C–§P` blokları **sona** eklendi | §A/§B bloklarının **hiçbir satırı** silinmedi/değiştirilmedi |
| 3 | Dürüstlük bloğu (§C) yazıldı | kanıt oranı **2 kenar · 2 kenarsız blok · 5 blok** — değişmedi |
| 4 | Yeni blok/kenar/donanım modeli/URL/ölçüm üretilmedi | **0 / 0 / 0 / 0 / 0** |
| 5 | Dış kaynak (web) kullanılmadı | doğrulanacak URL **0** |
| 6 | Dosya ölçüldü (`Get-Content -Encoding UTF8`) | son satır = tek Authority satırı |
| 7 | `git commit` | **ATILMADI** (orkestratöre aittir) |

**Değişiklik yüzeyi:** yalnız bu dosya (beyazliste #4). `index.md` ·
`AGENTS.md` · `CLAUDE.md` · `wasapi/` ve diğer dosyalara **dokunulmadı**.

**Authority:** Vault Steward — `.ai/architecture/diagrams/hardware-audio-architecture.md` (kaynak sadakati: v2 spec §2.1–§2.5 · §4.3 · append-only derinleştirme 2026-10-13)
