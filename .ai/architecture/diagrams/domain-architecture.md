---
title: "CoreMusic — Domain Architecture (25 K Domain Haritası)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §1.1 (ana ağaç) · §4.7 (band cetveli) · §3.1 (GAP/CONFLICT)"
group: mimari
---


# Domain Architecture — Band-1 Domain Haritası

> v2 §1.1 ana ağacının ASCII render'ı + §4.7 band cetvelinin mini hali.
> İmla ve yapı kaynağa sadıktır: `donanaım-type` · `identitiy` · `integraion` ·
> `comilance` · `comere` · `licence` · `recomenataion` **dokunulmaz** (v2 §3 KURAL 1).

## §A Ana Ağaç — v2 §1.1 (birebir)

```text
.ai/
└── architecture/
    ├── index.md
    ├── README.md
    │
    ├── adr/
    │   ├── index.md
    │   ├── templates/
    │   └── decisions/
    │
    ├── diagrams/
    │   ├── enterprise-architecture.md
    │   ├── domain-architecture.md
    │   ├── dependency-graph.md
    │   ├── runtime-architecture.md
    │   └── hardware-audio-architecture.md
    │
    ├── K000-isletim-sistemi/
    ├── K001-donanım/
    ├── K003-audio/
    ├── K004-dsp/
    ├── K005-firmware/
    ├── K006-yapay-zeka/
    ├── K007-data/
    ├── K008-domain/
    ├── K009-security/
    ├── K010-identitiy/
    ├── K011-application/
    ├── K012-services/
    ├── K013-integraion/
    ├── K014-network/
    ├── K015-media/
    ├── K016-AI/
    ├── K017-observability/
    ├── K018-devops/
    ├── K019-testing/
    ├── K020-adr/
    ├── K020-comilance/
    ├── K020-comere/
    ├── K020-licence/
    ├── K020-discovery/
    ├── K020-recomenataion/
    └── K21.....k5000 etc.../
```

**Yapısal işaretler (v2 §1.1 + §3.1 — düzeltilmedi, kaydedildi):**

| İşaret | Tür | Durum |
|---|---|---|
| `K002` hiç yok — `K001 → K003` atlanıyor | ID GAP | ⚠️ VERIFICATION REQUIRED |
| 6 klasör aynı `K020` önekinde (adr · comilance · comere · licence · discovery · recomenataion) | ID CONFLICT | ⚠️ VERIFICATION REQUIRED |
| `K020-comere` içeriği bilinmiyor | UNKNOWN | ⚠️ VERIFICATION REQUIRED — tahmin yasak |
| Band-1 klasör toplamı: **19 tekil K** (K000–K019, K002 hariç) **+ 6× K020 = 25 klasör** | sayım | ✅ `index.md` §5 ile aynı |

## §B Band Cetveli (mini) — v2 §4.7 + ADR-097

| Bant | Aralık | Slot | Durum |
|---|---|---|---|
| Band-1 | K000 – K020 | 21 | ✅ TANIMLI (19 tekil K + 6× K020 = 25 klasör) |
| Band-2 | K021 – K120 | 100 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-3 | K121 – K320 | 200 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-4 | K321 – K720 | 400 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-5 | K721 – K1520 | 800 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-6 | K1521 – K3120 | 1.600 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| Band-7 | K3121 – K5000 | 1.880 | ⏸ RESERVED · ADR-097 — klasör AÇILMAZ |
| **TOPLAM** | **K000 – K5000** | **5.001** | 20 tanımlı slot + 1 GAP + 4.980 rezerve |

```text
   K000                                                              K5000
     │                                                                  │
     ▼                                                                  ▼
     ├─B1─┼────────B2────────┼───────────B3───────────┼────────B4────────┼──B5───┼─B6──┼B7┤
     K000 K020              K021                    K121                K321 ... K1520  K3120 K5000
     ▲                                                                 ▲
     │ 21 TANIMLI                                                     │ 4.980 BOŞ SLOT
     │ (diskte var)                                                   │ (yalnız ADR ile doldurulur)
```

## Lejant

