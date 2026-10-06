---
title: "K015 WASAPI ve CoreAudio Platform Detayı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K015 — WASAPI ve CoreAudio Platform Detayı

> **K numarası:** K015 · **Klasör:** `k015-platform-suruculeri` · **Dosya:** `wasapi-coreaudio-platform-detay`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Windows (ASIO, WASAPI exclusive) ve macOS (CoreAudio/HAL) platform ses arayüzlerinin rollerini, akış modlarını ve kısıtlarını toplamak.

## 1. Kapsam ve Amaç

Bu dosya **WASAPI ve CoreAudio Platform Detayı** konusunu ele alır. Kapsamı: WASAPI exclusive/shared akış modları ile CoreAudio/HAL cihaz–akış yönetiminin karşılaştırmalı tanımı.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Uygulama (DAW) ]
            │
            ▼
      [ ASIO callback / WASAPI endpoint / CoreAudio HAL ]
            │
            ▼
      [ Platform sürücüsü ]
            │
            ▼
      [ OS ses motoru ]
            │
            ▼
      [ Donanım ]
```

**Akış notları:**

1. **Uygulama (DAW)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **ASIO callback / WASAPI endpoint / CoreAudio HAL** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Platform sürücüsü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **OS ses motoru** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Donanım** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | 200 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | 180 | ikincil kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k2-surucu/wasapi-exclusive.md`

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Latency | 1-3ms | 10-40ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |
| Multi-app | Hayır | Evet |
| DSP | Donanım | Windows |

### 4.2 · `k2-surucu/wasapi-exclusive.md`

| Bileşen | Açıklama |
|---------|----------|
| AudioSessionControl | Oturum kontrolü (ses seviyesi, durdurma) |
| AudioSessionManager | Oturum yönetimi (tercihler, efektler) |
| AudioMeterInformation | Gerçek zamanlı ses seviyesi metering |
| AudioEndpointVolume | Donanım ses seviyesi kontrolü |

### 4.3 · `k2-surucu/wasapi-exclusive.md`

| Hata | Kod | Çözüm |
|------|-----|-------|
| AUDCLNT_E_DEVICE_IN_USE | 0x8889000A | Shared mode'a geç |
| AUDCLNT_E_UNSUPPORTED_FORMAT | 0x88890008 | Formatı değiştir |
| AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED | 0x8889000E | Yetki kontrolü |
| AUDCLNT_E_BUFFER_SIZE_ERROR | 0x88890018 | Buffer boyutunu ayarla |

### 4.4 · `k2-surucu/wasapi-exclusive.md`

| Metrik | Exclusive | Shared |
|--------|-----------|--------|
| Input Latency | 1.5ms | 15ms |
| Output Latency | 1.5ms | 15ms |
| Round-trip | 3ms | 30ms |
| CPU (boşta) | 0.5% | 2% |
| Bit-perfect | Evet | Hayır |

### 4.5 · `k2-surucu/asio-drivers.md`

| Seviye | Sorumluluk |
|--------|------------|
| ASIO SDK | Callback yönetimi, buffer değişimi |
| Driver Interface | Chipset-specific register erişimi |
| HAL Abstraction | Platform-bağımsız arayüz |
| DMA Engine | Bellek → Donanım veri transferi |

### 4.6 · `k2-surucu/asio-drivers.md`

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Input Latency | 0.67ms | 0.65ms |
| Output Latency | 0.67ms | 0.68ms |
| Round-trip Latency | 1.34ms | 1.33ms |
| CPU Kullanımı (boşta) | < 1% | 0.3% |
| Maksimum Kanal | 64x64 | 64x64 |
| Buffer Değişim Süresi | < 10μs | 8μs |

### 4.7 · `k2-surucu/asio-drivers.md`

| Bağımlılık | Tür | Açıklama |
|------------|-----|----------|
| ASIO SDK | Dış kütüphane | Steinberg ASIO SDK v2.3+ |
| K1 Windows HAL | İç katman | Donanım erişimi için |
| K3 Neva Engine | İç katman | Ses verisi işleme |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k2-surucu/wasapi-exclusive.md` | H1 | WASAPI Sürücüleri |
| 2 | `k2-surucu/wasapi-exclusive.md` | H2 | Genel Bakış |
| 3 | `k2-surucu/wasapi-exclusive.md` | H2 | Teknik Detaylar |
| 4 | `k2-surucu/wasapi-exclusive.md` | H3 | WASAPI Çalışma Modları |
| 5 | `k2-surucu/wasapi-exclusive.md` | H3 | Exclusive Mode Implementasyonu |
| 6 | `k2-surucu/wasapi-exclusive.md` | H3 | Exclusive Mode Avantajları |
| 7 | `k2-surucu/wasapi-exclusive.md` | H3 | Buffer Yönetimi |
| 8 | `k2-surucu/wasapi-exclusive.md` | H3 | Windows Audio Session |
| 9 | `k2-surucu/wasapi-exclusive.md` | H3 | Format Desteği |
| 10 | `k2-surucu/wasapi-exclusive.md` | H3 | Latency Optimizasyonu |
| 11 | `k2-surucu/wasapi-exclusive.md` | H3 | Hata Durumları |
| 12 | `k2-surucu/wasapi-exclusive.md` | H2 | API / Arayüz |
| 13 | `k2-surucu/wasapi-exclusive.md` | H2 | Performans Metrikleri |
| 14 | `k2-surucu/wasapi-exclusive.md` | H2 | Bağımlılıklar |
| 15 | `k2-surucu/wasapi-exclusive.md` | H2 | Durum: Implementasyon |
| 16 | `k2-surucu/asio-drivers.md` | H1 | ASIO Sürücüleri |
| 17 | `k2-surucu/asio-drivers.md` | H2 | Genel Bakış |
| 18 | `k2-surucu/asio-drivers.md` | H2 | Teknik Detaylar |
| 19 | `k2-surucu/asio-drivers.md` | H3 | ASIO Mimarisi |
| 20 | `k2-surucu/asio-drivers.md` | H3 | ASIO Exclusive Mode |
| 21 | `k2-surucu/asio-drivers.md` | H3 | Buffer Yönetimi |
| 22 | `k2-surucu/asio-drivers.md` | H3 | ASIO Callback Zinciri |
| 23 | `k2-surucu/asio-drivers.md` | H3 | Donanım Abstraction |
| 24 | `k2-surucu/asio-drivers.md` | H3 | Latency Hesaplama |
| 25 | `k2-surucu/asio-drivers.md` | H3 | Hata Yönetimi |
| 26 | `k2-surucu/asio-drivers.md` | H2 | API / Arayüz |
| 27 | `k2-surucu/asio-drivers.md` | H2 | Performans Metrikleri |
| 28 | `k2-surucu/asio-drivers.md` | H2 | Bağımlılıklar |
| 29 | `k2-surucu/asio-drivers.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md`


### WASAPI Sürücüleri

#### Genel Bakış

WASAPI (Windows Audio Session API), Windows Vista ve sonrası için Microsoft'un modern ses API'sidir. COREMUSIC, WASAPI'yi hem Exclusive hem de Shared modda kullanarak Windows platformunda profesyonel ses desteği sağlar.

#### Teknik Detaylar

##### WASAPI Çalışma Modları

```
┌─────────────────────────────────────────────────────────┐
│                WASAPI Working Modes                     │
├─────────────────────┬───────────────────────────────────┤
│   Exclusive Mode    │         Shared Mode               │
├─────────────────────┼───────────────────────────────────┤
│ Doğrudan HW erişimi │ Windows Audio Service üzerinden   │
│ Mix organizer yok   │ Mix organizer devrede             │
│ Tek uygulama        │ Çoklu uygulama                    │
│ Bit-perfect output  │ DSP eklenebilir                   │
│ Düşük latency       │ Yüksek latency                    │
│ Donanım formatı     │ Win formats (PCM, IEEE float)     │
└─────────────────────┴───────────────────────────────────┘
```

##### Exclusive Mode Implementasyonu

Exclusive mode, uygulamanın ses kartına doğrudan erişmesini sağlar:

1. **IAudioClient arayüzü**: Ana kontrol noktası
   - `Initialize()`: Exclusive mode ile başlatma
   - `GetBufferDuration()`: Buffer süresi sorgusu
   - `Start()` / `Stop()`: Akış kontrolü

2. **IAudioCaptureClient**: Giriş verisi okuma
   - `GetBuffer()`: Ham veri erişimi
   - `ReleaseBuffer()`: Buffer'ı serbest bırak
   - `GetNextPacketSize()`: Bir sonraki paket boyutu

3. **IAudioRenderClient**: Çıkış verisi yazma
   - `GetBuffer()`: Yazma alanı alma
   - `ReleaseBuffer()`: Veriyi donanıma iletme

##### Exclusive Mode Avantajları

| Özellik | Exclusive | Shared |
|---------|-----------|--------|
| Latency | 1-3ms | 10-40ms |
| Bit-perfect | Evet | Hayır |
| CPU | Düşük | Yüksek |
| Multi-app | Hayır | Evet |
| DSP | Donanım | Windows |

##### Buffer Yönetimi

WASAPI buffer yönetimi ASIO'dan farklıdır:

```
Exclusive Mode:
┌──────────────────────────────────────┐
│  App Buffer → Driver Buffer → HW     │
│  (definite)   (definite)             │
└──────────────────────────────────────┘

Shared Mode:
┌──────────────────────────────────────┐
│  App Buffer → Audio Engine → HW      │
│  (definite)   (indefinite)           │
└──────────────────────────────────────┘
```

**Buffer Boyut Hesaplama**:
```
BufferDuration = (BufferSize / SampleRate) * 1,000,000 (μs)

Örnek:
Buffer: 288 samples @ 48kHz
Duration: (288 / 48000) * 1,000,000 = 6000μs = 6ms
```

##### Windows Audio Session

Her WASAPI oturumu aşağıdaki bileşenleri içerir:

| Bileşen | Açıklama |
|---------|----------|
| AudioSessionControl | Oturum kontrolü (ses seviyesi, durdurma) |
| AudioSessionManager | Oturum yönetimi (tercihler, efektler) |
| AudioMeterInformation | Gerçek zamanlı ses seviyesi metering |
| AudioEndpointVolume | Donanım ses seviyesi kontrolü |

##### Format Desteği

WASAPI aşağıdaki ses formatlarını destekler:

```cpp
// Desteklenen formatlar
WAVEFORMATEX formats[] = {
    {WAVE_FORMAT_PCM,     16, 2, 48000},  // 16-bit PCM
    {WAVE_FORMAT_PCM,     24, 2, 96000},  // 24-bit PCM
    {WAVE_FORMAT_IEEE_FLOAT, 32, 2, 192000}, // 32-bit Float
    {WAVE_FORMAT_EXTENSIBLE, 32, 8, 384000}, // 8-kanal 32-bit
};
```

##### Latency Optimizasyonu

WASAPI Exclusive mode'da minimum latency için:

1. **Buffer Süresi**: 3-6ms arası (donanıma bağlı)
2. **Period Goddessi**: `IAudioClient::SetEventHandle()` ile kesme zamanlaması
3. **Thread Önceliği**: `THREAD_PRIORITY_TIME_CRITICAL` ile yüksek öncelik
4. **CPU Affinity**: Ses thread'ini belirli CPU çekirdeğine ata

##### Hata Durumları

| Hata | Kod | Çözüm |
|------|-----|-------|
| AUDCLNT_E_DEVICE_IN_USE | 0x8889000A | Shared mode'a geç |
| AUDCLNT_E_UNSUPPORTED_FORMAT | 0x88890008 | Formatı değiştir |
| AUDCLNT_E_EXCLUSIVE_MODE_NOT_ALLOWED | 0x8889000E | Yetki kontrolü |
| AUDCLNT_E_BUFFER_SIZE_ERROR | 0x88890018 | Buffer boyutunu ayarla |

#### API / Arayüz

```cpp
class WASAPIDriver {
public:
    bool initialize(const AudioConfig& config);
    bool startExclusive();
    bool startShared();
    void stop();
    
    // Buffer yönetimi
    bool setBufferDuration(uint32_t durationMs);
    uint32_t getBufferDuration() const;
    
    // Format yapılandırması
    bool setFormat(const WAVEFORMATEX& format);
    bool isFormatSupported(const WAVEFORMATEX& format, 
                           EDataFlow dataFlow);
    
    // Session yönetimi
    bool setSessionName(const wchar_t* name);
    bool setSessionIconPath(const wchar_t* path);
    
    // Volume kontrolü
    bool setMasterVolume(float volume);
    bool setMute(bool mute);
    
    // Metering
    float getPeakLevel() const;
    float getRMSLevel() const;
};

// Exclusive mode başlatma örneği
WASAPIDriver driver;
AudioConfig config;
config.sampleRate = 96000;
config.bitsPerSample = 32;
config.channels = 2;
config.bufferDurationMs = 4;

driver.initialize(config);
driver.startExclusive();
```

#### Performans Metrikleri

| Metrik | Exclusive | Shared |
|--------|-----------|--------|
| Input Latency | 1.5ms | 15ms |
| Output Latency | 1.5ms | 15ms |
| Round-trip | 3ms | 30ms |
| CPU (boşta) | 0.5% | 2% |
| Bit-perfect | Evet | Hayır |

#### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| Windows SDK | Dış |
| K1 Windows Core | İç |
| K3 Engine | İç |

#### Durum: Implementasyon

- **Faz 1**: WASAPI SDK entegrasyonu, temel Exclusive mode
- **Faz 2**: Shared mode, session yönetimi
- **Faz 3**: Metering ve volume kontrolü
- **Faz 4**: Format dönüşümleri, hata yönetimi
- **Tahmini Süre**: 2.5 hafta (100 adam-saat)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md`


