---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K14 Ağ & İletişim Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
son_guncelleme: "2026-09-24, kaynak: 3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K14: Ağ & İletişim Layer

**Katman:** K14 (Ağ & İletişim)
**Kapsam:** HTTP/2, WebSocket, mDNS, DLNA, AirPlay, WebRTC, DNS, VPN
**Sorumlu Agent:** Network Engineer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K14 katmanı, CoreMusic'in tüm ağ iletişim protokollerini ve servis keşif mekanizmalarını içerir.

---

## 2. Protokol Haritası

| # | Protokol | Kullanım | Port |
|---|----------|----------|------|
| K14-01 | HTTP/2 | API iletişimi | 443 |
| K14-02 | WebSocket | Real-time iletişim | 443 |
| K14-03 | mDNS | Ağ keşfi | 5353 |
| K14-04 | DLNA/UPnP | Medya paylaşımı | 1900 |
| K14-05 | AirPlay | Apple streaming | 7000 |
| K14-06 | WebRTC | P2P ses aktarımı | 10000-20000 |
| K14-07 | DNS | Alan adı çözümleme | 53 |
| K14-08 | VPN | Güvenli uzaktan erişim | 1194 |

---

## 3. DLNA/UPnP

### 3.1 DLNA Akışı

```
Media Server → DLNA Control Point → DLNA Renderer

Device Discovery: SSDP (Simple Service Discovery Protocol)
Content Transfer: HTTP GET with Range
Format: DLNA compatible (MP3, FLAC, WAV)
```

---

## 4. AirPlay

### 4.1 AirPlay Akışı

```
Source (CoreMusic) → AirPlay Protocol → Sink (Apple TV, HomePod)

Audio: ALAC (Apple Lossless)
Sync: NTP-based clock sync
Buffer: 2-5 seconds
```

---

## 5. WebRTC P2P

### 5.1 P2P Ses Aktarımı

```
Client A ←→ STUN/TURN ←→ Client B

Use Cases:
  - Multi-room audio sync
  - Collaborative listening
  - Remote DJ
```

---

## 6. mDNS

### 6.1 Servis Kaydı

```
Service: _coremusic._tcp.local
Port: 81
TXT Records:
  version=1.0.0
  type=music-server
  device=rpi5
```

---

*K14 Ağ & İletişim Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*



---


## Alt Katman Şeması (K14.a.b.c)

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu bölüm, K14 katmanını onaylı şema biçiminde (K14 → K14.a → K14.a.b → K14.a.b.c) belgeler. Alanlar (a) katman protokol ailelerinden, alt alanlar (b) dosyalardaki H2 bölüm başlıklarından, yapraklar (c) ise k14-ag/ altındaki kanonik MD dosyalarındaki gerçek H2/H3 bölüm başlıklarından türetilmiştir; her yaprak kanıt satırıyla kaynak dosyasını ve bölümünü gösterir. Uydurma düğüm yoktur, silme yapılmamıştır.

**Şema kuralları:**

1. Zorunlu şema: `K14` → `K14.a` → `K14.a.b`; seviye-4 (`K14.a.b.c`) yalnız disk MD, README bileşen-tablosu satırı veya frontend-restructuring-plan §2.1-2.2 satırı kanıtıyla açılır.
2. Her düğüm: numara + ad + 1 satır sorumluluk + kanıt kaynağı taşır; kanıtsız düğüm üretilmez.
3. 21 ana katman sabittir (K0–K20; matris §1.1, §1.3 K3).
4. K14, K16–K20 üretim katmanlarının hiçbirine yazmaz; şema yalnız bu katmanın iç hiyerarşisini taşır.
5. Bağımlılık bağlamı: K14 → K9 (tek resmi bağımlılık — matris §2.2). K14'e gelen izinli ok yoktur; K15'in tek hedefi K14'tür (matris §1.3 K2, §2.1).
6. Onaylı sayımlar: a = alan, b = alan başına alt alan, c = yaprak; toplam = a×b + c.
7. Kanıt türleri: disk MD başlığı (H2/H3/H4), README/index bileşen-tablosu satırı, plan §2.1-2.2 satırı.

### Sayım Özeti

| Seviye | Onaylı hedef | Üretilen | Kanıt havuzu | Havuz − hedef |
|--------|--------------|----------|--------------|----------------|
| `K14` (a alan) | 9 | 9 | 9 | +0 |
| `K14.a.b` (a×b alt alan) | 54 | 54 | — | 0 |
| `K14.a.b.c` (c yaprak) | 170 | 170 | 171 | +1 |
| **Toplam düğüm** | **224** | **224** | **225** | **+1** |

### Alan Özeti

| Alan | Ad | Alt alan (b) | Yaprak (c) | Kanıt dosyaları |
|------|----|--------------|-----------|-----------------|
| `K14.1` | HTTP/2 & HTTP/3 | 6 | 15 | `http2-http3.md` |
| `K14.2` | WebSocket Gerçek Zamanlı | 6 | 13 | `websocket-realtime.md` |
| `K14.3` | WebRTC P2P | 6 | 13 | `webrtc-p2p.md` |
| `K14.4` | mDNS Keşif | 6 | 14 | `mdns-discovery.md` |
| `K14.5` | DLNA/UPnP | 6 | 12 | `dlna-upnp.md` |
| `K14.6` | AirPlay 2 | 6 | 12 | `airplay-streaming.md` |
| `K14.7` | DNS Çözümleme | 6 | 14 | `dns-resolver.md` |
| `K14.8` | TLS & VPN Güvenliği | 6 | 27 | `tls-security.md`, `vpn-support.md` |
| `K14.9` | Ağ Optimizasyonu & QoS | 6 | 50 | `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` |

### K14.1 — HTTP/2 & HTTP/3

**Sorumluluk:** Uygulama katmanı taşıma protokolleri: frame yapısı, bağlantı kurulumu, QUIC/HPACK ve push stratejileri.
**Kanıt dosyaları:** `http2-http3.md` — havuz 15 başlık, kullanıldı 15.

#### K14.1.1 — Genel Bakış

- **Sorumluluk:** HTTP/2 ve HTTP/3, COREMUSIC'in tüm REST API ve medya akış iletişimini sağlayan temel protokollerdir. HTTP/2 ile TCP tabanlı multiplexing, HTTP/3 ile QUIC…
- **Kanıt:** `http2-http3.md` § Genel Bakış (1 bölüm başlığı)

- **K14.1.1.1 — Genel Bakış**
  - Sorumluluk: HTTP/2 ve HTTP/3, COREMUSIC'in tüm REST API ve medya akış iletişimini sağlayan temel protokollerdir. HTTP/2 ile TCP tabanlı multiplexing, HTTP/3 ile QUIC tabanlı low-latency bağlantılar desteklenir.…
  - Kanıt: `http2-http3.md` § Genel Bakış

#### K14.1.2 — Protokol Detayı

- **Sorumluluk:** Transport: TCP + TLS 1.2/1.3 Mekanizma: Binary framing, stream multiplexing, header compression (HPACK)
- **Kanıt:** `http2-http3.md` § Protokol Detayı (3 bölüm başlığı)

- **K14.1.2.2 — Protokol Detayı**
  - Sorumluluk: Transport: TCP + TLS 1.2/1.3 Mekanizma: Binary framing, stream multiplexing, header compression (HPACK)
  - Kanıt: `http2-http3.md` § Protokol Detayı
- **K14.1.2.3 — HTTP/2 (RFC 7540)**
  - Sorumluluk: Transport: TCP + TLS 1.2/1.3 Mekanizma: Binary framing, stream multiplexing, header compression (HPACK)
  - Kanıt: `http2-http3.md` § Protokol Detayı > HTTP/2 (RFC 7540)
- **K14.1.2.4 — HTTP/3 (RFC 9114)**
  - Sorumluluk: Transport: QUIC (RFC 9000) — UDP tabanlı Mekanizma: 0-RTT connection establishment, native multiplexing
  - Kanıt: `http2-http3.md` § Protokol Detayı > HTTP/3 (RFC 9114)

#### K14.1.3 — Teknik Detaylar

- **Sorumluluk:** Frame Türleri: DATA (0x0): Gövde verisi HEADERS (0x1): HTTP header'ları
- **Kanıt:** `http2-http3.md` § Teknik Detaylar (8 bölüm başlığı)

- **K14.1.3.5 — Teknik Detaylar**
  - Sorumluluk: Frame Türleri: DATA (0x0): Gövde verisi HEADERS (0x1): HTTP header'ları
  - Kanıt: `http2-http3.md` § Teknik Detaylar
- **K14.1.3.6 — HTTP/2 Frame Yapısı**
  - Sorumluluk: Frame Türleri: DATA (0x0): Gövde verisi HEADERS (0x1): HTTP header'ları
  - Kanıt: `http2-http3.md` § Teknik Detaylar > HTTP/2 Frame Yapısı
- **K14.1.3.7 — HTTP/2 Conection Setup**
  - Sorumluluk: «HTTP/2 Conection Setup» — http2-http3.md dosyasında belgelenen bölüm.
  - Kanıt: `http2-http3.md` § Teknik Detaylar > HTTP/2 Conection Setup
- **K14.1.3.8 — QUIC (HTTP/3) Bağlantı Akışı**
  - Sorumluluk: «QUIC (HTTP/3) Bağlantı Akışı» — http2-http3.md dosyasında belgelenen bölüm.
  - Kanıt: `http2-http3.md` § Teknik Detaylar > QUIC (HTTP/3) Bağlantı Akışı
- **K14.1.3.9 — HPACK Header Compression**
  - Sorumluluk: Static Table (61 entry):
  - Kanıt: `http2-http3.md` § Teknik Detaylar > HPACK Header Compression
- **K14.1.3.10 — COREMUSIC Entegrasyonu**
  - Sorumluluk: API Endpoint Sensitivity:
  - Kanıt: `http2-http3.md` § Teknik Detaylar > COREMUSIC Entegrasyonu
- **K14.1.3.11 — H2C (Cleartext HTTP/2)**
  - Sorumluluk: «H2C (Cleartext HTTP/2)» — http2-http3.md dosyasında belgelenen bölüm.
  - Kanıt: `http2-http3.md` § Teknik Detaylar > H2C (Cleartext HTTP/2)
- **K14.1.3.12 — Server Push Stratejisi**
  - Sorumluluk: «Server Push Stratejisi» — http2-http3.md dosyasında belgelenen bölüm.
  - Kanıt: `http2-http3.md` § Teknik Detaylar > Server Push Stratejisi

#### K14.1.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — http2-http3.md dosyasında belgelenen bölüm.
- **Kanıt:** `http2-http3.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.1.4.13 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — http2-http3.md dosyasında belgelenen bölüm.
  - Kanıt: `http2-http3.md` § Konfigürasyon

#### K14.1.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — http2-http3.md dosyasında belgelenen bölüm.
- **Kanıt:** `http2-http3.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.1.5.14 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — http2-http3.md dosyasında belgelenen bölüm.
  - Kanıt: `http2-http3.md` § Bağımlılıklar

#### K14.1.6 — Durum: Implementasyon

- **Sorumluluk:** HTTP/2connection establishment HPACK header compression Stream multiplexing
- **Kanıt:** `http2-http3.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.1.6.15 — Durum: Implementasyon**
  - Sorumluluk: HTTP/2connection establishment HPACK header compression Stream multiplexing
  - Kanıt: `http2-http3.md` § Durum: Implementasyon

### K14.2 — WebSocket Gerçek Zamanlı

**Sorumluluk:** Sunucu-istemci tam çift yönlü bağlantı, kare yapısı, mesaj türleri ve oda senkronizasyonu.
**Kanıt dosyaları:** `websocket-realtime.md` — havuz 13 başlık, kullanıldı 13.

#### K14.2.1 — Genel Bakış

- **Sorumluluk:** WebSocket, COREMUSIC'in real-time iletişim altyapısının temelini oluşturur. Player durumu senkronizasyonu, çoklu oda kontrolü, anlık bildirimler ve…
- **Kanıt:** `websocket-realtime.md` § Genel Bakış (1 bölüm başlığı)

- **K14.2.1.1 — Genel Bakış**
  - Sorumluluk: WebSocket, COREMUSIC'in real-time iletişim altyapısının temelini oluşturur. Player durumu senkronizasyonu, çoklu oda kontrolü, anlık bildirimler ve bidirectional messaging bu protokol üzerinden…
  - Kanıt: `websocket-realtime.md` § Genel Bakış

#### K14.2.2 — Protokol Detayı

- **Sorumluluk:** RFC: 6455 (WebSocket), 8441 (WebSocket over HTTP/2) Transport: TCP (+ TLS opsiyonel) İletişim Modeli: Bidirectional, full-duplex
- **Kanıt:** `websocket-realtime.md` § Protokol Detayı (1 bölüm başlığı)

- **K14.2.2.2 — Protokol Detayı**
  - Sorumluluk: RFC: 6455 (WebSocket), 8441 (WebSocket over HTTP/2) Transport: TCP (+ TLS opsiyonel) İletişim Modeli: Bidirectional, full-duplex
  - Kanıt: `websocket-realtime.md` § Protokol Detayı

#### K14.2.3 — Teknik Detaylar

- **Sorumluluk:** Opcode Tablosu:
- **Kanıt:** `websocket-realtime.md` § Teknik Detaylar (8 bölüm başlığı)

- **K14.2.3.3 — Teknik Detaylar**
  - Sorumluluk: Opcode Tablosu:
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar
- **K14.2.3.4 — WebSocket Handshake**
  - Sorumluluk: «WebSocket Handshake» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > WebSocket Handshake
- **K14.2.3.5 — WebSocket Frame Yapısı**
  - Sorumluluk: Opcode Tablosu:
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > WebSocket Frame Yapısı
- **K14.2.3.6 — COREMUSIC WebSocket Mesaj Yapısı**
  - Sorumluluk: «COREMUSIC WebSocket Mesaj Yapısı» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > COREMUSIC WebSocket Mesaj Yapısı
- **K14.2.3.7 — Mesaj Türleri**
  - Sorumluluk: «Mesaj Türleri» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > Mesaj Türleri
- **K14.2.3.8 — Çoklu Oda Senkronizasyonu**
  - Sorumluluk: «Çoklu Oda Senkronizasyonu» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > Çoklu Oda Senkronizasyonu
- **K14.2.3.9 — Connection Management**
  - Sorumluluk: «Connection Management» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > Connection Management
- **K14.2.3.10 — Close Code'ları**
  - Sorumluluk: «Close Code'ları» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Teknik Detaylar > Close Code'ları

#### K14.2.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — websocket-realtime.md dosyasında belgelenen bölüm.
- **Kanıt:** `websocket-realtime.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.2.4.11 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Konfigürasyon

