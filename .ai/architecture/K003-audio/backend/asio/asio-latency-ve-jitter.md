---
title: "ASIO Derin-Dalı — Gecikme ve Jitter (Zincir · Saat · Ölçüm Protokolü)"
node_id: "K003-audio/backend/asio/asio-latency-ve-jitter"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K004-dsp"
evidence:
  - "https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h — ASIOGetLatencies/ASIOGetSamplePosition/ASIOOutputReady yorum metinleri (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio — render/capture/roundtrip/touch-to-sound gecikme terminolojisi ve ses yığını gecikme kalemleri (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getdeviceperiod — GetDevicePeriod: varsayılan/asgari işlem geçişi periyodu, 100 ns birim (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclock-getposition — GetPosition: cihaz konumu + QPC değeri, S_OK/S_FALSE davranışı (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclock-getfrequency — GetFrequency: konum birimini anlamlı kılan frekans (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclock — IAudioClock arayüzü: akış veri hızı ve konum izleme (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter — QPC: <1 µs çözünürlüklü zaman damgası (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/timeapi/nf-timeapi-timebeginperiod — timeBeginPeriod: periyodik zamanlayıcı çözünürlüğü talebi ve Win10 2004 değişikliği (2026-10-12 okundu)"
validation:
  - "URL + tarih + alıntı: §11 kanıt-iddia tablosundaki her satır bu turda açılıp okunmuş bir URL'ye bağlandı (2026-10-12)"
  - "Kod/protokol blokları kaynak URL + tarih etiketi taşır; uydurma `Dosya:`/`Satır:` referansı yoktur (repo'da *.cpp/*.h bulunmaz)"
  - "Sayısal gecikme ölçümü iddia edilmez; yalnız aritmetik dönüşüm ve kaynaktaki semantik yazılır (2026-10-12)"
  - "Satır sayısı ≥500, ön-matter alanları ve URL çakışması otomatik ölçüldü (2026-10-12)"
