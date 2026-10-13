---
title: "CoreMusic — Enterprise Architecture (Canonical Graph)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §5.1 (canonical graph) · §1.2 (hiyerarşi)"
group: mimari
---


# Enterprise Architecture — Canonical Graph

> v2 spec'teki canonical ASCII graph'ın (§5.1) **tam kaydı** ve hiyerarşi şemasının (§1.2)
> **birebir render'ı**. Yeni node/kenar **eklenmemiştir**; yalnız kaynaktaki çizim taşınmıştır.

## §A Canonical Graph — v2 §5.1 (tam kayıt)

```text
                        ┌─────────────────────┐
                        │      COREMUSIC      │
                        └──────────┬──────────┘
                                   │
                                   ▼
                             ┌───────────┐
                             │ K-LAYER   │
                             └─────┬─────┘
                                   │
                     ┌─────────────┼─────────────┐
                     ▼             ▼             ▼
                  DOMAIN       RUNTIME        DATA
                     │             │             │
                     ▼             ▼             ▼
                 SUBDOMAIN      PROCESS       BOUNDARY
                     │
                     ▼
              BOUNDED CONTEXT
                     │
                     ▼
                  MODULE
                     │
                     ▼
                 COMPONENT
                     │
                     ▼
                  SERVICE
                     │
                     ▼
                  ADAPTER
                     │
                     ▼
               IMPLEMENTATION
```

## §B Hiyerarşi Şeması — v2 §1.2 (birebir)

```text
                        ┌─────────────────────┐
                        │      COREMUSIC      │
                        └──────────┬──────────┘
                                   │
                                   ▼
                        ┌─────────────────────┐
                        │ K000–K5000          │
                        │ ARCHITECTURE        │
                        │ REGISTRY            │
                        └──────────┬──────────┘
                                   │
              ┌────────────────────┼────────────────────┐
              ▼                    ▼                    ▼
           DOMAIN               RUNTIME                DATA
              │                    │                    │
              ▼                    ▼                    ▼
         SUBDOMAIN             PROCESS              BOUNDARY
              │
              ▼
      ┌───────────────┐
      │ BOUNDED       │
      │ CONTEXT       │
      └───────┬───────┘
              │
              ▼
           MODULE
              │
              ▼
        COMPONENT
              │
              ▼
      SERVICE / ADAPTER
              │
              ▼
     IMPLEMENTATION
```

## Lejant

| Sembol | Anlam |
|---|---|
| `┌───┐` · `│ X │` | **Düğüm (node)** — katman / kayıt kutusu |
| `▼` | akış yönü (yukarıdan aşağıya) — tek yön |
| `├────┼────┤` | aynı kademeden üç kol: **DOMAIN · RUNTIME · DATA** |
| `K-LAYER` | kademe 1 — K000–K5000 Architecture Registry |
| `DOMAIN → … → IMPLEMENTATION` | §4.1 sekiz kademe (registry → implementation) |
| `→` / `▼` | **grafik akış**tır; `depends_on` kenarı **DEĞİLDİR** — gerçek kenarlar `dependency-graph.md`'dedir |

## Kaynak Atfı

- **v2 §5.1** "Ana Mimari Graf (v0.1) / CANONICAL GRAPH v0.1" → §A (birebir taşındı).
- **v2 §1.2** "Hiyerarşi Şeması (kuralın kendisi)" → §B (birebir taşındı).
- **v2 §5.2** (Kanıt → Doğrulama Zinciri) → `dependency-graph.md` §D'ye taşındı (kenar doğrulama zinciri).
- **v2 §5.3** (Hedef Okuma Sırası) → `runtime-architecture.md`.
- **v2 §5.4** (K5000 Kayıt Rafı) → §1.2 ile aynı hiyerarşinin iç içe kameradan tekrarı; yeniden yazılmadı.
- Dönüşüm: `index.md` §3 — "Yapı (v2 §1.1 — birebir)". Bu dosya setteki 5 diyagramın 1'incisidir.

## Durum Notu

- `status: draft` — diyagram **kaynak sadakati** taşır, tamamlanmışlık iddiası taşımaz.
- **Kenarlar doldukça genişler:** `depends_on` verisi Band-1 üretiminde doldukça bu graf genişletilecek.

---

## §C Dürüstlük Bloğu (Append-Only Derinleştirme Uyarısı — 2026-10-13)

> ⚠️ **KANIT ORANI DEĞİŞMEMİŞTİR.** Bu ek blok (`§C`–`§J`) append-only derinleme
> ile **sona** eklenmiştir; §A/§B şemalarının hiçbir satırı silinmemiş,
> düzeltilmemiştir.
>
> | Kanıt kalemi | Derinleme öncesi | Derinleme sonrası |
> |---|---|---|
> | §A canonical graph kademe kutuları | kaynaktaki hâli (birebir) | **değişmedi** |
> | §B hiyerarşi şeması | kaynaktaki hâli (birebir) | **değişmedi** |
> | Bu dosyadaki `depends_on` kenarı | **0** (bu dosya kenar **taşımaz**) | **0** |
> | Bu derinlemede eklenen node / kenar / kademe | — | **0** (grafik üretilmedi) |
> | Bu derinlemede eklenen URL / yüzde / ölçüm | — | **0** (dış kaynak kullanılmadı) |
>
> **Uyarı:** `§C`–`§J` *yorum ve okuma rehberi* katmanıdır — yeni düğüm, yeni
> kenar, yeni kademe **iddia etmez**. En kritik ayrım korunur: bu dosyadaki
> `▼`/`→` okları **grafik akışıdır, `depends_on` kenarı DEĞİLDİR** (Lejant);
> gerçek kenarlar `dependency-graph.md`'dedir. Kaynakta olmayan her şey
> `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` ile işaretlidir; yer tutucu desen
> (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) **kullanılmamıştır**.

## §D §A Canonical Graph — Kutu Kutu Derin Okuma

### §D.1 Gök-kutu (kök) ve kademe-1

| # | Kutu (§A) | İçerik anlamı | Kenar mı? |
|---|---|---|---|
| 1 | `COREMUSIC` | sistemin kökü — tek üst düğüm | hayır (akış) |
| 2 | `K-LAYER` | K kademe katmanı (K000–K5000) | hayır (akış) |

§A'da `K-LAYER` kutusundan sonra **üç kol** açılır: `DOMAIN` · `RUNTIME` ·
`DATA` (aynı kademede, `├────┼────┤` ile gösterilen üç kol).

### §D.2 Üç kol ve kendi iç akışları (yalnız §A'da çizilenler)

