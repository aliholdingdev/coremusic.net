---
node_id: "K000-isletim-sistemi/os-donanim-tespiti/os-donanim-tespiti-kod-seti"
node_type: code
status: verify-required
parent_id: "K000-isletim-sistemi/os-donanim-tespiti"
children: []
depends_on: ["K000-isletim-sistemi/isletim-sistemi-turu/isletim-sistemi-turu-kod-seti", "K000-isletim-sistemi/os-donanim-tespiti/os-donanim-tespiti-ornekler"]
evidence:
  - "https://man7.org/linux/man-pages/man3/sysconf.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.kernel.org/doc/html/latest/filesystems/proc.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man5/proc.5.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man5/proc_cpuinfo.5.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man5/proc_meminfo.5.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.kernel.org/filesystems/sysfs.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getsysteminfo (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/ns-sysinfoapi-system_info (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/wmisdk/wmi-start-page (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/sysctl.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/iokit (✅ HTTP 200 · 2026-10-14)"
  - "https://en.cppreference.com/w/cpp/thread/thread/hardware_concurrency (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/os.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.python.org/3/library/ctypes.html (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/runtime (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/net (✅ HTTP 200 · 2026-10-14)"
  - "https://pkg.go.dev/github.com/shirou/gopsutil/v4 (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/thread/fn.available_parallelism.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.rs/sysinfo/latest/sysinfo/ (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.environment.processorcount (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.gc.getgcmemoryinfo (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.gcmemoryinfo (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/api/system.net.networkinformation.networkinterface (✅ HTTP 200 · 2026-10-14)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/ (✅ HTTP 200 · 2026-10-14)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/group___control.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.alsa-project.org/alsa-doc/alsa-lib/group___hint.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.kernel.org/doc/html/latest/sound/designs/procfile.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.kernel.org/doc/html/latest/sound/alsa-configuration.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-enumaudioendpoints (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/combaseapi/nf-combaseapi-coinitializeex (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/coreaudio (✅ HTTP 200 · 2026-10-14)"
  - "https://developer.apple.com/documentation/coreaudio/audioobjectgetpropertydata(_:_:_:_:_:_:) (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man3/getifaddrs.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man8/ip.8.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/iphlpapi/nf-iphlpapi-getadaptersaddresses (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/desktop/api/iptypes/ns-iptypes-ip_adapter_addresses_lh (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/fs.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-15: 54 benzersiz resmi URL (HTTP 200 kapısı) · 26 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O-A1: macOS CPU/bellek sysctl MIB alt anahtar adları resmi sayfada geçmiyor → ad uydurulmadı, blok bilinçli eksik (§4)"
  - "O-A2: C#/PowerShell fiziksel RAM ölçümü için resmi API doğrulanmadı — GC.GetGCMemoryInfo().TotalAvailableMemoryBytes RAM ölçümü DEĞİL (§A.8)"
  - "O-A3: WMI sınıf/özellik adları (Win32_Processor vb.) WMI başlangıç sayfasında geçmiyor → yalnız kavram (§A sonrası not)"
  - "O-B2/O-B3: macOS CoreAudio tam imza ve C# COM interop CLSID/IID metinleri HTML'de yok → iki blok bilinçli eksik (§B.3, §B.4)"
  - "O-C2: Python getifaddrs FFI struct düzeni (offset/alignment) doğrulanmadı → blok bilinçli eksik (§C.3)"
  - "Tam açık soru listesi §4 (17 madde). Örnekler derlenmedi/çalıştırılmadı → ⚠️ VERIFICATION REQUIRED (tüm gruplar)"
group: kod
---

# os-donanim-tespiti — OS Donanım Tespiti Cross-Platform Kod Seti (CPU/bellek · ses aygıtı · ağ arayüzü)

> **Eksen:** `K000-isletim-sistemi / os-donanim-tespiti` (kök) · **Grup:** `kod` · **Durum:** `verify-required`
> **Sözleşme:** Üretim/CI **yalnız Linux'ta** koşar; kod ise **Windows · Linux · macOS** sınıflarında
> çalışacak biçimde yazılır (aynı sözleşme `isletim-sistemi-turu-kod-seti.md` §1). Bu dosya üç
> keşif işini — **CPU/bellek tespiti (GRUP A)**, **ses aygıtı enumerasyonu (GRUP B)**, **ağ/arayüzü
> enumerasyonu (GRUP C)** — dil başına birer blokla, **kod iskeleti** düzeyinde kurar.
> **Sahiplik sınırı:** ses zincirinin DSP/effekt ayrıntısı bu dosyanın **dışındadır** →
> `../isletim-sistemi-audio/isletim-sistemi-audio-ornekler.md`.
> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-14 · **Mod:** RED TEAM / TRUTH MODE

---

## §0. Politika (bu dosyanın kanıt dili — bağlayıcı)

| Kural | Açıklama |
|---|---|
| Örnek etiketi | Her kod bloğu **ilk satırında** `[ORİJİNAL ÖRNEK · derlenmedi]` + varsa `Resmi kaynak: <URL>` taşır |
| Repo referansı | Repo gerçekleri **kardeş dosyadan** (`os-donanim-tespiti-ornekler.md` §1.1–§1.5, §8.2, §12) referanslanır; **yeni `Dosya:/Satır:` çifti üretilmez** |
| Kaynak | Dil/arayüz adları §5'teki **2026-10-14 HTTP 200** resmi sayfalardan türetildi; sayfa metni alıntılanmadı, imza düzeyinde okundu |
| Derleme | Örnekler derlenmedi/çalıştırılmadı → tüm davranış iddiaları `⚠️ VERIFICATION REQUIRED` |
| Bilinçli eksik | İmza doğrulanamıyorsa blok **boş bırakılmaz**: çağrı satırı üretilmez, işlev açık hata/`-2` döner ve O-soru açılır |
| Üçüncü parti | `gopsutil` · `sysinfo` · `alsa-lib` kurulumu **yalnız `[KAVRAM]`/`[TASARIM]`** — `go.mod`/`Cargo.toml`/`composer.json` değişmez (plan R6) |
| Yasak | `...`/`TBD` dolgusu · sessiz fallback · uydurma URL/sembol/`/proc` girişi · "çalışıyor" iddiası · DSP'ye girmek |

**Etiket sözlüğü:** `[ORİJİNAL ÖRNEK · derlenmedi]` = mimari niyeti gösterir · `[KANIT]` = disk/URL
kanıtı · `[TASARIM]` = CoreMusic tercihi, kanıt değil · `[KAVRAM]` = bağımlılık/kurulum anlatımı,
kod yok · `⚠️ VERIFICATION REQUIRED` = sürüm/imza doğrulanmadı · `UNKNOWN` = bilinmiyor ·
`YOK` = o yüzey bu sette işlenmez.

---

## §1. Çalışma sözleşmesi — "koşan tek Linux, yazılan her yerde"

| Katman | Karar | Kanıt sınıfı |
|---|---|---|
| Üretim/CI runtime | **Linux** | [KANIT] `isletim-sistemi-turu-kod-seti.md` §1 (turu dosyası sahibi) |
| OS-seviyesi keşif kodu repo'da | **YOK** (`SetupDi`/`udev`/`devnode`/`PnP` grep 0) | [KANIT] `os-donanim-tespiti-ornekler.md` §1.2 |
| Uygulama-seviyesi cihaz kodu | `shared/src/Device/` envanteri = keşif **DEĞİL** | [KANIT] `-ornekler.md` §1.1 · sınır: §1.3 |
| UI'da görünen cihaz tahmini | keşif değildir, çıktı yüzeyidir | [KANIT] `-ornekler.md` §1.3, §1.5 |
| macOS | repo'da koşucu/üretim kanıtı **yok** | [BİLİNMEYEN] Q4 → sütunlar `[TASARIM]` |
| IOCTL / cihaz erişimi | keşif **değil**; IOCTL numarası uydurulmaz | [KANIT] `-ornekler.md` §8.2 |

**Sözleşme kuralları (6 madde — her grup bunlara uyar):**

1. **Keşif tek noktadan** yapılır (§GRUP A/B/C); UI tahmini veya sabit liste keşif sayılmaz
   (`-ornekler.md` §1.3, §1.5). Bu dosya kodu **üretir**, repo'da varmış gibi sunmaz.
2. **Sessiz fallback yok.** Okunamayan/çıkmayan yüzey = açık hata (`throw`/`return -1|-2`/`exit`)
   veya `UNKNOWN`; `0`/boş liste ile "bulundu" sayılmaz. Sanal/eksik durum "yok" ile karıştırılmaz.
3. **Sabit yol/anahtar yazılmaz**: `/proc` ve `/dev/snd` yolları ortam değişkeniyle ezilebilir;
   Windows yolları kütüphaneden gelir; dil ayracı kütüphanesine bırakılır
   (`isletim-sistemi-turu-kod-seti.md` §B serisi).
4. **Üçüncü parti paket eklenmez.** Bağımlılık gerektiren yüzey (Go bellek `gopsutil`, Rust bellek
   `sysinfo`, ALSA kurulumu) `[KAVRAM]` olarak anlatılır; blok içinde paket çağrısı **yazılmaz**.
5. **Keşif ≠ erişim ≠ DSP.** Sürücü/IOCTL erişimi ayrı konu (`-ornekler.md` §8.1–§8.3); ses
   zinciri efekt/işleme ayrı dosya (`../isletim-sistemi-audio/`). Bu dosya yalnız **enumerasyon**.
6. **macOS kanıtsızdır.** macOS hücreleri `[TASARIM]` kalır; doğrulanamayan imza **uydurulmaz**,
   bilinçli eksik blok + O-soru açılır (§A.3, §B.3).

---

## §2. Grup haritası — 3 grup × dil dağılımı

| Grup | Konu | Dil blokları (adet) | Birincil kanıt yüzeyi | Çapraz referans |
|---|---|---|---|---|
| **A** | CPU / bellek tespiti | C-POSIX · C-Win32 · C-macOS · C++20 · Python · Go · Rust · C# · bash · PowerShell · Zig · Swift · Java/Kotlin (**13**) | `sysconf(3)` · `/proc`+`/sys` · `GetSystemInfo`/`GlobalMemoryStatusEx` · `sysctl(3)`/IOKit · dil stdlib | `isletim-sistemi-turu-kod-seti.md` §A (platform tespiti) |
| **B** | Ses aygıtı enumerasyonu | C-ALSA · C++20-MMDevice · C++-CoreAudio · C#-COM · Python-FFI · PHP-sınır (**6**) | ALSA `snd_card_*`/`snd_device_name_*` · `/proc/asound`+`/dev/snd` · MMDevice `EnumAudioEndpoints` · CoreAudio | `../isletim-sistemi-audio/` (DSP sahibi) |
| **C** | Ağ / arayüzü enumerasyonu | C-getifaddrs · C-Win32 · Python-FFI · Go · bash · PowerShell (**6**) | `getifaddrs(3)` · `GetAdaptersAddresses` · `ip(8)` · `net.Interfaces` | `isletim-sistemi-turu-kod-seti.md` §C (süreç/kabuk değil — ağ) |

**Toplam: 25 kod bloğu · 43 resmi URL (§5 · 2026-10-14 P4 eki: Zig · Swift · Java/Kotlin).**

**Dil slotu dışı notu:** Node.js bu grupların dil listesinde **yer almıyor** (plan §3.2 satır 9).
`https://nodejs.org/api/fs.html` sayfası 200 doğrulandı; buna rağmen Node ile donanım enumerasyonu
bu sette **işlenmiyor** — sahiplik Q4 kapsamında (`YOK`, §3).

---

### GRUP A — CPU / bellek tespiti (envanterin ilk satırı)

**Amaç:** "Bu makinede kaç mantıksal CPU ve ne kadar bellek var?" sorusunu **keşif anında**, OS'nin
kendi yüzeyinden sormak. Linux yüzeyi **`/proc` + `/sys`** (`https://www.kernel.org/doc/html/latest/filesystems/proc.html`
· `https://docs.kernel.org/filesystems/sysfs.html`), Windows yüzeyi **`GetSystemInfo` /
`GlobalMemoryStatusEx`** (WMI ikinci katman olarak §A sonrası notta), macOS yüzeyi **`sysctl(3)` /
IOKit**. Repo'da bu işi yapan OS-seviyesi kod **yok** (kanıt: `-ornekler.md` §1.2) — bu grup
**yeni** kod iskeletidir, mevcut kodun tekrarı değil.

#### §A.1 C — POSIX `sysconf(3)` (Linux · macOS)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://man7.org/linux/man-pages/man3/sysconf.3.html
     _SC_NPROCESSORS_ONLN : "The number of processors currently online (available)."
     _SC_PHYS_PAGES       : "The number of pages of physical memory." (çarpımın taşabileceği uyarısı sayfada)
     _SC_PAGESIZE         : "Size of a page in bytes."
   Hata sözleşmesi: sysconf() hatada -1 döndürür (sayfanın RETURN VALUE bölümü). */
#include <stdio.h>
#include <errno.h>
#include <string.h>
#include <unistd.h>

typedef struct {
    long online_cpus;
    long total_bytes;
    int  ok;              /* 0 = okunamadı → çağıran bilir; "0" ile dolmaz */
} host_cpu_mem;

int host_cpu_mem_posix(host_cpu_mem *out)
{
    out->ok = 0;

    long cpus = sysconf(_SC_NPROCESSORS_ONLN);
    if (cpus < 0) {
        fprintf(stderr, "sysconf(_SC_NPROCESSORS_ONLN): %s\n", strerror(errno));
        return -1;
    }

    long pages = sysconf(_SC_PHYS_PAGES);
    if (pages < 0) {
        fprintf(stderr, "sysconf(_SC_PHYS_PAGES): %s\n", strerror(errno));
        return -1;
    }

    long psize = sysconf(_SC_PAGESIZE);
    if (psize < 0) {
        fprintf(stderr, "sysconf(_SC_PAGESIZE): %s\n", strerror(errno));
        return -1;
    }

    out->online_cpus = cpus;
    out->total_bytes = pages * psize;   /* 64-bit long gerekir — sayfadaki taşma uyarısı */
    out->ok = 1;
    return 0;
}
```

**Ders:** `_SC_PHYS_PAGES * _SC_PAGESIZE` taşması dokümanda **açıkça yazılı** → 32-bit derlemede
uzun aritmetik zorunlu (`⚠️ VERIFICATION REQUIRED`: bu blok 32-bit'te denenmedi).

#### §A.2 C — Windows `GetSystemInfo` + `GlobalMemoryStatusEx`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getsysteminfo
     VOID GetSystemInfo([out] LPSYSTEM_INFO lpSystemInfo);  → dönüş değeri YOK
     WOW64 altında doğru sonuç için sayfa GetNativeSystemInfo'yu işaret eder.
   https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/ns-sysinfoapi-system_info
     SYSTEM_INFO.dwNumberOfProcessors: "The number of logical processors in the current group."
     SYSTEM_INFO.dwPageSize          : sayfa boyutu / koruma granülaritesi
   https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex
     BOOL GlobalMemoryStatusEx(LPMEMORYSTATUSEX lpBuffer); → MEMORYSTATUSEX.dwLength = sizeof(MEMORYSTATUSEX) */
#include <windows.h>
#include <stdio.h>

int host_cpu_mem_win(unsigned long *out_cpus, unsigned long long *out_total_bytes)
{
    SYSTEM_INFO si;
    ZeroMemory(&si, sizeof si);
    GetSystemInfo(&si);                       /* VOID: hata dönüşü yok → alanlar ayrıca okunur */
    *out_cpus = si.dwNumberOfProcessors;      /* "current group" sınırı → O-A5 */

    MEMORYSTATUSEX ms;
    ZeroMemory(&ms, sizeof ms);
    ms.dwLength = sizeof ms;                  /* şarta bağlı çağrı: bu satır olmadan fonksiyon başarısız olur */
    if (!GlobalMemoryStatusEx(&ms)) {
        fprintf(stderr, "GlobalMemoryStatusEx başarısız: %lu\n", GetLastError());
        return -1;
    }
    *out_total_bytes = ms.ullTotalPhys;
    return 0;
}
```

**Ders:** `dwNumberOfProcessors` **işlemci grubuna** ait sayıdır (>64 mantıksal CPU düzenlerinde
grup sınırı görünür) → `⚠️ VERIFICATION REQUIRED` + **O-A5**. WOW64 ayrımı (sayfadaki
`GetNativeSystemInfo` notu) bu iskelete **eklenmedi** → bilinçli eksik.

#### §A.3 C — macOS `sysctl(3)` / `CTL_HW` (bilinçli eksik — ad uydurulmadı)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   BİLİNÇLİ EKSİK — çağrı satırı bilerek üretilmedi (neden: O-A1).
   Resmi kaynak: https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/sysctl.3.html
     Sayfa sysctl(3) ve CTL_HW ("Generic CPU, I/O") hiyerarşisini belgeler.
   ⚠️ VERIFICATION REQUIRED (O-A1): hw.ncpu · hw.memsize · machdep.cpu gibi ALT anahtar adları
   bu sayfada GEÇMİYOR → adlar uydurulmadı. Çağrı satırı bilerek yazılmadı. */
#include <stdio.h>
#include <sys/types.h>
#include <sys/sysctl.h>

int host_cpu_mem_macos_bilinc_eksik(unsigned long *out_cpus, unsigned long long *out_total_bytes)
{
    (void)out_cpus;
    (void)out_total_bytes;

    /* CTL_HW biliniyor; alt anahtar bilinmiyor → "bulunamadı" DEĞİL, "bilinmiyor". */
    fputs("macOS CPU/bellek: sysctl alt anahtarlari dogrulanmadi -> UNKNOWN (O-A1)\n", stderr);
    return -2;                                /* -1 = OS hatası, -2 = bilinçli eksik */
}
```

**Ders:** Sessiz fallback yasağı burada işler: ad uydurmak yerine blok **bilerek eksik** bırakılır
ve `-2` ile ayrılır. İkinci macOS yüzeyi IOKit (`https://developer.apple.com/documentation/iokit`)
yalnızca `[KAVRAM]` — donanım ağacı okuma bu setin scope dışı (K001 sözleşmesi).

#### §A.4 C++20 — `std::thread::hardware_concurrency()`

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://en.cppreference.com/w/cpp/thread/thread/hardware_concurrency
//   cppreference ISO belgesi DEĞİL referans sitesidir (dil dürüstlüğü notu) · noexcept · "only a hint"
//   hesaplanamazsa 0 döner.
#include <thread>
#include <optional>
#include <cstdio>

std::optional<unsigned> logical_cpus_hint()
{
    unsigned n = std::thread::hardware_concurrency();   // noexcept
    if (n == 0) {
        std::fprintf(stderr, "hardware_concurrency() = 0 -> bilinmiyor (0 cekirdek degil)\n");
        return std::nullopt;                            // sessiz fallback yok
    }
    return n;
}
```

**Bellek:** C++20 standart kütüphanesinde **fiziksel bellek okuma API'si yoktur** → yüzey devri
`[TASARIM]`: POSIX için §A.1, Windows için §A.2, macOS için §A.3. Bu devir **kopya değildir**;
aynı C kaynağı iki yerde aynı işi yapmaz.

#### §A.5 Python — `os.cpu_count()` + `os.sysconf_names`

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/os.html
#   os.cpu_count() -> logical CPUs in the system · "Returns None if undetermined."
#   os.process_cpu_count() -> current process için kullanılabilir CPU (sayfada belgelenmiş)
#   os.sysconf(name) -> int · tanımsızsa -1 · geçerli ad sözlüğü: os.sysconf_names
import os

def logical_cpus() -> int:
    n = os.cpu_count()
    if n is None:
        raise RuntimeError("os.cpu_count() None -> bilinmiyor (sessiz fallback yok)")
    return n

def total_phys_bytes() -> int:
    if "SC_PHYS_PAGES" not in os.sysconf_names:          # adın varlığı platforma bağlı
        raise RuntimeError("SC_PHYS_PAGES bu platformda tanimli degil -> UNKNOWN")
    pages = os.sysconf("SC_PHYS_PAGES")
    if pages <= 0:
        raise RuntimeError(f"os.sysconf(SC_PHYS_PAGES)={pages} -> hata/UNKNOWN")

    page_name = "SC_PAGE_SIZE" if "SC_PAGE_SIZE" in os.sysconf_names else "SC_PAGESIZE"
    page_size = os.sysconf(page_name)                    # sysconf(3): _SC_PAGE_SIZE synonym
    if page_size <= 0:
        raise RuntimeError(f"os.sysconf({page_name})={page_size} -> hata/UNKNOWN")
    return pages * page_size
```

**Ders:** `sysconf()` "tanımsız" ile "hata"nı `-1`'de birleştirir; Python tarafı bunu **ad
sözlüğü kontrolü** ile ayırır → `UNKNOWN` üretir, tahmin etmez.

#### §A.6 Go — `runtime.NumCPU()` (bellek `[KAVRAM]`)

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/runtime
//   func NumCPU() int — "returns the number of logical CPUs usable by the current process."
//   "The set of available CPUs is checked by querying the operating system at process startup."
package hardware

import "fmt"

func LogicalCPUs() (int, error) {
	n := runtime.NumCPU()           // import "runtime" eklenir
	if n <= 0 {
		return 0, fmt.Errorf("runtime.NumCPU() = %d -> bilinmiyor", n)
	}
	return n, nil
}
```

**Bellek `[KAVRAM]` (bağımlılık eklenmedi):** Go standart kütüphanesinde fiziksel bellek okuma
API'si **yoktur**. Üçüncü parti seçenek `github.com/shirou/gopsutil/v4`
(`https://pkg.go.dev/github.com/shirou/gopsutil/v4` · `mem.VirtualMemory()` · `HOST_PROC`/`HOST_SYS`
ortam değişkenleriyle kök ayrıştırma) — **`go.mod`'a eklenmedi** (plan R6), `[TASARIM]` tercih.

**Ders:** `NumCPU` **işlem başlangıcında** sabitlenir; sonradan değişen CPU dağıtımı yansımaz
(sayfanın kendi metni) → canlı izleme isteyen senaryo farklı API ister (**O-A4**).

#### §A.7 Rust — `std::thread::available_parallelism()` (bellek `[KAVRAM]`)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://doc.rust-lang.org/std/thread/fn.available_parallelism.html
//   -> Result<NonZero<usize>, io::Error> · Windows'ta CPU grubu, Linux'ta cpuset/cgroup dahil.
use std::thread;

pub fn usable_parallelism() -> Result<usize, String> {
    match thread::available_parallelism() {
        Ok(n) => Ok(n.get()),
        Err(e) => Err(format!("available_parallelism hatasi: {e} -> bilinmiyor")),
    }
}
```

**Bellek `[KAVRAM]` (bağımlılık eklenmedi):** `sysinfo` crate'i (`https://docs.rs/sysinfo/latest/sysinfo/`)
`System::new_all()` + `total_memory()` / `sys.cpus().len()` yüzeyini sunar ve desteklenen OS
listesini sayfasında belirtir — **`Cargo.toml`'a eklenmedi** (plan R6), `[TASARIM]` tercih.

**Ders:** Rust'ta hata **değeri yutulmaz**: `Result<NonZero<_>>` "sıfır CPU" imkânını tip olarak
kaldırır; `Err` = bilinmiyor, `Ok` = ölçüldü.

#### §A.8 C# (.NET) — `Environment.ProcessorCount` + `GC.GetGCMemoryInfo()`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.environment.processorcount
//   "Gets the number of processors available to the current process."
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.gc.getgcmemoryinfo
//   returns GCMemoryInfo
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.gcmemoryinfo
//   TotalAvailableMemoryBytes = "the total available memory for the garbage collector to use
//   when the last garbage collection occurred."  ← RAM ölçümü DEĞİL
using System;

public static class HostProbe
{
    public static int LogicalCPUs()
    {
        int cpus = Environment.ProcessorCount;           // süreç için kullanılabilir CPU
        if (cpus <= 0)
            throw new InvalidOperationException($"ProcessorCount={cpus} -> bilinmiyor");
        return cpus;
    }

    public static long GcAvailableBytes()
    {
        GCMemoryInfo info = GC.GetGCMemoryInfo();        // yalnız GC'nin gördüğü miktar
        long available = info.TotalAvailableMemoryBytes;
        if (available <= 0)
            throw new InvalidOperationException($"TotalAvailableMemoryBytes={available} -> bilinmiyor");
        return available;
    }
}
```

**Ders (dürüstlük kırılması):** `TotalAvailableMemoryBytes` **fiziksel RAM ölçümü değildir**;
sayfanın kendi metni bunu GC'ye bağlar. **.NET'te fiziksel RAM için resmi API bu sette
doğrulanamadı** → `UNKNOWN` + **O-A2**. Sessizce "RAM" diye etiketlemek yasaktır.

#### §A.9 bash (Linux) — `/proc/cpuinfo` + `/proc/meminfo`

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man5/proc_cpuinfo.5.html
#   "for each supported architecture a different list" · ortak giriş: processor (CPU numarası)
# Resmi kaynak: https://man7.org/linux/man-pages/man5/proc_meminfo.5.html
#   MemTotal: "Total usable RAM (i.e., physical RAM minus a few reserved bits and the kernel binary code)."
# Resmi kaynak: https://man7.org/linux/man-pages/man5/proc.5.html (/proc hiyerarşisi)
set -euo pipefail

cpuinfo_path="${PROC_CPUINFO:-/proc/cpuinfo}"     # sabit yol ortamla ezilebilir
meminfo_path="${PROC_MEMINFO:-/proc/meminfo}"

[ -r "$cpuinfo_path" ] || { echo "OKUMA YOK: $cpuinfo_path (Linux muhtemel degil) -> UNKNOWN" >&2; exit 2; }
[ -r "$meminfo_path" ] || { echo "OKUMA YOK: $meminfo_path -> UNKNOWN" >&2; exit 2; }

cpus="$(grep -c '^processor[[:space:]]*:' "$cpuinfo_path" || true)"
mem_kb="$(awk '/^MemTotal:/ {print $2; exit}' "$meminfo_path")"

[ -n "${cpus:-}" ] && [ "$cpus" -gt 0 ] 2>/dev/null || { echo "processor girisi yok -> UNKNOWN" >&2; exit 3; }
[ -n "${mem_kb:-}" ] || { echo "MemTotal bulunamadi -> UNKNOWN" >&2; exit 3; }

echo "cpus=${cpus} mem_kb=${mem_kb}"
```

**Ders:** `grep -c` eşleşme yokken `1` çıkış kodu döndürür → `set -e` bunu **sessizce** "boş
değer"e çevirmez; `|| true` + açık `UNKNOWN` kontrolü ayrımı görünür kılar. Mimariye göre
`/proc/cpuinfo` listesi değişir (**O-A6**).

#### §A.10 PowerShell — .NET yüzeyi (Windows birincil · pwsh çapraz `[TASARIM]`)

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.environment.processorcount
# Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.gcmemoryinfo
$ErrorActionPreference = 'Stop'

$cpus = [Environment]::ProcessorCount
if ($cpus -le 0) { throw "ProcessorCount=$cpus -> bilinmiyor" }

