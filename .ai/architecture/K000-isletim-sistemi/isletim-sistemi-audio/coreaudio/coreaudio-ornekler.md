---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-ornekler
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
validation:
  - "2026-10-13 · repo taraması (php/cpp/h/hpp/js/ts/cs; vendor/node_modules/.git hariç): CoreAudio|AVAudioSession|AudioDeviceIOProc|snd_pcm_|IMMDevice|WASAPI = 0 isabet"
  - "2026-10-13 · 13 Apple URL HTTP 200 · envanter (2103 satır) kesişimi 0 · 5 sayfa JSON birebir, 8 sayfa yalnız meta"
  - "2026-10-13 · repo satırları PowerShell 0-tabanlı dizi indeksiyle birebir alındı"
open_questions:
  - "CoreMusic CoreAudio hedefi yok → bu dosyadaki kod blokları belge/iskelet, çalıştırılmış kod DEĞİL (UNKNOWN)"
  - "`kAudioFormatLinearPCM` JSON 404 → sayısal değer yazılmadı · ⚠️ VERIFICATION REQUIRED"
  - "WASAPI sütunundaki karşılıklar bu turda doğrulanmadı → ⚠️ VERIFICATION REQUIRED (bkz. ../wasapi/)"
group: kod
---

# CoreAudio Örnekler — Tip Beyanları · Eşleme · Repo Kanıtı

> **Bu dosya ne değildir:** Çalıştırılabilir bir player veya sürücü kodu değildir.
> Bu dosya üç şey taşır: (1) CoreMusic reposunda CoreAudio kanıtı **araştırmasının
> sonucu**, (2) Apple JSON dokümanından **birebir** alınan tip beyanları,
> (3) ALSA/WASAPI ile eşleme matrisi — eşleşme iddiaları doğrulanana kadar
> `⚠️ VERIFICATION REQUIRED` işaretlidir.
>
> **Durum etiketi:** ŞARTNAME/belge IMPLEMENTED · Repo kodu **YOK** (§1).

---

## §0 ELI15 — bu dosya ne taşır

Önce soruyu diskte cevaplarız: "CoreMusic'te CoreAudio kodu var mı?" → tarama
sonucu **0 isabet** (§1). Sonra belgeden gelen tip beyanlarını kod gibi değil
**belge alıntısı** olarak veririz (§2–§3), sonra bu dalın kavramlarını ALSA ve
WASAPI dosyalarındaki kavramlarla eşleriz — eşleme kanıtlanana kadar hazır cevap
değil, işareli soru olarak durur (§4).

---

## §1 Repo kanıtı — CoreMusic'te CoreAudio kodu YOK (2026-10-13)

### §1.1 Tarama sonucu

| Desen | Kapsam | İsabet |
|---|---|---|
| `CoreAudio` | `*.php,*.cpp,*.h,*.hpp,*.js,*.ts,*.cs` (vendor/node_modules/.git hariç) | **0** |
| `AVAudioSession` | aynı | **0** |
| `AudioDeviceIOProc` | aynı | **0** |
| `snd_pcm_` | aynı | **0** |
| `IMMDevice` | aynı | **0** |
| `WASAPI` | aynı | **0** |

> Sonuç: repo **hiçbir yerel ses API'sine** bağlı değil — CoreAudio, ALSA ve
> WASAPI kodu da yok. Çalma yolu §1.4'tedir.

### §1.2 `shared/src/AI/AIEngine.php` — ffprobe çağrısı (satır 288–295)

**Dosya:** `C:\www\coremusic.net\shared\src\AI\AIEngine.php` (toplam 382 satır) ·
`Select-Object -Index (287..294)` = satır 288–295, birebir:

```php
    private function extractMetadata(string $filePath): array
    {
        $cmd = sprintf(
            'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
            escapeshellarg($filePath)
        );

        $output = shell_exec($cmd);
```

| Gözlem | Değer |
|---|---|
| Metadata kaynağı | `ffprobe` (ffmpeg ailesi) — işletim sistemi ses API'si **değil** |
| Çıkış | JSON (`-print_format json`) |
| Güvenlik | yol `escapeshellarg` ile kaçırılmış (PHP katmanı) |
| CoreAudio ilişkisi | **yok** — dosya bağımsız |

