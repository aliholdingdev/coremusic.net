---
title: "ASIO Derin-Dalı — Çok Kanal ve Zaman Damgası (Kanal Grupları · ASIOTime · Senkron)"
node_id: "K003-audio/backend/asio/asio-multichannel-ve-timestamp"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi"
evidence:
  - "https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h — ASIOChannelInfo/ASIOBufferInfo/AsioTimeInfo/ASIOTime tanımları ve channelGroup sözleşmesi (2026-10-12 okundu)"
  - "https://forums.steinberg.net/t/asio-sdk-hostsample-dsd-mode-problem/97092 — hostsample.cpp tam kaynak kodu (DriverInfo, kMaxInputChannels/kMaxOutputChannels=32, ASIOGetSamplePosition, channelGroup kullanımı) ve ASE_InvalidParameter hata senaryosu (2026-10-12 okundu)"
  - "https://dspace.tul.cz/server/api/core/bitstreams/39cee442-acf3-4ba4-8a9f-7b9988a8abe4/content — ASIO 2.3 spesifikasyon PDF'i, 326.940 bayt, %PDF-1.3 (2026-10-12 indirildi)"
  - "https://forums.steinberg.net/c/developer/asio/104 — resmî ASIO geliştirici forumu başlık listesi (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/stream-management — IAudioClient ile akış açma sırası ve Initialize/öncesi-sonrası metod grupları (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-roles — eConsole/eCommunications/eMultimedia rolleri, rol başına tek cihaz kuralı (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getmixformat — paylaşımlı mod iç işleme mix formatı, CoTaskMemFree sorumluluğu (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-isformatsupported — uç noktanın desteklediği format sorgusu (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immdevice — IMMDevice edinme yolları ve GetId/Activate/GetState metodları (2026-10-12 okundu)"
validation:
  - "URL + tarih + alıntı: §10 kanıt-iddia tablosundaki her satır bu turda açılıp okunmuş bir URL'ye bağlandı (2026-10-12)"
  - "Kod blokları kaynak URL + tarih etiketi taşır; uydurma `Dosya:`/`Satır:` referansı yoktur (repo'da *.cpp/*.h bulunmaz)"
  - "8.1 surround için kanal sayısı iddiası yazılmadı; yalnız kaynaktaki 32/32 örnek sınırı aktarıldı (2026-10-12)"
  - "Satır sayısı ≥500, ön-matter alanları ve URL çakışması otomatik ölçüldü (2026-10-12)"
open_questions:
  - "Neva Engine 8.1 kanal haritası (LFE dâhil) hangi ASIO kanal indekslerine karşılık geliyor? → K003-audio/backend"
  - "`channelGroup` değerleri seçili sürücüde gerçek gruplar mı, hepsi 0 mı? → sürücüye bağlı ölçüm PLANNED"
  - "Zaman bilgisi modu (`kAsioSupportsTimeInfo`) Neva Engine'de zorunlu mu? → ADR"
  - "WASAPI tarafında kanal ayrımı mix format ile mi, `IsFormatSupported` ile mi doğrulanacak? → `asio-wasapi-karsilastirma.md`"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Çok Kanal ve Zaman Damgası (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya ASIO'nun **kanal modelini** (sayı · grup · örnek türü) ve **zaman
> damgası modelini** (`ASIOTime` · `AsioTimeInfo` · bayraklar · senkron kuralları)
> tarif eder. Karşılaştırma tarafında WASAPI'in kanal/format/rol kavramları
> (`GetMixFormat`, `IsFormatSupported`, cihaz rolleri) verilir. Sayısal kanal
> haritası ve sürücüye özgü grup değerleri **ölçülmemiştir** → `UNKNOWN`.

---

## §0 ELI15

Bir stüdyo konsolunu düşün: her soket bir **kanal**, soketler gruplara ayrılmış
( analoglar bir yerde, dijital girişler başka yerde ). ASIO'da da aynı şey var —
`ASIOGetChannels` kaç soket olduğunu söyler, `ASIOGetChannelInfo` ise her soketin
**hangi grupta** olduğunu ve örneğin "Analog In" mi "SPDIF Out" mı olduğunu
söyler. Grup ile soket numarası **birbirinden bağımsızdır**: soketler 0'dan
sayılır, gruplar ayrıca adlandırılır.

**Zaman damgası** ise şudur: sürücü "şu blok" dediğinde, o bloğun **hangi
zamana** ait olduğunu da söylemek zorundadır. ASIO bunu `ASIOTime` yapısında
toplar: sistem zamanı + örnek konumu + örnek hızı + (varsa) timecode. Bu
dosyanın ikinci yarısı, bu paketin alanlarını ve "ne zaman geçerli" kurallarını
anlatır.

---

## §1 Kimlik kartı

| Alan | Değer |
|---|---|
| Soru | Kanallar nasıl sayılır, gruplanır, zaman damgasıyla eşleştirilir? |
| Sorumlu katman | Host (ASIO C API) + sürücü (kanal fiziksel haritası) |
| Sözleşme yeri | `asio.h` (`ASIOGetChannels`, `ASIOGetChannelInfo`, `ASIOBufferInfo`, `AsioTimeInfo`, `ASIOTime`) |
| WASAPI karşılığı | `IAudioClient::GetMixFormat` / `IsFormatSupported` · `IMMDevice` · `ERole` |
| Kesiştiği düğümler | `asio-latency-ve-jitter.md` (zaman damgası semantiği) · `asio-arayuzler.md` (imzalar) · `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` |
| Kapsam dışı | Kanal eşleme/arayüz tasarımı (`design/` PLANNED) · DSP kanal iş parçacığı (`K004-dsp`) |
| Ölçüm birimi | Kanal indeksi (0'dan) · grup indeksi (0'dan) · örnek · nanosaniye |

---

## §2 Kanal sayımı: `ASIOGetChannels`

Tek çağrı iki sayı döndürür ve **giriş ile çıkış ayrı sayılır**.

| Parametre | Anlamı | Yoksa |
|---|---|---|
| `numInputChannels` | Kullanılabilir giriş kanalı sayısı | `ASE_NotPresent` |
| `numOutputChannels` | Kullanılabilir çıkış kanalı sayısı | `ASE_NotPresent` |
| Yalnız giriş veya yalnız çıkış varsa | Diğeri 0 · **`ASE_OK`** döner | — |

Örnek kod (resmî SDK örneğinden, forumda paylaşılmış hâliyle):

```cpp
// Kaynak: forums.steinberg.net/t/asio-sdk-hostsample-dsd-mode-problem/97092 (2026-10-12)
enum {
    // number of input and outputs supported by the host application
    // you can change these to higher or lower values
    kMaxInputChannels = 32, kMaxOutputChannels = 32
};
// internal data storage
typedef struct DriverInfo {
    // ASIOInit()
    ASIODriverInfo driverInfo;
    // ASIOGetChannels()
    long inputChannels;
    long outputChannels;
    // ASIOGetBufferSize()
    long minSize, maxSize, preferredSize, granularity;
    // ASIOGetSampleRate()
    ASIOSampleRate sampleRate;
    // ASIOOutputReady()
    bool postOutput;
    // ASIOGetLatencies ()
    long inputLatency;
    long outputLatency;
    // ASIOCreateBuffers ()
    long inputBuffers; // becomes number of actual created input buffers
} DriverInfo;
```

**Önemli ayrım:** `kMaxInputChannels = 32` **host'un** desteklemek istediği
üst sınırdır; sürücünün gerçekten verdiği `inputChannels` bundan **küçük veya
eşit** olabilir. İkisini karıştırmak tampon dizisi taşmasına yol açar.

---

## §3 Kanal türü: `ASIOChannelInfo`

Her kanal için ayrı ayrı sorgulanan yapı; hem **girdi** hem **çıktı** kanalı
aynı yapıyla okunur.

| Alan | Yön | Anlamı |
|---|---|---|
| `channel` | giriş | Kanal indeksi (0'dan) |
| `isInput` | giriş | `ASIOTrue` → giriş kanalı, değilse çıkış |
| `isActive` | **çıkış** | Kanal `ASIOCreateBuffers()` ile kurulduğundan beri etkin mi |
| `channelGroup` | **çıkış** | Kanalın ait olduğu grup |
| `type` | **çıkış** | Kanalın örnek türü (`ASIOSampleType`) |
| `name[32]` | **çıkış** | Kullanıcıya gösterilecek ad |

### §3.1 `channelGroup` sözleşmesi (kaynaktaki yedi madde)

| # | Kural |
|---|---|
| 1 | Farklı kanal türlerini destekleyen sürücülerde (analog, S/PDIF, AES/EBU, ADAT vb.) **makul bir gruplama** olmalıdır |
| 2 | Gruplar her zaman **kanal indeksinden bağımsızdır** |
| 3 | Kanal indeksi her zaman 0'dan `numInputs`/`numOutputs`'a kadar sayılır, **hangi grupta olursa olsun** |
| 4 | Her zaman **en az bir grup** vardır (grup 0) |
| 5 | Varsayılan olarak host kanal **0 ve 1'i** etkinleştirmeye karar verebilir; bu yüzden bu kanallar **en kullanışlı tip** (varsa analog I/O) ait olmalıdır |
| 6 | `type` kanalın örnek türünü döndürür |
| 7 | `name` portu tanımlamak içindir; ilk karakterler portun doğasını **en anlamlı şekilde** taşımalıdır — arayüz 8 karakterden küçük gösterse bile tanınabilsin |

### §3.2 `isActive` zamanlaması

`isActive` değeri `ASIOCreateBuffers()` **sonrasında** anlamlıdır: kanal ancak
o çağrıyla aktifleşir. Bu, `ASIOGetChannelInfo`'nun tampon kurulumundan önce
çağrılmasında "etkin kanal" sorusunu boşa çıkarır.

| Çağrı sırası | `isActive` güvenilirliği |
|---|---|
| `ASIOCreateBuffers` öncesi | Kanal henüz atanmadı → etkinlik yok |
| `ASIOCreateBuffers` sonrası | **Güvenilir** |
| `ASIODisposeBuffers` sonrası | Sıfırlanır (kaynak: bu çağrı `ASIOStop` içerir) |

---

## §4 Tampon ↔ kanal eşleşmesi: `ASIOBufferInfo`

Tamponlar kanal indeksiyle değil, **kanal başına iki bellek penceresiyle**
tanımlanır.

| Alan | Yön | Anlamı |
|---|---|---|
| `isInput` | giriş | `ASIOTrue` → giriş tamponu, değilse çıkış |
| `channelNum` | giriş | Kanal indeksi (0'dan) |
| `buffers[2]` | **çıkış** | Kanalın çift tamponunun adresleri |

| Kural | Kaynak |
|---|---|
| `numChannels`, oluşturulacak **tüm** giriş ve çıkış kanallarının toplamıdır | `ASIOCreateBuffers` yorumu |
| `bufferSize`, `ASIOGetBufferSize`'den alınan değerlerden **biri** olmalıdır | `ASIOCreateBuffers` yorumu |
| Desteklenmeyen `bufferSize` veya geçersiz `bufferInfos` ayarı → **`ASE_InvalidMode`** | `ASIOCreateBuffers` yorumu |
| Yeterli bellek yoksa → `ASE_NoMemory` | `ASIOCreateBuffers` yorumu |
| Kanal başına tampon boyu hem `ASIOSampleType`'a hem `bufferSize`'ye bağlıdır | `ASIOCreateBuffers` yorumu |
| Ayrı kanal seçimi isteniyorsa ama mümkün değilse sürücü bunu yönetmelidir; `bufferSwitch()` yalnız etkin çıkışların tamponlarını doldurur | `ASIOCreateBuffers` yorumu |

**Bellek hesabı:** `toplam bayt = numChannels × bufferSize × 2 (çift tampon) ×
bayt/kanal`. Bayt/kanal `type`'dan gelir (ör. 16 bit → 2, 24 bit → 3, 32 bit → 4).
Bu çarpım **hafızada önceden ayrılmalıdır**; ses iş parçacığında tahsis yasaktır
(kural: sıfır tahsis).

---

## §5 Zaman damgası yapısı: `AsioTimeInfo` ve `ASIOTime`

### §5.1 `AsioTimeInfo` alanları

| Alan | Tip | Anlamı |
|---|---|---|
| `speed` | `double` | Mutlak hız (1. = nominal) |
| `systemTime` | `ASIOTimeStamp` | `samplePosition`'a karşılık gelen **sistem zamanı, nanosaniye** cinsinden |
| `samplePosition` | `ASIOSamples` | Örnek konumu |
| `sampleRate` | `ASIOSampleRate` | Geçerli oran |
| `flags` | `unsigned long` | Aşağıdaki bayraklar |
| `reserved[12]` | `char` | Rezerve |

Kaynak, `systemTime`'ın platforma özgü türetilme kuralını da yazar:

| Platform | Kaynaktaki kural |
|---|---|
| Windows | `timeGetTime()`'dan türetilmelidir |
| macOS | `Microseconds()`'tan türetilmelidir — **`UpTime()` ile DEĞİL** |

### §5.2 `AsioTimeInfoFlags`

| Bayrak | Değer | Kural |
|---|---|---|
| `kSystemTimeValid` | 1 | **Her zaman geçerli olmalıdır** |
| `kSamplePositionValid` | 1 << 1 | **Her zaman geçerli olmalıdır** |
| `kSampleRateValid` | 1 << 2 | Örnek hız geçerliyse |
| `kSpeedValid` | 1 << 3 | Hız geçerliyse |
| `kSampleRateChanged` | 1 << 4 | Örnek hız değiştiyse |
| `kClockSourceChanged` | 1 << 5 | Saat kaynağı değiştiyse |

### §5.3 `ASIOTime`

```text
Kaynak: github.com/introlab/irl_audio · raw rt_audio_ros/src/RTAudio/asio.h (2026-10-12)
typedef struct ASIOTime                          // both input/output
{
    long reserved[4];                       // must be 0
    struct AsioTimeInfo     timeInfo;       // required
    struct ASIOTimeCode     timeCode;       // optional, evaluated if (timeCode.flags & kTcValid)
} ASIOTime;
```

| Alan | Zorunluluk | Not |
|---|---|---|
| `reserved[4]` | **0 olmalıdır** | Doldurulmamış alan |
| `timeInfo` | **Zorunlu** | Her zaman doldurulur |
| `timeCode` | İsteğe bağlı | Yalnız `timeCode.flags & kTcValid` ise değerlendirilir |

### §5.4 `ASIOTimeCode` ve bayrakları

| Alan | Anlamı |
|---|---|
| `speed` | Hız ilişkisi (nominal hısa oran) — isteğe bağlı, desteklenmiyorsa 0. veya 1. |
| `timeCodeSamples` | Zaman kodu, örnek cinsinden |
| `flags` | `ASIOTimeCodeFlags` |

| `ASIOTimeCodeFlags` bayrağı | Değer |
|---|---|
| `kTcValid` | 1 |
| `kTcRunning` | 1 << 1 |
| `kTcReverse` | 1 << 2 |
| `kTcOnspeed` | 1 << 3 |
| `kTcStill` | 1 << 4 |
| `kTcSpeedValid` | 1 << 8 |

---

## §6 Zaman bilgisi modu (time info mode) — müzakere ve kullanım

### §6.1 Neden bu mod var?

Kaynak, bu modun **timecode desteklemeyen cihazlarda bile** önerildiğini yazar
ve üç gerekçe verir:

| # | Gerekçe (kaynaktaki ifade) |
|---|---|
| 1 | `ASIOGetSamplePosition` ve `ASIOGetSampleRate` çağrılarının **sürekli tekrarı önlenir** |
| 2 | Callback zamanı ile zaman bilgisi arasında **daha tanımlı bir ilişki** vardır |
| 3 | Timecode gereksiz olsa bile kullanılır |

### §6.2 Müzakere sırası

| Adım | Çağrı | Sonuç |
|---|---|---|
| 1 | `ASIOCreateBuffers` içinde `callbacks` işaretçisi alınır | Host artık `asioMessage`'ı çağırabilir |
| 2 | `asioMessage` · `kAsioSupportsTimeInfo` seçicisi | **1** dönerse host bu modu kabul etti |
| 3 | Host artık `bufferSwitch` **yerine** `bufferSwitchTimeInfo` bekler | Eski callback kullanılmaz |
| 4 | `ASIOTime` yapısı callback dönene kadar **geçerli ve erişilebilir** kabul edilir | Sürücü yapıyı serbest bırakmaz |
| 5 | (İsteğe bağlı) `asioMessage` · `kAsioSupportsTimeCode` seçicisi | 1 ise timecode alanı doldurulabilir |
| 6 | Host `kAsioEnableTimeCodeRead` ile `ASIOFuture` çağırarak okumayı açar/kapar | Timecode okuma denetimi |

> `kAsioSupportsTimeInfo` ve `kAsioSupportsTimeCode` seçicilerinin ayrıca
> `ASIOFuture` içinde de desteklenmesi kaynakta **zorunlu tutulur**.

### §6.3 Bayrak doldurma örneği (kaynaktan)

```cpp
// Kaynak: github.com/introlab/irl_audio · raw rt_audio_ros/src/RTAudio/asio.h (2026-10-12)
if(timeInfoMode)
{
    AsioTimeInfo* ti = &asioTime.timeInfo;
    ti->flags =   kSystemTimeValid | kSamplePositionValid | kSampleRateValid;
    ti->systemTime = theNanoSeconds;
    ti->samplePosition = theSamplePosition;
    if(ti->sampleRate != theSampleRate)
        ti->flags |= kSampleRateChanged;
    ti->sampleRate = theSampleRate;

#if kCanTimeCode
    if(canTimeCode && timeCodeEnabled)
    {
        ASIOTimeCode* tc = &asioTime.timeCode;
        tc->timeCodeSamples = tcSamples;                    // tc in samples
        tc->flags = kTcValid | kTcRunning | kTcOnspeed;     // if so...
    }
    ASIOTime* bb = callbacks->bufferSwitchTimeInfo(&asioTime, doubleBufferIndex, processNow ? ASIOTrue : ASIOFalse);
#else
    callbacks->bufferSwitchTimeInfo(&asioTime, doubleBufferIndex, processNow ? ASIOTrue : ASIOFalse);
#endif
}
```

**Bayrak mantığı:** üç zorunlu bayrak **her seferinde yeniden yazılır**
(doğrudan `=` ile atama), değişiklik bayrakları ise **koşulla eklenir**.
Bayrakların temizlenmemesi (OR ile eklemek yerine doğrudan atamamak) bayrak
kirliliğine yol açar — bu yüzden örnekte önce `=` ile sıfırlanır.

### §6.4 Zaman kodu ↔ örnek konumu ilişkisi

Kaynak, buffer switch anında iki sayacın ilişkisini **giriş ve çıkış için ayrı
ayrı** tanımlar:

| Yön | İlişki |
|---|---|
| Giriş | örnek 0 → giriş tamponu örneği 0 → zaman kodu 0 |
| Çıkış | örnek 0 → çıkış tamponu örneği 0 → zaman kodu 0 |

Ardından kritik uyarıyı ekler: **"gerçek hesaplamalar için ilgili gecikmelerin
dikkate alınması gerekir."** — yani ham zaman kodu ile gerçek ses arasında
`ASIOGetLatencies` kadar fark vardır (bkz. `asio-latency-ve-jitter.md` §3).

---

## §7 Çok kanallı çalışma için mimari kurallar (CoreMusic)

> Aşağıdaki satırlar kaynak semantiğinden **türetilmiş tasarım kurallarıdır**;
> hiçbiri ölçülmüş değildir.

| # | Kural | Dayanak | Durum |
|---|---|---|---|
| 1 | Tampon dizisi `kMaxInputChannels + kMaxOutputChannels` boyutunda **önceden** ayrılmalı | §2 örnek sınırı | Başlangıç (protokol) |
| 2 | Gerçek kanal sayısı sürücüden okunmalı, üst sınır kullanılmamalı | §2 ayrım | Başlangıç (protokol) |
| 3 | Kanal başına bayt genişliği `ASIOChannelInfo.type`'tan gelmeli | §3 `type` alanı | Başlangıç (protokol) |
| 4 | Kanal adları `name[32]`'den okunup ilk 8 karakteri özet gösterilmeli | §3.1 madde 7 | Öneri |
| 5 | 8.1 haritası `channelGroup`'a göre değil, **kanal indeksine** kurulmalı | §3.1 madde 2–3 | Başlangıç (protokol) |
| 6 | Zaman bilgisi modu müzakere edilmeden `bufferSwitchTimeInfo` beklenmemeli | §6.2 adım 2 | Başlangıç (protokol) |
| 7 | Zaman bilgisi geçerliliği callback **dönümüne** bağlı, daha sonraki bir zamana değil | §6.2 adım 4 | Başlangıç (protokol) |
| 8 | Ham zaman kodu kullanılırken gecikme düzeltmesi uygulanmalı | §6.4 kaynak uyarısı | Başlangıç (protokol) |

### §7.1 Gerçek 8.1 kanal haritası

| Alan | Değer |
|---|---|
| Hedef çıkış | 8.1 (LFE dâhil) |
| ASIO kanal indeksleri | **UNKNOWN** — seçili sürücüde okunmadı |
| `channelGroup` değerleri | **UNKNOWN** — sürücüye bağlı, ölçülmedi |
| Kanal adları | **UNKNOWN** — `ASIOGetChannelInfo` çağrılmadı |

> Bu hücreler uydurulmaz. Ölçüm `ASIOGetChannels` + `ASIOGetChannelInfo` ile
> yapılmalı ve sonuç K003 telemetrisine yazılmalıdır.

---

## §8 Karşı tarafta: WASAPI kanal, format ve rol modeli

### §8.1 Akış açma sırası (`Stream Management`)

| Adım | Çağrı | Ne verir |
|---|---|---|
| 1 | `IMMDevice::Activate(iid = IID_IAudioClient)` | `IAudioClient` arayüzü |
| 2 | `IAudioClient::GetMixFormat` / `IsFormatSupported` | Desteklenen format keşfi |
| 3 | `IAudioClient::GetDevicePeriod` / `GetStreamLatency` | Periyot ve gecikme |
| 4 | `IAudioClient::Initialize` | Format + tampon boyu + **shared/exclusive** seçimi |
| 5 | `IAudioClient::GetService` | Ek servis arayüzleri |

Kaynak, metodları **çağrı zamanına göre** ikiye ayırır:

| `Initialize`'dan **SONRA** çağrılabilen | `Initialize`'dan **önce veya sonra** çağrılabilen |
|---|---|
| `GetBufferSize` · `GetCurrentPadding` · `GetService` · `GetStreamLatency` · `Reset` · `Start` · `Stop` | `GetDevicePeriod` · `GetMixFormat` · `IsFormatSupported` |

`GetService`'in verebileceği arayüzler arasında kaynak şunları sayar:
`IAudioRenderClient` (çıkış tamponuna yazar), `IAudioCaptureClient` (giriş
tamponundan okur), `IAudioSessionControl`.

### §8.2 Format sorgusu: `GetMixFormat` ve `IsFormatSupported`

| Soru | Method | Dönen | Dikkat |
|---|---|---|---|
| Ses motoru paylaşımlı akışlarda **hangi** formatla iç işlem yapıyor? | `GetMixFormat` | `WAVEFORMATEX*` veya `WAVEFORMATEXTENSIBLE*` | Bellek **API tarafından** ayrılan; kullanıcı `CoTaskMemFree` ile **serbest bırakmalıdır**. Hata olursa `*ppDeviceFormat = NULL` |
| Belirli bir format uç nokta tarafından destekleniyor mu? | `IsFormatSupported` | Yaklaşık/özel format bilgisi | Paylaşımlı mod ile exclusive mod sonucu farklılaşır |
| Cihaz kimliği | `IMMDevice::GetId` | Uç nokta kimlik dizesi | Biçimi tanımsızdır — ** ayrıştırılmamalı** |

### §8.3 Cihaz rolleri (`Device Roles`)

| `ERole` sabiti | Rol | Örnek (çıkış) | Örnek (giriş) |
|---|---|---|---|
| `eConsole` | Bilgisayarla etkileşim | Oyunlar, sistem bildirimleri | Sesli komutlar |
| `eCommunications` | Başka biriyle sesli iletişim | Sohbet ve VoIP | Sohbet ve VoIP |
| `eMultimedia` | Ses içeriği oynatma/kayıt | Müzik ve filmler | Anlatım ve canlı müzik kaydı |

| Kural | Kaynaktaki ifade |
|---|---|
| Bir cihaz **hiçbir**, **bir**, **bazı** veya **tüm** roller atanmış olabilir | Device Roles |
| Her rol için **her anda tek** çıkış cihazı ve **tek** giriş cihazı vardır | Device Roles |
| Giriş ve çıkış rollerinin atanması **birbirinden bağımsızdır** | Device Roles |

### §8.4 `IMMDevice` edinme yolları

| Yol | Metod |
|---|---|
| Koleksiyondan | `IMMDeviceCollection::Item` |
| Varsayılan cihazdan | `IMMDeviceEnumerator::GetDefaultAudioEndpoint` |
| Kimlikten | `IMMDeviceEnumerator::GetDevice` |

`IMMDevice` metodları: `Activate` (COM nesnesi oluşturur), `GetId` (uç nokta kimlik
dizesi), `GetState` (§8.5 durum sabitleri). Uç noktaya özgü özellik için
`QueryInterface(iid = IID_IMMEndpoint)` kullanılır.

### §8.5 Karşılaştırma tablosu — iki model nasıl örtüşüyor

| Soru | ASIO | WASAPI |
|---|---|---|
| Kanal sayısı | `ASIOGetChannels` (giriş/çıkış ayrı) | `GetMixFormat` kanal maskesi + `IsFormatSupported` |
| Kanal kimliği | Kanal indeksi (0'dan) + `name[32]` | Kanal maskesi (`WAVEFORMATEX`) |
| Kanal grubu | `channelGroup` (analog/dijital ayrımı) | Yok karşılığı — format ile ayırt edilir |
| Kanal türü | `ASIOChannelInfo.type` | `WAVEFORMATEX` biçimi + alt format |
| Rol/lamba | Yok | `eConsole` / `eCommunications` / `eMultimedia` |
| Zaman damgası | `AsioTimeInfo` (tek yapı) | `IAudioClock::GetPosition` (iki çıkış parametresi) |
| Format değişikliği bildirimi | `sampleRateDidChange` callback · `kSampleRateChanged` bayrağı | Yeniden `Initialize` gerektiren hata yolları |

---

## §9 Karar tablosu (Neva Engine için öneri — başlangıç düzeyi değil)

| Karar | Öneri | Dayanak | Durum |
|---|---|---|---|
| Kanal sayımı | Sürücüden oku, üst sınırı kullanma | §2 | Başlangıç (protokol) |
| Grup kullanımı | Grup yalnız **gösterim** için; eşleme indeksle | §3.1 madde 2–3 | Başlangıç (protokol) |
| Zaman bilgisi modu | Müzakere et, edinmeden `bufferSwitchTimeInfo` bekleme | §6.2 | Başlangıç (protokol) |
| Zaman kodu | Kullanılmayacaksa `kTcValid` hiç set edilmez | §5.3 | Öneri |
| Karşı taraf kanal doğrulaması | `IsFormatSupported` + `GetMixFormat` | §8.2 | Öneri |
| 8.1 haritası | UNKNOWN — sürücüde okunacak | §7.1 | ⚠️ VERIFICATION REQUIRED |
| `channelGroup` gerçek değerleri | UNKNOWN — ölçülmedi | §7.1 | ⚠️ VERIFICATION REQUIRED |

---

## §10 İddia — kanıt tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| `ASIOGetChannels` giriş ve çıkış kanalı sayısını ayrı ayrı döndürür; yalnız biri varsa diğeri 0 ve `ASE_OK` | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `ASIOChannelInfo` girişte `channel`/`isInput`, çıkışta `isActive`/`channelGroup`/`type`/`name[32]` alır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `channelGroup` kanal indeksinden bağımsızdır; kanal indeksi her zaman 0'dan `numInputs`/`numOutputs`'a kadar sayılır ve her zaman en az bir grup (grup 0) vardır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Analog/S/PDIF/AES/EBU/ADAT gibi arayüzler için makul bir gruplama beklenir; host varsayılan 0 ve 1'i etkinleştirebilir | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `ASIOBufferInfo` girişte `isInput`/`channelNum`, çıkışta `buffers[2]` çift tampon adresleridir | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `AsioTimeInfo.systemTime` nanosaniye cinsindendir; Windows'ta `timeGetTime()`'dan türetilmelidir | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `kSystemTimeValid` ve `kSamplePositionValid` bayrakları her zaman geçerli olmalıdır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `ASIOTime` içinde `reserved[4]` 0 olmalı, `timeInfo` zorunlu, `timeCode` isteğe bağlıdır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Zaman bilgisi modu `kAsioSupportsTimeInfo` ile müzakere edilir; 1 dönünce `bufferSwitchTimeInfo` beklenir ve `ASIOTime` callback dönene kadar geçerlidir | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Zaman kodu ↔ örnek konumu ilişkisi giriş ve çıkışta ayrı tanımlıdır ve gerçek hesaplamada gecikmeler dikkate alınmalıdır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Örnek host kodu `kMaxInputChannels = 32` / `kMaxOutputChannels = 32` host sınırını ve sürücüden okunan `inputChannels`/`outputChannels` değerini ayrı tutar | https://forums.steinberg.net/t/asio-sdk-hostsample-dsd-mode-problem/97092 | 2026-10-12 | OK |
| Aynı konuda `ASIOCreateBuffers` DSD isteğinden sonra `ASE_InvalidParameter` döndürme senaryosu raporlanmıştır | https://forums.steinberg.net/t/asio-sdk-hostsample-dsd-mode-problem/97092 | 2026-10-12 | OK |
| ASIO 2.3 spesifikasyonu PDF olarak indirilebilir (326.940 bayt, %PDF-1.3) | https://dspace.tul.cz/server/api/core/bitstreams/39cee442-acf3-4ba4-8a9f-7b9988a8abe4/content | 2026-10-12 | OK (salt dosya varlığı; **metin çıkarılmadı → içerik iddiası yok**) |
| Steinberg'in resmî ASIO geliştirici forumu `developer/asio` kategorisinde lisans ve hata raporu başlıkları taşır | https://forums.steinberg.net/c/developer/asio/104 | 2026-10-12 | OK |
| `IAudioClient` `IMMDevice::Activate` ile alınır; `Initialize` akışın formatını, tampon boyunu ve shared/exclusive modunu belirler | https://learn.microsoft.com/en-us/windows/win32/coreaudio/stream-management | 2026-10-12 | OK |
| `GetBufferSize`/`GetCurrentPadding`/`GetService`/`GetStreamLatency`/`Reset`/`Start`/`Stop` yalnız `Initialize`'dan sonra; `GetDevicePeriod`/`GetMixFormat`/`IsFormatSupported` öncesi veya sonrası çağrılabilir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/stream-management | 2026-10-12 | OK |
| Roller `eConsole`, `eCommunications`, `eMultimedia`'dır; her rol için her anda tek çıkış ve tek giriş cihazı vardır ve giriş/çıkış atanması bağımsızdır | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-roles | 2026-10-12 | OK |
| `GetMixFormat` ses motorunun paylaşımlı akışlarda kullandığı formatı döndürür; bellek API tarafından ayrılır ve `CoTaskMemFree` ile serbest bırakılmalıdır | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getmixformat | 2026-10-12 | OK |
| `IsFormatSupported` uç noktanın desteklediği formatları keşfetmek için kullanılır | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-isformatsupported | 2026-10-12 | OK |
| `IMMDevice` `Item`/`GetDefaultAudioEndpoint`/`GetDevice` ile edinilir; `Activate`, `GetId`, `GetState` metodları vardır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immdevice | 2026-10-12 | OK |
| Seçili ASIO sürücüsünde 8.1 kanal haritası ve `channelGroup` değerleri | (ölçüm yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

---

## §11 Açık sorular

1. **Kanal haritası:** 8.1 çıkış hangi ASIO kanal indekslerine geliyor? LFE
   indeksi ayrı mı, `channelGroup` içinde mi? → sürücüde `ASIOGetChannelInfo`
   ile okunmalı → `K003-audio/backend`.
2. **Grup gerçekliği:** seçili sürücü `channelGroup`'u gerçekten kullanıyor mu,
   yoksa hepsi 0 mı dönüyor? → ölçüm PLANNED.
3. **Zaman bilgisi modu:** zorunlu mu, opsiyonel mi? Zorunluysa
   `bufferSwitch` yolu tamamen kullanılmayacak mı? → ADR.
4. **Timecode:** CoreMusic'te timecode gereksinimi var mı? Yoksa `kTcValid`
   hiç set edilmemeli ve `ASIOTimeCode` alanı boş bırakılmalı → ADR.
5. **WASAPI kanal doğrulaması:** fallback'te `IsFormatSupported` sonucu
   exclusive modda nasıl yorumlanacak? → `asio-wasapi-karsilastirma.md`.

---

## §12 İlgili vault düğümleri

| Düğüm | İlişki |
|---|---|
| `K003-audio/backend/asio/asio-latency-ve-jitter.md` | Zaman damgasının gecikme düzeltmesi (§6.4) |
| `K003-audio/backend/asio/asio-arayuzler.md` | İmzalar: `ASIOGetChannels`, `ASIOGetChannelInfo` |
| `K003-audio/backend/asio/asio-buffer-stratejileri.md` | Tampon boyu bu dosyadaki `bufferSize` ile aynı |
| `K003-audio/backend/asio/asio-cpp-ornekleri.md` | Kanal okuma kod örnekleri |
| `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` | `GetMixFormat` / `IsFormatSupported` / `IMMDevice` sahibi katman |
| `K004-dsp` | Kanal başına DSP iş parçacığı bu haritayı tüketecek |

---

## §13 Doğrulama protokolü (bu dosya için)

1. Her iddia §10 tablosunda bir URL'ye bağlanır; açılmayan iddia yazılmaz.
2. Kod blokları `Kaynak: <url> (<tarih>)` etiketlidir; `Dosya:` / `Satır:`
   biçiminde **uydurma repo referansı yoktur** (repo'da `*.cpp`/`*.h` bulunmaz).
3. PDF kaynağı yalnız **dosya varlığı** için kullanılmıştır; metni
   çıkarılamadığı için içerik iddiası bağlanmamıştır.
4. 8.1 kanal haritası ve `channelGroup` değerleri uydurulmaz → `UNKNOWN` /
   `⚠️ VERIFICATION REQUIRED`.
5. Yer tutucu etiketler kullanılmaz; bilinmeyen `UNKNOWN` yazılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** introlab/irl_audio asio.h (ASIOGetChannels · ASIOChannelInfo · channelGroup ·
ASIOBufferInfo · AsioTimeInfo · ASIOTime · kAsioSupportsTimeInfo) · Steinberg forum hostsample.cpp
paylaşımı (97092) · Steinberg ASIO developer forumu (104) · dspace.tul.cz ASIO 2.3 spesifikasyon
PDF'i · learn.microsoft Stream Management · Device Roles · GetMixFormat · IsFormatSupported ·
IMMDevice · 2026-10-12
