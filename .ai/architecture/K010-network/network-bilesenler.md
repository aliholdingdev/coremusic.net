---
node_id: K010-network/bilesenler
node_type: subdomain
status: draft
parent_id: K010-network
children: []
depends_on:
  - K006-codebase
  - K008-superapp
  - K009-api
  - K011-ui-kit
  - K012-security
  - K016-observability
evidence:
  - ".ai/architecture/K010-network/network-master.md §2 (7 çocuk + 5 comms torunu) — okundu 2026-10-18"
  - ".ai/architecture/K010-network/network-master.md §3 (durum makinesi) — okundu 2026-10-18"
  - ".ai/architecture/K010-network/network-kapsam.md (kapsam içi/dışı sınır tablosu) — okundu 2026-10-18"
  - "assets.coremusic.net/tests/mocks/player-api.mock.js L130-L164 (SSE EventSource mock) — okundu 2026-10-18"
validation: "7 çocuk + 5 torun network-master §2 ile birebir — doğrulandı 2026-10-18"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — network-bagimliliklar.md'deki K0100-identitiy ölü
  referansı (gerçeği K012-security olabilir) düzeltme beyazliste dışı; silinmedi.
group: mimari
---

# K010-network — Bileşenler

## §1 — ELI15: Bu nedir?

Uygulamanın internetle konuştuğu her kapı bu dosyada. Ses akışı (stream),
canlı ilerleme (SSE), gerçek zamanlı bildirim (WebSocket), API çağrıları
(REST), senkronizasyon (offline-first). Hepsi bir **durum makinesi** ile
yönetilir: bağlı → kopuk → yeniden bağlanıyor. Amaç: kullanıcı "bağlantı
koptu" görmeden sistem kendini toparlasın.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Ağ bileşenleri: akış, SSE, WebSocket, REST, sync, durum makinesi, yeniden bağlanma |
| Kapsam dışı | DSP (K004) · firmware (K005) · UI widget (K011) · güvenlik denetimi (K012) |

## §3 — Grup eşlemesi (K005 kanonu)

7 çocuk + 5 comms torunu (network-master §2):

| Çocuk K | Rol | Torunlar |
|---|---|---|
| K01001-stream | Byte-range ses akışı (206) | — |
| K01002-sse | Server-Sent Events (canlı progress) | — |
| K01003-websocket | Gerçek zamanlı çift yönlü | K01003a-comms-session · K01003b-comms-audio · K01003c-comms-presence · K01003d-comms-notification · K01003e-comms-sync |
| K01004-rest | API çağrıları (K009-api köprüsü) | — |
| K01005-sync | Offline-first senkronizasyon | — |
| K01006-retry | Yeniden bağlanma/backoff politikası | — |
| K01007-probe | Sağlık/heartbeat probu (K016-observability) | — |

## §4 — Bileşen sözleşmeleri

```mermaid
graph TD
  UI[K011-ui-kit] -->|HTTP/WS| MW[Durum makinesi<br/>C-net-1]
  MW --> S[K01001-stream<br/>206 range]
  MW --> SSE[K01002-sse<br/>EventSource]
  MW --> WS[K01003-websocket<br/>+ 5 comms]
  MW --> R[K01004-rest<br/>K009-api]
  MW --> Y[K01005-sync<br/>offline-first]
  MW --> BK[K01006-retry<br/>backoff]
  MW --> P[K01007-probe<br/>heartbeat]
  BK --> MW
  P --> OBS[K016-observability]
  WS -.ölü referans⚠️|-> K0100-identitiy
```

| Sözleşme | Tanım | Kanıt |
|---|---|---|
| C-net-1 | Durum makinesi: `IDLE → CONNECTING → CONNECTED → DEGRADED → RECONNECTING → CONNECTED/FAILED`; backoff üstel (jitter) | network-master §3 |
| C-net-2 | SSE: `EventSource` auto-reconnect; progress event'i JSON `{seekPct, elapsed}` | `player-api.mock.js` L136-L156 ✓ |
| C-net-3 | Stream: HTTP Range tek aralık → 206; ETag + Last-Modified + Accept-Ranges | `MusicStreamHandler.php` L16-L18 ✓ |
| C-net-4 | Retry: üstel backoff + jitter; max deneme ve timeout | network-master §3 |
| C-net-5 | Probe: heartbeat periyodu; K016'ya metrik | network-master §3 |
| C-net-6 | Sync: offline-first; çakışma çözümü (LWW/CRDT ⚠️ karar) | network-master §3 |

### Durum makinesi (C-net-1)

| Geçiş | Tetik | Sonuç |
|---|---|---|
| IDLE → CONNECTING | istek başlangıcı | bağlantı kur |
| CONNECTING → CONNECTED | 2xx/101 | akış canlı |
| CONNECTED → DEGRADED | yavaş/timeout eşiği | kullanıcıya "yavaş" sinyali |
| DEGRADED → RECONNECTING | kesin kopma | backoff ile yeniden dene |
| RECONNECTING → CONNECTED | başarı | durumu geri yükle |
| RECONNECTING → FAILED | max deneme | offline mod (K01005) |

## §5 — Bağımlılıklar (özet)

| Yön | K | Ne alıyor / veriyor |
|---|---|---|
| Gelen | K009-api | REST uçları, SSE kaynakları |
| Gelen | K012-security | Auth token, CSP (WS/SSE doğrulama) |
| Giden | K011-ui-kit | Bağlantı durumu göstergesi |
| Giden | K016-observability | Metrik/olay (probe, retry, xrun benzeri sayaç) |

## §6 — Kanıt ve doğrulama durumu

| İddia | Kanıt | Durum |
|---|---|---|
| 7 çocuk + 5 torun | `network-master.md` §2 | ✓ Doğrulandı 2026-10-18 |
| Durum makinesi 6 geçiş | `network-master.md` §3 | ✓ Okundu 2026-10-18 |
| SSE event şeması | `player-api.mock.js` L136-L156 | ✓ Okundu 2026-10-18 |
| Stream 206/ETag sözleşmesi | `MusicStreamHandler.php` L16-L18 | ✓ Okundu 2026-10-18 |
| K0100-identitiy (ölü referans) | `network-bagimliliklar.md` | ⚠️ Tespit, silinmedi |

## §7 — Açık sorular

| # | Soru | Sahip |
|---|---|---|
| 1 | Sync çakışma çözümü: LWW mi, CRDT mi? | K010-network |
| 2 | WebSocket auth yenileme (token expiry) nasıl? | K012-security |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
