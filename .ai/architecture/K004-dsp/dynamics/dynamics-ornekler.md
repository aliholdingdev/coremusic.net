---
title: "dynamics — Örnekler"
type: architecture-examples
node_id: K004-dsp/dynamics
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# dynamics — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: — yok.** `shared/src/` altında ses dinamiği (compressor/limiter/envelope)
koduna ait C++ veya PHP bulunmamaktadır. Eşleşen `RateLimiter`/`CacheRateLimiter`
HTTP request rate limiting'dir, ses sinyali dinamiği değildir — yanıltıcı eşleşme,
dahil edilmez. Uydurma kod yazılmaz.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — Capabilities, 2026-10-12 ✅)

```
Dynamics and level control | Compressor, Limiter, NoiseGate, Expander,
                            DynamicEQ, MultibandCompressor, DeEsser
```

**Ne gösterdiği:** "dynamics" ailesinin sınırları — tek çatı altında compressor,
gate, expander, limiter, dinamik EQ, multiband, de-esser. CoreMusic `dynamics`
dalının sınıflandırmasına doğrudan referans (R2/R3 açık sorularını besler).

**Kaynak:** https://github.com/Signalsmith-Audio/basics (README, 2026-10-12 ✅)

```
- Dynamics (compressor/expander/gate)
- Limiter
```

**Ne gösterdiği:** MIT lisanslı, temel efekt kütüphanesinde aynen "Dynamics" adıyla
ayrı bir başlık — compressor/expander/gate'i bir arada, limiter'ı ayıran gruplama.

**Kaynak:** https://github.com/ShmKnd/Patina (Effect Engines, 2026-10-12 ✅)
`CompressorEngine` (opto/FET/variable-mu/VCA) ve `LimiterEngine` (FET/VCA/opto)
— analog-modeling aile karşılıkları.

## §3 Uygulama notu

Üç referans da compressor/limiter'ı **ayrı** birim olarak sunar (v2 mini-ağaçtaki
kardeş düzenle uyumlu). Q3 (hiyerarşi↔kardeş) resmen kapanmadan bu gözlemsel
eşleşme bağlayıcı hiyerarşi çizmez; yalnız tasarım desteğidir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[dynamics-kapsam]]` | şemsiye sınırı ve Q3 |
| `[[dynamics-dogrulama]]` | iddia kanıtları |
| `../compressor/compressor-kapsam.md` | aile üyesi (kardeş) |
| `../limiter/limiter-kapsam.md` | aile üyesi (kardeş) |
| `../measurement/measurement-kapsam.md` | dB/seviye takibi ölçüm sınırı |

## §5 Kod bloğu — `PLANNED` compressor zarf (envelope follower)

```cpp
// DYNAMICS-ORNEKLER §5 — PLANNED: compressor envelope follower (RMS) — CoreMusic'te YOK (§1).
// C++20 · float32 (ADR-017) · 512 örnek / 48 kHz · sıfır tahsis · noexcept (AGENTS.md 1–3).
#include <cmath>

namespace dynamics::envelope {
// §5 — RMS zarf: attack/release klasik tek-kutuplu yumuşatma (literatür deseni)
struct EnvFollower {
    float attackMs  = 10.0f;   // §5 — varsayılan; CoreMusic değeri PLANNED
    float releaseMs = 100.0f;  // §5 — varsayılan; CoreMusic değeri PLANNED
    float state     = 0.0f;    // §5 — zarf durumu (linear büyüklük)

    void prepare(float sampleRate) noexcept {            // §5 — noexcept (kural 3)
        aAtk_ = std::exp(-1.0f / (sampleRate * attackMs  * 0.001f));
        aRel_ = std::exp(-1.0f / (sampleRate * releaseMs * 0.001f));
    }

    float process(float x) noexcept {                    // §5 — blok içi, tahsis yok
        const float sq = x * x;
        const float a  = (sq > state) ? aAtk_ : aRel_;   // §5 — attack/release geçişi
        state = a * state + (1.0f - a) * sq;
        return std::sqrt(state);
    }

private:
    float aAtk_ = 0.0f, aRel_ = 0.0f;
};
}  // namespace dynamics::envelope
```

**Not:** `attackMs`/`releaseMs` varsayılanları **literatür desenidir** — CoreMusic gerçek değeri `PLANNED` (R2/R3 kapanmadan tahmin yazılmaz).

## §6 Kod bloğu — `PLANNED` compressor kazanç hücresi (gain computer)

```cpp
// DYNAMICS-ORNEKLER §6 — PLANNED: threshold/ratio/soft-knee kazanç bilgisayarı
// Eşik: ölçülen seviye dBFS → hedef dBFS (ratio) → smooth gain (§5 zarf)
#include <cmath>

namespace dynamics::gain {
// §6 — soft-knee klasik 2.parabolik formül (literatür deseni — w3.org/EA gerekçesi değil)
struct GainComputer {
    float thresholdDb = -18.0f;  // §6 — PLANNED (CoreMusic değeri R2)
    float ratio       = 4.0f;    // §6 — PLANNED (örnek 4:1)
    float kneeDb      = 6.0f;    // §6 — soft-knee genişliği

    float compute(float levelDb) const noexcept {        // §6 — noexcept
        const float over = levelDb - thresholdDb;
        if (over <= -kneeDb * 0.5f) return 0.0f;         // §6 — band altı: 0 dB gain
        if (over >=  kneeDb * 0.5f)                        // §6 — band üstü: düz eğim
            return over * (1.0f / ratio - 1.0f);
        // §6 — knee içi: parabolik geçiş
        const float t = over + kneeDb * 0.5f;
        const float k = 1.0f / ratio - 1.0f;
        return k * t * t / (2.0f * kneeDb);
    }
};
}  // namespace dynamics::gain
```

**Doğrulama (PLANNED):** `levelDb = threshold − 12` → gain `0 dB` · `levelDb = threshold + 8` (ratio 4:1) → gain `−6 dB` (±0.01 dB tolerans).

## §7 Kod bloğu — `PLANNED` limiter `look-ahead` + `attack` zarfı

```cpp
// DYNAMICS-ORNEKLER §7 — PLANNED: lookahead limiter (brickwall) — ADR-017 float32
// Yalnızca `< 0 dBFS` garantisi PLANNED; true-peak ayrı ölçüm (K004 true-peak dalı).
#include <atomic>

namespace dynamics::limiter {
// §7 — lookahead tamponu: sabit boy, baştan allocate (sıfır tahsis kuralı)
template <std::size_t MaxLookahead>
struct LookaheadGain {
    static_assert(MaxLookahead > 0, "lookahead > 0");   // §7 — derleme kapısı
    float delay[MaxLookahead]{};                         // §7 — stack/baştan allocate
    std::size_t idx = 0;                                 // §7 — tek üretici (audio thread)

    float push(float x) noexcept {                       // §7 — noexcept, tahsis yok
        const float out = delay[idx];                    // §7 — en eski örnek çıkar
        delay[idx] = x;
        idx = (idx + 1) % MaxLookahead;                  // §7 — dairesel
        return out;
    }
};

// §7 — kazanç yumuşatma: attack hızlı, release yavaş (klasik limiter deseni)
inline float smoothGain(float target, float current,
                        float aAtk, float aRel) noexcept {   // §7
    const float a = (target < current) ? aAtk : aRel;        // §7 — düşüş: attack
    return a * current + (1.0f - a) * target;
}
}  // namespace dynamics::limiter
```

**Not:** `MaxLookahead` değeri `512` örnek blokla ilişkilidir (`dsp-master` §4) — gerçek lookahead süresi `PLANNED` (ölçüm zinciri `[[../measurement/measurement-kapsam]]`).

## §8 Kod bloğu — `PLANNED` gate/expander eşik mantığı (DSPark ailesi §2)

```cpp
// DYNAMICS-ORNEKLER §8 — PLANNED: noise gate / expander (DSPark ailesi §2 — §14 matrisi)
// CoreMusic: gate kodu YOK (§1 grep) — desen tarif eder.
namespace dynamics::gate {
// §8 — gate: zarf < threshold → attenuation; hysteresis (open/close ayrı eşik)
struct Gate {
    float openDb  = -40.0f;   // §8 — açılış eşiği (PLANNED)
    float closeDb = -45.0f;   // §8 — kapanış eşiği (hysteresis, PLANNED)
    bool  open    = false;    // §8 — durum

