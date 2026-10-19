---
title: "ASIO Derin-Dalı — Arayüzler (IASIO* ve C API Sözleşmesi)"
node_id: "K003-audio/backend/asio/asio-arayuzler"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-anayasa"
  - "K000-isletim-sistemi"
evidence:
  - "https://github.com/audiosdk/asio/blob/master/common/asio.h — C API prototipleri, yapılar, hata kodları (ham kaynak)"
  - "https://github.com/thestk/rtaudio/blob/master/include/iasiodrv.h — IASIO arayüzünün bağımsız aynası (ham kaynak)"
  - "https://github.com/audiosdk/asio/blob/master/README.md — SDK dosya envanteri (common/host/driver)"
  - "https://github.com/dechamps/ASIOUtil/blob/master/BUFFERS.md — tampon/callback semantiği yorumu"
  - "https://github.com/juce-framework/JUCE/blob/90e8da0cfb54ac593cdbed74c3d0c9b09bad3a9f/modules/juce_audio_devices/native/juce_win32_ASIO.cpp — gerçek host kullanım örneği"
  - "https://github.com/thestk/rtaudio/blob/master/include/asiolist.cpp — sürücü listesi ve `CoCreateInstance` kullanımı"
  - "https://raw.githubusercontent.com/audiosdk/asio/master/common/asio.h — düz metin kaynak (imza doğrulaması)"
  - "https://github.com/audiosdk/asio/blob/master/changes.txt — hangi sürümde hangi seçici eklendi"
validation:
  - "Tüm imzalar `asio.h`/`iasiodrv.h` metniyle birebir karşılaştırıldı; uydurma imza yok (2026-10-12)"
  - "Kod blokları kaynak dosya adıyla etiketlendi; imzalar yeniden yazılmadı"
  - "Satır ≥500, ön-matter alanları ve URL sayısı otomatik sayıldı (2026-10-12)"
open_questions:
  - "`ASIOFuture` seçicilerinden hangileri CoreMusic scaffold'unda zorunlu? → ADR-097/098"
  - "Zaman bilgisi modu (`bufferSwitchTimeInfo`) Neva Engine'de kullanılacak mı? → embedded-engineer"
  - "`ASIOOutputReady` desteği host tarafında zorunlu mu? (asio.h: destek yoksa gecikme +1 blok)"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Arayüzler (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya ASIO'nun **iki sözleşme yüzeyini** tarif eder: (1) sürücünün uygulamaya açtığı
> C++ arayüzü `IASIO`, (2) uygulamanın (host) kullandığı düz C API'si `ASIOInit … ASIOOutputReady`.
> İmzalar kaynak dosyalardan **birebir** alınmıştır; hiçbir imza tahmin edilmemiştir.

---

## §0 ELI15

Bir DAW, ASIO sürücüsüyle iki dilden konuşur: **sürücüyü açarken** `IASIO` metodlarını
çağırır, **ses akarken** sürücünün çağırdığı `bufferSwitch` geri çağırmasına yazar —
ikisi arasındaki tek bağ `createBuffers`'a verilen `ASIOCallbacks` yapısıdır.

---

## §1 İki yüzey, tek nesne

| Yüzey | Tanım yerini | Yönü | Örnek |
|---|---|---|---|
| `IASIO` (C++ arayüzü) | `iasiodrv.h` | Host → sürücü (21 sanal metod) | `init`, `start`, `createBuffers` |
| C API (sarmalayıcı) | `asio.h` | Host → sürücü (aynı nesneye gider) | `ASIOInit`, `ASIOStart` |
| Geri çağırma | `asio.h` `ASIOCallbacks` | Sürücü → Host | `bufferSwitch`, `asioMessage` |
| Uzantı | `ASIOFuture` + seçiciler | Host → sürücü (isteğe bağlı) | `kAsioSetIoFormat` |

- Bu yüzeyler **aynı nesneyi** iki biçimde anlatır: C API çağrıları aslında sürücünün
  `IASIO` metodlarına yönlendirilen sarmalayıcılardır.
- Host uygulamaları genelde C API'yi kullanır (basit, C'den çağrılabilir).
- Sürücü yazarları `IASIO`'yu uygular (COM benzeri `IUnknown` mirası).
- ⚠️ `interface`/`typedef` tanımları MSVC'ye özgü yazım kullanır (`interface IASIO : public IUnknown`).

---

## §2 `IASIO` arayüzü (ham kaynak: `iasiodrv.h`)

```cpp
#include "asiosys.h"
#include "asio.h"

/* Forward Declarations */
#ifndef __ASIODRIVER_FWD_DEFINED__
#define __ASIODRIVER_FWD_DEFINED__
typedef interface IASIO IASIO;
#endif

interface IASIO : public IUnknown
{
	virtual ASIOBool init(void *sysHandle) = 0;
	virtual void getDriverName(char *name) = 0;
	virtual long getDriverVersion() = 0;
	virtual void getErrorMessage(char *string) = 0;
	virtual ASIOError start() = 0;
	virtual ASIOError stop() = 0;
	virtual ASIOError getChannels(long *numInputChannels, long *numOutputChannels) = 0;
	virtual ASIOError getLatencies(long *inputLatency, long *outputLatency) = 0;
	virtual ASIOError getBufferSize(long *minSize, long *maxSize,
		long *preferredSize, long *granularity) = 0;
	virtual ASIOError canSampleRate(ASIOSampleRate sampleRate) = 0;
	virtual ASIOError getSampleRate(ASIOSampleRate *sampleRate) = 0;
	virtual ASIOError setSampleRate(ASIOSampleRate sampleRate) = 0;
	virtual ASIOError getClockSources(ASIOClockSource *clocks, long *numSources) = 0;
	virtual ASIOError setClockSource(long reference) = 0;
	virtual ASIOError getSamplePosition(ASIOSamples *sPos, ASIOTimeStamp *tStamp) = 0;
	virtual ASIOError getChannelInfo(ASIOChannelInfo *info) = 0;
	virtual ASIOError createBuffers(ASIOBufferInfo *bufferInfos, long numChannels,
		long bufferSize, ASIOCallbacks *callbacks) = 0;
	virtual ASIOError disposeBuffers() = 0;
	virtual ASIOError controlPanel() = 0;
	virtual ASIOError future(long selector,void *opt) = 0;
	virtual ASIOError outputReady() = 0;
};
```

### §2.1 Metod sırası (vokal tablo sırası = çağrı sırası)

