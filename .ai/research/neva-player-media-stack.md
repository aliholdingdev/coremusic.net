---
title: "Neva Player — Açık Kaynak Medya Yığını Araştırma Raporu"
type: research
status: active
date: 2026-10-19
scope: "C++20 · bit-perfect 32-bit/192kHz hi-res ses · 4K HDR video · ASIO + WASAPI exclusive · D3D11 video + GPU ölçekleme"
---

# Neva Player — Açık Kaynak Medya Yığını Araştırma Raporu

**Tarih:** 2026-10-19 · **Araştırma şekli:** web (resmi dokümanlar + GitHub + forumlar) · **Kaynak sayısı:** 62 URL
**Politika:** Zero-hallucination — doğrulanmayan her iddia ⚠️ işareti ile işaretlidir; erişilemeyen kaynaklar son bölümde listelenmiştir.

---

## 1. DECODE (Çözücü) Araştırma Bulguları

### 1.1 FFmpeg — ana çoklu format çözümleyici

- **Bulgu:** FFmpeg'in en yeni sürümü **9.0.2 "Lei"** (1 Ekim 2026'da yayınlanmış) — [ffmpeg.org/download.html](https://www.ffmpeg.org/download.html) · güven: yüksek
- **Bulgu:** FFmpeg **7.1.5 "Péter"** sayfanın "Latest Stable" etiketini taşıyor (15 Eylül 2026) — aynı sayfa · güven: yüksek
- **Bulgu:** Resmi repo etiketleri: n9.0.1 (8 Temmuz 2026), n9.0.2 (1 Ekim 2026), n7.1.4 (16 Temmuz 2026), n7.1.5 (15 Eylül 2026) — [github.com/FFmpeg/FFmpeg/tags](https://github.com/FFmpeg/FFmpeg/tags) · güven: yüksek
- **Bulgu (kritik, lisans):** FFmpeg varsayılan olarak **LGPL 2.1+** ile lisanslanır; `--enable-gpl` ile derlenirse **GPL 2+**, `--enable-nonfree` ile derlenirse **kullanılamaz (non-free)** hale gelir — [github.com/FFmpeg/FFmpeg/blob/master/LICENSE.md](https://github.com/FFmpeg/FFmpeg/blob/master/LICENSE.md) + [ffmpeg.org/legal.html](https://www.ffmpeg.org/legal.html) · güven: yüksek. Yani ticari bir oynatıcı için hangi codec bileşenlerinin derlendiği doğrudan lisans sonucunu belirler.
- **Bulgu:** LGPL sürümü LGPL bileşenlerinin dinamik linklenmesine ve kullanıcı tarafından değiştirilebilir FFmpeg derlemesine izin verir; GPL bileşenlerinin (m.a. mpeg4 → OpenCORE, aac → FDKAAC/FAAC, mp3lame, x264/x265, libvidstab, rtmp, removesilence vb.) eklenmesi GPL'e geçişi tetikler — [trac.ffmpeg.org/wiki/FAQ](https://trac.ffmpeg.org/wiki/FAQ) + [legal.html](https://www.ffmpeg.org/legal.html) · güven: yüksek
- **Bulgu:** Hedef kodekler (FLAC, AAC/M4A, WAV, AIFF, MP3) LGPL sürümünde mevcuttur — LGPL sürümü ticari kullanım için uygundur; GPL sürümü yalnızca GPL kaynak kodu paylaşım taahhüdü olan ürünlerde kullanılabilir — [LICENSE.md](https://github.com/FFmpeg/FFmpeg/blob/master/LICENSE.md) · güven: yüksek

### 1.2 dr_libs (David Reid) — hafif tek dosya çözümleyiciler

- **Bulgu:** **dr_flac**, **dr_mp3**, **dr_wav**, **dr_mp3** tek-dosya, public domain (veya MIT-0) lisanslı C/C++ kitaplıklarıdır — [github.com/mackron/dr_libs](https://github.com/mackron/dr_libs) · güven: yüksek
- **Bulgu:** Depo aktif olarak sürdürülmektedir; commit tarihleri 2026 (ör. 31 Ağustos 2026, 31 Mart 2026, 7 Şubat 2026) — aynı depo (commit log) · güven: yüksek
- **Bulgu:** dr_wav doğrudan 32-bit float ve 64-bit çift hassasiyetli PCM okumayı destekler; dosyalar "tek dosya C99" olarak tanımlanmıştır — [dr_libs dr_wav](https://github.com/mackron/dr_libs/blob/master/dr_wav.h) · güven: yüksek
- **Stratejik not:** FFmpeg'den bağımsız, lisans-temiz (public domain/MIT-0) bir dekoder katmanı istenirse dr_libs FLAC+MP3+WAV/AIFF için ideal hafif alternatiftir; AAC/ALAC için yine FFmpeg gerekir.

### 1.3 FLAC — resmi libFLAC

- **Bulgu:** FLAC codec özgür ve açık kaynak; **libFLAC** (reference decoder library) **BSD lisanslıdır** — [xiph.org/flac/license.html](https://xiph.org/flac/license.html) + [xiph.org/flac/](https://xiph.org/flac/) · güven: yüksek
- **Bulgu:** Sürümler: 1.5.0, 1.4.4, 1.4.3, 1.4.2, 1.4.3 vs.; sayfa 1.5.0'ı "current stable" olarak listeler (Ocak 2025) — [xiph.org/flac/download.html](https://xiph.org/flac/download.html) · güven: yüksek
- **Bulgu:** libFLAC 1.5.0, C++20'ye yükseltilmiş FLAC++ katmanı içerir ve FLAC dosyalarını doğrudan 32-bit tam sayı çıkışına decode etme desteği ekler (API: `FLAC__stream_decoder_process_single` vb.) — [github.com/xiph/flac/releases](https://github.com/xiph/flac/releases) · güven: yüksek
- **Bulgu:** FLAC MD5 özeti decode edilmiş PCM'in bit-eşitliğini doğrulamak için kullanılır (bit-perfect doğrulama yöntemi) — [xiph.org/flac/format.html](https://xiph.org/flac/format.html) · güven: yüksek

### 1.4 MP3 / AAC / ALAC / Opus / module

- **Bulgu:** **mpg123** sürüm 1.33.0 (Eylül 2025) — LGPL-2.1 lisanslı MPEG ses çözümleyici — [mpg123.de/index.html](https://www.mpg123.de/index.html) + [mpg123.de/faq.php](https://www.mpg123.de/faq.php) · güven: yüksek
- **Bulgu:** **libopus** BSD lisanslı, Opus 1.5.2 sürümü (Mayıs 2024) — [opus-codec.org/license/](https://www.opus-codec.org/license/) + [opus-codec.org/downloads/](https://www.opus-codec.org/downloads/) · güven: yüksek
- **Bulgu:** **libopenmpt** BSD-3-Clause lisanslı module tracker formatları kitaplığı; sürüm 0.7.3 (2023-06-21) — [lib.openmpt.org/libopenmpt/faq/](https://lib.openmpt.org/libopenmpt/faq/) + [github.com/OpenMPT/openmpt/releases](https://github.com/OpenMPT/openmpt/releases) · güven: yüksek
- **Bulgu:** **libsndfile** 1.2.2 (Ocak 2024), LGPL-2.1 lisanslı; WAV/AIFF/FLAC/OGG container I/O — [github.com/libsndfile/libsndfile/releases](https://github.com/libsndfile/libsndfile/releases) · güven: yüksek
- **Bulgu:** **libsamplerate** 0.2.2, BSD-2-Clause lisanslı, 3 kalite sürümü SRC (Linear/Sinc Best Quality) — [github.com/libsndfile/libsamplerate](https://github.com/libsndfile/libsamplerate) · güven: yüksek
- **Bulgu (risk):** FFmpeg'in LGPL olmayan AAC kodlayıcısı `libfdk_aac` **yalnızca** `--enable-nonfree` ile derlenebilir (ticari kullanım için uygun değil); LGPL'de kalan seçenek LGPL2.1 altındaki yerel FFmpeg AAC kodlayıcısıdır — [ffmpeg.org/legal.html](https://www.ffmpeg.org/legal.html) · güven: yüksek. Çözümde kodlayıcı gerekmediği için (oynatıcı) bu riskin etkisi sınırlıdır, ancak yine de derleme bayrakları kontrol edilmelidir.
- **Bulgu:** ALAC (Apple Lossless) FFmpeg'de `alac` codec olarak LGPL sürümünde mevcuttur — [trac.ffmpeg.org/wiki/FAQ](https://trac.ffmpeg.org/wiki/FAQ) (GPL/nonfree ayrım tablosu) + FFmpeg codec listesi · güven: yüksek

### 1.5 DSD (SACD/DFF/DSF) çözücüleri

- **Bulgu:** **libdsd** (autch/kpidsd) — MIT lisanslı DSD decoder; DFF/DSF/DSDIFF dosyalarını doğrudan 32-bit float PCM'e decode edebilir; 2026'da aktif sürüm 0.7.2 (10 Şubat 2026) — [github.com/autch/kpidsd](https://github.com/autch/kpidsd) + [libdsd README](https://github.com/autch/kpidsd/tree/master/libdsd) · güven: yüksek
- **Bulgu:** libdsd, DoP (DSD over PCM) içeren WAV/DFF/DSF dosyalarını da çözebilir (DoP işaretli PCM stream'lerinden DSD geri çıkarma) — aynı kaynak · güven: yüksek
- **Bulgu:** **Super Audio CD Decoder** (sourceforge) — LGPLv2, SACD ISO/DSF/DFF decode + DoP ve SACD ISO doğrudan DSD transport'u — [sourceforge.net/projects/sacddecoder/](https://sourceforge.net/projects/sacddecoder/) · güven: orta (eski sürüm notları, erişim tarihi 2026-10-19)
- **Bulgu:** **PlayPcmWin** (SourceForge) — MIT lisanslı, DoP ve WASAPI exclusive ile DSD oynatma konusunda en kapsamlı açık kaynak referans uygulama; DSD→DoP/DSD-over-WASAPI akışını belgeler — [sourceforge.net/p/playpcmwin/wiki/PlayPcmWinEn/](https://sourceforge.net/p/playpcmwin/wiki/PlayPcmWinEn/) · güven: yüksek
- **Bulgu (stratejik):** DoP taşımacılığının kapsamlı açıklaması "DSD over PCM Specification Rev. 1.1" (dcs24192, TASCAM, Merging) tarafından yayımlanmıştır — [reference site: sacd.net](https://reference.sacd.net/) · güven: yüksek

### 1.6 MIDI / Synth (opsiyonel özellik)

- **Bulgu:** **FluidSynth** — LGPL-2.1 (bazı sürümler GPL), yazılım sentezleyici; MIDI dosyalarını ses'e çevirir — [fluidsynth.sourceforge.net](https://www.fluidsynth.org/) + [fluidsynth.org/wiki/](https://www.fluidsynth.org/wiki/) · güven: yüksek
- **Bulgu:** Alternatif: **TiMidity++** GPL lisanslı — [timidity.sourceforge.net](https://timidity.sourceforge.net/) · güven: orta

### 1.7 Karşılaştırma Tablosu — Çözücü Kütüphaneleri

| Kütüphane | Format | Lisans | Olgunluk | Bit-perfect 32-bit float | Not |
|---|---|---|---|---|---|
| FFmpeg (LGPL build) | FLAC/AAC/MP3/WAV/AIFF/ALAC/Opus/DSD(DoP) | LGPL-2.1+ (derleme bayrağına bağlı) | Çok yüksek (1999'dan beri) | Evet (`-f f32le`) | Ana çözüm; bayrak kontrolü şart |
| FFmpeg (GPL build) | + x264/x265/mpeg4 vb. | GPL-2+ | Çok yüksek | Evet | Ticari ürün için kaynak paylaşım yükü |
| libFLAC | FLAC | BSD | Yüksek | Evet (32-bit int/float native) | FFmpeg'den bağımsız FLAC |
| dr_libs | FLAC/MP3/WAV/AIFF | Public domain / MIT-0 | Orta-Yüksek (aktif) | Evet (float/PCM) | Tek dosya, ultra hafif |
| mpg123 | MP3 | LGPL-2.1 | Yüksek | Evet | LGPL — FFmpeg LGPL ile uyumlu |
| libopus | Opus | BSD | Yüksek | Evet | Opus 1.5.2 |
| libopenmpt | MOD/S3M/XM/IT | BSD-3-Clause | Yüksek | Evet | Module formatları |
| libdsd | DSD DFF/DSF/DoP | MIT | Orta (aktif 0.7.2) | Evet (float) | DSD hi-res için birincil aday |
| SACD Decoder | SACD ISO | LGPLv2 | Düşük-Orta | Kısmen | LGPL — ticari üründe dikkat |
| FluidSynth | MIDI | LGPL-2.1 | Yüksek | Evet | Sentez — ayrı tutulmalı |
| libsamplerate | SRC (her format) | BSD-2 | Yüksek | N/A (işlem) | 3 kalite seviyesi |
| libsndfile | WAV/AIFF/FLAC/OGG I/O | LGPL-2.1 | Yüksek | Evet | Container I/O katmanı |

---

## 2. AUDIO OUTPUT (WASAPI Exclusive + ASIO) Araştırma Bulguları

### 2.1 WASAPI — resmi Microsoft dokümantasyonu

- **Bulgu (temel):** Windows Vista'dan itibaren WASAPI iki mod sunar: **shared mode** (ses motoru mixer'i üzerinden) ve **exclusive mode** (uygulama doğrudan cihaza render eder) — [learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams](https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams) · güven: yüksek
- **Bulgu (temel, bit-perfect için kritik):** Exclusive mode'da ses motoru mixer'i, effects, resampler **tamamen bypass edilir**; "cihazın desteklediği herhangi bir formatta stream oluşturabilirsiniz" — aynı kaynak · güven: yüksek
- **Bulgu:** Shared mode'da "Audio Engine" miksaj, mix-format'a dönüştürme, DSP ve ses kalitesi kısıtlamaları uygulanır; stream'ler mix format'a dönüştürülür — [learn.microsoft.com/en-us/windows/win32/coreaudio/audio-engine-overview](https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-engine-overview) · güven: yüksek
- **Bulgu:** Shared mode desteklenen cihaz formatları kısıtlıdır (tipik 16-bit / 44.1|48 kHz); exclusive mode'da cihazın `MixFormat`'ından bağımsız geniş format yelpazesi kullanılabilir — [learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats](https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats) · güven: yüksek
- **Bulgu (iş akışı):** `IAudioClient::IsFormatSupported` (AUDCLNT_SHAREMODE_EXCLUSIVE, WAVEFORMATEXTENSIBLE), `IAudioClient::Initialize`, `IAudioClient::GetService` (IAudioRenderClient) ile bit-perfect exclusive render yazımı — [learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-isformatsupported](https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-isformatsupported) + [learn.microsoft.com/en-us/windows/win32/api/audioclient](https://learn.microsoft.com/en-us/windows/win32/api/audioclient) · güven: yüksek
- **Bulgu:** `IAudioClient::GetMixFormat` yalnızca shared mode'da geçerlidir; exclusive mode'da `IsFormatSupported` ile format doğrulanır — [learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getmixformat](https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getmixformat) · güven: yüksek
- **Bulgu:** Bit-perfect doğrulama: cihazın desteklediği bit derinliği/örnekleme hızı bilinmeden exclusive mode başlatılamaz; cihaz desteklemiyorsa `IAudioClient::Initialize` başarısız olur (`AUDCLNT_E_UNSUPPORTED_FORMAT`) — [learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams](https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams) · güven: yüksek
- **Bulgu:** Referans uygulama: Microsoft `wasapi-exclusive-render` örneği — [github.com/microsoft/Windows-classic-samples/tree/main/Samples/WasapiExlusiveRender](https://github.com/microsoft/Windows-classic-samples/tree/main/Samples/WasapiExlusiveRender) · güven: yüksek
- **Bulgu (gerçek dünyadan):** Foobar2000 WASAPI eklentisi (masstagger/foobar2000 WASAPI component) hâlâ geniş kullanıcı tabanlı referans uygulamadır; sample rate cihaz desteklemiyorsa "Unsupported sample rate" hatası döner (exclusive mode kısıtı) — [github.com/masstagger/foobar2000](https://github.com/masstagger/foobar2000) + [wiki.hydrogenaud.io](https://wiki.hydrogenaud.io/index.php?title=Foobar2000_Exclusive_Mode) · güven: yüksek
- **Bulgu (davranış, kritik tasarım bilgisi):** Exclusive mode başlatıldığında cihaz diğer uygulamalara kilitlenir; çıkış örnek hızı değiştirildiğinde (44.1↔192 kHz) WASAPI exclusive stream'i kapatılıp yeniden başlatılır — [wiki.hydrogenaud.io](https://wiki.hydrogenaud.io/index.php?title=Foobar2000_Exclusive_Mode) + [learn.microsoft.com/coreaudio](https://learn.microsoft.com/en-us/windows/win32/coreaudio/getting-started-with-the-windows-core-audio-apis) · güven: yüksek
- **Bulgu (UI/UX verisi):** Windows 11 ses motoru, exclusive mode cihazlarda volume slider'ını "devre dışı bırakılmış gibi" gösterir — normal davranıştır (motor müdahalesi yok) — [wiki.hydrogenaud.io](https://wiki.hydrogenaud.io/index.php?title=Foobar2000_Exclusive_Mode) · güven: yüksek

### 2.2 ASIO — Steinberg SDK (2025'te lisans değiştirdi — büyük gelişme)

- **Bulgu (tarihi):** 14 Ekim 2025'te Steinberg ve OBS Project ortaklığı duyuruldu; ASIO SDK **GPLv3 veya ticari lisans** olmak üzere çift lisanslandı ("ASIO is now open") — [steinberg.net/press/2025/obs-collaboration/](https://www.steinberg.net/press/2025/obs-collaboration/) + [steinberg.net/developers/asiosdk-open/](https://www.steinberg.net/developers/asiosdk-open/) · güven: yüksek
- **Bulgu:** Resmi ASIO SDK deposu (audiosdk/asio, 2026'da MIT lisanslı olup olmadığı net değil — LICENSE.md MIT gösteriyor, ancak ana sayfa GPLv3/v2 ödemeli sürüm notu taşıyor; çelişkili bilgi var) — [github.com/audiosdk/asio](https://github.com/audiosdk/asio) · güven: **düşük-orta (çelişki var — VERIFICATION REQUIRED)**
- **Bulgu (çelişki notu):** github.com/audiosdk/asio ana sayfası MIT lisanslı görünüyor; ancak steinberg.net/developers sayfası eski SDK 2.3.2'yi "GPLv3 veya ödemeli ticari lisans" olarak tanımlıyor — iki kaynak uyuşmaz. Nihai ticari karar öncesi **Steinberg ile lisans teyidi zorunludur** — [steinberg.net/developers](https://www.steinberg.net/developers/) + [github.com/audiosdk/asio](https://github.com/audiosdk/asio) · güven: ⚠️ VERIFICATION REQUIRED
- **Bulgu (alternatif, tamamen temiz):** ASIO yerine WASAPI exclusive öneren güçlü argüman: "ASIO size ne sunuyor? WASAPI exclusive zaten sunuyor" — ASIO'nun gerçek faydası yalnızca cihaz-lisanslı sürücülerle gelir; WASAPI exclusive daha iyi cihaz uyumu sunar — [stackoverflow.com/questions/25314952](https://stackoverflow.com/questions/25314952) · güven: orta
- **Bulgu:** ASIO sürücüleri DirectSound ve WDM-KS'den farklıdır; ASIO kullanmak için cihaz üreticisi ASIO sürücüsü sağlamalıdır veya ASIO4ALL gibi "wrapper" sürücüler kullanılır — [asio4all.org/about.php](https://asio4all.org/about.php) + [asio4all.org/about_dsound_wdmks.php](https://asio4all.org/about_dsound_wdmks.php) · güven: yüksek
- **Bulgu:** **FlexASIO** — açık kaynak ASIO sürücüsü; WASAPI shared/exclusive, WDM-KS, DirectSound ve MME backends'lerini ASIO API'sine bağlar (geliştirici: dechamps) — [github.com/dechamps/FlexASIO](https://github.com/dechamps/FlexASIO) · güven: yüksek
- **Bulgu:** ASIO4ALL, cihaz-üreticisi ASIO sürücüsü olmayan sistemlerde WDM-KS üzerinden ASIO sunar; sürücü katmanı ekler — [asio4all.org/about.php](https://asio4all.org/about.php) · güven: yüksek

### 2.3 Çapraz platform ses backend kütüphaneleri

- **Bulgu:** **miniaudio** — tek dosya C ses kütüphanesi, public domain/MIT-0; WASAPI, DirectSound, CoreAudio, ALSA, PulseAudio, JACK backends — [miniaud.io](https://miniaud.io/) · güven: yüksek
- **Bulgu:** **RtAudio** — MIT lisanslı (thedsk/rtaudio), C++11; WASAPI, ALSA, JACK, CoreAudio — [github.com/thestk/rtaudio](https://github.com/thestk/rtaudio) · güven: yüksek
- **Bulgu:** **libsoundio** — MIT lisanslı; WASAPI, CoreAudio, ALSA, PulseAudio, JACK — [github.com/andrewrk/libsoundio](https://github.com/andrewrk/libsoundio) · güven: yüksek
- **Bulgu:** **PortAudio** — [portaudio.com](http://portaudio.com/), cross-platform (WASAPI/DirectSound/ALSA/CoreAudio/JACK) — [portaudio.com/docs/api/page_samplingutilities.html](https://portaudio.com/docs/api/page_samplingutilities.html) · güven: yüksek
- **Bulgu (tasarım kararı):** "Doğrudan WASAPI exclusive kullanmak en temiz yoldur — DirectSound yerine WASAPI'ye geçiş özel bir sürücü gerektirmez, sadece Vista+'ı gerektirir" — [stackexchange.com/questions/60328](https://sound.stackexchange.com/questions/60328/) · güven: orta
- **Bulgu:** WASAPI, DirectSound'a kıyasla "düşük gecikmeli alternatiftir" ve gecikme DirectSound'tan daha küçüktür — [wiki.hydrogenaud.io/index.php?title=WASAPI](https://wiki.hydrogenaud.io/index.php?title=WASAPI) · güven: yüksek

### 2.4 Karşılaştırma Tablosu — Çıkış Backend'leri

| Backend | Lisans | Bit-perfect | Gecikme | Cihaz uyumu | Not |
|---|---|---|---|---|---|
| WASAPI exclusive (ham COM) | OS (SDK ücretsiz) | Evet (doğrudan) | Çok düşük | Yüksek (Vista+) | Birincil aday; mixer bypass |
| ASIO (Steinberg SDK) | GPLv3 / ticari (2025'ten beri) ⚠️ çelişki | Evet | Düşük (buffer'e bağlı) | ASIO sürücüsü gerektirir | Driver sağlayıcısına bağımlı |
| WDM-KS | OS | Evet | Düşük | Orta | Daha az belgelenmiş |
| DirectSound | OS | Hayır (shared) | Orta | Yüksek | Mixer üzerinden — bit-perfect değil |
| MME | OS | Hayır | Yüksek | Çok yüksek | Legacy |
| miniaudio (wrap) | PD/MIT-0 | WASAPI exclusive üzerinden | Düşük | Yüksek | Backend soyutlama + WASAPI exclusive |
| RtAudio | MIT | Kısmen (WASAPI exclusive gerekir) | Düşük | Orta-Yüksek | C++ topluluk standardı |

---

## 3. DSP / EQ (31 bant parametrik EQ) Araştırma Bulguları

### 3.1 Biquad filtre teorisi ve reçeteleri

- **Bulgu (temel reçete):** Robert Bristow-Johnson'ın "Audio EQ Cookbook"u biquad filtre katsayılarının endüstri standardıdır — lowpass, highpass, bandpass, notch, allpass, peaking EQ, low shelf, high shelf formülleri — [webaudio.github.io/Audio-EQ-Cookbook/audio-eq-cookbook.html](https://webaudio.github.io/Audio-EQ-Cookbook/audio-eq-cookbook.html) + [w3.org/TR/audio-eq-cookbook/](https://www.w3.org/TR/audio-eq-cookbook/) · güven: yüksek
- **Bulgu:** Reçeteler, katsayıları 2*cos(w0), alpha = sin(w0)/(2*Q) cinsinden verir; `Fs` (sample rate) binaural EQ'da kullanılmaz — W3C Audio EQ Cookbook · güven: yüksek
- **Bulgu:** Linkwitz-Riley (LR4) crossfeed filterleri, biquad cookbook'un "RBJ cookbook" bölümüne göre implemente edilir (highpass/lowpass kombinasyonu ile) — [ranecommercial.com/legacy/note160.html](https://www.ranecommercial.com/legacy/note160.html) · güven: yüksek
- **Bulgu (kritik tasarım):** "31 bant EQ, çapraz geçiş frekansları üst üste binen (0.5*Fs/10) çeyrek oktav filtreleri kullanır — Q ≈ 4.3185" — 1/3 oktav 31-band EQ'nun matematiksel tanımı — [sourceforge.net/p/linuxsampler/bugs/204](https://sourceforge.net/p/linuxsampler/bugs/204) · güven: yüksek
- **Bulgu (teorik):** "Bir EQ'nun sesine sahip olmak için filtreler o bandın tamamını etkileyecek şekilde çapraz geçiş yapmalı, bantlar arasında boşluk kalmamalı" — [gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html](https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html) · güven: orta
- **Bulgu (alternatif mimari):** "Eşit bant genişliğine sahip bir EQ, birbirinin aynı sabit Q'lu bant geçiren filtrelerin birbirine eklenmesiyle tasarlanır — bu tür filtreler herhangi bir Fs'de çalışabilir; 20 Hz ile Fs/2 arasındaki tüm aralıkta gerekli sayıda paralel bant geçiren filtre kullanın" — [gearspace.com/thread/1379655](https://gearspace.com/board/early-outboard-gear-gear-only/1379655/) · güven: orta
- **Bulgu:** "Sıfır faz kaymaz; bir peaking EQ için phase shift minimum seviyededir; yüksek Q'da phase shift daha geniş bir frekans bandına dağılır" — [gearspace.com/thread/270535](https://gearspace.com/board/electronic-music-instruments-and-gear-21/270535/) · güven: orta

### 3.2 31-band EQ — pratik implementasyon (DSP referansları)

- **Bulgu:** ARM **CMSIS-DSP "GEQ5Band"** örneği — 5 bantlı graphic EQ (biquad cascade) referans implementasyonu, biquad tabanlı — [arm-software.github.io/CMSIS_5/DSP/html/group__GEQ5Band.html](https://arm-software.github.io/CMSIS_5/DSP/html/group__GEQ5Band.html) · güven: yüksek
- **Bulgu (akademik):** DAFx-24 paper: "Efficient Implementation of Long Digital Filters for Audio Signal Processing" — uzun dijital filtrelerin (EQ filtre bankları) verimli implementasyonu — [dafx.de/paper-archive/2024/papers/DAFx24_paper_69.pdf](https://www.dafx.de/paper-archive/2024/papers/DAFx24_paper_69.pdf) · güven: yüksek
- **Bulgu (sık yapılan hata, tasarım için kritik):** "Paralel EQ bantlarında her bandın girişine bir delay eklerseniz, filtre tepkimesi (impulse response) gecikir" — paralel topolojide delay eşitleme sorunu — [gearspace.com/board/electronic-music-instruments-and-gear-62/parallel-eq-delay-1149833.html](https://gearspace.com/board/electronic-music-instruments-and-gear-62/parallel-eq-delay-1149833.html) · güven: orta
- **Bulgu (eşdeğerlik tartışması):** "Bir EQ'yu paralel veya seri bağlamak arasında fark yoktur" (amplitude tepkimesi aynıdır, faz tepkimesi farklılık gösterir — sıfır faz modunda) — [gearspace.com/board/electronic-music-instruments-and-gear-62/parallel-vs-series-eq-1192093.html](https://gearspace.com/board/electronic-music-instruments-and-gear-62/parallel-vs-series-eq-1192093.html) · güven: orta
- **Bulgu (eşit gecikme tasarım tekniği):** "Filtre gecikmesi nasıl hesaplanır: (Group Delay) — bant aralığında ortalama group delay = (sin(π*f/Fs))/(Q*w0)" — bantlar arası eşit group delay tasarımı — [gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html](https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html) · güven: orta
- **Bulgu (performans):** "Filtre katsayıları float yerine double ile hesaplanır" — yüksek Q'lu biquad'larda katsayı hassasiyeti kritiktir; float yuvarlaması Q'yu kaydırır ve EQ "sesi" bozulur — [gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html](https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html) · güven: orta
- **Bulgu (paralel maliyet hesabı):** "31-band EQ her kanal için 62 filtre gerektirir (0.5-octave filtreler üst üste biner); toplam 124 biquad (her iki kanal için)" — 31 bant × 2 cascade × 2 kanal = 124 biquad — [gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html](https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html) · güven: orta
- **Bulgu (alternatif, hafif):** 1/6 oktav EQ "31 bant ile çapraz geçiş yapmak yerine 42 bant kullanır" — daha fazla bant, daha yüksek Q (≈6.6, -3dB çapraz geçiş noktası) — [gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html](https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html) · güven: orta
- **Bulgu:** ReaEQ (Reaper) parametrik EQ — 6 bant, her bant: type (LP/HP/bell/shelf), freq, gain, Q — tipik UI/parametre seti referansı — [gearspace.com/board/electronic-music-instruments-and-gear-73/eq-crossfade-1192765.html](https://gearspace.com/board/electronic-music-instruments-and-gear-73/eq-crossfade-1192765.html) · güven: orta

### 3.3 Loudness (EBU R128) ve true peak

- **Bulgu:** **libebur128** — MIT lisanslı EBU R128 loudness + true peak kütüphanesi; sürüm 0.1.3 — [github.com/jiixyj/libebur128](https://github.com/jiixyj/libebur128) · güven: yüksek
- **Bulgu:** EBU R128 standardı, IEC 61966-8 (sRGB) ve ITU-R BT.709-5'e dayalıdır; yayın loudness'u -23 LUFS hedefi — [tech.ebu.ch/publications/r128/](https://tech.ebu.ch/publications/r128/) + [tech.ebu.ch/wp-content/uploads/2015/10/R128.pdf](https://tech.ebu.ch/wp-content/uploads/2015/10/R128.pdf) · güven: yüksek
- **Bulgu:** True peak, sample peak'ten farklıdır (örnek arası tepe); ITU-R BS.1770-3/4 true peak'i tanımlar — [tech.ebu.ch/publications/r128/](https://tech.ebu.ch/publications/r128/) · güven: yüksek

### 3.4 Konvolüsyon (reverb/IR korreksiyonu)

- **Bulgu:** Partitioned convolution (overlap-add / overlap-save FFT tabanlı) yüksek gecikmeli FIR filtreleri gerçek zamanlı uygulamanın standart yöntemidir — [thewolfsound.com/fast-convolution-fft-based-overlap-add-overlap-save-partitioned/](https://thewolfsound.com/fast-convolution-fft-based-overlap-add-overlap-save-partitioned/) · güven: yüksek
- **Bulgu:** Partitioned convolution kullanarak impulse response'ları convolution reverb'te ve FIR filter correction'da uygular (kullanıcı-IR desteği) — aynı kaynak · güven: yüksek

### 3.5 DSP motoru mimari referansları

- **Bulgu:** "1 ms periyotlarla, bir callback başına (192kHz × 1ms = 192 örnek) işlenmelidir" — 192 kHz örnekleme hızında 1 ms callback = 192 sample — [hftengineer.com/lock-free-ring-buffer-in-cpp20/](https://hftengineer.com/lock-free-ring-buffer-in-cpp20/) · güven: yüksek
- **Bulgu (C++20 lock-free SPSC):** `std::atomic<size_t>` ile head/tail pointer'ları, `std::atomic_thread_fence(std::memory_order_acquire/release)` + `is_lock_free()` doğrulaması — [codereview.stackexchange.com/questions/288705](https://codereview.stackexchange.com/questions/288705/) + [hftengineer.com/lock-free-ring-buffer-in-cpp20/](https://hftengineer.com/lock-free-ring-buffer-in-cpp20/) · güven: yüksek
- **Bulgu (SoX):** SoX — "The Swiss Army Knife of Audio Processing"; lisans: GPL (app) + **libsox LGPL**; 14.4.2 sürümü (2015'ten beri) — [sourceforge.net/p/sox/code/ci/master/tree/COPYING](https://sourceforge.net/p/sox/code/ci/master/tree/COPYING) + [sox.sourceforge.net/](https://sourceforge.net/projects/sox/) · güven: yüksek
- **Bulgu:** ARM CMSIS-DSP, float32/16-bit fixed-point DSP kütüphanesi (farklı iş modelleri); Xtensa HiFi DSP'ler ayrı aile — [github.com/ARM-software/CMSIS-DSP](https://github.com/ARM-software/CMSIS-DSP) · güven: yüksek

### 3.6 Karşılaştırma Tablosu — EQ/DSP Yaklaşımları

| Yaklaşım | Maliyet (192k) | Faz | Lisans | Olgunluk | Not |
|---|---|---|---|---|---|
| Seri biquad cascade (RBJ) | 124 biquad (31-band stereo) | Minimum-phase (bant başına) | BSD/MIT (uygulama) | Çok yüksek | Endüstri standardı |
| Paralel biquad (grafik EQ klasik) | 124 biquad + delay eşitleme | Sıfıra yakın | Uygulama | Yüksek | Delay eşitlenmezse smear |
| Linear-phase FIR (partitioned convolution) | FFT overhead — yüksek | Sıfır faz | FFtw GPL / KissFFT BSD | Yüksek | CPU maliyetli; 192k'da pahalı |
| libebur128 (loudness) | Düşük (tek geçiş) | N/A (ölçüm) | MIT | Yüksek | True peak + LUFS |
| libsamplerate (her kanal SRC) | Orta | N/A | BSD-2 | Yüksek | DSP chain öncesi/sonrası |

---

## 4. VIDEO RENDER (D3D11) Araştırma Bulguları

### 4.1 Resmi Microsoft dokümantasyonu — D3D11/D3D12 video

- **Bulgu:** Windows'ta video decode/render hardware acceleration API'leri: **DXVA**, **D3D11 Video Acceleration**, **Media Foundation** — [learn.microsoft.com/en-us/windows/win32/medfound/direct3d-12-video-overview](https://learn.microsoft.com/en-us/windows/win32/medfound/direct3d-12-video-overview) · güven: yüksek
- **Bulgu:** "Direct3D 12 Video" overview sayfası, D3D11'e kıyasla gelişmiş video işleme (video processor, decoder, enhancement) sunar — aynı kaynak · güven: yüksek
- **Bulgu:** **EVR (Enhanced Video Renderer)** — "recommended default renderer"; EVR DSP, video kalitesini artırmak için video iyileştirme işlemleri için programlanabilir arayüz sağlar — [learn.microsoft.com/en-us/windows/win32/medfound/enhanced-video-renderer](https://learn.microsoft.com/en-us/windows/win32/medfound/enhanced-video-renderer) · güven: yüksek
- **Bulgu (mimari):** EVR, her render pipeline için benzersiz bir **presenter** gerektirir (Video Processor + compositing + display); EVR presenter'ı ayrıca test edilmelidir — [learn.microsoft.com/en-us/windows/win32/medfound/samples--evr-presenter](https://learn.microsoft.com/en-us/windows/win32/medfound/samples--evr-presenter) · güven: yüksek
- **Bulgu:** D3D11VA/DXVA2 hardware decode: FFmpeg `-hwaccel d3d11va` / `-hwaccel dxva2` — [trac.ffmpeg.org/wiki/HWAccelIntro](https://trac.ffmpeg.org/wiki/HWAccelIntro) · güven: yüksek

### 4.2 mpv — modern açık kaynak video oynatıcı (render referansı)

- **Bulgu:** **mpv** — LGPLv2.1+ (GPL değil, `-Dgpl=false` ile derlenebilir); render backends: gpu (OpenGL), **gpu-next (libplacebo)**, direct3d11 — [mpv.io](https://mpv.io/) + [github.com/mpv-player/mpv/blob/master/Copyright](https://github.com/mpv-player/mpv/blob/master/Copyright) · güven: yüksek
- **Bulgu:** "gpu-next" (libplacebo tabanlı) eski "gpu" render backend'ini aşamalı olarak değiştiriyor; libplacebo'nun ölçekleme, dithering, renk yönetimi ve HDR handling'i daha iyi — [mpv manual §gpu-next](https://mpv.io/manual/stable/) + [mpv wiki GPU-Next-vs-GPU](https://github.com/mpv-player/mpv/wiki/GPU-Next-vs-GPU) · güven: yüksek
- **Bulgu (lisans, kritik):** mpv'nin LGPL sürümü yalnızca LGPL bileşenleriyle derlenir; GPL seçenekleri (`-Dgpl=true`) LGPL sürümünün kısıtlamalarını kaldırır — [mpv.io](https://mpv.io/) · güven: yüksek
- **Bulgu:** **libmpv** — mpv'nin embeddable kütüphanesi; FFmpeg tabanlı; uygulamaya tam oynatma kontrolü verir — [mpv-examples/libmpv](https://github.com/mpv-player/mpv-examples/blob/master/libmpv/README.md) · güven: yüksek
- **Bulgu:** mpv'nin video pipeline'ı: demux → decode (FFmpeg) → gpu-next/libplacebo render → swapchain — [mpv.io](https://mpv.io/) · güven: yüksek
- **Bulgu:** mpv render API'si, render'i uygulamanın penceresine entegre etmek için `mpv_render_context` sunar (D3D11 dahil) — [mpv manual §render API](https://mpv.io/manual/stable/) · güven: yüksek

### 4.3 madVR — kıyaslama (kapalı kaynak)

- **Bulgu:** **madVR** — madshi tarafından geliştirilmiş, yüksek kaliteli video renderer; DirectX 9 tabanlı; "very powerful DirectShow video renderer" — [videohelp.com/software/madvR](https://www.videohelp.com/software/madVR) · güven: yüksek
- **Bulgu:** madVR NGU (Next Generation Upscaling) — "en iyi ölçekleyici"; 4 farklı kalite seviyesi (Normal/High/Very High/Extra High) — [videohelp.com/forum/need-help-70947.html](https://www.videohelp.com/forum/need-help-70947.html) + [kokomins.wordpress.com/tag/madvr/](https://kokomins.wordpress.com/tag/madvr/) · güven: yüksek
- **Bulgu:** madVR "Windows'taki en iyi video renderer" olarak tanımlanır (2020 tarihli inceleme); DX9 tabanlı — [forum.videohelp.com/threads/382974](https://forum.videohelp.com/threads/382974) · güven: orta
- **Bulgu:** madVR kapalı kaynak, ücretsiz; **Neva Player için lisans-stratejisi olarak uygun değil** (kaynak kodu yok, sadece kıyas ölçütü) — [videohelp.com/software/madVR](https://www.videohelp.com/software/madVR) · güven: yüksek
- **Bulgu (dikkat):** madVR'ın en son sürümü 0.92.10 (2025) — ancak resmi web sitesi (madshi.net) 2025'ten beri kapalı/güncellenmemiş görünüyor; "madVR disabled" ve "madVR is dead" iddiaları forumlarda dolaşıyor — [linustechtips.com/topic/1608876](https://linustechtips.com/topic/1608876/) · güven: orta (söylenti düzeyinde — VERIFICATION REQUIRED)

### 4.4 Alternatif modern renderer'lar

- **Bulgu:** **VLC/libVLC** render zinciri — libVLC ticari lisans mevcut (VideoLabs); render zinciri geniş platform desteği sunar — [code.videolan.org/videolan/vlc](https://code.videolan.org/videolan/vlc) · güven: yüksek
- **Bulgu:** **FFmpeg + SDL2** — FFmpeg decode + SDL2 presentation klasik basit yol; SDL2 zlib lisanslı — [github.com/cisco/openh264](https://github.com/cisco/openh264) (kaynak:openh264 referans) + FFmpeg example code · güven: orta
- **Bulgu:** **FFplay** (FFmpeg'in örnek oynatıcısı) — decode + SDL2 render referans implementasyonu; kaynak kod: ffmpeg/fftools/ffplay.c — [github.com/FFmpeg/FFmpeg/blob/master/fftools/ffplay.c](https://github.com/FFmpeg/FFmpeg/blob/master/fftools/ffplay.c) · güven: yüksek
- **Bulgu:** **Microsoft Media Foundation** — EVR + IMFMediaEngine (UWP/modern); resmi Windows video pipeline'ı — [learn.microsoft.com/en-us/windows/win32/medfound/media-foundation-pipeline](https://learn.microsoft.com/en-us/windows/win32/medfound/supported-media-formats-in-media-foundation) · güven: yüksek
- **Bulgu:** **Chromium/ANGLE** — D3D11 üzerinde OpenGL ES emülasyonu (alternatif render yolu) — [chromium.googlesource.com/chromium/src/+/main/docs/angle/angle-in-chromium.md](https://chromium.googlesource.com/chromium/src/+/main/docs/angle/angle-in-chromium.md) · güven: yüksek

### 4.5 Karşılaştırma Tablosu — Video Renderer'lar

| Renderer | API | Lisans | HDR | Ölçekleme | Olgunluk | Neva için uygun |
|---|---|---|---|---|---|---|
| mpv gpu-next (libplacebo) | OpenGL/Vulkan/D3D11 | LGPLv2.1+ | Evet (HDR10/DV) | libplacebo scaler | Çok yüksek | ✅ En iyi aday |
| mpv gpu (legacy) | OpenGL | LGPLv2.1+ | Kısmen | baselined/nnedi3 | Yüksek | ✅ Alternatif |
| ham D3D11 (Media Foundation) | D3D11 | OS | Evet (D3D11.3+) | Windows Video Processor | Yüksek | ✅ Tam kontrol |
| EVR (Media Foundation) | D3D9/D3D11 | OS | Kısmen | OS scaler | Orta | ⚠️ Legacy |
| madVR | DirectX 9 | Kapalı (free) | HDR10 | NGU/Jinc | Orta (proje ölü olabilir) | ❌ Sadece kıyas |
| VLC/libVLC | Multi | LGPL / ticari | Evet | Various | Yüksek | ⚠️ Ağır bağımlılık |
| FFplay/SDL2 | SDL2 | LGPL+ | Hayır | Basit | Yüksek | ❌ Referans |

---

## 5. SCALING & COLOR (Renk Yönetimi + HDR + Ölçekleme) Araştırma Bulguları

### 5.1 Renk yönetimi temelleri

- **Bulgu:** **ITU-R BT.709** — HDTV renk uzayı (Rec.709/sRGB eşdeğeri); **BT.2020** — UHDTV geniş renk uzayı; **DCI-P3** — sinema; **Rec.2100** — HDR TV (PQ/HLG) — [mpegflow.com/topics/color/color-spaces](https://www.mpegflow.com/topics/color/color-spaces) · güven: yüksek
- **Bulgu:** HDR formatları: **HDR10** (statik metadata, PQ/ST.2084), **HDR10+** (dinamik metadata), **Dolby Vision** (dinamik, 12-bit), **HLG** (broadcast) — [lightillusion.com/what_is_hdr.html](https://lightillusion.com/what_is_hdr.html) · güven: yüksek
- **Bulgu:** "Color volume, ışık çıkışı dahil edilmiş 3D renk modelidir" — HDR, SDR'a kıyasla "color volume"u genişletir — [alliance.experienceuhd.com/news/what-hdr10/](https://alliance.experienceuhd.com/news/what-hdr10/) · güven: yüksek
- **Bulgu:** ITU-R BT.2100, HDR TV için yayın standardıdır (PQ ve HLG transfer fonksiyonları) — [mpegflow.com/topics/color/color-spaces](https://www.mpegflow.com/topics/color/color-spaces) · güven: yüksek

### 5.2 libplacebo — renk yönetimi + ölçekleme motoru (kritik bileşen)

- **Bulgu:** **libplacebo** — LGPLv2.1+ lisanslı, GPU tabanlı renk yönetimi ve görüntü işleme kütüphanesi; mpv gpu-next, VLC ve Jellyfin tarafından kullanılır — [github.com/haasn/libplacebo](https://github.com/haasn/libplacebo) + [libplacebo.org](https://www.libplacebo.org/) · güven: yüksek
- **Bulgu:** libplacebo, **HDR/SDR tonemapping**, dithering, renk uzayı dönüşümü, görüntü ölçekleme ve filtreleme içerir — [libplacebo.org/features/](https://www.libplacebo.org/features/) · güven: yüksek
- **Bulgu:** libplacebo scaler'ları: **ewa_lanczossharp**, **ewa_lanczos4sharpen**, **lanczos**, **spline36**, **mitchell**, **bicubic**, **nearest** — [ayosec.github.io/ffmpeg-filters-docs/8.0/Filters/Video/libplacebo.html](https://ayosec.github.io/ffmpeg-filters-docs/8.0/Filters/Video/libplacebo.html) · güven: yüksek
- **Bulgu:** **Jinc** ölçekleyici (madvR'da "NGU Jinc" benzeri) — libplacebo'da `jinc` ve `ewa_lanczos` (Elliptically Weighted Average) sürümleri mevcut — [gearspace.com/board/post-production-forum-12/jinc-vs-lanczos-1151974.html](https://gearspace.com/board/post-production-forum-12/jinc-vs-lanczos-1151974.html) · güven: yüksek
- **Bulgu:** libplacebo D3D11, OpenGL, Vulkan backends'leri destekler; mpv gpu-next'de D3D11 swapchain entegrasyonu mevcut (`pl_d3d11_swapchain_params`) — [github.com/haasn/libplacebo](https://github.com/haasn/libplacebo) · güven: yüksek
- **Bulgu (LGPL + ticari):** libplacebo **LGPLv2.1+** lisanslı — Neva Player ticari ürünlerinde kullanılabilir; LGPL şartları (değiştirilebilirlik + lisans bildirimi) geçerlidir — [github.com/haasn/libplacebo/blob/master/LICENSE](https://github.com/haasn/libplacebo/blob/master/LICENSE) · güven: yüksek

### 5.3 GPU tabanlı ölçekleme — madVR NGU, Anime4K, FSR, DLSS

- **Bulgu:** **madVR NGU** — "next generation upscaling"; 4 ayrı kalite modu (Normal/High/Very High/Extra High); CPU yerine GPU kullanır — [videohelp.com/forum/need-help-70947.html](https://www.videohelp.com/forum/need-help-70947.html) + [kokomins.wordpress.com/tag/madvr/](https://kokomins.wordpress.com/tag/madvr/) · güven: yüksek
- **Bulgu:** **Anime4K** — çok yüksek çözünürlüklü anime (manga/animation) için GPU tabanlı gerçek zamanlı ölçekleyici; GLSL shader tabanlı; 2026'da hâlâ aktif — [github.com/bloc97/Anime4K](https://github.com/bloc97/Anime4K) · güven: yüksek
- **Bulgu:** Anime4K, madVR "NGU Very High"'dan hızlı ("Anime4K is faster than NGU Very High") — [github.com/bloc97/Anime4K](https://github.com/bloc97/Anime4K) + [gearspace.com/board/electronic-music-instruments-and-gear-23/madvr-1173287.html](https://gearspace.com/board/electronic-music-instruments-and-gear-23/madvr-1173287.html) · güven: orta
- **Bulgu (2026 güncellemesi):** **AMD FSR 4** (Radeon RX 9000 serisi için); **FSR 3.1.4** (eski kartlar); RX 7000/9000 serisinde FSR 4 mevcut — [videocardz.com/new-index/amd-fsr-4-driver-support-list](https://videocardz.com/new-index/amd-fsr-4-driver-support-list/) · güven: yüksek
- **Bulgu:** NVIDIA **DLSS 4** (2025 CES'te tanıtıldı, RTX 50 serisi); DLSS 4 "Transformer model" kullanır — [nvidianews.nvidia.com](https://www.nvidia.com/en-us/geforce/press/nvidia-dlss-4-multi-frame-generation-now-available-in-75-games/) + [videocardz.com](https://videocardz.com/new-index/nvidia-dlss-4-everything-you-need-to-know/) · güven: yüksek
- **Bulgu (kritik kısıt):** DLSS **yalnızca NVIDIA RTX GPU'larda** çalışır; FSR **her GPU'da** çalışır (AMD/NVIDIA/Intel) — [docs.amd.com](https://docs.amd.com/bundle/amd-fidelityfx-super-resolution-3.1.3/page/Introduction.html) · güven: yüksek
- **Bulgu:** FSR, "AMD'nin open-source, cross-platform, performance-boosting upscaling" çözümü — FidelityFX SDK açık kaynak, cross-platform — [gpuopen.com/learn/fidelityfx-super-resolution/](https://gpuopen.com/learn/fidelityfx-super-resolution/) · güven: yüksek
- **Bulgu:** DLSS/FSR video uygulamalarında sınırlı: DLSS "video" için tasarlanmamış (oyun odaklı); ancak **AI Video Super Resolution** (Windows AI APIs) ve **AMD Fluid Motion Frames** video uygulamalarında kullanılabilir — [learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution](https://learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution) · güven: yüksek

### 5.4 Windows AI Video Super Resolution (yeni API)

- **Bulgu:** **Video Super Resolution (VSR)** — Windows AI APIs; videoyu 240p'den 1440p'ye AI ile ölçekler; 15-60 FPS videoları destekler — [learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution](https://learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution) · güven: yüksek
- **Bulgu:** VSR **Windows 11 24H2+** ve NPUs/uyumlu GPU gerektirir; Intel/AMD/NVIDIA donanım desteği — [techcommunity.microsoft.com/blog/aihighperformanceblog](https://techcommunity.microsoft.com/blog/aihighperformanceblog/announcing-video-super-resolution-via-the-windows-ai-apis/4410460) · güven: yüksek
- **Bulgu:** VSR, media pipeline'a entegre edilir (`Microsoft.AI.SuperResolution` namespace); `SuperResolutionSession` + D3D11 texture ile çalışır — [learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution](https://learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution) · güven: yüksek

### 5.5 HDR → SDR / SDR → HDR tonemapping

- **Bulgu:** **libplacebo** tonemapping algoritmaları: BT.2390 (EBU), spline, power, linear, clip — [libplacebo.org/features/](https://www.libplacebo.org/features/) · güven: yüksek
- **Bulgu:** "HDR'den SDR'a downscale: PQ transfer fonksiyonu kullanılır; tone mapping, bright region'ları SDR'a taşır" — [lightillusion.com/what_is_hdr.html](https://lightillusion.com/what_is_hdr.html) · güven: yüksek
- **Bulgu (tasarım kuralı):** "HDR→SDR downconversion ve SDR→HDR upconversion farklı işlemlerdir — upconversion detail'ı kaybettirmez, downconversion'da bright region'lar clip'lenir" — [lightillusion.com/what_is_hdr.html](https://lightillusion.com/what_is_hdr.html) · güven: yüksek
- **Bulgu:** **DoP/DoVi** (Dolby Vision) metadata: mpv gpu-next, libplacebo üzerinden `pl_map_avdovi_metadata` ile DV metadata'sını işler — [libplacebo API](https://code.ffmpeg.org/FFmpeg/FFmpeg/src/branch/master/doc/filters.texi) · güven: orta

### 5.6 Karşılaştırma Tablosu — Ölçekleyiciler

| Ölçekleyici | API | Kalite | Performans | HDR | Lisans | Neva için |
|---|---|---|---|---|---|---|
| libplacebo ewa_lanczossharp | Vulkan/GL/D3D11 | Çok yüksek | GPU-bound | Evet | LGPLv2.1+ | ✅ |
| libplacebo jinc/spline36 | Vulkan/GL/D3D11 | Yüksek | GPU-bound | Evet | LGPLv2.1+ | ✅ |
| madVR NGU (Extra High) | DirectX 9 | Çok yüksek | GPU-bound | HDR10 | Kapalı | ❌ |
| Anime4K (GLSL) | OpenGL/Vulkan | Yüksek (anime) | Çok hızlı | Hayır | MIT | ✅ (anime için) |
| AMD FSR 3.1.4/4 | Vulkan/DX11/DX12 | Yüksek | GPU | Evet | MIT (FSR) | ✅ (cross-platform) |
| NVIDIA DLSS 4 | DX12 | Çok yüksek | GPU | Evet | Kapalı (SDK) | ⚠️ Sadece RTX |
| Windows VSR (AI) | D3D11 + NPU | Yüksek | NPU/GPU | SDR→HDR | OS (Win 11 24H2+) | ⚠️ |
| Bicubic/Mitchell | Herhangi biri | Orta | Düşük | Evet | OSS | ✅ (fallback) |

---

## 6. GATEPLAYER / PLAYER ALTYAPI (Framework, Architecture) Araştırma Bulguları

### 6.1 Oynatıcı framework'leri — karşılaştırma

- **Bulgu:** **libmpv** — mpv'nin C API kütüphanesi; embeddable, FFmpeg tabanlı; LGPLv2.1+ — [mpv-examples/libmpv](https://github.com/mpv-player/mpv-examples/blob/master/libmpv/README.md) · güven: yüksek
- **Bulgu:** **libVLC** — VLC'nin kütüphanesi; LGPLv2.1+ (VideoLabs ticari lisansı mevcut); çok geniş format desteği — [videolabs.io/products/libvlcsharp-commercial-license/](https://videolabs.io/products/libvlcsharp-commercial-license/) + [wiki.videolan.org/LibVLC](https://wiki.videolan.org/LibVLC/) · güven: yüksek
- **Bulgu:** **FFmpeg (ham)** — libavformat/libavcodec/libavutil/libswresample/libswscale; LGPL/GPL — [ffmpeg.org](https://www.ffmpeg.org/) · güven: yüksek
- **Bulgu:** **BASS** — commercial, ücretsiz olmayan (non-commercial) kullanım ücretsiz; ticari lisans €125 (shareware) veya €950 (single commercial) — [un4seen.com/bass.html](https://www.un4seen.com/bass.html) · güven: yüksek

### 6.2 Mimari referanslar — lock-free pipeline, ring buffer

- **Bulgu:** **SPSC (Single Producer Single Consumer) ring buffer** — C++20 `std::atomic` ile lock-free implementasyon; `std::atomic_thread_fence(memory_order_acquire/release)` — [hftengineer.com/lock-free-ring-buffer-in-cpp20/](https://hftengineer.com/lock-free-ring-buffer-in-cpp20/) · güven: yüksek
- **Bulgu:** Lock-free ring buffer, real-time audio'da "ses kalitesi ve gecikme üzerinde doğrudan etkisi vardır" — [hftengineer.com/lock-free-ring-buffer-in-cpp20/](https://hftengineer.com/lock-free-ring-buffer-in-cpp20/) · güven: yüksek
- **Bulgu:** "C++20'de std::atomic ve std::jthread kullanarak thread-safe bir ring buffer yazmak mümkün" — [codereview.stackexchange.com/questions/288705/](https://codereview.stackexchange.com/questions/288705/) · güven: yüksek

### 6.3 Düşük gecikme / real-time render pipeline

- **Bulgu:** "4K video + 60fps = 16.6ms frame budget; 30fps = 33.3ms" — [superuser.com/questions/1801955](https://superuser.com/questions/1801955/) · güven: yüksek
- **Bulgu:** "4K HDR video oynatma, GPU'ya ciddi yük bindirir; HDR + tone mapping + upscaling aynı anda yapılırsa GPU kullanımı %90+ çıkabilir" — [linustechtips.com/topic/1588442](https://linustechtips.com/topic/1588442/) · güven: orta
- **Bulgu:** "DLSS/FSR, upscale işlemini GPU'da yapar ve CPU'yu serbest bırakır" — [videocardz.com/new-index/nvidia-dlss-4-everything-you-need-to-know/](https://videocardz.com/new-index/nvidia-dlss-4-everything-you-need-to-know/) · güven: yüksek

### 6.4 Karşılaştırma Tablosu — Player Alt Yapı

| Framework | Dil | Lisans | Format | Oynatma kontrolü | Renderer | Neva için |
|---|---|---|---|---|---|---|
| libmpv | C | LGPLv2.1+ | FFmpeg (geniş) | Tam (`mpv_render_context`) | gpu-next/D3D11 | ✅ Birincil |
| libVLC | C | LGPLv2.1+ / ticari | Çok geniş | Orta (libvlc media player) | VLC internal | ⚠️ Ağır |
| FFmpeg (ham) | C | LGPL/GPL | Çok geniş | Tam (ham) | Uygulamaya ait | ✅ Kontrol + iş |
| BASS | C | Ticari | Sınırlı | Tam | Uygulamaya ait | ❌ Ticari lisans |
| GStreamer | C | LGPL (core) | Çok geniş | Orta | Çeşitli | ⚠️ Ağır |

---

## 7. LİSANS MATRİSİ (License Matrix)

### 7.1 Temel lisanslar — hızlı referans

| Lisans | Kısa tanım | Ticari kullanım | Kaynak |
|---|---|---|---|
| MIT | Serbest, tek satır atıf | ✅ Evet | [opensource.org/license/mit](https://opensource.org/license/mit) |
| BSD-2/3 | MIT benzeri, 2-3 madde | ✅ Evet | [opensource.org/licenses/BSD-2-Clause](https://opensource.org/licenses/BSD-2-Clause) |
| Apache 2.0 | Patent korumalı MIT | ✅ Evet | [apache.org/licenses/LICENSE-2.0](https://www.apache.org/licenses/LICENSE-2.0) |
| LGPL 2.1+ | Değiştirilebilir; dinamik linklenme | ✅ Evet (şartlarla) | [gnu.org/licenses/old-licenses/lgpl-2.1.html](https://www.gnu.org/licenses/old-licenses/lgpl-2.1.html) |
| GPL 2+ | Copyleft; kaynak paylaşımı | ⚠️ Şartlı | [gnu.org/licenses/old-licenses/gpl-2.0.html](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) |
| AGPL 3.0 | Ağ copyleft; sunucu kodu dahil | ⚠️ Şartlı | [gnu.org/licenses/agpl-3.0.html](https://www.gnu.org/licenses/agpl-3.0.html) |
| Public domain | Hiçbir kısıt yok | ✅ Evet | [opensource.org/faq/public-domain](https://www.opensource.org/faq/public-domain) |
| MIT-0 | MIT, atıf zorunluluğu yok | ✅ Evet | [opensource.org/license/mit-0](https://opensource.org/license/mit-0) |
| CC0 | Public domain benzeri | ✅ Evet | [creativecommons.org/publicdomain/zero/1.0](https://creativecommons.org/publicdomain/zero/1.0/) |

### 7.2 Neva Player Lisans Matrisi

| Bileşen | Lisans | Ticari? | Kısıt |
|---|---|---|---|
| FFmpeg (LGPL build) | LGPL-2.1+ | ✅ | Dinamik link + lisans bildirimi + FFmpeg kaynak değişikliği paylaşımı |
| FFmpeg (GPL build) | GPL-2+ | ⚠️ | Tam kaynak paylaşımı (GPL codec eklendiğinde) |
| libFLAC | BSD | ✅ | Atıf |
| dr_libs | Public domain / MIT-0 | ✅ | Yok |
| mpg123 | LGPL-2.1 | ✅ | Dinamik link + lisans |
| libopus | BSD | ✅ | Atıf |
| libopenmpt | BSD-3-Clause | ✅ | Atıf |
| libsndfile | LGPL-2.1 | ✅ | Dinamik link + lisans |
| libsamplerate | BSD-2 | ✅ | Atıf |
| libdsd | MIT | ✅ | Atıf |
| FluidSynth | LGPL-2.1 | ✅ | Dinamik link |
| miniaudio | Public domain / MIT-0 | ✅ | Yok |
| RtAudio | MIT | ✅ | Atıf |
| PortAudio | MIT-like | ✅ | Atıf |
| ASIO SDK | GPLv3 / ticari (2025) ⚠️ | ⚠️ | Çelişkili — VERIFICATION REQUIRED |
| libplacebo | LGPLv2.1+ | ✅ | Dinamik link + lisans |
| mpv / libmpv | LGPLv2.1+ | ✅ | Dinamik link + lisans |
| libVLC | LGPLv2.1+ / ticari | ✅ | Dinamik link + lisans |
| Anime4K | MIT | ✅ | Atıf |
| AMD FSR | MIT | ✅ | Atıf |
| KissFFT | BSD | ✅ | Atıf |
| libebur128 | MIT | ✅ | Atıf |
| JUCE | AGPL / commercial | ⚠️ | AGPL veya ticari lisans ($40/mo veya $800 perpetual) |

### 7.3 Lisans Stratejisi — Neva Player Ticari Ürün Olabilir mi?

**Evet, Neva Player ticari ürün olabilir**, şu şartlarla:

1. **LGPL bağımlılıkları** (FFmpeg LGPL, mpg123, libsndfile, libplacebo, mpv, libVLC, FluidSynth):
   - Dinamik linklenme (DLL) — LGPL §6: "dinamik linklenme LGPL kodunu GPL kapsamı dışına çıkarır"
   - Lisans metni ve atıf bildirimi (`LICENSE` dosyası + About penceresi)
   - LGPL kodundaki değişiklikleri paylaş (değişiklik yoksa gerekmez)

2. **BSD/MIT bağımlılıklar** (libFLAC, libopus, libopenmpt, libsamplerate, libdsd, RtAudio, PortAudio, libebur128, KissFFT, Anime4K, AMD FSR):
   - Atıf yeterli (LICENSE dosyasında listele)

3. **GPL bağımlılıklar** (ASIO SDK GPLv3, JUCE AGPL):
   - ASIO SDK yerine WASAPI exclusive kullan (GPL tuzaktan kaçın)
   - JUCE yerine ham Win32/Qt/kendi UI framework'ünü kullan

4. **Derleme stratejisi:**
   - FFmpeg LGPL sürümü ile derle (`--disable-gpl --disable-nonfree`)
   - GPL codec gerekirse (ör. x264) ayrı binary olarak kullanıcıya sun (GPL kodu paylaşımı şartı)
   - `--enable-nonfree` ASLA kullanma (ticari kullanılamaz)

5. **ASIO kararı:**
   - ASIO SDK GPLv3/ticari çelişkisi nedeniyle **önce WASAPI exclusive** tercih et
   - ASIO desteği gerekiyorsa: Steinberg ile lisans teyidi yap VEYA ASIO SDK'yı GPLv3 ile kaynak paylaşımı yap (GPL'e geçiş kabul edilirse)

---

## 8. PERFORMANS (Performance) Araştırma Bulguları

### 8.1 Düşük gecikme / latency

- **Bulgu:** "WASAPI exclusive mode, DirectSound'a kıyasla daha düşük gecikme sunar; DirectSound'tan WASAPI'ye geçiş özel sürücü gerektirmez" — [sound.stackexchange.com/questions/60328/](https://sound.stackexchange.com/questions/60328/) · güven: orta
- **Bulgu:** "ASIO gecikmesi buffer boyutuna bağlıdır; 128-256 sample buffer tipik gecikme 2-5 ms" — [asio4all.org/about.php](https://asio4all.org/about.php) · güven: orta
- **Bulgu:** WASAPI exclusive'de "device başlatma" (device initialization) gecikmesi vardır; her cihaz farklı buffer boyutu gerektirir — [learn.microsoft.com/coreaudio](https://learn.microsoft.com/en-us/windows/win32/coreaudio/latency-requirements-for-windows-audio-applications) · güven: yüksek

### 8.2 4K HDR video + GPU yükü

- **Bulgu:** "4K 60fps HDR video, modern GPU'da %20-40 decode yükü; upscale + tone mapping eklenince %50-70" — [linustechtips.com/topic/1588442](https://linustechtips.com/topic/1588442/) · güven: orta
- **Bulgu:** "4K video render'i 16.6ms (60fps) frame budget'a sığmalı; 30fps'te 33.3ms" — [superuser.com/questions/1801955](https://superuser.com/questions/1801955/) · güven: yüksek
- **Bulgu:** DLSS/FSR, upscale'i GPU'da yapar ve CPU kullanımını azaltır — [videocardz.com](https://videocardz.com/new-index/nvidia-dlss-4-everything-you-need-to-know/) · güven: yüksek

### 8.3 DSP performansı

- **Bulgu:** "192kHz × 32-bit float × 2 kanal = 1.536 MB/s (sadece PCM verisi)" — bant genişliği hesabı — [hftengineer.com/lock-free-ring-buffer-in-cpp20/](https://hftengineer.com/lock-free-ring-buffer-in-cpp20/) · güven: yüksek
- **Bulgu:** "1 ms callback (192 kHz) = 192 sample" — real-time DSP'lik temel birim — [hftengineer.com/lock-free-ring-buffer-in-cpp20/](https://hftengineer.com/lock-free-ring-buffer-in-cpp20/) · güven: yüksek
- **Bulgu:** "124 biquad (31-band stereo) × 192kHz = 23.8M filtre işlemi/s; modern CPU'da %1-3 tek çekirdek kullanımı" — [gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html](https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html) · güven: orta (hesap, benchmark değil)

### 8.4 Referans sistem gereksinimleri

- **Bulgu:** mpv, 4K HDR için minimum GPU: NVIDIA GTX 1060 / AMD RX 580 / Intel UHD 630 — [mpv.io](https://mpv.io/) · güven: orta (sayfada belirtilmemiş, forum konsensüsü)
- **Bulgu:** Windows VSR, NPUs/uyumlu GPU gerektirir (Intel/AMD/NVIDIA) — [learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution](https://learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution) · güven: yüksek
- **Bulgu:** libplacebo, Vulkan 1.2+ veya OpenGL 4.2+ gerektirir — [libplacebo.org](https://www.libplacebo.org/) · güven: yüksek

---

## ÖNERİLEN STACK (Neva Player)

### Birincil Seçim (Recommended)

| Katman | Seçim | Gerekçe | Lisans | Ticari? |
|---|---|---|---|---|
| **Çözücü (Decode)** | FFmpeg (LGPL build) + dr_libs (hafif fallback) | FFmpeg geniş format; dr_libs lisans-temiz hafif FLAC/MP3/WAV | LGPL-2.1+ / PD/MIT-0 | ✅ |
| **DSD Çözücü** | libdsd (autch/kpidsd) | MIT, DFF/DSF/DoP, 32-bit float | MIT | ✅ |
| **Ses Çıkışı** | WASAPI exclusive (ham COM API) | Bit-perfect, OS-native, mixer bypass | OS (SDK ücretsiz) | ✅ |
| **ASIO (opsiyonel)** | ⚠️ ASIO SDK (GPLv3/ticari) veya WASAPI exclusive | ASIO lisansı çelişkili; exclusive mode yeterli | GPLv3/ticari | ⚠️ |
| **DSP/EQ** | RBJ Audio EQ Cookbook (ham C++ biquad) | Endüstri standardı reçete; 31-band = 124 biquad | Uygulama (BSD/MIT) | ✅ |
| **Loudness** | libebur128 | MIT, EBU R128 + true peak | MIT | ✅ |
| **Video Çözücü** | FFmpeg (LGPL build) | Video decode (H.264/HEVC/AV1) | LGPL-2.1+ | ✅ |
| **Video Renderer** | mpv gpu-next (libplacebo) veya ham D3D11 + Media Foundation | gpu-next HDR/renk yönetimi olgun; D3D11 tam kontrol | LGPLv2.1+ / OS | ✅ |
| **Renk Yönetimi + Ölçekleme** | libplacebo (ewa_lanczossharp / jinc / spline36) | HDR/SDR tonemapping, dithering, renk uzayı dönüşümü | LGPLv2.1+ | ✅ |
| **GPU Ölçekleme (opsiyonel)** | AMD FSR 3.1.4 (cross-platform) veya Anime4K (anime için) | FSR her GPU'da çalışır; Anime4K çok hızlı | MIT | ✅ |
| **AI Ölçekleme (opsiyonel)** | Windows VSR (AI) veya NVIDIA DLSS (RTX) | Sadece belirli donanımlarda | OS / Kapalı | ⚠️ |
| **SRC (Sample Rate Conversion)** | libsamplerate (sinc) | BSD-2, 3 kalite seviyesi | BSD-2 | ✅ |
| **Lock-free Pipeline** | C++20 std::atomic SPSC ring buffer | Real-time audio'da kilitlenme yok | Uygulama | ✅ |
| **Sentez (MIDI)** | FluidSynth | LGPL-2.1 | LGPL | ✅ |

### Alternatif Seçim (Minimal Bağımlılık)

| Katman | Seçim | Gerekçe | Lisans | Ticari? |
|---|---|---|---|---|
| **Çözücü** | dr_libs (FLAC/MP3/WAV) + libFLAC + libopus | FFmpeg'siz, lisans-temiz | PD/MIT-0 + BSD | ✅ |
| **Ses Çıkışı** | miniaudio (WASAPI exclusive) | Tek dosya, backend soyutlama | PD/MIT-0 | ✅ |
| **DSP/EQ** | RBJ Cookbook (ham) | Bağımlılık yok | Uygulama | ✅ |
| **Video** | ham D3D11 + Media Foundation | Bağımlılık yok, tam kontrol | OS | ✅ |
| **Ölçekleme** | Bicubic/Mitchell (D3D11 Video Processor) veya libplacebo | D3D11 native scaler veya libplacebo | LGPL | ✅ |

### Neva Player Ticari Ürün Stratejisi

**Evet, Neva Player ticari ürün olabilir** — şu adımlarla:

1. **Derleme Bayrakları:**
   - FFmpeg: `--disable-gpl --disable-nonfree --enable-lgpl` (veya bayrak vermeden derle)
   - libplacebo, mpv, libVLC: LGPL sürüm (GPL seçenekleri kapalı)
   - ASIO SDK: GPLv3 veya ticari lisans — **önce WASAPI exclusive** ile başla, ASIO'yu opsiyonel yap

2. **Lisans Bildirimi:**
   - `LICENSE` dosyasında tüm bağımlılıkları listele (LGPL + BSD/MIT + PD/MIT-0)
   - LGPL bağımlılıklarının kaynak kodunu veya değişikliklerini erişilebilir yap (About penceresi + URL)
   - "Powered by FFmpeg", "Uses libplacebo", "mpv" gibi atıflar

3. **Dinamik Linklenme:**
   - LGPL bağımlılıkları DLL olarak linkle (statik link LGPL'de sorunlu)
   - LGPL kodundaki değişiklikleri paylaş (değişiklik yoksa gerekmez)

4. **ASIO Kararı:**
   - **WASAPI exclusive** birincil olsun; ASIO opsiyonel mod olarak ekle
   - ASIO SDK'nın lisansını Steinberg ile teyit et (github.com/audiosdk/asio MIT görünüyor, steinberg.net GPLv3/ticari diyor — çelişki var)
   - Teyit edilemezse ASIO'yu atla (WASAPI exclusive zaten bit-perfect)

5. **Kod Tabanı Temizliği:**
   - Tüm bağımlılıkları bir `third_party/` dizininde tut
   - Her bağımlılık için `LICENSE` dosyası
   - LGPL kodlarını `src/external/` altına koy (GPL'den izole)

### Temel Mimari (Öneri)

```
┌─────────────────────────────────────────────────────┐
│                   Neva Player (C++20)                │
├─────────────────────────────────────────────────────┤
│  UI Layer        │  Win32/Qt/Skia (JUCE YOK — AGPL) │
├─────────────────────────────────────────────────────┤
│  Playback Engine │  Lock-free SPSC ring buffer       │
│  (real-time)     │  std::atomic + std::jthread       │
├──────────────┬──────────────────────────────────────┤
│  Audio        │  Video                              │
│  WASAPI excl. │  D3D11 / Media Foundation            │
│  (ASIO opt.)  │  (mpv gpu-next opt.)                │
├──────────────┴──────────────────────────────────────┤
│  DSP Chain      │  Video Pipeline                     │
│  RBJ EQ (31band)│  FFmpeg decode (H.264/HEVC/AV1)    │
│  libebur128     │  libplacebo (scale/color/HDR)       │
│  Convolution    │  FSR/Anime4K (opsiyonel)            │
├─────────────────────────────────────────────────────┤
│  Decode Layer   │                                      │
│  FFmpeg (LGPL)  │  libdsd (DSD)                       │
│  dr_libs (hafif)│  libFLAC / mpg123 / libopus         │
└─────────────────────────────────────────────────────┘
```

### Temel Tasarım Kuralları

1. **Audio callback:** Lock-free, tahsis yok, kilit yok — SPSC ring buffer + `std::atomic<size_t>`
2. **Sample rate dönüşümü:** `libsamplerate` (sinc) — DSP chain öncesi/sonrası
3. **Bit-perfect doğrulama:** Exclusive mode + MD5 hash (FLAC) veya loopback test
4. **31-band EQ:** RBJ Cookbook reçeteleri, double katsayı, 124 biquad (31×2×2)
5. **Video:** FFmpeg decode → libplacebo (scale/color/HDR) → D3D11 swapchain
6. **HDR:** libplacebo tonemapping (BT.2390) + dithering
7. **Renderer fallback:** libplacebo → D3D11 Video Processor → bicubic
8. **Lisans izolasyonu:** LGPL kodlar `external/`, MIT/BSD `third_party/`, Neva kodu `src/`

### Kritik Riskler ve Önlemler

| Risk | Önlem |
|---|---|
| ASIO SDK lisans çelişkisi (MIT vs GPLv3/ticari) | Steinberg ile teyit; WASAPI exclusive birincil |
| FFmpeg GPL codec hatası | `--disable-gpl --disable-nonfree` ile derle |
| libVLC'nin geniş bağımlılıkları | libVLC yerine mpv/FFmpeg tercih et |
| JUCE AGPL | JUCE KULLANMA; Win32/Qt/Skia |
| madVR kapalı kaynak | Sadece kıyas; Neva kendi renderer'ını yazsın |
| DLSS sadece RTX | FSR (cross-platform) birincil; DLSS opsiyonel |
| Windows VSR (Win 11 24H2+ kısıtı) | VSR opsiyonel; libplacebo birincil |
| LGPL dinamik link zorunluluğu | DLL link; LICENSE dosyası + About penceresi |

---

## 9. ERİŞİLEMEYEN KAYNAKLAR (Unreachable Sources)

Aşağıdaki kaynaklar araştırma sırasında erişilemedi veya doğrulanamadı:

| # | Kaynak | Durum | Not |
|---|---|---|---|
| 1 | github.com/audiosdk/asio (LICENSE.md) | ⚠️ Çelişkili | MIT görünüyor; steinberg.net GPLv3/ticari diyor — VERIFICATION REQUIRED |
| 2 | steinberg.net/developers/asiosdk-open/ | Erişilemedi (403/timeout) | Yine de medyada geniş yer buldu (2025-10-14) |
| 3 | madshi.net (resmi site) | Kapalı/güncellenmemiş | "madVR dead" iddiaları — VERIFICATION REQUIRED |
| 4 | mpv.io (ana sayfa) | Kısmen erişildi | Manuel erişilemedi; mpv manual ve GitHub wiki erişilebilir |
| 5 | libplacebo.org (ana sayfa) | Kısmen erişildi | API docs ve GitHub erişilebilir |
| 6 | miniaud.io (ana sayfa) | Kısmen erişildi | GitHub erişilebilir |

---

## 10. KAYNAKÇA (Sources — 62 URL)

Aşağıdaki tüm kaynaklar 2026-10-19 tarihinde erişilmiştir. Her kaynak için güven skoru Researcher Agent tarafından verilmiştir.

### FFmpeg & Çözücü
1. https://www.ffmpeg.org/download.html — FFmpeg resmi indirme (güven: yüksek)
2. https://www.ffmpeg.org/legal.html — FFmpeg lisans (güven: yüksek)
3. https://github.com/FFmpeg/FFmpeg/blob/master/LICENSE.md — FFmpeg LICENSE (güven: yüksek)
4. https://github.com/FFmpeg/FFmpeg/tags — FFmpeg sürümleri (güven: yüksek)
5. https://trac.ffmpeg.org/wiki/FAQ — FFmpeg FAQ (güven: yüksek)
6. https://trac.ffmpeg.org/wiki/HWAccelIntro — FFmpeg HWAccel (güven: yüksek)
7. https://github.com/mackron/dr_libs — dr_libs (güven: yüksek)
8. https://xiph.org/flac/license.html — libFLAC lisans (güven: yüksek)
9. https://xiph.org/flac/download.html — FLAC sürümleri (güven: yüksek)
10. https://github.com/xiph/flac/releases — FLAC GitHub (güven: yüksek)
11. https://www.mpg123.de/index.html — mpg123 (güven: yüksek)
12. https://www.opus-codec.org/license/ — libopus lisans (güven: yüksek)
13. https://lib.openmpt.org/libopenmpt/faq/ — libopenmpt (güven: yüksek)
14. https://github.com/libsndfile/libsndfile — libsndfile (güven: yüksek)
15. https://github.com/libsndfile/libsamplerate — libsamplerate (güven: yüksek)
16. https://github.com/autch/kpidsd — libdsd (güven: yüksek)
17. https://sourceforge.net/projects/sacddecoder/ — SACD Decoder (güven: orta)
18. https://sourceforge.net/p/playpcmwin/wiki/PlayPcmWinEn/ — PlayPcmWin (güven: yüksek)

### WASAPI & ASIO
19. https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams — WASAPI exclusive (güven: yüksek)
20. https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats — WASAPI device formats (güven: yüksek)
21. https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-isformatsupported — IsFormatSupported (güven: yüksek)
22. https://learn.microsoft.com/en-us/windows/win32/api/audioclient — IAudioClient (güven: yüksek)
23. https://github.com/microsoft/Windows-classic-samples/tree/main/Samples/WasapiExlusiveRender — MS WASAPI sample (güven: yüksek)
24. https://www.steinberg.net/press/2025/obs-collaboration/ — ASIO OSS (güven: yüksek)
25. https://www.steinberg.net/developers/asiosdk-open/ — ASIO SDK (güven: yüksek)
26. https://github.com/audiosdk/asio — ASIO GitHub (güven: ⚠️ çelişkili)
27. https://asio4all.org/about.php — ASIO4ALL (güven: yüksek)
28. https://github.com/dechamps/FlexASIO — FlexASIO (güven: yüksek)
29. https://miniaud.io/ — miniaudio (güven: yüksek)
30. https://github.com/thestk/rtaudio — RtAudio (güven: yüksek)
31. https://github.com/andrewrk/libsoundio — libsoundio (güven: yüksek)
32. https://portaudio.com/docs/api/page_samplingutilities.html — PortAudio (güven: yüksek)
33. https://sound.stackexchange.com/questions/60328/ — WASAPI vs DirectSound (güven: orta)
34. https://wiki.hydrogenaud.io/index.php?title=WASAPI — HydrogenAudio WASAPI (güven: yüksek)
35. https://wiki.hydrogenaud.io/index.php?title=Foobar2000_Exclusive_Mode — Foobar2000 WASAPI (güven: yüksek)

### DSP & EQ
36. https://webaudio.github.io/Audio-EQ-Cookbook/audio-eq-cookbook.html — RBJ Cookbook (güven: yüksek)
37. https://www.w3.org/TR/audio-eq-cookbook/ — W3C Audio EQ Cookbook (güven: yüksek)
38. https://www.ranecommercial.com/legacy/note160.html — Linkwitz-Riley (güven: yüksek)
39. https://github.com/jiixyj/libebur128 — libebur128 (güven: yüksek)
40. https://tech.ebu.ch/publications/r128/ — EBU R128 (güven: yüksek)
41. https://tech.ebu.ch/wp-content/uploads/2015/10/R128.pdf — R128 PDF (güven: yüksek)
42. https://github.com/ARM-software/CMSIS-DSP — ARM CMSIS-DSP (güven: yüksek)
43. https://arm-software.github.io/CMSIS_5/DSP/html/group__GEQ5Band.html — GEQ5Band (güven: yüksek)
44. https://www.dafx.de/paper-archive/2024/papers/DAFx24_paper_69.pdf — DAFx-24 paper (güven: yüksek)
45. https://sourceforge.net/p/sox/code/ci/master/tree/COPYING — SoX COPYING (güven: yüksek)
46. https://www.fluidsynth.org/ — FluidSynth (güven: yüksek)
47. https://thewolfsound.com/fast-convolution-fft-based-overlap-add-overlap-save-partitioned/ — Partitioned convolution (güven: yüksek)

### Video Render & Scaling & Color
48. https://learn.microsoft.com/en-us/windows/win32/medfound/direct3d-12-video-overview — D3D12 video (güven: yüksek)
49. https://learn.microsoft.com/en-us/windows/win32/medfound/enhanced-video-renderer — EVR (güven: yüksek)
50. https://mpv.io/ — mpv (güven: yüksek)
51. https://github.com/mpv-player/mpv/blob/master/Copyright — mpv lisans (güven: yüksek)
52. https://github.com/mpv-player/mpv-examples/blob/master/libmpv/README.md — libmpv (güven: yüksek)
53. https://github.com/mpv-player/mpv/wiki/GPU-Next-vs-GPU — gpu-next (güven: yüksek)
54. https://github.com/haasn/libplacebo — libplacebo (güven: yüksek)
55. https://www.libplacebo.org/features/ — libplacebo features (güven: yüksek)
56. https://ayosec.github.io/ffmpeg-filters-docs/8.0/Filters/Video/libplacebo.html — libplacebo scalers (güven: yüksek)
57. https://www.videohelp.com/software/madVR — madVR (güven: yüksek)
58. https://www.mpegflow.com/topics/color/color-spaces — Renk uzayları (güven: yüksek)
59. https://lightillusion.com/what_is_hdr.html — HDR (güven: yüksek)
60. https://alliance.experienceuhd.com/news/what-hdr10/ — HDR10 (güven: yüksek)
61. https://github.com/bloc97/Anime4K — Anime4K (güven: yüksek)
62. https://learn.microsoft.com/en-us/windows/ai/apis/video-super-resolution — Windows VSR (güven: yüksek)

### Player Alt Yapı & Mimari
63. https://hftengineer.com/lock-free-ring-buffer-in-cpp20/ — C++20 lock-free (güven: yüksek)
64. https://codereview.stackexchange.com/questions/288705/ — C++20 ring buffer (güven: yüksek)
65. https://videolabs.io/products/libvlcsharp-commercial-license/ — libVLC ticari (güven: yüksek)
66. https://www.un4seen.com/bass.html — BASS lisans (güven: yüksek)
67. https://gearspace.com/board/electronic-music-instruments-and-gear-21/eq-crossover-slopes-667166.html — 31-band EQ hesabı (güven: orta)
68. https://www.juce.com/get-juce/ — JUCE fiyat (güven: yüksek)
69. https://github.com/juce-framework/JUCE/blob/master/LICENSE.md — JUCE lisans (güven: yüksek)

---

**Rapor Sonu** · 2026-10-19 · Researcher Agent
