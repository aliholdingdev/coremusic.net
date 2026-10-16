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

---

## §12 Modül Çağrı Zarfı Envanteri (dosya içi sayım — bu turda yeni GET yok)

**Yöntem:** aşağıdaki satırlar bu dosyanın §2–§8 tablolarında **zaten adı geçen**
fonksiyonlardır; isim/imza bu §'de ilk kez iddia edilmez. Her satır → kaynak §.

### §12.1 Modül bazlı sayım

| Modül | Dosya içi adı geçen | Kaynak § | Modül başlık sayımı |
|---|---|---|---|
| Volume | **11** | §2 (5) + §2.1 (5) + §2.1 tamamlama notu (1) | [R-V] **11** ✅ eşleşti |
| Device | **25** | §3.1 (4) + §3.2 (5) + §3.3 (12) + §3.4 (4) | [R-D] **23** ⚠️ fark 2 → SM-Q7 |
| Stream | **2** | §4 (create/destroy_stream_information) | — (ana modül [R-M]) |
| Focus | **5** | §5 (4 satır · son satır get+set = 2) | — (Stream Policy [R-S] 27 fn — modül toplamı, sayım bu turda değil) |
| Routing | **2** | §6 | — |
| Effects | **2** | §7 | — |
| Session | **6** | §8.2 (5 satır · son satır cb+unset = 2) | ⚠️ deprecated — [A-S] alıntı |
| **Toplam** | **53** | §12.1 satırlarının toplamı | — |

### §12.2 Volume — 11 kayıt

| # | Fonksiyon | Kayıt § |
|---|---|---|
| 1 | `sound_manager_get_volume` | §2 |
| 2 | `sound_manager_get_max_volume` | §2 |
| 3 | `sound_manager_set_volume` | §2 |
| 4 | `sound_manager_add_volume_changed_cb` | §2 |
| 5 | `sound_manager_get_current_sound_type` | §2 |
| 6 | `sound_manager_create_stream_ducking` | §2.1 |
| 7 | `sound_manager_destroy_stream_ducking` | §2.1 |
| 8 | `sound_manager_activate_ducking` | §2.1 |
| 9 | `sound_manager_deactivate_ducking` | §2.1 |
| 10 | `sound_manager_is_ducked` | §2.1 |
| 11 | `sound_manager_remove_volume_changed_cb` | §2.1 tamamlama notu |

### §12.3 Device — 25 adı geçen kayıt (içindekiler Listesi · ⚠️ SM-Q7)

| # | Fonksiyon | Kayıt § | Not |
|---|---|---|---|
| 1 | `sound_manager_get_device_list` | §3.1 | maske ile |
| 2 | `sound_manager_free_device_list` | §3.1 | liste+üyeler serbest |
| 3 | `sound_manager_get_next_device` | §3.1 | döngü sonu NO_DATA |
| 4 | `sound_manager_get_prev_device` | §3.1 | **yalnız [A-D] alıntısı** — [R-D] onayı satırda yok |
| 5 | `sound_manager_get_device_type` | §3.2 | |
| 6 | `sound_manager_get_device_io_direction` | §3.2 | |
| 7 | `sound_manager_get_device_id` | §3.2 | [G] gövde |
| 8 | `sound_manager_get_device_name` | §3.2 | [G] gövde |
| 9 | `sound_manager_is_device_running` | §3.2 | |
| 10 | `get_supported_sample_formats` | §3.3 | USB — `sound_manager_` öneki satırda kısaltılmış |
| 11 | `set_supported_sample_formats` | §3.3 | USB |
| 12 | `get_sample_format` | §3.3 | USB |
| 13 | `set_sample_format` | §3.3 | USB |
| 14 | `get_supported_sample_rates` | §3.3 | USB |
| 15 | `set_supported_sample_rates` | §3.3 | USB |
| 16 | `get_sample_rate` | §3.3 | USB |
| 17 | `set_sample_rate` | §3.3 | USB |
| 18 | `set_media_stream_only` | §3.3 | USB + `get_media_stream_only` (19) |
| 19 | `get_media_stream_only` | §3.3 | aynı satırda |
| 20 | `set_avoid_resampling` | §3.3 | USB + `get_avoid_resampling` (21) |
| 21 | `get_avoid_resampling` | §3.3 | aynı satırda |
| 22 | `add_device_connection_changed_cb` | §3.4 | |
| 23 | `add_device_running_changed_cb` | §3.4 | |
| 24 | `remove_device_connection_changed_cb` | §3.4 | [R-D] mevcut onayı |
| 25 | `add/remove_device_running_changed_cb` (remove) | §3.4 | [R-D] mevcut onayı |

**Dışarıda (kanıtlı):** `sound_manager_set_device_event_cb` — 10.0'da YOK (§3.4 · SM-Q6) ·
`sound_manager_get_device_state` — deprecated + 10.0'da YOK (§3.5 · SM-Q2).

### §12.4 Kalan modüller — 12 satır / 17 fonksiyon

| Modül | # | Fonksiyon | Kayıt § |
|---|---|---|---|
| Stream | 1 | `sound_manager_create_stream_information` | §4 |
| Stream | 2 | `sound_manager_destroy_stream_information` | §4 |
| Focus | 3 | `sound_manager_acquire_focus` | §5 |
| Focus | 4 | `sound_manager_release_focus` | §5 |
| Focus | 5 | `sound_manager_get_focus_state` | §5 |
| Focus | 6 | `sound_manager_get_focus_reacquisition` | §5 |
| Focus | 7 | `sound_manager_set_focus_reacquisition` | §5 |
| Routing | 8 | `sound_manager_add_device_for_stream_routing` | §6 |
| Routing | 9 | `sound_manager_apply_stream_routing` | §6 |
| Effects | 10 | `sound_manager_set_effect_method_with_reference` | §7 |
| Effects | 11 | `sound_manager_set_effect_method` | §7 |
| Session | 12 | `set_session_type` · `set_media_session_option` · `set_media_session_resumption_option` · `set_voip_session_mode` · `set_session_interrupted_cb` · `unset_...` (6 adet) | §8.2 |

---

## §13 Callback Zarfı Envanteri (dosya içi — 6 kayıt)

| # | Callback | İmza kanıtı | Kayıt § |
|---|---|---|---|
| C1 | `_sound_manager_volume_changed_cb` | `(sound_type_e, unsigned int, void *)` — 3 param | §2 (birebir) |
| C2 | `sound_stream_focus_state_changed_cb` | **7 param** — birebir tam metin | §5 (birebir) |
| C3 | device connection changed | `(sound_device_h, bool, void *)` — 3 param | §3.4 (birebir) |
| C4 | device running changed | `(sound_device_h, bool, void *)` — 3 param | §3.4 (birebir) |
| C5 | `sound_stream_ducking_state_changed_cb` | **yalnız ad** — imza bu dosyada yok → UNKNOWN | §2.1 |
| C6 | session interrupted cb | **parametre listesi yok** — yalnız alıntı düzeyi | §8.2 |

---

## §14 Hata-Yönetimi Desenleri (§9 türetim)

| # | Desen | Kural | Kayıt |
|---|---|---|---|
| H1 | dönüş kontrolü | her çağrı `== SOUND_MANAGER_ERROR_NONE` | §9 [1] · §3.1 örnek notu |
| H2 | gezinme bitişi | `get_next_device` → `SOUND_MANAGER_ERROR_NO_DATA` | §9 [4] · §3.1 |
| H3 | aktif ses yokluğu | `get_current_sound_type` → `SOUND_MANAGER_ERROR_NO_PLAYING_SOUND` | §9 [9] · §2 |
| H4 | yetki | `SOUND_MANAGER_ERROR_PERMISSION_DENIED` — kayıt privilege'ı ayrı dosyada | §9 [10] |
| H5 | politika | `SOUND_MANAGER_ERROR_POLICY` | §9 [11] |
| H6 | enum sınırı | sound_manager 11 kod · `audio_io_error_e` 13 kod (farklı enum — karıştırılmaz) | §9 · §9 alt not |
| H7 | cb içi hata | bu dosyada cb-içi hata-deseni **kayıtlı değil** → kardeş ornekler §4.8/§4.9 | §10 sınır satırı |

---

## §15 Örüntü Zincirleri (§2–§8 alıntılarından türetilmiş akışlar)

### §15.1 Seviye oku-ayarla (§2)

1. `get_volume(type, &v)` · `get_max_volume(type, &max)` — §2 örnek alıntısı
2. rehber kuralı: *"Make sure the value is within the system maximum volume"* — §2
3. `set_volume(type, value)` — §2
4. bildirim isteğe bağlı: `add_volume_changed_cb` → cb → `remove_volume_changed_cb` — §2 · §2.1

### §15.2 Cihaz listesi döngüsü (§3.1 · §6)

1. `get_device_list(mask, &list)` — §3.1
2. `get_next_device(list, &dev)` döngüsü — `ERROR_NONE` devam · `NO_DATA` bitiş — §3.1 · §9
3. döngü içinde `get_device_type` + `get_device_io_direction` — §6 döngü örneği
4. `free_device_list(list)` — §6 · §3.1

### §15.3 Odak akışı (§4 · §5)

1. `create_stream_information(type, focus_cb, ud, &info)` — §4
2. *"To activate a sound stream, you must acquire stream focus."* → `acquire_focus(info, mask, behavior, NULL)` — §5
3. akış çalışır → cb 7 parametreyle durum bildirir — §5 C2
4. bitişte `release_focus` — §5
5. `destroy_stream_information` — §4

### §15.4 Manuel rota (§6)

1. stream tipi `VOIP` veya `MEDIA_EXTERNAL_ONLY` — §6 (otomatik rota bu ikide çalışmaz)
2. `get_device_list(IN_MASK, &list)` → döngü — §6
3. USB ise `add_device_for_stream_routing` → `apply_stream_routing` — §6
4. `free_device_list` — §6

### §15.5 Efekt (AEC) kurulumu (§7)

1. `create_stream_information` — §7 (referanslı efekt münhasırdır)
2. `set_effect_method_with_reference(info, ACOUSTIC_ECHO_CANCEL_WEBRTC, device)` — §7
3. **münhasır kural:** *"exclusive system resource"* → başka yöntemle birleştirilemez — §7
4. `audio_in_*` akışına `set_sound_stream_info` ile bağlanır — §7 (detay kardeşte)

### §15.6 NS + AGC (§7)

1. `create_stream_information` — §7
2. `set_effect_method(info, NS_VOIP | AGC_CAPTURE)` — §7 · *"simultaneously using the 'or' keyword"*
3. capture başlarken uygulanır — §7

### §15.7 Ducking (§2.1)

1. `create_stream_ducking(target, cb, ud, &d)` — §2.1
2. `activate_ducking(d, duration_ms, ratio 0.0–1.0)` — *"asynchronously"* — §2.1
3. `is_ducked(d, &flag)` sorgu — §2.1
4. `deactivate_ducking(d)` — §2.1
5. `destroy_stream_ducking(d)` — §2.1

### §15.8 Cihaz olay kaydı (§3.4)

1. `add_device_connection_changed_cb(mask, cb, ud)` — §3.4
2. cb: `(device, is_connected, ud)` — iç cihaz başlangıçta *connected* — §3.4
3. `add_device_running_changed_cb` — ilk stream *running* yapar — §3.4
4. karşılıklı `remove_*_changed_cb` 10.0'da mevcut — §3.4

---

## §16 Kapı Ölçüm Kaydı (bu dosya — açık aritmetik)

| Adım | Satır | Durum |
|------|-------|-------|
| Keşim (2026-10-15) | 338 | <500 |
| §12–§15 sonrası | bu tabloya kadar eklendi | ölçüm tur yanıtında |
| Hedef | **≥505** | marj tablosuyla |
| Yöntem | `read_text_file` → `split(/\r\n|\r|\n/)` → son boş `pop` | `Measure-Object -Line` **YASAK** |

**Marj tablosu:**

| # | Marj kaydı |
|---|---|
| M1 | kapı değeri **yalnız** tur yanıtındaki son ölçümdür — dosya içi ara sayı bağlayıcı değildir |
| M2 | bu tur yeni dış iddia 0: §12–§15 tamamı §2–§11 içinden türetildi (yeni GET yok) |
| M3 | FM + §1 künye dokunulmadı · `group: mimari` korundu |
| M4 | kardeş dosyalara yazım yok (anayasa · ornekler · webos kendi turlarında) |
| M5 | `git commit` yok — orkestratöre ait |
| M6 | append deseni: `oldString` = yalnız Authority footer → eski 338 satır dokunulmaz |

---

## §17 Sayım Gerilimi Kaydı (bulundu — silinmedi, SM-Q7 olarak eklendi)

**Gerilim:** §12.3 bu dosyanın kendi tablolarından Device için **25** adı geçen
fonksiyon sayar; §1.1'de [R-D] 10.0 modül sayımı **23** yazıyor. Fark = **2**.

| Aday açıklık | Gerekçe (dosya içi) | Etiket |
|---|---|---|
| `get_prev_device` | §3.1'de **yalnız [A-D] alıntısı** — [R-D] 10.0 onayı satırda yok | aday 1 |
| §3.3 `get/set_` kısaltması | satırlar `get/set_x` biçiminde — iki fn olarak sayıldı; modül sayımı tek saymış olabilir | aday 2 |
| eşleşme yanlış etiketi | [R-D] 23 sayımı modül başlığından (§1.1) — dosya-tarama tekrarı gerekir | ⚠️ |

**Karar:** çözüm için **dosya-tarama tekrarı** (gelecek tur GET → §3.1–§3.4 satır
satır [R-D] listesiyle eşleme). Bu turda:
- ✅ doğru: her iki sayı da **bu dosyada kayıtlı** ve ikisi de değiştirilmedi;
- ⚠️ açık: hangisinin 10.0 gerçeği olduğu → **VERIFICATION REQUIRED** (SM-Q7 eklendi).

**SM-Q7 (yeni):** `§12.3 sayımı (25) ↔ [R-D] modül sayımı (23)` farkının
kapatılması — etiket ⚠️ VERIFICATION REQUIRED.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
