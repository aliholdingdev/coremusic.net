---
node_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api/udp-tcp-ve-tls-api
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man7/tcp.7.html
  - https://man7.org/linux/man-pages/man7/udp.7.html
  - https://www.rfc-editor.org/rfc/rfc9293
  - https://www.rfc-editor.org/rfc/rfc768
  - https://man7.org/linux/man-pages/man2/send.2.html
  - https://man7.org/linux/man-pages/man2/recv.2.html
  - https://man7.org/linux/man-pages/man2/fcntl.2.html
  - https://man7.org/linux/man-pages/man2/epoll_ctl.2.html
  - https://man7.org/linux/man-pages/man3/inet_ntop.3.html
  - https://www.rfc-editor.org/rfc/rfc6066
  - https://docs.openssl.org/3.0/man7/ssl/
  - https://www.rfc-editor.org/rfc/rfc3493
validation: "2026-10-14 — 4. tur: §F TCP/UDP gruplu kod örnekleri (POSIX C sendto/recvfrom + PHP socket_*/stream) + 5 yeni URL (man7 sendto.2/recvfrom.2 · php.net socket-sendto/recvfrom/set-option) HEAD-200 · envanter kesişimi 0 · 2026-10-13: 12 resmi URL HTTP 200"
open_questions: []
group: mimari
---

# udp-tcp-ve-tls-api

**ELI15:** Ağda iki temel taşıyıcı vardır: **TCP** güvenilir, sıralı, hatasız
akış verir; **UDP** hız için her şeyi göze alır — kayıp ve sırasızlık mümkündür.
**TLS** bu ikisinin üstüne şifreleme katmanı ekler. Bu dosya üçünün de API
davranışını, non-blocking çoğullama desenlerini ve ölçüm yöntemlerini belgeler.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api/udp-tcp-ve-tls-api |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §A — TCP akış semantiği

### A.1 Tanım

TCP (Transmission Control Protocol) **bağlantılı**, **güvenilir**, **sıralı**
ve **hatasız** bir taşıyıcıdır. Uygulama bayt akışı alır: gönderilen baytlar
aynı sırayla, tam olarak bir kez (ideal koşullarda) ulaşır.

> "TCP provides a reliable, bidirectional, byte-stream communication service"
> — RFC 9293 §1.1.

### A.2 Temel özellikler matrisi

| Özellik | TCP davranışı | Uygulamaya etkisi |
|---|---|---|
| Bağlantı | Üç-way handshake (`SYN` → `SYN-ACK` → `ACK`) | `connect()` başarı = bağlantı kuruldu |
| Güvenilirlik | Yeniden gönderim + teyit (ACK) | Uygulama kayıp telafisi yazmaz |
| Sıralama | Seri numaralama ile sıralama | `recv` sırası gönderim sırasıdır |
| Akış kontrolü | Pencere tabanlı (sliding window) | Hızlı gönderici yavaş alıcıya göre yavaşlar |
| Taşma kontrolü | Algoritma ailesi (Reno · CUBIC · BBR) | Çekirdek içi — uygulama müdahalesi yok |
| Bölünme | MSS boyutunda paketlere bölünür | Uygulama mesaj sınırı görmez — §A.4 |
| Byte-stream | Mesaj sınırı **yoktur** | Çerçeveleme (framing) uygulamanın sorumluluğundadır |

### A.3 TCP bağlantı durumları (özet)

| Durum | Anlam |
|---|---|
| `LISTEN` | Sunucu `listen()` sonrası bağlantı bekliyor |
| `SYN_SENT` | İstemci `connect()` — SYN gönderildi |
| `ESTABLISHED` | Bağlantı kuruldu — veri taşınabilir |
| `FIN_WAIT_1` / `FIN_WAIT_2` | Bu taraf kapanış başlattı |
| `CLOSE_WAIT` | Karşı taraftan FIN alındı — uygulama `close()` bekliyor |
| `TIME_WAIT` | Aktif kapanış sonrası 2MSL bekleme |
| `CLOSED` | Bağlantı yok |

### A.4 Çerçeveleme (framing) sorunu

TCP bir **bayt akışı** taşır: `send("MERHABA")` + `send("DÜNYA")` alan tarafın
`recv(1024)` çağrısı tek seferde `"MERHABADÜNYA"` döndürebilir. Mesaj sınırının
uygulama tarafından işaretlenmesi gerekir:

| Çerçeveleme yöntemi | Tanım | Örnek protokol |
|---|---|---|
| Uzunluk öneki | İlk N bayt mesaj boyutunu taşır | protobuf length-prefixed · msgpack-rpc |
| Ayırıcı (delimiter) | Özel bayt dizisi mesaj sonunu gösterir | HTTP `\r\n\r\n` · SMTP |
| Sabit boyut | Tüm mesajlar eşit uzunlukta | Ses frame protokolleri (512 örnek kare — sartname) |
| Kendini tanımlayan | Yapı başlığı boyut bilgisini içerir | HTTP chunked · MPEG-TS |

### A.5 TCP kuyruk ve gecikme ilişkisi

| Kuyruk | Tanım | Etki |
|---|---|---|
| Gönderim kuyruğu | Uygulamanın `send` ettiği, çekirdeğin henüz göndermediği veri | Ağ yavaşsa şişer — gecikme (latency) artar |
| Alım kuyruğu | Çekirdeğin uygulamanın `recv`'unu beklediği veri | Uygulama yavaşsa pencere daralır |
| Bloat | Kuyrukların aşırı büyümesi ("bufferbloat") | RTT katlanır — gerçek zamanlı ses akışı için zehir |

Sınır: uygulama kuyrukları **kasıtlı olarak küçük tutmalı**; ağ katmanına
güvenip tampon şişirmek gecikmeyi büyütür (ses akışında ADR-019 per-OS player
sınırını ihlal eden gecikme).

### A.6 `TCP_NODELAY` — Nagle algoritması

| `TCP_NODELAY` | Davranış |
|---|---|
| Kapalı (varsayılan) | Nagle: küçük paketleri birleştirir — bant genişliği verimli, küçük mesajlar 40 ms (tipik ACK gecikmesi) kadar gecikebilir |
| Açık | Küçük paketler hemen gönderilir — etkileşimli protokoller (RPC, komut/kontrol) için |

İlgili `getsockopt`/`setsockopt` seçeneği: `IPPROTO_TCP` seviyesinde `TCP_NODELAY`.

### A.7 Keep-alive

| Seçenek | Anlam |
|---|---|
| `SO_KEEPALIVE` | Sessiz bağlantı için periyodik keep-alive sonlandırmaları açar |
| Zamanlama | Varsayılanlar sistem belirler (Linux'ta 2 saat · ilk denemeden sonra aralıklı) — uygulama sabiti **değildir** |
| Uygulama katmanı alternatifi | Protokol içi ping (HTTP/2 PING · WebSocket ping frame) — daha kontrollü |

> Windows tarafında keep-alive zamanlaması `tcp_keepalive` yapısı ile
> `WSAIoctl` üzerinden özelleştirilir (bkz.
> `posix-socket-ve-winsock-karsilastirmasi.md` §E).

### A.8 TCP hata ve davranış tablosu

| Olay | `send`/`recv` sonucu | Uygulama tepkisi |
|---|---|---|
| Karşı taraf kibar kapandı (`FIN`) | `recv` → 0 | Akışın sonu — okuma döngüsü biter |
| RST alındı | `recv` → -1 `ECONNRESET` | Bağlantı ölü — yeniden bağlanma |
| Ağ kesildi | Belirli süre sonra `ETIMEDOUT` | Keep-alive kapalıysa gecikmeli fark edilir |
| Gönderim tamponu dolu | `send` → -1 `EAGAIN` (non-blocking) | Alım hızlanana kadar yazma bekler |

## §B — UDP dagram semantiği

### B.1 Tanım

UDP (User Datagram Protocol) **bağlantısız**, **en fazla bir kez teslim**
(best-effort) taşıyıcısıdır. Her `sendto` bağımsız bir dagram (datagram)
gönderir; mesaj sınırı korunur.

> "UDP is a minimal message-oriented Transport Layer protocol" — RFC 768.

### B.2 UDP ↔ TCP karşılaştırma matrisi

| Kriter | TCP | UDP |
|---|---|---|
| Bağlantı | `connect` zorunlu (kural olarak) | Yok — `sendto`/`recvfrom` ile adres her pakette |
| Güvenilirlik | Yeniden gönderim + teyit | Yok — kayıp sessiz |
| Sıralama | Sıralı | Sırasız olabilir |
| Mesaj sınırı | Yok (bayt akışı) | Korunur (dagram = mesaj) |
| Başlık overhead | 20 bayt | 8 bayt |
| Yayın (multicast/broadcast) | Yok | `SO_BROADCAST` · multicast grup üyeliği |
| Uygulama çerçevesi | Gerekli (§A.4) | Gerekmez |
| Gecikme varyansı | Yeniden gönderim gecikme spike'ı üretir | Düşük ortalama · spike yok (kaypuyu kabul eder) |

### B.3 UDP API farkları

| Çağrı | UDP kullanımı | TCP'den fark |
|---|---|---|
| `sendto(fd, buf, len, flags, addr, alen)` | Hedef her pakette verilir | TCP'de `send` |
| `recvfrom(fd, buf, len, flags, addr, alen)` | Gönderen adres döner | TCP'de `recv` |
| `connect` (UDP) | "Varsayılan hedef" iliştirir — filtreleme etkisi | Bağlantı yoktur; sonraki `send` bu adrese gider |

### B.4 UDP boyut sınırları

| Sınır | Değer | Kaynak |
|---|---|---|
| IPv4 uygulama payload sınırı | 65507 bayt (65535 − 20 IP − 8 UDP) | RFC 768 + IP başlığı hesabı |
| Pratik sınır (tek Ethernet) | 1472 bayt (MTU 1500 − 20 IP − 8 UDP) | IP fragmentasyonu tetiklenmemesi için |
| IPv6 minimum MTU | 1280 bayt — fragmentasyon yok | IPv6 çekirdeği yeniden gönderim kullanmaz |
| `EMSGSIZE` | Sınır aşılırsa `sendto` başarısız | §C.4 |

### B.5 Güvenilirlik katmanı — uygulama sorumluluğu

UDP kaybı **kabul edilebilir** veya **telafi edilebilir**:

| Telafi yöntemi | Mekanizma | Maliyet |
|---|---|---|
| ACK + yeniden gönderim | Alıcı teyitler, eksik istenir | RTT gecikmesi ekler |
| FEC (ileri hata düzeltme) | fazladan parite paketleri | Bant genişliği artışı |
| Tampon tabanlı | Alıcı jitter tamponu doldurur sonra oynatır | Gecikme (2× jitter tamponu) |
| Kayıp kabulü | Ses/görüntüde tek kare atlanabilir | Kalite düşüşü |

> Gerçek zamanlı ses için tipik kombinasyon: UDP + jitter tamponu + FEC opsiyonu
> (RTP modeli). Çekirdek RTP/RTCP yığını bu dalın kapsamı dışındadır.

### B.6 UDP hata modları

| Hata | Koşul | Not |
|---|---|---|
| `ECONNREFUSED` | ICMP "port unreachable" yanıt dönerse | `connect` edilmiş UDP soketlerinde görülür |
| `EMSGSIZE` | Dagram MTU'yu aşıyor | IP fragmentasyonu engellenmişse (ör. bazı yollar) |
| `EAGAIN` | Alım kuyruğu boş (non-blocking) | Normal akış kontrolü |
| `EHOSTUNREACH` | Yerel rota yok | Ağ yapılandırması |

## §C — Non-blocking + poll/epoll desenleri

### C.1 Non-blocking kurulumu

Soket `fcntl(F_SETFL, O_NONBLOCK)` ile non-blocking yapıldığında hazır olmayan
işlemler `EAGAIN`/`EWOULDBLOCK` ile hemen döner (bkz.
`socket-api-temelleri.md` §E.3). Windows'ta karşılığı `ioctlsocket(FIONBIO)`
ile kurulur (bkz. `posix-socket-ve-winsock-karsilastirmasi.md` §B).

### C.2 Çoğullama (multiplexing) araçları

| Araç | Kapsam | Mekanizma |
|---|---|---|
| `select` | POSIX + Windows (`WSASelect`) | Hazır descriptor seti döner — set boyutu ile sınırlı (`FD_SETSIZE`) |
| `poll` | POSIX | `pollfd` dizisi — `select`'in sabit set sınırlarını kaldırır |
| `epoll` | Linux özel | Olay ilgi listesi — büyük bağlantı sayılarında ölçeklenir |
| `kqueue` | BSD/macOS özel | `epoll`'un muadili — dosya + olay kaynakları |
| IOCP | Windows özel | Tam asenkron tampon tabanlı — `WSARecv` + overlapped (bkz. `posix-socket-ve-winsock-karsilastirmasi.md` §B.4) |

### C.3 Olay tabloları

| Olay | `poll` bayrağı | Anlam |
|---|---|---|
| Okunabilir | `POLLIN` | `recv`/`accept` bloklanmaz |
| Yazılabilir | `POLLOUT` | `send` bloklanmaz · non-blocking `connect` tamamlandı |
| Hata | `POLLERR` / `POLLHUP` / `POLLNVAL` | Bağlantı hatası · kapanış · geçersiz descriptor |
| Normal kapanış | `POLLHUP` | Karşı taraf kapandı — `recv` 0 dönecek |

> `epoll` Linux'ta `POLLIN`/`POLLOUT` karşılıkları `EPOLLIN`/`EPOLLOUT` olarak
> adlandırılır; `epoll_ctl` ile ilgi listesine eklenir, `epoll_wait` ile olaylar
> toplanır (epoll_ctl(2) sayfası — §E.8).

### C.4 Desen 1 — tek iş parçacıklı event döngüsü

| Adım | İşlem |
|---|---|
| 1 | Dinleyici non-blocking `socket`+`bind`+`listen` |
| 2 | Event demultiplexer başlat (`epoll_create1` / `kqueue` / IOCP) |
| 3 | Döngü: olayları topla → ilgili descriptor'da tek işlem yap → tekrar bekle |
| 4 | Bağlantı kabulü `accept` ile — yeni descriptor da ilgi listesine eklenir |
| 5 | Kapanan descriptor ilgi listesinden çıkarılır ve `close` edilir |

| Artı | Eksı |
|---|---|
| Kilit yok — tek thread | CPU çekirdeği tam kullanılmaz |
| Lock-free uyumlu (RT ses callback'i ile aynı süreç modeli) | Uzun CPU işi döngüyü bloklar |

### C.5 Desen 2 — worker havuzu (thread per core)

| Adım | İşlem |
|---|---|
| 1 | Accept thread: `accept` + bağlantıları worker'lara dağıt |
| 2 | Worker thread'ler: kendi descriptor'larını `poll` ile yönetir |
| 3 | Dağıtım: `SO_REUSEPORT` (çekirdek-dengeli) veya iş kuyruğu |

| Artı | Eksı |
|---|---|
| Çok çekirdek tam kullanım | Kilit/veri yarışı riski — worker'lar arası durum paylaşımı |
| Bağlantı başına thread (eski model) | Binlerce bağlantıda thread maliyeti ölçeklenmez |

### C.6 Desen 3 — asenkron I/O (Windows IOCP özeti)

| Kavram | Anlam |
|---|---|
| Overlapped I/O | `WSARecv`/`WSASend` + `OVERLAPPED` yapısı — çağrı anında dön |
| Tamamlama portu | Sonuçlar `GetQueuedCompletionStatus` ile toplanır |
| Tampon sahipliği | Uygulama tamponları completion gelene kadar **okunmaz/yazılmaz** |

IOCP bu dalın derin konusu değil; `posix-socket-ve-winsock-karsilastirmasi.md`
§B.4 çizgisindedir — burada yalnızca POSIX event desenleriyle karşılaştırma
amacıyla özetlendi.

### C.7 Kısmi işlem kuralı

| Kural | Açıklama |
|---|---|
| Hiçbir syscall bloklanmaz | Non-blocking + event döngüsünün temel kuralı |
| `EAGAIN` = "daha sonra" | Hata değil akış durumu |
| Yazma tamponu hazırlığı | `POLLOUT` gelmeden `send` denemek döngüyü bozar |
| Çıkış (drain) akışı | Kapanışta gönderim tamponu boşaltılır (`SHUT_WR` öncesi) |

### C.8 Karar matrisi — çoğullama aracı seçimi

| Platform hedefi | Araç | Gerekçe |
|---|---|---|
| L0 motoru (Windows) | IOCP veya `select` (WSA) | Winsock özel modeli — `posix-socket-ve-winsock-karsilastirmasi.md` §B.4 |
| L0 motoru (Linux) | `epoll` | Büyük bağlantı sayılarında ölçek |
| macOS / iOS hedefi | `kqueue` | BSD ailesi tek çatı |
| Taşınabilir referans | `select` / `poll` | Her iki ailede de mevcut — az bağlantı |
| CoreMusic PHP | `curl` multi | Repo kanıtı: `ToolCalling.php:256` — olay döngüsü sarmalayıcıdadır |

### C.9 Ölçüm — event döngüsü sağlığı

| Metrik | Ne ölçer | Eşik yaklaşımı |
|---|---|---|
| Olay gecikmesi (event lag) | Olay hazır → işlendi arası süre | Milisaniye altı hedeflenir (ses akışında 512 örnek = 10.7 ms @ 48 kHz) |
| Döngü süresi | Tek `wait`+işlem turu | Sürekli artış = CPU darboğazı |
| Kuyruk derinliği | Bekleyen hazır olay sayısı | Sürekli büyüme = işçi açığı |
| `EAGAIN` oranı | Gereksiz deneme frekansı | Desen hatası göstergesi |

## §D — TLS sarmalama (SSL_CTX · sertifika doğrulama)

### D.1 TLS nedir ve nerede durur

TLS (Transport Layer Security) TCP (ve UDP üzerinde DTLS) üstünde **şifreleme,
bütünlük ve sunucu kimlik doğrulama** sağlar. Soket API'sini değiştirmez —
`SSL_connect` / `SSL_read` / `SSL_write` soket descriptor'unun üstünde çalışan
katmandır.

### D.2 OpenSSL nesne modeli (SSL_CTX · SSL)

| Nesne | Anlam | Yaşam döngüsü |
|---|---|---|
| `SSL_CTX` | Paylaşılan bağlam: protokol sürümü, sertifika, anahtar, şifreleme listesi | Process geneli kurulur, paylaşılır |
| `SSL` | Bağlantı başına oturum nesnesi | Bağlantı ile oluşturulur, `SSL_free` ile serbest bırakılır |
| `SSL_CTX_new` | Bağlam oluşturur (TLS_client_method / TLS_server_method) | Başlangıç |
| `SSL_new(ctx)` | Bağlantı başına `SSL` türetir | Bağlantı kurulumu |
| `SSL_set_fd` | Soket descriptor'u bağlar | Bağlantı kurulumu |
| `SSL_connect` / `SSL_accept` | İstemci / sunucu el sıkışması | Handshake |
| `SSL_read` / `SSL_write` | Şifreli veri aktarımı | Veri fazı |
| `SSL_shutdown` | Kibar TLS kapanışı | Kapanış |
| `SSL_free` / `SSL_CTX_free` | Temizlik | Kapanış |

### D.3 Sertifika doğrulama — zorunlu kural

| Adım | API | Sonuç |
|---|---|---|
| Güven zinciri yükleme | `SSL_CTX_set_default_verify_paths` veya `SSL_CTX_load_verify_locations` | Kök CA sertifikaları bağlama |
| Doğrulama modu | `SSL_CTX_set_verify(ctx, SSL_VERIFY_PEER, NULL)` | Sunucu sertifikası zorunlu |
| İstemci tarafı | `SSL_connect` dönüşü + `SSL_get_verify_result` | `X509_V_OK` değilse bağlantı reddedilir |
| İsim doğrulama | `SSL_set1_host` / `X509_VERIFY_PARAM_set1_host` | CN/SAN kontrolü — sadece zincir doğrulaması **yetmez** |
| Sunucu tarafı (mTLS) | `SSL_CTX_set_client_CA_list` + `SSL_VERIFY_PEER \| SSL_VERIFY_FAIL_IF_NO_PEER_CERT` | İstemci sertifikası zorunlu |

> Hata modu: doğrulama **atlanırsa** (kötü pratiğe dönüşmüş `SSL_VERIFY_NONE`)
> bağlantı şifrelenir ama **karşı tarafın kimliği doğrulanmaz** — adam ortada
> (MITM) saldırısına açık. CoreMusic L0 motorunda bu bayrak **kullanılmaz**.

### D.4 Protokol sürümü matrisi

| Sürüm | Durum | Not |
|---|---|---|
| SSL 2.0 / 3.0 | Yasak | Kırılabilir — OpenSSL 1.1.0+ varsayılan dışı |
| TLS 1.0 / 1.1 | Yasak (modern politika) | RFC 8996 ile geri çekildi |
| TLS 1.2 | Yaygın | `TLS1_2_VERSION` — minimum kabul edilebilir |
| TLS 1.3 | Önerilen | Daha az round-trip (0-RTT opsiyonel), sadeleştirilmiş suite |
| DTLS 1.2 / 1.3 | UDP için | TCP dışı güvenli taşıma |

### D.5 Handshake maliyeti ve yeniden kullanım

| Konu | Ölçüm / kural |
|---|---|
| Handshake RTT'si | TLS 1.2: 2-RTT · TLS 1.3: 1-RTT (0-RTT opsiyonel, replay riski ile) |
| Oturum yeniden kurma | `SSL_SESSION` / ticket ile hızlı yeniden bağlanma (`SSL_CTX_set_session_cache_mode`) |
| Uzatma (keep-alive) | `SSL_MODE_ENABLE_PARTIAL_WRITE` · `SSL_MODE_ACCEPT_MOVING_WRITE_BUFFER` tampon esnekliği sağlar |
| Uzatma sarmalayıcı | `SSL_ctrl` + `SSL_CTRL_SET_TLSEXT_HOSTNAME` ile SNI (Server Name Indication) — çok-tenant sunucularda zorunlu |

### D.6 SNI ve uzantılar

| Uzantı | Amaç |
|---|---|
| SNI | TLS el sıkışmasında hedef ad verilir — aynı IP'de birden çok sertifika |
| ALPN | Uygulama katmanı protokol seçimi (HTTP/2 `h2`, HTTP/1.1 `http/1.1`) |
| Max fragment length | Küçük tamponlu cihazlar için fragment sınırı (RFC 6066 §4) |

> RFC 6066 (SNI · session ticket · max fragment length) doğrulanmış kaynaktır
> — §E.8.

### D.7 TLS hata modları

| Hata | Anlam | Tepki |
|---|---|---|
| `SSL_ERROR_WANT_READ` | Handshake/veri için okuma beklenir | Non-blocking: `poll` `POLLIN` |
| `SSL_ERROR_WANT_WRITE` | Yazma beklenir | Non-blocking: `poll` `POLLOUT` |
| `SSL_ERROR_ZERO_RETURN` | Karşı taraf kibar kapanış (`close_notify`) | Temiz son |
| `SSL_ERROR_SYSCALL` | Altındaki soket hatası | `errno` kontrol edilir |
| `SSL_ERROR_SSL` | Protokol hatası (doğrulama başarısız dahil) | `SSL_get_verify_result` · sertifika zinciri denetimi |
| Sertifika süresi doldu | `X509_V_ERR_CERT_HAS_EXPIRED` | Zaman senkronu (NTP) · sertifika yenileme |
| İsim uyuşmazlığı | `X509_V_ERR_HOSTNAME_MISMATCH` | SNI / SAN yapılandırması |

### D.8 Göz ardı edilen kuyruk uyarısı (TLS + TCP)

TCP + TLS katmanlı yapıda gönderim `SSL_write` ile şifrelenir ve TCP kuyruğuna
düşer — uygulama "yazdım" dese bile ağ henüz göndermemiş olabilir
(`socket-api-temelleri.md` §A.5 bufferbloat). Gerçek zamanlı ses akışında bu
kuyruğun sınırlı tutulması **zorunlu**tur; aksi halde gecikme katlanarak büyür.

## §E — Ölçüm (throughput · RTT · yeniden bağlanma)

### E.1 Ölçüm metrikleri

| Metrik | Tanım | Birim |
|---|---|---|
| Throughput | Zaman başına taşınan bayt | Mbps · MB/s |
| RTT (Round-Trip Time) | Paket gidip dönme süresi | ms |
| Jitter | RTT varyansı | ms (standart sapma) |
| Kayıp oranı | Kaybolan paket yüzdesi | % |
| Yeniden bağlanma süresi | Kopuş → yeni hazır bağlantı arası süre | sn · ms |

### E.2 RTT ölçüm yöntemleri

| Yöntem | Uygulama | Not |
|---|---|---|
| Uygulama seviyesi ping | Zaman damgalı echo paketleri | UDP'de uygulama içi; TCP'de protokol içi PING |
| `TCP_INFO` (Linux) | `getsockopt(TCP_INFO)` — `tcpi_rtt` | Kernel ölçümü, kopya maliyeti düşük |
| `TCP_TIMESTAMP` | El sıkışma/ACK zaman damgaları | `tcpdump` ile pasif ölçüm |
| ICMP ping | Ağ katmanı ölçümü | Soket API'si dışı — teşhis aracı |

### E.3 Throughput ölçüm yöntemleri

| Yöntem | Açıklama |
|---|---|
| Sabit süreli gönderim | Bilinen `len` × çağrı sayısı / süre |
| Bileşik (pipeline) gönderim | Aynı anda uçuşta tutulan bayt penceresi |
| Bant genişliği-gecikme ürünü | BDP = throughput × RTT — akış penceresi alt sınırı |

> Ölçüm tuzağı: non-blocking döngüde `EAGAIN`'ı saymayan throughput ölçümü
> şişirilmiş sonuç üretir — "denenen" değil "gönderilen" sayılmalıdır.

### E.4 Yeniden bağlanma (reconnect) ölçümü

| Senaryo | Ölçülen | Beklenen davranış |
|---|---|---|
| Sunucu kibar kapattı (`FIN`) | Yeniden hazır bağlantı süresi | 1 handshake RTT'si (+ backoff politikası) |
| RST ile kops | Aynı | Benzer + üst katmanın hata işleme süresi |
| Ağ kesildi | `ETIMEDOUT` süresi + backoff | Üstel backoff — saniyeler → dakikalar |
| TLS oturum ticket'ı geçerli | Handshake kısalır | 1-RTT yerine oturum yeniden kurma |

### E.5 Ölçüm protokolü — kayıt edilecek alanlar

| Alan | Değer |
|---|---|
| Tarih | Ölçüm günü (ISO 8601) |
| Ortam | OS · çekirdek · ağ yolu (LAN/WAN) |
| Buffer boyutu | `SO_SNDBUF` / `SO_RCVBUF` · uygulama tamponu |
| Protokol | TCP · UDP · TLS sürümü |
| N · süre | Kaç paket / kaç saniye |
| Sonuçlar | ortalam · p95 · min · max (RTT/jitter için) |

### E.6 Ölçüm — CoreMusic bağlamı

| Konu | Durum |
|---|---|
| Sartname ölçüm maddesi | 512 örnek buffer, 48 kHz — **ses** katmanı ölçümü (olcum PLANNED) |
| Ağ ölçümü | Sartname ağ metrikleri içermiyor — Q-A1 (`ag-api/index.md` §5) |
| L0 motoru ağ özelliği | Henüz planlanmadı — ölçüm protokolü bu dosyada hazır tutulur |

### E.7 Hata → ölçüm etkileşimi

| Olay | Ölçümü etkisi | Düzeltme |
|---|---|---|
| `EAGAIN`'lar sayıldı | Throughput şişer | Yalnız dönüş > 0 sayılar |
| Ölçüm sırasında keep-alive kopması | Ani RTT sıçraması | Ölçüm penceresi bağlantı kararlılığında |
| TLS yeniden el sıkışması ölçüm penceresine düştü | Yeniden bağlanma süresi iki kat | Oturum yeniden kurma açık/kapalı ayrıştırılır |
| CPU frekans ölçekleme | Gecikme artışı | Performans planlayıcı sabitlenir (platform-api dalı §D — Windows örneği) |

### E.8 Ölçüm araçları — ağ katmanı (teşhis)

| Araç | Ne ölçer | Erişim |
|---|---|---|
| `ping` | ICMP RTT | Kullanıcı katmanı — soket API'si dışı |
| `iperf3` | TCP/UDP throughput · jitter · kayıp | Ağ yolu başına ölçüm aracı |
| `ss` / `netstat` | Kuyruk derinlikleri (`Send-Q` · `Recv-Q`) · durum sayıları | Çekirdek soket tablosu |
| `tcpdump` | Paket zaman damgaları — pasif RTT | Kapsayıcı (capture) yetkisi gerekir |
| `SSLKEYLOGFILE` | TLS anahtar günlüğü — çözümlenmiş akış | Geliştirme/test ortamı |

> Bu araçların hiçbiri L0 motorunun bağımlılığı değildir; yalnızca doğrulama
> ve teşhis katmanında kullanılır. Sartname ölçüm maddesi (512/48 kHz) ses
> katmanını kapsar — ağ ölçümü için ayrı bir kapsam kararı gerekir (Q-A1).

### E.9 Sınır durumları ve ölçüm tuzakları

| Durum | Tuzak | Doğru yaklaşım |
|---|---|---|
| Loopback ölçümü | RTT mikrosaniye düzeyine iner — gerçek ağdan farklı | LAN/WAN ölçümü ayrı yapılır |
| Aynı makine iki süreç | `AF_UNIX` kullanılırsa ağ yığını atlanır | TCP ölçümü isteniyorsa `AF_INET` + loopback |
| Ölçüm penceresi çok kısa | 1 paket = istatistik yok | En az yüzlerce örnek · p95 raporlanır |
| `SO_SNDBUF` artırıldı | Throughput artar sanılır — kuyruk şişer | Gecikme ile birlikte raporlanır |
| Kuyruk taşması | Gecikme dakikalara çıkar (bufferbloat) | `ss`/`tcp_info` ile kuyruk izlenir |
| Nagle + gecikmeli ACK | 40 ms katlanır — `TCP_NODELAY` gerekir | Protokol tipi ile eşleştirilir (§A.6) |
| TLS session cache kapalı | Her bağlantıda tam handshake | Ticket/cache açık ölçülür (§D.5) |

### E.10 Karar matrisi — taşıyıcı seçimi

| Uygulama ihtiyacı | Seçim | Gerekçe |
|---|---|---|
| API trafiği (CoreMusic PHP → dış servis) | TCP + TLS 1.2+ | Güvenilirlik + kimlik doğrulama — `BaseOAuthProvider.php:76` kanıtı |
| Gerçek zamanlı ses komut kanalı | UDP + uygulama çerçevelemesi | Düşük gecikme · kayıp kabulü (kapsam: Q-A1) |
| Uzaktan kontrol / RPC | TCP + `TCP_NODELAY` + length-prefixed çerçeve | Küçük mesaj gecikmesi kritik |
| Yerel IPC | `AF_UNIX` stream | Ağ yığını gereksiz (bkz. `socket-api-temelleri.md` §A.6) |
| Dosya/patch indirme | HTTP(S) sarmalayıcı — `curl` | Repo kanıtı: `ToolCalling.php:256` |

### E.11 Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| `tcp(7)` — güvenilir bayt akışı, `TCP_NODELAY`, keep-alive, `TCP_INFO` | https://man7.org/linux/man-pages/man7/tcp.7.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `udp(7)` — dagram semantiği, boyut sınırları, `SO_BROADCAST` | https://man7.org/linux/man-pages/man7/udp.7.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| RFC 9293 — TCP spesifikasyonu (güvenilir · sıralı · bayt akışı) | https://www.rfc-editor.org/rfc/rfc9293 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| RFC 768 — UDP minimal mesaj taşıyıcı | https://www.rfc-editor.org/rfc/rfc768 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `send(2)` — kısmi gönderim, `MSG_NOSIGNAL`, `MSG_DONTWAIT` | https://man7.org/linux/man-pages/man2/send.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `recv(2)` — `MSG_PEEK`, `MSG_WAITALL`, dönüş değerleri | https://man7.org/linux/man-pages/man2/recv.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `fcntl(2)` — `O_NONBLOCK` kurulumu | https://man7.org/linux/man-pages/man2/fcntl.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `epoll_ctl(2)` — ilgi listesine ekleme/çıkarma | https://man7.org/linux/man-pages/man2/epoll_ctl.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| `inet_ntop(3)` — adres → metin çevirici | https://man7.org/linux/man-pages/man3/inet_ntop.3.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| RFC 6066 — SNI · session ticket · max fragment length | https://www.rfc-editor.org/rfc/rfc6066 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| OpenSSL `ssl(7)` — SSL_CTX / SSL nesne modeli, `SSL_VERIFY_PEER` | https://docs.openssl.org/3.0/man7/ssl/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| RFC 3493 — getaddrinfo temel arayüzü (basic socket interface) | https://www.rfc-editor.org/rfc/rfc3493 | 2026-10-13 | ✅ HTTP 200 (HEAD) |

---

## §F — Gruplu kod örnekleri: TCP/UDP taşıma katmanı (2026-10-14)

§A-§D doktrinini grup başına gösterir. Bloklar derlenebilirlik iddiası
TAŞIMAZ; her bloğun kaynağı §F.5 tablosudur.

### F.1 Linux/POSIX grubu · TCP — kısmi gönderim döngüsü (C)

§C.7'deki kuralın kodu: tek `send()` yeterli DEĞİLDİR.

```c
#include <sys/socket.h>
#include <errno.h>
#include <string.h>
#include <stddef.h>

static int tam_gonder(int fd, const void *buf, size_t len)
{
    const char *p = buf;
    size_t kalan = len;
    while (kalan > 0) {
        ssize_t n = send(fd, p, kalan, MSG_NOSIGNAL);  /* SIGPIPE üretmez */
        if (n == -1) {
            if (errno == EINTR) continue;              /* sinyal kesintisi: yeniden dene */
            if (errno == EAGAIN || errno == EWOULDBLOCK) {
                /* bloklanmayan soket: poll() ile yazılabilirlik bekle — §C.1.
                   Bu örnekte sadeleştirildi: çağrıya geri dönülmez. */
                return -1;
            }
            return -1;                                 /* EPIPE/ECONNRESET — §E hata modları */
        }
        p += n;
        kalan -= (size_t)n;                            /* kısmi gönderim: ilerleme §C.7 */
    }
    return 0;
}
```

### F.2 Linux/POSIX grubu · UDP — sendto/recvfrom (C)

```c
#include <sys/socket.h>
#include <arpa/inet.h>
#include <string.h>
#include <stdio.h>

struct sockaddr_in hedef = {0};
hedef.sin_family = AF_INET;
hedef.sin_port   = htons(5000);
inet_pton(AF_INET, "127.0.0.1", &hedef.sin_addr);   /* metin → ikili — socket-api-temelleri §A.7 */

const char *paket = "merhaba-udp";
ssize_t n = sendto(s, paket, strlen(paket), 0,
                   (struct sockaddr *)&hedef, sizeof hedef);
if (n == -1) perror("sendto");   /* UDP'de ICMP yanıtı ECONNREFUSED üretir — §B.6 */

char tampon[2048];               /* alım tamponu boyutu §B.4 sınırları içindedir */
struct sockaddr_in gelen;
socklen_t glen = sizeof gelen;
ssize_t m = recvfrom(s, tampon, sizeof tampon, 0,
                     (struct sockaddr *)&gelen, &glen);
if (m == -1) perror("recvfrom"); /* dagram kaybı sessizdir — güvenilirlik §B.5 */
```

### F.3 PHP grubu · UDP — socket_sendto/socket_recvfrom

```php
<?php
declare(strict_types=1);

$udp = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
if ($udp === false) { exit(1); }

socket_set_option($udp, SOL_SOCKET, SO_REUSEADDR, 1);  /* C tarafındaki setsockopt karşılığı */
socket_bind($udp, '0.0.0.0', 5000);

$adres = '127.0.0.1';
socket_sendto($udp, 'merhaba-udp', strlen('merhaba-udp'), 0, $adres, 5000);

$tampon = '';
$from = '';
$port = 0;
$bayt = socket_recvfrom($udp, $tampon, 2048, 0, $from, $port);
if ($bayt === false) {
    $no = socket_last_error($udp);
    fwrite(STDERR, "recvfrom hata: " . socket_strerror($no) . "\n");
}
socket_close($udp);
```

### F.4 PHP grubu · TCP + TLS yüzeyi — stream_socket_client

§D.2-§D.3 doktrini PHP'de tek çağrıya iner: `tls://` öneki ayarı runtime'a
devreder; sertifika doğrulama PHP sürümüne göre varsayılanıdır — kritik
servislerde §D.3'teki açık doğrulama kuralı uygulanır.

```php
<?php
declare(strict_types=1);

$fp = stream_socket_client('tcp://127.0.0.1:8080', $errno, $errstr, 5.0);
if ($fp === false) {
    fwrite(STDERR, "baglanti basarisiz: $errstr (no=$errno)\n");
    exit(1);
}

stream_set_timeout($fp, 2);                 /* okuma zaman aşımı */
fwrite($fp, "PING\n");
$yanit = fread($fp, 64);
$bilgi = stream_get_meta_data($fp);
if ($bilgi['timed_out']) {
    fwrite(STDERR, "okuma zaman asimi\n");
}
fclose($fp);
```

Grup notu: §F.1-§F.2 C blokları POSIX Linux içindir; Windows karşılığı bu
ağacın `posix-socket-ve-winsock-karsilastirmasi.md` §F.2'sindedir (bu dosya
tekrarlamaz). macOS/Android durumu ornekler.md §F.0-§F.4 dürüstlük kaydıyla
sınırlıdır — ⚠️ VERIFICATION REQUIRED.

### F.5 §F kanıt tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | `sendto(2)` — adresli gönderim | https://man7.org/linux/man-pages/man2/sendto.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 2 | `recvfrom(2)` — adresli alım | https://man7.org/linux/man-pages/man2/recvfrom.2.html | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 3 | PHP `socket_sendto` | https://www.php.net/manual/en/function.socket-sendto.php | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 4 | PHP `socket_recvfrom` | https://www.php.net/manual/en/function.socket-recvfrom.php | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 5 | PHP `socket_set_option` | https://www.php.net/manual/en/function.socket-set-option.php | 2026-10-14 | ✅ HTTP 200 (HEAD) · YENİ (kesişim 0) |
| 6 | `send(2)` — `MSG_NOSIGNAL`, kısmi gönderim | https://man7.org/linux/man-pages/man2/send.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR (§E.11) |
| 7 | `recv(2)` — dönüş değerleri | https://man7.org/linux/man-pages/man2/recv.2.html | 2026-10-13 | ✅ HTTP 200 (HEAD) · VAR (§E.11) |
| 8 | PHP `stream_socket_client` | https://www.php.net/manual/en/function.stream-socket-client.php | 2026-10-14 | ✅ HTTP 200 · VAR (4. turda bu ağacın `socket-api-temelleri.md` §F.6'sında tescillendi) |

Özet: 8/8 HTTP 200 · 5 YENİ + 3 VAR (envanter: 994 md · 3378 benzersiz URL —
2026-10-14 ölçümü). 4. tur ayrıntısı: `isletim-sistemi-api-dogrulama.md` §T.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
