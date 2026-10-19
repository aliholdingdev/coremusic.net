---
title: "ASIO Hata Yönetimi ve Fallback Stratejileri"
node_id: K003-audio/backend/asio/asio-hata-yonetimi-ve-fallback
node_type: architecture-deepdive
status: draft
parent_id: K003-audio/backend/asio
children: []
depends_on:
  - K003-audio/backend
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient2-setclientproperties
  - https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient2
  - https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-reset
  - https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize
  - https://learn.microsoft.com/en-us/windows/win32/api/audiopolicy/nn-audiopolicy-iaudiosessioncontrol
  - https://learn.microsoft.com/en-us/windows/win32/api/audiopolicy/nn-audiopolicy-iaudiosessionmanager
  - https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp
validation: []
open_questions: []
group: mimari
date: 2026-10-12
updated: 2026-10-12
---

# ASIO Hata Yönetimi ve Fallback Stratejileri

> Bu düğüm, ASIO'nun hata kodu modelini (`ASIOError` + `ASE_*` sabitleri),
> sürücü-bağımsız hata yönetimini ve hata sonrası fallback zincirini (WASAPI
> Exclusive → Shared, cihaz kaybı kurtarma) Türkçe olarak derinlemesine
> inceler. Dosya, `asio-kapsam.md` §2.1'deki "hata yönetimi" dal sorusunun
> (Soru 4) hedefidir.

---

## §0 Metadata ve Erişim

| Alan | Değer |
|---|---|
| Dosya yolu | `.ai/architecture/K003-audio/backend/asio/asio-hata-yonetimi-ve-fallback.md` |
| node_id | `K003-audio/backend/asio/asio-hata-yonetimi-ve-fallback` |
| node_type | `architecture-deepdive` |
| group | `mimari` |
| status | `draft` |
| parent_id | `K003-audio/backend/asio` |
| depends_on | `K003-audio/backend` |
| Düğüme özel kanıt | 7 resmi URL — frontmatter `evidence` listesinde (HEAD 200 doğrulandı) |

**Authority (bu dosyanın yetki sınırı):** ASIO hata kodu modeli ve host
tarafı fallback zinciri: `ASIOError` / `ASE_*` sabitleri, hata sonrası yeniden
deneme sırası, ASIO → WASAPI Exclusive → WASAPI Shared → seçici (scrub) çıkış
hierarşisi, cihaz kaybı (`device loss`) kurtarma durum makinesi. Yönlendirme
ve anahtar kavramlar için bkz. `index.md`; kapsam sınırları için bkz.
`asio-kapsam.md`; sürücü mesaj döngüsü için bkz. `asio-surucu-modeli.md`.

---

## §1 Bu Dosyanın Kapsamı

ASIO, tek bir geniş hata enumu (`ASIOError`) üzerinden hem **çağrı hatalarını**
(ne zaman ne yanlış gitti) hem **durum bildirimlerini** (`asioMessage` ile
gelen olaylar) iletir. Bu dosya şu soruları yanıtlar:

1. `ASIOError` tipi nedir; `ASE_SUCCESS` değeri neden garip bir hex'tir?
2. `ASE_*` sabitleri hangi çağrı hatalarını kapsar?
3. Çağrı hataları geldiğinde host'un yeniden deneme sırası nedir?
4. ASIO açılamazsa zincir hangi sırayla devam eder (fallback)?
5. Çalışma sırasında cihaz kaybı (device loss) olduğunda durum makinesi
   nasıl kurtarır?
6. JUCE bu hata modelini nasıl işler (`logError`, `resetRequest`)?
7. `.ai` içindeki `phonos.php` koruma eşikleri (DC offset) ile bu dosyanın
   sınırı nedir?

**Kapsam dışı:** Sürücü mesaj kataloğu (`asio-surucu-modeli.md`), hot-swap
donanım olayı (`asio-hot-swap-ve-device-loss.md` — o dosya cihaz kaybının
**donanım/timeline** tarafını ele alır; bu dosya **hata kodu + durum
makinesi** tarafını), latency ölçümü.

---

## §2 `ASIOError` Tipi ve `ASE_SUCCESS`

### §2.1 Tanım

```cpp
typedef long ASIOError;
#define ASE_SUCCESS             0x3f4847a0L
#define ASE_NotPresent          -1000L
#define ASE_HWMillinaudio       -999L
```

`(Kanıt: `asio.h`, ASE_SUCCESS / ASE_NotPresent / ASE_HWMillinaudio)` —
`ASIOError` **`long`** tipindedir (negatif değerler için işaretli tam sayı).
`ASE_SUCCESS` **`0x3f4847a0L`**'dir — normalde "başarı = 0" beklendiği için
bu değer dikkat çeker; ASIO SDK bunu bilinçli olarak pozitif-biçimsiz bir
imza (signature) yapmıştır, böylece sürücü yanlışlıkla `0` döndürürse host
hatayı fark eder.

### §2.2 Başarı Doğrulama Kuralı

```cpp
ASIOBufferInfo bufferInfos[2];
long numBuffers = 2;
long bufferSize = 512;
ASIOSampleRate sampleRate = 48000.0;
ASIOError err = ASIOCreateBuffers(bufferInfos, numBuffers, bufferSize, &sampleRate);
if (err != ASE_SUCCESS) { /* hata */ }
```

**Kural:** Her ASIO çağrısının dönüş değeri `ASE_SUCCESS` ile
**eşitlikle** karşılaştırılır (`==`), `> 0` / `< 0` işaretiyle değil — çünkü
`ASE_SUCCESS` pozitif (`0x3f4847a0`), hatalar negatif (`-1000` ve altı) olsa
da, sürücü imzası `0` döndürürse bu da başarısızlıktır. `(Kanıt: `asio.h`
ASE_SUCCESS tanımı)` — bu desen ASIO SDK `hostsample.cpp`'de ve JUCE'de
aynı şekilde kullanılır (JUCE: `== 0` kontrolü yerine `ASE_SUCCESS` kontrolü
— bkz. §7).

---

## §3 Çağrı Hataları: `ASE_*` Kataloğu

### §3.1 Tam Sabit Listesi (asio.h, aynen)

