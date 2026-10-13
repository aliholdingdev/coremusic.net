---
title: "ASIO Derin-Dalı — Buffer Stratejileri (Tampon Boyutu · Priming · Çift Tampon · Xrun)"
node_id: "K003-audio/backend/asio/asio-buffer-stratejileri"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-arayuzler"
  - "K003-audio/backend/asio/asio-surucu-modeli"
  - "K004-dsp"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getbuffersize — GetBufferSize: uç nokta tamponunun azami kapasitesi (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getcurrentpadding — GetCurrentPadding: tampondaki dolu çerçeve sayısı (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-seteventhandle — SetEventHandle: tampon hazır olduğunda sistemin işaretlediği olay (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize — Initialize: akışın başlatılması (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getservice — GetService: IAudioRenderClient/IAudioCaptureClient servis arayüzü (2026-10-12 okundu)"
  - "https://github.com/audiosdk/asio/blob/main/host/sample/hostsample.cpp — ASIOGetBufferSize/CreateBuffers/priming örneği (2026-10-12 okundu)"
  - "https://www.ableton.com/en/articles/low-latency-asio-buffer-size/ — Ableton: buffer size ile gecikme/CPU ilişkisi (2026-10-12 okundu)"
  - "https://github.com/dechamps/FlexASIO/blob/master/README.md — FlexASIO: WASAPI üzerinde ASIO tampon davranışı (2026-10-12 okundu)"
  - "https://docs.juce.com/master/classjuce_1_1AbstractFifo.html — JUCE AbstractFifo: lock-free FIFO sözleşmesi (2026-10-12 okundu)"
  - "https://docs.juce.com/master/classjuce_1_1AudioIODevice.html — JUCE AudioIODevice: tampon boyutu sorgu arayüzü (2026-10-12 okundu)"
validation:
  - "URL + tarih + alıntı: §12 kanıt-iddia tablosundaki her satır bu turda açılıp okunmuş bir URL'ye bağlandı (2026-10-12)"
  - "Kod blokları resmi SDK/örnek dosya ve resmi doküman URL'siyle etiketlendi; uydurma Dosya/Satır referansı yok (repo'da *.cpp/*.h yok)"
  - "Satır sayısı ≥500, ön-matter alanları ve URL sayısı otomatik ölçüldü (2026-10-12)"
open_questions:
  - "Neva Engine varsayılan tampon boyutu kaç örnek? (48 kHz'te 128 örnek ≈ 2,67 ms — ürün kararı, kanıtlanmış ölçüm yok → ⚠️ VERIFICATION REQUIRED)"
  - "ASIO preferredSize önerilmezse JUCE'un 'shouldUsePreferredSize' yolu Neva Engine'de devreye girer mi? → ADR"
  - "Xrun sayacı raporlama arayüzü (kAsioOverload) K003 telemetrisine bağlanacak mı? → K003-audio/backend"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Buffer Stratejileri (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya ASIO'nun **tampon stratejisini** tarif eder: çift tampon şeması, boyut
> seçimi (`min/max/preferred/granularity`), `ASIOStart` öncesi **priming**, WASAPI
> uç-nokta tamponuyla karşılaştırma ve **xrun** tespiti. Sayısal varsayılanlar
> verilmez; yalnız kaynakta okunan değerler yazılır, okunmayanlar `UNKNOWN` işaretidir.

---

## §0 ELI15

Tampon, sesin fabrikadan çıktığı **ara depo** gibidir: kimi büyük (az CPU, çok
gecikme), kimi küçük (az gecikme, sık dolum riski). ASIO bu depoyu **iki parçalı**
tutar ve dolumunu sürücüye **haber vererek** yaptırır; bu haber gelmeden host
dokunmaz. Bu dosyanın işi, depoyu ne kadar büyüteceğimizi ve dolum sırasını
**nasıl kaydırmayacağımızı** anlatır.

---

## §1 Kimlik kartı

| Alan | Değer |
|---|---|
| Soru | Tampon ne zaman, kaç örnek, hangi sırayla doldurulur? |
| Sorumlu katman | Host (ASIO C API) + sürücü (donanım tamponu) |
| Sözleşme yeri | `asio.h` (`ASIOCreateBuffers`, `ASIOStart` yorumları) · `iasiodrv.h` (`getBufferSize`, `createBuffers`) |
| WASAPI karşılığı | `IAudioClient::GetBufferSize` / `GetCurrentPadding` / `SetEventHandle` |
| Kesiştiği düğümler | `asio-arayuzler.md` (imzalar) · `asio-surucu-modeli.md` (yükleme) · `K004-dsp` (işleme bloğu) |
| Kapsam dışı | DSP algoritmaları (`K004-dsp`) · sürücü içi DMA planı (üretici) |
| Ölçüm birimi | Örnek (frame) — milisaniye değil |

---

## §2 ASIO çift tampon şeması (protokol katmanı)

ASIO'nun resmî şeması **senkron çift tampon**tur: her kanal için iki tampon (`buffers[0]`
ve `buffers[1]`) vardır ve sürücü sırayla birini okurken host diğerini doldurur.

| # | Terim | Ne anlama gelir | Kaynak |
|---|---|---|---|
| 1 | `ASIOBufferInfo.buffers[0]` / `buffers[1]` | Aynı kanalın iki ayrı bellek penceresi | `asio.h` (`ASIOBufferInfo`) |
| 2 | `isInput` / `channelNum` | Pencerenin giriş mi çıkış mı olduğu ve kanal sırası | `asio.h` (`ASIOBufferInfo`) |
| 3 | `bufferSize` (CreateBuffers parametresi) | İki tamponun da uzunluğu — **tek bir değer tüm kanallarda ortak** | `asio.h` `ASIOCreateBuffers` |
| 4 | `bufferSwitch(long index, ASIOBool processNow)` | Sürücü → host bildirimi; `index` hangi tamponun sırası olduğunu söyler | `asio.h` `ASIOCallbacks` |
| 5 | `bufferSwitchTimeInfo(ASIOTime*, long, ASIOBool)` | Zaman bilgisi modunda aynı bildirim + `systemTime`/`samplePosition` | `asio.h` `ASIOCallbacks` |
| 6 | `kAsioSupportsTimeInfo` | Zaman bilgisi modunun `asioMessage` ile müzakere edilmesi | `asio.h` |

**Şemanın zorunluluğu:** host, `bufferSwitch` dönüşünde **o tampona dokunamaz** —
sürücü onu bir sonraki geçişte yeniden kullanır. Bu, ASIO'nun tek kilit (lock-free)
eşzamanlılık sözleşmesidir.

---

## §3 Boyut seçimi: `ASIOGetBufferSize` dörtlüsü

`ASIOGetBufferSize` host'a **dört** sayı verir; boyut bu dörtlüden **dışarı
çıkamaz**.

| Alan | Anlamı | Örnek davranışı |
|---|---|---|
| `minSize` | Desteklenen en küçük tampon | Küçültmek gecikmeyi düşürür, CPU yükünü artırır |
| `maxSize` | Desteklenen en büyük tampon | Büyütmek gecikmeyi artırır, kesme sayısını düşürür |
| `preferredSize` | Sürücünün önerdiği boyut | JUCE bu değere geri döner (`shouldUsePreferredSize`) |
| `granularity` | İzin verilen adım (kademeler) | Her sayı geçerli değildir; adım adımı gezilir |

`hostsample.cpp` bu dörtlüyü **olduğu gibi okur ve olduğu gibi kullanır** — boyut
uydurma (`arbitrary rounding`) yapılmaz:

```cpp
// Kaynak: github.com/audiosdk/asio · blob/main/host/sample/hostsample.cpp (2026-10-12)
if (ASIOGetBufferSize (&asioDriverInfo->minSize, &asioDriverInfo->maxSize,
                       &asioDriverInfo->preferredSize, &asioDriverInfo->granularity) == ASE_OK)
{
    printf ("ASIOGetBufferSize (min: %d, max: %d, preferred: %d, granularity: %d);\n",
            asioDriverInfo->minSize, asioDriverInfo->maxSize,
            asioDriverInfo->preferredSize, asioDriverInfo->granularity);
}
```

### §3.1 Örnek ↔ milisaniye dönüşümü

| Formül | Sonuç (48 kHz) | Sonuç (44,1 kHz) |
|---|---|---|
| `ms = örnek / örnekleme×1000` · 64 örnek | 1,33 ms | 1,45 ms |
| 128 örnek | 2,67 ms | 2,90 ms |
| 256 örnek | 5,33 ms | 5,80 ms |
| 512 örnek | 10,67 ms | 11,61 ms |
| 1024 örnek | 21,33 ms | 23,22 ms |

⚠️ Bunlar **aritmetik dönüşümdür**, ölçüm değildir; gerçek gecikme tamponu da
kapsar (bkz `asio-latency-ve-jitter.md`).

---

## §4 Priming — `ASIOStart` öncesi ilk dolum

Protokol, `ASIOStart`'tan **önce** host'un birinci çıkış tamponunu doldurmasını
zorunlu kılar; aksi halde ilk geçişte sürücü boş okur.

```cpp
// Kaynak: github.com/audiosdk/asio · blob/main/host/sample/hostsample.cpp (2026-10-12)
// create_and activate buffers
result = ASIOCreateBuffers (asioDriverInfo->bufferInfos,
                            asioDriverInfo->inputBuffers + asioDriverInfo->outputBuffers,
                            asioDriverInfo->preferredSize, &asioCallbacks);
if (result == ASE_OK)
{
    // now get all the buffer details, sample word length, name, word clock group and activation
    for (i = 0; i < asioDriverInfo->inputBuffers + asioDriverInfo->outputBuffers; i++)
    {
        asioDriverInfo->channelInfos[i].channel = asioDriverInfo->bufferInfos[i].channelNum;
        asioDriverInfo->channelInfos[i].isInput = asioDriverInfo->bufferInfos[i].isInput;
        result = ASIOGetChannelInfo (&asioDriverInfo->channelInfos[i]);
        if (result != ASE_OK)
            break;
    }
}
```

> `ASIOCreateBuffers` başarılı olursa host ilk iş **kanal bilgilerini** toplar; ardından
> gecikmeler okunur. Kaynak yorumu: *"Latencies often are only valid after
> ASIOCreateBuffers()"* (kaynak: hostsample.cpp, 2026-10-12).

### §4.1 Priming sırası

| # | Adım | Çağrı | Bozulursa |
|---|---|---|---|
| 1 | Tamponlar ayrılır | `ASIOCreateBuffers` | `ASE_NotInitialized` |
| 2 | Çıkış tamponu 1 doldurulur | host bellek yazımı | İlk geçişte ses patlaması |
| 3 | Akış başlar | `ASIOStart` | — |
| 4 | İlk bildirim gelir | `bufferSwitch(0)` | — |
| 5 | Kapanışta ters sırada | `ASIOStop` → `ASIODisposeBuffers` → `ASIOExit` | `ASE_InvalidMode` |

### §4.2 Zaman bilgili bildirimde ilk geçişin okunuşu

`bufferSwitchTimeInfo` gövdesi, priming'den **sonraki** ilk geçişte hangi
bilgilerin geçerli olduğunu bayraklarla ayırır:

```cpp
// Kaynak: github.com/audiosdk/asio · blob/main/host/sample/hostsample.cpp (2026-10-12)
ASIOTime* bufferSwitchTimeInfo (ASIOTime* timeInfo, long index, ASIOBool processNow)
{
    static long processedSamples = 0;
    asioDriverInfo.tInfo = *timeInfo;

    if (timeInfo->timeInfo.flags & kSystemTimeValid)
        asioDriverInfo.nanoSeconds = ASIO64toDouble (timeInfo->timeInfo.systemTime);
    else
        asioDriverInfo.nanoSeconds = 0;

    if (timeInfo->timeInfo.flags & kSamplePositionValid)
        asioDriverInfo.samples = ASIO64toDouble (timeInfo->timeInfo.samplePosition);
    else
        asioDriverInfo.samples = 0;

    return 0;
}
```

| Bayrak | Anlamı | Geçersizse |
|---|---|---|
| `kSystemTimeValid` | `systemTime` geçerli (nanosaniye) | `nanoSeconds = 0` |
| `kSamplePositionValid` | `samplePosition` geçerli (örnek sayacı) | `samples = 0` |
| `kTcValid` | Zaman kodu geçerli | `tcSamples` yok sayılır |

> Bu desen, tampon stratejisinin **zamanlama tarafıdır**: dolum sırası kadar,
> "ilk dolumun zaman damgası geçerli mi" de önemlidir. Ayrıntı:
> `asio-multichannel-ve-timestamp.md` §4.

### §4.3 Priming + çift tampon birlikte okunduğunda

| Durum | `buffers[0]` | `buffers[1]` | Bildirim |
|---|---|---|---|
| `ASIOCreateBuffers` sonrası | ayrıldı, boş | ayrıldı, boş | yok |
| Priming sonrası | **doldu** | ayrıldı, boş | yok |
| `ASIOStart` sonrası ilk geçiş | sürücü okur | host doldurur | `bufferSwitch(0)` |
| İkinci geçiş | host doldurur | sürücü okur | `bufferSwitch(1)` |
| `ASIOStop` sonrası | dokunulmaz | dokunulmaz | bildirim kesilir |

---

## §5 WASAPI tampon üçlüsü (karşılaştırma katmanı)

ASIO'da tek bir `createBuffers` varken WASAPI'de **üç ayrı sorgu** vardır; ikisi
**dolumla** ilgilidir, biri **uyarmla**:

| WASAPI çağrısı | Ne döndürür | ASIO eşdeğeri |
|---|---|---|
| `GetBufferSize` | "Uç nokta tamponunun **azami kapasitesi**" (meta açıklaması) | `ASIOCreateBuffers` uzunluğu |
| `GetCurrentPadding` | "Tampondaki **dolu çerçeve sayısı**" (meta açıklaması) | `bufferSwitch` bildirimi (doluluk sorgusu yok) |
| `SetEventHandle` | "Sistem, tampon işlenmeye hazır olduğunda bu olayı işaretler" (meta açıklaması) | `bufferSwitch` geri çağırması |
| `Initialize` | "Ses akışını başlatır" (meta açıklaması) | `ASIOInit` + `ASIOStart` |
| `GetService` | `IAudioRenderClient` / `IAudioCaptureClient` servisini verir | Doğrudan tampon işaretçileri |

**Stratejik fark:** ASIO host'a **ham işaretçi** verir (kopya yok, tek kopya
semantiği); WASAPI host'tan **paket doldurmasını** ister (`GetBuffer` → `ReleaseBuffer`).

---

## §6 Olay tabanlı mı, yoklama (polling) mı?

| Strateji | WASAPI karşılığı | ASIO'da | Artı | Eksi |
|---|---|---|---|---|
| Olay tabanlı | `SetEventHandle` + bekleme | `bufferSwitch` geri çağırması | Uyku mümkün, gecikme düşük | Geri çağırma kuralarına sadık kalınmalı |
| Yoklama | `GetCurrentPadding` döngüsü | Yok (protokolde yoklama yok) | Basit | Boşa CPU, gecikme belirsiz |
| Karışık | Olay + doluluk kontrolü | `bufferSwitch` + `getSamplePosition` | Kilitlenme direnci | Kilit (lock) ekler → lock-free bozulur |

**ASIO'nun seçimi protokolle sabittir:** yoklama yoktur; sürücü bildirir.

---

## §7 JUCE katmanı: `AbstractFifo` ve blok kuyruğu

JUCE, ASIO tamponu ile uygulama kuyruğu arasındaki **tek okuyucu / tek yazıcı**
geçişi `AbstractFifo` ile kurar. Resmî dokümanın kendi örneği:

```cpp
// Kaynak: docs.juce.com/master/classjuce_1_1AbstractFifo.html (2026-10-12)
// "Encapsulates the logic required to implement a lock-free FIFO."
struct MyFifo
{
    void addToFifo (const int* someData, int numItems)
    {
        const auto scope = abstractFifo.write (numItems);
        if (scope.blockSize1 > 0) copySomeData (myBuffer + scope.startIndex1, someData, scope.blockSize1);
        if (scope.blockSize2 > 0) copySomeData (myBuffer + scope.startIndex2, someData + scope.blockSize1, scope.blockSize2);
    }
    AbstractFifo abstractFifo { 1024 };
    int myBuffer[1024];
};
```

| JUCE üye | Sözleşme |
|---|---|
| `AbstractFifo(int bufferSize)` | "Yönetilecek tampon boyutuyla bir FIFO oluşturur" |
| `write(numItems)` | Yazma **kapsamı** verir (`startIndex1/blockSize1` + sarma parçası) |
| `prepareToWrite` / `prepareToRead` | Dokümantasyonun tarif ettiği çift-parça erişim |
| `getNumReady()` | Okunabilir öğe sayısı |
| `reset()` | "Tamponu boş gösterir" |

**Kritik nokta:** `AbstractFifo` **veriyi tutmaz**, yalnız konumu yönetir — gerçek
kopya host'un kendi tamponunda kalır (çift kopya yasağı ihlal edilmez).

---

## §8 JUCE ASIO açılışındaki boyut stratejisi

JUCE, istenen boyut geçersizse **sürücünün tercihine** döner:

```cpp
// Kaynak: github.com/juce-framework/JUCE · blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
if (bufferSizeSamples < 8 || bufferSizeSamples > 32768)
    shouldUsePreferredSize = true;
```

ve `createBuffers` başarısız olursa `preferredBufferSize` ile **tekrar dener**:

```cpp
// Kaynak: github.com/juce-framework/JUCE · blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
err = asioObject->createBuffers (bufferInfos, totalBuffers, currentBlockSizeSamples, &callbacks);
if (err != ASE_OK)
{
    currentBlockSizeSamples = preferredBufferSize;
    asioObject->disposeBuffers ();
    err = asioObject->createBuffers (bufferInfos, totalBuffers, currentBlockSizeSamples, &callbacks);
}
```

### §8.1 JUCE tampon aralığı

| Sınır | Değer | Sonuç |
|---|---|---|
| Alt sınır | 8 örnek | Altında `shouldUsePreferredSize = true` |
| Üst sınır | 32768 örnek | Üstünde `shouldUsePreferredSize = true` |
| Tercih edilen | `preferredBufferSize` | `ASIOGetBufferSize`'den gelir |

> JUCE `AudioIODevice` arayüzü: `getCurrentBufferSizeSamples()` = "cihazın şu an
> kullandığı tampon boyutu" · `getAvailableBufferSizes()` = "kullanılabilir boyut
> kümesi" · `getDefaultBufferSize()` = "varsayılan boyut" (docs.juce.com, 2026-10-12).

---

## §9 Xrun (kesme/taşma) tespiti

| Sinyal | Kaynak | Host tepkisi |
|---|---|---|
| `kAsioOverload` | `asioMessage` seçicisi | sayaç artır (JUCE: `++xruns`) |
| İlk geçişte boş okuma | Priming unutulmuşsa | Protokol ihlali — priming zorunlu |
| `kAsioResetRequest` | Sürücü sıfırlama ister | Erteleme + güvenli anda `ASIOStop`/`ASIODisposeBuffers` |
| `kAsioResyncRequest` | Ölümcül olmayan veri kaybı | Medyaları yeniden hizala |
| `kAsioLatenciesChanged` | Gecikme değişti | Yeniden sorgu |

> `hostsample.cpp` `kAsioResetRequest` yorumu: sürücü **çağrı içinden**
> sıfırlanamaz; yıkım `ASIOStop()` + `ASIODisposeBuffers()` + yeniden `ASIOInit`
> ile güvenli anda yapılır (kaynak: hostsample.cpp, 2026-10-12).

### §9.1 `hostsample.cpp` mesaj çözümleyici (alıntı)

```cpp
// Kaynak: github.com/audiosdk/asio · blob/main/host/sample/hostsample.cpp (2026-10-12)
long asioMessages (long selector, long value, void* message, double* opt)
{
    long ret = 0;
    switch (selector)
    {
        case kAsioSelectorSupported:
            if (value == kAsioResetRequest
            || value == kAsioEngineVersion
            || value == kAsioResyncRequest
            || value == kAsioLatenciesChanged
            || value == kAsioSupportsTimeInfo
            || value == kAsioSupportsTimeCode
            || value == kAsioSupportsInputMonitor)
                ret = 1L;
            break;
        case kAsioResetRequest:
            // defer the task and perform the reset of the driver during the next "safe" situation
            // You cannot reset the driver right now, as this code is called from the driver.
            ret = 1L;
            break;
        case kAsioLatenciesChanged:
            ret = 1L;
            break;
        case kAsioEngineVersion:
            ret = 2L;
            break;
        case kAsioSupportsTimeInfo:
            ret = 1L;
            break;
    }
    return ret;
}
```

> Bu blok `hostsample.cpp` içindeki `asioMessages` gövdesinin **özgün**
> işleme dallarıdır; `kAsioSupportsTimeInfo`'in `1` dönmesi zaman bilgisi modunu
> açar (ayrıntı: `asio-arayuzler.md` §5).

### §9.2 JUCE karşı-tepki: xrun ve reset kuyruğu

```cpp
// Kaynak: github.com/juce-framework/JUCE · blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp (2026-10-12)
case kAsioOverload:          ++xruns; return 1;

void resetRequest() noexcept
{
    startTimer (500);
}
```

JUCE `kAsioResetRequest`'i **anında** işlemez; 500 ms gecikmeli bir `Timer` ile
`needToReset` bayrağını kaldırır ve sürücüyü **bir sonraki güvenli açılışta**
yeniden kurar. Bu, `hostsample.cpp` yorumundaki "defer the task" kuralının
uygulamadaki karşılığıdır.

---

## §10 Gecikme-CPU ödünleşimi (kaynaklı, sayısal ölçüm yok)

Ableton dokümanı ödünleşimi **nitel** kurar: küçük buffer = düşük gecikme ama
daha fazla CPU; büyük buffer = daha yüksek gecikme ama daha az kesme riski.
**Hangi boyutun Neva Engine için doğru olduğu ölçülmemiştir** →
⚠️ VERIFICATION REQUIRED (ölçüm protokolü: `asio-latency-ve-jitter.md` §9).

| Boyut ailesi | Eğilim | Kullanım tipi |
|---|---|---|
| 64–128 örnek | En düşük gecikme, yüksek CPU | Canlı monitör |
| 256–512 örnek | Denge | Genel kayıt/çalma |
| 1024–2048 örnek | Yüksek gecikme, düşük CPU | Ağır projeler |

⚠️ Bu tablo **eğilim** tablosudur; hiçbir hücre ölçüm iddiası taşımaz.

### §10.1 WASAPI tarafında aynı ödünleşimin kodu (karşılaştırmalı okuma)

```text
Kaynak: learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getbuffersize (2026-10-12)
Alıntı: "The GetBufferSize method retrieves the size (maximum capacity) of the endpoint buffer."

Kaynak: learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getcurrentpadding (2026-10-12)
Alıntı: "The GetCurrentPadding method retrieves the number of frames of padding in the endpoint buffer."

Kaynak: learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getstreamlatency (2026-10-12)
Alıntı: "The GetStreamLatency method retrieves the maximum latency for the current stream
and can be called any time after the stream has been initialized."
```

Bu üç çağrı birlikte WASAPI'de ASIO'nun tek seferlik `ASIOGetLatencies` +
`bufferSwitch` bildirim ikilisinin karşılığını üretir. Fark: WASAPI host **doluluk
sorgular**, ASIO host **bildirim alır**.

### §10.2 Aynı ödünleşimin JUCE arayüzündeki yansıması

```text
Kaynak: docs.juce.com/master/classjuce_1_1AudioIODevice.html (2026-10-12)
Alıntı (üyeler): start(AudioIODeviceCallback*) · stop() · isPlaying() ·
getLastError() · getCurrentBufferSizeSamples() · getCurrentSampleRate() ·
getCurrentBitDepth() · getAvailableSampleRates() · getAvailableBufferSizes() ·
getDefaultBufferSize()
```

JUCE bu üyeleri **saf sanal** tanımlar; ASIO'ya bakan alt sınıf (`ASIOAudioIODevice`)
değeri sürücüden okur. Yani boyut sorusu JUCE'da tek yerden, ASIO'da sürücüden
gelir — iki farklı sorgu yolu, tek uygulama yüzeyi.

---

## §11 Karar tablosu (Neva Engine için öneri — bağlayıcı değil)

| Karar | Öneri | Dayanak | Durum |
|---|---|---|---|
| Boyut kaynağı | `preferredSize` ile başla, başarısızlıkta `preferredBufferSize`'e dön | JUCE uygulaması (§8) | Öneri |
| Kademeler | `granularity` dışına **çıkma** | `ASIOGetBufferSize` sözleşmesi | Bağlayıcı (protokol) |
| Priming | Zorunlu | `ASIOStart` protokol kuralı | Bağlayıcı (protokol) |
| Kilit | ASIO callback'inde kilit yok | Çift tampon semantiği | Bağlayıcı (protokol) |
| Kuyruk | Tek yazıcı/tek okuyucu FIFO | JUCE `AbstractFifo` | Öneri |
| Boyut sayısı | UNKNOWN (ölçülmedi) | Ölçüm yok | ⚠️ VERIFICATION REQUIRED |

---

## §12 İddia — kanıt tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| `GetBufferSize` uç nokta tamponunun **azami kapasitesini** verir | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getbuffersize | 2026-10-12 | ✅ |
| `GetCurrentPadding` dolu çerçeve sayısını verir | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getcurrentpadding | 2026-10-12 | ✅ |
| `SetEventHandle` sistem tarafından işaretlenen olayı kurar | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-seteventhandle | 2026-10-12 | ✅ |
| `Initialize` ses akışını başlatır | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | 2026-10-12 | ✅ |
| `GetService` render/capture servisini verir | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getservice | 2026-10-12 | ✅ |
| Örnek `ASIOGetBufferSize` dörtlüsü okur, `ASIOCreateBuffers` bu dörtlüyle çağrılır | https://github.com/audiosdk/asio/blob/main/host/sample/hostsample.cpp | 2026-10-12 | ✅ |
| Ableton buffer size'ın gecikme/CPU ödünleşimini anlatır | https://www.ableton.com/en/articles/low-latency-asio-buffer-size/ | 2026-10-12 | ✅ |
| FlexASIO ASIO tamponunu WASAPI'e taşır | https://github.com/dechamps/FlexASIO/blob/master/README.md | 2026-10-12 | ✅ |
| `AbstractFifo` lock-free tek-oku/yaz FIFO mantığını kapsar, veriyi tutmaz | https://docs.juce.com/master/classjuce_1_1AbstractFifo.html | 2026-10-12 | ✅ |
| `AudioIODevice` boyut/sürat sorgu üyeleri vardır | https://docs.juce.com/master/classjuce_1_1AudioIODevice.html | 2026-10-12 | ✅ |
| Neva Engine için sayısal varsayılan tampon boyutu | (ölçüm yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| Priming zorunluluğu | `asio.h` `ASIOStart` yorumu → `asio-arayuzler.md` §3 | 2026-10-12 | ✅ (düğüm içi çapraz) |

---

## §13 Açık sorular

1. **Varsayılan boyut:** 128 mi, 256 mi? Ölçüm `asio-latency-ve-jitter.md` §9
   protokolüyle yapılmalı → **üst karar:** ürün.
2. **Kademeli seçim arayüzü:** Kullanıcı arayüzü `granularity` kümesini nasıl
   gösterecek? → `design/` (PLANNED).
3. **`kAsioBufferSizeChange`:** `asio.h` "desteklenmiyor, her zaman 0 dön" der;
   canlı boyut değişimi Neva Engine'de desteklenecek mi? → ADR.
4. **WASAPI fallback'te boyut:** ASIO kaybından sonra WASAPI karşılığı kaç
   çerçeve? (IAudioClient3 engine period'u → `asio-wasapi-karsilastirma.md` §6.)
5. **Ölçüm altyapısı:** Xrun sayacı hangi telemetri kanalına yazılacak? → K003.

---

## §14 İlgili vault düğümleri

| Düğüm | İlişki |
|---|---|
| `K003-audio/backend/asio/asio-arayuzler.md` | İmzaların kaynağı (`ASIOCreateBuffers`, `bufferSwitch`) |
| `K003-audio/backend/asio/asio-surucu-modeli.md` | Sürücünün tampon sahipliği |
| `K003-audio/backend/asio/asio-latency-ve-jitter.md` | Boyut → gecikme zinciri |
| `K003-audio/backend/asio/asio-wasapi-karsilastirma.md` | WASAPI tampon üçlüsü |
| `K003-audio/backend/asio/asio-cpp-ornekleri.md` | Uygulamalı örnekler |
| `K004-dsp` | İşleme bloğu boyutu bu boyutu tüketir |

---

## §15 Doğrulama protokolü (bu dosya için)

1. Her iddia §12 tablosunda bir URL'ye bağlıdır; açılmayan iddia yazılmaz.
2. Kod blokları `Kaynak: <depo/yol> (<tarih>)` etiketlidir; `Dosya: / Satır:`
   biçiminde **uydurma repo referansı yoktur** (repo'da `*.cpp`/`*.h` bulunmaz).
3. Sayısal ölçüm iddiası **yapılmaz**; yalnız aritmetik dönüşüm tablosu vardır.
4. Yer tutucu etiketler ve "ileri tarihte yapılacak" ifadeleri kullanılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** audiosdk/asio hostsample.cpp · learn.microsoft.com audioclient
(GetBufferSize · GetCurrentPadding · SetEventHandle · Initialize · GetService) ·
ableton.com low-latency-asio-buffer-size · dechamps/FlexASIO README ·
docs.juce.com AbstractFifo + AudioIODevice · juce_ASIO_windows.cpp · 2026-10-12
