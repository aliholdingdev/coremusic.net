---
title: "Sistem Çağrıları Rehberi - k003-cagri-thread"
type: architecture-sublayer
category: isletim-sistemi
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - K0 (k003-k005)"
updated: 2026-10-06
---

# Sistem Çağrıları Rehberi - `system-calls-rehberi.md`

> Klasör: `k003-cagri-thread` · Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md` (692 satır)
> Tür: salt-okunur verbatim aktarım · Wiki-link: [[index.md]] · Güncelleme: 2026-10-06

## Genel Bakış

System Calls modülü, COREMUSIC'ın düşük seviyeli işletim sistemi arayüzlerini tanımlar. POSIX syscall interface, Windows NT API, Linux io_uring ve epoll/kqueue ile yüksek performanslı I/O işleme sağlar. Her platform için optimize edilmiş system call implementasyonları içerir.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L11-L14

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L15-L264

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L592-L649

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L650-L666

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| POSIX syscall latency | < 100ns | Belirlenecek |
| NT API latency | < 200ns | Belirlenecek |
| io_uring submit | < 1μs | Belirlenecek |
| io_uring completion | < 1μs | Belirlenecek |
| epoll wait | < 10μs | Belirlenecek |
| kqueue wait | < 10μs | Belirlenecek |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L667-L677

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

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/system-calls.md - L678-L692

---
**Wiki-link:** [[index.md]] · Kardeş dosya: [[threading-ve-gercek-zaman.md]]
