---
title: "AIMP v5.40.2722 — Derin Mimari Dump (Reverse Engineering, non-decompile)"
type: reverse-engineering
target: "C:\Program Files\AIMP (AIMP v5.40.2722)"
method: "PE string/module-scan + version-info + config/envanter (DECOMPILE YOK)"
date: 2026-10-19
status: evidence-backed
---

# AIMP v5.40.2722 — Mimari Dump

> **Yöntem notu:** `dumpbin` bu makinede YOK (`Get-Command dumpbin` → boş). Bu nedenle
> import/export kanıtları **string-seviyesi tarama** ile toplandı (ASCII + UTF-16LE regex:
> `[A-Za-z_][A-Za-z0-9_]{2,60}` ve modül adı `[A-Za-z0-9_\-]{2,40}\.(dll|exe)`).
> **Dürüstlük sınırı:** PE import tablosu (IAT) birebir parse edilmedi — bir DLL adı string'i
> "import ediliyor" demek için güçlü (ve burada tutarlı) kanıttır ama %100 IAT izi değildir.
> Kesin IAT istenirse `dumpbin /imports` veya `llvm-readobj --coff-imports` gerekir → **UNKNOWN (IAT kesin)**.
> AIMP kapalı kaynak; DECOMPILE YOK. Tüm iddialar disk kanıtlıdır.

## §1 Genel envanter

Kök: `C:\Program Files\AIMP`

### §1.1 Ana çalıştırılabilirler ve motor DLL'leri

| Dosya | Boyut (byte) | VersionInfo | Rol (kanıt: desc/kanıtı) |
|---|---:|---|---|
| `AIMP.exe` | 7.138.752 | 5.4.0.2722, Company=`Artem Izmaylov`, Desc=`AIMP` | Ana player + UI (Delphi/Embarcadero RTL; VST editör formu, Winamp-uyumlu plugin yükleyici sembolleri içinde) |
| `AIMP.Runtime.dll` | 15.701.952 | 5.4.0.2722, Desc=`AIMP Runtime Library` | Uygulama çekirdeği: Windows API yüzeyi (advapi32/comctl32/gdiplus/ole/uxtheme/wininet/ws2_32/wtsapi32...), sqlite3.dll'i buradan yükler |
| `AIMP.Shared.dll` | 4.641.728 | 5.4.0.2722, Desc=`AIMP Shared Library` | Ortak katman: ses motoru (ASO namespace), plugin API (IID_IAIMP* 199 adet), codec yükleme listesi, encoder XML işleyici |
| `bass.dll` | 165.824 | **2.4.17**, Company=`Un4seen Developments`, Desc=`BASS` | Çalma motoru (BASS 2.4.17) |
| `AIMPac.exe` | 1.335.232 | 5.4.0.2722, Desc=`AIMP Audio Converter` | Batch transcoder (CLI encoder'ları XML ile bağlar) |
| `AIMPate.exe` | 1.544.128 | 5.4.0.2722, Desc=`AIMP Advanced Tag Editor` | Bağımsız tag editor |
| `Elevator.exe` | 198.080 | 5.4.0.2722, Desc=`AIMP Access Rights Elevator` | UAC/klasör-yazma izin yardimcisi (`Elevator.ini` kullanıcıda) |
| `Uninstall.exe` | 21.974.976 | — | Kaldırıcı (NSIS benzeri; boyutu kurulumun 2/3'ü — tüm dosyaları paket içinde taşır) |

### §1.2 Codec / DSP yardımcı DLL'leri (kök)

| Dosya | Boyut | VersionInfo | Rol |
|---|---:|---|---|
| `libFLAC.dll` | 530.368 | (boş) | FLAC encode/decode — iç string: `libFLAC.dll` |
| `libLAME.dll` | 1.089.984 | 3.100, Desc=`MP3 Encoder.` | MP3 encode — **iç import string: `libmp3lame.DLL` + `libmUI.dll`** (yani bu bir LAME sarmalayıcısı/shim) |
| `libMAC.dll` | 690.112 | **11.89**, Company=`Matthew T. Ashland` | Monkey's Audio (APE) |
| `libsoxr.dll` | 296.384 | (boş) | **SoX resampler** — AIMP.Shared.dll UTF-16 string'leri: `soxr_create`, `soxr_delete`, `soxr_process`, `soxr_quality_spec` |
| `libVorbisEn.dll` | 889.792 | (boş) | Vorbis/OGG encoder — iç string: `aimp_libvorbis.dll` |
| `libWavPack.dll` | 261.568 | 5.9.0.0, Company=`Conifer Software` | WavPack (history: v5.9 güncellemesi 5.40.2722'de) |
| `sqlite3.dll` | 1.662.912 | 3.10.2 | Müzik kütüphanesi/çalma listesi deposu (AIMP.Runtime.dll import listesinde) |

### §1.3 Yardımcı dosyalar

| Dosya | Boyut | Kanıt |
|---|---:|---|
| `AIMP.ini` | 238 | `[System] ID={A44DE1DD-277D-49F4-98D2-5558F390E570} DefaultLang=turkish.lng UserMode=1 Version=5400 Build=2722` |
| `AIMP.visualelementsmanifest.xml` | 320 | Win8/10 Start-Menu manifest: `BackgroundColor="#FF9B19"`, 150x150 + 70x70 logo (Assets altinda) |
| `AIMPac.visualelementsmanifest.xml` | 314 | Aynı desen (converter icin) |
| `AIMPate.visualelementsmanifest.xml` | 357 | `DisplayName="AIMP Advanced Tag Editor"` |
| `history.txt` | 250.323 | Sürüm günlüğü (bkz §1.6) |
| `license.rtf` | 18.143 | Lisans |
| `AIMP.url` | 74 | `URL=http://www.aimp.ru/` |

### §1.4 Dizin yapıları

- **`Skins\`** (2 dosya): `Bliss 4K.acs5` (488.738 B), `Pandemic.acs5` (478.848 B) — `.acs5` = AIMP 5 skin formatı
- **`Langs\`**: **47** `.lng` dosyası (arabic..uzbek; ~95-120 KB her biri). Örnek: `arabic.lng` 95.986 B
- **`Assets\`** (6 PNG): aimp/ac/ate 150x150 + 70x70 logo'lar
- **`Icons\`**: `aimp_icons.dll` (1.031.104 B) — ikon kaynağı DLL olarak
- **`Help\`**: `AIMP-en.chm` (293.321 B), `AIMP-ru.chm` (312.160 B)
- **`System\`** (4 DLL + 3 veri) — §1.7

### §1.5 Kullanıcı verisi: `%APPDATA%\AIMP` (registry YOK, her şey dosya-tabanlı)

**Registry bulgusu:** `HKCU:\Software\AIMP` → **YOK** (kanıt: `Test-Path 'HKCU:\Software\AIMP'` = false).
Tüm kalıcılık `%APPDATA%\AIMP\AIMP.ini` (372 satır) üzerinden. Dizin içeriği:
`AudioLibrary\`, `MyClouds\`, `PLS\`, `Skins\`, `AIMP-ContextMenu.ini`, `AIMP.ini`, `AIMP.ini.bak`,
`aimp_scheduler.xml`, `aimp_scrobbler.log`, `aimp_scrobbler.xml`, `AlbumArts.Cache.db`,
`CDDB.db`, `Elevator.ini`, `EQPresetsLibrary.ini`.

Bu, **taşınabilir/scratch-friendly mimari** demektir: config = plain INI + XML + SQLite.

### §1.6 history.txt — son 8 sürüm (kanıt: `Get-Content -TotalCount 120`)

| Sürüm | Tarih | Codec/motor notları |
|---|---|---|
| **5.40.2722** | 01.07.2026 | WavPack → **v5.9** (+32-bit float); WavPack converter'a 32f; **VST: sample-rate değişince plugin asılı kalıyor** düzeltildi; BASS_AAC "bazı dosyalar sonuna çalmıyor" düzeltildi |
| 5.40.2716 | 21.04.2026 | Sound engine: pause-between-tracks regresyon düzeltmesi |
| 5.40.2715 | 11.04.2026 | **Preload: sonraki parça yarısında ve bitişen en az 1 dk önce preload başlar**; kısa parça geçiş hatası |
| 5.40.2709 | 07.03.2026 | WebLyrics provider listesi; MP3 converter hedef sample rate; **DirectSound cihaz değiştirme hatası**; **VST sample-rate yok sayma + transcode bellek bozulması** |
| 5.40.2703 | 24.12.2025 | **BASS libraries güncellendi**; **Monkey's Audio → v11.89** |
| 5.40.2700 | 25.11.2025 | OPUS replaygain (rsgain); **resampler aktifken spontaneous stuttering** düzeltmesi; cihaz değiştirince parça atlama; performans |
| 5.40.2696 | 01.10.2025 | **Skin engine: çok-threadlı render asenkron erişim sorunları** düzeltmesi |
| 5.40.2691 | 15.08.2025 | **BASS libraries güncellendi**; **Monkey's Audio → v11.36**; **Wine 10 uyumluluğu**; fade-out sonu tıklaması |

### §1.7 `System\` dizini — version info

| Dosya | Boyut | FileVersion | FileDescription |
|---|---:|---|---|
| `aimp_mediakeys.dll` | 132.032 | 5.4.0.2722 | Media Keys Helper |
| `aimp_menu32.dll` | 899.288 | 4.0.0.0 | Context Menu Extension (32-bit) |
| `aimp_menu64.dll` | 1.403.608 | 4.0.0.0 | Context Menu Extension (64-bit) |
| `aimp_mousehook.dll` | 129.984 | 5.4.0.2722 | Mouse Helper (mouse-hook tabanlı kontrol — skin engine ile uyumlu) |
| `Catalogs-AlbumArt.ini` | 9.594 | — | Album-art katalog listesi (metaveri) |
| `Catalogs-Radio.ini` | 4.786 | — | Internet radyo katalog listesi |
| `Complete.mp3` | 15.876 | — | Bitiş jingle'ı (düz MP3 varlık) |

## §2 Mimari çıkarım — katman diyagramı

```
┌────────────────────────────────────────────────────────────────────────────┐
│ UI KATMANI — AIMP.exe (Delphi/Embarcadero RTL)                             │
│  · Aimp::Ui::* (ör. `Aimp::Ui::Equalizerdisplay::TAIMPEqualizerDi...`)     │
│  · Skin engine: .acs5 (Skin* string kümeleri: SkinFrames/SkinLayout/...)    │
│  · Kendi widget seti: IID_IAIMPUI* (Form/Button/TreeList/... 60+ iface)    │
│  · VST editör formu: TAIMPVSTEDITORFORM, "VSTPluginMain" (UTF-16 kanıt)    │
├────────────────────────────────────────────────────────────────────────────┤
│ PLUGIN API / SERVIS KATMANI — AIMP.Shared.dll                              │
│  · Export: `AIMPPluginGetHeader`, `ampGetGeneralPurposePlugin`              │
│  · 199 IID_IAIMP* arayüzü (Service/Extension/UI) — COM-benzeri QueryInterface│
│  · Winamp uyumluluğu: `winampGetGeneralPurposePlugin`, `winampDSPGetHeader2`│
│  · Decoder extension: `AIMPCustomPlugin`, `IID_IAIMPExtensionAudioDecoder`  │
├────────────────────────────────────────────────────────────────────────────┤
│ SES MOTORU (ASO = AIMP Sound Object) — AIMP.Shared.dll, ~1527 sembol       │
│  · Aso::Devices::{Wasapi, Directsound, Asio}  — TASOASIODevice / Manager    │
│  · Aso::Play (441 sembol) · Aso::Mix · Aso::Analysis (BPM, WAV, Sinc, RMS)  │
│  · Aso::Processors.Equalizer (IID_IAIMPEqualizerBands/Preset)               │
│  · Resampler: TASOResamplingFilter → libsoxr.dll (soxr_*)                   │
│  · Config namespace: AIMPSoundOut{.DSP,.Fading.*, .MixingPresets, .Devices} │
├────────────────────────────────────────────────────────────────────────────┤
│ ÇALMA MOTORU — bass.dll 2.4.17 (Un4seen)                                   │
│  · 109 BASS_* string: StreamCreateFile/User/URL, ChannelSetSync, SetFX, ... │
│  · BASS_FX* (SetParameters/Bypass/Priority) — FX chain cagrilari             │
│  · BASS record* grubu da export'ta (mikrofon arayuzu hazir)                 │
├────────────────────────────────────────────────────────────────────────────┤
│ DECODER PLUGINS (Plugins\ — 15 AIMP plugin + 13 BASS add-on + 3 codec lib)  │
│  · bass_aac/ac3/alac/ape/flac/hls/midi/mpc/opus/spx/tta/wma/wv              │
│  · aimp_inputFFmpeg → FFmpeg 4.x fork (avcodec-58/avformat-58/avutil-56)    │
│  · aimp_sacd → libsacd.dll (DSD/SACD) · OptimFROG.dll · tak_deco_lib.dll    │
├────────────────────────────────────────────────────────────────────────────┤
│ ENCODERLAR (Plugins\enc_* — harici CLI sarmalayıcı, XML sözleşmeli)         │
│  · enc_mpc → mpcenc.exe · enc_opus → opusenc.exe                            │
│  · enc_tak → Takc.exe · enc_tta → tta.exe                                   │
│  · Kök kütüphane encoderları: libLAME/libFLAC/libVorbisEn/libWavPack/libMAC │
│  · BASS_WMA_Encode* (WMA encode BASS icinde, CLI degil)                     │
├────────────────────────────────────────────────────────────────────────────┤
│ YARDIMCI PROSESLER                                                         │
│  · AIMPac.exe (Converter) · AIMPate.exe (Tag Editor)                        │
│  · Elevator.exe (UAC) · aimp_menu{32,64}.dll (Explorer context menu)        │
│  · aimp_mediakeys.dll · aimp_mousehook.dll                                  │
└────────────────────────────────────────────────────────────────────────────┘
Veri: %APPDATA%\AIMP (INI/XML/SQLite) — registry DEGIL · Langs: 47 lng · Skins: .acs5
```

**Derin mimari bulgu:** AIMP 5, **Delphi (Embarcadero) RTL** üzerine kuruludur.
Kanıt: mangled C++ sembolleri `System::TObject`, `System::UnicodeString`,
`System::DelphiInterface<...>`, `Winapi::Wincodec::WICBitmapDitherType`,
`System::Variants::_VarCmpEQ` — bunlar Embarcadero RTL namespace'leridir (MSVC/Itanium ABI mangling `E` son eki ile).

## §3 Ses yolu çıkarımı

### §3.1 Output API'leri — imza kanıtları

| API | Kanıt | Dosya |
|---|---|---|
| **WASAPI** | `_ZN3Aso7Devices6Wasapi14initializationEv`, `...12FinalizationEv`, `IID_IAudioClient`, `IID_IAudioRenderClient`, `IID_IMMDeviceEnumerator`, `CLSID_MMDeviceEnumerator`, `GetDeviceID(IMMDevice)`, `GetDeviceName(IMMDevice)`, `IsDefaultDevice(IMMDeviceEnumerator)` | `AIMP.exe` + `AIMP.Shared.dll` (ASCII) |
| **ASIO** | `_ZN3Aso7Devices4Asio...` — **45+ sembol**: `TASOASIODevice::DoOpen/DoPlay/DoClose/DoSwitchBuffer/DoReinitialize/DoGetBuffersLength`, `LoadDirect/LoadSwapped/LoadDirect24/LoadDirect32/LoadDirect32Aligned/...`, `TASOASIODeviceSettingsDialog`, `IASOASIODriver` | `AIMP.Shared.dll` (ASCII) |
| **DirectSound** | `_ZN3Aso7Devices11Directsound14initializationEv`, `CreateDirectSound` (UTF-16), `BASS_GetDSoundObject` (bass.dll export) | `AIMP.exe`, `AIMP.Shared.dll`, `bass.dll` |
| **WaveOut** | **KANIT YOK** — taramada geçmiyor (UNKNOWN) | — |

Aktif output (kullanıcı configi, kanıt `%APPDATA%\AIMP\AIMP.ini`):
```
[AIMPSoundOut]
DeviceName=WASAPI: Windows Default
DeviceBitDepth=4
DeviceChannels=1
DeviceFreq=48000
```
Aynı anahtarlar `[BASS Decoder]` bölümünde de tekrar ediyor → cihaz seçimi hem BASSDecode hem SoundOut katmanında saklanıyor.

### §3.2 Ses işleme zinciri kanıtları

- **Resampler — libsoxr (SoX):** `soxr_create`, `soxr_delete`, `soxr_process`, `soxr_quality_spec`
  (UTF-16, `AIMP.Shared.dll`) + kökte `libsoxr.dll` 296.384 B. Ayar anahtarları:
  `Resampler`, `ResamplingQuality`, `TASOResamplingFilter` (ASCII, AIMP.Shared).
- **EQ:** `ASO.Processors.Equalizer`, `IID_IAIMPEqualizerBands`, `IID_IAIMPEqualizerPreset`,
  `IID_IAIMPServicePlayerEqualizer{,Presets}`, `AIMP.UI.EqualizerDisplay` (AIMP.Shared ASCII).
  Kullanıcıda: `EQPresetsLibrary.ini` (`LibVersion=2722`, preset başına `Band0..Band19` → **20 bant**),
  `FFTEqualizer=0`, `FFTEqualizer.Bands=<hex-dizgi>`, `TrueBass=0`, `AutoLoadEqualizerPresets=0`.
- **VST2:** `VSTPluginMain` (UTF-16, hem AIMP.exe hem AIMP.Shared.dll),
  `TAIMPVSTEDITORFORM` (AIMP.exe), `"VST"` ve `"VST2"` string'leri.
  History'de VST sample-rate/memory düzeltmeleri (2709, 2722) → canlı, desteklenen yol.
- **Winamp DSP/GEN uyumluluğu:** `winampDSPGetHeader2`, `winampGetGeneralPurposePlugin`
  (UTF-16, AIMP.exe + AIMP.Shared.dll) → Winamp 2.x DSP pluginlerini de yükleyebiliyor.
- **Volume/fade/pitch:** `SmoothVolumeChange`, `FadingEnabled`, `PlaybackPitch`,
  `PlaybackSpeed=100`, `PlaybackTempo=100`, `SkipSilenceAmplifyThreshold=-60`,
  `LoudnessCompensation`, `PreampForCalculatedValues/PredefinedValues`, `ReplayGainDefault`.
- **ReplayGain:** tam string seti: `replaygain_track_gain/_peak`, `replaygain_album_gain/_peak` +
  `ReplayGainDecodeString`, `ReplayGainTrackGainToString`, `TMusePackReplayGainHelper`.
- **FX chain (BASS):** `BASS_ChannelSetFX`, `BASS_FXSetParameters/SetBypass/SetPriority/Reset/Free`,
  `BASS_ChannelSetDSP/SetDSPEx/RemoveDSP` (bass.dll 109-string listesi).
- **Dither:** converter fixi history 2722'de ("dither does not switched off ... 24-to-24-bit").
  Binary'de `dither` string'i **sadece WIC bitmap** bağlamında geçti (Winapi::Wincodec) → dither
  mantığının string'i yok, muhtemelen tablo/sayısal işlem → **DITHER IMPLEMENTASYON KANITI: UNKNOWN**.
- **Gapless/preload:** history 2715: "preloading next track starts halfway through the track and
  at least a minute before the end" + `BASS_ChannelSetSync`, `BASS_ChannelSlideAttribute` (gapless
  altyapısı BASS sync/slide ile kuruluyor).

## §4 Plugin API sözleşmeleri

**Giriş noktaları (export kanıtları):**

| Sembol | Hangi dosyada | Anlam |
|---|---|---|
| `AIMPPluginGetHeader` | `AIMP.Shared.dll` (UTF-16), `aimp_inputFFmpeg.dll`, `aimp_cdda.dll` (ASCII) | AIMP native plugin girişi — header struct döndüren tek export |
| `ampGetGeneralPurposePlugin` | `AIMP.Shared.dll` (UTF-16) | AIMP'ye özgü GEN-plugin girişi ("amp" = AIMP Plugin) |
| `AIMPCustomPlugin` | `aimp_inputFFmpeg.dll`, `aimp_cdda.dll` (ASCII) | Custom decoder plugin işareti |
| `winampGetGeneralPurposePlugin` | `AIMP.exe`, `AIMP.Shared.dll` (UTF-16) | Winamp GEN uyumluluğu |
| `winampDSPGetHeader2` | `AIMP.exe`, `AIMP.Shared.dll` (UTF-16) | Winamp DSP 2.x uyumluluğu |
| `VSTPluginMain` | `AIMP.exe`, `AIMP.Shared.dll` (UTF-16) | VST2 plugin girişi |
| `BASSplugin` | `bass_flac.dll`, `bass_opus.dll` (ASCII) | BASS add-on kayıt işareti |
| `IAIMPServiceConfig` | `AIMP.Shared.dll` | COM-interface ismi (plugin config erişimi) |

**Arayüz yüzeyi (199 `IID_IAIMP*` — AIMP.Shared.dll ASCII, seçme):**
Core: `IAIMPCore`, `IAIMPConfig`, `IAIMPPropertyList{,2}`, `IAIMPString`, `IAIMPStream`, `IAIMPTask{,Owner,Priority}`.
Decoder: `IAIMPExtensionAudioDecoder{,Old,Priority}`, `IAIMPAudioDecoder{,Listener,Notifications,BufferingProgressProgress}` (buffering: `IAIMPAudioDecoderBufferingProgress`).
Servisler (39): `IAIMPServicePlayer{,2}`, `...PlaylistManager{,2}`, `...AudioDecoders`, `...FileInfo`, `...FileTagEditor`, `...Lyrics`, `...FindTagsOnline`, `...HTTPClient{,2,3}`, `...AlbumArt{,Cache}`, `...SkinsManager`, `...Visualizations`, `...Waveform`, `...PlaybackQueue{,2}`, `...Threads`, `...MessageDispatcher`, `...MUI`, `...OptionsDialog`, `...MenuManager`, `...ActionManager`, `...FileSystems`, `...FileStreaming`, `...VersionInfo`, `...Shutdown`.
Extensions: `...ExtensionAlbumArtProvider{,2,3}`, `...AlbumArtCatalog{,2}`, `...CustomVisualization`, `...EmbeddedVisualization`, `...FileFormat`, `...FileExpander`, `...FileInfoProvider{,Ex}`, `...FileSystem`, `...LyricsProvider`, `...TagsProvider`, `...WaveformProvider`, `...PlaybackQueue{,2}`, `...PlayerHook`, `...PlaylistPreimageFactory` (+Provider/Data/Listener), `...ExternalSettingsDialog`, `...MessageHook`, `...PlaylistListener{,2}`.
UI (60+): `IAIMPUIForm{,Events,Events2,Events3}`, `...UIButton`, `...UICheckBox`, `...UIComboBox`, `...UITreeList{,...}`, `...UIPageControl`, `...UIScrollBox`, `...UIProgressDialog`, `...UITrayIcon`, ... (tam liste §6.3).

**BASS add-on sözleşmesi (decode):** her `bass_*.dll` kendi `_StreamCreateFile{,User,URL}` fonksiyonunu export eder
(ör. `BASS_FLAC_StreamCreateFile`, `BASS_OPUS_StreamCreate`, `BASS_WV_StreamCreateFileUserEx`,
`BASS_MP4_StreamCreateFile`, `BASS_MIDI_StreamCreateFile` + `BASS_MIDI_Font*`).
AIMP bunları `BASS_PluginLoad/PluginEnable/PluginGetInfo` ile etkinleştiriyor (bass.dll string'leri).

**Encoder sözleşmesi:** `enc_*\enc_*.xml` — şema:
`<encoders generator="AIMP" version="1000"><encoder Name App><description><inputformats type="autogen"><autogen SampleRates SampleFormats Channels/><property name="Ext|IgnoreLength|ID3|RequiresLength"/><presets><preset Name cmdparams default="1"/>` —
`cmdparams` içinde `%output%`, `%bitrate%`, `%mode%` placeholder'ları. Yeni encoder eklemek XML + exe = yeterli (kod değişikliği yok).

**Codec plugin yükleme sırası:** `Plugins\bass_ac3\loading_order` = `$7FFFFFFF` (9 bayt) →
BASS add-on'ları için yükleme-sırası mekanizması var (diğer bass_* klasörlerinde dosya yok → varsayılan sıralama).

## §5 Öğrenilecek dersler — "Neva Player bunu nasıl farklı yapmalı"

1. **Config = registry değil, plain text.** AIMP `HKCU\Software\AIMP`'yi **hiç kullanmıyor** (kanıt §1.5);
   tek gerçek dosya `%APPDATA%\AIMP\AIMP.ini` (372 satır) + yan XML/SQLite. → Neva: config'i
   JSON/TOML + tek dizin; registry yok, portable-mode ilk gün mümkün.
2. **Encoder'ları koda gömmek yerine XML sözleşmesi + harici CLI.** `enc_*.xml` + `%output%/%bitrate%`
   placeholder (§4) sayesinde MPC/OPUS/TAK/TTA codec'leri eklenebiliyor → Neva: encoder manifest'i
   (JSON schema) + out-of-proc çalıştırıcı; crash codec'i değil, yalnız işi öldürür.
3. **Decoder add-on'larını tek bir standart API'ye bağla (BASS modeli).** Her codec kendi
   `*_StreamCreateFile`'unu export eder, motor `PluginLoad` ile keşfeder → Neva: decode backend'i
   arayüzünü sabitle (open/read/seek/properties), codec'leri runtime-load et.
4. **Output katmanını 3'lü yap ama WASAPI'yi birincil yap:** AIMP ASIO'ya 45 sembolle en derin
   yatırımı yapıyor (LoadDirect32Aligned/Swapped24...), DirectSound'a en azını (init/finalization).
   Yine de kullanıcıda aktif olan `WASAPI: Windows Default`. → Neva: WASAPI shared+exclusive birincil,
   ASIO isteğe bağlı plugin, DirectSound legacy.
5. **Resampler'ı değiştirilebilir yap (TASOResamplingFilter → soxr).** AIMP sabit soxr kullanıyor ama
   ayarı `ResamplingQuality` ile açığa çıkarıyor; history 2700'de resampler stuttering düzeltmesi
   gösteriyor ki bu yol hata kaynağı. → Neva: resampler'ı sözleşmeli yap (kendi implementasyonun
   yanında soxr swap-in), kaliteyi user-chooseable + bypass.
6. **Preload stratejisini baştan tanımla.** AIMP 2715'e kadar "son 1 dk" heuristik'iyle takılıp
   kısa parçalarda hata yaptı. → Neva: gapless pipeline'ı (decode-ahead + crossfade-fence) ilk günde
   tasarla; heuristic patch değil.
7. **Kendi UI widget seti tut, ama plugin'e UI enjeksiyonu ver.** AIMP 60+ `IAIMPUI*` arayüzü +
   `IAIMPExternalSettingsDialog` ile plugin'lerin options sayfası eklemesine izin veriyor (sadece
   skin'i değil). → Neva: bileşen kütüphanesi çekirdeğe gömülü + plugin option-frame girişi.
8. **Version-info disiplini:** AIMP'ın kendi DLL'lerinde `FileVersion=5.4.0.2722` var, üçüncü taraf
   libFLAC/libsoxr/libVorbisEn'de **boş** (§1.2). → Neva: tüm binary'lerde `ProductVersion` zorunlu
   (CI gate); lisans envanteri otomatik üretilir.
9. **Kritik servisleri ayrı binary'e ayır (Elevator).** Yazma izni gerektiren işler `Elevator.exe`'e
   taşınmış (tek sorumluluk, UAC izole). → Neva: DB/library yazma servisini ayrı süreç + net IPC
   sözleşmesi yap; ana player çökmesin.
10. **Tek-instance / IPC gözetle:** history 2700 "IPC-client hangs up if IPC-server unavailable".
    → Neva: named-pipe/RPC health-check + timeout + otomatik reconnect; asılı kalma yok.

## §6 Kanıt ekleri — ham seçmeler

### §6.1 `AIMP.Shared.dll` — WASAPI/ASIO/DirectSound sembol seçmeleri (ASCII mangling)
```
_ZN3Aso7Devices6Wasapi5Types16IID_IAudioClientE
_ZN3Aso7Devices6Wasapi5Types22IID_IAudioRenderClientE
_ZN3Aso7Devices6Wasapi5Types23IID_IMMDeviceEnumeratorE
_ZN3Aso7Devices6Wasapi5Types24CLSID_MMDeviceEnumeratorE
_ZN3Aso7Devices4Asio14TASOASIODevice6DoOpenEv
_ZN3Aso7Devices4Asio14TASOASIODevice13DoSwitchBufferEi
_ZN3Aso7Devices4Asio14TASOASIODevice19LoadDirect32AlignedEPhS3_
_ZN3Aso7Devices4Asio31TASOASIODeviceFormatsEnumerator11IsSupportedEiiNS_5Types16TASOSampleFormatE
_ZN3Aso7Devices11Directsound14initializationEv
_ZN3Aso7Devices11Directsound12FinalizationEv
```

### §6.2 Modül adı string'leri (string-seviyesi import/şartname kanıtı)
```
AIMP.exe      : advapi32 comctl32 comdlg32 dbghelp FaultRep gdi32 gdiplus kernel32 ole32
                oleaut32 propsys shell32 shlwapi user32 version wsock32 Runtime.dll Shared.dll
AIMP.Runtime  : advapi32 comctl32 comdlg32 DWMAPI gdi32 gdiplus IMAGEHLP imm32 kernel32 mpr
                msimg32 msvcrt ole32 oleacc oleaut32 rpcrt4 Shcore shell32 SHFolder sqlite3
                user32 uxtheme version windowscodecs wininet ws2_32 wtsapi32
AIMP.Shared   : (AIMP.exe AIMPac.exe AIMPate.exe + 14 aimp_*.dll + 13 bass_*.dll + aorta.dll
                dsound.dll winmm.dll OptimFROG.dll tak_deco_lib.dll gdi32 kernel32 ole32
                shell32 user32 version)
bass.dll      : kernel32 MSACM32 msvcrt ole32 SHLWAPI user32 WINMM
libsoxr       : KERNEL32 · libFLAC: kernel32 kernelbase msvcrt · libLAME: KERNEL32 libmp3lame libmUI msvcrt
libMAC        : ADVAPI32 GDI32 KERNEL32 ole32 OLEACC OLEAUT32 SHELL32 SHLWAPI USER32
libVorbisEn   : KERNEL32 (iç string: aimp_libvorbis.dll) · libWavPack: KERNEL32 msvcrt
```
Not: `aorta.dll` sadece `AIMP.Shared.dll` string'lerinde geçiyor, kurulumda **dosya olarak YOK**
→ ya eski referans ya dinamik yükleme (UNKNOWN).

### §6.3 `IID_IAIMP*` tam liste (199) — §4'te gruplandı, kalanlar
```
IAIMPActionEvent IAIMPAlbumArtRequest IAIMPDPIAware IAIMPErrorInfo IAIMPHashCode
IAIMPHTTPClientEvents{,2} IAIMPImage{,2,Container} IAIMIMPLyrics IAIMPMemoryStream
IAIMPMenuItem IAIMPMessageHook IAIMPObjectList IAIMPOptionsDialogFrame{,KeyboardHelper,KeyboardHelper2}
IAIMPPlaybackQueueItem IAIMPPlaylistGroup IAIMPPlaylistItem IAIMPPlaylistProperties
IAIMPPlaylistPreimage{,DataProvider,Folders,Listener} IAIMPProgressCallback
IAIMPServiceAttrExtendable IAIMPServiceAttrObjects IAIMPServiceConnectionSettings
IAIMPServiceFileFormats IAIMPServiceFileInfoFormatter{,Utils} IAIMPServiceFileManager
IAIMPServiceFileStreaming IAIMPServiceFileSystems IAIMPServiceFileURI{,2}
IAIMPServiceShutdown IAIMPServiceThreads IAIMPServiceVersionInfo
IAIMPSkinInfo IAIMPTask IAIMPUIAuthDialog{,Events} IAIMPUIBaseButtonnedEdit IAIMPUIBaseComboBox
IAIMPUIBaseEdit IAIMPUIBBCBox IAIMPUIBevel IAIMPUIBrandBox IAIMPUIBrowseFolderDialog
IAIMPUICategory IAIMPUIChangeEvents IAIMPUICheckComboBox IAIMPUIColorSchema IAIMPUIControl
IAIMPUIDPIAwareness IAIMPUIDrawEvents IAIMPUIEditButton IAIMPUIFileDialogs IAIMPUIFormEvents{,2,3}
IAIMPUIGroupBox IAIMPUIImageComboBox IAIMPUIImage{,List,List2} IAIMPUIInputDialog{,Events}
IAIMPUIKeyboardEvents IAIMPUILabel IAIMPUIMemo IAIMPUMenuItem IAIMPUIMessageDialog
IAIMPUIMode IAIMPUIMouseEvents IAIMPUIMouseWheelEvents IAIMPUIPageControl{,Events}
IAIMPUIPaintBox IAIMPUIPanel IAIMPUIPlacementEvents IAIMPUIPopupMenu{,Events}
IAIMPUIProgressBar IAIMPUIProgressDialog{,Events} IAIMPUIRadioBox IAIMPUIScrollBox
IAIMPUISlider IAIMPUISpinEdit IAIMPUISplitter IAIMPUITabControl{,Events} IAIMPUITabSheet
IAIMPUITimeEdit IAIMPUItrayIcon IAIMPUITreeListColumn IAIMPUITreeListCustomDrawEvents
IAIMPUItrayListDragSortingEvents IAIMPUITreeListGroup IAIMPUITreeListInplaceEditingEvents
IAIMPUItrayListNode IAIMPUIValidationLabel IAIMPUIWinControl IAIMPUIWndProcEvents
IAIMPUIVirtualFile(=IAIMPVirtualFile)
```

### §6.4 Kullanıcı config kanıtı (`%APPDATA%\AIMP\AIMP.ini`)
```
[AIMPSoundOut]
SmoothVolumeChange=1 · FadingEnabled=1 · ReplayGainDefault=0 · PlaybackPitch=0
PlaybackSpeed=100 · PlaybackTempo=100 · SkipSilenceAmplifyThreshold=-60
DeviceName=WASAPI: Windows Default · DeviceBitDepth=4 · DeviceChannels=1 · DeviceFreq=48000
[BASS Decoder] (aynı cihaz anahtarlari tekrar)
[Encoders.FLAC] [Encoders.MusePack SV8] [Encoders.Opus] [Encoders.TAK] [Encoders.WavPack]
[Player.CustomEqPreset] [Player.AutoEqPreset] [AIMPSoundOut.MixingPresets]
[MusicLibrary.TAIMPMLLocalDataStorage{,.Presets,.Bookmarks,.SearchOptions}]
[MusicLibrary.AIMP.MyClouds.ML.Storage] [MusicLibrary.AIMP.Podcasts.ML.Storage]
```

### §6.5 Encoder XML kanıtı (`Plugins\enc_opus\enc_opus.xml`, özet)
```xml
<encoders generator="AIMP" version="1000" name="OPUS Encoder v0.2-34" author="Artem Izmaylov"
          info="Using the opusenc utility (http://opus-codec.org/)">
  <encoder Name="Opus" App="opusenc.exe">
    <description>
      <inputformats type="autogen">
        <autogen SampleRates="48000" SampleFormats="16" Channels="1;2;4;6;8"/>
      </inputformats>
      <property name="Ext" value=".opus"/><property name="IgnoreLength" value="true"/>
    </description>
    <presets><preset name="Default" cmdparams="--bitrate %bitrate% %mode% --ignorelength - %output%">
      ... <val name="~128 kbps" cmdparams="128" default="1"/> ...
```

### §6.6 Çelişki/boşluk kaydı (zero-hallucination)
- `libFLAC.dll`, `libsoxr.dll`, `libVorbisEn.dll`: VersionInfo **boş** → sürüm UNKNOWN.
- `dither` implementasyonu: binary'de anlamlı string yok → UNKNOWN.
- `aorta.dll`: sadece string referansı, dosya yok → rolü UNKNOWN (eski AIMP DSP motoru adı olabilir, doğrulanamaz).
- PE IAT kesin parse: dumpbin/llvm-readobj yok → §6.2 string-seviyesi kanıttır.
- `winampDSPGetHeader2`/`winampGetGeneralPurposePlugin`: bu sembollerin AIMP'e Winamp plugin
  yüklettiği **ima**; çalışır test yok → "destekleniyor" iddiası **VERIFICATION REQUIRED (çalıştırma testi)**.
- `BASS_CONFIG_*`: bass.dll'de string olarak geçmiyor (C-header #define'i, export değil) → UNKNOWN (beklenen).
- BASS_FX ayrı DLL (bass_fx.dll) kurulumda YOK; `BASS_FX*` sembollerinin ana bass.dll'de oluşu
  Un4seen'ın birleşik sürümüne işaret eder ama bu **çıkarım**, kesin değildir.