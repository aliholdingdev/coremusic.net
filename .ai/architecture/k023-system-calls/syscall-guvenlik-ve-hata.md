---
title: "Syscall Güvenlik ve Hata Yönetimi - k023-system-calls"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Syscall Güvenlik ve Hata Yönetimi

> Klasör: `k023-system-calls` · Dilim: D01 (k018–k035) · Dosya: `syscall-guvenlik-ve-hata.md`
> Sorumlu persona: `embedded-engineer` (Embedded Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Sistem çağrılarında yetki denetimi, hata sınıflandırması ve kurtarma.

Bu belge; D01 diliminin (Sistem Çağrıları) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Sistem çağrılarında yetki denetimi, hata sınıflandırması ve kurtarma.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Sistem Çağrıları)
- **Çapraz referanslar:** [[../k027-process-isolation/sandbox-ve-guvenlik.md]] · [[../k025-threading-model/threading-model.md]] · [[../k026-memory-management/memory-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### system-calls.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` (692 satır)

#### system-calls.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § (giriş) — L1–L10

---
title: "System Calls - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### System Calls


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Genel Bakış` — L11–L13


System Calls modülü, COREMUSIC'ın düşük seviyeli işletim sistemi arayüzlerini tanımlar. POSIX syscall interface, Windows NT API, Linux io_uring ve epoll/kqueue ile yüksek performanslı I/O işleme sağlar. Her platform için optimize edilmiş system call implementasyonları içerir.

#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Teknik Detaylar` — L15–L590


##### 1. POSIX Syscall Interface

POSIX, Unix tabanlı sistemler için standart arayüz:

```c
#include <sys/syscall.h>
#include <unistd.h>

// Temel POSIX syscall'lar
ssize_t sys_read(int fd, void *buf, size_t count) {
    return syscall(SYS_read, fd, buf, count);
}

ssize_t sys_write(int fd, const void *buf, size_t count) {
    return syscall(SYS_write, fd, buf, count);
}

int sys_open(const char *pathname, int flags, mode_t mode) {
    return syscall(SYS_open, pathname, flags, mode);
}

int sys_close(int fd) {
    return syscall(SYS_close, fd);
}

// Memory management syscall'lar
void *sys_mmap(void *addr, size_t length, int prot, int flags, int fd, off_t offset) {
    return (void *)syscall(SYS_mmap, addr, length, prot, flags, fd, offset);
}

int sys_munmap(void *addr, size_t length) {
    return syscall(SYS_munmap, addr, length);
}

int sys_mprotect(void *addr, size_t length, int prot) {
    return syscall(SYS_mprotect, addr, length, prot);
}

int sys_mlock(const void *addr, size_t length) {
    return syscall(SYS_mlock, addr, length);
}

int sys_munlock(const void *addr, size_t length) {
    return syscall(SYS_munlock, addr, length);
}

// Process management syscall'lar
pid_t sys_fork(void) {
    return syscall(SYS_fork);
}

int sys_execve(const char *filename, char *const argv[], char *const envp[]) {
    return syscall(SYS_execve, filename, argv, envp);
}

pid_t sys_wait4(pid_t pid, int *wstatus, int options, struct rusage *rusage) {
    return syscall(SYS_wait4, pid, wstatus, options, rusage);
}

int sys_kill(pid_t pid, int sig) {
    return syscall(SYS_kill, pid, sig);
}

// Signal handling
typedef void (*sighandler_t)(int);

sighandler_t sys_signal(int signum, sighandler_t handler) {
    // signal syscall yerine sigaction kullan
    struct sigaction sa;
    sa.sa_handler = handler;
    sigemptyset(&sa.sa_mask);
    sa.sa_flags = 0;

    if (sigaction(signum, &sa, NULL) == -1) {
        return SIG_ERR;
    }

    return handler;
}

// Thread management
pid_t sys_clone(unsigned long flags, void *child_stack, int *parent_tid, int *child_tid, unsigned long tls) {
    return syscall(SYS_clone, flags, child_stack, parent_tid, child_tid, tls);
}

// Semaphore
int sys_semget(key_t key, int nsems, int semflg) {
    return syscall(SYS_semget, key, nsems, semflg);
}

int sys_semop(int semid, struct sembuf *sops, unsigned long nsops) {
    return syscall(SYS_semop, semid, sops, nsops);
}

// Shared memory
int sys_shmget(key_t key, size_t size, int shmflg) {
    return syscall(SYS_shmget, key, size, shmflg);
}

void *sys_shmat(int shmid, const void *shmaddr, int shmflg) {
    return (void *)syscall(SYS_shmat, shmid, shmaddr, shmflg);
}
```

##### 2. Windows NT API

Windows NT API, Windows platformu için düşük seviyeli arayüz:

```c
#include <windows.h>
#include <winternl.h>

// NT API fonksiyon tanımı
typedef NTSTATUS (NTAPI *NtCreateFile_t)(
    PHANDLE FileHandle,
    ACCESS_MASK DesiredAccess,
    POBJECT_ATTRIBUTES ObjectAttributes,
    PIO_STATUS_BLOCK IoStatusBlock,
    PLARGE_INTEGER AllocationSize,
    ULONG FileAttributes,
    ULONG ShareAccess,
    ULONG CreateDisposition,
    ULONG CreateOptions,
    PVOID EaBuffer,
    ULONG EaLength
);

typedef NTSTATUS (NTAPI *NtReadFile_t)(
    HANDLE FileHandle,
    HANDLE Event,
    PIO_APC_ROUTINE ApcRoutine,
    PVOID ApcContext,
    PIO_STATUS_BLOCK IoStatusBlock,
    PVOID Buffer,
    ULONG Length,
    PLARGE_INTEGER ByteOffset,
    PULONG Key
);

typedef NTSTATUS (NTAPI *NtWriteFile_t)(
    HANDLE FileHandle,
    HANDLE Event,
    PIO_APC_ROUTINE ApcRoutine,
    PVOID ApcContext,
    PIO_STATUS_BLOCK IoStatusBlock,
    PVOID Buffer,
    ULONG Length,
    PLARGE_INTEGER ByteOffset,
    PULONG Key
);

// NT API fonksiyonlarını yükle
HMODULE ntdll = LoadLibrary("ntdll.dll");

NtCreateFile_t NtCreateFile = (NtCreateFile_t)GetProcAddress(ntdll, "NtCreateFile");
NtReadFile_t NtReadFile = (NtReadFile_t)GetProcAddress(ntdll, "NtReadFile");
NtWriteFile_t NtWriteFile = (NtWriteFile_t)GetProcAddress(ntdll, "NtWriteFile");

// NT dosya açma
NTSTATUS nt_open_file(const char *filename, HANDLE *handle) {
    UNICODE_STRING uniName;
    RtlInitUnicodeString(&uniName, L"\\??\\C:\\path\\to\\file");

    OBJECT_ATTRIBUTES objAttr;
    InitializeObjectAttributes(&objAttr, &uniName,
        OBJ_CASE_INSENSITIVE | OBJ_KERNEL_HANDLE,
        NULL, NULL);

    IO_STATUS_BLOCK ioStatus;
    LARGE_INTEGER allocationSize;

    NTSTATUS status = NtCreateFile(
        handle,
        GENERIC_READ | SYNCHRONIZE,
        &objAttr,
        &ioStatus,
        &allocationSize,
        FILE_ATTRIBUTE_NORMAL,
        FILE_SHARE_READ | FILE_SHARE_WRITE,
        FILE_OPEN_IF,
        FILE_SYNCHRONOUS_IO_NONALERT,
        NULL, 0);

    return status;
}

// NT dosya okuma
NTSTATUS nt_read_file(HANDLE handle, void *buffer, ULONG length) {
    IO_STATUS_BLOCK ioStatus;
    LARGE_INTEGER byteOffset = {0};

    NTSTATUS status = NtReadFile(
        handle,
        NULL, NULL, NULL,
        &ioStatus,
        buffer, length,
        &byteOffset, NULL);

    return status;
}

// NT dosya yazma
NTSTATUS nt_write_file(HANDLE handle, void *buffer, ULONG length) {
    IO_STATUS_BLOCK ioStatus;
    LARGE_INTEGER byteOffset = {0};

    NTSTATUS status = NtWriteFile(
        handle,
        NULL, NULL, NULL,
        &ioStatus,
        buffer, length,
        &byteOffset, NULL);

    return status;
}

// NT process oluşturma
NTSTATUS nt_create_process(const char *filename, HANDLE *process, HANDLE *thread) {
    UNICODE_STRING uniName;
    RtlInitUnicodeString(&uniName, L"\\??\\C:\\Windows\\System32\\notepad.exe");

    OBJECT_ATTRIBUTES objAttr;
    InitializeObjectAttributes(&objAttr, &uniName,
        OBJ_CASE_INSENSITIVE | OBJ_KERNEL_HANDLE,
        NULL, NULL);

    CLIENT_ID clientId;
    NTSTATUS status = RtlCreateProcessParameters(
        &processParameters, &uniName,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

    if (!NT_SUCCESS(status)) {
        return status;
    }

    status = NtCreateProcess(
        process,
        PROCESS_CREATE_THREAD | PROCESS_VM_OPERATION | PROCESS_VM_READ | PROCESS_VM_WRITE,
        &objAttr,
        NtCurrentProcess(),
        &clientId,
        processParameters,
        NULL, NULL, 0);

    return status;
}
```

##### 3. Linux io_uring

io_uring, yüksek performanslı asenkron I/O için:

```c
#include <liburing.h>
#include <sys/eventfd.h>

// io_uring yapısı
struct io_uring ring;
int event_fd;

// io_uring başlatma
int setup_io_uring() {
    int ret = io_uring_queue_init(256, &ring);
    if (ret < 0) {
        perror("io_uring_queue_init");
        return -1;
    }

    // Event fd oluştur
    event_fd = eventfd(0, EFD_NONBLOCK | EFD_SEMAPHORE);

    return 0;
}

// Async read operation
int async_read(int fd, void *buffer, size_t size, off_t offset) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_read(sqe, fd, buffer, size, offset);
    io_uring_sqe_set_data(sqe, buffer);

    io_uring_submit(&ring);
    return 0;
}

// Async write operation
int async_write(int fd, const void *buffer, size_t size, off_t offset) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_write(sqe, fd, buffer, size, offset);
    io_uring_sqe_set_data(sqe, (void *)buffer);

    io_uring_submit(&ring);
    return 0;
}

// Async accept
int async_accept(int listen_fd, struct sockaddr *addr, socklen_t *addrlen) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_accept(sqe, listen_fd, addr, addrlen, 0);

    int *client_fd = malloc(sizeof(int));
    io_uring_sqe_set_data(sqe, client_fd);

    io_uring_submit(&ring);
    return 0;
}

// Async connect
int async_connect(int fd, const struct sockaddr *addr, socklen_t addrlen) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_connect(sqe, fd, addr, addrlen);

    io_uring_sqe_set_data(sqe, NULL);
    io_uring_submit(&ring);
    return 0;
}

// Async send
int async_send(int fd, const void *buf, size_t len, int flags) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_send(sqe, fd, buf, len, flags);
    io_uring_sqe_set_data(sqe, (void *)buf);

    io_uring_submit(&ring);
    return 0;
}

// Async recv
int async_recv(int fd, void *buf, size_t len, int flags) {
    struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
    if (!sqe) return -1;

    io_uring_prep_recv(sqe, fd, buf, len, flags);
    io_uring_sqe_set_data(sqe, buf);

    io_uring_submit(&ring);
    return 0;
}

// Completion handling
int handle_completions(void (*callback)(void *data, int res)) {
    struct io_uring_cqe *cqe;
    int completed = 0;

    io_uring_for_each_cqe(&ring, &cqe) {
        void *data = io_uring_cqe_get_data(cqe);
        int result = cqe->res;

        callback(data, result);
        completed++;
    }

    io_uring_cq_advance(&ring, completed);
    return completed;
}

// Batch operations
int batch_read(int *fds, void **buffers, size_t *sizes, off_t *offsets, int count) {
    for (int i = 0; i < count; i++) {
        struct io_uring_sqe *sqe = io_uring_get_sqe(&ring);
        if (!sqe) return -1;

        io_uring_prep_read(sqe, fds[i], buffers[i], sizes[i], offsets[i]);
        io_uring_sqe_set_data(sqe, buffers[i]);
    }

    io_uring_submit(&ring);
    return count;
}

// io_uring temizleme
void cleanup_io_uring() {
    io_uring_queue_exit(&ring);
    close(event_fd);
}
```

##### 4. epoll (Linux)

epoll, yüksek performanslı I/O çoklama:

```c
#include <sys/epoll.h>
#include <unistd.h>

#define MAX_EVENTS 100

// epoll yapısı
typedef struct {
    int epoll_fd;
    struct epoll_event events[MAX_EVENTS];
    int event_count;
} EpollContext;

// epoll başlatma
EpollContext* epoll_create_context() {
    EpollContext *ctx = malloc(sizeof(EpollContext));
    ctx->epoll_fd = epoll_create1(EPOLL_CLOEXEC);
    ctx->event_count = 0;
    return ctx;
}

// fd ekleme
int epoll_add_fd(EpollContext *ctx, int fd, uint32_t events, void *data) {
    struct epoll_event ev;
    ev.events = events;
    ev.data.ptr = data;

    return epoll_ctl(ctx->epoll_fd, EPOLL_CTL_ADD, fd, &ev);
}

// fd güncelleme
int epoll_modify_fd(EpollContext *ctx, int fd, uint32_t events, void *data) {
    struct epoll_event ev;
    ev.events = events;
    ev.data.ptr = data;

    return epoll_ctl(ctx->epoll_fd, EPOLL_CTL_MOD, fd, &ev);
}

// fd kaldırma
int epoll_remove_fd(EpollContext *ctx, int fd) {
    return epoll_ctl(ctx->epoll_fd, EPOLL_CTL_DEL, fd, NULL);
}

// Eventleri bekleme
int epoll_wait_events(EpollContext *ctx, int timeout_ms) {
    return epoll_wait(ctx->epoll_fd, ctx->events, MAX_EVENTS, timeout_ms);
}

// Event handling
void epoll_handle_events(EpollContext *ctx,
    void (*handle_readable)(void *data),
    void (*handle_writable)(void *data),
    void (*handle_error)(void *data)) {

    for (int i = 0; i < ctx->event_count; i++) {
        struct epoll_event *ev = &ctx->events[i];
        void *data = ev->data.ptr;

        if (ev->events & EPOLLERR) {
            if (handle_error) handle_error(data);
        } else {
            if (ev->events & EPOLLIN) {
                if (handle_readable) handle_readable(data);
            }
            if (ev->events & EPOLLOUT) {
                if (handle_writable) handle_writable(data);
            }
        }
    }
}

// epoll temizleme
void epoll_destroy_context(EpollContext *ctx) {
    close(ctx->epoll_fd);
    free(ctx);
}
```

##### 5. kqueue (macOS/BSD)

kqueue, macOS ve BSD'ler için yüksek performanslı event notification:

```c
#include <sys/event.h>
#include <sys/time.h>

// kqueue yapısı
typedef struct {
    int kqueue_fd;
    struct kevent events[MAX_EVENTS];
    int event_count;
} KqueueContext;

// kqueue başlatma
KqueueContext* kqueue_create_context() {
    KqueueContext *ctx = malloc(sizeof(KqueueContext));
    ctx->kqueue_fd = kqueue();
    ctx->event_count = 0;
    return ctx;
}

// Event ekleme
int kqueue_add_event(KqueueContext *ctx, int fd, int filter, uint32_t flags, void *data) {
    struct kevent change;
    EV_SET(&change, fd, filter, flags, 0, 0, data);

    return kevent(ctx->kqueue_fd, &change, 1, NULL, 0, NULL);
}

// OKUYUN event'i ekleme (epoll equivalent: EPOLLIN)
int kqueue_add_read(KqueueContext *ctx, int fd, void *data) {
    return kqueue_add_event(ctx, fd, EVFILT_READ, EV_ADD | EV_ENABLE, data);
}

// YAZMA event'i ekleme (epoll equivalent: EPOLLOUT)
int kqueue_add_write(KqueueContext *ctx, int fd, void *data) {
    return kqueue_add_event(ctx, fd, EVFILT_WRITE, EV_ADD | EV_ENABLE, data);
}

// Timer event ekleme
int kqueue_add_timer(KqueueContext *ctx, int ident, int seconds, int milliseconds, void *data) {
    struct kevent change;
    struct timespec ts = {seconds, milliseconds * 1000000};

    EV_SET(&change, ident, EVFILT_TIMER, EV_ADD | EV_ENABLE, 0, 0, data);

    return kevent(ctx->kqueue_fd, &change, 1, NULL, 0, &ts);
}

// Signal event ekleme
int kqueue_add_signal(KqueueContext *ctx, int signum, void *data) {
    struct kevent change;
    EV_SET(&change, signum, EVFILT_SIGNAL, EV_ADD | EV_ENABLE, 0, 0, data);

    return kevent(ctx->kqueue_fd, &change, 1, NULL, 0, NULL);
}

// Eventleri bekleme
int kqueue_wait_events(KqueueContext *ctx, int timeout_ms) {
    struct timespec ts;
    if (timeout_ms >= 0) {
        ts.tv_sec = timeout_ms / 1000;
        ts.tv_nsec = (timeout_ms % 1000) * 1000000;
    }

    return kevent(ctx->kqueue_fd, NULL, 0, ctx->events, MAX_EVENTS,
        timeout_ms >= 0 ? &ts : NULL);
}

// Event handling
void kqueue_handle_events(KqueueContext *ctx,
    void (*handle_readable)(void *data),
    void (*handle_writable)(void *data),
    void (*handle_timer)(void *data),
    void (*handle_signal)(void *data)) {

    for (int i = 0; i < ctx->event_count; i++) {
        struct kevent *ev = &ctx->events[i];
        void *data = ev->udata;

        switch (ev->filter) {
            case EVFILT_READ:
                if (handle_readable) handle_readable(data);
                break;
            case EVFILT_WRITE:
                if (handle_writable) handle_writable(data);
                break;
            case EVFILT_TIMER:
                if (handle_timer) handle_timer(data);
                break;
            case EVFILT_SIGNAL:
                if (handle_signal) handle_signal(data);
                break;
        }
    }
}

// kqueue temizleme
void kqueue_destroy_context(KqueueContext *ctx) {
    close(ctx->kqueue_fd);
    free(ctx);
}
```

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `API / Arayüz` — L592–L648


##### COREMUSIC System Calls API Başlık Dosyası

```c
#ifndef COREMUSIC_SYSCALLS_H
#define COREMUSIC_SYSCALLS_H

#include <stdint.h>
#include <stddef.h>

// POSIX Syscalls
CM_Status CM_PosixRead(int fd, void *buf, size_t count, size_t *read);
CM_Status CM_PosixWrite(int fd, const void *buf, size_t count, size_t *written);
CM_Status CM_PosixOpen(const char *path, int flags, mode_t mode, int *fd);
CM_Status CM_PosixClose(int fd);
CM_Status CM_PosixMmap(void *addr, size_t length, int prot, int flags,
    int fd, off_t offset, void **ptr);
CM_Status CM_PosixMunmap(void *addr, size_t length);
CM_Status CM_PosixMlock(const void *addr, size_t length);
CM_Status CM_PosixMunlock(const void *addr, size_t length);

// Windows NT API
CM_Status CM_NtCreateFile(const char *path, ACCESS_MASK access, void **handle);
CM_Status CM_NtReadFile(void *handle, void *buf, size_t len, size_t *read);
CM_Status CM_NtWriteFile(void *handle, const void *buf, size_t len, size_t *written);
CM_Status CM_NtCloseFile(void *handle);

// Linux io_uring
CM_Status CM_IoUringSetup(int queue_size, void **ctx);
CM_Status CM_IoUringAsyncRead(void *ctx, int fd, void *buf, size_t len,
    off_t offset, void *user_data);
CM_Status CM_IoUringAsyncWrite(void *ctx, int fd, const void *buf, size_t len,
    off_t offset, void *user_data);
CM_Status CM_IoUringSubmit(void *ctx);
CM_Status CM_IoUringWaitCompletion(void *ctx, int *count);
CM_Status CM_IoUringDestroy(void *ctx);

// epoll (Linux)
CM_Status CM_EpollCreate(void **ctx);
CM_Status CM_EpollAddFd(void *ctx, int fd, uint32_t events, void *data);
CM_Status CM_EpollModifyFd(void *ctx, int fd, uint32_t events, void *data);
CM_Status CM_EpollRemoveFd(void *ctx, int fd);
CM_Status CM_EpollWait(void *ctx, int timeout_ms, int *count);
CM_Status CM_EpollDestroy(void *ctx);

// kqueue (macOS/BSD)
CM_Status CM_KqueueCreate(void **ctx);
CM_Status CM_KqueueAddRead(void *ctx, int fd, void *data);
CM_Status CM_KqueueAddWrite(void *ctx, int fd, void *data);
CM_Status CM_KqueueAddTimer(void *ctx, int ident, int ms, void *data);
CM_Status CM_KqueueAddSignal(void *ctx, int signum, void *data);
CM_Status CM_KqueueWait(void *ctx, int timeout_ms, int *count);
CM_Status CM_KqueueDestroy(void *ctx);

#endif // COREMUSIC_SYSCALLS_H
```

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Bağımlılıklar` — L650–L665


##### Gereksinimler
- Linux Kernel 5.1+ (io_uring için 5.6+)
- liburing 2.0+ (io_uring için)
- Windows SDK (NT API için)
- macOS SDK (kqueue için)

##### Alt Katmanlar
- CPU instruction set
- Donanım I/O

##### Üst Katmanlar
- K0 Cross-Platform API
- K0 IPC Mekanizmaları
- K1 Ses Motoru

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Performans Metrikleri` — L667–L676


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| POSIX syscall latency | < 100ns | Belirlenecek |
| NT API latency | < 200ns | Belirlenecek |
| io_uring submit | < 1μs | Belirlenecek |
| io_uring completion | < 1μs | Belirlenecek |
| epoll wait | < 10μs | Belirlenecek |
| kqueue wait | < 10μs | Belirlenecek |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Durum: Implementasyon` — L678–L692


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. io_uring implementasyonu (Linux için en yüksek performans)
2. epoll implementasyonu
3. kqueue implementasyonu
4. POSIX syscall wrapper'ları
5. Windows NT API entegrasyonu

**Sonraki Adımlar**:
- io_uring benchmark testlerinin yapılması
- epoll ile high-performance networking testleri
- kqueue macOS implementasyon testleri

### process-isolation.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` (629 satır)

#### process-isolation.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § (giriş) — L1–L10

---
title: "Process Isolation - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### Process Isolation


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Teknik Detaylar` — L15–L529


##### 1. Process Sandbox

Process sandbox, uygulamanın izole bir ortamda çalışmasını sağlar:

```c
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>

// Sandbox oluşturma
typedef struct {
    char *root_dir;
    char *tmp_dir;
    char *dev_dir;
    uid_t uid;
    gid_t gid;
    char **allowed_paths;
    int allowed_path_count;
} SandboxConfig;

int create_sandbox(SandboxConfig *config) {
    // Geçici dizin oluştur
    char tmp_path[] = "/tmp/coremusic-sandbox-XXXXXX";
    if (mkdtemp(tmp_path) == -1) {
        return -1;
    }
    config->root_dir = strdup(tmp_path);

    // Dizin yapısını oluştur
    char path[512];

    snprintf(path, sizeof(path), "%s/usr", config->root_dir);
    mkdir(path, 0755);

    snprintf(path, sizeof(path), "%s/usr/lib", config->root_dir);
    mkdir(path, 0755);

    snprintf(path, sizeof(path), "%s/usr/bin", config->root_dir);
    mkdir(path, 0755);

    snprintf(path, sizeof(path), "%s/tmp", config->root_dir);
    mkdir(path, 0777);

    snprintf(path, sizeof(path), "%s/dev", config->root_dir);
    mkdir(path, 0755);

    snprintf(path, sizeof(path), "%s/dev/null", config->root_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 3));

    snprintf(path, sizeof(path), "%s/dev/zero", config->root_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 5));

    snprintf(path, sizeof(path), "%s/dev/random", config->root_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 8));

    snprintf(path, sizeof(path), "%s/dev/urandom", config->root_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 9));

    // Ses cihazlarını kopyala
    snprintf(path, sizeof(path), "%s/dev/snd", config->root_dir);
    mkdir(path, 0755);

    return 0;
}

// chroot ile sandbox'a gir
int enter_sandbox(SandboxConfig *config) {
    // Mount namespace'de çalış
    if (unshare(CLONE_NEWNS) == -1) {
        return -1;
    }

    ///tmp'yi mount et
    char mount_opts[256];
    snprintf(mount_opts, sizeof(mount_opts),
        "size=256M,nr_inodes=4096,mode=0777");

    mount("tmpfs", config->root_dir, "tmpfs", 0, mount_opts);

    // chroot yap
    if (chroot(config->root_dir) == -1) {
        return -1;
    }

    chdir("/");

    return 0;
}

// Sandbox temizleme
void cleanup_sandbox(SandboxConfig *config) {
    if (config->root_dir) {
        // Dizin içeriğini temizle
        char cmd[512];
        snprintf(cmd, sizeof(cmd), "rm -rf %s", config->root_dir);
        system(cmd);
        free(config->root_dir);
    }
}
```

##### 2. Capability Dropping

Linux capabilities ile yetki yönetimi:

```c
#include <sys/capability.h>
#include <unistd.h>
#include <linux/capability.h>

// Capability yapısı
typedef struct {
    cap_value_t *caps;
    int count;
    int inherited;
} CapConfig;

// Gereksiz yetkileri bırakma
int drop_capabilities() {
    cap_t caps = cap_get_proc();
    if (!caps) return -1;

    // Tüm yetkileri temizle
    cap_clear(caps);

    // Sadece gerekli yetkileri ekle
    cap_value_t required_caps[] = {
        CAP_NET_BIND_SERVICE,  // Düşük port bağlama
        CAP_SYS_NICE,          // Realtime öncelik
        CAP_IPC_LOCK,          // Bellek kilitleme
        CAP_DAC_OVERRIDE,      // Dosya erişim bypass
    };

    int num_caps = sizeof(required_caps) / sizeof(required_caps[0]);

    for (int i = 0; i < num_caps; i++) {
        cap_set_flag(caps, CAP_EFFECTIVE, 1, &required_caps[i], CAP_SET);
        cap_set_flag(caps, CAP_PERMITTED, 1, &required_caps[i], CAP_SET);
    }

    if (cap_set_proc(caps) == -1) {
        cap_free(caps);
        return -1;
    }

    cap_free(caps);
    return 0;
}

