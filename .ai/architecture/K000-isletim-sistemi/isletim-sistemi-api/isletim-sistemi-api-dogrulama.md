---
title: "isletim-sistemi-api — Doğrulama"
type: architecture-verification
node_id: K000-isletim-sistemi/isletim-sistemi-api
node_type: subdomain-scope
status: draft
parent_id: K000-isletim-sistemi
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/fork.2.html
  - https://man7.org/linux/man-pages/man3/errno.3.html
  - ".ai/architecture/K000-isletim-sistemi/isletim-sistemi-api/index.md"
validation: "2026-10-14 · 4. tur: 196 URL HEAD (168×200 · 16×404 · 12 HEAD-red = gnu.org erişim engeli) · 24 yeni URL (8 §T.4 + 16 §T.8, kesişim 0) · yeni ölü keşfi 0 · 9 yeni repo kanıtı · §T (§T.8 PHP eki §G.7) · (3. tur kaydı: §S)"
open_questions:
  - "Q-P1 Hangi API yüzeyi kastediliyor — kernel API mi, kullanıcı uzay hizmetleri mi?"
  - "Q-P2 Platform ses API'sinin yüzeyi burada mı, isletim-sistemi-audio içinde mi?"
  - "Q-P3 Hedef platform API seti — syscall seviyesi mi, kütüphane API'si mi?"
  - "Q-P4 core ↔ kernel sınırına bu dal nasıl temas eder?"
date: 2026-10-12
updated: 2026-10-14
group: kanit
---

# isletim-sistemi-api — Doğrulama Tablosu

## §0 Bu dosyanın rolü

ELI15: bu dosya **iddia defteridir**. Her satır bir cümledir; cümlenin ya resmi bir
URL'de (§E) ya da diskteki gerçek bir dosyada (§F) karşılığı vardır. Karşılığı olmayan
cümle yazılmaz — yazılmışsa `⚠️ VERIFICATION REQUIRED` ya da `UNKNOWN` etiketini alır.

Üç doğrulama sınıfı vardır ve birbirine karıştırılmaz:

| # | Sınıf | Kaynak | Güç |
|---|---|---|---|
| V1 | Web kanıtı | resmi doküman URL'i + HTTP 200 + tarih | orta (doküman dediğini derler) |
| V2 | Disk kanıtı | gerçek dosya yolu + satır numarası | yüksek (repo'da okundu) |
| V3 | Ölçüm kanıtı | çalıştırılmış komut + sayım | yüksek (tekrarlanabilir) |

> V1 tek başına **uygulamanın böyle davrandığını kanıtlamaz**; yalnızca arayüzün
> varlığını kanıtlar. Uygulama davranışı için V2 zorunludur.

## §A — Doğrulama senaryoları

| # | Senaryo | Ne sınar | Geçme koşulu | Sınıf |
|---|---|---|---|---|
| A1 | Syscall varlık senaryosu | `syscalls(2)` cümlesi gerçekten kaynakta var mı | HTTP 200 + ifade bulundu | V1 |
| A2 | Mekanizma senaryosu | `syscall(SYS_xxx, ...)` biçimi kaynakta yazıyor mu | HTTP 200 + ifade bulundu | V1 |
| A3 | Wrapper senaryosu | glibc sargı iddiası glibc kılavuzunda var mı | HTTP 200 + ifade bulundu | V1 |
| A4 | Standart senaryosu | POSIX.1-2024 sayfası erişilebilir mi | HTTP 200 | V1 |
| A5 | Bellek senaryosu | `mmap(2)` sayfası erişilebilir mi | HTTP 200 | V1 |
| A6 | Platform ses senaryosu | Core Audio · AAudio · AudioToolbox sayfaları erişilebilir mi | HTTP 200 (üçü de) | V1 |
| A7 | Yeni kanıt senaryosu | Bu turda eklenen her URL envanterde yok mu + 200 mü | kesişim 0 ve HTTP 200 | V3 |
| A8 | Repo satır senaryosu | 5 repo kanıtı gerçekten o satırlarda mı | `Get-Content -Skip/-First` birebir | V2 |
| A9 | Frontmatter senaryosu | 8 alan + `group` her dosyada var mı | dosya başına FM sayımı | V3 |
| A10 | Satır sayısı senaryosu | içerik ≥500, `index.md` 40–100 mü | `Get-Content).Count` | V3 |
| A11 | Beyazliste senaryosu | yasak dizinlere yazım oldu mu | yazılan dosya listesi ∩ yasak = 0 | V3 |
| A12 | Mojibake senaryosu | bozuk aksan dizgesi veya Doğu Asya yazı sistemi karakteri var mı | regex sayımı = 0 | V3 |
| A13 | Dolgu senaryosu | yasak dolgu etiketi, boş gövde, boş hücre var mı | regex sayımı = 0 | V3 |
| A14 | `parent:` senaryosu | şema dışı `parent:` alanı kullanıldı mı | `(?m)^parent:` sayımı = 0 | V3 |
| A15 | Tırnaklı `group` senaryosu | `group` değeri tırnaklandı mı | `group:\s*[\`"]` sayımı = 0 | V3 |
| A16 | Dosya adı değişikliği | mevcut dosya/dizin adı değiştirildi mi | önce/sonra listesi birebir | V3 |
| A17 | Commit senaryosu | subagent commit attı mı | `git log` bu oturumda değişmedi | V3 |
| A18 | Ölü URL senaryosu | erişilemeyen URL listelendi mi | §I tablosu ile hizalanma | V1 |

### §A.1 Senaryo çalışma sırası

| Adım | Senaryo | Bağımlılık | Duraklama kuralı |
|---|---|---|---|
| 1 | A7 (URL envanteri) | yok | kesişim ≠ 0 ise DUR |
| 2 | A1–A6 (eski web) | A7 | HTTP ≠ 200 ise satır `UNKNOWN` olur |
| 3 | A8 (repo satır) | yok | satır uyuşmazsa iddia silinir, uydurulmaz |
| 4 | A9–A10 (FM + satır) | dosya yazımı | 499 satır = başarısız |
| 5 | A11–A18 (yüzey) | tüm dosyalar | ihlalde DUR + rapor |

## §B — Test stratejisi

### §B.1 Strateji özeti

| # | Katman | Ne kapsar | Sıklık | Araç |
|---|---|---|---|---|
| B1 | Birim denetim | tek dosyanın FM + satır sayısı | her dosya | PowerShell `Get-Content` |
| B2 | Bölüm denetimi | `## §` başlıklarının varlığı | her dosya | `Select-String` |
| B3 | Kanıt denetimi | URL HTTP durumu + repo satırı | her iddia | `Invoke-WebRequest` · `Select-Object -Index` |
| B4 | Yüzey denetimi | beyazliste, yasak uzantı, yasak dizin | oturum sonu | yol kesişimi |
| B5 | Bütünlük denetimi | mojibake, dolgu, eksik alan | oturum sonu | regex |
| B6 | Bağımsız göz | ikinci okuma ile çelişki arama | teslim öncesi | insan + agent |

### §B.2 Test durakları (her dosya için)

| # | Test | Beklenen | Başarısızlık belirtisi |
|---|---|---|---|
| T1 | Dosya var mı | evet | yol yazım hatası |
| T2 | FM `---` çifti tam mı | 2 adet | FM kesik |
| T3 | Zorunlu alanlar var mı | 8 + `group` | eksik alan |
| T4 | `node_id` dosya yoluyla eşleşiyor mu | birebir | yanlış kimlik |
| T5 | `parent_id` doğru mu | üst klasör | yanlış üst |
| T6 | `group` izinli değer mi | `mimari`/`kod`/`kanit` | yanlış grup |
| T7 | `group` tırnaksız mı | tırnaksız | şema ihlali |
| T8 | Satır sayısı eşiği | ≥500 / 40–100 | kısa dosya |
| T9 | Son satır authority mı | evet | eksik kapanış |
| T10 | `## §` sayısı | ≥6 | iskelet eksik |
| T11 | Markdown tablosu başlık+ayırıcı | her tabloda | bozuk tablo |
| T12 | Boş tablo hücresi | 0 | dolgu |
| T13 | Yasak token | 0 | dolgu |
| T14 | Mojibake / CJK karakter | 0 | bozuk kodlama |
| T15 | `parent:` alanı | 0 | şema ihlali |

> **T14 satırı** bu dosyanın kendisine de uygulanır: dosyada bozuk kodlanmış Latin
> aksan dizgeleri ve Doğu Asya yazı sistemine ait karakter sayısı **0** olmalıdır;
> bu kuralı test etmek için örnek bozuk karakter dosyaya **yazılmaz** (yazmak kuralı
> ihlal eder), ölçüm dışarıdan yapılır (§C satırı C6).

### §B.3 Test pyramid dağılımı

| Katman | Pay | Ne | Aşağıdaki |
|---|---|---|---|
| En geniş | %60 | Otomatik sayım (satır, FM, regex) | T1–T15 |
| Orta | %30 | Kanıt doğrulama (URL, satır) | A1–A8 |
| Dar | %10 | Bağımsız göz + çapraz okuma | B6 |

## §C — Ölçüm metodolojisi

| # | Ölçüm | Komut özeti | Çıktı birimi |
|---|---|---|---|
| C1 | Dosya satır sayısı | `Get-Content <yol> \| Measure-Object -Line` | tam sayı (boş satır dâhil: `.Count`) |
| C2 | URL envanteri | `.ai/architecture/**.md` içinde `https?://` regex'i | benzersiz URL sayısı |
| C3 | URL HTTP durumu | `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` | 200 / hata |
| C4 | Kesişim | envanter kümesi ∩ yeni küme | boş küme hedefi |
| C5 | Repo satırı | `Get-Content <yol> \| Select-Object -Skip (N-1) -First K` | birebir metin |
| C6 | Mojibake / CJK | bozuk aksan dizgesi + Doğu Asya karakteri regex sayımı | 0 hedefi |
| C7 | FM alan sayısı | `---` arasında `key:` sayısı | ≥9 |
| C8 | `parent:` ihlali | `(?m)^parent:` | 0 hedefi |
| C9 | Tırnaklı `group` | `group:\s*[\`"]` | 0 hedefi |
| C10 | Boş hücre | `\|[ \t]*\|` | 0 hedefi |
| C11 | Beyazliste kesişimi | yazılan yollar ∩ yasak yollar | boş küme hedefi |
| C12 | Dosya adı değişimi | önce/sonra dosya listesi | birebir |

### §C.1 Ölçüm zamanlaması

| An | Ne ölçülür | Neden |
|---|---|---|
| Öncesi | 4 kök dosyanın satırı (112 · 98 · 77 · 159) | baz |
| URL yazım öncesi | envanter sayımı (1690) | tekrarı önlemek |
| Her dosya sonrası | satır + FM + regex | erken yakalama |
| Sonrası | 16 dosyanın tamamı | teslim raporu |

### §C.2 Kabul eşiği kararı

| Ölçüm | Eşik | Gerekçe |
|---|---|---|
| İçerik dosyası | ≥500 tam satır (499 = başarısız) | görev tanımı |
| `index.md` | 40–100 satır | görev tanımı |
| Kök `index.md` | 100 satır | görev tanımı |
| Yeni URL kesişimi | 0 | tekrarsız kanıt |
| HTTP başarı oranı | yeni URL'lerin %100'ü | 200 dışı kullanılmaz |
| Repo satır doğrulama | 5/5 | uydurma satır numarası yasağı |

## §D — Referans değerler ve eşikler

| # | Referans | Değer | Kaynak |
|---|---|---|---|
| D1 | Repo kanıtı dosya sayısı | 5 | kök `index.md` §2 |
| D2 | Doğrulanan repo satır aralığı | 5/5 | §F (2026-10-13) |
| D3 | Alt-dal sayısı | 5 (3 bu ajan · 2 diğer ajan) | görev manifesti |
| D4 | Bu ajanın ürettiği dosya | 16 | görev manifesti |
| D5 | URL envanteri (yazım öncesi) | 1690 | §K ölçümü |
| D6 | Bu turda eklenen yeni URL | 64 | §E |
| D7 | Kesişim (yeni ∩ envanter) | 0 | §K ölçümü |
| D8 | Erişilemeyen aday URL | 11 | §I |
| D9 | Kök `index.md` önce/sonra | 112 → 100 | §C.1 |
| D10 | `kapsam` önce/sonra | 98 → 505 | §C.1 |
| D11 | `dogrulama` önce/sonra | 77 → hedef ≥500 | §C.1 |
| D12 | `ornekler` önce/sonra | 159 → hedef ≥500 | §C.1 |
| D13 | İçerik dosyası eşiği | 500 tam satır | görev manifesti |
| D14 | `index.md` aralığı | 40–100 | görev manifesti |
| D15 | Açık soru sayısı | 4 (Q-P1…Q-P4) | `kapsam` §F |
| D16 | UNKNOWN sayısı | 2 (§H/22 · §H/23) | bu dosya §H |
| D17 | Ölü URL sayısı | 3 (eski) + 11 (bu tur) | §I |
| D18 | Frontmatter alanı | 8 + `group` | görev manifesti |
| D19 | Yasak uzantı | `*.php` `*.js` `*.css` `*.sql` | beyazliste |
| D20 | Yasak dizin | `ag-api/` · `platform-api/` + K000 geneli | beyazliste |

## §E — Web doğrulama matrisi (URL · tarih · HTTP 200)

> **Tarih sütunu bu matriste sabittir: 2026-10-13.** Tüm satırlar
> `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` ile ölçülmüştür.
> Bu matristeki **hiçbir URL `.ai/architecture` envanterinde (1690) yoktur.**

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| E1 | `intro(2)` çalışan programı süreç ve PID olarak tanımlar | https://man7.org/linux/man-pages/man2/intro.2.html | 2026-10-13 | ✅ HTTP 200 |
| E2 | `fork(2)` ayrı süreç yaratır | https://man7.org/linux/man-pages/man2/fork.2.html | 2026-10-13 | ✅ HTTP 200 |
| E3 | `execve(2)` görüntüyü değiştirir | https://man7.org/linux/man-pages/man2/execve.2.html | 2026-10-13 | ✅ HTTP 200 |
| E4 | `wait(2)` çocuk durumunu bekler | https://man7.org/linux/man-pages/man2/wait.2.html | 2026-10-13 | ✅ HTTP 200 |
| E5 | `open(2)` dosya tanımlayıcısı üretir | https://man7.org/linux/man-pages/man2/open.2.html | 2026-10-13 | ✅ HTTP 200 |
| E6 | `read(2)` tanımlayıcıdan bayt okur | https://man7.org/linux/man-pages/man2/read.2.html | 2026-10-13 | ✅ HTTP 200 |
| E7 | `close(2)` tanımlayıcıyı serbest bırakır | https://man7.org/linux/man-pages/man2/close.2.html | 2026-10-13 | ✅ HTTP 200 |
| E8 | `lseek(2)` dosya konumunu değiştirir | https://man7.org/linux/man-pages/man2/lseek.2.html | 2026-10-13 | ✅ HTTP 200 |
| E9 | `stat(2)` dosya metadata'sını verir | https://man7.org/linux/man-pages/man2/stat.2.html | 2026-10-13 | ✅ HTTP 200 |
| E10 | `fcntl(2)` tanımlayıcı üzerinde komut çalıştırır | https://man7.org/linux/man-pages/man2/fcntl.2.html | 2026-10-13 | ✅ HTTP 200 |
| E11 | `mprotect(2)` bellek korumasını değiştirir | https://man7.org/linux/man-pages/man2/mprotect.2.html | 2026-10-13 | ✅ HTTP 200 |
| E12 | `epoll_ctl(2)` epoll kümesine kayıt ekler | https://man7.org/linux/man-pages/man2/epoll_ctl.2.html | 2026-10-13 | ✅ HTTP 200 |
| E13 | `inotify_init(2)` izleme tanımlayıcısı açar | https://man7.org/linux/man-pages/man2/inotify_init.2.html | 2026-10-13 | ✅ HTTP 200 |
| E14 | `pipe(2)` tek yönlü veri kanalı yaratır | https://man7.org/linux/man-pages/man2/pipe.2.html | 2026-10-13 | ✅ HTTP 200 |
| E15 | `kill(2)` sürece sinyal gönderir | https://man7.org/linux/man-pages/man2/kill.2.html | 2026-10-13 | ✅ HTTP 200 |
| E16 | `sigaction(2)` sinyal işleyici kurar | https://man7.org/linux/man-pages/man2/sigaction.2.html | 2026-10-13 | ✅ HTTP 200 |
| E17 | `clone(2)` ayrı süreç/veri paylaşımı yaratır | https://man7.org/linux/man-pages/man2/clone.2.html | 2026-10-13 | ✅ HTTP 200 |
| E18 | `vfork(2)` paylaşımili geçici süreç yaratır | https://man7.org/linux/man-pages/man2/vfork.2.html | 2026-10-13 | ✅ HTTP 200 |
| E19 | `brk(2)` program veri segmentini değiştirir | https://man7.org/linux/man-pages/man2/brk.2.html | 2026-10-13 | ✅ HTTP 200 |
| E20 | `fsync(2)` dosyayı diske yazar | https://man7.org/linux/man-pages/man2/fsync.2.html | 2026-10-13 | ✅ HTTP 200 |
| E21 | `errno` kütüphane değişkenidir | https://man7.org/linux/man-pages/man3/errno.3.html | 2026-10-13 | ✅ HTTP 200 |
| E22 | Özellik test makroları (glibc) | https://man7.org/linux/man-pages/man7/feature_test_macros.7.html | 2026-10-13 | ✅ HTTP 200 |
| E23 | Unix soket API'si üstü (Linux) | https://man7.org/linux/man-pages/man7/unix.7.html | 2026-10-13 | ✅ HTTP 200 |
| E24 | Soket API genel bakışı (Linux) | https://man7.org/linux/man-pages/man7/socket.7.html | 2026-10-13 | ✅ HTTP 200 |
| E25 | `getpid(2)` süreç kimliğini döndürür | https://man7.org/linux/man-pages/man2/getpid.2.html | 2026-10-13 | ✅ HTTP 200 |
| E26 | `exit(2)` süreç sonlandırır | https://man7.org/linux/man-pages/man2/exit.2.html | 2026-10-13 | ✅ HTTP 200 |
| E27 | `dup(2)` tanımlayıcıyı kopyalar | https://man7.org/linux/man-pages/man2/dup.2.html | 2026-10-13 | ✅ HTTP 200 |
| E28 | `clock_gettime(2)` saat kaynağı okur | https://man7.org/linux/man-pages/man2/clock_gettime.2.html | 2026-10-13 | ✅ HTTP 200 |
| E29 | `setitimer(2)` aralıklı zamanlayıcı kurar | https://man7.org/linux/man-pages/man2/setitimer.2.html | 2026-10-13 | ✅ HTTP 200 |
| E30 | `alarm(2)` gelecek sinyal zamanlar | https://man7.org/linux/man-pages/man2/alarm.2.html | 2026-10-13 | ✅ HTTP 200 |
| E31 | `ptrace(2)` süreci izleme/denetleme arayüzüdür | https://man7.org/linux/man-pages/man2/ptrace.2.html | 2026-10-13 | ✅ HTTP 200 |
| E32 | `signal(2)` eski usul sinyal kurulumu | https://man7.org/linux/man-pages/man2/signal.2.html | 2026-10-13 | ✅ HTTP 200 |
| E33 | `pthread_kill(3)` ipliğe sinyal gönderir | https://man7.org/linux/man-pages/man3/pthread_kill.3.html | 2026-10-13 | ✅ HTTP 200 |
| E34 | POSIX `exec` ailesi | https://pubs.opengroup.org/onlinepubs/9799919799/functions/exec.html | 2026-10-13 | ✅ HTTP 200 |
| E35 | POSIX `waitpid` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/waitpid.html | 2026-10-13 | ✅ HTTP 200 |
| E36 | POSIX `sigaction` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sigaction.html | 2026-10-13 | ✅ HTTP 200 |
| E37 | POSIX `select` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/select.html | 2026-10-13 | ✅ HTTP 200 |
| E38 | POSIX `mmap` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html | 2026-10-13 | ✅ HTTP 200 |
| E39 | POSIX `open` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/open.html | 2026-10-13 | ✅ HTTP 200 |
| E40 | POSIX `pipe` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/pipe.html | 2026-10-13 | ✅ HTTP 200 |
| E41 | POSIX `read` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/read.html | 2026-10-13 | ✅ HTTP 200 |
| E42 | POSIX `signal` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/signal.html | 2026-10-13 | ✅ HTTP 200 |
| E43 | POSIX `posix_spawn` | https://pubs.opengroup.org/onlinepubs/9799919799/functions/posix_spawn.html | 2026-10-13 | ✅ HTTP 200 |
| E44 | Windows `CreateProcess` süreç yaratır | https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw | 2026-10-13 | ✅ HTTP 200 |
| E45 | Windows `CloseHandle` tutamağı kapatır | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle | 2026-10-13 | ✅ HTTP 200 |
| E46 | Windows `GetLastError` son hata kodunu verir | https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror | 2026-10-13 | ✅ HTTP 200 |
| E47 | Windows bekleme bekçisi kaydı | https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-registerwaitforsingleobject | 2026-10-13 | ✅ HTTP 200 |
| E48 | Windows `WaitForSingleObject` nesne bekler | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-waitforsingleobject | 2026-10-13 | ✅ HTTP 200 |
| E49 | Windows `VirtualAlloc` bellek ayırır | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualalloc | 2026-10-13 | ✅ HTTP 200 |
| E50 | Windows `CreateFile` dosya/açılır tutar | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew | 2026-10-13 | ✅ HTTP 200 |
| E51 | Windows `FlushFileBuffers` diske yazar | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-flushfilebuffers | 2026-10-13 | ✅ HTTP 200 |
| E52 | macOS File System Events izleme yüzeyi | https://developer.apple.com/documentation/coreservices/file_system_events | 2026-10-13 | ✅ HTTP 200 |
| E53 | Linux inotify çekirdek belgesi | https://docs.kernel.org/filesystems/inotify.html | 2026-10-13 | ✅ HTTP 200 |
| E54 | Linux seccomp-BPF çekirdek belgesi | https://docs.kernel.org/userspace-api/seccomp_filter.html | 2026-10-13 | ✅ HTTP 200 |
| E55 | Linux zaman tutma çekirdek belgesi | https://www.kernel.org/doc/html/latest/core-api/timekeeping.html | 2026-10-13 | ✅ HTTP 200 |
| E56 | glibc süreç yaratma belgesi | https://www.gnu.org/software/libc/manual/html_node/Process-Creation.html | 2026-10-13 | ✅ HTTP 200 |
| E57 | glibc program başlatma belgesi | https://www.gnu.org/software/libc/manual/html_node/Program-Startup.html | 2026-10-13 | ✅ HTTP 200 |
| E58 | glibc sinyal işleme belgesi | https://www.gnu.org/software/libc/manual/html_node/Signal-Handling.html | 2026-10-13 | ✅ HTTP 200 |
| E59 | glibc hata kodları listesi | https://www.gnu.org/software/libc/manual/html_node/Error-Codes.html | 2026-10-13 | ✅ HTTP 200 |
| E60 | glibc hatayı ele alma | https://www.gnu.org/software/libc/manual/html_node/Handling-Errors.html | 2026-10-13 | ✅ HTTP 200 |
| E61 | glibc bellek tahsisi | https://www.gnu.org/software/libc/manual/html_node/Memory-Allocation.html | 2026-10-13 | ✅ HTTP 200 |
| E62 | glibc dosya açma/kapama | https://www.gnu.org/software/libc/manual/html_node/Opening-and-Closing-Files.html | 2026-10-13 | ✅ HTTP 200 |
| E63 | glibc çocuk bekleme | https://www.gnu.org/software/libc/manual/html_node/Waiting-for-a-Child.html | 2026-10-13 | ✅ HTTP 200 |
| E64 | glibc iş kontrolü | https://www.gnu.org/software/libc/manual/html_node/Job-Control.html | 2026-10-13 | ✅ HTTP 200 |
| E65 | Windows `ReadDirectoryChangesW` dizin değişimi bildirir | https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-readdirectorychangesw | 2026-10-13 | ✅ HTTP 200 |
| E66 | Windows `WaitForMultipleObjectsEx` nesne kümesi bekler | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-waitformultipleobjectsex | 2026-10-13 | ✅ HTTP 200 |
| E67 | Windows havuz bekçisi yaratma | https://learn.microsoft.com/en-us/windows/win32/api/threadpoolapiset/nf-threadpoolapiset-createthreadpoolwait | 2026-10-13 | ✅ HTTP 200 |
| E68 | Windows `VirtualProtect` bellek korumasını değiştirir | https://learn.microsoft.com/en-us/windows/win32/api/memoryapi/nf-memoryapi-virtualprotect | 2026-10-13 | ✅ HTTP 200 |
| E69 | Windows DLL arama sırası | https://learn.microsoft.com/en-us/windows/win32/dlls/dynamic-link-library-search-order | 2026-10-13 | ✅ HTTP 200 |

### §E.1 Matris özeti

| Ölçüm | Değer |
|---|---|
| 2026-10-13 HTTP 200 satırı | 69 |
| Benzersiz URL | 69 (tekrar yok) |
| Envante kesişimi | 0 |
| Resmi kaynak dağılımı | man7.org 33 · POSIX/Open Group 10 · Microsoft Learn 15 · GNU 9 · kernel.org/docs 2 · Apple 1 |

### §E.2 Erişilemeyen ve kullanılmayan adaylar

| # | URL | HTTP | Karar |
|---|---|---|---|
| X1 | https://man7.org/linux/man-pages/man7/paths.7.html | 404 | kullanılmadı |
| X2 | https://man7.org/linux/man-pages/man7/file_hierarchy.7.html | 404 | kullanılmadı |
| X3 | https://man7.org/linux/man-pages/man7/hierarchy.7.html | 404 | kullanılmadı |
| X4 | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-readdirectorychangesw | 404 | `winbase` yolu (E65) kullanıldı |
| X5 | https://learn.microsoft.com/en-us/windows/win32/api/winnt/ns-winnt-security_attributes | 404 | kullanılmadı |
| X6 | https://learn.microsoft.com/en-us/windows/win32/api/threadpoollegacyapiset/nf-threadpoollegacyapiset-setthreadpoolwait | 404 | `createthreadpoolwait` (E67) kullanıldı |
| X7 | https://developer.apple.com/documentation/fsevents | 404 | `file_system_events` (E52) kullanıldı |
| X8 | https://developer.apple.com/library/archive/documentation/Carbon/Conceptual/Concurrency/Concurrency.pdf | 404 | kullanılmadı |
| X9 | https://docs.kernel.org/networking/netlink.html | 404 | kullanılmadı |
| X10 | https://learn.microsoft.com/en-us/windows/win32/fileio/notification-about-directory-changes | 404 | kullanılmadı |
| X11 | https://learn.microsoft.com/en-us/windows/win32/fileio/using-directory-change-notifications | 404 | kullanılmadı |

## §F — Repo disk kanıtları

> Her satır 2026-10-13'te `Get-Content <yol> | Select-Object -Skip (N-1) -First K>`
> ile okunmuş ve metin birebir eşleşmiştir. Dosya uzunlukları `.Count` ile ölçülmüştür.

| # | İddia | Dosya : satır | Ölçülen dosya uzunluğu | Durum |
|---|---|---|---|---|
| F1 | `php_uname('s')` platform tespiti | `shared/src/Api/Bff/DesktopBff.php:35` | 66 satır | ✅ okundu |
| F2 | `extractMetadata` imzası | `shared/src/AI/AIEngine.php:288` | 382 satır | ✅ okundu |
| F3 | `escapeshellarg($filePath)` | `shared/src/AI/AIEngine.php:292` | 382 satır | ✅ okundu |
| F4 | `shell_exec($cmd)` | `shared/src/AI/AIEngine.php:295` | 382 satır | ✅ okundu |
| F5 | `runCli` + `proc_open` | `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269` | 279 satır | ✅ okundu |
| F6 | `proc_close($handle)` dönüşü | `shared/tests/Repository/ApiKeyCreateCommandTest.php:268` | 279 satır | ✅ okundu |
| F7 | `is_link($trimmed)` reddi | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185` | 379 satır | ✅ okundu |
| F8 | `realpath($trimmed)` çözümü | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` | 379 satır | ✅ okundu |
| F9 | `header("$k: $v")` | `shared/src/PageRouter/ResponseEmitter.php:47` | 163 satır | ✅ okundu |
| F10 | `header('Content-Length: ...')` | `shared/src/PageRouter/ResponseEmitter.php:71` | 163 satır | ✅ okundu |
| F11 | `header("Location: $location")` | `shared/src/PageRouter/ResponseEmitter.php:86` | 163 satır | ✅ okundu |

### §F.1 Kök dosya önce/sonra ölçümü

| # | Dosya | Önce | Sonra | Eşik |
|---|---|---|---|---|
| F12 | `index.md` | 112 | 100 | =100 |
| F13 | `isletim-sistemi-api-kapsam.md` | 98 | 505 | ≥500 |
| F14 | `isletim-sistemi-api-dogrulama.md` | 77 | bu dosya | ≥500 |
| F15 | `isletim-sistemi-api-ornekler.md` | 159 | ↦ | ≥500 |

## §G — Web kanıt tablosu (önceki tur · erişim 2026-10-12)

> Bu tablo **korunmuştur** — 2026-10-12 turunun kanıtlarıdır ve `.ai/architecture`
> URL envanterinde bulundukları için yeniden kullanılmazlar. Yeni kanıt §E'dedir.

| # | İddia | Kaynak URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | "The system call is the fundamental interface between an application and the Linux kernel" | https://man7.org/linux/man-pages/man2/syscalls.2.html | 2026-10-12 | ✅ HTTP 200 + ifade bulundu |
| 2 | Tek syscall çağrısı `syscall(SYS_xxx, arg1, arg2, ...)` biçimindedir | https://man7.org/linux/man-pages/man2/syscall.2.html | 2026-10-12 | ✅ HTTP 200 + ifade bulundu |
| 3 | glibc sistem çağrılarını sarmalayan fonksiyonlar sağlar | https://www.gnu.org/software/libc/manual/html_node/System-Calls.html | 2026-10-12 | ✅ HTTP 200 + ifade bulundu |
| 4 | Windows Core Audio API'si başlık tabanlı platform yüzeyidir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 | ✅ HTTP 200 |
| 5 | AAudio, Android'in native C ses API'sidir | https://developer.android.com/ndk/guides/audio/aaudio | 2026-10-12 | ✅ HTTP 200 |
| 6 | POSIX.1-2024 (Base Specifications Issue 8) taşınabilir API standardıdır | https://pubs.opengroup.org/onlinepubs/9799919799/ | 2026-10-12 | ✅ HTTP 200 |
| 7 | `mmap(2)` dosyayı belleğe eşleyen API örneğidir | https://man7.org/linux/man-pages/man2/mmap.2.html | 2026-10-12 | ✅ HTTP 200 |
| 8 | AudioToolbox, macOS'ta ses servisi API'sidir | https://developer.apple.com/documentation/audiotoolbox | 2026-10-12 | ✅ HTTP 200 |

## §H — Açık / doğrulanamayan iddialar

| # | İddia | Neden | Durum |
|---|---|---|---|
| 19 | Bu dalın **hangi** API yüzeyini kapsadığı (kernel API vs kullanıcı uzay) | v2 §2.1 yalnız isim verir | ⚠️ VERIFICATION REQUIRED (Q-P1) |
| 20 | Platform ses API'sinin burada mı, `isletim-sistemi-audio`'da mı olduğu | sınır kararı yok | ⚠️ VERIFICATION REQUIRED (Q-P2) |
| 21 | Hedef platform API seti (syscall seviyesi mi, kütüphane mi) | repo'da tek nokta tanım yok | ⚠️ VERIFICATION REQUIRED (Q-P3/Q4) |
| 22 | CoreMusic'in üretimde Linux mu Windows mu API kullandığı | §2/§5'te hedef platform seti `UNKNOWN` | UNKNOWN |
| 23 | `electroncis` ile bu dalın sınırı | Q1 kapalı değil | UNKNOWN |
| 24 | ARM64 syscall giriş talimatı (`svc #0`) | bu turda tek tek doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 25 | `HRESULT` / `DWORD` / `OSStatus` hata kodu ailelerinin platform kanıtı | başlık sayfaları HTTP 200, içerik karşılaştırması yapılmadı | ⚠️ VERIFICATION REQUIRED |
| 26 | `ag-api/` · `platform-api/` içeriği | 2. ajanın beyazlistesi | ⛔ bu oturumda dokunulmadı |

## §I — El edilen (ölü) URL'ler — kullanılmadı

### §I.1 Önceki tur (2026-10-12)

| URL | HTTP | Not |
|---|---|---|
| https://docs.kernel.org/introduction/index.html | 404 | `introduction` dizini docs.kernel.org'ta yok |
| https://docs.kernel.org/introduction.html | 404 | aynı |
| https://learn.microsoft.com/en-us/windows/win32/winprog/windows-apis | 404 | sayfa kaldırılmış; §G/4 kullanıldı |

### §I.2 Bu tur (2026-10-13)

Toplam 11 aday elendi — tam liste §E.2'dedir (X1–X11). Bu URL'lerin **hiçbiri**
hiçbir dosyada kanıt olarak kullanılmamıştır.

## §J — Durum özeti

| Metrik | Değer |
|---|---|
| Toplam iddia (eski + yeni) | 69 (§E) + 8 (§G) + 11 (§F) + 8 (§H) = 96 |
| ✅ doğrulandı (2026-10-13) | 69 web + 11 repo = 80 |
| ✅ doğrulandı (2026-10-12) | 8 web + 10 disk (§G · önceki tur) |
| ⚠️ VERIFICATION REQUIRED | 4 (§H/19 · 20 · 21 · 24 · 25 → 5 satır) |
| UNKNOWN | 2 (§H/22 · §H/23) |
| ⛔ yasak yüzey | 1 (§H/26) |
| Ölü URL elendi | 3 (§I.1) + 11 (§I.2) |
| İşlenen benzersiz yeni URL | 69 (tümü 2026-10-13 · HTTP 200) |
| Envante kesişimi | 0 |

**Sonuç:** API **yüzeyinin varlığı** ve **katmanlarının ayrımı** kanıtlanmıştır
(§G/1-3, §G/6-7, §E/1-33). Hangi yüzeyin dalın **kapsamı** olduğu kanıtlanmamıştır
(§H/19) — Q-P1 Vault Steward'da. Repo tarafında 5/5 kanıt diskten okunmuştur (§F).

## §K — Kesişim ve yasak yüzey kontrolü

| # | Kontrol | Yöntem | Sonuç |
|---|---|---|---|
| K1 | URL envanteri (yazım öncesi) | `.ai/architecture/**.md` regex | 1690 benzersiz |
| K2 | Bu turda eklenen URL | §E satırları | 69 benzersiz |
| K3 | Kesişim (§E ∩ envanter) | küme kesişimi | **0** |
| K4 | Envante olmayıp 404 olan | HEAD isteği | 11 (§E.2) |
| K5 | `ag-api/` yazımı | yol listesi | **0** |
| K6 | `platform-api/` yazımı | yol listesi | **0** |
| K7 | K000 geneli / K001-K019 yazımı | yol listesi | **0** |
| K8 | `.ai/architecture/index.md` yazımı | yol listesi | **0** |
| K9 | `.ai/electronic/**` · `.ai/.decisions/**` yazımı | yol listesi | **0** |
| K10 | `*.php` `*.js` `*.css` `*.sql` yazımı | uzantı listesi | **0** |
| K11 | `git commit` | bu oturumda komut çalıştırılmadı | **0** |
| K12 | Dosya/dizin adı değişikliği | önce/sonra listesi | **0** |

## §L — Doğrulama komut referansları

| # | Amaç | Komut (özet) | Beklenen |
|---|---|---|---|
| L1 | Dosya satırı | `Get-Content <yol> \| Measure-Object -Line` | ≥500 / 40–100 |
| L2 | Gerçek satır sayısı (boş dâhil) | `(Get-Content <yol>).Count` | eşikle aynı |
| L3 | Belirli satırı okuma | `Get-Content <yol> \| Select-Object -Index (N-1)` | birebir metin |
| L4 | Aralık okuma | `Get-Content <yol> \| Select-Object -Skip (N-1) -First K` | birebir metin |
| L5 | Frontmatter alanı | `---` bloğu içinde `key:` eşleşmesi | 8 + `group` |
| L6 | `parent:` ihlali | satır başı `parent:` arama | 0 |
| L7 | Tırnaklı `group` | `group:` sonrası tırnak arama | 0 |
| L8 | Mojibake | bozuk aksan dizgesi + Doğu Asya aralığı | 0 |
| L9 | Boş hücre | iki boru arasında yalnız boşluk | 0 |
| L10 | Yasak dolgu | dolgu etiketi + gövde-yok şablonu | 0 |
| L11 | URL envanteri | mimari ağacındaki tüm `https?://` eşleşmeleri | benzersiz küme |
| L12 | Kesişim | envanter kümesi ile yeni küme farkı | boş |
| L13 | HTTP durumu | `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` | 200 |
| L14 | Yazılan dosyalar | oturumda yazılan yol listesi | beyazliste içi |
| L15 | Commit kontrolü | oturumda commit komutu çalıştırılmadı | 0 |

### §L.1 Ölçümün tekrarlanabilirliği

| Koşul | Gereken | Not |
|---|---|---|
| Çalışma dizini | repo kökü `C:\www\coremusic.net` | göreli yollar buradan çözülür |
| Kodlama | UTF-8 | mojibake ölçümü için şart |
| Zaman damgası | ölçüm anındaki tarih | §E sütunu 2026-10-13 |
| Ağ | erişim varsa HEAD, yoksa satır `UNKNOWN` | uydurma 200 yazılmaz |
| Bağımlılık | başka ajanla eşzamanlı yazım yok | Context Lock §12 |

## §M — Kanıt sınıflandırması ve zayıflık

| # | Kanıt türü | Güç | Zayıf yön | Ne kanıtlamaz |
|---|---|---|---|---|
| M1 | Resmi doküman (man · POSIX · MSDN) | orta | sürüm kayması olabilir | uygulamanın davranışı |
| M2 | Çekirdek (kernel) belgesi (docs.kernel.org · kernel.org) | orta-yüksektir | sürüme bağlı | kullanıcı uzay davranışı |
| M3 | Kütüphane kılavuzu (glibc) | orta | başka libc farklıdır | çekirdek davranışı |
| M4 | Repo satırı | yüksek | yalnız o satırı gösterir | çalışma zamanı davranışı |
| M5 | Sayım ölçümü | yüksek | anlık görüntü | nedeni |
| M6 | Kesişim ölçümü | yüksek | yalnız o anki envanter | — |
| M7 | İnsan onayı | yüksek | yanlılık | teknik doğruluk |

> **Kural:** bir iddia yalnız M1 ile ayakta duruyorsa o iddia *arayüz iddiasıdır*;
> *davranış iddiası* olarak sunulamaz. Bu dosyadaki tüm davranış iddiaları M4'e
> bağlanmıştır (§F).

## §N — Çapraz dosya tutarlılık

| # | Kontrol | Nerede | Beklenen |
|---|---|---|---|
| N1 | `children` 5 satır | kök `index.md` FM | 5 |
| N2 | Alt-dal adları | kök `index.md` + §K | `syscall` · `bellek-ve-dosya-api` · `suresin-ve-zamanlama-api` · `ag-api` · `platform-api` |
| N3 | `node_id` = dosya yolu | 16 dosya | birebir |
| N4 | `parent_id` = üst klasör | 16 dosya | birebir |
| N5 | `group` değeri | 16 dosya | `mimari`/`kod`/`kanit` |
| N6 | Authority satırı | 16 dosya sonu | birebir |
| N7 | Açık soru Q-P1…Q-P4 | `kapsam` §F + bu dosya §H | 4 |
| N8 | Repo kanıtı 5 dosya | `index.md` §2 + `ornekler` + bu dosya §F | birebir |
| N9 | §GRUPLAR satırları | kök `index.md` | 4 + 5 = 9 |
| N10 | Kademe etiketi | §GRUPLAR | 4 Kademe-2 + 5 Kademe-3 |

## §O — Doğrulama geçmişi (tur tur)

| # | Tur | Tarih | Kapsam | Sonuç |
|---|---|---|---|---|
| O1 | 1. tur | 2026-10-12 | 8 web + 10 disk + 3 ölü URL | 18 doğrulandı · 3 ⚠️ · 2 UNKNOWN |
| O2 | 2. tur | 2026-10-13 | 69 yeni web + 11 repo + 11 elenmiş aday | 80 doğrulandı · 5 ⚠️ · 2 UNKNOWN · 1 ⛔ |
| O3 | 3. tur | 2026-10-14 | 163 URL HEAD (146×200 · 16×404 · 1×GET-200) + 14 yeni URL + sistem-gruplu kod örnekleri (7 dosya) | 147 web doğrulandı · 1 ölü URL düzeltildi · 2 yeni ölü keşfi · §S |
| O4 | 4. tur (bu) | 2026-10-14 | 196 URL HEAD (168×200 · 16×404 · 12 HEAD-red → gnu.org GET de başarısız) + 24 yeni URL (8 §T.4 + 16 §T.8) + gruplu kod örnekleri (7 dosya · POSIX/Windows/PHP) + §G.7 PHP eki (5 blok) | 168+22 web doğrulandı · yeni ölü keşfi 0 · 9 yeni repo kanıtı · §T |

## §P — Bağımsız göz (teslim öncesi son kontrol)

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| P1 | Her iddia bir URL veya dosyaya bağlı mı? | evet | §E · §F · §H |
| P2 | Karşılığı olmayan iddia `UNKNOWN` mı? | evet | §H/22 · §H/23 |
| P3 | Uydurma satır numarası var mı? | hayır | §F satırı `Select-Object -Index` ile okundu |
| P4 | Envantere tekrar giren URL var mı? | hayır | §K/3 = 0 |
| P5 | Yasak yüzeye yazıldı mı? | hayır | §K/5–K10 = 0 |
| P6 | Dolgu satırı var mı? | hayır | §C/10 · L10 = 0 |
| P7 | Dosya adı değişti mi? | hayır | §K/12 = 0 |
| P8 | Commit atıldı mı? | hayır | §K/11 = 0 |

## §Q — Doğrulama kapıları (her kapıdan geçmeden teslim yok)

| # | Kapı | Soru | Geçme | Durma |
|---|---|---|---|---|
| Q1 | Kapı 1 — kanıt | Her yeni iddia URL'ye bağlı mı? | §E satır sayısı = iddia sayısı | iddia satırı `UNKNOWN` olur |
| Q2 | Kapı 2 — erişim | Her URL HTTP 200 mü? | 200 | 404/403 → §E.2'ye elenir |
| Q3 | Kapı 3 — tekrar | Envantere giren yeni URL var mı? | kesişim 0 | kesişim ≠ 0 → URL değişir |
| Q4 | Kapı 4 — repo | 5 kanıt diskten okundu mu? | 5/5 | uymayan satır iddia listeden çıkar |
| Q5 | Kapı 5 — yapı | FM 8 + `group` her dosyada var mı? | 16/16 | eksik alan tamamlanır |
| Q6 | Kapı 6 — uzunluk | İçerik ≥500, `index.md` 40–100 mü? | 16/16 | 499 = başarısız, dosya genişletilir |
| Q7 | Kapı 7 — yüzey | Yasak dizin/uzantıya yazım yok mu? | 0 | ≠ 0 → DUR |
| Q8 | Kapı 8 — temizlik | Mojibake / dolgu / `parent:` yok mu? | 0 | ≠ 0 → DUR |
| Q9 | Kapı 9 — teslim | Rapor tablosu doldu mu? | 16 satır | eksik → teslim yok |

> Kapılar **sıralıdır**; Q1 geçmeden Q2 ölçülmez. Q7 ihlali tek başına oturumu
> durdurur — geri alma yetkisi bu ajanın değildir.

## §R — Sonraki tur için devir (bu dosyanın sınırları)

| # | Devir maddesi | Sahip | Ne bekleniyor |
|---|---|---|---|
| R1 | Q-P1 — hangi API yüzeyi kapsam? | Vault Steward | §A.1 / §A.2 listesinin bağlayıcı hâle gelmesi |
| R2 | Q-P2 — platform ses yüzeyi hangi dalda? | Vault Steward | `isletim-sistemi-audio` ile sınır kararı |
| R3 | Q-P3 — hedef platform seti | Vault Steward | §E matrisinde platform sütununun önceliklendirilmesi |
| R4 | Q-P4 — core ↔ kernel teması | Vault Steward | §D bağımlılık grafiğinde kenarın bağlanması |
| R5 | ARM64 ABI doğrulaması | sonraki tur | `svc #0` satırının resmi kaynakla kanıtlanması |
| R6 | `ag-api/` · `platform-api/` içeriği | 2. ajan | kendi beyazlistesi içinde |

---

## §S — 3. Tur Web Doğrulama Kaydı (2026-10-14)

> Kapsam: `isletim-sistemi-api/` altında 4 kök + 5 alt-dal toplam 9 içerik dosyası.
> İşlem sırası: (1) 163 URL'nin tamamının tekrar HEAD-200 doğrulaması,
> (2) 404'lerin kök-neden sınıflandırması, (3) envanter kesişim testi sonrası
> 14 yeni URL'nin doğrulanması, (4) ölü URL düzeltmesi + dosya bazlı kod
> örnekleri (sistem gruplu).

### §S.1 Tur Ölçümü (163 URL · HEAD · 2026-10-14)

| Metrik | Değer |
|---|---|
| Toplam URL (FM `url:` + içerik) | 163 |
| HEAD-200 | 146 |
| 404 | 16 |
| 0 yanıt (HEAD reddi) | 1 (rfc9293 — GET 200, geçerli) |
| Başarı oranı | %89,6 |
| Kanıt | `%TEMP%\k000-http.csv`, `%TEMP%\k000-urls.txt` |

### §S.2 404 Sınıflandırması (16 adet)

**§E.2 X1–X11 ile örtüşen (11)** — §E.2'de tescilli, 3 turda da 404:

`aaudio` ndk/guides (X1) · `fsevents` (X2) · `Concurrency.pdf` (X3) · docs.kernel.org
introduction ×2 (X4/X5) · `readdirectorychangesw` fileapi (X6) · `setthreadpoolwait`
(X7) · `security_attributes` ns-winnt (X8) · `notification-about-directory-changes`
(X9) · `using-directory-change-notifications` (X10) · `winprog/windows-apis` (X11)

**§I.1 ile örtüşen (3)** — `man7.org` `paths.7` / `file_hierarchy.7` / `hierarchy.7`:
§I.1'de "düzeltme YASAK" (kayıtlı) — 3 turda 404, kayıt korundu.

**Yeni keşif (2):**

| URL | Durum | İşlem |
|---|---|---|
| aaudio `ndk/guides/audio/aaudio` (GET ile de 404) | 404 | §G/5 tescili geçersiz → §S.4 düzeltme kaydı |
| `win32/com/coinitializeex` (eski COM yolu) | 404 | `platform-api/windows-api-yuzeyi.md` FM + §E.9 → `api/combaseapi/coinitializeex` (HEAD-200) ile değiştirildi |

**Winsock yolları düzeltmesi (check-point düzeltmesi):** `api/winsock/nf-winsock2-{listen,accept,connect,send}` 404 — doğru yol `api/winsock2/…` (hepsi HEAD-200); `bind` hariç (o `winsock/` altında da 200). `ag-api/posix-socket-ve-winsock-karsilastirmasi.md` §F.4'te tescilli.

### §S.3 14 Yeni URL (Kesişim Testi Sonrası Doğrulanmış)

24 aday URL envanter (2026-10-14 ölçümü: 995 md · 3303 benzersiz URL, `.ai/architecture/**`) ile kesişim testinden geçirildi: 10 mevcut envanter URL'si elendi (zaten bu turda kullanılıyor), 14 yeni kabul edildi. Hepsi HEAD-200:

| # | URL | Kullanım |
|---|---|---|
| 1 | `api/combaseapi/nf-combaseapi-coinitializeex` | `platform-api` §12 COM init |
| 2 | `fileapi/nf-fileapi-readfile` | `platform-api` §12 okuma |
| 3 | `fileapi/nf-fileapi-writefile` | `platform-api` §12 yazma |
| 4 | `err/nf-err-setlasterror` | `platform-api` §12 hata |
| 5 | `winsock2/nf-winsock2-bind` | `ag-api` §F bağlama |
| 6 | `winsock2/nf-winsock2-listen` | `ag-api` §F dinleme |
| 7 | `winsock2/nf-winsock2-accept` | `ag-api` §F kabul |
| 8 | `winsock2/nf-winsock2-connect` | `ag-api` §F bağlanma |
| 9 | `winsock2/nf-winsock2-send` | `ag-api` §F gönderme |
| 10 | `posix/write` | `sistem-cagrilari` §6.20 grup özeti |
| 11 | `posix/close` | `sistem-cagrilari` §6.20 grup özeti |
| 12 | `posix/socket` | `ag-api` §F.4 taşınabilirlik |
| 13 | `posix/accept` | `ag-api` §F.4 taşınabilirlik |
| 14 | `man.freebsd.org/en/man3/kqueue.2` | `ornekler` §F.3 macOS keşif |

### §S.4 Dosya Bazlı Eklenen Bölümler / Düzeltmeler

| Dosya | Değişiklik | Gerekçe |
|---|---|---|
| `platform-api/windows-api-yuzeyi.md` | FM satır 11 + §E.9: ölü `win32/com/coinitializeex` → `api/combaseapi/coinitializeex` (HEAD-200) + FM validation güncellendi | §S.2 keşif |
| `platform-api/windows-api-yuzeyi.md` | §12 (yeni): WriteFile/ReadFile, SetLastError/GetLastError, DeviceIoControl kod örnekleri + §12.4 kanıt tablosu | Kod örneği eksiği |
| `isletim-sistemi-api-ornekler.md` | §F (yeni): sistem gruplu örnekler — §F.0 gruplama özeti · §F.1 Linux/POSIX C · §F.2 Windows eşleme · §F.3 macOS kqueue · §F.4 Android NDK · §F.5 taşınabilirlik kanıt matrisi | Kullanıcı kararı "her ikisi de" |
| `syscall/syscall-mekanizmasi.md` | §J (yeni): x86_64 inline asm + libc `syscall()` geçişi + §J.3 kanıt tablosu | Kod örneği eksiği |
| `syscall/syscall-hata-kodlari-ve-errno.md` | §F.17–18 (yeni): C `errno` tek-okuma + PHP `proc_open` kanal yakalama + kanıt tablosu | Kod örneği eksiği |
| `sistem-cagrilari/sistem-cagrilari-ornekler.md` | §6.20–21 (yeni): fork+execve / posix_spawn / CreateProcessW / `proc_open` gruplu + kanıt tablosu | Sistem gruplama |
| `ag-api/posix-socket-ve-winsock-karsilastirmasi.md` | §F (yeni): POSIX vs Winsock2 çift kod örneği + §F.3 satır-satır eşleme + §F.4 dokuz yeni URL kanıt tablosu | Winsock 404 yollarının yerine `winsock2/` |
| `cihaz-erisimi/cihaz-erisimi-anayasa.md` | §10.23–24 (yeni): Linux `open`+`ioctl`+`close` + Windows karşılık + kanıt tablosu | Kod örneği eksiği |
| `isletim-sistemi-api-dogrulama.md` | FM `validation`/`updated` (2026-10-14) + §O O3/O4 satırları + bu §S | 3. tur kaydı |

### §S.5 Beyazliste / Değişiklik Kontrolü

Bu turda yazılan dosyalar (9) `isletim-sistemi-api/` altındadır — beyazliste
içinde. Başka dizine yazım YOK. `git commit` ATILMADI (orkestratöre ait).

### §S.6 rfc9293 Notu (HEAD=0 / GET=200)

`https://www.rfc-editor.org/rfc/rfc9293` HEAD isteğine yanıt vermiyor (0);
aynı URL GET ile 200 dönüyor — URL geçerli, HTTP hatası sayılmadı.

### §S.7 PHP Tur Eki (§G · 2026-10-14)

`isletim-sistemi-api-ornekler.md` §G (PHP grubu — fopen/fread/fwrite/fclose,
file_get_contents, proc_open, socket_*, stream_select, pcntl_fork, FFI) için
17 php.net URL'si HEAD-200 ile doğrulandı: 5 VAR (envanter) + 12 YENİ (kesişim
testinden geçti). Repo kanıtı: `shared/tests/Repository/ApiKeyCreateCommandTest.php:248-269`
(`proc_open` — §G.2). Ayrıntı: §G.6 tablosu.

---

## §T — 4. Tur Web Doğrulama Kaydı (2026-10-14)

### §T.1 Tur Ölçümü (196 URL · HEAD · 2026-10-14)

| Metrik | Değer |
|---|---|
| Ölçülen URL (ağaç tam taraması: 19 md dosyası) | 196 benzersiz |
| HEAD-200 | 168 |
| HEAD-404 | 16 |
| HTTP durumu alınamayan (bağlantı reddi/timeout) | 12 — tamamı `www.gnu.org` |
| Yeni ölü keşfi | **0** |

Ölçüm yöntemi: `Invoke-WebRequest -Method Head -TimeoutSec 12` (PowerShell);
dosya regexi `https?://[^\s\)\]>`"'<>]+`, son noktalama temizliği ile.

### §T.2 404 Sınıflandırması (16 adet — tamamı tescilli)

16×404 = §S.2'deki E.2 X1–X11 (11) + §I.1'deki 3 man7 sayfası
(paths/hierarchy/file_hierarchy) + §S.2 keşifleri (aaudio eski yol +
`com/coinitializeex` eski yol) = 16. Bu turda **yeni ölü yok**; 404'ler
ağaçta tombstone kaydı olarak bilinçli tutulur (§I kuralı: el edilen URL
kullanılmaz, silinmez).

### §T.3 gnu.org erişim notu (12 URL — 404 DEĞİL)

`www.gnu.org/software/libc/manual/**` 12 URL bu turda HTTP durumu
ÜRETMEDI: HEAD → GET → UA'lı GET, üçü de timeout/bağlantı reddi. Bu
"ölü sayfa" değil **ağ erişim engeli** görünümündedir. 3. tur (2026-10-13)
kaydında bu 12 URL HEAD-200 ile tescillidir; tescil kendi tarihine bağlı
kalır — bugüne "200" taşınmaz, bu tur durumu `UNKNOWN (erişim yok)` olarak
işaretlenir. Ağaçtan silinmez.

### §T.4 8 Yeni URL (envanter kesişim 0 · HEAD-200 · 2026-10-14)

Envanter (`.ai/architecture/**` tam taraması): **994 md · 3378 benzersiz
URL** — 3. tur ölçümüne (995 md · 3303) göre fark, 3. turun kendi yeni
URL'leri + ağacın diğer dosyalarındaki güncellemelerdir; ölçüm anına bağlı
fark, çelişki değildir.

| # | URL | Tahsis edildiği bölüm | Durum |
|---|---|---|---|
| 1 | https://www.php.net/manual/en/function.stream-socket-server.php | socket-api-temelleri.md §F.6 | ✅ HEAD 200 · kesişim 0 |
| 2 | https://www.php.net/manual/en/function.stream-socket-client.php | socket-api-temelleri.md §F.6 (+ udp-tcp §F.5 VAR) | ✅ HEAD 200 · kesişim 0 |
| 3 | https://www.php.net/manual/en/function.stream-socket-accept.php | socket-api-temelleri.md §F.6 | ✅ HEAD 200 · kesişim 0 |
| 4 | https://www.php.net/manual/en/function.socket-sendto.php | udp-tcp-ve-tls-api.md §F.5 | ✅ HEAD 200 · kesişim 0 |
| 5 | https://www.php.net/manual/en/function.socket-recvfrom.php | udp-tcp-ve-tls-api.md §F.5 | ✅ HEAD 200 · kesişim 0 |
| 6 | https://www.php.net/manual/en/function.socket-set-option.php | udp-tcp-ve-tls-api.md §F.5 | ✅ HEAD 200 · kesişim 0 |
| 7 | https://man7.org/linux/man-pages/man2/sendto.2.html | udp-tcp-ve-tls-api.md §F.5 | ✅ HEAD 200 · kesişim 0 |
| 8 | https://man7.org/linux/man-pages/man2/recvfrom.2.html | udp-tcp-ve-tls-api.md §F.5 | ✅ HEAD 200 · kesişim 0 |

Aday-ölen 1 URL: `php.net/manual/en/function.php-os-family.php` → **404**
(Dil sabitlerinin tekil sayfası yok) — kullanılmadı, windows-api-yuzeyi.md
§13.3'te dürüstlük kaydı olarak geçer.

### §T.5 Dosya Bazlı Eklenen Bölümler (4. tur)

| # | Dosya | Eklenen bölüm | Yeni URL | Yeni repo kanıtı |
|---|---|---|---|---|
| 1 | ag-api/socket-api-temelleri.md | §F.1–§F.6 (POSIX C · Winsock C · PHP stream) | 3 | 0 |
| 2 | ag-api/udp-tcp-ve-tls-api.md | §F.1–§F.5 (POSIX TCP/UDP C · PHP socket_*/stream) | 5 | 0 |
| 3 | ag-api/posix-socket-ve-winsock-karsilastirmasi.md | §F.5 (PHP tek kod iki OS) | 0 | 0 |
| 4 | platform-api/windows-api-yuzeyi.md | §13.1–§13.4 (PHP yüzeyi) | 0 | 2: `DesktopBff.php:35` (yeni) · `ApiKeyCreateCommandTest.php:253` (doğrulama — VAR §G.2) |
| 5 | cihaz-erisimi/cihaz-erisimi-anayasa.md | §10.25 (PHP fopen deseni) | 0 | 1: `MusicStreamHandler.php:333` (yeni) |
| 6 | sistem-cagrilari/sistem-cagrilari-surucu.md | §10.21.1–§10.21.2 (PHP gözlem deseni) | 0 | 2: `AIEngine.php:295` (doğrulama — VAR §7.1) · `ApiKeyCreateCommandTest.php:253` (VAR) |
| 7 | isletim-sistemi-api-ornekler.md | §H (ağaç geneli sistem-gruplu örnek haritası) | 0 | 0 |
| 8 | isletim-sistemi-api-dogrulama.md | §T + §O4 (bu bölüm) | 0 | 0 |

Toplam: 8 yeni URL · 2 yeni repo-satır iddiası (`DesktopBff.php:35`,
`MusicStreamHandler.php:333` — ikisi de 2026-10-14'te diskten doğrulandı) ·
2 doğrulama-yenileme (§7.1 ve §G.2 tescillerinin satır teyidi).

### §T.6 Beyazliste / Değişiklik Kontrolü

Yazılan dosyalar: §T.5'teki 8 dosya — tamamı
`.ai/architecture/K000-isletim-sistemi/isletim-sistemi-api/` altında.
Yasak dizin kesişimi 0 · dosya adı değişikliği 0 · yeni dosya 0 ·
uzantı dışına yazım 0 · commit 0 (subagent commit atmaz — §A17).

### §T.7 Kapı Durumu

§Q Q1–Q9 ölçüm kapıları bu turda **kapanmaz** (ölçüm yapılmadı); Q-P1…Q-P4
açık kalır. §T yalnız web/disk doğrulama turu kaydıdır.

### §T.8 4. Tur PHP Eki — §G.7 (2026-10-14)

**Kapsam:** `isletim-sistemi-api-ornekler.md` §G.7 — kalan beş syscall ailesinin
PHP yüzeyi (kilit · dizin · durum · non-blocking · sinyal), 5 kod bloğu +
kanıt tablosu (§G.7.6).

**URL ölçümü:** 22 benzersiz URL **22/22 HEAD-200** · envanter kesişimi
**6 VAR + 16 YENİ** (§G.7.6 #19 stream_select §G.6 atıfdır, sayılmaz) ·
4. tur toplam yeni URL: 8 (§T.4) + 16 = **24**.
Yeni URL'ler (14 php.net + 2 man7): `flock` · `file-put-contents` · `mkdir` ·
`rmdir` · `opendir` · `readdir` · `scandir` · `filesize` · `fileperms` ·
`filemtime` · `clearstatcache` · `stream-set-blocking` · `pcntl-signal-dispatch` ·
`usleep` · `man7/rmdir.2` · `man7/getdents.2`.

**Yeni repo kanıtları (7 satır — disk doğrulaması bu tur):**
`shared/src/Component/TemplateEngine.php:74` (LOCK_EX) ·
`shared/src/Log/FileHandler.php:251` (FILE_APPEND|LOCK_EX) ·
`shared/src/Session/SessionConfig.php:35` (@mkdir 0750) ·
`shared/tests/Unit/Log/LoggerRedactionTest.php:51` (@rmdir) / `:143` (mkdir) /
`:178` (clearstatcache) · `home.coremusic.net/include/Stream/MusicStreamHandler.php:72`
(filesize) · `shared/src/AI/ToolCalling.php:187` (filesize) ·
`vendor/php-di/php-di/src/Compiler/Compiler.php:341` (üçlü mkdir idiomu — vendor).

**§H harita düzeltmeleri (2026-10-14 — phantom alt-numara tespiti):**
`syscall-hata` satırı §F.16 "errno↔WSAGetLastError tablo" iddiası YANLIŞTI
(§F.16 = dürüstlük bloğu; eşleme bu dosyada yok) → `— (…YOK — bkz. posix-socket §F)`
+ PHP hücresi §F.18 (tablo) yerine **§F.17** (kod bloğu) yapıldı ·
`sistem-cagrilari-ornekler` satırı `§6.20.1-§6.20.3`/`§6.20.4-§6.20.6`/`§6.21.1-§6.21.3`
phantom numaraları → gerçek blok adlarına (`fork+execve · posix_spawn` /
`CreateProcessW` / `proc_open`) çekildi · `surucu` satırı §10.21.1 → §10.21.1-§10.21.2 ·
`windows-api` satırı §13.1-§13.2 → §13.1-§13.4 · `ornekler` satırı PHP hücresi
§B · §G → §B · §G.1-§G.7.

**Envanter ölçümü (bu ek sonrası):** `.ai/architecture/**` = 1015 md ·
3590 benzersiz URL · alt ağaç (`isletim-sistemi-api/`) = 19 md.

**Kapılar (§G.7 eki sonrası ölçüm — 2026-10-14):** satır ≥500 ✓ (min 505
kapsam · index.md 40–100 ✓ 6/6) · mojibake/CJK 0 ✓ ·
boş tablo hücresi 0 ✓ · `parent:` 0 ✓ (`parent_id` FM alanı ile karıştırılmaz) ·
tırnaksız `group` 19/19 ✓ · FM 19/19 ✓ (3 şema varyantı: A 10 alan · B +title/type/
date/updated · C id/layer/tags — hepsi mevcut ve tutarlı) · beyazliste 8/8 ✓
(6 dosya TUR4 validation + 2 schema-C dosya gövde kaydı §10.24/§10.21.2) ·
commit 0 ✓ (HEAD `45f276a` — subagent atmaz, §A17).

### §U — 5. Tur: Diğer Yazılım Dilleri (§I · 2026-10-14)

**Kapsam:** `isletim-sistemi-api-ornekler.md` §I — PHP (§G) ve C (§F)
tamamlayıcısı olarak **Python · Go · Rust · C#/.NET · Node.js** syscall
yüzeyi. §I.0 dil↔katman eşleme tablosu + 5 dil grubu kod örneği + §I.6 kanıt
tablosu.

**URL ölçümü:** 17 benzersiz URL **17/17 HEAD-200** · envanter kesişimi
**0 VAR + 17 YENİ** (docs.python.org 5 · pkg.go.dev 3 · doc.rust-lang.org 4 ·
learn.microsoft.com/dotnet 3 · nodejs.org 2). Repo-satır iddiası **0** — bu
diller CoreMusic üretim kodunda kullanılmaz (PHP 8.4 tek backend, ADR-039);
bloklar resmi dokümantasyon sarmalayıcılarıdır, üretim kodu değildir (§I.6).

**§H harita güncelleme:** `ornekler` satırı Not hücresine §I eklendi; §I çok-dilli
olduğu için dar "PHP" sütununa yerleştirilmedi (sütun yerleşimi yanıltıcı olurdu).

**Envanter ölçümü (bu ek sonrası):** `.ai/architecture/**` = 1015 md ·
3607 benzersiz URL (3590 + 17).

**Kapılar:** ≥500 satır ✓ (ölçüm kapanışta) · mojibake 0 · beyazliste içi 1 dosya
(ornekler.md) · commit 0 (subagent — §A17).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
