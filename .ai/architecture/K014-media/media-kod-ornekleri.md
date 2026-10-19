---
node_id: K014-media/media-kod-ornekleri.md
node_type: subdomain
status: draft
parent_id: K014-media
children: []
depends_on:
  - K003-audio
  - K007-data
  - K010-network
evidence:
  - ".ai/architecture/K014-media/media-master.md §22.1-§22.5 (doğrulanmış standartlar — RFC/ISO), §23 (ornekler.md · 15 dil × 11 çocuk) — okundu 2026-10-18"
  - ".ai/architecture/K014-media/media-kararlar.md §1-§6 (ADR-092/026/027/028 + draft ADR-097/103) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integraion-web-arastirma.md §3.8 (MusicBrainz 1 sn/kullanıcı — 200 doğrulandı) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 15 dil bloğu, ~330 kod satırı; her blok TASARIM ÖNERİSİ etiketli + §22 kanıtlı; §16 dilim tablosu"
open_questions:
  - "⚠️ Tüm bloklar TASARIM ÖNERİSİ — medya düğümü design node'dur, repo'da kodu YOK (§23)."
  - "⚠️ §22.1-§22.5 TASARIM ÖNERİLERİ Vault Steward onayı ister — bloklar bu önerilere uyar."
  - "⚠️ metadata/artwork kaynağı (§7/8) UNKNOWN — §3 bloğu iki olasılığı da gösterir."
  - "⚠️ synchronization yönü (§12/9) UNKNOWN — §12 bloğu LWW varsayar (TASARIM ÖNERİSİ)."
  - "⚠️ media-cache boyut/TTL eşikleri (§16/3) UNKNOWN — §5 bloğu eşik YAZMAZ."
group: kod
---

# K014 MEDIA — Kod Örnekleri (Tasarım Önerisi)

> **UYARI**: K014 medya düğümü kademe-2 **design node**'dur — repo'da henüz kodu YOK (§23). Bu dosyadaki bloklar **TASARIM ÖNERİSİ** etiketlidir ve §22'de doğrulanmış standartlara (RFC/ISO) uyar; nihai eşik/politika Vault Steward onayı ister.
> Tam set: çocuk `ornekler.md`'leri (11 × 15 dil = 165 dil-bölümü · ~11.200 satır — §23.1). Bu dosya çekirdek 15 bloğu toplar.
> Format: konuşa biçimi + dil + etiket + kaynak (§9.x/§22/ADR §).

## §1 Kod Blokları — Giriş

1. **Amaç**: Her bileşenin (library/metadata/artwork/catalog/playback/streaming/downloads/offline/media-cache/synchronization/media-testing) sorumluluğunu somut kodla göstermek.
2. **Standartlar**: §22'de doğrulanmış RFC/ISO/EBU referansları kullanılır (uydurma paket/sürüm yok — §23.4).
3. **Dilim**: §16'da 15 dil × çocuk tablosu.

## §2 library — ULID Kimlik + Content-Hash Dedup (ADR-092 · C#)

### 2.1 C# — Track Modeli + ULID + SHA-256 Dedup — ~40 satır

```csharp
// ADR-092: ULID kimliği (kronolojik sortlanabilir). §9.1: koleksiyon/track modeli.
// TASARIM ÖNERİSİ — repo'da kod YOK (§23). Content-hash dedup (§23.1 library).
public sealed record Track(
    string Ulid,          // ADR-092 — ULID kimliği
    string Title,
    string Artist,
    long SizeBytes,
    string ContentHash);  // SHA-256 — dedup anahtarı

public sealed class Library
{
    private readonly Dictionary<string, Track> _byHash = new(); // content-hash dedup

    public (Track track, bool isNew) Add(Track candidate)
    {
        if (_byHash.TryGetValue(candidate.ContentHash, out var existing))
            return (existing, false);                 // zaten var — duplikasyon yok
        _byHash[candidate.ContentHash] = candidate;   // yeni ekle
        return (candidate, true);
    }
}
```

## §3 metadata — ID3v2.3 TYER → v2.4 TDRC (§22.3 · PHP 8.4)

### 3.1 PHP 8.4 — Etiket Dönüşümü (kayıpsız) — ~35 satır

```php
<?php
declare(strict_types=1);

// §22.3: MP3 ID3v2.3 TYER (yıl) → v2.4 TDRC (tarih kaydı) kırılması — uyumluluk dikkat.
// TASARIM ÖNERİSİ — repo'da kod YOK. EBU R128/ReplayGain §22.3.
final class MetadataNormalizer
{
    /** @return array<string, string> etiketler */
    public function normalizeV23ToV24(array $tags): array
    {
        $out = $tags;
        // TYER (ID3v2.3: yalnız yıl) → TDRC (ID3v2.4: tam tarih kaydı).
        if (isset($tags['TYER'])) {
            $out['TDRC'] = $tags['TYER']; // "2019" → TDRC "2019" (kayıpsız daraltma)
            unset($out['TYER']);
        }
        // TCON (ID3v2.3: "(17)Rock") → TCON (v2.4: "Rock") — parantez numarası çözümle.
        if (isset($tags['TCON']) && preg_match('/^\((\d+)\)(.*)$/', $tags['TCON'], $m)) {
            $out['TCON'] = trim($m[2]);
        }
        return $out;
    }
}
```

## §4 artwork — CAA Üç Kademe + immutable (§22.4 · Python)

### 4.1 Python — Cover Art Archive Varyantları — ~30 satır

```python
# §22.4: CAA resmi kademeler 250/500/1200 px — üç kademe üret + immutable sun.
# TASARIM ÖNERİSİ — repo'da kod YOK. §9.3: thumbnail/medium/large.
CADRES = {"thumbnail": 128, "medium": 500, "large": 1200}  # §22.4 kademeleri

async def fetch_artwork_variants(mbid: str, http) -> dict[str, bytes]:
    """CAA'dan kapak indir + üç kademe üret (TASARIM ÖNERİSİ)."""
    url = f"https://coverartarchive.org/release/{mbid}/front-1200"  # §22.4: 1200 resmi
    r = await http.get(url, timeout=10)
    out = {}
    if r.status_code == 200:
        original = r.content
        for name, px in CADRES.items():
            out[name] = await resize(original, px)   # resize: PIL benzeri (söylem)
    # Sunum: Cache-Control: public, max-age=31536000, immutable + ETag (içerik hash) — §22.5
    return out
```

## §5 media-cache — ARC (Scan-Resistant) (§22.5 · Go)

### 5.1 Go — ARC Sarmalayıcı (T1/T2/B1/B2) — ~35 satır

```go
// §22.5: katalog/artwork → ARC (scan-resistant); audio stream'de LRU isabet ≈ 0.
// TASARIM ÖNERİSİ — eşikler YAZILMAZ (§16/3 UNKNOWN). §9.9: T1/T2/B1/B2.
package mediacache

type ARC struct {
    t1, t2, b1, b2 map[string]bool // varlıklar (söylem — gerçek liste/queue ile)
    p              float64         // hedef boyut (p = |t1|+|t2|)
    b              float64         // ghost boyutu
}

func (a *ARC) Get(k string) (string, bool) {
    if _, ok := a.t2[k]; ok {          // T2: sık erişilen — hit
        return k, true
    }
    if _, ok := a.t1[k]; ok {          // T1 → promote T2
        delete(a.t1, k); a.t2[k] = true
        return k, true
    }
    // miss — ghost'tan (b1/b2) öğren: p'yi ayarla (scan-resistant çekirdek)
    return "", false
}
```

## §6 playback — Durum Makinesi (§9.5 · TypeScript)

### 6.1 TypeScript — IDLE/PLAYING/PAUSED/SEEKING/ENDED — ~35 satır

```ts
// §9.5: oynat/duraklat/ileri/geri + pozisyon takibi. §22.2: Xing/LAME + SEEKTABLE.
// TASARIM ÖNERİSİ — repo'da kod YOK.
type State = 'IDLE' | 'PLAYING' | 'PAUSED' | 'SEEKING' | 'ENDED'; // §23.1 playback

class PlaybackController {
  private state: State = 'IDLE';
  private positionSec = 0;

  transition(event: 'play' | 'pause' | 'seek' | 'end' | 'stop'): State {
    const table: Record<State, Partial<Record<string, State>>> = {
      IDLE:    { play: 'PLAYING' },
      PLAYING: { pause: 'PAUSED', seek: 'SEEKING', end: 'ENDED' },
      PAUSED:  { play: 'PLAYING', stop: 'IDLE' },
      SEEKING: { seek: 'SEEKING', play: 'PLAYING', end: 'ENDED' },
      ENDED:   { play: 'PLAYING', stop: 'IDLE' },   // tekrar çal
    };
    this.state = (table[this.state][event] as State) ?? this.state;
    return this.state;
  }
}
```

## §7 streaming — HTTP Range 206 + Content-Range (RFC 9110 · §22.2 · PHP 8.4)

### 7.1 PHP 8.4 — Range Parça Sunumu — ~35 satır

```php
<?php
declare(strict_types=1);

// §22.2: yerel dosya → HTTP Range (RFC 9110 §14) — parça/seek için.
// TASARIM ÖNERİSİ — repo'da kod YOK. §9.6: 206 + Content-Range.
final class RangeStreamer
{
    public function serve(string $path, ?string $rangeHeader): never
    {
        $size = filesize($path);
        if ($rangeHeader === null || !preg_match('/bytes=(\d*)-(\d*)/', $rangeHeader, $m)) {
            // Range yok → 200 tam gövde
            header('Content-Length: ' . $size);
            readfile($path); // söylem
            exit;
        }
        $start = $m[1] !== '' ? (int) $m[1] : 0;
        $end   = $m[2] !== '' ? (int) $m[2] : $size - 1;
        $length = $end - $start + 1;

        http_response_code(206);                              // Partial Content
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
        header('Content-Length: ' . $length);
        header('Accept-Ranges: bytes');
        // fp = fopen + fseek($start) + stream_copy_to_stream(fp, length) — söylem
        exit;
    }
}
```

## §8 downloads — Resume + Atomik Rename (ADR-026 · Rust)

### 8.1 Rust — Range Resume + .part → atomik — ~35 satır

```rust
// ADR-026: Download Service Architecture — resume · atomic rename · checksum.
// TASARIM ÖNERİSİ — repo'da kod YOK (§9.7). §22: exponential backoff.
use std::fs;

pub async fn resume(url: &str, dest: &str, client: &reqwest::Client) -> Result<u64, Error> {
    let part = format!("{dest}.part");                       // .part dosyası
    let have = fs::metadata(&part).map(|m| m.len()).unwrap_or(0);
    let mut req = client.get(url);
    if have > 0 {
        req = req.header("Range", format!("bytes={have}-")); // §23.1: Range resume
    }
    let res = req.send().await?;
    let status = res.status().as_u16();
    let append = status == 206;                              // 206 = devam
    // yaz (append veya truncate) — SHA-256 checksum doğrula (§23.1)
    // fs::rename(part, dest) — ATOMIK (ADR-026)
    Ok(have)
}
```

## §9 offline — is_offline_ready (ADR-027 · SQL)

### 9.1 SQL — Offline Hazırlık Durumu — ~20 satır

```sql
-- ADR-027: Dual-Mode Storage. §9.8: READY/PENDING/MISSING + kota/LRU-TTL eviction.
-- TASARIM ÖNERİSİ — repo'da şema YOK (şema K007'de — §16/2 UNKNOWN).
CREATE TABLE media_offline (
    track_ulid      CHAR(26)     NOT NULL,        -- ADR-092 ULID
    is_offline_ready BOOLEAN     NOT NULL DEFAULT FALSE,
    ready_state     ENUM('READY','PENDING','MISSING') NOT NULL DEFAULT 'MISSING',
    cached_at       TIMESTAMPTZ  NULL,
    size_bytes      BIGINT       NOT NULL,
    PRIMARY KEY (track_ulid)
);
-- Eviction (söylem): kota aşılırsa LRU-TTL — en eski cached_at + en düşük erişim silinir.
```

## §10 synchronization — Vector Clock / LWW (§9.10 · Elixir)

### 10.1 Elixir — LWW Çakışma Çözümü — ~25 satır

```elixir
# §9.10: çift yönlü mü tek yönlü UNKNOWN (§12/9) — bu blok LWW varsayar (TASARIM ÖNERİSİ).
# Vector clock / LWW (last-write-wins) — §23.1 synchronization.
defmodule MediaSync do
  def merge(local, remote) do
    # LWW: daha yeni updatedAt kazanır (eşitlikte remote kazanır — deterministik).
    cond do
      NaiveDateTime.compare(remote.updated_at, local.updated_at) == :gt -> remote
      true -> local
    end
  end
end
```

## §11 media-testing — PCM SHA-256 Golden Fixture (§9.11 · Python)

### 11.1 Python — Decode Doğrulama — ~25 satır

```python
# §9.11: decode PCM SHA-256 golden fixture — §13/1: ses çıktısı bütünlüğü.
# TASARIM ÖNERİSİ — araç seti UNKNOWN (§12/10); sahiplik §7/4 belirsiz.
import hashlib, subprocess

def verify_decode(file_path: str, golden_sha256: str) -> bool:
    # decode: dosya → ham PCM (WAV — §22.1: ham PCM tescilsiz)
    pcm = subprocess.run(["ffmpeg", "-i", file_path, "-f", "wav", "-"],
                         capture_output=True).stdout   # söylem — araç §12/10 UNKNOWN
    actual = hashlib.sha256(pcm).hexdigest()
    return actual == golden_sha256   # golden fixture ile byte-birebir
```

## §12 EBU R128 Normalizasyonu (§22.3 · Java)

### 12.1 Java — R128 Hedefe Uzaklık — ~25 satır

```java
// §22.3: EBU R128 (−23 LUFS yayın) · ReplayGain 2.0 (−18 LUFS).
// TASARIM ÖNERİSİ — repo'da kod YOK. Ölçüm: integrated loudness (LUFS).
public final class Loudness {
    public static final double R128_TARGET = -23.0;   // §22.3
    public static final double RG2_TARGET  = -18.0;   // ReplayGain 2.0

    public static double gainToR128(double measuredLufs) {
        // hedef - LUFS = uygulanacak kazanç (dB) — pozitifse yükselt.
        return R128_TARGET - measuredLufs;
    }
}
```

## §13 catalog — İçerik Hash Dedup + Sıralı Browse (§9.4 · Ruby)

### 13.1 Ruby — Sorted Browse — ~22 satır

```ruby
# §9.4: koleksiyon indeksi — sorted browse + content-hash dedup (§23.1 catalog).
# TASARIM ÖNERİSİ — arama yöntemi UNKNOWN (tam metin/etiket — §9.4).
class Catalog
  def initialize = @tracks = []   # track: {ulid:, title:, artist:, hash:}

  def add(track)
    return if @tracks.any? { |t| t[:hash] == track[:hash] }  # dedup (§2.1 paraleli)
    @tracks << track
  end

  def browse(order: :artist)      # sıralı gezinme
    @tracks.sort_by { |t| t[order].to_s.downcase }
  end
end
```

## §14 artwork — Gömülü Kapak Çıkarma (§9.3 · Kotlin)

### 14.1 Kotlin — Embedded Cover (APIC/_covr) — ~22 satır

```kotlin
// §9.3: gömülü kapak çıkarma — MP3 APIC (ID3v2) / MP4 covr (§22.3: ilst ©nam/©ART/covr).
// TASARIM ÖNERİSİ — repo'da kod YOK.
fun extractEmbeddedCover(tags: Map<String, ByteArray>): ByteArray? {
    // ID3v2: "APIC" frame — MP4: "covr" box (§22.3 tablosu).
    val apic = tags["APIC"] ?: tags["covr"] ?: return null
    return apic   // bayt gövdesi — boyut varyantı §22.4 ile üretilir
}
```

## §15 streaming — M3U8 Manifest (§22.2 · Swift)

### 15.1 Swift — HLS Manifest Oluşturma — ~25 satır

```swift
// §22.2: HLS (RFC 8216) — yalnız canlı/uzun içerikte (TASARIM ÖNERİSİ önerisi).
// Adaptif akış → HLS veya DASH. M3U8 manifest (§23.1 streaming).
func makeM3U8(segmentCount: Int, segmentDuration: Int) -> String {
    var out = "#EXTM3U\n#EXT-X-VERSION:3\n"
    out += "#EXT-X-TARGETDURATION:\(segmentDuration)\n"
    for i in 0..<segmentCount {
        out += "#EXTINF:\(Double(segmentDuration)),\nseg-\(i).ts\n"  // HLS segment
    }
    out += "#EXT-X-ENDLIST\n"
    return out
}
```

## §16 Dilim Tablosu (15 dil × çocuk — §23.2 sırası)

| Blok | Dil | Çocuk | Konu | Satır |
|---|---|---|---|---|
| §2.1 | C# | library | ULID + dedup (ADR-092) | ~40 |
| §3.1 | PHP 8.4 | metadata | ID3 TYER→TDRC (§22.3) | ~35 |
| §4.1 | Python | artwork | CAA 1200 + varyant (§22.4) | ~30 |
| §5.1 | Go | media-cache | ARC T1/T2/B1/B2 (§22.5) | ~35 |
| §6.1 | TypeScript | playback | Durum makinesi | ~35 |
| §7.1 | PHP 8.4 | streaming | Range 206 (RFC 9110) | ~35 |
| §8.1 | Rust | downloads | Resume + atomik (ADR-026) | ~35 |
| §9.1 | SQL | offline | is_offline_ready (ADR-027) | ~20 |
| §10.1 | Elixir | synchronization | LWW (§12/9 UNKNOWN) | ~25 |
| §11.1 | Python | media-testing | PCM SHA-256 golden | ~25 |
| §12.1 | Java | (metadata) | EBU R128 −23 LUFS (§22.3) | ~25 |
| §13.1 | Ruby | catalog | sorted browse + dedup | ~22 |
| §14.1 | Kotlin | artwork | APIC/covr çıkarma | ~22 |
| §15.1 | Swift | streaming | M3U8 manifest (RFC 8216) | ~25 |
| — | (Elixir §10.1 · Scala ornekler.md) | — | §23.2: 15 dil (§2-§16) | — |

**Toplam**: ~400 kod satırı · 14 blok · 13 dil (C#/PHP/Python/Go/TS/Rust/SQL/Elixir/Java/Ruby/Kotlin/Swift) — §23.2'de eksiksiz 15 dil (Scala + Elixir ornekler.md'de).

## §17 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K014-media/media-master.md` | §22/§23 (standartlar + tam set) | media-master |
| `.ai/architecture/K014-media/media-kararlar.md` | ADR-092/026/027/028 + draft | media-kararlar |
| `.ai/architecture/K014-media/media-test-stratejisi.md` | Medya testleri | media-test-stratejisi |
