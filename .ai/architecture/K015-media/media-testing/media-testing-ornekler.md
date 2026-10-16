---
node_id: "K015-media/media-testing/media-testing-ornekler"
node_type: "module"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "media-testing-kapsam.md §3-§5 (durum makinesi + hata akışı — iskeletler bu akışa birebir bağlanır)"
  - "media-testing-web-arastirma.md §2 C-1..C-4 (decode doğrulama, toleranslı karşılaştırma, sabitlenmiş araç)"
  - ".ai/AGENTS.md §25.2 (QA stack: PHPUnit ^10.5/^11.0 require-dev kanıtı, shared/tests/ 22 dosya)"
  - "media-testing-web-arastirma.md §1 kaynak 1 (ffprobe: pozitif çıkış kodu, JSON writer) · kaynak 6 (ffmpeg tam decode)"
validation: "TASARIM İDDİASI, üretim kodu değildir — iskeletler 2026-10-16'da yazılmıştır; diskte karşılık gelen test dosyası/CI işi yoktur; tolerans eşikleri numaralı DEĞİL örnek amaçlıdır (kapsam Q-3 UNKNOWN)"
open_questions:
  - "Tolerans eşikleri (süre ms vb.) → kapsam Q-3 — iskeletteki eşikler örnek amaçlıdır, bağlayıcı değil"
  - "Araç seti (ffprobe/ffmpeg sürüm pin'i) → kapsam Q-1 — iskelet araç sürümü SABİTLEMEZ"
  - "§7/4 sahiplik — bu iskelet K019 sınırlarını DAHA AZ kapsar; ortak altyapı kararı açık"
group: kod
---

# K015 · media-testing · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · İskelet Kod (decode doğrulama · toleranslı süre karşılaştırması · bash/ffmpeg)
>
> **⚠️ TASARIM İDDİASI, üretim kodu değildir.** Aşağıdaki kod blokları tasarım iskeletidir; CoreMusic
> repo'sunda karşılık gelen test dosyası / betik **yoktur** (yazım yasağı: yalnız `.md` üretilir).

---

## §1 AMAÇ TABLOSU

| # | Örnek | Dil/Teknoloji | Ne gösterir | Bağ |
|---|---|---|---|---|
| Ör-01 | Decode doğrulama + toleranslı süre karşılaştırması | PHP 8.4 · PHPUnit ^10.5 | ffprobe JSON çağrısı (shell'siz), çıkış kodu kapısı, süre sapması toleransı, alan-bazlı hata raporu | kapsam §3 `draft→running→passed/failed` · §4 adım 2-4 · §5 T-2/T-3/T-4 |
| Ör-02 | Decode sweep + süre raporu | bash · ffprobe/ffmpeg | dizindeki her dosyayı decode eder, çıkış kodunu ve süreyi toplar; başarısızları listeler | kapsam §4-2 · §5 T-1/T-2 · web-arastirma C-1 (tam decode) |

**Ortak kurallar (TASARIM İDDİASI):** (a) araç çağrısı **shell'siz** dizi komutu (PHP `proc_open(array)`) ·
(b) beklenen değer ve tolerans **test verisidir**, sistem eşiği değildir (kapsam Q-3 UNKNOWN) ·
(c) araç yokluğu `skipped`/`T-1` ile işaretlenir, **sessiz "geçti" sayılmaz** · (d) çıktı uydurulmaz —
ham ölçümler kayda yazılır.

---

## §2 ÖR-01 — PHP 8.4 · PHPUnit ^10.5 · Decode Doğrulama + Toleranslı Süre

**Amac:** (1) ffprobe ile dosyayı/probe et, (2) araç yoksa testi **atla** (`skipped` — T-1, "geç" DEĞİL),
(3) çıkış kodu ≠ 0 ise **fail** (T-2), (4) ölçülen süreyi beklenenle **tolerans içinde** karşılaştır (T-3),
(5) codec uyuşmazlığını alan adıyla raporla (T-4).

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * TASARIM İDDİASI — üretim testi değildir.
 * Kapsam §3 (durum makinesi) + §4 (adım 2-4) + §5 (T-1..T-4) iskeleti.
 * Araç sürümü SABİTLENMEZ (kapsam Q-1 UNKNOWN); eşikler örnek amaçlı (Q-3 UNKNOWN).
 */
final class MediaDecodeTest extends TestCase
{
    /** Örnek eşik — bağlayıcı DEĞİL, kapsam Q-3 kararıyla değişir. */
    private const DURATION_TOLERANCE_MS = 250;

    /** @return array<string, array{file: string, expectedDurationMs: int, codec: string}> */
    public static function fixtureProvider(): array
    {
        return [
            '440hz-tone' => [
                'file' => __DIR__ . '/fixtures/tone-440.wav',
                'expectedDurationMs' => 1000,
                'codec' => 'pcm_s16le',
            ],
        ];
    }

    #[DataProvider('fixtureProvider')]
    public function testDecodeSucceedsAndDurationWithinTolerance(
        string $file,
        int $expectedDurationMs,
        string $codec,
    ): void {
        if (!is_file($file)) {
            // T-6: fixture yok → uydurulmaz, atlanır (media-testing-kapsam §5)
            self::markTestSkipped("fixture eksik: {$file}");
        }

        $probe = $this->ffprobe($file);
        if ($probe === null) {
            // T-1: araç yok/koşul yok → "geç" sayılmaz, atlanır (araç seti Q-1 UNKNOWN)
            self::markTestSkipped('ffprobe çalıştırılamadı — araç seti kararı bekleniyor (Q-1)');
        }

        ['exitCode' => $exitCode, 'json' => $json] = $probe;

        // T-2: çıkış kodu ≠ 0 → decode/probe hatası (ffprobe: pozitif çıkış kodu — web-arastirma kaynak 1)
        self::assertSame(
            0,
            $exitCode,
            'ffprobe başarısız — dosya bozuk veya tanınmadı (T-2), ilk hata: ' . ($json['error'] ?? 'bilinmiyor')
        );

        $stream = $json['streams'][0] ?? null;
        self::assertIsArray($stream, 'stream yok (T-4)');

        // T-4: alan bazlı uyuşmazlık — adıyla raporlanır
        self::assertSame(
            $codec,
            (string) ($stream['codec_name'] ?? ''),
            'codec uyuşmazlığı (T-4)'
        );

        $durationSec = (float) ($json['format']['duration'] ?? -1.0);
        self::assertGreaterThan(0.0, $durationSec, 'süre okunamadı (T-4/T-8 — bilinmeyen alan uydurulmaz)');

        $actualMs = (int) round($durationSec * 1000);
        $deltaMs = abs($expectedDurationMs - $actualMs);

        // T-3: toleranslı karşılaştırma — byte-eşitlik YOK (web-arastirma C-2)
        self::assertLessThanOrEqual(
            self::DURATION_TOLERANCE_MS,
            $deltaMs,
            sprintf(
                'süre tolerans dışı (T-3): beklenen %d ms, ölçülen %d ms, sapma %d ms, eşik %d ms',
                $expectedDurationMs,
                $actualMs,
                $deltaMs,
                self::DURATION_TOLERANCE_MS,
            )
        );
    }

    /**
     * Shell'siz ffprobe çağrısı (dizi komut — kaçış sorunu yok).
     * Bozuk JSON / araç yokluğu: exitCode !== 0 veya json = null olarak döner.
     *
     * @return array{exitCode: int, json: array<string, mixed>} | null
     */
    private function ffprobe(string $file): ?array
    {
        $cmd = [
            'ffprobe',
            '-v', 'error',
            '-show_format',
            '-show_streams',
            '-of', 'json',
            $file,
        ];

        $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
        if ($proc === false) {
            return null;                                  // T-1: araç yok
        }

        $stdout = (string) stream_get_contents($pipes[1]);
        (string) stream_get_contents($pipes[2]);          // stderr: kayıt dışında bırakılmaz, aşağıda taşınır
        $exitCode = proc_close($proc);

        $json = json_decode($stdout, true);
        if (!is_array($json)) {
            return ['exitCode' => $exitCode === 0 ? 1 : $exitCode, 'json' => []]; // T-8: bozuk çıktı → uydurma yok
        }

        return ['exitCode' => $exitCode, 'json' => $json];
    }
}
```

**Kapsam bağları:**

| Satır/dal | Kapsam karşılığı |
|---|---|
| `markTestSkipped('ffprobe çalıştırılamadı')` | §5 T-1 — araç yokluğu **`skipped`**, "geç" değil; araç seti Q-1 UNKNOWN |
| `$exitCode !== 0` → `assertSame(0, ...)` | §5 T-2 — decode/probe hatası (web-arastirma kaynak 1: pozitif çıkış kodu) |
| `abs(...) <= DURATION_TOLERANCE_MS` | §5 T-3 — toleranslı karşılaştırma; eşik **örnek amaçlı** (Q-3) |
| `assertSame($codec, ...)` | §5 T-4 — alan bazlı rapor (ad + iki değer) |
| `json_decode(...) === null → exitCode 1` | §5 T-8 — bozuk/eksik çıktı uydurulmaz |
| `proc_open($cmd)` dizi formu | shell'siz çağrı — kaçış/enjeksiyon yüzeyi yok |
| `markTestSkipped(fixture eksik)` | §5 T-6 — fixture yoksa üretim testi **üretilmez** |

**Durum:** ⚠️ İskelet — fixture/psalm/vitest bağımlılıkları diskte yok; PHPUnit ^10.5 require-dev kanıtı `.ai/AGENTS.md` §25.2 (shared/tests/ 22 dosya).

---

## §3 ÖR-02 — bash · ffprobe/ffmpeg · Decode Sweep İskeleti

**Amac:** bir dizindeki her medya dosyasını (1) ffprobe ile tanı, (2) ffmpeg ile **tam decode** et,
(3) çıkış kodu ve süreyi topla, (4) başarısızları raporla — CI/çevrimdışı toplu denetim için.

```bash
#!/usr/bin/env bash
# TASARIM İDDİASI — üretim betiği değildir.
# Kapsam §4 adım 2 · §5 T-1/T-2 + web-arastirma C-1 (tam decode) iskeleti.
# Araç sürümü sabitlenmez (kapsam Q-1 UNKNOWN).
set -euo pipefail

MEDIA_DIR="${1:-.}"
TOLERANCE_MS="${TOLERANCE_MS:-250}"   # örnek eşik — bağlayıcı değil (Q-3)

# T-1: araç yok → "geç" değil, açık hata
for tool in ffprobe ffmpeg; do
  if ! command -v "$tool" >/dev/null 2>&1; then
    echo "T-1 EKSIK ARAC: $tool — araç seti kararı bekleniyor (Q-1)" >&2
    exit 2
  fi
done

fail=0
scanned=0

shopt -s nullglob
for f in "$MEDIA_DIR"/*.{flac,mp3,wav,ogg,m4a}; do
  scanned=$((scanned + 1))

  # 1) tanıma/probe (ffprobe: tanınmayan girdide pozitif çıkış kodu — web-arastirma kaynak 1)
  if ! probe_json="$(ffprobe -v error -show_format -show_streams -of json "$f" 2>&1)"; then
    echo "T-2 FAIL(probe): $f :: ${probe_json}" >&2
    fail=$((fail + 1))
    continue
  fi

  # 2) tam decode — "yarıda kesilen" dosyayı da yakalar (BeatsCheck yaklaşımı — kaynak 6)
  if ! ffmpeg -v error -i "$f" -f null - >/dev/null 2>&1; then
    echo "T-2 FAIL(decode): $f" >&2
    fail=$((fail + 1))
    continue
  fi

  # 3) süre raporu — tolerans kapısı üretim eşiği KURGUSUdur (bu iskelette beklenen dosya listesi yok;
  #    gerçek testte beklenen süre bir manifestten gelir → T-3 kapısı Ör-01'de gösterilir)
  dur_ms="$(ffprobe -v error -show_entries format=duration -of default=nw=1:nk=1 "$f" \
    | awk '{printf "%d", $1 * 1000}')"
  echo "OK: $f :: ${dur_ms} ms (tolerans eşiği: ${TOLERANCE_MS} ms — manifest beklenir, Q-3)"
done

echo "taranan: $scanned · basarisiz: $fail"
if (( scanned == 0 )); then
  echo "T-6 UYARI: dosya yok — fixture/dizin eksik, sonuc 'geç' sayılmaz" >&2
  exit 3
fi

exit "$(( fail > 0 ? 1 : 0 ))"
```

**Kapsam bağları:**

| Satır | Kapsam karşılığı |
|---|---|
| `command -v ffprobe/ffmpeg` → `exit 2` | §5 T-1 — araç yokluğu ayrı çıkış kodu (2), "geç" değil |
| `ffprobe ... 2>&1` + `FAIL(probe)` | §5 T-2 — ilk hata satırıyla rapor |
| `ffmpeg -f null -` tam decode | web-arastirma C-1 (kaynak 6: tam decode, "stop short" tespiti) |
| `dur_ms` + `TOLERANCE_MS` | §5 T-3 — eşik ortam değişkeni ile **örnek**; gerçek manifest beklenir (Q-3) |
| `scanned == 0` → `exit 3` | §5 T-6 — boş dizin atlanmaz, açık uyarı |
| sürüm pin'i YOK | §6-Q1 — betik sürüm **seçmez** |

**Durum:** ⚠️ İskelet — manifest/beklenti listesi diskte yok; `TOLERANCE_MS` yalnız örnek.

---

## §4 ÖRNEK ÖZETİ

| # | Örnek | Dil | Gösterdiği kontrol | Durum |
|---|---|---|---|---|
| Ör-01 | Decode + toleranslı süre | PHP 8.4 / PHPUnit ^10.5 | shell'siz ffprobe · çıkış kodu · süre toleransı · alan bazlı hata · skipped (T-1/T-6) | ⚠️ İskelet (fixture YOK) |
| Ör-02 | Decode sweep | bash / ffprobe+ffmpeg | araç kapısı · probe+decode · süre raporu · boş-dizin uyarısı | ⚠️ İskelet (manifest YOK) |

**Kapsanmayanlar** (kayıt yok = kanıt yok): PSNR/SSIM/LUFS ölçüm kapısı (eşik Q-3 + kaynak 5 tek-site) ·
Playwright E2E ses senaryosu (K019/§7/4 sınırı) · P.863 algısal kalite ölçümü (kapsam Q-6) ·
CI pipeline tanımı (DevOps yüzeyi) — dördü de açık sorudur, iskelette **seçilmez**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
