---
title: "Çapraz Platform API - k029-cross-platform-api"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Çapraz Platform API

> Klasör: `k029-cross-platform-api` · Dilim: D01 (k018–k035) · Dosya: `cross-platform-api.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Platformlar arası ortak API yüzeyi, teknik detaylar ve bağımlılıklar.

Bu belge; D01 diliminin (Çapraz Platform API) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Platformlar arası ortak API yüzeyi, teknik detaylar ve bağımlılıklar.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Çapraz Platform API)
- **Çapraz referanslar:** [[../k019-windows-core/windows-core.md]] · [[../k020-linux-core/linux-core.md]] · [[../k021-macos-core/macos-core.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### cross-platform-api.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` (658 satır)

#### cross-platform-api.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § (giriş) — L1–L10

---
title: "Cross-Platform API - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### Cross-Platform API


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Genel Bakış` — L11–L13


Cross-Platform API modülü, COREMUSIC'ın tüm işletim sistemlerinde tutarlı bir programlama arayüzü sağlamak için soyutlama katmanları sunar. POSIX Threads, SDL2, libuv, libevent, Boost.Asio ve Rust tokio gibi kütüphaneleri entegre ederek çapraz platform uyumluluğu ve yüksek performanslı I/O işleme sağlar. Her platform için optimize edilmiş implementasyonlar içerir.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Teknik Detaylar` — L15–L539


##### 1. POSIX Threads (pthreads)

POSIX Threads, Unix tabanlı sistemler için standart çoklu iş parçacığı API'si:

```c
#include <pthread.h>
#include <sched.h>

// Thread oluşturma ve yapılandırma
typedef struct {
    void *(*function)(void *);
    void *arg;
    int priority;
    int core_affinity;
} ThreadConfig;

pthread_t create_thread(ThreadConfig *config) {
    pthread_t thread;
    pthread_attr_t attr;

    pthread_attr_init(&attr);

    // Detached thread oluştur
    pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_DETACHED);

    // Thread stack boyutu
    pthread_attr_setstacksize(&attr, 1024 * 1024);  // 1MB

    // Öncelik
    struct sched_param param;
    param.sched_priority = config->priority;
    pthread_attr_setschedparam(&attr, &param);
    pthread_attr_setschedpolicy(&attr, SCHED_FIFO);

    // CPU affinity
    cpu_set_t cpuset;
    CPU_ZERO(&cpuset);
    CPU_SET(config->core_affinity, &cpuset);
    pthread_attr_setaffinity_np(&attr, sizeof(cpu_set_t), &cpuset);

    // Thread oluştur
    pthread_create(&thread, &attr, config->function, config->arg);

    pthread_attr_destroy(&attr);
    return thread;
}

// Mutex hierarchy (deadlock önleme)
typedef struct {
    pthread_mutex_t low_priority;
    pthread_mutex_t medium_priority;
    pthread_mutex_t high_priority;
} MutexHierarchy;

void lock_in_order(MutexHierarchy *hierarchy) {
    // Öncelik sırasına göre kilitle
    pthread_mutex_lock(&hierarchy->high_priority);
    pthread_mutex_lock(&hierarchy->medium_priority);
    pthread_mutex_lock(&hierarchy->low_priority);
}

void unlock_in_reverse(MutexHierarchy *hierarchy) {
    // Ters sırada kilidi aç
    pthread_mutex_unlock(&hierarchy->low_priority);
    pthread_mutex_unlock(&hierarchy->medium_priority);
    pthread_mutex_unlock(&hierarchy->high_priority);
}

// Condition variable ile senkronizasyon
typedef struct {
    pthread_cond_t condition;
    pthread_mutex_t mutex;
    int ready;
} SyncPoint;

void sync_point_init(SyncPoint *sp) {
    pthread_cond_init(&sp->condition, NULL);
    pthread_mutex_init(&sp->mutex, NULL);
    sp->ready = 0;
}

void sync_point_wait(SyncPoint *sp) {
    pthread_mutex_lock(&sp->mutex);
    while (!sp->ready) {
        pthread_cond_wait(&sp->condition, &sp->mutex);
    }
    pthread_mutex_unlock(&sp->mutex);
}

void sync_point_signal(SyncPoint *sp) {
    pthread_mutex_lock(&sp->mutex);
    sp->ready = 1;
    pthread_cond_signal(&sp->condition);
    pthread_mutex_unlock(&sp->mutex);
}
```

##### 2. SDL2 - Simple DirectMedia Layer

SDL2, çoklu platform multimedia kütüphanesi:

```c
#include <SDL2/SDL.h>

// SDL audio callback
void audio_callback(void *userdata, Uint8 *stream, int len) {
    AudioState *state = (AudioState *)userdata;

    // Ses verisini doldur
    Sint16 *buffer = (Sint16 *)stream;
    int samples = len / sizeof(Sint16);

    for (int i = 0; i < samples; i++) {
        // Sine wave örneği
        double t = (double)state->sample_count / state->sample_rate;
        buffer[i] = (Sint16)(sin(2.0 * M_PI * state->frequency * t) * 32767);
        state->sample_count++;
    }
}

// SDL audio device başlatma
SDL_AudioDeviceID init_audio(int sample_rate, int channels) {
    SDL_AudioSpec spec, obtained;

    spec.freq = sample_rate;
    spec.format = AUDIO_S16SYS;
    spec.channels = channels;
    spec.samples = 1024;
    spec.callback = audio_callback;
    spec.userdata = &audio_state;

    SDL_AudioDeviceID device = SDL_OpenAudioDevice(
        NULL, 0, &spec, &obtained,
        SDL_AUDIO_ALLOW_FREQUENCY_CHANGE);

    SDL_PauseAudioDevice(device, 0);  // Başlat
    return device;
}

// SDL ile pencere ve render
SDL_Window* create_window(int width, int height) {
    SDL_Init(SDL_INIT_VIDEO | SDL_INIT_AUDIO);

    SDL_Window *window = SDL_CreateWindow(
        "COREMUSIC",
        SDL_WINDOWPOS_CENTERED, SDL_WINDOWPOS_CENTERED,
        width, height,
        SDL_WINDOW_SHOWN | SDL_WINDOW_RESIZABLE);

    return window;
}
```

##### 3. libuv - Asenkron I/O

libuv, yüksek performanslı asenkron I/O için:

```c
#include <uv.h>

// Timer ile periyodik görevler
uv_timer_t audio_timer;

void audio_timer_callback(uv_timer_t *handle) {
    // Periyodik ses işleme
    process_audio_buffers();
}

void init_audio_timer(uv_loop_t *loop) {
    uv_timer_init(loop, &audio_timer);
    uv_timer_start(&audio_timer, audio_timer_callback, 0, 23);  // 23ms
}

// TCP sunucu (IPC için)
uv_tcp_t server;

void on_new_connection(uv_stream_t *server, int status) {
    if (status < 0) return;

    uv_tcp_t *client = malloc(sizeof(uv_tcp_t));
    uv_tcp_init(uv_default_loop(), client);

    if (uv_accept(server, (uv_stream_t *)client) == 0) {
        // Yeni bağlantı kabul edildi
        start_reading(client);
    } else {
        uv_close((uv_stream_t *)client, NULL);
    }
}

void start_tcp_server(int port) {
    uv_tcp_init(uv_default_loop(), &server);

    struct sockaddr_in addr;
    uv_ip4_addr("0.0.0.0", port, &addr);

    uv_tcp_bind(&server, (const struct sockaddr *)&addr, 0);
    uv_listen((uv_stream_t *)&server, 128, on_new_connection);
}

// Pipe ile process arası iletişim
uv_pipe_t pipe;

void on_pipe_read(uv_stream_t *stream, ssize_t nread, const uv_buf_t *buf) {
    if (nread > 0) {
        // Pipe'dan veri okundu
        process_pipe_data(buf->base, nread);
    }
    free(buf->base);
}

void init_ipc_pipe(const char *pipe_name) {
    uv_pipe_init(uv_default_loop(), &pipe, 0);
    uv_pipe_open(&pipe, 0);  // stdin

    // Named pipe oluştur (Windows)
    #ifdef _WIN32
    uv_pipe_bind(&pipe, pipe_name);
    uv_listen((uv_stream_t *)&pipe, 1, on_pipe_connection);
    #else
    uv_read_start((uv_stream_t *)&pipe, alloc_buffer, on_pipe_read);
    #endif
}

// Work queue ile CPU yoğun işlemler
typedef struct {
    uv_work_t request;
    void *data;
    size_t size;
} WorkItem;

void heavy_processing_work(uv_work_t *req) {
    WorkItem *item = (WorkItem *)req->data;
    // Ağır hesaplama
    process_heavy_data(item->data, item->size);
}

void after_processing(uv_work_t *req, int status) {
    WorkItem *item = (WorkItem *)req->data;
    // İşleme tamamlandı, UI'ı güncelle
    update_ui(item->data);
    free(item);
}

void queue_heavy_processing(void *data, size_t size) {
    WorkItem *item = malloc(sizeof(WorkItem));
    item->request.data = item;
    item->data = data;
    item->size = size;

    uv_queue_work(uv_default_loop(), &item->request,
        heavy_processing_work, after_processing);
}
```

##### 4. libevent

libevent, yüksek performanslı event notification için:

```c
#include <event2/event.h>
#include <event2/listener.h>
#include <event2/http.h>

// Event base oluşturma
struct event_base *base = event_base_new();

// HTTP sunucu
void http_request_handler(struct evhttp_request *request, void *arg) {
    const char *uri = evhttp_request_get_uri(request);

    if (strcmp(uri, "/audio/status") == 0) {
        // Ses durumu endpoint'i
        const char *response = "{\"status\":\"active\"}";
        evhttp_add_header(evhttp_request_get_output_headers(request),
            "Content-Type", "application/json");
        evhttp_send_reply(request, HTTP_OK, "OK", NULL);
    }
}

void start_http_server(int port) {
    struct evhttp *http = evhttp_new(base);
    evhttp_bind_socket(http, "0.0.0.0", port);
    evhttp_set_gencb(http, http_request_handler, NULL);
}

// Timer ile periyodik görevler
struct event *audio_timer_event;

void audio_timer_callback(evutil_socket_t fd, short events, void *arg) {
    process_audio_buffers();
}

void init_audio_timer(struct event_base *base) {
    audio_timer_event = event_new(base, -1, EV_PERSIST | EV_TIMEOUT,
        audio_timer_callback, NULL);

    struct timeval tv = {0, 23000};  // 23ms
    event_add(audio_timer_event, &tv);
}

// Signal handling
struct event *sigint_event;

void signal_callback(evutil_socket_t fd, short events, void *arg) {
    printf("Received SIGINT, shutting down...\n");
    event_base_loopexit(base, NULL);
}

void init_signal_handler(struct event_base *base) {
    sigint_event = evsignal_new(base, SIGINT, signal_callback, NULL);
    event_add(sigint_event, NULL);
}
```

##### 5. Boost.Asio (C++)

Boost.Asio, modern C++ ile asenkron I/O:

```cpp
#include <boost/asio.hpp>
#include <boost/asio/ip/tcp.hpp>
#include <boost/asio/steady_timer.hpp>

using boost::asio::ip::tcp;
using boost::asio::steady_timer;

// Asenkron TCP sunucu
class AudioServer {
public:
    AudioServer(boost::asio::io_context &io, short port)
        : acceptor_(io, tcp::endpoint(tcp::v4(), port)) {
        start_accept();
    }

private:
    void start_accept() {
        auto socket = std::make_shared<tcp::socket>(acceptor_.get_executor());
        acceptor_.async_accept(*socket,
            [this, socket](boost::system::error_code ec) {
                if (!ec) {
                    start_read(socket);
                }
                start_accept();
            });
    }

    void start_read(std::shared_ptr<tcp::socket> socket) {
        auto buffer = std::make_shared<std::array<char, 1024>>();
        socket->async_read_some(boost::asio::buffer(*buffer),
            [this, socket, buffer](boost::system::error_code ec, std::size_t length) {
                if (!ec) {
                    // Veriyi işle
                    process_audio_data(buffer->data(), length);
                    start_read(socket);
                }
            });
    }

    tcp::acceptor acceptor_;
};

// Steady timer ile periyodik görevler
class AudioProcessor {
public:
    AudioProcessor(boost::asio::io_context &io)
        : timer_(io, steady_timer::time_point::clock::now() + std::chrono::milliseconds(23)) {
        start_processing();
    }

private:
    void start_processing() {
        timer_.async_wait([this](boost::system::error_code ec) {
            if (!ec) {
                process_audio_buffers();
                // Timer'ı yeniden ayarla
                timer_.expires_at(timer_.expiry() + std::chrono::milliseconds(23));
                start_processing();
            }
        });
    }

    steady_timer timer_;
};

// Strand ile serialized execution
class AudioStrand {
public:
    AudioStrand(boost::asio::io_context &io)
        : strand_(boost::asio::make_strand(io)) {}

    template<typename F>
    void post(F&& func) {
        boost::asio::post(strand_, std::forward<F>(func));
    }

private:
    boost::asio::io_context::strand strand_;
};

// Usage
int main() {
    boost::asio::io_context io;
    AudioServer server(io, 8080);
    AudioProcessor processor(io);
    AudioStrand strand(io);

    strand.post([]() {
        // Serialized processing
    });

    io.run();
    return 0;
}
```

##### 6. Rust tokio

Rust tokio, yüksek performanslı asenkron runtime:

```rust
use tokio::net::{TcpListener, TcpStream};
use tokio::io::{AsyncReadExt, AsyncWriteExt};
use tokio::time::{interval, Duration};
use tokio::sync::broadcast;

// Async TCP sunucu
async fn start_audio_server(addr: &str) -> Result<(), Box<dyn std::error::Error>> {
    let listener = TcpListener::bind(addr).await?;

    loop {
        let (mut socket, addr) = listener.accept().await?;

        tokio::spawn(async move {
            let mut buf = [0u8; 1024];

            loop {
                match socket.read(&mut buf).await {
                    Ok(0) => return,
                    Ok(n) => {
                        // Ses verisini işle
                        let processed = process_audio_data(&buf[..n]);

                        // Yanıt gönder
                        if let Err(e) = socket.write_all(&processed).await {
                            eprintln!("Write error: {}", e);
                            return;
                        }
                    }
                    Err(e) => {
                        eprintln!("Read error: {}", e);
                        return;
                    }
                }
            }
        });
    }
}

// Async timer ile periyodik görevler
async fn audio_processing_loop() {
    let mut interval = interval(Duration::from_millis(23));  // ~44.1Hz

    loop {
        interval.tick().await;
        process_audio_buffers().await;
    }
}

// Broadcast channel ile publish/subscribe
async fn setup_audio_event_system() {
    let (tx, _) = broadcast::channel::<AudioEvent>(100);

    // Publisher
    let tx_clone = tx.clone();
    tokio::spawn(async move {
        loop {
            let event = AudioEvent::BufferProcessed {
                timestamp: std::time::SystemTime::now(),
                samples: 1024,
            };
            let _ = tx_clone.send(event);
            tokio::time::sleep(Duration::from_millis(23)).await;
        }
    });

    // Subscriber
    let mut rx = tx.subscribe();
    tokio::spawn(async move {
        while let Ok(event) = rx.recv().await {
            handle_audio_event(event);
        }
    });
}

// Async file I/O
async fn load_audio_file(path: &str) -> Result<Vec<f32>, Box<dyn std::error::Error>> {
    let contents = tokio::fs::read(path).await?;
    let samples = parse_audio_data(&contents)?;
    Ok(samples)
}

// Main
#[tokio::main]
async fn main() -> Result<(), Box<dyn std::error::Error>> {
    // Sunucuyu başlat
    tokio::spawn(async move {
        start_audio_server("0.0.0.0:8080").await.unwrap();
    });

    // Audio processing loop'unu başlat
    tokio::spawn(async move {
        audio_processing_loop().await;
    });

    // Event system'i başlat
    setup_audio_event_system().await;

    // Ana loop
    tokio::signal::ctrl_c().await?;
    Ok(())
}
```

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `API / Arayüz` — L541–L612


##### COREMUSIC Cross-Platform API Başlık Dosyası

```c
#ifndef COREMUSIC_CROSS_PLATFORM_H
#define COREMUSIC_CROSS_PLATFORM_H

#include <stdint.h>
#include <stddef.h>

// Platform Detection
#if defined(_WIN32)
    #define CM_PLATFORM_WINDOWS 1
#elif defined(__APPLE__)
    #define CM_PLATFORM_MACOS 1
#elif defined(__linux__)
    #define CM_PLATFORM_LINUX 1
#else
    #define CM_PLATFORM_UNKNOWN 1
#endif

// Threading Abstraction
typedef void* CM_Thread;
typedef void* CM_Mutex;
typedef void* CM_Condition;
typedef void* CM_Semaphore;

CM_Status CM_Thread_Create(CM_Thread *thread, void *(*func)(void*), void *arg);
CM_Status CM_Thread_Join(CM_Thread thread);
CM_Status CM_Thread_Detach(CM_Thread thread);
CM_Status CM_Thread_SetPriority(CM_Thread thread, int priority);
CM_Status CM_Thread_SetAffinity(CM_Thread thread, int core);

CM_Status CM_Mutex_Create(CM_Mutex *mutex);
CM_Status CM_Mutex_Lock(CM_Mutex mutex);
CM_Status CM_Mutex_TryLock(CM_Mutex mutex);
CM_Status CM_Mutex_Unlock(CM_Mutex mutex);
CM_Status CM_Mutex_Destroy(CM_Mutex mutex);

CM_Status CM_Condition_Create(CM_Condition *cond);
CM_Status CM_Condition_Wait(CM_Condition cond, CM_Mutex mutex);
CM_Status CM_Condition_Signal(CM_Condition cond);
CM_Status CM_Condition_Broadcast(CM_Condition cond);
CM_Status CM_Condition_Destroy(CM_Condition cond);

// I/O Abstraction
typedef void* CM_EventLoop;
typedef void* CM_Timer;
typedef void* CM_Socket;

CM_Status CM_EventLoop_Create(CM_EventLoop *loop);
CM_Status CM_EventLoop_Run(CM_EventLoop loop);
CM_Status CM_EventLoop_Stop(CM_EventLoop loop);
CM_Status CM_EventLoop_Destroy(CM_EventLoop loop);

CM_Status CM_Timer_Create(CM_EventLoop loop, CM_Timer *timer,
    void (*callback)(void*), void *user_data);
CM_Status CM_Timer_Start(CM_Timer timer, uint64_t interval_ms);
CM_Status CM_Timer_Stop(CM_Timer timer);
CM_Status CM_Timer_Destroy(CM_Timer timer);

CM_Status CM_Socket_CreateTCP(CM_Socket *socket);
CM_Status CM_Socket_Bind(CM_Socket socket, const char *addr, uint16_t port);
CM_Status CM_Socket_Listen(CM_Socket socket, int backlog);
CM_Status CM_Socket_Accept(CM_Socket server, CM_Socket *client);
CM_Status CM_Socket_Read(CM_Socket socket, void *buf, size_t len, size_t *read);
CM_Status CM_Socket_Write(CM_Socket socket, const void *buf, size_t len);
CM_Status CM_Socket_Close(CM_Socket socket);

#endif // COREMUSIC_CROSS_PLATFORM_H
```

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Bağımlılıklar` — L614–L632


##### Gereksinimler
- POSIX uyumlu sistemler (Linux, macOS)
- Windows SDK (WinAPI)
- SDL2 2.0 ve üzeri
- libuv 1.0 ve üzeri
- libevent 2.0 ve üzeri
- Boost 1.70 ve üzeri (C++ için)
- Rust 1.60 ve üzeri (tokio için)

##### Alt Katmanlar
- K0 İşletim Sistemi katmanı (platform-specific)
- Donanım sürücüleri

##### Üst Katmanlar
- K1 Ses Motoru
- K2 Ağ Katmanı
- K3 Uygulama Katmanı

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Performans Metrikleri` — L634–L642


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Thread oluşturma | < 1ms | Belirlenecek |
| Mutex lock/unlock | < 100ns | Belirlenecek |
| Event loop latency | < 10μs | Belirlenecek |
| Timer accuracy | < 1ms | Belirlenecek |
| Socket I/O throughput | > 1Gbps | Belirlenecek |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Durum: Implementasyon` — L644–L658


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. POSIX threads abstraction implementasyonu
2. libuv event loop entegrasyonu
3. SDL2 audio device yönetimi
4. Boost.Asio TCP sunucu
5. Rust tokio async runtime

**Sonraki Adımlar**:
- Thread abstraction testlerinin yazılması
- libuv event loop benchmark'larının yapılması
- SDL2 audio callback implementasyonu

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - POSIX uyumlu sistemler (Linux, macOS) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L617 |
| - Windows SDK (WinAPI) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L618 |
| - SDL2 2.0 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L619 |
| - libuv 1.0 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L620 |
| - libevent 2.0 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L621 |
| - Boost 1.70 ve üzeri (C++ için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L622 |
| - Rust 1.60 ve üzeri (tokio için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L623 |
| - K0 İşletim Sistemi katmanı (platform-specific) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L626 |
| - Donanım sürücüleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L627 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L630 |
| - K2 Ağ Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L631 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L632 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| audio_timer_event = event_new(base, -1, EV_PERSIST \| EV_TIMEOUT, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L311 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| uv_tcp_init(uv_default_loop(), client); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L197 |
| uv_tcp_init(uv_default_loop(), &server); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L208 |
| uv_pipe_init(uv_default_loop(), &pipe, 0); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L229 |
| uv_queue_work(uv_default_loop(), &item->request, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L267 |
| [this, socket](boost::system::error_code ec) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L356 |
| [this, socket, buffer](boost::system::error_code ec, std::size_t length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L367 |
| timer_.async_wait([this](boost::system::error_code ec) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L389 |
| async fn start_audio_server(addr: &str) -> Result<(), Box<dyn std::error::Error>> { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L444 |
| eprintln!("Write error: {}", e); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L462 |
| eprintln!("Read error: {}", e); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L467 |
| async fn load_audio_file(path: &str) -> Result<Vec<f32>, Box<dyn std::error::Error>> { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L513 |
| async fn main() -> Result<(), Box<dyn std::error::Error>> { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L521 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| // CPU affinity | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L51 |
| cpu_set_t cpuset; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L52 |
| CPU_ZERO(&cpuset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L53 |
| CPU_SET(config->core_affinity, &cpuset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L54 |
| pthread_attr_setaffinity_np(&attr, sizeof(cpu_set_t), &cpuset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L55 |
| Sint16 *buffer = (Sint16 *)stream; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L126 |
| buffer[i] = (Sint16)(sin(2.0 * M_PI * state->frequency * t) * 32767); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L132 |
| process_audio_buffers(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L182 |
| uv_timer_start(&audio_timer, audio_timer_callback, 0, 23);  // 23ms | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L187 |
| uv_read_start((uv_stream_t *)&pipe, alloc_buffer, on_pipe_read); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L237 |
| // Work queue ile CPU yoğun işlemler | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L241 |
| process_audio_buffers(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L307 |
| struct timeval tv = {0, 23000};  // 23ms | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L314 |
| auto buffer = std::make_shared<std::array<char, 1024>>(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L365 |
| socket->async_read_some(boost::asio::buffer(*buffer), | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L366 |
| process_audio_data(buffer->data(), length); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L370 |
| process_audio_buffers(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L391 |
| let mut interval = interval(Duration::from_millis(23));  // ~44.1Hz | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L478 |
| process_audio_buffers().await; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L482 |
| let event = AudioEvent::BufferProcessed { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L494 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - Thread abstraction testlerinin yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L656 |
| - libuv event loop benchmark'larının yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L657 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **6** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

| Belirsizlik / risk (kaynak satırı) | Kanıt |
|---|---|
| #define CM_PLATFORM_UNKNOWN 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L560 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |

## Identifier Envanteri

> Identifier'lar kaynak metinden sayım ile üretilmiştir; ilk geçtiği satır kanıt olarak verilmiştir.

| Identifier | Geçiş sayısı | İlk kanıt |
|---|---|---|
| `API` | 7 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L2 |
| `abstraction` | 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L563 |
| `portability` | 0 | [kaynakta eşleşme yok] |
| `driver-stack` | 0 | [kaynakta eşleşme yok] |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | ## Genel Bakış | L11–L13 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | ## Teknik Detaylar | L15–L539 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | ## API / Arayüz | L541–L612 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | ## Bağımlılıklar | L614–L632 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | ## Performans Metrikleri | L634–L642 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` | ## Durum: Implementasyon | L644–L658 | ✓ (verbatim) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Durum: Implementasyon` — L644–L658


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. POSIX threads abstraction implementasyonu
2. libuv event loop entegrasyonu
3. SDL2 audio device yönetimi
4. Boost.Asio TCP sunucu
5. Rust tokio async runtime

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