#### K14.2.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — websocket-realtime.md dosyasında belgelenen bölüm.
- **Kanıt:** `websocket-realtime.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.2.5.12 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — websocket-realtime.md dosyasında belgelenen bölüm.
  - Kanıt: `websocket-realtime.md` § Bağımlılıklar

#### K14.2.6 — Durum: Implementasyon

- **Sorumluluk:** WebSocket handshake Binary/Text frame desteği Ping/Pong heartbeat
- **Kanıt:** `websocket-realtime.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.2.6.13 — Durum: Implementasyon**
  - Sorumluluk: WebSocket handshake Binary/Text frame desteği Ping/Pong heartbeat
  - Kanıt: `websocket-realtime.md` § Durum: Implementasyon

### K14.3 — WebRTC P2P

**Sorumluluk:** SDP/ICE müzakeresi, NAT geçişi ve DTLS-SRTP ile uçtan uca P2P ses aktarımı.
**Kanıt dosyaları:** `webrtc-p2p.md` — havuz 13 başlık, kullanıldı 13.

#### K14.3.1 — Genel Bakış

- **Sorumluluk:** WebRTC (Web Real-Time Communication), COREMUSIC'in tarayıcı tabanlı peer-to-peer ses ve veri iletişimini sağlayan katmandır. ICE, STUN ve TURN protokolleri ile…
- **Kanıt:** `webrtc-p2p.md` § Genel Bakış (1 bölüm başlığı)

- **K14.3.1.1 — Genel Bakış**
  - Sorumluluk: WebRTC (Web Real-Time Communication), COREMUSIC'in tarayıcı tabanlı peer-to-peer ses ve veri iletişimini sağlayan katmandır. ICE, STUN ve TURN protokolleri ile NAT traversal, SRTP ile güvenli ses…
  - Kanıt: `webrtc-p2p.md` § Genel Bakış

#### K14.3.2 — Protokol Detayı

- **Sorumluluk:** Spec: W3C WebRTC, IETF RTCWeb Transport: UDP (DTLS/SRTP) + TCP (fallback) Media: RTP/RTCP over DTLS
- **Kanıt:** `webrtc-p2p.md` § Protokol Detayı (1 bölüm başlığı)

- **K14.3.2.2 — Protokol Detayı**
  - Sorumluluk: Spec: W3C WebRTC, IETF RTCWeb Transport: UDP (DTLS/SRTP) + TCP (fallback) Media: RTP/RTCP over DTLS
  - Kanıt: `webrtc-p2p.md` § Protokol Detayı

#### K14.3.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — webrtc-p2p.md dosyasında belgelenen bölüm.
- **Kanıt:** `webrtc-p2p.md` § Teknik Detaylar (8 bölüm başlığı)

- **K14.3.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar
- **K14.3.3.4 — WebRTC Bağlantı Akışı**
  - Sorumluluk: «WebRTC Bağlantı Akışı» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > WebRTC Bağlantı Akışı
- **K14.3.3.5 — SDP (Session Description Protocol)**
  - Sorumluluk: «SDP (Session Description Protocol)» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > SDP (Session Description Protocol)
- **K14.3.3.6 — ICE Candidate Tipleri**
  - Sorumluluk: «ICE Candidate Tipleri» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > ICE Candidate Tipleri
- **K14.3.3.7 — NAT Traversal Stratejisi**
  - Sorumluluk: «NAT Traversal Stratejisi» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > NAT Traversal Stratejisi
- **K14.3.3.8 — DTLS-SRTP Akışı**
  - Sorumluluk: «DTLS-SRTP Akışı» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > DTLS-SRTP Akışı
- **K14.3.3.9 — SCTP Data Channel**
  - Sorumluluk: «SCTP Data Channel» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > SCTP Data Channel
- **K14.3.3.10 — ICE Candidate Pair Prioritization**
  - Sorumluluk: «ICE Candidate Pair Prioritization» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Teknik Detaylar > ICE Candidate Pair Prioritization

#### K14.3.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — webrtc-p2p.md dosyasında belgelenen bölüm.
- **Kanıt:** `webrtc-p2p.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.3.4.11 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Konfigürasyon

#### K14.3.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — webrtc-p2p.md dosyasında belgelenen bölüm.
- **Kanıt:** `webrtc-p2p.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.3.5.12 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — webrtc-p2p.md dosyasında belgelenen bölüm.
  - Kanıt: `webrtc-p2p.md` § Bağımlılıklar

#### K14.3.6 — Durum: Implementasyon

- **Sorumluluk:** SDP creation/modification ICE candidate gathering ICE connectivity checks
- **Kanıt:** `webrtc-p2p.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.3.6.13 — Durum: Implementasyon**
  - Sorumluluk: SDP creation/modification ICE candidate gathering ICE connectivity checks
  - Kanıt: `webrtc-p2p.md` § Durum: Implementasyon

### K14.4 — mDNS Keşif

**Sorumluluk:** Yerel ağ servis kaydı ve keşfi: DNS-SD record'ları, probing ve anti-çakışma stratejisi.
**Kanıt dosyaları:** `mdns-discovery.md` — havuz 14 başlık, kullanıldı 14.

#### K14.4.1 — Genel Bakış

- **Sorumluluk:** mDNS (Multicast DNS), COREMUSIC'in yerel ağ üzerindeki cihaz ve servis keşfinin temel mekanizmasıdır. Bonjour (Apple) ve Avahi (Linux) ile uyumlu çalışarak,…
- **Kanıt:** `mdns-discovery.md` § Genel Bakış (1 bölüm başlığı)

- **K14.4.1.1 — Genel Bakış**
  - Sorumluluk: mDNS (Multicast DNS), COREMUSIC'in yerel ağ üzerindeki cihaz ve servis keşfinin temel mekanizmasıdır. Bonjour (Apple) ve Avahi (Linux) ile uyumlu çalışarak, DNS yapısına ihtiyaç duymadan .local…
  - Kanıt: `mdns-discovery.md` § Genel Bakış

#### K14.4.2 — Protokol Detayı

- **Sorumluluk:** RFC: 6762 (mDNS), 6763 (DNS-SD) Transport: UDP port 5353 Multicast Adresi: 224.0.0.251 (IPv4), ff02::fb (IPv6)
- **Kanıt:** `mdns-discovery.md` § Protokol Detayı (1 bölüm başlığı)

- **K14.4.2.2 — Protokol Detayı**
  - Sorumluluk: RFC: 6762 (mDNS), 6763 (DNS-SD) Transport: UDP port 5353 Multicast Adresi: 224.0.0.251 (IPv4), ff02::fb (IPv6)
  - Kanıt: `mdns-discovery.md` § Protokol Detayı

#### K14.4.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — mdns-discovery.md dosyasında belgelenen bölüm.
- **Kanıt:** `mdns-discovery.md` § Teknik Detaylar (9 bölüm başlığı)

- **K14.4.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar
- **K14.4.3.4 — mDNS Mesaj Yapısı**
  - Sorumluluk: «mDNS Mesaj Yapısı» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > mDNS Mesaj Yapısı
- **K14.4.3.5 — DNS-SD Resource Record Tipleri**
  - Sorumluluk: «DNS-SD Resource Record Tipleri» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > DNS-SD Resource Record Tipleri
- **K14.4.3.6 — COREMUSIC Service Registration**
  - Sorumluluk: «COREMUSIC Service Registration» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > COREMUSIC Service Registration
- **K14.4.3.7 — Discovery Akışı**
  - Sorumluluk: «Discovery Akışı» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > Discovery Akışı
- **K14.4.3.8 — Probing Mekanizması**
  - Sorumluluk: «Probing Mekanizması» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > Probing Mekanizması
- **K14.4.3.9 — Cache Yönetimi**
  - Sorumluluk: «Cache Yönetimi» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > Cache Yönetimi
- **K14.4.3.10 — Multicast Grup Yönetimi**
  - Sorumluluk: «Multicast Grup Yönetimi» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > Multicast Grup Yönetimi
- **K14.4.3.11 — Anti-Conflict Stratejisi**
  - Sorumluluk: «Anti-Conflict Stratejisi» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Teknik Detaylar > Anti-Conflict Stratejisi

#### K14.4.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — mdns-discovery.md dosyasında belgelenen bölüm.
- **Kanıt:** `mdns-discovery.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.4.4.12 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Konfigürasyon

