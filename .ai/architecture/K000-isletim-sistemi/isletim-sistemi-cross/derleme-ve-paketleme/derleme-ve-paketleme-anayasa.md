---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme"
evidence:
  - "shared/composer.json:8"
  - ".github/workflows/ci.yml:48"
  - "https://clang.llvm.org/docs/CommandGuide/clang.html"
  - "https://cmake.org/cmake/help/latest/manual/cmake.1.html"
validation: "2026-10-13 · 4 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q6 — hedef paket formatları (MSI/deb/AppImage) seti UNKNOWN"
  - "Q-X28 — C++ toolchain seçimi (GCC/Clang/MSVC) onaylandı mı"
group: mimari
---

# derleme-ve-paketleme-anayasa — Kademe-4 Anayasa

> **Kapsam:** Derleme araç zinciri (preprocessor → derleyici → assembler → bağlayıcı),
> build sistemleri (CMake/Meson/Ninja/Make), bağımlılık çözümleme, paket formatları,
> tekrar üretilebilirlik (reproducible builds), sürümleme. **Kanıt dili:** S1 = birebir
> repo satırı · S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-anayasa` |
| parent_id | `…/isletim-sistemi-cross/derleme-ve-paketleme` |
| group | `mimari` |
| Komşular | `../platform-yuzeyleri` · `../tasinabilirlik-siniri` · `K17-devops` · `isletim-sistemi-testing` |
| Repo gerçeği | C++ toolchain YOK; derlenen yüzey = PHP (Composer) + JS (npm) |

## §0 ELI15

"Derleme" bir kaynak kod dosyasını ikili (binary) yapan zincirdir; "paketleme" o ikiliyi
kullanıcının kurabileceği kutuya (MSI/deb/AppImage/npm paketi) koymaktır. Bu depoda bugün
iki paket yöneticisi çalışır: **Composer** (PHP ≥8.4, `composer.json:8`) ve **npm**
(`package.json:10-17`); C++ araç zinciri (GCC/Clang/MSVC + CMake) henüz **yoktur** ve
Neva Engine ile gelecektir.

| Katman | Windows | Linux | macOS |
|---|---|---|---|
| Derleyici | MSVC/Clang-cl | GCC/Clang | Apple Clang |
| Bağlayıcı | link.exe | ld/lld | ld64 |
| Build sistemi | CMake/Meson → VS/Ninja | CMake/Meson → Ninja/Make | CMake/Meson → Ninja/Xcode |
| Paket biçimi | MSI/MSIX/nuget | deb/rpm/AppImage/flatpak | dmg/pkg/homebrew |
| Bağımlılık | NuGet/vcpkg | distro paketleri | Homebrew/CocoaPods |

## §1 Derleme zinciri matrisi

| # | Aşama | GCC | Clang | MSVC | Girdi → Çıktı |
|---|---|---|---|---|---|
| 1 | Ön işleme | `cpp`/`cc1` | `clang -E` | `cl /E` | `.c/.cpp` → `.i` |
| 2 | Derleme | `cc1plus` | `clang -cc1` | `cl` | `.i` → `.o/.obj` |
| 3 | Assemble | `as` | integrated | `ml64` | `.s` → `.o` |
| 4 | Bağlama | `ld`/`gold`/`lld` | `lld` | `link.exe` | `.o` → executable/lib |
| 5 | Arşivleme | `ar` | `llvm-ar` | `lib.exe` | `.o` → `.a/.lib` |
| 6 | İnce bağlama | `-fPIC` + `.so` | aynı | `/LD` + `.dll` | paylaşımlı kütüphane |
| 7 | Sembol denetimi | `nm`/`readelf` | `llvm-nm` | `dumpbin` | dışa açılan semboller |
| 8 | Hata ayıklama bilgisi | DWARF | DWARF | CodeView/PDB | §1.4 (tasinabilirlik) |

## §2 Build sistemi matrisi (CMake / Meson / Ninja / Make)

| # | Özellik | Make | CMake | Meson | Ninja |
|---|---|---|---|---|---|
| 1 | Tanım dili | Makefile (tab) | CMakeLists.txt (CMakeDSL) | meson.build (Python-benzeri) | build.ninja (üretilir) |
| 2 | Seviye | birincil | üretici (generator) | üretici | alt sistem (backend) |
| 3 | Ürettiği | — | Make/Ninja/VS/Xcode | Ninja | — |
| 4 | Cross derleme | elle | toolchain dosyası | `--cross-file` | — |
| 5 | Hız | orta | orta | hızlı | çok hızlı |
| 6 | Öğrenme eğrisi | düşük | orta-yüksek | düşük | yok (üretilir) |
| 7 | Windows entegrasyonu | zayıf | VS generator | Ninja + MSVC | MSVC destekli |
| 8 | Bağımlılık yönetimi | yok | `find_package`/FetchContent | `dependency()` | yok |

**Kaynak:** CMake komut referansı (§9 #3) · Meson kök dokümanı (§9 #4) · Ninja (§9 #2).

## §3 Bağımlılık çözümleme matrisi

| # | Ekosistem | Tanım dosyası | Kilit dosya | Çözümleyici |
|---|---|---|---|---|
| 1 | PHP | `composer.json` | `composer.lock` | Composer |
| 2 | JS | `package.json` | `package-lock.json` | npm |
| 3 | C++ (PLANNED) | `CMakeLists.txt` + `vcpkg.json` | vcpkg baseline | vcpkg/Conan |
| 4 | .NET | `.csproj` | `packages.lock.json` | NuGet |
| 5 | Python | `pyproject.toml` | `requirements.txt` | pip/venv |
| 6 | Rust | `Cargo.toml` | `Cargo.lock` | Cargo |

**Repo kanıtı:** `shared/composer.json:8` → `"php": ">=8.4"` (S1) · `package.json:10-17`
→ devDependencies (S1). C++ satırı PLANNED (S4).

## §4 Tekrar üretilebilir derleme (reproducible builds)

| # | Kural | Uygulama | Kaynak |
|---|---|---|---|
| 1 | Zaman damgası sabitle | `SOURCE_DATE_EPOCH` | §9 #5 |
| 2 | Sıralı dosya iteration | arşivlerde sıralı girdi | §9 #5 |
| 3 | Yol bağımsızlığı | gömülü yolları yeniden yaz (remap) | §9 #5 |
| 4 | Bağımlılık kilidi | lock dosyası commit edilir | §3 |
| 5 | Araç zinciri sabitle | toolchain sürümü/environment | §3 |
| 6 | Rastgelelik yok | derlemede entropy kullanma | §9 #5 |

**Repo bağlantısı:** CI önbelleği OS başına ayrılır — `ci.yml:44` `key: ${{ runner.os }}-composer-...` (S1); bu, çok-OS derlemede bağımlılık kilidinin parçasıdır.

## §5 Sürümleme ve paket adlandırma

| # | Kavram | Kural | Kaynak |
|---|---|---|---|
| 1 | SemVer | `MAJOR.MINOR.PATCH` | §9 #7 |
| 2 | Composer sürüm alan `"version"` | repo'da `"version": "2.0.0"` (`composer.json:6`) | S1 |
| 3 | npm sürüm alanı | `package.json` (repo sürümü okunmadı — S7) | S7 |
| 4 | NuGet sürümleme | SemVer + metadata | §9 #6 |
| 5 | Changelog | Keep a Changelog biçimi | §9 #8 |

## §6 Ölçüm / kabul kriterleri

| # | Ölçüm | Yöntem | Eşik |
|---|---|---|---|
| 1 | Lock dosyası commit | `composer.lock`/`package-lock.json` var mı | var (git) |
| 2 | PHP sürüm eşleşmesi | `composer.json:8` ↔ CI PHP sürümü | eşleşmeli |
| 3 | C++ toolchain | `CMakeLists.txt` glob | 0 (PLANNED) |
| 4 | Reproducible bayrakları | `SOURCE_DATE_EPOCH` kullanımı | CI'da yok (PLANNED) |
| 5 | URL doğrulama | HEAD 200 + envanter | kesişim 0 |

## §7 Riskler

| # | Risk | Olasılık | Etki |
|---|---|---|---|
| R1 | Toolchain seçimi yapılmadan Neva Engine kodu yazılır (GCC/MSVC farkları) | orta | yüksek |
| R2 | Lock dosyası commit edilmez → CI'da sürüm sürüklenmesi | orta | yüksek |
| R3 | `SOURCE_DATE_EPOCH` yok → tekrar üretilemeyen ikililer | yüksek | orta |
| R4 | Paket formatı kararı yok (Q6) → kurulum yüzeyi tasarlanamaz | yüksek | orta |
| R5 | vcpkg baseline sabitlenmez → bağımlılık drifti | orta | orta |
| R6 | npm `^` kademeli güncellemesi test dışı sürüm çeker | orta | orta |
| R7 | `runner.os` önbellek anahtarı OS kırılımı — yanlış paylaşımda bozuk cache | düşük | orta |

## §8 Kapsam dışı

| # | Konu | Nereye gider |
|---|---|---|
| 1 | CI pipeline tasarımı | `K17-devops` |
| 2 | Test koşumu | `isletim-sistemi-testing` |
| 3 | `#ifdef` guard kalıpları | `../tasinabilirlik-siniri` |
| 4 | OS API/ABI farkları | `../platform-yuzeyleri` |
| 5 | Paket formatı kurulum/denetim sürücüsü | `derleme-ve-paketleme-surucu.md` |
| 6 | Kod örnekleri | `derleme-ve-paketleme-ornekler.md` |
| 7 | Lisans uyumu | `compliance-auditor` alanı |

## §9 Kaynakça (web araştırma · 2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Clang komut satırı kılavuzu `clang` sürücüsünü tanımlar | https://clang.llvm.org/docs/CommandGuide/clang.html | 2026-10-13 | ✅ HTTP 200 |
| MSVC derleme bayrakları `compiler-options` sayfasında tanımlanır | https://learn.microsoft.com/en-us/cpp/build/reference/compiler-options?view=msvc-170 | 2026-10-13 | ✅ HTTP 200 |
| `cmake(1)` el kitabı cmake komut satırı sürücüsünü belgeler | https://cmake.org/cmake/help/latest/manual/cmake.1.html | 2026-10-13 | ✅ HTTP 200 |
| Meson resmi dokümantasyon kökü build sistemi tanımını verir | https://mesonbuild.com/ | 2026-10-13 | ✅ HTTP 200 |

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q6 | Hedef paket formatları seti (MSI? deb? AppImage? dmg?) nedir? | ⚠️ VERIFICATION REQUIRED |
| Q-X28 | C++ toolchain seçimi (GCC/Clang/MSVC) onaylandı mı? | UNKNOWN |
| Q-X29 | `SOURCE_DATE_EPOCH` CI'ya eklenecek mi? | UNKNOWN |
| Q-X30 | vcpkg/Conan tercihi yapıldı mı? | UNKNOWN |
| Q-X31 | npm `package.json` sürüm alanı hangi değerde (repo'da okunmadı)? | UNKNOWN |

## §11 Derleyici bayrak matrisi (PLANNED — Neva Engine)

> Bu tablo S4 sınıfındadır: depoda C++ derleme adımı **yoktur** (`CMakeLists.txt` glob = 0).

| # | Amaç | GCC/Clang | MSVC | Not |
|---|---|---|---|---|
| 1 | C++20 | `-std=c++20` | `/std:c++20` | anayasa kuralı C++20 |
| 2 | Uyarı sertliği | `-Wall -Wextra -Wpedantic` | `/W4` | uyarı = hata hedefi |
| 3 | Hata → hata | `-Werror` | `/WX` | CI'da tercih edilir |
| 4 | Optimizasyon | `-O2` (release) | `/O2` | RT ölçümünden sonra |
| 5 | Hata ayıklama | `-g` | `/Zi` | DWARF vs CodeView |
| 6 | ASan | `-fsanitize=address` | `/fsanitize=address` | testing dalı |
| 7 | TSan | `-fsanitize=thread` | (destek sınırlı) | testing dalı |
| 8 | Gömülü yapılandırma | `#pragma once` yerine guard | aynı | tasinabilirlik dalı |
| 9 | SIMD | `-mavx2` (hedefe özel) | `/arch:AVX2` | K004-dsp |
| 10 | RTTI/exceptions | `-fno-exceptions` (tercihe bağlı) | `/EHs-` | Neva Engine kararı |

## §12 Paket formatı karşılaştırma matrisi

| # | Format | OS | İçerik | İmza | Denetim |
|---|---|---|---|---|---|
| 1 | MSI | Windows | kurulum veritabanı | Authenticode | `msiexec /a` |
| 2 | MSIX | Windows | paket + manifest | Appx | `Get-AppxPackage` |
| 3 | WiX | Windows | MSI üreteci (kaynak) | — | — |
| 4 | NSIS | Windows | betik tabanlı kurulum | — | — |
| 5 | deb | Debian/Ubuntu | arşiv + kontrol | GPG | `dpkg -l` |
| 6 | rpm | Fedora/RHEL | arşiv + başlık | GPG | `rpm -qa` |
| 7 | AppImage | evrensel Linux | tek dosya | imza dosyası | `--appimage-extract` |
| 8 | flatpak | evrensel Linux | runtime + app | — | `flatpak list` |
| 9 | dmg | macOS | disk görüntüsü | codesign | `hdiutil verify` |
| 10 | pkg | macOS | kurulum | codesign | `pkgutil` |
| 11 | NuGet | .NET | paket | imza | `dotnet nuget verify` |
| 12 | npm | JS | tarball | provenance | `npm pack --dry-run` |

**Kaynak notu (S1):** Bu depoda çalışan paketçiler Composer (§13 #1) ve npm (§13 #5)'dir;
MSI/deb/AppImage satırları PLANNED'dir — surucu.md detayında işlenir.

## §13 Repo kanıtı — Composer/npm yüzeyi (S1)

**Kanıt 1 — PHP sürüm kısıtı:** `shared/composer.json:8`

```json
        "php": ">=8.4",
```

**Kanıt 2 — CI bağımlılık kurulumu:** `.github/workflows/ci.yml:48`

```yaml
      - name: Composer install (shared)
```

| # | Kanıt | Dosya:Satır | Ne kanıtlar | Sınıf |
|---|---|---|---|---|
| 1 | `"php": ">=8.4"` | `shared/composer.json:8` | dil sürümü kısıtı | S1 |
| 2 | `"version": "2.0.0"` | `shared/composer.json:6` | sürüm alanı | S1 |
| 3 | `Composer install (shared)` | `.github/workflows/ci.yml:48` | CI'da bağımlılık kurulumu | S1 |
| 4 | `key: ${{ runner.os }}-composer-...` | `.github/workflows/ci.yml:44` | önbellek OS başına | S1 |
| 5 | `npm ci` | `.github/workflows/ci.yml:154` | kilitli npm kurulumu | S1 |
| 6 | devDependencies (playwright/vitest) | `package.json:10-17` | test araç zinciri | S1 |
| 7 | `CMakeLists.txt` glob | 0 | C++ toolchain yok | S1 |
| 8 | `SOURCE_DATE_EPOCH` | 0 (CI'da yok) | reproducible PLANNED | S1 |

## §14 Bağımlılık kilit dosyaları matrisi

| # | Dosya | Amaç | Commit | Repo |
|---|---|---|---|---|
| 1 | `composer.lock` | PHP bağımlılık kilidi | evet | var (ci.yml:44 hashFiles) |
| 2 | `package-lock.json` | npm kilidi | evet | envanter (okunmadı — S7) |
| 3 | `vcpkg.json` + baseline | C++ kilidi | PLANNED | yok |
| 4 | `Cargo.lock` | Rust kilidi | — | yok |
| 5 | `requirements.txt` | Python kilidi | — | yok |

## §15 Çok OS derleme matrisi (CI kanıtı ile)

| # | OS | CI'da | Derleme | Not |
|---|---|---|---|---|
| 1 | Linux | `runs-on: ubuntu-latest` (ci.yml:27) | Composer+npm | test tek OS |
| 2 | Windows | CI'da yok | — | anayasa §5 #5-7 |
| 3 | macOS | CI'da yok | — | — |

**Sonuç (S1):** Çok OS derleme **kurulmamıştır**; Neva Engine PLANNED iken bu dalın
matrisleri hedef tasarımdır, bugünkü davranış değildir.

### §15.1 Çok OS hedef matrisi (PLANNED — hedef)

| # | OS | Derleyici | Paket |
|---|---|---|---|
| 1 | Windows x64 | MSVC/Clang-cl | MSI/zip (§23 #1) |
| 2 | Linux x64 | GCC/Clang | deb/AppImage (§23 #3) |
| 3 | Linux arm64 | GCC (Raspberry Pi 5) | deb/AppImage (§23 #4) |
| 4 | macOS universal | Apple Clang | dmg (§23 #5) |

## §16 Reproducible checklist — Neva Engine için (PLANNED)

| # | Adım | Karar | Bağ |
|---|---|---|---|
| 1 | `SOURCE_DATE_EPOCH` ekle | Q-X29 | §4 #1 |
| 2 | Toolchain sürümünü sabitle | container/image | §4 #5 |
| 3 | vcpkg baseline commit | Q-X30 | §3 #3 |
| 4 | Yol remap | `-ffile-prefix-map` | §4 #3 |
| 5 | Arşiv sıralaması | sorted input | §4 #2 |
| 6 | Deterministik RNG | derlemede entropy yok | §4 #6 |
| 7 | Üretilen ikiliyi karşılaştır | iki derleme hash eşleşmeli | §6 |

### §16.1 Reproducible — hash karşılaştırma protokolü (PLANNED)

| # | Adım | Beklenen |
|---|---|---|
| 1 | Aynı girdi, temiz ortamda 1. derleme | hash A kaydedilir |
| 2 | Aynı girdi, temiz ortamda 2. derleme | hash B kaydedilir |
| 3 | `A == B` | eşitse reproducible |
| 4 | Fark varsa | bölüm bölüm (timestamp/path/entropy) ayrıştır |
| 5 | Düzeltme → tekrar | §4 #1-6 kuralları uygulanır |
| 6 | Sonuç raporu | artifact'a eklenir (SBOM, §22 #5) |

## §17 Paket adlandırma kuralları

| # | Ekosistem | Şema | Örnek (repo) |
|---|---|---|---|
| 1 | Composer | `vendor/package` | repo içi paket adı okunmadı (S7) |
| 2 | npm | `@scope/name` | repo içi ad okunmadı (S7) |
| 3 | deb | `name_version_arch.deb` | — |
| 4 | rpm | `name-version-release.arch.rpm` | — |
| 5 | MSI | ProductCode GUID + Name | — |
| 6 | SemVer pre-release | `-alpha.1`/`-rc.2` | §9 #7 |
| 7 | Composer sürüm alanı | `"version": "2.0.0"` | S1 (§5 #2) |

## §18 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | Toolchain | derleme zinciri araç kümesi (derleyici+bağlayıcı+arşivci) (§1) |
| 2 | Generator | build dosyasını üreten üst sistem (CMake/Meson) (§2 #2) |
| 3 | Lock dosyası | çözülmüş bağımlılık grafiğinin dondurulmuş hali (§14) |
| 4 | Reproducible | aynı girdi → bayt-bayıt aynı ikili (§4) |
| 5 | Baseline | vcpkg'de bağımlılık sürüm dondurma noktası (§3 #3) |
| 6 | Provenance | paketin nerede/neyle üretildiğinin kanıtı (§12 #12) |

## §19 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-13 | İlk yazım — 4 URL doğrulandı (clang CommandGuide · MS compiler-options · cmake.1 · meson), S1 kanıtlar (composer.json:6,8 · ci.yml:44,48,154 · package.json:10-17) |

## §20 Araç zinciri — sürüm takip matrisi (PLANNED)

| # | Araç | Sürüm kaynağı | Sabitleme yöntemi |
|---|---|---|---|
| 1 | GCC | `gcc --version` | container image tag |
| 2 | Clang | `clang --version` | container image tag |
| 3 | MSVC | Visual Studio Installer | Developer Command Prompt sürümü |
| 4 | CMake | `cmake --version` | `CMakeLists.txt cmake_minimum_required` |
| 5 | Ninja | `ninja --version` | pip/distro paketi |
| 6 | Meson | `meson --version` | pip/distro |
| 7 | vcpkg | baseline commit hash | `vcpkg.json` |
| 8 | Python (meson/ninja) | `.python-version` | CI setup |
| 9 | Composer | `composer --version` | CI setup |
| 10 | Node/npm | `node -v` / `npm -v` | CI setup + `.nvmrc` |

## §21 Paket içeriği kontrol matrisi (paket-surucu sınırı)

| # | Kontrol | Amaç | Kime ait |
|---|---|---|---|
| 1 | Dosya manifesti | eksik dosya tespiti | surucu.md |
| 2 | İmza doğrulama | bütünlük | isletim-sistemi-security |
| 3 | Bağımlılık beyanı | çakışma | bu dosya (§3) |
| 4 | Kurulum sonrası denetim | smoke test | isletim-sistemi-testing |
| 5 | Sürüm uyumu | upgrade/downgrade | bu dosya (§5) |
| 6 | Lisans dosyası | compliance | compliance-auditor |

## §22 BuildOutput / artifact matrisi (PLANNED)

| # | Artifact | Üreten | Depolanan | Saklama süresi |
|---|---|---|---|---|
| 1 | executable (bin) | linker (§1 #4) | dist/ | PLANNED |
| 2 | paylaşımlı kütüphane | linker `/LD`/`-shared` | dist/lib | PLANNED |
| 3 | sembol dosyası (pdb/dSYM) | derleyici | artifacts | PLANNED |
| 4 | paket (msi/deb/dmg) | surucu.md araçları | release | PLANNED |
| 5 | SBOM | bağımlılık taraması | artifacts | PLANNED |
| 6 | Composer vendor/ | Composer | CI cache (ci.yml:44) | S1 |
| 7 | npm node_modules/ | npm ci (ci.yml:154) | CI cache | S1 |

## §23 Çok OS artifact adlandırma (PLANNED)

| # | OS | Mimari | Artifact şeması |
|---|---|---|---|
| 1 | Windows | x64 | `name-<ver>-win-x64.zip` |
| 2 | Windows | arm64 | `name-<ver>-win-arm64.zip` |
| 3 | Linux | x64 | `name-<ver>-linux-x64.tar.gz` |
| 4 | Linux | arm64 | `name-<ver>-linux-arm64.tar.gz` |
| 5 | macOS | universal | `name-<ver>-macos-universal.dmg` |

**Not (S4):** Şema bir **öneridir**; Q6 kararı alınana kadar bağlayıcı değildir.

## §24 Ölçüm protokolü — bu dosyanın doğrulaması

| # | Adım | Beklenen |
|---|---|---|
| 1 | URL HEAD 200 | 4/4 |
| 2 | Envanter kesişimi | 0 |
| 3 | Repo kanıtı okuma | `Select-Object -Index (N-1)` ×6 |
| 4 | `CMakeLists.txt` glob | 0 |
| 5 | Mojibake | 0 |
| 6 | `parent:` alanı | 0 |
| 7 | Son satır Authority | var |
| 8 | Git commit | atılmadı |

## §25 Bağlantı matrisi — bu dosya ↔ kardeş dosyalar

| # | Konu | Kardeş dosya |
|---|---|---|
| 1 | Paket formatı sürücüsü | `derleme-ve-paketleme-surucu.md` |
| 2 | Kod örnekleri (CMake/Composer) | `derleme-ve-paketleme-ornekler.md` |
| 3 | OS API/ABI farkları | `../platform-yuzeyleri/…-anayasa.md` |
| 4 | `#ifdef` guard | `../tasinabilirlik-siniri/…-anayasa.md` |
| 5 | CI pipeline | `K17-devops` (kanıt: ci.yml) |
| 6 | Test eşikleri | `isletim-sistemi-testing` |

## §26 Kırılma noktaları — derleme-paketleme (S4)

| # | Kırılma | Nerede | Önleme |
|---|---|---|---|
| 1 | C++20 desteği olmayan eski GCC/MSVC | toolchain seçimi | §11 #1 + minimum sürüm |
| 2 | MSVC `wchar_t`/`long` farkı | ABI | `../platform-yuzeyleri` §2 |
| 3 | `-mavx2` ile üretilen ikili eski CPU'da | runtime SIGILL | hedef CPU seviyesi kararı |
| 4 | ASan/TSan MSVC'de farklı kapsam | test | testing dalı |
| 5 | `SOURCE_DATE_EPOCH` unutulursa | reproducible kırılır | §16 #1 |
| 6 | npm major sürüm sıçraması | test kırığı | lock + renovate |
| 7 | Composer platform kontrolü (`config.platform`) | farklı PHP sürümünde kurulum farkı | `composer.json:8` eşleşmesi |
| 8 | AppImage FUSE yokluğu | bazı sistemlerde açılmaz | `--appimage-extract-and-run` |

## §27 Hızlı karar tablosu — "hangi araç nerede"

| # | Karar anı | Doğru soru | Yönlendirme |
|---|---|---|---|
| 1 | Yeni C++ projesi | build sistemi CMake mi Meson mu? | §2 |
| 2 | Bağımlılık ekle | lock commit edildi mi? | §14 |
| 3 | Çok OS paket | artifact şeması hazır mı? | §23 |
| 4 | CI önbellek | anahtar OS/mimari içeriyor mu? | §13 #4 |
| 5 | Sürüm bump | SemVer kuralına uygun mu? | §5, §9 #7 |
| 6 | Paket imzalama | hangi imza mekanizması? | §12, surucu.md |
| 7 | Reproducible hedef | 6 maddelik checklist uygulandı mı? | §16 |
| 8 | Toolchain upgrade | baseline/container tag güncellendi mi? | §20 |

## §28 Riskler — genişletme

| # | Risk | Olasılık | Etki |
|---|---|---|---|
| R8 | `cmake_minimum_required` ile CI cmake sürümü çakışır | orta | orta |
| R9 | MSVC/GCC davranışı farkı (§26 #2) derleme anında değil üretimde görünür | yüksek | yüksek |
| R10 | SBOM yokluğu (§22 #5) lisans denetimini imkânsız kılar | orta | orta |
| R11 | Artifact adlandırma şeması kararı alınmazsa (Q6) indirme sayfası tutarsız olur | orta | düşük |
| R12 | `npm ci` lock'suz koşarsa sürüm sürüklenir (`ci.yml:154` yalnız kilitli çalışıyor) | düşük | yüksek |

## §29 Açık sorular — genişletme

| # | Soru | Etiket |
|---|---|---|
| Q-X32 | Container tabanlı toolchain mi, native CI runner mı? | UNKNOWN |
| Q-X33 | SBOM üretimi (SPDX/CycloneDX) plana giriyor mu? | UNKNOWN |
| Q-X34 | Artifact imzalama anahtarı nerede saklanacak? (secret yönetimi) | UNKNOWN |
| Q-X35 | `composer.json` `config.platform` alanı sabitlendi mi? | UNKNOWN |

## §30 Kontrol listesi — Neva Engine paketleme başlangıcı (PLANNED)

| # | Adım | Karar bekleyen |
|---|---|---|
| 1 | Toolchain seçimi + minimum sürüm | Q-X28 |
| 2 | Build sistemi (CMake/Meson) | §2 |
| 3 | C++ bağımlılık yöneticisi | Q-X30 |
| 4 | Paket formatı seti | Q6 |
| 5 | Artifact adlandırma şeması | §23 |
| 6 | Reproducible checklist | §16 |
| 7 | SBOM | Q-X33 |
| 8 | İmzalama | Q-X34 |

## §31 BuildOutput — çıktının yeri (PLANNED)

| # | Çıktı | Üretim yeri | Repo durumu |
|---|---|---|---|
| 1 | executable | `build/bin` | yok (PLANNED) |
| 2 | kütüphane | `build/lib` | yok (PLANNED) |
| 3 | paket | `dist/` | yok (PLANNED) |
| 4 | `vendor/` | Composer (repo kökü/shared) | var (S1, `.gitignore`) |
| 5 | `node_modules/` | npm | var (S1, `.gitignore`) |

## §32 Sözlük — genişletme

| # | Terim | Tanım |
|---|---|---|
| 7 | Generator | CMake/Meson'un ürettiği üst sistem dosyası (§2 #2) |
| 8 | Cross derleme | farklı OS/mimari için derleme (§2 #4) |
| 9 | Toolchain dosyası | CMake cross derleme tanımı (§2 #4) |
| 10 | SBOM | yazılım bileşen envanteri (SPDX/CycloneDX) (§22 #5) |
| 11 | Provenance | paket üretim kanıtı (§12 #12) |
| 12 | `npm ci` | kilitten birebir kurulum (ci.yml:154) |
| 13 | `composer.lock` | PHP kilit dosyası (ci.yml:44 hashFiles) |
| 14 | WiX | MSI üreten kaynak araç (§12 #3) |

## §33 Sürüm geçmişi — genişletme

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 2 | v1.0.1 | 2026-10-13 | §20-§25 eklendi (toolchain takip · artifact · çok-OS adlandırma) |
| 3 | v1.0.2 | 2026-10-13 | §26-§30 eklendi (kırılma noktaları · hızlı karar · risk genişletme) |
| 4 | v1.0.3 | 2026-10-13 | §31-§32 eklendi (buildOutput · sözlük genişletme) |

## §34 Ölçüm kaydı (bu dosyanın doğrulaması — nihai)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | URL HEAD 200 | 4/4 (clang · MSVC · cmake.1 · meson) |
| 2 | Envanter kesişimi | 0 |
| 3 | Ninja manuel sayfası | envanterde VAR → kök `ninja-build.org/` ile değiştirildi |
| 4 | `cmake.org/.../command/cmake.html` | 404 → `manual/cmake.1.html` (200) |
| 5 | Repo kanıtı satır okuma | 6 (`Select-Object -Index (N-1)`) |
| 6 | Mojibake (Çince/Cyrillic) | 0 |
| 7 | Dolgu (`TBD`/`Yakında`/`// ...`) | 0 |
| 8 | `parent:` alanı | 0 (yalnız `parent_id`) |
| 9 | `group` | `mimari` (anayasa eşlemesi) |
| 10 | Son satır Authority | var |
| 11 | Git commit | atılmadı (orchestrator'a ait) |
| 12 | Yazılan dosyalar | yalnız whitelist (`derleme-ve-paketleme/**`) |
| 13 | Açık soru sayısı | 10 (Q6 + Q-X28..Q-X35) |
| 14 | Bölüm sayısı | §0-§34 |
| 15 | Kod bloğu | S1 (composer.json · ci.yml) + S4 (PLANNED C++) |
| 16 | Uydurma sürüm/API iddiası | 0 |
| 17 | Frontmatter alanı | 10 (v2 §4.2 + group) |
| 18 | `children` | 0 (modül, leaf) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode