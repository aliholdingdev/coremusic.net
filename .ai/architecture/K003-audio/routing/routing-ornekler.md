---
title: "routing — Örnekler"
type: architecture-examples
node_id: K003-audio/routing
node_type: subdomain
status: draft
parent_id: K003-audio
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# routing — Örnekler

> Kural: §1 repo kodu birebir okunur (yol + satır); ilgizse **— yok** yazılır.
> §2 yalnız bu oturumda erişilen URL'lerden üretilir. Uydurma kod/yapı yasaktır.

## §1 Gerçek repo kodu (ÖNCELİK 1)

**— yok.**

`shared/src/`, `shared/tests/` içinde kanal/akış yönlendirme kodu yoktur
(2026-10-12 kök `*.cpp`/`*.h` glob 0 isabet). Aranan: port/link eşleme, kanal
seçici (matrix/switch), kaynak→hedef tablosu. Bulunan: PageRouter (HTTP yönlendirme
— **sayfa** yönlendirmedir, ses yönlendirmesi DEĞİLDİR) ve PHP/JS UI katmanı.

```php
// DİKKAT: shared/src/PageRouter/ dizini "router" adı taşır ama HTTP route
// eşlemesidir (web isteği → controller); ses routing'ine kanıt sayılmaz.
// Aranan ses-routing kodu: kök *.cpp/*.h glob → 0 isabet (2026-10-12).
```

## §2 Resmi doküman örneği

