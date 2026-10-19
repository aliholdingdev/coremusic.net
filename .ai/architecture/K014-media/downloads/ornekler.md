---
node_id: K014-media/downloads/ornekler
node_type: module
status: draft
parent_id: K014-media/downloads
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.7"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/downloads — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.7
> (indirme · resume · saklama politikası ⚠️) + §22 (doğrulanmış standartlar: HTTP Range
> RFC 9110 §22.2 · ETag/cache başlıkları §22.5 · codec seti MP3/FLAC/WAV §22.1).
> Kod yorumları İngilizcedir; dosya salt-okunur vault içindir, kaynak koda dokunmaz.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK |
| 2 | Dayanak | media-master §9.7 + §22 (uydurma standart yasak) |
| 3 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala (§2-§16) |
| 4 | Resume | `Range: bytes=N-` (RFC 9110 §22.2) + `.part` geçici dosya + tamamlanınca atomik rename |
| 5 | Doğrulama | ETag (içerik hash §22.5) / SHA-256 checksum — uydurma kütüphane/paket adı yasak |
| 6 | Yeniden deneme | exponential backoff — üst sınır/süre ⚠️ UNKNOWN (Vault Steward §16/3) |
| 7 | İlerleme | indirilen/Toplam byte takibi (ölçüm eşikleri master §15/3 — ⚠️ UNKNOWN) |
| 8 | Değişiklik | yok — bu dosya yalnız tasarımı gösterir |

## §2 PHP

Resumable download manager: `Range` başlığıyla `.part` dosyasından devam, ETag checksum, atomik rename ve exponential backoff.

```php
<?php
declare(strict_types=1);

/**
 * K014-media/downloads — resumable download manager (TASARIM ÖNERİSİ).
 * Grounded in media-master.md §9.7 (resume) + §22.2 (HTTP Range, RFC 9110)
 * + §22.5 (ETag = içerik hash).
 * Repo'da bu sınıfın kodu YOK — illustrative design example.
 */
final class ResumableDownload
{
    private const MAX_ATTEMPTS = 5;

    /** @return bool true = doğrulandı ve atomik rename yapıldı */
    public function fetch(string $url, string $dest, string $expectedHash): bool
    {
        $part = $dest . '.part';
        for ($attempt = 0; $attempt < self::MAX_ATTEMPTS; $attempt++) {
            $offset = is_file($part) ? (int) filesize($part) : 0; // resume point

            $ctx = stream_context_create([
                'http' => [
                    'header'        => "Range: bytes={$offset}-\r\n", // RFC 9110 (master §22.2)
                    'ignore_errors' => true,
                ],
            ]);
            $src = @fopen($url, 'rb', false, $ctx);
            if ($src === false) {
                $this->backoff($attempt);
                continue;
            }

            $dst = fopen($part, $offset > 0 ? 'ab' : 'wb');
            $downloaded = $offset;
            $total = $offset + (int) ($http_response_header['content-length'] ?? 0);
            while (!feof($src)) {
                $chunk = fread($src, 65536);
                if ($chunk === false) {
                    break;
                }
                fwrite($dst, $chunk);
                $downloaded += strlen($chunk);
                $this->progress($downloaded, $total); // progress tracking
            }
            fclose($src);
            fclose($dst);

            // ETag/SHA-256 içerik hash doğrulaması (master §22.5)
            $actual = hash_file('sha256', $part);
            if ($actual !== $expectedHash) {
                $this->backoff($attempt); // bütünlük yok — yeniden dene
                continue;
            }

            return rename($part, $dest); // atomic rename: yarım dosya asla görünmez
        }
        return false;
    }

    private function backoff(int $attempt): void
    {
        usleep(100_000 * (2 ** $attempt)); // exponential backoff (üst sınır ⚠️ UNKNOWN)
    }

    private function progress(int $done, int $total): void
    {
        // UI raporu — gösterim K011-application'a aittir (master §2.2).
    }
}
```

## §3 JavaScript

`fetch` + `Range` ile resume, `.part` append, ETag doğrulama ve backoff döngüsü.

