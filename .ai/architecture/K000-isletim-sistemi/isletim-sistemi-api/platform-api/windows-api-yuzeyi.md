---
node_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api/windows-api-yuzeyi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/platform-api
children: []
depends_on: []
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/api/objbase/
  - https://learn.microsoft.com/en-us/windows/win32/api/combaseapi/
  - https://learn.microsoft.com/en-us/windows/win32/com/coinitializeex
  - https://learn.microsoft.com/en-us/windows/win32/dlls/dynamic-link-library-functions
  - https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-getprocaddress
  - https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibrarya
  - https://learn.microsoft.com/en-us/windows/uwp/cpp-and-winrt-apis/
  - https://learn.microsoft.com/en-us/uwp/api/
  - https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror
validation: "2026-10-13 — 9 resmi URL HTTP 200 + envanter kesişimi 0"
open_questions: []
group: mimari
---

# windows-api-yuzeyi

**ELI15:** Windows'ta uygulamanın işletim sistemine baktığı yüz, Win32
başlıklarının (`windows.h` ailesi) ve COM mekanizmasının oluşturduğu API
yüzeyidir. L0 (Neva Engine) Windows hedefi için bu dosya başlık, DLL/ABI ve
sürüm sınırlarını belgeler.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api/windows-api-yuzeyi |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/platform-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §A — Win32 başlık yüzeyi (windows.h · COM)

### A.1 Win32 nedir

Win32, Windows işletim sisteminin uygulama programlama arayüzüdür (application
programming interface). C/C++ uygulamaları `windows.h` başlığını dahil ederek
süreç, thread, bellek, dosya ve cihaz API'lerine erişir.

| Başlık | Ne sağlar |
|---|---|
| `windows.h` | Ana çatı başlık — Win32 API'nin çoğu bu üzerinden gelir |
| `winbase.h` | Süreç/thread/bellek yönetim API'leri (`windows.h` tarafından dahil edilir) |
| `winsock2.h` | Soket API'si (bkz. `ag-api/posix-socket-ve-winsock-karsilastirmasi.md`) |
| `mmsystem.h` | Multimedya zamanlama (`timeBeginPeriod` · `timeGetTime`) |
| `objbase.h` | COM temel mekanizmaları — `CoInitializeEx` bu başlıktan gelir |

### A.2 COM — Component Object Model özeti

COM, Windows'ta bileşenler arası dil-bağımsız arayüz protokolüdür. C++'da
arayüz = saf sanal sınıf; her COM nesnesi `IUnknown` türevlidir.

| `IUnknown` metodu | Görev |
|---|---|
| `QueryInterface` | Arayüz sorgusu — istenen arayüz alınır veya `E_NOINTERFACE` |
| `AddRef` | Referans sayısını artırır |
| `Release` | Referans sayısını azaltır — 0'a inince nesne yok olur |

### A.3 COM yaşam döngüsü — `CoInitializeEx`

| Parametre | Değer | Anlam |
|---|---|---|
| `pvReserved` | `NULL` | Rezerve — daima `NULL` |
| `dwCoInit` | `COINIT_MULTITHREADED` | MTA — COM kendi thread havuzunu yönetir (önerilen) |
| `dwCoInit` | `COINIT_APARTMENTTHREADED` | STA — tek thread modeli; UI/COM nesnesi eşzamanlılığı |
| Dönüş | `S_OK` | İlk başlatma başarılı |
| Dönüş | `S_FALSE` | Zaten başlatılmış — `CoUninitialize()` sayısıyla dengelenir |
| Dönüş | `RPC_E_CHANGED_MODE` | Farklı modelle zaten başlatılmış — programlama hatası |

> Kural: her başarılı `CoInitializeEx` çağrısına karşılık süreç kapanışında
> **bir** `CoUninitialize()` çağrılır — denge zorunlu.

### A.4 Win32 hata modeli

| Kavram | Ayrıntı |
|---|---|
| `GetLastError` | Son Win32 çağrısının hata kodunu (32-bit) döndürür |
| `HRESULT` | COM/çok-parçalı API'lerde genel hata dönüşü (`S_OK` · `E_FAIL` · `E_NOINTERFACE`) |
| `FormatMessage` | Hata kodunu metne çevirir |
| `SetLastError` | Kütüphaneler kendi hatalarını aynı mekanizmaya yazar |

| `HRESULT` makrosu | Anlam |
|---|---|
| `SUCCEEDED(hr)` | `hr` ≥ 0 — başarı |
| `FAILED(hr)` | `hr` < 0 — hata |
| `S_OK` | 0 — başarı |
| `E_FAIL` | Genel hata |
| `E_NOINTERFACE` | `QueryInterface` başarısız |

### A.5 Win32 zamanlama API'leri

| API | Kullanım | Not |
|---|---|---|
| `GetTickCount64` | Milisaniye sayaç (64-bit) | Uzun süreli zamanlama |
| `QueryPerformanceCounter` | Yüksek çözünürlüklü sayaç | Ses DSP ölçümü için aday — `queryPerformanceFrequency` ile ölçeklenir |
| `timeBeginPeriod` | Sistem çözünürlüğünü artırır | **Sistem genelinde** etki — dikkatli kullanılır |

> `timeBeginPeriod` çağrısı sistem zamanlayıcısının çözünürlüğünü globally
> değiştirir — güç tüketimi ve diğer süreçler üzerindeki etkisi nedeniyle
> yalnızca gereken süre boyunca açık tutulur (Microsoft dokümantasyonu §E.9).

### A.6 Win32 başlık yüzeyi — CoreMusic L0 bağlamı

| L0 ihtiyacı | Win32 karşılığı | Not |
|---|---|---|
| Yüksek çözünürlüklü sayaç | `QueryPerformanceCounter` | DSP ölçüm zinciri (sartname 48 kHz) |
| Thread önceliği | `SetThreadPriority` · `AvSetMmThreadCharacteristics` | RT audio thread — ayrı dosya (`isletim-sistemi-audio` Q-P2 sınırı) |
| Ağ | Winsock2 | `ag-api` alt-dalı |
| Bellek kilitleme | `VirtualLock` | Sıfır tahsis kuralı doğrulaması |

### A.7 Win32 çoklu iş parçacığı yüzeyi (tanım düzeyi)

| API | Görev | Not |
|---|---|---|
| `CreateThread` | İş parçacığı başlatır | CRT başlatıcısı `beginthreadex` önerilir (CRT güvenliği) |
| `WaitForSingleObject` | Nesne beklemesi | Event · mutex · process handle |
| `CreateEvent` / `SetEvent` | Olay sinyalizasyonu | IOCP ile birlikte |
| `InterlockedIncrement` | Atomik artırma | Lock-free sayaç |
| `QueryPerformanceCounter` | Yüksek çözünürlüklü sayaç | §A.5 |

> İş parçacığı API'lerinin RT ses ile ilişkisi `isletim-sistemi-audio` dalının
> konusudur (Q-P2) — burada yalnız yüzey tanımı verilir.

### A.8 Win32 başlık include sırası — derleme notları

| Sıra | Başlık | Not |
|---|---|---|
| 1 | `#define WIN32_LEAN_AND_MEAN` | `windows.h`'nin sık kullanılan alt başlıklarını ayırır (§D.3) |
| 2 | `#define NOMINMAX` | `min`/`max` makroları engellenir — C++ `<algorithm>` çakışması |
| 3 | `#include <windows.h>` | Ana çatı |
| 4 | `#include <winsock2.h>` | `windows.h` **önce** dahil edilirse `winsock.h` ile çakışır — `ag-api` dosyası §C.2 |

### A.9 Karar matrisi — Win32 yüzeyi kullanımı (L0)

| İhtiyaç | Win32 seçimi | Karar durumu |
|---|---|---|
| Yüksek çözünürlüklü sayaç | `QueryPerformanceCounter` | Aday |
| Sistem çözünürlüğü | `timeBeginPeriod` | Kısıtlı — sistem genel etki (§A.5) |
| COM kullanımı | Gerekli değil (şimdilik) | `CoInitializeEx` yalnız COM nesnesi gerekiyorsa |
| Ağ | Winsock2 (§A.1 alt satır) | `ag-api` dosyalarında karar verildi |
| Ses cihazı | Core Audio başlıkları | Q-P2 — sınır açık |

## §B — WinRT/Media

### B.1 Win32 ↔ WinRT

| Kavram | Win32 | WinRT |
|---|---|---|
| Dil | C/C++ (C API) | C++/WinRT (header-only projection), C#, Rust |
| Paketleme | DLL · EXE | MSIX · uygulama paketi |
| Dağıtım | Kurulum · taşıma | Microsoft Store · sideload |
| API yüzeyi | `windows.h` | Windows Runtime namespace'leri (`Windows::Media::*`) |

### B.2 C++/WinRT

| Konu | Ayrıntı |
|---|---|
| Projection | WinRT arayüzleri header-only C++ tiplerine yansıtılır — ayrı çalışma zamanı (runtime) yok |
| Başlık | `winrt/base.h` (Microsoft C++/WinRT) |
| Yaşam döngüsü | RAII sarmalayıcılar — `com_ptr` · `fire_and_forget` coroutine |
| İlişki | WinRT COM tabanlıdır — `IUnknown` mirası korunur |

### B.3 Windows.Media yüzeyi (tanım düzeyi — Q-P2 sınırı)

| Namespace | Ne içerir | Bu dosyada kapsam |
|---|---|---|
| `Windows.Media.Audio` | Ses efektleri · graf · capture (WinRT ses API'si) | Yalnız tanım — davranışsal tasarım `isletim-sistemi-audio` dalında (Q-P2) |
| `Windows.Media.Devices` | Cihaz yönetimi | Aynı |
| `Windows.Media.MediaProperties` | Kodlayıcı/codec özellikleri | Aynı |

> ⚠️ Q-P2 — platform-api `index.md` uyarısı geçerlidir: bu satırlar API
> varlığını belgeler; akış tasarımı burada yapılmaz.

### B.4 WinRT vs Core Audio seçimi

| Ölçüt | Win32 Core Audio | WinRT Media |
|---|---|---|
| Gecikme kontrolü | Doğrudan · ince ayar | Kısıtlı · politika üstünde |
| Dağıtım | Masaüstü uygulaması | Modern paketli uygulama |
| L0 (Neva Engine) hedefi | Uygun — masaüstü ses motoru | Kapsam dışı (şimdilik) |
| Dil | C++ | C++/WinRT projection gerekir |

### B.5 WinRT yüzeyi — sınır notları

| Not | Ayrıntı |
|---|---|
| Q-P2 geçerliliği | Windows.Media.* yalnız §B.3 tanımıyla sınırlıdır — akış tasarımı `isletim-sistemi-audio` |
| Sürüm bağımlılığı | WinRT namespace'leri Windows 10 SDK ile gelir — §D.3 `_WIN32_WINNT` kapısı |
| C++/WinRT derleme | Header-only — ABI riski DLL kadar ağır değil, ancak sürüm SDK'ya bağlı |
| CoreMusic kararı | L0 masaüstü hedefi — WinRT medya yüzeyi **planlanmadı** |

## §C — DLL/ABI ve sürümleme

### C.1 DLL — Dynamic-Link Library

| Konu | Kural |
|---|---|
| Paylaşım | Tek DLL kopyası · bellek paylaşımı · farklı dillerden çağrılabilir |
| Bağlantı | Statik (import library) veya dinamik (`LoadLibrary` + `GetProcAddress`) |
| Export | `__declspec(dllexport)` · `.def` dosyası |
| Arama sırası | Uygulama dizini → sistem dizini (yüksek bütünlük süreçlerde değişebilir) |

### C.2 Dinamik yükleme API'leri

| API | Görev |
|---|---|
| `LoadLibraryA` / `LoadLibraryW` | DLL'i yükler — tutamaç (handle) döner |
| `GetProcAddress` | Sembol adresini alır — fonksiyon işaretçisine dönüştürülür |
| `FreeLibrary` | Referansı düşürür — sayaç 0'a inince DLL boşaltılır |
| `GetModuleHandle` | Yüklü DLL tutamağını alır (artırmadan) |

### C.3 ABI — Application Binary Interface

| Öğe | Kural |
|---|---|
| Çağrı düzeni | x64 Windows: RCX/RDX/R8/R9 + yığın; POSIX x64: RDI/RSI/RDX/RCX/R8/R9 — **farklıdır** |
| Yapı hizalaması | 64-bit'te doğal hizalama — `#pragma pack` dikkatle |
| Karakter genişliği | `char` = 8 bit her iki platformda |
| `long` | **Windows x64: 32-bit** · Linux x64: 64-bit — taşınabilirlik tuzağı |
| Temizleme (cleanup) | Windows x64: çağıran temizler (`caller-cleanup`) |

### C.4 DLL sürümleme stratejileri

| Strateji | Açıklama |
|---|---|
| Sürüm kaydı | `VERSIONINFO` kaynağı · dosya sürümü |
| Semver benzeri | Major = kırıcı (ABI), Minor = ek, Patch = düzeltme |
| Yan yana (SxS) | Manifest ile belirli sürümün bağlanması |
| İsimlendirme | Sürüm ekli DLL adı (`foo2.dll`) — çakışma önleme |

### C.5 DLL güvenliği

| Risk | Önlem |
|---|---|
| DLL fixed-planted (DLL sideloading) | Uygulama dizininde beklenmeyen DLL yükleme sırası manipülasyonu |
| Çökme yayılımı | Kırıcı ABI değişikliğinde tüm yükleyiciler etkilenir — sürüm matrisi zorunlu |
| Bilinen DLL (KnownDLLs) | Sistem DLL'leri önceden bağlanır — yer değiştirme engeli |

### C.6 CoreMusic → Windows DLL sınırı (gerçek repo)

| # | Dosya:Satır | Kanıt | Ne gösterir |
|---|---|---|---|
| K1 | `shared/src/Api/Bff/DesktopBff.php:35` | `'platform' => php_uname('s'),` | Uygulama platformu çalışma zamanında sorgular — Win32 yüzeyi PHP'den **dolaylı** |
| K2 | `shared/src/AI/AIEngine.php:295` | `$output = shell_exec($cmd);` | Win32 yüzeyi yerine **süreç çalıştırma** (CreateProcess sarmalayıcısı) kullanır |
| K3 | `shared/src/AI/ToolCalling.php:256` | `$ch = curl_init($url);` | Ağ Winsock yüzeyi yerine `curl` DLL/Kütüphanesi üzerinden |

> Disk taraması: PHP kodunda **FFI kullanımı yok** (PHP 7.4+ FFI API'si
> kullanılmamış). PHP → Win32 doğrudan köprüsü mevcut değildir; mevcut yol
> `shell_exec` + harici süreçtir (K2). PHP → FFI → Win32 köprüsü
> **planlanmamıştır** — L0 C++ motoru ayrı süreç/uzantı olarak
> tasarlanacaktır (Q-L0: L0 entegrasyon biçimi — ⚠️ VERIFICATION REQUIRED).

### C.7 DLL — export/import akış diyagramı

| Adım | Aşama | İçerik |
|---|---|---|
| 1 | Derleme | `__declspec(dllexport)` sembolleri `.def`/export tablosuna yazılır |
| 2 | Bağlayıcı | Import library (`.lib`) üretilir — yükleyici için |
| 3 | Yükleme | `LoadLibrary` (dinamik) veya import tablosu (statik bağlama) |
| 4 | Sembol | `GetProcAddress` ile adres alınır — yoksa `NULL` |
| 5 | Çağrı | Fonksiyon işaretçisi üzerinden çağrılır — ABI kuralları (§C.3) |
| 6 | Boşaltma | `FreeLibrary` — sayaç 0'da DLL bırakılır |

### C.8 ABI denetim listesi — L0 C++ motoru için

| # | Denetim | Neden |
|---|---|---|
| 1 | Çağrı düzeni (calling convention) | `__cdecl` / `__stdcall` / `__fastcall` — Windows'ta farklı |
| 2 | `long` / `size_t` genişliği | Windows x64 `long` = 32-bit (§C.3) |
| 3 | Yapı hizalama | `#pragma pack` farkı — dosya paylaşımında kırılma |
| 4 | Karakter seti | ANSI (`A`) vs Unicode (`W`) API çiftleri — `LoadLibraryA`/`LoadLibraryW` |
| 5 | Sürüm sembolü | Export tablosu değişimi = kırıcı ABI |
| 6 | CRT uyumu | Statik/dinamik CRT karışımı — `malloc`/`free` çifti bozulur |

### C.9 DLL/ABI karar matrisi — L0

| Karar | Seçenek | Durum |
|---|---|---|
| Sarmalama | C++ sınıf ↔ C export sınırı (extern "C") | Öneri — ABI stabilitesi için |
| CRT | Statik CRT (dağıtım kolaylığı) | Aday — Q-L0 |
| Sürümleme | Semver + `VERSIONINFO` | Öneri |
| Dağıtım | Yan yana DLL | Öneri |

## §D — Windows SDK sürüm matrisi

### D.1 Sürüm kavramları

| Katman | Ne belirler | Değişme sıklığı |
|---|---|---|
| Windows sürümü | Hangi API'nin var olduğu (ör. `GetTickCount64` = Vista+) | Yıllık-yarıyıllık |
| Windows SDK sürümü | Başlık/kütüphane sürümü — derleme zamanı | Yıllık (SDK 10.0.xxxxx.0 formatı) |
| Platform Toolset (MSVC) | Derleyici sürümü | Yıllık |

### D.2 Minimum SDK ↔ Windows sürüm eşlemesi (bilgi düzeyi)

| SDK / API | Minimum Windows |
|---|---|
| Winsock2 | Windows 95'ten beri (pratikte: tüm hedefler) |
| `GetTickCount64` | Windows Vista+ |
| `QueryPerformanceCounter` | Windows XP+ (modern: tüm hedefler) |
| `timeBeginPeriod` | Windows multimedia zamanlama — eski ama aktif |
| C++/WinRT | Windows 10 + SDK 10.0.17763+ (pratikte daha yeni önerilir) |

> ⚠️ Hedef Windows sürümü için: CoreMusic L0 için kesin hedef **belirlenmemiş**
> (AGENTS.md kural 8: 512 buffer / 48 kHz — OS sürümü madde değil).
> Tablodaki minimumlar resmi SDK belgelerinden türetilmiştir; kesin hedef
> kararı ayrı ADR gerektirir — Q-L0.

### D.3 Derleme zamanı sürüm kapıları

| Kapı | Kullanım |
|---|---|
| `WINVER` / `_WIN32_WINNT` | Hedef Windows sürümü — eski API'ler gizlenir |
| `NTDDI_VERSION` | İnce sürüm kapısı |
| `WIN32_LEAN_AND_MEAN` | `windows.h`'den sık kullanılan alt başlıkları ayırır — derleme hızı |

### D.4 Sürüm doğrulama — çalışma zamanı

| Yöntem | API | Not |
|---|---|---|
| Sürücü sürümü | `VerifyVersionInfo` / manifest | Bkz. manifest = uyumluluk modu |
| Build numarası | `GetVersionEx` (manifest yoksa kısıtlı) | Uyumluluk manifesti **zorunlu** — aksi halde eski sürüm döner |
| RuntimeLibrary | Sürüm API'si değil — DLL çalışma zamanı | Statik/dinamik CRT seçimi ABI kararını etkiler |

### D.5 SDK matrisi — L0 (Neva Engine) gereksinimleri

| L0 gereksinimi | SDK kapısı | Durum |
|---|---|---|
| Winsock2 | Her SDK | `ag-api` dosyalarında belgelendi |
| Yüksek çözünürlüklü sayaç | Her SDK | §A.5 |
| Sürüm API'si | Manifest gerekli | Hedef sürüm belirlenince kapanır (Q-L0) |
| ASIO SDK (ses) | Üçüncü taraf — Steinberg ASIO SDK | `isletim-sistemi-audio` dalı (Q-P2 sınırı) — sürüm kanıtı: AGENTS.md kural 12 ⚠️ VERIFICATION REQUIRED |

### D.6 Windows sürüm ailesi — destek durumu tablosu (bilgi düzeyi)

| Sürüm ailesi | Not | Kaynak |
|---|---|---|
| Windows 10 | 22H2 · 2025-10-14 itibarıyla genel destek sonu (işletim sistemleri ailesi) | Microsoft Lifecycle — envanterde mevcut URL referansı |
| Windows 11 | Aktif sürüm | Microsoft Learn sürüm notları |
| Windows Server | Uzun servis dalları (LTSC) | Microsoft Lifecycle |

> ⚠️ Kesin sürüm tablosu bu dosyanın konusu değil — L0 hedef sürümü Q-L0 ile
> belirlenecek; tablo yalnız "sürüm matrisi kapısı" fikrini gösterir.

### D.7 Derleyici / toolchain matrisi

| Araç | Windows hedefi | Not |
|---|---|---|
| MSVC (cl.exe) | Ana desteklenen | Windows SDK ile birlikte |
| clang-cl | MSVC-uyumlu | `Ws2_32.lib` aynı |
| MinGW-w64 | Alternatif | ABI uyumlu — üretim hedefi değil |
| CMake | Platform bağımsız derleme | L0 CMakeLists = 0 (disk kanıtlı) — PLANNED |

### D.8 Sürüm matrisi — kullanım kuralı

| Kural | Açıklama |
|---|---|
| Hedef sürüm ADR'si olmadan minimum API kullanılmaz | Kural 13: sürüm bump → DUR + el değişimi |
| `_WIN32_WINNT` sabiti tek dosyada tanımlanır | Dağılmış tanımlar sürüm tutarsızlığı üretir |
| SDK sürümü + toolset birlikte | SDK yeni, toolset eski → başlık/sembol uyuşmazlığı |
| Her yeni Win32 çağrısı §D.2 tablosuna işlenir | Kapı (gate) denetimi |

## §E — CoreMusic ile sınır (PHP → FFI → Win32)

### E.1 Mevcut köprü tipleri

| Köprü | Durum | Kanıt |
|---|---|---|
| PHP → `shell_exec` → harici süreç | Mevcut | `shared/src/AI/AIEngine.php:295` — `$output = shell_exec($cmd);` |
| PHP → `curl` → Winsock (dolaylı) | Mevcut | `shared/src/AI/ToolCalling.php:256` — `$ch = curl_init($url);` |
| PHP → `php_uname` → platform sorgusu | Mevcut | `shared/src/Api/Bff/DesktopBff.php:35` — `'platform' => php_uname('s'),` |
| PHP → FFI → Win32 | **YOK** (disk kanıtlı) | FFI isabeti 0 — §C.6 |
| PHP → uzantı (C++ L0) | Planlanmadı | Q-L0 |

### E.2 FFI — ne, neden yok

| Konu | Ayrıntı |
|---|---|
| PHP FFI | PHP 7.4+ ile `FFI::cdef` üzerinden yerel kütüphane çağrısı |
| Repo durumu | Kullanılmamış (disk taraması — 2026-10-13) |
| Neden yeterli değil | Ses RT callback'leri için PHP FFI **uygun değildir**: GC · tahsis · gecikme belirsizliği (AGENTS.md sıfır tahsis kuralı — audio thread) |
| Öneri | L0 ses motoru **C++ uzantı / ayrı süreç** olmalıdır — arayüz (IPC/RPC) ayrı karardır (Q-L0) |

### E.3 Sınır matrisi — katman bazlı

| Katman | Sahip | Sınır |
|---|---|---|
| Uygulama (PHP) | K011-application | Win32 başlık bilmez — yalnız süreç/servis çağırır |
| L0 motoru (C++/CMake) | L0 — bu dal | Win32/COM başlıklarını doğrudan kullanır (Windows hedefi) |
| Ses cihaz katmanı | `isletim-sistemi-audio` (Q-P2) | Core Audio · WASAPI · ASIO — davranışsal tasarım orada |
| Çekirdek | `isletim-sistemi-kernel` | Win32 yüzeyinin altı |

### E.4 API sürüm riski — PHP ↔ Windows

| Risk | Etki | Önlem |
|---|---|---|
| `shell_exec` sunucuda kapalı | Metadata çıkarımı kırılır (`AIEngine.php:296-298` fallback döner) | Kod zaten null kontrolü yapar — disk kanıtlı |
| PHP sürüm değişimi | FFI API'si olsaydı uyumluluk riski olurdu | Kullanılmıyor — risk yok |
| Windows sürüm değişimi | Win32 API'lerde minimum sürüm kapıları (§D.3) | `_WIN32_WINNT` sabiti derlemede sabitlenir |

### E.5 Karar matrisi — PHP ↔ Win32 entegrasyon biçimi

| Seçenek | Değerlendirme | Karar |
|---|---|---|
| PHP FFI → Win32 | RT kuralına aykırı (tahsis/gc) | **Red** |
| PHP → `shell_exec` → L0 aracı | Mevcut desen (K2) — basit | Geçici kabul |
| PHP → HTTP/RPC → L0 servisi | Süreç izolasyonu · dil bağımsız | Aday — Q-L0 |
| PHP uzantısı (C++ .dll) | En düşük gecikme ama dağıtım maliyeti | Aday — Q-L0 |

> Bu dosya kararı **uygulamaz** — seçenekleri belgeler. Gerçek karar L0
> entegrasyon ADR'sinde alınacaktır (Q-L0 — ⚠️ VERIFICATION REQUIRED).

### E.6 Uzantı (extension) köprüsü — tasarım notları

| Not | Ayrıntı |
|---|---|
| C API sınırı | PHP uzantısı C++ sınıf değil yalnız C fonksiyon export'u gösterir — ABI stabilitesi (§C.8) |
| PHP kaynaklı tahsis | Uzantı içindeki tahsis PHP emme (emalloc) ile değil C++ heap ile yapılır — RT uyumu |
| Hata modeli | PHP exception fırlatma yerine `errno`-tarzı kod + PHP tarafı sarmalayıcı |
| Sürüm | Uzantı `.dll` sürümü PHP sürümüyle çapraz matriste doğrulanır (PHP 8.x — repo composer) |

> Bu tablo tasarım **notu**dur; uygulama kararı Q-L0 kapısı açılmadan alınmaz.

### E.7 Repo kanıtı — PHP ↔ dış dünya sınırları (tam liste)

| # | Dosya:Satır | Kanıt satırı | Köprü türü |
|---|---|---|---|
| K1 | `shared/src/AI/AIEngine.php:295` | `$output = shell_exec($cmd);` | Süreç çalıştırma (ffprobe — harici süreç) |
| K2 | `shared/src/AI/AIEngine.php:292` | `escapeshellarg($filePath)` | Komut satırı kaçış güvenliği |
| K3 | `shared/src/Api/Bff/DesktopBff.php:35` | `'platform' => php_uname('s'),` | Platform sorgusu |
| K4 | `shared/src/AI/ToolCalling.php:256` | `$ch = curl_init($url);` | HTTP köprüsü (dolaylı Winsock) |
| K5 | `shared/src/OAuth/Provider/BaseOAuthProvider.php:76` | `$ch = curl_init();` | OAuth HTTP köprüsü |
| K6 | `shared/src/Api/Registry/ServiceRegistry.php:102` | `$context = stream_context_create([` | PHP stream köprüsü (TLS ayarları) |

Doğrulama yöntemi: dosyalar `Select-Object -Index (N-1)` ile okunarak satır
içerikleri teyit edildi (2026-10-13).

| Kural | Açıklama |
|---|---|
| Win32 çağrısı PHP'den | **Yok** — dolaylı köprüler (K1 · K4 · K6) |
| FFI | Disk kanıtlı yok (§E.2) |
| L0 köprüsü | Karar bekliyor — Q-L0 ⚠️ VERIFICATION REQUIRED |

### E.8 Doğrulanmış kaynaklar (2026-10-13)

| # | Kaynak | Ne kanıtlar |
|---|---|---|
| 1 | Microsoft Learn — `objbase.h` API listesi | COM temel başlığı |
| 2 | Microsoft Learn — `combaseapi.h` API listesi | `CoInitializeEx` · `CoCreateInstance` başlığı |
| 3 | Microsoft Learn — `CoInitializeEx` | MTA/STA · dengeli `CoUninitialize` |
| 4 | Microsoft Learn — Dynamic-Link Library Functions | `LoadLibrary` · `GetProcAddress` · `FreeLibrary` |
| 5 | Microsoft Learn — `GetProcAddress` | Sembol çözümleme |
| 6 | Microsoft Learn — `LoadLibraryA` | DLL yükleme |
| 7 | Microsoft Learn — C++/WinRT | Header-only projection · WinRT |
| 8 | Microsoft Learn — UWP API listesi | Windows Runtime namespace'leri |
| 9 | Microsoft Learn — `GetLastError` | Win32 hata modeli |

### E.9 Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `objbase.h` — COM temel başlık yüzeyi | https://learn.microsoft.com/en-us/windows/win32/api/objbase/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `combaseapi.h` — COM base API listesi (`CoInitializeEx` · `CoCreateInstance`) | https://learn.microsoft.com/en-us/windows/win32/api/combaseapi/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `CoInitializeEx` — MTA/STA · `RPC_E_CHANGED_MODE` · dengeli `CoUninitialize` | https://learn.microsoft.com/en-us/windows/win32/com/coinitializeex | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| Dynamic-Link Library Functions — `LoadLibrary` · `GetProcAddress` · `FreeLibrary` | https://learn.microsoft.com/en-us/windows/win32/dlls/dynamic-link-library-functions | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `GetProcAddress` — DLL sembol çözümleme | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-getprocaddress | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `LoadLibraryA` — DLL yükleme (ANSI) | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-loadlibrarya | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| C++/WinRT — header-only projection · WinRT C++ yüzeyi | https://learn.microsoft.com/en-us/windows/uwp/cpp-and-winrt-apis/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| UWP API referansı — Windows Runtime namespace listesi | https://learn.microsoft.com/en-us/uwp/api/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `GetLastError` — Win32 hata kodu okuma mekanizması | https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror | 2026-10-13 | ✅ HTTP 200 (HEAD) |

---

## §11 Kullanım kararı matrisi (derinleştirme)

> §5–§7'deki yüzey envanteri buradan **seçim kararına** çevrilir. Her satır
> "hangi iş için hangi mekanizma" eşlemesidir; gerekçe § sütunundadır.

### §11.1 Windows yüzeyi seçimi

| # | İş | Önerilen mekanizma | Alternatif | Gerekçe (§) |
|---|---|---|---|---|
| 1 | Basit DLL sembol çözümleme | `LoadLibraryA` + `GetProcAddress` | COM | §5.2 |
| 2 | Windows Runtime erişimi | C++/WinRT header-only | C# projection | §5.3 |
| 3 | Windows Runtime başlatma | WinRT initializer (tek çağrı) | manuel | §5.3 |
| 4 | COM nesnesi üretimi | `CoCreateInstance` | manuel registry | §5.4 |
| 5 | STA/MTA seçimi | İş parçacığı modeline göre | varsayılan | §5.4 |
| 6 | Hata okuma | `GetLastError` (anında) | kuyruk (geç) | §5.5 |

### §11.2 Hata yakalama sırası

| # | Adım | Mekanizma | Zamanlama |
|---|---|---|---|
| 1 | Çağrı başarısız sinyali | dönüş değeri | anlık |
| 2 | Hata kodu okuma | `GetLastError` | çağrıdan hemen sonra |
| 3 | Kodu yorumlama | Win32 kod tablosu | okuma sonrası |
| 4 | Kullanıcıya çeviri | uygulama katmanı | son adım |

> **Kritik:** #2 adımı **başka bir Win32 çağrısı arasında yapılamaz** —
> araya giren çağrısı hata yazma kaydırabilir. Bu, Win32 hata modelinin en
> sık ihlal edilen kuralıdır ve §5.5'te ayrıntılıdır.

### §11.3 COM yaşam döngüsü kontrol listesi

| # | Kontrol | Sonuç ihlali |
|---|---|---|
| 1 | `CoInitializeEx` her COM kullanan thread'de çağrıldı mı? | RPC_E_NOT_INITIALIZED |
| 2 | Her `CoInitializeEx` için bir `CoUninitialize` var mı? | çalışma zamanı uyarı |
| 3 | STA/MTA seçimi beklentiyle uyumlu mu? | RPC_E_CHANGED_MODE |
| 4 | Arayüz işaretçisi her iade edildiğinde `Release` çağrıldı mı? | sızıntı |
| 5 | DLL hata yolu da `FreeLibrary` çağırıyor mu? | modül sayacı sızıntısı |

### §11.4 §11'nin kanıt etkisi

| Kategori | §11 öncesi | §11 sonrası | Not |
|---|---|---|---|
| S5 (URL HEAD 200) | §E.9'da 9 URL | değişmedi | §11 yeni URL **eklemez** |
| S4 (mimari yorum) | §8'de kayıtlı | +4 karar satırı (§11.2 · §11.3) | pratik eşleme |
| S7 (bilinmeyen) | §8'de kayıtlı | değişmedi | yeni iddia üretilmedi |

> **Sonuç:** §11 dosyanın **karar kullanılabilirliğini** artırır; kanıt
> oranını **değiştirmez**. Oran yalnızca yeni URL/repo ölçümüyle iner.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
