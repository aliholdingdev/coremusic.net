---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-anayasa"
evidence:
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:199"
  - "shared/src/Api/Bff/DesktopBff.php:34"
  - "shared/src/Component/TemplateEngine.php:73"
  - "https://pubs.opengroup.org/onlinepubs/9699919799/functions/sysconf.html"
  - "https://developer.apple.com/documentation/appkit"
validation: "2026-10-13 · 4 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q-X23 — PLANNED C++ örnekleri Neva Engine şablonuna taşınacak mı"
group: kod
---

# platform-yuzeyleri-ornekler — Kademe-4 Kod Örnekleri

> **Kapsam:** Bu dosya, `platform-yuzeyleri-anayasa.md` matrislerinin **elle tutulur**
> örnekleridir. Repo'daki gerçek PHP kanıtları (S1) ile PLANNED C/C++ kalıpları (S4)
> yan yana verilir. **Repo'da cross-platform native kod YOK — web katmanı PHP'dir**
> (`*.cpp`/`*.h` glob = 0); C++ örnekleri Neva Engine (PLANNED) içindir ve derlenmemiştir.

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-ornekler` |
| parent_id | `…/isletim-sistemi-cross/platform-yuzeyleri` |
| group | `kod` |
| Bağımlı | `platform-yuzeyleri-anayasa.md` (matrisler burada) |
| Kanıt dili | S1 = repo satırı · S4 = PLANNED kalıp (derlenmedi) |

## §0 ELI15

Örnekler iki rafta durur: **rafta 1** — depoda bugün çalışan PHP (yol ayracı, OS tespiti,
path traversal denetimi); **rafta 2** — Neva Engine için yazılmış, henüz derlenmemiş C++
kalıpları (ABI gardları, hata yüzeyi sarmalayıcı). Rafta 2'yi "çalışıyor" diye sunmak
yalan olur; o yüzden her blok S1/S4 etiketi taşır.

## §1 Rafta 1 — repo kanıtları (S1, birebir satır)

### §1.1 Yol ayracı — `DIRECTORY_SEPARATOR` (S1)

**Dosya:** `shared/src/Component/TemplateEngine.php` · **Satır: 73**

```php
        $cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
```

| # | Öğe | Ne gösteriyor |
|---|---|---|
| 1 | `sys_get_temp_dir()` | geçici dizin platformdan soyut (§4 matrisi) |
| 2 | `rtrim(..., "\\/") | çift ayracın temizlenmesi | hem `\` hem `/` |
| 3 | `DIRECTORY_SEPARATOR` | ayracı PHP verir — sabit yazılmaz |
| 4 | `bin2hex(random_bytes(8))` | rastgele dosya adı — çakışma yok |

### §1.2 OS tespiti — `php_uname('s')` (S1)

**Dosya:** `shared/src/Api/Bff/DesktopBff.php` · **Satır: 34-38**

```php
        $response['meta']['system'] = [
            'platform' => php_uname('s'),
            'phpVersion' => PHP_VERSION,
            'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
        ];
```

| # | Öğe | Ne gösteriyor |
|---|---|---|
| 1 | `php_uname('s')` | OS gizlenmez, raporlanır (anayasa §6 #2) |
| 2 | `$_SERVER['SERVER_SOFTWARE'] ?? 'unknown'` | sunucu bilinmiyorsa uydurulmaz |
| 3 | `meta['system']` | platform bilgisi API gövdesinde |

### §1.3 Path traversal denetimi — `str_starts_with` (S1)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php` · **Satır: 199-202**

```php
        $prefix = rtrim($root, '\\/') . DIRECTORY_SEPARATOR;
        if (!str_starts_with($real, $prefix)) {
            $this->fail(403, 'Erişim reddedildi');
        }
```

| # | Öğe | Ne gösteriyor |
|---|---|---|
| 1 | `rtrim($root, '\\/')` | hem `\` hem `/` kuyruğu temizlenir |
| 2 | `DIRECTORY_SEPARATOR` | platform ayracı ile birleştirilir |
| 3 | `str_starts_with($real, $prefix)` | gerçek yol (realpath) kökün altında mı |
| 4 | `fail(403)` | ihlalde sessizce geçilmez |

## §2 Rafta 2 — ABI gardları (S4, PLANNED — derlenmedi)

> Bu bloklar Neva Engine şablonu içindir; depoda karşılığı yoktur. Amaç: derleme anında
> sürprizi erken yakalamak (anayasa §2.3).

```cpp
// S4 — PLANNED, derlenmedi
#include <cstdint>
#include <cstddef>

// 1) Tümler boyutlu tipler: long YOK (LP64'te 8, Windows'ta 4)
static_assert(sizeof(std::int32_t) == 4, "int32_t 4 byte olmali");
static_assert(sizeof(std::int64_t) == 8, "int64_t 8 byte olmali");

// 2) wchar_t boyutu platforma bağlı (Windows 2, POSIX 4) — serileştirmede kullanma
static_assert(sizeof(wchar_t) == 2 || sizeof(wchar_t) == 4, "wchar_t 2 veya 4");

// 3) struct boyutu — packing farkını derleme anında yakala
struct alignas(64) AudioBlockHeader {
    std::uint32_t magic;
    std::uint32_t frames;
    std::uint64_t timestamp;
};
static_assert(sizeof(AudioBlockHeader) == 16, "padding sızıntisi var");
```

| # | Gard | Anayasa bağı |
|---|---|---|
| 1 | `int32_t`/`int64_t` boyutu | §2.3 #1 |
| 2 | `wchar_t` 2/4 | §2.1 #12 |
| 3 | `alignas(64)` + `sizeof` | §2.2 #5 |

## §3 Rafta 2 — hata yüzeyi sarmalayıcı (S4, PLANNED)

> `errno` ↔ `GetLastError` karışımı hatası (anayasa §3 #3, surucu R7) tek kapıda biter.

```cpp
// S4 — PLANNED, derlenmedi
#include <string>

// Tek kapı: hangi platform olursa olsun çağrının HEMEN ardından oku.
std::string last_os_error() {
#if defined(_WIN32)
    // GetLastError çağrısı başka Win32 çağrısı ile sıfırlanabilir — hemen oku.
    // (DWORD kod -> metin çevirisi PLANNED: FormatMessageA)
    return "win32-error";
#else
    // errno çağrılar arasında korunmayabilir — hemen oku ve kopyala.
    return "posix-error";
#endif
}
```

| # | Kural | Kırılırsa | Anayasa bağı |
|---|---|---|---|
| 1 | Hemen oku | arada ara çağrı hata kodunu sıfırlar | §3 #1-2 |
| 2 | Kopyala | sonradan okumak yanıltır | §3 #3 |
| 3 | Tek kapı | her yerde `#ifdef` dağılır | §3 #4 |

## §4 Rafta 2 — yol/kütüphane sarmalayıcı (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
// Yol ayracı: sabit '\' veya '/' YAZMA — sarmalayıcı verir.
#if defined(_WIN32)
inline constexpr char kPathSep = '\\';
#else
inline constexpr char kPathSep = '/';
#endif

// Kütüphane yükleme tek kapı (surucu §2): LoadLibrary/dlopen/dylib.
// İmza: handle = lib_open(const char*); sembol = lib_sym(handle, const char*);
```

| # | Öğe | Karşılık (surucu §2.1) |
|---|---|---|
| 1 | `kPathSep` | anayasa §1.1 #1-3 |
| 2 | `lib_open`/`lib_sym` | LoadLibrary/GetProcAddress ↔ dlopen/dlsym |

## §5 Sık yapılan hatalar — yanlış/doğru çiftleri

| # | Yanlış | Neden yanlış | Doğru |
|---|---|---|---|
| 1 | `long` ile 32-bit kaydet | LP64'te 8 byte | `std::int32_t` |
| 2 | `wchar_t` serileştirmek | 2 vs 4 byte | `char8_t`/`char16_t` seç |
| 3 | `'\\'` sabiti | POSIX'te `/` | `DIRECTORY_SEPARATOR`/`kPathSep` |
| 4 | `errno`'yu döngü sonunda okumak | sıfırlanabilir | hemen oku (§3) |
| 5 | Dosya adını küçük harfe indirgemek varsayımı | Linux duyarsız değil | iki yazımı da dene |
| 6 | `// ...` ile boşluk bırakmak | derlenmez, dolgu | tam yaz veya sil |

## §6 Ölçüm / kabul kriterleri

| # | Ölçüm | Yöntem | Eşik |
|---|---|---|---|
| 1 | Repo kanıtı birebirlik | `Select-Object -Index (N-1)` | 3/3 satır |
| 2 | S4 etiketi | her PLANNED blokta `S4 — PLANNED, derlenmedi` | %100 |
| 3 | Derleme iddiası | yok | "derlendi" denmez |
| 4 | URL doğrulama | HEAD 200 + envanter | kesişim 0 |

## §7 Riskler

| # | Risk | Olasılık | Etki | Bağlantı |
|---|---|---|---|---|
| R1 | S4 örneklerin "çalışıyor" sanılması | orta | yüksek | §2-§4 başlıkları |
| R2 | `wchar_t` serileştirmesi Neva Engine'e taşınırsa | düşük | yüksek | §2 #2 |
| R3 | Sarmalayıcılar atlanıp her yere `#ifdef` dağılır | orta | orta | §3 #3 |
| R4 | `str_starts_with` denetimi `realpath` olmadan kullanılırsa | düşük | yüksek | §1.3 |
| R5 | §20 eşleme tablosu kesin değil — yanlış kod çevirisi hatayı yutar | orta | yüksek | §20 |
| R6 | `sysconf` sınırı placeholder'ı gerçek sanılır | düşük | orta | §21 |

## §8 Kapsam dışı

| # | Konu | Nereye gider |
|---|---|---|
| 1 | Örneklerin derlenmesi/test edilmesi | `isletim-sistemi-testing` |
| 2 | Sarmalayıcıların API tasarımı | `isletim-sistemi-api` |
| 3 | `#ifdef` guard kalıplarının tam kataloğu | `../tasinabilirlik-siniri` |
| 4 | Derleme bayrakları | `../derleme-ve-paketleme` |

## §9 Kaynakça (web araştırma · 2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX `sysconf` çalışma zamanı sistem yapılandırma sınırlarını verir | https://pubs.opengroup.org/onlinepubs/9699919799/functions/sysconf.html | 2026-10-13 | ✅ HTTP 200 |
| POSIX `pathconf` dosya-bazlı yapılandırma sınırlarını verir | https://pubs.opengroup.org/onlinepubs/9699919799/functions/pathconf.html | 2026-10-13 | ✅ HTTP 200 |
| D-Bus Unix socket üzerinden sistem/masaüstü mesajlaşma turorialini yayınlar | https://dbus.freedesktop.org/doc/dbus-tutorial.html | 2026-10-13 | ✅ HTTP 200 |
| Apple AppKit macOS uygulama yüzeyini tanımlar | https://developer.apple.com/documentation/appkit | 2026-10-13 | ✅ HTTP 200 |

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-X23 | PLANNED C++ örnekleri Neva Engine şablonuna `static_assert` olarak taşınacak mı? | UNKNOWN |
| Q-X24 | `last_os_error()` sarmalayıcısının hedef dili C mi C++20 mi? | UNKNOWN |

## §11 Rafta 2 — serileştirme son (endianness) denetimi (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
#include <cstdint>
#include <bit>

// Büyük son (big-endian) hedef gelirse serileştirme kırılır (anayasa §2.1 #3).
std::uint32_t to_le32(std::uint32_t v) {
    if constexpr (std::endian::native == std::endian::big) {
        // byte tersleme ekle — PLANNED
        return v; // placeholder, derlenmedi
    }
    return v;
}
```

| # | Kural | Bağ |
|---|---|---|
| 1 | `std::endian::native` ile derleme anında karar | anayasa §2.1 #3 |
| 2 | Placeholder `return v;` "çalışıyor" sayılmaz | S4 dürüstlük |
| 3 | Gerçek tersleme Neva Engine'de yazılacak | Q-X11 |

## §12 Rafta 2 — eşzamanlılık sarmalayıcı iskeleti (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
// Win32 iplik ↔ pthread tek kapı (anayasa §1.2 #1-6).
// void* cm_thread_create(void (*fn)(void*), void* arg);
// int cm_mutex_lock(cm_mutex*);  // CRITICAL_SECTION ↔ pthread_mutex_t
// int cm_mutex_unlock(cm_mutex*);
```

| # | İş | Windows | POSIX | Bağ |
|---|---|---|---|---|
| 1 | iplik yaratma | `_beginthreadex` | `pthread_create` | anayasa §1.2 #1 |
| 2 | mutex | `CRITICAL_SECTION` | `pthread_mutex_t` | anayasa §1.2 #3 |
| 3 | bekleme | `WaitForSingleObject` | `pthread_join` | anayasa §1.2 #5 |
| 4 | futex/hızlı yol | SRWLOCK | `futex` | anayasa §1.2 #4 |

## §13 Rafta 1 — PHP yüzeyi geniş örnek tablosu (S1/S4 karışık)

| # | PHP | Platform bağımlılığı | Kanıt sınıfı |
|---|---|---|---|
| 1 | `sys_get_temp_dir()` | tam soyut | S1 (§1.1) |
| 2 | `DIRECTORY_SEPARATOR` | tam soyut | S1 (§1.1) |
| 3 | `php_uname('s')` | OS adı okunur | S1 (§1.2) |
| 4 | `str_starts_with($real,$prefix)` | `realpath` gerektirir | S1 (§1.3) |
| 5 | `PHP_OS_FAMILY` | `Windows`/`Unix`/`Darwin` | S4 (kullanım yok) |
| 6 | `glob()` | dosya adı duyarsızlığı platforma bağlı | S4 (anayasa §1.1 #19) |
| 7 | `flock()` | Windows'ta farklı davranış | S4 (bilinmeyen, test edilmedi) |
| 8 | `proc_open` | süreç modeli farkı | S4 (surucu §3) |
| 9 | `getenv('XDG_DATA_HOME')` | Linux'ta yoksa `~/.local/share` | S4 (surucu §4 #2) |
| 10 | `realpath` | Windows'ta `\\?\` uzun yol ön eki | S4 (anayasa §1.1) |

## §14 Yanlış örnek arşivi — depoda karşılaşılmaması gerekenler

| # | Yanlış kalıp | Kırıldığı platform | Doğru yaklaşım |
|---|---|---|---|
| 1 | `$path = "C:\\data\\" . $file;` | Linux/macOS | `DIRECTORY_SEPARATOR` + `rtrim` |
| 2 | `if ($os == 'Linux') ...` tek dallı | Windows atlanır | `PHP_OS_FAMILY` + açık else |
| 3 | `set_time_limit` ile servis beklemek | süreç modeli | surucu §3 (SCM/systemd) |
| 4 | `unlink` sonrası hata yok sayma | tümü | hemen oku/kaydet (§3 kuralı) |
| 5 | BOM'lu UTF-8 dosya yazma | Windows Notepad dışı | BOM'suz UTF-8 |
| 6 | `chdir` + göreli yol | tümü | mutlak yol + realpath |

## §15 Ölçüm protokolü — örnek dosyası denetimi

| # | Adım | Beklenen |
|---|---|---|
| 1 | S1 blokları repo satırıyla eşleşiyor mu | 3/3 (§1.1-§1.3) |
| 2 | S4 bloklarda "derlendi" iddiası var mı | 0 |
| 3 | `// ...` dolgusu var mı | 0 |
| 4 | `TBD`/`Yakında` var mı | 0 |
| 5 | Son satır Authority | var |
| 6 | Placeholder'lar işaretli mi (`// placeholder, derlenmedi`) | %100 |
| 7 | Uydurma benchmark/ölçüm sayısı | 0 |
| 8 | Kod blokları S4 etiketli mi | %100 |

## §16 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | S1 | birebir repo satırı kanıtı |
| 2 | S4 | mimari yorum / PLANNED kalıp (kod kanıtsız) |
| 3 | Sarmalayıcı (wrapper) | iki platform API'sini tek imzada gizleyen ince katman (§3-§4) |
| 4 | Gard (guard) | derleme anında boyut/denklik denetimi (`static_assert`) (§2) |
| 5 | LP64 | 64-bit POSIX'te `long`=8 modeli (§2 #1) |
| 6 | Endianness | byte sıralaması; serileştirmede son (endianness) denetimi (§11) |

## §17 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-13 | İlk yazım — 3 S1 kanıt (TemplateEngine:73 · DesktopBff:34-38 · MusicStreamHandler:199-202), 4 URL doğrulandı (sysconf · pathconf · dbus-tutorial · appkit); `V1_chap04.html` 404 → `pathconf.html` ile değiştirildi |
| 2 | v1.0.1 | 2026-10-13 | §26 repo kanıtı ikinci dalga (7 satır), §25 süreç sarmalayıcı iskeleti, §18-§19 tam kalıplar, §20 eşleme tablosu (kesin değil işaretiyle) |

## §18 Rafta 2 — dinamik yükleme sarmalayıcısı tam kalıp (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
#if defined(_WIN32)
  #include <windows.h>
  using cm_lib_t = HMODULE;
  inline cm_lib_t cm_lib_open(const char* n) { return ::LoadLibraryA(n); }
  inline void*    cm_lib_sym(cm_lib_t h, const char* s) {
      return reinterpret_cast<void*>(::GetProcAddress(h, s));
  }
  inline void     cm_lib_close(cm_lib_t h) { ::FreeLibrary(h); }
#else
  #include <dlfcn.h>
  using cm_lib_t = void*;
  inline cm_lib_t cm_lib_open(const char* n) { return ::dlopen(n, RTLD_NOW); }
  inline void*    cm_lib_sym(cm_lib_t h, const char* s) { return ::dlsym(h, s); }
  inline void     cm_lib_close(cm_lib_t h) { ::dlclose(h); }
#endif
```

| # | Not | Bağ |
|---|---|---|
| 1 | Üç imza da aynı — yükleyici farkı gömülü | surucu §2.1 |
| 2 | `RTLD_NOW` ↔ derleme anında bağlama benzeri | surucu §2.1 #5 |
| 3 | `dlerror` hata yüzeyi sarmalayıcıya bağlanmalı | surucu §2.2 #1 |

## §19 Rafta 2 — dosya sistemi sarmalayıcısı tam kalıp (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
#include <string>
#include <filesystem>

// Wide/byte farkı: Windows UTF-16 (wchar_t), POSIX UTF-8 (char).
// std::filesystem::path her iki dünyayı da taşır — tercih edilen yol.
std::string cm_temp_dir() {
    return std::filesystem::temp_directory_path().string(); // platform soyut
}
```

| # | Fark | Windows | POSIX | Bağ |
|---|---|---|---|---|
| 1 | kodlama | UTF-16 (`wchar_t`) | UTF-8 (`char`) | anayasa §1.1 |
| 2 | ayırıcı | `\` | `/` | §4 |
| 3 | duyarlık | duyarsız | duyarlı | anayasa §1.1 #19 |
| 4 | geçici dizin | `%TEMP%` | `$TMPDIR`/`/tmp` | surucu §4 #6 |
| 5 | dosya adı sınırı | 255 karakter (yol 260 MAX_PATH) | 255 byte/dosya | surucu §4 |
| 6 | sembolik link | junction/symlink (yetki gerekir) | `symlink` yerel | anayasa §1.1 |

## §20 Rafta 2 — hata kodu eşleme tablosu (S4, PLANNED, kesin değil)

> Bu tablo **tamamlanmış değildir**; kesin eşleme Neva Engine'de yazılır. Burada yalnızca
> en sık 5 kodun yönü verilir — uydurma sayı yazılmaz.

| # | Kavram | Windows yüzeyi | POSIX yüzeyi |
|---|---|---|---|
| 1 | dosya yok | ERROR_FILE_NOT_FOUND (2) | `ENOENT` |
| 2 | erişim reddi | ERROR_ACCESS_DENIED (5) | `EACCES` |
| 3 | geçersiz argüman | ERROR_INVALID_PARAMETER (87) | `EINVAL` |
| 4 | disk dolu | ERROR_DISK_FULL (39) | `ENOSPC` |
| 5 | geçersiz yol | ERROR_PATH_NOT_FOUND (3) | `ENOTDIR` |

## §21 Rafta 2 — dosya sınırı sorgusu kalıbı (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
// sysconf ile sınır okuma (URL §9 #1): _SC_OPEN_MAX vs Windows GetLimitOfFileHandles.
long cm_max_open_files() {
#if defined(_WIN32)
    // PLANNED: _getmaxstdio / SetProcessAffinityMask değil — gerçek sınır API'si
    return 512; // placeholder, derlenmedi
#else
    return ::sysconf(_SC_OPEN_MAX); // POSIX (§9 #1)
#endif
}
```

| # | Not | Bağ |
|---|---|---|
| 1 | `512` bir **placeholder**'dır, ölçüm değildir | S4 dürüstlük |
| 2 | POSIX tarafı gerçek API | §9 #1 |
| 3 | Windows karşılığı için surucu §4 + Q-X22 | surucu §4 |
| 4 | `_SC_OPEN_MAX` sembolü `unistd.h` (POSIX) | §9 #1 |
| 5 | Placeholder döndürmek sınırı **gizler** — gerçek ölçüm PLANNED | S4 dürüstlük |

## §22 Bağlantı matrisi — örnek ↔ anayasa ↔ surucu

| # | Örnek | Anayasa bölümü | Surucu bölümü |
|---|---|---|---|
| 1 | `DIRECTORY_SEPARATOR` (§1.1) | §1.1 #1-3 | — |
| 2 | `php_uname` (§1.2) | §6 #2 | — |
| 3 | path traversal (§1.3) | §1.1 #1-3 | — |
| 4 | ABI gardları (§2) | §2.2-§2.3 | §1 #10 |
| 5 | hata sarmalayıcı (§3) | §3 #1-4 | R7 |
| 6 | yol/kütüphane (§4) | §1.1 · §1.3 | §2.1 |
| 7 | eşzamanlılık (§12) | §1.2 #1-6 | — |
| 8 | `sysconf` (§21) | — | §4 #7 |

## §23 Kapsam dışı — genişletme

| # | Konu | Nereye |
|---|---|---|
| 5 | `errno` ↔ Win32 kod tablosunun tamamı | `isletim-sistemi-api` |
| 6 | DSP önbellek/ SIMD örnekleri | `K004-dsp` |
| 7 | Test kalıpları (matris testi) | `isletim-sistemi-testing` |
| 8 | Derleme komut satırı örnekleri | `../derleme-ve-paketleme` |
| 9 | `#ifdef` katalog tamamı | `../tasinabilirlik-siniri` |
| 10 | DBus/IPC kalıpları (§9 #3 kaynağı) | `isletim-sistemi-runtime` |
| 11 | AppKit kalıpları (§9 #4 kaynağı) | `isletim-sistemi-turu` |
| 12 | `pathconf`/`sysconf` sınır tabloları | `isletim-sistemi-kernel` |

## §24 Açık sorular — genişletme

| # | Soru | Etiket |
|---|---|---|
| Q-X25 | `std::filesystem` tercihi C++20 min sürümle çakışıyor mu (GCC 8-)? | UNKNOWN |
| Q-X26 | Dosya sınırı sorgusu kalıbı (§21) Neva Engine planına giriyor mu? | UNKNOWN |
| Q-X27 | §20 kod eşleme tablosu hangi dosyada SSOT olacak? | UNKNOWN |

## §25 Rafta 2 — süreç başlatma sarmalayıcı iskeleti (S4, PLANNED)

```cpp
// S4 — PLANNED, derlenmedi
// Windows: CreateProcess + WaitForSingleObject (surucu §3 #10 — zombie kavramı yok)
// POSIX:   fork+exec veya posix_spawn + waitpid
// int cm_spawn_blocking(const char* cmd);
// int cm_spawn_detached(const char* cmd); // servis değil, arka plan
```

| # | İş | Windows | POSIX | Bağ |
|---|---|---|---|---|
| 1 | başlat | `CreateProcessA` | `posix_spawn` | surucu §3 |
| 2 | bekle | `WaitForSingleObject` | `waitpid` | surucu §3 #10 |
| 3 | son kod | `GetExitCodeProcess` | `WEXITSTATUS` | surucu §3 |
| 4 | öldür | `TerminateProcess` | `SIGKILL` | — |
| 5 | gizli başlat | DETACHED_PROCESS | `setsid` | surucu §3 #1 |

## §26 Repo kanıtı — ikinci dalga okuma kaydı

| # | Dosya | Satır | Okuma | Ne gösteriyor |
|---|---|---|---|---|
| 1 | `shared/src/Component/TemplateEngine.php` | 73 | `Select-Object -Index 72` | `DIRECTORY_SEPARATOR` (§1.1) |
| 2 | `shared/src/Api/Bff/DesktopBff.php` | 34 | `Select-Object -Index 33` | `meta['system']` başlangıcı (§1.2) |
| 3 | `shared/src/Api/Bff/DesktopBff.php` | 35 | `Select-Object -Index 34` | `php_uname('s')` (§1.2) |
| 4 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 199 | `Select-Object -Index 198` | `$prefix` kurulumu (§1.3) |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 200 | `Select-Object -Index 199` | `str_starts_with` (§1.3) |
| 6 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 201 | `Select-Object -Index 200` | `fail(403)` (§1.3) |
| 7 | `home.coremusic.net/include/Stream/MusicStreamHandler.php` | 202 | `Select-Object -Index 201` | kapanış parantez (§1.3) |

**Dürüstlük satırı:** Yukarıdaki 7 satır birebir okunmuştur; depoda C/C++ dosyası
**yoktur** (`*.cpp`/`*.h` glob = 0) — bu yüzden §2-§4, §11-§12, §18-§19, §21, §25
bloklarının tamamı **S4 (PLANNED, derlenmedi)** sınıfındadır ve "çalışıyor" olarak
kullanılamaz.

## §27 Ölçüm kaydı (bu dosyanın doğrulaması)

| # | Ölçüm | Sonuç |
|---|---|---|
| 1 | URL HEAD 200 | 4/4 (sysconf · pathconf · dbus-tutorial · appkit) |
| 2 | 404 eleme | 1 (`V1_chap04.html` → `pathconf.html`) |
| 3 | Envanter kesişimi | 0 |
| 4 | S1 kanıt satırı | 7 (§26) |
| 5 | S4 etiketli blok | %100 (PLANNED bloklarda "derlendi" iddiası 0) |
| 6 | Mojibake | 0 |
| 7 | `parent:` alanı | 0 (yalnız `parent_id`) |
| 8 | `group` | `kod` (ornekler eşlemesi) |
| 9 | Son satır Authority | var |
| 10 | Git commit | atılmadı (orchestrator'a ait) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode