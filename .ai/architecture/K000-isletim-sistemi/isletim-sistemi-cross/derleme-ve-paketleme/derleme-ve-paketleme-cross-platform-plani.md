---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-cross-platform-plani
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa"
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-ide-platform-tespiti"
evidence:
  - "https://cmake.org/cmake/help/latest/module/CPack.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/CMAKE_BUILD_TYPE.html (✅ HTTP 200 · 2026-10-14)"
  - "https://cmake.org/cmake/help/latest/variable/CMAKE_OSX_ARCHITECTURES.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/msix/ (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.platformio.org/en/latest/core/installation.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.platformio.org/en/latest/platforms/native.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-14 · 6 URL HEAD 200 · envanter kesişimi 0 · repo satırı: ci.yml:27 (tek OS CI) · CMakeLists.txt glob 0"
open_questions:
  - "Q4 — hedef platform seti UNKNOWN (öneri: Windows x64 · Linux x64 · Linux arm64 · macOS universal)"
  - "Q-P1 — paketleme aracı seçimi (CPack vs elle script) PLANNED"
  - "Q-P2 — kod imzalama/nataryzasyon sertifikası envanteri UNKNOWN"
group: mimari
---

# derleme-ve-paketleme-cross-platform-plani — Kademe-4 Modül

> **Kapsam:** Bu dosya, kardeş `derleme-ve-paketleme-ide-platform-tespiti.md`'nin
> **önerisini** (tek CMake · Ninja CI · IDE serbest) somut **derleme planına** ve
> **paketleme planına** çevirir: OS başına toolchain, fazlar, artefakt adlandırması.
> IDE/OS tespiti matrisi kardeş dosyadır (§2 bağlantısı). **Kanıt dili:** S1 = birebir
> repo satırı · S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `…/derleme-ve-paketleme/derleme-ve-paketleme-cross-platform-plani` |
| parent_id | `…/isletim-sistemi-cross/derleme-ve-paketleme` |
| group | `mimari` |
| Komşular | `derleme-ve-paketleme-anayasa` · `derleme-ve-paketleme-ide-platform-tespiti` · `derleme-ve-paketleme-surucu` · `derleme-ve-paketleme-ornekler` |
| Repo gerçeği | `CMakeLists.txt` glob **0** (S1) → tüm plan **PLANNED**; bugün derlenen yüzey PHP (Composer) + JS (npm) |

## §0 ELI15

"Plan" = **ne, hangi sırayla, hangi araçla, ne çıkacak** dörtlüsü. Bu dosya üç tablo
üretir: **(1) OS başına derleme tablosu** (toolchain + komut), **(2) faz planı**
(önce ne → sonra ne), **(3) paketleme tablosu** (artefakt formatı OS başına).
Hepsi `[TASARIM]`/`[KALIP]` işaretlidir — repo'da henüz C++ derlemesi **yok** (S1).

## §1 Hedef platform seti (öneri — ⚠️ Q4)

| # | OS | Mimari | Paket biçimi | Öncelik |
|---|---|---|---|---|
| 1 | Windows 10/11 | x64 | MSIX (§5 #1) | 1 |
| 2 | Linux (geliştirme/CI) | x64 | `.tar.gz` + `.deb` (§5 #2) | 1 |
| 3 | Linux (RPi5 dağıtım) | arm64 | `.tar.gz` | 2 |
| 4 | macOS | universal (`arm64;x86_64`) | `.app` + `.dmg` | 2 |

> Q4 kapalı değil — bu set **öneridir**, bağlayıcı değildir. [TASARIM]

## §2 Derleme planı — OS başına toolchain

| # | OS | Derleyici | Yapılandırıcı | Üreteç (CI) | Çıktı |
|---|---|---|---|---|---|
| 1 | Windows | MSVC (`cl.exe`) veya MinGW-GCC | CMake | **Ninja** | `.exe` |
| 2 | Linux x64/arm64 | GCC | CMake | **Ninja** | ELF (dinamik) |
| 3 | macOS | Apple Clang | CMake | **Ninja** (veya `Xcode` üreteci konfor için) | Mach-O (universal) |

> **Kural (S4):** CI'da **tek üreteç = Ninja** (3 OS'ta aynı davranış — kardeş dosya
> §2.3 · §5 #2). `.sln`/`.xcodeproj` **yalnız geliştirici konforu** için üretilir.

### §2.1 Yapılandırma bayrakları (tüm OS'lar ortak) [KALIP]

| Bayrak | Değer | Neden |
|---|---|---|
| `CMAKE_BUILD_TYPE` | `Release` (CI) · `RelWithDebInfo` (artefakt) · `Debug` (geliştirici) | §9 #2 |
| `CMAKE_OSX_ARCHITECTURES` | `arm64;x86_64` | yalnız macOS evrensel binary (§9 #3) |
| `CMAKE_TOOLCHAIN_FILE` | yok (host derleme) | cross derleme ise verilir (§4) |

## §3 Derleme fazları (sıralı)

| # | Faz | Ne | Kapı |
|---|---|---|---|
| F0 | Ortam | CMake ≥3.25 + Ninja + derleyici kurulumu (3 OS) | araç sürümü sabitlenir |
| F1 | Skeleton | `CMakeLists.txt` + `CMakePresets.json` repo'ya girer | `cmake --preset …` her OS'ta geçer |
| F2 | Platform katmanı | Merkezi `platform.h` (derleme-anı) + adaptör (runtime) — kardeş dosya §1.3 / §3 | 3 OS'ta derleme yeşil |
| F3 | CI matrisi | `ci.yml` `runs-on` → `ubuntu-latest` + `windows-latest` + `macos-latest` | 3/3 yeşil |
| F4 | Artefakt | CPack/NSIS/DMG üretimi (§5) | artefakt adlandırma §6 |
| F5 | Sürüm | Semver + artefakt checksum (SHA-256) | surucu §8.5.1 |

> Fazlar birbirini **bloklar** (K-12: her faz kendi kanıtıyla onaylanır). Bugün repo
> **F0 öncesi** (S1: `CMakeLists.txt` yok).

## §4 Cross derleme (RPi5 arm64) — [KALIP]

| # | Adım | Komut kalıbı |
|---|---|---|
| 1 | Toolchain dosyası | `CMAKE_SYSTEM_NAME=Linux` · `CMAKE_SYSTEM_PROCESSOR=aarch64` (§9, kardeş dosya §2.2) |
| 2 | Configure | `cmake -B build-arm64 -DCMAKE_TOOLCHAIN_FILE=…/aarch64.cmake` |
| 3 | Build | `cmake --build build-arm64 -j` |
| 4 | Doğrulama | `file build-arm64/*` → `aarch64` ELF |

## §5 Paketleme planı — OS başına

| # | OS | Biçim | Üretici | Not |
|---|---|---|---|---|
| 1 | Windows | **MSIX** (öneri) | `CPack` (NSIS/ZIP alternatif) | §9 #4 |
| 2 | Linux | `.tar.gz` (+ `.deb` opsiyonel) | `CPack` (`TGZ`/`DEB`) | §9 #1 |
| 3 | macOS | `.app` → `.dmg` | `CPack` (`DragNDrop`) + imzalama | §9 #3 · ⚠️ Q-P2 |
| 4 | Embedded (PIC/AVR) | `.hex` | PlatformIO / avrdude (K001 dosyası) | kardeş `mikrodenetleyici` |

> **CPack neden (S4):** `anayasa` §10.3'teki **kalan** format (`DEB`·`RPM`·`NSIS`·
> `ZIP`·`DragNDrop`) tek üretimde **CPack** ile karşılanır → anayasa ile çakışma yok.

## §6 Artefakt adlandırma (anayasa §23.1 ile uyum)

| # | Örnek | Kalıp |
|---|---|---|
| 1 | `coremusic-1.4.0-win-x64.msix` | `{ad}-{sürüm}-{os}-{mimari}.{uzantı}` |
| 2 | `coremusic-1.4.0-linux-x64.tar.gz` | aynı |
| 3 | `coremusic-1.4.0-macos-universal.dmg` | `universal` (§2.1) |

## §7 Kabul kriterleri

| # | Kriter | Ölçüm | Bugün | Hedef |
|---|---|---|---|---|
| 1 | `CMakePresets.json` 3 OS configure | CLI | **yok** (S7) | 3/3 geçer |
| 2 | CI OS matrisi | `runs-on` satırı | **1** (`ubuntu-latest`, `ci.yml:27`) | 3/3 yeşil |
| 3 | Artefakt üretimi | CPack | **yok** | 3 OS artefaktı |
| 4 | Checksum (SHA-256) | artefakt yanında `.sha256` | **yok** | var |
| 5 | URL HEAD 200 | HTTP | 6/6 | 6/6 |

## §8 Kırılma noktaları

| # | Kırılma | Nerede | Önleme |
|---|---|---|---|
| 1 | Sürüm-sürükleme (toolchain sürümü makinelerde farklı) | CI | F0'da sürüm sabitleme |
| 2 | `.sln`/`.xcodeproj`'in CI'a taşınması | CI | Ninja (§2) |
| 3 | macOS notarizasyon eksikliği → Gatekeeper reddi | paketleme | Q-P2 |
| 4 | Artefakt adlandırmasının anayasa §23.1'e uymaması | release | §6 |
| 5 | Windows'ta dinamik CRT bağımlılığı | derleme | `/MT` veya CRT dağıtımı (PLANNED) |

## §9 Komşu sınırı

| # | Dışarıda olan | Nereye gider |
|---|---|---|
| 1 | OS tespiti (makro/CMake/runtime) matrisi | `derleme-ve-paketleme-ide-platform-tespiti.md` |
| 2 | Paket formatı sürüm/denetim matrisi | `derleme-ve-paketleme-surucu.md` |
| 3 | CI pipeline tasarımı (GitHub Actions akışı) | `K018-devops` |
| 4 | Embedded `.hex` dağıtımı | `K001-donanım/donanaım-type/mikrodenetleyici` |

## §10 Kaynakça (web · 2026-10-14 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-14 | Durum |
|---|---|---|---|
| CPack paketleme modülü (TGZ·DEB·NSIS·DragNDrop) | https://cmake.org/cmake/help/latest/module/CPack.html | 2026-10-14 | ✅ HTTP 200 |
| `CMAKE_BUILD_TYPE` yapılandırma türü (Debug/Release) | https://cmake.org/cmake/help/latest/variable/CMAKE_BUILD_TYPE.html | 2026-10-14 | ✅ HTTP 200 |
| `CMAKE_OSX_ARCHITECTURES` evrensel (çok-mimari) macOS binary | https://cmake.org/cmake/help/latest/variable/CMAKE_OSX_ARCHITECTURES.html | 2026-10-14 | ✅ HTTP 200 |
| Windows MSIX paket biçimi | https://learn.microsoft.com/en-us/windows/msix/ | 2026-10-14 | ✅ HTTP 200 |
| PlatformIO Core kurulumu (host toolchain) | https://docs.platformio.org/en/latest/core/installation.html | 2026-10-14 | ✅ HTTP 200 |
| PlatformIO `native` platformu (host derleme) | https://docs.platformio.org/en/latest/platforms/native.html | 2026-10-14 | ✅ HTTP 200 |

## §11 Repo kanıtları (S1)

| # | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | 27 | `runs-on: ubuntu-latest` → bugün **tek** OS CI; plan 3 OS |
| 2 | `CMakeLists.txt` | 0 | C++ build dosyası **yok** → plan tamamı **PLANNED** |

## §12 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q4 | Hedef platform seti (Windows x64 · Linux x64/arm64 · macOS) teyidi | ⚠️ VERIFICATION REQUIRED |
| Q-P1 | Paketleme aracı (CPack vs. özel script) kararı | PLANNED |
| Q-P2 | macOS imzalama/nataryzasyon sertifikası var mı? | UNKNOWN |
| Q-P3 | `.deb`/RPM üretimi ne zaman giriyor (RPi5 dağıtım planı)? | UNKNOWN |

## §13 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-14 | İlk yazım — 6 URL HEAD 200 + 2 S1 repo satırı; 4 OS derleme/paketleme tablosu; 6 fazlı plan (F0–F5); artefakt adlandırma anayasa §23.1 ile uyumlu |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
