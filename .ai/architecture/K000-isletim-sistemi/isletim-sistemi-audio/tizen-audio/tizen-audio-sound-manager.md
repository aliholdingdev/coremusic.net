---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-sound-manager
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-anayasa
evidence:
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/sound-manager.md
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__STREAM__POLICY__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__VOLUME__MODULE.html
  - https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__MODULE.html
  - https://docs.tizen.org/iot/api/4.0/tizen-iot-headless/group__CAPI__MEDIA__SOUND__MANAGER__SESSION__MODULE.html
validation:
  - "2026-10-13 · GÖVDE-GET (birinci dalga): Samsung/tizen-docs sound-manager.md rehberi indirildi (541 satır) — bu dosyadaki rehber kaynaklı tüm imza/örnekler birebir alıntıdır"
  - "2026-10-13 · GÖVDE-GET (ikinci dalga): 10.0 statik HTML API modülleri indirildi — Device 23 fn · Volume 11 fn · Stream Policy 27 fn · ana modül hata enumu 11 kod · enum üye sayımları dosya-taramasıyla doğrulandı (mask 6 · device_type 10 · focus_mask 3 · focus_state 2)"
  - "2026-10-13 · Session API sayfası (docs.tizen.org) indeks-alıntısı düzeyinde kalır"
open_questions:
  - "SM-Q2 ✅ KAPANDI: get_device_state 10.0 fonksiyon listesinde YOK — resmen kaldırılmış"
  - "SM-Q3 Session modülü hangi sürümde deprecated edildi → UNKNOWN (10.0 referans setinde modül dosyası yok)"
group: mimari
---

# tizen-audio-sound-manager — Device · Volume · Stream · Focus · Routing · Session

> **Bu dosya ne değildir:** Davranış (ducking/öncelik) tartışması bu dosyada
> **yapılmaz** — davranış modeli kardeş `../../isletim-sistemi-turu/tizen/tizen-ses-yoneticisi.md`'dedir.
> Bu dosya `sound_manager.h` yüzeyinin **API haritasıdır**; imzalar önce resmi
> rehber gövdesinden (gövde-GET), olmayanlar API sayfası alıntısından alınır.

**ELI15:** Tizen'in `sound_manager.h` kütüphanesi bir uygulamanın sesle ilgili
her kararını taşır: hangi cihaz (Device), hangi seviye (Volume), hangi niyet
(stream), kim önde (Focus), nereye gidiyor (Routing). Ve eski bir bölüm daha var
— Session — ama o bölüm artık terk edilmiş (`deprecated`).

---

## §1 Künye

| Alan | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-sound-manager` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio` |
| Kademe | 5 · yaprak-modül |
| group | `mimari` |
| Doğrulama tarihi | 2026-10-13 |
| Kanıt yöntemi | **Birincil: gövde-GET** ([G] rehber) · ikincil: indeks alıntısı ([A]) |

## §1.1 Kaynaklar

| Etiket | Kaynak | URL | Durum |
|---|---|---|---|
| [G] | Sound Manager resmi rehberi (tam metin 541 satır) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/guides/multimedia/sound-manager.md | ✅ GET 200 · indirildi |
| [R-D] | 10.0 Device API modülü (statik HTML) | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html | ✅ GET 200 · 137 KB · 23 fn |
| [R-S] | 10.0 Stream Policy modülü | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__STREAM__POLICY__MODULE.html | ✅ GET 200 · 189 KB · 27 fn |
| [R-V] | 10.0 Volume modülü | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__VOLUME__MODULE.html | ✅ GET 200 · 63 KB · 11 fn |
| [R-M] | 10.0 Sound Manager ana modül | https://raw.githubusercontent.com/Samsung/tizen-docs/master/docs/application/native/api/common/10.0/group__CAPI__MEDIA__SOUND__MANAGER__MODULE.html | ✅ GET 200 · 15 KB · hata enumu 11 kod |
| [A-S] | Session API sayfası (4.0, iot-headless) | https://docs.tizen.org/iot/api/4.0/tizen-iot-headless/group__CAPI__MEDIA__SOUND__MANAGER__SESSION__MODULE.html | ⚠️ indeks alıntısı |

---

## §2 Volume — Ses Seviyesi Modülü ([G] gövde-GET)

> [G] §Controlling the Volume: *"you can set and get a volume level and a maximum
> volume level of a particular sound type."*

| Fonksiyon | Görev (rehber metniyle) |
|---|---|
| `sound_manager_get_volume(sound_type, &volume)` | bir sound tipinin mevcut seviyesini alır |
| `sound_manager_get_max_volume(sound_type, &max_volume)` | sistem maksimumunu alır (set öncesi sınır bu) |
| `sound_manager_set_volume(sound_type, value)` | seviyeyi ayarlar — rehber uyarısı: *"Make sure the value is within the system maximum volume by using the sound_manager_get_max_volume() function"* |
| `sound_manager_add_volume_changed_cb(cb, user_data, &id)` | değişim bildirimi kaydı |
| `sound_manager_get_current_sound_type(&type)` | *"if there is an active output stream ... returns the sound type of that stream, and if not, it returns an error message."* |

**Callback imzası ([G] birebir):**

```text
[ETİKET: birebir alıntı · kaynak: [G] §Controlling the Volume]
static void
_sound_manager_volume_changed_cb(sound_type_e type, unsigned int volume, void *user_data)
```

Örnek kullanım (birebir): `sound_manager_get_volume(SOUND_TYPE_MEDIA, &cur_vol);`
· `sound_manager_get_max_volume(SOUND_TYPE_MEDIA, &max_vol);` ·
`sound_manager_set_volume(SOUND_TYPE_MEDIA, value);`

### §2.1 Ducking yüzeyi ([R-V] 10.0 — 5 fonksiyon · Since 5.5)

| Fonksiyon | Görev |
|---|---|
| `sound_manager_create_stream_ducking(target_stream, cb, user_data, &ducking)` | hedef stream tipi için ducking handle'ı — callback: `sound_stream_ducking_state_changed_cb` |
| `sound_manager_destroy_stream_ducking(ducking)` | handle'ı serbest bırakır |
| `sound_manager_activate_ducking(ducking, duration, ratio)` | *"Activates ducking, asynchronously."* — `duration` ms cinsinden, `ratio` (0.0–1.0 arası kesir) |
| `sound_manager_deactivate_ducking(ducking)` | *"Deactivates ducking, asynchronously."* |
| `sound_manager_is_ducked(ducking, &is_ducked)` | *"Checks if the stream is ducked."* |

> **Sınır:** ducking davranış modeli (hedefin gerçekten kısılması, geri dönüş
> zamanlaması) kardeş `tizen-ses-yoneticisi.md`'dedir — burada yalnız API
> yüzeyi.

**Modül tamamlık:** Volume modülü 10.0'da **11 fonksiyon** = 5 taban (§2) +
5 ducking (§2.1) + `sound_manager_remove_volume_changed_cb` — dosya-tarama
sayımıyla doğrulandı ([R-V]).

---

## §3 Device — Cihaz Modülü

### §3.1 Liste ve gezinme ([G] gövde-GET)

| Fonksiyon | Görev |
|---|---|
| `sound_manager_get_device_list(mask, &list)` | bağlı cihaz listesini alır — `sound_device_list_h` döner |
| `sound_manager_free_device_list(list)` | liste + tüm üyeleri serbest bırakır (`SOUND_MANAGER_ERROR_NONE` kontrolü örnekli) |
| `sound_manager_get_next_device(list, &device)` | ileri gezinme — döngü sonu `SOUND_MANAGER_ERROR_NO_DATA` |
| `sound_manager_get_prev_device(list, &device)` | geri gezinme ([A-D] alıntı) |

**Mask kombinasyonu örneği ([G] birebir):**

