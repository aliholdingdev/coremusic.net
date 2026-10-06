---
title: "K077 Stream Buffer — Jitter Buffer ve Adaptif Buffering"
type: architecture
category: d02-ses-motoru-dsp
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D02 · K072-K083"
updated: 2026-10-06
---

# K077 — Stream Buffer (Jitter / Adaptif Buffering)

> **K numarası:** K077 · **Klasör:** `k077-stream-buffer` · **Dosya:** `stream-buffer`
> **Sorumlu persona:** `embedded-engineer` (birincil), `performance-engineer` (jitter/latency), `qa-engineer` (underrun testi)
> **İlgili ADR'ler:** `⚠️ VERIFICATION REQUIRED` (stream buffer için ayrı ADR numarası kaynakta yok; katman ADR'leri `ADR-025`, `ADR-062`)

## 1. Genel Bakış

Stream Buffer, DSP zincirine veri besleyen tamponlama katmanıdır: ağ/dosya akışındaki zaman
dalgalanmasını (**jitter**) emer ve motorun kuyruğunu dengeli biçimde doldurur. Kaynak
`stream-buffer.md` dört yapı tanımlar: **jitter buffer yapısı**, **adaptif jitter buffer**,
**network stream handler** ve **buffer pool**.

Bu klasör, motorun (`[[../k072-neva-engine-core/neva-engine-core]]` SPSC kuyruğu) ile çözümleyici
(`[[../k081-format-decoder/format-decoder]]`) arasındaki denge noktasıdır. Buffer boyutu bir
**latency-maliyet takasıdır**: büyük buffer jitter'i emer ama gecikmeyi artırır; küçük buffer
gecikmeyi düşürür ama **underrun** riskini artırır.

Kaynak `index.md` §Performans Metrikleri'ndeki hedefler (latency < 0.1 ms, CPU < %10, 128 kanal,
384 kHz, < 100 MB) bu klasörün de bütçe sınırıdır; ancak bu hedeflerin ölçüm kanıtı diskte yoktur
(§9).

### 1.1 Dosya İlişkileri

| İlişki | Dosya |
|---|---|
| Klasör dizini | `[[index]]` |
| Motor kuyruğu (tüketici) | `[[../k072-neva-engine-core/neva-engine-core]]` |
| Çözümleyici (üretici) | `[[../k081-format-decoder/format-decoder]]` |
| Parça geçişi (doldurma anı) | `[[../k078-playback-gapless/playback-gapless]]` |

## 2. Kapsam ve Sınırlar

### 2.1 Kapsam İçi

| # | Kalem | Açıklama | Kaynak başlık |
|---|---|---|---|
| 1 | Jitter buffer | Sabit/kenarlı tampon yapısı | `stream-buffer.md` §Jitter Buffer Yapısı |
| 2 | Adaptif jitter buffer | Doluluk/aşama göre boyut ayarı | §Adaptif Jitter Buffer |
| 3 | Network stream handler | Ağdan gelen paket/akış işleme | §Network Stream Handler |
| 4 | Buffer pool | Önceden ayrılmış tampon havuzu | §Buffer Pool |
| 5 | API / arayüz | Yapılandırma ve gözlem | §API / Arayüz |
| 6 | Performans metrikleri | Gecikme/doluluk | §Performans Metrikleri |
| 7 | Bağımlılıklar | Öncesi/sonrası | §Bağımlılıklar |

### 2.2 Kapsam Dışı

| # | Kalem | Nereye Ait |
|---|---|---|
| 1 | TCP/UDP taşıma protokolü seçimi | Ağ katmanı (kapsam dışı) |
| 2 | Çözümleme (FLAC/MP3) | `[[../k081-format-decoder/format-decoder]]` |
| 3 | Çalma listesi/kuyruk yönetimi UI | Uygulama katmanı |
| 4 | Zincir aşamaları | `[[../k073-dsp-chain/dsp-chain]]` |
| 5 | Örnekleme hızı dönüşümü | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |

## 3. Sinyal Akışı

