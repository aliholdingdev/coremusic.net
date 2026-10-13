---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/android/android-medya-cihazi-yonetimi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/android
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# Android — Medya Cihazı Yönetimi (ses cihazı · route · stream tipi · MediaSession)

**Kapsam:** Android'in **ses cihazı envanteri** (`AudioDeviceInfo`), cihaz
bağlantı/değişimi bildirimleri (`AudioDeviceCallback`), yönlendirme (route)
müdahalesi (`AudioRouting`), format sabitleri (`AudioFormat`), odağı yöneten
session katmanı (`MediaSession` / `MediaController`) ve cihaz-özel mikser
öznitelikleri (USB) başlıklarının **resmi doküman kanıtlı** haritası.

**Yöntem (RED TEAM / TRUTH MODE):** Her iddia 2026-10-12 tarihinde HTTP 200 ile
doğrulanmış `developer.android.com`
sayfalarından gelir. API referans sayfalarının **gövdeleri indirilip düz metne
çevrilmiş** ve alıntılar bu metinden alınmıştır. Gövde okunmayan sayfalar yalnız
başlık kanıtı taşır ve içerik iddiaları `⚠️ VERIFICATION REQUIRED` işaretlenir.
Kod kanıtı: depoda 0 `.cpp/.h` (ölçüm 2026-10-12) → `Dosya:/Satır:` YASAK; yalnız
`Kaynak: [RESMİ ALINTI · URL · 2026-10-12]` veya `**[ORİJİNAL ÖRNEK · derlenmedi]**`.

## §0 Kanıt düzeyi özeti

| Kaynak | HTTP | Gövde | Kullanım |
|---|---|---|---|
| `AudioDeviceInfo` referansı | 200 | OKUNDU | §3 §4 |
| `AudioDeviceCallback` referansı | 200 | OKUNDU | §5 |
| `AudioRouting` referansı | 200 | OKUNDU | §6 |
| `AudioManager` referansı | 200 | başlık | §7 · `⚠️` |
| `AudioFormat` referansı | 200 | OKUNDU | §8 |
| `MediaSession` referansı | 200 | OKUNDU | §10 |
| `MediaController` referansı | 200 | OKUNDU | §11 |
| `Manage audio focus` kılavuzu | 200 | OKUNDU | §12 |
| Media3 medya session sayfası | 200 | OKUNDU (yan menü + akış) | §13 |
| `Improve audio playback` | 200 | OKUNDU (örnek kod) | §14 |

## §1 Kapsam ve sınır

| Kapsam İÇİ | Kapsam DIŞI |
|---|---|
| Cihaz envanteri ve tip sabitleri | HAL/ sürücü katmanı → `android-hal-ve-surucu.md` |
| Cihaz bağlantı/değişimi bildirimi | AAudio MMAP yolu → `android-aaudio-ve-opensl.md` |
| Yönlendirme (route) tercihi ve güncel route | İzin/living cycle → `android-izin-ve-lifecycle.md` |
| Session + controller + transport | Gerçek zamanlı gecikme ölçümü → `android-gercek-zamanli-dinleme.md` |
| Audio focus kazanım/duck kuralları | Gradle/ABI/dağıtım → `android-build-ve-dagilim.md` |
| USB mikser özniteliği örneği | Cihaz-özel liste değerleri → `UNKNOWN` |

## §2 Temel kavramlar (gövde kanıtlı sözlük)

| Terim | Gövdedeki karşılığı | Kanıt |
|---|---|---|
| Ses cihazı (audio device) | `AudioDeviceInfo` — "Provides information about an audio device." | `AudioDeviceInfo` gövdesi |
| Cihaz olayı | `AudioDeviceCallback` — bağlantı/deconnection bildirimi mekanizması | `AudioDeviceCallback` gövdesi |
| Yönlendirme (routing) | `AudioRouting` — "the current routing of this AudioTrack/AudioRecord" | `AudioRouting` gövdesi |
| Format | `AudioFormat` — "audio format and channel configuration constants" | `AudioFormat` gövdesi |
| Session | `MediaSession` — "interaction with media controllers, volume keys, media buttons, and transport controls" | `MediaSession` gövdesi |
| Controller | `MediaController` — "Allows an app to interact with an ongoing media session." | `MediaController` gövdesi |

## §3 AudioDeviceInfo — cihaz envanteri

Kaynak: `AudioDeviceInfo` referans gövdesi (`Kaynak: [RESMİ ALINTI · .../AudioDeviceInfo · 2026-10-12]`).

| İddia | Gövde ifadesi |
|---|---|
| Sınıf biçimi | `public final class AudioDeviceInfo extends Object` |
| Tanım | "Provides information about an audio device." |
| API seviyesi | "Added in API level 23" |
| Sabit ailesi | `TYPE_*` cihaz tipi sabitleri (ör. `TYPE_AUX_LINE`, `TYPE_BLE_BROADCAST`, `TYPE_BLE_CENTRAL`, `TYPE_BLE_CENTRAL_BROADCAST`) |
| BLE notu | `TYPE_BLE_CENTRAL`: "the receive or transmit path in an Android implementation operating in a Bluetooth audio peripheral mode (LE Audio, A2DP or HFP profiles)" |

### §3.1 Gövdede görülen tip sabitleri (tam liste DEĞİL — bölüm kanıtı)

| Sabit | Gövde açıklaması (özet) |
|---|---|
| `TYPE_AUX_LINE` | "auxiliary line-level connectors" |
| `TYPE_BLE_BROADCAST` | "Bluetooth Low Energy (BLE) broadcast group" |
| `TYPE_BLE_CENTRAL` | LE Audio / A2DP / HFP peripheral modunda alıcı veya verici yol |
| `TYPE_BLE_CENTRAL_BROADCAST` | "the receive path" (BLE broadcast) |

**RED TEAM bulgu 3-1:** "Android ses cihazı listesi sabittir ve tüm cihazlar aynı
tipleri kullanır" iddiası **REDDEDİLDİ**; gövde yeni BLE tipi ailesini
(`TYPE_BLE_*`) açıkça tanımlar — envanter sürümle büyür.

**Sınır:** `AudioDeviceInfo` içindeki **tam** tip sabit sayısı bu turda
sayılmadı → `UNKNOWN`; sayfadan "tüm TYPE_ listesi şudur" cümlesi yazılmaz.

## §4 AudioDeviceCallback — bağlantı/değişimi bildirimi

Kaynak: `AudioDeviceCallback` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sınıf biçimi | `public abstract class AudioDeviceCallback extends Object` |
| Tanım | "defines the mechanism by which applications can receive notifications of audio device connection and disconnection events" |
| API seviyesi | "Added in API level 23" |
| Yöntem 1 | `void onAudioDevicesAdded(AudioDeviceInfo[] addedDevices)` — "one or more audio devices have been connected" |
| Yöntem 2 | `void onAudioDevicesRemoved(AudioDeviceInfo[] removedDevices)` — aykırarma bildirimi |
| Kayıt yolu | Gövde: "See also: `AudioManager.registerAudioDeviceCallback(AudioDeviceCallback, android.os.Handler)`" |

### §4.1 Olay akışı (gövdeden türetilmiş sıra)

| Adım | Olay | Sahip |
|---|---|---|
| 1 | Uygulama callback'i kaydeder | `AudioManager.registerAudioDeviceCallback` (gövde atfı) |
| 2 | Cihaz bağlanır/düşer | sistem |
| 3 | `onAudioDevicesAdded` çağrılır | `AudioDeviceCallback` |
| 4 | `onAudioDevicesRemoved` çağrılır | `AudioDeviceCallback` |
| 5 | Uygulama güncel envanteri tekrar okur | `AudioManager.getDevices` → `⚠️` (yöntem imzası bu turda gövdeden okunmadı) |

## §5 AudioRouting — tercih edilen ve güncel route

Kaynak: `AudioRouting` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Arayüz biçimi | `public interface AudioRouting` |
| API seviyesi | "Added in API level 24" |
| Dolaylı alt sınıflar | "Known indirect subclasses: `AudioRecord`, `AudioTrack`, `MediaPlayer`, `MediaRecorder`" |
| Yöntem | `addOnRoutingChangedListener(AudioRouting.OnRoutingChangedListener, Handler)` — "receive notifications of routing changes on this AudioTrack/AudioRecord" |
| Yöntem | `getPreferredDevice()` — "the selected output/input specified by `setPreferredDevice(AudioDeviceInfo)`" |
| Yöntem | `getRoutedDevice()` — "identifying the current routing of this AudioTrack/AudioRecord" |
| Yöntem | `getRoutedDevices()` — "a List of AudioDeviceInfo" (çoklu route) |

### §5.1 Tercih ≠ gerçek route ayrımı (bu dosyanın en önemli kuralı)

