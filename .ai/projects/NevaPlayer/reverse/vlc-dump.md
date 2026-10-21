# VLC 3.0.23 Mimari Dump Raporu

- **Kaynak kurulum:** `C:\Program Files\VideoLAN\VLC`
- **Sürüm (kanıt: VersionInfo):** VLC media player 3.0.23 (VideoLAN)
- **Toplam plugin DLL:** 363 adet · 131.87 MB (138.280.080 byte)
- **Yöntem:** PowerShell `Get-ChildItem` + `VersionInfo` + binary string extraction (dumpbin yok)
- **Durum:** 🔴 AŞAMALI YAZIM — her bölüm bittiğinde diske yazıldı

## §1 Envanter Tabloları

### 1.1 Çekirdek DLL'ler

| Dosya | Boyut (byte) | Sürüm | Şirket |
|---|---:|---|---|
| libvlc.dll | 192.408 | 3.0.23 | VideoLAN |
| libvlccore.dll | 2.817.432 | 3.0.23 | VideoLAN |
| vlc.exe | 1.046.424 | 3.0.23 | VideoLAN |
| vlc-cache-gen.exe | 144.792 | 3.0.23 | VideoLAN |
| axvlc.dll (ActiveX) | 1.362.328 | 3.0.23 | VideoLAN |
| npvlc.dll (NPAPI) | 1.140.120 | 3.0.23 | VideoLAN |

### 1.2 Plugins\ kategori sayımı (30 kategori, 363 DLL)

| Kategori | Dosya sayısı | Toplam boyut (MB) |
|---|---:|---:|
| access | 36 | 18,64 |
| access_output | 8 | 5,42 |
| audio_filter | 25 | 3,83 |
| audio_mixer | 2 | 0,08 |
| audio_output | 7 | 0,37 |
| codec | 58 | 44,61 |
| control | 8 | 0,45 |
| d3d11 | 1 | 0,20 |
| d3d9 | 1 | 0,15 |
| demux | 47 | 10,38 |
| gui | 2 | 18,90 |
| keystore | 2 | 0,11 |
| logger | 2 | 0,13 |
| lua | 1 | 0,38 |
| meta_engine | 2 | 1,51 |
| misc | 10 | 4,21 |
| mux | 9 | 0,89 |
| packetizer | 14 | 0,97 |
| services_discovery | 6 | 1,33 |
| spu | 7 | 0,96 |
| stream_extractor | 1 | 0,49 |
| stream_filter | 9 | 0,47 |
| stream_out | 20 | 3,85 |
| text_renderer | 3 | 3,07 |
| video_chroma | 19 | 2,12 |
| video_filter | 41 | 2,75 |
| video_output | 15 | 3,41 |
| video_splitter | 3 | 0,18 |
| visualization | 4 | 2,01 |
| **TOPLAM** | **363** | **131.87** |

### 1.3 Kategori kırılımı (dosya adları — kanıt)

**audio_filter (25):** libaudiobargraph_a, libaudio_format, libchorus_flanger, libcompressor, libdolby_surround_decoder, libequalizer, libgain, libheadphone_channel_mixer, libkaraoke, libmad, libmono, libnormvol, libparam_eq, libremap, libsamplerate, libscaletempo_pitch, libscaletempo, libsimple_channel_mixer, libspatialaudio, libspatializer, libspeex_resampler, libstereo_widen, libtospdif, libtrivial_channel_mixer, libugly_resampler (+ `_plugin.dll`)
**audio_output (7):** libadummy, libafile, libamem, libdirectsound, libmmdevice, libwasapi, libwaveout

### 1.4 Bölüm Durumu
- §1 ✅ TAMAM
- §2 ✅ TAMAM
- §3 ✅ TAMAM
- §4 ✅ TAMAM
- §5 ✅ TAMAM (kritik)
- §6 ✅ TAMAM
- §7 ✅ TAMAM
- §8 ✅ TAMAM

---

## §2 Çekirdek Mimari

**Kanıt: dosya adları + kaynak yolları (libvlccore.dll ASCII strings) + ``lua\`` dizini.**

```
  vlc.exe (1.046.424 B)  ──  axvlc.dll / npvlc.dll (browser plug-in)
      │
      ▼
  libvlc.dll (192.408 B, 3.0.23)   Public API katmanı (libvlc_* )
      │
      ▼
  libvlccore.dll (2.817.432 B, 3.0.23)
      ├─ src/modules/bank.c     → module bank (tüm pluginlerin kayıt tablosu)
      ├─ src/modules/cache.c    → plugins cache (plugins\\plugins.dat, 314.637 B, "cache vlc 3.0.23")
      ├─ src/modules/modules.c  → "looking for %s module matching \\"%s\\": %zd candidates"
      ├─ loader: LoadLibraryA / LoadLibraryW / LoadLibraryExW + GetProcAddress
      └─ vlc-cache-gen.exe (144.792 B) → cache'i ilk kurulumda üretir
      │
      ▼ (her plugin bir DLL, 30 kategori klasöründe, 363 adet)
  plugins\\**\\lib*_plugin.dll
      └─ export sembolleri (ABI versioned):
           vlc_entry__3_0_0f
           vlc_entry_copyright__3_0_0f
           vlc_entry_license__3_0_0f
      │
      ▼ (ikinci dil: Lua script katmanı)
  plugins\\lua\\liblua_plugin.dll (396.184 B)
      └─ C:\\Program Files\\VideoLAN\\VLC\\lua\\
           extensions\\VLSub.luac (75.737 B, bytecode)
           http\\index.html + mobile_equalizer.html + css/  → web UI (HTTP remote)
           dialogs\\, playlist\\, metadata\\ (aynı ağaçta)
```

**Yorum (kanıtlı):**
1. VLC monolitik değil: çekirdek yalnızca **1 dosya** (libvlccore) + **363 ayrıntı DLL**. Yeni yetenek = yeni DLL, çekirdeğe dokunma.
2. Loader standard Win32: ``LoadLibrary`` + ``GetProcAddress``; sembol adı **ABI sürümüne kilitli** (``vlc_entry__3_0_0f``) — sürüm değişince eski pluginler kırılır, kayıt sözleşmesi korunur.
3. **Plugin cache zorunlu nitelikte** (314 KB ``plugins.dat``): tarama yok, startup'ta doğrudan okunur. ``--reset-plugins-cache`` ve ``--scan-for-plugins`` seçenekleri libvlccore strings'inde mevcut.
4. Lua, C plugin'in eşdeğeri bir modül türü (``Disable all lua plugins`` seçeneği libvlccore'da) — HTTP arayüzü ve VLSub eklentisi Lua ile yazılmış.

---

## §3 Ses Yolu

**Kanıt: 4 output DLL'i + 5 audio_filter DLL'i ASCII string extraction.**

### 3.1 Windows ses backendleri (``plugins\audio_output\`` — 7 DLL, 0.37 MB)

| DLL | Boyut | Kaynak yolu (strings'te gömülü) | Kanıtlanan API |
|---|---:|---|---|
| libwasapi_plugin.dll | 59.9 KB | ``modules/audio_output/wasapi.c`` | WASAPI render: ``cannot activate client``, ``cannot initialize audio client``, ``cannot get buffer size``, ``cannot get render client``, ``cannot get current padding``, ``cannot get buffer``, ``cannot release buffer``, ``cannot get clock``, ``cannot get position``, ``maximum latency: %llu00 ns``, ``default/minimum period : %llu00 ns``, ``buffer size : %u frames`` |
| libmmdevice_plugin.dll | 68.9 KB | ``modules/audio_output/mmdevice.c`` | MMDevice API: ``cannot create device enumerator``, ``vlc_MMNotificationClient_OnDeviceStateChanged/OnDeviceAdded``, ``default device changed``, ``mmdevice-passthrough`` (**HDMI/SPDIF audio passthrough**), ``ReloadAudioDevices`` |
| libdirectsound_plugin.dll | 62.9 KB | ``modules/audio_output/directsound.c`` | ``DirectSoundCreate``, ``DirectSoundEnumerateW``, ``IDirectSoundNotify``, ``CreateDSBuffer`` |
| libwaveout_plugin.dll | 59.9 KB | ``modules/audio_output/waveout.c`` | ``waveOutOpen/Write/Reset/Pause/Restart/GetPosition/SetVolume``, ``WAVEFORMATEXTENSIBLE`` alan dump'ı (``dwChannelMask``, ``wValidBitsPerSample``) |
| libadummy / libafile / libamem | — | dummy / file / memory aout | test & headless çıktı |

**Erişim tarafı:** ``plugins\access\libaccess_wasapi_plugin.dll`` (61.336 B) → ``wasapi-loopback``, ``cannot get capture client``, ``Loopback mode`` = sistem sesini **kay** (loopback capture).

### 3.2 WASAPI: ne KANITLI, ne KANITSIZ

- ✅ **KANITLI:** WASAPI kullanılıyor (``wasapi.c`` + ``ole32.dll`` import + ``cannot activate client`` / ``cannot get render client`` = ``IAudioClient::Initialize`` / ``IAudioRenderClient::GetBuffer`` hata metinleri).
- ✅ **KANITLI:** S/PDIF-HDMI passthrough: ``cannot negotiate DTS at 768khz IEC958 rate (HDMI), fallback to 48kHz (S/PDIF)``.
- ✅ **KANITLI:** Period/buffer hesabı kullanıcıya raporlanıyor (``default period``, ``minimum period``, ``buffer size``).
- ❓ **UNKNOWN (kanıt yok):** ``IMMDeviceEnumerator`` / ``IAudioClient`` / ``AUDCLNT_`` **dize olarak DOSYADA YOK** — bunlar COM vtable + ``#define`` HRESULT'dır, string olarak gömülmez. Exclusive-mode kanıtı da **yok** (tüm 4 DLL'de ``exclus*`` araması 0 isabet). Seçenek adı olarak ``exclusive`` geçmiyor → **shared-mode varsayımı doğrulanamadı, UNKNOWN**.
- ❓ **UNKNOWN:** ``CoInitializeEx`` yalnız ``libmmdevice`` içinde; ``libwasapi`` string'lerinde COM kurulum dizesi yok ( importing ``ole32.dll`` ).

### 3.3 audio_filter envanteri (25 DLL, 3.83 MB)

| DLL | Boyut (B) | Rol | Kanıt (parametre adları) |
|---|---:|---|---|
| libequalizer_plugin.dll | 82.840 | 10 bantlı grafik EQ | ``equalizer-bands``, ``equalizer-preset``, ``equalizer-preamp``, ``equalizer-vlcfreqs``, ``equalizer-2pass`` (§5) |
| libparam_eq_plugin.dll | 51.608 | **3 bantlı** parametrik EQ | ``param-eq-gain1``, ``param-eq-gain2``, ``param-eq-gain3``, ``param-eq-lowgain``, ``param-eq-highgain`` → 5 kazanç (3 bant + low shelf + high shelf) |
| libsamplerate_plugin.dll | 1.523.096 | yüksek kaliteli resampler (libsamplerate/SRC) | ``src-converter-type``, ``Band limited sinc interpolation ... 97dB SNR, 80% BW`` / ``121dB SNR, 90% BW``, ``SRC ratio outside [1/256, 256]`` |
| libspeex_resampler_plugin.dll | 52.632 | varsayılan hafif resampler | (parametre string'i yok — sabit işlev) |
| libcompressor_plugin.dll | 56.216 | dinamik sıkıştırma | ``compressor-attack``, ``compressor-ratio``, ``compressor-makeup-gain`` (``Set the makeup gain in dB (0 ... 24)``) |

Diğer 20 audio_filter: libaudiobargraph_a · libaudio_format · libchorus_flanger · libdolby_surround_decoder · libgain · libheadphone_channel_mixer · libkaraoke · **libmad** (MP3 decode, ``audio_filter`` altında!) · libmono · libnormvol · libremap · libscaletempo(+_pitch) · libsimple_channel_mixer · libspatialaudio · libspatializer · libstereo_widen · libtospdif · libtrivial_channel_mixer · libugly_resampler.

---

## §4 Video Yolu

**Kanıt: ``modules/video_output/win32/d3d11_quad.c``, ``modules/codec/avcodec/d3d11va.c``, ``modules/codec/avcodec/dxva2.c`` kaynak yolları (DLL string'leri).**

### 4.1 Render (``video_output`` — 15 DLL, 3.41 MB)

| DLL | Boyut | Kanıt |
|---|---:|---|
| libdirect3d11_plugin.dll | 408.472 | ``d3d11-hdr-mode``, ``d3d11-upscale-mode``, ``direct3d11-hw-blending`` ("Use hardware blending support"), ``D3D11 pool succeed with %d surfaces``, ``could not get a IDXGISwapChain3``, ``AMD driver too old, fallback to legacy shader mode`` |
| libdirect3d9_plugin.dll | 319.384 | D3D9 render path |
| libgl_plugin / libglwin32 / libwgl | — | OpenGL / WGL render |
| libglinterop_dxva2_plugin.dll | 130.968 | DXVA2 ↔ GL ara katmanı |
| libwingdi / libdirectdraw / libcaca / libdrawable / libvmem / libyuv / libvdummy / libwinhibit / libflaschen | — | GDI, drawable, mem-video, dummy çıktılar |

Eklenti kategorileri: ``plugins\d3d11\libdirect3d11_filters_plugin.dll`` (210.840 B) ve ``plugins\d3d9\libdirect3d9_filters_plugin.dll`` (152.984 B) — D3D9/D3D11'de çalışan **video filtresi** ayrı birer modül (render ≠ filtre ayrımı).

### 4.2 Donanım decode (``codec`` — 58 DLL, 44.61 MB)

| DLL | Boyut | Kanıt |
|---|---:|---|
| libd3d11va_plugin.dll | 278.936 | ``d3d11va`` · ``hw decoder`` · ``D3D11CreateDevice failed`` · ``Could not Query ID3D11VideoDevice Interface`` · ``ID3D11VideoDevice_CreateVideoDecoder`` · ``ID3D11VideoDecoderOutputView succeed with %d surfaces`` · ``D3D11VA (%s, vendor %x(%s), device %x, revision %x)`` |
| libdxva2_plugin.dll | 312.728 | ``dxva2`` · ``DirectX Video Acceleration (DXVA) 2.0`` · ``hw decoder`` · ``DXVA2CreateDirect3DDeviceManager9`` · ``IDirectXVideoDecoderService_CreateVideoDecoder`` · ``cannot load DXVA2 decoder DLL`` |
| libqsv_plugin.dll | — | Intel Quick Sync Video (MFX) |
| libmft_plugin.dll | — | Media Foundation Transform decoder |
| libcrystalhd_plugin.dll | — | Broadcom Crystal HD |

### 4.3 Chroma / P010 kanıtı (10-bit ve HDR yolu)

- ``libdirect3d11_plugin.dll`` içinde ``NV12`` ve ``P010`` FourCC string'leri (pool oluşturulurken).
- ``libdxva2_plugin.dll`` içinde ``NV12NV12`` ve ``P010P010`` (desteklenen çıktı formatları listesi).
- ``plugins\video_chroma\libi420_10_p010_plugin.dll`` → ``i420_10_p010`` = **10-bit ``I420_10`` → ``P010`` dönüşümü** (10-bit kaynak, DXVA/D3D11 hw çıktı).
- ``d3d11-hdr-mode`` seçeneği → HDR hedefi ``P010`` ile eşleşiyor.
- ``libd3d11va_plugin.dll`` kaynak yolu ``modules/codec/avcodec/d3d11va.c`` → **avcodec (ffmpeg) decoder ile ortak çalışıyor**, yani hw decode: ffmpeg → D3D11VA/DXVA2 yüzey → render.

**Sonuç (kanıtlı):** VLC'de video yolu = ``demux → packetizer → [avcodec + d3d11va|dxva2|qsv|mft] → video_chroma (P010/NV12) → video_output (direct3d11 → shader blit)``. Yazılım yolu ``libswscale_plugin.dll`` + ``libi420_*`` chroma converter'ları.

---

## §5 EQ Detayı (KRİTİK)

**Dosya:** ``plugins\audio_filter\libequalizer_plugin.dll`` (82.840 B) · kaynak ``modules/audio_filter/equalizer.c``

### 5.1 Bant sayısı — KANITLI: **10 bant**

- Doğrudan açıklama dizesi: ``"Equalizer with 10 bands"``
- Manuel kullanım dizesi: ``"You need to provide 10 values between -20dB and 20dB, separated by spaces, e.g. \"0 2 4 2 0 -2 -4 -2 0 2\""`` (örnek tam **10 değer**)
- Çalışma log'u: ``"equalizer loaded for %d Hz with %d bands %d pass"``
- Parametreler: ``equalizer-bands`` · ``equalizer-preset`` · ``equalizer-preamp`` · ``equalizer-2pass`` · ``equalizer-vlcfreqs``

### 5.2 Frekans listesi — KANITLI (binary float32 çıkarma, 4 byte hizalı dizi)

**A — ISO Standard bantları** (``equalizer-vlcfreqs = false``, seçenek dizesi: *"Use the VLC frequency bands. Otherwise, use the ISO Standard frequency bands."*) — byte offset 45952→45988, **ardışık 10 float32**:

```
31.25 | 62.5 | 125 | 250 | 500 | 1000 | 2000 | 4000 | 8000 | 16000     (Hz)
@45952 @45956 @45960 @45964 @45968 @45972 @45976 @45980 @45984 @45988
```

- **``62.5`` var, ``63`` YOK** → aranan ``63`` dizesi/float'ı dosyada **0 isabet**. İkinci frekans **62.5 Hz**.
- ``31.25`` float32 = 1 isabet, ``f64`` = 0 isabet → dizi **float32** (tek duyarlık), double değil.

**B — VLC frequency bantları** (``equalizer-vlcfreqs = true``) — byte offset 46016→46052, **ardışık 10 float32**:

```
60 | 170 | 310 | 600 | 1000 | 3000 | 6000 | 12000 | 14000 | 16000     (Hz)
```

Her iki listede de bant sayısı = 10. (Kalan tarama: ``15000``/``20``/``50``/``100`` için ek koşul bulunamadı → sabitler ayrı yeriyor; ``46060=10``, ``46068=20`` → büyük olasılıkla dB sınırı ``-20..20``, **ancak etiketli DEĞİL, bu yorumlama VERIFICATION REQUIRED**.)

### 5.3 Preset — KANITLI: **18 preset**

İki ardışık string bloğu (offset 45343→45486 gösterim adları, 45648→45780 kimlik adları) birebir örtüşüyor:

```
Kimlik : flat | classical | club | dance | fullbass | fullbasstreble | fulltreble |
         headphones | largehall | live | party | pop | reggae | rock | ska | soft |
         softrock | techno                                    → 18 adet
Gösterim: Flat, Classical, Club, Dance, Full bass, Full bass and treble, Full treble,
         Headphones, Large Hall, Live, Party, Pop, Reggae, Rock, Ska, Soft, Soft rock,
         Techno                                                → 18 adet
```

``Trance`` ve diğerleri dosyada **0 isabet**. ``flat`` ayrıca 44192'de (varsayılan preset, ``PresetCallback`` hemen yanında).

### 5.4 libvlc_audio_equalizer_* API (``libvlc.dll`` export string'leri — 11 fonksiyon)

```
libvlc_audio_equalizer_new                  libvlc_audio_equalizer_new_from_preset
libvlc_audio_equalizer_release              libvlc_audio_equalizer_set_preamp
libvlc_audio_equalizer_get_preamp           libvlc_audio_equalizer_set_amp_at_index
libvlc_audio_equalizer_get_amp_at_index     libvlc_audio_equalizer_get_band_count
libvlc_audio_equalizer_get_band_frequency   libvlc_audio_equalizer_get_preset_count
libvlc_audio_equalizer_get_preset_name
```

→ API sözleşmesi: **bant indeksi (0..9) + frekans sorgusu (``get_band_frequency``)** — yani bant listesi hem plugin içinde (§5.2) hem de public API üzerinden çekilebilir. ``libvlccore.dll`` içinde ``libvlc_audio_equalizer_*`` **yok** (API katmanı yalnız ``libvlc.dll``).

### 5.5 libparam_eq — KANITLI: **3 bantlı** parametrik EQ + 2 shelf

``plugins\audio_filter\libparam_eq_plugin.dll`` (51.608 B) parametre adları (tam liste):

```
param-eq-lowf   param-eq-lowgain      ← low shelf  (Freq Hz + dB)
param-eq-f1     param-eq-q1   param-eq-gain1   ← bant 1
param-eq-f2     param-eq-q2   param-eq-gain2   ← bant 2
param-eq-f3     param-eq-q3   param-eq-gain3   ← bant 3
param-eq-highf  param-eq-highgain     ← high shelf (Freq Hz + dB)
```

- **3 bant × (f, Q, gain) = 9 + low shelf 2 + high shelf 2 = 13 parametre.**
- Etiketler: ``"Freq 1 (Hz)"``, ``"Freq 1 Q"``, ``"Freq 1 gain (dB)"`` ... ``"Low freq (Hz)"``, ``"High freq (Hz)"`` — **ortada sabit Q yok, her bantın Q'su serbest** (gerçek parametrik EQ, biquad).
- Bant frekansı **varsayılanları bu DLL'de string olarak yok** → ``UNKNOWN`` (kod içinde float sabit, dize değil).
- ``libequalizer`` = sabit frekanslı **grafik** EQ (10 bant, ön ayarlı) ; ``libparam_eq`` = serbest frekanslı **parametrik** EQ (3 bant + 2 shelf) → ikisi farklı modül, aynı ``audio_filter`` kategorisinde.

---

## §6 Codec Envanteri

**Kategori:** ``plugins\codec\`` = **58 DLL · 44.61 MB** (tek başına tüm kurulumun %34'ü).

### 6.1 Sürüm kanıtları (string extraction)

| DLL | Boyut | Bulunan sürüm string'i | Yorum |
|---|---:|---|---|
| **libavcodec_plugin.dll** | **17.332.632** | ``Lavc58.134.100``, ``Lavf58.76.100``, ``Lavf57.``, ``ffmpeg_OpenVa``, ``ffmpeg_GetFormat``, ``ffmpeg-dr``, ``ffmpeg-hurry-up``, ``ffmpeg-skip-frame`` | Gömülü **FFmpeg** (lavc 58 serisi) — tek dosyada tüm ffmpeg decode/encode katmanı |
| libflac_plugin.dll | 269.720 | ``reference libFLAC 1.3.4 20220220`` + ``1.3.4`` | FLAC decode+encode (``FLAC__stream_decoder_new``, ``FLAC__stream_encoder_new``) |
| libopus_plugin.dll | 406.424 | sürüm dizesi **YOK** → ``UNKNOWN``; ``OpusHead``, ``OpusTags``, ``OPUS_SET_GAIN``, ``Opus Ambisonic audio order`` | libopus gömülü, sürüm stringi yok |
| libmpg123_plugin.dll | 475.544 | sürüm dizesi **YOK** → ``UNKNOWN``; ``mpg123_decode_frame``, ``[src/libmpg123/frame.c:%s():%i]`` | kaynak gömülü (hata formatı mpg123'ten) |
| libmad_plugin.dll | 177.048 | (audio_filter altında) ``MPEG audio layer I/II/III decoder``, ``layer3.c``, ``MAD_BUFFER_GUARD`` | libmad MP3 decode |
| libfaad_plugin.dll | 328.088 | ``2.10.0`` (1 isabet), ``AAC audio decoder (using libfaad2)``, ``First SBR frame is not the same as first AAC frame`` | FAAD2 = 2.10.0 |
| libx265_plugin.dll | 4.908.952 | ``x265_api_get_165``, ``x265_api_query``, ``libx265_main10.dll``, ``libx265_main12.dll``, ``[Windows][GCC 6.4.0][64 bit] 8bit`` | HEVC encode; **API sürümü 165**; 8/10/12-bit DLL varyantları |
| libdav1d_plugin.dll | 1.848.216 | ``1.5.1``, ``Using dav1d version %s with %d threads`` | AV1 decode = dav1d 1.5.1 |
| libaom_plugin.dll | 2.083.736 | ``v3.5.0``, ``AOMedia Project AV1 Decoder v3.5.0`` | AV1 decode/encode = libaom 3.5.0 |
| libvpx_plugin.dll | 2.681.240 | ``v1.15.2``, ``WebM Project VP8/VP9 Encoder/Decoder v1.15.2`` | VP8/VP9 = libvpx 1.15.2 |
| libvorbis_plugin.dll | 785.816 | ``Xiph.Org libVorbis 1.3.6`` | Vorbis 1.3.6 |
| libspeex_plugin.dll | 169.368 | ``1.2.1`` | Speex 1.2.1 |
| libx264_plugin.dll | 1.937.304 | sürüm dizesi yok (``broken ffmpeg default settings detected``) → ``UNKNOWN`` | H.264 encode = x264 |

**Derleyici damgası (tüm gömülü kütüphaneler):** ``GCC: (GNU) 6.4.0`` (MinGW-w64) — VLC win32 build zinciri.

### 6.2 avcodec entegrasyonu (KANITLI)

- Kaynak yolları: ``modules/codec/avcodec/avcodec.c``, ``video.c``, ``audio.c``, ``subtitle.c``, ``encoder.c`` → **tek plugin hem decode hem encode**.
- **Bırakma/çözme:** ``Lavc58.134.100`` = harici libavcodec **58.134.100** sembolü (ffmpeg sürüm barkodu ``av_version_info()`` çıktısı). ``Lavf58.76.100`` = libavformat.
- **HW decode köprüsü:** ``ffmpeg_OpenVa`` / ``ffmpeg_GetFormat`` + ``modules/codec/avcodec/d3d11va.c`` / ``dxva2.c`` (§4) → ffmpeg çağrıları VLC'in D3D11VA/DXVA2 cihazına bağlanır.
- ``libavcodec_plugin.dll`` (17.3 MB) tek başına ``codec`` kategorisinin %39'u.

### 6.3 Tam codec/demux/packetizer listesi (dosya adı kanıtı)

**codec (58):** adpcm · aes3 · aom · araw · aribsub · avcodec · cc · cdg · crystalhd · cvdsub · d3d11va · dav1d · ddummy · dmo · dvbsub · dxva2 · edummy · faad · flac · fluidsynth · g711 · jpeg · kate · libass · lpcm · mft · mpg123 · oggspots · opus · png · qsv · rawvideo · rtpvideo · schroedinger · scte18 · scte27 · sdl_image · spdif · speex · spudec · stl · subsdec · substx3g · subsusf · svcdsub · t140 · textst · theora · ttml · twolame · uleaddvaudio · vorbis · vpx · webvtt · x264 · x26410b · x265 · zvbi

**demux (47):** adaptive · aiff · asf · au · avi · caf · demuxdump · demux_cdg · demux_chromecast · demux_stl · diracsys · directory_demux · dmxmus · es · flacsys · gme · h26x · image · mjpeg · mkv · mod · mp4 · mpc · mpgv · noseek · nsc · nsv · nuv · ogg · playlist · ps · pva · rawaud · rawdv · rawvid · real · sid · smf · subtitle · ts · tta · ty · vc1 · vobsub · voc · wav · xa

**packetizer (14):** a52 · av1 · copy · dirac · dts · flac · h264 · hevc · mlp · mpeg4audio · mpeg4video · mpegaudio · mpegvideo · vc1

---

## §7 Öğrenilecek Dersler

> Her madde bir **§-kanıtına** bağlı. Neva Player için öneriler.

1. **Eklenti = tek sözleşmeli DLL, çekirdek sabit.** VLC 363 plugin'i ``LoadLibrary`` + tek export sembolü (``vlc_entry__3_0_0f``) ile yüklüyor; çekirdek (libvlccore, 2.8 MB) hiç değişmeden eklenti ekleniyor. → *Neva'da decode/render/effekt arayüzünü tek bir ``nv_module_v1`` export'una indir, ABI sürümünü sembol adına göm.* (§2)
2. **Cache zorunlu, tarama yok.** ``plugins\plugins.dat`` (314.637 B, ``cache vlc 3.0.23``) + ``vlc-cache-gen.exe`` sayesinde startup'ta DLL taraması yapılmıyor. → *Neva da ilk kurulumda bir ``modules.dat`` üret; sonraki açılışlarda sadece oku (Windows'ta ``LoadLibrary`` maliyeti 363 kez değil, cachede kaç kayıt varsa o kadar).* (§2)
3. **Veri, string değil binary'de.** EQ frekans listesi dize olarak **yok**; ``float32`` dizisi olarak byte offset 45952'de duruyor. → *Neva'da EQ band/frekans-tablolarını ``float``/``struct`` olarak tut; ayar dosyana (JSON) yaz, koda ``.rodata`` olarak göm. (Bu raporun §5'i bu yüzden binary taraması gerektirdi — okunabilirlik eksiği bir maliyet.)* (§5)
4. **Bant listesi tek noktadan (SSOT) + public erişim.** VLC hem ``equalizer-vlcfreqs`` ile iki frekans seti seçtiriyor hem ``libvlc_audio_equalizer_get_band_count/get_band_frequency`` ile API'ye açıyor. → *Neva'da bant seti tek ``bands[]`` dizisi + ``get_band_frequency(i)`` API'si olsun; UI frekansı koddan okusun, elle yazmasın.* (§5.2, §5.4)
5. **Grafik EQ ≠ parametrik EQ ayrı modül.** ``libequalizer`` (10 sabit bant, 18 preset, preamp, 2-pass) ile ``libparam_eq`` (3 bant × f/Q/gain + 2 shelf) birbirine karıştırılmamış. → *Neva'da EQ'yu iki özellik olarak sun: sabit bantlı (hızlı, preset'li) + tam parametrik (kullanıcı frekans/Q seçer).* (§5.5)
6. **Ses backend'i tek katman, çok impl.** wasapi/mmdevice/directsound/waveout 4 ayrı DLL; MMDevice cihaz olaylarını (``OnDeviceStateChanged``) ayrıca dinliyor, passthrough seçeneği (``mmdevice-passthrough``) ayrı. → *Neva'da ``IAudioOut`` arayüzü + 4 implementasyon (WASAPI shared varsayılan, DS fallback, WaveOut eski sistem, dummy test); cihaz hot-plug için MMDevice notification callback'i baştan koy.* (§3)
7. **HW decode'u iki nesil tut.** D3D11VA + DXVA2 + QSV + MFT hep ``codec`` altında ve **ffmpeg köprüsü** (``ffmpeg_GetFormat``) üzerinden; eski GPU için DXVA2, yeni için D3D11VA. → *Neva'da hw decode backend'ini ``D3D11VA → DXVA2 → software`` sırasıyla dene, ``ffmpeg``/``dav1d`` gibi kendi decoder'ınla aynı ``hw_frames_ctx`` kavramını kullan.* (§4, §6.2)
8. **Chroma'yı baştan genişlet.** ``P010``/``NV12`` FourCC'leri render, hw decode ve ``i420_10_p010`` chroma modüllerinde tekrar ediyor; HDR ``d3d11-hdr-mode`` ile. → *Neva'da iç formatı ``NV12`` (8-bit) + ``P010`` (10-bit) ile sınırla ve her dönüşümü ``video_chroma`` benzeri tek dönüşüm tablosundan geçir — 19 chroma modülü yerine hedeflenen 2-3 dönüşüm.* (§4.3)
9. **Fazla olanı seçmeli getir.** 363 plugin'in çoğu (``spu``, ``visualization``, ``video_filter`` 41 adet) varsayılan kullanılmıyor ama hepsi diske/dll sayısına mal oluyor. → *Neva'da core (demux/decode/render/eq) + optional paket ayrımı; ``--scan-for-plugins`` gibi dinamik yükleme yerine statik manifest.* (§1)
10. **Her mesaj eyleme geçilebilir hata + kaynak yolu içeriyor.** ``"cannot get render client (error 0x%lx)"`` + gömülü ``modules/audio_output/wasapi.c`` → crash'de dosya/satır ve HRESULT doğrudan çıkıyor. → *Neva'da her ``HRESULT``/``errno`` log'u ``file:line + hr=0x%lx`` formatında yaz; ``.pdb``/``.dbg`` dosyalarını ayrı paketle.* (§3, §8)
---

## §8 Kanıt Ekleri

> Bu raporda kullanılan tüm komutlar (PowerShell, salt-okunur) ve ham çıktı özeti.

### 8.1 Komutlar

```powershell
# §1 envanter
Get-ChildItem "C:\Program Files\VideoLAN\VLC" | Select-Object Name,Length
Get-ChildItem "...\VLC\plugins" -Directory | %{ $f=Get-ChildItem $_.FullName -Filter *.dll -File; [pscustomobject]@{Kat=$_.Name;Adet=$f.Count;MB=...} }
Get-Item "...\libvlc.dll","...\libvlccore.dll" | %{ $_.VersionInfo }
Get-ChildItem "...\plugins" -Filter *.dll -Recurse -File   # 363 DLL / 138.280.080 B

# §2-§6 string çıkarma (dumpbin YOK → ASCII/UTF-16 dize taraması)
$b=[System.IO.File]::ReadAllBytes($dll); $s=[System.Text.Encoding]::ASCII.GetString($b)
[regex]::Matches($s,'[\x20-\x7E]{4,}') | % Value | ? { $_ -match '<anahtar>' }
[System.Text.Encoding]::Unicode.GetString($b)   # UTF-16 (WASAPI/COM için denendi → 0 hit)

# §5 EQ float32 dizi çıkarma (kritik)
function Pat($name,$pat){ for($i=0;$i-le $b.Length-$pat.Length;$i++){ ... } }
function F4($v){ [BitConverter]::GetBytes([float]$v) }
Pat "f32 31.25" (F4 31.25)   # 1 isabet @45952
for($o=45940;$o-lt 46100;$o+=4){ [BitConverter]::ToSingle($b,$o) }  # ardışık dizi dökümü
```

### 8.2 Ham çıktı özetleri

| Bölüm | Anahtar ham çıktı |
|---|---|
| §1 | ``Kat Adet MB`` tablosu: codec 58/44.61 · access 36/18.64 · demux 47/10.38 · video_filter 41/2.75 · audio_filter 25/3.83 · audio_output 7/0.37 ... **TOPLAM 363 / 131.87 MB** |
| §1 | ``libvlc.dll 192408 B · 3.0.23`` · ``libvlccore.dll 2817432 B · 3.0.23`` · ``ProductVersion 3,0,23,0`` |
| §2 | libvlccore strings: ``/src/modules/bank.c`` · ``/src/modules/cache.c`` · ``/src/modules/modules.c`` · ``looking for %s module matching "%s": %zd candidates`` · ``LoadLibraryA/W`` · ``GetProcAddress`` · ``plugins-cache`` · ``reset-plugins-cache`` |
| §2 | plugin export: ``vlc_entry__3_0_0f`` · ``vlc_entry_copyright__3_0_0f`` · ``vlc_entry_license__3_0_0f`` |
| §2 | ``plugins.dat`` ilk 65 B: ``cache vlc 3.0.23`` · boyut 314.637 B |
| §2 | ``lua\`` → ``extensions\VLSub.luac`` 75.737 B · ``http\mobile_equalizer.html`` · ``plugins\lua\liblua_plugin.dll`` 396.184 B |
| §3 | wasapi: ``cannot activate client (error 0x%lx)`` · ``cannot get render client`` · ``cannot get buffer size`` · ``default period : %llu00 ns`` · import ``ole32.dll`` |
| §3 | ``dise tarama: exclus* / IMMDevice / IAudioClient / AUDCLNT_* → 0 isabet`` (hem ASCII hem UTF-16) |
| §3 | mmdevice: ``vlc_MMNotificationClient_OnDeviceStateChanged`` · ``mmdevice-passthrough = HDMI/SPDIF audio passthrough`` |
| §3 | access_wasapi: ``wasapi-loopback`` · ``cannot get capture client`` |
| §4 | d3d11va: ``D3D11VA (%s, vendor %x(%s), device %x, revision %x)`` · dxva2: ``DirectX Video Acceleration (DXVA) 2.0`` |
| §4 | chroma: FourCC ``NV12``/``P010`` · ``libi420_10_p010_plugin.dll`` · ``d3d11-hdr-mode`` |
| §5 | ``"Equalizer with 10 bands"`` · ``"0 2 4 2 0 -2 -4 -2 0 2"`` (10 değer) · ``equalizer-vlcfreqs`` |
| §5 | float32 @45952: ``31.25, 62.5, 125, 250, 500, 1000, 2000, 4000, 8000, 16000`` |
| §5 | float32 @46016: ``60, 170, 310, 600, 1000, 3000, 6000, 12000, 14000, 16000`` |
| §5 | ``63`` araması = **0 isabet** (``62.5`` var) · preset bloğu 45343→45486 = 18 ad |
| §5 | ``libvlc.dll`` export: ``libvlc_audio_equalizer_get_band_count/frequency``, ``_get_amp_at_index``, ``_get_preset_name`` ... (11 adet) |
| §5 | param_eq: ``param-eq-f1/q1/gain1``, ``f2/q2/gain2``, ``f3/q3/gain3``, ``lowf/lowgain``, ``highf/highgain`` |
| §6 | ``Lavc58.134.100`` · ``Lavf58.76.100`` · ``libFLAC 1.3.4 20220220`` · ``2.10.0`` (faad) · ``x265_api_get_165`` · ``1.5.1`` (dav1d) · ``v3.5.0`` (aom) · ``v1.15.2`` (vpx) · ``libVorbis 1.3.6`` · ``1.2.1`` (speex) |

### 8.3 Bilinmeyenler (ZERO-HALLUCINATION)

- ``exclusive`` (WASAPI exclusive mode) → **0 isabet**, kanıt yok → UNKNOWN.
- ``IMMDeviceEnumerator`` / ``IAudioClient`` / ``AUDCLNT_*`` → dize olarak yok (COM vtable/``#define``) → UNKNOWN.
- ``libopus`` / ``libmpg123`` / ``libx264`` iç kütüphane sürümleri → string yok → UNKNOWN.
- ``libparam_eq`` bant frekansı varsayılanları → binary sabit, etiketlenmemiş → UNKNOWN.
- ``46060=10.0f`` / ``46068=20.0f`` anlamları → ``-20..20 dB`` tahmini → VERIFICATION REQUIRED.
- VLC win32 build tarihi/ortamı → ``GCC (GNU) 6.4.0`` + ``/builds/videolan/vlc/extras/package/win32/`` yolu var, **tarih yok** → UNKNOWN.

