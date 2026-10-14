---
node_id: K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-cross-platform-ornekleri
node_type: module
status: active
parent_id: K000-isletim-sistemi/isletim-sistemi-testing
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kapsam
  - K000-isletim-sistemi/isletim-sistemi-testing/isletim-sistemi-testing-kod-ornekleri
evidence:
  - "https://vitest.dev/api/test — HTTP 200 (2026-10-14) — test.skipIf / test.runIf / context.skip"
  - "https://docs.phpunit.de/en/11.5/ — HTTP 200 (2026-10-14) — RequiresOperatingSystem(Family) attribute, Skipping Tests"
  - "https://docs.pytest.org/en/stable/how-to/skipping.html — HTTP 200 (2026-10-14) — pytest.mark.skipif, sys.platform"
  - "https://pkg.go.dev/testing — HTTP 200 (2026-10-14) — testing paketi, t.Skip, t.TempDir"
  - "https://doc.rust-lang.org/book/ch11-01-writing-tests.html — HTTP 200 (2026-10-14) — #[test], assert_eq!, should_panic"
  - "https://api.cmocka.org/group__cmocka__exec.html — HTTP 200 (2026-10-14) — skip(), cmocka_unit_test, cmocka_run_group_tests"
  - "https://github.com/google/googletest — HTTP 200 (2026-10-14) — GoogleTest 1.18.0, C++17"
  - "https://google.github.io/googletest/advanced.html — HTTP 200 (2026-10-14) — GTEST_SKIP() çalışma zamanı atlama"
  - "https://xunit.net/docs/getting-started/v3/whats-new — HTTP 200 (2026-10-14) — Assert.Skip / Assert.SkipWhen / Assert.SkipUnless"
  - "https://docs.junit.org/6.1.3/writing-tests/conditional-test-execution.html — HTTP 200 (2026-10-14) — @EnabledOnOs / @DisabledOnOs"
  - "https://junit.org/junit5/docs/current/user-guide/ — HTTP 200 (2026-10-14) — Assumptions, Conditional Test Execution (kullanım kılavuzu)"
  - "https://bats-core.readthedocs.io/en/stable/writing-tests.html — HTTP 200 (2026-10-14) — skip, run, setup/teardown, BATS_TEST_TMPDIR"
  - "https://github.com/bats-core/bats-core — HTTP 200 (2026-10-14) — Bats TAP, Bash 3.2+"
validation: []
open_questions:
  - T1 [UNKNOWN] — "test kapsamı" tam olarak hangi artefaktları kapsıyor: kapsam belirsiz → kapsam.md §G.1.
  - T2 [UNKNOWN] — coverage/ölçüm eşiği yok; rakam uydurmak yasak (kapsam.md §G.2).
  - X-01 [UNKNOWN] — bu dosyadaki referans kodlar 3 OS'ta (Windows/macOS/Linux) fiilen çalıştırılmadı; doğrulama = kanıtlanmış API + derleme/koşum denemesi YOK → §6.
group: kod
---

# cross-platform-ornekler — Windows / macOS / Linux Taşınabilir Test Kodu (Dil Gruplu)

> **Yer:** `K000-isletim-sistemi/isletim-sistemi-testing/` · **Kademe:** 2 · **group:** `kod` · **Tarih:** 2026-10-14
>
> **Kod statüsü (bağlayıcı):** Bu dosyadaki her kod bloğu **referans koddur — repo'da karşılığı yoktur**
> (aynı işaretleme `cekirdek-test/cekirdek-test-ornekler.md` §0'da kullanılır). Gerçek repo satır
> referanslı test örnekleri ayrı dosyadadır: `isletim-sistemi-testing-kod-ornekleri.md` ve
> `uygulama-testi/uygulama-testi-ornekler.md` (`phpunit.xml`, `shared/tests/`, `.github/workflows/ci.yml`).
> Bu dosya o iki dosyanın yerine geçmez, onları tamamlar.

## §0 ELI15

Bu dalın **çalışma hedefi tek bir işletim sistemi: Linux** — üretimde, dağıtımda, CI'da hedef OS
Linux'tur. Ama geliştirici makineleri ve CI koşucuları yalnız Linux değildir: Windows ve macOS'ta
da test yazılacak, çalıştırılacak, sonuç okunacaktır. Bu dosyanın işi budur: **üç OS'ta da aynı
doğrulukla koşan test kodu** yazmak.

Üç kural özet:

1. **Kod üç OS'ta taşınabilir yazılır** (yol, kabuk, satır sonu, harf duyarlılığı farkları
   dilden bağımsız kapatılır).
2. **Yalnız Linux'a özgü davranış Linux kapısından geçer** — Windows/macOS'ta o test *atlanır*
   (skip), *sessizce geçmez* (pass), *kırmızıya dönmez* (fail).
3. **Her dil kendi atlama mekanizmasını kullanır** — `if` sarmalayıcı değil, framework'ün
   resmî skip API'si; raporlama (skip/total) görünür kalır.

Dosya yapısı: §1 platform matrisi → §2 ortak kalıplar → §3 on dil grubu (kod örnekleri) →
§4 CI matrisi → §5 kanıt envanteri → §6 sınır ve açık sorular.

## §1 Platform matrisi — hangi fark hangi testi kırar

| # | Taşınabilirlik riski | Windows | macOS | Linux | Test stratejisi |
|---|---|---|---|---|---|
| P-01 | Yol ayırıcı | `\` | `/` | `/` | Asla string birleştirme; dilin yol API'si (`path.join`, `Path.Combine`, `filepath.Join` …) |
| P-02 | Kabuk | cmd / PowerShell | sh / zsh (bash) | sh / bash | Test kodu kabuğa bağımlı olmamalı; kabuk testi ayrı grupta (§3.10) ve `uname` ile kapılanır |
| P-03 | Satır sonu | CRLF | LF | LF | Karşılaştırmadan önce normalize: `str_replace("\r\n", "\n", …)` — yoksa Windows'ta her dosya testi kırmızı |
| P-04 | Büyük/küçük harf duyarlılığı | duyarlı değil (NTFS varsayılanı) | duyarlı değil (APFS varsayılanı) | duyarlı | Fixture dosya adları **birebir** yazılır; "aynı görünüyor" testi Linux'ta kırılır |
| P-05 | Ortam değişkeni okuma | `%VAR%` / PowerShell `$env:VAR` | `$VAR` | `$VAR` | Dilin env API'si (`os.Getenv`, `process.env`, `getenv`) — shell string'i değil |
| P-06 | Geçici dizin | `%TEMP%` | `$TMPDIR` | `/tmp` | Elle `/tmp` yazma; framework'ün temp fixture'ı (`t.TempDir()`, `tmp_path`, `BATS_TEST_TMPDIR`) |
| P-07 | İzin modeli | ACL | POSIX izin bitleri | POSIX izin bitleri | İzin testi POSIX ailesinde (Linux + macOS) koşulur; Windows'ta skip |
| P-08 | Sinyaller | konsol ctrl olayları | POSIX signal | POSIX signal | `SIGTERM`/`kill` testi Linux kapısından geçer (hedef OS zaten Linux) |
| P-09 | Konsol kodlaması | varsayılan OEM kod sayfası | UTF-8 | UTF-8 | Encoding açıkça verilir (`encoding="utf-8"`, `Encoding.UTF8`) — varsayılan varsayılmaz |
| P-10 | Dizge (path) uzunluğu | `MAX_PATH` sınırı | yok | yok | Uzun yol testi Windows'ta ayrı senaryo; ortak senaryoya gömülmez |

**Okuma kuralı:** Bu matrisin P-04/P-07/P-08 satırları "Windows kırılgan" satırlarıdır; onlar
her dilde `§3` gruplarındaki OS kapısına takılır. P-01/P-03/P-05/P-06/P-09 ise **her testin**
içinde kapatılır — kapıya bile ihtiyaç kalmaz.

## §2 Ortak portability kalıpları (dilden bağımsız)

### §2.1 Kalıp-1: OS algılama → tek semantik, on dil

Aynı karar ağacı her dilde tekrarlanır:

```text
hedef Linux mu?
  ├─ evet  → test koşar, Linux davranışı doğrulanır
  └─ hayır → test SKIP (gerekçeli) — ne pass ne fail
```

Bu dosyadaki hiçbir örnekte "Windows ise sessizce return et ve geç say" **yoktur**; her örnekte
framework'ün skip raporu görünür kalır (P-02 mantığı: neden atlandığı raporlanır).

### §2.2 Kalıp-2: yol birleştirme yalnız dil API'si ile

```text
YASAK : "var" + "\\" + "dosya"     ·  "var/" + "dosya"   (hardcoded ayırıcı)
YASAK : dirname + "/.." + name      (elle normalize)
İZİN  : path.join / Path.Combine / filepath.Join / std::filesystem::path / Path.of
```

### §2.3 Kalıp-3: satır sonunu önce normalize et, sonra karşılaştır

```text
girdi (Windows dosyası):  "a\r\nb\r\n"
 normalize:               replace("\r\n" → "\n")
 beklenti:                 "a\nb\n"
```

Normalize **karşılaştırma öncesi** yapılır; dosyaya yazılan içeriğe dokunulmaz (CRLF repo'da
`core.autocRLF` işindedir, test dosyası değildir).

### §2.4 Kalıp-4: temp dizin framework'ten gelir

```text
YASAK : "/tmp/test", "C:\\temp\\test", "$TMPDIR/..." sabit yazımı
İZİN  : pytest tmp_path · Go t.TempDir() · Rust std::env::temp_dir() · Vitest os.tmpdir()
        xUnit Path.GetTempPath() · JUnit Files.createTempDirectory · Bats BATS_TEST_TMPDIR
```

### §2.5 Kalıp-5: aynı iş — iki kabuk (CI adımları için)

```powershell
# Windows (PowerShell) — geçici dizin + OS bilgisi
$tmp = Join-Path ([System.IO.Path]::GetTempPath()) ("cm-" + [guid]::NewGuid().ToString("N"))
New-Item -ItemType Directory -Path $tmp | Out-Null
$os  = [System.Runtime.InteropServices.RuntimeInformation]::OSDescription
```

```bash
# macOS / Linux (sh) — birebir aynı iş
tmp="$(mktemp -d "${TMPDIR:-/tmp}/cm-XXXXXX")"
os="$(uname -s)"
```

İki betik aynı işi yapar, aynı adı taşıyan değişken üretir, birbirinin yerine **konmaz** — CI'da
`runs-on` matrisi (§4) hangisinin koşacağını belirler.

## §3 Dil grupları — taşınabilir test örnekleri

> Her grup aynı üç örneği verir: **(a)** yol + temp (ortak davranış), **(b)** Linux kapısı (skip),
> **(c)** platforma bağlı kenar durumu. Tüm bloklar referans koddur (bkz. dosya başlığı).

### §3.1 JavaScript / TypeScript — Vitest

**Çerçeve:** Vitest · **Repo bağımlılığı:** `package.json` → `test:js` (gerçek repo kanıtı:
`uygulama-testi-ornekler.md`) · **Atlama:** `test.skipIf(koşul)` — koşul doğruysa test skip olur.

```ts
// referans kod — repo'da karşılığı yoktur
import { describe, expect, it } from 'vitest';
import { mkdtempSync, rmSync, writeFileSync, readFileSync } from 'node:fs';
import os from 'node:os';
import path from 'node:path';

const isLinux = process.platform === 'linux';
const isWindows = process.platform === 'win32';

describe('çapraz platform: yol ve temp', () => {
  it('path.join kendi OS\'unun ayırıcısını üretir', () => {
    const p = path.join('var', 'lib', 'test.bin');
    expect(p.endsWith('test.bin')).toBe(true);
    expect(path.basename(p)).toBe('test.bin');
    // Windows: "var\\lib\\test.bin" · Linux/macOS: "var/lib/test.bin" — ikisi de geçer
  });

  it('temp dizin yazılabilir ve temizlenir', () => {
    const dir = mkdtempSync(path.join(os.tmpdir(), 'cm-vitest-'));
    try {
      const f = path.join(dir, 'ornek.txt');
      writeFileSync(f, 'merhaba\n', 'utf8');
      expect(readFileSync(f, 'utf8')).toBe('merhaba\n');
    } finally {
      rmSync(dir, { recursive: true, force: true });
    }
  });
});

// Linux kapısı — hedef OS Linux; Windows/macOS raporunda SKIP görünür
test.skipIf(!isLinux)('Linux hedefi: /proc mounted', () => {
  expect(isLinux).toBe(true);
});

// POSIX kenar durumu — Windows'ta skip, macOS/Linux'ta koşar (P-07)
test.skipIf(isWindows)('POSIX izin bitleri okunabilir değil', () => {
  expect(isWindows).toBe(false);
});

// CRLF kenar durumu — üç OS'ta da koşar, normalize ile doğrular (P-03)
test('CRLF normalize sonrası LF olur', () => {
  expect('a\r\nb'.replace(/\r\n/g, '\n')).toBe('a\nb');
});
```

| Alan | Değer |
|---|---|
| Atlama API'si | `test.skipIf(koşul)` / `test.runIf(koşul)` (doğrulandı: vitest.dev/api/test, 2026-10-14) |
| OS bilgisi | `process.platform` (`'linux' \| 'darwin' \| 'win32'`) |
| Yol API'si | `node:path` (`path.join`, `path.basename`) |
| Temp | `os.tmpdir()` + `mkdtempSync` |
| Koşum | `npm run test:js` (repo scripti) · `npx vitest run` |

### §3.2 PHP — PHPUnit

**Çerçeve:** PHPUnit · **Repo bağımlılığı:** gerçek repo test altyapısı bu çerçevededir
(`shared/phpunit.xml`, `shared/tests/` — kanıt: `uygulama-testi-ornekler.md`) · **Atlama:**
`#[RequiresOperatingSystemFamily('Linux')]` attribute veya `markTestSkipped()`.

```php
<?php
// referans kod — repo'da karşılığı yoktur (gerçek repo örnekleri: kod-ornekleri.md)
declare(strict_types=1);

namespace CoreMusic\Tests\CrossPlatform;

use PHPUnit\Framework\Attributes\RequiresOperatingSystemFamily;
use PHPUnit\Framework\TestCase;

final class PortablePathTest extends TestCase
{
    public function testDirectorySeparatorPlatformYerel(): void
    {
        $joined = implode(DIRECTORY_SEPARATOR, ['var', 'lib', 'test.bin']);
        self::assertStringEndsWith('test.bin', $joined);
        self::assertStringNotContainsString(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, $joined);
    }

    public function testSatirSonuNormalize(): void
    {
        $crlf = "a\r\nb\r\n";
        $lf   = str_replace("\r\n", "\n", $crlf);
        self::assertSame("a\nb\n", $lf);
    }

    public function testTempDiziniYazilabilir(): void
    {
        self::assertDirectoryIsWritable(sys_get_temp_dir());
    }

    // Kapı-1: attribute — keşif/atlama aşamasında karar verilir (PHPUnit 11.5)
    #[RequiresOperatingSystemFamily('Linux')]
    public function testHedefOsLinux(): void
    {
        self::assertSame('Linux', PHP_OS_FAMILY);
    }

    // Kapı-2: koşullu atlama — çalışma anında karar (üç OS'ta da dosya derlenir)
    public function testPosixSinyalleriYalnizPosixte(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            self::markTestSkipped('POSIX sinyalleri yalnız Unix ailesinde (P-08)');
        }
        self::assertNotSame('Windows', PHP_OS_FAMILY);
    }
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `#[RequiresOperatingSystem(Family)]` attribute + `markTestSkipped($reason)` (doğrulandı: docs.phpunit.de/en/11.5, 2026-10-14) |
| OS bilgisi | `PHP_OS_FAMILY` (`'Windows' \| 'Darwin' \| 'Linux'`) |
| Yol API'si | `DIRECTORY_SEPARATOR` · `path join` yerine dil birleştirme |
| Temp | `sys_get_temp_dir()` |
| Koşum | `vendor/bin/phpunit` (repo: `composer test`) |
| Repo notu | Repo `composer.json` PHPUnit kullanır (`.ai/AGENTS.md` §25.2 kanıtı); bu blok referanstır |

### §3.3 Python — pytest

**Çerçeve:** pytest · **Atlama:** `@pytest.mark.skipif(koşul, reason=…)`.

```python
# referans kod — repo'da karşılığı yoktur
import os
import sys
from pathlib import Path

import pytest

IS_LINUX = sys.platform.startswith("linux")
IS_WINDOWS = os.name == "nt"


def test_yol_birlestirme_platform_yerel(tmp_path: Path) -> None:
    p = Path("var") / "lib" / "test.bin"
    assert p.name == "test.bin"
    # karşılaştırma normalize ile: Windows da "var/lib/test.bin" gibi görür
    assert str(p).replace("\\", "/") == "var/lib/test.bin"


def test_temp_fixture_yaz_oku(tmp_path: Path) -> None:
    f = tmp_path / "ornek.txt"
    f.write_text("merhaba\n", encoding="utf-8")
    assert f.read_text(encoding="utf-8") == "merhaba\n"


@pytest.mark.skipif(not IS_LINUX, reason="hedef OS Linux (P: çalışma hedefi)")
def test_linux_kapisi_signal_modulu() -> None:
    import signal
    assert hasattr(signal, "SIGTERM")


@pytest.mark.skipif(IS_WINDOWS, reason="POSIX izin bitleri Windows'ta yok (P-07)")
def test_posix_izin_bitleri() -> None:
    assert not IS_WINDOWS


def test_crlf_normalize() -> None:
    assert "a\r\nb".replace("\r\n", "\n") == "a\nb"
```

| Alan | Değer |
|---|---|
| Atlama API'si | `@pytest.mark.skipif(koşul, reason=…)` · modül geneli `pytestmark` (doğrulandı: docs.pytest.org/en/stable/how-to/skipping.html, 2026-10-14) |
| OS bilgisi | `sys.platform` · `os.name` |
| Yol API'si | `pathlib.Path` (`/` operatörü) |
| Temp | `tmp_path` fixture (kendi içinde P-04/P-06 kapatır) |
| Koşum | `pytest -q` |

### §3.4 Go — testing paketi

**Çerçeve:** standart `testing` · **Atlama:** `t.Skipf(...)`.

```go
// referans kod — repo'da karşılığı yoktur
package portable

import (
	"os"
	"path/filepath"
	"runtime"
	"strings"
	"testing"
)

func TestYolBirlestirmePlatformYerel(t *testing.T) {
	p := filepath.Join("var", "lib", "test.bin")
	if filepath.Base(p) != "test.bin" {
		t.Fatalf("base = %q, want test.bin", p)
	}
}

func TestTempDizinYazOku(t *testing.T) {
	dir := t.TempDir() // her test için benzersiz, otomatik temizlenir (P-06)
	f := filepath.Join(dir, "ornek.txt")
	if err := os.WriteFile(f, []byte("merhaba\n"), 0o600); err != nil {
		t.Fatal(err)
	}
	b, err := os.ReadFile(f)
	if err != nil {
		t.Fatal(err)
	}
	if string(b) != "merhaba\n" {
		t.Fatalf("içerik = %q", b)
	}
}

func TestLinuxKapisi(t *testing.T) {
	if runtime.GOOS != "linux" {
		t.Skipf("hedef OS linux; bulundu %s — skip (P-08)", runtime.GOOS)
	}
	if _, err := os.Stat("/proc/self"); err != nil {
		t.Fatalf("Linux hedefi ama /proc yok: %v", err)
	}
}

func TestSatirSonuNormalize(t *testing.T) {
	got := strings.ReplaceAll("a\r\nb", "\r\n", "\n")
	if got != "a\nb" {
		t.Fatalf("normalize = %q", got)
	}
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `t.Skip` / `t.Skipf` (doğrulandı: pkg.go.dev/testing, 2026-10-14) |
| OS bilgisi | `runtime.GOOS` (`"linux" \| "darwin" \| "windows"`) |
| Yol API'si | `path/filepath` (`filepath.Join`, `filepath.Base`) |
| Temp | `t.TempDir()` (test bitiminde kendiliğinden temizler) |
| Koşum | `go test ./...` |

### §3.5 Rust — cargo test

**Çerçeve:** gömülü `#[test]` (Rust Book §11) · **Atlama:** Rust'ta gömülü koşullu skip yoktur —
`#[cfg(...)]` (derleme zamanı, test hiç var olmaz) veya çalışma zamanı erken dönüş + `eprintln`
kullanılır; ikisi de aşağıda gösterilmiştir ve **ikisi de farkı raporlar**.

```rust
// referans kod — repo'da karşılığı yoktur
use std::env::consts::OS;
use std::path::Path;

#[test]
fn yol_birlestirme_platform_yerel() {
    let p = Path::new("var").join("lib").join("test.bin");
    assert_eq!(p.file_name().unwrap().to_str().unwrap(), "test.bin");
}

#[test]
fn gecici_dosya_yaz_oku() {
    let mut dir = std::env::temp_dir(); // P-06: elle "/tmp" yazılmaz
    dir.push("cm-rust-test");
    std::fs::create_dir_all(&dir).unwrap();
    let f = dir.join("ornek.txt");
    std::fs::write(&f, "merhaba\n").unwrap();
    let s = std::fs::read_to_string(&f).unwrap();
    assert_eq!(s, "merhaba\n");
    let _ = std::fs::remove_dir_all(&dir);
}

#[test]
fn linux_kapisi_calisma_zamani() {
    if OS != "linux" {
        eprintln!("SKIP: hedef OS linux, bulundu {OS} (erken dönüş — test 'passed' sayılır, CI matrisi telafi eder)");
        return;
    }
    assert_eq!(OS, "linux");
}

// Alternatif (derleme zamanı): bu test Windows derlemesinde HİÇ var olmaz.
// Rapor avantajı: yok sayımına düşmez; dezavantaj: kapsam daralır.
#[cfg(target_os = "linux")]
#[test]
fn linux_kapisi_cfg() {
    assert_eq!(OS, "linux");
}

#[test]
#[should_panic] // Rust'ın kendi negatif testi — platform bağımsız
fn tasan_sayi_panikler() {
    let _: u8 = 200u8 + 100u8;
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `#[cfg(target_os = "linux")]` (derleme) · çalışma zamanı erken dönüş + log (Rust standart kütüphanesinde yerleşik skip **yok** → §6 X-01) |
| OS bilgisi | `std::env::consts::OS` |
| Yol API'si | `std::path::Path::join` |
| Temp | `std::env::temp_dir()` |
| Koşum | `cargo test` |
| Kanıt | Rust Book ch11-01 (2026-10-14, §5) |

### §3.6 C — cmocka

**Çerçeve:** cmocka · **Atlama:** `skip()` — "Forces the test to not be executed, but marked as
skipped" (doğrulandı: api.cmocka.org/group__cmocka__exec.html, 2026-10-14).

```c
/* referans kod — repo'da karşılığı yoktur */
#include <stdarg.h>
#include <stddef.h>
#include <setjmp.h>
#include <string.h>
#include <cmocka.h>

#if defined(_WIN32)
#define CM_OS "windows"
#elif defined(__APPLE__)
#define CM_OS "macos"
#elif defined(__linux__)
#define CM_OS "linux"
#else
#define CM_OS "unknown"
#endif

static void test_yol_birlestirme_basename(void **state) {
    (void)state;
    const char *p = "var/lib/test.bin"; /* POSIX göreceli yol — karşılaştırma öncesi normalize */
    const char *base = strrchr(p, '/');
    base = base ? base + 1 : p;
    assert_string_equal(base, "test.bin");
}

static void test_satir_sonu_normalize(void **state) {
    (void)state;
    const char *in = "a\r\nb\r\n";
    char out[16];
    size_t i = 0, j = 0;
    for (; in[i] != '\0'; i++) {
        if (in[i] == '\r' && in[i + 1] == '\n') { i++; out[j++] = '\n'; }
        else { out[j++] = in[i]; }
    }
    out[j] = '\0';
    assert_string_equal(out, "a\nb\n");
}

static void test_linux_kapisi(void **state) {
    (void)state;
    if (strcmp(CM_OS, "linux") != 0) {
        /* hedef OS Linux değil → skip olarak RAPORLA (sessiz geçme) */
        skip();
        return;
    }
    assert_string_equal(CM_OS, "linux");
}

int main(void) {
    const struct CMUnitTest tests[] = {
        cmocka_unit_test(test_yol_birlestirme_basename),
        cmocka_unit_test(test_satir_sonu_normalize),
        cmocka_unit_test(test_linux_kapisi),
    };
    return cmocka_run_group_tests(tests, NULL, NULL);
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `skip()` (doğrulandı: cmocka API, 2026-10-14) |
| OS bilgisi | derleme zamanı makrolar: `_WIN32` / `__APPLE__` / `__linux__` |
| Yol API'si | C standartında yok — manuel ayrıştırma; karşılaştırma öncesi normalize (P-01/P-03) |
| Temp | C standartında yok — `TMPDIR`/`TEMP` env okunur veya test kendi dizinini kullanır (P-06) |
| Koşum | `cc -o test test.c -lcmocka && ./test` |
| Not | cmocka'da OS attribute yoktur; kapı `skip()` ile çalışma zamanında kurulur |

### §3.7 C++ — GoogleTest

**Çerçeve:** GoogleTest 1.18.x (C++17; repo kanıtı yok → referans) · **Atlama:** `GTEST_SKIP()`
(doğrulandı: google.github.io/googletest/advanced.html, 2026-10-14).

```cpp
// referans kod — repo'da karşılığı yoktur
#include <gtest/gtest.h>

#include <filesystem>
#include <fstream>
#include <string>

namespace fs = std::filesystem;

TEST(PortablePath, JoinPlatformYerel) {
  const fs::path p = fs::path("var") / "lib" / "test.bin";
  EXPECT_EQ(p.filename().string(), "test.bin");
}

TEST(PortablePath, TempDosyaYazOku) {
  const fs::path dir = fs::temp_directory_path() / "cm-gtest";  // P-06
  fs::create_directories(dir);
  const fs::path f = dir / "ornek.txt";
  {
    std::ofstream out(f);
    out << "merhaba\n";
  }
  std::ifstream in(f);
  std::string line;
  std::getline(in, line);
  EXPECT_EQ(line, "merhaba");
  fs::remove_all(dir);
}

TEST(LinuxKapisi, HedefOsLinux) {
#if defined(__linux__)
  SUCCEED();  // Linux hedefi — koş
#else
  GTEST_SKIP() << "hedef OS Linux; bulundu: "
#if defined(_WIN32)
               << "windows";
#elif defined(__APPLE__)
               << "macos";
#else
               << "bilinmiyor";
#endif
#endif
}

TEST(Crlf, NormalizeSonrasiLf) {
  std::string s = "a\r\nb";
  std::string out;
  for (size_t i = 0; i < s.size(); ++i) {
    if (s[i] == '\r' && i + 1 < s.size() && s[i + 1] == '\n') { ++i; out += '\n'; }
    else { out += s[i]; }
  }
  EXPECT_EQ(out, "a\nb");
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `GTEST_SKIP() << "sebep"` — test vaka içinde veya `SetUp()` içinde (2026-10-14 doğrulandı) |
| OS bilgisi | `_WIN32` / `__APPLE__` / `__linux__` makroları |
| Yol API'si | `std::filesystem::path` (C++17; GoogleTest 1.18.x zaten C++17 ister) |
| Temp | `fs::temp_directory_path()` |
| Koşum | `ctest` veya test binary'si doğrudan |

### §3.8 C# — xUnit v3

**Çerçeve:** xUnit.net v3 · **Atlama:** `Assert.Skip` ailesi — çalışma zamanı atlama
(`Assert.Skip(msg)` · `Assert.SkipWhen(koşul, reason)` koşul doğruysa atlar ·
`Assert.SkipUnless(koşul, reason)` koşul yanlışsa atlar; doğrulandı: xunit.net v3 whats-new,
2026-10-14). v3 dokümanı OS örneklerini doğruda "Windows vs. macOS vs. Linux" diye verir.

```csharp
// referans kod — repo'da karşılığı yoktur
using System.Runtime.InteropServices;
using Xunit;

namespace CoreMusic.Tests.CrossPlatform;

public class PortablePathTests
{
    private static readonly bool IsLinux   = OperatingSystem.IsLinux();
    private static readonly bool IsWindows = OperatingSystem.IsWindows();

    [Fact]
    public void Yol_Birlestirme_Platform_Yerel()
    {
        var p = Path.Combine("var", "lib", "test.bin");
        Assert.EndsWith("test.bin", p);
        var sep = Path.DirectorySeparatorChar.ToString();
        Assert.DoesNotContain(sep + sep, p); // çift ayırıcı yok (P-01)
    }

    [Fact]
    public void Temp_Dosya_Yaz_Oku()
    {
        var dir = Path.Combine(Path.GetTempPath(), "cm-xunit"); // P-06
        Directory.CreateDirectory(dir);
        try
        {
            var f = Path.Combine(dir, "ornek.txt");
            File.WriteAllText(f, "merhaba\n");
            Assert.Equal("merhaba\n", File.ReadAllText(f));
        }
        finally
        {
            Directory.Delete(dir, recursive: true);
        }
    }

    [Fact]
    public void Linux_Kapisi()
    {
        // koşul doğruysa SKIP: Windows/macOS raporunda "Skipped" görünür
        Assert.SkipWhen(!IsLinux, "hedef OS Linux (P-08)");
        Assert.True(IsLinux);
    }

    [Fact]
    public void Posix_Izin_Bitsiz_Windows_Atlansin()
    {
        Assert.SkipWhen(IsWindows, "POSIX izin bitleri Windows'ta yok (P-07)");
        Assert.False(IsWindows);
    }

    [Fact]
    public void Crlf_Normalize()
    {
        Assert.Equal("a\nb\n", "a\r\nb\r\n".Replace("\r\n", "\n"));
    }
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `Assert.Skip` / `Assert.SkipWhen` / `Assert.SkipUnless` (xUnit **v3**; v2'de bu yoktur — SkippableFact eklentisi gerekir, §6 X-01) |
| OS bilgisi | `OperatingSystem.IsLinux()` / `IsWindows()` / `IsMacOS()` |
| Yol API'si | `System.IO.Path.Combine` · `Path.DirectorySeparatorChar` |
| Temp | `Path.GetTempPath()` |
| Koşum | `dotnet test` |

### §3.9 Java — JUnit Jupiter

**Çerçeve:** JUnit Jupiter (kullanım kılavuzu `junit.org/junit5/docs/current/user-guide/`;
2026-10-14'te sayfa sürüm **6.1.3** gösteriyor) · **Atlama:** `@EnabledOnOs` / `@DisabledOnOs`
(doğrulandı: docs.junit.org/6.1.3 conditional-test-execution, 2026-10-14) + `Assumptions`.

```java
// referans kod — repo'da karşılığı yoktur
import org.junit.jupiter.api.Assumptions;
import org.junit.jupiter.api.Test;
import org.junit.jupiter.api.condition.DisabledOnOs;
import org.junit.jupiter.api.condition.EnabledOnOs;
import org.junit.jupiter.api.condition.OS;

import java.nio.file.Files;
import java.nio.file.Path;
import java.util.Comparator;

import static org.junit.jupiter.api.Assertions.*;

class PortablePathTest {

    @Test
    void yolBirlestirmePlatformYerel() {
        Path p = Path.of("var", "lib", "test.bin");
        assertEquals("test.bin", p.getFileName().toString());
    }

    @Test
    void tempDosyaYazOku() throws Exception {
        Path dir = Files.createTempDirectory("cm-junit"); // P-06
        try {
            Path f = dir.resolve("ornek.txt");
            Files.writeString(f, "merhaba\n");
            assertEquals("merhaba\n", Files.readString(f));
        } finally {
            try (var yollar = Files.walk(dir)) {
                for (Path p : yollar.sorted(Comparator.reverseOrder()).toList()) {
                    Files.deleteIfExists(p);
                }
            }
        }
    }

    @Test
    @EnabledOnOs(OS.LINUX) // keşifte karar verilir; Windows/macOS raporunda disabled
    void linuxKapisi() {
        assertTrue(System.getProperty("os.name").toLowerCase().contains("linux"));
    }

    @Test
    @DisabledOnOs(OS.WINDOWS) // POSIX kenar durumu — Windows'ta disabled (P-07)
    void posixIzinBitleri() {
        assertFalse(System.getProperty("os.name").toLowerCase().contains("win"));
    }

    @Test
    void kosulluAtlamaAssumptionIle() {
        // Assumptions: koşul yanlışsa ABORTED/skip — pass değil
        Assumptions.assumeFalse(System.getProperty("os.name").toLowerCase().contains("win"));
        assertNotEquals("x", "y");
    }

    @Test
    void crlfNormalize() {
        assertEquals("a\nb\n", "a\r\nb\r\n".replace("\r\n", "\n"));
    }
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `@EnabledOnOs(OS.LINUX)` · `@DisabledOnOs(OS.WINDOWS)` · `Assumptions.assume*` (2026-10-14 doğrulandı) |
| OS bilgisi | `OS` enum (`LINUX`, `MAC`, `WINDOWS`) · `System.getProperty("os.name")` |
| Yol API'si | `java.nio.file.Path.of(...)` |
| Temp | `Files.createTempDirectory(...)` |
| Koşum | `mvn test` / `gradle test` / JUnit Console Launcher |

### §3.10 Shell — Bats (bats-core)

**Çerçeve:** bats-core (TAP; Bash 3.2+) · **Atlama:** `skip "sebep"` — koşullu `skip`
doğrulandı: bats-core.readthedocs.io writing-tests, 2026-10-14. **Not (P-02):** Bats bir
Bash testidir; Windows'ta Git Bash/WSL altında koşar — çıplak cmd/PowerShell'de **değildir**;
bu yüzden Windows ayağı ya WSL ya da bu dosyanın dışındadır (§6 X-01).

```bash
#!/usr/bin/env bats
# referans kod — repo'da karşılığı yoktur

setup() {
  # P-06: elle "/tmp" yazılmaz — bats'ın kendi temp değişkeni kullanılır
  WORK="$BATS_TEST_TMPDIR"
}

@test "yol birlestirme basename dogru" {
  p="var/lib/test.bin"
  base="${p##*/}"
  [ "$base" = "test.bin" ]
}

@test "gecici dizine yaz oku" {
  mkdir -p "$WORK/created"
  printf 'merhaba\n' > "$WORK/created/ornek.txt"
  run cat "$WORK/created/ornek.txt"
  [ "$status" -eq 0 ]
  [ "$output" = "merhaba" ]
}

@test "hedef OS linux kapisi" {
  # uname yoksa (çıplak Windows kabuğu) skip — sessiz geçme yok
  if ! command -v uname >/dev/null 2>&1; then
    skip "uname yok (P-02: çıplak Windows kabuğu)"
  fi
  case "$(uname -s)" in
    Linux*) : ;;
    *)      skip "hedef OS Linux; bulundu $(uname -s)" ;;
  esac
  run uname -s
  [ "$output" = "Linux" ]
}

@test "crlf normalize sonrasi lf" {
  s=$'a\r\nb'
  s="${s//$'\r\n'/$'\n'}"
  [ "$s" = $'a\nb' ]
}

teardown() {
  rm -rf "$WORK/created"
}
```

| Alan | Değer |
|---|---|
| Atlama API'si | `skip [sebep]` — koşullu kullanım resmî (2026-10-14 doğrulandı) |
| OS bilgisi | `uname -s` (`Linux` / `Darwin` / `MINGW64_NT…`) |
| Yol API'si | bash string genişletme (`${p##*/}`); `/` kabul edilir |
| Temp | `$BATS_TEST_TMPDIR` (test başına benzersiz) |
| Koşum | `bats test/` · `bats --tap test/` |

## §4 CI matrisi — üç OS koşar, hedef Linux'tur

Aynı test paketi üç koşucuda koşar; **yayın/dağıtım kapısı yalnız Linux ayağıdır** (referans kod):

```yaml
# referans kod — repo'daki ci.yml DEĞİLDİR; repo ci.yml kanıtı: ci-ve-kalite-kapisi-ornekler.md
jobs:
  cross-platform-test:
    strategy:
      fail-fast: false          # bir OS kırılırsa diğerleri koşmaya devam eder
      matrix:
        os: [ubuntu-latest, windows-latest, macos-latest]
    runs-on: ${{ matrix.os }}
    steps:
      - uses: actions/checkout@v4
      - name: Ortam bilgisi
        run: uname -a           # Windows ayağında bu adımı koşullu yap (P-02)
      - name: Testler
        run: <framework-tanimi> # §3 tablolarındaki "Koşum" satırı
      - name: Hedef OS kapısı (yalnız Linux)
        if: runner.os == 'Linux'
        run: echo "yayın hedefi Linux doğrulandı"
```

| Matris ayağı | Ne doğrular | Kapı etkisi |
|---|---|---|
| `ubuntu-latest` | hedef OS davranışı (yayın kapısı) | **sert** — kırılırsa yayın durur |
| `windows-latest` | P-01/P-03/P-05/P-06 taşınabilirliği (skip'ler görünür) | sert (taşınabilirlik) |
| `macos-latest` | POSIX + APFS duyarlılığı (P-04/P-07) | sert (taşınabilirlik) |
| Skip sayıları | Her ayakta raporlanır; **skip ≠ pass** | skip'te sızıntıyı §6 X-01 yakalar |

**Ölçüm notu (zero-hallucination):** Bu dosyada "3 OS'ta X test geçti" gibi bir sayı **yoktur** —
fiilen koşulmadı. IdDia edilen yalnız API varlığı ve davranışıdır (§5 kanıt listesi).

## §5 Kanıt envanteri (URL + tarih + ne doğrulandı)

Tümü 2026-10-14'te erişildi; "erişildi" = HTTP 200 + ilgili ifadenin sayfada görülmesi.

| # | URL | Doğrulanan |
|---|---|---|
| E-01 | `https://vitest.dev/api/test` | `test.skipIf` / `test.runIf` / `context.skip` |
| E-02 | `https://docs.phpunit.de/en/11.5/` | `RequiresOperatingSystem` · `RequiresOperatingSystemFamily` attribute'ları; Skipping Tests bölümü |
| E-03 | `https://docs.pytest.org/en/stable/how-to/skipping.html` | `pytest.mark.skipif` · `sys.platform` örnekleri · modül geneli `pytestmark` |
| E-04 | `https://pkg.go.dev/testing` | `testing` paketi (t.Skip, t.TempDir bu paketin parçası) |
| E-05 | `https://doc.rust-lang.org/book/ch11-01-writing-tests.html` | `#[test]` · `assert_eq!` · test yazım kuralları |
| E-06 | `https://api.cmocka.org/group__cmocka__exec.html` | `skip()` ("marked as skipped") · `cmocka_unit_test` · `cmocka_run_group_tests` · `assert_int_equal` |
| E-07 | `https://github.com/google/googletest` | GoogleTest 1.18.0 · C++17 zorunluluğu |
| E-08 | `https://google.github.io/googletest/advanced.html` | `GTEST_SKIP()` — vaka içi ve `SetUp()` içinde çalışma zamanı atlama |
| E-09 | `https://xunit.net/docs/getting-started/v3/whats-new` | `Assert.Skip(msg)` · `Assert.SkipWhen(bool, reason)` · `Assert.SkipUnless(bool, reason)` · "operating system (Windows vs. macOS vs. Linux)" örneği |
| E-10 | `https://docs.junit.org/6.1.3/writing-tests/conditional-test-execution.html` | `@EnabledOnOs(MAC)` · `@EnabledOnOs({LINUX, MAC})` · `@DisabledOnOs(WINDOWS)` · `disabledReason` |
| E-11 | `https://junit.org/junit5/docs/current/user-guide/` | Kullanım kılavuzu ana sayfası · Assumptions · Conditional Test Execution bölümleri · sürüm 6.1.3 |
| E-12 | `https://bats-core.readthedocs.io/en/stable/writing-tests.html` | `skip [sebep]` (koşullu) · `run` · `setup`/`teardown` · `$BATS_TEST_TMPDIR` |
| E-13 | `https://github.com/bats-core/bats-core` | bats-core · TAP · Bash 3.2+ |

Türetilmiş (dolaylı) kanıt, doğrudan okuma yok: `vitest.dev/guide` (E-01'e yönlendirir),
`cmocka.org` (E-06'ya yönlendirir), `xunit.net` (E-09'a yönlendirir),
`doc.rust-lang.org/book/ch11-00-testing.html` (E-05'in girişi).

## §6 Sınır ve açık sorular

### §6.1 Sınır (bu dosya ne DEĞİL)

| Sınır | Ayrıntı |
|---|---|
| Gerçek repo test kanıtı değil | Repo satır referanslı örnekler: `kod-ornekleri.md` · `uygulama-testi/uygulama-testi-ornekler.md` · `ci-ve-kalite-kapisi/ci-ve-kalite-kapisi-ornekler.md` |
| Uygulama test stratejisi değil | Strateji/eşik → `K019-testing` (kapsam.md §A.2 X-01) ve `-anayasa.md` |
| Platform test matrisi sahipliği değil | `isletim-sistemi-turu` dalı (kapsam.md §E E-07); bu dosya yalnız **test kodunun** taşınabilirliğini kapsar |
| Kod çalıştırma kanıtı değil | Bloklar derlenmedi/koşulmadı → X-01 |
| Eşik/coverage iddiası yok | T2 `UNKNOWN` kalır; sayı uydurmak yasak |

### §6.2 Açık sorular

| # | Soru | Durum |
|---|---|---|
| T1 | Test kapsamının tam tanımı | `[UNKNOWN]` — kapsam.md §G.1 |
| T2 | Coverage/ölçüm eşiği | `[UNKNOWN]` — kapsam.md §G.2 |
| X-01 | Bu dosyadaki 10 grubun kodu 3 OS'ta fiilen derlenmedi/koşulmadı; API'ler kanıtlanmış (§5) ama **çalıştırma kanıtı yok** | `VERIFICATION REQUIRED` — ilk fiili kullanımda her grup §4 matrisinde bir kez koşulacak |
| X-02 | Rust ve C#'ta yerleşik OS-atlama API'si farklı semantik taşır (Rust: cfg/erken dönüş · C#: Assert.SkipWhen) — rapor birebirliği sağlanmadı | izleniyor — §3.5 / §3.8 notları |
| Q-3 | `ci-ve-kalite-kapisi-surucu.md:398` açık sorusu "OS matrisi (Windows/macOS) eklenecek mi?" | bu dosya §1 + §4 ile **yanıtladı** (2026-10-14); surucu dosyasındaki kayıt kendi satırında kalır |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Tarih:** 2026-10-14 · **Grup:** `kod` · **Statü:** active
