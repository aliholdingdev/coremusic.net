---
title: "isletim-sistemi-api — Kapsam"
type: architecture-scope
node_id: K000-isletim-sistemi/isletim-sistemi-api
node_type: subdomain-scope
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/intro.2.html
  - https://man7.org/linux/man-pages/man3/errno.3.html
  - ".ai/architecture/K000-isletim-sistemi/os-master.md §3"
validation: "2026-10-13 · yeni 3 resmi URL HTTP 200 · 5 alt-dal envanteri · §A-§F iskeleti"
open_questions:
  - "Q-P1 Hangi API yüzeyi kastediliyor — kernel API mi, kullanıcı uzay hizmetleri mi?"
  - "Q-P2 Platform ses API'sinin yüzeyi burada mı, isletim-sistemi-audio içinde mi?"
  - "Q-P3 Hedef platform API seti — syscall seviyesi mi, kütüphane API'si mi?"
  - "Q-P4 core ↔ kernel sınırına bu dal nasıl temas eder?"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# isletim-sistemi-api — Kapsam

## §0 Rol (ELI15)

İşletim sisteminin uygulamalara açtığı **çağrı / arayüz yüzeyi**: program, işletim
sisteminden hizmet ister ve bu isteğin gittiği sözleşme bu dalın konusudur. `os-master.md`
§3 ELI15'i birebir: "Uygulamalara açılan OS çağrı yüzeyi".

Kanıtlı tanım: **"The system call is the fundamental interface between an application and
the Linux kernel."** — https://man7.org/linux/man-pages/man2/syscalls.2.html (2026-10-12 ✅)

Analoji: restoran menüsü (API) ile mutfağın (kernel) arasındaki **sipariş defteri**.
Menüyü yazarsın, mutfağın içine girmezsin.

Kanıtlı ikinci tanım: **"Linux is a Unix-like operating system... A running program is
called a process. Each process has a process ID..."** —
https://man7.org/linux/man-pages/man2/intro.2.html (2026-10-13 ✅ HTTP 200). `intro(2)`
tam olarak bu dalın **giriş katmanıdır**: syscall listesine, numaralandırmalara ve
`_syscall` makrolarına kapı açar.

> **Kapsamın özü tek cümlede:** bu dal, uygulamanın OS'e *ne sorabildiğini* tanımlar;
> OS'in *nasıl cevapladığı* `isletim-sistemi-kernel`'in, *ne servis verdiği*
> `isletim-sistemi-core`'un konusudur.

## §A — Kapsam

### §A.1 Ne KAPSAR

| # | Kapsam maddesi | Dayanak |
|---|---|---|
| A1 | Linux syscall yüzeyi: uygulama ↔ çekirdek temel arayüzü | dogrulama §1/1 |
| A2 | `syscall(2)` dolaylı çağrı mekanizması + `SYS_*` sabitleri | dogrulama §1/2 |
| A3 | Kütüphane wrapper katmanı: glibc'nin sistem çağrılarını sarmalayan fonksiyonları | dogrulama §1/3 |
| A4 | POSIX taşınabilir API standardı (Base Specifications Issue 8) | dogrulama §1/6 |
| A5 | Bellek/dosya eşleme API'si örneği: `mmap(2)` | dogrulama §1/7 |
| A6 | Windows platform API'si örneği: Core Audio başlıkları (`Audioclient.h` / `Audiopolicy.h`) | dogrulama §1/4 |
| A7 | Android native C API yüzeyi örneği: AAudio | dogrulama §1/5 |
| A8 | macOS uygulama-düzey servis API'si örneği: AudioToolbox | dogrulama §1/8 |
| A9 | "OS ↔ uygulama: Syscall / API yüzeyi" entegrasyon sınırı | `os-bagimliliklar.md` §1 B13 |
| A10 | CoreMusic'in API'yi **dolaylı** kullanması: PHP → Zend VM → runtime (C) → libc → syscall | `os-master.md` §2 |
| A11 | Çıkış değeri sözleşmesi: `0`/`-1` dönüşü + `errno` yazımı (katman 2'nin görünür yüzü) | https://man7.org/linux/man-pages/man3/errno.3.html |
| A12 | Argüman taşıma sözleşmesi: register sırası (x86-64) ve alt-program yığın düzeni | `syscall(2)` + §C.1 |
| A13 | Çağrı numarası kavramı: çekirdek içi `__NR_*` tanımı ile uygulama arasındaki sayısal köprü | `syscalls(2)` |
| A14 | Hata kodu ailesi ve yeniden deneme kuralları (`EINTR` · `EAGAIN`) | §B dalı `syscall/` |
| A15 | Kısıt ve sandbox arayüzleri: seccomp-BPF filtresi, capability sorgusu | §B dalı `syscall/` |
| A16 | Süreç/sinyal/bekleme arayüzleri: `fork` · `exec` · `waitpid` · `signal` · `select/poll/epoll` | §B dalı `suresin-ve-zamanlama-api/` |

### §A.2 Ne KAPSAMAZ

| # | Kapsam dışı | Nereye gider | Dayanak |
|---|---|---|---|
| B1 | Çekirdek içi yürütme: preemption, zamanlama, RT yapılandırması | `isletim-sistemi-kernel` | `../index.md` §4 |
| B2 | Merkez OS servislerinin kendisi (ne hizmet verdiği) | `isletim-sistemi-core` | `../index.md` §4 |
| B3 | Ses uç noktası akış davranışı (shared/exclusive, buffer) | `isletim-sistemi-audio` | `../index.md` §4 |
| B4 | OS log/metric/trace | `isletim-sistemi-observability` | `../index.md` §4 |
| B5 | Uygulama testinin kendisi (PHPUnit) | `isletim-sistemi-testing` | `../index.md` §4 |
| B6 | CoreMusic uygulama kodunun iş mantığı | K001+ | `os-master.md` §2 |
| B7 | Paket yönetimi ve modül yükleme (`finit_module` / `modprobe` yüzeyi) | `isletim-sistemi-core` | sınır kararı yok ⚠️ |
| B8 | Ağ soket protokol yığınının davranışı (TCP/UDP/QUIC) | `ag-api/` alt-dalı | 2. ajan beyazlistesi |
| B9 | Platform SDK tümü (yalnız API **yüzeyi** burada) | `platform-api/` alt-dalı | 2. ajan beyazlistesi |
| B10 | Donanım register erişimi ve DMA | `K001-donanım` | kök AGENTS.md §9 |
| B11 | Firmware OTA ve gömülü güncelleme | `K005-firmware` | kök AGENTS.md §9 |

### §A.3 Kabul kriteri

| # | Kriter | Ölçüm |
|---|---|---|
| K1 | Bu dalın her dosyası 8 alan + `group` frontmatter taşır | FM denetimi |
| K2 | Her içerik dosyası ≥500 tam satır, `index.md` 40–100 satır | satır sayımı |
| K3 | Her web iddiası resmi kaynakta 2026-10-13 tarihinde HTTP 200 | `Invoke-WebRequest -Method Head` |
| K4 | Her repo kanıtı `Dosya:` + `Satır:` ile diskten doğrulanır | `Get-Content -Skip/-First` |
| K5 | Bilinmeyen değer `UNKNOWN`, doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` | etiket denetimi |
| K6 | Yasak dolgu işaretleri yok (erteleme etiketi, boş gövde, boş şablon, boş tablo hücresi) | regex denetimi |
| K7 | `parent:` alanı yok — yalnız `parent_id:` | regex denetimi |
| K8 | `group` değeri tırnaksız (`mimari` / `kod` / `kanit`) | FM denetimi |

### §A.4 Bağımlılıklar

| # | Bağımlılık | Yön | Tür |
|---|---|---|---|
| D1 | `os-master.md` §2 §3 §8 | bu dal → üst | mimari |
| D2 | `os-bagimliliklar.md` §1 B13 | bu dal → üst | sınır |
| D3 | POSIX.1-2024 Base Specifications | bu dal → standart | dış |
| D4 | Linux man-pages (sec 1/2/3/7/8) | bu dal → belge | dış |
| D5 | glibc referans kılavuzu | bu dal → belge | dış |
| D6 | CoreMusic PHP deposu (5 dosya) | bu dal → kanıt | disk |
| D7 | `isletim-sistemi-kernel` | bu dal → kardeş | sınır |
| D8 | `isletim-sistemi-core` | bu dal → kardeş | sınır |

## §B — 5 alt-dal envanteri

> Beş alt-dalın tamamı bu bölümde envantere alınır; `ag-api/` ve `platform-api/`
> içerik üretiminde **2. ajanın beyazlistesindedir** — burada yalnızca envanter satırı
> taşınır, dosyalarına girilmez.

| # | Alt-dal | Ad | Sorumluluk | Girdi | Çıktı | Hata modu |
|---|---|---|---|---|---|---|
| S1 | `syscall/` | Çağrı mekanizması | çekirdeğe geçişin ham sözleşmesi | uygulama argümanları | dönüş değeri + `errno` | yanlış ABI → sessiz bozulma |
| S2 | `bellek-ve-dosya-api/` | Bellek ve dosya | adres uzayı ve dosya tanımlayıcı yaşam döngüsü | `fd` · offset · bayt | byte / işaretçi | sızıntı → `EMFILE` · `ENOMEM` |
| S3 | `suresin-ve-zamanlama-api/` | Süreç, sinyal, bekleme | süreç yaratma, sinyal, çok-islev demultiplex | pid · sinyal numarası · fd kümesi | çocuk durumu · olay kümesi | zombie · `ECHILD` · `EINTR` |
| S4 | `ag-api/` | Ağ | soket ve ağ protokol yüzeyi (2. ajan) | soket adresi · bayt | gönderilen/alınan bayt | `ECONNRESET` · `EPIPE` |
| S5 | `platform-api/` | Platform | OS-bağımsız platform yüzeyi (2. ajan) | platform anahtarı | platform hizmeti | sürüm uyuşmazlığı |

### §B.1 `syscall/` — detay

| Alan | Değer |
|---|---|
| Sorumluluk | Uygulama → libc → kernel zincirinin, çağrı numaralarının, ABI register düzeninin, dönüş değeri/`errno` modelinin ve sandbox arayüzünün (seccomp-BPF · capability · ptrace) belgelenmesi |
| Girdi | Uygulama tarafı: syscall numarası + argüman listesi; kullanıcı uzay: `SYS_*` sabiti |
| Çıktı | Kernel tarafı: `0` veya pozitif sonuç, hata ise `-errno` → libc `errno` yazımı |
| Hata modu | ABI ihlali (yanlış register), `EINTR` ile yarıda kesilmiş çağrı, seccomp'un `SIGSYS` ile öldürmesi |
| Dosyalar | `syscall-mekanizmasi.md` · `syscall-hata-kodlari-ve-errno.md` · `syscall-guvenlik-ve-sandbox.md` |
| Sınır | `isletim-sistemi-kernel` (çekirdek içi) · `isletim-sistemi-security` (politika sahibi) |

### §B.2 `bellek-ve-dosya-api/` — detay

| Alan | Değer |
|---|---|
| Sorumluluk | `mmap/munmap/mprotect` · `brk/sbrk` · `open/close/read/write/lseek` · `stat/fcntl/ioctl` · inotify/FSEvents/ReadDirectoryChangesW arayüzlerinin belgelenmesi |
| Girdi | Dosya yolu · bayt aralığı · koruma bayrakları · descriptor |
| Çıktı | Eşlenmiş adres aralığı · okunan/yazılan byte sayısı · `struct stat` · dosya olay akışı |
| Hata modu | `EACCES` (izin) · `ENOMEM` (eşleme) · `EBADF` (geçersiz fd) · `EMFILE` (fd taşması) · izleme olay taşması (overflow) |
| Dosyalar | `bellek-yonetim-api.md` · `dosya-sistemi-api.md` · `dosya-takibi-ve-degisiklik-api.md` |
| Sınır | `isletim-sistemi-core` (servis) · K001+ (uygulama kodu) |

### §B.3 `suresin-ve-zamanlama-api/` — detay

| Alan | Değer |
|---|---|
| Sorumluluk | `fork/vfork/clone` · `exec` ailesi · `wait/waitpid` · zombie/orphan · `signal`/`sigaction` · `select/poll` · `epoll/kqueue/IOCP` arayüzlerinin belgelenmesi |
| Girdi | Yeni görüntü argümanları · sinyal numarası + işleyici · izlenecek fd kümesi |
| Çıktı | Çocuk pid · çıkış durumu (`WIFEXITED` · `WEXITSTATUS`) · hazır fd kümesi |
| Hata modu | `ECHILD` (izlenecek çocuk yok) · `EINTR` (sinyalle kesilme) · fd sızması · zombie yığılması |
| Dosyalar | `suresin-yonetim-api.md` · `sinyal-api.md` · `cok-islev-ve-bekleme-api.md` |
| Sınır | `isletim-sistemi-kernel` (zamanlayıcı) · `isletim-sistemi-observability` (ölçüm) |

### §B.4 `ag-api/` — envanter satırı (2. ajan)

| Alan | Değer |
|---|---|
| Sorumluluk | Soket API'si, adres ailesi, bağlanma/kabul döngüsü, DNS çözümleme |
| Sahip | 2. ajan — bu oturumda dosya üretilmez |
| Bu dalın teması | Yalnız envanter satırı (S4) ve `children` kaydı |
| Hata modu | ⚠️ VERIFICATION REQUIRED — envanter dışı detay |

### §B.5 `platform-api/` — envanter satırı (2. ajan)

| Alan | Değer |
|---|---|
| Sorumluluk | OS-bağımsız platform yüzeyi: başlıklar, yükleyici, çalışma zamanı köprüsü |
| Sahip | 2. ajan — bu oturumda dosya üretilmez |
| Bu dalın teması | Yalnız envanter satırı (S5) ve `children` kaydı |
| Hata modu | ⚠️ VERIFICATION REQUIRED — envanter dışı detay |

## §C — Arayüzler (üç katman derinlemesine)

`index.md` §1'deki üç katman burada açılır. Katman numaraları **sabıttır**: 1 = syscall,
2 = libc, 3 = platform.

### §C.1 Katman 1 — syscall (çekirdek sözleşmesi)

| # | Öğe | Değer | Dayanak |
|---|---|---|---|
| C1.1 | Çağrı girişi | uygulama → `syscall` numarası → çekirdek giriş kapısı | `syscalls(2)` |
| C1.2 | x86-64 numara yazmacı | `rax` = syscall numarası | `syscall(2)` |
| C1.3 | x86-64 argüman yazmaçları | `rdi` · `rsi` · `rdx` · `r8` · `r9` (6 argüman) | `syscall(2)` |
| C1.4 | x86-64 dönüş yazmacı | `rax` = sonuç veya `-errno` | `syscall(2)` |
| C1.5 | ARM64 giriş | `svc #0` · numara `x8` · argüman `x0..x5` | `⚠️ VERIFICATION REQUIRED` (doküman bu turda doğrulanmadı) |
| C1.6 | Kullanım yeri | yalnız kütüphane yazarları ve taşınabilirlik köprüleri | bu dal §A.1 A12 |
| C1.7 | Yasak | uygulama kodunun `syscall()` doğrudan çağırması (CoreMusic yüzeyi değil) | `os-master.md` §2 |

### §C.2 Katman 2 — kütüphane wrapper (libc)

| # | Öğe | Değer | Dayanak |
|---|---|---|---|
| C2.1 | Sarmalama | libc, syscall numarasını saklar ve imzayı korumalı fonksiyona çevirir | glibc `System Calls` |
| C2.2 | Hata köprüsü | çekirdeğin `-errno` dönüşünü yakalar ve `errno` yazar | https://man7.org/linux/man-pages/man3/errno.3.html |
| C2.3 | `errno` bağlamı | başarısızlıkta **negatif değil**, `errno` dolu ve dönüş değeri `-1` | `errno(3)` |
| C2.4 | `errno` doğası | kütüphane değişkeni — başarılı çağrı onu **temizlemez** | `errno(3)` |
| C2.5 | Yazmac ve `errno` | yalnızca `errno` yazan çağrılar hata dönüşünde güvenilirdir | `errno(3)` |
| C2.6 | POSIX sargı adları | `open` · `read` · `write` · `close` · `fork` · `execve` · `waitpid` | POSIX.1-2024 |
| C2.7 | CoreMusic yüzeyi | PHP `shell_exec`/`proc_open`/`realpath` → C çalışma zamanı → libc | `os-master.md` §2 katman 3 |
| C2.8 | Sınır | bu dalın **dokunabildiği en son katman** | `index.md` §2 |

### §C.3 Katman 3 — platform API'si (başlık tabanlı yüzey)

| # | Öğe | Platform | Dayanak |
|---|---|---|---|
| C3.1 | Core Audio başlıkları | Windows (`Audioclient.h` · `Audiopolicy.h`) | dogrulama §1/4 |
| C3.2 | AAudio | Android native C ses API'si | dogrulama §1/5 |
| C3.3 | AudioToolbox | macOS ses servisi API'si | dogrulama §1/8 |
| C3.4 | Karakter | syscall **değil** — çalışma zamanı/servis katmanı | bu dal §A.1 A6-A8 |
| C3.5 | Taşınabilirlik | POSIX katman 2 ile örtüşür, platform genişletmeleri ayrı | `os-master.md` §2 |
| C3.6 | CoreMusic ilişkisi | ses platform yüzeyi Q-P2 kapsamında belirsiz | Q-P2 ⚠️ |

### §C.4 Katman karşılaştırma özeti

| Kriter | Katman 1 syscall | Katman 2 libc | Katman 3 platform |
|---|---|---|---|
| Ne sunar | çekirdek hizmeti | çekirdek hizmeti + hata çevrimi | OS/uygulama servisi |
| İmza | sayı + register | C prototipi | başlık (`.h`) |
| Taşınabilirlik | yok (mimariye bağlı) | POSIX ile yüksek | platforma özel |
| Hata gösterimi | `-errno` | dönüş `-1` + `errno` | HRESULT/`DWORD`/`OSStatus` ⚠️ |
| CoreMusic erişimi | dolaylı (üretilmez) | dolaylı (PHP→C→libc) | dolaylı |
| Kullanım sıklığı | nadir | çok sık | seçili alanlarda |

> **§C.4 satırı "HRESULT/`DWORD`/`OSStatus`" üçlüsü bu turda tek tek doğrulanmadı →
> `⚠️ VERIFICATION REQUIRED`.** Çekirdek iddia (üç platformun da hata kodu taşıdığı)
> `§A.1 A6-A8` kanıtlarıyla sınırlıdır.

## §D — Bağımlılık grafiği

```text
os-master.md (§2 katman · §3 çocuk · §8 Q2)
        │
        ▼
isletim-sistemi-api  (bu dal)
   ├─ §A.1 A1-A10  ──► POSIX.1-2024 · syscalls(2) · glibc
   │
   ├─ syscall/ ──────────────► syscall(2) · errno(3) · seccomp(2) · capabilities(7)
   │        │
   │        └── (bağımlı) ──► isletim-sistemi-kernel
   │
   ├─ bellek-ve-dosya-api/ ──► mmap(2) · open(2) · inotify(7) · FSEvents · ReadDirectoryChangesW
   │        │
   │        └── (bağımlı) ──► isletim-sistemi-core
   │
   ├─ suresin-ve-zamanlama-api/ ─► fork(2) · execve(2) · wait(2) · sigaction(2) · epoll_ctl(2)
   │        │
   │        └── (bağımlı) ──► isletim-sistemi-kernel · isletim-sistemi-observability
   │
   ├─ ag-api/      (2. ajan) ──► soket yüzeyi
   │
   └─ platform-api/ (2. ajan) ──► platform başlıkları
```

| # | Kenar | Kaynak → Hedef | Tür |
|---|---|---|---|---|
| E1 | üst referans | `isletim-sistemi-api` → `os-master.md` | doküman |
| E2 | çağrı akışı | uygulama → libc → çekirdek | çalışma zamanı |
| E3 | hata akışı | çekirdek → `-errno` → libc → `errno` → uygulama | çalışma zamanı |
| E4 | kanıt akışı | bu dal → 5 repo dosyası | disk |
| E5 | sınır | bu dal → `isletim-sistemi-kernel` | mimari |
| E6 | sınır | `bellek-ve-dosya-api` → `isletim-sistemi-core` | mimari |
| E7 | paylaşım | `suresin-ve-zamanlama-api` → `isletim-sistemi-observability` | ölçüm |
| E8 | bağımlılık | `syscall/` → `bellek-ve-dosya-api/` (fd'ler bellek üzerinden taşınır) | iç |
| E9 | bağımlılık | `syscall/` → `suresin-ve-zamanlama-api/` (sinyaller çağrıları keser) | iç |
| E10 | devralma | `ag-api/` · `platform-api/` → 2. ajan | organizasyon |

## §E — Sınır komşuları

| # | Dışında olan | Nereye gider | Ayrım kriteri |
|---|---|---|---|
| F1 | Çekirdek içi yürütme: preemption, zamanlama, RT yapılandırması | `isletim-sistemi-kernel` | içeride akış, burada sözleşme |
| F2 | Merkez OS servislerinin kendisi | `isletim-sistemi-core` | içeride ne sunduğu, burada ne sorulabildiği |
| F3 | Ses uç noktası akış davranışı | `isletim-sistemi-audio` | API yüzeyi burada, akış orada |
| F4 | OS log/metric/trace | `isletim-sistemi-observability` | ölçüm kendisi, ölçüm arayüzü burada değil |
| F5 | Uygulama testinin kendisi (PHPUnit) | `isletim-sistemi-testing` | test stratejisi, API kanıtı burada |
| F6 | CoreMusic uygulama kodunun iş mantığı | K001+ | çağrı burada, niyet orada |
| F7 | `electroncis` dalı ile sınır | Q1 kapalı değil | UNKNOWN (dogrulama §3/23) |
| F8 | `isletim-sistemi-security` | güvenlik politikası sahibi | burada mekanizma, orada politika |
| F9 | `ag-api/` · `platform-api/` | 2. ajan | dosya beyazlistesi |

## §F — Açık sorular (Q-P1..Q-P4)

| # | Soru | Etiket | Sahip | Etki |
|---|---|---|---|---|
| Q-P1 | Hangi API yüzeyi kastediliyor — kernel API mi, kullanıcı uzay hizmetleri mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward | kapsam sınırı |
| Q-P2 | Platform ses API'sinin yüzeyi burada mı, `isletim-sistemi-audio` içinde mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward | §C.3 kapsamı |
| Q-P3 | Hedef platform API seti (Q4) — syscall seviyesi mi, kütüphane API'si mi? | ⚠️ VERIFICATION REQUIRED | Vault Steward | §C katman önceliği |
| Q-P4 | core ↔ kernel sınırına bu dal nasıl temas eder (Q2)? | ⚠️ VERIFICATION REQUIRED | Vault Steward | §D kenarları |

## §G — Alt ayrım noktaları

| # | Ayrım | Ayıran şey | Durum |
|---|---|---|---|
| C1 | syscall ↔ kütüphane wrapper | çekirdeğe geçiş mi, kayıt/çeviri mi | ✅ kanıtlı (dogrulama §1/1·2·3) |
| C2 | Çekirdek sözleşmesi ↔ platform API'si | Linux `syscall` numarası vs Windows/Android başlık API'si | ✅ kanıtlı (§1/2·4·5·6) |
| C3 | API yüzeyi ↔ API'nin davrandığı alt sistem | sözleşme burada, akış davranışı audio'da | `[TASARIM]` — Q-P2 bekliyor |
| C4 | "Kernel API mi, kullanıcı uzay servisleri mi?" | hangi yüzeyin kastedildiği | ⚠️ Q-P1 açık |
| C5 | POSIX geneli ↔ platforma özel genişletme | taşınabilirlik sınırı | `[TASARIM]` — Q-P3/Q4 bekliyor |

## §H — Repo ile sınır (bu dalın gerçek temas noktaları)

| # | API katmanı | Gerçek repo dosyası | Sınır |
|---|---|---|---|
| R1 | `uname` sargısı | `shared/src/Api/Bff/DesktopBff.php:35` | libc sargısı (katman 3) |
| R2 | `execve` sargısı | `shared/src/AI/AIEngine.php:288-310` | libc sargısı + argüman kaçışı |
| R3 | `posix_spawn` sargısı | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | libc sargısı |
| R4 | `stat`/`realpath` sargısı | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185-215` | libc sargısı |
| R5 | SAPI `header()` | `shared/src/PageRouter/ResponseEmitter.php` | PHP SAPI — syscall **değil** |

> R5 kasıtlı olarak işaretlenmiştir: HTTP başlığı syscall'ın **önüdür**, syscall **değildir**.

## §I — Disk kanıtları

| # | İddia | Gerçek dosya yolu |
|---|---|---|
| E1 | Çocuk satırı: "Uygulamalara açılan OS çağrı yüzeyi · draft · ⚠️" | `os-master.md` §3 |
| E2 | Entegrasyon sınırı: "OS ↔ uygulama — Syscall / API yüzeyi (isletim-sistemi-api)" | `os-bagimliliklar.md` §1 B13 |
| E3 | Kademe-3 ön koşulu: "Q2 sınır kararı" | `os-master.md` §8 (Q2) |
| E4 | Çocuk index.md açık sorusu: kernel API mi, kullanıcı uzay hizmetleri mi | `isletim-sistemi-api/index.md` frontmatter |
| E5 | Kapsam notu: "Syscall, sistem servisleri ve uygulama sözleşmesi" | `isletim-sistemi-api/index.md` §1 |
| E6 | Kapsam içi tanım: "OS API yüzeyi" | `os-master.md` §2 (katman 3) |
| E7 | Doğrulanmış syscall kaynağı | dogrulama §1 satır 1-8 |
| E8 | 5 alt-dal `children` satırı | `isletim-sistemi-api/index.md` frontmatter (2026-10-13) |

## §J — Temsilî çağrı yüzeyi envanteri (kategori bazlı)

> Bu tablo **kapsamın sınırlayıcısıdır**: bu dal bu çağrı ailelerini *konu olarak* kapsar;
> her birinin implementasyonu `isletim-sistemi-kernel`'dedir.

| # | Kategori | Temsilî çağrı | Katman | Alt-dal |
|---|---|---|---|---|
| J1 | Süreç yaratma | `fork` · `vfork` · `clone` · `posix_spawn` | 2 libc | `suresin-ve-zamanlama-api/` |
| J2 | Görüntü değiştirme | `execve` · `execvp` · `execvpe` | 2 libc | `suresin-ve-zamanlama-api/` |
| J3 | Süreç sonlandırma | `exit` · `_exit` · `wait` · `waitpid` · `waitid` | 2 libc | `suresin-ve-zamanlama-api/` |
| J4 | Kimlik sorgu | `getpid` · `getppid` · `getuid` · `geteuid` | 2 libc | `suresin-ve-zamanlama-api/` |
| J5 | Sinyal gönderme | `kill` · `raise` · `pthread_kill` | 2 libc | `suresin-ve-zamanlama-api/` |
| J6 | Sinyal işleme | `signal` · `sigaction` · `sigprocmask` | 2 libc | `suresin-ve-zamanlama-api/` |
| J7 | Zaman ölçme | `clock_gettime` · `gettimeofday` · `clock_nanosleep` | 2 libc | `suresin-ve-zamanlama-api/` |
| J8 | Zamanlayıcı | `alarm` · `setitimer` · `timer_create` | 2 libc | `suresin-ve-zamanlama-api/` |
| J9 | Çok-islev | `select` · `poll` · `ppoll` | 2 libc | `suresin-ve-zamanlama-api/` |
| J10 | Çok-islev (ölçek) | `epoll_create` · `epoll_ctl` · `epoll_wait` | 1 syscall | `suresin-ve-zamanlama-api/` |
| J11 | Dosya açma/kapama | `open` · `openat` · `close` · `creat` | 2 libc | `bellek-ve-dosya-api/` |
| J12 | Dosya okuma/yazma | `read` · `write` · `pread64` · `pwrite64` | 2 libc | `bellek-ve-dosya-api/` |
| J13 | Konum | `lseek` · `dup` · `dup2` · `fcntl` | 2 libc | `bellek-ve-dosya-api/` |
| J14 | metadata | `stat` · `fstat` · `lstat` · `fstatat` | 2 libc | `bellek-ve-dosya-api/` |
| J15 | Kalıcılık | `fsync` · `fdatasync` · `ftruncate` | 2 libc | `bellek-ve-dosya-api/` |
| J16 | Denetim | `ioctl` · `access` · `faccessat` | 2 libc | `bellek-ve-dosya-api/` |
| J17 | Bellek eşleme | `mmap` · `munmap` · `mprotect` · `msync` | 2 libc | `bellek-ve-dosya-api/` |
| J18 | Bellek ipucu | `madvise` · `posix_fadvise` · `mlock` · `munlock` | 2 libc | `bellek-ve-dosya-api/` |
| J19 | Heap | `brk` · `sbrk` | 2 libc | `bellek-ve-dosya-api/` |
| J20 | Dosya olay izleme | `inotify_init` · `inotify_add_watch` | 1 syscall | `bellek-ve-dosya-api/` |
| J21 | Sınır | `pipe` · `socketpair` · `eventfd` | 2 libc | `suresin-ve-zamanlama-api/` |
| J22 | Sandbox | `prctl` · `seccomp` · `capget` · `capset` | 1 syscall | `syscall/` |
| J23 | İzleme | `ptrace` | 1 syscall | `syscall/` |
| J24 | Ham geçiş | `syscall(SYS_xxx, ...)` | 1 syscall | `syscall/` |
| J25 | Kütüphane köprüsü | glibc sargı fonksiyonları | 2 libc | `syscall/` |
| J26 | Platform yüzeyi | Core Audio başlıkları · AAudio · AudioToolbox | 3 platform | `platform-api/` (2. ajan) |
| J27 | Ağ yüzeyi | soket API'si · `connect` · `accept` · `send` · `recv` | 2 libc | `ag-api/` (2. ajan) |
| J28 | Dizin izleme (Windows) | `ReadDirectoryChangesW` | 3 platform | `bellek-ve-dosya-api/` |
| J29 | Dizin izleme (macOS) | FSEvents | 3 platform | `bellek-ve-dosya-api/` |
| J30 | Ham (PHP) | `shell_exec` · `proc_open` · `realpath` · `php_uname` | 2 libc (dolaylı) | repo kanıtı §H |

> **J26–J29** üç platformdaki dizin/platform izleme yüzeyidir; hangisinin CoreMusic'te
> kullanıldığı `UNKNOWN` (Q-P3 açık).

## §K — Terim sözlüğü (ELI15 · bu dalın kendi terimleri)

| # | Terim | ELI15 tanım | Kanıt |
|---|---|---|---|
| K1 | syscall | Uygulamanın çekirdeğe yönelttiği tek çağrı biçimi | `syscalls(2)` |
| K2 | ABI | Çağrı numarası + register sırası + dönüş kuralından oluşan görünmez sözleşme | `syscall(2)` |
| K3 | `errno` | Kütüphane değişkeni; başarısız çağrının nedenini sayı ile saklar | `errno(3)` |
| K4 | descriptor (`fd`) | Açık dosyayı temsil eden küçük tam sayı; 0/1/2 sırasıyla stdin/stdout/stderr | POSIX.1-2024 |
| K5 | eşleme (`mmap`) | Dosyayı veya anonim belleği adres uzayına serme | `mmap(2)` |
| K6 | demand paging | Sayfa ilk erişildiğinde fiziksel belleğe getirilir | ⚠️ VERIFICATION REQUIRED |
| K7 | vDSO | Çekirdeğin kullanıcı uzaya serdiği küçük kod parçası; bazı çağrıları kopya çeker | vdso(7) (envanterde mevcut) |
| K8 | context switch | Bir süreçten diğerine geçişin maliyeti | ⚠️ VERIFICATION REQUIRED |
| K9 | zombie | Bitmiş ama babası `wait` ile okumamış süreç girdisi | `wait(2)` |
| K10 | orphan | Babası ölmüş, `init`/`systemd`'ye devredilmiş süreç | ⚠️ VERIFICATION REQUIRED |
| K11 | seccomp-BPF | Sistem çağrılarını BPF filtresiyle süzen sandbox mekanizması | `seccomp(2)` (envanterde mevcut) |
| K12 | capability | `root` yetkisini parçalara bölen izin birimi | `capabilities(7)` (envanterde mevcut) |
| K13 | level-trigger | Hazır olan fd her bekleme dönüşünde bildirilir | ⚠️ VERIFICATION REQUIRED |
| K14 | edge-trigger | Yalnız hazır hâle geliş anında bildirilir | ⚠️ VERIFICATION REQUIRED |
| K15 | non-blocking | Çağrının bloklanmak yerine anında `EAGAIN` döndürmesi | ⚠️ VERIFICATION REQUIRED |
| K16 | idempotent | Çağrı tekrarlandığında aynı sonucu veren işlem | bu dal §B / errno |
| K17 | wrapper | syscall'ı sarmalayıp imza + hata çevrimi ekleyen kütüphane fonksiyonu | glibc `System Calls` |
| K18 | SAPI | PHP'nin çalışma zamanı arayüzü; syscall katmanının üstünde | repo §H R5 |
| K19 | sandbox | Uygulamanın erişebileceği çağrıları sınırlayan zarf | `syscall-guvenlik-ve-sandbox.md` |
| K20 | taşınabilirlik | Kodun değiştirilmeden başka POSIX sistemde çalışabilmesi | POSIX.1-2024 |

## §L — Risk matrisi

| # | Risk | Olasılık | Etki | Azaltma | Durum |
|---|---|---|---|---|---|
| L1 | Q-P1 kapanmazsa kapsam iki yana genişler | orta | yüksek | §A.1/§A.2 çift listesi ile sınır açık tutulur | açık |
| L2 | Q-P2 kapanmazsa platform ses yüzeyi iki dosyada tekrarlanır | orta | orta | §C.3.6 ile işaretli kalır | açık |
| L3 | Uydurma satır numarası | düşük | yüksek | her repo kanıtı `Get-Content -Skip/-First` ile doğrulanır | kontrol edildi (2026-10-13) |
| L4 | Envantere tekrar giren URL | orta | orta | 1690 URL envanterine karşı kesişim testi | kontrol edildi (2026-10-13) |
| L5 | `ag-api/` · `platform-api/`'ya yanlışlıkla yazma | düşük | yüksek | beyazliste dışı yazım yok | kontrol edildi (2026-10-13) |
| L6 | Frontmatter alan eksikliği | düşük | orta | 8 alan + `group` denetimi | kontrol edildi (2026-10-13) |
| L7 | `group` değerinin tırnaklanması | düşük | düşük | `group: mimari` biçiminde yazılır | kontrol edildi (2026-10-13) |
| L8 | Alt-dal adının değişmesi | düşük | yüksek | 5 ad sabit; §B tablosu tek referans | kontrol edildi (2026-10-13) |

## §M — Kapsam karar kaydı

| # | Karar | Gerekçe | Durum |
|---|---|---|---|
| M1 | Bu dal 3 katman taşır (syscall / libc / platform) | `os-master.md` §2 katman 3 + `index.md` §1 | ✅ kararlı |
| M2 | 5 alt-dal adı sabittir ve değişmez | 2 ajanlı üretim planı; `children` 5 satır | ✅ kararlı |
| M3 | `ag-api/` · `platform-api/` içeriği bu oturumda üretilmez | beyazliste ayrımı | ✅ kararlı |
| M4 | CoreMusic'in eriştiği en son katman libc sargısıdır | `os-master.md` §2 | ✅ kararlı |
| M5 | `header()` syscall değildir | PHP SAPI katmanı, örneği §H R5 | ✅ kararlı |
| M6 | Hangi yüzeyin kapsam olduğu henüz kararlı değil | Q-P1 | ⚠️ açık |
| M7 | Hedef platform seti (`UNKNOWN`) | Q-P3 | ⚠️ açık |

## §N — Kapsam iddialarının web doğrulama özeti

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| N1 | `intro(2)` çalışan program = süreç ve PID kavramını tanımlar | https://man7.org/linux/man-pages/man2/intro.2.html | 2026-10-13 | ✅ HTTP 200 |
| N2 | `errno` kütüphane değişkenidir, başarılı çağrı temizlemez | https://man7.org/linux/man-pages/man3/errno.3.html | 2026-10-13 | ✅ HTTP 200 |
| N3 | POSIX fonksiyon sayfaları taşınabilir API'nin kaynağıdır | https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html | 2026-10-13 | ✅ HTTP 200 |
| N4 | glibc süreç yaratma belgesi katman 2 kapsamını tanımlar | https://www.gnu.org/software/libc/manual/html_node/Process-Creation.html | 2026-10-13 | ✅ HTTP 200 |
| N5 | glibc hata kodları listesi §A.1 A14 dayanağıdır | https://www.gnu.org/software/libc/manual/html_node/Error-Codes.html | 2026-10-13 | ✅ HTTP 200 |

> Tam matris (id · URL · HTTP durumu) `isletim-sistemi-api-dogrulama.md` §E'dedir;
> bu tablo yalnızca kapsam iddialarını özetler.

## §O — Platform yüzeyi karşılaştırma matrisi

| # | Yüzey sorusu | Linux/POSIX | Windows | macOS | Android |
|---|---|---|---|---|---|
| O1 | Temel geçiş | `syscall` numarası + register | sistem servisi kütüphanesi | `syscall` numarası (XNU) | `syscall` numarası (Linux çekirdeği) |
| O2 | Kütüphane | glibc / musl | `kernel32` · `ntdll` | `libSystem` | Bionic libc |
| O3 | Hata gösterimi | `errno` (negatif → `errno`) | `GetLastError` | `errno` | `errno` |
| O4 | Dosya açma | `open` / `openat` | `CreateFile` | `open` / `open` | `openat` |
| O5 | Bellek eşleme | `mmap` | `VirtualAlloc` | `mmap` | `mmap` |
| O6 | Dizin izleme | `inotify` | `ReadDirectoryChangesW` | FSEvents | `inotify` |
| O7 | Çok-islev | `select`/`poll`/`epoll` | IOCP · `WaitForMultipleObjects` | `kqueue`/`select` | `epoll` |
| O8 | Süreç yaratma | `fork`+`execve` · `posix_spawn` | `CreateProcess` | `fork`+`execve` | `fork`+`execve` · `posix_spawn` |
| O9 | Sinyal | POSIX sinyalleri | ⚠️ VERIFICATION REQUIRED | POSIX sinyalleri | POSIX sinyalleri |
| O10 | Olay bildirimi | `signalfd` · `eventfd` | event nesneleri | kqueue filtreleri | `eventfd` |
| O11 | Sandbox | seccomp-BPF · capability | Job Object · AppContainer | ⚠️ VERIFICATION REQUIRED | SELinux · seccomp |
| O12 | İzleme | `ptrace` | debugger protokolü | `ptrace` | `ptrace` (sınırlı) |
| O13 | Saat çözünürlüğü | `clock_gettime` | `QueryPerformanceCounter` | `clock_gettime` | `clock_gettime` |
| O14 | Ses platform yüzeyi | ⚠️ bu dalda değil (`ag-api`/`platform-api`) | Core Audio başlıkları | AudioToolbox | AAudio |
| O15 | Dosya sistemi yolu ayıracı | `/` | `\` | `/` | `/` |
| O16 | Standart dosya tanımlayıcıları | 0 · 1 · 2 | `STDIN_HANDLE` · `STDOUT_HANDLE` · `STDERR_HANDLE` | 0 · 1 · 2 | 0 · 1 · 2 |
| O17 | Taşınabilirlik düzeyi | POSIX çekirdeği | düşük (Win32'ye özel) | POSIX çekirdeği | POSIX çekirdeği + Android genişletme |
| O18 | Q-P3 ile ilişki | `UNKNOWN` — hedef platform seti kararlı değil | `UNKNOWN` | `UNKNOWN` | `UNKNOWN` |

> **O1–O18 satırlarının tamamı bu oturumda tek tek URL ile doğrulanmadı →**
> yalnız `O14` (Core Audio · AAudio · AudioToolbox) `dogrulama §1/4·5·8` kanıtlarıyla
> desteklenir; diğer satırlar `⚠️ VERIFICATION REQUIRED` statüsündedir ve §N'deki
> doğrulanmış iddialarla **karıştırılmamalıdır**.

## §P — Kapsam giriş/çıkış akışı

| # | Adım | Giriş | İşlem | Çıkış |
|---|---|---|---|---|
| P1 | İstek | uygulama kodu | hizmet ihtiyacı doğar | çağrı niyeti |
| P2 | Sarma | libc | çağrı numarası + argüman hazırlanır | register yükü |
| P3 | Geçiş | çekirdek giriş kapısı | yazmaçlar okunur, yetki sınanır | iç çağrı |
| P4 | Yürütme | çekirdek içi servis | istek karşılanır | sonuç veya `-errno` |
| P5 | Dönüş | libc | `-errno` → `errno` yazılır, `-1` döner | görünür hata |
| P6 | Yorum | uygulama | `errno` okunur, karar verilir | dallanma |
| P7 | Yansıma | kullanıcı/arayüz | hata kodu insana çevrilir | mesaj |

## §Q — Bağımlılık doğrulama listesi

| # | Bağımlılık | Beklenen disk kanıtı | Durum |
|---|---|---|---|
| Q1 | `os-master.md` §3 çocuk satırı | "Uygulamalara açılan OS çağrı yüzeyi" | ✅ 2026-10-12 |
| Q2 | `os-bagimliliklar.md` §1 B13 | "OS ↔ uygulama — Syscall / API yüzeyi" | ✅ 2026-10-12 |
| Q3 | `os-master.md` §8 Q2 | Kademe-3 ön koşulu | ✅ 2026-10-12 |
| Q4 | `index.md` frontmatter `children` | 5 alt-dal satırı | ✅ 2026-10-13 |
| Q5 | `DesktopBff.php:35` | `php_uname('s')` | ✅ 2026-10-13 |
| Q6 | `AIEngine.php:288-310` | `shell_exec` + `escapeshellarg` | ✅ 2026-10-13 |
| Q7 | `ApiKeyCreateCommandTest.php:248-269` | `proc_open` | ✅ 2026-10-13 |
| Q8 | `MusicStreamHandler.php:185-215` | `realpath` + `is_link` | ✅ 2026-10-13 |
| Q9 | `ResponseEmitter.php` | `header()` satırları | ✅ 2026-10-13 |
| Q10 | 5 alt-dal `index.md` dosyaları | bu oturumda üretilir | ✅ 2026-10-13 |
| Q11 | `ag-api/` · `platform-api/` dosyaları | 2. ajanın beyazlistesi | ⛔ bu oturumda dokunulmadı |
| Q12 | `../index.md` (K000) | yalnız orkestratör | ⛔ bu oturumda dokunulmadı |

## §R — Bu turda yapılan kapsam genişlemesi (2026-10-13)

| # | Değişiklik | Eski | Yeni |
|---|---|---|---|
| R1 | `parent:` alanı `parent_id:` ile değiştirildi | `parent: K000-isletim-sistemi` | `parent_id: K000-isletim-sistemi` |
| R2 | Frontmatter 8 alan + `group` alanına tamamlandı | 7 alan eksik | tam |
| R3 | Bölüm iskeleti §A–§F'ye taşındı | §1–§7 | §A–§R |
| R4 | 5 alt-dal envanteri eklendi | yok | §B (S1–S5) |
| R5 | Üç katman derinleştirildi | 3 satırlık tablo | §C.1–§C.4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
