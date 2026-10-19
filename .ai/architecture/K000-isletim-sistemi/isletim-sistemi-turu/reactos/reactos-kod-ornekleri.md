---
node_id: ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-kod-ornekleri"
node_type: code
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-cekirdek-mimarisi"
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-turu/reactos/reactos-surucu-modeli"
evidence:
  - "https://reactos.org/wiki/Build_Environment (✅ HTTP 200 · içerik bot-duvarı · 2026-10-12)"
  - "https://reactos.org/wiki/Building_ReactOS (✅ HTTP 200 · içerik bot-duvarı · 2026-10-12)"
  - "https://reactos.org/wiki/CMake (✅ HTTP 200 · içerik bot-duvarı · 2026-10-12)"
  - "https://reactos.org/wiki/RosBE (✅ HTTP 200 · içerik bot-duvarı · 2026-10-12)"
validation:
  - "2026-10-12 RED TEAM: Dosya/Satır UYDURMA YASAK — repo'da 0 .cpp/.h; bu dosyadaki TÜM kod blokları '[ORİJİNAL ÖRNEK · derlenmedi]' etiketlidir; resmi alıntı YOKTUR (resmi içeriğe erişilemedi, bkz. §10)."
  - "≥500 satır · 10 alanlı frontmatter · reactos/ içinde URL kümesi ayrışık (kesişim 0)."
  - "Derleme/çalıştırma kanıtı YOK → status: verify-required."
open_questions:
  - "R-K1: ReactOS derleme zincirinin resmi sürüm/dil bilgileri (CMake sürümü, compiler) → içerik okunamadı. [⚠️ VERIFICATION REQUIRED]"
  - "R-K2: RosBE'nin güncel paket içeriği → [⚠️ VERIFICATION REQUIRED]"
  - "R-K3: Resmi dokümantasyondan alıntı yapılabilecek duruma gelme (bot-duvarı) → [⚠️ VERIFICATION REQUIRED]"
group: kod
---

# reactos-kod-ornekleri — ReactOS derleme/hata ayıklama kalıpları (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** verify-required
> **Kapsam:** `reactos/` beyazliste kalem 5 (kod) · 10 alanlı frontmatter · ≥500 satır.

---

## §0. Kod kaynak politikası (en üst — bağlayıcı)

**ZERO-HALLUCINATION kod kuralı (bu turda mutlak):**

| Kaynak türü | Durum | Kural |
|---|---|---|
| Repo'daki C++ dosyası | **YOK** — bu vault 0 `.cpp`/`.h` içerir | `Dosya:… / Satır:…` referansı **UYDURMA YASAK** |
| Resmi ReactOS dokümantasyon alıntısı | **MÜMKÜN DEĞİL** — wiki bot-duvarının arkasında (§10) | Alıntı yapılmadı |
| Bilinen resmi URL'den alıntı | İçerik okunamadığı için yok | `⚠️ bilinen resmi URL — içerik okunamadı` |
| **Orijinal örnek (bu vault için yazılmış)** | Bu dosyadaki TÜM bloklar | `**[ORİJİNAL ÖRNEK · derlenmedi]**` etiketi zorunlu |
| Compile/çalışma kanıtı | **YOK** — bu turda derlenmedi/çalıştırılmadı | `status: verify-required` |

**Doğrulanabilirlik iddiası:** Bu dosyadaki her kod bloğu **tek** etiketten birini taşır:
`[ORİJİNAL ÖRNEK · derlenmedi]`. Resmi kaynakmış gibi sunulması **yasaktır**.

---

## §1. Derleme zinciri başlıklarının envanteri (10 URL)

| # | Başlık | Ne vaat ediyor (başlık düzeyi) | URL | İçerik |
|---|---|---|---|---|
| 1 | `Build Environment` | Derleme ortamı | https://reactos.org/wiki/Build_Environment | ❌ |
| 2 | `Building ReactOS` | Derleme adımları | https://reactos.org/wiki/Building_ReactOS | ❌ |
| 3 | `CMake` | Yapılandırma/derleme sistemi | https://reactos.org/wiki/CMake | ❌ |
| 4 | `RosBE` | Derleme ortamı/araç paketi | https://reactos.org/wiki/RosBE | ❌ |
| 5 | `Git` | Sürüm kontrolü | https://reactos.org/wiki/Git | ❌ |
| 6 | `SVN` | Eski sürüm kontrolü | https://reactos.org/wiki/SVN | ❌ |
| 7 | `CMake Howto` | CMake rehberi | https://reactos.org/wiki/CMake_Howto | ❌ |
| 8 | `Developing ReactOS with Visual Studio` | IDE ile geliştirme | https://reactos.org/wiki/Developing_ReactOS_with_Visual_Studio | ❌ |
| 9 | `Building MINGW-w64` | Araç zinciri derleme | https://reactos.org/wiki/Building_MINGW-w64 | ❌ |
| 10 | `A Layman's Guide - How to Compile ReactOS` | Yeni başlayan derleme rehberi | https://reactos.org/wiki/A_Layman%27s_Guide_-_How_to_Compile_ReactOS | ❌ |

> **Sonuç:** 10 başlık da erişilebilir (HTTP 200), **hiçbiri içerik düzeyinde okunamadı**.
> Bu yüzden bu dosyada `**[RESMİ ALINTI]**` bloğu **yoktur**.

---

## §2. Örnek A — Genel CMake yapılandırma iskeleti

**[ORİJİNAL ÖRNEK · derlenmedi]**

> Bu blok ReactOS deposunun `CMakeLists.txt` dosyasının kopyası **DEĞİLDİR**; yalnızca
> "CMake kullanılıyor" başlığıyla uyumlu, **bu vault için yazılmış** genel bir
> yapılandırma iskeletidir. Gerçek ReactOS yapılandırması için §1 #3 ve #7 başlıkları
> içerik düzeyinde okunmalıdır.

```cmake
# [ORİJİNAL ÖRNEK] genel CMake iskeleti — derlenmedi — resmi ReactOS dosyası DEĞİLDİR
cmake_minimum_required(VERSION 3.20)
project(vault_example LANGUAGES C CXX)

set(CMAKE_CXX_STANDARD 20)
set(CMAKE_CXX_STANDARD_REQUIRED ON)

option(VAULT_BUILD_TESTS "ornek testleri etkinlestir" OFF)
option(VAULT_WERROR "uyarilari hata say" OFF)

add_library(vault_core STATIC
    src/core.cpp
    src/util.cpp
)

target_include_directories(vault_core PUBLIC ${CMAKE_CURRENT_SOURCE_DIR}/include)

if(MSVC)
    target_compile_options(vault_core PRIVATE /W4)
    if(VAULT_WERROR)
        target_compile_options(vault_core PRIVATE /WX)
    endif()
else()
    target_compile_options(vault_core PRIVATE -Wall -Wextra)
    if(VAULT_WERROR)
        target_compile_options(vault_core PRIVATE -Werror)
    endif()
endif()

if(VAULT_BUILD_TESTS)
    enable_testing()
    add_executable(vault_core_test tests/core_test.cpp)
    target_link_libraries(vault_core_test PRIVATE vault_core)
    add_test(NAME core COMMAND vault_core_test)
endif()
```

| Madde | Değer |
|---|---|
| Etiket | `[ORİJİNAL ÖRNEK · derlenmedi]` |
| Kaynak | bu vault için yazıldı |
| Resmi karşılığı | bilinmiyor (içerik ❌) |
| Derlendi mi? | Hayır — bu vault'ta derleme ortamı çalıştırılmadı |

---

## §3. Örnek B — Derleme kabuğu betiği (betik iskeleti)

**[ORİJİNAL ÖRNEK · derlenmedi]**

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK] genel derleme betigi iskeleti — derlenmedi/calistirilmadi
# Amaç: "derleme adimlari"ni adim adim gorebilmek icin orijinal bir taslak.
set -euo pipefail

BUILD_DIR="${BUILD_DIR:-build}"
JOBS="${JOBS:-$(getconf _NPROCESSORS_ONLN 2>/dev/null || echo 2)}"
GENERATOR="${GENERATOR:-}"          # bos birakilirsa cmake varsayilanini kullanir

step() { printf '\n== %s ==\n' "$1"; }

step "1/6 ag ortami kontrol"
test -n "${SRC_ROOT:-}" || { echo "SRC_ROOT tanimli degil"; exit 2; }

step "2/6 temiz dizin"
rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"

step "3/6 yapilandirma"
cmake -S "$SRC_ROOT" -B "$BUILD_DIR" \
      ${GENERATOR:+-G "$GENERATOR"} \
      -DCMAKE_BUILD_TYPE=RelWithDebInfo

step "4/6 derleme"
cmake --build "$BUILD_DIR" --parallel "$JOBS"

step "5/6 test"
ctest --test-dir "$BUILD_DIR" --output-on-failure || echo "test atlandi"

step "6/6 ozet"
du -sh "$BUILD_DIR" 2>/dev/null || true
echo "BETIK TAMAMLANDI (calistirilmadi — bu satir orijinal taslak metnidir)"
```

**Neden bu betik?** `Building ReactOS` ve `Build Environment` başlıkları bu konudadır;
içerik okunamadığı için betik **ReactOS'a özgü hiçbir komut/argüman taşımaz** — genel
iskelettir. Gerçek komutlar §17'deki kapatma planında çıkarılacaktır.

---

## §4. Örnek C — Git dal/etiket akışı

**[ORİJİNAL ÖRNEK · derlenmedi]**

```bash
# [ORİJİNAL ÖRNEK] surum kontrolu akisi — ReactOS deposuna uygulanmadi
# Kanit: https://reactos.org/wiki/Git (baslik) — icerik okunamadi (2026-10-12)
git status --short
git switch -c vault/topic-branch
git add -- spec.md
git commit -m "vault: ornek konu belgesi eklendi"
git log --oneline -n 10
git tag -a vault-0.1 -m "ornek etiket"
git push --follow-tags origin vault/topic-branch
```

| Adım | Ne yapar | Kanıt durumu |
|---|---|---|
| `git status` | Çalışma ağacını gösterir | genel Git bilgisi |
| `git switch -c` | Dal oluşturur | genel Git bilgisi |
| `git commit` | Değişikliği kaydeder | genel Git bilgisi |
| `git tag -a` | Etiket koyar | genel Git bilgisi |
| `git push` | Uzağa gönderir | genel Git bilgisi |
| ReactOS'a özgü akım | — | UNKNOWN (§1 #5 içerik ❌) |

---

## §5. Örnek D — SVN geçmişi araması (geçiş dönemi)

**[ORİJİNAL ÖRNEK · derlenmedi]**

```bash
# [ORİJİNAL ÖRNEK] SVN gecmisi arama iskeleti — hedef depoya uygulanmadi
# Kanit: https://reactos.org/wiki/SVN (baslik) — icerik okunamadi (2026-10-12)
svn info .
svn log -l 20 .
svn log -v -r {2026-01-01}:{2026-10-12} .
svn diff -c 12345 .
svn revert -R build_artifacts/
```

**Neden SVN hâlâ kayıtlarda?** §1 #6 başlığı hâlâ erişilebilirdir; bir projenin
geçmiş sürüm kontrol sistemi, arşiv erişimi ve taban çizgisi araştırması için
gereklidir. **Git'e geçişin tamamı mı, kısmi mi olduğu UNKNOWN'dur.**

---

## §6. Örnek E — GDB toplu (batch) hata ayıklama iskeleti

**[ORİJİNAL ÖRNEK · derlenmedi]**

```text
# [ORİJİNAL ÖRNEK] GDB toplu mod iskeleti — calistirilmadi
# Kanit: https://reactos.org/wiki/GDB (baslik) — icerik okunamadi (2026-10-12)
set pagination off
set confirm off
set print pretty on
file ./build/vault_core_test
break vault_core_run
run --sample-rate 48000 --buffer 512
info registers
bt full
info threads
detach
quit
```

| Ayar | Amaç | Durum |
|---|---|---|
| `set pagination off` | Çıktıyı sayfalama | genel GDB bilgisi |
| `break` | Kırılma noktası | genel GDB bilgisi |
| `bt full` | Tam yığın izi | genel GDB bilgisi |
| `info threads` | İş parçacıkları | genel GDB bilgisi |
| ReactOS çekirdeğine bağlama | — | UNKNOWN (§1 #5 başlık, içerik ❌) |

---

## §7. Örnek F — CI iş tanımı iskeleti (YAML)

**[ORİJİNAL ÖRNEK · derlenmedi]**

```yaml
# [ORİJİNAL ÖRNEK] CI is tanimi iskeleti — hicbir CI sisteminde calistirilmadi
# Kanit basliklari: Continuous_integration · BuildBot (URL'ler kardes dosyada)
name: vault-ci-example
on:
  push:
    branches: [ main ]
  pull_request:

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0
      - name: araclarin surumu
        run: |
          cmake --version
          c++ --version
      - name: yapilandirma
        run: cmake -S . -B build -DCMAKE_BUILD_TYPE=RelWithDebInfo
      - name: derleme
        run: cmake --build build --parallel 4
      - name: test
        run: ctest --test-dir build --output-on-failure
      - name: rapor
        if: always()
        run: echo "CI ORNEK ADIMI TAMAMLANDI (calistirilmadi)"
```

**Uyarı:** Bu YAML **bu vault'un `.github/workflows/` dosyalarının kopyası değildir**
ve ReactOS CI sistemini temsil etmez. Sadece §1 #5/#7 başlıklarının konusunu
göstermek için yazılmış **orijinal** bir iskelettir.

---

## §8. Örnek G — İmza denetimi düşünme iskeleti (pseudokod)

**[ORİJİNAL ÖRNEK · derlenmedi]**

```text
# [ORİJİNAL ÖRNEK] surucu imzasi denetim akisi — pseudokod — calistirilmadi
# Kanit basligi: Driver_Signing (URL kardes dosyada: reactos-surucu-modeli.md)
girdi: surucu_dosyasi, guven_zinciri

1. surucu_dosyasi var mi?            -> yoksa RED
2. imza alani dolu mu?               -> bossa RED
3. imza algoritmasi taniniyor mu?    -> taninmiyorsa RED
4. guven_zinciri dogrulanabiliyor mu?-> hayirsa RED (veya test modunda UYARI)
5. surucu sinifi izinli mi?          -> degilse RED
6. yukleme onaylanir                 -> AKTIF

cikti: YUKLE | RED | TEST_ONAYI
```

**Neden pseudokod?** İmza politikasının gerçek adımları bu turda **okunamadı**;
araç adı/argüman uydurmak yasak olduğu için akış **araç-bağımsız** yazılmıştır.

---

## §9. Örnek H — Ses ölçümü için platform-bağımsız betik (CoreMusic bağlamı)

**[ORİJİNAL ÖRNEK · derlenmedi]**

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK] ses olcum iskeleti — hicbir platformda calistirilmadi
# Baglam: vault gereksinimleri 48000 Hz / 512 ornek / float32 / 8.1
set -euo pipefail

REQ_RATE=48000
REQ_BUFFER=512
REQ_FORMAT="float32"
REQ_LAYOUT="8.1"

echo "hedef: rate=$REQ_RATE buffer=$REQ_BUFFER format=$REQ_FORMAT layout=$REQ_LAYOUT"
echo "platform ses arayuzu tespiti: BILINMIYOR (ReactOS icin UNKNOWN)"
echo "olcum adimlari (reactos-ses-stacki.md §15.5):"
echo "  M1 cihaz listesi"
echo "  M4 buffer alt siniri"
echo "  M5 uctan uca gecikme (ms)"
echo "  M7 kesme orani"
echo "SONUC: olcum yapilmadi — ortam yok"
```

| Gereksinim | Kaynak | Bu betikte |
|---|---|---|
| 48 kHz | vault içi sartname | sabit |
| 512 örnek | vault içi sartname | sabit |
| float32 | ADR-017 (32-bit float) | sabit |
| 8.1 surround | vault içi sartname | sabit |
| Kilit-siz callback | vault içi kural | betikte yok (C++ konusu) |
| Sıfır tahsis | vault içi kural | betikte yok |

---

## §10. Erişim ve kanıt bütünlüğü notu (bağlayıcı · 2026-10-12)

| URL sınıfı | HTTP | İçerik | Bu dosyadaki sonuç |
|---|---|---|---|
| 10 wiki başlığı (§1) | ✅ 200 | ❌ Anubis bot-duvarı (v1.19.1 · 4188 bayt) | **resmi alıntı üretilemedi** |
| `reactos.org/` · `/download/` · `/news/` | ✅ 200 | ✅ gerçek içerik | bu dosyada URL taşımıyor |
| `github.com/reactos/reactos` | ✅ 200 | ✅ gerçek içerik | `reactos-web-arastirma.md` |
| `jira.reactos.org` · `build.reactos.org` | ✅ 200 | ✅ gerçek içerik | `reactos-web-arastirma.md` |

Ayrıntılı sınıflandırma: `reactos-cekirdek-mimarisi.md` §11.5.

**Kod politikasına etkisi:** Resmi alıntı üretilemeyeceği için bu dosyadaki **8 bloğun
tamamı orijinaldir**. Bu bir tercih değil, erişim kısıtının sonucudur.

---

## §11. Varsayım günlüğü (RED TEAM)

| # | Varsayım | Kanıt | Karar |
|---|---|---|---|
| A1 | Proje CMake kullanıyor | `CMake` başlığı | KABUL (başlık) |
| A2 | CMake sürümü biliniyor | — | REDDEDİLDİ |
| A3 | RosBE bir derleme aracı paketidir | başlık | [⚠️] |
| A4 | Git ana sürüm kontrolü | `Git` başlığı | [⚠️] |
| A5 | SVN hâlâ kullanılıyor | `SVN` başlığı | [⚠️] |
| A6 | Visual Studio ile geliştirme mümkün | başlık | [⚠️] |
| A7 | MINGW-w64 araç zinciri derleniyor | başlık | [⚠️] |
| A8 | Yeni başlayan rehberi var | başlık | [⚠️] |
| A9 | Derleme adımları biliniyor | içerik ❌ | REDDEDİLDİ |
| A10 | §2–§9 blokları resmi | — | REDDEDİLDİ (tamamı orijinal) |
| A11 | Repo'da kod var | 0 `.cpp`/`.h` | REDDEDİLDİ |
| A12 | `Dosya:/Satır:` verilebilir | — | REDDEDİLDİ (uydurma yasak) |
| A13 | Derleme başarılı olurdu | — | REDDEDİLDİ (derlenmedi) |
| A14 | CI işi ReactOS CI'ını temsil eder | — | REDDEDİLDİ (§7) |
| A15 | Betik ReactOS komutlarını içerir | — | REDDEDİLDİ (içerik ❌) |
| A16 | Ses ölçümü yapılabilir | ortam yok | REDDEDİLDİ (§9) |
| A17 | İmza akışı gerçek politikadır | içerik ❌ | REDDEDİLDİ (§8) |
| A18 | GDB ayarları ReactOS'a özgü | — | REDDEDİLDİ (§6) |
| A19 | SVN komutları hedef depoda geçerli | — | REDDEDİLDİ (§5) |
| A20 | Git komutları hedef depoda geçerli | — | REDDEDİLDİ (§4) |
| A21 | 10 başlık erişilebilir | HTTP 200 | KABUL |
| A22 | İçerik okunamadı | Anubis gövdesi | KABUL |
| A23 | Bot-duvarı tüm wiki'yi kapsıyor | 10/10 aynı sonuç | KABUL |
| A24 | Derleme ortamı bu vault'ta yok | çalıştırma kanıtı yok | KABUL |
| A25 | Betik iskeleti geneldir | araç-spezifik komut yok | KABUL (tasarım) |
| A26 | 8 blok da etiketli | §0 politikası + blok etiketleri | KABUL |
| A27 | Etiketler arasında resmi alıntı yok | §0 | KABUL |
| A28 | `group: kod` doğru | ADR-097 §5.5 | KABUL |
| A29 | ReactOS, Neva Engine hedefi | kanıt yok | REDDEDİLDİ → UNKNOWN |
| A30 | Bu dosya ≥500 satır | §18 ölçüm | KABUL (ölçüm) |

**Özet:** 30 varsayım → 8 KABUL, 6 `[⚠️]`, **16 REDDEDİLDİ**.

---

## §12. Kanıt matrisi (≥8 benzersiz URL · reactos/ içinde ayrışık)

| # | İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|---|
| 1 | `Build Environment` başlığı erişilebilir | https://reactos.org/wiki/Build_Environment | 2026-10-12 | ✅ 200 · içerik ❌ |
| 2 | `Building ReactOS` başlığı erişilebilir | https://reactos.org/wiki/Building_ReactOS | 2026-10-12 | ✅ 200 · içerik ❌ |
| 3 | `CMake` başlığı erişilebilir | https://reactos.org/wiki/CMake | 2026-10-12 | ✅ 200 · içerik ❌ |
| 4 | `RosBE` başlığı erişilebilir | https://reactos.org/wiki/RosBE | 2026-10-12 | ✅ 200 · içerik ❌ |
| 5 | `Git` başlığı erişilebilir | https://reactos.org/wiki/Git | 2026-10-12 | ✅ 200 · içerik ❌ |
| 6 | `SVN` başlığı erişilebilir | https://reactos.org/wiki/SVN | 2026-10-12 | ✅ 200 · içerik ❌ |
| 7 | `CMake Howto` başlığı erişilebilir | https://reactos.org/wiki/CMake_Howto | 2026-10-12 | ✅ 200 · içerik ❌ |
| 8 | `Developing ReactOS with Visual Studio` başlığı erişilebilir | https://reactos.org/wiki/Developing_ReactOS_with_Visual_Studio | 2026-10-12 | ✅ 200 · içerik ❌ |
| 9 | `Building MINGW-w64` başlığı erişilebilir | https://reactos.org/wiki/Building_MINGW-w64 | 2026-10-12 | ✅ 200 · içerik ❌ |
| 10 | Yeni başlayan derleme rehberi başlığı erişilebilir | https://reactos.org/wiki/A_Layman%27s_Guide_-_How_to_Compile_ReactOS | 2026-10-12 | ✅ 200 · içerik ❌ |

**Dosya-içi benzersiz URL: 10 (≥8 ✅)** · reactos/ klasöründe kesişim: 0.

---

## §13. Açık sorular

| # | Soru | Etiket |
|---|---|---|
| R-K1 | Derleme zinciri sürüm/dil bilgileri neler? | ⚠️ VERIFICATION REQUIRED |
| R-K2 | RosBE paket içeriği nedir? | ⚠️ VERIFICATION REQUIRED |
| R-K3 | Resmi doküman alıntısına ne zaman ulaşılır? (bot-duvarı) | ⚠️ VERIFICATION REQUIRED |
| R-K4 | Git mi SVN mi baskın? | UNKNOWN |
| R-K5 | IDE desteği tam olarak nasıl? | ⚠️ VERIFICATION REQUIRED |
| R-K6 | Bu vault'ta derleme kanıtı ne zaman üretilecek? | UNKNOWN |

---

## §14. Terim sözlüğü

| Terim | Anlamı |
|---|---|
| Yapılandırma sistemi | Kaynaktan derleme kurallarını üreten araç (CMake) |
| Derleme zinciri | Derleyici + kütüphaneler + araçlar bütünü |
| Araç paketi | Hazır derleme araçları seti |
| Dal (branch) | Sürüm kontrolünde paralel geliştirme hattı |
| Etiket (tag) | Başlangıç noktası işareti |
| Geçmiş (log) | Değişiklik kayıtları |
| Fark (diff) | İki sürüm arasındaki değişiklik |
| Toplu mod | Etkileşimsiz çalışan araç kipi |
| Kırılma noktası | Duraklatılan kod konumu |
| Yığın izi (backtrace) | Çağrı zinciri kaydı |
| CI | Sürekli bütünleştirme — otomatik derleme/test |
| İş (job) | CI'daki birim görev |
| İmza zinciri | Kökten sürücüye uzanan güven zinciri |
| Pseudokod | Araç-bağımsız, çalıştırılmamış akış taslağı |
| Etiket `[ORİJİNAL ÖRNEK]` | Bu vault için yazılmış, resmi olmayan kod |
| Etiket `derlenmedi` | Derleme/çalıştırma kanıtı olmadığını beyanı |
| Dosya/Satır referansı | Repodaki gerçek dosya konumu — bu vault'ta imkânsız |
| Sıfır-tahsis | Çağrı sırasında bellek ayırma yapılmaması |
| Kilit-siz | Kilit kullanmadan eşzamanlı erişim |
| 48 kHz / 512 / float32 | Vault ses gereksinimleri (sartname) |
| 8.1 surround | Kanal düzeni gereksinimi |
| Bot-duvarı | İçeriği engelleyen koruma sayfası (Anubis) |
| Başlık kanıtı | Yalnız sayfa başlığının erişilebilirliği |
| İçerik kanıtı | Sayfa metninin okunmuş olması |
| UNKNOWN | Veri yok, tahmin yok |
| İşaretli belirsizlik | `[⚠️ VERIFICATION REQUIRED]` |
| Doğrulama planı | §17 kapatma adımları |
| Kanıt matrisi | İddia-URL-tarih-durum tablosu |
| Beyazliste | Bu turda yazımına izin verilen dosyalar |
| Sınır maddesi | Dosyanın bitip başka dosyanın başladığı konu |

---

## §15. Kod bloğu envanteri (denetim için)

| # | Bölüm | Blok dili | Etiket | Derlendi mi |
|---|---|---|---|---|
| 1 | §2 | CMake | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 2 | §3 | Bash | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 3 | §4 | Bash (Git) | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 4 | §5 | Bash (SVN) | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 5 | §6 | GDB script | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 6 | §7 | YAML (CI) | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 7 | §8 | Pseudokod | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |
| 8 | §9 | Bash (ses ölçüm) | `[ORİJİNAL ÖRNEK · derlenmedi]` | Hayır |

**Toplam: 8 blok · resmi alıntı: 0 · orijinal: 8 · derlenen: 0.**

---

## §16. Sınır dışı bırakılan konular

1. ReactOS'a özgü CMake hedefleri → içerik ❌, ad uydurmak yasak.
2. Sürücü kodu → `reactos-surucu-modeli.md` (ayrı dosya).
3. Ses API kodu → `reactos-ses-stacki.md` (UNKNOWN).
4. Win32 API kodu → `reactos-win32-uyumlulugu.md`.
5. URL tarama kaydı → `reactos-web-arastirma.md`.
6. Windows derleme ortamı (WDK) → `../windows/` dalı.
7. Repo'daki gerçek dosya/satır → repo'da 0 `.cpp`/`.h`.
8. Derleme çalıştırma → bu turda yapılmadı.
9. Test çalıştırma → bu turda yapılmadı.
10. Lisans metni → okunmadı, [⚠️].

---

## §17. Resmi alıntıya geçiş planı (kapatma adımları)

| # | Adım | Beklenen çıktı | Bağımlılık |
|---|---|---|---|
| 1 | Bot-duvarını geçebilen istemci | gerçek HTML | JS/tarayıcı |
| 2 | §1'deki 10 başlığı indirme | metin | adım 1 |
| 3 | `Building ReactOS` adımlarını çıkarma | komut listesi | adım 2 |
| 4 | `Build Environment` araç/sürüm tablosu | sürüm listesi | adım 2 |
| 5 | `CMake` / `CMake Howto` örneklerini çıkarma | resmi örnek | adım 2 |
| 6 | `RosBE` paket içeriği | araç listesi | adım 2 |
| 7 | `Git`/`SVN` iş akışı çıkarımı | dal/etiket kuralları | adım 2 |
| 8 | IDE sayfasından ayarlar | yapılandırma | adım 2 |
| 9 | `MINGW-w64` zincir adımları | komut listesi | adım 2 |
| 10 | Yeni başlayan rehberinin tamamı | adım listesi | adım 2 |
| 11 | Uygun blokları `**[RESMİ ALINTI · URL · tarih]**` olarak değiştirme | 8→x dönüşüm | adım 3–10 |
| 12 | Orijinal blokları geride bırakma veya silme | envanter güncellemesi | adım 11 |
| 13 | `Dosya:/Satır:` yalnız gerçek depo taramasıyla | github ✅ | adım 14 |
| 14 | Depoyu klonlayıp gerçek dosya/satır bulma | konum kanıtı | github ✅ |
| 15 | Küçük bir derleme denemesi | derleme kanıtı | ortam |
| 16 | Test çalıştırma | test kanıtı | adım 15 |
| 17 | Bulguları `reactos-web-arastirma.md`'ye yazma | kanıt satırları | 2–16 |
| 18 | `status` → `verified` | frontmatter | adım 15–17 |
| 19 | R-K1…R-R6 kapatma | 0 açık soru | adım 18 |
| 20 | §15 envanterini güncelleme | alıntı/orijinal dağılımı | adım 11 |

---

## §18. Ölçüm

| Metrik | Değer |
|---|---|
| Dosya | `reactos-kod-ornekleri.md` |
| Hedef satır | ≥500 |
| § sayısı | ≥17 |
| Benzersiz URL | 10 (≥8 ✅) |
| Kod bloğu | 8 (tamamı orijinal · resmi alıntı 0) |
| Reddedilen varsayım | 16 / 30 |
| Frontmatter | 10 alan ✅ · `group: kod` ✅ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode