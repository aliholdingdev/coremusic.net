---
title: "ASIO ve WASAPI Sürücüleri - k015-platform-suruculeri"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT - alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# ASIO ve WASAPI Sürücüleri

> Klasör: `k015-platform-suruculeri` · Dosya: `asio-ve-wasapi.md`
> Sorumlu persona: `windows-software-engineer` · `embedded-engineer`
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` (1 satir / 6 bolum) + `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` (1 satir / 6 bolum) — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.
## Genel Bakış

Windows platformunun iki ses sürücü yolu: ASIO (Steinberg — exclusive mode, düşük gecikme, doğrudan donanım erişimi) ve WASAPI (Microsoft — Exclusive / Shared çalışma modları, Windows audio session). Her iki kaynak dosya da tam olarak aktarılmıştır.


## Kapsam ve Sınırlar

- **Kapsam:** ASIO mimarisi, exclusive mode, buffer yönetimi, callback zinciri, donanım abstraction, latency hesabı, hata yönetimi; WASAPI çalışma modları, exclusive implementasyonu, buffer, session, format, latency, hata durumları; iki dosyanın da API / arayüz, performans metrikleri ve bağımlılık bölümleri.
- **Kapsam dışı:** macOS CoreAudio ve PipeWire (→ [[core-audio-ve-pipewire.md]]), Linux ALSA (→ [[../k016-linux-ses/index]]), sürücü yığını altyapısı (→ [[../k014-surucu-yigin/index]]).
- **Bağlı olduğu klasör:** [[index.md]] (Platform Sürücüleri)
- **Çapraz referanslar:** [[../k000-windows-core/index]] · [[../k036-asio-drivers/index]] · [[../k037-wasapi-exclusive/index]] · [[../k033-platform-ses-suruculeri/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi (`#` → `###`); her blokta kanıt satırı kaynak dosyayı ve gerçek satır aralığını (`L<başlangıç>-L<bitiş>`) gösterir. Bloklar kaynaktan değiştirilmeden kopyalanmıştır.

### asio-drivers.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` (179 satir)


#### ASIO Sürücüleri

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md - L8-L13

### ASIO Sürücüleri

#### Genel Bakış

ASIO (Audio Stream Input/Output), Steinberg tarafından geliştirilen ve Windows üzerinde profesyonel ses uygulamları için düşük gecikmeli doğrudan donanım erişimi sağlayan sürücü protokolüdür. COREMUSIC, ASIO Exclusive mode ile 0.5ms round-trip latency hedefler.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md - L14-L113

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

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md - L114-L179

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

### wasapi-exclusive.md - `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` (199 satir)


#### WASAPI Sürücüleri

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md - L8-L13

### WASAPI Sürücüleri

#### Genel Bakış

WASAPI (Windows Audio Session API), Windows Vista ve sonrası için Microsoft'un modern ses API'sidir. COREMUSIC, WASAPI'yi hem Exclusive hem de Shared modda kullanarak Windows platformunda profesyonel ses desteği sağlar.

#### Teknik Detaylar

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md - L14-L130

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

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md - L131-L199

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

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | ASIO Sürücüleri | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | Teknik Detaylar | L14-L113 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | API / Arayüz | L114-L179 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | WASAPI Sürücüleri | L8-L13 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | Teknik Detaylar | L14-L130 | ✓ verbatim |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | API / Arayüz | L131-L199 | ✓ verbatim |

## Belirsizlik Taraması (kaynak metin)

- Kaynaklarda `UNKNOWN` / `TODO` / `Belirlenecek` / `VERIFICATION REQUIRED` içeren satır **tespit edilmedi** (tam tarama).

## İlgili Dosyalar

[[index.md]] · [[core-audio-ve-pipewire.md]] · [[../k000-windows-core/index]] · [[../k036-asio-drivers/index]] · [[../k037-wasapi-exclusive/index]]

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault yedeği (`_backup/arch-2026-10-06_1057/architecture/k2-surucu/`) kanıtına dayanır; diskteki uygulama kodu ile çapraz doğrulama yapılmamıştır.
2. ⚠️ VERIFICATION REQUIRED — kaynak frontmatter `date: 2026-09-20` / `layer: K2` değerleri ile bu dosyanın `updated: 2026-10-06` değeri arasındaki fark üst merci onayı bekler.