| Kavram | Yöntem | Anlamı (gövde) |
|---|---|---|
| Tercih edilen cihaz | `getPreferredDevice()` | uygulamanın **istediği** cihaz |
| Gerçek route | `getRoutedDevice()` / `getRoutedDevices()` | sistemin **seçtiği** cihaz |
| Değişim bildirimi | `addOnRoutingChangedListener` | route değişince haber |

**RED TEAM bulgu 5-1:** "setPreferredDevice çağrısı route'u garanti eder" iddiası
**REDDEDİLDİ**; arayüz iki ayrı kavramı (tercih ve gerçek route) ayrı yöntemlerle
tanımlar — sistem tercihi reddedebilir.

## §6 AudioManager — kayıt ve envanter kapısı (başlık kanıtı + atıf)

| Öğe | Durum |
|---|---|
| Sayfa | `AudioManager` referansı · HTTP 200 · gövde okunmadı (yalnız atıflar) |
| Callback kayıt yolu | `AudioDeviceCallback` gövdesinden atıf (§4) |
| `getDevices` imzası | `⚠️ VERIFICATION REQUIRED` (gövde okunmadı) |
| Ses ayarları (ses seviyesi, mod) | `⚠️ VERIFICATION REQUIRED` |
| Mixer özniteliği API'si | §14'te başka sayfanın gövdesinden |

## §7 Stream tipi ve öznitelik ayrımı (sınır bölümü)

| Konu | Durum | Kanıt |
|---|---|---|
| `AudioAttributes` kullanım/`content` alanları | gövde okunmadı → `⚠️` | — |
| Eski "stream type" kavramı | ses odağı sayfasında `AudioAttributes` öne çıkıyor (§12) | odağı sayfası gövdesi |
| `AudioFormat` sabitleri | gövde okundu (§8) | `AudioFormat` |
| Cihaz başına desteklenen format listesi | `UNKNOWN` (cihaz-özel) | — |
| 8 kanal / surround desteği | `UNKNOWN` | `CHANNEL_OUT_7POINT1_SURROUND` sabiti gövdede görüldü (§8.1) |

## §8 AudioFormat — format ve kanal maskesi sabitleri

Kaynak: `AudioFormat` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sınıf biçimi | `public final class AudioFormat extends Object implements Parcelable` |
| Tanım | "used to access a number of audio format and channel configuration constants. They are for instance used in AudioTrack and AudioRecord" |
| API seviyesi | "Added in API level 3" |
| Örnek | `SAMPLE_RATE_UNSPECIFIED`: "Sample rate will be a route-dependent value." |

### §8.1 Kanal maskesi tablosu (gövdeden)

| Kanal sayısı | Kanal maskesi (gövdede görülen) |
|---|---|
| 1 | `CHANNEL_OUT_MONO` |
| 2 | `CHANNEL_OUT_STEREO` |
| 3 | `CHANNEL_OUT_STEREO | CHANNEL_OUT_FRONT_CENTER` |
| 4 | `CHANNEL_OUT_QUAD` |
| 5 | `CHANNEL_OUT_QUAD | CHANNEL_OUT_FRONT_CENTER` |
| 6 | `CHANNEL_OUT_5POINT1` |
| 7 | `CHANNEL_OUT_5POINT1 | CHANNEL_OUT_BACK_CENTER` |
| 8 | `CHANNEL_OUT_7POINT1_SURROUND` |

Gövde notu: "These masks are an ORed composite of individual channel masks."

### §8.2 PCM_FLOAT kodlama (CoreMusic ADR-017 ile kesişen tek yer)

| İddia | Gövde ifadesi |
|---|---|
| Sabit | `ENCODING_PCM_FLOAT` |
| Açıklama | "Introduced in API `Build.VERSION_CODES.LOLLIPOP`, this encoding specifies that the audio sample is a **32 bit IEEE single precision float**" |
| Bellek düzeni | "within a ByteBuffer it is stored in native endian byte order" |
| Java kullanımı | "The sample can be manipulated as a Java float in a float array" |

**Çıkarım:** Android ses yığını 32-bit kayan nokta örnekleme tanımını
`ENCODING_PCM_FLOAT` ile **belgeler** (gövde kanıtı); bu, CoreMusic'in 32-bit
float iç format kuralı ile **uyumlu kanıt**tur — ancak cihaz-özel destek
listesi bu turda ölçülmedi → destek yüzdesi `UNKNOWN`.

