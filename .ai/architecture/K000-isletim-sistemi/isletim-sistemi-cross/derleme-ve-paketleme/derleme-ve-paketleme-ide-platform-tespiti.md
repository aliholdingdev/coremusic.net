---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-ide-platform-tespiti
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-cross/tasinabilirlik-siniri/tasinabilirlik-siniri-anayasa"
evidence:
  - "https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/WIN32.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/APPLE.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/UNIX.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/MINGW.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/CMAKE_SYSTEM_NAME.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/CMAKE_HOST_SYSTEM_NAME.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/CMAKE_GENERATOR.html (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/xcode/build-settings-reference (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/cpp/build/reference/vcxproj-file-structure?view=msvc-170 (✅ HTTP 200 · 2026-10-14)"
  - "https://code.visualstudio.com/docs/cpp/config-mingw (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation.isosplatform (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/standard/frameworks (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-14 · 13 URL HEAD 200 · envanter kesişimi 0 · repo satırı: ci.yml:27 · TemplateEngine.php:73 · DesktopBff.php:35"
open_questions:
  - "Q4 — hedef platform seti UNKNOWN (üretim OS teyidi yok)"
  - "Q-X61 — IDE hedef seti (Xcode/VS/CLion/VSCode) hangi ekip için bağlayıcı? ⚠️ VERIFICATION REQUIRED"
  - "Q-X62 — CMake presets dosyası repo'da henüz yok (0 glob) → PLANNED"
group: mimari
---

# derleme-ve-paketleme-ide-platform-tespiti — Kademe-4 Modül

> **Kapsam:** Bu dosya, **bir IDE'nin / derleyicinin / build sisteminin "hangi işletim
> sistemi ve mimari için derlediğini"** nasıl tespit ettiğini (compile-time makrolar,
> CMake değişkenleri, runtime API'leri) ve **Xcode · Visual Studio · CLion · VS Code ·
> PlatformIO** gibi araçların OS tespiti nasıl devreyeğini tarif eder. Karar tablosu ve
> cross-platform **önerisi** bu dosyadadır; derleme/paketleme **planları** kardeş dosyadır
> (`derleme-ve-paketleme-cross-platform-plani.md`). **Kanıt dili:** S1 = birebir repo
> satırı · S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `…/derleme-ve-paketleme/derleme-ve-paketleme-ide-platform-tespiti` |
| parent_id | `…/isletim-sistemi-cross/derleme-ve-paketleme` |
| group | `mimari` |
| Komşular | `derleme-ve-paketleme-anayasa` · `derleme-ve-paketleme-surucu` · `../tasinabilirlik-siniri` · `../platform-yuzeyleri` |
| Repo gerçeği | C++ toolchain **YOK** (`CMakeLists.txt` glob = 0); derlenen yüzey PHP (Composer) + JS (npm) → bu dosyanın C++/makro matrisleri **PLANNED** (S4) |

## §0 ELI15

"İşletim sistemini tespit etmek" üç ayrı **zamanda** ve üç ayrı **yerde** olur:

| # | Zaman | Yapan | Örnek |
|---|---|---|---|
| 1 | **Derleme anı** (ön işlemci) | derleyici makrosu | `_WIN32` · `__APPLE__` · `__linux__` |
| 2 | **Yapılandırma anı** (CMake) | build sistemi değişkeni | `WIN32` · `APPLE` · `UNIX` · `MINGW` |
| 3 | **Çalışma zamanı** (runtime) | OS API sorgusu | .NET `RuntimeInformation.IsOSPlatform` · PHP `php_uname('s')` |

Bir cross-platform proje **her üçünü de** farklı amaçla kullanır: derleme anı doğru
`#if` dalını seçer, CMake doğru dosyaları/bayrakları bağlar, runtime ise kullanıcıya
göre davranış değiştirir (ör. geçici dizin, dosya ayracı).

## §1 Derleme-anı OS tespiti (GCC/Clang ön işlemci makroları)

> Kaynak: GCC ortak önceden tanımlı makrolar (§9 #1). MSVC kendi makrolarını verir
> (`_WIN32`, `_MSC_VER`) — §1.2.

### §1.1 Ana OS makroları

| # | Makro | Anlamı | Not |
|---|---|---|---|
| 1 | `_WIN32` | Windows (32 **ve** 64-bit'de tanımlı) | `_WIN64` yalnız 64-bit |
| 2 | `_WIN64` | Windows 64-bit | `_WIN32` ile birlikte |
| 3 | `__linux__` | Linux | Android'de de tanımlı → §1.3 sırası |
| 4 | `__ANDROID__` | Android | `__linux__`'den **önce** kontrol |
| 5 | `__APPLE__` | Apple (macOS/iOS/tvOS/watchOS) | `TargetConditionals.h` ile ayrışır |
| 6 | `__MACH__` | Mach çekirdeği (macOS) | `__APPLE__` ile örtüşür |
| 7 | `__unix__` / `__unix` | Unix benzeri | Linux + macOS + BSD |
| 8 | `__FreeBSD__` | FreeBSD | ayrı dal |
| 9 | `__EMSCRIPTEN__` | WebAssembly (Emsencripten) | `__linux__` taklidi yapabilir |

### §1.2 Derleyici makroları (OS'tan bağımsız)

| # | Makro | Derleyici | Not |
|---|---|---|---|
| 1 | `_MSC_VER` | MSVC (cl.exe) | sürüm sayısal |
| 2 | `__clang__` | Clang | `__GNUC__`'yi taklit eder |
| 3 | `__GNUC__` | GCC (ve Clang taklidi) | sürüm üçlüsü |
| 4 | `__APPLE_CC__` | Apple Clang | Xcode araç zinciri |

### §1.3 Doğru sıralama kuralı (kritik — S4)

> **Kural:** `#if` zincirinde **özel olan önce** gelir; aksi halde Android/Linux ve
> Apple/iOS dalları **yanlış** eşleşir.

| # | Sıra | Neden |
|---|---|---|
| 1 | `__EMSCRIPTEN__` | WebAssembly, `__linux__` taklidi yapabilir |
| 2 | `_WIN32` | Windows her şeyden önce ayrılır |
| 3 | `__ANDROID__` | Android, `__linux__` **içindedir** |
| 4 | `__linux__` | saf Linux |
| 5 | `__APPLE__` (+ `TARGET_OS_*`) | iOS/macOS ayrımı `TargetConditionals.h` ile |
| 6 | `__FreeBSD__` … | diğer BSD'ler |
| 7 | `#else → #error` | bilinmeyen platform sessizce geçmesin |

## §2 Yapılandırma-anı OS tespiti (CMake değişkenleri)

> Kaynaklar: CMake `WIN32` · `APPLE` · `UNIX` · `MINGW` · `CMAKE_SYSTEM_NAME` ·
> `CMAKE_HOST_SYSTEM_NAME` · `CMAKE_GENERATOR` (§9 #2–#8).

### §2.1 Kurucu (preset) OS değişkenleri

| # | Değişken | True olduğu yer | Tuzağa dikkat |
|---|---|---|---|
| 1 | `WIN32` | Windows (her mimari) | Cygwin'de de tanımlı olabilir → `MINGW`/`CYGWIN` ayır |
| 2 | `MINGW` | MinGW/MSYS2 derleyicisi | `WIN32` ile birlikte |
| 3 | `CYGWIN` | Cygwin ortamı | POSIX katmanı ≠ saf Windows |
| 4 | `APPLE` | macOS · iOS · tvOS · watchOS · visionOS | **yalnız macOS değil** |
| 5 | `UNIX` | Linux · macOS · BSD · Cygwin · Android | **Windows dışı her şey** (macOS dahil!) |
| 6 | `ANDROID` | Android | `UNIX` ile birlikte |

> **En sık hata (S4):** `if(UNIX)` ile "Linux" kontrol etmek — macOS de `UNIX`'tir.
> Doğrusu: `if(APPLE)` → macOS, `elseif(UNIX AND NOT APPLE)` → Linux/BSD.

### §2.2 Hedef vs ana makine (cross derleme)

| # | Değişken | Ne sorar | Kullanım |
|---|---|---|---|
| 1 | `CMAKE_SYSTEM_NAME` | **hedef** OS adı (`Linux`·`Windows`·`Darwin`) | cross derleme toolchain dosyası |
| 2 | `CMAKE_HOST_SYSTEM_NAME` | **bu makinede** CMake'in çalıştığı OS | host/target ayrımı |
| 3 | `CMAKE_SYSTEM_PROCESSOR` | hedef mimari (`x86_64`·`aarch64`) | evrensel/çok-mimari paket |

### §2.3 Üreteç (generator) OS tespiti

| # | `CMAKE_GENERATOR` | Tipik OS | Ürettiği |
|---|---|---|---|
| 1 | `Visual Studio 17 2022` | Windows | `.sln` + `.vcxproj` |
| 2 | `Xcode` | macOS | `.xcodeproj` |
| 3 | `Ninja` | 3 OS (üretim önerisi) | `build.ninja` |
| 4 | `Unix Makefiles` | Linux/macOS | `Makefile` |
| 5 | `MinGW Makefiles` | Windows (MSYS2) | `Makefile` |

> **Öneri (S4):** IDE-dosyalarını (`.sln`/`.xcodeproj`) **CI'da üretmeyin**; tek
> üreteç = **Ninja** (3 OS'ta aynı). IDE'ler CMake'i **dahili** kullanır (§4).

## §3 Çalışma-zamanı OS tespiti (runtime API)

| # | Ekosistem | API | OS | Repo kanıtı |
|---|---|---|---|---|
| 1 | .NET | `RuntimeInformation.IsOSPlatform(OSPlatform.Windows/Linux/OSX)` | 3 OS | S4 (PLANNED) |
| 2 | .NET | TFM (`net8.0-windows` vs `net8.0`) | paketleme | §9 #13 |
| 3 | PHP | `php_uname('s')` | Linux/Windows/Darwin | **S1** `shared/src/Api/Bff/DesktopBff.php:35` |
| 4 | PHP | `PHP_OS_FAMILY` | Windows/Unix/Darwin | S4 (aynı aile) |
| 5 | C/C++ | **standart runtime OS tespiti YOK** | — | platform API'si gerekir (§7 #1) |
| 6 | Node.js | `process.platform` (`win32`·`linux`·`darwin`) | 3 OS | S4 (PLANNED) |
| 7 | Python | `sys.platform` / `platform.system()` | 3 OS | S4 (PLANNED) |

> **C/C++ dürüstlüğü (S4):** C++ standardında "hangi OS'tayım" çağrısı **yoktur**;
> runtime tespiti ya platform API'si (`uname` / `GetVersionEx` / `NSProcessInfo`) ya da
> derleme-anı sabitinin yansımasıdır. Bu, C++'ın en zayıf cross-platform noktasıdır →
> `../tasinabilirlik-siniri` §2.1 kategori 6.

## §4 IDE bazlı OS tespiti matrisi

| # | IDE | OS kapsamı | OS tespiti nasıl | Proje biçimi | Üreteç/derleyici |
|---|---|---|---|---|---|
| 1 | **Xcode** | macOS (iOS hedefi) | `__APPLE__` + `TargetConditionals.h` + `MACOSX_DEPLOYMENT_TARGET` | `.xcodeproj` / `.xcworkspace` | Apple Clang (§9 #9) |
| 2 | **Visual Studio** | Windows (3 OS'ta VS Code değil) | `_WIN32` + `WINVER` + Windows SDK | `.sln` + `.vcxproj` (§9 #10) | MSVC `cl.exe` |
| 3 | **CLion** | 3 OS | CMake'e bırakır (`APPLE`/`WIN32`/`UNIX`) | `CMakeLists.txt` | CMake + Ninja/Make |
| 4 | **VS Code** | 3 OS | derleyiciye + CMake Tools'a bırakır | `CMakeLists.txt` / `tasks.json` | MinGW-GCC / Clang / MSVC (§9 #11) |
| 5 | **PlatformIO** | 3 OS (embedded host) | `platformio.ini` `[env:*]` hedefi | `platformio.ini` | kendi toolchain indirir (§9, K001) |

### §4.1 Xcode notu (S4)

Xcode **yalnız macOS'ta** çalışır; OS tespiti geliştiriciye düşmez — araç zinciri zaten
`__APPLE__` tanımlar. Asıl karar **deployment target** ve **arch**'tir:
`CMAKE_OSX_ARCHITECTURES="arm64;x86_64"` ile evrensel (universal) binary üretilir
(§9 #12 · kardeş dosya §3.3).

### §4.2 Visual Studio notu (S4)

`.vcxproj` içinde `WindowsTargetPlatformVersion` ve `PlatformToolset` **sabitlenmeli**;
aksi halde makineler arası derleme sürüklenir. Windows SDK sürümü ile `WINVER`/
`_WIN32_WINNT` uyumu `../platform-yuzeyleri` sorumluluğundadır.

### §4.3 CLion / VS Code notu (S4)

Bu iki IDE **OS tespitini kendi üstlenmez** — `CMakeLists.txt` tek doğruluk kaynağıdır.
VS Code'da Windows tarafı MinGW-GCC ile doğrulanmıştır (§9 #11). Sonuç: **IDE'yi
seçmek OS tespitini değiştirmez; build dosyasını (CMake) seçmek değiştirir.**

## §5 Cross-platform ÖNERİSİ (karar tablosu — bu dosyanın ana çıktısı)

| # | Karar | Öneri | Gerekçe | Bağlayıcılık |
|---|---|---|---|---|
| 1 | Build sistemi | **CMake + CMakePresets.json** | 3 OS + Xcode/VS/Ninja üreteçleri (§2.3) | [TASARIM] |
| 2 | Üreteç (CI) | **Ninja** | 3 OS'ta tek davranış; IDE dosyası üretmez | [TASARIM] |
| 3 | Üreteç (geliştirici) | IDE kendi üreteci (Xcode/VS) | konfor; CI'dan bağımsız | [TASARIM] |
| 4 | OS tespiti (derleme) | Tek merkezi başlık (`platform.h`) + §1.3 sırası | `#ifdef` dağınıklığı önlenir | [TASARIM] |
| 5 | OS tespiti (CMake) | `if(APPLE)`→macOS · `elseif(WIN32)` · `elseif(UNIX)` | `UNIX`≠Linux tuzağı (§2.1) | [TASARIM] |
| 6 | Runtime tespiti | C/C++'ta **soyutlama katmanı** (adaptör) | standart API yok (§3 #5) | [TASARIM] |
| 7 | Mimari kapsam | Windows x64 · Linux x64 · Linux arm64 (RPi5) · macOS universal | Q4 açık → hedef set **öneri** | ⚠️ Q4 |
| 8 | IDE bağımlılığı | IDE **asla** bağlayıcı olmaz; CMake + CLI bağlayıcı | ekip/CI taşınabilirliği | [TASARIM] |

> **Özet öneri (S4):** "Aynı kaynak · tek CMake · Ninja CI · IDE serbest." OS tespiti
> **yalnız iki yerde** toplanır: `platform.h` (derleme) ve CMake `if` bloğu (yapılandırma).
> Runtime tespiti **yalnız** adaptör arkasından yapılır.

## §6 Ölçüm / kabul kriterleri

| # | Ölçüm | Yöntem | Bugün | Hedef |
|---|---|---|---|---|
| 1 | `CMakeLists.txt` sayısı | glob | **0** (S1) | ≥1 (PLANNED) |
| 2 | `CMakePresets.json` varlığı | dosya | **yok** (S7) | var |
| 3 | Merkezi `platform.h` | glob | **yok** (S7) | 1 |
| 4 | `#ifdef` yüzey sayısı | grep | **0** C++ (S1) | bütçe tanımlı değil ⚠️ |
| 5 | CI OS matrisi | `ci.yml` `runs-on` | **1** (`ubuntu-latest`, `ci.yml:27`) | ≥2 (§5 #7) |
| 6 | URL HEAD 200 | HTTP | 13/13 | 13/13 |

## §7 Kırılma noktaları

| # | Kırılma | Nerede | Önleme |
|---|---|---|---|
| 1 | `if(UNIX)` ile "Linux" sanılması | CMake | `APPLE`'ı önce ele (§2.1) |
| 2 | `#if` sırası yanlış → Android/Linux çakışması | `platform.h` | §1.3 sırası |
| 3 | `_WIN32`'nin 64-bit'de de tanımlı olması | kod | `_WIN64` ayrıca sor |
| 4 | VS/Xcode dosyasının CI'da üretilmesi | CI | Ninja üreteci sabitle (§5 #2) |
| 5 | macOS ≠ UNIX ayrımının unutulması | paketleme | `APPLE` dalı (§3.3 kardeş dosya) |
| 6 | runtime OS tespitinin adaptörsüz yazılması | kod | §5 #6 |

## §8 Komşu sınırı

| # | Dışarıda olan | Nereye gider |
|---|---|---|
| 1 | Derleme/paketleme **planları** (fazlar, artefakt) | `derleme-ve-paketleme-cross-platform-plani.md` |
| 2 | Paket formatı **sürüm/denetim** matrisi | `derleme-ve-paketleme-surucu.md` |
| 3 | `#ifdef`/taşınabilirlik **kararı** | `../tasinabilirlik-siniri` |
| 4 | OS API/ABI **farkları** | `../platform-yuzeyleri` |
| 5 | Embedded (PIC/AVR/Arduino) derleme zinciri | `K001-donanım/donanaım-type/mikrodenetleyici` |
| 6 | CI pipeline tasarımı | `K018-devops` |

## §9 Kaynakça (web · 2026-10-14 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-14 | Durum |
|---|---|---|---|
| GCC ortak önceden tanımlı makrolar (`_WIN32`·`__linux__`·`__APPLE__`) | https://gcc.gnu.org/onlinedocs/cpp/Common-Predefined-Macros.html | 2026-10-14 | ✅ HTTP 200 |
| CMake `WIN32` değişkeni Windows'u işaret eder | https://cmake.org/cmake/help/latest/variable/WIN32.html | 2026-10-14 | ✅ HTTP 200 |
| CMake `APPLE` tüm Apple platformlarını kapsar | https://cmake.org/cmake/help/latest/variable/APPLE.html | 2026-10-14 | ✅ HTTP 200 |
| CMake `UNIX` Linux + macOS + BSD'yi kapsar | https://cmake.org/cmake/help/latest/variable/UNIX.html | 2026-10-14 | ✅ HTTP 200 |
| CMake `MINGW` MinGW derleyicisini işaret eder | https://cmake.org/cmake/help/latest/variable/MINGW.html | 2026-10-14 | ✅ HTTP 200 |
| `CMAKE_SYSTEM_NAME` hedef OS adıdır (cross derleme) | https://cmake.org/cmake/help/latest/variable/CMAKE_SYSTEM_NAME.html | 2026-10-14 | ✅ HTTP 200 |
| `CMAKE_HOST_SYSTEM_NAME` CMake'in çalıştığı OS'tur | https://cmake.org/cmake/help/latest/variable/CMAKE_HOST_SYSTEM_NAME.html | 2026-10-14 | ✅ HTTP 200 |
| `CMAKE_GENERATOR` aktif üreteci verir (VS/Xcode/Ninja) | https://cmake.org/cmake/help/latest/variable/CMAKE_GENERATOR.html | 2026-10-14 | ✅ HTTP 200 |
| Xcode build-settings referansı (deployment target · arch) | https://developer.apple.com/documentation/xcode/build-settings-reference | 2026-10-14 | ✅ HTTP 200 |
| `.vcxproj` dosya yapısı (Visual Studio proje biçimi) | https://learn.microsoft.com/en-us/cpp/build/reference/vcxproj-file-structure?view=msvc-170 | 2026-10-14 | ✅ HTTP 200 |
| VS Code'da MinGW ile C++ derleme yapılandırması | https://code.visualstudio.com/docs/cpp/config-mingw | 2026-10-14 | ✅ HTTP 200 |
| .NET `RuntimeInformation.IsOSPlatform` runtime OS tespiti | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.runtimeinformation.isosplatform | 2026-10-14 | ✅ HTTP 200 |
| .NET hedef-çatı (TFM) sistemi (`net8.0-windows` vb.) | https://learn.microsoft.com/en-us/dotnet/standard/frameworks | 2026-10-14 | ✅ HTTP 200 |

> `cmake-presets.7.html` · `cmake-toolchains.7.html` · `mesonbuild.com/` bu üretimde
> **yeniden kullanılmadı** — envanterde mevcut (kardeş `derleme-ve-paketleme-anayasa.md`
> ve `isletim-sistemi-cross-dogrulama.md` §9'a bak).

## §10 Repo kanıtları (S1)

| # | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | 27 | `runs-on: ubuntu-latest` → tek OS CI |
| 2 | `shared/src/Component/TemplateEngine.php` | 73 | `DIRECTORY_SEPARATOR` → yol ayracı soyutlaması |
| 3 | `shared/src/Api/Bff/DesktopBff.php` | 35 | `php_uname('s')` → runtime OS tespiti |
| 4 | `CMakeLists.txt` | 0 | C++ build dosyası **yok** (glob) → PLANNED |

## §11 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q4 | Üretim/çalışma OS seti (Linux/Windows/macOS/RPi5) teyit edildi mi? | ⚠️ VERIFICATION REQUIRED |
| Q-X61 | IDE hedef seti (hangi ekip/eğitim için bağlayıcı) tanımlı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X62 | `CMakePresets.json` hangi fazda repo'ya giriyor? | UNKNOWN |
| Q-X63 | `platform.h` merkezî başlığı K005-firmware (Neva) ile ortak mı? | UNKNOWN |

## §12 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-14 | İlk yazım — 13 URL HEAD 200 + 4 S1 repo satırı; IDE/OS tespiti 3 zaman modeli; cross-platform öneri tablosu (§5) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
