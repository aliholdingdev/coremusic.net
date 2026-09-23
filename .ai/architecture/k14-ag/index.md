---
title: "K14 Ağ & İletişim Katmanı"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# K14 Ağ & İletişim Katmanı

## Genel Bakış

K14, COREMUSIC projesinin tüm ağ iletişimini merkezi olarak yöneten katmandır. Bu katman, yerel ağ (LAN) ve geniş alan ağı (WAN) üzerindeki tüm protokol uç noktalarını, keşif mekanizmalarını ve güvenlik altyapısını barındırır. Gerçek zamanlı ses akışı, çoklu oda senkronizasyonu ve peer-to-peer iletişim bu katmanın temel sorumlulukları arasındadır.

## Ağ Mimarisi

```
┌─────────────────────────────────────────────────────────┐
│                    Uygulama Katmanı                       │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │
│  │ HTTP/2.3 │ │WebSocket │ │  mDNS    │ │ DLNA/UPnP│    │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘    │
│       │             │            │             │          │
│  ┌────┴─────┐ ┌────┴─────┐ ┌────┴─────┐ ┌────┴─────┐    │
│  │ AirPlay  │ │ WebRTC   │ │DNS Resolver│ │ VPN     │    │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘    │
├───────┼─────────────┼────────────┼─────────────┼─────────┤
│       │         Güvenlik Katmanı             │          │
│  ┌────┴─────────────┴────────────┴─────────────┴─────┐   │
│  │              TLS 1.3 / Certificate Mgmt           │   │
│  └─────────────────────┬─────────────────────────────┘   │
├────────────────────────┼─────────────────────────────────┤
│                  Optimizasyon Katmanı                    │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐     │
│  │Load Balancing│ │Conn. Pooling │ │ Multicast     │     │
│  └──────┬───────┘ └──────┬───────┘ └──────┬───────┘     │
├─────────┼────────────────┼────────────────┼──────────────┤
│                  Kalite Katmanı                          │
│  ┌──────┴────────────────┴────────────────┴───────┐      │
│  │              QoS / DSCP Marking                 │      │
│  └────────────────────────────────────────────────┘      │
└─────────────────────────────────────────────────────────┘
```

## Protokol Destekleri

| Protokol | Versiyon | Amaç | Durum |
|----------|----------|------|-------|
| HTTP | 2 / 3 | REST API, akış | Aktif |
| WebSocket | RFC 6455 | Gerçek zamanlı bidirectional | Aktif |
| mDNS | RFC 6762 | Yerel ağ keşfi | Aktif |
| DLNA | 1.5 | Medya paylaşımı | Aktif |
| UPnP | 2.0 | Cihaz keşfi | Aktif |
| AirPlay | 2 | iOS ses akışı | Aktif |
| WebRTC | N/A | P2P medya | Aktif |
| DNS | RFC 1034/1035 | İsim çözümleme | Aktif |
| WireGuard | N/A | VPN tünel | Aktif |
| TLS | 1.3 | Şifreleme | Aktif |
| QUIC | RFC 9000 | Hızlı bağlantı | Aktif |

## Katman İçi Bağımlılıklar

- **Giriş**: K0 (İşletim Sistemi) — soket seviyesi I/O
- **Giriş**: K1 (Donanım) — NIC kartı, WiFi modülü
- **Çıkış**: K7 (Middleware) — HTTP pipeline entegrasyonu
- **Çıkış**: K8 (Servisler) — servis discovery
- **Yatay**: K6 (Güvenlik) — sertifika yönetimi

## Temel Modüller

1. **Bağlantı Yöneticisi**: Tüm TCP/UDP bağlantilarını merkezi yönetir
2. **Protokol Motoru**: HTTP/2, HTTP/3, WebSocket protokollerini dönüştürür
3. **Keşif Servisi**: mDNS, UPnP, DLNA ile cihaz ve servis keşfi
4. **Akış Motoru**: AirPlay, WebRTC, multicast akışları
5. **Güvenlik Katmanı**: TLS el sıkışma, sertifika yönetimi
6. **Optimizasyon**: Load balancing, connection pooling, QoS

## Konfigürasyon

```yaml
k14_network:
  interfaces:
    - name: "eth0"
      type: "ethernet"
      enabled: true
    - name: "wlan0"
      type: "wifi"
      enabled: true
  protocols:
    http2:
      max_concurrent_streams: 100
      initial_window_size: 1048576
    http3:
      enabled: true
      max_udp_datagram_size: 1200
    websocket:
      ping_interval: 30000
      max_frame_size: 65536
  discovery:
    mdns:
      enabled: true
      domain: "local"
      ttl: 120
    upnp:
      enabled: true
      ssdp_port: 1900
  security:
    tls_version: "1.3"
    cipher_suites:
      - "TLS_AES_256_GCM_SHA384"
      - "TLS_CHACHA20_POLY1305_SHA256"
  qos:
    dscp_marking: true
    audio_priority: "EF"
    control_priority: "AF41"
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | Soket I/O, DNS |
| K1 | Giren | NIC, WiFi donanımı |
| K6 | Çıkan | Güvenlik politikaları |
| K7 | Çıkan | Middleware pipeline |
| K8 | Çıkan | Servis kayıtları |

## Durum: Implementasyon

- [x] HTTP/2 multiplexing
- [x] HTTP/3 QUIC desteği
- [x] WebSocket real-time iletişim
- [x] mDNS cihaz keşfi
- [x] DLNA/UPnP medya paylaşımı
- [x] AirPlay 2 ses akışı
- [x] WebRTC P2P
- [x] DNS çözümleyici
- [x] WireGuard VPN
- [x] TLS 1.3 sertifika yönetimi
- [x] Load balancing
- [x] Connection pooling
- [x] Multicast akış
- [x] QoS DSCP
