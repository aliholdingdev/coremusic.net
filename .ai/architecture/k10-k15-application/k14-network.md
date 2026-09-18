---
title: "K14 — Ağ ve İletişim Katmanı (Network & Communication)"
type: architecture
category: network
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
reference:
  adr: "N/A — Cross-cutting concern"
  github:
    - name: "libnice"
      url: "https://github.com/libnice/libnice"
    - name: "gupnp"
      url: "https://wiki.gnome.org/Projects/GUPnP"
    - name: "FreeSWITCH"
      url: "https://github.com/signalwire/freeswitch"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K14 — Ağ ve İletişim Katmanı (Network & Communication)

CoreMusic ekosistemi için ağ protokolleri, keşif, streaming ve iletişim katmanı. 40 bileşen.

## Genel Bakış

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                    K14 — NETWORK & COMMUNICATION                             │
├──────────────┬──────────────┬──────────────┬────────────────────────────────┤
│   HTTP/WS    │   DISCOVERY  │   STREAMING  │   INFRASTRUCTURE               │
│              │              │              │                                │
│  HTTP Client │  mDNS        │  DLNA Server │  DNS Resolution                │
│  HTTP/2      │  SSDP        │  DLNA Render │  DNS Caching                   │
│  HTTP/3 QUIC │  NAT-PMP     │  AirPlay     │  DNS over HTTPS                │
│  WebSocket   │  UPnP        │  Chromecast  │  DHCP                          │
│  TLS 1.3     │  Zeroconf    │  WebRTC Peer │  Network Monitor               │
│              │              │  WebRTC Sig  │  Network Diagnostics           │
│              │              │  WebRTC STUN │  Network Switch                │
│              │              │  WebRTC TURN │  Firewall                      │
│              │              │              │  VPN/Proxy Client              │
│              │              │              │  Load Balancer                 │
│              │              │              │  CDN Integration               │
│              │              │              │  SSL/TLS Cert                  │
└──────────────┴──────────────┴──────────────┴────────────────────────────────┘
```

## Bileşen Listesi (40)

| # | Bileşen | Alt Bileşenler | Teknoloji | Kapsam |
|---|---------|---------------|-----------|--------|
| 1 | HTTP Client | cURL, Guzzle, connection pooling, retry | PHP 8.4, cURL | API iletişimi |
| 2 | HTTP/2 | Server push, multiplexing, header compression | Nginx, PHP | HTTP optimize |
| 3 | HTTP/3 QUIC | UDP-based, 0-RTT, connection migration | Nginx, quiche | Gelecek nesil HTTP |
| 4 | WebSocket Client | Reconnection, heartbeat, binary support | Vanilla JS | Gerçek zamanlı |
| 5 | WebSocket Server | Channel management, broadcast, scaling | Ratchet/PHP | Gerçek zamanlı |
| 6 | TLS 1.3 | Handshake optimization, 0-RTT, cipher suites | OpenSSL | Güvenli iletişim |
| 7 | mDNS | Service discovery, .local resolution, Bonjour | Avahi/Bonjour | Yerel keşif |
| 8 | SSDP | UPnP discovery, M-SEARCH, NOTIFY | Custom PHP | Cihaz keşfi |
| 9 | NAT-PMP | Port mapping, automatic port forwarding | Custom C | NAT traversal |
| 10 | UPnP | Device control, content directory, rendering | GUPnP | Ev otomasyonu |
| 11 | DLNA Server | Media server, content directory, transfer | Custom PHP | Medya paylaşımı |
| 12 | DLNA Renderer | AV transport, rendering control, playlist | Custom C++ | Medya oynatma |
| 13 | AirPlay Server | Audio streaming, AirPlay 2, multi-room | Custom C++ | Apple ekosistemi |
| 14 | Chromecast Sender | Media casting, custom receiver, analytics | Google Cast SDK | Google ekosistemi |
| 15 | WebRTC Peer | Peer connection, ICE, DTLS, SRTP | libnice | P2P iletişim |
| 16 | WebRTC Signaling | SDP offer/answer, WebSocket signaling | Custom PHP | Oturum kurma |
| 17 | WebRTC STUN | NAT traversal, server reflexive candidate | Custom STUN | NAT keşfi |
| 18 | WebRTC TURN | Relay fallback, credential management | Coturn | NAT geçişi |
| 19 | DNS Resolution | Recursive resolver, caching, TTL management | Unbound | İsim çözümleme |
| 20 | DNS Caching | Local cache, negative cache, prefetch | Custom PHP | Performans |
| 21 | DNS over HTTPS | Encrypted DNS, Cloudflare/Google upstream | DoH client | Güvenlik |
| 22 | DHCP | IP allocation, lease management, reservations | ISC DHCP | Ağ yapılandırması |
| 23 | Zeroconf | Automatic addressing, name registration | Avahi | Sıfır yapılandırma |
| 24 | Network Monitor | Bandwidth monitoring, packet analysis, alerts | Prometheus, Custom | Ağ izleme |
| 25 | Network Diagnostics | Ping, traceroute, DNS lookup, speed test | Custom PHP | Ağ teşhisi |
| 26 | Network Switch | VLAN, QoS, port mirroring, STP | Managed Switch | Ağ altyapısı |
| 27 | Firewall | iptables/nftables, rules, logging, zones | nftables | Güvenlik duvarı |
| 28 | VPN Client | WireGuard, OpenVPN, split tunneling | WireGuard | Güvenli tünel |
| 29 | Proxy Client | HTTP/SOCKS5 proxy, authentication, rotation | Custom PHP | Proxy erişimi |
| 30 | Load Balancer | L4/L7 balancing, health checks, sticky sessions | Nginx, HAProxy | Yük dengeleme |
| 31 | CDN Integration | Static asset caching, purge, origin pull | Cloudflare/Custom | İçerik dağıtımı |
| 32 | SSL/TLS Cert | Let's Encrypt, auto-renewal, OCSP stapling | Certbot | Sertifika yönetimi |
| 33 | Connection Pool | Persistent connections, keep-alive, reuse | PHP, Nginx | Bağlantı yönetimi |
| 34 | Rate Limiting | Token bucket, sliding window, IP blocking | Nginx, APCu | Abuse önleme |
| 35 | Network Security | DDoS protection, bot detection, geo-blocking | Cloudflare/WAF | Ağ güvenliği |
| 36 | Traffic Shaping | Bandwidth allocation, QoS, priority queues | tc, Nginx | Trafik yönetimi |
| 37 | Packet Inspection | Deep packet inspection, protocol detection | nftables | Paket analizi |
| 38 | Network Logging | Connection logs, bandwidth logs, error logs | Custom, syslog | Ağ logları |
| 39 | Multicast | UDP multicast, group management, IGMP | Custom C | Grup iletişimi |
| 40 | Bandwidth Monitor | Real-time throughput, historical data, alerts | Prometheus | Bant genişliği izleme |

## Mimari Diyagram

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                    K14 — NETWORK ARCHITECTURE                                │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                        EXTERNAL NETWORK                            │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │ Internet │  │  CDN     │  │  DNS     │  │  STUN/   │          │     │
│  │  │          │  │ Cloudflare│  │ Unbound  │  │  TURN    │          │     │
│  │  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘          │     │
│  └───────┼──────────────┼──────────────┼──────────────┼───────────────┘     │
│          │              │              │              │                      │
│          ▼              ▼              ▼              ▼                      │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     FIREWALL / WAF                                  │     │
│  │              nftables · Rate Limiting · DDoS Protection            │     │
│  └────────────────────────────┬────────────────────────────────────────┘     │
│                               │                                             │
│                               ▼                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     LOAD BALANCER (Nginx)                           │     │
│  │              L7 Routing · SSL Termination · Health Checks          │     │
│  └───────┬─────────────┬──────────────┬──────────────┬────────────────┘     │
│          │             │              │              │                       │
│          ▼             ▼              ▼              ▼                       │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐                   │
│  │  Music   │  │  Admin   │  │  Auth    │  │  Media   │                   │
│  │  :81     │  │  :80     │  │  :443    │  │  :5000   │                   │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘                   │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     LOCAL DISCOVERY                                 │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │  mDNS    │  │  SSDP    │  │  UPnP    │  │  DLNA    │          │     │
│  │  │ Avahi    │  │ Custom   │  │ GUPnP    │  │ Custom   │          │     │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘          │     │
│  └─────────────────────────────────────────────────────────────────────┘     │
│                                                                              │
│  ┌─────────────────────────────────────────────────────────────────────┐     │
│  │                     STREAMING                                       │     │
│  │                                                                     │     │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐          │     │
│  │  │ AirPlay  │  │ Chromecast│  │  WebRTC  │  │  DLNA    │          │     │
│  │  │ Server   │  │ Sender   │  │  P2P     │  │ Renderer │          │     │
│  │  └──────────┘  └──────────┘  └──────────┘  └──────────┘          │     │
│  └─────────────────────────────────────────────────────────────────────┘     │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Protokol Karşılaştırması

| Protokol | Katman | Hız | Güvenlik | Kullanım |
|----------|--------|-----|----------|----------|
| HTTP/1.1 | Uygulama | Orta | TLS ile | Legacy API |
| HTTP/2 | Uygulama | Yüksek | TLS ile | Modern API |
| HTTP/3 QUIC | Uygulama | En yüksek | Built-in TLS | Gelecek |
| WebSocket | Uygulama | Yüksek | TLS ile | Gerçek zamanlı |
| WebRTC | Uygulama | En yüksek | DTLS/SRTP | P2P medya |
| TCP | Taşıma | Yüksek | — | Güvenilir |
| UDP | Taşıma | En yüksek | — | Streaming |
| TLS 1.3 | Oturum | Yüksek | — | Güvenlik |
| mDNS | Uygulama | Yüksek | Yok | Yerel keşif |
| SSDP | Uygulama | Orta | Yok | Cihaz keşif |

## WebRTC Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│                    WebRTC CONNECTION FLOW                        │
│                                                                  │
│  Peer A                    Signaling                Peer B      │
│    │                            │                      │         │
│    │──── SDP Offer ────────────▶│──── SDP Offer ──────▶│         │
│    │                            │◀─── SDP Answer ──────│         │
│    │◀─── SDP Answer ───────────│                      │         │
│    │                            │                      │         │
│    │──── ICE Candidate ────────▶│──── ICE Candidate ──▶│         │
│    │◀─── ICE Candidate ────────│◀─── ICE Candidate ───│         │
│    │                            │                      │         │
│    │◀═════════ DTLS Handshake ════════════════════════▶│         │
│    │                            │                      │         │
│    │◀═════════ SRTP Media Stream ═════════════════════▶│         │
│    │                            │                      │         │
│                                                                  │
│  NAT Traversal:                                                  │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐                   │
│  │   STUN   │───▶│   NAT    │───▶│  Public  │                   │
│  │  Server  │    │  Mapping │    │   IP     │                   │
│  └──────────┘    └──────────┘    └──────────┘                   │
│                                                                  │
│  Fallback:                                                       │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐                   │
│  │   TURN   │───▶│  Relay   │───▶│  Peer B  │                   │
│  │  Server  │    │  Server  │    │          │                   │
│  └──────────┘    └──────────┘    └──────────┘                   │
└─────────────────────────────────────────────────────────────────┘
```

## Port Haritası

| Port | Protokol | Servis | Kapsam |
|------|----------|--------|--------|
| 80 | TCP | Nginx (HTTP→HTTPS redirect) | Public |
| 443 | TCP | Nginx (HTTPS) | Public |
| 81 | TCP | Music Service | Internal |
| 3001 | TCP | Download Service | Internal |
| 3306 | TCP | MySQL 9 | Internal |
| 5000/6000 | TCP | Media Service | Internal |
| 9741 | TCP | Audio Service (REST) | Internal |
| 9742 | TCP | Audio Service (WebSocket) | Internal |
| 5353 | UDP | mDNS | Local |
| 1900 | UDP | SSDP | Local |
| 5004 | UDP | WebRTC RTP | P2P |
| 5004-5010 | UDP | WebRTC Media | P2P |

## Güvenlik Katmanları

```
┌─────────────────────────────────────────────────────┐
│                NETWORK SECURITY LAYERS               │
│                                                      │
│  Layer 7 (Uygulama)   ─── WAF, Rate Limiting        │
│  Layer 6 (Oturum)     ─── TLS 1.3, DTLS             │
│  Layer 5 (Oturum)     ─── SSL/TLS Cert              │
│  Layer 4 (Taşıma)     ─── Firewall, DDoS            │
│  Layer 3 (Ağ)         ─── IP Filtering, VPN         │
│  Layer 2 (Veri)       ─── VLAN, 802.1X              │
│  Layer 1 (Fiziksel)   ─── Switch Security           │
│                                                      │
│  ┌──────────────────────────────────────────┐       │
│  │  Defense in Depth Strategy                │       │
│  │                                           │       │
│  │  Internet → CDN/WAF → Firewall → LB →   │       │
│  │  Nginx → PHP-FPM → Application          │       │
│  └──────────────────────────────────────────┘       │
└─────────────────────────────────────────────────────┘
```

## GitHub Referansları

| Proje | Amaç | Lisans |
|-------|------|--------|
| [libnice/libnice](https://github.com/libnice/libnice) | ICE algorithm implementation for WebRTC | LGPL-2.1 |
| [gupnp](https://wiki.gnome.org/Projects/GUPnP) | UPnP/DLNA stack for Linux | LGPL-2.0 |
| [signalwire/freeswitch](https://github.com/signalwire/freeswitch) | Telephony platform, media handling | MPL-2.0 |

## İlişkili Katmanlar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K03 | Network servisi | Servisler arası iletişim K14 üzerinden |
| K12 | Ağ logları | Ağ olayları K12'ye akar |
| K15 | Streaming kaynağı | Medya streaming K14 tarafından taşınır |
| K06 | Servis keşfi | Servisler mDNS/SSDP ile keşfedilir |
| K09 | Güvenlik katmanı | Firewall/WAF K14 altında |

## Doğrulama

- [ ] HTTP/2 ve HTTP/3 desteği aktif
- [ ] WebSocket bağlantısı sağlıklı
- [ ] TLS 1.3 tüm public servislerde aktif
- [ ] mDNS servisleri keşfedilebilir
- [ ] SSDP/UPnP cihazları tanımlanıyor
- [ ] DLNA medya paylaşımı çalışıyor
- [ ] AirPlay ses streaming'i çalışıyor
- [ ] Chromecast casting çalışıyor
- [ ] WebRTC P2P bağlantısı kuruluyor
- [ ] DNS çözümlenmesi sağlıklı
- [ ] Firewall kuralları uygulanıyor
- [ ] Rate limiting aktif
- [ ] CDN önbellek çalışıyor
- [ ] SSL sertifikaları geçerli
- [ ] VPN bağlantısı sağlıklı

---

## Class AB Ağ Entegrasyonu

- [[electronics/amplifier-classab-circuit]] — Ağ üzerinden amplifikatör kontrolü
- [[electronics/power-supply-classab]] — Uzaktan güç yönetimi

---

## Ağ Detayı (Eski 10-network/ Referansları)

### 4 İletişim Protokolü

```
┌─────────────────────────────────────────────────────────────────────┐
│                    4 İLETİŞİM PROTOKOLÜ                              │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 1. HTTP/HTTPS                                              │   │
│  │    • REST API calls                                        │   │
│  │    • GraphQL (opsiyonel)                                   │   │
│  │    • Server-Sent Events                                    │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 2. WebSocket + MQTT                                        │   │
│  │    • Real-time notifications                               │   │
│  │    • Live player sync                                      │   │
│  │    • IoT device communication                              │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 3. gRPC + IPC                                              │   │
│  │    • Microservice communication                            │   │
│  │    • Inter-process communication                           │   │
│  │    • Audio engine ↔ Service layer                          │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ 4. Local Socket                                           │   │
│  │    • Unix domain sockets (Linux/macOS)                     │   │
│  │    • Named pipes (Windows)                                 │   │
│  │    • Shared memory (high-performance)                      │   │
│  └─────────────────────────────────────────────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

### DLNA/AirPlay/Chromecast Entegrasyonu

| Protokol | Kullanım | Port |
|----------|----------|------|
| DLNA/UPnP | Medya paylaşımı | 1900 (SSDP), 8200 (DLNA) |
| AirPlay | Apple cihazlar | 5000, 7000, 7100 |
| Chromecast | Google cihazlar | 8008, 8009 |
| mDNS | Servis keşfi | 5353 |
| SSDP | Cihaz bulma | 1900 |

### WebRTC

```
┌─────────────────────────────────────────────────────────────────────┐
│                    WEBRTC MİMARİSİ                                   │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ Peer A       │────►│ Signaling    │◄────│ Peer B       │       │
│  │ (Browser)    │     │ Server       │     │ (Browser)    │       │
│  └──────┬───────┘     └──────────────┘     └──────┬───────┘       │
│         │                                          │                │
│         ▼                                          ▼                │
│  ┌──────────────┐                           ┌──────────────┐      │
│  │ STUN/TURN    │◄──────────────────────────│ STUN/TURN    │      │
│  │ Server       │                           │ Server       │      │
│  └──────────────┘                           └──────────────┘      │
│         │                                          │                │
│         └──────────── P2P Connection ──────────────┘               │
│                        (ICE/DTLS/SRTP)                             │
└─────────────────────────────────────────────────────────────────────┘
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
