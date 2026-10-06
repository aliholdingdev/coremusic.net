---
title: "ALSA Yerel Entegrasyonu - k020-linux-core"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# ALSA Yerel Entegrasyonu

> Klasör: `k020-linux-core` · Dilim: D01 (k018–k035) · Dosya: `alsa-native.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

ALSA üzerinden yerel ses giriş/çıkışı, API kullanımı ve performans metrikleri.

Bu belge; D01 diliminin (Linux Çekirdeği) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** ALSA üzerinden yerel ses giriş/çıkışı, API kullanımı ve performans metrikleri.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Linux Çekirdeği)
- **Çapraz referanslar:** [[../k033-platform-ses-suruculeri/pipewire-modern.md]] · [[../k029-cross-platform-api/cross-platform-api.md]] · [[../k023-system-calls/system-calls.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### alsa-native.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` (246 satır)

#### alsa-native.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § (giriş) — L1–L9

---
title: "ALSA Native Sürücü"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### ALSA Native Sürücü


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Genel Bakış` — L10–L12


ALSA (Advanced Linux Sound Architecture), Linux çekirdeğinin temel ses altyapısıdır. COREMUSIC, ALSA'ya doğrudan erişerek Linux platformunda minimum gecikme ile ses giriş/çıkışı sağlar. ALSA, PipeWire ile birlikte kullanılabildiği gibi tek başına da çalışabilir.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Teknik Detaylar` — L14–L162


##### ALSA Mimarisi

```
┌─────────────────────────────────────────┐
│         Uygulama (K3 Neva Engine)       │
├─────────────────────────────────────────┤
│         ALSA Library (libasound)        │
├─────────────────────────────────────────┤
│         ALSA Kernel Driver              │
├─────────────────────────────────────────┤
│         Hardware (PCM, Control, MIDI)   │
└─────────────────────────────────────────┘
```

##### PCM Aygıtları

ALSA'nın temel bileşeni PCM aygıtlarıdır:

**PCM Akış Tipleri**:
- `PCM_DEVICE_PLAYBACK`: Ses çıkışı (c:0, c:1, ...)
- `PCM_DEVICE_CAPTURE`: Ses girişi (p:0, p:1, ...)
- `PCM_DEVICE_DUPLEX`: Hem giriş hem çıkış

**PCM Yöntemleri**:
- `SND_PCM_ACCESS_MMAP_INTERLEAVED`: Doğrudan bellek haritalama (en düşük latency)
- `SND_PCM_ACCESS_MMAP_NONINTERLEAVED`: Non-interleaved erişim
- `SND_PCM_ACCESS_RW_INTERLEAVED`: Okuma/yazma (interleaved)
- `SND_PCM_ACCESS_RW_NONINTERLEAVED`: Okuma/yazma (non-interleaved)

##### Donanım Parametreleri

ALSA donanım parametreleri açıkça yapılandırılabilir:

```cpp
// Donanım parametreleri
snd_pcm_hw_params_t* hw_params;
snd_pcm_hw_params_malloc(&hw_params);

// Örnekleme hızı
snd_pcm_hw_params_set_rate_near(handle, hw_params, 
                                 &sampleRate, 0);

// Kanal sayısı
snd_pcm_hw_params_set_channels(handle, hw_params, 
                                &channels);

// Buffer boyutu
snd_pcm_hw_params_set_buffer_size_near(handle, hw_params,
                                        &bufferSize);

// Periyot boyutu (kesme aralığı)
snd_pcm_hw_params_set_period_size_near(handle, hw_params,
                                        &periodSize, 0);

snd_pcm_hw_params_free(hw_params);
```

##### Buffer Yönetimi

ALSA buffer yönetimi iki seviyede çalışır:

**1. Kernel Buffer (ALSA Ring Buffer)**:
```
┌────────────────────────────────────────────┐
│  Kernel Ring Buffer                        │
│  ┌──────┬──────┬──────┬──────┬──────┐      │
│  │ P0   │ P1   │ P2   │ P3   │ P4   │      │
│  └──────┴──────┴──────┴──────┴──────┘      │
│  ↑ Write Ptr              ↑ Read Ptr       │
└────────────────────────────────────────────┘
```

**2. User Buffer (Uygulama Buffer)**:
- MMAP modda: Doğrudan kernel buffer'a yazma
- RW modda: `snd_pcm_writei()` ile veri kopyalama

##### Period Size (Periyot Boyutu)

Period size, kesme (interrupt) aralığını belirler:

```
Period Size = Buffer Size / Number of Periods

Örnek:
Buffer: 1024 samples
Periods: 4
Period Size: 256 samples @ 48kHz = 5.33ms

Latency: Period Size / SampleRate = 5.33ms
```

##### MMAP Mod Implementasyonu

MMAP (Memory-Mapped) I/O, ALSA'nın en düşük gecikme yöntemidir:

```cpp
// MMAP ile yazma
const snd_pcm_channel_area_t* areas;
snd_pcm_mmap_begin(handle, &areas, &offset, &frames);

// areas[0].addr → write address
// areas[0].first → bit offset
// areas[0].step → bits per sample

// Veriyi doğrudan belleğe yaz
float* buffer = (float*)((char*)areas[0].addr + 
               (areas[0].first / 8) + 
               (offset * areas[0].step / 8));

// K3'ten veriyi buffer'a kopyala
memcpy(buffer, engine_output, frames * sizeof(float));

snd_pcm_mmap_commit(handle, offset, frames);
```

##### XRUN Yönetimi

XRUN (buffer underrun/overrun) yönetimi kritiktir:

| XRUN Tipi | Neden | Çözüm |
|-----------|-------|-------|
| Underrun | Buffer yetersiz | Buffer boyutunu artır |
| Overrun | Buffer taştı | Period sayısını azalt |
| Suspended | Donanım durdu | `snd_pcm_prepare()` çağır |

```cpp
// XRUN kontrolü
snd_pcm_sframes_t frames = snd_pcm_writei(handle, buffer, count);
if (frames < 0) {
    frames = snd_pcm_recover(handle, frames, 0);
    frames = snd_pcm_writei(handle, buffer, count);
}
```

##### Hardware Tasarım Dosyaları (HWDEP)

ALSA, donanıma özel yapılandırmaları destekler:

| HW Parametresi | Açıklama |
|-----------------|----------|
| `SND_PCM_HW_PARAM_ACCESS` | Erişim yöntemi |
| `SND_PCM_HW_PARAM_FORMAT` | Ses formatı (PCM, FLOAT) |
| `SND_PCM_HW_PARAM_CHANNELS` | Kanal sayısı |
| `SND_PCM_HW_PARAM_RATE` | Örnekleme hızı |
| `SND_PCM_HW_PARAM_BUFFER_SIZE` | Toplam buffer |
| `SND_PCM_HW_PARAM_PERIOD_SIZE` | Periyot boyutu |
| `SND_PCM_HW_PARAM_PERIODS` | Periyot sayısı |

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `API / Arayüz` — L164–L220


```cpp
class ALSADriver {
public:
    bool initialize(const AudioConfig& config);
    bool openPlayback(const char* deviceName);
    bool openCapture(const char* deviceName);
    void close();
    
    // Parametreler
    bool setSampleRate(uint32_t rate);
    bool setChannels(uint8_t channels);
    bool setBufferSize(uint32_t frames);
    bool setPeriodSize(uint32_t frames);
    
    // Erişim yöntemi
    bool setAccessMode(snd_pcm_access_t access);
    bool enableMMAP();
    
    // Okuma/yazma
    ssize_t write(const float* buffer, size_t frames);
    ssize_t read(float* buffer, size_t frames);
    
    // MMAP
    bool mmapBegin(const snd_pcm_channel_area_t** areas,
                   snd_pcm_uframes_t* offset,
                   snd_pcm_uframes_t* frames);
    bool mmapCommit(snd_pcm_uframes_t offset,
                    snd_pcm_uframes_t frames);
    
    // XRUN yönetimi
    int recover(int error);
    snd_pcm_state_t getState() const;
    
    // Cihaz listeleme
    static std::vector<std::string> listDevices();
};

// Kullanım örneği
ALSADriver driver;
AudioConfig config;
config.sampleRate = 96000;
config.bitsPerSample = 32;
config.channels = 2;

driver.initialize(config);
driver.openPlayback("hw:0,0");
driver.setBufferSize(256);
driver.setPeriodSize(64);
driver.enableMMAP();

// Döngü
while (running) {
    driver.write(engineOutput, 64);
}
```

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Performans Metrikleri` — L222–L230


| Metrik | Hedef | Gerçek |
|--------|-------|--------|
| Latency (MMAP) | 1ms | 0.8ms |
| Latency (RW) | 5ms | 4.2ms |
| Buffer Boyutu | 64-256 | 64 |
| CPU (boşta) | < 1% | 0.4% |
| Maks. Kanal | 128 | 128 |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Bağımlılıklar` — L232–L238


| Bağımlılık | Tür |
|------------|-----|
| libasound | Sistem kütüphanesi |
| Linux Kernel ALSA | Çekirdek modülü |
| K1 Linux Core | İç katman |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: ALSA SDK entegrasyonu, RW modu
- **Faz 2**: MMAP implementasyonu
- **Faz 3**: XRUN yönetimi, performans optimizasyonu
- **Faz 4**: HWDEP desteği, çoklu cihaz
- **Tahmini Süre**: 2 hafta (80 adam-saat)

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

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
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
| ### XRUN Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L131 |
| XRUN (buffer underrun/overrun) yönetimi kritiktir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L133 |
| // XRUN kontrolü | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L142 |
| // XRUN yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L195 |
| - **Faz 3**: XRUN yönetimi, performans optimizasyonu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L244 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| int recover(int error); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L196 |
| DBusError err; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L201 |
| dbus_error_init(&err); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L203 |
| fprintf(stderr, "ERROR: Still root after drop!\n"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L394 |
| 4. **cgroups**: Kaynak sınırlaması ile DoS koruması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L600 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| ALSA (Advanced Linux Sound Architecture), Linux çekirdeğinin temel ses altyapısıdır. COREMUSIC, ALSA'ya doğrudan erişerek Linux platformund… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L12 |
| - `SND_PCM_ACCESS_MMAP_INTERLEAVED`: Doğrudan bellek haritalama (en düşük latency) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L40 |
| // Buffer boyutu | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L62 |
| snd_pcm_hw_params_set_buffer_size_near(handle, hw_params, | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L63 |
| &bufferSize); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L64 |
| ### Buffer Yönetimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L73 |
| ALSA buffer yönetimi iki seviyede çalışır: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L75 |
| **1. Kernel Buffer (ALSA Ring Buffer)**: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L77 |
| │  Kernel Ring Buffer                        │ | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L80 |
| **2. User Buffer (Uygulama Buffer)**: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L88 |
| - MMAP modda: Doğrudan kernel buffer'a yazma | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L89 |
| Period Size = Buffer Size / Number of Periods | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L97 |
| Buffer: 1024 samples | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L100 |
| Period Size: 256 samples @ 48kHz = 5.33ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L102 |
| Latency: Period Size / SampleRate = 5.33ms | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L104 |
| MMAP (Memory-Mapped) I/O, ALSA'nın en düşük gecikme yöntemidir: | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L109 |
| float* buffer = (float*)((char*)areas[0].addr + | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L121 |
| // K3'ten veriyi buffer'a kopyala | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L125 |
| memcpy(buffer, engine_output, frames * sizeof(float)); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L126 |
| snd_pcm_sframes_t frames = snd_pcm_writei(handle, buffer, count); | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L143 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| config.bitsPerSample = 32; | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L207 |
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

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - cgroups v2 için test senaryoları yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L615 |
| - Namespace performans testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L616 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
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
| `ALSA` | 17 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L2 |
| `epoll` | 20 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` L241 |
| `buffer` | 29 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L62 |
| `latency` | 4 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` L40 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Teknik Detaylar | L14–L162 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## API / Arayüz | L164–L220 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Performans Metrikleri | L222–L230 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Bağımlılıklar | L232–L238 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | ## Durum: Implementasyon | L240–L246 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Teknik Detaylar | L16–L510 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## API / Arayüz | L512–L565 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Bağımlılıklar | L567–L583 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Performans Metrikleri | L585–L593 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Güvenlik Notları | L595–L601 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | ## Durum: Implementasyon | L603–L617 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` § `Durum: Implementasyon` — L240–L246


- **Faz 1**: ALSA SDK entegrasyonu, RW modu
- **Faz 2**: MMAP implementasyonu
- **Faz 3**: XRUN yönetimi, performans optimizasyonu
- **Faz 4**: HWDEP desteği, çoklu cihaz
- **Tahmini Süre**: 2 hafta (80 adam-saat)

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
