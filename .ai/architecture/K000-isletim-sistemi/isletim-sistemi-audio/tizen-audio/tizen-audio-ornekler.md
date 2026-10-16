---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-sound-manager
evidence:
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/raw-audio.md
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/sound-manager.md
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__IN__MODULE.html
  - https://docs.tizen.org/application/dotnet/api/TizenFX/API12/api/Tizen.Multimedia.AudioManager.html
  - https://samsungtizenos.com/docs/web/guides/multimedia/audio/
validation:
  - "2026-10-13 · GÖVDE-GET: raw-audio.md 624 satır + sound-manager.md 541 satır indirildi — bu dosyadaki TÜM kod blokları resmi rehberlerden birebir alıntıdır (etiket satırı her blokta)"
  - "2026-10-13 · GÖVDE-GET (ikinci dalga): 10.0 Audio Out (19 fn) + Audio In (21 fn) statik HTML modülleri indirildi — §2.5 çelişkilerinin tamamı kapatıldı (Ç1/Ç2/Ç3 · E-Q1/E-Q2/E-Q3/E-Q5)"
open_questions:
  - "E-Q3 ✅ KAPANDI: drain = tampon boşaltma-bekleme (son çalma) · flush = tamponu atma — [M1] birebir"
  - "CoreMusic reposunda Tizen CAPI kodu YOK → UNKNOWN"
group: kod
---

# tizen-audio-ornekler — PCM Çağrı Dizileri ve Resmi Rehber Örnekleri

> **Bu dosya ne değildir:** Özgün yazılmış örnek kod içermez. Buradaki her kod
> bloğu **Samsung/tizen-docs resmi rehberlerinden birebir alıntıdır** ve başında
> kaynak-etiketi vardır. Uydurma kod yasaktır.

**ELI15:** Tizen'de senkron ses çalmak beş adımdır: oluştur (`create_new`) →
bağlamı tak (`set_sound_stream_info`) → hazırla (`prepare`) → yaz (`write`) →
kapanış (`unprepare`/`destroy`). Asenkron yol ise callback'e geçer: sen veriyi
değil, sistem seni çağırır (`set_stream_cb`).

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-ornekler` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio` |
| Kademe | 5 · yaprak-modül |
| group | `kod` |
| Doğrulama tarihi | 2026-10-13 |
| Kanıt yöntemi | **Birincil: gövde-GET** — tüm kod blokları resmi rehber alıntısı |

## §1.1 Kaynaklar

| Etiket | Kaynak | URL | Durum |
|---|---|---|---|
| [R1] | Raw Audio Playback and Recording rehberi | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/raw-audio.md | ✅ GET 200 · 624 satır |
| [R2] | Sound Manager rehberi | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/sound-manager.md | ✅ GET 200 · 541 satır |
| [M1] | 10.0 Audio Output API modülü (statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html | ✅ GET 200 · 80 KB · 19 fn |
| [M2] | 10.0 Audio Input API modülü | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__IN__MODULE.html | ✅ GET 200 · 95 KB · 21 fn |
| [A1] | Audio Output API sayfası (5.0) | https://docs.tizen.org/iot/api/5.0/tizen-iot-headed/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html | ➖ [M1] lehine |
| [A2] | .NET AudioManager (API12) | https://docs.tizen.org/application/dotnet/api/TizenFX/API12/api/Tizen.Multimedia.AudioManager.html | ⚠️ indeks alıntısı |
| [S1] | Web Audio Management | https://samsungtizenos.com/docs/web/guides/multimedia/audio/ | ⚠️ indeks alıntısı |

## §1.2 Repo Kod Kanıtı (CoreMusic)

CoreMusic deposunda Tizen/CAPI ses kodu **YOK** (0 isabet) → bu dosya gelecek-yüzey
tarifidir; etiksiz eşleme **yapılmaz**.

---

## §2 PCM Çağrı Dizileri (rehbere göre düzeltilmiş — gövde-GET)

### §2.1 Senkron Çalma (Audio Output — [R1] §Managing Synchronous Playback)

| # | Adım | Fonksiyon | Rehber notu (birebir) |
|---|---|---|---|
| 0 | başlık | `#include <audio_io.h>` + `#include <sound_manager.h>` | [R1] §Prerequisites |
| 1 | oluştur | `audio_out_create_new(SAMPLE_RATE, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &output)` | hata tipi `audio_io_error_e` |
| 2 | bağlam tak | `audio_out_set_sound_stream_info(output, stream_info)` | rota+seviye (R2 efekt örneklerinde `audio_in_` tarafında görülür) |
| 3 | hazırla | `audio_out_prepare(output)` | "starts the hardware playback process" |
| 4 | yaz | `int bytes_number = audio_out_write(output, buffer, buffer_size)` | *"returned value represents the number of bytes written ... A negative value represents an error code"* |
| 5 | duraklat/devam | `audio_out_unprepare(output)` ile akışı durdur / güç tasarrufu | "The device cannot go to sleep while in the PLAYING state." |
| 6 | kapanış | `audio_out_unprepare(output)` → `audio_out_destroy(output)` | — |

### §2.2 Asenkron Çalma ([R1] §Managing Asynchronous Playback)

| # | Adım | Fonksiyon |
|---|---|---|
| 1 | callback kaydı | `audio_out_set_stream_cb(output, _audio_io_stream_write_cb, NULL)` |
| 2 | callback imzası | `void cb(audio_out_h handle, size_t nbytes, void *userdata)` |
| 3 | hazırla + yaz | `audio_out_prepare` → callback içinde `audio_out_write(handle, buffer, nbytes)` |
| 4 | durdur | `audio_out_unprepare(output)` — "The device no longer invokes the callback function." |
| 5 | callback kaldır | `audio_out_unset_stream_cb(output)` |
| 6 | kapanış | `audio_out_destroy(output)` |

### §2.3 Senkron Kayıt (Audio Input — [R1] §Managing Synchronous Recording)

| # | Adım | Fonksiyon | Rehber notu |
|---|---|---|---|
| 1 | oluştur | `audio_in_create(SAMPLE_RATE, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &input)` | §Prerequisites |
| 2 | tampon ölç | `audio_in_get_buffer_size(input, &buffer_size)` | önerilen boyut "sound server (such as PulseAudio)" gelir · cihaza göre değişir |
| 3 | hazırla | `audio_in_prepare(input)` | iç tampon dolar |
| 4 | oku | `int bytes_number = audio_in_read(input, buffer, buffer_size)` | **3 parametre + int dönüş** · negatif = hata |
| 5 | kapanış | `audio_in_unprepare(input)` → `audio_in_destroy(input)` | — |

> `audio_in_read` davranışı (birebir): prepare sonrası hemen çağrılırsa **tampon
> dolana kadar bloklar**; iç tampon zaten doluysa anında döner.

### §2.4 Asenkron Kayıt ([R1] §Managing Asynchronous Recording)

| # | Adım | Fonksiyon |
|---|---|---|
| 1 | callback kaydı | `audio_in_set_stream_cb(input, _audio_io_stream_read_cb, NULL)` — **prepare'dan ÖNCE** ("otherwise the callback function is never invoked") |
| 2 | callback imzası | `void cb(audio_in_h handle, size_t nbytes, void *userdata)` |
| 3 | callback gövdesi | `audio_in_peek(handle, &buffer, &nbytes)` → `fwrite(...)` → `audio_in_drop(handle)` |
| 4 | durdur | `audio_in_unprepare` · `audio_in_unset_stream_cb` · `audio_in_destroy` |

### §2.5 Çelişkili eski dizinin kaydı (silinmez — statüsü düşürülür)

| Eski indeks-alıntısı adımı ([A1]) | Rehber + 10.0 API gerçeği ([R1] · [M1]/[M2]) | Statü |
|---|---|---|
| `audio_out_create_new(type, stream_info, buffer_size, &handle)` | `audio_out_create_new(sample_rate, channels, sample_type, &handle)` — [M1] 10.0 resmi imza üçüncü kez doğruladı | ❌ **KAPANDI** — eski biçim transkripsiyon hatası (anayasa §7 Ç1) |
| `audio_out_set_parameter(handle, rate, channels, type)` | [M1] 10.0 fonksiyon listesinde (19 fn) **YOK** — parametreler yalnızca `create_new`'ta verilir | ❌ **KAPANDI** — fonksiyon hiç var olmamış (Ç3) |
| `audio_in_read(handle, buffer, size, &bytes_read)` | `audio_in_read(handle, buffer, size)` → `int` — [M2] 21 fn listesi resmi | ❌ **KAPANDI** (Ç2) |
| `audio_out_prepare/write/drain/destroy` | `prepare/write/unprepare/destroy` · `drain` ve `flush` [M1]'de **var** (Since 2.4) — rehber yalnız göstermiyor | ⚠️ → ✅ `drain` gerçek fonksiyon; rehberdeki eksik gösterim |

---

## §3 Parametre Aralıkları ([R1] birebir — gövde-GET)

| Parametre | Değerler | Kaynak |
|---|---|---|
| Kanal (`audio_channel_e`) | `AUDIO_CHANNEL_MONO` (1) · `AUDIO_CHANNEL_STEREO` (2) · `AUDIO_CHANNEL_MULTI_3` … `MULTI_8` (**yalnız kayıt için**) | [R1] §Audio Output |
| Örnek tip (`audio_sample_type_e`) | `AUDIO_SAMPLE_TYPE_U8` · `AUDIO_SAMPLE_TYPE_S16_LE` · `AUDIO_SAMPLE_TYPE_S24_LE` · `AUDIO_SAMPLE_TYPE_S24_32LE` · `AUDIO_SAMPLE_TYPE_S32_LE` | [R1] |
| Örnek hızı | **8000 ~ 192000 Hz** | [R1] (birebir) |
| Tercih edilen cihaz formatı | "signed 16-bit little endian, stereo, 44100 Hz" (örnek — düşük maliyet) | [R1] §guidelines |
| Önerilen tampon | `audio_in_get_buffer_size()` ≈ **100 ms** kaydın karşılığı (örnek cihazda); saniye = ×10 ×saniye | [R1] §Creating a Buffer |
| Elle hesap | `buffer_size = SAMPLE_RATE * (STEREO ? 2 : 1) * (S16_LE ? 2 : 1)` → ×RECORDING_SEC | [R1] (birebir) |
| Privilege (kayıt) | `http://tizen.org/privilege/recorder` | [A1] indeks alıntısı |
| Sürüm | "Since Tizen 2.4" | [R1] §Related Information |

**Düşük seviye cihaz kuralları ([R1] — birebir özet):** aşırı çok-örnekleme
kullanma · cihaz-tercihli format kullan · `create_new`/`destroy` çağrılarını
sıklaştırma (prepare/unprepare çok daha hızlıdır) · write tamponunu olay
gecikmesine göre ayarla · **double-buffering** ile gecikme düşür · uzun süre
çalmıyorsan `unprepare` ile güç tasarrufu.

---

## §4 Resmi Rehber Kod Örnekleri (birebir alıntı)

### §4.1 Ses seviyesi ([R2] §Controlling the Volume)

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Controlling the Volume]
int ret;
int cur_vol = 0;
int max_vol = 0;

ret = sound_manager_get_volume(SOUND_TYPE_MEDIA, &cur_vol);
ret = sound_manager_get_max_volume(SOUND_TYPE_MEDIA, &max_vol);
```

### §4.2 Cihaz listesi döngüsü ([R2] §Querying Sound Devices)

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Querying Sound Devices]
while ((_ret = sound_manager_get_next_device(list, &device)) == SOUND_MANAGER_ERROR_NONE) {
    ret = sound_manager_get_device_type(device, &type);

    if (type == SOUND_DEVICE_BLUETOOTH_MEDIA)
        /* Sound device type is Bluetooth Media */
    else if (type == SOUND_DEVICE_AUDIO_JACK)
        /* Sound device type is Audio Jack */

    ret = sound_manager_get_device_io_direction(device, &direction);

    if (direction == SOUND_DEVICE_IO_DIRECTION_BOTH)
        /* Sound device has both headphone and mic */
}
if (_ret == SOUND_MANAGER_ERROR_NO_DATA)
    /* End of the available devices */
```

### §4.3 USB cihazda hız seçimi ([R2])

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Querying Sound Devices — USB örneği]
sound_sample_rate_e *rates;
int num;

ret = sound_manager_get_supported_sample_rates(device, &rates, &num);
if (ret != SOUND_MANAGER_ERROR_NONE) {
    /* Failed to get the supported sample rates of this device */
} else {
    int i;
    for (i = 0; i < num; i++) {
        if (rates[i] == SOUND_SAMPLE_RATE_48000) {
            ret = sound_manager_set_sample_rate(device, rates[i]);
            break;
        }
    }
    free(rates);
}
```

### §4.4 Stream bağlamı + odak callback'i ([R2])

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Creating a Sound Stream Handle]
int error_code;
sound_stream_info_h stream_info;

error_code = sound_manager_create_stream_information(SOUND_STREAM_TYPE_MEDIA, sound_stream_focus_state_changed_cb,
                                                     NULL, &stream_info);
```

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Managing Stream Focus]
int error_code;
int behavior = SOUND_BEHAVIOR_NONE;

error_code = sound_manager_acquire_focus(stream_info, SOUND_STREAM_FOCUS_FOR_BOTH, behavior, NULL);
if (!error_code)
    /* Activate your sound stream */
```

### §4.5 Manuel rota — USB cihaz ekleme ([R2] §Controlling Manual Stream Routing)

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Controlling Manual Stream Routing]
ret = sound_manager_create_stream_information(SOUND_STREAM_TYPE_MEDIA_EXTERNAL_ONLY, focus_cb,
                                              cb_userdata, &stream_info);
ret = sound_manager_get_device_list(SOUND_DEVICE_IO_DIRECTION_IN_MASK, &device_list);
...
while (!(ret = sound_manager_get_next_device(device_list, &device))) {
    ret = sound_manager_get_device_type(device, &type);
    ret = sound_manager_get_device_io_direction(device, &direction);
    if (/* Device type is USB */) {
        ret = sound_manager_add_device_for_stream_routing(stream_info, device);
        ret = sound_manager_apply_stream_routing(stream_info);
        break;
    }
}
```

### §4.6 Kayıt efektleri — AEC ([R2] §Add sound effect methods)

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Add sound effect methods — AEC]
ret = sound_manager_create_stream_information(SOUND_STREAM_TYPE_MEDIA, NULL, NULL, &stream_info);
ret = sound_manager_get_device_list(SOUND_DEVICE_ALL_MASK, &device_list);

ret = sound_manager_get_next_device(device_list, &device)
ret = sound_manager_set_effect_method_with_reference(stream_info, SOUND_EFFECT_ACOUSTIC_ECHO_CANCEL_WEBRTC, device);

ret = audio_in_create(48000, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &input);
ret = audio_in_set_sound_stream_info(input, stream_info);
ret = audio_in_get_buffer_size(input, &size);
ret = audio_in_prepare(handle);

while (1)
    ret = audio_in_read(input, (void *)buffer, size);
```

> Not: alıntıdaki `audio_in_prepare(handle)` rehberin kendi yazımıdır (`input`
> olmalı) — **düzeltülmez**, alıntı birebir korunur ve bu notla işaretlenir.

### §4.7 NS + AGC birlikte ([R2])

```c
[ETİKET: birebir alıntı · kaynak: [R2] §Add sound effect methods — NS+AGC]
ret = sound_manager_create_stream_information(SOUND_STREAM_TYPE_MEDIA, NULL, NULL, &stream_info);
ret = sound_manager_set_effect_method(stream_info, SOUND_EFFECT_NOISE_SUPPRESSION_VOIP | SOUND_EFFECT_AUTOMATIC_GAIN_CONTROL_CAPTURE);

ret = audio_in_create(48000, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &input);
ret = audio_in_set_sound_stream_info(input, stream_info);
```

### §4.8 Asenkron yazma callback'i ([R1])

```c
[ETİKET: birebir alıntı · kaynak: [R1] §Starting Asynchronous Playback]
void
_audio_io_stream_write_cb(audio_out_h handle, size_t nbytes, void *userdata)
{
    char * buffer = NULL;

    if (nbytes > 0) {
        buffer = malloc(nbytes);
        memset(buffer, 0, nbytes);
        fread(buffer, sizeof(char), nbytes, fp_r);

        int data_size = audio_out_write(handle, buffer, nbytes);

        if (data_size < 0)
            dlog_print(DLOG_ERROR, LOG_TAG, "audio_out_write() failed! Error code = %d", data_size);

        free(buffer);
    }
}
```

### §4.9 Asenkron okuma callback'i — peek/drop ([R1])

```c
[ETİKET: birebir alıntı · kaynak: [R1] §Starting Asynchronous Recording]
void
_audio_io_stream_read_cb(audio_in_h handle, size_t nbytes, void *userdata)
{
    const void * buffer = NULL;

    if (nbytes > 0) {
        int error_code = audio_in_peek(handle, &buffer, &nbytes);
        if (error_code != AUDIO_IO_ERROR_NONE) {
            dlog_print(DLOG_ERROR, LOG_TAG, "audio_in_peek() failed! Error code = %d", error_code);
            return;
        }
        fwrite(buffer, sizeof(char), nbytes, fp_w);
        error_code = audio_in_drop(handle);
    }
}
```

---

## §5 Senkron vs Asenkron Karşılaştırması ([R1] kanıtlı)

| Boyut | Senkron (`read`/`write`) | Asenkron (`set_stream_cb`) |
|---|---|---|
| Akış | ana thread'i **bloklar** → ayrı thread (`ecore_thread_run`) önerilir | sistem callback'i çağırır |
| Tampon | yerel tampon uygulamanın | iç tampon + `peek`/`drop` (kayıt) · `nbytes` (çalma) |
| Durum | `prepare → read/write → unprepare` | callback kur → prepare → ... → unprepare → unset |
| Hata | dönüş `int`: negatif = hata | `AUDIO_IO_ERROR_NONE` kontrolü |

---

## §6 .NET ve Web Yüzeyi Eşlemesi (ikincil kanıt)

| Yüzey | Tip/Sayfa | Karşılığı (CAPI) | Kanıt |
|---|---|---|---|
| .NET | `Tizen.Multimedia.AudioManager` — `GetConnectedDevices()` · `DeviceAdded`/`DeviceRemoved` · `Deactivate()` | Sound Manager **Device** modülü | [A2] indeks alıntısı |
| Web | Audio Management rehberi | Web uygulamaları için ayrı ses yönetimi | [S1] indeks alıntısı |
| Native | `<audio_io.h>` + `<sound_manager.h>` | §2–§4 | [R1]/[R2] gövde-GET |

---

## §7 Ölçüm

| Metrik | Değer |
|---|---|
| Doğrulanan URL | 5 (2 gövde-GET · 2 indeks alıntısı · 1 SPA) |
| Çağrı dizisi adımı | 6 (senkron OUT) + 6 (asenkron OUT) + 5 (senkron IN) + 4 (asenkron IN) |
| Kod bloğu | **9 — tamamı resmi rehber alıntısı** (etiketli · uydurma 0) |
| Parametre satırı | 8 (sürüm-etiketli) |
| Çelişki düzeltmesi | 4 satır (§2.5) |
| CoreMusic kesişimi | 0 isabet → UNKNOWN |

---

## §8 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| E-Q1 | `audio_in_read` tam imzası? | ✅ KAPANDI | [R1]: `audio_in_read(input, buffer, buffer_size)` → `int` (negatif = hata) · [M2] resmi |
| E-Q2 | `audio_out_write` hata kodu listesi? | ✅ KAPANDI | `audio_io_error_e` enumu 13 kod (anayasa §6.3 · [M1]) |
| E-Q3 | `drain` vs `flush` farkı? | ✅ KAPANDI | [M1] 10.0 birebir: `drain` — *"waits until drains stream buffer completely. (e.g end of playback)"* · `flush` — *"Flushes and discards buffered audio data"* · ikisi de Since 2.4 |
| E-Q4 | 192000 Hz tüm cihazlarda destekli mi? | UNKNOWN | Rehber aralığı yazar, hw garantisi yazmaz |
| E-Q5 | `audio_out_set_parameter` gerçek mi? | ✅ KAPANDI | Hayır — [M1] 19 fonksiyonluk tam listede YOK; parametreler yalnız `create_new`'ta (§2.5) |

---

## §PHP Tizen × PHP (2026-10-14)

Tizen uygulama katmanı C (`sound_manager` §21) veya web/JS'tir — cihazda PHP runtime
**yoktur**. PHP'nin Tizen'e erişimi: sunucu PHP → HTTP → cihaz JS/C (§PHP yolları
kök §25.5 · `audio_out_*` C API'si PHP'den çağrılamaz).

---

**Geniş dil seti — Tizen × dil (kök §26):** `sound_manager` **C API'sidir** (§21) —
Tizen Native (C/C++) uygulamaları çağırır; Python/Go/Rust/C#/PHP/Node için Tizen'de
resmi toolchain/dil binding'i **bu turda doğrulanmadı** (⚠️ VERIFICATION REQUIRED).
Web uygulaması katmanı JS (Web Audio API — kök §26.2).

---

---

## §9 API Çağrı Zarfı Envanteri (dosya içi türetim — bu turda yeni GET yok)

**Yöntem (bağlayıcı):** aşağıdaki her satır bu dosyanın §2–§4'ünde **zaten kayıtlı** bir
alıntıdan türetilir; fonksiyon adı/imza bu §'de **ilk kez** iddia edilmez
(zero-hallucination). Kanıt zinciri: satır → `(§x.y)` → o §'nin gövde-GET alıntısı.

### §9.1 `audio_io` yüzeyi — 21 çağrı (§2 · §4.6 · §4.8 · §4.9)

| # | Çağrı | Rol | Kanıt (bu dosya) |
|---|-------|-----|------------------|
| 1 | `audio_out_create_new` | output yarat | §2.1 [1] · §2.5 (eski imza ❌ kapanmış) |
| 2 | `audio_out_set_sound_stream_info` | rota/seviye bağla | §2.1 [2] |
| 3 | `audio_out_prepare` | hardware başlat | §2.1 [3] |
| 4 | `audio_out_write` | senkron yaz (int döner) | §2.1 [4] · §4.8 callback içi |
| 5 | `audio_out_unprepare` | durdur + güç tasarrufu | §2.1 [5][6] · §2.2 [4] |
| 6 | `audio_out_destroy` | yok et | §2.1 [6] · §2.2 [6] |
| 7 | `audio_out_set_stream_cb` | asenkron yazma cb kaydı | §2.2 [1] |
| 8 | `audio_out_unset_stream_cb` | cb kaldır | §2.2 [5] |
| 9 | `audio_out_drain` | tamponu boşalt (Since 2.4) | §2.5 [4] — rehberde gösterim yok |
| 10 | `audio_out_flush` | tamponu at (Since 2.4) | §2.5 [4] · §8 E-Q3 |
| 11 | `audio_in_create` | input yarat | §2.3 [1] · §4.6 · §4.7 |
| 12 | `audio_in_set_sound_stream_info` | efekt bağla | §4.6 · §4.7 |
| 13 | `audio_in_get_buffer_size` | tampon ölç | §2.3 [2] · §4.6 |
| 14 | `audio_in_prepare` | hazırla | §2.3 [3] · §4.6 (rehber `handle` yazımı — §4.6 notu) |
| 15 | `audio_in_read` | senkron oku (int döner) | §2.3 [4] · §4.6 döngü |
| 16 | `audio_in_unprepare` | durdur | §2.3 [5] · §2.4 [4] |
| 17 | `audio_in_destroy` | yok et | §2.3 [5] · §2.4 [4] |
| 18 | `audio_in_set_stream_cb` | asenkron okuma cb — **prepare'dan önce** | §2.4 [1] |
| 19 | `audio_in_unset_stream_cb` | cb kaldır | §2.4 [4] |
| 20 | `audio_in_peek` | cb'de tampon oku | §2.4 [3] · §4.9 |
| 21 | `audio_in_drop` | cb tamponunu bırak | §2.4 [3] · §4.9 |

**Dışarıda bırakılan (kanıtlı yokluk):** `audio_out_set_parameter` — §2.5 ❌
(mevcut değil, 19 fn'lik [M1] listesinde yok · §8 E-Q5 kapanış).

### §9.2 `sound_manager` yüzeyi — 14 çağrı (§4 · §6)

| # | Çağrı | Rol | Kanıt (bu dosya) |
|---|-------|-----|------------------|
| 1 | `sound_manager_get_volume` | seviye oku | §4.1 |
| 2 | `sound_manager_get_max_volume` | üst sınır oku | §4.1 |
| 3 | `sound_manager_get_next_device` | cihaz iteratörü | §4.2 · §4.5 · §4.6 |
| 4 | `sound_manager_get_device_type` | tip sorgu | §4.2 · §4.5 |
| 5 | `sound_manager_get_device_io_direction` | yön sorgu | §4.2 · §4.5 |
| 6 | `sound_manager_get_supported_sample_rates` | hız listesi | §4.3 |
| 7 | `sound_manager_set_sample_rate` | hız seç | §4.3 |
| 8 | `sound_manager_create_stream_information` | stream_info yarat | §4.4 · §4.5 · §4.6 · §4.7 |
| 9 | `sound_manager_acquire_focus` | odak al | §4.4 |
| 10 | `sound_manager_get_device_list` | liste al (mask) | §4.5 · §4.6 |
| 11 | `sound_manager_add_device_for_stream_routing` | manuel rota: cihaz ekle | §4.5 |
| 12 | `sound_manager_apply_stream_routing` | manuel rota: uygula | §4.5 |
| 13 | `sound_manager_set_effect_method_with_reference` | efekt (cihaza referanslı) | §4.6 (AEC) |
| 14 | `sound_manager_set_effect_method` | efekt (bit-mask) | §4.7 (NS\|AGC) |

### §9.3 Çapraz sayım

| Yüzey | Benzersiz çağrı | Kaynak § |
|---|---|---|
| `audio_io` (out+in) | **21** | §2 · §4.6/4.8/4.9 |
| `sound_manager` | **14** | §4 |
| Toplam canlı çağrı | **35** | §9.1 + §9.2 |
| Kanıtlı ölü çağrı | 1 (`set_parameter`) | §2.5 · §8 E-Q5 |

> Kardeş `tizen-audio-anayasa` §6.3'ün 19/21 fn envanteri **API modül sayımı**dır
> (modül html'indeki toplam); bu §9 ise **bu dosyada alıntılanan** çağrıdır — iki
> sayı farklı şey sayar, çelişki değildir (bağ: anayasa §6.3).

---

## §10 Yaşam Döngüsü — Birleşik Durum Tablosu (§2 türetim)

| Durum | Senkron çıkış | Asenkron çıkış | Senkron giriş | Asenkron giriş |
|---|---|---|---|---|
| CREATED | `create_new` (§2.1[1]) | `create_new` (§2.1[1]) | `create` (§2.3[1]) | `create` (§2.3[1]) |
| BOUND | `set_sound_stream_info` (§2.1[2]) | aynı | — (§2.3) | `set_sound_stream_info` (§4.6 deseni) |
| CB-SET | — | `set_stream_cb` (§2.2[1]) | — | `set_stream_cb` **prepare'dan ÖNCE** (§2.4[1]) |
| PREPARED | `prepare` (§2.1[3]) | `prepare` (§2.2[3]) | `prepare` (§2.3[3]) | `prepare` (§2.4[3]) |
| ACTIVE | `write` bloklu (§2.1[4]) | cb içinde `write` (§2.2[3]) | `read` bloklu (§2.3[4]) | cb `peek→fwrite→drop` (§2.4[3]) |
| IDLE | `unprepare` (§2.1[5]) | `unprepare` → cb durur (§2.2[4]) | `unprepare` (§2.3[5]) | `unprepare` (§2.4[4]) |
| DESTROYED | `destroy` (§2.1[6]) | `unset_stream_cb` → `destroy` (§2.2[5][6]) | `destroy` (§2.3[5]) | `unset_stream_cb` → `destroy` (§2.4[4]) |

**Zorunlu sıra kuralları (bu dosyanın kendi alıntıları):** (a) asenkron kayıtta cb
**prepare'dan önce** — aksi halde cb hiç çalışmaz (§2.4[1] birebir); (b) senkron
kayıtta `read`, iç tampon dolana kadar bloklar (§2.3 alıntısı); (c) çıkışta
`unprepare` sonrası cb çağrılmaz (§2.2[4] birebir); (d) `create/destroy` sıkıştırılır,
`prepare/unprepare` tercih edilir (§3 düşük-seviye kurallar).

---

## §11 Hata Yönetimi Desen Envanteri (§2 · §4 · §5 · §8 türetim)

| # | Desen | Kural | Kanıt |
|---|---|---|---|
| 1 | yazma dönüşü | `int` · negatif = hata kodu | §2.1[4] birebir |
| 2 | okuma dönüşü | `int` · negatif = hata · 3 parametre | §2.3[4] · §2.5 (Ç2 kapanışı) |
| 3 | hata tipi | `audio_io_error_e` — 13 kod | §2.1[1] · §8 E-Q2 |
| 4 | cb içi yazma hatası | `data_size < 0` → `dlog_print(DLOG_ERROR, ...)` | §4.8 |
| 5 | cb içi peek hatası | `!= AUDIO_IO_ERROR_NONE` → log + `return` (drop'a geçmez) | §4.9 |
| 6 | device iteratörü sonu | `== SOUND_MANAGER_ERROR_NONE` döngü · `NO_DATA` bitiş | §4.2 |
| 7 | odak başarısı | `if (!error_code)` → stream aktif | §4.4 |
| 8 | senkron/aşenkron hata yüzeyi | senkron: dönüş değeri · asenkron: cb içi kontrol | §5 tablo satırı "Hata" |

---

## §12 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 385 | <500 |
| §9–§11 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir (kendi-kendine-beyan yasağı) |
| M2 | bu tur yeni dış iddia 0: §9–§11 tamamı §2–§8 içinden türetildi (yeni GET yok) |
| M3 | FM 10 alan + `depends_on`/`evidence` listeleri **dokunulmadı** |
| M4 | kardeş dosyalara (sound-manager · webos) yazım yok — kendi turlarında |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 385 satır dokunulmaz |

---

## §13 Dosya İçi Varyant ve Not Kayıtları (silinmez — işaretli kalır)

| # | Varyant | Karar | Kayıt |
|---|---|---|---|
| V1 | §4.6 alıntısında `audio_in_prepare(handle)` (rehberin kendi yazımı) | alıntı **birebir korunur**, not düşülür | §4.6 alıntısı altı not |
| V2 | §2.5 eski indeks 4 satır (create_new/set_parameter/in_read/drain) | statü ❌/⚠️ — silinmez | §2.5 tablosu |
| V3 | §2.1[5] "duraklat" adımının `unprepare`'a eşlenmesi | rehber "duraklat"ı ayrıca API'ye bağlamaz — durdurma = unprepare | §2.1[5] notu |
| V4 | `drain`/`flush` rehberde gösterilmiyor ama API'de var (Since 2.4) | ✅ gerçek fonksiyon, gösterim eksikliği | §2.5[4] · §8 E-Q3 |
| V5 | E-Q4 (192000 Hz hw garantisi) | **UNKNOWN** — rehber aralığı var, cihaz garantisi yok | §8 E-Q4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
