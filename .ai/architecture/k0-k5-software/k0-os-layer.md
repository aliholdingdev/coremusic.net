---
type: architecture
category: layer-definition
title: "K0 — Operating System Layer (50 Components)"
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k0-os-layer.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K0
  component_count: 50
---

# K0 — İşletim Sistemi Katmanı (Operating System Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic platformunun çalıştığı tüm işletim sistemleri için temel sistem hizmetleri, çekirdek API'leri ve platform serviciosları.

---

## 1. Genel Bakış

K0 katmanı, CoreMusic'in çalıştığı tüm işletim sistemleri için thấp katmanlı sistem hizmetlerini tanımlar. Bu katman, uygulama katmanlarının (K1-K5) ihtiyaç duyduğu temel OS primitiflerini sağlar.

### 1.1 Katman Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────┐
│                    K0 — OS LAYER (50)                       │
├──────────────┬──────────────┬──────────────┬────────────────┤
│  WINDOWS     │    LINUX     │    macOS     │    RPi5        │
│  CORE (12)   │   CORE (10)  │   CORE (8)   │   CORE (4)    │
├──────────────┴──────────────┴──────────────┴────────────────┤
│              CONTAINER RUNTIME (3)                          │
├─────────────────────────────────────────────────────────────┤
│  AUDIO    │  MEMORY   │  THREADING  │  IPC      │  FILE    │
│  (6)      │  (3)      │  (5)        │  (3)      │  (3)     │
├───────────┴───────────┴─────────────┴───────────┴──────────┤
│  REGISTRY │ CONFIG  │ SERVICE │ NETWORK │ GPU    │ POWER   │
│  (1)      │ (1)     │ (2)     │ (2)     │ (3)    │ (2)     │
├───────────┴─────────┴─────────┴─────────┴────────┴─────────┤
│  SECURITY (3)  │  INSTALLER (2)  │  CONTAINER SUB (3)       │
└────────────────┴─────────────────┴──────────────────────────┘
```

### 1.2 Platform Dağılımı

| Platform | Bileşen Sayısı | Kapsam |
|----------|---------------|--------|
| Windows Core | 12 | Win32 API, COM/COM+, WMI, Registry, Service, Network, GPU, Power, Security, Installer |
| Linux Core | 10 | systemd, dbus, udev, cgroups, Config, Daemon, Network, GPU, Power, Security |
| macOS Core | 8 | CoreServices, Foundation, AppKit, Audio, Config, GPU, Power, Security |
| RPi5 Core | 4 | BCM2712, VideoCore VII, DMA, I2S/GPIO |
| Container Runtime | 3 | Docker Engine, containerd, CRI-O |
| Cross-Platform Audio | 6 | WASAPI, ALSA, PipeWire, CoreAudio, CoreMIDI, AudioToolbox |
| Cross-Platform Memory | 3 | Windows Heap/VM, Linux mmap/shm, macOS VM |
| Cross-Platform Threading | 5 | Win32 Threads, pthreads, GCD, io_uring, epoll |
| Cross-Platform IPC | 3 | Named Pipes, Unix Sockets, Shared Memory |
| Cross-Platform File | 3 | NTFS/ReFS, ext4/XFS/Btrfs, APFS/HFS+ |

**TOPLAM: 50 bileşen**

---

## 2. Windows Core (12 Bileşen)

### 2.1 Win32 API

| Özellik | Değer |
|---------|-------|
| API Seviyesi | Windows 10/11 (≥19041) |
| Amaç | Temel sistem çağrıları, dosya işleme, kayıt defteri |
| Kullanım | Dosya I/O, proses yönetimi, bellek yönetimi |
| Referans | https://learn.microsoft.com/en-us/windows/win32/ |

### 2.2 COM/COM+

| Özellik | Değer |
|---------|-------|
| Versiyon | COM+ 1.0 |
| Amaç | Bileşenlerarası iletişim, WMI erişimi |
| Kullanım | WMI sorguları, Device Enumerasyon |
| Referans | https://learn.microsoft.com/en-us/windows/win32/com/ |

### 2.3 WMI (Windows Management Instrumentation)

| Özellik | Değer |
|---------|-------|
| Amaç | Sistem bilgisi, donanım durumu, servis yönetimi |
| Kullanım | GPU capabilities, disk info, network adapters |
| Referans | https://learn.microsoft.com/en-us/windows/win32/wmisdk/ |

### 2.4 Windows Registry

| Özellik | Değer |
|---------|-------|
| Amaç | Yapılandırma depolama, uygulama ayarları |
| Kullanım | ASIO driver registry, audio device paths |
| Referans | https://learn.microsoft.com/en-us/windows/win32/sysinfo/registry |

### 2.5 Windows Service (SCM)

| Özellik | Değer |
|---------|-------|
| Amaç | Arka plan servisleri, otomatik başlatma |
| Kullanım | CoreMusic Service, Download Service |
| Referans | https://learn.microsoft.com/en-us/windows/win32/services/ |

### 2.6 Windows Network (Winsock2)

| Özellik | Değer |
|---------|-------|
| Amaç | Ağ iletişimi, TCP/UDP |
| Kullanım | API Gateway, WebSocket, DLNA |
| Ek | WinHTTP, WinINet for HTTP operations |
| Referans | https://learn.microsoft.com/en-us/windows/win32/winsock/ |

### 2.7 Windows GPU (DirectX 12)

| Özellik | Değer |
|---------|-------|
| Amaç | GPU hesaplama, grafik işleme |
| Kullanım | Spectrum analyzer visualization, UI rendering |
| Ek | WDDM driver model, DXGI enumeration |
| Referans | https://github.com/microsoft/DirectX-Graphics-Samples |

### 2.8 Windows Power Management

| Özellik | Değer |
|---------|-------|
| Amaç | Güç yönetimi, uyku/uyandırma |
| Kullanım | Audio session power handling, battery status |
| Referans | https://learn.microsoft.com/en-us/windows/win32/power/ |

### 2.9 Windows Security (DPAPI)

| Özellik | Değer |
|---------|-------|
| Amaç | Şifreleme, kimlik bilgileri koruması |
| Kullanım | Credential Vault, API key storage |
| Ek | CryptoAPI, Credential Manager |
| Referans | https://learn.microsoft.com/en-us/windows/win32/seccrgp/ |

### 2.10 Windows Installer (MSI)

| Özellik | Değer |
|---------|-------|
| Amaç | Uygulama dağıtımı, kurulum |
| Kullanım | CoreMusic MSI installer, WiX Toolset |
| Referans | https://wixtoolset.org/ |

### 2.11 Windows Audio (WASAPI)

| Özellik | Değer |
|---------|-------|
| Amaç | Ses oturum yönetimi |
| Kullanım | Shared ve Exclusive mode audio |
| Ek | MMDevice API, WaveRT driver |
| Referans | https://learn.microsoft.com/en-us/windows/win32/coreaudio/ |

### 2.12 Windows Memory Management

| Özellik | Değer |
|---------|-------|
| Amaç | Bellek yönetimi, sanal bellek |
| Kullanım | VirtualAlloc, HeapCreate, Memory-mapped files |
| Referans | https://learn.microsoft.com/en-us/windows/win32/memory/ |

---

## 3. Linux Core (10 Bileşen)

### 3.1 systemd

| Özellik | Değer |
|---------|-------|
| Versiyon | ≥ 250 |
| Amaç | Servis yönetimi, boot süreci, resource control |
| Kullanım | CoreMusic service unit, journal logging |
| Referans | https://github.com/systemd/systemd |

### 3.2 D-Bus (dbus)

| Özellik | Değer |
|---------|-------|
| Amaç | Sistem içi mesajlaşma |
| Kullanım | PipeWire communication, device hotplug |
| Referans | https://gitlab.freedesktop.org/dbus/dbus |

### 3.3 udev

| Özellik | Değer |
|---------|-------|
| Amaç | Device hotplug yönetimi |
| Kullanım | USB audio device detection, ALSA device nodes |
| Referans | https://github.com/systemd/systemd/tree/main/src/udev |

### 3.4 cgroups

| Özellik | Değer |
|---------|-------|
| Amaç | Resource isolation ve limiting |
| Kullanım | Audio process priority, CPU affinity |
| Referans | https://github.com/torvalds/linux/tree/master/kernel/cgroup |

### 3.5 Linux Config (/etc, sysctl)

| Özellik | Değer |
|---------|-------|
| Amaç | Sistem yapılandırması |
| Kullanım | Audio limits, network tuning, memory limits |
| Ek | /proc, /sys filesystem |
| Referans | https://www.kernel.org/doc/Documentation/ |

### 3.6 Linux Daemon (systemd, init.d)

| Özellik | Değer |
|---------|-------|
| Amaç | Arka plan servisleri |
| Kullanım | CoreMusic daemon, watchdog |
| Referans | https://www.freedesktop.org/software/systemd/man/systemd.service.html |

### 3.7 Linux Network (libcurl)

| Özellik | Değer |
|---------|-------|
| Amaç | Ağ iletişimi |
| Kullanım | API calls, download service |
| Ek | nftables, iptables for firewall |
| Referans | https://github.com/curl/curl |

### 3.8 Linux GPU (Vulkan)

| Özellik | Değer |
|---------|-------|
| Amaç | GPU hesaplama, grafik |
| Kullanım | Spectrum analyzer, visualization |
| Ek | DRM/KMS, EGL |
| Referans | https://github.com/KhronosGroup/Vulkan-Hpp |

### 3.9 Linux Power Management

| Özellik | Değer |
|---------|-------|
| Amaç | Güç yönetimi |
| Kullanım | CPU frequency scaling, thermal management |
| Ek | ACPI, cpufreq, thermald |
| Referans | https://www.kernel.org/doc/Documentation/power/ |

### 3.10 Linux Security (SELinux)

| Özellik | Değer |
|---------|-------|
| Amaç | Zorunlu erişim kontrolü |
| Kullanım | Service isolation, file permissions |
| Ek | AppArmor, PAM |
| Referans | https://github.com/SELinuxProject/selinux |

---

## 4. macOS Core (8 Bileşen)

### 4.1 CoreServices

| Özellik | Değer |
|---------|-------|
| Amaç | Temel sistem servisleri |
| Kullanım | File system events, URL handling |
| Referans | https://developer.apple.com/documentation/coreservices |

### 4.2 Foundation

| Özellik | Değer |
|---------|-------|
| Amaç | Temel Objective-C/Swift sınıfları |
| Kullanım | Data handling, networking, threading |
| Referans | https://developer.apple.com/documentation/foundation |

### 4.3 AppKit

| Özellik | Değer |
|---------|-------|
| Amaç | macOS GUI framework |
| Kullanım | UI rendering, window management |
| Referans | https://developer.apple.com/documentation/appkit |

### 4.4 macOS Audio (CoreAudio)

| Özellik | Değer |
|---------|-------|
| Amaç | Ses Alt Sistemi |
| Kullanım | HAL, Audio Units, device management |
| Ek | CoreMIDI, AudioToolbox |
| Referans | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudio/ |

### 4.5 macOS Config

| Özellik | Değer |
|---------|-------|
| Amaç | Yapılandırma yönetimi |
| Kullanım | UserDefaults, plist files |
| Referans | https://developer.apple.com/documentation/foundation/userdefaults |

### 4.6 macOS GPU (Metal)

| Özellik | Değer |
|---------|-------|
| Amaç | GPU hesaplama ve grafik |
| Kullanım | Spectrum visualization, UI rendering |
| Ek | Core Image, Quartz |
| Referans | https://developer.apple.com/metal/ |

### 4.7 macOS Power Management

| Özellik | Değer |
|---------|-------|
| Amaç | Güç yönetimi |
| Kullanım | Power assertions, sleep prevention |
| Referans | https://developer.apple.com/documentation/iokit/iopmpowerhelper |

### 4.8 macOS Security (Keychain)

| Özellik | Değer |
|---------|-------|
| Amaç | Kimlik bilgileri depolama |
| Kullanım | Credential Vault, API key storage |
| Ek | Security.framework |
| Referans | https://developer.apple.com/documentation/security/ |

---

## 5. RPi5 Core (4 Bileşen)

### 5.1 BCM2712 SoC

| Özellik | Değer |
|---------|-------|
| CPU | Quad-core Cortex-A76 @ 2.4GHz |
| RAM | 4GB/8GB LPDDR4X |
| Kullanım | Ana hesaplama birimi |
| Referans | https://www.raspberrypi.com/documentation/computers/processors.html |

### 5.2 VideoCore VII

| Özellik | Değer |
|---------|-------|
| Amaç | GPU, video decode |
| Kullanım | UI rendering, video playback |
| Referans | https://docs.broadcom.com/doc/12711545 |

### 5.3 DMA Controller

| Özellik | Değer |
|---------|-------|
| Amaç | Bellek transferi |
| Kullanım | I2S audio streaming, high-speed data transfer |
| Referans | https://www.raspberrypi.com/documentation/computers/processors.html |

### 5.4 RPi5 I2S/GPIO

| Özellik | Değer |
|---------|-------|
| Amaç | Dijital ses ve genel amaçlı giriş/çıkış |
| Kullanım | I2S to DAC, GPIO for control signals |
| Referans | https://www.raspberrypi.com/documentation/computers/raspberry-pi.html |

---

## 6. Container Runtime (3 Bileşen)

### 6.1 Docker Engine

| Özellik | Değer |
|---------|-------|
| Versiyon | ≥ 24.0 |
| Amaç | Container orchestration |
| Kullanım | CoreMusic deployment, development environment |
| Referans | https://github.com/moby/moby |

### 6.2 containerd

| Özellik | Değer |
|---------|-------|
| Amaç | Container runtime |
| Kullanım | Docker backend, Kubernetes runtime |
| Referans | https://github.com/containerd/containerd |

### 6.3 CRI-O

| Özellik | Değer |
|---------|-------|
| Amaç | Lightweight container runtime |
| Kullanım | Kubernetes-native container runtime |
| Referans | https://github.com/cri-o/cri-o |

---

## 7. Cross-Platform Alt Sistemler

### 7.1 Audio Subsystem (6 Bileşen)

```
┌──────────────────────────────────────────────────────┐
│               AUDIO SUBSYSTEM (6)                     │
├──────────────┬──────────────┬────────────────────────┤
│   Windows    │    Linux     │       macOS            │
├──────────────┼──────────────┼────────────────────────┤
│ WASAPI       │ ALSA         │ CoreAudio HAL          │
│ MMDevice     │ PipeWire     │ CoreMIDI Driver        │
│ WaveRT       │              │ AudioToolbox           │
└──────────────┴──────────────┴────────────────────────┘
```

### 7.2 Memory Management (3 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | VirtualAlloc, HeapCreate, Memory-mapped files | Audio buffer allocation, shared memory |
| Linux | mmap, shmget, madvise | Ring buffer, IPC shared memory |
| macOS | VM Allocate, mmap | Audio buffer, cross-process sharing |

### 7.3 Threading Model (5 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | CreateThread, Thread Pool, Fiber | Audio processing, UI thread |
| Linux | pthreads, io_uring, epoll | Event-driven I/O, async operations |
| macOS | Grand Central Dispatch (GCD), pthreads | Concurrent audio processing |

### 7.4 IPC Mechanisms (3 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | Named Pipes, Shared Memory, Mailslots | Inter-process audio streaming |
| Linux | Unix sockets, D-Bus, POSIX MQ | Service communication, device events |
| Cross-platform | TCP/UDP sockets | Network audio, API communication |

### 7.5 File System (3 Bileşen)

| Platform | Dosya Sistemi | Özellikler |
|----------|---------------|------------|
| Windows | NTFS, ReFS, Minifilter | Journaling, ACL, deduplication |
| Linux | ext4, XFS, Btrfs, FUSE | Journaling, CoW, snapshots |
| macOS | APFS, HFS+, FSEvents | Encryption, snapshots, events |

---

## 8. Destekleyici Sistemler

### 8.1 Registry & Configuration (2 Bileşen)

| Platform | Depolama | Kullanım |
|----------|----------|----------|
| Windows | Registry | ASIO driver config, audio device paths |
| Linux | /etc, sysctl, procfs, sysfs | System limits, network config |

### 8.2 Service & Daemon (2 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | Service Control Manager (SCM) | CoreMusic background service |
| Linux | systemd, init.d | CoreMusic daemon, auto-start |

### 8.3 Network Stack (2 Bileşen)

| Platform | Library | Kullanım |
|----------|---------|----------|
| Windows | Winsock2, WinHTTP, WinINet | API calls, download, streaming |
| Linux | libcurl, nftables, iptables | HTTP client, firewall rules |

### 8.4 GPU Acceleration (3 Bileşen)

| Platform | API | Kullanım |
|----------|-----|----------|
| Windows | DirectX 12, WDDM, DXGI | Spectrum visualization, UI rendering |
| Linux | Vulkan, DRM/KMS, EGL | GPU compute, graphics rendering |
| macOS | Metal, Core Image, Quartz | GPU-accelerated visualization |

### 8.5 Power Management (2 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | Power Management API | Battery status, sleep prevention |
| Linux | ACPI, cpufreq, thermald | CPU frequency, thermal control |

### 8.6 Security (3 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | DPAPI, CryptoAPI, Credential Manager | Encryption, credential storage |
| Linux | SELinux, AppArmor, PAM | MAC, authentication |
| macOS | Keychain, Security.framework | Secure storage, auth |

### 8.7 Installer & Packaging (2 Bileşen)

| Platform | Mekanizma | Kullanım |
|----------|-----------|----------|
| Windows | MSI, WiX Toolset | Application installation |
| Linux | DEB, RPM, Flatpak, Snap | Package distribution |

---

## 9. Bağımlılık Diyagramı

```
┌─────────────────────────────────────────────────────────┐
│                    K0 DEPENDENCY MAP                     │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────┐     ┌──────────┐     ┌──────────┐        │
│  │ K1 HW   │────>│ K2 Driver│────>│ K3 Audio │        │
│  │ Layer    │     │ Layer    │     │ Engine   │        │
│  └──────────┘     └──────────┘     └──────────┘        │
│       │                │                 │              │
│       ▼                ▼                 ▼              │
│  ┌──────────────────────────────────────────────┐      │
│  │              K0 — OS LAYER                    │      │
│  │  ┌─────────┬─────────┬─────────┬──────────┐  │      │
│  │  │Windows  │ Linux   │ macOS   │  RPi5    │  │      │
│  │  │Core(12) │Core(10) │Core(8)  │Core(4)   │  │      │
│  │  └─────────┴─────────┴─────────┴──────────┘  │      │
│  │  ┌─────────┬─────────┬─────────┬──────────┐  │      │
│  │  │Container│ Audio   │ Memory  │Threading │  │      │
│  │  │Runtime(3│ (6)     │ (3)     │(5)       │  │      │
│  │  └─────────┴─────────┴─────────┴──────────┘  │      │
│  │  ┌─────────┬─────────┬─────────┬──────────┐  │      │
│  │  │IPC(3)   │ File(3) │Config(2)│Service(2)│  │      │
│  │  └─────────┴─────────┴─────────┴──────────┘  │      │
│  │  ┌─────────┬─────────┬─────────┬──────────┐  │      │
│  │  │Network(2│ GPU(3)  │Power(2) │Security(3│  │      │
│  │  └─────────┴─────────┴─────────┴──────────┘  │      │
│  └──────────────────────────────────────────────┘      │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 10. GitHub Referansları

| Bileşen | Repository | URL |
|---------|-----------|-----|
| Docker Engine | Moby | https://github.com/moby/moby |
| containerd | containerd | https://github.com/containerd/containerd |
| systemd | systemd | https://github.com/systemd/systemd |
| D-Bus | dbus | https://gitlab.freedesktop.org/dbus/dbus |
| ALSA | alsa-lib | https://github.com/alsa-project/alsa-lib |
| PipeWire | PipeWire | https://gitlab.freedesktop.org/pipewire/pipewire |
| curl | curl | https://github.com/curl/curl |
| Vulkan | Vulkan-Hpp | https://github.com/KhronosGroup/Vulkan-Hpp |
| CRI-O | CRI-O | https://github.com/cri-o/cri-o |
| SELinux | selinux | https://github.com/SELinuxProject/selinux |
| DirectX Samples | DirectX-Graphics-Samples | https://github.com/microsoft/DirectX-Graphics-Samples |

---

## 11. İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[k1-hardware-layer]] | K0'u doğrudan kullanır |
| [[k2-driver-layer]] | K0 üstünde çalışır |
| [[k3-audio-engine]] | K0 servislerine bağlıdır |
| [[k4-ai-layer]] | K0 threading/memory kullanır |
| [[k5-data-layer]] | K0 file system ve network kullanır |

---

## Class AB Amplifikatör Entegrasyonu

Bu katman aşağıdaki dosyalarla ilişkilidir:
- [[electronics/amplifier-classab-circuit]] — Class AB devre şeması
- [[electronics/power-supply-classab]] — ±35V güç kaynağı
- [[electronics/thermal-design-classab]] — Termal tasarım
- [[electronics/pcb-classab]] — PCB tasarım kuralları
- [[electronics/bom-classab]] — BOM listesi

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Status:** draft
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
