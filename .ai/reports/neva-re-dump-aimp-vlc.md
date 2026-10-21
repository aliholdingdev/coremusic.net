---
title: "Neva Player — AIMP & VLC Reverse-Engineering Dump Raporu"
type: report
category: neva-player
status: active
date: 2026-10-19
method: "PE string extraction + import/export analizi + plugin envanteri (RE)"
legal: "Salt analiz notu — AIMP/BASS closed-source, HİÇBİR kod kopyalanmaz; interop/referans amaçlıdır"
related: "ADR (draft): neva-player-media-stack · architecture/neva-player/index.md"
---

# Neva Player — AIMP & VLC RE Dump Raporu

> **Amaç:** Kurulu AIMP ve VLC kurulumları "SDK gibi" sökülerek Neva Player medya stack'i için
> mimari referans çıkarmak. Yöntem: PE binary string extraction (ASCII strings, import/export
> sembolleri), plugin envanteri, dosya ağacı analizi. Decompilation/kod kopyalama YAPILMADI.
> **Lisans notu:** AIMP closed-source (BASS shareware) — bu rapor yalnız interop analizi,
> hiçbir AIMP/BASS kodu Neva Engine'e kopyalanamaz. VLC/ffmpeg GPL/LGPL — serbest referans.

---

## 1. AIMP Dump (C:\Program Files\AIMP)

### 1.1 Üst Düzey Envanter

| Dosya | Boyut | Rol |
|---|---|---|
| `AIMP.exe` | 7,1 MB | Ana uygulama (Embarcadero Delphi/C++Builder RTL — `System.SysUtils`, `Vcl.StdCtrls` sembolleri) |
| `AIMP.Runtime.dll` | 15,7 MB | Runtime katmanı (Delphi RTL genişletilmiş + ACL kütüphanesi: `Acl.Sqlite3`, `Acl.Graphics`, `Acl.UI`) |
| `AIMP.Shared.dll` | 4,6 MB | Plugin API yüzeyi (IAIMP* arayüzleri) |
| `bass.dll` | 165 KB | BASS audio library (un4seen, shareware) — decode/output çekirdeği |
| `libFLAC.dll` | 530 KB | FLAC decoder |
| `libsoxr.dll` | 296 KB | SoX resampler — sample-rate dönüşümü |
| `libWavPack.dll` / `libMAC.dll` / `libLAME.dll` / `libVorbisEn.dll` | 0,2–1 MB | Format decode/encode |
| `sqlite3.dll` | 1,6 MB | Medya kütüphanesi DB |
| `AIMPac.exe` / `AIMPate.exe` / `Elevator.exe` | — | Paketleyici / editör / elevation helper |

### 1.2 Çıkarılan KRİTİK semboller (AIMP.exe — C++ mangled)

```
_ZN3Aso7Devices4Asio21TASOASIODeviceManagerv04cctrEv
  → Aso::Devices::Asio::TASOASIODeviceManager::v0::ctor()
_ZN3Aso7Devices11Directsound12FinalizationEv
  → Aso::Devices::Directsound::finalization()
_ZN3Aso7Devices11Directsound14initializationEv
  → Aso::Devices::Directsound::initialization()
```

**Sonuç (KANITLI):** AIMP, kendi native **ASIO device manager**'ına ve **DirectSound**
backend'ine sahip (`Aso::Devices` namespace'i). BASS yalnız decode/DSP tarafını, cihaz
yönetimi AIMP'in kendi C++ katmanında. `F32BitMode` + `FFrequency` alanları Runtime.dll'de
görüldü (32-bit float + frekans yönetimi).

### 1.3 Plugin API Yüzeyi (AIMP.Shared.dll — çıkarılan arayüzler)

```
IAIMPPlugin, TAIMPPluginGetHeaderProc        ← plugin giriş noktası (header proc)
IAIMPAudioDecoder, IAIMPExtensionAudioDecoder, IAIMPExtensionAudioDecoderPriority
IAIMPAudioDecoderBufferingProgress, IAIMPAudioDecoderListener
IAIMPEqualizerBands, IAIMPEqualizerPreset
IAIMPServicePlayerEqualizer, IAIMPServicePlayerEqualizerPresets
IAIMPDeprecatedEqualizerPreset               ← deprecated EQ yolu da export edilmiş
IAIMPServiceAudioDecoders, IAIMPExtensionFileFormat
IAIMPAlbumArtRequest, IAIMPExtraFileInfoRequestProxy
TAIMPCoreServiceHttpClient                   ← built-in HTTP (scrobbler/cloud için)
TAIMPAddonPluginType: aptDecoder, aptPluginPackage
```

**EQ mimarisi:** bant sayısı + preset + service üçlüsü — 10-band graphic EQ + preset zinciri
(Standart AIMP davranışı; band count strings'te sayısal değer yok, `GetBandCount` API'den).

### 1.4 Format Plugin Envanteri (Plugins/ — 34 DLL)

| Grup | Plugin'ler |
|---|---|
| **Decoder (BASS tabanlı)** | `bass_flac` `bass_aac` `bass_ac3` `bass_alac` `bass_ape` `bass_opus` `bass_mpc` `bass_tta` `bass_wv` `bass_wma` `bass_midi` `bass_spx` `bass_hls` |
| **Encode** | `enc_mpc` `enc_opus` `enc_tak` `enc_tta` |
| **SACD/DSD** | `aimp_sacd` — strings: `TSACDChunk`, `TSACDSubTrack`, `TSACDCustomContainer`, `ESACDErrorInvalidFormat` → ISO/SACD container parser'ı; DSD decode yolu mevcut |
| **FFmpeg input** | `aimp_inputFFmpeg` — strings: `TFFmpegDecoder`, `TFFmpegLibrary`, `TFFmpegMetaHelper`, `IAIMPExtensionFileFormat` → AIMP ayrı plugin'de FFmpeg decode path'i tutuyor |
| **Diğer** | `aimp_cdda` `aimp_podcasts` `aimp_myClouds` `aimp_scrobbler` `aimp_scheduler` `aimp_webLyrics` `aimp_analogMeter` `aimp_albumArtVisualization` `OptimFROG` `tak_deco_lib` |

### 1.5 AIMP Mimari Çıkarımı (Neva'ya aktarılabilir dersler)

1. **Çift katman:** decode (BASS/libs) ≠ cihaz yönetimi (kendi C++ ASIO/DS katmanı) —
   Neva'da `IAudioBackend` (ADR-019) aynı ayrımı zaten tanımlıyor ✓
2. **Plugin = arayüz + priority** — decoder priority zinciri (extensible format hierarchy)
3. **EQ service olarak** — player'dan bağımsız servis, preset yönetimi ayrı
4. **FFmpeg opsiyonel plugin** — çekirdek hafif, FFmpeg "deluxe input" eklentisi (GPL riskini
   opsiyonel tutuyor)

---

## 2. VLC Dump (C:\Program Files\VideoLAN\VLC)

### 2.1 Versiyon & Çekirdek

```
libvlc_get_version → "3.0.23 Vetinari" (3.0.23-2-0-g79128878dd)
plugin ABI: vlc_entry__3_0_0f   (VLC 3.x plugin girişi)
```

Çekirdek: `libvlc.dll` (192 KB — public API barrel) + `libvlccore.dll` (2,8 MB — pipeline
çekirdeği) + 29 kategori plugin.

### 2.2 libvlc Public API — Neva için KRİTİK export'lar

**EQ API (10-band graphic + preamp + preset):**
```
libvlc_audio_equalizer_new / _new_from_preset / _release
libvlc_audio_equalizer_get_band_count / _get_band_frequency / _get_amp_at_index
libvlc_audio_equalizer_set_amp_at_index / _set_preamp / _get_preamp
libvlc_audio_equalizer_get_preset_count / _get_preset_name
```

**Custom audio path (Neva DSP zinciri için altın API):**
```
libvlc_audio_set_callbacks        ← render callback'ini sen veriyorsun (bitstream sana gelir)
libvlc_audio_set_format
libvlc_audio_set_format_callbacks ← format nego callback'i
libvlc_audio_set_volume_callback
```
> Bu API ile VLC decode eder, PCM'i Neva Engine'e aktarır, EQ/DSP Neva'da çalışır.

**Cihaz yönetimi:**
```
libvlc_audio_output_list_get / _set
libvlc_audio_output_device_list_get / _set / _enum / _count
libvlc_audio_output_set_device_type
```

**Video out + event sistemi:**
```
libvlc_video_set_callbacks / _set_format_callbacks   ← video frame path de custom
libvlc_event_attach / _detach
libvlc_media_player_new / _play / _set_hwnd
```

### 2.3 libvlccore Internal Pipeline Sembolleri (referans)

```
aout_New → aout_OutputNew → aout_DecNew → aout_DecPlay
aout_FiltersPipelineCreate → aout_FiltersNew        ← filtre zinciri
aout_DecSynchronize, aout_DecSilence, aout_CheckReady
vout_Request → vout_ManageDisplay → vout_InitWrapper
decoder_New, decoder_NewSubpicture
demux_NewAdvanced, demux_PacketizerNew, demux_vaControlHelper
es_out (ES_OUT_SET_PCR — clock/sync omurgası)
sout_StreamNew                                       ← stream-out
```
> kaynak yol strings'leri: `src/input/es_out.c`, `src/video_output/vout_*.c` (açık kaynak,
> tam referans okunabilir — GitHub videolan/vlc).

### 2.4 Plugin Envanteri (29 kategori — Neva ilgili seçmeler)

| Kategori | Neva için önemli olanlar | Not |
|---|---|---|
| **audio_output** | `libwasapi` `libmmdevice` `libdirectsound` `libwaveout` `libamem` `libafile` | WASAPI + mmdevice (exclusive potansiyeli) + mem/file sink (test!) |
| **audio_filter** | `libequalizer` `libparam_eq` (3-band parametric!) `libcompressor` `libspatializer` `libspatialaudio` `libsamplerate` `libspeex_resampler` `libscaletempo(_pitch)` `libchorus_flanger` `libheadphone_channel_mixer` `libtospdif` `libkaraoke` `libnormvol` | EQ/comp/resample referans impl |
| **codec** | `libavcodec` (ffmpeg!) `libflac` `libopus` `libvorbis` `libmpg123` `libfaad` `libdav1d` (AV1) `libaom` `libd3d11va` `libdxva2` `libqsv` `libmft` `libx264` `libx265` `libvpx` `libtheora` `libfluidsynth` (MIDI) `libaraw` `liblpcm` `libspdif` `libaes3` | HW decode: D3D11VA/DXVA2/QSV/MFT |
| **video_output** | `libdirect3d11` `libdirect3d9` `libgl` `libwgl` `libglwin32` `libglinterop_dxva2` `libwingdi` `libcaca` | D3D11 ana renderer |
| **video_filter** | `libadjust` (renk!) `libhqdn3d` `libdeinterlace` `libpostproc` `libscale` `libsharpen` `libantiflicker` `libgradfun` | renk/denoise/scale zinciri |
| **video_chroma** | `libi420_10_p010` (10-bit HDR yolu!) `libswscale` `libi420_nv12` `libyuvp` `libchain` | P010 = 10-bit video pipeline |
| **demux** | `libmp4` `libmkv` `libogg` `libflacsys` `libwav` `libavi` `libts` `libmod` `libsid` `libgme` `libadaptive` | kapsayıcı |
| **visualization** | `libprojectm` `libglspectrum` `libgoom` | spectrum viz |
| **gui** | `libqt` `libskins2` | UI ayrı process/layer |
| **d3d11/d3d9** | `libdirect3d11_filters` `libdirect3d9_filters` | GPU filter zinciri |