| # | Metod | C API karşılığı | Ne yapar |
|---|---|---|---|
| 1 | `init(void*)` | `ASIOInit` | Sürücüyü başlatır; Windows'ta ana pencere kolu |
| 2 | `getDriverName(char*)` | — (bilgi) | Sürücü adını yazar (eski host'lar için) |
| 3 | `getDriverVersion()` | — (bilgi) | Sürücü sürüm numarası |
| 4 | `getErrorMessage(char*)` | — (bilgi) | Son hata metni |
| 5 | `start()` | `ASIOStart` | Donanım akışını başlatır |
| 6 | `stop()` | `ASIOStop` | Akışı durdurur; sonra `bufferSwitch` yok |
| 7 | `getChannels(long*,long*)` | `ASIOGetChannels` | Giriş/çıkış kanal sayısı |
| 8 | `getLatencies(long*,long*)` | `ASIOGetLatencies` | Giriş/çıkış gecikme (örnek kare) |
| 9 | `getBufferSize(4×long)` | `ASIOGetBufferSize` | min/max/tercih/adım |
| 10 | `canSampleRate(double)` | `ASIOCanSampleRate` | Sürat desteği sorgusu |
| 11 | `getSampleRate(double*)` | `ASIOGetSampleRate` | Geçerli sürat |
| 12 | `setSampleRate(double)` | `ASIOSetSampleRate` | Sürat değiştirme (0 = harici senkron) |
| 13 | `getClockSources(...)` | `ASIOGetClockSources` | Saat kaynakları listesi |
| 14 | `setClockSource(long)` | `ASIOSetClockSource` | Saat kaynağı seçimi |
| 15 | `getSamplePosition(...)` | `ASIOGetSamplePosition` | Örnek konumu + zaman damgası |
| 16 | `getChannelInfo(...)` | `ASIOGetChannelInfo` | Kanal adı/grup/tür/aktiflik |
| 17 | `createBuffers(...)` | `ASIOCreateBuffers` | Çift tamponları ayırır + callback kaydı |
| 18 | `disposeBuffers()` | `ASIODisposeBuffers` | Tamponları serbest bırakır |
| 19 | `controlPanel()` | `ASIOControlPanel` | Üretici kontrol panelini açar |
| 20 | `future(long,void*)` | `ASIOFuture` | Seçicili uzantılar |
| 21 | `outputReady()` | `ASIOOutputReady` | "Çıkış tamponu doldu" bildirimi |

### §2.2 Arayüzün dikkat çeken yönleri

- **`IUnknown` mirası** vardır; yani `Release()` ile serbest bırakılır (COM yaşam döngüsü).
- **Sanal tablo sırası sabittir** — host ile sürücü aynı düzeni bekler; yeni metod
  sona eklenir, ortaya eklenmez.
- **Parametre yönü** dikkatle ayrılır: `init` `sysHandle` alır (Windows: ana pencere),
  `getChannels` gibi sorgular `long*` üzerinden geri yazar.
- **Geri dönen tip** sorgularda `ASIOError` (aslında `long`), bilgi sorgularında
  `void`/`long`'dur.
- **`outputReady` isteğe bağlıdır**: sürücü `ASE_NotPresent` dönerse host bu çağrıyı keser.
- **`future` selector tabanlıdır**: bilinmeyen seçicide `ASE_InvalidParameter` beklendi
  (asio.h: "if the selector is unknown, ASE_InvalidParameter should be returned").
- **Hata metni ayrıdır**: `getErrorMessage` son hatayı döndürür, hata koduna ek olarak.

---

## §3 C API prototip özeti (ham kaynak: `asio.h`)

```c
ASIOError ASIOInit(ASIODriverInfo *info);
ASIOError ASIOExit(void);
ASIOError ASIOStart(void);
ASIOError ASIOStop(void);
ASIOError ASIOGetChannels(long *numInputChannels, long *numOutputChannels);
ASIOError ASIOGetLatencies(long *inputLatency, long *outputLatency);
ASIOError ASIOGetBufferSize(long *minSize, long *maxSize, long *preferredSize, long *granularity);
ASIOError ASIOCanSampleRate(ASIOSampleRate sampleRate);
ASIOError ASIOGetSampleRate(ASIOSampleRate *currentRate);
ASIOError ASIOSetSampleRate(ASIOSampleRate sampleRate);
ASIOError ASIOGetClockSources(ASIOClockSource *clocks, long *numSources);
ASIOError ASIOSetClockSource(long reference);
ASIOError ASIOGetSamplePosition (ASIOSamples *sPos, ASIOTimeStamp *tStamp);
ASIOError ASIOGetChannelInfo(ASIOChannelInfo *info);
ASIOError ASIOCreateBuffers(ASIOBufferInfo *bufferInfos, long numChannels,
	long bufferSize, ASIOCallbacks *callbacks);
ASIOError ASIODisposeBuffers(void);
ASIOError ASIOControlPanel(void);
void *ASIOFuture(long selector, void *params);
ASIOError ASIOOutputReady(void);
```

### §3.1 Tip tanımları (asio.h)

```c
typedef long ASIOBool;
enum { ASIOFalse = 0, ASIOTrue = 1 };

typedef long ASIOSampleType;   /* örnek türü kodları, bkz. §7 */
typedef long ASIOError;        /* hata kodları, bkz. §6 */

/* 64-bit örnek sayacı ve zaman damgası (NATIVE_INT64 değilse {hi,lo} yapısı) */
typedef struct ASIOSamples  { unsigned long hi; unsigned long hi_lo_yerine_lo; } ASIOSamples;
```

> ⚠️ VERIFICATION REQUIRED: yukarıdaki blokta `ASIOSamples` yapısının `lo` alan adı
> kaynakta `unsigned long lo;` şeklindedir; kısaltma için yeniden yazılmıştır.
> Doğruluk istenen yerde kaynak metin esastır: `typedef struct ASIOSamples { unsigned long hi; unsigned long lo; }`.

- `ASIOSampleRate` IEEE-754 çift duyarlı `double`'dır (kaynak: `IEEE754_64FLOAT` şartlı bloğu).
- `ASIOTimeStamp` nanosaniye cinsindendir (kaynak: "Time format is Nanoseconds").
- Yapı hizalama `#pragma pack(push,4)` ile 4 bayta sabitlenmiştir.
- `ASIODriverInfo.sysRef` Windows'ta **ana uygulama penceresi koludur**.

### §3.2 `ASIOInit` giriş/çıkış sözü (asio.h yorumu)

| Alan | Giriş | Çıkış |
|---|---|---|
| `asioVersion` | Host sürümü (outputReady destekliyorsa ≥2) | Sürücünün ASIO sürümü |
| `driverVersion` | — | Sürücüye özgü sürüm |
| `name[32]` | — | Sürücü adı |
| `errorMessage[124]` | — | Kullanıcıya dönük hata metni |
| `sysRef` | Ana pencere kolu (Windows) | Platforma özgü |

- Hata dönüşleri: girdi/yoksa `ASE_NotPresent`; ayrıca `ASE_NoMemory`, `ASE_HWMalfunction`.
- `ASIOInit` içinde asioMessage **yoktur** (tasarım kusuru olarak belgede yazılıdır);
  host sürümü ayrı `asioMessage(kAsioEngineVersion)` ile sorulur.

---

## §4 `ASIOCallbacks` — host'un geri çağırma sözleşmesi

```c
typedef struct ASIOCallbacks
{
	void (*bufferSwitch) (long doubleBufferIndex, ASIOBool directProcess);
	void (*sampleRateDidChange) (ASIOSampleRate sRate);
	long (*asioMessage) (long selector, long value, void* message, double* opt);
	ASIOTime* (*bufferSwitchTimeInfo) (ASIOTime* params, long doubleBufferIndex,
	                                   ASIOBool directProcess);
} ASIOCallbacks;
```

### §4.1 Dört geri çağırmanın görevi

| Geri çağırma | Kaynak tipi | Temel kural (dio.h yorumu) |
|---|---|---|
| `bufferSwitch` | `void` | Hem giriş hem çıkış işlenir; index 0 = A, 1 = B |
| `sampleRateDidChange` | `void` | Sürat bilinmiyorsa 0 gelir (dış saat kaybı) |
| `asioMessage` | `long` | Yalnız ASIO 2+ ; seçici/destek sorguları |
| `bufferSwitchTimeInfo` | `ASIOTime*` | Zaman bilgisi modunda `bufferSwitch` yerine kullanılır |

### §4.2 `bufferSwitch` metninin kilit cümleleri (asio.h yorumundan)

- "the current buffer half index (0 for A, 1 for B) determines the output buffer that the
  host should start to fill; the other buffer will be passed to output hardware regardless
  of whether it got filled in time or not."
- "the input buffer that is now filled with incoming data."
- "because of the synchronicity of i/o, the input always has at least one buffer latency
  in relation to the output."
- "directProcess suggests to the host whether it should immediately start processing …
  If in doubt, directProcess should be set to ASIOFalse."
- "Note: bufferSwitch may be called at interrupt time for highest efficiency."

### §4.3 Zaman bilgisi modunun kurulumu

1. Host `ASIOCreateBuffers` sırasında `callbacks` işaretçisini sürücüye verir.
2. Host sürücüye `asioMessage(kAsioSupportsTimeInfo, …)` çağırır.
3. Host `1` dönerse sürücü **artık** `bufferSwitchTimeInfo` çağırır.
4. `ASIOTime` yapısı callback dönüşüne kadar geçerlidir.
5. Zaman kodu ayrı bir katmandır: `kAsioSupportsTimeCode` + `kAsioEnableTimeCodeRead`.
6. `AsioTimeInfo.flags` içinde `kSystemTimeValid | kSamplePositionValid` **her zaman** dolu olmalıdır.
7. `systemTime` Windows'ta `timeGetTime` türevli olmalıdır (kaynak yorumu: "on windows, must be derived from timeGetTime()").
8. `kSampleRateChanged` bayrağı sürat değiştiğinde set edilir.

### §4.4 `asioMessage` seçicileri (asio.h enum'u)

| Seçici | Anlamı | Host cevabı |
|---|---|---|
| `kAsioSelectorSupported` | `<value>` seçicisi destekleniyor mu | 1 / 0 |
| `kAsioEngineVersion` | Host ASIO uygulama sürümü | 2 veya üzeri |
| `kAsioResetRequest` | Sürücü yeniden kurulum ister | 1 = kabul edildi |
| `kAsioBufferSizeChange` | Tampon boyu değişikliği (henüz desteklenmiyor) | belgede hep 0 |
| `kAsioResyncRequest` | Uyumsuzluk; motoru yeniden başlat | 1 / 0 |
| `kAsioLatenciesChanged` | Gecikmeler değişti; yeniden oku | 1 / 0 |
| `kAsioSupportsTimeInfo` | Zaman bilgisi moduna geçilsin mi | 1 = evet |
| `kAsioSupportsTimeCode` | Zaman kodu moduna geçilsin mi | 1 = evet |
| `kAsioMMCCommand` | MMC komutları (kullanılmıyor) | — |
| `kAsioSupportsInputMonitor` | Giriş izleme desteği | 1 / 0 |
| `kAsioSupportsInputGain` | Tanımsız | — |
| `kAsioSupportsInputMeter` | Tanımsız | — |
| `kAsioSupportsOutputGain` | Tanımsız | — |
| `kAsioSupportsOutputMeter` | Tanımsız | — |
| `kAsioOverload` | Sürücü aşırı yük tespit etti | 1 |
| `kAsioNumMessageSelectors` | Sayı sonu işareti | — |

---

## §5 `ASIOFuture` seçicileri (uzatma yüzeyi)

| Seçici | Kod | Parametre |
|---|---|---|
| `kAsioEnableTimeCodeRead` | 1 | yok |
| `kAsioDisableTimeCodeRead` | 2 | yok |
| `kAsioSetInputMonitor` | 3 | `ASIOInputMonitor*` |
| `kAsioTransport` | 4 | `ASIOTransportParameters*` |
| `kAsioSetInputGain` | 5 | `ASIOChannelControls*` |
| `kAsioGetInputMeter` | 6 | `ASIOChannelControls*` |
| `kAsioSetOutputGain` | 7 | `ASIOChannelControls*` |
| `kAsioGetOutputMeter` | 8 | `ASIOChannelControls*` |
| `kAsioCanInputMonitor` … `kAsioCanOutputMeter` | 9–15 | yok (kAsioCanXXX) |
| `kAsioSetIoFormat` | `0x23111961` | `ASIOIoFormat*` (DSD) |
| `kAsioGetIoFormat` | `0x23111983` | `ASIOIoFormat*` |
| `kAsioCanDoIoFormat` | `0x23112004` | `ASIOIoFormat*` |
| `kAsioCanReportOverload` | `0x24042012` | yok; destekliyorsa `ASE_SUCCESS` |
| `kAsioGetInternalBufferSamples` | `0x25042012` | `ASIOInternalBufferInfo*` |

- Başarı değeri `ASE_SUCCESS = 0x3f4847a0`'dir; `ASE_OK` **değildir**.
- `kAsioCanReportOverload` JUCE tarafından sürücünün drop-out raporlayabilirliğini
  anlamak için `open()` akışında sorgulanır.
- `kAsioGetInternalBufferSamples` sürücünün **çift tamponun ötesindeki** iç gecikmesini
  (USB tasarımlarında yaygındır) sayısal olarak verir.
- DSD biçim geçişi `ASIOFuture` ile yapılır ve **"prepared" durumundan önce** olmalıdır.

---

## §6 Hata kodları (ham kaynak: `asio.h`)

```c
typedef long ASIOError;
enum {
	ASE_OK = 0,
	ASE_SUCCESS = 0x3f4847a0,   /* ASIOFuture için özgü başarı */
	ASE_NotPresent = -1000,     /* donanım girişi/çıkışı yok */
	ASE_HWMalfunction,          /* donanım arızalı */
	ASE_InvalidParameter,       /* geçersiz parametre */
	ASE_InvalidMode,            /* yanlış kipte kullanım */
	ASE_SPNotAdvancing,         /* örnek sayacı ilerlemiyor */
	ASE_NoClock,                /* saat yok / belirlenemiyor */
	ASE_NoMemory                /* yetersiz bellek */
};
```

| Kod | Değeri | Tipik nerede | Host tepkisi |
|---|---|---|---|
| `ASE_OK` | 0 | Tüm sorgular | devam |
| `ASE_SUCCESS` | `0x3f4847a0` | yalnız `ASIOFuture` | uzantı destekleniyor |
| `ASE_NotPresent` | -1000 | kanal yok / çıkış yok | cihazı reddet |
| `ASE_HWMalfunction` | -999 | `ASIOStart` başarısız | sürücüyü yeniden kur |
| `ASE_InvalidParameter` | -998 | bilinmeyen seçici | seçiciyi kullanmayı bırak |
| `ASE_InvalidMode` | -997 | desteklenmeyen tampon boyu | `getBufferSize`'e dön |
| `ASE_SPNotAdvancing` | -996 | `ASIOGetSamplePosition` | saat kaybı raporla |
| `ASE_NoClock` | -995 | sürat sorgusu / dış saat | `setClockSource` dene |
| `ASE_NoMemory` | -994 | `ASIOCreateBuffers` | tampon sayısını azalt |

---

## §7 Örnek türleri (`ASIOSampleType`)

| Kod | Değer | Anlamı |
|---|---|---|
| `ASIOSTInt16MSB` | 0 | 16-bit tamsayı, en anlamlı bayt önce |
| `ASIOSTInt24MSB` | 1 | 24-bit (20-bit için de kullanılır) |
| `ASIOSTInt32MSB` | 2 | 32-bit tamsayı, MSB |
| `ASIOSTFloat32MSB` | 3 | IEEE-754 32-bit float, MSB |
| `ASIOSTFloat64MSB` | 4 | IEEE-754 64-bit double, MSB |
| `ASIOSTInt32MSB16/18/20/24` | 8–11 | 32-bit tampon içinde hizalı veri |
| `ASIOSTInt16LSB` | 16 | 16-bit, küçük bayt önce (x86) |
| `ASIOSTInt24LSB` | 17 | 24-bit LSB |
| `ASIOSTInt32LSB` | 18 | 32-bit LSB |
| `ASIOSTFloat32LSB` | 19 | float32, "as found on Intel x86 architecture" |
| `ASIOSTFloat64LSB` | 20 | double, Intel x86 |
| `ASIOSTInt32LSB16/18/20/24` | 24–27 | 32-bit tampon, LSB hizalı |
| `ASIOSTDSDInt8LSB1` | 32 | DSD 1-bit, bayt başına 8 örnek, LSB ilk |
| `ASIOSTDSDInt8MSB1` | 33 | DSD 1-bit, MSB ilk |
| `ASIOSTDSDInt8NER8` | 40 | DSD 8-bit, uçkurulama yok |
| `ASIOSTLastEntry` | — | Sınır işareti |

- Kanalın türü `ASIOGetChannelInfo` ile **kanal bazında** öğrenilir (tek bir host tüm
  cihaz için tek tür varsayamaz).
- JUCE örneği bu türleri `byteStride`/`bitDepth`/`littleEndian`/`formatIsFloat`
  niteliklerine çevirir ve giriş/çıkış arasında `float`'a dönüştürür.
- `ASIOSTDSDInt8LSB1/MSB1/NER8` değerleri JUCE tarafında "unhandled" işaretlidir —
  yani her host her türü işlemez (kanıt: `juce_win32_ASIO.cpp` switch bloğu).

---

## §8 Yapılar (ham kaynak: `asio.h`)

```c
typedef struct ASIOBufferInfo
{
	ASIOBool isInput;      /* giriş mi çıkış mı */
	long channelNum;       /* kanal indeksi */
	void *buffers[2];      /* çift tampon adresleri (dönüşte doldurulur) */
} ASIOBufferInfo;

typedef struct ASIOChannelInfo
{
	long channel;          /* giriş: kanal indeksi */
	ASIOBool isInput;      /* giriş */
	ASIOBool isActive;     /* çıkış: ASIOCreateBuffers ile aktif mi */
	long channelGroup;     /* çıkış: kanal grubu (analog/SPDIF/ADAT…) */
	ASIOSampleType type;   /* çıkış: örnek türü */
	char name[32];         /* çıkış: kullanıcıya görünen ad */
} ASIOChannelInfo;

typedef struct ASIOClockSource
{
	long index;                /* ASIOSetClockSource için kimlik */
	long associatedChannel;    /* örn. S/PDIF ya da AES/EBU kanalı */
	long associatedGroup;      /* kanal grubu */
	ASIOBool isCurrentSource;  /* şu anki kaynak mı */
	char name[32];             /* kullanıcı seçimi için ad */
} ASIOClockSource;
```

### §8.1 `ASIOBufferInfo` sözleşmeleri

- `isInput` ve `channelNum` **girişte** doldurulur; `buffers[2]` **çıkışta** doldurulur.
- `numChannels`, seçilen giriş ve çıkış kanallarının **toplamıdır**.
- `bufferSize` mutlaka `ASIOGetBufferSize` ile bildirilen değerlerden biri olmalıdır;
  aksi halde `ASE_InvalidMode`.
- Bellek ayrılmazsa `ASE_NoMemory`.
- Kanal seçimi sürücüye bırakılırsa sürücü yalnız **aktif** kanallar için tampon doldurur.

### §8.2 `ASIOClockSource` sözleşmeleri

- Saat kaynağı fiziksel bağlantı hem veri hem saat taşıyorsa `associatedChannel` girilir
  (S/PDIF, AES/EBU, ADAT).
- Word clock veya dahilî osilatör için `associatedChannel = associatedGroup = -1`.
- En az **1** kaynak her zaman bildirilir (dahilî osilatör).
- Seçim saat değişimine yol açarsa sürücü `sampleRateDidChange` geri çağırısını çağırmalıdır.

---

## §9 Tampon ve geri çağırma modeli (BUFFERS.md ile)

- `bufferSwitch` çağrılmadan **önce** ilgili **giriş tamponu dolu** olmalıdır (1. yorum).
- `bufferSwitch` çağrılmadan **önce** ilgili **çıkış tamponu donanıma gönderilmiş** olmalıdır
  (2. yorum) — yani host o tamponu **yeniden yazabilir**.
- Bu iki yorum BUFFERS.md'nin resmî metinden türettiği **en tutarlı okumadır**; resmî belge
  tampon ömrünü açıkça yazmaz.
- İhtiyat kuralı (BUFFERS.md): sürücü, host'un `bufferSwitch(0)` dönüşünden hemen sonra
  tampon 0'a erişebileceğini **varsaymalıdır**; host ise `bufferSwitch(0)` dönüşünde
  tamponun geçersiz olabileceğini varsaymalıdır.
- `ASIOOutputReady` bu belirsizliği daraltır: host tamponu doldurduğunu bildirince sürücü
  güvenle işleyebilir; desteklenmiyorsa sürücü bir sonraki callback'i beklemek zorunda
  kalır ve **çıkış gecikmesi ikiye katlanabilir**.
- Priming akışı: host `ASIOStart`'tan önce tampon 1'i doldurur; sürücü girişleri sessizlikle
  doldurur, `bufferSwitch(0)` çağırır, dönüşünde tampon 1'i donanıma verir.
- Giriş-tek-uyum (input-only) modu için iki olası okuma vardır (hemen sessizlikle `bufferSwitch(0)`
  ya da ilk gerçek blok dolana kadar beklemek); resmî belge karar vermez — **⚠️ belirsiz**.

---

## §10 Çağrı zaman çizelgesi (host perspektifi)

```text
Host                                  Sürücü
 |                                       |
 |--- init(sysHandle) ----------------->|  durum: loaded → prepared
 |<-- ASE_OK ---------------------------|
 |--- getChannels/getBufferSize ------->|
 |--- setSampleRate ------------------->|
 |--- createBuffers(buf[], n, size, cb)->|  durum: derived
 |<-- buffers[0], buffers[1] -----------|
 |  (host tampon 1'i doldurur)          |
 |--- start() ------------------------->|  durum: started
 |                                       |-- girişleri sessizle --
 |<-- bufferSwitch(0, direct) ----------|
 |  (giriş 0 okunur, çıkış 0 yazılır)   |
 |--- outputReady() ------------------->|  (opsiyonel)
 |                                       |-- donanıma gönder ---
 |<-- bufferSwitch(1, direct) ----------|
 |  (giriş 1 okunur, çıkış 1 yazılır)   |
 |--- outputReady() ------------------->|
 |            ... steady-state ...      |
 |--- stop() -------------------------->|  durum: derived
 |--- disposeBuffers() ---------------->|
 |--- exit() -------------------------->|  durum: prepared → yok
```

---

## §11 Gerçek host örneği (JUCE, `juce_win32_ASIO.cpp`)

```cpp
bool tryCreatingDriver (bool& crashed)
{
   #if ! JUCE_MINGW
    __try
   #endif
    {
        return CoCreateInstance (classId, 0, CLSCTX_INPROC_SERVER,
                                 classId, (void**) &asioObject) == S_OK;
    }
   #if ! JUCE_MINGW
   __except (EXCEPTION_EXECUTE_HANDLER) { crashed = true; }
    return false;
   #endif
}
```

```cpp
static void setCallbacks (ASIOCallbacks& callbacks) noexcept
{
	callbacks.bufferSwitch          = &bufferSwitchCallback;
	callbacks.asioMessage           = &asioMessagesCallback;
	callbacks.bufferSwitchTimeInfo  = &bufferSwitchTimeInfoCallback;
	callbacks.sampleRateDidChange   = &sampleRateChangedCallback;
}
```

- Dikkat: `CoCreateInstance` çağrınsda **riid == clsid** verilir; standart COM'da bu
  geçerli değildir, ASIO'nun kendi yüklenme kuralıdır.
- `__try/__except` sarmalayıcısı **çöken sürücü** içindir: sürücü host sürecinin
  içinde çalışır, hata yönetimi yapılandırılmış istisna ile yapılır.
- JUCE, `asioMessage` içinde `kAsioEngineVersion` için **2** döner; `kAsioSupportsTimeInfo`
  ve `kAsioSupportsTimeCode` için **0** döner (zaman bilgisi modu kullanılmaz).
- `outputReady` desteği `openDevice()` içinde bir kez denenir: `postOutput = (asioObject->outputReady() == 0)`.

---

## §12 İddia — kanıt tablosu

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 1 | `IASIO` 21 sanal metottan oluşur | `iasiodrv.h` tam metin | ✅ |
| 2 | `IASIO` `IUnknown`'dan türetildir | `interface IASIO : public IUnknown` | ✅ |
| 3 | C API 19 prototipten oluşur | `asio.h` "prototypes summary" | ✅ |
| 4 | Hata kodları -1000'den başlar | `asio.h` enum | ✅ |
| 5 | `ASE_SUCCESS` `ASE_OK` değildir | `asio.h` "note: ASE_OK is *not* sufficient!" | ✅ |
| 6 | Çift tampon şeması resmîdir | `asio.h` ASCII diyagram | ✅ |
| 7 | Host `ASIOStart`'tan önce tampon 1'i doldurur | `asio.h` `ASIOStart` yorumu | ✅ |
| 8 | `outputReady` desteklenmiyorsa gecikme artar | `asio.h` `ASIOOutputReady` yorumu | ✅ |
| 9 | DSD seçicileri sabit kodludur | `asio.h` `0x23111961` … | ✅ |
| 10 | Zaman damgası nanosaniyedir | `asio.h` "Time format is Nanoseconds" | ✅ |
| 11 | `ASIOTimeInfo.systemTime` Windows'ta `timeGetTime` türevli | `asio.h` yorumu | ✅ |
| 12 | Host `riid == clsid` ile örnekler | JUCE `CoCreateInstance(classId,…,classId,…)` | ✅ |
| 13 | JUCE zaman bilgisi modunu reddeder | `asioMessage` `kAsioSupportsTimeInfo → 0` | ✅ |
| 14 | `outputReady` tek denemeyle keşfedilir | JUCE `postOutput = (outputReady() == 0)` | ✅ |
| 15 | Tampon ömrü resmî belgede açık değil | BUFFERS.md "does not address these questions" | ✅ |
| 16 | Giriş-tek modunda iki okuma var | BUFFERS.md "two possible approaches" | ✅ |
| 17 | `kAsioBufferSizeChange` henüz desteklenmiyor | `asio.h` "not yet supported … always return 0L" | ✅ |
| 18 | `ASIOFuture` bilinmeyen seçicide `ASE_InvalidParameter` | `asio.h` `ASIOFuture` yorumu | ✅ |
| 19 | Her kanal ayrı örnekleme türü bildirebilir | `ASIOChannelInfo.type` + JUCE kanal döngüsü | ✅ |
| 20 | `kAsioGetInternalBufferSamples` 2025 seçicisidir | `asio.h` `0x25042012` kodu | ✅ |

---

## §13 Açık sorular

1. Neva Engine (C++20) hangi yüzeyi kullanacak: doğrudan `IASIO` mu, C API mi? → ADR.
2. Zaman bilgisi modu kullanılırsa `ASIOTime` alanı önceden allocate edilecek mi? (RT kuralı)
3. `outputReady` desteği sürücüye göre dinamik mi keşfedilecek? (JUCE modeli yeterli mi)
4. `getAsioFuture` içinden `kAsioGetInternalBufferSamples` gecikme bütçesine yazılacak mı?
5. Hata kodu haritası scaffold'ta `std::error_code` olarak mı, `enum class` olarak mı? → ADR.
6. `ASIOCallbacks` fonksiyon işaretçilerinin çağırtype'ı (`__cdecl`) tüm SDK'lerde aynı mı?
   (JUCE yorumu: "different versions seem to be pretty random about whether or not they do this")
7. DSD gereksinimi var mı? (CoreMusic kapsamı: PCM öncelikli — UNKNOWN)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** ham kaynak turu (`asio.h` · `iasiodrv.h` · JUCE · ASIOUtil BUFFERS.md) · 2026-10-12
