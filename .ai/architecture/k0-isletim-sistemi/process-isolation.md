---
title: "Process Isolation - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.0
---

# Process Isolation

## Genel Bakış

Process Isolation modülü, COREMUSIC'ın güvenlik ve izolasyon stratejilerini tanımlar. Process sandboxing, capability dropping, chroot, namespaces ve seccomp-bpf ile uygulama ve process'leri birbirinden izole eder. Güvenli çalışma ortamı sağlar ve yetki yönetimini kontrol eder.

## Teknik Detaylar

### 1. Process Sandbox

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

### 2. Capability Dropping

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

### 3. chroot

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

### 4. Namespaces

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

### 5. seccomp-bpf

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

## API / Arayüz

### COREMUSIC Process Isolation API Başlık Dosyası

```c
#ifndef COREMUSIC_ISOLATION_H
#define COREMUSIC_ISOLATION_H

#include <stdint.h>
#include <sys/types.h>

// Process Sandbox
CM_Status CM_Sandbox_Create(const char *root_dir, void **sandbox);
CM_Status CM_Sandbox_Enter(void *sandbox);
CM_Status CM_Sandbox_AddAllowedPath(void *sandbox, const char *path);
CM_Status CM_Sandbox_AddAllowedDevice(void *sandbox, const char *device);
CM_Status CM_Sandbox_Destroy(void *sandbox);

// Capability Dropping
CM_Status CM_Capabilities_DropAll(void);
CM_Status CM_Capabilities_KeepOnly(cap_value_t *caps, int count);
CM_Status CM_Capabilities_Check(void);
CM_Status CM_Capabilities_SetSecurebits(void);

// chroot
CM_Status CM_Chroot_Create(const char *jail_dir, void **jail);
CM_Status CM_Chroot_Enter(void *jail);
CM_Status CM_Chroot_AddMount(void *jail, const char *source, const char *target);
CM_Status CM_Chroot_Destroy(void *jail);

// Namespaces
CM_Status CM_Namespace_Create(int flags, void **ns);
CM_Status CM_Namespace_Enter(void *ns);
CM_Status CM_Namespace_SetUserMap(pid_t pid, uid_t inside_uid, uid_t outside_uid, int count);
CM_Status CM_Namespace_Destroy(void *ns);

// seccomp-bpf
CM_Status CM_Seccomp_Setup(void);
CM_Status CM_Seccomp_AllowSyscall(int syscall);
CM_Status CM_Seccomp_DenySyscall(int syscall);
CM_Status CM_Seccomp_SetDefaultAction(int action);

// Combined Isolation
CM_Status CM_Isolation_CreateFull(const char *name, void **isolated);
CM_Status CM_Isolation_Start(void *isolated, const char *program, char **args);
CM_Status CM_Isolation_Stop(void *isolated);
CM_Status CM_Isolation_Destroy(void *isolated);

#endif // COREMUSIC_ISOLATION_H
```

## Bağımlılıklar

### Gereksinimler
- Linux Kernel 2.6+
- libseccomp 2.4+
- libcap 2.2+

### Alt Katmanlar
- K0 İşletim Sistemi katmanı
- Linux kernel security features

### Üst Katmanlar
- K1 Ses Motoru (sandbox içinde)
- K3 Uygulama Katmanı (sandbox içinde)

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Sandbox oluşturma | < 100ms | Belirlenecek |
| chroot giriş | < 10ms | Belirlenecek |
| Namespace oluşturma | < 5ms | Belirlenecek |
| seccomp filter load | < 1ms | Belirlenecek |
| Capability drop | < 1μs | Belirlenecek |

## Güvenlik Notları

1. **Defense in Depth**: Tüm izolasyon mekanizmalarını birlikte kullanın
2. **Minimum Privilege**: Sadece gerekli yetkileri verin
3. **seccomp**: Sistem call filtresi ile saldırı yüzeyini küçültün
4. **Namespaces**: Process ve kaynak izolasyonu sağlayın
5. **Regular Auditing**: İzolasyonconfigurations düzenli olarak denetleyin

## Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Capability dropping implementasyonu
2. seccomp-bpf filter oluşturma
3. Namespace tabanlı izolasyon
4. chroot jail oluşturma
5. Tam sandbox implementasyonu

**Sonraki Adımlar**:
- Capability dropping testlerinin yapılması
- seccomp filter'larının test edilmesi
- Namespace izolasyon testlerinin yazılması