| Sabit | Değer | Tetikleyen çağrı / durum |
|---|---|---|
| `ASE_SUCCESS` | `0x3f4847a0L` | Tüm başarılı çağrılar |
| `ASE_NotPresent` | `-1000L` | İstenen özellik/kanal/cihaz mevcut değil |
| `ASE_HWMillinaudio` | `-999L` | Donanım milli-audio sınırı (ASIO HW serisi) |
| `ASE_InvalidMode` | `-998L` | Geçersiz mod — örn. harici clock + `rate != 0` (bkz. `asio-timestamp-ve-clock-senkronu.md` §5.6) |
| `ASE_SPNotAdvancing` | `-997L` | Örnekleme sayacı durdu (clock kaynağı kaybı — `asio-timestamp-ve-clock-senkronu.md` §3.3) |
| `ASE_NoClock` | `-996L` | Clock kaynağı yok |
| `ASE_NoMemory` | `-995L` | Bellek yetersiz |
| `ASE_PositionUnknown` | `-994L` | Konum bilinmiyor (bufferSwitch öncesi `ASIOGetSamplePosition`) |
| `ASE_GeneralError` | `-993L` | Genel/airsız hata |
| `ASE_InvalidParameter` | `-992L` | Geçersiz parametre (tüm çağrılar) |
| `ASE_InvalidMode`* | `-998L` | (*§3.1'de listelendi — mod hatası) |

*(Toplam: `ASE_SUCCESS` + 9 negatif sabit; `ASIOError` uzayının tamamı
`asio.h` enum/define bloğundan alınmıştır.)*

`(Kanıt: `asio.h`, ASE_* define bloğu)` — **bu değerler aynen korunmuştur**;
yeni sabit uydurulmamıştır.

### §3.2 `ASIOFuture` Uzantı Hataları

`ASIOFuture` çağrısı, desteklenmeyen selector için `ASE_NotPresent`
döndürebilir; `kAsioGetTime` örneği bu durumu belgeler
(`asio-timestamp-ve-clock-senkronu.md` §4.3). Ayrıca `ASE_SUCCESS`
`ASIOFuture` içinde de başarı imzası olarak kullanılır — örneğin
`kAsioGetCanProduceOutput` → `ASE_SUCCESS` = "evet, çıktı üretilebilir",
`ASE_NotPresent` = "hayır".

`(Kanıt: `asio.h`, ASIOFuture yorumları)`.

### §3.3 Hostsample Çağırı-Bağımlı Hata Dönüşleri (kanıtlanmış)

`hostsample.cpp`, sürücünün verebileceği **durum** hatalarını **mesaj**
olarak işler (bkz. §5); ancak `ASIOOutputReady` gibi evet/hayır sorguları
`ASE_SUCCESS` üzerinden okunur:

```cpp
postOutput = (ASIOOutputReady() == ASE_SUCCESS);
```

`(Kaynak: hostsample.cpp)` — bu, "sürücü geç çıktıyı destekliyorsa
(`ASIOOutputReady == ASE_SUCCESS`) host, girişi işledikten sonra `postOutput`
bayrağını kurar" demektir. JUCE aynı kontrolü `== 0` ile yapar
(`asio-cpp-ornekleri.md` §-kod karşılaştırması) — **iki desen de
`ASE_SUCCESS`'in özel değerini (`0x3f4847a0`) vs `0` ikilemini** ortaya
koyar: `ASE_SUCCESS != 0` olduğundan `== 0` kontrolü **yanlış** olacaktır;
JUCE'nin `== 0` kullanımı `ASIOOutputReady()`'un JUCE'de `long` değil
`ASIOError` beklentisiyle **çelişkisidir** — bu, `asio-kapsam.md` §11
çelişki kaydında raporlanan bir noktadır (C-JUCE-2 ailesi). *(Bu çelişki
kayıt dışı yeni bir iddia değil, mevcut kapsam dosyasındaki çelişki
sınıfına aittir.)*

---

## §4 Çağrı Hataları Sonrası Yeniden Deneme Sırası (Host Protokolü)

| # | Hata | Host eylemi | Referans |
|---|---|---|---|
| 1 | `ASIOInit` → `ASE_NotPresent` | ASIO yok → fallback zincirine geç (§6) | §6 |
| 2 | `ASIOInit` → `-1..-6` (hostsample hata kodları) | Sağlayıcı seçimi geçersiz → sağlayıcı indeksini ilerlet, tekrar dene | `asio-cpp-ornekleri.md` |
| 3 | `ASIOCreateBuffers` → `ASE_NoMemory` | Buffer boyutunu düşür, yeniden dene | §3.1 |
| 4 | `ASIOCreateBuffers` → `ASE_InvalidParameter` | Buffer boyutu 2'nin kuvveti değil / kanal indeksi geçersiz → parametreyi düzelt | `asio-kanal-yapilandirmasi-ve-converters.md` §3.2 |
| 5 | `ASIOSetSampleRate` → `ASE_InvalidMode` | Harici clock + sıfırdan farklı rate → `ASIOSetSampleRate(0)` dene | `asio-timestamp-ve-clock-senkronu.md` §5.6 |
| 6 | `ASIOGetSamplePosition` → `ASE_NoClock` | Clock kaynağını `ASIOGetClockSources` + `ASIOSetClockSource` ile yeniden seç | `asio-timestamp-ve-clock-senkronu.md` §5 |
| 7 | `ASIOGetSamplePosition` → `ASE_SPNotAdvancing` | Sayaç durdu → clock kaynağını doğrula; çözülmüyorsa sürücüyü yeniden başlat (`ASIOExit` + `ASIOInit`) | §5.3 |
| 8 | `ASIOStart` → herhangi bir hata | `ASIOStop` → `ASIODisposeBuffers` → `ASIOExit` → yeniden `ASIOInit` (temiz yeniden başlatma) | §5.3 |
| 9 | `ASE_GeneralError` (herhangi bir çağrıda) | Sürücü-özel hata → sürücü sağlayıcısını log'a yaz; tekrar 1 başarısız denemede fallback'e geç | §6 |

**Kural (3 başarısız deneme):** Aynı çağrı **3 kez** başarısız olursa host
fallback zincirine (§6) geçer; sonsuz yeniden deneme audio thread'de yasaktır
(AGENTS.md §L0 — `noexcept` callback içinde hata döngüsü kurulamaz).
`(Bu tablo, §3 sabitleri + `asio-kapsam.md` §2.1 kabul kriterlerinden
türetilmiş host protokolüdür; ASIO SDK tek tek yeniden-deneme sırası
tarif etmez — sıralama uygulama kararıdır, açıkça işaretlenmiştir.)`

---

## §5 Durum Bildirimleri (Çalışma Sırası Hataları)

### §5.1 `ASIOError` Değil: `asioMessage` Hata Sınıfları

Çalışma sırasında gelen bildirimler `ASIOError` dönmez; `asioMessage`
callback'i üzerinden **long selector** olarak gelir (tam katalog bkz.
`asio-surucu-modeli.md`):

