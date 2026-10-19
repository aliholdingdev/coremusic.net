---
type: technical-spec
project: Neva Engine
component: ai-auto-eq
updated: 2026-06-03
refs:
  - FX Sound: AI-enhanced presets
  - Sonarworks SoundID: Room calibration
  - ISO 226:2003 — Equal-loudness contours
---

# Neva Engine — AI Auto-EQ System

> 3-tier hybrid: Genre Detection (rule-based) → ML Fine-Tuning → Environmental Calibration

---

## 1. Tier 1: Genre Detection & Preset Loading

### Genre Detection via FFT

```cpp
enum class Genre {
    Arabesk, Pop, Rock, Electronic, Jazz, Classical,
    HipHop, Metal, Folk, Latin, RNB, Ambient, Unknown
};

Genre detectGenre(const float* spectrum, int bins, float sampleRate) {
    // Spectral centroid → brightness
    float centroid = computeSpectralCentroid(spectrum, bins);

    // Bass energy ratio (20-250Hz / total)
    float bassRatio = energyInRange(spectrum, 20, 250, sampleRate)
                    / energyInRange(spectrum, 20, 20000, sampleRate);

    // Mid energy (500Hz-2kHz)
    float midRatio = energyInRange(spectrum, 500, 2000, sampleRate)
                   / energyInRange(spectrum, 20, 20000, sampleRate);

    // High energy (8kHz-16kHz)
    float highRatio = energyInRange(spectrum, 8000, 16000, sampleRate)
                    / energyInRange(spectrum, 20, 20000, sampleRate);

    // Tempo / BPM detection (onset detection)
    float bpm = detectBPM(spectrum, sampleRate);

    // Rhythm regularity (electronic vs live)
    float regularity = computeRhythmRegularity(spectrum);

    // Decision tree
    if (bassRatio > 0.4f && bpm < 100 && midRatio > 0.3f)   return Genre::Arabesk;
    if (bassRatio > 0.35f && bpm > 120 && highRatio > 0.15f) return Genre::Electronic;
    if (midRatio > 0.35f && regularity < 0.3f)                return Genre::Classical;
    if (bpm > 120 && highRatio > 0.1f && bassRatio > 0.3f)   return Genre::Pop;
    if (midRatio < 0.25f && bassRatio > 0.3f && highRatio > 0.1f) return Genre::Rock;
    if (bpm > 80 && bpm < 110 && regularity > 0.5f)           return Genre::HipHop;

    return Genre::Unknown;
}
```

---

## 2. Genre-Based EQ Presets

| Genre | Bass (60Hz) | Low-Mid (250Hz) | Mid (1kHz) | High-Mid (4kHz) | High (12kHz) | Curve |
|-------|------------|-----------------|-----------|-----------------|-------------|-------|
| **Arabesk** | +3dB | +2dB | +1dB | +3dB (vocal) | +1dB | Warm vocal |
| **Pop** | +4dB | 0dB | +1dB | +2dB | +3dB | Modern bright |
| **Rock** | +3dB | -1dB | -2dB (scoop) | +3dB | +2dB | Mid scoop |
| **Electronic/Dans** | +6dB | +1dB | 0dB | +1dB | +4dB | Bass heavy |
| **Jazz** | +2dB | +1dB | 0dB | -1dB | -2dB | Warm mellow |
| **Classical** | 0dB | 0dB | 0dB | 0dB | -1dB | Flat natural |
| **HipHop** | +5dB | +1dB | +1dB | +2dB | +2dB | Bass boosted |
| **Metal** | +2dB | 0dB | -3dB | +4dB | +3dB | Aggressive |
| **Folk** | +1dB | +2dB | +1dB | 0dB | 0dB | Natural warm |
| **R&B** | +4dB | +2dB | +1dB | +1dB | +2dB | Smooth bass |
| **Gamer FPS** | -2dB | +1dB | +3dB (footsteps) | +4dB | +2dB | Spatial clarity |
| **Gamer RPG** | +3dB | +2dB | +1dB | +2dB | +3dB | Cinematic |
| **Film/Movie** | +4dB | +2dB | +1dB | +2dB | +1dB | Theater |
| **Voice/Podcast** | -3dB | 0dB | +3dB (clarity) | +2dB | -1dB | Voice focus |

---

## 3. Tier 2: ML Fine-Tuning

### Kullanıcı Tercih Modeli

```json
{
  "user_id": 123,
  "listening_history": [
    {"song_id": 1, "genre": "arabesk", "eq_profile": "warm_vocal", "skip": false},
    {"song_id": 2, "genre": "pop", "eq_profile": "modern_bright", "skip": true}
  ],
  "preferred_curve": {
    "60Hz":  2.8,  // 3.0 preset'ten -0.2 sapma
    "250Hz": 2.5,  // 2.0 preset'ten +0.5 sapma
    "1kHz":  0.5,  // 1.0 preset'ten -0.5 sapma
    "4kHz":  3.5,  // 3.0 preset'ten +0.5 sapma
    "12kHz": 1.0   // 1.0 preset ile aynı
  },
  "skip_patterns": {
    "too_bright": 3,   // 3 kez bright şarkı skip
    "too_muddy": 2     // 2 kez muddy şarkı skip
  }
}
```

### Online Learning

```
Her dinleme seansında:
  1. Skip tespit → EQ profilini not al
  2. Şarkı sonuna kadar dinleme → EQ profilini güçlendir
  3. Volume değişikliği → loudness preference
  4. Manuel EQ override → model güncelle (high weight)

Model: Incremental gradient descent with forgetting factor (α = 0.95)
```

---

## 4. Tier 3: Environmental Calibration

### Room Correction

```cpp
// Mikrofon ile pink noise oynat → oda yanıtını ölç → inverse filter
void calibrateRoom() {
    // 1. Pink noise generate (20Hz - 20kHz)
    // 2. Play through speakers
    // 3. Record with microphone
    // 4. FFT → frequency response
    // 5. Inverse filter = target_flat_response - measured_response
    // 6. Apply inverse filter as room correction EQ

    for (int i = 0; i < BINS; ++i) {
        float correction_dB = targetResponse[i] - measuredResponse[i];
        correction_dB = std::clamp(correction_dB, -12.0f, 12.0f);
        roomCorrectionEQ.setBand(i, correction_dB);
    }
}
```

### Loudness Compensation (ISO 226:2003)

```cpp
// Düşük ses seviyesinde bas ve tiz boost
// Referans: Fletcher-Munson / ISO 226 equal-loudness contours

float loudnessCompensation(float frequency, float volume_dB) {
    // 80 phon = reference (flat)
    // < 80 phon → bass + treble boost
    // > 80 phon → slight reduction

    float phon = volume_dB; // approximate
    float compensation = 0.0f;

    if (frequency < 200.0f) {
        // Bass boost at low volumes
        compensation = (80.0f - phon) * 0.15f; // 6dB max at 60 phon
    } else if (frequency > 8000.0f) {
        // Treble boost at low volumes
        compensation = (80.0f - phon) * 0.08f; // 3dB max
    }

    return std::clamp(compensation, -6.0f, 6.0f);
}
```

### Time-Based Adaptation

```cpp
// Günün saatine göre adaptif EQ
void applyTimeAdaptation(int hour) {
    if (hour >= 22 || hour < 6) {
        // Gece modu: bass reduction, slight high cut (komşu rahatsız etme)
        eq.setBandBass(-3.0f);
        eq.setBandHigh(-2.0f);
    } else if (hour >= 6 && hour < 10) {
        // Sabah modu: light, natural
        eq.setBandBass(0.0f);
        eq.setBandHigh(1.0f);
    } else if (hour >= 10 && hour < 18) {
        // Gündüz: normal
        eq.setBandBass(1.0f);
        eq.setBandHigh(1.0f);
    } else {
        // Akşam: energetic
        eq.setBandBass(3.0f);
        eq.setBandHigh(2.0f);
    }
}
```

---

## 5. AI Auto-EQ Workflow

```
1. Ses başlar → FFT analizi → genre tespiti
2. Genre-based preset yükle (rule-based)
3. Kullanıcı geçmişine göre fine-tune (ML model)
4. Oda kalibrasyonu uygula (environmental)
5. Ses seviyesine göre loudness compensate
6. Saate göre time-based adapt
7. Kullanıcı manuel override → model güncelle
8. Sürekli öğrenme döngüsü
```

---

*Neva Engine AI Auto-EQ v1 — 3-Tier Hybrid · 2026-06-03*
