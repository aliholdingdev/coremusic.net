---
title: "routing — Kapsam"
type: architecture-scope
node_id: K003-audio/routing
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# routing — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 7 bu dalı **"Kanal/akış yönlendirme kuralları"**
olarak yazar. Basitçe: hangi sesin **hangi kanala/çıktıya** gideceğini belirleyen
anahtar tablosu. Bir havalimanı yönlendirme panosu gibi: uçuş (ses akışı) aynı,
gidiş kapısı (kanal/çıktı) farklı — hangisinin nereye gideceğini pano
(rule-tablosu) söyler. PipeWire örneğinde bu "nodes + ports + links" yapısıdır
(master §5 T17).

> ⚠️ VERIFICATION REQUIRED: tanım yalnız v2 §2.3'teki **adından** türetilmiştir
> (v2 §3 KURAL 3); içerik iddiası değildir. Yönlendirme topolojisi (döngüsel mi,
> sabit mi), kanal sayısı ve dinamiklik kanıt turunda netleşecektir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Kanal/akış yönlendirme kurallarının tanımlanması (kaynak → hedef eşlemesi) | Kuralların matematiksel uygulanması (gain/toplama) | mixing (master §3 satır 8) |
| 2 | Bağlantı grafiğinin (link/port) mantıksal yapısı | Grafiği yürüten ana döngü | engine (master §3 satır 1) |
| 3 | Kural değişikliğinin (yeniden yönlendirme) niyeti taşınması | Değişikliğin zamanlaması (click-free geçiş) | realtime (master §3 satır 9) |
| 4 | Yanlış yönlendirmenin (yanlış cihaz/kanal) önlenmesi niyeti | Çıkış cihazının seçimi | device (master §3 satır 2) |
| 5 | *(dışında)* DSP zincirinin içindeki sıralama | Efekt sırası | K004-dsp pipeline (master §2.2) |
| 6 | *(dışında)* hoparlör/kulaklık cihazı seçimi kararı | Cihaz envanteri | device (master §3 satır 2) |
| 7 | *(dışında)* yönlendirilen verinin buffer'ı | Bellek/kuyruk altyapısı | backend (master §3 satır 4) |
| 8 | *(dışında)* yönlendirme hatasının raporlanması | Sınıflandırma ve kabul kriteri | diagnostics · testing |

**Yasak:** satır 1'deki ayrım (routing ↔ mixing) v2 §2.3'te ayrı çocuklar
olduğu için **bağlayıcıdır**; ikisi aynı dosyada birleştirilemez (KURAL 1 —
çocuk adları birebir korunur).

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| T1 | routing ↔ mixing | eşleme kuralı (kaynak→hedef) ↔ sinyal birleştirme (toplama/gain) | ✅ master §3 satır 7/8 (ayrı çocuklar) |
| T2 | routing ↔ device | mantıksal hedef (kanal) ↔ fiziksel cihaz seçimi | ⚠️ master §8 Q6 ile bağlantılı |
| T3 | routing ↔ engine | kural grafiği ↔ grafiği yürüyen döngü | ⚠️ ad-türetme (master §3 satır 7 ve 1) |
| T4 | routing ↔ K004-dsp pipeline | akış topolojisi ↔ efekt sıralaması | ✅ master §2.2 sınır kuralı (sinyal gider, kod girmez) |
| T5 | routing ↔ realtime | neyin nereye gideceği ↔ ne zaman güncelleneceği | ⚠️ ad-türetme (master §3 satır 7 ve 9) |
| T6 | routing ↔ backend | bağlantı tablosu ↔ bağlantının buffer'ı | ⚠️ ad-türetme (master §3 satır 7 ve 4) |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "routing · Kanal/akış yönlendirme kuralları · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 68 | ✅ dosya okundu |
| 2 | PipeWire media graph = nodes + ports + links; node = medya işleme elemanı, link iki portu bağlar | `audio-master.md` §5 T17 → https://docs.pipewire.org/devel/page_objects_design.html | ✅ dosya okundu (URL fetch) |
| 3 | PipeWire port bazlı Latency nesnesi (direction + min/max); linked node latency'leri toplanır | `audio-master.md` §5 T16 → https://docs.pipewire.org/page_latency.html | ✅ dosya okundu (URL fetch) |
| 4 | AUGraph ses işleme zincirini oluşturur; düğüm eklenir, graf output unit ile biter | `audio-master.md` §4 A3 (Apple: ARoadmaptoCommonTasks) | ✅ dosya okundu |
| 5 | Sınır kuralı: K003 içinden K004'e sinyal gönderilir, içine sokulmaz | `audio-master.md` §2.2 | ✅ dosya okundu |
| 6 | Repo'da yönlendirme/kanal eşleme kodu yok | kök `*.cpp`/`*.h` glob 0 isabet (2026-10-12) | ✅ shell ölçümü |
| 7 | 18 veritabanı şeması vardır; ses yönlendirme şeması var mı? | `.ai/.sql/mysql/` envanteri (master §9) | ⚠️ VERIFICATION REQUIRED — ses şeması doğrulanmadı |
| 8 | Yönlendirme topolojisi (sabit/dinamik/kaç kanal) | — | **UNKNOWN** (tasarım yok) |
| 9 | Yeniden yönlendirme anında click-free davranış zorunluluğu | — | **UNKNOWN** (kriter yok) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | Yönlendirme topolojisi sabit mi, dinamik mi (kaç kanal/hedef)? | **UNKNOWN** — tasarım yok | embedded-engineer |
| R2 | routing ↔ mixing sınırı tam olarak nerededir (gain kimde)? | ⚠️ VERIFICATION REQUIRED | Vault Steward |
| R3 | Yeniden yönlendirme hangi anda uygulanır (block kenarı mı)? | ⚠️ VERIFICATION REQUIRED | realtime |
| R4 | K004 pipeline sırası ile K003 routing sırası nasıl ilişkilenir? | ⚠️ VERIFICATION REQUIRED (master Q5 komşusu) | Vault Steward |
| R5 | Yanlış yönlendirme nasıl tespit edilir (test kriteri)? | ⚠️ VERIFICATION REQUIRED | testing |
| R6 | Backend'de kaç bağlantının eşzamanlı tutulacağı (link sayısı üst sınırı) | **UNKNOWN** — kanıt yok | backend |

