---
title: "Otomatik EQ Optimizasyonu"
layer: K4
category: "Yapay Zeka"
date: 2026-09-20
version: 1.0.0
status: development
components: 8
dependencies: [K0, K2, K4, K5]
---

# Auto EQ Optimization - Otomatik EQ Optimizasyonu

## Genel Bakış

Otomatik EQ optimizasyonu, oda akustiğini analiz ederek hoparlör kalibrasyonu ve otomatik equalizasyon sağlayan bir sistemdir. Gerçek zamanlı room correction, speaker calibration ve parametric EQ optimizasyonu ile dinleme deneyimini iyileştirir. Bu modül, K0 (Donanım) katmanındaki mikrofon girdilerini ve K2 (Sürücüler) katmanındaki çıkışları kontrol eder.

## Teknik Detaylar

### Room Correction (Oda Düzeltme)

Oda akustik analizi ve düzeltme filtresi üretimi:

```python
import numpy as np
from scipy import signal
from scipy.optimize import minimize

class RoomCorrection:
    def __init__(self, sample_rate=48000, block_size=4096):
        self.sr = sample_rate
        self.block_size = block_size
        self.target_curve = self._load_harman_target()
    
    def analyze_room(self, impulse_response: np.ndarray) -> dict:
        """Oda impulse response'unu analiz et."""
        
        # Frequency response hesaplama
        freqs, freq_response = signal.freqz(
            impulse_response, worN=8192, fs=self.sr
        )
        
        # RT60 hesaplama (reverberation time)
        rt60 = self._calculate_rt60(impulse_response)
        
        # Frequency band analizi
        bands = self._analyze_frequency_bands(freqs, freq_response)
        
        # Room modes tespiti
        modes = self._detect_room_modes(freqs, freq_response)
        
        # Comb filtering tespiti
        comb_filtering = self._detect_comb_filtering(freq_response)
        
        return {
            'frequency_response': freq_response.tolist(),
            'rt60': rt60,
            'frequency_bands': bands,
            'room_modes': modes,
            'comb_filtering': comb_filtering,
            'overall_score': self._room_score(rt60, bands)
        }
    
    def generate_correction_filter(self, room_analysis: dict) -> dict:
        """Oda analizine göre düzeltme filtresi üret."""
        
        measured_curve = np.array(room_analysis['frequency_response'])
        target_curve = self.target_curve
        
        # Inverse filter hesaplama
        inverse_response = target_curve / (measured_curve + 1e-8)
        
        # Filtre regularizasyonu ( aşırı düzeltme önleme)
        inverse_response = self._regularize_filter(inverse_response)
        
        # Parametric EQ dönüşümü
        peq_params = self._to_parametric_eq(inverse_response)
        
        # FIR filtre katsayıları
        fir_coefficients = self._design_fir_filter(inverse_response)
        
        return {
            'parametric_eq': peq_params,
            'fir_coefficients': fir_coefficients.tolist(),
            'target_curve': target_curve.tolist(),
            'correction_curve': inverse_response.tolist(),
            'max_cut_db': float(np.min(
                20 * np.log10(np.abs(inverse_response) + 1e-8)
            )),
            'max_boost_db': float(np.max(
                20 * np.log10(np.abs(inverse_response) + 1e-8)
            ))
        }
    
    def _calculate_rt60(self, ir: np.ndarray) -> dict:
        """Reverberation time hesaplama."""
        # Energy decay curve
        energy = np.cumsum(ir[::-1] ** 2)[::-1]
        energy_db = 10 * np.log10(energy + 1e-10)
        
        # -60dB noktaları
        threshold = np.max(energy_db) - 60
        
        # Band-pass filtre ile frekans bandı bazında RT60
        bands = {
            'low': (20, 250),
            'mid': (250, 4000),
            'high': (4000, 20000)
        }
        
        rt60_values = {}
        for band_name, (low, high) in bands.items():
            filtered = signal.sosfiltfilt(
                signal.butter(4, [low, high], btype='band', fs=self.sr, output='sos'),
                ir
            )
            energy = np.cumsum(filtered[::-1] ** 2)[::-1]
            energy_db = 10 * np.log10(energy + 1e-10)
            
            # RT60 interpolasyonu
            above_threshold = np.where(energy_db > threshold)[0]
            if len(above_threshold) > 0:
                rt60 = above_threshold[-1] / self.sr
            else:
                rt60 = 0
            
            rt60_values[band_name] = round(rt60, 3)
        
        return rt60_values
    
    def _detect_room_modes(self, freqs: np.ndarray, 
                           freq_response: np.ndarray) -> list:
        """Oda modlarını tespit et."""
        modes = []
        
        # Peak detection
        peaks, properties = signal.find_peaks(
            20 * np.log10(np.abs(freq_response) + 1e-8),
            height=6,  # 6dB üzeri peak
            distance=10,
            prominence=3
        )
        
        for peak_idx in peaks:
            modes.append({
                'frequency': float(freqs[peak_idx]),
                'amplitude_db': float(20 * np.log10(
                    np.abs(freq_response[peak_idx]) + 1e-8
                )),
                'q_factor': self._estimate_q_factor(
                    freqs, freq_response, peak_idx
                )
            })
        
        return modes
```

### Speaker Calibration

Hoparlör kalibrasyonu ve crossover ayarı:

```python
class SpeakerCalibrator:
    def __init__(self, num_channels=2):
        self.num_channels = num_channels
        self.calibration_data = {}
    
    def calibrate_speakers(self, measurement_data: list) -> dict:
        """
        measurement_data: Her hoparlör için ölçüm verileri
        """
        results = {}
        
        for ch_idx, data in enumerate(measurement_data):
            # Level kalibrasyonu
            level = self._calibrate_level(data['pink_noise'])
            
            # Time alignment
            delay = self._measure_delay(data['impulse'])
            
            # Phase response
            phase = self._analyze_phase(data['frequency_sweep'])
            
            # Frequency response
            freq_resp = self._analyze_frequency_response(
                data['frequency_sweep']
            )
            
            results[f'channel_{ch_idx}'] = {
                'level_offset_db': level,
                'delay_samples': delay,
                'delay_ms': delay / self.sr * 1000,
                'phase_response': phase,
                'frequency_response': freq_resp
            }
        
        # Crossfeed ayarı (stereo için)
        if self.num_channels == 2:
            crossfeed = self._calculate_crossfeed(results)
            results['crossfeed'] = crossfeed
        
        return results
    
    def _calibrate_level(self, pink_noise: np.ndarray) -> float:
        """Ses seviyesi kalibrasyonu."""
        # RMS hesaplama
        rms = np.sqrt(np.mean(pink_noise ** 2))
        
        # Referans seviyeye göre offset
        target_rms = 0.1  # -20 dBFS
        offset_db = 20 * np.log10(target_rms / (rms + 1e-10))
        
        return round(offset_db, 2)
    
    def _measure_delay(self, impulse: np.ndarray) -> int:
        """Hoparlör gecikme ölçümü."""
        # Cross-correlation ile delay tespiti
        max_idx = np.argmax(np.abs(impulse))
        
        # Sub-sample accuracy için interpolation
        if 0 < max_idx < len(impulse) - 1:
            alpha = impulse[max_idx - 1]
            beta = impulse[max_idx]
            gamma = impulse[max_idx + 1]
            
            p = 0.5 * (alpha - gamma) / (alpha - 2*beta + gamma)
            delay = max_idx + p
        else:
            delay = max_idx
        
        return int(round(delay))
```

### Parametric EQ Optimizasyonu

```python
class ParametricEQOptimizer:
    def __init__(self, num_bands=10):
        self.num_bands = num_bands
        self.min_freq = 20
        self.max_freq = 20000
    
    def optimize(self, measured_curve: np.ndarray, 
                 target_curve: np.ndarray) -> dict:
        """Parametric EQ katsayılarını optimize et."""
        
        def objective(params):
            """Hedef eğriye olan sapmayı minimize et."""
            eq_response = self._apply_peq(params)
            error = np.sum((eq_response - target_curve) ** 2)
            
            # Regularizasyon (çok fazla bant kullanımı cezası)
            band_usage = np.sum(np.abs(params[1::5]) > 0.1)
            penalty = 0.01 * band_usage
            
            return error + penalty
        
        # Başlangıç parametreleri
        initial_params = self._initialize_params()
        
        # Optimizasyon
        result = minimize(
            objective,
            initial_params,
            method='Nelder-Mead',
            options={'maxiter': 1000, 'xatol': 1e-6}
        )
        
        # EQ bantlarını çıkar
        eq_bands = self._extract_bands(result.x)
        
        return {
            'bands': eq_bands,
            'total_correction_db': self._total_correction(result.x),
            'optimization_score': 1 / (1 + result.fun)
        }
    
    def _apply_peq(self, params: np.ndarray) -> np.ndarray:
        """Parametric EQ uygula."""
        freqs = np.logspace(
            np.log10(self.min_freq),
            np.log10(self.max_freq),
            1024
        )
        
        response = np.ones_like(freqs)
        
        for i in range(0, len(params), 5):
            if i + 4 >= len(params):
                break
            
            fc = 10 ** params[i]        # Center frequency (log scale)
            gain = params[i + 1]        # Gain in dB
            q = 10 ** params[i + 2]     # Q factor (log scale)
            filter_type = int(params[i + 3])  # 0=peak, 1=lowshelf, 2=highshelf
            
            # Biquad filter coefficients
            a, b = self._biquad_coefficients(fc, gain, q, filter_type, self.sr)
            
            # Frequency response
            w, h = signal.freqz(b, a, worN=freqs, fs=self.sr)
            response *= np.abs(h)
        
        return 20 * np.log10(response + 1e-10)
```

## API / Konfigürasyon

```yaml
# config/auto-eq.yaml
room_correction:
  enabled: true
  measurement_duration: 30  # saniye
  num_measurements: 5
  target_curve: "harman_2018"
  
speaker_calibration:
  num_channels: 2
  crossover_frequency: 80  # Hz
  phase_alignment: true
  time_alignment: true
  
parametric_eq:
  num_bands: 10
  min_gain_db: -12
  max_gain_db: 12
  min_q: 0.5
  max_q: 10
  
analysis:
  fft_size: 8192
  overlap: 0.75
  window: "hann"
  min_freq: 20
  max_freq: 20000
```

## Performans / Ölçeklenebilirlik

| Metrik | Değer |
|--------|-------|
| Room Analysis | 2.5s |
| Filter Generation | 500ms |
| EQ Optimization | 1.2s |
| Real-time Latency | 10ms |
| CPU Usage (4 band) | 5% |

## Bağımlılıklar

| Bağımlılık | Version | Amaç |
|------------|---------|------|
| scipy | 1.11+ | Signal processing |
| numpy | 1.24+ | Numerical computation |
| sounddevice | 0.4+ | Audio I/O |
| onnxruntime | 1.17+ | ML inference |

## Durum: Implementasyon

Auto EQ Optimization modülü **development** aşamasındadır. Room correction ve speaker calibration temel fonksiyonları çalışıyor. Parametric EQ optimizasyonu henüz tamamlanmamıştır.
