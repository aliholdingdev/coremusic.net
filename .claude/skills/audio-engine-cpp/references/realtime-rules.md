# Realtime C++ Kuralları — Zero-Allocation · Lock-Free · Noexcept · 64-byte · Publish

> **Otorite:** `.ai/CLAUDE.md` §19 ("C++ Guardrails: Zero-allocation, lock-free, noexcept,
> cache-line alignment (64-byte)") · `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` §3 ·
> **web teyidi:** JUCE `AudioProcessor::processBlock` dokümanı (değişken blok, 0 örnek,
> UI etkileşimi yasak — SKILL.md §4 `sources.md` S1).
> Bu dosya SKILL.md §4 üzerinden bağlayıcıdır; vault ile çelişirse vault kazanır,
> vault↔disk çelişkisinde SKILL.md §10.1 kapısı işletilir.
>
> **Disk referansı (birebir desenler):** `include/neva/eq_chain.h` (R5 publish+ramp),
> `include/neva/spsc_ring.h` (R2/R4 SPSC), `include/neva/iaudio_backend.h` (R3 noexcept alias).

## §1 Kural Tablosu (R1–R7 — SKILL.md §6 özeti burada tam metin)

| # | Kural | Kapsam | İhlal sonucu |
|---|-------|--------|--------------|
| R1 | **Zero-allocation** — callback ve tüm hot-path içinde `new`/`delete`/`malloc`/`free`, heap büyüten konteyner işlemleri (`std::vector::push_back` kapasite dışı, `std::string` büyütme), çalışma anında açılan `make_unique`/`make_shared` **YASAK**; tahsis yalnız `prepare()` / `open()` / `resize()` (hot-path DIŞI) | `process()` · `render()` · `RenderCallback` · ASIO `bufferSwitch` | Underrun → xrun → click/pop |
| R2 | **Lock-free** — callback içinde `std::mutex`/`lock_guard`/`unique_lock`/`condition_variable`/`CriticalSection`/`wait`/`sleep` **YASAK**; thread'ler arası paylaşım `std::atomic` publish veya SPSC ring ile | Callback ↔ UI/decode thread sınırı | Priority inversion → ölümcül gecikme |
| R3 | **noexcept** — callback ve tüm DSP `process()` fonksiyonları `noexcept`; sıcak yolda `throw`/`try`-`catch` **YASAK**; hata modu fail-silent (sustur/sıfırla), doğrulama `prepare()` tarafında | Tüm K3 sıcak yolu + `RenderCallback` | `std::terminate` veya tahmin edilemeyen zamanlama |
| R4 | **Cache-line alignment (64-byte)** — thread'lerin paylaştığı sıcak atomikler `alignas(64)`; üretici/tüketici indeksleri **ayrı** cache-line'da (false sharing yok) | Ring buffer göstergeleri, param atomikleri | False sharing → beklenmeyen gecikme |
| R5 | **Atomik publish / blok başı snapshot** — katsayı ve parametre üretimi RT-DIŞI (`set_gain` benzeri); callback blok BAŞINDA tek noktadan `memory_order_acquire` ile okur; blok içinde mutation yok (tornear riski) | Parametre geçişi her yerde | Tearing → bozuk katsayı → distortion/distorsiyon |
| R6 | **Değişken blok boyutu** — blok boyutu her callback'te farklı olabilir; 0 örnekli callback mümkündür → tüm algoritma `n` ile sınırlı ve 0-güvenli olmalı | JUCE `processBlock` + her backend | Click/crash (JUCE dokümanı: "you're going to get clicks and crashes") |
| R7 | **Blok dışı işler kuyruğa** — dosya I/O, log/string formatlama, `printf`, `dynamic_cast`/`typeid`, `std::function` invokasyonu, UI etkileşimi, `shared_ptr` son kopya düşüşü (dtor riski) callback'te **YASAK**; SPSC kuyrukla arka plan thread'ine taşınır | Tüm callback | Deadline aşımı → xrun |

## §2 Yanlış vs Doğru (tam çiftler)

### R1 — Callback içinde tahsis

```cpp
// ❌ YANLIŞ — audio callback içinde heap tahsisi (zero-allocation ihlali)
void audioCallback(AudioBuffer<float>& buffer) {
    std::vector<float> temp(buffer.getNumSamples());      // tahsis! (R1)
    temp.assign(buffer.getWritePointer(0),
                buffer.getWritePointer(0) + buffer.getNumSamples());
    // ...
}

// ❌ YANLIŞ — daha sinsi: kapasite YOKSA push_back de tahsis eder
void push(const float* data, std::size_t n) {
    for (std::size_t i = 0; i < n; ++i) queue_.push_back(data[i]);  // doluysa realloc (R1)
}

// ✅ DOĞRU — önceden tahsis edilmiş tampon; çalışma anında yalnız span/slice
class Processor {
    std::vector<float> scratch_;                        // prepare() içinde TEK SEFER tahsis
public:
    void prepare(std::size_t maxBlockSize) {            // hot-path DIŞI
        scratch_.resize(maxBlockSize);
    }
    void process(float* data, std::size_t n) noexcept { // n <= maxBlockSize (R6 guard)
        if (n > scratch_.size()) { n = scratch_.size(); }
        std::span<float> in(data, n);                   // tahsis yok
        // ... DSP ...
    }
};

// ✅ DOĞRU — sabit boyutlu konteyner (diskteki desen: eq_chain.h std::array, spsc_ring.h std::array)
std::array<float, 4096> scratch_{};
```

### R2 — Kilitli paylaşım vs atomik publish / SPSC ring

```cpp
// ❌ YANLIŞ — callback mutex bekler (lock-free ihlali)
std::mutex g_m;
float g_targetGain = 1.0f;
void audioCallback(...) {
    std::lock_guard<std::mutex> lk(g_m);                // (R2) callback'te kilit ASLA yok
    applyGain(block, g_targetGain);
}

// ✅ DOĞRU A — atomik publish + blok başı acquire snapshot (R5; eq_chain.h deseni)
std::atomic<float> gain_target_{1.0f};                  // RT-disi yazar (release)
void render(float* out, std::uint32_t frames) noexcept {
    const float g = gain_target_.load(std::memory_order_acquire);  // blok başı tek okuma
    applyGain(out, frames, g);                          // blok boyunca sabit
}

// ✅ DOĞRU B — SPSC ring (üretici decode thread, tüketici RT; spsc_ring.h birebir desen)
template <typename T, std::size_t Capacity>
class SpscRing {
    static_assert(Capacity >= 2 && (Capacity & (Capacity - 1)) == 0, "power-of-two");
public:
    bool push(const T& v) noexcept {                    // üretici (RT-dışı)
        const std::size_t w = write_.load(std::memory_order_relaxed);
        const std::size_t next = (w + 1) & kMask;
        if (next == read_.load(std::memory_order_acquire)) return false;  // dolu → drop
        buf_[w] = v;
        write_.store(next, std::memory_order_release);
        return true;
    }
    bool pop(T& v) noexcept {                           // tüketici (RT callback)
        const std::size_t r = read_.load(std::memory_order_relaxed);
        if (r == write_.load(std::memory_order_acquire)) return false;   // boş
        v = buf_[r];
        read_.store((r + 1) & kMask, std::memory_order_release);
        return true;
    }
private:
    static constexpr std::size_t kMask = Capacity - 1;
    alignas(64) std::atomic<std::size_t> write_{0};     // (R4) üretici cache-line
    alignas(64) std::atomic<std::size_t> read_{0};      // (R4) tüketici cache-line
    std::array<T, Capacity> buf_{};                     // (R1) tahsis yok
};
```

**SPSC'nin sınırı (H001 sınıfı yanlış kullanım):** iki+ üretici veya iki+ tüketici → SPSC
yanlıştır; MPSC/MPMC kuyruk gerekir (seçim ve kütüphane kanıtı yoksa `⚠️ VERIFICATION REQUIRED`).
Kapasite doluysa **bloklanma yok**: `push` false döner, üretici atar/ilerletir (drop-policy bilinçli yazılır).

### R3 — noexcept sınırı

```cpp
// ❌ YANLIŞ — sıcak yolda throw / noexcept olmayan callback
void processBlock(AudioBuffer<float>& b) {              // noexcept değil (R3)
    if (mode_ == Mode::unknown) throw std::runtime_error("bad mode");
}

// ✅ DOĞRU — noexcept; hata modu sustur/sıfırla; doğrulama prepare() tarafında
void processBlock(AudioBuffer<float>& b) noexcept {
    if (static_cast<std::size_t>(b.getNumSamples()) > maxBlockSize_) {
        b.clear();                                      // fail-silent, throw yok
        return;
    }
    // ... DSP ...
}

// ✅ DOĞRU — arayüz alias'ı baştan noexcept (disk: iaudio_backend.h RenderCallback)
using RenderCallback = void (*)(void* userData, float* out, std::uint32_t frames,
                                std::uint16_t channels) noexcept;
```

`dynamic_cast`/`typeid` (RTTI) de buraya girer: başarısız kopya → exception → ihlal; tip
değişimi `prepare()`'da, çalışma anında tip yok (R7).

### R4 — Cache-line padding (false sharing)

```cpp
// ❌ YANLIŞ — iki atomik aynı cache-line'da (false sharing)
struct Bad {
    std::atomic<bool> callbackActive{false};            // UI yazar
    std::atomic<int>  xrunCount{0};                     // callback yazar → aynı satır!
};

// ✅ DOĞRU — 64-byte hizalama + pad
struct alignas(64) HotFlags {
    std::atomic<bool> callbackActive{false};
    char pad0_[64 - sizeof(std::atomic<bool>)]{};
    std::atomic<int>  xrunCount{0};
    char pad1_[64 - sizeof(std::atomic<int>)]{};
};

// ✅ DOĞRU — sınıflaşmış hali (spsc_ring.h): her indeks kendi alignas(64) satırında
```

`sizeof` sabitiyle yastıklama (`char pad_[64 - sizeof(T)]`) taşımalı değil, sabit büyüklük
için uygundur; atomik **dizileri** için her eleman ayrı satıra alınır (eq_chain yorumu:
"`biquads_` alignas(64) — L1 uyumlu").

### R5 — Parametre geçişi: katsayı RT-dışı, ramp RT-içi (click yasağı)

```cpp
// ❌ YANLIŞ — blok ORTASında parametre değiştirmek (anlık sıçrama → click)
void process(float* io, std::size_t n) noexcept {
    for (std::size_t i = 0; i < n; ++i) {
        if (i == 64) set_gain_now(ui_gain_);            // blok içi mutation (R5) + click
        io[i] *= gain_;
    }
}

// ✅ DOĞRU — RT-dışı katsayı üretimi + atomik publish + per-block lineer ramp
// (disk: eq_chain.h — set_gain() RT-disi hedef yazar, process() blok başı acquire ile
//  seq farkını görür, ramp_from→ramp_to'yu blok boyunca lineer kaydırır; ramp 5–20 ms)
void process(float* L, float* R, std::size_t n) noexcept {
    const std::uint32_t seq = pending_.load(std::memory_order_acquire);  // blok başı
    if (seq != applied_) { beginRamp(); applied_ = seq; }
    advanceRamps();                                   // blok içinde lineer geçiş (click yok)
    processBands(L, R, n);                            // tahsis/lock/log YOK
}
```

### R6 — Değişken / sıfır blok (JUCE dokümanı S1)

```cpp
// ❌ YANLIŞ — sabit 256 varsayımı (prepare'da verilen tahmin HER ZAMAN doğru değil)
void process(float* io) noexcept {
    for (int i = 0; i < 256; ++i) io[i] *= g;          // n < 256 ise taşma!

// ✅ DOĞRU — n her çağrıda gelir; 0 dahil güvenli; tahmin prepare'daki "en az" değerdir
void process(float* io, std::size_t n) noexcept {
    if (n == 0) return;                                // 0 örnekli callback mümkün (S1)
    // ... yalnız [0, n) ...
}
```

### R7 — Blok dışı iş (kuyruk taşıması)

```cpp
// ❌ YANLIŞ — callback içinde log/I/O (R1+R7 ihlali)
void render(...) noexcept {
    log_.info("frames=%u", frames);                    // string formatlama + dosya I/O
}

// ✅ DOĞRU — olay SPSC kuyruğa yazılır, arka plan thread'i formatlar/yazar
struct EngineEvent { std::uint32_t code; std::uint32_t a; };
// render(): events_.push({kXrun, n});                 // tahsisiz, kilitsiz, non-blocking
// log thread(): while (events_.pop(e)) write(e);       // formatlama RT-dışı
```

## §3 prepare() / process() Ayrımı (tek doğru model)

| | `prepare()` / `open()` (RT-dışı) | `process()` / `render()` (RT) |
|---|---|---|
| Tahsis | ✅ tek sefer: buffer, kapasite, tablo, FFT plan | ❌ asla (R1) |
| Lock | ✅ sürücü kilidi, cihaz açma | ❌ asla (R2) |
| Doğrulama | ✅ SR, kanal, kapasite sınırı | yalnız fail-silent guard (R3) |
| Log/I/O | ✅ | ❌ — kuyruk (R7) |
| Parametre | ✅ katsayı üret + publish | acquire snapshot + ramp (R5) |
| Sıfırlama | `reset()`/`prepare()` çağrısı | durum sürekliliği korunur (anlık sıfırlama click yapar) |

## §4 Hata Masası (RT — belirti → kök neden → düzeltme)

| Belirti | Olası kök neden (önce ölç) | Düzeltme |
|---------|---------------------------|----------|
| Click/pop | (a) blok sınırında katsayı sıçraması (b) prepare/reset eksik (c) taşan `n` | ramp 5–20ms (R5) · `reset()` geçişte · `n` guard (R6) |
| Xrun | (a) tahsis (b) lock (c) CPU %100 (d) buffer çok küçük | §2 kalıntılarını temizle → sonra buffer (latency-targets §5) |
| CPU ani sıçraması | Denormal (sabit çok küçük değer) | FTZ/DAZ: `_mm_setcsr(_mm_getcsr() \| 0x8040)` (SSE2, prepare'da) |
| NaN/Inf yayıldı | Bölme/sqrt domain, geçersiz katsayı | `std::isfinite` guard; katsayı doğrulama `prepare()`'da (test_biquad kapısı gibi) |
| Kuyruk dolu sesi kesiyor | Üretici politikası yok / kapasite yanlış | bilinçli drop policy + sayacla raporla (SPSC false → drop) |
| Priority inversion şüphesi | Beklenmedik uzun callback | xrun sayacı + `steady_clock` süre ölçümü (latency-targets §4) |

## §5 Denetim Listesi (teslim öncesi — madde madde, "kod yazıldı" ≠ "geçti")

1. §6.2 on maddesi (SKILL.md) bu diff için satır satır işaretlendi mi?
2. `prepare()` dışında kalan **yeni** tahsis var mı? (`new`, `resize`, `push_back`, `make_*`)
3. Yeni paylaşılan atomik `alignas(64)` mı, tek thread'e mi ait?
4. Parametre yolu tek publish → tek acquire mi; blok içi mutation var mı?
5. Yeni callback/imza `noexcept` mı (alias üzerinden bile olsa)?
6. `n` değişkenliği test edildi mi (1, 0, max-1, max, max+1)?
7. `SpscRing` kapasite güç-2 mi (`static_assert` zorunlu), doluysa davranış tanımlı mı?
8. Yeni log/metric yolu kuyruk üzerinden mi (RT-dışı mi)?
9. Denormal koruması prepare'da mı (SSE2 varsa)?
10. ctest + §6.2 denetimi **çalıştırıldı ve çıktı raporlandı** mı (kanıtsız bitti sayılmaz)?

*CoreMusic Skill v3.0 — references/realtime-rules.md — Updated: 2026-10-21*
