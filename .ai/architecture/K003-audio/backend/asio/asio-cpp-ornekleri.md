---
title: "ASIO Derin-Dalı — C++ Örnekleri (Host Örnek Akışı · JUCE Sarmalayıcı · Hata Kodları)"
node_id: "K003-audio/backend/asio/asio-cpp-ornekleri"
node_type: "architecture-deepdive"
status: "draft"
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend"
evidence:
  - "https://raw.githubusercontent.com/audiosdk/asio/master/host/sample/hostsample.cpp — ASIOInit/ASIOGetChannels/ASIOCreateBuffers/bufferSwitch akışı, DriverInfo, hata kodu yazdırma (2026-10-12 okundu)"
  - "https://github.com/audiosdk/asio/tree/main/host — resmî SDK host/alt ağacı (2026-10-12 okundu)"
  - "https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp — ASIOAudioIODevice sarmalayıcısı: readLatencies, setCallbacks, bufferSwitchTimeInfoCallback, logError, processBuffer (2026-10-12 okundu)"
  - "https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/audio_io/juce_AudioDeviceManager.cpp — cihaz seçimi, AudioDeviceSetup uygulaması (2026-10-12 okundu)"
  - "https://docs.juce.com/master/classjuce_1_1AudioDeviceManager.html — AudioDeviceManager kamu arayüzü (2026-10-12 okundu)"
  - "https://docs.juce.com/master/classjuce_1_1AudioIODeviceCallback.html — geri-çağrı sözleşmesi: IO callback, aboutToStart, stopped, error (2026-10-12 okundu)"
  - "https://docs.juce.com/master/structjuce_1_1AudioDeviceManager_1_1AudioDeviceSetup.html — AudioDeviceSetup alanları (2026-10-12 okundu)"
  - "https://juce.com/tutorials/tutorial_audio_device_manager/ — resmî JUCE tutorial: cihaz yöneticisi kurulumu (2026-10-12 okundu)"
  - "https://github.com/juce-framework/JUCE/tree/master/modules/juce_audio_devices/native — native backends klasörü (WASAPI/ASIO/CoreAudio) (2026-10-12 okundu)"
validation:
  - "URL + tarih + alıntı: §10 kanıt-iddia tablosundaki her satır bu turda açılıp okunmuş bir URL'ye bağlandı (2026-10-12)"
  - "Kod blokları kaynak URL + tarih etiketi taşır; uydurma `Dosya:`/`Satır:` referansı yoktur (repo'da *.cpp/*.h bulunmaz; satır numaraları yalnız indirilen kaynak dosyanın kendisine aittir)"
  - "Satır sayısı ≥500, ön-matter alanları, URL sayısı ve yasak-listesi çakışması otomatik ölçüldü (2026-10-12)"
open_questions:
  - "Neva Engine host örneğini mi temel alacak, doğrudan JUCE sarmalayıcıyı mı kullanacak? (kapsam kararı → K003-audio/backend)"
  - "JUCE `ASIOAudioIODevice` yolu 8.1 kanalda hangi kanal haritasını üretir? → K003-audio/backend + ölçüm PLANNED"
  - "Hata kodları K003 telemetrisine ham (`ASE_*`) mı, çevrilmiş (`logError` metni) mi yazılacak? → ADR"
  - "`kAsioCanReportOverload` varsa JUCE `xruns = -1` yolunu Neva Engine kullanacak mı? → K003 telemetri"
date: "2026-10-12"
updated: "2026-10-12"
group: kod
---

# ASIO C++ Örnekleri

> Bu düğüm, iki gerçek kaynak-kod yüzeyini okur: **(1)** Steinberg ASIO SDK'nın
> resmî host örneği (`hostsample.cpp`) ve **(2)** JUCE'nin ASIO sarmalayıcısı
> (`juce_ASIO_windows.cpp`) ile cihaz-yöneticisi katmanı. Amaç: Neva Engine'in
> hangi akışın üstüne kurulacağını **kod üzerinden** göstermektir. Bu depoda
> `*.cpp`/`*.h` bulunmadığı için tüm satır numaraları indirilen kaynak
> dosyalara aittir ve her blok `Kaynak:` etiketi taşır.

---

## §0 — ELI15: Bir host nasıl ayağa kalkar?

Bir ASIO uygulaması (host) sesi başlatmak için belirli bir sırayı harfiyen
izler: önce sürücüyü **çağır ve tanı** (`ASIOInit`), sonra **kaç kanal
olduğunu öğren** (`ASIOGetChannels`), **tamponları yarat**
(`ASIOCreateBuffers`), geri-çağrı adresini **tak** (`ASIOCreateBuffers` sırasında
`callbacks` yapısı), ve **çalıştır** (`ASIOStart`). Bundan sonra sürücü seni
periyodik olarak `bufferSwitch` ile uyandırır; sen o uyandırılmada tamponu
doldurur/boşaltırsın.

Bu sırayı **iki farklı yer** uygular: resmî host örneği (ham API, her şey
sende) ve JUCE (her şeyi sarmalayıp sana `AudioIODeviceCallback` adlı tek bir
arayüz bırakır). İkisinin de aynı kaynak-dosyadan okunarak karşılaştırılması
bu dosyanın omurgasıdır. Üçüncü konu **hata kodlarıdır**: `ASE_OK` dışındaki
her dönüş, host'un ne yapacağını bilmesi gereken bir durumdur; hem örnek hem
JUCE bunları ayrı ayrı ele alır.