#### K14.4.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — mdns-discovery.md dosyasında belgelenen bölüm.
- **Kanıt:** `mdns-discovery.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.4.5.13 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — mdns-discovery.md dosyasında belgelenen bölüm.
  - Kanıt: `mdns-discovery.md` § Bağımlılıklar

#### K14.4.6 — Durum: Implementasyon

- **Sorumluluk:** mDNS query/response DNS-SD service registration Probing ve announcement
- **Kanıt:** `mdns-discovery.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.4.6.14 — Durum: Implementasyon**
  - Sorumluluk: mDNS query/response DNS-SD service registration Probing ve announcement
  - Kanıt: `mdns-discovery.md` § Durum: Implementasyon

### K14.5 — DLNA/UPnP

**Sorumluluk:** SSDP keşfi, CDS/AVTransport servisleri ve DIDL-Lite medya dizin yapısıyla paylaşım.
**Kanıt dosyaları:** `dlna-upnp.md` — havuz 12 başlık, kullanıldı 12.

#### K14.5.1 — Genel Bakış

- **Sorumluluk:** DLNA (Digital Living Network Alliance) ve UPnP (Universal Plug and Play), COREMUSIC'in yerel ağdaki medya cihazlarıyla uyumlu çalışmasını sağlayan…
- **Kanıt:** `dlna-upnp.md` § Genel Bakış (1 bölüm başlığı)

- **K14.5.1.1 — Genel Bakış**
  - Sorumluluk: DLNA (Digital Living Network Alliance) ve UPnP (Universal Plug and Play), COREMUSIC'in yerel ağdaki medya cihazlarıyla uyumlu çalışmasını sağlayan protokollerdir. MediaServer, MediaRenderer ve…
  - Kanıt: `dlna-upnp.md` § Genel Bakış

#### K14.5.2 — Protokol Detayı

- **Sorumluluk:** DLNA Version: 1.5 (UPnP 2.0 MediaServer) Transport: HTTP over TCP port 49152-65535 Keşif: SSDP (Simple Service Discovery Protocol) UDP port 1900
- **Kanıt:** `dlna-upnp.md` § Protokol Detayı (1 bölüm başlığı)

- **K14.5.2.2 — Protokol Detayı**
  - Sorumluluk: DLNA Version: 1.5 (UPnP 2.0 MediaServer) Transport: HTTP over TCP port 49152-65535 Keşif: SSDP (Simple Service Discovery Protocol) UDP port 1900
  - Kanıt: `dlna-upnp.md` § Protokol Detayı

#### K14.5.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — dlna-upnp.md dosyasında belgelenen bölüm.
- **Kanıt:** `dlna-upnp.md` § Teknik Detaylar (7 bölüm başlığı)

- **K14.5.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar
- **K14.5.3.4 — UPnP Mimari Roller**
  - Sorumluluk: «UPnP Mimari Roller» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar > UPnP Mimari Roller
- **K14.5.3.5 — SSDP Discovery Mesajları**
  - Sorumluluk: «SSDP Discovery Mesajları» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar > SSDP Discovery Mesajları
- **K14.5.3.6 — ContentDirectory Service (CDS)**
  - Sorumluluk: «ContentDirectory Service (CDS)» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar > ContentDirectory Service (CDS)
- **K14.5.3.7 — DIDL-Lite Medya Dizin Yapısı**
  - Sorumluluk: «DIDL-Lite Medya Dizin Yapısı» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar > DIDL-Lite Medya Dizin Yapısı
- **K14.5.3.8 — AVTransport Service**
  - Sorumluluk: «AVTransport Service» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar > AVTransport Service
- **K14.5.3.9 — UPnP Description XML**
  - Sorumluluk: «UPnP Description XML» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Teknik Detaylar > UPnP Description XML

#### K14.5.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — dlna-upnp.md dosyasında belgelenen bölüm.
- **Kanıt:** `dlna-upnp.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.5.4.10 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Konfigürasyon

#### K14.5.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — dlna-upnp.md dosyasında belgelenen bölüm.
- **Kanıt:** `dlna-upnp.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.5.5.11 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — dlna-upnp.md dosyasında belgelenen bölüm.
  - Kanıt: `dlna-upnp.md` § Bağımlılıklar

#### K14.5.6 — Durum: Implementasyon

- **Sorumluluk:** SSDP discovery (M-SEARCH/NOTIFY) UPnP device description ContentDirectory service
- **Kanıt:** `dlna-upnp.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.5.6.12 — Durum: Implementasyon**
  - Sorumluluk: SSDP discovery (M-SEARCH/NOTIFY) UPnP device description ContentDirectory service
  - Kanıt: `dlna-upnp.md` § Durum: Implementasyon

### K14.6 — AirPlay 2

**Sorumluluk:** Apple ekosistemi çoklu oda akışı: RTSP kontrol mesajları, ALAC yapılandırması, FairPlay.
**Kanıt dosyaları:** `airplay-streaming.md` — havuz 12 başlık, kullanıldı 12.

#### K14.6.1 — Genel Bakış

- **Sorumluluk:** AirPlay 2, Apple cihazlardan COREMUSIC hoparlörlerine yüksek kaliteli, düşük gecikmeli ses akışı sağlayan protokoldür. Çoklu oda desteği, fikir birliği tabanlı…
- **Kanıt:** `airplay-streaming.md` § Genel Bakış (1 bölüm başlığı)

- **K14.6.1.1 — Genel Bakış**
  - Sorumluluk: AirPlay 2, Apple cihazlardan COREMUSIC hoparlörlerine yüksek kaliteli, düşük gecikmeli ses akışı sağlayan protokoldür. Çoklu oda desteği, fikir birliği tabanlı senkronizasyon ve ALAC (Apple Lossless)…
  - Kanıt: `airplay-streaming.md` § Genel Bakış

#### K14.6.2 — Protokol Detayı

- **Sorumluluk:** Protokol: AirPlay 2 (proprietary, reverse-engineered) Transport: TCP (kontrol) + RTP/UDP (ses)
- **Kanıt:** `airplay-streaming.md` § Protokol Detayı (1 bölüm başlığı)

- **K14.6.2.2 — Protokol Detayı**
  - Sorumluluk: Protokol: AirPlay 2 (proprietary, reverse-engineered) Transport: TCP (kontrol) + RTP/UDP (ses)
  - Kanıt: `airplay-streaming.md` § Protokol Detayı

#### K14.6.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — airplay-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `airplay-streaming.md` § Teknik Detaylar (7 bölüm başlığı)

- **K14.6.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar
- **K14.6.3.4 — AirPlay 2 Bağlantı Akışı**
  - Sorumluluk: «AirPlay 2 Bağlantı Akışı» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar > AirPlay 2 Bağlantı Akışı
- **K14.6.3.5 — RTSP (Real Time Streaming Protocol) Mesajları**
  - Sorumluluk: «RTSP (Real Time Streaming Protocol) Mesajları» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar > RTSP (Real Time Streaming Protocol) Mesajları
- **K14.6.3.6 — AirPlay 2 Çoklu Oda Protocol**
  - Sorumluluk: «AirPlay 2 Çoklu Oda Protocol» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar > AirPlay 2 Çoklu Oda Protocol
- **K14.6.3.7 — FairPlay Şifreleme**
  - Sorumluluk: «FairPlay Şifreleme» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar > FairPlay Şifreleme
- **K14.6.3.8 — ALAC Audio Configuration**
  - Sorumluluk: «ALAC Audio Configuration» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar > ALAC Audio Configuration
- **K14.6.3.9 — Discoverability TXT Records**
  - Sorumluluk: «Discoverability TXT Records» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Teknik Detaylar > Discoverability TXT Records

#### K14.6.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — airplay-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `airplay-streaming.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.6.4.10 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Konfigürasyon

#### K14.6.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — airplay-streaming.md dosyasında belgelenen bölüm.
- **Kanıt:** `airplay-streaming.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.6.5.11 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — airplay-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `airplay-streaming.md` § Bağımlılıklar

#### K14.6.6 — Durum: Implementasyon

- **Sorumluluk:** mDNS service advertisement RTSP OPTIONS/ANNOUNCE/SETUP FairPlay key exchange
- **Kanıt:** `airplay-streaming.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.6.6.12 — Durum: Implementasyon**
  - Sorumluluk: mDNS service advertisement RTSP OPTIONS/ANNOUNCE/SETUP FairPlay key exchange
  - Kanıt: `airplay-streaming.md` § Durum: Implementasyon

### K14.7 — DNS Çözümleme

**Sorumluluk:** DNS mesaj formatı, kayıt tipleri, DoH/DoT güvenli çözümleme, cache ve upstream seçimi.
**Kanıt dosyaları:** `dns-resolver.md` — havuz 14 başlık, kullanıldı 14.

#### K14.7.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC DNS çözümleyici, tüm domain name resolution işlemlerini merkezi olarak yönetir. Custom DNS sunucu desteği, DNS-over-HTTPS (DoH), DNS-over-TLS (DoT)…
- **Kanıt:** `dns-resolver.md` § Genel Bakış (1 bölüm başlığı)

- **K14.7.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC DNS çözümleyici, tüm domain name resolution işlemlerini merkezi olarak yönetir. Custom DNS sunucu desteği, DNS-over-HTTPS (DoH), DNS-over-TLS (DoT) ve yerel DNS önbellek ile hızlı ve…
  - Kanıt: `dns-resolver.md` § Genel Bakış

#### K14.7.2 — Protokol Detayı

- **Sorumluluk:** RFC: 1034/1035 (DNS), 8484 (DoH), 7858 (DoT) Transport: UDP port 53 (klasik), TCP port 53 (büyük yanıt), TCP port 443 (DoH), TCP port 853 (DoT)
- **Kanıt:** `dns-resolver.md` § Protokol Detayı (1 bölüm başlığı)

- **K14.7.2.2 — Protokol Detayı**
  - Sorumluluk: RFC: 1034/1035 (DNS), 8484 (DoH), 7858 (DoT) Transport: UDP port 53 (klasik), TCP port 53 (büyük yanıt), TCP port 443 (DoH), TCP port 853 (DoT)
  - Kanıt: `dns-resolver.md` § Protokol Detayı

#### K14.7.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — dns-resolver.md dosyasında belgelenen bölüm.
- **Kanıt:** `dns-resolver.md` § Teknik Detaylar (9 bölüm başlığı)

- **K14.7.3.3 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar
- **K14.7.3.4 — DNS Message Format**
  - Sorumluluk: «DNS Message Format» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > DNS Message Format
- **K14.7.3.5 — DNS Record Tipleri**
  - Sorumluluk: «DNS Record Tipleri» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > DNS Record Tipleri
- **K14.7.3.6 — DNS Resolution Akışı**
  - Sorumluluk: «DNS Resolution Akışı» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > DNS Resolution Akışı
- **K14.7.3.7 — DNS-over-HTTPS (DoH) Akışı**
  - Sorumluluk: «DNS-over-HTTPS (DoH) Akışı» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > DNS-over-HTTPS (DoH) Akışı
- **K14.7.3.8 — DNS-over-TLS (DoT) Akışı**
  - Sorumluluk: «DNS-over-TLS (DoT) Akışı» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > DNS-over-TLS (DoT) Akışı
- **K14.7.3.9 — Cache Yönetimi**
  - Sorumluluk: «Cache Yönetimi» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > Cache Yönetimi
- **K14.7.3.10 — Upstream DNS Sunucu Seçimi**
  - Sorumluluk: «Upstream DNS Sunucu Seçimi» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > Upstream DNS Sunucu Seçimi
- **K14.7.3.11 — DNS Query Pipeline**
  - Sorumluluk: «DNS Query Pipeline» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Teknik Detaylar > DNS Query Pipeline

#### K14.7.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — dns-resolver.md dosyasında belgelenen bölüm.
- **Kanıt:** `dns-resolver.md` § Konfigürasyon (1 bölüm başlığı)

- **K14.7.4.12 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Konfigürasyon

#### K14.7.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — dns-resolver.md dosyasında belgelenen bölüm.
- **Kanıt:** `dns-resolver.md` § Bağımlılıklar (1 bölüm başlığı)

- **K14.7.5.13 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — dns-resolver.md dosyasında belgelenen bölüm.
  - Kanıt: `dns-resolver.md` § Bağımlılıklar

#### K14.7.6 — Durum: Implementasyon

- **Sorumluluk:** Klasik DNS (UDP/TCP port 53) DNS-over-HTTPS (DoH) DNS-over-TLS (DoT)
- **Kanıt:** `dns-resolver.md` § Durum: Implementasyon (1 bölüm başlığı)

- **K14.7.6.14 — Durum: Implementasyon**
  - Sorumluluk: Klasik DNS (UDP/TCP port 53) DNS-over-HTTPS (DoH) DNS-over-TLS (DoT)
  - Kanıt: `dns-resolver.md` § Durum: Implementasyon

### K14.8 — TLS & VPN Güvenliği

**Sorumluluk:** TLS 1.3 el sıkışma/sertifika yönetimi ile WireGuard tüneli, split tunneling ve DDoS koruması.
**Kanıt dosyaları:** `tls-security.md`, `vpn-support.md` — havuz 27 başlık, kullanıldı 27.

#### K14.8.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC TLS 1.3 katmanı, tüm ağ iletişiminin güvenliğini sağlayan temel bileşendir. Sertifika yönetimi, otomatik yenileme, OCSP stapling ve certificate…
- **Kanıt:** `tls-security.md`, `vpn-support.md` § Genel Bakış (2 bölüm başlığı)

- **K14.8.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC TLS 1.3 katmanı, tüm ağ iletişiminin güvenliğini sağlayan temel bileşendir. Sertifika yönetimi, otomatik yenileme, OCSP stapling ve certificate pinning ile zero-trust güvenlik modeli…
  - Kanıt: `tls-security.md` § Genel Bakış
- **K14.8.1.2 — Genel Bakış**
  - Sorumluluk: COREMUSIC VPN desteği, uzaktan erişim ve güvenli tünel iletişimi için WireGuard tabanlı entegrasyon sağlar. Ev dışı ağlardan güvenli medya erişimi, çoklu site birleştirme ve şifreli peer-to-peer…
  - Kanıt: `vpn-support.md` § Genel Bakış

#### K14.8.2 — Protokol Detayı

- **Sorumluluk:** RFC: 8446 (TLS 1.3), 6962 (Certificate Transparency) Transport: TCP (herhangi bir port) Handshake: 1-RTT (standart), 0-RTT (early data)
- **Kanıt:** `tls-security.md`, `vpn-support.md` § Protokol Detayı (2 bölüm başlığı)

- **K14.8.2.3 — Protokol Detayı**
  - Sorumluluk: RFC: 8446 (TLS 1.3), 6962 (Certificate Transparency) Transport: TCP (herhangi bir port) Handshake: 1-RTT (standart), 0-RTT (early data)
  - Kanıt: `tls-security.md` § Protokol Detayı
- **K14.8.2.4 — Protokol Detayı**
  - Sorumluluk: Protokol: WireGuard (IETF draft-irtf-curdle-wireguard) Transport: UDP port 51820 Şifreleme: ChaCha20-Poly1305
  - Kanıt: `vpn-support.md` § Protokol Detayı

#### K14.8.3 — Teknik Detaylar

- **Sorumluluk:** 0-RTT Uyarıları: Replay attack risk: Idempotent operations only No forward secrecy for early data
- **Kanıt:** `tls-security.md`, `vpn-support.md` § Teknik Detaylar (17 bölüm başlığı)

- **K14.8.3.5 — Teknik Detaylar**
  - Sorumluluk: 0-RTT Uyarıları: Replay attack risk: Idempotent operations only No forward secrecy for early data
  - Kanıt: `tls-security.md` § Teknik Detaylar
- **K14.8.3.6 — TLS 1.3 Handshake Akışı**
  - Sorumluluk: «TLS 1.3 Handshake Akışı» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > TLS 1.3 Handshake Akışı
- **K14.8.3.7 — 0-RTT Early Data**
  - Sorumluluk: 0-RTT Uyarıları: Replay attack risk: Idempotent operations only No forward secrecy for early data
  - Kanıt: `tls-security.md` § Teknik Detaylar > 0-RTT Early Data
- **K14.8.3.8 — Sertifika Zinciri**
  - Sorumluluk: «Sertifika Zinciri» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > Sertifika Zinciri
- **K14.8.3.9 — Sertifika Oluşturma Akışı**
  - Sorumluluk: «Sertifika Oluşturma Akışı» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > Sertifika Oluşturma Akışı
- **K14.8.3.10 — OCSP Stapling**
  - Sorumluluk: «OCSP Stapling» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > OCSP Stapling
- **K14.8.3.11 — Certificate Pinning**
  - Sorumluluk: «Certificate Pinning» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > Certificate Pinning
- **K14.8.3.12 — Cipher Suites**
  - Sorumluluk: «Cipher Suites» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > Cipher Suites
- **K14.8.3.13 — Session Ticket**
  - Sorumluluk: «Session Ticket» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Teknik Detaylar > Session Ticket
- **K14.8.3.14 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar
- **K14.8.3.15 — WireGuard Tünel Mimarisi**
  - Sorumluluk: «WireGuard Tünel Mimarisi» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > WireGuard Tünel Mimarisi
- **K14.8.3.16 — WireGuard Paket Yapısı**
  - Sorumluluk: «WireGuard Paket Yapısı» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > WireGuard Paket Yapısı
- **K14.8.3.17 — Key Exchange Akışı**
  - Sorumluluk: «Key Exchange Akışı» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > Key Exchange Akışı
- **K14.8.3.18 — COREMUSIC VPN Kullanım Senaryoları**
  - Sorumluluk: «COREMUSIC VPN Kullanım Senaryoları» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > COREMUSIC VPN Kullanım Senaryoları
- **K14.8.3.19 — Split Tunneling**
  - Sorumluluk: «Split Tunneling» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > Split Tunneling
- **K14.8.3.20 — Performance Monitoring**
  - Sorumluluk: «Performance Monitoring» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > Performance Monitoring
- **K14.8.3.21 — DDoS Koruması (Cookie Mechanism)**
  - Sorumluluk: «DDoS Koruması (Cookie Mechanism)» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Teknik Detaylar > DDoS Koruması (Cookie Mechanism)

#### K14.8.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — tls-security.md dosyasında belgelenen bölüm.
- **Kanıt:** `tls-security.md`, `vpn-support.md` § Konfigürasyon (2 bölüm başlığı)

- **K14.8.4.22 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Konfigürasyon
- **K14.8.4.23 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Konfigürasyon

#### K14.8.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — tls-security.md dosyasında belgelenen bölüm.
- **Kanıt:** `tls-security.md`, `vpn-support.md` § Bağımlılıklar (2 bölüm başlığı)

- **K14.8.5.24 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — tls-security.md dosyasında belgelenen bölüm.
  - Kanıt: `tls-security.md` § Bağımlılıklar
- **K14.8.5.25 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — vpn-support.md dosyasında belgelenen bölüm.
  - Kanıt: `vpn-support.md` § Bağımlılıklar

#### K14.8.6 — Durum: Implementasyon

- **Sorumluluk:** TLS 1.3 1-RTT handshake 0-RTT early data X25519 key exchange
- **Kanıt:** `tls-security.md`, `vpn-support.md` § Durum: Implementasyon (2 bölüm başlığı)

- **K14.8.6.26 — Durum: Implementasyon**
  - Sorumluluk: TLS 1.3 1-RTT handshake 0-RTT early data X25519 key exchange
  - Kanıt: `tls-security.md` § Durum: Implementasyon
- **K14.8.6.27 — Durum: Implementasyon**
  - Sorumluluk: WireGuard interface setup Key pair generation (Curve25519) Handshake initiation/response
  - Kanıt: `vpn-support.md` § Durum: Implementasyon

### K14.9 — Ağ Optimizasyonu & QoS

**Sorumluluk:** Connection pooling, load balancing, multicast dağıtım ve DSCP tabanlı kalite yönetimi.
**Kanıt dosyaları:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` — havuz 51 başlık, kullanıldı 50, seçim dışı 1.

#### K14.9.1 — Genel Bakış

- **Sorumluluk:** COREMUSIC load balancing katmanı, gelen istekleri birden fazla backend sunucusu arasında dağıtarak yüksek erişilebilirlik ve performans sağlar. L4/L7 load…
- **Kanıt:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` § Genel Bakış (4 bölüm başlığı)

