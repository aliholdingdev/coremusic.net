---
title: "pipeline — Örnekler"
type: architecture-examples
node_id: K004-dsp/pipeline
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# pipeline — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında çalışan bir C++/DSP pipeline zinciri yoktur.
Tek ilgili yüzey PHP katmanındaki bir placeholder'dır (dolgu kod — K004 motoru
sayılmaz):

```php
// shared/src/AI/AIEngine.php satır 315-327
private function extractFeatures(string $filePath): array
{
    // Gerçek implementasyonda C++ DSP engine ile yapılacak
    // Şimdilik placeholder değerler
    return [
        'bpm'          => 120.0,
        'key'          => 'C',
        'energy'       => 0.7,
        // ...
    ];
}
```

> Bu snippet bir **kanıt** değil, **eksikliğin kanıtıdır**: yorum satırı gerçek
> DSP motorunun henüz yazılmadığını beyan eder. Uydurma zincir yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Quick start, 2026-10-12 ✅)

```cpp
const dspark::AudioSpec spec { 48000.0, 512, 2 };
dspark::Equalizer<float> equalizer;
dspark::Compressor<float> compressor;
dspark::Limiter<float> limiter;
equalizer.prepare(spec);
compressor.prepare(spec);
limiter.prepare(spec);
// ...
equalizer.processBlock(block);
compressor.processBlock(block);
limiter.processBlock(block);
```

**Ne gösterdiği:** pipeline'ın iki fazlı deseni — önce her düğüm `prepare(AudioSpec)`
ile yapılandırılır (allocate serbest), sonra `processBlock()` realtime döngüde
allocate etmeden zincirleme çağrılır. Sıra: EQ → compressor → limiter.

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities/Infrastructure)

`ProcessorChain` sınıfı, `AudioBufferView` ile host-taraflı kanal işaretçileri
üzerinden çalışır; "real-time-style channel-strip" örneği zincirin host'a nasıl
oturtulduğunu gösterir.

## §3 Uygulama notu

CoreMusic pipeline'ı bu iki fazlı deseni izlemelidir (setup ≠ realtime). Basamak
sırası (Q3) kararı verilmeden zincir kanonik olarak kilitlenmez; K004 düğümleri
K003'ün graph'ına eklenir, ayrı thread tutmaz (dsp-master §2.2).

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[pipeline-kapsam]]` | bu dalın sınırı ve açık soruları |
| `[[pipeline-dogrulama]]` | bu dosyadaki iddiaların kanıt tablosu |
| `../dsp-master.md` | üst anayasa · §2.2 · §3 satır 1 |

## §5 Web araştırması — pipeline altyapı referansları (2026-10-12)

`Invoke-WebRequest -Method Head -UseBasicParsing` ile doğrulandı (HTTP 200).

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | `AudioWorkletNode`, özel ses işleme kodunu audio worklet thread'inde çalıştırır; `AudioWorkletProcessor` alt sınıfı `process()` metodu ile blok işler | https://developer.mozilla.org/en-US/docs/Web/API/AudioWorkletNode | 2026-10-12 | ✅ HTTP 200 |
| 2 | `AudioWorkletProcessor`, `process(inputs, outputs, parameters)` imzasıyla her blok çağrılır; dönen `true` node yaşamaya devam eder | https://developer.mozilla.org/en-US/docs/Web/API/AudioWorkletProcessor | 2026-10-12 | ✅ HTTP 200 |
| 3 | `AudioContext.baseLatency`, giriş ile çıkış arasındaki donanım-tarasal gecikmeyi saniye cinsinden döner | https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/baseLatency | 2026-10-12 | ✅ HTTP 200 |
| 4 | `AudioContext.outputLatency`, girişten işletim sistemine yazılan ses arasındaki tahmini gecikmeyi döner | https://developer.mozilla.org/en-US/docs/Web/API/AudioContext/outputLatency | 2026-10-12 | ✅ HTTP 200 |
| 5 | `OfflineAudioContext`, gerçek zaman kısıtı olmadan senkron render sağlar; pipeline dışı test/hesaplama için kullanılır | https://developer.mozilla.org/en-US/docs/Web/API/OfflineAudioContext | 2026-10-12 | ✅ HTTP 200 |
| 6 | `AudioParam`, zamanlanmış değer animasyonu (`setValueAtTime`, `linearRampToValueAtTime`) destekler — parametre automation'ın referansı | https://developer.mozilla.org/en-US/docs/Web/API/AudioParam | 2026-10-12 | ✅ HTTP 200 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §6 Pipeline mimari desenleri — karşılaştırma

Gerçek zamanlı ses işlemede pipeline'ın iki temel deseni vardır.

### §6.1 Desen A — Statik zincir (compile-time)

Düğüm sırası derleme zamanında sabitlenir; `processBlock` çağrıları inline
edilebilir; şablon tabanlı.

```cpp
// Taslak — CoreMusic hedef mimari (C++20)
template <typename... Nodes>
class StaticChain {
    std::tuple<Nodes...> nodes;
public:
    void prepare(const ProcessSpec& spec) {
        std::apply([&](auto&... n) { (n.prepare(spec), ...); }, nodes);
    }
    void process(AudioBuffer<float>& buf) noexcept {
        std::apply([&](auto&... n) { (n.process(buf), ...); }, nodes);
    }
};
```

| Artı | Eksi |
|---|---|
| Sıfır dispatch maliyeti (inline) | Sıra değişimi → yeniden derleme |
| Sıfır alokasyon (düğümler üyede) | Yeni düğüm türü eklemek template'i büyütür |
| Cache dostu (tutarlı yerleşim) | Debug/ölçümde esneklik düşük |

### §6.2 Desen B — Dinamik zincir (runtime)

Düğümler arayüz (interface) üzerinden çağrılır; çalışma anında ekleme/çıkarma
mümkün.

```cpp
// Taslak — referans desen
class DynamicChain {
    std::vector<Processor*> nodes;  // baştan allocate
public:
    void process(AudioBuffer<float>& buf) noexcept {
        for (auto* n : nodes) n->process(buf);
    }
};
```

| Artı | Eksi |
|---|---|
| Sıra çalışma anında değişir | Virtual dispatch (~1-2 ns/düğüm) |
| Yeni efekt ekleme kolay | `std::vector` reallokasyon riski (mitigasyon: sabit kapasite) |
| Test/ölçüm esnek | Cache buddy pattern kırılabilir |

### §6.3 CoreMusic kararı (taslak)

C++20 + `constexpr` ile statik zincir (Desen A) ana hat; dinamik override
yalnız debug/ölçüm modunda. Karar kesinleşmesi Q3'e bağlı (§3) —
`⚠️ VERIFICATION REQUIRED` (sıra kararı verilmedi).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 İki fazlı desen — setup ve realtime ayrımı

DSPark örneği (§2) ve CoreMusic kuralı aynı: `prepare` (allocate/configure) ile
`processBlock` (realtime, allocate yok) net ayrılmıştır.

### §7.1 `prepare` fazı (realtime değil)

| İş | Ayrıntı |
|---|---|
| Bellek allocate | Tüm buffer'lar (FIR taps, delay line, reverb IR, worklet ring) |
| Örnekleme oranı yapılandırma | Her düğüm `fs`'ye göre katsayı hesaplar (biquad dönüşümü vb.) |
| Blok boyutu yapılandırma | 512 (CoreMusic sabiti) |
| Kanal sayısı yapılandırma | 1 (mono) / 2 (stereo) / 8 (7.1 veya 8.1) |
| Parametre başlangıç değeri | `setValueAtTime` benzeri başlangıç (§5, satır 6) |
| Durum sıfırlama | Filter state, reverb hatları, kompresör envanter |

`prepare` çağrıldığında realtime thread **durmuş olmalıdır** (ASIOExclusiveLock
altında) — ses thread'i `prepare` sırasında blok çağırmaz.

### §7.2 `processBlock` fazı (realtime, noexcept, zero-allocation)

| Kural | Gerekçe |
|---|---|
| `malloc`/`new`/`delete` YASAK | Gerçek zamanlı thread'de allocate → priority inversion / XRUN |
| Mutex/lock YASAK | Bloklanma → ses boşluğu |
| `std::string`/`std::vector` push YASAK | Kapasite aşımında allocate |
| `noexcept` zorunlu | İstisna yayılımı stack unwinding realtime'de tahmin edilemez süre |
| Parametre atomik okuma | `std::atomic<float>` relaxed ya da lock-free mailbox |
| NaN/Inf temizliği | DSP hataları yayılımı engelle (bir düğümde NaN → tüm hat NaN) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 CoreMusic pipeline latans bütçesi

512 örnek / 48 kHz = 10,67 ms blok süresi. ASIO Exclusive Lock altında gerçek
toplam latans = blok + donanım + iş zinciri.

### §8.1 Latans bileşenleri (tahmini)

| Bileşen | Tahmini | Kaynak |
|---|---|---|
| ASIO driver buffer round-trip | ~10,67 ms (512) + driver iç buffer | Sartname (512/48k) |
| `baseLatency` (donanım giriş→çıkış) | Cihazbağımlı — `⚠️ VERIFICATION REQUIRED` | §5, satır 3 |
| `outputLatency` (OS kuyruk) | Cihaz/OS bağımlı — `⚠️ VERIFICATION REQUIRED` | §5, satır 4 |
| DSP zinciri iş süresi (EQ+comp+lim) | Ölçülmedi — hedef < %20 blok (2,1 ms) | Ölçüm PLANNED |
| Toplam hedef | < 20 ms (interactive) · < 15 ms (studio) | Sartname hedefi |

### §8.2 Latans ölçüm yöntemi (PLANNED)

1. `baseLatency`/`outputLatency` WebAudio karşılığı → CoreMusic'te ASIO
   `latency` yapısı + `asioGetLatencies` (⚠️ Steinberg ASIO SDK API —
   doğrulama: ASIO SDK 2.3.4 kurulu değil, `VERIFICATION REQUIRED`).
2. DSP zinciri: her düğüm başına `std::chrono::steady_clock` ile blok süresi
   ölçümü (yalnız debug/ölçüm modunda; release'de kapalı).
3. Round-trip: bilinen impulse üret → loopback kaydet → gecikme ölç
   (`measurement` dalı).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 WebAudio iş emsali — process() sözleşmesi

WebAudio `AudioWorkletProcessor.process()` (§5, satır 2), WebAudio'daki
"pipeline blok" emsalidir. CoreMusic C++ moturu bu sözleşmeye paralel kurulur.

### §9.1 `process(inputs, outputs, parameters)` sözleşmesi

```
inputs  : Float32Array[][] — kanal × örnek (kaynak blok)
outputs : Float32Array[][] — kanal × örnek (hedef blok)
parameters: AudioParam map — automation değeri (§5, satır 6)
dönüş   : true → node yaşamaya devam; false → DSP durur (kaynak bitti)
```

### §9.2 CoreMusic eşlemesi

| WebAudio | CoreMusic C++ |
|---|---|
| `AudioWorkletProcessor` | `DspNode` arayüzü |
| `process(inputs, outputs, params)` | `process(AudioBuffer<float>& in, AudioBuffer<float>& out) noexcept` |
| `parameters: AudioParam` map | `DspParams` atomik üyeleri |
| dönüş `false` (son) | `bool finished()` — kaynak bitince zincirden çıkar |
| `sampleRate` (context) | `ProcessSpec.sampleRate` (48000.0) |
| `renderQuantum` (128 örnek) | CoreMusic blok 512 (sartname — WebAudio 128'den farklı) |

> ⚠️ **Not:** WebAudio `renderQuantum` 128 örnektir; CoreMusic 512 kullanır.
> Emsal olarak sözleşme (girdi/çıktı/param) alınır, blok boyutu FARKLIDIR.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 Hata yayılımı ve zarf (fault propagation)

Bir düğümde hata (NaN, Inf, overflow) tüm zincire yayılabilir; zarf kontrolü
pipeline seviyesinde yapılmalıdır.

### §10.1 Zarf kontrol deseni (blok sonu)

```cpp
// Taslak — blok sonu zarf kontrolü (noexcept, zero-alloc)
bool envelopeCheck(const AudioBuffer<float>& buf) noexcept {
    for (int c = 0; c < buf.getNumChannels(); ++c) {
        const float* d = buf.getReadPointer(c);
        for (int i = 0; i < buf.getNumSamples(); ++i) {
            if (!std::isfinite(d[i])) return false;  // NaN/Inf → zarf ihlali
        }
    }
    return true;
}
```

### §10.2 Zarf ihlali tepkisi

| İhlal | Tepki |
|---|---|
| NaN/Inf tespiti | Sus (mute) + hata sayacı atomik artır; UI'a `DspFault` bayrağı |
| DC offset > 0,5 V (kural 10) | Koruma rölesi tetikleme sinyali (donanım) |
| Clip (±1,0 aşıldı) | Limiter aşım sayacı; sinyale dokunma (limiter zaten sınırlar) |
| Süre aşımı (blok > 10,67 ms) | XRUN sayacı; teşhis log'u (yalnız debug) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 Parametre otomasyonu — audio thread ↔ UI thread

UI thread parametre değiştirir; audio thread okur. Kilit yasak; lock-free mailbox
kullanılır.

### §11.1 Lock-free mailbox deseni (taslak)

```cpp
// UI → audio: tek yönlü atomik publish
struct alignas(64) ParamMailbox {
    std::atomic<uint32_t> seq{0};   // publish sırası (çift tampon indeksi)
    float values[2][ParamCount];    // double buffer
    // UI: values[!seq&1] doldur → seq.fetch_add(1, release)
    // Audio: seq.load(acquire) → values[seq&1] oku (yalnız blok başında)
};
```

| Kural | Gerekçe |
|---|---|
| Audio thread yalnız blok BAŞINDA okur | Blok ortasında parametre değişimi tutarsızlık üretir |
| UI thread write yapar, publish `release` | Hafıza sırası: write → publish (release-acquire) |
| `alignas(64)` | Cache line false sharing önlenir |
| Kapasite sabit (double buffer) | Reallocasyon yok |

### §11.2 WebAudio `AudioParam` emsali

WebAudio `setValueAtTime` / `linearRampToValueAtTime` (§5, satır 6) sample-accurate
otomasyon sağlar; CoreMusic'te sample-accurate ramp blok-içi lineer interpolasyon
ile yapılabilir (yaklaşık), ya da `AudioParam` benzeri zamanlanmış kuyruk
(ek karmaşıklık — faz 2).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 Ölçüm hattı — pipeline dışı test deseni

Gerçek zamanlı zincir offline render ile doğrulanır; WebAudio
`OfflineAudioContext` (§5, satır 5) emsalidir.

| Adım | İşlem |
|---|---|
| 1 | `OfflineAudioContext(2, 48000, 48000)` emsali: `OfflineProcessor` (CoreMusic: `processBlock` döngüsü, saat yok) |
| 2 | Bilinen test sinyali (impulse, sine sweep, beyaz gürültü) besle |
| 3 | Çıkışı kaydet ( WAV / raw float ) |
| 4 | İstatistikler: RT60 (reverb), THD+N (EQ/comp), gain reduction (limiter) |
| 5 | Hedefle karşılaştır (tolerans) — başarısızsa pipeline sürümü geri alınır |

> ⚠️ Sadece offline render; gerçek ASIO cihaz testi ayrı fazdır
> (`measurement` dalı).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 Pipeline düğüm bağımlılık grafiği (K004 iç)

K004-dsp altındaki pipeline'a dahil düğümler ve öncelik sırası (kaynak:
`dsp-master.md` §2.2 ve ilgili kapsamlar; sıra Q3 kararı ile kesinleşir).

| Sıra | Düğüm | Dal | Öncelik | Durum |
|---|---|---|---|---|
| 1 | EQ (biquad) | `equalizer/` | Yüksek | Faz 1 |
| 2 | Dynamics (comp/lim) | `dynamics/`, `limiter/` | Yüksek | Faz 1 |
| 3 | Delay | `delay/` | Orta | Faz 1.5 |
| 4 | Reverb | `reverb/` | Orta | Faz 1.5-2 |
| 5 | Spatial (pan/panner) | `spatial-audio/` | Düşük-Orta | Faz 2 |
| 6 | Measurement tap | `measurement/` | Cross-cutting | Pasif izleme (inline değil) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 ASIO pipeline entegrasyon notu (CoreMusic hattı)

CoreMusic gerçek zamanlı hattı ASIO Exclusive Lock altında çalışır (kural 9).
Pipeline bu hattın içindedir; ASIO callback'i tek blok işleyicidir.

### §14.1 ASIO callback → pipeline çağrısı deseni (taslak)

```cpp
// ASIO bufferSwitch callback eşlemesi (noexcept, zero-alloc)
void ASIOBufferSwitch(long doubleBufferIndex, ASIOBool directProcess) noexcept {
    // 1. Girdi kanal işaretçilerini hazırla (ASIO hazır)
    AudioBuffer<float> in(channels, doubleBufferIndex, 512);
    // 2. Pipeline.process(in, out) — statik zincir
    pipeline.process(in, out);
    // 3. Çıkış kanallarına kopyala (out → ASIO buffer)
    // 4. XRUN/zarf sayaçlarını atomik güncelle
}
```

| Kural | Uygulama |
|---|---|
| Callback tek thread (ASIO driver thread) | Pipeline bu thread'de; ayrı worker thread YOK |
| ASIO Exclusive Lock | Tek uygulama — CoreMusic dışında kullanıcı açamaz |
| 512 örnek blok | Callback başına tek `process()` çağrısı |
| Latans raporu | Her 1000 blokta (≈10,7 s) ortalama blok süresi atomik sayacı |

### §14.2 WASAPI fallback ilişkisi

ASIO cihaz kaybı durumunda (edge case §6 — AGENTS.md) WASAPI fallback başlar
(Windows SW Engineer domeni). Pipeline statik zinciri WASAPI blockAlign'ine
uyarlanır; `ProcessSpec.blockSize` yeniden `prepare` ile ayarlanır (§7.1).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 Pipeline performans bütçesi — CPU profili

512 örnek / 48 kHz → 10,67 ms blok süresi. Hedef: DSP zinciri bu sürenin
**< %20'sini** (< 2,13 ms) kullanmalı; kalanı OS/driver'a bırakılır.

### §15.1 Tahmini düğüm CPU maliyeti (512 örnek, 2 kanal)

| Düğüm | Tahmini CPU | Hesap yöntemi |
|---|---|---|
| Biquad EQ (5 bant, 2 ch) | ~0,15 ms | 512×2×5 × ~30 flops / (3 GHz × 4 flops/ns kabaca) |
| Compressor (2 ch) | ~0,08 ms | Envelope + gain hesap, blok-içi lineer |
| Limiter (2 ch) | ~0,05 ms | lookahead 32 örnek + gain |
| Delay (2 ch, 48000 örnek) | ~0,02 ms | Bellek erişim baskın |
| Reverb (FDN 4 hat, 2 ch) | ~0,20 ms | 4 gecikme hattı + Householder matris |
| Toplam (EQ+comp+lim+delay+reverb) | **~0,50 ms** | ≤ %4,7 blok — hedefin altında |

> ⚠️ **Bu tablo hesap-tahmindir**, gerçek ölçüm PLANNED (`measurement` dalı).
> `VERIFICATION REQUIRED` — gerçek CPU profili ölçülene kadar kesin sayılmaz.

### §15.2 Ölçüm Protokolü (PLANNED)

1. Release modda derle (`/O2`, `/fp:fast` — dikkat: `fp:fast` NaN davranışı
   değiştirir; zarf kontrolü §10 ile birlikte değerlendirilir).
2. Her düğüm başına `steady_clock` kronometre (yalnız ölçüm build'inde).
3. 10.000 blok (≈107 s) koş; ortalama + p99 latency raporla.
4. p99 > 2,13 ms ise bütçe ihlali — düğüm optimize edilir ya da çıkarılır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 Pipeline bypass ve true-bypass (CoreMusic notu)

Kullanıcı düğümü kapatmak istediğinde iki davranış mümkündür:

| Mod | Davranış | CPU | Latans | Karakter |
|---|---|---|---|
| Soft bypass (process içinde) | Düğüm işlenir ama çıktı = girdi | Aynı | Aynı | Phase shift yok |
| Hard bypass (sıra atlatma) | Düğüm zincirden çıkarılır | Düğüm kadar az | Blok az | Fark edilebilir |
| Mute (sus) | Çıkış sıfırlanır | Aynı | Aynı | — |

**CoreMusic kararı (taslak):** soft bypass (düğüm member'ı kalır ama
`enabled=false` → erken return). Sıra değişimi (hard bypass) yalnız pipeline
yeniden `prepare` çağrısıyla mümkün (realtime'de sıra değişmez — §6.1 statik
zincir kuralı).

```cpp
void process(AudioBuffer<float>& buf) noexcept override {
    if (!enabled.load(std::memory_order_relaxed)) return;  // soft bypass
    // ...normal işleme
}
```

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 Uygulama yol haritası (pipeline)

| Faz | Kapsam | Bağımlılık | Durum |
|---|---|---|---|
| Faz 0 | Statik zincir iskeleti (§6.1) + `ProcessSpec` + `AudioBuffer` | Yok | PLANNED |
| Faz 1 | EQ + compressor + limiter düğümleri (§13) | Faz 0 | PLANNED |
| Faz 1.5 | Delay + reverb (FDN) düğümleri | Faz 1 | PLANNED |
| Faz 2 | Offline render test hattı (§12) + zarf kontrolü (§10) | Faz 1 | PLANNED |
| Faz 2 | WASAPI fallback (§14.2) | ASIO kaybı senaryosu | PLANNED |
| Faz 3 | CPU profili + bütçe doğrulama (§15.2) | Tüm düğümler | PLANNED |

**Q3 (pipeline sırası) kararı:** §13 tablosu taslak; kesin sırası Q3 kapısında
kilitlenecek — bu dosyadaki sıralama öneridir, nihai değildir
(`⚠️ VERIFICATION REQUIRED`).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §18 Pipeline — test senaryoları (QA referansı)

CoreMusic pipeline'ı için QA senaryo taslağı (QA Engineer domeni; bu tablo
taslak'tır, kesin kabul kriteri QA kapısında sabitlenir).

| # | Senaryo | Girdi | Beklenen davranış | Doğrulama |
|---|---|---|---|---|
| 1 | Boş zincir (tüm düğümler bypass) | Sine 1 kHz, 0,5 amp | Çıkış ≡ girdi (byte-eşit, tolerance 1e-6) | Offline render |
| 2 | EQ 5 bant, kazanç +6 dB @ 1 kHz | Sine sweep | 1 kHz'de +6 dB, diğer frekanslar ±0,5 dB | FFT analiz |
| 3 | Compressor 4:1, threshold −12 dB | Sine, amp 0,9 | Gain reduction ≈ hedef | Envelope ölçüm |
| 4 | Limiter threshold −1 dBFS | Sine, amp 1,5 (clip) | Çıkış ≤ −1 dBFS; no overshoot | Peak ölçüm |
| 5 | Reverb RT60 = 1,5 s | Impulse | Schroeder RT60 ∈ [1,35; 1,65] s | Offline render |
| 6 | 8.1 kanal EQ | 8 kanal impulse | Tüm kanallarda tutarlı frekans yanıtı | Çok kanallı FFT |
| 7 | XRUN dayanıklılığı | 60 s beyaz gürültü | 0 XRUN (ASIO Exclusive Lock altında) | ASIO sayaç |
| 8 | Zarf ihlali (NaN enjeksiyonu) | NaN enjeksiyonu (unit test) | Sus + hata sayacı 1 | Unit test |
| 9 | Parametre otomasyonu (bloom) | 1000 parametre değişimi/s | No glitch, no click | Click ölçüm (transient) |
| 10 | Soft bypass karakteri | EQ bypass toggle | Çıkış = girdi (faz kayması yok) | Karşılaştırma |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §19 Referanslar ve ilgili K-node

| Kaynak | K-node / dosya | İlişki |
|---|---|---|
| `../dsp-master.md` | K004-dsp anayasa | §2.2 pipeline yerleşimi · §3 satır 1 |
| `../equalizer/equalizer-kapsam.md` | EQ kapsamı | Faz 1 düğüm girdisi |
| `../dynamics/dynamics-kapsam.md` | Dynamics kapsamı | Faz 1 comp/lim girdisi |
| `../reverb/reverb-kapsam.md` | Reverb kapsamı | Faz 1.5 girdisi |
| `../measurement/measurement-kapsam.md` | Ölçüm kapsamı | Offline render veri kaynağı (§12) |
| K003-audio | Graph anayasası | Pipeline K003 graph'ına eklenir (§3) |

**Web referansları (doğrulanmış, 2026-10-12):**
`AudioWorkletNode` · `AudioWorkletProcessor` · `AudioContext/baseLatency` ·
`AudioContext/outputLatency` · `OfflineAudioContext` · `AudioParam`
(MDN Web APIs — §5).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §20 Bu dosyanın kapsam dışıları

Aşağıdaki konular bu dosyaya dahil DEĞİLDİR; ilgili dosyalara yönlendirilir:

| Konu | Dosya |
|---|---|
| EQ katsayı hesabı, biquad dönüşümü | `../equalizer/equalizer-ornekler.md` |
| Kompresör zarf eğrisi detayı | `../dynamics/dynamics-dogrulama.md` |
| Limiter lookahead mühendisliği | `../limiter/limiter-ornekler.md` |
| Reverb algoritması (FDN/convolution) | `../reverb/reverb-ornekler.md` |
| ASIO/WASAPI driver katmanı | K003-audio (driver) + Windows SW Engineer |
| 8.1 hoparler yerleşimi | `../spatial-audio/spatial-audio-kapsam.md` |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
