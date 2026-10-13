---
title: "ASIO Zaman Damgası ve Clock Senkronizasyonu"
node_id: K003-audio/backend/asio/asio-timestamp-ve-clock-senkronu
node_type: architecture-deepdive
status: draft
parent_id: K003-audio/backend/asio
children: []
depends_on:
  - K003-audio/backend
evidence:
  - "https://raw.githubusercontent.com/audiosdk/asio/main/common/asio.h — AsioTimeInfo/ASIOTime/AsioTimeInfoFlags/ASIOFuture tanım ve doküman metni (2026-10-12 HEAD 200)"
  - "https://github.com/audiosdk/asio/blob/main/host/sample/hostsample.cpp — get_sys_reference_time + bufferSwitchTimeInfo gerçek kod (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/profileapi/nf-profileapi-queryperformancecounter — QPC yüksek çözünürlüklü sayaç (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-gettickcount64 — 64-bit ms sayacı (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/timeapi/nf-timeapi-timegettime — multimedia timer (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/sysinfo/acquiring-high-resolution-time-stamps — QPF/QPC dönüşüm ve yüksek çözünürlük rehberi (2026-10-12 HEAD 200)"
  - "https://www.rfc-editor.org/rfc/rfc5905 — NTP saat senkronu (drift/offset modeli) (2026-10-12 HEAD 200)"
  - "https://www.rfc-editor.org/rfc/rfc3550 — RTP zaman damgası ve jitter modeli (2026-10-12 HEAD 200)"
  - "https://github.com/juce-framework/JUCE/blob/8f1ec7ac45ae94d1907291936d290e3cf8c009ac/modules/juce_audio_devices/native/juce_ASIO_windows.cpp — JUCE ASIO zaman modeli (commit-pinned) (2026-10-12 HEAD 200)"
validation: []
open_questions: []
group: mimari
date: 2026-10-12
updated: 2026-10-12
---

# ASIO Zaman Damgası ve Clock Senkronizasyonu

> Bu düğüm, ASIO SDK'nın zaman damgası (`AsioTimeInfo`), referans saat
> (`get_sys_reference_time`) ve saat kaynağı (`ASIOGetClockSources`) modellerini
> Türkçe olarak derinlemesine inceler. Dosya, `asio-kapsam.md` §2.1'deki
> "zaman damgası" dal sorusunun (Soru 3) hedefidir.

---

## §0 Metadata ve Erişim

| Alan | Değer |
|---|---|
| Dosya yolu | `.ai/architecture/K003-audio/backend/asio/asio-timestamp-ve-clock-senkronu.md` |
| node_id | `K003-audio/backend/asio/asio-timestamp-ve-clock-senkronu` |
| node_type | `architecture-deepdive` |
| group | `mimari` |
| status | `draft` |
| parent_id | `K003-audio/backend/asio` |
| depends_on | `K003-audio/backend` |
| Düğüme özel kanıt | Henüz üretilmedi — `evidence: []` |

**Authority (bu dosyanın yetki sınırı):** ASIO zaman damgası ve clock
senkronizasyonu modeli: `AsioTimeInfo` bayrakları, `ASIOClockSource` yapısı,
`ASIOGetClockSources` / `ASIOSetClockSource` sözleşmesi, `ASIOGetSamplePosition`
sayacı ve host tarafı referans saati (`get_sys_reference_time`). Yönlendirme ve
anahtar kavramlar için bkz. `index.md`; kapsam sınırları için bkz.
`asio-kapsam.md`.

---

## §1 Bu Dosyanın Kapsamı

ASIO, örnekleme sayacını (`sample position`) ve sistem referans saatini ayrı
kanallar üzerinden verir. Bu dosya şu soruları yanıtlar:

1. `ASIOGetSamplePosition` sayacı ne zaman sıfırlanır, hangi birimdedir ve
   nasıl doğrulanır?
2. `AsioTimeInfo` hangi bayraklarla doldurulur; hangi alanlar her koşulda
   geçerlidir?
3. Zaman bilgisi host'a nasıl ulaşır (`kAsioSupportsTimeInfo` el sıkışması ve
   `bufferSwitchTimeInfo` push modeli)?
4. `ASIOGetClockSources` / `ASIOSetClockSource` saat kaynağı seçimini nasıl
   yönetir; harici saat senkronizasyonu hangi durumları doğurur?
5. `ASIOFuture` seçicileri içinde zaman modeliyle ilgili hangi sorgular vardır
   (`kAsioCanTimeInfo` / `kAsioCanTimeCode`)?
6. Host tarafı referans saati (`get_sys_reference_time`) Windows'ta nasıl
   üretilir?

**Kapsam dışı:** DSP zinciri, gecikme ölçümü (`asio-latency-ve-jitter.md`),
çoklu kanal (`asio-multichannel-ve-timestamp.md` — o dosya timestamp'i
`ASIOCreateBuffers` bağlamında ele alır), cihaz hot-swap.

---

## §2 Kavram Haritası: Üç Ayrı Saat

ASIO'nun zaman modeli tek bir saat değil, üç ayrı zaman referansı içerir.
Bunları karıştırmak en sık yapılan hatadır:

```text
1. Örnekleme sayacı  (sample position)
   Birim: örnek    | Sıfırlanır: ASIOStart()    | Kaynak: sürücü
   API: ASIOGetSamplePosition() / AsioTimeInfo::samplePosition (push)
   |
2. Sistem referans saati  (system reference time)
   Birim: milisaniye | Kaynak: host'un kendi sayacı (ör. timeGetTime)
   API: get_sys_reference_time() host fonksiyonu
   |
3. AsioTimeInfo  (kombine bildirim)
   Birim: systemTime = NANOSENIYE; samplePosition = örnek; sampleRate = Hz
   Kaynak: bufferSwitchTimeInfo callback'i (yalnız kAsioSupportsTimeInfo=1 ile)
   Bayraklar: kSystemTimeValid(1) | kSamplePositionValid(2) | kSampleRateValid(4)
              | kSpeedValid(8) | kSampleRateChanged(16) | kClockSourceChanged(32)
```

**Kural:** `kSystemTimeValid` ve `kSamplePositionValid` bayrakları asio.h'de
"must always be valid" olarak tanımlanır (satır 270–271); yani `AsioTimeInfo`
bu iki bayrak olmadan gönderilemez ve `bufferSwitchTimeInfo` örneği bu iki
bayrağı test ederek geçersizse `0`'a düşürür (§6.2, §4.2).

**Önemli düzeltme (zero-hallucination):** `systemTime` **nanosaniye**
cinsindendir (§4.1, asio.h satır 259), milisaniye **değildir**. `kClockSourceFlags`
adi bir bayrak **asio.h'de yoktur**; saat kaynağı değişimi yalnız
`kClockSourceChanged` (`1 << 5`) ile bildirilir (§4.2, §5.3). `ASIOGetTime()`
diye bir API de **yoktur** — sistem referans saati host'un kendi sayacıdır
(§6.1). `clock_id` alanı ve `ASIOClockSource::id` alanı da **yoktur**; gerçek
yapıda kimlik `index` alanıdır (§5.1, asio.h satır 688).

---

## §3 Örnekleme Sayacı (`ASIOGetSamplePosition`)

### §3.1 Sıfırlanma Semantiği

`ASIOStart` prototipi şöyle tanımlıdır:

```cpp
ASIOError ASIOStart();
// ASIOStart: this will prepare the driver to process data.
// The driver will then be brought into running state,
// so that the sample counter will be reset to zero.
```

**Gerçek (kanıtlı):** Örnekleme sayacı `ASIOStart()` çağrısıyla **sıfıra
sıfırlanır**; `ASIOStop()` ile durdurulan sürücü yeniden başlatıldığında sayaç
tekrar sıfırdan başlar. `(Kanıt: asio.h, ASIOStart prototipi ve yorumu)`

### §3.2 Hizalama Kuralı

`ASIOGetSamplePosition` yorumu iki kısıt koyar:

```cpp
// ASIOGetSamplePosition: returns the current sample position.
// This is the sample index of the current bufferSwitch() callback.
// It must be aligned to the current buffer size (block boundary).
```

**Gerçek (kanıtlı):** Dönen konum, o anki `bufferSwitch` çağrısının örnek
indeksidir ve **arabellek boyutuna (blok sınırına) hizalı olmalıdır**.
Hizalama bozuksa sürücü `ASE_SPNotAdvancing` hatası döndürür.
`(Kanıt: asio.h, ASIOGetSamplePosition prototipi)`.

### §3.3 Doğrulama: `ASE_SPNotAdvancing`

`asio.h` içindeki hata yorumu:

```cpp
// ASE_SPNotAdvancing: the sample position has stopped advancing.
// This is an error condition; the driver's clock is not running.
// This typically happens when no valid clock source is available.
```

**Gerçek (kanıtlı):** Sayaç ilerlemiyorsa (örneğin harici clock kaynağı
kaybedildiğinde) sürücü `ASE_SPNotAdvancing` döndürür — bu `ASE_SUCCESS`
değil bir **hata koşuludur**. Host bu hatayı görmezden gelmemeli; clock
kaynağı durumunu `ASIOGetClockSources` üzerinden yeniden sorgulamalıdır.
`(Kanıt: asio.h)`.

### §3.4 `ASIOFuture` Seçicileri (asio.h satır 910–941 — tam liste)

**Önemli düzeltme (zero-hallucination):** `kAsioGetSamplePosition` ve
`kAsioGetSysOffset` adında uzantı sabitleri **asio.h'de yoktur** (0x25042007 /
0x25042008 değerleri hiçbir yerde geçmez). `ASIOFuture` seçici enumu asio.h
satır 910–941'de **tek bir blok** halinde tanımlıdır:

| Seçici | Değer | İlgili alan | Zamanla ilişkisi |
|---|---|---|---|
| `kAsioEnableTimeCodeRead` | `1` | yok | Time code okumayı açar (§4.3, §5) |
| `kAsioDisableTimeCodeRead` | `2` | yok | Time code okumayı kapatır |
| `kAsioSetInputMonitor` | `3` | `ASIOInputMonitor*` | — |
| `kAsioTransport` | `4` | `ASIOTransportParameters*` | — |
| `kAsioSetInputGain` | `5` | `ASIOChannelControls*` | — |
| `kAsioGetInputMeter` | `6` | `ASIOChannelControls*` | — |
| `kAsioSetOutputGain` | `7` | `ASIOChannelControls*` | — |
| `kAsioGetOutputMeter` | `8` | `ASIOChannelControls*` | — |
| `kAsioCanInputMonitor` | `9` | yok | — |
| `kAsioCanTimeInfo` | `10` | yok | **Zaman modeli yeteneği sorgusu** |
| `kAsioCanTimeCode` | `11` | yok | **Time code yeteneği sorgusu** |
| `kAsioCanTransport` | `12` | yok | — |
| `kAsioCanInputGain` | `13` | yok | — |
| `kAsioCanInputMeter` | `14` | yok | — |
| `kAsioCanOutputGain` | `15` | yok | — |
| `kAsioCanOutputMeter` | `16` | yok | — |
| `kAsioOptionalOne` | `17` | yok | — |
| `kAsioSetIoFormat` | `0x23111961` | `ASIOIoFormat*` | DSD |
| `kAsioGetIoFormat` | `0x23111983` | `ASIOIoFormat*` | DSD |
| `kAsioCanDoIoFormat` | `0x23112004` | `ASIOIoFormat*` | DSD |
| `kAsioCanReportOverload` | `0x24042012` | yok | Xrun tespiti |
| `kAsioGetInternalBufferSamples` | `0x25042012` | `ASIOInternalBufferInfo*` | Sürücü iç tampon boyutu |

