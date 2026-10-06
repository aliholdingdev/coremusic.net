---
title: "Platform Soyutlama Katmanı - k029-cross-platform-api"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Platform Soyutlama Katmanı

> Klasör: `k029-cross-platform-api` · Dilim: D01 (k018–k035) · Dosya: `platform-soyutlama-katmani.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Soyutlama katmanının sınırı, platform-specific kaçakların denetimi.

Bu belge; D01 diliminin (Çapraz Platform API) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Soyutlama katmanının sınırı, platform-specific kaçakların denetimi.
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

### linux-core.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` (617 satır)

#### linux-core.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` § (giriş) — L1–L11

---
title: "Linux Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "Linux"
date: 2026-09-20
version: 1.0.1
---

### Linux Core


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` § `Teknik Detaylar` — L16–L510


##### 1. Control Groups (cgroups)

cgroups, kaynak izleme ve sınırlama için kullanılır:

```c
// cgroup oluşturma ve yapılandırma
#include <sys/stat.h>
#include <fcntl.h>
#include <stdio.h>

#define CGROUP_PATH "/sys/fs/cgroup/coremusic"

void setup_cgroup(pid_t pid) {
    // cgroup dizini oluştur
    mkdir(CGROUP_PATH, 0755);

    // CPU limiti ayarla
    FILE *f = fopen(CGROUP_PATH "/cpu.max", "w");
    fprintf(f, "50000 100000");  // 50% CPU
    fclose(f);

    // Memory limiti ayarla
    f = fopen(CGROUP_PATH "/memory.max", "w");
    fprintf(f, "536870912");  // 512MB
    fclose(f);

    // IO sınırlaması
    f = fopen(CGROUP_PATH "/io.max", "w");
    fprintf(f, "8:0 rbps=104857600 wbps=104857600");  // 100MB/s
    fclose(f);

    // Process'i cgroup'a ekle
    f = fopen(CGROUP_PATH "/cgroup.procs", "w");
    fprintf(f, "%d", pid);
    fclose(f);
}

// cgroup v2 ile gelişmiş kaynak yönetimi
void setup_cgroup_v2(pid_t pid) {
    // CPU weight (ağırlık)
    FILE *f = fopen(CGROUP_PATH "/cpu.weight", "w");
    fprintf(f, "100");  // 10-1000 arası
    fclose(f);

    // Memory pressure monitoring
    f = fopen(CGROUP_PATH "/memory.pressure", "w");
    fprintf(f, "some avg10=0.1 avg60=0.05 avg300=0.01");
    fclose(f);

    // PSI (Pressure Stall Information) izleme
    f = fopen(CGROUP_PATH "/cpu.pressure", "w");
    fprintf(f, "some avg10=0.1 avg60=0.05 avg300=0.01");
    fclose(f);
}
```

##### 2. Namespaces

Linux namespaces, process izolasyonu için kullanılır:

```c
#define _GNU_SOURCE
#include <sched.h>
#include <sys/wait.h>

#define STACK_SIZE (1024 * 1024)

// PID namespace ile process izolasyonu
static int child_func(void *arg) {
    printf("Child PID: %d\n", getpid());
    // Yeni PID namespace'inde çalışır
    execl("/bin/bash", "bash", NULL);
    return 0;
}

void create_isolated_process() {
    char *stack = malloc(STACK_SIZE);
    if (!stack) return;

    pid_t pid = clone(child_func,
        stack + STACK_SIZE,
        CLONE_NEWPID | CLONE_NEWNS | CLONE_NEWNET,
        NULL);

    waitpid(pid, NULL, 0);
    free(stack);
}

// Network namespace ile ağ izolasyonu
void create_network_namespace() {
    // Yeni namespace oluştur
    unshare(CLONE_NEWNET);

    // Loopback arayüzünü yapılandır
    system("ip link set lo up");
    system("ip addr add 127.0.0.1/8 dev lo");

    // Yeni ağ arayüzü oluştur
    system("ip link add veth0 type veth peer name veth1");
    system("ip link set veth1 up");
    system("ip addr add 10.0.0.1/24 dev veth0");
}

// Mount namespace ile dosya sistemi izolasyonu
void create_mount_namespace() {
    unshare(CLONE_NEWNS);

    // tmpfs mount
    mount("tmpfs", "/tmp/coremusic", "tmpfs", 0, "size=1G");

    // /proc mount
    mount("proc", "/proc", "proc", 0, NULL);
}
```

##### 3. systemd Integration

systemd, servis yönetimi ve otomatik başlatma için:

```c
// systemd servis dosyası oluşturma
void create_systemd_service() {
    FILE *f = fopen("/etc/systemd/system/coremusic.service", "w");
    fprintf(f,
        "[Unit]\n"
        "Description=COREMUSIC Audio Processing Service\n"
        "After=network.target sound.target\n"
        "Requires=sound.target\n"
        "\n"
        "[Service]\n"
        "Type=simple\n"
        "ExecStart=/usr/bin/coremusic daemon\n"
        "Restart=always\n"
        "RestartSec=5\n"
        "User=coremusic\n"
        "Group=audio\n"
        "Nice=-20\n"
        "CPUSchedulingPolicy=realtime\n"
        "MemoryMax=1G\n"
        "CPUQuota=50%%\n"
        "LimitRTPRIO=99\n"
        "LimitRTTIME=infinity\n"
        "\n"
        "[Install]\n"
        "WantedBy=multi-user.target\n"
    );
    fclose(f);
}

// D-Bus ile systemd iletişimi
void restart_service_via_dbus() {
    // D-Bus ile servis yeniden başlatma
    DBusConnection *conn;
    dbus_connection_bus_get(DBUS_BUS_SYSTEM, &conn);

    DBusMessage *msg = dbus_message_new_method_call(
        "org.freedesktop.systemd1",
        "/org/freedesktop/systemd1",
        "org.freedesktop.systemd1.Manager",
        "RestartUnit"
    );

    const char *unit = "coremusic.service";
    const char *mode = "replace";
    dbus_message_append_args(msg,
        DBUS_TYPE_STRING, &unit,
        DBUS_TYPE_STRING, &mode,
        DBUS_TYPE_INVALID);

    DBusMessage *reply = dbus_connection_send_with_reply_and_block(conn, msg, -1, NULL);
    dbus_message_unref(msg);
    dbus_message_unref(reply);
}
```

##### 4. D-Bus Communication

D-Bus, process arası iletişim ve sistem servisleri için:

```c
// D-Bus sunucu oluşturma
void setup_dbus_server() {
    DBusConnection *conn;
    DBusError err;

    dbus_error_init(&err);
    conn = dbus_bus_get(DBUS_BUS_SESSION, &err);

    // Benzersiz isim talep et
    dbus_bus_request_name(conn, "org.coremusic.AudioService",
        DBUS_NAME_FLAG_REPLACE_EXISTING, &err);

    // Sinyal dinleme
    dbus_connection_add_filter(conn, signal_handler, NULL, NULL);

    DBusMessage *msg = dbus_connection_pop_message(conn);
    if (msg) {
        if (dbus_message_is_method_call(msg, "org.coremusic.AudioService", "ProcessAudio")) {
            // Audio işleme metodunu çağır
            process_audio_method(msg, conn);
        }
        dbus_message_unref(msg);
    }
}

// D-Bus ile sinyal gönderme
void send_audio_status(DBusConnection *conn, const char *status) {
    DBusMessage *signal = dbus_message_new_signal(
        "/org/coremusic/AudioService",
        "org.coremusic.AudioService",
        "StatusChanged"
    );

    dbus_message_append_args(signal,
        DBUS_TYPE_STRING, &status,
        DBUS_TYPE_INVALID);

    dbus_connection_send(conn, signal, NULL);
    dbus_connection_flush(conn);
    dbus_message_unref(signal);
}
```

##### 5. epoll - Event Notification

epoll, yüksek performanslı I/O çoklama için:

```c
#include <sys/epoll.h>
#include <unistd.h>

#define MAX_EVENTS 10

void setup_epoll(int *fds, int count) {
    int epoll_fd = epoll_create1(0);

    struct epoll_event ev;
    for (int i = 0; i < count; i++) {
        ev.events = EPOLLIN | EPOLLET;  // Edge-triggered
        ev.data.fd = fds[i];
        epoll_ctl(epoll_fd, EPOLL_CTL_ADD, fds[i], &ev);
    }

    struct epoll_event events[MAX_EVENTS];
    while (running) {
        int nfds = epoll_wait(epoll_fd, events, MAX_EVENTS, -1);
        for (int i = 0; i < nfds; i++) {
            if (events[i].events & EPOLLIN) {
                // Okunabilir veri var
                handle_readable(events[i].data.fd);
            }
            if (events[i].events & EPOLLOUT) {
                // Yazılabilir alan var
                handle_writable(events[i].data.fd);
            }
        }
    }

    close(epoll_fd);
}
```

##### 6. inotify - Dosya Sistemi İzleme

inotify, dosya sistemi değişikliklerini izlemek için:

```c
#include <sys/inotify.h>
#include <unistd.h>

void watch_config_directory() {
    int inotify_fd = inotify_init();
    int watch_descriptor = inotify_add_watch(inotify_fd,
        "/etc/coremusic",
        IN_CREATE | IN_DELETE | IN_MODIFY | IN_MOVED_FROM | IN_MOVED_TO);

    char buffer[4096];
    while (running) {
        int length = read(inotify_fd, buffer, sizeof(buffer));
        int i = 0;

        while (i < length) {
            struct inotify_event *event = (struct inotify_event *)&buffer[i];
            if (event->len) {
                if (event->mask & IN_CREATE) {
                    printf("File created: %s\n", event->name);
                }
                if (event->mask & IN_MODIFY) {
                    printf("File modified: %s\n", event->name);
                }
                if (event->mask & IN_DELETE) {
                    printf("File deleted: %s\n", event->name);
                }
            }
            i += sizeof(struct inotify_event) + event->len;
        }
    }

    inotify_rm_watch(inotify_fd, watch_descriptor);
    close(inotify_fd);
}
```

##### 7. seccomp - System Call Filtering

seccomp, güvenlik için system call sınırlaması:

```c
#include <seccomp.h>

void setup_seccomp() {
    scmp_filter_ctx ctx = seccomp_init(SCMP_ACT_KILL);

    // İzin verilen system call'ları ekle
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(read), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(write), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(open), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(close), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(mmap), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(mprotect), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(ioctl), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(epoll_wait), 0);

    // Socket operation izni
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(socket), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(bind), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(listen), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(accept), 0);

    // Filtreyi uygula
    seccomp_load(ctx);
    seccomp_release(ctx);
}
```

##### 8. Linux Capabilities

Linux capabilities, root yetkileri için ince ayar:

```c
#include <sys/capability.h>
#include <unistd.h>

void setup_capabilities() {
    cap_t caps = cap_get_proc();

    // Gereksiz yetkileri kaldır
    cap_clear(caps);

    // Sadece gerekli yetkileri ekle
    cap_set_flag(caps, CAP_EFFECTIVE, 1, CAP_NET_BIND_SERVICE, CAP_SET);
    cap_set_flag(caps, CAP_PERMITTED, 1, CAP_NET_BIND_SERVICE, CAP_SET);

    // RT öncelik yetkisi
    cap_set_flag(caps, CAP_EFFECTIVE, 1, CAP_SYS_NICE, CAP_SET);
    cap_set_flag(caps, CAP_PERMITTED, 1, CAP_SYS_NICE, CAP_SET);

    // Memory locked pages yetkisi
    cap_set_flag(caps, CAP_EFFECTIVE, 1, CAP_IPC_LOCK, CAP_SET);
    cap_set_flag(caps, CAP_PERMITTED, 1, CAP_IPC_LOCK, CAP_SET);

    cap_set_proc(caps);
    cap_free(caps);
}

// Yetki düşürme (privilege dropping)
void drop_privileges(uid_t uid, gid_t gid) {
    // Set groups first
    setgroups(0, NULL);

    // Set GID then UID (tersten)
    setgid(gid);
    setuid(uid);

    // Yetki düşürdükten sonra kontrol
    if (setuid(0) == 0) {
        fprintf(stderr, "ERROR: Still root after drop!\n");
        exit(1);
    }
}
```

##### 9. tmpfs - Geçici Dosya Sistemi

tmpfs, bellek tabanlı geçici dosya depolama:

```c
#include <sys/mount.h>

void setup_tmpfs() {
    // tmpfs mount
    mount("tmpfs", "/tmp/coremusic", "tmpfs",
        MS_NOEXEC | MS_NOSUID | MS_NODEV,
        "size=2G,mode=0755,uid=1000,gid=1000");

    // Huge pages tmpfs (büyük audio buffer'lar için)
    mount("tmpfs", "/dev/hugepages", "tmpfs",
        MS_NOEXEC | MS_NOSUID | MS_NODEV,
        "size=4G,mode=0755");

    // Sysfs mount
    mount("sysfs", "/sys", "sysfs", 0, NULL);
}
```

##### 10. /proc Filesystem

/proc, process ve sistem bilgileri için:

```c
#include <stdio.h>
#include <stdlib.h>

void read_process_info(pid_t pid) {
    char path[256];
    char line[256];
    FILE *f;

    // CPU bilgisi
    snprintf(path, sizeof(path), "/proc/%d/stat", pid);
    f = fopen(path, "r");
    if (f) {
        fgets(line, sizeof(line), f);
        // CPU time bilgilerini parse et
        fclose(f);
    }

    // Bellek bilgisi
    snprintf(path, sizeof(path), "/proc/%d/status", pid);
    f = fopen(path, "r");
    if (f) {
        while (fgets(line, sizeof(line), f)) {
            if (strncmp(line, "VmRSS:", 6) == 0) {
                printf("RSS: %s", line + 6);
            }
            if (strncmp(line, "VmSize:", 7) == 0) {
                printf("Virtual: %s", line + 7);
            }
        }
        fclose(f);
    }

    // IO bilgisi
    snprintf(path, sizeof(path), "/proc/%d/io", pid);
    f = fopen(path, "r");
    if (f) {
        while (fgets(line, sizeof(line), f)) {
            printf("%s", line);
        }
        fclose(f);
    }
}

// Sistem bilgileri
void read_system_info() {
    FILE *f;

    // CPU bilgisi
    f = fopen("/proc/cpuinfo", "r");
    if (f) {
        char line[256];
        while (fgets(line, sizeof(line), f)) {
            if (strncmp(line, "model name", 10) == 0) {
                printf("CPU: %s", line + 13);
                break;
            }
        }
        fclose(f);
    }

    // Bellek bilgisi
    f = fopen("/proc/meminfo", "r");
    if (f) {
        char line[256];
        while (fgets(line, sizeof(line), f)) {
            if (strncmp(line, "MemTotal:", 9) == 0) {
                printf("Total Memory: %s", line + 10);
                break;
            }
        }
        fclose(f);
    }

    // Kernel version
    f = fopen("/proc/version", "r");
    if (f) {
        char line[256];
        fgets(line, sizeof(line), f);
        printf("Kernel: %s", line);
        fclose(f);
    }
}
```

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` § `Bağımlılıklar` — L567–L583


##### Gereksinimler
- Linux Kernel 5.4 ve üzeri
- systemd 245 ve üzeri
- D-Bus 1.12 ve üzeri
- libseccomp 2.4 ve üzeri

##### Alt Katmanlar
- ALSA (Advanced Linux Sound Architecture)
- PulseAudio / PipeWire
- Linux kernel ses alt sistemi

##### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

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
| - Linux Kernel 5.4 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L570 |
| - systemd 245 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L571 |
| - D-Bus 1.12 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L572 |
| - libseccomp 2.4 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L573 |
| - ALSA (Advanced Linux Sound Architecture) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L576 |
| - PulseAudio / PipeWire | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L577 |
| - Linux kernel ses alt sistemi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L578 |
| - Cross-Platform API soyutlama katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L581 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L582 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L583 |

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
| DBusError err; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L201 |
| dbus_error_init(&err); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L203 |
| fprintf(stderr, "ERROR: Still root after drop!\n"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L394 |
| 4. **cgroups**: Kaynak sınırlaması ile DoS koruması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L600 |

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
| Linux Core modülü, COREMUSIC'ın Linux platformu için temel işletim sistemi işlevlerini sağlar. Bu modül, Linux kernel'inin gelişmiş özellik… | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L14 |
| ### 2. Namespaces | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L74 |
| Linux namespaces, process izolasyonu için kullanılır: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L76 |
| // PID namespace ile process izolasyonu | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L85 |
| // Yeni PID namespace'inde çalışır | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L88 |
| // Network namespace ile ağ izolasyonu | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L106 |
| void create_network_namespace() { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L107 |
| // Yeni namespace oluştur | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L108 |
| // Mount namespace ile dosya sistemi izolasyonu | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L121 |
| void create_mount_namespace() { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L122 |
| ### 7. seccomp - System Call Filtering | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L321 |
| seccomp, güvenlik için system call sınırlaması: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L323 |
| #include <seccomp.h> | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L326 |
| void setup_seccomp() { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L328 |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - Thread abstraction testlerinin yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L656 |
| - libuv event loop benchmark'larının yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L657 |
| - cgroups v2 için test senaryoları yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L615 |
| - Namespace performans testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L616 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **11** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

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
| `abstraction` | 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L563 |
| `API` | 10 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` L2 |
| `ALSA` | 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L576 |
| `fallback` | 0 | [kaynakta eşleşme yok] |

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
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Teknik Detaylar | L16–L510 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## API / Arayüz | L512–L565 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Bağımlılıklar | L567–L583 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Performans Metrikleri | L585–L593 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Güvenlik Notları | L595–L601 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Durum: Implementasyon | L603–L617 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/cross-platform-api.md` § `Durum: Implementasyon` — L644–L658


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. POSIX threads abstraction implementasyonu
2. libuv event loop entegrasyonu
3. SDL2 audio device yönetimi
4. Boost.Asio TCP sunucu
5. Rust tokio async runtime

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` § `Durum: Implementasyon` — L603–L617


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. cgroups v2 entegrasyonu
2. Namespace tabanlı process izolasyonu
3. epoll ile high-performance I/O
4. seccomp-bpf ile güvenlik sınırlaması
5. systemd servis yönetimi

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