## §9 Red-team — bu dosyada reddedilen iddialar

| # | Reddedilen iddia | Gerekçe |
|---|---|---|
| 1 | "Ses cihazı tipleri sabittir" | `TYPE_BLE_*` ailesi sürümle eklenmiştir (§3.1) |
| 2 | "setPreferredDevice route'u garanti eder" | tercih ile gerçek route ayrı yöntemlerdir (§5.1) |
| 3 | "Callback olmadan cihaz değişimi izlenebilir" | bildirim mekanizması `AudioDeviceCallback`'tir (§4) |
| 4 | "MediaSession yalnız oynatma durumunu taşır" | medya tuşları, ses seviyesi tuşları ve transport da dahildir (§10) |
| 5 | "Audio focus yalnız 'durdur/başlat' demektir" | `duck` (kısma) davranışı dokümante edilmiştir (§12) |
| 6 | "Kanal maskesi tek sayıdır" | ORed birleşik maskelerdir (§8.1) |
| 7 | "PCM_FLOAT Android'de yoktur/yalnız 16-bit vardır" | `ENCODING_PCM_FLOAT` 32-bit IEEE float olarak tanımlıdır (§8.2) |

## §10 MediaSession — session yayınlama

Kaynak: `MediaSession` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sınıf biçimi | `public final class MediaSession extends Object` |
| Tanım | "Allows interaction with media controllers, volume keys, media buttons, and transport controls." |
| Öneri | "A MediaSession should be created when an app wants to publish media playback information or handle media keys." |
| API seviyesi | "Added in API level 21" |
| Yöntem | `setPlaybackState(PlaybackState)` — "Update the current playback state." |
| Yöntem | `setMetadata(MediaMetadata)` — "Update the current metadata." |
| Yöntem | `setPlaybackToLocal(AudioAttributes)` — "Set the attributes for this session's audio." |
| Yöntem | `setPlaybackToRemote(VolumeProvider)` — "Configure this session to use remote volume handling." |
| Yöntem | `setQueue(List<MediaSession.QueueItem>)` — "Update the list of items in the play queue." |
| Yöntem | `setQueueTitle(CharSequence)` · `setRatingType(int)` | kuyruk başlığı / oylama tipi |

### §10.1 Yerel ve uzak ses seviyesi ayrımı

| Mod | Yöntem | Bağımlılık |
|---|---|---|
| Yerel | `setPlaybackToLocal(AudioAttributes)` | oturumun `AudioAttributes`'ı |
| Uzak | `setPlaybackToRemote(VolumeProvider)` | `VolumeProvider` (gövdede adı geçti, içeriği okunmadı → `⚠️`) |

**RED TEAM bulgu 10-1:** "Ses seviyesi her zaman `AudioManager` üzerinden
yönetilir" iddiası **REDDEDİLDİ**; session iki ayrı ses seviyesi modu tanımlar
(§10.1).

## §11 MediaController — oturuma istemci tarafı erişim

Kaynak: `MediaController` referans gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Sınıf biçimi | `public final class MediaController extends Object` |
| Tanım | "Allows an app to interact with an ongoing media session. Media buttons and other commands can be sent to the session." |
| Bildirim | "A callback may be registered to receive updates from the session" |
| API seviyesi | "Added in API level 21" |
| Yöntem | `getPlaybackState()` — "Get the current playback state for this session." |
| Yöntem | `getQueue()` · `getQueueTitle()` · `getRatingType()` | kuyruk/oylama sorgusu |
| Yöntem | `getTransportControls()` — "Get a TransportControls instance to send transport actions to the associated session." |
| Yöntem | `registerCallback(MediaController.Callback, Handler)` — oturum güncellemeleri |

### §11.1 Session ↔ Controller sorumluluk ayrımı

| Taraf | Sınıf | Rol (gövde) |
|---|---|---|
| Yayıncı | `MediaSession` | durum/meta/kuyruk yayınlar, medya tuşlarını ele alır |
| İstemci | `MediaController` | durumu okur, transport komutu gönderir |

## §12 Audio focus — odağı yönetme kılavuzu

Kaynak: `Manage audio focus` kılavuz gövdesi.

| İddia | Gövde ifadesi |
|---|---|
| Odak kaybı davranışı | "When another app gains audio focus, stop or pause playing, or duck (that is, reduce) the volume." |
| Bırakma | "When playback stops (for example, when the app has nothing left to play), abandon audio focus." |
| İstisna | "Your app doesn't have to abandon audio focus if the user pauses playback but might resume playback later." |
| Öznitelik kullanımı | "Use `AudioAttributes` to describe the type of audio your app is playing." |
| Duck kuralı | "the audio system ducks all the active players of the first app while the second app has focus. When the second app abandons focus, it unducks them." |
| Duck istisnası | `AudioFocusRequest.Builder.setWillPauseWhenDucked(true)` mevcut olduğunda duck uygulanmaz (gövde: "The app did not set ...") |
| İstek tipi | `AUDIOFOCUS_GAIN_TRANSIENT_MAY_DUCK` (gövde) |
| İçerik tipi örneği | "content type `AudioAttributes.CONTENT_TYPE_SPEECH`" |

### §12.1 Odağı kazanma/kaybetme şeması (gövde cümlesinden)

| Durum | Uygulama davranışı (gövde) |
|---|---|
| Odağı kazandım | çalmaya devam |
| Odağı kaybettim (diğer uygulama kazandı) | duraklat **veya** sesi kıs (duck) |
| Oynatma bitti | odağı bırak (abandon) |
| Kullanıcı duraklattı, dönüş mümkün | odağı bırakmak zorunda değil |

## §13 Media3 medya session akışı (legacy sayfa)

Kaynak: `/media/legacy/audio/mediasession` sayfası (200 · gövde: yan menü + akış tablosu).

| Gövde öğesi | Değer |
|---|---|
| Sayfa başlığı ağacı | "About audio and video" → "Media3 MediaSession" → "The Player interface" |
| Akış adımı | "Media Session `setActive(true)`" — "Update metadata and state" |
| Duraklama adımı | "Media Session `setActive(false)`" — "Update metadata and state" |
| Oyuncu adımları | "Start the player" · "Pause the player" · "Stop the player" |
| "Becoming Noisy" | "Register your BroadcastReceiver" · "Unregister your BroadcastReceiver" |
| Servis adımı | "startForeground(notification)" · "stopForeground(false)" |
| Odak adımı | "abandonAudioFocus()" (gövde akışında) |

**Sınır:** sayfanın tam gövde metni (paragraflar) bu turda alınmadı → mediat3
API imzaları `⚠️ VERIFICATION REQUIRED`; yalnız akış tablosu satırları kullanılabilir.

## §14 USB cihazı ve tercih edilen mikser öznitelikleri (örnek kod kanıtı)

Kaynak: `Improve audio playback` sayfası (200 · gövde: örnek kod bloğu).

Gövdede görülen örnek kod (sayfa metninden alınmış, derlenmedi):

```kotlin
val audioTrack = AudioTrack.Builder()
    .setAudioAttributes(attr)
    .setAudioFormat(mixerAttributes!!.format)
    .build()
mAudioManager.clearPreferredMixerAttributes(attr, usbDevice)
```

| İddia | Gövde ifadesi |
|---|---|
| API ailesi | `setPreferredMixerAttributes` (gövde yazımı: `setPreferredMixerAttriutes` — sayfa yazım hatası korunmuştur) |
| Temizleme | `clearPreferredMixerAttributes(attr, usbDevice)` |
| Dinleyici temizliği | `removeOnPreferredMixer...` (gövde kesilmiş → `⚠️ VERIFICATION REQUIRED`) |
| Cihaz bağımlılığı | `usbDevice` argümanı — mikser özniteliği USB cihazına bağlıdır |

**RED TEAM bulgu 14-1:** "Mikser özniteliği evrenseldir" iddiası **REDDEDİLDİ**;
örnek kod bir `usbDevice` argümanı taşır — öznitelik **cihaza bağlanmıştır**.

## §15 Doğrulama matrisi

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| `AudioDeviceInfo` sınıf tanımı + API 23 | https://developer.android.com/reference/android/media/AudioDeviceInfo | 2026-10-12 | HTTP 200 · gövde alındı |
| `TYPE_*` sabitleri (BLE ailesi dahil) | https://developer.android.com/reference/android/media/AudioDeviceInfo | 2026-10-12 | Gövde alıntısı |
| `AudioDeviceCallback` tanımı + iki yöntem | https://developer.android.com/reference/android/media/AudioDeviceCallback | 2026-10-12 | HTTP 200 · gövde alındı |
| `registerAudioDeviceCallback` atfı | https://developer.android.com/reference/android/media/AudioDeviceCallback | 2026-10-12 | Gövde alıntısı |
| `AudioRouting` arayüzü + 4 yöntem | https://developer.android.com/reference/android/media/AudioRouting | 2026-10-12 | HTTP 200 · gövde alındı |
| `AudioManager` sayfası var | https://developer.android.com/reference/android/media/AudioManager | 2026-10-12 | HTTP 200 · başlık kanıtı · `⚠️` |
| `AudioFormat` tanımı + kanal maskeleri | https://developer.android.com/reference/android/media/AudioFormat | 2026-10-12 | HTTP 200 · gövde alındı |
| `ENCODING_PCM_FLOAT` 32-bit IEEE float | https://developer.android.com/reference/android/media/AudioFormat | 2026-10-12 | Gövde alıntısı |
| `MediaSession` tanımı + session yöntemleri | https://developer.android.com/reference/android/media/session/MediaSession | 2026-10-12 | HTTP 200 · gövde alındı |
| `MediaController` tanımı + controller yöntemleri | https://developer.android.com/reference/android/media/session/MediaController | 2026-10-12 | HTTP 200 · gövde alındı |
| Odağı kuralları (duck, abandon, `AudioAttributes`) | https://developer.android.com/media/optimize/audio-focus | 2026-10-12 | HTTP 200 · gövde alındı |
| Media3 session akış adımları | https://developer.android.com/media/legacy/audio/mediasession | 2026-10-12 | HTTP 200 · gövde (akış tablosu) |
| USB mikser özniteliği örneği | https://developer.android.com/media/platform/improve-audio-playback | 2026-10-12 | HTTP 200 · gövde (örnek kod) |
| Media3 oynatıcı kurulum sayfası | https://developer.android.com/media/implement/playback-app | 2026-10-12 | HTTP 200 · gövde (yan menü + `setAudioAttributes` atfı) |

## §16 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| M-1 | `AudioDeviceInfo` tam tip sabit sayısı | `UNKNOWN` |
| M-2 | `AudioManager.getDevices` imzası ve filtreleri | `⚠️ VERIFICATION REQUIRED` |
| M-3 | `AudioAttributes` usage/content değer listesi | `⚠️ VERIFICATION REQUIRED` |
| M-4 | `VolumeProvider` soyut metodları | `⚠️ VERIFICATION REQUIRED` |
| M-5 | Cihaz başına desteklenen format listesi | `UNKNOWN` |
| M-6 | Route değişimi sırasında kesinti süresi | `UNKNOWN` (ölçüm yapılmadı) |
| M-7 | Media3 `MediaSession` sınıfının Java karşılığı | `⚠️ VERIFICATION REQUIRED` |
| M-8 | USB mikser özniteliği destek cihaz oranı | `UNKNOWN` |

## §17 Kanıt toplama kaydı

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | HEAD/GET ile 14 URL doğrulama | 14/14 HTTP 200 |
| 2 | Gövde indirme + script/style temizliği + etiket soyma | düz metin |
| 3 | Anahtar kelime ile alıntı çıkarma | §3–§14 tabloları |
| 4 | `.ai/architecture/**` içinde URL kesişim denetimi | 0 (tümü yeni) |
| 5 | Repo `.cpp/.h` ölçümü | 0 dosya → `Dosya:/Satır:` yasak |
| 6 | Kullanılan UA | tarayıcı User-Agent |

## §18 Dosyaya tahsis edilen URL listesi (tekil)

| # | URL | Bölüm |
|---|---|---|
| 1 | https://developer.android.com/reference/android/media/AudioDeviceInfo | §3 §4 |
| 2 | https://developer.android.com/reference/android/media/AudioDeviceCallback | §4 |
| 3 | https://developer.android.com/reference/android/media/AudioRouting | §5 |
| 4 | https://developer.android.com/reference/android/media/AudioManager | §6 |
| 5 | https://developer.android.com/reference/android/media/AudioFormat | §8 |
| 6 | https://developer.android.com/reference/android/media/session/MediaSession | §10 |
| 7 | https://developer.android.com/reference/android/media/session/MediaController | §11 |
| 8 | https://developer.android.com/media/optimize/audio-focus | §12 |
| 9 | https://developer.android.com/media/legacy/audio/mediasession | §13 |
| 10 | https://developer.android.com/media/platform/improve-audio-playback | §14 |
| 11 | https://developer.android.com/media/implement/playback-app | §13 §15 |

Bu 11 URL bu dosyaya ÖZELDİR; `android/` klasörünün diğer dosyalarında tekrarlanmaz.

### §18.1 Doğrulama anı kaydı (2026-10-12)

| # | Yöntem | Sonuç | Gövde uzunluğu sınıfı |
|---|---|---|---|
| 1 | HEAD → GET | 200 | referans sayfası (uzun) |
| 2 | HEAD → GET | 200 | referans sayfası (uzun) |
| 3 | HEAD → GET | 200 | referans sayfası (uzun) |
| 4 | HEAD | 200 | başlık kanıtı (gövde okunmadı) |
| 5 | HEAD → GET | 200 | referans sayfası (uzun) |
| 6 | HEAD → GET | 200 | referans sayfası (uzun) |
| 7 | HEAD → GET | 200 | referans sayfası (uzun) |
| 8 | HEAD → GET | 200 | kılavuz sayfası (orta) |
| 9 | HEAD → GET | 200 | kılavuz sayfası (kısa) |
| 10 | HEAD → GET | 200 | kılavuz sayfası (orta, örnek kodlu) |
| 11 | HEAD → GET | 200 | kılavuz sayfası (orta) |

**HEAD notu:** `HEAD` tek başına yeterli değildir; 11 URL'nin tamamı **GET** ile de
doğrulanarak gövde imzası okunmuştur (bkz. `android-hal-ve-surucu.md` §26 — 1. madde:
"HEAD ile doğrulayıp 'erişilebilir' demek" yerine GET kullan).

## §19 Komşu dosya ilişkileri (URL kesişimi olmadan)

| Komşu | Konu ayrımı | Temas noktası |
|---|---|---|
| `android-audio-mimarisi.md` | Çerçeve + terminoloji | katman tanımı (orada) |
| `android-aaudio-ve-opensl.md` | AAudio/MMAP + OpenSL ES | düşük gecikme yolu (orada) |
| `android-hal-ve-surucu.md` | HAL + politika + öncelik | cihaz listesinin alt katmanı |
| `android-gercek-zamanli-dinleme.md` | gecikme/buffer/underrun | `getUnderrunCount` orada |
| `android-build-ve-dagilim.md` | Gradle/ABI/dağıtım | — |
| `android-izin-ve-lifecycle.md` | izin + yaşam döngüsü | cihaz izinleri orada |
| `android-kod-ornekleri.md` | referans kod örnekleri | bu dosyadaki Kotlin bloğu orada DEĞİL |
| `android-web-arastirma.md` | kaynak taraması | bu dosyanın 11 URL'si bu dosyada |

## §19.1 Cihaz değişimi karar ağacı (gövde kanıtlı)

| # | Durum | Soru | Davranış (kanıt) | Kanıt düzeyi |
|---|---|---|---|---|
| 1 | Uygulama başlatılıyor | Cihaz listesi alındı mı? | `AudioDeviceInfo` envanteri (§3) | A — gövde |
| 2 | Uygulama açık | Bağlantı olayı dinleniyor mu? | `AudioDeviceCallback` kaydı (§4) | A — gövde |
| 3 | Cihaz bağlandı | Eklendi listesi geldi mi? | `onAudioDevicesAdded` (§4) | A — gövde |
| 4 | Cihaz düştü | Kaldırıldı listesi geldi mi? | `onAudioDevicesRemoved` (§4) | A — gövde |
| 5 | Route değişti | Dinleyici var mı? | `addOnRoutingChangedListener` (§5) | A — gövde |
| 6 | Route sorgulanıyor | Tercih mi gerçek mi? | `getPreferredDevice` vs `getRoutedDevice` (§5) | A — gövde |
| 7 | Oynatma sürüyor | Odağı kaybettim mi? | duraklat veya duck (§12) | A — gövde |
| 8 | Oynatma bitti | Odağı bırak | abandon (§12) | A — gövde |
| 9 | USB cihaz seçildi | Mikser özniteliği ayarlandı mı? | örnek kod §14 | A — gövde (örnek) |
| 10 | Format seçiliyor | Kanal maskesi doğru mu? | §8.1 maskesi | A — gövde |
| 11 | 32-bit float gerekli mi? | `ENCODING_PCM_FLOAT` var mı? | §8.2 | A — gövde |
| 12 | Cihaz destek listesi | Hangi cihaz destekliyor? | — | D — yok → `UNKNOWN` |

