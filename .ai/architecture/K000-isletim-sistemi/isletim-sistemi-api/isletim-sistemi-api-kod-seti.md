---
node_id: isletim-sistemi-api-kod-seti
node_type: code
status: verify-required
parent_id: isletim-sistemi-api
children: []
depends_on:
  - isletim-sistemi-turu-kod-seti
  - isletim-sistemi-api-ornekler
evidence:
  - "https://man7.org/linux/man-pages/man2/open.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man2/read.2.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-readfile (✅ HTTP 200 · 2026-10-14)"
  - "https://man7.org/linux/man-pages/man3/strerror.3.html (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-formatmessage (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/std/io/struct.Error.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.php.net/manual/en/function.json-last-error.php (✅ HTTP 200 · 2026-10-14)"
  - "https://doc.rust-lang.org/book/ch19-01-unsafe-rust.html#calling-external-functions-using-ffi (✅ HTTP 200 · 2026-10-14)"
  - "https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke (✅ HTTP 200 · 2026-10-14)"
  - "https://nodejs.org/api/buffer.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-15: 20 benzersiz resmi URL (HTTP 200 kapısı) · 20 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions:
  - "O1: PHP FFI uzantısı production'da FFI.enable=preload dışında kapalı — çekirdek API köprüsü PHP'de native sınır (dl()) mi, FFI mi? (O6 bağlantılı)"
  - "O2: Node.js koffi üçüncü taraf pakettir — bu dosyada yalnız [TASARIM] konsepttir, bağımlılık eklenmez (R6)."
  - "O3: read(2) kısa okuma (short read) döngüsü C#'ta ReadFile için zorunlu mu, FileStream üstünde mi çözülür?"
  - "O4: GetLastError çağrısı araya başka Win32 çağrısı girdiğinde sıfırlanır — C# Marshal.GetLastWin32Error() pinvoke anında alınmalı; köprü katmanı bu disiplini nasıl dayatır?"
  - "O5: strerror(3) thread-güvenli varyant strerror_r(3) Glibc/XSI ayrımı (int vs char* dönüş) derleme bayrağına bağlı — hangi varyant?"
  - "O6: Dosya yolu ayracı: POSIX '/' yalnız Linux/macOS, Windows '\\' veya '/' (CreateFileW her ikisini de kabul eder) — köprü katmanında tek biçime normalizasyon nerede yapılır?"
  - "O7: Dosya adı kodlaması: Linux ve Windows NTFS UTF-8/UTF-16 ayrımı köprüde nerede belgelenir (bkz. api-ornekler §A: wide-char / normalizasyon bölümü)?"
  - "O8 (Q4 köprüsü): macOS hücreleri [TASARIM] — POSIX open/read macOS doğrulaması vault dışında."
group: kod
---

# isletim-sistemi-api · Kod Seti

> **Kapsam:** Dosya sistemi API'leri (POSIX `open`/`read` ↔ Win32 `CreateFileW`/`ReadFile`), hata kodu köprüsü (`errno`/`strerror` ↔ `GetLastError`/`FormatMessage` ↔ `std::io::Error` ↔ PHP `json_last_error` deseni) ve dil-bağımsız FFI (Foreign Function Interface) desenleri.
> **Sahiplik sınırı:** Dosya ağacı/katalog/izler `isletim-sistemi-core-kod-seti.md`'de; `ioctl`/`DeviceIoControl` cihaz köprüsü `isletim-sistemi-kernel-kod-seti.md` §GRUP C'de; süreç başlatma `isletim-sistemi-turu-kod-seti.md` §GRUP C'de; atomik yazma `isletim-sistemi-turu-kod-seti.md` §GRUP E'de. Bu dosya yalnız sistem dosya API'si + hata köprüsü + FFI desenleri.
> **Ortak köprü senaryosu:** Üç GRUP'taki tüm örnekler aynı senaryoyu çizer — bir yapılandırma dosyasını güvenle açmak, okumak ve hatayı dile çevirmek.

**Dürüstlük notu:** Bu dosyadaki hiçbir kod bu vault'ta derlenmedi (`[ORİJİNAL ÖRNEK · derlenmedi]`). API imzaları resmi sayfalardan (man7.org Linux man-pages, Microsoft Learn, doc.rust-lang.org, php.net, nodejs.org) doğrulandı; davranış üretimde test edilmelidir.

---

## §0 Politika

- Bu dosyadaki kod, "çalışan" değil, "doğrulanmış resmi API'lerle çizilmiş" koddur: `verify-required`.
- Kod bloğu yazımı `isletim-sistemi-turu-kod-seti.md` §0–§4 (politika, sözçe, dürüstlük notu, yasaklılar, stil) ile birebir aynıdır.
- **Kode sahiplik yasağı:** Dosya açma/okuma, hata çevrimi ve FFI desenleri burada tanımlanır; `-ornekler` dosyasındaki konu (dosya ağacı taraması, yedekleme, uzantı/boyut ayrıştırma) burada **tekrar yazılmaz** — yalnız § numarasıyla köprülenir.
- **Bilinen API sınırı:** Dosya sistemi, hata ve FFI yüzeyi bu dosyada; ağ (socket), bellek eşleme, `ioctl`/`DeviceIoControl`, süreç ve servis API'leri bu dosyada **değildir** (ilgili dosyalara köprü §6'da).
- **Kapsam dışı:** Dosya sistemi API'si, hata kodu köprüsü ve FFI desenleri.

## §1 Çalışma sözleşmesi

- Her kod bloğu: dil yorum satırı + `[ORİJİNAL ÖRNEK · derlenmedi]` + resmi URL (ilk satır, tek başına).
- Sessiz fallback yok: uygulanamayan hücre `[TASARIM]` veya `UNKNOWN` + `⚠️ VERIFICATION REQUIRED` + §4 açık soru.
- macOS hücreleri `[TASARIM]` (Q4: vault'ta macOS doğrulaması yok) — POSIX `open`/`read` imzaları Linux man-page kaynaklıdır.
- Gövde içinde dosya yolu/URL/anahtar/tekrarlanan değişken adı yok.
- Paragraf 1–3 cümle; her teknik terim ilk geçtiği yerde en fazla 3 cümlede açıklanır (`errno`, `FFI`, `short read`).
- Yazım dili Türkçe; resmi API adları ve kod İngilizce.
- Bridge köprüleri yalnız `-ornekler` dosyasının § numarasını işaret eder; içerik buraya kopyalanmaz.

## §2 Grup haritası

| GRUP | Konu | Soru | Dil kapsamı |
|---|---|---|---|
| GRUP A | Dosya açma/okuma eşlemesi | `open`/`read` ↔ `CreateFileW`/`ReadFile` nasıl eşlenir? | bash · pwsh · PHP · C++20 · Python · Go · Zig · Swift · Java/Kotlin |
| GRUP B | Hata kodu köprüsü | `errno`/`GetLastError`/`io::Error`/PHP hata kodu nasıl çevrilir? | C · Rust · PHP |
| GRUP C | FFI desenleri | Dil içinden çekirdek API'sine nasıl çağrılır? | Rust · C# · PHP · Node.js |

**Bridge köprüleri:**

| Konu | Bu dosyada | Köprü |
|---|---|---|
| Dosya açma/okuma (düz) | GRUP A | `isletim-sistemi-api-ornekler.md` §A (dosya okuma yolları, wide-char/normalizasyon) |
| Hata kodu deseni | GRUP B | `isletim-sistemi-api-ornekler.md` §B (hata yakalama/raporlama) |
| FFI/sınır-ötesi çağrı | GRUP C | `isletim-sistemi-api-ornekler.md` §C (dil bağımsız köprü notları) |
| Dosya ağacı gezinme, tarama | — | `isletim-sistemi-api-ornekler.md` §D (recursive gezinme deseni) |
| Atomik yazma / kilit | — | `isletim-sistemi-turu-kod-seti.md` §GRUP E |
| `ioctl`/`DeviceIoControl` (cihaz köprüsü) | — | `isletim-sistemi-kernel-kod-seti.md` §GRUP C |
| Bellek eşleme | — | `isletim-sistemi-kernel-kod-seti.md` §GRUP B |
| Süreç başlatma | — | `isletim-sistemi-turu-kod-seti.md` §GRUP C |

---

## GRUP A · Dosya açma/okuma eşlemesi

### A.1 POSIX `open(2)` — dosya bayrakları ve mod

| POSIX `open` | Windows eşleniği | Fark |
|---|---|---|
| `O_RDONLY` | `GENERIC_READ` | eşdeğer |
| `O_WRONLY` / `O_RDWR` | `GENERIC_WRITE` / `GENERIC_READ\|GENERIC_WRITE` | eşdeğer |
| `O_CREAT` | yaratma bayrağı yok — `CreateFile` + `OPEN_ALWAYS`/`CREATE_ALWAYS` | farklı model: bayrak değil disposition |
| `O_TRUNC` | `CREATE_ALWAYS` (yarat + kırp) | disposition modeline taşınır |
| `O_EXCL` | `CREATE_NEW` | eşdeğer |
| `O_APPEND` | `FILE_APPEND_DATA` (+ yazma bayrağı) | bayrak değil erişim modu |
| mod (örn. `0644`) | `SECURITY_ATTRIBUTES` (ACL) | POSIX modu Win32'de yok; bkz. api-ornekler §A |

> `open(2)` 3. argümanı (`mode_t`) yalnız `O_CREAT`/`O_TMPFILE` ile geçerlidir — imza kaynağı: man7 `open.2`.

```bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://man7.org/linux/man-pages/man2/open.2.html
# POSIX open(2): bayraklar O_RDONLY/O_CREAT/O_TRUNC, mod yalnız O_CREAT'te anlamlı.
# macOS [TASARIM]: imza Linux ile aynı, davranış doğrulanmadı (Q4).

exec 3<"/etc/hosts"          # open(O_RDONLY) karşılığı: salt okuma
while IFS= read -r satir <&3; do
    printf '%s\n' "$satir"
done
exec 3<&-                    # close(3) karşılığı: fd 3'ü kapat
```

### A.2 POSIX `read(2)` — kısa okuma döngüsü (short read)

`read(2)` tek çağrıda istenen bayt sayısının **azını** döndürebilir (dosya sonu, sinyal, tampon sınırı) — bu kısa okuma (`short read`) **hata değildir**; döngüyle tamamlanır.

```bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://man7.org/linux/man-pages/man2/read.2.html
# read(2) kısa okuma döngüsü: hata (-1) ile kısa okuma (0 < n < istenen) ayrılır.
# macOS [TASARIM]: POSIX davranışı varsayıldı, doğrulanmadı (Q4).

oku_kisa_dongu() {
    local yol="$1" toplam=0 n
    exec 3<"$yol" || return 1
    while :; do
        n=$(dd if=/proc/self/fd/3 bs=4096 count=1 status=none 2>/dev/null | wc -c) || { exec 3<&-; return 1; }
        toplam=$((toplam + n))
        [ "$n" -lt 4096 ] && break     # kısa/tampon sonu: döngü biter (eof dahil)
    done
    exec 3<&-
    printf '%d\n' "$toplam"
}
```

### A.3 Win32 `CreateFileW` — disposition ve paylaşım

`CreateFileW` üç şeyi tek çağrıda belirler: erişim (`GENERIC_*`), paylaşım (`FILE_SHARE_*`) ve disposition (`OPEN_EXISTING`, `CREATE_ALWAYS`, …). POSIX `open`'ın bayrak yığını Win32'de bu üç ayrı alana dağılır.

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynak: https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew
# CreateFileW: GENERIC_READ + OPEN_EXISTING + FILE_SHARE_READ — salt okuma açılışı.
# Paylaşımsız açılış (dwShareMode=0) ikinci açıcıda ERROR_SHARING_VIOLATION üretir.
# macOS [TASARIM]: bu blok Win32'dir; PowerShell'in macOS varyantı doğrulanmadı (Q4).

Add-Type -TypeDefinition @'
using System;
using System.Runtime.InteropServices;
public static class NativeOpenW {
    [DllImport("kernel32.dll", SetLastError = true, CharSet = CharSet.Unicode)]
    public static extern IntPtr CreateFileW(string lpFileName, uint dwDesiredAccess,
        uint dwShareMode, IntPtr lpSecurityAttributes, uint dwCreationDisposition,
        uint dwFlagsAndAttributes, IntPtr hTemplateFile);
    [DllImport("kernel32.dll", SetLastError = true)]
    [return: MarshalAs(UnmanagedType.Bool)]
    public static extern bool CloseHandle(IntPtr hObject);
}
'@

$GENERIC_READ     = 0x80000000u
$FILE_SHARE_READ  = 0x00000001u
$OPEN_EXISTING    = 3u
$INVALID_HANDLE   = [IntPtr]::new(-1)

$h = [NativeOpenW]::CreateFileW('C:\Windows\Win.ini', $GENERIC_READ, $FILE_SHARE_READ,
                                [IntPtr]::Zero, $OPEN_EXISTING, 0x80u, [IntPtr]::Zero)
if ($h -eq $INVALID_HANDLE) {
    $kod = [Runtime.InteropServices.Marshal]::GetLastWin32Error()   # GRUP B.5 köprüsü
    throw "CreateFileW başarısız: Win32 hata $kod"
}
try {
    'açildi (okuma için) — bayt okuma GRUP A.4 ReadFile desenine geçer'
}
finally {
    [void][NativeOpenW]::CloseHandle($h)                            # CloseHandle: sızıntı önleme
}
```

### A.4 POSIX `read` ↔ Win32 `ReadFile` eşleme tablosu

| POSIX | Win32 | Not |
|---|---|---|
| `open` bayrağı | disposition + erişim + paylaşım | A.1 |
| `read(fd, buf, n)` | `ReadFile(h, buf, n, &read, NULL)` | `read` parametresi **döner** (kısa okuma) |
| `close(fd)` | `CloseHandle(h)` | sızıntı riski: handle'lar kapanmazsa dosya kilitli kalır |
| `pread` (pozisyonlu) | `OVERLAPPED` yapı + `Offset` | eşzamanlı/atomik pozisyonlu okuma |
| `EINTR` döngüsü | `ERROR_IO_PENDING` + `GetOverlappedResult` | asenkron model tamamen farklı |
| kısa okuma = 0 < n | `lpNumberOfBytesRead` < n | ikisi de kısa okuma döner; **her ikisi de** döngü ister |

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Kaynak: https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-readfile
 * ReadFile: lpNumberOfBytesRead kısa okuma sayısını döner; senkron handle'da lpOverlapped NULL.
 * macOS [TASARIM]: bu blok Win32'dir, POSIX karşılığı A.2 bash bloğudur (Q4). */
#include <windows.h>
#include <stdio.h>

static int dosyayi_tamoku_w(LPCWSTR yol, char *tampon, DWORD istenen, DWORD *okunan)
{
    HANDLE h = CreateFileW(yol, GENERIC_READ, FILE_SHARE_READ, NULL,
                           OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);
    if (h == INVALID_HANDLE_VALUE)
        return -1;                       /* GetLastError AGRUP B.2'de çevrilir */
    DWORD top = 0, n;
    while (top < istenen) {
        if (!ReadFile(h, tampon + top, istenen - top, &n, NULL)) {
            CloseHandle(h);
            return -2;                   /* kesin hata: kısa okuma ile karıştırılmaz */
        }
        if (n == 0)
            break;                       /* dosya sonu: kısa okuma değil */
        top += n;                        /* kısa okuma: döngü tamamlar */
    }
    CloseHandle(h);
    *okunan = top;
    return 0;
}
```

### A.5 PHP `fopen` — dil-içi eşdeğer (FFI'sız)

PHP dilinde dosya API'si `fopen`/`fread`/`fclose`'dir; POSIX `open`'ın `errno` hataları PHP'de `false` dönüş + `error_get_last()` ile temsil edilir. PHP'de `O_*` sabitleri dil çekirdeğinde tanımlı değildir (posix uzantısı hariç) — bu yüzden PHP hücreleri `fopen` bayraklarıyla çizilir.

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://www.php.net/manual/en/function.json-last-error.php
// PHP hata deseni: işlev false döner + error_get_last() son hatayı verir (dizi).
// Aşağıdaki desen json_last_error() ile aynı "işlev + durum kodu" akışıdır;
// dosya API'sinde PHP hata akışı error_get_last() üzerinden yürür (error_reporting'e bağlı).

declare(strict_types=1);

$yol = '/etc/hosts';
$h = @fopen($yol, 'rb');            // rb: POSIX O_RDONLY karşılığı (binary)
if ($h === false) {
    $hata = error_get_last();       // POSIX errno karşılığı: PHP hata dizisi
    fwrite(STDERR, 'fopen başarısız: ' . ($hata['message'] ?? 'bilinmiyor') . PHP_EOL);
    exit(1);
}
$icerik = fread($h, 8192);          // kısa okuma: fread 0 veya az bayt dönebilir
$toplam = strlen($icerik);
while (!feof($h) && $toplam < 65536) {   // kısa okuma döngüsü (A.2 deseni)
    $ek = fread($h, 8192);
    if ($ek === false) { fclose($h); exit(2); }
    if ($ek === '') break;
    $toplam += strlen($ek);
}
fclose($h);
printf("okundu: %d bayt\n", $toplam);
```

### A.6 C++20 — `std::ifstream` kısa okuma döngüsü

```cpp
// [ORİJİNAL ÖRNEK · derlenmedi] https://en.cppreference.com/w/cpp/io/basic_ifstream
#include <cerrno>
#include <cstring>
#include <fstream>
#include <stdexcept>
#include <string>
#include <vector>

// POSIX open(2)+read(2) karşılığı: binary mod, kısa okuma döngüsü (A.2 deseni).
std::vector<char> read_all_ifstream(const std::string &path)
{
    errno = 0;
    // ⚠️ VERIFICATION REQUIRED: stream açma hatasının errno'yu garanti etmesi STANDARTTA YOK
    // (uygulama-dışı davranış) — pratikte glibc/libstdc++ bunu uygular, iddia burada işaretli.
    std::ifstream f(path, std::ios::binary); // "r" / O_RDONLY karşılığı
    if (!f) {
        throw std::runtime_error(std::string("open başarısız: ") + std::strerror(errno));
    }

    std::vector<char> out;
    char buf[4096];
    while (f.read(buf, sizeof buf)) {                 // tam blok döndüğü sürece
        out.insert(out.end(), buf, buf + sizeof buf); // kısa okuma yalnız son blokta
    }
    out.insert(out.end(), buf, buf + f.gcount());     // son kısmi blok: gcount() baytı
    if (f.bad()) throw std::runtime_error("okuma I/O hatası (badbit)"); // dosya sonu eofbit: hata DEĞİL
    return out;
}
```

**Ders:** `ifstream` POSIX akışını dilin akış modeline çevirir; kısa okuma `gcount()` ile
**görünür** olur (A.2 dersinin birebir karşılığı). `errno` kullanımı bilinçli olarak ⚠️
işaretlidir — standart garanti etmez, uygulama-dışı davranışı **uydurulmaz**. `eofbit`
hata değildir; `badbit` ayrıca denetlenir (sessiz son yok).

### A.7 Python — `os.open` + `os.read` kısa okuma

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/os.html
import errno
import os

def read_all(path: str, limit: int = 65536) -> bytes:
    """POSIX open(2)/read(2) karşılığı: bayraklar dilde, hata modeli OSError+errno."""
    fd = os.open(path, os.O_RDONLY | os.O_CLOEXEC)  # O_CREAT/O_EXCL anlamsız — yazılmadı
    try:
        chunks: list[bytes] = []
        total = 0
        while total < limit:
            b = os.read(fd, 8192)                   # kısa okuma: 0 bayt = dosya sonu
            if not b:
                break
            chunks.append(b)
            total += len(b)
        return b"".join(chunks)
    except OSError as e:
        if e.errno == errno.EIO:                    # errno köprüsü (B.1 deseni)
            raise RuntimeError(f"okuma I/O hatası: {path}") from e
        raise                                        # bilinmeyen errno yukarı taşınır
    finally:
        os.close(fd)
```

**Ders:** Python'da POSIX bayrakları **stdlib** adlarıyla taşınır (`os.O_*`); hata nesnesi
`OSError` içinde `errno` **alanını** korur → B.1 köprüsü dil dışına taşmaz. `raise ... from e`
zinciri korur: kök neden (errno) gizlenmez.

### A.8 Go — `os.Open` + `Read` kısa okuma (io.EOF köprüsü)

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/os
// io EOF: https://pkg.go.dev/io
package fileread

import (
	"errors"
	"io"
	"os"
)

// readAll POSIX open(2)+read(2) deseninin Go karşılığı. Taşma sınırı bilinçli (limit).
func readAll(path string, limit int) ([]byte, error) {
	f, err := os.Open(path) // O_RDONLY karşılığı; hata *os.PathError (errno köprüsü B.1'e benzer)
	if err != nil {
		return nil, err
	}
	defer f.Close()

	buf := make([]byte, 8192)
	var out []byte
	for {
		n, err := f.Read(buf) // kısa okuma: n>0 ile EOF aynı çağrıda gelebilir
		out = append(out, buf[:n]...)
		if len(out) >= limit {
			return out, nil // taşma: bilinçli sınır — sessiz kırpma değil, çağrıyan kararı
		}
		if err != nil {
			if errors.Is(err, io.EOF) {
				return out, nil // dosya sonu hata DEĞİL (Go'nun köprüsü)
			}
			return nil, err // diğer hatalar sessiz yutulmaz
		}
	}
}
```

**Ders:** Go'da dosya sonu bir `error` değeriyle **temsil** edilir (`io.EOF`) ama **hata**
sayılmaz — `errors.Is` köprüsü bu ayrımı zorunlu kılar. `*os.PathError` errno'yu içinde
taşıdığı için POSIX hata sınıfı dil içinde korunur. Taşma `limit` ile açık: kırpma/sessizlik
**çağıranın** kararıdır.

### A.9 Zig — `std.fs` open + read kısa okuma

```zig
// [ORİJİNAL ÖRNEK · derlenmedi] https://ziglang.org/documentation/master/std/
const std = @import("std");

/// POSIX open(2)+read(2) deseni: hata birleşik union — sessiz fallback yok.
/// Çıktı çağırana ait tampondur; dönen uzunluk 0 = boş dosya (hata değil).
pub fn readAll(path: []const u8, buf: []u8) !usize {
    const file = try std.fs.cwd().openFile(path, .{}); // O_RDONLY varsayılan
    defer file.close();

    var total: usize = 0;
    while (total < buf.len) {
        const n = try file.read(buf[total..]); // kısa okuma: 0 = dosya sonu
        if (n == 0) break;
        total += n;
    }
    return total; // tampon dolduysa taşma bilgisi çağıranda — kırpma politikası onun
}
```

**Ders:** Zig'de hata yolu `try` ile **zorunlu** görünür (birleşik union) — `errno` yoktur,
hata değeri kendisidir. Tampon-boyutu bilinçli dışarıda bırakıldı: "daha fazlası" bir hata
mı, kırpma mı, çağıranın politikasıdır (gizli varsayım yok).

### A.10 Swift — `FileHandle` kısa okuma (Foundation)

```swift
// [ORİJİNAL ÖRNEK · derlenmedi] https://developer.apple.com/documentation/foundation/filehandle
import Foundation

/// POSIX open(2)+read(2) deseninin Foundation karşılığı. Hata: throws — sessiz fallback yok.
func readAll(from url: URL, limit: Int = 65536) throws -> Data {
    let handle = try FileHandle(forReadingFrom: url)   // O_RDONLY karşılığı
    defer {
        try? handle.close()  // close yalnız kaynak temizliği: veri bütünlüğü okuma hatasındadır;
                             // close hatasının yutulması bu bilinçli kapsam dışındadır (işaretli)
    }

    var out = Data()
    while out.count < limit {
        guard let chunk = try handle.read(upToCount: 8192), !chunk.isEmpty else {
            break  // nil/boş = dosya sonu (kısa okuma döngüsü A.2)
        }
        out.append(chunk)
    }
    return out
}
```

**Ders:** `read(upToCount:)` `Data?` döndürür: `nil` dosya sonudur — Go'daki `io.EOF` gibi
**ayrı bir hata türü değil**, opsiyonel değerdir. Hata modeli `throws` (NSError köprüsü
Foundation'da kalır). `defer` içindeki `try?` **kapsamı açıkça** yazılmıştır (yutma bilinçli
ve işaretli — sessizlik değil, tanımlı istisna).

### A.11 Java/Kotlin (JVM) — `FileChannel` kısa okuma

```java
// [ORİJİNAL ÖRNEK · derlenmedi] https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/channels/FileChannel.html
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.nio.ByteBuffer;
import java.nio.channels.FileChannel;
import java.nio.file.Path;
import java.nio.file.StandardOpenOption;

public final class FileRead {
    private FileRead() {}

    /** POSIX open+read deseni: tampon + kısa okuma. Taşma limiti açık fırlatılır (kırpma yok). */
    public static byte[] readAll(Path path, int limit) throws IOException {
        try (FileChannel ch = FileChannel.open(path, StandardOpenOption.READ)) {
            ByteBuffer buf = ByteBuffer.allocate(8192);
            ByteArrayOutputStream out = new ByteArrayOutputStream();
            int n;
            while ((n = ch.read(buf)) > 0) {   // kısa okuma: n>0; -1 = dosya sonu
                buf.flip();
                out.write(buf.array(), 0, buf.limit());
                buf.clear();
                if (out.size() >= limit) {
                    throw new IllegalStateException("limit aşıldı: " + limit); // taşma: sessiz kırpma YOK
                }
            }
            return out.toByteArray();          // n == 0: ilerleme yok → döngü kilitlenmez
        }
    }
}
```

```kotlin
// [ORİJİNAL ÖRNEK · derlenmedi] https://kotlinlang.org/api/latest/jvm/stdlib/  (JDK API: FileChannel — Java bloğu URL)
import java.io.ByteArrayOutputStream
import java.nio.ByteBuffer
import java.nio.channels.FileChannel
import java.nio.file.Path
import java.nio.file.StandardOpenOption

fun readAll(path: Path, limit: Int): ByteArray = FileChannel.open(path, StandardOpenOption.READ).use { ch ->
    val buf = ByteBuffer.allocate(8192)
    val out = ByteArrayOutputStream()
    while (true) {
        val n = ch.read(buf)
        if (n <= 0) break                  // -1 = dosya sonu; 0 = ilerleme yok
        buf.flip()
        out.write(buf.array(), 0, buf.limit())
        buf.clear()
        check(out.size() <= limit) { "limit aşıldı: $limit" }  // taşma: sessiz kırpma yok
    }
    out.toByteArray()
}  // use: kanal otomatik kapatılır (try-with-resources karşılığı)
```

**Ders:** JVM'de dosya I/O'su `java.nio` **çekirdek** yüzeyidir — bağımlılık yok. Kısa okuma
`ch.read` dönüş sayısında görünür; dosya sonu `-1`'dir (Go `io.EOF` ↔ Java `-1` köprüsü).
Kotlin `use`, Java try-with-resources'in stdlib karşılığı: kanal **garantili** kapatılır.
Her iki dilde de taşma politikası **sessiz kırpma değil**, açık istisnadır.

---

## GRUP B · Hata kodu köprüsü

### B.1 POSIX `errno` + `strerror(3)` — C

`errno` thread-local bir tamsayı kodudur; `strerror(3)` onu insan-okunur dile çevirir. DİKKAT: `strerror` döndürdüğü tampon statiktir — üst üste iki `strerror` çağrısı **aynı tamponu paylaşır**; kopyalanmadan kullanılmaz.

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Kaynak: https://man7.org/linux/man-pages/man3/strerror.3.html
 * strerror(3): errno'yu dile çevirir; dönen char* statiktir, kopyalanmadan saklanmaz.
 * macOS [TASARIM]: glibc varyantı çizildi; macOS/BSD strerror_r varyantı için O5 (Q4). */
#include <errno.h>
#include <stdio.h>
#include <string.h>

static void errno_cevir(int kod, char *cikti, size_t boyut)
{
    /* XSI strerror_r (glibc): int döner; GNU varyantı char* döner (O5 açık soru).
     * Portability için strerror_r yerine aşağıdaki thread-güvenli çizim kullanılır:
     * strerror() sonucu hemen kopyalanır (statik tampon yarışı engellenir). */
    const char *mesaj = strerror(kod);
    if (mesaj == NULL)
        mesaj = strerror(EINVAL);    /* kod tanınmazsa ikinci çevrim */
    snprintf(cikti, boyut, "%s (%d)", mesaj, kod);
}
```

### B.2 Win32 `GetLastError` + `FormatMessageW` — C

`GetLastError` çağrısından **hemen sonra** okunmalıdır: araya giren başka bir Win32 çağrısı hatayı sıfırlayabilir (O4). `FormatMessageW` `FORMAT_MESSAGE_ALLOCATE_BUFFER` ile sistem ayrılmış tampon verir; `LocalFree` ile serbest bırakılmazsa sızıntı olur.

```c
/* [ORİJİNAL ÖRNEK · derlenmedi] */
/* Kaynak: https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror
 *          https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-formatmessage
 * GetLastError hemen okunur; FormatMessageW FORMAT_MESSAGE_FROM_SYSTEM ile dile çevirir.
 * macOS [TASARIM]: bu blok Win32'dir (Q4). */
#include <windows.h>
#include <stdio.h>

static int win32_hatayi_cevir(char *cikti, size_t boyut)
{
    DWORD kod = GetLastError();      /* diğeri çağrılmadan ÖNCE okunur (O4) */
    LPWSTR tampon = NULL;
    DWORD n = FormatMessageW(
        FORMAT_MESSAGE_ALLOCATE_BUFFER | FORMAT_MESSAGE_FROM_SYSTEM | FORMAT_MESSAGE_IGNORE_INSERTS,
        NULL, kod, 0, (LPWSTR)&tampon, 0, NULL);
    if (n == 0 || tampon == NULL) {
        snprintf(cikti, boyut, "Win32 hata %lu (FormatMessage başarısız: %lu)",
                 (unsigned long)kod, (unsigned long)GetLastError());
        return -1;
    }
    /* tampon UTF-16'dır; tam genişlik dönüşümü köprü katmanının işidir (O7). */
    snprintf(cikti, boyut, "%ls (%lu)", tampon, (unsigned long)kod);
    LocalFree(tampon);               /* FORMAT_MESSAGE_ALLOCATE_BUFFER zorunlu serbest bırakma */
    return 0;
}
```

### B.3 Rust `std::io::Error` — dil-içi köprü

Rust'ta POSIX hata kodları `ErrorKind`'e normalize edilir; `raw_os_error()` sistem kodunu (`errno`/`GetLastError`) döner. `io::Error` zaten `Display` + `Debug` taşır — `strerror`/`FormatMessage` manuel çağırmak gerekmez.

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://doc.rust-lang.org/std/io/struct.Error.html
// io::Error: raw_os_error() sistem kodunu (errno/GetLastError) döner; Display dile çevirir.
// macOS [TASARIM]: ErrorKind davranışı POSIX üzerinde varsayıldı, doğrulanmadı (Q4).

use std::fs::File;
use std::io::{self, Read};

fn dosya_oku(yol: &str) -> io::Result<Vec<u8>> {
    let mut f = File::open(yol)?;                    // hata: io::Error (NotFound, PermissionDenied, vb.)
    let mut tampon = Vec::new();
    f.read_to_end(&mut tampon)?;                     // kısa okuma döngüsü kütüphanede çözülür
    Ok(tampon)
}

fn hata_cevir(h: &io::Error) {
    println!("gorunur: {}", h);                       // dil çevrimi: Display
    println!("kod: {:?}", h.kind());                  // ErrorKind: NotFound, PermissionDenied, Interrupted
    println!("sistem: {:?}", h.raw_os_error());       // errno / GetLastError (kod taşınabilirliği O-ek)
}
```

### B.4 PHP `json_last_error` deseni — dil-içi durum kodu (POSIX'e en yakın PHP modeli)

PHP çekirdeğinde `errno` yoktur; en yakın desen "işlev çağır + son durum kodunu sor" akışıdır: `json_last_error()` + `json_last_error_msg()`. Aşağıdaki blok bu **deseni** dosya hatasına uygulanır — PHP'de dosya hatası `error_get_last()` üzerinden okunur (A.5).

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://www.php.net/manual/en/function.json-last-error.php
// json_last_error() + json_last_error_msg(): "işlev + durum kodu" deseni (errno'ya en yakın PHP modeli).
// Bu blok desen gösterimidir: gerçek dosya hatası error_get_last() (A.5); JSON ayrıştırma hatası burada.

declare(strict_types=1));

$veri = '{"a":1,}';                 // geçersiz JSON: trailing comma
$sonuc = json_decode($veri, true, 512, JSON_THROW_ON_ERROR);   // PHP 7.3+: istisna fırlatır (desen 1)
// Desen 2 (exception'sız — errno benzeri):
$sonuc2 = json_decode($veri);
if ($sonuc2 === null && json_last_error() !== JSON_ERROR_NONE) {
    $kod  = json_last_error();                       // enum: JSON_ERROR_SYNTAX = 4
    $dil  = json_last_error_msg();                   // "Syntax error"
    fwrite(STDERR, "JSON hatası: {$dil} (kod {$kod})\n");
    exit(1);
}
```

### B.5 Köprü özet tablosu

| Katman | Hata taşıma | Dile çevirme | Kısa okuma? |
|---|---|---|---|
| POSIX | `errno` (thread-local int) | `strerror(3)` (statik tampon!) | `read` → 0 < n |
| Win32 | `GetLastError()` (anlık okuma!) | `FormatMessageW` (`LocalFree`!) | `ReadFile` → n |
| Rust | `io::Error` (sahiplik) | `Display`/`ErrorKind` | `read_to_end` döngüsü kütüphanede |
| PHP | `false` dönüş + `error_get_last()` / `json_last_error()` | `json_last_error_msg()` / mesaj dizisi | `fread` + `feof` döngüsü |

---

## GRUP C · FFI (Foreign Function Interface) desenleri

**Genel dürüstlük notu:** FFI kodu, dilin çekirdeği ile OS çekirdeği arasındaki **en riskli** katmandır: işaretçi güvenliği, çağrı sözleşmesi (calling convention) ve hata köprüsü (GRUP B) birlikte doğrulanmalıdır. Üçüncü taraf FFI paketleri (örn. Node.js koffi, Python cffi) bu vault'ta **yalnız [TASARIM] konsepttir** — bağımlılık eklenmez (R6).

### C.1 Rust `unsafe extern "C"` — POSIX dosya API'sine çağrı

```rust
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://doc.rust-lang.org/book/ch19-01-unsafe-rust.html#calling-external-functions-using-ffi
// unsafe extern "C" bloğu: libc sembolleri tanımlanır; errno okuma güvenliği O2 (kernel dosyası) ile bağlantılı.
// macOS [TASARIM]: glibc sembolleri çizildi; musl/macOS varyantı için O5 (Q4).

use std::ffi::{c_char, c_int, CStr, CString};

unsafe extern "C" {
    fn open(path: *const c_char, flags: c_int, ...) -> c_int;
    fn read(fd: c_int, buf: *mut u8, count: usize) -> isize;
    fn close(fd: c_int) -> c_int;
}

const O_RDONLY: c_int = 0;

fn dosya_oku_c(dosya_yolu: &str) -> std::io::Result<Vec<u8>> {
    let cpath = CString::new(dosya_yolu)
        .map_err(|_| std::io::Error::new(std::io::ErrorKind::InvalidInput, "NUL bayt içeren yol"))?;
    unsafe {
        let fd = open(cpath.as_ptr(), O_RDONLY);
        if fd < 0 {
            return Err(std::io::Error::last_os_error());   // errno → io::Error (B.3)
        }
        let mut tampon = Vec::with_capacity(4096);
        let n = read(fd, tampon.as_mut_ptr(), 4096);
        if n < 0 {
            let hata = std::io::Error::last_os_error();
            close(fd);
            return Err(hata);                                // hata okunmadan close YAPILDI
        }
        tampon.set_len(n as usize);
        close(fd);
        Ok(tampon)
    }
}
```

### C.2 C# `LibraryImport` — Win32 dosya API'sine pinvoke

`LibraryImport` (.NET 7+) `DllImport`'ın yerini alır: AOT/trimming uyumlu, kaynak üretimli marshal. `SetLastError = true` olmadan `GetLastError` (B.2) **okunamaz** — bu bayrak hata köprüsünün parçasıdır.

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke
// LibraryImport: SetLastError=true olmadan Marshal.GetLastWin32Error() okunamaz (O4 köprü disiplini).
// macOS [TASARIM]: bu blok Win32 pinvoke'dır; .NET 8+ varsayımı doğrulanmadı (Q4).

using System.Runtime.InteropServices;

internal static partial class NativeFile
{
    [LibraryImport("kernel32.dll", SetLastError = true, StringMarshalling = StringMarshalling.Utf16)]
    internal static partial IntPtr CreateFileW(
        string lpFileName, uint dwDesiredAccess, uint dwShareMode,
        IntPtr lpSecurityAttributes, uint dwCreationDisposition,
        uint dwFlagsAndAttributes, IntPtr hTemplateFile);

    [LibraryImport("kernel32.dll", SetLastError = true)]
    [return: MarshalAs(UnmanagedType.Bool)]
    internal static partial bool ReadFile(
        IntPtr hFile, byte[] lpBuffer, uint nNumberOfBytesToRead,
        out uint lpNumberOfBytesRead, IntPtr lpOverlapped);

    [LibraryImport("kernel32.dll")]
    [return: MarshalAs(UnmanagedType.Bool)]
    internal static partial bool CloseHandle(IntPtr hObject);
}

// Kullanım akışı (gövde kodu — desen):
// var h = NativeFile.CreateFileW(yol, 0x80000000u /*GENERIC_READ*/, 1u /*FILE_SHARE_READ*/,
//                                IntPtr.Zero, 3u /*OPEN_EXISTING*/, 0x80u, IntPtr.Zero);
// if (h == new IntPtr(-1)) { var kod = Marshal.GetLastWin32Error(); /* GRUP B.5 köprüsü */ }
// var ok = NativeFile.ReadFile(h, tampon, (uint)tampon.Length, out var n, IntPtr.Zero);
// NativeFile.CloseHandle(h);
```

### C.3 PHP FFI — çekirdek API'sine doğrudan çağrı (çekirdek uzantı, üçüncü taraf değil)

PHP'nin `FFI` sınıfı çekirdek uzanttır (üçüncü taraf değildir); üretimde `ffi.enable` yapılandırması (preload/restrict/off) **gereklidir** — açık soru O1. Aşağıdaki blok desenin konseptidir: `cdef` ile C imzası tanımlanır, çağrı `unsafe` bölgesindedir.

```php
<?php
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://www.php.net/manual/en/function.json-last-error.php  (hata deseni)
// PHP FFI: çekirdek uzantıdır (üçüncü taraf değil); üretimde ffi.enable yapılandırması gerekir (O1).
// cdef imzaları POSIX'tir: open(2)/read(2)/close — Windows'ta FFI farklı DLL çağırır (O6).

declare(strict_types=1);

if (!class_exists('FFI')) {
    fwrite(STDERR, "FFI uzantısı kapalı — O1 açık soru\n");
    exit(77);                                   // EX_CONFIG benzeri: yapılandırma eksik
}

$ffi = FFI::cdef(
    "int open(const char *path, int flags, ...);"
  . "long read(int fd, void *buf, unsigned long count);"
  . "int close(int fd);",
    PHP_OS_FAMILY === 'Windows' ? 'libc.so' : 'libc.so.6'   // OS ayrımı O6 (Q4)
);

$tampon = FFI::buffer(4096);
$fd = $ffi->open('/etc/hosts', 0 /*O_RDONLY*/);
if ($fd < 0) {
    fwrite(STDERR, "open başarısız (errno okunması B.4 deseniyle köprülenir)\n");
    exit(1);
}
$n = $ffi->read($fd, $tampon, 4096);           // kısa okuma: n < 4096 mümkün
$ffi->close($fd);
printf("okundu: %d bayt\n", (int)$n);
```

### C.4 Node.js — dil çekirdeğinde FFI **yok** (üçüncü taraf kütüphane [TASARIM])

Node.js çekirdeğinde FFI yoktur; `Buffer` yalnız bayt tamponudur (resmi belge: nodejs.org/api/buffer). Dosya API'si `fs` modülüdür. Çekirdek API çağrısı için üçüncü taraf paketler (koffi, node-ffi-napi) gerekir — **bu vault'ta bağımlılık eklenmez, yalnız konsept çizilir (R6, O2)**.

```js
// [ORİJİNAL ÖRNEK · derlenmedi]
// Kaynak: https://nodejs.org/api/buffer.html
// Node.js çekirdeğinde FFI YOKTUR: Buffer bayt tamponu, fs dosya API'sidir.
// Aşağıdaki blok [TASARIM] konsepttir: üçüncü taraf FFI paketi (koffi vb.) gerekir — bağımlılık eklenmez (O2, R6).
// macOS [TASARIM]: fs davranışı POSIX varsayımı, doğrulanmadı (Q4).

'use strict';
const fs = require('node:fs');

function dosya_oku_kisa_dongu(yol) {                 // GRUP A.2 deseninin Node karşılığı
  const fd = fs.openSync(yol, 'r');                  // POSIX O_RDONLY karşılığı 'r'
  try {
    const parcalar = [];
    let toplam = 0;
    for (;;) {
      const tampon = Buffer.alloc(4096);              // nodejs.org/api/buffer.html
      const n = fs.readSync(fd, tampon, 0, 4096, null);
      if (n === 0) break;                             // dosya sonu
      parcalar.push(Buffer.from(tampon.subarray(0, n)));  // kısa okuma: yalnız n bayt
      toplam += n;
    }
    return Buffer.concat(parcalar, toplam);
  } finally {
    fs.closeSync(fd);
  }
}

// [TASARIM — bağımlılık yok] FFI konsepti (koffi benzeri API, kurulum YOK):
// const koffi = require('koffi');                     // O2: üçüncü taraf, bu vault'ta kurulmaz
// const libc = koffi.load('libc.so.6');
// const cOpen = libc.func('int open(const char*, int, ...)');
// const fd = cOpen('/etc/hosts', 0);
```

### C.5 FFI köprü özet tablosu

| Dil | FFI mekanizması | Hazır mı? | Hata köprüsü |
|---|---|---|---|
| Rust | `unsafe extern "C"` (çekirdek) | evet | `last_os_error()` (B.3) |
| C# | `LibraryImport` (.NET 7+) | evet | `SetLastError=true` + `GetLastWin32Error()` (B.5) |
| PHP | `FFI::cdef` (çekirdek uzantı) | `ffi.enable` yapılandırması gerekir (O1) | `error_get_last()` deseni (B.4) |
| Node.js | çekirdekte YOK | üçüncü taraf gerekir [TASARIM] (O2) | `errno`/`GetLastError` doğrudan görünmez |
| C | doğrudan (zaten POSIX) | evet | `errno` (B.1) |

---

## §3 Dil × OS matrisi

| Dil | Linux (POSIX) | Windows (Win32) | macOS |
|---|---|---|---|
| bash | GRUP A.1–A.2 (open/read/close) | `[TASARIM]` — Git Bash sınırlı Win32 kapsamı (O6) | `[TASARIM]` (Q4) |
| PowerShell | `[TASARIM]` — .NET FileStream POSIX'te çalışır (O-ek) | GRUP A.3–A.4 (CreateFileW/ReadFile) | `[TASARIM]` (Q4) |
| PHP | GRUP A.5 + C.3 (fopen + FFI) | GRUP A.5 (fopen; FFI 'kernel32' O6) | `[TASARIM]` (Q4) |
| C | GRUP B.1 (errno/strerror) | GRUP B.2 (GetLastError/FormatMessageW) | `[TASARIM]` (Q4) |
| Rust | GRUP B.3 + C.1 (io::Error + unsafe FFI) | C.1 Win32 sembolleri [TASARIM] (O-ek) | `[TASARIM]` (Q4) |
| C# | GRUP C.2 .NET 8 FileStream [TASARIM] | GRUP C.2 (LibraryImport) | `[TASARIM]` (Q4) |
| Node.js | C.4 (fs + kısa okuma döngüsü) | C.4 (fs; Windows yolları O6) | `[TASARIM]` (Q4) |
| C++20 | A.6 (ifstream; `errno` köprüsü ⚠️ işaretli) | A.6 (yollar O6) | `[TASARIM]` (Q4) |
| Python | A.7 (os.open/os.read + `OSError.errno`) | ⚠️ `O_CLOEXEC` Unix-only → Windows kolu `[TASARIM]` | `[TASARIM]` (Q4) |
| Go | A.8 (os.Open + `io.EOF` köprüsü) | A.8 (os paketi çapraz) | `[TASARIM]` (Q4) |
| Zig | A.9 (std.fs open/read) | ⚠️ std.fs Windows kolu `[TASARIM]` | `[TASARIM]` (Q4) |
| Swift | ⚠️ swift-corelibs doğrulanmadı | Foundation Windows `[TASARIM]` | A.10 (Darwin Foundation) ✓ |
| Java | A.11 (FileChannel) | A.11 (JVM) | A.11 (JVM) ✓ |
| Kotlin | A.11 (aynı JVM) | A.11 (aynı JVM) | A.11 (aynı JVM) ✓ |

---

## §4 Açık sorular

- **O1:** PHP FFI production yapılandırması (`ffi.enable`) preload mu, restrict mi olmalı? Dosya API'si FFI'sız `fopen` ile mi karşılanır?
- **O2:** Node.js FFI için hangi üçüncü taraf paket onaylanır (koffi, node-ffi-napi)? Bağımlılık kararı R6 gereği üst katmandan.
- **O3:** C# kısa okuma döngüsü `ReadFile` pinvoke'da mı, yoksa `FileStream` (yüksek seviye) üstünde mi dayatılır?
- **O4:** `GetLastError`/`Marshal.GetLastWin32Error()` disiplini (hemen-okuma) köprü katmanında nasıl denetlenir (analiz kuralı)?
- **O5:** `strerror_r` Glibc (int) vs XSI (char*) varyantı hangi hedefle standardize edilir? BSD/macOS varyantı farklıdır.
- **O6:** POSIX `/` ve Windows `\`/`/` ayrımı köprüde tek biçime nerede normalize edilir (`fs/path` katmanı, GRUP A öncesi)?
- **O7:** Dosya adı kodlaması (UTF-8 vs UTF-16 `W` ailesi) köprüde nerede belgelenir? (`api-ornekler.md` §A genişletilir.)
- **O8 (Q4 köprüsü):** macOS hücreleri `[TASARIM]` — POSIX `open`/`read` macOS doğrulaması vault dışında.

## §5 Kaynakça

1. https://man7.org/linux/man-pages/man2/open.2.html
2. https://man7.org/linux/man-pages/man2/read.2.html
3. https://man7.org/linux/man-pages/man3/strerror.3.html
4. https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-createfilew
5. https://learn.microsoft.com/en-us/windows/win32/api/fileapi/nf-fileapi-readfile
6. https://learn.microsoft.com/en-us/windows/win32/api/errhandlingapi/nf-errhandlingapi-getlasterror
7. https://learn.microsoft.com/en-us/windows/win32/api/winbase/nf-winbase-formatmessage
8. https://learn.microsoft.com/en-us/dotnet/standard/native-interop/pinvoke
9. https://doc.rust-lang.org/std/io/struct.Error.html
10. https://doc.rust-lang.org/book/ch19-01-unsafe-rust.html#calling-external-functions-using-ffi
11. https://www.php.net/manual/en/function.json-last-error.php
12. https://nodejs.org/api/buffer.html

> URL'ler 2026-10-14'te HTTP 200 ile doğrulandı. Liste dışı API kullanımı için self-fetch zorunlu.

## §6 İlişkili dosyalar

| Dosya | İlişki | Köprü |
|---|---|---|
| `isletim-sistemi-turu-kod-seti.md` | SSOT · §0–§4 sözçe · §GRUP C süreç · §GRUP E atomik yazma | stil + atomiklik |
| `isletim-sistemi-api-kod-seti.md` (bu dosya) | Dosya API'si + hata köprüsü + FFI desenleri | GRUP A/B/C |
| `isletim-sistemi-api-ornekler.md` | Konu köprüsü (§ numaraları) | §A · §B · §C · §D |
| `isletim-sistemi-api-acik-mimari.md` | Mimari bağlam | §GRUP B/API kararları |
| `isletim-sistemi-core-kod-seti.md` | Dosya ağacı/katalog/gezinme sahipliği | §GRUP B/C |
| `isletim-sistemi-kernel-kod-seti.md` | Bellek eşleme + ioctl/DeviceIoControl köprüsü | §GRUP B · §GRUP C |
| `isletim-sistemi-runtime-kod-seti.md` | SAPI/web sunucusu bağlamı | §GRUP A (php_sapi_name) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