| Kol | §A'daki akış (çizilen) | §A'da **çizilmeyen** devam | İşaret |
|---|---|---|---|
| DOMAIN | `DOMAIN → SUBDOMAIN → BOUNDED CONTEXT → MODULE → COMPONENT → SERVICE → ADAPTER → IMPLEMENTATION` | — (kol §A'da tam çizilmiş) | ✅ |
| RUNTIME | `RUNTIME → PROCESS` | PROCESS altı §A'da **yok** | `⚠️ VERIFICATION REQUIRED` |
| DATA | `DATA → BOUNDARY` | BOUNDARY altı §A'da **yok** | `⚠️ VERIFICATION REQUIRED` |

> §A'da `RUNTIME` ve `DATA` kollarının çizimi, `DOMAIN` kolundan **kısadır**;
> bu, o kolların tamamlandığı anlamına **gelmez** — eksik kısımlar kaynakta
> yoktur, uydurulmaz. (RUNTIME detayı `runtime-architecture.md` §A'dadır.)

### §D.3 DOMAIN kolunun sekiz kademesi (§A sırası — değiştirilemez)

| Kademe | §A kutusu | Bu kademenin §A'daki hâli |
|---|---|---|
| 1 | DOMAIN | kol başı |
| 2 | SUBDOMAIN | tek kutu |
| 3 | BOUNDED CONTEXT | §A'da kutusuz (yalnız satır) |
| 4 | MODULE | tek kutu |
| 5 | COMPONENT | tek kutu |
| 6 | SERVICE | tek kutu |
| 7 | ADAPTER | tek kutu |
| 8 | IMPLEMENTATION | kol sonu |

> Not: §A ile §B arasında kademe 6–7 farkı **kaynaktan** gelir: §A `SERVICE`
> ve `ADAPTER`'ı **iki ayrı** kutu, §B ise `SERVICE / ADAPTER`'ı **tek** kutu
> yazar. İkisi de birebir taşınmıştır; biriyle diğerinin yerine **geçilmez**
> (§E karşılaştırma tablosu).

## §E §A ↔ §B Fark Analizi (aynı kaynağın iki render'ı)

| Kriter | §A — Canonical Graph (v2 §5.1) | §B — Hiyerarşi Şeması (v2 §1.2) |
|---|---|---|
| Kök kutu | `COREMUSIC` | `COREMUSIC` |
| İkinci kutu | `K-LAYER` | `K000–K5000 ARCHITECTURE REGISTRY` (3 satır kutu) |
| Üç kol | DOMAIN · RUNTIME · DATA | DOMAIN · RUNTIME · DATA |
| RUNTIME devamı | `RUNTIME → PROCESS` | `RUNTIME → PROCESS` |
| DATA devamı | `DATA → BOUNDARY` | `DATA → BOUNDARY` |
| DOMAIN zinciri | 8 kademe (SERVICE ve ADAPTER ayrı) | `SERVICE / ADAPTER` tek kutu (7 görünüm satırı) |
| Kaynak | v2 §5.1 | v2 §1.2 |
| Fark kaydedildi mi? | — | ✅ bu tabloda — **çelişki değil, iki kaynak render'ı** |

**Sonuç:** iki şema birbirinin **yerine geçmez**; ikisi de kaynak sadakatiyle
taşınmıştır. §A'nın `SERVICE`/`ADAPTER` ayrımı ile §B'nin birleştirmesi
arasında **öncelik sorunu yoktur** — biri §5.1'in, diğeri §1.2'nin çizimidir.

## §F "Grafik Oku ≠ Kenar" Kuralının Derinlemesi (Lejant notunun açılımı)

| # | Grafikte görünen | Doğru yorum | Yanlış yorum |
|---|---|---|---|
| 1 | `COREMUSIC ▼ K-LAYER` | "kök, K-LAYER'ı **kapsar**" | "COREMUSIC, K-LAYER'a muhtaçtır" |
| 2 | `K-LAYER ▼ DOMAIN ▼ SUBDOMAIN` | "kademe sırası / kapsama" | "`depends_on` kenarı" |
| 3 | üç kol `├────┼────┤` | "aynı kademeden üç **ayrı** kol" | "üç kol birbirine bağlı" |
| 4 | `DOMAIN → … → IMPLEMENTATION` | "sekiz kademe akışı" | "sekiz bağımlılık oku" |
| 5 | §A'da `RUNTIME → PROCESS` | "kolun §A'daki son görünen kademesi" | "PROCESS'te biter (tamamdır)" |
| 6 | §A'da `DATA → BOUNDARY` | "kolun §A'daki son görünen kademesi" | "BOUNDARY'de biter (tamamdır)" |

**Kural (bu dosyanın Lejant'ından türetildi):** `→`/`▼` = grafik akışı;
**gerçek `depends_on` kenarları yalnız `dependency-graph.md` §B'dedir** ve o
dosyada bugünkü sayı **2**'dir. Bu dosyada kenar sayısı **0**'dır.

## §G Set İçi Tutarlılık Kontrol Listesi (bu derinlemede yapılan çapraz okuma)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | §A üç kol = §B üç kol? | ✅ `DOMAIN · RUNTIME · DATA` — aynı |
| 2 | §A'da `K-LAYER` = §B'de `K000–K5000 ARCHITECTURE REGISTRY`? | ✅ aynı kademe-1 (farklı render) |
| 3 | §A kademe-8 `IMPLEMENTATION` = §B `IMPLEMENTATION`? | ✅ kol sonu aynı |
| 4 | SERVICE/ADAPTER farkı | ✅ §5.1 iki kutu · §1.2 tek kutu — §E'de kayıtlı |
| 5 | Bu dosyada `depends_on` iddiası var mı? | ✅ **YOK** (Lejant: gerçek kenarlar `dependency-graph.md`'de) |
| 6 | Setteki klasör sayımı tutarlı mı? | `domain-architecture.md` §A: **25 klasör** (19 tekil K + 6× K020) — bu dosya sayım **iddia etmez** |
| 7 | Band cetveli bu dosyada tekrarlanıyor mu? | Hayır — band cetveli `domain-architecture.md` §B'dedir (ADR-097) |
| 8 | Yeni URL/ölçüm/kanıt eklendi mi? | ✅ **0** |

## §H Diyagram Seti Çapraz Referans (5 dosya)

| Dosya | Setteki rolü | Bu dosyayla ilişkisi |
|---|---|---|
| **bu dosya** | canonical graf + hiyerarşi şeması (§5.1 · §1.2) | setin **çatı** şeması |
| `domain-architecture.md` | 25 klasör ağacı + band cetveli | `K-LAYER` kutusunun envanter hâli |
| `dependency-graph.md` | kenar verisi + doğrulama zinciri | bu dosyanın oklarının **gerçek kenar karşılığı** |
| `runtime-architecture.md` | RUNTIME/PROCESS kolu + K1–K9 okuma sırası | §B'deki RUNTIME kolunun **alt-küme** derinlemesi |
| `hardware-audio-architecture.md` | K000/K001/K003/K004/K005 blokları | K-LAYER içindeki **beş somut domain** |

**Okuma sırası önermesi (yorum katmanı — v2 iddiası değil):** bu dosyadan
başlamak setin doğal sırasıdır (çatı → birim → kenar → kol → somut blok);
gerekçe: §A/§B her şeyin **kapsayıcısını** gösterir.

## §I SSS — Sık Sorulan Sorular

**S1: Bu dosyadaki oklar bağımlılık mı?**
Hayır. Lejant açıkça yazar: `→`/`▼` **grafik akışıdır**; `depends_on` kenarı
**değildir**. Gerçek kenarlar `dependency-graph.md` §B'dedir (2 adet).

**S2: §A ile §B niçin farklı görünüyor?**
İki farklı kaynak bölümüdür: §A = v2 §5.1 canonical graph, §B = v2 §1.2
hiyerarşi şeması. Fark §E tablosunda kayıtlıdır; biri diğerinin **hatalı**
kopyası **değildir**.

**S3: `SERVICE` ile `ADAPTER` tek mi, iki mi?**
Kaynağa göre: §5.1'de **iki ayrı** kutu, §1.2'de **tek** birleşik kutu.
Birleştirme veya ayırma **yapılmadı** — iki render da korunmuştur.

**S4: RUNTIME kolu PROCESS'te biter mi?**
§A/§B bu kolu `PROCESS`'te **çizmeyi bitirir**; kolun orada bittiği **kanıt
değildir** → alt kademe `⚠️ VERIFICATION REQUIRED` (`runtime-architecture.md` §A).

**S5: DATA kolu neden tek geçiş?**
Aynı sebep — kaynakta `BOUNDARY` altı **çizilmemiş**; uydurulmaz.

**S6: Bu dosyaya yeni node eklenebilir mi?**
Hayır — bu derinleme **0 node** ekledi. Node yalnız kaynak (v2) veya dolu
`depends_on` verisiyle eklenir.

**S7: `K-LAYER` kutusundaki K aralığı nedir?**
§A `K-LAYER` yazar; §B aynı kutuyu `K000–K5000 ARCHITECTURE REGISTRY` olarak
yazar. Aralığın ayrıntısı band cetvelidir → `domain-architecture.md` §B.

**S8: Kanıt → Doğrulama Zinciri bu dosyada neden yok?**
v2 §5.2 bu sette **`dependency-graph.md` §D'ye** taşınmıştır (Kaynak Atfı §D
notu). Tekrarı yapılmadı — çakışma önlenmiştir.

**S9: `index.md` §3 ile bu dosya arasındaki ilişki nedir?**
Kaynak Atfı'nda "Dönüşüm: `index.md` §3" kayıtlıdır; bu derinlemede `index.md`
**okunmadı** (beyazliste dışı yüzey) → çelişki görülürse `⚠️ VERIFICATION
REQUIRED` + üst karar.

**S10: Eklenen bölümler kaynak oranını değiştirdi mi?**
Hayır — §C tablosu: 0 yeni node · 0 yeni kenar · 0 yeni ölçüm. Değişiklik
**yalnız açıklama katmanıdır**.

## §J Lejant ve Kademe Ek Açıklamaları (Lejant devamı)

| Sembol / terim | Ek açıklama (Lejant'ın genişlemesi) |
|---|---|
| `K-LAYER` | kademe-1 kutu; §B'deki karşılığı `K000–K5000 ARCHITECTURE REGISTRY` |
| `├────┼────┤` | üç **eş-kademe** kol ayracı; kol **içi** akış kendi satırlarında |
| `SUBDOMAIN` | DOMAIN kolunun 2. kademesi — tek kutu |
| `BOUNDED CONTEXT` | 3. kademe; §A'da kutusuz satır, §B'de kutulu — **iki render** |
| `MODULE` / `COMPONENT` | 4. / 5. kademe |
| `SERVICE` / `ADAPTER` | 6./7. kademe; §A ayrı, §B birleşik (§E) |
| `IMPLEMENTATION` | sekiz kademenin sonu (registry → implementation) |
| `PROCESS` | RUNTIME kolunun görünen son kademesi — **altı `UNKNOWN`** |
| `BOUNDARY` | DATA kolunun görünen son kademesi — **altı `UNKNOWN`** |
| grafik oku (`→` · `▼`) | kapsama/akış — **`depends_on` DEĞİL** |
| canonical graph | v2 §5.1'in adı — "tek doğru çizim" anlamında, içeriği §A'dadır |

## §K Sekiz Kademenin Sözlüğü (yorum katmanı — v2 §5.1/§1.2 yalnız adları verir)

> Her kademe için: **bu dosyada ne yazıyor** (kayıt) ve **set içi karşılığı**
> (çapraz okuma). Tanım sütunları **yorumdur**; yeni olgu **iddia etmez**.

### §K.1 Kademe 1 — DOMAIN (kol başı)

| Alan | Değer |
|---|---|
| Bu dosyada | §A/§B'de üç kolun ilki — tek kutu |
| Yorum *(yorum katmanı)* | "neyin alanı olduğu" — kapsam tanımı |
| Set içi karşılığı | `domain-architecture.md` §A: **25 domain klasörü** (K000–K019 + 6× K020) |
| `UNKNOWN` | DOMAIN kolunun kaç subdomain'e ayrıldığı — bu sette çizilmiyor |

### §K.2 Kademe 2 — SUBDOMAIN

| Alan | Değer |
|---|---|
| Bu dosyada | tek kutu (§A · §B aynı) |
| Yorum *(yorum katmanı)* | domainin alt bölmesi |
| Set içi karşılığı | `UNKNOWN` — sette subdomain envanteri **çizilmedi** → `⚠️ VERIFICATION REQUIRED` |
| Uyarı | "alt kademe var" **denebilir**, "şu kademe" **denemez** |

### §K.3 Kademe 3 — BOUNDED CONTEXT

| Alan | Değer |
|---|---|
| Bu dosyada | §A'da **kutusuz** satır, §B'de **kutulu** (iki render — §E) |
| Yorum *(yorum katmanı)* | sınırlı bağlam — bağımlılığın sınırı |
| Set içi karşılığı | `UNKNOWN` — sette BC listesi **yok** → `⚠️ VERIFICATION REQUIRED` |
| Not | `dependency-graph.md` §F.2'deki kademe-3 örneği `/transport`'tur (yalnız **örnek**, envanter **değildir**) |

### §K.4 Kademe 4 — MODULE

| Alan | Değer |
|---|---|
| Bu dosyada | tek kutu |
| Yorum *(yorum katmanı)* | kod modülü |
| Set içi karşılığı | `UNKNOWN` (modül envanteri bu sette yok) |
| Kural | dosya listesi ≠ modül listesi — sette **eşleme yapılmaz** |

### §K.5 Kademe 5 — COMPONENT

| Alan | Değer |
|---|---|
| Bu dosyada | tek kutu |
| Yorum *(yorum katmanı)* | bileşen |
| Set içi karşılığı | `UNKNOWN`; `dependency-graph.md` §A kademe örneğinde kademe-5 = `/jitter-buffer` (yalnız **örnek**) |
| Uyarı | örnek, **envanter değildir** — bileşen listesi iddia **edilmez** |

### §K.6 Kademe 6 — SERVICE

| Alan | Değer |
|---|---|
| Bu dosyada | §A'da ayrı kutu; §B'de `SERVICE / ADAPTER` ile birleşik (§E) |
| Yorum *(yorum katmanı)* | servis katmanı |
| Set içi karşılığı | `domain-architecture.md` §A: `K012-services/` klasörü (klasör ↔ kademe **eşlemesi kanıtlanmadı** → ⚠️) |
| Uyarı | `K012-services/` = kademe-1 klasördür; **kademe-6 servis ile aynı şeydir** **demez** |

### §K.7 Kademe 7 — ADAPTER

| Alan | Değer |
|---|---|
| Bu dosyada | §A'da ayrı kutu; §B'de SERVICE ile birleşik (§E) |
| Yorum *(yorum katmanı)* | dışa/sağa uyarlama katmanı |
| Set içi karşılığı | `UNKNOWN` (adapter envanteri bu sette yok) |
| Not | kademe 7, `dependency-graph.md` §F.2'de **atlanmıştı** (orada `UNKNOWN`) — iki kaynak **bağımsızdır**, birleştirilmez |

### §K.8 Kademe 8 — IMPLEMENTATION

| Alan | Değer |
|---|---|
| Bu dosyada | kol sonu — §A ve §B aynı |
| Yorum *(yorum katmanı)* | gerçekleşim (kod) |
| Set içi karşılığı | bu repo'nun kaynak dosyaları — **bu derinlemede okunmadı** (beyazliste dışı) |
| Not | Kaynak Atfı: "registry → implementation" sekiz kademenin toplamıdır |

## §L Üç Kolun Sözlüğü (§A/§B'deki kol-kutuları)

| Kutu | Kol | Bu dosyadaki durum | Set içi karşılığı |
|---|---|---|---|
| `DOMAIN` | kol 1 | sekiz kademeye iner | `domain-architecture.md` (25 klasör) |
| `SUBDOMAIN` | kol 1 | tek kutu | `UNKNOWN` |
| `BOUNDED CONTEXT` | kol 1 | iki render (§E) | `UNKNOWN` |
| `MODULE` · `COMPONENT` · `SERVICE` · `ADAPTER` · `IMPLEMENTATION` | kol 1 | §A'da ayrı, §B'de 6–7 birleşik | `UNKNOWN` (kısmen — §K.6 notu) |
| `RUNTIME` | kol 2 | `RUNTIME → PROCESS` — **biter** | `runtime-architecture.md` §A (alt-kademe `UNKNOWN`) |
| `PROCESS` | kol 2 | görünen son kutu | `UNKNOWN` — bu dosyanın §A'sı **doldurmaz** |
| `DATA` | kol 3 | `DATA → BOUNDARY` — **biter** | `UNKNOWN` — sette DATA kolunun derinlemesi **yok** |
| `BOUNDARY` | kol 3 | görünen son kutu | `UNKNOWN` |
| `K-LAYER` / `K000–K5000 ARCHITECTURE REGISTRY` | kök-altı | §A/§B farklı yazım, aynı kademe | `domain-architecture.md` §B (band cetveli) |

**Denge:** bu dosyada **12 kutu adı** geçiyor; set içi karşılığı dolu olanlar
yalnız DOMAIN kolunun **ilk** kademesi (klasör envanteri) ve band cetvelidir.
Kalanı `UNKNOWN`/⚠️ olarak **işaretli kalır** — kapatılmaz.

## §M Set İçi Envanter Çapraz Doğrulama (bu derinlemede yapılan okuma)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | 5 diyagram dosyası = `domain-architecture.md` §A `diagrams/` satırları? | ✅ 5/5 aynı adlar |
| 2 | `K-LAYER` ↔ 25 klasör envanteri bağlantısı | ✅ envanter `domain-architecture.md` §A'da |
| 3 | Kenar sayısı | ✅ bu dosyada **0**; sette **2** (`dependency-graph.md` §B) |
| 4 | Band-2..7 kuralı | ✅ bu dosyada geçmiyor (band bu dosyanın konusu değil) → `domain-architecture.md` §B |
| 5 | RUNTIME alt kademe | ✅ iki dosyada da `UNKNOWN` — **çelişki yok** |
| 6 | İmla dokunulmazlığı | ✅ bu derinlemede imla düzeltmesi **yapılmadı** |
| 7 | Yeni URL / ölçüm / yüzde | **0** |
| 8 | Yeni node / kenar | **0** |

## §N Append-Only Doğrulama Kontrol Listesi (bu dosya için — her değişimde)

1. [ ] §A/§B şemalarından **bir satır silinmiş/değiştirilmiş mi?** → yasak.
2. [ ] Yeni node eklendi mi? → eklendiyse kaynağı **v2 §5.1/§1.2 mi?** (değilse RED).
3. [ ] `→`/`▼` oklarına **`depends_on`** muamelesi yapıldı mı? → yapıldıysa RED (Lejant).
4. [ ] `SERVICE`/`ADAPTER` ayrımı biriyle **değiştirildi mi?** → RED (§E iki render korunur).
5. [ ] `PROCESS`/`BOUNDARY` altına kademe **uyduruldu mu?** → RED (§L `UNKNOWN`).
6. [ ] Yüzde/ölçüm/URL eklendi mi? → bu derinlemede **0**; eklendiyse
      `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
      ile HTTP 200 doğrulanır.
7. [ ] Yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) var mı? → RED.
8. [ ] Dosyanın **son satırı** tek Authority satırı mı? · Dürüstlük bloğu (§C) geçerli mi?
9. [ ] `git commit` **atılmadı** mı?

## §O Ek SSS (§I devamı)

**S11: Sekiz kademenin tamamı bu dosyada uygulanabilir mi?**
Kademe **sırası** uygulanabilir (§D.3); kademe-içi **içerik** setin çoğunda
`UNKNOWN`'dur (§K) — uydurarak doldurulmaz.

**S12: `BOUNDED CONTEXT` neden bir yerde kutulu, bir yerde kutusuz?**
İki farklı kaynak render'ı (§5.1 kutusuz · §1.2 kutulu) — §E kayıtlıdır;
ikisi de korunur.

**S13: Bu dosyaya `DATA` kolu derinlemesi eklenebilir mi?**
Kaynak v2 §1.2 bu kolu `BOUNDARY`'de **çizmeyi bırakır** → derinleme, kaynağın
**dışına** çıkmak olur; eklenecek her şey `⚠️ VERIFICATION REQUIRED` ister.

**S14: Set içinde en "dolu" kademe hangisi?**
Kademe-1 DOMAIN — `domain-architecture.md` §A: 25 klasör. Diğer kademeler
`UNKNOWN` (§L dengesi).

**S15: Kapı K1–K9 ile sekiz kademe aynı mı?**
Hayır — kademeler **yapı** (§A/§B), kapılar **okuma sırası**
(`runtime-architecture.md` §B). Karıştırılmaz.

**S16: Bu derinlemenin toplam katkısı nedir?**
Yorum + sözlük + kontrol listesi; **0 node · 0 kenar · 0 URL · 0 ölçüm**.

## §P Sekiz Kademe ↔ K1–K9 Kapıları — Karşılaştırma (set içi okuma; ≠ eşitleme)

> Bu tablo **iki farklı çerçeveyi yan yana koyar**: kademeler **yapıdır**
> (bu dosya §A/§B), kapılar **okuma sırasıdır** (`runtime-architecture.md` §B).
> Eşitlik **iddia edilmez** — yalnız çerçeve karşılaştırmasıdır.

| # | Kademe (bu dosya) | Kapı (runtime §B) ile olası temas *(yorum)* | İlişki |
|---|---|---|---|
| 1 | DOMAIN | K2 (Dependencies → Boundaries) — sınır konusu | **temas** — eşitlik değil |
| 2 | SUBDOMAIN | — | kapı eşlemesi `UNKNOWN` |
| 3 | BOUNDED CONTEXT | K2 | **temas** — eşitlik değil |
| 4 | MODULE | — | `UNKNOWN` |
| 5 | COMPONENT | K3 (Technology → Abstraction) | **temas** — eşitlik değil |
| 6 | SERVICE | K7 (Application → API/Services) | **temas** — eşitlik değil |
| 7 | ADAPTER | K3 | **temas** — eşitlik değil |
| 8 | IMPLEMENTATION | — | `UNKNOWN` |
| — | *(kademe yok)* K4 · K5 · K6 | donanım/sürücü/motor kapıları | bu dosyanın şemasında **ayrı kol yok** → `hardware-audio-architecture.md` |
| — | *(kademe yok)* K8 · K9 | istemci/kullanıcı kapıları | bu sette **hiçbir diyagramda yok** → ⚠️ |

**Sonuç:** 8 kademe ile 9 kapı **birebir eşleşmez**; temaslar **yorumdur**.
Kademe ↔ kapı **eşlemesi** kaynakta **yoktur** → `⚠️ VERIFICATION REQUIRED`.

## §Q Set İçi Sayım Özetleri (dosyaların kendi sayıları — çapraz bakış)

| Sayım | Kaynak dosya | Değer | Bu derinlemede |
|---|---|---|---|
| Kenar sayısı | `dependency-graph.md` §B | **2** | değişmedi |
| Kenarsız iskelet | `dependency-graph.md` §C | **24** | değişmedi |
| Band-1 klasör | `domain-architecture.md` §A | **25** | değişmedi |
| ID GAP | `domain-architecture.md` §A | **1** (K002) | değişmedi |
| Slot toplamı | `domain-architecture.md` §B | **5.001** | değişmedi |
| Blok (bu set) | `hardware-audio-architecture.md` §A/§B | **5** | değişmedi |
| Blok-içi düğüm | `hardware-audio-architecture.md` §D.1 | **63** | listeleme, ölçüm **değil** |
| Okuma kapısı | `runtime-architecture.md` §B | **9** (uyarlama) | değişmedi |
| Bu dosyadaki `depends_on` | bu dosya (Lejant) | **0** | değişmedi |
| Bu derinlemede eklenen node/kenar/URL/ölçüm | tüm set | **0 / 0 / 0 / 0** | — |

## §R Bu Derinlemenin Kaydı (append-only günlüğü)

| # | İşlem | Sonuç / kanıt |
|---|---|---|
| 1 | Dosya okundu (önce) | 120 satır |
| 2 | `§C–§R` blokları **sona** eklendi | §A/§B şemalarının **hiçbir satırı** silinmedi/değiştirilmedi |
| 3 | Dürüstlük bloğu (§C) yazıldı | kanıt oranı **0 bu-dosya-kenarı** — değişmedi |
| 4 | Yeni node/kenar/URL/ölçüm üretilmedi | **0 / 0 / 0 / 0** |
| 5 | Dış kaynak (web) kullanılmadı | doğrulanacak URL **0** |
| 6 | Dosya ölçüldü (`Get-Content -Encoding UTF8`) | son satır = tek Authority satırı |
| 7 | `git commit` | **ATILMADI** (orkestratöre aittir) |

**Değişiklik yüzeyi:** yalnız bu dosya (beyazliste #3). `index.md` ·
`AGENTS.md` · `CLAUDE.md` · `wasapi/` ve diğer dosyalara **dokunulmadı**.

**Authority:** Vault Steward — `.ai/architecture/diagrams/enterprise-architecture.md` (kaynak sadakati: v2 spec §5.1 · §1.2 · append-only derinleştirme 2026-10-13)
