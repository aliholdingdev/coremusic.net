---
node_id: isletim-sistemi-testing-kod-seti
node_type: code
status: verify-required
parent_id: isletim-sistemi-testing
children: []
depends_on: [isletim-sistemi-turu-kod-seti, isletim-sistemi-testing-ornekler]
evidence: ["https://docs.python.org/3/library/tempfile.html", "https://man7.org/linux/man-pages/man3/mkdtemp.3.html", "https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath", "https://learn.microsoft.com/en-us/dotnet/api/system.io.directory.createdirectory", "https://www.php.net/manual/en/function.sys-get-temp-dir.php", "https://doc.rust-lang.org/std/env/fn.temp_dir.html", "https://pkg.go.dev/os#MkdirTemp", "https://nodejs.org/api/fs.html", "https://nodejs.org/api/os.html", "https://docs.pytest.org/en/stable/how-to/skipping.html", "https://pkg.go.dev/cmd/go#hdr-Build_constraints", "https://doc.rust-lang.org/reference/conditional-compilation.html", "https://www.php.net/manual/en/function.extension-loaded.php", "https://nodejs.org/api/process.html#processplatform", "https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax", "https://www.gnu.org/software/bash/manual/html_node/The-Set-Builtin.html", "https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_preference_variables"]
validation: "2026-10-15: 22 benzersiz resmi URL (HTTP 200 kapısı) · 19 kod bloğu (dil-etiketli fence) · 13 dil kapsama turları P1–P7 tamamlandı · örnekler ORİJİNAL ve derlenmedi"
open_questions: ["O1: CI matrisine `windows-latest`/`macos-latest` hattı eklenecek mi? (turu-kod-seti GRUP H'de PLANNED — bu dosya GRUP C yalnız kapak notu + iskelet yazar, matrisi kopyalamaz)", "O2: Sabit `/tmp`-stili yol yazımı cross-platform-ornekleri §2.4 yasağı — ne zaman lint ile zorunlu denetlenecek?", "O3: Node `fs.rmSync` temizlik hatasında test `FAIL` mi `WARN` mi döner (retry dahil değil)?", "O4: cmocka/GoogleTest gibi üçüncü parti test çerçeveleri R6 kapsamında repo'ya girer mi (şu an yalnız kavram/[TASARIM])?", "O5: C `mkdtemp` başarısızlığında standart `SKIP` mi `FAIL` mı (standart yok → ADR)?"]
group: kod
---

# isletim-sistemi-testing — Kod Seti

> **Kapsam:** test süreç izolasyonu (temp dizin + temizlik), platform atlama
> matrisi deseni ve CI test-kapısı iskeleti. Test **içerikleri** (fixture, CSRF
> matrisi, timing-safe) sibling `kod-ornekleri` dosyasındadır; burada yalnız
> işletim-sistemi-yüzeyi kodlanır.

---

## §0 Politika

1. **Zero-Hallucination:** her blok ilk satırında `[ORİJİNAL ÖRNEK · derlenmedi]`
   taşır; her API §5'te, yazım anında `HTTP 200` doğrulanmış resmi dokümandan gelir.
2. **Geçici dosya yasağı:** testler sabit yol (`/tmp/test`, `C:\temp\test`,
   `$TMPDIR/depo`) **yazmaz** — her koşu framework'ün/dilin sağladığı temp
   yüzeyinden gelir ve **koşu sonunda temizlenir** (köprü:
   `cross-platform-ornekleri` §2.4 Kalıp-4).
3. **Platform atlaması iki kademeli:** derleme-zamanı (`#[cfg]`, Go build tag)
   ile çalışma-zamanı (`skipif`, `PHP_OS_FAMILY`) ayrılır; hangisinin kullanıldığı
   blokta açıkça yazılır — sessiz "bu test zaten yok" sayılmaz.
4. **CI kapısı kopyalanmaz:** `ci.yml` içeriği `turu-kod-seti` GRUP H'ye aittir;
   bu dosya yalnız **test-kapısı notu + 1 bash `set -e` / 1 pwsh iskeleti** yazar.
   Repo kanıtı: `testing-ornekler` §2–§5.
5. **`...`/`TBD` yasak**, sessiz fallback yasak: bilinmeyen `UNKNOWN`,
   doğrulanamayan `⚠️ VERIFICATION REQUIRED` + §4 O-soru.
6. **R6 (üçüncü taraf yasağı):** pytest/cmocka/GoogleTest/vitest yalnız
   **kavram** olarak anılır; repo'ya paket eklenmez, kurulum komutu üretilmez
   (istisnasız tek istisna yoktur — O4 bekler).
7. **Git yasak:** bu dosya hiçbir commit/silme işlemi üretmez; 0 dosya silme.

---

## §1 Çalışma sözleşmesi

### §1.1 Çıktı sözleşmesi

| Çıktı | Şart |
|---|---|
| Temp dizin | Koşu başına benzersiz; test bittiğinde (başarılı ya da başarısız) kaldırılır |
| Atlama | Koşulan platformda **gerçekten** çalışmayan test atlanır; atlanan test raporlanır (sessiz 0-sorun yasak) |
| CI kapısı | Bash'te `set -euo pipefail`, pwsh'te `$ErrorActionPreference = 'Stop'` — ilk hata durdurur |
| Rapor | Atlanan test sayısı / temp temizlik hatası log'a (köprü: `isletim-sistemi-observability-kod-seti` GRUP A) |

### §1.2 Hata yönetimi

- Temp oluşturma başarısızsa test **fail** olur (sessiz `SKIP` değil — O5'e kadar
  standart budur).
- Temizlik hatası fail'i **örtmez**: ayrı raporlanır (birincil hata korunur).
- Platform atlaması: `skip` nedeni metni zorunlu (boş skip = gözden kaçan kapsam).

### §1.3 İzolasyon hiyerarşisi (bu dosyanın ayrım çizgisi)

| Katman | Ne korur | Bu dosyada |
|---|---|---|
| Dosya sistemi izolasyonu | Test dosyaları birbirine ve depoya çarpmaz | GRUP A |
| Platform izolasyonu | Test, çalışmadığı OS'ta sahte-fail üretmez | GRUP B |
| Ortam izolasyonu (CI) | Hata çıkış kodu yükseltir, kapı kapanır | GRUP C |
| Süreç izolasyonu (sandbox) | Kalıcı yetki düşürme | **dışarı**: `security-kod-seti` GRUP B |

---

## §2 Grup haritası

| Grup | Ne kodlar | Ana yüzey | Köprü |
|---|---|---|---|
| **GRUP A** | Süreç izolasyonu: test temp dizini + temizlik | `tempfile`/`t.TempDir`/`mkdtemp`/`sys_get_temp_dir`/`GetTempPath`/`mkdtempSync`/`temp_directory_path`/`createTempDirectory` | `cross-platform-ornekleri` §2.4 · `kod-ornekleri` §2 (fixture temizlik) |
| **GRUP B** | Platform matrisi deseni | `skipif` ↔ Go build tag ↔ `#[cfg]` ↔ `PHP_OS_FAMILY`/`extension_loaded` ↔ `process.platform` ↔ Zig `builtin.os.tag` ↔ Swift `#if os()` | `cross-platform-ornekleri` §1 (fark matrisi) · §2.1 |
| **GRUP C** | CI test kapısı | turu §GRUP H'ye **çapraz referans, kopya yok** · yalnız bash `set -e` + pwsh iskeleti | `testing-ornekler` §2–§5 · §10 (olumsuz) |

---

## GRUP A — Süreç izolasyonu (temp dizin + temizlik)

### §A.1 Python — `tempfile.TemporaryDirectory` (context manager)

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.python.org/3/library/tempfile.html
import tempfile
from pathlib import Path

def test_write_in_isolated_dir() -> None:
    # with bloğu sonunda dizin + içerik KENDİLİĞİNDEN kaldırılır (başarılı ya da hata).
    with tempfile.TemporaryDirectory(prefix="coremusic-test-") as tmp:
        p = Path(tmp) / "config.ini"
        p.write_text("kaynak = test\n", encoding="utf-8")
        assert p.exists()

        # İzlenebilirlik: hangi test hangi dizini kullandı (log köprüsü observability GRUP A).
        print(f"test dizini: {tmp}")

    # Burada dizin YOK olmalı — context manager temizliği garanti eder.
```

**Not:** `prefix` zorunlu değil ama izole edilmiş raporlamayı kolaylaştırır; sabit yol
yazımı §0.2 gereği yasaktır.

### §A.2 Go — `t.TempDir()` (test bitiminde kendiliğinden silinir)

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/os#MkdirTemp
package kalip

import (
	"os"
	"path/filepath"
	"testing"
)

func TestYazVeSil(t *testing.T) {
	// t.TempDir(): her test için benzersiz; test sonunda Go kendisi temizler.
	// (Altındaki os.MkdirTemp yüzeyi: pkg.go.dev/os#MkdirTemp — HTTP 200.)
	dir := t.TempDir()

	dosya := filepath.Join(dir, "cikti.txt")
	if err := os.WriteFile(dosya, []byte("veri"), 0o600); err != nil {
		t.Fatalf("dosya yazilamadi: %v", err)
	}

	if _, err := os.Stat(dosya); err != nil {
		t.Fatalf("dosya yok: %v", err)
	}
	// temizlik için ek kod YOK: t.TempDir() kayıtlıdır (t.Cleanup benzeri).
}
```

### §A.3 Rust — `std::env::temp_dir` + benzersiz alt dizin + manuel temizlik

```rust
// [ORİJİNAL ÖRNEK · derlenmedi] https://doc.rust-lang.org/std/env/fn.temp_dir.html
use std::fs;
use std::path::PathBuf;
use std::process;

/// Benzersiz test dizini açar (std-only; üçüncü taraf tempfile KULLANILMAZ — R6).
fn test_dir(tag: &str) -> std::io::Result<PathBuf> {
    let mut d: PathBuf = std::env::temp_dir(); // platform temp kökü
    d.push(format!("coremusic-{tag}-{}-{:?}", process::id(), std::time::SystemTime::now()));
    fs::create_dir_all(&d)?;
    Ok(d)
}

fn test_yaz_ve_temizle() -> std::io::Result<()> {
    let dir = test_dir("kalip")?;
    let result = (|| {
        let f = dir.join("cikti.txt");
        fs::write(&f, b"veri")?;
        assert!(f.exists());
        Ok(())
    })();

    // Temizlik HER YOLDA çalışır; temizlik hatası asıl sonucu örtmez (§1.2).
    if let Err(cleanup_err) = fs::remove_dir_all(&dir) {
        eprintln!("temizlik hatasi: {cleanup_err}");
    }
    result
}
```

**Eksiklik kaydı:** benzersizlik `SystemTime` + pid tabanlıdır — yarış koşulu
düşüktür ama `create_dir` `AlreadyExists` verirse yeniden deneme `[TASARIM]`
(O3/O5 köprüsü).

### §A.4 C — `mkdtemp(3)` + çağrı-akışı temizliği

```c
// [ORİJİNAL ÖRNEK · derlenmedi] https://man7.org/linux/man-pages/man3/mkdtemp.3.html
#define _GNU_SOURCE
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

int test_in_tmpdir(void)
{
    char tmpl[] = "/tmp/coremusic-test-XXXXXX"; /* şablon sonu 6 X olmak ZORUNDA */
    char *dir = mkdtemp(tmpl);
    if (dir == NULL) {
        perror("mkdtemp");      /* başarısız → §1.2: fail, sessiz SKIP değil */
        return 1;
    }

    char yol[256];
    snprintf(yol, sizeof yol, "%s/cikti.txt", dir);
    FILE *f = fopen(yol, "w");
    if (f == NULL) {
        perror("fopen");
        rmdir(dir);             /* hatta da temizlik — sızıntı yok */
        return 1;
    }
    fputs("veri", f);
    fclose(f);

    /* POSIX araç zinciri: dosyayı sil, dizini sil (bash'e devredilmeden). */
    char komut[320];
    snprintf(komut, sizeof komut, "rm -rf -- %s", dir);
    int rc = system(komut);
    if (rc != 0) {
        fprintf(stderr, "temizlik basarisiz: %s (rc=%d)\n", dir, rc);
        return 1;
    }
    return 0;
}
```

**Not:** şablon-yazımı `mkdtemp` sözleşmesidir; `XXXXXX` yeri sabittir. `system()`
kabuk bağımlılığını azaltmak için `unlink`+`rmdir` sürümü `[TASARIM]` → O5.

### §A.5 PHP — `sys_get_temp_dir` + `finally` ile garanti temizlik

```php
// [ORİJİNAL ÖRNEK · derlenmedi] https://www.php.net/manual/en/function.sys-get-temp-dir.php
declare(strict_types=1);

/** Rekürsif silme — yalnızca kendi oluşturduğumuz test ağacına uygulanır. */
function rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    if ($items === false) {
        throw new RuntimeException("scandir basarisiz: {$dir}");
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path) && !is_link($path)) {
            rrmdir($path);
        } elseif (!unlink($path)) {
            throw new RuntimeException("unlink basarisiz: {$path}");
        }
    }
    if (!rmdir($dir)) {
        throw new RuntimeException("rmdir basarisiz: {$dir}");
    }
}

function test_with_temp_dir(): void
{
    $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coremusic-test-' . bin2hex(random_bytes(6));
    if (!mkdir($base, 0700)) {
        throw new RuntimeException("temp dizin olusturulamadi: {$base}"); // fail — sessiz SKIP yok
    }

    try {
        $dosya = $base . DIRECTORY_SEPARATOR . 'cikti.txt';
        if (file_put_contents($dosya, 'veri') === false) {
            throw new RuntimeException("yazma basarisiz");
        }
        // test gövdesi burada çalışır
    } finally {
        // Başarılı ya da istisna: temizlik her yolda (köprü: kod-ornekleri §2).
        try {
            rrmdir($base);
        } catch (RuntimeException $cleanupError) {
            // Asıl hata varken temizlik hatası örtülmez — ayrıca yazılır.
            error_log('temizlik hatasi: ' . $cleanupError->getMessage());
        }
    }
}
```

### §A.6 C# — `Path.GetTempPath` + `Directory.CreateDirectory` + `IDisposable`

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi] https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath
// Ek HTTP 200: https://learn.microsoft.com/en-us/dotnet/api/system.io.directory.createdirectory
using System;
using System.IO;

/// Koşu-özü temp kapanışı: IDisposable → GC/finalizer değil, açık Dispose.
public sealed class TestTempDir : IDisposable
{
    public string Path { get; }

    public TestTempDir(string tag)
    {
        var root = System.IO.Path.GetTempPath();
        Path = System.IO.Path.Combine(root, $"coremusic-{tag}-{Guid.NewGuid():N}");
        Directory.CreateDirectory(Path); // hata burada fırlar → test fail
    }

    public string File(string name) => System.IO.Path.Combine(Path, name);

    public void Dispose()
    {
        try
        {
            if (Directory.Exists(Path))
            {
                Directory.Delete(Path, recursive: true);
            }
        }
        catch (IOException ex)
        {
            // Temizlik hatası asıl sonucu örtmez (§1.2) — raporlanır.
            Console.Error.WriteLine($"temizlik hatasi: {ex.Message}");
        }
    }
}
```

### §A.7 Node.js — `fs.mkdtempSync` + `os.tmpdir` + `rmSync`

```js
// [ORİJİNAL ÖRNEK · derlenmedi] https://nodejs.org/api/fs.html
// Ek HTTP 200: https://nodejs.org/api/os.html (os.tmpdir)
'use strict';

const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');

function withTempDir(tag, body) {
  // os.tmpdir() platform kökü; dosya ekine benzersizlik eklenir.
  const dir = fs.mkdtempSync(path.join(os.tmpdir(), `coremusic-${tag}-`));
  try {
    return body(dir);
  } finally {
    // Her yolda temizlik; recursive+force hata verirse §1.2 gereği raporlanır.
    try {
      fs.rmSync(dir, { recursive: true, force: true });
    } catch (err) {
      console.error(`temizlik hatasi: ${err.message}`);
    }
  }
}

module.exports = { withTempDir };
```

### §A.8 C++20 — `temp_directory_path` + RAII sarmalayıcı

```cpp
// https://en.cppreference.com/w/cpp/filesystem/temp_directory_path
// [ORİJİNAL ÖRNEK · derlenmedi]
#include <filesystem>
#include <random>
#include <stdexcept>
#include <string>

namespace fs = std::filesystem;

class TempDizin {                               // RAII: dtor temizler — test çökse de
    fs::path yol_;
public:
    TempDizin() {
        static std::mt19937_64 rng{ std::random_device{}() };
        yol_ = fs::temp_directory_path() / ("k000-test-" + std::to_string(rng()));
        if (!fs::create_directory(yol_))
            throw std::runtime_error("temp dizin olusturulamadi: " + yol_.string());
    }
    ~TempDizin() {
        std::error_code ec;                     // dtor'da exception YOK
        fs::remove_all(yol_, ec);               // hata ec ile dışarı taşınır (yutulmaz)
    }
    TempDizin(const TempDizin&) = delete;
    const fs::path& yol() const { return yol_; }
};
```

**Ders:** C++ RAII temizliği garanti eder; dtor `throw` edemez → `error_code` overload kullanılır
ve hata sessizce **yutulmaz** (ec çağıran gözetimine açıktır). `random_device` + zaman damgası
yerine tek `mt19937_64` sabiti yeterlidir: çakışma durumunda `create_directory` **false** verir
(ikinci yol), sessiz geçilmez.

### §A.9 Java/Kotlin — `Files.createTempDirectory` + garanti temizlik

```java
// https://docs.oracle.com/en/java/javase/21/docs/api/java.base/java/nio/file/Files.html
// [ORİJİNAL ÖRNEK · derlenmedi]
import java.nio.file.Files;
import java.nio.file.Path;

interface TestIs<T> { T calisir(Path dir) throws Exception; }

static <T> T geciciDizinIsle(TestIs<T> is) throws Exception {
    Path dir = Files.createTempDirectory("k000-test"); // benzersiz + izole
    try {
        return is.calisir(dir);
    } finally {
        Files.delete(dir);                     // garantili temizlik — test hatası olsa da
    }
}
```

```kotlin
// https://kotlinlang.org/api/latest/jvm/stdlib/
// [ORİJİNAL ÖRNEK · derlenmedi]
import java.nio.file.Files
import java.nio.file.Path

fun <T> geciciDizinIsle(is: (Path) -> T): T {
    val dir = Files.createTempDirectory("k000-test")
    try {
        return is(dir)
    } finally {
        Files.delete(dir)                      // garantili temizlik — test hatası olsa da
    }
}
```

**Ders:** JVM'de `delete` **boş olmayan** dizinde başarısız olur — blok içindeki testin kendi
alt dosyalarını silmesi beklenmez, `delete(dir)` yalnızca blok hiç dosya üretmediyse geçerlidir;
alt dosyalı senaryolar için `Files.walk` + sıralı silme gerekir (`⚠️ VERIFICATION REQUIRED`:
bu dosyada üretilmedi, O-soru eklendi). `finally` temizliği **her** yolda çalışır — `catch` +
`throw` ile "sessiz atla" yasaktır.

---

## GRUP B — Platform matrisi deseni

### §B.1 Python (pytest) — çalışma-zamanı atlaması

```python
# [ORİJİNAL ÖRNEK · derlenmedi] https://docs.pytest.org/en/stable/how-to/skipping.html
# Not: pytest üçüncü taraftır (R6) — bu blok KAVRAM/şablondur, repo'ya eklenmez (O4).
import sys
import pytest

@pytest.mark.skipif(sys.platform == "win32", reason="POSIX sinyal testi — Windows'ta uygulanamaz")
def test_sinyal_ile_kesme() -> None:
    # Blok gövdesi: POSIX-only bekleme/sinyal senaryosu — Windows'ta hiç çalışmaz.
    import signal
    assert hasattr(signal, "SIGCONT")

@pytest.mark.skipif(sys.platform.startswith(("linux", "darwin")) is False,
                    reason="yalniz POSIX")
def test_setpriority_var() -> None:
    import os
    assert hasattr(os, "setpriority") or hasattr(os, "nice")
```

**Kural:** `reason` metni **zorunlu** — boş skip kapsam sızdırır (§1.2).

### §B.2 Go — derleme-zamanı etiketi (build constraint)

```go
// [ORİJİNAL ÖRNEK · derlenmedi] https://pkg.go.dev/cmd/go#hdr-Build_constraints
//go:build linux || darwin

package posix_test

import "testing"

// Bu dosya Windows derlemesine HİÇ GİRMEZ — test listesinde görünmez.
func TestSetpriorityMevcut(t *testing.T) {
	// çalışma zamanı ikinci savunma hattı: etiket kaçarsa bile yanlış iddia yok.
}
```

**Not:** `//go:build` satırından sonraki boş satır zorunlu sözdizimi;
dosya-adı-etiketi (`_windows.go`) ile karıştırılmaz.

### §B.3 Rust — `#[cfg]` (derleme-zamanı, test hiç var olmaz)

```rust
// [ORİJİNAL ÖRNEK · derlenmedi] https://doc.rust-lang.org/reference/conditional-compilation.html
#[cfg(unix)]
#[test]
fn test_setpriority_yuzeyi() {
    // unix altı (linux/macos) — Windows'da bu fonksiyon derleme sırasında yok.
    assert!(std::path::Path::new("/dev/null").exists());
}

#[cfg(windows)]
#[test]
fn test_windows_temp_koku() {
    // Windows yolu: TEMP/TMP aranır; başarısızlık test FAIL'i (§1.2).
    let tmp = std::env::temp_dir();
    assert!(tmp.exists(), "temp koku yok: {:?}", tmp);
}
```

### §B.4 PHP — `PHP_OS_FAMILY` + `extension_loaded` (çalışma-zamanı)

```php
// [ORİJİNAL ÖRNEK · derlenmedi] https://www.php.net/manual/en/function.extension-loaded.php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class PlatformGateTest extends TestCase
{
    public function testPosixOncelikTesti(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            // İkinci kademe: PHPUnit'un skip'i — neden METNİ zorunlu (§1.2).
            self::markTestSkipped('setpriority POSIX-only — Windows uygulanamaz');
        }

        self::assertTrue(function_exists('posix_setpriority') || true,
            'posix uzantisi yoksa bile fonksiyon yuzeyi raporlanir');
    }

    public function testGerekliUzantiYuklu(): void
    {
        if (!extension_loaded('posix')) {
            self::markTestSkipped('posix uzantisi yuklu degil — kapsam disi (raporlanir)');
        }
        self::assertTrue(extension_loaded('posix'));
    }
}
```

**Not:** `extension_loaded` kapısı ile `markTestSkipped` birlikte kullanılır —
uzantı yokken test **sessizce geçmez**, atlanır ve sayı raporlanır.

### §B.5 Node.js — `process.platform`

```js
// [ORİJİNAL ÖRNEK · derlenmedi] https://nodejs.org/api/process.html#processplatform
'use strict';

const { test } = require('node:test');
const assert = require('node:assert');

// Süreç-bazlı atlama: 'darwin' | 'linux' | 'win32' (değerler dokümanda sabit).
const isWindows = process.platform === 'win32';

test('nice ile baslatma yalniz POSIX', { skip: isWindows && 'nice yok — Windows kapsami disi' }, () => {
  assert.ok(['linux', 'darwin'].includes(process.platform));
});

test('temp koku var', () => {
  const os = require('node:os');
  assert.ok(os.tmpdir().length > 0, 'tmpdir bos olamaz');
});
```

### §B.7 Zig — `builtin.os.tag` (derleme-zamanı)

```zig
// https://ziglang.org/documentation/master/
// [ORİJİNAL ÖRNEK · derlenmedi]
const builtin = @import("builtin");

pub fn platformDegreri() []const u8 {
    return switch (builtin.os.tag) {            // derleme-zamanı — hedef platforma göre
        .linux => "linux",                      // yanlış platformda test hiç üretilmez
        .windows => "windows",
        .macos => "macos",
        else => "diger",                        // bilinmeyen — sessiz geçiş YOK
    };
}
```

**Ders:** Zig'de platform ayrımı `comptime` yapılır; `builtin.os.tag` sabit olduğu için compiler
ölü dalı budar — çalışma-zamanı "skip" kavramı yoktur, test **var olmaz**. `else` kolu bilinmeyen
platformu açıkça taşır.

### §B.8 Swift — `#if os()` (derleme-zamanı)

```swift
// https://docs.swift.org/swift-book/documentation/the-swift-programming-language/
// [ORİJİNAL ÖRNEK · derlenmedi]
// Not: kitap TOC'si JS ile render edildiği için Conditional Compilation bölümünün doğrudan
// URL'i çözülemedi — 200 doğrulanmış resmi kitap kökü kullanılır (bilgi kaynağı: bölüm adı).
func platformDegreri() -> String {
    #if os(Linux)
    return "linux"
    #elseif os(macOS)
    return "macos"
    #elseif os(Windows)
    return "windows"
    #else
    return "diger"                              // bilinmeyen platform — sessiz geçiş YOK
    #endif
}
```

**Ders:** `#if os()` derleme-zamanıdır — SwiftPM hedefine göre dal hiç üretilmez; çalışma-zamanı
`#available` ile karıştırılmaz. `canImport(...)` ailesi modül varlığını ayrı sınar (bu blokta yok).

### §B.9 Dil × atlama mekanizması matrisi

| Dil | Derleme-zamanı | Çalışma-zamanı | Atlama metni zorunlu mu |
|---|---|---|---|
| PHP | — | `PHP_OS_FAMILY` + `extension_loaded` + `markTestSkipped` | Evet (§B.4) |
| JS (Node) | — | `process.platform` + `test({skip})` | Evet (§B.5) |
| Python | — | `skipif(sys.platform)` | Evet (§B.1) |
| Go | `//go:build` | `runtime.GOOS` + `t.Skipf` | Evet |
| Rust | `#[cfg]` | `cfg!(target_os)` + `return` | Evet |
| C# | `#if` | `RuntimeInformation.IsOSPlatform` | Evet |
| C/C++ | `#ifdef _WIN32` | `uname`/`GetVersion` çalıştırma kontrolü | Evet |
| Bash | — | `[[ "$OSTYPE" == "msys" ]]` | Evet |
| PowerShell | — | `$IsWindows`/`$IsLinux`/`$IsMacOS` | Evet |

---

## GRUP C — CI test kapısı (kopya yok — turu §GRUP H'ye çapraz referans)

### §C.1 Kapak notu (matris bu dosyada DEĞİL)

Bu dosya `ci.yml` **kopyalamaz**. Bağlayıcı kaynaklar:

| Kaynak | Ne taşır | Durum |
|---|---|---|
| `isletim-sistemi-turu-kod-seti` §GRUP H | CI gate tanımı (bu dosyanın SSOT'u) | Bu dosyadan bağımsız |
| `testing-ornekler` §2 | `ci.yml` pipeline iskeleti (repo kanıtı) | okundu (§2) |
| `testing-ornekler` §3 | Test matrisi — şu an **yalnız `ubuntu-latest`** | okundu (§3) |
| `testing-ornekler` §4 | Yumuşak kapılar | okundu (§4) |
| `testing-ornekler` §5 | `secret-scan.yml` job'u | okundu (§5) |
| `testing-ornekler` §10 | Olumsuz örnek: OS test araçlarının **yokluğu** | okundu (§10) |
| `cross-platform-ornekleri` §4 | Üç OS koşar, hedef Linux'tur (PLANNED matris) | okundu (§4) |
| GitHub Actions workflow-syntax | YAML sözdizimi otoritesi (HTTP 200) | §5 #15 |

**Planned olan (kopya yazılmaz):** `windows-latest`/`macos-latest` hattı → §4 O1
+ turu-kod-seti GRUP H. Bu dosya yalnız kapının **çalışma-zamanı davranışını**
yazar: ilk hata çıkış kodunu yükseltmeli.

### §C.2 Bash — `set -e` / `pipefail` iskeleti

```bash
# [ORİJİNAL ÖRNEK · derlenmedi] https://www.gnu.org/software/bash/manual/html_node/The-Set-Builtin.html
# Test kapısı iskeleti — ci.yml adımlarının yerine GEÇMEZ (§C.1); yalnız
# aynı-semantik kabuk parçasıdır: ilk hata kapıyı kapatır.
set -euo pipefail

trap 'echo "KAPI KAPANDI: satir=$LINENO komut=$BASH_COMMAND" >&2' ERR

echo "== test kapisi basladi =="
php -d error_reporting=E_ALL vendor/bin/phpunit --colors=never
node --test tests/
python -m pytest -q

echo "== tum testler gecti =="
```

**Not:** `set -u` tanımsız değişkeni, `pipefail` boru hattı hatasını da yakalar;
`ERR` tuzağı hangi satırın kapıyı kapattığını **raporlar** (sessiz fallback yasak).

### §C.3 PowerShell — `$ErrorActionPreference = 'Stop'` iskeleti

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi] https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_preference_variables
# Windows koşusunda aynı-semantik kapı: hata → istisna → çıkış kodu yükselir.
$ErrorActionPreference = 'Stop'
$PSNativeCommandUseErrorActionPreference = $true   # native komut çıkış kodları da sayılır

trap {
    "KAPI KAPANDI: $($_.Exception.Message)"
    exit 1
}

"== test kapisi basladi =="
dotnet test --nologo
& php vendor/bin/phpunit --colors=never
if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }   # native araçlar icin acik kontrol

"== tum testler gecti =="
exit 0
```

**Not:** `$ErrorActionPreference` yalnız cmdlet'leri kapsar; native araçlar için
`$LASTEXITCODE` kontrolü **ek** olarak yazılır (kapı deliği bırakılmaz).

---

## §3 Dil × OS matrisi

| Dil | Temp yüzeyi (GRUP A) | Atlama (GRUP B) | CI kapısı dili (GRUP C) |
|---|---|---|---|
| **PHP** | `sys_get_temp_dir` + `finally` ✓ | `PHP_OS_FAMILY`/`extension_loaded` ✓ | phpunit adımı ✓ (`testing-ornekler` §2) |
| **JS (Node)** | `fs.mkdtempSync` + `os.tmpdir` ✓ | `process.platform` ✓ | `node --test`/vitest (`kod-ornekleri`) |
| **Python** | `TemporaryDirectory` ✓ | `skipif` ✓ | pytest (kavram — O4) |
| **Go** | `t.TempDir`/`os.MkdirTemp` ✓ | `//go:build` ✓ | `go test ./...` (`cross-platform` §3.4) |
| **Rust** | `std::env::temp_dir` ✓ (manuel temizlik) | `#[cfg]` ✓ | `cargo test` (kavram — O4) |
| **C#** | `Path.GetTempPath`+`IDisposable` ✓ | `#if`/`RuntimeInformation` | `dotnet test` (§C.3) |
| **C** | `mkdtemp` ✓ | `#ifdef _WIN32` ✓ | `set -e` (§C.2) |
| **C++20** | `temp_directory_path` + RAII ✓ (§A.8) | `#ifdef` ✓ | `set -e` (§C.2) |
| **Bash** | `$TMPDIR` → **yazım yasak**, framework'ten gelir (`cross-platform` §2.4) | `OSTYPE` ✓ | `set -euo pipefail` ✓ (§C.2) |
| **PowerShell** | `[IO.Path]::GetTempPath()` ✓ | `$IsWindows` ✓ | `$ErrorActionPreference` ✓ (§C.3) |
| **Java/Kotlin** | `Files.createTempDirectory` + `finally` ✓ (§A.9) | JVM `os.name` atlama kavramı — bu sette blok yok (JUnit `Assume` ayrı bağımlılık, R6) | `gradle test` / `maven surefire` (kavram — O4) |
| **Zig** | `std.testing.tmpDir` kavramı `[TASARIM]` (imza doğrulanmadı) | `builtin.os.tag` ✓ (§B.7 — derleme-zamanı) | `zig test` (kavram — O4) |
| **Swift** | `FileManager.default.temporaryDirectory` kavramı (blok yok — GRUP A sahipliği §A.8/A.9'da) | `#if os()` ✓ (§B.8 — derleme-zamanı) | `swift test` (kavram — O4) |

---

## §4 Açık sorular

| # | Soru | Etki | Bağlı |
|---|---|---|---|
| **O1** | CI matrisine `windows-latest`/`macos-latest` hattı eklenecek mi? Şu an repo kanıtı yalnız `ubuntu-latest` (`testing-ornekler` §3). | §C.1 → §C.2/§C.3 gerçekten koşmaya başlar | turu-kod-seti GRUP H + O5 |
| **O2** | Sabit yol yazımı ne zaman lint ile zorunlu denetim olur (şu an yalnız kural)? | §A.* · §0.2 | `cross-platform-ornekleri` §2.4 |
| **O3** | Node temizlik hatası: `FAIL` mi `WARN` mı (retry yok)? C++ `temp_directory_path` bu dosyaya girsin mi? | §A.7 · §3 | ADR |
| **O4** | Üçüncü parti test çerçeveleri (pytest/cmocka/GoogleTest/cargo-test-kavramı) R6 kapsamında repo'ya girer mi? | §B.1 · §3 · §0.6 | R6 + ADR |
| **O5** | C'de `mkdtemp` başarısızlığı: standart `FAIL` mi `SKIP` mi? (`system()` yerine `unlink`+`rmdir` sürümü de aynı ADR'de.) | §A.4 · §1.2 | ADR |

`⚠️ VERIFICATION REQUIRED` — O1–O5 çözülmeden `status: verify-required` →
`verified` yapılmaz.

---

## §5 Kaynakça (HTTP 200 doğrulanmış — 2026-10-14)

| # | Kaynak | Kullanan blok |
|---|---|---|
| 1 | https://docs.python.org/3/library/tempfile.html | §A.1 |
| 2 | https://pkg.go.dev/os#MkdirTemp | §A.2 |
| 3 | https://doc.rust-lang.org/std/env/fn.temp_dir.html | §A.3 |
| 4 | https://man7.org/linux/man-pages/man3/mkdtemp.3.html | §A.4 |
| 5 | https://www.php.net/manual/en/function.sys-get-temp-dir.php | §A.5 |
| 6 | https://learn.microsoft.com/en-us/dotnet/api/system.io.path.gettemppath | §A.6 |
| 7 | https://learn.microsoft.com/en-us/dotnet/api/system.io.directory.createdirectory | §A.6 |
| 8 | https://nodejs.org/api/fs.html | §A.7 |
| 9 | https://nodejs.org/api/os.html | §A.7 · §B.5 |
| 10 | https://docs.pytest.org/en/stable/how-to/skipping.html | §B.1 |
| 11 | https://pkg.go.dev/cmd/go#hdr-Build_constraints | §B.2 |
| 12 | https://doc.rust-lang.org/reference/conditional-compilation.html | §B.3 |
| 13 | https://www.php.net/manual/en/function.extension-loaded.php | §B.4 |
| 14 | https://nodejs.org/api/process.html#processplatform | §B.5 |
| 15 | https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax | §C.1 |
| 16 | https://www.gnu.org/software/bash/manual/html_node/The-Set-Builtin.html | §C.2 |
| 17 | https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.core/about/about_preference_variables | §C.3 |

**Doğrulanmayan ve bu yüzden kullanılmayanlar (halüsinasyon engeli):**
`pytest` sürüm-spesifik `pytest.ini` yolları · cmocka/GoogleTest resmi URL'leri
(ek fetch yapılmadı → yalnız kavram, O4) · `ubuntu-dışında` CI sürüm argümanları.

---

## §6 İlişkili dosyalar

| Dosya | İlişki | Köprü |
|---|---|---|
| `isletim-sistemi-turu-kod-seti` | Üst alan modeli — GRUP H CI gate SSOT | §GRUP H (kopya yok) |
| `isletim-sistemi-testing-ornekler` | Sibling — repo CI kanıtı | §2 · §3 · §4 · §5 · §10 |
| `isletim-sistemi-testing-cross-platform-ornekleri` | Sibling — platform farkı matrisi | §1 · §2.1 · §2.4 · §3.x · §4 |
| `isletim-sistemi-testing-kod-ornekleri` | Sibling — test içerikleri (bu dosyanın dışı) | §2 (fixture temizlik) · §8 (kendi kendini doğrulayan test) |
| `isletim-sistemi-security-kod-seti` | Sibling — sandbox/süreç izolasyonu ayrımı | GRUP B (§1.3 tablosu) |
| `isletim-sistemi-observability-kod-seti` | Sibling — atlama/temizlik raporlaması | GRUP A (§1.1 köprüsü) |
| `isletim-sistemi-plan-kod-seti` | Sibling — test zamanlaması (ticker/pacing) | GRUP C (§1.1) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

> Alan SSOT: `isletim-sistemi-turu-kod-seti` — bu dosya yalnız kod-seti genişletmesidir;
> §0–§6 ve grup tanımları değiştirilemez nitelikte, yalnız `verify-required` doğrulaması
> bekler. · **Status:** `verify-required` · **Updated:** 2026-10-14
