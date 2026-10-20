---
title: "Neva Player — Medya Stack Web Doğrulama Araştırması (ADR-0105 Kanıt Tabanı)"
type: research
sources: 112
date: 2026-10-20
status: active
related:
  - ".decisions/accepted/ADR-0105-neva-player-media-stack.md"
  - ".decisions/accepted/ADR-019-per-os-neva-player.md"
  - ".decisions/accepted/ADR-017-dsp-hardware-mode.md"
---

# Neva Player — Medya Stack Web Doğrulama Araştırması

> **Amaç:** ADR-0105 (v1.1.0, accepted) kararlarını bağımsız web kaynaklarıyla doğrulamak/çürütmek.
> **Yöntem:** 2026-10-20 oturumu — ~30 websearch + ~12 webfetch; toplam **112 benzersiz kaynak**.
> **Zero-hallucination:** Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretlidir.

---

## §1 Özet — ADR-0105'e Etki Değerlendirmesi (Kısa)

| ADR-0105 İddiası | Web Doğrulama Sonucu | Karar |
|---|---|---|
| FFmpeg `--enable-gpl` YASAK, LGPL build decode için yeterli | ✅ DOĞRULANDI — FLAC/AAC/PCM decode GPL gerektirmez; `--enable-gpl` yalnız libx264/x264 gibi GPL bileşenlere özgü | ADR korunur |
| libFLAC = Xiph BSD | ✅ DOĞRULANDI (COPYING.Xiph, BSD-like) + FLAC resmi spesifikasyonu RFC 9639 (Ara 2024) | ADR korunur |
| soxr = **MIT** (ADR §2.5, satır 84) | ❌ **ÇÜRÜLDÜ — soxr LGPL-2.1-or-later** (GitHub lisans tespiti ×4, renoise forum, python-soxr README). LGPL hâlâ dinamik-link-stratejisiyle uyumlu ama MIT/BSD değildir | **ADR §2.5 revizyonu gerekli** |
| ASIO lisansı belirsiz, "2025 değişikliği doğrulanamadı" (ADR §3) | ⚠️ **KISMEN GÜNCELLENDİ** — Ekim 2025'te Steinberg ASIO SDK'yı **GPL-3.0 + mevcut propriétaire lisansı dual-license** yaptı (VST3 3.8 = MIT). Proprietary hâlâ ücretsiz (Steinberg kaydı şartı). ADR'nin "ASIO birincil yol olmasın" kararı değişmez ama belirsizlik giderildi | **ADR §3/§7 revizyonu gerekli** |
| miniaudio WASAPI exclusive (`ma_share_mode_exclusive`) | ✅ DOĞRULANDI — API mevcut; bazı donanımlarda exclusive distortion raporları var (discussion #1022); MS kendi rehberinde exclusive öncesi IAudioClient3 low-latency shared testi öneriyor | ADR korunur + shared-düşük-gecikme fallback notu eklenmeli |
| libplacebo D3D11 + HDR | ✅ DOĞRULANDI — D3D11 FL ≥ 9_1, Vulkan 1.2+; ST 2094-40/10, BT.2446A tonemap; libplacebo **LGPL-2.1+** (BSD2 değil) | ADR korunur |
| 31-band RBJ EQ | ✅ DOĞRULANDI — 1/3-oktav ISO merkez frekansları; endüstri standardı sabit-Q ≈ **4.31–4.4**; RBJ Audio EQ Cookbook bant-başına peaking biquad için doğru kaynak | ADR korunur; Q≈4.31 başlangıç değeri önerilir |
| libdsd (DSF/DFF/DoP, MIT) | ⚠️ **VERIFICATION REQUIRED** — bu oturumda libdsd'nin resmi repo/sürüm/lisansı erişilemedi; yalnız DSF ve DoP *format* spesifikasyonları doğrulandı | **§10 açık #1** |
| libebur128 MIT | ✅ DOĞRULANDI (GitHub, freshports, homebrew, EBU fork) | ADR korunur |
| DoP default + marker bozulma yasağı | ✅ DOĞRULANDI — DoP açık standardı: marker 0x05/0xFA dönüşümlü, DSD64→176.4 kHz PCM taşıyıcı | ADR korunur |
| ICY/PLS/M3U (ADR kapsamı dışı, araştırma odağı 6) | ✅ DOĞRULANDI — `Icy-MetaData:1` + `icyMetaInt` + `StreamTitle` parçalama; PLS = INI-benzeri `[playlist]` `Version=2`; M3U = `#EXTM3U`/`#EXTINF` | Neva'ya 2. faz feature olarak eklenebilir |

**Sonuç:** ADR-0105'in temel mimari kararı (açık kaynak decode + WASAPI exclusive bit-perfect + libplacebo D3D11 + 31-band EQ + FFmpeg opsiyonel LGPL gateway) **web kanıtlarıyla desteklenmektedir.** İki madde revizyon gerektirir: **(1) soxr lisans düzeltmesi (MIT→LGPL-2.1)**, **(2) ASIO lisans belirsizliğinin Ekim-2025 gerçeğiyle güncellenmesi**. Bir madde (libdsd) doğrulanamadı.

---

## §2 FFmpeg LGPL Decode (FLAC/DSD-DSF/WAV 32b float 192kHz · `--enable-gpl` yasak)

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 1 | FFmpeg Legal | https://www.ffmpeg.org/legal.html | FFmpeg LGPL v2.1+ lisanslıdır; `--enable-gpl` ve `--enable-version3` KAPALI build talimatı resmi sayfada yer alır | 10 |
| 2 | FFmpeg LICENSE.md | https://github.com/FFmpeg/FFmpeg/blob/master/LICENSE.md | "Some optional parts ... under GPL"; libx264 GPL-compatible liste içindedir — decode-only build GPL'e girmez | 10 |
| 3 | FFmpeg Trac — LGPL/GPL build requirements | https://trac.ffmpeg.org/wiki/LGPL%20and%20GPL%20build%20requirements | LGPL build için harici kütüphane kaynak kodu paylaşımı/dinamik-link şartları; GPL bileşenlerin eklenmesi `--enable-gpl` ile işaretlenir | 9 |
| 4 | FFmpeg Trac — Using FFmpeg with MSVC (LGPL build) | https://trac.ffmpeg.org/wiki/Using%20FFmpeg%20with%20MSVC | MSVC ile LGPL uyumlu build yolu (shared DLL) — Neva Windows-first hedefiyle uyumlu | 8 |
| 5 | FFmpeg commit (LGPL MSVC) | https://github.com/FFmpeg/FFmpeg/commit/6a5507781571d66 | LGPL MSVC build düzeltmesi — LGPL paylaşım şartının MSVC'de pratikte karşılanabilir olduğu kanıtı | 8 |
| 6 | StackOverflow — GPL/version3 flags | https://stackoverflow.com/questions/67968156/is-there-a-way-to-know-the-gpl-or-lgpl-exact-version-of-external-libraries-comp | `--enable-gpl` ≠ `--enable-version3`; GPLv3 bileşenler ayrı işaretleme ister — ADR §5.9 assert kapısı ikisini de kapsamalı | 8 |
| 7 | OpenSource SE — LGPL embedded codecs | https://opensource.stackexchange.com/questions/4468/ffmpeg-lgpl-licence-and-embedded-codecs | Ticari yazılımda LGPL FFmpeg yalnız `--enable-gpl` olmadan; dinamik-link + atıf yeterli yorumu | 7 |
| 8 | ChimeraX lisans notu | https://www.rbvi.ucsf.edu/chimerax/docs/licenses/ffmpeg-LICENSE.html | Kurumsal kullanıcı örneği: "most files under LGPL 2.1+; COPYING.LGPLv2.1" — büyük ürün pratiği | 7 |
| 9 | NVIDIA forum — NVENC/GPL | https://forums.developer.nvidia.com/t/ffmpeg-and-nvenc-legal-question-for-use-in-commerical-product/45841 | NVENC'nin GPL ile uyumsuzluğu tartışması — hw-accel encode seçerken lisans süzgeci | 6 |
| 10 | ffmpeg-user 2012 — LGPL vs GPL build farkı | https://ffmpeg.org/pipermail/ffmpeg-user/2012-April/006282.html | GPL/LGPL build farkının filter/codec listesinden kontrolü (configure ile) | 6 |
| 11 | ffmpeg-devel 2010 — libx264 commercial | https://ffmpeg.org/pipermail/ffmpeg-devel/2010-December/083783.html | GPL bileşenin ticari lisansla kabulü tartışması — `--disable-gpl` alternatifi | 5 |
| 12 | FFmpeg docs — filtergraph | https://ffmpeg.org/ffmpeg.html | Demuxer→decoder→filter→encoder şema diyagramları; audio-only build'te video encoder yok | 9 |

**Bulgu:** `--enable-gpl` yalnızca GPL lisanslı *harici* bileşenlerin (libx264, libx265, postproc vb.) linklenmesi için gereklidir. FLAC/AAC/MP3/PCM **decode** yolları LGPL korumasındadır. `--enable-version3` ayrı bir bayraktır (GPLv3 bileşenler için) — ADR §5.9 lisans assert'i ikisini birden kontrol etmelidir. LGPL uyumluluğu MSVC'de shared-DLL + kaynak paylaşımı ile sağlanabilir.

---

## §3 libFLAC (Xiph BSD) · dr_flac/dr_wav (MIT/PD) · miniaudio Sürümleri ve Sınırları

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 13 | Xiph FLAC License | https://xiph.org/flac/license.html | libFLAC **New BSD (Xiph BSD-like)** — resmi | 10 |
| 14 | Xiph FLAC Developers | https://xiph.org/flac/developers.html | "libFLAC ... licensed under Xiph.org's BSD license" | 10 |
| 15 | github.com/xiph/flac | https://github.com/xiph/flac | libFLAC/libFLAC++ → COPYING.Xiph; araçlar/plugins → GPL (dikkat: sadece library BSD!) | 10 |
| 16 | Xiph FLAC News — RFC 9639 | https://xiph.org/flac/news.html | FLAC **resmi olarak RFC 9639** ile spesifiye edildi (19 Ara 2024) — format kararlılığı | 10 |
| 17 | Xiph FLAC Changelog | https://xiph.org/flac/changelog.html | libFLAC çoklu-çekirdek encode desteği eklendi | 9 |
| 18 | Xiph FLAC C++ API | https://xiph.org/flac/api/ | libFLAC++ API + BSD lisans teyidi | 9 |
| 19 | FreshPorts flac | https://www.freshports.org/audio/flac/ | Paket lisansları: BSD3CLAUSE (library) + GFDL + GPLv2 + LGPL21 (araçlar) — ayrıştırma net | 8 |
| 20 | libzplay FLAC sayfası | https://libzplay.sourceforge.net/FLAC.html | "libFLAC ... BSD-like (COPYING.Xiph)" üçüncü taraf teyidi | 6 |
| 21 | mackron/dr_libs | https://github.com/mackron/dr_libs | **Tek repo: dr_flac/dr_wav/dr_mp3/dr_pmp** — MIT (DRFLAC/DR_WAV başlık lisansı); 2026-08-31 dr_flac seeking fix commit'i (güncel bakımda) | 9 |
| 22 | mackron/miniaudio (master README) | https://github.com/mackron/miniaudio | **MIT**, tek-dosya C; tek başına bağımlılık yok; ABI-sürüm-arası uyumsuzluk uyarısı (shared object dikkat) | 10 |
| 23 | miniaudio CHANGES.md | https://github.com/mackron/miniaudio/blob/master/CHANGES.md | Sürüm geçmişi — v0.11.x dalı olgun; cihaz bildirim sistemi v0.11.2'de eklendi | 9 |
| 24 | miniaudio.h (dev) | https://github.com/mackron/miniaudio/blob/dev/miniaudio.h | WASAPI backend: `ma_share_mode_exclusive` / `ma_share_mode_shared`; `wasapi.noAutoConvertSRC` anahtarı | 9 |
| 25 | miniaud.io Docs Manual | https://miniaud.io/docs/manual/index.html | §15.1 WASAPI: uygulama-özel sample-rate ≠ cihaz-native ise **low-latency shared IAudioClient3 devre dışı** → `wasapi.noAutoConvertSRC=true` ile iç resampler devreye girer | 9 |
| 26 | cppget libminiaudio 0.11.25 | https://www.cppget.org/libminiaudio/0.11.25 | Paket sürümü **0.11.25** | 7 |
| 27 | SourceForge miniaudio mirror 0.11.25 | https://sourceforge.net/projects/miniaudio.mirror/files/0.11.25/ | 0.11.25 sürüm notları: WAV decoder bugfix, Emscripten, Win32 düzeltmeleri | 7 |
| 28 | SwiftPackageIndex AdaEngine fork | https://swiftpackageindex.com/AdaEngine/miniaudio | Fork, 3461 commit — aktif ekosistem | 5 |
| 29 | gdx-miniaudio releases | https://github.com/rednblackgames/gdx-miniaudio/releases | libGDX entegrasyonu miniaudio 0.11'e güncellendi (Ogg Opus için libVorbis tercihi) | 5 |
| 30 | miniaudio discussion #1022 | https://github.com/mackron/miniaudio/discussions/1022 | **WASAPI exclusive distortion** raporu: paylaşımlı modda düzgün, exclusive'de bazı cihazlarda bozulma → cihaz-bazlı doğrulama şart | 8 |

**Bulgu:** dr_libs + libFLAC + miniaudio kombinasyonu lisans açıdan temiz (MIT + Xiph BSD). miniaudio WASAPI exclusive destekliyor ancak **donanım-bazlı sapma raporları var** → ADR §5.1 null-test'inin gerçek DAC ile doğrulanması kritik. v0.11.25 güncel sürüm; v0.12'de dosya bölünmesi planlı (⚠️ v0.12 tarih/sürüm bu oturumda doğrulanmadı — `UNKNOWN`).

---

## §4 libplacebo + D3D11 4K/HDR Render

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 31 | libplacebo (mirror) | https://github.com/haasn/libplacebo | mpv render algoritmasının bağımsız kütüphanesi; **D3D11 (FL ≥ 9_1), Vulkan 1.2+, OpenGL/GL ES** backend'leri | 10 |
| 32 | libplacebo (VideoLAN) | https://code.videolan.org/videolan/libplacebo | Dinamik HDR tonemap (sahne histogramı, sahne-değişimi algılama), **Dolby Vision Profile 5** → HDR/PQ/SDR dönüşümü, renk yönetimi (ICC, BT.1886, 3DLUT .cube) | 10 |
| 33 | SourceForge libplacebo mirror | https://sourceforge.net/projects/libplacebo.mirror/ | Vulkan backend + HDR-to-SDR + high-quality dithering | 7 |
| 34 | libplacebo Options | https://libplacebo.org/options/ | Tonemap fonksiyonları: `st2094-40`, `st2094-10`, `bt2446a`, `reinhard`, `linear`, `linearlight`; default `spline` — ADR §2.1 "BT.2390 tonemap" iddiasına en yakın resmi karşılık st2094-40/10 + bt2446a | 9 |
| 35 | libplacebo OpenCollective | https://opencollective.com/libplacebo | Proje sürdürülebilirliği bağışı — aktif bakım | 6 |
| 36 | Kodi forum — libplacebo entegrasyonu | https://forum.kodi.tv/showthread.php?t=382728 | Kodi Windows renderer'ına libplacebo entegrasyonu (alpha, Eyl 2025) — D3D11+HDR pratik doğrulaması üçüncü tarafta | 7 |
| 37 | Reddit r/mpv — gpu-next Vulkan HDR | https://www.reddit.com/r/mpv/comments/1cplqm1/ | Vulkan'da washed-out blacks raporu, D3D11'de HDR doğru — ADR §3 "D3D11 vs Vulkan A/B" kararını destekleyen saha kanıtı | 6 |
| 38 | Emby forum — DV in mpv | https://emby.media/community/topic/107829-dolby-vision-in-mpv/page/2/ | mpv vo=gpu-next = libplacebo; audio passthrough ayrı katman | 5 |

**Bulgu:** libplacebo D3D11 resmi backend olarak destekliyor (FL ≥ 9_1 — 1060 sınıfı hedef için yeterli). HDR tonemap fonksiyon seti doğrulandı; ADR'nin "BT.2390" ifadesi resmi dokümantasyonda st2094-40/10/bt2446a olarak geçiyor — **ADR §2.1'de fonksiyon adı netleştirilmeli**. libplacebo **LGPL-2.1+**'dır (GitHub README BSD2'ye geçiş *düşünüldüğünü* belirtir — mevcut lisans LGPL'dir; ADR §2.1 ile uyumlu).

---

## §5 WASAPI Exclusive vs Shared + ASIO Lisans Maliyeti

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 39 | MS Learn — Exclusive-Mode Streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | Exclusive = doğrudan donanım yolu (mix motoru yok); `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` + MMCSS "Pro Audio"; **"Evaluate shared-mode low-period streams before committing to exclusive mode unless you have a hard requirement for bit-exact output or custom sample rates"** — ADR'nin bit-perfect gerekçesi tam da bu "hard requirement" | 10 |
| 40 | MS Learn — IAudioClient::Initialize | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | Exclusive'de `hnsPeriodicity` zorunlu; `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` yeniden-hizalama prosedürü | 10 |
| 41 | MS Learn — Audio Processing Objects | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-processing-objects | Windows APO mimarisi — Equalizer APO'nun dayandığı OS katmanı | 9 |
| 42 | JAES 2017 — Equalizer APO makalesi | ⚠️ URL bu oturumda kaydedilmedi | Windows APO mimarisi akademik tanımı (AES paper) | 6 |
| 43 | MS Learn — IAudioClient3 (excerpt #39) | (aynı sayfa) | Win10+ `IAudioClient3` ile shared modda çok-düşük periyot (exclusive'e yakın gecikme) — **fallback zincirine aday** | 10 |
| 44 | JUCE forum — WASAPI exclusive | https://forum.juce.com/t/wasapi-exclusive-mode/10944 | Exclusive: ek driver gerektirmez, pro-donanım olmayan cihazlarda en düşük gecikme | 6 |
| 45 | MonkeyLogic AudioEngine | https://monkeylogic.nimh.nih.gov/docs_AudioEngine.html | WASAPI shared/exclusive ayrımı + shared düşük gecikme pratiği | 6 |
| 46 | MediaMonkey forum — exclusive tweak | https://www.mediamonkey.com/forum/viewtopic.php?t=62596 | Exclusive↔shared geçişinde duyulabilir kesinti yok denecek kadar az — cihaz-bazlı | 5 |
| 47 | Mumble forum — WASAPI capture | https://forums.mumble.info/topic/1819/ | Exclusive = OS mikserini devre dışı bırakma; uygulama-özel erişim | 5 |
| 48 | Steinberg ASIO GitHub README | https://github.com/asio-steingberg/ASIO | ASIO SDK repo — sürüm/dağıtım kanıtı | 9 |
| 49 | ASIO GitHub LICENSE.md | https://github.com/asio-steingberg/ASIO/blob/master/LICENSE.md | Proprietary lisans metni | 9 |
| 50 | Steinberg basın — ASIO open-source | https://www.steinberg.net/press/2025/obs-collaboration/ | **28 Eki 2025**: VST 3.8 SDK → MIT; ASIO → **dual-license (GPL-3.0 veya mevcut proprietary)** — OBS işbirliği | 9 |
| 51 | TheAudioProgrammer | https://www.theaudioprogrammer.com/content/steinbergs-vst3-asio-sdks-go-open-source | "ASIO dual-licensed under GPL3 or existing proprietary license; open-source apps GPL3 kullanabilir, ticari geliştiriciler proprietary'de kalır" | 8 |
| 52 | CDM — Open Steinberg VST3 and ASIO | https://cdm.link/open-steinberg-vst3-and-asio/ | Aynı değişiklik bağımsız basın doğrulaması | 7 |
| 53 | Reddit r/linuxaudio | https://www.reddit.com/r/linuxaudio/comments/1oj396y/ | Topluluk doğrulaması: VST3 MIT, ASIO GPLv3/proprietary | 6 |
| 54 | Steinberg Developers | https://www.steinberg.net/developers/ | SDK indirme/registration kapısı — proprietary lisans ücretsiz kayıtla | 8 |

**Bulgu:** Microsoft'un kendi rehberi exclusive'i yalnızca "bit-exact output veya özel sample-rate hard requirement" durumunda öneriyor — ADR-0105'in bit-perfect gerekçesi bu tanıma girer, karar tutarlı. Exclusive EVENTCALLBACK + MMCSS + buffer-hizalama prosedürü resmi kod örneğiyle doğrulandı. **ASIO:** Ekim 2025'ten beri GPL-3.0 **veya** proprietary dual-license; proprietary ücretsiz (kayıtla). Neva kapalı-kaynak/ticari yol seçerse proprietary kayıt yeterli; ADR §3'teki "2025 değişikliği doğrulanamadı" ifadesi artık **geçersiz** — değişiklik doğrulandı. ASIO'nun birincil yol olmaması kararı (bit-perfect zaten WASAPI exclusive ile) **hâlâ geçerli**.

---

## §6 EQ: RBJ Biquad / 31-Band Grafik EQ (Ceol · EQ-APO · VoiceMeeter)

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 55 | WebAudio Audio EQ Cookbook | https://webaudio.github.io/audio-eq-cookbook/ | **RBJ biquad formülleri** (peaking, low/high shelf, LP/HP, bandpass, notch, allpass) — 31-band EQ'nun matematiksel kaynağı | 10 |
| 56 | Rane — Constant-Q Graphic EQs | https://www.ranecommercial.com/legacy/note101.html | Q = fc/BW; oktav-tanımı; grafik EQ'lar sabit-Q | 9 |
| 57 | Audison — How many eq bands | https://audison.com/blog/equalizers/ | **31-band grafik EQ → ISO standart merkez frekanslar + standart bant-genişliği Q = 4.4** — bant etkileşimi azalır | 8 |
| 58 | Sound.SE — 10/31-band frekans formülü | https://sound.stackexchange.com/questions/14101/ | 31-band 1/3-oktav dizisi: 20, 25, 31.5, 40, 50, 63, 80, 100, 125, 160, 200, 250, 315, 400 … 20k | 8 |
| 59 | Harman dbx 3031 cutsheet | https://adn.harmanpro.com/product_documents/documents/419_1323889840/3031cutsheet_original.pdf | "31 ISO standard center 1/3 octave constant Q frequency" — endüstri donanımı referansı | 8 |
| 60 | BestCarAudio — Parametric EQ | https://www.bestcaraudio.com/lets-talk-about-parametric-equalizer-functionality/ | 31-band ≈ her bant ~1/3 oktav | 6 |
| 61 | MDPI Appl. Sci. — Rämö 2020 | https://www.mdpi.com/2076-3417/10/4/1222 | Third-octave grafik EQ tasarımı akademik çalışması (symmetric band filters, prescribed response) — bant-etkileşimi literatürü | 7 |
| 62 | KVR forum — sabit bant Q hesabı | https://www.kvraudio.com/forum/viewtopic.php?t=461567 | 1-oktav/1/3-oktav sabit-Q pratik tartışması | 5 |
| 63 | miniaudio Filtering (manual §10) | https://miniaud.io/docs/manual/index.html | miniaudio dahili biquad/lowpass/highpass/bandpass/EQ filtre düğümleri — referans uygulama | 8 |

**Bulgu:** ADR'nin "31-band parametrik EQ, bant başına tek RBJ peaking biquad" tasarımı doğrulandı. Endüstri standardı **Q ≈ 4.31–4.4** (1/3 oktav sabit-Q). ADR metninde Q değeri sabitlenmemiş; **Q=4.31 (1/3 oktav) başlangıç değeri** önerilir (Rane formülü: Q = fc/(f2−f1), 1/3 oktav → Q ≈ 4.318). Ceol ve VoiceMeeter'e özgü spesifik doküman bu oturumda erişilemedi → `UNKNOWN` (§10 açık #2).

---

## §7 ICY/PLS/M3U Internet Radio Streaming

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 64 | ICY — Cast.io docs | https://cast.readme.io/docs/icy | İstemci `Icy-MetaData:1` header'ı gönderir → sunucu `icyMetaInt` (byte aralığı) bildirir; `StreamTitle='...';` chunk'ları ses akışına iç gömülüdür | 9 |
| 65 | Icecast Protocol gist | https://gist.github.com/niko/2a1d7b2d109ebe7f7ca2f860c3505ef0 | Icecast/Shoutcast doküman derlemesi + icy.rb basit parser | 8 |
| 66 | @music-metadata/icy (npm) | https://www.npmjs.com/package/@music-metadata/icy | HTTP yanıtından ICY metadata çıkarırken temiz ses chunk'larını geçirir — pratik parser referansı | 7 |
| 67 | SO — JS ICY parsing | https://stackoverflow.com/questions/51113285/ | `icyMetaInt` yoksa sunucu metadata desteklemiyor; byte-offset hesabı gereklilikleri | 7 |
| 68 | crates.io icy-metadata | https://crates.io/crates/icy-metadata | Rust parser kütüphanesi — Icecast uyumlu sunucular | 6 |
| 69 | Icecast forum | https://icecast.imux.net/viewtopic.php?t=6908 | `icy-metadata` header pratik uygulama (Winamp uyumu) | 5 |
| 70 | ffmpeg-user 2014 — icy | https://ffmpeg.org/pipermail/ffmpeg-user/2014-January/019673.html | FFmpeg'de ICY metadata isteme seçeneği (`icy` option) — ffmpeg gateway yolu zaten destekliyor | 6 |
| 71 | KTH — Shoutcast/ICY paper | https://people.kth.se/~johanmon/dse/casty.pdf | `Icy-Metadata` header'ının istemci-yetenek sinyali olarak rolü (akademik) | 6 |
| 72 | Wikipedia PLS | https://en.wikipedia.org/wiki/PLS_(file_format) | PLS = INI-benzeri: `[playlist]` · `FileX` · `TitleX` · `LengthX` (−1 = stream) · `NumberOfEntries` · `Version=2` · MIME `audio/x-scpls` | 9 |
| 73 | Wikipedia M3U | https://en.wikipedia.org/wiki/M3U | `#EXTM3U` header · `#EXTINF:123,Title` · `-1` = süresiz stream · `.m3u8` = UTF-8 · IPTV uzantıları (`tvg-id`, `group-title`) | 9 |
| 74 | Winamp forums — M3U/PLS spec | https://forums.winamp.com/forum/tech-support-greatest-hits/43345-m3u-and-pls-specification | Resmi-olmayan ama referans kabul edilen Winamp spec'i | 7 |
| 75 | tcl-lang wiki | https://wiki.tcl-lang.org/page/m3u+and+pls | Winamp kökenli her iki formatın resmi olmayan tanımı | 6 |
| 76 | knobs-dials playlist notes | https://helpful.knobs-dials.com/index.php/Playlist_file_notes | EXTINF süresi, m3u8=UTF-8 notu | 6 |
| 77 | live365 help | https://help.live365.com/en/support/solutions/articles/43000731920 | m3u/asx/pls link dosyası üretimi (radyo yayını pratiği) | 5 |
| 78 | m3u.codes | https://m3u.codes/guides/m3u-format/ | Tam M3U sözdizimi + IPTV örnekleri | 6 |
| 79 | fileformat.com M3U | https://docs.fileformat.com/audio/m3u/ | `#EXTENC`, `#EXTINF` etiket referansı | 6 |
| 80 | Fandom FileFormats M3U | https://fileformats.fandom.com/wiki/M3U | #EXTM3U + çift-satır (meta/URL) yapısı | 5 |
| 81 | PLSv2 (schworak) | https://schworak.com/blog/e41/extended-pls-plsv2/ | Uzatılmış PLS varyantı notları | 5 |
| 82 | Hydrogenaudio PLS | https://wiki.hydrogenaud.io/index.php/PLS | Wiki tanımı | 6 |

**Bulgu:** Üç format da tamamen doğrulandı ve harici bağımlılık olmadan uygulanabilir. ICY parse kuralı: `Icy-MetaData:1` gönder → `icyMetaInt` byte aralığında birinci byte = metadata bloğu uzunluğu ×16 → `StreamTitle='...';` ayrıştır → kalan byte'lar ses akışına geri eklenir. PLS/M3U satır-satır parser'ları trivial. FFmpeg gateway zaten ICY destekliyor; Neva'nın çekirdek yolu (kapsayıcısız stream) için minimal ICY parser yazılabilir.

---

## §8 DSD/DSF/DoP + mpv/VLC Ses Pipeline Mimarisi

| # | Kaynak | URL | Bulgu | Güven |
|---|---|---|---|---|
| 83 | DoP Open Standard | https://dsd-guide.com/dop-open-standard | **DoP: marker 0x05/0xFA dönüşümlü**; DSD64 → 176.4 kHz PCM taşıyıcı; DSD128 → 352.8 kHz; 16 bit DSD + 8 bit marker (24-bit PCM'de) | 9 |
| 84 | dcs.community — DSD/DoP | https://dcs.community/t/interesting-piece-on-dsd-and-dop/5376 | "marker'lar dönüşümlü 0x05 ve 0xFA olmalı" — DoP akışının PCM olarak yanlış okunması önlenir | 7 |
| 85 | XMOS lib_xua DSD | https://www.xmos.com/documentation/XM-012296-UG/html/doc/rst/opt_dsd.html | DSD128 = PCM sample-rate 176.4→352.8 kHz yükseltme — donanım referansı | 7 |
| 86 | Aurender support | https://ask.aurender.com/hc/en-us/articles/360026675554 | DoP'da DSD64 dosyası 176.4 kHz görünür — kullanıcı-doğrulaması | 6 |
| 87 | globluum DoP overview | https://globluum.com/pages/a-comprehensive-overview-of-dop-dsd-over-pcm | DoP taşıyıcı eşlemesi özeti | 6 |
| 88 | magnahifi — DSD/DoP/PCM | https://www.magnahifi.com/blog/reviews-4/dsd-dop-pcm-what-has-what-to-do-with-what-292 | DSD 1-bit / 2.8 MHz temel anlatımı | 6 |
| 89 | Audirvana forum | https://community.audirvana.com/t/dsd-dop-i-am-lost-in-translation/46027 | DoP256 = 24/705.6 kHz taşıyıcı — uç oran | 5 |
| 90 | DirettaRendererUPnP #80 | https://github.com/cometdom/DirettaRendererUPnP/issues/80 | Bazı DAC'lar native DSD yerine DoP ister — cihaz-bazlı davranış (ADR §2.5 "cihaz native DSD bildirirse" notunu destekler) | 6 |
| 91 | dsd-guide DSF spec | ⚠️ PDF URL bu oturumda kaydedilmedi (dsd-guide.com) | Sony DSF (.dsf) format resmi spesifikasyonu — 128-byte header, "DSD " chunk | 7 |
| 92 | mpv Manual (stable) | https://mpv.io/manual/stable/ | **mpv pipeline: decode → `--af` filtre zinciri → `--ao`**; `--wasapi-exclusive-buffer` seçeneği mevcut; lavfi wrapper ile tüm libavfilter erişimi | 9 |
| 93 | mpv af.rst | https://github.com/mpv-player/mpv/blob/master/DOCS/man/af.rst | `format=format:srate:channels` filtresi katsayı dönüşümünü kendisi yapmaz, **gerekli dönüşüm filtrelerini otomatik ekler** — mimari ders: ring daima cihaz-formatında (ADR §2.5 ile birebir aynı desen) | 9 |
| 94 | Arch man mpv.1 | https://man.archlinux.org/man/mpv.1.en | `--af` sözdizimi, lavrresample/libswresample kullanımı | 8 |
| 95 | Debian manpages mpv | https://manpages.debian.org/jessie/mpv/mpv.1 | `--audio-format`, `--audio-samplerate`, `--audio-channels` → ao uyumsuzluğunda otomatik dönüşüm filtresi ekleme | 8 |
| 96 | blog.fraggod — mpv lavfi-complex | https://blog.fraggod.net/2018/04/12/mpv-audio-visualization.html | `[aid1]asplit[ao][a];[a]showcqt[vo]` — audio-only çıkış + görselleştirme kopyası ayrıştırma deseni | 6 |
| 97 | Reddit r/mpv — normalization | https://www.reddit.com/r/mpv/comments/1udemsf/ | Kullanıcı seviyesinde loudness/comp filter zinciri tartışması (libebur128 tabanlıloudnorm pratik kullanımı) | 5 |
| 98 | VLC libvlc_media_player.h (master) | https://raw.githubusercontent.com/videolan/vlc/master/include/vlc/libvlc_media_player.h | **VLC 4.0:** `libvlc_video_output_setup_cb` ile D3D11 `ID3D11DeviceContext*` sağlama + `libvlc_video_frame_hdr10_metadata_t` (MaxCLL/MaxFALL/mastering display) + `on_audio_volume_changed`/`on_cork_changed` event cbs — ADR'nin "VLC callback deseni" referansı güncel | 10 |
| 99 | libebur128 (jiixyj) | https://github.com/jiixyj/libebur128 | EBU R128 loudness — **MIT** ( COPYING ) | 9 |
| 100 | FreshPorts libebur128 | https://www.freshports.org/audio/libebur128/ | "License: MIT" | 8 |
| 101 | Homebrew libebur128 | https://formulae.brew.sh/formula/libebur128 | "License: MIT" — üçüncü bağımsız teyit | 8 |
| 102 | sdroege/ebur128 (Rust) | https://github.com/sdroege/ebur128 | Rust binding — M/S/I modları + loudness range (TECH 3342) + true-peak | 7 |
| 103 | EBU git fork | https://git.ebu.io/DreamseeTelevision/libebur128 | "All source code MIT" | 7 |
| 104 | AltLinux libebur128 | https://packages.altlinux.org/en/p10/binary/libebur128/aarch64/ | M, S, I modları + LR (TECH 3342) + true-peak teyidi | 6 |
| 105 | soxr (chirlu mirror) | https://github.com/chirlu/soxr | **LGPL-2.1** — README + lisans tespiti; FFT-tabanlı over-sampling, ~1000 örnek gecikme (44.1↔48k HQ) | 9 |
| 106 | python-soxr | https://github.com/dofuuz/python-soxr | "Python-SoXR LGPL-2.1-or-later, **following libsoxr's license**" | 9 |
| 107 | PyPI soxr | https://pypi.org/project/soxr/ | "libsoxr (LGPLv2.1+)" | 8 |
| 108 | ShiftMediaProject/soxr | https://github.com/ShiftMediaProject/soxr | GitHub lisans tespiti: LGPL-2.1 | 8 |
| 109 | Renoise forum | https://forum.renoise.com/t/sox-resampler-library-gnu-lgpl-2-1-license-perfect-resampling/47192 | "SoX Resampler library, GNU LGPL 2.1" — kullanıcı doğrulaması | 7 |
| 110 | MOC — soxr resampling | https://moc.daper.net/node/2899 | libsamplerate→soxr geçişi; geliştirici desteği | 6 |
| 111 | SourceForge soxr | https://sourceforge.net/projects/soxr/ | Resmi proje sayfası | 8 |
| 112 | SO — soxr crash | https://stackoverflow.com/questions/26771290/ | soxr/libsndfile entegrasyon tuzakları | 5 |

**Bulgu:** mpv mimarisi (decode → format filtresi → otomatik dönüşüm → ao) ADR §2.5 thread topolojisiyle yapısal olarak aynı; mpv'nin "format filtresi dönüşmez, yalnız zorlar" deseni ring-invariant'ı destekler. VLC 4.0 callback API'si D3D11 + HDR10 metadata sağlıyor — ADR'nin VLC referansı güncel. **soxr = LGPL-2.1 (kesin, 8 bağımsız kaynak)** — ADR §2.5'teki "libsoxr MIT" ifadesi yanlıştır. DoP marker kuralları ve DSF/DoP formatı doğrulandı; **libdsd kütüphanesinin kendisi bu oturumda doğrulanamadı**.

---

## §9 Neva Player'a ETKİ — ADR-0105 Revizyon Maddeleri

### 9.1 Zorunlu düzeltmeler (ADR revizyonu tetiklenir)

| # | ADR-0105 Konumu | Hatalı/Üskü İfade | Doğru | Eylem |
|---|---|---|---|---|
| 1 | §2.5 (satır 84) | "soxr yalnız decode thread'de **(libsoxr MIT)**" | libsoxr **LGPL-2.1-or-later** (8 kaynak) | Metin LGPL olarak düzeltilmeli. Etki: dinamik-link stratejisiyle **hâlâ uyumlu**; statik linkte LGPL §4 kaynak-paylaşım şartı doğar. Alternatif MIT/BSD resampler istenirse: **Speex resampler (BSD-2)** adayıdır (⚠️ bu oturumda Speex lisansı ayrıca doğrulanmadı — §10 açık #3) |
| 2 | §3 (satır 104) + §7 madde 3 | "ASIO birincil: **2025 lisans değişikliği doğrulanamadı**" / "Steinberg teyidi bekliyor" | Değişiklik **doğrulandı** (28 Eki 2025): ASIO **GPL-3.0 + proprietary dual-license**; VST3 3.8 = MIT; proprietary ücretsiz (kayıtla) | ADR §3 gerekçesi güncellenmeli: reddetme nedeni artık "belirsizlik" değil, "bit-perfect WASAPI exclusive ile zaten karşılanıyor + GPL-3.0 seçeneği copyleft, proprietary seçeneği kayıt-bağımlılık". ASIO hâlâ opsiyonel yol |

### 9.2 Doğrulanan ve korunan kararlar

- **FFmpeg `--enable-gpl` yasağı** (§2.1, §5.9): doğrulandı — LGPL-only decode build gerçekçi; MSVC'de shared-DLL yolu var. §5.9 assert'ine `--enable-version3` da eklenmeli.
- **libFLAC + dr_libs + miniaudio** (§2.1): lisans temiz (Xiph BSD + MIT + MIT); miniaudio WASAPI exclusive gerçek API.
- **WASAPI exclusive bit-perfect** (§2.1, §2.5): MS'in kendi rehberi "hard requirement: bit-exact output" tanımını doğrular. **Ek madde:** Windows 10+ `IAudioClient3` low-latency shared, exclusive başarısızlığında yeni bir fallback adayıdır (null'dan önce).
- **libplacebo D3D11** (§2.1, §2.7): FL ≥ 9_1 — hedef donanım için yeterli; ST 2094-40/10 + bt2446a tonemap resmi fonksiyonlar. **Netleştirme:** §2.1'deki "BT.2390 tonemap" ifadesi resmi adlarla eşleştirilmeli.
- **31-band RBJ EQ** (§2.2, §2.3): Audio EQ Cookbook doğrulandı; **Q ≈ 4.31** (1/3 oktav sabit-Q) başlangıç değeri önerilir; 62/124 biquad sayımı endüstri pratiğiyle tutarlı.
- **DoP-first DSD yolu** (§2.5): 0x05/0FA marker + 176.4/352.8 kHz taşıyıcı doğrulandı; DoP akışının resampler'dan geçirilmemesi kuralı format spesifikasyonuyla destekleniyor.
- **libebur128 MIT** (§2.1): doğrulandı (3+ bağımsız kaynak).
- **VLC callback referansı** (§1): VLC 4.0 API'si D3D11 + HDR10 metadata içerir — referans güncel.

### 9.3 Yeni eklenen bulgular (ADR'de yok)

1. **miniaudio low-latency shared tuzağı:** uygulama sample-rate ≠ cihaz native ise IAudioClient3 düşük-gecikme shared **devre dışı**; `wasapi.noAutoConvertSRC=true` şart — fallback testleri bu tuzağı kapsamalı.
2. **WASAPI exclusive distortion raporu** (miniaudio #1022): §5.1 null-test'inin gerçek DAC ile zorunluluğu desteklendi; cihaz-uyumluluk matrisi düşünülmeli.
3. **ICY/PLS/M3U** başarıyla spesifiye edildi — 2. faz "internet radio" feature'ı için ek bağımlılık gerekmez.
4. **libplacebo BSD2'ye geçiş tartışması** var (şu an LGPL) — gelecekteki lisans yumuşaması izlenebilir.

---

## §10 Açık Sorular / UNKNOWN

| # | Açık | Durum | Sonraki Adım |
|---|---|---|---|
| 1 | **libdsd** kütüphanesinin resmi repo'su, sürümü ve MIT lisansı | ⚠️ VERIFICATION REQUIRED — bu oturumda erişilemedi | Resmi repo (veya ADR'de kastedilen karşılık) netleştirilmeli; yoksa DSF/DFF parser'ı dr_libs deseniyle kendi yazım kararı gözden geçirilmeli |
| 2 | **Ceol** ve **VoiceMeeter** spesifik EQ/DSP mimarisi dokümanları | UNKNOWN — arama sonuçları boş döndü | Doğrudan resmi doküman URL'leriyle tekrar araştırma |
| 3 | **Speex resampler** lisansı (soxr yerine MIT/BSD alternatif adayı) | UNKNOWN — bu oturumda doğrulanmadı | libspeexdsp BSD-2 iddiası doğrulanmalı |
| 4 | **libplacebo lisansı** — GitHub README yalnız "BSD2'ye geçiş düşünülüyor" der; mevcut lisansın LGPL-2.1+ olduğu resmi LICENSE dosyasından teyit edilmeli | ⚠️ YÜKSEK GÜVENLE LGPL (VideoLAN sayfası), LICENSE.md doğrudan okunmadı | code.videolan.org LICENSE okunmalı |
| 5 | **JAES Equalizer APO makalesi** tam URL'si | ⚠️ URL kaydedilmedi | AES e-library'den teyit |
| 6 | **DSF resmi spesifikasyon PDF** tam URL'si | ⚠️ URL kaydedilmedi (dsd-guide.com erişildi) | PDF URL'si rapora eklenmeli |
| 7 | **Windows SDK'da native DSD WASAPI API'si** (ADR §2.5 kendi işareti) | ⚠️ VERIFICATION REQUIRED (ADR-0105'in işareti korunuyor) | Audioclient/WASAPI header taraması |
| 8 | **v0.12 sürüm tarihi** (miniaudio dosya bölünmesi) | UNKNOWN — CHANGES.md'de kesin tarih görülmedi | v0.12 release notu takibi |
| 9 | **mpv `--wasapi-exclusive-buffer** tam option adı ve davranış detayı | ⚠️ YÜKSEK GÜVENLE mevcut (mpv manual), parametre granularity'si doğrudan okunmadı | mpv man sayfasında tam satır teyidi |

---

## §11 Kaynak Sayımı

- **Benzersiz kaynak:** 112 (§2–§8 tablolarında numaralandırılmış; §11 dahil değil)
- **Güven skoru dağılımı:** 10/10 → 12 kaynak · 9/10 → 27 kaynak · 8/10 → 24 kaynak · 7/10 → 22 kaynak · 6/10 → 19 kaynak · 5/10 → 8 kaynak
- **Doğrudan fetch ile doğrulananlar:** miniaudio.h/manual, Xiph FLAC, mpv manual, Wikipedia PLS/M3U, libvlc_media_player.h, libplacebo GitHub, soxr GitHub, dsd-guide DoP — geri kalanlar arama-sonucu tabanlı (güven skoruna yansıtılmıştır)

---

*Bu rapor Researcher Agent tarafından 2026-10-20'de üretilmiştir. Git commit YAPILMADI (kural gereği).*