**Kapanış:** status: draft kalır. R1 kapanmadan kanal/hedef sayısı yazılmaz;
R3 için block-size/örnek sayımı uydurulmaz → UNKNOWN.

## §6 Yönlendirme matrisi şablonları (kaynak → hedef)

§2 satır 1'deki "kaynak → hedef eşlemesi" maddesinin matris şablonu. Matris
**kavramsaldir**: satırlar kaynak kanalları, sütunlar hedefleri gösterir;
girdi değerleri (gain, yönlendirme var/yok) **karar değildir**. Boş hücreler
"karar yok" demektir.

### §6.1 Temel matris (stereo kaynak → stereo çıkış — şablon)

| Kaynak \ Hedef | Sol çıkış | Sağ çıkış | Sahip |
|---|---|---|---|
| Kaynak Sol | eşlendi | boş | routing |
| Kaynak Sağ | boş | eşlendi | routing |

### §6.2 Çoklu çıkış matrisi (ayna — şablon, R3 kararı yok)

| Kaynak \ Hedef | Hoparlör Sol | Hoparlör Sağ | Kulaklık Sol | Kulaklık Sağ |
|---|---|---|---|---|
| Karışım (mixing çıkışı) | eşlendi | eşlendi | eşlendi | eşlendi |
| Durum | ⚠️ R3 kararı yok | ⚠️ | ⚠️ | ⚠️ |

### §6.3 Çoklu kaynak matrisi (mixing girişi — şablon)

| Kaynak \ Hedef | Bus A | Bus B | Doğrudan çıkış |
|---|---|---|---|
| Kaynak 1 | eşlendi | boş | boş |
| Kaynak 2 | eşlendi | eşlendi | boş |
| Sahip | routing (eşleme) + mixing (toplama) | | |

**Matris kuralı (şablon):** bir hücrenin **sahibi** iki dalın sınırıdır:
"nerelere gider" (routing) ↔ "orada ne olur" (mixing gain/toplama). §6.3'teki
çift sahiplik, tam olarak R2'deki (routing ↔ mixing sınırı) sorudur — bu
dosyada çözülmez, işaretlenir.

## §7 Çapraz bağlama (patching) şablonları

§2 satır 2'deki "bağlantı grafiğinin (link/port) mantıksal yapısı" maddesi.
PipeWire modeli (T17 ✅: node + port + link) bu şablonun **dayanağıdır**.

| # | Şablon | Tanım | Ne taşır | Sahip |
|---|---|---|---|---|
| 1 | Port | Düğümün uç noktası (yön: giriş/çıkış) | Kanal | routing |
| 2 | Link | İki portu bağlar | Yönlendirme kararı | routing |
| 3 | Node | Medya işleme elemanı | K003 içi düğüm ya da K004 eki | engine · K004 |
| 4 | Patchbay (bağlantı panosu) | Tüm link'lerin görünür tablosu | Durum (aktif/pasif) | routing |

**Port yönü kuralı (şablon):** bir link yalnız **çıkış portu → giriş portu**
yönüne kurulur; ters yön ve döngü (loopback) **geçersizdir** (yanlış yönlendirme
önleme — §2 satır 4). Döngü tespiti graf kontrolüdür: kaynak→hedef zincirinde
kaynağa geri dönülürse link **reddedilir** (niyet; uygulama yok).

## §8 Bus topolojileri (şablon — karar yok)

§2 satır 1'in "akış topolojisi" uzantısı. Bus = birden fazla kaynağın
toplandığı mantıksal kanal grubu. **Hangi topolojinin seçileceği R1'dir.**

| # | Topoloji | Tanım | Tipik kullanım | Durum |
|---|---|---|---|---|
| 1 | Tek stereo bus | Tüm kaynaklar tek stereo çıkışa | Basit oynatıcı | varsayılan aday |
| 2 | Alt grup bus (subgroup) | Kaynaklar önce gruplanır, gruplar karıştırılır | Stüdyo miksaj | ⚠️ R1 |
| 3 | Çoklu çıkış topolojisi | Kanallar ayrı uçlara (ör. surround düzeni) | Çoklu hoparlör | ⚠️ R1 + R3 |
| 4 | Döngüsel topoloji | Bus çıktıları geri girişe beslenir | Efekt döngüsü | **yasak** (§7 loopback kuralı) |

**Çoklu çıkış senaryosu (dikkat — domain hedefi ile çakışma riski):** CoreMusic
alan tanımında "8.1 surround" bir **hedef senaryodur**; ancak K003'te bu
topolojinin **onaylı karar olarak kaydı yoktur** → ⚠️ VERIFICATION REQUIRED.
§6.2/§8 satır 3 yalnız şablon statüsündedir; kanal sayısı uydurulmaz.

## §9 Değişken routing ve click-free geçiş şablonu

§2 satır 3'teki "yeniden yönlendirme niyeti" maddesi. §2 satır 3 ayrıca
"değişikliğin zamanlaması (click-free geçiş) realtime dalındadır" der — bu
bölüm yalnız **niyet** taşır.

| # | Değişiklik | Niyet davranışı | Zamanlama (niyet) | Sahip |
|---|---|---|---|---|
| 1 | Kaynak → hedef ekleme | Yeni link açılır; eski korunur | Blok kenarı | routing + realtime (R3) |
| 2 | Kaynak → hedef kaldırma | Link kapatılır; sinyal sessizliğe gider | Rampa ile kısma → sonra kapat | routing |
| 3 | Gain değişimi (matris hücresi) | Click-free rampa | Birkaç tick (süre UNKNOWN) | mixing |
| 4 | Topoloji değişimi (bus yapısı) | Yeni yapı kur → başlat → eskisini kapat | Çift tampon şablonu | engine |

**Click-free kuralı (niyet):** hiçbir yönlendirme değişikliği **anında** uygulanmaz;
blok kenarında ve en az bir rampa (crossfade) ile yapılır. Ramp süresi
**UNKNOWN** (ölçüm yok → sayı uydurulmaz).

## §10 Routing hata modları (şablon)

| # | Hata modu | Belirti | Önleme niyeti | Sahip |
|---|---|---|---|---|
| 1 | Yanlış hedef (yanlış cihaz/kanal) | Ses yanlış yerden çıkar | Matris doğrulaması + cihaz kimliği kontrolü | routing + device (Q6) |
| 2 | Çift yönlü link (loopback) | Rezonans/feedback | §7 döngü kontrolü | routing |
| 3 | Kopuk link (hedef yok) | Sessizlik | Bağlantı durumu sorgusu | routing + diagnostics |
| 4 | Yanlış gain (0 yerine 1) | Patlama/bozulma | Rampa + varsayılan 0 | mixing |
| 5 | Link taşması (çok fazla link) | Bütçe aşımı | Üst sınır (R6) + reddetme | backend |

## §11 Link sayısı ve kapasite (R6 — UNKNOWN)

§2 satır 7 ve R6 arasındaki soru: backend'de **kaç bağlantının** eşzamanlı
tutulacağı. Şablon:

| Katman | Üst sınır sorusu | Değeri |
|---|---|---|
| Tek düğüm port sayısı | Bir düğümün kaç portu olabilir | **UNKNOWN** |
| Toplam link sayısı | Graf genelinde kaç link | **UNKNOWN** |
| Eşzamanlı aktif link | Aynı anda kaç link ses taşır | **UNKNOWN** |
| Rezerv (dinamik ekleme için) | Yeni link için boşluk | **UNKNOWN** |

**Kural:** üst sınır **statik** (baştan allocate — T9) olacaktır; dinamik
genişleme RT yolunda yasaktır (realtime K8). Sayı uydurulmaz → UNKNOWN (R6).

## §12 Ölçüm ve kapanış kapıları (açık)

| Kapı | Koşul | Şu an |
|---|---|---|
| RO-G1 | Topoloji kararı (sabit/dinamik, bus yapısı — R1) | ❌ UNKNOWN |
| RO-G2 | routing ↔ mixing sınırı kararı (gain kimde — R2) | ❌ açık |
| RO-G3 | Geçiş zamanlaması kararı (blok kenarı — R3) | ❌ açık |
| RO-G4 | K004 pipeline ↔ routing sırası ilişkisi (R4) | ❌ açık |
| RO-G5 | Yanlış yönlendirme test kriteri (R5) | ❌ açık |
| RO-G6 | Link üst sınırı (R6) | ❌ UNKNOWN |

## §13 Yönendirme matrisi — okuma kuralı

Matris (§6) *kaynak satır × hedef sütun* okunur. Her hücre üç durumdan
biridir:

| Durum | Anlamı | Aksiyon |
|---|---|---|
| `✓` | Tasarım gereği bağlantı var | — |
| `—` | Bağlantı kasıtlı olarak yok | — |
| `?` | Henüz karar verilmedi | Kapıya bağla (§12) |

**Kural:** `?` hücresi karar verilmeden pipeline kodlaması başlamaz —
§2 komşu kapsamlar çakışmasın diye. Matriste `?` olan hücreler RO-G1…
RO-G6 kapılarına yazılıdır; kapı açılmadan hücre `✓`'ye çekilemez.

## §14 Matris → çalışma zamanı eşlemesi

| Matris hücresi | Çalışma zamanı karşılığı | Nerede işlenir |
|---|---|---|
| `✓` (kalıcı) | Statik bağlantı listesi | Başlatmada kurulur (T9) |
| `?` → karar | Bağlantı ekleme/çıkarma | Non-RT işlem, RT'ye atomik bayrak |
| `—` | Yok | Hiçbir döngüde ziyaret edilmez |

**Sınır:** bağlantı tablosu RT yolunda **değiştirilmez**; değişiklik
blok kenarına (§16) veya tampon değişimine (A/B) yayılır. Bu, kural 1
(zero-allocation) ve kural 2 (lock-free) ile uyumludur.

## §15 Çoklu çıkış senaryoları (şablon)

| # | Senaryo | Kaynak | Hedef | Karar durumu |
|---|---|---|---|---|
| 1 | Ana çıkış | Mix bus | Çıkış 1 | `✓` (kategori gereği) |
| 2 | Kulaklık | Mix bus | Çıkış 2 | `?` — ayrı mı mix mi? → RO-G2 |
| 3 | Yansıtma (monitor) | Kaynak | Çıkış 1 | `?` — gain kimde? → RO-G2 |
| 4 | Kayıt yolu | Kaynak | Kayıt bus'ı | `?` — pre/post fader? → RO-G3 |
| 5 | Test/jeneratör | Test kaynağı | Çıkış N | `?` — üretimde var mı? → RO-G1 |

Ölçüm/üst sınır yok → `UNKNOWN` (R6). Tablo **tasarım iskeletidir**,
mevcut sistem iddiası değildir.

## §16 Geçiş zamanlaması — blok kenarı modeli

Click-free geçiş (§9) için zamanlama kararı R3 ile kapıda. Model:

```text
[kenar modeli — şablon]
  non-RT  : "yeni bağlantı" niyetini atomik bayrağa yaz
  RT      : blok işleme başlarken bayrağı oku
  RT      : geçişi bloğun İLK örneğinde uygula (fade veya crossfade)
  RT      : bayrağı temizle
```

| Seçenek | Artısı | Eksişi | Karar |
|---|---|---|---|
| Anlık değişim (0 örnek) | Sade | Click riski | `?` → RO-G3 |
| Kısa fade (blok içi) | Click yok | Gain envanteri | `?` → RO-G3 |
| Çift yol crossfade | En pürüzsüz | 2× iş yükü | `?` → RO-G3 |

Süre değeri **üretilmez** → `UNKNOWN`; sartnamede böyle bir eşik yok.

## §17 Bus topolojisi — derinlik ve çember yasağı

| Kural | Neden | Kaynak |
|---|---|---|
| Bus → bus zinciri serbest mi? | Çember = sonsuz döngü | §8 / T9 |
| Bus derinliği üst sınırı | İş yükü ve gecikme | **UNKNOWN** |
| Bus → gönderim (send) yolları | Çapraz besleme karmaşası | §7 |
| Bus payı (solo/mute) kime ait | routing ↔ mixing sınırı | RO-G2 |

**Çember tespiti** başlatmada yapılmalıdır (graph traversal); RT'de
yapılması yasaktır (kural 1/2). Tespit algoritmasının uygulanıp
uygulanmadığı **UNKNOWN** — repo'da kod yok.

## §18 gain kime ait — sınır analizi (R2)

| Varyant | gain nerede | Sonuç |
|---|---|---|
| A | routing katmanında | Geçişte iki gain çakışması riski |
| B | mixing katmanında | routing saf taşıyıcı olur |
| C | her ikisinde | Ölçekleme hatalarına açık |

Bu dosya **karar vermez** — karar RO-G2 kapısındadır. Seçilmeyen
varyant için kod yazılmaz; iki dosyanın (routing + mixing) aynı anda
idDia etmesi halinde §2'deki komşu-kapsam kuralı devreye girer.

## §19 Bağlantı yaşam döngüsü (şablon)

| Aşamа | İşlem | Taraf |
|---|---|---|
| 1 | Niyet (kaynak/hedef/port) | Non-RT |
| 2 | Doğrulama (kapasite, çember, port var mı) | Non-RT |
| 3 | Rezerv (statik tabloda yer ayırma) | Başlatma/Non-RT |
| 4 | Yayın (atomik bayrak) | Non-RT |
| 5 | Uygulama (blok kenarı) | RT |
| 6 | Geri alma | Non-RT → RT (ters sıra) |

Adım 3'te başarısızlık → bağlantı **kurulmaz**, hata UI'a döner (RT'ye
dokunulmaz). Yaşam döngüsünün gerçek uygulanışı **UNKNOWN**.

## §20 Yanlış yönlendirme — hata modları

| # | Hata | Belirti | Önleme | Kapı |
|---|---|---|---|---|
| 1 | Çember | CPU sürekli, ses yok | Başlatma taraması | RO-G1 |
| 2 | Yanlış port | Sessiz kanal / yanlış hoparlör | Port doğrulama | RO-G5 |
| 3 | Çift besleme | Aşırı gain, faz sorunu | Matris doğrulama | RO-G5 |
| 4 | Kopuk bağlantı | Hiç ses yok | Bağlantı sayacı | RO-G5 |
| 5 | Gölge bağlantı (aynı hedefe iki kaynak) | Beklenmeyen karışım | Matris tek hücre kuralı | RO-G2 |
| 6 | statik üst sınır taşması | Yeni bağlantı sessizce reddedilir | Rezerv sayacı | RO-G6 |

**Test kriteri (R5)** bu tablonun sütunları olarak yazılabilir; ancak
kabul eşiği (ör. "sessiz kanal" tanımı) belirlenmedi → `UNKNOWN`.

## §21 K004 pipeline ↔ routing sırası (R4)

```text
[şablon sıra]
  kaynak → routing (hangi bus?) → pipeline (ne iş?) → çıkış
  veya
  kaynak → pipeline → routing → çıkış
```

| Sıra | Sonuç | Soru |
|---|---|---|
| routing önce | Her bus ayrı işlenir | İş yükü çoğalır mı? |
| pipeline önce | İş tek yerde | Bus'a özel efekt mümkün mü? |

Karar **açık** (RO-G4). Bu dosya iki seçeneği de savunmaz; K004
(diagnostics) ile ortak kapıdadır.