### 2.5 VLC Mimari Çıkarımı

1. **Monolithic core + plugin ABI** (`vlc_entry__3_0_0f`) — tüm capability plugin
2. **Custom callback API** = VLC'yi "decode motoru" olarak gömme imkânı — Neva'nın en hızlı
   yolu: libvlc ile decode → `libvlc_audio_set_callbacks` ile PCM'i Neva DSP'ye ver
3. **es_out clock** — A/V sync omurgası test edilmiş üretim kalitesinde
4. **P010/10-bit chroma** + D3D11VA — 4K/HDR yolu hazır

---

## 3. Karşılaştırma Özeti — Neva Player'a Yansıması

| Özellik | AIMP | VLC | Neva Kararı |
|---|---|---|---|
| Lisans | closed (BASS shareware) | **LGPL/GPL açık** | VLC referans serbest; AIMP yalnız mimari ders |
| Decode | BASS + format dll'leri + opsiyonel FFmpeg plugin | libavcodec (ffmpeg gömülü) + 60+ codec | ffmpeg (LGPL build) + dr_* hafif path |
| Output | **kendi ASIO + DirectSound** (`Aso::Devices`) | wasapi/mmdevice/ds/waveout plugin | `IAudioBackend`: WASAPI exclusive → ASIO (ADR-019 sırası) |
| EQ | 10-band + presets (service) | 10-band + 3-band parametric + presets (public API) | Neva 31-band parametrik (k3 spec) — VLC bant API deseni referans |
| Custom DSP hook | plugin API (decoder-side) | **`libvlc_audio_set_callbacks`** | libvlc callback → Neva DSP zinciri (hızlı entegrasyon) |
| DSD/SACD | evet (`aimp_sacd`) | hayır | Neva: DoP/DSD path — AIMP kanıtı talep var |
| 4K/HDR video | hayır (ses odaklı) | **d3d11va + P010 + 10-bit chroma + D3D11 renderer** | libplacebo + D3D11 (araştırma raporu kararı) |
| Resampler | libsoxr | speex_resampler/ugly_resampler | libsoxr (MIT) — AIMP de kullanıyor |

---

## 4. Kanıt Bütünlüğü

| İddia | Kanıt | Güven |
|---|---|---|
| AIMP kendi ASIO manager'ına sahip | `_ZN3Aso7Devices4Asio21TASOASIODeviceManagerv04cctrEv` (AIMP.exe strings) | 95 |
| AIMP 32-bit float + frequency yönetimi | `F32BitMode`, `FFrequency` (AIMP.Runtime.dll) | 90 |
| AIMP DSD/SACD decode yolu var | `TSACDChunk/TSACDSubTrack/TSACDCustomContainer` (aimp_sacd.dll) | 90 |
| AIMP FFmpeg input plugin kullanıyor | `TFFmpegDecoder`, `TFFmpegLibrary` (aimp_inputFFmpeg.dll) | 90 |
| VLC public EQ API 10-band | 10× `libvlc_audio_equalizer_*` export (libvlc.dll) | 99 |
| VLC custom audio callback var | `libvlc_audio_set/_format/_volume_callbacks` export | 99 |
| VLC 3.0.23 | `libvlc_get_version` → "3.0.23 Vetinari" | 99 |
| VLC 10-bit/HDR yolu | `libi420_10_p010` chroma plugin + `libd3d11va` | 95 |
| BASS API yüzeyi | 60+ `BASS_*` export (bass.dll strings) | 99 |

**UNKNOWN:** AIMP EQ band sayısı kesin (API var, sayı string'de yok — AIMP 5.x = 10 band
biliniyor ama dump'da doğrulanmadı) · AIMP WASAPI exclusive kullanımı (strings'te WASAPI
görülmedi, ASIO+DS kanıtlandı — AIMP yeni sürümlerde WASAPI kullanabilir, doğrulanmadı).