| Sembol | Anlam |
|---|---|
| `├── X/` · `└── X/` | **Klasör düğümü** — bir domain (kademe 1) |
| `│` · `└──` | ağaç dallanması (iç içe hiyerarşi) |
| `K21.....k5000 etc.../` | v2'de yazıldığı gibi rezerve alan (isim verilmemiş) |
| `B1..B7` | band aralığı — slot cetveli, **klasör değil** |
| `▲ · ⏸` | tanımlı / rezerve durum işareti |

## Kaynak Atfı

- **v2 §1.1** "Ana Ağaç" → §A (birebir taşındı; düğüm eklenmedi/silinmedi).
- **v2 §4.7** "BANT (BAND) YERLEŞİM ŞEMASI" → §B tablo (mini hali) + slot cetveli (birebir).
- **v2 §3.1** "Bu ağacdaki iç çakışmalar" + **v2 §1.1** alt notu (2 yapısal nokta) → işaret tablosu.
- **v2 §4.7 kuralı:** band yalnız **rezervasyon aralığıdır**; dosya/klasör açılmaz, numara ADR ile atanır.
- Eş kaynak: `.ai/architecture/index.md` §4 (band cetveli) · §5 (25 klasör tablosu) · §6 (GAP/CONFLICT).

## Durum Notu

- `status: draft` — kaynak sadakati taşır; Band-2..7 **klasör olarak açılana kadar bu harita genişlemez**.
- **Kenarlar doldukça genişler:** kademe-2+ node'lar (`K0xx/<alt-ad>`, v2 §4.5) eklendikçe ağaç derinleşir.

---

## §C Dürüstlük Bloğu (Append-Only Derinleştirme Uyarısı — 2026-10-13)

> ⚠️ **KANIT ORANI DEĞİŞMEMİŞTİR.** Bu ek blok (`§C`–`§J`) append-only derinleme
> ile **sona** eklenmiştir; önceki hiçbir satır silinmemiş, düzeltilmemiştir.
>
> | Kanıt kalemi | Derinleme öncesi | Derinleme sonrası |
> |---|---|---|
> | Band-1 klasör sayısı | **25** (19 tekil K + 6× K020) | **25** (değişmedi) |
> | ID GAP | **1** (K002) | **1** (değişmedi) |
> | ID CONFLICT | **1** grubu (6× K020) | **1** (değişmedi) |
> | Rezerve band (Band-2..7) | **6** — klasör AÇILMAZ (ADR-097) | **6** (değişmedi) |
> | Bu derinlemede eklenen klasör / düğüm / band | — | **0** (ağaç üretilmedi) |
> | Bu derinlemede eklenen URL / yüzde / ölçüm | — | **0** (dış kaynak kullanılmadı) |
>
> **Uyarı:** `§C`–`§J` *yorum, okuma rehberi ve tutarlılık kontrolü* katmanıdır;
> yeni klasör, yeni GAP/CONFLICT, yeni band **iddia etmez**. İmla dokunulmazlığı
> (§A notu — v2 §3 KURAL 1) bu bloklarda da geçerlidir: `donanaım-type` ·
> `identitiy` · `integraion` · `comilance` · `comere` · `licence` ·
> `recomenataion` **yazımda olduğu gibi korunur**. Kaynakta olmayan her şey
> `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` ile işaretlidir; yer tutucu desen
> (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) **kullanılmamıştır**.

## §D §A Ana Ağaç — Okuma Rehberi (satır satır)

### §D.1 Ağaç üç ana gövdedir (kayıt, düzeltilmedi)

| Gövde | Satır aralığı (§A) | İçerik |
|---|---|---|
| Set gövdesi | `architecture/index.md` · `README.md` + `adr/` + `diagrams/` | doküman ve karar altyapısı — **K domain değildir** |
| K gövdesi | `K000-isletim-sistemi/` … `K020-recomenataion/` | Band-1 domainleri — **25 klasör** |
| Rezerve gövde | `K21.....k5000 etc.../` | v2'de yazıldığı gibi isimsiz rezerve alan — **klasör DEĞİL** |