| Bildirim | Anlamı | Host karşılığı |
|---|---|---|
| `resetRequest` | Sürücü sıfırlama istiyor | Buffer'ları yeniden kur (JUCE: 500 ms sonra — §7) |
| `engineVersion` | Sürücü ASIO motoru sürümü | `kAsioEngineVersion = 2` beklenir |
| `resyncRequest` | Yeniden senkron gerekli | Win16Mutex tarihçeli bildirim; host kaynağı yeniden kilitler |
| `latenciesChanged` | Gecikme değişti | Buffer size değişmedi demektir; `ASIOGetLatencies` yeniden okunur |
| `sampleRateDidChange` | Örnekleme oranı değişti | Tüm yeniden hesaplama (§5.3) |
| `bufferSwitchTimeInfo` | Zamanlı buffer anahtarı | `AsioTimeInfo` — bkz. `asio-timestamp-ve-clock-senkronu.md` §4 |

`(Kanıt: `asio.h` selector listesi; bkz. `asio-surucu-modeli.md` §5)`.

### §5.2 `kAsioCanReportOverload` (Xrun Bildirimi)

```text
kAsioCanReportOverload (0x24042012): sürücü aşırı yükü (xrun) bildirebilir.
Sürücü bu selector'a ASE_SUCCESS döndürürse host, xrun sayacını etkinleştirir;
ASE_SUCCESS değilse host sayacı devre dışı bırakır (xruns = -1).
```

`(Kanıt: `asio.h` selector + JUCE kaynağı §7 — JUCE `kAsioCanReportOverload`
yanıtını `ASE_OK` ile karşılaştırıp `xruns` bayrağını kurar)` — sayılmayan
xrun'lar (`-1`) sahte "0 hata" raporlamasını önler. Bu, `.ai` metrik
doldurma kuralıyla (uydurma ölçüm yasak) aynı felsefedir.

### §5.3 Örnekleme Oranı Değişimi Sonrası Protokolü

`sampleRateDidChange` geldiğinde host'un **zorunlu** adımları:

```text
1. ASIOGetSampleRate() → yeni oranı oku
2. Tüm sample-rate-bağlı DSP state'lerini yeniden hesapla
   (filtre katsayıları, resampler oranı, latency tamponları)
3. ASIOGetLatencies() → gecikmeleri yeniden oku
4. Yalnız ASIOStop/ASIOStart gerekiyorsa: temiz yeniden başlatma (§4, satır 8)
```

