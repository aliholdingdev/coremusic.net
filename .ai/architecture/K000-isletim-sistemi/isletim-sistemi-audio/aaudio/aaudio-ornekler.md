---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/aaudio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-stream-yasam-dongusu
evidence:
  - https://developer.android.com/ndk/guides/audio/aaudio/aaudio
  - https://developer.android.com/ndk/guides/audio/opensl/opensl-for-android
  - https://developer.android.com/ndk/reference/group/audio
validation:
  - "2026-10-14 · GÖVDE-GET: AAudio rehberi tam metin indirildi — bu dosyadaki TÜM kod blokları birebir alıntıdır, her blokta kaynak etiketi var; sayfa son güncelleme 2026-03-06 UTC"
  - "2026-10-14 · 3 URL HEAD 200 (aaudio/aaudio · opensl-for-android · reference/group/audio) · ölü aday 3 URL §7'de elendi"
open_questions:
  - "CoreMusic reposunda AAudio/Android kodu YOK (2026-10-13 tarama 0 isabet) → bu dosya gelecek yüzeyi tarif eder"
  - "MMAP/exclusive yolunun gerçek cihazda desteklenip desteklenmediği ölçülmedi → ⚠️ VERIFICATION REQUIRED (aaudio/index.md A2)"
group: kod
---

# aaudio-ornekler — Resmi Rehber Kod Alıntıları ve Çağrı Sırası

> **Bu dosya ne değildir:** Özgün yazılmış, çalıştırılabilir bir uygulama
> değildir. Buradaki her kod bloğu **developer.android.com NDK AAudio
> rehberinden birebir alıntıdır** ve başında kaynak etiketi vardır. Uydurma
> kod yasaktır (RED TEAM).

**ELI15:** AAudio'da ses çalmak builder (kurucu) ile başlar: builder'ı oluştur
→ parametreleri kur → `openStream` ile akışı aç → durumunu doğrula → başlat
(`requestStart`) → callback ile veriyi al/yaz → `close` ile kapat. Bu dosya bu
zinciri resmi rehberin kendi koduyla, adım adım taşır.

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/aaudio/aaudio-ornekler` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/aaudio` |
| Kademe | 5 · yaprak-modül |
| group | `kod` |
| Doğrulama tarihi | 2026-10-14 |
| Kanıt yöntemi | **Birincil: gövde-GET** — tüm kod blokları resmi rehber alıntısı |

### §1.1 Kaynaklar

| Etiket | Kaynak | URL | 2026-10-14 Durum |
|---|---|---|---|
| [G1] | AAudio rehberi (NDK, ana sayfa) | https://developer.android.com/ndk/guides/audio/aaudio/aaudio | ✅ HEAD 200 · GÖVDE-GET |
| [G2] | OpenSL ES for Android (geçiş sayfası) | https://developer.android.com/ndk/guides/audio/opensl/opensl-for-android | ✅ HEAD 200 |
| [G3] | AAudio API referansı (rehberin içindeki link) | https://developer.android.com/ndk/reference/group/audio | ✅ HEAD 200 |
| [G4] | NDK Samples sayfası | https://developer.android.com/ndk/samples | ✅ HEAD 200 |
| [G5] | Oboe (rehberin kendi notu — GitHub) | https://github.com/google/oboe | ⚠️ yalnız rehber metni içinde referans |

> [G5] bu turda ayrı HEAD ile ölçülmedi — rehberin kendi cümlesi
> ("open source Oboe library which is available on GitHub") kaynak olarak
> [G1]'den gelir; bağımsız URL doğrulaması yapılmadı → ⚠️ VERIFICATION REQUIRED.

---

## §2 Repo kanıtı — CoreMusic'te AAudio kodu YOK

| Ölçüm | Yüzey | Sonuç | Tarih |
|---|---|---|---|
| `AAudio` · `OpenSL` · `AAudioStream` desen taraması | `shared/src` + `home.coremusic.net` PHP/JS | **0 isabet** | 2026-10-13 (`aaudio/index.md` §1) |
| Android/AAudio kaynak dosyası | repo geneli | **0 dosya** | 2026-10-13 |

**Sonuç:** bu dosyadaki kodların hiçbiri CoreMusic'in kodu değildir; Neva
Engine'in **hedef Android yüzeyi** için şartname-alıntıdır (⚠️ PLANNED).

---

## §3 Başlangıç — header ve builder

**Kaynak:** [G1] "Getting started" + "Creating an audio stream" · GET 2026-10-14 · birebir

```cpp
#include <aaudio/AAudio.h>
```