## §22 routing ↔ mixing sınırı (R2 — detay)

| Görev | routing'de mi | mixing'de mi | Not |
|---|---|---|---|
| Kaynak→hedef eşleme | ✅ | — | §6 |
| Seviye (gain) | `?` | `?` | RO-G2 |
| Solo/mute | `?` | `?` | RO-G2 |
| Panorama | ❓ | `?` | RO-G2 |
| Bus toplamı | `?` | `?` | RO-G2 |
| Geçiş fade'i | `?` | `?` | RO-G3 |

**Tek kazanan kural:** bir görev **yalnız bir dosyada** tanımlanır; ikisi
de tanımlarsa çakışma raporlanır ve RO-G2 çözülünceye kadar kod
yazılmaz. Bu, mimari çelişki protokolü (L1 → L2) ile aynı davranıştır.

## §23 Ölçüm kapıları — beklenen kanıt türü

| Kapı | Kapandığında ne görülmeli | Şu anki durum |
|---|---|---|
| RO-G1 | Topoloji kararı + diyagram | ❌ Karar yok |
| RO-G2 | Sınır tablosu (§22) işaretli | ❌ Karar yok |
| RO-G3 | Geçiş zamanı + ölçüm | ❌ Karar yok |
| RO-G4 | Sıra kararı + K004 mutabakatı | ❌ Karar yok |
| RO-G5 | Yanlış yönlendirme testi (§20 sütunları) | ❌ Kriter yok |
| RO-G6 | Link üst sınırı sayısı | ❌ UNKNOWN |

Hiçbir kapı **bu dosyada kapatılamaz** — kapatma kararı +
ölçüm sonuçları ister.

## §24 Bağımlılık matrisi (bu dosyanın komşuları)

| Komşu | İlişki | Ne taşır | Ne TAŞIMAZ |
|---|---|---|---|
| mixing | Sınır | gain/pan kararı (R2) | eşleme |
| output | Akış | routing çıktısı → çıkış | cihaz seçimi |
| realtime | Kısıt | lock-free tablo (K8) | topoloji |
| diagnostics | Sıra (R4) | pipeline sırası | matris |
| synchronization | Bağlantı | clock ≠ rota | — |
| testing | Doğrulama | yanlış yönlendirme testi (R5) | tasarım |

Çakışma halinde §2 kuralı: **bir karar tek dosyada**.

## §25 Risk kaydı (bu dosya özelinde)

| # | Risk | Etki | Azaltma | Durum |
|---|---|---|---|---|
| R1 | Topoloji kararı verilmediği için kodun iki kez yazılması | İsraf | RO-G1'i önce aç | Açık |
| R2 | gain'in iki dosyada tanımlanması | Çift ölçekleme | Tek kazanan kuralı (§22) | Açık |
| R3 | Geçişte click | Ses kalitesi | RO-G3 + blok kenarı (§16) | Açık |
| R4 | Pipeline sırası yanlış tahmin | Yeniden iş | RO-G4 | Açık |
| R5 | Yanlış yönlendirme sessiz kalır | Hata bulunamaz | RO-G5 kriteri | Açık |
| R6 | Link üst sınırı bilinmiyor → taşma | Sessizce bağlantı kaybı | Statik rezerv + sayacı | Açık |

## §26 Sık sorulanlar (kısa)

| Soru | Cevap |
|---|---|
| Matris statik mi? | Karar RO-G1'de; T9 yalnız **üst sınırın** statik olmasını söyler |
| Geçiş anında olur mu? | Tercih blok kenarı (§16); karar RO-G3'te |
| Gain kimde? | Bilinmiyor → RO-G2 |
| Link kaç olabilir? | `UNKNOWN` (R6) |
| Kod yazıldı mı? | Hayır — repo'da `*.cpp`/`*.h` = 0 |

