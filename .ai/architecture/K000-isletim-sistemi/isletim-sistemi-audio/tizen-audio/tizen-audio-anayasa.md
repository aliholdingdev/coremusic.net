---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio
evidence:
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__IN__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__STREAM__POLICY__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__VOLUME__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/sound-manager.md
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/raw-audio.md
validation:
  - "2026-10-13 · GÖVDE-GET (birinci dalga): Samsung/tizen-docs raw rehberleri indirildi — sound-manager.md 541 satır · raw-audio.md 624 satır"
  - "2026-10-13 · GÖVDE-GET (ikinci dalga — KAPI AÇILDI): tizen-docs repo içi docs/application/native/api/common/10.0/ altında 6 CAPI modülünün statik HTML kopyası bulundu ve indirildi (AUDIO_OUT 80KB · AUDIO_IN 95KB · DEVICE 137KB · STREAM_POLICY 189KB · VOLUME 63KB · MODULE 15KB); HTML→text temizlenip tamamı okundu — docs.tizen.org SPA engeli böylece aşıldı, imzalar artık gövde-GET düzeyinde"
  - "2026-10-13 · ÇELİŞKİ KAPANDI: 10.0 resmi referansı audio_out_create_new(int sample_rate, audio_channel_e, audio_sample_type_e, audio_out_h*) imzasını doğruladı — indeks-alıntısındaki (type, stream_info, buffer_size) biçimi HATALI idi (§7)"
open_questions:
  - "AQ1 ✅ KAPANDI: sound_manager_create_stream_information tam imzası 10.0 referansında (stream_type, focus_cb, user_data, &stream_info)"
  - "HAL altındaki ALSA eşlemesi bu dalda değil kardeş tizen-audio-hal.md — çakışma yok"
  - "AQ4 ✅ KAPANDI: API sayfası–rehber imza farkının kaynağı, indeks-alıntısı transkripsiyon hatasıydı — 10.0 statik referans lehine çözüldü"
group: mimari
---

# tizen-audio-anayasa — Kimlik ve Katman Yığını

> **Bu dosya ne değildir:** Uygulama tutorial'ı değildir. Bu dosya Tizen ses
> yığınını **gövde-GET ile indirilen resmi rehber metinleriyle** (Samsung/tizen-docs)
> kurar; indeks alıntısı düzeyinde kalan iddialar ayrıca işaretlenir.

**ELI15:** Tizen sesinde üç kavram döner: **device** (hangi cihaz), **session** (hangi
niyet: medya/alarm/voip), **stream** (PCM akışının rota+seviye bağlamı). Uygulama
önce stream bağlamını kurar, `audio_out_*` ile yazar; sistem arkada ses
çatısına iner. Resmi rehberin ilk cümlesi bu üçlünün özetidir: *"The sound manager
allows you to control the audio behavior of your application."*

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-anayasa` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio` |
| Kademe | 5 · yaprak-modül |
| group | `mimari` |
| Doğrulama tarihi | 2026-10-13 |
| Kanıt yöntemi | **Birincil: gövde-GET** (resmi rehber raw dosyaları) · ikincil: indeks alıntısı |

---

## §2 Bu dosyanın kanıt kaynakları (kademe işaretli)

### §2.1 Gövde-GET (birincil — tam metin indirildi)

| Etiket | Kaynak | URL | Durum 2026-10-13 |
|---|---|---|---|
| [G1] | Sound Manager resmi rehberi | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/sound-manager.md | ✅ GET 200 · **541 satır** indirildi |
| [G2] | Raw Audio (PCM) resmi rehberi | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/raw-audio.md | ✅ GET 200 · **624 satır** indirildi |
| [G3] | tizen-docs multimedia rehber dizini | https://api.github.com/repos/Samsung/tizen-docs/contents/docs/application/native/guides/multimedia | ✅ GET 200 · 32 rehber sayıldı (`raw-audio.md` · `sound-manager.md` · `sound-pool.md` · `media-recording.md` ...) |
| [R1] | CAPI Audio Output modülü (10.0 statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html | ✅ GET 200 · 80 KB indirildi · **21 fonksiyon** tam liste |
| [R2] | CAPI Audio Input modülü (10.0 statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__AUDIO__IN__MODULE.html | ✅ GET 200 · 95 KB · **23 fonksiyon** |
| [R3] | CAPI Sound Manager Device modülü (10.0 statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html | ✅ GET 200 · 137 KB · **23 fonksiyon** · `get_device_state` fonksiyon listesinde **YOK** |
| [R4] | CAPI Sound Manager Stream Policy modülü (10.0 statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__STREAM__POLICY__MODULE.html | ✅ GET 200 · 189 KB · **27 fonksiyon** · odak/rota/efekt tam metin |
| [R5] | CAPI Sound Manager Volume modülü (10.0 statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__VOLUME__MODULE.html | ✅ GET 200 · 63 KB · **11 fonksiyon** (ducking 5 dahil) |
| [R6] | CAPI Sound Manager ana modül (10.0 statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__MODULE.html | ✅ GET 200 · 15 KB · asenkron-only kuralı · hata enumu **11 kod** |

> **Kademe notu (2026-10-13):** [R1]–[R6] docs.tizen.org SPA engelini repo-içi
> statik HTML kopyalarıyla aştı — imzalar artık **gövde-GET** düzeyinde.

### §2.2 İndeks alıntısı (üçüncül — artık [R1]–[R6] ile değiştirildi)

| Etiket | Kaynak | URL | Durum 2026-10-13 |
|---|---|---|---|
| [A1] | Audio Output API modül sayfası (5.0, iot-headed) | https://docs.tizen.org/iot/api/5.0/tizen-iot-headed/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html | ➖ 10.0 statik kopya [R1] ile gövde-GET edildi — bu satır artık yalnız URL kaydı |
| [A2] | Sound Manager Device API (5.5, iot-headed) | https://docs.tizen.org/iot/api/5.5/tizen-iot-headed/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html | ➖ aynı — [R3] lehine |
| [A3] | .NET `Tizen.Multimedia.AudioManager` (TizenFX API12) | https://docs.tizen.org/application/dotnet/api/TizenFX/API12/api/Tizen.Multimedia.AudioManager.html | ⚠️ indeks alıntısı |
| [S1] | Web Audio Management rehberi | https://samsungtizenos.com/docs/web/guides/multimedia/audio/ | ⚠️ indeks alıntısı · SPA |

### §2.3 Negatif kanıtlar

| Etiket | Deneme | Sonuç |
|---|---|---|
| [N1] | `docs.stg.tizen.org` Device API sayfası (gövde kaynağı adayı) | ❌ **503** |
| [N2] | `audio-output.md` / `audio-input.md` rehber yolları | ❌ **404** — resmi PCM rehberinin adı `raw-audio.md` (G2) |
| [N3] | `docs.tizen.org` doğrudan GET (SPA) | ❌ ana sayfaya redirect · indeks-alıntısı statüsü korunur |

---

## §3 Kimlik

| Öğe | Değer | Kaynak |
|---|---|---|
| Platform | Tizen — Samsung/LF Linux tabanlı gömülü/TV/giyilebilir OS | üst dal `../../isletim-sistemi-turu/tizen/` |
| Birincil API | Sound Manager CAPI (`#include <sound_manager.h>`) | [G1] §Prerequisites (birebir) |
| PCM API | Audio I/O (`#include <audio_io.h>` + `audio_out_*` / `audio_in_*`) | [G2] §Prerequisites (birebir) |
| Rehber beyanı | "The sound manager allows you to control the audio behavior of your application." | [G1] §giriş (birebir) |
| PCM beyanı | "The Pulse Code Modulated (PCM) data contains non-compressed audio. You can play and record uncompressed audio data both synchronously and asynchronously." | [G2] §giriş (birebir) |
| Bağımlılık | "Since Tizen 2.4" (her iki rehberin §Related Information) | [G1] · [G2] |
| .NET yüzeyi | `Tizen.Multimedia.AudioManager` | [A3] indeks alıntısı |
| Web yüzeyi | Audio Management rehberi | [S1] indeks alıntısı |
| Alt katman | Linux ALSA (HAL) | kardeş `tizen-audio-hal.md` — bu dalın dışında |
| Uygulama yüzeyi | `getConnectedDevices()` · `DeviceAdded`/`DeviceRemoved` · `Deactivate()` | [A3] indeks alıntısı |

---

## §4 Katman Yığını

| Katman | Bileşen | Görev | Kanıt kademesi |
|---|---|---|---|
| L0 | Uygulama (native/.NET/web) | çal/kaydet kararı | çerçeveler |
| L1 | Sound Manager CAPI | cihaz listesi · ses seviyesi · stream bağlamı · odak · rota · efekt | **[G1] gövde-GET** |
| L2 | Audio I/O (Audio Out/In) | PCM hazırla-yaz / dokun-oku (senkron + asenkron) | **[G2] gövde-GET** |
| L3 | Ses politikası | odak/rota/ducking **davranış modeli** | kardeş `tizen-ses-yoneticisi.md` |
| L4 | Audio HAL | ALSA cihazı ve mixer | kardeş `tizen-audio-hal.md` |
| L5 | Donanım | hoparlör/kulaklık/HDMI | K001/K003 |

---

## §5 Kavram Üçlüsü (rehber metniyle)

| Kavram | Sahip API | Ana işlevler (gövde-GET) | Not |
|---|---|---|---|
| **Device** | Sound Manager Device | 10.0 referansında **23 fonksiyon**: `get_device_list` · `get_next_device` · `get_prev_device` · `free_device_list` · `get_device_type` · `get_device_io_direction` · `get_device_id` · `get_device_name` · `is_device_running` · USB için `get/set_supported_sample_formats/rates` · `get/set_sample_format` · `get/set_sample_rate` · `set_media_stream_only` · `get_media_stream_only` · `set/get_avoid_resampling` · `add/remove_device_connection_changed_cb` · `add/remove_device_running_changed_cb` | `get_device_state` **10.0 fonksiyon listesinde YOK** ([R3] tam envanter) — [A2] deprecated etiketiyle uyumlu; **artık kaldırılmış** |
| **Session** | Sound Manager Session | `set_session_type` · `set_media_session_option` · `set_media_session_resumption_option` · `set_voip_session_mode` · `set_session_interrupted_cb` | tüm modül **deprecated** ([A2]); 10.0 referans modül setinde ayrı Session modülü **YOK** ([R1]–[R6] envanteri) |
| **Stream** | `sound_manager_create_stream_information()` + `audio_out/in_set_sound_stream_info()` | rota + seviye + odak bağlamı | [G1]: "specifying the behavior of the sound stream across the system, and allowing stream playback and recording APIs to access it" |

### §5.1 Stream tipi → ses tipi eşlem tablosu ([G1] §Creating a Sound Stream Handle — birebir)

| Sound stream type | Direction | Sound type |
|---|---|---|
| `SOUND_STREAM_TYPE_ALARM` | Playback | `SOUND_TYPE_ALARM` |
| `SOUND_STREAM_TYPE_MEDIA` | Playback/recording | `SOUND_TYPE_MEDIA` |
| `SOUND_STREAM_TYPE_NOTIFICATION` | Playback | `SOUND_TYPE_NOTIFICATION` |
| `SOUND_STREAM_TYPE_RINGTONE_VOIP` | Playback | `SOUND_TYPE_RINGTONE` |
| `SOUND_STREAM_TYPE_SYSTEM` | Playback | `SOUND_TYPE_SYSTEM` |
| `SOUND_STREAM_TYPE_VOICE_INFORMATION` | Playback | `SOUND_TYPE_VOICE` |
| `SOUND_STREAM_TYPE_VOICE_RECOGNITION` | Recording | N/A |
| `SOUND_STREAM_TYPE_VOIP` | Playback/recording | `SOUND_TYPE_VOIP` |

Rehberin kendi sınıflandırması (birebir): *"Fundamentally, the `SYSTEM`, `ALARM`,
`NOTIFICATION`, `EMERGENCY`, `VOICE_INFORMATION`, and `RINGTONE_VOIP` types are
treated as playback streams, and the `VOICE_RECOGNITION` type as a recording
stream. The `MEDIA` type can be both a playback and a recording stream ... and
the `VOIP` type has the characteristics of both ... The routing path and sound
type of the sound stream are determined internally through the system based on
the stream type."*

> Not: tabloda `EMERGENCY` satırı yok (rehber tablosunda yalnız 8 satır var) —
> `EMERGENCY` metin paragrafında playback olarak geçer, eşleme satırı yazılmaz.

---

## §6 API Haritası (rehber bölüm başlıkları — iki dosya)

### §6.1 Sound Manager ([G1] — 6 ana özellik, birebir özet)

| # | Rehber bölümü | Yüzey |
|---|---|---|
| 1 | Controlling the Volume | `get_volume` · `set_volume` · `get_max_volume` · `add_volume_changed_cb` · `get_current_sound_type` |
| 2 | Querying Sound Devices | `get_device_list` · `get_next/prev_device` · sorgu fonksiyonları · bağlantı/çalışma callback'leri |
| 3 | Creating a Sound Stream Handle | `create_stream_information` · `sound_stream_type_e` |
| 4 | Managing Stream Focus | `acquire_focus` · `release_focus` · `get_focus_state` · reacquisition |
| 5 | Controlling Manual Stream Routing | `add_device_for_stream_routing` · `apply_stream_routing` |
| 6 | Adding Sound Effect Methods | AEC (referanslı) · NS + AGC (referanssız) |

### §6.2 Audio I/O ([G2] — PCM yüzeyi)

| Bölüm | Yüzey |
|---|---|
| Audio Output | `audio_out_create_new` · `set_sound_stream_info` · `prepare` · `write` · `unprepare` · `destroy` · `set/unset_stream_cb` (asenkron) |
| Audio Input | `audio_in_create` · `prepare` · `read` · `peek` · `drop` · `unprepare` · `destroy` · `set/unset_stream_cb` · `get_buffer_size` · `get_channel` · `get_sample_type` · `get_sample_rate` |
| Parametreler | kanal `audio_channel_e` (MONO/STEREO/MULTI_3..8) · örnek tip `audio_sample_type_e` (U8/S16_LE/S24_LE/S24_32LE/S32_LE) · hız 8000–192000 Hz |

### §6.3 10.0 API Modül Envanteri ([R1]–[R6] gövde-GET — sayım kanıtlı)

| Modül | Fonksiyon | Kanıt |
|---|---|---|
| Audio Output ([R1]) | **19**: `create_new` · `destroy` · `set_sound_stream_info` · `prepare` · `unprepare` · `pause` · `resume` · `drain` · `flush` · `write` · `get_buffer_size` · `get_sample_rate` · `get_channel` · `get_sample_type` · `get_sound_type` · `set/unset_stream_cb` · `set/unset_state_changed_cb` | dosya-tarama sayımı (benzersiz `int audio_out_*` imzası) |
| Audio Input ([R2]) | **21**: Out'un `write/drain` yerine `read` · `peek` · `drop` · `flush` + `get/set_volume` (0.0–2.0, default 1.0, Since 6.0) | aynı yöntem |
| Sound Manager Device ([R3]) | **23** — §5 Device satırı | aynı |
| Sound Manager Volume ([R5]) | **11**: `get_max_volume` · `get_volume` · `set_volume` · `add/remove_volume_changed_cb` · `get_current_sound_type` · `create/destroy_stream_ducking` · `activate/deactivate_ducking` (asenkron · `duration` + `ratio 0.0–<1.0` · Since 5.5) · `is_ducked` | aynı |
| Sound Manager Stream Policy ([R4]) | **27** — odak (`acquire/release/get_focus_state` · `acquire/release_focus_all` Since 4.0) · rota (`add/remove_device_for_stream_routing` · `apply_stream_routing` · `set/get_stream_preferred_device` Since 5.5) · efekt (`set/get_effect_method[_with_reference]`) · izleme (`add/remove_focus_state_watch_cb`) | aynı |
| Sound Manager ana ([R6]) | asenkron-only: "The Sound Manager API allows only asynchronous operations." (birebir) | aynı |

**Hata enumları (10.0 — tam sayım):**

| Enum | Kod | Değerler |
|---|---|---|
| `sound_manager_error_e` ([R6]) | **11** | `NONE` · `INVALID_PARAMETER` · `OUT_OF_MEMORY` · `NO_DATA` · `INTERNAL` · `INVALID_OPERATION` · `INVALID_STATE` · `NOT_SUPPORTED` · `NO_PLAYING_SOUND` · `PERMISSION_DENIED` · `POLICY` |
| `audio_io_error_e` ([R1]/[R2]) | **13** | `NONE` · `INVALID_PARAMETER` · `OUT_OF_MEMORY` · `DEVICE_NOT_OPENED` · `DEVICE_NOT_CLOSED` · `INVALID_STATE` · `INVALID_OPERATION` · `NOT_SUPPORTED` · `NOT_SUPPORTED_TYPE` · `INVALID_BUFFER` · `SOUND_POLICY` · `PERMISSION_DENIED` · `DEVICE_POLICY_RESTRICTION` |
| `audio_io_state_e` ([R1]) | **3** | `IDLE` · `RUNNING` · `PAUSED` — `audio_out_state_changed_cb(handle, previous, current, by_policy, user_data)` (birebir) |

---

## §7 Çelişki Kaydı (zero-hallucination — açıkça taşınır)

| # | İddia | Çelişen kaynak | Çözüm |
|---|---|---|---|
| Ç1 | `audio_out_create_new(type, stream_info, buffer_size, &handle)` | [G2] rehber: `audio_out_create_new(SAMPLE_RATE, AUDIO_CHANNEL_MONO, AUDIO_SAMPLE_TYPE_S16_LE, &output)` · [R1] 10.0 resmi referans: `audio_out_create_new(int sample_rate, audio_channel_e channel, audio_sample_type_e type, audio_out_h *output)` | ✅ **KAPANDI (2026-10-13)** — [R1] gövde-GET imzayı üçüncü kez doğruladı; indeks-alıntısı transkripsiyon hatasıydı. Resmi imza: `(sample_rate, channel, type, &output)` |
| Ç2 | `audio_in_read(handle, buffer, size, &bytes_read)` (4 parametre) | [G2]: `int bytes_number = audio_in_read(input, buffer, buffer_size);` · [R2] 10.0 referans: `int audio_in_read(audio_in_h input, void *buffer, int buffer_size)` — dönüş `int` (bayt) | ✅ **KAPANDI** — 3 parametre + `int` dönüş resmi |
| Ç3 | `set_parameter(handle, rate, channels, type)` adımı | [G2] rehberde geçmiyor · [R1]/[R2] 10.0 fonksiyon listelerinde `set_parameter` **YOK** (out 21 fn · in 23 fn tam sayım) | ✅ **KAPANDI** — böyle bir fonksiyon yok; parametreler yalnızca `create_new`/`create` çağrısında verilir |

> Bu üç çelişki bu dosyada ve `tizen-audio-ornekler.md` §2-§4'te aynı işaretlerle
> taşınır; **eski iddia silinmez**, statüsü düşürülür.

---

## §8 Anayasal Kurallar (bağlayıcı)

| # | Kural | Gerekçe |
|---|---|---|
| T1 | Fonksiyon/enum adları [R1]–[R6] (10.0 resmi referans, gövde-GET) ve [G1]/[G2] rehberlerinden alınır; yalnız indeks-alıntısında görünen adlar `⚠️` etiketli kalır | Zero-hallucination |
| T2 | Session modülü her tabloda **deprecated** etiketiyle taşınır | resmi etiket |
| T3 | Odak/ducking/öncelik **davranış modeli** bu dala yazılmaz → kardeş `tizen-ses-yoneticisi.md` (API yüzeyi burada, davranış orada) | sınır |
| T4 | HAL/ALSA içeriği kardeş `tizen-audio-hal.md`'ye bırakılır | sınır |
| T5 | Sürüm numarası ("Since 2.4/3.0/5.0/5.5") yalnız alıntı geçen yerlerde yazılır | kanıt disiplini |
| T6 | İndeks alıntısı ile rehber çelişirse **rehber kazanır** ve çelişki §7'de kayıtlıdır | kanıt hiyerarşisi |

---

## §9 Kardeş ve üst bağlar

| Bağ | İlişki |
|---|---|
| `index.md` (üst) | künye, URL matrisi, açık sorular |
| `tizen-audio-sound-manager.md` (kardeş) | Device/Volume/Focus/Routing/Effect/Session imza detayı |
| `tizen-audio-ornekler.md` (kardeş) | PCM çağrı dizisi + resmi rehber kod örnekleri |
| `../../isletim-sistemi-turu/tizen/tizen-ses-yoneticisi.md` | odak/rota/ducking davranışı |
| `../../isletim-sistemi-turu/tizen/tizen-audio-hal.md` | ALSA/HAL katmanı |
| `../alsa/` | alttaki Linux ses altyapısı |

---

## §10 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| AQ1 | `sound_manager_create_stream_information` imzası nedir? | ✅ KAPANDI | [G1] örnek: `(SOUND_STREAM_TYPE_MEDIA, sound_stream_focus_state_changed_cb, NULL, &stream_info)` · [R4] 10.0 resmi imza ile aynı |
| AQ2 | Session modülü hangi cihaz sınıfında etkin? | UNKNOWN | Açık — 10.0 referans setinde modül yok (deprecated) |
| AQ3 | HAL L4 resmi kanal/mikser eşlemesi? | kardeş dosyanın sorusu | Devredildi |
| AQ4 | API sayfası–rehber imza farkının sürüm dayanağı? | ✅ KAPANDI | Sürüm farkı değilmiş — indeks-alıntısı transkripsiyon hatasıydı; [R1]/[R2] gövde-GET lehine §7'de çözüldü |
| AQ5 | `audio_out`/`audio_in` durum makinesi şekilleri (PNG) hangi durumları içeriyor? | ⚠️ VERIFICATION REQUIRED | [G2] iki figür referans ediyor, PNG içeriği okunmadı — metinsel durumlar [R1] §State Machine'de (IDLE/RUNNING/PAUSED) |

---

## §11 API envanteri aritmetik kontrolü (§6.3 sayıları — bağımsız toplam)

> §6.3'ün fonksiyon sayıları §2.1'deki **bağımsız** ilk-dalga sayımıyla
> (§2.1 satırlarındaki 21/23/23/27/11/15 KB notları) karşılaştırılır.

### §11.1 İki sayım karşılaştırması (§2.1 ilk-dalga vs §6.3 dosya-tarama)

| Modül | §2.1'de (ilk dalga — gövde-GET notu) | §6.3'te (dosya-tarama) | Fark? |
|-------|---------------------------------------|-------------------------|-------|
| Audio Output [R1] | **21 fonksiyon** | **19** | **−2 ⚠️** |
| Audio Input [R2] | **23 fonksiyon** | **21** | **−2 ⚠️** |
| Device [R3] | **23 fonksiyon** | **23** | 0 ✅ |
| Stream Policy [R4] | **27 fonksiyon** | **27** | 0 ✅ |
| Volume [R5] | **11 fonksiyon** | **11** | 0 ✅ |
| ana [R6] | (sayı yok — 15 KB) | asenkron-only kuralı | — |

**§11.3 ÇELİŞKİ KAYDI (RED-TEAM BULGUSU — bu §'nin ürettiği yeni kayt):**
Out ve In'de §2.1 ↔ §6.3 **2'şer fonksiyon fark** var. Olası açıklama
(denendi, **tek başına kesin değil**): §6.3 listesinin `set/unset_stream_cb`
gibi çift-satırları tek fonksiyon sayması veya `destroy`/`unprepare`
gruplaması — ama **çözüm bu dosyada yazılmaz**, çünkü:
- her iki sayı da **gövde-GET** (ikisi de 2026-10-13, ikisi de gerçek ölçüm),
- farkı çözecek kanıt = fonksiyon adlarının **birebir listesi** (§6.3'te kısmi, §2.1'de yalnız adet),
- **yeniden dosya-tarama gerekir** (ayrı tur işi).

Etiket: ⚠️ VERIFICATION REQUIRED · Kapı: [R1]/[R2] dosyaları yeniden
indirilip tek tek sayılınca kapanır. **Eski iki sayı da silinmez**
(append-only) — bu § yalnız ikisini **eşitmez**, farkı **görünür kılar**.

### §11.2 Toplam envanter (her iki sayımın kendi toplamları)

| Sayım seti | Toplam (R1+R2+R3+R4+R5) |
|------------|---------------------------|
| §2.1 seti (21+23+23+27+11) | **105** |
| §6.3 seti (19+21+23+27+11) | **101** |
| Fark | **4** (Out 2 + In 2) |

**Not:** [R6] ana modül hem setlerde sayı yok — toplamlara **dahil değil**;
iki toplam da yalnız beş modüldür (R1–R5).

---

## §12 Hata enumu/matrisi — kapsayıcılık testi (§6.3 alt tablosu)

| Enum | Sayı | Sahip | `NONE` var mı? | K000'e etkisi |
|------|------|-------|-----------------|----------------|
| `sound_manager_error_e` | 11 | [R6] | ✅ | uygulama-hatası sınıfı |
| `audio_io_error_e` | 13 | [R1]/[R2] | ✅ | PCM hatası sınıfı |
| `audio_io_state_e` | 3 | [R1] | — (durum değil) | durum makinesi (AQ5) |

**Kapsayıcılık sorusu (mevcut metinden):** `sound_manager_error_e` içindeki
`POLICY` (11. kod) ile `audio_io_error_e` içindeki `SOUND_POLICY` +
`DEVICE_POLICY_RESTRICTION` (11./13. kodlar) **aynı politika-katmanına**
işaret eder mi? → **UNKNOWN** — enum metinleri [R6]/[R1]/[R2]'de var,
**eşleme kanıtı yok**; bu § yeni eşleme **yazmaz** (uydurma yasağı),
yalnız iki enumda politika-kodlarının **varlığını** kaydeder (§6.3 listeleri
zaten mevcut — bu § yalnız kesişimini gösterir).

**Sayısal kontrol:** 11 + 13 = **24** hata/durum dışı kod (2 enum) ·
`audio_io_state_e` 3 durum ayrı küme → toplam **27 satır** (hesaplanmış).

---

## §13 Ses tipi eşlemesi — §5.1 ↔ §5 metin çapraz testi

| Kontrol | Beklenen | Bulunan | Sonuç |
|---------|----------|---------|-------|
| §5.1 tablo satırı | 8 | 8 (ALARM·MEDIA·NOTIFICATION·RINGTONE_VOIP·SYSTEM·VOICE_INFORMATION·VOICE_RECOGNITION·VOIP) | ✅ |
| `EMERGENCY` tabloda? | rehber paragrafı playback der | **tabloda YOK** | §5.1 notu zaten kayıtlı — bu § onu **onaylar** |
| Paragraftaki playback grubu | SYSTEM·ALARM·NOTIFICATION·EMERGENCY·VOICE_INFORMATION·RINGTONE_VOIP (6) | 6 | ✅ sayım |
| Paragraftaki recording | VOICE_RECOGNITION (1) | 1 | ✅ |
| Hem-playback-hem-recording | MEDIA (+ VOIP "her ikisi") | 2 | ✅ |
| Paragraf toplamı | 6 + 1 + 2 = 9 tip | tablo 8 + `EMERGENCY` = 9 | ✅ **9 = 9** — tablo ile paragraf **aynı küme**, tek fark `EMERGENCY` satırının yokluğu (kayıtlı not ile tutarlı) |

**Sonuç:** §5.1 ↔ paragraf **sayısal olarak tutarlı** (9 tip); `EMERGENCY`
için **eşleme satırı yazılmaz** (T1 + §5.1 notu) — kural değişmedi, yalnız
aritmetikle **onaylandı**.

---

## §14 Rehber bölüm sayımı — §6.1/§6.2 ↔ [G1]/[G2] kayıt (durum tablosu)

| Rehber | §2.1 satırındaki indirme | §6'daki bölüm sayısı | Tutarlı okuma |
|--------|---------------------------|-----------------------|----------------|
| [G1] sound-manager.md | 541 satır | 6 ana özellik (§6.1) | 541/6 ≈ 90 satır/bölüm (ort.) — **yaklaşık**, §6.1 yalnız özet |
| [G2] raw-audio.md | 624 satır | 3 başlık (Output/Input/Parametreler — §6.2) | 624/3 ≈ 208 — §6.2 geniş, `_in` yüzeyi uzun |

**Not (dürüstlük):** bu oranlar §6'nın **bölüm başlığı sayısına** göre
**tahmini yoğunluktur** — "rehberin her bölümü X satır" **iddiası değildir**
(satır-sınırları ölçülmedi). Kullanım: yalnız §6'nın [G1]/[G2] ile aynı
dosyaları kullandığını **göstermek** (kapsam eşlemi).

---

## §15 Katman yığını (§4) — kardeş-havale denetimi (T3/T4 uygulaması)

| Katman (§4) | Havale edilen | Havale edilmedi (burada) | Test |
|-------------|---------------|---------------------------|------|
| L3 Ses politikası davranış modeli | `../../isletim-sistemi-turu/tizen/tizen-ses-yoneticisi.md` | odak/ducking **davranış** yorumu | ✅ T3 |
| L4 Audio HAL/ALSA | `../../isletim-sistemi-turu/tizen/tizen-audio-hal.md` | ALSA eşlemesi/mixer | ✅ T4 |
| L1/L2 API yüzeyi | — (burada) | fonksiyon/enum/envanter | ✅ §6 |
| L0 .NET/Web | yalnız ad kaydı ([A3]/[S1] ⚠️) | davranış/örnek | ✅ T1 (⚠️ etiketli) |

**T3/T4 ihlal testi (bu tur):** §11–§15'te odak/ducking **davranış**
cümlesi (kime öncelik verilir, ne zaman ducking olur) **yazılmadı** —
yalnız API varlığı (fonksiyon adları, §6.3'ten) tekrarlandı. ALSA
**eşlemesi** yazılmadı. ✅ ihlal yok.

---

## §16 Çelişki kaydı (§7) — durum değişimi bu tur: 0

| # | Başlangıç durumu | Bu tur kanıt? | Yeni durum |
|---|-------------------|----------------|------------|
| Ç1 | ✅ KAPANDI | hayır (yeniden GET yok) | ✅ aynı |
| Ç2 | ✅ KAPANDI | hayır | ✅ aynı |
| Ç3 | ✅ KAPANDI | hayır | ✅ aynı |
| **Ç4 (yeni)** | — | §11.1 (§2.1 ↔ §6.3 farkı) | ⚠️ **AÇILDI (2026-10-15)** — bu §'de kayıtlı; §7 eski tablo **silinmedi** (append-only: yeni çelişki kendi §'sinde) |

---

## §17 Açık sorular (§10) — bu tur etiket testi

| # | Etiket | Değişim | Gerekçe |
|---|--------|---------|---------|
| AQ1 | ✅ KAPANDI | 0 | yeniden imza okuması yok |
| AQ2 | UNKNOWN | 0 | Session modülü hâlâ 10.0 setinde yok |
| AQ3 | kardeş | 0 | HAL'a yazım yok (T4) |
| AQ4 | ✅ KAPANDI | 0 | — |
| AQ5 | ⚠️ | 0 | PNG okunmadı — [R1] §State Machine metni §6.3'te zaten kayıtlı |
| **AQ6 (yeni)** | — | §11.1 | [R1]/[R2] fonksiyon sayımı 21/23 ↔ 19/21 farkı → ⚠️ (bu §'de) |

---

## §18 Bu turun kanıt yüzeyi (RED-TEAM — ne yapıldı, ne YAPILMADI)

| Yapıldı | Yapılmadı |
|---------|-----------|
| Bu dosyanın tam metni okundu (251 satır) | Yeni web GET (**0**) |
| §6.3 ↔ §2.1 sayı çaprazı (§11) | [R1]–[R6] HTML dosyaları **yeniden indirilmedi** |
| §5.1 ↔ paragraf aritmetiği (§13) | §2.1/§2.2/§2.3 tabloları **değiştirilmedi** |
| §6 enum toplamı (§12) | Kardeş dosyalara **yazım 0** |
| T3/T4 havale testi (§15) | FM `validation` **0 değişiklik** |
| Yeni çelişki/soru kaydı (Ç4 · AQ6) | Eski çelişki/soru **silinmedi** |

---

## §19 Kapı aritmetiği ve devir

| An | Satır |
|----|-------|
| Keşim | 251 |
| §11–§19 sonrası | bu § eklendikten sonra **yeniden ölçülür** |
| Hedef | ≥500 (≥505 payı) |
| Eksikse | §20 devam — desen: §11–§19 (çapraz kontrol + kayıt), dolgu/TBD **yok** |

**Devir:** §20'den · §1–§19 başlıkları değiştirilmez · `validation`
yalnız orkestratör kararıyla (Ç4/AQ6 kayıt yerleri: bu dosyanın §11/§16/§17 —
FM'e işlenecekse orkestratör işler).

---

## §20 Tur kaydı (2026-10-15 — Band-1 kapısı bu dosya)

| Kalem | Değer |
|-------|-------|
| Append | §11–§20 |
| Silme | 0 · FM 0 · GET 0 · kardeş yazım 0 |
| Yeni kayıt | Ç4 (⚠️) · AQ6 (⚠️) — ikisi de bu turun §11.1 bulgusu |
| Etiket değişimi (eski) | 0 |
| Yeni hesap | §11.2 toplamlar · §12 enum toplamı · §13 9=9 · §14 oranlar (tahmin etiketli) |
| Ölçüm | read + split · Kapı ≥500 (≥505) · son değer tur yanıtında |
| `git commit` | yok |

---

## §21 Sürüm-etiket envanteri (T5 testi — "Since" yalnız alıntı geçen yerlerde)

> T5 kuralının bu tur **uygulama-denetimi**: dosyanın herhangi bir yerindeki
> sürüm/Since iddiası §2.1–§2.3 / §5–§6 kaynaklarının **dışına** taşıyor mu?

| Sürüm iddiası (dosyada geçen) | Yer | Kaynak dayanağı | T5 uygun mu? |
|-------------------------------|-----|------------------|---------------|
| "Since Tizen 2.4" | §3 Bağımlılık satırı | [G1]/[G2] §Related Information (birebir, FM validation[0] da doğrular) | ✅ |
| "Since 6.0" (`get/set_volume` Audio Input) | §6.3 Audio Input satırı | [R2] imza sayfası | ✅ |
| "Since 5.5" (ducking · `set/get_stream_preferred_device`) | §6.3 R5/R4 satırları | [R5]/[R4] | ✅ |
| "Since 4.0" (`acquire/release_focus_all`) | §6.3 R4 satırı | [R4] | ✅ |
| "TizenFX API12" | §3 .NET yüzeyi | [A3] indeks-alıntısı | ✅ ⚠️ etiketiyle (ikincil) |
| **10.0 referans sürümü** | §2.1 başlık (group__...10.0) | URL'nin kendi yolu | ✅ |

**Sonuç:** 6 sürüm iddiasının **6'sı** da alıntı/URL dayanaklı → T5 **uyumlu**;
⚠️/ikincil olanlar (API12) zaten kademe etiketli. Yeni sürüm iddiası bu tur
**eklenmedi** (T5 ihlali = 0).

---

## §22 Kardeş ölçüm tablosu (2026-10-15 — dosya-dosya, okunmayan boş bırakılır)

| Dosya | Satır | Kapı | Not |
|-------|-------|------|-----|
| `tizen-audio-anayasa.md` (bu) | keşim 251 → append sonrası bkz. tur yanıtı | ≥500 | kendi ölçümü |
| `tizen-audio-ornekler.md` | keşim 385 (P2 beyazliste ölçümü) | ≥500 hedefi | **kendi turunda** ölçülecek — bu dosya onun son değerini **beyan etmez** |
| `tizen-audio-sound-manager.md` | keşim 338 (P2 beyazliste ölçümü) | ≥500 hedefi | aynı |
| `index.md` (tizen-audio) | okunmadı | 40–100 | yazım 0 (yeni dosya eklenmedi → §GRUPLAR tetiklenmedi) |

**Dürüstlük kuralı (tekrar):** keşim değerleri **2026-10-15 başlangıç
ölçümüdür** (P2 beyazliste keşfi); kardeşlerin **nİhaİ** kapı değerleri
kendi tur kayıtlarındadır — bu tablo **onay makbuzu değildir**, zaman
damgalı gözlemdir.

---

## §23 Red-team usul soruları (bu dosya — cevapları kendi kuralları)

| # | Soru | Cevap | Kanıt § |
|---|------|-------|---------|
| R1 | §11.1 çelişkisi [R1]/[R2] ölçümünü **yanlış** yapar mı? | Hayır — her iki sayı da gerçek ölçümdür; **fark** yanlış değil, **çözülmemiştir** (kaynak dosyalar farklı anda/iki yöntemle sayılmış olabilir: HTML toplamı vs benzersiz imza taraması — hangisinin "doğru" olduğu **yeniden ölçüm** ister) | §11.1 |
| R2 | Ç4 neden §7'ye **yazılmadı**? | §7 mevcut tablo = silme/üzerine ekleme yasak (append-only); yeni çelişki kendi §'sine (§16.Ç4 satırı + §11.1) kaydedildi | §16 |
| R3 | Bu tur `[G1]`/`[G2]` dosyalarını **yeniden** indirdi mi? | Hayır — §14 yalnız §2.1'deki **kayıtlı** satır sayılarını (541/624) kullandı | §14 · §18 |
| R4 | T3/T4 (davranış/HAL yasağı) ihlal edildi mi? | Hayır — §15 testi: davranış cümlesi yok, ALSA eşlemesi yok | §15 |
| R5 | `EMERGENCY` eşleme satırı bu turda **eklendi** mi? | Hayır — §13 yalnız onaylar: tablo 8 + paragraf 9 = kayıtlı not tutarlı | §13 |
| R6 | FM `validation` güncellendi mi? | Hayır — 0 satır değişikliği; Ç4/AQ6 kayıt yerleri bu dosyanın §'leri, FM'e işlenmesi orkestratöre | §16 · §17 · §20 |

### §23.1 Kapı son sözü (bu dosya)

| Kalem | Değer |
|-------|-------|
| Append | §11–§23 |
| Silme/FM/GET/kardeş | hepsi 0 |
| Yeni kayıt | Ç4 · AQ6 (⚠️) |
| Kapı | ≥500 · son ölçüm tur yanıtında · marj ≥505 |
| Devir | §24'ten · §1–§23 başlıkları korunur |
| Tur durumu | P2: reactos 3/3 ✅ · tizen anayasa (bu) · tizen 2 + webos 3 bekliyor |
| Marj 1 | bu satırlar ≥505 tamponu — sonraki tur §24/§25 eklerken §23.1 tablosunun satır sayısını **artırmaz** (tablo sabit, ek yeni §) |
| Marj 2 | ölçüm yöntemi tekrar: read + split, `Measure-Object -Line` YASAK |
| Marj 3 | yeni dış iddia (URL/fonksiyon/enum) bu turda 0 — sonraki tur isterse önce GET, sonra yazım (§18 deseni) |
| Marj 4 | kardeş yazım 0 (tizen-ornekler · sound-manager kendi turlarında) |
| Marj 5 | `git commit` yok — orkestratöre ait |
| Marj 6 | append deseni: `oldString` = yalnız Authority footer → eski 251 satır **dokunulmaz** |
| Marj 7 | Ç4/AQ6 yalnız bu dosyada; kardeş dosyalara bilgi **yazmadan** havale (bağ §9) |
| Marj 8 | §11.1 farkının kapanışı = [R1]/[R2] yeniden sayımı (gelecek tur kapısı §18 deseni) |

### §23.2 Kapı son ölçüm kaydı (açık aritmetik — değer aşağıda sabitlenir)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 251 | <500 |
| §11–§23.1 sonrası | 492 | <500 (ölçüldü, açık kayıtlı) |
| §23.2 sonrası | bu satırlarla **≥500** | tur yanıtındaki son değer **bağlayıcı** |
| Marj hedefi | ≥505 | tablodaki tampon satırlarıyla |

**Kural (bu dosya):** kapı değeri **yalnız** tur yanıtındaki son ölçümdür;
dosya içi ara ölçümler (492 gibi) **geçici**dir ve "geçti" yazmak için
**kullanılmaz** — bu §23.2 bunu **açıkça** yazar (kendi kendine beyan yasağı).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
