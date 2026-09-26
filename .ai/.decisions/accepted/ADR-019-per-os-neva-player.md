---
title: "CoreMusic — ADR-019: Per-OS Neva Player (Windows ASIO/WASAPI · macOS CoreAudio · Linux ALSA/PipeWire — Ortak Çekirdek + İnce Adapter, IAudioBackend Arayüzü, Fallback Zinciri + Kill-Switch)"
type: adr
category: audio
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-019 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-019: Per-OS Neva Player (Windows ASIO/WASAPI · macOS CoreAudio · Linux ALSA/PipeWire — Ortak Çekirdek + İnce Adapter, IAudioBackend Arayüzü, Fallback Zinciri + Kill-Switch)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-019'u sıfırdan yaz"; karar içeriğinin tamamı kullanıcı onaylı: 3 platform tek ADR · ortak çekirdek + ince adapter · özellikler · §1.3 araştırması) · debate: **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · Tech Lead: **✅ (2026-09-25)**
**İlgili ADR'ler:** [[ADR-017-dsp-hardware-mode]] (hard-RT kısıtları — callback tahsisat/bloklayıcı I/O yasağı, buffer/latency bütçesi, xrun politikası bu kararın çekirdeğini **bağlar**; `brain.md:862` fallback zinciri oradan gelir — dosya diskte VAR ✅) · [[ADR-004-multi-domain-spa]] (ürün mimarisi / çoklu domain — player'ın ürün içindeki yeri ve web katmanı ile sınır; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı — kod yokluğu etiketli; dosya diskte VAR ✅) · [[ADR-006-performance-targets]] (latency hedefi `<10ms ASIO / <20ms WASAPI` `.ai/CLAUDE.md:429` — bu kararın ölçüm kapısı; dosya diskte VAR ✅) · karar dizini [[../index]] **satır 56** `[[ADR-019-per-os-neva-player]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic'in masaüstü ses yolu **üç işletim sisteminde** çalışmak zorundadır: Windows (ASIO + WASAPI Exclusive/Shared), macOS (CoreAudio), Linux (ALSA + PipeWire). Bu üç platformun ses API'leri birbirinden **tamamen farklı** (callback sözleşmesi, cihaz enumerasyonu, exclusive kilit semantiği, örnek hızı yönetimi); buna karşılık Neva Player'ın **iş mantığı** (decode, DSP zinciri, durum makinesi, buffer politikası) **platformdan bağımsızdır**. Bugüne kadar bu sınır hiçbir tek belgede yazılmadı: k2-surucu spec'leri platform API'lerini tek tek anlatıyor (`asio-drivers.md`, `wasapi-exclusive.md`, `alsa-native.md`, `pipewire-modern.md`, `core-audio-macos.md`), `brain.md:862` yalnız tek satırlık fallback zinciri veriyor, **"çekirdek nerede biter, adapter nerede başlar"** cümlesi hiçbir yerde yok. Bu ADR; **tek Neva Player çekirdeği + her OS için ince adapter** mimarisini, ortak `IAudioBackend` arayüzünü, backend seçim sırasını ve özellik sözleşmesini (hot-plug, exclusive/shared geçiş, sample-rate mismatch, fallback + kill-switch) tek karar altında bağlayıcı hale getirir.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskte okundu — IMPLEMENTED/PLANNED etiketleri dosya + satır ile):**

**A) SPEC/VAULT KATMANI — IMPLEMENTED (doküman var; kod YOK):**

- **Sürücü spec'leri — `.ai/architecture/k2-surucu/` (IMPLEMENTED doküman, 14 dosya):**
  - `asio-drivers.md:12` — "ASIO Exclusive mode ile **0.5ms round-trip** latency hedefler"; **satır 21** buffer "32 sample'a kadar düşürülebilir"; **satır 35** Exclusive `< 0.5ms` ↔ Shared `2-10ms`; **satır 109** "`ASIOError_InvalidMode`: Exclusive kullanılamıyorsa **Shared mode'a geç**" (fallback davranışı spec'te yazılı).
  - `wasapi-exclusive.md:12` — "hem Exclusive hem de Shared modda"; **satır 38** `Initialize()` exclusive mod; **satır 126** `AUDCLNT_E_DEVICE_IN_USE` → "**Shared mode'a geç**" (çakışma fallback'i spec'te yazılı).
  - `alsa-native.md:12` — "ALSA, PipeWire ile birlikte kullanılabildiği gibi **tek başına da çalışabilir**"; **satır 40** `SND_PCM_ACCESS_MMAP_INTERLEAVED` (en düşük latency); **satır 55** `snd_pcm_hw_params_set_rate_near` (örnek hızı uyarlama).
  - `pipewire-modern.md:12` — PipeWire "ALSA, PulseAudio ve JACK'ın yerini alarak **tek bir unified API**"; **satır 130-133** "otomatik format dönüşümü: Kaynak Format → PipeWire → Hedef Format" (mismatch çözümü spec'te yazılı).
  - `core-audio-macos.md:12` — "CoreAudio'nun **AudioUnit** ve **HAL** katmanlarını kullanarak"; **satır 46-57** `AudioDeviceID` + `kAudioHardwarePropertyDevices` (cihaz enumerasyonu/hot-plug kanıtı spec'te yazılı).
  - Aynı dizin: `buffer-management.md`, `latency-optimization.md`, `driver-stack-mimari.md`, `bluetooth-a2dp.md`, `usb-audio-class.md`, `network-audio-drivers.md` + `README.md`/`index.md`/`CLAUDE.md`.
- **Motor çekirdeği spec'i — `.ai/architecture/k3-ses-motoru/neva-engine-core.md` (IMPLEMENTED doküman):**
  - `:12` — "C++20 … **real-time safe** ve **lock-free**"; **satır 46-70** `namespace neva::rt` hard-RT kuralları (YASAK: `new/malloc`, `std::mutex`, file I/O, sleep); **satır 76-80** `SPSCRingBuffer` (lock-free veri yolu) — **çekirdek zaten OS'tan bağımsız tasarlanmış**, adapter boşluğu bu ADR ile kapanır.
- **Backend seçim + edge-case — `.ai/brain.md` (IMPLEMENTED):**
  - **satır 862** — `ASIO Device Loss | USB kopması | **WASAPI fallback → Null Output** | [[ADR-017-dsp-hardware-mode]]` (**ASIO → WASAPI → Null sırası** bu kararın seçim zinciridir).
  - **satır 218** — K2 sürücü katmanı "ASIO, WASAPI, ALSA, **PipeWire**, I2S"; **satır 286** "ASIO Buffer: 512 sample varsayılan (64-1024), 48kHz, 32-bit float, ~10.67ms"; **satır 382** §7.2 ASIO callback örneği.
- **Sınır/eksalasyon — `.ai/AGENTS.md`:** **§17 satır 169** "ASIO device loss → WASAPI fallback" (Edge Case 6); **§10.1** "ASIO cihaz kaybı L1(Embedded) → L2, 30s"; **§15** Windows Software Engineer (`win-sw`) "WASAPI, COM, WinRT, WDK" · Embedded Engineer "C++20, JUCE, ASIO SDK" (adapter sahipliği bu iki agent arasında bölünür).
- **Dizin kayıtları (önceden rezerve — bu dosya ile canlanır):** `.ai/.decisions/index.md:56` · `.ai/index.md:636` · `.ai/brain.md:974` · `.ai/keys.md:254` · `.ai/architecture/index.md:95` (K0 "ADR-017, ADR-019") · `k0-isletim-sistemi/README.md:393` + `k0-isletim-sistemi/CLAUDE.md:51` + `k0-isletim-sistemi/windows-api.md:258` + `k2-surucu/README.md:234` (hepsi `ADR-019 | Per-OS Neva Player`) · `.ai/.templates/adr/adr-index.md:90` (`🔴 core`, `adr-audio-template.md ⚠️`).
- **Eski çelişki (düzeltme kaydı):** `.ai/.decisions/accepted/ADR-006-performance-targets.md:291` "ADR-019 … dosyalar diskte YOK → düz metin, wiki-link kurulmaz" — **bu yazım ile dosya diskte canlanır**, o satırın hükmü sona erer (ADR-006 frozen → metni değiştirilmez, yalnız bu ADR §4.4'te bağlanır).

**B) KOD KATMANI — YOK → PLANNED (uydurulmadı — `⚠️ VERIFICATION REQUIRED`):**

- **Kaynak dosya taraması:** `**/*.{cpp,h,hpp,cc}` glob → **0 dosya** (repo geneli) → Neva Player çekirdeği, adapter, backend kodu **henüz yazılmamıştır**.
- **İçerik taraması (kod dosyaları `.php/.js/.cpp/.h/.ts`):** `neva` · `player` · `WASAPI` · `CoreAudio` · `ALSA` · `PipeWire` · `PortAudio` · `RtAudio` · `miniaudio` · `IAudioBackend` → **0 eşleşme** (tek istisna: `miniaudio`/`PortAudio` yalnız `.md` dokümanlarında — `.ai/ecosystem/ses-dsp-acik-kaynak.md:82,250`, `.ai/architecture/github-referanslari.md:58,171`, `VISION.md:82`).
- **`IAudioBackend` adı:** repo geneli grep → **0 sonuç** (kod + doküman) → arayüz bu ADR'de **ilk kez** tanımlanır, mevcut bir kod parçasına atıf yapılmaz.
- **Dizin taraması:** `.ai/projects/NevaEngine/`, `neva/`, `engine/`, `audio/`, `player/` → YOK (ADR-017 §1.1-B ile aynı sonuç; `.ai/AGENTS.md` §24.3 "dizin var, 0 dosya" ifadesi de çelişkili → `⚠️ VERIFICATION REQUIRED`).
- **`.ai/log.md:98` (2026-09-18) iddiası:** "NevaEngine … `driver/asio_driver.h`, `driver/wasapi_driver.h`" → dosya araması 0 → **desteksiz iddia** (ADR-017 §1.1-B'de kayıtlı; bu ADR de kod varmış gibi yazılmaz).

**C) ÖZELLİK KATMANI — spec'te var, kodda yok (PLANNED):**

- Exclusive/Shared geçiş kuralı → spec: `wasapi-exclusive.md:126`, `asio-drivers.md:109` · kod: **0**.
- Örnek hızı/bit derinliği uyarlama → spec: `alsa-native.md:55`, `pipewire-modern.md:130-133` · kod: **0**.
- Cihaz hot-plug → spec: `core-audio-macos.md:46-57` (property dinleme deseni) · kod: **0** (k2 spec'lerinde `hot-plug` ayrı olay API'si olarak geçmiyor → `⚠️ VERIFICATION REQUIRED`).
- Fallback zinciri + kill-switch → spec: `brain.md:862`, `AGENTS.md` §17.6 · kod: **0**.

**Sonuç etiketi:** spec/vault katmanı **IMPLEMENTED** (k2-surucu 14 dosya + neva-engine-core + brain/AGENTS edge-case satırları + 10 dizin kaydı); **player/adapter/backend kodu tamamen PLANNED** (0 `*.cpp/*.h` dosyası, 0 API eşleşmesi). Bu ADR **kod sözü değil, mimari sözleşmedir** — ADR-017'nin RT kısıtlarıyla uyumlu, uygulaması §5.1 adımlarına bağlıdır.

### 1.2 Sorun Tanımı

1. **Çekirdek/adapter sınırı yazılmamış:** `neva-engine-core.md:46-70` RT kurallarını verir, `asio-drivers.md`/`wasapi-exclusive.md` API'leri verir ama "OS API'sine kim çağırır?" sorusunun cevabı yok → herkes çekirdeğin içine `#ifdef _WIN32` koyma riskiyle karşı karşıya (RT ihlali + portability kaybı).
2. **Üç platform tek kararda değil:** Windows spec'i (`asio-drivers`, `wasapi-exclusive`), Linux spec'i (`alsa-native`, `pipewire-modern`), macOS spec'i (`core-audio-macos`) ayrı dosyalarda; **ortak API sözleşmesi, backend seçim sırası ve hata semantiği** hiçbirinde yok → adapter yazılmaya başlayınca üç ayrı gerçeklik doğar.
3. **Fallback zinciri tek satır:** `brain.md:862` "ASIO → WASAPI → Null" der, `asio-drivers.md:109` ve `wasapi-exclusive.md:126` parça parça fallback yazar — ama **zincirin tamamı, kill-switch ve "hangi hata hangi basamağa düşer"** tablosu yok.
4. **Özellikler tanımsız:** device hot-plug, exclusive/shared geçiş tetiği, sample-rate/bit-depth mismatch'in resample sınırı (ne zaman resample, ne zaman hata) hiçbir yerde karar değil.
5. **Kod yok → uygulama riski:** 0 `*.cpp/*.h` (§1.1-B); karar yazılmazsa her uygulama denemesi sıfırdan başlar ve ADR-017 RT kısıtlarına uyum denetlenemez.
6. **Sahiplik dağınık:** adapter kodu Embedded (`C++20/ASIO`) ile Windows SW (`WASAPI/COM`) arasında (`AGENTS.md` §5 `*.cpp → Embedded Engineer`, §15 `win-sw`); sınır çizilmezse domain violation + layer violation riski (§17.7 revert).

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırması protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — resmi/anahtar kaynak önce (developer.microsoft.com/learn.microsoft.com, pipewire.org, portaudio.com, apple developer), her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) cross-platform audio backend kütüphaneleri 2025-26 (RtAudio/PortAudio/miniaudio), (b) CoreAudio/ALSA/PipeWire API'leri, (c) WASAPI vs ASIO (OS adapter odağı — ADR-017'den fark: burada sürücü değil adapter karşılaştırması), (d) adapter/bridge OOP deseni, (e) hot-plug + sample-rate mismatch + fallback.** Erişim: **5 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "miniaudio vs RtAudio vs PortAudio comparison 2025 cross-platform audio backend library" · (2) "WASAPI exclusive vs ASIO latency low latency Windows audio API 2025" · (3) "CoreAudio macOS AVAudioEngine ALSA PipeWire Linux audio API comparison device hot-plug 2025" · (4) "adapter design pattern abstract backend interface cross-platform audio abstraction layer C++ Bridge pattern" · (5) "audio device hot-plug sample rate mismatch resampling bit depth conversion backend fallback strategy cross-platform" |
| Web Search **Konusu** | (1) 2025-26 cross-platform I/O kütüphaneleri: PortAudio (C, callback/bloklayıcı, Windows/macOS/Linux/OSS-ALSA), RtAudio (C++ sınıf seti, ortak API), miniaudio (tek dosya, public domain, CoreAudio/PulseAudio/JACK/ALSA backend'leri), JUCE/libsoundio alternatifleri; (2) WASAPI Exclusive vs ASIO: mixer bypass, exclusive kilit, örnek hızı geçişi, latency tabanı; (3) macOS CoreAudio HAL/AudioUnit + `kAudioHardwarePropertyDevices` property dinleme, Linux'ta ALSA (çekirdek, mmap) + PipeWire (PulseAudio/JACK'i birleştiren unified grafik, otomatik format dönüşümü); (4) Adapter vs Bridge: adaptee'yi sarmalayarak uyumsuz arayüzü eşleme, platform-bağımsız soyutlama × platform-bağımlı uygulama hiyerarşilerinin ayrımı; (5) örnek hızı/bit derinliği uyumsuzluğunun belirtileri (bozuk ses, pitch/BPM kayması) ve donanım ekleme-çıkarma yönetimi |
| Web Search **Bağlam** | **~41 adlandırılmış kaynak / 5 sorgu**: portaudio.com (resmi) ×1, audio-digital.net ×2, AlternativeTo, GitHub LabSound issue, Handmade Network (miniaudio), linuxvox, Tech Kaizen (kütüphane listesi) · learn.microsoft.com (Low Latency Audio — resmi), pchardwarepro, undisclosedsounds, recordingbase, patchd, soundlatencytest, asio-renderer/coffeeholictech · pipewire.org (resmi), Reddit r/linuxaudio, Hacker News, discourse.ardour.org, canartuc Medium, supermegaultragroovy (AVAudioEngine/HAL deneyimi), Apple Developer Forums (aggregate device/channel mismatch hatası), YouTube ×1 · sourcemaking.com (Adapter + Bridge — 2 sayfa), tutorialspoint, CodeSignal (C++ Adapter), modernescpp (Adapter/Bridge), dzone (Modern C++ Adapter), Medium, Incus Data · Microsoft Answers, Samson/Positive Grid destek, Cakewalk forum, HomeRecording, soundstagehifi, reddit r/audioengineering, ardop.groups.io |
| Web Search **Kısa Açıklama** | **(1) Kütüphane seçimi:** PortAudio "free, cross-platform, callback **veya** bloklayıcı" (portaudio.com), RtAudio "common API … Linux, Mac, Windows" (audio-digital/AlternativeTo), miniaudio "tek dosya, public domain" + CoreAudio/PulseAudio/JACK/ALSA backend listesi (Handmade Network/linuxvox) → **üçü de adapter işini yapar ama CoreAudio/ALSA'nın özel yeteneklerini (property hot-plug, mmap düşük latency, exclusive kilit) soyutlar/ince ilikler**. **(2) Windows:** learn.microsoft.com "exclusive mode ve ASIO kendi sınırlılıklarına sahip … uygulama doğrudan ASIO sürücüsüne konuşmalı"; WASAPI Exclusive mixer'ı bypass eder ama "latency floor ASIO'dan yüksektir" (soundlatencytest: 2-3 ms), ASIO native sürücü + exclusive lock + örnek hızı geçişinde daha güvenilir (asio-renderer) → **ASIO birinci, WASAPI Exclusive ikinci sıradaki backend'tir**. **(3) Linux/macOS:** PipeWire "ALSA, PulseAudio ve JACK'ı birleştirir" + otomatik format dönüşümü (pipewire.org/canartuc); ALSA mmap en düşük latency (madenci kaynaklarıyla uyumlu); macOS'ta `coreaudiod` cihaz paylaşımını yönetir, CoreAudio HAL property dinleme ile hot-plug verir (Apple Forums) ama AVAudioEngine aggregate device'ta kanal uyumsuzluğu düşer → **macOS/Linux adapter'ları PLANNED, API olgunlaşmış**. **(4) Desen:** Adapter "uyumsuz arayüzleri eşler" (sourcemaking), Bridge "platform-bağımsız soyutlama × platform-bağımlı uygulama" iki hiyerarşi (sourcemaking/Incus) → **ortak `IAudioBackend` + OS adapter = Bridge+Adapter birleşimi**. **(5) Mismatch:** örnek hızı uyumsuzluğu "garbled audio", pitch/BPM kayması üretir (Samson/PositiveGrid/HomeRecording); çözüm ayar eşleme + donanım tarafında dönüşüm → **resample sınırı kararı gerekli**. |
| Web Search **Uzun Açıklama** | **(a) Neden hazır kütüphaneye tam teslimiyet değil:** üç kütüphane de (PortAudio/RtAudio/miniaudio) "ortak en küçük payda" API verir; buna karşılık CoreMusic'in ihtiyacı **ASIO exclusive kilit + WASAPI Exclusive/Shared geçiş tetiği + ALSA mmap + PipeWire format dönüşümü gibi platform-özel kolları** kontrol etmektir (§1.3 kaynak 1-9 vs 21-27) → mimari: **kütüphane opsiyonel, arayüz zorunlu** (§2.2b — adapter ister elle yazılsın ister kütüphane sarmalansın, `IAudioBackend` değişmez). **(b) Backend sırasının dayanağı:** learn.microsoft.com resmi dokümanı ASIO'yu "harici sürücü kurulumu gerektiren, doğrudan donanım" yolu, WASAPI'yi "daha fazla kontrol/düşük gecikme" yolu olarak tanımlar; pratik ölçüm yazıları ASIO'nun tek haneli ms'i tutturduğunu, WASAPI Exclusive'in "erişilebilir (ek sürücü yok)" avantajını yazar (kaynak 10-16) → `brain.md:862` sırası (**ASIO → WASAPI → Null**) doğrulanır; ADR-017'den **fark**: ADR-017 katman/sınır ve xrun politikası tartışır, bu ADR **adapter seçim zincirini ve OS adapter sorumluluğunu** tartışır. **(c) Platform olgunluğu:** PipeWire 2023'ten beri ana distrolarda (kaynak 23) ve "tek unified API" iddiasını taşır; ALSA tek başına da çalışabilir (k2 spec'i ile uyumlu); macOS'ta hot-plug `kAudioHardwarePropertyDevices` property'si ile standarttır ama AVAudioEngine'in aggregate device hataları (Apple Forums, err=-10875 kanal uyumsuzluğu) yüksek seviye API'nin kırılganlığını gösterir → **adapter mümkün olduğunca HAL/seviye-düşük API'ye yakın durur**. **(d) Desen gerekçesi:** sourcemaking "Adapter retrofitted, Bridge up-front" der; CoreMusic'te durum **up-front** olduğu (henüz kod yok, arayüz baştan kuruluyor) için **Bridge hiyerarşisi + Adapter sarmalayıcı** birlikte doğrudur; CodeSignal/dzone C++ örneğinde saf virtual fonksiyonlu hedef arayüz + adaptee'yi saran adapter aynen `IAudioBackend` + `WasapiBackend/AsioBackend/CoreAudioBackend/AlsaBackend/PipewireBackend` yapısına karşılık gelir. **(e) Mismatch/fallback:** örnek hızı uyumsuzluğu bozuk ses/pitch kayması üretir (kaynak 34-41); PipeWire bunu grafik içinde otomatik çözer, WASAPI/ASIO'da uygulamanın çözmesi gerekir → **karar: cihaz-native hız ≠ içerik hızsa önce cihazı content'e getir (set_rate), olmuyorsa adapter içinde resample, o da olmuyorsa fallback zinciri** (§2.2d). |
| Web Search **Paragraf Veri Uzun** | PortAudio: C, callback+bloklayıcı, Windows/macOS/Linux (portaudio.com, audio-digital) · RtAudio: C++ common API (AlternativeTo, LabSound) · miniaudio: tek dosya public domain, CoreAudio/PulseAudio/JACK/ALSA backend (Handmade Network, linuxvox) · JUCE/libsoundio/SDL alternatif listesi (Tech Kaizen) · WASAPI Exclusive mixer bypass + latency floor ASIO'dan yüksek (soundlatencytest 2-3ms, patchd, undisclosedsounds) · ASIO: native sürücü, exclusive lock, temiz örnek hızı geçişi (asio-renderer, recordingbase) · learn.microsoft.com: exclusive/ASIO sınırlılıkları + WASAPI daha fazla kontrol · ALSA mmap en düşük latency (alsa-native.md:40 ile uyumlu) · PipeWire unified API + otomatik format dönüşümü (pipewire.org, canartuc, r/linuxaudio "2025'te alsa+pipewire") · macOS: coreaudiod tek sunucu, HAL property hot-plug, AVAudioEngine aggregate kanal hatası (Apple Forums) · Adapter = uyumsuz arayüz eşlemesi (sourcemaking, CodeSignal, tutorialspoint) · Bridge = platform-bağımsız × platform-bağımlı iki hiyerarşi (sourcemaking, Incus, modernescpp) · mismatch = garbled/pitch/BPM kayması, çözüm = ayar eşleme + donanım dönüşümü (Samson, PositiveGrid, Cakewalk, HomeRecording) · hot-plug = cihaz ekleme/çıkarma durumunda kesinti (destek makaleleri PLANNED kanıt). **Sonuç: kütüphane = araç, arayüz = karar; sıra ASIO→WASAPI→Null; adapter deseni Bridge+Adapter; mismatch önce cihaz sonra resample sonra fallback.** |
| Web Search **Sonucu** | 1) **Cross-platform kütüphane olgunlaştı** (kaynak 1-9): üçü de callback/I/O soyutlar — ≥2 çapraz kaynak; ama platform-özel kollar için **yetersiz/kalın** olduğu iddiası yalnız bizim mimari çıkarımımız → `⚠️ VERIFICATION REQUIRED` (kod deneyi yok). 2) **Backend sırası doğrulandı** (kaynak 10-16): ASIO = latency/lock/örnek hızı şampiyonu, WASAPI Exclusive = erişilebilir ikinci yol → `brain.md:862` ile ≥2 çapraz kaynak. 3) **Linux/macOS API doğrulandı** (kaynak 21-27): PipeWire unified + otomatik format dönüşümü (resmi + topluluk), ALSA mmap, CoreAudio HAL property hot-plug → ≥2 çapraz kaynak; **AVAudioEngine aggregate riski** tek kaynak (Apple Forums) → açıkça tekil. 4) **Desen doğrulandı** (kaynak 28-33): Bridge up-front + Adapter eşleme → ≥2 çapraz kaynak. 5) **Mismatch sonuçları doğrulandı** (kaynak 34-41): bozuk ses/pitch/BPM + ayar eşleme reçetesi → ≥2 çapraz kaynak; "otomatik hot-plug olay API'si" iddiası **çaprazsız** → `⚠️ VERIFICATION REQUIRED`. **Toplam ~41 adlandırılmış kaynak, 5 sorgu**; sayfa-içi derin tur yapılmadığı için sayısal latency değerleri başlık/özet düzeyindedir (açıkça işaretli). |
| Web Search **Alınan Karar** | **ADR-019 KABUL EDİLİR — TEK Neva Player ÇEKİRDEĞİ + HER OS İÇİN İNCE ADAPTER:** **(A) Kapsam:** Windows (ASIO + WASAPI Exclusive/Shared) · macOS (CoreAudio) · Linux (ALSA + PipeWire) **tek ADR**'de. **(B) Mimari:** tek Neva Player çekirdeği (decode + DSP + durum makinesi — **ADR-017 hard-RT kısıtları bağlayıcı**) + her OS için **ince adapter** (yalnız I/O + cihaz yönetimi); ortak C++ `IAudioBackend` arayüzü (Bridge hiyerarşisi + Adapter sarmalayıcı); **OS-specific kod yalnız adapter altında**; **kural: çekirdek asla OS API'sini doğrudan çağırmaz** (yalnız `IAudioBackend` üzerinden). **(C) Backend sırası:** `brain.md:862` — **ASIO → WASAPI → Null fallback** (Windows); macOS: CoreAudio; Linux: PipeWire → ALSA. macOS/Linux adapter'ları **PLANNED** (kod 0) — dürüst etiket. **(D) Özellikler:** device hot-plug, exclusive↔shared geçiş, sample-rate/bit-depth mismatch (**önce cihaz hızı, sonra adapter resample, sonra fallback** — resample sınırı), hata → fallback zinciri + **kill-switch** (ADR-017 xrun politikası ile uyum). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: cross-platform kütüphaneler (9 kaynak), Windows ASIO/WASAPI (7), CoreAudio/ALSA/PipeWire (7), adapter/bridge deseni (7), mismatch/fallback (8) → **~41 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak dört ana iddiada karşılanır, iki iddia tek kaynak kalıp `⚠️ VERIFICATION REQUIRED` ile işaretlendi. Kod tarafı da aynı resmi verdi: **spec IMPLEMENTED** (k2-surucu 14 dosya, `neva-engine-core.md` RT kuralları, `brain.md:862` fallback), **kod PLANNED** (0 `*.cpp/*.h`, `IAudioBackend` = 0 sonuç) → bu ADR **mimari sözleşmedir, kod taahhüdü değil**; uygulaması §5.1'e bağlıdır. **Kaynak listesi (~41):** 1) portaudio.com — PortAudio resmi · 2) audio-digital.net — PortAudio vs RtAudio · 3) audio-digital.net — PortAudio vs SDL_mixer · 4) AlternativeTo — RtAudio/PortAudio · 5) GitHub LabSound issue #80 · 6) Handmade Network — miniaudio · 7) linuxvox — hafif WAV kütüphaneleri · 8) Tech Kaizen — cross-platform kütüphane listesi · 9) VISION.md/ecosystem/github-referanslari (vault içi, doküman) · 10) learn.microsoft.com — Low Latency Audio (resmi) · 11) pchardwarepro — WASAPI latency ayarları · 12) undisclosedsounds — ASIO vs WASAPI vs CoreAudio · 13) recordingbase — ASIO vs WASAPI · 14) patchd — WASAPI vs ASIO · 15) soundlatencytest — WASAPI/ASIO/CoreAudio latency · 16) asio-renderer/coffeeholictech — ASIO vs WASAPI Exclusive · 17) pipewire.org (resmi) · 18) Reddit r/linuxaudio — alsa+pipewire (2025) · 19) Hacker News — PipeWire · 20) discourse.ardour.org — PipeWire/coreaudiod · 21) canartuc Medium — PipeWire unified stack · 22) supermegaultragroovy — AVAudioEngine/HAL · 23) Apple Developer Forums — aggregate device err=-10875 · 24) YouTube — Mac vs Linux audio · 25) k2-surucu/alsa-native.md (vault) · 26) k2-surucu/pipewire-modern.md (vault) · 27) k2-surucu/core-audio-macos.md (vault) · 28) sourcemaking.com — Adapter · 29) sourcemaking.com — Bridge · 30) tutorialspoint — Adapter · 31) CodeSignal — C++ Adapter · 32) modernescpp — Adapter/Bridge · 33) dzone + Medium + Incus Data — Modern C++ Adapter/Bridge · 34) Microsoft Answers — bit/örnek hızı · 35) Samson — sample rate mismatch · 36) Positive Grid — mismatch onarımı · 37) Cakewalk forum — örnek hızı çakışması · 38) HomeRecording — mismatch sonuçları · 39) soundstagehifi — Windows otomatik örnek hızı · 40) Reddit r/audioengineering · 41) ardop.groups.io — SRC |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-017 (DSP Hard-RT) | Çekirdek callback yolu: `malloc/new/mutex/bloklayıcı I/O/throw` **yasak** (`brain.md:378-380`, `neva-engine-core.md:46-70`); buffer/latency bütçesi ve **xrun politikası** (fade-out → 50ms sessizlik → restart, `brain.md:869`) adapter dahil her katmanı bağlar; kill-switch bu politikayla uyumlu olmak zorunda |
| ADR-004 (Ürün mimarisi / çoklu domain) | Player bir ürün bileşenidir: web/SPA katmanına (ADR-001/006/018) karışmaz; native player ile web footer player (ADR-018) **aynı durum makinesi semantiğini** (STOPPED/PLAYING/PAUSED/LOADING/ERROR) paylaşır ama kod yolları ayrıdır |
| ADR-005 (Doğrulama) | Kod kanıtı olmayan her iddia etiketli: 0 `*.cpp/*.h`, `IAudioBackend` 0 sonuç, hot-plug olay API'si çaprazsız → `⚠️ VERIFICATION REQUIRED`; uydurma dosya/yol yazılmaz |
| ADR-006 (Latency bütçesi) | `<10ms ASIO / <20ms WASAPI` (`.ai/CLAUDE.md:429`) ölçüm kapısı; adapter eklenen her katman bu bütçeyi aşamaz (`underrun = 0` hedefi `⚠️ VERIFICATION REQUIRED` olarak kalır) |
| Domain boundary (`AGENTS.md` §5, §17.7) | `*.cpp/*.h` → **Embedded Engineer** (çekirdek + arayüz); WASAPI/COM detayı → **Windows Software Engineer** (adapter kolu); layer violation → revert + log ERROR |
| In-Place Refactoring | Dosya adları (`asio-drivers.md`, `wasapi-exclusive.md`, `alsa-native.md`, `pipewire-modern.md`, `core-audio-macos.md`, `neva-engine-core.md`, `brain.md`, `.ai/.decisions/index.md`) **onaysız değiştirilemez**; bu ADR yalnız karar yazar |
| Frozen ADR-001-037 dokunulmaz | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — ADR-017 bu kuralın dışındadır çünkü bu ADR yeni karardır |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme |
| REDACTED | Sürücü anahtarı, lisans anahtarı, API anahtarı, cihaz kimlik bilgisi/credential hiçbir koşulda bu ADR'ye yazılmaz |
| Numara kuralı | "Yeni ADR ≥ 088" bu yazımda uygulanmaz: `ADR-019` `.ai/.decisions/index.md:56`'da **rezerve boş slottur** (doldurma, yeni numara tahsisi değil — ADR-017/018 aynı istisnayı kaydetmişti) |

---

## 2. Karar (Decision)

**CoreMusic masaüstü ses yolu üç platformda TEK kararla kurulur: (A) Windows = ASIO + WASAPI Exclusive/Shared, macOS = CoreAudio, Linux = ALSA + PipeWire — hepsi tek ADR kapsamında; (B) mimari = tek Neva Player çekirdeği (decode · DSP · durum makinesi — ADR-017 hard-RT kısıtları bağlayıcı) + her OS için ince adapter (yalnız I/O + cihaz yönetimi), ortak C++ `IAudioBackend` arayüzü üzerinden; OS-specific kod yalnız adapter altında yaşar ve çekirdek asla OS API'sini doğrudan çağırmaz; (C) backend seçim sırası `brain.md:862` ile aynı: Windows'ta ASIO → WASAPI → Null fallback; (D) özellik sözleşmesi: device hot-plug, exclusive↔shared geçiş, sample-rate/bit-depth mismatch (önce cihaz hızı → sonra adapter resample → sonra fallback), hata → fallback zinciri + kill-switch (ADR-017 xrun politikası ile uyum).**

### 2.1 Neden Bu Seçenek?

- **Üç ayrı fork olsaydı iş mantığı üç kez yazılırdı:** decode/DSP/durum makinesi platformdan bağımsızdır (`neva-engine-core.md:12,46-70` RT kurallarında OS geçmiyor) → ortak çekirdek tek kod tabanı, adapter'lar yalnız I/O demek.
- **Adapter çizgisi çekilmezse RT ihlali kaçınılmaz:** çekirdeğin içine OS çağrısı (`CoInitialize`, `snd_pcm_open`, property listener) girerse ya bloklayıcı I/O callback'e sızar (ADR-017 yasağı) ya da `#ifdef` ormanı doğar → "çekirdek OS API'yi **asla** doğrudan çağırmaz" kuralı hem RT hem taşınabilirlik garantisi verir.
- **Sıra tek satıra sığmazdı:** `brain.md:862` fallback'i veriyor ama hata→basamak tablosu, kill-switch ve macOS/Linux kolları yoktu; tek ADR hepsini bağlar (§2.2c).
- **Hazır kütüphane = araç, arayüz = karar:** PortAudio/RtAudio/miniaudio olgun ama ince platform kollarını (ASIO exclusive lock, WASAPI `AUDCLNT_E_DEVICE_IN_USE` → Shared, ALSA mmap, CoreAudio property hot-plug) kontrol etmeyi zorlaştırır (§1.3 kaynak 1-9, 10-16) → `IAudioBackend` sabit kalır, altına ister kütüphane sarmalanır ister elle adapter yazılır (§3 alternatif 2).
- **Kod yokken arayüzü yazmak en ucuz karardır:** 0 `*.cpp/*.h` (§1.1-B) → sözleşme şimdi yazılırsa ilk implementasyon sıfırdan başlamaz ve ADR-017/006 denetlenebilir olur.

### 2.2 Teknik Detaylar

**a) Platform ↔ backend matrisi (tek ADR — 3 platform):**

| OS | Birincil | İkincil | Son çare | Spec dosyası (IMPLEMENTED) | Adapter kodu |
|----|----------|---------|----------|---------------------------|--------------|
| Windows | **ASIO** (exclusive, `asio-drivers.md:12,35`) | **WASAPI Exclusive** (`wasapi-exclusive.md:38`) → **WASAPI Shared** (`wasapi-exclusive.md:126`) | **Null Output** (`brain.md:862`) | `k2-surucu/asio-drivers.md`, `wasapi-exclusive.md` | **PLANNED** (0 kod) |
| macOS | **CoreAudio** (HAL + AudioUnit, `core-audio-macos.md:12`) | CoreAudio aggregate device | Null | `k2-surucu/core-audio-macos.md` | **PLANNED** (0 kod) |
| Linux | **PipeWire** (unified, `pipewire-modern.md:12`) | **ALSA native** (mmap, `alsa-native.md:40`) | Null | `k2-surucu/pipewire-modern.md`, `alsa-native.md` | **PLANNED** (0 kod) |

**b) Ortak arayüz + adapter kuralları (bağlayıcı):**

```cpp
// Kural: çekirdek yalnız bu arayüzü görür. OS API'si YASAK (çekirdek içinde).
class IAudioBackend {                 // Bridge hiyerarşisi + Adapter (§1.3 kaynak 28-33)
public:
  virtual ~IAudioBackend() = default;
  virtual BackendInfo enumerate() = 0;                    // cihaz listesi + hot-plug tespiti
  virtual Result open(const StreamConfig&) = 0;           // sample-rate/bit-depth/channels
  virtual Result start(CallbackFn) noexcept = 0;          // RT callback (ADR-017 kısıtları)
  virtual Result switchMode(Mode) = 0;                    // exclusive <-> shared
  virtual Result setDeviceRate(uint32_t) = 0;             // mismatch: önce cihaz hızı
  virtual void   stop() noexcept = 0;
  virtual void   kill() noexcept = 0;                     // kill-switch (§2.2c)
};
// OS-specific TÜM kod yalnız adapter dosyalarında:
// AsioBackend / WasapiBackend / CoreAudioBackend / AlsaBackend / PipewireBackend / NullBackend
```

| # | Kural | Ayrıntı |
|---|-------|---------|
| 1 | **Çekirdek → OS API yasağı** | Neva çekirdeği (decode/DSP/state machine) yalnız `IAudioBackend` çağırır; `#ifdef _WIN32`/`__APPLE__`/`__linux__` **çekirdekte yasak**, yalnız adapter dosyalarında |
| 2 | **Adapter incedir** | Adapter yalnız: cihaz enumerasyonu, open/close, exclusive/shared geçışı, callback köprüsü, hata çevirisi (OS kodu → `Result`); **DSP/decode/timestamp işi yapmaz** |
| 3 | **Callback köprüsü RT-korumalı** | OS callback'i → `IAudioBackend` callback'i → çekirdek; arada tahsisat/lock **yok** (ADR-017); adapter gerektiğinde önceden tahsisli ring buffer (`neva-engine-core.md:76-80` SPSC) kullanır |
| 4 | **Hata semantiği tek tip** | Tüm adapter'lar aynı `Result` enum'unu döndürür (`OK / DEVICE_LOST / RATE_UNSUPPORTED / BUSY / PERMISSION / FATAL`) → fallback tablosu OS'tan bağımsız çalışır |
| 5 | **Backend seçim** | Başlangıçta OS'a göre sırada dener; Windows: **ASIO → WASAPI Exclusive → WASAPI Shared → Null** (`brain.md:862` + `asio-drivers.md:109` + `wasapi-exclusive.md:126`) |
| 6 | **Sahiplik** | Çekirdek + `IAudioBackend` + tüm adapter'lar `*.cpp/*.h` → **Embedded Engineer**; WASAPI/COM ayrıntı danışmanlığı **Windows Software Engineer** (`AGENTS.md` §5, §15) |

**c) Fallback zinciri + kill-switch (bağlayıcı tablo):**

| Tetik (OS/hata) | Basamak 1 | Basamak 2 | Son çare | Kaynak |
|------------------|-----------|-----------|----------|--------|
| ASIO cihaz kaybı (USB kopması) | **WASAPI Exclusive** | WASAPI Shared | **Null Output** (sessiz, state=ERROR) | `brain.md:862`, `AGENTS.md` §17.6 |
| `ASIOError_InvalidMode` | **WASAPI Shared** | — | Null | `asio-drivers.md:109` |
| `AUDCLNT_E_DEVICE_IN_USE` (exclusive kilit başka uygulamada) | **WASAPI Shared** | — | Null | `wasapi-exclusive.md:126` |
| PipeWire daemon yok/çöktü | **ALSA native (mmap)** | — | Null | `pipewire-modern.md`, `alsa-native.md:12` |
| Cihaz hot-plug (çıkarıldı) | Mevcut stream'i durdur → yeni enumerate → **ilk tercihli cihaza yeniden open** | 3 sn içinde gelmezse Null | kullanıcı bildirimi (state=ERROR) | `core-audio-macos.md:46-57` deseni; **olay API'si** `⚠️ VERIFICATION REQUIRED` |
| Örnek hızı/bit derinliği mismatch | **Cihaz hızını içerik hızına çek** (`setDeviceRate`, `alsa-native.md:55`) | Çekilemiyorsa **adapter içi resample** (sınır: yalnız 44.1↔48 çifti ve 2'nin katı oranlar; kroma/fazı koruyan lineer/faz-kilitli filtre) | Çözülmüyorsa fallback zincirine düş | `pipewire-modern.md:130-133` (otomatik dönüşüm emsal), §1.3 kaynak 34-41 |
| **xrun/underrun (CPU %100)** | **Fade-out → 50ms sessizlik → restart** (ADR-017 politikası) | Aynı hatanın 3 tekrarı → bir üst basamağa fallback | Null + ERROR | `brain.md:869`, ADR-017 |
| **Kill-switch** | Tek çağrı `backend->kill()` → stream anında durur, cihaz kilidi bırakılır, state=STOPPED | Sinyal: `html/data-` karşılığı native tarafta `NevaConfig::killSwitch=true` (config, kod PLANNED) | Uygulama çalışır kalır (Null backend'e düşer) | ADR-017 xrun politikası + bu ADR §2.2c |

**d) Özellik sözleşmesi (bağlayıcı):**

| Özellik | Davranış | Sınır |
|---------|----------|-------|
| **Device hot-plug** | Cihaz ekle/çıkar olayı adapter'da dinlenir; çıkarılan cihaz aktifse stream durur + enumerate → tercih sırasıyla yeniden open | Hot-plug callback'i **RT değil** (control thread); tahsisat yasak değil ama callback'e taşınmaz |
| **Exclusive ↔ Shared** | Exclusive **varsayılan düşük gecikme**; `BUSY/InvalidMode` → Shared'a otomatik geçiş; kullanıcı tercihi config'te (`preferExclusive`) | Geçiş kesintisiz değil: eski stream kapanır → yenisini açılır (≈ tek buffer kesintisi), hata olursa fallback zinciri |
| **Sample-rate / bit-depth mismatch** | Önce cihaz hızı, sonra adapter resample, sonra fallback; **resample sınırı** yukarıdaki tablo | Bit derinliği: 16/24/32f dışına çıkmaz; 32-bit float çekirdek formatı korunur (`brain.md:41`) |
| **Fallback + kill-switch** | Yukarıdaki tablo; tüm geçişler `Result` enum ile, log'a tek satır | xrun politikası ADR-017 ile aynı; kill-switch her an çağrılabilir |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Her OS için ayrı player (fork):** `player-win`, `player-mac`, `player-linux` | Her platformda en doğal API, en yüksek kontrol | Decode/DSP/state machine 3 kez; hata/fallback davranışı ayrışır; bakım 3× | Çekirdek iş mantığı platformdan bağımsız (`neva-engine-core.md:12`); ADR-017 RT kurallarını 3 yerde denetlemek gerekir |
| 2 | **Hazır kütüphaneye tam teslimiyet** (miniaudio veya PortAudio tek başına) | En az kod, tek API, hızlı başlangıç | ASIO exclusive lock / WASAPI `DEVICE_IN_USE` → Shared / ALSA mmap / CoreAudio hot-plug gibi ince kollar ya yok ya da ince ayarsız; kütüphane = tek vendor riski | §1.3 kaynak 1-9: kütüphaneler ortak payda verir; karar `IAudioBackend` ile kilitlenip altına **ister** kütüphane sarmalanabilir (esneklik korunur) — reddedilen teslimiyet, araç kullanımı değil |
| 3 | **Sadece Windows/ASIO** (tek platform, sonra bakarız) | En hızlı ilk teslim (k2 spec'i Windows en dolu) | macOS/Linux spec'leri (`core-audio-macos`, `alsa-native`, `pipewire-modern`) çöpe girer; ürün vizyonu 3 platform diyor | Kullanıcı onayı 3 platform tek ADR; spec dosyaları diskte hazır (§1.1-A) |
| 4 | **Çekirdeğin içine `#ifdef` ile OS kodu** (adapter yok, tek derleme) | Başlangıçta dosya sayısı az | RT ihlali riski (callback'e bloklayıcı OS çağrısı), kod okunmaz, yeni platform eklemek çekirdeği kirletir, domain boundary belirsiz | §1.2 madde 1 + ADR-017 yasağı + `AGENTS.md` §17.7 layer violation; adapter zorunluluğu bu kararın özü |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek kod tabanı:** decode/DSP/durum makinesi tek yerde; 3 platform yalnız I/O adapter'ı paylaşır — yeni platform (ör. PipeWire'ün JIT/Pro Audio modu, CoreAudio AUHAL alternatifi) eklemek = tek adapter dosyası.
- **RT denetlenebilirliği:** çekirdek OS çağrısından yasak olduğundan ADR-017 kısıtları (tahsisat/lock/bloklayıcı I/O) **yerinde denetlenir**; adapter hataları ayrı yüzeyde kalır.
- **Fallback tek tablo:** `brain.md:862` tek satırı + iki spec'teki parça fallback (`asio-drivers.md:109`, `wasapi-exclusive.md:126`) tek zincirde toplanır; hata→basamak her OS için aynı `Result` semantiğiyle çalışır.
- **Özellikler karara bağlandı:** hot-plug, exclusive/shared, mismatch (resample sınırı) ve kill-switch artık yorum değil sözleşme (§2.2d).
- **Spec-kod köprüsü:** diskteki 14 k2-surucu spec'i + `neva-engine-core.md` bu ADR ile bir arayüzün arkasında toplanır; `ADR-006:291`'deki "ADR-019 dosyası yok" hükmü kapanır.

### 4.2 Olumsuz Sonuçlar

- **Adapter katmanı = ek soyutlama:** her çağırmada bir sanal fonksiyon (vtable) ve `Result` çevirisi → ölçüm gerekir (ADR-006 bütçesi; PRF `⚠️ VERIFICATION REQUIRED`).
- **En küçük ortak payda korkusu:** `IAudioBackend` tasarımı yanlış seçilirse platform-özel yetenek (ör. ASIO sample-type, CoreAudio aggregate) sızar → arayüz genişler/kirletilir (risk §4.3).
- **Resample sınırı dar:** yalnız 44.1↔48 ve 2'nin katı oranlar → 88.2/176.4 gibi uçlarda fallback'e düşülür (bilinçli daraltma, sürpriz değil ama kısıt).
- **Uygulama işi duruyor:** kod 0 (§1.1-B) — bu ADR karar metnidir; adapter/çekirdek kodu §5.1 adımlarında ayrıca yazılmalıdır.
- **macOS/Linux kolları doğrulanamadı:** spec var, kod yok; AVAudioEngine aggregate hatası gibi tek-kaynak riskler (§1.3) testte netleşir.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Adapter sızıntısı** — çekirdeğe OS API'si/`#ifdef` sızar (tasarım baskısı) | 3 (olası) | 4 (yüksek) | Kural §2.2b-1 + code review kapısı: çekirdek ağacında `#ifdef`/OS başlık taraması = 0 hedef; ihlal → revert + log ERROR (`AGENTS.md` §17.7) |
| **OS API farklılıkları** — exclusive kilit/hot-plug/örnek hızı semantiği platformda ayrışır, `Result` eşlemesi unutulur | 4 (çok olası) | 3 (orta) | Tek `Result` enum + adapter bazlı hata eşleme tablosu (§2.2b-4); her adapter'ın kendi hata matrisi testte zorunlu |
| **macOS ses oturumu değişimi** — `coreaudiod` cihaz değiştirince/uykudan dönünce stream kapanır | 3 (olası) | 3 (orta) | Hot-plug + property listener → otatik yeniden open (§2.2d); başarısızsa Null + ERROR; **olay API'si tek kaynak** → `⚠️ VERIFICATION REQUIRED`, gerçek cihaz testi §5.1 adım 5 |
| **Linux ses sistemi parçalanmışlık** — distro'da PipeWire yok/eski, PulseAudio hibrit, ALSA doğrudan erişim izni | 4 (çok olası) | 3 (orta) | Sıra: PipeWire → ALSA → Null (`pipewire-modern.md:12` "tek unified" iddiası tek kaynak → testte doğrulanır); `snd_pcm` mmap yolu spec'te hazır (`alsa-native.md:40`) |
| **Latency bütçesi aşımı** — adapter + resample eklenince `<10ms ASIO / <20ms WASAPI` (ADR-006/`.ai/CLAUDE.md:429`) ihlal | 2 (mümkün) | 4 (yüksek) | Resample yalnız mismatch'te devrede; adapter tahsisatsız (önceden tahsisli SPSC, `neva-engine-core.md:76-80`); ölçüm §5.1 adım 6 |
| **Kütüphane/adapter karmaşası** — altta PortAudio/miniaudio sarmalanınca arayüz iki kez soyutlanır | 3 (olası) | 2 (düşük) | Karar: araç = adapter'ın iç detayı; `IAudioBackend` değişmez (§3 alternatif 2 gerekçesi) |
| **Uygulama gecikmesi** — kod 0, §5.1 adımları yapılmazsa ADR kâğıtta kalır | 3 (olası) | 3 (orta) | Adım 1 (arayüz + Null backend) en küçük uygulanabilir dilim; debate ✅ + Arch Lead ⏳ → §7 akışı tamamlanmadan frozen yapılmaz |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-017-dsp-hardware-mode]] | Hard-RT kısıtları + xrun politikası + latency bütçesi çekirdeği **bağlar**; `brain.md:862` fallback zincirinin kaynağı (§1.4, §2.2c) |
| [[ADR-004-multi-domain-spa]] | Ürün mimarisi: native player web katmanıyla (ADR-001/006/018) sınırda, aynı durum makinesi semantiğini paylaşır (§1.4) |
| [[ADR-005-ultrathink-protocol]] | `⚠️ VERIFICATION REQUIRED` standardı — 0 kod, `IAudioBackend` 0 sonuç, hot-plug olay API'si çaprazsız (§1.1-B/C) |
| [[ADR-006-performance-targets]] | `<10ms/<20ms` ölçüm kapısı (§4.3); `ADR-006:291` "ADR-019 dosyası yok" hükmü bu yazım ile kapanır |
| [[ADR-001-vanilla-js-itcss]] | Web sınırı — native player bu ADR'de, web footer player ADR-018'de (kod yolları ayrık) |
| [[../index]] | Satır 56 `[[ADR-019-per-os-neva-player]]` — slug eşleşmesi ✅ (bu dosya rezervasyonu doldurur) |
| [[../../index.md]] | Satır 636 `decisions/accepted/ADR-019-per-os-neva-player` kaydı ✅ |
| [[../../keys.md]] | Satır 254 `ADR-019 \| Neva Player, per-OS \| Audio` ✅ |
| [[../../brain.md]] | Satır 974 `ADR-019 \| Per-OS Neva Player` ✅ · satır 862 fallback zinciri · §7.1-7.2 RT kuralları · satır 218 K2 backend listesi |
| [[../../architecture/k2-surucu/asio-drivers]] | `:12,21,35,109` — ASIO exclusive 0.5ms, 32 sample, Exclusive/Shared, InvalidMode→Shared (§1.1-A, §2.2a/c) |
| [[../../architecture/k2-surucu/wasapi-exclusive]] | `:12,38,126` — Exclusive/Shared, `Initialize()`, `DEVICE_IN_USE`→Shared (§1.1-A, §2.2c) |
| [[../../architecture/k2-surucu/alsa-native]] | `:12,40,55` — tek başına/PipeWire, mmap, `set_rate_near` (§2.2a/d) |
| [[../../architecture/k2-surucu/pipewire-modern]] | `:12,130-133` — unified API, otomatik format dönüşümü (§2.2a/d) |
| [[../../architecture/k2-surucu/core-audio-macos]] | `:12,46-57` — HAL/AudioUnit, `AudioDeviceID` property enumerasyonu (§2.2a/c) |
| [[../../architecture/k3-ses-motoru/neva-engine-core]] | `:12,46-70,76-80` — RT kuralları + SPSC ring buffer (çekirdek, §2.2b-3) |
| [[../../architecture/k0-isletim-sistemi/cross-platform-api]] | K0 çapraz-platform araç envanteri (pthreads/SDL2/libuv — adapter'ın bağlandığı katman) |
| [[../../AGENTS.md]] | §5 `*.cpp → Embedded Engineer`, §6 routing (`C++, ASIO, JUCE, WASAPI → Embedded`), §15 `win-sw`, §17.6 ASIO→WASAPI fallback, §17.7 layer violation, §10.1 eskalasyon, §25.3 frozen |
| [[../../CLAUDE.md]] | 16 Hard Guardrail, Guardrail #16, REDACTED; latency hedefi `:429` |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırması protokolü (diskte VAR ✅) |
| Ek dizin kayıtları | `.ai/architecture/index.md:95` (K0 "ADR-017, ADR-019") · `k0-isletim-sistemi/README.md:393` · `k0-isletim-sistemi/CLAUDE.md:51` · `k0-isletim-sistemi/windows-api.md:258` · `k2-surucu/README.md:234` · `.templates/adr/adr-index.md:90` (`🔴 core`) · `.agents/embedded-engineer.md:30,64,68,117,121` (ADR-019 şartname sahipliği) |
| Kod kanıtları | **YOK → PLANNED:** `**/*.{cpp,h,hpp,cc}` = 0 dosya · kod taraması (`neva/player/WASAPI/CoreAudio/ALSA/PipeWire/PortAudio/RtAudio/miniaudio/IAudioBackend`) = 0 eşleşme · `.ai/log.md:98` "NevaEngine 79KB" iddiası desteksiz (ADR-017 §1.1-B ile aynı) |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Arayüz + Null backend:** `IAudioBackend` başlık dosyası + `Result` enum + `NullBackend` (§2.2b); çekirdek tarafında OS çağrısı olmadığını doğrulayan statik kural (başlık bağımlılığı taraması) | Embedded Engineer | 1 oturum |
| 2 | **Windows adapter kolonu:** `AsioBackend` + `WasapiBackend` (Exclusive/Shared geçiş, `DEVICE_IN_USE`→Shared, `InvalidMode`→Shared) — spec `asio-drivers.md`/`wasapi-exclusive.md` satırlarına birebir | Embedded Engineer + Windows SW Engineer | 3 oturum |
| 3 | **Fallback + kill-switch:** §2.2c tablosunun kodu (hata→basamak, xrun fade-out→restart ADR-017 ile aynı, `kill()`); tüm geçişler `Result` üzerinden | Embedded Engineer | 1.5 oturum |
| 4 | **Mismatch motoru:** `setDeviceRate` → adapter resample (44.1↔48 + 2'nin katı) → fallback; bit derinliği 16/24/32f sınırı | Embedded Engineer | 1.5 oturum |
| 5 | **macOS/Linux adapter'ları (PLANNED → ilk kod):** `CoreAudioBackend` (property listener hot-plug), `PipewireBackend` → `AlsaBackend` (mmap); gerçek cihaz testi (hot-plug, örnekleme değişimi, AVAudioEngine yerine HAL seviyesi) | Embedded Engineer | 3 oturum |
| 6 | **Ölçüm + kapı:** latency `<10ms ASIO / <20ms WASAPI` (ADR-006), adapter + resample ek yükü ölçümü, `underrun` sayacı; `.github/workflows/` bugün 0 dosya → CI kapısı **PLANNED** | QA Engineer + DevOps Engineer | 1 oturum |
| 7 | **Sahiplik + dizin kayıtları:** `.ai/.decisions/index.md:56` satırı bu dosya ile canlanır; `ADR-006:291` düz metin hükmü not edilir (frozen → **dokunulmaz**, yalnız burada bağlanır); broken-link varsa `broken-links-report.md` append | Vault Steward | 0.5 oturum |
| 8 | **Test paketi:** backend seçimi (ASIO yok → WASAPI, kilitleme → Shared), hot-plug çıkar/tak, mismatch (44.1↔48, 88.2 → fallback), kill-switch (anlık stop + kilit bırakma), çekirdek ağaç `#ifdef`=0 denetimi | QA Engineer | 2 oturum |

### 5.2 Geri Dönüş Planı

1. **Karar metni (bu dosya):** karar değişirse **yeni ADR** yazılır (`ADR-088+` serisi), bu dosya `superseded by` bağlanır — metin silinmez (In-Place yasağı).
2. **Arayüz (adım 1):** `IAudioBackend` genişletilebilir ama **daraltılamaz**; vazgeçilirse çekirdek tek backend'e (Null dâhil) sabitlenir → tüm adapter'lar tek commit ile kaldırılır.
3. **Windows adapter (adım 2):** ayrı commit serisi → `git revert` ile ASIO/WASAPI kolu kapanır, çekirdek Null'a düşer (uygulama çalışır kalır).
4. **Fallback/kill-switch (adım 3):** geri alınması **ADR-017 xrun politikasını ihlal eder** → yalnız yeni ADR ile kaldırılabilir; acil durumda kill-switch kendisi geri almanın aracıdır (`kill()` çağrısı → Null).
5. **Resample sınırı (adım 4):** mismatch çözülmüyorsa fallback zincirine düşer → resample devre dışı bırakılabilir, ses kalitesi değil süreklilik bozulur (kabul edilebilir geri dönüş).
6. **macOS/Linux (adım 5):** adapter yokken o platformda **oynatma yok** (dürüst PLANNED durumu) → bu ADR etkilenmez, yalnız §5.1 adımı ertelenir.
7. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (geçmiş satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — debate tamamlandı (2026-09-25) |
| Karar içeriği | Tümü **kullanıcı onaylı** (üst görev kapsamı: 3 platform tek ADR · ortak çekirdek + ince adapter · özellikler · §1.3 araştırması) |
| Beklenen biçim | ADR-004/008/010-018 formatı — 3 tur / 20 persona (**uygulandı: 3/20**) |
| Tur 1 (20 persona) | **Bulgu:** kod **0** — `IAudioBackend` repo'da hiç yok, `*.cpp/*.h` = 0; spec **IMPLEMENTED** (k2-surucu asio/wasapi/alsa/pipewire/core-audio + `neva-engine-core`); 10 rezerve dizin kaydı; **2 iddia tek kaynak** (`⚠️ V.R.`); `brain.md:862` ASIO→WASAPI→Null doğrulandı. Oy: **15 kabul/neutral, 4 uyarı** (QA: sözleşme testi yok · DevOps: Linux distro tespiti · Critic: 2 tek-kaynak iddia + katman ihlali riski). |
| Tur 2 (İtiraz → çözüm) | (1) kod 0 + arayüz bu ADR'de → **`IAudioBackend` sözleşmesi + sözleşme testi iskeleti** → **Şart 1a**; (2) tek kaynaklı 2 iddia → **`⚠️ V.R.` + ikinci kaynak** → **Şart 1b**; (3) çekirdek doğrudan OS API çağırabilir → **katman denetimi** (çekirdek yalnız `IAudioBackend` görür) → **Şart 1c**; (4) Linux parçalanmışlık → **PipeWire-first + ALSA fallback + distro tespiti** (kararda teyit → §2.2a/c). |
| Tur 3 (Oy) | **18 kabul / 2 çekimser / 0 red → KABUL** |
| Tech Lead | **✅** — debate sonrası onay (2026-09-25) |
| Şartlar | **3/3** → §5.4'e madde olarak eklendi, §6'da çapraz kayıtlı |
| Kural | Debate tamamlanmadan bu ADR **frozen yapılmaz**; sonuç §5.3/§5.4/§7.1'e ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir |

### 5.4 Debate Şartları (Kabul Koşulları — 3/3)

> debate **✅ KABUL** (3 tur / 20 persona, 18/2/0 — 2026-09-25) ile bağlayıcı hale geldi; onay §7.1'de Tech Lead **✅** ile kayıtlıdır. Şartlar §5.1 adımlarını ve §2.2 kararlarını **tamamlayıcıdır** (mevcut metin değiştirilmez).

| # | Şart | İçerik (1a-1c dâhil) | Sorumlu | Durum | Kaynak |
|---|------|------------------------|---------|-------|--------|
| 1 | **Sözleşme + sözleşme testi + katman denetimi + V.R. iddialar** | **1a)** `IAudioBackend` sözleşmesi + **sözleşme testi iskeleti** (§2.2b arayüzü; arayüz sözleşmesi test edilmeden çekirdek/adapter kodu yazılmaz) · **1b)** tek kaynaklı 2 iddia → `⚠️ VERIFICATION REQUIRED` + **ikinci kaynak** aranır (§1.1-C hot-plug olay API'si, §1.3 AVAudioEngine aggregate) · **1c)** **katman denetimi** — çekirdek yalnız `IAudioBackend` görür; çekirdek ağacında `#ifdef`/OS başlık taraması = 0 (§2.2b kural 1 + §4.3 adapter sızıntısı mitigasyonu) | Embedded Engineer + QA Engineer | ⏳ PLANNED → §5.1 adım 1, 8 | Tur 1 QA/Critic uyarısı → Tur 2 itiraz 1-3 |
| 2 | **Fallback zinciri + kill-switch entegrasyon testi** | §2.2c tablosunun uçtan uca testi: ASIO yok → WASAPI, exclusive kilitleme → Shared, PipeWire yok → ALSA, mismatch → resample → fallback, xrun fade-out → restart; **kill-switch entegrasyon testi ADR-017 xrun politikası ile uyumlu** (`brain.md:869`, ADR-017) | QA Engineer | ⏳ PLANNED → §5.1 adım 3, 8 | Tur 1 QA uyarısı → Tur 2 itiraz 1 |
| 3 | **OS adapter uçtan uca test planı (3 platform)** | Windows (ASIO + WASAPI Exclusive/Shared) · macOS (CoreAudio hot-plug — HAL seviyesi) · Linux (**PipeWire-first + ALSA fallback + distro tespiti** — Tur 2 itiraz 4 kararı teyit eder, §2.2a/c + §4.3 Linux riski) uçtan uca test planı; gerçek cihaz testi §5.1 adım 5 | QA Engineer + DevOps Engineer (distro tespiti) + Embedded Engineer | ⏳ PLANNED → §5.1 adım 5, 8 | Tur 1 DevOps uyarısı → Tur 2 itiraz 4 |

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — **satır 56** `[[ADR-019-per-os-neva-player]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16, latency hedefi `:429` |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`C++, ASIO, JUCE, WASAPI, hardware → Embedded`), §5 domain boundary (`*.cpp → Embedded Engineer`), §17.6/§17.7 edge case |
| [[../../WORKFLOW.md]] | Debate/onay akışı başlangıcı |
| [[../../brain.md]] | Satır 974 `ADR-019` kaydı ✅ · satır 862 fallback · §7.1-7.2 RT · satır 218 K2 · satır 286 buffer |
| [[../../keys.md]] | Satır 254 ADR-019 keyword eşlemesi ✅ |
| [[../../index.md]] | Satır 636 ADR-019 kaydı ✅ |
| [[../../glossary.md]] | Terim sözlüğü (adapter, backend, hot-plug, resample, kill-switch — ekleme ADR-019 uygulamasıyla) |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| Debate sonucu | §5.3 — debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** (sonuç §7.1'e işlendi) |
| Debate Şart 1 | §5.4 — `IAudioBackend` sözleşmesi + sözleşme testi iskeleti + katman denetimi + V.R. iddialar (1a-1c) |
| Debate Şart 2 | §5.4 — fallback zinciri + kill-switch entegrasyon testi (ADR-017 xrun uyumu) |
| Debate Şart 3 | §5.4 — OS adapter uçtan uca test planı (3 platform: Windows/macOS/Linux) |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16, 7 bölüm + §1.3 9 alan) |
| [[ADR-017-dsp-hardware-mode]] | Hard-RT + xrun + latency bütçesi (dosya diskte VAR ✅) |
| [[ADR-004-multi-domain-spa]] | Ürün mimarisi sınırı (dosya diskte VAR ✅) |
| [[ADR-005-ultrathink-protocol]] | Doğrulama + `⚠️ VERIFICATION REQUIRED` standardı (dosya diskte VAR ✅) |
| [[ADR-006-performance-targets]] | Latency ölçüm kapısı (dosya diskte VAR ✅) |
| [[../../architecture/k2-surucu/asio-drivers]] · [[../../architecture/k2-surucu/wasapi-exclusive]] · [[../../architecture/k2-surucu/alsa-native]] · [[../../architecture/k2-surucu/pipewire-modern]] · [[../../architecture/k2-surucu/core-audio-macos]] | Platform spec'leri (IMPLEMENTED doküman, §1.1-A) |
| [[../../architecture/k3-ses-motoru/neva-engine-core]] | Çekirdek spec'i — RT kuralları + SPSC (IMPLEMENTED doküman) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-019'u sıfırdan yaz"; karar içeriğinin tamamı onaylı) | 2026-09-25 | ✅ |
| Tech Lead | ✅ — debate KABUL (3 tur / 20 persona, 18/2/0) | 2026-09-25 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Debate Kaydına İlişkin Not

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010-018 formatı — 3 tur / 20 persona (**uygulandı: 3/20**) |
| Debate | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-25 (frontmatter `debate` alanıyla aynı); Tur 1 bulgu → Tur 2 itiraz→çözüm (4) → Tur 3 oy: §5.3 |
| Tur 1 | **20 persona** — bulgu: kod **0** (`IAudioBackend` repo'da hiç yok, `*.cpp/*.h` = 0); spec **IMPLEMENTED** (k2-surucu asio/wasapi/alsa/pipewire/core-audio + `neva-engine-core`); 10 rezerve dizin kaydı; **2 iddia tek kaynak** (`⚠️ V.R.`); `brain.md:862` ASIO→WASAPI→Null doğrulandı. Oy: **15 kabul/neutral, 4 uyarı** (QA: sözleşme testi yok · DevOps: Linux distro tespiti · Critic: 2 tek-kaynak iddia + katman ihlali riski). |
| Tur 2 | **İtiraz → çözüm:** (1) kod 0 + arayüz bu ADR'de → `IAudioBackend` sözleşmesi + sözleşme testi iskeleti → **Şart 1a**; (2) tek kaynaklı 2 iddia → `⚠️ V.R.` + ikinci kaynak → **Şart 1b**; (3) çekirdek doğrudan OS API çağırabilir → katman denetimi (çekirdek yalnız `IAudioBackend` görür) → **Şart 1c**; (4) Linux parçalanmışlık → PipeWire-first + ALSA fallback + distro tespiti (kararda teyit → §2.2a/c). |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Şartlar | **3/3** → §5.4 (madde) + §6 (çapraz kayıt): (1) sözleşme + sözleşme testi + katman denetimi + V.R. iddialar, (2) fallback zinciri + kill-switch entegrasyon testi (ADR-017 xrun uyumu), (3) OS adapter uçtan uca test planı (3 platform) |
| Tech Lead | **✅** (2026-09-25) — debate sonrası onay |
| Frozen | Bu ADR **frozen değildir**; debate ✅ + Tech Lead ✅ ama **Arch Lead ⏳** → §7 satır 3 tamamlanmadan `frozen` yapılmaz |
| Kural | Debate tamamlanmadan bu ADR **frozen yapılmaz**; §7'nin üç satırı da ✅ olmadan Active/Frozen olmaz (şablon §4.2) |

---

*ADR-019 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-019 Karar Metni (SSOT) · Mode: Red Team · Human Mode · Truth Mode*
*Last Updated: 2026-09-25*

*ADR-019 debate | 2026-09-25 | ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead ✅ · 3 şart (§5.4) · frozen YOK (Arch Lead ⏳)*
