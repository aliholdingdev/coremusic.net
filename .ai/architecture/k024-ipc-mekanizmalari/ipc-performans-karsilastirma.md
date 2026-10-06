---
title: "IPC Performans Karşılaştırması - k024-ipc-mekanizmalari"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# IPC Performans Karşılaştırması

> Klasör: `k024-ipc-mekanizmalari` · Dilim: D01 (k018–k035) · Dosya: `ipc-performans-karsilastirma.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

IPC mekanizmalarının gecikme/throughput karşılaştırması ve seçim kriterleri.

Bu belge; D01 diliminin (IPC Mekanizmaları) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** IPC mekanizmalarının gecikme/throughput karşılaştırması ve seçim kriterleri.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (IPC Mekanizmaları)
- **Çapraz referanslar:** [[../k025-threading-model/threading-model.md]] · [[../k031-buffer-management/kilitsiz-kuyruklar.md]] · [[../k026-memory-management/memory-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

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


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Genel Bakış` — L11–L13


IPC (Inter-Process Communication) Mekanizmaları modülü, COREMUSIC'ın process arası iletişim çözümlerini yönetir. Unix Domain Sockets, Windows Named Pipes, Shared Memory, Message Queues ve gRPC gibi çeşitli iletişim protokollerini destekler. Yüksek performanslı, düşük gecikmeli ve güvenli veri transferi sağlar.

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

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `API / Arayüz` — L554–L607


##### COREMUSIC IPC API Başlık Dosyası

```c
#ifndef COREMUSIC_IPC_H
#define COREMUSIC_IPC_H

#include <stdint.h>
#include <stddef.h>

// Unix Domain Sockets
CM_Status CM_UnixSocket_Create(const char *path, int *socket_fd);
CM_Status CM_UnixSocket_Bind(int socket_fd, const char *path);
CM_Status CM_UnixSocket_Listen(int socket_fd, int backlog);
CM_Status CM_UnixSocket_Accept(int server_fd, int *client_fd);
CM_Status CM_UnixSocket_Connect(int socket_fd, const char *path);
CM_Status CM_UnixSocket_Read(int socket_fd, void *buf, size_t len, size_t *read);
CM_Status CM_UnixSocket_Write(int socket_fd, const void *buf, size_t len);
CM_Status CM_UnixSocket_Close(int socket_fd);

// Windows Named Pipes
CM_Status CM_NamedPipe_Create(const char *name, void **handle);
CM_Status CM_NamedPipe_Connect(const char *name, void **handle);
CM_Status CM_NamedPipe_Read(void *handle, void *buf, size_t len, size_t *read);
CM_Status CM_NamedPipe_Write(void *handle, const void *buf, size_t len);
CM_Status CM_NamedPipe_Close(void *handle);

// Shared Memory
CM_Status CM_SharedMemory_Create(const char *name, size_t size, void **ptr);
CM_Status CM_SharedMemory_Open(const char *name, size_t size, void **ptr);
CM_Status CM_SharedMemory_Read(void *ptr, void *buf, size_t len);
CM_Status CM_SharedMemory_Write(void *ptr, const void *buf, size_t len);
CM_Status CM_SharedMemory_Close(void *ptr);
CM_Status CM_SharedMemory_Unlink(const char *name);

// Message Queues
CM_Status CM_MessageQueue_Create(const char *name, int max_messages, int max_size, void **mq);
CM_Status CM_MessageQueue_Send(void *mq, const void *msg, size_t len, int priority);
CM_Status CM_MessageQueue_Receive(void *mq, void *msg, size_t len, int *priority);
CM_Status CM_MessageQueue_Close(void *mq);
CM_Status CM_MessageQueue_Unlink(const char *name);

// gRPC
CM_Status CM_GrpcServer_Create(int port, void **server);
CM_Status CM_GrpcServer_Start(void *server);
CM_Status CM_GrpcServer_Stop(void *server);
CM_Status CM_GrpcClient_Create(const char *address, void **client);
CM_Status CM_GrpcClient_SendAudio(void *client, const void *data, size_t len);
CM_Status CM_GrpcClient_GetStatus(void *client, void *status);
CM_Status CM_GrpcClient_Destroy(void *client);

#endif // COREMUSIC_IPC_H
```

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Bağımlılıklar` — L609–L624


##### Gereksinimler
- POSIX (Unix Domain Sockets, Shared Memory, Message Queues)
- Windows API (Named Pipes)
- gRPC 1.40 ve üzeri
- Protocol Buffers 3.x

##### Alt Katmanlar
- K0 İşletim Sistemi katmanı (platform-specific)
- Ağ stack'i (TCP/IP)

##### Üst Katmanlar
- K1 Ses Motoru
- K2 Ağ Katmanı
- K3 Uygulama Katmanı

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Performans Metrikleri` — L626–L634


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Unix Socket latency | < 10μs | Belirlenecek |
| Named Pipe latency | < 15μs | Belirlenecek |
| Shared Memory throughput | > 10GB/s | Belirlenecek |
| Message Queue latency | < 50μs | Belirlenecek |
| gRPC latency | < 1ms | Belirlenecek |

#### Güvenlik Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Güvenlik Notları` — L636–L642


1. **Unix Domain Sockets**: Dosya izinleri ile erişim kontrolü
2. **Named Pipes**: Security descriptor ile erişim kontrolü
3. **Shared Memory**: Lock mekanizmaları ile veri bütünlüğü
4. **Message Queues**: POSIX mq_open ile erişim kontrolü
5. **gRPC**: TLS ile şifreli iletişim

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Durum: Implementasyon` — L644–L658


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Shared Memory implementasyonu (en yüksek performans)
2. Unix Domain Sockets implementasyonu
3. Windows Named Pipes implementasyonu
4. Message Queues implementasyonu
5. gRPC entegrasyonu

**Sonraki Adımlar**:
- Shared memory spinlock testlerinin yapılması
- Socket performance benchmark'larının hazırlanması
- gRPC proto dosyalarının compile edilmesi

### threading-model.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` (736 satır)

#### threading-model.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § (giriş) — L1–L10

---
title: "Threading Model - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### Threading Model


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Teknik Detaylar` — L15–L621


##### 1. Thread Pools

Thread pool'lar, iş parçacığı oluşturma/m yok etme overhead'ini azaltır:

```c
#include <stdatomic.h>
#include <pthread.h>

// Task yapısı
typedef struct Task {
    void (*function)(void *);
    void *arg;
    struct Task *next;
} Task;

// Thread pool yapısı
typedef struct {
    pthread_t *threads;
    int thread_count;

    Task *task_queue;
    pthread_mutex_t queue_mutex;
    pthread_cond_t queue_cond;

    atomic_int active_tasks;
    atomic_int running;

    // Worker thread ID'leri
    int *thread_ids;
    cpu_set_t *cpu_affinities;
} ThreadPool;

// Thread pool oluşturma
ThreadPool* thread_pool_create(int thread_count) {
    ThreadPool *pool = malloc(sizeof(ThreadPool));
    pool->thread_count = thread_count;
    pool->threads = malloc(sizeof(pthread_t) * thread_count);
    pool->thread_ids = malloc(sizeof(int) * thread_count);
    pool->cpu_affinities = malloc(sizeof(cpu_set_t) * thread_count);

    pthread_mutex_init(&pool->queue_mutex, NULL);
    pthread_cond_init(&pool->queue_cond, NULL);

    pool->task_queue = NULL;
    pool->active_tasks = 0;
    pool->running = 1;

    // Worker thread'leri başlat
    for (int i = 0; i < thread_count; i++) {
        pool->thread_ids[i] = i;

        // CPU affinity ayarla
        CPU_ZERO(&pool->cpu_affinities[i]);
        CPU_SET(i, &pool->cpu_affinities[i]);

        pthread_create(&pool->threads[i], NULL, worker_thread, pool);
    }

    return pool;
}

// Worker thread fonksiyonu
void* worker_thread(void *arg) {
    ThreadPool *pool = (ThreadPool *)arg;

    while (pool->running) {
        pthread_mutex_lock(&pool->queue_mutex);

        while (!pool->task_queue && pool->running) {
            pthread_cond_wait(&pool->queue_cond, &pool->queue_mutex);
        }

        if (!pool->running && !pool->task_queue) {
            pthread_mutex_unlock(&pool->queue_mutex);
            break;
        }

        // Task'ı al
        Task *task = pool->task_queue;
        if (task) {
            pool->task_queue = task->next;
        }

        pthread_mutex_unlock(&pool->queue_mutex);

        if (task) {
            atomic_fetch_add(&pool->active_tasks, 1);
            task->function(task->arg);
            atomic_fetch_sub(&pool->active_tasks, 1);
            free(task);
        }
    }

    return NULL;
}

// Task ekleme
void thread_pool_submit(ThreadPool *pool, void (*function)(void *), void *arg) {
    Task *task = malloc(sizeof(Task));
    task->function = function;
    task->arg = arg;
    task->next = NULL;

    pthread_mutex_lock(&pool->queue_mutex);

    // Queue sonuna ekle
    if (!pool->task_queue) {
        pool->task_queue = task;
    } else {
        Task *current = pool->task_queue;
        while (current->next) {
            current = current->next;
        }
        current->next = task;
    }

    pthread_cond_signal(&pool->queue_cond);
    pthread_mutex_unlock(&pool->queue_mutex);
}

// Thread pool durdurma
void thread_pool_destroy(ThreadPool *pool) {
    pool->running = 0;
    pthread_cond_broadcast(&pool->queue_cond);

    for (int i = 0; i < pool->thread_count; i++) {
        pthread_join(pool->threads[i], NULL);
    }

    pthread_mutex_destroy(&pool->queue_mutex);
    pthread_cond_destroy(&pool->queue_cond);

    free(pool->threads);
    free(pool->thread_ids);
    free(pool->cpu_affinities);
    free(pool);
}

// Priority thread pool
typedef struct {
    ThreadPool *high_priority;
    ThreadPool *normal_priority;
    ThreadPool *low_priority;
    atomic_int current_priority;
} PriorityThreadPool;

PriorityThreadPool* priority_thread_pool_create(int threads_per_priority) {
    PriorityThreadPool *pool = malloc(sizeof(PriorityThreadPool));
    pool->high_priority = thread_pool_create(threads_per_priority);
    pool->normal_priority = thread_pool_create(threads_per_priority);
    pool->low_priority = thread_pool_create(threads_per_priority);
    pool->current_priority = 0;
    return pool;
}

void priority_thread_pool_submit(PriorityThreadPool *pool, int priority,
    void (*function)(void *), void *arg) {
    switch (priority) {
        case 0: thread_pool_submit(pool->high_priority, function, arg); break;
        case 1: thread_pool_submit(pool->normal_priority, function, arg); break;
        case 2: thread_pool_submit(pool->low_priority, function, arg); break;
    }
}
```

##### 2. Lock-free Structures

Lock-free veri yapıları, kilitleme olmadan eşzamanlı erişim:

```c
#include <stdatomic.h>

// Lock-free stack
typedef struct LFNode {
    void *data;
    _Atomic(struct LFNode *) next;
} LFNode;

typedef struct {
    _Atomic(LFNode *) top;
    atomic_int count;
} LFStack;

LFStack* lf_stack_create() {
    LFStack *stack = malloc(sizeof(LFStack));
    stack->top = NULL;
    stack->count = 0;
    return stack;
}

void lf_stack_push(LFStack *stack, void *data) {
    LFNode *node = malloc(sizeof(LFNode));
    node->data = data;

    LFNode *old_top;
    do {
        old_top = atomic_load(&stack->top);
        node->next = old_top;
    } while (!atomic_compare_exchange_weak(&stack->top, &old_top, node));

    atomic_fetch_add(&stack->count, 1);
}

void* lf_stack_pop(LFStack *stack) {
    LFNode *old_top;
    LFNode *new_top;

    do {
        old_top = atomic_load(&stack->top);
        if (!old_top) return NULL;
        new_top = old_top->next;
    } while (!atomic_compare_exchange_weak(&stack->top, &old_top, new_top));

    atomic_fetch_sub(&stack->count, 1);
    void *data = old_top->data;
    free(old_top);
    return data;
}

// Lock-free queue (Michael-Scott queue)
typedef struct LFQueueNode {
    void *data;
    _Atomic(struct LFQueueNode *) next;
} LFQueueNode;

typedef struct {
    _Atomic(LFQueueNode *) head;
    _Atomic(LFQueueNode *) tail;
    atomic_int count;
} LFQueue;

LFQueue* lf_queue_create() {
    LFQueue *queue = malloc(sizeof(LFQueue));
    LFQueueNode *sentinel = malloc(sizeof(LFQueueNode));
    sentinel->next = NULL;

    queue->head = sentinel;
    queue->tail = sentinel;
    queue->count = 0;

    return queue;
}

void lf_queue_enqueue(LFQueue *queue, void *data) {
    LFQueueNode *node = malloc(sizeof(LFQueueNode));
    node->data = data;
    node->next = NULL;

    LFQueueNode *old_tail;
    LFQueueNode *next;

    do {
        old_tail = atomic_load(&queue->tail);
        next = atomic_load(&old_tail->next);

        if (old_tail == atomic_load(&queue->tail)) {
            if (next == NULL) {
                if (atomic_compare_exchange_weak(&old_tail->next, &next, node)) {
                    break;
                }
            } else {
                atomic_compare_exchange_weak(&queue->tail, &old_tail, next);
            }
        }
    } while (1);

    atomic_compare_exchange_weak(&queue->tail, &old_tail, node);
    atomic_fetch_add(&queue->count, 1);
}

void* lf_queue_dequeue(LFQueue *queue) {
    LFQueueNode *old_head;
    LFQueueNode *old_tail;
    LFQueueNode *next;

    do {
        old_head = atomic_load(&queue->head);
        old_tail = atomic_load(&queue->tail);
        next = atomic_load(&old_head->next);

        if (old_head == atomic_load(&queue->head)) {
            if (old_head == old_tail) {
                if (next == NULL) {
                    return NULL;  // Queue boş
                }
                atomic_compare_exchange_weak(&queue->tail, &old_tail, next);
            } else {
                void *data = next->data;
                if (atomic_compare_exchange_weak(&queue->head, &old_head, next)) {
                    free(old_head);
                    atomic_fetch_sub(&queue->count, 1);
                    return data;
                }
            }
        }
    } while (1);
}
```

##### 3. Atomic Operations

Atomik işlemler, kilitleme gerektirmeyen güvenli operasyonlar:

```c
#include <stdatomic.h>

// Atomic counter
typedef struct {
    _Atomic int64_t value;
} AtomicCounter;

void atomic_counter_init(AtomicCounter *counter, int64_t initial) {
    atomic_store(&counter->value, initial);
}

int64_t atomic_counter_increment(AtomicCounter *counter) {
    return atomic_fetch_add(&counter->value, 1) + 1;
}

int64_t atomic_counter_decrement(AtomicCounter *counter) {
    return atomic_fetch_sub(&counter->value, 1) - 1;
}

int64_t atomic_counter_get(AtomicCounter *counter) {
    return atomic_load(&counter->value);
}

// Atomic compare and swap
typedef struct {
    _Atomic int64_t value;
} AtomicCAS;

int64_t atomic_cas_compare(AtomicCAS *cas, int64_t expected, int64_t desired) {
    atomic_compare_exchange_strong(&cas->value, &expected, desired);
    return expected;
}

// Atomic min/max
int64_t atomic_min(_Atomic int64_t *value, int64_t new_value) {
    int64_t old_value;
    do {
        old_value = atomic_load(value);
        if (new_value >= old_value) break;
    } while (!atomic_compare_exchange_weak(value, &old_value, new_value));
    return old_value;
}

int64_t atomic_max(_Atomic int64_t *value, int64_t new_value) {
    int64_t old_value;
    do {
        old_value = atomic_load(value);
        if (new_value <= old_value) break;
    } while (!atomic_compare_exchange_weak(value, &old_value, new_value));
    return old_value;
}

// Spinlock
typedef struct {
    _Atomic int lock;
} Spinlock;

void spinlock_init(Spinlock *spin) {
    atomic_store(&spin->lock, 0);
}

void spinlock_lock(Spinlock *spin) {
    while (atomic_exchange(&spin->lock, 1)) {
        // Busy wait - pause instruction for CPU
        #ifdef __x86_64__
        asm volatile("pause");
        #endif
    }
}

int spinlock_trylock(Spinlock *spin) {
    return atomic_exchange(&spin->lock, 0) == 0;
}

void spinlock_unlock(Spinlock *spin) {
    atomic_store(&spin->lock, 0);
}

// Read-Write Lock
typedef struct {
    _Atomic int readers;
    _Atomic int writer;
    Spinlock spin;
} RWLock;

void rwlock_init(RWLock *rw) {
    atomic_store(&rw->readers, 0);
    atomic_store(&rw->writer, 0);
    spinlock_init(&rw->spin);
}

void rwlock_read_lock(RWLock *rw) {
    while (atomic_load(&rw->writer)) {
        #ifdef __x86_64__
        asm volatile("pause");
        #endif
    }
    atomic_fetch_add(&rw->readers, 1);
}

void rwlock_read_unlock(RWLock *rw) {
    atomic_fetch_sub(&rw->readers, 1);
}

void rwlock_write_lock(RWLock *rw) {
    spinlock_lock(&rw->spin);
    while (atomic_load(&rw->readers) > 0) {
        spinlock_unlock(&rw->spin);
        #ifdef __x86_64__
        asm volatile("pause");
        #endif
        spinlock_lock(&rw->spin);
    }
    atomic_store(&rw->writer, 1);
}

void rwlock_write_unlock(RWLock *rw) {
    atomic_store(&rw->writer, 0);
    spinlock_unlock(&rw->spin);
}
```

##### 4. Condition Variables

Condition variables, thread senkronizasyonu için:

```c
#include <pthread.h>

// Condition variable wrapper
typedef struct {
    pthread_cond_t cond;
    pthread_mutex_t mutex;
} CMCondition;

CMCondition* cm_condition_create() {
    CMCondition *cond = malloc(sizeof(CMCondition));
    pthread_cond_init(&cond->cond, NULL);
    pthread_mutex_init(&cond->mutex, NULL);
    return cond;
}

void cm_condition_wait(CMCondition *cond) {
    pthread_mutex_lock(&cond->mutex);
    pthread_cond_wait(&cond->cond, &cond->mutex);
    pthread_mutex_unlock(&cond->mutex);
}

void cm_condition_wait_timeout(CMCondition *cond, uint64_t timeout_ms) {
    struct timespec ts;
    clock_gettime(CLOCK_REALTIME, &ts);
    ts.tv_sec += timeout_ms / 1000;
    ts.tv_nsec += (timeout_ms % 1000) * 1000000;

    if (ts.tv_nsec >= 1000000000) {
        ts.tv_sec++;
        ts.tv_nsec -= 1000000000;
    }

    pthread_mutex_lock(&cond->mutex);
    pthread_cond_timedwait(&cond->cond, &cond->mutex, &ts);
    pthread_mutex_unlock(&cond->mutex);
}

void cm_condition_signal(CMCondition *cond) {
    pthread_mutex_lock(&cond->mutex);
    pthread_cond_signal(&cond->cond);
    pthread_mutex_unlock(&cond->mutex);
}

void cm_condition_broadcast(CMCondition *cond) {
    pthread_mutex_lock(&cond->mutex);
    pthread_cond_broadcast(&cond->cond);
    pthread_mutex_unlock(&cond->mutex);
}

// Barrier (tüm thread'lerin tamamlanmasını bekleme)
typedef struct {
    pthread_barrier_t barrier;
    int thread_count;
} CMBarrier;

CMBarrier* cm_barrier_create(int thread_count) {
    CMBarrier *barrier = malloc(sizeof(CMBarrier));
    barrier->thread_count = thread_count;
    pthread_barrier_init(&barrier->barrier, NULL, thread_count);
    return barrier;
}

void cm_barrier_wait(CMBarrier *barrier) {
    pthread_barrier_wait(&barrier->barrier);
}

void cm_barrier_destroy(CMBarrier *barrier) {
    pthread_barrier_destroy(&barrier->barrier);
    free(barrier);
}
```

##### 5. Mutex Hierarchy

Mutex hiyerarşisi, deadlock önleme için:

```c
#include <pthread.h>

// Mutex seviyeleri
typedef enum {
    MUTEX_LEVEL_CRITICAL = 0,   // En yüksek öncelik
    MUTEX_LEVEL_HIGH = 1,
    MUTEX_LEVEL_MEDIUM = 2,
    MUTEX_LEVEL_LOW = 3,        // En düşük öncelik
    MUTEX_LEVEL_COUNT = 4
} MutexLevel;

// Mutex hierarchy yapısı
typedef struct {
    pthread_mutex_t mutexes[MUTEX_LEVEL_COUNT];
    _Atomic int current_level;
    _Atomic int lock_count[MUTEX_LEVEL_COUNT];
    pthread_t owner_thread;
} MutexHierarchy;

MutexHierarchy* mutex_hierarchy_create() {
    MutexHierarchy *hierarchy = malloc(sizeof(MutexHierarchy));

    for (int i = 0; i < MUTEX_LEVEL_COUNT; i++) {
        pthread_mutexattr_t attr;
        pthread_mutexattr_init(&attr);
        pthread_mutexattr_settype(&attr, PTHREAD_MUTEX_RECURSIVE);
        pthread_mutex_init(&hierarchy->mutexes[i], &attr);
        atomic_store(&hierarchy->lock_count[i], 0);
    }

    atomic_store(&hierarchy->current_level, MUTEX_LEVEL_COUNT);
    return hierarchy;
}

// Hiyerarşik mutex kilitleme
void mutex_hierarchy_lock(MutexHierarchy *hierarchy, MutexLevel level) {
    // Seviye kontrolü (deadlock önleme)
    int current = atomic_load(&hierarchy->current_level);
    if (level < current) {
        fprintf(stderr, "Mutex hierarchy violation: level %d < current %d\n",
                level, current);
        abort();
    }

    pthread_mutex_lock(&hierarchy->mutexes[level]);
    atomic_store(&hierarchy->current_level, level);
    atomic_fetch_add(&hierarchy->lock_count[level], 1);

    // Thread sahipliğini kaydet
    hierarchy->owner_thread = pthread_self();
}

// Hiyerarşik mutex kilidi açma
void mutex_hierarchy_unlock(MutexHierarchy *hierarchy, MutexLevel level) {
    if (hierarchy->owner_thread != pthread_self()) {
        fprintf(stderr, "Mutex not owned by current thread\n");
        abort();
    }

    atomic_fetch_sub(&hierarchy->lock_count[level], 1);
    pthread_mutex_unlock(&hierarchy->mutexes[level]);

    // Bir sonraki seviyeye geç
    for (int i = 0; i < MUTEX_LEVEL_COUNT; i++) {
        if (atomic_load(&hierarchy->lock_count[i]) > 0) {
            atomic_store(&hierarchy->current_level, i);
            return;
        }
    }
    atomic_store(&hierarchy->current_level, MUTEX_LEVEL_COUNT);
}

// Timeout ile mutex kilitleme
int mutex_hierarchy_trylock(MutexHierarchy *hierarchy, MutexLevel level,
                           uint64_t timeout_ms) {
    struct timespec ts;
    clock_gettime(CLOCK_REALTIME, &ts);
    ts.tv_sec += timeout_ms / 1000;
    ts.tv_nsec += (timeout_ms % 1000) * 1000000;

    int result = pthread_mutex_timedlock(&hierarchy->mutexes[level], &ts);
    if (result == 0) {
        atomic_store(&hierarchy->current_level, level);
        atomic_fetch_add(&hierarchy->lock_count[level], 1);
        hierarchy->owner_thread = pthread_self();
    }

    return result;
}

void mutex_hierarchy_destroy(MutexHierarchy *hierarchy) {
    for (int i = 0; i < MUTEX_LEVEL_COUNT; i++) {
        pthread_mutex_destroy(&hierarchy->mutexes[i]);
    }
    free(hierarchy);
}
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Performans Metrikleri` — L711–L720


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Thread creation | < 50μs | Belirlenecek |
| Mutex lock/unlock | < 100ns | Belirlenecek |
| Spinlock lock/unlock | < 50ns | Belirlenecek |
| LF stack push/pop | < 100ns | Belirlenecek |
| LF queue enqueue/dequeue | < 200ns | Belirlenecek |
| Condition wait/signal | < 1μs | Belirlenecek |

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
| - pthreads (Linux/macOS) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L699 |
| - Windows Threads API | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L700 |
| - C11 atomics veya GCC atomics | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L701 |
| - K0 Memory Management | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L704 |
| - K0 İşletim Sistemi katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L705 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L708 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L709 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| 0,      // Default timeout | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L125 |
| DisconnectNamedPipe(hPipe); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L156 |
| void cm_condition_wait_timeout(CMCondition *cond, uint64_t timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L469 |
| ts.tv_sec += timeout_ms / 1000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L472 |
| ts.tv_nsec += (timeout_ms % 1000) * 1000000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L473 |
| // Timeout ile mutex kilitleme | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L597 |
| uint64_t timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L599 |
| ts.tv_sec += timeout_ms / 1000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L602 |
| ts.tv_nsec += (timeout_ms % 1000) * 1000000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L603 |
| CM_Status CM_Condition_WaitTimeout(void *cond, uint64_t timeout_ms); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L680 |

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
| IPC (Inter-Process Communication) Mekanizmaları modülü, COREMUSIC'ın process arası iletişim çözümlerini yönetir. Unix Domain Sockets, Windo… | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L13 |
| void send_audio_data(int sock, AudioBuffer *buffer) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L81 |
| .length = buffer->size | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L85 |
| send(sock, buffer->data, buffer->size, 0); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L90 |
| void receive_audio_data(int sock, AudioBuffer *buffer) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L93 |
| buffer->data = malloc(header.length); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L98 |
| recv(sock, buffer->data, header.length, 0); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L99 |
| 65536,  // Output buffer | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L123 |
| 65536,  // Input buffer | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L124 |
| char buffer[65536]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L145 |
| if (ReadFile(hPipe, buffer, sizeof(buffer), &bytesRead, &overlapped)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L147 |
| process_message(buffer, bytesRead); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L148 |
| char buffer[65536]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L194 |
| ReadFile(hPipe, buffer, sizeof(buffer), &bytesRead, &overlapped); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L196 |
| process_message(buffer, bytesRead); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L204 |
| SharedAudioBuffer *shared = (SharedAudioBuffer *)ptr; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L229 |
| shared->buffer_size = size - sizeof(SharedAudioBuffer); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L232 |
| void write_to_shared_memory(SharedAudioBuffer *shared, float *data, int length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L240 |
| int available = shared->buffer_size - | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L247 |
| ((shared->write_index - shared->read_index + shared->buffer_size) % | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L248 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ## Güvenlik Notları | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L636 |
| 2. **Named Pipes**: Security descriptor ile erişim kontrolü | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L639 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| while (__sync_lock_test_and_set(&shared->lock, 1)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L242 |
| while (__sync_lock_test_and_set(&shared->lock, 1)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L263 |
| - Shared memory spinlock testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L656 |
| - Socket performance benchmark'larının hazırlanması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L657 |
| - Thread pool performans testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L734 |
| - Lock-free yapıların stres testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L735 |
| - Deadlock test senaryolarının yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` L736 |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **11** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

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
| `latency` | 6 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L429 |
| `buffer` | 32 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L81 |
| `queue` | 83 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L13 |
| `benchmark` | 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L657 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Genel Bakış | L11–L13 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Teknik Detaylar | L15–L552 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## API / Arayüz | L554–L607 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Bağımlılıklar | L609–L624 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Performans Metrikleri | L626–L634 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Güvenlik Notları | L636–L642 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` | ## Durum: Implementasyon | L644–L658 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Genel Bakış | L11–L13 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Teknik Detaylar | L15–L621 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## API / Arayüz | L623–L694 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Bağımlılıklar | L696–L709 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Performans Metrikleri | L711–L720 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` | ## Durum: Implementasyon | L722–L736 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` § `Durum: Implementasyon` — L644–L658


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Shared Memory implementasyonu (en yüksek performans)
2. Unix Domain Sockets implementasyonu
3. Windows Named Pipes implementasyonu
4. Message Queues implementasyonu
5. gRPC entegrasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/threading-model.md` § `Durum: Implementasyon` — L722–L736


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Thread pool implementasyonu
2. Lock-free queue implementasyonu
3. Spinlock implementasyonu
4. Mutex hierarchy
5. RWLock implementasyonu

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
