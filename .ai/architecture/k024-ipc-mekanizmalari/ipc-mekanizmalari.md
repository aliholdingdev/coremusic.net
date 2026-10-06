---
title: "IPC Mekanizmaları - k024-ipc-mekanizmalari"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# IPC Mekanizmaları

> Klasör: `k024-ipc-mekanizmalari` · Dilim: D01 (k018–k035) · Dosya: `ipc-mekanizmalari.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

IPC mekanizmalarının teknik detayları, API yüzeyi, güvenlik notları ve metrikleri.

Bu belge; D01 diliminin (IPC Mekanizmaları) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** IPC mekanizmalarının teknik detayları, API yüzeyi, güvenlik notları ve metrikleri.
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
| 0,      // Default timeout | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L125 |
| DisconnectNamedPipe(hPipe); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L156 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
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
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |

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
| `pipe` | 45 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L13 |
| `socket` | 28 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L13 |
| `shared memory` | 12 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L13 |
| `latency` | 6 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` L429 |

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

## Durum: Implementasyon

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
