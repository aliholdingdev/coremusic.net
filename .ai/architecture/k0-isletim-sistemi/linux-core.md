---
title: "Linux Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "Linux"
date: 2026-09-20
version: 1.0.0
---

# Linux Core

## Genel Bakış

Linux Core modülü, COREMUSIC'ın Linux platformu için temel işletim sistemi işlevlerini sağlar. Bu modül, Linux kernel'inin gelişmiş özelliklerini kullanarak gerçek zamanlı ses işleme ve çoklu ortam uygulamaları için optimize edilmiş bir altyapı sunar. cgroups, namespaces, systemd ve diğer Linux-specific teknolojileri entegre eder.

## Teknik Detaylar

### 1. Control Groups (cgroups)

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

### 2. Namespaces

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

### 3. systemd Integration

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

### 4. D-Bus Communication

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

### 5. epoll - Event Notification

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

### 6. inotify - Dosya Sistemi İzleme

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

### 7. seccomp - System Call Filtering

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

### 8. Linux Capabilities

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

### 9. tmpfs - Geçici Dosya Sistemi

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

### 10. /proc Filesystem

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

## API / Arayüz

### COREMUSIC Linux API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- Linux Kernel 5.4 ve üzeri
- systemd 245 ve üzeri
- D-Bus 1.12 ve üzeri
- libseccomp 2.4 ve üzeri

### Alt Katmanlar
- ALSA (Advanced Linux Sound Architecture)
- PulseAudio / PipeWire
- Linux kernel ses alt sistemi

### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| cgroup oluşturma | < 1ms | Belirlenecek |
| Namespace oluşturma | < 0.5ms | Belirlenecek |
| epoll wait | < 10μs | Belirlenecek |
| seccomp filter load | < 1ms | Belirlenecek |
| inotify event | < 1ms | Belirlenecek |

## Güvenlik Notları

1. **seccomp**: Tüm process'ler için system call filtresi uygulayın
2. **Capabilities**: Minimum yetki ile çalıştırın
3. **Namespaces**: Process izolasyonu için namespace kullanımı
4. **cgroups**: Kaynak sınırlaması ile DoS koruması
5. **tmpfs**: Güvenli dosya izinleri ile geçici depolama

## Durum: Implementasyon

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
