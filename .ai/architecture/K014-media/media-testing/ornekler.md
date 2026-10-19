---
node_id: K014-media/media-testing/ornekler
node_type: module
status: draft
parent_id: K014-media/media-testing
children: []
depends_on: []
evidence:
  - "K014-media/media-master.md §9.11"
  - "K014-media/media-master.md §13 (test senaryoları)"
  - "K014-media/media-master.md §22 (doğrulanmış standartlar)"
validation:
  - "2026-10-17: örnekler TASARIM ÖNERİSİ etiketli — medya düğümü repo'da henüz kodu YOK (kademe-2 design node)"
open_questions: []
group: kod
---

# K014-media/media-testing — ornekler.md

> **Not:** Bu dosyadaki tüm örnekler **TASARIM ÖNERİSİ** illustrative design örnekleridir
> (kademe-2 design node — repo'da henüz bu alanın kodu YOK). Dayanak: media-master §9.11
> (medya testleri · kapsam decode/çalma/akış kalitesi TASARIM İDDİASI · **araç seti
> ⚠️ UNKNOWN** · sahiplik K019-testing ile belirsiz §7/4), §13 (test senaryoları:
> decode doğruluğu · akış kalitesi · …) ve §22 (doğrulanmış standartlar: MP3/FLAC/WAV
> §22.1 · RFC 9110 Range / HLS RFC 8216 / DASH ISO/IEC 23009-1:2026 §22.2 · EBU R128
> −23 LUFS / ReplayGain 2.0 −18 LUFS §22.3 · ETag/immutable §22.5). **EŞİK UYARISI:**
> ölçüm eşikleri hâlâ **UNKNOWN**'dur (master §15) — sayısal tolerans gerektiren her
> yerde `TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister` işareti vardır;
> bu dosyada eşik KONMAZ. **ARAÇ UYARISI:** test aracı seti AÇIKTIR (master §12/10 →
> §22.6 "⏳ AÇIK") — örnekler uydurma package/sürüm ADI YAZMAZ; yalnız dil-dahili std
> yapılar veya açıkça işaretli pseudocode seam'ler kullanır. Kod yorumları İngilizcedir;
> dosya salt-okunur vault içindir, kaynak koda dokunmaz.

## §1 Örnek Kullanım Kılavuzu

| # | Öğe | Kural |
|---|---|---|
| 1 | Örnek statüsü | hepsi TASARIM ÖNERİSİ — repo'da bu alan kodu YOK (kademe-2 design node) |
| 2 | Dayanak | media-master §9.11 (rol/sınır) + §13 (senaryolar) + §22 (doğrulanmış standartlar) — uydurma standart/kütüphane/sürüm yasak |
| 3 | Dil listesi | 15 dil: PHP · JavaScript · TypeScript · C++ · C# · SQL · Go · Rust · Python · Java · Swift · Kotlin · Ruby · Elixir · Scala (§2-§16) |
| 4 | **ARAÇ UYARISI** | test aracı seti AÇIKTIR (master §12/10 → §22.6 "⏳ AÇIK") — örnekler dil-dahili std yapılar veya işaretli pseudocode seam'ler kullanır |
| 5 | Decode doğruluk | çözülmüş PCM'in SHA-256 hash'i → golden fixture hash'i ile birebir (senaryo master §13/1) |
| 6 | Loudness | EBU R128 hedef −23 LUFS (master §22.3 doğrulanmış); **tolerans eşik AÇIK** (master §15) → her tolere eden sayı TASARIM ÖNERİSİ işaretli |
| 7 | Range sözleşmesi | 206 + `Content-Range` + `Accept-Ranges` (RFC 9110 · master §22.2) |
| 8 | Etiket round-trip | yaz → oku → eşitlik (ID3v2 · Vorbis Comment · `ilst` · LIST-INFO · master §22.3) |
| 9 | Sahiplik | decode doğruluğu sahipliği ⚠️ master §7/4 (media-testing vs K019); akış kalitesi sahipliği ⚠️ belirsiz (master §13/2) |
| 10 | Değişiklik | yok — bu dosya yalnız tasarımı gösterir; `*.php`/`*.js`/`*.css`/`*.sql` kaynak koda dokunulmaz |

## §2 PHP

Decode hash → golden fixture (`hash`/`hash_equals`), EBU R128 −23 LUFS, Range 206 + `Content-Range` + `Accept-Ranges`, etiket round-trip — HTTP/decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```php
<?php
declare(strict_types=1);

/**
 * K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
 * Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
 * sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
 * §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
 * No repo code yet — illustrative design example (kademe-2 design node).
 */

final class TestFailure extends RuntimeException {}

function assertSameValue(mixed $expected, mixed $actual, string $label): void
{
    if ($expected !== $actual) {
        throw new TestFailure(
            "$label: expected " . var_export($expected, true) . ', got ' . var_export($actual, true)
        );
    }
}

/** master §22.3 — doğrulanmış EBU R128 yayın hedefi. */
const EBU_R128_TARGET_LUFS = -23.0;

// TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
const LOUDNESS_TOLERANCE_LU = 1.0;

// --- A) decode doğruluk: çözülmüş PCM hash'i -> golden fixture (master §13/1) ---
// decodeFilePcm(): decode seam'i — ses motoru K003-audio'dadır (pseudocode, master §2.2).
function testDecodeMatchesGoldenFixture(string $decodedPcm, string $goldenSha256): void
{
    $actual = hash('sha256', $decodedPcm); // dil-dahili std
    if (!hash_equals($goldenSha256, $actual)) { // sabit-zamanlı karşılaştırma
        throw new TestFailure("decode hash mismatch: golden=$goldenSha256 actual=$actual");
    }
}

// --- B) loudness doğrulama: EBU R128 hedef −23 LUFS (master §22.3) ---
// measureLoudnessLufs(): R128 ölçer seam'i — pseudocode (araç seti §12/10 UNKNOWN).
function testLoudnessNearTargetLufs(
    float $measuredLufs,
    float $targetLufs = EBU_R128_TARGET_LUFS,
    float $toleranceLu = LOUDNESS_TOLERANCE_LU
): void {
    if (abs($measuredLufs - $targetLufs) > $toleranceLu) {
        throw new TestFailure("loudness $measuredLufs LUFS outside ±$toleranceLu of $targetLufs");
    }
}

// --- C) HTTP Range sözleşmesi: 206 + Content-Range + Accept-Ranges (RFC 9110 · §22.2) ---
// httpRangeGet(): pseudocode seam — taşıma K010-network'dedir (master §2.2).
/** @return array{status: int, headers: array<string, string>} */
function httpRangeGet(string $url, int $start, int $end): array;

function testRangeContractReturns206(array $response, int $start, int $end, int $totalBytes): void
{
    assertSameValue(206, $response['status'], 'range status');
    assertSameValue('bytes', $response['headers']['accept-ranges'] ?? null, 'Accept-Ranges');
    assertSameValue(
        "bytes $start-$end/$totalBytes",
        $response['headers']['content-range'] ?? null,
        'Content-Range'
    );
}

// --- D) etiket round-trip: yaz -> oku -> eşitlik (master §22.3) ---
// writeTags()/readTags(): etiket seam'i (pseudocode) — formatlar: ID3v2 (MP3) ·
// Vorbis Comment (FLAC/Ogg) · ilst (MP4/M4A) · LIST-INFO (WAV). ID3v2.3↔v2.4
// TYER→TDRC kırılması ayrıca sürümlü fixture ile sınanır.
function writeTags(string $file, array $tags): void;
/** @return array<string, string|null> */
function readTags(string $file): array;

function testTagReadWriteRoundTrip(string $file, array $tagsOut, array $expected): void
{
    writeTags($file, $tagsOut);
    $tagsIn = readTags($file);
    foreach ($expected as $key => $value) {
        assertSameValue($value, $tagsIn[$key] ?? null, "tag $key");
    }
}
```

## §3 JavaScript

`node:crypto` SHA-256 (`timingSafeEqual`), EBU R128 −23 LUFS, `fetch`-tarzı Range 206 sözleşmesi, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```js
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

import { createHash, timingSafeEqual } from 'node:crypto'; // Node.js stdlib

export class TestFailure extends Error {}

export function assertEqual(expected, actual, label) {
  if (expected !== actual) throw new TestFailure(`${label}: expected ${expected}, got ${actual}`);
}

/** master §22.3 — doğrulanmış EBU R128 yayın hedefi. */
export const EBU_R128_TARGET_LUFS = -23.0;

// TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
export const LOUDNESS_TOLERANCE_LU = 1.0;

// A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
export function testDecodeMatchesGoldenFixture(decodedPcm, goldenSha256Hex) {
  const actual = createHash('sha256').update(decodedPcm).digest('hex');
  const a = Buffer.from(actual, 'hex');
  const b = Buffer.from(goldenSha256Hex, 'hex');
  if (a.length !== b.length || !timingSafeEqual(a, b)) {
    throw new TestFailure(`decode hash mismatch: golden=${goldenSha256Hex} actual=${actual}`);
  }
}

// B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
// measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
export function testLoudnessNearTargetLufs(
  measuredLufs,
  targetLufs = EBU_R128_TARGET_LUFS,
  toleranceLu = LOUDNESS_TOLERANCE_LU,
) {
  if (Math.abs(measuredLufs - targetLufs) > toleranceLu) {
    throw new TestFailure(`loudness ${measuredLufs} LUFS outside ±${toleranceLu} of ${targetLufs}`);
  }
}

// C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
// httpRangeGet(): pseudocode seam — taşıma K010-network (master §2.2); {status, headers} döner.
export async function testRangeContractReturns206({ status, headers }, start, end, totalBytes) {
  assertEqual(206, status, 'range status');
  assertEqual('bytes', headers.get('accept-ranges'), 'Accept-Ranges');
  assertEqual(`bytes ${start}-${end}/${totalBytes}`, headers.get('content-range'), 'Content-Range');
}

// D) etiket round-trip — yaz → oku → eşitlik (master §22.3: ID3v2 · Vorbis Comment ·
// ilst · LIST-INFO). writeTags()/readTags(): etiket seam'i (pseudocode).
export function testTagReadWriteRoundTrip(file, tagsOut, expected) {
  writeTags(file, tagsOut);
  const tagsIn = readTags(file);
  for (const [key, value] of Object.entries(expected)) {
    assertEqual(value, tagsIn[key], `tag ${key}`);
  }
}
```

## §4 TypeScript

Tipli harness: `RangeResponse`/`Tags` arayüzleri, `node:crypto` SHA-256, EBU R128 −23 LUFS, Range 206 sözleşmesi, etiket round-trip — seam'ler `declare` ile işaretli pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```ts
// K014-media/media-testing — typed test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

import { createHash, timingSafeEqual } from 'node:crypto'; // Node.js stdlib

export class TestFailure extends Error {}

export interface RangeResponse {
  status: number;
  headers: { get(name: string): string | null };
}

export type Tags = Record<string, string | undefined>;

export function assertEqual<T>(expected: T, actual: T, label: string): void {
  if (expected !== actual) throw new TestFailure(`${label}: expected ${String(expected)}, got ${String(actual)}`);
}

/** master §22.3 — doğrulanmış EBU R128 yayın hedefi. */
export const EBU_R128_TARGET_LUFS = -23.0;

// TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
export const LOUDNESS_TOLERANCE_LU = 1.0;

// A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
export function testDecodeMatchesGoldenFixture(decodedPcm: Uint8Array, goldenSha256Hex: string): void {
  const actual = createHash('sha256').update(decodedPcm).digest('hex');
  const a = Buffer.from(actual, 'hex');
  const b = Buffer.from(goldenSha256Hex, 'hex');
  if (a.length !== b.length || !timingSafeEqual(a, b)) {
    throw new TestFailure(`decode hash mismatch: golden=${goldenSha256Hex} actual=${actual}`);
  }
}

// B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
// measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
export function testLoudnessNearTargetLufs(
  measuredLufs: number,
  targetLufs: number = EBU_R128_TARGET_LUFS,
  toleranceLu: number = LOUDNESS_TOLERANCE_LU,
): void {
  if (Math.abs(measuredLufs - targetLufs) > toleranceLu) {
    throw new TestFailure(`loudness ${measuredLufs} LUFS outside ±${toleranceLu} of ${targetLufs}`);
  }
}

// C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
// httpRangeGet(): pseudocode seam — taşıma K010-network (master §2.2).
export function testRangeContractReturns206(res: RangeResponse, start: number, end: number, totalBytes: number): void {
  assertEqual(206, res.status, 'range status');
  assertEqual('bytes', res.headers.get('accept-ranges'), 'Accept-Ranges');
  assertEqual(`bytes ${start}-${end}/${totalBytes}`, res.headers.get('content-range'), 'Content-Range');
}

// D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
// writeTags()/readTags(): etiket seam'i — pseudocode (declare ile işaretli).
declare function writeTags(file: string, tags: Record<string, string>): void;
declare function readTags(file: string): Tags;

export function testTagReadWriteRoundTrip(
  file: string,
  tagsOut: Record<string, string>,
  expected: Record<string, string>,
): void {
  writeTags(file, tagsOut);
  const tagsIn = readTags(file);
  for (const [key, value] of Object.entries(expected)) {
    assertEqual(value, tagsIn[key], `tag ${key}`);
  }
}
```

## §5 C++

`std::string` fixture hash karşılaştırması, EBU R128 −23 LUFS, Range 206 sözleşmesi, etiket round-trip — C++ std'de SHA-256/HTTP/etiket YOKTUR; seam'ler açıkça `PSEUDOCODE SEAM` işaretlidir (master §9.11 · §13/1 · §15 · §22.2 · §22.3 · araç §12/10 UNKNOWN).

```cpp
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

#include <cmath>
#include <map>
#include <stdexcept>
#include <string>
#include <string_view>

struct TestFailure : std::runtime_error {
    using std::runtime_error::runtime_error;
};

// master §22.3 — doğrulanmış EBU R128 yayın hedefi.
inline constexpr float kEbuR128TargetLufs = -23.0F;

// TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
inline constexpr float kLoudnessToleranceLu = 1.0F;

// --- PSEUDOCODE SEAMs (C++ standard library bu yüzeyleri içermez) ---
// SHA-256: uydurma kütüphane adı YAZILMAZ — çözüm test aracı seti kararıdır (§12/10 UNKNOWN).
std::string sha256HexPseudocode(std::string_view bytes);
// Decode pipeline K003-audio'dadır (master §2.2).
float measureLoudnessLufsPseudocode(std::string_view decodedPcm);
// HTTP taşıma K010-network'dedir (master §2.2).
struct RangeResponse {
    int status;
    std::string acceptRanges;   // "bytes"
    std::string contentRange;   // "bytes 1024-2047/5242880"
};
RangeResponse httpRangeGetPseudocode(std::string_view url, int start, int end);
// Etiket seam'i — formatlar master §22.3 (ID3v2 · Vorbis Comment · ilst · LIST-INFO).
void writeTagsPseudocode(std::string_view file, const std::map<std::string, std::string>& tags);
std::map<std::string, std::string> readTagsPseudocode(std::string_view file);

// A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
void testDecodeMatchesGoldenFixture(std::string_view decodedPcm, std::string_view goldenSha256Hex) {
    const std::string actual = sha256HexPseudocode(decodedPcm);
    if (actual != goldenSha256Hex) {
        throw TestFailure("decode hash mismatch: golden=" + std::string(goldenSha256Hex) +
                          " actual=" + actual);
    }
}

// B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
void testLoudnessNearTargetLufs(float measuredLufs,
                                float targetLufs = kEbuR128TargetLufs,
                                float toleranceLu = kLoudnessToleranceLu) {
    if (std::fabs(measuredLufs - targetLufs) > toleranceLu) {
        throw TestFailure("loudness outside tolerance of EBU R128 target");
    }
}

// C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
void testRangeContractReturns206(const RangeResponse& r, int start, int end, long long totalBytes) {
    if (r.status != 206) {
        throw TestFailure("range status must be 206, got " + std::to_string(r.status));
    }
    if (r.acceptRanges != "bytes") {
        throw TestFailure("Accept-Ranges must be bytes");
    }
    const std::string expected = "bytes " + std::to_string(start) + "-" + std::to_string(end) +
                                 "/" + std::to_string(totalBytes);
    if (r.contentRange != expected) {
        throw TestFailure("Content-Range mismatch: " + r.contentRange);
    }
}

// D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
void testTagReadWriteRoundTrip(std::string_view file,
                               const std::map<std::string, std::string>& tagsOut,
                               const std::map<std::string, std::string>& expected) {
    writeTagsPseudocode(file, tagsOut);
    const auto tagsIn = readTagsPseudocode(file);
    for (const auto& [key, value] : expected) {
        const auto it = tagsIn.find(key);
        if (it == tagsIn.end() || it->second != value) {
            throw TestFailure("tag mismatch: " + key);
        }
    }
}
```

## §6 C#

`System.Security.Cryptography` SHA-256 (`CryptographicOperations.FixedTimeEquals`), `HttpClient` + `Range` başlığı ile 206 sözleşmesi, EBU R128 −23 LUFS, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```csharp
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

using System.Net.Http.Headers;
using System.Security.Cryptography;
using System.Text;

public sealed class TestFailureException(string message) : Exception(message);

public static class MediaTestingHarness
{
    // master §22.3 — doğrulanmış EBU R128 yayın hedefi.
    public const double EbuR128TargetLufs = -23.0;

    // TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
    public const double LoudnessToleranceLu = 1.0;

    public static string Sha256Hex(byte[] data) =>
        Convert.ToHexString(SHA256.HashData(data)).ToLowerInvariant(); // .NET std

    // A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
    public static void TestDecodeMatchesGoldenFixture(byte[] decodedPcm, string goldenSha256Hex)
    {
        var actual = Sha256Hex(decodedPcm);
        var equal = CryptographicOperations.FixedTimeEquals(
            Encoding.ASCII.GetBytes(actual),
            Encoding.ASCII.GetBytes(goldenSha256Hex));
        if (!equal)
            throw new TestFailureException($"decode hash mismatch: golden={goldenSha256Hex} actual={actual}");
    }

    // B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
    // MeasureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
    public static void TestLoudnessNearTargetLufs(
        double measuredLufs,
        double targetLufs = EbuR128TargetLufs,
        double toleranceLu = LoudnessToleranceLu)
    {
        if (Math.Abs(measuredLufs - targetLufs) > toleranceLu)
            throw new TestFailureException(
                $"loudness {measuredLufs} LUFS outside ±{toleranceLu} of {targetLufs}");
    }

    // C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
    // HttpClient .NET std'dir; istek taşıması K010-network seam'iyle değiştirilebilir (master §2.2).
    public static async Task TestRangeContractReturns206Async(
        HttpClient client, string url, long start, long end, long totalBytes,
        CancellationToken ct = default)
    {
        using var request = new HttpRequestMessage(HttpMethod.Get, url);
        request.Headers.Range = new RangeHeaderValue(start, end);
        using var response = await client.SendAsync(request, ct);

        if ((int)response.StatusCode != 206)
            throw new TestFailureException($"range status must be 206, got {(int)response.StatusCode}");
        if (!response.Headers.AcceptRanges.Contains("bytes"))
            throw new TestFailureException("Accept-Ranges must be bytes");

        var expected = $"bytes {start}-{end}/{totalBytes}";
        var actual = response.Content.Headers.ContentRange?.ToString();
        if (actual != expected)
            throw new TestFailureException($"Content-Range mismatch: expected {expected}, got {actual ?? "null"}");
    }

    // D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
    // WriteTags()/ReadTags(): etiket seam'i — pseudocode (ID3v2 · Vorbis Comment · ilst · LIST-INFO).
    public static void TestTagReadWriteRoundTrip(
        string file, IReadOnlyDictionary<string, string> tagsOut,
        IReadOnlyDictionary<string, string> expected)
    {
        WriteTags(file, tagsOut);
        var tagsIn = ReadTags(file);
        foreach (var (key, value) in expected)
        {
            if (!tagsIn.TryGetValue(key, out var actual) || actual != value)
                throw new TestFailureException($"tag {key} mismatch");
        }
    }

    private static void WriteTags(string file, IReadOnlyDictionary<string, string> tags) => throw new NotImplementedException();
    private static Dictionary<string, string> ReadTags(string file) => throw new NotImplementedException();
}
```

## §7 SQL

Golden fixture depo + regresyon sorguları: her sorgu **başarısızlığı SATIR olarak döner** (0 satır = PASS); tolerans `test_thresholds` tablosundan okunur ve Vault Steward kararı gelene kadar `NULL` = SKIP (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```sql
-- K014-media/media-testing — regression queries (TASARIM ÖNERİSİ).
-- Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10),
-- §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN), §22.2 (RFC 9110 Range),
-- §22.3 (EBU R128 −23 LUFS · etiket şemaları).
-- No repo code yet — illustrative design example (kademe-2 design node).
-- Sözleşme: her sorgu BAŞARISIZLIK satırları döndürür — 0 satır = PASS.

-- Depo (salt-okunur golden fixture + test eşikleri + koşu kayıtları).
CREATE TABLE media_fixtures (
    fixture_id     VARCHAR(64)  NOT NULL PRIMARY KEY,  -- ör. "flac-44k-golden-01"
    format         VARCHAR(16)  NOT NULL,              -- master §22.1: MP3 | FLAC | WAV
    golden_sha256  CHAR(64)     NOT NULL,              -- beklenen çözülmüş PCM hash'i
    fixture_bytes  BIGINT       NOT NULL
);

CREATE TABLE test_thresholds (
    threshold_key  VARCHAR(64)      NOT NULL PRIMARY KEY,
    value_lu       DECIMAL(6,2)     NULL  -- NULL = Vault Steward kararı BEKLİYOR (§15) -> SKIP
);

INSERT INTO test_thresholds (threshold_key, value_lu) VALUES ('loudness_tolerance_lu', NULL);

CREATE TABLE decode_runs (
    run_id         BIGINT       NOT NULL PRIMARY KEY,
    fixture_id     VARCHAR(64)  NOT NULL,
    pcm_sha256     CHAR(64)     NOT NULL,   -- çözüm sonucu hash
    measured_lufs  DECIMAL(6,2) NOT NULL
);

-- A) decode doğruluk: hash birebir değilse FAIL satırı (master §13/1).
SELECT f.fixture_id, r.pcm_sha256 AS actual, f.golden_sha256 AS expected
FROM media_fixtures f
JOIN decode_runs r ON r.fixture_id = f.fixture_id
WHERE r.pcm_sha256 <> f.golden_sha256;

-- B) loudness doğrulama: EBU R128 hedef −23 LUFS (master §22.3).
--    TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister (NULL -> SKIP).
SELECT r.run_id, r.measured_lufs, t.value_lu AS tolerance
FROM decode_runs r
CROSS JOIN test_thresholds t
WHERE t.threshold_key = 'loudness_tolerance_lu'
  AND t.value_lu IS NOT NULL
  AND ABS(r.measured_lufs - (-23.0)) > t.value_lu;

-- C) Range sözleşmesi: 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
CREATE TABLE range_responses (
    request_id     BIGINT       NOT NULL PRIMARY KEY,
    status         INT          NOT NULL,
    accept_ranges  VARCHAR(16)  NULL,
    content_range  VARCHAR(64)  NULL,     -- "bytes 1024-2047/5242880"
    range_start    BIGINT       NOT NULL,
    range_end      BIGINT       NOT NULL,
    file_bytes     BIGINT       NOT NULL
);

SELECT s.request_id, s.status, s.accept_ranges, s.content_range
FROM range_responses s
WHERE s.status <> 206
   OR s.accept_ranges <> 'bytes'
   OR s.content_range <> FORMAT('bytes %d-%d/%d', s.range_start, s.range_end, s.file_bytes);

-- D) etiket round-trip: yazılan değer okunan değerle eşit değilse FAIL (master §22.3).
CREATE TABLE tag_writes  (file_id VARCHAR(64) NOT NULL, tag_key VARCHAR(32) NOT NULL, tag_value VARCHAR(255) NOT NULL);
CREATE TABLE tag_reads   (file_id VARCHAR(64) NOT NULL, tag_key VARCHAR(32) NOT NULL, tag_value VARCHAR(255) NULL);

SELECT w.file_id, w.tag_key, w.tag_value AS written, rt.tag_value AS read_back
FROM tag_writes w
JOIN tag_reads rt ON rt.file_id = w.file_id AND rt.tag_key = w.tag_key
WHERE w.tag_value <> rt.tag_value;
```

## §8 Go

`crypto/sha256` + `net/http` + `testing` (hepsi Go stdlib), EBU R128 −23 LUFS, `Range` başlığı ile 206 sözleşmesi, etiket round-trip — decode/etiket seam'leri `TagStore` arayüzü ile işaretli (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```go
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

package mediatesting

import (
	"crypto/sha256"
	"encoding/hex"
	"fmt"
	"math"
	"net/http"
	"testing"
)

// EbuR128TargetLufs — master §22.3 doğrulanmış yayın hedefi.
const EbuR128TargetLufs = -23.0

// LoudnessToleranceLu — TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
const LoudnessToleranceLu = 1.0

// TestDecodeMatchesGoldenFixture — decode doğruluk (master §13/1):
// çözülmüş PCM hash'i golden fixture hash'iyle birebir.
func TestDecodeMatchesGoldenFixture(t *testing.T, decodedPcm []byte, goldenSha256Hex string) {
	t.Helper()
	sum := sha256.Sum256(decodedPcm) // Go stdlib
	actual := hex.EncodeToString(sum[:])
	if actual != goldenSha256Hex {
		t.Fatalf("decode hash mismatch: golden=%s actual=%s", goldenSha256Hex, actual)
	}
}

// TestLoudnessNearTargetLufs — EBU R128 hedef −23 LUFS (master §22.3).
// measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
func TestLoudnessNearTargetLufs(t *testing.T, measuredLufs float64) {
	t.Helper()
	if math.Abs(measuredLufs-EbuR128TargetLufs) > LoudnessToleranceLu {
		t.Fatalf("loudness %.2f LUFS outside ±%.2f of %.1f",
			measuredLufs, LoudnessToleranceLu, EbuR128TargetLufs)
	}
}

// TestRangeContractReturns206 — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
// client K010-network seam'iyle değiştirilebilir (master §2.2).
func TestRangeContractReturns206(t *testing.T, client *http.Client, url string, start, end int, totalBytes int64) {
	t.Helper()
	req, err := http.NewRequest(http.MethodGet, url, nil)
	if err != nil {
		t.Fatalf("request build: %v", err)
	}
	req.Header.Set("Range", fmt.Sprintf("bytes=%d-%d", start, end))

	resp, err := client.Do(req)
	if err != nil {
		t.Fatalf("request failed: %v", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusPartialContent {
		t.Fatalf("range status must be 206, got %d", resp.StatusCode)
	}
	if got := resp.Header.Get("Accept-Ranges"); got != "bytes" {
		t.Fatalf("Accept-Ranges must be bytes, got %q", got)
	}
	want := fmt.Sprintf("bytes %d-%d/%d", start, end, totalBytes)
	if got := resp.Header.Get("Content-Range"); got != want {
		t.Fatalf("Content-Range mismatch: want %q got %q", want, got)
	}
}

// TagStore — etiket seam'i (pseudocode): formatlar master §22.3
// (ID3v2 · Vorbis Comment · ilst · LIST-INFO).
type TagStore interface {
	Write(file string, tags map[string]string) error
	Read(file string) (map[string]string, error)
}

// TestTagReadWriteRoundTrip — yaz → oku → eşitlik (master §22.3).
func TestTagReadWriteRoundTrip(t *testing.T, store TagStore, file string, tagsOut, expected map[string]string) {
	t.Helper()
	if err := store.Write(file, tagsOut); err != nil {
		t.Fatalf("tag write: %v", err)
	}
	tagsIn, err := store.Read(file)
	if err != nil {
		t.Fatalf("tag read: %v", err)
	}
	for key, want := range expected {
		if got, ok := tagsIn[key]; !ok || got != want {
			t.Fatalf("tag %s mismatch: want %q got %q", key, want, got)
		}
	}
}
```

## §9 Rust

`Result`-tabanlı harness; Rust std'de SHA-256/HTTP/etiket YOKTUR — seam'ler `PSEUDOCODE SEAM` işaretli, uydurma crate adı YAZILMAZ (master §9.11 · §13/1 · §15 · §22.2 · §22.3 · araç §12/10 UNKNOWN).

```rust
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).
// NOTE: Rust standard library has NO SHA-256 / HTTP client / tag IO — no crate name
// is invented here (tool set UNKNOWN §12/10); seams are explicitly marked.

/// master §22.3 — doğrulanmış EBU R128 yayın hedefi.
pub const EBU_R128_TARGET_LUFS: f64 = -23.0;

// TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
pub const LOUDNESS_TOLERANCE_LU: f64 = 1.0;

// --- PSEUDOCODE SEAMs ---
fn sha256_hex_pseudocode(bytes: &[u8]) -> String;                    // decode hash
fn measure_loudness_lufs_pseudocode(decoded_pcm: &[u8]) -> f64;      // K003-audio seam (§2.2)
pub struct RangeResponse {                                            // K010-network seam (§2.2)
    pub status: u16,
    pub accept_ranges: String,
    pub content_range: String,
}
fn http_range_get_pseudocode(url: &str, start: u64, end: u64) -> RangeResponse;
fn write_tags_pseudocode(file: &str, tags: &[(&str, &str)]);          // etiket seam (§22.3)
fn read_tags_pseudocode(file: &str) -> Vec<(String, String)>;

/// A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
pub fn test_decode_matches_golden_fixture(
    decoded_pcm: &[u8],
    golden_sha256_hex: &str,
) -> Result<(), String> {
    let actual = sha256_hex_pseudocode(decoded_pcm);
    if actual != golden_sha256_hex {
        return Err(format!(
            "decode hash mismatch: golden={golden_sha256_hex} actual={actual}"
        ));
    }
    Ok(())
}

/// B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
pub fn test_loudness_near_target_lufs(measured_lufs: f64) -> Result<(), String> {
    if (measured_lufs - EBU_R128_TARGET_LUFS).abs() > LOUDNESS_TOLERANCE_LU {
        return Err(format!(
            "loudness {measured_lufs} LUFS outside ±{LOUDNESS_TOLERANCE_LU} of {EBU_R128_TARGET_LUFS}"
        ));
    }
    Ok(())
}

/// C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
pub fn test_range_contract_returns_206(
    r: &RangeResponse,
    start: u64,
    end: u64,
    total_bytes: u64,
) -> Result<(), String> {
    if r.status != 206 {
        return Err(format!("range status must be 206, got {}", r.status));
    }
    if r.accept_ranges != "bytes" {
        return Err(format!("Accept-Ranges must be bytes, got {}", r.accept_ranges));
    }
    let expected = format!("bytes {start}-{end}/{total_bytes}");
    if r.content_range != expected {
        return Err(format!(
            "Content-Range mismatch: expected {expected} got {}",
            r.content_range
        ));
    }
    Ok(())
}

/// D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
pub fn test_tag_read_write_round_trip(
    file: &str,
    tags_out: &[(&str, &str)],
    expected: &[(&str, &str)],
) -> Result<(), String> {
    write_tags_pseudocode(file, tags_out);
    let tags_in = read_tags_pseudocode(file);
    for (key, want) in expected {
        match tags_in.iter().find(|(k, _)| k == key) {
            Some((_, got)) if got == want => {}
            Some((_, got)) => return Err(format!("tag {key} mismatch: want {want} got {got}")),
            None => return Err(format!("tag {key} missing after round-trip")),
        }
    }
    Ok(())
}
```

## §10 Python

`hashlib` SHA-256 + `urllib.request` Range isteği (ikisi de std), EBU R128 −23 LUFS, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```python
# K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
# sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
# §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
# No repo code yet — illustrative design example (kademe-2 design node).

import hashlib
import urllib.request

# master §22.3 — doğrulanmış EBU R128 yayın hedefi.
EBU_R128_TARGET_LUFS = -23.0

# TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
LOUDNESS_TOLERANCE_LU = 1.0


class TestFailure(AssertionError):
    """Harness failure — medya testi başarısızlığı."""


def assert_equal(expected, actual, label):
    if expected != actual:
        raise TestFailure(f"{label}: expected {expected!r}, got {actual!r}")


def test_decode_matches_golden_fixture(decoded_pcm: bytes, golden_sha256_hex: str) -> None:
    """A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1)."""
    actual = hashlib.sha256(decoded_pcm).hexdigest()  # std
    if actual != golden_sha256_hex:
        raise TestFailure(f"decode hash mismatch: golden={golden_sha256_hex} actual={actual}")


def test_loudness_near_target_lufs(
    measured_lufs: float,
    target_lufs: float = EBU_R128_TARGET_LUFS,
    tolerance_lu: float = LOUDNESS_TOLERANCE_LU,
) -> None:
    """B) loudness — EBU R128 hedef −23 LUFS (master §22.3).

    measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
    """
    if abs(measured_lufs - target_lufs) > tolerance_lu:
        raise TestFailure(
            f"loudness {measured_lufs} LUFS outside ±{tolerance_lu} of {target_lufs}"
        )


def test_range_contract_returns_206(url: str, start: int, end: int, total_bytes: int) -> None:
    """C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · §22.2).

    urllib.request std'dir; taşıma K010-network seam'iyle değiştirilebilir (§2.2).
    """
    request = urllib.request.Request(url, headers={"Range": f"bytes={start}-{end}"})
    with urllib.request.urlopen(request) as response:  # fixture URL — test verisi
        status = response.status
        accept_ranges = response.headers.get("Accept-Ranges")
        content_range = response.headers.get("Content-Range")

    assert_equal(206, status, "range status")
    assert_equal("bytes", accept_ranges, "Accept-Ranges")
    assert_equal(f"bytes {start}-{end}/{total_bytes}", content_range, "Content-Range")


def write_tags(file: str, tags: dict) -> None:
    """etiket seam'i (pseudocode) — formatlar master §22.3."""


def read_tags(file: str) -> dict:
    """etiket seam'i (pseudocode) — formatlar master §22.3."""
    return {}


def test_tag_read_write_round_trip(file: str, tags_out: dict, expected: dict) -> None:
    """D) etiket round-trip — yaz → oku → eşitlik (master §22.3)."""
    write_tags(file, tags_out)
    tags_in = read_tags(file)
    for key, value in expected.items():
        assert_equal(value, tags_in.get(key), f"tag {key}")
```

## §11 Java

`MessageDigest` SHA-256 + `java.net.http.HttpClient` Range isteği (JDK std), EBU R128 −23 LUFS, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```java
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.security.GeneralSecurityException;
import java.security.MessageDigest;
import java.util.HexFormat;
import java.util.Map;

final class TestFailure extends RuntimeException {
    TestFailure(String message) { super(message); }
}

final class MediaTestingHarness {

    // master §22.3 — doğrulanmış EBU R128 yayın hedefi.
    static final double EBU_R128_TARGET_LUFS = -23.0;

    // TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
    static final double LOUDNESS_TOLERANCE_LU = 1.0;

    static String sha256Hex(byte[] data) {
        try {
            return HexFormat.of().formatHex(MessageDigest.getInstance("SHA-256").digest(data));
        } catch (GeneralSecurityException e) {
            throw new TestFailure("SHA-256 unavailable: " + e.getMessage());
        }
    }

    // A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
    static void testDecodeMatchesGoldenFixture(byte[] decodedPcm, String goldenSha256Hex) {
        String actual = sha256Hex(decodedPcm);
        if (!actual.equals(goldenSha256Hex)) {
            throw new TestFailure("decode hash mismatch: golden=" + goldenSha256Hex + " actual=" + actual);
        }
    }

    // B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
    // measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
    static void testLoudnessNearTargetLufs(double measuredLufs) {
        if (Math.abs(measuredLufs - EBU_R128_TARGET_LUFS) > LOUDNESS_TOLERANCE_LU) {
            throw new TestFailure("loudness " + measuredLufs + " LUFS outside tolerance of "
                    + EBU_R128_TARGET_LUFS);
        }
    }

    // C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
    // java.net.http JDK std'dir; taşıma K010-network seam'iyle değiştirilebilir (§2.2).
    static void testRangeContractReturns206(HttpClient client, String url,
                                            long start, long end, long totalBytes) throws Exception {
        HttpRequest request = HttpRequest.newBuilder(URI.create(url))
                .header("Range", "bytes=" + start + "-" + end)
                .GET()
                .build();
        HttpResponse<Void> response =
                client.send(request, HttpResponse.BodyHandlers.discarding());

        if (response.statusCode() != 206) {
            throw new TestFailure("range status must be 206, got " + response.statusCode());
        }
        String acceptRanges = response.headers().firstValue("Accept-Ranges").orElse(null);
        if (!"bytes".equals(acceptRanges)) {
            throw new TestFailure("Accept-Ranges must be bytes, got " + acceptRanges);
        }
        String expected = "bytes " + start + "-" + end + "/" + totalBytes;
        String contentRange = response.headers().firstValue("Content-Range").orElse(null);
        if (!expected.equals(contentRange)) {
            throw new TestFailure("Content-Range mismatch: expected " + expected + " got " + contentRange);
        }
    }

    // D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
    // writeTags()/readTags(): etiket seam'i — pseudocode (ID3v2 · Vorbis Comment · ilst · LIST-INFO).
    static void testTagReadWriteRoundTrip(String file, Map<String, String> tagsOut,
                                          Map<String, String> expected) {
        writeTags(file, tagsOut);
        Map<String, String> tagsIn = readTags(file);
        expected.forEach((key, value) -> {
            if (!value.equals(tagsIn.get(key))) {
                throw new TestFailure("tag " + key + " mismatch");
            }
        });
    }

    private static void writeTags(String file, Map<String, String> tags) { throw new UnsupportedOperationException("pseudocode seam"); }
    private static Map<String, String> readTags(String file) { throw new UnsupportedOperationException("pseudocode seam"); }
}
```

## §12 Swift

CryptoKit `SHA256` + `URLSession` Range isteği (Apple std framework'leri), EBU R128 −23 LUFS, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```swift
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

import CryptoKit // Apple std framework — hedef platformda kullanılabilirlik teyidi gerekir
import Foundation

struct TestFailure: Error, CustomStringConvertible {
    let description: String
}

enum MediaTestingHarness {

    // master §22.3 — doğrulanmış EBU R128 yayın hedefi.
    static let ebuR128TargetLufs: Double = -23.0

    // TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
    static let loudnessToleranceLu: Double = 1.0

    static func sha256Hex(_ data: Data) -> String {
        SHA256.hash(data: data).map { String(format: "%02x", $0) }.joined()
    }

    // A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
    static func testDecodeMatchesGoldenFixture(decodedPcm: Data, goldenSha256Hex: String) throws {
        let actual = sha256Hex(decodedPcm)
        if actual != goldenSha256Hex {
            throw TestFailure(description: "decode hash mismatch: golden=\(goldenSha256Hex) actual=\(actual)")
        }
    }

    // B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
    // measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
    static func testLoudnessNearTargetLufs(
        measuredLufs: Double,
        targetLufs: Double = ebuR128TargetLufs,
        toleranceLu: Double = loudnessToleranceLu
    ) throws {
        if abs(measuredLufs - targetLufs) > toleranceLu {
            throw TestFailure(description:
                "loudness \(measuredLufs) LUFS outside ±\(toleranceLu) of \(targetLufs)")
        }
    }

    // C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
    // URLSession Foundation std'dir; taşıma K010-network seam'iyle değiştirilebilir (§2.2).
    static func testRangeContractReturns206(
        session: URLSession = .shared,
        url: URL,
        start: Int,
        end: Int,
        totalBytes: Int
    ) async throws {
        var request = URLRequest(url: url)
        request.setValue("bytes=\(start)-\(end)", forHTTPHeaderField: "Range")

        let (_, response) = try await session.data(for: request)
        guard let http = response as? HTTPURLResponse else {
            throw TestFailure(description: "not an HTTP response")
        }
        guard http.statusCode == 206 else {
            throw TestFailure(description: "range status must be 206, got \(http.statusCode)")
        }
        guard http.value(forHTTPHeaderField: "Accept-Ranges") == "bytes" else {
            throw TestFailure(description: "Accept-Ranges must be bytes")
        }
        let expected = "bytes \(start)-\(end)/\(totalBytes)"
        guard http.value(forHTTPHeaderField: "Content-Range") == expected else {
            throw TestFailure(description:
                "Content-Range mismatch: expected \(expected) got "
                + "\(http.value(forHTTPHeaderField: "Content-Range") ?? "nil")")
        }
    }

    // D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
    // writeTags()/readTags(): etiket seam'i — pseudocode (ID3v2 · Vorbis Comment · ilst · LIST-INFO).
    static func testTagReadWriteRoundTrip(
        file: String,
        tagsOut: [String: String],
        expected: [String: String]
    ) throws {
        try writeTags(file: file, tags: tagsOut)
        let tagsIn = try readTags(file: file)
        for (key, value) in expected where tagsIn[key] != value {
            throw TestFailure(description: "tag \(key) mismatch")
        }
    }

    private static func writeTags(file: String, tags: [String: String]) throws {
        throw TestFailure(description: "pseudocode seam")
    }

    private static func readTags(file: String) throws -> [String: String] {
        throw TestFailure(description: "pseudocode seam")
    }
}
```

## §13 Kotlin

`MessageDigest` SHA-256 + `java.net.http.HttpClient` Range isteği (JVM std interop), EBU R128 −23 LUFS, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```kotlin
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

import java.net.URI
import java.net.http.HttpClient
import java.net.http.HttpRequest
import java.net.http.HttpResponse
import java.security.MessageDigest

class TestFailure(message: String) : RuntimeException(message)

object MediaTestingHarness {

    // master §22.3 — doğrulanmış EBU R128 yayın hedefi.
    const val EBU_R128_TARGET_LUFS: Double = -23.0

    // TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
    const val LOUDNESS_TOLERANCE_LU: Double = 1.0

    fun sha256Hex(bytes: ByteArray): String =
        MessageDigest.getInstance("SHA-256").digest(bytes).joinToString("") { "%02x".format(it) }

    // A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
    fun testDecodeMatchesGoldenFixture(decodedPcm: ByteArray, goldenSha256Hex: String) {
        val actual = sha256Hex(decodedPcm)
        require(actual == goldenSha256Hex) {
            "decode hash mismatch: golden=$goldenSha256Hex actual=$actual"
        }
        // require başarısız olursa IllegalArgumentException fırlatır — harness FAIL.
    }

    // B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
    // measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
    fun testLoudnessNearTargetLufs(
        measuredLufs: Double,
        targetLufs: Double = EBU_R128_TARGET_LUFS,
        toleranceLu: Double = LOUDNESS_TOLERANCE_LU,
    ) {
        if (kotlin.math.abs(measuredLufs - targetLufs) > toleranceLu) {
            throw TestFailure(
                "loudness $measuredLufs LUFS outside ±$toleranceLu of $targetLufs"
            )
        }
    }

    // C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
    // java.net.http JVM std interop; taşıma K010-network seam'iyle değiştirilebilir (§2.2).
    fun testRangeContractReturns206(
        client: HttpClient,
        url: String,
        start: Long,
        end: Long,
        totalBytes: Long,
    ) {
        val request = HttpRequest.newBuilder(URI.create(url))
            .header("Range", "bytes=$start-$end")
            .GET()
            .build()
        val response = client.send(request, HttpResponse.BodyHandlers.discarding())

        if (response.statusCode() != 206) {
            throw TestFailure("range status must be 206, got ${response.statusCode()}")
        }
        val acceptRanges = response.headers().firstValue("Accept-Ranges").orElse(null)
        if (acceptRanges != "bytes") {
            throw TestFailure("Accept-Ranges must be bytes, got $acceptRanges")
        }
        val expected = "bytes $start-$end/$totalBytes"
        val contentRange = response.headers().firstValue("Content-Range").orElse(null)
        if (contentRange != expected) {
            throw TestFailure("Content-Range mismatch: expected $expected got $contentRange")
        }
    }

    // D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
    // writeTags()/readTags(): etiket seam'i — pseudocode (ID3v2 · Vorbis Comment · ilst · LIST-INFO).
    fun testTagReadWriteRoundTrip(
        file: String,
        tagsOut: Map<String, String>,
        expected: Map<String, String>,
    ) {
        writeTags(file, tagsOut)
        val tagsIn = readTags(file)
        for ((key, value) in expected) {
            if (tagsIn[key] != value) throw TestFailure("tag $key mismatch")
        }
    }

    private fun writeTags(file: String, tags: Map<String, String>): Unit =
        throw TestFailure("pseudocode seam")

    private fun readTags(file: String): Map<String, String> =
        throw TestFailure("pseudocode seam")
}
```

## §14 Ruby

`Digest::SHA256` + `Net::HTTP` (her ikisi de Ruby stdlib), EBU R128 −23 LUFS, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```ruby
# K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
# sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
# §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
# No repo code yet — illustrative design example (kademe-2 design node).

require 'digest'    # Ruby stdlib
require 'net/http'  # Ruby stdlib
require 'uri'

module MediaTestingHarness
  # master §22.3 — doğrulanmış EBU R128 yayın hedefi.
  EBU_R128_TARGET_LUFS = -23.0

  # TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
  LOUDNESS_TOLERANCE_LU = 1.0

  class TestFailure < StandardError; end

  # A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
  def self.test_decode_matches_golden_fixture(decoded_pcm, golden_sha256_hex)
    actual = Digest::SHA256.hexdigest(decoded_pcm)
    return if actual == golden_sha256_hex

    raise TestFailure, "decode hash mismatch: golden=#{golden_sha256_hex} actual=#{actual}"
  end

  # B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
  # measure_loudness_lufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
  def self.test_loudness_near_target_lufs(measured_lufs,
                                          target_lufs: EBU_R128_TARGET_LUFS,
                                          tolerance_lu: LOUDNESS_TOLERANCE_LU)
    return if (measured_lufs - target_lufs).abs <= tolerance_lu

    raise TestFailure,
          "loudness #{measured_lufs} LUFS outside ±#{tolerance_lu} of #{target_lufs}"
  end

  # C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
  # Net::HTTP stdlib; taşıma K010-network seam'iyle değiştirilebilir (§2.2).
  def self.test_range_contract_returns_206(url, start, finish, total_bytes)
    uri = URI(url)
    request = Net::HTTP::Get.new(uri)
    request['Range'] = "bytes=#{start}-#{finish}"

    response = Net::HTTP.start(uri.hostname, uri.port, use_ssl: uri.scheme == 'https') do |http|
      http.request(request)
    end

    raise TestFailure, "range status must be 206, got #{response.code}" unless response.code == '206'
    raise TestFailure, "Accept-Ranges must be bytes" unless response['Accept-Ranges'] == 'bytes'

    expected = "bytes #{start}-#{finish}/#{total_bytes}"
    actual = response['Content-Range']
    raise TestFailure, "Content-Range mismatch: expected #{expected} got #{actual}" unless actual == expected
  end

  # write_tags/read_tags: etiket seam'i — pseudocode (master §22.3:
  # ID3v2 · Vorbis Comment · ilst · LIST-INFO).
  def self.write_tags(file, tags); end
  def self.read_tags(file) = {}

  # D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
  def self.test_tag_read_write_round_trip(file, tags_out, expected)
    write_tags(file, tags_out)
    tags_in = read_tags(file)
    expected.each do |key, value|
      raise TestFailure, "tag #{key} mismatch" unless tags_in[key] == value
    end
  end
end
```

## §15 Elixir

`:crypto.hash(:sha256, …)` (OTP std), EBU R128 −23 LUFS, Range 206 sözleşmesi, etiket round-trip — Elixir/OTP'de std HTTP client/etiket IO YOKTUR; seam'ler açıkça pseudocode işaretlidir (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```elixir
# K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
# Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
# sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
# §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
# No repo code yet — illustrative design example (kademe-2 design node).
# NOTE: Elixir/OTP has NO std HTTP client or tag IO — no package name is invented
# here (tool set UNKNOWN §12/10); seams are explicitly marked.

defmodule MediaTestingHarness do
  @moduledoc """
  Decode doğruluk (golden fixture hash) · EBU R128 loudness · HTTP Range
  sözleşmesi · etiket round-trip harness'ı — TASARIM ÖNERİSİ.
  """

  defmodule TestFailure do
    defexception [:message]
  end

  # master §22.3 — doğrulanmış EBU R128 yayın hedefi.
  @ebu_r128_target_lufs -23.0

  # TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
  @loudness_tolerance_lu 1.0

  # A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
  def test_decode_matches_golden_fixture(decoded_pcm, golden_sha256_hex) do
    actual = :crypto.hash(:sha256, decoded_pcm) |> Base.encode16(case: :lower) # OTP std
    if actual != golden_sha256_hex do
      raise TestFailure, "decode hash mismatch: golden=#{golden_sha256_hex} actual=#{actual}"
    end

    :ok
  end

  # B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
  # measure_loudness_lufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
  def test_loudness_near_target_lufs(measured_lufs,
                                     target_lufs \\ @ebu_r128_target_lufs,
                                     tolerance_lu \\ @loudness_tolerance_lu) do
    if abs(measured_lufs - target_lufs) > tolerance_lu do
      raise TestFailure,
            "loudness #{measured_lufs} LUFS outside ±#{tolerance_lu} of #{target_lufs}"
    end

    :ok
  end

  # C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
  # PSEUDOCODE SEAM: http_range_get/3 → %{status: 206, accept_ranges: "bytes",
  # content_range: "bytes 1024-2047/5242880"} — taşıma K010-network (§2.2).
  def test_range_contract_returns_206(%{status: status, accept_ranges: ar, content_range: cr},
                                      start,
                                      end_,
                                      total_bytes) do
    expected = "bytes #{start}-#{end_}/#{total_bytes}"

    cond do
      status != 206 -> raise TestFailure, "range status must be 206, got #{status}"
      ar != "bytes" -> raise TestFailure, "Accept-Ranges must be bytes, got #{inspect(ar)}"
      cr != expected -> raise TestFailure, "Content-Range mismatch: expected #{expected} got #{inspect(cr)}"
      true -> :ok
    end
  end

  # write_tags/read_tags: etiket seam'i — pseudocode (master §22.3:
  # ID3v2 · Vorbis Comment · ilst · LIST-INFO).
  defp write_tags(_file, _tags), do: :ok
  defp read_tags(_file), do: %{}

  # D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
  def test_tag_read_write_round_trip(file, tags_out, expected) do
    :ok = write_tags(file, tags_out)
    tags_in = read_tags(file)

    Enum.each(expected, fn {key, value} ->
      if tags_in[key] != value do
        raise TestFailure, "tag #{key} mismatch"
      end
    end)

    :ok
  end
end
```

## §16 Scala

`MessageDigest` + `java.net.http` JVM std interop, EBU R128 −23 LUFS, Range 206 sözleşmesi, etiket round-trip — decode/etiket seam'leri pseudocode (master §9.11 · §13/1 · §15 · §22.2 · §22.3).

```scala
// K014-media/media-testing — test harness (TASARIM ÖNERİSİ).
// Grounded in media-master.md §9.11 (medya testleri · araç ⚠️ UNKNOWN §12/10 ·
// sahiplik §7/4 ⚠️), §13/1 (decode doğruluk), §15 (eşik ⚠️ UNKNOWN),
// §22.2 (RFC 9110 Range), §22.3 (EBU R128 −23 LUFS · etiket şemaları).
// No repo code yet — illustrative design example (kademe-2 design node).

import java.net.URI
import java.net.http.{HttpClient, HttpRequest, HttpResponse}
import java.security.MessageDigest

object MediaTestingHarness {

  final class TestFailure(message: String) extends RuntimeException(message)

  // master §22.3 — doğrulanmış EBU R128 yayın hedefi.
  val ebuR128TargetLufs: Double = -23.0

  // TASARIM ÖNERİSİ — eşik §15/UNKNOWN, Vault Steward onayı ister.
  val loudnessToleranceLu: Double = 1.0

  def sha256Hex(bytes: Array[Byte]): String =
    MessageDigest.getInstance("SHA-256") // JVM std interop
      .digest(bytes)
      .map(b => f"${b & 0xff}%02x")
      .mkString

  // A) decode doğruluk — çözülmüş PCM hash'i → golden fixture (master §13/1).
  def testDecodeMatchesGoldenFixture(decodedPcm: Array[Byte], goldenSha256Hex: String): Unit = {
    val actual = sha256Hex(decodedPcm)
    if (actual != goldenSha256Hex)
      throw new TestFailure(s"decode hash mismatch: golden=$goldenSha256Hex actual=$actual")
  }

  // B) loudness — EBU R128 hedef −23 LUFS (master §22.3).
  // measureLoudnessLufs(): pseudocode seam — araç seti §12/10 UNKNOWN.
  def testLoudnessNearTargetLufs(
      measuredLufs: Double,
      targetLufs: Double = ebuR128TargetLufs,
      toleranceLu: Double = loudnessToleranceLu): Unit = {
    if (math.abs(measuredLufs - targetLufs) > toleranceLu)
      throw new TestFailure(
        f"loudness $measuredLufs%.2f LUFS outside ±$toleranceLu%.2f of $targetLufs%.1f")
  }

  // C) Range sözleşmesi — 206 + Content-Range + Accept-Ranges (RFC 9110 · master §22.2).
  // java.net.http (JDK 11+) std interop; taşıma K010-network seam'iyle değiştirilebilir (§2.2).
  def testRangeContractReturns206(
      client: HttpClient,
      url: String,
      start: Long,
      end: Long,
      totalBytes: Long): Unit = {
    val request = HttpRequest
      .newBuilder(URI.create(url))
      .header("Range", s"bytes=$start-$end")
      .GET()
      .build()
    val response = client.send(request, HttpResponse.BodyHandlers.discarding())

    if (response.statusCode() != 206)
      throw new TestFailure(s"range status must be 206, got ${response.statusCode()}")

    val acceptRanges = response.headers().firstValue("Accept-Ranges").orElse(null)
    if (acceptRanges != "bytes")
      throw new TestFailure(s"Accept-Ranges must be bytes, got $acceptRanges")

    val expected  = s"bytes $start-$end/$totalBytes"
    val contentRange = response.headers().firstValue("Content-Range").orElse(null)
    if (contentRange != expected)
      throw new TestFailure(s"Content-Range mismatch: expected $expected got $contentRange")
  }

  // writeTags/readTags: etiket seam'i — pseudocode (master §22.3:
  // ID3v2 · Vorbis Comment · ilst · LIST-INFO).
  private def writeTags(file: String, tags: Map[String, String]): Unit =
    throw new TestFailure("pseudocode seam")

  private def readTags(file: String): Map[String, String] =
    throw new TestFailure("pseudocode seam")

  // D) etiket round-trip — yaz → oku → eşitlik (master §22.3).
  def testTagReadWriteRoundTrip(
      file: String,
      tagsOut: Map[String, String],
      expected: Map[String, String]): Unit = {
    writeTags(file, tagsOut)
    val tagsIn = readTags(file)
    expected.foreach { case (key, value) =>
      if (tagsIn.get(key) != Some(value))
        throw new TestFailure(s"tag $key mismatch")
    }
  }
}
```

## §17 Kaynaklar

| # | Kaynak | Yer | Kullanım |
|---|---|---|---|
| 1 | K014-media/media-master.md §9.11 | vault | media-testing rol/sınır (sahiplik ⚠️ §7/4 · araç ⚠️ UNKNOWN) |
| 2 | K014-media/media-master.md §13/1 · §13/2 | vault | decode doğruluk + akış kalitesi test senaryoları |
| 3 | K014-media/media-master.md §15 | vault | ölçüm protokolü — eşikler ⚠️ UNKNOWN (Vault Steward) |
| 4 | K014-media/media-master.md §22.1 | vault | codec/format seti MP3 + FLAC + WAV (set seçimi TASARIM ÖNERİSİ — Steward onayı ister) |
| 5 | K014-media/media-master.md §22.2 | vault | HTTP Range RFC 9110 (STD 97) · HLS RFC 8216 · DASH ISO/IEC 23009-1:2026 |
| 6 | K014-media/media-master.md §22.3 | vault | EBU R128 −23 LUFS · ReplayGain 2.0 −18 LUFS · etiket şemaları (ID3v2 · Vorbis Comment · ilst · LIST-INFO) |
| 7 | K014-media/media-master.md §22.5 | vault | `ETag` + `Cache-Control: immutable` (indirme/önbellek testi) |
| 8 | K014-media/media-master.md §12/10 · §22.6 | vault | test araç seti KAPANMADI ("⏳ AÇIK") — araç adı yazılmaz |
| 9 | K014-media/media-master.md §7/4 | vault | media-testing ↔ K019-testing sahipliği ⚠️ VERIFICATION REQUIRED |
| 10 | K014-media/media-testing/index.md | vault | bu modülün künyesi (§ROL/§KAPSAM/§BİLEŞENLER/§STANDARTLAR) |
| 11 | K014-media/synchronization/ornekler.md | vault | format referansı (15 dil · TASARIM ÖNERİSİ deseni) |

> Tüm kod blokları TASARIM ÖNERİSİ illustrative design örnekleridir (kademe-2 design node —
> repo'da henüz kodu YOK); dayanak: media-master §9.11 + §13 + §22. **Test aracı seti ve
> ölçüm eşikleri seçilmemiştir** — araç kararı K019 incelemesi + Vault Steward (§12/10 ·
> §15 · §22.6 "⏳ AÇIK"), eşik kararı Vault Steward (§15) bekliyor.

**Authority:** Bayram Ali / Vault Steward
