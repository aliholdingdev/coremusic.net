# Örnek: Reverb Modu Ekleme — DUR + Onay Akışı

> **Bu örneğin amacı:** reverb'ün gerçek tasarım durumunu göstermektir. Depoda
> reverb kodu/enum'u YOKTUR (SKILL.md §10.1.2) — bu yüzden doğrudan kod yazmak
> değil, **onaylı tasarım akışı** örneklenir. Yanlışlıkla "kod yazılabilir"
> izlenimi veren her adım DUR ile işaretlenir.

## 1. İstek (tipik)

> "Reverb ekle — hall/plate/room modları olsun, wet kontrolüyle."

## 2. Önce Dur — Disk Kontrolü

```bash
# reverb izi var mı? (disk kanıtı)
grep -ri "reverb" app-cpp-projects/neva-engine/   # → sonuç yok (2026-10-21)
```

**Beklenen:** sonuç yok. **Bulunan:** reverb dosyası/enum'u yok → tasarım onayı gerekir.

## 3. Tasarım Kararları (onaya sunulur)

| Karar | Öneri | Onay bekler |
|---|---|---|
| Mod enum'u | `ReverbMode { Hall, Plate, Room }` | Evet (isim/üye) |
| Pipeline yeri | EQ sonrası, crossover öncesi (dsp-chain-map.md §1) | Evet |
| Algoritma | RT-güvenli delay-line (FDN/Freeverb sınıfı) — lisans: Comtus-reverb referansı **lisans metni okunmalı** (§VERIFICATION) | Evet |
| Parametreler | `wet, room-size, damping, predelay` | Evet |
| Yayılım | R5: wet gain rampası (blok sınırında), blok-içi anlık geçiş YOK | Hayır (kural) |

## 4. Sözleşme (onay SONRASI — taslak)

```cpp
// neva_reverb.h — onay sonrası yazılacak (şu an diskte YOK)
enum class ReverbMode : std::uint8_t { Hall, Plate, Room };

struct ReverbParams {           // RT-olmayan thread doldurur
    float wet       = 0.0f;     // 0..1
    float roomSize  = 0.5f;     // 0..1
    float damping   = 0.5f;     // 0..1
    ReverbMode mode = ReverbMode::Hall;
};
```

## 5. RT Kuralları (bağlayıcı — realtime-rules.md)

| Kural | Uygulama |
|---|---|
| R1 (zero-alloc) | Delay-line buffer'ları `prepare(sampleRate, maxBlock)` içinde baştan allocate; RT'de `new` YOK |
| R2 (noexcept) | Tüm `process()` noexcept |
| R3 (lock-free) | Parametre geçişi atomik publish (contracts.md §5) |
| R5 (ramp) | Wet/room-size değişimi blok sınırında + kısa rampa (pop önleme) |
| R7 | Ağır iş (preset yükleme vs.) RT thread DIŞINDA |

## 6. Test (ctest + `<cassert>` — Google Test DEĞİL, §10.1.1)

```cpp
// tests/reverb_gates.cpp — onay sonrası
#include <cassert>
// 1) passthrough: wet=0 → giriş≡çıkış (DoD: bypass doğrulama)
// 2) mod değiştir → NaN yok
// 3) wet=1, roomSize=1 → çıkış enerjisi > 0
```

```powershell
# app-cpp-projects/neva-engine/ altından
cmake -S . -B build && cmake --build build && ctest --test-dir build --output-on-failure
```

## 7. Yasaklar Özeti (bu örnekte)

1. **Enum yokken kod yazmak** (DUR — §10.1.2)
2. RT thread'de `new` / `delete` (R1)
3. Parametre değişikliğinde anlık geçiş — rampa olmadan (R5)
4. Google Test API'si kullanmak (depoda ctest + `<cassert>` var, §10.1.1)
5. Lisansı doğrulanmamış reverb kodunu kopyalayıp gömmek (§VERIFICATION)