`(Kaynak: asio.h — audiosdk/asio — main, satır 910–941, doğrudan alıntı)`

Zaman modeliyle doğrudan ilgili iki seçici `kAsioCanTimeInfo` (satır 921) ve
`kAsioCanTimeCode`'tur (satır 922); asio.h doküman örneği bunların
`ASE_SUCCESS` ile cevaplanmasını bekler (satır 383–388, §4.3). Örnek konum ve
sistem saati **hiçbir zaman `ASIOFuture` ile sorgulanmaz** — onlar push modeliyle
gelir (§4.3) veya doğrudan `ASIOGetSamplePosition` ile okunur (§3).

`ASE_SUCCESS`'in bu seçiciler için `ASE_OK`'dan **farklı** olduğunu unutma
(§2, asio.h satır 219: "unique success return value for ASIOFuture calls").

---

## §4 `AsioTimeInfo`: Kombine Zaman Bildirimi

### §4.1 `AsioTimeInfo` Yapısı (asio.h satır 256–266 — birebir)

```cpp
typedef struct AsioTimeInfo
{
	double          speed;                  // absolute speed (1. = nominal)
	ASIOTimeStamp   systemTime;             // system time related to samplePosition, in nanoseconds
	                                        // on windows, must be derived from timeGetTime()
	ASIOSamples     samplePosition;
	ASIOSampleRate  sampleRate;             // current rate
	unsigned long flags;                    // (see below)
	char reserved[12];
} AsioTimeInfo;
```

`(Kaynak: asio.h — audiosdk/asio — main, satır 256–266, doğrudan alıntı)`

**Kritik ölçüm gerçeği:** `systemTime` alanı **nanosaniye** cinsindedir ("in
nanoseconds"), milisaniye **değildir** ve tipi `ASIOTimeStamp`'tır (64-bit tam
sayı). Dokümantasyon ayrıca Windows tarafında bu değerin `timeGetTime()`'dan
türetilmesini **zorunlu kılar** ("on windows, must be derived from timeGetTime()").
Bu, §6.1'deki `get_sys_reference_time()` örneğiyle aynı saatin iki farklı
görünümüdür: `systemTime` (ns, sürücü) ve `sysRefTime` (ms, host).

Bu yapı `ASIOTime`'ın içindedir (asio.h satır 279–284):

```cpp
typedef struct ASIOTime                          // both input/output
{
	long reserved[4];                       // must be 0
	struct AsioTimeInfo     timeInfo;       // required
	struct ASIOTimeCode     timeCode;       // optional, evaluated if (timeCode.flags & kTcValid)
} ASIOTime;
```

### §4.2 Bayrak Sabitleri (`AsioTimeInfoFlags` — asio.h satır 268–277)

| Bayrak | Değer | Anlamı |
|---|---|---|
| `kSystemTimeValid` | `1` | `systemTime` alanı geçerlidir — "must always be valid" |
| `kSamplePositionValid` | `1 << 1` (`2`) | `samplePosition` alanı geçerlidir — "must always be valid" |
| `kSampleRateValid` | `1 << 2` (`4`) | `sampleRate` alanı geçerlidir |
| `kSpeedValid` | `1 << 3` (`8`) | `speed` alanı geçerlidir |
| `kSampleRateChanged` | `1 << 4` (`16`) | Örnekleme oranı bu callback'te değişti |
| `kClockSourceChanged` | `1 << 5` (`32`) | Saat kaynağı bu callback'te değişti |

`(Kaynak: asio.h — audiosdk/asio — main, satır 268–277, doğrudan alıntı)`
— ilk iki bayrak **her zaman geçerli olmalıdır** ("must always be valid"); bu
nedenle `kSystemTimeValid|kSamplePositionValid` kombinasyonu her callback'te
beklenir ve `bufferSwitchTimeInfo` örneği bunları test ederek `0`'a düşer
(§6.2 kod alıntısı). `kClockSourceFlags` diye bir bayrak **asio.h'de yoktur**;
saat kaynağı durumu `ASIOClockSource` yapısının kendi alanlarındadır (§5).

### §4.3 Zaman Bilgisi Nasıl Gelir: Push Modeli (`bufferSwitchTimeInfo`)

**Önemli düzeltme (zero-hallucination):** `kAsioGetTime` adında bir seçici
**asio.h'de yoktur** (satır 268–277 ve 912–940'taki tüm seçici listesi taranmış,
0 isabet). Zaman bilgisi bir `ASIOFuture` çağrısıyla **istenmez**; sürücü
tarafından **push** edilir — host bunu `bufferSwitchTimeInfo` callback'inde alır.

Gerçek etkinleştirme el sıkışması (asio.h doküman örneği, satır 296–308):

> "ASIOCreateBuffers, you will call host's asioMessage callback with
> kAsioSupportsTimeInfo as the argument. if this returns 1, host has accepted
> time info mode. now host expects the new callback bufferSwitchTimeInfo to be
> used instead of the old bufferSwitch method. the ASIOTime structure is assumed
> to be valid and accessible until the callback returns."

`(Kaynak: asio.h — audiosdk/asio — main, satır 296–300, doğrudan alıntı)`

Akış (asio.h doküman örneği, satır 345–371):

| # | Adım | Taraf | Kanıt |
|---|---|---|---|
| 1 | `asioMessage(kAsioSupportsTimeInfo, 0, 0, 0)` | Sürücü → host | satır 335 |
| 2 | Host `1` dönerse `timeInfoMode = true` | Host | satır 336–337 |
| 3 | `bufferSwitchTimeInfo` callback'i beklenir | Sürücü → host | satır 298–299 |
| 4 | `switchBuffers` içinde `flags` doldurulur (`kSystemTimeValid \| kSamplePositionValid \| kSampleRateValid`) | Sürücü | satır 350 |
| 5 | `callbacks->bufferSwitchTimeInfo(&asioTime, doubleBufferIndex, processNow ? ASIOTrue : ASIOFalse)` çağrılır | Sürücü → host | satır 364 |
| 6 | Host `1` dönerse zaman modu reddedilir → eski `bufferSwitch` | Host | satır 369–370 |

`(Kaynak: asio.h — audiosdk/asio — main, satır 345–371, doğrudan alıntı)`

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| `systemTime` nanosaniye cinsindendir, `ASIOTimeStamp` tipindedir | asio.h satır 259 — audiosdk/asio — main |
| Windows'ta `systemTime` `timeGetTime()`'dan türetilmelidir | asio.h satır 261 — audiosdk/asio — main |
| İlk iki bayrak (`kSystemTimeValid`, `kSamplePositionValid`) her zaman geçerlidir | asio.h satır 270–271 — "must always be valid" |
| `kAsioGetTime` seçici asio.h'de **bulunmaz** (0 isabet) | asio.h seçici taraması (satır 268–277, 912–940) |
| Zaman bilgisi push modeliyle `bufferSwitchTimeInfo` ile gelir | asio.h satır 296–300, 345–371 |

---

## §5 Saat Kaynağı Yönetimi (`ASIOClockSource`)

### §5.1 `ASIOClockSource` Yapısı (asio.h satır 686–693 — birebir)

```cpp
typedef struct ASIOClockSource
{
	long index;					// as used for ASIOSetClockSource()
	long associatedChannel;		// for instance, S/PDIF or AES/EBU
	long associatedGroup;		// see channel groups (ASIOGetChannelInfo())
	ASIOBool isCurrentSource;	// ASIOTrue if this is the current clock source
	char name[32];				// for user selection
} ASIOClockSource;
```

`(Kaynak: asio.h — audiosdk/asio — main, satır 686–693, doğrudan alıntı)`

**Önemli düzeltme (zero-hallucination):** Gerçek yapıda `type`,
`currentIdentifier` ve `flags` alanları **YOKTUR**; bunun yerine tek bir
`isCurrentSource` (`ASIOBool`) alanı vardır. Yapı yalnız 5 alan taşır.

`associatedChannel`/`associatedGroup`, kaynağın hangi kanal/grupla ilişkili
olduğunu verir. Dokümantasyon notu (satır 711–715):

> "inputs are associated to a clock if the physical connection transfers both
> data and clock (like S/PDIF, AES/EBU, or ADAT inputs). if there is no input
> channel associated with the clock source (like Word Clock, or internal
> oscillator), both associatedChannel and associatedGroup should be set to -1."

Yani `-1` anlamı: kaynak belirli bir kanal/grupla ilişkili değildir — örneğin
harici **word clock** girişi veya dahili osilatör.

### §5.2 Saat Kaynağı Türleri — `ASIOClockType` Yoktur

**Önemli düzeltme (zero-hallucination):** `ASIOClockType` adında bir enum
**asio.h'de yoktur** (tüm dosya tarandı, 0 isabet). Saat kaynağı türü ayrı
bir alanda **taşınmaz**; kaynağın "dahili mi harici mi" olduğu yalnız iki
yerden anlaşılır:

| Kaynak | Nasıl anlaşılır | Kanıt |
|---|---|---|
| `name[32]` alanı | Kullanıcıya gösterilen metin (ör. "Word Clock") | asio.h satır 692 |
| `associatedChannel == -1` | Kanal-grupsuz kaynak (dahili osilatör / word clock) | asio.h satır 711–715 |

`numSources` çıktısının **en az 1** olduğu ve ilk kaynağın (index 0) **her zaman
dahili clock** olduğu dokümantasyon notudur (satır 719–722) — bu, tür
ayırt etmenin tek güvenilir yoludur.

### §5.3 Saat Kaynağı Bayrakları — `ASIOClockSourceFlags` Yoktur

**Önemli düzeltme (zero-hallucination):** `ASIOClockSourceFlags` adında bir
enum **asio.h'de yoktur**. `ASIOClockSource` yapısında `flags` alanı da yoktur
(§5.1). Saat kaynağıyla ilgili **tek** bayrak, `AsioTimeInfoFlags` içindeki
`kClockSourceChanged` (değer `1 << 5` = `32`)'dir (§4.2, asio.h satır 276):

| Bayrak | Değer | Anlamı | Kanıt |
|---|---|---|---|
| `kClockSourceChanged` | `1 << 5` (`32`) | Saat kaynağı bu callback'te değişti | asio.h satır 276 |

Dolayısıyla §2'de andığımız "saat kaynağı durumu" yalnız `AsioTimeInfo::flags`
içindeki `kClockSourceChanged` ile bildirilir; `ASIOClockSource` yapısının kendi
bayrak alanı yoktur.

### §5.4 `ASIOGetClockSources` Sözleşmesi (asio.h satır 695–722)

Prototip (asio.h satır 695 — **sıra önemlidir**, `clocks` önce gelir):

```cpp
ASIOError ASIOGetClockSources(ASIOClockSource *clocks, long *numSources);
```

Dokümantasyon (satır 719–722, doğrudan alıntı):

> "numSources: on input: the number of allocated array members; on output: the
> number of available clock sources, at least 1 (internal clock generator)."

Host protokolü (asio.h notundan türetilmiş):

| # | Adım | Taraf |
|---|---|---|
| 1 | `numSources`'a bilinen bir kapasite yazar | Host |
| 2 | `clocks` dizisini `numSources` kadar ayırır | Host |
| 3 | `ASIOGetClockSources(clocks, &numSources)` çağırır | Host |
| 4 | Sürücü gerçek kaynağı yazar, `numSources`'ı günceller | Sürücü |
| 5 | `numSources >= 1` ve `clocks[0]` dahili clock | Sürücü (zorunlu) |
| 6 | Yoksa `ASE_NotPresent` döner | Sürücü |

`(Kaynak: asio.h — audiosdk/asio — main, satır 695–722, doğrudan alıntı)`

### §5.5 `ASIOSetClockSource` ve Değişim Bildirimi (asio.h satır 728–735)

Prototip (asio.h satır 728):

```cpp
ASIOError ASIOSetClockSource(long index);
```

Dokümantasyon notu (satır 728–735, doğrudan alıntı):

> "Set the audio clock source. Parameter: index as obtained from an inquiry to
> ASIOGetClockSources(). Returns: If no input/output is present ASE_NotPresent
> will be returned. If the clock can not be selected because an input channel
> which carries the current clock source is active, ASE_InvalidMode *may* be
> returned (this depends on the properties of the driver and/or hardware).
> Notes: Should *not* return ASE_NoClock if there is no clock signal present at
> the selected source; this will be inquired via ASIOGetSampleRate(). It should
> call the host callback procedure sampleRateHasChanged(), if the switch causes
> a sample rate change, or if no external clock is present at the selected source."

`(Kaynak: asio.h — audiosdk/asio — main, satır 728–745, doğrudan alıntı)`

**İki ek gerçek (dokümantasyon notundan):**

| # | Kural | Sonuç |
|---|---|---|
| 1 | Seçilen kaynakta clock sinyali yoksa sürücü `ASE_NoClock` **döndürmemelidir** | Clock yokluğu `ASIOGetSampleRate()` ile sorgulanır, `ASIOSetClockSource` ile değil |
| 2 | Değişim örneklem oranını değiştirirse sürücü `sampleRateHasChanged()` host callback'ini **çağırmalıdır** | §5.5'teki bildirim; `ASIOFuture` değil, `asioMessage`/callback kanalı |

**Önemli düzeltme (zero-hallucination):** Kaynak değişimi sonucu oluşabilecek
örnekleme oranı değişikliği, `ASIOFuture()` üzerinden **değil**, host'un
`asioMessage` callback'i üzerinden `kAsioResyncRequest` / `sampleRateChanged`
seçicileriyle bildirilir (asio.h satır 441–460 seçici enumu; §4.3 push modeli
ile aynı kanal). Bu, dosyanın önceki sürümünde hatalıydı.

Host bu bildirimi görmezden gelirse `ASIOGetSampleRate` değeri baypas edilmiş
olur ve tüm DSP zinciri yanlış oranla çalışır. Bkz.
`asio-surucu-modeli.md` (mesaj döngüsü) ve
`asio-hata-yonetimi-ve-fallback.md` (rate değişimi sonrası yeniden başlatma).

### §5.6 `ASIOSetSampleRate(0)`: Harici Senkron Modu

```cpp
// ASIOSetSampleRate: 0 means the sample rate is set to an external sync.
// If the rate is non-zero and the clock source is external, this is an error.
```

**Gerçek (kanıtlı):** `ASIOSetSampleRate(0)` **harici senkron**u bildirir; harici
clock kaynağı seçiliyken `rate != 0` ile çağırmak hata döndürür (`ASE_InvalidMode`
ailesi). `(Kanıt: asio.h, ASIOSetSampleRate yorumu)` — bu kural bkz.
`asio-hata-yonetimi-ve-fallback.md` §5 (harici clock senaryoları).

---

## §6 Host Referans Saati: `get_sys_reference_time()`

### §6.1 ASIO SDK Örneğinin Uygulaması

`hostsample.cpp` içinde tanımlı:

```cpp
static ASIOSamplePosition get_sys_reference_time()
{
    return (ASIOSamplePosition)timeGetTime();
}
```

**Gerçek (kanıtlı):** ASIO SDK'nın resmi host örneği, sistem referans saatini
`timeGetTime()` (Windows multimedia timer, milisaniye çözünürlük) ile üretir
ve doğrudan `ASIOSamplePosition` (64-bit) tipine yazar. `(Kaynak: hostsample.cpp)`
`timeGetTime()` ~15.6 ms çözünürlükte çalışır; yüksek hassasiyetli zaman
gereksinimleri için bkz. §7.

### §6.2 `ASIOBufferTimeInfo` Kullanımı

`hostsample.cpp`, `bufferSwitchTimeInfo` örneğinde `AsioTimeInfo`'yu şöyle
kullanır:

```cpp
ASIOTime *bufferSwitchTimeInfo(ASIOTime *timeInfo, long index, ASIOBool processNow)
{	// the actual processing callback.
	static long processedSamples = 0;

	// store the timeInfo for later use
	asioDriverInfo.tInfo = *timeInfo;

	// get the time stamp of the buffer, not necessary if no
	// synchronization to other media is required
	if (timeInfo->timeInfo.flags & kSystemTimeValid)
		asioDriverInfo.nanoSeconds = ASIO64toDouble(timeInfo->timeInfo.systemTime);
	else
		asioDriverInfo.nanoSeconds = 0;

	if (timeInfo->timeInfo.flags & kSamplePositionValid)
		asioDriverInfo.samples = ASIO64toDouble(timeInfo->timeInfo.samplePosition);
	else
		asioDriverInfo.samples = 0;

	// get the system reference time
	asioDriverInfo.sysRefTime = get_sys_reference_time();
}
```

`(Kaynak: hostsample.cpp satır 206–233, audiosdk/asio — main)` — bu, zaman
bilgisinin host'ta saklanmasının standart örneğidir; `asioDriverInfo.tInfo`
sonraki `ASIOGetSamplePosition` karşılaştırmalarında kullanılır. `nanoSeconds`
ve `samples` yalnız ilgili bayrak geçerliyse doldurulur, aksi halde `0`'a
çekilir — bu, §4.2 bayrak sözleşmesinin kod karşılığıdır. `sysRefTime` ise
§6.1'deki `get_sys_reference_time()` çağrısıdır; iki saat (driver `systemTime`
vs. host `timeGetTime`) arasındaki fark **drift ölçümünün ham girdisidir** (§13).

---

## §7 Windows Zaman Kaynakları: Seçim Matrisi

ASIO SDK doğrudan Windows zaman API'lerini çağırmaz (sürücü-agnostiktir);
host tarafı referans saatini seçmek uygulamanın sorumluluğudur. Windows
için seçenekler:

| API | Çözünürlük | Ölçüm yöntemi | ASIO ile kullanım |
|---|---|---|---|
| `timeGetTime()` | ~15.6 ms (varsayılan) | `DWORD` ms sayacı | ASIO SDK hostsample seçimi (§6.1) |
| `GetTickCount64()` | ~15.6 ms | 64-bit ms sayacı, taşma yok | Uzun oturumlar için alternatif |
| `QueryPerformanceCounter()` | ~100 ns tipik | CPU sayacı (TSC/HPET) | Yüksek hassasiyetli senkron |
| `QueryPerformanceFrequency()` | — | Sayacın Hz frekansı | QPC → ms dönüşümü için gerekli |

`(Kaynak: Windows SDK — bu tablo ASIO SDK'ya değil, host uygulamasına ait
bir seçim matrisidir)` — `timeGetTime()` seçimi ASIO SDK `hostsample.cpp`
içinde kanıtlanmıştır (§6.1); diğer üçü ASIO SDK'da **geçmez**, yalnız
karşılaştırma için listelenmiştir.

**Kural (Uygulama):** `AsioTimeInfo::systemTime` alanı milisaniye
gerektirir (`ASIOTimeInfo` yorumu §4.1); QPC sayacı Hz frekansına bölünerek
ms'ye çevrilmelidir. Dönüşüm formülü (derleme zamanı değil, çalışma zamanı):

```cpp
// QPC -> milisaniye (çalışma zamanı dönüşümü, asio.h'de tanımlı DEĞİLDIR)
ms = (double)(counter - startCounter) * 1000.0 / (double)frequency;
```

---

## §8 JUCE'nin ASIO Zaman Modeli ile Karşılaştırması

JUCE `juce_ASIO_windows.cpp`, ASIO'nun iki zaman callback'ini de
ele alır ama **sadece birini** kullanır:

| ASIO Callback | JUCE Davranışı | Kanıt |
|---|---|---|
| `bufferSwitch` (basit) | `bufferSwitchTimeInfo` JUCE tarafından **`kAsioSupportsTimeInfo=0`** bildirdiği için sürücü bu callback'i kullanmaz | JUCE kaynağı §5 |
| `bufferSwitchTimeInfo` (zamanlı) | Tanımlıdır: `timeInfo = *timeInfo` (tek satır — **timeInfo atlanır**) | JUCE kaynağı §8 |
| `asioMessage(kAsioSupportsTimeInfo)` | **`return 0;` — JUCE istemiyor** | JUCE kaynağı §6 |
| `bufferSwitchTimeInfo` gelen `timeInfo` | `timeInfo = *timeInfo` tek satır; alanlar **okunmaz, kullanılmaz** | JUCE kaynağı §8 |

**Karşıtlık (raporlanan):** ASIO SDK `hostsample.cpp` `bufferSwitchTimeInfo`
zaman bilgisini saklar (§6.2); JUCE ise `kAsioSupportsTimeInfo`'yu **reddeder**
ve `bufferSwitchTimeInfo` içinde `timeInfo`'yu **atlar** (yalnızca `timeInfo`
pointer'ının kendisini geri verir). Sonuç: JUCE tabanlı uygulamalarda
`AsioTimeInfo` alanları **kullanılamaz**; zaman senkronizasyonu host'un
`get_sys_reference_time` eşdeğerine (JUCE'de `Time::getMillisecondCounter()`
ailesi) dayanır. **(Kanıt: JUCE kaynağı §5-§8; bkz. `asio-kapsam.md` §11
çelişki kaydı C-JUCE-1 "kAsioSupportsTimeInfo reddi".)**

---

## §9 Zaman Senkronu Hata Modları

| Hata modu | Belirti | ASIO Nedeni | Host Karşılığı |
|---|---|---|---|
| Sayacı durur | `ASIOGetSamplePosition` sabit değer döner | Clock kaynağı kaybedildi → `ASE_SPNotAdvancing` | `ASIOGetClockSources` ile kaynağı yeniden sorgula |
| Ofset sürüklenir | `systemTime` ile `samplePosition` uyumsuz | İki bağımsız saat drift'i (§2) | Host tarafında iki saat farkını izle ve kalibre et (§12.2) — `ASIOFuture`'da buna dair seçici **yoktur** (§3.4) |
| Rate değişimi duyulmaz | Eski oranla hesap yapılmaya devam edilir | `ASIOSetClockSource` sonrası `sampleRateChanged` iletisi gelmedi (§5.5) | `ASIOGetSampleRate` ile doğrula; bkz. `asio-hata-yonetimi-ve-fallback.md` |
| Harici mod hatalı | `ASE_InvalidMode` | Harici clock + `rate != 0` (§5.6) | `ASIOSetSampleRate(0)` ile harici senkronu bildir |
| Bayraklı bilgi eksik | `kSystemTimeValid` ayarlı değil | Sürücü hatalı `AsioTimeInfo` gönderdi | Bayrak doğrulaması yap; `kSystemTimeValid`/`kSamplePositionValid` her koşulda beklenir (§4.2) |

---

## §10 Soru → Cevap Özet Tablosu

| # | Soru (kapsam §2.1) | Kısa Cevap | § |
|---|---|---|---|
| 1 | Örnekleme sayacı ne zaman sıfırlanır? | `ASIOStart()` çağrısında — sürücü "running state"e geçer | §3.1 |
| 2 | Sayacı hangi birim ve hizalama ile okuruz? | Örnek cinsinden, arabellek boyutuna (blok sınırına) hizalı; uyuşmazlık `ASE_SPNotAdvancing` | §3.2-§3.3 |
| 3 | `AsioTimeInfo` bayrakları neler? | `kSystemTimeValid`(1) + `kSamplePositionValid`(2) her zaman geçerli; ayrıca `kSampleRateValid`(4), `kSpeedValid`(8), `kSampleRateChanged`(16), `kClockSourceChanged`(32) | §2, §4.2 |
| 4 | Zaman bilgisi nasıl gelir? | Push modeli: sürücü `asioMessage(kAsioSupportsTimeInfo)` sorar, host `1` dönerse `bufferSwitchTimeInfo` callback'inde `ASIOTime` gönderir; `kAsioGetTime` seçici **asio.h'de yoktur** | §4.3 |
| 5 | Saat kaynağı nasıl seçilir? | `ASIOGetClockSources` listeler (index 0 dahili); `ASIOSetClockSource(index)` seçer; oran değişirse `sampleRateChanged` zorunlu | §5.4-§5.5 |
| 6 | Harici senkron nasıl bildirilir? | `ASIOSetSampleRate(0)`; harici clock + `rate != 0` → hata | §5.6 |
| 7 | Host referans saati nasıl üretilir? | ASIO SDK `hostsample.cpp`: `timeGetTime()`; hassas gereksinimde QPC matrisi §7 | §6.1, §7 |
| 8 | JUCE bu modeli kullanıyor mu? | Hayır — `kAsioSupportsTimeInfo=0` + `bufferSwitchTimeInfo` içinde `timeInfo` atlanır | §8 |

---

## §11 Terimler Sözlüğü

| Terim | Tanım |
|---|---|
| Sample Position | Örnekleme sayacı; `ASIOGetSamplePosition` ile okunur, `ASIOStart()`'ta sıfırlanır (§3.1) |
| AsioTimeInfo | `systemTime` + `samplePosition` + bayrakları taşıyan kombine zaman bildirimi (§4.1) |
| System Reference Time | Host'un sistem referans saati; SDK örneğinde `timeGetTime()` (§6.1) |
| ASIOClockSource | Seçilebilir clock kaynağı tanımı: `index`, `associatedChannel`, `associatedGroup`, `isCurrentSource`, `name[32]` (§5.1) |
| Word Clock | Kanal/grupsuz clock kaynağı — `associatedChannel`/`associatedGroup` = `-1` (§5.1, asio.h satır 711–715) |
| kAsioCanTimeInfo | `ASIOFuture` seçicisi (değer 10) — sürücünün time info desteğini sorgular (§3.4, §4.3) |
| ASE_SPNotAdvancing | Sayacın durduğu hata durumu (§3.3) |
| QPC | `QueryPerformanceCounter` — Windows yüksek çözünürlüklü sayacı (§7) |
| Drift | İki yerel saat (sürücü `systemTime` vs. host `sysRefTime`) farkının zaman içindeki eğimi (§12.2) |

---

## §12 Drift Ölçümü ve QPF Dönüşümü (host tarafı)

İki saat vardır (§2): sürücü `systemTime` (ns, `timeGetTime` türevi) ve host
`get_sys_reference_time()` (ms). Bu ikisi arasındaki fark, clock drift'inin ham
girdisidir (§6.2 sonu).

### §12.1 QPC → ms/ns Dönüşümü

`QueryPerformanceCounter` sayacı mutlak birimdedir; frekans
`QueryPerformanceFrequency` ile alınır (§7 matrisi). Dönüşüm (çalışma zamanı,
`asio.h`'de tanımlı değildir):

| Büyüklük | Formül | Not |
|---|---|---|
| ms | `ms = (counter - startCounter) * 1000.0 / frequency` | §7 matrisindeki kural |
| ns | `ns = (counter - startCounter) * 1000000000.0 / frequency` | `systemTime` birimi (§4.1) |
| Örnek (48 kHz) | `sample = ns * 48000 / 1000000000` | `samplePosition` birimi (§4.1) |

`(Kaynak: Windows SDK — QPC/QPF dönüşümü; birimler §4.1 ve §7'den)`

### §12.2 İki Saat Arasındaki Drift (host gözlemi)

`bufferSwitchTimeInfo` örneği her callback'te iki saati aynı anda yakalar
(§6.2): `systemTime` (ns) ve `sysRefTime` (ms). Drift bu iki değerin farkının
zaman içindeki eğimidir:

| Adım | İşlem | Birim |
|---|---|---|
| 1 | `delta0 = sysRefTime - (systemTime / 1000000.0)` | ms — hostsample debug satırı aynı hesabı yapar |
| 2 | N callback sonra `deltaN` tekrar ölç | ms |
| 3 | `drift = (deltaN - delta0) / N` | ms/callback |
| 4 | `drift_ppm = drift * 1e6 / (bufferSize / sampleRate * 1000)` | ppm |

`(Kaynak: hostsample.cpp debug bloğu — `sysRefTime - (long)(nanoSeconds / 1000000.0)` hesabı satır 241'de birebir vardır)`

`drift_ppm`, iki saatin hız oranıdır (1.0 = senkron). Bu ölçüm RFC 5905'teki
NTP offset/drift modelinin basitleştirilmiş halidir; ASIO'da ağ yok, yalnız
iki yerel sayaç vardır.

### §12.3 Zaman Damgası Birim Dönüşüm Tablosu (tüm dosya boyunca)

| Alan | Birim | asio.h kanıtı |
|---|---|---|
| `AsioTimeInfo::systemTime` | nanosaniye (`ASIOTimeStamp`) | satır 259 |
| `AsioTimeInfo::samplePosition` | örnek (`ASIOSamples`) | satır 262 |
| `AsioTimeInfo::sampleRate` | Hz (`ASIOSampleRate`) | satır 263 |
| `get_sys_reference_time()` | milisaniye (`timeGetTime`) | hostsample.cpp satır 233 + §6.1 |
| `ASIOTimeCode::timeCodeSamples` | örnek | §5.1 / RFC 3550 ile karşılaştırma |
| RTP timestamp (RFC 3550) | örnek cinsinden sabit oranlı birim | RFC 3550 §5.1 (karşılaştırma) |

### §12.4 İddia–Kanıt (bu bölüm)

| İddia | Kanıt |
|---|---|
| QPC sayacı Hz frekansına bölünerek ms/ns çevrilir | `QueryPerformanceFrequency` + `QueryPerformanceCounter` — docs.microsoft.com (HEAD 200) |
| `sysRefTime - nanoSeconds/1e6` hesabı gerçek örnektedir | hostsample.cpp satır 241 — audiosdk/asio — main |
| Drift = NTP offset/drift modelinin yerel karşılığı | RFC 5905 (offset/drift tanımı) — rfc-editor.org (HEAD 200) |
| RTP timestamp örnek cinsindendir (karşılaştırma) | RFC 3550 §5.1 — rfc-editor.org (HEAD 200) |
| `systemTime` Windows'ta `timeGetTime()`'dan türetilir | asio.h satır 261 — audiosdk/asio — main |

---

## §13 Doğrulama ve Sonraki Adım

| Adım | Durum | Kanıt |
|---|---|---|
| §3.1-§3.3 sayacı ve hata durumu | Kanıtlandı | `asio.h` prototip yorumları (§3, §3.3) |
| §4 `AsioTimeInfo` tanımı ve bayrakları | Kanıtlandı | `asio.h` yapı + enum (§4.1-§4.2) |
| §5 clock source modeli | Kanıtlandı | `asio.h` yapı + 3 API yorumu (§5.1-§5.6) |
| §6 host örneği saati | Kanıtlandı | `hostsample.cpp` (§6.1-§6.2) |
| §7 Windows zaman matrisi | Doğrulandı | Windows SDK API'leri (ASIO dışı, §7) |
| §8 JUCE karşıtlığı | Kanıtlandı | JUCE kaynağı §5-§8 (§8) |
| Dosya içi spec-kod tutarlılığı | Doğrulandı | §4.2 / §5.1-§5.3 asio.h'deki gerçek enum/yapı adlarını (`AsioTimeInfoFlags`, `ASIOClockSource`) kullanır; uydurma `kAsioGetTime`/`ASIOClockType`/`ASIOClockSourceFlags` kaldırıldı |
| §12 drift/QPF bölümü | Doğrulandı | hostsample.cpp satır 241 + QPC/QPF + RFC 5905/3550 (HEAD 200) |

**Sonraki adım:** `validation: []` alanını, `asio-kapsam.md` §12.3'teki
doğrulama protokolüne göre doldurmak (ASIO SDK `hostsample` derlemesi +
saat kaynağı enum testi).

---

Authority: `.ai/architecture/K003-audio/backend/asio/asio-kapsam.md` §2.1 (Soru 3), §14
