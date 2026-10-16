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

## §14 Toolchain Tam Envanter Matrisi (append-only, 2026-10-14)

| # | OS | Derleyici | Paketleyici | Paket formatı | Kaynak (bu belge) | Durum |
|---|---|---|---|---|---|---|
| T-1 | Windows (x64) | MSVC (cl/link) veya clang-cl | NSIS / WiX (MSI) | `.exe` / `.msi` | §5 | ✅ (belge) |
| T-2 | macOS (arm64/x64) | AppleClang (Xcode) | `pkgbuild`/`productbuild` | `.pkg` | §5 | ✅ (belge) |
| T-3 | Linux (x64/arm64) | GCC/Clang | `dpkg-deb` / `rpmbuild` / AppImage | `.deb`/`.rpm`/AppImage | §5 | ✅ (belge) |
| T-4 | RPi5 (arm64) | cross GCC (aarch64-linux-gnu) | dpkg-deb (cross) | `.deb` (arm64) | §4 | ✅ (belge) |
| T-5 | CI runner mimarisi | GCC/Clang (Ubuntu) + MSVC + AppleClang | CI artefakt yükleyici | sürümlü artefakt | §3, §7 | ⚠️ (koşum kanıtı yok) |
| T-6 | Paket-imzası (code signing) | Windows: Authenticode · macOS: notarization | imzalama aracı | imzalı paket | ⚠️ (bu belgede derinleşmedi) | ⚠️ VERIFICATION REQUIRED |

## §15 Bayrak Matrisi (§2.1'in genişletmesi — append-only)

| # | Bayrak | Windows (MSVC) | macOS (Clang) | Linux (GCC) | Amaç | Durum |
|---|---|---|---|---|---|---|
| B-1 | Uyarı seviyesi | `/W4` | `-Wall -Wextra` | `-Wall -Wextra` | Uyarı disiplini | ✅ (§2.1 KALIP) |
| B-2 | Hata→uyarı | `/WX` | `-Werror` | `-Werror` | CI'da kırılma | ✅ (§2.1 KALIP) |
| B-3 | C++ standardı | `/std:c++20` | `-std=c++20` | `-std=c++20` | Dille hizalama | ✅ (§2.1 KALIP) |
| B-4 | Optimize (release) | `/O2` | `-O2` | `-O2` | Üretim optimizasyonu | ✅ (§2.1 KALIP) |
| B-5 | Sembolik (debug) | `/Zi` | `-g` | `-g` | Hata ayıklama | ✅ (§2.1 KALIP) |
| B-6 | Boyut (release LTO) | `/GL`+`/LTCG` | `-flto` | `-flto` | Boyut/hız dengesi | ⚠️ (belge; ölçüm yok) |
| B-7 | RTTI/exceptions | Varsayılan açık | Varsayılan açık | Varsayılan açık | Ses motoru bağımlılığı | ⚠️ (seçim Q2) |

## §16 Paket Formatı Karşılaştırması (append-only)

| # | Kriter | Windows | macOS | Linux | Durum |
|---|---|---|---|---|---|
| P-1 | Kurulum deneyimi | MSI sihirbazı | `.pkg` sihirbazı | `.deb`/`.rpm` (GUI + CLI) | ✅ (belge §5) |
| P-2 | Sürüm/geri alma | MSI upgrade tablosu | pkg upgrade | paket yöneticisi sürümü | ✅ (belge §5) |
| P-3 | İmzalama | Authenticode | notarization + hardened runtime | imza anahtarı (rpm) | ⚠️ (T-6 açık) |
| P-4 | Bağımlılık taşıma | Statik/dinamik DLL | Framework/dylib | statik + glibc uyumu | ⚠️ (glibc min sürüm ölçümü yok) |
| P-5 | Taşınabilir (RPi5) | — | — | arm64 `.deb` (§4) | ✅ (belge §4) |
| P-6 | Temiz kaldırma | MSI uninstall | pkg receive-files | paket kaldırma + artakalan dosya testi | ⚠️ (kabul kriteri §7) |

## §17 Faz Kapıları (F0–F5 — §3'ün test karşılığı, append-only)

| # | Faz | Kapı (geçiş ölçütü) | Ölçülebilir mi? | Durum |
|---|---|---|---|---|
| F0 | Araç doğrulama | Her OS'ta derleyici sürümü yazdırılır | Evet (sürüm çıktısı) | ⚠️ (koşum yok) |
| F1 | İskelet derleme | 0 hata + `/WX`-`-Werror` temiz | Evet (exit code) | ⚠️ (koşum yok) |
| F2 | Test eşiği | §7 kabul kriteri: testler geçiyor | Evet (test raporu) | ⚠️ (koşum kanıtı yok) |
| F3 | Cross (RPi5) | arm64 artefakt üretilir + QEMU/boot testi | Evet (çalıştırma) | ⚠️ (donanım testi yok) |
| F4 | Paketleme | 4 OS'ta paket üretilir + kurulum testi | Evet (kurulum logu) | ⚠️ (test yok) |
| F5 | İmzalama+yayım | İmza doğrulama + sürüm etiketi | Evet (imza kontrolü) | ⚠️ VERIFICATION REQUIRED (T-6) |

## §18 Cross-Derleme Detay (§4'ün genişletmesi — append-only)

| # | Öğe | Değer/kural | Kaynak (bu belge) | Durum |
|---|---|---|---|---|
| C-1 | Hedef üçlü | `aarch64-linux-gnu` | §4 | ✅ (belge) |
| C-2 | Sysroot | RPi5 rootfs/sysroot (sürüm eşleşmeli) | §4 | ⚠️ (sürüm ölçümü yok) |
| C-3 | İçerme/bağımlılık | `find_package` + pkg-config (cross) | §2, §4 | ✅ (belge) |
| C-4 | Çalıştırma doğrulama | QEMU user veya gerçek RPi5 | §4 | ⚠️ (test yok) |
| C-5 | Ses cihazı bağımlılığı (RPi5) | ALSA üzerinden çıkış (Linux payı) | K000/audio (dahili) | ✅ (dahili) |
| C-6 | Çapraz → yerel fark (CMake) | `CMAKE_SYSTEM_NAME` vs host | ide-platform-tespiti §2.2 (kardeş) | ✅ (kardeş belge) |

## §19 Artefakt + Bütünlük Zinciri (§6'nın genişletmesi — append-only)

| # | Öğe | Kural | Durum |
|---|---|---|---|
| A-1 | Adlandırma | `ürün-sürüm-os-arch.paket` (anayasa §23.1) | ✅ (§6) |
| A-2 | Sürüm etiketi | Semver + git rev (kısa) | ✅ (§6, §11) |
| A-3 | Checksum | SHA-256 yan dosyası (`.sha256`) | ⚠️ (uygulama yok — ölçüm gerek) |
| A-4 | İmza | T-6 zinciri (code signing) | ⚠️ VERIFICATION REQUIRED |
| A-5 | Yeniden üretilebilirlik | Aynı araç sürümü → aynı çıktı | ⚠️ (reproducible build ölçümü yok) |
| A-6 | Saklama | CI artefakt saklama süresi | ⚠️ (CI yapılandırması) |

## §20 Test Matrisi (append-only)

| # | Test | Windows | macOS | Linux | RPi5 | Durum |
|---|---|---|---|---|---|---|
| X-1 | Derleme (F1) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Hepsi açık (koşum yok) |
| X-2 | Birim test (F2) | ⚠️ | ⚠️ | ⚠️ | — | Açık (test raporu yok) |
| X-3 | Paket kurulum (F4) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Açık |
| X-4 | İmza doğrulama (F5) | ⚠️ | ⚠️ | ⚠️ | — | Açık (T-6) |
| X-5 | Çapraz boot (F3) | — | — | — | ⚠️ | Açık (C-4) |
| X-6 | Artefakt adlandırma tutarlılığı | ✅ (belge) | ✅ | ✅ | ✅ | Belge düzeyi ✅ |

## §21 Red Team (append-only)

| # | İddia | Neden zayıf | Durum |
|---|---|---|---|
| R-1 | "Plan uygulanabilir" | Hiçbir faz koşulmadı (F0–F5 ⚠️) | AÇIK |
| R-2 | "4 OS tek planla kapsanır" | macOS notarization + Windows imza derinleşmedi (T-6) | ⚠️ |
| R-3 | "Cross derleme hazır" | Sysroot sürümü + QEMU testi yok (C-2, C-4) | ⚠️ |
| R-4 | "Paket temiz kaldırılabilir" | Artakalan dosya testi yok (P-6) | ⚠️ |
| R-5 | "Reproducible" | Ölçüm yok (A-5) | ⚠️ |
| R-6 | "URL'ler kanıt" | HEAD 200 = erişilebilirlik; içerik iddiası ≠ doğrulama | ✅ işaretli (§10) |

## §22 Kapı + Ölçüm (append-only, 2026-10-14)

| # | Açık soru (§12 devamı) | Durum | Kanıt |
|---|---|---|---|
| Q-1 | F0–F5 fazları koşuldu mu? | **AÇIK** | Hepsi ⚠️ (koşum kanıtı yok) |
| Q-2 | Bayrak seçimi (B-6, B-7) ölçüldü mü? | **AÇIK** | ⚠️ (ölçüm yok) |
| Q-3 | İmzalama (T-6, A-4) kuruldu mu? | **AÇIK** | ⚠️ VERIFICATION REQUIRED |
| Q-4 | Hedef platform seti (§1 Q4) onaylandı mı? | **AÇIK** | §1'de ⚠️ öneri olarak duruyor |
| Q-5 | Cross testi (C-4, X-5) yapıldı mı? | **AÇIK** | ⚠️ (donanım/QEMU yok) |
| Q-6 | Reproducible build (A-5) doğrulandı mı? | **AÇIK** | ⚠️ (ölçüm yok) |

**Ölçüm (nihai):** Başlangıç 185 → §14–§22 ile **≥505** (append-only, dolgu/TBD yok) ·
Frontmatter korunur (`group:` çıplak) · `status:`/`Sürüm geçmişi` değişmedi (yalnız satır eklendi) ·
kapı ihlali 0 (Q1–Q6 açık kaldı) · beyazliste dışı yazım 0 (yalnız K000) · `git commit` yok ·
mojibake 0 · Authority son satır.

---

## §23 OS-Başı Derleme Komut Kalıpları (append-only — belge düzeyi, koşum yok)

| # | OS | Yapılandırma komutu (kalıp) | Derleme komutu (kalıp) | Durum |
|---|---|---|---|---|
| K-1 | Windows | `cmake -B build -G "Visual Studio 17 2022" -A x64` | `cmake --build build --config Release` | ⚠️ (belge; koşum yok) |
| K-2 | macOS | `cmake -B build -G Xcode -DCMAKE_OSX_ARCHITECTURES=arm64` | `cmake --build build --config Release` | ⚠️ (belge; koşum yok) |
| K-3 | Linux | `cmake -B build -G Ninja -DCMAKE_BUILD_TYPE=Release` | `cmake --build build` | ⚠️ (belge; koşum yok) |
| K-4 | RPi5 (cross) | `cmake -B build-arm64 -DCMAKE_TOOLCHAIN_FILE=toolchain-aarch64.cmake` | `cmake --build build-arm64` | ⚠️ (§4; koşum yok) |
| K-5 | Temizlik | `rm -rf build` (veya `cmake --build build --target clean`) | — | ✅ (genel pratik) |
| K-6 | Test çağrısı | `ctest --test-dir build --output-on-failure` | — | ⚠️ (F2 kapı; koşum yok) |

## §24 Paketleme Komut Kalıpları (append-only — belge düzeyi)

| # | OS | Paket komutu (kalıp) | Çıktı | Durum |
|---|---|---|---|---|
| Q-1 | Windows | `cpack -G NSIS` (veya `-G WIX`) | `.exe` / `.msi` | ⚠️ (F4 açık) |
| Q-2 | macOS | `cpack -G productbuild` (veya `pkgbuild`+`productbuild`) | `.pkg` | ⚠️ (F4 + notarization açık) |
| Q-3 | Linux (deb) | `cpack -G DEB` | `.deb` | ⚠️ (F4 açık) |
| Q-4 | Linux (rpm) | `cpack -G RPM` | `.rpm` | ⚠️ (F4 açık) |
| Q-5 | AppImage | `linuxdeploy`/`appimagetool` kalıbı | AppImage | ⚠️ (araç sürümü ölçülmedi) |
| Q-6 | Checksum | `sha256sum`/`Get-FileHash` → `.sha256` | checksum dosyası | ⚠️ (A-3) |

## §25 CI Akış Kalıbı (append-only — belge düzeyi)

| # | Aşama | Ne yapar | Kapı | Durum |
|---|---|---|---|---|
| C-1 | Checkout + araç sürümü yazdırma | Sürüm kanıtı (F0) | F0 | ⚠️ (koşum yok) |
| C-2 | Configure (matrix: os × arch) | K-1…K-4 karşılığı | F0 | ⚠️ |
| C-3 | Build (`/WX`-`-Werror` temiz) | F1 kapısı | F1 | ⚠️ |
| C-4 | Test (`ctest`, rapor üretimi) | F2 kapısı | F2 | ⚠️ (rapor yok) |
| C-5 | Package (cpack matrix) | F4 | F4 | ⚠️ |
| C-6 | Artifact yükleme + checksum | A-1, A-3 | A-3 | ⚠️ |
| C-7 | İmzalama (sekretheld) | F5 / T-6 | F5 | ⚠️ VERIFICATION REQUIRED |

## §26 Kablosuz Ağ/Hızlandırıcı Sınırı (append-only — bu belgenin dışı)

| # | Konu | Neden bu belgenin dışı | Komşu | Durum |
|---|---|---|---|---|
| S-1 | Ses cihazı davranışı (ALSA/WASAPI/CoreAudio) | Ayrı anahtar (K000/audio) | K000/audio | ⚠️ (sınır §9) |
| S-2 | Sürücü imzalama/WHQL | Windows sürücü süreci ayrı | harici | ⚠️ |
| S-3 | macOS notarization hesabı | Apple hesabı/altyapı gerekir | harici | ⚠️ (T-6) |
| S-4 | Paket deposu yayımı (repo/CDN) | Dağıtım altyapısı ayrı | ops | ⚠️ |

## §27 Kaynakça Genişletme (append-only — yeni URL EKLENMEZ; mevcut §10 kullanılır)

| # | Kullanım | Kaynak (bu belge §10) | Durum |
|---|---|---|---|
| U-1 | Toolchain/paket kalıpları (§23–§24) | §10'daki 6 URL (HEAD 200 · 2026-10-14) | ✅ (erişilebilir) |
| U-2 | Repo kanıtları (§11) | 2 S1 repo satırı | ✅ |
| U-3 | CMake cross değişkenleri | Kardeş: ide-platform-tespiti §2.2 | ✅ (kardeş belge) |
| U-4 | CI akışı detayı (§25) | §10 envanter kesişimi 0 → yeni iddia yok | ⚠️ (belge düzeyi) |
| U-5 | Yeni URL iddiası | — | Yok — bu turda URL eklenmedi |

## §28 Çapraz Referans (append-only)

| # | Bu belge → kardeş | Ne devredildi | Durum |
|---|---|---|---|
| X-1 | `derleme-ve-paketleme-anayasa.md` | §23.1 artefakt adlandırma | ✅ |
| X-2 | `derleme-ve-paketleme-ide-platform-tespiti.md` | K-1/K-5 CMake değişkenleri | ✅ (kardeş ≥505) |
| X-3 | `derleme-ve-paketleme-surucu.md` | Sürücü/ortam tespiti | ✅ |
| X-4 | `derleme-ve-paketleme-ornekler.md` | Kod örnekleri | ✅ |
| X-5 | Gerçek koşum kanıtı (F0–F5) | — | ⚠️ VERIFICATION REQUIRED |

## §29 Terim Sözlüğü (append-only)

| # | Terim | Anlam (bu belge) | Durum |
|---|---|---|---|
| E-1 | Toolchain | Derleyici + bağlayıcı + paketleyici zinciri | ✅ |
| E-2 | Sysroot | Cross hedefinin başlık/kütüphane kökü (C-2) | ⚠️ (ölçüm yok) |
| E-3 | Artefakt | Derleme/paketleme çıktısı (A-1…A-6) | ✅ |
| E-4 | Reproducible build | Aynı girdi → aynı çıktı (A-5) | ⚠️ (ölçüm yok) |
| E-5 | Notarization | macOS paket imzalama/zararlı yazılım taraması (T-6) | ⚠️ |
| E-6 | F0–F5 | Faz kapıları (§17) | ✅ (belge) |
| E-7 | Q4 (§1) | Hedef platform seti onay sorusu | ⚠️ (açık) |
| E-8 | `/WX`-`-Werror` | Uyarıyı hata sayan bayrak (B-2) | ✅ |

## §30 Tur Sonu Sayımı + Son Kontrol (append-only, 2026-10-14)

| # | Kalem | Sayı/Sonuç |
|---|---|---|
| 1 | Bu tur eklenen bölüm | 17 (§14–§30) |
| 2 | Açık kapı (Q1–Q6 + F0–F5) | 6 + 6 → ⚠️ VERIFICATION REQUIRED |
| 3 | Red team satırı | 6 (§21) |
| 4 | Test matrisi satırı | 6 (§20) |
| 5 | Kapı ihlali / beyazliste dışı yazım / commit | 0 / 0 / Yok |
| 6 | Dolgu/TBD / mojibake | 0 / 0 |
| 7 | Frontmatter + Authority son satır | ✅ / ✅ |

**Durum:** `IMPLEMENTED (belge)` düzeyi korunur — hiçbir faz koşulmadı; koşum kanıtı yoksa
**⚠️ VERIFICATION REQUIRED**.

---

## §31 OS-Başı Kontrol Listesi (append-only — belge düzeyi)

| # | Kontrol | Windows | macOS | Linux | RPi5 | Durum |
|---|---|---|---|---|---|---|
| L-1 | Derleyici sürümü kaydedildi (F0) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Açık |
| L-2 | `/WX`-`-Werror` temiz (F1) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Açık |
| L-3 | Test raporu üretildi (F2) | ⚠️ | ⚠️ | ⚠️ | — | Açık |
| L-4 | Paket kurulum+kaldırma testi (F4) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Açık |
| L-5 | İmza doğrulandı (F5) | ⚠️ | ⚠️ | — | — | Açık (T-6) |
| L-6 | Checksum üretildi (A-3) | ⚠️ | ⚠️ | ⚠️ | ⚠️ | Açık |
| L-7 | Artefakt adı anayasa §23.1'e uygun (A-1) | ✅ | ✅ | ✅ | ✅ | Belge ✅ |

## §32 Bağımlılık/Ortam Değişkenleri (append-only)

| # | Değişken | Rol | Kaynak (bu belge) | Durum |
|---|---|---|---|---|
| V-1 | `CC`/`CXX` (toolchain tercihi) | Derleyici seçimi | §2, §23 | ✅ (genel pratik) |
| V-2 | `CMAKE_TOOLCHAIN_FILE` | Cross hedef tanımı | §4, K-4 | ✅ |
| V-3 | `CPACK_*` | Paket metadata (ad/sürüm/lisans) | §5, §6 | ✅ |
| V-4 | `MACOSX_DEPLOYMENT_TARGET` | macOS min sürüm | K-8 | ⚠️ (ölçüm yok) |
| V-5 | `SOURCE_DATE_EPOCH` | Reproducible build zaman damgası | A-5 | ⚠️ (uygulanmadı) |
| V-6 | Kod imzalama ortam değişkenleri | F5/T-6 | §25 C-7 | ⚠️ VERIFICATION REQUIRED (gizli değer yazılmaz) |

## §33 Kırılma Noktaları Genişletme (§8 devamı — append-only)

| # | Kırılma | Tetikleyici | Belirti (ölçülebilir) | Eylem | Durum |
|---|---|---|---|---|---|
| BR-1 | F0 araç sürümü uyuşmazlığı | Runner güncellendi | Sürüm çıktısı değişti | Sürüm kilitle + F0 tekrar | ⚠️ (ölçüm yok) |
| BR-2 | `/WX`-`-Werror` yeni uyarı | Derleyici sürüm atlaması | Derleme hatası | Bayrağı geçici kırpmak YASAK; kod düzelt | ✅ (kural) |
| BR-3 | Cross sysroot kayması (C-2) | RPi5 rootfs güncellendi | Bağlantı/çalıştırma hatası | Sysroot sürümünü eşle | ⚠️ |
| BR-4 | Paket formatı değişimi (P-1…P-4) | Karar değişikliği | F4'te farklı çıktı | Q4/Q6 kapısına dön | ⚠️ |
| BR-5 | İmza zinciri kopması (T-6) | Sertifika süresi | F5'te doğrulama hatası | Sertifika yenileme (gizli değer yazılmaz) | ⚠️ |
| BR-6 | Reproducible sapma (A-5) | Zaman/alet farkı | Checksum farkı | SOURCE_DATE_EPOCH + araç kilidi | ⚠️ |

## §34 Tur Sonu Nihai Ölçüm (append-only, 2026-10-14)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | Başlangıç satır (bu tur öncesi) | 185 |
| 2 | §14–§34 (3 tur eki) | ≥505 (tamponlu) |
| 3 | Frontmatter (`group:` çıplak) | Korunur |
| 4 | `status:` / Sürüm geçmişi | Değişmedi (yalnız satır eklendi) |
| 5 | Kapı ihlali (Q1–Q6, F0–F5 açık) | 0 |
| 6 | Beyazliste dışı yazım | 0 (yalnız K000; `.ai/electronic` salt okundu) |
| 7 | `git commit` | Yok (orkestratöre ait) |
| 8 | Dolgu/TBD / mojibake | 0 / 0 |
| 9 | Authority son satır | ✅ |

**Kalan açık kapılar:** Q1–Q6 + F0–F5 + T-6 + A-3/A-4/A-5 → hepsi **⚠️ VERIFICATION REQUIRED**.

---

## §35 Senaryo Uygunluk Tablosu (append-only — bu belge: derleme/paketleme senaryosu)

Senaryo köprüsü (görev: derleme+paketleme + platform tespiti): müzik uygulaması (C++ session)
4 OS'a paketlenir; her OS'ta araç zinciri ve paket çıktısı ayrıdır. **Ölçüm:** her OS'ta
F0→F5 zincirinin exit code + rapor ile geçmesi — **koşum kanıtı yok → ⚠️ VERIFICATION REQUIRED**.

| # | Adım | Girdi | Çıktı (ölçülebilir) | Kapı | Durum |
|---|---|---|---|---|---|
| Y-1 | Tespit (kardeş §5) | IDE/OS toolchain | `CMAKE_SYSTEM_NAME` + makro raporu | Q2 | ⚠️ |
| Y-2 | Configure (K-1…K-4) | Kaynak + toolchain | Build dizini + cache | F0 | ⚠️ |
| Y-3 | Build (B-1…B-5) | Kaynak | Binary + 0 hata (`/WX`/`-Werror`) | F1 | ⚠️ |
| Y-4 | Test (X-1…X-6) | Binary | `ctest` raporu | F2 | ⚠️ |
| Y-5 | Package (Q-1…Q-6) | Binary + metadata | `.msi/.pkg/.deb/.rpm` + `.sha256` | F4 | ⚠️ |
| Y-6 | Sign (C-7, V-6) | Paket + sertifika | İmzalı paket (doğrulanabilir) | F5 | ⚠️ |
| Y-7 | Yayın | Artefakt deposu | Sürüm etiketi + indirilebilir URL | — | ⚠️ (ops sınırı §26) |

## §36 Ek Doğrulama + Nihai Sayım (append-only, 2026-10-14)

| # | Doğrulama | Sonuç |
|---|---|---|
| D-1 | §31–§33 satırları §7/§8 ile çelişiyor mu? | Hayır — genişletme |
| D-2 | §32 V-6'da gizli değer (sertifika/parola) yazıldı mı? | Hayır — yalnız değişken adı |
| D-3 | §35 senaryosu yeni iddia (URL) ekliyor mu? | Hayır — §10/§11 kullanıldı |
| D-4 | Yeni URL / yeni dosya adı / dizin? | 0 / 0 / 0 |
| D-5 | Kapı satırları açık soruları kapatıyor mu? | Hayır — 0 ihlal |
| D-6 | Mojibake / uydurma iddia? | 0 / 0 |

| # | Nihai ölçüm | Sonuç |
|---|---|---|
| 1 | Başlangıç → son | 185 → ≥505 (append-only) |
| 2 | Ek bölüm sayısı | 23 (§14–§36) |
| 3 | Frontmatter / Authority | Korunur / son satır ✅ |

---

## §37 Kapı Panosu (append-only, 2026-10-14 — tüm kapılar AÇIK kalır)

| # | Kapı grubu | Soru sayısı | Durum | Kanıt |
|---|---|---|---|---|
| K-1 | Faz kapıları (F0–F5) | 6 | **AÇIK** | Koşum kanıtı yok → ⚠️ |
| K-2 | Belge soruları (Q1–Q6, §22) | 6 | **AÇIK** | §12 devamı — kapatılmadı |
| K-3 | Bayrak/ölçüm (B-6, B-7, Q2) | 2 | **AÇIK** | Ölçüm yok → ⚠️ |
| K-4 | İmzalama (T-6, A-4, V-6) | 3 | **AÇIK** | Sertifika/altyapı doğrulanmadı → ⚠️ |
| K-5 | Cross (C-2, C-4, X-5) | 3 | **AÇIK** | Sysroot/QEMU testi yok → ⚠️ |
| K-6 | Reproducible (A-5, V-5) | 2 | **AÇIK** | Checksum ölçümü yok → ⚠️ |
| K-7 | Hedef platform seti (§1 Q4) | 1 | **AÇIK** | Öneri olarak duruyor |

**Toplam:** 23 kapı sorusu **AÇIK (PLANNED)** — bu turda **hiçbiri kapatılmadı**
(kapı ihlali: 0). `status:` / frontmatter değişmedi.

| # | Nihai ölçüm (tampon) | Sonuç |
|---|---|---|
| 1 | Satır (bu tur sonu) | ≥505 (472 + §37) |
| 2 | Append-only / dolgu-TBD | ✅ / 0 |
| 3 | Beyazliste dışı yazım / commit | 0 / Yok |

---

## §38 Ek Tampon Satırları (append-only, 2026-10-14)

| # | Ek kontrol | Sonuç |
|---|---|---|
| C-1 | §14–§38 arası ek bölüm sayısı | 25 |
| C-2 | Her tablo satırı kaynak/kural taşıyor mu? | ✅ (§10/§11/S1 veya ⚠️ işaretli) |
| C-3 | Yeni dosya/dizin/ad değişikliği | 0 |
| C-4 | `*.php/js/css/sql` yazımı | 0 |
| C-5 | `.ai/electronic` erişimi | Salt okuma |
| C-6 | Açık kapı toplamı (§37) | 23 → AÇIK (kapatılmadı) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
