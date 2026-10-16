---
node_id: "K015-media/offline/offline-ornekler"
node_type: "module"
status: draft
group: kod
parent_id: "K015-media/offline"
children: []
depends_on:
  - "K015-media/offline/offline-kapsam"
  - "K015-media/offline/offline-web-arastirma"
evidence:
  - "offline-kapsam.md — §1a/§3 durum makinesi, §5 hata durumları (2026-10-16)"
  - "offline-web-arastirma.md — O-1..O-11 URL havuzu (2026-10-16, HTTP 200)"
  - "Repo taraması (2026-10-16): `serviceWorker|navigator.onLine|caches.open` = 0 isabet (js/php) · `offline` (php, büyük/küçük duyarsız) = 0 isabet — çevrimdışı yüzeyi repo'da YOK"
  - "home.coremusic.net/include/Repository/MusicRepository.php:53-55,57-66 — repo'da mevcut PDO prepared sorgu deseni (2026-10-16 okundu)"
validation: "TASARIM İDDİASI — üretim kodu değildir; repo'da çevrimdışı yüzeyi (SW · onLine · Cache API · offline endpoint) YOK (2026-10-16 grep: 0 isabet). Örnekler yalnız tasarım referansıdır."
open_questions: "Çevrimdışı sahipliği (§6/1 — kesinleştirilmez) · stale/TTL eşiği (UNKNOWN) · SW stratejisi (precache/on-demand — UNKNOWN) · envanter şeması (K007 sınırı)"
updated: 2026-10-16
---

# offline — Örnekler (tasarım iddiası)

> **Etiket kuralı:** Bu dosyadaki tüm örnekler **TASARIM İDDİASI** etiketlidir; repo'da
> karşılığı OLAN tek şey PDO prepared desenidir (`MusicRepository.php:53-55,57-66`).
> Çevrimdışı yüzeyi (SW · `navigator.onLine` · Cache API · offline endpoint) repo'da
> **0 isabettir** (grep 2026-10-16) — "çevrimdışı uygulandı" iddiası üretilmez.

> **Tarih:** 2026-10-16 · **Kapsam:** K015-media / offline

---

## Ör-01 — Bağlantı durumu durum makinesi (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `connectivity.js` (tasarım) |
| Amaç | `offline-kapsam.md` §3'teki 4 durumu (`online` · `online-degraded` · `offline` · `offline-synced`) dış olaylarla taşımak |
| Kanıt | `offline-web-arastirma.md:A-2` (O-7 `navigator.onLine` + O-8 `online` olayı — 2 kaynak) |
| Durum | TASARIM İDDİASI — repo'da `navigator.onLine` 0 isabet |

```javascript
// connectivity.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

const State = Object.freeze({
  ONLINE: 'online',
  DEGRADED: 'online-degraded',
  OFFLINE: 'offline',
  SYNCED: 'offline-synced',
});

const ALLOWED = {
  online: ['online-degraded', 'offline'],
  'online-degraded': ['online', 'offline'],
  offline: ['online-degraded', 'online'],   // baglanti dondu (kapsam §3 gecis kurali)
  'offline-synced': ['online', 'online-degraded', 'offline'],
};

export class Connectivity {
  #state = State.ONLINE;
  #onState = null;

  constructor(subscribe) { this.#onState = subscribe; }

  /** Dis olaylar (O-7/O-8): tarayici gozlemi. */
  bindWindow(win) {
    win.addEventListener('online', () => this.#go(State.OFFLINE, State.ONLINE));
    win.addEventListener('offline', () => this.#from(State.OFFLINE));
    // baslangic gozlemi
    if (win.navigator.onLine === false) this.#state = State.OFFLINE;
  }

  /** Bekleyen sync bitti (§3: offline-synced kalici degildir). */
  markSynced() {
    if (this.#state === State.OFFLINE) this.#go(State.OFFLINE, State.SYNCED);
  }

  /** degrade gozlemi (§3a/W-2: esik UNKNOWN — disaridan gelen bayrak). */
  degrade(flag) {
    if (flag && this.#state === State.ONLINE) this.#go(State.ONLINE, State.DEGRADED);
    if (!flag && this.#state === State.DEGRADED) this.#go(State.DEGRADED, State.ONLINE);
  }

  #from(target) {
    if (this.#state === target) return;
    if (!ALLOWED[this.#state]?.includes(target)) {
      throw new Error(`illegal transition ${this.#state} -> ${target}`);
    }
    this.#state = target;
    this.#onState?.(this.#state);
  }

  #go(from, to) {
    if (this.#state === from) this.#from(to);
  }

  get state() { return this.#state; }
}
```

**Not:** `online-degraded` tetik eşiği **UNKNOWN** (`offline-web-arastirma.md` W-2) —
örnekte sayısal eşik yazılmamıştır.

---

## Ör-02 — Service Worker kaydı + precache (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `sw-register.js` (tasarım) |
| Amaç | `offline-kapsam.md` §1a-4 hazırlık el sıkışması: çevrimdışı kabuğu kurmak |
| Kanıt | `offline-web-arastirma.md:A-4` (O-4 `ServiceWorkerContainer` + O-3 SW API — 2 kaynak) |
| Durum | TASARIM İDDİASI — repo'da `serviceWorker` 0 isabet |

```javascript
// sw-register.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

export async function registerOfflineShell(win, scope = '/') {
  if (!('serviceWorker' in win.navigator)) return null; // O-3 destek yok
  const reg = await win.navigator.serviceWorker.register(scope); // O-4
  await win.navigator.serviceWorker.ready;                       // O-4 (ready)
  return reg;
}
```

```javascript
// sw.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

const PRECACHE = '/offline-manifest.json'; // §1a-1 hazir listesi (ornek-04)

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open('coremusic-offline-v1')     // O-5 CacheStorage
      .then((cache) => cache.addAll([PRECACHE]))
      .then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(self.clients.claim());
});
```

**Not:** precache/on-demand stratejisi seçimi **UNKNOWN** (frontmatter `open_questions`);
önbellek adı `coremusic-offline-v1` tasarım iddiasıdır.

---

## Ör-03 — Offline-first fetch yakalayıcı (Vanilla JS ES6+)

| Alan | Değer |
|---|---|
| Dosya | `sw-fetch.js` (tasarım) |
| Amaç | Ağ yokken hazır yanıtı sunmak; `stale` işaretini taşımak (kapsam §3a · §5 H-3) |
| Kanıt | `offline-web-arastirma.md:A-1` (O-1 + O-2 + O-3) · `A-3` (O-6 `Cache.match` + O-5) |
| Durum | TASARIM İDDİASI |

```javascript
// sw-fetch.js — TASARIM IDDIASI (uretim kodu degildir)
'use strict';

self.addEventListener('fetch', (event) => {
  const req = event.request;
  if (req.method !== 'GET') return;

  event.respondWith(
    caches.match(req).then((hit) => {            // O-6: hazir yaniti bul
      if (hit) {
        // §3a: stale isareti yanit basligindan okunur (esik/son tarih YOK — UNKNOWN)
        const stale = hit.headers.get('X-Offline-Stale') === '1';
        if (stale) {
          // silme YOK (kapsam §5 H-3 / §6/4): uyariyla kullan
          const warn = hit.clone();
          warn.headers.set('X-Offline-Warn', 'stale');
          return Promise.resolve(warn);
        }
        return hit;
      }
      return fetch(req).then((res) => {         // ag var: agdan al + kaydet
        const copy = res.clone();
        caches.open('coremusic-offline-v1')
          .then((cache) => cache.put(req, copy));
        return res;
      }).catch(() => caches.match('/offline-manifest.json')); // H-1: ag yok
    })
  );
});
```

---

## Ör-04 — Çevrimdışı envanter manifest endpoint'i (PHP 8.4)

| Alan | Değer |
|---|---|
| Dosya | `OfflineManifest.php` (tasarım) |
| Amaç | `offline-kapsam.md` §1a-1/§1a-5: "ne hazır, ne bekliyor" listesini JSON taşımak |
| Kanıt | **Repo:** `MusicRepository.php:53-55` (`prepare`) · `:57-66` (`execute` + `fetch`) — PDO deseni birebir |
| Durum | Tasarım iddiası; tablo/alan adları UNKNOWN (envanter şeması K007 — kapsam §2/4) |

```php
<?php
declare(strict_types=1);

// OfflineManifest.php — TASARIM IDDIASI (uretim kodu degildir)
// Repo kaniti: MusicRepository.php:53-55 (prepare), :57-66 (execute + fetch).

final class OfflineManifest
{
    public function __construct(private readonly PDO $pdo) {}

    /** Hazir + bekleyen envanter (kapsam §1a-1/§1a-5). Sema adlari UNKNOWN. */
    public function forUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT item_id, item_type, ready_state, stale_flag
             FROM offline_inventory
             WHERE user_id = :user_id
             ORDER BY ready_state, item_id'
        );
        $stmt->execute([':user_id' => $userId]);

        $ready = [];
        $pending = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            // stale = varlık düzeyi işaret (kapsam §3a) — eşik burada ÜRETİLMEZ
            if ($row['ready_state'] === 'ready') {
                $ready[] = [
                    'id' => (int)$row['item_id'],
                    'type' => (string)$row['item_type'],
                    'stale' => (bool)$row['stale_flag'],
                ];
            } else {
                $pending[] = (int)$row['item_id'];
            }
        }

        header('Content-Type: application/json');
        header('Cache-Control: private, no-store'); // kisisel envanter (karsilastir: downloads ornek-04)
        return ['ready' => $ready, 'pending' => $pending];
    }
}
```

**Not:** `offline_inventory` tablo adı **tasarım iddiasıdır** (şema K007'de — kapsam §2/4);
envanterde "sil" davranışı **yoktur** (kapsam §6/4 kapanmadan otomatik temizlik YOK).

---

## Kapsam dışı (bu dosyada YOK)

- İndirme kuyruğu/Range resume → `downloads/downloads-ornekler.md`.
- Önbellek boyutu/LRU/TTL sayıları → `media-cache/` kardeşleri + kapsamların §6/§4 açık soruları (UNKNOWN).
- Senkron çakışma çözümü → sahiplik belirsiz (`offline-kapsam.md` §6/2) — örenek **üretilmez**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