### ASIO Sürücüleri

#### Genel Bakış

ASIO (Audio Stream Input/Output), Steinberg tarafından geliştirilen ve Windows üzerinde profesyonel ses uygulamları için düşük gecikmeli doğrudan donanım erişimi sağlayan sürücü protokolüdür. COREMUSIC, ASIO Exclusive mode ile 0.5ms round-trip latency hedefler.

#### Teknik Detaylar

##### ASIO Mimarisi

ASIO, Windows ses alt yapısını (WDM/MME/DirectSound) tamamen atlayarak uygulama ile ses kartı arasında doğrudan bir veri yolu oluşturur. Bu sayede:

- **Kernel geçişleri minimize edilir**: Veri kopyalama yalnızca bir kez gerçekleşir
- **Buffer boyutu uygulama tarafından kontrol edilir**: 32 sample'a kadar düşürülebilir
- **Interrupt-driven processing**: Donanım kesmesi tetikleme ile çalışır

##### ASIO Exclusive Mode

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

##### Buffer Yönetimi

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

##### ASIO Callback Zinciri

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

##### Donanım Abstraction

ASIO sürücüsü aşağıdaki soyutlama katmanlarını kullanır:

| Seviye | Sorumluluk |
|--------|------------|
| ASIO SDK | Callback yönetimi, buffer değişimi |
| Driver Interface | Chipset-specific register erişimi |
| HAL Abstraction | Platform-bağımsız arayüz |
| DMA Engine | Bellek → Donanım veri transferi |

##### Latency Hesaplama

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

##### Hata Yönetimi

ASIO sürücüsü aşağıdaki hata durumlarını işler:

- **ASIOError_InvalidMode**: Exclusive mode kullanılamıyorsa Shared mode'a geç
- **ASIOError_BufferSize**: Buffer boyutu donanım tarafından desteklenmiyorsa
- **ASIOError_HardwareFailure**: Donanım hatası, K3'ü durdur
- **ASIOError_UnableToStart**: Başlatma hatası, 3 yeniden deneme

#### API / Arayüz

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

#### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Input Latency | 0.67ms | 0.65ms |
| Output Latency | 0.67ms | 0.68ms |
| Round-trip Latency | 1.34ms | 1.33ms |
| CPU Kullanımı (boşta) | < 1% | 0.3% |
| Maksimum Kanal | 64x64 | 64x64 |
| Buffer Değişim Süresi | < 10μs | 8μs |

#### Bağımlılıklar

| Bağımlılık | Tür | Açıklama |
|------------|-----|----------|
| ASIO SDK | Dış kütüphane | Steinberg ASIO SDK v2.3+ |
| K1 Windows HAL | İç katman | Donanım erişimi için |
| K3 Neva Engine | İç katman | Ses verisi işleme |

#### Durum: Implementasyon

- **Faz 1**: ASIO SDK entegrasyonu ve temel callback yapısı
- **Faz 2**: Exclusive mode implementasyonu
- **Faz 3**: Buffer optimizasyonu ve latency testleri
- **Faz 4**: Hata yönetimi ve graceful degradation
- **Tahmini Süre**: 3 hafta (120 adam-saat)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | WASAPI shared modda OS mixer kaybı | ölçülmüş etki yok | ⚠️ VERIFICATION REQUIRED |
| 2 | CoreAudio cihaz/kanel örneği vault'ta listelenmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Exclusive mod talebinin reddi (başka uygulama açık) (kaynak: `wasapi-exclusive`).
2. Oturum değişiminde akışın kesilmesi (kaynak: `wasapi-exclusive`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k014-surucu-yigin/index]]` | ↑ | Genel sürücü yığını ve buffer kuralları | `.ai/architecture/k014-surucu-yigin/index.md` |
| `[[../k000-windows-core/index]]` | ↑ | Windows API yüzeyi (Win32/COM) | `.ai/architecture/k000-windows-core/index.md` |
| `[[../k002-macos-tasinabilirlik/index]]` | ↑ | macOS çekirdek/taşınabilirlik | `.ai/architecture/k002-macos-tasinabilirlik/index.md` |
| `[[../k016-linux-ses/index]]` | ↓ | Linux karşılığı ALSA/PipeWire | `.ai/architecture/k016-linux-ses/index.md` |

Yerel dosyalar:

- `[[asio-wasapi-coreaudio]]` — ASIO · WASAPI · CoreAudio Sürücü Yüzeyi
- `[[wasapi-coreaudio-platform-detay]]` — WASAPI ve CoreAudio Platform Detayı

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K015 · WASAPI ve CoreAudio Platform Detayı — SSOT: `.ai/architecture/k015-platform-suruculeri/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