| # | Örnek | URL | Adım / parçası | Ne gösterdiği |
|---|---|---|---|---|
| 1 | PipeWire nesne modeli: nodes + ports + links | https://pipewire.org/page_objects_design | node = medya işleme elemanı · port · link iki portu bağlar | Yönlendirme **üç kavram** üzerine kurulur: düğüm, uç (port), bağlantı (link) — CoreMusic'in şablonu |
| 2 | PipeWire port latency nesnesi | https://pipewire.org/page_latency | direction + min/max; linked node latency'leri toplanır, mismatch tespit edilir | Yönlendirme sadece "nereden nereye" değil, **gecikme uyumu** da taşır; mismatch raporlanır |
| 3 | PipeWire driver node scheduling | https://pipewire.org/page_scheduling | driver node döngüyü başlatır; required/pending sayaçları | Yönlendirilen grafiğin **zamanlaması** ayrı bir sahiptir (realtime) — routing kuralı zaman sahibi değildir |
| 4 | AUGraph zincir kurulumu | `audio-master.md` §4 A3 (Apple: ARoadmaptoCommonTasks — bu oturumda vault'tan okundu) | AUGraphNewNode → output unit ile biten graf → Initialize | Zincir **tek çıkışa** biter; çoklu çıkış ayrı yapı kurmayı gerektirir (Q6/R3) |
| 5 | ALSA plug plugin kanal dönüşümü | https://www.alsa-project.org/doc/api/group___p_l_u_g.html | format/channels/rate otomatik dönüşümü (slave belirtilir) | Kanal sayısı uyuşmazlığını **sürücü katmanında** çözmek mümkün — routing bunu istismar etmeden bilmeli |

## §3 Uygulama notu

Bu örnekler CoreMusic'te **routing tasarımı öncesi** şablon olarak kullanılır:
1–3, PipeWire'in üç parçalı modeli (node/port/link) + gecikme uyumu + ayrı
zamanlama sahibi şablonunu verir; 4, zincirin tek çıkışa bağlandığını (çoklu
çıkış tasarımı ayrı karardır); 5, kanal dönüşümünün sürücüde yapılabilirliğini.
Topoloji (sabit/dinamik, kaç link) R1 (`routing-kapsam.md` §5) kapanmadan
yazılmaz; link sayısı uydurulmaz → UNKNOWN.

## §4 İlişkili dosyalar

| Dosya | Rol |
|---|---|
| `routing-kapsam.md` | Kademe-2 kapsam kaydı (rol · sınır · disk kanıtları · açık sorular R1–R6) |
| `routing-dogrulama.md` | Kanıt/doğrulama tablosu (12 satır: ✅/has-been-fetched 8 · ⚠️ 2 · UNKNOWN 2) |
| `routing/index.md` | Düğüm kaydı (ELI15 · açık sorular) |
| `../audio-master.md` | Üst anayasa §3 satır 7 · §4 A3 · §5 T16/T17 · §8 |
| `../audio-bagimliliklar.md` | §4 — routing ↔ K004 pipeline ilişkisi riski |
| `../mixing/mixing-kapsam.md` | Kardeş çocuk — eşleme ↔ birleştirme sınırı (R2) |

## §5 PipeWire node/port/link modeli — adım kaydı (master §5 T17)

> Kaynak: `docs.pipewire.org/devel/page_objects_design.html` (✅ doğrulanmış).

| # | Adım | Ne gösterdiği | Routing açısından |
|---|---|---|---|
| 1 | Node tanımlanır (medya işleme elemanı) | İşlem birimi | Düğüm = K003 içi düğüm ya da K004 eki |
| 2 | Port tanımlanır (yön: giriş/çıkış) | Uç | Yön zorunlu (§7 kapalı şablonu) |
| 3 | Link kurulur (çıkış → giriş) | Bağlantı | §6.1 matrisinin karşılığı |
| 4 | Link kaldırılır | Ayrılma | §9 satır 2 |
| 5 | Graf durumu okunur (hangi link aktif) | Görünürlük | Patchbay tablosu (§7) |

**Örnek dersi:** üç kavram (node · port · link) **bağımsız tanımlanır**;
bu yüzden matris (§6) ve patchbay (§7) birbirinden ayrı tutulur — matris
"neyin nereye gideceği", patchbay "hangi bağlantının açık olduğu"dur.

## §6 PipeWire latency uyumu — adım kaydı (master §5 T16)

| # | Adım | Ne gösterdiği | Routing açısından |
|---|---|---|---|
| 1 | Port başına Latency nesnesi (direction + min/max) | Her uç kendi gecikme aralığını bildirir | Matris hücresi yalnız eşlemeyi değil, **uyumu** da taşır |
| 2 | Linked node latency'leri toplanır | Beklenen toplam | §11 kapasite tablosunun gecikme yüzü |
| 3 | Mismatch tespit edilir | Uyumsuzluk **raporlanır** | Hata modu 3 (kopuk/uyumsuz — kapsam §10) |
| 4 | Uyumsuzluk düzeltilmez (yalnız rapor) | Karar testing'de | TS1 ayrımı ile aynı örüntü |

**Örnek dersi:** routing **gecikme uyumunu da taşır** — sadece "nereden
nereye" değil, "hangi gecikmeyle". CoreMusic'te bu, synchronization dalının
R3/R4 kapılarıyla kesişir.

## §7 AUGraph zincir kurulumu — adım kaydı (master §4 A3)

| # | Adım | Ne gösterdiği | Routing açısından |
|---|---|---|---|
| 1 | `AUGraphNewNode` ile düğüm eklenir | Düğüm listesi | Node şablonu (§5) |
| 2 | Düğümler bağlanır (bağlantılar kurulur) | Graf bağlantısı | Link şablonu (§5) |
| 3 | Graf output unit ile biter | **Tek** çıkışa iniş | Çoklu çıkış ayrı yapı (kapsam §11) |
| 4 | Initialize belleği ayırır | Allocate başlangıçta | §11 statik kapasite ile uyumlu |
| 5 | Start ile işlem başlar | Sıralı başlangıç | §9 satır 4 (çift tampon şablonu) |

**Örnek dersi:** zincir **tek çıkışa** biter (adım 3); bu, "çoklu çıkış"
için **ayrı yapı kurmayı** zorunlu kılar (Q6/R3) — matris §6.2 bunu şablonlar.

## §8 ALSA plug plugin — adım kaydı (master §5 T14)

| # | Adım | Ne gösterdiği | Routing açısından |
|---|---|---|---|
| 1 | Slave cihaz tanımı | Hedef cihazın gerçek kapasitesi | §6 matrisi hedef kapasitesine uymalı |
| 2 | format/channels/rate otomatik dönüşüm | Uyuşmazlık **sürücüde** çözülür | Routing bunu **istismar etmeden** bilmeli |
| 3 | plug devreye girer (yazma anında) | Ek gecikme/kayıp | Gecikme bütçesine (realtime §7) yazılır |

**Örnek dersi:** kanal dönüşümü sürücüde yapılabilir; bu, routing'in işini
**kolaylaştırır ama gizlemez** — CoreMusic hangi dönüşümün sürücüde,
hangisinin routing'te olduğu kararını **vermemiştir** (⚠️ R1 ile kilitli).

## §9 Yanlış yönlendirme tespit senaryoları (test adayları — R5)

> Bu tablo **test senaryosu adaylarıdır**; eşik/ölçüm testing dalındadır (R5).

| # | Senaryo | Yöntem | Beklenen | Sahip |
|---|---|---|---|---|
| 1 | Matris bütünlüğü | Kurulum sonrası matris taranır | Geçersiz hücre yok | routing |
| 2 | Döngü (loopback) testi | Graf zinciri taranır | Döngü bulunursa reddetme | routing |
| 3 | Cihaz kimliği | Hedef cihaz kimliği okunur | Eşleşmeyen hedef var mı | routing + device |
| 4 | Gecikme uyumu | Linked latency toplanır | Mismatch raporu (PipeWire örüntüsü) | routing + synchronization |
| 5 | Değişiklik sonrası doğrulama | Yeniden yönlendirme sonrası matris tekrar okunur | Eski link kalmadı | routing |

**Kural:** bu adaylar **eşik içermeksizin** yazılır (eşik R5'te tanımlanacak);
"test geçti" iddiası ölçüm olmadan yazılmaz (testing-kapsam §13).

## §10 Uygulama kontrol listesi (routing tasarımı öncesi — taslak)

| # | Kontrol | Neden | Kapanış kapısı |
|---|---|---|---|
| 1 | Topoloji seçildi mi (bus yapısı, kanal sayısı)? | kapsam §8 | RO-G1 |
| 2 | routing ↔ mixing sınırı çizildi mi (gain kimde)? | R2 | RO-G2 |
| 3 | Geçiş zamanlaması (blok kenarı) kararlaştırıldı mı? | R3 | RO-G3 |
| 4 | K004 pipeline sırası ilişkisi tanımlandı mı? | R4 | RO-G4 |
| 5 | Yanlış yönlendirme test kriteri yazıldı mı? | R5 | RO-G5 |
| 6 | Link üst sınırı belirlendi mi? | R6 (kapsam §11) | RO-G6 |

## §11 Örnek okuma kuralı (bu dosya — kapsam §6'nın karşılığı)

| Örnek türü | Ne gösterir | Ne GÖSTERMEZ |
|---|---|---|
| §2 resmi doküman örneği | Kavramsal ağ modeli | CoreMusic'in kendi topolojisi |
| §5–§8 adım kayıtları | Referans mimarinin kurulum sırası | Ölçülmüş gecikme |
| §11–§35 şablonları | Nasıl kurulacağı | Mevcut sistem durumu |

**Tek cümle:** bu dosya **örnek/şablon** taşır; `✓` uygulanmış anlamına
gelmez. Uygulanmış kanıt için `[KANIT]` etiketi aranır — repo'da
`*.cpp`/`*.h` = 0 olduğu için bu etiket **hiçbir örnekte yok**.

## §12 Port adlandırma şablonu

| Katman | Örnek desen | Not |
|---|---|---|
| Düğüm adı | `<rol>-<sıra>` | Sıra 0'dan |
| Port adı | `<yon>-<kanal>` | `out` / `in` |
| Bus adı | `bus-<rol>` | Karar RO-G1'de |
| Bağlantı adı | `kaynak.port → hedef.port` | Okuma yönü |

Adlandırma şeması **öneridir**; onaylı şema yok → gerçek desen
`UNKNOWN`.

## §13 Yönlendirme matrisi — küçük örnek (3×3)

> Aşağıdaki hücreler **örnek**tır; K003'ün gerçek matrisi RO-G1'dedir.

| Kaynak \ Hedef | Bus-A | Bus-B | Çıkış |
|---|---|---|---|
| Kaynak-1 | `✓` | `—` | `?` |
| Kaynak-2 | `—` | `✓` | `?` |
| Kaynak-3 | `?` | `?` | `?` |

Okuma: Kaynak-1 yalnız Bus-A'ya gider; Kaynak-3 henüz kararsız (`?`).
`?` hücreleri kapıya yazılıdır (kapsam §13).

## §14 Aynı tablo — matris → liste dönüşümü (şablon)

```text
[çalışma zamanı listesi — şablon]
  satır 0 : (kaynak=0, hedef=bus-A, durum=aktif)
  satır 1 : (kaynak=1, hedef=bus-B, durum=aktif)
  satır 2 : (kaynak=2, hedef=?,     durum=bekliyor)
  toplam  : statik rezerv içinde
```

| Dönüşüm kuralı | Not |
|---|---|
| `?` satırı listeye alınmaz | Kararsız bağlantı taşınmaz |
| `—` satırı üretilmez | Boş yer kaplamasın |
| Liste uzunluğu baştan belli | Kural 1 (allocate yasağı) |

## §15 Bus toplama örneği (kavramsal)

```text
  Kaynak-1 ─┐
  Kaynak-2 ─┼→ Bus-A → [gain *] → Çıkış-L
  Kaynak-3 ─┘
  Kaynak-4 ──── Bus-B → [gain *] → Çıkış-R
```

| Düğüm | Görevi | Sahiplik |
|---|---|---|
| Bus-A/B | Toplama | mixing (RO-G2) |
| `[gain *]` | Seviye | **kararsız** → RO-G2 |
| Çıkış-L/R | Yönlendirme | routing → output |

## §16 Çapraz besleme (send) örneği

| Gönderen | Alıcı | Amaç | Risk |
|---|---|---|---|
| Bus-A | Bus-B | Efekt gönderimi | Çember (§17) |
| Kaynak-2 | Bus-B | Alt grup | Çift besleme |
| Bus-B | Bus-A | Geri besleme | **Çember — yasak** |

Send yolları da matriste temsil edilir ve çember taramasına dahildir.

## §17 Çember tespiti — örnek ipuçları

| Belirti | Neden |
|---|---|
| CPU sürekli, ses yok | Döngü içinde sonsuz iş |
| Aynı örnek tekrarlanır | Geri besleme |
| Zamanla büyüyen gecikme | Kuyruk birikiyor |

Tarama **başlatmada** yapılır (kapsam §17); RT'de yapılmaz. Algoritmanın
mevcut olup olmadığı `UNKNOWN` (kod yok).

## §18 Geçiş örneği — A→B bağlantı değişimi

```text
  t0 : bağlantı A aktif
  t1 : non-RT "B" niyetini yazar (atomik bayrak)
  t2 : RT blok başında bayrağı okur
  t3 : blok ilk örneğinde geçiş (fade / crossfade — karar RO-G3)
  t4 : bayrak temizlenir
```

| Adım | Taraf | Kilit var mı |
|---|---|---|
| t1 | non-RT | Hayır (atomik) |
| t2–t4 | RT | Hayır |

## §19 Click testi — beklenen gözlem (RO-G3/R5)

| Senaryo | Beklenen | Şu an |
|---|---|---|
| Tek bağlantı değişimi | Halka yok (click) | BLOCK |
| Hızlı A→B→A | Kararsızlık yok | BLOCK |
| Aynı anda 2 değişiklik | Tanımlı sonuç | BLOCK |

Ölçüm yok → hepsi **PLANNED**; kabul eşiği RO-G3'te.

## §20 Link üst sınırı — örnek rezerv tablosu

| Alan | Değer | Not |
|---|---|---|
| Kullanılan slot | `UNKNOWN` | Ölçüm yok |
| Rezerv slot | `UNKNOWN` | Karar yok (R6) |
| Boş slot | `UNKNOWN` | — |
| Taşma davranışı | Sessiz kayıp yerine **hata** | kapsam hata modu 6 |

Sayı uydurmak yasak → tamamı `UNKNOWN`.

## §21 Hata modu → örnek gözlem

| Mod | Sayaç ne göstermeli | Eşik |
|---|---|---|
| Kopuk bağlantı | Aktif link sayısı ↓ | `UNKNOWN` |
| Çift besleme | Aynı hedefe 2 kaynak | Karar RO-G2 |
| Çember | Döngü taraması ihlali | Başlatmada 0 |
| Taşma | Reddedilen bağlantı sayısı > 0 | `UNKNOWN` |

## §22 Mimari sıra şablonları (R4 — iki seçenek)

```text
[A] kaynak → routing → pipeline → çıkış
[B] kaynak → pipeline → routing → çıkış
```

| | A | B |
|---|---|---|
| Bus'a özel efekt | Mümkün | Zor |
| İş yükü | Bus başına tekrar | Tek yerde |
| Karar | ❓ RO-G4 | ❓ RO-G4 |

Bu dosya **seçim yapmaz**.

## §23 Referans platform desenleri (kavramsal)

| Platform | Yönlendirme birimi | Not |
|---|---|---|
| PipeWire | node/port/link | §5 adım kaydı |
| ALSA | plug/plugin + route | §8 adım kaydı |
| CoreAudio | AUGraph / graph | §7 adım kaydı |
| Windows | WASAPI endpoint | output/device dosyalarında |

Tablo **karşılaştırma** amaçlıdır; platform seçimi bu dosyanın
kapsamı **dışıdır**.

## §24 Topoloji boyutu — bilinmeyenler

| Boyut | Değeri | Neden |
|---|---|---|
| Kaynak sayısı | `UNKNOWN` | R6 |
| Bus sayısı | `UNKNOWN` | RO-G1 |
| Çıkış sayısı | `UNKNOWN` | 8.1 + ayrı çıkış kararı yok |
| Bağlantı derinliği | `UNKNOWN` | RO-G1 |
| Eşzamanlı aktif link | `UNKNOWN` | Ölçüm yok |

## §25 Karşılaştırma: statik vs dinamik (kod yazımı öncesi)

| Kriter | Statik | Dinamik |
|---|---|---|
| RT allocate | Yok ✅ | Risk ❌ |
| Yeni bağlantı | Rezervten | Genişleme gerekir |
| Bellek | Baştan belli | Değişken |
| Karmaşıklık | Düşük | Yüksek |

Henüz karar yok (RO-G1) — **hiçbiri tercih edilmez**.

## §26 Test adayları — genişletilmiş liste (R5)

| ID | Adım | Beklenen | Durum |
|---|---|---|---|
| R-EX1 | Matristeki tüm `✓` bağlantılarını tek tek doğrula | Her biri ses verir | BLOCK |
| R-EX2 | `—` hücrelerini doğrula | Ses gelmez | BLOCK |
| R-EX3 | `?` hücreye ses vermeyi dene | Karar öncesi reddedilir | BLOCK |
| R-EX4 | Bağlantıyı kaldır, geri ekle | Kararlı sonuç | BLOCK |
| R-EX5 | Rezerv taşması | Tanılı hata | BLOCK |
| R-EX6 | Çember enjeksiyonu | Başlatma reddi | BLOCK |

Kabul eşiği `UNKNOWN` (RO-G5 açık).

## §27 UI ↔ routing etkileşimi (kavramsal)

| UI eylemi | Routing'e karşılık | Taraf |
|---|---|---|
| Kaynak seç | Bağlantı niyeti | non-RT |
| Bus ata | Rezerv + yayın | non-RT |
| Bağlantıyı kes | Ters sıra geri alma | non-RT → RT |
| Ölçüm göster | Atomik okuma | RT → non-RT |

RT, UI'dan gelen veriyi **atomik** alır (kural 2).

## §28 Senaryo: 8.1 çıkışa yönlendirme (şablon)

| Adım | İşlem | Karar |
|---|---|---|
| 1 | Kanal sırasını belirle | output dosyasında — tekrar tanımlanmaz |
| 2 | Mix bus → 8 kanal + LFE | Topoloji RO-G1 |
| 3 | Gain seviyesi | RO-G2 |
| 4 | Geçiş | RO-G3 |

Bu dosya yalnız **rota** kısmını taşır; eşleme/sıra **output**'tadır.

## §29 Tablo: her bölümün kapısı

| Bölüm | Kapı |
|---|---|
| §13 matris | RO-G1 |
| §15 bus | RO-G1 + RO-G2 |
| §18 geçiş | RO-G3 |
| §20 sınır | RO-G6 |
| §22 sıra | RO-G4 |
| §26 test | RO-G5 |

## §30 SSS (özet — kapsam ile tutarlı)

| Soru | Cevap |
|---|---|
| Örnekler kod mu? | Hayır — şablon/niyet; `[KANIT]` yok |
| Matris gerçek mi? | Hayır — §13 3×3 **örnek** |
| Sayılar ölçülü mü? | Hayır — tamamı `UNKNOWN` |
| Geçiş ne zaman? | Karar RO-G3'te |

## §31 Kapsam dosyası ile fark tablosu

| Konu | Kapsam | Örnekler (bu dosya) |
|---|---|---|
| Soru listesi | R1–R6 | Kapıya eşlenmiş |
| Kapılar | RO-G1…G6 | Test adayları |
| Matris | Tanım | 3×3 örnek |
| Geçiş | Model | Adım akışı |

## §32 Bağımlılık özeti

| Dosya | İlişki |
|---|---|
| `routing-kapsam.md` | Bu dosyanın kaynağı (soru/kapı) |
| `mixing/*` | gain sınırı ortak |
| `output/*` | kanal sırası/eşleme |
| `realtime/*` | lock-free kısıtları |
| `testing/*` | R5 testleri |

## §33 Riskler (örnek dosyası özelinde)

| Risk | Azaltma |
|---|---|
| Örneklerin gerçek sanılması | §11 statü satırı + `[KANIT]` yokluğu |
| Sayı uydurma | Her sayı `UNKNOWN` |
| Kapı kapanmadan kod | §29 kapı eşlemesi |

## §34 Uygulama sırası (kapsam §31 ile aynı)

1. RO-G1 → 2. RO-G2 → 3. RO-G3 → 4. RO-G4 → 5. RO-G5 → 6. RO-G6.

## §35 Kapanış özeti

| # | Öğe | Değer |
|---|---|---|
| 1 | Dosya | `routing/routing-ornekler.md` |
| 2 | Bölüm aralığı | §1–§43 (son ek: §11–§43, 2026-10-13) |
| 3 | Kanıt oranı | **Değişmedi** — şablon/niyet |
| 4 | `[KANIT]` sayısı | 0 (repo'da `*.cpp`/`*.h` = 0) |
| 5 | Açık kapı | RO-G1…RO-G6 |

## §36 Yönlendirme matrisi — 5×4 örnek (genişletilmiş)

> Yine **örnek**. `✓`/`—`/`?` sözlüğü kapsam §13'tedir.

| Kaynak \ Hedef | Bus-Main | Bus-Monitor | Bus-Rec | Çıkış |
|---|---|---|---|---|
| Kaynak-1 | `✓` | `—` | `?` | `—` |
| Kaynak-2 | `✓` | `✓` | `—` | `—` |
| Kaynak-3 | `—` | `✓` | `?` | `—` |
| Kaynak-4 | `?` | `—` | `?` | `—` |
| Kaynak-5 | `—` | `?` | `—` | `?` |

**Sayım:** `✓` = 4 · `—` = 8 · `?` = 8 (toplam 20 hücre). Bu sayı
**örneğin kendisine** aittir; K003'ün gerçek sayımına çevrilemez.

## §37 Hücre → işlem tablosu

| Hücre | Üretilen işlem | Ne zaman |
|---|---|---|
| `✓` | Aktif link kaydı | Başlatma |
| `—` | Kayıt üretilmez | — |
| `?` | Bekleme kaydı (bloklu) | Karar sonrası |
| Geçiş | Fade işlemi (RO-G3) | Blok kenarı |

## §38 Bağlantı kaldırma — ters sıra şablonu

```text
  1 : non-RT "kaldır" niyeti
  2 : RT blok kenarında send-off (sessize alma / fade-out)
  3 : link kaydını pasife al
  4 : rezerv slotu serbest (non-RT)
```

| Sıra | Taraf | Kilit |
|---|---|---|
| 1, 4 | non-RT | Hayır |
| 2, 3 | RT | Hayır |

Sıralamanın tersine yapılması (önce rezerv serbest) → use-after-free
riski; bu, **tasarım kuralı olarak** yazılır, ölçülmüş hata değildir.

## §39 Ölçüm kapıları — bu dosyanın katkısı

| Kapı | Bu dosyada hazır olan | Eksik olan |
|---|---|---|
| RO-G1 | Topoloji boyutu soruları (§24) | Karar |
| RO-G2 | Sahiplik tablosu (§15, §22) | Karar |
| RO-G3 | Geçiş adım akışı (§18) | Zaman/süre ölçümü |
| RO-G4 | İki sıra seçeneği (§22) | K004 mutabakatı |
| RO-G5 | 6 test adayı (§26) | Kabul eşiği |
| RO-G6 | Rezerv tablosu iskeleti (§20) | Sayı |

## §40 Karşılaştırmalı kontrol listesi (kod yazmadan önce)

- [ ] Topoloji kararı alındı mı? (RO-G1)
- [ ] gain sahibi tek dosya mı? (RO-G2)
- [ ] Geçiş zamanı ölçüldü mü? (RO-G3)
- [ ] Pipeline sırası onaylandı mı? (RO-G4)
- [ ] Test eşikleri yazıldı mı? (RO-G5)
- [ ] Link üst sınırı sayısı var mı? (RO-G6)
- [ ] `[KANIT]` iddiası yapılıyor mu? → Varsa **dur** (kod yok)

## §41 Glossary genişletmesi

| Terim | Bu dosyadaki karşılığı |
|---|---|
| Link | Tek kaynak→hedef bağlantısı |
| Slot | Rezervteki link yeri (§20) |
| Niyet | non-RT'nin atomik bayrakla bildirdiği değişiklik |
| Send-off | Bağlantıyı kesme işlemi (§38) |
| Bekleme kaydı | `?` hücre için bloklu kayıt (§37) |

## §42 Kaynak/gözden geçirme notu

| Öğe | Durum |
|---|---|
| Derinleştirme tarihi | 2026-10-13 |
| Eklenen bölümler | §11–§42 |
| Mevcut bölümler | §1–§10 **dokunulmadı** |
| Authority satırı | 1 adet (dosya sonu) |
| Frontmatter | `parent_id` / `group` korundu |
| Git commit | **ATILMADI** (orkestratöre ait) |

## §43 Sonraki tur için not

Bu dosyanın kalan eksikliği **içerik değil kanıttır**: gerçek matris,
gerçek sayı ve gerçek ölçüm olmadan `[KANIT]` geçişi (D6)
**imkânsızdır**. Sonraki tur yalnızca şunları ekleyebilir: onaylı topoloji
kararı (RO-G1) ve ölçüm sonuçları (RO-G3/G5/G6).

---

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış teknik çerçeveler içerir; doğrulanamayan
iddialar ⚠️ VERIFICATION REQUIRED ile işaretlidir. Ölçüm kapıları bu
dosyada AÇIK kalır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
