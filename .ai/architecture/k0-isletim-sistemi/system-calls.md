---
title: "System Calls - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.0
---

# System Calls

## Genel Bakış

System Calls modülü, COREMUSIC'ın düşük seviyeli işletim sistemi arayüzlerini tanımlar. POSIX syscall interface, Windows NT API, Linux io_uring ve epoll/kqueue ile yüksek performanslı I/O işleme sağlar. Her platform için optimize edilmiş system call implementasyonları içerir.

## Teknik Detaylar

### 1. POSIX Syscall Interface

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

### 2. Windows NT API

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

### 3. Linux io_uring

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

### 4. epoll (Linux)

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

### 5. kqueue (macOS/BSD)

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

## API / Arayüz

### COREMUSIC System Calls API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- Linux Kernel 5.1+ (io_uring için 5.6+)
- liburing 2.0+ (io_uring için)
- Windows SDK (NT API için)
- macOS SDK (kqueue için)

### Alt Katmanlar
- CPU instruction set
- Donanım I/O

### Üst Katmanlar
- K0 Cross-Platform API
- K0 IPC Mekanizmaları
- K1 Ses Motoru

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| POSIX syscall latency | < 100ns | Belirlenecek |
| NT API latency | < 200ns | Belirlenecek |
| io_uring submit | < 1μs | Belirlenecek |
| io_uring completion | < 1μs | Belirlenecek |
| epoll wait | < 10μs | Belirlenecek |
| kqueue wait | < 10μs | Belirlenecek |

## Durum: Implementasyon

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
