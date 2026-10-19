---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-surucu
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio/coreaudio
children: []
depends_on: []
evidence:
  - https://developer.apple.com/documentation/coreaudio/audiodeviceioproc
  - https://developer.apple.com/documentation/coreaudio/audiodeviceid
  - https://developer.apple.com/documentation/coreaudio/audioobjectid
  - https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification
  - https://developer.apple.com/documentation/coreaudio/audiobuffer
validation:
  - "2026-10-13 · IOProc sayfası JSON uç noktadan birebir alındı (7 parametre + dönüş + Discussion) · envanter kesişimi 0"
  - "2026-10-13 · kesinti bildirimi JSON birebir (user-info anahtarları, began/ended, iOS 10 notu, main thread)"
  - "repo taraması: CoreAudio|AVAudioSession|AudioDeviceIOProc deseni 0 isabet → kod YOK (bkz. coreaudio-ornekler.md §1)"
open_questions:
  - "IOProc'un hangi thread/öncelikte ve hangi periyotla çağrıldığı belgede yazmıyor → ⚠️ VERIFICATION REQUIRED"
  - "IOProc başlatma/kaldırma API'leri (kayıt, start/stop) bu turda doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "512 örnek · 48 kHz periyot doğrulaması cihaz üstünde ölçülmedi → PLANNED"
group: mimari
---

# CoreAudio Sürücü — IO Döngüsü · Zaman Damgası · Tampon Sözleşmesi · Kesinti

> **Bu dosya ne değildir:** Kod, benchmark veya cihaz sürücüsü geliştirme rehberi
> değildir. Bu dosya `AudioDeviceIOProc` IO döngüsünün **sözleşmesini** belge
> kanıtıyla (Apple JSON dokümantasyon ucu, 2026-10-13) tanımlar; zaman damgası ve
> tampon kuralları birebir alıntıdır. Belgede olmayan her iddia
> `⚠️ VERIFICATION REQUIRED` ile işaretlenir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Kod **YOK** (repo taraması
> 2026-10-13: `CoreAudio|AVAudioSession|AudioDeviceIOProc` desenine 0 isabet).
> Kimlik ve oturum modelinin tanımı için `coreaudio-anayasa.md`; tip beyanı ve
> repo kanıtı için `coreaudio-ornekler.md` kullanılır.

---

## §0 ELI15 — IO döngüsü bir cümlede

Apple cihazı ses işini **periyodik bir çağrıyla** yürütür: donanım (veya onun
yerine geçen sistem) `AudioDeviceIOProc` callback'ini her IO döngüsünde çağırır,
giriş verisini *işlenmemiş* olarak getirir, çıkış için *baştan sıfırlanmış* bir
tampon ister ve üç ayrı zaman damgası (döngü başlangıcı · girişin donanımdan
alındığı an · çıkışın donanıma verileceği an) verir; uygulamanın işi bu çağrı
içinde girmek-çıkmak, sırayı bozmamak ve dönüş değerini 0 yazmaktır.
(Kaynak: §2 · §3 · §4)

---

## §1 Dosya künyesi

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-surucu` |
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio/coreaudio` |
| Kademe | 4 · deep-dal içeriği |
| Birincil kaynak | `AudioDeviceIOProc` JSON (platform: macOS · Mac Catalyst) |
| İkincil kaynak | `interruptionNotification` JSON (platform: iOS · iPadOS · Mac Catalyst · tvOS · visionOS · watchOS) |
| Repo durumu | CoreAudio kodu YOK → UNKNOWN (§10 S1) |

---

## §2 IO döngüsünün sözleşmesi

### §2.1 Amaç (birebir, `AudioDeviceIOProc` Discussion)

> "An AudioDeviceIOProc is called by an AudioDevice to provide input data read
> from the device and collect output data to be written to the device for the
> current IO cycle."

| Öğe | Belge metni | Yorum (belgeye sadık) |
|---|---|---|
| Ne çağırır | "an AudioDevice" | çağıran donanım/dönüşüm katmanı; sürücü sınıfı adı belgede yok |
| Ne getirir | "input data read from the device" | giriş verisi okunmuş olarak gelir |
| Ne toplar | "output data to be written to the device" | çıkış verisi callback içinde koyulur |
| Kapsam | "for the current IO cycle" | her çağrı = bir IO döngüsü |

### §2.2 Dönüş değeri (birebir)

> "The return value is currently unused and should always be 0."

| Kural | Değer | Kanıt |
|---|---|---|
| Dönüş | `OSStatus` imzada var, "currently unused" | [S1] JSON |
| Uygulama | "should always be 0" | [S1] JSON |
| Hata raporulama | belgede dönüş üzerinden hata kanalı **tanımlanmıyor** → hata yönetimi nasıl? | ⚠️ VERIFICATION REQUIRED |

### §2.3 Kayıt — 7. parametre (client data, birebir)

> "A pointer to client data established when the AudioDeviceIOProc was registered
> with the AudioDevice."

| Öğe | Belge metni | Durum |
|---|---|---|
| 7. parametre | `UnsafeMutableRawPointer?` | [S1] JSON imza |
| Kayıt anı | "established when the AudioDeviceIOProc was registered" | kayıt API'sinin adı belgede yok → ⚠️ VERIFICATION REQUIRED |
| Kullanım | callback'e özel veri taşıma kancası | belge metninden çıkan anlam |

### §2.4 Döngünün çağrı amacı — tam blok

| Alan | Belge metni (birebir) |
|---|---|
| Sınıf | `typealias AudioDeviceIOProc = (AudioObjectID, UnsafePointer<AudioTimeStamp>, UnsafePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>, UnsafeMutablePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>, UnsafeMutableRawPointer?) -> OSStatus` |
| platforms (JSON) | Mac Catalyst · macOS |
| roleHeading (JSON) | `Type Alias` |
| externalID (JSON) | `c:@T@AudioDeviceIOProc` |

> **Kapsam notu:** IO döngüsünün *başlatılması/durdurulması*, cihaz seçimi ve
> özellik (property) okuma-yazma API'leri bu turda JSON'dan alınamadı → bu dosyada
> yalnızca **çağrı içi** sözleşme belgelenir (§10 S2).

### §2.5 Varyant tipler (JSON ilgili-simboller listesinden birebir)

| Tip | Belgedeki satırı | Bu dosyadaki rolü |
|---|---|---|
| `AudioDeviceIOProc` | `typealias AudioDeviceIOProc` | birincil callback (§2.1–§2.4) |
| `AudioDeviceIOBlock` | `typealias AudioDeviceIOBlock` | block (closure) varyantı — imzası bu turda alınamadı → ⚠️ VERIFICATION REQUIRED |
| `AudioDeviceIOProcID` | `typealias AudioDeviceIOProcID` | kayıtlı callback kimliği — kayıt API'si ⚠️ (§10 S2) |
| `AudioDevicePropertyListenerProc` | `typealias AudioDevicePropertyListenerProc` | cihaz özelliği dinleyicisi — bu dosyanın dışı (anayasa §3.2) |
| `AudioHardwarePropertyListenerProc` | `typealias AudioHardwarePropertyListenerProc` | donanım özelliği dinleyicisi — dışı (anayasa §3.2) |

> Simge listesi [S1]/[S2]/[S3] JSON `references`/`topicSections` bloklarından
> alınmıştır; her satırın ayrı sayfası bu turda çekilmedi.

---

## §3 Zaman damgası — üç damga (IOProc parametre 2 · 4 · 6)

### §3.1 Özet tablosu

| # | Parametre | Ne bildirir | Sıfırlanma kuralı |
|---|---|---|---|
| 2 | `UnsafePointer<AudioTimeStamp>` | IO cycle **başladı** | sıfırlama metni yok |
| 4 | `UnsafePointer<AudioTimeStamp>` | ilk örneğin donanımdan **alındığı** an | "If the device has no input streams, the time stamp will be zeroed out." |
| 6 | `UnsafePointer<AudioTimeStamp>` | ilk örneğin donanıma **verileceği** an | "If the device has no output streams, the time stamp will be zeroed out." |

### §3.2 Parametre 2 — döngü başlangıcı (birebir)

> "An AudioTimeStamp that indicates the IO cycle started. Note that this time
> includes any scheduling latency that may have been incurred waking the thread
> on which IO is being done."

| Çıkarım (metne bağlı) | Açıklama |
|---|---|
| "IO cycle started" | damga, döngünün başlangıcını işaretler |
| "includes any scheduling latency" | damga uyandırma gecikmesini **içerir** — ölçüm yapan bu bileşeni hesaba katmalı |
| "waking the thread on which IO is being done" | IO bir thread üzerinde yapılır; thread uyandırma maliyeti damgaya gömülü |

> ⚠️ VERIFICATION REQUIRED: thread'in öncelik/izolasyon düzeyi ve periyot
> (buffer boyutu → gecikme) belgede sayılmıyor; 512 örnek · 48 kHz doğrulaması
> cihaz üstünde ölçülür (PLANNED).

### §3.3 Parametre 4 — giriş zaman damgası (birebir)

> "An AudioTimeStamp that indicates the time at which the first frame in the data
> was acquired from the hardware. If the device has no input streams, the time
> stamp will be zeroed out."

| Kural | Değer |
|---|---|
| Referans nokta | "the first frame in the data" |
| Kaynak | "acquired from the hardware" |
| Giriş yoksa | "zeroed out" |

### §3.4 Parametre 6 — çıkış zaman damgası (birebir)

> "An AudioTimeStamp that indicates the time at which the first frame in the data
> will be passed to the hardware. If the device has no output streams, the time
> stamp will be zeroed out."

| Kural | Değer |
|---|---|
| Referans nokta | "the first frame in the data" |
| Hedef | "will be passed to the hardware" (gelecek zaman — çıkış henüz yazılmadı) |
| Çıkış yoksa | "zeroed out" |

### §3.5 Damga alanları — kapsam dışı

| İddia | Durum |
|---|---|
| `AudioTimeStamp` iç alanları (`mSampleTime`, `mHostTime`, bayraklar) | bu tur JSON'larda **geçmiyor** → ⚠️ VERIFICATION REQUIRED (CoreAudioTypes sayfası ayrı kaynak) |
| "zeroed out"un alan-bazlı anlamı (hangi bayrak sıfırlı) | belgede açılanmıyor → ⚠️ VERIFICATION REQUIRED |
| Giriş–çıkış damgası farkından gecikme hesabı | yöntem belgede yok → ölçüm tasarımı K003-audio |

---

## §4 Tampon sözleşmesi — IOProc parametre 3 ve 5

### §4.1 Parametre 3 — giriş tamponu (birebir, tam metin)

> "An AudioBufferList containing the input data for the current IO cycle. For
> streams that are disabled, the AudioBuffer's mData field will be NULL but the
> mDataByteSize field will still say how much data would have been there if it
> was enabled. Note that the contents of this structure should never be modified."

| Kural | Belge metni | Sınıf |
|---|---|---|
| İçerik | "the input data for the current IO cycle" | bilgi |
| Devre dışı akış | "mData field will be NULL" | davranış |
| Devre dışı akış | "mDataByteSize field will still say how much data would have been there if it was enabled" | davranış |
| Değiştirme | "the contents of this structure should never be modified" | **YASAK** |

### §4.2 Parametre 5 — çıkış tamponu (birebir, tam metin)

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
> modified." *(metindeki "other wise" kaynağın yazımıdır — [sic], 2026-10-13 JSON)*

| Aşama | Kural | Belge metni |
|---|---|---|
| Girişte | `mDataByteSize` = kapasite | "the maximum amount of data that can be placed in the buffer" |
| Girişte | bellek temizlenmiş | "the buffer's memory has been zeroed out" |
| Çıkışta (sabit paket) | `mDataByteSize` **değiştirilmez** | "Otherwise, the mDataByteSize field should not be changed." |
| Çıkışta (değişken bayt/paket — AC-3 örneği) | `mDataByteSize` yazılır | "the client has to fill out on exit each mDataByteSize field … with the amount of data that was put in the buffer" |
| Devre dışı akış | `mData` NULL · `mDataByteSize` yine de yazılır | §4.1 ile aynı kural |
| Genel | diğer alanlara dokunulmaz | "the contents of this structure should not other wise be modified." |

### §4.3 `mData` NULL davranışı — her iki yön için tek tablo

| Yön | `mData` | `mDataByteSize` | Sonuç (uygulama) |
|---|---|---|---|
| Giriş (param 3) | NULL | "would have been there if it was enabled" | NULL'a yazılmaz/okunmaz → ⚠️ çökme riski uygulama sorumluluğu |
| Çıkış (param 5) | NULL | aynı kural | callback'te yazım yapılmaz |
| Akış etkin | geçerli işaretçi | giriş: veri boyutu · çıkış: kapasite | belgelenen tek normal davranış |

> **L0 bağlantısı:** "NULL tampona dokunma" bu dalda *kanıtlanmış davranış*
> değildir; belge yalnızca NULL durumunu tanımlar. Savunma (kontrol + erken
> dönüş) uygulama katmanında — L0 kuralıdır (bkz. §6).

### §4.4 `mDataByteSize` karar ağacı

```text
callback param 5 (çıkış) girişi
├─ mData == NULL (akış devre dışı)
│    → bu AudioBuffer'a yazma; mDataByteSize'a dokunma
├─ mData != NULL
│    ├─ bayt/paket sabit mi (LinearPCM benzeri)?
│    │    → mDataByteSize DEĞİŞTİRİLMEZ; kapasiteyi aşmadan yaz
│    └─ bayt/paket değişken mi (AC-3 örneği — belgedeki tek örnek)?
│         → çıkışta her AudioBuffer için konulan veri kadar mDataByteSize YAZ
```

> Ağaç yalnızca §4.2 alıntısından türetilmiştir; belgede "LinearPCM sabittir"
> ifadesi **geçmiyor** → LinearPCM satırı `⚠️ VERIFICATION REQUIRED` (kapsam:
> `kAudioFormatLinearPCM` sayfası bu turda yalnız `<meta description>` verdi).

### §4.5 Yapısal kavramlar

| Kavram | Belge metni | Etiket |
|---|---|---|
| `AudioBuffer` | "A structure that holds a buffer of audio data." | [S5] `<meta>` |
| `AudioBufferList` | imzada `UnsafePointer<AudioBufferList>` / `UnsafeMutablePointer<AudioBufferList>` | [S1] JSON |
| Giriş yönü | `UnsafePointer` (değiştirilemez) | [S1] imza |
| Çıkış yönü | `UnsafeMutablePointer` (yazılabilir) | [S1] imza |
| `mNumberBuffers` / çoklu tampon anlamı | bu tur JSON'da alan dokümanı **yok** | ⚠️ VERIFICATION REQUIRED |
| `AudioBuffer` alan listesi (mData, mDataByteSize, mNumberChannels) | JSON 404 — yalnız meta alındı | ⚠️ VERIFICATION REQUIRED |

### §4.6 Yasaklar özeti (audio thread L0 ile çakışan yüzey)

| # | Yasak | Kaynak |
|---|---|---|
| 1 | giriş `AudioBufferList` içeriğini değiştirmek | [S1] "should never be modified" |
| 2 | devre dışı akışta NULL `mData`'ya yazmak/okumak | [S1] NULL kuralı + L0 savunması |
| 3 | sabit-paket formatta `mDataByteSize`'i değiştirmek | [S1] "should not be changed" |
| 4 | çıkış yapısının "diğer alanlarına" dokunmak | [S1] "should not other wise be modified" |
| 5 | callback içinde tahsis/kilit (L0) | belgede **yazmıyor** → bağlayıcı olan L0 kuralıdır (§6) |

---

## §5 Kesinti ve oturum döngüsü (AVAudioSession)

### §5.1 Bildirim künyesi (birebir, [S13])

| Alan | Değer |
|---|---|
| Swift beyanı | `class let interruptionNotification: NSNotification.Name` |
| Sabit adı | `AVAudioSessionInterruptionNotification` |
| Amaç | "A notification the system posts when an audio interruption occurs." |
| roleHeading (JSON) | `Type Property` |
| Yayın thread'i | "The system posts this notification on the main thread." |
| platforms (JSON) | iOS · iPadOS · Mac Catalyst · tvOS · visionOS · watchOS |

### §5.2 User-info anahtarları (JSON `topicSections` birebir)

| Anahtar | Belgedeki rolü (Discussion birebir bağlamı) |
|---|---|
| `AVAudioSessionInterruptionTypeKey` | "The notification's user-information dictionary contains the … key" |
| `AVAudioSessionInterruptionType` (began/ended) | "If the interruption type is …, the system interrupted your app's audio session and it's no longer active." (began) |
| `AVAudioSessionInterruptionOptionKey` | "If the interruption type is …, this dictionary also contains the … key." (ended) |
| `AVAudioSessionInterruptionWasSuspendedKey` | iOS 10 notunda: "the user-information dictionary contains the … key with a value of true" |
| `AVAudioSessionInterruptionReasonKey` | JSON topic listesinde (User Info Keys) |

### §5.3 Kesinti tipi (birebir)

| Vaka | Swift | Sabit adı | Belge metni |
|---|---|---|---|
| başlangıç | `case began` | `AVAudioSessionInterruptionTypeBegan` | "A type that indicates that the operating system began interrupting the audio session." |
| bitiş | `case ended` | `AVAudioSessionInterruptionTypeEnded` | "A type that indicates that the operating system ended interrupting the audio session." |

| Enum | Belge metni |
|---|---|
| `InterruptionType` | "Constants that describe the type of an audio interruption." |
| `InterruptionReason` | "Constants that define the reasons for an audio session interruption." |
| `InterruptionOptions` | "Constants that indicate the state of an audio session after an interruption." |

### §5.4 iOS 10 davranışı (birebir, tam not)

> "Starting in iOS 10, the system deactivates an app's audio session when it
> suspends the app process. When the app starts running again, it receives an
> interruption notification that the system has deactivated its audio session.
> This notification is necessarily delayed in time because the system can only
> deliver it once the app is running again. If the system suspended your app's
> audio session for this reason, the user-information dictionary contains the
> `AVAudioSessionInterruptionWasSuspendedKey` key with a value of `true`."

| Çıkarım (metne bağlı) | Sonuç |
|---|---|
| "necessarily delayed in time" | kesinti bildirimi **gecikmeli** gelebilir → bekleyen bildirim tamponu uygulama sorumluluğu |
| "only deliver it once the app is running again" | arka plandan dönüşte sıfırdan tetiklenir |

### §5.5 Arka plan kuralı (birebir, tam not)

> "If you configured your audio session to be nonmixable (the default behavior
> for the `playback`, `playAndRecord`, `soloAmbient`, and `multiRoute` categories),
> deactivate your audio session if you're not actively using audio when you go
> into the background. Doing so avoids having your audio session deactivated by
> the system (and receiving this somewhat confusing notification)."

*(Kategoriler JSON'da `AVAudioSession.Category` vaka referansı olarak geçiyor;
parantez içi adlar §5.7 tablosundadır.)*

### §5.6 Rota kopması

| Alan | Belge metni (birebir) | Etiket |
|---|---|---|
| `var prefersInterruptionOnRouteDisconnect: Bool` | "A Boolean value that indicates whether the system interrupts the audio session when the active route disconnects." | [S13] JSON |
| `setPrefersInterruptionOnRouteDisconnect(_:)` | JSON seeAlso "Handling interruptions" listesinde | [S13] JSON |
| `prefersNoInterruptionsFromSystemAlerts` (+ setter) | aynı seeAlso listesinde | [S13] JSON |

### §5.7 Kategori sabitleri (JSON birebir)

| Vaka | Swift beyanı | Sabit adı | Belge metni |
|---|---|---|---|
| `playback` | `static let playback: AVAudioSession.Category` | `AVAudioSessionCategoryPlayback` | "The category for playing recorded music or other sounds that are central to the successful use of your app." |
| `playAndRecord` | `static let playAndRecord: AVAudioSession.Category` | `AVAudioSessionCategoryPlayAndRecord` | "The category for recording (input) and playback (output) of audio, such as for a Voice over Internet Protocol (VoIP) app." |
| `soloAmbient` | `static let soloAmbient: AVAudioSession.Category` | `AVAudioSessionCategorySoloAmbient` | "The default audio session category." |
| `multiRoute` | `static let multiRoute: AVAudioSession.Category` | `AVAudioSessionCategoryMultiRoute` | "The category for routing distinct streams of audio data to different output devices at the same time." |

| Oturum nesnesi | Belge metni |
|---|---|
| `AVAudioSession` | "An object that communicates to the system how you intend to use audio in your app." |

> **Kapsam notu:** kategori *seçimi* (CoreMusic hangi kategoriyi kullanacak?) bu
> dosyanın konusu değil → `coreaudio-anayasa.md` §6 ve açık soru C3.

### §5.8 Kesinti akışı — belgeden çizilen sıra

```text
sistem kesinti başlatır (arama · alarm · rota değişimi …)
  → AVAudioSessionInterruptionNotification, ana thread'de yayınlanır
  → user-info: AVAudioSessionInterruptionTypeKey = began
       ("your app's audio session … no longer active")
  → (uygulama: ses üretimini durdurur — L0/kendi kararı)
sistem kesinti bitirir
  → aynı bildirim, ana thread'de
  → user-info: type = ended + AVAudioSessionInterruptionOptionKey
iOS 10+ askı senaryosu
  → uygulama tekrar çalıştığında gecikmeli bildirim
  → AVAudioSessionInterruptionWasSuspendedKey = true
```

> Akış çizimi yalnızca §5.2–§5.4 metinlerinden türetilmiştir; aradaki
> API çağrıları (ör. oturumu yeniden etkinleştirme) bu turda doğrulanmadı →
> `⚠️ VERIFICATION REQUIRED` (§10 S3).

---

## §6 L0 kural eşlemesi (CoreMusic ↔ bu belge)

| L0 kuralı | Belgede karşılığı | Durum |
|---|---|---|
| Zero-allocation (audio thread) | IOProc metninde tahsis yasağı **geçmiyor** → kural uygulama katmanında bağlayıcı | bağlayıcı (L0) · belgede yok |
| Lock-free (mutex yasak) | kilit/öncelik metni belgede yok | ⚠️ VERIFICATION REQUIRED |
| `noexcept` (C++) | callback Swift/C tipi; C++ tarafı Neva Engine (K003) | bağlayıcı (K003) |
| 512 örnek · 48 kHz | periyot/buffer boyutu belgede sayılmıyor | PLANNED (ölçüm) |
| ASIO Exclusive Lock | bu sürümde karşılık politikası belgesi yok | ⚠️ VERIFICATION REQUIRED |
| 32-bit float (ADR-017) | yalnız `kAudioFormatLinearPCM` meta tanımı alındı; 32-bit float doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 8.1 surround | kanal yerleşimi bu belgelerde yok | ⚠️ VERIFICATION REQUIRED |
| PCM3168A (ADR-038 H001) | donanım katmanı — bu dalın dışı | K001-donanım |
| DC offset >0.5V → röle | donanım koruması — bu dalın dışı | K001-donanım |
| Per-OS player FROZEN (ADR-019) | bu dosya yalnız belge özeti; player kodu yok | geçerli — dokunulmaz |

---

## §7 Eşik hesabı — 512 örnek · 48 kHz (kapsam içi aritmetik)

| Büyüklük | Hesap | Değer |
|---|---|---|
| Örnekleme oranı | sartname | 48 000 Hz |
| Tampon | sartname | 512 örnek |
| Periyot | 512 ÷ 48000 | **10,666… ms** (yaklaşık 10,67 ms) |
| Saniyedeki döngü | 48000 ÷ 512 | 93,75 IO döngüsü/s |
| Tek örnek süresi | 1 ÷ 48000 | 20,833… µs (yaklaşık 20,83 µs) |
| Belge karşılığı | IOProc çağrısının periyodu | belgede sayı **yok** → ⚠️ cihaz üstünde ölçüm PLANNED |

> Hesap saf aritmetiktir (sartname değerleri); CoreAudio'nun bu periyodu
> desteklediği **iddia edilmez** — doğrulama cihaz üstünde yapılır.

---

## §8 Belge sınırları — bu turda doğrulanmayanlar

| # | Konu | Neden | Etiket |
|---|---|---|---|
| B1 | IOProc kaydı/başlatma/durdurma API'leri | JSON'da geçmedi (evet isabeti 0) | ⚠️ VERIFICATION REQUIRED |
| B2 | Cihaz seçimi / varsayılan cihaz özellikleri | ayrı sayfa bu turda alınmadı | ⚠️ VERIFICATION REQUIRED |
| B3 | `AudioTimeStamp` alanları (`mSampleTime`/`mHostTime`) | JSON evet isabeti 0 | ⚠️ VERIFICATION REQUIRED |
| B4 | `AudioBufferList` alan dokümanı (`mNumberBuffers`) | JSON evet isabeti 0 | ⚠️ VERIFICATION REQUIRED |
| B5 | `kAudioFormatLinearPCM` sayısal/4-char değeri | sayfa JSON 404 → yalnız meta | ⚠️ VERIFICATION REQUIRED |
| B6 | Örnek formatı (interleaved · float32) doğrulaması | belgede yok | ⚠️ VERIFICATION REQUIRED |
| B7 | Zaman damgası birimi (saniye vs. tick) | belgede açılanmıyor | ⚠️ VERIFICATION REQUIRED |
| B8 | IO thread önceliği / gerçek-zaman garantisi | belgede yok | ⚠️ VERIFICATION REQUIRED |

---

## §9 Web kanıt matrisi (2026-10-13 · 13 URL · envanter kesişimi 0)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| S1 | IOProc imzası + 7 parametre + dönüş + Discussion | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | 2026-10-13 | ✅ 200 · JSON birebir |
| S2 | `AudioDeviceID = AudioObjectID` + sınıf ilişkisi | https://developer.apple.com/documentation/coreaudio/audiodeviceid | 2026-10-13 | ✅ 200 · JSON birebir |
| S3 | `AudioObjectID = UInt32` + "handle on a specific AudioObject" | https://developer.apple.com/documentation/coreaudio/audioobjectid | 2026-10-13 | ✅ 200 · JSON birebir |
| S4 | `AVAudioUnitEffect` ailesi | https://developer.apple.com/documentation/avfaudio/avaudiouniteffect | 2026-10-13 | ✅ 200 · JSON birebir |
| S5 | `AudioBuffer` tanımı | https://developer.apple.com/documentation/coreaudio/audiobuffer | 2026-10-13 | ✅ 200 · `<meta description>` (JSON 404) |
| S6 | kesinti bildirimi + iOS 10 notu + main thread + kategoriler | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification | 2026-10-13 | ✅ 200 · JSON birebir |
| S7 | `playback` kategori tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback | 2026-10-13 | ✅ 200 · `<meta description>` |
| S8 | `AVAudioSession.Mode` tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessionmode | 2026-10-13 | ✅ 200 · `<meta description>` |
| S9 | `default` mod tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessionmodedefault | 2026-10-13 | ✅ 200 · `<meta description>` |
| S10 | `AudioFormatID` tanımı | https://developer.apple.com/documentation/coreaudio/audioformatid | 2026-10-13 | ✅ 200 · `<meta description>` |
| S11 | `AudioFormatFlags` tanımı | https://developer.apple.com/documentation/coreaudio/audioformatflags | 2026-10-13 | ✅ 200 · `<meta description>` |
| S12 | `kAudioFormatLinearPCM` tanımı | https://developer.apple.com/documentation/coreaudio/kaudioformatlinearpcm | 2026-10-13 | ✅ 200 · `<meta description>` |
| S13 | Audio Services tanımı | https://developer.apple.com/documentation/audiotoolbox/audio_services | 2026-10-13 | ✅ 200 · `<meta description>` |

**Çakışma kontrolü:** bu 13 URL repo URL envanteri (2103 satır) ile
**kesişim 0** — 2026-10-13 ölçümü.

---

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| S1 | CoreMusic/CoreAudio hedefi var mı (repo taraması 0 isabet)? | UNKNOWN |
| S2 | IOProc kayıt/start/durdurma API'leri hangi sayfalardan doğrulanacak? | ⚠️ VERIFICATION REQUIRED |
| S3 | Kesinti sonrası oturumu yeniden etkinleştirme akışının API adları? | ⚠️ VERIFICATION REQUIRED |
| S4 | Periyot/buffer (512·48k) hangi property ile okunacak ve doğrulanacak? | ⚠️ VERIFICATION REQUIRED |
| S5 | Giriş–çıkış damga farkı ölçümü K003'te nasıl kullanılacak? | ⚠️ VERIFICATION REQUIRED |

---

## §11 Alıntı politikası

| Kural | Uygulama (bu dosya) |
|---|---|
| Kaynak apostrof (`’` = JSON'da bozuk `ǽ�?�ts` çıktısı) | alıntıda **düz apostrof** `'` kullanıldı — okunabilirlik için; metin değişmedi |
| "other wise" yazımı | kaynağın kendi yazımıdır → `[sic]` ile işaretlendi (§4.2) |
| Türetme | yalnız metinden çıkan yorum "Çıkarım" tablosunda; belge dışı iddia `⚠️` |
| Envantelesel URL | 13/13 bu turda taze 200 · kesişim 0 |
| Placeholder yasağı | `TBD` · boş hücre · `{...}` yer tutucu kullanılmadı |

---

## §12 Dosyalar arası sınır

| Soru | Gideceği dosya |
|---|---|
| Kimlik tipleri, nesne ağacı, kategori/oturum *modeli* | `coreaudio-anayasa.md` §3 · §6 |
| IO çağrısı içi davranış (zaman damgası, tampon, kesinti akışı) | bu dosya (§3–§5) |
| Tip beyanı kod blokları, repo kanıtı, ALSA/WASAPI eşlemesi | `coreaudio-ornekler.md` §2 · §4 |
| DSP algoritması (EQ/reverb), Neva Engine kodu | K004-dsp · K003-audio (bu dalın dışı) |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `coreaudio-anayasa.md` | Kimlik, nesne ağacı ve oturum modeli | Kademe-4 |
| mimari | `coreaudio-surucu.md` | IO döngüsü, zaman damgası ve tampon sözleşmesi (bu dosya) | Kademe-4 |
| kod | `coreaudio-ornekler.md` | Tip beyanları, eşleme ve repo kanıtı | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
