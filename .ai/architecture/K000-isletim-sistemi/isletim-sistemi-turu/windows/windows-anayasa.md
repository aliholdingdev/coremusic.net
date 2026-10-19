---
title: "Windows Anayasa — NT Çekirdek Ailesi, Sürüm Zaman Çizelgesi ve Mimari Katmanlar"
type: architecture-deepdive
node_id: K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-anayasa
status: draft
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# Windows Anayasa (windows-anayasa.md)

**ELI15:** Windows, Microsoft'un NT (New Technology) çekirdeği üzerine kurulu işletim
sistemidir. Bu dosya, Windows'un ne olduğunu, NT ailesinin 1993'ten bugüne sürüm
zaman çizelgesini, masaüstü/sunucu ürün ayrımını ve uygulamadan donanıma kadar uzanan
mimari katmanları tanımlar. CoreMusic (Neva Engine) için Windows, ASIO ve WASAPI
üzerinden ses düşük gecikmeli ses erişiminin sağladığı platformdur.

**Kapsam / sınır:** Bu dosya yalnızca **anayasa** (tanım · zaman çizelgesi · katmanlar ·
CoreMusic hedefi) düzeyindedir. Kernel/IRQL/driver ayrıntısı `windows-kernel.md`,
Win32 API envanteri `windows-api.md`, framework'ler `windows-framework.md`, ses
yığını `windows-ses-yigini.md`, kanıt tablosu `windows-dogrulama.md` içindedir.
**ASIO deep-dive bu dosyada DEĞİLDİR** — sınır maddesi §6'da tanımlıdır.

| Öğe | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-anayasa |
| parent | K000-isletim-sistemi/isletim-sistemi-turu/windows |
| Durum | draft |
| Kanıt standardı | Zero-hallucination — her teknik iddia `windows-dogrulama.md` tablosunda URL ile kayıtlı |
| Tarih | 2026-10-12 |

---

## §1 Windows Nedir?

**Tanım (kaynaklı):** Windows, Microsoft tarafından geliştirilen; uygulama programlarına
bir grafik kullanıcı arayüzü ve standart API'ler sağlayan; NT çekirdek ailesi üzerine
kurulu işletim sistemi ailesidir.

Temel gerçekler:

1. **NT çekirdeği:** Windows'un modern sürümüleri (Windows 10, Windows 11, Windows
   Server) NT mimarisinin ardıl sürümleridir; NT 3.1 (1993) ilk sürümdür.
2. **Çift mod (ring):** Windows uygulama kodunu **user mode** (ring 3), çekirdek ve
   sürücü kodunu **kernel mode** (ring 0) içinde çalıştırır. Microsoft, sürücü
   geliştirme dokümanlarında bu ayrımı "User Mode and Kernel Mode" sayfasında tanımlar
   (learn.microsoft.com/windows-hardware/drivers/gettingstarted/user-mode-and-kernel-mode).
3. **İşletim sistemi bileşenleri:** Microsoft docs "Overview of Windows Components"
   sayfasında NT çekirdeğinin parçalarını listeler: NTOSKRNL.EXE (kernel + executive),
   HAL (Hardware Abstraction Layer), SSDT (System Service Dispatch Table),
   Win32k.sys (GDI/pencere), CSRSS.exe, SMSS.exe, LSASS.exe, Winlogon.exe,
   Küsel Sürücü yöneticileri (kernel-mode), NTDLL.DLL (user-mode NT API stub).
4. **User mode / kernel mode ayrımı sürücü dokümanlarının temelidir:** Microsoft,
   hangi kodun hangi modda çalıştığını "user-mode-and-kernel-mode" sayfasında
   açıklar; sürücüler kernel mode'da, uygulamalar user mode'da çalışır.

**⚠️ Not (bileşen listesi):** NTOSKRNL/HAL/NTDLL/Win32k gibi bileşen adları
Microsoft docs sayfalarında geçmektedir; her bileşenin birebir görev tanımı için
`windows-kernel.md` §2'ye bakılır. Bu dosyada listeleme ile sınırlıdır.

### §1.1 Bu Deep-Dive Seti — Dosya Görevleri

| Dosya | Görev | Okuma sırası |
|---|---|---|
| `windows-anayasa.md` (bu dosya) | Tanım · zaman çizelgesi · katmanlar · CoreMusic hedefi | 1 |
| `windows-kernel.md` | User/kernel mode · executive · IRQL · WDM/KMDF/UMDF | 2 |
| `windows-api.md` | Win32 API envanteri · DLL'ler · COM/WinRT · API setleri | 3 |
| `windows-framework.md` | CRT/UCRT · MSVC/WDK · Media Foundation/XAudio2 · servisler · PHP/Node | 4 |
| `windows-ses-yigini.md` | Core Audio · shared/exclusive · AVRT/MMCSS · ASIO konumu | 5 |
| `windows-dogrulama.md` | İddia · URL · tarih · statü tablosu (≥30 satır) + Q-listesi | 6 (referans) |

---

## §2 NT Çekirdek Ailesi — Sürüm Zaman Çizelgesi

Aşağıdaki tablo, Windows NT çekirdek ailesinin bilinen ana sürümlerini ve NT build
numaralarını toplar. Kaynaklar: Microsoft sürüm geçmişi (learn.microsoft.com),
yaşam döngüsü sayfaları ve üçüncü taraf sürüm arşivi (gaijin.at — Windows NT
sürüm listesi; Wikipedia Windows NT sürüm listesi). **Üçüncü taraf kaynaklar
Microsoft docs değildir; bu nedenle ilgili satırlar doğrulama tablosunda
"3rd-party (corroboration)" statüsüyle işaretlenmiştir.**

### §2.1 Erken NT dönemi (1993–2001)

| Sürüm | NT Build | Çıkış yılı | Not |
|---|---|---|---|
| Windows NT 3.1 | 511 / 528 | 1993 | İlk NT sürümü |
| Windows NT 3.5 | 807 | 1994 | — |
| Windows NT 3.51 | 1057 | 1995 | — |
| Windows NT 4.0 | 1381 | 1996 | Shell/GDI user mode'dan kernel mode'a taşındı (NT 4.0 "GDI performans" kararı — ayrıntı ⚠️ VERIFICATION REQUIRED, bu dosyanın kapsamı dışı) |
| Windows 2000 (NT 5.0) | 2195 | 2000 | NT 5.0 |
| Windows XP (NT 5.1) | 2600 | 2001 | NT 5.1 |
| Windows Server 2003 (NT 5.2) | 3790 | 2003 | NT 5.2 |

> ⚠️ **VERIFICATION REQUIRED:** NT 4.0'ın GDI'ı kernel mode'a taşıma kararı bu
> dosyada yalnızca popüler bir anlatı olarak geçmektedir; Microsoft docs'tan tekil
> sayfa kanıtı bu görevde toplanmamıştır. İddia, doğrulama tablosunda "3rd-party"
> statüsüyle işaretlenmeden kesin sayılmaz.

### §2.2 NT 6.x dönemi (2006–2012)

| Sürüm | NT Build | Çıkış yılı | Not |
|---|---|---|---|
| Windows Vista (NT 6.0) | 6000/6001/6002 | 2006/SP1/SP2 | Core Audio API'leri (WASAPI, MMDevice) bu sürümde tanıtıldı |
| Windows 7 (NT 6.1) | 7600/7601 | 2009 | — |
| Windows 8 (NT 6.2) | 9200 | 2012 | — |
| Windows 8.1 (NT 6.3) | 9600 | 2013 | — |

> **CoreMusic bağlantısı:** Core Audio'nun (MMDevice + WASAPI) Vista'da
> tanıtıldığı iddiası Microsoft Core Audio dokümanlarından (Vista+ gerekçesi)
> doğrulanmıştır; ayrıntı `windows-ses-yigini.md` §3.

### §2.3 NT 10.0 dönemi (2015–bugün)

Microsoft, Windows 10 sürüm numarasını "6.4"ten "10.0"a çevirdi; karar,
Windows 10 tanıtıldığında Microsoft tarafından açıklanmıştır (Windows blog yazısı /
üçüncü taraf raporlar — ⚠️ bu görevde tekil Microsoft blog URL'si toplanmadı,
"3rd-party corroboration" statüsüyle işaretlenmiştir).

| Ürün | Sürüm | NT build | Not |
|---|---|---|---|
| Windows 10 | 1507 | 10240 | İlk Windows 10 sürümü |
| Windows 10 | 1511 | 10586 | — |
| Windows 10 | 1607 | 14393 | = Server 2016 build |
| Windows 10 | 1703 | 15063 | — |
| Windows 10 | 1709 | 16299 | — |
| Windows 10 | 1803 | 17134 | C++/WinRT SDK'ya girdi (10.0.17134.0) |
| Windows 10 | 1809 | 17763 | = Server 2019 build |
| Windows 10 | 1903 | 18362 | — |
| Windows 10 | 1909 | 18363 | — |
| Windows 10 | 2004 | 19041 | — |
| Windows 10 | 20H2 | 19042 | — |
| Windows 10 | 21H1 | 19043 | — |
| Windows 10 | 21H2 | 19044 | — |
| Windows 10 | 22H2 | 19045 | **Son Windows 10 sürümü** — EOS 2025-10-14 |
| Windows 11 | 21H2 | 22000 | İlk Windows 11 |
| Windows 11 | 22H2 | 22621 | — |
| Windows 11 | 23H2 | 22631 | — |
| Windows 11 | 24H2 | 26100 | = Server 2025 build; büyük OS değişimi (3rd-party iddialar §2.4) |
| Windows 11 | 25H2 | 26200 | — |
| Windows 11 | 26H1 | 28000 | Şubat 2026 (3rd-party) |
| Windows Server | 2016 | 14393 | — |
| Windows Server | 2019 | 17763 | — |
| Windows Server | 2022 | 20348 | — |
| Windows Server | 2025 | 26100 | — |

**Yaşam döngüsü (kaynaklı):** Microsoft lifecycle sayfalarına göre Windows 10 22H2
genel destek **2025-10-14** sona erdi. Windows 11 Home/Pro sürümleri **24 ay**,
Enterprise/Education **36 ay** servis alır (Microsoft lifecycle learn.microsoft.com —
ayrıntılar `windows-dogrulama.md` §3 tablosunda).

**⚠️ VERIFICATION REQUIRED:** 26H1 = 28000, 25H2 = 26200 eşlemeleri üçüncü taraf
sürüm arşivlerinden (gaijin.at / Wikipedia) alınmıştır; Microsoft docs'tan birebir
build numarası sayfası bu görevde doğrulanmadı.

### §2.3.1 Görsel Zaman Çizelgesi (özet)

```text
1993 NT 3.1 ─── 1996 NT 4.0 ─── 2000 Win2000 ─── 2001 XP
   │                                             │
   │              2006 Vista (Core Audio!)       │ 2009 Win7
   │                     │                       │
   │              2012 Win8 ── 2013 Win8.1       │
   │                             │               │
   └─────────────── NT 6.x ──────┘               │
                                                 │
2015 Win10 (NT 10.0, build 10240) ───────────────┘
   │  1607=14393 · 1809=17763 · 22H2=19045 (EOS 2025-10-14)
   │
2021 Win11 21H2=22000 ── 22H2=22621 ── 23H2=22631
   │
2024 24H2=26100 (= Server 2025) ── 25H2=26200 ── 26H1=28000 (2026, 3rd-party)
```

### §2.4 Windows 11 24H2 — platform kayması (3rd-party iddialar)

Üçüncü taraf kaynaklara (Wikipedia Windows 11 maddesi ve ilgili teknik basın)
göre 24H2:

- Tam OS değişimi ("full OS swap") olarak tanımlanır.
- x86-64-v2 (SSE4.2 + POPCNT) CPU şartı getirir.
- ARM64 tarafında ARMv8.1 şartı; 32-bit ARM uygulama desteği kaldırılır.
- Çekirdekte Rust bileşenleri belirir (ör. win32kbase_rs.sys GDI bölgesi).
- "Germanium" platformu 24H2/25H2/26H2'yi, "Bromine" platformu 26H1'ı (Arm-only)
  kapsar.

**Durum:** Bu satırlar **3rd-party** kaynaklıdır; Microsoft docs doğrulanması
`windows-dogrulama.md` Q-listesinde açık soru olarak kayıtlıdır. CoreMusic hedef
platformu için kesin sayılmaz; yalnızca bilgi notudur.

---

## §3 Masaüstü / Sunucu Ürün Ayrımı

| Ürün ailesi | Örnek sürümler | Hedef kitle | CoreMusic ilgisi |
|---|---|---|---|
| Windows 10 | 1507–22H2 (10240–19045) | Masaüstü (EOS 2025-10-14) | Eski hedef; ASIO/WASAPI sürücüleri hâlâ çalışır ama destek sonu |
| Windows 11 | 21H2–26H1 (22000–28000) | Masaüstü (ana hedef) | Neva Engine ASIO/WASAPI hedef platformu |
| Windows Server | 2016/2019/2022/2025 (14393/17763/20348/26100) | Sunucu | Ses düşük gecikme hedefi DEĞİL; yalnızca build eşlemesi için |
| Windows on Arm | ARM64 varyantları | Arm tabanlı cihaz | ASIO: Microsoft+Yamaha ortaklığıyla Arm'a yerel ASIO (3rd-party, §6) |

**Build eşlemesi değeri:** Windows 10 1607 = Server 2016, Windows 10 1809 =
Server 2019, Windows 11 24H2 = Server 2025 — aynı NT build'ini paylaşırlar.
WDK/SDK sürümleri bu build'lerle eşleşir (ör. SDK/WDK 10.0.26100.x hem Windows 11
24H2 hem Server 2025 hedefidir; ayrıntı `windows-framework.md` §7).

---

## §4 Mimari Katmanlar — Uygulamadan Donanıma

Aşağıdaki ASCII diyagram, Microsoft docs'ta anlatılan katman sırasını özetler
(kaynaklar: user-mode-and-kernel-mode · Overview of Windows Components ·
windows-audio-architecture).

```text
+--------------------------------------------------------------+
|  Uygulamalar (user mode, ring 3)                             |
|  - Neva Engine (C++20, JUCE 9, ASIO host / WASAPI host)      |
|  - PHP >=8.4 (web), Node araçları (test/tooling)             |
+--------------------------------------------------------------+
|  Kütüphaneler / API katmanı (user mode DLL'leri)             |
|  - Win32 API: kernel32 · advapi32 · user32 · ws2_32 ...      |
|  - COM/OLE: ole32 · oleaut32 · Core Audio: mmdevapi.dll      |
|  - AVRT (avrt.dll) · WinMM (winmm.dll) · API setleri         |
+--------------------------------------------------------------+
|  NTDLL.DLL — NT sistem servisleri için user-mode stub        |
+--------------------------------------------------------------+
================ ring 0 / kernel mode sınırı ===================
|  Executive (NTOSKRNL.EXE içinde)                             |
|  Object Mgr · Memory Mgr · Process/Thread · I/O Mgr ·        |
|  PnP · Power · KTM · Security Reference Monitor · RTL ...     |
|  + Kernel (scheduling, synchronization, ISR/DPC)             |
+--------------------------------------------------------------+
|  HAL — Hardware Abstraction Layer                            |
+--------------------------------------------------------------+
|  Sürücüler (kernel mode) — WDM / KMDF / UMDF / ACX ...       |
|  - Audio: Portcls.sys · Ks.sys · Usbaudio.sys · APO'lar      |
+--------------------------------------------------------------+
|  Donanım (DAC/ADC/I2S · USB audio · DMA · IRQ)               |
+--------------------------------------------------------------+
```

### §4.1 NT Bileşen Sözlüğü (kaynak: Overview of Windows Components)

Microsoft WDK "Overview of Windows Components" sayfası NT kurulumunun temel
bileşenlerini listeler; aşağıdaki tablo bu listeyi görev bağlamıyla birleştirir.

| Bileşen | Mod | Rol (docs özeti) | CoreMusic ilgisi |
|---|---|---|---|
| NTOSKRNL.EXE | kernel | Çekirdek + executive (scheduler, executive managers) | WASAPI'nin altında; ses sürücüleri burada çalışır |
| HAL (hal.dll) | kernel | Donanım soyutlama katmanı | IRQ/DMA farklılıklarını gizler |
| NTDLL.DLL | user | NT API user-mode stub'ları | Uygulama → sistem servisi geçişi |
| Win32k.sys | kernel | Pencere/GDI alt sistemi (user+kernel tarafı) | UI; ses yolumuzla ilgisiz |
| CSRSS.exe | user | Client/Server Runtime (konsol/ikon süreçleri) | — |
| SMSS.exe | user | Session Manager (oturum başlatma) | — |
| LSASS.exe | user | Güvenlik/oturum doğrulama | — |
| Winlogon.exe | user | Kayıt/oturum ekranı | — |
| SSDT | kernel | System Service Dispatch Table (sistem servisi yönlendirme) | `Nt/Zw` çağrılarının çekirdeğe gidişi |
| Kernel-mode sürücüler | kernel | Aygıtlarla doğrudan ilgilenen kod | Portcls.sys, Ks.sys, Usbaudio.sys (ses yığını) |
| Kütüphaneler (kernel) | kernel | Kernel/Executive/RTL/Safe String/HAL/CLFS/WMI kütüphaneleri | Sürücü geliştirme API'si |

> **⚠️ Not:** Tablodaki "Rol" sütunu Microsoft docs özetidir; her bileşenin
> derinlemesine görev tanımı bu dosyanın **kapsam dışıdır** (kernel dosyasına
> bakılır: `windows-kernel.md`). CSRSS/SMSS/LSASS/Winlogon satırları aynı
> sayfada listelenir; tekil görev ayrıntıları için ⚠️ VERIFICATION REQUIRED
> (bu görevde tekil sayfa taraması yapılmadı).

### §4.2 User Mode ↔ Kernel Mode Geçiş Yolu

```text
Uygulama kodu (ring 3)
   │  Win32 API çağrısı (ör. CreateFileW / CoCreateInstance)
   ▼
Kernel32.dll · Advapi32.dll · Ole32.dll ... (user-mode DLL)
   │
   ▼
Ntdll.dll — Nt*/Zw* sistem servisi stub'ları
   │  syscall / sysenter benzeri geçiş (mekanizma adı ⚠️ VERIFICATION REQUIRED)
   ▼
NTOSKRNL.EXE — SSDT üzerinden executive'e yönlendirme (ring 0)
   │
   ▼
Executive manager (ör. I/O Manager) + ilgili sürücü
```

**User mode vs kernel mode karşılaştırması (docs temelli):**

| Özellik | User mode | Kernel mode |
|---|---|---|
| Ring | 3 | 0 |
| Çalışan kod | Uygulama + user-mode DLL'ler | Çekirdek, executive, sürücüler |
| Bellek erişimi | Kendi sanal adres alanıyla sınırlı | Sistem belleği dahil tüm adres alanı |
| Hata etkisi | Hata yalnız uygulamayı çökertir | Hata sistem çöküşü (BSOD) potansiyeli |
| Örnek (ses) | Neva Engine, PHP, Node | audiodg.exe yükleyicisi, Portcls.sys, APO'lar |

> Kaynak: `user-mode-and-kernel-mode` + `overview-of-windows-components`
> (WDK). "Hata etkisi" sütunu bu iki sayfanın "faulting in kernel mode can
> bring down the system" benzeri ifadelerinden türetilmiştir; birebir alıntı
> değildir.

### §4.3 Sürüm Numaralandırma ve Servis Modeli (özet)

1. **NT build numarası** (ör. 26100) sürümün çekirdek kimliğidir; §2.3 tablosu.
2. **Windows 10/11 sürümleri** (ör. 24H2) yıl-yarıyıl etiketidir; karşılığı NT
   build'dir (24H2 → 26100).
3. **Servis modeli:** Microsoft, Windows 10'dan itibaren sürüm güncellemelerini
   kümülatif "servicing" ile dağıtır (Windows as a Service). ⚠️ Bu paragraf
   genel bilgidir; bu görevde tekil Microsoft servis sayfası taraması YAPILMADI
   → VERIFICATION REQUIRED; kesin iddia olarak kullanılmaz.
4. **WDK/SDK eşleşmesi:** SDK ve WDK build numaraları eşleşmelidir (kaynak:
   `/windows-hardware/drivers/download-the-wdk`); ayrıntı
   `windows-framework.md` §7.

**Katman notları (kaynaklı):**

1. **NTDLL sınırı:** NTDLL.DLL, user mode ile NT çekirdeği arasındaki ince
   katmandır; `Nt/Zw` sistem servisi çağrılarının user-mode stub'larını içerir
   (WDK "libraries-and-headers" sayfası: Ntoskrnl.exe Nt/Zw vs Ntdll.dll
   user-mode).
2. **Executive içeriği:** Microsoft WDK "Overview of Windows Components" sayfası
   executive'i bir dizi yönetici (manager) olarak tanımlar — Object Manager,
   Memory Manager, Process-Thread Manager, I/O Manager, PnP Manager, Power
   Manager, Configuration Manager (registry), KTM (Kernel Transaction Manager),
   Security Reference Monitor.
3. **Kernel/Executive ayrımı sürücü dokümanlarında:** WDK'daki "Executive Support
   Library", "I/O Manager" gibi başlıklar executive alt sistemlerinin sürücülere
   açılan arayüzlerini tanımlar (ayrıntı `windows-kernel.md` §3).
4. **Ses yığını özelinde:** uygulama → Core Audio API'leri (mmdevapi/audioses) →
   audio engine (audiodg.exe) → sürücü katmanı (port/miniport, ACX/KS) → donanım
   sırası `windows-ses-yigini.md` §2'de ayrıntılıdır; bu dosyada yalnız §5'te
   konumlanır.

### §4.4 Katman → Sorumluluk → İlgili Dosya Matrisi

| Katman | Sorumluluk | İlgili deep-dive dosyası | İlgili agent |
|---|---|---|---|
| Uygulama (Neva Engine) | Ses üretimi, DSP zinciri, ASIO/WASAPI host | (K3-ses-motoru şartnameleri) | embedded-engineer |
| Uygulama (web: PHP/Node) | HTTP, routing, test araçları | `windows-framework.md` §9 | backend-architect · qa-engineer |
| Win32 API / COM | Dosya, kayıt, thread, COM yaşam döngüsü | `windows-api.md` | windows-software-engineer |
| Core Audio API | Cihaz listeleme, akış açma, oturum/SES | `windows-ses-yigini.md` | windows-software-engineer |
| AVRT/MMCSS | Zamanlama önceliği (Pro Audio görevi) | `windows-ses-yigini.md` §5 | embedded-engineer |
| Executive (I/O, Memory) | IRP yönlendirme, bellek, senkronizasyon | `windows-kernel.md` §3 | — (kernel dokümantasyonu) |
| Sürücü modelleri (WDM/KMDF/UMDF/ACX) | Donanım erişimi | `windows-kernel.md` §5 | windows-software-engineer |
| HAL + donanım | IRQ/DMA, DAC/ADC/USB | (k17/k18/k19 hardware vault) | audio-hardware-engineer |

---

## §5 CoreMusic'in Windows Hedefi

### §5.1 Repo kanıtları (disk)

| İddia | Kanıt (dosya) | Durum |
|---|---|---|
| PHP >=8.4 çalışma zamanı | `shared/composer.json` (`"php": ">=8.4"` require) | ✅ IMPLEMENTED (repo) |
| Node araçları yalnız test/tooling | `package.json` (vitest, playwright devDependencies) | ✅ IMPLEMENTED (repo) |
| ASIO cihaz kaybı → WASAPI fallback | `.ai/AGENTS.md` §17/6 (Edge Cases satırı 6) | ✅ IMPLEMENTED (belge) |
| DSP Hardware Mode — ASIO/WASAPI host | `.ai/.decisions/accepted/ADR-017-dsp-hardware-mode.md` (accepted, 2026-09-24) | ✅ IMPLEMENTED (ADR) · Kod: ⚠️ PLANNED |
| Latency bütçesi <10ms ASIO / <20ms WASAPI | `.ai/CLAUDE.md` §ADR-017 notu (satır ~429) | ✅ IMPLEMENTED (belge) |
| Neva Engine kodu | `projects/NevaEngine` · `*.cpp/*.h` = 0 | ⚠️ PLANNED (cpp=0) |
| ASIO SDK 2.3.4 · JUCE 9 · C++20 | `.ai/AGENTS.md` §4/#6, §15 agent tablosu (Embedded Engineer) | ✅ IMPLEMENTED (belge) · Kod: ⚠️ PLANNED |

### §5.2 Hedef mimari (ADR-017 özeti)

ADR-017 (accepted) DSP Hardware Mode'u üç katman tanımlar:

1. **XMOS DSP** (firmware katmanı — `dsp-firmware-engineer`).
2. **JUCE 9 / C++20 Neva Engine** (host uygulaması — `embedded-engineer`).
3. **ASIO / WASAPI host yolları** (Windows ses backend'i — `windows-software-engineer`
   ve `embedded-engineer` sınırında).

Latency bütçesi: **ASIO <10 ms · WASAPI <20 ms** (ADR-017 — `.ai/CLAUDE.md`).

### §5.3 Windows platformunun CoreMusic rolü

- **Birincil düşük gecikmeli yol:** ASIO (exclusive, donanım doğrudan) —
  deep-dive `K003-audio/backend/asio/` altında ayrıdır (§6).
- **Yedek yol:** WASAPI (shared mode dahil) — `.ai/AGENTS.md` §17/6 "ASIO device
  loss → WASAPI fallback". Ayrıntı `K000/isletim-sistemi-audio/wasapi/` deep-dalı
  hedeflenir; **bu görevde diskte mevcut değildir** (bkz. §7).
- **Uygulama katmanı:** Neva Engine, Win32 API + COM üzerinden Core Audio'ya
  bağlanır (`windows-api.md` §5, `windows-ses-yigini.md` §4).

### §5.4 Destek Matrisi (CoreMusic hedef platformu)

| Kriter | Windows 10 22H2 | Windows 11 24H2+ | Server 2025 | Windows on Arm |
|---|---|---|---|---|
| Destek durumu (2026-10) | ❌ EOS 2025-10-14 | ✅ aktif servis | ✅ aktif servis | ⚠️ ASIO yerel desteği 3rd-party iddia |
| ASIO host | ✅ (ASIO SDK 2.3.4) | ✅ | ⚠️ ses etkileşimi UNKNOWN | ⚠️ Microsoft+Yamaha ortak ASIO (3rd-party) |
| WASAPI host | ✅ (IAudioClient3 Win10+) | ✅ | ✅ (teknik olarak) | ✅ (WASAPI Arm64 mevcut — ⚠️ tekil doğrulama yok) |
| JUCE 9 backend | ✅ hedef | ✅ hedef | ⚠️ UNKNOWN | ⚠️ UNKNOWN |
| CPU şartı | klasik x86-64 | x86-64-v2 (3rd-party iddia) | — | ARMv8.1 (3rd-party iddia) |

> **Sonuç:** CoreMusic pratik hedefi **Windows 11 (x86-64)** + ASIO birincil /
> WASAPI yedektir. Windows 10 satırı yalnız geçiş dönemidir (EOS doldu).

### §5.5 Repo Durum Etiketi Özeti

