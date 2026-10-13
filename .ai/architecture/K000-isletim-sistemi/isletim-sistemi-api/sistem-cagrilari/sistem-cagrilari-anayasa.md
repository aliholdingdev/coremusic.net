---
node_id: K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari
evidence:
  - https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/unistd.h.html
  - https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/errno.h.html
  - https://www.gnu.org/software/libc/manual/html_node/Program-Components.html
validation: "2026-10-13 · 13 resmi URL HTTP 200 · repo shell_exec/proc_open satır doğrulandı"
open_questions:
  - "Q-S1 Hedef mimari syscall numarası aralığı → ⚠️ VERIFICATION REQUIRED"
  - "Q-S2 Uygulama sargı katmanı tercihi → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# sistem-cagrilari — Anayasa

## §0 ELI15

Bir program "bu dosyayı aç" dediğinde, bu istek işletim sistemi çekirdeğine gider.
İşletim sistemi her isteğe bir **numara** atar; program o numarayı ve argümanları
önceden anlaşılmış bir **kayıt (register) düzenine** koyar ve çekirdeğe geçer.
Çekirdek işi yapar, sonucu (başarı ya da hata kodu) geri gönderir. Bu döngünün
adı **sistem çağrısı (system call)**. Uygulama bunu nadiren doğrudan yapar —
`printf`, `malloc`, `fopen` gibi kütüphane fonksiyonları altta bu geçişi saklar.

## §1 Kimlik

| Alan | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari/sistem-cagrilari-anayasa |
| node_type | module |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/sistem-cagrilari |
| Kademe | 4 |
| group | mimari |
| Durum | draft |
| Kapsam | syscall yüzeyi · kullanıcı↔çekirdek geçişi · dönüş değeri/hata · numaralandırma · uyumluluk |

## §2 Kavram modeli

### §2.1 Tanımlar

| Terim | Tanım |
|---|---|
| System call (syscall) | Uygulama ile çekirdek arasındaki temel arayüz — bir isteğin numaralı, kayıtlarla taşınan biçimi |
| Syscall number | Her işleve atanan sabit tamsayı; mimariye göre değişir |
| Register convention | Argümanların ve dönüş değerinin CPU kayıtlarına yerleştirilme düzeni |
| Wrapper (sargı) | Kütüphanenin syscall'ı saran, tip güvenli fonksiyonu (ör. glibc `open()` → `open(2)`) |
| errno | Çağrı başarısız olduğunda ayrı bir global değişkene yazılan hata kodu |
| User space / Kernel space | Bellek korumalı iki ayrı yürütme alanı; geçiş yalnızca kapıdan yapılır |
| VDSO | Çekirdeğin kullanıcı uzaya haritaladığı, bazı çağrıları hızlandıran sanal kütüphane |
| Trap / exception | Donanımın kontrollü çekirdek geçişi tetiklediği mekanizma |
| ABI (Application Binary Interface) | Çağrı-no, register ve stack düzeni sözleşmesi |
| Restartable sequence | Çekirdeğin kullanıcı uzayda kesintisiz yürütmesine izin verdiği kısa bölge |

### §2.2 Katman modeli

```
Uygulama kodu (C/C++/PHP-runtime)
        │  fonksiyon çağrısı (printf/fopen/malloc)
        ▼
Kütüphane sargısı (libc / libSystem / UCRT)   ← CoreMusic'in dokunabildiği EN SON katman
        │  syscall numarası + register yükleme
        ▼
Geçiş kapısı (syscall / sysenter / int 0x80 / svc)
        │  kullanıcı → çekirdek
        ▼
Çekirdek dispatcher (sys_call_table)          ← KAPSAM DIŞI (isletim-sistemi-kernel)
        │  doğrulama + yetki + işle
        ▼
Dönüş: rax/retval + errno (başarısızlıkta)
```

> Bu dal yalnızca **kullanıcı uzaydan çekirdek kapısına kadar** olan bölümü
> belgeler. Dispatcher ve ötesi `isletim-sistemi-kernel` alanıdır.

### §2.3 Geçiş kapısı türleri

| Kapı | Kullanıldığı yer | Not |
|---|---|---|
| `syscall` (x86_64) | 64-bit Linux | Hızlı modern kapı; `syscall`/`sysret` talimatları |
| `sysenter`/`sysexit` | 32-bit x86 | Intel'in hızlı geçişi |
| `int 0x80` | Eski 32-bit | Yazılım kesmesi; yavaş, uyumluluk için |
| `svc #0` | ARM/ARM64 | Supervisor call |
| `sc` | PowerPC | System call talimatı |

Numara kapısı seçimi ABI'ın parçasıdır; uygulama bunu bilmez, sargı kütüphanesi
doğru talimatı seçer.

### §2.4 Numaralandırma modeli

| Mimari | Çağrı no. kaydı | Arg-0 | Arg-1 | Arg-2 | Arg-3 | Arg-4 | Arg-5 | Dönüş |
|---|---|---|---|---|---|---|---|---|
| x86_64 (Linux) | rax | rdi | rsi | rdx | r10 | r8 | r9 | rax |
| x86 (Linux, legacy) | eax | ebx | ecx | edx | esi | edi | ebp | eax |
| arm64 (Linux) | x8 | x0 | x1 | x2 | x3 | x4 | x5 | x0 |
| arm32 (Linux) | r7 | r0 | r1 | r2 | r3 | r4 | r5 | r0 |
| x86_64 (Windows ntdll) | eax | rdx | r8 | r9 | — | — | — | eax (NTSTATUS) |

Numaraların kendisi mimariye ve çekirdek sürümüne göre **değişebilir**; bu yüzden
uygulama numaraları asla sabit kodlayıp doğrudan çağırmaz, numarayı başlık dosyasından
(`unistd.h` içindeki `_SYS_*` / `__NR_*`) alır. POSIX bu numaraları değil, yalnızca
**sarmalayıcı fonksiyonların adını ve davranışını** standartlaştırır.

| Numara kaynağı | Ne verir | Değişkenlik |
|---|---|---|
| `unistd.h` (`__NR_*`) | Mimariye özel syscall numaraları | Mimari + çekirdek |
| `syscalls(2)` listesi | Linux syscall envanteri | Çekirdek sürümüne göre |
| NT syscall tablosu (ntdll) | Windows iç syscall'lar | Windows sürümüne göre, desteklenmeyen yüzey |

> CoreMusic **hiçbir zaman** numara-sabitli kod yazmaz; yalnızca başlıktan
> sarmalayıcı çağırır. Numara doğrudan çağrısı yalnızca imza/attribution aracı
> olarak birim testlerinde düşünülebilir, üretimde değil.

### §2.5 Dönüş değeri ve hata modeli

İki farklılık vardır ve bu fark hata kodunu belirler:

| Dönüş biçimi | Ne anlama gelir | Hata nasıl anlaşılır |
|---|---|---|
| Pointer döndüren (ör. `malloc`, `fopen`) | `NULL` = hata | errno yazılır |
| Tamsayı döndüren (ör. `read`, `write`) | `-1` = hata (EOF ayrı: 0) | errno yazılır |
| Windows HANDLE | `INVALID_HANDLE_VALUE` = hata | `GetLastError` okunur |

| errno | Anlamı (örnek seçki) |
|---|---|
| `EPERM` (1) | Yetki yok |
| `ENOENT` (2) | Dosya/dizin yok |
| `EINTR` (4) | Sinyalle kesildi, yeniden dene |
| `EIO` (5) | Giriş/çıkış hatası |
| `EAGAIN` (11) | Kaynak kısa süre yok, tekrar dene |
| `ENOMEM` (12) | Bellek yetersiz |
| `EACCES` (13) | Erişim reddedildi |
| `EBUSY` (16) | Kaynak meşgul |
| `EEXIST` (17) | Dosya zaten var |
| `EINVAL` (22) | Geçersiz argüman |
| `ENOSPC` (28) | Disk alanı yok |
| `ERANGE` (34) | Sonuç aralığı taştı |
| `ENOSYS` (38) | İşlev bu çekirdekte desteklenmiyor |
| `ENOTEMPTY` (39) | Dizin boş değil |
| `EOVERFLOW` (75) | Tipler taşma aralığında |
| `ECANCELED` (125) | İşlem iptal edildi |

