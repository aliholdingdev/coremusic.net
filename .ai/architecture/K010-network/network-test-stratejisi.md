---
node_id: K010-network/test-stratejisi
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
  - ".ai/architecture/K010-network/network-master.md §4 (doğrulanmış RFC'ler — RFC 2544 vb.) — okundu 2026-10-18"
  - ".ai/architecture/K010-network/network-master.md §3 (durum makinesi + backoff) — okundu 2026-10-18"
  - "assets.coremusic.net/tests/mocks/player-api.mock.js L130-L164 (SSE mock = test altyapısı gerçeği) — okundu 2026-10-18"
validation: "RFC 2544 network-master §4 ile hizalı; ölçüm araçları ⚠️ UNKNOWN"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — RFC 2544 ölçüm araçları (iperf3/netperf/spektro-analiz)
  repo'da yok; test betikleri üretim öncesi .ai/templates/ ile doğrulanacak.
group: kod
---

# K010-network — Test Stratejisi

## §1 — ELI15: Bu nedir?

Ağ katmanını nasıl sınayacağımızın planı. Üç soru: (1) bağlantı koparsa
sistem kendini toparlıyor mu? (2) akış/seek doğru byte'ları veriyor mu?
(3) ölçüm (RFC 2544) yol gecikmesini/bant genişliğini doğru mu veriyor?
Testler "koparmayı" kasıtlı yapar: backoff sırası, offline kuyruk, ETag
doğruluğu.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Test piramidi, senaryolar (kopma/backoff/range/SSE/sync), kabul kriterleri, RFC 2544 |
| Kapsam dışı | DSP ölçüm (K004) · firmware test (K005) · auth denetimi (K012) |

## §3 — Test piramidi

| Katman | Ne test edilir | Araç | Pay |
|---|---|---|---|
| Birim | Backoff formülü, durum makinesi geçişleri, ETag hesabı, kuyruk sırası | PHPUnit 12 · Vitest (JS) | %60 |
| Entegrasyon | Kasıtlı kopma → reconnect; Range→206; SSE event şeması; offline drain | Vitest + SSE mock (`player-api.mock.js` ✓) · fake server | %30 |
| Ölçüm | RFC 2544: yol gecikmesi, throughput, frame loss | iperf3/netperf ⚠️ | %10 |

## §4 — Senaryolar

### 4.1 Birim

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-N1 | Backoff: attempt 0..8 | 2^n üstel, üst 30 s, jitter %25; sabit aralık ASLA | KN-N3 · kod §4.1/§4.9 |
| T-N2 | Durum makinesi geçersiz geçiş (IDLE→CONNECTED) | Reddedilir, durum değişmez | C-net-1 · kod §4.2 |
| T-N3 | ETag: path|size|mtime değişince | ETag değişir (cache kırılır) | `MusicStreamHandler.php` L80 ✓ |
| T-N4 | Kuyruk: remote hata → w.offer + break | Sıra korunur, kısmi drain yok | kod §4.6 |
| T-N5 | If-Range + eski etag | Sunucu 200 (baştan), istemci mode=wb | kod §4.7 · C-net-3 |

### 4.2 Entegrasyon

| # | Senaryo | Beklenen | Kanıt |
|---|---|---|---|
| T-N6 | WS kopar → backoff ile yeniden bağlan | CONNECTED'e dönüş; attempt sıfırlanır | C-net-1 · RN-1 |
| T-N7 | Range: 0-1048575 aralığı | 206 + Content-Range; Accept-Ranges: bytes | `MusicStreamHandler.php` L16-L18 ✓ |
| T-N8 | SSE: {seekPct, elapsed} event'i | İstemci JSON parse edip UI'a iletir | `player-api.mock.js` L136-L156 ✓ |
| T-N9 | Offline: 3 yazım kuyruğa → bağlantı | 3'ü sırayla uygulanır, idempotencyKey çift engeli | KN-N4 · RN-9 |
| T-N10 | Token expiry (WS) | Yeniden auth; sessiz kopma YOK | RN-3 (K012) ⚠️ |

### 4.3 Ölçüm (RFC 2544)

| # | Senaryo | Beklenen | Araç |
|---|---|---|---|
| T-N11 | Yol gecikmesi (round-trip) | raporlanır (ms); hedef ≤ 50 ms LAN | iperf3 ⚠️ |
| T-N12 | Throughput (stream 1 MiB chunk) | bant genişliğini doldurur; düşüş alarm | iperf3 ⚠️ |
| T-N13 | Frame loss / jitter | RFC 2544 çerçevesinde rapor | netperf ⚠️ |
| T-N14 | Heartbeat periyodu kalibrasyonu | K016 metrikleriyleRN-5 kapatılır | K01007 ⚠️ |

## §5 — Kabul kriterleri

| Kriter | Eşik | Kapı |
|---|---|---|
| Backoff | Üstel + jitter; üst 30 s; sabit aralık yok | T-N1 — BLOCK |
| Durum makinesi | Geçersiz geçiş reddedilir (tüm 6 geçiş) | T-N2 — BLOCK |
| Range→206 | Tek aralık 206; yanlış aralık 416 | T-N7 — BLOCK |
| ETag doğruluğu | path|size|mtime üçlüsü değişince cache kırılır | T-N3 — BLOCK |
| Offline drain | Sıra korunur; kısmi uygulama yok; idempotent | T-N9 — BLOCK |
| RFC 2544 ölçüm | Yol gecikmesi/throughput raporlanır | T-N11/12/13 ⚠️ |
| Birim kapsamı | K010 çekirdek (backoff/FSM/ETag/kuyruk) %90+ | release gate |

## §6 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| RFC 2544 referansı | `network-master.md` §4 | ✓ Okundu 2026-10-18 |
| SSE mock test altyapısı | `player-api.mock.js` L130-L164 | ✓ Okundu 2026-10-18 |
| Durum makinesi/backoff | `network-master.md` §3 · kod §4.1/§4.2 | ✓ 2026-10-18 |
| Ölçüm araçları (iperf3/netperf) | repo'da yok | ⚠️ VERIFICATION REQUIRED |
| RN-5 heartbeat kalibrasyonu | K016 metrikleri hâlâ yok | ⚠️ VERIFICATION REQUIRED |

## §7 — Açık sorular

| # | Soru | Sahip |
|---|---|---|
| 1 | RFC 2544 ölçüm ortamı (LAN mı, WAN mı) sabitlenecek mi? | K010-network |
| 2 | T-N10 (WS token expiry) testi K012-security ile mi yazılır? | K012-security |
| 3 | iperf3/netperf ortamı CI'ye mi girer, elle mi koşulur? | K007-sandbox |

## §8 — Ölçüm raporu şablonu (RFC 2544)

Her ölçüm testi (T-N11..T-N13) sonrası bu şablon doldurulur:

```yaml
test_id: T-N11
ortam: lan | wan          # sabitlenecek ⚠️
arac: iperf3 | netperf
sure_saniye: 60
olcumler:
  latency_ms: 0.0         # round-trip
  throughput_mbps: 0.0
  frame_loss_pct: 0.0
  jitter_ms: 0.0
sonuc: gecti | gecmedi | raporlandi
tarih: YYYY-MM-DD
not: "⚠️ araç/ortam teyitsizse yaz"
```

### Kapı karar matrisi

| Ölçüm | Eşik | Aşarsa/ne olur |
|---|---|---|
| Backoff/jitter | Üstel + %25 jitter; üst 30 s | BLOCK (T-N1) |
| Durum makinesi | 6 geçiş, geçersiz geçiş reddi | BLOCK (T-N2) |
| Range→206 | Tek aralık 206; yanlış 416 | BLOCK (T-N7) |
| Offline drain | Sıra + idempotency | BLOCK (T-N9) |
| RFC 2544 ölçüm | raporlanır (hedef LAN ≤50 ms) | rapor; BLOCK değil ⚠️ |

### Araç bağımlılıkları (durum)

| Araç | Kullanım | Durum |
|---|---|---|
| Vitest + SSE mock | T-N8 (gerçek mock dosyası var ✓) | ✓ Repo |
| PHPUnit 12 | T-N1..T-N5 birim | ✓ Repo (test altyapısı) |
| iperf3 / netperf | T-N11..T-N13 RFC 2544 | ⚠️ Repo'da yok |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
