---
name: audio-engine-cpp
description: "Use when touching Neva Engine, JUCE 9, ASIO, DSP chain, or realtime C++ audio code — Tetikleyiciler: 'C++ ses motoru', 'DSP kodla', 'ASIO gecikme', 'EQ/reverb ekle'."
license: MIT
metadata:
  version: 4.0.0
  previous-version: 3.0.0
  format: claude-skill-v3
  author: Bayram Ali (ULTRATHINK Engineering)
  category: audio-engine
  tags: [cpp20, juce, asio, dsp, realtime, coremusic]
  updated: 2026-10-21
---

# Audio Engine C++ — Neva Engine (C++20 Gerçek-Zamanlı Ses Motoru)

> **Bu dosya Katman-2 orkestrasyon dosyasıdır:** akış + bağlayıcı kural + link.
> Derinlik (sözleşmeler, tam kod çiftleri, ölçüm prosedürü, sürüm geçmişi)
> `references/` ve `examples/` içindedir; her yan dosya §4/§5 tablolarından
> linklenmiştir (orphan = hata, skill-maker §5-N4).

---

## §1 Genel Bakış & Honest Scope

CoreMusic **Neva Engine** (K3 ses işleme motoru) ve K2 sürücü katmanı üzerindeki
gerçek-zamanlı C++ ses kodu için uygulama skill'i. Kapsam: RT callback kuralları,
DSP zinciri (EQ → Reverb → Crossover → Limiter), sürücü/gecikme yapılandırması
(ASIO/WASAPI), kanal matrisi (1.0 → 8.1) ve test/ölçüm kapıları.

### §1.1 Bağlayıcı spec (kaynak: `.ai/CLAUDE.md` §19 · §24)

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| Dil / Framework / SDK hedefi | C++20 · JUCE 9.x · ASIO SDK 2.3.4 | CLAUDE §24 (sürüm teyidi → §10.2) |
| Örnek formatı / hız | Float32 (32-bit) · 48kHz standart (96/192kHz desteği) | CLAUDE §19 |
| Kanal | 1.0 Mono → 8.1 Surround (+1 LFE) | CLAUDE §19 · PROJECTS §2 |
| Gecikme hedefi | <10ms (ASIO) · <20ms (WASAPI) | CLAUDE §19 |
| DSP | 31-band EQ · 4 reverb modu · compressor · limiter (True Peak) · crossover | CLAUDE §19 · PROJECTS §2 |
| Kalite hedefi | THD+N <%0.005 · SNR >105dB · 64-bit float yol haritası | PROJECTS §2 |
| C++ Guardrails | Zero-allocation · lock-free · noexcept · cache-line (64-byte) | CLAUDE §19 |
| Test hedefi | coverage ≥80% (min) / ≥90% (hedef) | PROJECTS §7.1.1 |

### §1.2 Disk durumu (kanıt — ölçüm 2026-10-21, tahmin yok)

| Yüzey | Disk gerçeği | Durum |
|-------|--------------|-------|
| C++ kaynağı | `app-cpp-projects/neva-engine/` → `include/neva/` (7 başlık) + `src/` (4 cpp) + `tests/` (3 cpp) + `CMakeLists.txt` | ✅ MEVCUT |
| Build | CMake ≥3.20 · C++20 · MSVC `/W4 /permissive- /Zc:__cplusplus /utf-8` · hedefler: `neva_engine` (STATIC) · `neva_session` (SHARED, C ABI) · 3 test exe | ✅ MEVCUT |
| 3. parti bağımlılık | CMakeLists satır 6: "3. parti bağımlılık YOK" → **depoda JUCE/ASIO SDK entegre DEĞİL** | ⚠️ PLANNED (entegrasyon yok) |
| Backend adapter | `wasapi_backend.cpp` + `null_backend.cpp` — **ASIO backend dosyası YOK** | ⚠️ ASIO backend PLANNED |
| Test framework | ctest + el-yazımı `<cassert>` kapıları (`biquad_eq_ring`, `eq_grids_gates`, `icy_parser_gates`) — **Google Test kurulu DEĞİL** | ⚠️ §10.1 çelişki |
| Donanım zinciri | ADR-038: XMOS XU316 + PCM3168A (PCM5122 REDDEDİLMİŞ) · AK4458 DAC · 8× Class AB | ✅ vault (hardware ADR) |

> **Honest scope kuralı:** Depoda JUCE/ASIO kodu yokken "JUCE ile şunu yazdım" / "ASIO
> callback'imiz çalışıyor" iddiası **yasaktır** (Zero-Hallucination). Kod yalnızca
> diskteki gerçek dosyalardan (`include/neva/*.h`, `src/*.cpp`) türetilir; vault spec'i
> hedefi anlatır, disk kodu gerçeği anlatır — çelişkide §10 kapısı işletilir.

### §1.3 Bu skill'in sınırları

| Kapsam | Kapsam dışı → nereye |
|--------|----------------------|
| RT callback / DSP / lock-free / gecikme kodu | Vault `.md` yazımı → `prompt-maker` / `.ai/.templates/` |
| K3 DSP zinciri + K2 backend sözleşmesi | Donanım devre/PCB/BOM → `hardware-electronics` · firmware/DSP çip → `dsp-firmware-engineer` |
| C++ test kapıları (ctest) | PHP/JS test → ilgili domain skill |
| Sürücü fallback mantığı (kod) | Gerçek cihaz envanteri iddiası → vault kanıtı yoksa `UNKNOWN` |

---

## §2 When-to-use (tetikleyici tablosu)

| Trigger | Ne zaman | Bu skill |
|---------|----------|----------|
| 'C++ ses motoru' | Neva Engine / audio callback / `include/neva` kodu | ✅ |
| 'DSP kodla' | EQ, reverb, compressor, limiter, crossover değişikliği | ✅ |
| 'ASIO gecikme' | Buffer/sürücü/gecikme yapılandırması, exclusive mod | ✅ |
| 'EQ/reverb ekle' | DSP zincirine efekt/parametre ekleme | ✅ |
| CMake/ctest derleme kapısı | `app-cpp-projects/neva-engine` build/test | ✅ |
| Vault `.md` / prompt işi | Dokümantasyon kurgusu | ❌ → `prompt-maker` |
| PCB/güç/devre | Donanım tasarımı | ❌ → `hardware-electronics` |

---

## §3 Otonom Çalışma Protokolü (6 adım)

```text
ADIM 1 ANALİZ   → Talep hangi katmana değiyor? K3 DSP mi, K2 backend mi, build/test mi?
ADIM 2 KEŞİF    → İlgili disk dosyaları OKUNUR (include/neva/*.h · src/*.cpp · CMakeLists)
ADIM 3 DOĞRULA  → Vault + web çapraz teyit (Truth Mode); teyitsiz iddia → ⚠️ VERIFICATION REQUIRED
ADIM 4 REFERANS → §4 tablosundan ilgili reference okunur (okumadan kod yazılmaz)
ADIM 5 KOD      → §6 RT kuralları + H001 kapıları uygulanır; yasaklı kalıp → RED
ADIM 6 TEST     → ctest + ölçüm kapıları (§9); kanıtsız "bitti" YASAK → rapor
```

**Durdurma koşulları (DUR + kullanıcıya sor):**
1. Vault içi çelişki (§10.1 çelişki kapıları) — çözüm yetkim yoksa.
2. Mimari sıra değişikliği (EQ → Reverb → Crossover → Limiter bozulacaksa) → Human Approval Gate.
3. Lisans kararı gerektiren bağımlılık ekleme (JUCE AGPL / ASIO GPLv3 yüzeyi) → kullanıcı onayı.
4. Üç başarısız düzeltme turu → dur, şüpheli varsayımı yaz, üst karar iste.

---

## §4 Zorunlu Okumalar (references/)

| Dosya | İçerik | Ne zaman okunur |
|-------|--------|-----------------|
| [references/realtime-rules.md](references/realtime-rules.md) | R1–R7 RT kuralları: zero-allocation, lock-free, noexcept, 64B align, atomik publish, denormal, hot-path yasakları + doğru/yanlış C++ çiftleri + denetim listesi | RT callback / hot-path kodu yazmadan-önce (ADIM 5) |
| [references/contracts.md](references/contracts.md) | Thread/sahiplik sözleşmeleri: RenderCallback, BackendResult fallback tablosu, EqChain RT↔RT-dışı sınırı, SpscRing üretici-tüketici, DeviceList sabit boyut + sahiplik tabloları | Arayüz/imza değişikliğinde, backend/DSP bağlantısında |
| [references/latency-targets.md](references/latency-targets.md) | ASIO <10ms / WASAPI <20ms, buffer matematiği (512@48k=10.67ms), exclusive mod, MMCSS "Pro Audio", ölçüm yaklaşımı | Buffer/sürücü/gecikme konfigürasyonunda ('ASIO gecikme') |
| [references/dsp-chain-map.md](references/dsp-chain-map.md) | Uçtan uca sinyal zinciri, pipeline aşamaları (EQ→Reverb→Crossover→Limiter), 1.0→8.1 matris, kalite hedefleri, reverb mod adı çelişkisi | DSP zincirine müdahale / kanal matrisi işinde |
| [references/sources.md](references/sources.md) | Web kaynakları: ad · URL · erişim tarihi · ne doğrulandı (Truth Mode kanıtı) | Sürüm/API iddiası yazılacak her seferinde (ADIM 3) |
| [references/changelog.md](references/changelog.md) | Sürüm geçmişi (3.0.0 → 4.0.0 reset kaydı, N10) | Sürüm davranışı sorgulandığında |

### §4.1 Dosya Ağacı (disk kanıtı — 2026-10-21 ölçüm)

```
app-cpp-projects/neva-engine/
├── CMakeLists.txt                 # MSVC /W4 /permissive- /Zc:__cplusplus /utf-8
│                                  # link: ole32 avrt uuid · "3. parti bağımlılık YOK"
├── include/neva/
│   ├── biquad.h                   # BiquadCoeffs {b0,b1,b2,a1,a2} · DF1/DF2
│   ├── biquad_eq_ring.h           # ring-tabanlı biquad yardımcıları
│   ├── convert_plan.h             # kaynak→zincir format/kanal dönüşüm planı
│   ├── eq_grids.h                 # 31 bant oktabav grid (kEqFrequencies)
│   ├── eq_chain.h                 # EqChain · set_gain · set_active_bands · process (62 biquad)
│   ├── iaudio_backend.h           # IAudioBackend arayüzü + BackendResult
│   ├── neva_session.h             # NevaSession::render() noexcept · SPSC ring
│   ├── icy_parser.h               # ICY (shoutcast) metadata ayrıştırıcı
│   └── spsc_ring.h                # tek-yazıcı/tek-okuyucu lock-free kuyruk
├── src/
│   ├── neva_session.cpp           # oturum orkestrasyonu (RT-DISI hazırlık + render)
│   ├── wasapi_backend.cpp         # WASAPI backend · MMCSS "Pro Audio" kaydi VAR
│   ├── null_backend.cpp           # test backend'i (null destekli)
│   └── decode_stub.cpp            # çözücü stub (null destekli)
└── tests/                         # ctest + <cassert> (Google Test DEĞİL, §10.1.1)
    ├── biquad_eq_ring.cpp
    ├── eq_grids_gates.cpp
    └── icy_parser_gates.cpp
```

**Eksik (hedef mimari — diskte YOK, üretim öncesi onay):** `asio_backend.*` ·
`juce_*` · `reverb*` · `crossover*` · `limiter*` · `neva_upmix.*` · gerçek decoder.

> **Kural:** Yeni `.cpp/.h` üretimi görev onayı gerektirir (AGENTS.md §16.5);
> "dosya var" iddiası bu tabloyla çelişiyorsa §10 çelişki kapısı işletilir.

## §5 Örnekler (examples/)

| Dosya | Ne gösterir |
|-------|-------------|
| [examples/add-band-eq.md](examples/add-band-eq.md) | Ham istek "EQ ekle" → sözleşme (EqChain) + DSP (RBJ katsayı) + RT kısıtları + ctest kapısı — girdi → çıktı tam döngü |
| [examples/add-reverb-mode.md](examples/add-reverb-mode.md) | Yeni reverb modu ekleme: param struct → enum → insertion point (pipeline sırası korunur) → test → doğrulama adımları |

---

## §6 Çekirdek RT Kuralları (bağlayıcı özet — tam metin §4'teki realtime-rules.md)

> Otorite: `.ai/CLAUDE.md` §19 C++ Guardrails · `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` §3 ·
> web teyidi: JUCE `processBlock` dokümanı ([sources.md](references/sources.md) S1).
> **İhlal = H001 RED** (geri çevrildi, kullanıcı uyarılır).

| # | Kural | İhlal sonucu | Denetim |
|---|-------|--------------|---------|
| R1 | **Zero-allocation** — callback/`process()` içinde `new`/`delete`/`malloc`/`free`, kapasite büyüten `vector::push_back`, `string` büyütme, ilk kez açılan `unique_ptr` **YASAK**; tahsis yalnız `prepare()`/`open()` | Underrun → xrun → click/pop | Kod denetimi + §9 ölçüm |
| R2 | **Lock-free** — callback içinde `mutex`/`lock_guard`/`condition_variable`/`CriticalSection` **YASAK**; paylaşim `std::atomic` publish yada `SpscRing` | Priority inversion → ölümcül gecikme | Kod denetimi |
| R3 | **noexcept** — callback ve tüm `process()`/`RenderCallback` noexcept; sıcak yolda `throw`/try-catch **YASAK**; hata → fail-silent (sustur/sıfırla) | `std::terminate` / tahmin edilemeyen zamanlama | İmza denetimi |
| R4 | **Cache-line (64-byte)** — thread'lerin paylaştığı atomikler `alignas(64)`; üretici/tüketici indeksleri ayrı satırda | False sharing → beklenmeyen gecikme | `spsc_ring.h` deseni |
| R5 | **Parametre geçişi = atomik publish** — katsayı üretimi RT-dışı (`set_gain`), callback blok başında `memory_order_acquire` ile snapshot alır; mid-callback mutation yok | Tearing → bozuk katsayı → distortion | EqChain deseni (§4 contracts) |
| R6 | **Değişken blok boyutu** — blok boyutu her callback'te farklı olabilir; 0 örnekli callback mümkündür → `n` ile sınırlı işle | Click/crash (JUCE dokümanı S1) | Test: farklı `n` değerleri |
| R7 | **Blok dışı işler kuyruğa** — dosya I/O, log, string formatlama, `dynamic_cast` zinciri, UI etkileşimi callback'te **YASAK**; SPSC kuyrukla arka plana | Deadline aşımı → xrun | Kod denetimi |

### §6.1 Hızlı doğru/yanlış (tam çiftler: references/realtime-rules.md §2)

```cpp
// ❌ YANLIŞ — R1 ihlali: callback içinde tahsis
void RenderCallback(void*, float* out, std::uint32_t frames, std::uint16_t ch) noexcept {
    std::vector<float> tmp(frames * ch);          // heap tahsisi! (R1)
    // ...
}

// ✅ DOĞRU — önceden tahsis edilmiş scratch; callback'te yalnız span/slice
class Engine {
    std::array<float, 4096> scratch_{};           // prepare() dışında büyümüyor (R1)
public:
    void render(float* out, std::uint32_t frames, std::uint16_t ch) noexcept {
        const std::size_t n = std::min<std::size_t>(frames * ch, scratch_.size()); // R6
        // ... DSP ...
    }
};
```

```cpp
// ❌ YANLIŞ — R2 ihlali: UI ile mutex paylaşma
std::mutex g_m;
void render(...) noexcept { std::lock_guard lk(g_m); apply(gain_); }   // (R2)

// ✅ DOĞRU — atomik publish + blok başı acquire snapshot (R5)
std::atomic<float> gain_target_{1.0f};
void render(float* out, std::uint32_t frames) noexcept {
    const float g = gain_target_.load(std::memory_order_acquire);      // blok başı
    apply(out, frames, g);                                             // blok içi sabit
}
```

```cpp
// ❌ YANLIŞ — R3 ihlali: noexcept olmayan / throw atan callback
void process(float* io, std::size_t n) { if (bad_) throw std::runtime_error("x"); }

// ✅ DOĞRU — noexcept; doğrulama prepare'da, sıcak yolda fail-silent (R3)
void process(float* io, std::size_t n) noexcept {
    if (n > max_block_) { clear(io, n); return; }   // throw yok
}
```

### §6.2 Hızlı yasaklı-kalıp denetimi (kod yazıldıktan sonra, satır satır)

- [ ] `process`/`render`/callback gövdesinde `new`/`delete`/`malloc`/`free` yok (R1)
- [ ] `std::vector`/`std::string` büyütmesi yok; kapasite sabit veya `prepare()`'da (R1)
- [ ] `mutex`/`lock`/`condition_variable`/`wait`/`sleep` yok (R2)
- [ ] `throw`/`try`/`catch`/`dynamic_cast`/`typeid` yok (R3, R7)
- [ ] `printf`/`std::cout`/`std::format`/dosya yazımı yok (R7)
- [ ] `std::function` çağrısı, `std::shared_ptr` kopyası (son referans düşüşü → dtor riski) yok (R7)
- [ ] Paylaşılan atomikler `alignas(64)` veya tek thread'e ait (R4)
- [ ] Parametreler blok başı tek noktadan okunuyor; blok içinde değiştirilmiyor (R5)
- [ ] `n` (frames) çağrıdan çağrıya değişebilir; 0 dahil her boyut güvenli (R6)
- [ ] Callback imzası `noexcept` (R3) — `RenderCallback` alias'ı zaten noexcept (contracts §2)

### §6.3 Atomik Yayın Sözdizimi (R5 — EqChain deseni, disk kanıtı)

`eq_chain.h` yorumları + kodu (2026-10-21) canonical deseni tanımlar:

| Aşama | Thread | İşlem | Bellek sırası |
|-------|--------|-------|----------------|
| 1 | RT-DISI | Yeni katsayıyı `targets_[i]`'ye yaz (üretim) | — |
| 2 | RT-DISI | `pending_.fetch_add(1, std::memory_order_release)` | **release** (yayın) |
| 3 | RT | Blok başında `pending_` consume (acquire) | **acquire** (okuma) |
| 4 | RT | `ramp_from_ ← biquads_.coefficients()` · `ramp_to_ ← targets_` | — |
| 5 | RT | Per-block lineer rampa (b0,b1,b2,a1,a2); `ramp_pos_ >= 1.0` → sabit | — |

**Maske (aktif bant) özel kuralı (eq_chain.h yorumu):** `set_active_bands`'ta
**maske publish'i pending bump'ından ÖNCE** yapılır → release-acquire yayılımı
(ADR-106 §2.2b kural 5). Pasif bant unity'ye rampalanır; ramp bitince bant
**işlenmez** (bypass — ADR-106 kural 4, CPU kazancı).

```cpp
// ✅ canonical (EqChain'den) — RT-DISI publish
void set_gain(std::uint32_t band, std::uint32_t ch, double db, double ramp_ms) noexcept {
    // ... katsayı üret (RT-DISI) → targets_[i] ...
    pending_.fetch_add(1, std::memory_order_release);        // yayın
}

// ✅ canonical — RT consume (process, blok başında TEK nokta)
if (pending_.load(std::memory_order_acquire) > 0) {         // okuma
    for (...) { ramp_from_[i] = biquads_[i].coefficients();  // eski
                ramp_to_[i]   = targets_[i];                 // yeni
                ramp_pos_[i]  = 0.0; }
}
```

### §6.4 Denormal (FTZ/DAZ) — prepare()'da bir kez (R-komşu)

Denormal (subnormal) sayılar SSE'de **yüzlerce kat yavaş** yol alır → sabit
küçük değerli sinyallerde CPU patlar. Karşılaştırma: NaN/Inf guard (`isfinite`)
alan kod != denormal koruması alan kod.

```cpp
// ✅ prepare()/open() içinde — RT DIŞI, bir kez (x86/x64 SSE2, MSVC)
#include <xmmintrin.h>
_mm_setcsr(_mm_getcsr() | 0x8040);   // FTZ (bit15) + DAZ (bit6)
```

| Bayrak | Bit | Ne yapar |
|--------|-----|----------|
| FTZ (Flush-To-Zero) | 15 | Sonucu 0'a yuvarla (denormal üretme) |
| DAZ (Denormals-Are-Zero) | 6 | Denormal girdiyi 0 say (okuma hızı) |

- Kural: **yalnız prepare'da**; RT callback içinde tekrarlanmaz (gereksiz).
- Doğrulama: `csr & 0x8040 == 0x8040` assert'i (test ortamında).

### §6.5 Scratchpad Deseni (R1 — blok-içi geçici bellek)

| Desen | Ne | Kural |
|-------|----|-------|
| `std::array<float, N> scratch_` (static boyut) | Sınıf üyesi, prepare'da sıfırlanır | R1 uyumlu; N ≥ maxBlock×channels |
| `std::vector` üye + `reserve(maxBlock)` + `resize` yok | Kapasite prepare'da | `push_back`/`resize` RT'de YASAK |
| Çift/tamponlu (A/B) | Bir blok A'ya yazılırken diğeri okunur | SpscRing deseni (R4) |
| span/slice | `std::span<float>(scratch_.data(), frames*ch)` | Tahsis yok |

---

## §7 DSP Zinciri Özeti (harita → references/dsp-chain-map.md)

```text
[K2 I/O: WASAPI/ASIO · Float32 · 48kHz]
  → 1. Input Gain (atomik, ±24dB)
  → 2. 31-Band Parametrik EQ (RBJ/TPT biquad, 1/3 oktav 20Hz…20kHz)   ← diskte: EqChain (62 biquad)
  → 3. Compressor (feed-forward, stereo-link)
  → 4. Reverb (4 mod: Geniş Konser · Düğün Salonu · Oda · Stüdyo)
  → 5. Crossover (LR4 @80Hz → 8.1 matris + LFE bass management)
  → 6. True-Peak Limiter (oversample, ceiling -0.1dBTP)
  → 7. Maximizer (lookahead)
  → 8. Output Matrix 1.0→8.1 → [K2 I/O]
  ↳ paralel tap: Metering (LUFS · RTA · Peak/RMS — callback DIŞI kuyruk)
```

- **Sıra değiştirilemez** (EQ → Reverb → Crossover → Limiter); değişiklik mimari karardır
  → Human Approval Gate + vault ADR notu (dsp-chain-map §5).
- **K2 → K3 sıkı bağımlılık:** K3 asla K2'yi atlamaz (`.ai/CLAUDE.md` §5).
- Donanım ucu: ADR-038 → XMOS XU316 + PCM3168A (PCM5122 **YASAK**, H001) → AK4458 → Class AB.

---

## §8 Hata Modları (özet — kök neden + yön, tam tablo: realtime-rules §4 / latency-targets §5)

| Hata modu | Belirti | Kök neden (önce şunu ölç) | Müdahale |
|-----------|---------|---------------------------|----------|
| **Xrun (underflow/overflow)** | Click/pop, kesik ses | Callback süresi > `bufferSize/sampleRate` deadline'ı; bak: tahsis? lock? CPU? | RT ihlalini bul (§6.2), buffer boyutunu artırmak **yalnız** son çare |
| **Sample-rate mismatch** | Pitch/ton farkı, frekans kayması | Cihaz SR ≠ 48k; `setDeviceRate`/`open` SR müzakeresi başarısız → `RateUnsupported` | Fallback tablosu (contracts §4): retry → shared → NULL backend |
| **Device hot-swap / cihaz kaybı** | Ses kesilir, callback durur | `DeviceLost` sonucu; sürücü elinden alındı | Backend'i kapat → yeniden enumerate → fallback zinciri (latency-targets §5) |
| **Cihaz meşgul (exclusive lock)** | `Busy`, ikinci instance açılamaz | ASIO/exclusive aynı anda tek uygulama; çoklu deneme **sürücü çökmesi** (brain.md #910) | İkinci süreci açma; kullanıcıya bildir → shared'a düş |
| **Click/pop (blok sınırında)** | Param değişimi anında tıklama | Katsayı anlık sıçraması | 5–20ms ramp (EqChain `set_gain` deseni, R5) |
| **NaN/Inf yayılımı** | Sessizlik/kirlenmiş çıkış | Bölme/sqrt domain; denormal | `std::isfinite` guard; FTZ/DAZ (MXCSR 0x8040) |
| **DC drift** | Subsonik enerji, ısı | DC offset filtresi yok | DC blocker (high-pass ~20Hz) |
| **Denormal (DAZ)** | CPU beklenmedik şekilde yükselir | Sabit küçük/sonlu değerler | `_mm_setcsr(_mm_getcsr() \| 0x8040)` (SSE2) — prepare'da |

### §8.1 Teşhis Akışı (sırayla — tahmin yok, ölç)

```text
BELRTİ: click/pop veya boşluk (xrun şüphesi)
  ├─ ADIM 1: §6.2 denetimini çalıştır (tahsis/lock/throw var mı?) → varsa ihlali bul ve düzelt
  ├─ ADIM 2: callback süresini ölç (steady_clock around process/render)
  │          süre > blockSize/sampleRate mı? → EVET: CPU bütçesi aşıyor
  │            ├─ hangi bileşen? → EqChain 62 biquad CPU profili / zincir bypass testi (DoD)
  │            └─ SIMD yolu aktif mi? → convert_plan /arch:AVX2 derleme
  ├─ ADIM 3: yalnız yüksek kanalda mı? → kanal başına maliyet (upmix matrisi)
  ├─ ADIM 4: yalnız shared modda mı? → exclusive dene (latency-targets §4)
  ├─ ADIM 5: MMCSS kaydı var mı? → wasapi_backend.cpp "Pro Audio" (§4.1 latency-targets)
  └─ ADIM 6: hâlâ yoksa → xrun sayacı + ETW/WPR kaydı (ölçüm aracı → §10.3 VERIFICATION)
```

| Belirti | İlk ölçüm | Çıkarım |
|---------|-----------|---------|
| Kesintili ses + CPU düşük | Xrun sayacı > 0 | Tahsis/lock (R1/R2) ihlali ara |
| Kesintili ses + CPU yüksek | Callback süresi bütçesi | CPU bütçesi → bileşen bypass testi |
| Tek uygulamada, ikincisinde yok | `Busy` sonucu | Exclusive lock (§11.3) — çoklu deneme YASAK |

---

## §9 Ölçüm & Kalite Kapıları (Definition of Done)

> Kural: **ölçülmeyen iddia yazılmaz**; test edilmemiş iddia = kabul yok.
> Hedefler vault'tan (PROJECTS §2 · §7.1.1); ölçüm yöntemi kaynaklı olmalı.

| # | Kapı | Hedef | Yöntem | Durum |
|---|------|-------|--------|-------|
| 1 | Derleme | temiz, `/W4` uyarı yığını yok | `cmake --build` (§9.1) | zorunlu |
| 2 | Birim test | 3/3 ctest geçer | `ctest --test-dir build` | zorunlu |
| 3 | RT denetimi | §6.2 on maddesi tam | kod satır denetimi | zorunlu |
| 4 | Xrun | ≈0 | atomik xrun sayacı + callback süresi ölçümü (steady_clock) | zorunlu |
| 5 | Gecikme | ASIO <10ms · WASAPI <20ms | roundtrip loopback ölçüm (kurulum → latency-targets §4) | rapor |
| 6 | THD+N / SNR | <%0.005 @1kHz · >105dB | sine analiz + -60dBFS dither (ölçüm kurulumu ⚠️ §10.3) | rapor |
| 7 | Coverage | ≥80% min / ≥90% hedef | coverage aracı seçimi vault'ta tanımlı değil → ⚠️ VERIFICATION REQUIRED | rapor |
| 8 | Kulak/A-B | click yok, bit-perfect bozulmaz | dinleme + render karşılaştırma | rapor |

### §9.1 Build & test komutları (disk kanıtı: `app-cpp-projects/neva-engine/CMakeLists.txt`)

```bash
# repo kökünden
cmake -S app-cpp-projects/neva-engine -B app-cpp-projects/neva-engine/build
cmake --build app-cpp-projects/neva-engine/build --config Release
ctest --test-dir app-cpp-projects/neva-engine/build -C Release --output-on-failure
# kayıtlı testler: biquad_eq_ring · eq_grids_gates · icy_parser_gates
```

> Google Test **değil** — ctest + el-yazımı assert kapıları (§10.1 çelişki #1).
> Komut çıktısı çalıştırılmadan "test geçti" denmez (kanıt zorunlu).

### §9.2 Ölçüm Metodolojisi (ölçülmeyen iddia yazılmaz)

| Metrik | Nasıl ölçülür | Bileşen | Not |
|--------|---------------|---------|-----|
| **Callback süresi** | `std::chrono::steady_clock` ile process/render sarmalayıcı; histogram (min/med/max) | RT thread | Xrun teşhisi (§8.1) |
| **Xrun sayısı** | Atomik sayaç: backend underrun/overrun bildiriminde `++` | Backend | ≈0 hedefi |
| **Roundtrip gecikme** | Loopback: girişi fiziksel çıkışa bağla; impulse gönder → Δt (samples/sampleRate) | Donanım | latency-targets §6 |
| **THD+N** | 1 kHz sine (-60dBFS dither) → çıkış FFT; harmonik toplam / fundamental | Kalite | sartname: <%0.005 (SKILL §1.1 tablo <%0.01 — §10.1 çelişki #5) |
| **SNR** | Sessiz giriş → çıkış RMS | Kalite | >105dB hedef |
| **Bypass bit-tutarlılığı** | Zincir bypass → giriş vs çıkış byte karşılaştırma | DSP | DoD: "x8 → passthrough doğrulanmalı" (vault DoD) |

**Ölçüm koşulları:** (1) Release derleme; (2) `Pro Audio` MMCSS kaydı açık; (3) güç
planı "Yüksek Performans"; (4) en düşük bant (256 örnek) test edilmeden gecikme
iddiası yazılmaz (latency-targets §6).

---

## §10 Çelişki Kapıları (Contradiction Gates — DUR + rapor)

### §10.1 Vault ↔ disk çelişkileri (bilinen, taşıdı)

| # | Çelişki | Vault diyor | Disk diyor | Davranış |
|---|---------|-------------|------------|----------|
| 1 | Test framework | CLAUDE §19 · PROJECTS §7.1.1: **Google Test** | CMake: 3. parti yok, `<cassert>` + ctest | **Disk kazanır** (kök AGENTS §3); yeni kod mevcut kalıba uyar; düzeltme yalnız vault-sync ile |
| 2 | Reverb mod adları | CLAUDE §19: Geniş Konser/Düğün Salonu/Oda/Stüdyo vs PROJECTS §2: Düğün Salonu/Konser Alanı&Arena/Canlı Stüdyo/Kulüp | kodda enum yok (reverb modu implemente edilmedi) | Enum sabitlemeden önce DUR + kullanıcı onayı (SSOT: CLAUDE > PROJECTS) |
| 3 | JUCE/ASIO entegrasyonu | CLAUDE §24: JUCE 9.x + ASIO SDK 2.3.4 **zorunlu** | CMake: "3. parti bağımlılık YOK"; ASIO backend dosyası yok | Hedef PLANNED; "entegre ettim" iddiası yasak |
| 4 | Kanal aralığı | CLAUDE §19: "2.0 → 8.1" vs PROJECTS: "1.0 Mono → 8.1" | — | Her iki uç da desteklenir; kod yazımında hedef kanalı iste, uydurma |
| 5 | THD+N hedefi | CLAUDE §19/PROJECTS §2: <%0.005 vs sartname §1.1: <%0.01@1kHz | Ölçüm altyapısı yok | İki hedef de raporlanır; **daha sıkı olan** (<%0.005) kabul eşiği sayılır, ölçüm kurulumu §10.3 |

### §10.2 Sürüm kanıtı (kural: kanıtsız sürüm iddiası yazılmaz)

| İddia | Web kanıtı | Depo kanıtı | Karar |
|-------|-----------|-------------|-------|
| ASIO SDK **2.3.4** | vcpkg/MSYS2 paketleri `ASIO-SDK_2.3.4_2025-10-15.zip` indirme URL'i (Steinberg host); Steinberg sayfası "ASIO 2.3 SDK" + GPLv3/tescilli dual-license (S2, S3) | SDK depoda yok | **Kaynaklıdır** (paketleyiciler + Steinberg); yine de kurulum kanıtı yok → entegrasyon öncesi indirip doğrula |
| **JUCE 9** | juce.com/whats-new JUCE 9 özellikleri + forum preview branch (2026-05-21) (S4) | Depoda JUCE yok | **Varlık kaynaklıdır**; GA sürüm/pin detayı → ⚠️ VERIFICATION REQUIRED (pin almadan `find_package` sürümü yazma) |
| "ASIO SDK 2.3.4 = Steinberg sayfasındaki sürüm" | Steinberg sayfası yalnız "2.3" der (S2) | — | ⚠️ VERIFICATION REQUIRED (tam 2.3.4 pin'i indirme paketi üzerinden doğrulanır) |

### §10.3 Ölçüm kurulumu boşlukları

- THD+N/SNR/roundtrip **ölçüm prosedürü** vault'ta hedef olarak var, kurulum olarak yok
  → araç/seçim yazılmaz, `⚠️ VERIFICATION REQUIRED` (latency-targets §4).
- Coverage aracı (llvm-cov? istanbul? gcov?) vault/CMake'de tanımlı değil → `⚠️ VERIFICATION REQUIRED`.

---

## §11 Truth Mode & Güvenlik (bağlayıcı)

1. **Zero-Hallucination:** JUCE/ASIO/DSP API'si, sürüm, config, dosya yolu tahminle yazılmaz;
   vault'ta/web'de karşılığı olmayan iddia → `⚠️ VERIFICATION REQUIRED`; bilinmeyen → `UNKNOWN`.
   Kaynak hiyerarşisi: kullanıcı talimatı > onaylı karar (vault) > resmi doküman > model hafızası.
2. **H001 Kritik Reddi:** deprecated/uyumsuz/güvensiz yapı reddedilir — callback içinde tahsis,
   kilitli hot-path, **PCM5122** (yalnız PCM3168A — ADR-038 · CLAUDE §21), katman tersi bağımlılık.
3. **ASIO Exclusive Lock:** aynı anda yalnız tek uygulama; çoklu deneme sürücü çökmesine yol
   açabilir (`.ai/brain.md` #910) → exclusive cihaz kodu dikkatli; ikinci süreç test edilmez.
4. **WASAPI exclusive:** cihazı devralır, sistem seslerini susturur → kullanıcıya her zaman
   shared fallback seçeneği sun (Microsoft dokümanı S5).
5. **Vault uyumu:** `.ai/` kurallarıyla (CLAUDE §7 guardrail, AGENTS §5) çelişilemez → DUR + sor.
6. **Güvenlik:** API key/secret skill/koda hardcode edilemez (`.env`/vault); yıkıcı komut
   (silme/deploy) kullanıcı onayına bağlıdır; proje C++ kaynak kodu bu skill kapsamında
   yalnızca görev onaylıysa değişir.
7. **Test kapısı:** ctest çalıştırılmadan "tamam" denmez; §9 kapılarından düşen madde
   raporda açıkça yazılır (kapatılmış gibi gösterilmez).

---

## §12 Otorite & Vault Bağlantıları

| Kaynak | Bölüm | Bu skilldeki rolü |
|--------|-------|-------------------|
| `.ai/CLAUDE.md` | §19 Audio Engine Standards | Sample format, gecikme, DSP, reverb modları, C++ guardrails |
| `.ai/CLAUDE.md` | §24 Dependencies | C++20 · JUCE 9.x · ASIO SDK 2.3.4 hedef sürüm otoritesi |
| `.ai/CLAUDE.md` | §5 K2/K3 satırları · §21 (PCM5122 yasağı) | Katman sırası + donanım yasağı |
| `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` | §1–§4 | Doğrulanmış SDK/lisans envanteri (JUCE AGPLv3 dual, ASIO GPLv3 kararı 2026-10-14) |
| `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` | §1–§4 | Genişletilmiş pipeline, RT guardrails, DoD ölçüm tablosu |
| `.ai/VISION.md` | §17.2 Ses Sinyal Zinciri | Donanım ucu (XMOS → ADC → DSP → DAC → amfi) |
| `.ai/ecosystem/asio-wasapi-rehber.md` | §3.1–§3.4 · §5.1 | ASIO/WASAPI exclusive-shared, fallback zinciri, 64-bit-only notu |
| `.ai/brain.md` | Kritik Uyarılar #6 (#910) | ASIO exclusive lock — tek uygulama, çoklu deneme çökme riski |
| `.ai/.decisions/accepted/ADR-038-*.md` | tümü | PCM3168A + XMOS XU316 (PCM5122 RED), saat/jitter + ölçüm planı |
| `.ai/.decisions/accepted/ADR-0105-*.md` | §2.5/§5/§7.1 (kod yorumlarında) | SPSC ring, test kapıları, ilk kod iskeleti (kod atıfları diskte) |
| `app-cpp-projects/neva-engine/` | CMakeLists + `include/neva/*` | **Disk gerçeği — kod bu dosyalardan türetilir** (§1.2) |

**SSOT sırası:** `.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` > `.ai/brain.md` >
`.ai/index.md` > `.ai/.templates/` — harici kaynak (web/model hafızası) her zaman vault'un altındadır;
**disk kodu** ise vault spec'ine göre gerçeği taşır (çelişki → §10.1).

**Şablon hizası (Guardrail #16):** `.ai/.templates/other/cpp-template.md` (C++/Neva Audio Engine
Template v2.0.1) okundu — §4 DSP kural tablosu, §12 kod stili, §13 test matrisi, §14 hata masası
bu skillin §6/§8/§9 içerikleriyle uyumlu; skill **iskeleti** kanonik olarak skill-maker
`templates/skill-template.md`'den türetildi (cpp-template kod şablonudur, skill iskeleti içermez).

---

## §13 Sembol Sözlüğü (disk kanıtlı — tahmin yok)

| Sembol | Dosya | Değer / Ne |
|--------|-------|------------|
| `kEqBands` | eq_chain.h | `31` — EQ bant sayısı |
| `kEqChannels` | eq_chain.h | `2` — L/R |
| `kEqBiquads` | eq_chain.h | `62` — 31×2 temel biquad |
| `kEqMaskAll` | eq_chain.h | `(1u << 31) - 1` — tüm bantlar aktif |
| `kEqFrequencies` | eq_chain.h | 31 elemanlı constexpr frekans dizisi |
| `kunity` | eq_chain.h (private) | `BiquadCoeffs{1,0,0,0,0}` — bypass katsayısı |
| `pending_` | eq_chain.h | `atomic<uint32_t>` publish sayacı (R5) |
| `RenderCallback` | iaudio_backend.h | noexcept callback alias (contracts §2) |
| `BackendResult` | iaudio_backend.h | açma/başlatma sonucu enum (contracts §4) |
| `kSpscCapacity` | neva_session.h | SPSC ring kapasitesi (varsayılan 512) |
| `_mm_getcsr() \| 0x8040` | (uygulanacak) | FTZ+DAZ denormal koruması (§6.4) |
| MMCSS `"Pro Audio"` | wasapi_backend.cpp | callback thread önceliği (S5) |

## §14 Sık Yapılan Hatalar (bu skill kapsamında — her biri RED/HATA)

| # | Hata | Neden yanlış | Doğrusu |
|---|------|--------------|---------|
| 1 | "Google Test ile test yazdım" | Depoda Google Test YOK (§10.1.1) | ctest + `<cassert>` kalıbı (eq_grids_gates deseni) |
| 2 | `process()` içinde `std::vector<float> tmp(n)` | R1 ihlali → xrun | prepare'da tahsis edilmiş scratch (§6.5) |
| 3 | UI'dan `mutex` ile EQ katsayısı değiştirmek | R2 ihlali → priority inversion | atomik publish + blok başı acquire (§6.3) |
| 4 | `ramp_ms=0` ile `set_gain` çağırmak | Click/pop (EqChain yorumu: 5-20 ms) | `ramp_ms` 5-20 (EqChain varsayılanı 10) |
| 5 | Reverb enum'u "tahminle" yazmak | §10.1.2: enum yok → DUR + onay | add-reverb-mode.md akışı |
| 6 | "JUCE 9 ile processBlock yazdım" | §10.1.3: JUCE depoda entegre değil | Honest scope (§1.2); yalnız disk kodu |
| 7 | ASIO'yu ikinci süreçte denemek | brain.md #910: sürücü çökme riski | Exclusive Lock tek uygulama (kural 9) |
| 8 | 512 örnek = "düşük gecikme" demek | 512@48k = 10.67 ms (§2.2 latency) | 256 bandı (<5.4 ms) — latency-targets §2.3 |
| 9 | Callback'te `printf`/log | R7 → deadline aşımı | SPSC kuyrukla arka plan log |
| 10 | PCM5122'den bahsetmek | ADR-038 H001: REDDEDİLMİŞ | Yalnız PCM3168A (+ XMOS XU316) |

---

*CoreMusic Skill v3.0 — metadata.version: 4.0.0 — Updated: 2026-10-21*