> `errno` **fonksiyonun dönüş değeri değildir**; başarısızlıkta ayrı bir global
> değişkendir. Başarılı bir çağrının errno'u değiştirmediği varsayılmaz — errno'u
> yalnızca dönüş değeri hatayı gösterdiğinde oku. Bu kural POSIX'te de,
> Windows `GetLastError` modelinde de geçerlidir (bkz. §9 K7).

### §2.6 `EINTR` ve `EAGAIN` ayrımı

| Kod | Ne demek | Doğru tepki | Yanlış tepki |
|---|---|---|---|
| `EINTR` | Çağrı henüz tamamlanmadı, sinyal geldi | Aynı işi yeniden dene (kaldığı yerden) | Hata say, işlemi bırak |
| `EAGAIN` | Kaynak şu an yok (non-blocking) | `poll`/`select` ile bekle, sonra tekrar dene | Sıcak döngüde tekrarla (CPU yakar) |
| `EWOULDBLOCK` | `EAGAIN` ile aynı değere eşlenebilir | `EAGAIN` gibi işle | Ayrı kod san, iki yol yaz |

### §2.7 Uygulamanın gerçekleştirdiği geçiş (CoreMusic bağlamı)

CoreMusic bir **PHP uygulamasıdır**; PHP kodu syscall'a asla doğrudan erişmez.
Gerçek zincir şudur:

```
PHP kodu → Zend VM → PHP runtime (C, libc'ye bağlı) → libc sargısı → syscall kapısı
```

| # | PHP düzeyinde yapılan | Altındaki syscall sargısı (tipik) |
|---|---|---|
| R1 | Dosya okuma (`file_get_contents`) | `open` + `read` + `close` |
| R2 | Dizin tarama (`scandir`) | `open` + `getdents` + `close` |
| R3 | Alt süreç (`shell_exec`) | `fork`/`posix_spawn` + `execve` |
| R4 | Bellek dosyası (`tmpfile`) | `open` (O_TMPFILE/O_CREAT) |
| R5 | Zaman damgası (`time`) | `clock_gettime` |
| R6 | Dizin değiştirme (`chdir`) | `chdir` |
| R7 | Yetki sorgusu (`is_writable`) | `access`/`faccessat` |

> Çekirdek kapısına **en yakın** erişim katmanımız libc sargısıdır; syscall
> numarasını elle çağırmak taşınabilirliği yok eder ve K001 uygulama katmanında
> yapılmaz.

## §3 Mekanizma akışı

### §3.1 Tipik bir çağrının yaşam döngüsü

| Adım | Katman | Ne olur |
|---|---|---|
| 1 | Uygulama | `fopen("x","r")` çağırır |
| 2 | Kütüphane | Yolları doğrular, `open` syscall'ına çevirir |
| 3 | Sargı | `O_RDONLY` flag'ini hazırlar, syscall no. + path'i kayıtlara yükler |
| 4 | Kapı | `syscall`/`sysenter` ile çekirdeğe geçilir |
| 5 | Çekirdek | Path'i çözümler (KAPSAM DIŞI), yetki denetimi, dosya açılır |
| 6 | Dönüş | fd (tamsayı) döner; hata olursa `-1` + errno |
| 7 | Sargı | fd'yi `FILE*`'a sarar (`fdopen`) ve uygulamaya verir |

### §3.2 Yeniden deneme (retry) döngüsü

Bazı çağrılar sinyalle kesilince `EINTR` döndürür ve iş bitmemiş sayılır. Doğru
davranış yeniden denemektir:

| Durum | Dönen | Uygulama davranışı |
|---|---|---|
| Başarılı | ≥0 / pointer | devam |
| Sinyalle kesildi | `-1`, errno=`EINTR` | aynı argümanla tekrar dene |
| Bloklayıcı ve asla dönmeyebilir | `-1`, errno=`EINTR` | `EINTR`'ı yutma — iptal kontrolü yap |
| Kalıcı hata | `-1`, errno=diğer | hatayı işle, tekrarlama |

`EINTR`'ı körlemesine `while` ile tekrarlamak, iptal edilemeyen sonsuz döngü
oluşturabilir; bu bir anti-pattern'dir (bkz. `sistem-cagrilari-ornekler.md` §3).

### §3.3 Bloklayıcı / bloklamayan seçimi

| Mod | Nerede belirlenir | Davranış |
|---|---|---|
| Bloklayıcı (varsayılan) | fd'nin open flag'i | Veri gelene kadar çağrıyı bekletir |
| Bloklamayan | `O_NONBLOCK` (open) veya `fcntl(F_SETFL)` | Hazır değilse `-1` + `EAGAIN` |
| Zaman aşımli | `poll`/`select`/`epoll` ile önce bekleme | Süre dolarsa 0 sonuç |
| Tamamen asenkron | `io_uring` (Linux) / OVERLAPPED (Windows) | Çağrı hemen döner, bitiş ayrı bildirilir |

### §3.4 Çoklu-çığır (multiplexing) seçimi

| Arayüz | Nerede | Ölçek | Not |
|---|---|---|---|
| `select` | POSIX | Düşük fd sayısı | fd sınırı vardır; her çağrıda tüm set taranır |
| `poll` | POSIX | Orta | fd sınırı yok; yine de lineer tarama |
| `epoll` | Linux | Yüksek | Olay-uyarlı, ölçeklenir |
| `kqueue` | BSD/macOS | Yüksek | epoll karşılığı |
| OVERLAPPED + IOCP | Windows | Yüksek | IO Completion Port |

### §3.5 Taşınabilirlik geçişi (POSIX ↔ Windows)

| Kavram | POSIX yüzeyi | Windows yüzeyi |
|---|---|---|
| Evrensel dosya tanıtıcısı | `int fd` | `HANDLE` |
| Açma | `open` | `CreateFile` |
| Kapatma | `close` | `CloseHandle` |
| Okuma | `read` | `ReadFile` |
| Yazma | `write` | `WriteFile` |
| Konum değiştirme | `lseek` | `SetFilePointer` |
| Hata kodu | `errno` (`strerror`) | `GetLastError` (`FormatMessage`) |
| Sonlandırma | `exit` | `ExitProcess` |
| Alt süreç | `fork`/`execve`/`posix_spawn` | `CreateProcess` |

> İkisi **farklı sözleşmelerdir**; birindeki errno kodu diğerinde birebir eşleşmez.
> Bu yüzden CoreMusic uygulama katmanı platform API'sini sarmalayan bir soyutlama
> katmanı kullanır (bkz. `platform-kutuphaneleri/`).

### §3.6 Uyumluluk katmanları

| Uyumluluk | Ne garanti eder | Ne garanti etmez |
|---|---|---|
| POSIX.1-2024 | Sarmalayıcı fonksiyon adı ve davranışı | Numara değerlerini, çekirdek içi mekanizmayı |
| Linux syscall listesi | Çağrı envanteri | Sürüm-arası kararlılık (eklenebilir/silinebilir) |
| Windows ABI | HANDLE/NTSTATUS/çagrı düzeni | POSIX davranışını (farklı hata modeli) |
| libc sürümü | Sargının hangi syscall'ı çağırdığı | Farklı libc'lerde aynı seçim |

### §3.7 Sürüm uyumluluk ve `ENOSYS`

Yeni bir syscall eski çekirdekte yoktur. Sargı genellikle şunu yapar:

| Durum | Sargı davranışı |
|---|---|
| Çağrı çekirdekte var | Doğrudan çağır |
| Çağrı yok (`ENOSYS`) | Kullanılabilir alternatife düş (fallback) |
| Sürüm sorgusu | `uname`/`sysconf` ile çekirdek sürümü kontrol edilir |

Bu, glibc'in bazı çağrıları (ör. `truncate` için farklı yollar) birden fazla
syscall denemesine sarmasının nedenidir. Uygulama katmanı bu karmaşayı görmez.

## §4 Ölçüm / eşik

| Metrik | Eşik | Neden |
|---|---|---|
| syscall başına maliyet | ~ yüzlerce ns mertebesi (bağlama göre değişir) | Çağrı sınır aşımı pahalıdır; toplu işlem tercih edilir |
| Syscall sayısı (Linux, tipik) | ~350–450 aktif çağrı | Numara aralığı çekirdeğe göre değişir; sabit sayı varsayılmaz |
| `EINTR` beklentisi | Sinyal/iptal varsa her an gelebilir | Bloklayıcı çağrılarda yeniden deneme zorunlu |
| errno okuma penceresi | Yalnızca hata dönüşünden hemen sonra | Başka çağrı errno'u ezebilir |
| Toplu I/O | `readv`/`writev`/`sendfile` tercih edilir | Tekil çağrı sınırı geçiş başına ödenir |
| Gecikme bütçesi (ses yolu) | 512 örnek @ 48 kHz ≈ 10.67 ms | Sartname; syscall gecikmesi bütçeyi eritir |

> Sayısal eşikler bağlama (çekirdek sürümü, mimari, yük) göre değişir; bu tablo
> **yön** verir, kesin ns değeri iddia etmez. Ölçüm gerektiren satırlar
> `isletim-sistemi-observability` dalında ele alınır.

## §5 Komşu sınırı

| Dışında olan | Nereye gider |
|---|---|
| Çekirdek dispatcher, `sys_call_table`, interrupt handler | `../isletim-sistemi-kernel` |
| Cihaz dosyası erişimi, `ioctl`, `mmap` üzerinden cihaz | `../cihaz-erisimi` |
| libc/libSystem/Win32 sarmalayıcı kütüphane katmanı | `../platform-kutuphaneleri` |
| Uygulama kodunun bu çağrıları kullanışı (PHP sarmalayıcılar) | K001-application |
| HTTP/REST API | K011 · K012 |

## §6 Terim sözlüğü

| Terim | Kısa tanım |
|---|---|
| System call | Numaralı, kayıt-üstü taşınan çekirdek isteği |
| Syscall number | Çeşide atanan sabit tamsayı; mimariye göre değişir |
| Wrapper / sargı | Kütüphanenin tip güvenli sarmalayıcısı |
| errno | Hata durumunda yazılan global hata kodu değişkeni |
| `EINTR` | Çağrının sinyalle kesildiğini bildiren errno |
| `EAGAIN` | Kaynağın kısa süre kullanılamadığını bildiren errno |
| `ENOSYS` | İstenen işlevin çekirdekte bulunmadığını bildiren errno |
| `EWOULDBLOCK` | `EAGAIN` ile eşlenebilen bloklayamama kodu |
| `ECANCELED` | İşlemin iptal edildiğini bildiren errno |
| VDSO | Bazı çağrıları çekirdeğe gitmeden hızlandıran haritalanmış sayfa |
| fd (file descriptor) | Açık kaynağın süreç-içi tamsayı tanıtıcısı |
| HANDLE | Windows'ta kaynağın tür-güvenli tanıtıcısı |
| Blocking / non-blocking | Çağrının veri yokken bekleyip beklemediği mod |
| Retry döngüsü | `EINTR` için çağrıyı güvenle tekrarlama kalıbı |
| Register convention | Argüman/dönüşün kayıtlara yerleşim düzeni |
| ABI | Çağrı-no + register + stack sözleşmesi |
| Trap | Kontrollü donanım-destekli çekirdek geçişi |
| Multiplexing | Birden fazla kaynağın tek bekleme çağrısıyla izlenmesi |
| Toplu I/O | Bir geçişte çoklu tampon taşıyan çağrı (`readv`/`writev`) |

## §7 Kapsam dışı

| Kapsam dışı | Neden dışarıda |
|---|---|
| `sys_call_table` oluşturulması, dispatcher kodu | Çekirdek içi — `isletim-sistemi-kernel` |
| Preemption, zamanlayıcı, scheduler | Çekirdek içi — `isletim-sistemi-kernel` |
| `ioctl` komutları, cihaz mmap | Cihaz erişim katmanı — `cihaz-erisimi` |
| libc iç uygulaması, dinamik yükleyici (`ld.so`) | Kütüphane katmanı — `platform-kutuphaneleri` |
| HTTP endpoint, REST, middleware | Uygulama — K011/K012 |
| PHP kaynak kodu | Uygulama — K001; burada yalnızca dolaylı sargı eşlemesi var |
| Windows ntdll iç syscall'ları | Desteklenmeyen yüzey; yalnızca Win32 API |

## §8 Açık sorular

| # | Soru | Neden önemli | Durum |
|---|---|---|---|
| Q-S1 | Hedef mimariler (x86_64 / arm64) için numara aralığı ve VDSO kullanımı kesinleşti mi? | Numaralar mimari+çekirdek sürümüne bağlı; sabit kodlama kırılgan | ⚠️ VERIFICATION REQUIRED |
| Q-S2 | CoreMusic sargı katmanı olarak yalnızca libc mi, yoksa platform API'si (Win32) mi hedefleniyor? | Soyutlama sınırını ve taşınabilirlik hedefini belirler | ⚠️ VERIFICATION REQUIRED |
| Q-S3 | `EINTR`/`EAGAIN` yeniden deneme politikası K001'de merkezi mi, çağrı başına mı? | Hata modeli tutarlılığı ve tekrar eden kod riski | ⚠️ VERIFICATION REQUIRED |
| Q-S4 | Toplu I/O (`readv`/`writev`) hedefleniyor mu, yoksa tekil çağrı yeterli mi? | Geçiş-başı maliyet ve gecikme hedefi | ⚠️ VERIFICATION REQUIRED |
| Q-S5 | Çoklu-çığır seçimi (epoll vs io_uring vs IOCP) neye göre belirlenecek? | Ölçek ve gecikme hedefi | ⚠️ VERIFICATION REQUIRED |

## §9 Kaynakça

### §9.1 Web araştırma (2026-10-13 doğrulaması)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| POSIX `unistd.h` syscall ile ilgili arayüzleri standartlaştırır; numaraları değil | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/unistd.h.html | 2026-10-13 | ✅ HTTP 200 |
| errno POSIX'te `<errno.h>` ile tanımlanır | https://pubs.opengroup.org/onlinepubs/9799919799/basedefs/errno.h.html | 2026-10-13 | ✅ HTTP 200 |
| glibc program bileşenleri ve sargı katmanı | https://www.gnu.org/software/libc/manual/html_node/Program-Components.html | 2026-10-13 | ✅ HTTP 200 |
| POSIX kavramsal modeli | https://www.gnu.org/software/libc/manual/html_node/POSIX-Concepts.html | 2026-10-13 | ✅ HTTP 200 |
| Linux çekirdek genel API'si | https://www.kernel.org/doc/html/latest/core-api/kernel-api.html | 2026-10-13 | ✅ HTTP 200 |
| `strerror` errno'u metne çevirir | https://man7.org/linux/man-pages/man3/strerror.3.html | 2026-10-13 | ✅ HTTP 200 |
| `system` alt süreç başlatır (fork/exec sargısı) | https://man7.org/linux/man-pages/man3/system.3.html | 2026-10-13 | ✅ HTTP 200 |
| `printf` biçimli çıktı sargısı | https://man7.org/linux/man-pages/man3/printf.3.html | 2026-10-13 | ✅ HTTP 200 |

### §9.2 Repo kanıtı

| İddia | Dosya | Satır | Doğrulama |
|---|---|---|---|
| Uygulama alt süreci `shell_exec` ile başlatır (dolaylı `execve` sargısı) | `shared/src/AI/AIEngine.php` | 295 | `Get-Content ... \| Select-Object -Index 294` → `$output = shell_exec($cmd);` |
| Test, alt süreci `proc_open` ile başlatır (dolaylı `posix_spawn` sargısı) | `shared/tests/Repository/ApiKeyCreateCommandTest.php` | 253 | `Select-Object -Index 252` → `$handle = proc_open(` |

> **Repo dürüstlüğü:** CoreMusic'te syscall'ı **doğrudan** (numara ile) çağıran kod
> **YOK**tur — PHP bir web uygulamasıdır ve yalnızca libc sargısı düzeyinde, dolaylı
> olarak işletim sistemi arayüzünü kullanır. Yukarıdaki satırlar bu dolaylı katmanın
> kanıtıdır. Doğrudan syscall/API seviyesi kod repo'da yoktur — kaynak: upstream docs.

## §10 Sınıflandırma Derinlemesine ve Çoklu-Çığır Seçim Kapısı

> Bu bölüm §1-§9'u derinleştirir ve §8'deki Q-S5 açık sorusuna (çoklu-çığır
> seçimi) **ölçüm kapıları** ekler. Yeni tescil: §10.3'te 1 adres
> (envanter kesişimi 0 · HEAD-200 · 2026-10-13).

### §10.1 Sistem çağrısı sınıflandırması (bu dosyanın anayasal haritası)

| # | Sınıf | Ne taşır | CoreMusic ilgisi |
|---|---|---|---|
| 10.1.1 | dosya ve dizin | yol açma/kapama/ofset | §9.2 repo kanıtları |
| 10.1.2 | veri aktarım | okuma/yazma/boru | §9.2 repo kanıtları |
| 10.1.3 | süreç oluşturma | fork/exec ailesi sınıfı | §9.2 repo kanıtları (dolaylı) |
| 10.1.4 | süreç bekleme/sonlandırma | çıkış kodu toplama | §E.1-§E.4 (ağacın syscall dosyası) |
| 10.1.5 | bellek yönetimi | sayfalama/şeritleme | doğrudan kullanım yok |
| 10.1.6 | sinyal ve zamanlayıcı | bildirim/atama | RT ayrıntısı PLANNED |
| 10.1.7 | senkronizasyon | bekleme/bırakma | çekirdek-yonetim dosyası §6.6 |
| 10.1.8 | çoklu-çığır | olay demultipleksi | **§10.2-§10.4 (bu bölüm)** |
| 10.1.9 | kimlik/yetki | kullanıcı/imtiyaz | güvenlik dalı sınırı |
| 10.1.10 | bilgi sorgu | yapılandırma okuma | örnek dosyası §6.5 (confstr) |

### §10.2 Q-S5'e ölçüm kapıları (seçim nasıl kapanır?)

§8'deki Q-S5 ("çoklu-çığır seçimi neye göre belirlenecek?") bu bölümde
**ölçülebilir kapılara** ayrılır; seçim kararı bu kapıların çıktısıyla
verilir (karar bu dosyada verilmez — kapılar tanımlanır):

| # | Kapı | Soru | Ölçüm |
|---|---|---|---|
| 10.2.1 | ölçek kapısı | eşzamanlı tanıtıcı sayısı kaç? | PLANNED sayım |
| 10.2.2 | olay türü kapısı | dosya/soket/boru karışımı mı? | envanter sayımı |
| 10.2.3 | gecikme kapısı | olay-yanıt üst sınırı nedir? | PLANNED ölçüm |
| 10.2.4 | CPU maliyeti kapısı | taranacak tanıtıcı başına maliyet | PLANNED ölçüm |
| 10.2.5 | taşınabilirlik kapısı | kaç platform hedef? | sartname okuması |

### §10.3 select ailesi — öğretici sayfa tescili

Çoklu-çığır ailesinin giriş seviyesi deseni `select` sınıfıdır; bu ağacın
bu turda tescil ettiği adres, ailenin **öğretici** sayfasıdır:

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 10.3.1 | Linux man-pages projesi, select ailesi için öğretici sayfa yayımlar | https://man7.org/linux/man-pages/man2/select_tut.2.html | 2026-10-13 | ✅ HEAD 200 (envanter kesişimi 0) |

> §10.3.1 satırı yalnız sayfanın **varlığını** tesciller; sayfanın içeriğinden
> ölçek/performans sayısı aktarılmadı (sayı okuması ayrı bir gövde-okuma
> turu gerektirir — bu turda yapılmadı, iddia sınırı korundu).

### §10.4 Seçim matrisi (kavramsal — sayısal eşik yok)

| # | Mekanizma sınıfı | Bekleme deseni | Ölçek davranışı (kavramsal) | Seçim girdisi |
|---|---|---|---|---|
| 10.4.1 | taranarak (select sınıfı) | uyuyan bekleyiş | tanıtıcı arttıkça tarama maliyeti büyür | kapı 10.2.1 + 10.2.4 |
| 10.4.2 | ilgili olay (epoll sınıfı) | ilgilenilen olay uyanır | yalnız hazır olaylar gelir | kapı 10.2.1 |
| 10.4.3 | tampon tamlayıcı (IOCP sınıfı) | tamamlanmış iş bildirimi | bildirim-temelli | kapı 10.2.5 (platform) |
| 10.4.4 | halka (ring) sınıfları | zincirleme bildirim | zincirleme tamponlar | ⚠️ bu turda tescil edilmedi |

> 10.4.4 satırı bilinçli olarak adlandırılmadır: halka tabanlı arayüzün adı
> ve ayrıntıları bu turda doğrulanmadı → iddia kurulmadı (Q-S5 kuyruğu
> §10.6'da).

### §10.5 Sargı katmanının mimari özeti (dosya-içi sentez)

```text
[PHP uygulama]
     |  dil fonksiyonlari
     v
[PHP calisma zamani sargisi] --repo kanitlari: §9.2
     |  libc sargilari
     v
[libc adlandirilmis arayuzu] --unistd.h tescili: §9.1/1
     |  cekirdege gecis
     v
[cekirdek govdesi] -- numaralar ayrintidir (§6.3, cekirdek-yonetim)
```

| # | Kural | Kaynak |
|---|---|---|
| 10.5.1 | uygulama yalnız en üst kattan çağırır | §9.2 dürüstlük notu |
| 10.5.2 | adlandırılmış arayüz sözleşme, numara ayrıntıdır | §9.1/1 |
| 10.5.3 | her katmanın hata yüzeyi ayrıdır | §9.1/2 (errno tanımı) |

### §10.6 Açık soru kuyruğunun genişlemesi

| # | Yeni soru | Ne için | Durum |
|---|---|---|---|
| 10.6.1 | Q-S5/1: hedef platform seti tam olarak nedir? | kapı 10.2.5 | ⚠️ sartname yeterince kesin değil |
| 10.6.2 | Q-S5/2: eşzamanlı tanıtıcı üst sınırı nedir? | kapı 10.2.1 | PLANNED sayım |
| 10.6.3 | Q-S5/3: olay-yanıt üst sınırı (milisaniye) nedir? | kapı 10.2.3 | PLANNED ölçüm |
| 10.6.4 | yeni: sinyal-deseninin ses yolundaki rolü? | §10.1.6 | ⚠️ L0 kuralları bloklama yasaklar — çapraz inceleme |
| 10.6.5 | yeni: zincirleme bildirim arayüzü bu projede gerekli mi? | kapı 10.2.5 | UNKNOWN |

### §10.7 Web araştırma tablosu (bu bölüm)

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | man-pages projesi select öğretici sayfası erişilebilir | https://man7.org/linux/man-pages/man2/select_tut.2.html | 2026-10-13 | ✅ HEAD 200 |

> Doğrulama: `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20`;
> kesişim: mimari ağacının tüm markdown dosyalarında desen taraması → 0.

### §10.8 Dürüstlük bloğu

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10'nın eklediği tek yeni tescil
§10.7'deki tek adres satırıdır; §9.1-§9.2 tabloları aynen korunmuştur.
Q-S5 bu bölümde kapatılmadı — kapıları tanımlandı (dürüst sonuç: hâlâ açık).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

### §10.9 Seçim kararının belge-zinciri kuralı (genişletme)

§10.2-§10.4'teki kapı ve seçim tablolarına bu bölüm belge-sırası ekler —
seçim hangi sıra ile haklanır:

1. **ihtiyaç kalemi** (girdi-bütçesi, bildirim-gecikmesi, karmaşıklık) —
   sayı yok, kavram var;
2. **doktrin kısıtları** (dosyanın kendi anayasal kuralları) — örn. seçim
   kriteri sayı-uydurularak kapatılamaz;
3. **kapı-tarifleri** (§10.2) — hangi kriter kapanırsa seçim kapanır;
4. **seçim kararı** (§10.4) — yalnız ilk üç adımdan sonra yazılır;
5. **Q-S5 kapısı** (§10.2) — bu belge-zinciri tamamlanmadan kapatılamaz.

**Zincir kuralı:** adım atlanarak yazılmış seçim kararı geçersizdir; adım
5'in kapanışı yalnız kapı-4 ölçüm sonuçlarıyla olur ve o sonuçlar bu dosyada
değil, ölçüm dosyasında üretilir. Q-S5 bu ekle kapatılmaz — kapı tanımlı,
sonuç yok, durum: açık.

### §10.10 Dürüstlük bloğu (tamamlama eki 2)

Bu bölüm kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt değildir; mevcut
iddia·URL·tarih·durum sayıları aynen kalır. §10.9 ekli 0 URL, 0 repo iddiası,
0 ölçüm sonucu içerir; yalnız belge-zinciri sırası ve kapı-tarifi doktrininden
oluşur. §10.7'deki tek adres tescili, §9.1-§9.2 tabloları ve §10.8 dürüstlük
kaydı aynen korunur; Q-S5 kapısı tanımlı-kalır, kapatılmış sayılmaz.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