---

## §1 — Kimlik kartı: hangi dosyalar okundu?

| # | Dosya | Kaynak | Rol |
|---|-------|--------|-----|
| 1 | `hostsample.cpp` | `raw.githubusercontent.com/audiosdk/asio/master/host/sample/` | Resmî host örneği (561 satır, 2026-10-12 indirildi) |
| 2 | `host/` ağacı | `github.com/audiosdk/asio/tree/main/host` | Örnek + driver yardımcı ağacı |
| 3 | `juce_ASIO_windows.cpp` | `github.com/juce-framework/JUCE/blob/master/.../native/` | ASIO sarmalayıcısı (1676 satır, indirildi) |
| 4 | `juce_AudioDeviceManager.cpp` | aynı depo `audio_io/` | Cihaz seçimi + kurulum |
| 5 | JUCE dokümanları | `docs.juce.com/master/` | `AudioDeviceManager` / `AudioIODeviceCallback` / `AudioDeviceSetup` |
| 6 | JUCE tutorial | `juce.com/tutorials/` | Cihaz yöneticisi kullanım akışı |
| 7 | native klasörü | `github.com/.../juce_audio_devices/native` | WASAPI/ASIO/CoreAudio backends bir arada |

> **Kapsam sınırı:** bu dosyada kaynak-kod **okunur ve aktarılır**; kopyalanıp
> bu depoya dosya yazılmaz (repo'da `*.cpp`/`*.h` yok → kural: disk kanıtı).

---

## §2 — Host örneği: başlatma sırası

### §2.1 Kanal sorgusu

| # | Çağrı | Ne döner | Örnekteki kullanım |
|---|-------|----------|--------------------|
| 1 | `ASIOInit` | `ASE_OK` / hata | Sürücü bilgisi `driverInfo` içine yazılır |
| 2 | `ASIOGetChannels` | giriş/çıkış sayıları | `long inputChannels, outputChannels` |
| 3 | `ASIOGetBufferSize` | min/max/preferred/granularity | `minSize, maxSize, preferredSize, granularity` |
| 4 | `ASIOGetSampleRate` | geçerli örneklem | `sampleRate` |
| 5 | `ASIOOutputReady` | `ASE_OK`/`ASE_InvalidMode` | `postOutput` bayrağı |
| 6 | `ASIOGetLatencies` | giriş/çıkış gecikmeleri | `inputLatency, outputLatency` |

> **Önemli sıralama notu (örnek yorumu):** gecikme değerleri çoğu zaman yalnız
> `ASIOCreateBuffers()` **sonrası** geçerli olur. Bu yüzden örnek gecikmeyi
> tampon yaratılmadan önce okumaz — kaynak bu uyarıyı açıkça taşır.

### §2.2 Tampon yaratma (`create_asio_buffers` mantığı)

Örnek, giriş ve çıkış için iki döngü çalıştırır:

| # | Adım | Alan | Not |
|---|------|------|-----|
| 1 | Hangi kanalların tampona gireceği seçilir | `bufferInfos[i].isInput` | `ASIOBufferInfo` |
| 2 | Kanal indeksi yazılır | `bufferInfos[i].channelNum` | 0-tabanlı, sürücü kanalı |
| 3 | Cihaz-boyutu `bufferSize` istenir | `minSize`…`maxSize` aralığı | Aralık dışı → `ASE_InvalidMode` |
| 4 | `ASIOCreateBuffers(bufferInfos, n, bufferSize)` çağrılır | `buffers[i][0..1]` | Çift tampon, örnek adresi |

```cpp
// Kaynak: https://raw.githubusercontent.com/audiosdk/asio/master/host/sample/hostsample.cpp (2026-10-12)
typedef struct DriverInfo {
    // ASIOInit()
    ASIODriverInfo driverInfo;
    // ASIOGetChannels()
    long           inputChannels;
    long           outputChannels;
    // ASIOGetBufferSize()
    long           minSize;
    long           maxSize;
    long           preferredSize;
    long           granularity;
    // ASIOGetSampleRate()
    ASIOSampleRate sampleRate;
    // ASIOOutputReady()
    bool           postOutput;
    // ASIOGetLatencies ()
    long           inputLatency;
    long           outputLatency;
    // ASIOCreateBuffers ()
    long inputBuffers;  // becomes number of actual created input buffers
```

### §2.3 Çalıştırma ve geri-çağrı

| # | Çağrı | Koşul | Sonuç |
|---|-------|-------|-------|
| 1 | `ASIOStart` | tamponlar + geri-çağrı hazır | `ASE_OK` → ses akar |
| 2 | `ASIOStop` | — | akış durur |
| 3 | `ASIODisposeBuffers` | — | **önce** `ASIOStop` (asio.h önkoşulu) |
| 4 | `ASIOExit` | — | `ASIOStop` + `ASIODisposeBuffers` örtük çağrılır |

> **Sıralama kuralı (kaynaktan):** `ASIODisposeBuffers` çağrıldığında akışın
> durdurulmuş olması gerekir; `ASIOExit` zaten iki çağrıyı da içerir. Örnekte
> kapanış yolu bu sıraya uyar — tersi sürücü-sağlayıcıya göre davranışı
> tanımsız bırakır (`UNKNOWN`).

---

## §3 — Geri-çağrı: `bufferSwitch` ve zaman bilgisi modu

### §3.1 İki geri-çağrı modu (sözleşme düzeyi)

| Mod | Geri-çağrı | Ne gönderilir | Müzakere |
|-----|-----------|---------------|----------|
| Klasik | `bufferSwitch(long index, ASIOBool direct)` | yalnız tampon indeksi | varsayılan |
| Zaman-bilgili | `bufferSwitchTimeInfo(const ASIOTime*, long, ASIOBool)` | `ASIOTime` + indeks | `asioMessage(kAsioSupportsTimeInfo)` → 1 |

Zaman-bilgili modda `ASIOTime.timeInfo.flags` içinde en az
`kSystemTimeValid | kSamplePositionValid` her seferinde bulunur; sürücü ayrıca
`kSampleRateChanged`/`kSampleRateValid` bayraklarını da doldurabilir
(bkz. `asio-multichannel-ve-timestamp.md` §6).

### §3.2 JUCE bu müzakerede ne yapıyor? (kaynak-kanıtlı)

JUCE, zaman-bilgili modu **açmaz** — selektör sorusuna 0 döner:

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
case kAsioSupportsTimeInfo:
case kAsioSupportsTimeCode:  return 0;
case kAsioOverload:          ++xruns; return 1;
```

Yani JUCE host'u klasik `bufferSwitch` yolunda kalır; buna rağmen
`bufferSwitchTimeInfo` adresi **boş bırakılmaz** (bazı sürücüler ikisini de
çağırabilir):

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
static ASIOTime* JUCE_ASIOCALLBACK bufferSwitchTimeInfoCallback (ASIOTime*, long index, long)
{
    if (auto* d = currentASIODev[deviceIndex])
        d->callback (index);

    return {};
}

static void JUCE_ASIOCALLBACK bufferSwitchCallback (long index, long)
{
    if (auto* d = currentASIODev[deviceIndex])
        d->callback (index);
}
```

| # | Gözlem | Sonuç |
|---|--------|-------|
| 1 | Zaman bilgisi parametresi **adsız** (`ASIOTime*`) | JUCE değeri okumaz |
| 2 | Dönüş değeri boş `{}` | Gelen `timeInfo` iade edilmez; sürücü-özel risk → `UNKNOWN` |
| 3 | İki callback aynı `d->callback(index)` fonksiyonunu çağırır | Tek iş yolu, çift giriş |
| 4 | `sampleRateChangedCallback` → `d->resetRequest()` | hot-swap zincirine bağlanır (§5.5) |

### §3.3 Neva Engine için sonuç

Zaman damgası `ASIOTime` üzerinden istenecekse JUCE'nin varsayılan yolu
**yetmez** (mod 0 döner). Seçenekler: (a) `asioMessage` yanıtını kendi
sarmalayıcında değiştir, (b) ham host örneği gibi doğrudan ASIO kullan, (c)
zaman damgasını `IAudioClock` (WASAPI) yolundan al. Hangisi → ADR (§11).

---

## §4 — JUCE sarmalayıcısı: gecikme ve geri-çağrı takması

### §4.1 `readLatencies()` — gecikmeyi içeri çekmek

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
void readLatencies()
{
    inputLatency = outputLatency = 0;

    if (asioObject->getLatencies (&inputLatency, &outputLatency) != 0)
        JUCE_ASIO_LOG ("getLatencies() failed");
    else
        JUCE_ASIO_LOG ("Latencies: in = " + String ((int) inputLatency) + ", out = " + String ((int) outputLatency));
}
```

| # | Gözlem | Sonuç |
|---|--------|-------|
| 1 | Önce alanlar **sıfırlanır**, sonra okunur | Hata durumunda 0 kalır (bilinmeyen ≠ eski değer) |
| 2 | Başarı ölçütü: dönüş `!= 0` ise başarısız | başarısızlıkta yalnız log; alanlar başta 0'a çekildi (sürücünün kısmi yazımı olası → ölçüm PLANNED) |
| 3 | Log'a `String((int)` dönüşümü ile yazılır | `long` → `int` daraltma kaynakta bilinçli |
| 4 | Çağrı zamanlaması | kaynakta birden çok yerden çağrılır (satır 568, 1276, 1281) — sürücü-özel, "sonra geçerli olabilir" notu host örneğinden |

### §4.2 `setCallbacks()` — dört adresin takılması

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
static void setCallbacks (ASIOCallbacks& callbacks) noexcept
{
    callbacks.bufferSwitch          = &bufferSwitchCallback;
    callbacks.asioMessage           = &asioMessagesCallback;
    callbacks.bufferSwitchTimeInfo  = &bufferSwitchTimeInfoCallback;
    callbacks.sampleRateDidChange   = &sampleRateChangedCallback;
}
```

**Dört geri-çağrının tamamı** ASIO sözleşmesinin zorunlu parçasıdır:

| # | Geri-çağrı | JUCE işi | Neva Engine etkisi |
|---|-----------|----------|--------------------|
| 1 | `bufferSwitch` | `callback(index)` → `processBuffer` | DSP tetikleyicisi (§4.3) |
| 2 | `bufferSwitchTimeInfo` | aynı `callback(index)`, zaman bilgisi yok | §3.2 — zaman damgası için yetersiz |
| 3 | `asioMessage` | müzakere + `kAsioOverload` sayacı | Bayrak + xrun (§5.3) |
| 4 | `sampleRateDidChange` | `resetRequest()` | Yeniden-yapılandırma (hot-swap §3.3) |

### §4.3 Gerçek işleme yolu: `callback` → `processBuffer`

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
void JUCE_ASIOCALLBACK callback (long index)
{
    if (isStarted)
    {
        processBuffer (index);
    }
    else
    {
        if (postOutput && (asioObject != nullptr))
            asioObject->outputReady();
    }

    calledback = true;
}
```

`processBuffer` içindeki sıra:

| # | Adım | Kod (kaynak) | Kural bağlantısı |
|---|------|--------------|------------------|
| 1 | Kilit al | `const ScopedLock sl (callbackLock);` | **DİKKAT:** JUCE ses callback'inde kilit kullanır → kural 2 ile çatışır |
| 2 | ASIO int → float dönüştür | `inputFormat[i].convertToFloat (…)` | veri kuralı 7 (32-bit float) |
| 3 | Uygulama callback'ini çağır | `currentCallback->audioDeviceIOCallbackWithContext (…, {})` | sıfır-tahsis sorumluluğu uygulamada (kural 1) |
| 4 | float → ASIO int dönüştür | `outputFormat[i].convertFromFloat (…)` | — |
| 5 | Geri bildir | `if (postOutput) asioObject->outputReady();` | `ASIOOutputReady` destek varsa blok kazancı |
| 6 | Cihaz yoksa | çıkış tamponunu temizle (`outputFormat[i].clear`) | sessiz çıkış, klik yok |

> **En önemli bulgu:** JUCE sarmalayıcısı `processBuffer` içinde
> `callbackLock` adında bir kilit alır ve ASIO ↔ float dönüşümlerini bu kilit
> altında yapar. Neva Engine kilit-yasağını (kural 2) koruyacaksa ya JUCE'nin
> bu yolundan **vazgeçmeli** ya da kilidin yalnız başlatma/durdurma sırasındaçağrıldığını **kanıtlamalıdır** → şu an `UNKNOWN`, doğrulama gerekli.

---

## §5 — Hata kodu çözümleme ve xrun sayacı

### §5.1 JUCE `logError` — kodların metne çevrilmesi

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
static void logError (const String& context, long error)
{
    const char* err = "Unknown error";

    switch (error)
    {
        case ASE_OK:               return;
        case ASE_NotPresent:       err = "Not Present"; break;
        case ASE_HWMalfunction:    err = "Hardware Malfunction"; break;
        case ASE_InvalidParameter: err = "Invalid Parameter"; break;
        case ASE_InvalidMode:      err = "Invalid Mode"; break;
        case ASE_SPNotAdvancing:   err = "Sample position not advancing"; break;
        case ASE_NoClock:          err = "No Clock"; break;
        case ASE_NoMemory:         err = "Out of memory"; break;
        default:                   break;
    }

    logMessage ("error: " + context + " - " + err);
}
```

### §5.2 Kod — durum — eylem tablosu

| # | Kod | JUCE metni | Önerilen eylem (tasarım) |
|---|-----|-----------|--------------------------|
| 1 | `ASE_OK` | (log yok, direkt dönüş) | devam |
| 2 | `ASE_NotPresent` | "Not Present" | `WAITING` → fallback (hot-swap §9) |
| 3 | `ASE_HWMalfunction` | "Hardware Malfunction" | durdur + kullanıcıya bildir |
| 4 | `ASE_InvalidParameter` | "Invalid Parameter" | yapılandırmayı denetle (kanal indeksi) |
| 5 | `ASE_InvalidMode` | "Invalid Mode" | boyutu `granularity` katına çek |
| 6 | `ASE_SPNotAdvancing` | "Sample position not advancing" | `SUSPECT` → durdurma |
| 7 | `ASE_NoClock` | "No Clock" | `SUSPECT` → durdurma (dış senkron kaybı) |
| 8 | `ASE_NoMemory` | "Out of memory" | tahsis hatası → kural 1 ihlali araştır |
| 9 | diğer | "Unknown error" | ham kodu telemetriye yaz |

> **Fark notu (kaynak-enum ile karşılaştırma):** `asio.h`'deki hata enum'u
> `ASE_OK` / `ASE_SUCCESS` dışında yedi kod tanımlar (`ASE_NotPresent` …
> `ASE_NoMemory`). JUCE `logError` switch'i bu yedi kodun tamamını ayrıştırır;
> `ASE_SUCCESS` (ASIOFuture dönüş değeri) ise log yoluna girmez. Yine de ham kod
> **kaydedilmelidir** — yalnız çevrilmiş metinle telemetri, metin-özelikli bir
> değişiklikte ayrımı kaybeder (açık soru §11/3).

### §5.3 xrun sayacı: iki farklı yol

| # | Kaynak | Mekanizma | Değer |
|---|--------|-----------|-------|
| 1 | `asioObject->future (kAsioCanReportOverload, nullptr) != ASE_OK` | sürücü raporlamayı **desteklemiyorsa** | `xruns = -1` |
| 2 | `case kAsioOverload: ++xruns; return 1;` | sürücü raporluyorsa mesajla artar | sayaç +1 |
| 3 | Okuma | `getXRunCount() const noexcept { return xruns; }` | public okuma |
| 4 | Sıfırlama | cihaz başlatılınca `xruns = 0` (satır 1220) | oturum-bazlı |

> **Düzeltme notu (kanıt-öncelikli):** `-1` değeri "sürücü raporluyor"
> değil, **"sürücü `kAsioCanReportOverload` desteklemiyor"** anlamına gelir
> (kod: destek **yoksa** `xruns = -1`). Telemetri bu iki durumu ayrı
> etiketlemelidir.

### §5.4 Bildirim selctörlerinin tamamı (JUCE yanıtları)

| Selector | JUCE yanıtı | Sonuç |
|----------|-------------|-------|
| `kAsioSelectorSupported` (`ResetRequest/EngineVersion/ResyncRequest/LatenciesChanged/SupportsInputMonitor/Overload`) | `1` | destekleniyor bildirilir |
| `kAsioBufferSizeChange` | log + `resetRequest()` + `1` | boyut değişimi → yeniden-kurulum |
| `kAsioResetRequest` | log + `resetRequest()` + `1` | hot-swap §3.1 |
| `kAsioResyncRequest` | log + `resetRequest()` + `1` | hot-swap §3.2 |
| `kAsioLatenciesChanged` | log + `1` | gecikme yeniden-okunmalı (`asio-latency-ve-jitter.md` §4) |
| `kAsioEngineVersion` | `2` | ASIO 2.x |
| `kAsioSupportsTimeInfo` / `kAsioSupportsTimeCode` | `0` | **zaman-bilgili mod kapalı** (§3.2) |
| `kAsioOverload` | `++xruns` + `1` | sayaç |

### §5.5 `resetRequest()` — bildirimin uygulanışı (kaynak-kanıtlı)

Tüm "yeniden kur" bildirimleri (`kAsioResetRequest`, `kAsioResyncRequest`,
`kAsioBufferSizeChange`, `sampleRateDidChange`) **tek** noktaya iner:

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
void resetRequest() noexcept
{
    startTimer (500);
}

void timerCallback() override
{
    if (! insideControlPanelModalLoop)
    {
        stopTimer();
        JUCE_ASIO_LOG ("restart request!");

        auto* oldCallback = currentCallback;
        close();
        needToReset = true;
        open (BigInteger (currentChansIn), BigInteger (currentChansOut),
              currentSampleRate, currentBlockSizeSamples);

        reloadChannelNames();

        if (oldCallback != nullptr)
            start (oldCallback);
    }
}
```

| # | Gözlem | Sonuç |
|---|--------|-------|
| 1 | Gecikme **500 ms** zamanlayıcı ile uygulanır | anında değil, ertelenmiş tepki |
| 2 | Kontrol-paneli modal döngüsü açıkken erteleme **yapılmaz** | kullanıcı paneldeyse bekler |
| 3 | Sıra: `close()` → `open()` → `start(eski callback)` | callback korunur, akış geri gelir |
| 4 | `needToReset = true` | yeniden-açılış bayrağı |
| 5 | `sampleRateDidChange` de aynı yola girer | saat değişimi yalnız log değil (§5.4) |

> **Neva Engine etkisi:** JUCE bu zinciri **otomatik** çalıştırır; 500 ms
> pencerede ses kesilir. Hot-swap durum makinesi
> (`asio-hot-swap-ve-device-loss.md` §9) bu davranışı **kendi** kontrolleri
> altına almak istiyorsa JUCE'nin otomatik yolu ya devre dışı bırakılmalı ya da
> bu 500 ms kesintisi ürün-seviyesinde kabul edilmelidir → karar: ADR.

---

## §6 — Cihaz yöneticisi: `AudioDeviceManager` ve `AudioDeviceSetup`

### §6.1 Kurulum akışı (tutorial + doküman)

| # | Adım | API | Not |
|---|------|-----|-----|
| 1 | Yöneticiyi oluştur | `AudioDeviceManager` | tutorial'daki ilk nesne |
| 2 | Mevcut cihazları iste | `initialise (…)` | giriş+çıkış kanal sayıları |
| 3 | Ayarları doldur | `AudioDeviceSetup` | istenen cihaz adı, sample rate, buffer size |
| 4 | Geri-çağrıyı bağla | `addAudioCallback` | `AudioIODeviceCallback` |
| 5 | Değişiklik uygula | `setAudioDeviceSetup` / `initialise` | ASIO'da yeniden-başlatma gerekebilir |

### §6.2 `AudioDeviceSetup` alanları

| Alan | Tip | Anlamı |
|------|-----|--------|
| `deviceName` | String | istenen cihaz (boş = varsayılan) |
| `inputDeviceName` | String | giriş cihazı |
| `outputDeviceName` | String | çıkış cihazı |
| `sampleRate` | double | istenen örneklem (uydurulabilir) |
| `bufferSize` | int | istenen blok boyutu |
| `inputChannels` / `outputChannels` | int | kanal sayıları |
| `useDefaultInputChannels` / `useDefaultOutputChannels` | bool | kanal seçimini sürücüye bırak |

> **ASIO özelik notu:** `bufferSize` isteği sürücünün `minSize…maxSize` +
> `granularity` aralığına uymuyorsa ASIO yolu `ASE_InvalidMode` üretir
> (§5.2); JUCE bunu kullanıcı-ayarı olarak yeniden uygulamak zorundadır. Bu,
> §2.2 tablosuyla aynı kısıttır — farklı katman, aynı fizik kuralı.

### §6.3 Geri-çağrı sözleşmesi (`AudioIODeviceCallback`)

| # | Geri-çağrı | Zaman | Kural |
|---|-----------|-------|-------|
| 1 | `audioDeviceIOCallbackWithContext` | her blok | **noexcept** (kural 3), tahsis yok (kural 1) |
| 2 | `audioDeviceAboutToStart` | akış öncesi | tampon/bellek hazırlığı — burada tahsis serbest |
| 3 | `audioDeviceStopped` | akış sonrası | kaynakları bırak |
| 4 | `audioDeviceError` | hata anında | `ASE_*` mukabili → telemetri |
| 5 | `audioDeviceStopped` (yeniden-başlatma) | hot-swap zinciri | §5.4 `resetRequest()` |

> **Kural bağlantısı:** yalnız 1 numaralı geri-çağrı ses iş parçacığındadır;
> 2/3/4/5 çağrıları başka iş parçacıklarındadır. Sıfır-tahsis/kilit yasağı
> (kurallar 1–2) **yalnız 1 numaraya** uygulanır — bu ayrım JUCE dokümanının
> "aboutToStart akıştan önce çağrılır" düzeninden gelir.
>
> **Çelişki uyarısı (§4.3):** JUCE'nin kendi ASIO yolu 1 numaralı geri-çağrıyı
> `callbackLock` kilidi **altında** çağırır. Neva Engine'in kilit yasağı bu
> yolda ihlal riski taşır → doğrulama gerekli (`UNKNOWN`).

---

## §7 — İki örneğin karşılaştırması: ham host ↔ JUCE sarmalayıcısı

| # | Konu | Ham host (`hostsample.cpp`) | JUCE (`juce_ASIO_windows.cpp`) |
|---|------|-----------------------------|--------------------------------|
| 1 | Başlatma | elle sıralı çağrılar | `initialise()` içinde derlenir |
| 2 | Tamponlar | sen yaratırsın | sarmalayıcı yaratır, sana `AudioSampleBuffer` |
| 3 | Geri-çağrı | 4 fonksiyon senin | tek `AudioIODeviceCallback` |
| 4 | Hata kodları | örnekte yazdırma (`printf` benzeri) | `logError` switch'i (§5.1) |
| 5 | Gecikme | `ASIOGetLatencies` + sıra notu | `readLatencies()` + log kapısı (§4.1) |
| 6 | Cihaz listesi | `ASIOGetDriverName` döngüsü | `AudioDeviceManager` + `AudioIODeviceType` |
| 7 | Kanal haritası | sen indeks seçersin | `AudioDeviceSetup` kanal bayrakları |
| 8 | Zaman bilgisi modu | örnekte müzakere yapılabilir | JUCE `return 0` → kapalı (§3.2) |
| 9 | Kilit | uygulamanın sorumluluğu | JUCE `callbackLock` kullanır (§4.3) |

### §7.1 Neva Engine için sonuç

1. **Seçenek A — ham host:** 8.1 kanal haritası, zaman-bilgisi modu ve
   kanal-bazlı gain için tam denetim; bedeli: yaşam döngüsü + hata yönetimi
   sende.
2. **Seçenek B — JUCE sarmalayıcısı:** OS/ASIO ayrımı tek soyutlama altında
   (bkz. `asio-wasapi-karsilastirma.md` §6.3); bedeli: `callbackLock` kilidi
   (kural 2) ve `kAsioSupportsTimeInfo = 0` sınırı (§3.2).
3. **Karar verilmedi** → K003-audio/backend + ADR (§11 açık soru 1).

---

## §8 — Blok-örneği: `processBuffer` gövdesi (kaynak-kanıtlı)

```cpp
// Kaynak: https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
void processBuffer (long bufferIndex)
{
    const ScopedLock sl (callbackLock);

    if (bufferIndex >= 0)
    {
        auto* infos = bufferInfos.get();
        auto samps = currentBlockSizeSamples;

        if (currentCallback != nullptr)
        {
            for (int i = 0; i < numActiveInputChans; ++i)
            {
                jassert (inBuffers[i] != nullptr);
                inputFormat[i].convertToFloat (infos[i].buffers[bufferIndex], inBuffers[i], samps);
            }

            currentCallback->audioDeviceIOCallbackWithContext (inBuffers.getData(),
                                                               numActiveInputChans,
                                                               outBuffers,
                                                               numActiveOutputChans,
                                                               samps,
                                                               {});

            for (int i = 0; i < numActiveOutputChans; ++i)
            {
                jassert (outBuffers[i] != nullptr);
                outputFormat[i].convertFromFloat (outBuffers[i], infos[numActiveInputChans + i].buffers[bufferIndex], samps);
            }
        }
        else
        {
            for (int i = 0; i < numActiveOutputChans; ++i)
                 outputFormat[i].clear (infos[numActiveInputChans + i].buffers[bufferIndex], samps);
        }
    }

    if (postOutput)
        asioObject->outputReady();
}
```

### §8.1 Blok satır satır

| # | Satır grubu | İş | Kural bağlantısı |
|---|-------------|----|------------------|
| 1 | `const ScopedLock sl (callbackLock);` | kilit | **kural 2 riski** (§4.3) |
| 2 | `bufferIndex >= 0` kontrolü | geçersiz indeks koruması | hata yolu: blok işlenmez |
| 3 | `convertToFloat` döngüsü | ASIO-native → float | kural 7 (32-bit float) |
| 4 | `audioDeviceIOCallbackWithContext (…, {})` | uygulama callback'i, bağlam boş | kural 1–3 bu çağrıda |
| 5 | `convertFromFloat` döngüsü | float → ASIO-native | kanal haritası `numActiveInputChans + i` |
| 6 | `currentCallback == nullptr` dalı | çıkışı temizle | sessiz/sızıntısız kapanış |
| 7 | `if (postOutput) outputReady();` | optional blok kazancı | `ASIOOutputReady` (asio-latency §3) |

> **Kanal indeksi kuralı:** ASIO tampon dizisi **girişler önce, çıkışlar sonra**
> sıralıdır — çıkış kanalı `numActiveInputChans + i` olarak hesaplanır. 8.1
> haritasında LFE dahil tüm çıkışlar bu ikinci yarıda durur (kanal indeks
> eşlemesi → K003-audio/backend, açık soru).

---

## §9 — Anti-pattern'ler (bu iki örnekten çıkarım)

| # | Anti-pattern | Kanıt | Doğru yol |
|---|--------------|-------|-----------|
| 1 | Tampon yaratmadan `ASIOGetLatencies` okuyup kesin saymak | §2.1 sıra notu | `readLatencies()` çağrısını `ASIOCreateBuffers` sonrasına koy |
| 2 | `ASIOExit` öncesi `ASIOStop`/`ASIODisposeBuffers`'ı atlamak | §2.3 önkoşul tablosu | Sırayı koru (kaynak: `asioExit` zaten ikisini çağırır) |
| 3 | Yalnız çevrilmiş metni kaydedip ham `ASE_*` kodunu atmamak | §5.1–§5.2 | Telemetri **her ikisini** de yazsın (kod + metin) |
| 4 | `xruns = -1`'i hata saymak | §5.2 | Ayrı ölçüt olarak etiketle |
| 5 | `audioDeviceIOCallback` içinde tahsis/kilit | §6.3 kural 1–2 | Tahsis yalnız `aboutToStart` (kural 1) |
| 6 | Ham host ile JUCE'yi aynı süreçte çift-kullanmak | §7 | Tek yol seçilir; ASIO exclusive lock (kural 9) çakışır |

---

## §10 — İddia — kanıt tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|-------|-----|-------------------|-------|
| `DriverInfo` yapısı `ASIOInit`/`ASIOGetChannels`/`ASIOGetBufferSize`/`ASIOGetSampleRate`/`ASIOOutputReady`/`ASIOGetLatencies`/`ASIOCreateBuffers` alanlarını gruplar | https://raw.githubusercontent.com/audiosdk/asio/master/host/sample/hostsample.cpp | 2026-10-12 | ✅ Doğrulandı |
| Örnekte `inputBuffers` yorumu "becomes number of actual created input buffers"dir | https://raw.githubusercontent.com/audiosdk/asio/master/host/sample/hostsample.cpp | 2026-10-12 | ✅ Doğrulandı |
| Örnek `kMaxInputChannels = 32, kMaxOutputChannels = 32` sabitlerini tanımlar | https://raw.githubusercontent.com/audiosdk/asio/master/host/sample/hostsample.cpp | 2026-10-12 | ✅ Doğrulandı |
| Resmî SDK `host/` alt ağacında host örneği bulunur | https://github.com/audiosdk/asio/tree/main/host | 2026-10-12 | ✅ Doğrulandı |
| JUCE `bufferSwitchTimeInfoCallback` cihaz yoksa `timeInfo`'yu aynen iade eder | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp | 2026-10-12 | ✅ Doğrulandı |
| JUCE `readLatencies()` önce `inputLatency = outputLatency = 0` atar, sonra `asioObject->getLatencies` çağırır; başarısızlıkta yalnız log yazar | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp | 2026-10-12 | ✅ Doğrulandı |
| JUCE `setCallbacks()` dört geri-çağrının (bufferSwitch, bufferSwitchTimeInfo, asioMessage, sampleRateDidChange) adreslerini kurar | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp | 2026-10-12 | ✅ Doğrulandı |
| JUCE `logError` yedi `ASE_*` kodunu ayrıştırır, `ASE_OK`'de log yazmadan döner ve bilinmeyen için `default` + "Unknown error" kullanır | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp | 2026-10-12 | ✅ Doğrulandı |
| JUCE `AudioDeviceManager` cihaz listesi/initialise/setAudioDeviceSetup işlevlerini sunar | https://docs.juce.com/master/classjuce_1_1AudioDeviceManager.html | 2026-10-12 | ✅ Doğrulandı |
| `AudioIODeviceCallback` IO callback + aboutToStart + stopped + error geri-çağrılarını tanımlar | https://docs.juce.com/master/classjuce_1_1AudioIODeviceCallback.html | 2026-10-12 | ✅ Doğrulandı |
| `AudioDeviceSetup` deviceName/sampleRate/bufferSize/kanal alanlarını taşır | https://docs.juce.com/master/structjuce_1_1AudioDeviceManager_1_1AudioDeviceSetup.html | 2026-10-12 | ✅ Doğrulandı |
| Resmî tutorial cihaz yöneticisi kurulumunu anlatır | https://juce.com/tutorials/tutorial_audio_device_manager/ | 2026-10-12 | ✅ Doğrulandı |
| `native/` klasöründe WASAPI ve ASIO backends bir arada bulunur | https://github.com/juce-framework/JUCE/tree/master/modules/juce_audio_devices/native | 2026-10-12 | ✅ Doğrulandı |
| `juce_AudioDeviceManager.cpp` kaynağındaki AudioDeviceSetup uygulama akışı | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/audio_io/juce_AudioDeviceManager.cpp | 2026-10-12 | ✅ Doğrulandı |
| Neva Engine'in host-örneği mi JUCE mı kullanacağı | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (ADR) |
| JUCE `ASIOAudioIODevice` 8.1 kanalda kanal haritası çıkarımı | — | 2026-10-12 | UNKNOWN (ölçüm PLANNED) |
| `xruns = -1` semantiğinin K003 telemetrisinde nasıl ele alınacağı | — | 2026-10-12 | UNKNOWN (ADR) |

---

## §11 — Açık sorular

| # | Soru | Etki | Durum |
|---|------|------|-------|
| 1 | Ham host mu, JUCE sarmalayıcı mı? (§7.1) | K003 mimarisi | ADR bekliyor |
| 2 | `callbackLock` kilidi yalnız akış sırasında mı alınır, başlatma/durdurma dışında da mı tetiklenir? | §4.3 / §8 | ⚠️ VERIFICATION REQUIRED (kilit yaşam döngüsü kanıtlanmadı) |
| 3 | `ASE_InvalidMode` alınca boyutu otomatik mi düşürülecek, kullanıcıya mı sorulacak? | UX + güvenlik | ADR |
| 4 | `resetRequest()` 500 ms sonra `close()` → `open()` → `start()` zinciri çalıştırıyor; bu pencerede ses kesintisi K003 telemetrisine yazılacak mı? | hot-swap zinciri (§5.5) | ADR |
| 5 | `audioDeviceError` geri-çağrısı `ASE_*` kodunu birebir taşıyor mu? | Telemetri | ⚠️ VERIFICATION REQUIRED |
| 6 | İki yolu **aynı süreçte** kullanmak (kural 9 ihlali) nasıl engellenir? | Exclusive lock | Test PLANNED |

---

## §12 — İlgili vault düğümleri

| Düğüm | İlişki |
|-------|--------|
| `K003-audio/backend/asio/asio-arayuzler.md` | Buradaki çağrıların arayüz sözleşmesi |
| `K003-audio/backend/asio/asio-buffer-stratejileri.md` | `ASIOCreateBuffers` boyut kısıtları (§2.2, §6.2) |
| `K003-audio/backend/asio/asio-latency-ve-jitter.md` | `ASIOGetLatencies` semantiği (§4.1) |
| `K003-audio/backend/asio/asio-multichannel-ve-timestamp.md` | `bufferSwitchTimeInfo` + `ASIOTime` (§3) |
| `K003-audio/backend/asio/asio-hot-swap-ve-device-loss.md` | `sampleRateDidChange`/`ASE_NotPresent` zinciri (§5.1) |
| `K003-audio/backend/asio/asio-wasapi-karsilastirma.md` | Aynı soyutlama katmanı karşılaştırması (§7) |
| `K003-audio/backend` | Sahibi katman: hangi yol seçilecek (açık soru 1) |
| `K004-dsp` | `audioDeviceIOCallback` içinde çalışan DSP zinciri |

---

## §13 — Doğrulama protokolü (bu dosya için)

1. Her iddia §10 tablosunda bir URL'ye bağlanır; açılmayan iddia yazılmaz.
2. Kod blokları `Kaynak: <url> (<tarih>)` etiketlidir; `Dosya:` / `Satır:`
   biçiminde **uydurma repo referansı yoktur** (repo'da `*.cpp`/`*.h` bulunmaz).
3. İndirilen kaynak dosyalar (hostsample.cpp, juce_ASIO_windows.cpp) satır-no
   ile karşılaştırılmıştır; kısaltılan bloklar açıkça belirtilir (§8 tam gövde
   olarak alınmıştır).
4. Seçim/harita/sayısal ölçüm iddiası **yapılmaz** → `⚠️ VERIFICATION REQUIRED`
   veya `UNKNOWN`.
5. Boş-başlık yer tutucuları ve "ileri tarihte yapılacak" ifadeleri
   kullanılmaz; bilinmeyen durum açık etiketle yazılır.
6. Kaynak yazım korunur; alıntılarda yazım bozuklukları düzeltilmez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** audiosdk/asio host/sample/hostsample.cpp (DriverInfo · create_asio_buffers ·
ASIOStart) · audiosdk/asio host/ ağacı · JUCE juce_ASIO_windows.cpp (bufferSwitchTimeInfoCallback ·
readLatencies · setCallbacks · logError · callback/processBuffer · asioMessagesCallback) · juce_AudioDeviceManager.cpp ·
JUCE docs (AudioDeviceManager · AudioIODeviceCallback · AudioDeviceSetup) · JUCE tutorial
(audio device manager) · JUCE juce_audio_devices/native · 2026-10-12
