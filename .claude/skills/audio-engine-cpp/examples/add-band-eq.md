# Örnek: Band EQ Ekleme — "EQ kodla" Tam Döngü (gerçek EqChain kontratı)

> Bu örnek, depodaki **gerçek** `EqChain` API'siyle (include/neva/eq_chain.h,
> disk kanıtı 2026-10-21) bir EQ isteğinin nasıl işleneceğini gösterir:
> **istek → sözleşme → DSP → RT → ctest**.

## 1. İstek

> "Bir band EQ ekle — kullanıcı bantları açıp kapatabilsin, kazanç ayarlayabilsin."

## 2. Sözleşme (kod YAZMADAN — mevcut API'yi oku)

EqChain zaten var; yeni kod yazmak yerine mevcut sözleşmeye uy:

```cpp
// include/neva/eq_chain.h (disk kanıtı — özet)
inline constexpr std::uint32_t kEqBands    = 31;              // 31 bant
inline constexpr std::uint32_t kEqChannels  = 2;               // L/R
inline constexpr std::uint32_t kEqBiquads   = kEqBands * kEqChannels;  // 62
inline constexpr std::uint32_t kEqMaskAll   = (1u << kEqBands) - 1u;   // tümü aktif
// kEqFrequencies: 31 elemanlı constexpr frekans dizisi

class EqChain {
  void prepare(double sample_rate) noexcept;
  // RT-DISI: katsayı üretimi + atomik publish + ramp. ramp_ms: 5-20 ms (click yasağı §2.3)
  void set_gain(std::uint32_t band, std::uint32_t ch, double db, double ramp_ms) noexcept;
  // RT-DISI: bant maskesi yayını; pasif bant unity'ye rampalanır, ramp bitince ISLENMEZ (bypass)
  void set_active_bands(std::uint32_t mask, double ramp_ms) noexcept;
  // RT-YOLU: per-block ramp + 62 biquad. Tahsisat/lock/log YOK (ADR-017)
  void process(float* L, float* R, std::size_t n) noexcept;
};
```

**İstek ↔ sözleşme eşlemesi:**

| İstek | API | Not |
|---|---|---|
| Kazanç ayarı | `set_gain(band, ch, db, ramp_ms)` | RT-dışı çağrılır; publish `pending_.fetch_add(1, release)` |
| Bant aç/kapa | `set_active_bands(mask, ramp_ms)` | Maske publish'i pending bump'ından ÖNCE (release-acquire) |
| Zincirde çalıştırma | `process(L, R, n)` | Blok sınırında publish consume; rampa blok başına ilerler |

**Sonuç:** "EQ ekle" isteği için *yeni DSP kodu gerekmez* — mevcut EqChain
kullanılır. Yeni kod yalnız UI/kontrol katmanıysa (ör. bant düğmesi →
`set_gain`) onun yazımı yeterlidir.

## 3. DSP Kullanımı (RT-dışı kontrol katmanı — taslak)

```cpp
// Kontrol katmani (RT-DISI thread) — örnek çağrılar
eq.prepare(48000.0);                                  // bir kez, başlangıçta

eq.set_gain(5, 0, +3.0, 10.0);   // bant 5, L kanal, +3 dB, 10 ms rampa
eq.set_gain(5, 1, +3.0, 10.0);   // R kanal (ch=1)
eq.set_active_bands(kEqMaskAll & ~(1u << 10), 10.0);  // bant 10'u kapat (unity rampası)
```

## 4. RT Yolu (değişiklik YOK — mevcut desen)

`process()` içinde (eq_chain.h, disk kanıtı):
1. `pending_` acquire ile oku → yeni publish varsa hedefleri kopyala, ramp başlat
2. Per-block lineer rampa: `ramp_from_[i] → ramp_to_[i]` (b0,b1,b2,a1,a2)
3. Pasif bant + ramp tamam → bant **işlenmez** (bypass, CPU kazancı — ADR-106 kural 4)

R5 (ramp) zaten gömülüdür — kontrol katmanı **asla** `process` içine parametre yazmaz.

## 5. Test (ctest + `<cassert>` — Google Test DEĞİL, SKILL.md §10.1.1)

Mevcut testler: `biquad_eq_ring.cpp` · `eq_grids_gates.cpp` · `icy_parser_gates.cpp`.
Yeni band davranışı için `eq_grids_gates.cpp` desenini izle:

```cpp
// tests/eq_grids_gates.cpp (desen) — gate testleri
#include <cassert>
// 1) passthrough: tüm bandlar kapalı (maske 0) → giriş ≈ çıkış (bypass, kural 4)
// 2) set_gain(db=+6) sonra process → çıkış enerjisi artar
// 3) rampa ortasında process → NaN/Inf yok
```

```powershell
# app-cpp-projects/neva-engine/ altından (CMakeLists.txt disk kanıtı)
cmake -S . -B build
cmake --build build
ctest --test-dir build --output-on-failure
```

## 6. DoD (Definition of Done)

1. Bypass doğrulandı mı (maske 0 → giriş≡çıkış)? ✓ (test 1)
2. Kazanç değişimi pop üretmiyor mu (rampa)? ✓ (r5 + test 3)
3. RT yolu tahsis/lock içeriyor mu? **HAYIR** (R1/R2/R3 — eq_chain.h yorumu)
4. /W4 /permissive- derleme uyarısız? ✓ (CMakeLists MSVC bayrakları)

## 7. Ne YAPILMAZ (bu örnekte)

1. `process()` içine `set_gain` çağırmak (RT-dışı API — RT yoluna taşımak yasak)
2. `ramp_ms=0` kullanmak (click yasağı — en az 5 ms önerilir, §2.3)
3. Yeni bant eklemek (kEqBands=31 → 32+ = sözleşme değişimi → ADR gerekir)
4. Google Test API'si yazmak (depoda ctest + `<cassert>` var, §10.1.1)