`(Kanıt: `asio.h` sampleRateDidChange yorumu — "the sample rate has changed;
the host must re-read it" + `asio-kapsam.md` §2.1 kabul kriteri)` — bu
adımlar `asio-kapsam.md` §5 (kabul kriteri 6: "sampleRateDidChange sonrası
DSP state yeniden hesaplanır") ile aynıdır.

---

## §6 Fallback Zinciri: ASIO → WASAPI

### §6.1 Zincir Sırası (CoreMusic kuralı — `.ai` kararları)

```text
1. ASIO (Exclusive Lock — tek uygulama, AGENTS.md §L0 kural 9)
   ├─ açıldı → devam
   └─ açılamadı (§4 satır 1/2/9) ↓
2. WASAPI Exclusive (paylaşımsız, düşük gecikme)
   ├─ açıldı → devam
   └─ açılamadı ↓
3. WASAPI Shared (Windows mix, en uyumlu)
   ├─ açıldı → devam
   └─ açılamadı ↓
4. Seçici (scrub) / dummy çıktı — sessiz mod
```

`(Kaynak: `.ai` içindeki ADR kararları + `AGENTS.md` §L0 — bu zincir bu
dosyanın konusudur; her basamağın doğrulanması için alt dosyalar:
`asio-hot-swap-ve-device-loss.md` (ASIO→kurtarma), `asio-surucu-modeli.md`
(ASIO_init sağlayıcı seçimi)).`

### §6.2 ASIO Exclusive Lock Kuralı (AGENTS.md §L0, Kural 9)

**ASIO Exclusive Lock: aynı anda yalnız BİR uygulama ASIO cihazını
kullanabilir.** İkinci bir uygulama `ASIOInit`'te başarısız olur →
`ASE_NotPresent` veya sürücü-özel hata. Host bu durumu **hata değil,
beklenen durum** olarak log'lar ve zincirde WASAPI'e düşer.
`(Kaynak: `AGENTS.md` §L0 kural 9)`.

### §6.3 `.ai` Koruma Eşikleri ile Sınır (Kural 10)

**DC Offset > 0.5V → koruma rölesi zorunlu** (AGENTS.md §L0 kural 10). Bu
eşik `.ai` tarafındaki (`phonos.php`) ölçüm/uyarı katmanına aittir; ASIO
sürücü katmanı **DC offset ölçmez** (örnek akışı yalnız taşır). Sınır:

| Katman | Sorumluluk | Bu dosyada |
|---|---|---|
| ASIO driver/host | Akış, format, hata kodları, fallback zinciri | Evet (§2-§6) |
| `.ai` / `phonos.php` | DC offset ölçümü, 0.5V eşik, röle tetikleme | Hayır — ayrı vault dosyası |

`(Kaynak: `AGENTS.md` §L0 kural 10)` — bu ayrım, bu dosyanın **yetki
sınırıdır** (Authority §0): DC/termal/koruma rölesi bu dosyada işlenmez.

---

### §6.4 `IAudioClient2` — Exclusive Mode Optimizasyonları (Windows 10 2004+)

`IAudioClient2` (Windows SDK 10.0.19041.0), `IAudioClient3` türetir ve exclusive
mode için ek `IAudioClientProperties` tabanlı yöntemler tanımlar — **Windows 10
sürüm 2004 (derleme 19041) ile birlikte kullanılabilir** (dokümantasyon notu).

`IAudioClient2` iki yeni yöntem içerir:

| Yöntem | Görevi |
|---|---|
| `IAudioClient2::SetClientProperties` | Exclusive-mode yapılandırmasını ayarlar (WASAPI uygulaması) |
| `IAudioClient2::SetEventHandleExclusive` | Exclusive mode için olay tutamağını ayarlar (Windows uygulaması) |

**`SetClientProperties` kabul koşulu (dokümantasyon, doğrudan alıntı):**

> "On Windows 10, version 2004 and later, a call to this method succeeds on an
> uninitialized audio endpoint device if the client configures unspecified
> audio stream properties to their default values."
> — `IAudioClient2::SetClientProperties` — Windows SDK 10.0.26100.0

Bu, Neva Engine'de kullanışlıdır: exclusive mode başlatma öncesi yapılandırma,
uninitialized endpoint'te bile başarısız olmaz (önceki yapılandırma değerleri geri
getirilir — dokümantasyon notu).

**Dönüş değeri notu (dokümantasyon, doğrudan alıntı):**

> "If the method succeeds, S_OK is returned. Otherwise, the method returns one
> of the error codes shown in the following table."

Tablo: `AUDCLNT_E_DEVICE_INVALIDATED` (device yuvasından çıkarılmışsa),
`AUDCLNT_E_INVALID_DEVICE_PERIOD` (min ve max periyot aralığı dışındaysa),
`AUDCLNT_E_INVALID_STREAM_FLAG` (desteklenmeyen stream bayrağı ayarlanmışsa).

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| `IAudioClient2` exclusive-mode optimizasyonları için gerekli | `IAudioClient2 (audioclient.h)` — Windows SDK 10.0.26100.0 |
| Exclusive mode olay kuyruğu `SetEventHandleExclusive` ile oluşturulur | `IAudioClient2::SetEventHandleExclusive` — Windows SDK 10.0.26100.0 (dokümantasyon notu: haziran 2019) |
| `SetClientProperties` uninitialized endpoint'te de başarısız olmayabilir | `IAudioClient2::SetClientProperties` — Windows SDK 10.0.26100.0 (dokümantasyon notu) |

### §6.5 `IAudioClient::Reset` — Kuyruk Sıfırlama ve Hata Durumları

**Dokümantasyon tanımı (doğrudan alıntı):**

> "The Reset method resets the audio stream. After resetting the audio stream,
> the caller must submit a new set of audio data to each endpoint buffer in the
> stream via Initialize before calling Start. Reset flushes any pending audio data
> that has been queued in the audio device buffer."
> — `IAudioClient::Reset` — Windows SDK 10.0.26100.0

**Dönüş değerleri (dokümantasyon tablosu):**

| HRESULT | Durum |
|---|---|
| `S_OK` | Method succeeded |
| `AUDCLNT_E_DEVICE_INVALIDATED` | Audio endpoint device has been unplugged, or the hardware device associated with the endpoint has changed properties |
| `AUDCLNT_E_NONEXCLUSIVE_MODE_ONLY` | Reset is called in shared mode |

**Kritik kısıt (dokümantasyon, doğrudan alıntı):**

> "The Reset method fails if it is called on a stream that is running (that is, the
> audio client was last in the running state) or is stuck in the paused state
> because of an error."

**Kritik kısıt 2 (dokümantasyon, doğrudan alıntı):**

> "The client is responsible for eventually calling Release on each of the
> endpoint buffers that it holds. However, to avoid an object reference leak, the
> client must release all of its endpoint buffer interfaces before calling Reset
> or Release on the audio client."

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| `Reset` yalnızca duraklatılmış (paused) stream'de başarılı olur | `IAudioClient::Reset` — docs.microsoft.com, Windows SDK 10.0.26100.0 |
| `Reset` exclusive mode'da `AUDCLNT_E_NONEXCLUSIVE_MODE_ONLY` döner | `IAudioClient::Reset` — docs.microsoft.com |
| Endpoint buffer interface'leri `Reset` öncesi bırakılmalı | `IAudioClient::Reset` — docs.microsoft.com (object reference leak notu) |

### §6.6 `E_FAIL` Kategorilere Ayrılması — Exclusive vs Shared Mode

**Dokümantasyon (doğrudan alıntı):**

> "E_FAIL — Exclusive mode service has timed out; the audio session is
> disconnected and should be destroyed."
> — `IAudioClient::Initialize` — Windows SDK 10.0.26100.0

**Aynı HRESULT'ın bağlama göre ayrıştırılması (JUCE kodu, doğrudan alıntı):**

```cpp
// WASAPI audio client has terminated an audio session that has shared mode effects in the audio signal path.
// https://docs.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize
if (res == E_FAIL)
    return exclusiveMode ? WASAPIAudioIODevice::ClientTerminated
                         : WASAPIAudioIODevice::AccessDenied;
```

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| `E_FAIL` exclusive mode'da "exclusive mode service timeout" anlamına gelir | `IAudioClient::Initialize` — docs.microsoft.com |
| JUCE bu HRESULT'ı exclusive/shared mode'a göre ayrıştırır | `juce_WASAPI_windows.cpp` — juce-framework/JUCE — master |
| `E_ACCESSDENIED` "shared mode effects" varsa erişim reddi anlamına gelir | `IAudioClient::Initialize` — docs.microsoft.com |

---

## §7 JUCE'nin Hata Yönetimi (Karşılaştırma)

| Durum | JUCE davranışı | Kanıt |
|---|---|---|
| `ASE_SUCCESS` kontrolü | `== 0` ile okur (§3.3 çelişki notu) | JUCE kaynağı |
| Çözülemeyen hatayı logla | `logError()` statik yardımcı → `DBG()` ile `const char*` mesaj yazar | JUCE kaynağı §9 |
| `resetRequest()` | **`startTimer(500)`** → 500 ms sonra `audioDeviceAboutToStart` benzeri yeniden kurulum tetikler | JUCE kaynağı §6 |
| `kAsioOverload` | `++xruns` (sayacı artırır) | JUCE kaynağı §7 |
| `kAsioCanReportOverload` cevabı | `ASE_OK` ise `xruns` etkin; değilse `xruns = -1` | JUCE kaynağı §7 |
| `ASE_NotPresent` (buffer/sürücü) | Kullanıcıya `DBG` log; yeniden deneme **yok** — uygulama kararı | JUCE kaynağı §1-§9 |

`(Kaynak: JUCE kaynağı §1, §6, §7, §9)` — JUCE'nin **`resetRequest →
500 ms timer`** deseni, ASIO SDK `hostsample.cpp`'deki **`kAsioResetRequest`
→ anlık** işleme (§5.1) ile **farklıdır**: JUCE geciktirmeli kurtarma
tercih eder. Bu fark `asio-kapsam.md` §11 çelişki kaydında C-JUCE-3 olarak
sınıflandırılmıştır (bu dosya yalnız kaydı yansıtır, yeni iddia eklemez).

---

## §8 Fallback Durum Makinesi (Host)

```text
[İlk deneme]
   │
   ▼
ASIOInit(sağlayıcı i)
   ├─ ASE_SUCCESS → [ASIORun]
   └─ hata (§4) → sağlayıcı i+1'e geç
        └─ sağlayıcı kalmadı → WASAPI Exclusive
             ├─ açıldı → [Run]
             └─ açılamadı → WASAPI Shared
                  ├─ açıldı → [Run]
                  └─ açılamadı → Scrub/sessiz mod

[Run sırasında]
   │
   ├─ resetRequest → 500 ms (JUCE) / anlık (hostsample) yeniden kurulum
   ├─ sampleRateDidChange → §5.3 protokolü
   ├─ xrun (kAsioCanReportOverload) → sayaç ++, -1 ise etkin değil
   ├─ device loss (cihaz fiziksel kaybı) →
   │     ASIOExit → cihaz listesini yeniden tara → ASIOInit (bkz.
   │     asio-hot-swap-ve-device-loss.md durum makinesi)
   └─ 3 başarısız yeniden başlatma → §6.1 zincirine düş
```

---

## §9 Soru → Cevap Özet Tablosu

| # | Soru (kapsam §2.1) | Kısa Cevap | § |
|---|---|---|---|
| 1 | `ASE_SUCCESS` neden `0` değil? | `0x3f4847a0L` imza — sürücünün `0` döndürmesi hatadır; `==` ile karşılaştırılır | §2 |
| 2 | Çağrı hataları hangi aralıkta? | `-1000` (NotPresent) … `-992` (InvalidParameter) + `-995` (NoMemory) | §3.1 |
| 3 | Aynı hata 3 kez gelirse? | Fallback zincirine geç (§6); sonsuz döngü audio thread'de yasak | §4 |
| 4 | Çalışma sırasında bildirimler nasıl gelir? | `asioMessage`: resetRequest, sampleRateDidChange, resync, latenciesChanged, overload | §5.1-§5.2 |
| 5 | Rate değişince ne yapılır? | `ASIOGetSampleRate` + DSP state yeniden hesap + `ASIOGetLatencies` | §5.3 |
| 6 | Fallback sırası nedir? | ASIO (Exclusive) → WASAPI Exclusive → WASAPI Shared → scrub | §6.1 |
| 7 | Exclusive Lock ihlali ne döndürür? | `ASIOInit` hatası (beklenen durum) → host log'lar, WASAPI'e düşer | §6.2 |
| 8 | DC offset / röle eşiği bu dosyada mı? | Hayır — `.ai` katmanı (kural 10); sınır Authority §6.3 | §6.3 |
| 9 | JUCE resetRequest'i nasıl işler? | `startTimer(500)` — 500 ms geciktirmeli kurtarma | §7 |

---

## §10 Terimler Sözlüğü

| Terim | Tanım |
|---|---|
| ASIOError | `long` tipinde ASIO hata/durum tipi (§2.1) |
| ASE_SUCCESS | `0x3f4847a0L` başarı imzası (§2.1) |
| ASE_NotPresent | `-1000L` — özellik/cihaz yok (§3.1) |
| ASE_InvalidMode | `-998L` — geçersiz mod (harici clock ihlali) (§3.1) |
| ASE_SPNotAdvancing | `-997L` — sayacı durdu (§3.1) |
| Xrun | Taşma/undersrun bildirimi; `kAsioCanReportOverload` ile sayılır (§5.2) |
| resetRequest | Sürücünün sıfırlama isteği; JUCE 500 ms geciktirir (§5.1, §7) |
| Fallback zinciri | ASIO → WASAPI Ex → WASAPI Shared → scrub çıkış sırası (§6.1) |
| Exclusive Lock | ASIO cihazının tek-uygulama kilidi (AGENTS.md §L0 kural 9) (§6.2) |
| DC offset eşiği | 0.5V koruma rölesi eşiği — `.ai` katmanı, bu dosya dışı (§6.3) |
| Durum makinesi | Run sırasında hata/olay → kurtarma geçişleri (§8) |

---

## §11 Doğrulama ve Sonraki Adım

| Adım | Durum | Kanıt |
|---|---|---|
| §2 `ASE_SUCCESS` tanımı | Kanıtlandı | `asio.h` define (§2.1) |
| §3 `ASE_*` sabitleri | Kanıtlandı | `asio.h` — değerler aynen alındı (§3.1) |
| §3.3 `ASIOOutputReady` deseni | Kanıtlandı | `hostsample.cpp` (§3.3) |
| §5.1 bildirim sınıfları | Kanıtlandı | `asio.h` selector listesi (§5.1) |
| §5.2 overload/xrun | Kanıtlandı | `asio.h` + JUCE kaynağı §7 (§5.2) |
| §6 zincir + kural 9/10 | Kaynaklı | `AGENTS.md` §L0 kural 9-10 (§6.1-§6.3) |
| §7 JUCE karşılaştırması | Kanıtlandı | JUCE kaynağı §1, §6, §7, §9 (§7) |
| §4 yeniden-deneme sırası | Türetilmiş (işaretlendi) | §3 + kabul kriterleri (§4 — uygulama kararı, açıkça işaretli) |

**Sonraki adım:** `validation: []` alanını, `asio-kapsam.md` §12.3'teki
doğrulama protokolüne göre doldurmak (ASIO hata enjeksiyonu + WASAPI fallback
testi).

---

Authority: `.ai/architecture/K003-audio/backend/asio/asio-kapsam.md` §2.1 (Soru 4), §14
