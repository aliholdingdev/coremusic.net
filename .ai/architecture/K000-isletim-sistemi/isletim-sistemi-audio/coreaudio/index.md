---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/coreaudio
node_type: subdomain
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
children:
  - K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-anayasa
  - K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-surucu
  - K000-isletim-sistemi/isletim-sistemi-audio/coreaudio/coreaudio-ornekler
depends_on: []
evidence:
  - https://developer.apple.com/documentation/coreaudio/audioobjectid
  - https://developer.apple.com/documentation/coreaudio/audiodeviceioproc
  - https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification
validation:
  - "2026-10-13 · 13 Apple URL HTTP 200 (HEAD/GET) · envanter kesişimi 0 · 5 sayfanın JSON uç noktası tip beyanını verdi"
  - "repo taraması: `CoreAudio|AudioDevice|AVAudioSession` deseni 0 isabet → kod YOK"
open_questions:
  - "CoreMusic reposunda CoreAudio kodu YOK → UNKNOWN (bkz. `coreaudio-ornekler.md` §1)"
  - "`kAudioFormatLinearPCM` vb. sabitlerin JSON uç noktası bu turda 404 — yalnız HTML `<meta description>` alındı → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# coreaudio — Deep-Dal İndeksi (Kademe-4)

**ELI15:** CoreAudio, Apple'ın (macOS/iOS) **ses donanımıyla konuşma katmanıdır** —
her ses nesnesi (cihaz · akış · özellik) 32-bit bir kimlik (`AudioObjectID`) ile
adlandırılır, cihaz periyodik olarak `AudioDeviceIOProc` callback'ini çağırır ve
uygulama o callback'e giriş/çıkış tamponunu koyar; oturum politikası (kesinti ·
kategori · mod) AVAudioSession katmanından yönetilir.

| Öğe | Değer |
|---|---|
| parent_id | `K000-isletim-sistemi/isletim-sistemi-audio` |
| Kademe | 4 · deep-dal |
| Durum | `draft` |
| Platform | CoreAudio typealias'ları ([C10]): macOS · Mac Catalyst · oturum katmanı ([C13]): iOS · iPadOS · Mac Catalyst · tvOS · visionOS · watchOS — JSON `metadata.platforms` birebir |
| Repo durumu | CoreMusic'te CoreAudio kodu YOK → UNKNOWN (§4 Q1) |

## §1 Kanıtlanmış çerçeve

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `AudioObjectID` bir `UInt32`'dir ("A UInt32 that provides a handle on a specific AudioObject.") | https://developer.apple.com/documentation/coreaudio/audioobjectid | 2026-10-13 | ✅ 200 · JSON `typealias AudioObjectID = UInt32` |
| `AudioDeviceIOProc` tam callback imzası ve 7 parametre dokümanı JSON'da | https://developer.apple.com/documentation/coreaudio/audiodeviceioproc | 2026-10-13 | ✅ 200 · JSON birebir alındı |
| Kesinti bildirimi `class let interruptionNotification: NSNotification.Name` + iOS 10 davranışı metni | https://developer.apple.com/documentation/avfaudio/avaudiosession/interruptionnotification | 2026-10-13 | ✅ 200 · JSON birebir alındı |
| `AudioBuffer` "A structure that holds a buffer of audio data." | https://developer.apple.com/documentation/coreaudio/audiobuffer | 2026-10-13 | ✅ 200 · `<meta description>` |
| Audio Services "Play short sounds or trigger a vibration effect…" | https://developer.apple.com/documentation/audiotoolbox/audio_services | 2026-10-13 | ✅ 200 · `<meta description>` |

## §2 Bu deep-dalın dosya envanteri (disk, 2026-10-13)

| # | Dosya | İçerik yüzeyi | Bu turda |
|---|---|---|---|
| 1 | `index.md` (bu dosya) | deep-dal indeksi | **oluşturuldu** |
| 2 | `coreaudio-anayasa.md` | Kimlik · nesne ağacı · IOProc · oturum modeli | **oluşturuldu** |
| 3 | `coreaudio-surucu.md` | IO döngüsü · zaman damgası · tampon sözleşmesi · kesinti | **oluşturuldu** |
| 4 | `coreaudio-ornekler.md` | Tip beyanları · eşleme matrisi · repo kanıtı | **oluşturuldu** |

> Kural: bu 4 dosyanın yüzeyi bu deep-dalın **kendi beyazlistesidir**; kök
> `isletim-sistemi-audio/*.md`, `wasapi/`, `alsa/`, `aaudio/` dosyalarına bu
> turda **dokunulmadı** (başka ajanların yüzeyi).

## §3 Bu deep-dalın üst sınırları

| Dışında olan | Nereye gider |
|---|---|
| Windows ses yolu (WASAPI · exclusive/shared) | `../wasapi/` |
| Linux ses yolu (ALSA · `snd_pcm_ops`) | `../alsa/` |
| AVFoundation (oynatıcı seviyesi `AVAudioPlayer` vb.) | bu dal §3 · kapsam dışı — ⚠️ VERIFICATION REQUIRED |
| DSP zinciri, EQ/reverb algoritması (kod) | K004-dsp |
| Ses motorunun C++ kodu (Neva Engine) | K003-audio |
| DAC/ADC devresi · kulaklık amplifikatörü | K001-donanım · `electroncis` (Q1) |
| Web'de çalma UI'ı | frontend katmanı |

## §4 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| C1 | CoreMusic/CoreAudio hedefi var mı (repo taraması 0 isabet)? | UNKNOWN |
| C2 | macOS mu, iOS mu, ikisi mi? (platform hedefi) | ⚠️ VERIFICATION REQUIRED |
| C3 | `AVAudioSession` kategori kararı (`playback` mi, `playAndRecord` mı)? | ⚠️ VERIFICATION REQUIRED |
| C4 | 512 örnek · 48 kHz hedefi (sartname) CoreAudio cihazında doğrulandı mı? | ⚠️ VERIFICATION REQUIRED |
| C5 | `kAudioFormatLinearPCM` sabitinin JSON kanıtı hangi turda alınacak? | ⚠️ VERIFICATION REQUIRED |
| C6 | ASIO Exclusive Lock'a karşılık CoreAudio'da hangi politika seçilecek? | ⚠️ VERIFICATION REQUIRED |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-4 |
| mimari | `coreaudio-anayasa.md` | Kimlik, nesne ağacı ve oturum modeli | Kademe-4 |
| mimari | `coreaudio-surucu.md` | IO döngüsü, zaman damgası ve tampon sözleşmesi | Kademe-4 |
| kod | `coreaudio-ornekler.md` | Tip beyanları, eşleme ve repo kanıtı | Kademe-4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
