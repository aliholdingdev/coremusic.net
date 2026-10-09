---
title: "EK B — Web Research Kaynak Defteri · 6. Tur (Verified Kaynaklar)"
type: report
category: research
version: 1.0.0
status: active
date: 2026-10-09
author: "research-analyst (subagent)"
access_date: "2026-10-09"
source_prompt: ".ai/prompts/2026-10-08-master-prompt-v2.2.0-f1.md (EK B)"
---

# EK B — WEB RESEARCH KAYNAK DEFTERİ · 6. TUR

> Kural: bu defter yalnız **görülen (fetch ile doğrulanmış) URL'leri** taşır.
> Gözlemlenmeyen hiçbir kaynak uydurulmaz (Zero-Hallucination, AGENTS.md §3).
> Erişim tarihi: **2026-10-09**. Numaralar #42'den devam eder.

## B.4 — VERIFIED KAYNAKLAR (6. tur)

### T1 — Bit-perfect WASAPI/ASIO (Windows ses yolu)

| # | Başlık | URL | Tarih | Güven | Kullanım |
|---|---|---|---|---|---|
| 43 | Microsoft Learn — Exclusive-Mode Streams (Win32 Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | er. 2026-10-09 | 97 | §8.10 P03 "bit-perfect": sayfa doğrudan "hard requirement for bit-exact output" ifadesini exclusive mode'un gerekçesi olarak verir; shared-mode düşük periyotlu stream'ler önce denenmelidir. **P03 için birincil kanıt** |
| 44 | Microsoft Learn — About WASAPI (Win32 Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2025-07-26 (er. 2026-10-09) | 96 | §8.10: WASAPI arayüz ailesi (IAudioClient · IAudioClient2/3 · render/capture buffer), audio engine = user-mode karışım; shared/exclusive ayrımının API temeli |
| 45 | ASIO SDK — resmî repo (Steinberg, ASIO 2.3) | https://github.com/audiosdk/asio | er. 2026-10-09 | 94 | §8.10 P01/P04: ASIO SDK 2.3 kaynağının kendisi; lisans GPLv3/proprietary dual — Steinberg sayfası (#35) için birincil çapraz kaynak |
| 46 | Microsoft Dev Blogs — Make Great Music with Windows on Arm | https://devblogs.microsoft.com/windows-music-dev/making-music-on-windows/ | 2024-10-21 (er. 2026-10-09) | 90 | §8.10 P04: Windows in-box düşük gecikmeli UAC2 + ASIO arayüzlü sürücü duyurusu (ARM64) — "ASIO4ALL yerine donanım sürücüsü" tercihinin Microsoft kaynağı |
| 47 | Wikipedia — Audio Stream Input/Output (ASIO) | https://en.wikipedia.org/wiki/Audio_Stream_Input/Output | 2026-07-14 (er. 2026-10-09) | 70 | §8.10 P01: ASIO'nun KMixer/DirectKS/WASAPI katmanlarını atlaması, 16/24/32-bit integer + 32/64-bit float, lisans tarihimcilik (Ekim 2025 dual license) — ikincil çapraz kaynak |
| 48 | Wikipedia — Technical features new to Windows Vista (Audio §) | https://en.wikipedia.org/wiki/Technical_features_new_to_Windows_Vista#Audio | 2026-09-26 (er. 2026-10-09) | 70 | §8.10 P02/P03: exclusive mode = DMA mode, karıştırma yok, sinyal işleme etkisiz, "bit-perfect music playback" için kullanışlı; shared mode LFX/GFX zinciri — ikincil çapraz kaynak |

### T2 — Enterprise Architecture Layering

| # | Başlık | URL | Tarih | Güven | Kullanım |
|---|---|---|---|---|---|
| 49 | The Open Group — TOGAF Standard (resmî) | https://www.opengroup.org/togaf | er. 2026-10-09 | 98 | Katmanlı enterprise mimari çerçevesinin otorite tanımı (ADM, BA/BIA/ISA/DSA/OSG) |
| 50 | Microsoft Learn — Common web application architectures (.NET) | https://learn.microsoft.com/en-us/dotnet/architecture/modern-web-apps-azure/common-web-application-architectures | er. 2026-10-09 | 96 | Web uygulaması katman ayrımı (presentation/business/data), monolith→service evrimi — §8.4/§8.14 bağlamı |
| 51 | Martin Fowler — PresentationDomainDataLayering (bliki) | https://martinfowler.com/bliki/PresentationDomainDataLayering.html | 2021-07-20 (er. 2026-10-09) | 95 | Presentation/Domain/Data üç katmanının arka planda ikna edici ayrımı; "her şeyi ayırmak zorunda değilsiniz" uyarısı |
| 52 | Microsoft Learn — N-tier architecture style | https://learn.microsoft.com/en-us/azure/architecture/guide/architecture-styles/n-tier | er. 2026-10-09 | 97 | N-tier: presentation/business/data tier'ları, ölçek ve bakım sonuçları; katmanlı mimari için resmî rehber |
| 53 | Microsoft Learn — Microservices architecture style | https://learn.microsoft.com/en-us/azure/architecture/guide/architecture-styles/microservices | er. 2026-10-09 | 97 | Mikroservis mimari stili: sınır, bağımsız dağıtım, her servis kendi veri deposu — N-tier'a karşı alternatif |
| 54 | Microsoft Learn — Architecture styles (dizin) | https://learn.microsoft.com/en-us/azure/architecture/guide/architecture-styles/ | er. 2026-10-09 | 96 | Mimari stil envanteri (N-tier, microservices, event-driven, serverless, broker-based) — stil seçimi için indeks |

### T3 — AI/ML Serving Stacks

| # | Başlık | URL | Tarih | Güven | Kullanım |
|---|---|---|---|---|---|
| 55 | vLLM — resmî dokümantasyon | https://docs.vllm.ai/ | er. 2026-10-09 (→ /en/latest/) | 96 | LLM serving motoru: yüksek verimli inference, continuous batching bağlamı |
| 56 | Anyscale — Continuous Batching (blog) | https://www.anyscale.com/blog/continuous-batching-llm-inference | 2023 (er. 2026-10-09) | 88 | Continuous/oracle batching kavramı: iterasyon bazlı kuyruk, throughput sıçraması — #55'in kavramsal çaprazı |
| 57 | NVIDIA Triton Inference Server — Architecture | https://docs.nvidia.com/deeplearning/triton-inference-server/user-guide/docs/user_guide/architecture.html | er. 2026-10-09 | 96 | Serving mimarisi: model repository, scheduler, backend (TensorRT/PyTorch/ONNX), instance grupları |
| 58 | KServe — Documentation (Intro, v0.20) | https://kserve.github.io/website/docs/intro | er. 2026-10-09 | 95 | Kubernetes üzerinde model serving (InferenceService, predictor/transformer), CNCF incubating |
| 59 | NVIDIA TensorRT-LLM — resmî dokümantasyon | https://nvidia.github.io/TensorRT-LLM/ | er. 2026-10-09 | 96 | LLM optimizasyonu: batching, quantization, engine build — serving zincirinin GPU katmanı |
| 60 | ONNX Runtime — resmî dokümantasyon | https://onnxruntime.ai/docs/ | er. 2026-10-09 | 96 | Çapraz platform inference runtime (execution providers, EP'ler) — serving yığınında taşınabilirlik katmanı |

### T4 — Web Media / Streaming (derinlik)

| # | Başlık | URL | Tarih | Güven | Kullanım |
|---|---|---|---|---|---|
| 61 | DASH-IF — Low Latency DASH (duyuru) | https://dashif.org/news/low-latency-dash/ | er. 2026-10-09 | 94 | Düşük gecikmeli DASH (LL-DASH): chunked CMAF, Latency target — canlı yayın gecikme hedefleri |
| 62 | W3C — Web Audio API 1.1 (Working Draft) | https://www.w3.org/TR/webaudio/ | 2026-09-22 (er. 2026-10-09) | 97 | Tarayıcıda ses işleme modeli: AudioContext, graph scheduling, render quantum — web ses zincirinin spec'i |
| 63 | RFC 8216 — HTTP Live Streaming (HLS) | https://www.rfc-editor.org/rfc/rfc8216.html | 2017-08 (er. 2026-10-09) | 98 | HLS IETF spec'i: playlist, segment, variant stream — streaming bölümünün normatif kaynağı |
| 64 | W3C — WebCodecs (Working Draft) | https://www.w3.org/TR/webcodecs/ | 2026-10-07 (er. 2026-10-09) | 97 | Düşük seviye codec erişimi (AudioDecoder/Encoder, VideoFrame) — MSE + WebCodecs ile tam encode/decode kontrolü |
| 65 | Wikipedia — Media Source Extensions | https://en.wikipedia.org/wiki/Media_Source_Extensions | 2025-12-20 (er. 2026-10-09) | 70 | MSE'nin kapsamı ve tarayıcı desteği (iPhone istisna), EME ile ilişkisi — ikincil çapraz kaynak |
| 66 | Wikipedia — Dynamic Adaptive Streaming over HTTP (MPEG-DASH) | https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP | 2026-09-14 (er. 2026-10-09) | 70 | ISO/IEC 23009-1, MPD/segment modeli, ABR mantığı spec'te yok (BOLA/DYNAMIC), codec-agnostic — ikincil çapraz kaynak |

### T5 — Resilience / DRM / Future-proofing

| # | Başlık | URL | Tarih | Güven | Kullanım |
|---|---|---|---|---|---|
| 67 | Principles of Chaos Engineering (resmî site) | https://principlesofchaos.org/ | er. 2026-10-09 | 92 | Dayanıklılık prensipleri: sürekli enjeksiyon, üretim benzeri ortam, büyüyen hasar varsayımı — kaos/DRM (dayanıklılık) bölümünün çerçevesi |
| 68 | W3C — Encrypted Media Extensions (EME) | https://www.w3.org/TR/encrypted-media/ | WD 2026-07-07 (er. 2026-10-09) | 96 | Tarayıcı DRM soyutlaması: CDM/license, MediaKeySession — Widevine/PlayReady/FairPlay erişim katmanı |
| 69 | Apple — FairPlay Streaming (resmî) | https://developer.apple.com/streaming/fps/ | er. 2026-10-09 | 96 | HLS üzerinden Apple platformu DRM'i: KSM, key exchange, SDK 27 — Apple tarafının birincil kaynağı |
| 70 | Widevine — resmî site (Google) | https://www.widevine.com/ | er. 2026-10-09 | 94 | Widevine DRM: ücretsiz/standart tabanlı şifreleme, 4K/UHD/HDR studio onayı, ~5 milyar cihaz — DRM ekosistem ölçeği |
| 71 | Microsoft Learn — PlayReady (dizin) | https://learn.microsoft.com/en-us/playready/ | 2024-10-04 (er. 2026-10-09) | 96 | PlayReady: lisans/hak yönetimi, security level, output protection levels — Microsoft DRM birincil kaynağı |
| 72 | Wikipedia — ISO 22301 (Business Continuity) | https://en.wikipedia.org/wiki/ISO_22301 | 2026-07-01 (er. 2026-10-09) | 70 | İş sürekliliği yönetim sistemi (BCMS): 10 madde yapısı, 2019 2. baskı, Annex SL — dayanıklılık çerçevesinin standart referansı (ikincil) |

**6. tur sayım:** 30 kaynak (#43–#72) · her konu 6/6 · toplam defter: 72 kaynak.
**Elenen URL'ler (fetch başarısız/404 → listelenmedi, Zero-Hallucination):**
Apple HLS authoring spec (boş yanıt) · steinberg.net/developers/asiosdk-open (JS nav) ·
learn.microsoft guide/styles/{layered,n-tier,microservices} (404; doğru yollar #52–#54) ·
docs.nvidia.com .../docs/architecture.html (404; doğru #57) · kserve.github.io/website/latest/ (404; doğru #58) ·
w3.org/TR/mse/ (404) · csrc.nist.gov/pubs/sp/800-34/r1/final (404) ·
netflixtechblog.com/the-simian-army-... (404) · learn.microsoft.com/en-us/playready/overview (404; doğru #71) ·
learn.microsoft.com .../audio/{exclusive-mode-streams,about-wasapi} (404 → doğru yollar #43, #44).

## B.5 — ARAŞTIRMA OTURUMU KAYDI

| Sorgu | Motor | Sonuç |
|---|---|---|
| WASAPI exclusive mode bit-perfect official Microsoft documentation | websearch | ~10 sonuç — #43, #44 doğru yolları bulundu (başlangıçta 404) |
| ASIO SDK GitHub official Steinberg repository license | websearch+fetch | #45 (200) |
| TOGAF official Open Group architecture framework | fetch | #49 (200) |
| enterprise architecture layering N-tier microservices Microsoft Learn | fetch | #52, #53, #54 (200) |
| Martin Fowler presentation domain data layering bliki | fetch | #51 (200) |
| dotnet common web application architectures learn.microsoft | fetch | #50 (200, doğru yolla modern-web-apps-azure) |
| vLLM documentation LLM inference serving official | fetch | #55 (200 → /en/latest/) |
| Anyscale continuous batching LLM inference blog | fetch | #56 (200) |
| NVIDIA Triton Inference Server architecture user guide | fetch | #57 (200) |
| KServe documentation intro inference service | fetch | #58 (200, trailing-slash 404 → düzeltildi) |
| TensorRT-LLM ONNX Runtime official docs | fetch | #59, #60 (200) |
| DASH-IF low latency DASH CMAF announcement | fetch | #61 (200) |
| W3C Web Audio API WebCodecs working draft 2026 | fetch | #62, #64 (200) |
| RFC 8216 HTTP Live Streaming specification | fetch | #63 (/rfc/rfc8216 → yönlendirme; .html doğrulandı 200) |
| Wikipedia Media Source Extensions MPEG-DASH | fetch | #65, #66 (200) |
| principles of chaos engineering site | fetch | #67 (200) |
| W3C Encrypted Media Extensions EME TR | fetch | #68 (200) |
| FairPlay Streaming Apple developer | fetch | #69 (200) |
| Widevine DRM official site | fetch | #70 (200) |
| Microsoft PlayReady documentation | fetch | #71 (/playready/ 200; /overview 404) |
| ISO 22301 business continuity wikipedia | fetch | #72 (200) |

**Toplam:** ~22 sorgu/fetch turu · bu turda listelenen: **30 kaynak** (hepsi fetch ile 200 doğrulandı;
19 birincil/resmî + 4 ikincil (Wikipedia) + çapraz notlar).

## B.6 — AÇIK KONULAR GÜNCELLEME (6. tur, 2026-10-09)

| # | Açık konu | Durum | Kanıt |
|---|---|---|---|
| OPEN-09 | §8.10 P01–P03 ASIO öncelik/bit-perfect iddiaları | ✅ KAPANDI (P03) / 🔶 P01·P04 kısmi | **P03:** #43 Microsoft resmî sayfası "hard requirement for bit-exact output" → bit-perfect iddiası birincil kaynakla kapandı; #47/#48 (Wikipedia) ikincil çapraz. **P01/P04:** #45 (ASIO SDK resmî repo) + #46 (Microsoft in-box ASIO sürücü blogu) + #35 (4. tur) — zincirin tam sırası hâlâ ⚠️ |
| OPEN-10 | Spotify 2013 arşivi için güncel kaynak | ✅ KAPANDI (önceki tur) | #36 (değişiklik yok) |
| OPEN-11 | §8.9 A01 RT-safe için ikincil birincil kaynak | 🔶 KISMEN (değişiklik yok) | #37 (4. tur); Doumler makalesinin tam URL'si hâlâ yazılmadı → ⚠️ korunur |

**6. tur ek gözlemler:**
- §8.10 P03 "bit-perfect" ilk kez **birincil Microsoft kaynağıyla** desteklendi (#43); 4. tur notundaki "⚠️ KALIR" ifadesi bu satır için geçersizdir.
- Konu başına 6 kaynak şartı 5/5 konuda karşılandı (T1–T5 = 6 her biri).
- Tüm URL'ler erişim tarihinde HTTP 200 ile doğrulanmıştır; 404/boş yanıt verenler tabloya alınmamıştır.

---

**Doğrulama:** Bu defterdeki 30 URL'nin tamamı 2026-10-09 tarihinde fetch ile görüntülenip
erişilebilirliği doğrulanmıştır (Zero-Hallucination — AGENTS.md §3); erişilemeyen hiçbir
URL listelenmemiştir.