> Not: `adr/` ve `diagrams/` ağaçta K öneki **almaz**; "25 klasör" sayımı bunları
> **içermez** (§A işaret tablosu ile aynı sayı). Rezerve gövde `K21.....k5000`
> satırı **bir klasör adı değildir** — v2'nin kendi yazımdır (Lejant: "isim
> verilmemiş").

### §D.2 K gövdesi — 25 klasörün okuma sırası (ağaca sadık)

> `#` sütunu **envanter sıra numarasıdır** (1–25), K numarası **değildir**.
> K002 ağaçta olmadığı için sıraya **girmez**.

| # | Klasör | # | Klasör | # | Klasör |
|---|---|---|---|---|---|
| 1 | K000-isletim-sistemi | 10 | K010-identitiy | 19 | K019-testing |
| 2 | K001-donanım | 11 | K011-application | 20 | K020-adr |
| 3 | K003-audio | 12 | K012-services | 21 | K020-comilance |
| 4 | K004-dsp | 13 | K013-integraion | 22 | K020-comere |
| 5 | K005-firmware | 14 | K014-network | 23 | K020-licence |
| 6 | K006-yapay-zeka | 15 | K015-media | 24 | K020-discovery |
| 7 | K007-data | 16 | K016-AI | 25 | K020-recomenataion |
| 8 | K008-domain | 17 | K017-observability | | |
| 9 | K009-security | 18 | K018-devops | | |

*(K002 bu sıraya girmez — ID GAP, ağaçta satır yok.)*

**Sayım kontrolü (§A işaret tablosu ile aynı):** 19 tekil K (K000–K019, K002
hariç) + 6× K020 = **25 klasör** ✅ `index.md` §5 ile aynı.

### §D.3 Ağaçta K016-AI ↔ K006-yapay-zeka ayrımı

İki ayrı klasör ağaçta **iki ayrı satırdır** (`K006-yapay-zeka/` ve `K016-AI/`):
biri tekil K serisinde (K006), diğeri K016'da. Aynı domainin iki yazımı mı,
farklı domainler mi olduğu v2 §1.1'de **açıklanmamıştır** →
`⚠️ VERIFICATION REQUIRED` (birleştirilmez, adlandırılmaz).

### §D.4 Ağaçta olmayanlar (doldurulması yasak boşluklar)

| Boşluk | Durum | Davranış |
|---|---|---|
| K002 | ID GAP — satır **hiç yok** | ara numara açılmaz; `⚠️ VERIFICATION REQUIRED` |
| K021..K5000 | Band-2..7 — tek satır `K21.....k5000 etc.../` | klasör açılmaz; ADR-097 |
| `K020-comere` içeriği | `UNKNOWN` | içeriği tahmin edilmez |
| kademe-2+ alt klasörler (`K0xx/<alt-ad>`) | ağaçta **çizilmemiş** | doldukça genişler (Durum Notu) |

## §E 25 Klasör Envanteri — İşaret ve Kenar Durumu

> Bu tablo §A işaret tablosu + set içindeki `dependency-graph.md` §B/§C
> (kenar verisi) ile **çapraz okunarak** üretilmiştir; yeni olgu **eklenmedi**.

| # | Klasör | ID durumu | Kenar durumu (v2 §4.3) | İşaret |
|---|---|---|---|---|
| 1 | K000-isletim-sistemi | normal | `depends_on` yazılmadı | ⏳ |
| 2 | K001-donanım | normal | hedef (K003→K001); kendi alanı boş | ⏳ |
| — | *(K002)* | **ID GAP** | — | ⚠️ |
| 3 | K003-audio | normal (set içi ID CONFLICT sorusu) | **2 kenar** (→K001 · →K004) | ✅ |
| 4 | K004-dsp | normal | hedef (K003→K004); kendi alanı boş | ⏳ |
| 5 | K005-firmware | normal | `depends_on` yazılmadı | ⏳ |
| 6 | K006-yapay-zeka | normal | `depends_on` yazılmadı | ⏳ |
| 7 | K007-data | normal | `depends_on` yazılmadı | ⏳ |
| 8 | K008-domain | normal | `depends_on` yazılmadı | ⏳ |
| 9 | K009-security | normal | `depends_on` yazılmadı | ⏳ |
| 10 | K010-identitiy | normal (imla dokunulmaz) | `depends_on` yazılmadı | ⏳ |
| 11 | K011-application | normal | `depends_on` yazılmadı | ⏳ |
| 12 | K012-services | normal | `depends_on` yazılmadı | ⏳ |
| 13 | K013-integraion | normal (imla dokunulmaz) | `depends_on` yazılmadı | ⏳ |
| 14 | K014-network | normal | `depends_on` yazılmadı | ⏳ |
| 15 | K015-media | normal | `depends_on` yazılmadı | ⏳ |
| 16 | K016-AI | normal | `depends_on` yazılmadı | ⏳ |
| 17 | K017-observability | normal | `depends_on` yazılmadı | ⏳ |
| 18 | K018-devops | normal | `depends_on` yazılmadı | ⏳ |
| 19 | K019-testing | normal | `depends_on` yazılmadı | ⏳ |
| 20 | K020-adr | **K020 öneki** (CONFLICT grubu) | `depends_on` yazılmadı | ⚠️⏳ |
| 21 | K020-comilance | **K020 öneki** (imla dokunulmaz) | `depends_on` yazılmadı | ⚠️⏳ |
| 22 | K020-comere | **K020 öneki** + içerik `UNKNOWN` | `depends_on` yazılmadı | ⚠️⏳ |
| 23 | K020-licence | **K020 öneki** (imla dokunulmaz) | `depends_on` yazılmadı | ⚠️⏳ |
| 24 | K020-discovery | **K020 öneki** | `depends_on` yazılmadı | ⚠️⏳ |
| 25 | K020-recomenataion | **K020 öneki** (imla dokunulmaz) | `depends_on` yazılmadı | ⚠️⏳ |

**Sayım özeti:** ✅ kenarlı **1** · ⏳ kenarsız **24** · ⚠️ ID işaretli
**7** (1 GAP + 6 CONFLICT öneki) — sayımlar §A/§C kaynaklarıyla **aynıdır**.

## §F §B Band Cetveli — Derin Okuma (ADR-097)

### §F.1 Band tablosunun kural sütunları

| Sütun | Anlamı | Değiştirilebilir mi? |
|---|---|---|
| Bant | adlandırılmış aralık (Band-1..7) | yalnız ADR ile |
| Aralık | numara kapsayışı (`K000 – K020` dahil) | hayır — sayım buna bağlı |
| Slot | aralıktaki numara sayısı | hayır — iç tutarlılık kontrolü §F.2'de |
| Durum | TANIMLI / RESERVED | Band-2..7 **klasör AÇILMAZ** (ADR-097) |

### §F.2 Slot aritmetiği (dosyanın kendi sayılarıyla iç tutarlılık)

| Bant | Aralık | Slot |
|---|---|---|
| Band-1 | K000 – K020 | 21 |
| Band-2 | K021 – K120 | 100 |
| Band-3 | K121 – K320 | 200 |
| Band-4 | K321 – K720 | 400 |
| Band-5 | K721 – K1520 | 800 |
| Band-6 | K1521 – K3120 | 1.600 |
| Band-7 | K3121 – K5000 | 1.880 |
| **Toplam** | **K000 – K5000** | **5.001** |

Kontrol: 21 + 100 + 200 + 400 + 800 + 1.600 + 1.880 = **5.001** — §B "TOPLAM"
satırıyla **aynı**; K000–K5000 dahil aralık da 5.001 numaradır (1 + 5.000).
Bu **dosyanın kendi tablosundan** türetilmiş aritmetiktir; dış ölçüm **değildir**.

### §F.3 "20 tanımlı + 1 GAP + 4.980 rezerve" okuması

| Kalem | Sayı | Açıklama |
|---|---|---|
| Band-1 slot | 21 | K000–K020 aralığı |
| tanımlı slot | 20 | 21 − 1 (K002 GAP hariç tutulduğunda) |
| GAP | 1 | K002 |
| Band-2..7 rezerve | 4.980 | 100+200+400+800+1.600+1.880 = 4.980 |
| toplam kontrol | 20 + 1 + 4.980 = 5.001 | §B toplamıyla **aynı** |

> Not: 25 **klasör** (19 tekil K + 6× K020) ile 21 **slot** farklı sayılardır —
> 6 klasör tek `K020` slotunu **paylaşır** (§A CONFLICT işareti). Bu ikisi
> karıştırılmaz.

### §F.4 Rezerve band davranışı (Band-2..7)

1. Klasör **açılmaz** (ADR-097 — §B Durum sütunu).
2. Numara **yalnız ADR ile** atanır (§B + Kaynak Atfı notu).
3. `K21.....k5000 etc.../` satırı **isimsiz** kalır — isim uydurulmaz.
4. Rezerve slot "doldu" **sayılmaz**; dolan slot Band-1'deki gibi envantere girer.
5. Bu derinlemede Band-2..7 için **hiçbir yeni aralık/klasör/iddia eklenmedi**.

## §G GAP ve CONFLICT Derin Analizi

### §G.1 K002 — ID GAP

| Soru | Cevap |
|---|---|
| Ağaçta satırı var mı? | **Hayır** (§A: "K002 hiç yok — K001 → K003 atlanıyor") |
| Numara neden atlanmış? | v2 §1.1/§3.1 bunu **açıklamıyor** → `UNKNOWN` |
| K002 açılabilir mi? | Bu derinlemede **açılmaz**; numara ADR'siz atanmaz |
| İşaret | ⚠️ VERIFICATION REQUIRED (§A tablosu aynen geçerli) |

### §G.2 6× K020 — ID CONFLICT

| Klasör | Ayrı bir klasör mü? | İçerik |
|---|---|---|
| K020-adr | evet (ağaçta ayrı satır) | bu derinlemede `UNKNOWN` değil — ağaçta **var**; içerik okunmadı |
| K020-comilance | evet | içerik `UNKNOWN` (tahmin yasak) |
| K020-comere | evet | **içerik bilinmiyor** → §A: `UNKNOWN` ⚠️ |
| K020-licence | evet | içerik okunmadı |
| K020-discovery | evet | içerik okunmadı |
| K020-recomenataion | evet | içerik okunmadı |

**Ortak işaret:** aynı `K020` önekinde 6 klasör → §A'daki CONFLICT kaydı
**değişmez**; "biri diğerinin yerine geçer" **demez**, birleştirilmez,
yeniden adlandırılmaz.

### §G.3 K003 kimlik sorusu (set içi — `dependency-graph.md` §C ile aynı)

§C'de kayıtlı açık soru: `K003 ile diskteki K003-ses-motoru aynı mı?` →
**ID CONFLICT — VERIFY REQUIRED**. Bu dosya (domain-architecture) kendi ağacında
`K003-audio` yazar; iki ismin eşdeğerliği **kanıtlanmadı** → `⚠️ VERIFICATION
REQUIRED`. Bu derinlemede **eşleştirme yapılmadı**.

## §H İmla Dokunulmazlığı Listesi (v2 §3 KURAL 1 — genişletilmiş kayıt)

> Aşağıdaki yazımlar v2 §3 KURAL 1 gereği **dokunulmazdır**; bu derinlemede de
> olduğu gibi korunur (ne düzeltilir, ne de "doğru yazım"la değiştirilir).

| # | Kaynaktaki yazımda kalan terim | Kapsam | Bu derinlemede |
|---|---|---|---|
| 1 | `donanaım-type` | K001 alt klasörü | aynen yazıldı |
| 2 | `identitiy` | K010 klasör adı | aynen yazıldı |
| 3 | `integraion` | K013 klasör adı | aynen yazıldı |
| 4 | `comilance` | K020 öneki | aynen yazıldı |
| 5 | `comere` | K020 öneki | aynen yazıldı |
| 6 | `licence` | K020 öneki | aynen yazıldı |
| 7 | `recomenataion` | K020 öneki | aynen yazıldı |
| 8 | `electroncis` | (K000 bloğu — `hardware-audio-architecture.md` §B) | set içinde aynen taşındı |

**Kural:** bu sekiz yazımdan birini düzeltmek = kaynağa ihanet; düzeltme
**istense bile** önce kaynak (v2) düzeltilir, bu dosya değil.

## §I Diyagram Seti Çapraz Referans (5 dosya)

| Dosya | Setteki rolü | Bu dosyayla ilişkisi |
|---|---|---|
| `enterprise-architecture.md` | canonical graf + hiyerarşi (§5.1 · §1.2) | o grafikteki K-LAYER kutusu bu ağaçtadır (K000–K5000) |
| `dependency-graph.md` | kenar verisi + okuma kuralı | §E tablosunun "Kenar durumu" sütunu oradan geldi |
| `runtime-architecture.md` | RUNTIME/PROCESS kolu + K1–K9 okuma sırası | okuma sırası ≠ ağaç sırası — kapılar klasör **değildir** |
| `hardware-audio-architecture.md` | K000/K001/K003/K004/K005 blokları | bu ağacın beş dalının **mini-ağaç** hâli |
| **bu dosya** | 25 klasör ağacı + band cetveli | setin envanter/birim kaynağı |

## §J SSS — Sık Sorulan Sorular

**S1: `K21.....k5000 etc.../` bir klasör mü?**
Hayır. Ağaçta isimsiz rezerve **alan** satırıdır (Lejant: "isim verilmemiş").
Gerçek klasör Band-2..7'de ADR ile açılır.

**S2: 25 klasör mü, 21 slot mu?**
Farklı ölçüler: **25 klasör** (fiziksel envanter) · **21 slot** (Band-1 numara
aralığı). 6 klasör tek K020 slotunu paylaştığı için iki sayı farklıdır (§F.3).

**S3: K002'yi açalım mı?**
Bu derinlemede **hayır**. GAP kaydı veridir; numara ADR'siz atanmaz (Band kuralı).

**S4: K006-yapay-zeka ile K016-AI aynı mı?**
Bilinmiyor → `⚠️ VERIFICATION REQUIRED` (§D.3). İkisi ağaçta ayrı satırdır,
birleştirilmedi.

**S5: `comilance`/`comere` yazımı neden düzeltilmiyor?**
v2 §3 KURAL 1 — imla **dokunulmaz** (§H).

**S6: Band-2 için `depends_on` yazabilir miyiz?**
Hayır — Band-2..7 klasörleri **açılmaz** (ADR-097) ve alan henüz yoktur.

**S7: Bu dosyanın kenar verisi var mı?**
Hayır — kenarlar `dependency-graph.md` §B'dedir (2 kenar). Bu dosya
**envanter + band** taşır.

**S8: Eklenen bölümler sayımı değiştirdi mi?**
Hayır — §C tablosu: 25 klasör · 1 GAP · 6 CONFLICT öneki · 6 rezerve band,
**hepsi değişmedi**.

**S9: `index.md` §4/§5/§6 ile çelişki olursa?**
Bu derinlemede `index.md` **okunmadı** (beyazliste dışı yüzey). Çelişki
görülürse sessizce çözülmez → `⚠️ VERIFICATION REQUIRED` + üst karar.

## §K Band-1 Üretim Senaryosu — Ağaç Nasıl Genişler (adım adım; uydurma yok)

> Bu bölüm **senaryo/rehberdir**: hangi adımda **ne tür** değişiklik olacağı
> tarif edilir. Somut yeni klasör adı, yeni band, yeni numara **üretilmez**.

1. **Tetik:** bir Band-1 `index.md` dosyasında kademe-2 alanı doldu (ör. `K0xx/<alt-ad>`).
2. **Doğrulama:** alanın `evidence` kanıtı var mı (gerçek dosya yolu · link · test kaydı — v2 §4.2)? → yoksa **dur**.
3. **Adresleme:** yeni düğümün ID'si = üst ID + `/` + alt ad (v2 §4.5 Yöntem A) — noktalı yöntem önerilmez.
4. **Yerleşim:** düğüm bu dosyanın §A ağacının **ilgili dalına** append edilir;
   mevcut satırlar **silinmez**, `├──`/`└──` dallanma düzeni korunur.
5. **Sayım:** §A işaret tablosundaki "25 klasör" sayımı **değişmez** (kademe-2+
   düğüm klasör **sayımına girmez** — sayım kademe-1 domain içindir).
6. **Band kuralı:** Band-2..7'de hâlâ **klasör açılmaz** (ADR-097); yalnız
   Band-1 içi derinleşme olur.
7. **Çelişki:** yeni düğüm mevcut ID ile çakışırsa (GAP/CONFLICT benzeri durum)
   → `⚠️ VERIFICATION REQUIRED` + üst karar; sessizce yerleştirilmez.
8. **İmla:** yeni düğüm adı kaynak imlası **bozuyorsa** kaynak düzeltilmeden
   buraya **yanlış yazım taşınmaz, doğru yazım da uydurulmaz** → işaretle.
9. **Commit:** bu dosya için `git commit` **atılmaz** (orkestratöre aittir).

**Bu derinlemede bu senaryonun uygulanışı:** 0 — hiçbir kademe-2 düğüm
**eklenmemiştir** (§C tablosu: "Bu derinlemede eklenen klasör / düğüm / band = 0").

## §L Append-Only Doğrulama Kontrol Listesi (bu dosya için — her değişimde)

1. [ ] Mevcut 25 klasör satırından **biri silinmiş mi?** → yasak.
2. [ ] İmla dokunulmazlığı (§H — 8 terim) **korunmuş mu?**
3. [ ] Yeni klasör/band/numara eklendi mi? → eklendiyse ADR kaydı **var mı?** (yoksa RED).
4. [ ] GAP (K002) satırı **acıma** ile doldurulmuş mu? → doldurulduysa RED.
5. [ ] K020 CONFLICT grubu **birleştirilmiş/birine indirgenmiş** mi? → RED.
6. [ ] Sayım hâlâ `19 tekil K + 6× K020 = 25` ve `21 slot` mı? (§D.2/§F.3)
7. [ ] Slot aritmetiği hâlâ 5.001'e oturuyor mu? (§F.2)
8. [ ] Yüzde/ölçüm/URL eklendi mi? → bu derinlemede **0**; eklendiyse
      `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
      ile HTTP 200 doğrulanır.
9. [ ] Yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) var mı? → RED.
10. [ ] Dosyanın **son satırı** tek Authority satırı mı? · Dürüstlük bloğu (§C) geçerli mi?
11. [ ] `git commit` **atılmadı** mı?

## §M Set İçi Okuma Sırası ve Çapraz Doğrulama (bu derinlemede yapılan okuma)

| Sıra | Dosya | Bu dosyaya ne katar? |
|---|---|---|
| 1 | `enterprise-architecture.md` | ağacın **çatısı** (K-LAYER / K000–K5000 kutusu) |
| 2 | **bu dosya** | **envanter**: 25 klasör + band cetveli |
| 3 | `runtime-architecture.md` | okuma **yöntemi** (K1–K9) — envanter değildir |
| 4 | `hardware-audio-architecture.md` | 5 dalın **mini-ağaç** dökümü (§D.1'deki 5 blok) |
| 5 | `dependency-graph.md` | **kenar verisi** (2 kenar) — bu dosyada yok |

**Çapraz doğrulama sonuçları (bu derinlemede):**

| Kontrol | Sonuç |
|---|---|
| Bu dosyanın 5 blok ismi = hardware dosyasındaki 5 blok? | ✅ K000 · K001 · K003 · K004 · K005 — aynı |
| Kenar sayımı uyuşuyor mu? | ✅ burada sayım yok → `dependency-graph.md` §B: **2** |
| İmla listesi setle tutarlı mı? | ✅ §H #8 `electroncis` = hardware §B'deki yazım |
| Band-2..7 ADR-097 referansı tutarlı mı? | ✅ §B tablo + Kaynak Atfı + §F — aynı kural |
| Yeni URL/ölçüm | **0** |

## §N Ek SSS (§J devamı)

**S10: Kademe-2 düğüm eklenince "25 klasör" değişir mi?**
Hayır — sayım **kademe-1 domain** içindir (§K adım 5).

**S11: Bu dosyada `depends_on` yazılabilir mi?**
Hayır — kenar verisi `dependency-graph.md`'dedir; buraya taşınırsa iki kaynak
**çakışır** (tek doğruluk kaynağı kuralı).

**S12: `K21.....k5000` satırı silinip yerine gerçek listeyi yazabilir miyiz?**
Hayır — v2 §1.1'de **isimsiz** rezerve alan olarak yazılır; isim uydurmak
kaynağa ihanettir (§D.1/§F.4).

**S13: Ağaçta `adr/` ve `diagrams/` neden K sayımında yok?**
K öneki **almazlar** — K gövdesi sayımı bunları **içermez** (§D.1).

**S14: Band-1 dışında tek satırlık `K21.....k5000 etc.../` bir klasör mü?**
Hayır — ağaç gövdesi (rezerve), klasör değil (§D.1 + Lejant).

**S15: Bu derinlemenin toplam katkısı nedir?**
Yorum + rehber + kontrol listesi; **0 klasör · 0 band · 0 URL · 0 ölçüm**.

## §O Ağaç Okuma Mini-Tutorial (bu dosyayı ilk kez okuyan için)

**Adım 1 — Ne tür bir dosya:** bu dosya **envanterdir** — hangi domain
klasörleri var, hangi band aralığı neyi kapsıyor (giriş notu).

**Adım 2 — Ağaçtan gövdeyi ayır:** §A üç gövde (§D.1) — doküman gövdesi
(`index.md` · `README.md` · `adr/` · `diagrams/`), **K gövdesi (25 klasör)**,
rezerve gövde (`K21.....k5000 etc.../` = klasör değil).

**Adım 3 — Sayımı doğru yap:** 25 = 19 tekil K + 6× K020 (§D.2); **21 slot**
≠ 25 klasör (6 klasör tek K020 slotunu paylaşır — §F.3).

**Adım 4 — İşaretleri oku:** §A tablosu — K002 GAP ⚠️ · 6× K020 CONFLICT ⚠️ ·
`K020-comere` `UNKNOWN`. Bu işaretler **eksiklik kaydıdır**, hata **değildir**.

**Adım 5 — Band kuralını uygula:** Band-2..7 klasör **açılmaz**, numara
**yalnız ADR** ile atanır (§B + §F.4).

**Adım 6 — Kenar arama:** bu dosyada **yok** → `dependency-graph.md` §B'ye git
(2 kenar).

**Adım 7 — İmlayı koru:** §H'deki 8 terim **dokunulmaz**; "doğru yazım"la
değiştirmek kaynağa ihanettir.

**Mini-tutorial'ın kanıt katkısı:** **0** — yalnız §A–§J'dekilerin sıralı
okunma tarifidir.

## §P Bu Derinlemenin Kaydı (append-only günlüğü)

| # | İşlem | Sonuç / kanıt |
|---|---|---|
| 1 | Dosya okundu (önce) | 122 satır |
| 2 | `§C–§P` blokları **sona** eklendi | eski satır silinmedi/değiştirilmedi |
| 3 | Dürüstlük bloğu (§C) yazıldı | kanıt oranı **25 klasör · 1 GAP · 6 CONFLICT · 6 rezerve band** — değişmedi |
| 4 | Yeni klasör/band/URL/ölçüm üretilmedi | **0 / 0 / 0** |
| 5 | Dış kaynak (web) kullanılmadı | doğrulanacak URL **0** |
| 6 | Dosya ölçüldü (`Get-Content -Encoding UTF8`) | son satır = tek Authority satırı |
| 7 | `git commit` | **ATILMADI** (orkestratöre aittir) |

**Değişiklik yüzeyi:** yalnız bu dosya (beyazliste #2). `index.md` ·
`AGENTS.md` · `CLAUDE.md` · `wasapi/` ve diğer dosyalara **dokunulmadı**.

**Authority:** Vault Steward — `.ai/architecture/diagrams/domain-architecture.md` (kaynak sadakati: v2 spec §1.1 · §3.1 · §4.7 · ADR-097 · append-only derinleştirme 2026-10-13)
