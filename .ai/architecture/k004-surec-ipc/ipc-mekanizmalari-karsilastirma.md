---
title: "IPC Mekanizmaları Karşılaştırma - k004-surec-ipc"
type: architecture-sublayer
category: isletim-sistemi
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - K0 (k003-k005)"
updated: 2026-10-06
---

# IPC Mekanizmaları - `ipc-mekanizmalari-karsilastirma.md`

> Klasör: `k004-surec-ipc` · Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md` (658 satır)
> Tür: salt-okunur verbatim aktarım · Wiki-link: [[index.md]] · Güncelleme: 2026-10-06

## Genel Bakış

IPC (Inter-Process Communication) Mekanizmaları modülü, COREMUSIC'ın process arası iletişim çözümlerini yönetir. Unix Domain Sockets, Windows Named Pipes, Shared Memory, Message Queues ve gRPC gibi çeşitli iletişim protokollerini destekler. Yüksek performanslı, düşük gecikmeli ve güvenli veri transferi sağlar.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L11-L14

## Teknik Detaylar

### 1. Unix Domain Sockets

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

### 2. Windows Named Pipes

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

### 3. Shared Memory

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L15-L316

## API / Arayüz

### COREMUSIC IPC API Başlık Dosyası

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L554-L608

## Bağımlılıklar

### Gereksinimler
- POSIX (Unix Domain Sockets, Shared Memory, Message Queues)
- Windows API (Named Pipes)
- gRPC 1.40 ve üzeri
- Protocol Buffers 3.x

### Alt Katmanlar
- K0 İşletim Sistemi katmanı (platform-specific)
- Ağ stack'i (TCP/IP)

### Üst Katmanlar
- K1 Ses Motoru
- K2 Ağ Katmanı
- K3 Uygulama Katmanı


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L609-L625

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Unix Socket latency | < 10μs | Belirlenecek |
| Named Pipe latency | < 15μs | Belirlenecek |
| Shared Memory throughput | > 10GB/s | Belirlenecek |
| Message Queue latency | < 50μs | Belirlenecek |
| gRPC latency | < 1ms | Belirlenecek |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L626-L635

## Güvenlik Notları

1. **Unix Domain Sockets**: Dosya izinleri ile erişim kontrolü
2. **Named Pipes**: Security descriptor ile erişim kontrolü
3. **Shared Memory**: Lock mekanizmaları ile veri bütünlüğü
4. **Message Queues**: POSIX mq_open ile erişim kontrolü
5. **gRPC**: TLS ile şifreli iletişim


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L636-L643

## Durum: Implementasyon

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

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/ipc-mekanizmalari.md - L644-L658

---
**Wiki-link:** [[index.md]] · Kardeş dosya: [[surec-izolasyon.md]]