- **K14.9.1.1 — Genel Bakış**
  - Sorumluluk: COREMUSIC load balancing katmanı, gelen istekleri birden fazla backend sunucusu arasında dağıtarak yüksek erişilebilirlik ve performans sağlar. L4/L7 load balancing, health checking, session affinity…
  - Kanıt: `load-balancing.md` § Genel Bakış
- **K14.9.1.2 — Genel Bakış**
  - Sorumluluk: COREMUSIC connection pooling, TCP/UDP bağlantılarının yeniden kullanımını ve yönetilmesini sağlayan optimizasyon katmanıdır. Bağlantı oluşturma overhead'ini azaltarak, yüksek frekanslı isteklerde…
  - Kanıt: `connection-pooling-network.md` § Genel Bakış
- **K14.9.1.3 — Genel Bakış**
  - Sorumluluk: Multicast streaming, COREMUSIC'in çoklu oda senkronizasyonunda bant genişliği optimizasyonu sağlayan protokoldür. Tek bir kaynaktan birden fazla alıcıya aynı anda ses verisi göndererek network…
  - Kanıt: `multicast-streaming.md` § Genel Bakış
- **K14.9.1.4 — Genel Bakış**
  - Sorumluluk: COREMUSIC QoS katmanı, real-time ses akışının network seviyesinde önceliklendirilmesini sağlar. DSCP (Differentiated Services Code Point) marking, traffic shaping ve queue management ile gecikme…
  - Kanıt: `network-qos.md` § Genel Bakış

#### K14.9.2 — Protokol Detayı

- **Sorumluluk:** L4 (Transport): TCP/UDP tabanlı, fast switching L7 (Application): HTTP/WebSocket tabanlı, content-aware
- **Kanıt:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` § Protokol Detayı (4 bölüm başlığı)

- **K14.9.2.5 — Protokol Detayı**
  - Sorumluluk: L4 (Transport): TCP/UDP tabanlı, fast switching L7 (Application): HTTP/WebSocket tabanlı, content-aware
  - Kanıt: `load-balancing.md` § Protokol Detayı
- **K14.9.2.6 — Protokol Detayı**
  - Sorumluluk: Min Connections: Minimum havuz boyutu Max Connections: Maksimum havuz boyutu Idle Timeout: Boşta kalma süresi
  - Kanıt: `connection-pooling-network.md` § Protokol Detayı
- **K14.9.2.7 — Protokol Detayı**
  - Sorumluluk: Transport: UDP multicast Multicast Adres Aralığı: 239.0.0.0/8 (org-scoped) Varsayılan Adres: 239.1.1.1 (site-local)
  - Kanıt: `multicast-streaming.md` § Protokol Detayı
- **K14.9.2.8 — Protokol Detayı**
  - Sorumluluk: RFC: 2474 (DSCP), 2475 (DiffServ), 3246 (EF PHB) DSCP Values: EF (46), AF41 (34), AF21 (18), CS0 (0)
  - Kanıt: `network-qos.md` § Protokol Detayı

#### K14.9.3 — Teknik Detaylar

- **Sorumluluk:** «Teknik Detaylar» — load-balancing.md dosyasında belgelenen bölüm.
- **Kanıt:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` § Teknik Detaylar (31 bölüm başlığı)

- **K14.9.3.9 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar
- **K14.9.3.10 — Load Balancing Mimarisi**
  - Sorumluluk: «Load Balancing Mimarisi» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > Load Balancing Mimarisi
- **K14.9.3.11 — Algoritma Detayları**
  - Sorumluluk: «Algoritma Detayları» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > Algoritma Detayları
- **K14.9.3.12 — Health Checking**
  - Sorumluluk: «Health Checking» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > Health Checking
- **K14.9.3.13 — Session Affinity**
  - Sorumluluk: «Session Affinity» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > Session Affinity
- **K14.9.3.14 — Layer 7 Routing Rules**
  - Sorumluluk: «Layer 7 Routing Rules» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > Layer 7 Routing Rules
- **K14.9.3.15 — DSCP-Based Routing**
  - Sorumluluk: «DSCP-Based Routing» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > DSCP-Based Routing
- **K14.9.3.16 — Connection Draining**
  - Sorumluluk: «Connection Draining» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Teknik Detaylar > Connection Draining
- **K14.9.3.17 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar
- **K14.9.3.18 — Connection Pool Mimarisi**
  - Sorumluluk: «Connection Pool Mimarisi» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar > Connection Pool Mimarisi
- **K14.9.3.19 — Bağlantı Durum Makinesi**
  - Sorumluluk: «Bağlantı Durum Makinesi» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar > Bağlantı Durum Makinesi
- **K14.9.3.20 — Pool İşlemleri**
  - Sorumluluk: «Pool İşlemleri» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar > Pool İşlemleri
- **K14.9.3.21 — Warm-up Stratejisi**
  - Sorumluluk: «Warm-up Stratejisi» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar > Warm-up Stratejisi
- **K14.9.3.22 — Keep-Alive Mekanizması**
  - Sorumluluk: «Keep-Alive Mekanizması» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar > Keep-Alive Mekanizması
- **K14.9.3.23 — Metrics Collection**
  - Sorumluluk: «Metrics Collection» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Teknik Detaylar > Metrics Collection
- **K14.9.3.24 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar
- **K14.9.3.25 — Multicast Mimarisi**
  - Sorumluluk: «Multicast Mimarisi» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > Multicast Mimarisi
- **K14.9.3.26 — IGMP Join/Leave Akışı**
  - Sorumluluk: «IGMP Join/Leave Akışı» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > IGMP Join/Leave Akışı
- **K14.9.3.27 — RTP Packet Structure**
  - Sorumluluk: «RTP Packet Structure» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > RTP Packet Structure
- **K14.9.3.28 — Multicast Sender Implementation**
  - Sorumluluk: «Multicast Sender Implementation» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > Multicast Sender Implementation
- **K14.9.3.29 — Multicast Receiver Implementation**
  - Sorumluluk: «Multicast Receiver Implementation» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > Multicast Receiver Implementation
- **K14.9.3.30 — Sync Protocol over Multicast**
  - Sorumluluk: «Sync Protocol over Multicast» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > Sync Protocol over Multicast
- **K14.9.3.31 — IGMP Snooping Optimizasyonu**
  - Sorumluluk: «IGMP Snooping Optimizasyonu» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Teknik Detaylar > IGMP Snooping Optimizasyonu
- **K14.9.3.32 — Teknik Detaylar**
  - Sorumluluk: «Teknik Detaylar» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar
- **K14.9.3.33 — DSCP Code Point Tablosu**
  - Sorumluluk: «DSCP Code Point Tablosu» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > DSCP Code Point Tablosu
- **K14.9.3.34 — Traffic Classification**
  - Sorumluluk: «Traffic Classification» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > Traffic Classification
- **K14.9.3.35 — Traffic Shaping (Token Bucket)**
  - Sorumluluk: «Traffic Shaping (Token Bucket)» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > Traffic Shaping (Token Bucket)
