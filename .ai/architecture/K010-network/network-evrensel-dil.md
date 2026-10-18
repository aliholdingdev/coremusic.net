---
node_id: K010-network/evrensel-dil
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
  - ".ai/architecture/K010-network/network-master.md §2-§4 (terim kaynakları, RFC'ler) — okundu 2026-10-18"
  - "home.coremusic.net/include/Stream/MusicStreamHandler.php L9-L20 (Range/ETag terimleri) — okundu 2026-10-18"
  - "assets.coremusic.net/tests/mocks/player-api.mock.js L130-L156 (SSE/EventSource terimleri) — okundu 2026-10-18"
validation: "18 terim EN/TR; K003 (backend) + K004 (algoritma) sözlükleriyle KAPSAM SINIRI NET"
open_questions: |
  ⚠️ VERIFICATION REQUIRED — network-evrensel-dil.md beyazliste dışı mevcut dosya
  değil (üretildi); sonraki turda K003/K004 sözlükleriyle çakışma taraması yapılacak.
group: mimari
---

# K010-network — Evrensel Dil (EN/TR Sözlük)

## §1 — ELI15: Bu nedir?

Ağ terimlerinin tek karşılık defteri. "Bağlantı koptu mu, yeniden
bağlanıyor mu?" gibi tartışmalarda iki taraf aynı kelimeyi kullansın diye.
K003 sözlüğü **backend/sürücü**, K004 sözlüğü **DSP algoritma**, bu sözlük
**ağ/iletişim** terimlerini sabitler — üçü ayrı dosyadır, çakışmaz.

## §2 — Kapsam

| Satır | İçerik |
|---|---|
| Kapsam içi | Ağ terimleri: transport, durum, yeniden bağlanma, akış, ölçüm |
| Kapsam dışı | Backend terimleri (K003) · DSP terimleri (K004) · auth terimleri (K012) |

## §3 — Sözlük (EN ↔ TR, 18 terim)

| # | English | Türkçe | Kısa tanım (TR) | Kaynak |
|---|---|---|---|---|
| 1 | Server-Sent Events (SSE) | Sunucu Gönderimli Olaylar | Tek yönlü HTTP canlı akışı (EventSource) | `player-api.mock.js` L130 ✓ |
| 2 | EventSource | Olay Kaynağı | SSE istemci API'si (auto-reconnect) | C-net-2 |
| 3 | WebSocket | WebSocket (Web Yuvası) | Çift yönlü kalıcı bağlantı (101) | network-master §2 |
| 4 | Byte-range | Bayt aralığı | Dosyanın bir dilimi (Seek/HTTP Range) | `MusicStreamHandler.php` L16 ✓ |
| 5 | Partial Content | Kısmi İçerik | 206: istenen aralık döndü | C-net-3 |
| 6 | ETag | İçerik Etiketi | Kaynak sürüm etiketi; cache doğruluğu | `MusicStreamHandler.php` L17 ✓ |
| 7 | If-Range | Aralık-İse | "etag aynıysa 206, değiştiyse 200" | kod-ornekleri §4.7 |
| 8 | Backoff | Geri çekilme (bekleme) | Kopmadan sonra üstel artan bekleme | KN-N3 |
| 9 | Jitter | Jitter (rastgelelik) | Backoff'a %25 rastgelelik (thundering herd önleme) | KN-N3 |
| 10 | Thundering herd | Sürü halinde istek | Aynı anda patlayan istemci dalgası | RN-2 |
| 11 | Heartbeat | Nabız (canlılık) | Periyodik ping; kopmayı erken fark | C-net-5 |
| 12 | Idempotency key | Teklik anahtarı | Aynı yazımın iki kez uygulanmasını engelleyen UUID | kod-ornekleri §4.11 |
| 13 | Offline-first | Çevrimdışı öncelikli | Ağ yokken çalışır; kuyruk + sonra uygula | KN-N4 |
| 14 | Degraded | Bozulmuş (ağır) | Bağlı ama yavaş; eşiği aşınca RECONNECTING | C-net-1 |
| 15 | Throughput | Verim (bant genişliği) | Saniyedeki bayt (RFC 2544 ölçümü) | network-master §4 |
| 16 | Round-trip latency | Gidiş-dönüş gecikme | İstek→yanıt toplam süresi (ms) | RFC 2544 |
| 17 | Frame loss | Çerçeve kaybı | Paket/çerçeve kayıp oranı (RFC 2544) | network-master §4 |
| 18 | Rate limit | İstek sınırı | 429 + Retry-After; storm önleme | RN-10 |

## §4 — Yazım kuralları

| Kural | Örnek |
|---|---|
| HTTP kodları olduğu gibi yazılır | "206", "416", "429" — "kısmi içerik 206" |
| Durum adları kodda İngilizce, dokümanda çift ilk kullanımda | "RECONNECTING (yeniden bağlanıyor)" |
| Birimler: ms, Mbps, Hz — Türkçe ek almaz | "50 ms", "100 Mbps" |
| Ondalık nokta (CI uyumu) | "0.25" — "0,25" DEĞİL |

## §5 — Doğrulama

| İddia | Kanıt | Durum |
|---|---|---|
| 18 terim EN/TR | bu dosya §3 | ✓ 2026-10-18 |
| Terim kaynakları (gerçek dosya + master) | `player-api.mock.js` L130 · `MusicStreamHandler.php` L16-L17 · `network-master.md` §4 | ✓ 2026-10-18 |
| K003/K004 sözlükleriyle sınır | K003=backend · K004=algoritma · bu=ağ | ✓ 2026-10-18 |
| Mevcut dosya çakışması | beyazliste dışı dosya yoktu → bu dosya YENİ | ✓ 2026-10-18 |

## §5 — Terim üretimi kuralı (sonraki terimler için)

```yaml
en: "English term"
tr: "Türkçe karşılık"
tanim: "tek cümlelik kısa tanim (TR)"
kaynak: "<dosya> §<bölüm>" | "resmi doküman (RFC/MS doc)"
sinif: transport | durum | yeniden-baglanma | akis | olcum
```

### Sınıf dağılımı (bu sürüm)

| Sınıf | Adet | Terimler (kısa) |
|---|---|---|
| Transport | 3 | #1, #2, #3 |
| Akış (stream) | 4 | #4, #5, #6, #7 |
| Yeniden bağlanma | 4 | #8, #9, #10, #14 |
| Durum/sync | 4 | #11, #12, #13, #18 |
| Ölçüm | 3 | #15, #16, #17 |

### Kapsam sınırı komşuları

| Komşu sözlük | Kapsamı | Ayrım çizgisi |
|---|---|---|
| K003-audio/evrensel-dil | Backend/donanım (ASIO, xrun, hot-plug) | "Ses donanıma nasıl ulaşır" |
| K004-dsp/evrensel-dil | Algoritma (biquad, LUFS, THD+N) | "Ses nasıl işlenir" |
| Bu dosya (K010) | Ağ (SSE, backoff, ETag) | "Ses nasıl taşınır" |

### HTTP durum kodları hızlı tablosu

| Kod | Ad | K010 kullanımı |
|---|---|---|
| 206 | Partial Content | Range aralığı döndü (KN-N2) |
| 416 | Range Not Satisfiable | Yanlış aralık (T-N7) |
| 429 | Too Many Requests | Rate limit; Retry-After (RN-10) |
| 101 | Switching Protocols | WebSocket açılışı (KN-N5) |
| 200 | OK | If-Range tutmazsa baştan indirme |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