## §27 Dosya kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `routing/routing-kapsam.md` |
| 2 | Bölüm aralığı | §1–§35 (son ek: §13–§35, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** |
| 4 | Açık kapı | RO-G1…RO-G6 |
| 5 | Açık soru | R1…R6 |

## §28 Bus yapısı — iki model karşılaştırması (karar yok)

| Model | Tanım | Artısı | Eksişi |
|---|---|---|---|
| Sabit bus seti | Bus'lar baştan tanımlı | Statik allocate kolay | Yeni bus eklenemez |
| Dinamik bus | Bus çalışma zamanında eklenir | Esnek | RT'de allocate riski |

**Tercih yok** — karar RO-G1'e bağlıdır. Her iki modelde de geçerli
olan tek kısıt: RT yolunda yeni allocate **yok** (kural 1).

## §29 Port eşleme — kanal sırası uyarısı

| Not | Ayrıntı |
|---|---|
| Kanal sırası | Sıra kararı output dosyasındadır (device eşleme) — bu dosya tekrar tanımlamaz |
| Mono→stereo yayılımı | `?` — mixing mi routing mi? → RO-G2 |
| Orta kanal (LFE/C) | 8.1 kapsamı K003 genelinde; burada karar yok |
| Bağlantı başına kanal sayısı | `UNKNOWN` |

## §30 Geçiş testleri (RO-G3/R5 için taslak)

| ID | Test | Adım | Beklenen |
|---|---|---|---|
| RT-CH1 | Bağlantı değişimi + dinleme | Bağlantıyı değiştir, dinle | Click yok |
| RT-CH2 | Hızlıca geri alma | A→B→A | Kararsızlık yok |
| RT-CH3 | Aynı anda iki değişiklik | İki bağlantı birden | Tanımlı sonuç |
| RT-CH4 | Taşan bağlantı | Üst sınırı aş | Sessizce kayıp değil, hata |

**Adımların ölçülü karşılığı yok** (kod yok) → hepsi **BLOCK (PLANNED)**.

## §31 Uygulama sırası önerisi (önceliklendirme)

| Sıra | Ne | Neden |
|---|---|---|
| 1 | RO-G1 topoloji kararı | Diğer kapılar buna bağlı |
| 2 | RO-G2 gain sınırı | mixing ile eşzamanlı |
| 3 | RO-G3 geçiş zamanı | §16 modelinin seçimi |
| 4 | RO-G4 sıra (K004 mutabakatı) | Komşu dosya |
| 5 | RO-G5 test kriteri | §20 sütunlarının ölçümü |
| 6 | RO-G6 üst sınır | En son — ölçümle belirlenir |

Sıra **öneridir**, onaylı plan değildir.

## §32 Dış bağımlılıklar

| Bağımlılık | Tür | Durum |
|---|---|---|
| OS ses yönlendirmesi (WASAPI/CoreAudio/ALSA) | platform | device/output dosyalarında |
| DSP zinciri girdisi | K003 engine | PLANNED |
| UI (bağlantı seçimi) | K8 | UI ≠ RT sınırı |
| Test altyapısı | K005 testing | Kapı açık |

Bu dosyada **yeni bağımlılık tanımlanmaz**.

## §33 Terminoloji (bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| **Matris** | Kaynak×hedef bağlantı tablosu (§6) |
| **Bus** | Birden fazla kaynağın toplandığı yol (§8) |
| **Kenar** | Blok işleme başlangıcı — geçiş anı (§16) |
| **Tek kazanan** | Bir görevin yalnız bir dosyada tanımlanması (§22) |
| **Rezerv** | Statik üst sınırda ayrılmış boş link yeri (§11) |

## §34 Açık soruların kaynağı

R1…R6, §5'te açılmış sorulardır; bu dosya ek soru **açmaz**, yalnız
soruları kapı bağlarına (§12) eşler. Yeni soru çıkarsa §5'ye eklenir
(append), mevcut satır değiştirilmez.

## §35 Boşluk kaydı (dürüstlük)

| Boşluk | Neden | Ne zaman kapanır |
|---|---|---|
| Gerçek matris hücreleri | Tasarım kararı yok | RO-G1 sonrası |
| Gain konumu | İki dosya sınırda | RO-G2 |
| Geçiş süresi | Ölçüm yok | RO-G3 |
| Link üst sınırı | Ölçüm/karar yok | RO-G6 |
| Kod kanıtı | Repo'da `*.cpp`/`*.h` = 0 | İlk uygulama |

## §36 Karar geçmişi (append-only)

| Tarih | Olay | Sonuç |
|---|---|---|
| 2026-10-13 | Dosya derinleştirildi | §13–§36 eklendi |
| 2026-10-13 | Kapı durumu | RO-G1…RO-G6 **hepsi açık** |
| — | Karar kaydı | **Yok** — henüz onaylı topoloji kararı alınmadı |

Geçmiş satırları **değiştirilmez**; yeni karar bu tabloya eklenir.

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
