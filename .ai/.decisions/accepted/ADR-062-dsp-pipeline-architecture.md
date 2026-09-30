---
title: "CoreMusic — ADR-062: DSP Pipeline Architecture (Sinyal Zinciri Sırası · Hard-RT Kısıtları Hizası · Katman Sınırı · RT-Güvenli Parametre Değişimi)"
type: "architecture-decision"
category: "dsp"
date: "2026-09-30"
updated: "2026-09-30"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic DSP pipeline (A0/Embedded-DSP) kararı: (a) **sinyal zinciri** tek, sabit sıralı seri blok zinciridir — Giriş → EQ (grafik → parametrik) → Dynamics → Efekt → Mix/Route → Master → Çıkış; paralel EQ **yok** (ADR-025-d hizası), (b) **hard-RT kısıtları** ADR-017'ye tam hizalıdır (buffer/latency bütçesi, tahsisat/kilit/bloklayıcı I/O yasağı, xrun politikası) — **tekrar yok**, bu ADR yalnız pipeline'a özgü(stage) ekler, (c) **katman sınırı**: pipeline mantığı bu ADR'de, yürütme yeri ve RT garantisi sahibi ADR-017'nin 3 katmanındadır (XMOS firmware · JUCE/Neva Engine · ASIO/WASAPI) — bu ADR katmanları **yeniden almaz**, (d) **parametre değişimi**: RT-güvenli (lock-free kuyruk/çift-tampon + blok-sınırı uygulama + one-pole smoothing, zipper-noise önleme, DF2T/float32/FTZ) — ADR-025-e hizası"
kaynak: "Disk kanıtı taraması (2026-09-30: `.ai/.decisions/index.md:93` slug satırı MEVCUT (`ADR-062-dsp-pipeline-architecture`) · `.ai/index.md:694`, `.ai/brain.md:1010`, `.ai/keys.md:286`, `.ai/architecture/index.md:98`, `k3-ses-motoru/README.md:329`, `k3-ses-motoru/CLAUDE.md:62` · bu işlem öncesi `**/ADR-062*.md` = **0 dosya** → metin diskte YOKTU · repo geneli `*.cpp|*.h|*.hpp|*.c` (vendor/node_modules/.git/dist/build hariç) = **0 dosya** (**tekrar doğrulandı**) → DSP kodu YOK · `.ai/architecture/k3-ses-motoru/` = **18** .md · `architecture/firmware/` = **8** .md · `electronic/` (kök + `.ai/architecture/`) Test-Path = **False** → **0 dosya** (ADR-061 bulgusu doğrulandı) · `.ai/projects/` Test-Path = **False** → AGENTS §24.3 'NevaEngine dizini var, 0 dosya' ifadesi **eskimiş**: dizin **hiç yok** · `ADR-063`/`ADR-064` glob = **0 dosya** (yalnız ADR-061 diskte) → düz metin + ⚠️ · `.ai/log.md:98` 'NevaEngine 12 header ~79KB' iddiası diskte **karşılıksız** ⚠️ · `dsp-chain.md:16-39, 41-65, 68-103, 108, 143, 169-174, 373-381` · `mixer-routing.md:16, 34, 174, 375` · `neva-engine-core.md:12` · `eq-parametric.md:12` · `dynamics-compressor.md:12` · `stream-buffer.md:12`) + web araştırması (**5 sorgu / 24 adlandırılmış kaynak**)"
governance: "Red Team → Human Mode → Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-062: DSP Pipeline Architecture

> **Durum:** accepted (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-09-30 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` — **Slug:** `ADR-062-dsp-pipeline-architecture` (dizin otoritesi: [[../index.md]] satır 93 — gerçek disk slug'ı ile birebir hizalı ✅)
> **İlgili kararlar:** [[ADR-017-dsp-hardware-mode]] (3 katman XMOS/JUCE/ASIO + hard-RT kısıtları, buffer/latency bütçesi, xrun politikası — **bu ADR'nin zorunlu girdisi; kısıtları yeniden almaz**) · [[ADR-025-professional-eq-system]] (çift EQ: 31-band grafik + ayrı parametrik, sıra grafik → parametrik, RT-güvenli biquad cascade — **bu ADR EQ'yu yeniden karar almaz, yalnız zincir içindeki yerini bağlar**) · [[ADR-061-electronics-architecture]] (L6 sınırı — "DSP boru hattı → ADR-062" bölüm sınırı bu ADR ile kapanır; dosya diskte VAR ✅) · [[ADR-006-performance-targets]] (latency bütçesi `<10ms ASIO / <20ms WASAPI` — kapı birebir korunur) · [[ADR-038-8-1-sound-card-chip-selection]] + [[ADR-089-classab-24v]] + [[ADR-090-channel-variant-product-family]] (donanım/ürün ailesi — **pipeline'ın taşıyıcısı, bu ADR çipi yeniden seçmez**) · [[ADR-019-per-os-neva-player]] (fallback/kill-switch zinciri) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · [[ADR-013-rate-limiting-apcu]] (sayaç/limit/alert ruhu — xrun telemetrisi) · karar dizini [[../index.md]] **satır 93**.
> **⚠️ VERIFICATION REQUIRED (komşu metinler hâlâ yok):** `ADR-063-hardware-design-standards` ve `ADR-064-electronics-platform-architecture` **diskte dosya olarak YOK** (glob = 0) → bu ADR'de **düz metindir, wiki-link değildir**; ayrıca `dsp-chain.md` "Gerçek" sütunları (0.08ms / 6.5% / 8MB / 1.2M frames) **kod 0 iken ölçülmüş sayılamaz**; `log.md:98` NevaEngine 12 header iddiası ve `brain.md:1010`/`keys.md:286` özet satırları **kod kanıtı olmadan IMPLEMENTED sayılmaz**.
> **Bölüm sınırı (ADR-061 §2(c) ile kenetli):** ADR-061 L6 katmanını + kart/modül hiyerarşisini yazdı; **pipeline'ı bu ADR yazar**; tasarım standartları `ADR-063`'e, platform/L0-L6 geneli `ADR-064`'e aittir (iki dosya da ⚠️ metinsiz) → bu ADR onların konusunu **yazmaz**.
> **Frozen değil:** ADR-001–037 frozen kapsamı dışındadır; kural 4'teki "yeni ADR ≥ 088" ile arşivin 062 slotu arasındaki **numara çakışması ADR-061 §5.1/9'da raporlandı** — bu ADR aynı çakışmayı **tekrar raporlar, düzelmez**.

---

## 1. Bağlam (Context)

CoreMusic'in ses işleme tasarımı vault'ta **dağınık ama fazlasıyla yazılmış** durumda: 15 aşamalı pipeline şeması, stage arayüzü (`noexcept`), biquad yapısı, 31 bant EQ katsayısı, mixer bus mimarisi ve jitter buffer tanımı `architecture/k3-ses-motoru/` altındaki **18 dokümanda** var; buna karşılık repo'da **tek bir `*.cpp`/`*.h` dosyası bile yok** (§1.1 — iki ayrı tarama). Bu yüzden "pipeline nasıl işliyor?" sorusunun cevabı **kodda değil, dört ayrı belgede** yatıyor ve hangi belgenin **karar** olduğu yazmıyor: `dsp-chain.md` bir şema + örnek kod veriyor, `eq-parametric.md` EQ'yu tarif ediyor, `mixer-routing.md` bus'ları tarif ediyor, ADR-017 katmanları ve RT kısıtlarını bağlamış, ADR-025 ise EQ'nun kendisini bağlamış. **Sıra (giriş→EQ→dynamics→mix→çıkış), iki EQ'nun zincirdeki yeri (seri mi paralel mi), pipeline'ın katman sınırı ve parametre değişiminin RT-güvenli yolu hiçbir tek belgede yok.** Bu ADR o dört soruyu tek kayıtta bağlar — ne kod yazar, ne katmanları yeniden karar alır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-09-30 taraması)

| İddia | Kanıt | Etiket |
|-------|-------|--------|
| ADR-062 slotu ayrılmış mı? | [[../index.md]] `:93` → `\| ../brain.md ADR-062-dsp-pipeline-architecture \| DSP Pipeline Architecture \| Electronics \|` · [[../../index.md]] `:694` · [[../../brain.md]] `:1010` · [[../../keys.md]] `:286` · `.ai/architecture/index.md:98` (`K3 \| Ses Motoru \| 50 \| 18 \| ADR-025, ADR-062`) | ✅ **KAYITLI** (6 indeks/katalog satırı) |
| ADR-062 dosyası bu işlem öncesi diskte var mıydı? | glob `.ai/.decisions/**/ADR-06*` = **yalnız ADR-061** (062/063/064 = 0) | ❌ **YOKTU** → bu işlemde yazılıyor |
| **DSP kodu** (biquad/filter/gain/mixer/buffer) | repo geneli `*.cpp\|*.h\|*.hpp\|*.c` (vendor/node_modules/.git/dist/build hariç) = **0 dosya**; `biquad\|processBlock\|lock-free` grep'i `*.cpp/h` = **0 isabet** — **iki bağımsız tarama, ikisi de 0** | ⏳ **PLANNED** (kod yok → her aşama PLANNED) |
| `log.md:98` "NevaEngine 12 header, ~79KB, `dsp_pipeline.h`, `biquad_filter.h`" iddiası | diskte bu dosyalar **yok** (kod taraması 0) | ⚠️ **VAULT BEYANI — DOĞRULANAMADI** |
| NevaEngine dizini (AGENTS §24.3 "dizin var, 0 dosya") | `.ai/projects/` → `Test-Path` = **False** (`.ai/projects` **hiç yok**) | ⚠️ **İFADE ESKİMİŞ — dizin yok, dosya da yok** |
| DSP motoru dokümanları | `architecture/k3-ses-motoru/` = **18** .md: `index · CLAUDE · README · dsp-chain · mixer-routing · neva-engine-core · eq-parametric · dynamics-compressor · effects-reverb · effects-chorus-delay · channel-processing · sample-rate-conversion · bit-depth-conversion · format-decoder · surround-decoder · playback-gapless · stream-buffer · analysis-spectrum` | ✅ **IMPLEMENTED** (doküman) |
| Pipeline şeması | `dsp-chain.md:16-39` → 15 aşama (Input Gain → Channel Map → Format Conv → SR Conv → EQ Parametric → Dynamics → Reverb → Chorus/Delay → Surround Decode → Mixer/Route → Master EQ → Limiting → Dithering → Output Gain → Format Output); `:41-65` `IDSPStage` `noexcept` arayüzü; `:68-103` biquad (DF1 benzeri tek-örnek + batch); `:108, :143, :169-174` örnek implementasyon **yalnız 3 aşama (1, 2, 5)**; `:174` `MAX_BANDS = 31` | ✅ **IMPLEMENTED** (doküman) / ⏳ kod yok |
| Pipeline performans tablosu | `dsp-chain.md:373-381` → Hedef `<0.1ms / <8% / <10MB / >1M fps` · "Gerçek" `0.08ms / 6.5% / 8MB / 1.2M` | ⚠️ **"Gerçek" sütunu KODSUZ — ölçülmüş sayılmaz (V.R.)** |
| Mixer/bus | `mixer-routing.md:16` bus mimarisi · `:34` channel strip · `:174` routing matrix · `:375` "Durum: Implementasyon" | ✅ IMPLEMENTED (doküman) / ⏳ kod |
| Motor çekirdeği | `neva-engine-core.md:12` "C++20 … real-time safe … lock-free"; `ADR-017`'den `:329` `bufferSize = 256` | ✅ IMPLEMENTED (doküman) |
| EQ / dynamics / stream | `eq-parametric.md:12` (31 bant, biquad IIR) · `dynamics-compressor.md:12` (compressor/limiter/expander) · `stream-buffer.md:12` (jitter buffer) | ✅ IMPLEMENTED (doküman) |
| Katman-1 spec (firmware) | `architecture/firmware/` = **8** .md (`xmos-firmware · i2s-driver · dsp-firmware · usb-audio-firmware · bootloader · gpio-control · mcu-support · index`) — `*.xc`/`*.c` = **0** | ✅ IMPLEMENTED (doküman) / ⏳ kod |
| `electronic/` dizini | `Test-Path` = **False** (kök `electronic/` ve `.ai/architecture/electronic/`) → **0 dosya** — ADR-061'in 0 dosya bulgusu **doğrulandı** | ❌ **YOK — SSOT DEĞİL** |
| ADR-063 / ADR-064 metinleri | glob = **0 dosya**; [[../index.md]] `:94-95` + [[../../index.md]] `:695-696` satırları var | ⚠️ **VERIFICATION REQUIRED** (düz metin) |
| ADR-017 / ADR-025 / ADR-061 / ADR-006 / ADR-038 dosyaları | glob → `.ai/.decisions/accepted/` altında **MEVCUT** | ✅ **IMPLEMENTED** (bağlantılı ADR) |
| `faz6-link-ledger.md:89` | `ADR-061..064` kırık hedef listesi | ✅ RAPORLANDI — 061 kapatıldı, **062 bu işlemle kapanır**, 063/064 açık |

### 1.2 Sorun Tanımı

1. **Sıra hiçbir yerde bağlanmamış.** `dsp-chain.md` şeması 15 aşamayı sıralıyor ama "bu sıra **kararlıdır**, değiştirmek yeni ADR ister" diyen tek satır yok; `mixer-routing.md` ayrı bir bakış açısı veriyor. Sıra bozulursa gain staging, faz ve xrun davranışı değişir — kimse bunu sahiplenmiyor.
2. **İki EQ'nun yeri belirsiz.** ADR-025 iki ayrı EQ bloğu ve "grafik → parametrik" sırası kararlaştırdı; `dsp-chain.md` ise şemada **iki EQ düğümü** gösteriyor (`[5] EQ Parametric` ve `[11] Master EQ`). `[11]`'in ADR-025'teki **grafik EQ** ile aynı şey olup olmadığı **kanıtlanamadı** — paralel/seri sorusu cevapsız kalırsa iki agent iki farklı zincir üretir.
3. **RT kısıtları ile pipeline ayrı dosyalarda.** ADR-017 hard-RT'yi (tahsisat/kilit/I/O yasağı, buffer bütçesi, xrun) **katmanlar** üzerinden yazdı; pipeline'ın **kendi** stage bazlı ekleri (her aşama `noexcept`, blok sınırında parametre, stage CPU bütçesi, latency toplamı) hiçbir yerde değil.
4. **Parametre değişimi yazılı değil.** UI'dan gelen EQ/kazanç değişikliğinin RT thread'e nasıl geçtiği (kilit mi, atomik mi, ne zaman uygulanır, nasıl yumuşatılır) yalnız ADR-025'in "callback'te tahsisat yok, DF2T, denormal" maddelerinde **dağınık** duruyor; zipper noise ve blok-sınırı kuralı yok.
5. **Kanıt ile iddia ayrıştı.** Doküman "Gerçek 6.5% CPU / 0.08ms" yazarken kod **0 dosya**; `log.md:98` 12 header bildiriyor, disk boş. Bu ADR **kod olmadan IMPLEMENTED yazamaz**.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "audio DSP pipeline architecture real-time signal chain stages 2025" · (2) "real-time audio processing constraints buffer size latency xrun underrun zero-allocation callback" · (3) "parameter smoothing audio plugin one-pole zipper noise RT-safe atomic lock-free real-time thread" · (4) "pro audio signal flow order gain staging EQ before compressor mixing chain best practice" · (5) "audio plugin graph processing architecture JUCE AudioProcessorGraph block-based DAG 2025 real-time safe" |
| Web Search **Konusu** | **(1)** Boru hattı/blok mimarisi — sabit sıralı aşamalı zincir mi, modüler yeniden düzenlenebilir bloklar mı; **(2)** hard-RT kısıtları — buffer/latency ilişkisi, xrun/underrun, tahsisatsız callback; **(3)** parametre değişimi — smoothing (one-pole/ramp), zipper noise, lock-free/atomic el sıkışma; **(4)** sinyal akışı sırası — gain staging, EQ önce mi dynamics önce mi; **(5)** zincir topolojisi — statik sıra mı, graf (DAG) mı ve graf'in RT thread güvenliği. |
| Web Search **Bağlamı** | CoreMusic: 15 aşamalı şema + `noexcept` stage arayüzü dokümanda var (`dsp-chain.md:16-65`), ama kod **0 dosya**; ADR-017 katmanları ve RT bütçesini, ADR-025 çift EQ'yu bağlamış; **sıra, seri/paralel EQ kararı ve parametre geçiş yolu** hiçbir belgede yok. Araştırma bu üç boşluğu hedefliyor; sayısal kapılar ADR-006'da (`<10ms/<20ms`) zaten vault'a bağlı. |
| Web Search **Kısa Açıklama** | **(1)** Endüstri, gömülü/sistem ses işlemede zinciri **modüler bloklarla sabit sıralı** kuruyor: FOH/DSP literatürü "EQ, dynamics, mixing, level control" bloklarından açık mimarili (open-architecture) sinyal yolu kurulduğunu; Analog Devices EngineerZone örneği DSP zincirini **dört aşamalı** (toplama → ön-işleme → işlem → gösterim) veriyor; DSP Concepts Audio Weaver blokları **önceden yerleştirilmiş, statik** bir layout olarak dağıtıp çok-çekirdeğe bölüyor (yeniden düzenleme tasarım zamanında yapılır). **(2)** Ross Bencina: callback her buffer'ı **zamanında** üretmek zorundadır (ör. 5.8ms'lik buffer için "no exceptions"); normal buffer aralığı **1–5ms**, 64-sample (~1.45ms @44.1k) agresif ama yapılabilir; tahsisat (heap) callback içinde **yapılmaz**, bellek önceden ayrılır; xrun'lar (PipeWire/ALSA örneğinde `pw-top` ERR sütunu) **izlenir**, hedef quantuma inilememişse buffer artırılır; MATLAB notu **dropout olan ölçümden sonra ölçülen latency'nin değiştiğini** (yani ölçümün xrun'suz koşula bağlı olduğunu) söylüyor. **(3)** Parametre yumuşatma standart: hedef değere **one-pole interpolasyon** (ör. 5ms) + **lock-free kuyruk** üzerinden ses-iş parçacığına aktarım → zipper noise önlenir; `std::atomic` modern platformlarda **lock-free** sayılır; gerçek zamanlı olmayan iş parçacığında grafik/efekt ekleme-çıkarma yapılırsa RT thread **çalışırken free edilme** riski doğar (incremental reconcile başka iş parçacığına alınır). **(4)** iZotope/mA: EQ'nun **compressor'dan önce** gelmesi "daha geleneksel" kabul edilir; gain staging her aşamada **headroom** ister (tepe ≈ −6 dBFS, ortalama ≈ −18 dBFS); kompresör sonrası EQ küçük adımlarla kullanılır. **(5)** JUCE `AudioProcessorGraph` bir **graf** sunar ama graf'i işleme thread'i **dışında** değiştirmek thread-safety/pops-clicks sorunu yaratır (JUCE forum) → pratikte graf yeniden düzenlemesi blok sınırında ve kilitli/taklitli yapılır. |
| Web Search **Uzun Açıklama** | **(i) Zincir topolojisi:** gömülü ses motorları (Audio Weaver, DSP Concepts; Symetrix Cognio 2026 — FOH) tipik olarak **layout'u önceden tasarlar**: bloklar (gain, EQ, dynamics, mix, limiter) sabit sırayla yerleşir, çalışma anında yalnız **parametre** değişir; çok-çekirdekli dağıtım bile layout sabitken yapılır. Bu, CoreMusic'in 15 aşamalı sabit şemasıyla **aynı modeldir** ve "neden statik sıra?" sorusunu yanıtlar: yeniden düzenlenebilir graf, tasarım/yeniden dağıtma maliyeti ve RT güvenliği pahasına esneklik verir. Analog Devices zinciri (4 aşama) ve Wikipedia'nın pipeline/MAC/looptop DSP karakteri, aşamalar arası **veri akışının tek yönlü ve tahmin edilebilir** olmasını vurgular. **(ii) Hard-RT:** Bencina'nın kuralı basittir — her buffer, deadline'ında üretilir; heap tahsisatı, bloklayıcı kilit ve log I/O callback'te **yapılmaz** (performans makaleleri aynı sonuca varır: buffer önceden ayrılır, blok boyu 64–1024 örnek aralığındadır). Buffer büyütme **tolerans ↔ gecikme** takasıdır: büyük buffer CPU dalgalanmasına alan açar ama latency artırır; xrun görüldüğünde çözüm önce **buffer'ı bir kademe artırmak**tır (PipeWire rehberi: 512'den başla, xrun yoksa düş). Dropout/ölçüm ilişkisi (MATLAB) kapı değerinin **xrun'suz oturumda** ölçülmesi gerektiğini söyler — bu, ADR-017'nin "kapı = ölçülen round-trip" kuralıyla aynı. **(iii) Parametre yolu:** üç bağımsız kaynak aynı reçeteyi veriyor: hedef değer **atomik/kuyruk** ile taşınır, **ses iş parçacığında** blok sınırında uygulanır ve **one-pole/ramp** ile yumuşatılır (zipper noise); katsayı üretimi (sin/cos vb.) RT dışında ya da blok sınırında sabit bütçeyle yapılır; durum (delay line, bant bellekleri) **önceden tahsis edilir**; efekt ekleme/çıkarma gibi yapısal değişiklikler RT thread dışında reconcile edilir. **(iv) Sıra:** kaynaklar EQ→dynamics'i **varsayılan** kabul eder ama "dynamics→EQ"nun da meşru senaryosu vardır (tonal dengesizliği kompresörden sonra telafi); bu, **kullanıcı kararıyla** açık bırakılır — CoreMusic'te varsayılan ADR-025'teki gibidir ve **her iki EQ bloğu da dynamics'ten önce** yerleştirilir (kullanıcı onaylı kapsam), alternatif sıra **konfigürasyon + yeni ADR** ister. Gain staging, her bloğun girişinde headroom tutmayı zorunlu kılar (EQ boost toplanır → limiter/headroom koruması ADR-025-e ile örtüşür). **(v) Graf vs sıra:** JUCE graf API'si esneklik verir ama graf değişikliğinin RT thread ile eşzamanlılığı kilit/pops riski taşır; kilitsiz el sıkışma `std::atomic` ile yapılır. CoreMusic bugün **graf gerektirecek kadar dinamik değil** (sabit 15 aşama, değişen tek şey parametre) → statik sıra + blok sınırında parametre, graf'e göre **daha az RT riski** taşır. |
| Web Search **Paragraf Veri Uzun** | 5 sorgu / **24 adlandırılmış kaynak**: **(1)** audioXpress "November 2025 Sets the Roadmap for Audio Signal Processing" (2025-11) · Analog Devices EngineerZone "Implementing a Complete DSP Chain for Signal Analysis" (4 aşamalı zincir) · Wikipedia "Digital signal processor" (pipeline/MAC/looptop) · FOH Magazine "The History of DSP for Live Sound" (open-architecture: EQ/dynamics/mixing/level blokları; Symetrix Cognio 2026) · DSP Concepts "Audio Weaver" (önceden yerleştirilmiş layout, çok-çekirdek). **(2)** Ross Bencina "Real-time audio programming 101" (deadline, 1–5ms normal aralık, tahsisat yasağı) · scsynth "Real-time Audio Processing" (hardwareBufferSize vs blockSize, 64 örnek = 1.45ms @44.1k) · Niccolò Abate "How to write performant realtime audio code" (blok 64–1024, callback öncesi tahsisat) · oneuptime "Configure PipeWire for Low-Latency Audio" (quantum 512→düşür, xrun izleme) · MathWorks/MATLAB Answers "Varying Delay Depending on Initial Under/Overruns" (dropout → ölçüm sapması) · JUCE Forum "Real-Time Multi-Threading in an Audio Application" (graf bölme + ek gecikme). **(3)** timur.audio "Using locks in real-time audio processing, safely" (`std::atomic` lock-free) · truce.audio `#[derive(Params)]` (one-pole 5ms smoothing + lock-free kuyruk) · ModernMube/OwnAudioSharp (RT-safe preallocation, wet/dry ramp anti-zipper, atomic parameter updates, incremental reconcile) · StackOverflow "What lock-free primitives do people actually use…" · KVR Forum "Zipper Noise". **(4)** iZotope "Signal chain: order of operations" (EQ önce daha geleneksel) · Production Advice "My mastering chain – signal flow" (kompr. sonrası EQ küçük adımlar) · Avid "Guide to gain staging" (−6 dBFS tepe / −18 dBFS RMS) · macprovideo "What's The Correct Order For Effect Processing Plug-Ins?" · r/audioengineering "EQ before, after compression" (pratik oydaşma). **(5)** JUCE docs `juce::AudioProcessorGraph` / `juce::AudioProcessor` (`processBlock`, `getCallbackLock`, `Realtime` enum) · JUCE Forum "AudioProcessorGraph thread safety" (graf değişimi → pops/clicks). |
| Web Search **Sonucu** | **(1) Karar destekleniyor:** gömülü/pro ses zincirleri **sabit sıralı modüler bloklar** olarak kurulur; esneklik parametre düzeyinde kalır. **(2) Karar destekleniyor:** hard-RT = deadline + tahsisatsız/kilitsiz callback + **ölçülen** latency kapısı; xrun izlenir ve hafifletmek için önce buffer artırılır → ADR-017 bütçesiyle **uyumlu**, bu ADR'ye yalnız stage bazlı ekler bırakır. **(3) Karar destekleniyor:** parametre değişimi = atomik/kuyruk + **blok sınırında** uygulama + **one-pole smoothing** (zipper) + önceden tahsis durum belleği → ADR-025-e ile **aynı reçete**. **(4) Karar destekleniyor (varsayılan):** EQ→dynamics yaygın konvansiyondur; ters sıra **senaryo bazlı istisna** → bu ADR'de varsayılan sabitlenir, alternatif **yeni ADR** ister. **(5) Karar destekleniyor:** tam graf (DAG) yeniden düzenlemesi RT thread güvenliği gerektirir ve CoreMusic'in dinamik ihtiyacı **kanıtlanamadı** → statik sıra benimsenir; graf seçeneği §3.2'de koşul olarak saklanır. **İtiraz/karşıt bulgu:** kaynaklar buffer'ı "büyütme"yi kurtarma olarak verirken **latency'yi artırır** → tolerans ↔ gecikme takası açıkça §4.3 risk 2'ye yazıldı. |
| Web Search **Alınan Karar** | **(a) Sinyal zinciri** = **tek, sabit sıralı seri blok zinciri**: `Giriş (Input Gain) → Kanal/Format/SR → EQ-A Grafik (31-band) → EQ-B Parametrik → Dynamics (compressor/limiter) → Efekt (reverb/chorus/surround) → Mix/Route → Master → Çıkış Gain → Format Out`; **iki EQ seri, paralel değil** (ADR-025-d sırası); sıra kararı **bağlayıcıdır**, değişimi = yeni ADR. **(b) Hard-RT** = ADR-017 **birebir geçerli** (tahsisat/kilit/bloklayıcı I/O yasağı, 256 varsayılan / 128–512 aralık, kapı `<10ms ASIO / <20ms WASAPI` **ölçerek**, xrun tespit→log→kurtarma); bu ADR stage ekleri: her aşama `noexcept` + blok-başına tahsisatsız, stage CPU bütçesi, pipeline latency toplamı **ölçülür**. **(c) Katman sınırı** = ADR-017'nin 3 katmanı (XMOS firmware · JUCE/Neva Engine · ASIO/WASAPI) **bu ADR'de yeniden karar alınmaz**; bu ADR yalnız **pipeline mantığını** (aşama listesi, sıra, veri akışı, parametre yolu) tanımlar ve mantığın **her iki yürütücüde (XMOS çekirdeği ve Neva Engine) aynı sözleşmeyle** uygulanmasını şart koşar. **(d) Parametre değişimi** = UI/kontrol iş parçacığı → **lock-free SPSC kuyruk veya `std::atomic` çift-tampon (gölge katsayılar)** → RT iş parçacığı **blok sınırında** uygular; kazanç/Q/frekans **one-pole (~5ms) veya örnek-bazlı linear ramp** ile yumuşatılır (zipper yok); katsayı hesabı RT dışında ya da sabit bütçeli blok sınırında; bant bypass/aktifleştirme blok sınırında; durum belleği **önceden tahsis**; DF2T + float32 + FTZ/DAZ (ADR-025-e). |
| Web Search **Sonuç** | Karar **destekleniyor**: statik aşamalı zincir (FOH · DSP Concepts · Analog Devices · Wikipedia), hard-RT kuralları (Bencina · scsynth · Abate · PipeWire · MATLAB), parametre yolu (timur.audio · truce.audio · OwnAudioSharp · SO · KVR) ve sıra konvansiyonu (iZotope · Production Advice · Avid · macprovideo) **bağımsız kaynaklarda oybirliğiyle** var; graf alternatifi yalnız JUCE forum notuyla **koşullu** tutuldu. **İki gerilim** açıkça kabul edildi: (1) **ölçülen değer ile hedef ayrımı** — `dsp-chain.md` "Gerçek" sütunları kod 0 iken **kanıt değildir** (ADR-005) → §4.3 risk 6; (2) **buffer tolerans ↔ latency takası** → §4.3 risk 2 + ADR-017 fallback'i. **⚠️ VERIFICATION REQUIRED:** NevaEngine kodu (0 dosya), `dsp-chain.md` ölçülmüş metrikler, `[11] Master EQ` ↔ grafik EQ eşlemesi, `ADR-063`/`ADR-064` metinleri — **hiçbiri bu ADR'de "var/implement edildi" olarak yazılmadı.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR'ler (001–037) | Değiştirilemez; bu ADR yalnız okur ve atıf yapar |
| ADR-017 sınırı | 3 katman (XMOS · JUCE/ASIO) ve hard-RT kısıtları, buffer/latency bütçesi, xrun politikası **zaten kararlaştırıldı**; bu ADR bunları **yeniden almaz**, yalnız pipeline'ın onlara uyumunu yazar |
| ADR-025 sınırı | Çift EQ (31-band grafik + ≥2 bant parametrik), "grafik → parametrik" sırası, DF2T/float32/denormal, bant bypass/preset **zaten kararlaştırıldı**; bu ADR EQ'nun **teknik içeriğini** yeniden yazmaz, yalnız zincirdeki **yerini** bağlar |
| ADR-061 sınırı | L6 katman tanımı + kart/modül hiyerarşisi + bileşen politikası ADR-061'de; bu ADR yalnız "DSP boru hattı" bölümünü doldurur (ADR-061 §2(c) bağlayıcı) |
| ADR-063 / ADR-064 | Dosyalar diskte **YOK** → tasarım standartları ve platform/L0-L6 mimarisi bu ADR'de **yazılmaz** (düz metin + `⚠️ VERIFICATION REQUIRED`) |
| ADR-038 / ADR-089 / ADR-090 sınırı | Çip/ürün kararları **alt kararlardır**; pipeline bu kararları **uygular, yeniden almaz** |
| Kod kanıtı = 0 | `*.cpp/h/hpp/c` = **0 dosya** → bu ADR'nin tüm maddeleri **PLANNED** olarak etiketlenir; "IMPLEMENTED" yalnız **doküman** içindir |
| In-Place Refactoring | Dosya adı değişikliği **yok**; `.ai/.decisions/index.md:93` zaten doğru slug'u taşıyor → **dizine satır eklenmedi** (report-only) |
| Dizin özet-link düzeltmesi | `.ai/.decisions/index.md:93` hücresindeki **çift köşeli bağlantı** (hedef `../brain.md`) dosyaya değil **özete** işaret ediyor → **düzeltme sonraki vault reset'ine ertelendi**, burada **raporlanır** (kural 18: dosya/dizin adına dokunulmaz) |
| UTF-8 yazım protokolü | Tüm vault yazımları `vault-utf8-writer.mjs` üzerinden; `log.md` yalnız `append` (bayt seviyesi) |
| Hallucination sweep | Diskte olmayan `ADR-063`/`ADR-064`'e **wiki-link yok** (düz metin); `electronic/`, `projects/NevaEngine/` hedeflerine **wiki-link yok**; ölçülmüş gibi görünen metrikler `⚠️` ile işaretli |
| Debate / onay | `debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)` · Tech Lead ✅ · Arch Lead ⏳ → status `accepted`; Frozen geçişi YOK (Arch Lead ⏳ — §7.1) · 3 şart §5.1/11-14 · §6.3 |
| REDACTED | `.env`, anahtar/sertifika, BOM fiyat/parça numarası hiçbir ADR'ye yazılmaz |
| Numara serisi | Kural 4 yeni ADR'leri ADR-088+'ya ayırır; bu dosya arşivin atadığı **062** slotunu doldurur → çakışma **raporlanır, düzeltilmez** |

---

## 2. Karar (Decision)

**CoreMusic DSP pipeline'ı DÖRT maddeyle bağlayıcı ilan edilir.**

### (a) Sinyal zinciri mimarisi — tek, sabit sıralı seri zincir

| # | Aşama | İçerik | Karar durumu |
|---|-------|--------|--------------|
| 1 | **Giriş** | Input Gain + kanal eşlemesi (`dsp-chain.md` [1] Input Gain, [2] Channel Mapping) | ✅ doküman (kod PLANNED) |
| 2 | **Ön-hazırlık** | Format dönüşümü + örnek hızı dönüşümü (`[3]`, `[4]`) | ✅ doküman |
| 3 | **EQ — Grafik** | 31-band ISO 1/3-oktav, sabit Q, aktif bant 2–31 (ADR-025-b) — **kendi bypass'ı var** | ✅ ADR-025 bağlayıcı |
| 4 | **EQ — Parametrik** | ≥2 bant serbest frekans/Q/gain (ADR-025-c) — **kendi bypass'ı var** | ✅ ADR-025 bağlayıcı |
| 5 | **Dynamics** | Compressor → Limiter/Expander (`dynamics-compressor.md`; `[6]`, `[12]`) | ✅ doküman |
| 6 | **Efekt** | Reverb → Chorus/Delay → Surround Decode (`[7]`, `[8]`, `[9]`) | ✅ doküman |
| 7 | **Mix / Route** | Bus mimarisi + routing matrix (`mixer-routing.md`; `[10]`) | ✅ doküman |
| 8 | **Master** | Master EQ (bkz. aşağıdaki eşleme uyarısı) + toplam gain | ⚠️ eşleme doğrulanacak (§5.1/3) |
| 9 | **Çıkış** | Dithering → Output Gain → Format Output (`[13]`, `[14]`, `[15]`) | ✅ doküman |

**Bağlayıcı kurallar:**

1. **İki EQ AYNI seri zincirdedir — paralel değildir.** Sıra **grafik → parametrik** (ADR-025-d ile birebir). Paralel şube (iki EQ'nun çıkışını toplamak) **yoktur**: ek mix gecikmesi, faz etkileşimi ve iki kat CPU üretir (§3.1).
2. **Sıra kararlıdır.** Yukarıdaki 9 aşama sırası **değiştirilemez**; bypass bir aşamayı **atlar** (sırayı bozmaz), yeniden sıralama **yeni ADR** gerektirir.
3. **Her aşamanın kendi bypass'ı vardır** ve zincir toplamı **tek kill-switch** ile devre dışı bırakılabilir (ADR-019 fallback zinciri + ADR-017 §2.2f).
4. **Toplam latency = aşama latency'lerinin toplamı** ve **ölçülür**; hedef `dsp-chain.md:377` `<0.1ms` (pipeline içi) — kapı ADR-006'daki **ölçülen** round-trip (`<10ms ASIO / <20ms WASAPI`)dur. İç hedef ≠ kapı.
5. **⚠️ Eşleme uyarısı (uydurma YOK):** `dsp-chain.md:16-39` şemasında **iki EQ düğümü** var: `[5] EQ Parametric` ve `[11] Master EQ`. `[11]`'in (i) ADR-025'teki **grafik EQ** mı, (ii) mix'ten sonra gelen **ayrı master EQ** mı olduğu **diskte kanıtlanamadı** → bu ADR tahmin üretmez; mutabakat §5.1/3'tedir. Mutabakata kadar: **(a) maddesindeki sıra** (grafik → parametrik → dynamics) geçerlidir, `[11]` düğümü **belirsiz işaretli** kalır.

### (b) Gerçek zamanlı kısıtlar — ADR-017'ye hizalı (tekrar yok) + stage ekleri

| # | Kural | Değer / Dayanak | Durum |
|---|-------|-----------------|-------|
| 1 | **Buffer / latency bütçesi** | Varsayılan **256 sample ≈ 5.33ms @48k** (`neva-engine-core.md:329`); izinli aralık **128–512**; kapı **`<10ms ASIO` / `<20ms WASAPI`** — **ölçerek** (ADR-006 · `.ai/CLAUDE.md:429`) | ✅ ADR-017-b (tekrar yok) |
| 2 | **Tahsisat yasağı** | Callback içinde `new/delete/malloc/vector::push_back/string` genişlemesi/`throw` **yasak**; tüm pipeline buffer'ları **başlamadan önce** ayrılır (`alignas(64)`, sabit kapasite) | ✅ ADR-017-c/1,4 |
| 3 | **Kilit + bloklayıcı I/O yasağı** | `mutex/lock_guard` ve disk/DB/ağ/konsol/log/**sleep** RT yolunda **yasak**; paylaşım `std::atomic` + lock-free ring | ✅ ADR-017-c/2,3 |
| 4 | **Stage sözleşmesi (pipeline'a özgü)** | Her aşama `process(...) noexcept` · `getLatency() const noexcept` (`dsp-chain.md:41-65`); aşama **kendi durumunu blok içinde** ayırır; `virtual` çağrı zinciri **blok başına bir kez** | ⏳ PLANNED (doküman ✅) |
| 5 | **Stage CPU bütçesi (pipeline'a özgü)** | 15 aktif aşama toplamı **< %8** (`dsp-chain.md:379`) — **ölçülmeden "Gerçek" yazılmaz**; aşama başına pay hedefi = toplam / aktif aşama sayısı | ⏳ PLANNED + ⚠️ ölçüm |
| 6 | **Pipeline latency toplamı** | Hedef **< 0.1ms** (`dsp-chain.md:377`); aşama `getLatency()` değerleri toplanır, kapıya eklenir | ⏳ PLANNED + ⚠️ ölçüm |
| 7 | **Determinizm + denormal** | Aynı girdi → aynı CPU yolu; FTZ/DAZ callback başında (ADR-025-e); NaN/inf koruması bloklayıcı değil | ✅ doküman/ADR-025 |
| 8 | **Xrun politikası** | Tespit (atomik sayaç) → non-RT iş parçacığında sıklık-limitli log → kurtarma (buffer artır → fade-out/50ms/restart → ASIO→WASAPI→Null) — **tamamı ADR-017-d**; bu ADR **eklemez**, yalnız pipeline'ın xrun üretip üretmediğini **ölçmekle** yükümlüdür | ✅ ADR-017-d |

### (c) Katman sınırı — 3 katman ADR-017'nindir, bu ADR yalnız pipeline mantığı

| Katman | Sahip | Bu ADR'nin tutumu |
|--------|-------|-------------------|
| **1. XMOS/xCORE firmware** — USB↔I²S/TDM zamanlaması, donanım RT garantisi | ADR-017 §2.2a | **Yeniden karar alınmaz.** Firmware, pipeline **mantığını** (aşama sözleşmesi + sıra) aynı semantikle uygular; zamanlama/RT garantisi yine firmware'dedir |
| **2. JUCE / Neva Engine (masaüstü)** — DSP hesabını blok bazında yapar, host RT garantisi altında | ADR-017 §2.2a | **Yeniden karar alınmaz.** Bu ADR'nin zinciri burada **birincil** yürütülür; `processBlock` bloğu = 1 pipeline çağrısı |
| **3. ASIO/WASAPI host** — Windows ses I/O, buffer boyu, gerçekleşen latency | ADR-017 §2.2a | **Yeniden karar alınmaz.** Buffer seçimi burada; pipeline aşama sayısı latency bütçesine **girdi** olarak bildirilir (toplam aşama latency > bütçe → aşama kaldırma **yeni ADR**) |
| **Pipeline mantığı (aşama listesi · sıra · veri akışı · parametre yolu)** | **ADR-062 (bu dosya)** | ✅ Burada kararlaştırılır; **tek SSOT** |

**Sınır kuralı:** pipeline mantığı katmanlar arasında **aynı kalır**, yürüten **değişir**. Bir katman aşamaları değiştiremez/sıralayı değiştirirse katman ihlali değil, **ADR-062 ihlali** olur → revert + log ERROR (AGENTS §18).

### (d) Parametre değişimi — RT-güvenli güncelleme ve smoothing (ADR-025-e hizası)

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | **Taşıma yolu** | UI/kontrol iş parçacığı → **lock-free SPSC kuyruk** ya da `std::atomic` işaretçili **çift-tampon (gölge katsayı seti)** → RT iş parçacığı **blok sınırında** okur. `std::mutex` **yasak** (ADR-017-c/2) |
| 2 | **Katsayı hesabı** | Biquad katsayıları (RBJ formülü, `dsp-chain.md:220-238` ile aynı) **RT dışında** hesaplanır; RT yolunda yalnız **katsayı yazımı** vardır. Sürekli akış için gerekiyorsa blok sınırında **sabit bütçeli** yeniden hesap (sin/cos önbellekli) izinlidir |
| 3 | **Uygulama anı** | Parametre **her örnekte değil**, **blok başında** uygulanır (256-sample blok). Örnek-accurate geçiş **yalnız** yeni ADR ile |
| 4 | **Smoothing (zipper yok)** | Kazanç/Q/frekans için **one-pole (~5ms)** veya örnek-bazlı **linear ramp**; EQ bant kazançları **blok-başına yumuşatılır**; ramp durumu önceden tahsis |
| 5 | **Yapısal değişiklik** | Aşama bypass/aktifleştirme, bant ekleme/çıkarma, preset yükleme **blok sınırında** ve **RT dışında hazırlık** ile; delay/reverb durum belleği **sıfırlanmaz** (tail korunur) |
| 6 | **Sayı biçimi** | float32 işlem · DF2T · FTZ/DAZ (ADR-025-e) · katsayı çift hassasiyetinde saklanabilir ama işlem tek hassasiyette |
| 7 | **Yasaklar** | RT iş parçacığında tahsisat · kilit · dosya/log yazımı · katsayı için `std::function`/heap · parametre için `std::string` |

### 2.1 Neden Bu Seçenek?

- **Statik sıra endüstri standardı:** gömülü/pro ses motorları zinciri önceden tasarlanmış sabit bloklarla kurar, çalışma anında yalnız parametre değişir (FOH · DSP Concepts · Analog Devices) → CoreMusic'in 15 aşamalı şeması da bu modeldir; graf'e geçmek kanıtsız karmaşa üretir.
- **EQ→dynamics konvansiyonu + kullanıcı kapsamı:** kaynaklar EQ'nun kompresörden önce "daha geleneksel" olduğunu söyler (iZotope · productionadvice · macprovideo); kullanıcı onaylı kapsam da `giriş→EQ→dynamics→mix→çıkış`dır → **iki EQ da dynamics öncesinde, seri**.
- **Paralel EQ'nun bedeli ölçülebilir:** ikinci şube ek gecikme + faz etkileşimi + iki kat biquad CPU üretir ve ADR-025'in "grafik → parametrik" sıra kararını anlamsız kılar → ret.
- **RT kısıtları zaten bağlandı:** ADR-017 bunu 3 katman üzerinden yazdı; tekrar yazmak **iki SSOT** yaratır → bu ADR yalnız **stage bazlı** ekleri (sözleşme, CPU/latency bütçesi, bypass) yazar.
- **Parametre yolu literatürle birebir:** atomik/kuyruk + blok sınırı + one-pole smoothing üç bağımsız kaynakta aynı (timur.audio · truce.audio · OwnAudioSharp) → ADR-025-e'nin "callback'te tahsisat yok" maddesini **tamamlayan** tek reçete budur.
- **Kod yok:** `*.cpp/h` = **0 dosya** (§1.1) → önce **sıra + sınır + parametre yolu** yazılır, sonra kod; tersi yazılsa kod sırayı kendisi seçer ve refactor pahalılaşır.
- **Ölçüm kapı, kâğıt değer değil:** `dsp-chain.md` "Gerçek 0.08ms / 6.5%" satırları kodsuz **kanıt değildir** (ADR-005) → bu ADR hedef yazar, ölçülmüş sayıyı **bekler**.

### 2.2 Teknik Detaylar

**a) Zincir sözleşmesi (arayüz):**

```cpp
// Doküman kaynağı: dsp-chain.md:41-65 (spec IMPLEMENTED, kod PLANNED)
class IDSPStage {
public:
    virtual void process(float** input, float** output,
                         uint32_t frameCount) noexcept = 0;   // blok başına 1 çağrı
    virtual bool  setParameter(uint32_t id, float v) noexcept = 0; // blok sınırında
    virtual const char* getName() const noexcept = 0;
    virtual uint32_t    getLatency() const noexcept = 0;      // toplama eklenir
};
```

**b) Zincir veri yolu (sabit sıra — §2(a)):**

```
Giriş Gain → Kanal/Format/SR → [EQ Grafik → EQ Parametrik] → Dynamics → Efekt → Mix/Route → Master → Dither → Çıkış Gain → Format Out
                                      (her düğüm: bypass var, latency var, CPU payı var)
```

**c) Bütçe tablosu (kaynaklı, dürüst etiketli):**

| Bütçe | Hedef | Kaynak | Durum |
|-------|-------|--------|-------|
| Host buffer | 256 varsayılan, 128–512 izinli | `neva-engine-core.md:329` · ADR-017-b | ✅ doküman |
| Round-trip kapı | `<10ms ASIO / <20ms WASAPI` (ölçerek) | `.ai/CLAUDE.md:429` · ADR-006 | ✅ vault |
| Pipeline iç gecikme | `<0.1ms` (15 aşama toplamı) | `dsp-chain.md:377` | ⏳ hedef — ⚠️ ölçülmedi |
| CPU (15 aktif aşama) | `<%8` | `dsp-chain.md:379` | ⏳ hedef — ⚠️ ölçülmedi |
| Bellek | `<10MB` | `dsp-chain.md:380` | ⏳ hedef — ⚠️ ölçülmedi |
| Aşama bypass/kill-switch | her düğüm + toplam | ADR-017 §2.2f · ADR-019 | ⏳ PLANNED |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Paralel EQ** (grafik ∥ parametrik, çıkışları toplanır) | İki EQ bağımsız ayarlanır | Ek mix gecikmesi + faz etkileşimi + iki kat biquad CPU; ADR-025'in "grafik → parametrik" sıra kararı **çıkmaza** girer; headroom tahmini zorlaşır | Ret (§2(a)/1) — literatürde toplam gecikme/phase maliyeti; ADR-025 çelişkisi |
| 2 | **Tam serbest graf (DAG) — JUCE AudioProcessorGraph benzeri, çalışma anında yeniden düzenlenebilir** | Maksimum esneklik, dinamik routing | Graf değişimi RT thread güvenliği ister (JUCE forum: pops/clicks); kilit/epoch maliyeti; CoreMusic'in dinamik yeniden düzenleme ihtiyacı **kanıtlanamadı** | **Ret (şartlı saklandı)** — tetik: çok-kanallı/dinamik routing ihtiyacı doğarsa **yeni ADR** ile §3.2'ye geri dönülür |
| 3 | **Stage'leri ayrı iş parçacıklarına dağıtmak (multi-thread blok işleme)** | Çok-çekirdekli hız | Uyandırma/kilit maliyeti; non-RT OS'ta garanti yok; ADR-017 "RT yoluna yalnız ses işi" kuralını zorlar | Ret — ADR-017 §2.2e ile çelişir |
| 4 | **Pipeline'ı tamamen firmware'e (XMOS) veya tamamen host'a taşımak** | Tek sahiplik | ADR-017'nin 3 katman sınırı ihlal edilir (firmware buffer politikası yazamaz, host DSP hesaplayamaz) → katman ihlali = revert | Ret — ADR-017 §2.2a bağlayıcı |
| 5 | **Parametreyi her örnek anında uygulamak (sample-accurate)** | En hassas geçiş | Katsayı yeniden hesap her örnekte → CPU patlar; smoothing olmadan zipper noise | Ret — blok sınırı + one-pole ramp (§2(d)/3,4) |
| 6 | **Bu ADR'de ADR-017'nin buffer/RT kurallarını yeniden yazmak** | Tek dosyadan okuma | İki SSOT, ileride çelişki denetimi zor | Ret — **tekrar yasağı**: ADR-017'ye çizilir, yalnız stage ekleri yazılır |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Sıra tek yerde:** giriş→EQ→dynamics→mix→çıkış + seri iki EQ **tek karar** olarak bağlandı; yeni agent "zincir nasıl?" sorusunu tek dosyada bulur.
- **Tekrar yok:** buffer/RT/xrun kuralları ADR-017'ye, EQ içeriği ADR-025'e çizildi → üç ADR **birbirine girmez**, SSOT tek.
- **Bölüm sınırı kapanıyor:** ADR-061 §2(c)'deki "DSP boru hattı → ADR-062" satırı bu ADR ile **dolduruldu** (ADR-063/064 hâlâ ⚠️).
- **Parametre yolu standartlaşıyor:** atomik/kuyruk + blok sınırı + one-pole smoothing → zipper noise ve kilit riski tasarımdan çıkar.
- **Ölçülebilirlik:** her aşamanın `getLatency()` + CPU payı toplanabilir → kapı (ADR-006) ölçümle konur, kâğıtla değil.
- **İndeks hizası:** dosya adı `.ai/.decisions/index.md:93` slug'ı ile **birebir** (uydurma slug yok).

### 4.2 Olumsuz Sonuçlar / Maliyet

- **Kod 0 → her şey PLANNED:** `*.cpp/h` = 0; 15 aşamanın **yalnız 3'ünde** (1, 2, 5) örnek implementasyon var (`dsp-chain.md:108, 143, 169`), geri kalanı **arayüz + şema**.
- **Ölçülmüş değer yok:** `dsp-chain.md` "Gerçek" sütunları (0.08ms / 6.5% / 8MB / 1.2M) **kodsuz kanıt değildir** → hedef olarak kalır, `⚠️` işaretli.
- **EQ eşlemesi çözülmedi:** `[11] Master EQ` ↔ grafik EQ mutabakatı §5.1/3'e ertelendi → mutabakata kadar doküman ile karar arasında **açık nokta** var.
- **Statik sıra esnekliği sınırlı:** yeni bir aşama/sıra değişikliği her seferinde **yeni ADR** ister (kasıtlı maliyet).
- **Komşu ADR'ler boş:** `ADR-063`/`ADR-064` metinsiz → tasarım standartları ve platform mimarisi bu ADR'de **yazılmadı** (sınır korundu, boşluk sürdü).
- **NevaEngine belirsiz:** `log.md:98` "12 header" iddiası diskte **karşılıksız** → motor kodunun gerçek durumu ⚠️ (§4.3 risk 6).

### 4.3 Riskler

| # | Risk | Olasılık/Etki | Azaltma / Fallback |
|---|------|---------------|---------------------|
| 1 | **Xrun / underrun** — 15 aşama CPU'yu deadline'a sığdıramaz | Orta / Yüksek | Fallback: ADR-017-d (buffer bir kademe artır → fade-out/50ms/restart → ASIO→WASAPI→Null); stage bypass ile yükü kapat; telemetri ADR-013 ruhu |
| 2 | **Latency birikimi** — aşama latency'leri toplamı bütçeyi aşar (`<0.1ms` iç hedef + host buffer) | Orta / Orta | Fallback: `getLatency()` toplamı **kapı ölçümüne eklenir**; bütçe aşılırsa aşama kaldırma/özetleme **yeni ADR** ile; buffer artışı latency faturasını yazan log zorunlu |
| 3 | **RT ihlali (kilit/tahsisat)** — parametre yolunda mutex veya callback'te `new` | Orta / Yüksek | Fallback: §2(d) 7 kural + CI denetimi (ADR-017 §2.2f "RT kısıtı ihlali → fail"); ihlal tespitinde **revert + log ERROR** (AGENTS §18) |
| 4 | **Zincir sırası bozulması** — bir aşamanın taşınması/sıralamanın değişmesi gain staging ve fazı değiştirir | Orta / Yüksek | Fallback: sıra **yalnız bu ADR ile** değişir; bypass ≠ yeniden sıralama; kod revizyonunda sıra hash'i/şema testi ile korunur (PLANNED) |
| 5 | **EQ eşlemesi yanlış yazılması** — `[11] Master EQ` ≠ grafik EQ olduğu ortaya çıkarsa | Orta / Orta | Fallback: §5.1/3 mutabakatı; mutabakata kadar **iki yorum da uygulanmaz**, şema düzeltilir; tahmin **yazılmaz** |
| 6 | **Kanıtsız iddialar** — NevaEngine "12 header" (log:98), "Gerçek" metrikler, ADR-063/064 slotları | Yüksek / Orta | Fallback: tamamı `⚠️ VERIFICATION REQUIRED`; kanıt gelmeden "implement edildi" **denmez**; kod yazıldığında §5.1/4 ölçüm kapısı çalışır |
| 7 | **Numara çakışması** — kural 4 (ADR-088+) ↔ arşiv serisi 062 | Düşük / Düşük | Fallback: raporlanır, **düzeltilmez** (ADR-061 §5.1/9 ile aynı tutum) |

### 4.4 Vault Çapraz Referans

| Vault hedefi | İlişki |
|--------------|--------|
| [[ADR-017-dsp-hardware-mode]] | 3 katman + hard-RT + buffer/latency + xrun — **bu ADR'nin zorunlu girdisi, tekrar yok** |
| [[ADR-025-professional-eq-system]] | Çift EQ + sıra grafik→parametrik + RT-güvenli biquad — bu ADR yalnız **zincirdeki yerini** bağlar |
| [[ADR-061-electronics-architecture]] | L6 sınırı: "DSP boru hattı → ADR-062" satırı **bu ADR ile kapandı**; `ADR-063`/`ADR-064` hâlâ ⚠️ |
| [[ADR-038-8-1-sound-card-chip-selection]] · [[ADR-089-classab-24v]] · [[ADR-090-channel-variant-product-family]] | Donanım/ürün ailesi — pipeline'ın taşıyıcısı (çip yeniden seçilmez) |
| [[ADR-006-performance-targets]] | Kapı: `<10ms ASIO / <20ms WASAPI` — bu ADR'nin latency kapısının sahibi |
| [[ADR-019-per-os-neva-player]] · [[ADR-005-ultrathink-protocol]] · [[ADR-013-rate-limiting-apcu]] · [[ADR-024-ecosystem-modular-docs]] · [[ADR-001-vanilla-js-itcss]] | Fallback/kill-switch · kanıt standardı · sayaç/limit · wiki-link disk kuralı · web katmanı sınırı |
| [[../../index.md]] `:694` · [[../../brain.md]] `:1010` · [[../../keys.md]] `:286` · [[../index.md]] `:93` | ADR-062 kaydı — dosya bu slotu doldurdu (özet-link düzeltmesi ertelendi — §1.4) |
| `.ai/architecture/index.md:98` · `k3-ses-motoru/README.md:329` · `k3-ses-motoru/CLAUDE.md:62` | K3 ↔ ADR-062 çapraz kayıt satırları (düz metin) |
| `[[../../architecture/k3-ses-motoru/dsp-chain]]` · `[[../../architecture/k3-ses-motoru/mixer-routing]]` · `[[../../architecture/k3-ses-motoru/neva-engine-core]]` · `[[../../architecture/k3-ses-motoru/eq-parametric]]` · `[[../../architecture/k3-ses-motoru/dynamics-compressor]]` · `[[../../architecture/k3-ses-motoru/stream-buffer]]` | Pipeline spec kaynakları (18 dokümanın çekirdeği) |
| `[[../../architecture/firmware/xmos-firmware]]` · `[[../../architecture/k2-surucu/asio-drivers]]` | Katman-1 ve katman-3 spec'i (ADR-017 hizası) |
| [[../../AGENTS.md]] §24.3 · `.ai/log.md:98` · `.ai/reports/faz6-link-ledger.md:89` | NevaEngine "dizin var 0 dosya" ifadesi **eskimiş** (dizin yok) · "12 header" iddiası ⚠️ · kırık-link defteri (062 bu işlemle kapanır) |
| `ADR-063-hardware-design-standards` · `ADR-064-electronics-platform-architecture` | **Düz metin — diskte dosya YOK → wiki-link KURULMADI** (`⚠️ VERIFICATION REQUIRED`) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Durum |
|---|------|---------|-------|
| 1 | ADR-062 dosyasını `.ai/.decisions/accepted/` altına **indeks slug'ıyla** yaz (frontmatter 7 alan, debate ⏳ PENDING) | Vault Steward | ✅ tamamlandı (bu işlem) |
| 2 | `log.md`'ye **tek satır** append: `ADR-062 yazıldı (debate PENDING)` | Vault Steward | ✅ tamamlandı (bu işlem) |
| 3 | **EQ eşleme mutabakatı:** `dsp-chain.md` `[11] Master EQ` ↔ ADR-025 grafik EQ — hangisi? Şemanın §2(a) sırasıyla hizalanması (tahmin değil, karar) | Embedded + Audio | ⏳ debate öncesi |
| 4 | **Ölçüm kapısı:** `dsp-chain.md:373-381` "Gerçek" sütunlarının kod yazıldıktan sonra **ölçülmesi** (pipeline latency, CPU, bellek) → hedef ile gerçek ayrımı kapanır | Embedded (Neva Engine) | ⏳ kod yazıldıktan sonra |
| 5 | **Debate** (3 tur) → `debate` alanı güncellenir → Tech Lead ⏳ → Arch Lead ⏳ | Debate ekibi + Tech Lead | ✅ tamamlandı (3 tur / 20 persona, 18/2/0 KABUL — Tech Lead ✅ · Arch Lead ⏳ · 3 şart §5.1/11-14 · §6.3) |
| 6 | `ADR-063`/`ADR-064`'ün **kendi kanıtıyla** yazılması (her biri ayrı işlem — bu ADR sınırını korudu) | Audio HW + Embedded | ⏳ bekliyor |
| 7 | `.ai/.decisions/index.md:93`'teki çift köşeli bağlantının (hedef `../brain.md`) gerçek dosya hedefine yönlendirilmesi (dosya artık var) | Vault Steward | ⏳ **sonraki vault reset'i** (In-Place Refactoring — report-only) |
| 8 | `.ai/log.md:98` NevaEngine "12 header" iddiasının **disk kanıtıyla** doğrulanması ya da `⚠️` ile düzeltilmesi | Embedded + MO | ⏳ doğrulama |
| 9 | AGENTS §24.3 Embedded satırındaki "**dizin var, 0 dosya**" ifadesinin "`projes/NevaEngine` **dizin hiç yok**" olarak güncellenmesi | MO (vault-updater) | ⏳ vault-sync |
| 10 | **Sıra + bütçe sözleşmesi** kod iskeleti yazıldığında §2(a) sırasının ve §2(c) bütçelerin **test ile** kilitlenmesi (sıra değişimi = yeni ADR kapısı) | Embedded + QA | ⏳ kod ile |
| 11 | **[Şart 1a]** Uygulama fazında `noexcept` stage arayüzü (§2.2a) + **statik zincir iskeleti** (§2(a) 9 aşama sırası) kod olarak yazılır — iskeletsiz kod kapısı geçemez | Embedded (Neva Engine) | ⏳ debate şartı (Tur 2/1) |
| 12 | **[Şart 1b]** Kanıtsız iddialar (`log.md:98` NevaEngine 12 header · `dsp-chain.md` "Gerçek" metrikler) → `⚠️ VERIFICATION REQUIRED` + **AGENTS §24.3 düzeltme kapısı** ("dizin hiç yok" olarak güncellenir) | MO + Embedded | ⏳ debate şartı (Tur 2/2) |
| 13 | **[Şart 2]** **RT bütçesi ölçüm kapısı:** kod yazıldıktan sonra 1–5ms deadline (ADR-017 · Bencina) ve **xrun=0** koşulunda pipeline latency/CPU ölçülür; ölçüm yoksa "Gerçek" sütunu açılmaz (§5.1/4 ile kenetli) | Embedded + QA | ⏳ debate şartı (Tur 2/3) |
| 14 | **[Şart 3]** `ADR-063` / `ADR-064` **diskte dosya olarak oluşana kadar** düz metin + `⚠️ VERIFICATION REQUIRED` kalır; wiki-link yalnız dosya diskteyken kurulur | Audio HW + Embedded | ⏳ debate şartı (Tur 2/4) |

### 5.2 Geri Dönüş Planı

1. **Adım 1 geri dönüşü:** debate RED çıkarsa dosya `status: rejected` veya `superseded by` ile işaretlenir (`../../.templates/adr/adr-template.md` §6.3) — **bu metin düzenlenmez**, yeni ADR ile bağlanır; indeks satırı (93) **silinmez**.
2. **Adım 3 geri dönüşü:** EQ eşlemesi yanlış mutabakat edilirse revert + log ERROR; ADR-025 metnine **dokunulmaz** (o ayrı ADR, kendi onayıyla değişir).
3. **Adım 4 geri dönüşü:** Ölçüm hedefleri tutmazsa **ADR değişmez** — hedef satırları ölçüm sonrası revizyonu için §6.3 (limited revision + log append) kullanılır; kapı (ADR-006) **değiştirilemez**.
4. **Adım 7 geri dönüşü:** Düzeltme yanlış hedefe yapılırsa `git checkout` + son commit (AGENTS §18); `vault-utf8-writer` `.bak` yedeği (`C:/temp/opencode/vault-backups`) son çare.
5. **Adım 10 geri dönüşü:** Sıra testi kasten sıkıysa gevşetilmez — test değil, **yeni ADR** yazılır (sıra kararı ADR-062'nin tekelinde).

---

## 6. İlgili Dokümanlar

### 6.1 Çapraz Referans

| Dosya | Satır | İddia | Bu ADR ile durum |
|-------|-------|-------|------------------|
| `.ai/.decisions/index.md` | `:93` | slug = `ADR-062-dsp-pipeline-architecture` | ✅ dosya adı bu satıra hizalandı (sapma YOK) |
| `.ai/.decisions/index.md` | `:94-95` | ADR-063 / ADR-064 satırları | ⚠️ satır var, **dosya yok** → düz metin + V.R. |
| `.ai/index.md` | `:694` | ADR-062 kaydı (kök indeks) | ✅ hizalı |
| `.ai/brain.md` | `:1010` | `ADR-062 \| DSP Pipeline Architecture` | ✅ bu ADR o özeti doldurur |
| `.ai/keys.md` | `:286` | `ADR-062 \| DSP pipeline architecture \| Electronics` | ✅ hizalı |
| `.ai/architecture/index.md` | `:98` | `K3 \| Ses Motoru \| 50 \| 18 \| ADR-025, ADR-062` | ✅ K3 ↔ bu ADR bağı kuruldu |
| `.ai/architecture/k3-ses-motoru/dsp-chain.md` | `:16-39, 41-65, 68-103, 169-174, 373-381` | 15 aşama · `noexcept` arayüz · biquad · `MAX_BANDS=31` · "Gerçek" metrikler | ✅ §2(a), §2(b) kanıtı · ⚠️ metrikler ölçülmedi |
| `.ai/architecture/k3-ses-motoru/mixer-routing.md` | `:16, 34, 174` | bus mimarisi · channel strip · routing matrix | ✅ §2(a) aşama 7 |
| `.ai/AGENTS.md` | §24.3 (Embedded satırı) | "`projes/NevaEngine/*.md` ⚠️ dizin var, 0 dosya" | ⚠️ **eskimiş**: `.ai/projects` **hiç yok** → §5.1/9 |
| `.ai/log.md` | `:98` | "NevaEngine 12 header, ~79KB, `dsp_pipeline.h`" | ⚠️ **diskte karşılıksız** → §4.3 risk 6 |
| `.ai/reports/faz6-link-ledger.md` | `:89` | `ADR-061..064` kırık hedef listesi | ✅ 062 bu işlemle kapandı · 063/064 açık |
| `.ai/MEMORY.md` | `:656` | "3 yeni ADR (061-063)" | ⚠️ 061 ✅ · **062 ✅ (bu işlem)** · 063 hâlâ yok |
| `electronic/` · `.ai/projects/` | — | ADR-061 0-dosya bulgusu · AGENTS §24.3 | ✅ ikisi de **False** (yeniden doğrulandı) |

### 6.2 Bağlantılar

- Şablon: [[../../.templates/adr/adr-template.md]] (Guardrail #16) — format referansı: [[ADR-061-electronics-architecture]]
- İlgili ADR'ler: [[ADR-017-dsp-hardware-mode]] · [[ADR-025-professional-eq-system]] · [[ADR-006-performance-targets]] · [[ADR-019-per-os-neva-player]] · [[ADR-038-8-1-sound-card-chip-selection]] · [[ADR-089-classab-24v]] · [[ADR-090-channel-variant-product-family]] · [[ADR-005-ultrathink-protocol]] · [[ADR-013-rate-limiting-apcu]] · [[ADR-024-ecosystem-modular-docs]] · [[ADR-001-vanilla-js-itcss]]
- Vault kökü: [[../../index.md]] · [[../../brain.md]] · [[../../keys.md]] · [[../../MEMORY.md]] · [[../../log.md]] · [[../../AGENTS.md]] · [[../index.md]]
- Spec dokümanları: [[../../architecture/k3-ses-motoru/dsp-chain]] · [[../../architecture/k3-ses-motoru/mixer-routing]] · [[../../architecture/k3-ses-motoru/neva-engine-core]] · [[../../architecture/k3-ses-motoru/eq-parametric]] · [[../../architecture/k3-ses-motoru/dynamics-compressor]] · [[../../architecture/k3-ses-motoru/stream-buffer]] · [[../../architecture/firmware/xmos-firmware]] · [[../../architecture/k2-surucu/asio-drivers]]
- Dizin kayıtları (düz metin): `architecture/k3-ses-motoru/` (18 .md) · `architecture/firmware/` (8 .md) — **dizin hedefidir, .md dosyası değildir → wiki-link değil**
- Diskte **olmayan** (düz metin + ⚠️): `ADR-063-hardware-design-standards` · `ADR-064-electronics-platform-architecture` · `electronic/` · `.ai/projects/NevaEngine/` · `architecture/l6-electronics`

### 6.3 Debate Şartları (bağlayıcı — KABUL koşulu)

| Şart | Madde | Doğrulama kapısı | Durum |
|------|-------|------------------|-------|
| **1a** — Uygulama iskeleti | §5.1/11 · §2.2a-b | `noexcept` stage arayüzü + statik 9-aşama zincir iskeleti kodda mevcut; sıra testi (§5.1/10) yeşil | ⏳ |
| **1b** — V.R. düzeltme kapısı | §5.1/12 · §1.1 · §6.1 | `log.md:98` + `dsp-chain.md` "Gerçek" iddiaları `⚠️` işaretli **veya** kanıtlanmış; AGENTS §24.3 düzeltildi | ⏳ |
| **2** — RT bütçesi ölçüm kapısı | §5.1/13 · §5.1/4 · §2(b)/5,6 | 1–5ms deadline + **xrun=0** oturumunda ölçülen latency/CPU; ölçüm raporu `.ai/reports/` altında | ⏳ |
| **3** — ADR-063/064 doğrulama | §5.1/14 · §1.4 · §6.1 | `ADR-063*` / `ADR-064*` glob ≥ 1 dosya → wiki-link açılabilir; hâlâ 0 ise düz metin korunur | ⏳ |

> **Not:** Üç şart da kapanmadan bu ADR'nin maddeleri **PLANNED** etiketini korur (§1.4 "Kod kanıtı = 0"); şartlar yalnız uygulama/ölçüm kapılarını bağlar, §2 karar metnini **değiştirmez**.

---

## 7. Onay

### 7.1 Onay Akışı

| Rol | Kişi | Tarih | Durum |
|-----|------|-------|-------|
| Vault Steward | CoreMusic Vault Steward | 2026-09-30 | ✅ |
| Tech Lead | — | 2026-09-30 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.2 Debate

| Alan | Değer |
|------|-------|
| Debate | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** |
| Tur sayısı | 3 (tamamlandı) |
| Oy | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tech Lead | ✅ |
| Arch Lead | ⏳ |

#### Tur 1 — 20 persona, bulgu turu (17 kabul/neutral · 3 uyarı)

- **DSP kodu = 0 dosya** (`*.cpp/h/hpp/c` repo-geneli iki bağımsız tarama) → §2 maddelerinin tamamı **PLANNED** etiketli kaldı.
- **`.ai/projects/` dizini hiç yok** → AGENTS §24.3 "dizin var, 0 dosya" ifadesi eskimiş; `.ai/log.md:98` "12 header" iddiası karşılıksız ⚠️.
- `architecture/k3-ses-motoru/` = **18 .md**, `architecture/firmware/` = **8 .md**, `electronic/` = **0 dosya** → ADR-061 bulgusu doğrulandı.
- `ADR-063` / `ADR-064` **diskte YOK** → düz metin + `⚠️ VERIFICATION REQUIRED` (wiki-link kurulmadı).
- **Karar maddeleri:** seri sabit zincir (giriş→EQ→dynamics→mix→çıkış) · ADR-017 hizası (1–5ms deadline, tahsisat yasağı, `noexcept`) · ADR-025-e RT-güvenli parametre (one-pole smoothing + lock-free kuyruk) · EQ→comp **varsayılan** sıra · JUCE graf **RT-dışı** değiştirilir.
- Kaynak tabanı: **24 kaynak / 5 sorgu** (Bencina · Analog Devices · JUCE Forum ×3 · audioXpress) — §1.3.
- **`[11] Master EQ` eşlemesi kanıtlanamadı** → tahmin yazılmadı (§2(a)/5 uyarısı korundu).
- `.ai/.decisions/index.md:93` slug hizalı ✅.
- **Oylar:** 17 kabul/neutral · **3 uyarı** — QA: xrun testi yok · Critic: NevaEngine/`log.md` kanıtsız + V.R. şartı · DSP: ADR-063/064 doğrulama.

#### Tur 2 — İtiraz → çözüm

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | DSP kodu 0 → hiçbir aşama doğrulanamaz | Uygulama fazında `noexcept` arayüz + statik zincir iskeleti zorunlu | **1a** → §5.1/11 |
| 2 | NevaEngine/`log.md` kanıtsız iddialar | `⚠️ VERIFICATION REQUIRED` + AGENTS §24.3 düzeltme kapısı | **1b** → §5.1/12 |
| 3 | xrun/latency testi yok → RT bütçesi kâğıtta | RT bütçesi ölçüm kapısı: 1–5ms deadline + **xrun=0** | **2** → §5.1/13 |
| 4 | ADR-063/064 diskte yok → bağlantı kırık | Düz metin + doğrulama şartı (dosya yoksa wiki-link yok) | **3** → §5.1/14 |

#### Tur 3 — Oy

**18 kabul / 2 çekimser / 0 red → KABUL.** Üç bağlayıcı şart §5.1/11-14 ve §6.3'e işlendi; şartlar kapanmadan maddeler PLANNED kalır (§1.4).

> **Not:** `status: accepted` bu ADR'nin **kapsam kararının** (§2 a-d) vault tarafından kabul edildiğini gösterir; debate ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** ve Tech Lead ✅ olmasına rağmen **Arch Lead ⏳** olduğu için **Frozen'a geçiş YOK**tur. ADR-001–037 frozen kapsamı dışındadır. Debate RED çıkarsa §5.2/1 uygulanır (dosya `rejected` olur, indeks satırı silinmez).

---

*1.0.0 | 2026-09-30 | Created — ADR-062 DSP Pipeline Architecture (debate PENDING)*
*Authority: CoreMusic Vault Steward · Mode: Red Team · Human Mode · Truth Mode*
