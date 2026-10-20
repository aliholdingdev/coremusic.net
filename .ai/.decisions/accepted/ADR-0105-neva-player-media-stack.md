---
title: "CoreMusic — ADR-0105: Neva Player Media Stack (Açık Kaynak Decode + WASAPI Exclusive Bit-Perfect + libplacebo 4K/HDR Video + 31-Band Parametrik EQ, FFmpeg Opsiyonel Gateway)"
type: adr
category: audio
date: 2026-10-19
updated: 2026-10-20
version: 1.3.1
authority: "SSOT: bu dosya"
governance: "Red Team → Human Mode → Truth Mode — 3 tur / 20 persona debate TAMAMLANDI (4 uzlaşma raporu: 3 Expert + 1 Senior; Junior tek-tur raporu eklendi)"
debate: "TAMAM 2026-10-19 — Tur 1: 4 persona kendi önerisi · Tur 2: çapraz eleştiri (biquad cascade sorgusu, DPC riski, demux boşluğu, null-test sahte-PASS) · Tur 3: 4/4 KOŞULLU-KABUL → şartlar bu v1.1'e entegre edildi → KABUL"
related:
  - ".decisions/accepted/ADR-019-per-os-neva-player.md"
  - ".decisions/accepted/ADR-017-dsp-hardware-mode.md"
  - ".decisions/accepted/ADR-006-performance-targets.md"
  - "architecture/neva-player/index.md"
  - "reports/neva-re-dump-aimp-vlc.md"
  - "projects/NevaPlayer/reverse/aimp-decompile-dump.md"
  - "research/neva-player-media-stack.md"
  - "research/neva-player-dil-uyumluluk-matrisi.md"
---

# ADR-0105: Neva Player Media Stack

**Durum:** accepted (2026-10-19 — 3 tur debate kapandı, tüm koşullar metne işlendi · 2026-10-20: **libmpv hibrit-fallback red notu** (§3) + **Ghidra decompile dump atfı** eklendi)
**İlgili:** [[ADR-019-per-os-neva-player]] (IAudioBackend + fallback zinciri — bu ADR onu **tamamlar**) · [[ADR-017-dsp-hardware-mode]] (hard-RT kısıtları bağlayıcı) · [[ADR-006-performance-targets]] (latency kapısı)

---

## 1. Context

Neva Player PLANNED (0 .cpp — disk kanıtı: `.opencode/agents/embedded-engineer.md:16`). Medya kütüphane seçimi ve 32b/192kHz bit-perfect + 4K/HDR hedefinin nasıl karşılanacağı kararı yoktu. Kanıt tabanı: kurulu AIMP ve VLC RE dump (string-extraction + import/export + plugin envanteri), 69 kaynaklı web araştırması, 25 dil FFI matrisi. **v1.2.0 (2026-10-20):** 112 kaynaklı ikinci web doğrulama turu (`.ai/projects/NevaPlayer/research/media-stack-web-verification.md`) — soxr lisans düzeltmesi, ASIO lisans doğrulaması, libdsd boşluğu, miniaudio exclusive distortion riski, IAudioClient3 low-latency shared fallback adayı.

**RE kritik kanıtları:** AIMP kendi ASIO/DS cihaz katmanını decode'dan ayırıyor (`Aso::Devices::Asio::TASOASIODeviceManager` ≠ BASS) · AIMP FFmpeg'i opsiyonel plugin'de izole tutuyor (`aimp_inputFFmpeg/TFFmpegLibrary`) · AIMP DSD parser'ı var (`aimp_sacd/TSACDChunk`) · VLC 3.0.23 `libvlc_audio_set_callbacks` ile decode çıktısını custom DSP'e aktarıyor · VLC 10-band EQ public API + P010/10-bit + D3D11VA yolu hazır.

## 2. Decision (Karar)

### 2.1 Karar tablosu

| Bileşen | Karar | Lisans |
|---|---|---|
| Hafif decode (ses) | **dr_flac/dr_wav/dr_mp3** + **libFLAC** | PD/MIT/BSD |
| DSD/SACD | **libdsd** (DSF/DFF/DoP) — repo/sürüm/lisans **UNKNOWN** (2026-10-20, 112 kaynaklı web araştırmasında doğrulanamadı; §10 açık #1). Doğrulanana kadar DoP yolu **libFLAC + dr_* yedek planıyla** kurgulanır | ⚠️ MIT (teyitsiz) |
| Kapsayıcı (demux) | **2.4'e bak** — kapsayıcısız formatlar çekirdekten, kapsayıcılı dosyalar ffmpeg gateway | — |
| Ağır/evrensel decode | **ffmpeg (LGPL build)** — opsiyonel gateway eklentisi, çekirdek değil | LGPL |
| Ses çıkışı (Windows) | **WASAPI exclusive birincil** → ASIO (opsiyonel — lisans **GPL-3.0 + proprietary dual-license** olarak doğrulandı 2025-10-28, §3; birincil yapılmaz) → shared (**IAudioClient3 low-latency shared fallback adayı** — 2026-10-20 eklenti) → null (kill-switch) | — |
| DSP | **Neva core: 31-band RBJ parametrik EQ (1/3-oktav ISO · sabit-Q ≈ 4.31–4.4 başlangıç önerisi) + Linkwitz-Riley XO + EBU R128 (libebur128) + true-peak limiter + lock-free SPSC ring** | MIT/BSD |
| Video decode | **ffmpeg libavcodec + D3D11VA/DXVA2/MFT HW** | LGPL |
| Video render | **libplacebo + D3D11 swapchain, ST 2094-40/10 + bt2446a tonemap (BT.2390 eşdeğeri), jinc/ewa_lanczos scaling, HDR10 P010** | LGPLv2.1+ |
| A/V sync | **audio master clock** + es_out-sınıfı PCR (VLC referans mimarisi) | — |
| FFI | **tek C ABI** yüzeyi (`neva_session_*`) — sürüm sözleşmesi 2.6 | MIT (Neva kendi yüzeyi) |
| UI | C++20 native (Qt/native); ayrı süreç/IPC opsiyonel (ADR-019 pipe) | — |

**YASAK:** JUCE (AGPL) · BASS (shareware €950) · madVR (kapalı) · ffmpeg `--enable-gpl` build · ASIO'nun birincil yol yapılması (lisans teyidi yok).

### 2.2 EQ biquad sayısı (debate K1 — çözüldü)

31-band **parametrik** EQ her bantta **tek** RBJ biquad kullanır → 31 × 2 kanal = **62 biquad temel**. "124" yalnız **opsiyonel çift-kat (cascade)** durumu içindir: bant başına ikinci biquad (ör. high-shelf + peaking kombinasyonu, LR crossover korreksiyonu) kullanılırsa üst sınır 124'e çıkar. Cascade **varsayılan değildir**; her bant için cascade gerekçesi (headroom kaybı 2×, maliyet 2×) yazılmadan kullanılmaz. Benchmark kapısı (§5.6) hem 62 hem 124 senaryosunu ölçer.

### 2.3 Biquad state/katsayı kuralları (debate Expert #2)

- Katsayı + state **double**, `alignas(64)`, baştan allocate (62×2×state ≈ 2 KB, L1'e sığar)
- Parametre/preset değişikliği **RT thread dışında** hesaplanır, katsayılar per-block **5–20 ms ramp smoothing** ile değişir (click yasağı)
- Denormal: thread başlangıcında SSE2 **FTZ/DAZ** MXCSR set edilir (callback'te değil)
- Biquad'lar IIR zinciri olduğundan band-içi SIMD **YAGNI** — scalar double + benchmark kapısı

### 2.4 Demux/kapsayıcı sorumluluğu (debate Z1 — boşluk kapatıldı)

dr_libs/libFLAC/libdsd **tek-dosya çözücüdür, kapsayıcı demux etmez** (WAV/DSF/DFF/raw-FLAC çekirdekten oynar). Kapsayıcılı dosyalar (MKV/MP4/OGG/TS/M4A) **ffmpeg gateway üzerinden** oynatılır. Format→decoder öncelik zinciri (AIMP `IAIMPExtensionAudioDecoderPriority` deseni, RE §1.3) tanımlanana kadar bu ayrım **bağlayıcıdır**; gapless test vektörleri (§5.4) her iki yolu da kapsar.

### 2.5 Thread topolojisi (debate Z3 + Expert #2 — bağlayıcı)

```
[demux/decode thread] ──(SPSC ring, daima cihaz formatında PCM)──▶ [RT render callback]
     · DSD→PCM (producer-side)                                          · 31-band biquad EQ
     · soxr resample (producer-side, mismatch'te)                       · LR crossover
     · ffmpeg gateway decode (ASLA callback'te)                         · true-peak limiter
[non-RT ayrı thread: konvolüsyon reverb + EBU R128 analiz — ring üzerinden, callback'e DAHİL DEĞİL]
```

- **Callback içinde decode, resample, yeniden kaynaklama veya konvolüsyon yasaktır** (ADR-017 ile uyumlu)
- Ring invariant'ı: ring daima **cihaz formatında PCM** taşır
- DSD yolu: libdsd çıktısı **DoP default**; cihaz native DSD waveform formatını exclusive mode'da bildirirse native (⚠️ VERIFICATION REQUIRED — Windows SDK header teyidi). **DoP/DSD akışı asla resampler'dan geçirilmez** (marker bozulur) — oran uyuşmazlığında önce DSD→PCM, sonra soxr. AES67 kapsamı PCM'dir, DSD beyanı AES67'ye dayandırılamaz.
- Sample-rate mismatch: kaynak oranı = cihaz oranı ise **resampler tam bypass** (bit-perfect null-test bunun üzerinden); uyuşmazlıkta soxr yalnız decode thread'de (**libsoxr LGPL-2.1-or-later** — 2026-10-20 web doğrulama düzeltmesi: MIT DEĞİL, 8 bağımsız kaynak; LGPL dinamik-link stratejisiyle uyumlu, §5.9 lisans denetimine dahil)
- Hot-plug: cihaz kopması RT thread'de re-open **YAPILMAZ**; `IMMNotificationClient` (non-RT) atomic flag yazar → callback sessizlik üretir → **ADR-0105'in kendi fallback kararı** (ADR-019'un `IAudioBackend` mekanizması üzerine kurulur): exclusive → ASIO (opsiyonel, teyit sonrası) → shared → null kill-switch. Not: ADR-019'un kendi varsayılan zinciri `ASIO → WASAPI Exclusive → Shared → Null` (ADR-019:155,162); bu ADR exclusive'i bit-perfect kapısı nedeniyle **ön sıraya alır** — `tamamlar` iddiası bu sıralama farkını da kapsar.
- 192k çıkışta 512-sample periyot yalnız 2.67 ms → buffer 1024/2048'e yükseltilir; RT thread MMCSS **"Pro Audio"**, core 0 dışına sabit (SMT kardeşi boş), ses thread'i power-throttle dışında

### 2.6 C ABI sürüm sözleşmesi (debate Z2 — boşluk kapatıldı)

`neva_session_*` yüzeyi: `neva_session_api_version()` handshake'i · opak handle zorunluluğu (C++ tipi sınırı geçemez) · tüm işlevlerde hata kodu dönüşü · ABI'yi kıran her değişiklik **major sürüm artışı** gerektirir. İlk iki binding doğrulayıcısı: Python (ctypes, birincil) + C# (P/Invoke, ikincil).

### 2.7 Video yolu kopya kuralı (debate Performance #3 — bağlayıcı)

DXVA2/D3D11VA decode çıktısı (D3D11 texture) → libplacebo pl_image → render → swapchain zincirinde **zero-copy (shared handle) zorunludur**; ffmpeg gateway kullanıldığında AVFrame'den sistem belleğine kopya/readback **yapılamaz** — aksi halde ölçüm geçersiz ve 4K60 düşer. **Sayı düzeltmesi (2026-10-20):** P010 (4:2:0, 10-bit 16-bit konteynerde) 4K kare = 3840×2160×3 bayt = **24,9 MB/kare** (12,4 MB olan **NV12/8-bit** değeridir; eski metin P010'u NV12 ile karıştırmıştı) → kopya trafiği 24,9×60 ≈ **1,49 GB/s** (eski 744 MB/s yarım kalmış). Bu, zero-copy zorunluluğunu **2 kat güçlendirir**. GPU bellek bütçesi: **§2.8**.

### 2.8 GPU bellek (VRAM) bütçesi ve tükenme merdiveni (2026-10-20 eklendi — sayısal boşluk kapatıldı)

> **Durum:** Bu bütçe **birinci-ilkellerden hesaplanmıştır (derived), ölçülmemiştir.** Otorite §5.6 kapısının gerçek GPU'daki **peak VRAM ölçümüdür**; ölçüm bu tabloyu doğrular ya da revize eder. Tüm alt terimler ARALIK olarak verilir (DPB derinliği içerik-sinyalli, libplacebo geçiş sayısı yola bağlı).

**4K60 HDR10 (P010) bütçe dökümü:**

| Kalem | Hesap | MB |
|---|---|---|
| Decode DPB (HEVC L5.1, 12–16 P010 yüzeyi × 24,9 MB) | 12–16 × 24,9 | **299–398** |
| libplacebo ara render-target'ları (HDR tonemap FP16, 4K × 66,4 MB; 0–3 geçiş — ölçek yoksa doğrudan backbuffer'a) | 0–3 × 66,4 | **0–199** |
| Swapchain (çift/triple, RGB10A2 4 bayt/px × 33,2 MB) | 2–3 × 33,2 | **66–100** |
| 3D LUT + shader/sabit buffer | — | ~5 |
| **Toplam (tasarım noktası)** | | **~400–700** |

- **Tasarım noktası: ≤600 MB peak** (16 DPB + 2 FP16 ara RT + triple RGB10A2). Üst sınır 700 MB (20 DPB + 3 RT) — bu, 2 GB VRAM'lı giriş seviyesi GPU'da bile güvenlidir; **1 GB VRAM'lı çok eski GPU'da darboğaz olur** → merdiven.
- **Tutarlılık:** §2.7'nin 24,9 MB/kare düzeltmesi bu bütçenin temelidir; NV12 varsaysaydık DPB yarıya iner ve "200-400 MB" görünürdü — o eski (yanlış) sayı buradan geliyordu.

**VRAM tükenme merdiveni (runtime, `IDXGIAdapter3::QueryVideoMemoryInfo` ile izlenir):**

| Kademe | Aksiyon | Maliyet |
|---|---|---|
| V0 | Tam: 4K HDR10 P010 + DPB 16 + 2 FP16 RT | — (hedef) |
| V1 | DPB 16→12 (decode yüzey havuzu küçülür) | Düşük — DPB daralması, HDR korunur |
| V2 | Ara RT 2→1 / tonemap tek geçişe | Orta — çok-pass ölçek/renk geçişleri sadeleşir |
| V3 | HDR10→SDR (NV12'ye düş, 12,4 MB/kare) | Yüksek — HDR kaybı, ~yarı bellek |
| V4 | 4K→1080p çıkış (çözünürlük ölçekleme) | Yüksek — kalite kaybı |
| V5 | Yazılım decode (ffmpeg) → CPU'ya geç, GPU yalnız render | Son çare — CPU yükü artar, §5.7 DPC/ISR bütçesini tehdit eder |

- **Kural:** Merdiven yalnız **runtime bellek baskısı** altında iner (`QueryVideoMemoryInfo` usage/budget); başlangıçta V0'dan başlar, oturum başına **geri-çıkış (yükseltme) YOK** (dalgalanma/titreme yasağı) — yeni oturumda V0 denenir.
- **Kesişim:** V5 (yazılım decode) ses yolunu **asla** etkilemez — ses bit-perfect exclusive (§2.5) GPU'dan bağımsızdır; video düşerken ses kesintisiz sürer (ADR-019 ayrımı).
- **Ölçüm ortamı (2026-10-20 eklendi — VRAM ölçümünün tekrarlanabilirliği zorunlu):** §5.6/§5.7 peak VRAM raporu **GPU modeli + toplam VRAM (GB) + sürücü sürümü + Windows sürümü** alanları olmadan **geçerli sayılmaz**. Hedef sınıf **GTX 1060 (§4 risk 2)**; **gerçek test bench adı `UNKNOWN`** (henüz atanmadı) → atama `⚠️ VERIFICATION REQUIRED`. Rapor mutlaka **yüzde** de versin (ör. usage/budget = 600 MB / 6 GB ≈ %10) — aynı 600 MB, 2 GB ve 8 GB GPU'da farklı risk taşır.

### 2.9 Sistem belleği (RAM) bütçesi (2026-10-20 eklendi — VRAM ile aynı kategorideki boşluk)

> **Durum:** VRAM gibi **derived (birinci-ilkellerden türetilmiş), ölçülmemiş**. Otorite §5.7 stres testinin **peak RSS** logudur. Zero-copy video (§2.7) host RAM'i şişirmez — bu bütçe ses + UI + opsiyonel efekt içindir.

| Kalem | Hesap | MB |
|---|---|---|
| SPSC ring'ler (dosya + radyo yolları, stereo float, ~100 ms @48k–192k) | 2 yol × ≤0,2 MB | ≤0,5 |
| EQ/XO/limiter state + preset havuzu (62–124 biquad × double + ramp buffer) | 124 × ~100 B | ~0,01 |
| Convolution reverb (opsiyonel; 10 s IR stereo @192k float = 15,4 MB + partitioned FFT ~2×) | 15,4 × ~3 | 30–45 |
| FFmpeg demux/decode host tamponu (AVPacket/AVFrame + prefetch; video zero-copy'de host staging minimal) | ses-only 10–30 MB | 10–50 |
| Radyo jitter/prefetch (30 s × 128 kbps ≈ 0,5 MB + HLS segment havuzu) | ≤ 8 MB | ≤8 |
| Playlist + metadata + cover cache (LRU tavanı — tasarım tercihi) | sınır | ≤100 |
| UI (Qt) RSS | tipik aralık | 100–200 |
| **Toplam tasarım noktası** | | **≤400 (ses-only ≤150)** |

- **Kural:** Reverb kapalı + video yokken **≤150 MB** hedefi; tam yük (video + reverb + büyük playlist) **≤400 MB**. Aşım → LRU cache'ler (playlist/cover) ilk küçülen, ring/IR sabit (allocate-then-reuse, ADR-017).
- **Kesişim:** §5.7'de peak RSS olarak loglanır; `device_removed` benzeri "out of memory" değil, **yumuşak** aşım → cache eviction (hard-RT yol asla serbest bırakılmaz/serbest edilmez).

### 2.10 Uçtan uca gecikme bütçesi (AK-3 <20 ms — 2026-10-20 eklendi: hedef vardı, ölçüm metodu yoktu)

> **Boşluk:** AK-3 `<20ms WASAPI exclusive` istedi; ADR-006 **sayıyı** veriyordu (`<10ms ASIO / <20ms WASAPI`), ADR-0105 §5'in **9 kapısının hiçbiri latency ölçmüyordu**. Bu madde bütçeyi + ölçümü bağlar.

**Bütçe dökümü (derived; yapı taşları ADR-006 kaynak 14 = 48 kHz buffer tablosu):**

| Kalem | Hesap | ms |
|---|---|---|
| WASAPI exclusive event period (`IAudioClient::GetDevicePeriod` — **cihaz-sürücüye bağlı, min değer UNKNOWN**) | tipik 2,5–10 | 3–10 |
| Çift tampon (§2.5 DoSwitchBuffer deseni — decode ile render arası) | 2 × period | 6–20 |
| Engine SPSC ring + DSP blok (per-block işleme; 256–512 örnek) | 512 @48k = 10,7 · 256 @48k = 5,3 · 512 @192k = 2,7 | 2,7–10,7 |
| **Toplam ≈ 2×period + blok** | tasarım: period ≤5 ms, blok ≤256@48k / 512@192k | **≤20** |

- **Sert kural:** 1024 örnek blok @48k = **21,3 ms** (ADR-006 kaynak 14) — tek başına bütçeyi kırar ⇒ **hedef bit-perfect'te blok ≥1024 @48k yasak**.
- **Ölçüm merdiveni (AK-3 >20 ms gerçekleşirse, §5.10):** L1 period 10→5 ms · L2 blok 512→256 · L3 sürücü min period >5 ms ise **cihaz-limiti olarak raporlanır** (hedef küçültülmez, sahte PASS yasak — ölçüm "device-limited" yazılır) · L4 IAudioClient3 low-latency shared **ayrı satırda** raporlanır (bit-perfect değil, AK-3'ü karşılamaz).
- **Ölçüm otoritesi:** §5.10 (a) gerçek DAC loopback — (b) CI'da yalnız **alt sınır** (yazılım aritmetiği tek başına "geçti" sayılmaz).

## 3. Alternatives (Reddedilenler)

| Alternatif | Neden reddedildi |
|---|---|
| libvlc tüm player olarak gömme | GPL yayılım riski + 10-band EQ mahkûmiyeti; "decode gateway" (callback) olarak opsiyonel kaldı |
| BASS (AIMP yolu) | Shareware €950, ticari kilit |
| JUCE | AGPL — copyleft çatışması |
| libmpv tüm player olarak gömme | Black-box; Neva DSP/donanım hedefleriyle (k2 sürücü, Class AB telemetri) bütünleşmez |
| **libmpv "fallback" olarak hibrit yola eklenmesi (v1.1.1 — kullanıcı opsiyonu "4" olarak sordu, karar yetkisi delege edildi)** | **REDDEDİLDİ**: (1) mpv **GPL-2.0** — statik/dinamik gömme copyleft riski taşır, dinamik-link kaçamağı LGPL'de olduğu gibi GPL'de garanti değildir; (2) black-box render/DSP hattı Neva'nın 31-band EQ + bit-perfect exclusive hedefiyle **çift motor tutarsızlığı** yaratır (hangi motor EQ'yu uygular? test matrisi ikiye katlanır); (3) fallback ihtiyacı zaten ADR-019 `IAudioBackend` + ADR-0105 §2.5 fallback zinciri ile karşılanır. Gerekçe web araştırmasıyla doğrulandı (mpv COPYING, 2026-10-19 oturumu) |
| ASIO birincil | ASIO 2025-10-28'de **GPL-3.0 + proprietary dual-license** yapıldı (doğrulandı); buna rağmen birincil yapılmaz — WASAPI exclusive zaten bit-perfect, ASIO yalnız opsiyonel ek yol |
| Vulkan-first libplacebo | D3D11VA decode texture'ı D3D11'dir → Vulkan geçişi kopya/interop maliyeti; ama **P99 frame time hangi yolda geçiyorsa o yol karardır** — D3D11 vs Vulkan A/B ölçüm kapıda (§5.6) |

## 4. Consequences

**Olumlu:** ticari serbest stack (LGPL dinamik link + MIT/BSD çekirdek) · RE'de doğrulanmış desenler · ADR-019/017/006'a sadık · 25 dil FFI kapısı.
**Riskler (debate ile derinleştirildi):** (1) "%1-3 CPU" tahmini **kabul edilemez güven** — forum hesabı, stereo sayımında 2× hata içeriyor; benchmark kapısı IMPLEMENTED etiketinin ön koşulu. (2) libplacebo D3D11 HDR'da GPU yükü %50-70'e çıkabilir (1060 sınıfı hedef donanımda P99 teğet). (3) ~~ASIO lisans belirsizliği~~ (2026-10-20: dual-license doğrulandı — kapandı). (4) exclusive + 4K eşzamanlı DPC spike = xrun adayı. (5) Native DSD API'si Windows SDK'da doğrulanmadı. **(6, 2026-10-20 eklendi):** miniaudio exclusive-mode distortion raporu (upstream #1022) — miniaudio fallback kullanılırsa **null-test zorunlu**; exclusive bit-perfect iddiası doğrulanmadan "geçti" sayılmaz.
**Kapılar §5'e yazıldı; benchmark kapıları ADR-006'ya değil ADR-0105'e bağlıdır.**

## 5. Verification Planı (debate QA Expert ile ölçülebilir hale getirildi)

> **Kural:** Test kapıları yalnız ölçülebilir hâle getirildiğinde geçerlidir; her kapı **ölçüm metodu + ortam + sahte-PASS savunması** taşır.

| # | Kapı | Metot | Ortam |
|---|---|---|---|
| 5.1 | **Bit-perfect null-test** | Referans WAV → exclusive yolda oynat → aynı session'da yakala → **SHA-256 byte-eşitliği**. **Zorunlu canary:** yakalanan sinyal enerjisi > eşik VE hash(sessizlik) ≠ hash(yakalanan) — aksi halde sahte PASS. ⚠️ Loopback-exclusive davranışı gerçek cihazda teyit edilecek (VERIFICATION REQUIRED) | CI + gerçek DAC |
| 5.2 | **THD+N <%0.005 / SNR >105dB** | İki domen: (a) **DSP-alanı** — offline float64 sweep + notch/FFT, donanımsız, **her PR'da CI kapısı**; (b) donanım-ucu DAC loopback — laboratuvar raporu, CI gate değil | CI / lab ayrı |
| 5.3 | **Preset-change click testi** | Band gain/preset değişimi sırasında çıktıda click/spike enerjisi eşiği (5-20 ms ramp doğrulanır) | CI (offline render) |
| 5.4 | **Gapless vektör seti** | MP3(LAME tag)/FLAC block-boundary, ≥3 vektör/format, ardışık çift oynatım → byte-eşitlik; her iki demux yolu (2.4) | merge gate |
| 5.5 | **C ABI smoke** | Python ctypes + C# P/Invoke: open/load/render/close + hata kodu + **api_version handshake** | merge gate |
| 5.6 | **Benchmark kapısı (IMPLEMENTED ön koşulu)** | 62 ve 124 biquad @192k: FTZ/DAZ etkin, tek-çekirdek **≤%5**, P99 callback jitter ≤ bütçenin %50'si · 4K60 HDR10: P99/P99.9 GPU frame ≤16,6 ms · D3D11 vs Vulkan **A/B**: P99'su geçen yol karardır · **zero-copy doğrulaması** (kopya/readback yok) · **VRAM peak ölçümü** (`QueryVideoMemoryInfo`, §2.8 tablosunu ≤600 MB tasarım noktasında doğrular/revize eder; budget aşımı → merdiven V1-V5 tetiklenir, `device_removed`/`out of memory` hatası YASAK) · **rapor zorunlu alanları:** GPU modeli + toplam VRAM + sürücü + Windows sürümü (§2.8 "ölçüm ortamı" — yoksa ölçüm geçersiz) · **merdiven doğrulaması:** yapay GPU bellek baskısı (dış yükleyici) ile **V0→V1/V2 geçişi gözlemlenir** + oturum-içi geri-çıkış YOK (titreme = FAIL) | self-hosted GPU (bench adı `UNKNOWN` — atanacak) |
| 5.7 | **1 saat stres** | 4K60+192k, **nightly self-hosted** (per-merge DEĞİL, GPU şart), retry YASAK; LatencyMon ≥30 dk DPC/ISR kaydı; AC **ve** pil+throttling koşullarının ikisi; xrun=0 + **peak RSS (§2.9 bütçe ≤400 MB / ses-only ≤150 MB)** + DPC raporu `.ai/reports/`'a kanıt dosyası | nightly |
| 5.8 | **Hot-plug testi** | Oynatım sırasında cihaz koparma → sessizlik → fallback zinciri (ADR-019) → konum koruma | hardware-in-loop |
| 5.9 | **Lisans denetimi** | ffmpeg build'de `--enable-gpl` **ve** `--enable-version3` yasağı assert (ikisi ayrı bayrak — 2026-10-20 doğrulaması) + atıf dosyası varlık kontrolü (otomatik); **libsoxr LGPL-2.1-or-later** + LGPL dinamik-link yorumu = compliance-auditor inceleme kapısı (otomatik değil, ayrıştırıldı) | CI + auditor |
| 5.10 | **Uçtan uca gecikme (AK-3 <20 ms — 2026-10-20 eklendi)** | (a) **Birincil (gerçek):** DAC elektriksel loopback — impulse/click üret → DAC out → ADC in → **cross-correlation gecikme** ölçümü; (b) **CI alt sınırı:** `GetDevicePeriod` + §2.10 aritmetiği + `IAudioClient::GetBufferDelay`. **Sahte-PASS savunması:** yalnız yazılım aritmetiğiyle "AK-3 geçti" **DEĞİL** (yalnız alt sınır); cihaz-sürücü min period gerçek ölçümde doğrulanır. Aşım → §2.10 merdiveni L1–L4 (L4 shared ayrı satır, bit-perfect iddiası taşımaz) | gerçek DAC + CI (alt sınır) |
| 5.11 | **A/V sync (2026-10-20 eklendi — eşik yoktu)** | Timestamp tabanlı: demux PTS → render/orchestrator offset'i. **Tasarım eşikleri (Neva kendi kararı):** P95 \|A−V\| ≤ **20 ms** · ilk kilit (first-frame ↔ first-audio) ≤ **100 ms** · 10 dk sürüşte drift ≤ **5 ms** (master clock düzeltmesi **kapalıyken** ölçülür — düzeltme açıkken ölçüm sahte PASS). ⚠️ Standart atıf eşikleri (ITU-R BT.1359 / EBU R37 sayıları) **VERIFICATION REQUIRED** — doğrulanıp revize edilirse bu satır güncellenir | CI (simüle clock) + lab (photodiode opsiyonel) |

**Pyramid:** Unit %70 (biquad katsayı, ring, hash canary — <5 sn) · Integration %20 (decode→DSP→ring, ABI smoke) · E2E %10 (null-test, nightly stres). **Coverage:** çekirdek DSP line ≥%90, branch ≥%75 global. RT yolu: DSP çekirdeği saf fonksiyon olarak offline test edilir; deadline/xrun yalnız hardware-in-loop'ta ölçülür; callback heap-allocation sayacı = 0 (zero-allocation, ADR-017).

## 6. Debate Kaydı (3 tur)

| Persona | Seviye | Tur 3 Oyu | Temel itiraz/şart |
|---|---|---|---|
| architect | Expert | KOŞULLU-KABUL → v1.1'de karşılandı | demux boşluğu (2.4) · C ABI sürümü (2.6) · thread topolojisi (2.5) · cascade gerekçesi (2.2) |
| embedded-engineer | Expert | KOŞULLU-KABUL → v1.1'de karşılandı | RT topolojisi callback-içi EQ (2.5) · DSD DoP-first (2.5) · soxr producer-side (2.5) · hot-plug (2.5) · 124 vs 62 cascade (2.2) · preset-click testi (5.3) |
| performance-engineer | Senior | KOŞULLU-KABUL → v1.1'de karşılandı | %1-3 tahmini kabul edilemez → benchmark kapısı 5.6 · D3D11 vs Vulkan A/B (5.6) · zero-copy zorunluluğu (2.7) · AC+pil koşulları (5.7) · ring/VRAM hesabı |
| qa-engineer | Expert | KOŞULLU-KABUL → v1.1'de karşılandı | null-test canary (5.1) · THD domen ayrımı (5.2) · nightly stres ayrımı (5.7) · gapless maddesi (5.4) · lisans ayrıştırma (5.9) |
| explore (Junior) | Junior | 2026-10-19 tek-tur raporu — metin tutarlılığı + vault-link denetimi | eklendi ↓ |

**Karar:** 4 oy (Expert 3 + Senior 1, tamamı koşullu-kabul) + 1 Junior raporu (oy yok, 4 düzeltme şartı — uygulandı: ADR-019 zincir atfı, debate aritmetiği, 3 mojibake, accepted'a taşıma) = **0 RED → KABUL** (şartlar v1.1'e işlendi).

## 7. Sonraki Adımlar

1. Kod iskeleti: `neva_session_*` C ABI + WASAPI exclusive backend + SPSC ring (Windows-first, ADR-019 adapter)
2. Benchmark harness'ı (5.6) ilk kodla birlikte yazılır — TDD
3. ~~ASIO SDK lisans teyidi (Steinberg)~~ → **2026-10-20 KAPANDI**: ASIO dual-license (GPL-3.0 + proprietary) doğrulandı → Aşama 2 opsiyonel eklentisi
4. macOS CoreAudio / Linux PipeWire adapter (ADR-019) — Windows bit-perfect kanıtından sonra
5. **libdsd repo/sürüm/lisans doğrulaması** (2026-10-20 açık #1) — doğrulanana kadar DoP yolu libFLAC + dr_* yedek planıyla kurgulanır; §2.1 satırı `⚠️ MIT (teyitsiz)` işareti taşır

### 7.1 v1.2.0 Revizyon Kaydı (2026-10-20 — web doğrulama turu)

> Kaynak: `projects/NevaPlayer/research/media-stack-web-verification.md` (112 kaynak · ~30 websearch + ~12 webfetch · §1 etki tablosu). Kararın **özü (açık kaynak decode + WASAPI exclusive bit-perfect + libplacebo D3D11 + 31-band EQ + FFmpeg opsiyonel LGPL gateway) DEĞİŞMEDİ** — yalnız lisans/gerekçe düzeltmeleri ve bir eklenti.

| # | Değişiklik | Bölüm | Gerekçe (web kanıtı) |
|---|---|---|---|
| 1 | **soxr MIT → LGPL-2.1-or-later** (❌ çürüdü) | §2.5 · §2.1 | 8 bağımsız kaynak (GitHub lisans tespiti ×4, renoise forum, python-soxr README). LGPL hâlâ dinamik-link-stratejisiyle uyumlu, ama MIT/BSD değil — §5.9 lisans denetimine alındı |
| 2 | **ASIO belirsizlik → dual-license doğrulandı** (GPL-3.0 + proprietary, Ekim 2025) | §3 · §7 madde 3 | "2025 değişikliği doğrulanamadı" ifadesi geçersizleşti. **ASIO birincil olmasın kararı hâlâ doğru** (WASAPI exclusive zaten bit-perfect); yalnız belirsizlik giderildi, Aşama 2 opsiyonel eklentisi |
| 3 | **`--enable-version3` ayrı bayrak** (§5.9'a eklendi) | §5.9 | `--enable-gpl` ≠ `--enable-version3` (StackOverflow #67968156); GPLv3 bileşenleri ayrı işaretleme ister — lisans assert'i ikisini de kapsamalı |
| 4 | **libplacebo tonemap terminolojisi** (BT.2390 → ST 2094-40/10 + BT.2446A) | §2.1 · §4 risk 2 | libplacebo resmi API'si ST 2094-40/10 + bt2446a kullanır; D3D11 FL ≥ 9_1, **LGPL-2.1+ (BSD2 değil)** doğrulandı |
| 5 | **libdsd → UNKNOWN** (repo/sürüm/lisans erişilemedi) | §2.1 · §7 madde 5 | Yalnız DSF ve DoP *format* spesifikasyonları doğrulandı; libdsd'nin resmi repo'su doğrulanamadı → `⚠️ MIT (teyitsiz)` + libFLAC/dr_* yedek planı |
| 6 | **Yeni risk #6: miniaudio exclusive distortion** (upstream #1022) | §4 | Bazı donanımlarda `ma_share_mode_exclusive` distortion raporları; MS kendi rehberinde exclusive öncesi **IAudioClient3 low-latency shared** testini öneriyor → §2.1 fallback zincirine "IAudioClient3 low-latency shared fallback adayı" eklendi, miniaudio fallback kullanılırsa **null-test zorunlu** |

**Doğrulandı (değişiklik yok):** FFmpeg LGPL + `--enable-gpl` yasağı · libFLAC Xiph BSD (+ FLAC RFC 9639, Ara 2024) · libebur128 MIT · 31-band RBJ EQ (1/3-oktav ISO · sabit-Q ≈ 4.31–4.4 önerisi eklendi) · DoP default + marker 0x05/0xFA dönüşümlü · ICY/PLS/M3U (araştırma odağı 6 — 2. faz feature olarak eklenebilir).

### 7.2 v1.2.1 Revizyon Kaydı (2026-10-20 — VRAM boşluğu kapatıldı)

> **Tetik:** Kullanıcı "eksik bir şey vramı" bildirdi (Neva Player turu). Kararın **özü DEĞİŞMEDİ** (açık kaynak decode + WASAPI exclusive bit-perfect + libplacebo D3D11 + 31-band EQ + FFmpeg opsiyonel LGPL gateway) — yalnız GPU bellek tarafı tamamlandı ve bir sayısal tutarsızlık düzeltildi.

| # | Değişiklik | Bölüm | Gerekçe |
|---|---|---|---|
| 1 | **P010 byte düzeltmesi:** `12,4 MB/kare` → **`24,9 MB/kare`** (kopya trafiği `744 MB/s` → **`1,49 GB/s`**) | §2.7 | 12,4 MB **NV12/8-bit** değeridir; P010 (4:2:0, 10-bit 16-bit konteyner = 3 bayt/px) tam 2× tir. Eski metin P010'u NV12 ile karıştırmış — kendi HDR10 hedefiyle çelişiyordu. Düzeltme zero-copy zorunluluğunu **2 kat güçlendirir** |
| 2 | **Yeni §2.8 GPU bellek (VRAM) bütçesi** | §2.8 | Tek satırlık "~200-400 MB" hedefi artık **dökümlü tablo** (DPB 299–398 + libplacebo FP16 ara RT 0–199 + swapchain 66–100 + LUT ~5 = **~400–700 MB**; tasarım noktası **≤600 MB**). Tümü **birinci-ilkellerden türetilmiş (derived), ölçülmemiş** — otorite §5.6 ölçümüdür |
| 3 | **Yeni VRAM tükenme merdiveni V0–V5** | §2.8 | DPB 16→12 → ara RT 2→1 → HDR→SDR (NV12) → 4K→1080p → yazılım decode. Runtime `QueryVideoMemoryInfo` ile izlenir; oturum-içi geri-çıkış YASAK (dalgalanma). V5 ses yolunu **asla** etkilemez (ADR-019 ayrımı — video düşerken bit-perfect ses sürer) |
| 4 | **§5.6 kapısına VRAM peak ölçümü eklendi** | §5.6 | Eski kapı yalnız GPU **frame time** ölçüyor, **VRAM peak/budget** ölçmüyordu. Yeni metrik: `QueryVideoMemoryInfo` ≤600 MB tasarım noktasını doğrular/revize eder; budget aşımı → merdiven tetiklenir; `device_removed`/`out of memory` hatası YASAK |

**Boşluk türü (eski durum):** VRAM bütçe dökümü · ölçüm metodu · tükenme fallback'i **üçü de yoktu**; mimari index (`architecture/neva-player/index.md`) VRAM'den **hiç bahsetmiyordu** (bkz. `log.md` 2026-10-20).

**Doğrulama otoritesi:** §2.8 sayıları **derived** (kare-matematiği) — gerçek GPU'da §5.6 peak ölçümü bu tabloyu doğrular ya da revize eder. Ölçüm öncesi "400-700 MB" bir **tasarım tahmini**, kesin sınır değil.

### 7.3 v1.3.0 Revizyon Kaydı (2026-10-20 — kriter↔kapı boşlukları 1-3 kapatıldı)

> **Tetik:** VRAM taramasından sonra sistematik boşluk taraması ("başka eksik var mı") — 5 boşluk bulundu, bu turda 1-3 kapatıldı (4: gapless format↔kapı, 5: fuzzing — sonraki tur).

| # | Değişiklik | Bölüm | Eski boşluk |
|---|---|---|---|
| 1 | **Yeni §2.10 uçtan uca gecikme bütçesi + ölçüm merdiveni L1-L4** | §2.10 | **AK-3 `<20ms` hedefi vardı, ölçüm metodu HİÇ YOKTU** — §5'in 9 kapısının hiçbiri latency ölçmüyordu; bütçe dökümü yoktu (2×period + blok ≈ ≤20 ms; **1024 örnek blok @48k = 21,3 ms ⇒ yasak**) |
| 2 | **Yeni kapı 5.10 gecikme ölçümü** (DAC loopback cross-correlation + CI alt sınırı, sahte-PASS savunması) | §5.10 | Ölçüm otoritesi kuruldu; ADR-006'daki sayı artık **metotla** bağlandı |
| 3 | **Yeni kapı 5.11 A/V sync + tasarım eşikleri** (P95 ≤20 ms · kilit ≤100 ms · drift ≤5 ms/10 dk, düzeltme-kapalı ölçüm) | §5.11 | A/V sync yalnız "audio master clock + PCR" tek satırıydı — **sayısal eşik ve ölçüm yoktu**; standart atıflar (ITU/EBU) `⚠️ VERIFICATION REQUIRED` |
| 4 | **Yeni §2.9 sistem RAM bütçesi** + §5.7'ye peak RSS ölçümü | §2.9 · §5.7 | VRAM bütçesi vardı, **sistem belleği hiç dökümlenmemişti** (ring/IR/decode/prefetch/playlist/UI = ≤400 MB, ses-only ≤150 MB) — aynı kategori boşluk |
| 5 | **Mimari index + kapsam senkronu** | — | §1'e Gecikme + RAM satırları, A/V sync satırına eşik; AK-3/AK-4 kapı pointerları §5.10/§5.11'e bağlandı |

**Kalan açık (sonraki tur):** (4) gapless kapı 5.4 yalnız MP3+FLAC adlandırıyor ↔ AK-5 8 format istiyor (DoP/DSF geçişi en zoru) · (5) hostile media file fuzzing (FFmpeg/dr_flac) hiçbir kapıda yok.

**Doğrulama otoritesi:** §2.9/§2.10 sayıları **derived** — §5.7 peak RSS ve §5.10 gerçek DAC ölçümü otoritedir. Standart eşik atıfları doğrulanana kadar tasarımsal karar olarak kalır.

### 7.4 v1.3.1 Revizyon Kaydı (2026-10-20 — VRAM ikinci denetim: kalan 3 detay)

> **Tetik:** Kullanıcı ikinci kez "eksik bir şey vramı" bildirdi — ilk düzeltmeler (§2.8/§5.6, tur10) yerinde ve vault genelinde eski sayı kalıntısı **yok** (tarama: "200-400 MB" / "744 MB" / "P010 12,4" yalnız düzeltme-kayıtlarında). Kalan üç detay kapatıldı:

| # | Değişiklik | Bölüm | Boşluk |
|---|---|---|---|
| 1 | **AK-4'e VRAM ölçütü eklendi** (kapsam 01, v0.2.2) | kapsam | VRAM ≤600 MB yalnız §5.6 kapısında vardı — **kabul kriterinde (AK-4) hiç geçmiyordu** |
| 2 | **Ölçüm ortamı zorunlu alanları** (GPU model + toplam VRAM + sürücü + Windows sürümü; rapora yüzde zorunlu) + **bench `UNKNOWN`** | §2.8 | "self-hosted GPU" **isimsiz** — ortam sabitlenmeden peak ölçüm tekrarlanabilir değil; aynı 600 MB 2 GB ve 8 GB GPU'da farklı risk taşır |
| 3 | **Merdiven doğrulama testi** (yapay bellek baskısı → V0→V1/V2 geçişi; oturum-içi geri-çıkış = FAIL) | §5.6 | V0-V5 ladder **tarif edilmişti ama test maddesi hiç yoktu** |
