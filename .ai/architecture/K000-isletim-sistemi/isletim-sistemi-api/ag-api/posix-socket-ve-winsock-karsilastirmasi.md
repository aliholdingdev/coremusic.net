---
node_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api/posix-socket-ve-winsock-karsilastirmasi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api
children: []
depends_on: []
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/winsock/windows-sockets-start-page-2
  - https://learn.microsoft.com/en-us/windows/win32/winsock/windows-sockets-error-codes-2
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-wsastartup
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-closesocket
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-ioctlsocket
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/ns-winsock2-wsadata
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-wsasocketa
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-socket
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-select
  - https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-recv
  - https://learn.microsoft.com/en-us/windows/win32/api/mstcpip/
validation: "2026-10-13 — 12 resmi URL HTTP 200 + envanter kesişimi 0"
open_questions: []
group: mimari
---

# posix-socket-ve-winsock-karsilastirmasi

**ELI15:** Linux/macOS'ta ağ kodu `socket()` çağrısıyla başlar. Windows'ta aynı
kod `WSAStartup()` ile başlar, `SOCKET` tipini kullanır ve `close()` yerine
`closesocket()` çağırır. Bu dosya iki dünya arasındaki farkları tablo tablo
belgeler ve tek kod tabanının ikisinde de çalışması için sarmalama stratejisi
çizer.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api/posix-socket-ve-winsock-karsilastirmasi |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §A — POSIX socket ↔ Winsock2 farkları tablosu (WSAStartup · closesocket · SOCKET tipi · hata kodu)

### A.1 Başlangıç / kapanış farkları

| Kavram | POSIX | Winsock2 | Not |
|---|---|---|---|
| Başlangıç çağrısı | Yok — soket her zaman hazır | `WSAStartup(MAKEWORD(2,2), &wsa)` | Winsock yükleyici DLL'ini başlatır; **her süreçte bir kez** |
| Kapanış çağrısı | `close(fd)` | `closesocket(s)` | `close()` bir Winsock soketinde **belirsiz sonuç verir** |
| Kapanış (sürümleme) | Yok | `WSACleanup()` | `WSAStartup` sayısınca çağrılır — pratikte süreç kapanışında bir kez |
| Sürüm pazarlığı | Yok | `MAKEWORD(major, minor)` + `WSAStartup` dönüşü | İstenen sürümün onaylanmadığı kontrol edilir |
| Çakışma koruması | `SO_REUSEADDR` (TIME_WAIT bind'i) | `SO_EXCLUSIVEADDRUSE` (tek kullanıcı) | §A.5 — bayrak anlamları farklıdır |

### A.2 Tip farkları

| Kavram | POSIX | Winsock2 | Fark |
|---|---|---|---|
| Soket tipi | `int` (dosya descriptor'u) | `SOCKET` (`UINT_PTR` — işaretleyici genişliğinde tamsayı) | 64-bit Windows'ta `SOCKET` 64-bit; `int` değil |
| Geçersiz değer | `-1` (`EBADF`) | `INVALID_SOCKET` (`~0`) | Karşılaştırma `== -1` değil `== INVALID_SOCKET` |
| Hata dönüşü | `-1` + `errno` | `SOCKET_ERROR` (-1) + `WSAGetLastError()` | `errno` **Winsock hatalarını taşımaz** |
| Bağlantı sonucu | `connect` → 0 | `connect` → 0 (aynı) | Ayrışma yok |
| Adres uzunluğu | `socklen_t` | `int` | `accept` · `recvfrom` · `getpeername` imzaları |

### A.3 Hata kodu farkları

| Konu | POSIX | Winsock2 |
|---|---|---|
| Okuma yeri | `errno` | `WSAGetLastError()` |
| Karşılık gelen kod | `ECONNRESET` (104 — Linux değeri) | `WSAECONNRESET` (10054) |
| Bağlantı reddi | `ECONNREFUSED` (111) | `WSAECONNREFUSED` (10061) |
| Non-blocking hazır değil | `EAGAIN` / `EWOULDBLOCK` | `WSAEWOULDBLOCK` (10035) |
| Devam eden bağlantı | `EINPROGRESS` (115) | `WSAEWOULDBLOCK` (10035) — `connect` non-blocking'te |
| Adres kullanımda | `EADDRINUSE` (98) | `WSAEADDRINUSE` (10048) |
| Zaman aşımı | `ETIMEDOUT` (110) | `WSAETIMEDOUT` (10060) |

> Sayısal değerler Linux x86/x64 değerleridir; POSIX standart `errno` değerleri
> için **sabit adları** kullanılır (sayısal değerler platforma göre değişir).
> Winsock kodlarının tam listesi Microsoft "Windows Sockets Error Codes"
> sayfasındadır (§E.12).

### A.4 non-blocking farkları

| İşlem | POSIX | Winsock2 |
|---|---|---|
| Kurulum | `fcntl(fd, F_SETFL, O_NONBLOCK)` | `ioctlsocket(s, FIONBIO, &arg)` — `u_long arg = 1` |
| Kaldırma | `fcntl` + `O_NONBLOCK` bayrağı kaldırılır | `ioctlsocket(s, FIONBIO, &0)` |
| Hazır değil hatası | `EAGAIN` / `EWOULDBLOCK` | `WSAEWOULDBLOCK` |
| Bağlanıyor | `EINPROGRESS` | `WSAEWOULDBLOCK` |

### A.5 `SO_REUSEADDR` — davranış farkı (kritik)

| Platform | `SO_REUSEADDR` davranışı |
|---|---|
| Linux | `TIME_WAIT` durumundaki aynı adres/port'a yeniden `bind`'i **izin verir** |
| Windows | Aynı etkiyi vermez; **`SO_REUSEADDR` iki aktif sunucunun aynı port'u bind etmesine izin verir** — yanlış yapılandırılmış çoğul sunucu trafiği böler (gizli hata kaynağı) |

Windows'ta doğru seçenek **`SO_EXCLUSIVEADDRUSE`**dur: adresin tek kullanıcıyla
sınırlanmasını sağlar (Microsoft Winsock dokümantasyonu — §E.12).

### A.6 Hata kodu → POSIX karşılık tablosu (yaygın subset)

| Winsock kodu | Değeri | POSIX karşılığı | Anlam |
|---|---|---|---|
| `WSAEWOULDBLOCK` | 10035 | `EAGAIN` / `EWOULDBLOCK` | Non-blocking: tekrar dene |
| `WSAECONNRESET` | 10054 | `ECONNRESET` | Bağlantı sıfırlandı |
| `WSAECONNREFUSED` | 10061 | `ECONNREFUSED` | Sunucu reddetti |
| `WSAETIMEDOUT` | 10060 | `ETIMEDOUT` | Zaman aşımı |
| `WSAEADDRINUSE` | 10048 | `EADDRINUSE` | Adres kullanımda |
| `WSAENOTCONN` | 10057 | `ENOTCONN` | Soket bağlı değil |
| `WSAEINTR` | 10004 | `EINTR` | Çağrı kesintiye uğradı |
| `WSAEMSGSIZE` | 10040 | `EMSGSIZE` | Mesaj çok büyük |
| `WSAHOST_NOT_FOUND` | 11001 | `EAI_NONAME` (getaddrinfo) | İsim bulunamadı |
| `WSANOT_INITIALISED` | 10093 | — (POSIX karşılığı yok) | `WSAStartup` çağrılmadı |

### A.7 `send`/`recv` imza farkları

| Parametre | POSIX | Winsock2 |
|---|---|---|
| Bayt sayısı tipi | `size_t` | `int` — negatif giriş koruması derleyici düzeyinde |
| `flags` tipi | `int` | `int` (aynı) | 
| Dönüş | `ssize_t` | `int` | 
| `MSG_NOSIGNAL` | Linux'ta var | Yok — `SIGPIPE` zaten yoktur |

### A.8 `select` farkları

| Konu | POSIX | Winsock2 |
|---|---|---|
| İlk parametre | `nfds` — en yüksek descriptor + 1 | Yok — yok sayılır (`0` yazılır) |
| Set temizliği | Her çağrıdan önce yeniden `FD_SET` gerekir | Aynı kural — `fd_set` Windows'ta **taşma riski** taşır |
| `FD_SETSIZE` | 1024 (varsayılan) | Winsock'ta `FD_SETSIZE` 64 (dizi boyutu) — **büyük bağlantı sayılarında genişletilmeli** |
| Kapanan soket | `POLLHUP` benzeri | `FD_CLOSE` olayı |

### A.9 Byte order ve adres API eşdeğerliği

| API | POSIX | Winsock2 | Not |
|---|---|---|---|
| `htons` / `htonl` | Var | Var (`winsock2.h` içinde) | Eşdeğer |
| `inet_pton` | Var | Var (`WSA` öneki gerekmez) | Vista+ |
| `inet_ntop` | Var | Var | Vista+ |
| `getaddrinfo` | Var | Var | RFC 3493 tabanlı — iki platformda da çağdaş çözümleyici |
| `struct sockaddr_in` | `<netinet/in.h>` | `<winsock2.h>` | Alan düzeni aynı |

> `getaddrinfo` + `inet_pton`/`inet_ntop` kullanımı **platform bağımsızdır** —
> eski `inet_addr`/`inet_ntoa` (nokta-dört-bayt sadece) kaçınılmalıdır.

### A.10 Sinyal farkları

| Konu | POSIX | Winsock2 |
|---|---|---|
| `SIGPIPE` | `send`/`write` üretir — `MSG_NOSIGNAL` veya `SIG_IGN` gerekir | Yok — `WSAECONNRESET` döner |
| `EINTR` | Sinyaller çağrıyı böler — yeniden dene | Winsock'ta anlamsız (Windows sinyal modeli farklı) |
| `MSG_NOSIGNAL` bayrağı | Linux var | Desteklenmez (bayrak yok) |

### A.11 Ek yapılandırma farkları

| Konu | POSIX | Winsock2 |
|---|---|---|
| Keep-alive zamanlaması | `TCP_KEEPIDLE` / `TCP_KEEPINTVL` / `TCP_KEEPCNT` (Linux) | `WSAIoctl` + `tcp_keepalive` yapısı (`mstcpip.h`) |
| Adres yeniden kullanım bayrağı | `SO_REUSEADDR` | `SO_EXCLUSIVEADDRUSE` (güvenli seçenek) |
| Overlapped I/O | Yok (tek model) | `WSARecv` + `OVERLAPPED` — asenkron tamamlama modeli |
| Header | `<sys/socket.h>` | `<winsock2.h>` (+ `<ws2tcpip.h>`) |
| Kütüphane bağlantısı | libc içinde | `Ws2_32.lib` (linker) — DLL: `ws2_32.dll` |

## §B — Soket türleri (stream/dgram/raw · overlapped)

### B.1 `type` parametresi karşılaştırması

| Tür | POSIX | Winsock2 | Not |
|---|---|---|---|
| `SOCK_STREAM` | Var — TCP / UNIX stream | Var — TCP | Eşdeğer |
| `SOCK_DGRAM` | Var — UDP / UNIX dgram | Var — UDP | Eşdeğer |
| `SOCK_RAW` | Var — CAP_NET_RAW | Var — `WSASocket` + `WSA_FLAG_NO_HANDLE_INHERIT` ayrıcalık gerekebilir | Kapsam dışı |
| `SOCK_SEQPACKET` | Var (UNIX, SCTP) | Yok | Windows'ta destek yok — taşınabilirlik tuzağı |
| `SOCK_RDM` | Sınırlı | Yok | Pratikte kullanılmaz |

### B.2 `socket()` vs `WSASocket()`

| Çağrı | Parametre fazlalığı | Kullanım |
|---|---|---|
| `socket(af, type, proto)` | 3 parametre | Basit senaryo — Winsock2'de `WSASocket` sarmalayıcısı |
| `WSASocket(af, type, proto, info, g, flags)` | `WSAPROTOCOL_INFO` + bayraklar | Protocol seçimi · gruplandırma · `WSA_FLAG_OVERLAPPED` |

> Windows `socket()` çağrısı belgeye göre `WSASocket`'ın `WSA_FLAG_OVERLAPPED`
> ile çağrılmış sarmalayıcısıdır — overlapped bayrağı varsayılan olarak vardır
> (Microsoft Winsock dokümantasyonu — §E.12).

### B.3 Overlapped I/O — Windows'a özgü asenkron model

| Kavram | Açıklama |
|---|---|
| `OVERLAPPED` yapısı | Çağrıya verilen tampon + olay kolu (event) + bitiş bildirimi |
| `WSARecv` / `WSASend` | Overlapped ile çağrılır — hemen döner, veri hazır olunca tamamlanır |
| Tamamlama bildirimi | 3 yol: olay kolu · `WSAGetOverlappedResult` · completion routine |
| IOCP (I/O Completion Port) | Binlerce soketi tek completion port'ta toplama — ölçekli sunucu modeli |
| POSIX muadili | `aio` (POSIX) / `io_uring` (Linux) — **model farklıdır**, birebir eşleme yok |

### B.4 Model karşılaştırması — tek işlemli döngü

| Adım | POSIX event döngüsü | Winsock event döngüsü |
|---|---|---|
| 1 | `socket` + `bind` + `listen` | `WSAStartup` + `socket` + `bind` + `listen` |
| 2 | `fcntl(O_NONBLOCK)` | `ioctlsocket(FIONBIO)` |
| 3 | `epoll_create1` / `kqueue` / `select` | `WSAEventSelect` + `WSAWaitForMultipleEvents` veya `select` |
| 4 | `recv` → `EAGAIN` bekle | `recv` → `WSAEWOULDBLOCK` bekle |
| 5 | `close` | `closesocket` + `WSACleanup` |

### B.5 `select` vs `WSAEventSelect` vs IOCP

| Mekanizma | Olay dayanma | Sınıf |
|---|---|---|
| `select` | Her iki platformda — az bağlantı | Taşınabilir referans |
| `WSAEventSelect` | Soket başına Win32 event — `WaitForSingleObject` ailesiyle | Windows entegrasyonu |
| IOCP | Completion port — en yüksek ölçek | Windows özel |
| `epoll` / `kqueue` | Linux/BSD özel | POSIX-üstü |

### B.6 Kalıtım (inheritance) bayrakları

| Bayrak | Anlam |
|---|---|
| `WSA_FLAG_NO_HANDLE_INHERIT` | `CreateProcess` ile child'a soket sızmasını engeller — POSIX `SOCK_CLOEXEC` karşılığı |
| `SOCK_CLOEXEC` (POSIX) | `exec` sırasında descriptor kapatılır |
| `WSA_FLAG_OVERLAPPED` | Overlapped I/O izni — `socket()` varsayılanı |

### B.7 Soket türü karar matrisi

| İhtiyaç | POSIX türü | Winsock türü | Not |
|---|---|---|---|
| Güvenilir akış (HTTP · RPC) | `SOCK_STREAM` | `SOCK_STREAM` | Birebir eşdeğer |
| Hızlı dagram (telemetri · ses komutu) | `SOCK_DGRAM` | `SOCK_DGRAM` | Birebir eşdeğer |
| Yerel IPC | `SOCK_STREAM` + `AF_UNIX` | `AF_UNIX` (Windows 10 1803+) | Windows AF_UNIX desteği sınırlı — `AF_INET` loopback daha taşınabilir |
| Paket dinleme (raw) | `SOCK_RAW` | `SOCK_RAW` | Ayrıcalık gerekir — kapsam dışı |
| Paket sıralı (SCTP) | `SOCK_SEQPACKET` | Yok | Windows'ta destek yok — kaçın |
| Overlapped asenkron | Olay demultiplexing | `WSA_FLAG_OVERLAPPED` + IOCP | Modeller farklı — §B.3 |

> Windows 10 1803+'da `AF_UNIX` Winsock'e eklendi; ancak taşınabilirlik için
> `AF_INET` loopback önerilir (§D.6 sürüm notu ile birlikte okunur).

## §C — Platform bağımsız sarmalama stratejisi

### C.1 Strateji seçenekleri

| # | Strateji | Tanım | Artı | Eksı |
|---|---|---|---|---|
| S1 | `#ifdef` sarmalayıcı | Tek dosya içinde platform blokları | Basit | Bakım maliyeti — bloklar iç içe geçer |
| S2 | Soyut arayüz + iki uygulama | `ISocket` arayüzü · `PosixSocket` · `WinsockSocket` | Temiz sınır — SOLID | Sanal dispatch maliyeti (RT yolunda dikkat) |
| S3 | Alt kütüphane sarmalayıcı (ör. asio · libuv · JUCE) | Mevcut soyutlamayı kullanma | En az kod | Bağımlılık bump kuralı: DUR + el değişimi |
| S4 | Kapsayıcı / uzaklaştırma | Ağ işi ayrı süreç | Sınırlama güçlü | Gecikme artışı — ses senaryosuna uygun değil |

### C.2 CoreMusic bağlamında değerlendirme

| Senaryo | Değerlendirme |
|---|---|
| L0 (Neva Engine) C++ motoru | Ağ özelliği henüz planlanmadı (Q-A1) — sarmalama kararı **ertelendi** |
| PHP katmanı | `curl` zaten sarmalayıcı — repo kanıtı: `shared/src/AI/ToolCalling.php:256` (`$ch = curl_init($url);`) |
| JUCE kullanımı | JUCE `DatagramSocket`/`StreamingSocket` soyutlaması sunar — sürüm kanıtı yok (AGENTS.md kural 12) |

> Kural: sarmalama **ihtiyaç ortaya çıkınca** yazılır (YAGNI). Mevcut durumda
> iki hedef platform arasında paylaşılan ağ kodu **yok** — dosya yalnızca
> kararı hazırlar, uygulama yapmaz.

### C.3 Önerilen soyutlama (gelecek karar için)

| Seviye | İçerik |
|---|---|
| Arayüz | `send` · `recv` · `close` · `set_nonblocking` · `last_error` → taşınabilir hata enum'u |
| Hata dönüşü | Platform `errno`/`WSAGetLastError` → ortak enum (`ConnReset` · `WouldBlock` · `Timeout` · `Refused`) |
| Temizlik | RAII sarmalayıcı — `noexcept` yıkıcı (`socket-api-temelleri.md` §D.9) |
| RT kuralı | Ağ çağrısı ses callback'inde **yapılmaz** — ayrı thread + lock-free kuyruk |

### C.4 Hata eşleme katmanı

| Ortak enum | POSIX karşılığı | Winsock karşılığı |
|---|---|---|
| `NetErr::WouldBlock` | `EAGAIN` · `EWOULDBLOCK` · `EINPROGRESS` | `WSAEWOULDBLOCK` |
| `NetErr::ConnReset` | `ECONNRESET` | `WSAECONNRESET` |
| `NetErr::ConnRefused` | `ECONNREFUSED` | `WSAECONNREFUSED` |
| `NetErr::Timeout` | `ETIMEDOUT` | `WSAETIMEDOUT` |
| `NetErr::AddrInUse` | `EADDRINUSE` | `WSAEADDRINUSE` |
| `NetErr::NotConnected` | `ENOTCONN` | `WSAENOTCONN` |

### C.5 Derleme zamanı sarmalama şeması

| Dosya | İçerik |
|---|---|
| `net_types.h` | Ortak tipler · ortak hata enum'u · platform bağımsız sabitler |
| `net_socket_posix.h/.cpp` | POSIX uygulaması |
| `net_socket_winsock.h/.cpp` | Winsock uygulaması (`Ws2_32.lib` bağlantısı) |
| `net_socket.h` | Fabrika — `#if defined(_WIN32)` ile doğru uygulamayı seçer |

> Bu şema **öneridir** — L0 ağ özelliği kapsam onayı almadan dosya üretilmez
> (Q-A1).

### C.6 Sarmalama test matrisi

| Test | POSIX | Winsock |
|---|---|---|
| Loopback bağlantı + veri | `127.0.0.1` round-trip | Aynı |
| Non-blocking `EAGAIN`/`WSAEWOULDBLOCK` | Tekrar dene akışı | Aynı |
| Kapanış davranışı | `close` sonrası `EPIPE` | `closesocket` sonrası `WSAECONNRESET` |
| Adres çakışması | `EADDRINUSE` | `WSAEADDRINUSE` + `SO_EXCLUSIVEADDRUSE` |
| Uzun boyutlu `send` | Kısmi gönderim döngüsü | `int` sınırı — 2 GB+ tek çağrı yok |

## §D — Taşınabilirlik kontrol listesi

### D.1 Kod denetimi — her satır için

| # | Kontrol | Neden |
|---|---|---|
| 1 | `SOCKET` tipi mi `int` mi? | Winsock 64-bit `SOCKET` — `int` kesme veri kaybı |
| 2 | Geçersiz soket testi `INVALID_SOCKET` mı? | `== -1` Windows'ta yeterli değil |
| 3 | `errno` okuması var mı? | Winsock hataları `WSAGetLastError()`'dedir |
| 4 | `close()` mu `closesocket()` mu? | Soket descriptor'ı platform doğru çağrıyı bekler |
| 5 | `WSAStartup` bir kez çağrılıyor mu? | Çağrılmazsa tüm Winsock çağrıları `WSANOT_INITIALISED` |
| 6 | `SO_REUSEADDR` yanlış mı kullanılıyor? | Windows'ta aktif-sunucu çakışması üretir |
| 7 | `MSG_NOSIGNAL` kullanılıyor mu? | Windows'ta tanımsız — `#ifdef` gerekir |
| 8 | `size_t` / `int` boyut dönüşümü | Winsock `int` boyut — negatif giriş koruması |
| 9 | `FD_SETSIZE` genişletilmiş mi? | Winsock varsayılan 64 — sessiz taşma |
| 10 | Header doğru mu? | `<sys/socket.h>` ↔ `<winsock2.h>` + `<ws2tcpip.h>` |
| 11 | Bağlantı kütüphanesi | Windows: `Ws2_32.lib` |
| 12 | `SOCK_SEQPACKET` kullanılıyor mu? | Windows'ta yok — `SOCK_STREAM` + çerçeveleme |
| 13 | `getaddrinfo` mu eski API mi? | `gethostbyname` IPv4-only ve türetilmiş |
| 14 | Sinyal kurulumu | POSIX `SIGPIPE` yönetimi Windows'ta derleme hatası |
| 15 | Byte order | `htons` her iki platformda zorunlu |

### D.2 Yapılandırıcı (build) denetimi

| Öğe | POSIX | Windows |
|---|---|---|
| Tanım | — | `WIN32_LEAN_AND_MEAN` + `NOMINMAX` ile `<windows.h>` sadeleştirme |
| Include sırası | `<sys/socket.h>` sonrası `<netinet/in.h>` | `<winsock2.h>` **önce** `<windows.h>` (yoksa çakışma) |
| Linker | libc (otomatik) | `Ws2_32.lib` |
| C++ çağırmalar | `noexcept` uyumlu | Winsock C API — `noexcept` sarmalayıcı yazılabilir |

### D.3 Davranışsal denetim — çalışma zamanı

| Test | Beklenen |
|---|---|
| `WSAStartup` sonrası `WSACleanup` dengesi | Temiz kapanış — sızıntı yok |
| Aynı port iki kez bind (Windows) | `SO_EXCLUSIVEADDRUSE` ile reddedilmeli |
| 100+ bağlantı `select` (Windows) | `FD_SETSIZE` aşıldıysa sessiz eksik olay — genişletilmiş boyut kullanılmalı |
| Ağ kesintisi simülasyonu | Her iki platformda da yeniden bağlanma backoff'u çalışır |

### D.4 Dokümantasyon denetimi

| Öğe | Kural |
|---|---|
| `errno` değerlerine sayısal referans | Yasak — yalnız sabit adları kullanılır |
| "Windows'ta `errno`" iddiası | Yanlış — hata okuma yeri `WSAGetLastError()` |
| `SO_REUSEADDR` eşdeğerliği iddiası | Yanlış — §A.5 farkı açıkça belgelenir |
| Overlapped = POSIX async iddiası | Yanlış eşleme — §B.3 modeller farklıdır |
| `close()` ↔ `closesocket()` eşdeğerliği | Koşullu — yalnız soket descriptor'larında; dosya descriptor'larında `close()` doğrudur |
| `errno` → `WSAGetLastError()` otomatik dönüşümü | Yoktur — hata eşleme katmanı elle yazılır (§C.4) |

### D.5 Kaynak matrisi — platform API bağımlılıkları

| Kaynak | POSIX | Winsock2 |
|---|---|---|
| Header grubu | `<sys/socket.h>` · `<netinet/in.h>` · `<arpa/inet.h>` | `<winsock2.h>` · `<ws2tcpip.h>` · `<mstcpip.h>` |
| Kütüphane | libc | `Ws2_32.lib` → `ws2_32.dll` |
| Başlangıç | Yok | `WSAStartup` / `WSACleanup` |
| Temel tip | `int` descriptor | `SOCKET` (`UINT_PTR`) |
| Hata okuma | `errno` | `WSAGetLastError()` |
| Non-blocking | `fcntl(O_NONBLOCK)` | `ioctlsocket(FIONBIO)` |
| Kapanış | `close` | `closesocket` |
| Keep-alive özelleştirme | `setsockopt(TCP_KEEPIDLE)` ailesi | `WSAIoctl` + `tcp_keepalive` |
| Async model | olay demultiplexing (epoll/kqueue) | overlapped + IOCP |

### D.6 Sürüm notları

| Konu | Not |
|---|---|
| Winsock2 | Windows 95'ten beri standart — sürüm karşılaştırması yapısı (`WSAStartup`) |
| `inet_pton` / `inet_ntop` (Winsock) | Vista ve sonrası — eski sistemlerde sembol bulunmaz |
| `SO_EXCLUSIVEADDRUSE` | Windows 2000+ — güvenli bind bayrağı |
| POSIX.1-2024 | Açık Group güncel standardı — `socket` · `getaddrinfo` çekirdeği korur (üst dal `index.md` §4, kaynak 5) |

## §E — Sınır ve kaçış durumları

### E.1 `SO_REUSEADDR` yanlış kullanım senaryosu

| Senaryo | Sonuç | Kaçış |
|---|---|---|
| Windows sunucusu `SO_REUSEADDR` ile bind | İkinci bir süreç aynı port'u alabilir — trafik bölünür | `SO_EXCLUSIVEADDRUSE` kullan |
| POSIX restart sonrası `TIME_WAIT` | `EADDRINUSE` | `SO_REUSEADDR` doğru seçenektir |
| Taşınabilir kod tek bayrak | İki platformda farklı anlam | `#ifdef` ile platform bayrağı |

### E.2 64-bit `SOCKET` kesme senaryosu

| Hata | Sonuç | Kaçış |
|---|---|---|
| `SOCKET` → `int` dönüşümü | Üst 32-bit kesilir — geçersiz descriptor | `SOCKET` tipi korunur · `intptr_t` ara dönüşüm |
| `-1` karşılaştırması | `INVALID_SOCKET` = `~0` (64-bit) — `int` -1'e eşlenmez | `INVALID_SOCKET` sabiti kullanılır |

### E.3 Winsock sürüm uyumsuzluğu

| Durum | Belirti | Kaçış |
|---|---|---|
| `WSAStartup` 1.1 istendi, 1.0 alındı | Geri uyumlu | Dönüş `wVersion` kontrol edilir — eşleşmiyorsa `WSACleanup` + hata |
| `inet_pton` eski XP | Sembol yok | Vista+ hedeflenir; XP için alternatif (bilgi düzeyi — hedeflenmiyor) |

### E.4 `select` FD_SETSIZE sessiz hatası

| Durum | Sonuç |
|---|---|
| Windows'ta 64+ descriptor tek `select` | Taşanlar **sessizce** düşer — olay kaybı |
| Çözüm | `FD_SETSIZE` derleme öncesi genişletilir veya IOCP/`WSAEventSelect`'e geçilir |

### E.5 `EINTR` / `EINTR`-benzeri davranış farkı

| Durum | POSIX | Winsock |
|---|---|---|
| Sinyal çağrıyı böler | `EINTR` — yeniden dene döngüsü | Anlamsız — Windows sinyal modeli |
| `SIGPIPE` | `send` çağrısı öncesinde üretilebilir | Yok — `WSAECONNRESET` döner |
| Kaçış | `EINTR` döngüsü yalnız POSIX bloğunda | `#ifdef` ile sınırlandırılır |

### E.6 POSIX-only API kaçışları

| API | Windows'ta | Taşınabilir alternatif |
|---|---|---|
| `pipe()` | `_pipe()` (CRT) | Ortak kuyruk / dosya |
| `fork()` | Yok | `CreateProcess` |
| `MSG_NOSIGNAL` | Yok | `signal(SIGPIPE, SIG_IGN)` — Windows'ta derleme bloğu |
| `SOCK_SEQPACKET` | Yok | `SOCK_STREAM` + çerçeveleme (`udp-tcp-ve-tls-api.md` §A.4) |
| `epoll` / `kqueue` | Yok | IOCP / `select` |
| `pipe()` temp dosyasız | Yok | Ortak dosya + kilit (son çare) |

### E.7 Repo kanıtı — mevcut durum

| # | Dosya:Satır | Kanıt | Ne gösterir |
|---|---|---|---|
| K1 | `shared/src/AI/ToolCalling.php:256` | `$ch = curl_init($url);` | CoreMusic PHP ağ trafiği `curl` üzerinden — POSIX/Winsock ayrımı kütüphanede gizli |
| K2 | `shared/src/Api/Bff/DesktopBff.php:35` | `'platform' => php_uname('s'),` | Uygulama platformu çalışma zamanında sorar — çalışma zamanı sarmalama örneği |

Doğrulama: `Select-Object -Index (N-1)` — 2026-10-13.

> Repo'da doğrudan soket kodu **yoktur** (`socket_create` · `fsockopen` ·
> `stream_socket_client` isabeti 0 — disk taraması). Karşılaştırma dosyası
> L0 motorunun gelecekteki C++ kodu için hazır referanstır.

### E.8 Kaçış durumu özeti

| Durum | Tepki |
|---|---|
| POSIX-only özellik gerekti | `#ifdef` bloğu + §D.1 kontrol listesinden geçirme |
| Winsock-only özellik gerekti | Karşılığının POSIX'te olup olmadığı §E.6 ile kontrol |
| İkisi de yok | Üst katman sarmalayıcı (S3) — bağımlılık bump kuralı: DUR + el değişimi |
| Emin olunmayan fark | Dosya bu matriste aranır — yoksa `⚠️ VERIFICATION REQUIRED` + kaynak aranır |

### E.9 Karar matrisi — hangi API ailesi

| Durum | Karar | Gerekçe |
|---|---|---|
| Yeni L0 kodu | Önce POSIX imzası + Winsock sarmalayıcı | L0 hedefi henüz onaylanmadı (Q-A1) |
| PHP | `curl` / stream | Repo kanıtı K1 |
| Taşınabilirlik gereksinimi doğarsa | S2 (soyut arayüz) | SOLID — tek sorumluluk: platform ayrımı |

### E.10 Açık konular

| # | Konu | Etiket |
|---|---|---|
| Q-W1 | L0 motorunun Windows hedefi kesin mi? | ⚠️ VERIFICATION REQUIRED |
| Q-W2 | Sarmalama stratejisi S2 mi S3 mü (JUCE/asio)? — sürüm kanıtı yok (AGENTS.md kural 12) | ⚠️ VERIFICATION REQUIRED |

### E.11 Referanslar — bu dosyanın dayandığı resmi sayfalar

| # | Kaynak | Kapsadığı § |
|---|---|---|
| 1 | Microsoft — Windows Sockets start page | §A genel, §B |
| 2 | Microsoft — Windows Sockets Error Codes | §A.3 · §A.6 · §E |
| 3 | Microsoft — `winsock2.h` API listesi | §B.1 · §B.2 |
| 4 | Microsoft — `WSAStartup` / `WSACleanup` | §A.1 · §E.3 |
| 5 | Microsoft — `closesocket` | §A.1 |
| 6 | Microsoft — `ioctlsocket` (`FIONBIO`) | §A.4 |
| 7 | Microsoft — `WSADATA` yapısı | §A.1 |
| 8 | Microsoft — `WSASocket` | §B.2 · §B.6 |
| 9 | Microsoft — `socket` (Winsock) | §B.2 |
| 10 | Microsoft — `select` (Winsock) | §A.8 · §B.5 |
| 11 | Microsoft — `recv` (Winsock) | §A.7 |
| 12 | Microsoft — Mstcpip (keep-alive · `tcp_keepalive`) | §A.11 |

### E.12 Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Winsock2 genel bakış — Windows sockets başlangıç sayfası | https://learn.microsoft.com/en-us/windows/win32/winsock/windows-sockets-start-page-2 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| Windows Sockets Error Codes — `WSAGetLastError` kodları | https://learn.microsoft.com/en-us/windows/win32/winsock/windows-sockets-error-codes-2 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `winsock2.h` — Winsock 2 API referansı | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `WSAStartup` — başlatma ve sürüm pazarlığı | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-wsastartup | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `closesocket` — soket kapatma (`close`'un Winsock karşılığı) | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-closesocket | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `ioctlsocket` — `FIONBIO` non-blocking kurulumu | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-ioctlsocket | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `WSADATA` yapısı — `WSAStartup` dönüş verisi | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/ns-winsock2-wsadata | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `WSASocket` — extended socket oluşturma, `WSA_FLAG_OVERLAPPED` | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-wsasocketa | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `socket` (Winsock) — `SOCKET` tipi, `INVALID_SOCKET` | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-socket | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `select` (Winsock) — `fd_set`, ilk parametre farkı | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-select | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `recv` (Winsock) — `int` boyut, `MSG_PEEK` | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-recv | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| Mstcpip — `tcp_keepalive` yapısı · keep-alive özelleştirme | https://learn.microsoft.com/en-us/windows/win32/api/mstcpip/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