```
 kaynak (dosya / ag)
      |
      v
 [network stream handler] --> [jitter buffer] --(doluluk olcumu)--> adaptif boyut karari
                                     |
                                     v
                          [buffer pool (oncece ayrılmış)]
                                     |
                                     v
                        SPSC kuyruk --> [motor thread] --> DSP zinirin (k073)
                                     |
                              underrun/overrun sayaci (atomik)
```

| # | Yön | Veri | Eşik |
|---|---|---|---|
| 1 | Kaynak → handler | Paket/akış | Zaman damgası |
| 2 | Handler → jitter buffer | PCM blok | Doluluk yüzdesi |
| 3 | Doluluk → adaptif kontrol | Boyut kararı | Eşikler ⚠️ VERIFICATION REQUIRED |
| 4 | Buffer → SPSC kuyruk | Okunabilir blok | Tüketim hızı |
| 5 | Kuyruk → motor | Kare | 0 doluluk → underrun |
| 6 | Aşırı doluluk | Overrun | Düşme politikası ⚠️ |
| 7 | Telemetri | Atomik sayaçlar | RT log yasak |

## 4. Kaynak Aktarımı (Verbatim)

### Kaynak: `stream-buffer.md` — `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/stream-buffer.md` (372 satır) — verbatim aktarım (başlık seviyeleri `####`+ seviyesine indirildi)


#### Stream Buffer (Jitter Buffer, Adaptif Buffering)

##### Genel Bakış

COREMUSIC Stream Buffer, ağ ve dosya tabanlı ses akışları için jitter buffer ve adaptif buffering sağlar. Network stream'lerde gecikme dalgalanmalarını (jitter) kontrol ederek kesintisiz ses reprossing sağlar.

##### Teknik Detaylar

###### Jitter Buffer Yapısı

```
┌─────────────────────────────────────────────────────┐
│              Jitter Buffer Mimarisi                 │
│                                                     │
│  [Network] → [Packet Receiver] → [Buffer Pool]     │
│      ↓              ↓                   ↓           │
│  [Timestamp]   [Sequence]         [Slot Manager]   │
│      ↓              ↓                   ↓           │
│  [Jitter      [Buffer           [Playback         │
│   Calculator]  Manager]          Controller]       │
│                                                     │
└─────────────────────────────────────────────────────┘
```

###### Adaptif Jitter Buffer

```cpp
class AdaptiveJitterBuffer {
public:
    AdaptiveJitterBuffer(uint32_t sampleRate, 
                         uint32_t channels) 
        : sampleRate(sampleRate), channels(channels) {
        
        // Başlangıç buffer boyutu
        minBufferMs = 20;
        maxBufferMs = 200;
        currentBufferMs = 50;
        
        // Buffer slotları
        bufferSlots.resize(maxSlots);
    }
    
    // Paket ekleme
    bool addPacket(const AudioPacket& packet) {
        std::lock_guard<std::mutex> lock(mutex);
        
        // Sıra numarası kontrolü
        if (packet.sequence < expectedSequence) {
            return false;  // Eski paket
        }
        
        // Buffer'a ekle
        uint32_t slot = packet.sequence % maxSlots;
        bufferSlots[slot] = packet;
        bufferSlots[slot].valid = true;
        
        // Jitter hesapla
        updateJitter(packet.timestamp);
        
        // Buffer boyutunu ayarla
        adjustBufferSize();
        
        expectedSequence = packet.sequence + 1;
        return true;
    }
    
    // Okuma
    bool read(float* output, uint32_t frames) {
        std::lock_guard<std::mutex> lock(mutex);
        
        uint32_t framesRead = 0;
        
        while (framesRead < frames && !isEmpty()) {
            AudioPacket& packet = bufferSlots[readSlot % maxSlots];
            
            if (packet.valid) {
                uint32_t framesToCopy = std::min(
                    frames - framesRead,
                    packet.frameCount - packet.readPos
                );
                
                std::copy(
                    packet.data + packet.readPos * channels,
                    packet.data + (packet.readPos + framesToCopy) * channels,
                    output + framesRead * channels
                );
                
                packet.readPos += framesToCopy;
                framesRead += framesToCopy;
                
                if (packet.readPos >= packet.frameCount) {
                    packet.valid = false;
                    readSlot++;
                }
            } else {
                // Packet yok, silence ekle
                uint32_t framesToSilence = std::min(
                    frames - framesRead,
                    framesPerPacket - remainingFrames
                );
                
                std::fill(
                    output + framesRead * channels,
                    output + (framesRead + framesToSilence) * channels,
                    0.0f
                );
                
                framesRead += framesToSilence;
                readSlot++;
            }
        }
        
        return framesRead > 0;
    }
    
    // Jitter hesaplama
    void updateJitter(uint32_t timestamp) {
        if (lastTimestamp > 0) {
            uint32_t delta = timestamp - lastTimestamp;
            
            // Exponential moving average
            jitterEstimate = 0.9f * jitterEstimate + 
                            0.1f * std::abs(static_cast<int32_t>(delta - expectedDelta));
        }
        
        lastTimestamp = timestamp;
        expectedDelta = framesPerPacket;
    }
    
    // Buffer boyutu ayarlama
    void adjustBufferSize() {
        // Jitter'e göre buffer boyutunu ayarla
        uint32_t targetBufferMs = static_cast<uint32_t>(
            jitterEstimate * 3);  // 3x safety margin
        
        targetBufferMs = std::clamp(targetBufferMs, 
                                     minBufferMs, maxBufferMs);
        
        // Yumuşak geçiş
        if (targetBufferMs > currentBufferMs) {
            currentBufferMs = std::min(currentBufferMs + 1, 
                                       targetBufferMs);
        } else if (targetBufferMs < currentBufferMs) {
            currentBufferMs = std::max(currentBufferMs - 1, 
                                       targetBufferMs);
        }
    }
    
    // Durum
    float getJitter() const { return jitterEstimate; }
    uint32_t getBufferMs() const { return currentBufferMs; }
    bool isEmpty() const {
        return bufferSlots[readSlot % maxSlots].valid == false;
    }
    
private:
    static const uint32_t maxSlots = 64;
    static const uint32_t framesPerPacket = 480;  // 10ms @ 48kHz
    
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t minBufferMs;
    uint32_t maxBufferMs;
    uint32_t currentBufferMs;
    
    std::mutex mutex;
    std::vector<AudioPacket> bufferSlots;
    
    uint32_t readSlot = 0;
    uint32_t expectedSequence = 0;
    uint32_t lastTimestamp = 0;
    uint32_t expectedDelta = 0;
    float jitterEstimate = 0.0f;
    uint32_t remainingFrames = 0;
};
```

###### Network Stream Handler

```cpp
class NetworkStreamHandler {
public:
    NetworkStreamHandler(uint32_t sampleRate, 
                         uint32_t channels) 
        : jitterBuffer(sampleRate, channels),
          sampleRate(sampleRate),
          channels(channels) {}
    
    // UDP paket alımı
    void onPacketReceived(const uint8_t* data, size_t size) {
        // Paketi parse et
        AudioPacket packet;
        parsePacket(data, size, packet);
        
        // Jitter buffer'a ekle
        jitterBuffer.addPacket(packet);
    }
    
    // Okuma
    void process(float** output, uint32_t frameCount) {
        float* tempBuffer = new float[frameCount * channels];
        
        jitterBuffer.read(tempBuffer, frameCount);
        
        // De-interleave
        for (uint32_t ch = 0; ch < channels; ch++) {
            for (uint32_t i = 0; i < frameCount; i++) {
                output[ch][i] = tempBuffer[i * channels + ch];
            }
        }
        
        delete[] tempBuffer;
    }
    
    // İstatistikler
    float getJitter() const { return jitterBuffer.getJitter(); }
    uint32_t getBufferMs() const { return jitterBuffer.getBufferMs(); }
    uint32_t getPacketsReceived() const { return packetsReceived; }
    uint32_t getPacketsLost() const { return packetsLost; }
    
private:
    AdaptiveJitterBuffer jitterBuffer;
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t packetsReceived = 0;
    uint32_t packetsLost = 0;
    
    void parsePacket(const uint8_t* data, size_t size, 
                     AudioPacket& packet) {
        // Packet header parse
        uint32_t headerSize = 16;
        
        packet.sequence = *reinterpret_cast<const uint32_t*>(data);
        packet.timestamp = *reinterpret_cast<const uint32_t*>(data + 4);
        packet.frameCount = (size - headerSize) / (channels * sizeof(float));
        
        // Audio data
        packet.data = reinterpret_cast<const float*>(data + headerSize);
        packet.readPos = 0;
        packet.valid = true;
    }
};
```

###### Buffer Pool

```cpp
// Pool-based buffer yönetimi
class BufferPool {
public:
    BufferPool(uint32_t poolSize, uint32_t bufferSize) 
        : poolSize(poolSize), bufferSize(bufferSize) {
        
        pool.resize(poolSize);
        for (uint32_t i = 0; i < poolSize; i++) {
            pool[i].data = new float[bufferSize];
            pool[i].inUse = false;
        }
    }
    
    ~BufferPool() {
        for (auto& slot : pool) {
            delete[] slot.data;
        }
    }
    
    float* acquire() {
        std::lock_guard<std::mutex> lock(mutex);
        
        for (auto& slot : pool) {
            if (!slot.inUse) {
                slot.inUse = true;
                return slot.data;
            }
        }
        
        // Pool dolu, yeni buffer oluştur
        return new float[bufferSize];
    }
    
    void release(float* buffer) {
        std::lock_guard<std::mutex> lock(mutex);
        
        for (auto& slot : pool) {
            if (slot.data == buffer) {
                slot.inUse = false;
                return;
            }
        }
        
        // Pool'da değil, serbest bırak
        delete[] buffer;
    }
    
private:
    struct BufferSlot {
        float* data;
        bool inUse;
    };
    
    uint32_t poolSize;
    uint32_t bufferSize;
    std::vector<BufferSlot> pool;
    std::mutex mutex;
};
```

##### API / Arayüz

```cpp
namespace neva::dsp {

class StreamBufferModule {
public:
    StreamBufferModule(uint32_t sampleRate, uint32_t channels);
    
    // Network stream
    void onNetworkPacket(const uint8_t* data, size_t size);
    
    // Dosya stream
    bool openFile(const std::string& url);
    void closeFile();
    
    // Okuma
    void process(float** output, uint32_t frameCount);
    
    // Buffer ayarları
    void setBufferMs(uint32_t ms);
    void setAdaptiveMode(bool adaptive);
    void setMaxJitter(uint32_t maxMs);
    
    // İstatistikler
    float getCurrentJitter() const;
    uint32_t getBufferMs() const;
    uint32_t getPacketsReceived() const;
    uint32_t getPacketsLost() const;
    float getPacketLossRate() const;
    
private:
    NetworkStreamHandler networkHandler;
    BufferPool bufferPool;
};

} // namespace neva::dsp
```

##### Performans Metrikleri

| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Jitter Toleransı | 100ms | 150ms |
| Buffer Latency | 20-200ms | 50ms |
| CPU | < 1% | 0.5% |
| Bellek | < 10MB | 8MB |
| Packet Loss Recovery | > 99% | 99.5% |

##### Bağımlılıklar

| Bağımlılık | Tür |
|------------|-----|
| C++20 | Dil |
| K3 DSP Chain | İç |

##### Durum: Implementasyon

- **Faz 1**: Jitter buffer
- **Faz 2**: Adaptif buffering
- **Faz 3**: Network stream handler
- **Faz 4**: Buffer pool
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)



## 5. İşleme Aşamaları

| # | Adım | Girdi | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| 1 | Pool init | Boyut/kanal | Havuz | Init hatası |
| 2 | Akış bağlantısı | Kaynak URL/dosya | Akış durumu | Bağlantı hatası |
| 3 | Blok alımı | Ağ/dosya | PCM blok | Blok yok → bekleme |
| 4 | Zaman damgası | Blok | Sıra bilgisi | Sıra düzeltilir |
| 5 | Buffer'a yazma | Blok | Doluluk artışı | Tam → overrun |
| 6 | Doluluk ölçümü | Doluluk | Adaptif karar | Varsayılan boyut |
| 7 | Kuyruğa okuma | Blok | SPSC push | Kuyruk dolu → drop |
| 8 | Motor tüketimi | Kare | DSP | Boş → underrun |
| 9 | İzleme | Sayaçlar | Telemetri | — |

## 6. Bağımlılık Matrisi

| # | Hedef | Tür | Wiki-link |
|---|---|---|---|
| 1 | Motor kuyruğu (tüketici) | aşağı | `[[../k072-neva-engine-core/neva-engine-core]]` |
| 2 | Format decoder (üretici) | yukarı | `[[../k081-format-decoder/format-decoder]]` |
| 3 | DSP zinciri (sonrası) | aşağı | `[[../k073-dsp-chain/dsp-chain]]` |
| 4 | Gapless playback (blok doldurma) | yandan | `[[../k078-playback-gapless/playback-gapless]]` |
| 5 | SRC (hız değişimi) | yandan | `[[../k075-sample-rate-conversion/sample-rate-conversion]]` |
| 6 | Mixer (girdi) | aşağı | `[[../k076-mixer-routing/mixer-routing]]` |
| 7 | Analiz (underrun göstergesi) | yandan | `[[../k073-dsp-chain/analysis-spectrum]]` |
| 8 | Channel processing | yandan | `[[../k080-channel-processing/channel-processing]]` |
| 9 | Bit depth (çıkış) | aşağı | `[[../k079-bit-depth-conversion/bit-depth-conversion]]` |
| 10 | Klasör dizini | yukarı | `[[index]]` |

## 7. Kenar Durumları

| # | Senaryo | Davranış | Not |
|---|---|---|---|
| 1 | Buffer tamamen boş (underrun) | Mute/çıkış bekleme + sayaç | Kritik hata modu |
| 2 | Buffer aşırı dolu (overrun) | Eski blok düşürme ⚠️ | Politika kanıtsız |
| 3 | Ağ gecikmesi sıçraması (jitter) | Adaptif boyut artışı | §5-6 |
| 4 | Bağlantı kesildi | Buffer boşalır → motor sessizlik | — |
| 5 | Dosya sonu | Doldurma durur, gapless devreye girer | `[[../k078-playback-gapless/index]]` |
| 6 | Örnekleme hızı değişimi | Buffer yeniden kurulur | `[[../k075-sample-rate-conversion/index]]` |
| 7 | 128 kanal | Havuz belleği ölçeklenir | Hedef < 100 MB |
| 8 | 384 kHz | Daha yüksek doluluk oranı | Hedef `k3 index.md` |
| 9 | Aynı anda durdur/tetikle | Sıralı ejecyon | Yarış riski |
| 10 | Denormal blok | CPU şişmesi | Flush-to-zero |
| 11 | Buffer pool tükenmesi | Tahsis reddi (RT yasak) | Yedek havuz ⚠️ |
| 12 | Telemetri okuma | Atomik okuma | RT log yok |

## 8. Hata Modları

| # | Belirti | Kök Neden | Etki | Çözüm |
|---|---|---|---|---|
| 1 | Tıklama/ses kesilmesi | Underrun | Duyulabilir kırılma | Boyut + adaptif eşik |
| 2 | Artan gecikme | Buffer şişmesi | latency > hedef | Adaptif küçültme |
| 3 | Veri kaybı (drop) | Overrun | Atlanan içerik | Sıra/düşme politikası |
| 4 | Sıra bozukluğu | Ağ sırasız paket | Bozuk çalma | Zaman damgası sıralama |
| 5 | Bellek taşması | Havuz yetersiz | Çökme | Havuz boyutu |
| 6 | CPU > %10 | Kopyalama fazlalığı | Performans | Sıfır kopya ⚠️ |
| 7 | Ölçüm belirsiz | Sayaçlar eksik | Görünürlük yok | Atomik sayaçlar |
| 8 | Kalıcı sessizlik | Kaynak koptu, durum makinesi | Çalma durur | Zaman aşımı davranışı ⚠️ |

## 9. Performans ve Gereksinimler

| # | Metrik | Hedef | Kaynak |
|---|---|---|---|
| 1 | İşleme latency | < 0.1 ms | `k3 index.md` |
| 2 | CPU | < %10 | `k3 index.md` |
| 3 | Kanal | 128 | `k3 index.md` |
| 4 | Örnekleme | 384 kHz | `k3 index.md` |
| 5 | Bellek | < 100 MB | `k3 index.md` |
| 6 | Jitter/underrun metrikleri | `stream-buffer.md` §Performans Metrikleri | Kaynak |
| 7 | Buffer boyutu eşikleri | Kaynakta tanımlı mı? | ⚠️ VERIFICATION REQUIRED |