// Process'in capability'lerini kontrol et
int check_capabilities() {
    cap_t caps = cap_get_proc();
    if (!caps) return -1;

    char *text = cap_to_text(caps, NULL);
    printf("Current capabilities: %s\n", text);

    cap_free(text);
    cap_free(caps);
    return 0;
}

// Yetki yükseltme (setuid bit ile)
int escalate_privileges(const char *program) {
    // setuid bit ile program çalıştır
    execlp(program, program, NULL);
    return -1;
}

// Yetki düşürme (real ve effective)
int drop_privileges_completely(uid_t uid, gid_t gid) {
    // Setgroups önce
    if (setgroups(0, NULL) == -1) {
        return -1;
    }

    // GID sonra UID
    if (setgid(gid) == -1) {
        return -1;
    }

    if (setuid(uid) == -1) {
        return -1;
    }

    // Hâlâ root mu kontrol et
    if (setuid(0) == 0) {
        fprintf(stderr, "ERROR: Still root after drop!\n");
        return -1;
    }

    return 0;
}

// securebits ayarlama
void set_securebits() {
    // SECUREBIT'leri ayarla
    prctl(PR_SET_SECUREBITS, SECBIT_NOROOT | SECBIT_NOROOT_LOCKED, 0, 0, 0);
}
```

##### 3. chroot

chroot ile dosya sistemi izolasyonu:

```c
#include <sys/stat.h>
#include <unistd.h>
#include <errno.h>

