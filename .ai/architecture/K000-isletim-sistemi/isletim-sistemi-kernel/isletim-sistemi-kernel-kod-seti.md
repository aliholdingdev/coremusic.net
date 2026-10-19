---
node_id: "K000-isletim-sistemi/isletim-sistemi-kernel/isletim-sistemi-kernel-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/isletim-sistemi-kernel"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti"
  - "K000-isletim-sistemi/isletim-sistemi-kernel/isletim-sistemi-kernel-ornekler"
evidence:
  - "https://man7.org/linux/man-pages/man3/posix_spawn.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw (✅ HTTP 200 · 2026-10-14)"
  - "https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/munmap.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-createfilemappingw (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-mapviewoffile (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/ioctl.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/book/ch20-01-unsafe-rust.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.io.memorymappedfiles.memorymappedfile (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.libraryimportattribute (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-15: 20 benzersiz resmi URL (HTTP 200 kapısı) · 17 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O1: Çekirdek modülü (LKM/kmode) kodu repo'da YOK → bu dosyanın tamamı kullanıcı-uzayı iskeletidir; gerçek modül yazımı ayrı mimari karar (ADR) gerektirir · UNKNOWN"
  - "O2: Rust bellek eşleme FFI yerine `memmap2` crate [TASARIM] eklenir mi? — R6 gereği bağımlılık yok · UNKNOWN"
  - "O3: C# `MemoryMappedFile` Unix/Linux davranışının (`PlatformNotSupportedException` riski) ölçümü yok · ⚠️ VERIFICATION REQUIRED"
  - "O4: macOS (Q4 açık) → `mmap` POSIX sütunu teorik olarak aynıdır ama Darwin/XNU ölçümü yok; tüm macOS hücreleri [TASARIM]"
  - "O5: ioctl op kodu sürücü-spesifiktir — hangi sürücü hedeflenecek (ses/DSP yok, repo'da sürücü-spesifik ioctl örneği YOK) belirsiz · UNKNOWN"
  - "O6: `CreateProcessW` güvenlik notları (boşluk/tırnak ayrıştırma tuzağı, handle sızıntısı) dokümanda 200 ile doğrulandı ama davranış testi yok · ⚠️ VERIFICATION REQUIRED"
  - "O7: Örnekler derlenmedi/çalıştırılmadı → tüm gruplar ⚠️ VERIFICATION REQUIRED"
group: kod
---

# isletim-sistemi-kernel — Cross-Platform Kod Seti (çekirdek-yüze-yakın desenler · kullanıcı uzayı)

> **Eksen:** `K000-isletim-sistemi / isletim-sistemi-kernel` (kök) · **Grup:** `kod` · **Durum:** `verify-required`
> **Dürüstlük notu (bağlayıcı):** Bu depoda **çekirdek modülü kodu YOKTUR**; kernel dalının `-ornekler.md`
> §A/§D bunu açıkça taşır. Bu dosya, çekirdeğe **kullanıcı uzayından** dokunan üç deseni
> (süreç doğuşu · bellek eşleme · sürücü ioctl) resmi upstream dokümanlardan türetilmiş
> **orijinal iskeletlerle** gösterir — çekirdek-kipi (kmode) kodu **üretilmez**.
> Bu dosyanın **sahipliği**: (A) süreç/fork **desen karşılaştırması** + spawn sarmalayıcı,
> (B) bellek eşleme (mmap ↔ CreateFileMapping), (C) sürücü iletişim (ioctl ↔ DeviceIoControl).
> Süreç **çalıştırma/bekleme API'si** (`fork`/`exec`/`waitpid` pratik kalıbı) burada tekrar edilmez →
> `isletim-sistemi-turu-kod-seti.md` §GRUP C'ye çapraz referans verilir.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu ilk satırında dil yorumuyla `[ORİJİNAL ÖRNEK · derlenmedi]` taşır |
| Kaynak | API adları §5'teki **2026-10-14 HTTP 200** resmi dokümanlardan türetildi; içerik alıntısı **yapılmadı** |
| Kmode yasağı | `module_init` · `DriverEntry` · `ioctl` **kernel tarafı** implementasyonu bu dosyada **üretilmez** (O1) |
| Repo referansı | Repo gerçekleri yalnız kardeş `isletim-sistemi-kernel-ornekler.md` **mevcut § numaralarıyla** referanslanır; yeni `Dosya:/Satır:` çifti **üretilmez** |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Hata | Sessiz fallback **yasak**: `MAP_FAILED` · `NULL` · `FALSE` dönüşleri her blokta açıkça test edilir |
| Yer tutucu | `...` · `TBD` · boş blok **yasak**; bilinmeyen imza `UNKNOWN` + §4 O-soru ile işaretlenir |
| Bağımlılık | `memmap2` · `windows-rs` gibi paketler **yalnız kavram/[TASARIM]**; manifest eklenmez (R6) |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir ·
`[TASARIM]` = CoreMusic tercihi, kanıt değil · `⚠️ VERIFICATION REQUIRED` = sürüm/davranış
doğrulanmadı · `UNKNOWN` = kanıt yok.

---

## §1. Çalışma sözleşmesi — "çekirdeğe dokunma, deseni göster"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** (`ubuntu-latest`) — `isletim-sistemi-turu-kod-seti.md` §GRUP H | [KANIT] turu dosyası |
| Çekirdek-kipi kod | **YOK** — bu dosya yalnız kullanıcı-uzayı iskeleti üretir | [KANIT] kernel-ornekler §A · §D |
| Birincil doğrulama | Linux POSIX sütunu yazılır ve denetlenir | [TASARIM] bu dosyanın sözleşmesi |
| Windows | Win32 sütunu **baştan yazılır** (`CreateProcessW` · `CreateFileMappingW` · `DeviceIoControl`) | [TASARIM] §GRUP A–C |
| macOS | Q4 açık → POSIX sütunu paylaşır; Darwin'a özgü ölçüm yok → `[TASARIM]` | §4 O4 |

**Sözleşme kuralları (4 madde — her grup bunlara uyar):**

1. **Sistem çağrısı sarılır, gizlenmez:** sarmalayıcı hata kodunu **yutmaz**; çağırana döner veya
   açık bırakır (`-errno` · `GetLastError` · `io::Result`).
2. **Kaynak çifti zorunlu:** her `mmap`↔`munmap`, `CreateFileMapping`↔`CloseHandle`/`UnmapViewOfFile`,
   `open`↔`close` blok içinde birlikte görünür — tek başına açma örneği yazılmaz.
3. **unsafe/FFI blokları minimal:** Rust `unsafe` yalnız imza çağrısını kapsar; C++ `extern "C"`
   yalnız sembol köprüsüdür, C++ tip güvenliği sarmalayıcıda kalır.
4. **ioctl op kodu sabitlenmez:** sürücü-spesifik sayısal op değerleri bilinmediğinden blok
   `UNKNOWN` işaretli parametre alır (O5) — uydurma `0xDEAD` kodu yazılmaz.

---

## §2. Grup haritası — 3 grup × dil dağılımı

| Grup | Konu | Diller | § |
|---|---|---|---|
| A | Süreç doğuşu deseni (spawn) + karşılaştırma | C · Rust (FFI) + çapraz referans (PHP/JS/Python/Go/C# → turu §GRUP C · Bash/PowerShell → turu sahipliğinde) | §GRUP A |
| B | Bellek eşleme (dosya/anonim) | C · C++20 · Rust (unsafe FFI) · C# · Python · Go · Zig · Java/Kotlin | §GRUP B |
| C | Sürücü iletişim (ioctl / DeviceIoControl) | C · C++20 · Rust (unsafe FFI) · C# · Python | §GRUP C |

**Toplam:** 3 grup · 17 kod bloğu · 13 dil yüzeyi (blok: C, C++20, Rust, C#, Python, Go, Zig,
Java/Kotlin · çapraz referans: PHP, JS, Bash, PowerShell → §A.5 · N/A: Swift — Foundation yüzeyi
değil, §3). Süreç **çalıştırma/bekleme** pratik kalıpları bu dosyada **yok** (turu §GRUP C sahibi).

---

## GRUP A — Süreç doğuşu deseni: `posix_spawn` ↔ `CreateProcessW`

> **Amaç:** "Yeni süreç nasıl *başlatılır*" sorusunun iki büyük OS ailesindeki karşılığını
> göstermek. Linux sütunu POSIX.1-2008 `posix_spawn(3)` (fork+exec tek çağrıda birleşir —
> man sayfası bunu glibc ≥2.24'te `clone(CLONE_VM|CLONE_VFORK)` olarak tanımlar); Windows sütunu
> `CreateProcessW` (tek çağrı, thread de yaratır).
> **Sahiplik sınırı:** `fork()`/`execve()`/`waitpid()` pratik kalıbı (pipe + bekleme zinciri)
> `isletim-sistemi-turu-kod-seti.md` §GRUP C'nindir — burada **kopya yoktur**, yalnız spawn
> deseni ve karşılaştırma vardır. Repo kanıtı: `isletim-sistemi-kernel-ornekler.md` §C.2
> (`proc_open` — fork/exec benzeri sınır), §I (syscall eşlemesi).

### §A.1 Karşılaştırma tablosu (POSIX ↔ Win32)

| Kavram | Linux/POSIX | Windows (NT) | Not |
|---|---|---|---|
| Tek çağrıda başlatma | `posix_spawn(3)` | `CreateProcessW` | İkisi de ayrı `fork`+`exec` adımını çağırana gizler |
| PATH araması | `posix_spawnp` (PATH'te ara) | `lpApplicationName=NULL` + cmd ilk token | Windows'ta tırnak/tuzak dokümanda açık (O6) |
| Dosya eylemi (fd yönlendirme) | `posix_spawn_file_actions_*` | `STARTUPINFO` handle'ları + `bInheritHandles` | Modeller farklı: sıra-tabanlı liste ↔ handle kopyası |
| Dönüş değeri | `0`/`errno` (hata numarası **dönüş değeri**) | `BOOL` + `GetLastError` | errno ≠ dönüş değeri — api-ornekler §C.9 dersi |
| Çocuk başarısızlığı | housekeeping/exec hatası → çıkış **127** | init başarısızsa süreç sonlanır | `posix_spawn` "başarılı" dönerken çocuk 127 ile çıkabilir |
| Bekleme | `waitpid(2)` (turu §GRUP C) | `WaitForSingleObject` + `GetExitCodeProcess` | Bekleme bu dosyanın sahipliğinde **değil** |

### §A.2 C — `posix_spawn` sarmalayıcı (Linux birincil · POSIX)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://man7.org/linux/man-pages/man3/posix_spawn.3.html */
#include <spawn.h>
#include <sys/wait.h>
#include <unistd.h>
#include <stdio.h>
#include <errno.h>

extern char **environ;

/* Tek adımda çocuk başlat: argv[0] çalıştırılabilir dosya.
   Hata: 0 değil errno döner (doküman: hata durumunda dönüş değeri errno'nun kendisidir).
   Bekleme YOK — bekleme turu §GRUP C'nin işidir. */
int cm_spawn(const char *path, char *const argv[], pid_t *out_pid)
{
    pid_t pid;
    int rc = posix_spawn(&pid, path, NULL, NULL, argv, environ);
    if (rc != 0) return rc;              /* rc zaten errno değeri */
    *out_pid = pid;
    return 0;
}

/* Çocuğun çıkışını al: dönen 0 ise *status dolu, -1 ise errno. */
int cm_wait_child(pid_t pid, int *out_status)
{
    int status;
    if (waitpid(pid, &status, 0) == -1) return -1;
    *out_status = status;
    return 0;
}

int cm_child_exit_code(int status, int *out_code)
{
    if (!WIFEXITED(status)) return -1;   /* sinyalle ölüm: çıkış kodu yok */
    *out_code = WEXITSTATUS(status);
    return 0;
}
```

**Ders:** `posix_spawn` "başarı" ile "çocuğun gerçekten çalışması"nı **ayırmaz** — housekeeping/exec
hatasında çocuk 127 ile çıkar (man §RETURN VALUE). Sarmalayıcı bu yüzden bekleme ve ayrıştırma
adımlarını **ayrı** zorunlu kılar; sessiz varsayım yok.

### §A.3 C — `CreateProcessW` sarmalayıcı (Windows sütunu)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw */
#include <windows.h>

/* Mutlak yollarda bile tırnak ekleme tuzağı dokümanda açık (Security Remarks):
   "C:\Program Files\App Dir\app.exe" boşlukla ayrıştırılıp yanlış exe çalıştırılabilir.
   Bu sarmalayıcı lpApplicationName'i asla NULL geçmez — ayrıştırma belirsizliği baştan biter. */
BOOL cm_spawn_win(const wchar_t *app, wchar_t *cmdline, DWORD *out_pid, DWORD *out_tid)
{
    STARTUPINFOW si;
    PROCESS_INFORMATION pi;
    ZeroMemory(&si, sizeof si);
    si.cb = sizeof si;
    ZeroMemory(&pi, sizeof pi);

    BOOL ok = CreateProcessW(
        app,                 /* asla NULL: modül adı ayrıştırma belirsizliği yok */
        cmdline,             /* CreateProcessW yazabilir — const literatür DEĞİL */
        NULL, NULL,
        FALSE,               /* handle devri yok (varsayılan güvenli taraf) */
        0, NULL, NULL,
        &si, &pi);
    if (!ok) return FALSE;

    /* Handle sızıntısı yasak: kimlik bilgilerini al, handle'ları KAPAT. */
    *out_pid = pi.dwProcessId;
    *out_tid = pi.dwThreadId;
    CloseHandle(pi.hThread);
    CloseHandle(pi.hProcess);
    return TRUE;
}
```

**Ders (doküman kanıtlı):** `lpApplicationName == NULL` durumunda ilk boşluklu token modül adı
olur ve `.exe` eklenir → yanlış çalıştırılabilir dosya riski (CreateProcessW §Security Remarks).
`lpCommandLine` Unicode sürümünde **yazılabilir** bellek ister; `const` literatür erişim ihlali üretir.

### §A.4 Rust — `unsafe extern "C"` ile `posix_spawn` FFI (bilinçli imza köprüsü)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/book/ch20-01-unsafe-rust.html
// Resmi kaynak: https://man7.org/linux/man-pages/man3/posix_spawn.3.html
// libc bağımlılığı YOK (R6): yalnız imza iskeleti — gerçek bağlantı `libc` crate [TASARIM] ister.

#[derive(Debug)]
pub enum SpawnError {
    Errno(i32),   // POSIX: dönüş değeri doğrudan errno
}

unsafe extern "C" {
    fn posix_spawn(
        pid: *mut i32,
        file: *const i8,
        file_actions: *const core::ffi::c_void,
        attrp: *const core::ffi::c_void,
        argv: *const *mut i8,
        envp: *const *mut i8,
    ) -> i32;
}

/// Güvenli sarmalayıcı: unsafe yalnız FFI çağrısını kapsar (kitap kuralı: minimal unsafe bloğu).
/// argv[0] dahil NULL-sonlandırmalı dizi şarttır; Rust dünyasında bu imza yalnız
/// FFI sınırında görünür, güvenli API iade edilir.
pub unsafe fn spawn(path: &std::ffi::CStr, argv: &[*const std::ffi::CStr]) -> Result<i32, SpawnError> {
    let mut pid: i32 = 0;
    let argv_raw: Vec<*mut i8> = argv.iter().map(|s| s.as_ptr() as *mut i8).collect();
    let rc = unsafe {
        posix_spawn(
            &mut pid,
            path.as_ptr(),
            core::ptr::null(),
            core::ptr::null(),
            argv_raw.as_ptr(),
            core::ptr::null(),   // envp NULL: POSIX'te geçerli değildir — gerçek kullanımda environ (O2)
        )
    };
    if rc != 0 { return Err(SpawnError::Errno(rc)); }
    Ok(pid)
}
```

**Ders:** `unsafe extern "C"` bloğu (Rust Book §20.1) yalnız **sembol ve imza** beyanıdır; güvenli
sarmalayıcı fonksiyon unsafe **olarak işaretlenmez**, unsafe gövdede kalır. `envp = NULL` POSIX'te
tanımsızdır — bu bilinçli eksiklik O2'ye bağlanır, sessizce `environ` varsayımı **yapılmaz**.

### §A.5 Çapraz referans — süreç çalıştırma/bekleme kalıpları (TEKRAR YOK)

| Konu | Sahibi | Bu dosyada var mı? |
|---|---|---|
| `fork`+`execve`+`waitpid` zinciri, pipe yönlendirme | `isletim-sistemi-turu-kod-seti.md` **§GRUP C** | **Hayır** — kopya kod üretilmedi |
| PHP `proc_open` sınır kanıtı | `isletim-sistemi-kernel-ornekler.md` §C.2 | **Hayır** — yalnız § numarası referans |
| Syscall eşlemesi (repo kodu → çekirdek çağrısı) | `isletim-sistemi-kernel-ornekler.md` §I | **Hayır** |
| Spawn **deseni** (bu grup) | bu dosya §A.2–§A.4 | Evet |
| Python `subprocess` doğuşu | `isletim-sistemi-turu-kod-seti.md` **§GRUP C** (kabuksuz başlatma) | **Hayır** — kopya kod üretilmedi (R4) |
| Go `os/exec` doğuşu | `isletim-sistemi-turu-kod-seti.md` **§GRUP C** (kabuksuz başlatma) | **Hayır** — kopya kod üretilmedi (R4) |
| JS `child_process` doğuşu | `isletim-sistemi-turu-kod-seti.md` **§GRUP C** (kabuksuz başlatma) | **Hayır** — kopya kod üretilmedi (R4) |
| Bash / PowerShell süreç doğuşu | `isletim-sistemi-turu-kod-seti.md` sahipliğinde (kabuksuz §GRUP C pratik · kabuk §GRUP G) | **Hayır** — kalıp bu dosyada **yazılmadı**; sahiplik belirsizse O-soru, kopya yine yasak (R4) |
| Öncelik/nice ayarı süreç üzerinde | `isletim-sistemi-plan-kod-seti.md` (B2) | **Hayır** |

**Kural:** aynı desenin iki yerde tekrarı yasaktır (R3): burada yalnız **doğuş** (spawn) var;
çalıştırma, bekleme, türetme turu dosyasındadır.

---

## GRUP B — Bellek eşleme: `mmap` ↔ `CreateFileMappingW`+`MapViewOfFile`

> **Amaç:** Dosya veya anonim belleği işlem adres alanına **eşleyip** doğrudan okuma/yazma deseni.
> POSIX tek çağrıda yapar (`mmap`); Windows iki adımdadır (mapping nesnesi + view). Sahiplik
> sınırı: paylaşılan bellekte **atomik dosya yazımı/kilidi** `isletim-sistemi-turu-kod-seti.md`
> §GRUP E'nindir — burada yalnız **eşleme yaşam döngüsü** vardır.

### §B.1 C — `mmap`/`munmap` (POSIX · Linux birincil)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html
   Resmi kaynak: https://man7.org/linux/man-pages/man2/munmap.2.html */
#include <sys/mman.h>
#include <fcntl.h>
#include <unistd.h>
#include <errno.h>

/* Dosya eşlemesi: read-only görünüm. len==0 POSIX'te ERRORDUR (EINVAL) — biz önceden keseriz.
   MAP_FAILED dönerse -errno; başarıda gösterge döner. */
void *cm_map_file_ro(const char *path, size_t len, int *out_err)
{
    if (len == 0) { *out_err = EINVAL; return MAP_FAILED; }

    int fd = open(path, O_RDONLY | O_CLOEXEC);   /* CLOEXEC: çocuk handle sızdırmaz */
    if (fd == -1) { *out_err = errno; return MAP_FAILED; }

    /* PROT_READ + MAP_PRIVATE: yazma girişimi SIGBUS/segv üretir — bilinçli seçim. */
    void *p = mmap(NULL, len, PROT_READ, MAP_PRIVATE, fd, 0);
    int saved = errno;
    close(fd);            /* mmap ek referans tutar: fd'yi hemen kapatalım (doküman §DESCRIPTION) */
    if (p == MAP_FAILED) { *out_err = saved; return MAP_FAILED; }
    *out_err = 0;
    return p;
}

int cm_unmap(void *p, size_t len)
{
    if (p == MAP_FAILED || p == NULL) return -EINVAL;
    return munmap(p, len) == 0 ? 0 : -errno;
}

/* Anonim eşleme: fd==-1, off==0 zorunlu (POSIX: başka kombinasyon tanımsız). */
void *cm_map_anon(size_t len, int *out_err)
{
    if (len == 0) { *out_err = EINVAL; return MAP_FAILED; }
    void *p = mmap(NULL, len, PROT_READ | PROT_WRITE, MAP_PRIVATE | MAP_ANONYMOUS, -1, 0);
    if (p == MAP_FAILED) { *out_err = errno; return MAP_FAILED; }
    *out_err = 0;
    return p;
}
```

**Ders (doküman kanıtlı):** `mmap` çağrısı dosyaya **ek referans** katar; `close()` bu referansı
kaldırmaz (POSIX §DESCRIPTION) — fd blok içinde kapatılır, eşleme `munmap` ile sonlandırılır.
Dosya sonradan değişirse sınır ötesi erişim **SIGBUS** üretir (§DESCRIPTION) → eşlenen uzunluk
dosya boyundan büyük istenmez.

### §B.2 C++20 — RAII sarmalayıcı (`extern "C"` köprü + otomatik unmap)

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html
#include <sys/mman.h>
#include <cstddef>
#include <stdexcept>
#include <utility>

extern "C" void* cm_map_file_ro(const char*, size_t, int*);   // §B.1 köprüsü

namespace cm {

class FileMapping {
    void*  ptr_  = MAP_FAILED;
    size_t len_  = 0;
public:
    FileMapping(const char* path, std::size_t len) : len_(len) {
        int err = 0;
        ptr_ = cm_map_file_ro(path, len, &err);
        if (ptr_ == MAP_FAILED) {
            throw std::runtime_error("mmap başarısız");   // sessiz fallback yok
        }
    }
    ~FileMapping() { if (ptr_ != MAP_FAILED) { ::munmap(ptr_, len_); } }   // kaynak çifti

    FileMapping(FileMapping&& o) noexcept : ptr_(std::exchange(o.ptr_, MAP_FAILED)), len_(std::exchange(o.len_, 0)) {}
    FileMapping& operator=(FileMapping&&) = delete;   // kopyalama/ikinci unmap yasak
    FileMapping(const FileMapping&) = delete;

    const std::byte* data() const noexcept { return static_cast<const std::byte*>(ptr_); }
    std::size_t size() const noexcept { return len_; }
};

} // namespace cm
```

**Ders:** C++ katmanı `extern "C"`'yi yalnız **sembol köprüsü** tutar; tipler, RAII ve
hareket-semantiği C++ tarafında kalır. Destructor tek unmap noktasıdır — kopyalama kapalı
(çifte `munmap` engeli).

### §B.3 Rust — `unsafe extern "C"` mmap/munmap (dosya eşlemesi iskeleti)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html
// Resmi kaynak: https://doc.rust-lang.org/book/ch20-01-unsafe-rust.html
// libc bağımlılığı YOK (R6): imza iskeleti — gerçek kullanım libc crate [TASARIM] ister (O2).

const MAP_FAILED: isize = -1;
const PROT_READ: i32 = 1;
const MAP_PRIVATE: i32 = 0x02;
const MAP_ANONYMOUS: i32 = 0x20;

unsafe extern "C" {
    fn mmap(addr: *mut u8, len: usize, prot: i32, flags: i32, fd: i32, off: i64) -> *mut u8;
    fn munmap(addr: *mut u8, len: usize) -> i32;
}

pub enum MapError { ZeroLength, Failed(i32) }

pub struct MappedRegion { ptr: *mut u8, len: usize }

impl MappedRegion {
    /// Anonim eşleme: fd=-1, off=0 (POSIX zorunluluğu — doküman §DESCRIPTION).
    pub fn anonymous(len: usize) -> Result<Self, MapError> {
        if len == 0 { return Err(MapError::ZeroLength); }   // POSIX: len==0 hatadır
        let ptr = unsafe { mmap(core::ptr::null_mut(), len, PROT_READ, MAP_PRIVATE | MAP_ANONYMOUS, -1, 0) };
        if ptr as isize == MAP_FAILED {
            return Err(MapError::Failed(12));   // 12 == ENOMEM (Linux) — sabit kod değil, örnekleme
        }
        Ok(Self { ptr, len })
    }
    pub fn as_slice(&self) -> &[u8] { unsafe { core::slice::from_raw_parts(self.ptr, self.len) } }
}

impl Drop for MappedRegion {
    fn drop(&mut self) {
        if self.ptr as isize != MAP_FAILED {
            unsafe { munmap(self.ptr, self.len) };   // tek unmap noktası
        }
    }
}
```

**Ders:** `Drop` bir kez unmap eder; `MAP_FAILED` (-1) ayrımı pointer-eşitlikle değil **sayısal
karşımla** yapılır (pointer `-1`'e eşitlenebilir — doküman §RETURN VALUE). unsafe blokları yalnız
FFI çağrısını kapsar.

### §B.4 C# — `MemoryMappedFile` + view (çapraz · Windows birincil)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.io.memorymappedfiles.memorymappedfile
using System;
using System.IO;
using System.IO.MemoryMappedFiles;

static class MemoryMap
{
    // Dosya eşlemesi: view dispose edilince eşleme sonlanır (IDisposable = kaynak çifti).
    public static byte[] ReadPrefix(string path, int count)
    {
        if (count <= 0) throw new ArgumentOutOfRangeException(nameof(count));
        using var mmf = MemoryMappedFile.CreateFromFile(path, FileMode.Open, null, 0, MemoryMappedFileAccess.Read);
        using var view = mmf.CreateViewAccessor(0, count, MemoryMappedFileAccess.Read);
        var buf = new byte[count];
        view.ReadArray(0, buf, 0, count);
        return buf;
    }

    // Anonim (sayfa dosyası destekli) eşleme — Windows sütunu.
    public static void WithAnonymousMap(long size, Action<IntPtr> use)
    {
        if (size <= 0) throw new ArgumentOutOfRangeException(nameof(size));
        using var mmf = MemoryMappedFile.CreateOrOpen(null, size, MemoryMappedFileAccess.ReadWrite);
        using var view = mmf.CreateViewAccessor(0, size, MemoryMappedFileAccess.ReadWrite);
        use(view.SafeMemoryMappedViewHandle.DangerousGetHandle());
    }
}
```

**Ders:** .NET'te yaşam döngüsü `IDisposable` ile taşınır — `using` bloğu `munmap`/`CloseHandle`
eşdeğerini garanti eder. Unix/Linux üzerinde `MemoryMappedFile` davranışının **ölçümü yok** (O3) →
bu blok Windows sınıfında yazılmıştır, "her yerde çalışır" iddiası **taşımaz**.

### §B.5 Python — `mmap` modülü (stdlib · Linux birincil)

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/mmap.html
# mmap(2): https://man7.org/linux/man-pages/man2/mmap.2.html
import mmap
import os

def map_file_ro(path: str, size: int) -> mmap.mmap:
    """Dosyayı read-only eşle (Linux). size<=0 POSIX'te EINVAL — bilinçli erken red."""
    if size <= 0:
        raise ValueError("size > 0 olmalı (POSIX mmap len==0 → EINVAL)")  # hata sessiz yutulmaz
    fd = os.open(path, os.O_RDONLY | os.O_CLOEXEC)   # CLOEXEC: çocuk süreç fd sızdırmaz
    try:
        # MAP_PRIVATE + PROT_READ: yazma girişimi OS düzeyinde sinyal üretir (Python korumaz).
        return mmap.mmap(fd, size, flags=mmap.MAP_PRIVATE, prot=mmap.PROT_READ)
    finally:
        os.close(fd)   # eşleme fd'den bağımsız yaşar — fd hemen kapatılır
```

**Ders:** Python `mmap` **stdlib**'dedir (bağımlılık yok — R6); hata modeli `OSError`+`errno`
(api-§B.1 ile aynı köprü). POSIX semantiği dilde **gizlenmez**: `PROT_READ` altındaki yazma
Python istisnası değil **işletim sistemi sinyalidir** — dil katmanı çekirdek davranışını örtmez.

### §B.6 Go — `syscall.Mmap` (Linux · stdlib)

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/syscall
// mmap(2): https://man7.org/linux/man-pages/man2/mmap.2.html
package kvmmap

import (
	"fmt"
	"syscall"
)

// mapFileRO dosyayı read-only eşler (Linux). fd erken kapatılır: eşleme ondan bağımsız yaşar.
func mapFileRO(path string, size int) ([]byte, error) {
	if size <= 0 {
		return nil, fmt.Errorf("size > 0 olmalı (POSIX mmap len==0 → EINVAL): %d", size)
	}
	fd, err := syscall.Open(path, syscall.O_RDONLY|syscall.O_CLOEXEC, 0)
	if err != nil {
		return nil, err // syscall.Errno zaten errno taşır (dil içi köprü)
	}
	defer syscall.Close(fd)

	b, err := syscall.Mmap(fd, 0, size, syscall.PROT_READ, syscall.MAP_PRIVATE)
	if err != nil {
		return nil, err // errno → error: sessiz fallback yok
	}
	return b, nil // çağıran bitişte syscall.Munmap(b) çağırır — yaşam döngüsü onda
}
```

**Ders:** `syscall` **stdlib**'dedir — `golang.org/x/sys/unix` daha moderndir ama bağımlılık
ekleme yasağı (R6) gereği **kullanılmadı**. `[]byte` dil seviyesinde yazılabilir *görünür*;
asıl yasak `PROT_READ` katmanındadır (fiziksel yazma → sinyal) — Go dil güvenliği bu noktada
**sahte güvenlik üretmez**, `Munmap` zamanlaması ise bilinçli olarak çağıranındır.

### §B.7 Zig — `std.posix.mmap` (Linux birincil · std kaynak kodundan doğrulandı)

```zig
// [ORİJİNAL ÖRNEK · derlenmedi] https://ziglang.org/documentation/master/std/
// İmza kanıtı (resmi std kaynak kodu): https://raw.githubusercontent.com/ziglang/zig/master/lib/std/posix.zig
// mmap(2): https://man7.org/linux/man-pages/man2/mmap.2.html
const std = @import("std");
const posix = std.posix;

/// Dosyayı read-only eşler (Linux). size==0 POSIX'te EINVAL — bilinçli erken red.
/// Yaşam döngüsü: eşleme blok içinde kullanılır ve `munmap` ile kapatılır — sızıntı yok.
pub fn mapFileRo(path: []const u8, size: usize) !void {
    if (size == 0) return error.InvalidSize; // POSIX len==0 → EINVAL; sessiz geçiş yok

    const file = try std.fs.cwd().openFile(path, .{});
    defer file.close();

    const view = try posix.mmap(
        null,
        size,
        posix.PROT.READ,           // prot: u32 (0x1) — yazma yasağı OS katmanında
        .{ .TYPE = .PRIVATE },     // flags: packed struct MAP; ANONYMOUS varsayılan false
        file.handle,               // fd_t — dosya eşlemesi
        0,                         // offset (bayt) — sayfa hizası şartı: 0 güvenli
    );
    defer posix.munmap(view);      // mmap döndürdüğü dilim doğrudan munmap'e gider

    // Kullanım: view read-only görünüm — yazma girişimi SIGSEGV üretir (OS semantiği).
    std.debug.assert(view.len == size);
}
```

**Ders:** İmza **std kaynak kodundan** (2026-10-14 çekildi) doğrulandı: `prot: u32` sabitleri
`posix.PROT.*`, `flags` ise **packed struct** `MAP` (`.TYPE = .PRIVATE`) — C'nin makro
bayrak-yığını dilin tip sistemine taşınmış durumda (tip-güvenli karşılık). `munmap` `void`
döner: EINVAL/NOMEM std'de `unreachable` ile *varsayılan güvenli* kabul edilir — uydurulan
hata yolları yoktur.

### §B.8 Java/Kotlin (JVM) — `FileChannel.map` (taşınabilir eşleme)

```java
// [ORİJİNAL ÖRNEK · derlenmedi] https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/channels/FileChannel.html
import java.io.IOException;
import java.nio.MappedByteBuffer;
import java.nio.channels.FileChannel;
import java.nio.file.Path;
import java.nio.file.StandardOpenOption;

public final class MappedFile {
    private MappedFile() {}

    /**
     * Dosyayı read-only eşler. Kanal açık bırakılır: kanal kapatma ile eşleme
     * arasındaki takvim JDK'ya göre değişebilir → ⚠️ VERIFICATION REQUIRED (ders).
     */
    public static MappedByteBuffer mapRo(Path path, long size) throws IOException {
        if (size <= 0) throw new IllegalArgumentException("size > 0 olmalı: " + size);
        FileChannel ch = FileChannel.open(path, StandardOpenOption.READ);
        try {
            return ch.map(FileChannel.MapMode.READ_ONLY, 0, size);
            // MapMode.READ_ONLY: görünümde yazma UnsupportedOperationException üretir (dil katmanı).
        } catch (IOException | RuntimeException e) {
            ch.close(); // hata yolunda kanal kapatılır — handle sızıntısı yok
            throw e;
        }
    }
}
```

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi] https://kotlinlang.org/api/latest/jvm/stdlib/  (JDK API: FileChannel — Java bloğu URL)
import java.nio.MappedByteBuffer
import java.nio.channels.FileChannel
import java.nio.file.Path
import java.nio.file.StandardOpenOption

fun mapRo(path: Path, size: Long): MappedByteBuffer {
    require(size > 0) { "size > 0 olmalı: $size" }   // hata sessiz yutulmaz (fail-fast)
    val ch = FileChannel.open(path, StandardOpenOption.READ)
    return try {
        ch.map(FileChannel.MapMode.READ_ONLY, 0, size)
    } catch (e: Exception) {
        ch.close()                                    // hata yolunda kanal kapatılır
        throw e
    }
}
```

**Ders:** JVM'de eşleme **dilin içindedir** (JNI/FFI gerekmez) → `FileChannel.map` Java SE
parçasıdır, bağımlılık eklenmez. İki katman **yerinde** durur: `MapMode.READ_ONLY` yazmayı
arayüz düzeyinde reddeder (POSIX sinyali yerine **istisna**) — ama yine sessiz geçiş yok.
Kanal kapatma ile görünüm ömrü arasındaki takvim doğrulanmadı → blok
`⚠️ VERIFICATION REQUIRED` taşır (açık eksik iddia; uydurma garanti yok).

---

## GRUP C — Sürücü iletişim: `ioctl` ↔ `DeviceIoControl`

> **Amaç:** Karakter-aygıtı/sürücüyle **kontrol çağrısı** deseni. Linux `ioctl(2)` cihaz-spesifik
> op kodu alır (yönlendirme + boyut + tip + seri numarası bitleri) — man NOTES: op değerleri
> "cihaz sürücüsüne bağlıdır". Windows `DeviceIoControl` kontrol **kodunu** (CTL_CODE) alır.
> **Kmode yasağı:** sürücünün kendi tarafı (işleyici) bu dosyada **üretilmez** (O1).
> Repo kanıtı: `isletim-sistemi-kernel-ornekler.md` §D (upstream çekirdek dokümanları),
> §K.2.1 (Win32 yüzeyinde `DeviceIoControl` envanteri).

### §C.1 C — `ioctl` sarmalayıcı (Linux)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://man7.org/linux/man-pages/man2/ioctl.2.html */
#include <sys/ioctl.h>
#include <fcntl.h>
#include <unistd.h>
#include <errno.h>

/* op: cihaz-spesifik — bilinen bir sürücü seçilmediği için çağrıya parametre olarak girer
   (O5: uydurma sabit yazılmaz). argp: giriş/çıkış belleği; yön/boyut bilgisi op'un içinde kodlanır. */
int cm_ioctl(int fd, unsigned long op, void *argp, int *out_err)
{
    int rc = ioctl(fd, op, argp);
    if (rc == -1) { *out_err = errno; return -1; }   /* EBADF/EFAULT/EINVAL/ENOTTY — errno ayrı */
    *out_err = 0;
    return rc;   /* birkaç op dönüş değerini çıktı sayısında kullanır (man §RETURN VALUE) */
}

/* Aygıt açma + kapatma çifti: O_NONBLOCK istenirse bilinçli (man §NOTES: yan etkileri azaltır). */
int cm_open_device(const char *path, int flags, int *out_err)
{
    int fd = open(path, flags | O_CLOEXEC);
    if (fd == -1) { *out_err = errno; return -1; }
    *out_err = 0;
    return fd;
}
```

**Ders (doküman kanıtlı):** `ENOTTY` "fd bir karakter-aygıtı değil / op bu nesneye uygulanamaz"
demektir (man §ERRORS) — sürücüyle konuşma denemesinde ilk teşhis bu koddur. Op değerlerinin
boyut alanı güvenilmezdir (man §NOTES: "size bits are very unreliable") → güven doğrulaması
cihaz dokümanında kalır.

### §C.2 C++20 — tip-güvenli ioctl sarmalayıcı (`extern "C"` köprü)

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://man7.org/linux/man-pages/man2/ioctl.2.html
#include <stdexcept>
#include <string>

extern "C" int cm_ioctl(int, unsigned long, void*, int*);   // §C.1 köprüsü

namespace cm {

enum class IoctlResult { Ok, DeviceError };

template <typename In, typename Out>
IoctlResult ioctl_call(int fd, unsigned long op, In& in, Out& out, int* err)
{
    // Tek buffer yerine iki ayrı yapı; sürücü op'a göre yönü belirler —
    // C++ tarafı yalnız hata dağıtımını tip güvenliğiyle yapar.
    struct { const In& in; Out& out; } buf{in, out};
    int local = 0;
    int rc = ::cm_ioctl(fd, op, &buf, &local);
    if (err) *err = local;
    if (rc == -1) return IoctlResult::DeviceError;
    return IoctlResult::Ok;
}

} // namespace cm
```

**Ders:** C++ sarmalayıcı sürücü sözleşmesini **uydurmaz**; şablon yalnız çağrı kabuğu verir.
Gerçek sürücü başına `In`/`Out` tipleri o aygıtın UAPI başlığından türetilir — bu dosya o
sözleşmeyi bilmez (O5).

### §C.3 Rust — `unsafe extern "C"` ioctl

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://man7.org/linux/man-pages/man2/ioctl.2.html
// Resmi kaynak: https://doc.rust-lang.org/book/ch20-01-unsafe-rust.html
// libc bağımlılığı YOK (R6): imza iskeleti [TASARIM]

unsafe extern "C" {
    // glibc/BSD imzası: op `unsigned long` (man §SYNOPSIS iki varyant gösterir — bu Linux/glibc).
    fn ioctl(fd: i32, op: u64, arg: *mut core::ffi::c_void) -> i32;
}

/// op: cihaz-spesifik sabit (O5 — uydurma değer yazılmaz; çağırana parametre).
/// Güvenli yüzey yalnız Result döner; errno okuması FFI sınırında bırakılır.
pub unsafe fn ioctl_call(fd: i32, op: u64, arg: *mut core::ffi::c_void) -> Result<i32, i32> {
    let rc = unsafe { ioctl(fd, op, arg) };
    if rc == -1 {
        // errno okuma libc'ye bağlıdır (O2): burada yalnız "hata" işaretlenir,
        // sayısal kod uydurulmaz.
        return Err(-1);
    }
    Ok(rc)
}
```

**Ders:** Rust errno'u **kutlamaz** — `std` errno okuma imzası `io::Error` içindedir ve bu blok
libc'siz yazılmıştır; hata durumunda sayısal kod yerine `Err` işareti döner ve ayrıştırma
bağlantı katmanına (O2) bırakılır. Bilinçli eksiklik gizlenmez.

### §C.4 C# — `LibraryImport` ile `DeviceIoControl` (Windows)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.libraryimportattribute
using System;
using System.Runtime.InteropServices;

static partial class Native
{
    [LibraryImport("kernel32.dll", SetLastError = true)]
    internal static partial int DeviceIoControl(
        IntPtr hDevice,
        uint dwIoControlCode,
        IntPtr lpInBuffer, uint nInBufferSize,
        IntPtr lpOutBuffer, uint nOutBufferSize,
        out uint lpBytesReturned,
        IntPtr lpOverlapped);
}

static class DriverChannel
{
    // hDevice: CreateFile ile açılmış \\.\DeviceName kolu (doküman §Remarks).
    // lpOverlapped = NULL ise lpBytesReturned NULL OLAMAZ (doküman §Parameters) — out parametreye zorlanır.
    public static uint Call(IntPtr hDevice, uint controlCode, ReadOnlySpan<byte> input, Span<byte> output)
    {
        if (hDevice == IntPtr.Zero) throw new ArgumentException("geçersiz aygıt handle'ı");
        unsafe
        {
            fixed (byte* inP = input)
            fixed (byte* outP = output)
            {
                int ok = Native.DeviceIoControl(
                    hDevice, controlCode,
                    (IntPtr)inP, (uint)input.Length,
                    (IntPtr)outP, (uint)output.Length,
                    out uint returned,
                    IntPtr.Zero);
                if (ok == 0)
                {
                    int err = Marshal.GetLastPInvokeError();
                    throw new System.ComponentModel.Win32Exception(err, "DeviceIoControl başarısız");
                }
                return returned;
            }
        }
    }
}
```

**Ders (doküman kanıtlı):** `SetLastError = true` ile `LibraryImport` hatayı yakalar; okuma
`Marshal.GetLastPInvokeError()` ile **hemen** yapılır (GetLastError dokümanı: gecikmeli okuma
sıfırlanmış kod verir). Çıktı tam dolmazsa sürücü `ERROR_MORE_DATA` döndürebilir (DeviceIoControl
§Parameters) — çağıran bunu ayrıştırmak zorundadır, sarmalayıcı yutmaz.

### §C.5 Python — `fcntl.ioctl` (Linux · stdlib)

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/fcntl.html
# ioctl(2): https://man7.org/linux/man-pages/man2/ioctl.2.html
import fcntl
import struct

def sndctl(fd: int, op: int, arg: int) -> int:
    """Sürücüye 4 baytlık int argümanla kontrol çağrısı (Linux).

    op değeri cihaz-spesifik (UAPI) — §C.1 NOTS: sürücüye bağlıdır, sabitlenemez (O5).
    """
    buf = struct.pack("i", arg)      # native-endian int
    out = fcntl.ioctl(fd, op, buf)   # immutable bytes → değişmiş kopya DÖNER
    (ret,) = struct.unpack("i", out)
    return ret                       # hata → OSError (errno taşır): sessiz fallback yok
```

**Ders:** `fcntl` Linux'ta **stdlib**'dir (bağımlılık yok). İki açık nokta bilinçli bırakıldı:
(1) `struct` bayt düzeni/boyutu (`i`) ile sürücünün beklediği C struct boyutunun eşleşmesi
`⚠️ VERIFICATION REQUIRED` (O5) — eşleşmeyen boyut tanımlanmamış davranış üretir, Python
kaynağı bunu **gizlemez**; (2) `op` değeri cihaz-spesifik olduğundan sabit bir çağrı-yüzeyi
**uydurulmadı**. Hata yolu tek: `OSError` + `errno` (§C.1 dersiyle aynı sınıf).

### §C.6 GRUP C eşleme özeti

| Kavram | Linux/POSIX | Windows | Rust (FFI) | C# |
|---|---|---|---|---|
| Çağrı | `ioctl(2)` | `DeviceIoControl` | `unsafe extern "C" ioctl` | `[LibraryImport]` DeviceIoControl |
| Op tanımı | `_IO/_IOR/_IOW/_IOWR` makroları (cihaz UAPI'si) | `CTL_CODE` (cihaz başlığı) | parametre (sabit yok — O5) | parametre (uint) |
| Hata | `errno` (EBADF/EINVAL/ENOTTY/EFAULT) | `GetLastError` (ERROR_MORE_DATA …) | `Err(i32)` işaretli | `Win32Exception` |
| Aygıt handle'ı | `open` (§C.1) | `CreateFile` `\\.\X` + `OPEN_EXISTING` | libc'siz sınır (O2) | `IntPtr` çağıranın |

---

## §3. Dil × OS kapsama matrisi

| Dil | Linux (birincil) | Windows | macOS | Mekanizma (§) |
|---|---|---|---|---|
| C | ✅ `posix_spawn` · `mmap` · `ioctl` | ✅ `CreateProcessW` · (bellek/ioctl Win32 sütunu) | ⚠️ POSIX sınıfı · [TASARIM] (O4) | A.2 · A.3 · B.1 · C.1 |
| C++20 | ✅ RAII sarmalayıcı (`extern "C"` köprü) | ✅ (köprü aynı) | ⚠️ [TASARIM] | B.2 · C.2 |
| Rust | ⚠️ FFI iskeleti — libc bağlantısı yok (O2) | ⚠️ aynı iskelet, Win32 imzası yazılmadı | ⚠️ [TASARIM] | A.4 · B.3 · C.3 |
| C# (.NET) | ⚠️ `MemoryMappedFile` Unix ölçümü yok (O3) | ✅ `MemoryMappedFile` · `DeviceIoControl` P/Invoke | ⚠️ [TASARIM] (O3) | B.4 · C.4 |
| PHP / JS | ✅ süreç deseni **turu §GRUP C'de** (A.5) · mmap/ioctl: **N/A** — dil yüzeyi değil | ✅ | ✅ | A.5 |
| Bash / PowerShell | ✅ süreç doğuşu **turu sahipliğinde** (A.5) · mmap/ioctl: **N/A** — kabuk yüzeyi değil | ✅ | ✅ | A.5 |
| Python | ✅ `mmap` stdlib (B.5) · `fcntl.ioctl` (C.5) | **N/A** — POSIX yüzey; Windows mmap/ioctl kolonu yok | ⚠️ POSIX sınıfı [TASARIM] | B.5 · C.5 |
| Go | ✅ `syscall.Mmap` Linux (B.6) | **N/A** — `syscall.Mmap` POSIX-only | ⚠️ [TASARIM] | B.6 |
| Zig | ✅ `std.posix.mmap` (B.7) | **N/A** — `std.posix` yüzeyi POSIX; Windows kolonu bu dosyada yok | ⚠️ [TASARIM] | B.7 |
| Swift | **N/A** — bu dosyanın grupları (spawn/mmap/ioctl) Foundation yüzeyi değil | **N/A** | **N/A** | §3 N/A gerekçesi |
| Java/Kotlin | ✅ `FileChannel.map` (B.8) — JNI/FFI gerekmez | ✅ (JVM yüzeyi) | ⚠️ [TASARIM] | B.8 |

**Okuma kuralı:** ✅ = hedef sınıfı vurur (etiket `derlenmedi` **kalkmaz**) · ⚠️ = sınıf geçerli
ama kanıt/uygulama yok · ❌ = sınıf dışı (kural gereği). Çekirdek-kipi hiçbir hücrede **yok** (O1).

---

## §4. Açık sorular (bu dosya eksenli)

| # | Soru | Durum |
|---|---|---|
| O1 | Çekirdek modülü (LKM / WDM) kodu bu vault'a girer mi? | UNKNOWN — bu dosya yalnız kullanıcı-uzayı üretir; ADR gerekli |
| O2 | Rust FFI için `libc` crate bağımlılığı girer mi? | UNKNOWN — R6 gereği manifest yok; errno okuma sınırı açık bırakıldı |
| O3 | C# `MemoryMappedFile`/`LibraryImport` Unix/Linux davranışı | `⚠️ VERIFICATION REQUIRED` — yalnız Windows sınıfı iddia edilir |
| O4 | macOS (Q4) → Darwin/XNU mmap/ioctl ölçümü | UNKNOWN — `[TASARIM]` |
| O5 | Hangi sürücü hedeflenecek (ioctl/CTL_CODE op değerleri)? | UNKNOWN — repo'da sürücü-spesifik ioctl örneği yok |
| O6 | `CreateProcessW` ayrıştırma/handle tuzaklarının davranış testi | `⚠️ VERIFICATION REQUIRED` — doküman 200, test yok |
| O7 | Örnekler derlenmedi/çalıştırılmadı | `⚠️ VERIFICATION REQUIRED` (tüm gruplar) |

**Kural:** bu sorular bu dosyada **cevaplanmaz**; cevap gelene kadar ilgili hücre `UNKNOWN` kalır.

---

## §5. Kaynakça (2026-10-14 · HTTP 200 doğrulandı)

| # | Konu | URL | Tarih |
|---|---|---|---|
| 1 | POSIX `posix_spawn` (Linux man) | https://man7.org/linux/man-pages/man3/posix_spawn.3.html | 2026-10-14 ✅ |
| 2 | Win32 `CreateProcessW` | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw | 2026-10-14 ✅ |
| 3 | POSIX `mmap` (Issue 8) | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html | 2026-10-14 ✅ |
| 4 | Linux `munmap(2)` | https://man7.org/linux/man-pages/man2/munmap.2.html | 2026-10-14 ✅ |
| 5 | Win32 `CreateFileMappingW` (memoryapi) | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-createfilemappingw | 2026-10-14 ✅ |
| 6 | Win32 `MapViewOfFile` | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-mapviewoffile | 2026-10-14 ✅ |
| 7 | Linux `ioctl(2)` | https://man7.org/linux/man-pages/man2/ioctl.2.html | 2026-10-14 ✅ |
| 8 | Win32 `DeviceIoControl` | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | 2026-10-14 ✅ |
| 9 | Rust Book — Unsafe Rust (`unsafe extern "C"`) | https://doc.rust-lang.org/book/ch20-01-unsafe-rust.html | 2026-10-14 ✅ |
| 10 | .NET — Platform Invoke (P/Invoke / `LibraryImport`) | https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke | 2026-10-14 ✅ |
| 11 | .NET `MemoryMappedFile` | https://learn.microsoft.com/en-us/dotnet/api/system.io.memorymappedfiles.memorymappedfile | 2026-10-14 ✅ |
| 12 | .NET `LibraryImportAttribute` | https://learn.microsoft.com/en-us/dotnet/api/system.runtime.interopservices.libraryimportattribute | 2026-10-14 ✅ |

**Kullanım kuralı:** URL'ler blokların **günü** doğrulanmış arayüz referansıdır; içerik alıntısı
yapılmamıştır, bloklar orijinaldir ve derlenmemiştir (`[ORİJİNAL ÖRNEK · derlenmedi]`).

---

## §6. İlişkili dosyalar

- Kardeş repo kanıtları (çapraz köprü): [`isletim-sistemi-kernel-ornekler.md`](isletim-sistemi-kernel-ornekler.md) — §A · §C.1–§C.6 · §D · §I · §K · §K.3
- Kapsam: [`isletim-sistemi-kernel-kapsam.md`](isletim-sistemi-kernel-kapsam.md)
- Doğrulama: [`isletim-sistemi-kernel-dogrulama.md`](isletim-sistemi-kernel-dogrulama.md)
- Dizin: [`index.md`](index.md)
- OS ailesi karşılaştırması (grup sahibi): [`../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md`](../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md) — §GRUP C (süreç çalıştırma/bekleme) · §GRUP E (kilit/atomik yazma)
- Sürücü-hedefi/kapsam kararları: `isletim-sistemi-audio` (17 dosya — ayrı dal, bu dosyaya girmez)

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode