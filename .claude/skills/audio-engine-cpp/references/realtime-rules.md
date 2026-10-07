# Realtime C++ Kuralları — Zero-Allocation · Lock-Free · Noexcept · 64-byte

> **Otorite:** `.ai/CLAUDE.md` §19 ("C++ Guardrails: Zero-allocation, lock-free, noexcept,
> cache-line alignment (64-byte)") · `.ai/PROJECTS.md` §7.1.1 ("Zero-allocation, lock-free
> ring buffer") · §7.1.7 ("Gerçek Zamanlı: Evet — ASIO callback içinde").
> Bu dosya SKILL.md §4 üzerinden bağlayıcıdır; vault ile çelişirse vault kazanır.

## §1 Kural Tablosu

| # | Kural | Kapsam | İhlal sonucu |
|---|-------|--------|--------------|
| R1 | **Zero-allocation** — audio callback ve tüm hot-path içinde `new`/`delete`/`malloc`/`free`, heap büyüten konteyner işlemleri, `std::vector::push_back` (kapasite dışı), `std::string` büyütme **YASAK** | ASIO `bufferSwitch` / processBlock | Underrun → xrun → click/pop |
| R2 | **Lock-free** — callback içinde `std::mutex`/`lock_guard`/`condition_variable` **YASAK**; parametre paylaşımı SPSC atomik ring buffer veya atomik snapshot ile | Callback ↔ UI/thread sınırı | Priority inversion → ölümcül gecikme |
| R3 | **noexcept** — callback ve tüm DSP `process()` fonksiyonları `noexcept`; hot-path'de exception fırlatma/yakalama **YASAK** | Tüm K3 sıcak yolu | std::terminate veya tahmin edilemeyen zamanlama |
| R4 | **Cache-line alignment (64-byte)** — thread'lerin paylaştığı sıcak atomikler `alignas(64)` + padding (false sharing yok) | Ring buffer göstergeleri, param atomikleri | False sharing → beklenmeyen gecikme |

## §2 Yanlış vs Doğru

### R1 — Callback içinde tahsis

```cpp
// ❌ YANLIŞ — audio callback içinde heap tahsisi (zero-allocation ihlali)
void audioCallback(AudioBuffer<float>& buffer) {
    std::vector<float> temp(buffer.getNumSamples()); // tahsis! (R1)
    temp.assign(buffer.getWritePointer(0),
                buffer.getWritePointer(0) + buffer.getNumSamples());
    // ...
}

// ✅ DOĞRU — önceden tahsis edilmiş tampon, çalışma anında yalnız span/slice
class Processor {
    std::vector<float> scratch_;               // prepare() içinde TEK SEFER tahsis
public:
    void prepare(int maxBlockSize) {
        scratch_.resize(static_cast<size_t>(maxBlockSize)); // prepare hot-path DIŞI
    }
    void process(float* data, int n) noexcept {            // n <= maxBlockSize
        std::span<float> in(data, static_cast<size_t>(n)); // tahsis yok
        // ... DSP ...
    }
};
```

### R2 — Kilitli paylaşım vs atomik ring buffer

```cpp
// ❌ YANLIŞ — callback mutex bekler (lock-free ihlali)
std::mutex g_m;
float g_targetGain = 1.0f;
void audioCallback(...) {
    std::lock_guard<std::mutex> lk(g_m);   // (R2) callback'te kilit ASLA yok
    applyGain(block, g_targetGain);
}

// ✅ DOĞRU — SPSC atomik ring buffer (lock-free, güç-2 boyut, 64B padding)
template <typename T, size_t Capacity>   // Capacity = 2^n
class alignas(64) SpscRing {
    static_assert((Capacity & (Capacity - 1)) == 0, "power-of-two");
public:
    bool push(const T& v) noexcept {                  // writer (UI thread)
        const auto w = write_.load(std::memory_order_relaxed);
        const auto r = read_.load(std::memory_order_acquire);
        if (w - r >= Capacity) return false;          // dolu → drop, bloklanma yok
        buf_[w & (Capacity - 1)] = v;
        write_.store(w + 1, std::memory_order_release);
        return true;
    }
    bool pop(T& out) noexcept {                       // reader (audio callback)
        const auto r = read_.load(std::memory_order_relaxed);
        const auto w = write_.load(std::memory_order_acquire);
        if (r == w) return false;                     // boş
        out = buf_[r & (Capacity - 1)];
        read_.store(r + 1, std::memory_order_release);
        return true;
    }
private:
    alignas(64) std::atomic<size_t> read_{0};         // false sharing yok (R4)
    alignas(64) std::atomic<size_t> write_{0};
    alignas(64) std::array<T, Capacity> buf_{};
};
```

### R3 — noexcept sınırı

```cpp
// ❌ YANLIŞ — sıcak yolda throw / noexcept olmayan callback
void processBlock(AudioBuffer<float>& b) {           // noexcept değil (R3)
    if (mode_ == Mode::unknown) throw std::runtime_error("bad mode");
}

// ✅ DOĞRU — noexcept; hata modu sustur/sıfırla; doğrulama prepare() tarafında
void processBlock(AudioBuffer<float>& b) noexcept {
    if (b.getNumSamples() > maxBlockSize_) {         // güvenli sınır
        b.clear();                                   // fail-silent, throw yok
        return;
    }
    // ... DSP ...
}
```

### R4 — Cache-line padding

```cpp
// ❌ YANLIŞ — iki atomik aynı cache-line'da (false sharing)
struct Bad {
    std::atomic<bool> callbackActive{false};   // UI yazar
    std::atomic<int>  xrunCount{0};            // callback yazar → aynı satır!
};

// ✅ DOĞRU — 64-byte hizalama + pad
struct alignas(64) HotFlags {
    std::atomic<bool> callbackActive{false};
    char pad0_[64 - sizeof(std::atomic<bool>)]{};
    std::atomic<int>  xrunCount{0};
    char pad1_[64 - sizeof(std::atomic<int>)]{};
};
```

## §3 Hot-Path Kaçınılacakları (özet)

- Dosya I/O, `printf`/log string formatlama, `dynamic_cast` zinciri, sistem çağrısı
  (callback içinde) → arka plan thread'ine önceden tahsis edilmiş lock-free kuyrukla taşınır.
- Parametre güncellemesi blok sınırında atomik snapshot'lanır; mid-callback mutation yok.
- `prepare()`/`resize()` (tahsis) ile `process()` (tahsis yok) net ayrılır.
- K2 sürücü sözleşmesi: ASIO doğrudan lock-free callback ile bağlanır; bu callback imzası
  K3'e aynen yansıtılır (`.ai/ecosystem/asio-wasapi-rehber.md` §3.1).

*CoreMusic Skill v3.0 — references/realtime-rules.md — Updated: 2026-10-07*