open_questions:
  - "Neva Engine'in hedef gecikme bütçesi kaç ms? (512 örnek @ 48 kHz = 10,67 ms blok — ürün kararı, ölçüm yok → ⚠️ VERIFICATION REQUIRED)"
  - "ASIO `ASIOOutputReady` destekleniyorsa Neva Engine bir blok kazancı için çağıracak mı? → ADR"
  - "`kAsioLatenciesChanged` geldiğinde gecikme yeniden okunup K003 telemetrisine yazılacak mı? → K003-audio/backend"
  - "Round-trip ölçümü fiziksel loopback ile mi, yazılım döngüsü ile mi yapılacak? → ölçüm altyapısı PLANNED"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Gecikme ve Jitter (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya ASIO'nun **gecikme zincirini** ve **jitter kaynaklarını** tarif eder:
> `ASIOGetLatencies` semantiği (giriş `age` · çıkış blok geçiş-ses-arası), saat
> kaynağı sorusu (`ASIOGetSamplePosition` vs `IAudioClock`), ölçüm birimleri
> (örnek · 100 ns · QPC sayacı) ve CoreMusic için **ölçüm protokolü**. Sayısal
> gecikme ölçümü **yapılmaz**; yalnız kaynakta okunan semantik ve açıkça
> etiketlenmiş aritmetik dönüşüm yazılır.

---

## §0 ELI15

Ses bir **kova hattından** akar: mikrofona düşen damla ile hoparlörden çıkan
damla arasında her kovada biraz bekler. ASIO bu hattı iki ucuyla ölçer —
`ASIOGetLatencies` **girişte** "elimdeki suyun kaç damla eski olduğunu",
**çıkışta** "kova devrildikten kaç damla sonra suyun ses çıkarmaya başlayacağını"
söyler. Hattın uzunluğu bir **blok boyuna** ve sürücünün kovaları taşıma alışına
bağlıdır: kovaları hiç taşımadan doğrudan boşaltan sürücü en hızlısıdır.

**Jitter** ise hattın uzunluğunun **sabit olmaması**dır. Saat her tick'i aynı
uzunlukta atarsa ve her geçiş tam zamanında olursa jitter yoktur; bir tick
kayarsa veya geçiş gecikirse aynı ortalama gecikme bile "hissettirilir". Bu
dosyanın ikinci işi, tick'i (soru 3) ve kaymayı (soru 6) ayırmaktır.

---

## §1 Kimlik kartı

| Alan | Değer |
|---|---|
| Soru | Gecikme nereden oluşur, ne ölçülür, nasıl düşürülür? |
| Sorumlu katman | Host (ASIO C API) + sürücü (donanım/DMA gecikmesi) + işletim sistemi saat altyapısı |
| Sözleşme yeri | `asio.h` (`ASIOGetLatencies`, `ASIOGetSamplePosition`, `ASIOOutputReady` yorumları) |
| WASAPI karşılığı | `IAudioClient::GetStreamLatency` · `IAudioClient::GetDevicePeriod` · `IAudioClock::GetPosition` |
| Kesiştiği düğümler | `asio-buffer-stratejileri.md` (blok boyu) · `asio-wasapi-karsilastirma.md` (karşı tarafta ölçüm) · `K004-dsp` (işleme gecikmesi) |
| Kapsam dışı | Hoparlör/amp fizik gecikmesi · ağ gecikmesi · DSP algoritması grup gecikmesi (`K004-dsp`) |
| Ölçüm birimi | Örnek (frame) · 100 nanosaniye (`REFERENCE_TIME`) · QPC sayacı — **milisaniye tek başına kullanılmaz** |

---

## §2 Gecikme sözlüğü (kaynaktaki dört terim)

Microsoft'un düşük-gecikme dokümanı gecikmeyi **dört ayrı terimle** ayırır;
bunları karıştırmak ölçümü anlamsızlaştırır.

| Terim | Tanım (kaynak metni) | ASIO'daki karşılığı |
|---|---|---|
| **Render latency** | Uygulamanın veriyi render API'lerine verdiği andan hoparlörden duyulduğu ana kadar geçen süre | `outputLatency` + blok işleme süresi |
| **Capture latency** | Ses mikrofona düştüğünden capture API'lerine ulaşana kadar geçen süre | `inputLatency` |
| **Roundtrip latency** | Mikrofon → uygulama → hoparlör tam döngüsü; kabaca render + capture | `inputLatency + outputLatency + işleme` |
| **Touch-to-sound latency** | Ekrana dokunmadan sesin duyulmasına kadar (roundtrip + touch-to-app) | ASIO kapsamı dışı |

Ayrıca kaynaki yığın tanımı üç **kalem** içerir: APO gecikmesi (sinyal işlemine
göre değişir), uygulamanın tampona yazması, ses motorunun tamponu okuyup
işlemesi. Kaynak bu yığında şu sayısal geçmişi verir:

| Dönem | Ses motoru gecikmesi (kaynaktaki ifade) |
|---|---|
| Windows 10 öncesi — kayan nokta | ~12 ms (floating point kullanan uygulamalar için) |
| Windows 10 öncesi — tam sayı | ~6 ms (integer kullanan uygulamalar için) |
| Windows 10 ve sonrası | **1,3 ms** — tüm uygulamalar için |
| Motor tamponu (Windows 10 öncesi) | her zaman ~10 ms'ye ayarlıydı |
| Motor tamponu (Windows 10 ve sonrası) | boyutu **ses sürücüsü belirler** |

> Bu tablo **ASIO ölçümü değildir**; yalnız karşılaştırma tarafının (WASAPI
> paylaşımlı yığın) kendi dokümanındaki ifadedir. ASIO sürücüsü bu motoru
> atlar — bkz. `asio-wasapi-karsilastirma.md`.

---

## §3 `ASIOGetLatencies` — iki ucu iki farklı anlama gelir

ASIO gecikmeyi **tek çağrıda iki sayı** olarak verir ve bu iki sayı **aynı
temsilî birimdedir: örnek (sample frame)**. Kaynak yorumu iki sayıyı ayrı ayrı
tanımlar.

### §3.1 Giriş gecikmesi (`inputLatency`)

| Öğe | Kaynaktaki semantik |
|---|---|
| Ne ifade eder | `bufferSwitch()` içinde host veriyi okurken giriş tamponundaki **ilk örneğin "yaşı"** |
| Kapsam | Cihaza özgü gecikmeler (FIFO vb.) **dahil** |
| Kapsam dışı | Fiziksel geçiş ile `bufferSwitch()` giriş arasındaki **fazla** gecikme (açıkça "theoretical" diyor) |
| Tipik değer | **Bir bloğun boyu + cihaz gecikmesi** |
| Girdi yoksa | `ASE_NotPresent`; yalnız giriş veya yalnız çıkış varsa diğer parametre 0 ve `ASE_OK` |

### §3.2 Çıkış gecikmesi (`outputLatency`)

| Öğe | Kaynaktaki semantik |
|---|---|
| Ne ifade eder | Buffer switch ile **bir sonraki oynatma tamponunun ses çıkarmaya başlaması** arasındaki süre |
| "Bir sonraki oynatma tamponu" | `bufferSwitch()`'ten **sonra** işlenmeye başlanan tampon (`index` 0 = A, 1 = B) |
| Tipik değer 1 | **Bir blok** — host doğrudan DMA tamponuna yazıyorsa |
| Tipik değer 2 | **İki blok veya daha fazla** — tampon sürücü tarafında "latch" ediliyorsa |
| Kopyalama bedeli | Sürücü tamponu kopyalayıp bir sonraki slota veriyorsa çıkış gecikmesi **iki bloğa** çıkar |

### §3.3 Asgari giriş/çıkış gecikmesi — "üç blok" kuralı

Kaynak, kopyalamanın bedelini açıkça yazar: sürücü tamponu kopyalarsa çıkış
gecikmesi iki blok olur ve bu **en az toplam 3 blok** giriş/çıkış gecikmesi
sonucunu verir. Aynı yorum, asgariye ulaşmanın yolunu da tarif eder:

| Koşul | Sonuç |
|---|---|
| Host girişi işleyip doğrudan çıkış tamponuna yazar, çıkış bir sonraki zaman diliminde ses çıkarır | **Asgari** giriş/çıkış gecikmesi |
| Sürücü host tamponunu kopyalar (kaynak: "en sık yapılan ama kaçınılması önerilen" durum) | Çıkış +1 blok, toplam ≥3 blok |
| Bellek erişimi sınırlayıcı olduğu için kopyalama önlenebilir | Gecikme düşer — bu yüzden tamponların **sahibi sürücüdür** |

### §3.4 Zaman çizelgesi — `ASIOStart` örneği

Kaynak, `ASIOStart` anındaki tampon durumunu örnek olarak verir:

```text
Kaynak: github.com/introlab/irl_audio · raw rt_audio_ros/src/RTAudio/asio.h (2026-10-12)
"on ASIOStart(), the host will have filled the play buffer at index 1 already;
 when it gets the callback (with the parameter index == 0), this tells it to read
 from the input buffer 0, and start to fill the play buffer 0 (assuming that now
 play buffer 1 is already sounding). In this case, the output latency is one block."
```

| An | Giriş tamponu | Çıkış tamponu | `index` |
|---|---|---|---|
| `ASIOStart()` | — | 1 zaten dolduruldu | — |
| İlk `bufferSwitch()` | 0 okunur | 0 doldurulmaya başlanır (1 ses çıkarıyor) | 0 |
| Sonraki `bufferSwitch()` | 1 okunur | 1 doldurulmaya başlanır (0 ses çıkarıyor) | 1 |

### §3.5 `ASIOOutputReady` — bir blok kazancı

Kaynağa göre sürücü DMA tamponu kullanıyorsa ve donanım biçimi ASIO biçiminden
farklıysa sürücü **önceki** host tamponunu dönüştürmek zorunda kalır; bu çıkış
gecikmesine **bir blok** ekler. Host `ASIOOutputReady`'yi çağırabilirse sürücü
veriyi doğrudan sıradaki DMA tamponuna dönüştürür ve **bir blok geri kazanılır**.

| Durum | `ASIOOutputReady` dönüşü | Host davranışı |
|---|---|---|
| Mekanizma anlamlı ve gecikme düşürülebilir | `ASE_OK` | Her çıkış işlemi bitiminde çağır |
| Mekanizma yok (çoğu durum) | `ASE_NotPresent` | Çağrıyı bırak |
| Sistem henüz tam hazır değil ama mekanizma anlamlı | `ASE_OK` (kaynak: "her zaman dönmeli") | Devam et |

### §3.6 Blok sayısına göre giriş/çıkış toplamı (aritmetik)

Kaynaktaki semantik (§3.3) tek bir eşitsizliği verir. Bu tablo o eşitsizliğin
**aritmetik açılımıdır**; hiçbir satır ölçülmüş değildir.

| Sürücü davranışı | `outputLatency` (blok) | `inputLatency` (tipik min., blok) | Topam giriş/çıkış (blok) | 48 kHz'te 512 örnekli blokla |
|---|---|---|---|---|
| Doğrudan DMA yazımı (kopya yok) | 1 | 1 | **2** | 21,33 ms |
| Sürücü tamponu kopyalar | 2 | 1 | **3** | 32,00 ms |
| Kopya + `ASIOOutputReady` destekli | 1 (geri kazanılır) | 1 | **2** | 21,33 ms |
| Cihaz FIFO gecikmesi `F` örnek | 1 | 1 + `F` | 2 + `F` | 21,33 ms + `F`/48000×1000 ms |

> Blok cinsinden yazılan sütunlar §3.1–§3.3'teki kaynak semantiğinin
> **doğrudan sonucudur**; son sütun yalnız `ms = blok/48000×1000` dönüşümüdür.

### §3.7 Gecikme okuma sırası (host yaşam döngüsü)

`ASIOGetLatencies` her zaman **anlamlı** çağrılamaz: gecikme blok cinsindendir
(§3.1: `inputLatency` tipik olarak "bir bloğun boyu + cihaz gecikmesi"), bu
yüzde bloklar oluşmadan okunan değer bir sayıdan ibarettir.

| Sıra | Çağrı | Neden bu sırada |
|---|---|---|
| 1 | `ASIOInit` | Sürücü bağlamı kurulmadan kanal/tampon boyu sorulamaz |
| 2 | `ASIOGetChannels` | Kanal sayısı tampon sayısını belirler |
| 3 | `ASIOGetBufferSize` | min/max/preferred/granularity dörtlüsü |
| 4 | `ASIOCreateBuffers` | Tamponlar yokken "bir blok" tanımı geçersizdir |
| 5 | `ASIOGetLatencies` | Blok boyu biliniğine göre gecikme okunabilir |
| 6 | `ASIOStart` | Örnek konumu sıfırlanır (§5) |
| 7 | `bufferSwitch` içinde tekrar oku | `kAsioLatenciesChanged` gelirse (§4) |

> `ASIODisposeBuffers` çağrısının `ASIOStop`'u da içerdiği kaynakta yazılıdır;
> bu nedenle yeniden boyutlandırma öncesi durdurma adımı atlanmaz.

---

## §4 `kAsioLatenciesChanged` — gecikme sabit değildir

ASIO selector listesinde gecikmenin değişebileceğine dair **açık bir mesaj
vardır**: sürücü gecikmelerinin değiştiğini bildirir; motor gecikmeleri **yeniden
okur**.

| Selector | Kaynaktaki anlamı | Host'un tepkisi |
|---|---|---|
| `kAsioLatenciesChanged` | "Sürücünün gecikmeleri değişti. Motor gecikmeleri yeniden alacak." | `ASIOGetLatencies`'i tekrar çağır |
| `kAsioResyncRequest` | Sürücü senkronunu kaybetti; zaman damgası artık geçerli değil — motorun yeniden başlatılması isteği | Yeniden başlatma akışı |
| `kAsioBufferSizeChange` | "Henüz desteklenmiyor, şu an her zaman 0L döner; şimdilik `kAsioResetRequest` kullanın" | Sıfır say |
| `kAsioOverload` | Sürücü bir taşma (xrun) tespit etti | Sayaç artır |

**Mimari sonuç:** gecikme bir kez okunup önbelleğe alınmış bir sabit değil,
**yeniden okunabilir bir gözlem**dir. Neva Engine tarafında bu, ölçüm
telemetrisinin tetikleyicisi olabilir (bkz. §12 açık soru 3).

---

## §5 Saat kimdir: `ASIOGetSamplePosition` sözleşmesi

Ölçüm için önce **ne zaman** sorusunun cevabı gerekir. ASIO bunu
`ASIOGetSamplePosition(sPos, tStamp)` ile verir: örnek konumu + o konumun
kilitlendiği sistem zamanı çifti.

| Kural | Kaynaktaki ifade |
|---|---|
| Başlangıç | Örnek konumu `ASIOStart()` çağrıldığında **sıfıra sıfırlanır** |
| Eşleştirme | Konum/zaman çifti **geçerli bloğa** referans vermelidir |
| İlk callback | `ASIOStart()`'ten sonraki **ilk** `bufferSwitch()`'te konum **sıfır** olmalı, zaman damgası akışın başladığı an olmalı |
| Hizalama | Örnek konumu **blok hizalı** olmalıdır |
| Uç durum | Saat yoksa `ASE_SPNotAdvancing` döner |
| Sürücü sorumluluğu | Sistem zamanı blok konumu için belirlenemiyorsa sürücü **uygun interpolasyonu** yapmalıdır |
| Ağırlık | "ASIO_GetSamplePosition() ve ASIO_GetLatencies() davranışının uygunluğu **hassas medya senkronizasyonu için hayati önemdedir**" |

Ayrıca zaman bilgisi modu `bufferSwitchTimeInfo` üzerinden müzakere edilir:
host `kAsioSupportsTimeInfo`'ya true dönerse `bufferSwitch` yerine
`bufferSwitchTimeInfo`'nun çağrılmasını bekler.

---

## §6 Karşı taraftaki saat: `IAudioClock` üçlüsü

ASIO tek bir çift verirken WASAPI tarafı **üç ayrı sorgu** sunar; bunları
bilmek, iki API'nin gecikme raporunu karşılaştırırken farkı açıklar.

| Arayüz/Method | Ne verir | Birim | Öne çıkan not |
|---|---|---|---|
| `IAudioClock::GetPosition` | Cihaz konumu (`pu64Position`) + okuma anındaki performans sayacı (`pu64QPCPosition`) | Konum birimi **tanımsız** — yalnız `GetFrequency` ile birlikte anlamlı | `pu64QPCPosition` 100 ns birimine çevrilerek yazılır; NULL olabilir |
| `IAudioClock::GetPosition` dönüş kodu | Tam okuma → `S_OK`; çağrı çok uzun sürüp doğruluğu düşürürse → **`S_FALSE`** | — | `S_FALSE` "başarısız" değil, "okuma yavaşladı" anlamıdır |
| `IAudioClock::GetFrequency` | Konum birimini anlamlı kılan frekans | konum/birim | Konum tek başına okunursa sayı değersizdir |
| `IAudioClient::GetDevicePeriod` | Ses motorunun art arda işlem geçişleri arasındaki periyot: **varsayılan** ve **asgari** | `REFERENCE_TIME` = **100 nanosaniye** | `Initialize`'dan **önce veya sonra** çağrılabilir |
| `IAudioClockAdjustment::SetSampleRate` | Akışın örnek hızını değiştirir | çerçeve/sn | **Gerçek zamanlı iş parçacığından çağrılmamalı**; paylaşımlı modda geçerlidir |

`GetPosition`'un `pu64QPCPosition` alanı, **ASIO'daki `tStamp`'in karşılığıdır** —
ikisi de "bu konum hangi anda kilitlendi" sorusunu yanıtlar. Fark: ASIO bunu
tek çağrıda verir, WASAPI ayrı bir performans sayacı döndürür.

### §6.1 İki API'nin saat/gölge tablosu

| Soru | ASIO | WASAPI |
|---|---|---|
| Konum birimi | Örnek (frame) | Tanımsız — `GetFrequency` gerekli |
| Konum + zaman birlikte mi? | Evet (`ASIOGetSamplePosition(sPos, tStamp)`) | Evet ama ayrı alanlar (`pu64Position`, `pu64QPCPosition`) |
| Başlangıç | `ASIOStart()`'te sıfır | Akışın başlangıcı |
| Hizalama zorunluluğu | **Blok hizalı** olmalı | Kaynak bunu şart koşmaz |
| Yavaş okuma | `ASE_SPNotAdvancing` (saat yoksa) | `S_FALSE` (okuma uzun sürdüyse) |
| Periyot sorgusu | Yok ayrı çağrı; blok boyu `ASIOGetBufferSize`'den gelir | `GetDevicePeriod` (varsayılan + asgari, 100 ns) |
| Örnek hız değiştirme | `ASIOCanSampleRate` (desteklenmiyorsa `ASE_NoClock`) + `ASIOSetSampleRate` (0 ise harici senkron açılır; harici saat açıkken 0 dışı oran → `ASE_InvalidMode`) | `IAudioClockAdjustment::SetSampleRate` (paylaşımlı mod zorunlu; gerçek zamanlı iş parçacığından **yasak**) |
| Gecikme değişikliği bildirimi | `kAsioLatenciesChanged` selector | `AUDCLNT_E_DEVICE_INVALIDATED` vb. hata yolu (bkz. `asio-hot-swap-ve-device-loss.md`) |

---

## §7 Ölçüm araçları: QPC ve `timeBeginPeriod`

### §7.1 `QueryPerformanceCounter`

| Özellik | Kaynaktaki değer |
|---|---|
| Ne yapar | Performans sayacının geçerli değerini alır |
| Çözünürlük | **< 1 µs** (yüksek çözünürlüklü) |
| Dönüş | Başarıda sıfırdan farklı; hatada 0 (geçerli parametrelerle Windows XP ve sonrası **her zaman başarılı**) |
| Başlık / kütüphane | `profileapi.h` (`Windows.h` ile) · `Kernel32.lib` / `Kernel32.dll` |
| Asgari istemci | Windows 2000 Professional |

### §7.2 `timeBeginPeriod`

| Özellik | Kaynaktaki değer |
|---|---|
| Ne yapar | Periyodik zamanlayıcılar için **asgari çözünürlük** talep eder |
| Parametre | `uPeriod` — milisaniye; **daha düşük değer = daha yüksek çözünürlük** |
| Dönüş | `TIMERR_NOERROR` / `TIMERR_NOCANDO` |
| Zorunluluk | `timeEndPeriod` ile **aynı** değerde eşleştirilmelidir |
| Küresel davranış (Windows 10 2004 öncesi) | İşlev **küresel** bir Windows ayarını etkilerdi; sistem tüm süreçler için **en düşük (en yüksek çözünürlüklü)** değeri kullanırdı |
| Küresel davranış (Windows 10 2004 ve sonrası) | Artık **küresel etkisi yoktur**; bu işlevi çağıran süreçler için düşük değer kullanılır, çağırmayanlar için varsayılanın üstü garanti edilmez |

**Jitter bağlantısı:** zamanlayıcı çözünürlüğü, `sleep` tabanlı beklemelerin
adımını belirler. Windows 10 2004 sonrası davranış değişikliği, **aynı kodun**
farklı Windows sürümlerinde farklı jitter üretebileceği anlamına gelir — bu
nedenle ölçüm protokolü (§9) sabit sürümlerde ve açıkça raporlanan ayarlarla
yapılmalıdır.

### §7.3 Zaman kaynağı seçimi (karşılaştırma)

Ölçüm katmanı üç farklı zaman kaynağı kullanabilir; hepsi **farklı bir soruyu**
yanıtlar ve birbirinin yerine konamaz.

| Kaynak | Ne ölçer | Çözünürlük | Birim | ASIO'da var mı? | Kullanım yeri |
|---|---|---|---|---|---|
| `QueryPerformanceCounter` | Duvar saati değil, **sayaç** | <1 µs | sayac | Hayır (host kodu çağırır) | İki olay arasındaki süre |
| `ASIOGetSamplePosition` → `tStamp` | Akış içi olay zamanı | Sürücüye bağlı | sistem zamanı | **Evet** | "Bu blok hangi andaydı" |
| `IAudioClock::GetPosition` → `pu64QPCPosition` | Akış içi olay zamanı | Sürücüye bağlı | 100 ns | Hayır (WASAPI) | Karşılaştırma ölçümü |
| `timeBeginPeriod` | Zamanlayıcı **adımı** | milisaniye | ms | Hayır | Sleep tabanlı beklemeler |
| `GetDevicePeriod` | Motor geçiş periyodu | 100 ns | `REFERENCE_TIME` | Hayır | WASAPI blok döngüsü |

**Kural:** iki olay arasındaki süreyi ölçmek için **aynı sayacı** iki kez
okumak gerekir. Farklı kaynaklardan gelen iki zaman damgası karşılaştırılırsa
sonuç jitter değil, **kaynak hatası** olur.

---

## §8 Jitter kaynakları tablosu (sınıflandırma)

Jitter tek bir şey değil; **hangi katmanın** kaydığını bilmek düzeltmeyi
belirler. Aşağıdaki sınıflandırma kaynak semantiğinden türetilmiştir; her satır
**ölçülmemiş** bir gözlem adayıdır.

| # | Kaynak | Sınıf | Belirti | ASIO tarafındaki karşılığı | Düşürme kolu |
|---|---|---|---|---|---|
| 1 | Sürücü kopyalama yaparsa | Protokol | Çıkış gecikmesi +1 blok, dalgalanma artar | `ASIOGetLatencies` yorumu ("kaçınılması önerilen") | Kopyasız DMA yazımı |
| 2 | Saat interpolasyonu | Sürücü | Zaman damgası blok konumuna tam oturmaz | `ASIOGetSamplePosition` notu (sürücü interpolasyon yapar) | Sürücü kalitesi — host kontrolü yok |
| 3 | Blok hizasız konum | Protokol | Senkron kayması, `kAsioResyncRequest` | "sample position must be block aligned" | Blok boyunu sabitle |
| 4 | Zamanlayıcı çözünürlüğü | OS | Bekleme adımı değişir (`timeBeginPeriod`) | ASIO dışı, host ölçüm katmanı | Ölçümde QPC kullan |
| 5 | CPU/çekirdek yoğunlaşması | OS | Geçiş anında gecikme | `bufferSwitch` zaman aşımı | Çekirdek sabitleme (bkz. §10) |
| 6 | `GetPosition` yavaş okuması | OS API | `S_FALSE` dönüşü | WASAPI tarafı | Konum + QPC çiftini birlikte oku |
| 7 | Sürücü taşması (xrun) | Sürücü | Bir blok tam işlenmez | `kAsioOverload` | Sayaç + blok boyu ayarı |
| 8 | Gecikmenin değişmesi | Sürücü | Önbellekteki değer bayatlar | `kAsioLatenciesChanged` | Yeniden okuma |

---

## §9 Ölçüm protokolü (CoreMusic — dört aşama)

> **Uyarı:** Bu protokol bir **tasarımdır**, sonuç değildir. Hiçbir aşama bu
> dosyada çalıştırılmamıştır; sonuç alanları `UNKNOWN` olarak işaretlidir.

| Aşama | Ne ölçülür | Birim | Araç | Kaynak dayanağı | Sonuç |
|---|---|---|---|---|---|
| 1 — Bildirilen gecikme | `ASIOGetLatencies` giriş/çıkış | örnek | ASIO SDK | §3 | UNKNOWN (ölçülmedi) |
| 2 — Saat kararlılığı | Ardışık `ASIOGetSamplePosition` farkının blok boyuna oturması | örnek | ASIO SDK | §5 | UNKNOWN |
| 3 — OS sayacı | QPC farkının beklenen periyotla uyumu | sayac / µs | `QueryPerformanceCounter` | §7.1 | UNKNOWN |
| 4 — Round-trip | Fiziksel döngü (loopback) ile giriş→çıkış süresi | ms | Dış donanım | §2 roundtrip tanımı | UNKNOWN |

### §9.1 Aritmetik dönüşüm tablosu (ölçüm değil, hesap)

Dönüşüm: `ms = örnek / (örnekleme hızı) × 1000`.

| Örnek (blok) | 48 kHz | 44,1 kHz |
|---|---|---|
| 64 | 1,33 ms | 1,45 ms |
| 128 | 2,67 ms | 2,90 ms |
| 256 | 5,33 ms | 5,80 ms |
| 512 | **10,67 ms** | 11,61 ms |
| 1024 | 21,33 ms | 23,22 ms |

| `REFERENCE_TIME` (100 ns) | ms |
|---|---|
| 10 000 | 1,00 |
| 100 000 | 10,00 |
| 1 000 000 | 100,00 |

### §9.2 CoreMusic gecikme bütçesi (512 örnek @ 48 kHz)

> `sartname` (olcum) durumu **PLANNED** — ölçüm yapılmamıştır; aşağıdaki
> dağılım bir **hedef bütçe**tir, bir ölçüm sonucu değil.

| Kalem | Birim | Değer | Dayanak |
|---|---|---|---|
| ASIO blok periyodu | ms | 10,67 | §9.1 aritmetik (512 @ 48 kHz) |
| `inputLatency` (bir blok + cihaz) | örnek | ≥ 512 + cihaz | §3.1 kaynak semantiği |
| `outputLatency` (kopyasız) | örnek | ≥ 512 | §3.2 |
| `outputLatency` (kopyalı) | örnek | ≥ 1024 | §3.3 |
| DSP zinciri grup gecikmesi | ms | UNKNOWN | `K004-dsp` — ölçüm yok |
| Toplam round-trip | ms | UNKNOWN | §9 aşama 4 çalıştırılmadı |

### §9.3 Ölçüm kayıt şablonu (boş — doldurulmadı)

Aşağıdaki tablo **boş bir formdur**; hiçbir hücresi doldurulmamıştır. Ölçüm
başladığında satırlar dolacak, dolmayan hücreler `UNKNOWN` olarak kalacaktır.

| Alan | Birim | Değer | Ölçüm tarihi | Sürücü | OS sürümü |
|---|---|---|---|---|---|
| `ASIOGetLatencies` → `inputLatency` | örnek | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| `ASIOGetLatencies` → `outputLatency` | örnek | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| Blok boyu | örnek | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| Örnek hız | Hz | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| `ASIOGetSamplePosition` farkı (min/maks) | örnek | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| QPC farkı (min/maks) | sayac | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| `kAsioLatenciesChanged` sayacı | adet | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| `kAsioOverload` (xrun) sayacı | adet | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |
| Round-trip (loopback) | ms | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |

**Kayıt kuralı:** ölçüm birimi her satırda açıkça yazılır; örnek ve milisaniye
aynı sütunda birleştirilmez. Sürücü adı ve OS sürümü **zorunlu** alandır —
jitter sonuçları bu iki bilgi olmadan tekrarlanamaz.

---

## §10 Karar tablosu (Neva Engine için öneri — başlangıç düzeyi değil)

| Karar | Öneri | Dayanak | Durum |
|---|---|---|---|
| Gecikme birimi | Örnek; ms yalnız sunumda | `ASIOGetLatencies` parametreleri örnek cinsindendir | Başlangıç (protokol) |
| Gecikme okuması | Tek okuma değil, `kAsioLatenciesChanged` ile yenileme | §4 selector | Başlangıç (protokol) |
| Kopyalama | Kopyasız yazım hedeflenir | §3.3 kaynak ("kaçınılması önerilen") | Başlangıç (protokol) |
| `ASIOOutputReady` | Destekleniyorsa çağır | §3.5 | Öneri → ADR |
| Ölçüm saati | QPC (`QueryPerformanceCounter`) | §7.1 (<1 µs) | Öneri |
| `timeBeginPeriod` | Ölçüm kodunda, eşleştirilmiş çağrı | §7.2 | Öneri |
| Toplam gecikme hedefi | UNKNOWN | Ölçüm yok | ⚠️ VERIFICATION REQUIRED |

---

## §11 İddia — kanıt tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| `ASIOGetLatencies` giriş ve çıkış gecikmesini cihaza özgü gecikmelerle (FIFO vb.) birlikte döndürür | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `inputLatency` "ilk örneğin yaşı"dır, tipik olarak bir blok + cihaz gecikmesi | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `outputLatency` buffer switch ile bir sonraki oynatma tamponunun ses çıkarması arasındaki süredir; doğrudan DMA yazımında bir blok | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Sürücü tamponu kopyalarsa çıkış gecikmesi iki bloğa çıkar ve toplam giriş/çıkış gecikmesi en az 3 blok olur | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `ASIOOutputReady` true dönerse sürücü doğrudan sıradaki DMA tamponuna dönüştürür ve çıkış gecikmesi bir blok azalır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `ASIOGetSamplePosition` örnek konumu `ASIOStart()`'te sıfırlanır ve blok hizalı olmalıdır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Render/capture/roundtrip/touch-to-sound dört ayrı gecikme terimidir; roundtrip kabaca render + capture'dır | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | OK |
| Windows 10 öncesi ses motoru gecikmesi ~12 ms (float) / ~6 ms (int), Windows 10+ 1,3 ms'tir; motor tamponu Win10+ sürücü tarafından belirlenir | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | OK |
| `GetDevicePeriod` varsayılan ve asgari işlem geçişi periyotunu 100 nanosaniye biriminde döndürür | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getdeviceperiod | 2026-10-12 | OK |
| `GetPosition` hem cihaz konumunu hem de okuma anındaki performans sayacını verir; yavaş okumada `S_FALSE` döner | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclock-getposition | 2026-10-12 | OK |
| Konum değeri birimi tanımsızdır; yalnız `GetFrequency` ile birlikte anlamlıdır | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclock-getfrequency | 2026-10-12 | OK |
| `IAudioClock` bir akışın veri hızını ve akış içindeki geçerli konumu izlemek içindir | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclock | 2026-10-12 | OK |
| `QueryPerformanceCounter` <1 µs çözünürlüklü, `profileapi.h` içinde, `Kernel32.dll` ile gelir | https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter | 2026-10-12 | OK |
| `timeBeginPeriod` periyodik zamanlayıcı için asgari çözünürlük talep eder ve `timeEndPeriod` ile eşleştirilmelidir | https://learn.microsoft.com/en-us/windows/win32/api/timeapi/nf-timeapi-timebeginperiod | 2026-10-12 | OK |
| Windows 10 sürüm 2004'ten itibaren `timeBeginPeriod` küresel zamanlayıcı çözünürlüğünü artık etkilemez | https://learn.microsoft.com/en-us/windows/win32/api/timeapi/nf-timeapi-timebeginperiod | 2026-10-12 | OK |
| CoreMusic için ölçülmüş toplam round-trip gecikme | (ölçüm yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| Neva Engine hedef gecikme bütçesi (ms) | (ürün kararı yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

---

## §12 Açık sorular

1. **Hedef bütçe:** 512 örnek (@48 kHz = 10,67 ms blok) kabul edilebilir mi,
   yoksa ürün 128/256 örnek mi zorlar? → **üst karar:** ürün.
2. **`ASIOOutputReady`:** destekleyen sürücüde çağırıp bir blok kazanacak mıyız?
   Kazanç yalnız çıkış tarafındadır; giriş tarafı için eşdeğer mekanizma yok → ADR.
3. **`kAsioLatenciesChanged`:** değiştiğinde gecikme yeniden okunup K003
   telemetrisine yazılacak mı? → `K003-audio/backend`.
4. **Round-trip ölçümü:** fiziksel loopback (donanım kablo) mı, yoksa
   yazılım döngüsü mü? Fiziksel ölçüm donanım gerektirir → ölçüm altyapısı PLANNED.
5. **Zamanlayıcı ayarı:** ölçüm süreci `timeBeginPeriod` çağıracak mı; çağırırsa
   `timeEndPeriod` eşleşmesi ve Win10 2004 sonrası süreç-bazlı davranış nasıl
   raporlanacak? → ölçüm protokolü.

---

## §13 İlgili vault düğümleri

| Düğüm | İlişki |
|---|---|
| `K003-audio/backend/asio/asio-buffer-stratejileri.md` | Blok boyu bu dosyadaki "blok" teriminin kaynağı |
| `K003-audio/backend/asio/asio-arayuzler.md` | `ASIOGetLatencies` / `ASIOGetSamplePosition` imzaları |
| `K003-audio/backend/asio/asio-wasapi-karsilastirma.md` | Karşı tarafta gecikme ve periyot ölçümü |
| `K003-audio/backend/asio/asio-cpp-ornekleri.md` | Gecikme okuma kod örnekleri |
| `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` | `IAudioClock` / `GetDevicePeriod` sahibi katman |
| `K004-dsp` | İşleme zinciri gecikmesi (grup gecikmesi) bu bütçenin kalemlerinden |

---

## §14 Doğrulama protokolü (bu dosya için)

1. Her iddia §11 tablosunda bir URL'ye bağlanır; açılmayan iddia yazılmaz.
2. Protokol blokları `Kaynak: <url> (<tarih>)` etiketlidir; `Dosya:` / `Satır:`
   biçiminde **uydurma repo referansı yoktur** (repo'da `*.cpp`/`*.h` bulunmaz).
3. Ölçülmüş sayısal gecikme iddiası **yapılmaz**; §9 tablolarındaki ms değerleri
   yalnız aritmetik dönüşümdür ve açıkça "ölçüm değil" diye etiketlenmiştir.
4. Boş-başlık yer tutucuları ve "ileri tarihte yapılacak" ifadeleri kullanılmaz;
   bilinmeyen durum `UNKNOWN` veya `⚠️ VERIFICATION REQUIRED` olarak yazılır.
5. Kaynak yazım korunur; alıntılarda yazım bozuklukları düzeltilmez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** introlab/irl_audio asio.h (ASIOGetLatencies · ASIOGetSamplePosition · ASIOOutputReady ·
kAsioLatenciesChanged) · learn.microsoft low-latency-audio · IAudioClient::GetDevicePeriod ·
IAudioClock::GetPosition / GetFrequency / IAudioClock · QueryPerformanceCounter · timeBeginPeriod ·
2026-10-12
