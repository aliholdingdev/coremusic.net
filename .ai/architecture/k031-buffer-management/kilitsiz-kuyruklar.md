---
title: "Kilitsiz Kuyruklar - k031-buffer-management"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Kilitsiz Kuyruklar

> Klasör: `k031-buffer-management` · Dilim: D01 (k018–k035) · Dosya: `kilitsiz-kuyruklar.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Lock-free SPSC/MPMC kuyruklar, bellek bariyerleri ve ölçümler.

Bu belge; D01 diliminin (Buffer Yönetimi) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Lock-free SPSC/MPMC kuyruklar, bellek bariyerleri ve ölçümler.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Buffer Yönetimi)
- **Çapraz referanslar:** [[../k032-latency-optimization/latency-optimization.md]] · [[../k024-ipc-mekanizmalari/ipc-mekanizmalari.md]] · [[../k026-memory-management/memory-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### buffer-management.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` (381 satır)

#### buffer-management.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § (giriş) — L1–L9

---
title: "Buffer Yönetimi"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### Buffer Yönetimi


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Genel Bakış` — L10–L12


COREMUSIC buffer yönetimi, ses verilerinin donanım ile yazılım arasında verimli ve düşük gecikmeli transferini sağlar. Ring buffer, double buffering ve lock-free queue'lar kullanılarak gerçek zamanlı performans elde edilir.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Teknik Detaylar` — L14–L301


##### Ring Buffer Yapısı

```
Ring Buffer Yapısı:
┌─────────────────────────────────────────────────────┐
│                                                     │
│  ┌───┬───┬───┬───┬───┬───┬───┬───┬───┬───┐       │
│  │ 0 │ 1 │ 2 │ 3 │ 4 │ 5 │ 6 │ 7 │ 8 │ 9 │       │
│  └───┴───┴───┴───┴───┴───┴───┴───┴───┴───┘       │
│    ↑                                       ↑       │
│   Read Pointer                      Write Pointer  │
│                                                     │
│  Read Pointer: okunacak bir sonraki konum          │
│  Write Pointer: yazılacak bir sonraki konum        │
│                                                     │
└─────────────────────────────────────────────────────┘
```

##### Lock-Free Ring Buffer

```cpp
// Lock-free ring buffer implementasyonu
template<typename T>
class LockFreeRingBuffer {
public:
    LockFreeRingBuffer(size_t capacity) 
        : capacity(capacity), buffer(new T[capacity]),
          readIndex(0), writeIndex(0) {}
    
    // Yazma (tek producer)
    bool write(const T* data, size_t count) {
        size_t currentWrite = writeIndex.load(std::memory_order_relaxed);
        size_t currentRead = readIndex.load(std::memory_order_acquire);
        
        size_t available = capacity - (currentWrite - currentRead);
        if (count > available) return false;
        
        // Veriyi kopyala
        for (size_t i = 0; i < count; i++) {
            buffer[(currentWrite + i) % capacity] = data[i];
        }
        
        writeIndex.store(currentWrite + count, 
                        std::memory_order_release);
        return true;
    }
    
    // Okuma (tek consumer)
    bool read(T* data, size_t count) {
        size_t currentRead = readIndex.load(std::memory_order_relaxed);
        size_t currentWrite = writeIndex.load(std::memory_order_acquire);
        
        size_t available = currentWrite - currentRead;
        if (count > available) return false;
        
        // Veriyi oku
        for (size_t i = 0; i < count; i++) {
            data[i] = buffer[(currentRead + i) % capacity];
        }
        
        readIndex.store(currentRead + count, 
                       std::memory_order_release);
        return true;
    }
    
    // Kullanılabilir veri miktarı
    size_t availableRead() const {
        return writeIndex.load(std::memory_order_acquire) - 
               readIndex.load(std::memory_order_acquire);
    }
    
    // Kullanılabilir alan miktarı
    size_t availableWrite() const {
        return capacity - availableRead();
    }
    
private:
    size_t capacity;
    std::unique_ptr<T[]> buffer;
    std::atomic<size_t> readIndex;
    std::atomic<size_t> writeIndex;
};
```

##### Double Buffering

Double buffering, kesintisiz ses akışı için kullanılır:

```
Double Buffering Akışı:
┌─────────────────────────────────────────────────────┐
│                                                     │
│  Zaman Dilimi 1:                                   │
│  ┌─────────────┐  ┌─────────────┐                  │
│  │  Buffer A    │  │  Buffer B    │                  │
│  │  (Okunuyor) │  │  (Yazılıyor)│                  │
│  └──────┬──────┘  └──────┬──────┘                  │
│         ↓                ↓                          │
│  [Donanım Input]  [Uygulama Output]                │
│                                                     │
│  Zaman Dilimi 2:                                   │
│  ┌─────────────┐  ┌─────────────┐                  │
│  │  Buffer A    │  │  Buffer B    │                  │
│  │  (Yazılıyor)│  │  (Okunuyor) │                  │
│  └──────┬──────┘  └──────┬──────┘                  │
│         ↓                ↓                          │
│  [Uygulama Output]  [Donanım Input]                │
│                                                     │
└─────────────────────────────────────────────────────┘
```

```cpp
// Double buffer implementasyonu
class DoubleBuffer {
public:
    DoubleBuffer(size_t frameSize, size_t channels)
        : bufferSize(frameSize * channels),
          bufferA(new float[bufferSize]),
          bufferB(new float[bufferSize]),
          activeBuffer(bufferA.get()),
          backBuffer(bufferB.get()) {}
    
    // Yazma (back buffer'a)
    void write(const float* data, size_t frames) {
        std::lock_guard<std::mutex> lock(mutex);
        size_t offset = writePos * channels;
        std::copy(data, data + frames * channels, 
                  backBuffer + offset);
        writePos += frames;
    }
    
    // Okuma (active buffer'dan)
    void read(float* data, size_t frames) {
        std::lock_guard<std::mutex> lock(mutex);
        size_t offset = readPos * channels;
        std::copy(activeBuffer + offset, 
                  activeBuffer + offset + frames * channels, 
                  data);
        readPos += frames;
    }
    
    // Buffer değişimi
    void swap() {
        std::lock_guard<std::mutex> lock(mutex);
        std::swap(activeBuffer, backBuffer);
        writePos = 0;
        readPos = 0;
    }
    
private:
    size_t bufferSize;
    size_t channels = 2;
    std::unique_ptr<float[]> bufferA;
    std::unique_ptr<float[]> bufferB;
    float* activeBuffer;
    float* backBuffer;
    size_t writePos = 0;
    size_t readPos = 0;
    std::mutex mutex;
};
```

##### SPSC Queue (Single Producer Single Consumer)

```cpp
// SPSC lock-free queue
template<typename T>
class SPSCQueue {
public:
    SPSCQueue(size_t capacity) 
        : capacity(capacity), buffer(new T[capacity]) {}
    
    bool push(const T& item) {
        size_t currentWrite = writePos.load(std::memory_order_relaxed);
        size_t nextWrite = (currentWrite + 1) % capacity;
        
        if (nextWrite == readPos.load(std::memory_order_acquire)) {
            return false; // Buffer dolu
        }
        
        buffer[currentWrite] = item;
        writePos.store(nextWrite, std::memory_order_release);
        return true;
    }
    
    bool pop(T& item) {
        size_t currentRead = readPos.load(std::memory_order_relaxed);
        
        if (currentRead == writePos.load(std::memory_order_acquire)) {
            return false; // Buffer boş
        }
        
        item = buffer[currentRead];
        readPos.store((currentRead + 1) % capacity, 
                     std::memory_order_release);
        return true;
    }
    
    size_t size() const {
        size_t w = writePos.load(std::memory_order_acquire);
        size_t r = readPos.load(std::memory_order_acquire);
        return (w - r + capacity) % capacity;
    }
    
private:
    size_t capacity;
    std::unique_ptr<T[]> buffer;
    std::atomic<size_t> writePos{0};
    std::atomic<size_t> readPos{0};
};
```

##### Buffer Boyut Optimizasyonu

```cpp
// Buffer boyutu hesaplama
struct BufferConfig {
    uint32_t sampleRate;
    uint32_t channels;
    uint32_t targetLatencyMs;
    uint32_t minBufferSize;
    uint32_t maxBufferSize;
};

uint32_t calculateOptimalBufferSize(const BufferConfig& config) {
    // Minimum buffer boyutu
    uint32_t minFrames = (config.sampleRate * config.targetLatencyMs) 
                         / 1000;
    
    // Güvenlik payı (%20)
    uint32_t safeFrames = minFrames * 1.2;
    
    // 2'nin kuvvetine yuvarla (performans için)
    uint32_t optimal = 1;
    while (optimal < safeFrames) optimal <<= 1;
    
    // Sınır kontrolü
    optimal = std::max(optimal, config.minBufferSize);
    optimal = std::min(optimal, config.maxBufferSize);
    
    return optimal;
}
```

##### Adapte Buffer Yönetimi

```cpp
// Adaptif buffer
class AdaptiveBuffer {
public:
    AdaptiveBuffer(size_t initialSize) 
        : currentSize(initialSize), targetSize(initialSize) {}
    
    void adjust(double currentJitter, double targetLatency) {
        // Jitter istatistiğini güncelle
        jitterHistory.push_back(currentJitter);
        if (jitterHistory.size() > 100) {
            jitterHistory.erase(jitterHistory.begin());
        }
        
        // Ortalama jitter hesapla
        double avgJitter = std::accumulate(
            jitterHistory.begin(), 
            jitterHistory.end(), 0.0) / jitterHistory.size();
        
        // Buffer boyutunu ayarla
        targetSize = static_cast<size_t>(
            (avgJitter * 2 + targetLatency) * sampleRate / 1000);
        
        // Yumuşak geçiş
        if (targetSize > currentSize) {
            currentSize = std::min(currentSize + 1, targetSize);
        } else if (targetSize < currentSize) {
            currentSize = std::max(currentSize - 1, targetSize);
        }
    }
    
    size_t getCurrentSize() const { return currentSize; }
    
private:
    size_t currentSize;
    size_t targetSize;
    uint32_t sampleRate = 96000;
    std::vector<double> jitterHistory;
};
```

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `API / Arayüz` — L303–L355


```cpp
class BufferManager {
public:
    BufferManager(const BufferConfig& config);
    
    // Ana buffer
    bool writeInput(const float* data, size_t frames);
    bool readOutput(float* data, size_t frames);
    void swapBuffers();
    
    // adapte
    void enableAdaptiveMode();
    void updateAdaptive(double jitter);
    
    // Ölçümler
    double getCurrentLatency() const;
    double getJitter() const;
    size_t getAvailableRead() const;
    size_t getAvailableWrite() const;
    
    // Bilgi
    BufferStats getStats() const;
};

struct BufferStats {
    size_t totalBuffers;
    size_t activeBuffers;
    double averageLatency;
    double maxJitter;
    uint64_t overflowCount;
    uint64_t underflowCount;
};

// Kullanım örneği
BufferConfig config;
config.sampleRate = 96000;
config.channels = 2;
config.targetLatencyMs = 1;
config.minBufferSize = 64;
config.maxBufferSize = 1024;

BufferManager bufferMgr(config);
bufferMgr.enableAdaptiveMode();

// İşleme döngüsü
while (running) {
    bufferMgr.writeInput(inputData, 256);
    bufferMgr.readOutput(outputData, 256);
    bufferMgr.swapBuffers();
}
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Performans Metrikleri` — L357–L365


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Write Latency | < 1μs | 0.8μs |
| Read Latency | < 1μs | 0.7μs |
| Buffer Değişim | < 5μs | 3.2μs |
| CPU (boşta) | < 0.1% | 0.05% |
| Bellek | < 10MB | 8MB |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Bağımlılıklar` — L367–L373


| Bağımlılık | Tür |
|------------|-----|
| C++ STL | Dil |
| std::atomic | Dil |
| K1 Bellek | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Durum: Implementasyon` — L375–L381


- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)

### ipc-mekanizmalari.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` (658 satır)

#### ipc-mekanizmalari.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § (giriş) — L1–L10

---
title: "IPC Mekanizmaları - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### IPC Mekanizmaları


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Teknik Detaylar` — L15–L552


##### 1. Unix Domain Sockets

Unix Domain Sockets, aynı makinedeki process'ler arası iletişim için:

```c
#include <sys/socket.h>
#include <sys/un.h>
#include <unistd.h>

// Unix domain socket sunucu
void start_unix_socket_server(const char *socket_path) {
    int server_fd = socket(AF_UNIX, SOCK_STREAM, 0);

    struct sockaddr_un addr;
    memset(&addr, 0, sizeof(addr));
    addr.sun_family = AF_UNIX;
    strncpy(addr.sun_path, socket_path, sizeof(addr.sun_path) - 1);

    // Mevcut socket'i sil
    unlink(socket_path);

    if (bind(server_fd, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
        perror("bind");
        return;
    }

    if (listen(server_fd, 5) == -1) {
        perror("listen");
        return;
    }

    while (running) {
        int client_fd = accept(server_fd, NULL, NULL);
        if (client_fd == -1) {
            perror("accept");
            continue;
        }

        // Client'ı handler thread'ine gönder
        pthread_t thread;
        pthread_create(&thread, NULL, handle_client, &client_fd);
        pthread_detach(thread);
    }
}

// Unix domain socket client
int connect_unix_socket(const char *socket_path) {
    int sock = socket(AF_UNIX, SOCK_STREAM, 0);

    struct sockaddr_un addr;
    memset(&addr, 0, sizeof(addr));
    addr.sun_family = AF_UNIX;
    strncpy(addr.sun_path, socket_path, sizeof(addr.sun_path) - 1);

    if (connect(sock, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
        perror("connect");
        close(sock);
        return -1;
    }

    return sock;
}

// Veri gönderme/alma
void send_audio_data(int sock, AudioBuffer *buffer) {
    // Header
    IPCMessageHeader header = {
        .type = MSG_AUDIO_DATA,
        .length = buffer->size
    };
    send(sock, &header, sizeof(header), 0);

    // Data
    send(sock, buffer->data, buffer->size, 0);
}

void receive_audio_data(int sock, AudioBuffer *buffer) {
    IPCMessageHeader header;
    recv(sock, &header, sizeof(header), 0);

    if (header.type == MSG_AUDIO_DATA) {
        buffer->data = malloc(header.length);
        recv(sock, buffer->data, header.length, 0);
    }
}
```

##### 2. Windows Named Pipes

Windows Named Pipes, Windows platformu için yüksek performanslı IPC:

```c
#include <windows.h>

// Named pipe sunucu
void start_named_pipe_server(const char *pipe_name) {
    HANDLE hPipe;
    char pipe_path[256];

    snprintf(pipe_path, sizeof(pipe_path), "\\\\.\\pipe\\%s", pipe_name);

    hPipe = CreateNamedPipe(
        pipe_path,
        PIPE_ACCESS_DUPLEX | FILE_FLAG_OVERLAPPED,
        PIPE_TYPE_MESSAGE | PIPE_READMODE_MESSAGE | PIPE_WAIT,
        PIPE_UNLIMITED_INSTANCES,
        65536,  // Output buffer
        65536,  // Input buffer
        0,      // Default timeout
        NULL);  // Default security

    if (hPipe == INVALID_HANDLE_VALUE) {
        printf("CreateNamedPipe failed: %d\n", GetLastError());
        return;
    }

    OVERLAPPED overlapped = { 0 };
    overlapped.hEvent = CreateEvent(NULL, TRUE, FALSE, NULL);

    while (running) {
        // Bağlantı bekle
        if (!ConnectNamedPipe(hPipe, &overlapped)) {
            if (GetLastError() == ERROR_IO_PENDING) {
                WaitForSingleObject(overlapped.hEvent, INFINITE);
            }
        }

        // Mesaj oku
        char buffer[65536];
        DWORD bytesRead;
        if (ReadFile(hPipe, buffer, sizeof(buffer), &bytesRead, &overlapped)) {
            process_message(buffer, bytesRead);
        }

        // Mesaj yaz
        DWORD bytesWritten;
        const char *response = "ACK";
        WriteFile(hPipe, response, strlen(response), &bytesWritten, &overlapped);

        DisconnectNamedPipe(hPipe);
    }

    CloseHandle(hPipe);
}

// Named pipe client
HANDLE connect_named_pipe(const char *pipe_name) {
    char pipe_path[256];
    snprintf(pipe_path, sizeof(pipe_path), "\\\\.\\pipe\\%s", pipe_name);

    HANDLE hPipe = CreateFile(
        pipe_path,
        GENERIC_READ | GENERIC_WRITE,
        0,
        NULL,
        OPEN_EXISTING,
        0,
        NULL);

    if (hPipe == INVALID_HANDLE_VALUE) {
        printf("CreateFile failed: %d\n", GetLastError());
        return INVALID_HANDLE_VALUE;
    }

    // Pipe modunu ayarla
    DWORD mode = PIPE_READMODE_MESSAGE;
    SetNamedPipeHandleState(hPipe, &mode, NULL, NULL);

    return hPipe;
}

// Asenkron pipe işlemleri
void async_pipe_communication(HANDLE hPipe) {
    OVERLAPPED overlapped = { 0 };
    overlapped.hEvent = CreateEvent(NULL, TRUE, FALSE, NULL);

    // Asenkron okuma
    char buffer[65536];
    DWORD bytesRead;
    ReadFile(hPipe, buffer, sizeof(buffer), &bytesRead, &overlapped);

    // Diğer işleri yap
    do_other_work();

    // Okuma tamamlandı mı kontrol et
    if (WaitForSingleObject(overlapped.hEvent, 1000) == WAIT_OBJECT_0) {
        GetOverlappedResult(hPipe, &overlapped, &bytesRead, FALSE);
        process_message(buffer, bytesRead);
    }

    CloseHandle(overlapped.hEvent);
}
```

##### 3. Shared Memory

Shared Memory, yüksek performanslı veri paylaşımı:

```c
#include <sys/mman.h>
#include <sys/stat.h>
#include <fcntl.h>

// POSIX shared memory
void create_shared_memory(const char *name, size_t size) {
    int shm_fd = shm_open(name, O_CREAT | O_RDWR, 0666);
    ftruncate(shm_fd, size);

    void *ptr = mmap(NULL, size, PROT_READ | PROT_WRITE,
        MAP_SHARED, shm_fd, 0);

    // Shared memory yapısı
    SharedAudioBuffer *shared = (SharedAudioBuffer *)ptr;
    shared->sample_rate = 48000;
    shared->channels = 2;
    shared->buffer_size = size - sizeof(SharedAudioBuffer);
    shared->write_index = 0;
    shared->read_index = 0;

    // Spinlock ile senkronizasyon
    shared->lock = 0;
}

void write_to_shared_memory(SharedAudioBuffer *shared, float *data, int length) {
    // Spinlock kilitle
    while (__sync_lock_test_and_set(&shared->lock, 1)) {
        // Busy wait
    }

    // Boş alan var mı kontrol et
    int available = shared->buffer_size -
        ((shared->write_index - shared->read_index + shared->buffer_size) %
         shared->buffer_size);

    if (length <= available) {
        // Veriyi yaz
        memcpy(shared->data + shared->write_index, data, length * sizeof(float));
        shared->write_index = (shared->write_index + length) % shared->buffer_size;
    }

    // Spinlock aç
    __sync_lock_release(&shared->lock);
}

void read_from_shared_memory(SharedAudioBuffer *shared, float *data, int length) {
    // Spinlock kilitle
    while (__sync_lock_test_and_set(&shared->lock, 1)) {
        // Busy wait
    }

    // Mevcut veri var mı kontrol et
    int available = (shared->write_index - shared->read_index + shared->buffer_size) %
                    shared->buffer_size;

    if (length <= available) {
        // Veriyi oku
        memcpy(data, shared->data + shared->read_index, length * sizeof(float));
        shared->read_index = (shared->read_index + length) % shared->buffer_size;
    }

    // Spinlock aç
    __sync_lock_release(&shared->lock);
}

// Windows shared memory
void create_windows_shared_memory(const char *name, size_t size) {
    HANDLE hMapFile = CreateFileMapping(
        INVALID_HANDLE_VALUE,
        NULL,
        PAGE_READWRITE,
        0,
        size,
        name);

    LPVOID ptr = MapViewOfFile(
        hMapFile,
        FILE_MAP_ALL_ACCESS,
        0,
        0,
        size);

    SharedAudioBuffer *shared = (SharedAudioBuffer *)ptr;
    shared->sample_rate = 48000;
    shared->channels = 2;
    shared->buffer_size = size - sizeof(SharedAudioBuffer);

    // Interlocked operations ile senkronizasyon
    InitializeCriticalSection(&shared->critical_section);
}

void write_to_windows_shared_memory(SharedAudioBuffer *shared, float *data, int length) {
    EnterCriticalSection(&shared->critical_section);

    memcpy(shared->data + shared->write_index, data, length * sizeof(float));
    shared->write_index = (shared->write_index + length) % shared->buffer_size;

    LeaveCriticalSection(&shared->critical_section);
}
```

##### 4. Message Queues

POSIX Message Queues, reliable mesaj iletimi için:

```c
#include <mqueue.h>

// Message queue oluşturma
mqd_t create_message_queue(const char *name, int max_messages, int max_size) {
    struct mq_attr attr;
    attr.mq_flags = 0;
    attr.mq_maxmsg = max_messages;
    attr.mq_msgsize = max_size;
    attr.mq_curmsgs = 0;

    mqd_t mq = mq_open(name, O_CREAT | O_RDWR, 0666, &attr);
    if (mq == (mqd_t)-1) {
        perror("mq_open");
        return -1;
    }

    return mq;
}

// Mesaj gönderme
void send_message(mqd_t mq, AudioMessage *msg) {
    if (mq_send(mq, (char *)msg, sizeof(AudioMessage), msg->priority) == -1) {
        perror("mq_send");
    }
}

// Mesaj alma
void receive_message(mqd_t mq, AudioMessage *msg) {
    unsigned int priority;
    if (mq_receive(mq, (char *)msg, sizeof(AudioMessage), &priority) == -1) {
        perror("mq_receive");
    }
}

// Asenkron mesaj alma
void async_receive_message(mqd_t mq, AudioMessage *msg) {
    struct sigevent sev;
    sev.sigev_notify = SIGEV_THREAD;
    sev.sigev_notify_function = message_handler;
    sev.sigev_notify_attributes = NULL;
    sev.sigev_value.sival_ptr = msg;

    mq_notify(mq, &sev);
}

void message_handler(union sigval sv) {
    AudioMessage *msg = (AudioMessage *)sv.sival_ptr;

    // Mesajı işle
    process_audio_message(msg);

    // Tekrar dinlemeye başla
    async_receive_message(mq, msg);
}

// Message queue temizleme
void cleanup_message_queue(const char *name, mqd_t mq) {
    mq_close(mq);
    mq_unlink(name);
}
```

##### 5. gRPC

gRPC, yüksek performanslı RPC framework'ü:

```protobuf
// coremusic.proto
syntax = "proto3";

package coremusic;

service AudioService {
    // Streaming RPC - ses verisi akışı
    rpc ProcessAudioStream (stream AudioChunk) returns (stream ProcessedChunk);

    // Unary RPC - tek istek/yanıt
    rpc GetAudioStatus (AudioStatusRequest) returns (AudioStatusResponse);

    // Server streaming
    rpc SubscribeEvents (EventSubscription) returns (stream AudioEvent);

    // Client streaming
    rpc UploadAudio (stream AudioChunk) returns (UploadResponse);
}

message AudioChunk {
    bytes data = 1;
    int64 timestamp = 2;
    int32 sample_rate = 3;
    int32 channels = 4;
}

message ProcessedChunk {
    bytes data = 1;
    int64 timestamp = 2;
    bool is_final = 3;
}

message AudioStatusRequest {
    string device_id = 1;
}

message AudioStatusResponse {
    bool is_active = 1;
    int32 sample_rate = 2;
    int32 buffer_size = 3;
    int32 latency_ms = 4;
}

message EventSubscription {
    string event_type = 1;
    int32 interval_ms = 2;
}

message AudioEvent {
    string type = 1;
    int64 timestamp = 2;
    map<string, string> metadata = 3;
}

message UploadResponse {
    bool success = 1;
    string message = 2;
    int64 bytes_received = 3;
}
```

```cpp
// gRPC server implementasyonu
#include <grpcpp/grpcpp.h>
#include "coremusic.grpc.pb.h"

class AudioServiceImpl final : public coremusic::AudioService::Service {
public:
    grpc::Status ProcessAudioStream(
        grpc::ServerContext* context,
        grpc::ServerReaderWriter<coremusic::ProcessedChunk,
                                coremusic::AudioChunk>* stream) override {

        coremusic::AudioChunk chunk;
        while (stream->Read(&chunk)) {
            // Ses verisini işle
            auto processed = process_audio(chunk);

            // Yanıtı gönder
            coremusic::ProcessedChunk response;
            response.set_data(processed.data());
            response.set_timestamp(processed.timestamp());
            stream->Write(response);
        }

        return grpc::Status::OK;
    }

    grpc::Status GetAudioStatus(
        grpc::ServerContext* context,
        const coremusic::AudioStatusRequest* request,
        coremusic::AudioStatusResponse* response) override {

        response->set_is_active(is_audio_active());
        response->set_sample_rate(get_sample_rate());
        response->set_buffer_size(get_buffer_size());
        response->set_latency_ms(get_latency());

        return grpc::Status::OK;
    }
};

void start_grpc_server(int port) {
    std::string server_address = "0.0.0.0:" + std::to_string(port);
    AudioServiceImpl service;

    grpc::ServerBuilder builder;
    builder.AddListeningPort(server_address, grpc::InsecureServerCredentials());
    builder.RegisterService(&service);

    auto server = builder.BuildAndStart();
    std::cout << "gRPC server listening on " << server_address << std::endl;
    server->Wait();
}
```

```cpp
// gRPC client implementasyonu
class AudioGrpcClient {
public:
    AudioGrpcClient(std::string address)
        : channel_(grpc::CreateChannel(address, grpc::InsecureChannelCredentials())),
          stub_(coremusic::AudioService::NewStub(channel_)) {}

    void stream_audio(const std::vector<float>& audio_data) {
        grpc::ClientContext context;

        auto stream = stub_->ProcessAudioStream(&context);

        // Ses verisini gönder
        for (size_t i = 0; i < audio_data.size(); i += 1024) {
            coremusic::AudioChunk chunk;
            chunk.set_data(audio_data.data() + i, 1024 * sizeof(float));
            chunk.set_timestamp(get_timestamp());
            chunk.set_sample_rate(48000);
            chunk.set_channels(2);
            stream->Write(chunk);
        }

        stream->WritesDone();

        // Yanıtları al
        coremusic::ProcessedChunk response;
        while (stream->Read(&response)) {
            process_response(response);
        }
    }

    coremusic::AudioStatusResponse get_status() {
        grpc::ClientContext context;
        coremusic::AudioStatusRequest request;
        request.set_device_id("default");

        coremusic::AudioStatusResponse response;
        stub_->GetAudioStatus(&context, request, &response);

        return response;
    }

private:
    std::shared_ptr<grpc::Channel> channel_;
    std::unique_ptr<coremusic::AudioService::Stub> stub_;
};
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Performans Metrikleri` — L626–L634


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Unix Socket latency | < 10μs | Belirlenecek |
| Named Pipe latency | < 15μs | Belirlenecek |
| Shared Memory throughput | > 10GB/s | Belirlenecek |
| Message Queue latency | < 50μs | Belirlenecek |
| gRPC latency | < 1ms | Belirlenecek |

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - POSIX (Unix Domain Sockets, Shared Memory, Message Queues) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L612 |
| - Windows API (Named Pipes) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L613 |
| - gRPC 1.40 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L614 |
| - Protocol Buffers 3.x | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L615 |
| - K0 İşletim Sistemi katmanı (platform-specific) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L618 |
| - Ağ stack'i (TCP/IP) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L619 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L622 |
| - K2 Ağ Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L623 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L624 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| uint64_t overflowCount; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L334 |
| 0,      // Default timeout | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L125 |
| DisconnectNamedPipe(hPipe); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L156 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| perror("bind"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L39 |
| perror("listen"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L44 |
| perror("accept"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L51 |
| perror("connect"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L72 |
| NULL);  // Default security | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L126 |
| printf("CreateNamedPipe failed: %d\n", GetLastError()); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L129 |
| if (GetLastError() == ERROR_IO_PENDING) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L139 |
| printf("CreateFile failed: %d\n", GetLastError()); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L177 |
| perror("mq_open"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L334 |
| perror("mq_send"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L344 |
| perror("mq_receive"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L352 |
| request.set_device_id("default"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L540 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| title: "Buffer Yönetimi" | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L2 |
| # Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L8 |
| COREMUSIC buffer yönetimi, ses verilerinin donanım ile yazılım arasında verimli ve düşük gecikmeli transferini sağlar. Ring buffer, double … | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L12 |
| ### Ring Buffer Yapısı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L16 |
| Ring Buffer Yapısı: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L19 |
| ### Lock-Free Ring Buffer | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L34 |
| // Lock-free ring buffer implementasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L37 |
| class LockFreeRingBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L39 |
| LockFreeRingBuffer(size_t capacity) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L41 |
| : capacity(capacity), buffer(new T[capacity]), | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L42 |
| buffer[(currentWrite + i) % capacity] = data[i]; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L55 |
| data[i] = buffer[(currentRead + i) % capacity]; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L73 |
| std::unique_ptr<T[]> buffer; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L94 |
| ### Double Buffering | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L100 |
| Double buffering, kesintisiz ses akışı için kullanılır: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L102 |
| Double Buffering Akışı: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L105 |
| │  │  Buffer A    │  │  Buffer B    │                  │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L110 |
| │  │  Buffer A    │  │  Buffer B    │                  │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L118 |
| // Double buffer implementasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L128 |
| class DoubleBuffer { | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L129 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| // Güvenlik payı (%20) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L245 |
| ## Güvenlik Notları | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L636 |
| 2. **Named Pipes**: Security descriptor ile erişim kontrolü | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L639 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| // Ölçümler | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L319 |
| while (__sync_lock_test_and_set(&shared->lock, 1)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L242 |
| while (__sync_lock_test_and_set(&shared->lock, 1)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L263 |
| - Shared memory spinlock testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L656 |
| - Socket performance benchmark'larının hazırlanması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L657 |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **5** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

| Belirsizlik / risk (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |

## Identifier Envanteri

> Identifier'lar kaynak metinden sayım ile üretilmiştir; ilk geçtiği satır kanıt olarak verilmiştir.

| Identifier | Geçiş sayısı | İlk kanıt |
|---|---|---|
| `lock-free` | 5 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L12 |
| `atomic` | 5 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L95 |
| `queue` | 24 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` L12 |
| `DMA` | 0 | [kaynakta eşleşme yok] |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Teknik Detaylar | L14–L301 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## API / Arayüz | L303–L355 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Performans Metrikleri | L357–L365 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Bağımlılıklar | L367–L373 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` | ## Durum: Implementasyon | L375–L381 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Genel Bakış | L11–L13 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Teknik Detaylar | L15–L552 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## API / Arayüz | L554–L607 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Bağımlılıklar | L609–L624 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Performans Metrikleri | L626–L634 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Güvenlik Notları | L636–L642 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Durum: Implementasyon | L644–L658 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/buffer-management.md` § `Durum: Implementasyon` — L375–L381


- **Faz 1**: Lock-free ring buffer
- **Faz 2**: Double buffering
- **Faz 3**: SPSC queue
- **Faz 4**: Adaptif buffer yönetimi
- **Tahmini Süre**: 1.5 hafta (60 adam-saat)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Durum: Implementasyon` — L644–L658


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Shared Memory implementasyonu (en yüksek performans)
2. Unix Domain Sockets implementasyonu
3. Windows Named Pipes implementasyonu
4. Message Queues implementasyonu
5. gRPC entegrasyonu

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