**Kaynak:** [G1] adım 1 (builder oluşturma)

```c
AAudioStreamBuilder *builder;
aaudio_result_t result = AAudio_createStreamBuilder(&builder);
```

**Ne yapar:** AAudio **builder design pattern** kullanır — akış, tek çağrıyla
değil, kurulup açılan bir builder üzerinden hazırlanır (rehberin kendi ifadesi:
"The AAudio library follows a builder design pattern").

### §3.1 Yapılandırma çağrıları (builder set*)

**Kaynak:** [G1] adım 2 · birebir

```c
AAudioStreamBuilder_setDeviceId(builder, deviceId);
AAudioStreamBuilder_setDirection(builder, direction);
AAudioStreamBuilder_setSharingMode(builder, mode);
AAudioStreamBuilder_setSampleRate(builder, sampleRate);
AAudioStreamBuilder_setChannelCount(builder, channelCount);
AAudioStreamBuilder_setFormat(builder, format);
AAudioStreamBuilder_setBufferCapacityInFrames(builder, frames);
```

**Rehberin kendi uyarısı (birebir):** "Note that these methods do not report
errors, such as an undefined constant or value out of range." — yani bu set*
çağrıları **hata döndürmez**; doğrulama adım 4'te yapılır.

| Varsayılan | Rehber cümlesi |
|---|---|
| `deviceId` verilmezse | default = primary output device |
| `direction` verilmezse | default = output stream |
| diğer parametreler | `AAUDIO_UNSPECIFIED` veya hiç verilmemek = sistem optimumu seçer |

---

## §4 Akışı açma ve doğrulama (set ↔ get eşlemesi)

**Kaynak:** [G1] adım 3 · birebir

```c
AAudioStream *stream;
result = AAudioStreamBuilder_openStream(builder, &stream);
```

**Kaynak:** [G1] adım 4 tablosu (set/get karşılıkları — birebir eşleme)

| Builder (set) | Akış (get) — doğrulama |
|---|---|
| `AAudioStreamBuilder_setDeviceId()` | `AAudioStream_getDeviceId()` |
| `AAudioStreamBuilder_setDirection()` | `AAudioStream_getDirection()` |
| `AAudioStreamBuilder_setSharingMode()` | `AAudioStream_getSharingMode()` |
| `AAudioStreamBuilder_setSampleRate()` | `AAudioStream_getSampleRate()` |
| `AAudioStreamBuilder_setChannelCount()` | `AAudioStream_getChannelCount()` |
| `AAudioStreamBuilder_setFormat()` | `AAudioStream_getFormat()` |
| `AAudioStreamBuilder_setBufferCapacityInFrames()` | `AAudioStream_getBufferCapacityInFrames()` |

**Rehber kuralı (birebir):** sample format/rate/frame sayısı **değişmez**;
sharing mode ve buffer capacity cihaza göre **değişebilir** → "you should check
the stream's configuration before using it".

**Kaynak:** [G1] adım 5 · birebir

```c
AAudioStreamBuilder_delete(builder);
```

---

## §5 Durum geçişleri (state machine)

**Kaynak:** [G1] "State transitions" · birebir

Kararlı 5 durum: `Open` · `Started` · `Paused` · `Flushed` · `Stopped`
(bir de hata durumu `Disconnected`). **Veri yalnız `Started` durumunda akar.**

```c
aaudio_result_t result;
result = AAudioStream_requestStart(stream);
result = AAudioStream_requestStop(stream);
result = AAudioStream_requestPause(stream);
result = AAudioStream_requestFlush(stream);
```

Geçici (transient) durumlar: `Starting` · `Pausing` · `Flushing` · `Stopping` · `Closing`.
Rehber notu: `pause`/`flush` yalnız **output** stream'de istenebilir.

**Kaynak:** [G1] `waitForStateChange` örneği · birebir

```c
aaudio_stream_state_t inputState = AAUDIO_STREAM_STATE_PAUSING;
aaudio_stream_state_t nextState = AAUDIO_STREAM_STATE_UNINITIALIZED;
int64_t timeoutNanos = 100 * AAUDIO_NANOS_PER_MILLISECOND;
result = AAudioStream_requestPause(stream);
result = AAudioStream_waitForStateChange(stream, inputState, &nextState, timeoutNanos);
```

**Rehber kuralı:** durum değişim **callback'i yoktur** — `waitForStateChange`
kullanılır; bu fonksiyon "kendi başına" durum yakalamaz, `inputState`'ten
farklı bir duruma geçişi bekler. `close()` sonrası bu çağrı **çağrılmaz**
(stream kapanışta silinir).

---

## §6 Okuma / yazma ve sıfır dolgusu

**Kaynak:** [G1] "Reading and writing to an audio stream" · birebir

İki yol: (1) yüksek öncelikli **callback**, (2) `AAudioStream_read` /
`AAudioStream_write`. "For a blocking read or write that transfers the
specified number of frames, set timeoutNanos greater than zero. For a
non-blocking call, set timeoutNanos to zero."

```c
aaudio_result_t result =
    AAudioStream_read(stream, audioData, numFrames, timeout);
if (result < 0) {
  // Error!
}
if (result != numFrames) {
  // pad the buffer with zeros
  memset(static_cast<sample_type*>(audioData) + result * samplesPerFrame, 0,
      sizeof(sample_type) * (numFrames - result) * samplesPerFrame);
}
```

**Neden sıfır dolgusu:** rehber — "the buffer might contain unknown data that
could cause an audio glitch. You can pad the buffer with zeros to create a
silent dropout".

**Kapanış — Kaynak:** [G1] · birebir

```c
AAudioStream_close(stream);
```

**Rehber kuralı (birebir):** "Closing a stream is not thread safe! Do NOT try
to close a stream in one thread while using the stream in another thread."

### §6.1 Bağlantı kopması (Disconnected)

| Tetikleyen | Rehber maddesi |
|---|---|
| cihaz tak-çıkar (kulaklık çıkarma) | "The associated audio device is no longer connected" |
| dahili hata | "An error occurs internalty" — internal error |
| cihaz artık birincil değil | "An audio device is no longer the primary audio device" |

Kural: kopan akış her koşulda **önce durdurulur, sonra kapatılır**
("You must always stop and close a disconnected stream"). Callback ile
çalışıyorsanız dönüş kodu gelmez → **error callback** zorunlu:

**Kaynak:** [G1] errorCallback örneği · birebir

```c
void errorCallback(AAudioStream *stream,
                   void *userData,
                   aaudio_result_t error) {
    // Launch a new thread to handle the disconnect.
    std::thread myThread(my_error_thread_proc, stream, userData);
    myThread.detach(); // Don't wait for the thread to finish.
}
```

> Rehber uyarısı: error callback **kendi thread'inde** bildirim yapar; durdurma
> / kapatma başka thread'de yapılmalıdır — aksi halde **deadlock** riski.

---

## §7 Yüksek öncelikli callback (çekirdek yüzey)

**Kaynak:** [G1] callback prototipi · birebir

```c
typedef aaudio_data_callback_result_t (*AAudioStream_dataCallback)(
        AAudioStream *stream,
        void *userData,
        void *audioData,
        int32_t numFrames);

```

**Kaynak:** [G1] kayıt (registration) · birebir

```c
AAudioStreamBuilder_setDataCallback(builder, myCallback, myUserData);
```

**Kaynak:** [G1] sinüs üretici callback · birebir

```c
aaudio_data_callback_result_t myCallback(
        AAudioStream *stream,
        void *userData,
        void *audioData,
        int32_t numFrames) {
    int64_t timeout = 0;

    // Write samples directly into the audioData array.
    generateSineWave(static_cast<float *>(audioData), numFrames);
    return AAUDIO_CALLBACK_RESULT_CONTINUE;
}
```

**Callback kuralı (rehber):** callback, **kendi çağırdığı akış üzerinde read/write
yapmamalı**; input callback'i gelen veriyi işler, output callback'i `audioData`
buffer'ına yazar.

### §7.1 Round-trip callback (input → output köprüsü)

**Kaynak:** [G1] · birebir — master akış output'tur; input stream userData ile
taşınır ve **non-blocking** okunur:

```c
aaudio_data_callback_result_t myCallback(
        AAudioStream *stream,
        void *userData,
        void *audioData,
        int32_t numFrames) {
    AAudioStream *inputStream = (AAudioStream *) userData;
    int64_t timeout = 0;
    aaudio_result_t result =
        AAudioStream_read(inputStream, audioData, numFrames, timeout);

  if (result == numFrames)
      return AAUDIO_CALLBACK_RESULT_CONTINUE;
  if (result >= 0) {
      memset(static_cast<sample_type*>(audioData) + result * samplesPerFrame, 0,
          sizeof(sample_type) * (numFrames - result) * samplesPerFrame);
      return AAUDIO_CALLBACK_RESULT_CONTINUE;
  }
  return AAUDIO_CALLBACK_RESULT_STOP;
}
```

---

## §8 Buffer optimizasyonu ve performance mode

**Kaynak:** [G1] buffer optimizasyon döngüsü · birebir

```c
int32_t previousUnderrunCount = 0;
int32_t framesPerBurst = AAudioStream_getFramesPerBurst(stream);
int32_t bufferSize = AAudioStream_getBufferSizeInFrames(stream);

int32_t bufferCapacity = AAudioStream_getBufferCapacityInFrames(stream);

while (go) {
    result = writeSomeData();
    if (result < 0) break;

    // Are we getting underruns?
    if (bufferSize < bufferCapacity) {
        int32_t underrunCount = AAudioStream_getXRunCount(stream);
        if (underrunCount > previousUnderrunCount) {
            previousUnderrunCount = underrunCount;
            // Try increasing the buffer size by one burst
            bufferSize += framesPerBurst;
            bufferSize = AAudioStream_setBufferSize(stream, bufferSize);
        }
    }
}
```

**Ölçüm karşılığı:** `AAudioStream_getXRunCount()` ALSA'daki xrun sayacının
AAudio karşılığıdır (karşılaştırma: kök `-dogrulama.md` §C.5).

**Performance mode (rehberin üç modu):**

| Mod | Rehber tanımı |
|---|---|
| `AAUDIO_PERFORMANCE_MODE_NONE` | varsayılan — gecikme/enerji dengesi |
| `AAUDIO_PERFORMANCE_MODE_LOW_LATENCY` | küçük buffer, optimize yol — oyun/klavye-sentezleyici |
| `AAUDIO_PERFORMANCE_MODE_POWER_SAVING` | büyük buffer — akış müzik/MIDI çalar |

**Kaynak:** [G1] "en düşük gecikme" örneği · birebir

```c
// Create a stream builder
AAudioStreamBuilder *streamBuilder;
AAudio_createStreamBuilder(&streamBuilder);
AAudioStreamBuilder_setDataCallback(streamBuilder, dataCallback, nullptr);
AAudioStreamBuilder_setPerformanceMode(streamBuilder, AAUDIO_PERFORMANCE_MODE_LOW_LATENCY);

// Use it to create the stream
AAudioStream *stream;
AAudioStreamBuilder_openStream(streamBuilder, &stream);
```

**Rehber cümlesi:** "in order to achieve the lowest possible latency you must
use the `AAUDIO_PERFORMANCE_MODE_LOW_LATENCY` performance mode **along with a
high-priority callback**".

---

## §9 Thread safety (bağlayıcı kurallar — birebir)

**Kaynak:** [G1] "Thread safety" bölümü:

| # | Kural (rehber metni) |
|---|---|
| T1 | "The AAudio API is not completely thread safe." — mutex yok, bilerek (glitch önlemi) |
| T2 | `waitForStateChange()` iki thread'den **çağrılmaz** |
| T3 | aynı stream'e iki thread'den read/write **yapılmaz** |
| T4 | bir thread'de close, diğerinde read/write **yapılmaz** |
| T5 | `AAudioStream_get*()` (timestamp hariç) · `AAudio_createStreamBuilder()` · `AAudio_convert*ToText()` **thread safe** |
| T6 | callback kullanırken: callback thread'de read/write **serbest**, close callback'in çalıştığı thread'de **güvenli** |

---

## §10 Çağrı sırası özeti (sözleşme)

| Adım | Çağrı | Ne çözer |
|---|---|---|
| 1 | `AAudio_createStreamBuilder` | builder oluşturulur |
| 2 | `AAudioStreamBuilder_set*` (7 çağrı) | yön · cihaz · format · paylaşım · buffer |
| 3 | `AAudioStreamBuilder_openStream` | akış açılır (state: Open) |
| 4 | `AAudioStream_get*` + `getDataFormat` | yapı doğrulanır, gerekirse format çevirisi |
| 5 | `setDataCallback` (+ `setPerformanceMode`) | veri yolu bağlanır |
| 6 | `AAudioStream_requestStart` | state → Starting → Started |
| 7 | callback `AAudioStream_dataCallback` | burst ritminde veri akar |
| 8 | `requestStop` → `AAudioStream_close` | durdurma ve kapanış (tek thread kuralı T4) |

**Format tablosu (rehber — birebir):** `AAUDIO_FORMAT_PCM_I16` (int16_t) ·
`AAUDIO_FORMAT_PCM_FLOAT` (-1.0…+1.0) · `AAUDIO_FORMAT_PCM_I24_PACKED` ·
`AAUDIO_FORMAT_PCM_I32` · `AAUDIO_FORMAT_IEC61937`.

**Paylaşım (rehber — birebir):** `AAUDIO_SHARING_MODE_EXCLUSIVE` = cihaz tek
akışta ("lowest possible latency", kopmaya daha açık) · `AAUDIO_SHARING_MODE_SHARED`
= AAudio aynı cihazdaki tüm shared akışları karıştırır (mix). Varsayılan: **SHARED**.

---

## §11 Anti-pattern — NE YAPILMAZ

| # | Yasak davranış | Sonuç | Kaynak |
|---|---|---|---|
| 1 | set* çağrılarının hatasız varsayılması | sessiz yanlış yapılandırma | [G1] adım 2 uyarısı |
| 2 | openStream sonrası get* doğrulamayı atlamak | sharing/buffer sürprizi | [G1] adım 4 |
| 3 | disconnect sonrası yalnız close (durmadan) | rehber ihlali — önce stop+close | [G1] Disconnected |
| 4 | error callback'te doğrudan close (aynı thread) | deadlock riski | [G1] errorCallback |
| 5 | callback içinde akışa read/write (kendi akışı) | glitch/lock-free ihlali | [G1] callback kuralı |
| 6 | iki thread'de close + read/write | thread safety ihlali (T4) | [G1] Thread safety |
| 7 | LOW_LATENCY isteyip callback kullanmamak | en düşük gecikme elde edilmez | [G1] performance mode |
| 8 | xrun/artış saymadan buffer büyütmek | ölçüm yok, kör ayar | [G1] optimizasyon döngüsü |

---

## §12 OS grup eşlemesi (kök dosya ile)

| OS | Bu dal | Kök `-ornekler.md` grubu |
|---|---|---|
| Android | **bu dosya** (7/7 dal örneği tamamlanır) | §20 (AAudio) |
| Windows | `../wasapi/wasapi-kod-ornekleri.md` | §17 |
| Linux | `../alsa/alsa-ornekler.md` | §10 (mevcut) |
| macOS / iOS | `../coreaudio/coreaudio-ornekler.md` | §11 (mevcut) |
| Tizen | `../tizen-audio/tizen-audio-ornekler.md` | §21 |
| webOS | `../webos-audio/webos-audio-ornekler.md` | §22 |
| ReactOS | `../reactos-audio/reactos-audio-ornekler.md` | §23 |

---

## §13 Doğrulama matrisi (bu tur — 2026-10-14)

| # | İddia | URL | Durum |
|---|---|---|---|
| 1 | AAudio rehberi builder/state/callback kodunu sayfasında taşır | https://developer.android.com/ndk/guides/audio/aaudio/aaudio | ✅ HEAD 200 · GÖVDE-GET |
| 2 | OpenSL ES "deprecated → Oboe" geçiş sayfası resmi NDK'da | https://developer.android.com/ndk/guides/audio/opensl/opensl-for-android | ✅ HEAD 200 |
| 3 | AAudio API referansı `/ndk/reference/group/audio` altında | https://developer.android.com/ndk/reference/group/audio | ✅ HEAD 200 |
| 4 | NDK Samples sayfası erişilebilir | https://developer.android.com/ndk/samples | ✅ HEAD 200 |

**El edilen (ölü) URL'ler — kullanılmadı:**

| URL | HTTP | Not |
|---|---|---|
| https://developer.android.com/ndk/guides/audio/aaudio | 404 | doğru yol `/aaudio/aaudio` (çift segment) |
| https://developer.android.com/ndk/reference/group/aaudio | 404 | doğru slug `group/audio` |
| https://github.com/google/ndk-samples | 404 | bu adla depo yok — örneklere [G4] üzerinden gidildi |

---

## §PHP AAudio × PHP (2026-10-14)

Android cihazda resmi PHP runtime **yoktur** → PHP'nin AAudio'ya erişimi **yoktur**.
A Audio (§20 `AAudioStreamBuilder` C++ dalı) yalnız NDK/C++'dan sürülür; PHP'nin
yolu sunucudan HTTP + tarayıcı/WebView HTML5 audio'dur (kök §25.1 yol 2 · §25.5).
NDK'dan AAudio çağırma gereksinimi PHP'nin bu katmanda **devre dışı** olduğunu
kod düzeyinde doğrular (§20.7 `aaudio-developer`).

---

**Geniş dil seti — AAudio × dil (kök §26):** AAudio C API'sidir (§20) — Rust `cpal`
**Android backend'i AAudio'dur** (README matrisi: "Android | AAudio | -" — birebir
alıntı) · diğer diller (Python/Go/PHP/Node) cihazda runtime **olmadığından** bu
katmana erişemez; tarayıcı Web Audio katmanı (kök §26.2) tek JS yolu.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
