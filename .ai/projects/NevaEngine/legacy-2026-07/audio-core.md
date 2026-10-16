---
type: technical-spec
project: Neva Engine
module: audio-core
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — Ses Çekirdeği (Audio Core)

## 1. Genel Bakış

Ses çekirdeği, Neva Engine'in en alt katmanıdır. İşletim sistemi ses API'leri
ile doğrudan iletişim kurar, ses buffer'larını yönetir ve üst katmanlara
düşük gecikmeli, yüksek kaliteli ses akışı sağlar.

### Tasarım Prensipleri

| Prensip | Uygulama |
|---------|----------|
| Lock-free | Audio callback içinde mutex yasak |
| Pre-allocation | Tüm buffer'lar başlangıçta ayrılır |
| No exception | Audio thread'de exception fırlatılmaz |
| SIMD | x86-64 (SSE4.1/AVX2) ve ARM64 (NEON) |
| Fail-safe | Ses kesintisinde graceful fallback |

---

## 2. JUCE::AudioDeviceManager Mimarisi

### 2.1 Cihaz Keşfi ve Seçimi

```cpp
class DeviceManager {
public:
    struct DeviceInfo {
        std::string name;
        std::string type;       // "ASIO", "WASAPI", "CoreAudio", "JACK", "ALSA"
        int maxInputChannels;
        int maxOutputChannels;
        std::vector<double> sampleRates;
        std::vector<int> bufferSizes;
        uint64_t latencyIn;     // mikrosaniye
        uint64_t latencyOut;
    };

    std::vector<DeviceInfo> scanDevices();
    bool selectDevice(const std::string& name);
    DeviceInfo currentDevice() const;

private:
    juce::AudioDeviceManager manager;
    juce::OwnedArray<juce::AudioIODeviceType> deviceTypes;
};
```

### 2.2 Cihaz Öncelik Sıralaması

| Öncelik | API | Platform | Özellikler |
|---------|-----|----------|------------|
| 1 | ASIO | Windows | En düşük latency, bit-perfect |
| 2 | WASAPI Exclusive | Windows | Bit-perfect, düşük latency |
| 3 | CoreAudio Exclusive | macOS | Bit-perfect, düşük latency |
| 4 | JACK | Linux | Profesyonel, düşük latency |
| 5 | WASAPI Shared | Windows | Kolay, yüksek latency |
| 6 | CoreAudio Shared | macOS | Kolay, yüksek latency |
| 7 | ALSA | Linux | Evrensel, orta latency |
| 8 | PulseAudio | Linux | Son çare fallback |

---

## 3. ASIO Entegrasyonu

### 3.1 ASIO Avantajları

- **Doğrudan donanım erişimi:** Windows ses katmanını (WASAPI, DirectSound) bypass eder
- **Bit-perfect:** Örnek değerleri değişmez, sample rate conversion yapılmaz
- **Düşük latency:** 16-4096 sample buffer, tipik 32-256 sample
- **Multi-client:** Aynı ASIO cihazı birden fazla uygulamadan erişilebilir
- **Zero-latency monitoring:** ASIO Direct Monitoring (ADM)

### 3.2 ASIO Konfigürasyonu

| Parametre | Minimum | Önerilen | Maksimum |
|-----------|---------|----------|----------|
| Buffer Size | 16 | 128 | 4096 |
| Sample Rate | 44100 | 48000 | 384000 |
| Input Channels | 0 | 2 | 64 |
| Output Channels | 1 | 2 | 64 |
| ASIO Clock Source | Internal | Internal | External (S/PDIF, ADAT) |

### 3.3 ASIO Buffer Formatları

| Format | Bit | Byte/Sample | Kullanım |
|--------|-----|-------------|----------|
| ASIOSTInt16LSB | 16 | 2 | Standart ses kartları |
| ASIOSTInt24LSB | 24 | 3 | Profesyonel arayüzler |
| ASIOSTInt32LSB | 32 | 4 | En yüksek çözünürlük |
| ASIOSTFloat32LSB | 32 | 4 | Float pipeline |
| ASIOSTFloat64LSB | 64 | 8 | Master bus |

### 3.4 ASIO Callback (Kritik Yol)

```cpp
void audioDeviceIOCallback(
    const float** inputChannelData,
    int numInputChannels,
    float** outputChannelData,
    int numOutputChannels,
    int numSamples
) {
    // 1. Input buffer'ı ring buffer'a yaz (lock-free)
    inputRingBuffer.write(inputChannelData, numInputChannels, numSamples);

    // 2. DSP zincirini çalıştır
    dspChain.process(inputRingBuffer, outputRingBuffer, numSamples);

    // 3. Output buffer'a yaz
    outputRingBuffer.read(outputChannelData, numOutputChannels, numSamples);

    // 4. Peak metre güncelle (atomic)
    for (int ch = 0; ch < numOutputChannels; ++ch) {
        float peak = findPeak(outputChannelData[ch], numSamples);
        meterPeaks[ch].store(peak, std::memory_order_release);
    }
}
```

---

## 4. WASAPI Entegrasyonu

### 4.1 WASAPI Exclusive Mode

- Uygulama ses cihazını tamamen kontrol eder
- Diğer uygulamalar ses çalamaz (münhasır erişim)
- Bit-perfect: Windows ses motoru bypass edilir
- Düşük latency: 3-10 ms RTL

### 4.2 WASAPI Shared Mode

- Birden fazla uygulama aynı cihazı paylaşır
- Windows ses motoru sample rate conversion yapar
- Yüksek latency: 20-50 ms RTL
- Fallback olarak kullanılır

### 4.3 WASAPI vs ASIO Karşılaştırması

| Özellik | ASIO | WASAPI Exclusive | WASAPI Shared |
|---------|------|-----------------|---------------|
| Latency | < 2 ms | 3-10 ms | 20-50 ms |
| Bit-perfect | Evet | Evet | Hayır |
| Direct Monitoring | Evet | Hayır | Hayır |
| Multi-client | Evet | Hayır | Evet |
| Tüm ses kartları | Sürücü gerekli | Evet | Evet |

---

## 5. CoreAudio Entegrasyonu (macOS)

### 5.1 CoreAudio Özellikleri

- macOS'un yerel ses API'si
- HAL (Hardware Abstraction Layer) üzerinden düşük seviye erişim
- Aggregate Device: Birden fazla cihazı tek sanal cihazda birleştirme
- Multi-channel: 64+ kanal desteği
- Sample rate: 176.4/192 kHz native desteği

### 5.2 CoreAudio vs ASIO

```cpp
// CoreAudio — macOS'a özgü
AudioObjectID deviceID = getDefaultOutputDevice();
AudioStreamBasicDescription format;
format.mSampleRate = 48000.0;
format.mFormatID = kAudioFormatLinearPCM;
format.mFormatFlags = kAudioFormatFlagIsFloat | kAudioFormatFlagIsPacked;
format.mBitsPerChannel = 32;
format.mChannelsPerFrame = 2;
format.mFramesPerPacket = 1;
```

---

## 6. Linux Ses API'leri

### 6.1 JACK (Jack Audio Connection Kit)

- Profesyonel ses için tasarlanmış
- Düşük latency: 1-5 ms
- Uygulamalar arası ses yönlendirme
- Gerçek zamanlı scheduling (SCHED_FIFO)

### 6.2 ALSA (Advanced Linux Sound Architecture)

- Linux kernel seviyesinde ses
- mmap() ile doğrudan bellek erişimi
- Donanıma özgü özellikler
- Daha yüksek latency (JACK'e göre)

### 6.3 PulseAudio

- Kullanıcı dostu ses sunucusu
- Yüksek latency (30-100 ms)
- Sadece son çare fallback

### 6.4 Linux Ses Önceliği

```
1. JACK (profesyonel kullanım, düşük latency)
2. ALSA (doğrudan donanım, orta latency)
3. PulseAudio (genel kullanım, yüksek latency)
```

---

## 7. Ses Buffer Yönetimi

### 7.1 Lock-Free Ring Buffer (SPSC)

```cpp
template<typename T>
class LockFreeRingBuffer {
    static constexpr size_t BUFFER_SIZE = 4096;  // 2'nin kuvveti
    static constexpr size_t MASK = BUFFER_SIZE - 1;

    std::array<std::atomic<T>, BUFFER_SIZE> buffer;
    std::atomic<size_t> writeIdx{0};    // Sadece audio thread yazar
    std::atomic<size_t> readIdx{0};     // Sadece GUI/network thread okur

public:
    bool write(const T* data, size_t count);
    bool read(T* data, size_t count);
    size_t available() const;
    void reset();
};
```

### 7.2 Triple Buffer (Audio ↔ GUI meter)

```
┌──────────┐     ┌──────────┐     ┌──────────┐
│ Buffer 0 │ ←── │ Buffer 1 │ ←── │ Buffer 2 │
└──────────┘     └──────────┘     └──────────┘
  Audio yazar       GUI okur       Boş
```

- Audio thread her zaman en yeni buffer'a yazar
- GUI thread tamamlanmış en yeni buffer'ı okur
- Buffer değişimi single atomic `std::atomic<int> current{0}` ile

### 7.3 Bellek Stratejisi

| Strateji | Açıklama |
|----------|----------|
| Pre-allocate | Tüm buffer'lar `init()` ile ayrılır, `process()` içinde `new` yasak |
| Cache-line aligned | `alignas(64)` ile false sharing önleme |
| Huge pages | İsteğe bağlı: Linux `madvise(MADV_HUGEPAGE)`, Windows `MEM_LARGE_PAGES` |
| Memory-mapped | Büyük dosyalar için mmap/MapViewOfFile |
| Pool allocator | Küçük, sık nesneler için slab allocator (EQ band state) |

---

## 8. 32/64-bit Float Pipeline

### 8.1 Float Formatları

| Format | Bit | Dinamik Aralık | Kullanım |
|--------|-----|---------------|----------|
| Float32 | 32 | ~770 dB | Standart DSP pipeline |
| Float64 | 64 | ~1540 dB | Master bus, toplama |
| Int24 (packed) | 24 | ~144 dB | ASIO giriş/çıkış |
| Int16 | 16 | ~96 dB | Legacy formatlar |

### 8.2 Float Pipeline Akışı

```
Input (Int24/Int16/Float32)
    │
    ▼
Float32 Dönüşümü              (optimize SIMD)
    │
    ▼
DSP Zinciri (Float32)         (EQ, comp, gate, spatial)
    │
    ▼
Bus Toplaması (Float64)       (overflow koruması)
    │
    ▼
Master Limiter (Float64)       (true peak detection)
    │
    ▼
Dither (Float64 → Float32)    (TPDF noise shaping)
    │
    ▼
Output (Int24/Float32)
```

---

## 9. Sample Rate Conversion

### 9.1 libsamplerate Entegrasyonu

```cpp
class SampleRateConverter {
    SRC_STATE* state;
    SRC_DATA data;

public:
    bool configure(int srcRate, int dstRate, int channels);
    int process(const float* input, float* output, int frames);

    enum Quality {
        SINC_BEST     = SRC_SINC_BEST_QUALITY,    // En yüksek
        SINC_MEDIUM   = SRC_SINC_MEDIUM_QUALITY,  // Orta
        SINC_FASTEST  = SRC_SINC_FASTEST,         // En hızlı
        LINEAR        = SRC_LINEAR,               // En düşük
    };
};
```

### 9.2 SRC Kalite vs Performans

| Mod | SNR (dB) | BW (pass) | CPU (relatif) | Kullanım |
|-----|----------|-----------|---------------|----------|
| SINC_BEST | 145 | 97% | 30x | Offline render |
| SINC_MEDIUM | 121 | 97% | 10x | Real-time (yüksek) |
| SINC_FASTEST | 97 | 95% | 3x | Real-time (orta) |
| LINEAR | 55 | 85% | 1x | Son çare |

### 9.3 SRC Stratejisi

- Master clock kaynağı: ASIO cihaz clock'u
- Giriş SRC: Eğer giriş sample rate ≠ master rate ise
- Çıkış SRC: Eğer çıkış sample rate ≠ master rate ise
- Önbellek: Aynı oran dönüşümlerinde SRC state tekrar kullanılır

---

## 10. Thread Modeli Detay

### 10.1 Audio Thread (Gerçek Zamanlı)

| Özellik | Değer |
|---------|-------|
| Priority | THREAD_PRIORITY_TIME_CRITICAL (Windows) |
| Scheduling | SCHED_FIFO, prio 99 (Linux) |
| Affinity | Tek çekirdek (Core 0), IRQ ile aynı |
| CPU quota | < %80 (headroom bırak) |
| Kısıtlamalar | Mutex, I/O, syscall, allocation YASAK |

### 10.2 GUI Thread

| Özellik | Değer |
|---------|-------|
| Priority | Normal |
| Frame rate | 60 FPS (16.7 ms/frame) |
| V-Sync | Açık (tearing önleme) |
| Render | JUCE::Component::paint() |
| Timer | 15-30 ms (meter güncelleme) |

### 10.3 Network Thread

| Özellik | Değer |
|---------|-------|
| Count | 4 (iş parçacığı havuzu) |
| Priority | Normal |
| I/O model | Non-blocking + epoll/IOCP |
| WebSocket | Per-connection thread yok, shared pool |

### 10.4 Worker Thread Pool

| İş | Thread Count | Kullanım |
|----|-------------|----------|
| Plugin scanning | 2 | Başlangıçta |
| AI inference | 4 | Batch processing |
| SRC conversion | 1 | Gerektiğinde |
| File I/O | 2 | Kayıt, export |

---

## 11. Benchmark Hedefleri

### 11.1 Latency Hedefleri (Round-Trip)

| API | Buffer Size | Sample Rate | RTL Hedefi |
|-----|-------------|-------------|-----------|
| ASIO | 32 | 96 kHz | < 1.0 ms (1.3 ms incl. driver) |
| ASIO | 64 | 48 kHz | < 2.0 ms |
| ASIO | 128 | 48 kHz | < 3.5 ms |
| ASIO | 256 | 48 kHz | < 6.0 ms |
| WASAPI Excl | 128 | 48 kHz | < 8.0 ms |
| WASAPI Excl | 256 | 48 kHz | < 12.0 ms |
| WASAPI Shared | 512 | 48 kHz | < 30.0 ms |
| CoreAudio | 64 | 48 kHz | < 3.0 ms |
| JACK | 64 | 48 kHz | < 2.0 ms |

### 11.2 CPU Kullanımı Hedefleri

| Senaryo | CPU (Ryzen 7 5800X) | Açıklama |
|---------|---------------------|----------|
| Idle (no audio) | < 0.3% | Bekleme |
| Passthrough | < 1.0% | Ses geçişi (DSP yok) |
| Full EQ (31 bant) | < 5.0% | Tüm bantlar aktif |
| + Compressor + Gate | < 8.0% | Dinamik işleme |
| + 4 VST3 plugin | < 15.0% | 4 hafif plugin |
| + Spatial (binaural) | < 20.0% | HRTF işleme |
| + AI inference (real-time) | < 30.0% | ONNX hafif model |

### 11.3 Bellek Hedefleri

| Bileşen | RAM | Açıklama |
|---------|-----|----------|
| Motor çekirdeği | ~30 MB | AudioDeviceManager, ring buffer'lar |
| DSP zinciri | ~20 MB | EQ, compressor, gate state |
| Plugin host | ~50 MB | Plugin state, sandbox process'ler |
| AI modelleri (yüklü) | ~2 GB | ONNX runtime + model ağırlıkları |
| GUI | ~50 MB | JUCE bileşenleri, grafik kaynakları |
| Toplam (headless) | ~200 MB | AI olmadan |
| Toplam (GUI + AI) | ~4 GB | Tüm özellikler aktif |

---

## 12. Hata Yönetimi ve Fallback

### 12.1 Ses Kesintisi Senaryoları

| Senaryo | Tepki | Kurtarma |
|---------|-------|----------|
| Cihaz çıkarıldı | Sessizlik, GUI uyarı | 5 sn'de bir yeniden bağlanma |
| Buffer underrun | Sessiz sample ekle | Dropout counter artır |
| ASIO driver çökmesi | WASAPI fallback | 30 sn sonra ASIO yeniden dene |
| Sample rate değişimi | Tüm DSP yeniden başlat | Zincir yeniden yapılandır |
| Clock drift | Adaptif resampling | ±1 sample/sn tolerans |

### 12.2 Fallback Zinciri

```
ASIO
  ↓ başarısız
WASAPI Exclusive
  ↓ başarısız
CoreAudio Exclusive (Mac) / JACK (Linux)
  ↓ başarısız
WASAPI Shared (Win) / CoreAudio Shared (Mac) / ALSA (Linux)
  ↓ başarısız
PortAudio (generic, son çare)
  ↓ başarısız
Dummy Audio Device (sessiz, test için)
```

---

## 13. Test Stratejisi

### 13.1 Birim Testleri

| Test | Kapsam |
|------|--------|
| RingBufferTest | Lock-free doğruluk, race condition |
| DeviceScanTest | Cihaz keşfi, format enumeration |
| SampleRateTest | SRC kalitesi ve performansı |
| FloatPipelineTest | 32/64-bit float doğruluğu |
| FallbackTest | API geçişi, cihaz çıkarma |

### 13.2 Entegrasyon Testleri

| Test | Kapsam |
|------|--------|
| FullChainTest | Input → DSP → Output tam zincir |
| StressTest | 48 saat sürekli çalışma |
| LatencyTest | RTL ölçümü (Oblique RTL) |
| BitPerfectTest | Dijital loopback + checksum |
| MultiDeviceTest | Birden fazla ses kartı |

---

## 14. Yapılandırma API'si

### 14.1 WebSocket JSON-RPC

```json
// Cihaz listesi
{"method": "audio.listDevices", "params": {}, "id": 1}

// Cihaz seçimi
{"method": "audio.selectDevice", "params": {
    "name": "Focusrite USB ASIO",
    "sampleRate": 48000,
    "bufferSize": 128
}, "id": 2}

// Durum sorgulama
{"method": "audio.getStatus", "params": {}, "id": 3}
// Yanıt: {"result": {"running": true, "latency": 2.8, "dropouts": 0, "cpu": 4.2}}

// Cihaz bilgisi
{"method": "audio.getDeviceInfo", "params": {}, "id": 4}
```

### 14.2 REST API

```
GET  /api/v1/audio/devices           → Cihaz listesi
GET  /api/v1/audio/status            → Durum (latency, CPU, dropouts)
POST /api/v1/audio/device/select     → Cihaz seçimi
GET  /api/v1/audio/meters            → Peak/RMS metre değerleri
```

---

## 15. Gelecek Genişletmeler

| Özellik | Faz | Açıklama |
|---------|-----|----------|
| DSD (Direct Stream Digital) | F6 | DSD64/128/256 native playback |
| AES67 / Dante | F6+ | Ağ ses protokolü |
| AVB (Audio Video Bridging) | F6+ | IEEE 1722 standardı |
| MADI | F6+ | 64+ kanal profesyonel |
| Network clock (PTP) | F6+ | IEEE 1588 hassas saat |
| ASIO Multi-Device | F6+ | Birden fazla ASIO senkronizasyonu |

---

*Neva Engine v1.0.0 — Ses Çekirdeği Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