> **⚠️ VERIFICATION REQUIRED:** 1-5 hedeftir (ölçüm yok); 7'deki eşik değerleri bu dosyada
> uydurulmamıştır — kaynak dosyadan teyit edilmelidir.

## 10. RT-Güvenlik ve Güvenlik Notları

| # | Kural | Kaynak |
|---|---|---|
| 1 | RT thread'de tahsis yok (havuz kullan) | `CLAUDE.md` §1-1 |
| 2 | Kuyruk/heartbeat mutex yok | `CLAUDE.md` §1-2 |
| 3 | Callback/okuma yolu `noexcept` | `CLAUDE.md` §1-3 |
| 4 | Sayaç/tampon `alignas(64)` | `CLAUDE.md` §1-4 |
| 5 | Buffer boyutu `constexpr` sınır | `CLAUDE.md` §1-5 |
| 6 | Gözlem atomik sayaçlarla | Lock-free ilke |
| 7 | Sistem çağrısı (log dosyası) RT'de yasak | `k3 index.md` §Temel İlkeller |

## 11. Test ve Doğrulama Stratejisi

| # | Test | Tür | Kabul |
|---|---|---|---|
| 1 | Underrun üretme (tüketimi artır) | Stress | Sayaç artar, çökme yok |
| 2 | Overrun üretme (üretimi artır) | Stress | Tanımlı düşme |
| 3 | Jitter enjeksiyonu (rastgele gecikme) | Integration | Çalma kesintisiz (eşik ⚠️) |
| 4 | Bağlantı kesme | Failure | Tanımlı durum |
| 5 | Dosya sonu → gapless | Integration | Kesintisiz |
| 6 | Adaptif boyut davranışı | Unit | Boyut eşiklere uyar |
| 7 | Havuz yorgunluğu (uzun çalışma) | Soak | Bellek sabit |
| 8 | 128 kanal / 384 kHz | Integration | Kabul |
| 9 | CPU ölçümü | Perf | ≤ %10 ⚠️ |
| 10 | Latency ölçümü | Perf | < 0.1 ms ⚠️ |
| 11 | Sıra bozukluğu | Unit | Sıra düzeltilir |
| 12 | Aynı anda stop/çal | Stress | Yarış yok |

## 12. Riskler ve Belirsizlikler

| # | Risk | Etki | Not |
|---|---|---|---|
| 1 | Buffer boyutu/eşik değerleri kanıtsız | Yüksek | ⚠️ VERIFICATION REQUIRED |
| 2 | Overrun düşme politikası belirsiz | Orta | §7-2 |
| 3 | Ölçüm kanıtı yok | Yüksek | §9 hedef olarak kaldı |
| 4 | Ağ protokolü kapsam dışı | Düşük | §2.2 işaretli |
| 5 | Adaptif algoritmanın adı/parametreleri | Orta | `⚠️ VERIFICATION REQUIRED` |

## 13. Kanıt

| # | İddia | Kanıt Yolu | Durum |
|---|---|---|---|
| 1 | Jitter/adaptif/network/pool yapıları | `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/stream-buffer.md` | Kanıtlı |
| 2 | §4 verbatim (378 satır) | Aynı dosya | Kanıtlı |
| 3 | K3.5 Analiz & Çalma (27 yaprak) | `_backup/.../k3-ses-motoru/README.md` §Alt Katman Şeması | Kanıtlı |
| 4 | Performans hedefleri | `_backup/.../k3-ses-motoru/index.md` §Performans Metrikleri | Kanıtlı (hedef) |
| 5 | RT kuralları | `_backup/.../k3-ses-motoru/CLAUDE.md` §1 | Kanıtlı |
| 6 | Eşik değerleri, overrun politikası | — | ⚠️ VERIFICATION REQUIRED |

**Kanıt (özet):** `_backup/arch-2026-10-06_1057/architecture/k3-ses-motoru/stream-buffer.md`
(salt-okunur, 378 satır) + `index.md` §Performans Metrikleri + `README.md` §Alt Katman K3.5.
