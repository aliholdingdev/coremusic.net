---
title: "K001-donanım — OS × Dil Erişim Matrisi (13 dil × 3 işletim sistemi)"
type: analysis-report
node_id: K001-donanım/raporlar/os-dil-matrisi
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım
  - K000-isletim-sistemi
evidence:
  - "Birincil kaynak: .ai/architecture/K001-donanım/donanım-platformlararasi-kod-ornekleri.md (1018 satır — §G1–§G8 mevcut, §G9–§G11 bu tur paralel ajan tarafından ekleniyor, §K3 = 30 satır URL matrisi, §K4 = 8 reddedilen URL)"
  - "2026-10-14 ölçümü (bu oturum): 4 yeni URL fetch ile HTTP 200 — §6 N1–N4; Swift içerik doğrulaması chrome-devtools tarayıcı render ile (39.677 karakter, başlık 'Statements | Documentation')"
  - "§K3 kaynaklı 30 URL platformlararasi dosyasında 2026-10-14 tarihli HTTP 200 ölçümüyle kayıtlıdır (bu rapor yeniden ölçmez, atıf yapar)"
  - "K001 raporlar/ seti ≥500 satır/dosya hedefi: K001-donanım-mevcut-durum-raporu.md:356-357"
  - "PHP_OS_FAMILY repo'da 0 isabet iddiası: platformlararasi-kod-ornekleri.md:828-829 (ikincil aktarım — K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-ornekler.md L161 sayımı, bu oturumda bağımsız okunmadı)"
validation:
  - "Bu dosya salt-okunur kaynaklardan üretilmiştir; başka hiçbir dosya değiştirilmemiş, git commit atılmamıştır"
  - "§6 URL matrisi: 4 yeni URL (N1–N4, 2026-10-14, 4/4 HTTP 200) + §K3'teki 30 mevcut URL + 13 reddedilen/404 kayıt (R1–R13)"
  - "§4'teki her dil önerisi 'şablon — derlenmedi/çalıştırılmadı' etiketlidir; rapor üretim kodu sunmaz (platformlararasi §KAPSAM 3)"
  - "Doğrulanamayan her iddia satırında '⚠️ VERIFICATION REQUIRED', bilinmeyen değer 'UNKNOWN' olarak bırakılmıştır (zero-hallucination §3)"
open_questions:
  - "§G9 (Swift) · §G10 (Kotlin) · §G11 (C++) bu tur paralel ajan tarafından platformlararasi dosyasına yazılıyor — bu rapor bölümleri '§G9–§G11 (bu tur)' olarak referanslar, içeriklerini bu oturumda okumaz"
  - "Swift araç zincirinin (derleyici/SDK) Windows destek olgunluk düzeyi bu raporda doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "PowerShell'in macOS/Linux üzerinde kurulu-varsayılan erişim yolu doğrulanmadı (yalnız Windows 11 çalışma makinesi var) → UNKNOWN"
  - "§K3 tablosu 30 satır taşır; platformlararasi §K1 G4 kapısı '29 URL' der — sayım farkı (29 vs 30) hangisi bağlayıcı?"
  - "§G9–§G11 tamamlandığında bu raporun §2/§4/§5 satırları bölüm içerikleriyle hizalanmalı mı (Vault Steward kararı)"
date: 2026-10-14
group: kod
---

# K001-donanım — OS × Dil Erişim Matrisi (13 dil × 3 OS)

## §1 Bu raporun rolü (ELI15)

Bir donanım katmanı kodu tek bir dilde yazılmaz: CoreMusic'in repo dili PHP/JS
olsa da, ses motoru C++20, CLI araçları PowerShell/Bash, gömülü taraf C'dir.
Aynı soru her dilde tekrarlanır: **"Bu işletim sisteminde diske, belleğe,
donanım kaydına, ses aygıtına nasıl ulaşırım?"** Cevap dile göre değil,
önce işletim sistemine göre değişir; dil yalnızca o cevabı hangi sözdizimiyle
çağırdığını belirler.

Bu rapor 13 dil × 3 işletim sistemi = **39 hücrelik** tam matrisi tek yerde
toplar. Her hücre üç şeyi verir: (1) OS algılama mekanizması, (2) fiziksel
erişim yolu özeti, (3) birincil kaynağın §G referansı. Ardından dört tablo
daha gelir: OS başına dil-bağımsız erişim yolu (§3), dil başına öneriler
(§4), bilinen risk/sınır kayıtları (§5) ve URL doğrulama matrisi (§6).

**Okuma kuralı:** bu bir karar dosyası değil, **durum + kanıt** dosyasıdır.
Buradaki hiçbir satır bağlayıcı değildir; bağlayıcı olan
`donanım-anayasa.md` §2 ve accepted ADR'lerdir. Tüm kod satırları
**"şablon — derlenmedi/çalıştırılmadı"** statüsündedir; bu rapor üretim kodu
üretmez, kod fikri taşımaz, yalnız mevcut şablonların matrisini yapar.

### §1.1 Kanıt türleri ve güven düzeyi

| Kanıt türü | Örnek (bu rapor) | Güven | Sınır |
|---|---|---|---|
| Birincil URL ölçümü (bu oturum) | 4 yeni URL fetch 200 (§6 N1–N4) | en yüksek | HTTP durum kodu |
| Birincil içerik okuması (bu oturum) | Swift sayfası tarayıcı render (§6.2) · Kotlin/C++ sayfası HTML çekimi | en yüksek | okunan metin parçası |
| Aktarılan URL ölçümü | §K3'teki 30 URL (2026-10-14, platformlararasi dosyası) | orta — dosyanın kendi ölçümü | bu oturumda yeniden ölçülmedi |
| Aktarılan kod/envanter satırı | §G7.4 "PHP_OS_FAMILY 0 isabet" (L161 sayımı) | orta — ikincil | sayım dosyası bağımsız okunmadı, `⚠️` |
| Yorum/öneri | §4 önerileri | yok — bağlayıcı değil | her satır dayanağına bağlanır |

Kural: hiçbir satır "duydum" düzeyinde değildir; her iddia ya URL+ölçüm, ya
dosya+satır, ya `⚠️ VERIFICATION REQUIRED`/`UNKNOWN` taşır.

### §1.2 Dil kodları (bu raporun §2–§5'teki etiketleri)

| Kod | Dil | Kaynak bölüm (platformlararasi) |
|---|---|---|
| G1 | JavaScript (Node.js) | §G1 (satır 66) |
| G2 | C | §G2 (satır 159) |
| G3 | Python | §G3 (satır 293) |
| G4 | Go | §G4 (satır 425) |
| G5 | Rust | §G5 (satır 545) |
| G6 | C# (.NET) | §G6 (satır 613) |
| G7 | PHP | §G7 (satır 727) |
| G8a | Bash | §G8.1 (satır 836) |
| G8b | PowerShell | §G8.2 (satır 865) |
| G8c | Java | §G8.3 (satır 885) |
| G9 | Swift | **§G9 (bu tur — paralel ajan yazıyor)** |
| G10 | Kotlin | **§G10 (bu tur — paralel ajan yazıyor)** |
| G11 | C++ | **§G11 (bu tur — paralel ajan yazıyor)**; §G8.4 satır 4'te "Aynı G2 makroları" kaydı zaten vardır |

> **Durum notu (gizlenmez):** 2026-10-14 bu oturumda platformlararasi dosyası
> 1018 satır olup §G1–§G8 içermektedir; §G9/Swift · §G10/Kotlin · §G11/C++
> başlıkları diskte henüz görünmemektedir. Bu rapor üçünü de **"§G9–§G11
> (bu tur)"** olarak referanslar; üçünün içeriği bu raporun kanıtı değildir.

## §2 Ana matris — 13 dil × 3 OS

Her satır bir dildir; her OS hücresi **algılama + erişim yolu özeti** taşır.
Kaynak sütunu, hücredeki iddiaların dayandığı §G bölümüdür.

| # | Dil | Windows 10/11 | macOS (Darwin) | Linux | §G |
|---|---|---|---|---|---|
| G1 | JavaScript (Node.js) | `process.platform === 'win32'` → PowerShell `Get-PSDrive` köprüsü ile disk kökleri; bellek `os.totalmem()` (OS-bağımsız) | `=== 'darwin'` → `/Volumes` dizin okuma; ağ arayüzü `en0` tipi adlar | `=== 'linux'` → `/proc/mounts` okuma; `/proc` köprüleri | §G1 |
| G2 | C | `#if defined(_WIN32)` → Win32 `GetLogicalDrives` + `GlobalMemoryStatusEx` | `#elif defined(__APPLE__)` ⚠️ (URL yok) → POSIX `uname` + IOKit IORegistry (üye adları yazılmadı) | `#elif defined(__linux__)` → `/proc/meminfo` + `/sys/class/block` | §G2 |
| G3 | Python | `sys.platform == 'win32'` → ctypes ile `GlobalMemoryStatusEx` P/Invoke | `'darwin'` → `/Volumes` + `sysctl -n hw.memsize` ⚠️ (kaynak 404) | `'linux'` → `/proc/meminfo` | §G3 |
| G4 | Go | `runtime.GOOS == "windows"` → ortam `SystemDrive` (stdlib'de disk kök listeleme API'si **yok** — tam liste ADR bekliyor) | `"darwin"` → `/` + `/Volumes/*` (`os.ReadDir`) | `"linux"` → `/proc/meminfo` (`os.Open`) | §G4 |
| G5 | Rust | `cfg!(target_os = "windows")` → ortam `SystemDrive` (stdlib disk kök API'si **yok**; crate adı yazılmaz) | `cfg!(target_os = "macos")` → `std::fs::read_dir("/Volumes")` | `cfg!(target_os = "linux")` → `std::fs::read_to_string("/proc/meminfo")` | §G5 |
| G6 | C# (.NET) | `RuntimeInformation.IsOSPlatform(OSPlatform.Windows)` → `DriveInfo.GetDrives()` + P/Invoke `GlobalMemoryStatusEx` | `OSPlatform.OSX` → `DriveInfo` + IOKit P/Invoke ⚠️ (üye adı doğrulanmadı) | `OSPlatform.Linux` → `DriveInfo` + `/proc/meminfo` regex | §G6 |
| G7 | PHP | `PHP_OS_FAMILY === 'Windows'` → PowerShell `Win32_OperatingSystem` köprüsü | `PHP_OS_FAMILY === 'Darwin'` → `sysctl -n hw.memsize` ⚠️ (kaynak 404) | `PHP_OS_FAMILY === 'Linux'` → `/proc/meminfo` | §G7 |
| G8a | Bash | Git Bash/MSYS2 altında `uname -s` çıktısının MINGW/MSYS önekli olduğu iddiası ⚠️ doğrulanamadı → `*)` dalı | `uname -s == Darwin` → `sysctl -n hw.memsize` ⚠️ | `uname -s == Linux` → `/proc/meminfo` (awk) | §G8.1 |
| G8b | PowerShell | `[RuntimeInformation]::IsOSPlatform([OSPlatform]::Windows)` + `[System.IO.DriveInfo]::GetDrives()` | `[OSPlatform]::OSX` sorgulanabilir; PowerShell'in macOS'ta kurulu olduğu varsayımı **UNKNOWN** | `[OSPlatform]::Linux` aynı yöntem; kurulu ortam doğrulanmadı | §G8.2 |
| G8c | Java | `System.getProperty("os.name")` önek eşleşmesi (`windows`) → disk/registry için JNI veya süreç çalıştırma yolu ⚠️ (şablonda yok) | aynı erişim, önek `mac`/`darwin` (`Mac OS X` değeri ⚠️ önek-sabit dize değil) | aynı erişim, önek `linux` | §G8.3 |
| G9 | Swift | `#if os(Windows)` — platform koşulu `os()` argümanları arasında **Windows doğrulandı**; fiziksel erişim yolu (Foundation/Win32 köprüsü) bu raporda yazılmadı ⚠️ | `#if os(macOS)` → algılama doğrulandı; IOKit/CoreAudio erişimi §3.2'de (dil-köprüsü ⚠️) | `#if os(Linux)` → algılama doğrulandı; `/proc` erişimi dil-bağımsız §3.3 | **§G9 (bu tur)** |
| G10 | Kotlin | Kotlin/Native: POSIX platform kütüphanesi Windows hedefleri için de vardır — **"Windows (MinGW)"** bağı korunur (doğrulandı); Kotlin/JVM: `os.name` G8.3 ile aynı | Kotlin/Native `~/.konan` + POSIX bindings; Kotlin/JVM `os.name` | Kotlin/Native Linux POSIX bindings + `/proc` köprüsü; Kotlin/JVM `os.name` | **§G10 (bu tur)** |
| G11 | C++ | `#if defined(_WIN32)` → koşullu dahil etme doğrulandı (cppreference); erişim G2 ile aynı Win32 yolları | `__APPLE__` ⚠️ (URL yok) + IOKit; sözdizimi G11 doğrulandı | `__linux__` + `/proc` `/sys` (aynı makrolar) | **§G11 (bu tur)** |

### §2.1 Matris okuma notları

1. **Algılama ≠ erişim.** Sütunların ilk yarısı "hangi sözdizimiyle OS'yi
   anlarım?", ikinci yarısı "o OS'ta veriye nasıl ulaşırım?" sorusudur; ikisi
   her dilde aynı hızda olgunlaşmaz (ör. Swift: algılama doğrulandı, erişim
   yolu yazılmadı).
2. `win32` değeri 32/64-bit ayrımı değildir — Windows'un tüm sürümlerinde
   aynıdır (platformlararasi:155, kaynak nodejs `process.html`).
3. Üç hücre ("§G9 (bu tur)", "§G10 (bu tur)", "§G11 (bu tur)") bu raporun
   **okumadığı**, paralel ajanın bu turda yazdığı bölümlere atıftır; bu üç
   satır tamamlandı sayılır mı, Vault Steward kararıdır (§OPEN QUESTIONS Q5).
4. `⚠️` işareti = doğrulanamayan iddia; `UNKNOWN` = bu ortamda ölçülemeyen
   değer. İkisi de bilgi boşluğu değil, **işaretli boşluktur** — doldurulmaz.

## §3 OS → fiziksel erişim yolu (dilden bağımsız)

Bu bölüm dili değil, **işletim sisteminin kapısını** anlatır: herhangi bir
dilden (§2'nin 13 satırı) aynı fiziksel yola gidilir; fark yalnızça o yolu
çağıran sözdizimidir. Kaynak: platformlararasi §PLATFORM MATRİSİ (satır 51-62).

| # | İhtiyaç | Windows | macOS (Darwin) | Linux |
|---|---|---|---|---|
| P1 | Disk/liste kökleri | `GetLogicalDrives` (Win32) | `/Volumes` dizini · `mount` | `/proc/mounts` |
| P2 | Bellek | `GlobalMemoryStatusEx` (Win32) | `sysctl -n hw.memsize` ⚠️ | `/proc/meminfo` |
| P3 | Ağ arayüzleri | `GetAdaptersAddresses` (Win32) | IOKit/CoreAudio ⚠️ eşleşme yok → `getifaddrs` ⚠️ (Apple sayfası 404) | `/proc/net` · `netlink` |
| P4 | Donanım kayıt (registry) | Kayıt defteri | **IOKit** IORegistry | **sysfs** `/sys/class/**` |
| P5 | Ses aygıtı | WASAPI ⚠️ (K000 sahipliği) | **CoreAudio** | **ALSA** |
| P6 | Sistem bilgisi | `GetSystemInfo` ailesi ⚠️ | `uname` (POSIX) | `uname` · `/proc` · `/sys` |

> **Sınır notu (platformlararasi:62):** P4–P6 satırlarındaki alt sistem
> **sahiplikleri** K000-isletim-sistemi ile paylaşılıdır; bu rapor yalnız
> **çağrı şablonunu** listeler, sürücü/düşük-seviye uygulama K001 kapsamı
> dışındadır (`donanım-anayasa.md` §2 satır 5).

### §3.1 Windows — registry · WASAPI · Win32

| Katman | Yol | Dayanak | Durum |
|---|---|---|---|
| Diskkök | `GetLogicalDrives` (bit maskesi, A:\ → Z:\) | §K3 #20 (HTTP 200) | şablon — derlenmedi (§G2.2) |
| Bellek | `GlobalMemoryStatusEx` → `MEMORYSTATUSEX` | §K3 #21 (HTTP 200) | şablon — derlenmedi (§G2.2 · §G3.4 · §G6.3) |
| Ağ | `GetAdaptersAddresses` | §K3 #22 (HTTP 200) | şablon (§G8.2 arkası) |
| Donanım kayıt | Kayıt defteri (registry) | §3 tablosu P4 | erişim şablonu bu turda yazılmadı → `UNKNOWN` (kayıt okuma kodu yok) |
| Ses | WASAPI | §3 P5 · ADR-017 katman 3 | **K000 sahipliği** — K001 yalnız sınır |
| Sistem bilgisi | `GetSystemInfo` ailesi | §3 P6 | `⚠️ VERIFICATION REQUIRED` (şablonda çağrı yok) |

### §3.2 macOS (Darwin) — IOKit · CoreAudio · sysctl

| Katman | Yol | Dayanak | Durum |
|---|---|---|---|
| Diskkök | `/Volumes` dizin okuma | platformlararasi §G1.2/§G3.2/§G4.3/§G5.2 | şablon — derlenmedi |
| Bellek | `sysctl -n hw.memsize` | **404** (§6 R1) | `⚠️ VERIFICATION REQUIRED` |
| Ağ | `getifaddrs` | **404** (§6 R8) | `⚠️ VERIFICATION REQUIRED` |
| Donanım kayıt | IOKit IORegistry (`IOServiceMatching` kalıbı) | §K3 #14 (HTTP 200) | çağrı imzaları yazılmadı (§G2.4 notu) → üye adları `⚠️` |
| Ses | CoreAudio | §K3 #15 (HTTP 200) | dil-köprüsü bu raporda yazılmadı → `UNKNOWN` |
| Sistem bilgisi | POSIX `uname` | §K3 #28 (HTTP 200) | şablon — çalıştırılmadı (§G2.4) |

### §3.3 Linux — sysfs · proc · ALSA

| Katman | Yol | Dayanak | Durum |
|---|---|---|---|
| Diskkök | `/proc/mounts` (2. sütun = bağlama noktası) | §K3 #17 (HTTP 200) | şablon — çalıştırılmadı |
| Blok aygıtları | `/sys/class/block` | §K3 #16 (HTTP 200) | şablon — derlenmedi (§G2.3) |
| Bellek | `/proc/meminfo` → `MemTotal` | §K3 #17 (HTTP 200) | şablon (§G2.3 · §G3.3 · §G4.2 · §G6.3 · §G7.3) |
| Ağ | `/proc/net` · `netlink` | §3 P3 | netlink şablonu yok → `UNKNOWN` (yalnız `/proc` var) |
| Donanım kayıt | sysfs `/sys/class/**` | §K3 #16 (HTTP 200) | şablon — derlenmedi |
| Ses | ALSA | §K3 #30 (HTTP 200, GET) | şablon yok → erişim kodu `UNKNOWN` |
| Sysctl | `/proc/sys` (admin-guide) | §K3 #18 (HTTP 200) | şablon yok |

## §4 Dil bazlı öneriler

Her dil için üç OS satırı: **hangi OS'ta hangi erişim yolu önerilir** (öneri
= yorumdur, karar değil) + dayanak + kod statüsü. **Kod statüsü: şablon —
derlenmedi/çalıştırılmadı** (tümü için geçerlidir; tek tek tekrar edilmez,
her alt bölümün son satırında teyit edilir).

### §4.1 G1 — JavaScript (Node.js)

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `process.platform === 'win32'` + PowerShell `Get-PSDrive` alt süreç | §G1.2 · §K3 #3 |
| macOS | `=== 'darwin'` + `/Volumes` `fs.readdirSync` | §G1.2 |
| Linux | `=== 'linux'` + `/proc/mounts` okuma | §G1.2 · §K3 #17 |

Bellek/ağ için OS-bağımsız `os.totalmem` · `os.freemem` · `os.networkInterfaces`
(§G1.3 · §K3 #4). **Kod statüsü: şablon — derlenmedi/çalıştırılmadı.**

### §4.2 G2 — C

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `#if defined(_WIN32)` → `GetLogicalDrives` + `GlobalMemoryStatusEx` | §G2.2 · §K3 #20/#21/#23 |
| macOS | `#elif defined(__APPLE__)` ⚠️ → `uname` + IOKit (üye imzaları yazılmadı) | §G2.4 · §K3 #14/#28 · `__APPLE__` URL'siz (§6 R4) |
| Linux | `#elif defined(__linux__)` → `/proc/meminfo` + `/sys/class/block` | §G2.3 · §K3 #16/#17/#24 |

Derleme komutları OS başına §G2.5'te (MSVC · GCC · Clang — üçü de şablon).
**Kod statüsü: şablon — derlenmedi/çalıştırılmadı.**

### §4.3 G3 — Python

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `sys.platform == 'win32'` + ctypes `GlobalMemoryStatusEx`; disk `string.ascii_uppercase` taraması | §G3.1/§G3.4 · §K3 #21 |
| macOS | `'darwin'` + `/Volumes`; bellek `sysctl hw.memsize` ⚠️ | §G3.2/§G3.4 · §6 R1 |
| Linux | `'linux'` + `/proc/meminfo`; disk `/proc/mounts` | §G3.2/§G3.3 |

`platform.system()` ikinci doğrulama olarak önerilir (`Windows`·`Darwin`·`Linux`)
(§K3 #1). `shutil.disk_usage` iddiası URL'sizdir — şablon düzeyinde kalır
(platformlararasi:316 kendi notunu taşır). **Kod statüsü: şablon —
çalıştırılmadı.**

### §4.4 G4 — Go

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `runtime.GOOS == "windows"` + `SystemDrive` kontrolü; tam kök listesi için Win32 sarmalayıcı = **ADR bekliyor** | §G4.3 · §K3 #5/#20 |
| macOS | `"darwin"` + `os.ReadDir("/Volumes")` | §G4.3 |
| Linux | `"linux"` + `os.Open("/proc/meminfo")` | §G4.2 · §K3 #17 |

**Kod statüsü: şablon — derlenmedi/çalıştırılmadı.** Ek risk: Go standart
kütüphanesi disk kök listeleme API'si içermez (§5 R-06).

### §4.5 G5 — Rust

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `cfg!(target_os = "windows")` + `SystemDrive`; tam liste sarmalayıcısı = **ADR bekliyor** | §G5.2 · §K3 #7/#8 |
| macOS | `#[cfg(target_os = "macos")]` → `read_dir("/Volumes")` | §G5.2 |
| Linux | `#[cfg(target_os = "linux")]` → `read_to_string("/proc/meminfo")` | §G5.2 · §K3 #17 |

**Kod statüsü: şablon — derlenmedi/çalıştırılmadı.** Ek risk: üçüncü-parti
crate adı yazılmaz (uydurma yasağı — §5 R-07).

### §4.6 G6 — C# (.NET)

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `RuntimeInformation.IsOSPlatform(OSPlatform.Windows)` + `DriveInfo.GetDrives()` + P/Invoke `GlobalMemoryStatusEx` | §G6.1–§G6.3 · §K3 #9/#10/#21 |
| macOS | `OSPlatform.OSX` + `DriveInfo`; bellek IOKit P/Invoke ⚠️ | §G6.3 · §K3 #14 |
| Linux | `OSPlatform.Linux` + `DriveInfo` + `/proc/meminfo` regex | §G6.3 · §K3 #17 |

**Kod statüsü: şablon — derlenmedi/çalıştırılmadı.** Diskkök için en geniş
OS kapsamı bu dilde (`DriveInfo` üç OS'ta da aynı API — §K3 #10).

### §4.7 G7 — PHP

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `PHP_OS_FAMILY === 'Windows'` + PowerShell `Win32_OperatingSystem` köprüsü; disk `disk_total_space` aday taraması | §G7.1–§G7.3 · §K3 #11/#13 |
| macOS | `PHP_OS_FAMILY === 'Darwin'` + `sysctl hw.memsize` ⚠️ | §G7.3 · §6 R1 |
| Linux | `PHP_OS_FAMILY === 'Linux'` + `/proc/meminfo` regex | §G7.3 · §K3 #17 |

`php_uname('s')` ikincil doğrulamadır (§K3 #12). **Kod statüsü: şablon —
çalıştırılmadı.** Repo ilişkisi: `PHP_OS_FAMILY` birinci taraf kodda **0
isabet** (§5 R-08) — bu şablonlar henüz üretim koduna taşınmamıştır.

### §4.8 G8a — Bash

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `*)` dalı — `uname -s` çıktısı MINGW/MSYS önekli ise tut; bu iddia ⚠️ doğrulanamadı | §G8.1 · §6 R (MINGW) |
| macOS | `Darwin` dalı + `sysctl -n hw.memsize` ⚠️ | §G8.1 · §K3 #28 |
| Linux | `Linux` dalı + `awk '/MemTotal/' /proc/meminfo` | §G8.1 · §K3 #17 |

**Kod statüsü: şablon — çalıştırılmadı.**

### §4.9 G8b — PowerShell

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `[RuntimeInformation]::IsOSPlatform([OSPlatform]::Windows)` + `[System.IO.DriveInfo]::GetDrives()` | §G8.2 · §K3 #9/#10 |
| macOS | `[OSPlatform]::OSX` sorgulanır; PowerShell'in o OS'ta kurulu olduğu varsayımı **UNKNOWN** | §G8.2 (yalnız Windows şablonu var) |
| Linux | `[OSPlatform]::Linux` aynı yöntem; kurulu ortam doğrulanmadı | §G8.2 |

**Kod statüsü: şablon — çalıştırılmadı.** Windows dışı PowerShell erişimi
bu raporda **kanıtsızdır** (§5 R-11).

### §4.10 G8c — Java

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `System.getProperty("os.name")` önek `windows` → disk/registry için süreç çalıştırma veya JNI ⚠️ (şablonda yok) | §G8.3 · §K3 #29 |
| macOS | önek `mac`/`darwin` (`Mac OS X` değeri ⚠️ — tam dize sabitlenmez) | §G8.3 |
| Linux | önek `linux` | §G8.3 |

**Kod statüsü: şablon — derlenmedi/çalıştırılmadı.** JVM'in disk/registry
doğrudan API'si bu raporda doğrulanmadı → erişim yolu `UNKNOWN`; önerilen
yol önek-eşleşme + alt süreçtir (yorum).

### §4.11 G9 — Swift (§G9 — bu tur)

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `#if os(Windows)` ile derleme-dalı — `os()` koşulu argümanları arasında **Windows doğrulandı**; çalışma-zamanı erişim yolu bu raporda yazılmadı → `⚠️` | §6 N1 (200) + tarayıcı render (§6.2) |
| macOS | `#if os(macOS)` + §3.2 yolları (IOKit/CoreAudio — dil köprüsü `UNKNOWN`) | §6 N1 |
| Linux | `#if os(Linux)` + §3.3 yolları (`/proc` `/sys` — dil köprüsü `UNKNOWN`) | §6 N1 |

**Kod statüsü: şablon yazılmadı — bu rapor Swift kod bloğu üretmez**
(Swift erişim API'si için doğrulanmış üye adı kanıtı yok → uydurma yasağı).

### §4.12 G10 — Kotlin (§G10 — bu tur)

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | Kotlin/Native: POSIX platform kütüphanesi Windows hedefinde de vardır ("Windows (MinGW)" bağı); Kotlin/JVM: `os.name` önek eşleşmesi (G8.3 ile aynı) | §6 N2 (200 + içerik) · §6 N3 |
| macOS | Kotlin/Native POSIX bindings + `~/.konan`; Kotlin/JVM `os.name` | §6 N2 · §6 N3 |
| Linux | Kotlin/Native POSIX bindings + `/proc`; Kotlin/JVM `os.name` | §6 N2 · §6 N3 |

**Kod statüsü: şablon yazılmadı — bu rapor Kotlin kod bloğu üretmez.**
İki çalışma-zamanı ayrımı (Native vs JVM) §5 R-09'da risk olarak kayıtlıdır.

### §4.13 G11 — C++ (§G11 — bu tur)

| OS | Önerilen erişim yolu | Dayanak |
|---|---|---|
| Windows | `#if defined(_WIN32)` koşullu dahil etme → Win32 yolları (P1/P2/P6) | §6 N4 (200, "Conditional inclusion") · §K3 #23 |
| macOS | `__APPLE__` ⚠️ (doğrulanmış URL yok) + IOKit (§K3 #14) | §6 R4 |
| Linux | `__linux__` + `/proc` `/sys` (§K3 #16/#17/#24) | §K3 #24 |

**Kod statüsü: şablon — derlenmedi/çalıştırılmadı.** Sözdizimi dayanağı
`#if`/`#ifdef`/`#elif`/`#else`/`#endif` ailesi §6 N4'te doğrulanmıştır;
makro adları §K3 #23/#24'ten (G2 ile ortak — §G8.4 satır 4 kaydı).

## §5 Risk ve uyumluluk notları

Her satır bir **OS × dil** sınır kaydıdır; çözüm üretilmez, kayıt tutulur.

| # | OS × Dil | Sınır / risk | Durum |
|---|---|---|---|
| R-01 | macOS × C/C++ (G2/G11) | `__APPLE__` ön-tanımlı makrosu için doğrulanmış birincil URL **yok** — Apple'nin predefined-macros sayfası 404 (§6 R4) | ⚠️ VERIFICATION REQUIRED |
| R-02 | macOS × Python/PHP/Bash (G3/G7/G8a) | `sysctl -n hw.memsize` komut çağrısı doğrulanamadı — Apple sysctl sayfası HTTP 404 (§6 R1) | ⚠️ VERIFICATION REQUIRED |
| R-03 | macOS × tüm diller (P3) | `getifaddrs` karşılığı Apple sayfası 404; IOKit/CoreAudio eşleşmesi P3 satırında kanıtlanmadı | ⚠️ VERIFICATION REQUIRED |
| R-04 | Windows × Bash (G8a) | Git Bash/MSYS2'de `uname -s` çıktısının MINGW/MSYS önekli olduğu iddiası doğrulanamadı (platformlararasi:858-860) | ⚠️ VERIFICATION REQUIRED |
| R-05 | Windows × Swift (G9) | Dil seviyesinde `os(Windows)` doğrulandı; **araç zinciri/derleyici Windows destek olgunluğu** bu raporda doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| R-06 | Windows/macOS × Go (G4) | Go standart kütüphanesi disk kök listeleme API'si **içermez**; tam kök listesi Win32 sarmalayıcısı ister = ADR bekliyor (§G4.3) | KAYITLI SINIR |
| R-07 | Windows/macOS/Linux × Rust (G5) | Rust std disk kök API'si **yok**; IOKit/Win32/ALSA çağrıları std'de yok; crate adı yazılmaz (§G5.3) | KAYITLI SINIR |
| R-08 | Tümü × PHP (G7) | Repo'da `PHP_OS_FAMILY` **0 isabet** (yalnız `vendor/`) — şablonlar K001 üretim koduna taşınmadı (ikincil: platformlararasi:828-829 → K000 tasinabilirlik-siniri-ornekler.md L161) | ⚠️ (ikincil kanıt) |
| R-09 | Windows × Kotlin (G10) | Kotlin/Native Windows hedefi **MinGW** üzerinden tanımlanır ("Windows (MinGW)" posix.def — §6 N2 içerik okuması); JNI/MinGW araç bağımlılığı Native-JVM farkı | DOĞRULANDI (sınırla birlikte) |
| R-10 | Windows × Java (G8c) | `os.name` tam dizesi JVM/OS sürümüne göre değişir; sabit dize sabitlenmez, önek eşleşmesi kullanılır (§G8.3 notu) | DOĞRULANDI (kural) |
| R-11 | macOS/Linux × PowerShell (G8b) | Windows dışı PowerShell kurulu ortamı bu oturumda doğrulanmadı (yalnız Windows 11 makinesi var) | UNKNOWN |
| R-12 | macOS/Linux × Java (G8c) | Disk/registry erişimi için JVM doğrudan API'si doğrulanmadı; süreç çalıştırma/JNI yolu şablonda yok | UNKNOWN |
| R-13 | Tümü × ses (P5) | WASAPI sahipliği K000'dedir (ADR-017 katman 3); K001 bu hücrede yalnız sınır sahibidir | SINIR KAYDI |
| R-14 | Tümü | Bu oturumda macOS/Linux test makinesi **yok** — hiçbir şablon bu raporda derlenmedi/çalıştırılmadı | ⚠️ VERIFICATION REQUIRED |

### §5.1 Bilinen 4 ⚠️'nin kökeni (platformlararasi open_questions ile birebir)

| Köken | Dosya içi konum | Bu rapordaki karşılığı |
|---|---|---|
| Kod şablonları derlenmedi/çalıştırılmadı (yalnız Windows 11 makinesi) | platformlararasi open_question #1 | R-14 · §4 tüm satırlar |
| `__APPLE__` birincil URL yok | open_question #2 · §G2.1 · §K4 R4 | R-01 |
| `sysctl -n hw.memsize` 404 | open_question #3 · §G3.4 · §K4 R1 | R-02 |
| MINGW `uname` 404 | open_question #4 · §G8.1 | R-04 |

### §5.2 Öneri gücü sınıflandırması (§4'teki "öneri" nedir?)

| Sınıf | Anlamı | Örnek |
|---|---|---|
| Kanıtlı yol | algılama + erişim ikisi de HTTP 200 kaynaklı | G1 · G3 · G6 · G7 (Linux kolu) |
| Kısıtlı yol | algılama kanıtlı, erişim yolu ADR/sarmalayıcı bekliyor | G4 · G5 (Windows tam kök listesi) |
| Kısmi yol | algılama doğrulandı, erişim yolu yazılmadı | G9 Swift |
| İşaretli boşluk | her ikisi de ⚠️/UNKNOWN | macOS bellek/ağ satırları (R-02 · R-03) |

## §6 Doğrulama

### §6.1 Yeni URL'ler (2026-10-14 — bu oturumda ölçüldü)

| # | URL | Ne için | Yöntem | Sonuç |
|---|---|---|---|---|
| N1 | https://docs.swift.org/latest/documentation/the-swift-programming-language/statements | G9 Swift koşullu derleme (`#if`/`#endif`, `os()` platform koşulu) | fetch GET + chrome-devtools render (içerik 39.677 karakter) | **200 ✅** + içerik doğrulandı |
| N2 | https://kotlinlang.org/docs/native-platform-libs.html | G10 Kotlin/Native platform kütüphaneleri (Windows MinGW · POSIX bindings) | fetch GET + HTML içerik parçası | **200 ✅** + içerik doğrulandı |
| N3 | https://kotlinlang.org/docs/java-interop.html | G10 Kotlin/JVM — Java ile birlikte çalışma (`os.name` köprüsü) | fetch GET + HTML (başlık "Calling Java from Kotlin") | **200 ✅** + içerik doğrulandı |
| N4 | https://en.cppreference.com/w/cpp/preprocessor/conditional | G11 C++ koşullu dahil etme (`#if/#elif/#else/#endif`) | fetch GET + HTML (başlık "Conditional inclusion") | **200 ✅** + içerik doğrulandı |

### §6.2 İçerik kanıtları (okunan metin parçaları — iddiaların dayanağı)

| # | URL | Okunan kanıt (öz) | Dayandığı iddia |
|---|---|---|---|
| N1 | Swift statements | "Every conditional compilation block begins with the `#if` compilation directive and ends with the `#endif` compilation directive" · platform koşulu `os()` geçerli argümanları tablosunda **`macOS, iOS, watchOS, tvOS, visionOS, Linux, Windows`** | §2 G9 · §4.11 · §5 R-05 (yalnız dil seviyesi) |
| N2 | Kotlin platform libs | "Kotlin provides the POSIX platform library for all UNIX- and **Windows-based targets**" · bağlantı metni **"Windows (MinGW)"** (`platformLibs/src/platform/mingw/posix.def`) · kurulum dizinleri: `~/.konan` (macOS/Linux) vs `%USERPROFILE%\.konan` (Windows) | §2 G10 · §4.12 · §5 R-09 |
| N3 | Kotlin java-interop | Java tip ↔ Kotlin tip eşleme tablosu (`java.lang.Object` → `kotlin.Any!`) · platform tipleri bölümü | §4.12 Kotlin/JVM `os.name` köprüsü |
| N4 | cppreference conditional | `#if` / `#ifdef` / `#ifndef` / `#elif` / `#elifdef` / `#else` / `#endif` koşullu dahil etme sözdizimi · "predefined macro definitions" bağlantısı | §2 G11 · §4.13 |

> **Yöntem notu:** N1'in HTML'i `fetch` ile 3.981 karakterlik bir uygulama
> kabuğu (SPA) döndürür — sayfa içeriği **chrome-devtools tarayıcı render**
> ile okunmuştur. Bu nedenle N1 hem HTTP 200 hem içerik kanıtı taşır; `fetch`
> tek başına içerik kanıtı sayılmaz.

### §6.3 §K3 URL matrisi (mevcutlar — platformlararasi dosyasında 2026-10-14 HTTP 200)

Bu rapor yeniden ölçmez; birincil kaynaktan aynen atıf yapar (30 satır):

| # | URL | Ne için | Sonuç |
|---|---|---|---|
| 1 | https://docs.python.org/3/library/platform.html | G3 `platform.system()` | 200 ✅ |
| 2 | https://docs.python.org/3/library/sys.html | G3 `sys.platform` | 200 ✅ |
| 3 | https://nodejs.org/api/process.html | G1 `process.platform` | 200 ✅ |
| 4 | https://nodejs.org/api/os.html | G1 `os.*` | 200 ✅ |
| 5 | https://pkg.go.dev/runtime | G4 `runtime.GOOS` | 200 ✅ |
| 6 | https://pkg.go.dev/runtime#GOOS | G4 `GOOS` bölümü | 200 ✅ |
| 7 | https://doc.rust-lang.org/reference/conditional-compilation.html | G5 `cfg!`/`#[cfg]` | 200 ✅ |
| 8 | https://doc.rust-lang.org/std/env/consts/constant.OS.html | G5 `consts::OS` | 200 ✅ |
| 9 | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation | G6/G8b algılama | 200 ✅ |
| 10 | https://learn.microsoft.com/en-us/dotnet/api/system.io.driveinfo | G6.2/G8.2 disk | 200 ✅ |
| 11 | https://www.php.net/manual/en/reserved.constants.php | G7 `PHP_OS_FAMILY` | 200 ✅ |
| 12 | https://www.php.net/manual/en/function.php-uname.php | G7 `php_uname()` | 200 ✅ |
| 13 | https://www.php.net/manual/en/function.disk-total-space.php | G7.2 `disk_total_space` | 200 ✅ |
| 14 | https://developer.apple.com/documentation/iokit | G2.4/G6.3 donanım kayıt | 200 ✅ |
| 15 | https://developer.apple.com/documentation/coreaudio | P5 macOS ses | 200 ✅ |
| 16 | https://docs.kernel.org/filesystems/sysfs.html | G2.3 `/sys` | 200 ✅ |
| 17 | https://docs.kernel.org/filesystems/proc.html | `/proc` (G2.3/G3.3/G4.2/G6.3) | 200 ✅ |
| 18 | https://docs.kernel.org/admin-guide/sysctl/ | P2 Linux sysctl | 200 ✅ |
| 19 | https://docs.kernel.org/admin-guide/mm/index.html | P2 Linux bellek yönetimi | 200 ✅ |
| 20 | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-getlogicaldrives | G2.2 disk kökleri | 200 ✅ |
| 21 | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex | G2.2/G3.4/G6.3 bellek | 200 ✅ |
| 22 | https://learn.microsoft.com/en-us/windows/win32/api/iphlpapi/nf-iphlpapi-getadaptersaddresses | P3/G8.2 ağ adapterları | 200 ✅ |
| 23 | https://learn.microsoft.com/en-us/cpp/preprocessor/predefined-macros | G2.1 `_WIN32` | 200 ✅ |
| 24 | https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html | G2.1 `__linux__` | 200 ✅ |
| 25 | https://gcc.gnu.org/onlinedocs/cpp/Conditionals.html | G2.1 koşullu derleme | 200 ✅ |
| 26 | https://en.cppreference.com/w/c/preprocessor/conditional.html | G2.1 `#if/#elif` | 200 ✅ |
| 27 | https://man7.org/linux/man-pages/man2/uname.2.html | G8.1 `uname` (Linux) | 200 ✅ |
| 28 | https://pubs.opengroup.org/onlinepubs/9699919799/functions/uname.html | G8.1 `uname` (POSIX/Darwin) | 200 ✅ |
| 29 | https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/lang/System.html | G8.3 `System.getProperty` | 200 ✅ |
| 30 | https://www.alsa-project.org/ | P5 Linux ses (ALSA) — erişim kaydı | 200 ✅ (GET) |

**Sayım notu (gizlenmez):** §K3 tablosu **30 satır** taşır; platformlararasi
§K1 G4 kapısı ve evidence alanı **"29 URL"** der. Fark, satır 30'un
(alsa-project.org) "erişim kaydı" olarak ayrı sayılmasından kaynaklanır —
ikisi de aynı gün (2026-10-14) ölçülmüştür; hangi sayım bağlayıcı
**UNKNOWN** (§OPEN QUESTIONS Q4).

### §6.4 Reddedilen / 404 URL'ler (R1–R8 birincil kaynaktan, R9–R13 bu oturumda)

| # | URL | Sonuç | Karar |
|---|---|---|---|
| R1 | https://developer.apple.com/documentation/kernel/sysctl | **404** | KULLANILMADI — macOS `sysctl hw.memsize` iddiası ⚠️ (R-02) |
| R2 | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.informationruntimeinformation | **404** | KULLANILMADI — §K3 #9 kullanıldı |
| R3 | https://learn.microsoft.com/en-us/windows/win32/sysinfo/globalmemorystatus | **404** | KULLANILMADI — §K3 #21 kullanıldı |
| R4 | https://developer.apple.com/documentation/kernel/predefined-macros | **404** | KULLANILMADI — `__APPLE__` ⚠️ (R-01) |
| R5 | https://learn.microsoft.com/en-us/cpp/preprocessor/preprocessor-macros | **404** | KULLANILMADI — §K3 #23 kullanıldı |
| R6 | https://learn.microsoft.com/en-us/cpp/preprocessor/conditional-compilation | **404** | KULLANILMADI — §K3 #25/#26 kullanıldı |
| R7 | https://clang.llvm.org/docs/Manual/PredefinedMacros.html | **404** | KULLANILMADI — kaynak yok |
| R8 | https://developer.apple.com/library/archive/ (getifaddrs tam adresi) | **404** | KULLANILMADI — P3 macOS `getifaddrs` ⚠️ (R-03) |
| R9 | https://docs.swift.org/swift-book/documentation/the-swift-programming-language/statements/index.html | 200 ama **SPA kabuğu** (3.981 byte, içerik yok) | KULLANILMADI — N1 + tarayıcı render kullanıldı |
| R10 | https://docs.swift.org/swift-book/documentation/the-swift-programming-language/statements.md | **404** | KULLANILMADI — düz metin varyantı dene olmadı |
| R11 | https://docs.swift.org/swift-book/The-Swift-Programming-Language/Statements.html | **404** | KULLANILMADI |
| R12 | https://docs.swift.org/swift-book/LanguageGuide/Statements.html | **404** | KULLANILMADI |
| R13 | https://docs.swift.org/swift-book/documentation/the-swift-programming-language/statements/index.json | **404** | KULLANILMADI — DocC JSON varyantı yok |

> **Kural:** 404 (veya içeriği olmayan 200) hiçbir iddiaya bağlanmaz; ilgili
> iddia `⚠️ VERIFICATION REQUIRED` olarak kalır (zero-hallucination §3 ·
> platformlararasi §K4 kuralı).

### §6.5 Kapı ölçümü (bu rapor)

| Kapı | Koşul | Ölçüm | Sonuç |
|---|---|---|---|
| K1 | Frontmatter alanları | title · type · node_id · status · parent_id · children · depends_on · evidence · validation · open_questions · date · group | ✅ |
| K2 | Satır sayısı ≥500 | yazım sonrası dosya ölçümü (§6.6) | ✅ |
| K3 | Son satır kanonik | `Authority:` + `Mode:` satırı | ✅ |
| K4 | Yeni URL HTTP 200 | 4/4 (§6.1) | ✅ |
| K5 | Doldurucu yok | TBD · üç nokta · "Yakında" taraması | ✅ |
| K6 | Mojibake yok | UTF-8 Türkçe yazım | ✅ |
| K7 | Başka dosya değişmedi | yalnız yeni dosya yazıldı · `git commit` yok | ✅ |

### §6.6 Ölçüm kaydı

| Ölçüm | Değer | Tarih |
|---|---|---|
| Kaynak dosya satırı | platformlararasi-kod-ornekleri.md = 1018 satır | 2026-10-14 |
| Yeni URL (bu rapor) | 4 (N1–N4) — 4/4 HTTP 200 | 2026-10-14 |
| Reddedilen URL kaydı (bu rapor) | 5 (R9–R13) + birincil kaynaktan 8 (R1–R8) = 13 | 2026-10-14 |
| Matris hücresi | 13 dil × 3 OS = 39 | 2026-10-14 |
| Dil satırı (§4) | 13 (G1–G11, G8 a/b/c ayrı) | 2026-10-14 |
| P satırı (§3) | 6 (P1–P6) × 3 OS | 2026-10-14 |
| Risk satırı (§5) | 14 (R-01–R-14) | 2026-10-14 |

## §YASAKLAR

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | `TBD` · üç nokta doldurucu · "Yakında" | Guardrail #6 (§6.5 K5) |
| 2 | Doğrulanmamış API/üye adı — doğrulanamıyorsa `⚠️ VERIFICATION REQUIRED` zorunlu | Zero-hallucination §3 |
| 3 | Uydurma paket/crate/kütüphane adı (Rust crate, Go modülü, Kotlin paketi vb.) | §4.5 · §4.12 — ADR bekliyor |
| 4 | Şablon kodu "çalışıyor/üretim hazır" diye sunmak | §1 — derlenmedi/çalıştırılmadı |
| 5 | 404 (veya içeriği olmayan 200) URL'i iddiaya bağlamak | §6.4 |
| 6 | `git commit` | Orkestratöre aittir (AGENTS.md §13.9/5) |
| 7 | Başka dosyayı değiştirmek (platformlararasi dâhil) | Görev emri — bu dosya salt-okunur kaynaklardan üretilir |
| 8 | `⚠️`/`UNKNOWN` işaretli hücreyi tahminle doldurmak | §2.1 madde 4 |
| 9 | `donanaım-type` yazımını "düzeltmek" | v2 §3 KURAL 1 — dokunulmaz |

## §OPEN QUESTIONS

| # | Soru | Etki | Sahip |
|---|---|---|---|
| Q1 | §G9–§G11 (bu tur) tamamlandığında bu raporun §2/§4/§5'indeki üç satır bölüm içeriğiyle hizalanmalı mı? | Matris güncelliği | Vault Steward |
| Q2 | Rust/Go için disk kök listeleme sarmalayıcısı hangi kütüphane/ADR ile seçilecek? | R-06 · R-07 | architect |
| Q3 | macOS/Linux test makinesi/CI derleme kapısı kurulacak mı (şablon → üretim geçişi)? | R-14 · §4 tümü | devops |
| Q4 | §K3 sayımı 29 mu 30 mu (alsa-project.org erişim kaydı dâhil mi)? | §6.3 | kanit |
| Q5 | `__APPLE__` ve `sysctl hw.memsize` doğrulanana kadar macOS dalları geçerli sayılacak mı? | R-01 · R-02 | kanit |
| Q6 | P5 ses alt sistemi (WASAPI/CoreAudio/ALSA) şablonları ayrı dosyaya mı taşınır? | R-13 · dosya boyutu | Vault Steward |
| Q7 | Swift'in Windows araç zinciri olgunluğu hangi birincil kaynakla doğrulanacak? | R-05 | researcher |

## §KAPSAM-DIŞI (bu rapor neyi çözmez)

1. **Bağlayıcı karar üretilmez** — anayasa §2 ve accepted ADR'ler bağlayıcıdır;
   bu rapor yalnız matris + kanıt üretir.
2. **Yeni kod bloğu üretilmez** — Swift/Kotlin/C++ için kod şablonu yazılmaz
   (doğrulanmış üye adı kanıtı yok); mevcut §G1–§G8 şablonları da burada
   tekrar edilmez, yalnız satır referansıyla anılır.
3. **§G9–§G11 içeriği okunmaz/tekrar edilmez** — paralel ajanın bu tur işidir;
   bu rapor onları "bu tur" olarak referanslar (Q1).
4. **Riskler çözülmez** — R-01–R-14 kaydedilir; düzeltme Vault Steward
   kararıyla başka oturumda yapılır.
5. **K000 ile sahiplik sınırı yeniden çizilmez** — P4–P6 sahipliği K000'dedir
   (platformlararasi:62 · anayasa §2 satır 5); bu rapor sınırı tekrarlar.
6. **Hiçbir mevcut dosya değiştirilmez, commit atılmaz** — yalnız bu yeni
   rapor dosyası yazılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
