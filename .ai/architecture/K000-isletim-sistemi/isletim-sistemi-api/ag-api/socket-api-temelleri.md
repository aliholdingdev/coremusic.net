---
node_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api/socket-api-temelleri
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/socket.2.html
  - https://man7.org/linux/man-pages/man2/bind.2.html
  - https://man7.org/linux/man-pages/man2/listen.2.html
  - https://man7.org/linux/man-pages/man2/accept.2.html
  - https://man7.org/linux/man-pages/man2/connect.2.html
  - https://man7.org/linux/man-pages/man3/getaddrinfo.3.html
  - https://man7.org/linux/man-pages/man7/unix.7.html
  - https://man7.org/linux/man-pages/man2/close.2.html
  - https://man7.org/linux/man-pages/man2/shutdown.2.html
  - https://man7.org/linux/man-pages/man2/getsockopt.2.html
validation: "2026-10-14 — 4. tur: §F gruplu kod örnekleri (POSIX C · Winsock2 C · PHP stream) + 3 yeni URL (php.net stream_socket_server/client/accept) HEAD-200 · envanter kesişimi 0 · 2026-10-13: 10 resmi URL HEAD 200"
open_questions: []
group: mimari
---

# socket-api-temelleri

**ELI15:** Bir program ağ ile konuşmak için önce bir **soket** (iletişim ucu) açar,
bu ucu bir **adresle** bağlar, karşı tarafı bekler veya bağlanır, sonra veri
gönderip alır. Bu dosya bu dört adımın POSIX API'sini ve hata modlarını belgeler.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api/socket-api-temelleri |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §A — Adresleme ailesi (AF_INET · AF_INET6 · AF_UNIX)

### A.1 Adresleme ailesi nedir

`socket()` çağrısının ilk parametresi **adresleme ailesi**dir (address family):
soketin hangi adres tipiyle çalışacağını belirler. POSIX'te `AF_` öneki, aynı
sabiti oluşturma tarafında `PF_` (protocol family) önekiyle de kullanılır;
`AF_UNIX` ve `PF_UNIX` değerleri aynıdır.

> "AF_* and PF_* values are identical" — Linux `socket(2)` sayfası.

### A.2 Yaygın adresleme aileleri

| Aile | Anlamı | Adres struct'ı | Tipik kullanım |
|---|---|---|---|
| `AF_INET` | IPv4 Internet adresleri | `struct sockaddr_in` | Web, DNS, API istemcisi (127.0.0.1 · 10.x · 192.168.x) |
| `AF_INET6` | IPv6 Internet adresleri | `struct sockaddr_in6` | Yeni dağıtımlar · çift yığın (dual-stack) sunucular |
| `AF_UNIX` | Unix domain soketleri (aynı makine) | `struct sockaddr_un` | Veritabanı sunucusu yerel bağlantı, X11, systemd |
| `AF_NETLINK` | Çekirdek ile kullanıcı uzayı mesajlaşması | `struct sockaddr_nl` | Linux'a özel — yönlendirme tablosu okuma (kapsam dışı) |
| `AF_UNSPEC` | Belirsiz / herhangi | — | `getaddrinfo()` çözümlemesinde "aile seçimi serbest" bayrağı |

### A.3 IPv4 adres yapısı (`sockaddr_in`)

| Alan | Tip | Anlam |
|---|---|---|
| `sin_family` | `sa_family_t` | `AF_INET` |
| `sin_port` | `in_port_t` | **Network byte order** (big-endian) port numarası — `htons()` ile çevrilir |
| `sin_addr` | `struct in_addr` | 32-bit IPv4 adresi — `inet_pton(AF_INET, "127.0.0.1", &addr)` ile doldurulur |
| `sin_zero` | `char[8]` | Dolgu — `memset` ile sıfırlanır |

### A.4 IPv6 adres yapısı (`sockaddr_in6`)

| Alan | Tip | Anlam |
|---|---|---|
| `sin6_family` | `sa_family_t` | `AF_INET6` |
| `sin6_port` | `in_port_t` | Port — `htons()` gerekli |
| `sin6_flowinfo` | `uint32_t` | Akış etiketi / öncelik bilgisi — çoğu uygulamada 0 |
| `sin6_addr` | `struct in6_addr` | 128-bit IPv6 adresi |
| `sin6_scope_id` | `uint32_t` | Link-local (`fe80::`) adreslerde arayüz indeksi |

### A.5 IPv4 ↔ IPv6 adres yapısı farkları

| Kriter | `sockaddr_in` (IPv4) | `sockaddr_in6` (IPv6) |
|---|---|---|
| Boyut | 16 bayt | 28 bayt |
| Adres genişliği | 32 bit | 128 bit |
| Dolgu alanı | `sin_zero[8]` | Yok — `flowinfo` + `scope_id` tarafından doldurulur |
| Adres çeviricisi | `inet_pton(AF_INET, ...)` | `inet_pton(AF_INET6, ...)` |
| Ters çeviri | `inet_ntop` | `inet_ntop` (aynı fonksiyon, farklı aile parametresi) |
| `AF_UNSPEC` eşleşmesi | Hayır | Hayır — `getaddrinfo` hangi aileyi döndüreceğine o karar verir |

### A.6 `AF_UNIX` — aynı makine içi soketler

`AF_UNIX` soketleri IP ağında değil, çekirdeğin yerel veri yolunda çalışır;
ikisi de aynı makinedeki süreçler arasında **dosya sistemi yolu** veya **isimsiz
(anonim) eşleme** ile bağlanır.

| Alt tür | Adres biçimi | Not |
|---|---|---|
| `SOCK_STREAM` UNIX soketi | Dosya yolu (`sun_path`) | TCP benzeri güvenilir akış — veritabanı yerel bağlantıları |
| `SOCK_DGRAM` UNIX soketi | Dosya yolu (`sun_path`) | UDP benzeri dagram — sınırlı, mesaj sınırları korunur |
| İkili (çift yönlü) soket | Dosya yolu yok — `socketpair()` | Ebeveyn-çocuk süreç kanalı |

| Kural | Değer |
|---|---|
| `sun_path` uzunluğu | `sizeof(sun_path)` ile sınırlı (Linux'ta 108 bayt) — taşma `bind()`'i `EINVAL` ile başarısız kılar |
| Bağlantı reddi | Sunucu yolu bind etmeden önce mevcutsa `bind()` → `EADDRINUSE` |
| Yetki | Dizin yazma yetkisi + (istenen durumda) soket dosyası üzerindeki mod bitleri bağlama yetkisini belirler |
| Dosya temizliği | Çöküş sonrası bırakılan soket dosyası yeniden `bind()`'i engeller — sunucu açılışta eski yolu silmelidir |

### A.7 Adres çeviri yardımcıları

| Yardımcı | Görev | Not |
|---|---|---|
| `inet_pton(af, src, dst)` | Metin → ikili adres | `AF_INET` ve `AF_INET6`; dönüş değeri 1 = başarı |
| `inet_ntop(af, src, dst, size)` | İkili → metin adres | `INET6_ADDRSTRLEN` (46) tampon yeterli |
| `getaddrinfo(host, service, hints, res)` | İsim + servis → bağlanılabilir adres listesi | `sockaddr_storage` içinde döner; `freeaddrinfo()` ile serbest bırakılır |
| `getnameinfo(addr, len, host, ...)` | Adres → metin (ters çözümleme) | `NI_NUMERICHOST` sayısal çıktı zorlar |
| `htons` / `htonl` | 16/32-bit host → network byte order | Port alanları için zorunlu |

### A.8 `getaddrinfo` — çağdaş adres çözümleme

Eski `gethostbyname()` IPv4'e kilitlidir ve yeniden girilemez değildir;
`getaddrinfo()` hem IPv4 hem IPv6'yı tek çağrıda çözer, `AF_UNSPEC` ile
aileyi serbest bırakır.

| `hints` alanı | Anlam |
|---|---|
| `ai_family` | `AF_UNSPEC` (her ikisi) · `AF_INET` · `AF_INET6` · `AF_UNIX` |
| `ai_socktype` | `SOCK_STREAM` (TCP · UNIX stream) veya `SOCK_DGRAM` (UDP · UNIX dgram) |
| `ai_protocol` | `IPPROTO_TCP` · `IPPROTO_UDP` — `ai_socktype` genelde yeterli |
| `ai_flags` | `AI_PASSIVE` (sunucu: NULL host → INADDR_ANY) · `AI_NUMERICSERV` (servis sayısal) · `AI_ADDRCONFIG` (sistemsel yapılandırma filtresi) |

Çıktı **bağlı liste** halindedir: her `struct addrinfo` bir sonrakine `ai_next`
ile bağlanır; ilk bağlantı denemesi başarısız olursa sıradakine geçilir ve
çözümleme sonunda `freeaddrinfo()` çağrılmalıdır.

### A.9 Adresleme hata modları

| Hata | Koşul | Tepki |
|---|---|---|
| `EAFNOSUPPORT` | Desteklenmeyen adres ailesi | `hints->ai_family` sınırlandırılır |
| `EAI_NONAME` | İsim çözümlenemedi | DNS yapılandırması / yazım denetimi |
| `EADDRNOTAVAIL` | Adres bu arayüzde mevcut değil | `bind()` hedefi arayüz adresiyle karşılaştırılır |
| `EINVAL` | `sockaddr` uzunluğu / `sun_path` taşması | Yapı uzunluğu `bind()` çağrısına tam aktarılır |

### A.10 Karar — adres ailesi seçimi (CoreMusic bağlamı)

| Senaryo | Seçim | Gerekçe |
|---|---|---|
| Yerel sunucu (loopback) | `AF_INET` + `127.0.0.1` | Basitlik; sartname ağ katmanını IPv6 zorunlu kılmıyor (Q-A2) |
| Genel dinleme | `AF_UNSPEC` + `AI_PASSIVE` | IPv4/IPv6 ikisini de kapsar |
| Aynı makine IPC | `AF_UNIX` | Ağ yığınını atlar — gecikme avantajı |

> Not: `AF_UNIX` seçimi L0 motorunun (Neva Engine) yerel kontrol kanalı için
> adaydır; hâlâ kapsam onayı gerekiyor (Q-A1 — `ag-api/index.md` §5).

## §B — Soket oluşturma ve bağlama (socket · bind · listen · accept)

### B.1 Yaşam döngüsü — sunucu tarafı

| # | Çağrı | Ne yapar | Başarı değeri |
|---|---|---|---|
| 1 | `socket(af, type, proto)` | Yeni soket (descriptor) oluşturur | Geçerli descriptor (≥ 0) |
| 2 | `bind(fd, addr, len)` | Sokağı bir adrese/port'a iliştirir | 0 |
| 3 | `listen(fd, backlog)` | Bağlantı isteklerini kuyruğa alır | 0 |
| 4 | `accept(fd, addr, len)` | Kuyruktan bir bağlantıyı ayırır — **yeni** descriptor döner | Yeni descriptor |
| 5 | `send` / `recv` | Veri aktarımı | Aktarılan bayt sayısı |
| 6 | `close(fd)` | Descriptor'ı kapatır | 0 |

### B.2 Yaşam döngüsü — istemci tarafı

| # | Çağrı | Ne yapar | Başarı değeri |
|---|---|---|---|
| 1 | `socket(af, type, proto)` | Soket oluşturur | Geçerli descriptor |
| 2 | `connect(fd, addr, len)` | Sunucuya bağlanır (TCP'de üç-way handshake'i tetikler) | 0 |
| 3 | `send` / `recv` | Veri aktarımı | Aktarılan bayt sayısı |
| 4 | `close(fd)` | Kapatır | 0 |

### B.3 `socket()` parametreleri

| Parametre | Değerler | Not |
|---|---|---|
| `domain` | `AF_INET` · `AF_INET6` · `AF_UNIX` | §A tablolarına bakılır |
| `type` | `SOCK_STREAM` · `SOCK_DGRAM` · `SOCK_RAW` | `SOCK_RAW` CAP_NET_RAW gerektirir — kullanıcı uygulaması kapsamı dışı |
| `protocol` | `IPPROTO_TCP` · `IPPROTO_UDP` · 0 | 0 = `type` için varsayılan |
| Bayrak | `SOCK_NONBLOCK` · `SOCK_CLOEXEC` | Linux — `fcntl` ile de kurulabilir; `CLOEXEC` descriptor'ın `execve` sırasında sızmasını engeller |

### B.4 `bind()` — adres iliştirme

| Konu | Kural |
|---|---|
| Adres | `AI_PASSIVE` + NULL host verildiğinde tüm arayüzlere (`INADDR_ANY` / `in6addr_any`) bağlanır |
| Port 0 | Çekirdek **boş bir port** atar — test ve dinamik sunucular için |
| Düşük port (< 1024) | POSIX'te root/privileged süreç gerektirir |
| `SO_REUSEADDR` | Yeniden bağlanma (restart) sırasında `TIME_WAIT` kaynaklı `EADDRINUSE`'i önler |
| `SO_REUSEPORT` | Aynı port'ta birden çok çekirdek-dengeli dinleyici — Linux/Windows Vista+ |

### B.5 `listen()` — kuyruk

| Parametre | Anlam |
|---|---|
| `fd` | `bind()` sonrası dinleyici descriptor |
| `backlog` | Kuyruğa alınabilenniç bağlantı sayısı — taşan istemciler `ECONNRESET` ile reddedilebilir |

| `backlog` değeri | Davranış |
|---|---|
| 0 | Sistem varsayılanı uygulanır (Linux'ta `somaxconn` sınırı içindedir) |
| Küçük (ör. 5) | Kısa trafik patlamasında bağlantı kaybı riski |
| Büyük (ör. 128–4096) | Yoğun eşzamanlı bağlantı kabulü — sistem `somaxconn` ile kırpar |

> `listen()` çağrısı bağlantıyı kabul etmez; yalnızca kuyruğu kurar. Kabul
> `accept()` ile ayrı bir descriptor üzerinden yapılır.

### B.6 `accept()` — bağlantı kabulü

| Konu | Kural |
|---|---|
| Dönüş değeri | **Yeni** descriptor — dinleyici descriptor `fd` ayrı kalır ve yeniden `accept()`'e hazır |
| `addr` parametresi | İstemci adresi doldurulur; NULL verilirse yok sayılır |
| Bloklama | Varsayılan olarak bağlantı gelene kadar bloklanır — non-blocking'de `EAGAIN` döner |
| `EINTR` | Sinyal ile bölünürse çağrı yenilenir (otomatik yenileme POSIX'te garanti edilmez) |
| Hata descriptor'u | `-1` — `errno` nedeni taşır |

### B.7 `connect()` — istemci bağlanması

| Konu | Kural |
|---|---|
| TCP | Üç-way handshake tamamlanana kadar bloklanır (başarısızlıkta `ECONNREFUSED` · `ETIMEDOUT`) |
| UDP | Adres "iliştirilir" — paket gönderilmez; sonraki `send()` bu adrese gider |
| Non-blocking | `EINPROGRESS` döner; tamamlanma `poll`/`epoll`/`select` ile `POLLOUT` üzerinden izlenir |
| Yeniden bağlanma | Aynı soket `connect()` ile ikinci kez bağlanamaz — yeni `socket()` gerekir |

### B.8 Sunucu akış diyagramı (metin)

```text
Sunucu                                   İstemci
------                                   ------
socket(AF_INET, SOCK_STREAM, 0)          socket(AF_INET, SOCK_STREAM, 0)
bind(fd, 0.0.0.0:8080)
listen(fd, 128)                                                       |
                                         connect(fd, 203.0.113.5:8080)
cfd = accept(fd, ...)  <-----------------------------------------------+
recv(cfd, buf)        <-------------------- send(cfd, "GET ...")
send(cfd, "HTTP/1.1") -------------------->
close(cfd)                                                     close(fd)
close(fd)
```

### B.9 Bağlantı kurulumu hata modları

| Hata | Anlamı | Tipik neden |
|---|---|---|
| `EADDRINUSE` | Adres zaten kullanımda | Port açık · `TIME_WAIT` · `SO_REUSEADDR` eksik |
| `EACCES` | Yetki yok | Düşük port root'suz bağlandı |
| `ECONNREFUSED` | Sunucu reddetti | Dinleyici yok · firewall · servis kapalı |
| `ETIMEDOUT` | Zaman aşımı | Paketler yol boyunca kayboldu · hedef yok |
| `ENETUNREACH` | Ağ yok | Routing tablosu · arayüz kapalı |
| `EHOSTUNREACH` | Host yok | Gateway ARP/NDP çözümleyemedi |

### B.10 Port durumu tablosu — `bind()` sonrası

| Durum | `bind()` sonucu | `SO_REUSEADDR` etkisi |
|---|---|---|
| Port tamamen boş | Başarı | — |
| `TIME_WAIT` (eski bağlantı) | `EADDRINUSE` (bazı sistemlerde izin) | Genellikle bağlamayı mümkün kılar |
| Aktif dinleyici aynı süreçte | `EADDRINUSE` | Engellemez — aynı port iki kez dinlenemez |
| Aktif dinleyici başka süreçte | `EADDRINUSE` | Engellemez — yetki çatışması |

## §C — Veri aktarımı (send/recv · write/read)

### C.1 İki çağrı ailesi arasındaki fark

| Kriter | `send` / `recv` | `write` / `read` |
|---|---|---|
| İmza | Bayrak (`flags`) parametresi taşır | Taşımaz |
| Geçerli descriptor | Yalnız bağlantılı soketler | Her descriptor (soket, dosya, pipe) |
| `MSG_NOSIGNAL` | `send` ile kullanılabilir (Linux) | Kullanılamaz — `SIGPIPE` riski tampon |
| Öneri | Soket trafiğinde `send`/`recv` | Tanıdık API isteyen kod için `write`/`read` |

### C.2 `send()` parametreleri ve dönüş değeri

| Parametre | Anlam |
|---|---|
| `fd` | Bağlı soket (bağlantısız `AF_UNIX SOCK_DGRAM` kabul edilebilir) |
| `buf` / `len` | Gönderilecek baytlar |
| `flags` | `MSG_DONTWAIT` · `MSG_NOSIGNAL` · `MSG_CONFIRM` (Linux) |
| Dönüş | Gönderilen bayt sayısı — `len`'den **az** olabilir (kısmi gönderim) |

### C.3 `recv()` dönüş değerleri tablosu

| Dönüş | Anlam |
|---|---|
| > 0 | Okunan bayt sayısı |
| 0 | Karşı taraf **düzenli kapanış** yaptı (TCP FIN — `shutdown`/`close`) |
| -1 + `EAGAIN` | Non-blocking'de veri yok — tekrar denenir |
| -1 + `EINTR` | Sinyal ile bölündü — yeniden denenir |
| -1 + `ECONNRESET` | Karşı taraf anormal kapattı (RST paketi geldi) |

### C.4 Kısmi okuma ve kısmi yazma

TCP **akış** (stream) protokolüdür: `send(1000)` çağrısı 1000 bayt göndermeyi
garanti etmez — çekirdek az gönderip kalanı sonraki çağrıya bırakabilir. Aynı
şekilde `recv(1000)` istenenin daha azını döndürebilir. UDP'de ise `recv`
**tek bir dagramı** alır; istenenden uzun dagram kesilir (taşma) ve fazlası
atılır.

| Protokol | Kısmi aktarım | Uygulama tepkisi |
|---|---|---|
| TCP | Yaygın | Döngü ile kalan baytlar tamamlanır |
| UDP | Gönderimde hayır (tek syscall tüm dagramı iletir) | Alımda taşma sessiz — tampon `dgram` boyutundan büyük yapılır |
| `AF_UNIX` stream | TCP gibi | Döngü gerekir |
| `AF_UNIX` dagram | UDP gibi | Mesaj sınırları korunur |

### C.5 `flags` bayrakları tablosu

| Bayrak | Geçerli | Etki |
|---|---|---|
| `MSG_DONTWAIT` | send/recv | Tek çağrı için non-blocking davranışı |
| `MSG_NOSIGNAL` | send (Linux) | Karşı taraf kapanmışsa `SIGPIPE` üretmez |
| `MSG_OOB` | send/recv (TCP) | Acil (out-of-band) veri — pratikte neredeyse kullanılmaz |
| `MSG_PEEK` | recv | Veriyi okur ama kuyruktan çıkarmaz |
| `MSG_WAITALL` | recv | İstenen baytların tamamı gelene kadar bloklar (kesinti hariç) |
| `MSG_TRUNC` | recv (dgram) | Taşan dagramda gerçek boyut bildirilir (Linux) |

### C.6 `send`/`recv` hata modları

| Hata | Çağrı | Anlam | Tepki |
|---|---|---|---|
| `EPIPE` | send | Karşı taraf kapalı (SIGPIPE ile birlikte) | Bağlantı yeniden kurulur · `MSG_NOSIGNAL` ile çökme engellenir |
| `ECONNRESET` | send/recv | RST alındı | Bağlantı sona erdi — uygulama seviyesinde yeniden bağlanma |
| `EAGAIN` | send/recv | Non-blocking'de hazır veri yok | `poll`/`epoll` ile beklenir |
| `EINTR` | send/recv | Sinyal kesintisi | Çağrı yenilenir |
| `ENOTCONN` | send/recv | Soket bağlı değil | `connect()` kontrol edilir |
| `EMSGSIZE` | send (dgram) | Dagram boyut sınırı aşıldı | Boyut küçültülür |

### C.7 `write`/`read` ile eşdeğerlik

| Durum | `write` davranışı | `send` karşılığı |
|---|---|---|
| Bağlı TCP soketi | Veriyi akışa yazar | `send(fd, ..., 0)` |
| Dosya descriptor'ı | Yazma | `send` geçersiz — dosyalarda `write` kullanılır |
| `SIGPIPE` | Üretilebilir | `MSG_NOSIGNAL` bastırılabilir |
| Non-blocking | `fcntl(F_SETFL, O_NONBLOCK)` gerekli | `MSG_DONTWAIT` tek çağrıya özgü |

### C.8 Karar — hangi çağrı ailesi

| Senaryo | Seçim | Gerekçe |
|---|---|---|
| CoreMusic PHP katmanı | `curl` / stream | Repo kanıtı — `ToolCalling.php:256` · `ServiceRegistry.php:102` |
| L0 C++ motoru (ileride) | `send`/`recv` + `MSG_NOSIGNAL` | Bayrak kontrolü · sinyal sürprizi yok |
| POSIX ↔ Windows taşınabilirlik | `send`/`recv` | Winsock2 birebir aynı imzayı sunar (bkz. `posix-socket-ve-winsock-karsilastirmasi.md`) |

### C.9 Ölçüm notları — aktarım davranışı

| Ölçüm | Yöntem | Not |
|---|---|---|
| Kısmi yazma sıklığı | `send` dönüşü < `len` sayımı | Küçük `len` değerlerinde nadir |
| `EAGAIN` oranı | Non-blocking sayaçları | Tampon yönetimi kararı için girdi |
| Zaman aşımı | `recv` bekleme süresi | `SO_RCVTIMEO` / bayrak ile sınırlandırılır |

## §D — Descriptor yaşam döngüsü

### D.1 Descriptor nedir

Soket (ve dosya, pipe) erişimi işletim sisteminin atadığı **tamsayı descriptor**
ile yapılır. POSIX'te 0 = stdin, 1 = stdout, 2 = stderr'dir; yeni descriptor'lar
en küçük boş numaradan atanır. Windows'ta bu model `SOCKET` tipi ile farklılaşır
(`posix-socket-ve-winsock-karsilastirmasi.md` §A).

### D.2 `close()` davranışı

| Konu | Kural |
|---|---|
| Referans sayımı | Descriptor üzerinde `dup()` ile çoğaltma varsa yalnız bir `close()` çağrısı kaydı düşürür |
| Son kapanış | Son referans kapatıldığında çekirdek soketi gerçekten kapatır |
| Veri akışı | Alınmamış kuyruk verileri atılır (gönderim tarafı RST tetiklenebilir) |
| `EINTR` | Bazı sistemlerde `close()` `EINTR` ile bölünebilir ve descriptor durumu belirsizdir (Linux'ta kapatılmıştır) |
| Hata | `EBADF` — geçersiz/önceden kapatılmış descriptor |

### D.3 `shutdown()` — yönsel kapanış

| `how` değeri | Etki |
|---|---|
| `SHUT_RD` | Okuma yönü kapatılır — sonraki `recv` 0 döner |
| `SHUT_WR` | Yazma yönü kapatılır — FIN gönderilir; okuma hâlâ mümkündür (yarı-kapanış) |
| `SHUT_RD` + `SHUT_WR` | `close()`'a yakın; ancak descriptor açık kalır |

`close()` ile farkı: `shutdown` **yönsel** ve anlıktır; descriptor hâlâ
geçerlidir, karşı tarafın verisi okunmaya devam edilebilir — HTTP sunucularının
"istek alındı, yanıtı gönder, FIN gönder, yanıtı bekle" kalıbının temeli.

### D.4 `SO_LINGER` parametresi

| `l_onoff` | `l_linger` | Davranış |
|---|---|---|
| 0 | — | `close()` hemen döner; çekirdek gönderim kuyruğunu arka planda bitirir |
| 1 | 0 | Anlık kapanış: kuyruk atılır, RST gönderilir |
| 1 | N saniye | `close()` en fazla N saniye bloklanır — veri gönderildikten sonra FIN |

### D.5 TCP kapanış durumları

| Durum | Ne oldu | Uygulama etkisi |
|---|---|---|
| Yarı-kapanış (`SHUT_WR`) | FIN gönderildi, okuma açık | Protokol sonu sinyali |
| Karşılıklı kapanış | Her iki yönde FIN/ACK tamam | Temiz kapanış |
| `TIME_WAIT` | Aktif kapanan taraf 2MSL bekler | Yeniden başlatmada `EADDRINUSE` — `SO_REUSEADDR` |
| `ECONNRESET` | RST alındı | Anormal kapanış — `recv` -1 döner |
| Yarım bağlantı | FIN gönderildi, ACK gelmedi | Yeniden gönderilir, sonra süre aşımı |

### D.6 `dup` / `dup2` — descriptor çoğaltma

| Çağrı | Kullanım |
|---|---|
| `dup(fd)` | En küçük boş numarayla kopya — referans sayısını artırır |
| `dup2(old, new)` | `new` numarasına kopyalar (gerekirse kapatılır) — stdout'u dosyaya yönlendirme (`exec` öncesi klasik kalıp) |

> Soket descriptor'larında pratik kullanımı nadirdir; asıl alanı `exec` sonrası
> dosya yönlendirme ve paylaşılan descriptor tabanlı protokollerdir.

### D.7 Kaynak sızıntısı denetimi

| Sızıntı biçimi | Belirti | Önlem |
|---|---|---|
| `close()` unutulması | Artan descriptor sayısı → `EMFILE` | RAII sarmalayıcı (C++) · `finally` (PHP) |
| `accept` döngüsü hata yolu | Hata dalında `cfd` kapatılmıyor | Hata yolu kontrol listesi |
| Fork sonrası iki kopya | Ebeveyn/çocuk aynı descriptor'ı açık tutar | `CLOEXEC` · fork sonrası kapatma |
| `freeaddrinfo` unutulması | Bellek sızıntısı (descriptor değil) | `getaddrinfo` sonucu her yolda serbest bırakılır |

### D.8 Kaynak sınırı tabloları

| Sınır | Anlam | Ayar yeri (Linux) |
|---|---|---|
| `RLIMIT_NOFILE` | Süreç başına açık descriptor üst sınırı | `ulimit -n` · `setrlimit` |
| `somaxconn` | `listen` kuyruk üst sınırı | `/proc/sys/net/core/somaxconn` |
| `fs.file-max` | Sistem geneli dosya/soket üst sınırı | `/proc/sys/fs/file-max` |

### D.9 C++ sarmalama kuralı (L0 motoru için)

| Kural | Gerekçe |
|---|---|
| Descriptor sarmalayıcı sınıf — tek sahiplik | Kural: RAII, `unique_ptr` benzeri desen; çifte `close()` yasak |
| `noexcept` yıkıcı | Yıkıcıda `close()` çağrısı hata fırlatmaz |
| Ses callback'inde ağ çağrısı yasak | Ağ I/O bloklanabilir — RT audio thread kuralı (AGENTS.md §L0) |
| Ağ işlemleri ayrı thread | RT thread yalnız lock-free kuyruk üzerinden haberleşir |

## §E — Hata modları (ECONNRESET · EPIPE · EAGAIN)

### E.1 `ECONNRESET` — bağlantı sıfırlandı

| Konu | Ayrıntı |
|---|---|
| Anlamı | Karşı taraf soketi **anormal** kapattı — çekirdek RST gönderdi |
| `recv` tarafı | `-1` dönüş + `errno = ECONNRESET` |
| `send` tarafı | Önce `EPIPE`, ardından bağlantı kapanır |
| Tipik nedenler | Süreç çökmesi · `SO_LINGER(0)` ile kapanış · firewall reset · alt sistem yeniden başlatma |
| Uygulama tepkisi | Bağlantı ölü sayılır; üst katman (TLS/HTTP) yeniden bağlanma kararı verir |

### E.2 `EPIPE` — boru kırıldı (send)

| Konu | Ayrıntı |
|---|---|
| Anlamı | Karşı tarafın okuma ucunu kapattığı biliniyor; gönderim imkânsız |
| Eşzamanlı sinyal | POSIX `send`/`write` çağrısı **öncesinde** `SIGPIPE` üretir — varsayılan davranış süreci öldürür |
| Önlem 1 | `signal(SIGPIPE, SIG_IGN)` — sinyal yok sayılır, hata `EPIPE` olarak okunur |
| Önlem 2 | `MSG_NOSIGNAL` bayrağı ile `send` çağrısı (Linux) |
| Windows karşılığı | `SIGPIPE` yoktur — `WSAECONNRESET` / `WSAECONNABORTED` dönmesi beklenir |

### E.3 `EAGAIN` / `EWOULDBLOCK` — tekrar dene

| Konu | Ayrıntı |
|---|---|
| Anlamı | Non-blocking sokette şu an ilerleme imkânsız; **hata değil** akış kontrolü |
| Eşdeğerlik | POSIX'te farklı sabitler, çoğu sistemde aynı değer |
| `recv` | Veri yok — beklemek gerekir |
| `send` | Gönderim tamponu dolu — alıcı yavaş |
| Doğru tepki | `poll` / `epoll` / `select` ile hazır olma olayı beklenir; **sabo döngüsü** (sürekli yeniden deneme) yasak |

### E.4 Diğer önemli hata kodları

| `errno` | Anlam | Sınıf |
|---|---|---|
| `EINTR` | Sinyal kesintisi | Yeniden dene |
| `EINPROGRESS` | Non-blocking `connect` başladı | Olay beklemesi |
| `ETIMEDOUT` | Bağlantı/gönderim zaman aşımı | Yeniden bağlan |
| `ECONNREFUSED` | Sunucu reddetti | Yapılandırma / servis kontrolü |
| `ENOTCONN` | Soket bağlı değil | Programlama hatası |
| `EMSGSIZE` | Dagram boyutu aşıldı | Gönderim boyutu küçültülür |
| `EADDRINUSE` | Adres kullanımda | `SO_REUSEADDR` / port seçimi |
| `ENOBUFS` / `ENOMEM` | Tampon yetersiz | Yük / sistem kaynağı |

### E.5 Hata → tepki matrisi (uygulama katmanı)

| Hata | Kaldırılabilir mi? | Tepki | Üst katman kararı |
|---|---|---|---|
| `EAGAIN` | Evet — beklenir | Olay demultiplexing | Yok — akış normal |
| `EINTR` | Evet — yeniden dene | Çağrı tekrarı | Yok |
| `ECONNRESET` | Hayır | Bağlantı sona erdi | Yeniden bağlanma politikası |
| `EPIPE` | Hayır | Gönderim imkânsız | Yeniden bağlanma + sinyal önlemleri |
| `ETIMEDOUT` | Koşullu | Zaman aşımı | Üstel bekleme (exponential backoff) |
| `ECONNREFUSED` | Koşullu | Sunucu yok | Servis keşfi / fallback |
| `EMFILE` | Hayır (anlık) | Yeni bağlantı kabul edilemez | Yeniden bağlanma kabulünü geciktir · sızıntı giderilir |

### E.6 Yeniden bağlanma prensipleri

| Prensip | Açıklama |
|---|---|
| Üstel geri çekilme (backoff) | 1 sn → 2 sn → 4 sn … üstel artar · üst sınır (ör. 60 sn) |
| Rastgelelik (jitter) | Çoklu istemcinin senkron "thundering herd" olmasını önler |
| Sınırsız deneme | Kötü fikir — üst katman (kullanıcıya/monitor'a) hata taşımalı |
| Yeniden bağlanma maliyeti | TLS yeniden el sıkışması RTT maliyeti taşır (bkz. `udp-tcp-ve-tls-api.md` §D) |

### E.7 Repo kanıtları — PHP katmanında hata ve ağ davranışı

| # | Dosya:Satır | Kanıt satırı | Ne gösterir |
|---|---|---|---|
| K1 | `shared/src/AI/ToolCalling.php:256` | `$ch = curl_init($url);` | Ağ erişimi `curl` sarmalayıcısı üzerinden — soket hataları `curl` hatasına dönüşür |
| K2 | `shared/src/Api/Registry/ServiceRegistry.php:102` | `$context = stream_context_create([` | PHP stream katmanı — soket üstünde TLS/HTTP ayarları |
| K3 | `shared/src/OAuth/Provider/BaseOAuthProvider.php:76` | `$ch = curl_init();` | OAuth akışı da `curl` üzerinden ağ trafiği yürütür |

Doğrulama yöntemi: `Select-Object -Index (N-1)` ile ilgili satır içerikleri
okunarak teyit edildi (2026-10-13).

> PHP `curl`/stream sarmalayıcıları POSIX'te `libc` soket çağrılarına, Windows'ta
> Winsock2'ye iner — doğrudan `socket()` çağrısı repo PHP kodunda **yoktur**
> (disk taraması: `fsockopen` · `stream_socket_client` · `socket_create` isabeti 0).

### E.8 Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `socket(2)` — soket oluşturma, AF_ / PF_ eşdeğerliği, `SOCK_NONBLOCK` / `SOCK_CLOEXEC` | https://man7.org/linux/man-pages/man2/socket.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `bind(2)` — adres iliştirme, port 0 davranışı | https://man7.org/linux/man-pages/man2/bind.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `listen(2)` — backlog kuyruğu | https://man7.org/linux/man-pages/man2/listen.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `accept(2)` — yeni descriptor, `EINTR`, `EAGAIN` | https://man7.org/linux/man-pages/man2/accept.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `connect(2)` — `EINPROGRESS`, non-blocking bağlanma | https://man7.org/linux/man-pages/man2/connect.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `getaddrinfo(3)` — `AI_PASSIVE`, `sockaddr_storage`, `freeaddrinfo` | https://man7.org/linux/man-pages/man3/getaddrinfo.3.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `unix(7)` — AF_UNIX soket türleri, `sun_path` sınırı | https://man7.org/linux/man-pages/man7/unix.7.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `close(2)` — descriptor kapatma, referans sayımı, `EBADF` | https://man7.org/linux/man-pages/man2/close.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `shutdown(2)` — yönsel kapanış, `SHUT_WR` yarıkapanış | https://man7.org/linux/man-pages/man2/shutdown.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `getsockopt(2)` — `SO_REUSEADDR`, `SO_LINGER`, `SO_RCVTIMEO` soket seçenekleri | https://man7.org/linux/man-pages/man2/getsockopt.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |

---

## §F — Gruplu kod örnekleri: bind → listen → accept (2026-10-14)

§B'deki sunucu sıra-zincirini grup başına gösterir. Bloklar derlenebilirlik
iddiası TAŞIMAZ; her bloğun kaynağı §F.6 tablosudur. "Sadece Linux" kuralı:
POSIX + Windows + PHP — macOS/Android durumu §F.4'te dürüstlükle kayıtlıdır.

### F.1 Linux/POSIX grubu (C)

```c
#include <sys/socket.h>
#include <netdb.h>
#include <unistd.h>
#include <stdio.h>

struct addrinfo hints = {0}, *res = NULL;
hints.ai_family   = AF_UNSPEC;      /* IPv4/IPv6 seçimi çekirdeğe bırakılır */
hints.ai_socktype = SOCK_STREAM;
hints.ai_flags    = AI_PASSIVE;     /* sunucu tarafı pasif adres — §A.8 */

int gai = getaddrinfo(NULL, "8080", &hints, &res);
if (gai != 0) {
    fprintf(stderr, "getaddrinfo: %s\n", gai_strerror(gai));
    return 1;
}

int srv = socket(res->ai_family, res->ai_socktype, res->ai_protocol);
if (srv == -1) { perror("socket"); freeaddrinfo(res); return 1; }

int on = 1;
setsockopt(srv, SOL_SOCKET, SO_REUSEADDR, &on, sizeof on);  /* getsockopt(2) — §E.8 */
if (bind(srv, res->ai_addr, res->ai_addrlen) == -1) { perror("bind"); close(srv); freeaddrinfo(res); return 1; }
freeaddrinfo(res);                    /* bağladıktan sonra serbest — §A.8 */
if (listen(srv, 16) == -1) { perror("listen"); close(srv); return 1; }  /* backlog — §B.5 */

struct sockaddr_storage cli;
socklen_t clilen = sizeof cli;
int cfd = accept(srv, (struct sockaddr *)&cli, &clilen);   /* EINTR/EAGAIN — §B.6 */
if (cfd == -1) { perror("accept"); close(srv); return 1; }

close(cfd);                           /* hata yolu dâhil her yol close — §D.2 */
close(srv);
```

### F.2 Windows grubu (Winsock2 · C)

```c
#include <winsock2.h>
#pragma comment(lib, "ws2_32.lib")

WSADATA wsa;
if (WSAStartup(MAKEWORD(2, 2), &wsa) != 0) return 1;   /* süreçte bir kez — posix-socket §A.1 */

SOCKET srv = socket(AF_INET, SOCK_STREAM, IPPROTO_TCP);
if (srv == INVALID_SOCKET) { WSACleanup(); return 1; }

struct sockaddr_in addr = {0};
addr.sin_family      = AF_INET;
addr.sin_addr.s_addr = INADDR_ANY;
addr.sin_port        = htons(8080);

int on = 1;
setsockopt(srv, SOL_SOCKET, SO_REUSEADDR, (const char *)&on, sizeof on); /* Windows'ta argüman char* */
if (bind(srv, (struct sockaddr *)&addr, sizeof addr) == SOCKET_ERROR) { closesocket(srv); WSACleanup(); return 1; }
if (listen(srv, SOMAXCONN) == SOCKET_ERROR) { closesocket(srv); WSACleanup(); return 1; }

SOCKET cli = accept(srv, NULL, NULL); /* geçersizlik değeri: INVALID_SOCKET */
if (cli == INVALID_SOCKET) { closesocket(srv); WSACleanup(); return 1; }

closesocket(cli);                     /* close() değil — posix-socket §A.1 */
closesocket(srv);
WSACleanup();                         /* sürecin sonunda bir kez */

/* posix-socket = ag-api/posix-socket-ve-winsock-karsilastirmasi.md */
```

### F.3 PHP grubu (stream — her iki işletim sistemi aynı kod)

```php
<?php
declare(strict_types=1);

// PHP stream soyutlaması platform ayrımını runtime'a devreder:
// WSAStartup/closesocket'a PHP kullanıcı alanı kodu DOKUNMAZ.
$server = stream_socket_server('tcp://127.0.0.1:8080', $errno, $errstr);
if ($server === false) {
    fwrite(STDERR, "sunucu acilamadi: $errstr (no=$errno)\n");
    exit(1);
}

$client = stream_socket_accept($server, 5.0);   // 5 sn bekleme sınırı
if ($client === false) {
    fwrite(STDERR, "istemci baglantisi zaman asiminda\n");
    fclose($server);
    exit(1);
}

fwrite($client, "HTTP/1.1 200 OK\r\nContent-Length: 2\r\n\r\nOK");
fclose($client);                                // close() karşılığı
fclose($server);
```

### F.4 Grup eşleme tablosu — ve dürüstlük kaydı

| Grup | Örnek | Durum |
|---|---|---|
| Linux | §F.1 | kaynaklı (§F.6) |
| Windows | §F.2 | kaynaklı (§F.6) |
| macOS | §F.1 ile aynı POSIX çağrı seti varsayılmaz | ⚠️ VERIFICATION REQUIRED — ornekler.md §F.0 3. tur kaydı aynen korunur: macOS POSIX kimliği iddiası kaynaksız yazılmaz |
| Android (NDK) | örnek üretilmedi | ⚠️ VERIFICATION REQUIRED — ornekler.md §F.4: AAudio çağrı-imzası örnekleri 3. turda doğrulanmadı, uydurma kod yazılmaz |
| PHP (tüm OS) | §F.3 | php.net stream_socket_* — platform ayrımı PHP runtime'ında |

### F.5 C ↔ PHP karşılık tablosu

| Konu | C (POSIX / Windows) | PHP |
|---|---|---|
| süreç-kez kurulum | `WSAStartup` (yalnız Windows) | yok — runtime yapar |
| geçersizlik denetimi | `-1` / `INVALID_SOCKET` | `false` dönüş |
| kapatma | `close` / `closesocket` ayrımı | tek `fclose` |
| port seçimi | sayısal port + `htons` | `tcp://host:port` dizgesi |
| bekleme sınırı | `select`/`poll` | `stream_socket_accept` timeout argümanı |

### F.6 §F kanıt tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | `socket(2)` — soket yaratma | https://man7.org/linux/man-pages/man2/socket.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR (FM) |
| 2 | `bind(2)` — adres bağlama | https://man7.org/linux/man-pages/man2/bind.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR |
| 3 | `listen(2)` — backlog kuyruğu | https://man7.org/linux/man-pages/man2/listen.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR |
| 4 | `accept(2)` — yeni descriptor | https://man7.org/linux/man-pages/man2/accept.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR |
| 5 | `getaddrinfo(3)` — `AI_PASSIVE` | https://man7.org/linux/man-pages/man3/getaddrinfo.3.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR |
| 6 | Winsock `socket` | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-socket | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR |
| 7 | Winsock `closesocket` | https://learn.microsoft.com/en-us/windows/win32/api/winsock2/nf-winsock2-closesocket | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR |
| 8 | PHP `stream_socket_server` | https://www.php.net/manual/en/function.stream-socket-server.php | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 9 | PHP `stream_socket_client` | https://www.php.net/manual/en/function.stream-socket-client.php | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 10 | PHP `stream_socket_accept` | https://www.php.net/manual/en/function.stream-socket-accept.php | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |

Özet: 10/10 HTTP 200 · 7 VAR + 3 YENİ (envanter: 994 md · 3378 benzersiz URL —
2026-10-14 ölçümü) · repo iddiası eklenmez; PHP blokları php.net referanslarına
dayanır. 4. tur ölçüm ayrıntısı: `isletim-sistemi-api-dogrulama.md` §T.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
