---
node_id: K004-dsp/kod-ornekleri
node_type: subdomain
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K002-electroncis
  - K003-audio
  - K005-firmware
  - K006-codebase
evidence:
  - ".ai/architecture/K004-dsp/dsp-master.md §A (12 çocuk, §A.7 grup eşlemesi) — okundu 2026-10-18"
  - ".ai/architecture/K004-dsp/dsp-kararlar.md (KD- önek kuralı, ADR atıf) — okundu 2026-10-18"
  - "grep: repo'da WebAudio/BiquadFilter/DynamicsCompressor=YOK; C++ motor dosyası YOK — 2026-10-18"
validation: "13 dil × biquad/compressor/limiter/LUFS; THERM: tüm bloklar şartname gövdesi (repo'da DSP kodu 0)"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — T8 saatlik bütçesi K004'te UNKNOWN (dsp-master);
  kod blokları üretim öncesi .ai/templates/ gerçek motor koduyla doğrulanacak,
  satır numarası UYDURULMADI (repo'da DSP kaynağı 0).
group: kod
---

# K004-dsp — Kod Örnekleri (DSP algoritma sözleşmeleri)

## §1 — ELI15: Bu nedir?

Ses sinyalini işleyen matematik parçalarının her dilde "nasıl çizilir" kartpostalları.
Biquad (EQ'nun tuğlası), kompresör (sesi kısma), limiter (tavana çarpma), LUFS
(insan kulağına göre loudness). **Önemli dürüstlük notu:** repo'da DSP motor
kodu YOK (grep: `*.cpp`/`*.h`/WebAudio = 0) — bu dosyanın tüm blokları
**şartname gövdesidir**, "işte böyle olacak" çizimleridir, üretim kodu değil.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Biquad (RBJ), kompresör, limiter, LUFS/EBU R128, FIR, oversampling |
| Kapsam dışı | Backend sürücü kodu (K003-audio kod-ornekleri) · donanım/PCB (K002-electroncis) |

## §3 — Sözleşme örnekleri (şartname gövdesi — 13 dil)

### 3.1 C — RBJ biquad (EQ çekirdeği)

```c
/* Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
 * RBJ Audio EQ Cookbook lowpass: tek tuğla, tüm EQ preset'leri bundan türetilir. */
#include <math.h>

typedef struct { float b0, b1, b2, a1, a2, z1, z2; } biquad_t;

void biquad_set_lowpass(biquad_t *q, float fs, float f0, float Q) {
    float w0 = 2.0f * 3.14159265f * f0 / fs;
    float c = cosf(w0), s = sinf(w0), alpha = s / (2.0f * Q);
    float b0 = (1 - c) / 2, b1 = 1 - c, b2 = (1 - c) / 2;
    float a0 = 1 + alpha, a1 = -2 * c, a2 = 1 - alpha;
    q->b0 = b0 / a0; q->b1 = b1 / a0; q->b2 = b2 / a0;  // a0'a böl, katsayıları normalize et
    q->a1 = a1 / a0; q->a2 = a2 / a0;
    q->z1 = q->z2 = 0.0f;                                  // durum sıfırla
}

/* Direct Form II transposed — tek geçiş, 2 delay element. */
float biquad_process(biquad_t *q, float x) {
    float y = q->b0 * x + q->z1;
    q->z1 = q->b1 * x - q->a1 * y + q->z2;
    q->z2 = q->b2 * x - q->a2 * y;
    return y;
}
```

### 3.2 C++ — Feed-forward kompresör (threshold/ratio/attack/release)

```cpp
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Sözleşme: level→gain bilanço; attack/release tek kutuplu yumuşatma.
#include <cmath>

class Compressor {
    float thresholdDb_, ratio_, attackCoef_, releaseCoef_;
    float envelopeDb_{-100.0f};

public:
    Compressor(float thDb, float ratio, float attackMs, float releaseMs, float fs)
        : thresholdDb_(thDb), ratio_(ratio)
    {
        attackCoef_  = std::exp(-1.0f / (attackMs  * 0.001f * fs));  // katsayı → zaman sabiti
        releaseCoef_ = std::exp(-1.0f / (releaseMs * 0.001f * fs));
    }

    float gainDb(float inDb) {
        // Grapik kırılma: threshold üstünde over → ratio ile kıs
        float over = inDb - thresholdDb_;
        float outDb = (over > 0.0f) ? thresholdDb_ + over / ratio_ : inDb;
        return outDb - inDb;                            // negatif gain (kısma)
    }

    void processBlock(float *samples, int n) {          // tek kanal blok
        for (int i = 0; i < n; ++i) {
            float inDb = 20.0f * std::log10(std::fabs(samples[i]) + 1e-9f);
            float target = gainDb(inDb);
            float coef = (target < envelopeDb_) ? attackCoef_ : releaseCoef_;
            envelopeDb_ = target + coef * (envelopeDb_ - target);  // yumuşatma
            samples[i] *= std::pow(10.0f, envelopeDb_ * 0.05f);    // uygula
        }
    }
};
```

### 3.3 C# — LUFS anlık loudness (EBU R128)

```csharp
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// ITU-R BS.1770-4 K-weighting + 400ms blok loudness (LUFS hesabı basitleştirilmiş).
public sealed class LoudnessMeter
{
    // K-weighting high-shelf + high-pass katsayıları (48 kHz)
    private const float GainDb = 3.99984f;

    /// Bir bloğun LUFS değeri (momentary loudness).
    public double MomentaryLufs(ReadOnlySpan<double> weightedMeanSquares)
    {
        double sum = 0.0;
        foreach (var ms in weightedMeanSquares) sum += ms;   // kanal ağırlıklı toplam
        double lufs = -0.691 + 10.0 * Math.Log10(sum + 1e-12);
        return lufs;                                          // tipik hedef: -23 LUFS (EBU R128)
    }
}
```

### 3.4 Rust — Brickwall limiter (tavan + lookahead)

```rust
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Sözleşme: sample asla ceilingDb'yi aşamaz; lookahead = tanılama payı.
pub struct Limiter {
    ceiling: f32,             // örn. -0.3 dBTP
    gain: f32,                // güncel kısma gain'i
    delay_line: Vec<f32>,     // lookahead tamponu (allocate EDİLMEDİ — K005 kuralı)
    read: usize,
}

impl Limiter {
    /// Blok işleme: her örnek ceiling altında kalır (yapısal).
    pub fn process(&mut self, buf: &mut [f32]) {
        for s in buf.iter_mut() {
            // 1) lookahead tamponuna yaz, 2) en büyük genlik için gereken gain'i hesapla
            let delayed = self.push(*s);
            let peak = delayed.abs().max(self.gain.min(1.0));
            let need = if peak > self.ceiling { self.ceiling / peak } else { 1.0 };
            self.gain = need.min(self.gain + 0.0005f32);  // yumuşak saldırı
            *s = delayed * self.gain;                     // garanti: |s| <= ceiling
        }
    }
}
```

### 3.5 Go — Parametre doğrulama (preset şema kapısı)

```go
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// C-prof-1 sözleşmesi: preset yüklerken aralık dışı değer REDDEDİLİR.
package dsp

import "fmt"

type BiquadParams struct {
	FreqHz  float64 // 20..20000
	GainDb  float64 // -24..+24
	Q       float64 // 0.1..10
	Filter  string  // lowpass|highpass|peaking|lowshelf|highshelf
}

func (p BiquadParams) Validate(fs float64) error {
	if p.FreqHz < 20 || p.FreqHz > fs/2 { // Nyquist sınırı (fs/2) aşımı = geçersiz
		return fmt.Errorf("freq %v out of [20, %v]", p.FreqHz, fs/2)
	}
	if p.GainDb < -24 || p.GainDb > 24 {
		return fmt.Errorf("gain %v dB out of [-24, 24]", p.GainDb)
	}
	if p.Q < 0.1 || p.Q > 10 {
		return fmt.Errorf("Q %v out of [0.1, 10]", p.Q)
	}
	return nil
}
```

### 3.6 Java — EBU R128 loudness hedefi (normalizasyon)

```java
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// EBU R128: program loudness -23 LUFS, true peak -1 dBTP (yayın standardı).
public final class R128Target {
    public static final double TARGET_LUFS = -23.0;
    public static final double TRUE_PEAK_DBTP = -1.0;

    /** Ölçülen loudness'tan gereken düzeltme (dB). */
    public static double correctionDb(double measuredLufs) {
        return TARGET_LUFS - measuredLufs;   // pozitif → yükselt, negatif → kıs
    }

    /** Düzeltme sonrası true peak kontrolü: taşarsa limit uygula. */
    public static boolean needsLimiter(double measuredPeakDb, double correctionDb) {
        return measuredPeakDb + correctionDb > TRUE_PEAK_DBTP;  // aşım → limiter zorunlu
    }
}
```

### 3.7 Python — Frekans tarama üretimi (test sinyali)

```python
# Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
# Test: 20 Hz → 20 kHz log sweep; EQ düzlem doğrulaması için referans.
import numpy as np

def log_sweep(fs: int, duration: float = 5.0,
              f1: float = 20.0, f2: float = 20000.0) -> np.ndarray:
    """Exponential sine sweep (Farina): frekans üstel artar."""
    n = int(fs * duration)
    t = np.arange(n) / fs
    k = np.log(f2 / f1) / duration                       # üstel katsayı
    phase = 2 * np.pi * f1 * (np.exp(k * t) - 1) / k
    return np.sin(phase).astype(np.float32)

def thd_n(reference: np.ndarray, measured: np.ndarray, fs: int) -> float:
    """1 kHz THD+N: temel hariç tüm harmonik + gürültü oranı."""
    spec = np.fft.rfft(measured)
    freqs = np.fft.rfftfreq(len(measured), 1 / fs)
    fundamental = np.abs(spec[np.argmin(np.abs(freqs - 1000))])
    total = np.sqrt(np.sum(np.abs(spec) ** 2))
    noise = np.sqrt(max(total ** 2 - fundamental ** 2, 0.0))
    return noise / (fundamental + 1e-12)                 # hedef: < 0.0001 (%0.01)
```

### 3.8 Swift — AU v3 render (K00310 köprüsü, ADR-037/038)

```swift
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// AU pull-model: HAL render çağırır → DSP zinciri burada işler (doc #1).
import AudioUnit

final class DspRenderBus {
    // Zincir: input → EQ (biquad dizisi) → kompresör → limiter → output
    func render(actionFlags: UnsafeMutablePointer<AudioUnitRenderActionFlags>,
                timestamp: AudioTimeStamp,
                frameCount: AUAudioFrameCount,
                outputData: UnsafeMutablePointer<AudioBufferList>) {
        // 1) Her biquad tuğlasını sırayla çalıştır (§3.1 sözleşmesi)
        // 2) Kompresör envelope güncelle (§3.2)
        // 3) Limiter ceiling garantisi (§3.4)
        // allocate/lock YASAK (K005-firmware, KA-A6)
    }
}
```

### 3.9 Kotlin — DSP zinciri (düğüm grafiği)

```kotlin
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Zincir = düğüm listesi; her düğüm process(buffer) (AUGraph benzeri, doc #3).
interface DspNode {
    fun process(buffer: FloatArray, sampleRate: Int)
    fun reset()
}

class DspChain(private val nodes: List<DspNode>) {
    fun process(buffer: FloatArray, sampleRate: Int) {
        for (node in nodes) node.process(buffer, sampleRate)  // sıralı geçiş
    }
    fun bypass(node: DspNode) { /* zincirden çıkar, ses yolunu koru */ }
}
```

### 3.10 TypeScript — Parametre tipi (UI ↔ motor sözleşmesi)

```typescript
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// UI (K011-ui-kit) ↔ motor sözleşmesi: tip güvenli parametre nesnesi.
export type FilterType = 'lowpass' | 'highpass' | 'peaking' | 'lowshelf' | 'highshelf';

export interface DspParam {
  readonly id: string;          // örn. "eq.band1.freq"
  readonly type: FilterType;
  readonly value: number;       // Validate() aralığında (§3.5)
  readonly unit: 'hz' | 'db' | 'q' | 'ms' | 'lufs';
  readonly min: number;
  readonly max: number;
}

// Motor'a gönderim: JSON + şema sürümü (C-prof-1, K003 köprüsü)
export interface DspPresetMessage {
  readonly schemaVersion: string;
  readonly params: readonly DspParam[];
}
```

### 3.11 SQL — Preset saklama (MySQL 8)

```sql
-- Şartname gövdesi — üretim şeması .ai/templates/ ile doğrulanacak (⚠️).
-- Kural: SELECT * yok, parametreli sorgu (global §5).
CREATE TABLE dsp_preset (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id         BIGINT UNSIGNED NOT NULL,
    name            VARCHAR(120) NOT NULL,
    schema_version  VARCHAR(16) NOT NULL,        -- C-prof-1 migration
    kind            ENUM('eq','compressor','limiter','chain') NOT NULL,
    payload         JSON NOT NULL,               -- biquad/kompresör parametreleri
    lufs_target     DECIMAL(5,2) NULL,           -- EBU R128 hedefi (-23.0)
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_preset_user_kind (user_id, kind),
    CONSTRAINT fk_dsp_preset_user FOREIGN KEY (user_id) REFERENCES user (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 3.12 PHP 8.4 — Preset doğrulama servisi (repo kalıbına uygun)

```php
<?php declare(strict_types=1);
// Şartname gövdesi — üretim kodu .ai/templates/ ile doğrulanacak (⚠️).
// Kalıp: MusicStreamHandler strict_types + final class (gerçek repo kanıtı).
namespace CoreMusic\Dsp;

final class BiquadValidator
{
    /** @var array<string, true> */
    private const ALLOWED_TYPES = [
        'lowpass' => true, 'highpass' => true, 'peaking' => true,
        'lowshelf' => true, 'highshelf' => true,
    ];

    public static function validate(array $p, float $sampleRate): true
    {
        $type = (string)($p['type'] ?? '');
        if (!isset(self::ALLOWED_TYPES[$type])) {
            throw new \InvalidArgumentException("geçersiz filtre türü: {$type}");
        }
        $freq = (float)($p['freq'] ?? 0.0);
        if ($freq < 20.0 || $freq > $sampleRate / 2.0) {   // Nyquist sınırı
            throw new \InvalidArgumentException('frekans aralık dışı');
        }
        return true;
    }
}
```

### 3.13 SQL (ek) — Ölçüm sonuçları (test raporu saklama)

```sql
-- Şartname gövdesi — üretim şeması .ai/templates/ ile doğrulanacak (⚠️).
-- Test:stratejisi T-D ölçümlerinin saklanması (null test, sweep, R128).
CREATE TABLE dsp_measurement (
    id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    preset_id    BIGINT UNSIGNED NOT NULL,
    test_kind    ENUM('null','sweep','thd_n','lufs') NOT NULL,
    sample_rate  SMALLINT UNSIGNED NOT NULL,
    measured_db  DECIMAL(8,3) NOT NULL,     -- sonuç (dB / dBFS / LUFS)
    passed       TINYINT(1) NOT NULL,       -- kapı sonucu
    measured_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_meas_preset_kind (preset_id, test_kind),
    CONSTRAINT fk_meas_preset FOREIGN KEY (preset_id) REFERENCES dsp_preset (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## §4 — Dil × algoritma matrisi

| # | Dil | Algoritma | Blok | Durum |
|---|---|---|---|---|
| 1 | C | RBJ biquad (DF-II T) | §3.1 | ⚠️ Şartname |
| 2 | C++ | Feed-forward kompresör | §3.2 | ⚠️ Şartname |
| 3 | C# | LUFS (K-weighting) | §3.3 | ⚠️ Şartname |
| 4 | Rust | Brickwall limiter | §3.4 | ⚠️ Şartname |
| 5 | Go | Preset doğrulama | §3.5 | ⚠️ Şartname |
| 6 | Java | EBU R128 hedef | §3.6 | ⚠️ Şartname |
| 7 | Python | Sweep + THD/N | §3.7 | ⚠️ Şartname |
| 8 | Swift | AU render | §3.8 | ⚠️ Şartname |
| 9 | Kotlin | DSP zinciri grafiği | §3.9 | ⚠️ Şartname |
| 10 | TypeScript | Parametre tipi | §3.10 | ⚠️ Şartname |
| 11 | SQL | Preset tablosu | §3.11 | ⚠️ Şartname |
| 12 | PHP 8.4 | Preset validator | §3.12 | ⚠️ Şartname |
| 13 | SQL (ek) | Ölçüm raporu | §3.13 | ⚠️ Şartname |

## §5 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 13 dil × algoritma | bu dosya §3 | ✓ 2026-10-18 |
| Repo'da DSP kodu yok → şartname | grep: WebAudio/biquad/C++ = 0 | ✓ 2026-10-18 |
| PHP kalıbı (strict_types/final) | `MusicStreamHandler.php` L1, L21 | ✓ Okundu 2026-10-18 |
| T8 saatlik bütçe | `dsp-master.md` | ⚠️ UNKNOWN (test-stratejisi dosyasında işaretli) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