// chroot jail oluşturma
int create_chroot_jail(const char *jail_dir, const char **allowed_paths) {
    // Dizin yapısını oluştur
    char path[512];

    mkdir(jail_dir, 0755);

    // Temel dizinleri oluştur
    const char *dirs[] = {"/usr", "/usr/lib", "/usr/bin", "/tmp", "/dev", "/proc", NULL};
    for (int i = 0; dirs[i]; i++) {
        snprintf(path, sizeof(path), "%s%s", jail_dir, dirs[i]);
        mkdir(path, 0755);
    }

    // Gerekli dosyaları kopyala
    // (Gerçek implementasyonda dosya kopyalama gerekir)

    // Dev node'ları oluştur
    snprintf(path, sizeof(path), "%s/dev/null", jail_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 3));

    snprintf(path, sizeof(path), "%s/dev/zero", jail_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 5));

    snprintf(path, sizeof(path), "%s/dev/random", jail_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 8));

    snprintf(path, sizeof(path), "%s/dev/urandom", jail_dir);
    mknod(path, S_IFCHR | 0666, makedev(1, 9));

    // Proc mount
    snprintf(path, sizeof(path), "%s/proc", jail_dir);
    mount("proc", path, "proc", 0, NULL);

    return 0;
}

// chroot'a gir
int enter_chroot(const char *jail_dir) {
    // Kök dizine chdir
    if (chdir(jail_dir) == -1) {
        return -1;
    }

    // chroot yap
    if (chroot(jail_dir) == -1) {
        return -1;
    }

    // Kök dizine dön
    chdir("/");

    return 0;
}

