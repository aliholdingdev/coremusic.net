---
title: "Sandbox ve Güvenlik - k027-process-isolation"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# Sandbox ve Güvenlik

> Klasör: `k027-process-isolation` · Dilim: D01 (k018–k035) · Dosya: `sandbox-ve-guvenlik.md`
> Sorumlu persona: `security-engineer` (Security Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Sandbox katmanı, yetki indirgeme ve sürücü süreçlerinin karantınaya alınması.

Bu belge; D01 diliminin (Süreç İzolasyonu) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Sandbox katmanı, yetki indirgeme ve sürücü süreçlerinin karantınaya alınması.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (Süreç İzolasyonu)
- **Çapraz referanslar:** [[../k028-container-runtime/container-runtime.md]] · [[../k023-system-calls/syscall-guvenlik-ve-hata.md]] · [[../k026-memory-management/memory-management.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

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


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Genel Bakış` — L11–L13


Process Isolation modülü, COREMUSIC'ın güvenlik ve izolasyon stratejilerini tanımlar. Process sandboxing, capability dropping, chroot, namespaces ve seccomp-bpf ile uygulama ve process'leri birbirinden izole eder. Güvenli çalışma ortamı sağlar ve yetki yönetimini kontrol eder.

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

#### API / Arayüz

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `API / Arayüz` — L531–L580


##### COREMUSIC Process Isolation API Başlık Dosyası

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

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Bağımlılıklar` — L582–L595


##### Gereksinimler
- Linux Kernel 2.6+
- libseccomp 2.4+
- libcap 2.2+

##### Alt Katmanlar
- K0 İşletim Sistemi katmanı
- Linux kernel security features

##### Üst Katmanlar
- K1 Ses Motoru (sandbox içinde)
- K3 Uygulama Katmanı (sandbox içinde)

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Performans Metrikleri` — L597–L605


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Sandbox oluşturma | < 100ms | Belirlenecek |
| chroot giriş | < 10ms | Belirlenecek |
| Namespace oluşturma | < 5ms | Belirlenecek |
| seccomp filter load | < 1ms | Belirlenecek |
| Capability drop | < 1μs | Belirlenecek |

#### Güvenlik Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Güvenlik Notları` — L607–L613


1. **Defense in Depth**: Tüm izolasyon mekanizmalarını birlikte kullanın
2. **Minimum Privilege**: Sadece gerekli yetkileri verin
3. **seccomp**: Sistem call filtresi ile saldırı yüzeyini küçültün
4. **Namespaces**: Process ve kaynak izolasyonu sağlayın
5. **Regular Auditing**: İzolasyonconfigurations düzenli olarak denetleyin

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Durum: Implementasyon` — L615–L629


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

### container-runtime.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` (511 satır)

#### container-runtime.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` § (giriş) — L1–L10

---
title: "Container Runtime - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
date: 2026-09-20
version: 1.0.1
---

### Container Runtime


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` § `Teknik Detaylar` — L15–L412


##### 1. Docker Engine Entegrasyonu

Docker, konteyner oluşturma ve yönetimi için temel platform:

```c
#include <docker/client.h>

// Docker client oluşturma
docker_client_t *client = docker_client_create("unix:///var/run/docker.sock");

// Konteyner oluşturma
docker_container_config_t config = {
    .name = "coremusic-audio-processor",
    .image = "coremusic/audio:latest",
    .env = {
        "SAMPLE_RATE=48000",
        "BUFFER_SIZE=512",
        "CHANNELS=2"
    },
    .volumes = {
        "/dev/snd:/dev/snd",  // Ses cihazları
        "/tmp/coremusic:/tmp"  // Geçici dosyalar
    },
    .network_mode = "host",
    .privileged = true,  // Donanım erişimi için
    .resources = {
        .memory_limit = "2G",
        .cpus = "4.0",
        .cpu_shares = 1024
    }
};

docker_container_t *container = docker_container_create(client, &config);
docker_container_start(container);
```

##### 2. containerd Entegrasyonu

containerd, düşük seviyeli konteyner runtime'ı:

```c
#include <containerd/client.h>

// containerd client oluşturma
containerd_client_t *client = containerd_client_connect("unix:///run/containerd/containerd.sock");

// Image pull
containerd_image_t *image = containerd_image_pull(client,
    "docker.io/coremusic/audio:latest",
    NULL, NULL);

// Konteyner oluşturma
containerd_container_config_t config = {
    .id = "coremusic-worker-001",
    .image = image,
    .rootfs = {
        .type = "overlayfs",
        .path = "/var/lib/containerd/io.containerd.snapshotter.overlayfs"
    },
    .spec = {
        .process = {
            .args = {"/usr/bin/coremusic", "--worker"},
            .env = {
                "COREMUSIC_MODE=worker",
                "COREMUSIC_LOG_LEVEL=info"
            },
            .cwd = "/workspace"
        },
        .mounts = {
            {
                .type = "bind",
                .source = "/dev/snd",
                .destination = "/dev/snd",
                .options = "rbind,rw"
            }
        },
        .linux = {
            .resources = {
                .memory = {
                    .limit = 2147483648  // 2GB
                },
                .cpu = {
                    .shares = 1024,
                    .quota = 400000,  // 4 CPU
                    .period = 100000
                }
            }
        }
    }
};

containerd_container_t *container = containerd_container_create(client, &config);
containerd_task_t *task = containerd_task_create(client, container);
containerd_task_start(task);
```

##### 3. Kubernetes Entegrasyonu

Kubernetes, konteyner orkestrasyonu için:

```yaml
### coremusic-deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: coremusic-audio-processor
  namespace: coremusic
spec:
  replicas: 3
  selector:
    matchLabels:
      app: coremusic-audio
  template:
    metadata:
      labels:
        app: coremusic-audio
    spec:
      containers:
      - name: audio-processor
        image: coremusic/audio:latest
        ports:
        - containerPort: 8080
          name: http
        - containerPort: 50000
          name: audio
          protocol: UDP
        env:
        - name: SAMPLE_RATE
          value: "48000"
        - name: BUFFER_SIZE
          value: "512"
        resources:
          requests:
            memory: "1Gi"
            cpu: "2"
          limits:
            memory: "2Gi"
            cpu: "4"
        volumeMounts:
        - name: snd-dev
          mountPath: /dev/snd
        - name: tmp-audio
          mountPath: /tmp/coremusic
        livenessProbe:
          exec:
            command:
            - /usr/bin/coremusic
            - --health-check
          initialDelaySeconds: 30
          periodSeconds: 10
          timeoutSeconds: 5
        readinessProbe:
          exec:
            command:
            - /usr/bin/coremusic
            - --ready-check
          initialDelaySeconds: 5
          periodSeconds: 5
        securityContext:
          privileged: true
          capabilities:
            add:
            - NET_ADMIN
            - SYS_NICE
      volumes:
      - name: snd-dev
        hostPath:
          path: /dev/snd
          type: CharDevice
      - name: tmp-audio
        emptyDir:
          medium: Memory
          sizeLimit: 1Gi
      nodeSelector:
        kubernetes.io/arch: amd64
      tolerations:
      - key: "node-role.kubernetes.io/audio"
        operator: "Equal"
        value: "true"
        effect: "NoSchedule"
---
### coremusic-service.yaml
apiVersion: v1
kind: Service
metadata:
  name: coremusic-service
  namespace: coremusic
spec:
  selector:
    app: coremusic-audio
  ports:
  - name: http
    port: 8080
    targetPort: 8080
  - name: audio
    port: 50000
    targetPort: 50000
    protocol: UDP
  type: LoadBalancer
---
### coremusic-hpa.yaml
apiVersion: autoscaling/v2
kind: HorizontalPodAutoscaler
metadata:
  name: coremusic-hpa
  namespace: coremusic
spec:
  scaleTargetRef:
    apiVersion: apps/v1
    kind: Deployment
    name: coremusic-audio-processor
  minReplicas: 2
  maxReplicas: 10
  metrics:
  - type: Resource
    resource:
      name: cpu
      target:
        type: Utilization
        averageUtilization: 70
  - type: Resource
    resource:
      name: memory
      target:
        type: Utilization
        averageUtilization: 80
```

##### 4. Docker Compose

Docker Compose, çoklu konteyner uygulamaları için:

```yaml
### docker-compose.yaml
version: '3.8'

services:
  audio-processor:
    build:
      context: .
      dockerfile: Dockerfile.audio
    container_name: coremusic-processor
    privileged: true
    network_mode: host
    volumes:
      - /dev/snd:/dev/snd
      - /tmp/coremusic:/tmp
      - ./config:/etc/coremusic
    environment:
      - SAMPLE_RATE=48000
      - BUFFER_SIZE=512
      - LOG_LEVEL=info
      - REDIS_HOST=redis
      - REDIS_PORT=6379
    deploy:
      resources:
        limits:
          cpus: '4'
          memory: 2G
        reservations:
          cpus: '2'
          memory: 1G
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "/usr/bin/coremusic", "--health-check"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 40s

  redis:
    image: redis:7-alpine
    container_name: coremusic-redis
    ports:
      - "6379:6379"
    volumes:
      - redis-data:/data
    command: redis-server --appendonly yes --maxmemory 512mb
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 5

  prometheus:
    image: prom/prometheus:latest
    container_name: coremusic-prometheus
    ports:
      - "9090:9090"
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml
      - prometheus-data:/prometheus
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.path=/prometheus'

  grafana:
    image: grafana/grafana:latest
    container_name: coremusic-grafana
    ports:
      - "3000:3000"
    volumes:
      - grafana-data:/var/lib/grafana
      - ./grafana/dashboards:/var/lib/grafana/dashboards
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin

volumes:
  redis-data:
  prometheus-data:
  grafana-data:
```

##### 5. Health Check Implementasyonu

Sağlık kontrolü mekanizmaları:

```c
#include <sys/socket.h>
#include <netinet/in.h>

// Health check HTTP endpoint'i
void setup_health_check_server(int port) {
    int server_fd = socket(AF_INET, SOCK_STREAM, 0);

    struct sockaddr_in address;
    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(port);

    bind(server_fd, (struct sockaddr *)&address, sizeof(address));
    listen(server_fd, 10);

    while (running) {
        int client_fd = accept(server_fd, NULL, NULL);

        char buffer[1024];
        read(client_fd, buffer, sizeof(buffer));

        // Health check endpoint
        if (strncmp(buffer, "GET /health", 11) == 0) {
            int is_healthy = check_audio_processing_health();

            const char *response = is_healthy ?
                "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"healthy\",\"audio\":\"active\"}" :
                "HTTP/1.1 503 Service Unavailable\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"unhealthy\",\"audio\":\"inactive\"}";

            write(client_fd, response, strlen(response));
        }

        // Ready check endpoint
        if (strncmp(buffer, "GET /ready", 10) == 0) {
            int is_ready = check_audio_buffers_ready();

            const char *response = is_ready ?
                "HTTP/1.1 200 OK\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"ready\"}" :
                "HTTP/1.1 503 Service Unavailable\r\nContent-Type: application/json\r\n\r\n"
                "{\"status\":\"not_ready\"}";

            write(client_fd, response, strlen(response));
        }

        close(client_fd);
    }
}

// Sağlık kontrolü fonksiyonları
int check_audio_processing_health() {
    // Ses cihazı durumunu kontrol et
    if (!is_audio_device_active()) return 0;

    // Buffer durumunu kontrol et
    if (audio_buffer_underrun_count > 100) return 0;

    // CPU kullanımını kontrol et
    if (get_cpu_usage() > 90) return 0;

    // Bellek kullanımını kontrol et
    if (get_memory_usage() > 90) return 0;

    return 1;
}

int check_audio_buffers_ready() {
    // Tüm buffer'lar dolu mu?
    for (int i = 0; i < buffer_count; i++) {
        if (buffer_status[i] != BUFFER_READY) {
            return 0;
        }
    }
    return 1;
}
```

#### Güvenlik Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` § `Güvenlik Notları` — L489–L495


1. **Privileged Mode**: Sadece donanım erişimi gereken konteynerlerde
2. **Resource Limits**: Tüm konteynerler için memory ve CPU sınırları
3. **Network Policies**: Pod-to-Pod iletişimini sınırlama
4. **Security Context**: Capability dropping ve read-only root filesystem
5. **Image Scanning**: Güvenlik açığı taraması

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - Linux Kernel 2.6+ | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L585 |
| - libseccomp 2.4+ | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L586 |
| - libcap 2.2+ | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L587 |
| - K0 İşletim Sistemi katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L590 |
| - Linux kernel security features | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L591 |
| - K1 Ses Motoru (sandbox içinde) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L594 |
| - K3 Uygulama Katmanı (sandbox içinde) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L595 |
| - Docker 20.10 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L464 |
| - containerd 1.6 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L465 |
| - Kubernetes 1.24 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L466 |
| - Docker Compose v2 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L467 |
| - Linux Kernel (namespaces, cgroups) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L470 |
| - Network (bridge, overlay) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L471 |
| - Storage (overlayfs, volume) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L472 |
| - K1 Ses Motoru (konteyner içinde) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L475 |
| - K3 Uygulama Katmanı (konteyner içinde) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L476 |
| - CI/CD Pipeline | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L477 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| timeoutSeconds: 5 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L167 |
| timeout: 10s | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L283 |
| timeout: 5s | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L298 |
| if (audio_buffer_underrun_count > 100) return 0; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L392 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| fprintf(stderr, "ERROR: Still root after drop!\n"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L204 |
| // Symlink koruması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L282 |
| perror("unshare"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L336 |
| perror("unshare"); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L351 |
| // Diğer syscall'ları reddet | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L510 |
| CM_Status CM_Seccomp_SetDefaultAction(int action); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L571 |
| Container Runtime modülü, COREMUSIC'ın konteyner tabanlı dağıtım ve çalışma zamanı altyapısını yönetir. Docker Engine, containerd ve Kubern… | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L13 |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| "BUFFER_SIZE=512", | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L33 |
| .cpus = "4.0", | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L44 |
| .cpu_shares = 1024 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L45 |
| .cpu = { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L98 |
| .quota = 400000,  // 4 CPU | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L100 |
| - name: BUFFER_SIZE | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L146 |
| cpu: "2" | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L151 |
| cpu: "4" | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L154 |
| name: cpu | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L233 |
| - BUFFER_SIZE=512 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L267 |
| cpus: '4' | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L274 |
| cpus: '2' | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L277 |
| char buffer[1024]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L353 |
| read(client_fd, buffer, sizeof(buffer)); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L354 |
| if (strncmp(buffer, "GET /health", 11) == 0) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L357 |
| if (strncmp(buffer, "GET /ready", 10) == 0) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L370 |
| int is_ready = check_audio_buffers_ready(); | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L371 |
| // Buffer durumunu kontrol et | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L391 |
| // CPU kullanımını kontrol et | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L394 |
| if (get_cpu_usage() > 90) return 0; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L395 |

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
| - Capability dropping testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L627 |
| - seccomp filter'larının test edilmesi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L628 |
| - Namespace izolasyon testlerinin yazılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L629 |
| .image = "coremusic/audio:latest", | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L30 |
| "docker.io/coremusic/audio:latest", | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L65 |
| image: coremusic/audio:latest | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L136 |
| test: ["CMD", "/usr/bin/coremusic", "--health-check"] | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L281 |
| test: ["CMD", "redis-cli", "ping"] | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L296 |
| image: prom/prometheus:latest | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L302 |
| image: grafana/grafana:latest | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L314 |
| - Kubernetes test cluster kurulumu | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` L511 |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **10** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

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
| `sandbox` | 21 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |
| `seccomp` | 81 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |
| `capability` | 11 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |
| `Güvenlik` | 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` L13 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Genel Bakış | L11–L13 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Teknik Detaylar | L15–L529 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## API / Arayüz | L531–L580 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Bağımlılıklar | L582–L595 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Performans Metrikleri | L597–L605 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Güvenlik Notları | L607–L613 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` | ## Durum: Implementasyon | L615–L629 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | (başlık + giriş) | L1–L10 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## Genel Bakış | L11–L13 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## Teknik Detaylar | L15–L412 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## API / Arayüz | L414–L459 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## Bağımlılıklar | L461–L477 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## Performans Metrikleri | L479–L487 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## Güvenlik Notları | L489–L495 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` | ## Durum: Implementasyon | L497–L511 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` § `Durum: Implementasyon` — L615–L629


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Capability dropping implementasyonu
2. seccomp-bpf filter oluşturma
3. Namespace tabanlı izolasyon
4. chroot jail oluşturma
5. Tam sandbox implementasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/container-runtime.md` § `Durum: Implementasyon` — L497–L511


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. Docker image oluşturma ve optimize etme
2. containerd runtime entegrasyonu
3. Kubernetes deployment manifestleri
4. Health check ve monitoring
5. Auto-scaling yapılandırması

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
