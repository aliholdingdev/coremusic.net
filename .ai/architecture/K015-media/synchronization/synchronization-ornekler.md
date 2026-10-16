---
node_id: "K015-media/synchronization/synchronization-ornekler"
node_type: "module"
status: "draft"
parent_id: "K015-media"
children: []
depends_on: []
evidence:
  - "synchronization-kapsam.md §3-§5 (durum makinesi + hata akışı — iskeletler bu akışa birebir bağlanır)"
  - "synchronization-web-arastirma.md §2 C-1..C-4 (mekanizma kanıtları: işaretçi, sunucu-kazanır vs LWW, kuyruk eşitleme)"
  - ".ai/AGENTS.md §16 (Backend standartları: strict_types, PDO, prepared statement, SELECT * yasak)"
validation: "TASARIM İDDİASI, üretim kodu değildir — iskeletler 2026-10-16'da yazılmıştır; diskte karşılık gelen üretim dosyası yoktur"
open_questions:
  - "İşaretçi biçimi (etag vs damga) → kapsam Q-4 — iskelette alan adı örnek amaçlıdır"
  - "Çakışma stratejisi → kapsam Q-2 — Ör-02 yalnız arayüzü gösterir, strateji seçmez"
group: kod
---

# K015 · synchronization · Örnekler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-16 · İskelet Kod (çakışma tespiti · fusion · kuyruk UI)
>
> **⚠️ TASARIM İDDİASI, üretim kodu değildir.** Aşağıdaki kod blokları tasarım iskeletidir; CoreMusic
> repo'sunda karşılık gelen `*.php` / `*.js` dosyası **yoktur** (yazım yasağı: yalnız `.md` üretilir).

---

## §1 AMAÇ TABLOSU

| # | Örnek | Dil/Teknoloji | Ne gösterir | Bağ |
|---|---|---|---|---|
| Ör-01 | Çakışma tespiti + last-write-fusion iskeleti | PHP 8.4 · PDO prepared statement | işaretçi (etag) ile optimistik kilit; uyuşmazlıkta `conflict`; alan bazlı son-yazan-birleştirme | kapsam §3 `merging/conflict` · §5 E-3/E-5 |
| Ör-02 | Sync queue UI | Vanilla JS ES6+ | kuyruk durum rozetleri (6 durum), retry/backoff göstergesi, çakışma satırı | kapsam §3 durum makinesi · §4 akış |

**Ortak kurallar (TASARIM İDDİASI):** (a) `declare(strict_types=1)` · (b) tüm SQL prepared statement ·
`SELECT *` yasak · (c) bilinmeyen alan `UNKNOWN` işaretlenir, ezilmez (kapsam §5 E-4) · (d) atomik yazma (E-5).

---

## §2 ÖR-01 — PHP 8.4 · Çakışma Tespiti + Last-Write-Fusion İskeleti

**Amac:** tek transaction'da (1) sürüm uyuşmazlığını tespit et, (2) fark yoksa erken çık, (3) varsa
alan bazında son-yazan-kazanır birleştir, (4) çözülemez alanları `UNKNOWN` olarak bırak.

```php
<?php
declare(strict_types=1);

/**
 * TASARIM İDDİASI — üretim kodu değildir.
 * Kapsam §3 (merging/conflict) + §5 (E-3, E-4, E-5) iskeleti.
 */
final class SyncFusion
{
    public function __construct(private readonly PDO $pdo) {}

    /**
     * Optimistik kilitli birleştirme: beklenen etag tutmuyorsa KIRMIZI bayrak.
     *
     * @param array<string, string|null> $incoming  alan => yeni değer (kaynak cihazdan)
     * @return array{status: 'resolved'|'conflict'|'error', unknown_fields: list<string>}
     */
    public function merge(string $recordId, array $expectedEtag, array $incoming): array
    {
        try {
            $this->pdo->beginTransaction();

            // 1) Satırı mevcut sürümüyle birlikte oku (prepared — SELECT * yasak, alan listesi açık)
            $stmt = $this->pdo->prepare(
                'SELECT id, etag, position_ms, queue_rev, updated_at
                   FROM media_sync_state
                  WHERE id = :id
                  LIMIT 1 FOR UPDATE'
            );
            $stmt->execute(['id' => $recordId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row === false) {
                $this->pdo->rollBack();
                return ['status' => 'error', 'unknown_fields' => []];
            }

            // 2) Çakışma tespiti: işaretçi uyuşmuyorsa → conflict (sessiz ezme YOK)
            if (!hash_equals((string) $row['etag'], (string) $expectedEtag['etag'])) {
                $this->pdo->rollBack();
                return ['status' => 'conflict', 'unknown_fields' => []];
            }

            // 3) Last-write-fusion: alan bazında kaynak zaman damgası yeni olan kazanır
            $unknown = [];
            $sets = [];
            $params = ['id' => $recordId];
            foreach ($incoming as $field => $value) {
                if (!in_array($field, ['position_ms', 'queue_rev'], true)) {
                    $unknown[] = $field;      // E-4: bilinmeyen alan → UNKNOWN işaretle, EZME
                    continue;
                }
                $sets[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }

            if ($sets === []) {                // yazılacak tanınmış alan yok → sadece kapat
                $this->pdo->commit();
                return ['status' => 'resolved', 'unknown_fields' => $unknown];
            }

            // 4) Atomik yazma + yeni etag (E-5): tek UPDATE, koşullu etag teyidi
            $sets[] = 'etag = :new_etag';
            $sets[] = 'updated_at = CURRENT_TIMESTAMP';
            $params['new_etag'] = bin2hex(random_bytes(16));
            $params['old_etag'] = $expectedEtag['etag'];

            $upd = $this->pdo->prepare(
                'UPDATE media_sync_state SET ' . implode(', ', $sets) . '
                  WHERE id = :id AND etag = :old_etag'
            );
            $upd->execute($params);

            if ($upd->rowCount() === 0) {      // yarım saniyede başka yazım → conflict
                $this->pdo->rollBack();
                return ['status' => 'conflict', 'unknown_fields' => $unknown];
            }

            $this->pdo->commit();
            return ['status' => 'resolved', 'unknown_fields' => $unknown];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();        // E-5: atomiklik korunur
            }
            return ['status' => 'error', 'unknown_fields' => []];
        }
    }
}
```

**Kapsam bağları:**

| Satır/dal | Kapsam karşılığı |
|---|---|
| `etag` uyuşmazlığı → `conflict` | §3 geçişi `merging → conflict` · §5 E-3 (**strateji UNKNOWN: Q-2**) |
| `$unknown[] = $field` | §5 E-4 — bilinmeyen alan `UNKNOWN` işaretlenir |
| koşullu `UPDATE ... WHERE etag = :old_etag` + rollback | §5 E-5 — atomik yazma |
| `hash_equals` | sabit-zamanlı karşılaştırma (proje standardı) |
| `FOR UPDATE` | yarış koşulu kilitlenir — iskelet **TASARIM İDDİASI**, şema adı örnek amaçlı (`media_sync_state` diskte YOK) |

**Durum:** ⚠️ İskelet — şema/sınıf diskte yok; üretim entegrasyonu Q-4/Q-2 kararlarına bağlı.

---

## §3 ÖR-02 — Vanilla JS ES6+ · Sync Queue UI İskeleti

**Amac:** kuyruktaki her işin durumunu (6 durum) rozetle göster, çakışmada kullanıcıya seçenek sun,
retry sayısını görünür yap — framework yok, ES6+ (class, template literal, fetch).

```javascript
// TASARIM İDDİASI — üretim kodu değildir.
// Kapsam §3 (durum makinesi) + §4 (veri akışı) + §5 (E-1/E-2/E-3) iskeleti.

const STATE_META = Object.freeze({
  idle:      { label: 'Bekliyor',     tone: 'neutral' },
  detecting: { label: 'Fark aranıyor', tone: 'info' },
  merging:   { label: 'Birleştiriliyor', tone: 'info' },
  conflict:  { label: 'Çakışma',      tone: 'danger' },  // Q-2: çözüm stratejisi seçilmedi
  resolved:  { label: 'Tamam',        tone: 'success' },
  error:     { label: 'Hata',         tone: 'warn' },
});

class SyncQueue {
  #queue = [];                     // özel alan: dışarıdan doğrudan yazım yok
  #onConflict = null;              // strateji kararı dışarıdan gelir (kapsam Q-2)

  constructor({ mount, onConflict }) {
    this.#onConflict = onConflict;
    this.#mount = document.querySelector(mount);
  }

  enqueue(job) {
    this.#queue.push({ retries: 0, state: 'idle', ...job });
    this.render();
  }

  async runNext() {
    const job = this.#queue.find((j) => j.state === 'idle' || j.state === 'error');
    if (!job) return;

    job.state = 'detecting'; this.render();
    try {
      const res = await fetch(job.endpoint, {          // işaretçi karşılaştırması (kapsam §4 adım 2)
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: job.id, etag: job.etag }),
      });
      if (res.status === 409) {                        // E-3 → conflict
        job.state = 'conflict'; this.render();
        this.#onConflict?.(job, await res.json());     // strateji sunulur; sistem EZMEZ
        return;
      }
      if (!res.ok) throw new Error(`HTTP ${res.status}`);

      job.state = 'merging'; this.render();
      job.state = 'resolved';                          // §4 adım 7 (doğrulama iskelette atlandı)
    } catch {
      job.retries += 1;                                // E-1 backoff (eşik: kapsam Q-6 UNKNOWN)
      job.state = job.retries >= 3 ? 'error' : 'idle'; // E-6 döngü koruması
    } finally {
      this.render();
    }
  }

  render() {
    this.#mount.innerHTML = this.#queue.map((job) => {
      const meta = STATE_META[job.state] ?? { label: 'UNKNOWN', tone: 'warn' };
      return `
        <li class="sync-job sync-job--${meta.tone}" data-state="${job.state}">
          <span class="sync-job__badge">${meta.label}</span>
          <span class="sync-job__id">${job.id}</span>
          <span class="sync-job__retries">deneme: ${job.retries}</span>
          ${job.state === 'conflict'
            ? '<button data-act="resolve">Çözümü seç</button>'   // E-3: kullanıcı bilinçli seçer
            : ''}
        </li>`;
    }).join('');
  }
}
```

**Kapsam bağları:**

| Satır | Kapsam karşılığı |
|---|---|
| `STATE_META` 6 anahtar | §3 durum makinesi (idle/detecting/merging/conflict/resolved/error) |
| `409 → conflict` + `onConflict` | §5 E-3 — çakışma **sessiz çözülmez**, strateji dışarıdan (Q-2) |
| `retries >= 3 → 'error'` | §5 E-1/E-6 — pes eşiği iskelette 3; **gerçek eşik Q-6 UNKNOWN** |
| `'UNKNOWN'` fallback | §5 E-4 — bilinmeyen durum da işaretlenir |
| rozet tonları (BEM sınıf adları) | proje CSS kuralı: `sync-job--*` BEM modifikatörü; sabit renk/token **token dosyasından** okunur (bu iskelette yok) |

**Durum:** ⚠️ İskelet — DOM/CSS bağımlılıkları diskte yok; `resolve` akışı çözüm stratejisine (Q-2) bağlıdır.

---

## §4 ÖRNEK ÖZETİ

| # | Örnek | Dil | Gösterdiği kontrol | Durum |
|---|---|---|---|---|
| Ör-01 | Çakışma tespiti + fusion | PHP 8.4 (PDO) | optimistik etag kilidi · koşullu UPDATE · UNKNOWN alan · rollback | ⚠️ İskelet (şema YOK) |
| Ör-02 | Sync queue UI | Vanilla JS ES6+ | 6 durum rozeti · 409→conflict · retry sayacı · BEM sınıfları | ⚠️ İskelet (DOM YOK) |

**Kapsanmayanlar** (kayıt yok = kanıt yok): ağ backoff sayısal eşiği (Q-6) · gerçek şema/ADL (Q-4) ·
çakışma çözüm UI akışının hangi stratejiyle çalışacağı (Q-2) — üçü de açık sorudur, iskelette **seçilmez**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