```js
// K014-media/downloads — resumable fetch + backoff (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.

const MAX_ATTEMPTS = 5;
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function downloadResumable(url, destPath, expectedHash, fs, onProgress) {
  const partPath = `${destPath}.part`;
  let attempt = 0;

  while (attempt < MAX_ATTEMPTS) {
    let offset = await fs.size(partPath); // resume point (.part varsa oradan devam)
    try {
      const res = await fetch(url, { headers: { Range: `bytes=${offset}-` } }); // RFC 9110
      if (res.status !== 200 && res.status !== 206) throw new Error(`HTTP ${res.status}`);

      const reader = res.body.getReader();
      const total = offset + Number(res.headers.get('content-length') ?? 0);
      for (;;) {
        const { done, value } = await reader.read();
        if (done) break;
        await fs.append(partPath, value);   // .part geçici dosyaya yaz
        offset += value.length;
        onProgress(offset, total);          // progress tracking
      }

      const actual = await fs.sha256(partPath);   // içerik hash (ETag — §22.5)
      if (actual !== expectedHash) throw new Error('checksum mismatch');

      await fs.rename(partPath, destPath); // atomic rename: yarım dosya görünmez
      return true;
    } catch (err) {
      await sleep(100 * 2 ** attempt++);   // exponential backoff
    }
  }
  return false;
}
```

## §4 TypeScript

Tip güvenli download state union + Range/ETag/backoff sözleşmeleri.

```ts
// K014-media/downloads — typed resume contract (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 + §22.2 (Range) + §22.5 (ETag = içerik hash).
// No repo code yet — illustrative design example.

export type DownloadState =
  | { tag: 'RESUMING'; offsetBytes: number }
  | { tag: 'DOWNLOADING'; receivedBytes: number; totalBytes: number }
  | { tag: 'VERIFYING'; actualHash: string }
  | { tag: 'DONE'; path: string }
  | { tag: 'FAILED'; attempt: number; reason: string };

export interface RangeResult {
  status: 200 | 206;
  etag: string | null;
  body: ReadableStream<Uint8Array>;
}

/** "bytes=N-" Range başlığı — resume temeli (RFC 9110 §14 — master §22.2). */
export function rangeHeader(offsetBytes: number): string {
  return `bytes=${offsetBytes}-`;
}

export async function resumeFetch(url: string, offsetBytes: number): Promise<RangeResult> {
  const res = await fetch(url, { headers: { Range: rangeHeader(offsetBytes) } });
  if (res.status !== 200 && res.status !== 206) throw new Error(`unexpected status ${res.status}`);
  if (!res.body) throw new Error('missing body');
  return { status: res.status as 200 | 206, etag: res.headers.get('etag'), body: res.body };
}

/** Üstel geri çekilme: 100ms * 2^attempt (üst sınır ⚠️ UNKNOWN — Vault Steward §16/3). */
export function backoffMs(attempt: number, baseMs = 100): number {
  return baseMs * 2 ** Math.min(attempt, 6);
}

/** ETag/SHA-256 eşitliği — bütünlük doğrulaması (master §22.5). */
export function verifyHash(expected: string, actual: string): boolean {
  return expected === actual;
}
```

## §5 C++

`.part` dosya yazımı + atomik rename + `Range` başlığı üretimi + backoff hesabı (STL ile).

```cpp
// K014-media/downloads — resumable download helpers (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. HTTP istemcisi: taşıma K010-network.
#include <cstdint>
#include <filesystem>
#include <fstream>
#include <string>

namespace cm::downloads {

// Range başlığı: "bytes=N-" (RFC 9110 §14 — master §22.2).
inline std::string rangeHeader(std::uint64_t offset) {
    return "Range: bytes=" + std::to_string(offset) + "-";
}

struct Progress {
    std::uint64_t received = 0;
    std::uint64_t total    = 0;
    double ratio() const noexcept {
        return total ? static_cast<double>(received) / static_cast<double>(total) : 0.0;
    }
};

// Üstel geri çekilme: 100ms * 2^attempt (tavanlı — üst sınır ⚠️ UNKNOWN).
inline std::uint32_t backoffMs(unsigned attempt) noexcept {
    return 100u << (attempt > 6 ? 6u : attempt);
}

// .part dosyasına append; başarıda atomik rename (yarım dosya asla görünmez).
inline bool appendAndFinalize(const std::filesystem::path& part,
                              const std::filesystem::path& dest,
                              const std::string& chunk) {
    std::error_code ec;
    if (!std::filesystem::exists(part, ec)) return false;
    {
        std::ofstream out(part, std::ios::binary | std::ios::app); // append kuyruğu
        if (!out.write(chunk.data(), static_cast<std::streamsize>(chunk.size()))) return false;
    }
    std::filesystem::rename(part, dest, ec); // atomic on same volume
    return !ec;
}

} // namespace cm::downloads
```

## §6 C#

`HttpClient` + `RangeHeaderValue` ile resume, ETag karşılaştırması, `.part` atomik kapanışı.

```csharp
// K014-media/downloads — Range resume + retry policy (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.
using System;
using System.IO;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Security.Cryptography;
using System.Threading;
using System.Threading.Tasks;

namespace CoreMusic.Downloads;

public sealed class ResumableDownloader
{
    private static readonly HttpClient Http = new();
    private const int MaxAttempts = 5;

    public async Task<bool> ResumeAsync(string url, string dest, string expectedHash,
                                        CancellationToken ct = default)
    {
        var part = dest + ".part";
        for (var attempt = 0; attempt < MaxAttempts; attempt++)
        {
            var offset = File.Exists(part) ? new FileInfo(part).Length : 0; // resume point
            try
            {
                using var req = new HttpRequestMessage(HttpMethod.Get, url);
                req.Headers.Range = new RangeHeaderValue(offset, null); // "Range: bytes=N-"

                using var res = await Http.SendAsync(req, HttpCompletionOption.ResponseHeadersRead, ct);
                res.EnsureSuccessStatusCode();

                var received = await AppendToPartAsync(res, part, offset, ct); // .part'a append + progress

                // ETag/SHA-256 içerik hash doğrulaması (master §22.5)
                var actual = await Sha256FileAsync(part, ct);
                if (!string.Equals(actual, expectedHash, StringComparison.OrdinalIgnoreCase))
                    throw new InvalidDataException("checksum mismatch");

                File.Move(part, dest, overwrite: true); // atomic rename: yarım dosya görünmez
                return true;
            }
            catch (Exception) when (attempt + 1 < MaxAttempts)
            {
                await Task.Delay(TimeSpan.FromMilliseconds(100 * (1 << attempt)), ct); // exponential backoff
            }
        }
        return false;
    }

    private static async Task<long> AppendToPartAsync(HttpResponseMessage res, string part,
                                                      long offset, CancellationToken ct)
    {
        await using var body = await res.Content.ReadAsStreamAsync(ct);
        await using var file = new FileStream(part, FileMode.Append, FileAccess.Write);
        var buffer = new byte[65536];
        var total = offset;
        int read;
        while ((read = await body.ReadAsync(buffer, ct)) > 0)
        {
            await file.WriteAsync(buffer.AsMemory(0, read), ct);
            total += read; // progress tracking (indirilen byte)
        }
        return total;
    }

    private static async Task<string> Sha256FileAsync(string path, CancellationToken ct)
    {
        await using var stream = File.OpenRead(path);
        var hash = await SHA256.HashDataAsync(stream, ct);
        return Convert.ToHexString(hash);
    }
}
```

## §7 SQL

İndirme işi kalıcılığı şema taslağı — NOT: kalıcı şema K007-data'ya aittir (master §2.2); bu blok taslaktır.

```sql
-- K014-media/downloads — indirme işi/kalıcılığı şema taslağı (TASARIM ÖNERİSİ).
-- Grounded in media-master.md §9.7 (resume · saklama politikası ⚠️ UNKNOWN)
-- + §22.2 (Range) + §22.5 (ETag = içerik hash).
-- NOT: kalıcı şema K007-data'ya aittir (master §2.2) — bu blok taslak/örnek niteliğindedir.

CREATE TABLE download_job (
    track_id       BIGINT       NOT NULL,
    url            VARCHAR(2048) NOT NULL,
    state          ENUM('QUEUED','DOWNLOADING','VERIFYING','DONE','FAILED')
                   NOT NULL DEFAULT 'QUEUED',
    part_path      VARCHAR(1024) NULL,              -- ".part" geçici dosya
    bytes_done     BIGINT       NOT NULL DEFAULT 0, -- resume offset (Range başlangıcı)
    bytes_total    BIGINT       NULL,               -- 206 Content-Range toplamı
    content_hash   CHAR(64)     NULL,               -- SHA-256 (ETag = içerik hash — §22.5)
    attempt_count  INT          NOT NULL DEFAULT 0, -- retry sayacı (backoff)
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (track_id),
    CONSTRAINT chk_bytes_done_nonneg CHECK (bytes_done >= 0)
) ENGINE=InnoDB;

-- Devam eden indirmeleri bul (kuyruk/kaldığı yerden devam taraması)
CREATE INDEX idx_download_active ON download_job (state, updated_at);
```

## §8 Go

`net/http` Range başlığı + `.part` boyutundan resume + exponential backoff.

```go
// K014-media/downloads — resume + exponential backoff (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package downloads

import (
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"io"
	"net/http"
	"os"
	"time"
)

const maxAttempts = 5

// rangeHeader: "bytes=N-" (RFC 9110 §14 — master §22.2).
func rangeHeader(offset int64) string { return fmt.Sprintf("bytes=%d-", offset) }

// backoff: 100ms * 2^attempt — üstel geri çekilme (üst sınır ⚠️ UNKNOWN).
func backoff(attempt int) time.Duration {
	return 100 * time.Millisecond * time.Duration(1<<min(attempt, 6))
}

// Resume .part dosyasının boyutundan devam eder; doğrulayınca atomik rename yapar.
func Resume(client *http.Client, url, dest, expectedHash string, onProgress func(int64, int64)) error {
	part := dest + ".part"

	for attempt := 0; attempt < maxAttempts; attempt++ {
		offset := partSize(part) // resume point

		req, err := http.NewRequest(http.MethodGet, url, nil)
		if err != nil {
			return err
		}
		req.Header.Set("Range", rangeHeader(offset)) // RFC 9110

		res, err := client.Do(req)
		if err != nil {
			time.Sleep(backoff(attempt))
			continue
		}

		f, err := os.OpenFile(part, os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0o644)
		if err != nil {
			res.Body.Close()
			return err
		}
		written, err := io.Copy(f, res.Body) // .part'a append
		res.Body.Close()
		f.Close()
		onProgress(offset+written, offset+written) // progress tracking

		if err != nil {
			time.Sleep(backoff(attempt))
			continue
		}

		// ETag/SHA-256 içerik hash doğrulaması (master §22.5)
		sum, err := sha256File(part)
		if err != nil || sum != expectedHash {
			time.Sleep(backoff(attempt)) // bütünlük yok — yeniden dene
			continue
		}

		return os.Rename(part, dest) // atomic rename: yarım dosya görünmez
	}
	return fmt.Errorf("download failed after %d attempts", maxAttempts)
}

func partSize(part string) int64 {
	st, err := os.Stat(part)
	if err != nil {
		return 0
	}
	return st.Size()
}

func sha256File(path string) (string, error) {
	b, err := os.ReadFile(path)
	if err != nil {
		return "", err
	}
	sum := sha256.Sum256(b)
	return hex.EncodeToString(sum[:]), nil
}
```

## §9 Rust

`Result` tabanlı resume akışı — panic yok; checksum uyuşmazlığı ve hata yolları `Err` döner.

```rust
// K014-media/downloads — Result-based resume, no panic (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. HTTP istemcisi: taşıma K010-network.

use std::fmt;
use std::time::Duration;

#[derive(Debug)]
pub enum DownloadError {
    Http(u16),
    HashMismatch { expected: String, actual: String },
    Io(std::io::Error),
    AttemptsExhausted(u32),
}

impl fmt::Display for DownloadError {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        match self {
            DownloadError::Http(code) => write!(f, "unexpected HTTP status {code}"),
            DownloadError::HashMismatch { expected, actual } => {
                write!(f, "checksum mismatch: expected {expected}, got {actual}")
            }
            DownloadError::Io(e) => write!(f, "io error: {e}"),
            DownloadError::AttemptsExhausted(n) => write!(f, "gave up after {n} attempts"),
        }
    }
}

impl From<std::io::Error> for DownloadError {
    fn from(e: std::io::Error) -> Self { DownloadError::Io(e) }
}

/// Range başlığı: "bytes=N-" (RFC 9110 §14 — master §22.2).
pub fn range_header(offset: u64) -> String {
    format!("bytes={offset}-")
}

/// Üstel geri çekilme: 100ms * 2^attempt (tavanlı — üst sınır ⚠️ UNKNOWN).
pub fn backoff(attempt: u32) -> Duration {
    Duration::from_millis(100u64.saturating_mul(1u64 << attempt.min(6)))
}

/// ETag/SHA-256 eşitliği — bütünlük doğrulaması (master §22.5).
pub fn verify_hash(expected: &str, actual: &str) -> Result<(), DownloadError> {
    if expected == actual {
        Ok(())
    } else {
        Err(DownloadError::HashMismatch {
            expected: expected.to_string(),
            actual: actual.to_string(),
        })
    }
}

/// .part -> hedef: std::fs::rename atomik taşır (aynı birimde; yarım dosya görünmez).
pub fn finalize(part: &std::path::Path, dest: &std::path::Path) -> Result<(), DownloadError> {
    std::fs::rename(part, dest)?;
    Ok(())
}
```

## §10 Python

`urllib` + `Range` başlığı, `.part` append, SHA-256 doğrulama ve backoff döngüsü.

```python
# K014-media/downloads — resumable download manager (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
# Repo'da bu modülün kodu YOK — illustrative design example. Taşıma: K010-network.

from __future__ import annotations

import hashlib
import os
import time
import urllib.request

MAX_ATTEMPTS = 5


def backoff(attempt: int) -> float:
    """Üstel geri çekilme: 0.1s * 2^attempt (üst sınır ⚠️ UNKNOWN)."""
    return 0.1 * (2 ** min(attempt, 6))


def sha256_file(path: str) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for block in iter(lambda: f.read(65536), b""):
            h.update(block)
    return h.hexdigest()  # içerik hash (ETag karşılığı — master §22.5)


def resume(url: str, dest: str, expected_hash: str, on_progress=None) -> bool:
    part = f"{dest}.part"
    for attempt in range(MAX_ATTEMPTS):
        offset = os.path.getsize(part) if os.path.exists(part) else 0  # resume point
        req = urllib.request.Request(url, headers={"Range": f"bytes={offset}-"})  # RFC 9110
        try:
            with urllib.request.urlopen(req) as resp, open(part, "ab" if offset else "wb") as out:
                total = offset + int(resp.headers.get("Content-Length", 0))
                while True:
                    chunk = resp.read(65536)
                    if not chunk:
                        break
                    out.write(chunk)
                    offset += len(chunk)
                    if on_progress:
                        on_progress(offset, total)  # progress tracking

            if sha256_file(part) != expected_hash:  # checksum doğrulaması
                raise ValueError("checksum mismatch — bütünlük yok")

            os.replace(part, dest)  # atomic rename: yarım dosya asla görünmez
            return True
        except Exception:
            time.sleep(backoff(attempt))  # exponential backoff
    return False
```

## §11 Java

`HttpURLConnection` + `Range` başlığı, `.part` append, SHA-256 doğrulama, backoff.

```java
// K014-media/downloads — Range resume + exponential backoff (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package com.coremusic.downloads;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.StandardOpenOption;
import java.security.MessageDigest;

public final class ResumableDownload {
    private static final int MAX_ATTEMPTS = 5;

    /** "bytes=N-" Range başlığı (RFC 9110 §14 — master §22.2). */
    static String rangeHeader(long offset) { return "bytes=" + offset + "-"; }

    /** Üstel geri çekilme: 100ms * 2^attempt (üst sınır ⚠️ UNKNOWN). */
    static long backoffMs(int attempt) { return 100L << Math.min(attempt, 6); }

    /** Resume: .part boyutundan devam; doğrulayınca atomik rename. */
    public static boolean resume(URL url, Path dest, String expectedHash, ProgressSink progress)
            throws IOException {
        Path part = dest.resolveSibling(dest.getFileName() + ".part");

        for (int attempt = 0; attempt < MAX_ATTEMPTS; attempt++) {
            long offset = Files.isRegularFile(part) ? Files.size(part) : 0; // resume point
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestProperty("Range", rangeHeader(offset)); // RFC 9110
            try {
                int status = conn.getResponseCode();
                if (status != 200 && status != 206) throw new IOException("HTTP " + status);

                long received = appendToPart(conn, part, offset, progress); // .part append + progress

                // ETag/SHA-256 içerik hash doğrulaması (master §22.5)
                String actual = sha256Hex(part);
                if (!actual.equalsIgnoreCase(expectedHash)) throw new IOException("checksum mismatch");

                Files.move(part, dest,
                        java.nio.file.StandardCopyOption.REPLACE_EXISTING); // atomic rename
                return true;
            } catch (IOException e) {
                sleep(backoffMs(attempt)); // exponential backoff
            } finally {
                conn.disconnect();
            }
        }
        return false;
    }

    private static long appendToPart(HttpURLConnection conn, Path part, long offset,
                                     ProgressSink progress) throws IOException {
        long total = offset;
        try (InputStream in = conn.getInputStream();
             OutputStream out = Files.newOutputStream(part, StandardOpenOption.CREATE,
                     StandardOpenOption.APPEND)) {
            byte[] buf = new byte[65536];
            int n;
            while ((n = in.read(buf)) > 0) {
                out.write(buf, 0, n);
                total += n; // progress tracking
                progress.onProgress(total, total);
            }
        }
        return total;
    }

    private static String sha256Hex(Path path) throws IOException {
        MessageDigest md = MessageDigest.getInstance("SHA-256");
        byte[] digest = md.digest(Files.readAllBytes(path));
        StringBuilder sb = new StringBuilder();
        for (byte b : digest) sb.append(String.format("%02x", b & 0xff));
        return sb.toString();
    }

    private static void sleep(long ms) {
        try { Thread.sleep(ms); } catch (InterruptedException ie) { Thread.currentThread().interrupt(); }
    }

    interface ProgressSink { void onProgress(long done, long total); }
}
```

## §12 Swift

`URLSession` + `Range` başlığı, `.part` dosyaya append, SHA-256 doğrulama, backoff.

```swift
// K014-media/downloads — Range resume + backoff (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.
import Foundation
import CryptoKit // SHA-256 — dile yerleşik

enum DownloadError: Error {
    case httpStatus(Int)
    case checksumMismatch(expected: String, actual: String)
    case attemptsExhausted(Int)
}

struct ResumableDownload {
    static let maxAttempts = 5

    /// "bytes=N-" Range başlığı (RFC 9110 §14 — master §22.2).
    static func rangeHeader(offset: Int64) -> String { "bytes=\(offset)-" }

    /// Üstel geri çekilme: 0.1s * 2^attempt (üst sınır ⚠️ UNKNOWN).
    static func backoff(attempt: Int) -> TimeInterval { 0.1 * pow(2, Double(min(attempt, 6))) }

    /// Resume: .part boyutundan devam; doğrulayınca atomik rename.
    static func resume(url: URL, dest: URL, expectedHash: String,
                       onProgress: (Int64, Int64) -> Void = { _, _ in }) async throws {
        let part = dest.appendingPathExtension("part")
        let fm = FileManager.default

        for attempt in 0..<maxAttempts {
            let attrs = try? fm.attributesOfItem(atPath: part.path)
            let offset = (attrs?[.size] as? Int64) ?? 0            // resume point

            var req = URLRequest(url: url)
            req.setValue(rangeHeader(offset: offset), forHTTPHeaderField: "Range") // RFC 9110

            do {
                let (data, response) = try await URLSession.shared.data(for: req)
                guard let http = response as? HTTPURLResponse,
                      http.statusCode == 200 || http.statusCode == 206
                else { throw DownloadError.httpStatus((response as? HTTPURLResponse)?.statusCode ?? -1) }

                // .part'a append + progress tracking
                let handle = try FileHandle(forWritingTo: part)
                defer { try? handle.close() }
                if fm.fileExists(atPath: part.path) { try handle.seekToEnd() }
                else { fm.createFile(atPath: part.path, contents: nil) }
                try handle.write(contentsOf: data)
                onProgress(offset + Int64(data.count), offset + Int64(data.count))

                // ETag/SHA-256 içerik hash doğrulaması (master §22.5)
                let fileData = try Data(contentsOf: part)
                let digest = SHA256.hash(data: fileData)
                let actual = digest.map { String(format: "%02x", $0) }.joined()
                guard actual == expectedHash else {
                    throw DownloadError.checksumMismatch(expected: expectedHash, actual: actual)
                }

                try fm.moveItem(at: part, to: dest) // atomic rename: yarım dosya görünmez
                return
            } catch {
                if attempt == maxAttempts - 1 { throw DownloadError.attemptsExhausted(maxAttempts) }
                let delay = backoff(attempt: attempt) * 1_000_000_000
                try await Task.sleep(nanoseconds: UInt64(delay)) // exponential backoff
            }
        }
    }
}
```

## §13 Kotlin

Sealed class sonuç + `Range` resume + `.part` append + exponential backoff.

```kotlin
// K014-media/downloads — resume + exponential backoff (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package com.coremusic.downloads

import java.io.File
import java.security.MessageDigest

sealed class DownloadResult {
    data class Done(val path: String, val bytes: Long) : DownloadResult()
    data class Failed(val attempts: Int, val reason: String) : DownloadResult()
}

data class RangeResponse(val status: Int, val body: ByteArray)

object ResumableDownload {
    const val MAX_ATTEMPTS = 5

    /** "bytes=N-" Range başlığı (RFC 9110 §14 — master §22.2). */
    fun rangeHeader(offset: Long): String = "bytes=$offset-"

    /** Üstel geri çekilme: 100ms * 2^attempt (üst sınır ⚠️ UNKNOWN). */
    fun backoffMs(attempt: Int): Long = 100L shl minOf(attempt, 6)

    /** Resume: .part boyutundan devam; doğrulayınca atomik rename. */
    fun resume(url: String, dest: String, expectedHash: String,
               get: (String, String) -> RangeResponse): DownloadResult { // taşıma: K010-network
        val part = "$dest.part"
        var lastError = "unknown"

        repeat(MAX_ATTEMPTS) { attempt ->
            val offset = File(part).takeIf { it.exists() }?.length() ?: 0L // resume point
            try {
                val res = get(url, rangeHeader(offset)) // Range başlığı
                require(res.status == 200 || res.status == 206) { "HTTP ${res.status}" }

                File(part).appendBytes(res.body) // .part'a append

                val actual = sha256Hex(File(part).readBytes())  // içerik hash (master §22.5)
                require(actual.equals(expectedHash, ignoreCase = true)) { "checksum mismatch" }

                check(File(part).renameTo(File(dest))) { "rename failed" } // atomic rename
                return DownloadResult.Done(dest, File(dest).length())
            } catch (e: Exception) {
                lastError = e.message ?: "error"
                Thread.sleep(backoffMs(attempt)) // exponential backoff
            }
        }
        return DownloadResult.Failed(MAX_ATTEMPTS, lastError)
    }

    private fun sha256Hex(bytes: ByteArray): String =
        MessageDigest.getInstance("SHA-256").digest(bytes)
            .joinToString("") { "%02x".format(it.toInt() and 0xff) }
}
```

## §14 Ruby

Hash tabanlı akış + `Range` başlığı, `.part` append, Digest doğrulama, backoff.

```ruby
# K014-media/downloads — resume + exponential backoff (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
# Repo'da bu sınıfın kodu YOK — illustrative design example. Taşıma: K010-network.

require 'digest'

class ResumableDownload
  MAX_ATTEMPTS = 5

  # "bytes=N-" Range başlığı (RFC 9110 §14 — master §22.2).
  def self.range_header(offset)
    "bytes=#{offset}-"
  end

  # Üstel geri çekilme: 0.1s * 2^attempt (üst sınır ⚠️ UNKNOWN).
  def self.backoff(attempt)
    0.1 * (2**[attempt, 6].min)
  end

  # opener bir tasarım seam'idir: HTTP taşıması K010-network'dedir.
  # Yanıt beklenen: { status:, body: } (bayt dizesi)
  def initialize(opener:)
    @opener = opener
  end

  # Resume: .part boyutundan devam; doğrulayınca atomik rename.
  def fetch(url, dest, expected_hash, &on_progress)
    part = "#{dest}.part"

    MAX_ATTEMPTS.times do |attempt|
      offset = File.exist?(part) ? File.size(part) : 0 # resume point
      begin
        res = @opener.call(url, self.class.range_header(offset))
        raise "HTTP #{res[:status]}" unless [200, 206].include?(res[:status])

        File.open(part, offset.positive? ? 'ab' : 'wb') { |io| io.write(res[:body]) } # .part append
        total = offset + res[:body].bytesize
        on_progress&.call(total, total) # progress tracking

        actual = Digest::SHA256.file(part).hexdigest # içerik hash (master §22.5)
        raise 'checksum mismatch' unless actual == expected_hash

        File.rename(part, dest) # atomic rename: yarım dosya asla görünmez
        return true
      rescue StandardError
        sleep(self.class.backoff(attempt)) # exponential backoff
      end
    end
    false
  end
end
```

## §15 Elixir

Functional resume akışı — `{:ok, _}` / `{:error, _}` Result, ETag/SHA-256 doğrulama ve backoff.

```elixir
# K014-media/downloads — resume + exponential backoff (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
# Repo'da bu modülün kodu YOK — illustrative design example. Taşıma: K010-network.

defmodule CoreMusic.Downloads.Resumable do
  @max_attempts 5

  # "bytes=N-" Range başlığı (RFC 9110 §14 — master §22.2).
  def range_header(offset), do: "bytes=#{offset}-"

  # Üstel geri çekilme: 100ms * 2^attempt (üst sınır ⚠️ UNKNOWN).
  def backoff(attempt), do: round(100 * :math.pow(2, min(attempt, 6)))

  @doc "Resume: .part boyutundan devam; doğrulayınca atomik rename."
  def fetch(url, dest, expected_hash, get, on_progress \\ fn _, _ -> :ok end) do
    part = dest <> ".part"
    do_fetch(url, dest, part, expected_hash, get, on_progress, 0)
  end

  defp do_fetch(_url, _dest, _part, _expected, _get, _progress, attempt)
       when attempt >= @max_attempts,
       do: {:error, :attempts_exhausted}

  defp do_fetch(url, dest, part, expected_hash, get, on_progress, attempt) do
    offset = part_size(part) # resume point

    case get.(url, range_header(offset)) do # {:ok, %{status:, body:}} | {:error, reason}
      {:ok, %{status: status, body: body}} when status in [200, 206] ->
        File.write!(part, body, [:append])          # .part'a append
        on_progress.(offset + byte_size(body), offset + byte_size(body)) # progress

        if sha256_hex(part) == expected_hash do     # içerik hash (master §22.5)
          File.rename!(part, dest)                  # atomic rename: yarım dosya görünmez
          {:ok, dest}
        else
          Process.sleep(backoff(attempt))           # checksum uyuşmadı — backoff
          do_fetch(url, dest, part, expected_hash, get, on_progress, attempt + 1)
        end

      _other ->
        Process.sleep(backoff(attempt))             # exponential backoff
        do_fetch(url, dest, part, expected_hash, get, on_progress, attempt + 1)
    end
  end

  defp part_size(part) do
    case File.stat(part) do
      {:ok, %{size: size}} -> size
      _ -> 0
    end
  end

  defp sha256_hex(part) do
    part |> File.read!() |> then(&:crypto.hash(:sha256, &1)) |> Base.encode16(case: :lower)
  end
end
```

## §16 Scala

`Either` tabanlı resume — hata tipi ayrılmış; `java.nio` ile atomik rename.

```scala
// K014-media/downloads — Either tabanlı resume + backoff (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.7 (resume) + §22.2 (Range RFC 9110) + §22.5 (ETag).
// No repo code yet — illustrative design example. Taşıma: K010-network.
package com.coremusic.downloads

import java.nio.file.{Files, Path, StandardCopyOption}
import java.security.MessageDigest
import scala.concurrent.duration._

sealed trait DownloadError
object DownloadError {
  case class HttpStatus(code: Int) extends DownloadError
  case class HashMismatch(expected: String, actual: String) extends DownloadError
  case object AttemptsExhausted extends DownloadError
}

object ResumableDownload {
  val MaxAttempts = 5

  /** "bytes=N-" Range başlığı (RFC 9110 §14 — master §22.2). */
  def rangeHeader(offset: Long): String = s"bytes=$offset-"

  /** Üstel geri çekilme: 100ms * 2^attempt (üst sınır ⚠️ UNKNOWN). */
  def backoff(attempt: Int): FiniteDuration = (100L << math.min(attempt, 6)).millis

  /** .part boyutu — resume noktası (dosya yoksa 0). */
  def partOffset(part: Path): Long =
    if (Files.exists(part)) Files.size(part) else 0L

  /**
   * Resume + checksum doğrulama + atomik rename.
   * `get` bir tasarım seam'idir (taşıma K010-network): (url, range) -> (status, body).
   */
  def resume(url: String,
             dest: Path,
             expectedHash: String,
             get: (String, String) => Either[DownloadError, (Int, Array[Byte])],
             onProgress: (Long, Long) => Unit = (_, _) => ()): Either[DownloadError, Path] = {
    val part = dest.resolveSibling(dest.getFileName.toString + ".part")

    def loop(attempt: Int): Either[DownloadError, Path] =
      if (attempt >= MaxAttempts) Left(DownloadError.AttemptsExhausted)
      else
        get(url, rangeHeader(partOffset(part))) match { // Range başlığı
          case Right((status, body)) if status == 200 || status == 206 =>
            val offset = partOffset(part)
            Files.write(part, body, StandardCopyOption.APPEND)      // .part'a append
            onProgress(offset + body.length, offset + body.length)  // progress tracking

            val actual = hex(sha256(Files.readAllBytes(part)))      // içerik hash (§22.5)
            if (actual.equalsIgnoreCase(expectedHash)) {
              Files.move(part, dest, StandardCopyOption.REPLACE_EXISTING) // atomic rename
              Right(dest)
            } else {
              Thread.sleep(backoff(attempt).toMillis) // checksum uyuşmadı — backoff
              loop(attempt + 1)
            }

          case _ =>
            Thread.sleep(backoff(attempt).toMillis)    // exponential backoff
            loop(attempt + 1)
        }

    loop(0)
  }

  private def sha256(bytes: Array[Byte]): Array[Byte] =
    MessageDigest.getInstance("SHA-256").digest(bytes)

  private def hex(bytes: Array[Byte]): String =
    bytes.map(b => f"${b & 0xff}%02x").mkString
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.7 | vault | downloads rol/sınır/iddata (resume TASARIM İDDİASI) |
| 2 | K014-media/media-master.md §22.2 | vault | HTTP Range resume — RFC 9110 (STD 97) |
| 3 | K014-media/media-master.md §22.5 | vault | ETag (içerik hash) + `Cache-Control: immutable` başlıkları |
| 4 | K014-media/media-master.md §22.1 | vault | indirilebilir codec seti MP3/FLAC/WAV (patent durumu doğrulandı) |
| 5 | K014-media/media-master.md §14.3 | vault | indirme zinciri: K011 → downloads → K010 → offline |
| 6 | K014-media/media-master.md §13/4 | vault | indirme resume test senaryosu (yarıda kesilme) |
| 7 | RFC 9110 (STD 97) | master §22.7 kaynak listesi | `Range` başlığı temeli |
| 8 | K014-media/downloads/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.7 + §22.

**Authority:** Bayram Ali / Vault Steward
