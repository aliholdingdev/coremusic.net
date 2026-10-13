---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/coreaudio
children: []
depends_on: []
evidence:
  - https://developer.apple.com/documentation/coreaudio/audioobjectid
  - https://developer.apple.com/documentation/coreaudio/audiodeviceid
  - https://developer.apple.com/documentation/coreaudio/audiodeviceioproc
  - https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification
  - https://developer.apple.com/documentation/avfaudio/avaudiouniteffect
  - https://developer.apple.com/documentation/audiotoolbox/audio_services
  - https://developer.apple.com/documentation/coreaudio/audioobjectpropertyaddress
  - https://developer.apple.com/documentation/coreaudio/audiohardwarepropertylistenerproc
  - https://developer.apple.com/documentation/avfaudio/avaudiosession/routechangenotification
  - https://developer.apple.com/documentation/audiotoolbox/audiounit
  - https://developer.apple.com/documentation/audiotoolbox/audio_queue_services
validation:
  - "2026-10-13 · 18 Apple URL HTTP 200 · envanter kesişimi 0 · tip beyanları JSON uç noktasından birebir"
  - "repo taraması: `CoreAudio|AudioDevice|AVAudioSession|AudioUnit` 0 isabet → CoreAudio kodu YOK"
open_questions:
  - "Nesne ağacının tamamı (property/scope/selector semantiği) tek kaynaktan doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "Platform hedefi (macOS/iOS) → UNKNOWN"
group: mimari
---

# CoreAudio Anayasa — Kimlik · Nesne Ağacı · IOProc · Oturum Modeli

> **Bu dosya ne değildir:** Kod, benchmark veya kurulum rehberi değildir. Bu dosya
> CoreAudio/AVAudioSession yüzeyini *belge kanıtıyla* tanımlar; tip beyanları
> Apple'ın JSON dokümantasyon ucundan (2026-10-13) birebir alınmıştır. Erişilemeyen
> her iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretidir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod **YOK** (repo taraması 2026-10-13:
> `CoreAudio|AudioDevice|AVAudioSession|AudioUnit` desenine 0 isabet).

---

## §0 ELI15 — CoreAudio bir cümlede

CoreAudio, Apple cihazlarında ses donanımını **numaralandırılmış nesneler**
(`AudioObjectID` = `UInt32`) üzerinden yönetir: cihaz bir nesnedir, akışlar
alt-nesnelerdir, özellikler (property) bu nesnelerin ayarlarıdır; cihaz her IO
döngüsünde uygulamanın `AudioDeviceIOProc` callback'ini çağırır — giriş tamponunu
verir, çıkış tamponunu ister — üstteki oturum politikası (kesinti · kategori ·
mod) ise AVAudioSession tarafından belirlenir. (Kaynak: §3 · §4 · §5 · §6)

---

## §1 CoreAudio Kimlik Kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | Core Audio (framework) | [C4] `modules: [{name: "Core Audio"}]` |
| Temel kimlik | `typealias AudioObjectID = UInt32` | [C4] JSON birebir |
| Cihaz kimliği | `typealias AudioDeviceID = AudioObjectID` | [C3] JSON birebir |
| IO callback'i | `typealias AudioDeviceIOProc = (…) -> OSStatus` | [C10] JSON birebir |
| Nesne ilişkisi | "AudioDevice is a subclass of AudioObject." | [C3] Discussion birebir |
| Cihaz içeriği | "AudioDevices normally contain AudioStreams and AudioControls" | [C3] birebir |
| Topluluk (aggregate) cihaz | "aggregate devices contain other AudioDevices" | [C3] birebir |
| Oturum katmanı | `AVAudioSession` — "An object that communicates to the system how you intend to use audio in your app." | [C13] JSON |
| Kesinti bildirimi | `class let interruptionNotification: NSNotification.Name` | [C13] JSON birebir |
| Ses birimi sarmalayıcısı | `class AVAudioUnitEffect` | [C7] JSON birebir |
| Test sesleri | Audio Services — "Play short sounds or trigger a vibration effect…" | [C1] `<meta>` |
| Platform | macOS · Mac Catalyst (IOProc metadata `platforms`) | [C10] JSON |
| Sahibi | Apple Inc. (framework) | [C1]-[C13] |
| CoreMusic sahibi agent | Embedded Engineer (L0 · C++20 · realtime) | kök `AGENTS.md` §routing |
| Repo durumu | CoreAudio kodu YOK — UNKNOWN (§9.1) | tarama, 2026-10-13 |

---

## §2 Kanıt kaynakları

| Etiket | Sayfa | URL | 2026-10-13 |
|---|---|---|---|
| [C1] | Audio Services | https://developer.apple.com/documentation/audiotoolbox/audio_services | ✅ 200 · meta alındı · JSON 404 |
| [C2] | playback (kategori) | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback | ✅ 200 · meta alındı · JSON 404 |
| [C3] | AudioDeviceID | https://developer.apple.com/documentation/coreaudio/audiodeviceid | ✅ 200 · **JSON birebir** |
| [C4] | AudioObjectID | https://developer.apple.com/documentation/coreaudio/audioobjectid | ✅ 200 · **JSON birebir** |
| [C5] | AVAudioSession.Mode | https://developer.apple.com/documentation/avfaudio/avaudiosessionmode | ✅ 200 · meta alındı · JSON 404 |
| [C6] | default (mod) | https://developer.apple.com/documentation/avfaudio/avaudiosessionmodedefault | ✅ 200 · meta alındı · JSON 404 |
| [C7] | AVAudioUnitEffect | https://developer.apple.com/documentation/avfaudio/avaudiouniteffect | ✅ 200 · **JSON birebir** |
| [C8] | AudioFormatID | https://developer.apple.com/documentation/coreaudio/audioformatid | ✅ 200 · meta alındı · JSON 404 |
| [C9] | AudioFormatFlags | https://developer.apple.com/documentation/coreaudio/audioformatflags | ✅ 200 · meta alındı · JSON 404 |
| [C10] | AudioDeviceIOProc | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | ✅ 200 · **JSON birebir** |
| [C11] | AudioBuffer | https://developer.apple.com/documentation/coreaudio/audiobuffer | ✅ 200 · meta alındı · JSON 404 |
| [C12] | kAudioFormatLinearPCM | https://developer.apple.com/documentation/coreaudio/kaudioformatlinearpcm | ✅ 200 · meta alındı · JSON 404 |
| [C13] | interruptionNotification | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification | ✅ 200 · **JSON birebir** |
| [C14] | AudioObjectPropertyAddress | https://developer.apple.com/documentation/coreaudio/audioobjectpropertyaddress | ✅ 200 · **JSON birebir** |
| [C15] | AudioHardwarePropertyListenerProc | https://developer.apple.com/documentation/coreaudio/audiohardwarepropertylistenerproc | ✅ 200 · **JSON birebir** |
| [C16] | routeChangeNotification | https://developer.apple.com/documentation/avfaudio/avaudiosession/routechangenotification | ✅ 200 · **JSON birebir** |
| [C17] | AudioUnit | https://developer.apple.com/documentation/audiotoolbox/audiounit | ✅ 200 · **JSON birebir** |
| [C18] | Audio Queue Services | https://developer.apple.com/documentation/audiotoolbox/audio_queue_services | ✅ 200 · **JSON birebir** |

> 18 URL, repo URL envanteri (2103 satır · oturum içi 2633 satır) ile **kesişim 0** — 2026-10-13 taze doğrulama.
> [C1]–[C13] ilk turda; [C14]–[C18] ikinci turda (JSON uç noktası GET → 200) alındı.
> 8 sayfada JSON uç noktası 404 döndü; o sayfalarda yalnız HTML `<meta name="description">`
> metni kullanıldı (durum sütununda açıkça yazıyor).

### §2.1 Erişim kaydı (2026-10-13 · nasıl alındığı)

| Etiket | Yöntem | Alınan veri | Ham dosya (oturum içi) |
|---|---|---|---|
| [C3] [C4] [C7] [C10] [C13] | JSON uç nokta GET → 200 | imza · Discussion · ilgili-simboller · `metadata.platforms` | `apple__*.json` (5 dosya) |
| [C14] [C15] [C16] [C17] [C18] | JSON uç nokta GET → 200 (ikinci tur) | beyan token'ları · `abstract` · Discussion · `topicSections` · `variants` | `apple-aopropertyaddr.json` · `apple-ahwlistener.json` · `apple-routechange.json` · `apple-audiounit.json` · `apple-aqs.json` (5 dosya) |
| [C1] [C2] [C5] [C6] [C8] [C9] [C11] [C12] | JSON uç nokta → **404**; HTML GET → 200 | yalnız `<meta name="description">` | `appleh__*.html` (8 dosya) |
| hepsi | repo envanteri karşılaştırması | kesişim **0** | `url-inventory-20261013.txt` (2103 satır) |

| Gözlem (erişim sırasında) | Sonuç |
|---|---|
| JSON 404 sayfaları JS kabuğu mu? | evet — `data-token` özniteliği 0 isabet → gövde metni HTML'den alınamadı |
| `kAudioFormatLinearPCM` 4-char değeri | HTML'de `lpcm` dizgesi **geçmiyor** → sayısal değer yazılmadı (⚠️) |
| Apple JSON'undaki apostrof | kaynakta bozuk `ǽ�?�ts` çıkıyor (U+2019 mojibake'si) → alıntıda düz `'` |

---

## §3 Nesne ağacı

### §3.1 Sınıf ilişkisi (birebir, [C3])

