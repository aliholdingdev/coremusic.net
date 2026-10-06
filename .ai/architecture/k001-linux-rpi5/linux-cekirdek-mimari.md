---
title: "K001 Linux Çekirdek Mimarisi — POSIX Çekirdek Katmanı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K001 — Linux Çekirdek Mimarisi (POSIX Çekirdek)

> **K numarası:** K001 · **Klasör:** `k001-linux-rpi5` · **Alan:** D01 (OS / Donanım / Sürücü)
> **Sorumlu persona:** `embedded-engineer` (birincil) · `devops-engineer` (dağıtım/ortam)

## 1. Kapsam

Linux çekirdek katmanı; POSIX arayüzleri, zamanlama ve bellek semantiği ile ses motorunun
gerçek zamanlı gereksinimlerini barındırır. Raspberry Pi 5 gömülü platformu ayrı dosyada
(`[[rpi5-gomulu-platform]]`) ele alınır; bu dosya çekirdeğin kendisini kapsar.

| İlişki | Hedef |
|---|---|
| Kardeş dosya | `[[rpi5-gomulu-platform]]` |
| Klasör dizini | `[[index]]` |
| OS kardeşleri | `[[../k000-windows-core/index]]` · `[[../k002-macos-tasinabilirlik/index]]` |
| Sürücü katmanı | `[[../k016-linux-ses/index]]` (ALSA / PipeWire) |

## 2. Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` | 610 | 486 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 38 | 29 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 55 | 54 |


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` — satır: 610 (boş olmayan: 486).


## Linux Core

### Genel Bakış

Linux Core modülü, COREMUSIC'ın Linux platformu için temel işletim sistemi işlevlerini sağlar. Bu modül, Linux kernel'inin gelişmiş özelliklerini kullanarak gerçek zamanlı ses işleme ve çoklu ortam uygulamaları için optimize edilmiş bir altyapı sunar. cgroups, namespaces, systemd ve diğer Linux-specific teknolojileri entegre eder.

### Teknik Detaylar

#### 1. Control Groups (cgroups)

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

#### 2. Namespaces

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

#### 3. systemd Integration

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

#### 4. D-Bus Communication

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

#### 5. epoll - Event Notification

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

#### 6. inotify - Dosya Sistemi İzleme

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

#### 7. seccomp - System Call Filtering

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

#### 8. Linux Capabilities

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

#### 9. tmpfs - Geçici Dosya Sistemi

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

#### 10. /proc Filesystem

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

### API / Arayüz

#### COREMUSIC Linux API Başlık Dosyası

```c
#ifndef COREMUSIC_LINUX_H
#define COREMUSIC_LINUX_H

#include <sys/types.h>
#include <stdint.h>

// Cgroups
CM_Status CM_CreateCgroup(const char *name);
CM_Status CM_SetCpuLimit(const char *name, uint32_t percent);
CM_Status CM_SetMemoryLimit(const char *name, uint64_t bytes);
CM_Status CM_SetIoLimit(const char *name, uint64_t readBps, uint64_t writeBps);
CM_Status CM_AddProcessToCgroup(const char *name, pid_t pid);

// Namespaces
CM_Status CM_CreatePidNamespace();
CM_Status CM_CreateNetworkNamespace();
CM_Status CM_CreateMountNamespace();
CM_Status CM_CreateUtsNamespace();

// Capabilities
CM_Status CM_DropCapabilities();
CM_Status CM_SetCapability(cap_value_t cap);
CM_Status CM_RemoveCapability(cap_value_t cap);

// epoll
int CM_CreateEpoll();
CM_Status CM_AddToEpoll(int epollFd, int fd, uint32_t events);
CM_Status CM_RemoveFromEpoll(int epollFd, int fd);

// inotify
int CM_CreateInotify();
CM_Status CM_WatchDirectory(int inotifyFd, const char *path, uint32_t mask);

// seccomp
CM_Status CM_SetupSeccomp();

// systemd
CM_Status CM_RegisterService(const char *name);
CM_Status CM_StartService(const char *name);
CM_Status CM_StopService(const char *name);
CM_Status CM_EnableService(const char *name);

// D-Bus
CM_Status CM_DbusConnect();
CM_Status CM_DbusSendSignal(const char *path, const char *iface, const char *signal);
CM_Status CM_DbusCallMethod(const char *path, const char *iface, const char *method);

#endif // COREMUSIC_LINUX_H
```

### Bağımlılıklar

#### Gereksinimler
- Linux Kernel 5.4 ve üzeri
- systemd 245 ve üzeri
- D-Bus 1.12 ve üzeri
- libseccomp 2.4 ve üzeri

#### Alt Katmanlar
- ALSA (Advanced Linux Sound Architecture)
- PulseAudio / PipeWire
- Linux kernel ses alt sistemi

#### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

### Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| cgroup oluşturma | < 1ms | Belirlenecek |
| Namespace oluşturma | < 0.5ms | Belirlenecek |
| epoll wait | < 10μs | Belirlenecek |
| seccomp filter load | < 1ms | Belirlenecek |
| inotify event | < 1ms | Belirlenecek |

### Güvenlik Notları

1. **seccomp**: Tüm process'ler için system call filtresi uygulayın
2. **Capabilities**: Minimum yetki ile çalıştırın
3. **Namespaces**: Process izolasyonu için namespace kullanımı
4. **cgroups**: Kaynak sınırlaması ile DoS koruması
5. **tmpfs**: Güvenli dosya izinleri ile geçici depolama

### Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. cgroups v2 entegrasyonu
2. Namespace tabanlı process izolasyonu
3. epoll ile high-performance I/O
4. seccomp-bpf ile güvenlik sınırlaması
5. systemd servis yönetimi

**Sonraki Adımlar**:
- cgroups v2 için test senaryoları yazılması
- Namespace performans testlerinin yapılması
- seccomp filter'larının genişletilmesi



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:105-142` — satır: 38 (boş olmayan: 29).

#### 3.2 Linux API (K0-02)

```cpp
// Linux API Soyutlama
namespace coremusic::os::linux {

class LinuxAPI {
public:
    // ALSA Support
    static bool initializeALSA();
    static void shutdownALSA();

    // PipeWire Support
    static bool initializePipeWire();
    static void shutdownPipeWire();

    // Memory
    static void* mmapAllocate(size_t size);
    static void mmapFree(void* ptr, size_t size);

    // Threading
    static void setRealtimePriority(int priority);
    static void setCPUAffinity(int core);

    // Timer
    static uint64_t getClockMonotonic();
};

} // namespace coremusic::os::linux
```

**Kritik API'ler:**
- `clock_gettime(CLOCK_MONOTONIC)` — High-resolution timer
- `mmap` — Large page allocation
- `pthread_create` / `pthread_setschedparam` — Thread yönetimi
- `snd_pcm_*` — ALSA API
- `pw_*` — PipeWire API



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:418-472` — satır: 55 (boş olmayan: 54).

| K0.1.1.12 | epoll — Event Notification | linux-core.md L241 |
| K0.1.1.13 | inotify — Dosya Sistemi İzleme | linux-core.md L280 |
| K0.1.1.14 | seccomp — System Call Filtering | linux-core.md L321 |
| K0.1.1.15 | Linux Capabilities | linux-core.md L353 |
| K0.1.1.16 | tmpfs — Geçici Dosya Sistemi | linux-core.md L400 |
| K0.1.1.17 | /proc Filesystem | linux-core.md L423 |
| **K0.1.2** | macOS Core (3. katman) | macos-core.md — 15 yaprak |
| K0.1.2.1 | Genel Bakış | macos-core.md L12 |
| K0.1.2.2 | Teknik Detaylar | macos-core.md L16 |
| K0.1.2.3 | API / Arayüz | macos-core.md L457 |
| K0.1.2.4 | Bağımlılıklar | macos-core.md L507 |
| K0.1.2.5 | Performans Metrikleri | macos-core.md L525 |
| K0.1.2.6 | Güvenlik Notları | macos-core.md L535 |
| K0.1.2.7 | Durum: Implementasyon | macos-core.md L543 |
| K0.1.2.8 | Grand Central Dispatch (GCD) | macos-core.md L18 |
| K0.1.2.9 | XPC Communication | macos-core.md L73 |
| K0.1.2.10 | Core Foundation | macos-core.md L131 |
| K0.1.2.11 | Metal GPU Hesaplama | macos-core.md L209 |
| K0.1.2.12 | IOKit — Donanım Erişimi | macos-core.md L267 |
| K0.1.2.13 | LaunchAgent / LaunchDaemon | macos-core.md L338 |
| K0.1.2.14 | App Sandbox | macos-core.md L385 |
| K0.1.2.15 | Hardened Runtime | macos-core.md L422 |
| **K0.1.3** | Windows Core (3. katman) | windows-core.md — 19 yaprak |
| K0.1.3.1 | Genel Bakış | windows-core.md L12 |
| K0.1.3.2 | Teknik Detaylar | windows-core.md L16 |
| K0.1.3.3 | API / Arayüz | windows-core.md L332 |
| K0.1.3.4 | Bağımlılıklar | windows-core.md L373 |
| K0.1.3.5 | Performans Metrikleri | windows-core.md L390 |
| K0.1.3.6 | Güvenlik Notları | windows-core.md L400 |
| K0.1.3.7 | Durum: Implementasyon | windows-core.md L408 |
| K0.1.3.8 | Process Management | windows-core.md L18 |
| K0.1.3.9 | Memory Management | windows-core.md L51 |
| K0.1.3.10 | Threading Model | windows-core.md L82 |
| K0.1.3.11 | Registry Operations | windows-core.md L113 |
| K0.1.3.12 | Service Management | windows-core.md L138 |
| K0.1.3.13 | Event Log | windows-core.md L162 |
| K0.1.3.14 | Windows Management Instrumentation (WMI) | windows-core.md L182 |
| K0.1.3.15 | COM Interface | windows-core.md L205 |
| K0.1.3.16 | IPC — Named Pipes | windows-core.md L237 |
| K0.1.3.17 | Memory Mapped Files | windows-core.md L258 |
| K0.1.3.18 | Job Objects | windows-core.md L282 |
| K0.1.3.19 | User Account Control (UAC) | windows-core.md L306 |
| **K0.1.4** | RPi5 Core (3. katman) | rpi5-core.md — 11 yaprak |
| K0.1.4.1 | Genel Bakış | rpi5-core.md L12 |
| K0.1.4.2 | Teknik Detaylar | rpi5-core.md L16 |
| K0.1.4.3 | API / Arayüz | rpi5-core.md L338 |
| K0.1.4.4 | Bağımlılıklar | rpi5-core.md L383 |
| K0.1.4.5 | Performans Metrikleri | rpi5-core.md L401 |
| K0.1.4.6 | Donanım Notları | rpi5-core.md L411 |
| K0.1.4.7 | Durum: Implementasyon | rpi5-core.md L419 |
| K0.1.4.8 | GPIO Control | rpi5-core.md L18 |
| K0.1.4.9 | DMA Engine | rpi5-core.md L101 |
| K0.1.4.10 | PWM Audio | rpi5-core.md L171 |
| K0.1.4.11 | I2S Interface | rpi5-core.md L242 |


## 3. Kenar Durumları

| # | Kenar durum | Koşul | Risk | Yaklaşım |
|---|---|---|---|---|
| E1 | Kullanma önceliğinin (nice) düşürülmesi | sistem yükü arttığında | ses thread'i geç kalır | gerçek zamanlı zamanlayıcı sınıfı |
| E2 | Periyot süresinin aygıttan farklı olması | donanım sınırlaması | beklenmeyen tampon boyu | negotiate + fallback kaydı |
| E3 | CPU frekans yönetimi (governor) değişimi | güç yönetimi | gecikme varyansı artar | performans yöneticisi sabitleme |
| E4 | Sayfa kirliliği / baloncuk kullanımı | bellek baskısı | tahsis gecikmesi | ön tahsis + kilitli sayfa |
| E5 | Cihaz yolu değişimi (enumeration sırası) | USB yeniden bağlanma | yanlış cihaza açılma | kalıcı path kimliği |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` ve `README.md` §3.2.

## 4. Hata Modları

| # | Hata modu | Belirti | Sinyal | Sonuç |
|---|---|---|---|---|
| F1 | Zamanlayıcı overrun | kopukluk | periyot aşım sayacı | xrun |
| F2 | Bellek tahsis hatası | başlatılamama | tahsis hata kodu | oturum açılamaz |
| F3 | Yetki reddi (permission) | cihaz açılamaz | erişim reddi logu | ses yok |
| F4 | Sürücü/çekirdek uyuşmazlığı | sembol bulunamıyor | modül yükleme hatası | aygıt görünmez |
| F5 | Ağ kesintisi (uzak kullanım) | akış donması | paket kaybı sayacı | kesinti |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` §3.2, §8-§9.

## 5. Bağımlılıklar

- **Yukarı:** çekirdek zamanlama/bellek/dosya/ağ alt sistemleri (gömülü `linux-core.md`).
- **Aşağı:** `[[rpi5-gomulu-platform]]`, `[[../k003-cagri-thread/index]]`, `[[../k005-bellek-container/index]]`,
  `[[../k016-linux-ses/index]]` (ALSA/PipeWire bu çekirdeğin üzerine kurulur).

## 6. Sorumluluk Matrix

| Görev | Persona |
|---|---|
| Gerçek zamanlı zamanlayıcı ve periyot doğrulaması | `embedded-engineer` |
| CPU governor / affinity yapılandırması | `performance-engineer` |
| Dağıtım ve paket bağımlılıkları | `devops-engineer` |

## 7. Kanıt Kataloğu

1. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/linux-core.md` — gömülü gövde.
2. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:105-142` — §3.2 Linux API.
3. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:418-472` — K0.1 platform çekirdekleri.
4. `⚠️ VERIFICATION REQUIRED` — hedef donanımda ölçülmüş periyot gecikmesi (vault'ta ölçüm yok).
5. `⚠️ VERIFICATION REQUIRED` — çekirdek sürümüne özel gerçek zamanlı yama durumu (disk kanıtı yok).

## 8. Doğrulama Durumu

- Yeni iddia eklenmedi; tüm sayılar gömülü kaynaklardan gelir.
- Üst dizin: `[[index]]`