| Yüzey | Durum etiketi |
|---|---|
| Şartname (`.ai/architecture/firmware/`, ADR'ler, AGENTS.md) | ✅ IMPLEMENTED (belge) |
| C++ kod (`*.cpp/*.h`, `projects/NevaEngine`) | ⚠️ PLANNED (cpp=0) |
| Elektronik (`electronic/`) | ⚠️ PLANNED (0 dosya) |
| Bu 6 dosyalık deep-dive seti | ✅ IMPLEMENTED (belge) · Kod: ⚠️ PLANNED |

---

## §6 Kapsam Sınırı — ASIO Deep-Dive Ayrıdır

Bu dosyanın **ASIO kapsamı yalnızca konumlandırmadır**; ASIO'nun:

- COM arayüz modeli (asio host, `host/pc/asiolist.cpp`),
- ASIO SDK 2.3.4 sürüm/ lisans (proprietary + GPLv3 çift lisans),
- buffer/latency getPositions akışı,
- Steinberg built-in ASIO Driver ve Generic Low Latency ASIO Driver karşıtlığı,

gibi ayrıntıları **`K003-audio/backend/asio/`** altındaki deep-dive dosyalarına
aittir. Bu dosyada ASIO yalnız şu cümlelerle anılır:

> ASIO, Windows üzerinde Core Audio yığınının **dışında/dibinde** çalışan,
> uygulamanın donanıma exclusive erişimini sağlayan bir driver-arayüzü
> protokolüdür; CoreMusic'in birincil düşük gecikmeli yoludur ve ADR-017'de
> WASAPI ile birlikte iki host seçeneği olarak tanımlanmıştır.

**Kural:** ASIO ayrıntısı bu dosyaya kopyalanmaz; kopyalanırsa tekrar (duplicate)
olur ve `windows-dogrulama.md` Q-listesine "scope overlap" olarak düşer.

### §6.1 Platform Konum Karşılaştırması (yalnız konumlandırma)

| Platform | Düşük gecikmeli ses yolu | Bu vault'taki karşılığı |
|---|---|---|
| Windows | ASIO (exclusive) + WASAPI (Core Audio) | `K000/.../windows/` (bu set) + `K003/backend/asio/` + `K000/isletim-sistemi-audio/wasapi/` |
| macOS | Core Audio — Steinberg dokümanına göre macOS'ta ayrı ASIO driver'a gerek yoktur (built-in) | ⚠️ macOS dalı bu görev kapsamında DEĞİL |
| Linux | ⚠️ UNKNOWN (bu görevde doğrulanmadı — ALSA/PipeWire iddiası yazılmaz) | ⚠️ VERIFICATION REQUIRED |

> Kaynak (macOS satırı): Steinberg "Built-in ASIO Driver" sayfası — macOS'un
> Core Audio üzerine kurulu olması nedeniyle ayrı ASIO driver'ı gerekmediği
> ifade edilir. URL: `steinberg.help` (doğrulama tablosu §3, satır "ASIO-4").

---

## §7 Açık Sorular (Bu Dosyaya Özgü)

| # | Soru | Durum |
|---|---|---|
| Q-A1 | 26H1 build 28000 eşlemesi Microsoft docs'tan doğrulanabilir mi? | ⚠️ VERIFICATION REQUIRED (3rd-party) |
| Q-A2 | NT 4.0 GDI kernel-mode taşıma kararı için tekil Microsoft kaynak var mı? | ⚠️ VERIFICATION REQUIRED |
| Q-A3 | Windows 11 24H2 "full OS swap" + Rust-in-kernel iddiası Microsoft kaynaklı mı? | ⚠️ VERIFICATION REQUIRED (3rd-party) |
| Q-A4 | CoreMusic asgari desteklenen Windows sürümü (min build) nedir? | UNKNOWN — repo'da pin yok; `.ai/` vault'ta hedef build kaydı aranmalı |
| Q-A5 | `K000/isletim-sistemi-audio/wasapi/` deep-dalı diskte yok (yalnız index+kapsam+doğrulama var) — ne zaman açılır? | UNKNOWN (orchestrator kararı) |

---

## §8 İlişkili Dosyalar

- `windows-kernel.md` — kernel/executive, IRQL, sürücü modelleri (KMDF/UMDF/WDM)
- `windows-api.md` — Win32 API envanteri, DLL'ler, COM/WinRT
- `windows-framework.md` — CRT/UCRT, MSVC, Media Foundation, XAudio2, servisler, PHP/Node temas katmanı
- `windows-ses-yigini.md` — Core Audio, shared/exclusive, AVRT/MMCSS, ASIO konumu
- `windows-dogrulama.md` — iddia · URL · tarih · statü tablosu + Q-listesi
- `../isletim-sistemi-audio/index.md` — WASAPI ana dalı (deep-dive hedefi)
- `K003-audio/backend/asio/` — ASIO deep-dive (bu dosyanın sınır dışı)

---

## §9 Terim Sözlüğü (bu dosyaya özgü)

| Terim | Tanım (kaynaklı) |
|---|---|
| **NT (New Technology)** | Windows 10/11/Server'ın çekirdek ailesi; ilk sürüm NT 3.1 (1993) |
| **NT build** | Çekirdek sürüm numarası (ör. 26100) — §2.3 |
| **User mode / Kernel mode** | Ring 3 / ring 0 ayrımı — §4.2 |
| **Executive** | NTOSKRNL içindeki yönetici kümesi (Object, Memory, I/O...) — §4.1 |
| **HAL** | Hardware Abstraction Layer — donanım soyutlama — §4.1 |
| **SSDT** | System Service Dispatch Table — sistem servisi yönlendirme tablosu — §4.1 |
| **NTDLL.DLL** | User-mode NT API stub katmanı — §4.1 |
| **WDK** | Windows Driver Kit — sürücü geliştirme kiti — `windows-framework.md` §7 |
| **EOS** | End of Services (destek sonu) — örn. Windows 10 22H2: 2025-10-14 |
| **Host uygulama** | ASIO/WASAPI üzerinden ses üreten uygulama (Neva Engine) |

---

## §10 Kaynak URL Listesi (bu dosyanın dayandığı sayfalar)

> Tam iddia · URL · tarih · statü tablosu `windows-dogrulama.md` §3'tedir; burada
> yalnızca bu dosyada kullanılan sayfalar kısaca listelenir.

1. `https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/user-mode-and-kernel-mode`
2. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/overview-of-windows-components`
3. `https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/libraries-and-headers`
4. `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/windows-audio-architecture`
5. `https://learn.microsoft.com/en-us/windows-hardware/drivers/wdk/download-the-wdk`
6. `https://learn.microsoft.com/en-us/windows-hardware/drivers/gettingstarted/choosing-a-driver-model`
7. `https://learn.microsoft.com/en-us/lifecycle/products/windows-10-home-pro` (yaşam döngüsü — EOS tarihi)
8. Steinberg built-in ASIO Driver yardım sayfası (`steinberg.help`) — macOS notu
9. Üçüncü taraf sürüm arşivleri: `gaijin.at` Windows NT sürüm listesi + Wikipedia Windows NT/Windows 11 maddeleri (yalnız corroboration)

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Status:** draft — tüm iddialar `windows-dogrulama.md` §3 tablosunda URL ile
kayıtlıdır; ⚠️ işaretli satırlar doğrulanmadan kesin sayılmaz (zero-hallucination).
**Durum etiketi:** Şartname: ✅ IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode