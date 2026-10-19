---
title: "CoreMusic — ADR-0105: Neva Player Media Stack (Açık Kaynak Decode + WASAPI Exclusive Bit-Perfect + libplacebo 4K/HDR Video + 31-Band Parametrik EQ, FFmpeg Opsiyonel Gateway)"
type: adr
category: audio
date: 2026-10-19
updated: 2026-10-19
version: 1.1.0
status: accepted
authority: "SSOT: bu dosya"
governance: "Red Team → Human Mode → Truth Mode — 3 tur / 20 persona debate TAMAMLANDI (4 uzlaşma raporu: 3 Expert + 1 Senior; Junior tek-tur raporu eklendi)"
debate: "TAMAM 2026-10-19 — Tur 1: 4 persona kendi önerisi · Tur 2: çapraz eleştiri (biquad cascade sorgusu, DPC riski, demux boşluğu, null-test sahte-PASS) · Tur 3: 4/4 KOŞULLU-KABUL → şartlar bu v1.1'e entegre edildi → KABUL"
related:
  - ".decisions/accepted/ADR-019-per-os-neva-player.md"
  - ".decisions/accepted/ADR-017-dsp-hardware-mode.md"
  - ".decisions/accepted/ADR-006-performance-targets.md"
  - "architecture/neva-player/index.md"
  - "reports/neva-re-dump-aimp-vlc.md"
  - "research/neva-player-media-stack.md"
  - "research/neva-player-dil-uyumluluk-matrisi.md"
---

# ADR-0105: Neva Player Media Stack

**Durum:** accepted (2026-10-19 — 3 tur debate kapandı, tüm koşullar metne işlendi)
**Tarih:** 2026-10-19 · **v1.1** (v1.0.0 taslak → debate → v1.1.0 kabul)
**İlgili:** [[ADR-019-per-os-neva-player]] (IAudioBackend + fallback zinciri — bu ADR onu **tamamlar**) · [[ADR-017-dsp-hardware-mode]] (hard-RT kısıtları bağlayıcı) · [[ADR-006-performance-targets]] (latency kapısı)

---

## 1. Context

Neva Player PLANNED (0 .cpp — disk kanıtı: `.opencode/agents/embedded-engineer.md:16`). Medya kütüphane seçimi ve 32b/192kHz bit-perfect + 4K/HDR hedefinin nasıl karşılanacağı kararı yoktu. Kanıt tabanı: kurulu AIMP ve VLC RE dump (string-extraction + import/export + plugin envanteri), 69 kaynaklı web araştırması, 25 dil FFI matrisi.

**RE kritik kanıtları:** AIMP kendi ASIO/DS cihaz katmanını decode'dan ayırıyor (`Aso::Devices::Asio::TASOASIODeviceManager` ≠ BASS) · AIMP FFmpeg'i opsiyonel plugin'de izole tutuyor (`aimp_inputFFmpeg/TFFmpegLibrary`) · AIMP DSD parser'ı var (`aimp_sacd/TSACDChunk`) · VLC 3.0.23 `libvlc_audio_set_callbacks` ile decode çıktısını custom DSP'e aktarıyor · VLC 10-band EQ public API + P010/10-bit + D3D11VA yolu hazır.

## 2. Decision (Karar)

### 2.1 Karar tablosu

| Bileşen | Karar | Lisans |
|---|---|---|
| Hafif decode (ses) | **dr_flac/dr_wav/dr_mp3** + **libFLAC** | PD/MIT/BSD |
| DSD/SACD | **libdsd** (DSF/DFF/DoP) | MIT |
| Kapsayıcı (demux) | **2.4'e bak** — kapsayıcısız formatlar çekirdekten, kapsayıcılı dosyalar ffmpeg gateway | — |
| Ağır/evrensel decode | **ffmpeg (LGPL build)** — opsiyonel gateway eklentisi, çekirdek değil | LGPL |
| Ses çıkışı (Windows) | **WASAPI exclusive birincil** → ASIO (opsiyonel, lisans teyidi sonrası) → shared → null (kill-switch) | — |
| DSP | **Neva core: 31-band RBJ parametrik EQ + Linkwitz-Riley XO + EBU R128 (libebur128) + true-peak limiter + lock-free SPSC ring** | MIT/BSD |
| Video decode | **ffmpeg libavcodec + D3D11VA/DXVA2/MFT HW** | LGPL |
| Video render | **libplacebo + D3D11 swapchain, BT.2390 tonemap, jinc/ewa_lanczos scaling, HDR10 P010** | LGPLv2.1+ |
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
- Sample-rate mismatch: kaynak oranı = cihaz oranı ise **resampler tam bypass** (bit-perfect null-test bunun üzerinden); uyuşmazlıkta soxr yalnız decode thread'de (libsoxr MIT)
- Hot-plug: cihaz kopması RT thread'de re-open **YAPILMAZ**; `IMMNotificationClient` (non-RT) atomic flag yazar → callback sessizlik üretir → **ADR-0105'in kendi fallback kararı** (ADR-019'un `IAudioBackend` mekanizması üzerine kurulur): exclusive → ASIO (opsiyonel, teyit sonrası) → shared → null kill-switch. Not: ADR-019'un kendi varsayılan zinciri `ASIO → WASAPI Exclusive → Shared → Null` (ADR-019:155,162); bu ADR exclusive'i bit-perfect kapısı nedeniyle **ön sıraya alır** — `tamamlar` iddiası bu sıralama farkını da kapsar.
- 192k çıkışta 512-sample periyot yalnız 2.67 ms → buffer 1024/2048'e yükseltilir; RT thread MMCSS **"Pro Audio"**, core 0 dışına sabit (SMT kardeşi boş), ses thread'i power-throttle dışında

### 2.6 C ABI sürüm sözleşmesi (debate Z2 — boşluk kapatıldı)

`neva_session_*` yüzeyi: `neva_session_api_version()` handshake'i · opak handle zorunluluğu (C++ tipi sınırı geçemez) · tüm işlevlerde hata kodu dönüşü · ABI'yi kıran her değişiklik **major sürüm artışı** gerektirir. İlk iki binding doğrulayıcısı: Python (ctypes, birincil) + C# (P/Invoke, ikincil).

### 2.7 Video yolu kopya kuralı (debate Performance #3 — bağlayıcı)

DXVA2/D3D11VA decode çıktısı (D3D11 texture) → libplacebo pl_image → render → swapchain zincirinde **zero-copy (shared handle) zorunludur**; ffmpeg gateway kullanıldığında AVFrame'den sistem belleğine kopya/readback **yapılamaz** — aksi halde ölçüm geçersiz ve 4K60 düşer (P010 ≈ 12,4 MB/kare × 60 ≈ 744 MB/s kopya trafiği riski). GPU hedefi ~200-400 MB VRAM.

## 3. Alternatives (Reddedilenler)

| Alternatif | Neden reddedildi |
|---|---|
| libvlc tüm player olarak gömme | GPL yayılım riski + 10-band EQ mahkûmiyeti; "decode gateway" (callback) olarak opsiyonel kaldı |
| BASS (AIMP yolu) | Shareware €950, ticari kilit |
| JUCE | AGPL — copyleft çatışması |
| libmpv tüm player olarak gömme | Black-box; Neva DSP/donanım hedefleriyle (k2 sürücü, Class AB telemetri) bütünleşmez |
| ASIO birincil | 2025 lisans değişikliği doğrulanamadı; WASAPI exclusive zaten bit-perfect |
| Vulkan-first libplacebo | D3D11VA decode texture'ı D3D11'dir → Vulkan geçişi kopya/interop maliyeti; ama **P99 frame time hangi yolda geçiyorsa o yol karardır** — D3D11 vs Vulkan A/B ölçüm kapıda (§5.6) |

## 4. Consequences

**Olumlu:** ticari serbest stack (LGPL dinamik link + MIT/BSD çekirdek) · RE'de doğrulanmış desenler · ADR-019/017/006'a sadık · 25 dil FFI kapısı.
**Riskler (debate ile derinleştirildi):** (1) "%1-3 CPU" tahmini **kabul edilemez güven** — forum hesabı, stereo sayımında 2× hata içeriyor; benchmark kapısı IMPLEMENTED etiketinin ön koşulu. (2) libplacebo D3D11 HDR'da GPU yükü %50-70'e çıkabilir (1060 sınıfı hedef donanımda P99 teğet). (3) ASIO lisans belirsizliği (Steinberg teyidi bekliyor). (4) exclusive + 4K eşzamanlı DPC spike = xrun adayı. (5) Native DSD API'si Windows SDK'da doğrulanmadı.
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
| 5.6 | **Benchmark kapısı (IMPLEMENTED ön koşulu)** | 62 ve 124 biquad @192k: FTZ/DAZ etkin, tek-çekirdek **≤%5**, P99 callback jitter ≤ bütçenin %50'si · 4K60 HDR10: P99/P99.9 GPU frame ≤16,6 ms · D3D11 vs Vulkan **A/B**: P99'su geçen yol karardır · **zero-copy doğrulaması** (kopya/readback yok) | self-hosted GPU |
| 5.7 | **1 saat stres** | 4K60+192k, **nightly self-hosted** (per-merge DEĞİL, GPU şart), retry YASAK; LatencyMon ≥30 dk DPC/ISR kaydı; AC **ve** pil+throttling koşullarının ikisi; xrun=0 + DPC raporu `.ai/reports/`'a kanıt dosyası | nightly |
| 5.8 | **Hot-plug testi** | Oynatım sırasında cihaz koparma → sessizlik → fallback zinciri (ADR-019) → konum koruma | hardware-in-loop |
| 5.9 | **Lisans denetimi** | ffmpeg build'de `--enable-gpl` yasağı assert + atıf dosyası varlık kontrolü (otomatik); LGPL dinamik-link yorumu = compliance-auditor inceleme kapısı (otomatik değil, ayrıştırıldı) | CI + auditor |

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
3. ASIO SDK lisans teyidi (Steinberg) → Aşama 2 eklentisi
4. macOS CoreAudio / Linux PipeWire adapter (ADR-019) — Windows bit-perfect kanıtından sonra
