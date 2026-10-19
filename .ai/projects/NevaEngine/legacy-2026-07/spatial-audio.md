---
type: technical-spec
project: Neva Engine
module: spatial-audio
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — Spatial Audio / 3D Ses Sistemi

## 1. Genel Bakış

Neva Engine spatial audio modülü, binaural rendering (HRTF), oda simülasyonu,
Dolby Atmos downmix, Ambisonics ve çok kanallı surround formatları destekleyen
kapsamlı bir 3D ses işleme sistemidir.

### Tasarım Hedefleri

| Hedef | Açıklama |
|-------|----------|
| Kulaklıkta 3D ses | HRTF ile sanal surround |
| Oda simülasyonu | Gerçekçi mekan akustiği |
| Dolby Atmos | Object-based spatial ses |
| Ambisonics | Full-sphere ses alanı |
| Düşük latency | < 10 ms (binaural + room) |
| Head tracking | Gerçek zamanlı baş pozisyonu |
| Özelleştirme | Kişisel HRTF desteği |

---

## 2. Binaural Rendering (HRTF)

### 2.1 HRTF Nedir?

Head-Related Transfer Function (HRTF), sesin kaynaktan kulak zarına ulaşana
kadar baş, kulak kepçesi, omuzlar ve gövde tarafından nasıl değiştirildiğini
modelleyen bir transfer fonksiyonudur.

```
Ses Kaynağı (x, y, z)
    │
    ▼
┌─────────────────────────────────────────┐
│  HRTF Filtre Çifti                       │
│                                          │
│  ┌──────────────┐  ┌──────────────┐     │
│  │ Sol Kulak    │  │ Sağ Kulak    │     │
│  │ HRIR (512-tap│  │ HRIR (512-tap│     │
│  │  FIR filtre) │  │  FIR filtre) │     │
│  └──────┬───────┘  └──────┬───────┘     │
│         │                 │              │
│         ▼                 ▼              │
│    ┌─────────┐      ┌─────────┐         │
│    │ Sol Çıkış│     │ Sağ Çıkış│        │
│    └─────────┘      └─────────┘         │
└─────────────────────────────────────────┘
```

### 2.2 HRTF Datasetleri

| Dataset | Kaynak | Nokta Sayısı | Çözünürlük | Açıklama |
|---------|--------|-------------|-----------|----------|
| MIT KEMAR | MIT Media Lab | 710 | 5° azimuth, 10° elev. | KEMAR manken |
| CIPIC | UC Davis | 1250 | ~5° | 45 denek |
| SADIE II | Uni of York | 2204 | 3°-5° | KEMAR + 20 denek |
| LISTEN | IRCAM | 187 | 15° | Oda akustiği ile |
| ARI | ARI Austria | 1550 | ~2.5° | 170+ denek |
| HUTUBS | TU Berlin | 440 | ~9° | 96 denek, antropometri ile |

### 2.3 HRTF Veri Formatı

```cpp
struct HRTFData {
    int numDirections;                      // Toplam yön sayısı
    int irLength;                           // Impulse response uzunluğu (sample)
    std::vector<float> azimuths;            // Azimuth açıları (derece)
    std::vector<float> elevations;          // Elevation açıları (derece)
    std::vector<float> distances;           // Mesafeler (metre, opsiyonel)
    std::vector<float> leftIRs;             // Sol kulak IR verisi (flattened)
    std::vector<float> rightIRs;            // Sağ kulak IR verisi (flattened)
    float sampleRate;                       // Dataset'in sample rate'i

    // En yakın HRIR'i interpolasyon ile bul
    void getHRIR(float azimuth, float elevation,
                 float* leftIR, float* rightIR) const;
};
```

### 2.4 HRTF İnterpolasyonu

```
Ses kaynağı pozisyonu (az=37°, el=-12°)
    │
    ├── Yakın nokta 1: (az=35°, el=-10°) → HRIR_1L, HRIR_1R
    ├── Yakın nokta 2: (az=35°, el=-15°) → HRIR_2L, HRIR_2R
    ├── Yakın nokta 3: (az=40°, el=-10°) → HRIR_3L, HRIR_3R
    └── Yakın nokta 4: (az=40°, el=-15°) → HRIR_4L, HRIR_4R

    ↓ Bilinear interpolasyon

    HRIR_L = w1·HRIR_1L + w2·HRIR_2L + w3·HRIR_3L + w4·HRIR_4L
    HRIR_R = w1·HRIR_1R + w2·HRIR_2R + w3·HRIR_3R + w4·HRIR_4R
```

---

## 3. Head Tracking

### 3.1 Baş Takip Sistemi

| Platform | API | Güncelleme Hızı | Latency |
|----------|-----|----------------|---------|
| Windows | Windows Spatial Audio API | 50-120 Hz | < 20 ms |
| macOS | CoreMotion (AirPods) | 50-100 Hz | < 10 ms |
| IMU (harici) | OSC/UDP | 100-250 Hz | < 5 ms |
| Web kamera | OpenCV yüz takibi | 30 Hz | < 50 ms |

### 3.2 Head Tracking Veri Formatı

```cpp
struct HeadPose {
    float yaw;      // Baş dönüşü (sol-sağ, derece)
    float pitch;    // Baş eğimi (yukarı-aşağı, derece)
    float roll;     // Baş yatışı (derece)
    float x, y, z;  // Baş pozisyonu (metre)
    uint64_t timestamp;
};

// Head tracking callback
using HeadTrackingCallback = std::function<void(const HeadPose&)>;
```

### 3.3 Head Tracking ile Ses Güncelleme

```
Head rotation (yaw: +15°)
    │
    ▼
Ses sahnesi döndürme (-15°)
    │
    ▼
Ses kaynağı sanal pozisyonu güncelle
    │
    ▼
Yeni HRIR seçimi + interpolasyon
    │
    ▼
Crossfade (10 ms) ile yumuşak geçiş
```

---

## 4. Personalized HRTF

### 4.1 Kişiselleştirme Yöntemleri

| Yöntem | Doğruluk | Gereksinim |
|--------|----------|-----------|
| Antropometrik ölçüm | Yüksek | Kulak/kafa ölçüleri (fotoğraf) |
| 3D tarama | En yüksek | LiDAR/Photogrammetry |
| Dinleme testi | Orta | Kullanıcı feedback |
| Numerical modelleme | Yüksek | BEM/FEM hesaplama |
| Makine öğrenmesi | Orta-Yüksek | Fotoğraftan tahmin |

### 4.2 Antropometrik Parametreler

| Parametre | Açıklama | Tipik Değer (cm) |
|-----------|----------|-----------------|
| head_width | Kafa genişliği | 14-17 |
| head_height | Kafa yüksekliği | 19-24 |
| head_depth | Kafa derinliği | 18-22 |
| pinna_height | Kulak kepçesi yüksekliği | 5.5-7.0 |
| pinna_width | Kulak kepçesi genişliği | 3.0-4.5 |
| pinna_rotation | Kulak kepçesi dönüş açısı | 10°-30° |
| concha_depth | Concha derinliği | 1.0-2.0 |
| shoulder_width | Omuz genişliği | 38-50 |

### 4.3 Kişisel HRTF API

```json
// Antropometrik veri gönder
{"method": "spatial.setAnthropometry", "params": {
    "headWidth": 15.2,
    "headHeight": 22.1,
    "headDepth": 19.5,
    "pinnaHeight": 6.3,
    "pinnaWidth": 3.8
}, "id": 1}

// Kişisel HRTF hesaplama başlat (arka planda)
{"method": "spatial.generatePersonalHRTF", "params": {
    "method": "numerical",
    "modelResolution": "high"
}, "id": 2}

// HRTF ilerleme durumu
// Yanıt: {"result": {"progress": 65, "eta": 45, "status": "computing"}}
```

---

## 5. Oda Simülasyonu

### 5.1 Akustik Model

```
Doğrudan Ses (Direct Path)
    │
    ├── Erken Yansımalar (Early Reflections, 0-80 ms)
    │   ├── 1. yansıma: zemin
    │   ├── 2. yansıma: tavan
    │   ├── 3. yansıma: sol duvar
    │   └── ... (image-source model)
    │
    └── Geç Yankılanma (Late Reverb, 80 ms+)
        └── FDN (Feedback Delay Network)
```

### 5.2 Image-Source Model (Erken Yansımalar)

```cpp
class ImageSourceModel {
public:
    struct Room {
        float width, height, depth;     // metre
        float absorption[6];            // 6 yüzey (sol, sağ, ön, arka, tavan, zemin)
    };

    struct Reflection {
        float delay;                    // saniye
        float gain;                     // doğrusal
        float azimuth, elevation;       // geliş açısı
        int order;                      // yansıma derecesi
    };

    std::vector<Reflection> computeEarlyReflections(
        const Room& room,
        const Vec3& sourcePos,
        const Vec3& listenerPos,
        int maxOrder = 3
    );
};
```

### 5.3 FDN (Feedback Delay Network — Geç Yankılanma)

```cpp
class FeedbackDelayNetwork {
    static constexpr int NUM_DELAY_LINES = 16;
    static constexpr int NUM_ABSORPTION_BANDS = 6;

    struct DelayLine {
        std::vector<float> buffer;
        size_t writePtr;
        float length;                   // saniye olarak
    };

    std::array<DelayLine, NUM_DELAY_LINES> delayLines;
    juce::dsp::Matrix<float> feedbackMatrix;    // Hadamard veya Householder

public:
    void setRT60(float rt60, int frequencyBand);
    void setRoomDimensions(float w, float h, float d);
    float processSample(float input);
};
```

### 5.4 RT60 Hesaplama (Sabine ve Eyring Formülleri)

```
Sabine Formülü:
RT60 = 0.161 × V / (S × α_avg)

Eyring Formülü:
RT60 = 0.161 × V / (-S × ln(1 - α_avg))

V = oda hacmi (m³)
S = toplam yüzey alanı (m²)
α_avg = ortalama soğurma katsayısı
```

### 5.5 Malzeme Soğurma Katsayıları (α)

| Malzeme | 125 Hz | 250 Hz | 500 Hz | 1 kHz | 2 kHz | 4 kHz |
|---------|--------|--------|--------|-------|-------|-------|
| Beton (boyasız) | 0.01 | 0.01 | 0.02 | 0.02 | 0.02 | 0.03 |
| Ahşap panel | 0.30 | 0.25 | 0.20 | 0.17 | 0.15 | 0.10 |
| Halı (ağır) | 0.08 | 0.24 | 0.57 | 0.69 | 0.71 | 0.73 |
| Cam pencere | 0.35 | 0.25 | 0.18 | 0.12 | 0.07 | 0.04 |
| Alçıpan | 0.29 | 0.10 | 0.05 | 0.04 | 0.07 | 0.09 |
| Perde (ağır) | 0.14 | 0.35 | 0.55 | 0.72 | 0.70 | 0.65 |
| Mermer | 0.01 | 0.01 | 0.01 | 0.01 | 0.02 | 0.02 |
| Akustik sünger | 0.25 | 0.50 | 0.85 | 0.95 | 0.90 | 0.85 |

---

## 6. Oda Presetleri

### 6.1 Hazır Presetler

| Preset | Boyut (m) | RT60 | Karakteristik |
|--------|-----------|------|--------------|
| Konser Salonu | 30×20×15 | 1.8-2.2s | Geniş, sıcak |
| Düğün Salonu | 20×15×6 | 1.8-2.5s | Canlı, yankılı |
| Arena | 60×40×25 | 3.0-5.0s | Devasa, uzun yankı |
| Kilise | 40×20×18 | 3.5-6.0s | Katedral reverb |
| Stüdyo | 6×4×3 | 0.2-0.4s | Kuru, kontrollü |
| Small Room | 4×3×2.5 | 0.1-0.3s | Yakın, samimi |
| Dış Mekan | ∞ | 0.0s | Sadece direkt ses |
| Hall | 15×10×8 | 1.0-1.5s | Orta salon |
| Plate | — | 0.5-2.0s | Klasik plate reverb |
| Spring | — | 1.0-3.0s | Vintage spring reverb |

### 6.2 Düğün Salonu Detaylı Modeli

```json
{
  "room": {
    "name": "Düğün Salonu (Standart)",
    "dimensions": {"width": 20.0, "height": 6.0, "depth": 15.0},
    "volume": 1800.0,
    "surfaceArea": 780.0,
    "materials": {
      "walls": "alcipan",
      "floor": "mermer",
      "ceiling": "alcipan",
      "stage": "ahşap"
    },
    "rt60Target": {
      "125Hz": 2.2,
      "250Hz": 2.0,
      "500Hz": 1.9,
      "1000Hz": 1.8,
      "2000Hz": 1.7,
      "4000Hz": 1.6
    },
    "earlyReflections": {
      "maxOrder": 3,
      "enabled": true
    },
    "lateReverb": {
      "fdnLines": 16,
      "enabled": true
    }
  }
}
```

### 6.3 Özel Oda Oluşturma API

```json
{"method": "spatial.createCustomRoom", "params": {
    "name": "Benim Stüdyom",
    "dimensions": {"width": 5.5, "height": 2.8, "depth": 4.2},
    "materials": {
        "walls": "acoustic_foam",
        "floor": "carpet_heavy",
        "ceiling": "drywall"
    },
    "listenerPosition": {"x": 2.5, "y": 1.2, "z": 2.0}
}, "id": 1}
```

---

## 7. Dolby Atmos Entegrasyonu

### 7.1 Windows Spatial Audio API

Windows 10/11 Spatial Audio API, Dolby Atmos for Headphones ve Windows Sonic
için sistem seviyesinde spatial ses desteği sağlar.

```cpp
class DolbyAtmosRenderer {
    ISpatialAudioClient* spatialClient = nullptr;
    ISpatialAudioObjectRenderStream* renderStream = nullptr;

public:
    bool initialize(int sampleRate, int maxObjects = 128);
    void updateObject(int objectId, float x, float y, float z);
    void setObjectVolume(int objectId, float volume);
    void processFrame();
    void destroy();
};
```

### 7.2 Object-Based Audio

| Özellik | Destek |
|---------|--------|
| Maksimum obje | 128 (Dolby Atmos Home), 118 (Dolby Atmos Cinema) |
| Obje pozisyonu | X, Y, Z (normalize -1 ... 1) |
| Obje boyutu | Spread (0-1) |
| Obje önceliği | Evet (encoder optimizasyonu) |
| Binaural downmix | 128 obje → 2 kanal (HRTF) |
| Metadata | Dolby Atmos XML |

### 7.3 Atmos Binaural Downmix

```
128 Obje (x, y, z + ses sinyali)
    │
    ▼
┌──────────────────────────────┐
│ Dolby Atmos Renderer         │
│                              │
│ Her obje için:               │
│  1. Pozisyon → HRIR seçimi  │
│  2. Mesafe → gain/zayıflama │
│  3. Spread → HRIR genişletme│
│  4. HRTF konvolüsyonu        │
│                              │
│ Toplam: 128 × 2 HRTF = 256  │
│         konvolüsyon          │
└──────────────┬───────────────┘
               │
               ▼
        2 Kanal Binaural (Sol + Sağ)
```

---

## 8. Ambisonics

### 8.1 Ambisonics Order'ları

| Order | Kanal Sayısı | Uzamsal Çözünürlük | Kullanım |
|-------|-------------|-------------------|----------|
| 1st (FOA) | 4 (W,X,Y,Z) | Düşük | YouTube 360, temel VR |
| 2nd (SOA) | 9 | Orta | VR oyunları |
| 3rd (TOA) | 16 | Yüksek | Profesyonel VR, sinema |
| 4th | 25 | Çok yüksek | Araştırma |
| 5th | 36 | En yüksek | Araştırma |

### 8.2 Ambisonics İşlemleri

```cpp
class AmbisonicProcessor {
public:
    // A-format → B-format dönüşümü
    void a2b(const float* aInput, float* bOutput, int order);

    // B-format → binaural dönüşüm (sanal hoparlör + HRTF)
    void b2binaural(const float* bInput, float* stereoOutput, int order);

    // Ses alanı döndürme
    void rotate(float yaw, float pitch, float roll);

    // Ses alanı yansıtma (mirror)
    void mirror(bool x, bool y, bool z);

    // Dominance / zoom efekti
    void dominance(float factor);

    // Order yükseltme/düşürme
    void convertOrder(const float* input, int inOrder,
                      float* output, int outOrder);
};
```

### 8.3 Ambisonics Kanal Haritası (ACN/SN3D)

| ACN | Kanal | 1st Order | 2nd Order | 3rd Order |
|-----|-------|-----------|-----------|-----------|
| 0 | W | ✓ | ✓ | ✓ |
| 1 | Y | ✓ | ✓ | ✓ |
| 2 | Z | ✓ | ✓ | ✓ |
| 3 | X | ✓ | ✓ | ✓ |
| 4 | V | — | ✓ | ✓ |
| 5 | T | — | ✓ | ✓ |
| 6 | R | — | ✓ | ✓ |
| 7 | S | — | ✓ | ✓ |
| 8 | U | — | ✓ | ✓ |
| 9+ | — | — | — | ✓ |

---

## 9. Çok Kanallı Format Desteği

### 9.1 Desteklenen Formatlar

| Format | Kanal Düzeni | Hoparlör |
|--------|-------------|----------|
| Mono | 1.0 | C |
| Stereo | 2.0 | L, R |
| Stereo (geniş) | 2.1 | L, R, LFE |
| 5.1 Surround | 6 | L, R, C, LFE, Ls, Rs |
| 7.1 Surround | 8 | L, R, C, LFE, Ls, Rs, Lb, Rb |
| 9.1.4 (Dolby Atmos) | 14 | 7.1 + 4 overhead |
| Custom | 1-64 | Kullanıcı tanımlı |

### 9.2 Surround → Binaural Downmix

```
5.1 / 7.1 / 9.1.4 Kaynak
    │
    ▼
┌──────────────────────────────┐
│ Sanal Hoparlör Dizilimi      │
│                              │
│ Her hoparlör → sanal pozisyon│
│   · HRIR seçimi              │
│   · Mesafe telafisi          │
│   · Gecikme (ITD)            │
│   · Seviye (ILD)             │
│   · Konvolüsyon              │
│                              │
│ Tüm kanallar toplanır        │
└──────────────┬───────────────┘
               │
               ▼
        2 Kanal Binaural
```

---

## 10. Spatial Ses API

### 10.1 WebSocket JSON-RPC

```json
// Oda seçimi
{"method": "spatial.setRoom", "params": {
    "preset": "wedding_hall",
    "rt60Scale": 1.0,
    "earlyReflectionsEnabled": true,
    "lateReverbEnabled": true,
    "wetDryMix": 0.5
}, "id": 1}

// Ses kaynağı pozisyonu
{"method": "spatial.setSourcePosition", "params": {
    "sourceId": "vocals",
    "x": 2.0, "y": 1.5, "z": -3.0,
    "spread": 0.3
}, "id": 2}

// Dinleyici pozisyonu
{"method": "spatial.setListenerPosition", "params": {
    "x": 5.0, "y": 1.65, "z": 0.0,
    "yaw": 15.0, "pitch": 0.0, "roll": 0.0
}, "id": 3}

// Head tracking aç/kapat
{"method": "spatial.setHeadTracking", "params": {"enabled": true}, "id": 4}

// Ambisonics giriş ayarla
{"method": "spatial.setAmbisonicsInput", "params": {
    "order": 3,
    "normalization": "sn3d",
    "channelOrder": "acn"
}, "id": 5}

// HRTF dataset değiştir
{"method": "spatial.setHRTFDataset", "params": {
    "dataset": "mit_kemar",
    "sampleRate": 48000
}, "id": 6}

// Spatial durum sorgulama
{"method": "spatial.getStatus", "params": {}, "id": 7}
```

### 10.2 Mesafe Modelleme

```
Ses kaynağı → dinleyici mesafesi (r)

Zayıflama:
gain(r) = 1 / (1 + r/reference)²

Hava soğurması:
α(f) = exp(-α₀ · r · (f/1000)²)
α₀ = 0.01 (standart hava)

Doppler efekti (hareketli kaynak):
f' = f · (c ± vr) / (c ± vs)

c = ses hızı (343 m/s @ 20°C)
vr = dinleyici hızı
vs = kaynak hızı
```

---

## 11. Yapılandırma

```json
{
  "spatial": {
    "enabled": true,
    "defaultHRTF": "mit_kemar",
    "headTracking": {
      "enabled": false,
      "source": "auto",
      "smoothingMs": 20
    },
    "room": {
      "defaultPreset": "studio",
      "maxReflectionOrder": 3,
      "fdnLines": 16
    },
    "dolbyAtmos": {
      "enabled": false,
      "maxObjects": 128,
      "outputMode": "binaural"
    },
    "ambisonics": {
      "maxOrder": 3,
      "normalization": "sn3d",
      "channelOrder": "acn"
    },
    "performance": {
      "maxHRTFConvolutions": 256,
      "cpuBudgetPercent": 25
    }
  }
}
```

---

## 12. Performans Analizi

| İşlem | CPU (Ryzen 7) | RAM |
|-------|---------------|-----|
| 1 HRTF (512-tap FIR) | 0.05% | 4 KB |
| 128 HRTF (Atmos) | 6.4% | 512 KB |
| Early reflections (3rd order) | 1.2% | 256 KB |
| FDN16 reverb | 2.0% | 128 KB |
| Ambisonics 3rd → binaural | 3.5% | 32 KB |
| Toplam (full spatial) | < 15% | < 5 MB |

---

## 13. Gelecek Genişletmeler

| Özellik | Açıklama |
|---------|----------|
| Gerçek IR konvolüsyonu | Gerçek mekan impulse response'ları |
| Wave-based modelleme | FDTD/BEM ile hassas simülasyon |
| MPEG-H Audio | Next-gen broadcast audio |
| 6DoF audio | Tam 6 serbestlik dereceli ses |
| ML tabanlı oda modeli | Fotoğraftan oda akustiği tahmini |
| Object-based reverb | Her objeye bağımsız reverb |
| Auralization | Mimari akustik simülasyon |

---

*Neva Engine v1.0.0 — Spatial Audio Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