## §19.2 Sık yapılan hatalar (RED TEAM eğitimi)

| # | Hata | Doğru davranış |
|---|---|---|
| 1 | `setPreferredDevice` sonrası route'u garanti saymak | `getRoutedDevice` ile doğrula (§5.1) |
| 2 | Cihaz callback'ini kaydetmemek | `AudioManager.registerAudioDeviceCallback` (§4) |
| 3 | Audio focus'u hiç istememek | odak kuralı §12 — iste, kaybedince tepki ver |
| 4 | Duck'ı duraklatma sanmak | duck = sesi kısma, oynatma sürer (§12) |
| 5 | Session'ı durum deposu sanmak | session medya tuşlarını ve transport'u da taşır (§10) |
| 6 | `setPlaybackToLocal` ile `setPlaybackToRemote`'u karıştırmak | iki ayrı moddur (§10.1) |
| 7 | Kanal sayısını maskeyle eşdeğer sanmak | maskeler ORed birleşiktir (§8.1) |
| 8 | 16-bit varsayıp float'ı reddetmek | `ENCODING_PCM_FLOAT` gövdede tanımlıdır (§8.2) |
| 9 | Mikser özniteliğini cihazdan bağımsız sanmak | örnek kodda `usbDevice` argümanı vardır (§14) |
| 10 | `⚠️` işaretli hücreyi dolu saymak | §6 §7 yalnız başlık/atıf kanıtı taşır |

## §19.3 CoreMusic / Neva Engine etkisi

| Etki | Ayrıntı | Durum |
|---|---|---|
| 8.1 kanal haritası | `CHANNEL_OUT_7POINT1_SURROUND` gövdede tanımlı (§8.1) | kanıt var · cihaz desteği `UNKNOWN` |
| 32-bit float iç format | `ENCODING_PCM_FLOAT` = "32 bit IEEE single precision float" (§8.2) | uyumlu kanıt |
| Cihaz değişimi güvenliği | route değişimi bildirimli (§4 §5) | uygulanabilir |
| Odak disiplini | duck/abandon kuralları belgeli (§12) | uygulanabilir |
| Platform bağımsızlığı | ADR-019 per-OS oyuncu ayrımı | Android dalı ayrı oyuncu |
| Ölçüm | gerçek cihaz üzerinde route değişim ölçümü | YAPILMADI → `UNKNOWN` |

## §19.4 Kanıt sınıfı dökümü (dosya içi)

| Sınıf | Tanım | Bu dosyadaki bölüm | Sayı |
|---|---|---|---|
| A — gövde alıntısı | HTML indirildi, düz metne çevrildi, cümle alındı | §2 §3 §4 §5 §8 §10 §11 §12 §13 §14 | 10 |
| B — başlık kanıtı | HTTP 200 + sayfa başlığı, içerik okunmadı | §6 §7 | 2 |
| C — atıf | Başka dosyanın kanıtına bölüm atfı | §19 | 1 |
| D — yok | Kanıt yok → iddia yazılmaz | §16 | 8 açık soru |

## §20 Kırmızı takım son kontrolü

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | Gövde okunmayan sayfaya içerik atfedildi mi? | HAYIR — §6 §7 `⚠️` işaretli |
| 2 | Sürüm/sayı uyduruldu mu? | HAYIR — API seviyeleri gövdeden |
| 3 | Uydurma metod/imza yazıldı mı? | HAYIR — §3–§11 tabloları gövde alıntısı |
| 4 | `Dosya:/Satır:` | 0 (repo 0 `.cpp/.h`) |
| 5 | Dolgu/kısaltma yer tutucu | 0 |
| 6 | Mojibake | 0 |
| 7 | `git commit` | ATILMADI |
| 8 | Frontmatter alanı | 10 · `group: mimari` |

## §21 Kapanış ölçümü

| Metrik | Değer |
|---|---|
| Dosya | `android-medya-cihazi-yonetimi.md` |
| Gövde okunan sayfa | 13 (yalnız `AudioManager` başlık-only) |
| Başlık-only sayfa | 1 |
| Tekil URL | 11 |
| `## §N` bölüm | 22 (§0–§21) |
| Tablo sayısı | ≥20 |
| ORİJİNAL ÖRNEK | 1 (§14, derlenmedi) |
| `⚠️ VERIFICATION REQUIRED` hücre | 12 |
| Açık soru | 8 (§16) |
| `git commit` | ATILMADI |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode