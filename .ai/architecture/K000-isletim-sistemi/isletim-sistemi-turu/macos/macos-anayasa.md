---
title: "macOS Anayasa — Darwin/XNU İlişkisi, Sürüm Ailesi, Mimari Katmanlar, CoreMusic Hedefi"
type: architecture-deepdive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/macos/macos-anayasa
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/macos
siblings: [macos-kernel, macos-api, macos-framework, macos-ses-yigini, macos-dogrulama]
evidence:
  - "https://en.wikipedia.org/wiki/MacOS_Golden_Gate (sürüm/Darwin/xnu doğrulama, 2026-10-12)"
  - "https://en.wikipedia.org/wiki/MacOS_Tahoe (sürüm/Darwin/xnu tablosu, 2026-10-12)"
  - "https://github.com/apple-oss-distributions/xnu (README + dosya ağacı, 2026-10-12)"
  - "https://www.apple.com/os/macos/ (macOS 27 resmi sayfası, 2026-10-12)"
  - "C:\\www\\coremusic.net\\.ai\\AGENTS.md §17 Edge #6 (ASIO→WASAPI, disk kanıtı)"
  - "C:\\www\\coremusic.net\\.ai\\.agents\\embedded-engineer.md §4.5 (macOS=CoreAudio, disk kanıtı)"
open_questions:
  - "macOS 15 Sequoia'nın Darwin ana sürümü bu turda doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "Golden Gate build öneki (26A) ile Darwin ana sürümü (27) arasındaki numaralandırma sapması Apple kaynaklı teyit bekliyor"
  - "CoreMusic macOS hedef minimum sürümü (min deployment target) henüz ADR'de sabitlenmemiş"
group: mimari
---

# macOS Anayasa (K000 · isletim-sistemi-turu · macos)

**ELI15:** macOS, Apple'ın Mac bilgisayarları için geliştirdiği işletim sistemi. İçinde
**Darwin** (açık kaynak çekirdek + sistem katmanı) ve onun içinde **XNU** (hibrit çekirdek)
çalışır; üstte framework'ler, onların üstünde uygulamalar yer alır. CoreMusic'in macOS
hedefi, ses tarafında **CoreAudio** backend'idir — macOS'ta ASIO **yoktur**.

---

## §1 Amaç ve Okuma Rehberi

Bu dosya K-Space V3 "OS türü teknoloji derin-dal"sının macOS ayağıdır ve tek başına
anayasa belgesi olarak çalışır: macOS nedir, Darwin ile ilişkisi nedir, sürüm ailesi
nasıldır, mimari katmanlar nelerdir ve CoreMusic bu sisteme nereye bağlanır.

| # | Dosya | Soru |
|---|---|---|
| 1 | `macos-anayasa.md` (bu dosya) | macOS nedir, sürümler, katmanlar, CoreMusic hedefi |
| 2 | `macos-kernel.md` | XNU = Mach + BSD + IOKit; scheduling, QoS, IOKit |
| 3 | `macos-api.md` | POSIX/BSD syscall + Apple framework API'leri, sandbox |
| 4 | `macos-framework.md` | SwiftUI/AppKit, AudioUnit/AVAudioEngine, CLT, HomeBrew |
| 5 | `macos-ses-yigini.md` | CoreAudio HAL, düşük gecikme yolu, ASIO yerine ne |
| 6 | `macos-dogrulama.md` | iddia·URL·tarih·durum tablosu + açık sorular |

**Durum etiketi kuralı (§9):** Bu dosya bir **şartnamedir** → `IMPLEMENTED (belge)`;
içinde geçen hiçbir C++/JUCE kodu repo'da yok → `⚠️ PLANNED (cpp=0)`.

### 1.1 Bu dalın okuma sırası (vault Reading Order ile uyumlu)

| Sıra | Dosya | Ne zaman okunur |
|---|---|---|
| P0 | `macos-anayasa.md` | macOS'a ilk girişte (her zaman) |
| P1 | `macos-kernel.md` | Zamanlama/IOKit/sürücü sorusunda |
| P1 | `macos-ses-yigini.md` | Ses backend'i, gecikme, exclusive sorusunda |
| P2 | `macos-api.md` · `macos-framework.md` | API/envanter/araç zinciri sorusunda |
| P2 | `macos-dogrulama.md` | Her iddia kontrolünde (tur-4 kapsamı) |

---

## §2 macOS Nedir?

| Öğe | Değer | Kaynak |
|---|---|---|
| Tanım | Apple masaüstü işletim sistemi; Unix benzeri aile | Wikipedia *MacOS Golden Gate*: "OS family: Mac, Unix (based on Darwin (BSD))" |
| Geliştirici | Apple Inc. | aynı kaynak |
| Kaynak modeli | Kapalı + açık kaynak bileşenler ("Closed, with open source components") | Wikipedia *MacOS Golden Gate* infobox |
| Lisans (açık bileşen) | Apple Public Source License (APSL) — Wikipedia: "proprietary software with open-source components and content licensed with APSL" | Wikipedia *MacOS Golden Gate* |
| Çekirdek | Hibrit **XNU** | Wikipedia *MacOS Golden Gate* infobox: "Kernel type: Hybrid (XNU)" |
| Unix sertifikası geçmişi | OS X 10.5 Leopard, yalnız i386 yapılandırması POSIX/UNIX teyidi aldı (OpenGroup markası) | CCC 2007 "Inside the Mac OS X Kernel" sunumu |
| İlk sürüm | Mac OS X 10.0 Cheetah (2001) — Public Beta 2000 | Wikipedia macOS sürüm şablonu |
| Ad zinciri | Mac OS X (10.0–10.7) → OS X (10.8–10.11) → macOS (10.12–15) → yıl tabanlı (26+) | Setapp "All macOS versions" makalesi |

**Ad düzeltmesi (görev varsayımına karşı):** Görevde geçen "macOS 15/16" ifadesinin
16 ayağı **yanlıştır** — Apple 15 Sequoia'dan sonra 16'ya **atlamamış**, 2025'te tüm
sistemleri yıl tabanlı numaralandırmaya taşımıştır: **macOS 15 → macOS 26 → macOS 27**.
(Doğrulama: Setapp "Instead of going from macOS 15 Sequoia to macOS 16, Apple now
numbers its operating systems by the year after launch"; Wikipedia *macOS Tahoe* dipnotu:
"Apple advanced the version number to 26, succeeding macOS Sequoia (version 15), as part
of a shift to a year-based versioning convention".)

### 2.2 macOS'un OS Türü Dalındaki Konumu (Windows/Linux kardeşi dal ile çapraz)

> Bu tablo yalnızca **çapraz konumlandırma** içindir; `windows\` ve `linux\` klasörlerine
> bu dosyadan yazılmaz (görev kısıtı: kardeş dizinlere DOKUNMA).

| Özellik | macOS (27 Golden Gate) | Windows (kardeş dal, özet) | Linux (kardeş dal, özet) |
|---|---|---|---|
| Çekirdek | XNU (Mach + BSD + IOKit hibrit) | NT çekirdeği (referans: yok — kardeş dal) | Monolitik Linux çekirdeği (referans: yok) |
| Unix kökü | Evet (BSD soyu, POSIX) | Hayır (POSIX katmanı emüle) | Evet (POSIX) |
| Ses "pro" API'si | CoreAudio (dahili) | ASIO (Steinberg) + WASAPI | ALSA + PipeWire/JACK |
| Ses exclusive kavramı | Hog mode (`kAudioDevicePropertyHogMode`) | WASAPI exclusive / ASIO exclusive | PipeWire/JACK paylaşım politikası |
| Kullanıcı arayüzü çatısı | AppKit/SwiftUI (Quartz üstü) | Win32/WinUI | Wayland/X11 (masaüstü göreli) |
| Paket yöneticisi | HomeBrew (üçüncü taraf; CLT gerektirir) | — (kardeş dal) | distro paketçisi |
| Dağıtım imzası | Gatekeeper + notarization | Authenticode | paket imzası |
| CoreMusic ses backend'i | CoreAudio | ASIO + WASAPI | ALSA/PipeWire (PLANNED, §4.5) |

Çekirdek satırlarının üçü de **kardeş dosyalarda** teyit edilmelidir; buradaki Windows/
Linux sütunları yalnızca okuyucuya yön verir ve `⚠️ REFERANS` niteliğindedir.

### 2.1 macOS'un Ne Osmanlı Devraldığı (tarihsel seri)

| Seri | Dönem | Adlandırma | Son temsilci |
|---|---|---|---|
| Mac OS X | 2001–2011 | 10.0–10.7 (Cheetah…Lion) | 10.7 Lion |
| OS X | 2012–2015 | 10.8–10.11 (Mountain Lion…El Capitan) | 10.11 El Capitan |
| macOS | 2016–2024 | 10.12–10.15, 11–15 (Sierra…Sequoia) | 15 Sequoia |
| macOS (yıl) | 2025– | 26 Tahoe, 27 Golden Gate | 27 Golden Gate (2026-09-14) |

Devralınan çekirdek soyu: **Mach (CMU) + 4.4BSD** → NeXTSTEP kernel'i → Rhapsody →
OS X (XNU). Wikipedia XNU maddesi: XNU "originally developed by NeXT for NeXTSTEP",
"hybrid kernel derived from version 2.5 of the Mach kernel … incorporated the bulk of
the 4.3BSD kernel"; Apple, OSF MK 7.3 kodunu lisanslayıp FreeBSD parçalarıyla
güncellemiştir (XNU Wikipedia + CCC 2007 sunumu).

---

## §3 Darwin ↔ XNU İlişkisi

```text
┌─────────────────────────────────────────────────────────────────┐
│ macOS 27 Golden Gate (kapalı kaynak kullanıcı katmanı)          │
│  Finder · Safari · SwiftUI/AppKit · CoreAudio · AVFoundation    │
├─────────────────────────────────────────────────────────────────┤
│ Darwin (açık kaynak çekirdek + userland çekirdeği)              │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │ XNU = "X is Not Unix" — hibrit çekirdek                  │  │
│  │  ┌─────────────┐ ┌─────────────┐ ┌─────────────────────┐  │  │
│  │  │ osfmk (Mach)│ │ bsd (BSD)   │ │ iokit (IOKit)       │  │  │
│  │  └─────────────┘ └─────────────┘ └─────────────────────┘  │  │
│  └───────────────────────────────────────────────────────────┘  │
│  libSystem (libc + libdispatch + libobjc + …)                   │
│  açık kaynak araçlar (bash, perl, zsh … Apple yamalı sürümleri) │
└─────────────────────────────────────────────────────────────────┘
```

| Kavram | Tanım | Kanıt |
|---|---|---|
| **Darwin** | macOS'u oluşturan açık kaynak çekirdek+alt sistemler bütünü; macOS bunun üstüne kapalı GUI/framework ekler | Wikipedia Golden Gate: "based on Darwin (BSD)" |
| **XNU** | Darwin'ın kernel'i; "X is Not Unix" akronimi | GitHub README: "XNU is an acronym for X is Not Unix" |
| **Hibritlik** | Mach mikrokernel parçaları tek adres uzayında BSD + IOKit ile linklenmiş → mikrokernel değil, monolitik-ağırlıklı hibrit | GitHub README + Apple Kernel Programming Guide |
| **Açık kaynak akışı** | Her ana sürüm sonrası Apple kaynak kodu yayınlar → github.com/apple-oss-distributions | https://opensource.apple.com/releases |
| **Eski ayna** | `apple/darwin-xnu` artık "Legacy mirror … Replaced by apple-oss-distributions/xnu" | github.com/apple/darwin-xnu README |
| **Dil** | Mach ve BSD C; IOKit kısımlı C++ (Embedded C++: exception yok, template yok, RTTI sınırlı) | Wikipedia XNU + IOKit Overview |

**İlişki özü:** Darwin = XNU + user-space çekirdek kütüphaneleri; macOS = Darwin + kapalı
kaynak katman (GUI, CoreAudio'nun bir kısmı, kapalı framework'ler). Apple açık kaynak
yayar ama **sürücülerin çoğu ve GUI kapalıdır** (OSnews 2025-04-07 deep-dive: "they did
not include most of the drivers for Apple hardware or third-party hardware").

---

## §4 Sürüm Ailesi (doğrulanmış zaman çizelgesi)

### 4.1 Ana sürüm serisi (2001 → 2026)

| Sürüm | Ad | Çıkış | Not |
|---|---|---|---|
| 10.0 | Cheetah | 2001 | ilk Mac OS X |
| 10.1 … 10.4 | Puma…Tiger | 2001–2005 | PowerPC dönemi |
| 10.5 | Leopard | 2007 | UNIX/POSIX teyidi (i386) |
| 10.6 | Snow Leopard | 2009 | Intel odaklı |
| 10.7 | Lion | 2011 | Mac App Store |
| 10.8–10.11 | Mountain Lion…El Capitan | 2012–2015 | "OS X" adı |
| 10.12–10.15 | Sierra…Catalina | 2016–2019 | "macOS" adı |
| 11 | Big Sur | 2020 | Apple silicon (M1) başlangıcı, 10.x→11 atlaması |
| 12 | Monterey | 2021/22 | **PHP userland'den kaldırıldı** |
| 13 | Ventura | 2022 | Stage Manager |
| 14 | Sonoma | 2023 | game mode |
| 15 | Sequoia | 2024 | yıl öncesi son sürüm; 15.8 güvenlik güncellemesi hâlâ yayınlanıyor (2026-09) |
| **26** | **Tahoe** | **2025-09-15** | yıl tabanlı numaraya geçiş; **Intel Mac'ler için son sürüm** |
| **27** | **Golden Gate** | **2026-09-14** | **yalnız Apple silicon**; Rosetta 2'nin tam işlevli olduğu son sürüm |

Kaynaklar: Wikipedia *MacOS Tahoe* sürüm tablosu · Wikipedia *MacOS Golden Gate* sürüm
tablosu · Macworld "list of all macOS versions" · Setapp "full list of all macOS versions".

### 4.2 Sürüm ↔ Darwin ↔ xnu eşlemesi (doğrudan doğrulanan satırlar)

| macOS | Build | Tarih | Darwin | xnu | Kaynak |
|---|---|---|---|---|---|
| 26.0 Tahoe | 25A354 | 2025-09-15 | 25.0.0 | xnu-12377.1.9~3 | Wikipedia Tahoe |
| 26.0.1 | 25A362 | 2025-09-29 | 25.0.0 | xnu-12377.1.9~141 | Wikipedia Tahoe |
| 26.1 | 25B78 | 2025-11-03 | 25.1.0 | xnu-12377.41.6~2 | Wikipedia Tahoe |
| 26.2 | 25C56 | 2025-12-12 | 25.2.0 | xnu-12377.61.12~1 | Wikipedia Tahoe |
| 26.3 | 25D125 | 2026-02-11 | 25.3.0 | xnu-12377.81.4~5 | Wikipedia Tahoe |
| 26.4 | 25E246 | 2026-03-24 | 25.4.0 | xnu-12377.101.15~1 | Wikipedia Tahoe |
| 26.5 | 25F71 | 2026-05-11 | 25.5.0 | xnu-12377.121.6~2 | Wikipedia Tahoe |
| 26.6 | 25G72 | 2026-07-27 | 25.6.0 | xnu-12377.161.13~4 | Wikipedia Tahoe |
| 26.7 | 25G229 | 2026-09-14 | 25.6.0 | xnu-12377.161.15.700.19~2 | Wikipedia Tahoe |
| 26.7.1 (son) | 25G241 | 2026-09-28 | — (25.x) | — | Wikipedia Tahoe |
| 27.0 Golden Gate | 26A428 | 2026-09-14 | 27.0.0 | xnu-13432.1.9~1 | Wikipedia Golden Gate |
| 27.0.1 (son) | 26A434 | 2026-09-28 | — (27.x) | — | Wikipedia Golden Gate |
| 27.2 beta 3 | 26B5101f | 2026-10-05 | 27.2.0 | xnu-13432.40.177.0.3~56 | Wikipedia Golden Gate |

**⚠️ Doğrulanmayan:** macOS 15 Sequoia'nın Darwin ana sürümü (arşivde "Darwin 24" olarak
bilinir) bu turda kaynakla teyit edilmedi → `⚠️ VERIFICATION REQUIRED` (bkz. Q1,
`macos-dogrulama.md`).

### 4.3 Donanım kapsamı (ses hedefi için kritik)

| Sürüm | Desteklenen Mac | Ses açısından anlamı |
|---|---|---|
| 26 Tahoe | Apple silicon + 4 Intel model (2019 16" MBP, 2020 4×TB3 13" MBP, 2020 27" iMac, 2019 Mac Pro) | Intel+ARM evresi; Universal Binary zorunlu değil |
| 27 Golden Gate | **Yalnız Apple silicon** (M1+, A18 Pro tabanlı MacBook Neo dahil) | x86_64 binary'leri yalnızca Rosetta 2 altında; gerçek-zamanlı ses için **native arm64 build şart** |
| 27'de kaldırılanlar | Boot Camp, AFP, AirPort Utility (önyüklü) | Boot Camp ile Windows çift-boot üzerinden ASIO'ya erişim **kalmadı** |

Kaynak: Wikipedia *MacOS Golden Gate* §Supported hardware / §Removed features ·
Ars Technica 2026-06-08 "macOS 27 requires Apple Silicon".

---

## §5 Sürüm Adlandırma ve Paketleme Kuralları

| Kural | Gözlem | Kanıt |
|---|---|---|
| Yıl tabanlı numara | 15 → **26** atlaması; numara "lansman sonrasındaki yıl"ı ifade eder (2025 sonu → 26) | Setapp + Wikipedia Tahoe dipnotu |
| Ad her yıl değişir | 26 Tahoe (Lake Tahoe), 27 Golden Gate (Golden Gate Köprüsü) — California yer adları geleneği sürüyor | Wikipedia Tahoe (Federighi açıklaması) + Wikipedia Golden Gate |
| Build öneki ≠ Darwin ana sürümü | Tahoe: build `25A…` = Darwin 25 · Golden Gate: build `26A…` ama Darwin **27.0.0** | Wikipedia iki sürüm tablosu (§4.2) |
| xnu sürüm formatı | `xnu-<major>.<minor>.<patch>~<build>` — major atlaması ana sürümle birlikte (12377 → 13432) | Wikipedia release tabloları |
| İç kod adı | Golden Gate iç kod adı daha önce Big Sur için de kullanılmış ("GoldenGate" without a space) | Wikipedia Golden Gate §Development |
| Beta kanalı | WWDC (Haziran ilk haftası) → developer beta, ~3 ay sonra final (14 Eylül 2026) | Apple Newsroom WWDC 2026 + MacRors 2026-09-14 |

---

### 4.4 Destek pencereleri ve dağıtım gerçekleri (2026-10-12 anlık görüntüsü)

| Sürüm | Yayın durumu (12.10.2026) | Destek bandı | Not |
|---|---|---|---|
| 15 Sequoia | Eski | Güvenlik güncellemesi sürüyor (15.8, 2026-09-14 ile birlikte yayınlandı) | Son Intel+ARM birleşik sürümün güvenlik kolu |
| 26 Tahoe | Bir önceki ana | 26.7.1 (2026-09-28) | Intel Mac'lerin **son** sürümü |
| 27 Golden Gate | Güncel | 27.0.1 (2026-09-28) · 27.2 beta 3 (2026-10-05) | Yalnız Apple silicon |
| 14 Sonoma | Desteği kesildi | Eylül 2026'da güvenlik güncellemesi yayınlanmadı | Setapp: "didn't release an update for macOS 14 Sonoma" |

**Seçim sonucu (CoreMusic hedefi için argüman):** Neva Engine için minimum macOS
**15** veya **26** önerilir; **27-only** yapmak Apple silicon zorunluluğu nedeniyle
Intel kullanıcıları tamamen dışlar, **14 ve altı** HomeBrew/CLT araç zinciriyle
uyumsuzlaşabilir (HomeBrew: "macOS Sequoia (15) (or higher) … best and supported;
macOS 11 – 14 are unsupported but may work"). Kesin karar ADR'dedir → Q2.

---

## §6 Mimari Katmanlar

### 6.1 Katman diyagramı (yukarıdan aşağıya)

```text
┌──────────────────────────────────────────────────────────────────────┐
│ L6  Uygulama (Application)                                           │
│     CoreMusic web panelleri (PHP/JS) · Neva Engine (C++/JUCE, PLANNED)│
├──────────────────────────────────────────────────────────────────────┤
│ L5  Apple Framework'leri                                             │
│     SwiftUI/AppKit · AVFoundation/AVFAudio · CoreAudio · AudioToolbox│
│     CoreFoundation/Foundation · Security(App Sandbox) · Metal        │
├──────────────────────────────────────────────────────────────────────┤
│ L4  Çalışma zamanı (Runtime)                                         │
│     libSystem (libc, libdispatch, libobjc) · Swift runtime (ABI sabit)│
│     dyld (binary loader) · Foundation/ObjC message dispatch          │
├──────────────────────────────────────────────────────────────────────┤
│ L3  POSIX/BSD syscall yüzeyi + Mach syscall yüzeyi                   │
│     (pozitif syscall no → BSD · negatif → Mach; tek giriş noktası)  │
├──────────────────────────────────────────────────────────────────────┤
│ L2  XNU çekirdeği                                                    │
│     ┌──────────────┐ ┌──────────────────┐ ┌────────────────────┐     │
│     │ osfmk (Mach) │ │ bsd (POSIX, VFS) │ │ iokit + libkern    │     │
│     └──────────────┘ └──────────────────┘ └────────────────────┘     │
├──────────────────────────────────────────────────────────────────────┤
│ L1  Sürücü katmanı                                                   │
│     KEXT (klasik) / DriverKit (user-space) / Audio Server Plug-in    │
├──────────────────────────────────────────────────────────────────────┤
│ L0  Donanım (Apple silicon SoC · USB/Thunderbolt ses cihazları)      │
└──────────────────────────────────────────────────────────────────────┘
```

### 6.2 Katman tablosu

| Katman | İçerik | CoreMusic teması | Durum etiketi |
|---|---|---|---|
| L6 Uygulama | Safari, Finder; CoreMusic panelleri (PHP 8.4 + vanilla JS) | Web UI, downloader | PHP backend IMPLEMENTED · macOS paketleme PLANNED |
| L5 Framework | CoreAudio, AudioToolbox, AVFAudio, AppKit, SwiftUI | Ses I/O, codec, dosya | Şartname IMPLEMENTED · kod ⚠️ PLANNED |
| L4 Runtime | libSystem, GCD/dispatch, ObjC/Swift runtime | JUCE runtime bağımlılığı | Kod yok (cpp=0) |
| L3 Syscall | POSIX (`open/read/write/mmap/pthread`) + Mach (`mach_msg`, `thread_policy_set`) | Gerçek-zamanlı öncelik ayarı | Kullanım PLANNED |
| L2 XNU | Mach + BSD + IOKit | Zamanlama, IPC, sürücü nesneleri | Çekirdek dış kaynaklı (Apple) |
| L1 Sürücü | Audio Server Plug-in (user-space), DriverKit | Harici DAC sürücüsü (varsa) | Apple/üçüncü taraf |
| L0 Donanım | Apple silicon (M1+), gömülü DAC, USB audio class cihazlar | PCM3168A/AK4458 sınıfı DAC (donanım dalında) | Donanım dalı K000 dışı |

### 6.3 Katmanlar arası kural

1. **Yukarıdan aşağı çağrı:** uygulama → framework → syscall → çekirdek; çekirdekten
   uygulamaya dönüş **callback/IOProc** ile olur (ses: `AudioDeviceIOProc`).
2. **Aynı katmanda çapraz:** framework'ler birbirini çağırabilir (AVFoundation →
   AudioToolbox → CoreAudio), ama uygulama framework'süz syscall çağırmak zorunda değil
   (imkân var, nadir gerekir).
3. **Ses veri yolu** L5→L4→L3→L2→L1→L0 üzerinden tek yönde iner; ayrıntı
   `macos-ses-yigini.md` §4'tedir.

### 6.4 Örnek çağrı akışları (katmanlar arası)

**Akış A — dosya okuma (basit syscall yolu):**

```text
Uygulama (L6)          fopen/fread (libc, L4)      syscall open/read (L3)     XNU bsd/ (L2)
     │                        │                            │                        │
     └──── "kullanıcı kütüphanesi" ────────────────────────►│  pozitif syscall no    │
                                                            └───────────────────────►│
                                                                      vnode/VFS ─────► APFS sürücüsü
```

**Akış B — gerçek-zamanlı ses I/O (callback yolu):**

```text
CoreAudio framework (L5) ── AudioDeviceIOProc kaydı ──► HAL (L5/L3 sınırı)
                                                            │
XNU IOKit Audio family (L2/L1) ── ring buffer zaman damgası ─┘
                                                            │
Donanım I/O motoru her tam turda ──► IOProc callback ──► uygulama render fonksiyonu (L6)
```

**Akış C — iş parçacığı öncelik yükseltme (gerçek-zamanlı ses için):**

```text
Uygulama thread'i ── thread_policy_set(THREAD_TIME_CONSTRAINT_POLICY) ──► Mach scheduler (osfmk)
                        │ (pthreads ile yalnız 0-63 bandı; RT için Mach API gerekir)
                        └─ Apple KPG: "This is commonly used when interacting with CoreAudio."
```

### 6.5 Katman sertifikasyon matrisi (kim neyi değiştirir)

| Katman | Apple | Üçüncü taraf | CoreMusic |
|---|---|---|---|
| L0 Donanım | Apple silikon + sertifikalı aksesuar | USB audio class cihazlar | Donanım dalı (K000 dışı) |
| L1 Sürücü | Apple Audio Server Plug-in'ler | AudioServerPlug-in / DriverKit | **Yok** (kullanıcı uzayında bile sürücü yazımı yok) |
| L2 Çekirdek | XNU (açık kaynak, APSL) | KEXT yazımı pratikte kapalı (notaryzasyon+SIP) | **Yok** (ROLE yasağı: firmware güvenlik → DUR) |
| L3/L4 | syscall + libSystem | — | Kullanıcı (dolaylı) |
| L5 Framework | CoreAudio/AudioToolbox/AVFAudio | JUCE (framework sarmalayıcı) | **Evet — hedef katman** |
| L6 Uygulama | Finder/Safari | Neva Engine (.app), paneller | **Evet — hedef katman** |

---

### 4.5 Sürüm serisinden çıkarılan gözlem desenleri (analiz — kanıt §4.2 tablosu)

| # | Desen | Kanıt özeti | CoreMusic etkisi |
|---|---|---|---|
| D1 | xnu major sürümü macOS major'ı ile **gitmez** (Tahoe=25, Golden Gate=27, build'ler 25A→26A) | §4.2 sütun karşılaştırması | CI'da `uname -r` ile sürüm kontrolü deseni yazılırken build ile Darwin ayrı okunmalı |
| D2 | Patch sürüm Darwin minor'unu taşır (26.6 → Darwin 25.6.0) | Wikipedia Tahoe satırları | Sürüm-kıyas testleri Darwin'a bağlanmalı |
| D3 | Beta → final ~3 ay sürer (WWDC 8 Haziran → final 14 Eylül) | Wikipedia Golden Gate §Development | Destek matrisi yılın 3. çeyreğinde güncellenmeli |
| D4 | Her yıl donanım penceresi daralır (27 = Intel yok) | §4.3 tablosu | Universal Binary stratejisi yalnız 26'ya kadar geçerli |
| D5 | Eski sürüm kolu kısa kesilir (14 Sonoma Ağustos 2026'dan sonra güncellemesiz) | Setapp FAQ | Min hedef 15 altına düşmemeli (Q2) |
| D6 | Dahili kod adı tekrar kullanılır (GoldenGate = Big Sur eski kod adı) | Wikipedia Golden Gate §Development | Kod adı ile sürüm numarası eşleştirmesi yapılmamalı |

---

## §7 Uygulama ve Çalışma Zamanı Modeli

| Öğe | macOS gerçeği | Kaynak/kanıt |
|---|---|---|
| Binary formatı | Mach-O (universal binary = birden fazla mimari dilimi) | Wikipedia Golden Gate şablonu "Mach-O"; Universal binary maddesi |
| Yükleyici | `dyld` (dynamic linker) | Apple dokümantasyonu (bu turda sayfa çekilmedi → ⚠️ temel bilgi) |
| Ortak kütüphane | `libSystem` = libc + libdispatch + libobjc + …; uygulamalar syscall'a doğrudan değil libSystem üzerinden çıkar | CCC 2007 sunumu: "applications must call through libSystem, which is the equivalent of libc" |
| Süreç modeli | BSD `fork/exec` süreçler, Mach task/thread altında | Kernel Programming Guide (Mach task = resource owner) |
| Aynı birden çok süreç | App sandbox + hardened runtime dağıtımı | developer.apple.com/documentation/security/app-sandbox |
| Unix ad alanı | 10.5'ten beri POSIX uyumu teyitli (i386 yapılandırması) | CCC 2007 sunumu |
| GUI | Quartz/Core Animation üstünde AppKit (klasik) veya SwiftUI | Wikipedia Golden Gate teknoloji listesi |

### 7.2 Güvenlik ve dağıtım mekanizmaları (katmanlar üstü)

| Mekanizma | Ne yapar | Kanıt |
|---|---|---|
| System Integrity Protection (SIP) | Çekirdek/sistem dosyalarına yetkisiz yazmayı engeller | Wikipedia Golden Gate teknoloji listesi (maddenin adı) |
| Gatekeeper | İmzasız/imsizsiz uygulamayı çalıştırmayı kullanıcı onayına bağlar | Wikipedia Golden Gate teknoloji listesi |
| Notarization | Apple'a gönderilen yapının taranıp onaylanması | Xcode CLT dokümanı: `notarytool` CLT/Xcode araçları arasında listelenir |
| Hardened Runtime | Çalışma anı saldırı yüzeyini kısıtlar; entitlement'ları (ör. audio-input) açmak için ön koşul | entitlement JSON: "first enable the Hardened Runtime capability in Xcode" |
| App Sandbox | Kaynak erişimini entitlement listesiyle kısıtlar; Mac App Store dağıtımı için **zorunlu** | app-sandbox JSON: "To distribute a macOS app through the Mac App Store, you must enable the App Sandbox capability." |
| KEXT kısıtları | Çekirdek uzantısı yazımı imza/zorunlu imzalama ile sınırlandırılmış (detay → `macos-kernel.md` §6) | XNU `security/` ağacı + AMFI (libkern/amfi) — README ağacı kanıtı |

### 7.3 Bellek modeli notları (Mach soyu)

| Kavram | macOS karşılığı | Kaynak |
|---|---|---|
| Adres uzayı | Mach **task** = sanal adres uzayı + port hakları + 0-n thread | Apple Kernel Programming Guide (Mach) |
| Bellek nesnesi | Named entries/regions, kalıcı depoya bağlı sayfalar | aynı |
| EMMI | "external memory management interface" — portless varyantı OS X 10.1'den beri | Apple Kernel Programming Guide |
| Paylaşımlı bellek | Mach portları üzerinden paylaşım (ses I/O ring buffer'ları da paylaşımlı haritalanabilir → `macos-ses-yigini.md` §5) | Mach IPC bölümü |
| Sayfa değiştirme | default_pager + VM alt sistemi (`osfmk/vm`, `osfmk/default_pager`) | GitHub xnu ağacı listesi |

---

## §8 CoreMusic / macOS Hedefi

### 8.1 Çekirdek karar: macOS ses backend = CoreAudio

| İddia | Durum | Kanıt |
|---|---|---|
| Windows: ASIO (pro) + WASAPI (paylaşımlı) | ⚠️ PLANNED (repo'da kod yok) | `.ai/.agents/embedded-engineer.md` §4.5 satır "Windows \| ASIO (pro) + WASAPI … ⚠️ PLANNED" |
| **macOS: CoreAudio** | ⚠️ PLANNED (karar şartnamede, kod yok) | `.ai/.agents/embedded-engineer.md` §4.5 satır "macOS \| CoreAudio \| kök §25 stack satırı \| ⚠️ PLANNED" |
| ASIO Windows'a özgü fallback zinciri (ASIO→WASAPI) | IMPLEMENTED (belge) | `.ai/AGENTS.md` §17 Edge #6: "ASIO device loss → WASAPI fallback" |
| macOS'ta ASIO fallback'i **gerekmez** | Türetilmiş karar (bu belge) | ASIO yalnız Windows sürücü modelidir; macOS'ta karşılığı doğrudan CoreAudio'dur (§8.2) |
| JUCE sürümü | `⚠️ VERIFICATION REQUIRED` ("JUCE 9" iddiası kanıtsız) | `.ai/.agents/embedded-engineer.md` §4.5 "Çapraz \| JUCE … ⚠️ PLANNED (9 sürüm kanıtsız)" |
| Kod tabanı | `*.cpp`/`*.h` = 0, `projects/NevaEngine` = 0 | ROLE §1 (glob sayımı) → tüm C++ iddiaları `⚠️ PLANNED` |

### 8.2 Neden macOS'ta ASIO yerine CoreAudio?

1. **ASIO, Steinberg'in Windows sürücü API'sidir** — macOS çekirdeğinde (XNU/IOKit)
   karşılığı yoktur; macOS'ta ses donanımına erişim IOKit Audio family + CoreAudio HAL
   üzerinden yürür (Apple *Audio Device Driver Programming Guide*: "Immediately above the
   driver and the I/O Kit's Audio family … is the Audio Hardware Abstraction Layer").
2. **macOS'un kendi "pro" yolu zaten CoreAudio'dur:** Core Audio Overview — "Mac apps
   can be written to use these technologies directly when they require the highest
   possible, real-time performance."
3. **JUCE bu platformda CoreAudio backend'i kullanır** (dosya adı kanıtı:
   `juce_mac_CoreAudio.cpp`, JUCE forum başlıkları) → Neva Engine için JUCE seçildiyse
   macOS'ta ek sürücü katmanı gerekmez (`macos-ses-yigini.md` §6).
4. **Karşılıklı bağımlılık yok:** `.ai/AGENTS.md` §17/§10.1 ASIO→WASAPI zinciri
   **yalnız Windows** senaryosudur; macOS dalı bu zincirin dışındadır ve "ASIO cihaz
   kaybı" senaryosu macOS'ta **oluşmaz** (ASIO device hiç var olmaz).

### 8.3 macOS hedefinin CoreMusic katmanlarıyla kesişimi

| CoreMusic katmanı | macOS karşılığı | Durum |
|---|---|---|
| PHP 8.4 backend (`php >=8.4` composer.json) | macOS'ta PHP önyüklü **değil** (Monterey 12'de kaldırıldı) → HomeBrew `php` veya CLT dışı kurulum gerekir | Kanıt: composer.json × 4 + §3.4 below |
| Vanilla JS paneller | Safari/Chrome ile çalışır; CLT gerekmez | IMPLEMENTED (repo) |
| Neva Engine (C++20, JUCE) | arm64 native build + CoreAudio backend | ⚠️ PLANNED (cpp=0) |
| WASAPI/ASIO desktop | macOS'a taşınmaz; karşılığı CoreAudio | Şartname (bu belge §8.2) |
| CI (GitHub Actions) | `macos-14/15/16` runner'ları ile derleme | PLANNED (`.github/workflows/` = 2 dosya, macOS job kanıtsız) |

### 8.4 ADR-019 (per-OS player) ile ilişki

ADR-019 "per-OS player" kararı, platform başına ses backend'i atanmasını öngörür
(kayıt: `.ai/.decisions/index.md` satırı — tam metin `⚠️ VERIFICATION REQUIRED`; ROLE
kuralı: frozen ADR düzenlenmez, yalnız referans verilir). Bu belge'nin ataması:

| OS | Backend | Fallback zinciri | Kaynak |
|---|---|---|---|
| Windows | ASIO (birincil) | ASIO → WASAPI (`§17 Edge #6`) | `.ai/AGENTS.md` §17 |
| **macOS** | **CoreAudio (tekil)** | **Fallback gerekmez — ASIO hiç var olmaz** | bu belge §8.2 |
| Linux | ALSA/PipeWire | `⚠️ PLANNED` (kök §4.5 listesi) | embedded-engineer §4.5 |

**Kanıt disiplini:** Yukarıdaki satırların tamamı **belge-seviyesidir**; repo'da
`grep -RIlE "ASIO|CoreAudio|ALSA|PipeWire|WASAPI" projects/` → **0 sonuç** (ROLE
§4.4 kanıtı). Bu yüzden macOS backend satırı da `⚠️ PLANNED` etiketini taşır.

### 8.5 Bu belgeye özgü terimler

| Terim | İngilizce | Anlamı |
|---|---|---|
| Anayasa | constitution | Dalın ana referans belgesi; alt dosyalara otorite verir |
| Hibrit çekirdek | hybrid kernel | Mach + BSD + IOKit'in tek adres uzayında linklenmesi |
| Yıl tabanlı sürüm | year-based versioning | 15 → 26 atlaması; numara lansman sonrasındaki yıl |
| Ses backend'i | audio backend | OS'e özgü ses I/O katmanı (CoreAudio, WASAPI, ALSA) |
| Fallback zinciri | fallback chain | Bir backend başarısızsa diğerine geçiş (yalnız Windows) |
| Durum etiketi | status label | IMPLEMENTED (belge) · PLANNED (kod yok) · VERIFICATION REQUIRED |
| Kardeş dal | sibling dizin | `windows\`, `linux\` — bu görevde yazma yasağı |
| Zaman damgası | timestamp | HAL'ın ring buffer turunu tahmin ettiği mutlak zaman değeri |
| Entitlement | entitlement | Sandbox izin anahtarı → `macos-api.md` §5 |
| Zorunlu okuma | reading order | Vault P0→P1→P2→P3 sırası (kök §13) |

---

## §9 Kapsam Sınırı

| Bu dosya KAPSAR | Bu dosya KAPSAMAZ |
|---|---|
| macOS tanımı, Darwin/XNU ilişkisi | XNU kod incelemesi → `macos-kernel.md` |
| Sürüm ailesi + Darwin eşlemesi (26/27) | Sürücü/IOKit detayı → `macos-kernel.md` |
| Mimari katman diyagramı (L0–L6) | Syscall/entitlement listesi → `macos-api.md` |
| CoreMusic macOS backend kararı (CoreAudio) | Framework envanteri → `macos-framework.md` |
| Kapsam dışı: iOS/iPadOS/watchOS/tvOS/visionOS | Ses düşük-gecikme yolu → `macos-ses-yigini.md` |
| Kapsam dışı: donanım PCB/amber (k17/k18/k19) | Doğrulama tablosu → `macos-dogrulama.md` |

**Yasaklar (ROLE):** DSP algoritma kodu · Windows API implementasyonu · PCB tasarımı ·
test kodu · PHP/domain kodu · frozen ADR (001-037, 019 dahil) düzenleme · secret yazma.

---

## §10 Açık Sorular (Q listesi)

| # | Soru | Yönlendirme |
|---|---|---|
| Q1 | Sequoia (15) hangi Darwin sürümünü taşıyor? | ⚠️ teyit → `macos-dogrulama.md` |
| Q2 | CoreMusic macOS minimum hedefi: 14 Sonoma mı, 15 mi, 26 mı? (HomeBrew 15+ destekliyor) | Karar → **root** / ADR |
| Q3 | Golden Gate build `26A` ↔ Darwin `27` sapmasının resmi açıklaması var mı? | Apple release notes teyidi |
| Q4 | Neva Engine macOS dağıtımı: imzalı/notarized .app mi, CLI mı? | ADR gerekçe (Gatekeeper) |
| Q5 | JUCE sürümü (9 iddiası) hangi kaynakla sabitlenecek? | Guardrail #16: bağımlılık bump → **DUR + handover** |

---

## §11 Kaynakça (bu dosyada kullanılan)

1. https://en.wikipedia.org/wiki/MacOS_Golden_Gate — sürüm, Darwin 27, xnu-13432, donanım (2026-10-12)
2. https://en.wikipedia.org/wiki/MacOS_Tahoe — sürüm tablosu, Darwin 25, xnu-12377 (2026-10-12)
3. https://en.wikipedia.org/wiki/Xnu — hibrit çekirdek, NeXT/OSF MK 7.3, APSL (2026-10-12)
4. https://github.com/apple-oss-distributions/xnu — README + dosya ağacı (2026-10-12)
5. https://github.com/apple/darwin-xnu — "Legacy mirror" ifadesi (2026-10-12)
6. https://www.apple.com/os/macos/ — macOS 27 resmi sayfası (2026-10-12)
7. https://setapp.com/how-to/full-list-of-all-macos-versions — yıl tabanlı geçiş (2026-10-12)
8. https://www.macworld.com/article/672681/list-of-all-macos-versions-including-the-latest-macos.html — sürüm tarihleri (2026-10-12)
9. https://fahrplan.events.ccc.de/congress/2007/Fahrplan/attachments/986_inside_the_mac_osx_kernel.pdf — XNU mimarisi, syscall numaralandırması (2026-10-12)
10. https://www.osnews.com/story/142069/apples-darwin-os-and-xnu-kernel-deep-dive/ — açık kaynak kapsamı (2026-10-12)
11. https://arstechnica.com/gadgets/2026/06/macos-27-requires-apple-silicon… — yalnız Apple silicon (2026-10-12)
12. `.ai/AGENTS.md` §17 Edge #6 · `.ai/.agents/embedded-engineer.md` §4.5 — repo fallback/backend kanıtları (disk)

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** Şartname IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0, CMakeLists=0)
**İlişkili:** `../index.md` · `../isletim-sistemi-turu-kapsam.md` · `macos-kernel.md` · `macos-ses-yigini.md` · `macos-dogrulama.md`

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode