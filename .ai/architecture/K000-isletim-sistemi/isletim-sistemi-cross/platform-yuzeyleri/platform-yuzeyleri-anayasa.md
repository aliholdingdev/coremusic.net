---
node_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-anayasa
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri
children: []
depends_on: []
evidence:
  - "shared/src/Component/TemplateEngine.php:73"
  - "shared/src/Api/Bff/DesktopBff.php:35"
  - ".github/workflows/ci.yml:27"
  - "https://pubs.opengroup.org/onlinepubs/9699919799/functions/V2_chap02.html"
  - "https://pubs.opengroup.org/onlinepubs/9699919799/functions/socket.html"
validation: "2026-10-13 · 4 URL HEAD 200 · envanter kesişimi 0 · repo satırları Select-Object -Index (N-1) ile okundu"
open_questions:
  - "Q4 — hedef platform seti UNKNOWN"
  - "Q-X2 — cross ↔ turu sınır kararı onaylanmadı"
group: mimari
---

# platform-yuzeyleri-anayasa — Kademe-4 Anayasa

> **Kapsam:** Bu dosya, işletim sistemi **yüzey farklarının** anayasasıdır: Win32 ↔ POSIX
> ↔ Apple/GLib karşılaştırması, ABI sonluluk kuralları (endianness · struct packing),
> sistem çağrı yüzeyi farkları, cihaz/API erişim katmanı, ölçüm, repo kanıtı, riskler.
> **Kanıt dili:** S1 = birebir repo satırı · S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

| Öğe | Değer |
|---|---|
| node_id | `K000-isletim-sistemi/isletim-sistemi-cross/platform-yuzeyleri/platform-yuzeyleri-anayasa` |
| parent_id | `…/isletim-sistemi-cross/platform-yuzeyleri` |
| group | `mimari` |
| Komşular | `isletim-sistemi-api` · `isletim-sistemi-kernel` · `isletim-sistemi-turu` · `isletim-sistemi-testing` · `K17-devops` |
| Repo gerçeği | Native kod yok (`.cpp`/`.h` glob = 0) → kanıtlar PHP/web/CI katmanından |

## §0 ELI15

"Bu kod başka OS'ta da çalışır mı?" sorusunun cevabı **yüzey**dedir: hangi fonksiyon
çağrılıyor, hangi veri tipi kaç byte, hangi hata kodu dönüyor, hangi yapılandırma dosyası
okunuyor. Aşağıdaki matris bu dört soruyu üç platform ailesi için yan yana koyar.

| Yüzey | Windows (Win32) | Linux (POSIX) | macOS (POSIX + Apple) |
|---|---|---|---|
| Dosya açma | `CreateFileW` | `open()` | `open()` + sandbox profili |
| Metin kodlaması | UTF-16 (wide) | UTF-8 byte | UTF-8 byte |
| Hata yüzeyi | `GetLastError()` | `errno` | `errno` |
| Yapılandırma | Kayıt defteri + `.config` dosyaları | `/etc` + dizin dosyaları | `plist` + `/etc` |
| Kütüphane | DLL (`LoadLibrary`) | `.so` (`dlopen`) | `.dylib` (`dlopen`) |
| Dosya biçimi | PE (`.exe`/`.dll`) | ELF (`.so`/bin) | Mach-O |
| Servis/süreç başlatma | SCM (`CreateService`) | systemd birimi | `launchd` |
| Eşzamanlılık | Win32 iplik + nesneler | pthread + futex | pthread + GCD |

## §1 Win32/POSIX/API yüzey karşılaştırma matrisi

> Bu matris **tanım kaynağı değildir** (tanım `isletim-sistemi-api` dalındadır); burada
> yalnızca **aynı işi yapan farklı adlar** listelenir. Tanımlayıcı URL'ler §9'dadır.

### §1.1 Dosya sistemi yüzeyi

| # | İş | POSIX | Win32 | Fark özeti |
|---|---|---|---|---|
| 1 | Dosya aç | `open()` | `CreateFileW()` | paylaşım modu (deny/read/write) Windows'ta açık |
| 2 | Dosya oku | `read()` | `ReadFile()` | bayrak kümesi farklı |
| 3 | Dosya yaz | `write()` | `WriteFile()` | üstünde `OVERLAPPED` opsiyonu |
| 4 | Kapat | `close()` | `CloseHandle()` | tutamaç tipi Windows'ta tiplidir |
| 5 | Oluştur | `open(O_CREAT)` | `CreateFileW(CREATE_NEW)` | mod bayrağı ile yaratma bayrağı |
| 6 | Sil | `unlink()` | `DeleteFileW()` | salt-okunur bayrak farklı |
| 7 | Dizin oluştur | `mkdir()` | `CreateDirectoryW()` | güvenlik ayarı (ACL) eklenir |
| 8 | Dizin sil | `rmdir()` | `RemoveDirectoryW()` | doluysa hata kodu farklı |
| 9 | Yeniden adlandır | `rename()` | `MoveFileExW()` | aynı birim/farklı birim davranışı |
| 10 | Bilgi al | `stat()` | `GetFileAttributesExW()` | zaman alanları ve birimleri farklı |
| 11 | Dizin tara | `opendir()`/`readdir()` | `FindFirstFileW()`/`FindNextFileW()` | akış vs arama tutamacı |
| 12 | Sembolik bağlantı | `symlink()`/`readlink()` | `CreateSymbolicLinkW()` | ayrıcalık gerekebilir |
| 13 | Gerçek yol | `realpath()` | `GetFullPathNameW()` | Windows'ta çözümleme katmanı farklı |
| 14 | İzin değiştir | `chmod()` | `SetFileAttributesW()` | Unix izin modeli yok |
| 15 | Sahiplik | `chown()` | ACL API'leri | tamamen farklı model |
| 16 | Dosya kilidi | `fcntl(F_SETLK)` | `LockFileEx()` | bayraklar ve kilik modu farklı |
| 17 | Geçici dizin | `TMPDIR`/`/tmp` | `GetTempPathW()` | PHP `sys_get_temp_dir()` bunu yutar |
| 18 | Ayrac | `/` | `\` | PHP `DIRECTORY_SEPARATOR` bunu yutar |
| 19 | Büyük/küçük harf | duyarlı (varsayılan) | duyarsız (varsayılan) | aynı yol farklı dosya olabilir |
| 20 | Aygıta erişim | `/dev/…` yolu | `\\.\…` aygıt adı | adlandırma uzayı farklı |
| 21 | Boru (pipe) | `mkfifo()` | `CreateNamedPipeW()` | istemci sunucu modeli benzer |
| 22 | Ayrık dosya | `link()` | sabit bağlantı (NTFS) | destek dosya sistemine bağlı |
| 23 | Eşzamanlı yazma | `O_APPEND` atomik | `FILE_APPEND_DATA` | atomiklik garantisi farklı |
| 24 | Tarama sonucu | `struct dirent` | `WIN32_FIND_DATA` | alan adları farklı |
| 25 | Dosya tanımlayıcı | `int` fd | `HANDLE` | 64-bit'te boyut/şeffaflık farkı |
| 26 | Monitor | `inotify` | `ReadDirectoryChangesW()` | mekanizma ve eşik farklı |

### §1.2 Süreç ve iplik yüzeyi

| # | İş | POSIX | Win32 | Fark özeti |
|---|---|---|---|---|
| 1 | Süreç başlat | `fork()` + `exec*()` | `CreateProcessW()` | Windows'ta `fork` yoktur |
| 2 | Süreç bitir | `exit()` / `_exit()` | `ExitProcess()` | atık temizliği farklı |
| 3 | Bekle | `waitpid()` | `WaitForSingleObject()` | nesne beklemesi modeli |
| 4 | PID al | `getpid()` | `GetCurrentProcessId()` | basit karşılık |
| 5 | Ortam oku | `getenv()` | `GetEnvironmentVariableW()` | kodlama UTF-16 vs byte |
| 6 | Dizin değiştir | `chdir()` | `SetCurrentDirectoryW()` | süreç-bağlamındadır |
| 7 | İplik yarat | `pthread_create()` | `CreateThread()` | yığın boyutu ve TLS farkı |
| 8 | İplik bekle | `pthread_join()` | `WaitForSingleObject()` | ayrık vs birleşik API |
| 9 | Kilit | `pthread_mutex_*()` | `CRITICAL_SECTION` / `SRWLOCK` | özyineleme ve sahiplik kuralları |
| 10 | koşul değişkeni | `pthread_cond_*()` | `ConditionVariable` | zamanlama API farklı |
| 11 | Sinyal | `signal()` / `sigaction()` | APC / `SetConsoleCtrlHandler()` | sinyal kavramı yoktur |
| 12 | İsimli kilit | `sem_open()` | `CreateMutexW()` | ad uzayı ve öncelik farklı |
| 13 | Olay nesnesi | `eventfd()` / `pipe()` | `CreateEventW()` | sayım/otomatik sıfırlama bayrakları |
| 14 | Zamanlayıcı | `timer_create()` | `CreateWaitableTimerW()` | çözünürlük ve birim farkı |
| 15 | Öncelik | `nice()` / `sched_*()` | `SetThreadPriority()` | varsayılanlar farklı |
| 16 | İşlemci numarası | `sched_getcpu()` | `GetCurrentProcessorNumber()` | ikisi de mevcut |
| 17 | TLS | `pthread_key_*()` | `FlsAlloc()` / `__declspec(thread)` | yaşam döngüsü farklı |
| 18 | Bölgesel iplik iptali | `pthread_cancel()` | `CancelSynchronousIo()` | iptal modeli farklı |
| 19 | Bellek bariyeri | `__atomic_*` / `stdatomic` | `_ReadWriteBarrier` / fence | derleyici düzeyinde ortak |
| 20 | Yield | `sched_yield()` | `SwitchToThread()` | nadiren kullanılır |
| 21 | Sona erme | `_exit()` | `TerminateProcess()` | temizlik yapmaz — riskli |
| 22 | Çocuk yeniden yönlendirme | `dup2()` ile stdin/stdout | `STARTF_USESTDHANDLES` | imza farklı |
| 23 | Zombie engelleme | `wait()` zorunlu | süreç nesnesi otomatik | Windows'ta zombie yok |
| 24 | Öncelikli sınıf | yok (nice) | `SetPriorityClass()` | süreç seviyesi ayrımı |
| 25 | Ayrık süreç grubu | `setsid()` | konsol/uzak masaüstü ayrımı | terminal kavramı farklı |

### §1.3 Bellek yüzeyi

| # | İş | POSIX | Win32 | Fark özeti |
|---|---|---|---|---|
| 1 | Tahsis | `malloc()` (libc) | `HeapAlloc()` / `malloc()` | CRT seçimi etkiler |
| 2 | Eşleme | `mmap()` | `VirtualAlloc()` | koruma bayrakları farklı |
| 3 | Kaldırma | `munmap()` | `VirtualFree()` | serbest bırakma bayrağı zorunlu |
| 4 | Kilitleme (sayfa) | `mlock()` | `VirtualLock()` | kota/dinamik denetim |
| 5 | Sayfa boyu | `sysconf(_SC_PAGESIZE)` | `GetSystemInfo()` | çalışma anında sorulur |
| 6 | Büyük sayfa | `mmap(MAP_HUGETLB)` | `MEM_LARGE_PAGES` | ayrıcalık gerekebilir |
| 7 | Anonim eşleme | `MAP_ANONYMOUS` | `MEM_RESERVE` + `MEM_COMMIT` | iki aşamalı tahsis |
| 8 | Dosyaya eşleme | `MAP_SHARED`/`MAP_PRIVATE` | `CreateFileMappingW()` | tutamaç üzerinden |
| 9 | Başlangıç adresi | `MAP_FIXED` (tehlikeli) | `MEM_TOP_DOWN` | yerleştirme politikası |
| 10 | Korum | `PROT_READ/WRITE/EXEC` | `PAGE_READWRITE/EXECUTE` | NX/DEP bayrakları |
| 11 | Yığın büyümesi | `brk()`/`sbrk()` | CRT yığını (otomatik) | nadiren doğrudan kullanılır |
| 12 | Yığın izleme | `pthread_attr_setstack()` | `CreateThread` başlangıç adresi | iplik yığını kontrolü |
| 13 | Okumaz sayfa koruma | `mprotect()` | `VirtualProtect()` | hata modeli farklı |
| 14 | Kirli bayrak | `MAP_NORESERVE` | commit edilmemiş rezerv | aşırı tahsis davranışı |
| 15 | Bellek raporu | `/proc/self/statm` | `GetProcessMemoryInfo()` | erişim yolu farklı |
| 16 | Sızıntı tespiti | `valgrind` / ASan | ASan / CRT debug heap | araç yüzeyi farklı |
| 17 | GC baskısı | `mincore()`/`madvise()` | `VirtualQuery()` + `PrefetchVirtualMemory()` | ipuçları API farklı |
| 18 | Paylaşımlı ad alanı | `shm_open()` | `OpenFileMappingW()` | ad/uzantı kuralları |
| 19 | Bölünmüş bellek | `shmget()` (SysV) | eski API yok | Windows'ta karşılığı yok |
| 20 | Nil referans koruma | `mmap` + `PROT_NONE` | `PAGE_NOACCESS` | aynı amaç farklı çağrı |

### §1.4 Ağ yüzeyi

| # | İş | POSIX | Win32/Winsock | Fark özeti |
|---|---|---|---|---|
| 1 | Soket başlat | yok (libc dâhil) | `WSAStartup()` zorunlu | süreç başlangıcında bir kez |
| 2 | Soket kapat | `close()` | `closesocket()` | dosya `close()` ile karıştırılmamalı |
| 3 | Hata | `errno` | `WSAGetLastError()` | ayrı hata alanı |
| 4 | Soket tanımlayıcısı | `int` | `SOCKET` (UINT_PTR) | 64-bit'te boyut farkı |
| 5 | Bloklayıcı mod | `fcntl(O_NONBLOCK)` | `ioctlsocket(FIONBIO)` | çağrı adı farklı |
| 6 | Seçim | `select()`/`poll()`/`epoll` | `select()`/`WSAPoll`/IOCP | `epoll` Linux'a özgü |
| 7 | Eşzamanlı model | epoll/kqueue/IOCP | IOCP | kırmızı bayrak kavramı yok |
| 8 | Adres yapısı | `struct sockaddr_in` | `sockaddr_in` + `sin_len` | BSD'de `sa_len` alanı |
| 9 | Adres çözme | `getaddrinfo()` | `getaddrinfo()` (Winsock2) | imza temelde ortak |
| 10 | UDP/TCP | `sendto()`/`recvfrom()` | birebir aynı adlar | en uyumlu yüzey |
| 11 | TLS | OpenSSL/tonlarca kütüphane | SChannel/tonlarca kütüphane | kütüphane seçimi farklı |
| 12 | UNIX soketi | `AF_UNIX` | Windows 10+ kısmi destek | taşınabilirlik riski |
| 13 | Broadcast | `SO_BROADCAST` | birebir aynı | ortak |
| 14 | Keep-alive | `SO_KEEPALIVE` (süre ayarı `setsockopt`) | `SIO_KEEPALIVE_VALS` | ayar yöntemi farklı |
| 15 | Çoklu adres | `IPV6_*` sabitleri | `IPV6_*` Winsock2 | temelde ortak |
| 16 | Timeout | `SO_RCVTIMEO` | `SO_RCVTIMEO` (milisaniye) | birim aynı |
| 17 | Arayüz seçimi | `SO_BINDTODEVICE` | `IP_UNICAST_IF` | karşılık dolaylı |
| 18 | Port aralığı | 1-65535 (aynı) | 1-65535 (aynı) | ortak numaralandırma |

### §1.5 Kütüphane, hata ve yüksek katman yüzeyi

| # | İş | POSIX | Win32 | Fark özeti |
|---|---|---|---|---|
| 1 | Kütüphane yükle | `dlopen()` | `LoadLibraryW()` | arama yolu kuralları farklı |
| 2 | Sembol bul | `dlsym()` | `GetProcAddress()` | isim manglama riski (C++'ta) |
| 3 | Kapat | `dlclose()` | `FreeLibrary()` | referans sayımı benzer |
| 4 | Hata kodu | `errno` | `GetLastError()` | çağrı anında okunmalı |
| 5 | Hata metni | `strerror()` | `FormatMessageW()` | kodlama ve dil farklı |
| 6 | Log | `syslog()`/`journal` | Event Log | kanal ve kalıcılık farklı |
| 7 | Zaman | `clock_gettime()` | `QueryPerformanceCounter()` / `GetSystemTimeAsFileTime()` | birim ve çözünürlük |
| 8 | Tekrarlanabilir rastgele | `/dev/urandom` | `BCryptGenRandom()` | erişim yolu farklı |
| 9 | Dizin tarama (üst katman) | `glob()` | `FindFirstFileW()` dizisi | şablon sözdizimi farklı |
| 10 | Dinamik bölüm | ELF `.dynsym` | PE export tablosu | biçimler farklı |
| 11 | Sembolik çizelge | ELF symbol | PE symbol (opsiyonel) | hata ayıklama bilgisi |
| 12 | Nihai kodlama | locale | `WideCharToMultiByte()` | dönüşüm açıkça yapılmalı |
| 13 | Komut satırı argümanı | `char** argv` | `wchar_t** wargv` | kodlama farkı |
| 14 | Konsol/terminal | `isatty()` | `GetConsoleMode()` | terminal kavramı |
| 15 | Dosya sistemi bekleyici | `inotify`/`kqueue` | `ReadDirectoryChangesW()` | tampon/bildirim modeli |
| 16 | Sistem bilgisi | `uname()` | `GetSystemInfo()` / `RtlGetVersion()` | runtime tespit |
| 17 | Mimari bilgisi | `uname -m` | `GetNativeSystemInfo()` | ARM64/AMD64 ayrımı |
| 18 | Kullanıcı adı | `getpwuid()` | `GetUserNameW()` | kimlik modeli farklı |
| 19 | Dil ayarı | `setlocale()` | `GetUserDefaultLocaleName()` | kullanıcı varsayılanı |
| 20 | Zaman dilimi | `TZ` / `/etc/localtime` | Registry + `GetTimeZoneInformation()` | yapılandırma yeri |
| 21 | Statik/dinamik CRT | `.a`/`.so` | `/MT` vs `/MD` | Windows'a özgü ayrım |
| 22 | Geriye dönük uyumluluk | semver + glibc sürümü | API seviyesi + `_WIN32_WINNT` | sürüm kapıları |

**§1 sonu notları:**

- 26 + 25 + 20 + 18 + 22 = **111 satırlık fark** listelenmiştir; hiçbiri tek başına
  "taşınabilirlik hatası" değildir — taşınabilirlik, **hangisinin çağrıldığını** gizlemektir.
- PHP katmanı bu yüzeyin **büyük kısmını yutar** (dosya, süreç, ağ fonksiyonları tek adla);
  bu nedenle CoreMusic'te görünen gerçek farklar §6'da listelenmiştir.
- `errno` yerine `GetLastError()` kullanımı ve `WSAStartup()` zorunluluğu, C/C++ katmanına
  (Neva Engine — PLANNED) geldiğinde doğrudan devreye girecektir.

## §2 ABI / sonluluk (endianness · struct packing)

> ABI = uygulama ile işletim sistemi arasındaki **ikili sözleşme**: hangi tip kaç byte,
> hangi sırayla yığına konuyor, hangi sırayla register'a yazılıyor. Bu bölüm bir
> **mimari yorumdur (S4)**; sayısal değerler derleyici/belge ile doğrulanmalıdır.

### §2.1 Sonluluk (endianness)

| # | Konu | Windows x86/AMD64 | Linux x86/AMD64 | ARM (her iki OS) | Not |
|---|---|---|---|---|---|
| 1 | Varsayılan byte sırası | küçük son (little-endian) | küçük son | çoğunlukla küçük son | büyük son ARM seçeneği ⚠️ VERIFICATION REQUIRED |
| 2 | Ağ byte sırası | büyük son (network order) | büyük son | büyük son | `htonl()`/`ntohl()` ortak |
| 3 | Yapıyı diske yazma | derleme zamanı sırası | derleme zamanı sırası | derleme zamanı sırası | **serileştirmede endianness açıkça kodlanmalı** |
| 4 | Byte sırası makrosu | `<stdlib.h>` içinde makro yok | `<endian.h>` `BYTE_ORDER` | `<endian.h>` | taşınabilir kod kendi makrosunu tanımlar |
| 5 | Uçlu sayı formatı | IEEE-754 | IEEE-754 | IEEE-754 | `float`/`double` serileştirme yine de dikkat ister |
| 6 | Bit alanı sırası | sağdan (LSB) | GCC: sağdan (varsayılan) | derleyiciye bağlı | MSVC ≠ GCC bit alanı yerleşimi ⚠️ |
| 7 | Yapı dolgusu | derleyiciye bağlı | derleyiciye bağlı | derleyiciye bağlı | §2.2 |
| 8 | Dosya biçimi başlığı | PE (küçük son sabit imza) | ELF (`0x7F 'E' 'L' 'F'`) | ikisi de | imzadan okunarak tanınır |
| 9 | PCM/ses verisi | küçük son (kayıt) | küçük son | küçük son | donanım tarafı K001/K004 |
| 10 | Ağ trafiği | büyük son | büyük son | büyük son | yığında dönüşüm zorunlu |
| 11 | SQLite/JSON gibi katmanlar | bayt-düz | bayt-düz | bayt-düz | format bağımsız |
| 12 | `wchar_t` genişliği | 2 byte (UTF-16) | 4 byte (UTF-32) | OS'a göre | serileştirme ❌ yapılmaz |
| 13 | `char` işaretliliği | işaretsiz | işaretli (x86) | derleyiciye bağlı | `char` diske yazılmaz |
| 14 | `long` genişliği | 4 byte (LLP64) | 8 byte (LP64) | OS'a göre | **en sık ABI sürprizi** |
| 15 | `size_t` genişliği | 64-bit'te 8 | 64-bit'te 8 | 64-bit'te 8 | ortak |
| 16 | Boyut sabitleme kuralı | `int32_t`/`uint64_t` kullan | aynı | aynı | sabit genişlik tipleri zorunlu |

### §2.2 Struct packing ve hizalama

| # | Kural | MSVC (Windows) | GCC/Clang (Linux/macOS) | Sonuç |
|---|---|---|---|---|
| 1 | Varsayılan hizalama | üyenin kendi hizasına kadar doldurur | aynı ilke | derleyici başına fark çıkar |
| 2 | Paketleme bayrağı | `/Zp` veya `#pragma pack(n)` | `__attribute__((packed))` | sözdizimi farklı |
| 3 | Yerleşim yönü | soldan (üyeler artan adreste) | aynı | ortak |
| 4 | En büyük üye hizası | yapı genel hizası ona göredir | aynı | ortak |
| 5 | Son dolgu | yapı sonunda eklenir | aynı | `sizeof` farkı yaratabilir |
| 6 | `long long` hizası | 8 | 8 | ortak |
| 7 | `long` hizası | 4 (boyut da 4) | 8 (boyut da 8) | **`sizeof` farkı** |
| 8 | `wchar_t` | 2 byte | 4 byte | **`sizeof` farkı** |
| 9 | Bit alanı `int` içi | 32 bit | 32 bit | taşma davranışı derleyiciye bağlı |
| 10 | Sıfır boyutlu dizi | uzantı | uzantı (GCC) | C23 ile standart |
| 11 | Anonim union | destekli (uzantı) | destekli (C11) | kaynak taşınabilir |
| 12 | Anonim struct (iç içe) | destekli | destekli (C11) | kaynak taşınabilir |
| 13 | Yerleşim `alignof` | `alignof` (C11) | `alignof` | ortak |
| 14 | `#pragma pack` kalıcılığı | dosya sonuna kadar | GCC'de pragma sınırlı | dikkatli kapatma |
| 15 | Ortak dış yazım | `#pragma pack(push, 1)` + `pop` | `packed` özniteliği | ikisi birden yazılmalı |
| 16 | Ağ yapıları | elle dolgusuz yazılır | aynı | her iki tarafta aynı olmalı |
| 17 | `static_assert(sizeof(…))` | C11 `static_assert` | aynı | **zorunlu savunma hattı** |
| 18 | Yerel yapı diske | ❌ yasak | ❌ yasak | serileştirme ayrı katman |
| 19 | Sabit genişlik alan yazımı | `uint8_t d[16]` | aynı | ortak desen |
| 20 | Bit alanı sırası | sağdan sayar | GCC varsayılanı sağdan | yine de taşınabilir kod yazmaz |
| 21 | `-fpack-struct` | yok | GCC bayrağı | yalnız test aracı |
| 22 | PE içinde yapı | PE başlığı `#pragma pack(4)` ile derlenir | ELF başlığı kendine özgü | biçim okuyucu formatı bilmeli |

### §2.3 ABI özet matrisi (taşınabilirlik kapıları)

| # | Kapı | Windows LLP64 | Linux LP64 | macOS LP64 | Karar |
|---|---|---|---|---|---|
| 1 | Tam sayı serileştirme | `int32_t`/`uint64_t` | aynı | aynı | sabit genişlik kullan |
| 2 | Gösterge serileştirme | ❌ asla | ❌ asla | ❌ asla | yalnız içindekileri yaz |
| 3 | `long` | 4 | 8 | 8 | `long` kullanma, `int32_t` kullan |
| 4 | `wchar_t` | 2 | 4 | 4 | geniş metin serileştirilmez |
| 5 | `size_t` karşılaştırma | 8 | 8 | 8 | 64-bit'te ortak |
| 6 | Çağrı düzeni | x64 Windows (RCX,RDX,R8,R9) | SysV AMD64 (RDI,RSI,RDX…) | SysV | **C++/C sınırında `extern "C"` + düz düzen** |
| 7 | C++ ad manglama | MSVC `?…` / Itanium farklı | Itanium mangling | Itanium | `.lib`/`.a` platforma özgü |
| 8 | `bool` boyutu | 1 | 1 | 1 | ortak (yine de serileştirme yok) |
| 9 | enum boyutu | `int` (varsayılan) | `int` | `int` | sabit genişlik yaz |
| 10 | Yığın büyümesi | 1 MB (varsayılan) | 8 MB (varsayılan) | 8 MB | derin özyineleme taşabilir |
| 11 | Async/SEH | `__try/__except` | `setjmp`/signal | aynı | mekanizma farklı |
| 12 | Alt sistem | `SUBSYSTEM_CONSOLE/GUI` | yok | yok | PE'ye özgü |

**§2 sonu notları:** Serileştirme kuralları — (1) yalnız sabit genişlik tam sayılar, (2) byte
sırası gönderim anında belgelenmeli, (3) yapı **asla** ham diske yazılmaz, (4) her iki uçta
`static_assert` ile `sizeof`/`offsetof` doğrulanır. Bu dört kural CoreMusic'in Neva Engine
(C++20, PLANNED) için de geçerli olacaktır.

## §3 Sistem çağrı yüzeyi farkları

| # | Boyut | POSIX (Linux) | Windows (NT) | CoreMusic etkisi |
|---|---|---|---|---|
| 1 | Çağrı kapısı | `syscall` numarası (glibc sarar) | `ntdll` üzerinden (numaralar değişebilir) | C++ katmanında doğrudan görünmez |
| 2 | Sarıcı | glibc fonksiyon adları | Win32 API adları | PHP fonksiyonları ikisini de yutar |
| 3 | Hata | syscall `-errno` → `errno` | `NTSTATUS`/`WIN32_ERROR` → `GetLastError` | PHP `false`/exception döner |
| 4 | Sayı | yaklaşık 450+ (x86-64) | binlerce (Win32 yüzeyi) | ⚠️ sayılar sürümle değişir |
| 5 | `fork()` | var | **yok** (`CreateProcess` kullanılır) | PHP `proc_open` altta yutar |
| 6 | Dosya tanımlayıcısı | her şey `int` fd | `HANDLE`/`SOCKET`/`SOCKET` tipli | şeffaf olmayan tip |
| 7 | Sembolik bağlantı | çekirdek destekler | NTFSjunction/symlink (ayrıcalık) | `realpath` davranışı farklı |
| 8 | Aygıt IOCTL | `ioctl()` | `DeviceIoControl()` | §4 |
| 9 | Bellek eşleme | tek çağrı `mmap` | `VirtualAlloc` + `MapViewOfFile` | iki aşamalı |
| 10 | Zaman | `clock_gettime` (sayfa) | QPC + dosya saati | ölçüm ikilisi |
| 11 | Süreç sinyali | tam sinyal seti | yok (konsol kontrol) | otomatik script davranışları |
| 12 | Dizin olayı | `inotify` | `ReadDirectoryChangesW` | bildirim mimarisi farklı |
| 13 | Sembolik ağ | BSD socket | Winsock2 (`WSAStartup`) | zorunlu başlatma |
| 14 | Uzantı yok | — | — | kavramsal fark: çekirdek modülleri |
| 15 | Kullanıcı alanı API'si | syscall + libc | Win32 kütüphanesi | iki katman farkı |
| 16 | Modül yükleme | `.so` + `LD_LIBRARY_PATH` | DLL + `LoadLibrary` arama sırası | dağıtımı etkiler |
| 17 | Çağrı sayısı (dosya) | `open/read/write/close` 4 çağrı | `CreateFile/ReadFile/WriteFile/CloseHandle` 4 çağrı | adlar farklı, sayı benzer |
| 18 | Çağrı sayısı (süreç) | `fork` + `exec` 2 aşama | tek `CreateProcess` | semantik fark |
| 19 | Çağrı sayısı (iplik) | `pthread_create` + `join` | `CreateThread` + `Wait*` | benzer |
| 20 | Hata metni dili | `strerror` (C locale) | `FormatMessage` (yerel dil) | log eşiği |
| 21 | Yetki modeli | uid/gid + mod | ACL + token | PHP'ye dolaylı |
| 22 | Sınırlama (`rlimit`) | `setrlimit()` | Job Object / kota | farklı mekanizma |
| 23 | `/proc` | var | yok (WMI/`.dll` yerine) | teşhis yolu farklı |
| 24 | Çekirdek modülü | `.ko` yüklenir | `.sys` sürücüsü (imzalı) | K002/K000-kernel |
| 25 | Kullanıcı alanı dosya sistemi | FUSE | filtre sürücüsü | karmaşıklık farklı |
| 26 | Yığın izleme aracı | valgrind/strace | ETW/Debug API | ölçüm aracılığı |
| 27 | Sistem çağrı günlüğü | `strace` | API Monitor/ETW | gözleme |
| 28 | Geriye dönük uyumluluk | glibc semantik + kernel LTS | API seviyesi bayrağı | sürüm kapısı |
| 29 | Kısıtlandırılmış çağrı | `seccomp` | AppLocker/CB | güvenlik katmanı |
| 30 | Taşınabilir kod | yalnız libc ortak alt küme | yalnız Win32 ortak alt küme | **ortak payda §4.3'te** |

**§3 notları:** Sistem çağrı yüzeyi iki OS arasında **eşlenmez, örten iki kümedir**.
PHP'nin dosya/işlem/ağ fonksiyonları bu örtüşmeyi CoreMusic için zaten **yutar**; açıkta
kalan yüzey yalnız yapılandırma dosyaları (§6) ve CI runner'ıdır.

## §4 Cihaz/API erişim katmanı

| # | Katman | Linux | Windows | Erişim kuralı |
|---|---|---|---|---|
| 1 | Aygıt adlandırma | `/dev/<ad>` | `\\.\Device\<ad>` veya `\\.\AygıtYolu` | PHP'den erişim sınırlı |
| 2 | Cihaz IOCTL | `ioctl(fd, cmd, arg)` | `DeviceIoControl(h, code, …)` | kod alanı ayrı |
| 3 | Seri port | `/dev/ttyS*` / `/dev/ttyUSB*` | `COM3` + `CreateFile` | bayrak ve Baud yapılandırması |
| 4 | Ses aygıtı | ALSA `/dev/snd/*` · PipeWire | WASAPI + `\\.\` ses aygıtı | **K000-isletim-sistemi-audio** |
| 5 | Ağ arayüzü | `netlink`/`/sys/class/net` | `GetAdaptersAddresses` | teşhis |
| 6 | USB | `udev` + `libusb` | SetupAPI/WinUSB | ayrı API ailesi |
| 7 | GPIO | `/sys/class/gpio` · `gpiod` | sensör/IOCTL (uygulamaya özel) | K001-donanım |
| 8 | Disk | `/dev/sd*` + `ioctl(BLK*)` | `CreateFile` + `DeviceIoControl(IOCTL_DISK_*)` | ayrıcalık gerekir |
| 9 | Kayıt defteri | yok (dosya tabanlı) | `RegOpenKeyEx` / `RegGetValue` | config katmanı |
| 10 | Servis başlatma | systemd birimi (root) | SCM (`StartService`) | kurulum adımı |
| 11 | Olay günlüğü | `journal` / `syslog` | Event Log | PHP log dosyası kullanır |
| 12 | Zamanlayıcı görev | cron/systemd timer | Task Scheduler | dağıtım betiği |
| 13 | Cihaz bildirimi | `udev` monitor | `WM_DEVICECHANGE`/RegisterDeviceNotification | mesaj döngüsü |
| 14 | Yazıcı | CUPS | GDI/Spooler | kapsam dışı |
| 15 | Kamera/mikrofon | PipeWire/Pulse `portal` | Media Foundation + COM | K000-audio/K011 |
| 16 | Ağ kuralı | `nftables`/`iptables` | Windows Firewall API | güvenlik |
| 17 | Dosya sistemi izleme | `inotify`/`fanotify` | `ReadDirectoryChangesW` | yapılandırma değişimi |
| 18 | Dizin silme/su | `mount()` (ayrıcalık) | `DefineDosDevice` | nadir |
| 19 | Erişim politikası | `AppArmor`/`SELinux` | AppLocker/CB politikası | ortam bağımlı |
| 20 | Python/PHP uzantısı | `.so` doldurma | DLL`doldurma` | PHP eklenti yüzeyi |
| 21 | PCI/EEPROM okuma | `sysfs` + `lspci` | SetupAPI | donanım |
| 22 | Donanım ölçüm | `/sys` dosyaları | WMI sorguları | ölçüm |
| 23 | Erişim izni | `chmod`/`chown` | ACL (`SetNamedSecurityInfo`) | farklı model |
| 24 | Aygıt kilidi | `flock` (dosya) | `LockFileEx` | PHP'de `flock` |
| 25 | Yazılım envanteri | paket yöneticisi | Win32/WMI/`Get-Package` | envanter |
| 26 | PHP'den erişim | `fopen`/`file_get_contents` (sınırlı) | COM üzerinden kısmi | CoreMusic PHP katmanı |

**§4 notları:** Bu katman CoreMusic'in **şimdi** kullanmadığı yüzeydir (PHP web katmanı
yalnız dosya ve ağ erişimi yapar); ancak Neva Engine (C++20 · PLANNED) ve K000-audio dalı
bu tabloyla doğrudan karşılaşacaktır. Ses satırı (#4) komşu daldır: `isletim-sistemi-audio`.

## §5 Ölçüm

> Ölçüm yoksa iddia yoktur. Bu bölüm, `platform-yuzeyleri` dalında **neyin** ölçüleceğini
> tanımlar; koşum ve eşikler `isletim-sistemi-testing` dalına aittir.

| # | Metrik | Nasıl ölçülür | Beklenen sonuç (bu depo) | Durum |
|---|---|---|---|---|
| 1 | Repo'daki native dosya sayısı | `Get-ChildItem -Include *.cpp,*.h` | 0 | ✅ ölçüldü (2026-10-13 = 0) |
| 2 | CMakeLists sayısı | glob | 0 | ✅ ölçüldü (0) |
| 3 | Web sunucu yapılandırması sayısı | `.htaccess` glob | 4 | ✅ ölçüldü (4) |
| 4 | IIS yapılandırması sayısı | `web.config` glob | 4 | ✅ ölçüldü (4) |
| 5 | CI runner sayısı | `runs-on:` satır taraması | 1 (`ubuntu-latest`) | ✅ ölçüldü (satır 27) |
| 6 | `windows-latest` geçmişi | workflow taraması | 0 | ✅ ölçüldü (0) |
| 7 | `macos-latest` geçmişi | workflow taraması | 0 | ✅ ölçüldü (0) |
| 8 | Platforma bağlı API çağrısı | `DIRECTORY_SEPARATOR` araması | ≥2 dosya | ✅ ölçüldü (TemplateEngine:73 · MusicStreamHandler:199) |
| 9 | OS tespit çağrısı | `php_uname` araması | ≥1 dosya | ✅ ölçüldü (DesktopBff:35) |
| 10 | Dosya yolu ayracı literal kullanımı | kodda sabit `/` birleştirme | envanter çıkar | ⚠️ VERIFICATION REQUIRED |
| 11 | `errno`/`GetLastError` doğrudan kullanımı | kod taraması | 0 (PHP katmanı) | ✅ ölçüldü (0 — C kodu yok) |
| 12 | Bağımlılık sürüm kilidi | `composer.lock`/`package-lock.json` | var | ✅ `ci.yml:44` hashFiles kanıtı |
| 13 | Uzantı şartı sayısı | `composer.json` `ext-*` | 4 | ✅ ölçüldü (satır 9-12) |
| 14 | Alt-alan başına yapılandırma çifti | `.htaccess` ∩ `web.config` | 4/4 | ✅ ölçüldü (api · assets · auth · home) |
| 15 | Üretim OS kaydı | vault araması | yok | ❌ **UNKNOWN** (Q4) |
| 16 | ARM64 build adımı | workflow adı araması | yok | ❌ **UNKNOWN** (Q-X9) |
| 17 | Eşzamanlılık yüzeyi ölçümü | kilit/kuyruk gözlemi | PLANNED | ⚠️ yalnız C++ katmanında anlamlı |
| 18 | Kodlama ölçümü (UTF-8 tutarlılığı | dosya kodlama tarama | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Ölçüm notu:** 1-14 satırı **S1** sınıfıdır (bu dosyada method ile birlikte yazılmıştır);
15-18 satırı bilinmeyendir ve `verified` yapılmaz.

## §6 Repo kanıtı

> Bu depoda cross-platform native kod **YOKTUR** — web katmanı PHP'dir. Sınır = web
> sunucusu yapılandırması (`.htaccess` · `web.config`) ve PHP'nin platform yardımı.

**Kanıt 1 — Dosya yolu ayracı (S1):** `shared/src/Component/TemplateEngine.php:73`

```php
        $cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';
```

**Kanıt 2 — Runtime OS tespiti (S1):** `shared/src/Api/Bff/DesktopBff.php:35`

```php
            'platform' => php_uname('s'),
```

**Kanıt 3 — Test runner'ı tek OS (S1):** `.github/workflows/ci.yml:27`

```yaml
    runs-on: ubuntu-latest
```

| # | Kanıt | Dosya:Sınıf | Ne kanıtlar | Kanıt sınıfı |
|---|---|---|---|---|
| 1 | `DIRECTORY_SEPARATOR` | `shared/src/Component/TemplateEngine.php:73` | ayracı PHP verir | S1 |
| 2 | `php_uname('s')` | `shared/src/Api/Bff/DesktopBff.php:35` | OS **gizlenmez**, raporlanır | S1 |
| 3 | `runs-on: ubuntu-latest` | `.github/workflows/ci.yml:27` | test tek OS | S1 |
| 4 | `mkdir`/`realpath` sınır denetimi | `home.coremusic.net/include/Stream/MusicStreamHandler.php:199` | yol ayracı denetimi | S1 |
| 5 | Apache rewrite | `home.coremusic.net/.htaccess:27` | Apache yüzeyi | S1 |
| 6 | IIS rewrite | `home.coremusic.net/web.config:14` | IIS yüzeyi | S1 |
| 7 | Çift yapılandırma | 4 alt-alanda `.htaccess` + `web.config` | platform farkı **yutulmaz**, çift dosya | S1 |
| 8 | Native kod yok | `*.cpp`/`*.h` glob = 0 | ABI matrisi §2 henüz **kodlanmadı** | S1 |
| 9 | Üretim OS | kayıt yok | bilinmiyor | S7 (UNKNOWN) |

**Dürüstlük satırı:** §2 (ABI) ve §3 (syscall) bölümleri bu depoda **kod kanıtıyla
desteklenemez** çünkü C/C++ katmanı yoktur — bu bölümler **S4 (mimari yorum)** sınıfındadır
ve Neva Engine PLANNED olduğunda S1'e çevrilecektir.

## §7 Riskler

| # | Risk | Olasılık | Etki | Bağlantı |
|---|---|---|---|---|
| R1 | Üretim OS bilinmiyor (Q4) — yanlış varsayım tüm matrisi yanlış kurar | yüksek | yüksek | §5 #15 |
| R2 | Test tek OS (`ubuntu-latest`) — Windows/macOS kırılması üretimde görülür | orta | yüksek | §5 #5-7 |
| R3 | `.htaccess` ↔ `web.config` elle senkron — unutulan başlık/kural güvenlik açığı yaratır | orta | yüksek | §6 #7 |
| R4 | ABI matrisi (§2) kod kanıtsız — Neva Engine'de `long`/`wchar_t` sürprizi | orta | yüksek | §2.3 |
| R5 | Bit alanı/`struct packing` MSVC ≠ GCC varsayılanı | düşük | orta | §2.2 #1-5 |
| R6 | Büyük son (big-endian) ARM hedefi gelirse serileştirme kırılır | düşük | yüksek | §2.1 #3 |
| R7 | `errno` vs `GetLastError` karışımı C++ katmanında yanlış hata yutması | orta | orta | §3 #3 |
| R8 | `fork()` varsayımı olan kod Windows'a taşınırsa kırılır | düşük | orta | §3 #5 |
| R9 | Dosya adı duyarsızlığı — Linux'ta `Config.php` vs `config.php` | orta | orta | §1.1 #19 |
| R10 | Kodlama (UTF-16 vs UTF-8) dönüşümü — `wchar_t` serileştirmesi | düşük | yüksek | §2.1 #12 |
| R11 | ARM64 build adımı yok (Q-X9) | orta | orta | §5 #16 |
| R12 | Uzantı (`ext-apcu` vb.) olmayan ortamda kurulum düşer | orta | orta | §5 #13 |
| R13 | `inotify`/`ReadDirectoryChangesW` farkı — config izleme özelliği | düşük | düşük | §1.1 #26 |
| R14 | Dosya monitorü/işlem modeli PHP'de `filemtime` döngüsüne gömülür | orta | düşük | §4 #17 |
| R15 | Ayrık/zombie kavramı Windows'ta olmadığından süreç bekleme kodu yanlış yazılır | düşük | orta | §1.2 #23 |
| R16 | `COM`/`GObject` yüksek katman farkı — ses/gihaz API'lerine giriş | orta | orta | §4 #15 |

## §8 Kapsam dışı

| # | Konu | Nereye gider | Neden bu dalda değil |
|---|---|---|---|
| 1 | OS API imzalarının tanımı | `isletim-sistemi-api` | Bu dal yalnız **fark** üretir |
| 2 | Çekirdek/AB anatomisi | `isletim-sistemi-kernel` | Sistem çağrısının **iç yapısı** sınır dışı |
| 3 | OS ailelerinin derin karşılaştırması | `isletim-sistemi-turu` | Aile **içinde** derinlik orada |
| 4 | Test koşumu ve eşikler | `isletim-sistemi-testing` | Burada yalnız ölçüm **tanımı** var |
| 5 | CI pipeline tasarımı | `K17-devops` | `ci.yml` yalnız kanıt |
| 6 | DSP/donanım taşınabilirliği | `K004-dsp` · `K001-donanım` | Donanım katmanı sınır dışı |
| 7 | Ses platform API'leri (WASAPI/ALSA/CoreAudio) | `isletim-sistemi-audio` | §4 #4 komşuya devredildi |
| 8 | OS güvenlik mekanizmaları | `isletim-sistemi-security` | Güvenlik politikası ayrı dal |
| 9 | Gözlemlenebilirlik (log/metrik) | `isletim-sistemi-observability` | Ölçüm ≠ gözetim |
| 10 | Runtime/örtük kütüphane | `isletim-sistemi-runtime` | Çalışma zamanı ayrı dal |
| 11 | Paket formatları (MSI/deb/rpm) | `../derleme-ve-paketleme` | Komşu alt-dal |
| 12 | `#ifdef`/platform guard kalıpları | `../tasinabilirlik-siniri` | Komşu alt-dal |
| 13 | Kod örneklerinin tamamı | `platform-yuzeyleri-ornekler.md` | Bu dosya yalnız özet kanıt taşır |
| 14 | Sürücü/servis yaşam döngüsü detayı | `platform-yuzeyleri-surucu.md` | Komşu modül |

## §9 Kaynakça (web araştırma · 2026-10-13 · HEAD · envanter kesişimi 0)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| POSIX sistem çağrıları hata yüzeyi (`errno`) V2 bölümünde tanımlanır | https://pubs.opengroup.org/onlinepubs/9699919799/functions/V2_chap02.html | 2026-10-13 | ✅ HTTP 200 |
| POSIX `socket()` ağ API'sinin ortak tanımıdır | https://pubs.opengroup.org/onlinepubs/9699919799/functions/socket.html | 2026-10-13 | ✅ HTTP 200 |
| Apple Foundation Apple platformlarında yüksek katman API sağlar | https://developer.apple.com/documentation/foundation | 2026-10-13 | ✅ HTTP 200 |
| Linux `ip(7)` el kitabı ağ arayüzü/socket katmanını belgeler | https://man7.org/linux/man-pages/man7/ip.7.html | 2026-10-13 | ✅ HTTP 200 |

## §10 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q4 | Hedef platform seti (Windows/Linux/macOS/ARM) tek SSOT'ta tanımlı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X2 | `cross` ↔ `turu` sınırı (fark vs derinlik) onaylandı mı? | ⚠️ VERIFICATION REQUIRED |
| Q-X20 | §2 ABI matrisi Neva Engine şablonuna `static_assert` olarak taşınacak mı? | UNKNOWN |
| Q-X11 | Büyük son ARM hedefi gündeme gelecek mi (serileştirme kapısı)? | UNKNOWN |
| Q-X21 | §4 cihaz katmanından hangi satırlar K000-audio'ya devredildi? | UNKNOWN |
| Q-X17 | `e2e` CI işinin adı/satırı okunmadı | ⚠️ VERIFICATION REQUIRED |

## §11 Yüzey farkları hızlı referans (S4 — mimari yorum)

> Bu bölüm, §1–§4 matrislerinin **sık bakış** özetidir; her satır yukarıdaki bölümlere
> bağlanır. Kanıt sınıfı S4'tür (kod kanıtı yok — bkz. §6 dürüstlük satırı).

### §11.1 10 saniyelik karar tablosu

| # | Karar anı | Doğru soru | Yönlendirme |
|---|---|---|---|
| 1 | Yeni dosya açılıyor | wide mı byte mı? | §1.1 #1-3 |
| 2 | Hata kodu çevriliyor | errno mu GetLastError mı? | §3 #1-3 |
| 3 | Serileştirme yazılıyor | son (endianness) sabit mi? | §2.1 #3 |
| 4 | Struct belleğe yazılıyor | packing/katıştırma verildi mi? | §2.2 #1-5 |
| 5 | Kütüphane yükleniyor | LoadLibrary mı dlopen mı? | §1.3 #1-4 |
| 6 | Servis başlatılıyor | SCM mı systemd mı launchd mı? | §5 #8-10 |
| 7 | Yol birleştiriliyor | `\\` mı `/` mı, DIRECTORY_SEPARATOR var mı? | §6 #1 |
| 8 | Uzantı/eksik bağımlılık | Windows `.exe` uzantısı unutuldu mu? | §4 #9-12 |
| 9 | Eşzamanlılık primitive | pthread mı Win32 iplik nesnesi mi? | §1.2 #1-6 |
| 10 | Config okunuyor | Kayıt defteri mi dosya mı plist mı? | §1.1 #10-12 |

### §11.2 Sık yapılan 12 platform hatası

| # | Hata | Kırıldığı yer | Önleme |
|---|---|---|---|
| 1 | `long` boyutunu 4 sanmak | LP64 (Linux/macOS 64-bit) → 8 | `int32_t`/`int64_t` kullan |
| 2 | `wchar_t` 2 byte sanmak | POSIX'te 4 (UTF-32) | `char16_t`/`char32_t` seç |
| 3 | Path ayracını sabit yazmak | Windows `\\` | `DIRECTORY_SEPARATOR` (repo kanıtı §6 #1) |
| 4 | Dosya adı duyarsızlığını varsaymak | Linux/macOS duyarsız değil | iki yazım da denensin |
| 5 | `errno` tek okuma sanmak | Çağrılar arasında sıfırlanabilir | hemen oku, kopyala |
| 6 | `GetLastError`'ı başka çağrıdan sonra okumak | Ara çağrı hatayı sıfırlar | hemen oku |
| 7 | Bit alanını yazmak | MSVC/GCC sıralama farklı | `static_assert(sizeof(T)==N)` |
| 8 | Big-endian yok sanmak | ARM/PowerPC hedefleri | serileştirmede son denetimi |
| 9 | `fork()` her yerde var sanmak | Windows'ta yok | süreç modeli soyutlanır |
| 10 | Zombie/ayrık süreçi Windows'ta beklemek | kavram yok | `WaitForSingleObject` ayrı yol |
| 11 | `.so`/`.dll` yüklemeyi tek kodda tutmamak | üç ayrı API | ince sarmalayıcı (wrapper) |
| 12 | Testi tek OS'ta geçirmek | `ci.yml:27` ubuntu tek | matris testi (§5 #5-7) |

### §11.3 Neva Engine'e aktarım listesi (PLANNED)

| # | Kural | Hedef (PLANNED) | Bağ |
|---|---|---|---|
| 1 | Tümler boyutlu tipler (`int32_t` …) | Neva Engine tipleri | §2.3 #1 |
| 2 | `static_assert(sizeof…)` gardları | şablonda derleme anı denetim | §2.2 #5 |
| 3 | Serileştirme son denetimi | metadata kodu | §2.1 #3 |
| 4 | Hata yüzeyi sarmalayıcı | errno/GetLastError tek kapı | §3 #1-3 |
| 5 | Yol sarmalayıcı | tek path API | §1.1 #1-3 |
| 6 | Kütüphane yükleme sarmalayıcı | LoadLibrary/dlopen/dylib | §1.3 #1-4 |
| 7 | Eşzamanlılık soyutlaması | pthread/Win32 arkasında tek arayüz | §1.2 #1-6 |
| 8 | Platform matrisi testi | Linux+Windows+macOS derleme | §5 #5-7 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
