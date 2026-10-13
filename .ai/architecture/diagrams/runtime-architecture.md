---
title: "CoreMusic — Runtime Architecture (RUNTIME kolu + Okuma Sırası)"
type: diagram
category: architecture
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
authority: "Kaynak: v2 spec §1.2 (RUNTIME/PROCESS kolu) · §5.3 (hedef okuma sırası)"
group: mimari
---


# Runtime Architecture — RUNTIME/PROCESS Kolu ve Okuma Sırası

> v2 §1.2'deki üç koldan (DOMAIN · RUNTIME · DATA) **RUNTIME kolu** ile
> **§5.3 hedef okuma sırasının (karmaşıklıktan sadeliğe) akış diyagramı**.

## §A RUNTIME / PROCESS Kolu — v2 §1.2 (alt-küme, düğüm eklenmedi)

```text
                         ┌─────────────────────┐
                         │ K000–K5000          │
                         │ ARCHITECTURE        │
                         │ REGISTRY            │
                         └──────────┬──────────┘
                                   │
              ┌────────────────────┼────────────────────┐
              ▼                    ▼                    ▼
           DOMAIN               RUNTIME                DATA
                                 │
                                 ▼
                              PROCESS
```

- v2 §1.2'de `RUNTIME → PROCESS` kolunun devamında alt kademe **tanımlı değildir**
  (DOMAIN kolu SUBDOMAIN'e, DATA kolu BOUNDARY'ye iner; RUNTIME kolu PROCESS'te biter).
- `⚠️ VERIFICATION REQUIRED`: PROCESS altı node'lar Band-1 üretimine kadar açık değildir;
  uydurulmaz.
- Tam görünüm (üç kol + sekiz kademe): `enterprise-architecture.md` §B.

## §B Hedef Okuma Sırası — v2 §5.3 (birebir)

```text
  Complexity      ↓  Structure
  Dependencies    ↓  Boundaries
  Technology      ↓  Abstraction
  Hardware        ↓  Drivers
  Drivers         ↓  Audio Engine
  Audio Engine    ↓  Application
  Application     ↓  API / Services
  Services        ↓  Clients
  Clients         ↓  Users
```

**Kapılar (her `↓` = bir okuma kapısı — kapı numaralandırması uyarlamadır, düğüm/içerik değişmedi):**

```text
   K1  Complexity      ↓  Structure
   K2  Dependencies    ↓  Boundaries
   K3  Technology      ↓  Abstraction
   K4  Hardware        ↓  Drivers
   K5  Drivers         ↓  Audio Engine
   K6  Audio Engine    ↓  Application
   K7  Application     ↓  API / Services
   K8  Services        ↓  Clients
   K9  Clients         ↓  Users
```

**Akış yönü:** solda (karmaşıklık/hardware) başlar → sağda (users) biter; kapı sırası
**zorunlu okuma sırasıdır**, atlanan kapı = kanıtsız adım (v2 §5.3 başlığı: "karmaşıklıktan sadeliğe").

## Lejant

| Sembol | Anlam |
|---|---|
| `▼` | akış / iniş yönü (yukarıdan aşağıya) |
| `↓` | okuma sırası geçişi (karmaşıklık → sadelik) |
| `├────┼────┤` | üç ana kol: DOMAIN · RUNTIME · DATA |
| `K1…K9` | okuma kapısı numarası (**uyarlama** — v2 §5.3 satırlarından türetildi) |
| `RUNTIME → PROCESS` | v2 §1.2'de bu kolun tamamı; altı yok |

## Kaynak Atfı

- **v2 §1.2** "Hiyerarşi Şeması (kuralın kendisi)" → §A (RUNTIME/PROCESS kolunun alt-kümesi).
- **v2 §5.3** "Hedef Okuma Sırası (karmaşıklıktan sadeliğe)" → §B (birebir + kapı uyarlaması).
- **Kaynak: v2 §5.3** — okuma sırası akışının tek dayanağı budur; kapı numaraları (K1–K9)
  v2'de yoktur, **açıklama katmanıdır** (node/kenar olarak grafiğe alınmadı).
- Dönüşüm: `.ai/architecture/index.md` §3 (v2 §1.1 birebir).

## Durum Notu

- `status: draft` — kaynak sadakati taşır, tamamlanmışlık iddiası taşımaz.
- **Kenarlar doldukça genişler:** RUNTIME/PROCESS altı node'lar Band-1 üretiminde
  `index.md` alanları doldukça eklenecek (uydurma yasak).

---

## §C Dürüstlük Bloğu (Append-Only Derinleştirme Uyarısı — 2026-10-13)

> ⚠️ **KANIT ORANI DEĞİŞMEMİŞTİR.** Bu ek blok (`§C`–`§J`) append-only derinleme
> ile **sona** eklenmiştir; §A/§B şemalarının hiçbir satırı silinmemiş,
> düzeltilmemiştir.
>
> | Kanıt kalemi | Derinleme öncesi | Derinleme sonrası |
> |---|---|---|
> | §A RUNTIME/PROCESS kol uzunluğu | `RUNTIME → PROCESS` (son — altı tanımlı değil) | **değişmedi** |
> | §B okuma kapısı | **9** (K1–K9 — kapı numaraları *uyarlama*) | **9** (değişmedi) |
> | Bu dosyadaki `depends_on` kenarı | **0** (bu dosya kenar **taşımaz**) | **0** |
> | Bu derinlemede eklenen node / kapı / kenar | — | **0** (yeni kapı, yeni kademe yok) |
> | Bu derinlemede eklenen URL / yüzde / ölçüm | — | **0** (dış kaynak kullanılmadı) |
>
> **Uyarı:** `§C`–`§J` *yorum, uygulama rehberi ve çapraz referans* katmanıdır;
> yeni RUNTIME kademesi, yeni kapı, yeni kenar **iddia etmez**. Kapı
> numaralarının (K1–K9) **uyarlama** olduğu gerçeği korunur (§B + Lejant):
> kapılar node **değildir**, v2 §5.3 satırlarından türetilmiş **açıklama
> katmanıdır**. Kaynakta olmayan her şey `⚠️ VERIFICATION REQUIRED` veya
> `UNKNOWN` ile işaretlidir; yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) **kullanılmamıştır**.

## §D K1–K9 Kapıları Derinlemesine (§B'nin kapı-tarafı açılımı)

> Her kapı §B'deki bir `↓` geçişidir. Aşağıdaki "Bu kapıda ne aranır" ve
> "atlanırsa" sütunları **yorum katmanıdır** (v2 §5.3 yalnız başlıkları verir);
> yeni olgu/ölçüm **iddia etmez**.

### §D.1 K1 — Complexity ↓ Structure

| Alan | Değer |
|---|---|
| Geçiş | karmaşıklık → yapı |
| Solda (gelen) | `Complexity` — ham, dağınık gereksinim/bilgi |
| Sağda (giden) | `Structure` — düzenlenmiş yapı |
| Bu kapıda ne aranır *(yorum)* | gereksinimin yapıya oturup oturmadığı |
| Kapı atlanırsa *(yorum)* | yapı, kanıtsız karmaşıklıktan türetilir |
| Setteki karşılığı | `enterprise-architecture.md` §A/§B (yapının kendisi) |

### §D.2 K2 — Dependencies ↓ Boundaries

| Alan | Değer |
|---|---|
| Geçiş | bağımlılıklar → sınırlar |
| Solda (gelen) | `Dependencies` |
| Sağda (giden) | `Boundaries` |
| Bu kapıda ne aranır *(yorum)* | bağımlılığın sınır içinde/de dışında olduğu |
| Kapı atlanırsa *(yorum)* | sınır, bağımlılık haritası olmadan çizilir |
| Setteki karşılığı | `dependency-graph.md` (bağımlılık = **kenar verisi**) · sınır = K/kanıt katmanları |

### §D.3 K3 — Technology ↓ Abstraction

| Alan | Değer |
|---|---|
| Geçiş | teknoloji → soyutlama |
| Solda (gelen) | `Technology` |
| Sağda (giden) | `Abstraction` |
| Bu kapıda ne aranır *(yorum)* | teknolojinin soyut katman arkasına mı geçtiği |
| Kapı atlanırsa *(yorum)* | soyutlama katmanı teknoloji isimleriyle kirlenir |
| Setteki karşılığı | K kademe soyutlaması (`domain-architecture.md` §B) |

### §D.4 K4 — Hardware ↓ Drivers

| Alan | Değer |
|---|---|
| Geçiş | donanım → sürücüler |
| Solda (gelen) | `Hardware` |
| Sağda (giden) | `Drivers` |
| Bu kapıda ne aranır *(yorum)* | donanımın sürücü üzerinden mi görüldüğü |
| Kapı atlanırsa *(yorum)* | sürücü katmanı kanıtsız kalır |
| Setteki karşılığı | `hardware-audio-architecture.md` (K001-donanım · K003 `driver/`) |

### §D.5 K5 — Drivers ↓ Audio Engine

| Alan | Değer |
|---|---|
| Geçiş | sürücüler → ses motoru |
| Solda (gelen) | `Drivers` |
| Sağda (giden) | `Audio Engine` |
| Bu kapıda ne aranır *(yorum)* | motorun sürücüye bağımlılığı |
| Kapı atlanırsa *(yorum)* | motor, sürücü gerçekliği olmadan anlatılır |
| Setteki karşılığı | `hardware-audio-architecture.md` §A (K003 `engine/` + `driver/`) |

### §D.6 K6 — Audio Engine ↓ Application

| Alan | Değer |
|---|---|
| Geçiş | ses motoru → uygulama |
| Solda (gelen) | `Audio Engine` |
| Sağda (giden) | `Application` |
| Bu kapıda ne aranır *(yorum)* | motorun uygulama katmanına açılan yüzeyi |
| Kapı atlanırsa *(yorum)* | uygulama, motorun gerçek arayüzünü **varsayar** |
| Setteki karşılığı | `domain-architecture.md` §A (`K011-application/`) |

### §D.7 K7 — Application ↓ API / Services

| Alan | Değer |
|---|---|
| Geçiş | uygulama → API / servisler |
| Solda (gelen) | `Application` |
| Sağda (giden) | `API / Services` |
| Bu kapıda ne aranır *(yorum)* | uygulamanın servis/API'ye açılan yüzeyi |
| Kapı atlanırsa *(yorum)* | API, uygulama iç mantığından **türetilmiş** görünür |
| Setteki karşılığı | `domain-architecture.md` §A (`K012-services/` · K000 `isletim-sistemi-api/`) |

### §D.8 K8 — Services ↓ Clients

| Alan | Değer |
|---|---|
| Geçiş | servisler → istemciler |
| Solda (gelen) | `Services` |
| Sağda (giden) | `Clients` |
| Bu kapıda ne aranır *(yorum)* | servisin hangi istemciye açıldığı |
| Kapı atlanırsa *(yorum)* | istemci, servis sözleşmesi olmadan çizilir |
| Setteki karşılığı | `UNKNOWN` — bu sette 5 diyagramın hiçbiri istemci katmanını **çizmez** → `⚠️ VERIFICATION REQUIRED` |

### §D.9 K9 — Clients ↓ Users

| Alan | Değer |
|---|---|
| Geçiş | istemciler → kullanıcılar |
| Solda (gelen) | `Clients` |
| Sağda (giden) | `Users` |
| Bu kapıda ne aranır *(yorum)* | istemcinin kullanıcıyla teması |
| Kapı atlanırsa *(yorum)* | kullanıcı, arayüz/erişilebilirlik kanıtı olmadan **varsayılır** |
| Setteki karşılığı | `UNKNOWN` — kullanıcı katmanı bu sette **çizilmiyor** → `⚠️ VERIFICATION REQUIRED` |

### §D.10 Kapı tablosu (tek bakışta)

| Kapı | Geçiş | Sette karşılığı | Kanıt durumu |
|---|---|---|---|
| K1 | Complexity → Structure | `enterprise-architecture.md` | yorum katmanı |
| K2 | Dependencies → Boundaries | `dependency-graph.md` | yorum katmanı |
| K3 | Technology → Abstraction | `domain-architecture.md` | yorum katmanı |
| K4 | Hardware → Drivers | `hardware-audio-architecture.md` | yorum katmanı |
| K5 | Drivers → Audio Engine | `hardware-audio-architecture.md` | yorum katmanı |
| K6 | Audio Engine → Application | `domain-architecture.md` | yorum katmanı |
| K7 | Application → API / Services | `domain-architecture.md` | yorum katmanı |
| K8 | Services → Clients | **bu sette yok** | `⚠️ VERIFICATION REQUIRED` |
| K9 | Clients → Users | **bu sette yok** | `⚠️ VERIFICATION REQUIRED` |

> Kapı ↔ dosya eşlemesi **yorum katmanıdır** (v2 §5.3 tek tek dosya eşlemesi
> vermez); eşleme, dosyaların kendi konularından **türetilmiştir**.

## §E RUNTIME Kolu — Bilinen / Bilinmeyen Envanteri

### §E.1 §A'da bilinen her şey

| # | Öğe | Kaynak | Durum |
|---|---|---|---|
| 1 | `K000–K5000 ARCHITECTURE REGISTRY` (kolun kökü) | v2 §1.2 (§A birebir) | ✅ tanımlı |
| 2 | üç kol ayracı: DOMAIN · RUNTIME · DATA | v2 §1.2 | ✅ tanımlı |
| 3 | `RUNTIME → PROCESS` | v2 §1.2 | ✅ tanımlı |

**Toplam: 3 bilinen öğe.** Daha fazlası §A'da **yoktur**.

### §E.2 §A'da bilinmeyen her şey (doldurulması yasak)

| # | Soru | Cevap |
|---|---|---|
| 1 | PROCESS altındaki ilk kademe ne? | `UNKNOWN` |
| 2 | RUNTIME kolu kaç kademeden oluşur? | `UNKNOWN` (§A'da tek geçiş çiziliyor) |
| 3 | DOMAIN kolu PROCESS'e bağlanır mı? | `UNKNOWN` — §A'da üç kol **aynı** kökten ayrı, iç bağlantı **çizilmemiş** |
| 4 | DATA kolu ile RUNTIME kolu ilişkili mi? | `UNKNOWN` — §A'da ilişki **yok** |
| 5 | PROCESS = işletim sistemi süreci mi? | `UNKNOWN` — v2 §1.2 bu kutuyu **açıklamıyor** (§A'ya alınan bölümde) |
| 6 | RUNTIME ≠ PROCESS farkı ne? | `UNKNOWN` — iki ayrı kutu, tanım bu dosyada yok |
| 7 | Kapı K1–K9 bu kolla ilişkili mi? | İlişki **kanıtlanmadı** — kapılar §5.3'ten (okuma sırası), kol §1.2'den gelir; **aynı şey değildir** |

### §E.3 Bilinen / bilinmeyen dengesi (bu derinlemede değişmedi)

| Kalem | Sayı |
|---|---|
| §A'da tanımlı RUNTIME-PROCESS öğesi | 1 geçiş (`RUNTIME → PROCESS`) |
| §A'da tanımsız alt kademe | `UNKNOWN` (adı yok) |
| Bu derinlemede eklenen node | **0** |
| Bu derinlemede giderilen `UNKNOWN` | **0** (hiçbir `UNKNOWN` kapatılmadı) |

## §F Okuma Sırasının Uygulanışı — Rehber ve İhlal Senaryoları

### §F.1 Sıranın anlamı (§B'den sapmasız)

§B sırası: `Complexity → Dependencies → Technology → Hardware → Drivers →
Audio Engine → Application → API/Services → Clients → Users` — başlıktaki
tanım: **karmaşıklıktan sadeliğe**. Sıra **zorunlu okuma sırasıdır** (§B
"Kapılar" notu); atlanan kapı = kanıtsız adım.

### §F.2 Doğru uygulama (adım adım — yorum katmanı)

1. **K1'den başla:** önce karmaşıklık, sonra yapı — yapıyı okumadan teknolojiye inme.
2. **K2'de bağımlılık haritasını aç:** bu sette karşılığı `dependency-graph.md` (2 kanıtlı kenar).
3. **K3'te soyutlamayı doğrula:** teknoloji adlarını kademe dışı yazma.
4. **K4–K5'te somutla:** donanım → sürücü → motor (`hardware-audio-architecture.md`).
5. **K6–K7'de katman geçişi:** motor → uygulama → API/servis.
6. **K8–K9'da dur:** bu sette istemci/kullanıcı katmanı **yok** → `⚠️ VERIFICATION REQUIRED`; kapı **atlanmaz**, işaretlenerek ilerlenir.
7. Her kapıda kanıt iste: `EVIDENCE` (v2 §5.2 zinciri — `dependency-graph.md` §D).

### §F.3 Tipik ihlaller (bu dosyanın kurallarına göre)

| # | İhlal | Sonuç | Doğrusu |
|---|---|---|---|
| 1 | K9'dan başlayıp "kullanıcı ihtiyacı" ile teknoloji seçmek | sıra tersine döner | K1'den başla |
| 2 | Kapıları atlayıp doğrudan `Audio Engine`'e inmek | kanıtsız adım | K1–K5 zorunlu |
| 3 | Kapıları `depends_on` sanmak | kapı ≠ kenar | kenarlar `dependency-graph.md` §B'de |
| 4 | `PROCESS` altında kademe **uydurmak** | uydurma node | `UNKNOWN` bırak (§E.2) |
| 5 | Kapı sırasını "öneri"ye indirgemek | §B "zorunlu okuma sırasıdır" | sıra korunur |
| 6 | K8/K9'u bu sette **varmış** gibi göstermek | kanıtsız katman | `⚠️ VERIFICATION REQUIRED` (§D.8/§D.9) |
| 7 | Kapı numaralarını v2'ye ait saymak | kaynakla çelişki | kapılar **uyarlamadır** (Lejant) |

## §G Diyagram Seti Çapraz Referans (5 dosya)

| Dosya | Setteki rolü | Bu dosyayla ilişkisi |
|---|---|---|
| `enterprise-architecture.md` | canonical graf + hiyerarşi | §A'daki üç kolun **tam** hâli oradadır (§A: "Tam görünüm … §B" notu ile aynı) |
| `domain-architecture.md` | 25 klasör ağacı + band cetveli | K6/K7'deki `application`/`services` birimleri orada |
| `dependency-graph.md` | kenar verisi + doğrulama zinciri | K2 kapısının (Dependencies) somut verisi orada |
| `hardware-audio-architecture.md` | K000/K001/K003/K004/K005 blokları | K4/K5 kapılarının (Hardware · Drivers · Audio Engine) blok-görseli orada |
| **bu dosya** | RUNTIME/PROCESS kolu + K1–K9 okuma sırası | setin **okuma yöntemi** |

## §H Set İçi Tutarlılık Kontrol Listesi (bu derinlemede yapılan çapraz okuma)

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | §A üç kol = `enterprise-architecture.md` §B üç kol? | ✅ `DOMAIN · RUNTIME · DATA` — aynı |
| 2 | §A kök kutu = o dosyadaki kök? | ✅ `K000–K5000 ARCHITECTURE REGISTRY` |
| 3 | §A `RUNTIME → PROCESS` = o dosyadaki kol? | ✅ aynı — o dosyada PROCESS **alt kademesi de yok** |
| 4 | Kapılar grafikte node mu? | ❌ Hayır — Lejant: kapılar **uyarlamadır**, node/kenar olarak grafiğe **alınmadı** |
| 5 | Kapı sırası ile ağaç sırası aynı mı? | Hayır — kapılar okuma **yöntemi**, ağaç envanterdir; biri diğerinin **yerine geçmez** |
| 6 | Bu dosyada kenar var mı? | **0** — gerçek kenarlar `dependency-graph.md` §B'de (2 adet) |
| 7 | PROCESS altı dolduruldu mu? | Hayır — §E.2'de 7 soru `UNKNOWN` olarak **kilitli** |
| 8 | Yeni URL/ölçüm/kanıt eklendi mi? | ✅ **0** |

## §I SSS — Sık Sorulan Sorular

**S1: Kapılar (K1–K9) mimari düğüm mü?**
Hayır. Lejant: kapı numaraları **uyarlamadır** (v2 §5.3 satırlarından türetildi);
node/kenar olarak grafiğe **alınmadı**.

**S2: K1–K9 ile K000–K5000 karışır mı?**
Karıştırılmaz: `K0xx` = **domain klasörü numarası** (envanter), `K1..K9` = **okuma
kapısı numarası** (yöntem). Bu dosyada yalnız ikincisi geçerli.

**S3: Kapı sırası ile `depends_on` yönü aynı şey mi?**
Hayır. Kapılar **okuma sırasıdır**; `depends_on` **muhtaçlık yönüdür** ve o
veri `dependency-graph.md`'dedir (2 kenar).

**S4: `PROCESS` altında ne var?**
`UNKNOWN` — §A'da tanımlı değildir; uydurulmaz (§E.2).

**S5: `DOMAIN` kolu `RUNTIME` koluna bağlanır mı?**
§A/§B'de iç bağlantı **çizilmemiş** → `UNKNOWN` (§E.2 #3).

**S6: Kapılar zorunlu mu?**
Evet — §B notu: kapı sırası **zorunlu okuma sırasıdır**, atlanan kapı kanıtsız
adımdır (§F.1).

**S7: K8/K9 neden "bu sette yok"?**
Bu 5 diyagramın hiçbiri `Clients`/`Users` katmanını **çizmiyor** (dosya
envanterinden) → `⚠️ VERIFICATION REQUIRED`; kapı **silinmedi**, işaretle
ilerlenir.

**S8: Kapı ↔ dosya eşlemesi kaynaktan mı?**
Hayır — **yorum katmanıdır**; v2 §5.3 dosya eşlemesi vermez (§D.10 notu).

**S9: Bu dosyanın kanıt oranı nedir?**
§C tablosu: 1 tanımlı geçiş (`RUNTIME → PROCESS`) · 9 kapı (uyarlama) ·
0 kenar · 0 yeni node · 0 ölçüm — hepsi **değişmedi**.

**S10: `index.md` §3 ile ilişki ne?**
Kaynak Atfı: "Dönüşüm: `.ai/architecture/index.md` §3" — bu derinlemede
`index.md` **okunmadı** (beyazliste dışı yüzey); çelişki çıkarsa `⚠️
VERIFICATION REQUIRED` + üst karar.

## §J Terim ve Sembol Ek Açıklamaları (Lejant devamı)

| Terim / sembol | Bu dosyadaki anlamı | Belirsiz tarafı |
|---|---|---|
| `RUNTIME` | üç ana kolun 2.'si (v2 §1.2) | kolun adının tanımı `UNKNOWN` |
| `PROCESS` | RUNTIME kolunun görünen son kutusu | altı `UNKNOWN` |
| `↓` | okuma sırası geçişi | `depends_on` **değildir** |
| `▼` | kol/iç akış iniş yönü | kenar **değildir** |
| `K1…K9` | okuma kapısı — **uyarlama** | v2'de numara **yoktur** |
| zorunlu okuma sırası | §5.3'ün başlığı: karmaşıklıktan sadeliğe | sıra **tavsiye değil**, kapı kuralı |
| Complexity | okumanın başladığı ham katman | tanımı bu dosyada yok (`UNKNOWN`) |
| Boundaries | K2'nin hedefi — sınır | somut sınır listesi bu sette yok |
| Abstraction | K3'ün hedefi — soyutlama | — |
| Users | K9'un hedefi — kullanıcı katmanı | bu sette **çizilmiyor** → ⚠️ |

## §K Kapı Uygulama Senaryoları (adım adım — yorum katmanı; yeni olgu yok)

> Üç senaryo, §B'deki sıranın **nasıl işletileceğini** gösterir. Hiçbirinde
> yeni node/kenar/ölçüm **üretilmez**; `UNKNOWN` olan yerde kapı **işaretlenip**
> geçilir (kapatılmaz).

### §K.1 Senaryo A — Bir domain kaydı okunuyor (K1 → K3)

| Adım | Kapı | Yapılan | Çıktı |
|---|---|---|---|
| 1 | K1 | kaydın karmaşıklığı kabul edilir, önce **yapı** sorulur | yapı = K/klasör kademesi (`domain-architecture.md` §A) |
| 2 | K2 | bağımlılık sorulur → tek kaynak `dependency-graph.md` §B | **2** kenar · 24 iskelet |
| 3 | K3 | teknoloji adları soyut kademe arkasına **itilir** | teknoloji listesi **yazılmaz** (bu sette yok ⚠️) |
| durak | — | K3 sonrası kanıt yoksa **dur** | `⚠️ VERIFICATION REQUIRED` |

### §K.2 Senaryo B — Donanım → motor zinciri okunuyor (K4 → K6)

| Adım | Kapı | Yapılan | Çıktı |
|---|---|---|---|
| 1 | K4 | donanım bloğu açılır | `hardware-audio-architecture.md` §A/§B (5 blok) |
| 2 | K5 | sürücü → motor geçişi aranır | K003 `driver/` + `engine/` **mevcut** (klasör olarak) |
| 3 | — | aradaki `depends_on` sorulur | **2** kenar (K003→K001 · K003→K004) |
| 4 | K6 | motor → uygulama yüzeyi aranır | yüzey `UNKNOWN` → ⚠️ işaretlenir |
| durak | K6 | kanıtsız uygulama iddiası **yazılmaz** | — |

### §K.3 Senaryo C — Uc kapılar (K7 → K9)

| Adım | Kapı | Yapılan | Çıktı |
|---|---|---|---|
| 1 | K7 | API/servis katmanı aranır | sette `K012-services/` ve `isletim-sistemi-api/` **klasör olarak** var; API **sözleşmesi** çizilmedi → ⚠️ |
| 2 | K8 | istemci katmanı aranır | **bu sette yok** → `⚠️ VERIFICATION REQUIRED` — kapı **atlanmaz**, boş işaretlenir |
| 3 | K9 | kullanıcı katmanı aranır | **bu sette yok** → aynı davranış |
| sonuç | — | seri tamamlanır | 9 kapının **tümü** işaretli; gizlenen boşluk **yok** |

**Ortak kural:** kapı, karşılığı sette yoksa **"atlandı"** sayılmaz —
**"işaretlenerek geçildi"**; gizleme/sahte dolgu **yasak**.

## §L Append-Only Doğrulama Kontrol Listesi (bu dosya için — her değişimde)

1. [ ] §A/§B şemalarından **bir satır silinmiş/değiştirilmiş mi?** → yasak.
2. [ ] Yeni kapı (K10+) eklendi mi? → kaynak §5.3'te **yok** → RED (kapı = uyarlama, artırılmaz).
3. [ ] `PROCESS` altına node **uyduruldu mu?** → RED (§E.2 `UNKNOWN` kilitli).
4. [ ] Kapı numaraları `K0xx` domain numaralarıyla **karıştırıldı mı?** → RED (§I S2).
5. [ ] Kapı ≠ kenar ayrımı korunmuş mu? (Lejant + §D.10)
6. [ ] K8/K9 "bu sette yok" işareti **silinmiş mi?** → silinmişse RED.
7. [ ] Yüzde/ölçüm/URL eklendi mi? → bu derinlemede **0**; eklendiyse
      `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`
      ile HTTP 200 doğrulanır.
8. [ ] Yer tutucu desen (koseli parantez yer tutucusu veya İngilizce yer belirteci kısaltması) var mı? → RED.
9. [ ] Dosyanın **son satırı** tek Authority satırı mı? · Dürüstlük bloğu (§C) geçerli mi?
10. [ ] `git commit` **atılmadı** mı?

## §M Ek SSS (§I devamı)

**S11: Kapı sırası "yönerge" mi, "kural" mı?**
§B'nin kendi notu: kapı sırası **zorunlu okuma sırasıdır**; numaralandırma
**uyarlamadır** (kural metni §5.3'te, numara değil).

**S12: Bir kapı atlanırsa ne olur?**
§B: atlanan kapı = **kanıtsız adım** (§F.3 #2). Kapıyı **kapatmak** yerine
işaretleyip kanıt aramak gerekir (§K.3).

**S13: `RUNTIME` kolu ile kapılar aynı şey mi?**
Hayır — kol **yapıdır** (§A), kapı **okuma yöntemidir** (§B); §E.2 #7'de
ilişkinin kanıtlanmadığı kayıtlıdır.

**S14: K8/K9 bu sette hiç dolmayacak mı?**
Bilinmiyor → bu derinlemede `⚠️ VERIFICATION REQUIRED` olarak **bekletilir**;
"yakında dolar" gibi garanti **verilmez**.

**S15: Kapı ↔ dosya eşlemesi değişebilir mi?**
Evet — bu **yorum katmanıdır** (§D.10 notu); değişimi kaynak değil, setin
konu dağılımı belirler ve değişiklikte eşleme **yeniden gerekçelendirilir**.

**S16: `Complexity` … `Users` terimlerinin resmi tanımı var mı?**
Bu dosyaya alınan bölümde **yok** → §J'de belirsizlik `UNKNOWN` olarak
işaretlidir; tanım **uydurulmaz**.

**S17: Bu derinlemenin toplam katkısı nedir?**
Yorum + senaryo + kontrol listesi; **0 node · 0 kapı · 0 kenar · 0 URL · 0 ölçüm**.

## §N Sıra Okuma Mini-Tutorial (bu dosyayı ilk kez okuyan için)

**Adım 1 — Ne tür bir dosya:** bu dosya iki şey taşır: **RUNTIME/PROCESS kolunun**
set-içi alt-kümesini (§A) ve **hedef okuma sırasını** (§B) — giriş notu.

**Adım 2 — Kolun sınırını gör:** §A — `RUNTIME → PROCESS` ve **biter**;
altındaki kademe `UNKNOWN`'dur (§E.2), uydurulmaz.

**Adım 3 — Sırayı zorunlu uygula:** §B — `Complexity → … → Users`,
"karmaşıklıktan sadeliğe"; kapı sırası **zorunlu okuma sırasıdır**, atlanan
kapı = kanıtsız adım (§F).

**Adım 4 — Kapıların statüsünü unutma:** `K1…K9` **uyarlamadır** — v2'de
numara **yoktur**, node **değildir** (Lejant + §D.10). `K0xx` domain
numaralarıyla **karıştırılmaz** (§I S2).

**Adım 5 — Kapı ↔ kenar ayrımı:** kapı = okuma yöntemi; kenar = `depends_on`
(2 tane, `dependency-graph.md` §B). İkisi birbirine **çevrilmez**.

**Adım 6 — Boş kapıları gizleme:** K8/K9 bu sette **yok** → **işaretleyip**
geç (§K.3); kapatma/sahte dolgu **yasak**.

**Adım 7 — Şüphe durumunda:** §F.2/§L — kanıt yoksa kapıda **dur**,
`⚠️ VERIFICATION REQUIRED` yaz, üst kararı bekle.

**Mini-tutorial'ın kanıt katkısı:** **0** — yalnız §A–§J'dekilerin sıralı
okunma tarifidir.

## §O Bu Derinlemenin Kaydı (append-only günlüğü)

| # | İşlem | Sonuç / kanıt |
|---|---|---|
| 1 | Dosya okundu (önce) | 95 satır |
| 2 | `§C–§O` blokları **sona** eklendi | §A/§B şemalarının **hiçbir satırı** silinmedi/değiştirilmedi |
| 3 | Dürüstlük bloğu (§C) yazıldı | kanıt oranı **1 tanımlı geçiş · 9 kapı · 0 bu-dosya-kenarı** — değişmedi |
| 4 | Yeni kapı/node/kenar/URL/ölçüm üretilmedi | **0 / 0 / 0 / 0** |
| 5 | `PROCESS` altı `UNKNOWN` kapatıldı mı? | **hayır** — §E.2'de 7 soru **açık** kaldı |
| 6 | Dış kaynak (web) kullanılmadı | doğrulanacak URL **0** |
| 7 | Dosya ölçüldü (`Get-Content -Encoding UTF8`) | son satır = tek Authority satırı |
| 8 | `git commit` | **ATILMADI** (orkestratöre aittir) |

**Değişiklik yüzeyi:** yalnız bu dosya (beyazliste #5). `index.md` ·
`AGENTS.md` · `CLAUDE.md` · `wasapi/` ve diğer dosyalara **dokunulmadı**.

**Authority:** Vault Steward — `.ai/architecture/diagrams/runtime-architecture.md` (kaynak sadakati: v2 spec §1.2 · §5.3 · append-only derinleştirme 2026-10-13)
