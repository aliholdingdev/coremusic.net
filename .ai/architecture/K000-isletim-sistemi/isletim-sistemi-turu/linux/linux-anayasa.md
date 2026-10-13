---
title: "Linux Anayasa — Kernel, GNU/Distro Ayrımı, Sürüm Modeli ve CoreMusic/Linux Hedefi"
type: architecture-deep-dive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/linux/linux-anayasa
node_type: module
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/linux
children: []
depends_on:
  - "K000-isletim-sistemi/os-master.md"
  - "K000-isletim-sistemi/isletim-sistemi-turu/index.md"
evidence:
  - "https://www.kernel.org/ (2026-10-12 erişim — canlı sürüm tablosu)"
  - "https://docs.kernel.org/scheduler/sched-eevdf.html (2026-10-12)"
  - "https://docs.pipewire.org/ (2026-10-12 — PipeWire 1.6.9)"
  - "https://docs.kernel.org/process/howto.html (2026-10-12 — HTTP 200)"
  - "https://docs.kernel.org/core-api/symbol-namespaces.html (2026-10-12 — HTTP 200)"
  - ".ai/AGENTS.md §6/§17 (disk kanıtı)"
  - "shared/composer.json · package.json (disk kanıtı)"
  - ".github/workflows/ci.yml L14 · L17 · L27 · L28 (disk kanıtı)"
validation: []
open_questions: []
group: mimari
---

# Linux Anayasa (linux-anayasa.md)

> **ELI15:** Linux, bilgisayarın donanımını yöneten çekirdeğin (kernel) adıdır; bilgisayarı
> kullanılabilir bir sisteme çeviren tüm programlar (kabuk, dosya araçları, servis
> yöneticisi) ise GNU araçları + dağıtım (distro) katmanıdır. "Linux" denince çoğu
> kullanıcının gördüğü şey, ikisinin birleşimi olan bir dağıtımdır.

**Durum etiketi (bu dosya):** ŞARTNAME/belge — IMPLEMENTED (belge) · Kod: ⚠️ PLANNED
(Neva Engine Linux backend'i kod kanıtı repoda YOK; bu dosya yalnız şartname/doctrine düzeyinde).

---

## §1 Amaç ve Kapsam

| Öğe | Değer |
|---|---|
| Dosya rolü | Linux derin-dalı **anayasası** — tanım, sürüm modeli, aileler, katmanlar, CoreMusic hedefi |
| Kapsam | Linux kernel + GNU userland + distro ekosistemi; sürüm/kararlılık modeli; mimari katmanlar; CoreMusic/ALSA-PipeWire hedefi |
| Kapsam dışı | Kernel içi implementasyon detayı (→ `linux-kernel.md`) · syscall envanteri (→ `linux-api.md`) · systemd/D-Bus/Wayland (→ `linux-framework.md`) · ses yığını (→ `linux-ses-yigini.md`) · iddia·URL·durum tablosu (→ `linux-dogrulama.md`) |
| Kardeş dallar | `windows/` ve `macos/` (başka ajanlar — bu dosya oraya yazmaz) |
| Üretim tarihi | 2026-10-12 · K-Space V3 OS türü derin-dalı (kullanıcı kararı 2026-10-12) |
| Dil | Türkçe anlatım + İngilizce teknik terim (parantez içinde) |

**Okuma sırası:** `linux-anayasa.md` → `linux-kernel.md` → `linux-api.md` →
`linux-framework.md` → `linux-ses-yigini.md` → `linux-dogrulama.md`.

---

## §2 Linux Nedir: Çekirdek · GNU · Dağıtım Ayrımı

"Linux" sözcüğü üç farklı şeyi aynı anda ifade eder; CoreMusic şartnamesinde bu üçü
her zaman ayrı yazılır (yanılgıyı önleme kuralı).

| Terim | Kapsadığı | Resmi kaynak | Bu oturumda doğrulandı mı? |
|---|---|---|---|
| **Linux kernel** | Monolitik + modüler çekirdek; sürücüler, MM, VFS, ağ yığını | https://www.kernel.org/ · https://docs.kernel.org/ | ✅ (2026-10-12 — canlı sürüm sayfası çekildi) |
| **GNU userland** | Çekirdeğin üstündeki temel araçlar ve C kütüphanesi tartışması | https://www.gnu.org/ ⚠️ | ⚠️ bu oturumda erişilmedi (bilinen proje sayfası) |
| **Linux dağıtımı (distro)** | Kernel + userland + paket yöneticisi + servis yöneticisi + masaüstü bütünleşmesi | Dağıtım resmi siteleri — §7 tablosu | ⚠️ distro siteleri bu oturumda tek tek çekilmedi; varlıkları systemd.io "distributions" bölümünden çapraz destekli |
| **"GNU/Linux"** | Çekirdek + GNU araçlarının birlikte adlandırılması | GNU projesi terminolojisi ⚠️ | ⚠️ (tartışmalı isimlendirme — bu dosya tartışmaya girmez, yalnız ayrımı zorunlu kılar) |

**Kural (CoreMusic yazım kuralı):** Bir sürüm/kararlılık iddiası yazılırken hangi
katmanın kastedildiği belirtilir. Örnek: "PipeWire 1.6.9" bir userland sürümüdür;
"6.12.112" bir kernel LTS sürümüdür; ikisi karıştırılmaz.

### §2.1 Çekirdek mi "işletim sistemi" mi?

| Sınıf | Tanım | Linux örneği |
|---|---|---|
| Monolitik çekirdek | Tüm alt sistemler tek adres alanında, çekirdek modunda çalışır | Linux (sürücüler modüler olsa da hepsi çekirdek bağlamındadır) |
| Mikroçekirdek | Minimal çekirdek; sürücüler/servisler kullanıcı alanında | MINIX ⚠️, seL4 ⚠️ (bu dalın konusu değil; karşılaştırma amaçlı) |
| Hibrit | Her ikisinin karışımı | Windows NT ailesi ⚠️ (kardeş `windows/` dalının konusu) |

> **Not (2026-10-12):** "Linux monolitiktir" ifadesi `linux-kernel.md` §1'de
> modülerlik (loadable kernel modules) ile birlikte ele alınır — tek başına
> "monolitik" demek modülerliği dışlamaz.

---

## §3 Tarihçe ve Sürüm Zaman Çizelgesi

Aşağıdaki tablonun 2024–2026 satırları **bu oturumda canlı kaynaklardan doğrulanmıştır**;
daha eski satırlar yaygın tarih bilgisi olup tek-URL doğrulaması yapılmamıştır ve
`⚠️` işaretlidir (zero-hallucination disiplini).

| Yıl | Olay | Kaynak | Durum |
|---|---|---|---|
| 1991 | Linus Torvalds ilk Linux sürümünü (0.01) duyurdu | https://en.wikipedia.org/wiki/Linux_kernel ⚠️ | ⚠️ bu oturumda çekilmedi |
| 1992 | Linux, GNU Genel Kamu Lisansı (GPL) v2 ile lisanslandı | kernel ağacındaki `COPYING` dosyası ⚠️ | ⚠️ dosya içeriği bu oturumda çekilmedi |
| 1994 | Linux 1.0 | https://kernelnewbies.org/ arşivi ⚠️ | ⚠️ |
| 2003–2004 | 2.6 serisi — `-mm` ağacı, scheduler revizyonları (CFS 2.6.23, 2007 ⚠️) | LWN arşivi ⚠️ | ⚠️ |
| 2011 | Sürümleme değişti: büyük sürüm numarası artık "dal" değil (3.0 = 2.6.x devamı) | https://kernel.org FAQ/Releases ⚠️ (sayfa çekildi ama bu cümle doğrudan metinde yok) | ⚠️ |
| 2024-11-17 | **Linux 6.12 yayında** — PREEMPT_RT ana akıma (mainline) girdi (~20 yıl çalışma); EEVDF tam destek | https://kernelnewbies.org/Linux_6.12 | ✅ (arama özeti alındı) |
| 2024 | EEVDF'ye geçiş 6.6'da **seçenek olarak** başladı (CFS'ten kademeli) | https://docs.kernel.org/scheduler/sched-eevdf.html | ✅ (sayfa çekildi) |
| 2026-10-11 | **stable: 7.2.10** (7.2 serisinin son stabil yaması — 2026-10-11) | https://www.kernel.org/ | ✅ (canlı tablo) |
| 2026-10-12 | **mainline: 7.3-rc7** (rc haftalık döngüde) | https://www.kernel.org/ | ✅ (canlı tablo) |
| 2026-10-11 | **longterm: 6.18.56** (en güncel LTS) | https://www.kernel.org/ | ✅ (canlı tablo) |
| 2026-10-10 | linux-next: next-20261009 (birleşim testi dalı) | https://www.kernel.org/ | ✅ (canlı tablo) |

> **Sürümleme gerçeği (2026-10-12 ölçümü):** kernel.org ana sayfası o tarihte
> `mainline 7.3-rc7`, `stable 7.2.10`, `longterm 6.18.56 · 6.12.112 · 6.6.158 ·
> 6.1.189 · 5.15.222 · 5.10.271` satırlarını taşıyordu. Yani Linux 2026'da
> **7.x çağında**, LTS hattı ise **6.x**'te kalmaktadır.

---

## §4 Sürüm ve Kararlılık Modeli (kernel.org LTS)

Linux kernel sürüm döngüsü dört kola ayrılır; her kolun güncelleme sözleşmesi farklıdır.
CoreMusic Linux hedefi (Neva Engine) **yalnız LTS kollarına** dayanır (karar: bu dosya §9).

| Kol | Ne anlama gelir | 2026-10-12 durumu (kernel.org) | Çekirdek destek süresi |
|---|---|---|---|
| **mainline** | Torvalds'ın ağacı; rc (release candidate) haftalık yayınlanır | 7.3-rc7 · 2026-10-12 | Geliştirme; "destek" kavramı yok |
| **stable** | Son mainline sürümünün yama hattı (bug fix) | 7.2.10 · 2026-10-11 | Sonraki stable serisine kadar |
| **longterm (LTS)** | Seçilmiş sürümlerin uzun dönem yama hattı | 6.18.56 · 6.12.112 · 6.6.158 · 6.1.189 · 5.15.222 · 5.10.271 | LTS taahhüdü süresince (süreler kernel.org'da per-seri ilan edilir — ⚠️ süreler bu oturumda sayfa metninden okunmadı) |
| **linux-next** | Birleşme öncesi test yığını | next-20261009 · 2026-10-10 | Test amaçlı; üretime alınmaz |

**Kaynak:** https://www.kernel.org/ (2026-10-12, canlı tablo).

### §4.1 LTS seçim mantığı

| Kriter | Açıklama | CoreMusic için anlamı |
|---|---|---|
| Dağıtım LTS tercih eder | Ubuntu/RHEL/SUSE uzun destek sürümlerini LTS çekirdekler üzerine kurar | Distro ne LTS çekirdek verirse Neva Engine onunla test edilir |
| API/ABI kararlılığı | Distro bazlı yamalar (downstream patches) çekirdeği modifiye edebilir | "6.12" = upstream 6.12 **+** distro yamaları; davranış farkları mümkün ⚠️ |
| RT çekirdek | 6.12+ ile `CONFIG_PREEMPT_RT` ana akımda (→ `linux-kernel.md` §7) | Ses motoru için hedef yapılandırma 6.12 LTS + PREEMPT_RT |

### §4.2 Sürüm sütunlarından CoreMusic hedef çıkarımı

| Hedef bileşen | Minimum kernel | Gerekçe | Durum |
|---|---|---|---|
| Neva Engine (JUCE + ALSA backend) | ⚠️ minimum sürüm belirlenmedi | ALSA PCM API'si eski sürümlerde de mevcut; kesin minimum için emülasyon testi gerekir | ⚠️ AÇIK (Q1) |
| PipeWire ses yığını | ⚠️ PipeWire sürümü ile kernel sürümü ilişkisi doğrulanmadı | PipeWire 1.6.9 doğrulandı; alt sınır bilinmiyor | ⚠️ AÇIK (Q2) |
| PREEMPT_RT çekirdek | **6.12+** (ana akım desteği 6.12'de girdi) | kernelnewbies Linux_6.12 + kernel/Kconfig.preempt @v6.12 | ✅ |

---

## §5 Dağıtım (Distro) Aileleri

Dağıtımlar kernel'i alır, userland'i (GNU araçları, libc, servis yöneticisi, paket
yöneticisi) bütünleştirir. CoreMusic bu tabloyu **yalnız hedef ortam seçimi** için
kullanır; her distro için şartname yazılmaz.

| Aile | Örnek dağıtımlar | Paket yöneticisi | libc (tipik) | Varlık kanıtı (bu oturum) |
|---|---|---|---|---|
| Debian | Debian · Ubuntu · Linux Mint ⚠️ | dpkg/apt ⚠️ | glibc ⚠️ | systemd.io distributions sayfasında Debian/Ubuntu package+bugtracker linkleri ✅ (varlık) |
| Red Hat | Fedora · RHEL · CentOS Stream · Rocky/AlmaLinux ⚠️ | rpm/dnf ⚠️ | glibc ⚠️ | systemd.io'da Fedora package/source linkleri ✅ (varlık) |
| SUSE | openSUSE · SLES ⚠️ | zypper/rpm ⚠️ | glibc ⚠️ | systemd.io'da openSUSE bugtracker/package linkleri ✅ (varlık) |
| Arch | Arch Linux · Manjaro ⚠️ | pacman ⚠️ | glibc ⚠️ | systemd.io'da Arch package+bugtracker linkleri ✅ (varlık) |
| Gentoo | Gentoo ⚠️ | portage ⚠️ | glibc/musl ⚠️ | systemd.io'da Gentoo package+bugtracker linkleri ✅ (varlık) |
| Alpine | Alpine Linux ⚠️ | apk ⚠️ | **musl** ⚠️ (musl ana sayfasında sponsorlar/donörler; Alpine eşlemesi ⚠️) | musl libc resmi sitesi ✅ (musl var; Alpine eşlemesi doğrulanmadı) |
| Mageia | Mageia ⚠️ | rpm ⚠️ | glibc ⚠️ | systemd.io'da Mageia bugtracker/package linkleri ✅ (varlık) |

**Kaynaklar:** musl — https://musl.libc.org/ (✅ çekildi: "musl is an implementation of
the C standard library built on top of the Linux system call API", son sürüm
**1.2.6**); distro varlık çaprazı — https://systemd.io/ (✅ çekildi: "The various
distributions" bölümünde Arch/Debian/Fedora/Gentoo/Mageia/Ubuntu/openSUSE linkleri).

> **⚠️ Sınır:** Distro **sürümleri** (örn. "Ubuntu 24.04", "Fedora 42") bu oturumda
> doğrulanmadı ve yazılmadı. Sürüm iddiası gereken görevlerde taze doğrulama yapılır
> (→ `linux-dogrulama.md` Q3).

### §5.1 CoreMusic hedef distro politikası (taslak — onay bekliyor)

| Kullanım senaryosu | Önerilen sınıf | Gerekçe | Durum |
|---|---|---|---|
| Neva Engine geliştirme/test | Debian veya Arch ailesi ⚠️ | Geniş ALSA/PipeWire paket erişimi (kamuoyu pratiği — bu oturumda doğrulanmadı) | ⚠️ TASARIM, onay bekliyor |
| Gömülü hedef (XMOS/ARM) | musl tabanlı (Alpine tarzı) ⚠️ | Küçük ayak izi iddiası — doğrulanmadı | ⚠️ TASARIM, onay bekliyor |
| Sunucu (web tarafı) | Mevcut stack Windows/IIS varsayımı ⚠️ | Repodaki PHP/Node kanıtları deployment OS'unu göstermez | ⚠️ AÇIK (Q4) |

---

## §6 Mimari Katmanlar (Kernel → Syscall → libc → Uygulama)

```text
┌────────────────────────────────────────────────────────────────────┐
│  Uygulama katmanı                                                   │
│  Neva Engine (C++20/JUCE) · PHP 8.4 (php-fpm) · Node dev araçları │
│  PipeWire client · JUCE ALSA backend · libasound uygulaması        │
├────────────────────────────────────────────────────────────────────┤
│  libc katmanı                                                      │
│  glibc veya musl — POSIX API + Linux uzantıları                   │
│  (JUCE: libasound2-dev ↔ alsa/asoundlib.h)                        │
├────────────────────────────────────────────────────────────────────┤
│  Sistem çağrısı ABI'si (syscall boundary)                          │
│  x86-64: syscall talimatı · argümanlar rdi/rsi/rdx/r10/r8/r9       │
│  (man7 syscall(2) tabloları ✅)                                    │
├────────────────────────────────────────────────────────────────────┤
│  Linux kernel (7.2.10 stable / 6.18 LTS hedefi)                    │
│  Scheduler (EEVDF) · MM · VFS · ağ · ALSA çekirdek sürücüsü       │
│  kesme/softirq · PREEMPT_RT (6.12+, opsiyonel)                    │
├────────────────────────────────────────────────────────────────────┤
│  Donanım                                                           │
│  CPU/RAM · PCIe ses kartı (HD-Audio/USB Audio) · SoC ASoC codec    │
└────────────────────────────────────────────────────────────────────┘
```

| Katman | Sorumluluk | CoreMusic ilgisi | Ayrıntı dosyası |
|---|---|---|---|
| Uygulama | İş mantığı, DSP zinciri, web | Neva Engine + PHP/Node | `linux-framework.md` §7 |
| libc | POSIX sarmalayıcılar, dinamik yükleyici | glibc varsayımı; musl alternatif | `linux-api.md` §5 |
| Syscall ABI | Tek geçiş kapısı | Ses motoru: read/write/mmap/ioctl/poll | `linux-api.md` §3–§4 |
| Kernel | Kaynak yönetimi, zamanlama, sürücüler | ALSA PCM, EEVDF, PREEMPT_RT | `linux-kernel.md` |
| Donanım | Ses kartı/codec | PCM3168A/AK4458 hedefleri (k19/k17 belgeleri) | kardeş dal `electroncis/` |

### §6.1 Kullanıcı / çekirdek ayrımının ses motoru için anlamı

| Geçiş | Maliyeti | Ses motoru etkisi |
|---|---|---|
| syscall çağrısı | Bağlam değişimi (user→kernel) | IO thread'inde beklenen maliyet; real-time thread'de minimize edilir |
| sayfa hatası (page fault) | MM yardımı | Real-time thread'de **yasak** (ön-tahsis: `mlockall`) ⚠️ |
| kesme (IRQ) | Donanım→çekirdek | Ses kartı IRQ'u → ALSA period tamamlanma bildirimi |
| context switch | Zamanlayıcı | RT öncelik (SCHED_FIFO) ile ses thread'i öne alınır ⚠️ |

---

## §7 CoreMusic ile Linux Hedefi (ALSA/PipeWire · §6/§17 ilişkisi)

### §7.1 Vault içi ilişkiler (disk kanıtları)

| İlişki | Kanıt (disk) | Ne diyor |
|---|---|---|
| Ruting | `.ai/AGENTS.md` §6 routing tablosu (satır: "C++, ASIO, JUCE, audio, DSP, Neva Engine, ring buffer, WASAPI, hardware → Embedded Engineer (primary) · DevOps (secondary)") | Ses/C++ işleri embedded-engineer'a bağlanır — Linux ses yığını da bu domain |
| Edge case | `.ai/AGENTS.md` §17 madde 6: "ASIO device loss → **WASAPI fallback**" | Edge case **Windows-özgündür**; Linux'ta karşılığı için bkz. aşağıdaki §7.2 (açık soru) |
| OS bağımlılığı | `K000-isletim-sistemi/os-bagimliliklar.md` satır 32: "K018-devops → K000 [TASARIM] Build/çalıştırma ortamı OS sürümüne bağlıdır" | CI/CD ortamı OS sürümüne bağımlı — Linux hedefi bu bağımlılığı doğrudan etkiler |
| Kullanım iddiaları | `K000-isletim-sistemi/os-master.md` satır 88: "used_by (iddia): K003-audio · K004-dsp · K005-firmware · K018-devops — tasarım iddiası" | K000 (OS) dalının ses/DSP/firmware/devops dallarınca kullanıldığı **iddia** (tasarım seviyesi) |
| Container ilişkisi | `os-master.md` satır 37: "CI/CD, konteyner, deploy → K018-devops" | Namespace/cgroup konuları bu dalda anlatılır, sahiplik K018'dedir |

### §7.2 §17/6 ilişkisi — ASIO→WASAPI fallback'in Linux karşılığı

| Platform | Birincil düşük gecikme yolu | Fallback | Durum |
|---|---|---|---|
| Windows | ASIO (JUCE `juce_ASIO_windows.cpp`) | WASAPI (§17 madde 6 — vault kuralı) | Vault kuralı IMPLEMENTED (belge) |
| Linux | ALSA hw doğrudan (JUCE `juce_ALSA_linux.cpp` ✅ JUCE kaynağından) | PipeWire üzerinden paylaşımlı akış (kamuoyu pratiği) ⚠️ | ⚠️ PLANNED — kural yok, AÇIK (Q5) |

> **Karar boşluğu:** `.ai/AGENTS.md` §17'de Linux için bir fallback kuralı **yoktur**.
> Bu dosya yeni kural **önermez**; yalnız boşluğu işaretler (kural ekleme yetkisi root/ADR).

### §7.3 Repo disk kanıtları — Linux hedefinin gerçek zemini

| Kanıt | İçerik | Linux ilgisi |
|---|---|---|
| `shared/composer.json` | `"php": ">=8.4"`, `ext-apcu`, `ext-json`, `ext-mbstring`, `ext-pdo`; phpunit ^10.5; phpstan ^1.10 | PHP 8.4 runtime Linux'ta php-fpm + APCu ile çalışır (paket karşılıkları distro bazlı ⚠️) |
| Alt alan composer.json'ları | `api.` / `auth.` / `home.` / `media.coremusic.net/composer.json` (aynı ekosistem) | 5 composer.json = 5 PHP birimi; Linux dağıtım hedefi tümünü etkiler |
| Kök `package.json` | devDependencies: `@playwright/test ^1.63.0`, `vitest ^5.0.3`, `jsdom ^30.1.1`, `axe-playwright ^1.0.0` | Node tabanlı test araçları Linux CI koşucusunda çalışır (Playwright Linux browser binary'leri ⚠️) |
| `projects/NevaEngine` | Vault kaydı: dizin var, 0 dosya (`.ai/AGENTS.md` §24.3 notu) | C++20/JUCE ses motoru **kod kanıtsız** → Linux backend spekülasyonu yapmaz |

### §7.4 CoreMusic/Linux hedef yığını (şartname özeti)

```text
Neva Engine (C++20, PLANNED — kod yok)
   └─ JUCE 9 audio device abstraction
        ├─ [Windows] WASAPI / ASIO (vault: §17 madde 6 fallback)
        └─ [Linux]   JUCE_ALSA → juce_ALSA_linux.cpp (✅ upstream kanıt)
                       ├─ doğrudan ALSA hw (düşük gecikme, exclusive benzeri)
                       └─ PipeWire (default ses sunucusu) → ALSA (paylaşımlı)
   └─ Kernel: ALSA PCM + (opsiyonel) PREEMPT_RT çekirdek (6.12+ LTS)
```

---

## §8 Kapsam Sıfırı (Ne Değil)

| Sınır | Ne | Nereye gider |
|---|---|---|
| Kernel iç implementasyon (kod) | Bu dal **kod yazmaz** (embedded-engineer profili: kod PLANNED etiketi zorunlu) | İleride scaffold: `projects/NevaEngine` |
| Windows API'leri | WASAPI/COM/WinRT | Kardeş dal `windows/` (başka ajan) |
| macOS CoreAudio | — | Kardeş dal `macos/` (başka ajan) |
| PCB/donanım | DAC/ADC/codec devreleri | `K000-isletim-sistemi/electroncis/` + audio-hardware-engineer |
| DSP algoritması | EQ/reverb/filtre | dsp-firmware-engineer (handover) |
| Web/PHP/domain kodu | Controller/middleware | backend-architect |
| Container sahipliği | Docker/K8s operasyonu | K018-devops (bu dal yalnız namespace/cgroup **kavramını** anlatır, sahiplenmez) |
| Frozen ADR metinleri | ADR-001…037 | Dokunulmaz — yalnız referans |

---

## §9 Kararlar ve Varsayımlar (bu dosyanın taşıdıkları)

| # | Karar/varsayım | Gerekçe | Durum |
|---|---|---|---|
| K1 | CoreMusic Linux hedefi **LTS çekirdek** üzerine kurulur (mainline/rc üretime alınmaz) | kernel.org kol modeli (§4); üretim kararlılığı | ✅ Bu dosyada alındı (belge düzeyi) |
| K2 | Ses yığını hedefi **ALSA + PipeWire**'dur; JACK hattı yalnız uzman senaryosu | PipeWire resmi dokümanı ("professional audio graph processing like JACK" hedefi ✅) | ✅ Belge düzeyi |
| K3 | libc varsayımı **glibc**'dir; musl yalnız gömülü senaryo adayı | glibc endüstriyel varsayımı ⚠️; musl 1.2.6 doğrulandı ama CoreMusic bağımlılıkları (ext-apcu vb.) musl uyumu doğrulanmadı | ⚠️ AÇIK (Q6) |
| K4 | §17/6 (ASIO→WASAPI) Linux'a **genişletilmez**; yeni fallback kuralı root/ADR kararıdır | Vault kural değişimi subagent yetkisi dışı | 🔒 SONRA (yeni ADR gerekçeli) |
| K5 | Distro seçimi bu dosyada **kilitlenmez** | Hedef senaryo (gömülü vs masaüstü vs sunucu) netleşmedi | ⚠️ AÇIK (Q4) |

**ADR notu:** K4 için yeni ADR (numara: `.ai/.decisions/` envanterinden sonraki boş numara —
bu dosya numara **uydurmaz**) gerekir. K1–K3 şartname düzeyi olup ADR eşiği için
`architect-reviewer` + root onayı gerekir.

---

## §10 Açık Sorular

| # | Soru | Yönlendirme |
|---|---|---|
| Q1 | Neva Engine'in Linux'ta destekleyeceği **minimum kernel** hangisi? (ALSA PCM hedef sürümleri) | embedded-engineer + test |
| Q2 | PipeWire hangi alt kernel sürümlerinde desteklenir? (resmi alt sınır belirsiz) | research → PipeWire GitLab |
| Q3 | Hedef distro sürümleri (örn. Ubuntu LTS mu, Debian stable mı?) | root onayı |
| Q4 | Web tarafı (PHP/Node) Linux'a taşınacak mı, yoksa Windows sunucu mu kalıyor? | backend-architect + root |
| Q5 | Linux için §17 benzeri fallback kuralı (ALSA hw hatası → PipeWire?) yazılacak mı? | root + ADR |
| Q6 | `ext-apcu` gibi PHP eklentileri musl tabanlı distrolarda mevcut mu? | backend-architect (mobilite gereksinimi yoksa glibc kalır) |
| Q7 | PREEMPT_RT çekirdek mi yoksa `PREEMPT_DYNAMIC` distro çekirdeği mi hedef? | embedded-engineer + devops |

---

## §11 Kaynakça (bu dosya)

| # | Kaynak | Erişim tarihi | Durum |
|---|---|---|---|
| S1 | https://www.kernel.org/ | 2026-10-12 | ✅ canlı sürüm tablosu çekildi |
| S2 | https://docs.kernel.org/ (7.3.0-rc7 doküman ağacı) | 2026-10-12 | ✅ |
| S3 | https://docs.kernel.org/scheduler/sched-eevdf.html | 2026-10-12 | ✅ (EEVDF/CFS geçişi 6.6) |
| S4 | https://kernelnewbies.org/Linux_6.12 | 2026-10-12 | ✅ (RELEASED 17 Nov 2024 · PREEMPT_RT mainline) |
| S5 | https://musl.libc.org/ | 2026-10-12 | ✅ (musl 1.2.6 · tanım) |
| S6 | https://systemd.io/ | 2026-10-12 | ✅ (PID 1 · distro link listesi) |
| S7 | https://docs.pipewire.org/ | 2026-10-12 | ✅ (PipeWire 1.6.9) |
| S8 | https://man7.org/linux/man-pages/man2/syscall.2.html | 2026-10-12 | ✅ (ABI tabloları) |
| S9 | `C:\www\coremusic.net\shared\composer.json` | 2026-10-12 | ✅ disk kanıtı |
| S10 | `C:\www\coremusic.net\package.json` | 2026-10-12 | ✅ disk kanıtı |
| S11 | `.ai/AGENTS.md` §6 · §17 · §24.3 | 2026-10-12 | ✅ disk kanıtı |
| S12 | `K000-isletim-sistemi/os-master.md` · `os-bagimliliklar.md` | 2026-10-12 | ✅ disk kanıtı |

---

## §12 Çekirdek Geliştirme Süreci ve İstikrar Hattı Kuralları

§4'teki LTS kararının (K1) dayanağı yalnız sürüm numarası tablosu değil, kernel.org'un
kendi süreç belgeleridir. Bu bölüm o belgeleri 2026-10-12'de HTTP 200 ile doğrulayarak
kararın gerekçesini sabitler.

| Süreç adımı | Belgenin taşıdığı çerçeve | CoreMusic için anlamı | Kaynak |
|---|---|---|---|
| Geliştirme akışı | Katkı, inceleme (review) ve birleştirme beklentileri tek belgede tanımlanır | Neva Engine Linux backend'i kullanıcı alanı kütüphanesidir; çekirdek katkısı bu dalın kapsamı değildir | https://docs.kernel.org/process/howto.html |
| İstikrar hattı kuralları | stable hattının kapsamı ve yama kabul kriterleri ayrı belgede tanımlanır | §4 tablosundaki "sonraki stable serisine kadar" ifadesinin resmi dayanağı budur | https://docs.kernel.org/process/stable-kernel-rules.html |
| Kol sözleşmesi | mainline / stable / longterm kolları ayrı güncelleme sözleşmeleri taşır | K1 kararı (§9): üretime yalnız LTS kolu girer | kernel.org ana sayfası (§4 · S1) |
| Katkı kimliği | MAINTAINERS/CONTRIBUTING metinleri bu oturumda çekilmedi | Rol/sahiplik iddiası üretilmez | ⚠️ VERIFICATION REQUIRED |

### §12.1 Süreç → sürüm karar zinciri

| Karar | Dayandığı süreç gerçeği | Sonuç |
|---|---|---|
| rc haftalık döngü | mainline'ın release candidate üretimi düzenli (§3 canlı tablo) | rc üretime alınmaz |
| stable yama hattı | stable kuralları yamaların yalnız hataya yönlendirildiğini şart koşar | üretim görüntüsü stable/LTS üzerine kurulur |
| longterm taahhüdü | longterm serileri ayrı ilan edilir ve ayrı süresi olur | CoreMusic hedef çekirdeği bir longterm serisinden seçilir (§4.2) |

---

## §13 Çekirdek Sembol Ad Alanları (Symbol Namespaces) — Modülerlik Sınırı

Linux çekirdeği, yüklenir modüllerin kullanabildiği sembolleri ad alanlarıyla (symbol
namespace) sınırlar. Bu, "monolitik ama sözleşmeli" yapının belge düzeyindeki karşılığıdır.

| Ad alanı sınıfı | Ne düzenler | Bu dalın ilgisi | Kaynak |
|---|---|---|---|
| Sembol ad alanı beyanı | Bir sembolün hangi alt sisteme ait olduğunu ve kimin kullanabileceğini tanımlar | sürücü/çekirdek sınırı (§6) kavramının sembol düzeyindeki hali | https://docs.kernel.org/core-api/symbol-namespaces.html |
| çekirdek sembolleri | Zamanlama, bellek, kesme gibi temel alt sistemlerin dışa kapalı yüzeyi | linux-kernel.md §1–§7'nin sınırı | aynı kaynak |
| sürücü sembolleri | Sürücü altyapısının paylaştığı yardımcı semboller | kardeş dosya `linux-kernel.md` §6 (modül yükleme) | aynı kaynak |

> **Sınır notu:** Bu bölüm yalnız kavram düzeyindedir; hangi sembolün hangi ad alanında
> olduğu **tek tek listelenmemiştir** (listeler sürümle değişir, bu oturumda çekilmedi —
> ⚠️ VERIFICATION REQUIRED).

---

## §14 Çekirdek API Yüzeyinin Sınıflandırılması

docs.kernel.org çekirdek API'sini ayrı bir belgede toplar; bu dal bu yüzeyi yalnız
**referans** olarak kullanır, uygulamaz.

| API sınıfı | Kapsam | Bu dalda kullanım | Kaynak |
|---|---|---|---|
| Çekirdek yardımcıları | Bellek, kopyalama, biçimlendirme, dizi/öbek yardımcıları | yalnız isim düzeyinde referans | https://docs.kernel.org/core-api/kernel-api.html |
| Zamanlayıcı/zaman | Zaman damgası ve saat alt sistemi | linux-kernel.md §7 (kesme/softirq) ile ilişkili | bu dosya §17 |
| Yükleme kuyrukları (workqueue) | Çekirdek içi ertelenmiş iş yürütme | kesme bağlamından iş ayırma kavramı | bu dosya §17.1 |
| Bağlayıcı ABI | Kullanıcı/çekirdek sınırı | §6 katman şemasındaki syscall sınırı | §6 · S8 |

### §14.1 API yüzeyi sınıflarının bu dal için geçerliliği

| Yüzey | Neva Engine için durum | Gerekçe |
|---|---|---|
| Çekirdek yardımcıları | kullanılmayacak | kullanıcı alanı uygulaması çekirdek sembolü kullanamaz |
| Yükleme kuyrukları | kullanılmayacak | aynı gerekçe |
| Sürücü API'si | yalnız referans | ses kartı sürücüsü distro'nun paketidir |
| Kullanıcı alanı API'si | kullanılacak | ALSA/PipeWire üzerinden (§7.4) |

---

## §15 Sürücü Altyapısı İskeleti ve "Staging" Alanı

| Kavram | Belgede ne anlatılır | CoreMusic kararı | Kaynak |
|---|---|---|---|
| Sürücü altyapısı | Sürücü modeli, kaydetme (register) ve yaşam döngüsü yardımcıları | ses kartı sürücüsü yazılmaz; hazır distro sürücüsü kullanılır | https://docs.kernel.org/driver-api/infrastructure.html |
| staging alanı | Olgunlaşmamış/taşınma sürecindeki sürücülerin tutulduğu alan | production hedefinde staging sürücüsü **kullanılmaz** | https://docs.kernel.org/staging/index.html |
| Belge hiyerarşisi | driver-api ağacı ayrı bir hiyerarşidedir (core-api ile karıştırılmaz) | bu dal dosyalarında hangi ağacın kastedildiği açık yazılır | iki kaynak birlikte |

### §15.1 Staging reddi gerekçesi

| Gerekçe | Açıklama | Sonuç |
|---|---|---|
| Olgunluk | staging sürücüleri değişken API taşır | sürüm bağımlılığı riski |
| Destek | upstream destek taahhüdü staging için ayrıdır | üretim güvencesi yok |
| Alternatif | HD-Audio/USB Audio için olgun sürücüler ana ağaçtadır | ana ağaç sürücüsü tercih edilir |

---

## §16 Kalite Araçları: KASAN ve Doğrulama Disiplini

| Araç | Ne yapar | Bu dal için anlamı | Kaynak |
|---|---|---|---|
| KASAN | Bellek erişim hatalarını (taşma, serbest sonrası erişim, aralık ihlali) çalışma anında yakalar | çekirdek düzeyinde hata yakalama, kod PLANNED olduğu için uygulanmaz; doğrulama disiplini referansıdır | https://docs.kernel.org/dev-tools/kasan.html |
| Dev-tools ağacı | Araçların genel indeksi (KASAN, KUnit, ftrace vb.) | bu dalda yalnız indeks düzeyinde | https://docs.kernel.org/dev-tools/index.html |
| Kapsam dışı | Araçların kurulumu ve kullanımı | ayrı dosyanın konusu değil; uygulama kanıtı yok | ⚠️ VERIFICATION REQUIRED |

> **Çapraz not:** CoreMusic kod yüzeyi (PHP/Node) testleri `.github\workflows\ci.yml`
> içinde koşulur; çekirdek düzeyi KASAN kapsamı **dışındadır** ve bu dosya böyle bir
> iddia taşımaz (§19).

---

## §17 Uzun Dönem Referans Sayımı (RCU) — Kavram Sınırı

RCU (Read-Copy-Update), çekirdeğin okuma tarafı kilitlenmeyen veri paylaşım mekanizmasıdır.
Bu dosya onu yalnız **kavram** olarak; uygulaması `linux-kernel.md` §7'nin konusudur.

| Kavram | Belgenin kapsamı | Bu dosyadaki rolü | Kaynak |
|---|---|---|---|
| RCU okuma tarafı | OKU (read-side) kritik bölümün kuralları | gerçek zamanlı okuma desenleriyle akraba kavram | https://docs.kernel.org/RCU/index.html |
| Çağrılmış (callback) tarafı | Serbest bırakma erteleme modeli | kesme/softirq ile ilişkili (§6.1) | aynı kaynak |
| Sürücü uygulaması | Sürücülerde kullanım kalıpları | bu dalda YOK; uygulama iddiası yazılmaz | ⚠️ VERIFICATION REQUIRED |

### §17.1 Yükleme kuyruğu (workqueue) ile karşılaştırma

| Mekanizma | Bağlam | Uygun olduğu yer | Bu dal |
|---|---|---|---|
| Workqueue | süreç bağlamı (bağlam değişimi kabul edilebilir) | ertelenen işler | kavram düzeyi (§14) |
| Softirq/timer | kesme bağlamı | zaman duyarlı iş | linux-kernel.md §7 |
| Kullanıcı alanı eşdeğeri | pthread/eventfd ailesi | Neva Engine IO thread'i | linux-api.md (§3) |

---

## §18 Ağ Yığını Dokümantasyonu — Kapsam Sınırı

| Öğe | Belge ne kapsar | Bu dalın tutumu | Kaynak |
|---|---|---|---|
| Ağ yığını ağacı | Çekirdeğin ağ alt sistemi dokümantasyonu genel indeksi | ses yığını değildir; yalnız sınır işaretlenir | https://docs.kernel.org/networking/index.html |
| Ses yığını | ağ ağacında değildir (ayrı dal) | bu dosya ağ hakkında iddia üretmez | §8 kapsam sıfırı |
| İlişki | Ağ/API ilişkisi CoreMusic web katmanını etkiler | web katmanı bu dalın konusu değil (§8) | §8 |

---

## §19 Repo Disk Kanıtları — Linux CI Koşucusu ve Test Zinciri

Bu bölüm, CoreMusic repodunun Linux'a gerçek bağımlılığını satır düzeyinde kanıtlar.
Referanslar 2026-10-12'de `Get-Content` ile okunmuştur; numaralar uydurma değildir.

| Kanıt | Dosya / Satır | İçerik | Linux bağlantısı |
|---|---|---|---|
| CI koşucusu | Dosya: .github\workflows\ci.yml / Satır: 27 → `runs-on: ubuntu-latest` | işler Linux üzerinde koşar | Linux hedefi CI gerçeğidir, tasarım iddiası değil |
| Zaman aşımı | Dosya: .github\workflows\ci.yml / Satır: 28 → `timeout-minutes: 15` | iş başına sınır | Linux koşucu ortamının operasyonel kuralı |
| İzin yüzeyi | Dosya: .github\workflows\ci.yml / Satır: 14 → `permissions:` | iş izinleri beyanı | CI güvenlik disiplini (§12.1 ile birlikte okunur) |
| Eşzamanlılık | Dosya: .github\workflows\ci.yml / Satır: 17 → `concurrency:` | çakışan çalıştırma kuralı | aynı |
| Tarama işi | Dosya: .github\workflows\secret-scan.yml / Satır: 20 → `name: GitLeaks` | sızıntı taraması | Linux koşucusunda çalışan güvenlik kapısı |
| Test aracı | Dosya: package.json / Satır: 11 → `"@playwright/test": "^1.63.0",` | E2E test bağımlılığı | Node+Linux test zinciri |
| PHP hedefi | Dosya: shared\composer.json / Satır: 8 → `"php": ">=8.4",` | çalışma zamanı eşiği | Linux dağıtımı bu sürümü sağlamalı (§5) |

### §19.1 Kanıt → karar eşlemesi

| Kanıt kümesi | Desteklediği karar | Dayanak |
|---|---|---|
| ci.yml L27 · L28 | Linux CI gerçeği | §19 tablosu |
| package.json L11 | Node test zinciri Linux'ta koşar | §19 tablosu |
| composer.json L8 | PHP sürüm eşiği distro seçimini kısıtlar | §5.1 (Q4 ile ilişkili) |

---

## §20 Web Araştırma Tablosu (bu dosyanın doğrulanan iddiaları)

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Çekirdek geliştirme süreci HOWTO yayımlanmıştır | https://docs.kernel.org/process/howto.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| stable hattı kuralları ayrı belgededir | https://docs.kernel.org/process/stable-kernel-rules.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| Çekirdek API yüzeyi ayrı belgede toplanır | https://docs.kernel.org/core-api/kernel-api.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| Sembol ad alanları belgesi yayımlanmıştır | https://docs.kernel.org/core-api/symbol-namespaces.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| Sürücü altyapısı belgesi yayımlanmıştır | https://docs.kernel.org/driver-api/infrastructure.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| staging alanı ayrı indekste tutulur | https://docs.kernel.org/staging/index.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| KASAN belgesi yayımlanmıştır | https://docs.kernel.org/dev-tools/kasan.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| dev-tools indeksi yayımlanmıştır | https://docs.kernel.org/dev-tools/index.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| ağ yığını indeksi yayımlanmıştır | https://docs.kernel.org/networking/index.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |
| RCU belgesi yayımlanmıştır | https://docs.kernel.org/RCU/index.html | 2026-10-12 | ✅ doğrulandı (HTTP 200) |

---

## İlişkili dosyalar

- `linux-kernel.md` — monolitik+modüler çekirdek, EEVDF, PREEMPT_RT, kesme modeli
- `linux-api.md` — POSIX/Linux syscall envanteri, glibc/musl, /dev·sysfs, namespace/cgroup
- `linux-framework.md` — systemd, D-Bus, X11/Wayland, toolchain, paket yöneticileri
- `linux-ses-yigini.md` — ALSA, PulseAudio, PipeWire, düşük gecikme, ASIO yokluğu
- `linux-dogrulama.md` — iddia·URL·2026-10-12·durum tablosu + Q listesi
- `../index.md` — üst düğüm kaydı (DEĞİŞTİRİLMEDİ — 2026-10-12)

---

**Authority:** Bayram Ali / Vault Steward · embedded-engineer subagent (K-Space V3 OS derin-dalı) · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0) · `git commit`: ATILMADI (orchestrator'a ait)
**Sonraki adım:** `linux-kernel.md` (çekirdek + PREEMPT_RT) — bu dosyayı okumadan ses yığını dosyasına geçilmez.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode