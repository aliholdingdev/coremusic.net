---
node_id: electroncis-kod-ornekleri
node_type: subdomain
status: draft
parent_id: K002-electroncis
children: []
depends_on: [K000-isletim-sistemi, K001-donanım, K004-dsp, K005-firmware]
evidence:
  - ".ai/architecture/K002-electroncis/electroncis-master.md §6 (SNR/THD/ripple eşikleri) · §9 #7 ('elektronik kod repo'da YOK') · 2026-10-18"
  - ".ai/architecture/K002-electroncis/electroncis-web-arastirma.md §2 (INA219 · UCC27524 · TPS54202 · PCM3168A bileşen kanıtı) · 2026-10-18"
  - ".ai/.decisions/accepted/ADR-038 (PCM3168A kilidi) · ADR-062 §2 (satır 94 — seri zincir pipeline) · 2026-10-18"
validation: "Repo'da elektronik kodu YOK (master §9 #7) → tüm bloklar SPEC ÖRNEĞİDİR, repo dosyası değildir; her blok dil etiketli, 15–60 satır arası, yorum satırlı; eşikler master §6/§14'ten alınır, uydurulmaz."
open_questions:
  - "⚠️ Bloklar üretim öncesi ilgili K'nin sahibi ajan tarafından K005-firmware (C/C++/Rust gerçek firmware) ve K004-dsp (DSP çekirdekleri) ile birleştirilmelidir — bu dosya tek başına çalışan üretim kodu DEĞİLDİR."
  - "⚠️ Verilog/SQL blokları simulatedir; gerçek FPGA bitstreami ve üretim şeması henüz yok → '⚠️ VERIFICATION REQUIRED'."
group: mimari
---

# K002-electroncis — Kod Örnekleri (13 Dil · Spec Örnekleri)

> **Önemli (master §9 #7):** CoreMusic repo'sunda elektronik domainine ait
> kaynak kod **YOKTUR**. Bu dosyadaki tüm bloklar **şartname örnekleridir** —
> laboratuvar/CI ortamında çalışan referans snippets; repo'ya kopyalanacak
> üretim kodu değildirler. Eşik değerleri `electroncis-master.md` §6/§14'ten
> alınmıştır.

## §1 ELI15

Bir devrenin kuralını anlatmanın en dili açıcı yolu, o kuralı yazmaktır: "ripple
10 mV'u geçmesin" cümlesi, bunu kontrol eden on satırlık bir fonksiyona
dönüştüğünde gerçekten ne yaptığı anlaşılır. Bu dosya K002'nin her kritik kuralını
(SNR, THD+N, ripple, DC offset, termal koruma) 13 farklı dilde küçük, etiketli,
yorumlu örneklerle gösterir — firmware ekibi hangi dili kullanırsa kullansın
aynı şartnameyi okusun diye.

## §2 Eşik Tablosu (şartname kaynağı: master §6)

| # | Eşik | Değer | Bloklarda gösteren |
|---|---|---|---|
| 1 | SNR | >100 dB | PHP · Python · C# |
| 2 | THD+N @1 kHz | <0.01 % (1 W) · <0.001 % (10 W) | JS · Go |
| 3 | PVDD ripple | <10 mV | C · Rust |
| 4 | DC offset koruma | >0.5 V → röle aç | C++ · Java |
| 5 | Termal koruma | >80 °C → shutdown (5-kademe) | TypeScript · SQL |
| 6 | I2C INA219 okuma | 12 bit · 26 V sınırı | Bash · Verilog |

## §3 Dil Blokları

### §3.1 PHP 8.4 — AES17 fabrika testi kapısı (SNR/THD kontrolü)

```php
<?php
// SPEC ÖRNEĞİ — repo dosyası değil; ADR-063 §2(f) fabrika testi kapısı
declare(strict_types=1);

final class Aes17Gate
{
    // master §6 eşikleri — sabittir, fonksiyon girdisi DEĞİLDİR
    private const float MIN_SNR_DB      = 100.0;
    private const float MAX_THD_1W_PCT  = 0.01;
    private const float MAX_THD_10W_PCT = 0.001;

    /** @param array{snr_db:float,thd_pct:float,power_w:float} $m Ölçüm sonucu */
    public function pass(array $m): bool
    {
        if ($m['snr_db'] < self::MIN_SNR_DB) {
            return false; // SNR kapısı — üretim reddi
        }
        // THD eşiği güce göre değişir: 10 W'da 10 kat sıkı
        $thdLimit = $m['power_w'] >= 10.0
            ? self::MAX_THD_10W_PCT
            : self::MAX_THD_1W_PCT;

        return $m['thd_pct'] < $thdLimit;
    }
}
```

### §3.2 JavaScript (Node) — I2S kareleme hesabı (BCLK/MCLK)

```js
// SPEC ÖRNEĞİ — PCM3168A clock ağacı (ADR-038); repo dosyası değil
'use strict';

/**
 * I2S bit clock hesaplama: BCLK = fs × kanal × bit_derinligi
 * @param {number} fs      - Örnekleme frekansı (örn. 48000)
 * @param {number} ch      - Kanal sayısı (TDM'de slot sayısı)
 * @param {number} bits    - Bit derinliği (32-bit float zincir: ADR-017)
 * @returns {number} BCLK (Hz)
 */
function calcBclk(fs, ch, bits) {
  if (fs <= 0 || ch <= 0 || bits <= 0) throw new Error('gecersiz girdi');
  return fs * ch * bits;
}

// 48 kHz × 8 kanal (PCM3168A) × 32 bit = 12.288 MHz
const bclk = calcBclk(48_000, 8, 32);
console.log(`BCLK = ${bclk} Hz`); // 12288000 — master §4.4 I2S eşikleri
```

### §3.3 TypeScript — termal koruma durum makinesi (5 kademe)

```ts
// SPEC ÖRNEĞİ — master §6.3 termal ölçek; repo dosyası değil
type ThermalState = 'NORMAL' | 'WARM' | 'HOT' | 'CRITICAL' | 'SHUTDOWN';

// master §6.3: 5 kademe — eşikler şartnameden, değiştirilemez
const STAGES = [
  { maxC: 60, state: 'NORMAL' },
  { maxC: 70, state: 'WARM' },
  { maxC: 80, state: 'HOT' },      // derating başlangıcı
  { maxC: 90, state: 'CRITICAL' },
  { maxC: Infinity, state: 'SHUTDOWN' }, // >80 °C kademeli koruma
] as const;

export function thermalStage(tempC: number): ThermalState {
  if (tempC < 0 || Number.isNaN(tempC)) {
    throw new RangeError('sicaklik gecersiz (sensor hatasi?)');
  }
  return STAGES.find((s) => tempC <= s.maxC)!.state;
}
// thermalStage(85) → 'CRITICAL' · thermalStage(95) → 'SHUTDOWN'
```

### §3.4 Python — THD+N ölçüm zinciri (FFT + harmonik toplam)

```python
# SPEC ÖRNEĞİ — AES17 test setup; repo dosyası değil
import numpy as np

MIN_SNR_DB = 100.0        # master §6
MAX_THD_1W = 0.01         # % — 1 W
MAX_THD_10W = 0.001       # % — 10 W

def thd_n_percent(samples: np.ndarray, fs: float, f0: float = 1000.0) -> float:
    """1 kHz test tonu üzerinde THD+N hesabı (harmonik toplam / fundamental)."""
    spectrum = np.abs(np.fft.rfft(samples * np.hanning(len(samples))))
    freqs = np.fft.rfftfreq(len(samples), 1.0 / fs)
    idx0 = np.argmin(np.abs(freqs - f0))          # fundamental bin
    v1 = spectrum[idx0]                            # temel genlik
    if v1 == 0:
        raise ValueError("fundamental yok — sinyal kopuk")
    harmonic = sum(spectrum[i * idx0] for i in range(2, 11))  # H2..H10
    return float(harmonic / v1 * 100.0)

def gate(thd_pct: float, power_w: float) -> bool:
    limit = MAX_THD_10W if power_w >= 10.0 else MAX_THD_1W
    return thd_pct < limit
```

### §3.5 C — INA219 I2C register okuma (12 bit, 26 V sınırı)

```c
/* SPEC ÖRNEĞİ — INA219 (web-arastirma §2.1 #2); repo dosyası değil */
#include <stdint.h>
#include <stdbool.h>

#define INA219_ADDR        0x40u   /* 16 adresli (A0..A3) */
#define INA219_REG_CURRENT 0x04u   /* akım registerı */
#define INA219_SHUNT_UOHM  100u    /* 0.1 ohm shunt = 100 uohm */
#define INA219_BUS_MAX_V   26.0f   /* datasheet sınırı: 0..26 V */

/* calibration: current_lsb = max_expected_A / 32768 */
bool ina219_init(float max_a, int (*i2c_write)(uint8_t, uint8_t, uint16_t))
{
    float current_lsb = max_a / 32768.0f;
    uint16_t cal = (uint16_t)(0.04096f / (current_lsb * INA219_SHUNT_UOHM / 1e6f));
    return i2c_write(INA219_ADDR, 0x05u, cal) == 0; /* cal register */
}

float ina219_read_bus_v(int16_t raw)   /* bus voltage register (4 mV/bit) */
{
    float v = raw * 0.004f;
    return (v > INA219_BUS_MAX_V) ? -1.0f : v; /* 26 V aşımı: hata */
}
```

### §3.6 C++ — DC offset koruma rölesi (>0.5 V aç)

```cpp
// SPEC ÖRNEĞİ — master §6 DC offset kapısı; repo dosyası değil
#include <cmath>
#include <deque>

class DcOffsetGuard {
    static constexpr float kTripV   = 0.5f;  // master §6: >0.5 V → röle aç
    static constexpr int   kWindow  = 480;   // 48 kHz'te 10 ms pencere
    std::deque<float> buf_;

public:
    // Her örnek çağrılır; true = röle AÇ (hoparlör koruması)
    bool process(float sample) {
        buf_.push_back(std::fabs(sample));
        if (buf_.size() > kWindow) buf_.pop_front();
        float peak = 0.f;
        for (float v : buf_) peak = (v > peak) ? v : peak;
        return peak > kTripV;   // DC eşiği aşılırsa röle aç
    }
};
```

### §3.7 C# — SNR hesaplama ve kapı (dB)

```csharp
// SPEC ÖRNEĞİ — master §6 SNR >100 dB; repo dosyası değil
namespace CoreMusic.Electronics.Spec;

public static class SnrGate
{
    public const double MinSnrDb = 100.0;   // şartname sabiti

    /// <summary>Sinyal gücünün gürültü gücüne oranı (dB).</summary>
    public static double CalculateDb(double signalRms, double noiseRms)
    {
        if (noiseRms <= 0) throw new ArgumentOutOfRangeException(nameof(noiseRms));
        return 20.0 * Math.Log10(signalRms / noiseRms);
    }

    public static bool Pass(double signalRms, double noiseRms)
        => CalculateDb(signalRms, noiseRms) >= MinSnrDb;
}
```

### §3.8 Go — boost converter PVDD telemetrisi

```go
// SPEC ÖRNEĞİ — PVDD Class D 18–53.5 V (KD-E06, datasheet kazanır)
package main

import "fmt"

// pvddValid: PVDD ölçülen gerilimi datasheet aralığında mı?
func pvddValid(volts float64) bool {
	const (
		minV = 18.0  // datasheet alt sınır (master §6/KD-E06)
		maxV = 53.5  // datasheet üst sınır
	)
	return volts >= minV && volts <= maxV
}

func main() {
	measured := []float64{12.0, 24.0, 55.0} // ölçüm örneği
	for _, v := range measured {
		fmt.Printf("PVDD=%.1fV geçerli=%v\n", v, pvddValid(v))
	}
	// 12.0=false (alt sınır altı) · 24.0=true · 55.0=false (aşım)
}
```

### §3.9 Rust — ripple ölçümü (10 mV kapısı)

```rust
// SPEC ÖRNEĞİ — master §6 ripple <10 mV; repo dosyası değil

/// PVDD rail üzerinde tepe-tepe ripple kontrolü (mV cinsinden).
pub fn ripple_ok(samples_mv: &[f64]) -> bool {
    let (min, max) = samples_mv.iter().fold(
        (f64::MAX, f64::MIN),
        |(lo, hi), &v| (lo.min(v), hi.max(v)),
    );
    let pp_mv = max - min;
    pp_mv < 10.0 // şartname: <10 mV tepe-tepe
}

#[cfg(test)]
mod tests {
    use super::ripple_ok;

    #[test]
    fn temiz_rail_gecer() {
        assert!(ripple_ok(&[24.01, 24.02, 24.00, 24.015]));
    }

    #[test]
    fn ripple_asimi_reddedilir() {
        assert!(!ripple_ok(&[23.95, 24.05, 24.12])); // 17 mV pp
    }
}
```

### §3.10 Java — UCC27524 UVLO kontrolü (gate driver)

```java
// SPEC ÖRNEĞİ — UCC27524 UVLO 4 V (web-arastirma §2.1 #3); repo dosyası değil
public final class UvloGuard {
    // datasheet: UVLO typ 4 V; VDD 4.5–18 V
    private static final double UVLO_MIN_V = 4.0;
    private static final double VDD_MAX_V  = 18.0;

    private UvloGuard() {}

    /** Gate driver sürümü ancak VDD UVLO üstünde ve 18 V altında güvenli. */
    public static boolean driverEnabled(double vddVolts) {
        if (Double.isNaN(vddVolts)) {
            throw new IllegalArgumentException("VDD ölçümü NaN (I2C kopuk?)");
        }
        return vddVolts > UVLO_MIN_V && vddVolts <= VDD_MAX_V;
    }
}
```

### §3.11 SQL — üretim test kaydı şeması (BCNF adayı)

```sql
-- SPEC ÖRNEĞİ — ADR-063 §2(f) fabrika testi veri modeli; repo şeması değil
-- BCNF: her aday anahtar belirleyicidir (data-engineer gözden geçirmesi gerekli)
CREATE TABLE production_test (
    serial_no      CHAR(12)      NOT NULL,
    test_station   VARCHAR(16)   NOT NULL,
    tested_at      DATETIME      NOT NULL,
    snr_db         DECIMAL(6,2)  NOT NULL,
    thd_1w_pct     DECIMAL(7,4)  NOT NULL,
    ripple_mv      DECIMAL(6,2)  NOT NULL,
    dc_offset_v    DECIMAL(4,3)  NOT NULL,
    result         ENUM('PASS','FAIL') NOT NULL,
    PRIMARY KEY (serial_no, test_station, tested_at), -- tekrarlı istasyon testi
    CONSTRAINT ck_snr  CHECK (snr_db >= 100.0),       -- master §6
    CONSTRAINT ck_thd  CHECK (thd_1w_pct < 0.0100),
    CONSTRAINT ck_rip  CHECK (ripple_mv < 10.0)
);
```

### §3.12 Bash — laboratuvar seri port ölçüm döngüsü

```bash
#!/usr/bin/env bash
# SPEC ÖRNEĞİ — AES17 ölçüm tezgâhı; repo dosyası değil
# Her kanal için ölç, kapıları uygula, sonucu CSV'ye yaz.
set -euo pipefail

PORT=${1:-/dev/ttyUSB0}
OUT="aes17_results.csv"
MIN_SNR=100      # master §6
MAX_THD_1W=0.01

echo "serial,channel,snr_db,thd_pct,result" > "$OUT"

for ch in $(seq 0 7); do                       # PCM3168A: 8 kanal
    read -r snr thd <<< "$(measure "$PORT" "$ch")"   # tezgâh komutu
    result=FAIL
    awk -v s="$snr" -v t="$thd" -v ms="$MIN_SNR" -v mt="$MAX_THD_1W" \
        'BEGIN { exit !(s >= ms && t < mt) }' && result=PASS
    echo "UNIT-0001,$ch,$snr,$thd,$result" >> "$OUT"
    echo "kanal $ch: SNR=${snr}dB THD=${thd}% -> $result"
done
```

### §3.13 Verilog — I2S alıcı çerçevesi (simülasyon)

```verilog
// SPEC ÖRNEĞİ (simülasyon) — I2S LRCLK/BCLK çerçeve; repo dosyası değil
// ⚠️ VERIFICATION REQUIRED: gerçek FPGA bitstreami yok; yalnız kavramsal RTL
`timescale 1ns/1ps
module i2s_frame_sync (
    input  wire        bclk,      // bit clock
    input  wire        lrclk,     // kanal seçim (fs)
    output reg         is_left,   // aktif kanal bayrağı
    output reg  [5:0]  bit_cnt    // 32 bit sayacı (32-bit float: ADR-017)
);
    always @(posedge bclk) begin
        if (bit_cnt == 6'd31) begin
            bit_cnt <= 6'd0;
            is_left <= ~is_left;   // her kare sonunda L/R geçişi
        end else begin
            bit_cnt <= bit_cnt + 6'd1;
        end
    end

    initial begin
        is_left = 1'b1;            // ilk kare sol kanal
        bit_cnt = 6'd0;
    end
endmodule
```

## §4 Blok → Bileşen Eşlemesi

| # | Blok | Bileşen/kural | Doğrulama |
|---|---|---|---|
| 1 | PHP | AES17 fabrika kapısı | ADR-063 §2(f) |
| 2 | JS | I2S BCLK (12.288 MHz) | master §4.4 |
| 3 | TS | Termal 5-kademe | master §6.3 |
| 4 | Python | THD+N FFT | master §6 |
| 5 | C | INA219 I2C | web-arastirma §2.1 #2 |
| 6 | C++ | DC offset röle | master §6 |
| 7 | C# | SNR dB | master §6 |
| 8 | Go | PVDD 18–53.5 V | KD-E06 |
| 9 | Rust | ripple <10 mV | master §6 |
| 10 | Java | UCC27524 UVLO | web-arastirma §2.1 #3 |
| 11 | SQL | üretim test şeması | ADR-063 §2(f) |
| 12 | Bash | ölçüm tezgâhı döngüsü | ADR-063 §2(f) |
| 13 | Verilog | I2S çerçeve (sim) | ADR-017 (32-bit) |

## §5 Çapraz Referanslar

| Hedef | İlişki |
|---|---|
| `electroncis-master.md` §6/§9/§14 | Eşik değerleri SSOT |
| `electroncis-web-arastirma.md` §2 | Bileşen URL kanıtları (INA219, UCC27524, TPS54202) |
| `K005-firmware/firmware-kod-ornekleri.md` | Gerçek firmware blokları (C/C++/Rust) bu dosyayla birleştirilir |
| `K004-dsp/dsp-kod-ornekleri.md` | DSP çekirdek blokları (Python/C++) |
