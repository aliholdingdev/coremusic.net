---
title: "ASIO Sürücüleri"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

# ASIO Sürücüleri

## Genel Bakış

ASIO (Audio Stream Input/Output), Steinberg tarafından geliştirilen ve Windows üzerinde profesyonel ses uygulamları için düşük gecikmeli doğrudan donanım erişimi sağlayan sürücü protokolüdür. COREMUSIC, ASIO Exclusive mode ile 0.5ms round-trip latency hedefler.

## Teknik Detaylar

### ASIO Mimarisi

ASIO, Windows ses alt yapısını (WDM/MME/DirectSound) tamamen atlayarak uygulama ile ses kartı arasında doğrudan bir veri yolu oluşturur. Bu sayede:

- **Kernel geçişleri minimize edilir**: Veri kopyalama yalnızca bir kez gerçekleşir
- **Buffer boyutu uygulama tarafından kontrol edilir**: 32 sample'a kadar düşürülebilir
- **Interrupt-driven processing**: Donanım kesmesi tetikleme ile çalışır

### ASIO Exclusive Mode

COREMUSIC, iki mod destekler:

```
┌─────────────────────────────────────────────┐
│           ASIO Working Modes                │
├─────────────────┬───────────────────────────┤
│ Exclusive Mode  │ Shared Mode               │
├─────────────────┼───────────────────────────┤
│ Doğrudan HW     │ Windows Mixed ile_paylaşım│
│ < 0.5ms latency │ 2-10ms latency            │
│ Tek uygulama    │ Çoklu uygulama             │
│ Kesin kontrol   │ Sınırlı kontrol            │
└─────────────────┴───────────────────────────┘
```

### Buffer Yönetimi

ASIO buffer yönetimi kritik önem taşır:

1. **Double Buffering**: İki buffer arasında kesintisiz geçiş
   - Buffer A okunurken Buffer B yazılır
   - Geçiş: `callbackDrivenMode` ile tetiklenir

2. **Buffer Boyut Seçimi**:
   - 32 sample @ 96kHz = 0.33ms (minimum)
   - 64 sample @ 96kHz = 0.67ms (dengeli)
   - 128 sample @ 96kHz = 1.33ms (güvenli)

3. **Ring Buffer Implementasyonu**:
   ```
   Head → [data] → [data] → [data] → Tail
   Head, donanım tarafından güncellenir
   Tail, uygulama tarafından güncellenir
   ```

### ASIO Callback Zinciri

```cpp
void ASIOCallback(long index, long process) {
    // 1. Input buffer'ı oku
    readInputBuffer(index, inputBuffers);
    
    // 2. K3 Ses Motoru'na ilet
    feedToEngine(inputBuffers, sampleCount);
    
    // 3. K3'ten output buffer'ı al
    readFromEngine(outputBuffers, sampleCount);
    
    // 4. Output buffer'ı donanıma yaz
    writeOutputBuffer(index, outputBuffers);
}
```

### Donanım Abstraction

ASIO sürücüsü aşağıdaki soyutlama katmanlarını kullanır:

| Seviye | Sorumluluk |
|--------|------------|
| ASIO SDK | Callback yönetimi, buffer değişimi |
| Driver Interface | Chipset-specific register erişimi |
| HAL Abstraction | Platform-bağımsız arayüz |
| DMA Engine | Bellek → Donanım veri transferi |

### Latency Hesaplama

```
Total Latency = Input Buffer + Processing + Output Buffer + Driver Overhead

Örnek (96kHz, 64 sample):
Input:    64/96000 = 0.667ms
Process:  ~0.1ms (K3 DSP)
Output:   64/96000 = 0.667ms
Driver:   ~0.05ms
─────────────────────────────
Total:    ~1.48ms (one-way)
RTT:      ~2.96ms (round-trip)
```

### Hata Yönetimi

ASIO sürücüsü aşağıdaki hata durumlarını işler:

- **ASIOError_InvalidMode**: Exclusive mode kullanılamıyorsa Shared mode'a geç
- **ASIOError_BufferSize**: Buffer boyutu donanım tarafından desteklenmiyorsa
- **ASIOError_HardwareFailure**: Donanım hatası, K3'ü durdur
- **ASIOError_UnableToStart**: Başlatma hatası, 3 yeniden deneme

## API / Arayüz

```cpp
// ASIO Driver Manager
class ASIODriverManager {
public:
    bool initialize(const AudioDeviceConfig& config);
    bool start();
    void stop();
    
    // Buffer yapılandırması
    bool setBufferSize(long minSize, long maxSize, long* preferred);
    bool canSampleRate(ASIOSampleRate rate);
    bool setSampleRate(ASIOSampleRate rate);
    
    // Callback kayıt
    void registerCallback(ASIOCallback* callback);
    
    // Exclusive mode kontrolü
    bool enableExclusiveMode();
    bool isExclusiveModeActive() const;
    
    // Latency bilgisi
    double getInputLatency() const;
    double getOutputLatency() const;
};

// Örnek kullanım
ASIODriverManager driver;
AudioDeviceConfig config;
config.deviceId = getDefaultASIODevice();
config.sampleRate = 96000;
config.bufferSize = 64;
config.exclusiveMode = true;

driver.initialize(config);
driver.setBufferSize(32, 128, &config.bufferSize);
driver.start();
```

## Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Input Latency | 0.67ms | 0.65ms |
| Output Latency | 0.67ms | 0.68ms |
| Round-trip Latency | 1.34ms | 1.33ms |
| CPU Kullanımı (boşta) | < 1% | 0.3% |
| Maksimum Kanal | 64x64 | 64x64 |
| Buffer Değişim Süresi | < 10μs | 8μs |

## Bağımlılıklar

| Bağımlılık | Tür | Açıklama |
|------------|-----|----------|
| ASIO SDK | Dış kütüphane | Steinberg ASIO SDK v2.3+ |
| K1 Windows HAL | İç katman | Donanım erişimi için |
| K3 Neva Engine | İç katman | Ses verisi işleme |

## Durum: Implementasyon

- **Faz 1**: ASIO SDK entegrasyonu ve temel callback yapısı
- **Faz 2**: Exclusive mode implementasyonu
- **Faz 3**: Buffer optimizasyonu ve latency testleri
- **Faz 4**: Hata yönetimi ve graceful degradation
- **Tahmini Süre**: 3 hafta (120 adam-saat)