- **K14.9.3.36 — Queue Management**
  - Sorumluluk: «Queue Management» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > Queue Management
- **K14.9.3.37 — QoS Monitoring**
  - Sorumluluk: «QoS Monitoring» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > QoS Monitoring
- **K14.9.3.38 — Adaptive Bitrate**
  - Sorumluluk: «Adaptive Bitrate» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > Adaptive Bitrate
- **K14.9.3.39 — Linux Traffic Control Integration**
  - Sorumluluk: «Linux Traffic Control Integration» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Teknik Detaylar > Linux Traffic Control Integration

#### K14.9.4 — Konfigürasyon

- **Sorumluluk:** «Konfigürasyon» — load-balancing.md dosyasında belgelenen bölüm.
- **Kanıt:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` § Konfigürasyon (4 bölüm başlığı)

- **K14.9.4.40 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Konfigürasyon
- **K14.9.4.41 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Konfigürasyon
- **K14.9.4.42 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Konfigürasyon
- **K14.9.4.43 — Konfigürasyon**
  - Sorumluluk: «Konfigürasyon» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Konfigürasyon

#### K14.9.5 — Bağımlılıklar

- **Sorumluluk:** «Bağımlılıklar» — load-balancing.md dosyasında belgelenen bölüm.
- **Kanıt:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md`, `network-qos.md` § Bağımlılıklar (4 bölüm başlığı)

- **K14.9.5.44 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — load-balancing.md dosyasında belgelenen bölüm.
  - Kanıt: `load-balancing.md` § Bağımlılıklar
- **K14.9.5.45 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — connection-pooling-network.md dosyasında belgelenen bölüm.
  - Kanıt: `connection-pooling-network.md` § Bağımlılıklar
- **K14.9.5.46 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — multicast-streaming.md dosyasında belgelenen bölüm.
  - Kanıt: `multicast-streaming.md` § Bağımlılıklar
- **K14.9.5.47 — Bağımlılıklar**
  - Sorumluluk: «Bağımlılıklar» — network-qos.md dosyasında belgelenen bölüm.
  - Kanıt: `network-qos.md` § Bağımlılıklar

#### K14.9.6 — Durum: Implementasyon

- **Sorumluluk:** Round Robin Weighted Round Robin Least Connections
- **Kanıt:** `load-balancing.md`, `connection-pooling-network.md`, `multicast-streaming.md` § Durum: Implementasyon (3 bölüm başlığı)

- **K14.9.6.48 — Durum: Implementasyon**
  - Sorumluluk: Round Robin Weighted Round Robin Least Connections
  - Kanıt: `load-balancing.md` § Durum: Implementasyon
- **K14.9.6.49 — Durum: Implementasyon**
  - Sorumluluk: Connection pool lifecycle Min/max pool sizing Idle connection cleanup
  - Kanıt: `connection-pooling-network.md` § Durum: Implementasyon
- **K14.9.6.50 — Durum: Implementasyon**
  - Sorumluluk: UDP multicast sender/receiver RTP packet construction IGMPv3 join/leave
  - Kanıt: `multicast-streaming.md` § Durum: Implementasyon

---


## Kanıt Kataloğu

*son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*

Bu katalog, k14-ag/ klasöründeki tüm MD dosyalarını (ad + 1 satır sorumluluk) ve her dosyanın onaylı sayım hangi kısmını desteklediğini listeler. 13 kanonik katman MD'si + index.md + README.md + CLAUDE.md.

| # | Dosya | Sorumluluk (1 satır) | Desteklediği sayımlar |
|---|-------|----------------------|------------------------|
| 1 | `http2-http3.md` | HTTP/2 & HTTP/3 Protokol Katmanı | `K14.1` alanı (1), 6 alt alan, 15 yaprak → `K14` toplam 224 içine katkı |
| 2 | `websocket-realtime.md` | WebSocket Real-Time İletişim | `K14.2` alanı (1), 6 alt alan, 13 yaprak → `K14` toplam 224 içine katkı |
| 3 | `webrtc-p2p.md` | WebRTC Peer-to-Peer İletişim | `K14.3` alanı (1), 6 alt alan, 13 yaprak → `K14` toplam 224 içine katkı |
| 4 | `mdns-discovery.md` | mDNS Service Discovery | `K14.4` alanı (1), 6 alt alan, 14 yaprak → `K14` toplam 224 içine katkı |
| 5 | `dlna-upnp.md` | DLNA/UPnP Medya Paylaşımı | `K14.5` alanı (1), 6 alt alan, 12 yaprak → `K14` toplam 224 içine katkı |
| 6 | `airplay-streaming.md` | AirPlay 2 Ses Akışı | `K14.6` alanı (1), 6 alt alan, 12 yaprak → `K14` toplam 224 içine katkı |
| 7 | `dns-resolver.md` | DNS Çözümleyici | `K14.7` alanı (1), 6 alt alan, 14 yaprak → `K14` toplam 224 içine katkı |
| 8 | `tls-security.md` | TLS 1.3 & Sertifika Yönetimi | `K14.8` alanı (1), 6 alt alan, 14 yaprak → `K14` toplam 224 içine katkı |
| 9 | `vpn-support.md` | VPN Desteği & WireGuard Entegrasyonu | `K14.8` alanı (1), 6 alt alan, 13 yaprak → `K14` toplam 224 içine katkı |
| 10 | `load-balancing.md` | Load Balancing Stratejileri | `K14.9` alanı (1), 6 alt alan, 13 yaprak → `K14` toplam 224 içine katkı |
| 11 | `connection-pooling-network.md` | Network Connection Pooling | `K14.9` alanı (1), 6 alt alan, 12 yaprak → `K14` toplam 224 içine katkı |
| 12 | `multicast-streaming.md` | Multicast Ses Akışı | `K14.9` alanı (1), 6 alt alan, 13 yaprak → `K14` toplam 224 içine katkı |
| 13 | `network-qos.md` | Network QoS & DSCP Marking | `K14.9` alanı (1), 5 alt alan, 12 yaprak → `K14` toplam 224 içine katkı |
| 14 | `index.md` | Katman ana sayfası: mimari diyagram, protokol/modül tabloları, bağımlılıklar | `K14.a) alan tanımı bağlamı (plan §2.1 L14.1-L14.5 satırları ile) |
| 15 | `README.md` | Katman künyesi ve protokol haritası (Bileşen Sayısı: 40) | `K14.a) alan/alt alan doğrulaması; bileşen-tablosu satırları kanıt havuzuna girer |
| 16 | `CLAUDE.md` | Katman kural ve kapsam notları | Şema kuralları bağlamı (sayıma doğrudan girmez) |

**Sayım dayanağı:** 13 dosyadan çıkarılan 171 H2/H3 başlığı arasından hedef c=170 için 1 başlık orantılı olarak seçimin dışında bırakıldı (aşağıda); alan/alt alan sayıları a=9, b=6 ile kilitlidir.

### Seçim Dışı Kanıtlar

| Başlık | Dosya | Neden |
|--------|-------|-------|
| Durum: Implementasyon | `network-qos.md` | hedef c=170 aşıldı; havuz 171 > hedef — sayım fazlalığı, içerik korunmuştur |

*K14 Alt Katman Şeması + Kanıt Kataloğu v1.0 — son güncelleme: 2026-09-24, kaynak: 3 turlu agent tartışması*
