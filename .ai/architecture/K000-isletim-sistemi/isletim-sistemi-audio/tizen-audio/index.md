---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children:
  - K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-sound-manager
  - K000-isletim-sistemi/isletim-sistemi-audio/tizen-audio/tizen-audio-ornekler
depends_on: []
evidence:
  - https://docs.tizen.org/iot/api/5.5/tizen-iot-headed/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html
  - https://docs.tizen.org/iot/api/4.0/tizen-iot-headless/group__CAPI__MEDIA__SOUND__MANAGER__SESSION__MODULE.html
  - https://docs.tizen.org/iot/api/5.0/tizen-iot-headed/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html
  - https://docs.tizen.org/iot/api/5.5/tizen-iot-headed/group__CAPI__MEDIA__AUDIO__IN__MODULE.html
  - https://docs.tizen.org/application/dotnet/api/TizenFX/API12/api/Tizen.Multimedia.AudioManager.html
  - https://samsungtizenos.com/docs/web/guides/multimedia/audio/
validation:
  - "2026-10-13 · 6 URL · içerik websearch indeks alıntısıyla doğrulandı (fonksiyon imzaları görünür) · doğrudan GET application/native yolu samsungtizenos.com docs ana sayfasına yönlendirildi · eski 'SPA kabuğu' bulgusu (üst dal) /iot/api/ yolları için aşılmıştır, gövde-GET hâlâ ⚠️ · isletim-sistemi-audio/ içi kesişimi 0"
open_questions:
  - "Doğrudan gövde-GET ile /iot/api/ imzaları yeniden üretilebilir mi? (şu an indeks-alıntısı) ⚠️ VERIFICATION REQUIRED"
  - "CoreMusic reposunda Tizen/CAPI ses kodu YOK → UNKNOWN"
  - "Odak/ducking öncelik tablosu üst daldaki gibi UNKNOWN (kardeş dosya §11 Q2)"
group: mimari
---

# tizen-audio — Deep-Dal İndeksi (Kademe-4)

**ELI15:** Tizen'de uygulama ses kartına **doğrudan dokunmaz**: `Sound Manager` CAPI
modülleri cihazı/oturumu/akış bilgisini yönetir, `Audio Output/Input` API'si PCM
yazıp okur; her şey Linux'un ALSA üstündeki HAL'ine iner. Bu dal bu API'lerin
imza-kanıtlı haritasıdır.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | Tizen (Samsung · giyilebilir/TV/IoT) |
| Repo durumu | CoreMusic'te Tizen kodu YOK → UNKNOWN (§4 Q3) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Sound Manager **Device** API cihaz listesi/öznitelik/callback'lerini (`sound_manager_get_device_list` · `get_device_type` · `get_supported_sample_rates` · `add_device_connection_changed_cb` ...) imzalarıyla yayınlar | https://docs.tizen.org/iot/api/5.5/tizen-iot-headed/group__CAPI__MEDIA__SOUND__MANAGER__DEVICE__MODULE.html | 2026-10-13 | ✅ indeks alıntısında imzalar görünür · gövde-GET ⚠️ |
| Sound Manager **Session** API önceden tanımlı oturumları (media · alarm · notification · emergency · voip) sayar; `sound_manager_set_session_type` vb. **deprecated** işaretlidir | https://docs.tizen.org/iot/api/4.0/tizen-iot-headless/group__CAPI__MEDIA__SOUND__MANAGER__SESSION__MODULE.html | 2026-10-13 | ✅ indeks alıntısında metin + imzalar |
| **Audio Output** API (`audio_out_create_new` · `prepare` · `write` · `drain` · `flush`) örnek hızını 8000–48000 Hz (5.0 öncesi) / 8000–192000 Hz (5.0+) belgeler | https://docs.tizen.org/iot/api/5.0/tizen-iot-headed/group__CAPI__MEDIA__AUDIO__OUT__MODULE.html | 2026-10-13 | ✅ indeks alıntısında parametre tablosu |
| **Audio Input** API `http://tizen.org/privilege/recorder` gerektirir; numune tipi 5.5'ten itibaren 8/16/24/32-bit, kanal 5.5'ten itibaren çok-kanal | https://docs.tizen.org/iot/api/5.5/tizen-iot-headed/group__CAPI__MEDIA__AUDIO__IN__MODULE.html | 2026-10-13 | ✅ indeks alıntısında "Since" notları |
| .NET yüzeyinde `Tizen.Multimedia.AudioManager` sınıfı cihaz listesi + bağlantı olayları sunar | https://docs.tizen.org/application/dotnet/api/TizenFX/API12/api/Tizen.Multimedia.AudioManager.html | 2026-10-13 | ✅ indeks alıntısında metot listesi |
| Web uygulaması için ayrı Audio Management rehberi yayımlanır | https://samsungtizenos.com/docs/web/guides/multimedia/audio/ | 2026-10-13 | ✅ arama sonucu başlık + URL |

**Yöntem notu (bağlayıcı):** `docs.tizen.org` derin yolları bu turda **websearch indeks
alıntısıyla** içerik düzeyinde doğrulandı (fonksiyon imzaları alıntıda görünür); doğrudan
GET hâlâ samsungtizenos.com yönlendirmesi/SPA kabuğu riski taşıdığı için gövde-kanıtı
adımları ⚠️ işaretlidir. Üst dal `../../isletim-sistemi-turu/tizen/tizen-web-arastirma.md`
§8.1'in "SPA kabuğu" bulgusu `/iot/api/` yolları için **geçersiz kalmıştır**, tam gövde
GET'i sonraki tur kapısıdır (§4 Q1).

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-13)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |
| 2 | `tizen-audio-anayasa.md` | Kimlik · katman yığını · stream/device/session kavramları | **oluşturuldu** |
| 3 | `tizen-audio-sound-manager.md` | Device + Session CAPI modülleri · imza tabloları · deprecated uyarıları | **oluşturuldu** |
| 4 | `tizen-audio-ornekler.md` | `audio_out`/`audio_in` çağrı dizisi · parametre aralıkları · .NET eşlemesi | **oluşturuldu** |

> Kural: bu 4 dosyanın yüzeyi bu deep-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-audio/*.md` ve diğer 6 deep-dala bu turda **dokunulmadı**.

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| Tizen ses politikası (odak · rota · ducking davranış modeli) | `../../isletim-sistemi-turu/tizen/tizen-ses-yoneticisi.md` |
| Audio HAL / ALSA cihaz katmanı detayı | `../../isletim-sistemi-turu/tizen/tizen-audio-hal.md` |
| GStreamer pipeline / multimedya çatısı | `../../isletim-sistemi-turu/tizen/tizen-multimedya-altyapisi.md` |
| Buffer/underrun hesabı | `../../isletim-sistemi-turu/tizen/tizen-gercek-zamanli-tampon.md` |
| Manifest/privilege paketleme geneli | `../../isletim-sistemi-turu/tizen/tizen-uygulama-ortami.md` |
| DSP zinciri | K004-dsp · Neva Engine | K003-audio |
| Linux ALSA'nın kendisi | `../alsa/` |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| T1 | `/iot/api/` sayfalarının tam gövdesi doğrudan GET ile alınabilir mi? | ⚠️ VERIFICATION REQUIRED |
| T2 | CoreMusic/Neva Engine hedefinde Tizen var mı? (repo 0 isabet) | UNKNOWN |
| T3 | `SOUND_SESSION_TYPE_*` oturum öncelik tablosu (kardeş §11 Q2) ne zaman kapanır? | ⚠️ VERIFICATION REQUIRED |
| T4 | `avoid_resampling` / `media_stream_only` cihaz bayrakları hangi cihaz sınıfında etkin? | ⚠️ VERIFICATION REQUIRED |
| T5 | 512 örnek · 48 kHz ölçüm hedefi Tizen cihazda doğrulandı mı? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `tizen-audio-anayasa.md` | Kimlik, katman yığını ve kavram üçlüsü | Kademe-4 |
| mimari | `tizen-audio-sound-manager.md` | Device/Session CAPI imza tabloları | Kademe-4 |
| kod | `tizen-audio-ornekler.md` | Çağrı dizisi, parametre aralıkları, .NET eşlemesi | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