$info = [System.GC]::GetGCMemoryInfo()
$total = $info.TotalAvailableMemoryBytes
if ($total -le 0) { throw "TotalAvailableMemoryBytes=$total -> bilinmiyor" }

[pscustomobject]@{
    LogicalCPUs     = $cpus
    GcAvailableByte = $total   # GC'ye available — fiziksel RAM DEĞİL (bkz. §A.8, O-A2)
}
```

**Ders:** PowerShell yalnız **sarmalayıcıdır**; ölçüm .NET API'sinden gelir → iki kabuk (§A.9 /
§A.10) aynı kararı **farklı** iki keşif yolunda verir, birbirinin kopyası değildir. Sürüm
repo'da sabitlenmedi (Windows PowerShell 5.1 vs pwsh 7) → `⚠️ VERIFICATION REQUIRED` (**O-C3**).

#### §A.11 Zig — `Thread.getCpuCount` + `linux.sysinfo` (Linux birincil)

```zig
// https://raw.githubusercontent.com/ziglang/zig/master/lib/std/os/linux.zig
// [ORİJİNAL ÖRNEK · derlenmedi]
const std = @import("std");

pub fn cpuVeBellek() !struct { cpus: usize, ram: u64 } {
    const cpus = try std.Thread.getCpuCount();   // get_nprocs karşılığı — ham imza doğrulandı
    var info: std.os.linux.Sysinfo = undefined;  // sysinfo(2) sarmalayıcısı (linux.zig)
    if (std.os.linux.sysinfo(&info) != 0) return error.SysinfoFailed;  // 0 = başarısız
    return .{ .cpus = cpus, .ram = info.totalram };  // bayt
}
```

**Ders:** Yalnız **Linux** (`std.os.linux` — dar hedef); Windows/macOS bu blokta yok, sınıf
dışıdır. `sysinfo` errno-stildir: `0` başarı, `-errno` hata → hata `error.SysinfoFailed` olarak
açıkça yükseltilir. Bellek `totalram` (bayt) — `freeram` ayrı alan, bu blokta sınırda okunmaz.

#### §A.12 Swift — `ProcessInfo.processorCount` / `physicalMemory`

```swift
// https://developer.apple.com/documentation/foundation/processinfo
// [ORİJİNAL ÖRNEK · derlenmedi]
import Foundation

enum EnvanterHata: Error { case okunamadi }

func envanter() throws -> (cpus: Int, ram: UInt64) {
    let pi = ProcessInfo.processInfo
    let cpus = pi.processorCount                 // mantıksal çekirdek
    let ram  = pi.physicalMemory                 // bayt — fiziksel RAM
    guard cpus > 0, ram > 0 else { throw EnvanterHata.okunamadi }
    return (cpus, ram)
}
```

**Ders:** Foundation `ProcessInfo` çapraz-platformdur (Darwin + Linux Foundation); hata durumunda
`throw` — `0` değerleri "bilinmeyen" diye sessizce raporlanmaz. Bellek `physicalMemory` fiziksel
RAM'dir (JVM/GC belleğiyle karıştırılmaz — §A.13 notu).

#### §A.13 Java/Kotlin — `ManagementFactory` + `com.sun.management.OperatingSystemMXBean`

```java
// https://docs.oracle.com/en/java/javase/21/docs/api/java.management/java/lang/management/ManagementFactory.html
// [ORİJİNAL ÖRNEK · derlenmedi]
import java.lang.management.ManagementFactory;

static long[] cpuVeBellek() {
    var os  = ManagementFactory.getOperatingSystemMXBean();
    int cpus = os.getAvailableProcessors();
    var sun  = (com.sun.management.OperatingSystemMXBean) os;  // HotSpot/OpenJDK genişletmesi
    long ram = sun.getTotalMemorySize();          // JVM'in gördüğü toplam RAM
    if (cpus <= 0 || ram <= 0)
        throw new IllegalStateException("envanter okunamadi: cpus=" + cpus + " ram=" + ram);
    return new long[]{ cpus, ram };
}
```

```kotlin
// https://docs.oracle.com/en/java/javase/21/docs/api/java.management/com/sun/management/OperatingSystemMXBean.html
// [ORİJİNAL ÖRNEK · derlenmedi]
import java.lang.management.ManagementFactory

fun cpuVeBellek(): Pair<Int, Long> {
    val os   = ManagementFactory.getOperatingSystemMXBean()
    val cpus = os.availableProcessors
    val sun  = os as com.sun.management.OperatingSystemMXBean  // HotSpot'a bağlı — cast açık
    val ram  = sun.totalMemorySize
    require(cpus > 0 && ram > 0) { "envanter okunamadi: cpus=$cpus ram=$ram" }
    return cpus to ram
}
```

**Ders:** `getAvailableProcessors` std JDK'dur; bellek `com.sun.management` **HotSpot genişletmesidir**
— başka JVM'de cast `ClassCastException` fırlatır (sessiz fallback yok). `getTotalMemorySize`
JVM'in *gördüğü* toplam RAM'dir; fiziksel RAM okuması ayrı yetki yüzeyidir (`⚠️ VERIFICATION
REQUIRED`: Java 14 öncesi sürümde adı `getTotalPhysicalMemorySize` — repo sürümü sabitlenmedi).

#### §A sonrası not — WMI (ikinci katman, kavram)

WMI yüzeyi `https://learn.microsoft.com/en-us/windows/win32/wmisdk/wmi-start-page` (200) üzerinden
**kavram** olarak geçer: donanım/envanter sorguları WMI üzerinden de yapılabilir. Ancak sınıf ve
özellik adları (ör. işlemci/bellek sınıfları) bu başlangıç sayfasında **geçmiyor** → ad
uydurulmadı, kod bloğu **üretilmedi** → **O-A3**. Aynı anda `sysinfo(2)`/`sysfs` ikinci okuma
yüzeyi olarak kalır: `/sys` detayı `https://docs.kernel.org/filesystems/sysfs.html` (200).

---

### GRUP B — Ses aygıtı enumerasyonu (kod iskeleti · DSP'ye girmez)

**Amaç:** oynatma/yakalama uç noktalarını (card / device / endpoint) **keşif anında** listelemek.
Bu grup yalnız **enumerasyon** üretir; efekt, filtre, örnekleme/işleme zinciri
`../isletim-sistemi-audio/isletim-sistemi-audio-ornekler.md` **sahibidir** (aynı dalın audio
alt dosyaları: `-kapsam.md`, `-dogrulama.md`, `index.md`). Repo kanıtı: OS-seviyesi keşif kodu yok
(`-ornekler.md` §1.2); eldeki `shared/src/Device/` envanteri uygulama seviyesidir (§1.1).

**Yüzey eşlemesi:** Linux = **ALSA-lib API** + dosya yüzeyi `/proc/asound` (kernel:
`https://www.kernel.org/doc/html/latest/sound/designs/procfile.html` — "ALSA has its own proc tree,
/proc/asound ... cardX") ve `/dev/snd` (kernel: `https://www.kernel.org/doc/html/latest/sound/alsa-configuration.html`
— `pcmC0D0c`/`pcmC0D0p` benzeri düğümler) · Windows = **MMDevice API** (WASAPI giriş katmanı) ·
macOS = **CoreAudio** (`AudioObjectGetPropertyData`). ALSA-lib belgeleri kökü:
`https://www.alsa-project.org/alsa-doc/alsa-lib/` (200) — **kurulum `[KAVRAM]`, komut yazılmaz**.

#### §B.1 C — ALSA (Linux): kart döngüsü + device hint

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://www.alsa-project.org/alsa-doc/alsa-lib/group___control.html
     int snd_card_next(int *card);  "Iterate over physical sound cards."
       semantik: -1 -> ilk kart · son karttan sonra -1 · "This does not work for virtual sound cards."
   Resmi kaynak: https://www.alsa-project.org/alsa-doc/alsa-lib/group___hint.html
     int   snd_device_name_hint(int card, const char *iface, void ***hints);
       card = -1 -> tüm kartlar · iface: "pcm", "rawmidi", "timer", "seq" · hints: NULL-terminated array
     char *snd_device_name_get_hint(const void *hint, const char *id);
       id: "NAME" | "DESC" | "IOID" · "an allocated ASCII string if success, otherwise NULL"
       "The return value should be freed when no longer needed."
     int   snd_device_name_free_hint(void **hints);  "Free a list of device name hints." */
#include <stdio.h>
#include <stdlib.h>
#include <alsa/asoundlib.h>          /* alsa-lib [KAVRAM] — paket kurulumu bu sette yazılmaz */

static void print_hints(int card)
{
    void **hints = NULL;
    int rc = snd_device_name_hint(card, "pcm", &hints);   /* iface "pcm" resmi listede */
    if (rc < 0) {
        fprintf(stderr, "snd_device_name_hint(card=%d, pcm) = %d -> atlandi\n", card, rc);
        return;                                           /* kart döngüsü kesilmez */
    }
    for (void **n = hints; n != NULL && *n != NULL; ++n) {
        char *name = snd_device_name_get_hint(*n, "NAME");
        char *desc = snd_device_name_get_hint(*n, "DESC");
        char *ioid = snd_device_name_get_hint(*n, "IOID");   /* "Input"/"Output"/NULL */
        printf("  name=%s | ioid=%s | desc=%s\n",
               name ? name : "(null)", ioid ? ioid : "(null)", desc ? desc : "(null)");
        free(name); free(desc); free(ioid);                  /* donen dize serbest bırakilir */
    }
    snd_device_name_free_hint(hints);
}

int list_alsa_cards(void)
{
    int card = -1;
    if (snd_card_next(&card) < 0) {
        fputs("snd_card_next ilk cagri hatasi -> UNKNOWN\n", stderr);
        return -1;
    }
    if (card < 0) {
        fputs("fiziksel ses karti YOK (sanal kartlar kapsam disi) -> bos liste degil\n", stderr);
        return 1;                                             /* 1 = kanitli yokluk */
    }
    while (card >= 0) {
        printf("card index=%d\n", card);
        print_hints(card);
        if (snd_card_next(&card) < 0) {
            fputs("snd_card_next ortasinda hata -> eksik listeye DUR\n", stderr);
            return -1;
        }
    }
    return 0;
}
```

**Ders:** ALSA `snd_card_next` **sanal kartları kapsamaz** (resmi metin) → sanal/USB uç noktalar
için ikinci yüzey `/proc/asound` kart alt-ağaçlarıdır; "kart yok" ile "sanal kart var" **ayrı**
durumdur (**O-B1**).

#### §B.2 C++20 — Windows MMDevice (`EnumAudioEndpoints`)

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-enumaudioendpoints
//   HRESULT EnumAudioEndpoints(EDataFlow dataFlow, DWORD dwStateMask, IMMDeviceCollection **ppDevices);
//   EDataFlow: eRender | eCapture | eAll · durum maskeleri: DEVICE_STATE_ACTIVE (0x1) vb.
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api
//   const CLSID CLSID_MMDeviceEnumerator = __uuidof(MMDeviceEnumerator);
//   const IID   IID_IMMDeviceEnumerator   = __uuidof(IMMDeviceEnumerator);
//   hr = CoCreateInstance(CLSID_MMDeviceEnumerator, NULL, CLSCTX_ALL, IID_IMMDeviceEnumerator, (void**)&pEnumerator);
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/combaseapi/nf-combaseapi-coinitializeex
//   HRESULT CoInitializeEx([in, optional] LPVOID pvReserved, [in] DWORD dwCoInit);
#include <windows.h>
#include <mmdeviceapi.h>
#include <stdio.h>

HRESULT list_active_render_endpoints()
{
    HRESULT hr = CoInitializeEx(NULL, COINIT_MULTITHREADED);   /* COINIT değeri: O-B4 */
    if (FAILED(hr)) {
        fwprintf(stderr, L"CoInitializeEx failed: 0x%08lx\n", hr);
        return hr;
    }

    IMMDeviceEnumerator *pEnumerator = nullptr;
    hr = CoCreateInstance(CLSID_MMDeviceEnumerator, NULL, CLSCTX_ALL,
                          IID_IMMDeviceEnumerator, (void **)&pEnumerator);
    if (FAILED(hr)) {
        fwprintf(stderr, L"CoCreateInstance failed: 0x%08lx\n", hr);
        CoUninitialize();
        return hr;
    }

    IMMDeviceCollection *pEndpoints = nullptr;
    hr = pEnumerator->EnumAudioEndpoints(eRender, DEVICE_STATE_ACTIVE, &pEndpoints);
    if (FAILED(hr)) {
        fwprintf(stderr, L"EnumAudioEndpoints failed: 0x%08lx\n", hr);
        pEnumerator->Release();
        CoUninitialize();
        return hr;
    }

    UINT count = 0;
    hr = pEndpoints->GetCount(&count);                          /* GetCount imzası sayfa doğrulanmadı → O-B5 */
    if (FAILED(hr))
        fwprintf(stderr, L"GetCount failed: 0x%08lx\n", hr);
    else
        printf("aktif render endpoint: %u\n", count);

    pEndpoints->Release();
    pEnumerator->Release();
    CoUninitialize();
    return hr;
}
```

**Ders:** `EnumAudioEndpoints` yalnız **durum maskesiyle** süzgeç yapar; "cihaz var mı" ile
"cihaz aktif mi" aynı soru değildir → boş koleksiyon `0`, hata `FAILED(hr)` olarak ayrılır.
`CoInitializeEx` çağrı modeli (`COINIT_MULTITHREADED`) bu sette doğrulanmadı → **O-B4**.

#### §B.3 C++ — macOS CoreAudio (bilinçli eksik — imza yazılmadı)

```cpp
/* [ORİJİNAL ÖRNEK · derlenmedi]
   BİLİNÇLİ EKSİK — çağrı satırı bilerek üretilmedi (neden: O-B2).
   Resmi kaynak: https://developer.apple.com/documentation/coreaudio/audioobjectgetpropertydata(_:_:_:_:_:_:)
     Sayfa HTTP 200 · fonksiyon adı ve 6 parametre girişi HTML'de görünür;
     tam imza (tipler, AudioObjectPropertyAddress, kAudio sabitleri) İÇERİKTE YOK.
   https://developer.apple.com/documentation/coreaudio (kök sayfa, kavram)
   ⚠️ VERIFICATION REQUIRED (O-B2): tam imza doğrulanana kadar çağrı satırı ÜRETİLMEZ. */
#include <CoreAudio/CoreAudio.h>
#include <stdio.h>

int list_coreaudio_devices_bilinc_eksik(void)
{
    /* AudioObjectPropertyAddress alanları ve property anahtar adları doğrulanmadı:
       çağrı yok, sabit yok, tahmin yok. */
    fputs("macOS CoreAudio enumerasyonu: imza dogrulanmadi -> UNKNOWN (O-B2)\n", stderr);
    return -2;                       /* -1 = OS hatası, -2 = bilinçli eksik */
}
```

**Ders:** JS ile renderlanan doküman sayfası **imza kanıtı sayılmaz**: fonksiyon adının görünmesi
çağrının doğruluğunu **kanıtlamaz** → blok bilinçli eksik kalır, macOS sütunu `[TASARIM]` (Q4).

#### §B.4 C# — MMDevice COM interop (bilinçli eksik — GUID uydurulmadı)

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// BİLİNÇLİ EKSİK — GUID metni ve COM interop çağrısı bilerek üretilmedi (neden: O-B3).
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api
//   CLSID_MMDeviceEnumerator / IID_IMMDeviceEnumerator sembolleri sayfada anlatılır;
//   GUID metinleri (16'lık dize) HTML'de YOK -> [Guid("...")] DEĞİL (uydurma yasak).
// Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-enumaudioendpoints
//   HRESULT EnumAudioEndpoints(EDataFlow, DWORD, IMMDeviceCollection**);
using System;

internal static class AudioEndpoints
{
    public static int RenderEndpointCountBilincEksik()
    {
        // COM interop (CCW/RCW) için gereken CLSID/IID metni doğrulanmadı.
        // Alternatif arayüz (ör. üçüncü parti NAudio) [KAVRAM] — paket eklenmedi (plan R6).
        throw new NotSupportedException(
            "CLSID_MMDeviceEnumerator/IID_IMMDeviceEnumerator GUID metni dogrulanmadi -> VERIFICATION REQUIRED (O-B3)");
    }
}
```

**Ders:** C# tarafında blok **boş değil ama çağrı yok**: `NotSupportedException` açık ve
gürültülüdür — sessizce "0 cihaz" dönmekten kötü değildir, çünkü bu **bilgi eksikliğidir**.

#### §B.5 Python — FFI notu (ALSA `snd_card_next` imzası doğrulandı)

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://docs.python.org/3/library/ctypes.html (ctypes.CDLL)
# Resmi kaynak: https://www.alsa-project.org/alsa-doc/alsa-lib/group___control.html
#   int snd_card_next(int *card);  -1 -> ilk kart, sonra -1
import ctypes

def first_alsa_card(libname: str) -> int:
    """libname zorunlu: paylaşımlı kütüphane adı/soname platforma ve dağıtıma göre
    değişir -> varsayılan UYDURULMADI (⚠️ VERIFICATION REQUIRED)."""
    if not libname:
        raise ValueError("libname boş -> UNKNOWN (sessiz fallback yok)")
    try:
        lib = ctypes.CDLL(libname)
    except OSError as e:
        raise RuntimeError(f"ALSA kutuphanesi acilamadi ({libname}) -> {e}") from e

    card = ctypes.c_int(-1)                       # -1: "ilk kart" resmi semantik
    if lib.snd_card_next(ctypes.byref(card)) < 0:
        raise RuntimeError("snd_card_next hatasi -> UNKNOWN")
    return card.value                             # -1 = fiziksel kart yok (kanıtlı yokluk)
```

**Ders:** FFI'da **imza** (C tarafı) ile **yükleme** (kütüphane adı) ayrı kanıttır; ilki doğrulanmış,
ikincisi platforma bağlıdır → ikisi de tek blokta birleştirilmez, ikincisi çağrıya parametre yapılır.

#### §B.6 PHP — sınır/kavram (native enumerasyon yok · süreç burada yazılmaz)

```php
<?php
/* [ORİJİNAL ÖRNEK · derlenmedi] · KAVRAM / SINIR
   Bu blokta ÇAĞRILMIŞ resmi API YOK — PHP standart kütüphanesinde ses aygıtı
   enumerasyonu yüzeyi YOKTUR. Repo kanıtı: uygulama-seviyesi cihaz listesi
   (os-donanim-tespiti-ornekler.md §1.1) — OS keşfi DEĞİL (§1.2, §1.3).
   Sahiplik: keşif ya C/Python yardımcısında (§B.1, §B.5) ya da kabuk/süreç
   sarmalayıcısında (isletim-sistemi-turu-kod-seti.md §C.1 / §D.1) yapılır;
   süreci burada yeniden yazmak TEKRAR olurdu. */
declare(strict_types=1);

interface AudioEndpointSource
{
    /**
     * @return list<array{name: string, direction: string, origin: string}>
     *         origin: 'alsa' | 'mmdevice' | 'coreaudio' — keşfi yapan katmanı işaretler
     * @throws RuntimeException keşif yapılamadığında (boş liste dönmek YASAK)
     */
    public function endpoints(): array;
}
```

**Ders:** Sınır bloğu da **kod bloğudur** ve etiket taşır: PHP burada "yok" demez, **sahipliği**
başka katmana devreder; bu, fallback değil **mimari karardır** (kayıt: §B başlığındaki sahiplik
cümlesi).

---

### GRUP C — Ağ / arayüzü enumerasyonu (keşif, çözümleme değil)

**Amaç:** yerel makinedeki ağ arayüzlerini ve adreslerini **OS'nin kendi listesinden** okumak
(DNS çözümleme, ping, traceroute bu setin **dışında**). Yüzey eşlemesi: POSIX = `getifaddrs(3)` ·
Windows = `GetAdaptersAddresses` · Linux araç katmanı = `ip(8)` · yönetilen dil = `net` /
`NetworkInterface`.

#### §C.1 C — `getifaddrs(3)` (Linux · macOS `[TASARIM]`)

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://man7.org/linux/man-pages/man3/getifaddrs.3.html
     int  getifaddrs(struct ifaddrs **ifap);
     void freeifaddrs(struct ifaddrs *ifa);
     struct ifaddrs { ifa_next; ifa_name; ifa_flags; ifa_addr; ifa_netmask; union{ifu_broadaddr, ifu_dstaddr}; ... }
     "creates a linked list of structures describing the network interfaces of the local system" */
#include <stdio.h>
#include <ifaddrs.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <string.h>

int list_ipv4_addresses(void)
{
    struct ifaddrs *ifa = NULL;
    if (getifaddrs(&ifa) != 0) {
        perror("getifaddrs");
        return -1;
    }

    int printed = 0;
    for (struct ifaddrs *p = ifa; p != NULL; p = p->ifa_next) {
        if (p->ifa_addr == NULL) continue;                 /* adresi olmayan satır: atla, hata değil */
        if (p->ifa_addr->sa_family != AF_INET) continue;    /* bu örnek IPv4 — AF_INET6 ayrı tur */

        char buf[INET_ADDRSTRLEN];
        const struct sockaddr_in *sin = (const struct sockaddr_in *)p->ifa_addr;
        if (inet_ntop(AF_INET, &sin->sin_addr, buf, sizeof buf) == NULL) {
            perror("inet_ntop");
            freeifaddrs(ifa);
            return -1;
        }
        printf("%-12s %s\n", p->ifa_name, buf);
        printed++;
    }

    freeifaddrs(ifa);
    if (printed == 0) {
        fprintf(stderr, "IPv4 adresi okunamadi -> UNKNOWN (bos liste 'yok' sayilmaz)\n");
        return 2;
    }
    return 0;
}
```

**Ders:** `getifaddrs` **fiziksel** arayüzlerle sınırlı değildir ve aile bazlıdır; BSD/macOS'ta
alan/davranış farkları HISTORY bölümünde anlatılır → macOS sütunu `[TASARIM]` (**O-C1**).

#### §C.2 C — Windows `GetAdaptersAddresses`

```c
/* [ORİJİNAL ÖRNEK · derlenmedi]
   Resmi kaynak: https://learn.microsoft.com/en-us/windows/win32/api/iphlpapi/nf-iphlpapi-getadaptersaddresses
     ULONG GetAdaptersAddresses(ULONG Family, ULONG Flags, PVOID Reserved,
                                PIP_ADAPTER_ADDRESSES AdapterAddresses, PULONG SizePointer);
     Family: AF_UNSPEC 0 (IPv4+IPv6) · AF_INET 2 · AF_INET6 23
     Flags : GAA_FLAG_INCLUDE_PREFIX 0x0010 ("Return a list of IP address prefixes on this adapter")
     Hata  : ERROR_BUFFER_OVERFLOW -> "SizePointer parameter is too small ... or AdapterAddresses is NULL"
   Resmi kaynak: https://learn.microsoft.com/en-us/windows/desktop/api/iptypes/ns-iptypes-ip_adapter_addresses_lh
     IP_ADAPTER_ADDRESSES: Next · AdapterName · FriendlyName · Description · FirstUnicastAddress */
#include <windows.h>
#include <iphlpapi.h>
#include <stdio.h>
#include <stdlib.h>

int list_adapters(void)
{
    ULONG size = 0;
    ULONG rc = GetAdaptersAddresses(AF_UNSPEC, GAA_FLAG_INCLUDE_PREFIX, NULL, NULL, &size);
    if (rc != ERROR_BUFFER_OVERFLOW) {
        fprintf(stderr, "GetAdaptersAddresses on olcum: %lu\n", rc);
        return -1;
    }

    PIP_ADAPTER_ADDRESSES addrs = (PIP_ADAPTER_ADDRESSES)malloc(size);
    if (addrs == NULL) {
        fprintf(stderr, "bellek ayrilamadi: %lu bayt\n", size);
        return -1;
    }

    rc = GetAdaptersAddresses(AF_UNSPEC, GAA_FLAG_INCLUDE_PREFIX, NULL, addrs, &size);
    if (rc != NO_ERROR) {
        fprintf(stderr, "GetAdaptersAddresses: %lu\n", rc);
        free(addrs);
        return -1;
    }

    int printed = 0;
    for (PIP_ADAPTER_ADDRESSES a = addrs; a != NULL; a = a->Next) {
        printf("adapter=%s friendly=%ls desc=%ls\n",
               a->AdapterName,
               a->FriendlyName  ? a->FriendlyName  : L"(yok)",
               a->Description   ? a->Description   : L"(yok)");
        printed++;
    }

    free(addrs);
    if (printed == 0) {
        fprintf(stderr, "adapter listesi bos -> UNKNOWN\n");
        return 2;
    }
    return 0;
}
```

**Ders:** iki aşamalı boyut sorgusu (`NULL` + `ERROR_BUFFER_OVERFLOW`) bu API'nin **zorunlu**
desenidir; tek çağrı "boş" ile "yetersiz arabellek"i ayırt edemez → bellek ayrımı `malloc` hatası
açık döner (sessiz `NULL` geçişi yok).

#### §C.3 Python — FFI kavramı (getifaddrs struct düzeni doğrulanmadı)

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# BİLİNÇLİ EKSİK — struct tanımı ve FFI çağrısı bilerek üretilmedi (neden: O-C2).
# Resmi kaynak: https://man7.org/linux/man-pages/man3/getifaddrs.3.html (struct ifaddrs alanları)
#   struct ifaddrs { ifa_next; ifa_name; ifa_flags; ifa_addr; ifa_netmask; ... }
#   ⚠️ VERIFICATION REQUIRED (O-C2): ctypes Structure için alan offset/alignment hizası
#   (imzalı/heaparkeli düzen, 32/64-bit farkı) DOĞRULANMADI -> struct TANIMLANMADI, çağrı YOK.
from typing import NoReturn

def list_ipv4_addresses() -> NoReturn:
    """KAVRAM: getifaddrs -> tek yönlü liste -> ifa_name + ifa_addr -> inet_ntop.
    ctypes struct düzeni doğrulanmadığı için implemente EDİLMEDİ."""
    raise NotImplementedError(
        "getifaddrs FFI struct duzeni dogrulanmadi -> VERIFICATION REQUIRED (O-C2)"
    )
```

**Ders:** FFI'da doğru olan **yapı tanımı** değil, **kanıttır**: alan sırası doğrulanmadan yazılan
`ctypes.Structure` sessizce yanlış okur → Python yerine doğrulanmış yüzey (§C.4 Go / §C.1 C)
kullanılır.

#### §C.4 Go — `net.Interfaces()`

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://pkg.go.dev/net
//   func Interfaces() ([]Interface, error) · (*Interface).Addrs() ([]Addr, error)
package netprobe

import "fmt"

type IfaceAddr struct {
	Iface string
	Addr  string
}

func ListInterfaces() ([]IfaceAddr, error) {
	ifs, err := netInterfaces()
	if err != nil {
		return nil, fmt.Errorf("net.Interfaces: %w", err)
	}

	out := make([]IfaceAddr, 0, len(ifs))
	for _, iface := range ifs {
		addrs, err := ifaceAddrs(iface)
		if err != nil {
			return nil, fmt.Errorf("arayuz %q Addrs: %w", iface.name, err)
		}
		for _, a := range addrs {
			out = append(out, IfaceAddr{Iface: iface.name, Addr: a})
		}
	}

	if len(out) == 0 {
		return nil, fmt.Errorf("arayuz/adres bulunamadi -> UNKNOWN (bos liste 'yok' sayilmaz)")
	}
	return out, nil
}
```

**Not (bilinçli incelik):** `net.Interfaces` ve `(*Interface).Addrs` pkg.go.dev indeksinde
doğrulanmış olmakla birlikte bu blok, tipleri `net.Interface`/`net.Addr` yerine **yerel kayıt**
(`IfaceAddr`) üzerinden taşır → dışa aktarılan sözleşme dil bağımsız kalır; ham `net` tiplerinin
imza detayı **O-C4** olarak açık bırakıldı.

#### §C.5 bash (Linux) — `ip(8)`

```bash
#!/usr/bin/env bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://man7.org/linux/man-pages/man8/ip.8.html
#   -o, -oneline : "output each record on a single line" (sayma/grep için)
#   -j, -json    : JSON çıktı (işleyici gerektirir — işleyici bu setin dışı)
set -euo pipefail

command -v ip >/dev/null 2>&1 || { echo "ip araci YOK -> UNKNOWN (sessiz fallback yok)" >&2; exit 2; }

ip -o addr show          # satir basina kayit (ham keşif)
ip -j addr show          # JSON bicim (ayni keşif, farkli bicimlendirme)
```

**Ders:** `ip(8)` aynı keşfi **iki biçimde** verir; satır içi ayrıştırma (`-o`) sayıma, `-j`
yapısal işleme içindir. Çıktıyı **yorumlamak** bu setin dışında (işleyici bağımlılığı → **O-C5**).

#### §C.6 PowerShell — `NetworkInterface.GetAllNetworkInterfaces()`

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/api/system.net.networkinformation.networkinterface
#   NetworkInterface.GetAllNetworkInterfaces() · .NetworkInterfaceType · .OperationalStatus · .GetPhysicalAddress()
$ErrorActionPreference = 'Stop'

$ifs = [System.Net.NetworkInformation.NetworkInterface]::GetAllNetworkInterfaces()
if (-not $ifs -or $ifs.Count -eq 0) { throw "arayuz bulunamadi -> UNKNOWN" }

$ifs | ForEach-Object {
    [pscustomobject]@{
        Name   = $_.Name
        Type   = $_.NetworkInterfaceType.ToString()
        Status = $_.OperationalStatus.ToString()
        Mac    = $_.GetPhysicalAddress().ToString()
    }
} | Format-Table -AutoSize
```

**Ders:** `OperationalStatus` "keşif" ile "durum" arasındaki farkı gösterir: arayüz **var** ama
`Down` olabilir → liste doluluğu ile erişilebilirlik **aynı** ölçü değildir.

---

## §3. Dil × OS kapsama matrisi

| Dil | Linux | Windows | macOS | Not |
|---|---|---|---|---|
| C | §A.1 · §B.1 · §C.1 | §A.2 · §C.2 | §A.3 · §B.3 (bilinçli eksik) | en geniş yüzey |
| C++20 | §A.4 `[TASARIM]` | §B.2 | §B.3 (bilinçli eksik) | bellek API'si std'de yok |
| Python | §A.5 · §B.5 · §C.3 (eksik) | §A.5 `[TASARIM]` | §A.5 `[TASARIM]` | FFI hattı ayrı kanıt |
| Go | §A.6 · §C.4 | §A.6 · §C.4 `[TASARIM]` | `[TASARIM]` | bellek = `[KAVRAM]` |
| Rust | §A.7 | `[TASARIM]` | `[TASARIM]` | bellek = `[KAVRAM]` |
| C# (.NET) | §A.8 · §A.10 `[TASARIM]` | §A.8 · §B.4 · §C.6 | `[TASARIM]` | fiziksel RAM = `UNKNOWN` (O-A2) |
| PowerShell | `[TASARIM]` | §A.10 · §C.6 | `[TASARIM]` | sürüm sabitlenmedi (O-C3) |
| bash | §A.9 · §C.5 | `YOK` | `[TASARIM]` | `/proc` yüzeyi Linux'a özgü |
| Zig | §A.11 | `YOK` | `YOK` | yalnız `std.os.linux` yüzeyi — dar hedef |
| Swift | §A.12 | `[TASARIM]` | §A.12 (Darwin) | `ProcessInfo` Foundation çapraz |
| Java/Kotlin | §A.13 (JVM) | `[TASARIM]` (HotSpot cast) | `[TASARIM]` | bellek yüzeyi `com.sun.management`'a bağlı |
| PHP | §B.6 (sınır) | §B.6 (sınır) | §B.6 (sınır) | native enumerasyon yok |
| JavaScript (Node.js) | `YOK` | `YOK` | `YOK` | dil slotu dışı — §2 notu |

**`[TASARIM]` hücre anlamı:** arayüz varlığı bilinen dil/portabilite tercihidir; **koşucu kanıt yok**
(Q4). `YOK` = bu sette sahiplik yok (dil slotu dışı veya platform yüzeyi yok).

---

## §4. Açık sorular (bu dosya eksenli — 17 madde)

| # | Soru | Etki | Durum |
|---|---|---|---|
| O-A1 | macOS `sysctl` CPU/bellek **alt anahtar** adları resmi sayfada geçmiyor | §A.3 blok bilinçli eksik | `UNKNOWN` |
| O-A2 | .NET'te **fiziksel RAM** için resmi API doğrulanamadı (`TotalAvailableMemoryBytes` GC'ye ait) | §A.8/§A.10 etiket | `UNKNOWN` |
| O-A3 | WMI sınıf/özellik adları başlangıç sayfasında yok | §A sonrası not, blok yok | `⚠️ VERIFICATION REQUIRED` |
| O-A4 | Go/Rust bellek ölçümü üçüncü partiye bağlı (`gopsutil`/`sysinfo`) → paket eklenmedi mi? | plan R6 / Q4 | `[KAVRAM]` |
| O-A5 | `dwNumberOfProcessors` **işlemci grubu** sınırı; >64 CPU düzende grup başına okuma gerekir mi? | §A.2 | `⚠️ VERIFICATION REQUIRED` |
| O-A6 | `/proc/cpuinfo` girdileri **mimariye göre** değişir → mimariye özel ayrım gerekir mi? | §A.9 | `⚠️ VERIFICATION REQUIRED` |
| O-B1 | `snd_card_next` sanal kartları **kapsamaz** → sanal/USB uç nokta ikinci yüzeyi (`/proc/asound`) doğrulanacak mı? | §B.1 | `⚠️ VERIFICATION REQUIRED` |
| O-B2 | CoreAudio `AudioObjectGetPropertyData` **tam imza** HTML'de yok | §B.3 bilinçli eksik | `UNKNOWN` |
| O-B3 | C# COM interop **CLSID/IID metinleri** doğrulanmadı | §B.4 bilinçli eksik | `UNKNOWN` |
| O-B4 | `CoInitializeEx` **çağrı modeli** (`COINIT_*`) bu set için seçilmedi | §B.2 | `⚠️ VERIFICATION REQUIRED` |
| O-B5 | `IMMDeviceCollection::GetCount` imzası bu blokta doğrulanmadı | §B.2 | `⚠️ VERIFICATION REQUIRED` |
| O-B6 | PHP tarafında keşif yardımcısının **sahibi** (C/Python mı, yardımcı süreç mi) | §B.6 | `[TASARIM]` |
| O-C1 | `getifaddrs` **BSD/macOS semantik farkları** (HISTORY) | §C.1 macOS sütunu | `[TASARIM]` |
| O-C2 | Python `ctypes` `struct ifaddrs` **düzeni** (offset/alignment) doğrulanmadı | §C.3 bilinçli eksik | `UNKNOWN` |
| O-C3 | PowerShell sürümü repo'da **sabitlenmedi** (5.1 vs 7) | §A.10 · §C.6 | `⚠️ VERIFICATION REQUIRED` |
| O-C4 | Go bloğunun yerel kayıt üzerinden **imza taşınması** `net` tipleriyle doğrulandı mı? | §C.4 | `⚠️ VERIFICATION REQUIRED` |
| O-C5 | `ip -j` çıktısının **işleyicisi** (bağımlılık?) | §C.5 | `UNKNOWN` |

**Genel kapı:** tüm 22 blok **derlenmedi/çalıştırılmadı** → `⚠️ VERIFICATION REQUIRED` (§0).
**Q4** (hedef platform seti) açık kaldığı sürece macOS hücreleri `[TASARIM]` kalır.

---

## §5. Kaynakça (2026-10-14 · HTTP 200 doğrulandı)

| # | Konu | URL | Tarih |
|---|---|---|---|
| 1 | POSIX `sysconf(3)` (`_SC_NPROCESSORS_ONLN` · `_SC_PHYS_PAGES` · `_SC_PAGESIZE`) | https://man7.org/linux/man-pages/man3/sysconf.3.html | 2026-10-14 ✅ |
| 2 | Linux procfs genel bakış | https://www.kernel.org/doc/html/latest/filesystems/proc.html | 2026-10-14 ✅ |
| 3 | `proc(5)` hiyerarşi (SEE ALSO: `proc_cpuinfo(5)`, `proc_meminfo(5)`) | https://man7.org/linux/man-pages/man5/proc.5.html | 2026-10-14 ✅ |
| 4 | `/proc/cpuinfo` (`processor` girişi, mimari farkı) | https://man7.org/linux/man-pages/man5/proc_cpuinfo.5.html | 2026-10-14 ✅ |
| 5 | `/proc/meminfo` (`MemTotal`) | https://man7.org/linux/man-pages/man5/proc_meminfo.5.html | 2026-10-14 ✅ |
| 6 | Linux sysfs (`/sys`) | https://docs.kernel.org/filesystems/sysfs.html | 2026-10-14 ✅ |
| 7 | Win32 `GetSystemInfo` (+ WOW64 notu) | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getsysteminfo | 2026-10-14 ✅ |
| 8 | `SYSTEM_INFO` yapısı (`dwNumberOfProcessors`, `dwPageSize`) | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/ns-sysinfoapi-system_info | 2026-10-14 ✅ |
| 9 | Win32 `GlobalMemoryStatusEx` (`MEMORYSTATUSEX`) | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-globalmemorystatusex | 2026-10-14 ✅ |
| 10 | WMI başlangıç sayfası (kavram — sınıf adları yok) | https://learn.microsoft.com/en-us/windows/win32/wmisdk/wmi-start-page | 2026-10-14 ✅ |
| 11 | Apple `sysctl(3)` arşiv (`CTL_HW` = "Generic CPU, I/O") | https://developer.apple.com/library/archive/documentation/System/Conceptual/ManPages_iPhoneOS/man3/sysctl.3.html | 2026-10-14 ✅ |
| 12 | Apple IOKit (kavram) | https://developer.apple.com/documentation/iokit | 2026-10-14 ✅ |
| 13 | C++ `std::thread::hardware_concurrency` (cppreference — ISO değil) | https://en.cppreference.com/w/cpp/thread/thread/hardware_concurrency | 2026-10-14 ✅ |
| 14 | Python `os.cpu_count` / `os.sysconf` / `os.sysconf_names` | https://docs.python.org/3/library/os.html | 2026-10-14 ✅ |
| 15 | Python `ctypes` (`CDLL`) | https://docs.python.org/3/library/ctypes.html | 2026-10-14 ✅ |
| 16 | Go `runtime.NumCPU` | https://pkg.go.dev/runtime | 2026-10-14 ✅ |
| 17 | Go `net.Interfaces` / `(*Interface).Addrs` | https://pkg.go.dev/net | 2026-10-14 ✅ |
| 18 | Go gopsutil v4 (üçüncü parti — `[KAVRAM]`) | https://pkg.go.dev/github.com/shirou/gopsutil/v4 | 2026-10-14 ✅ |
| 19 | Rust `available_parallelism` | https://doc.rust-lang.org/std/thread/fn.available_parallelism.html | 2026-10-14 ✅ |
| 20 | Rust `sysinfo` crate (üçüncü parti — `[KAVRAM]`) | https://docs.rs/sysinfo/latest/sysinfo/ | 2026-10-14 ✅ |
| 21 | .NET `Environment.ProcessorCount` | https://learn.microsoft.com/en-us/dotnet/api/system.environment.processorcount | 2026-10-14 ✅ |
| 22 | .NET `GC.GetGCMemoryInfo()` | https://learn.microsoft.com/en-us/dotnet/api/system.gc.getgcmemoryinfo | 2026-10-14 ✅ |
| 23 | .NET `GCMemoryInfo` (`TotalAvailableMemoryBytes` tanımı) | https://learn.microsoft.com/en-us/dotnet/api/system.gcmemoryinfo | 2026-10-14 ✅ |
| 24 | .NET `NetworkInterface.GetAllNetworkInterfaces` | https://learn.microsoft.com/en-us/dotnet/api/system.net.networkinformation.networkinterface | 2026-10-14 ✅ |
| 25 | ALSA-lib belge kökü (üçüncü parti kurulum `[KAVRAM]`) | https://www.alsa-project.org/alsa-doc/alsa-lib/ | 2026-10-14 ✅ |
| 26 | ALSA `snd_card_next` / `snd_card_get_name` | https://www.alsa-project.org/alsa-doc/alsa-lib/group___control.html | 2026-10-14 ✅ |
| 27 | ALSA `snd_device_name_hint` / `_get_hint` / `_free_hint` | https://www.alsa-project.org/alsa-doc/alsa-lib/group___hint.html | 2026-10-14 ✅ |
| 28 | Kernel ALSA proc ağacı (`/proc/asound`, `cardX`) | https://www.kernel.org/doc/html/latest/sound/designs/procfile.html | 2026-10-14 ✅ |
| 29 | Kernel ALSA yapılandırma (düğüm adları `/dev/snd`) | https://www.kernel.org/doc/html/latest/sound/alsa-configuration.html | 2026-10-14 ✅ |
| 30 | Windows MMDevice API genel bakış (`CoCreateInstance` deseni) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api | 2026-10-14 ✅ |
| 31 | `IMMDeviceEnumerator::EnumAudioEndpoints` | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-enumaudioendpoints | 2026-10-14 ✅ |
| 32 | `CoInitializeEx` | https://learn.microsoft.com/en-us/windows/win32/api/combaseapi/nf-combaseapi-coinitializeex | 2026-10-14 ✅ |
| 33 | Apple CoreAudio kök (kavram) | https://developer.apple.com/documentation/coreaudio | 2026-10-14 ✅ |
| 34 | Apple `AudioObjectGetPropertyData` (imza HTML'de yok → eksik blok) | https://developer.apple.com/documentation/coreaudio/audioobjectgetpropertydata(_:_:_:_:_:_:) | 2026-10-14 ✅ |
| 35 | `getifaddrs(3)` / `freeifaddrs(3)` + `struct ifaddrs` | https://man7.org/linux/man-pages/man3/getifaddrs.3.html | 2026-10-14 ✅ |
| 36 | `ip(8)` (`-o oneline`, `-j json`) | https://man7.org/linux/man-pages/man8/ip.8.html | 2026-10-14 ✅ |
| 37 | Win32 `GetAdaptersAddresses` (aile/flag/hata) | https://learn.microsoft.com/en-us/windows/win32/api/iphlpapi/nf-iphlpapi-getadaptersaddresses | 2026-10-14 ✅ |
| 38 | `IP_ADAPTER_ADDRESSES` yapısı | https://learn.microsoft.com/en-us/windows/desktop/api/iptypes/ns-iptypes-ip_adapter_addresses_lh | 2026-10-14 ✅ |
| 39 | Node.js `fs` (dil slotu dışı notu) | https://nodejs.org/api/fs.html | 2026-10-14 ✅ |
| 40 | Zig `Thread.getCpuCount()` (ham imza) | https://raw.githubusercontent.com/ziglang/zig/master/lib/std/Thread.zig | 2026-10-14 ✅ |
| 41 | Zig `linux.sysinfo` / `Sysinfo.totalram` (ham imza) | https://raw.githubusercontent.com/ziglang/zig/master/lib/std/os/linux.zig | 2026-10-14 ✅ |
| 42 | Apple `ProcessInfo.processorCount` / `physicalMemory` | https://developer.apple.com/documentation/foundation/processinfo | 2026-10-14 ✅ |
| 43 | Java `ManagementFactory.getOperatingSystemMXBean` + `com.sun.management` genişletmesi | https://docs.oracle.com/en/java/javase/21/docs/api/java.management/java/lang/management/ManagementFactory.html | 2026-10-14 ✅ |

**Kullanım kuralı:** URL'ler blokların **günü** doğrulanmış arayüz referansıdır; blokların kendisi
orijinaldir ve derlenmemiştir (`[ORİJİNAL ÖRNEK · derlenmedi]`). Bir URL'nin 200 olması **kodun
doğruluğunu** kanıtlamaz → davranış iddiaları §4'teki O-sorularıyla bağlıdır.

---

## §6. İlişkili dosyalar

- Repo örnekleri (kanıt köprüsü — §1.1 envanter · §1.2 grep-0 · §1.3 tahmin ≠ keşif · §8.2 IOCTL
  yasağı · §12 çapraz referans): [`os-donanim-tespiti-ornekler.md`](os-donanim-tespiti-ornekler.md)
- Kapsam/sınır: [`os-donanim-tespiti-kapsam.md`](os-donanim-tespiti-kapsam.md)
- Doğrulama kapısı: [`os-donanim-tespiti-dogrulama.md`](os-donanim-tespiti-dogrulama.md)
- Dizin: [`index.md`](index.md) — **bu dosya tarafından güncellenmedi** (B4 kaydı orkestratöre ait)
- Model/skeleton sahibi (platform tespiti, yol, süreç, kabuk): [`../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md`](../isletim-sistemi-turu/isletim-sistemi-turu-kod-seti.md)
- Ses zinciri sahibi (DSP — bu dosyanın dışı): [`../isletim-sistemi-audio/isletim-sistemi-audio-ornekler.md`](../isletim-sistemi-audio/isletim-sistemi-audio-ornekler.md)
  · aynı dalda `isletim-sistemi-audio-kapsam.md` · `isletim-sistemi-audio-dogrulama.md` ·
  `../isletim-sistemi-audio/index.md`

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