```text
[ETİKET: birebir alıntı · kaynak: [G] §Querying Sound Devices]
sound_device_mask_e mask = SOUND_DEVICE_IO_DIRECTION_OUT_MASK |
                           SOUND_DEVICE_IO_DIRECTION_BOTH_MASK;
```

Görülen mask bayrakları: `SOUND_DEVICE_IO_DIRECTION_OUT_MASK` ·
`SOUND_DEVICE_IO_DIRECTION_IN_MASK` (routing örneğinde) ·
`SOUND_DEVICE_IO_DIRECTION_BOTH_MASK` · `SOUND_DEVICE_ALL_MASK` (AEC örneğinde).

**Tam mask kümesi ([R-D] 10.0 dosya-taraması — 6 üye ✅ SM-Q4 kapandı):**

| Bayrak | Anlam |
|---|---|
| `SOUND_DEVICE_IO_DIRECTION_IN_MASK` | giriş yönü |
| `SOUND_DEVICE_IO_DIRECTION_OUT_MASK` | çıkış yönü |
| `SOUND_DEVICE_IO_DIRECTION_BOTH_MASK` | her iki yön |
| `SOUND_DEVICE_TYPE_INTERNAL_MASK` | dahili cihazlar |
| `SOUND_DEVICE_TYPE_EXTERNAL_MASK` | harici cihazlar |
| `SOUND_DEVICE_ALL_MASK` | tümü |

**Cihaz tipi enumu ([R-D] — 10 üye):** `BUILTIN_SPEAKER` · `BUILTIN_RECEIVER` ·
`BUILTIN_MIC` · `AUDIO_JACK` · `BLUETOOTH_MEDIA` (Since 3.0) · `HDMI` ·
`FORWARDING` (Since 3.0) · `USB_AUDIO` · `BLUETOOTH_VOICE` (Since 3.0) ·
`NETWORK` (Since 5.5).

### §3.2 Sorgu fonksiyonları ([G] gövde-GET)

| Fonksiyon | Görev |
|---|---|
| `sound_manager_get_device_type(device, &type)` | cihaz türü — örnekte: `SOUND_DEVICE_BLUETOOTH_MEDIA` · `SOUND_DEVICE_AUDIO_JACK` |
| `sound_manager_get_device_io_direction(device, &direction)` | IO yönü — örnekte: `SOUND_DEVICE_IO_DIRECTION_BOTH` ("both headphone and mic") |
| `sound_manager_get_device_id(device, &id)` · `get_device_name` | kimlik ve ad |
| `sound_manager_is_device_running(device, &running)` | *"To verify whether the device is running. This means that a stream is running on the device."* |

### §3.3 USB-audio genişletmesi ([G] — yalnız `SOUND_DEVICE_USB_AUDIO` + `IO_DIRECTION_OUT`)

| Fonksiyon | Görev (rehber birebir) |
|---|---|
| `get/set_supported_sample_formats(device, ...)` | desteklenen numune formatları |
| `get/set_sample_format(device, ...)` | formatı oku/ayarla |
| `get/set_supported_sample_rates(device, ...)` | desteklenen hızlar (örnek: `SOUND_SAMPLE_RATE_48000` seçimi döngüsü) |
| `get/set_sample_rate(device, ...)` | hızı oku/ayarla |
| `set_media_stream_only(device, true)` | *"no other stream type is allowed to the device except for `SOUND_STREAM_TYPE_MEDIA`"* + `get_media_stream_only` |
| `set_avoid_resampling(device, enable)` | *"the device uses the original sample format and rate of the first stream without resampling if supported"* + `get_avoid_resampling` |

### §3.4 Durum callback'leri ([G] — iki kayıt, birebir imzalar)

| Fonksiyon | Olay | Callback imzası |
|---|---|---|
| `sound_manager_add_device_connection_changed_cb(mask, cb, user_data)` | cihaz takma/çıkarma | `void cb(sound_device_h device, bool is_connected, void *user_data)` |
| `sound_manager_add_device_running_changed_cb(mask, cb, user_data)` | çalışma durumu değişimi | `void cb(sound_device_h device, bool is_running, void *user_data)` |
| `sound_manager_set_device_event_cb(...)` | cihaz **ekleme/silme** olayı (id · name · devpath · state) | [A-D] alıntı · 10.0 fonksiyon listesinde **YOK** ([R-D]) — kaldırılmış olabilir → SM-Q6 |

Rehber notları (birebir): *"The initial state of the internal sound device is
connected."* · *"The state will be changed to **running** when the first stream
goes to the device."*

✅ `remove_device_connection_changed_cb` / `remove_device_running_changed_cb`
10.0 fonksiyon listesinde mevcut ([R-D] — kaldırma kayıt fonksiyonları).

### §3.5 Deprecated üye — 10.0'da kaldırıldı

| Fonksiyon | Etiket |
|---|---|
| `sound_manager_get_device_state(device, &state)` | **TIZEN_DEPRECATED_API** — "will be removed in 9.0" ([A-D] alıntı) · ✅ **TEYİTLİ**: 10.0 resmi fonksiyon listesinde (23 fn sayımı) **YOK** ([R-D]) — kaldırma gerçekleşmiş |

---

## §4 Stream — Bağlam Handle'ı ([G] gövde-GET)

| İddia | Rehber metni (birebir) |
|---|---|
| Handle ne yapar | *"Once a handle created, it is used in the stream playback and recording APIs, such as Player, WAV Player, and Audio I/O"* |
| Çokluk | *"An application can have multiple stream information handles and each handle can have multiple sound streams."* |
| Oluşturma | `sound_manager_create_stream_information(SOUND_STREAM_TYPE_MEDIA, sound_stream_focus_state_changed_cb, NULL, &stream_info)` |
| İmza okuması | `(sound_stream_type_e type, sound_stream_focus_state_changed_cb_t cb, void *user_data, sound_stream_info_h *stream_info)` |
| Serbest bırakma | `sound_manager_destroy_stream_information(stream_info)` (AEC/NS örneklerinde) |

`sound_stream_type_e` sınıflandırması ve stream→sound eşleme tablosu:
anayasa §5.1'dedir (burada tekrarlanmaz — SSOT).

---

## §5 Focus — Odak API Yüzeyi ([G] gövde-GET · davranış modeli kardeşte)

| Fonksiyon | Görev (rehber birebir) |
|---|---|
| `sound_manager_acquire_focus(stream_info, focus_mask, behavior, reserved)` | örnekte: `sound_manager_acquire_focus(stream_info, SOUND_STREAM_FOCUS_FOR_BOTH, behavior, NULL)` · `behavior = SOUND_BEHAVIOR_NONE` |
| `sound_manager_release_focus(stream_info, focus_mask, behavior, reserved)` | biten akışta bırakma |
| `sound_manager_get_focus_state(stream_info, &playback_state, &recording_state)` | callback içinde anlık durum |
| `sound_manager_get_focus_reacquisition(stream_info, &enable)` / `set_focus_reacquisition(...)` | *"Focus reacquisition is enabled by default. To disable it, call ... with the second parameter set to `false`."* |

**Odak callback imzası ([G] birebir — 7 parametre):**

```text
[ETİKET: birebir alıntı · kaynak: [G] §Managing Stream Focus]
static void
sound_stream_focus_state_changed_cb(sound_stream_info_h stream_info, sound_stream_focus_mask_e focus_mask,
                                    sound_stream_focus_state_e focus_state, sound_stream_focus_change_reason_e reason_for_change,
                                    int sound_behavior, const char *additional_info, void *user_data)
```

Görülen enum üyeleri: `SOUND_STREAM_FOCUS_FOR_BOTH` ·
`SOUND_STREAM_FOCUS_STATE_RELEASED` · `SOUND_STREAM_FOCUS_STATE_ACQUIRED` ·
`SOUND_BEHAVIOR_NONE` · odak maski `sound_stream_focus_mask_e`.

Rehber kuralı (birebir): *"To activate a sound stream, you must acquire stream
focus."* · reacquisition senaryosu: A → B alır → B bırakır → A **otomatik geri
alır** (özellik açıksa).

> **Sınır:** odak-kesinti/davranış öncelik tablosu (kim kimi ezer) bu dosyada
> DEĞİL → `../../isletim-sistemi-turu/tizen/tizen-ses-yoneticisi.md`.

---

## §6 Routing — Manuel Rota ([G] gövde-GET)

| İddia | Rehber metni (birebir) |
|---|---|
| Otomatik rota | *"you cannot select a specific device. The Audio Framework controls automatic stream routing for all stream types, except `SOUND_STREAM_TYPE_VOIP` and `SOUND_STREAM_TYPE_MEDIA_EXTERNAL_ONLY`."* |
| Manuel rota | *"Manual stream routing is done for the `SOUND_STREAM_TYPE_VOIP` and `SOUND_STREAM_TYPE_MEDIA_EXTERNAL_ONLY` stream types."* |
| API | `sound_manager_add_device_for_stream_routing(stream_info, device)` · `sound_manager_apply_stream_routing(stream_info)` |
| Döngü örneği | liste → `get_device_type`/`get_device_io_direction` → USB ise ekle + uygula → `free_device_list` |

---

## §7 Effects — Önişlem Efektleri ([G] gövde-GET)

| Tür | API | Kural (rehber birebir) |
|---|---|---|
| Referanslı (münhasır) | `sound_manager_set_effect_method_with_reference(stream_info, method, device)` | AEC — *"it uses a playback reference which is an exclusive system resource"* → **başka yöntemle birleştirilemez** · örnek yöntem: `SOUND_EFFECT_ACOUSTIC_ECHO_CANCEL_WEBRTC` |
| Referanssız | `sound_manager_set_effect_method(stream_info, methods_mask)` | NS + AGC — *"you can use these methods simultaneously using the 'or' keyword"* · örnek: `SOUND_EFFECT_NOISE_SUPPRESSION_VOIP \| SOUND_EFFECT_AUTOMATIC_GAIN_CONTROL_CAPTURE` |
| Uygulama anı | capture başlarken — *"the capture stream will be preprocessed by the audio framework before receiving it"* | `audio_in_*` akışına `set_sound_stream_info` ile bağlanır |

---

## §8 Session Modülü (deprecated — [A-S] indeks alıntısı + [G] sınır notu)

> [G] rehberin Session bölümü **yoktur** — rehber doğrudan Volume/Device/Stream'e
> gider; Session kanıtı API sayfası alıntısıdır.

### §8.1 Önceden tanımlı ses oturumları

| Oturum | Alıntıdaki karşılığı |
|---|---|
| `SOUND_SESSION_TYPE_MEDIA` | normal medya oynatma |
| `SOUND_SESSION_TYPE_ALARM` | alarm |
| `SOUND_SESSION_TYPE_NOTIFICATION` | bildirim sesi |
| `SOUND_SESSION_TYPE_EMERGENCY` | acil durum sesi |
| `SOUND_SESSION_TYPE_VOIP` | VoIP çağrısı |

### §8.2 Fonksiyonlar (imza-kanıtlı — API sayfası alıntısı)

| Fonksiyon | Görev |
|---|---|
| `sound_manager_set_session_type(session, session_type)` | oturum tipini belirler |
| `sound_manager_set_media_session_option(stream, type, option)` | önce duraklat / aynı anda oynat |
| `sound_manager_set_media_session_resumption_option(stream, option)` | odak geri geldiğinde otomatik devam |
| `sound_manager_set_voip_session_mode(mode, stream, option)` | VoIP oturum kipi + seçenek |
| `sound_manager_set_session_interrupted_cb(cb, user_data)` / `unset_...` | kesinti callback'i |

### §8.3 Davranış (alıntı düzeyinde)

| İddia | Kanıt durumu |
|---|---|
| Alarm çalarken uygulamanın sesi otomatik kısılır (duck), alarm bitince normale döner | indeks alıntısı · gövde-GET ⚠️ · **davranış ayrı dosyada**: `tizen-ses-yoneticisi.md` |
| "Since 3.0" — kayıtlı dışı bırakılmış VoIP işlevi | alıntıda görünür · ⚠️ |

---

## §9 Hata Kodları ([R-M] 10.0 — tam liste ✅ SM-Q5 kapandı)

| # | Kod | Not |
|---|---|---|
| 1 | `SOUND_MANAGER_ERROR_NONE` | başarı — tüm örneklerde dönüş kontrolü |
| 2 | `SOUND_MANAGER_ERROR_INVALID_PARAMETER` | geçersiz parametre |
| 3 | `SOUND_MANAGER_ERROR_OUT_OF_MEMORY` | bellek yetersiz |
| 4 | `SOUND_MANAGER_ERROR_NO_DATA` | veri yok — `get_next_device` döngü sonu |
| 5 | `SOUND_MANAGER_ERROR_INTERNAL` | iç hata |
| 6 | `SOUND_MANAGER_ERROR_INVALID_OPERATION` | geçersiz işlem |
| 7 | `SOUND_MANAGER_ERROR_INVALID_STATE` | geçersiz durum |
| 8 | `SOUND_MANAGER_ERROR_NOT_SUPPORTED` | desteklenmiyor |
| 9 | `SOUND_MANAGER_ERROR_NO_PLAYING_SOUND` | çalan ses yok — `get_current_sound_type` hatası |
| 10 | `SOUND_MANAGER_ERROR_PERMISSION_DENIED` | yetki yok |
| 11 | `SOUND_MANAGER_ERROR_POLICY` | politika kısıtı |

> Ses I/O tarafının ayrı enumu `audio_io_error_e` (**13 kod**) anayasa §6.3'tedir.

---

## §10 Sınır

| Dışında olan | Nereye |
|---|---|
| Odak/rota/ducking **davranış modeli** ve öncelik tablosu | `../../isletim-sistemi-turu/tizen/tizen-ses-yoneticisi.md` |
| PCM `audio_out_*`/`audio_in_*` çağrı dizisi + kod örnekleri | kardeş `tizen-audio-ornekler.md` |
| `sound_stream_type_e` + eşleme tablosu (SSOT) | `tizen-audio-anayasa.md` §5.1 |
| HAL/ALSA eşlemesi | `../../isletim-sistemi-turu/tizen/tizen-audio-hal.md` |
| Windows ses oturumları (WASAPI session) | `../wasapi/` |

---

## §11 Açık sorular

| # | Soru | Etiket | Durum |
|---|---|---|---|
| SM-Q1 | `add_device_connection_changed_cb` parametre listesi? | ✅ KAPANDI | [G]: `(mask, cb, user_data)` · cb `(device, is_connected, user_data)` |
| SM-Q2 | `get_device_state` kaldırılma sürümü 9.0 mı? | ✅ KAPANDI | 10.0 listesinde YOK — kaldırılmış (§3.5) |
| SM-Q3 | Session modülü hangi sürümde deprecated? | UNKNOWN | Açık — 10.0 referans setinde modül dosyası yok |
| SM-Q4 | `device_mask` tam bayrak kümesi? | ✅ KAPANDI | 6 bayrak — §3.1 ([R-D] dosya-taraması) |
| SM-Q5 | `SOUND_MANAGER_ERROR_*` tam kod listesi? | ✅ KAPANDI | 11 kod — §9 ([R-M]) |
| SM-Q6 | `set_device_event_cb` 10.0'da var mı? | ⚠️ VERIFICATION REQUIRED | 10.0 fonksiyon listesinde (23 fn) YOK — kaldırılmış olabilir; callback parametreleri de yalnız alıntı düzeyinde |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
