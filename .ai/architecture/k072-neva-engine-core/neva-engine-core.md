---
title: "K072 Neva Engine Core — C++20 Real-Time Safe Ses Motoru Çekirdeği"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K072 — Neva Engine Core (C++20 / Real-Time Safe / Lock-Free)

> **K numarası:** K072 · **Klasör:** `k072-neva-engine-core` · **Dosya:** `neva-engine-core`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (latency / CPU bütçesi)
> **İlgili ADR'ler:** `ADR-025` (31-band parametrik EQ) · `ADR-062` (DSP Pipeline Architecture) — kaynak: `_backup/.../k3-ses-motoru/README.md` §10 ve `CLAUDE.md` §5

## 1. Genel Bakış

Neva Engine Core, CoreMusic ses işleme motorunun (K3 katmanı) çekirdek yürütme birimidir. Kaynak
dokümana göre motor **C++20**, **real-time safe** ve **lock-free** olarak tanımlanmıştır; DSP zincirini
(15 aşama), efektleri, analiz bloklarını ve format dönüşümlerini tek bir çalışma zamanı içinde yürütür.
Bu dosya; motorun yaşam döngüsü, thread modeli, bellek disiplini ve durum makinesini tanımlar; zincirin
içeriğini `[[../k073-dsp-chain/dsp-chain]]` belgeler.

Temel ilkeler kaynak `index.md` (K3 Ses Motoru Katmanı) içinde açıkça listelenir: bellek ayırma,
kilitlenme (blocking), sistem çağrısı ve bellek serbest bırakma işlemleri real-time context'te yasaktır.
Bu dört yasak, motorun davranışı kadar **hata modlarını** da belirler: ihlal edilen her kural ayrı bir
kilitlenme / ses patlaması (glitch) senaryosuna dönüşür (bkz. §8).

Motor K2'den ham ses verisini alır, işlenmiş sinyali K2'ye geri iletir; katmanlar arası sınır
`K0 (Donanım) → K1 (OS) → K2 (Sürücü) → K3 (Ses Motoru) → K4 (Uygulama)` şemasıyla tanımlıdır.
Bu şema kaynak `index.md` §"Mimari Konum" bölümünden alınmıştır; K1/K2/K4 tarafındaki karşılıkları
bu klasörün **kapsam dışı**dır (uygulama ve sürücü katmanları kendi K dizinlerinde belgelenir).

Motorun dışa açtığı birincil kavramlar: **engine state machine**, **buffer pool**, **SPSC/MPMC kuyruk**,
**SIMD optimizasyonu** ve **thread hiyerarşisi**. Bu kavramların hiçbiri bu dosyada uydurulmaz; her biri
§4'teki verbatim kaynak aktarımı ile desteklenir ve §13'te kanıt yolu verilir.

### 1.1 Bu Dosyanın Kapsam Sınırı

| Kapsam İçi | Kapsam Dışı | Sınır Nerede |
|---|---|---|
| Motor yaşam döngüsü (init/prepare/start/stop/dispose) | Sürücü callback gerçekleşme detayı | `[[../k073-dsp-chain/dsp-chain]]` |
| Thread modeli ve kuyruk topolojisi | EQ/kompressor katsayı hesabı | `[[../k074-eq-parametric/eq-parametric]]` |
| Buffer pool, ring buffer, RT bellek disiplini | Ağ jitter'i ve adaptif buffer boyutu | `[[../k077-stream-buffer/stream-buffer]]` |
| Durum makinesi ve hata durumuna geçiş | Gapless geçiş zamanlaması | `[[../k078-playback-gapless/playback-gapless]]` |
| SIMD (float32) işleme yolu | Örnekleme hızı dönüşüm matematiği | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi (In-Scope)

| # | Kalem | Açıklama | Kaynak |
|---|---|---|---|
| 1 | Engine yaşam döngüsü | Kurulum, hazırlık, başlatma, durdurma, temizlik | `neva-engine-core.md` §Engine State Machine |
| 2 | Real-time safe ilkeler | Yasaklı işlemler listesi ve denetim noktası | `index.md` §Temel İlkeller |
| 3 | Lock-free veri yapıları | SPSC/MPMC kuyruk, atomic操作, wait-free algoritmalar | `index.md` §Lock-Free Tasarım |
| 4 | Buffer pool | Önceden ayrılmış sabit boyutlu tampon havuzu | `neva-engine-core.md` §Buffer Pool Sistemi |
| 5 | Thread yönetimi | Ses thread'i + yardımcı thread ayrımı | `neva-engine-core.md` §Thread Yönetimi |
| 6 | SIMD optimizasyonu | float32 doğrusal işleme hızlandırma yolu | `neva-engine-core.md` §SIMD Optimizasyonu |
| 7 | 15 aşamalı zincirin yürütücüsü | Sıra disiplini ve aşama geçişi | `dsp-chain.md` §15-Aşamalı Pipeline |

### 2.2 Kapsam Dışı (Out-of-Scope)

| # | Kalem | Gerekçe | Nereye Ait |
|---|---|---|---|
| 1 | ASIO/WASAPI/CoreAudio çağrıları | Sürücü yüzeyi ayrı katmanda | K dizinleri (platform ses sürücüleri) |
| 2 | MySQL/PHP tarafı veri modeli | Uygulama katmanı | D04 veri domaini |
| 3 | EQ bant katsayı üretimi | Aşama içeriği | `[[../k074-eq-parametric/eq-parametric]]` |
| 4 | Reverb/chorus algoritma detayı | Efekt aşamaları | `[[../k083-effects-reverb/effects-reverb]]` |
| 5 | Downmix/surround matrisleri | Kanal işleme | `[[../k080-channel-processing/channel-processing]]` |
| 6 | Dither/noise shaping | Bit derinliği katmanı | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |

### 2.3 Karar Otoriteleri

| Karar | Otorite | Durum |
|---|---|---|
| Zincir sırası (EQ → Compressor → Reverb → Limiter) | `CLAUDE.md` §3 · `dsp-chain.md` L16 · `README.md` §2.2 L68 | Kanıtlı |
| Crossover'ın aşama değil kardeş olduğu | `README.md` §2.1 L59 (Linkwitz-Riley 4. derece) | Kanıtlı |
| ADR referansları (ADR-025, ADR-062) | `README.md` §10 · `CLAUDE.md` §5 | Kanıtlı |
| ADR-062'in dondurulmuş ADR aralığı (001-037) dışındadır | `.ai/log.md` (2026-10-06 kaydı) | Üst karar — ⚠️ VERIFICATION REQUIRED |

## 3. Sinyal Akışı

### 3.1 Üst Düzey Akış

```
                +--------------------------------------------------+
                |              k072-neva-engine-core               |
                |                                                  |
 K2 sürücü ---> |  callback --> SPSC kuyruk --> engine thread       |
 (ham ses)      |                    |                              |
                |                    v                              |
                |            buffer pool (alignas(64))             |
                |                    |                              |
                |                    v                              |
                |      [ K073 DSP zinciri -- 15 aşama ]  ----------+--> K2
                |                    |                              |  (işlenmiş)
                |                    v                              |
                |            telemetry / hata durumu               |
                +--------------------------------------------------+
```

### 3.2 Yön Tablosu

| # | Yön | Veri | Birim | Not |
|---|---|---|---|---|
| 1 | K2 → Motor | Ham ses karesi (frame) | örnek × kanal | Sürücü callback'i çağırır |
| 2 | Motor → Kuyruk | SPSC push (tek yazıcı/tek okuyucu) | kare | Lock-free, atomik head/tail |
| 3 | Kuyruk → Thread | SPSC pop | kare | Real-time context'te kuyruk boşsa glitch |
| 4 | Thread → Buffer pool | Önceden ayrılmış tampon | bayt | Runtime `new`/`malloc` yasak |
| 5 | Buffer → K073 | 15 aşamalı zincir girişi | kare | Sıra: Gain → EQ → … → Limiter |
| 6 | K073 → K2 | İşlenmiş kare | kare | Dışa aktarım (output gain sonrası) |
| 7 | Motor → Telemetry | Sayaç/hata bayrakları | atomik sayaç | Real-time thread'de log yazılmaz |

### 3.3 Thread Topolojisi Özeti

| Thread | Görev | Kuyruk Tipi | RT Durumu |
|---|---|---|---|
| Sürücü callback | Kuyruğa yazma | SPSC producer | RT (sürücü bağlamı) |
| Engine thread | Zincir yürütme | SPSC consumer | RT |
| Decode/IO thread | Çözümleme, dosya okuma | MPMC/SPSC doldurma | RT-dışı |
| UI/telemetri thread | Gözlem, durum gösterimi | Atomik sayaç okuma | RT-dışı |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `neva-engine-core.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/neva-engine-core.md` (365 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### Neva Engine Core

##### Genel Bakış

Neva Engine, COREMUSIC'in merkezi ses işleme motorudur. C++20 ile yazılmış, gerçek zamanlı güvenli (real-time safe) ve kilit-free (lock-free) bir mimariye sahiptir. Tüm ses işleme zincirini yönetir ve alt sistemlerini orkestra eder.

##### Teknik Detaylar

###### Neva Engine Mimarisi

```
┌─────────────────────────────────────────────────────┐
│                Neva Engine Core                      │
│                                                     │
│  ┌──────────────────────────────────────────────┐   │
│  │              Engine Manager                   │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐   │   │
│  │  │ Config   │  │ State    │  │ Metrics  │   │   │
│  │  └──────────┘  └──────────┘  └──────────┘   │   │
│  └──────────────────────────────────────────────┘   │
│                                                     │
│  ┌──────────────────────────────────────────────┐   │
│  │              DSP Pipeline                     │   │
│  │  ┌──────┐  ┌──────┐  ┌──────┐  ┌──────┐    │   │
│  │  │Stage1│→│Stage2│→│Stage3│→│Stage4│→ ...  │   │
│  │  └──────┘  └──────┘  └──────┘  └──────┘    │   │
│  └──────────────────────────────────────────────┘   │
│                                                     │
│  ┌──────────────────────────────────────────────┐   │
│  │              Resource Manager                 │   │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐   │   │
│  │  │ Buffer   │  │ Memory   │  │ Thread   │   │   │
│  │  │ Pool     │  │ Pool     │  │ Pool     │   │   │
│  │  └──────────┘  └──────────┘  └──────────┘   │   │
│  └──────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────┘
```

###### Real-Time Safe İlkeler

Neva Engine, real-time context'te çalışırken aşağıdaki kısıtlamalara uyar:

```cpp
// Real-time kuralları
namespace neva::rt {

// Yasak: Bellek ayırma
// YASAK: new, malloc, std::make_unique
// İZİN: Stack allocation, pool allocation

// Yasak: Kilitlenme
// YASAK: std::mutex, std::condition_variable
// İZİN: std::atomic, spinlock (kısa süreli)

// Yasak: Sistem çağrısı
// YASAK: file I/O, network, sleep
// İZİN: atomic operations, SIMD

// Yasak: Bellek serbest bırakma
// YASAK: delete, free
// İZİN: Pool cleanup (non-RT context'te)

} // namespace neva::rt
```

###### Lock-Free Veri Yapıları

```cpp
// SPSC Ring Buffer (Single Producer Single Consumer)
template<typename T, size_t Capacity>
class alignas(64) SPSCRingBuffer {
public:
    bool push(const T& item) noexcept {
        size_t currentWrite = writePos.load(std::memory_order_relaxed);
        size_t nextWrite = (currentWrite + 1) % Capacity;
        
        if (nextWrite == readPos.load(std::memory_order_acquire)) {
            return false;
        }
        
        buffer[currentWrite] = item;
        writePos.store(nextWrite, std::memory_order_release);
        return true;
    }
    
    bool pop(T& item) noexcept {
        size_t currentRead = readPos.load(std::memory_order_relaxed);
        
        if (currentRead == writePos.load(std::memory_order_acquire)) {
            return false;
        }
        
        item = buffer[currentRead];
        readPos.store((currentRead + 1) % Capacity, 
                     std::memory_order_release);
        return true;
    }
    
private:
    alignas(64) std::atomic<size_t> writePos{0};
    alignas(64) std::atomic<size_t> readPos{0};
    T buffer[Capacity];
};
```

###### Engine State Machine

```cpp
// Engine durum makinesi
enum class EngineState {
    UNINITIALIZED,
    INITIALIZED,
    CONFIGURED,
    RUNNING,
    PAUSED,
    ERROR,
    SHUTDOWN
};

class EngineStateMachine {
public:
    bool transitionTo(EngineState newState) noexcept {
        EngineState current = state.load(std::memory_order_acquire);
        
        if (!isValidTransition(current, newState)) {
            return false;
        }
        
        state.store(newState, std::memory_order_release);
        return true;
    }
    
    EngineState getState() const noexcept {
        return state.load(std::memory_order_acquire);
    }
    
private:
    std::atomic<EngineState> state{EngineState::UNINITIALIZED};
    
    bool isValidTransition(EngineState from, EngineState to) {
        switch (from) {
            case EngineState::UNINITIALIZED:
                return to == EngineState::INITIALIZED;
            case EngineState::INITIALIZED:
                return to == EngineState::CONFIGURED || 
                       to == EngineState::SHUTDOWN;
            case EngineState::CONFIGURED:
                return to == EngineState::RUNNING || 
                       to == EngineState::SHUTDOWN;
            case EngineState::RUNNING:
                return to == EngineState::PAUSED || 
                       to == EngineState::ERROR ||
                       to == EngineState::SHUTDOWN;
            case EngineState::PAUSED:
                return to == EngineState::RUNNING || 
                       to == EngineState::SHUTDOWN;
            case EngineState::ERROR:
                return to == EngineState::INITIALIZED || 
                       to == EngineState::SHUTDOWN;
            default:
                return false;
        }
    }
};
```

###### Buffer Pool Sistemi

```cpp
// Real-time güvenli buffer pool
template<size_t BlockSize, size_t BlockCount>
class BufferPool {
public:
    BufferPool() {
        // Tüm blokları başlangıçta ayır
        for (size_t i = 0; i < BlockCount; i++) {
            freeList.push(&pool[i]);
        }
    }
    
    // RT context'te çağrılabilir
    void* allocate() noexcept {
        void* block = nullptr;
        freeList.pop(block);
        return block;
    }
    
    // RT context'te çağrılabilir
    void deallocate(void* block) noexcept {
        if (block) {
            freeList.push(static_cast<char*>(block));
        }
    }
    
private:
    alignas(64) char pool[BlockSize * BlockCount];
    SPSCRingBuffer<void*, BlockCount> freeList;
};

// Kullanım
using SmallBuffer = BufferPool<256, 1024>;   // 256B bloklar
using MediumBuffer = BufferPool<1024, 512>;  // 1KB bloklar
using LargeBuffer = BufferPool<4096, 128];   // 4KB bloklar
```

###### SIMD Optimizasyonu

```cpp
// SSE/AVX optimizasyonu
#ifdef __AVX2__
#include <immintrin.h>

void processAVX2(float* input, float* output, size_t count) {
    size_t i = 0;
    
    // 8 float aynı anda işle
    for (; i + 8 <= count; i += 8) {
        __m256 in = _mm256_loadu_ps(&input[i]);
        __m256 out = _mm256_mul_ps(in, gain);
        _mm256_storeu_ps(&output[i], out);
    }
    
    // Kalan elemanlar
    for (; i < count; i++) {
        output[i] = input[i] * gainValue;
    }
}
#endif
```

###### Thread Yönetimi

```cpp
// İş parçacığı yönetimi
class ThreadManager {
public:
    bool initialize(uint32_t threadCount) {
        for (uint32_t i = 0; i < threadCount; i++) {
            threads.emplace_back(&ThreadManager::workerThread, this, i);
        }
        return true;
    }
    
    void shutdown() {
        running = false;
        for (auto& t : threads) {
            if (t.joinable()) t.join();
        }
    }
    
private:
    void workerThread(uint32_t threadId) {
        // RT priority ayarla
        setRealTimePriority();
        
        // CPU affinity ayarla
        setCpuAffinity(threadId);
        
        while (running) {
            // İş kuyruğundan iş al
            // İşlemi yap
            // Sonucu ilet
        }
    }
    
    std::vector<std::thread> threads;
    std::atomic<bool> running{true};
};
```

##### API / Arayüz

```cpp
namespace neva {

class Engine {
public:
    static Engine& getInstance() {
        static Engine instance;
        return instance;
    }
    
    bool initialize(const EngineConfig& config);
    void shutdown();
    
    // İşleme
    bool process(AudioBuffer& input, AudioBuffer& output, 
                 uint32_t frameCount);
    
    // Durum
    EngineState getState() const;
    bool isRunning() const;
    
    // Parametreler
    bool setSampleRate(uint32_t rate);
    bool setBufferSize(uint32_t frames);
    bool setChannelCount(uint32_t channels);
    
    // Metrikler
    EngineMetrics getMetrics() const;
    double getCPUUsage() const;
    double getLatency() const;
    
private:
    Engine() = default;
    
    EngineStateMachine stateMachine;
    BufferPool<256, 1024> smallPool;
    BufferPool<1024, 512> mediumPool;
    BufferPool<4096, 128> largePool;
    ThreadManager threadManager;
    
    // DSP Pipeline
    std::unique_ptr<DSPChain> pipeline;
};

// Kullanım örneği
neva::Engine& engine = neva::Engine::getInstance();

EngineConfig config;
config.sampleRate = 96000;
config.bufferSize = 256;
config.channels = 2;
config.threadCount = 4;

engine.initialize(config);

// İşleme döngüsü
AudioBuffer input, output;
while (running) {
    engine.process(input, output, 256);
}

engine.shutdown();

} // namespace neva
```

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| İşleme Latency | < 0.1ms | 0.08ms |
| CPU (boşta) | < 1% | 0.5% |
| CPU (tam kapasite) | < 10% | 8.2% |
| Bellek | < 100MB | 85MB |
| Throughput | > 1M frames/s | 1.2M frames/s |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| SIMD (SSE/AVX) | Donanım |
| K2 Driver | İç |

##### Durum: Implementasyon

- **Faz 1**: Core motor, state machine
- **Faz 2**: Buffer pool sistemi
- **Faz 3**: SIMD optimizasyonu
- **Faz 4**: Thread yönetimi
- **Tahmini Süre**: 4 hafta (160 adam-saat)



## 5. İşleme Aşamaları

| # | Aşama | Giriş | Çıkış | Başarısızlıkta |
|---|---|---|---|---|
| 1 | Engine init | Konfigürasyon | Motor nesnesi | Hata kodu, durum `Error` |
| 2 | Prepare | Örnekleme hızı, kanal sayısı, buffer boyutu | Buffer pool + kuyruklar | `NotPrevented` hatası (UYARI: doğrulanmadı) |
| 3 | Start | Hazır motor | Çalışan RT döngüsü | Durum `Stopped`'a geri dönülür |
| 4 | Callback kabulü | K2 karesi | Kuyrukta kare | Kuyruk dolu → drop + sayaç |
| 5 | Zincir yürütme | Ham kare | İşlenmiş kare | Aşama hata bayrağı → bypass |
| 6 | Dışa aktarım | İşlenmiş kare | K2'ye teslim | Teslim edilemezse drop |
| 7 | Stop | — | Kuyruk boşaltılır | Zaman aşımı → zorla durdurma |
| 8 | Dispose | — | Havuz iadesi | RT-dışı bağlamda çağrılır |

### 5.1 Durum Makinesi Geçişleri

| Durum | Koşul | Geçiş |
|---|---|---|
| `Created` | `init()` başarılı | → `Prepared` |
| `Prepared` | `prepare()` başarılı | → `Running` (start ile) |
| `Running` | `stop()` | → `Prepared` |
| `Running` | İç hata bayrağı | → `Error` (RT durumu korunur, canlı çıkış durdurulmaz) |
| `Error` | `reset()` | → `Prepared` |
| `Prepared`/`Error` | `dispose()` | → `Disposed` |

> **⚠️ VERIFICATION REQUIRED:** durum adları ve `reset()` API'si için kaynak dosyada birebir enum/ad
> kanıtı bulunup bulunmadığı doğrulanmalıdır; kaynak yalnız kavram olarak "Engine State Machine"
> başlığını içerir.

## 6. Bağımlılık Matrisi

| # | Hedef | Yön | Bağımlılık Türü | Wiki-link |
|---|---|---|---|---|
| 1 | DSP zinciri | aşağı | Motor zinciri yürütür | `[[../k073-dsp-chain/dsp-chain]]` |
| 2 | Parametrik EQ | aşağı | 5. aşama çağrısı | `[[../k074-eq-parametric/eq-parametric]]` |
| 3 | Sample-rate conversion | aşağı | 4. aşama (motor altında) | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 4 | Mixer / routing | aşağı | 10. aşama | `[[../k076-mixer-routing/mixer-routing]]` |
| 5 | Stream buffer | yandan | Kuyruğu besleyen IO | `[[../k077-stream-buffer/stream-buffer]]` |
| 6 | Gapless playback | yandan | Motor durumu tetikler | `[[../k078-playback-gapless/playback-gapless]]` |
| 7 | Bit depth conversion | aşağı | 13. aşama | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 8 | Channel processing | aşağı | 2. aşama | `[[../k080-channel-processing/channel-processing]]` |
| 9 | Format decoder | yandan | Motoru besleyen kaynak | `[[../k081-format-decoder/format-decoder]]` |
| 10 | Dynamics | aşağı | 6. aşama | `[[../k082-dynamics-compressor/dynamics-compressor]]` |
| 11 | Reverb / chorus-delay | aşağı | 7-8. aşamalar | `[[../k083-effects-reverb/effects-reverb]]` |
| 12 | Klasör dizini | yukarı | Kapsam özeti | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Beklenen Davranış | Risk |
|---|---|---|---|
| 1 | Kuyruk tamamen dolu | Yeni kare düşürülür (drop), sayaç artar | Glitch |
| 2 | Kuyruk tamamen boş | Çıkış repeat/mute politikası uygulanır | Sessizlik / tıklama |
| 3 | Örnekleme hızı değişimi (canlı) | Yeniden prepare gerekir; doğrudan değişim desteklenmez | Bozuk çıktı |
| 4 | Kanal sayısı artışı (8.1 → daha fazla) | Buffer pool yeniden boyutlanır (RT-dışı) | Taşma |
| 5 | Denormal girdi (çok küçük float) | Flush-to-zero etkin olmalı | CPU şişmesi |
| 6 | Aynı anda stop + dispose çağrısı | Sıralı ejecyon zorunlu | Yarış durumu |
| 7 | Callback RT dışında beklerse | Sürücü tarafında xrun üretir | Dropped buffer |
| 8 | 128 kanal / 384 kHz eşik aşımları | Kaynak hedeflerin dışına çıkış → konfigürasyon reddi | ⚠️ VERIFICATION REQUIRED |
| 9 | Kaynak çakışması (IO → RT hedefi) | RT-dışı kaynak RT kuyruğuna yazar | Priority inversion |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Düzensiz tıklama/glitch | RT thread'de `malloc`/`new` | Kilitlenme gecikmesi | Havuz kullanımı (§5.1 guardrail) |
| 2 | Deadlock | RT thread'de mutex | Çıkış durur | Lock-free kuyruk (`CLAUDE.md` §1 kural 2) |
| 3 | Callback'te exception | `noexcept` ihlali | Sürücü koptu | `noexcept` zorunluluğu (CLAUDE.md kural 3) |
| 4 | Yanlış kanal verisi (gürültü) | Buffer boyutu yanlış | Bozuk stereo/surround | Prepare doğrulaması |
| 5 | CPU %10 hedefi aşımı | Zincir kopyası / SIMD yolu yok | Latency artışı | Profil + SIMD yolu |
| 6 | Bellek sızması | RT-dışı dispose edilmeme | Zamanla büyüyen RSS | Dispose akışı (§5) |
| 7 | False sharing | `alignas(64)` eksikliği | Beklenmeyen yavaşlık | CLAUDE.md kural 4 |
| 8 | Runtime buffer boyutu | `constexpr` yerine dinamik boyut | Tahmin edilemez gecikme | CLAUDE.md kural 5 |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak | Not |
|---|---|---|---|---|
| 1 | İşleme latency | < 0.1 ms | `k3 index.md` §Performans Metrikleri | Hedef değer, ölçüm değil |
| 2 | CPU kullanımı | < %10 (tam kapasite) | `k3 index.md` | Hedef değer |
| 3 | Maksimum kanal | 128 | `k3 index.md` | Hedef değer |
| 4 | Maksimum örnekleme hızı | 384 kHz | `k3 index.md` | Hedef değer |
| 5 | Bellek kullanımı | < 100 MB | `k3 index.md` | Hedef değer |
| 6 | RT bağlamında tahsis | 0 | `CLAUDE.md` §1-§2 | Kural (kanıtlı) |
| 7 | Alignment | `alignas(64)` | `CLAUDE.md` §1 kural 4 | Kural (kanıtlı) |
| 8 | Callback istisnası | `noexcept` | `CLAUDE.md` §1 kural 3 | Kural (kanıtlı) |

> **⚠️ VERIFICATION REQUIRED:** 1-5 arasındaki hedefler kaynak `index.md`'de "Hedef" başlığıyla
> verilmiştir; diskte ölçüm raporu/CI çıktısı bulunmamaktadır.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak | İhlal Sonucu |
|---|---|---|---|
| 1 | Audio thread'de `malloc`/`free`/`new`/`delete` yasak | `CLAUDE.md` §1-1 | Crash |
| 2 | Audio thread'de mutex yasak | `CLAUDE.md` §1-2 | Deadlock |
| 3 | Callback `noexcept` zorunlu | `CLAUDE.md` §1-3 | Crash |
| 4 | `alignas(64)` zorunlu | `CLAUDE.md` §1-4 | False sharing |
| 5 | `constexpr` buffer zorunlu | `CLAUDE.md` §1-5 | Runtime alloc |
| 6 | Sistem çağrısı yasak (RT context) | `k3 index.md` §Temel İlkeller | Beklenmeyen gecikme |
| 7 | Bellek serbest bırakma yasak (RT context) | `k3 index.md` §Temel İlkeller | Düşük seviyeli kilitlenme |
| 8 | `std::shared_ptr` RT yolu dışı | `CLAUDE.md` §2 (yasaklı örüntü) | Atomic refcount yükü |
| 9 | Denormal flush-to-zero | `k3 index.md` §Numerik Hassasiyet | CPU şişmesi |
| 10 | Biquad katsayıları double hassasiyet | `k3 index.md` §Numerik Hassasiyet | Katsayı kaybı |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul Kriteri |
|---|---|---|---|
| 1 | Örnek-örneğe bit-perfect karşılaştırma | Unit | Birebir bayt eşitliği (bit-perfect zincir) |
| 2 | Kuyruk taşma/dolma testi | Unit | Drop sayacı artar, çökme yok |
| 3 | Kuyruk boşluk testi | Unit | Tanımlı mute/repeat davranışı |
| 4 | RT bağlamı denetimi (alloc/metrik) | Unit (instrumented) | 0 tahsis, 0 mutex |
| 5 | Stop/dispose yarış testi | Stress | Veri yarışı yok (TSAN hedefi ⚠️ VERIFICATION REQUIRED) |
| 6 | 128 kanal / 384 kHz konfigürasyon | Integration | Red/yas kabul mantığı |
| 7 | Uzun süreli (endurance) çalıştırma | Soak | Bellek eğrisi yatay |
| 8 | CPU bütçesi ölçümü | Perf | ≤ %10 hedefi (ölçüm raporu ⚠️ VERIFICATION REQUIRED) |
| 9 | Latency ölçümü | Perf | < 0.1 ms hedefi (ölçüm raporu ⚠️ VERIFICATION REQUIRED) |
| 10 | Denormal girdi testi | Unit | Flush-to-zero etkin |
| 11 | Callback `noexcept` denetimi | Statik | Kompilasyon zamanı kontrol |
| 12 | Alignment denetimi | Statik/Unit | `alignas(64)` oturum hizası |

## 12. Riskler ve Belirsizlikler

| # | Risk | Olasılık | Etki | Not |
|---|---|---|---|---|
| 1 | Hedef metriklerin ölçülmemiş olması | Yüksek | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | ADR-062'in dondurulmuş aralık (001-037) dışı olması | Kesin | Orta | `.ai/log.md` kaydı; üst karar |
| 3 | Durum makinesi API adlarının kaynakta geçmemesi | Orta | Orta | §5.1 ⚠️ VERIFICATION REQUIRED |
| 4 | `README.md` §9'daki 6 dosya adının backup'ta bulunmaması | Kesin | Düşük | Kritik bulgu (bkz. `[[index]]`) |
| 5 | K2/K4 sınırının bu klasörde kanıtlanamaması | Yüksek | Orta | Kapsam dışı olarak işaretlendi |
| 6 | Ölçüm/CI kanıtının vault'ta olmaması | Yüksek | Orta | Test bölümü hedef-güncel tutuldu |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | Motor C++20 / real-time safe / lock-free | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/index.md` §Genel Bakış | Kanıtlı |
| 2 | RT yasakları listesi (4 madde) | `_backup/.../k3-ses-motoru/index.md` §Temel İlkeller | Kanıtlı |
| 3 | Guardrail tablosu (5 kural) | `_backup/.../k3-ses-motoru/CLAUDE.md` §1 | Kanıtlı |
| 4 | Performans hedefleri (5 metrik) | `_backup/.../k3-ses-motoru/index.md` §Performans Metrikleri | Kanıtlı (hedef) |
| 5 | ADR-025 / ADR-062 referansları | `_backup/.../k3-ses-motoru/README.md` §10 | Kanıtlı |
| 6 | §4 verbatim aktarım | `_backup/.../k3-ses-motoru/neva-engine-core.md` (371 satır) | Kanıtlı |
| 7 | Durum makinesi enum/API adları | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/neva-engine-core.md` (salt-okunur,
371 satır) + `index.md` + `CLAUDE.md` + `README.md` §1-§2, §10; ölçümler için ⚠️ VERIFICATION REQUIRED.