// Symlink koruması
int protect_symlinks(const char *jail_dir) {
    // Protected_symlinks symlink_tolerance_control
    char path[512];
    snprintf(path, sizeof(path), "%s/proc/sys/fs/protected_symlinks", jail_dir);

    FILE *f = fopen(path, "w");
    if (f) {
        fprintf(f, "1");
        fclose(f);
    }

    return 0;
}
```

##### 4. Namespaces

Linux namespaces ile process izolasyonu:

```c
#define _GNU_SOURCE
#include <sched.h>
#include <sys/wait.h>

#define STACK_SIZE (1024 * 1024)

// Namespace türleri
typedef struct {
    int pid;
    int user;
    int net;
    int mnt;
    uts;
    ipc;
} NamespaceConfig;

// PID namespace ile izolasyon
int create_pid_namespace(void *arg) {
    printf("Child PID: %d\n", getpid());
    printf("Parent PID: %ppid\n", getppid());

    // Yeni PID namespace'inde çalışır
    execl("/bin/bash", "bash", NULL);
    return 0;
}

// User namespace ile root olmama
int create_user_namespace(void *arg) {
    // Yeni user namespace'de UID 0 (root) gibi görünür
    printf("UID: %d, EUID: %d\n", getuid(), geteuid());

    // Mount namespace ile dosya sistemi izolasyonu
    if (unshare(CLONE_NEWNS) == -1) {
        perror("unshare");
        return -1;
    }

    // /tmp mount
    mount("tmpfs", "/tmp", "tmpfs", 0, "size=1G");

    execl("/bin/bash", "bash", NULL);
    return 0;
}

// Network namespace ile ağ izolasyonu
int create_network_namespace(void *arg) {
    // Yeni network namespace
    if (unshare(CLONE_NEWNET) == -1) {
        perror("unshare");
        return -1;
    }

    // Loopback arayüzünü yapılandır
    system("ip link set lo up");
    system("ip addr add 127.0.0.1/8 dev lo");

    // Yeni ağ arayüzü oluştur
    system("ip link add veth0 type veth peer name veth1");
    system("ip link set veth1 up");
    system("ip addr add 10.0.0.1/24 dev veth0");

    execl("/bin/bash", "bash", NULL);
    return 0;
}

// Tüm namespace'leri birleştirme
int create_isolated_process() {
    char *stack = malloc(STACK_SIZE);
    if (!stack) return -1;

    // Tüm namespace'leri oluştur
    int flags = CLONE_NEWPID | CLONE_NEWNS | CLONE_NEWNET |
                CLONE_NEWUSER | CLONE_NEWUTS | CLONE_NEWIPC;

    pid_t pid = clone(create_pid_namespace,
        stack + STACK_SIZE,
        flags,
        NULL);

    if (pid == -1) {
        free(stack);
        return -1;
    }

    waitpid(pid, NULL, 0);
    free(stack);
    return pid;
}

// Namespace map dosyasını ayarlama
void setup_user_namespace_map(pid_t pid) {
    char path[256];

    // UID map
    snprintf(path, sizeof(path), "/proc/%d/uid_map", pid);
    FILE *f = fopen(path, "w");
    if (f) {
        fprintf(f, "0 1000 1\n");  // Container UID 0 -> Host UID 1000
        fclose(f);
    }

    // GID map
    snprintf(path, sizeof(path), "/proc/%d/gid_map", pid);
    f = fopen(path, "w");
    if (f) {
        fprintf(f, "0 1000 1\n");  // Container GID 0 -> Host GID 1000
        fclose(f);
    }
}
```

##### 5. seccomp-bpf

seccomp-bpf ile system call sınırlaması:

```c
#include <seccomp.h>
#include <linux/seccomp.h>
#include <sys/prctl.h>

// seccomp filter oluşturma
int setup_seccomp_filter() {
    scmp_filter_ctx ctx = seccomp_init(SCMP_ACT_KILL);

    if (!ctx) return -1;

    // Temel system call'lara izin ver
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(read), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(write), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(open), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(close), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(stat), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(fstat), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(lstat), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(poll), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(lseek), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(mmap), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(mprotect), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(munmap), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(brk), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(ioctl), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(access), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(pipe), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(select), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(sched_yield), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(mremap), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(msync), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(mincore), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(madvise), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(dup), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(dup2), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(nanosleep), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(getpid), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(socket), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(connect), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(accept), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(sendto), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(recvfrom), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(sendmsg), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(recvmsg), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(shutdown), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(bind), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(listen), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(getsockname), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(getpeername), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(socketpair), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(setsockopt), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(getsockopt), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(clone), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(fork), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(vfork), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(execve), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(exit), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(exit_group), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(futex), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(set_robust_list), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(get_robust_list), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(clock_gettime), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(clock_getres), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(epoll_create1), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(epoll_ctl), 0);
    seccomp_rule_add(ctx, SCMP_ACT_ALLOW, SCMP_SYS(epoll_wait), 0);

    // Filtreyi uygula
    if (seccomp_load(ctx) == -1) {
        seccomp_release(ctx);
        return -1;
    }

    seccomp_release(ctx);
    return 0;
}

// BPF filter ile gelişmiş sınırlama
int setup_bpf_filter() {
    struct sock_filter filter[] = {
        // System call numarasını yükle
        BPF_STMT(BPF_LD | BPF_W | BPF_ABS, offsetof(struct seccomp_data, nr)),

        // read syscall (0) izin ver
        BPF_JUMP(BPF_JMP | BPF_JEQ | BPF_K, 0, 0, 1),
        BPF_STMT(BPF_RET | BPF_K, SECCOMP_RET_ALLOW),

        // write syscall (1) izin ver
        BPF_JUMP(BPF_JMP | BPF_JEQ | BPF_K, 1, 0, 1),
        BPF_STMT(BPF_RET | BPF_K, SECCOMP_RET_ALLOW),

        // Diğer syscall'ları reddet
        BPF_STMT(BPF_RET | BPF_K, SECCOMP_RET_KILL_PROCESS),
    };

    struct sock_fprog prog = {
        .len = sizeof(filter) / sizeof(filter[0]),
        .filter = filter,
    };

    if (prctl(PR_SET_NO_NEW_PRIVS, 1, 0, 0, 0) == -1) {
        return -1;
    }

    if (prctl(PR_SET_SECCOMP, SECCOMP_MODE_FILTER, &prog) == -1) {
        return -1;
    }

    return 0;
}
```

#### Güvenlik Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Güvenlik Notları` — L607–L613


1. **Defense in Depth**: Tüm izolasyon mekanizmalarını birlikte kullanın
2. **Minimum Privilege**: Sadece gerekli yetkileri verin
3. **seccomp**: Sistem call filtresi ile saldırı yüzeyini küçültün
4. **Namespaces**: Process ve kaynak izolasyonu sağlayın
5. **Regular Auditing**: İzolasyonconfigurations düzenli olarak denetleyin

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - Linux Kernel 5.1+ (io_uring için 5.6+) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L653 |
| - liburing 2.0+ (io_uring için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L654 |
| - Windows SDK (NT API için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L655 |
| - macOS SDK (kqueue için) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L656 |
| - CPU instruction set | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L659 |
| - Donanım I/O | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L660 |
| - K0 Cross-Platform API | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L663 |
| - K0 IPC Mekanizmaları | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L664 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L665 |
| - Linux Kernel 2.6+ | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L585 |
| - libseccomp 2.4+ | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L586 |
| - libcap 2.2+ | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L587 |
| - K0 İşletim Sistemi katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L590 |
| - Linux kernel security features | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L591 |
| - K1 Ses Motoru (sandbox içinde) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L594 |
| - K3 Uygulama Katmanı (sandbox içinde) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L595 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| int epoll_wait_events(EpollContext *ctx, int timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L452 |
| return epoll_wait(ctx->epoll_fd, ctx->events, MAX_EVENTS, timeout_ms); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L453 |
| int kqueue_wait_events(KqueueContext *ctx, int timeout_ms) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L546 |
| if (timeout_ms >= 0) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L548 |
| ts.tv_sec = timeout_ms / 1000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L549 |
| ts.tv_nsec = (timeout_ms % 1000) * 1000000; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L550 |
| timeout_ms >= 0 ? &ts : NULL); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L554 |
| CM_Status CM_EpollWait(void *ctx, int timeout_ms, int *count); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L635 |
| CM_Status CM_KqueueWait(void *ctx, int timeout_ms, int *count); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L644 |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| perror("io_uring_queue_init"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L281 |
| void (*handle_error)(void *data)) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L460 |
| if (handle_error) handle_error(data); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L467 |
| fprintf(stderr, "ERROR: Still root after drop!\n"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L204 |
| // Symlink koruması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L282 |
| perror("unshare"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L336 |
| perror("unshare"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L351 |
| // Diğer syscall'ları reddet | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L510 |
| CM_Status CM_Seccomp_SetDefaultAction(int action); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L571 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| PVOID EaBuffer, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L140 |
| PVOID Buffer, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L150 |
| PVOID Buffer, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L162 |
| NTSTATUS nt_read_file(HANDLE handle, void *buffer, ULONG length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L204 |
| buffer, length, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L212 |
| NTSTATUS nt_write_file(HANDLE handle, void *buffer, ULONG length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L219 |
| buffer, length, | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L227 |
| int async_read(int fd, void *buffer, size_t size, off_t offset) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L292 |
| io_uring_prep_read(sqe, fd, buffer, size, offset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L296 |
| io_uring_sqe_set_data(sqe, buffer); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L297 |
| int async_write(int fd, const void *buffer, size_t size, off_t offset) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L304 |
| io_uring_prep_write(sqe, fd, buffer, size, offset); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L308 |
| io_uring_sqe_set_data(sqe, (void *)buffer); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L309 |
| int batch_read(int *fds, void **buffers, size_t *sizes, off_t *offsets, int count) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L383 |
| io_uring_prep_read(sqe, fds[i], buffers[i], sizes[i], offsets[i]); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L388 |
| io_uring_sqe_set_data(sqe, buffers[i]); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L389 |
| - CPU instruction set | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L659 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| Process Isolation modülü, COREMUSIC'ın güvenlik ve izolasyon stratejilerini tanımlar. Process sandboxing, capability dropping, chroot, name… | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |
| ### 1. Process Sandbox | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L17 |
| Process sandbox, uygulamanın izole bir ortamda çalışmasını sağlar: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L19 |
| // Sandbox oluşturma | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L26 |
| } SandboxConfig; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L35 |
| int create_sandbox(SandboxConfig *config) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L37 |
| char tmp_path[] = "/tmp/coremusic-sandbox-XXXXXX"; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L39 |
| // chroot ile sandbox'a gir | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L82 |
| int enter_sandbox(SandboxConfig *config) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L83 |
| // Mount namespace'de çalış | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L84 |
| // Sandbox temizleme | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L106 |
| void cleanup_sandbox(SandboxConfig *config) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L107 |
| Linux capabilities ile yetki yönetimi: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L120 |
| // Gereksiz yetkileri bırakma | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L134 |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - io_uring benchmark testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L690 |
| - epoll ile high-performance networking testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L691 |
| - kqueue macOS implementasyon testleri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L692 |
| - Capability dropping testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L627 |
| - seccomp filter'larının test edilmesi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L628 |
| - Namespace izolasyon testlerinin yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L629 |

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
| `syscall` | 36 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` L13 |
| `sandbox` | 21 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |
| `seccomp` | 81 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |
| `Güvenlik` | 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Genel Bakış | L11–L13 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Teknik Detaylar | L15–L590 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## API / Arayüz | L592–L648 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Bağımlılıklar | L650–L665 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Performans Metrikleri | L667–L676 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` | ## Durum: Implementasyon | L678–L692 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Genel Bakış | L11–L13 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Teknik Detaylar | L15–L529 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## API / Arayüz | L531–L580 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Bağımlılıklar | L582–L595 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Performans Metrikleri | L597–L605 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Güvenlik Notları | L607–L613 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Durum: Implementasyon | L615–L629 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` § `Durum: Implementasyon` — L678–L692


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. io_uring implementasyonu (Linux için en yüksek performans)
2. epoll implementasyonu
3. kqueue implementasyonu
4. POSIX syscall wrapper'ları
5. Windows NT API entegrasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Durum: Implementasyon` — L615–L629


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Capability dropping implementasyonu
2. seccomp-bpf filter oluşturma
3. Namespace tabanlı izolasyon
4. chroot jail oluşturma
5. Tam sandbox implementasyonu

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
