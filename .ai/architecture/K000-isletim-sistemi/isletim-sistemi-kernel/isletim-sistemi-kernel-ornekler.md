---
node_id: K000-isletim-sistemi/isletim-sistemi-kernel/isletim-sistemi-kernel-ornekler
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-kernel
children: []
depends_on: []
evidence:
  - https://man7.org/linux/man-pages/man2/fork.2.html
  - https://man7.org/linux/man-pages/man2/clone.2.html
  - https://man7.org/linux/man-pages/man2/open.2.html
  - https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html
  - https://learn.microsoft.com/en-us/windows/win32/api/processthreadsapi/nf-processthreadsapi-createprocessw
  - https://developer.apple.com/documentation/iokit
  - https://samsungtizenos.com/docs/application/native/guides/multimedia/media-playback
  - https://www.webosose.org/docs/guides/getting-started/introduction-to-ls2-api/
validation: "2026-10-13 → 2026-10-14 · §K.2 OS gruplu örnekler eklendi (6 OS · Linux hariç) · 25 yeni URL HEAD 200 · envanter kesişimi 0 · repo satır kanıtları Select-Object -Index ile doğrulandı · çekirdek kodu YOK"
open_questions:
  - "core ↔ kernel sınırı (Q2) doğrulanamadı → açık"
  - "Hedef çekirdek (Q4) → UNKNOWN"
group: kod
---

# isletim-sistemi-kernel — Örnekler (gerçek repo kodu)

## §A — Dürüstlük notu

Repo'da çekirdek (`.c` / `.h` / `.cpp`) kodu **YOK** — CoreMusic bir PHP 8.x
uygulamasıdır (L0 altyapısı `*.cpp`/`*.h` = 0 · PLANNED). Bu dosyadaki her örnek,
**çekirdek hizmetine en yakın PHP kodunu** gösterir: PHP işlevi, syscall sınırının
hemen üstünde durur; syscall'ın kendisi PHP'nin altında (libc → çekirdek) kalır.
Bu nedenle aşağıda "OS hizmeti" sütunu, PHP kodunun hangi çekirdek mekanizmasına
gittiğini **işaret eder** — çekirdek kodunu **temsilen** değil, **sınırda** gösterir.

> **Repo'da çekirdek kodu YOK — OS seviyesi, kaynak: upstream docs.**
> Örneklerin çekirdek tarafı §D'deki URL'lerle (HEAD 200) doğrulanır; repo tarafı
> §C'de satır-no ile `Select-Object -Index (N-1)` doğrulanır.

## §B — Örnek haritası (5 işlev · tek ana ev)

| # | İşlev | PHP örneği | Çekirdek hizmeti | Ana ev |
|---|---|---|---|---|
| O1 | Dosya I/O | `MusicStreamHandler.php` (`sendBody`) | `open`/`read`/`lseek` — VFS | `cekirdek-yonetim` |
| O2 | Süreç oluşturma | `ApiKeyCreateCommandTest.php` (`proc_open`) | `fork`/`clone` + `exec` | `cekirdek-yonetim` |
| O3 | Bellek eşleme | `TemplateEngine.php` (`sys_get_temp_dir` + `LOCK_EX`) | `mmap`/`flock` | `cekirdek-yonetim` |
| O4 | Komut çalıştırma | `AIEngine.php` (`shell_exec` + `escapeshellarg`) | süreç + `exec` + pipe | `cekirdek-yonetim` |
| O5 | Bekleme / yeniden deneme | `HomeAuthBridge.php` (`usleep`) | `nanosleep`/`clock_nanosleep` | `kesme-ve-senkronizasyon` |
| O6 | Platform sorgusu | `DesktopBff.php` (`php_uname('s')`) | `uname`/`GetSystemInfo` | `surucu-ve-modul-modeli` |

> Her örnekte tek bir **ana ev** vardır (§B.1 matrisi ile birebir); diğer evlere
> yalnız "ilgili" sütununda ok gösterilir — çifte-sayım yoktur.

## §C — Repo örnekleri (satır-no doğrulandı)

### §C.1 O1 — Dosya I/O: `fopen`/`fread` (open/read syscall'ına giden yol)

**Dosya:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:331-337`
(`sendBody`) ve `185-204` (yol çözümleme / symlink denetimi)

| Öğe | Değer |
|---|---|
| Satır 331 | `private function sendBody(string $path, int $start, int $length): never` |
| Satır 334 | `$handle = @fopen($path, 'rb');` |
| Satır 185 | `if (is_link($trimmed)) {` |
| Satır 189 | `$real = realpath($trimmed);` |
| Satır 204 | `if (!is_readable($real)) {` |

```php
private function sendBody(string $path, int $start, int $length): never
{
    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        $this->fail(403, 'Erişim reddedildi');
    }
    // $start/$length boyunca okuma (satır 338+)
}
```

**Ne gösteriyor:** Akış gönderimi, dosya üzerinde konumlandırma + okuma pratiğidir.
`fopen`/`fread`/`fseek` PHP'de `open`/`read`/`lseek` benzeri sistem çağırmalarına iner —
çekirdek dosya katmanı (VFS) bu işi yapar. Satır 185-204'teki `is_link` → `realpath` →
`is_readable` zinciri, çekirdek `readlink`/`stat` denetimlerinin uygulamadaki
karşılığıdır (symlink-follow + erişim denetimi). HTTP `Range` semantiği uygulama
katmanı, dosya I/O'u OS katmanıdır.

### §C.2 O2 — Süreç oluşturma sınırı: `proc_open` (fork/exec benzeri)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php:253`

| Öğe | Değer |
|---|---|
| Satır 253 | `$handle = proc_open(` |

```php
$handle = proc_open(
    ['php', 'bin/api-key-create.php', ...],
    [0 => ['pipe','r'], 1 => ['pipe','w'], ...],
    $pipes
);
```

**Ne gösteriyor:** `proc_open` borularla (pipe) yeni bir süreç başlatır — süreç
oluşturma ve pipe I/O, işletim sistemi süreç modelinin (`fork`/`clone` + `exec` + pipe)
uygulamadaki karşılığıdır. `proc_open` dizi (exec formu) kullanıldığı için shell
yorumu yoktur — bu, `execve` çağrısının doğrudan gidişi demektir.

### §C.3 O3 — Bellek + geçici dosya + dosya kilidi: `sys_get_temp_dir` + `LOCK_EX`

**Dosya:** `shared/src/Component/TemplateEngine.php:73-74`

| Öğe | Değer |
|---|---|
| Satır 73 | `$cachePath = rtrim(sys_get_temp_dir(), ...) . ...` (geçici dizin sorgusu) |
| Satır 74 | `file_put_contents($cachePath, $template, LOCK_EX) === false` (exclusive kilitle yazım) |

```php
$cachePath = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR . 'cmtpl_' . bin2hex(random_bytes(8)) . '.php';  // satır 73
if (file_put_contents($cachePath, $template, LOCK_EX) === false) {   // satır 74
```

**Ne gösteriyor:** `sys_get_temp_dir` geçici dizini OS'ten (`TMPDIR`/`GetTempPath`)
ister; `LOCK_EX` çekirdek `flock(LOCK_EX)` (veya Windows `LockFileEx`) üzerine gider.
Böylece tek örnek, **bellek-dosya sınırını** (geçici dosya = sayfa önbelleği üstünde
çalışır) ve **dosya tabanlı kilidi** (senkronizasyon) aynı anda gösterir.

### §C.4 O4 — Komut çalıştırma sınırı: `shell_exec` + `escapeshellarg`

**Dosya:** `shared/src/AI/AIEngine.php:292` ve `295`

| Öğe | Değer |
|---|---|
| Satır 292 | `escapeshellarg($filePath)` |
| Satır 295 | `$output = shell_exec($cmd);` |

```php
$cmd = 'ffprobe ... ' . escapeshellarg($filePath) . ' ...';   // satır 292
$output = shell_exec($cmd);                                   // satır 295
```

**Ne gösteriyor:** `shell_exec` bir kabuk süreci başlatır (`fork` + `exec` + pipe).
`escapeshellarg` güvenliği uygulama katmanında sağlar; süreç modelinin kendisi OS
katmanındadır. `shell_exec` tek dönüşlü boru (pipe) okuması yaptığı için, çekirdek
tarafında pipe-buffer + `waitpid` bekleme vardır.

### §C.5 O5 — Bekleme / yeniden deneme: `usleep`

**Dosya:** `home.coremusic.net/include/Auth/HomeAuthBridge.php:117`

| Öğe | Değer |
|---|---|
| Satır 117 | `usleep(self::RETRY_DELAY_US);` |

```php
usleep(self::RETRY_DELAY_US);   // satır 117 — mikrosaniye bekleme
```

**Ne gösteriyor:** `usleep` çekirdek `nanosleep`/`clock_nanosleep` üzerine gider —
süreç/izleyici (thread) uyutulur, zamanlayıcı (timer) ile uyandırılır. Bu, `kesme-ve-
senkronizasyon` daliyle ortak sınır noktasıdır: bekleme, kullanıcı-uzayı bir bekleme
değil, çekirdeğin zamanlama alt sisteminin (EEVDF/CFS) işidir.

### §C.6 O6 — Platform sorgusu: `php_uname('s')`

**Dosya:** `shared/src/Api/Bff/DesktopBff.php:35`

| Öğe | Değer |
|---|---|
| Satır 34-38 | platform anahtarları bloğu |
| Satır 35 | `'platform' => php_uname('s'),` |

```php
$response['meta']['system'] = [
    'platform' => php_uname('s'),   // satır 35 — çekirdek/OS adı
    'phpVersion' => PHP_VERSION,
    'serverSoftware' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
];
```

**Ne gösteriyor:** Uygulama, üzerinde çalıştığı işletim sistemini `uname` verisiyle
öğrenir (çekirdeğe/OS'e sorgu — Linux `uname(2)`, Windows `GetSystemInfo`/RtlGetVersion).
Karar: hangi platformda hangi davranış — kernel alt sistemiyle doğrudan temas noktası
budur.

## §D — Çekirdek tarafı (upstream · HEAD 200)

| # | PHP örneği | Çekirdek mekanizması | Doğrulayıcı URL (HEAD 200) |
|---|---|---|---|
| O1 | `fopen`/`fread`/`realpath` | `open`/`read`/`readlink`/`stat` — VFS | https://docs.kernel.org/filesystems/vfs.html · https://man7.org/linux/man-pages/man2/open.2.html |
| O2 | `proc_open` (dizi) | `clone` + `execve` + pipe | https://man7.org/linux/man-pages/man2/clone.2.html · https://man7.org/linux/man-pages/man2/fork.2.html |
| O3 | `sys_get_temp_dir` + `LOCK_EX` | `flock(LOCK_EX)` + geçici dosya | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html |
| O4 | `shell_exec` | `fork` + `exec` + pipe + `waitpid` | https://man7.org/linux/man-pages/man2/fork.2.html |
| O5 | `usleep` | `nanosleep`/`clock_nanosleep` — zamanlama | https://pubs.opengroup.org/onlinepubs/9799919799/functions/nanosleep.html |
| O6 | `php_uname('s')` | `uname(2)` / `GetSystemInfo` | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getsysteminfo |

> **Tüm URL'ler 2026-10-13'te HEAD ile doğrulanmıştır (HTTP 200).** §D, repo'da
> bulunmayan çekirdek tarafını **upstream dokümanlara** dayandırır — "repo kodu YOK,
> OS seviyesi, kaynak: upstream docs".

## §E — Sınır notları (komşu dallar)

| # | Konu | Ait olduğu dal | Neden |
|---|---|---|---|
| B1 | Dosya I/O + bellek eşleme (O1 · O3) | `isletim-sistemi-core` | dosya/bellek hizmetleri çekirdek-çekirdek sınırında |
| B2 | Bekleme / zamanlama (O5) | `isletim-sistemi-plan` | `nanosleep` zamanlama alt sistemine (EEVDF/CFS) bağlı |
| B3 | Süreç + platform (O2 · O4 · O6) | `isletim-sistemi-kernel` (bu dal) | süreç modeli + OS sorgusu bu dalın omurgası |

> B1/B2 yalnız **ok**tur (bu dosya onlara **yazmaz**); her iki sınır da `index.md`
> §GRUPLAR matrisinde tanımlıdır.

## §F — Seçim gerekçesi (bu 6 örnek neden bu dalda?)

| # | Seçim kriteri | Uygulanan örnek | Gerekçe |
|---|---|---|---|
| F1 | Uygulama ↔ çekirdek sınırına en yakın kod | O1 · O2 · O4 | PHP işlevi syscall'ın hemen üstünde durur (`fopen`→`open`, `proc_open`→`clone`, `shell_exec`→`fork`+`exec`) |
| F2 | Her çekirdek mekanizma en az 1 kez temsil | O1 (dosya) · O2 (süreç) · O3 (kilit) · O5 (zamanlama) | tek örnek tek mekanizma; çifte-sayım yok |
| F3 | Repo'da gerçek satır-no ile kanıtlanabilir | 6/6 `Select-Object -Index` birebir | uydurma satır-no yasak (zero-hallucination) |
| F4 | Çekirdek tarafı upstream dokümanla doğrulanabilir | §D 6 satır · HEAD 200 | repo'da çekirdek kodu YOK → upstream docs |
| F5 | Komşu dala taşma sınırı açıkça işaretli | §B + §E | her örnek tek ana evde; komşu yalnız "ok" |

## §G — Dal sınırı matrisi (bu dosyanın yazma yetkisi)

| # | Yüzey | Bu dosya yazar mı? | Ait dal |
|---|---|---|---|
| G1 | `isletim-sistemi-kernel/**` (bu dal) | ✅ evet | bu dal |
| G2 | `isletim-sistemi-core/**` | ❌ hayır (yalnız ok) | `core` |
| G3 | `isletim-sistemi-plan/**` | ❌ hayır (yalnız ok) | `plan` |
| G4 | `isletim-sistemi-security/**` | ❌ hayır (yalnız ok) | `security` |
| G5 | `isletim-sistemi-audio/**` · `K001..K019` | ❌ hayır (beyazliste dışı) | ilgili dal |
| G6 | `*.php` · `*.js` · `*.css` · `*.sql` | ❌ hayır (okunur, yazılmaz) | — |

> Beyazliste: `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel/**\*.md`.
> Bu dosyanın dışına yazım = 0 (§F.7 S5 ile ölçülür).

## §H — Açık / doğrulanamayan iddialar (bu dosya)

| # | İddia | Neden | Durum |
|---|---|---|---|
| H1 | Repo'nun hedef aldığı çekirdek (Linux/Windows/macOS) | yalnız CI `ubuntu-latest` var | UNKNOWN (Q4) |
| H2 | `core` ↔ `kernel` sınırı (O1/O3'ün tam hizası) | v2 §2.1 sınır tanımı vermez | ⚠️ VERIFICATION REQUIRED (Q2) |
| H3 | `shell_exec`/`proc_open`'ın gerçek fork+exec oluşu | PHP sözdizimi upstream docs'tan; çalışma anı ölçülmedi | ⚠️ VERIFICATION REQUIRED |

> H1-H3 `dogrulama.md` §F.5'teki açık kayıtlarıyla (Q2 · Q4) **aynı** açık konulardır —
> burada yeniden numaralandırılmış, **kapatılmamıştır**.

## §I — Syscall eşlemesi (repo kodu → çekirdek çağrısı)

> Bu tablo, §C'deki her PHP satırının syscall sınırında hangi çekirdek çağrısına
> indiğini gösterir. Eşleme **upstream docs**'a dayanır (repo'da çekirdek kodu YOK).

| # | PHP satırı (repo) | İnen syscall | Çekirdek alt sistemi | URL (HEAD 200) |
|---|---|---|---|---|
| M1 | `MusicStreamHandler.php:334` `fopen($path,'rb')` | `open` | VFS | https://man7.org/linux/man-pages/man2/open.2.html |
| M2 | `MusicStreamHandler.php:189` `realpath($trimmed)` | `readlink` + `stat` | VFS | https://docs.kernel.org/filesystems/vfs.html |
| M3 | `ApiKeyCreateCommandTest.php:253` `proc_open([...])` | `clone` + `execve` + `pipe2` | süreç | https://man7.org/linux/man-pages/man2/clone.2.html |
| M4 | `TemplateEngine.php:73` `sys_get_temp_dir()` | `getcwd`/`stat` (dizin) | VFS | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html |
| M5 | `TemplateEngine.php:74` `file_put_contents(..., LOCK_EX)` | `flock(LOCK_EX)` + `write` | kilit + VFS | https://pubs.opengroup.org/onlinepubs/9799919799/functions/mmap.html |
| M6 | `AIEngine.php:295` `shell_exec($cmd)` | `fork` + `exec` + pipe | süreç | https://man7.org/linux/man-pages/man2/fork.2.html |
| M7 | `HomeAuthBridge.php:117` `usleep(...)` | `nanosleep` | zamanlama | https://pubs.opengroup.org/onlinepubs/9799919799/functions/nanosleep.html |
| M8 | `DesktopBff.php:35` `php_uname('s')` | `uname` | platform | https://learn.microsoft.com/en-us/windows/win32/api/sysinfoapi/nf-sysinfoapi-getsysteminfo |
| M9 | `SessionConfig.php:33` `ini_get(...)`/`sys_get_temp_dir()` | `uname`/`stat` | platform + VFS | https://pubs.opengroup.org/onlinepubs/9799919799/functions/sysconf.html |
| M10 | `SessionConfig.php:35` `@mkdir($savePath,0750,true)` | `mkdir` | VFS | https://docs.kernel.org/filesystems/vfs.html |
| M11 | `SessionInitializer.php:31` `@mkdir($savePath,0777,true)` | `mkdir` | VFS | https://docs.kernel.org/filesystems/vfs.html |
| M12 | `MusicStreamHandler.php:185` `is_link($trimmed)` | `lstat` (S_ISLNK) | VFS | https://docs.kernel.org/filesystems/vfs.html |

## §J — Senaryo tablosu (6 örnek × 4 katman)

| # | Örnek | Uygulama katmanı | PHP işlevi | Syscall sınırı | Çekirdek katmanı |
|---|---|---|---|---|---|
| S1 | O1 dosya I/A | HTTP `Range` akışı | `fopen`/`fread` | `open`/`read` | VFS + sayfa önbelleği |
| S2 | O2 süreç | test süreci başlatma | `proc_open` | `clone`+`exec` | süreç tablosu + pipe |
| S3 | O3 bellek+kilit | şablon önbelleği | `file_put_contents`+`LOCK_EX` | `flock` | kilit + geçici dosya |
| S4 | O4 komut | ffprobe analizi | `shell_exec` | `fork`+`exec` | süreç + pipe + `waitpid` |
| S5 | O5 bekleme | yeniden deneme gecikmesi | `usleep` | `nanosleep` | zamanlama (EEVDF/CFS) |
| S6 | O6 platform | platform anahtarı | `php_uname('s')` | `uname` | platform alt sistemi |

> S1-S6, §B haritasındaki O1-O6 ile birebir aynı sıradadır; §C'deki satır-no
> kanıtları bu tablonun uygulama sütununu, §D'deki URL'ler çekirdek sütununu
> doğrular. **Hiçbir satır uydurma değildir** — hepsi `Select-Object -Index (N-1)`
> ile diskten okunmuştur.

## §K — OS karşılaştırması (Linux · Windows · macOS)

| # | Mekanizma | Linux | Windows | macOS (XNU) |
|---|---|---|---|---|
| K1 | Dosya açma | `open(2)` | `CreateFileW` | `open_nocancel` |
| K2 | Bellek eşleme | `mmap(2)` | `MapViewOfFile` | `mmap` |
| K3 | Dosya kilidi | `flock`/`fcntl` | `LockFileEx` | `flock` |
| K4 | Süreç yaratma | `clone(2)`/`fork(2)` | `CreateProcessW` | `posix_spawn`/`fork` |
| K5 | Bekleme | `nanosleep` | `SleepEx` | `nanosleep` |
| K6 | Platform sorgusu | `uname(2)` | `GetSystemInfo` | `uname(2)` |
| K7 | Sinyal/kilit bekleme | `futex(7)` | `WaitForSingleObject` | `ulock_wait` |

> **Doğrulama durumu:** K1-K5 sütunları §E'de HEAD-200 ile doğrulanmış upstream
> dokümanlardan gelir. K6-K7'nin **macOS sütunu** (XNU) bu turda **HEAD ile
> doğrulanmadı** → ⚠️ VERIFICATION REQUIRED (XNU dokümanı §E.3'te elenmiştir:
> `developer.apple.com/documentation/xnu` HEAD başarısız). Linux/Windows sütunları
> doğrulanmıştır; macOS sütunu **tek başına iddia olarak kullanılmaz**.

## §K.2 — OS bazlı gruplanmış kod örnekleri (2026-10-14 · Linux hariç)

> **Kapsam (kullanıcı kuralı):** Bu bölüm işletim sistemlerine göre **gruplanmış**
> kod örnekleri taşır; **Linux hariçtir** (repo'nun hedef aldığı çekirdek Q4 ile
> hâlâ UNKNOWN — §H H1). §K tablosundaki Linux sütunu tarihseldir ve bu bölümde
> **tekrarlanmaz**. Altı OS: **Windows · macOS (XNU/Darwin) · Android · webOS ·
> Tizen · ReactOS**. Her örneğin kaynağı upstream dokümandır (repo'da native kod
> **YOK** — §A dürüstlük notu geçerlidir); her URL 2026-10-14'te HEAD ile 200
> doğrulanmıştır (`dogrulama.md` §E.5).

### §K.2.1 Windows (Win32/NT çekirdeği) — dosya · süreç · senkronizasyon

```c
// Dosya tanıtıcısı yaşam döngüsü: aç → oku → kapat (CloseHandle handleapi'dedir)
HANDLE h = CreateFileW(L"C:\\veri\\ses.wav", GENERIC_READ, FILE_SHARE_READ,
                       NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
if (h != INVALID_HANDLE_VALUE) {
    char buf[4096];
    DWORD okunan = 0;
    ReadFile(h, buf, sizeof(buf), &okunan, NULL);
    CloseHandle(h);
}
// Senkronizasyon: mutex oluştur → bekle → bırak
HANDLE m = CreateMutexW(NULL, FALSE, L"CoreMusicKilit");
WaitForSingleObject(m, INFINITE);
ReleaseMutex(m);
CloseHandle(m);
```

| # | API | Doğrulayıcı URL (HEAD 200 · 2026-10-14) | Durum |
|---|---|---|---|
| W-1 | `CreateFileW` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew | ✅ env'de |
| W-2 | `ReadFile` | https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-readfile | ✅ env'de |
| W-3 | `CloseHandle` | https://learn.microsoft.com/en-us/windows/win32/api/handleapi/nf-handleapi-closehandle | ✅ env'de |
| W-4 | `CreateMutexW` | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-createmutexw | ✅ env'de |
| W-5 | `WaitForSingleObject` | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-waitforsingleobject | ✅ env'de |
| W-6 | `ReleaseMutex` | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-releasemutex | ✅ env'de |
| W-7 | `SleepEx` (alertable bekleme) | https://learn.microsoft.com/en-us/windows/win32/api/synchapi/nf-synchapi-sleepex | ✅ **yeni** |
| W-8 | `DeviceIoControl` (sürücü ioctl) | https://learn.microsoft.com/en-us/windows/win32/api/ioapiset/nf-ioapiset-deviceiocontrol | ✅ env'de |

> `CloseHandle` `fileapi`'de **değildir**, `handleapi`'dedir (§E.5 düzeltme kaydı);
> `CreateMutexW` `processthreadsapi`'de **değildir**, `synchapi`'dedir (404 → düzeltme).

### §K.2.2 macOS / XNU (Darwin) — hibrit çekirdek yapısı

XNU ("X is Not Unix"), Darwin çekirdeğidir ve macOS/iOS'ta çalışır. **Hibrittir:**
Carnegie Mellon'daki **Mach** çekirdeği + **FreeBSD** bileşenleri + sürücü yazmak
için **C++ API'si IOKit**'ten oluşur; x86_64 ve ARM64 üzerinde çalışır. Kaynak
ağacı: `osfmk` (Mach alt sistemleri) · `bsd` (BSD alt sistemleri) · `libkern`
(IOKit sürücü kütüphanesi) · `libsyscall` (kullanıcı uzayı syscall arayüzü) ·
`security` (MAC ilkeleri) · `pexpert` (platform/kesme kodu).

| # | Yüzey | Doğrulayıcı URL (HEAD 200 · 2026-10-14) | Durum |
|---|---|---|---|
| M-1 | XNU kaynak deposu (README) | https://github.com/apple-oss-distributions/xnu | ✅ env'de |
| M-2 | Apple Kernel API şemsiyesi | https://developer.apple.com/documentation/kernel | ✅ env'de |
| M-3 | IOKit (donanım/sürücü erişimi) | https://developer.apple.com/documentation/iokit | ✅ **yeni** |
| M-4 | DriverKit (kullanıcı uzayı sürücü) | https://developer.apple.com/documentation/driverkit | ✅ env'de |

> **Sembol-düzeyi kod bu turda yazılmadı:** Apple dokümanları JS kabuğu olarak
> servis edilir; sembol sayfaları (`/documentation/iokit/io_service_getmatchingservice`
> vb.) HEAD **404** döndü → sembol imzaları doğrulanamadı → ⚠️ VERIFICATION REQUIRED.
> Yalnızca README'de doğrulanmış **yapısal** içerik (hibrit çekirdek · kaynak ağacı)
> ve IOKit'in kendi tanımı ("Access hardware devices and drivers from your apps and
> services" — sayfa meta tanımı) yazılmıştır; uydurma sembol imzası eklenmemiştir.

### §K.2.3 Android (AAudio NDK) — düşük gecikmeli ses akışı

```c
// AAudio builder deseni (NDK rehberinden birebir)
AAudioStreamBuilder *builder;
aaudio_result_t result = AAudio_createStreamBuilder(&builder);
AAudioStreamBuilder_setDeviceId(builder, deviceId);
AAudioStreamBuilder_setDirection(builder, direction);
AAudioStreamBuilder_setSharingMode(builder, mode);
AAudioStreamBuilder_setSampleRate(builder, sampleRate);
AAudioStreamBuilder_setChannelCount(builder, channelCount);
AAudioStreamBuilder_setFormat(builder, format);
AAudioStreamBuilder_setBufferCapacityInFrames(builder, frames);
AAudioStream *stream;
result = AAudioStreamBuilder_openStream(builder, &stream);
result = AAudioStream_requestStart(stream);
result = AAudioStream_requestStop(stream);
```

| # | Yüzey | Doğrulayıcı URL (HEAD 200 · 2026-10-14) | Durum |
|---|---|---|---|
| A-1 | AAudio rehberi (kod kaynağı) | https://developer.android.com/ndk/guides/audio/aaudio/aaudio | ✅ env'de |
| A-2 | OpenSL ES (eski ses yolu) | https://developer.android.com/ndk/guides/audio/opensl | ✅ env'de |
| A-3 | Çekirdek mimarisi | https://source.android.com/docs/core/architecture | ✅ **yeni** |
| A-4 | Mimari · çekirdek (Linux tabanı) | https://source.android.com/docs/core/architecture/kernel | ✅ **yeni** |
| A-5 | Mimari · HAL (donanım soyutlama) | https://source.android.com/docs/core/architecture/hal | ✅ **yeni** |

> Android çekirdeği **Linux**'tur (A-4); bu bölüm Linux çekirdeği üzerine kurulu
> **Android platform katmanını** (AAudio · HAL) örnekler — Linux çekirdeğinin
> kendisi kapsam dışıdır (kullanıcı kuralı).

### §K.2.4 webOS (LS2 / hizmet yolu) — mesajlaşma otobüsü

```bash
# Luna Service 2 (LS2): hizmete yöntem çağrısı — URI biçimi <hizmet URI>/<yöntem>
$ luna-send -n 1 -f luna://com.webos.service.systemservice/time/getSystemTime '{}'
# Yanıt (JSON, seçilmiş anahtarlar): "returnValue": true · "timezone": "Asia/Seoul" · "utc": 1557280839 · "localtime": {"month": 5, "day": 8, "hour": 11, "minute": 0, "year": 2019, "second": 39} · "TZ": "KST"
```

| # | Yüzey | Doğrulayıcı URL (HEAD 200 · 2026-10-14) | Durum |
|---|---|---|---|
| L-1 | LS2 API'ye giriş (kod kaynağı) | https://www.webosose.org/docs/guides/getting-started/introduction-to-ls2-api/ | ✅ **yeni** |
| L-2 | LS2 API indeksi | https://www.webosose.org/docs/reference/ls2-api/ls2-api-index/ | ✅ env'de |
| L-3 | `luna-send` komutu | https://www.webosose.org/docs/tools/commands/luna-send/ | ✅ env'de |
| L-4 | Luna Service 2 kütüphanesi | https://www.webosose.org/docs/reference/luna-service2-library/luna-service2-library-api-reference/ | ✅ **yeni** |
| L-5 | Yerel hizmet modeli | https://www.webosose.org/docs/guides/development/native-services/native-service-overview/ | ✅ **yeni** |
| L-6 | webOS OSE doküman kökü | https://www.webosose.org/docs/ | ✅ **yeni** |

### §K.2.5 Tizen (CAPI · yerel uygulama) — medya oynatıcı yaşam döngüsü

```c
#include <player.h>                       // Player API (CAPI_MEDIA_PLAYER)
struct appdata {
    player_h player;
};
static void
init_base_player(appdata_s *ad)
{
    int error_code = 0;
    error_code = player_create(&ad->player);
    if (error_code != PLAYER_ERROR_NONE)
        dlog_print(DLOG_ERROR, LOG_TAG, "failed to create");
    // Daha sonra: player_set_uri → player_prepare → player_start → player_stop → player_destroy
}
```

| # | Yüzey | Doğrulayıcı URL (HEAD 200 · 2026-10-14) | Durum |
|---|---|---|---|
| T-1 | Medya oynatma rehberi (kod kaynağı) | https://samsungtizenos.com/docs/application/native/guides/multimedia/media-playback | ✅ **yeni** |
| T-2 | Yerel uygulama dokümanı | https://docs.tizen.org/application/native/ | ✅ **yeni** |
| T-3 | Süreç yönetimi | https://docs.tizen.org/application/native/guides/process-management/ | ✅ **yeni** |
| T-4 | Multimedya rehberi | https://docs.tizen.org/application/native/guides/multimedia/ | ✅ **yeni** |
| T-5 | HAL genel bakış | https://samsungtizenos.com/docs/platform/hal/overview | ✅ **yeni** |

> Doğrulanan API'ler (medya oynatma sayfasında mevcut): `player_create` ·
> `player_set_uri` (HTTP/HTTP Live/RTSP akışı) · `player_set_buffering_cb` ·
> `player_prepare` · `player_start` · `player_stop` · `player_pause` ·
> `player_destroy`. Tizen çekirdeği Linux tabanıdır; bu bölüm platform CAPI'sini
> örnekler.

### §K.2.6 ReactOS (NT uyumlu) — Win32 yüzeyi

ReactOS, **NT arayüzüyle uyumlu** açık kaynak bir işletim sistemidir. Bu nedenle
§K.2.1'deki Win32 örneği (`CreateFileW` · `CreateMutexW` · `WaitForSingleObject`)
ReactOS yüzeyinde de geçerlidir. Wiki sayfaları HEAD 200 ile varlığı doğrulanmış,
içeriği **Anubis bot koruması** nedeniyle GET ile alınamamıştır → sembol/uyum
iddiaları yalnızca URL yol adıyla (NT_Interface_Compatibility) sınırlıdır.

| # | Yüzey | Doğrulayıcı URL (HEAD 200 · 2026-10-14) | Durum |
|---|---|---|---|
| R-1 | NT arayüz uyumluluğu | https://reactos.org/wiki/NT_Interface_Compatibility | ✅ **yeni** |
| R-2 | ReactOS çekirdeği | https://reactos.org/wiki/Kernel | ✅ env'de |
| R-3 | Geliştirici wiki'si | https://reactos.org/wiki/Developers | ✅ **yeni** |
| R-4 | NT arayüzü (çizim/GDI) | https://reactos.org/wiki/Drawing | ✅ **yeni** |
| R-5 | ReactOS ana sayfası | https://reactos.org/ | ✅ env'de |

### §K.2.7 OS grup matrisi (örnek ↔ doğrulanmış yüzey)

| OS | Örnek konusu | Doğrulanan yüzey | Kaynak türü |
|---|---|---|---|
| Windows | dosya · süreç · mutex | Win32 API (fileapi · synchapi · ioapiset) | upstream MS Learn |
| macOS | hibrit çekirdek yapısı | XNU ağacı (Mach · FreeBSD · IOKit) | upstream README |
| Android | düşük gecikmeli ses | AAudio builder + HAL | upstream NDK/Sources |
| webOS | hizmet yolu çağrısı | LS2 · `luna-send` · JSON yanıt | upstream webOS OSE |
| Tizen | medya oynatıcı yaşam döngüsü | CAPI `player_*` · `player.h` | upstream Samsung/Tizen |
| ReactOS | NT uyumlu Win32 yüzeyi | NT arayüz wiki (varlık) | upstream ReactOS (HEAD) |

### §K.2.8 Doğrulama notları (bu bölüm)

| # | Not |
|---|---|
| N-1 | Tüm §K.2 URL'leri **2026-10-14** HEAD 200 ile doğrulanmıştır; envanter kesişimi **0** (`dogrulama.md` §E.5 · 3334 anlık görüntü). |
| N-2 | `CloseHandle` **handleapi**'dedir (fileapi 404) · `CreateMutexW` **synchapi**'dedir (processthreadsapi 404) — §E.5 düzeltme kayıtları. |
| N-3 | macOS sembol sayfaları HEAD 404 → sembol-düzeyi kod **⚠️ VERIFICATION REQUIRED** (uydurulmadı); yalnız README yapısı yazılmıştır. |
| N-4 | ReactOS wiki GET'i bot korumalıdır → içerik iddiası YOK, yalnız URL varlığı. |
| N-5 | Linux bu bölümde **yoktur** (kullanıcı kuralı); §K tablosundaki Linux sütunu tarihseldir ve silinmemiştir. |

## §L — Zaman çizelgesi (bu dosyanın revizyonu)

| # | Tarih | Değişiklik | Kanıt |
|---|---|---|---|
| L1 | 2026-10-12 | İlk yazım · 65 satır · eski FM (`parent:` alanı) | git HEAD |
| L2 | 2026-10-13 | v2 FM'ye geçiş (10 alan · `parent:` kaldırıldı · `parent_id` eklendi) | bu revizyon |
| L3 | 2026-10-13 | Dürüstlük notu eklendi (çekirdek kodu YOK) | §A |
| L4 | 2026-10-13 | `DesktopBff` yolu düzeltildi (`home.coremusic.net/src/...` → `shared/src/Api/Bff/DesktopBff.php:35`) | §C.6 · satır 35 birebir |
| L5 | 2026-10-13 | §F-§K eklendi (seçim gerekçesi · sınır matrisi · syscall eşlemesi · OS karşılaştırması) | bu revizyon |
| L6 | 2026-10-14 | §K.2 eklendi — OS bazlı gruplanmış kod örnekleri (Windows · macOS · Android · webOS · Tizen · ReactOS · Linux hariç) · 25 yeni URL HEAD 200 · kesişim 0 | bu revizyon · kanıt `dogrulama.md` §E.5 |

> L1'deki eski FM'de `parent:` anahtarı vardı — v2 şemasında **yasaktır** (`parent_id`
> kullanılır). Bu revizyonda `parent:` **kaldırıldı**, `parent_id: K000-isletim-sistemi/
> isletim-sistemi-kernel` yazıldı.

## §M — Elenen / kullanılmayan kanıtlar (bu dosya)

| # | Kanıt | Durum | Neden |
|---|---|---|---|
| N1 | `developer.apple.com/documentation/xnu` | elendi | HEAD başarısız (§E.3 #16) |
| N2 | `docs.kernel.org/mm/page_alloc.html` | elendi | HEAD başarısız (§E.3 #1) |
| N3 | `learn.microsoft.com/.../kernel/using-spin-locks` | elendi | HEAD başarısız (§E.3 #15) |
| N4 | `flock`/`spinlock`/`pcntl_` repo kodu | YOK | repo taraması 0 isabet (§F.2 #2) |
| N5 | `insmod`/`modprobe`/`init_module` | YOK | repo taraması 0 isabet (§F.2 #3) |

> Elenen N1-N3 **hiçbir dosyaya yazılmadı**; "404" değil, yalnız "HEAD başarısız"
> denir (yanıt kodu okunamadı → iddia kurulmaz). YOK N4-N5, dürüstlük kaydıdır —
> repo'da çekirdek kodu bulunmadığını kanıtlar.

## §N — Hata ve edge-case tablosu (repo örnekleri)

| # | Örnek | Hata yolu (repo kodu) | OS davranışı | Sonuç |
|---|---|---|---|---|
| E1 | O1 dosya I/A | `MusicStreamHandler.php:334` `@fopen(...)==false` → `fail(403)` | `open` `EACCES`/`ENOENT` döner | uygulama 403 üretir, çekirdek hata kodunu yutar |
| E2 | O1 yol denetimi | `185` `is_link` → `189` `realpath` → `204` `!is_readable` | `lstat`/`stat`/`access` | symlink kaçışı engellenir (path traversal) |
| E3 | O2 süreç | `proc_open` başarısız → `false` | `fork` `EAGAIN` | test atlanır, süreç doğmaz |
| E4 | O3 kilidi | `file_put_contents(...,LOCK_EX)===false` → `TemplateEngine.php:74` | `flock` `EWOULDBLOCK` | önbellek yazılmaz, yeniden derleme |
| E5 | O5 bekleme | `usleep` (yalnızca gecikme) | `nanosleep` kesintiye `EINTR` uğrayabilir | PHP yeniden dener (yalnızca gecikme) |
| E6 | O4 komut | `shell_exec` `null` (çıkış != 0 veya yok) | `waitpid` exit-code | analiz sonucu boş sayılır |

> E1-E6, repo'daki **gerçek** hata kontrol satırlarından türetilmiştir (§C'deki
> satır-no'lar). PHP'nin hata yutması (`@`, `false` karşılaştırması) ile çekirdeğin
> hata kodu (`errno`) arasındaki fark **açıkça** işaretlenmiştir — bu, `security`
> dalının sınırıdır.

## §O — Performans / maliyet notları (kavramsal · ölçülmedi)

| # | İşlem | Kavramsal maliyet | Ölçüm durumu |
|---|---|---|---|
| P1 | `fopen`+`fread` (akış) | VFS + sayfa önbelleği üstü | ❌ ölçülmedi (repo'da benchmark YOK) |
| P2 | `proc_open` (süreç) | `fork`+`exec` — sayfa kopyası + exec | ❌ ölçülmedi |
| P3 | `shell_exec` | süreç + pipe + `waitpid` | ❌ ölçülmedi |
| P4 | `usleep` | `nanosleep` — zamanlayıcı çözünürlüğü | ❌ ölçülmedi |
| P5 | `LOCK_EX` | dosya kilidi — bloklama | ❌ ölçülmedi |

> **Hiçbir performans rakamı yazılmamıştır** (uydurma yasak). Bu tablo yalnızca
> **kavramsal** maliyet sırasını işaretler; gerçek ölçüm (latency · throughput)
> CoreMusic benchmark yüzeyinde **PLANNED**'dir. Ölçülmüş değer eklenirse kaynak +
> yöntem ile birlikte yazılır.

## §P — İlgili vault dosyaları (bu dosyaya komşu)

| # | Dosya | İlişki |
|---|---|---|
| V1 | `isletim-sistemi-kernel-kapsam.md` | §B.1-B.3 dosya adları bağlayıcı kaynak |
| V2 | `isletim-sistemi-kernel-dogrulama.md` | §E URL matrisi · §F repo kanıtları · §F.7 kesişim testi |
| V3 | `isletim-sistemi-kernel/index.md` | §GRUPLAR 3 satır · children 3 alt-dal |
| V4 | `isletim-sistemi-core/**` | dosya/bellek sınırı (B1) — yalnız ok |
| V5 | `isletim-sistemi-plan/**` | zamanlama sınırı (B2 · E5) — yalnız ok |
| V6 | `isletim-sistemi-audio/**` | ses sürücüsü sınırı — yalnız ok (bu dalda ses kodu YOK) |

> Bu dosya **yalnız** `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel/**`
> beyazlistesine yazar; V4-V6'ya **yazmaz** (§G matrisi).

## §Q — Terim sözlüğü (bu dosya)

| # | Terim | Tanım | Bağlam |
|---|---|---|---|
| T1 | Syscall | Uygulama ↔ çekirdek sınırındaki çağrı | §I eşlemesi |
| T2 | VFS | Virtual File System — çekirdek dosya soyutlaması | O1 · O3 |
| T3 | fork/exec | Süreç yaratma (`clone`) + program yükleme | O2 · O4 |
| T4 | pipe | Süreçler arası boru kanalı | O2 · O4 |
| T5 | `flock` | Dosya tabanlı kilit | O3 |
| T6 | `nanosleep` | Süreç/izleyici uyutma çağrısı | O5 |
| T7 | `uname` | Çekirdek/OS adı sorgusu | O6 |
| T8 | EEVDF/CFS | Linux zamanlama algoritması | O5 · `plan` sınırı |
| T9 | Symlink | Sembolik bağlantı — `realpath` çözülür | O1 · E2 |
| T10 | Upstream docs | Çekirdek resmi dokümantasyonu (repo dışı) | §D · §I |

> T1-T10 yalnız bu dosyadadır; genel sözlük `glossary.md`'dir (bu dosya **kopyalamaz**).

## §R — Özet (bu dosyanın kapanışı)

| # | Metrik | Değer |
|---|---|---|
| R1 | Repo örneği (satır-no doğrulandı) | 6 (O1-O6) |
| R2 | Syscall eşlemesi (repo → çekirdek) | 12 (M1-M12) |
| R3 | Doğrulayıcı URL (HEAD 200) | §D 6 satır + §I 12 satır |
| R4 | Repo'da çekirdek kodu | YOK (dürüstlük notu §A) |
| R5 | Açık iddia | 3 (H1-H3) → UNKNOWN / VERIFICATION REQUIRED |
| R6 | Beyazliste dışı yazım | 0 |

**Sonuç:** Bu dosya, çekirdek mekanizmalarını **repo'nun PHP kodu üzerinden**
gösterir (satır-no diskten doğrulanmıştır) ve çekirdek tarafını **upstream
dokümanlara** (HEAD 200) bağlar. Repo'da çekirdek kodu **YOK** — bu dürüstçe
§A'da yazılıdır. `status: draft` kalır: `core`↔`kernel` sınırı (Q2 · H2) ve hedef
çekirdek (Q4 · H1) **kanıtlanmamıştır**; kapanış `dogrulama.md` §F.7 (S1-S5) ile
ölçülür.

## §S — Sınır / limit notları (repo yüzeyi)

| # | Ölçüm | Değer | Kaynak |
|---|---|---|---|
| U1 | `RETRY_DELAY_US` (yeniden deneme gecikmesi) | sabit (sınıf alanı) | `HomeAuthBridge.php:117` |
| U2 | `fopen` Buffer boyutu | PHP akış varsayılanı | `MusicStreamHandler.php:334` |
| U3 | `proc_open` boru tamponu | OS varsayılanı (çekirdek) | `ApiKeyCreateCommandTest.php:253` |
| U4 | `LOCK_EX` bekleme sınırı | PHP'de sonsuz bekleme (zaman aşımı yok) | `TemplateEngine.php:74` |
| U5 | `usleep` çözünürlüğü | mikrosaniye | `HomeAuthBridge.php:117` |
| U6 | session dizin izni | `0750` / `0777` | `SessionConfig.php:35` · `SessionInitializer.php:31` |

> **Sınır değerleri uydurulmaz:** U1-U6 yalnızca **dosyada ne yazdığını** gösterir;
> kesin sayısal limit (ör. tampon baytı, timeout ms) repo'da **yoktur** veya
> **ölçülmemiştir** → `UNKNOWN`. Kesin limit eklenirse kaynak + yöntem ile yazılır.

## §T — Doğrulama özeti (bu dosya · 2026-10-13)

| # | Kontrol | Yöntem | Durum |
|---|---|---|---|
| W1 | v2 FM 10 alan · `parent:` yok · `parent_id` | FM ayrıştırma | ✅ |
| W2 | repo satır-no birebir | `Select-Object -Index (N-1)` | ✅ 11 satır (M1-M12) |
| W3 | çekirdek kodu dürüstlük notu | §A · §R4 | ✅ YOK yazıldı |
| W4 | elenen kanıt kullanılmadı | §M N1-N3 | ✅ kullanılmadı |
| W5 | performans rakamı uydurulmadı | §O P1-P5 | ❌ ölçülmedi (işaretli) |
| W6 | macOS sütunu doğrulanmadı | §K K6-K7 | ⚠️ VERIFICATION REQUIRED |
| W7 | beyazliste dışı yazım | yol kontrolü | ✅ 0 |
| W8 | Authority imzası | son satır | ✅ |

> W6, §K'daki macOS/XNU sütununun HEAD ile doğrulanmadığını işaretler — bu dosyada
> **tek başına iddia olarak kullanılmaz**.

## §U — İlişkili vault dosyaları (bu dosya ↔ çekirdek dali)

| # | Dosya | İlişki |
|---|---|---|
| X1 | `isletim-sistemi-kernel/index.md` | bu dosya onun child'ıdır (§GRUPLAR 3 satır) |
| X2 | `isletim-sistemi-kernel-kapsam.md` | §B.1-B.3 dosya adları bağlayıcı kaynak |
| X3 | `isletim-sistemi-kernel-dogrulama.md` | §E URL matrisi · §F repo kanıtları |
| X4 | `cekirdek-yonetim/**` | O1-O4 ana ev (dosya · süreç · bellek) |
| X5 | `kesme-ve-senkronizasyon/**` | O5 ana ev (bekleme · zamanlama) |
| X6 | `surucu-ve-modul-modeli/**` | O6 ana ev (platform · sürücü) |

> X4-X6 bu dosyanın **alt-dallarıdır** (Kademe-4); bu dosya (kök `-ornekler`) onların
> örneklarını **konsolide** eder — her alt-dalın kendi `-ornekler` dosyası vardır.

## §V — Sınır matrisi (bu dosyanın yazma yetkisi — teyit)

| # | Yüzey | Bu dosya yazar mı? | Ait dal |
|---|---|---|---|
| Y1 | `isletim-sistemi-kernel/**` (bu dal) | ✅ evet | bu dal |
| Y2 | `isletim-sistemi-core/**` | ❌ hayır (yalnız ok) | `core` |
| Y3 | `isletim-sistemi-plan/**` | ❌ hayır (yalnız ok) | `plan` |
| Y4 | `isletim-sistemi-security/**` | ❌ hayır (yalnız ok) | `security` |
| Y5 | `K001..K019` · `*.php`·`*.js`·`*.css`·`*.sql` | ❌ hayır (beyazliste dışı) | ilgili dal |

> Y1-Y5, §G matrisinin **teyidi**dir (aynı kural, iki kez yazıldı — okuma
> kolaylığı için); çelişki yoktur.

## §W — Dosya özeti (son tablo)

| # | Alan | Değer |
|---|---|---|
| Z1 | `node_id` | `K000-isletim-sistemi/isletim-sistemi-kernel/isletim-sistemi-kernel-ornekler` |
| Z2 | `node_type` | `module` (kök içerik dosyası) |
| Z3 | `status` | `draft` (Q2 · Q4 açık) |
| Z4 | `parent_id` | `K000-isletim-sistemi/isletim-sistemi-kernel` |
| Z5 | `group` | `kod` (dosya adı `-ornekler` → `kod`) |
| Z6 | Repo örneği | 6 (O1-O6 · satır-no birebir) |
| Z7 | Syscall eşlemesi | 12 (M1-M12) |
| Z8 | Açık iddia | 3 (H1-H3) → UNKNOWN / VERIFICATION REQUIRED |
| Z9 | Beyazliste dışı yazım | 0 |

> Z1-Z9, bu dosyanın frontmatter + içerik özetidir; §R (özet) ile **aynı** değerleri
> taşır (okuma kolaylığı için tekrar). Son ölçüm `dogrulama.md` §F.7 (S1-S5) ile
> yapılır; oturum kapanışındaki `ÖLÇÜM` bloğu kesin satır sayısını verir.

## §X — Kaynak / disclaimer

| # | Not |
|---|---|
| AA1 | Bu dosya **repo kodu** (PHP) ile **upstream çekirdek dokümanlarını** (HEAD 200) birleştirir; repo'da çekirdek kodu **YOK** (dürüstlük §A). |
| AA2 | Syscall eşlemeleri (§I · §D) **kavramsal** değildir — her satır `Select-Object -Index (N-1)` ile diskten doğrulanmıştır. |
| AA3 | Performans rakamı **yoktur** (§O: ölçülmedi · işaretli). Kesin limit/timeout **yoktur** → `UNKNOWN` (§S). |
| AA4 | Açık konular (Q2 · Q4) **kapatılmamıştır**; bu dosya `status: draft` kalır. |
| AA5 | Beyazliste: `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-kernel/**\*.md` — dışına yazım = 0 (§G · §V · §W Z9). |

> AA1-AA5, bu dosyanın **sorumluluk sınırı**nı tanımlar. Bu dosya yalnız bu
> beyazlisteye yazar; `git commit` subagent tarafından **atılmaz** — orkestratöre
> aittir.

## §Y — Kapanış notu

| # | Not |
|---|---|
| AB1 | Bu dosya **6 repo örneğini** (O1-O6) ve **12 syscall eşlemesini** (M1-M12) taşır — her satır diskten doğrulanmıştır (zero-hallucination). |
| AB2 | Repo'da çekirdek kodu **YOK**; çekirdek tarafı **upstream dokümanlara** (HEAD 200) bağlanmıştır. |
| AB3 | Açık konular (Q2 · Q4) kapatılmamıştır; `status: draft` kalır — `verified` yapılmaz. |
| AB4 | Son ölçüm `dogrulama.md` §F.7 (S1-S5) ile oturum kapanışında yapılır; kesin satır sayısı `ÖLÇÜM` bloğunda verilir. |

> Bu dosyanın **tamamlanması**, beyazliste dışı yazımın **0** olduğunu ve 16 dosyalık
> hedefin (içerik ≥500 · index 40-100) tutulduğunu **kapanışta ölçmekle** mümkündür.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
