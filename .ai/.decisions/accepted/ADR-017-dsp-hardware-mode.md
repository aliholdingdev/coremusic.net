---
title: "CoreMusic — ADR-017: DSP Hardware Mode (3 Katman: XMOS/xCORE Firmware · JUCE Plugin DSP · ASIO/WASAPI Host — Hard RT Kısıtları, Buffer/Latency Bütçesi, Xrun Politikası)"
type: adr
category: audio
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-017 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-017: DSP Hardware Mode (3 Katman: XMOS/xCORE Firmware · JUCE Plugin DSP · ASIO/WASAPI Host — Hard RT Kısıtları, Buffer/Latency Bütçesi, Xrun Politikası)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-017'yi sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı) · debate: **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · Tech Lead: **✅ (2026-09-24)**
**İlgili ADR'ler:** [[ADR-005-ultrathink-protocol]] (doğrulama disiplini + `⚠️ VERIFICATION REQUIRED` etiketi — bu ADR'nin kanıt standardı; dosya diskte VAR ✅) · [[ADR-013-rate-limiting-apcu]] (sayaç + limit + alert ruhu — xrun telemetrisi buradan devralınır; dosya diskte VAR ✅) · [[ADR-001-vanilla-js-itcss]] (web katmanı sınırı — tarayıcı RT yoluna girmez; dosya diskte VAR ✅) · [[ADR-006-performance-targets]] (audio latency bütçesi `<10ms ASIO / <20ms WASAPI` + `underrun = 0` `⚠️ VERIFICATION REQUIRED` hedefi; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 54** `[[ADR-017-dsp-hardware-mode]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in ses yolu **üç ayrı teknoloji katmanına** yayılır: donanım zamanlamasını yapan gömülü firmware (XMOS XU316), DSP hesabını yapan uygulama/plugin kodu (JUCE / C++20 Neva Engine) ve Windows ses giriş-çıkışını taşıyan sürücü-katmanı (ASIO/WASAPI). Bu üç katmanın **sorumluluk sınırı, gerçek zamanlı (RT) garantisinin kime ait olduğu, veri yolu ve xrun üretecinin sahibi** hiçbir tek belgede yazılmamıştır; kurallar brain.md §7.1, `.ai/.templates/adr/adr-audio-template.md` ve prompt-maker referanslarına **dağılmıştır**. Bu ADR; üç katmanı **tek bir karar altında karşılaştırır ve sınır çizer**, **hard-RT kısıtlarını** (buffer/latency bütçesi, callback tahsisatı yasağı, bloklayıcı I/O yasağı, xrun politikası, öncelik sıralaması) bağlayıcı hale getirir ve vault'taki `⚠️ VERIFICATION REQUIRED` işaretli `underrun = 0` hedefini ADR-006 ile aynı dürüst konumda tutar.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) SPEC/VAULT KATMANI — IMPLEMENTED (doküman var, kod yok):**

- **XMOS/xCORE firmware spesifikasyonu — `.ai/architecture/firmware/` (IMPLEMENTED doküman):**
  - `xmos-firmware.md:12` — XU316 **8-core**, XC, "deterministic timing ve low-latency audio processing"; **satır 50** `i2s_driver.xc` ağacı; **satır 66** `lib_i2s/`; **satır 91-92** `chan c_usb_to_i2s` / `chan c_i2s_to_usb` (kanal tabanlı veri yolu).
  - Aynı dizinde `i2s-driver.md`, `dsp-firmware.md`, `usb-audio-firmware.md`, `bootloader.md`, `gpio-control.md`, `mcu-support.md` mevcut (6 dosya + index).
- **JUCE/Neva Engine DSP spesifikasyonu — `.ai/architecture/k3-ses-motoru/` (IMPLEMENTED doküman):**
  - `neva-engine-core.md:12` — "C++20 … **real-time safe** ve **lock-free**"; **satır 177** "Real-time güvenli buffer pool"; **satır 329** `config.bufferSize = 256`.
  - `dsp-chain.md:12` — **15 aşamalı** pipeline, "biquad katsayıları, ring buffer'lar ve lock-free yapılar"; **satır 50-56** stage arayüzü `virtual void process(...) noexcept` / `getParameter(...) const noexcept`.
- **ASIO/WASAPI host spesifikasyonu — `.ai/architecture/k2-surucu/` (IMPLEMENTED doküman):**
  - `asio-drivers.md:12` — "ASIO Exclusive mode ile **0.5ms round-trip** latency hedefler"; **satır 21** buffer "32 sample'a kadar düşürülebilir"; **satır 35** Exclusive `< 0.5ms` ↔ Shared `2-10ms`; **satır 41-45** buffer yönetimi + double buffering.
  - `wasapi-exclusive.md:12, 33-38, 51` — Exclusive/Shared ayrımı, `Initialize()` exclusive mod.
  - `buffer-management.md`, `latency-optimization.md`, `alsa-native.md`, `core-audio-macos.md` aynı dizinde (12 dosya).
- **Hard-RT kural metinleri — `.ai/brain.md` (IMPLEMENTED):**
  - **§7.1 satır 378-380** — callback içinde **yasak**: `malloc()`, `free()`, `new`, `delete`, `std::make_shared`, `std::vector::push_back`, bloklayıcı I/O, `throw`; **izinli**: stack tahsisi, `std::atomic`, SIMD, `constexpr`, member, `alignas(64)`.
  - **§7.2 satır 382-396** — `processAudioBlock(...) noexcept` ASIO callback örneği (EQ → compressor → limiter).
  - **satır 286** — "ASIO Buffer: **512 sample** varsayılan (64-1024), 48kHz, 32-bit float, **~10.67ms**".
  - **satır 862** — Edge case: `ASIO Device Loss → WASAPI fallback → Null Output`.
  - **satır 869** — Edge case: `Buffer Underrun (CPU %100) → Fade-out → 50ms sessizlik → restart`.
- **Bütçe hedefi — `.ai/CLAUDE.md:429`** — "Latency Hedefi | **<10ms (ASIO), <20ms (WASAPI)**".
- **Agent/edge-case bağları — `.ai/AGENTS.md`** — **satır 169** "ASIO device loss → WASAPI fallback"; **satır 348** eskalasyon "ASIO cihaz kaybı L1(Embedded) → L2, 30s"; **satır 683** `§17 Edge | [[ADR-017-dsp-hardware-mode]] | ASIO/WASAPI` (**bu dosya ile wiki-link canlanır**).
- **Dizin kaydı — `.ai/.decisions/index.md:54`** `[[ADR-017-dsp-hardware-mode]] | DSP Hardware Mode (XMOS, JUCE, ASIO) | Audio` ✅ slug eşleşmesi. Ek referanslar: `.ai/keys.md:117, 140, 252` · `.ai/index.md:634` · `.ai/glossary.md:678` · `.ai/brain.md:972, 1037` · `.ai/.templates/adr/adr-index.md:88, 133`.
- **Kırık-link kanıtı (bu dosya kapatır):** `.ai/broken-links-report.md:49` ve `.ai/reports/broken-files-report.md:182` — `[[ADR-017-dsp-hardware-mode]]` diskte YOK → bu yazım ile **kapanır**.

**B) KOD KATMANI — YOK → PLANNED (uydurulmadı):**

- **Dizin taraması:** kökte `xmos/`, `plugin/`, `firmware/`, `dsp/`, `juce/`, `audio/`, `engine/`, `neva/` **hiçbiri yok** (`Test-Path` = false, 8/8). Kök altındaki tek kod dizini `shared/` (**PHP**) + 3 subdomain.
- **Kaynak dosya taraması:** `*.cpp`, `*.h`, `*.hpp`, `*.c`, `*.cc`, `*.xc` → **repo genelinde 0 dosya** (vendor/node_modules hariç) → Neva Engine, JUCE plugin, ASIO/WASAPI sürücü kodu ve XMOS firmware'i **henüz yazılmamıştır**.
- **`.ai/projects/` ve `.ai/electronic/` dizinleri YOK:** `.ai/keys.md:122` `VST3, plugin, MIDI → projects/NevaEngine/vst3-hosting` hedefi diskte yok; `.ai/AGENTS.md` §24.3 "`.ai/projects/NevaEngine/*.md` ⚠️ VERIFICATION REQUIRED (dizin var, 0 dosya)" ifadesi de **çelişkili** (dizin kendisi yok) → `⚠️ VERIFICATION REQUIRED`.
- **Neva Engine "implement edildi" iddiası = ÇELİŞKİ:** `.ai/log.md:98` (2026-09-18) "NevaEngine C++20 JUCE/ASIO … **12 header files, ~79KB** … `core/neva_engine.h`, `buffer/lock_free_ring_buffer.h`, `driver/asio_driver.h`, `driver/wasapi_driver.h`" yazar; `neva_engine*` dosya araması **0 sonuç** → iddia disk kanıtıyla desteklenmiyor, **`⚠️ VERIFICATION REQUIRED`** (ADR-005). Bu ADR kod varmış gibi yazılmaz.
- **VST3/AU plugin kaynağı yok:** yalnız `.ai/ecosystem/ses-dsp-acik-kaynak.md:119, 258, 385` `processBlock` deseni spesifikasyonu (PLANNED).
- **Test/CI:** `shared/tests/` altında ASIO/JUCE/DSP/callback ile ilgili test **0**; `.github/workflows/` **0 dosya** (`.ai/AGENTS.md` §25.2) → RT bütçesi CI'da denetlenmiyor.
- **Format/şablon kanıtları:** `.ai/.templates/adr/adr-template.md` (Guardrail #16, 7 bölüm + §1.3 9 alan) VAR ✅ · `.ai/.templates/adr/adr-audio-template.md` VAR ✅ · format referansı `.ai/.decisions/accepted/ADR-016-url-normalization.md` VAR ✅ · `.claude/skills/prompt-maker/references/10-web-research-protocol.md` VAR ✅.

**Sonuç etiketi:** bu ADR **spec/yön veren** bir karardır — **vault dokümanları IMPLEMENTED, üç katmanın da kodu PLANNED**.

### 1.2 Sorun Tanımı

1. **Sınır çizgisi yok:** firmware, plugin ve host katmanı kimin işi? Kim RT garantisi verecek? Kim xrun üretir, kim kurtarır? — hiçbir belgede yazılı değil → her katman her şeyi yapmaya kalkar (katman ihlali = `.ai/AGENTS.md` §5/§18 revert kuralı).
2. **Hard-RT kuralları parça parça:** yasak liste brain.md §7.1, callback deseni `adr-audio-template.md`, `malloc`/`lock` yasağı prompt-maker `06-deep-domain-rules.md:82, 87`, xrun sayacı `c-template.md:244` — **tek bağlayıcı karar yok**.
3. **Buffer/latency hedefleri dağınık ve hiyerarşisiz:** `<10ms/<20ms` (`.ai/CLAUDE.md:429`) · `<0.5ms` (`asio-drivers.md:12, 35`) · `512 sample ≈ 10.67ms` (`brain.md:286`) · `bufferSize = 256` (`neva-engine-core.md:329`) — hangisi **bütçe**, hangisi **tasarım hedefi** yazılı değil (ADR-006 §1.1 aynı bulguyu raporlamıştı).
4. **Xrun/underrun toleransı vault'ta tescilli değil:** ADR-006 bu hedefi `⚠️ VERIFICATION REQUIRED` ile bıraktı; brain.md:869 yalnız **kurtarma** veriyor (fade-out → 50ms → restart), **tolerans/limit/sayaç sahibi** yok.
5. **Kod-spec boşluğu:** üç katman için de tek satır C/C++ yok (§1.1-B); `.ai/log.md:98` "12 header" iddiası dosyalarla çelişiyor.
6. **Kırık bağlantı ağı:** `[[ADR-017-dsp-hardware-mode]]` en az 12 vault dosyasında referanslanıyor, dosya yok → `.ai/broken-links-report.md:49` kırmızı.
7. **Numara istisnası:** "yeni ADR ≥ 088" kuralına rağmen `ADR-017` `.ai/.decisions/index.md:54`'te **çoktan rezerve edilmiş boş slottur** (ADR-016 aynı istisnayı kaydetmişti) → numara üretilmez, rezervasyon doldurulur.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (xmos.com, learn.microsoft.com, source.android.com, docs.redhat.com, lwn.net), her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) XMOS/xCORE real-time mimarisi, (b) JUCE audio plugin best practices 2025-26, (c) ASIO vs WASAPI latency, (d) buffer underrun/xrun politikaları, (e) real-time audio thread safety (tahsisat/kilit/öncelik).** Erişim: **6 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "XMOS xCORE real-time audio firmware architecture deterministic timing I2S TDM channels xtcp 2025" · (2) "JUCE audio plugin best practices 2025 real-time audio thread no allocations processBlock AudioProcessorValueTreeState" · (3) "ASIO vs WASAPI exclusive latency comparison buffer size ms Windows audio 2025" · (4) "real-time audio thread priority scheduling SCHED_FIFO xrun underrun recovery policy glitch detection" · (5) "audio buffer underrun xrun handling policy DAW increase buffer size silent recovery log professional audio" · (6) "real-time audio thread safety rules no memory allocation no locks noexcept denormal NaN guard audio callback" |
| Web Search **Konusu** | (1) xcore.ai I²S/TDM donanım + `lib_i2s` master/slave + çok çekirdekli deterministik zamanlama; (2) JUCE `processBlock` real-time safety, APVTS atomik/güvenli parametre güncellemesi, tahsisat yasakları; (3) ASIO Exclusive vs WASAPI Exclusive/Shared gerçek ölçümler + Microsoft low-latency API dokümanı; (4) SCHED_FIFO 1-99 öncelik aralığı, underrun/overrun tanımı, RT kernel kuyruk sorunları, realtime throttling; (5) DAW/xrun kurtarma akışı (buffer artırma, dropout bildirimi, JACK xrun tanımı); (6) callback'te tahsisat/kilit yasağının gerekçesi (allocator lock, sistem çağrısı) + atomik/wait-free alternatif |
| Web Search **Bağlam** | **~22 adlandırılmış kaynak / 6 sorgu**: xmos.com (xcore.ai I/O, lib_i2s, multichannel audio board manual), xcore.com forum (XU316 USB audio firmware), astutegroup (xCORE determinism), JUCE Forum ×4 (audio thread lock, APVTS realtime safety, Windows WASAPI latency, real-time multi-threading), melatonin.dev (JUCE tips), lobehub juce-best-practices, skills.rest realtime-audio, soundlatencytest.com (WASAPI/ASIO/Core Audio), GitHub FlexASIO #153, learn.microsoft.com (low-latency audio), source.android.com (audio latency contributors), docs.redhat.com (RT kernel scheduling), lwn.net (SCHED_FIFO throttling), oneuptime/techveda (SCHED_FIFO yapılandırma), rossbencina.com (real-time programming 101), timur.audio (locks safely), StackOverflow (block or not), discourse.ardour.org (x-run tanımı), discuss.cakewalk.com (dropout = buffer underrun), pcaudiolabs (dropout önleme). |
| Web Search **Kısa Açıklama** | **(1) xCORE donanım zamanlaması:** XMOS, xcore.ai I/O sayfasında TDM/PCM/PDM, I²S, S/PDIF için "**high speed, deterministic and low-latency**" arayüzler der; `lib_i2s` I²S **master ve slave** + TDM çok-kanal (surround) destekler (kaynak 1, 2); multichannel audio board manual'de I²S/TDM seçimi **jumper** ile yapılır (kaynak 3) → firmware katmanı fiziksel zamanlama sahibidir. **(2) JUCE tarafı:** forum ve rehberler `processBlock`'in **real-time thread** olduğunu, APVTS `parameterChanged`'inin ses iş parçacığında çalışabileceğini ve UI/günlük kodun ses iş parçacığına kilit getiremeyeceğini yazar (kaynak 6, 7); Melatonin "çizim ile ses iş parçacığına aynı muameleyi yap, `paint`/callback'te tahsisat yok" der (kaynak 8); skills.rest "buffer'ları **audio thread başlamadan önce** ayır, paylaşım **lock-free queue** ile olsun" (kaynak 10). **(3) ASIO vs WASAPI:** ses-latency karşılaştırması WASAPI Exclusive ≈ **10ms**, ASIO **sub-5ms** (kaynak 11); FlexASIO issue #153'te WASAPI Exclusive gerçek ölçümü ASIO'ya yakın ama **talep edilen buffer ≠ gerçekleşen gecikme** (kaynak 12, 14); Microsoft low-latency audio dokümanı AudioGraph/WASAPI ile desteklenen buffer boyutlarının **sorgulanmasını** ister (kaynak 13). **(4) Öncelik:** Android ses gecikmesi dokümanı SCHED_FIFO önceliklerinin **1-99** olduğunu ve yetersiz önceliğin **underrun/overrun** ürettiğini yazar (kaynak 15); Red Hat RT kernel rehberi yüksek öncelikli CPU-hog'un kesme iş parçacıklarını bile engelleyebileceğini, LWN **rt throttling**'in kilitlenmeyi engellemek için var olduğunu hatırlatır (kaynak 16, 17). **(5) Xrun politikası:** Ardour forumu x-run'ın buffer over/underrun'u olduğunu, Cakewalk "Audio Engine Dropout = buffer underrun" bildirimi verir (kaynak 21, 22); PCAudioLabs düşüşlerin çözümünü buffer boyunu artırmak ve CPU/bus yükünü azaltmakta bulur (kaynak 23). **(6) Thread safety:** Ross Bencina "callback'te tahsisat yapma — allocator kilidi taşıyabilir" der (kaynak 18); Timur Doumler ve StackOverflow tartışması **mutex'in gerçek-zamanlı iş parçacığında uygun olmadığını**, alternatifin atomik/wait-free paylaşım olduğunu söyler (kaynak 19, 20). |
| Web Search **Uzun Açıklama** | **(a) Üç katmanın sorumluluğu kaynaklarla örtüşüyor:** firmware katmanı I²S/TDM master/slave ve **fiziksel kare-zamanlamasını** tutar (`lib_i2s`, xcore.ai I/O — kaynak 1, 2, 3); host katmanı Windows ses yığınını bypass eder ve **gerçekleşen** gecikmeyi belirler (ASIO Exclusive sub-5ms, WASAPI Exclusive ≈10ms — kaynak 11, 12, 13, 14); plugin katmanı ise yalnız **hesap** yapar ve tahsisat/kilit üretmez (kaynak 6, 7, 8, 9, 10). Yani "RT garantisi kimde?" sorusunun cevabı literatürde net: **zamanlama sahibi firmware + sürücü; plugin yalnız hesaplar, garanti vermez.** **(b) Buffer bütçesi hesapla tutarlı:** 48kHz'te 128/256/512 sample ≈ 2.67/5.33/10.67ms (ADR-006 §1.3 kaynak 14 ile aynı taban) — bu ADR'nin 128/256 önerisi bu matematikten gelir; `talep edilen buffer ≠ gerçekleşen gecikme` gerçeği (kaynak 12, 14) nedeniyle **ölçüm kapı** olarak yazılır, kâğıt değer olarak değil. **(c) Xrun politikası literatürde üç adımdır:** tespit (underrun/overrun sayacı — kaynak 15, 21), bildirim (dropout log'u — kaynak 22), kurtarma (buffer'ı artırma / yükü azaltma — kaynak 23). Sessizce geçmek veya çökme üretmek kabul edilmez; **kurtarma sırasında ses kesilmez, fade-out uygulanır** (CoreMusic ruhu: `brain.md:869`). **(d) Öncelik + deterministik gecikme:** SCHED_FIFO 1-99 (kaynak 15) ve Windows tarafında süreklilik (MMCSS benzeri yüksek öncelik — FlexASIO tartışmasında otomatik öncelik atamanın avantajı sayılır, kaynak 12) kullanılır; rt throttling/rtkit gibi mekanizmaların **başarısızlıkta sessiz normal önceliğe düşme** riski vardır (kaynak 17, Reddit/rnoise örneği) → bu da **ölçüm + log** ile yakalanır. **(e) Tahsisat/kilit yasağı gerekçeli:** allocator kilitli olabilir, sistem çağrıları kilit/tahsisat/swap tetikleyebilir (kaynak 18, 19, 20) → yasağın istisnası "gerekirse kilitle" değil, **paylaşımı atomik/wait-free yap** (kaynak 10, Jatin Chowdhury wait-free yazısı). **İtiraz olasılığı:** "bazı ekipler ses iş parçacığında tahsisat yapar" (audio.dev sunumu) — bu ADR'de **varsayılan yasak**, istisna yalnız ölçümüyle birlikte Tech Lead onayıyla açılabilir (§4.3 risk 6). |
| Web Search **Paragraf Veri Uzun** | xcore.ai I/O: TDM/PCM/PDM + I²S deterministik düşük gecikme (kaynak 1) · `lib_i2s` master/slave + TDM çok-kanal (kaynak 2) · jumper ile I²S/TDM seçimi (kaynak 3) · XU316 USB audio firmware sürümleri xcore.com forumda (kaynak 4) · xCORE çok-çekirdek deterministik (kaynak 5) · JUCE: `processBlock` = gerçek zamanlı thread; APVTS `parameterChanged` ses iş parçacığında çalışabilir (kaynak 6, 7) · çizim/callback'te tahsisat yok (kaynak 8) · real-time rehberi: tahsisat önceden, paylaşım lock-free (kaynak 9, 10) · WASAPI Exclusive ≈10ms, ASIO sub-5ms (kaynak 11) · talep edilen buffer ≠ gerçekleşen gecikme (kaynak 12, 14) · Microsoft low-latency: desteklenen buffer boyutunu sorgula (kaynak 13) · SCHED_FIFO 1-99, yetersiz öncelik → underrun/overrun (kaynak 15) · RT kernel'de yüksek öncelikli hog kesmeleri engeller (kaynak 16) · rt throttling kilitlenme koruması (kaynak 17) · callback'te tahsisat yasağı: allocator lock (kaynak 18) · mutex gerçek-zamanlı iş parçacığında uygun değil (kaynak 19, 20) · x-run = buffer over/underrun (kaynak 21) · Cakewalk dropout = buffer underrun (kaynak 22) · dropout çözümü: buffer artır + CPU/bus yükünü azalt (kaynak 23). **Sonuç: katman sınırı + 128/256 buffer önerisi + xrun 3 adım + atomik paylaşım tek kararda toplanır.** |
| Web Search **Sonucu** | 1) **Katman sınırı doğrulandı** (kaynak 1, 2, 3, 11, 13) → zamanlama = firmware + sürücü; hesap = plugin. 2) **Plugin RT kuralları doğrulandı** (kaynak 6, 7, 8, 9, 10, 18, 19, 20) → tahsisat/kilit yasağı brain.md §7.1 ile birebir; **≥3 bağımsız çapraz kaynak**. 3) **ASIO/WASAPI latency farkı doğrulandı** (kaynak 11, 12, 13, 14) → `<10ms ASIO / <20ms WASAPI` bütçesi (`.ai/CLAUDE.md:429`) literatürle tutarlı; `<0.5ms` hedefi **k2 tasarım hedefi** olarak ayrılır (ADR-006 ile aynı ayrım). 4) **Xrun politikası doğrulandı** (kaynak 15, 21, 22, 23) → tespit + log + kurtarma; **sessiz geçiş yok**. 5) **Öncelik/determinizm doğrulandı** (kaynak 15, 16, 17) → RT önceliği verilir ama **ölçüm olmadan güvenilmez** (sessiz düşme riski). 6) **Buffer ≠ gerçek gecikme** (kaynak 12, 14) → kapı **ölçülen round-trip** ile konur. 7) **`underrun = 0` vault'ta hâlâ tescilli değil** → ADR-006'daki `⚠️ VERIFICATION REQUIRED` **korunur**, bu ADR'de kesinleştirilmez. **Toplam ~22 adlandırılmış kaynak, 6 sorgu**; her ana iddia ≥2 çapraz kaynakla karşılanır; derin sayfa-içi tur yapılmadığı için sayfa-içi sayısal iddialar başlık/özet düzeyindedir (açıkça işaretli). |
| Web Search **Alınan Karar** | **ADR-017 KABUL EDİLİR — ÜÇ KATMAN TEK ADR'DE, SINIR ÇİZGİLİ + HARD-RT KISITLARI BAĞLAYICI:** **(A) Katman 1 — XMOS/xCORE firmware (hard RT, donanım):** USB UAC2 ↔ I²S/TDM zamanlaması, `lib_i2s` master/slave, kanal tabanlı XC veri yolu; **RT garantisi burada** (deterministik çekirdek planı). **(B) Katman 2 — JUCE plugin / Neva Engine DSP (host-bağımlı RT):** 15 aşamalı zincir, 32-bit float, `processBlock(...) noexcept`; **RT garantisi HOST'tadır** — plugin yalnız hesaplar, tahsisat/kilit/lock yasak. **(C) Katman 3 — ASIO/WASAPI host katmanı (Windows I/O):** ASIO Exclusive birincil, WASAPI Exclusive/Shared ikincil, buffer/latency yönetimi ve cihaz keşfi; **gerçekleşen gecikme** bu katmanın sorumluluğudur. **(D) Buffer/latency bütçesi:** varsayılan **256 sample @48kHz ≈ 5.33ms** (tasarım), **azami bütçe `<10ms` ASIO / `<20ms` WASAPI** (`.ai/CLAUDE.md:429` korunur), k2 `<0.5ms` **tasarım hedefi** olarak ayrılır; kapı **ölçülen round-trip** ile konur (talep ≠ gerçek). **(E) Hard-RT yasakları:** callback'te `new/malloc/free/delete/throw`, mutex/kilit, disk-DB-ağ I/O, loglama, catch — **hepsi yasak**; paylaşılan durum `std::atomic` / lock-free ring. **(F) Xrun politikası:** tespit (atomik sayaç) → non-RT iş parçacığında **sıklık-limitli log** (ADR-013 ruhu) → kurtarma (buffer artır → CPU yükü azalt → fade-out/50ms → restart) + **ASIO device loss → WASAPI → Null Output** zinciri; sayaç ve limit telemetrisi ADR-013'e bağlanır. **(G) Öncelik:** RT iş parçacığı en yüksek uygulama önceliğinde (Windows: süreklilik/AVRT benzeri; Linux: SCHED_FIFO 1-99) — **öncelik verilmezse ölçüm geçersiz**. **(H) Kapsam:** web/UI katmanı (ADR-001) RT yoluna **dokunmaz**; tüm sayısal hedefler `⚠️ VERIFICATION REQUIRED` disipliniyle ADR-005'e bağlanır. |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: xCORE donanım zamanlaması (3 kaynak), JUCE real-time safety (5 kaynak), ASIO/WASAPI latency (4 kaynak), RT öncelik/kuyruk (3 kaynak), xrun kurtarma (3 kaynak), tahsisat/kilit yasağı (5 kaynak) → **~22 adlandırılmış kaynak, 6 sorgu**; çapraz doğrulama ≥2 kaynak tüm ana iddialarda karşılanır. Kod tarafı aynı resmi verdi: **spec bol, kod sıfır** (§1.1-B: 0 `.cpp/.h/.xc`, 8 hedef dizinin 8'i de yok, `.ai/log.md:98` çelişkisi) → bu ADR **kod sözü değil, sınır ve kısıt kararı**dır; uygulama §5.1 adımlarına bağlanır, `underrun = 0` hedefi ADR-006'daki `⚠️ VERIFICATION REQUIRED` konumuyla **korunur** (uydurulmadı). **Kaynak listesi (~22):** 1) xmos.com — xcore.ai for I/O (deterministic TDM/I²S) · 2) xmos.com — lib_i2s (master/slave, TDM) · 3) xmos.com — xcore.ai Multichannel Audio Board 1v1 Hardware Manual (I²S/TDM jumper) · 4) xcore.com — XMOS schedule for xCORE-AI USB Audio apps · 5) astutegroup — xCORE multicore deterministic architecture · 6) JUCE Forum — Understanding Lock in Audio Thread · 7) JUCE Forum — APVTS Updates & Thread/Realtime Safety · 8) melatonin.dev — The big list of JUCE tips and tricks · 9) lobehub — juce-best-practices (realtime safety) · 10) skills.rest — realtime-audio (lock-free, pre-allocated buffers) · 11) soundlatencytest.com — WASAPI vs ASIO vs Core Audio · 12) GitHub FlexASIO issue #153 — real-world low-latency testing · 13) learn.microsoft.com — Low Latency Audio (Windows drivers) · 14) JUCE Forum — JUCE and Latency on Windows; WASAPI · 15) source.android.com — Contributors to audio latency (SCHED_FIFO 1-99) · 16) docs.redhat.com — Scheduling problems on the real-time kernel · 17) lwn.net — SCHED_FIFO and realtime throttling · 18) rossbencina.com — Real-time audio programming 101 · 19) timur.audio — Using locks in real-time audio processing, safely · 20) StackOverflow — Multithreaded realtime audio: to block or not to block · 21) discourse.ardour.org — x-runs (buffer over/underrun) · 22) discuss.cakewalk.com — Audio Engine Dropout = buffer underrun (+ pcaudiolabs.com — dropout önleme). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-005 (doğrulama + `⚠️ VERIFICATION REQUIRED`) | Kod kanıtı olmayan her iddia etiketli kalır; `.ai/log.md:98` "12 header" çelişkisi **uydurulmaz**, işaretlenir; sayısal hedefler kaynaksız yazılmaz |
| ADR-013 (sayaç + limit + alert) | xrun/underrun sayacı atomik üretilir; log **sıklık-limitli** (log taşkını yok); limit aşımı telemetriye sayılır |
| ADR-001 (mimari — Vanilla JS/ITCSS web katmanı) | Web/UI katmanı RT yoluna girmez; JS audio (`Web Audio API`) bu ADR'nin katmanı değildir, ayrı karar ister |
| ADR-006 (performans hedefleri) | `<10ms ASIO / <20ms WASAPI` bütçesi birebir korunur; `underrun = 0` hedefinin `⚠️ VERIFICATION REQUIRED` etiketi bu ADR ile **kalkmaz** (kalkış koşulu: vault'a tescil — ADR-006 §5.1) |
| In-Place Refactoring | Dosya adları (`.ai/architecture/firmware/*.md`, `k2-surucu/*.md`, `k3-ses-motoru/*.md`, `brain.md`) **onaysız değiştirilemez**; bu ADR yalnız karar yazar |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`.ai/AGENTS.md` §25.3 kural 2) |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |
| REDACTED | Firmware/sürücü yapılandırma, cihaz anahtarı, lisans, API anahtarı hiçbir koşulda bu ADR'ye yazılmaz |
| Numara kuralı istisnası | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-017` `.ai/.decisions/index.md:54`'te rezerve boş slottur (doldurma, yeni numara tahsisi değil) |

---

## 2. Karar (Decision)

**CoreMusic'in ses yolu üç katmana ayrılır ve sınırı bağlayıcıdır: (1) XMOS/xCORE firmware donanım zamanlamasının ve hard-RT garantisinin sahibidir, (2) JUCE/Neva Engine plugin katmanı yalnız hesap yapar ve host'un RT garantisi altında çalışır, (3) ASIO/WASAPI host katmanı Windows ses I/O'sunu, buffer ve gerçekleşen latency'yi yönetir. Hard-RT kısıtları (buffer bütçesi, callback tahsisat/kilit/I/O yasağı, xrun tespit-log-kurtarma politikası, RT öncelik sıralaması) üç katman için de tek karar olarak bağlayıcıdır.**

### 2.1 Neden Bu Seçenek?

- **Zamanlama sahibi ayrı, hesap ayrı:** literatür firmware/sürücünün deterministik zamanlamayı, plugin'in ise yalnız hesap yaptığını söyler (§1.3 kaynak 1, 2, 3, 11, 13) — sınırı çizmek, "kim garanti veriyor" sorusunu tek seferde kapatır.
- **Kod yok, spec var:** üç katmanda da tek satır C/C++ yok (§1.1-B) → önce **sınır + kısıt** yazılır, sonra kod yazılır; tersi yazsa kod, sonra sınır çizse kırık refactor olur.
- **Dağınık kurallar birleşiyor:** brain.md §7.1 (yasak), `asio-drivers.md` (0.5ms), `.ai/CLAUDE.md:429` (bütçe), `brain.md:862/869` (fallback) tek yerde toplanınca denetim tek noktadan yapılır (ADR-005 ruhu).
- **Xrun = olay, sessiz geçiş yok:** tespit + log + kurtarma üçlüsü ADR-013'ün sayaç/limit ruhuyla birebir örtüşür; "sessizce devam et" ses patlaması üretir ve görünmez kalır.
- **Ölçüm kapı, kâğıt değer değil:** talep edilen buffer ≠ gerçekleşen gecikme (§1.3 kaynak 12, 14) → hedef yazılır ama **kapı ölçümle** konur.

### 2.2 Teknik Detaylar

**a) Üç katman — sınır tablosu (bağlayıcı):**

| Katman | Sorumluluk sınırı | RT garantisi kimde | Arayüz / veri yolu | Besleyen kod (durum) | Xrun sahibi |
|--------|-------------------|--------------------|--------------------|----------------------|-------------|
| **1. XMOS/xCORE firmware** (K1/firmware) | USB UAC2 ↔ I²S/TDM zamanlaması, clock/master-slave, kanal eşlemesi, DSP çekirdeği | **Burada (hard RT)** — deterministik çekirdek planı, donanım kare-zamanlaması | `lib_i2s` (master/slave, TDM), XC `chan` (USB↔I²S), DMA descriptor | `.ai/architecture/firmware/*.md` (spec **IMPLEMENTED**, satır kanıtları §1.1) · `firmware/*.xc` → **YOK = PLANNED** | Firmware: DMA/USB kuyruk taşması (girdi düşüşü) |
| **2. JUCE plugin / Neva Engine DSP** (K3) | DSP zinciri (EQ, dinamik, crossover, surround), parametre işleme, mix | **Host'ta** — plugin kendi garantisini vermez; host callback çağırır | `processBlock(AudioBuffer<float>&) noexcept`, 32-bit float, lock-free ring + atomik parametre | `k3-ses-motoru/*.md` (spec **IMPLEMENTED**, `dsp-chain.md:12, 50-56`) · `NevaEngine/**.cpp` → **YOK = PLANNED** | Plugin **xrun üretmez**, yalnız **ölçer** (sayaç okur) |
| **3. ASIO/WASAPI host katmanı** (K2) | Windows ses I/O, cihaz keşfi/kaybı, buffer boyu, gerçekleşen latency, exclusive/shared mod | **Sürücü + OS** — ASIO Exclusive / WASAPI event-driven zamanlar | ASIO SDK 2.3.4 (`bufferSwitch`, `asioDriver`), WASAPI `IAudioClient` event loop | `k2-surucu/*.md` (spec **IMPLEMENTED**, `asio-drivers.md:12, 21, 35, 41-45`) · `driver/asio_driver.h`, `wasapi_driver.h` → **YOK = PLANNED** | Host: **underrun/overrun** (callback geç kaldı / donanım verisi yetmedi) |

**Sınır kuralı:** hiçbir katman diğerinin işini üstlenemez — firmware buffer politikası yazamaz, plugin cihaz keşfi yapamaz, host DSP hesaplayamaz. Sınır ihlali = katman ihlali (`.ai/AGENTS.md` §5/§18 → revert + log ERROR).

**b) Buffer / latency bütçesi (hesaplanmış, kaynaklı):**

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Örnekleme | 48 kHz, 32-bit float | `brain.md:286`, `dsp-chain.md` |
| Önerilen varsayılan buffer | **256 sample ≈ 5.33 ms** (tasarım) | `neva-engine-core.md:329` (`bufferSize = 256`) + §1.3 hesap |
| İzinli aralık (host) | **128 (≈2.67 ms) – 512 (≈10.67 ms)**; 64'e kadar yalnız ölçüm modu | `brain.md:286` (512 varsayılan, 64-1024) + §1.3 kaynak 11 |
| **Azami bütçe (kapı)** | **<10 ms ASIO / <20 ms WASAPI** — ölçerek | `.ai/CLAUDE.md:429` + ADR-006 (birebir korunur) |
| k2 tasarım hedefi (ayrı klasör) | <0.5 ms round-trip (ASIO Exclusive), 32 sample'a kadar | `asio-drivers.md:12, 21, 35` — **hedef ≠ bütçe** |
| Round-trip ölçümü | Kapı = **ölçülen** round-trip, talep edilen buffer değil | §1.3 kaynak 12, 14 |

**c) Hard-RT kısıtları — callback yolunda (üç katman için bağlayıcı):**

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | **Tahsisat yasağı** | `new`, `delete`, `malloc`, `free`, `std::make_shared`, `std::vector::push_back`, `std::string` genişlemesi, `throw` → **yasak** (brain.md §7.1; §1.3 kaynak 18) |
| 2 | **Kilit yasağı** | `std::mutex`, `lock_guard`, `shared_mutex`, kuyrukta bloklayıcı pop → **yasak**; paylaşım `std::atomic` / wait-free ring (§1.3 kaynak 19, 20, 10) |
| 3 | **Bloklayıcı I/O yasağı** | Disk, DB, ağ (HTTP/socket), konsol, log yazımı, `sleep`, kullanıcı girişi → **RT yolunda asla** (prompt-maker `06-deep-domain-rules.md:87`; §1.3 kaynak 18) |
| 4 | **Bellek önceden** | Tüm buffer/pool/ses verisi iş parçacığı **başlamadan önce** ayrılır (§1.3 kaynak 10); `alignas(64)` + sabit kapasite (brain.md §7.1) |
| 5 | **Sadece hesap** | İzinli: stack, member, `constexpr`, SIMD (SSE2/AVX2/NEON), `std::atomic`, `noexcept` (brain.md §7.1, `dsp-chain.md:50-56`) |
| 6 | **Deterministik gecikme** | Aynı girdi → aynı CPU yolu; veri-bağımlılı dallanma ve tahmin edilemez döngü sınırlandırılır; NaN/inf ve denormal koruması hesap içinde, **bloklayıcı değil** |

**d) Xrun / underrun politikası (tespit → log → kurtarma):**

| Aşama | Kural | Ayrıntı |
|-------|-------|---------|
| **Tespit** | Her katman kendi xrun'ını **atomik sayaç** ile işaretler | Firmware: DMA/USB kuyruk taşması · Host: callback geç kaldı / buffer boş (underrun) veya taştı (overrun) — §1.3 kaynak 15, 21; sayaç örneği `c-template.md:244` |
| **Log** | Sayaç **non-RT iş parçacığında** okunur, **sıklık-limitli** ERROR yazar | Callback'te log **yazılmaz** (§2.2c kural 3); limit/alert ADR-013 ruhu |
| **Kurtarma 1 (yumuşak)** | Öncelik: buffer'ı bir kademe artır → CPU/bus yükünü azalt | §1.3 kaynak 23 |
| **Kurtarma 2 (ses bütünlüğü)** | CPU %100 → **fade-out → 50ms sessizlik → restart** | `brain.md:869` (birebir korunur) |
| **Kurtarma 3 (cihaz)** | **ASIO device loss → WASAPI → Null Output** | `brain.md:862`, `.ai/AGENTS.md:169`; eskalasyon `:348` (L1→L2, 30s) |
| **Telemetri/limit** | Sayıcı ve eşik ADR-013'e bağlanır; `underrun = 0` hedefi `⚠️ VERIFICATION REQUIRED` kalır | ADR-006 §2.2c, §1.4 |
| **Yasak** | Xrun'da **sessizce devam**, **çökme** veya **callback içinde log/Printf** | §1.3 kaynak 21-22; `adr-audio-template.md` `audio_task(){ printf }` anti-örneği |

**e) Öncelik ve zamanlama:**

- RT iş parçacığı **en yüksek uygulama önceliğinde** çalışır (Windows: süreklilik/AVRT benzeri mekanizma; Linux: **SCHED_FIFO 1-99** — §1.3 kaynak 15).
- Öncelik **verilmezse ölçüm geçersizdir**; sessiz normal-önceliğe düşme (rt throttling/rtkit — §1.3 kaynak 17) log ile izlenir.
- CPU-hog yüksek öncelikli iş parçacığı RT iş parçacığını engelleyebilir (§1.3 kaynak 16) → RT yoluna yalnız ses işi konur, analiz/GC/bağlayıcı işler **düşük öncelikli** iş parçacığına alınır.

**f) Fallback zinciri ve kill-switch:**

| Tetik | Zincir | Kaynak |
|-------|--------|--------|
| ASIO cihazı kaybolursa (USB kopması) | **ASIO → WASAPI Exclusive → WASAPI Shared → Null Output** (sessizce, kullanıcıya durum bildirimi UI'da) | `brain.md:862`, `.ai/AGENTS.md:169` |
| Ölçülen round-trip bütçe aşarsa (<10ms/20ms) | Buffer'ı otomatik bir kademe artır + ERROR log + kullanıcıya "latency yükseldi" bildirimi | `.ai/CLAUDE.md:429`, §1.3 kaynak 23 |
| Xrun tekrarlayan | Sayacı artır, sıklık-limitli log, oturum sonunda rapor | ADR-013, §1.3 kaynak 22 |
| RT kısıtı ihlali (CI'da) | **Fail:** build/denetim başarısız (kod çekerse) | ADR-005 §2.2c (disk kanıtı) |
| Kill-switch | Tek anahtar ile plugin DSP bypass (ekolayzır/dinamik pasif, ham akış) → acil durum geri dönüşü | ADR-016 ruhu (tek config anahtarı) |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Tek süreçte monolitik audio engine** (firmware + DSP + sürücü tek kod tabanı) | Tek dil, tek test, tek deploy | Donanım zamanlaması (I²S/TDM) işletim sistemi koduyla **yazılamaz**; USB/ASIO ayrı süreç-ayrı öncelik ister; tek hata tüm zinciri götürür | §1.3 kaynak 1, 2, 3 — zamanlama firmware'in donanım işi; monolit sınırı belirsizleştirir, bu ADR'nin tam tersi |
| 2 | **Yalnız firmware** (host/plugin katmanı yok, her şey XU316'da) | Donanım RT'si en saf hali | VST3/AU dağıtım yolu yok, geliştirme döngüsü yavaş (her değişiklik firmware flash), PC playback/monitoring çözülmez | K3 (Neva Engine) ve K2 (sürücü) katmanları `.ai/architecture/` içinde ayrı ve zaten planlı (brain.md §217-219) |
| 3 | **Web Audio API / tarayıcı DSP** | Kurulum yok, ADR-001 web yığınıyla uyumlu | RT garantisi tarayıcıdadır, ölçülemez; buffer/latency işletim sistemi ve tarayıcıya bağımlı; ASIO'ya erişim yok | Bu ADR "donanım modu" kararıdır; web katmanı ADR-001 kapsamında ayrıdır (§1.4 kısıt); pro ses hedefi (`.ai/CLAUDE.md:429`) karşılanmaz |
| 4 | **Yalnız WASAPI, ASIO'yu atla** | Tek Windows API, sürücü bağımlılığı az | Pro ses standardı ASIO sub-5ms bandı kapanır; DAW/ekipman uyumu kaybolur; `asio-drivers.md:12` hedefi çöker | §1.3 kaynak 11, 12 — ASIO hâlâ Windows'ta düşük gecikme referansı; ADR/edge-case (`brain.md:862`) ASIO'yu birincil yapar, **fallback'i silmek** seçenek değildir |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek sınır kararı:** üç katmanın sorumluluğu, RT garantisi ve xrun sahibi tek tabloda (§2.2a) → katman ihlalleri denetlenebilir hale gelir.
- **Hard-RT kuralları tek elden:** tahsisat/kilit/I/O yasağı + öncelik + ölçüm kapısı bir arada; kod yazılmadan önce elde olan spec dosyalarına bağlanır.
- **Geri alınabilir tasarım:** her katmanın fallback'i ve kill-switch'i yazılı (§2.2f) → cihaz kaybı/Xrun sessiz çökmez, zincirleme devam eder.
- **Ölçülebilir kapı:** `<10ms/<20ms` bütçesi ADR-006 ile birebir uyumlu; `underrun = 0` dürüst etiketle (`⚠️ VERIFICATION REQUIRED`) korunur — uydurma sayı yok.
- **Kırık bağlantılar canlanır:** `[[ADR-017-dsp-hardware-mode]]` 12+ dosyada (`AGENTS.md:683`, `brain.md:862`, `keys.md:117/140`, `index.md:634`, `.claude/CLAUDE.md:556`) artık diskteki dosyaya gider; `.ai/broken-links-report.md:49` kapanır.

### 4.2 Olumsuz Sonuçlar

- **Kod hâlâ yok:** üç katman da PLANNED (§1.1-B) — bu ADR tek başına çalışan ses vermez; §5.1 adımları ayrıca icra edilmeli.
- **Ölçüm altyapısı kurulu değil:** round-trip/xrun ölçümü, CI gate (`.github/workflows/` = 0 dosya) yok → kapı yazıldı ama denetlenmiyor.
- **Çelişki mirası:** `.ai/log.md:98` "12 header" iddiası dosyasız → bir sonraki ekibin kafa karışıklığı; ADR-005 gereği işaretlendi ama **silinmedi** (append-only).
- **Katman disiplini bedeli:** her katmanın sınırında ek adaptör/arayüz gerekir (firmware↔plugin↔host veri yolu) → ilk kod yazımında %10-20 ek tasarım yükü.
- **Platform bağımlılığı:** ASIO yalnız Windows; macOS/Linux ayrı iş (CoreAudio/ALSA/PipeWire dokümanları var, bu ADR'de karar değil).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Xrun ses patlaması** (click/pop) — callback geç kaldı, boş buffer çalındı | 3 (olası) | 4 (yüksek) | §2.2d üç aşamalı politika: atomik sayaç → sıklık-limitli log → buffer artır/fade-out; `underrun = 0` hedefi ADR-006'da etiketli olarak izlenir |
| **Host farklılıkları** — talep edilen buffer ≠ gerçekleşen gecikme (JUCE/ASIO/WASAPI) | 4 (çok olası) | 3 (orta) | Kapı **ölçülen** round-trip ile konar (§1.3 kaynak 12, 14); host başına ölçüm tablosu §5.1 adım 4 |
| **Platform bağımlılık** — ASIO Windows-only, sürücü/ekipman farklılığı | 4 (çok olası) | 3 (orta) | Fallback zinciri §2.2f (ASIO→WASAPI→Null); katman 3 tek arayüz arkasında tutulur (sürücü değiştirilebilir) |
| **Ölçüm yokluğu / sessiz öncelik düşüşü** — RT önceliği alınamaz veya sessizce düşer | 3 (olası) | 4 (yüksek) | Öncelik başarısızlık **loglanır** (§2.2e); round-trip ve xrun ölçümü olmadan "RT çalışıyor" iddiası yazılmaz (ADR-005) |
| **Spec-kod boşluğu** — ADR uygulanmadan kod yazılır, sınır sonra çizilir | 3 (olası) | 3 (orta) | §5.1 adım 1-2 sınır+arayüz önce; kod adımları 3'ten sonra; katman ihlali → revert + log ERROR (AGENTS.md §18) |
| **Tahsisat yasağı istisnasının istismarı** — "gerekirse lock" | 2 (mümkün) | 4 (yüksek) | Varsayılan **yasak**; istisna yalnız ölçümle + Tech Lead onayıyla geçici kayıt (ADR-005); §1.3 kaynak 19, 20 tartışması bu ADR'de yasağı güçlendirir |
| **Xrun log taşkını** — tekrarlayan hata ERROR boğar | 3 (olası) | 2 (düşük) | Sıklık limiti + sayaç meta olarak (ADR-013 ruhu); log RT yolunda **yazılmaz** |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı: her `IMPLEMENTED` etiketi dosya+satır kanıtlı, her doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` (§1.1, §1.4) |
| [[ADR-013-rate-limiting-apcu]] | Sayaç + limit + sıklık-limitli alert ruhu → xrun/underrun telemetrisi (§2.2d, §4.3) |
| [[ADR-001-vanilla-js-itcss]] | Web katmanı sınırı: JS/UI RT yoluna girmez (§1.4) |
| [[ADR-006-performance-targets]] | `<10ms ASIO / <20ms WASAPI` bütçesi + `underrun = 0` `⚠️ VERIFICATION REQUIRED` konumu birebir korunur (§2.2b, §1.4) |
| [[../index]] | Satır 54 `[[ADR-017-dsp-hardware-mode]]` — slug eşleşmesi ✅ (bu dosya rezervasyonu doldurur) |
| [[../../index.md]] | Satır 634 `decisions/accepted/ADR-017-dsp-hardware-mode` kaydı ✅ (dosya ile canlanır) |
| [[../../keys.md]] | Satır 117, 140, 252 — `ADR-017 | DSP hardware, XMOS, JUCE, ASIO` ✅ |
| [[../../brain.md]] | §7.1 satır 378-380 (RT yasakları), §7.2 satır 382-396 (callback), satır 286 (512≈10.67ms), satır 862/869 (fallback), satır 972/1037 (ADR-017 referansları) |
| [[../../AGENTS.md]] | §17 satır 169 (ASIO→WASAPI), §10 satır 348 (eskalasyon), §21 satır 683 (bu dosyaya wiki-link) |
| [[../../glossary.md]] | Satır 678 `DSP / JUCE / ASIO → ADR-017` — terim girişi bu kararla geçerli olur |
| [[../../.templates/adr/adr-index.md]] | Satır 88, 133 `ADR-017 · XMOS XU316 + PCM3168A DSP` ✅ (satır 216'daki "diskte yok" uyarısı bu dosya ile kapanır) |
| [[../../.templates/adr/adr-audio-template.md]] | Ses-domaini şablonu — bu ADR §1.3/§2.2d/xrun dilini bu şablonla hizalar |
| [[../../architecture/firmware/xmos-firmware]] | Katman 1 spec (satır 12, 50, 66, 91-92) |
| [[../../architecture/k3-ses-motoru/neva-engine-core]] · [[../../architecture/k3-ses-motoru/dsp-chain]] | Katman 2 spec (satır 12, 177, 329 · 12, 50-56) |
| [[../../architecture/k2-surucu/asio-drivers]] · [[../../architecture/k2-surucu/wasapi-exclusive]] · [[../../architecture/k2-surucu/latency-optimization]] | Katman 3 spec (satır 12, 21, 35, 41-45 · 12, 33-38, 51 · 0.5ms hedefi) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| `.ai/broken-links-report.md` satır 49 · `.ai/reports/broken-files-report.md` satır 182 | Kırık `[[ADR-017-dsp-hardware-mode]]` — bu dosya ile kapanır |
| `.ai/log.md` satır 98 | "12 header" iddiası — dosya yok → `⚠️ VERIFICATION REQUIRED` (§1.1-B) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Sınır + arayüz sözleşmesi:** §2.2a katman tablosu `architecture/index.md` §7.4 ve `brain.md` §13'e **bağlayıcı not** olarak işlenir (dosya adı değişmez); firmware↔plugin↔host veri yolu arayüzleri (ne taşınır, kim başlatır, kim durdurur) tek sayfada tanımlanır | Embedded Engineer + DSP Firmware Engineer | 0.5 oturum |
| 2 | **Hard-RT kural dosyası:** brain.md §7.1 + bu ADR §2.2c tek kontrol listesine dönüştürülür; kod incelemesi şablonuna "callback'te tahsisat/kilit/I/O" denetimi eklenir | Embedded Engineer + QA Engineer | 0.5 oturum |
| 3 | **Katman 3 (host) — ilk kod:** `asio_driver`/`wasapi_driver` iskeleti, buffer seçimi (128-512), **ölçülen round-trip** raporu, fallback zinciri §2.2f; dosya adı `.ai/architecture/k2-surucu/*` ile hizalı | Windows Software Engineer + Embedded Engineer | 2 oturum |
| 4 | **Katman 2 (plugin) — ilk kod:** `processBlock(...) noexcept` iskeleti + lock-free ring + atomik parametre; **xrun sayacı okuması** (üretmez, ölçer); tahsisat/kilit testi | Embedded Engineer | 2 oturum |
| 5 | **Katman 1 (firmware) — spec tasdiki:** XU316 + `lib_i2s` (I²S/TDM) spesifikasyonu `firmware/xmos-firmware.md` ile hizalanır; kanal eşlemesi tablosu PCM3168A/AK4458 girişiyle birebir | DSP Firmware Engineer + Audio Hardware Engineer | 1 oturum |
| 6 | **Ölçüm + telemetri:** round-trip ve xrun ölçümü (host başına tablo), sıklık-limitli ERROR (ADR-013), CI gate fikri (`.github/workflows/` bugün 0 dosya → PLANNED) | QA Engineer + DevOps Engineer | 1 oturum |
| 7 | **Çelişki düzeltmeleri (vault, append-only, ayrı işlem):** `.ai/log.md:98` "12 header" iddiasına düzeltme-notu (dosya yok → `⚠️ VERIFICATION REQUIRED`); `.ai/AGENTS.md` §24.3 "dizin var, 0 dosya" ifadesi "dizin YOK" olarak; `brain.md:972`/`adr-index.md:88` etiketleri "DSP Hardware Mode" ile hizalanır | Vault Steward | 0.5 oturum |
| 8 | **Test paketi:** RT yasak ihlali (tahsisat/kilit tuzakları), buffer 128/256/512 ölçümü, xrun sayacı + log limiti, fallback zinciri (ASIO→WASAPI→Null), bütçe kapısı (`<10ms/<20ms`) | QA Engineer | 1 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** yazılır (`ADR-088+` serisi veya rezerve slot), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı).
2. **Sınır/uygulama (adım 1-2):** yalnız vault notu/eklendiği checklist → `git revert` ile eski hâline döner; kod etkilenmez.
3. **Katman 3 host kodu (adım 3):** ayrı commit → `git revert` tek adımda; zaten kod yok (PLANNED) → kayıp yok.
4. **Katman 2 plugin kodu (adım 4):** ayrı commit → `git revert`; xrun sayacı okuması kaldırılsa bile host katmanı çalışmaya devam eder (katman bağımsızlığı §2.2a).
5. **Ölçüm/telemetri (adım 6):** telemetri anahtarı kapatılabilir (**kill-switch**: `audio.rt_telemetry = off`) — log/CI kesilir, ses yolu durmaz.
6. **Fallback zinciri bozulursa:** tek config ile **WASAPI-only moda** dönülür (ASIO devre dışı) → ses kesilmez (brain.md:862 zinciri korunur).
7. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (eski satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → **KABUL** (2026-09-24) |
| Karar içeriği | Tümü **kullanıcı onaylı** (üst görev kapsamı — 3 katman + hard-RT kısıtları + §1.3 araştırması) |
| Beklenen biçim | ADR-004/008/010-016 formatı — 3 tur / 20 persona (uygulandı — sonuç §7.1) |
| Tech Lead | **✅** — debate sonrası onay (2026-09-24) |
| Kural | Debate tamamlanmadan bu ADR **frozen yapılmaz**; sonuç §5.3/§5.5/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir |

### 5.4 Debate Bekleyen Şartlar (ön kayıt — KABUL/RED debate'de netleşir)

| # | Şart | Kapsam | Sorumlu | Durum |
|---|------|--------|---------|-------|
| 1 | **Katman sınırı bağlayıcı** — hiçbir katman diğerinin işini üstlenemez | §2.2a tablosu + ihlal → revert (AGENTS.md §18) | Embedded + DSP FW | ✅ debate (3/20 KABUL) |
| 2 | **Hard-RT yasakları** — tahsisat/kilit/bloklayıcı I/O callback'te yasak | §2.2c 6 kural; istisna yalnız ölçüm + Tech Lead onayı | Embedded | ✅ debate (3/20 KABUL) |
| 3 | **Xrun politikası** — tespit (atomik sayaç) → non-RT sıklık-limitli log → kurtarma (buffer/fade-out/restart) + fallback zinciri | §2.2d, §2.2f; ADR-013 ruhu | Embedded + QA | ✅ debate (3/20 KABUL) |
| 4 | **Buffer/latency bütçesi** — 256 varsayılan, 128-512 aralık, `<10ms/<20ms` ölçülen kapı, `<0.5ms` ayrı hedef | §2.2b; `.ai/CLAUDE.md:429` korunur | Windows SW + QA | ✅ debate (3/20 KABUL) |
| 5 | **RT öncelik + ölçüm şartı** — öncelik verilmezse ölçüm geçersiz; `underrun = 0` `⚠️ VERIFICATION REQUIRED` kalır | §2.2e, §1.4 (ADR-006) | QA + DevOps | ✅ debate (3/20 KABUL) |

### 5.5 Debate Şartları (Kabul Koşulları — 3/3)

| # | Şart | Kapsam | Sorumlu | Durum | Kanıt |
|---|------|--------|---------|-------|-------|
| 1 | **`.ai/log.md:98` sahte-iddia düzeltmesi** | "12 header files, ~79KB NevaEngine implement" iddiası `neva_engine*` araması = **0 sonuç** ile çelişir → log **append-only** olduğu için eski satıra dokunulmaz (AGENTS.md §25.3 kural 3); düzeltme **yeni satır olarak append** edilir: `Düzeltme: log.md:98 NevaEngine "79KB implement" iddiası desteksiz` | Vault Steward | ✅ UYGULANDI (2026-09-24 — log.md append) | Debate Tur 1 Critic → §1.1-B, §4.2 |
| 2 | **Latency / xrun test bench** | `<10ms ASIO / <20ms WASAPI` ölçüm kapısı yalnız **ölçülen round-trip** ile konur; **xrun = 0 CI hedefi** yazılır (`.github/workflows/` bugün **0 dosya** → PLANNED) — kapı bugün denetlenmiyor (§4.2) | QA Engineer + DevOps Engineer | ⏳ PLANNED → §5.1 adım 6, 8 | Debate Tur 2 itiraz 3 → §2.2b, §2.2d |
| 3 | **Spec→kod geçiş maddesi + `underrun = 0` tescili** | Bu ADR **sınır/kısıt kararıdır**: kod 0, spec bol (§1.1-B) → üç katman kodu §5.1 adım 3-5 ile üretilir (ADR = sınır, sonra kod); `underrun = 0` hedefi **vault'a tescil edilmeden** kesinleştirilmez, `⚠️ VERIFICATION REQUIRED` etiketi ADR-006 ile korunur (§1.4) | Embedded Engineer + DSP Firmware Engineer + Vault Steward | ⏳ PLANNED → §5.1 adım 3-5 · tescil: ADR-006 §5.1 | Debate Tur 1 bulgu + Tur 2 itiraz 1 → §1.1-B, §1.4 |

**Tur 2 teyitleri (şart sayılmadı):** xrun **kill-switch** (§2.2f — tek anahtar ile plugin DSP bypass) teyit edildi; ölçüm kapısının "nasıl doğrulanacağı" belirsizliği şart 2'ye bağlandı; `.ai/AGENTS.md` §24.3 "dizin var, 0 dosya" çelişkisi §5.1 adım 7'ye bağlandı (şart sayılmadı).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 54** `[[ADR-017-dsp-hardware-mode]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`C++, ASIO, JUCE, audio, DSP, ring buffer, WASAPI` → Embedded Engineer), §17 #6 (ASIO→WASAPI), §21 satır 683 |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | §7.1 RT yasakları · §7.2 ASIO callback · satır 286/862/869 · satır 972 `ADR-017` |
| [[../../keys.md]] | Satır 117, 140, 252 ADR-017 keyword eşlemeleri ✅ |
| [[../../index.md]] | Satır 634 ADR-017 kaydı ✅ · satır 391 `Audio/embedded … PLANNED` |
| [[../../glossary.md]] | Satır 326, 328, 678 — `zero-allocation`, `lock-free`, `xrun` terimleri |
| [[../../log.md]] | Audit trail — bu işlem tek satır append; satır 98 çelişkisi §1.1-B |
| Debate ön şartları (5) | §5.4 — sınır · hard-RT yasak · xrun politikası · buffer/latency · öncelik+ölçüm (**3/20 KABUL** ile onaylandı) |
| Debate (✅ TAMAMLANDI) | §5.3/§5.5/§7.1 — 3 tur / 20 persona, 18/2/0 KABUL (2026-09-24) + 3 şart · sonuç frontmatter `debate` alanına işlendi |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[../../.templates/adr/adr-audio-template.md]] | Ses-domaini şablonu (xrun/latency dilinin kaynağı) |
| [[../../.templates/adr/adr-index.md]] | Satır 88, 133, 216 — "diskte yok" uyarısı bu dosya ile kapanır |
| [[ADR-005-ultrathink-protocol]] | Doğrulama + `⚠️ VERIFICATION REQUIRED` standardı (dosya diskte VAR ✅) |
| [[ADR-006-performance-targets]] | `<10ms/<20ms` bütçesi + `underrun = 0` etiketi (dosya diskte VAR ✅) |
| [[ADR-013-rate-limiting-apcu]] | Sayaç + limit + alert ruhu → xrun telemetrisi (dosya diskte VAR ✅) |
| [[ADR-001-vanilla-js-itcss]] | Web katmanı sınırı (dosya diskte VAR ✅) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| `shared/src/` · `shared/tests/` | Ses/DSP kodu **YOK** (yalnız PHP) → §1.1-B, §5.1 adım 3-4 |
| `.ai/architecture/firmware/` · `k2-surucu/` · `k3-ses-motoru/` | Üç katmanın spec dosyaları (IMPLEMENTED doküman, §1.1-A) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-017'yi sıfırdan yaz"; karar içeriğinin tamamı onaylı) | 2026-09-24 | ✅ |
| Tech Lead | ✅ — debate KABUL (3 tur / 20 persona, 18/2/0) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010-016 formatı — 3 tur / 20 persona (uygulandı) |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-24 (frontmatter `debate` alanı ile aynı) |
| Tur 1 | **20 persona** — bulgu: kod **0** (8/8 hedef DSP dizini yok → tümü spec: `xmos-firmware.md`, `neva-engine-core.md`, `asio-drivers.md`); `.ai/log.md:98` "12 header, ~79KB NevaEngine implement" iddiası **desteksiz** (`neva_engine*` = 0 sonuç); `underrun = 0` **tescilsiz**; ölçüm kapısı `<10ms/<20ms` nasıl doğrulanacak **belirsiz**. Oy: **15 kabul/neutral, 4 uyarı** — **Critic:** sahte iddia düzeltmesi şart. |
| Tur 2 | **İtiraz → çözüm:** (1) kod 0 + spec bol → **spec→kod geçiş planı** maddesi (ADR = sınır kararı) → **şart 3**; (2) `log.md:98` asılsız iddia → **düzeltme** → **şart 1**; (3) ölçüm kapısı → **latency test bench + xrun = 0 CI hedefi** → **şart 2**; (4) xrun kill-switch → **teyit** (şart sayılmadı — §2.2f). |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL — 3 şart (§5.5):** (1) `log.md:98` sahte-iddia düzeltmesi, (2) latency/xrun test bench, (3) spec→kod geçiş maddesi + `underrun = 0` tescili |
| Tech Lead | **✅** (2026-09-24) — debate sonrası onay |
| Frozen | Bu ADR **frozen değildir**; debate ✅ + Tech Lead ✅ ama **Arch Lead ⏳** → §7 satır 3 tamamlanmadan `frozen` yapılmaz |
| Kural | Debate sonucu §5.3/§5.5/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` **append** ile kaydedilir |

---

*ADR-017 v1.0.0 | 2026-09-24 | Created — DSP Hardware Mode (3 katman + hard-RT kısıtları)*
*ADR-017 debate | 2026-09-24 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead ✅ · 3 şart (§5.5)*
*Authority: ADR-017 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