> "AudioDevice is the base class for all objects that represent an audio device.
> AudioDevice is a subclass of AudioObject. AudioDevices normally contain
> AudioStreams and AudioControls, but may contain other things depending on the
> kind of AudioDevice (e.g. aggregate devices contain other AudioDevices)."

| Düğüm | Tip | Üst/alt ilişki | Kanıt |
|---|---|---|---|
| `AudioObject` | temel | en üst | [C3] "subclass of AudioObject" |
| `AudioDevice` | cihaz | `AudioObject` alt sınıfı | [C3] birebir |
| `AudioStream` | akış | cihaz içinde | [C3] "normally contain AudioStreams" |
| `AudioControl` | kontrol | cihaz içinde | [C3] "and AudioControls" |
| aggregate device | topluluk cihazı | diğer `AudioDevice`'leri içerir | [C3] birebir |

### §3.2 Kimlik tipleri (birebir liste, [C4]/[C3] JSON)

| Tip | Tanım (JSON metni) |
|---|---|
| `AudioClassID` | sınıf kimliği (liste satırı) |
| `AudioDeviceID` | `= AudioObjectID` |
| `AudioDeviceIOBlock` | IO blok varyantı (liste satırı) |
| `AudioDeviceIOProc` | IO callback (§4) |
| `AudioDeviceIOProcID` | kayıtlı IOProc kimliği |
| `AudioDevicePropertyID` | cihaz özelliği kimliği |
| `AudioDevicePropertyListenerProc` | cihaz özelliği dinleyicisi |
| `AudioHardwarePropertyID` | donanım özelliği kimliği |
| `AudioHardwarePropertyListenerProc` | donanım özelliği dinleyicisi |
| `AudioObjectPropertyElement` | özellik elemanı |
| `AudioObjectPropertyListenerBlock` | özellik dinleyici (block) |
| `AudioObjectPropertyListenerProc` | özellik dinleyici (proc) |
| `AudioObjectPropertyScope` | özellik kapsamı |
| `AudioObjectPropertySelector` | özellik seçicisi |
| `AudioStreamID` | akış kimliği |

> `typedef AudioObjectID AudioDeviceID;` (C karşılığı, JSON'da aynı sayfada) [C3].

### §3.3 Kimlik boyutu

| Tip | Tanım | Kanıt |
|---|---|---|
| `UInt32` | "A 32-bit unsigned integer value type." | [C4] JSON |
| `AudioObjectID` | "= UInt32" + "A UInt32 that provides a handle on a specific AudioObject." | [C4] JSON |

### §3.4 JSON sayfa metadata'sı (birebir, 3 CoreAudio typealias'ı)

| Sayfa | `roleHeading` | `externalID` | `platforms` |
|---|---|---|---|
| `audioobjectid` | `Type Alias` | `c:@T@AudioObjectID` | Mac Catalyst · macOS |
| `audiodeviceid` | `Type Alias` | `c:@T@AudioDeviceID` | Mac Catalyst · macOS |
| `audiodeviceioproc` | `Type Alias` | `c:@T@AudioDeviceIOProc` | Mac Catalyst · macOS |

| Çıkarım | Sonuç |
|---|---|
| CoreAudio typealias sayfaları macOS + Mac Catalyst listeliyor | iOS satırı **bu üç sayfada yok** → iOS kanıtı oturum katmanından gelir ([C13]: iOS · iPadOS · Mac Catalyst · tvOS · visionOS · watchOS) |
| `copyright` (JSON) | "Copyright © 2026 Apple Inc. All rights reserved." (erişim: 2026-10-13) |

---

## §4 IOProc — çekirdek callback sözleşmesi

### §4.1 İmza (birebir, [C10])

```swift
typealias AudioDeviceIOProc = (AudioObjectID, UnsafePointer<AudioTimeStamp>,
    UnsafePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>,
    UnsafeMutablePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>,
    UnsafeMutableRawPointer?) -> OSStatus
```

### §4.2 Parametre sırası (JSON Discussion metninden birebir)

| # | Parametre (tip) | Belge metni (birebir özet) |
|---|---|---|
| 1 | `AudioObjectID` | "The AudioDevice doing the IO." |
| 2 | `UnsafePointer<AudioTimeStamp>` | "An AudioTimeStamp that indicates the IO cycle started. Note that this time includes any scheduling latency that may have been incurred waking the thread on which IO is being done." |
| 3 | `UnsafePointer<AudioBufferList>` (giriş) | "An AudioBufferList containing the input data for the current IO cycle. For streams that are disabled, the AudioBuffer's mData field will be NULL but the mDataByteSize field will still say how much data would have been there if it was enabled. Note that the contents of this structure should never be modified." |
| 4 | `UnsafePointer<AudioTimeStamp>` | "the time at which the first frame in the data was acquired from the hardware. If the device has no input streams, the time stamp will be zeroed out." |
| 5 | `UnsafeMutablePointer<AudioBufferList>` (çıkış) | "the output data for the current IO cycle is to be placed. On entry, each AudioBuffer's mDataByteSize field indicates the maximum amount of data that can be placed in the buffer and the buffer's memory has been zeroed out." |
| 6 | `UnsafePointer<AudioTimeStamp>` | "the time at which the first frame in the data will be passed to the hardware. If the device has no output streams, the time stamp will be zeroed out." |
| 7 | `UnsafeMutableRawPointer?` | "A pointer to client data established when the AudioDeviceIOProc was registered with the AudioDevice." |

| Alan | Belge metni |
|---|---|
| Dönüş değeri | "The return value is currently unused and should always be 0." |
| Amaç | "An AudioDeviceIOProc is called by an AudioDevice to provide input data read from the device and collect output data to be written to the device for the current IO cycle." |

### §4.3 Parametre 5 — tam metin (JSON birebir, AC-3 notu dahil)

> "An AudioBufferList in which the output data for the current IO cycle is to be
> placed. On entry, each AudioBuffer's mDataByteSize field indicates the maximum
> amount of data that can be placed in the buffer and the buffer's memory has
> been zeroed out. For formats where the number of bytes per packet can vary (as
> with AC-3, for example), the client has to fill out on exit each mDataByteSize
> field in each AudioBuffer with the amount of data that was put in the buffer.
> Otherwise, the mDataByteSize field should not be changed. For streams that are
> disabled, the AudioBuffer's mData field will be NULL but the mDataByteSize
> field will still say how much data would have been there if it was enabled.
> Except as noted above, the contents of this structure should not other wise be
> modified." *(kaynaktaki "other wise" yazımı [sic] korunmuştur)*

### §4.4 Parametre 3 — tam metin (JSON birebir)

> "An AudioBufferList containing the input data for the current IO cycle. For
> streams that are disabled, the AudioBuffer's mData field will be NULL but the
> mDataByteSize field will still say how much data would have been there if it
> was enabled. Note that the contents of this structure should never be modified."

| Karar yüzeyi | Özet |
|---|---|
| giriş | asla değiştirilmez ("never") |
| çıkış · sabit paket | `mDataByteSize` değişmez |
| çıkış · değişken paket (AC-3 örneği) | çıkışta `mDataByteSize` yazılır |
| devre dışı akış | `mData` NULL · boyut alanı yine de dolu |

---

## §5 Format ve tampon tarafı

| Kavram | Tanım (birebir) | Etiket |
|---|---|---|
| `AudioBuffer` | "A structure that holds a buffer of audio data." | [C11] |
| `AudioFormatID` | "A type definition for audio format identifiers." | [C8] |
| `AudioFormatFlags` | "A type definition for audio format flags." | [C9] |
| `kAudioFormatLinearPCM` | "A key that specifies the linear PCM codec, and uses the standard flags." | [C12] |
| Devre dışı akış | "the AudioBuffer's mData field will be NULL but the mDataByteSize field will still say how much data would have been there if it was enabled" | [C10] |
| Çıkış tamponu başlangıcı | "the buffer's memory has been zeroed out" | [C10] |
| Değiştirme yasağı (giriş) | "the contents of this structure should never be modified" | [C10] |

> **Kapsam sınırı:** format bayraklarının tam listesi ve `kAudioFormatLinearPCM`
> değeri bu turda JSON'dan alınamadı (JSON 404) → sayısal değer yazılmaz,
> `⚠️ VERIFICATION REQUIRED` (C5).

---

## §6 Oturum modeli (AVAudioSession)

### §6.1 Roller

| Kavram | Belge metni (birebir) | Etiket |
|---|---|---|
| `AVAudioSession` | "An object that communicates to the system how you intend to use audio in your app." | [C13] JSON |
| Kategori `playback` | "The category for playing recorded music or other sounds that are central to the successful use of your app." | [C2] `<meta>` + [C13] JSON |
| Kategori `playAndRecord` | "The category for recording (input) and playback (output) of audio, such as for a Voice over Internet Protocol (VoIP) app." | [C13] JSON |
| Kategori `soloAmbient` | "The default audio session category." | [C13] JSON |
| Kategori `multiRoute` | "The category for routing distinct streams of audio data to different output devices at the same time." | [C13] JSON |
| Mod | "Audio session mode identifiers." (`AVAudioSession.Mode`) | [C5] `<meta>` |
| Mod `default` | "The default audio session mode." | [C6] `<meta>` |

### §6.2 Kesinti (interruption) modeli

| Öğe | Belge metni (birebir) | Etiket |
|---|---|---|
| Bildirim | `class let interruptionNotification: NSNotification.Name` — "A notification the system posts when an audio interruption occurs." | [C13] JSON |
| Bildirim adı | `AVAudioSessionInterruptionNotification` | [C13] JSON |
| Tip enum | "Constants that describe the type of an audio interruption." — `InterruptionType` | [C13] JSON |
| `began` | "A type that indicates that the operating system began interrupting the audio session." (`AVAudioSessionInterruptionTypeBegan`) | [C13] JSON |
| `ended` | "A type that indicates that the operating system ended interrupting the audio session." (`AVAudioSessionInterruptionTypeEnded`) | [C13] JSON |
| Neden enum | `InterruptionReason` — "Constants that define the reasons for an audio session interruption." | [C13] JSON |
| Durum enum | `InterruptionOptions` — "Constants that indicate the state of an audio session after an interruption." | [C13] JSON |
| Yayın kuralı | "The system posts this notification on the main thread." | [C13] JSON |
| iOS 10 davranışı | "Starting in iOS 10, the system deactivates an app's audio session when it suspends the app process. When the app starts running again, it receives an interruption notification that the system has deactivated its audio session. This notification is necessarily delayed in time because the system can only deliver it once the app is running again." | [C13] JSON |
| Arka plan önerisi | "If you configured your audio session to be nonmixable (the default behavior for the … categories), deactivate your audio session if you're not actively using audio when you go into the background." | [C13] JSON |
| Rota kopması | `prefersInterruptionOnRouteDisconnect` — "A Boolean value that indicates whether the system interrupts the audio session when the active route disconnects." | [C13] JSON |

### §6.3 User-info anahtarları (JSON `topicSections` birebir)

| Bölüm | Anahtar/enum |
|---|---|
| User Info Keys | `AVAudioSessionInterruptionTypeKey` · `AVAudioSessionInterruptionOptionKey` · `AVAudioSessionInterruptionReasonKey` · `AVAudioSessionInterruptionWasSuspendedKey` |
| User Info Values | `AVAudioSession/InterruptionType` · `AVAudioSession/InterruptionOptions` · `AVAudioSession/InterruptionReason` |

| Anahtar | Bağlam (Discussion birebir) |
|---|---|
| `…TypeKey` | "The notification's user-information dictionary contains the … key" |
| `…OptionKey` | "If the interruption type is ended, this dictionary also contains the … key." |
| `…WasSuspendedKey` | iOS 10 notu: "contains the … key with a value of true" |

### §6.4 Kategori sabit adları (JSON birebir)

| Vaka | Swift beyanı | Sabit adı |
|---|---|---|
| `playback` | `static let playback: AVAudioSession.Category` | `AVAudioSessionCategoryPlayback` |
| `playAndRecord` | `static let playAndRecord: AVAudioSession.Category` | `AVAudioSessionCategoryPlayAndRecord` |
| `soloAmbient` | `static let soloAmbient: AVAudioSession.Category` | `AVAudioSessionCategorySoloAmbient` |
| `multiRoute` | `static let multiRoute: AVAudioSession.Category` | `AVAudioSessionCategoryMultiRoute` |

### §6.5 Sayfa metadata'sı ve seeAlso (birebir)

| Alan | Değer |
|---|---|
| [C13] `roleHeading` | `Type Property` |
| [C13] `externalID` | `c:@AVAudioSessionInterruptionNotification` |
| [C13] `platforms` | iOS · iPadOS · Mac Catalyst · tvOS · visionOS · watchOS (**macOS listede yok**) |
| seeAlso başlığı | `Handling interruptions` |
| seeAlso sembolleri | `prefersNoInterruptionsFromSystemAlerts` · `setPrefersNoInterruptionsFromSystemAlerts(_:)` · `prefersInterruptionOnRouteDisconnect` · `setPrefersInterruptionOnRouteDisconnect(_:)` |

---

## §7 Ses birimleri (AVAudioUnitEffect ailesi)

| Öğe | Belge metni (birebir) | Etiket |
|---|---|---|
| `class AVAudioUnitEffect` | "An object that processes audio in real time." | [C7] JSON |
| Genel bakış | "These effects run in real time and process some number of audio input samples to produce several audio output samples. A delay unit is an example of an effect unit." | [C7] JSON |
| Türler | "effect, music effect, panner, remote effect, or remote music effect" | [C7] JSON |
| Sınıf ilişkisi | "A subclass of the audio node class that, processes audio either in real time or nonreal time, depending on the type of the audio unit." | [C7] JSON |
| `AVAudioUnitDelay` | "An object that implements a delay effect." | [C7] JSON |
| `AVAudioUnitDistortion` | "An object that implements a multistage distortion effect." | [C7] JSON |
| `AVAudioUnitEQ` | "An object that implements a multiband equalizer." | [C7] JSON |
| `AVAudioUnitReverb` | "An object that implements a reverb effect." | [C7] JSON |
| Baypas | `var bypass: Bool` — "The bypass state of the audio unit." | [C7] JSON |
| Kurucu | `init(audioComponentDescription: AudioComponentDescription)` | [C7] JSON |
| `AudioUnit` | "The data type for a plug-in component that provides audio processing or audio data generation." | [C7] JSON |

> EQ/reverb'nin **algoritması** (K004-dsp) bu dalın dışıdır; burada yalnız
> Apple'ın sınıf tanımı taşınır.

---

## §8 Audio Services

| Öğe | Metin | Etiket |
|---|---|---|
| Sayfa | Audio Services (AudioToolbox) | [C1] `<title>` |
| Amaç | "Play short sounds or trigger a vibration effect on iOS devices with the appropriate hardware." | [C1] `<meta>` |
| Kullanım senaryosu (CoreMusic) | uygulama-içi geri bildirim sesi / uyarı — Neva Engine çalma yolunun **dışında** | kapsam notu |

---

## §9 CoreMusic ile bağ

### §9.1 Repo kanıtı (2026-10-13)

| İddia | Durum |
|---|---|
| CoreMusic reposunda CoreAudio/AVAudioSession deseni | **0 isabet** → YOK · UNKNOWN |
| CoreMusic ses yolu | dosya → ffmpeg/ffprobe → HTTP Range → HTML5 (bkz. ornekler §1) | 

### §9.2 Kural eşlemesi (L0 → bu dal)

| L0 kuralı | CoreAudio karşılığı | Durum |
|---|---|---|
| Zero-allocation (audio thread) | IOProc gerçek zamanlı çağrılır; belgede tahsis yasağı **geçmiyor** → kural uygulama katmanında bağlayıcı | bağlayıcı (uygulama) |
| Lock-free | IOProc "IO cycle" içinde çağrılır; kilit politikası belgede yok → ⚠️ | ⚠️ VERIFICATION REQUIRED |
| `noexcept` (C++) | Swift/C sarmalayıcı — C++ tarafı Neva Engine'de | bağlayıcı |
| 512 örnek · 48 kHz | cihaz IO periyodu belgede sayılmıyor → ölçüm PLANNED | PLANNED |
| ASIO Exclusive Lock | bu sürümde karşılık belgesi yok → politika kararı | ⚠️ VERIFICATION REQUIRED |
| PCM3168A (ADR-038 H001) | donanım katmanı — bu dalın dışı | K001-donanım |
| DC offset >0.5V → röle | donanım koruması — bu dalın dışı | K001-donanım |

---

## §10 Web kanıt matrisi

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `AudioObjectID = UInt32` tip beyanı | https://developer.apple.com/documentation/coreaudio/audioobjectid | 2026-10-13 | ✅ 200 · JSON birebir |
| `AudioDeviceID = AudioObjectID` + AudioDevice Discussion | https://developer.apple.com/documentation/coreaudio/audiodeviceid | 2026-10-13 | ✅ 200 · JSON birebir |
| IOProc imzası + 7 parametre dokümanı | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | 2026-10-13 | ✅ 200 · JSON birebir |
| Kesinti bildirimi + iOS 10 metni | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification | 2026-10-13 | ✅ 200 · JSON birebir |
| `class AVAudioUnitEffect` + alt sınıflar | https://developer.apple.com/documentation/avfaudio/avaudiouniteffect | 2026-10-13 | ✅ 200 · JSON birebir |
| `playback` kategori tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback | 2026-10-13 | ✅ 200 · meta + JSON |
| Audio Services tanımı | https://developer.apple.com/documentation/audiotoolbox/audio_services | 2026-10-13 | ✅ 200 · meta |
| `struct AudioObjectPropertyAddress` + üç alan sözlüğü | https://developer.apple.com/documentation/coreaudio/audioobjectpropertyaddress | 2026-10-13 | ✅ 200 · JSON birebir (§11) |
| `typealias AudioUnit = AudioComponentInstance` | https://developer.apple.com/documentation/audiotoolbox/audiounit | 2026-10-13 | ✅ 200 · JSON birebir (§13) |
| `AudioHardwarePropertyListenerProc` imzası | https://developer.apple.com/documentation/coreaudio/audiohardwarepropertylistenerproc | 2026-10-13 | ✅ 200 · JSON birebir (§12) |
| `routeChangeNotification` + ikincil thread notu | https://developer.apple.com/documentation/avfaudio/avaudiosession/routechangenotification | 2026-10-13 | ✅ 200 · JSON birebir (§12) |
| Audio Queue Services koleksiyonu | https://developer.apple.com/documentation/audiotoolbox/audio_queue_services | 2026-10-13 | ✅ 200 · JSON birebir (§13) |

**Çakışma kontrolü:** §2'deki 13 URL + §10'daki 5 yeni URL (toplam 18) repo envanteri
(2103 satır `url-inventory-20261013.txt` · oturum içi 2633 satır `url-inventory.txt`)
ile **kesişim 0** — 2026-10-13 script çıktısı: `inv=2633 fresh=82 hits=0`.

---

## §11 Property adresi sözleşmesi (AudioObjectPropertyAddress)

### §11.1 Tip beyanı (birebir, [C14])

| Öğe | Değer | Kanıt |
|---|---|---|
| Rol | `Structure` | [C14] `metadata.roleHeading` |
| Beyan | `struct AudioObjectPropertyAddress` | [C14] JSON token dizisi (`keyword: struct` → `identifier: AudioObjectPropertyAddress`) |
| Amaç (birebir) | "An AudioObjectPropertyAddress collects the three parts that identify a specific property together in a struct for easy transmission." | [C14] `abstract` |
| Üç parça | `mSelector` · `mScope` · `mElement` | [C14] `topicSections → Instance Properties` |
| Arayüz varyantı | Swift + Objective-C (`occ`) | [C14] `variants[].traits[].interfaceLanguage` |
| Modül | Core Audio | [C14] `metadata.modules[0].name` |

### §11.2 Alan sözlüğü (birebir, [C14])

| Alan | Tanım (birebir) | Kesin tanımlayıcı |
|---|---|---|
| `mSelector` | "The AudioObjectPropertySelector for the property." | `c:@T@AudioObjectPropertySelector` |
| `mScope` | "The AudioObjectPropertyScope for the property." | `c:@T@AudioObjectPropertyScope` |
| `mElement` | "The AudioObjectPropertyElement for the property." | `c:@T@AudioObjectPropertyElement` |

### §11.3 Başlatıcı (birebir, [C14])

| Başlatıcı | Parametre → Tip |
|---|---|
| `init()` | parametresiz |
| `init(mSelector:mScope:mElement:)` | `mSelector: AudioObjectPropertySelector` · `mScope: AudioObjectPropertyScope` · `mElement: AudioObjectPropertyElement` |

### §11.4 L0 kural eşlemesi (bu adres yapısı üzerinden)

| L0 kuralı | Adres yapısındaki karşılığı | Durum |
|---|---|---|
| Zero-allocation (audio thread) | `AudioObjectPropertyAddress` stack'te bir `struct` — okuma/yazma çağrılarında tahsis **yok** (beyan: "easy transmission") | uyumlu (tasarım) |
| Lock-free | adres yapısı kilit taşımaz; kilit/kilit-sizlik kararı **çağrıyan** tarafındadır → ⚠️ | ⚠️ VERIFICATION REQUIRED |
| Property değeri nereye yazılır? | bu yapı yalnız **adresi** taşır; değer belleği çağıran tarafça sağlanır → belgede tahsis kuralı **geçmiyor** | ⚠️ VERIFICATION REQUIRED |
| `constexpr` kullanımı | Swift/Occ arayüzü — C++ `constexpr` karşılığı yok → Neva Engine tarafında değerlendirilir | kapsam dışı (L0 C++) |

---

## §12 Dinleyici ve rota bildirimi sözleşmesi

### §12.1 `AudioHardwarePropertyListenerProc` (birebir, [C15])

| Öğe | Değer | Kanıt |
|---|---|---|
| Rol | `Type Alias` (`symbolKind: typealias`) | [C15] `metadata` |
| Beyan | `typealias AudioHardwarePropertyListenerProc = (AudioHardwarePropertyID, UnsafeMutableRawPointer?) -> OSStatus` | [C15] JSON token dizisi |
| Kesin tanımlayıcı | `c:@T@AudioHardwarePropertyListenerProc` | [C15] `metadata.externalID` |
| Platform | Mac Catalyst · macOS (beta/deprecated=false, unavailable=false) | [C15] `metadata.platforms` |
| Dönüş değeri (birebir) | "The return value is currently unused and should always be 0." | [C15] Discussion |
| Kayıt hedefi (birebir) | "Clients register an AudioHardwarePropertyListenerProc with the AudioSystemObject in order to receive notifications when the properties of the object change." | [C15] Discussion |
| Eşdeğerlik notu (birebir) | "Note that the same functionality is provided by AudioObjectPropertyListenerProc." | [C15] Discussion |

### §12.2 `routeChangeNotification` (birebir, [C16])

| Öğe | Değer | Kanıt |
|---|---|---|
| Rol | `Type Property` | [C16] `metadata.roleHeading` |
| Beyan | `class let routeChangeNotification: NSNotification.Name` | [C16] JSON token dizisi |
| Amaç (birebir) | "A notification the system posts when its audio route changes." | [C16] `abstract` |
| `userInfo` anahtarları (birebir) | `AVAudioSessionRouteChangeReasonKey` ve `AVAudioSessionRouteChangePreviousRouteKey` — "keys, which provide information about the route change." | [C16] `primaryContentSections` |
| Yöneltme notu (birebir) | "See Responding to audio route changes for more information on using this notification." | [C16] (`responding-to-audio-route-changes` referansı) |
| Çağrı thread'i (birebir) | "The system posts this notification on a secondary thread." | [C16] |

### §12.3 L0 kural eşlemesi (bildirim kanalı)

| L0 kuralı | Bu bölümdeki karşılığı | Durum |
|---|---|---|
| `noexcept` (audio callback) | bildirim **ikincil thread**'te gelir → ana ses callback'ine doğrudan taşınması yasak; kuyruk gerekir | bağlayıcı (uygulama tasarımı) |
| Lock-free | `userInfo` sözlüğü Objective-C nesnesi — ses thread'ine kopyalanmadan taşınamaz → ⚠️ | ⚠️ VERIFICATION REQUIRED |
| ASIO Exclusive Lock | bu sürümde CoreAudio karşılık belgesi yok → politika kararı | ⚠️ VERIFICATION REQUIRED |
| Dönüş değeri 0 | dinleyici imzasında iade değeri "currently unused" → hata sinyalleme kanalı **değil** | uyumlu (belge birebir) |

---

## §13 Ses birimi ve kuyruk katmanı

### §13.1 `AudioUnit` tip tanımı (birebir, [C17])

| Öğe | Değer | Kanıt |
|---|---|---|
| Rol | `Type Alias` | [C17] `metadata.roleHeading` |
| Beyan | `typealias AudioUnit = AudioComponentInstance` | [C17] JSON token dizisi |
| Platform | iOS · iPadOS · Mac Catalyst · macOS · tvOS · visionOS | [C17] `declarations[0].platforms` |
| Amaç (birebir) | "The data type for a plug-in component that provides audio processing or audio data generation." | [C17] `abstract` |
| Alt tipler (birebir) | "The various types of audio units are described in the Audio Unit Types … and the subtypes of audio units provided by Apple are described in Effect Audio Unit Subtypes, …" | [C17] Discussion |

### §13.2 Bileşen örneği (birebir, [C17] referansları)

| Simge | Beyan | Amaç (birebir) |
|---|---|---|
| `AudioComponentInstance` | `typealias AudioComponentInstance` | "A component instance, or object, is an audio unit or audio codec." |

### §13.3 Audio Queue Services (birebir, [C18])

| Öğe | Değer | Kanıt |
|---|---|---|
| Rol | `API Collection` | [C18] `metadata.roleHeading` |
| Amaç (birebir) | "Connect to audio hardware and manage the recording or playback process." | [C18] `abstract` |
| Konum | AudioToolbox | [C18] hierarchy `com.apple.audiotoolbox` |
| Kapsam dışı not | kuyruk (queue) mimarisi AAudio/Oboe dalındaki callback zinciriyle **aynı seviyede değil** → karşılaştırma bu dalın dışında, yalnız kayıt tutulur | kapsam notu |

### §13.4 L0 kural eşlemesi (işleme birimi)

| L0 kuralı | Karşılığı | Durum |
|---|---|---|
| 32-bit float (ADR-017) | `AudioUnit` girdi/çıktı formatı belgede sayılmıyor → format kararı kurulum katmanında | ⚠️ VERIFICATION REQUIRED |
| 512 örnek · 48 kHz | `AudioUnit` render periyodu belgede yok → ölçüm PLANNED | PLANNED |
| Zero-allocation | "plug-in component" çalışma anında örnek tamponu üzerinde çalışır; tahsis yasağı uygulama katmanında bağlayıcı | bağlayıcı (uygulama) |
| PCM3168A (ADR-038 H001) | donanım katmanı — bu dalın dışı | K001-donanım |

---

## §14 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| A1 | Property/scope/selector okuma-yazma semantiği hangi sayfadan doğrulanacak? | ⚠️ VERIFICATION REQUIRED |
| A2 | Platform hedefi (macOS / iOS / ikisi)? | UNKNOWN |
| A3 | Kategori kararı (`playback` vs `playAndRecord`)? | ⚠️ VERIFICATION REQUIRED |
| A4 | 8 sayfanın JSON uç noktası neden 404 — farklı path mi gerekiyor? | ⚠️ VERIFICATION REQUIRED |
| A5 | IOProc yerine `AudioDeviceIOBlock` (block varyantı) kullanılacak mı? | ⚠️ VERIFICATION REQUIRED |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `coreaudio-anayasa.md` | Kimlik, nesne ağacı ve oturum modeli (bu dosya) | Kademe-4 |
| mimari | `coreaudio-surucu.md` | IO döngüsü, zaman damgası ve tampon sözleşmesi | Kademe-4 |
| kod | `coreaudio-ornekler.md` | Tip beyanları, eşleme ve repo kanıtı | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