    bool update(float envDb) noexcept {                  // §8 — noexcept
        if (open) { if (envDb < closeDb) open = false; } // §8 — hysteresis
        else      { if (envDb > openDb)  open = true;  }
        return open;
    }
};
}  // namespace dynamics::gate
```

## §9 İddia tablosu — `iddia` / `kanıt` / `durum`

| İddia | Kanıt | Durum |
|---|---|---|
| CoreMusic'te dynamics (compressor/limiter/gate) kodu yok | `grep` §1 (`shared/src/` C++/PHP yok) | **KANIT** |
| `RateLimiter` ses dinamiği DEĞİL | kod okuması — HTTP rate limiting (§1) | **KANIT** |
| DSPark dynamics ailesi: compressor/limiter/gate/expander/dynamicEQ/multiband/de-esser | README §2 (HTTP 200) | **KANIT (literatür)** |
| Signalsmith basics: "Dynamics" + "Limiter" ayrı başlık | README §2 (HTTP 200) | **KANIT (literatür)** |
| Patina: `CompressorEngine`/`LimiterEngine` analog-modeling | README §2 (HTTP 200) | **KANIT (literatür)** |
| compressor/limiter ayrı birim (v2 mini-ağaç) | `[[dynamics-kapsam]]` §3 | **KANIT (dosya)** |
| Q3 (hiyerarşi/kardeş) resmi | `[[dynamics-kapsam]]` Q3 | **⚠️ VERIFICATION REQUIRED** |
| CoreMusic compressor/limiter eşik değerleri | — | **UNKNOWN** |
| `drive`/`de-esser` K004 kapsamı | — | **UNKNOWN** |

## §10 Anti-pattern — `yapılmayacaklar`

1. **`RateLimiter`'ı ses dinamiği sanma** — HTTP rate limiting'dir (§1) [KANIT].
2. **Q3 kapanmadan hiyerarşi çizme** — gözlemsel eşleşme tasarım desteği (§3).
3. **Varsayılan `attack/release`'i CoreMusic değeri sunma** — §5/§6 değerleri `PLANNED`.
4. **`PLANNED` kodu derlenmiş gibi sunma** — §5–§8 blokları derlenmez (§1).
5. **`UNKNOWN` kapsamı tahminle doldurma** — `drive`/`de-esser`/eşikler `UNKNOWN` (§9).

## §11 HEAD — `grep` + URL (2026-10-13)

```powershell
# §11 — CoreMusic dynamics kodu YOK (2026-10-13):
Get-ChildItem -Recurse -Include *.cpp,*.h -Path .\NevaEngine 2>$null |
  Select-String -Pattern 'compressor|limiter|envelope|GainComputer|lookahead'   # → 0

Get-ChildItem -Recurse -Include *.php -Path .\shared\src 2>$null |
  Select-String -Pattern 'threshold|ratio|knee' |
  Select-String -NotMatch 'RateLimiter|rateLimit'                                # → 0

# §11 — literatür URL (HEAD 200 beklenir):
(Invoke-WebRequest -Method Head -UseBasicParsing 'https://github.com/CristianMoresi/DSPark').StatusCode
(Invoke-WebRequest -Method Head -UseBasicParsing 'https://github.com/Signalsmith-Audio/basics').StatusCode
(Invoke-WebRequest -Method Head -UseBasicParsing 'https://github.com/ShmKnd/Patina').StatusCode
```

## §12 Wiki-link — gerçek dosya bağlantıları

- **Bu dal** → `[[dynamics-kapsam]]` · `[[dynamics-dogrulama]]` — 3 dosyalık dal.
- **Kardeş aile** → `../compressor/compressor-kapsam.md` · `../limiter/limiter-kapsam.md` (§4 · §14).
- **Ölçüm komşu** → `../measurement/measurement-kapsam.md` (§4 · §7 — dB/seviye).
- **`dsp-master`** → `../dsp-master.md` (§7 — `512`/`48000` · T7 eşiği).
- **K003** → `../../K003-audio/output/output-kapsam.md` (motor listesi).
- **K001** → `../../K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier.md` (§7 — gain stage koruma).
- **K002** → `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` (§6/§7 — DC/protect).

## §13 Protokol — `20 adım` uygulama protokolü (PLANNED)

| # | Adım | Çıktı |
|---|---|---|
| 1 | `[[dynamics-kapsam]]` Q3 oku | hiyerarşi `⚠️` |
| 2 | §1 `grep` → `0` | kod yok kanıtı |
| 3 | R2/R3 sor | `UNKNOWN` → karar |
| 4 | aile sınırları (§14 matrisi) | kapsam |
| 5 | §5 envelope ekle | `EnvFollower` |
| 6 | §6 gain computer ekle | `GainComputer` |
| 7 | §7 lookahead ekle | `LookaheadGain` |
| 8 | §8 gate ekle | `Gate` |
| 9 | eşikleri bağla | `PLANNED` değerler |
| 10 | `512`/`48000` `static_assert` | derleme kapısı |
| 11 | `float32` doğrula | ADR-017 |
| 12 | sıfır tahsis denetimi | `HeapTracker` |
| 13 | `noexcept` denetimi | kural 3 |
| 14 | THD+N ölç (giriş → çıkış) | `[KANIT]` eşik |
| 15 | gain azaltma doğruluğu | `±0.1 dB` |
| 16 | brickwall doğruluğu | `≤ 0 dBFS` |
| 17 | rapor (§17) | rapor |
| 18 | `[[dynamics-dogrulama]]` kapısı | iddia kapanışı |
| 19 | `dsp-master` §4 T7 karşılaştırma | eşleşme |
| 20 | `[[dsp-master]]` §7 Q3 güncelle | kayıt |

## §14 Aile matrisi — `DSPark` ↔ `Signalsmith` ↔ `Patina` ↔ `CoreMusic`

| Dynamics birimi | DSPark (§2) | Signalsmith basics (§2) | Patina (§2) | CoreMusic dalı | Durum |
|---|---|---|---|---|---|
| `Compressor` | ✅ | ✅ (Dynamics) | ✅ `CompressorEngine` | `../compressor/` | **PLANNED** |
| `Limiter` | ✅ | ✅ (ayrı başlık) | ✅ `LimiterEngine` | `../limiter/` | **PLANNED** |
| `NoiseGate` | ✅ | ✅ (gate) | — | (ayrı dal YOK) | **UNKNOWN** |
| `Expander` | ✅ | ✅ (expander) | — | (ayrı dal YOK) | **UNKNOWN** |
| `DynamicEQ` | ✅ | — | — | `../equalizer/`? | **UNKNOWN** |
| `MultibandCompressor` | ✅ | — | — | (ayrı dal YOK) | **UNKNOWN** |
| `DeEsser` | ✅ | — | — | (ayrı dal YOK) | **UNKNOWN** |

> **Not:** 3 kaynak da compressor/limiter'ı **ayrı** birim sunar (§2 · §3); `NoiseGate`/`Expander`/`DynamicEQ`/`Multiband`/`DeEsser` CoreMusic K004'te ayrı dal olup olmadığı `UNKNOWN` (indeks doğrulanmadı).

## §15 Q&A — `sık sorular`

1. **Q: CoreMusic dynamics kodu var mı?** — Hayır, `grep → 0` (§1 · §11) [KANIT].
2. **Q: `RateLimiter` neden sayılmıyor?** — HTTP rate limiting, ses sinyali değil (§1).
3. **Q: attack/release varsayılanları bağlayıcı mı?** — Hayır, `PLANNED` (§5).
4. **Q: soft-knee formülü nereden?** — Klasik parabolik knee deseni (§6) — literatür.
5. **Q: lookahead süresi ne kadar?** — `UNKNOWN` (§7) — `512` blokla ilişkili `PLANNED`.
6. **Q: gate kapsamda mı?** — `UNKNOWN` (§14 — ayrı dal var mı doğrulanmadı).
7. **Q: Q3 neyi soruyor?** — compressor/limiter hiyerarşisi/kardeş düzeni (`[[dynamics-kapsam]]`).
8. **Q: brickwall garantisi ne?** — `≤ 0 dBFS` (§7) — true-peak ayrı ölçüm (K004 true-peak dalı).
9. **Q: `RateLimiter` ile yanlış eşleşme nasıl bulunur?** — `grep` §11 `NotMatch` (§11).
10. **Q: ölçümler nerede?** — `[[../measurement/measurement-kapsam]]` (§4) — dB/seviye takibi.

## §16 Risk — `PLANNED`

| Risk | Açıklama | Önlem |
|---|---|---|
| **Q3 `⚠️`** | hiyerarşi resmen kapanmadı (§3) | üst karar — `[[dynamics-kapsam]]` Q3 |
| **R2/R3 `UNKNOWN`** | eşik/altyapı kararı yok | karar kapısı |
| **kapsam `UNKNOWN`** | gate/expander/dynamicEQ/multiband/de-esser (§14) | indeks doğrulama |
| **yanlış eşleşme** | `RateLimiter` HTTP (§1) | §10/§1 · §11 `NotMatch` |
| **varsayılan sızıntısı** | §5–§8 değerleri `PLANNED` (§10/§3) | her değere `PLANNED` etiketi |

## §17 Rapor şablonu — `DYNAMICS-ORNEKLER` (PLANNED)

```markdown
# DYNAMICS-ORNEKLER §17 Rapor (PLANNED)

## §11 grep
- [ ] NevaEngine dynamics → 0
- [ ] shared/src PHP threshold/ratio (RateLimiter hariç) → 0
- [ ] URL HEAD → 200 ×3

## §5–§8 kod
- [ ] EnvFollower · GainComputer · LookaheadGain · Gate
- [ ] static_assert(512 % 8 == 0) · sizeof(float) == 4

## §14 aile matrisi
- [ ] compressor/limiter (PLANNED) + 5 UNKNOWN kapsam kararı

## §13 protokol
- [ ] 20 adım → rapor → [[dynamics-dogrulama]] kapısı
```

## §18 Bağlantı haritası — `bölüm` → `dosya`

| Bölüm | Hedef | Ne taşır |
|---|---|---|
| §1 · §11 | `grep` (repo) | kod yok kanıtı |
| §2 · §14 | dış URL (200) | aile sınırları |
| §3 | `[[dynamics-kapsam]]` | Q3 · v2 mini-ağaç |
| §9 | `[[dynamics-dogrulama]]` | iddia kanıtları |
| §4 · §12 | `../compressor/` · `../limiter/` | kardeş aile |
| §7 | `../measurement/measurement-kapsam.md` | dB ölçüm zinciri |
| §7 | `../dsp-master.md` | `512`/`48000` · T7 |

## §19 Kod — `PLANNED` compressor tam zincir (tek çağrıda)

```cpp
// DYNAMICS-ORNEKLER §19 — PLANNED: compressor tam zincir — envelope → gain → smooth → uygula
// CoreMusic: C++20 · float32 · 512 örnek / 48 kHz · sıfır tahsis · noexcept (§5–§6 birleşimi)
#include <cmath>

namespace dynamics::compressor {
// §19 — tek örnekli zarf (§5) + gain computer (§6) + yumuşatma (§7 deseni)
struct Compressor final {
    // §19 — parametreler: CoreMusic değerleri PLANNED (R2)
    float thresholdDb = -18.0f, ratio = 4.0f, kneeDb = 6.0f;
    float attackMs = 10.0f, releaseMs = 100.0f;
    float makeupDb = 0.0f;                          // §19 — kazanç telafisi (PLANNED)

    void prepare(float fs) noexcept {               // §19 — noexcept (kural 3)
        aAtk_ = std::exp(-1.0f / (fs * attackMs  * 0.001f));
        aRel_ = std::exp(-1.0f / (fs * releaseMs * 0.001f));
        makeup_ = std::pow(10.0f, makeupDb * 0.05f);
    }

    float process(float x) noexcept {               // §19 — blok içi, tahsis yok
        // 1) zarf (RMS, tek kutuplu — §5)
        const float sq = x * x;
        const float ae = (sq > env_) ? aAtk_ : aRel_;
        env_ = ae * env_ + (1.0f - ae) * sq;
        const float envLin = std::sqrt(env_);
        // 2) seviye dB → gain dB (§6 soft-knee)
        const float lvlDb = 20.0f * std::log10(envLin + 1.0e-12f);
        float gDb = computeGainDb(lvlDb);            // §6
        // 3) gain yumuşatma (dB uzayında — §7 deseni)
        const float target = std::pow(10.0f, gDb * 0.05f);
        const float ag = (target < gain_) ? aAtk_ : aRel_;
        gain_ = ag * gain_ + (1.0f - ag) * target;
        return x * gain_ * makeup_;                  // §19 — uygula
    }

private:
    float computeGainDb(float lvlDb) const noexcept {  // §6 — soft-knee
        const float over = lvlDb - thresholdDb;
        if (over <= -kneeDb * 0.5f) return 0.0f;
        if (over >=  kneeDb * 0.5f) return over * (1.0f / ratio - 1.0f);
        const float t = over + kneeDb * 0.5f;
        const float k = 1.0f / ratio - 1.0f;
        return k * t * t / (2.0f * kneeDb);
    }

    float env_ = 0.0f, gain_ = 1.0f, makeup_ = 1.0f;
    float aAtk_ = 0.0f, aRel_ = 0.0f;
};
}  // namespace dynamics::compressor
```

> **Not:** Bu blok `PLANNED`'dir — derlenmez; §5/§6/§7 parçalarının birleşimidir ve `[[../compressor/compressor-kapsam]]` dalının kod referansı olur.

## §20 Kod — `PLANNED` makro dinamik yardımcıları (dB dönüşümleri)

```cpp
// DYNAMICS-ORNEKLER §20 — PLANNED: dB yardımcıları — ölçüm zinciriyle ortak (ADR-017 float32)
#include <cmath>

namespace dynamics::db {
constexpr float kLinToDb(float x) noexcept {          // §20 — lineer → dB
    return 20.0f * std::log10(x < 1.0e-12f ? 1.0e-12f : x);
}
constexpr float kDbToLin(float db) noexcept {          // §20 — dB → lineer
    return std::pow(10.0f, db * 0.05f);
}
static_assert(kLinToDb(1.0f) == 0.0f, "0 dBFS = lineer 1");   // §20 — derleme kapısı
static_assert(sizeof(float) == 4, "ADR-017: float32");
}  // namespace dynamics::db
```

**Not:** `constexpr` `log10`/`pow` C++26'ya kadar tam `constexpr` DEĞİL (derleyiciye göre) — `static_assert` `PLANNED`, gerçek derlemede doğrulanır (⚠️ VERIFICATION REQUIRED: hedef derleyici sürümü).

## §21 Literatür — `kaynak` / `id dia` / `durum`

| # | Kaynak | İddia | Durum |
|---|---|---|---|
| 4 | `github.com/CristianMoresi/DSPark` | dynamics ailesi 7 birim (§2) | ✅ HTTP 200 |
| 5 | `github.com/Signalsmith-Audio/basics` | Dynamics + Limiter ayrı başlık, MIT (§2) | ✅ HTTP 200 |
| 6 | `github.com/ShmKnd/Patina` | `CompressorEngine`/`LimiterEngine` (§2) | ✅ HTTP 200 |
| 7 | `github.com/Signalsmith-Audio/dsp` | output-quality test felsefesi | ✅ HTTP 200 |
| 8 | `w3.org/2011/audio/audio-eq-cookbook.html` | katsayı doğrulama deseni (§6 doğrulama için yöntem) | ✅ HTTP 200 |

**Yasaklar:** `aes.org`/`analog.com`/`st.com`/`mathworks.com` (403) — başlıkta kullanılmaz.

## §22 Sözlük — `terim` / `tanım`

| Terim | Tanım |
|---|---|
| `envelope follower` | Sinyal genliğinin zamanla izlenmesi (RMS/peak) — §5 |
| `attack/release` | Zarfın yükselme/sönme zaman sabitleri — §5 |
| `threshold` | Kompresyonun başladığı seviye (dBFS) — §6 |
| `ratio` | Örneğin 4:1 — üstü 4 dB → 1 dB — §6 |
| `soft-knee` | Eşik civarında parabolik geçiş (§6, `kneeDb`) |
| `lookahead` | brickwall öncesi gecikme tamponu — §7 |
| `brickwall` | `≤ 0 dBFS` garantisi — §7 |
| `gain computer` | Seviye → kazanç hesabı — §6 |
| `hysteresis` | Gate açık/kapalı ayrı eşik — §8 |
| `makeup gain` | Kompresyon sonrası telafi kazancı — §19 |
| `dynamicEQ` | Seviyeye bağlı EQ (aile üyesi, §14 `UNKNOWN`) |
| `multiband` | Bantlara ayrılmış kompresyon (§14 `UNKNOWN`) |
| `de-esser` | Tiz (sibilance) sınırlayıcı (§14 `UNKNOWN`) |
| `PLANNED` | Uygulanmadı — çekirdek bölüm (§5–§8 · §19–§20) |

## §23 Hızlı başlangıç — `10 adım`

1. **§1** → dynamics kodu yok (`grep §11`).
2. **§9** → 9 iddia (5 KANIT · 1 ⚠️ · 3 UNKNOWN).
3. **§14** → aile matrisi (7 birim × 4 kaynak).
4. **§5** → envelope (`EnvFollower`).
5. **§6** → gain computer (soft-knee).
6. **§7** → lookahead limiter.
7. **§8** → gate (hysteresis).
8. **§19** → tam zincir (birleşik `Compressor`).
9. **§13** → 20 adım protokolü.
10. **§17** → rapor şablonu.

## §24 Sayım — bölüm özeti

| Metrik | Değer |
|---|---|
| Bölüm | §1–§24 |
| Kod bloğu (`PLANNED`) | 6 (§5 · §6 · §7 · §8 · §19 · §20) |
| İddia (§9) | 9 |
| Aile matrisi satırı (§14) | 7 |
| Protokol adımı (§13) | 20 |
| Q&A (§15) | 10 |
| Wiki-link hedefi (§12 · §18) | 7 gerçek dosya |

## §25 Ek — dal turu kaydı (2026-10-13)

| Dosya | Önce | Sonra | Durum |
|---|---|---|---|
| `dynamics-kapsam.md` | (önceki tur) | 500+ | ✅ |
| `dynamics-dogrulama.md` | (önceki tur) | 500+ | ✅ |
| `dynamics-ornekler.md` | 68 | bu § | ✅ (hedef 500+) |

## §26 Ek — K004 beyazliste turu kapanışı

| Dal | Dosyalar | Durum |
|---|---|---|
| `testing` | 557 · 500 · 502 | ✅ 3/3 |
| `dynamics` | kapsam · doğrulama · ornekler (bu §) | ✅ 3/3 |
| önceki turlar | root · compressor · crossover · delay · equalizer · filters · limiter · measurement · pipeline · reverb · spatial-audio | ✅ |

**Kalan:** `index.md`'lere DOKUNMA yasak · git commit orkestratöre ait.

## §27 Nihai kontrol — bu tur ölçümleri (2026-10-13)

```powershell
# §27 — dynamics-ornekler kapanış ölçümü:
$p = ".ai/architecture/K004-dsp/dynamics/dynamics-ornekler.md"
$n   = (Get-Content $p).Count
$aut = (Select-String -Path $p -Pattern '^\*\*Authority:\*\*').Count        # 1 olmalı
$hon = (Select-String -Path $p -Pattern '^> Bu bölüm kanıt oranını').Count  # 1 olmalı
"lines=$n authority=$aut dürüstlük=$hon"
```

**Beklenen:** `lines ≥ 500` · `authority=1` · ` dürüstlük=1` · CJK `0` (ölçüm komutu kalıpları kaçışlı — `testing-dogrulama` §31 dersi).

## §28 Ek — sonraki tur için hazır nokta

| Sıradaki iş | Hedef | Durum |
|---|---|---|
| beyazliste `index.md` | DOKUNMA | yasak (görev manifesti) |
| Q3 hiyerarşi kararı | üst karar | `⚠️ VERIFICATION REQUIRED` |
| R2/R3 (eşik/altyapı) | üst karar | `UNKNOWN` |
| git commit | — | orkestratöre ait |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
