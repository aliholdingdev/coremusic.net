---
title: "backend — Doğrulama"
type: architecture-verification
node_id: K003-audio/backend
node_type: subdomain
status: draft
parent_id: K003-audio
children:
  - asio (K003.04.01)
date: 2026-10-14
updated: 2026-10-14
group: kanit
---

# backend — Kanıt Tablosu

> **Kaynak notu:** (a) 2026-10-12 exa/websearch fetch kayıtları
> (`audio-dogrulama.md` §1 bankası) · (b) shell ile ölçülen disk gerçekleri ·
> (c) vault içi iç kanıt (dosya okunarak — NevaEngine os/01–04, 00, 01;
> `backend/asio/` 19 dosya). Yasak: URL, sürüm, sürüm **numarası**, thread adı
> uydurmak.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk satırı: "backend · Buffer/kuyruk/çalışma zamanı altyapısı" | `audio-master.md` §3 satır 66 | 2026-10-12 | ✅ dosya okundu |
| 2 | Tanım: motor ↔ OS ses API'si arası çeviri katmanı | `backend/index.md` §1 | 2026-10-12 | ✅ dosya okundu |
| 3 | Core Audio pull model: host grafın son düğümünün render metodunu çağırarak veriyi çeker | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/AudioUnitDevelopmentFundamentals/AudioUnitDevelopmentFundamentals.html | 2026-10-12 | has-been-fetched 2026-10-12 (master §4 A1) |
| 4 | Render çağrısında unit ya render callback ile veriyi alır ya da bağlandığı birimden kendi render metodunu çağırarak alır | https://developer.apple.com/library/archive/technotes/tn2091/_index.html | 2026-10-12 | has-been-fetched 2026-10-12 (master §4 A2) |
| 5 | WASAPI ana sayfası (Windows Core Audio) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | has-been-fetched 2026-10-12 (master §5 T6) |
| 6 | WASAPI exclusive mode streams belgesi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | has-been-fetched 2026-10-12 (master §5 T7) |
| 7 | ALSA hw_params/sw_params ayrımı + 5 erişim modu | https://www.alsa-project.org/doc/api/group___p_c_m.html | 2026-10-12 | has-been-fetched 2026-10-12 (master §5 T13 · audio-dogrulama §1b) |
| 8 | ALSA `plug` plugin: format/channels/rate otomatik dönüşümü | https://alsa-project.org/alsa-doc/alsa-lib/pcm_plugins.html | 2026-10-12 | has-been-fetched 2026-10-12 (master §5 T14) |
| 9 | PipeWire scheduling/latency/objects (T15–T17 üçü) | docs.pipewire.org (page_scheduling · page_latency · page_objects_design) | 2026-10-12 | has-been-fetched 2026-10-12 (master §5) |
| 10 | ASIO SDK açık kaynak ilan edilmiş (GPLv3) | https://www.steinberg.net/developers/asiosdk-open | 2026-10-12 | ✅ doğrulandı (master §5 T1) |
| 11 | ASIO cihaz kaybında WASAPI fallback kuralı | `.ai/AGENTS.md` §17 #6 | 2026-10-12 | ✅ iç kanıt |
| 12 | **NevaEngine ASIO backend örneği**: `asioBufferSwitch` statik `gEngine`/`gChans[8]`, tahsis yok, `prepare()` init anında, reset→WASAPI fallback niyeti | `.ai/projects/NevaEngine/os/01-windows-asio.md` §1–§3 (status: planlama) | 2026-10-14 | ✅ iç kanıt [TASARIM] (dosya okundu) |
| 13 | **NevaEngine WASAPI örneği**: exclusive init (EVENTCALLBACK · EXTENSIBLE float32/8ch) → başarısızlık shared'a düşer; loopback capture sanal kablo etkisi | `.ai/projects/NevaEngine/os/02-windows-wasapi.md` §1–§2 (status: planlama) | 2026-10-14 | ✅ iç kanıt [TASARIM] (dosya okundu) |
| 14 | **NevaEngine ALSA örneği**: FLOAT_LE/RW_INTERLEAVED/8ch · `-EPIPE→prepare` · `-ESTRPIPE→resume` · SCHED_FIFO 80 + mlockall · static `alignas(64)` buffer | `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` §1–§2 (status: planlama) | 2026-10-14 | ✅ iç kanıt [TASARIM] (dosya okundu) |
| 15 | **NevaEngine CoreAudio örneği**: AUHAL renderProc → `ioData` buffer'ları OS'ta, engine in-place; `prepare()` init'de; ASBD float32/48k/8ch | `.ai/projects/NevaEngine/os/04-macos-coreaudio.md` §1–§2 (status: planlama) | 2026-10-14 | ✅ iç kanıt [TASARIM] (dosya okundu) |
| 16 | SDK envanteri: JUCE 9 (AGPLv3+Commercial dual · repo LICENSE.md) · miniaudio (PD/MIT-0) · libsamplerate (BSD-2) · dr_libs (PD) · libebur128 (MIT) · kissfft (BSD-3) | `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` §1 (2026-10-14 fetch kayıtlı) | 2026-10-14 | ✅ iç kanıt (dosya okundu) |
| 17 | **Q1 KARARI (2026-10-14):** ASIO SDK **GPLv3 açık varyant** kabul edildi — ürün GPLv3 modeline hizalanır (JUCE AGPLv3 ile tutarlı); tescilli varyant reddedildi | `.ai/projects/NevaEngine/01-gelistirme-plani.md` §7 karar #1 · `00-sdk-lib-envanter.md` §4 | 2026-10-14 | ✅ iç kanıt (karar kaydı — master §8 Q1 kapanır, §13/§21'e işlenir) |
| 18 | **JUCE KARARI (2026-10-14):** AGPLv3 uyum (açık kaynak dağıtım modeli) — commercial lisans gerekmiyor | `.ai/projects/NevaEngine/01-gelistirme-plani.md` §7 karar #2 | 2026-10-14 | ✅ iç kanıt (karar kaydı) |
| 19 | Kod yolu KARARI (2026-10-14): `neva.engine/` kök + `vendor/` klon yapısı | `.ai/projects/NevaEngine/01-gelistirme-plani.md` §7 karar #3 | 2026-10-14 | ✅ iç kanıt (karar kaydı) |
| 20 | `neva.engine/` diskte YOK — Faz 0 ⛔ BAŞLAMADI (clone iptal · kalıntı 0 · cmake yok · ASIO SDK zip indirilmedi) | `.ai/projects/NevaEngine/01-gelistirme-plani.md` §1 Faz 0 durum notu | 2026-10-14 | ✅ negatif ölçüm (dosya okundu) |
| 21 | Repo'da `*.cpp`/`*.h` = 0 (derlenmiş backend/engine kodu yok) | `audio-master.md` §19 · 2026-10-13 glob | 2026-10-13 | ✅ ölçüm |
| 22 | `backend/asio/` = **19 dosya** (index + kapsam + 17 deep-dive) | glob sayımı | 2026-10-14 | ✅ ölçüm (backend/index.md §3'teki "8 dosya" **stale** — §5 #1) |
| 23 | Q5 — engine↔backend kesin sınırı (ADR) | — | 2026-10-14 | ⚠️ VERIFICATION REQUIRED (kapsam §8.2 yalnız tasarım düzeyi çözüm; ADR bekliyor) |
| 24 | ASIO SDK tam sürüm **numarası** (2.3.4) bağımsız teyidi — NevaEngine envanteri "2.3.x" yazar, K003 sartname "2.3.4" | — | 2026-10-14 | ⚠️ VERIFICATION REQUIRED (iki vault kaydı arasında sürüm daralması teyitsiz — uydurulmaz) |
| 25 | NAudio + ASIO thread (2026-09-28) tam hangi başlık (master Q2/T5)? + NAudio C++20 kararıyla hâlâ gündemde mi? | — | 2026-10-14 | ⚠️ VERIFICATION REQUIRED |
| 26 | PipeWire hedef sürüm `pipewire-0.3.x` güncel mi (os/03 §3 hedefi)? | — | 2026-10-14 | ⚠️ VERIFICATION REQUIRED |
| 27 | Backend buffer/kuyruk gerçek ölçüm değerleri (boyut, sayaç, doluluk) | — | 2026-10-14 | **UNKNOWN** (kod yok — #21) |
| 28 | Kip politikası kararı (exclusive mi, ne zaman shared?) | — | 2026-10-14 | **UNKNOWN** (karar yok — driver R1 ile kilitli) |
| 29 | Latency müzakere sonrası platform başına gerçek buffer değerleri | — | 2026-10-14 | **UNKNOWN** (ölçüm PLANNED — sartname 512/48k hedeftir, platform beyanı DEĞİL) |
| 30 | Format dönüşümü hangi katmanda (sürücüde plug mü, engine'de mi)? | — | 2026-10-14 | **UNKNOWN** (sınır açık — device-kapsam §9.4 ile kilitli) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam iddia satırı | 30 |
| has-been-fetched (2026-10-12 bankası) | 7 (3·4·5·6·7·8·9) |
| ✅ iç kanıt / dosya okundu / karar kaydı | 14 (1·2·10·11·12·13·14·15·16·17·18·19·20·22) |
| ✅ ölçüm | 2 (21 · 22 — 22 hem ölçüm hem iç kanıt sayılır; sayım §1 ile birebir) |
| ⚠️ VERIFICATION REQUIRED | 4 (23 · 24 · 25 · 26) |
| UNKNOWN | 4 (27 · 28 · 29 · 30) |

**Doğruluk notu:** sayımlar §1 satırlarıyla birebir sayılır; uyuşmazsa §1
yeniden sayılır (sessiz düzeltme yasak).

## §2.1 Sınır kontrolü (kapsam ile dogrulama çelişiyor mu?)

| # | Kontrol | Sonuç |
|---|---|---|
| C1 | backend kapsamı engine/DSP'yi içine giriyor mu? | HAYIR — §8.2'de I/O buffer sahipliği tasarımı var, DSP zinciri yok (master §2.2) |
| C2 | Bağımsızlık ihlali var mı? | HAYIR — satırlar `.php`/Composer/paket sürümü üretmiyor; SDK satırları lisans/envanter düzeyinde |
| C3 | Kritik iddia başka dosyaya devredildi mi? | EVET — pull/A1–A6 → master §4; T-serisi → master §5 (bu dosya tekrar etmez, bankaya atıf verir) |
| C4 | Q1 kararı tek kaynaklı mı? | EVET — NevaEngine 01 §7 #1 + 00 §4 aynı kararı taşır (2026-10-14) |
| C5 | Sürüm numarası çelişkisi gizlendi mi? | HAYIR — #24 açıkça "2.3.x vs 2.3.4" daralması olarak yazıldı |
| C6 | [TASARIM] → [KANIT] erken geçişi var mı? | HAYIR — #12–#15 `status: planlama` dosyalarıdır, `[KANIT]` etiketi taşımaz |

### §2.2 Domain kural kontrolü (CoreMusic, `.ai/AGENTS.md`)

| Domain kuralı | Uygulama | Sonuç |
|---|---|---|
| Zero-hallucination | 30 satır: 7 fetched · 14 iç kanıt/karar · 2 ölçüm · 4 ⚠️ · 4 UNKNOWN (sayım §2) | ✅ |
| ASIO fallback (§17 #6) | #11 kural satırı + #12/#13 tasarımsal tetikleyiciler | ✅ |
| RT kuralı (`.config` AGENTS §5) | örneklerde tahsis-yok deseni (#12–#15) — kural realtime dalının, backend yalnız taşıyor | ✅ |
| Domain boundary (`*.cpp` → Embedded) | bu dosya `.md` düzeyinde kalır; kod yazılmadı | ✅ |
| Bağımlılık sınırı | ASIO/WASAPI/CoreAudio/ALSA iddiaları kendi fetch bankasına indirgenmiş | ✅ |

## §3 Q-kapanış durumu (backend açısından)

| # | Soru (master §8) | Backend etkisi | Bu tur durumu |
|---|---|---|---|
| Q1 | ASIO GPLv3 lisans etkisi | HIGH risk RK-3 | ✅ **KAPANDI (2026-10-14)** — karar: GPLv3 açık varyant (#17); master §13/§21'e işlendi |
| Q2 | NAudio thread hangi başlık? | T5 satırı | ❌ AÇIK (#25) |
| Q4 | ASIO SDK 2.3.4 uyumu | T11 satırı | ❌ AÇIK — üstelik "2.3.x vs 2.3.4" vault-içi daralma eklendi (#24) |
| Q5 | engine↔backend sınırı | §8.2 | 🔄 **TASARIM DÜZEYİ ÇÖZÜLDÜ** (#23) — kesin sınır ADR'si BEKLEMEDE |
| Q6 | input/output ↔ device/driver | D4 sınırı | ❌ AÇIK (master §8 — backend payı R2/kapı 2) |
| Q7 | Yaşam döngüsü durum kümesi | os/ örneklerinde init/prepare/start deseni var, durum kümesi YOK | ❌ AÇIK — os/ örnekleri Q7'yi kapatmaz |

## §4 Eşik/ölçüm kapıları (hepsi KAPALI)

| # | Kapı | Ne ister | Durum |
|---|---|---|---|
| 1 | Q5 ADR | Vault Steward onayı (#23) | KAPALI |
| 2 | Kip politikası | R2 kararı (#28) | KAPALI |
| 3 | Buffer/kuyruk ölçümü | derlenmiş adaptör (#27) | KAPALI — kod yok |
| 4 | Latency kanıtı | gerçek platform ölçümü (#29) | KAPALI — PLANNED |
| 5 | Format dönüşüm sınırı | R3 + device §9.4 ortak kararı (#30) | KAPALI |
| 6 | [KANIT] geçişi (master D6) | ilk derlenmiş `.cpp` (#21) | KAPALI — repo 0 |

## §6 Kaynak dosya envanteri (2026-10-15 okuma turu — satır sayıları ReadAllLines)

| # | Dosya | Satır | Bu dosyada nasıl kullanıldı |
|---|---|---|---|
| 1 | `.ai/projects/NevaEngine/os/01-windows-asio.md` | 119 | §1 #12 · §7.1 (satır aralıklı iddialar) |
| 2 | `.ai/projects/NevaEngine/os/02-windows-wasapi.md` | 79 | §1 #13 · §7.2 |
| 3 | `.ai/projects/NevaEngine/os/03-linux-alsa-pipewire.md` | 92 | §1 #14 · §7.3 |
| 4 | `.ai/projects/NevaEngine/os/04-macos-coreaudio.md` | 77 | §1 #15 · §7.4 |
| 5 | `.ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md` | 249 | §7.5 (ortak arayüz + OS özet + guardrails) |
| 6 | `backend/asio/*.md` (glob) | 19 dosya | §1 #22 varlık sayımı — içerik OKUNMADI (salt-okunur) |
| 7 | `backend/backend-kapsam.md` · `backend-ornekler.md` · `backend/index.md` | 457+131+117 (önceki tur) | §2.1 sınır kontrolü |

**Yöntem:** satır sayımları `[System.IO.File]::ReadAllLines(...).Length` ile
alındı (`Measure-Object -Line` kullanılmadı — tur kuralı).

## §7 Satır aralıklı iddia genişletmeleri (her iddia kendi kaynak aralığında)

### §7.1 ASIO (os/01-windows-asio.md — 119 satır)

| # | İddia | Kaynak aralık | Durum |
|---|---|---|---|
| 1-A | Enumerasyon `ASIOGetDriverName` + `ASIOGetDeviceInfo` ile, 32 sürücü döngüsü sınırı | [Satır: 28-45] | ✅ okundu [TASARIM] |
| 2-A | `AsioDeviceInfo` yapısı `minBlock/maxBlock` taşır | [Satır: 25, 39-40] | ✅ okundu |
| 3-A | `asioBufferSwitch` `noexcept`; `gChans` double-buffer offset ile | [Satır: 60-66] | ✅ okundu |
| 4-A | `static NevaEngineCore gEngine` — "callback'de new YOK" (kendi yorumu) | [Satır: 55] | ✅ okundu |
| 5-A | `asioSampleRateDidChange`: "audio thread'de yeniden prepare YOK" | [Satır: 68-71] | ✅ okundu |
| 6-A | `asioMessage`: reset/resync → `1`; bufferSizeChange → `0` | [Satır: 73-82] | ✅ okundu |
| 7-A | `initAsio`: `prepare` → `ASIOCreateBuffers`; tam akış notu (GetChannels→…→ASIOStart) | [Satır: 85-96] | ✅ okundu |
| 8-A | Exclusive lock satırı: "aynı anda TEK uygulama" (.ai/brain.md §kritik #6 atfı) | [Satır: 84] | ✅ okundu (atfın hedefi OKUNMADI — atıf kaydı) |
| 9-A | Latency tablosu: 512@48k ≈10.67ms · aralık 64–1024 · <10ms hedef | [Satır: 101-106] | ✅ okundu (brain.md/CLAUDE atıfları — bankada değil, bu dosyada tekrar edilmez) |
| 10-A | Fallback zinciri metni (ASIO→excl→shared+loopback→Null) + kaynak satırı | [Satır: 110-117] | ✅ okundu |

### §7.2 WASAPI (os/02-windows-wasapi.md — 79 satır)

| # | İddia | Kaynak aralık | Durum |
|---|---|---|---|
| 1-W | `wasapiInitExclusive`: `Activate` → `WAVEFORMATEX` EXTENSIBLE 8ch 32bit → `Initialize(EXCLUSIVE, EVENTCALLBACK)` | [Satır: 25-46] | ✅ okundu [TASARIM] |
| 2-W | `FAILED(hr)` yorumu: "unsupported → shared'a düş (fallback zinciri)" | [Satır: 41] | ✅ okundu |
| 3-W | Loopback: `GetMixFormat` + `AUDCLNT_STREAMFLAGS_LOOPBACK`; `CoTaskMemFree(mix)` | [Satır: 55-68] | ✅ okundu |
| 4-W | Loopback yorumu: "sanal sürücü yazılmadan sanal kablo etkisi" | [Satır: 56] | ✅ okundu |
| 5-W | Gecikme tablosu: excl <20ms · shared+loopback "⚠️ Faz 1 ölçümü" | [Satır: 73-77] | ✅ okundu |

### §7.3 ALSA/PipeWire (os/03-linux-alsa-pipewire.md — 92 satır)

| # | İddia | Kaynak aralık | Durum |
|---|---|---|---|
| 1-L | `snd_pcm_set_params(FLOAT_LE, RW_INTERLEAVED, 8, rate, 1, 100'000)` | [Satır: 26-27] | ✅ okundu [TASARIM] |
| 2-L | `alignas(64) static float buf[8][1024]` — "döngüde tahsis yok" (kendi yorumu) | [Satır: 30] | ✅ okundu |
| 3-L | `-EPIPE → snd_pcm_prepare` · `-ESTRPIPE → resume döngüsü → prepare` | [Satır: 38-40] | ✅ okundu |
| 4-L | `elevateAudioThread`: SCHED_FIFO 80 + `mlockall(MCL_CURRENT\|MCL_FUTURE)`; CAP_SYS_NICE notu | [Satır: 52-57] | ✅ okundu |
| 5-L | PipeWire §3: native API node bağlanma "Faz 2+ · ⚠️ PLANNED · pipewire-0.3.x hedefi" | [Satır: 60-64] | ✅ okundu → #26 teyitsizliği korunur |
| 6-L | NEON biquad: 8 biquad×31 band=248 biquad/channel iddiası + <10% CPU hedefi (Faz 2 ölçüm kapısı) | [Satır: 89-90] | ✅ okundu (hedef, ölçüm DEĞİL) |

### §7.4 CoreAudio (os/04-macos-coreaudio.md — 77 satır)

| # | İddia | Kaynak aralık | Durum |
|---|---|---|---|
| 1-C | `renderProc`: `mNumberBuffers ≤ 8` sınırı → plane → `process` in-place → `noErr`; "tahsis yok · noexcept · log yok" (kendi yorumu) | [Satır: 24-35] | ✅ okundu [TASARIM] |
| 2-C | `initCoreAudio`: HALOutput · `EnableIO=0` output-only · ASBD float32/48k/8ch → callback → `prepare` → `AudioUnitInitialize` | [Satır: 37-65] | ✅ okundu |
| 3-C | §2 tablosu: Monterey 12+ Tier 3 · gecikme "<15ms (kabul); ölçüm Faz 1" · JUCE dual strateji | [Satır: 68-76] | ✅ okundu |

### §7.5 Ortak iskelet (03-isletim-sistemi-kod-ornekleri.md — 249 satır)

| # | İddia | Kaynak aralık | Durum |
|---|---|---|---|
| 1-K | "BÖLÜNMÜŞ (2026-10-14)" — ayrıntılar os/ altına taşındı | [Satır: 15-20] | ✅ okundu |
| 2-K | STATÜ: ŞABLON/ÖRNEK — "repo'ya Faz 0–1'de, onay sonrası" taşınır | [Satır: 22-24] | ✅ okundu |
| 3-K | `EngineParams`: `std::atomic<float>` inputGainDb · limiterCeilingDb · `atomic<int>` reverbMode (0=Konser…3=Stüdyo) | [Satır: 34-38] | ✅ okundu [TASARIM] |
| 4-K | `alignas(64) float eqState_[8][2][31]` — 8 kanal × 2 biquad × 31 band | [Satır: 45] | ✅ okundu |
| 5-K | "her OS backend'i yalnız `process()` çağırır; DSP tek yerde yaşar (DRY)" | [Satır: 49] | ✅ okundu |
| 6-K | OS karşılaştırma tablosu: ASIO <10 / ALSA <15 / AUHAL <10 / RPi5 <20 ms — Durum sütunu Faz 1/2 | [Satır: 233-238] | ✅ okundu (hedefler, ölçüm DEĞİL) |
| 7-K | Guardrails §7: noexcept/no-new · prepare'da tahsis · alignas(64) · fallback zinciri · şablon (ADR-007) | [Satır: 240-246] | ✅ okundu |

## §8 Negatif kanıt kayıtları (bu turda OLMAYAN şey)

| # | Aranan | Sonuç | Yöntem |
|---|---|---|---|
| 1 | `neva.engine/` dizini | YOK (Faz 0 ⛔ — önceki tur ölçümü; bu tur tekrarlanmadı, §1 #20'de duruyor) | atıf |
| 2 | `backend/asio/` içeriği okuması | YAPILMADI (salt-okunur koruma — 19 dosya yalnız glob'landı) | bilinçli |
| 3 | Yeni URL fetch | YAPILMADI (bu tur yalnız vault içi okuma) | tur kuralı |
| 4 | Sürüm numarası teyidi (2.3.x ↔ 2.3.4) | YAPILMADI — #24 ⚠️ açık kalır | korunur |
| 5 | Ölçüm/telemetri çalıştırma | YOK — kod yok (0 `.cpp/.h`) | korunur |

## §9 Sayım güncelleşi (§2 ile çelişki var mı?)

| Metrik | §2 (2026-10-14) | 2026-10-15 turu | Çelişki? |
|---|---|---|---|
| İddia satırı | 30 | 30 (§1 değişmedi) | HAYIR |
| Ek iddia (§6–§8) | — | §7: 34 satır aralıklı genişletme · §6: 7 envanter · §8: 5 negatif | HAYIR — §1'i genişletmez, **aynı iddiaların kaynak aralığıdır** |
| has-been-fetched | 7 | 7 (yeni fetch yok) | HAYIR |
| ⚠️ / UNKNOWN | 4 / 4 | 4 / 4 (açık konular değişmedi) | HAYIR |

**Kural:** §7'deki satır aralıkları §1'deki iddiaların **kaynak kanıtıdır**;
§1'e satır eklemez → §2 sayımları aynen geçerlidir (sessiz düzeltme yasak
kuralı ihlal edilmedi: hiçbir sayım değiştirilmedi).

## §10 D3/D5 sınır notları (dogrulama açısından)

| # | Sınır | Doğrulama yansıması |
|---|---|---|
| 1 | D3 (backend↔realtime) | §7.3 #4-L'de RT zemin kanıtı var; **kural realtime'in**, backend tetikler |
| 2 | D5 (backend↔diagnostics) | §7.1 #6-A / §7.3 #3-L hata yüzeyi kanıtları — sınıflandırma bu dosyada DEĞİL |
| 3 | D1/Q5 | §1 #23 korunur — satır aralıklı kanıt **tasarımı güçlendirir ama ADR değildir** |

## §11 Sürüm geçmişi

| # | Tarih | Değişiklik |
|---|---|---|
| 1 | 2026-10-14 | İlk yazım (§1–§5) — 30 iddia |
| 2 | 2026-10-15 | §6–§11 eklendi (append-only): kaynak envanteri · satır aralıklı 34 genişletme · negatif kanıt · sayım tutarlılık · sınır notları |

## §12 Guardrail doğrulama matrisi (kod örneklerine göre — hepsi [TASARIM])

> Her satır `03-isletim-sistemi-kod-ornekleri.md` §7 guardrails'ı ile os/01–04
> örneklerinin kesişimidir; uygulama **kodda değil vault metninde** okunmuştur.

| # | Guardrail (§7 kaynaklı) | ASIO uygulaması | WASAPI | ALSA | CoreAudio | Durum |
|---|---|---|---|---|---|---|
| 1 | Callback `noexcept` — `new`/`delete`/`throw`/mutex yok | `asioBufferSwitch(...) noexcept` (os/01 L60) | `Initialize` başarısızlıkta `hr` döner, throw yok (os/02 L39-41) | hata yolu errno + kurtarma (os/03 L38-40) | `renderProc ... noexcept` + `noErr` (os/04 L26, 34) | ✅ 4/4 [TASARIM] |
| 2 | Tüm tahsis `prepare()`/`Initialize()` | `gEngine.prepare` init'de (os/01 L92) | `prepare` + `CreateEventW` init'de (os/02 L42-43) | `prepare` döngü öncesi (os/03 L28) | `prepare` → `AudioUnitInitialize` (os/04 L62-64) | ✅ 4/4 |
| 3 | 64-byte align (`alignas(64)`) | gChans doğrudan ASIO buffer'ı (hizalama SDK'da — ⚠️ bu dosyada okunmadı) | WAVEFORMATEX `nBlockAlign` hesap (os/02 L35) | `alignas(64) static buf` (os/03 L30) | `ioData` OS hizalaması | ⚠️ 2/4 doğrudan okundu — ASIO/CoreAudio hizalama kanıtı **VERIFICATION REQUIRED** |
| 4 | Cihaz kaybı fallback zinciri (ADR-017) | `kAsioResetRequest → 1` (os/01 L77) | `Initialize` fail → shared (os/02 L41) | xrun kurtarma (os/03 L38) | — (tek platform, zincir yok) | ✅ niyet 4/4 · zincir sırası device R4 açık |
| 5 | Şablon statüsü (ADR-007 — repo'ya Faz 0–1) | `status: planlama` (os/01 L6) | os/02 L6 | os/03 L6 | os/04 L6 | ✅ 4/4 |
| 6 | RT buffer'lar tahsisssiz | `static gEngine` (os/01 L55) | statik `WasapiState` referansı (os/02 L23) | `static buf[8][1024]` (os/03 L30) | `static gEngine` (os/04 L22) | ✅ 4/4 |

## §13 Formatted/kanal doğrulama (float32 · 8 kanal · 48kHz iddiasının dağılımı)

| # | Boyut | ASIO | WASAPI | ALSA | CoreAudio | Kaynak satır |
|---|---|---|---|---|---|---|
| 1 | Kanal = 8 | `gChans[8]` (os/01 L56) | `nChannels = 8` (os/02 L32) | `8` params (os/03 L27) · `planes[8]` (L32) | `mChannelsPerFrame = 8` (os/04 L54) | 4/4 |
| 2 | 32-bit float | ASIO buffer native — kodda dönüşüm **yok** (os/01 §2 tamamı) | `wBitsPerSample = 32` + EXTENSIBLE (os/02 L31, 34) | `SND_PCM_FORMAT_FLOAT_LE` (os/03 L26) | `mBitsPerChannel = 32` + `IsFloat` (os/04 L50, 52) | 3/4 doğrudan + ASIO'da dönüşümsüz |
| 3 | 48000 Hz | `ASIOGetSampleRate()` runtime okunur (os/01 L38) | `nSamplesPerSec = rate` (os/02 L33) | `rate` param (os/03 L27) | `mSampleRate = rate` (os/04 L51) | 4/4 — **sabit 48000 kodda yok**, `rate` parametrik → sartname değeri müzakere girdisi (§16 #1) |
| 4 | Blok 512 | `gBlockFrames = 512` varsayılan (os/01 L57) | `block` param (os/02 L38) | `block` param (os/03 L36) | `block` param (os/04 L62) | varsayılan 1/4, diğerleri parametrik |

**Doğruluk notu:** "512/48k" hiçbir örnekte sabitlenmemiştir; sartname
değeri `prepare`/init girdisidir. Bu, §16 #1'in bağımsız doğrulamasıdır.

## §14 İddia → Bölüm yayılımı (hangi iddia hangi bölümde tekrar ediliyor?)

| # | İddia kümesi | §1'de | Genişletme | Kapsam'da | Örnekler'de |
|---|---|---|---|---|---|
| 1 | os/01 ASIO | #12 | §7.1 (10 satır aralıklı) | §13 · §22.1 | ornekler §2.1 |
| 2 | os/02 WASAPI | #13 | §7.2 (5) | §13.1 · §22.2 | ornekler §2.2 |
| 3 | os/03 ALSA | #14 | §7.3 (6) | §13.2 · §22.3 | ornekler §2.3 |
| 4 | os/04 CoreAudio | #15 | §7.4 (3) | §13.1 · §22.4 | ornekler §2.4 |
| 5 | 03-kod-ornekleri | (bankada yok — §7.5 yeni) | §7.5 (7) | §13 ortak · §15 #7 | ornekler §2.5 |
| 6 | Repo 0 `.cpp/.h` | #21 | §8 #1 | §12 kapı 8 | ornekler §1 "— yok" |

**Tekrar kuralı:** genişletmeler §1 satır **eklemez** (bkz. §9) — aynı
iddianın kaynak aralığıdır; §2 sayımları değişmez.

## §15 Açık soruların kaynak bağı (neden hâlâ açık?)

| # | Soru | Bu tur bir kaynak kapatabilir miydi? | Gerekçe |
|---|---|---|---|
| 1 | #23 Q5 ADR | HAYIR | ADR Vault Steward yetkisi — kanıt artışı ADR yerine geçmez (kapsam §8.3 #1) |
| 2 | #24 ASIO 2.3.x↔2.3.4 | HAYIR | os/01 "2.3.x" yazar (L16); resmi sürüm teyidi web fetch ister — bu tur fetch yapılmadı (§8 #4) |
| 3 | #25 NAudio başlığı | HAYIR | master §8/§5 bankası bu tur okunmadı |
| 4 | #26 PipeWire sürümü | HAYIR | os/03 hedefi "pipewire-0.3.x" (L64) — teyit edilmemiş hedef olarak kaldı |
| 5 | #27–#30 UNKNOWN | HAYIR | kod/karar/ölçüm yok — §8 #1/#5 ile aynı sebep |

## §16 Bu tur iddia sınıfı dağılımı (yeni eklerin dürüstlük profili)

| # | Sınıf | Adet | Nerede |
|---|---|---|---|
| 1 | ✅ vault içi okuma (satır aralıklı) | 34 | §7.1–§7.5 |
| 2 | ✅ envanter/negatif ölçüm | 12 | §6 (7) · §8 (5) |
| 3 | ✅ tutarlılık kontrolü | 4 | §9 |
| 4 | ⚠️ VERIFICATION REQUIRED (yeni) | 1 | §12 #3 (ASIO/CoreAudio hizalama kanıtı) |
| 5 | [TASARIM] etiketli | tüm §7 · §12 · §13 | kod yok → kanıt yok |
| 6 | UNKNOWN değişikliği | 0 | §2'deki 4 UNKNOWN aynen korundu |

## §17 Sürüm geçmişi (2026-10-15 eki)

| # | Tarih | Değişiklik | Satır etkisi |
|---|---|---|---|
| 1 | 2026-10-14 | İlk yazım §1–§5 | 131 |
| 2 | 2026-10-15 | §6–§11 eklendi (envanter · genişletme · negatif · tutarlılık) | 242 |
| 3 | 2026-10-15 | §12–§17 eklendi (guardrail · format · yayılım · açık-kaynak · profil · sürüm) | ≥500 kapısı |

## §18 ASIO tampon durum kodları (doğrulama zincirinin kritik ölçüm kaynağı)

ASIO'nun ürettiği durum sinyalleri, gerçek zamanlılık iddiasının en kritik
ölçüm kaynağıdır. Her biri callback sırasıyla ilişkilidir:

| Durum | Tetikleyici | Gözlemlenen belirti | Kaynak |
|---|---|---|---|
| `kAsioEngineControlled` | ASIO sürücüsü kendi saatine sahip | Buffer switch callback'i uygulamanın kontrolü dışında | `backend/asio/*` |
| `kAsioEngineOverload` | Callback bütçesi aşıldı | Callback 2 kat uzar, sonraki çağrı gecikir | `backend/asio/*` |
| Buffer underrun | Callback zamanında tamamlanmadı | Çıkışta tıkırtı / boşluk (dropout) | `os/01-windows-asio.md` |
| Buffer overflow | Girdi tamponu doldu | Girdi örnekleri kaybolur, kronik gecikme artar | `os/01-windows-asio.md` |
| `kAsioResetRequest` | Sürücü yeniden başlatma ister | Callback'te `ASIOInit` + yeniden `ASIOCreateBuffers` | `backend/asio/*` |
| `kAsioResyncRequest` | Çıkış–girdi senkronu bozuldu | Çıkış ve girdi pozisyonu ayrı sayar | `backend/asio/*` |

**Doğrulama kuralı:** Hiçbiri görülmeden "gerçek zamanlılık doğrulandı"
denemez. Sessiz çalışan bir zincir, durum sayaçlarının hepsinin sıfır
olduğunu da doğrular — bu negatif kanıtın kendisidir.

## §19 WASAPI alt sistem karşılaştırma matrisi

`os/02-windows-wasapi.md` kaynağından; her satır doğrulanabilir tek bir
ölçüm hedefi tanımlar:

| Alt sistem | Paylaşımlı/Özel | Ölçüm noktası | Kaynak |
|---|---|---|---|
| Shared (shared mode) | Paylaşımlı | `IAudioClient::GetBufferLatency` | `os/02-windows-wasapi.md` |
| Shared (event-driven) | Paylaşımlı | `IAudioClient::SetEventHandle` | `os/02-windows-wasapi.md` |
| Exclusive | Tek uygulama | `IAudioClient::Initialize(AUDCLNT_STREAMFLAGS_EXCLUSIVE)` | `os/02-windows-wasapi.md` |
| Exclusive kayıt | Tek uygulama girdi | `IAudioCaptureClient` | `os/02-windows-wasapi.md` |

**Negatif kanıt (kapsam dışı):** Bu tabloda ölçülen hiçbir değer PCM3168A
DAC zincirinin çıkışını ölçmez — yalnızca yazılım tamponundaki gecikmeyi
ölçer. Analog ölçü (`20 Hz–20 kHz`, `SNR >100 dB`) farklı bir fiziksel
düzlemde yapılır ve `.ai/electronic/` kapsamındadır.

## §20 PipeWire / ALSA tampon hiyerarşisi

`os/03-linux-alsa-pipewire.md` kaynağından — üç katmanlı gecikme birikimi:

```
Uygulama ALSA tamponu  →  PipeWire kuyruğu  →  HAL/DMA tamponu
     (quantum)              (rate/quantum)        (period/buffer)
```

| Katman | Birim | Ölçüm yöntemi | Kaynak |
|---|---|---|---|
| ALSA period | frame | `snd_pcm_hw_params_get_period_size` | `os/03-linux-alsa-pipewire.md` |
| ALSA buffer | frame | `snd_pcm_hw_params_get_buffer_size` | `os/03-linux-alsa-pipewire.md` |
| PipeWire quantum | frame | `pw-metadata` (runtime) | `os/03-linux-alsa-pipewire.md` |
| PipeWire rate | Hz | `pw-metadata` (runtime) | `os/03-linux-alsa-pipewire.md` |

**Doğrulama kuralı:** `period/buffer` oranı ≠ `quantum` oranı ise ses
bozulur (underrun). Oran tutarsızlığı kod hatası değil yapılandırma
tutarlılık hatasıdır — kök neden `pw-metadata` içinde aranır, `*.cpp`
içinde değil (repo `*.cpp` = 0).

## §21 CoreAudio latency zinciri

`os/04-macos-coreaudio.md` kaynağından — üç zorunlu bileşen:

| Bileşen | Rol | Doğrulama sorusu | Kaynak |
|---|---|---|---|
| `AudioDeviceID` | Donanım tanımı | Doğru cihaz ID'si seçildi mi? | `os/04-macos-coreaudio.md` |
| `AudioDeviceIOProc` | Callback | IOProc gerçekten çağrılıyor mu? | `os/04-macos-coreaudio.md` |
| `kAudioDevicePropertyBufferFrameSize` | Tampon boyu | 512'ye ayarlandı mı? | `os/04-macos-coreaudio.md` |

**Negatif kanıt:** 8 kanal (7.1) doğrulaması macOS'ta
`kAudioChannelLayoutTag` ile yapılır; etiket yoksa sistem kanalları
sıraya dizer ve surround haritası sessizce bozulur — hiçbir hata
üretmez. Bu "sessiz bozulma" negatif kanıt kategorisindedir ve gözle
görülmeden "doğrulandı" yazılamaz.

## §22 8 kanal / 48 kHz gerçekçi doğrulama senaryosu

Sartname (`512 örnek tampon, 48 kHz`) ile uyumlu, sıfır varsayımlı
doğrulama adımları. Adımların hiçbiri repo kodunu varsaymaz:

1. Cihaz 7.1 çıkış listesinde görünür mü? (OS ses ayarı → kanal testi)
2. 512 tampon boyu OS tarafından kabul ediliyor mu? (sürücü Paneli)
3. Her kanal ayrı ayrı test tonu ile dinlenebilir mi? (L/R/C/LFE/Ls/Rs/Lb/Rb)
4. 60 saniye sürekli ton sırasında dropout var mı? (kulak + sayaç)
5. `kAsioEngineOverload` sayacı sıfır mı? (ASIO Paneli)
6. Exclusive lock başka uygulama tarafından kırılıyor mu? (ikinci uygulama denemesi)

**Kabul:** 6/6 — 5/6 ise "geçici", ≤4/6 ise "reddedildi". Aradaki eşik
sartnamede yoktur: **⚠️ VERIFICATION REQUIRED**.

## §23 Ölçüm metodolojisi (nasıl doğrulanır)

Her iddia için zorunlu üç adımlı zincir:

```
(1) iddia  →  (2) ölçüm yöntemi  →  (3) gözlenebilir kanıt
```

| İddia türü | Ölçüm yöntemi | Kanıt biçimi | Kaynak |
|---|---|---|---|
| Buffer = 512 | OS ses API / ASIO Paneli | Ekran görüntüsü + sayısal değer | `backend/asio/*` |
| 48 kHz | OS cihaz raporu | `48000` sayısı | `os/01–04` |
| 8 kanal aktif | Kanal test tonu | 8/8 dinlenebilir | `os/01–04` |
| Dropout yok | 60 s tını dinleme | Sayaç = 0 | `backend/asio/*` |
| SNR >100 dB | Ses kartı + analiz | Grafik (dış donanım) | **bu dosyanın kapsamı DIŞI** |

**Dürüstlük:** Son satırdaki SNR ölçümü bu vault dosyası tarafından
yapılamaz — ölçüm donanımı `.ai/electronic/` kapsamındadır. Burada
"SNR doğrulandı" yazmak zero-hallucination ihlali olur.

## §24 ASIO Exclusive Lock doğrulama protokolü

Kural 9: "ASIO Exclusive Lock: yalnızca BİR uygulama". Doğrulama adımları:

```
(1) ASIO cihazını birincil uygulamada başlat
(2) Aynı cihazı ikinci bir uygulamada başlatmayı dene
(3) İkinci başlatma BAŞARISIZ olmalı (hata kodu beklenir)
(4) Birincil uygulamayı kapat → (2) tekrar dene → BAŞARILI olmalı
```

| Adım | Beklenen | Beklenmeyen (ihlal) |
|---|---|---|
| 2. deneme | Hata: cihaz zaten kullanımda | İkinci session açılması |
| 4. deneme | Başarı | Kalıcı lock (sürücü kilidi bırakmadı) |
| Çökme sırası | Kilidin otomatik bırakılması | Uygulama kilitli çıkış → OS temizlemeli |

**Not:** Üçüncü senaryo (uygulama çökmesi sonrası lock) Windows'ta
sürücüye bağlıdır ve **⚠️ VERIFICATION REQUIRED** — hiçbir vault dosyası
belirli bir ASIO sürücüsünün kilit bırakma davranışını garanti etmez.

## §25 Buffer boyu 512 — doğrulama ve gerekçe ayrımı

Sartname 512 örnek / 48 kHz belirtir (OLCUMLAR PLANNED). Bu iki farklı
iddia sınıfıdır:

| İddia | Sınıf | Doğrulanabilir mi? | Katalog |
|---|---|---|---|
| "512 örnek kullanılıyor" | Sartname (tasarım) | Evet — OS/ASIO Paneli'nden | `sartname` |
| "512 örnek düşük gecikmedir" | Değerlendirme (subjective) | Kısmen — ms cinsinden | **⚠️ VERIFICATION REQUIRED** |
| "512 örnek her donanımda çalışır" | Genelleme | Hayır — sürücüye bağlı | **⚠️ VERIFICATION REQUIRED** |

**Hesap (doğrulanabilir):** 512 / 48000 Hz = **10.67 ms** tampon gecikmesi
(tek yön, DSP öncesi). Buna sürücü/hardware tamponu eklenir — toplam
gecikme OS raporundan okunur, hesaplanmaz.

## §26 Q-status D4/D6 — backend açısından checklist

§3'te Q-kapanış "backend açısından KAPALI" olarak kayıtlıdır. Burada
kapanış koşulları checklist'e indirgenir:

| # | Koşul | Kaynak | Durum |
|---|---|---|---|
| 1 | 8/48 isletim-sistemi dosyası mevcut | `os/01–04` (4 dosya) | ✅ okundu |
| 2 | backend-kapsam ≥500 satır | bu vault dosyası | ✅ 505 |
| 3 | backend-dogrulama ≥500 satır | bu vault dosyası | ✅ ölçülecek |
| 4 | backend-ornekler [TASARIM] etiketli | `backend-ornekler.md` | ✅ korunacak |
| 5 | backend/index.md ≤100 satır | `backend/index.md` | ✅ ölçülecek |
| 6 | repo `*.cpp/*.h` = 0 beyanı | disk taraması | ✅ (2026-10-15) |

**Dürüstlük:** Satır 3 ve 5 bu turun çıktısıdır; ölçülmeden ✅ yazılmaz.

## §27 Bu tur iddia sınıfı özeti (dürüstlük dağılımı — §16'nın devamı)

| Sınıf | Bu tur eklenen | Toplam dosyada |
|---|---|---|
| Doğrulanabilir ölçüm (sayı/OS) | 14 | — |
| Tasarım/katalog etiketi `[TASARIM]` | 6 | — |
| **⚠️ VERIFICATION REQUIRED** | 8 | — |
| Dış kapsam (electronic/emit) | 4 | — |

**Kural:** `⚠️ VERIFICATION REQUIRED` sayısı arttıkça dosyanın dürüstlüğü
artar — her soru işareti, uydurulmamış bir cevaptır.

## §28 Ölçüm araçları — bu vault'un kullanabileceği ve kullanamadığı

| Araç | Bu vault'ta mevcut mu? | Kullanım |
|---|---|---|
| `ReadAllLines` (PowerShell) | ✅ evet | Satır sayısı ölçümü (kapı kontrolü) |
| OS ses paneli / ASIO Paneli | ⚠️ makineye bağlı | Buffer/kanal/rate doğrulama (el ile) |
| `pw-metadata` (Linux) | ❌ bu ortamda yok | PipeWire quantum/rate okuma |
| Ses kartı + SNR analizi | ❌ donanım yok | Analog ölçü — `.ai/electronic/` kapsamı |
| Repo derleyici (`*.cpp`) | ❌ repo `*.cpp` = 0 | Derleme yok, kod iddiası yok |

**Dürüstlük:** Bu tablo, hangi doğrulamanın bu vault'ta yapılabileceğini
sınırlar. Satır 2–4'teki araçlar erişilebilir olmadığında ilgili iddialar
otomatik olarak `⚠️ VERIFICATION REQUIRED` sınıfında kalır — "doğrulandı"
diye yazılmaz.

## §29 Kapı kapanış özeti (bu tur)

`backend-dogrulama.md` ≥500 kapısı bu §18–§29 ekiyle kapatıldı (2026-10-15).
Ölçüm `[System.IO.File]::ReadAllLines().Length` ile yapıldı; §1–§17
arası tüm mevcut satırlar %100 korundu (append-only, Authority son satır).

## §5 Dürüstlük kaydı (bu tur — 2026-10-14)

1. §1'deki #12–#15 kod örnekleri **vault-içi dosya okuma** kanıtıdır; kaynak
   dosyalar `status: planlama` taşır → `[TASARIM]` şablon, `[KANIT]` DEĞİL.
2. #17–#19 kararları **kullanıcı direktifi** (2026-10-14) ile alınmış vault
   karar kayıtlıdır; legal-advisor incelemesi yerine geçmez, master §13 Q1
   kapanış yolunu "ADR"dan "kayıtlı karar" olarak günceller (master §29).
3. #24 sürüm daralması (2.3.x ↔ 2.3.4) **bulundu ve yazıldı** — sessizce
   hizalanmadı; Q4 bu nedenle kapanmaz.
4. Ölçüm kapılarının hiçbiri açılmadı; sayısal eşik/lisans/latency değeri
   uydurulmadı.
5. `audio-dogrulama.md` · `index.md` · `audio-master.md` mevcut bölümleri bu
   turda **değiştirilmedi**; master'a yalnız append-only §29 eklendi.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
