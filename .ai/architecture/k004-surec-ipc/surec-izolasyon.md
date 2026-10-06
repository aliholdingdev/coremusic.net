---
title: "Süreç İzolasyonu - k004-surec-ipc"
type: architecture-sublayer
category: isletim-sistemi
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - K0 (k003-k005)"
updated: 2026-10-06
---

# Süreç İzolasyonu - `surec-izolasyon.md`

> Klasör: `k004-surec-ipc` · Kaynak: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md` (629 satır)
> Tür: salt-okunur verbatim aktarım · Wiki-link: [[index.md]] · Güncelleme: 2026-10-06

## Genel Bakış

Process Isolation modülü, COREMUSIC'ın güvenlik ve izolasyon stratejilerini tanımlar. Process sandboxing, capability dropping, chroot, namespaces ve seccomp-bpf ile uygulama ve process'leri birbirinden izole eder. Güvenli çalışma ortamı sağlar ve yetki yönetimini kontrol eder.


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L11-L14

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L15-L217

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L531-L581

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


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L582-L596

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| Sandbox oluşturma | < 100ms | Belirlenecek |
| chroot giriş | < 10ms | Belirlenecek |
| Namespace oluşturma | < 5ms | Belirlenecek |
| seccomp filter load | < 1ms | Belirlenecek |
| Capability drop | < 1μs | Belirlenecek |


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L597-L606

## Güvenlik Notları

1. **Defense in Depth**: Tüm izolasyon mekanizmalarını birlikte kullanın
2. **Minimum Privilege**: Sadece gerekli yetkileri verin
3. **seccomp**: Sistem call filtresi ile saldırı yüzeyini küçültün
4. **Namespaces**: Process ve kaynak izolasyonu sağlayın
5. **Regular Auditing**: İzolasyonconfigurations düzenli olarak denetleyin


> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L607-L614

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

> Aktarım: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/process-isolation.md - L615-L629

---
**Wiki-link:** [[index.md]] · Kardeş dosya: [[ipc-mekanizmalari-karsilastirma.md]]
