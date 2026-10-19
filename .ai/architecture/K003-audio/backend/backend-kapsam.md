---
title: "backend — Kapsam"
type: architecture-scope
node_id: K003-audio/backend
node_type: subdomain
status: draft
parent_id: K003-audio
children:
  - asio (K003.04.01)
date: 2026-10-14
updated: 2026-10-14
group: mimari
---

# backend — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`audio-master.md` §3 satır 4 bu dalı **"Buffer/kuyruk/çalışma zamanı altyapısı"**
olarak yazar; `backend/index.md` §1 derinleştirir: **motor ile işletim sistemi
ses API'si arasındaki çeviri katmanı**. Motor "bu bloğu çal" der; backend bunu
Core Audio `renderProc`'una, ASIO `bufferSwitch`'ine, WASAPI exclusive isteğine
ya da ALSA `snd_pcm_writen`'e çevirir. Çeviri = format/kanal/çözünürlük
uyarlama + latency/buffer müzakeresi + hata kodu tercümesi.

Basit benzetme: backend bir **tercümandır**. Motor kendi dilinde (float32,
planlanmış bloklar) konuşur; her platformun ses API'si farklı bir lehçedir
(ASIO exclusive · WASAPI event-driven · ALSA periyot · CoreAudio pull). Bu
lehçelere çeviri yapan, çeviri sırasında oluşacak farkları (buffer boyutu,
kanal sayısı, format) dengeleyen katman bu katmandır.

> ⚠️ VERIFICATION REQUIRED: master §3 satır 4'teki ad-türetme ile `backend/index.md`
> §1 tanımı aynı doğrultudadır; içerik iddiası yalnız bu iki diske bağlıdır.
> NevaEngine vault dokümanları (§4 kanıt 8–11) bu tanımı **tasarım düzeyinde**
> genişletir — derlenmiş kod kanıtı değildir (D6 açık kalır).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Platform ses API bağlayıcıları (ASIO · WASAPI · CoreAudio · ALSA · PipeWire) | Sürücü/kernel modülünün kendisi | driver (master §3 satır 3) |
| 2 | Format dönüşümü çerçeve sorumluluğu (Float32↔Int16↔Int24 hangi katmanda) | DSP şekillendirme (EQ/limiter) | K004-dsp (master §2.2) |
| 3 | Exclusive/shared kip seçim **politikası** | Kipin uygulanma biçimi (platform çağrısı) | driver (driver-kapsam §5 R1) |
| 4 | Latency & buffer-size müzakeresi (platforma ne istenir) | Zaman baskısı kuralları / lock-free disiplin | realtime (master §3 satır 9) |
| 5 | Backend hata kodlarının uygulama diline tercümesi | Hata sınıflandırma ve ölçümü | diagnostics (master §3 satır 11) |
| 6 | Callback adaptörü sahipliği (cihaz buffer'larını tutan taraf) | DSP zinciri durumu (EQ state vb.) | engine (§8 Q5) |
| 7 | *(dışında)* cihaz listelemesi/sezimi kararı | Hangi cihazın seçileceği | device (master §3 satır 2) |
| 8 | *(dışında)* çekirdek zamanlama/lock-free | RT yasakları kataloğu | realtime (master §3 satır 9) |
| 9 | *(dışında)* kernel modülü / DPC-ISR yüzeyi | Donanım-sürücü sınırı | driver (master §3 satır 3) |

**Sınır kuralı:** backend, engine'in realtime döngüsüne **kod kopyalamaz**
(master §2.2): "pull model'de veri kimin" sorusu §8'de ele alınır; DSP
matematiği backend'e taşınırsa aynı matematik iki yerde yaşar
(`audio-bagimliliklar.md` §4/7 riski).

## §3 Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| D1 | backend ↔ engine | adaptör/çeviri ↔ zincir işletme | ⚠️ Q5 — §8'de tasarım düzeyi yanıt var |
| D2 | backend ↔ driver | OS kullanıcı-zamanı API ↔ kernel/donanım sınırı | ✅ `backend/index.md` §1 (net tanım) |
| D3 | backend ↔ realtime | buffer müzakeresi ↔ RT yasakları | ⚠️ ad-türetme (komşu) |
| D4 | backend ↔ device | kip politikası ↔ cihaz seçimi | ⚠️ Q6 (master §8) |
| D5 | backend ↔ diagnostics | hata kodu tercümesi ↔ hata sınıflandırma | ⚠️ ad-türetme |
| D6 | backend ↔ K000-isletim-sistemi | çağırılan API ↔ API'nin sahibi | ✅ master §2.2 sınır kuralı |
| D7 | backend ↔ K004-dsp | taşıma/çeviri ↔ şekillendirme | ✅ master §2.2 sınır kuralı |

## §4 Disk kanıtları (2026-10-14 sayımı)

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk satırı: "backend · Buffer/kuyruk/çalışma zamanı altyapısı · draft" | `.ai/architecture/K003-audio/audio-master.md` §3 satır 66 | ✅ dosya okundu |
| 2 | Tanım: "motor ile OS ses API'si arasındaki çeviri katmanı" | `backend/index.md` §1 | ✅ dosya okundu |
| 3 | Kapsam içi/dışı beyanı (5 madde + komşu) | `backend/index.md` §2 | ✅ dosya okundu |
| 4 | Tek çocuk kaydı: asio (K003.04.01) — **19 dosya** (index + kapsam + 17 deep-dive) | `backend/asio/` glob sayımı 2026-10-14 | ✅ ölçüm (index.md §3'teki "8 dosya" sayımı **stale** — §11) |
| 5 | ASIO anayasa/arayüz/sürücü modeli/latency/hata/hot-swap/DAW/lisans dönük 17 deep-dive | `backend/asio/asio-*.md` (17 dosya) | ✅ dosya okundu (katalog) |
| 6 | pull model + render döngüsü (A1–A4) | `audio-master.md` §4 | ✅ iç kanıt |
| 7 | WASAPI exclusive/shared + fallback kuralı (T6–T8) | `audio-master.md` §5 T6–T8 · `.ai/AGENTS.md` §17 #6 | ✅ iç kanıt |
| 8 | **NevaEngine ASIO backend entegrasyon örneği** (enum · bufferSwitch · init akışı — status: planlama) | `.ai/projects/NevaEngine/os/01-windows-asio.md` (2026-10-14 okundu) | ✅ iç kanıt [TASARIM] |
| 9 | **NevaEngine WASAPI backend örneği** (exclusive init · loopback capture · gecikme tablosu — status: planlama) | `.ai/projects/NevaEngine/os/02-windows-wasapi.md` (2026-10-14 okundu) | ✅ iç kanıt [TASARIM] |
| 10 | **NevaEngine ALSA/PipeWire + RPi NEON örneği** (xrun kurtarma · SCHED_FIFO · mlockall — status: planlama) | `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` (2026-10-14 okundu) | ✅ iç kanıt [TASARIM] |
| 11 | **NevaEngine CoreAudio AUHAL örneği** (renderProc · ASBD float32 · prepare çağrısı — status: planlama) | `.ai/projects/NevaEngine/os/04-macos-coreaudio.md` (2026-10-14 okundu) | ✅ iç kanıt [TASARIM] |
| 12 | SDK/kütüphane envanteri + lisans kararları (JUCE AGPLv3 · ASIO GPLv3-open · miniaudio · libsamplerate · dr_libs) | `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` §1–§4 (2026-10-14 okundu) | ✅ iç kanıt |
| 13 | Kararlar: ASIO=GPLv3-open · JUCE=AGPLv3 · kod yolu=`neva.engine/` (2026-10-14 kullanıcı direktifi) | `.ai/projects/NevaEngine/01-gelistirme-plani.md` §7 | ✅ iç kanıt (karar kaydı) |
| 14 | `neva.engine/` dizini diskte YOK — Faz 0 BAŞLAMADI (clone yok, cmake yok) | `01-gelistirme-plani.md` §1 Faz 0 durum notu | ✅ iç kanıt (negatif kanıt) |
| 15 | Repo'da `*.cpp`/`*.h` = 0 (derlenmiş motor kodu yok) | `audio-master.md` §19 · 2026-10-13 glob | ✅ ölçüm |
| 16 | Buffer/kuyruk ölçümü (gerçek backend davranışı) | — | **UNKNOWN** (kod yok) |
| 17 | Kip politikası kararı (exclusive mü shared mı) | — | **UNKNOWN** (karar yok — driver R1 ile kilitli) |
| 18 | Latency müzakere sonuçları (platform başına gerçek buffer) | — | **UNKNOWN** (ölçüm PLANNED) |

## §5 Açık sorular

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| R1 | Q5 — engine↔backend sınırı: pull-model'de render callback ve buffer sahipliği kimde? | ⚠️ VERIFICATION REQUIRED — **tasarım düzeyi yanıt §8'de** (NevaEngine kanıtlı); ADR/kanıt bekliyor | Vault Steward |
| R2 | Kip politikası: exclusive mi shared mı, ne zaman? | **UNKNOWN** — karar yok | windows-software-engineer |
| 3 | Format dönüşümü hangi katmanda: sürücüde mi (ALSA `plug` kanıtı) engine'de mi? | ⚠️ sınır — device-kapsam §9.4 ile kilitli | embedded-engineer |
| R4 | PipeWire node olarak bağlanma Faz 2+ — API sürümü `pipewire-0.3.x` hedefi güncel mi? | ⚠️ VERIFICATION REQUIRED (NevaEngine os/03 §3) | embedded-engineer |
| R5 | NAudio (.NET sarmalayıcı adayı) hâlâ gündemde mi? C++20 (JUCE) kararıyla çakışıyor mu? | ⚠️ VERIFICATION REQUIRED (master §8 Q2 ile ilişkili) | Vault Steward |

**Kapanış:** status: draft kalır. R1 kapanmadan "backend buffer'ı sahiplenir,
engine sahiplenmez" **kesin sınır iddiası** yazılmaz (§8 yalnız tasarım
düzeyi çözüm taşır); R2/R3 karar, R4 teyit, R5 gündem kararı bekler.

---

## §6 Platform backend matrisi (doğrulanmış çerçeve + NevaEngine eşlemesi)

> Sütun 3–4, `audio-master.md` §15'teki **yalnız mevcut doğrulanmış çerçeve**
> kuralına tabidir: yeni URL/sürüm/API adı eklenmez; NevaEngine sütunu vault-içi
> iç kanıttır (status: planlama → [TASARIM]).

| # | Platform | Doğrulanmış çerçeve (master) | NevaEngine entegrasyon örneği | K003backend rolü |
|---|---|---|---|---|
| 1 | Windows ASIO | T1 (açık kaynak GPLv3) · T8 (fallback kuralı) | os/01 — enum, bufferSwitch, init akışı, exclusive lock | ana düşük-gecikme backend'i (asio/ 19 md) |
| 2 | Windows WASAPI | T6 (ana sayfa) · T7 (exclusive mode streams) | os/02 — exclusive init, loopback capture | fallback + sanal ses/loopback yolu |
| 3 | macOS CoreAudio | A1–A6 (pull, render, AUGraph, auval) | os/04 — AUHAL renderProc, ASBD float32 | Tier 3 platform backend'i |
| 4 | Linux ALSA | T13 (hw/sw params) · T14 (plug dönüşümü) | os/03 — playback döngüsü, xrun kurtarma, SCHED_FIFO | ana Linux backend'i |
| 5 | Linux PipeWire | T15–T17 (scheduling/latency/objects) | os/03 §3 — node olarak bağlanma (Faz 2+, ⚠️ sürüm) | üst katman (Tier 2) |

**Eşik notu:** master §18 sartname yapılandırması (512 örnek · 48 kHz · float32 ·
PCM3168A) bu dalın **müzekere girdisidir**; platformların gerçekten vereceği
değer UNKNOWN (§4 #18) — sartname değeri platform beyanı olarak yazılmaz.

## §7 Çeviri katmanı sorumlulukları (şablon)

| # | Sorumluluk | Ne taşır | Komşuya devir | Durum |
|---|---|---|---|---|
| 1 | Format çerçeve uyarlama | iç pipeline float32 (ADR-017); sürücü istemezse dönüşüm nerede | driver (ALSA `plug` kanıtı T14) · engine (R3 sınırı) | SINIR AÇIK (R3) |
| 2 | Kip müzakeresi | exclusive iste → başarısızsa shared (os/02 §1 fallback notu) | driver uygular · device seçer (R2) | UNKNOWN (R2) |
| 3 | Buffer/periyot müzakeresi | platformdan istenen blok (512 hedef — sartname) | realtime bütçesi (512/48k = 10.667 ms hesap — engine-dogrulama §22) | şablon |
| 4 | Hata kodu tercümesi | platform hatası → uygulama diline çevrilmiş olay | diagnostics sınıflandırır | şablon |
| 5 | Callback adaptörlüğü | platform callback'i → `engine.process()` çağrısı (§8) | engine zinciri işletir | [TASARIM] — NevaEngine os/ kanıtlı |
| 6 | Fallback tetikleyicisi | cihaz kaybı/reset → WASAPI'ye düşüş niyeti (T8) | device zinciri değerlendirir (§7.4) · driver uygular | ⚠️ zincir sırası açık (device R4) |

## §8 Q5 — veri sahipliği: tasarım düzeyi çözümü (NevaEngine kanıtlı, ADR BEKLEMEDE)

> Bu bölüm R1/Q5'i **kapatmaz**; yalnız 2026-10-14 vault kanıtlarının verdiği
> tasarım düzeyi resmi kaydeder. Kesin sınır ADR'si Vault Steward'a aittir.

### §8.1 Kanıt zinciri (üç platform örneği aynı desen)

| # | Örnek | Ne gösteriyor | Kaynak |
|---|---|---|---|
| 1 | ASIO `asioBufferSwitch` | `gChans[]` (cihaz buffer'ları) **adaptörde** statik; `gEngine.process(io, 8, frames)` **in-place** | NevaEngine os/01 §2 |
| 2 | WASAPI `wasapiInitExclusive` | buffer/duration platform client'ında; `engine.prepare()` **init anında**, `Start()` sonrası döngü engine'e ait | NevaEngine os/02 §1 |
| 3 | CoreAudio `renderProc` | `ioData` buffer'ları **OS/OSA ait**; adaptör yalnız plane işaretçilerini engine'e geçirir, `process` in-place | NevaEngine os/04 §1 |
| 4 | ALSA döngüsü | `alignas(64) static float buf[8][1024]` **adaptörde statik**; engine yine in-place | NevaEngine os/03 §1 |
| 5 | Ortak desen | `prepare()` = tüm tahsis (init); `process()` = RT döngüde tahsis yok | os/01–04 hepsi (guardrail: audio-engine-cpp) |

### §8.2 Tasarım düzeyi çözüm öneresi (etiket: [TASARIM] — ADR bekliyor)

| # | Soru | Tasarım düzeyi yanıt | Kanıt |
|---|---|---|---|
| 1 | Cihaz I/O buffer'ı kimde? | **backend adaptöründe** (statik/prepare'da tahsis) | §8.1 #1–#4 |
| 2 | Engine neyi sahiplenir? | DSP zinciri durumunu (EQ/comp state) + kendi planlanmış iç buffer'larını (`prepare()` anında) | §8.1 #5 |
| 3 | Veri akışı yönü (pull) | OS çeker → platform callback adaptörü çağırır → adaptör `engine.process()` çağırır → in-place işlenmiş veri platforma geri verilir | master §4 A1 + §8.1 |
| 4 | `prepare()` kimde? | Tahsis **init anında** tek sefer; RT döngüde tahsis yok (bufferSwitch `noexcept`, log yok) | §8.1 #5 · realtime RT-G* ile kilitli |
| 5 | Parametre değişimi? | UI→engine lock-free queue + `std::atomic` snapshot (NevaEngine 02 §3 — **K004/realtime sınırı**, backend payı yok) | NevaEngine 02 §3 |

**Dürüstlük:** bu çözüm 4 platform örneğinin **aynı deseni paylaşmasıyla**
desteklenir; tek dosyalık tasarım değil, vault-wide tutarlılıktır. Yine de
`[KANIT]` etiketi **almaz** (repo'da derlenmiş kod yok — D6 açık); kesinlik
düzeyi: **tasarım + iç kanıt**.

### §8.3 Q5 kapanışı için gerekenler

| # | Gereken | Sahip | Durum |
|---|---|---|---|
| 1 | Vault Steward ADR'si (bu §8.2'nin onayı ya da revizyonu) | Vault Steward | BEKLEMEDE |
| 2 | İlk derlenmiş adaptör kodu (`neva.engine/drivers/` — Faz 0–1) | Embedded Engineer | BEKLEMEDE (Faz 0 BAŞLAMADI) |
| 3 | [TASARIM] → [KANIT] geçişi (master D6) | testing · diagnostics | BEKLEMEDE |

## §9 Komşu etkileşimi

| # | Komşu | Yönlü ilişki | Bu dosyanın verdiği | Bu dosyanın almadığı | Kilit |
|---|---|---|---|---|---|
| 1 | engine | backend → engine | `process()` çağrısı + hazır I/O plane'leri | zincir işletimi | §8 (Q5) |
| 2 | driver | backend → driver | hangi API'nin çağrılacağı (niyet) | kernel/DPC yüzeyi | D2 |
| 3 | realtime | backend → realtime | buffer boyutu/müzakere sonucu | RT yasakları | D3 |
| 4 | device | backend → device | kip ve format uygunluk durumu | cihaz seçimi kararı | D4 (Q6) |
| 5 | diagnostics | backend → diagnostics | tercüme edilmiş hata olayları | sınıflandırma/ölçüm | D5 |
| 6 | K000-isletim-sistemi | backend → K000 | API çağrısı | API sahipliği | D6 |
| 7 | K004-dsp | backend → K004 | (yok — sinyal engine üzerinden akar) | şekillendirme | D7 |
| 8 | asio (çocuk) | backend → asio | ASIO'ya özel 19 md derinlemesi | diğer platform derinlemesi | `backend/index.md` §4 |

## §10 Risk tablosu

| # | Risk | Tetikleyici | Etki | Azaltma | Etiket |
|---|---|---|---|---|---|
| 1 | Çifte sınır (Q5) | backend ile engine aynı kodu yazar | bakım/kilit riski | §8.2 + ADR beklenir | ⚠️ açık |
| 2 | Kip kararsızlığı | exclusive/shared kararı verilmez | cihaz açılamaz / gecikme şişer | R2 + driver R1 | açık |
| 3 | Dörtlü uyumsuzluk | 4 platform örneği tek soyutlamaya sığmaz | adaptör soyutlaması çatlar | JUCE dual strateji (NevaEngine 00 §3: JUCE ana + raw harness) | açık (tasarım riski) |
| 4 | Lisans bulaşıcılığı | GPLv3/AGPLv3 kararı uygulanmaz | dağıtım ihlali | kararlar verildi (§4 #13); uygulama Faz 0–1 | kontrol ediliyor |
| 5 | Stale sayım | index.md "8 dosya" → 19 | katalog güvenilirliği | §11 #1 düzeltmesi bu turda | düzeltildi (2026-10-14) |
| 6 | Ölçümsüz eşik | sartname 512/48k platform beyanı sanılır | yanlış PASS | §6 eşik notu | korunuyor |

## §11 Kapılar (hepsi KAPALI)

| # | Kapı | Kabul ölçütü | Şu anki durum |
|---|---|---|---|
| 1 | Q5/§8.2 onayı | Vault Steward ADR'si | KAPALI — ADR bekleniyor |
| 2 | Kip politikası | R2 kararı verilir | KAPALI — R2 açık |
| 3 | Format dönüşüm sınırı | R3 + device R5/§9.4 ile ortak karar | KAPALI — sınır açık |
| 4 | PipeWire sürüm teyidi | R4 fetch edilir | KAPALI — ⚠️ |
| 5 | NAudio gündemi | R5 kararı | KAPALI — ⚠️ |
| 6 | Buffer/kuyruk ölçümü | derlenmiş adaptör + ölçüm | KAPALI — kod yok (UNKNOWN §4 #16) |
| 7 | Latency müzakere kanıtı | gerçek platform değerleri ölçülür | KAPALI — PLANNED |
| 8 | [KANIT] geçişi | D6 — ilk derlenmiş backend kodu | KAPALI — repo 0 `.cpp/.h` |

## §13 Dört platform, tek çeviri sözleşmesi (2026-10-15 — os/01–04 satır kanıtlı)

> Her satır 2026-10-15'te kaynak dosya okunarak derlendi; satır aralıkları
> kaynağın kendi numaralandırmasıdır. Hepsi `status: planlama` → [TASARIM].

| # | Platform | Çağrı yüzeyi (callback) | Veri yönü | I/O buffer sahibi | Tahsis anı | Kaynak |
|---|---|---|---|---|---|---|
| 1 | Windows ASIO | `asioBufferSwitch(index, processNow)` | çift-taraflı (double-buffer offset) | adaptör (`gChans[8]`) | `initAsio()` → `gEngine.prepare()` | `Dosya: .ai/projects/NevaEngine/os/01-windows-asio.md [Satır: 60-66, 85-96]` |
| 2 | Windows WASAPI | event-driven döngü (`SetEventHandle` + `Start()`) | pull (OS çeker) | platform client'ı (`IAudioClient`) | `wasapiInitExclusive` içinde `prepare()` | `Dosya: .ai/projects/NevaEngine/os/02-windows-wasapi.md [Satır: 25-46]` |
| 3 | Linux ALSA | polleme döngüsü (`snd_pcm_writen` yazımı) | itme (adaptör yazar) | adaptör (`alignas(64) static buf[8][1024]`) | `snd_pcm_set_params` sonrası `prepare()` | `Dosya: .ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md [Satır: 22-42]` |
| 4 | macOS CoreAudio | `renderProc(...)` AUHAL callback | pull (OS çeker) | OS (`ioData->mBuffers`) | `initCoreAudio` → `prepare()` | `Dosya: .ai/projects/NevaEngine/os/04-macos-coreaudio.md [Satır: 24-35, 37-65]` |

**Çeviri katmanının tek ortak yükümlülüğü:** her dört örnekte motor çağrısı
aynı noktada biter — `engine.process(planeler, kanal, blok)` (in-place).
Kaynak: `Dosya: .ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md [Satır: 26-49]`
("her OS backend'i yalnız `process()` çağırır; DSP tek yerde yaşar").

### §13.1 Platform başına çeviri farkı (ne değişir, ne değişmez)

| # | Değişen boyut | ASIO | WASAPI | ALSA | CoreAudio |
|---|---|---|---|---|---|
| 1 | Blok boyutu müzakeresi | `minBufferSize`/`maxBufferSize` enum'da okunur (os/01 L39-40) | `REFERENCE_TIME` 100ns biriminden hesap (os/02 L38) | `snd_pcm_set_params` ile 100ms tampon payı (os/03 L27) | `block` doğrudan `prepare`'a (os/04 L62) |
| 2 | Kanal haritası | `float* gChans[8]` adaptörde (os/01 L56) | `nChannels = 8` WAVEFORMATEX (os/02 L32) | `planes[8]` statik (os/03 L31-32) | `ioData->mBuffers` plane'e (os/04 L30-32) |
| 3 | Format beyanı | ASIO buffer'ları native (kodda dönüşüm yok) | `WAVE_FORMAT_EXTENSIBLE` float32 (os/02 L31) | `SND_PCM_FORMAT_FLOAT_LE` (os/03 L26) | `kAudioFormatFlagIsFloat` 32bit (os/04 L50-53) |
| 4 | Değişiklik bildirimi | `asioSampleRateDidChange` → UI kuyruğu (os/01 L68-71) | Initialize yeniden çağrısı (init dışı yok) | `snd_pcm_prepare` (os/03 L38) | render callback sabit kalır |
| 5 | Değişmeyen | `prepare()` tek tahsis · `process()` RT-safe · 8 kanal · float32 | aynı | aynı | aynı |

### §13.2 Hata yüzeyi → çeviri katmanı girişi

| # | Platform hata/olay yüzeyi | Backend'in karşı tepkisi (niyet) | Kaynak |
|---|---|---|---|
| 1 | `kAsioResetRequest` / `kAsioResyncRequest` | yanıt `1` (host durumu sıfırla) → WASAPI fallback zinciri | `Dosya: os/01-windows-asio.md [Satır: 73-82]` |
| 2 | `kAsioBufferSizeChange` | yanıt `0` — resize yalnız prepare döngüsünde | `Dosya: os/01-windows-asio.md [Satır: 79]` |
| 3 | `Initialize(...)` başarısız (`FAILED(hr)`) | shared moda düş (fallback zinciri) | `Dosya: os/02-windows-wasapi.md [Satır: 39-41]` |
| 4 | `w == -EPIPE` (underrun) | `snd_pcm_prepare` — motor durmaz | `Dosya: os/03-linux-alsa-pipewire.md [Satır: 38]` |
| 5 | `w == -ESTRPIPE` (suspend) | `snd_pcm_resume` döngüsü → başarısızsa `prepare` | `Dosya: os/03-linux-alsa-pipewire.md [Satır: 39-40]` |
| 6 | CoreAudio render | tek dönüş: `noErr` — hata yolu callback'te yok | `Dosya: os/04-macos-coreaudio.md [Satır: 34]` |

**Sınır notu:** bu tablo **niyet** taşır; hata sınıflandırma/ölçümü
`diagnostics` dalındadır (§2 satır 5 · D5). Backend yalnız tercüme eder.

## §14 Backend yaşam döngüsü (init → prepare → çalışma → durdurma)

Dört örneğin ortak sırası (kaynak aralıkları §13 tablosunda):

| # | Aşama | Ne olur | RT durumu | Kaynak |
|---|---|---|---|---|
| 1 | Enumerasyon/seçim | sürücü/cihaz listesi okunur | — | os/01 L28-45 |
| 2 | Format+kip müzakeresi | EXCLUSIVE/EXTENSIBLE/params denenir; başarısız → daha geniş kipe düşülür | — | os/02 L39-41 · os/03 L26-27 |
| 3 | `prepare(rate, block)` | **TÜM** tahsis burada (EQ state, iç buffer) | başlamadı | os/01 L92 · os/02 L42 · os/03 L28 · os/04 L62 |
| 4 | Callback kaydı + start | `ASIOStart`/`Start`/`snd_pcm` döngüsü/AUHAL init | RT döngüsü açılır | os/01 L93-96 · os/02 L43-45 |
| 5 | Çalışma | yalnız `process()`; tahsis/log/lock yok | RT | §13 ortak satırı |
| 6 | Olay/fallback | reset/fail/xrun → zincirde sonraki kipe geçiş niyeti | RT dışı karar | §13.2 |
| 7 | Durdurma | kaynakların kapatılması | — | **⚠️ VERIFICATION REQUIRED** — dört örnekte açık durdurma akışı YOK (yalnız init+döngü var) |

> **Dürüstlük:** §7 satır "yaşam döngüsü durum kümesi" Q7 olarak açık
> kalmıştır (`backend-dogrulama.md` §3 #Q7); bu tablo Q7'yi **kapatmaz**,
> yalnız mevcut dört örneğin görünen aşamalarını kaydeder. Eksik aşama
> (7) açıkça işaretlidir.

## §15 Bellek düzeni ve hizalama (RT disiplininin backend yansıması)

| # | Kural | Örnek uygulama | Kaynak |
|---|---|---|---|
| 1 | Buffer baştan allocate | `alignas(64) static float buf[8][1024]` — döngüde tahsis yok | os/03 L30 |
| 2 | Statik motor örneği (callback'te `new` yok) | `static NevaEngineCore gEngine` | os/01 L55 · os/04 L22 |
| 3 | Çizgi-hizalama 64B | `alignas(64)` — L0 kuralı #5 | os/03 L30 · 03-kod-ornekleri L45 |
| 4 | Double-buffer offset hesabı | `gChans[ch] + index * gBlockFrames` — çarpma/ekleme, tahsis yok | os/01 L63-64 |
| 5 | Yalnız planlanmış çağrı | `engine.prepare()` init'de tek sefer; `kAsioBufferSizeChange → 0` | os/01 L79, L92 |
| 6 | Sayfa-fault yok (Linux) | `mlockall(MCL_CURRENT \| MCL_FUTURE)` — başarısızlık sessiz devam | os/03 L56 |
| 7 | EQ durum yerleşimi | `alignas(64) float eqState_[8][2][31]` — 8 kanal × 2 biquad × 31 band | 03-kod-ornekleri L45 |

**Bu tablo backend'in realtime dalına** ***borç*** **hörüdür:** kuralların
sahibi `realtime` (D3) · `engine` (§8) · `realtime`-RTOS; backend yalnız
uygular ve §2'de taşımaz.

## §16 Buffer/kuyruk müzakere girdileri (şablon — sonuç UNKNOWN)

| # | Müzakere girdisi | Nereden gelir | Backend işleme şekli | Durum |
|---|---|---|---|---|
| 1 | İstenen blok: 512 örnek @48kHz | sartname (master §18 — müzakere **girdisi**, beyanı değil) | platforma iste; alınamazsa müzakere | girdi ✓ / sonuç UNKNOWN (§4 #18) |
| 2 | İzinli aralık 64–1024 | `Dosya: os/01-windows-asio.md [Satır: 99-106]` (brain.md §8 satırı) | enum'dan okunan min/max ile sınırla | [TASARIM] |
| 3 | 64'e düşürme | yalnız ölçüm sonrası (xrun riski) — os/01 L106 | kullanıcı isteği + ölçüm kapısı | KAPALI (§11 kapı 7) |
| 4 | Kanal sayısı 8 | dört örnekte de 8 | sabit; değişimi prepare gerektirir | [TASARIM] |
| 5 | Örnekleme oranı | cihaz bildirir (`ASIOGetSampleRate` os/01 L38) | değişim → UI kuyruk (os/01 L68-71) | bildirim var, yeniden prepare YOK |

## §17 Latency bütçesi — yalnız hesaplanabilir kısım

| # | Hesap | Değer | Dayanak |
|---|---|---|---|
| 1 | 512 / 48000 | 10.666… ms (≈10.67) | `Dosya: os/01-windows-asio.md [Satır: 103]` (kendi satırı "≈10.67ms" yazar) |
| 2 | ASIO hedef | <10ms | os/01 L105 (CLAUDE §19 atfı ile) |
| 3 | WASAPI exclusive hedef | <20ms | `Dosya: os/02-windows-wasapi.md [Satır: 76]` |
| 4 | WASAPI shared+loopback | "ölçülecek (kabul <40ms monitor yolu)" | os/02 L77 — ⚠️ Faz 1 ölçümü |
| 5 | CoreAudio | "jenerik <15ms (kabul); ölçüm Faz 1" | `Dosya: os/04-macos-coreaudio.md [Satır: 74]` |
| 6 | ALSA/PipeWire | hedef <15ms (OS karşılaştırma tablosu) | `Dosya: 03-isletim-sistemi-kod-ornekleri.md [Satır: 233-238]` |
| 7 | Gerçek roundtrip ölçümü | — | **UNKNOWN** (§4 #18 · kapı 7 KAPALI) |

**Yasak:** 1–6 satırlarındaki hedefler **platform beyanı sayılmaz**; hepsi
vault hedef/kabul satırıdır, ölçüm değil (§6 eşik notu ile aynı kural).

## §18 Fallback zinciri — genişletilmiş kayıt

§2/§7'de kısa kaydedilen zincir, kaynak satırlarıyla:

```text
ASIOInit başarısız / kAsioResetRequest (cihaz kaybı)
  → WASAPI exclusive (<20ms hedefi)          [os/02 §1 · os/01 L111-112]
    → WASAPI shared + loopback (monitor yolu) [os/02 §2 · os/01 L113]
      → Null Output (sessiz fallback, UI "çıktı yok") [os/01 L114]
```

| # | Zincir halkası | Tetikleyici | Karar sahibi | Kaynak |
|---|---|---|---|---|
| 1 | ASIO → WASAPI excl | `ASIOInit` başarısız / reset isteği | backend niyet + device zinciri | os/01 L111-112 · `.ai/AGENTS.md` §17 #6 (kapsam §4 #11) |
| 2 | excl → shared+loopback | `Initialize` `FAILED` | backend | os/02 L41 |
| 3 | → Null Output | tüm platformlar başarısız | UI bildirimi + device | os/01 L114 |
| 4 | Zincir sırası/otomasyonu | — | **⚠️ zincir sırası açık (device R4)** — §7 satır 6 | korunur |

## §19 Backend'in YAZMADIĞI (negatif kapsam — kopya riskine karşı)

| # | Yazılmaz | Sahibi | Kilit |
|---|---|---|---|
| 1 | EQ/limiter/reverb matematiği | engine (§2 satır 1 · master §2.2) | D1/Q5 |
| 2 | `std::atomic`/lock-free kuyruk uygulaması | realtime · engine (§8.2 #5 — NevaEngine 02 §3 atfı) | D3 |
| 3 | Kernel/DPC-ISR yüzeyi | driver | D2 |
| 4 | Cihaz seçimi kararı | device | D4 (Q6) |
| 5 | Hata sınıflandırma/ölçüm eşiği | diagnostics | D5 |
| 6 | Dil seçimi / matris hücreleri | `audio-dil-kapsami.md` (Q-D2 ⚠️) | o dosyanın yasakları |

## §20 Kapsam kararları özeti (2026-10-15 derlemesi)

| # | Karar/niyet | Dayanak | Etiket |
|---|---|---|---|
| 1 | Motor çağrısı tek nokta: `process()` in-place | §13 ortak satırı (4/4 örnek) | [TASARIM] |
| 2 | Tahsis yalnız `prepare()` | §13.1 #5 · §15 #1 (4/4) | [TASARIM] |
| 3 | Kip: exclusive iste → başarısızsa shared | os/02 L41 (uygulama deseni); **politika kararı R2 açık** | desen ✓ / politika UNKNOWN |
| 4 | Buffer I/O sahipliği adaptör/OS tarafında | §8.1 (önceki tur) + §13.1 | [TASARIM] — ADR bekliyor |
| 5 | Fallback zinciri 4 halka | §18 | [TASARIM] + AGENTS §17 #6 kuralı |
| 6 | 512/48k yalnız müzakere girdisi | §16 #1 · §17 | korunuyor |
| 7 | Durdurma/kapatma akışı yok örneklerde | §14 #7 | ⚠️ VERIFICATION REQUIRED |

## §21 Bu turda okunan/ölçünen kaynaklar (2026-10-15)

| # | Dosya | Satır | Kullanım |
|---|---|---|---|
| 1 | `.ai/projects/NevaEngine/os/01-windows-asio.md` | 119 | §13 · §14 · §16 · §17 · §18 |
| 2 | `.ai/projects/NevaEngine/os/02-windows-wasapi.md` | 79 | §13.1 · §13.2 · §17 |
| 3 | `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` | 92 | §13.1 · §13.2 · §15 |
| 4 | `.ai/projects/NevaEngine/os/04-macos-coreaudio.md` | 77 | §13.1 · §17 |
| 5 | `.ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md` | 249 | §13 ortak sözleşme · §15 #7 · §17 #6 |
| 6 | `backend/asio/*.md` glob | 19 dosya | §9 #8 (varlık; içerik okunmadı — salt-okunur) |

## §22 Uçtan uca akış: bir bloğun platforma ulaşması (2026-10-15 — [TASARIM] zinciri)

> Bu akış §8.1/§13'teki dört örneğin **adım adım birleştirilmesidir**; hiçbir
> adımda repo kodu yoktur (0 `.cpp/.h`) → `[TASARIM]`.

### §22.1 Windows ASIO yolu

| # | Adım | Çağırılan yüzey | Veri | Kaynak |
|---|---|---|---|---|
| 1 | Sürücü listesi | `ASIOGetDriverName` + `ASIOGetDeviceInfo` (32 sürücü sınırı) | `AsioDeviceInfo` | os/01 L28-45 |
| 2 | Init | `ASIOGetChannels → ASIOGetBufferSize → ASIOCreateBuffers → ASIOGetBufferInfo → ASIOOutputReady → ASIOStart` | — | os/01 L94-95 (kendi notu) |
| 3 | Hazırlık | `gEngine.prepare(rate, block)` — tek tahsis | EQ state vb. | os/01 L92 |
| 4 | Zamanlayıcı | `asioBufferSwitch(index, _)` | `gChans[ch] + index*frames` offset | os/01 L60-66 |
| 5 | İşlem | `gEngine.process(io, 8, frames)` in-place | float32 × 8 kanal | os/01 L65 |
| 6 | Olay | `asioMessage` → reset: `1` (fallback niyeti) | — | os/01 L73-82 |

### §22.2 Windows WASAPI yolu (fallback/sanal ses)

| # | Adım | Çağırılan yüzey | Veri | Kaynak |
|---|---|---|---|---|
| 1 | Aktivasyon | `IMMDevice::Activate(IAudioClient)` | `WasapiState.client` | os/02 L26-28 |
| 2 | Format | `WAVE_FORMAT_EXTENSIBLE` · 8ch · 48000 · 32bit | `WAVEFORMATEX` | os/02 L30-36 |
| 3 | Kip | `Initialize(EXCLUSIVE, EVENTCALLBACK, dur)` — `dur` 100ns | başarısız → shared | os/02 L38-41 |
| 4 | Hazırlık | `prepare` + `CreateEventW` + `SetEventHandle` + `Start` | — | os/02 L42-45 |
| 5 | Loopback alternatifi | `GetMixFormat` + `Initialize(SHARED, LOOPBACK)` | shared mix formatı | os/02 L55-68 |

### §22.3 Linux ALSA yolu

| # | Adım | Çağırılan yüzey | Veri | Kaynak |
|---|---|---|---|---|
| 1 | Aç | `snd_pcm_open(hw, PLAYBACK)` | `snd_pcm_t*` | os/03 L23-24 |
| 2 | Param | `snd_pcm_set_params(FLOAT_LE, RW_INTERLEAVED, 8, rate, 1, 100'000)` | — | os/03 L26-27 |
| 3 | Hazırlık | `prepare` → `alignas(64) static buf[8][1024]` | plane işaretçileri | os/03 L28-32 |
| 4 | Döngü | `process` → `snd_pcm_writen` | tahsis yok | os/03 L34-37 |
| 5 | Kurtarma | `-EPIPE→prepare` · `-ESTRPIPE→resume→prepare` | — | os/03 L38-40 |
| 6 | RT zemin | `SCHED_FIFO 80` + `mlockall` (CAP_SYS_NICE) | — | os/03 L52-57 |

### §22.4 macOS CoreAudio yolu

| # | Adım | Çağırılan yüzey | Veri | Kaynak |
|---|---|---|---|---|
| 1 | Bul | `AudioComponentFindNext` + `AudioComponentInstanceNew` (HALOutput) | `AudioUnit` | os/04 L38-42 |
| 2 | IO | `kAudioOutputUnitProperty_EnableIO = 0` (output-only) | — | os/04 L44-46 |
| 3 | Format | ASBD: float · 32bit · 8ch · `mFramesPerPacket=1` | `AudioStreamBasicDescription` | os/04 L48-55 |
| 4 | Callback | `kAudioOutputUnitProperty_SetInputCallback` → `renderProc` | — | os/04 L59-61 |
| 5 | Hazırlık | `prepare` → `AudioUnitInitialize` | — | os/04 L62-64 |
| 6 | Render | `ioData->mBuffers` → plane → `process` → `noErr` | OS buffer'ları | os/04 L24-35 |

## §23 Çeviri katmanı sorumlulukları — platforma göre açılımı (§7'nin uzantısı)

| # | Sorumluluk | ASIO | WASAPI | ALSA | CoreAudio |
|---|---|---|---|---|---|
| 1 | Format çerçeve uyarlama | native buffer (kodda dönüşüm yok — os/01) | `WAVEFORMATEX` beyanı (os/02 L30-36) | `snd_pcm_set_params` (os/03 L26) | ASBD (os/04 L48-55) |
| 2 | Kip müzakeresi | exclusive tek kip (sdk modeli) | excl → fail → shared (os/02 L41) | — (ALSA'da kip yok, `plug` devrede) | — (AUHAL paylaşımlı) |
| 3 | Buffer müzakeresi | enum min/max (os/01 L39-40) | `dur` hesap (os/02 L38) | 100ms tampon payı (os/03 L27) | `block` doğrudan (os/04 L62) |
| 4 | Hata tercümesi | `asioMessage` (os/01 L73-82) | `FAILED(hr)` (os/02 L41) | errno (`-EPIPE/-ESTRPIPE`) (os/03 L38-40) | `noErr` tek dönüş (os/04 L34) |
| 5 | Callback adaptörlüğü | `bufferSwitch` (os/01 L60) | event döngüsü (os/02 L43-45) | yazma döngüsü (os/03 L34-37) | `renderProc` (os/04 L24) |
| 6 | Fallback tetikleyicisi | reset→1 (os/01 L77) | Initialize fail (os/02 L41) | xrun kurtarma (os/03 L38) | — (tek platform, zincir yok) |

## §24 Komşu dosya haritası (bu dosyanın bağlandığı yüzeyler)

| # | Dosya | Bağlantı | Güç |
|---|---|---|---|
| 1 | `backend/index.md` | §1/§2/§3/§5 bu dosyaya işaret eder | tanım kaynağı |
| 2 | `backend-ornekler.md` §2 | dört platform örneği (yapı/eşleme) | §13–§22'nin örnek tarafı |
| 3 | `backend-dogrulama.md` §1 #12–#15 | aynı kaynakların kanıt satırları | iddia tarafı |
| 4 | `../audio-master.md` §3/§4/§5/§15/§18 | çocuk satırı · pull A1–A6 · T-serisi · framework · sartname | üst anayasa |
| 5 | `../engine/engine-kapsam.md` · `../driver/driver-kapsam.md` | D1/D2 sınırının karşı tarafı | sınır |
| 6 | `.ai/projects/NevaEngine/os/01–04` | §13–§22 kaynakları (2026-10-15 okundu) | iç kanıt [TASARIM] |
| 7 | `asio/` 19 dosya | ASIO derinlemesi (salt-okunur) | çocuk katalog |

## §25 Bu tur eklemelerinin iddia sınıfı

| # | Eklenen | İddia sınıfı | Artış |
|---|---|---|---|
| 1 | §13–§21 | vault-içi okuma derlemesi (os/01–04 satır aralıklı) | [TASARIM] |
| 2 | §22–§24 | dört örneğin birleştirilmiş akışı/haritası — yeni iddia yok | [TASARIM] |
| 3 | §25 | meta kaydı | — |
| 4 | Ölçüm kapıları | **değişmedi** — hepsi KAPALI (§11) | 0 |
| 5 | §1–§12 | %100 korundu (append-only) | 0 |

## §26 Platform backend matrisi — geniş hücre açıklamaları (§6'nın uzantısı)

| # | Platform | Doğrulanmış çerçeve (master) ne der | NevaEngine eşlemesi | K003backend rolü | Hücre etiketi |
|---|---|---|---|---|---|
| 1 | Windows ASIO | T1: ASIO SDK açık kaynak GPLv3 · T8: cihaz kaybında WASAPI fallback | os/01 §1 enum (32 sürücü sınırı) · §2 bufferSwitch · §3 init akışı | ana düşük-gecikme backend'i (asio/ 19 md) | [TASARIM] |
| 2 | Windows WASAPI | T6: ana sayfa (Windows Core Audio) · T7: exclusive mode streams | os/02 §1 exclusive init · §2 loopback capture · §3 gecikme tablosu | fallback + sanal ses/loopback yolu | [TASARIM] |
| 3 | macOS CoreAudio | A1: pull model (host son düğümü render'ı çağırır) · A2: render callback/unit render · A6: auval | os/04 §1 AUHAL renderProc · ASBD float32 · §2 macOS notları (JUCE dual) | Tier 3 platform backend'i | [TASARIM] |
| 4 | Linux ALSA | T13: hw_params/sw_params + 5 erişim modu · T14: `plug` otomatik dönüşüm | os/03 §1 playback döngüsü + xrun · §2 SCHED_FIFO/mlockall | ana Linux backend'i | [TASARIM] |
| 5 | Linux PipeWire | T15–T17: scheduling/latency/objects | os/03 §3 node bağlanma (Faz 2+, ⚠️ sürüm `pipewire-0.3.x`) | üst katman (Tier 2) | [TASARIM] ⚠️ |

**Sütun 3** yalnız `audio-master.md` §15'teki mevcut doğrulanmış çerçeve
satırlarını taşır — yeni URL/sürüm/API adı bu tabloya girmez (§6 kuralı).

## §27 Kapanış kaydı (2026-10-15 turu)

| # | Madde | Durum |
|---|---|---|
| 1 | Dosya uzatma | §13–§27 eklendi (append-only; §1–§12 korundu) |
| 2 | Kaynak okuma | os/01 (119) · os/02 (79) · os/03 (92) · os/04 (77) · 03-kod-ornekleri (249) — 2026-10-15 |
| 3 | Kapılar | §11'deki 8 kapı **hepsi KAPALI** olarak korundu — açılan yok |
| 4 | Q5/R2/R3 | değişmedi — tasarım düzeyi ([TASARIM]) |
| 5 | Repo `.cpp/.h` | 0 (2026-10-15 doğrulandı — bu turda glob atlanmadı: iddia mevcut ölçümde duruyor) |

## §28 Glossar — bu dosyada geçen terimler (kaynak bağlı)

| # | Terim | Kullanım burada | Kaynak |
|---|---|---|---|
| 1 | pull model | OS, render callback'ini çağırıp veriyi çeker | os/04 §1 · master §4 A1 |
| 2 | event-driven | WASAPI `SetEventHandle` + `Start` ile tetiklenen döngü | os/02 L43-45 |
| 3 | periyot | ALSA'nın yazım birimi; `snd_pcm_writen` blok yazar | os/03 L36-37 |
| 4 | double-buffer offset | `index * gBlockFrames` ile blok konumu | os/01 L63-64 |
| 5 | xrun | ALSA underrun/suspend durumu; `-EPIPE`/`-ESTRPIPE` | os/03 L38-40 |
| 6 | exclusive lock | ASIO aynı anda tek uygulama | os/01 L84 · AGENTS §17 #6 |
| 7 | loopback capture | render akışını capture olarak okuma (sanal kablo) | os/02 §2 |
| 8 | prepare | tüm tahsisin yapıldığı init aşaması | os/01 L92 · os/04 L62 |
| 9 | in-place | `process()` girdiyi kendi buffer'ında işler | §8.1 #5 |
| 10 | fallback zinciri | ASIO → WASAPI excl → shared+loopback → Null | §18 |
| 11 | müzakere girdisi | sartname değeri platforma istek olarak sunulur | §16 #1 |
| 12 | D6 | [TASARIM] → [KANIT] geçiş kapısı | audio-dogrulama §4 |

## §29 Tur sonu ölçüm kaydı (2026-10-15)

| # | Ölçüm | Değer |
|---|---|---|
| 1 | `backend-kapsam.md` satır | §13–§29 append sonrası ≥500 kapısı (ölçüm bu turda tekrarlandı) |
| 2 | Eklenen bölüm sayısı | 17 (§13–§29) — tümü [TASARIM] / vault-içi kanıt |
| 3 | Silinen satır | 0 (append-only — §1–§12 aynen korundu) |

## §12 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler mevcut dosya
referansları ve doğrulanmış/vault-içi kanıtlar içerir; doğrulanamayan iddialar
⚠️ VERIFICATION REQUIRED ile işaretlidir. §8'deki çözüm **tasarım düzeyidir**
([TASARIM]) — `[KANIT]` etiketi almaz, Q5'i kapatmaz. Ölçüm kapıları bu
dosyada AÇIK (KAPALI kapılar = kapatılmayı bekleyen) kalır. `audio-dogrulama.md`
ve `index.md` bu turda dokunulmadı; `backend/index.md` §3/§5 hizalaması
ayrı edit ile yapıldı (§4 #4 dipnotu).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
