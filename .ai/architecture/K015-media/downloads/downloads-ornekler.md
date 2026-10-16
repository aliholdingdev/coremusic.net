---
node_id: "K015-media/downloads/downloads-ornekler"
node_type: "module"
title: "Downloads — Örnekler (tasarım iddiası)"
status: draft
group: kod
parent_id: "K015-media/downloads"
children: []
depends_on:
  - "K015-media/downloads/downloads-kapsam"
  - "K015-media/downloads/downloads-web-arastirma"
evidence:
  - "downloads-kapsam.md — §3 durum makinesi, §5 hata durumları (2026-10-16)"
  - "downloads-web-arastirma.md — D-01..D-16 URL havuzu (2026-10-16, HTTP 200)"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php:91,95-99,101-106,224-262,264-290,310-355 — repo'da mevcut Range/206/416/ETag başlık uygulaması (2026-10-16 okundu)"
  - "home.coremusic.net/include/Repository/MusicRepository.php:1,53-55,57-66 — repo'da mevcut PDO prepared sorgu deseni (2026-10-16 okundu)"
validation: "TASARIM İDDİASI — üretim kodu değildir; repository'de indirme endpoint'i / indirme kuyruğu JS'i / Cache API sarmalayıcı YOK (2026-10-16 grep: 0 isabet). Örnekler yalnız tasarım referansıdır."
open_questions: "Uygulama dili/paketi (UNKNOWN) · indirme kuyruğu istemcisi (kendi JS'imiz mi, tarayıcı mı — UNKNOWN) · kuyruk kalıcılığı (§5 K007 sınırı) · PHP sürümü (repo strict_types=1 doğrulandı, sürüm UNKNOWN)"
updated: 2026-10-16
---

# Downloads — Örnekler (tasarım iddiası)

> **Etiket kuralı:** Bu dosyadaki tüm örnekler **TASARIM İDDİASI** etiketlidir; repo'da
> birebir karşılığı olan satırlar `Dosya:Satır` ile kanıtlanır. Üretim kodu değildir.

> **Tarih:** 2026-10-16 · **Kapsam:** K015-media / downloads

---

## Ör-01 — İndirme kuyruğu durum makinesi (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `download-queue.js` (tasarım) |
| Amaç | Kuyruk kalemilerini `downloads-kapsam.md` §3'teki altı durumda tutmak, hata/yeniden deneme (§5) |
| Kanıt | `downloads-kapsam.md:§3` (durum makinesi) · `downloads-web-arastirma.md:D-15,D-16` (AbortController/Streams) |
| Durum | TASARIM İDDİASI — repo'da kuyruk JS'i yok (grep 0 isabet) |

```javascript
// download-queue.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

const State = Object.freeze({
  QUEUED: 'queued', ACTIVE: 'active', PAUSED: 'paused',
  RESUMED: 'resumed', FAILED: 'failed', COMPLETED: 'completed',
});

const ALLOWED = {
  queued: ['active', 'failed'],
  active: ['paused', 'failed', 'completed', 'queued'],
  paused: ['resumed', 'queued', 'failed'],
  resumed: ['active', 'paused', 'failed'],
  failed: ['queued'],
  completed: [],
};

export class DownloadQueue {
  #items = new Map();   // id -> { state, attempt, controller }

  transition(id, next) {
    const item = this.#items.get(id);
    if (!item) throw new Error(`unknown item: ${id}`);
    if (!ALLOWED[item.state].includes(next)) {
      throw new Error(`illegal transition ${item.state} -> ${next}`);
    }
    if (next === 'active' && !item.controller) {
      item.controller = new AbortController(); // D-15
    }
    if (next === 'failed') item.attempt += 1;  // §5 hata sayaci
    item.state = next;
    return item.state;
  }

  pause(id) { return this.transition(id, 'paused'); }
  resume(id) {
    const item = this.#items.get(id);
    if (!item) throw new Error(`unknown item: ${id}`);
    item.controller = new AbortController();
    return this.transition(id, item.state === 'paused' ? 'resumed' : 'active');
  }
  cancel(id) {
    const item = this.#items.get(id);
    if (item?.controller) item.controller.abort(); // D-15
    this.#items.delete(id);
  }
}
```

**Not:** İsimler/alanlar tasarım iddiasıdır; §6 TTL/boyut/yeniden deneme eşiği
UNKNOWN'dur — burada sayısal eşik yazılmamıştır.

---

## Ör-02 — Range header ile resume endpoint (PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `DownloadResumeHandler.php` (tasarım) |
| Amaç | `downloads-kapsam.md` §3 `active` kaleminin bayt aralığıyla sürdürülmesi |
| Kanıt | **Repo:** `home.coremusic.net/include/Stream/MusicStreamHandler.php:91` (Range ayrıştırma), `:95-99` (416 kontrolü), `:101-106` (`206 Partial Content` + `Content-Range`), `:310-355` (tame/multipart bayt aralığı yazımı) |
| Durum | Repo'da davranış VAR; **dosya adı/uygulama yüzeyi tasarım iddiası** — repo'da `DownloadResumeHandler.php` YOK |

```php
<?php
declare(strict_types=1);

// DownloadResumeHandler.php — TASARIM IDDIASI (uretim kodu degildir)
// Repo'daki kanit: MusicStreamHandler.php:91-106 (Range/206/416/Content-Range).

final class DownloadResumeHandler
{
    public function handle(string $path, int $fileSize): never
    {
        $range = $_SERVER['HTTP_RANGE'] ?? null;

        header('Accept-Ranges: bytes');
        header('ETag: "' . md5_file($path) . '"');
        // D-08: degisimi engellemek icin sartli GET
        if (isset($_SERVER['HTTP_IF_RANGE'])
            && $_SERVER['HTTP_IF_RANGE'] !== '"' . md5_file($path) . '"') {
            $range = null; // dosya degismis: tam govde gonder
        }

        if ($range === null) {                 // tam indirme
            header('Content-Length: ' . $fileSize);
            header('Content-Type: application/octet-stream');
            readfile($path);
            exit;
        }

        [$start, $end] = $this->parseRange($range, $fileSize);
        if ($start === null) {                 // D-10: 416
            http_response_code(416);
            header(sprintf('Content-Range: bytes */%d', $fileSize));
            exit;
        }

        http_response_code(206);               // D-04
        header(sprintf('Content-Range: bytes %d-%d/%d', $start, $end, $fileSize));
        header('Content-Length: ' . ($end - $start + 1));
        header('Content-Type: application/octet-stream');

        $fh = fopen($path, 'rb');
        fseek($fh, $start);
        $remaining = $end - $start + 1;
        while ($remaining > 0 && !feof($fh)) {
            $chunk = fread($fh, min(8192, $remaining)); // Streams benzeri akis
            echo $chunk;
            $remaining -= strlen($chunk);
        }
        fclose($fh);
        exit;
    }

    /** @return array{0: ?int, 1: ?int} */
    private function parseRange(string $range, int $size): array
    {
        if (!preg_match('/^bytes=(\d*)-(\d*)$/', $range, $m)) {
            return [null, null];
        }
        $start = $m[1] !== '' ? (int)$m[1] : null;
        $end = $m[2] !== '' ? (int)$m[2] : null;
        if ($start === null && $end === null) return [null, null];
        if ($start === null) { $start = max(0, $size - $end); $end = $size - 1; }
        if ($end === null || $end > $size - 1) $end = $size - 1;
        if ($start > $end || $start >= $size) return [null, null];
        return [$start, $end];
    }
}
```

**Sınır notu (`downloads-kapsam.md` §3):** repo'daki Range davranışı **akış**
yüzeyinde (`GET /stream/{id}`); diske indirme endpoint'i için repo'da isabet yok —
"indirme uygulandı" iddiası üretilmez.

---

## Ör-03 — PDO prepared sorgu (indirme kaydı, PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `DownloadRepository.php` (tasarım) |
| Amaç | İndirme kaydı/derlemesi (§1) — parametreli sorgu, `SELECT *` yok |
| Kanıt | **Repo:** `home.coremusic.net/include/Repository/MusicRepository.php:53-55` (`prepare`), `:57-66` (`execute` + `fetch`) — PDO prepared deseni birebir mevcut |
| Durum | Tasarım iddiası; indirme tablosu adı UNKNOWN (§6) |

```php
<?php
declare(strict_types=1);

// DownloadRepository.php — TASARIM IDDIASI (uretim kodu degildir)
// Repo kaniti: MusicRepository.php:53-55 (prepare), :57-66 (execute + fetch).

final class DownloadRepository
{
    public function __construct(private readonly PDO $pdo) {}

    /** Indirme kalemini kuyruga ekle (§1 — satir TOPLAM, ad UNKNOWN). */
    public function enqueue(int $mediaId, int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO download_queue (media_id, user_id, state, created_at)
             VALUES (:media_id, :user_id, :state, NOW())'
        );
        $stmt->execute([
            ':media_id' => $mediaId,
            ':user_id' => $userId,
            ':state' => 'queued',          // §3 durum makinesi
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function setState(int $id, string $state): bool
    {
        $allowed = ['queued','active','paused','resumed','failed','completed'];
        if (!in_array($state, $allowed, true)) {
            throw new InvalidArgumentException('illegal state: ' . $state);
        }
        $stmt = $this->pdo->prepare(
            'UPDATE download_queue SET state = :state WHERE id = :id'
        );
        return $stmt->execute([':state' => $state, ':id' => $id]);
    }
}
```

---

## Ör-04 — İndirme yanıtı için cache-control setter (PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `DownloadHeaders.php` (tasarım) |
| Amaç | §1 "kişiselleştirilmiş indirme yanıtı önbelleklenmez" — `private, no-store` |
| Kanıt | `downloads-kapsam.md:§1` (kapsam içi) · `downloads-web-arastirma.md:D-06,D-07` (Cache-Control / Retry-After) |
| Durum | Tasarım iddiası — repo'da indirme yanıt başlığı YOK (grep 0 isabet) |

```php
<?php
declare(strict_types=1);

// DownloadHeaders.php — TASARIM IDDIASI (uretim kodu degildir)

final class DownloadHeaders
{
    /** Kisisel indirme yaniti: ortak onbelleklerde saklanmaz (D-06). */
    public static function personal(): void
    {
        header('Cache-Control: private, no-store');
        header('Content-Type: application/octet-stream');
        header('X-Content-Type-Options: nosniff');
    }

    /** Yeniden deneme sinirlamasi (§5, D-07): saniye cinsinden. */
    public static function retryAfter(int $seconds): void
    {
        http_response_code(429);
        header('Retry-After: ' . max(0, $seconds));
    }
}
```

**Not:** `Retry-After` sayısı §6 açık sorusudur (UNKNOWN) — örnekte eşik yazılmamıştır.

---

## Ör-05 — Abort + akışla parça okuma (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `download-stream.js` (tasarım) |
| Amaç | §3 `active` akışında iptal (D-15) ve parça parça yazma (D-16) |
| Kanıt | `downloads-web-arastirma.md:D-15,D-16` · repo'da JS karşılığı YOK |
| Durum | TASARIM İDDİASI |

```javascript
// download-stream.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

export async function fetchToSink(url, sink, signal) {
  const res = await fetch(url, { signal, credentials: 'same-origin' }); // D-15
  if (!res.ok && res.status !== 206) {
    throw new Error(`unexpected status: ${res.status}`); // §5
  }
  const reader = res.body.getReader();                   // D-16 (Streams)
  for (;;) {
    const { done, value } = await reader.read();
    if (done) break;
    await sink.write(value);                             // bayt bayt yaz
  }
}
```

---

## Kapsam dışı (bu dosyada YOK)

- Depolama yüzeyi (Cache Storage/IndexedDB kotaları) → `offline/` kardeş dosyaları.
- TTL/LRU/boyut eşik sayıları → `downloads-kapsam.md` §6 (UNKNOWN, kapatma yasak).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
