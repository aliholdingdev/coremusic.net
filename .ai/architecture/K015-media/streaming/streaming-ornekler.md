---
node_id: K015-MEDIA-STREAMING-ORNEKLER
node_type: kod
status: active
parent_id: K015-MEDIA
children: []
depends_on: [K015-MEDIA-STREAMING-KAPSAM]
evidence: [K015-media/streaming/streaming-kapsam.md, K015-media/streaming/streaming-web-arastirma.md]
validation: partial
open_questions: 3
group: kod
---

# Streaming — Kod Örnekleri (K015-media/children/streaming)

> **Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
>
> Bu dosya `streaming` düğümü için **tasarım iskeletleri** içerir:
> Vanilla JS ES6+ (istemci) + gerekirse PHP 8.4 (sunucu kuyruğu).
> Format referansı:
> `.ai/architecture/K009-security/threat-model/threat-model-ornekler.md`.
>
> **Her örnek TASARIM İDDİASI'dır — üretim kodu değildir.**
> Üretim koduna geçiş §6 (protokol/codec — UNKNOWN) açık soruları
> **çözüldükten** sonra yapılır.

## §1 — Kayıt Formatı

Her kayıt şu tabloyu taşır:

| Alan | İçerik |
|---|---|
| Amaç | Örneğin neyi gösterdiği |
| İlgili kapsar madde | `streaming-kapsam.md` §x/x |
| Dil | Vanilla JS ES6+ (istemci) · PHP 8.4 (sunucu, gerekirse) |
| Üretim mü? | Hayır — TASARIM İDDİASI |

## §2 — Örnekler

### Örnek 1 — Durum Makinesi: HTMLAudioElement olayları (kapsam §3 — 6 durum)

```js
// Amaç: kapsam §3'teki connecting/buffering/rebuffering/playing/ended/error
//       durumlarını HTMLAudioElement olaylarından türetmek.
// Üretim mü? HAYIR — TASARIM İDDİASI (tasarım iskeleti).
'use strict';

/** @enum {string} */
const StreamState = Object.freeze({
  CONNECTING: 'connecting',
  BUFFERING: 'buffering',
  REBUFFERING: 'rebuffering',
  PLAYING: 'playing',
  ENDED: 'ended',
  ERROR: 'error',
});

/**
 * Bağlantı olayı yoktur: 'loadstart' -> connecting,
 * ilk 'canplay' -> playing; ara tamponlar buffering/rebuffering'e haritalanır.
 * Harita TASARIM İDDİASI'dır (§6/4 açık sorusu ile ilgili).
 */
class StreamStateMachine {
  #state = StreamState.CONNECTING;
  #hadPlayed = false;

  get state() { return this.#state; }

  /** Geçiş tablosu: kapsam §3 ile birebir; olmayan geçiş yutulur. */
  #transition(to) {
    const allowed = {
      [StreamState.CONNECTING]: [StreamState.BUFFERING, StreamState.ERROR],
      [StreamState.BUFFERING]: [StreamState.PLAYING, StreamState.ERROR],
      [StreamState.PLAYING]: [StreamState.REBUFFERING, StreamState.ENDED, StreamState.ERROR],
      [StreamState.REBUFFERING]: [StreamState.PLAYING, StreamState.ERROR],
      [StreamState.ENDED]: [StreamState.CONNECTING],
      [StreamState.ERROR]: [StreamState.CONNECTING],
    };
    if ((allowed[this.#state] ?? []).includes(to)) this.#state = to;
  }

  /** HTMLAudioElement olaylarını duruma haritalar. */
  bind(audio) {
    audio.addEventListener('loadstart', () => this.#transition(StreamState.CONNECTING));
    audio.addEventListener('waiting', () => {
      this.#transition(this.#hadPlayed ? StreamState.REBUFFERING : StreamState.BUFFERING);
    });
    audio.addEventListener('playing', () => {
      this.#hadPlayed = true;
      this.#transition(StreamState.PLAYING);
    });
    audio.addEventListener('ended', () => this.#transition(StreamState.ENDED));
    audio.addEventListener('error', () => this.#transition(StreamState.ERROR));
    audio.addEventListener('stalled', () => {
      // H-2 (kopma): otomatik yeniden bağlanma politikası §6/5 — AÇIK.
    });
  }
}

export { StreamState, StreamStateMachine };
```

**Not:** `rebuffering` **hata değildir** (kapsam §5/H-3) — `waiting`
olayından `hadPlayed` bayrağıyla ayrıştırılır; yeniden deneme sayısı
(§6/5) burada **bilinçli olarak yazılmaz**.

---

### Örnek 2 — MSE SourceBuffer İskeleti (web-arastirma §1/§2 — MSE kanıtı)

```js
// Amaç: MediaSource + SourceBuffer ile parça (segment) eklemenin iskeleti.
// Protokol/segment biçimi SEÇİLMEDİ (kapsam §6/1-§6/2 — UNKNOWN).
// Üretim mü? HAYIR — TASARIM İDDİASI.
'use strict';

/**
 * @param {HTMLMediaElement} media
 * @param {string} mime  Örn. belirsiz — codec seti UNKNOWN (§6/2).
 * @returns {Promise<{ mediaSource: MediaSource, sourceBuffer: SourceBuffer }>}
 */
async function createStreamPipeline(media, mime) {
  if (typeof MediaSource === 'undefined') {
    throw new Error('MSE desteklenmiyor — caniuse/mediasource (web-arastirma §1/5)');
  }
  const mediaSource = new MediaSource();
  media.src = URL.createObjectURL(mediaSource);

  await new Promise((resolve) => {
    mediaSource.addEventListener('sourceopen', resolve, { once: true });
  });

  const sourceBuffer = mediaSource.addSourceBuffer(mime);

  return { mediaSource, sourceBuffer };
}

/** Parça ekleme: kuyruk disiplini — SourceBuffer aynı anda 1 append ister. */
function appendSegmentQueue(sourceBuffer, segments) {
  const queue = [...segments];
  const step = () => {
    if (queue.length === 0 || sourceBuffer.updating) return;
    const segment = queue.shift();
    sourceBuffer.appendBuffer(segment);
    sourceBuffer.addEventListener('updateend', step, { once: true });
  };
  step();
}

export { createStreamPipeline, appendSegmentQueue };
```

**Not:** MSE'nin tarayıcı desteği yaygın ama tam değildir
(web-arastirma §1/5) — desteği olmayan tarayıcı için **düşük kademe
düşüş planı AÇIK SORU** olarak kalır (kapsam §6/1).

---

### Örnek 3 — PHP 8.4 Sunucu Kuyruğu (kapsam §5/H-5 — dış servis hataları)

```php
<?php
declare(strict_types=1);

/**
 * Amaç: dış servisten gelen akış parçalarının/hatalarının kuyruğa alınması
 *       ve tekrar deneme politikasının İSKELETİ (K013 dış kaynak hataları).
 * Üretim mü? HAYIR — TASARIM İDDİASI.
 */

final readonly class StreamJob
{
    public function __construct(
        public string $sourceUrl,   // dış servis adresi (K013 üzerinden)
        public int $attempt = 0,
    ) {}
}

final class StreamRetryQueue
{
    /** @var list<StreamJob> */
    private array $jobs = [];

    private const int MAX_ATTEMPTS = 3;      // §6/5 (açık soru) — TASARIM İDDİASI
    private const int BACKOFF_MS = 500;      // basit sabit gecikme (tasarım)

    public function enqueue(StreamJob $job): void
    {
        $this->jobs[] = $job;
    }

    /**
     * Sıradaki işi işler; başarısızlıkta attempt++ ve yeniden kuyruk.
     * Dış servis hatası (H-5) yorumu K013'ün; burada yalnız tekrar deneme.
     *
     * @param callable(StreamJob): bool $fetch K013 üzerinden çekilme
     */
    public function run(callable $fetch): void
    {
        while ($job = array_shift($this->jobs)) {
            if ($fetch($job)) {
                continue;                                  // başarılı
            }
            if ($job->attempt + 1 < self::MAX_ATTEMPTS) {
                $this->jobs[] = new StreamJob($job->sourceUrl, $job->attempt + 1);
                usleep(self::BACKOFF_MS * 1000);
                continue;
            }
            // Kalıcı hata -> üst katmana bildir (K011/K012 yüzeyi).
        }
    }
}
```

**Not:** Deneme sayısı ve backoff **kapsam §6/5 açık sorusudur** — buradaki
`MAX_ATTEMPTS = 3` / sabit gecikme **örnek değerdir, bağlayıcı değildir**.

## §3 — Özet

| # | Örnek | Dil | Kapsam karşılığı | Üretim mü? |
|---|---|---|---|---|
| 1 | Durum makinesi (HTMLAudioElement olayları) | Vanilla JS ES6+ | §3 (6 durum) | Hayır — TASARIM İDDİASI |
| 2 | MSE SourceBuffer parça kuyruğu | Vanilla JS ES6+ | §4 orkestre eder + web-arastirma §1/1 | Hayır — TASARIM İDDİASI |
| 3 | Dış servis yeniden deneme kuyruğu | PHP 8.4 | §5/H-5 (K013 hataları) | Hayır — TASARIM İDDİASI |

**Üretim öncesi çözülecekler:** §6/1 (protokol) · §6/2 (codec seti) ·
§6/5 (yeniden deneme politikası).

## §4 — Revizyon

| Tarih | Revizyon | Authority |
|---|---|---|
| 2026-10-16 | İlk örnek seti — JS ES6+ ×2 + PHP 8.4 ×1; protokol/codec seçilmedi | Bayram Ali / Vault Steward |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