> Not: bu fonksiyonun docblock satırı (286–287) kaynakta bozuk karakter
> (mojibake) taşıdığı için alıntıya **alınmadı** — alıntı satır 288'de, fonksiyon
> imzasında başlar. (Kaynak dosya bu turun beyazlistesi dışında; düzeltilmedi.)

### §1.2.1 Repo çalma yolu (satırlardan türetilen şema)

```text
disk dosyası
  → shared/src/AI/AIEngine.php::extractMetadata()   [satır 288–295]
      ffprobe -print_format json -show_format -show_streams
      → sample_rate/bit_depth/channels (yoksa 44100/16/2)  [satır 102–104]
  → home.coremusic.net/include/Stream/MusicStreamHandler.php
      HTTP_RANGE parse               [satır 91]
      416 / 206 / 200 yanıt kodları  [satır 95–106]
      base header'lar (Accept-Ranges, Content-Length, ETag) [satır 310–329]
      gövde: fopen('rb') + Range     [satır 331–334]
  → tarayıcı <audio> / HTML5 oynatma  (kod YOK — arayüz katmanı)
```

| Şemadaki adım | Yerel ses API'si kullanır mı? |
|---|---|
| ffprobe | hayır (süre/kodlama bilgisi) |
| HTTP Range | hayır (dosya aktarımı) |
| tarayıcı oynatma | tarayıcı kendi yolunu kullanır — repo kodu değildir |

### §1.3 `shared/src/AI/AIEngine.php` — varsayılan format değerleri (satır 102–104)

**Dosya:** aynı · `Select-Object -Index (101..103)` = satır 102–104, birebir:

```php
            'sample_rate'   => $metadata['sample_rate'] ?? 44100,
            'bit_depth'     => $metadata['bit_depth'] ?? 16,
            'channels'      => $metadata['channels'] ?? 2,
```

| Gözlem | Değer | Durum |
|---|---|---|
| Varsayılan örnekleme | 44 100 Hz | repo kodunda böyle |
| Varsayılan bit derinliği | 16 | repo kodunda böyle |
| Kanal | 2 (stereo) | repo kodunda böyle |
| Sartname ile karşılaştırma | sartname 48 kHz · 512 örnek → bu PHP yolunda **yok** | ⚠️ ölçüm PLANNED (L0/K003 işi) |

### §1.4 `home.coremusic.net/include/Stream/MusicStreamHandler.php` — HTTP Range yolu

**Dosya:** `C:\www\coremusic.net\home.coremusic.net\include\Stream\MusicStreamHandler.php`
(toplam 379 satır).

**Satır 46** — `Select-Object -Index (45)`, birebir:

```php
    private const CHUNK_BYTES = 1048576; // 1 MiB
```

**Satır 91–106** — `Select-Object -Index (90..105)`, birebir (parça):

```php
        $range = $this->parseRange((string)($_SERVER['HTTP_RANGE'] ?? ''), $size);

        $this->emitBaseHeaders($mime, $range['length'], $etag, $lastMod);

        if ($range['status'] === 416) {
            header('Content-Range: bytes */' . $size);
            http_response_code(416);
            exit;
        }

        if ($range['status'] === 206) {
            header('Content-Range: bytes ' . $range['start'] . '-' . $range['end'] . '/' . $size);
            http_response_code(206);
        } else {
            http_response_code(200);
        }
```

**Satır 310–329** — `Select-Object -Index (309..328)`, birebir (parça):

```php
    private function emitBaseHeaders(string $mime, int $length, string $etag, string $lastMod): void
    {
        if (function_exists('ini_set')) {
            @ini_set('zlib.output_compression', '0');
        }
        while (ob_get_level() > 0) {
            @ob_end_clean();
        }
        @ignore_user_abort(true);

        header_remove('Content-Type');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $length);
```

**Satır 331–334** — `Select-Object -Index (330..333)`, birebir:

```php
    private function sendBody(string $path, int $start, int $length): never
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
```

### §1.5 Sonuç tablosu

| Soru | Cevap | Kanıt |
|---|---|---|
| CoreMusic'te CoreAudio kodu var mı? | **YOK** | §1.1 · 0 isabet |
| Çalma yolu nedir? | dosya → ffmpeg/ffprobe → HTTP Range (206) → tarayıcı | §1.2–§1.4 |
| Yerel ses API'si kullanılıyor mu? | hayır (ALSA/WASAPI de 0) | §1.1 |
| Bu dal kod tarafında ne ifade eder? | yalnız **şartname/belge** — implementasyon PLANNED | frontmatter `status: draft` |

---

## §2 Swift tip beyanları — JSON uç noktadan birebir (2026-10-13)

### §2.1 Kimlik tipleri

```swift
typealias AudioObjectID = UInt32
```

> Discussion (birebir): "A UInt32 that provides a handle on a specific AudioObject."
> JSON `metadata.roleHeading` = `Type Alias` · `externalID` = `c:@T@AudioObjectID`
> · `platforms` = Mac Catalyst · macOS.
> Kaynak: https://developer.apple.com/documentation/coreaudio/audioobjectid

```swift
typealias AudioDeviceID = AudioObjectID
```

> Discussion (birebir): "AudioDevice is the base class for all objects that
> represent an audio device. AudioDevice is a subclass of AudioObject.
> AudioDevices normally contain AudioStreams and AudioControls, but may contain
> other things depending on the kind of AudioDevice (e.g. aggregate devices
> contain other AudioDevices)."
> C karşılığı (JSON birebir): `typedef AudioObjectID AudioDeviceID;`
> Kaynak: https://developer.apple.com/documentation/coreaudio/audiodeviceid

```swift
struct UInt32
```

> Tanım (birebir): "A 32-bit unsigned integer value type."
> Kaynak: `audioobjectid` JSON ilgili-simboller bloğu.

### §2.2 IOProc imzası

```swift
typealias AudioDeviceIOProc = (AudioObjectID, UnsafePointer<AudioTimeStamp>,
    UnsafePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>,
    UnsafeMutablePointer<AudioBufferList>, UnsafePointer<AudioTimeStamp>,
    UnsafeMutableRawPointer?) -> OSStatus
```

> Dönüş (birebir): "The return value is currently unused and should always be 0."
> `platforms` = Mac Catalyst · macOS · `externalID` = `c:@T@AudioDeviceIOProc`.
> Parametre metinleri ve tampon kuralları: `coreaudio-surucu.md` §2–§4.
> Kaynak: https://developer.apple.com/documentation/coreaudio/audiodeviceioproc

### §2.3 Oturum ve kesinti beyanları

```swift
class let interruptionNotification: NSNotification.Name
```

> Sabit adı (JSON birebir): `AVAudioSessionInterruptionNotification` ·
> Amaç: "A notification the system posts when an audio interruption occurs." ·
> "The system posts this notification on the main thread." ·
> `roleHeading` = `Type Property` · `platforms` = iOS · iPadOS · Mac Catalyst ·
> tvOS · visionOS · watchOS.
> Kaynak: https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification

```swift
static let playback: AVAudioSession.Category
```

> Sabit adı (JSON birebir): `AVAudioSessionCategoryPlayback` · metin: "The category
> for playing recorded music or other sounds that are central to the successful
> use of your app." (kaynak: `interruptionnotification` JSON ilgili-simboller bloğu).

### §2.4 Ses birimi beyanları

```swift
class AVAudioUnitEffect
```

> Tanım (JSON birebir): "An object that processes audio in real time." ·
> "These effects run in real time and process some number of audio input samples
> to produce several audio output samples. A delay unit is an example of an
> effect unit." · `platforms` = iOS · iPadOS · Mac Catalyst · macOS · tvOS · visionOS.
> Alt sınıflar (JSON): `AVAudioUnitDelay` "An object that implements a delay
> effect." · `AVAudioUnitEQ` "An object that implements a multiband equalizer." ·
> `AVAudioUnitReverb` "An object that implements a reverb effect." ·
> `AVAudioUnitDistortion` "An object that implements a multistage distortion effect."
> Kaynak: https://developer.apple.com/documentation/avfaudio/avaudiouniteffect

### §2.5 Kalan kategori sabitleri (JSON birebir)

```swift
static let playAndRecord: AVAudioSession.Category   // AVAudioSessionCategoryPlayAndRecord
static let soloAmbient: AVAudioSession.Category     // AVAudioSessionCategorySoloAmbient
static let multiRoute: AVAudioSession.Category      // AVAudioSessionCategoryMultiRoute
```

| Sabit adı | Belge metni (birebir) |
|---|---|
| `AVAudioSessionCategoryPlayAndRecord` | "The category for recording (input) and playback (output) of audio, such as for a Voice over Internet Protocol (VoIP) app." |
| `AVAudioSessionCategorySoloAmbient` | "The default audio session category." |
| `AVAudioSessionCategoryMultiRoute` | "The category for routing distinct streams of audio data to different output devices at the same time." |

### §2.6 Oturum nesnesi ve kesinti enum'ları (JSON birebir)

```swift
class AVAudioSession           // "An object that communicates to the system how you intend to use audio in your app."
enum InterruptionType          // began  -> AVAudioSessionInterruptionTypeBegan
                               // ended  -> AVAudioSessionInterruptionTypeEnded
enum InterruptionReason        // "Constants that define the reasons for an audio session interruption."
struct InterruptionOptions     // "Constants that indicate the state of an audio session after an interruption."
```

| Vaka | Belge metni (birebir) |
|---|---|
| `began` | "A type that indicates that the operating system began interrupting the audio session." |
| `ended` | "A type that indicates that the operating system ended interrupting the audio session." |

> **Yorum yasağı:** bu bloklar *belge alıntısıdır*; çalıştırıldıkları iddia
> edilmez (repo'da karşılığı yok — §1).

---

## §3 Belge metinleri — JSON 404 olan 8 sayfa (yalnız `<meta description>`)

| Sayfa | Metin (birebir) | URL |
|---|---|---|
| Audio Services | "Play short sounds or trigger a vibration effect on iOS devices with the appropriate hardware." | https://developer.apple.com/documentation/audiotoolbox/audio_services |
| `playback` kategorisi | "The category for playing recorded music or other sounds that are central to the successful use of your app." | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback |
| `AVAudioSession.Mode` | "Audio session mode identifiers." | https://developer.apple.com/documentation/avfaudio/avaudiosessionmode |
| `default` mod | "The default audio session mode." | https://developer.apple.com/documentation/avfaudio/avaudiosessionmodedefault |
| `AudioBuffer` | "A structure that holds a buffer of audio data." | https://developer.apple.com/documentation/coreaudio/audiobuffer |
| `AudioFormatID` | "A type definition for audio format identifiers." | https://developer.apple.com/documentation/coreaudio/audioformatid |
| `AudioFormatFlags` | "A type definition for audio format flags." | https://developer.apple.com/documentation/coreaudio/audioformatflags |
| `kAudioFormatLinearPCM` | "A key that specifies the linear PCM codec, and uses the standard flags." | https://developer.apple.com/documentation/coreaudio/kaudioformatlinearpcm |

> Bu 8 sayfada JSON uç noktası 404 döndü; sayfa HTML'i JS kabuğu olduğu için
> yalnız meta metni kullanılabilir. `kAudioFormatLinearPCM`'in 4-char/ sayıssal
> değeri **yazılmadı** → `⚠️ VERIFICATION REQUIRED`.

### §3.1 Kesinti Discussion'ı — tam metin (JSON birebir, apostrof normalize)

> "The notification's user-information dictionary contains the
> `AVAudioSessionInterruptionTypeKey` key. If the interruption type is `began`,
> the system interrupted your app's audio session and it's no longer active. If
> the interruption type is `ended`, this dictionary also contains the
> `AVAudioSessionInterruptionOptionKey` key."
>
> "See ⟨bağlantı: `handling-audio-interruptions`⟩ for more information on using
> this notification." *(bağlantı başlığı JSON `references` map'inde yok → başlık
> `⚠️ VERIFICATION REQUIRED`; alıntıdaki ⟨…⟩ bağlantı yer tutucusudur, metin
> değildir)*
>
> "The system posts this notification on the main thread."

| Not | Değer |
|---|---|
| Kaynak | `interruptionnotification` JSON `primaryContentSections` |
| Anahtar adları | JSON `references` bloğunda birebir (`AVAudioSessionInterruptionTypeKey` · `AVAudioSessionInterruptionOptionKey` · `AVAudioSessionInterruptionReasonKey` · `AVAudioSessionInterruptionWasSuspendedKey`) |
| iOS 10 notu ve arka plan notu | `coreaudio-surucu.md` §5.4–§5.5'te tam metin |

---

## §4 ALSA · WASAPI eşleme matrisi (işaretli)

> Kural: CoreAudio sütunu bu turda JSON'dan birebir doğrulanmıştır. ALSA sütunu
> yalnız bu oturumda doğrulanmış kernel/alsa alıntılarına dayanır (`../alsa/`).
> WASAPI sütunu bu turda **hiçbir sayfa doğrulanmadı** → tüm hücreler
> `⚠️ VERIFICATION REQUIRED` (yüzey: `../wasapi/*.md`, yazma yasak).

| Kavram | CoreAudio (birebir kanıt) | ALSA (kanıt konumu) | WASAPI | Durum |
|---|---|---|---|---|
| Çağrı sözleşmesi | `AudioDeviceIOProc` her IO cycle'da çağrılır | `snd_pcm_ops` callback tablosu — `../alsa/alsa-surucu.md` §4 | ⚠️ VERIFICATION REQUIRED | eşleme **kanıtlanmadı** |
| Durum/hata kodu | dönüş "should always be 0" (hata kanalı belgede yok) | `-EPIPE` → `SNDRV_PCM_STATE_XRUN` — `../alsa/alsa-surucu.md` §6 | ⚠️ VERIFICATION REQUIRED | kanıtsız |
| Akış taşması | IOProc metninde XRUN karşılığı **geçmiyor** | XRUN durumu — `../alsa/alsa-surucu.md` §6 | ⚠️ VERIFICATION REQUIRED | CoreAudio tarafı VR |
| Periyot/döngü | "current IO cycle" (süre sayılmıyor) | `snd_pcm_period_elapsed` — `../alsa/alsa-surucu.md` §5 | ⚠️ VERIFICATION REQUIRED | süre eşikleri §6 |
| Atomiklik | belgede atomiklik metni yok → ⚠️ | "atomic by default" — `../alsa/alsa-surucu.md` §5.1 | ⚠️ VERIFICATION REQUIRED | CoreAudio tarafı VR |
| Kesme/uyandırma yolu | "waking the thread on which IO is being done" (§3.2) | kesme yolu — `../alsa/alsa-surucu.md` §5 | ⚠️ VERIFICATION REQUIRED | kanıtsız |
| Paylaşım modeli | "aggregate devices contain other AudioDevices" | dmix/dsnoop paylaşımı — `../alsa/alsa-anayasa.md` §6 | ⚠️ VERIFICATION REQUIRED | kanıtsız |
| Oturum/kesinti | `AVAudioSessionInterruptionNotification` (ana thread) | karşılık bu dalda **yok** (Linux oturum modeli ayrı konu) | ⚠️ VERIFICATION REQUIRED | platform farkı |
| Exclusive Lock karşılığı | belgede yok → ⚠️ | — | ⚠️ VERIFICATION REQUIRED | bkz. açık soru C6 (index.md) |

### §4.1 ALSA sütununun kanıt blokları (bu oturumda doğrulandı, birebir)

Aşağıdaki üç alıntı `../alsa/alsa-surucu.md` içinde, indirilen çekirdek
HTML'inden 2026-10-13 doğrulanmıştır; eşleme iddiaları bu alıntılara
**sadece bu kadarını** dayandırır:

> "When the return value is `-EPIPE`, PCM core treats that as a buffer XRUN, and
> changes the state to `SNDRV_PCM_STATE_XRUN` automatically."
> (`../alsa/alsa-surucu.md` §6.1)

> "… Then the PCM middle layer updates the position and calculates the available
> space, and wakes up the sleeping poll threads, etc. This callback is also
> atomic by default."
> (`../alsa/alsa-surucu.md` §5.1)

> "**.trigger** - with `SNDRV_PCM_TRIGGER_STOP` at XRUN or DRAINING state."
> (`../alsa/alsa-surucu.md` §4.3 — `snd_pcm_ops` dikkat notu)

| Eşleme kararı | Dayanak | Durum |
|---|---|---|
| IOProc ↔ `snd_pcm_ops` callback'leri | her ikisi de "çekirdek/donanım → sürücü callback" düzeni | **kanıtlanmadı** — yalnız kavramsal benzerlik → ⚠️ |
| IOProc dönüş 0 ↔ ALSA negatif errno | belgeler farklı dillerde hata kanalı tanımlıyor | ⚠️ VERIFICATION REQUIRED |
| XRUN ↔ kesinti bildirimi | **eş değiller** — XRUN akış taşması, kesinti oturum olayı | ayrıştırıldı (yanlış eşleşmeye izin yok) |

---

## §5 Anti-pattern'ler (belge gözlemi + L0)

| # | Anti-pattern | Sonuç | Kaynak sınıfı |
|---|---|---|---|
| 1 | giriş `AudioBufferList` içinde değişiklik | belge: "should never be modified" → veri bozulması | belge yasağı (birebir) |
| 2 | devre dışı akışta `mData == NULL` iken yazma/okuma | davranış belgelenmiş (NULL), koruma yok → çökme riski | belge + L0 savunması |
| 3 | sabit bayt/paket formatta `mDataByteSize` değiştirmek | belge: "should not be changed" | belge yasağı (birebir) |
| 4 | çıkışta belge dışı alanlara dokunmak | "should not other wise be modified" [sic] | belge yasağı (birebir) |
| 5 | IO callback içinde `new`/malloc/kilit | L0 yasağı — belgede **yazılmıyor**, bağlayıcı L0 | L0 kuralı |
| 6 | kesinti bildirimini ana thread dışında işlemeye çalışmak | belge: "posts this notification on the main thread" | belge + uygulama |
| 7 | iOS 10 askı bildirimini yok saymak | "necessarily delayed in time" → durum kaybı | belge notu |
| 8 | arka planda nonmixable oturumu açık bırakmak | sistem oturumu düşürür ("somewhat confusing notification") | belge önerisi |

---

## §6 Eşik tablosu — 512 örnek · 48 kHz (aritmetik)

| Büyüklük | Hesap | Değer | Kanıt sınıfı |
|---|---|---|---|
| Periyot | 512 ÷ 48000 | **10,666… ms** (≈10,67 ms) | sartname aritmetiği |
| Döngü frekansı | 48000 ÷ 512 | 93,75 cycle/s | aritmetik |
| Tek örnek | 1 ÷ 48000 | 20,833… µs | aritmetik |
| 1024 örnek (referans) | 1024 ÷ 48000 | 21,333… ms | aritmetik |
| 256 örnek (referans) | 256 ÷ 48000 | 5,333… ms | aritmetik |
| IOProc periyodunun belgede karşılığı | — | **yok** | ⚠️ cihaz üstünde ölçüm PLANNED |

> Ölçüm (L0 sartname madde 8): 512 tampon · 48 kHz · ölçüm PLANNED. Bu tabloda
> yalnız sartname değerlerinden türetilen aritmetik vardır; hiçbir cihaz
> performansı iddia edilmez.

### §6.1 Doğrulama protokolü (PLANNED — bu turda çalıştırılmadı)

| Adım | Ne ölçülecek | Durum |
|---|---|---|
| 1 | CoreAudio cihazının IO periyodu (arabellek/süre) okunacak | ⚠️ API bu turda doğrulanmadı (B1) |
| 2 | Gerçekleşen çağrı aralığı zaman damgası (param 2) üzerinden | PLANNED |
| 3 | 10,667 ms hedefi ile sapma | PLANNED |
| 4 | Sonuç: sartname 512·48k destekleniyor mu? | PLANNED |

> Bu protokol **çalıştırılmadı**; satırlar bir sonuç taşımaz, yalnız plan taşır.

---

## §7 Web kanıt matrisi (2026-10-13 · 13 URL · envanter kesişimi 0)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| O1 | `AudioObjectID = UInt32` + Discussion | https://developer.apple.com/documentation/coreaudio/audioobjectid | 2026-10-13 | ✅ 200 · JSON birebir |
| O2 | `AudioDeviceID = AudioObjectID` + sınıf ilişkisi + C typedef | https://developer.apple.com/documentation/coreaudio/audiodeviceid | 2026-10-13 | ✅ 200 · JSON birebir |
| O3 | IOProc imzası + 7 parametre + dönüş | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | 2026-10-13 | ✅ 200 · JSON birebir |
| O4 | kesinti bildirimi + sabit adı + main thread + iOS 10 | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification | 2026-10-13 | ✅ 200 · JSON birebir |
| O5 | `AVAudioUnitEffect` + 4 alt sınıf | https://developer.apple.com/documentation/avfaudio/avaudiouniteffect | 2026-10-13 | ✅ 200 · JSON birebir |
| O6 | `playback` kategori tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategoryplayback | 2026-10-13 | ✅ 200 · `<meta>` |
| O7 | `AVAudioSession.Mode` tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessionmode | 2026-10-13 | ✅ 200 · `<meta>` |
| O8 | `default` mod tanımı | https://developer.apple.com/documentation/avfaudio/avaudiosessionmodedefault | 2026-10-13 | ✅ 200 · `<meta>` |
| O9 | `AudioBuffer` tanımı | https://developer.apple.com/documentation/coreaudio/audiobuffer | 2026-10-13 | ✅ 200 · `<meta>` |
| O10 | `AudioFormatID` tanımı | https://developer.apple.com/documentation/coreaudio/audioformatid | 2026-10-13 | ✅ 200 · `<meta>` |
| O11 | `AudioFormatFlags` tanımı | https://developer.apple.com/documentation/coreaudio/audioformatflags | 2026-10-13 | ✅ 200 · `<meta>` |
| O12 | `kAudioFormatLinearPCM` tanımı | https://developer.apple.com/documentation/coreaudio/kaudioformatlinearpcm | 2026-10-13 | ✅ 200 · `<meta>` |
| O13 | Audio Services tanımı | https://developer.apple.com/documentation/audiotoolbox/audio_services | 2026-10-13 | ✅ 200 · `<meta>` |

**Çakışma kontrolü:** 13/13 URL repo URL envanteri (2103 satır, 2026-10-13) ile
**kesişim 0**.

---

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | CoreMusic CoreAudio hedefi var mı? (tarama 0 isabet) | UNKNOWN |
| Q2 | Platform hedefi: macOS mu, iOS mu, ikisi mi? | UNKNOWN |
| Q3 | Kategori kararı (`playback` vs `playAndRecord`)? | ⚠️ VERIFICATION REQUIRED |
| Q4 | `kAudioFormatLinearPCM` değeri hangi turda JSON'dan alınacak? | ⚠️ VERIFICATION REQUIRED |
| Q5 | WASAPI eşleme hücreleri hangi dosyadan/URL'den doğrulanacak? | ⚠️ VERIFICATION REQUIRED |
| Q6 | 512·48k CoreAudio cihazında doğrulandı mı (IOProc periyodu)? | ⚠️ VERIFICATION REQUIRED |
| Q7 | 8 sayfanın JSON'u neden 404 — path sürümü farklı mı? | ⚠️ VERIFICATION REQUIRED |

---

## §9 Alıntı bütünlüğü

| Kural | Bu dosyadaki uygulama |
|---|---|
| Repo satırları | PowerShell 0-tabanlı dizi indeksi (`Select-Object -Index (N-1)`) ile alındı — §1.2–§1.4 |
| Kaynak mojibake | AIEngine docblock (satır 286–287) mojibake → alıntı dışı · MusicStreamHandler `fail()` satırı alıntı dışı |
| JSON apostrof | Apple JSON'unu bozuk `ǽ�?�ts` çıktısı düz `'` ile yazıldı — metin değişmedi |
| Bağlantı başlığı | `handling-audio-interruptions` başlığı JSON'da yok → ⟨…⟩ yer tutucusu + ⚠️ (§3.1) |
| Eşleme iddiası | kanıtlanmayan her hücre ⚠️ (§4) · hazır cevap yazılmadı |
| Placeholder yasağı | `TBD` · boş hücre · `{...}` yer tutucu kullanılmadı |

---

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `coreaudio-anayasa.md` | Kimlik, nesne ağacı ve oturum modeli | Kademe-4 |
| mimari | `coreaudio-surucu.md` | IO döngüsü, zaman damgası ve tampon sözleşmesi | Kademe-4 |
| kod | `coreaudio-ornekler.md` | Tip beyanları, eşleme ve repo kanıtı (bu dosya) | Kademe-4 |

---

## §PHP CoreAudio × PHP (2026-10-14)

PHP'nin CoreAudio'ya (`.dylib` FFI dahil) doğrudan erişimi **bu turda doğrulanamadı**
(⚠️ VERIFICATION REQUIRED). Pratik yol: `shell_exec('afplay file.mp3')` — `afplay`
macOS yerleşik CLI'dır ancak **resmi Apple dokümanı bulunamadı** (2026-10-14 ·
yalnız üçüncü taraf). iOS'ta PHP runtime yok → sunucu PHP + tarayıcı HTML5 audio
(§PHP yolları kök §25.1 yol 2 · §25.4).

---

**Geniş dil seti — CoreAudio × dil (kök §26):** C zaten native (§11/§19 IOProc) ·
Rust `cpal` macOS/iOS/tvOS backend'i **CoreAudio** (README matrisi) · Go `oto`
CoreAudio backend'i · Python `sounddevice` (PortAudio → CoreAudio) · C#/Node/PHP'de
doğrudan CoreAudio **yok** — süreç/tarayıcı yolu (kök §26.4–§26.6).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
