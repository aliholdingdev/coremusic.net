---
type: electronic
category: network-drivers
title: "CoreMusic — Network Audio Drivers"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Network Audio Drivers

**See also:** [[electronic/drivers/index]] · [[architecture/10-network/index]] · [[electronic/drivers/wifi-drivers]] · [[electronic/drivers/device-integration]]

> **Repo gerçeği:** Bu depoda Ethernet/kart (NIC) çalıştıran gerçek bir C/C++ sürücü kodu **YOK** (`.cpp`/`.c`/`.h` taraması 0 sonuç). Bu dosya, `.ai/electronic/**` vault mimarisinin ağ sürücüsü katmanı tasarımını ve ağ performans gereksinimlerini tanımlar — çalışan kod kanıtı değil, **tasarım şartnamesidir**.

---

## 1. Amaç & NIC Katmanı

Network Audio Drivers, CoreMusic platformunun Ethernet ve network ses protokollerini tanımlar. Bu katman, donanım NIC'i (Network Interface Card) ile üst yığın (TCP/UDP yığını) arasındaki veri aktarımını taşır.

### 1.1 Katman Konumu (OSI Eşlemesi)

```
┌────────────────────────────────────────────────┐
│  Uygulama (Player / CoreMusic Engine)          │  ← OSI 7
├────────────────────────────────────────────────┤
│  WiFi/Ethernet Ses Protokolleri                │
│  (DLNA/AirPlay/Roon/WebRTC)                    │  ← OSI 5-7
├────────────────────────────────────────────────┤
│  TCP / UDP / RTP (transport)                   │  ← OSI 4
├────────────────────────────────────────────────┤
│  IP (IPv4/IPv6) + ICMP                         │  ← OSI 3
├────────────────────────────────────────────────┤
│  Ethernet (Ethernet II) + ARP                  │  ← OSI 2
├────────────────────────────────────────────────┤
│  NIC Sürücüsü (BU DOSYA — ring buffer, DMA)    │  ← OSI 1-2
├────────────────────────────────────────────────┤
│  Fiziksel NIC (RJ45 / PHY)                     │  ← OSI 1
└────────────────────────────────────────────────┘
```

### 1.2 NIC Sürücü Bileşenleri (Bileşen Tablosu)

| Bileşen | Görev | Ses Etkisi |
|---------|-------|------------|
| TX/RX ring buffer | Paket kuyruğu | Underflow/overflow dropout |
| DMA motoru | Bellek ↔ NIC aktarımı | CPU yükü, jitter |
| Interrupt (IRQ/NAPI) | Paket bildirimi | Latency |
| Offload (TSO/GSO/GRO) | Yazılım yerine donanım | CPU, paket boyutu |
| Link status | Bağlantı durumu | Kesinti algılama |

### 1.3 Ağ Protokol Desteği (Protokol Tablosu)

| Protokol | Kullanım | Latency |
|----------|----------|---------|
| mDNS | Cihaz keşfi | <100ms |
| DLNA/UPnP | Medya paylaşımı | 100-500ms |
| AirPlay | Apple cihazlar | ~2s |
| Roon | Profesyonel | <100ms |
| WebRTC | Real-time | <50ms |

**Protokol→katman eşlemesi:**

| Protokol | Transport | Port | Ağ Katmanı |
|----------|-----------|------|------------|
| mDNS | UDP | 5353 | 224.0.0.251 (IPv4), ff02::fb (IPv6) |
| SSDP (DLNA) | UDP | 1900 | 239.255.255.250 |
| AirPlay | TCP | 7000 (tipik) | — |
| Roon (RAIS) | TCP | 49152+ (tipik) | — |
| WebRTC | UDP (SRTP) | dinamik | ICE/STUN/TURN |

### 1.4 NIC Sürücü Türleri (Driver Types)

| Tür | Açıklama | Kullanım |
|-----|----------|----------|
| PMD (Poll Mode) | Interrupt yok, polling | Düşük latency (DPDK benzeri) |
| Interrupt-driven | IRQ ile bildirim | Genel kullanım |
| NAPI (ybrid) | IRQ + polling | Linux yaygın |
| User-space bypass | Kernel bypass | Ultra-düşük latency |

> ⚠️ VERIFICATION REQUIRED: CoreMusic'in hangi NIC sürücü türünü kullanacağı vault'ta tanımlı değil; bu tablo yalnızca seçenekleri tanımlar.

---

## 2. Paket Akışı (Packet Flow)

### 2.1 TX (Transmit) Akışı

```
Uygulama (Player)
  │ send()/write()
  ▼
TCP/UDP yığını
  │ segment/paket
  ▼
IP katmanı
  │ paket + DSCP (§4)
  ▼
Ethernet + ARP
  │ çerçeve (frame)
  ▼
NIC sürücüsü → TX ring → DMA → NIC
  │
  ▼
Fiziksel ağ (RJ45)
```

### 2.2 RX (Receive) Akışı

```
Fiziksel ağ (RJ45)
  │
  ▼
NIC → RX ring ← DMA ← NIC sürücüsü
  │ interrupt/NAPI
  ▼
Ethernet + ARP
  │ çerçeve doğrulama (CRC/FCS)
  ▼
IP katmanı
  │ yönlendirme
  ▼
TCP/UDP yığını
  │ defragment/reassembly
  ▼
Uygulama (Player) → jitter buffer → decode
```

### 2.3 Paket Zamanlaması (Packet Timing)

| Aşama | Gecikme (tipik) | Not |
|-------|-----------------|-----|
| Uygulama → TCP/UDP | <1 ms | Buffer |
| TCP/UDP → IP | <1 ms | Routing |
| IP → Ethernet | <1 ms | ARP |
| Ethernet → NIC ring | <1 ms | DMA |
| NIC → Fiziksel | <1 ms | PHY |
| **Toplam (TX)** | **<5 ms** | LAN hedefi |

### 2.4 Paket Kaybı Noktaları (Loss Points)

| Nokta | Neden | Belirti | Çözüm |
|-------|-------|---------|-------|
| TX ring overflow | Yüksek yük | Düşük throughput | Ring büyütme (§3) |
| RX ring overflow | Yüksek trafik | Paket kaybı | NAPI tuning |
| ARP cache miss | IP değişimi | Gecikme | Statik ARP |
| TCP retransmit | Ağ hatası | Latency ↑ | UDP'ye geç (gerçek zamanlı) |
| ICMP throttling | Ping flood koruması | Route keşfi yavaş | Statik route |

---

## 3. Ring Buffer, TSO/GSO ve Offload

### 3.1 Ring Buffer (Descriptor Ring)

| Yön | varsayılan | Tavsiye (ses) | Not |
|-----|-----------|---------------|-----|
| TX ring | 256 descriptor | 1024+ | Underflow önleme |
| RX ring | 256 descriptor | 1024+ | Overflow önleme |

**Ring buffer hesabı:**

```
Ring boyutu = descriptor sayısı × paket başına ortalama boyut
Örnek: 1024 descriptor × 1500 bayt (MTU) ≈ 1.5 MB ring
Tavsiye: ses akışı için ≥2 MB ring (her yön)
```

**Ring buffer kuralı:** 48kHz/32-bit/stereo ses akışı ≈ 384 KB/s (§7.1); ring buffer, en az 1 saniyelik akışı (≈400 KB) barındırmalıdır — ağ sıçramalarına (burst) karşı tampon.

### 3.2 TSO/GSO/GRO Offload

| Offload | Tam Adı | Görev | Ses Etkisi |
|---------|---------|-------|------------|
| TSO | TCP Segmentation Offload | Büyük segmenti donanıma bölme | CPU ↓ |
| GSO | Generic Segmentation Offload | TSO'nun yazılım sürümü | CPU orta |
| GRO | Generic Receive Offload | RX birleştirme | CPU ↓ |
| LRO | Large Receive Offload | RX birleştirme (donanım) | CPU ↓ |

**Offload kuralları:**

1. TSO/GSO, düşük latency gerektirmeyen (kaydedilmiş içerik) akışlarda etkinleştirilir.
2. Gerçek zamanlı (WebRTC, senkron multi-room) akışlarda TSO **kapatılır** — paket boyutu küçük kalır, jitter düşer.
3. GRO/LRO, RX tarafında CPU yükünü azaltır; ses akışında etkin tutulur.

### 3.3 Checksum Offload

| Offload | Görev | Ses Etkisi |
|---------|-------|------------|
| TX checksum | Donanımda checksum hesabı | CPU ↓ |
| RX checksum doğrulama | Donanımda doğrulama | CPU ↓ |

### 3.4 Interrrupt Coalescing (IRQ Birleştirme)

| Ayar | varsayılan | Tavsiye (ses) | Not |
|------|-----------|---------------|-----|
| IRQ coalescing süresi | 50 µs | 10–20 µs | Düşük latency |
| NAPI poll budget | 64 paket | 32 paket | Düşük latency |

**Coalescing kuralı:** Coalescing süresi arttıkça CPU yükü ↓, latency ↑; ses akışı için ≤20 µs hedeflenir.

---

## 4. VLAN, QoS ve IP Planlama

### 4.1 VLAN Etiketleme (802.1Q)

| VLAN | Kullanım | Öncelik |
|------|----------|---------|
| VLAN 10 | Ses akışı (management) | Yüksek |
| VLAN 20 | Medya verisi (payload) | En yüksek |
| VLAN 30 | Control (RTSP/HTTP) | Yüksek |
| VLAN 99 | Misafir / bakım | Düşük |

**VLAN kuralları:**

1. Ses akışı (VLAN 20) misafir trafiğinden (VLAN 99) ayrılmalıdır.
2. VLAN trunk, switch'te 802.1Q ile tanımlanır.
3. Router, VLAN'ler arası yönlendirmede DSCP korur (§4.2).

### 4.2 DSCP (Differentiated Services) İşaretleme

| Trafik | DSCP | WMM AC (WiFi) | Ethernet CoS (802.1p) |
|--------|------|---------------|------------------------|
| Ses verisi | EF (46) | AC_VO | 5 (En yüksek) |
| Ses kontrol | AF41 (34) | AC_VI | 4 |
| Keşif (mDNS/SSDP) | CS0 (0) | AC_BE | 0 (Default) |
| Arka plan | CS1 (8) | AC_BK | 1 |

### 4.3 IP Planlama (IPv4/IPv6)

| Ağ | Kullanım | RFC |
|----|----------|-----|
| 192.168.x.x /24 | Yerel LAN (özel) | RFC 1918 |
| 10.x.x.x /8 | Geniş yerel | RFC 1918 |
| 172.16.x.x /12 | Orta ölçek | RFC 1918 |
| /31 (point-to-point) | NIC ↔ NIC | RFC 3021 |
| IPv6 link-local | fe80::/10 | RFC 4862 (SLAAC) |
| IPv6 multicast | ff02::fb (mDNS) | RFC 1112 (IGMP/MLD) |

**IP planlama kuralları:**

1. Ses NIC'i statik IP alır (DHCP rezervasyonu) — yeniden bağlanma gecikmesi önlenir.
2. IPv6, IPv4 ile çift-yığın (dual-stack) çalışır; mDNS IPv6 multicast (ff02::fb) izinli olmalıdır.
3. Özel ağlar (RFC 1918) router'da NAT dışındadır; VLAN'ler arası erişim firewall ile kontrol edilir.

### 4.4 TCP/UDP Parametreleri (Ses Akışı)

| Parametre | varsayılan | Tavsiye (ses) | Not |
|-----------|-----------|---------------|-----|
| TCP window size | 64 KB | 256 KB+ | Yüksek throughput |
| TCP_NODELAY (Nagle) | Kapalı | **Açık** | Düşük latency |
| UDP buffer (SO_RCVBUF) | 208 KB | 1 MB+ | Burst absorption |
| MTU | 1500 bayt | 1500 (varsayılan) | Jumbo frame ≈9000 (LAN) |

**Nagle kuralı:** Ses akışında `TCP_NODELAY` etkin olmalıdır — Nagle algoritması küçük paketleri biriktirir, latency ↑.

---

## 5. Multi-Room Streaming

### 5.1 Multi-Room Tablosu

| Özellik | Değer |
|---------|-------|
| Max Oda | 32 |
| Senkronizasyon | <1ms |
| Protokol | P2P veya Hub |
| Latency | 10-50ms |

### 5.2 Senkron Mimarisi (Sync Architecture)

| Mimarî | Açıklama | Avantaj | Dezavantaj |
|--------|----------|---------|------------|
| Hub-and-spoke | Merkez → oda | Basit senkron | Tek nokta hata |
| P2P | Oda ↔ oda | Dayanıklılık | Senkron karmaşık |
| Hybrid | Hub + P2P backup | Dengeli | Karmaşık |

### 5.3 Senkron Protokolleri

| Protokol | Mekanizma | Tolerans |
|----------|-----------|----------|
| PTP (IEEE 1588) | Zaman damgası | µs seviyesi |
| NTP | Zaman damgası | ms seviyesi |
| Uygulama senkronu | Sequence number + timestamp | ≤1 ms |

> ⚠️ VERIFICATION REQUIRED: CoreMusic'in hangi senkron protokolünü kullanacağı vault'ta tanımlı değil; PTP, NTP ve uygulama senkronu yalnızca seçeneklerdir.

### 5.4 Akış (Streaming) Akışı

```
mDNS Discovery ──▶ Oda Seç ──▶ TCP Bağlantı ──▶ Kimlik Doğrula ──▶ Senkronizasyon ──▶ Stream Başlat ──▶ İzleme
```

**Akış adımları:**

| Adım | İşlem | Hata Modu |
|------|-------|-----------|
| 1 | mDNS Discovery (§1.3) | Oda bulunamıyor |
| 2 | Oda seçimi (render point) | Yanlış oda |
| 3 | TCP bağlantısı (Nagle kapalı §4.4) | Port reddi |
| 4 | Kimlik doğrulama (RTSP/Roon) | Yetkisiz |
| 5 | Senkronizasyon (§5.3) | Senkron kayması |
| 6 | Stream başlatma | İlk paket kaybı |
| 7 | İzleme (heartbeat) | Bağlantı timeout |

### 5.5 Multi-Room Hata Modları

| Hata | Belirti | Kök Neden | Çözüm |
|------|---------|-----------|-------|
| Oda bulunamıyor | mDNS boş | VLAN/multicast engelli | IGMP snooping + multicast izni |
| Senkron kayması | Odalar arası eko | Zaman damgası kayması | PTP/NTP senkronu |
| Tek oda kesildi | Oda susuyor | Ağ hatası | Hub-and-spoke → P2P fallback |
| Latency artışı | Gecikme | Buffer taşması / TCP | UDP + jitter buffer |

---

## 6. Ağ Topoloji Kuralları

| Kural | Açıklama |
|-------|----------|
| VLAN ayrımı | Ses (20), medya (20), kontrol (30), misafir (99) |
| Statik IP | Ses NIC'i statik (DHCP rezervasyonu) |
| QoS (DSCP) | EF (46) → AC_VO → CoS 5 |
| Switch | Yönetilebilir (802.1Q + IGMP snooping) |
| Router | DSCP koruma + VLAN yönlendirme |
| MTU | 1500 (varsayılan), jumbo ≈9000 (LAN) |

---

## 7. Ölçüm (Measurement)

### 7.1 Throughput Gereksinimi

| Format | Ham bitrate (stereo) | Sıkıştırılmış (FLAC ≈ %60) | Gereken NIC |
|--------|---------------------|----------------------------|-------------|
| 16-bit/44.1kHz | 1.41 Mbps | ≈0.85 Mbps | 100 Mbps |
| 24-bit/96kHz | 4.61 Mbps | ≈2.76 Mbps | 100 Mbps |
| 24-bit/192kHz | 9.22 Mbps | ≈5.53 Mbps | 1 Gbps |
| 32-bit/384kHz (Roon) | 24.58 Mbps | ≈14.75 Mbps | 1 Gbps |

**Hesap notu:** Ham bitrate = kanal sayısı × bit derinliği × örnekleme hızı. 8.1 surround (9 kanal, ADR-038) 24-bit/192kHz ≈ 41.5 Mbps ham / ≈25 Mbps FLAC — 1 Gbps NIC şart.

### 7.2 Latency Bütçesi

| Aşama | Değer (LAN) | Not |
|-------|-------------|-----|
| NIC ring (TX/RX) | <1 ms | DMA |
| TCP/UDP yığın | <1 ms | Routing |
| Ağ geçidi (switch/router) | <1 ms | 802.1Q |
| Jitter buffer | 10–50 ms | Tasarım |
| **Toplam (tipik)** | **12–53 ms** | LAN |

### 7.3 Ölçüm Protokolü

| Metod | Araç | Kabul |
|-------|------|-------|
| Throughput | iperf3 (TCP/UDP) | ≥2× gereksinim |
| Latency | ping / one-way timestamp | ≤5 ms (LAN hop) |
| Jitter | UDP timestamp analizi | ≤10 ms (p95) |
| Packet loss | iperf3 loss report | <0.1% |
| Roaming | WiFi AP'ler arası (§4 wifi) | ≤1 dropout |

### 7.4 Ölçüm Adımları

| Adım | İşlem | Çıktı |
|------|-------|-------|
| 1 | NIC/link status (§1.2) | Link up, hız |
| 2 | Ring buffer boyutu (§3.1) | ≥2 MB |
| 3 | Offload (§3.2) | TSO/GSO/GRO durumu |
| 4 | DSCP işaretleme (§4.2) | EF (46) |
| 5 | Throughput ölç (§7.1) | ≥2× gereksinim |
| 6 | Jitter/loss ölç (§7.3) | ≤10 ms / <0.1% |
| 7 | Raporla | Ölçüm kaydı |

### 7.5 Kabul Kriterleri

| Kriter | Eşik | Test |
|--------|------|------|
| Throughput | ≥2× gereksinim | iperf3 |
| Latency (toplam) | ≤100 ms | §7.2 |
| Jitter (p95) | ≤10 ms | §7.3 |
| Packet loss | <0.1% | iperf3 |
| Roaming dropout | ≤1 | §7.3 |
| Multi-room senkron | ≤1 ms | §5.3 |

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-026-download-service-architecture]] | Network mimarisi |
| [[ADR-037-wirelessconnect-integration]] | Kablosuz entegrasyon |

---

## 9. Kaynakça (Sources)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| RFC 894 — IP over Ethernet | https://www.rfc-editor.org/rfc/rfc894 | 2026-10-13 | HTTP 200 ✓ |
| RFC 2464 — IPv6 over Ethernet | https://www.rfc-editor.org/rfc/rfc2464 | 2026-10-13 | HTTP 200 ✓ |
| RFC 8200 — IPv6 Specification | https://www.rfc-editor.org/rfc/rfc8200 | 2026-10-13 | HTTP 200 ✓ |
| RFC 826 — ARP Protocol | https://www.rfc-editor.org/rfc/rfc826 | 2026-10-13 | HTTP 200 ✓ |
| RFC 2474 — DSCP Field (DiffServ) | https://www.rfc-editor.org/rfc/rfc2474 | 2026-10-13 | HTTP 200 ✓ |
| RFC 2475 — DiffServ Architecture | https://www.rfc-editor.org/rfc/rfc2475 | 2026-10-13 | HTTP 200 ✓ |
| RFC 4594 — DiffServ Service Classes | https://www.rfc-editor.org/rfc/rfc4594 | 2026-10-13 | HTTP 200 ✓ |
| RFC 4571 — TCP/UDP over ... (framing) | https://www.rfc-editor.org/rfc/rfc4571 | 2026-10-13 | HTTP 200 ✓ |
| RFC 1918 — Private Address Space | https://www.rfc-editor.org/rfc/rfc1918 | 2026-10-13 | HTTP 200 ✓ |
| RFC 3021 — /31 Prefixes | https://www.rfc-editor.org/rfc/rfc3021 | 2026-10-13 | HTTP 200 ✓ |
| RFC 1112 — IGMP / IPv4 Multicast | https://www.rfc-editor.org/rfc/rfc1112 | 2026-10-13 | HTTP 200 ✓ |
| RFC 4862 — IPv6 SLAAC | https://www.rfc-editor.org/rfc/rfc4862 | 2026-10-13 | HTTP 200 ✓ |
| RFC 2544 — Network Benchmarking | https://www.rfc-editor.org/rfc/rfc2544 | 2026-10-13 | HTTP 200 ✓ |
| RFC 5681 — TCP Congestion Control | https://www.rfc-editor.org/rfc/rfc5681 | 2026-10-13 | HTTP 200 ✓ |
| RFC 4960 — SCTP (referans) | https://www.rfc-editor.org/rfc/rfc4960 | 2026-10-13 | HTTP 200 ✓ |
| Linux kernel networking doc | https://www.kernel.org/doc/html/latest/networking/index.html | 2026-10-13 | HTTP 200 ✓ |
| Linux kernel PCI doc | https://www.kernel.org/doc/html/latest/PCI/index.html | 2026-10-13 | HTTP 200 ✓ |

**Vault içi referanslar:** [[electronic/drivers/index]] · [[electronic/drivers/wifi-drivers]] · [[electronic/drivers/audio-drivers]] · [[architecture/10-network/index]] · [[electronic/drivers/device-integration]]

---

## 10. PTP (IEEE 1588) Hassas Zamanlama

### 10.1 PTP Gecikme Kompansasyonu (Matematik)

```
Tek yönlü gecikme (one-way delay):
  d = [(t2 − t1) + (t3 − t4)] / 2
  t1 = gönderim (master), t2 = alım (slave),
  t3 = yanıt gönderim (slave), t4 = yanıt alım (master)

Clock offset:
  θ = [(t2 − t1) − (t4 − t3)] / 2
Kabul: |θ| ≤ 1 µs (ses senkronu için), ≤ 100 ns (video+ses senkronu)
```

### 10.2 PTP vs NTP Karşılaştırması (Senkron Gerekçesi)

| Kriter | PTP (IEEE 1588) | NTP |
|--------|-----------------|-----|
| Doğruluk | µs–ns | ms |
| Donanım timestamp | Evet (NIC) | Hayır |
| Multi-room senkron | ≤1 ms (RAIS/§5.3) | Sınır |
| Ağ yükü | Düşük (multicast) | Düşük |
| Kurulum karmaşıklığı | Yüksek | Düşük |

### 10.3 Jumbo Frame (MTU 9000) hesabı

```
Ethernet header (14) + IP (20) + UDP (8) + payload
Jumbo MTU (9000) − overhead (42) = 8958 bayt yararlı yük
Fayda: 1 saniyelik 24-bit/192kHz FLAC (§7.1 ≈5.53 Mbps)
  5.53e6/8 = 691 KB/s → jumbo ile ~78 paket/sn (1500 MTU: ~461 paket/sn)
  Paket başına kesinti (interrupt) sayısı ↓ → CPU/jitter ↓
```

**Jumbo frame kuralı:** Tüm hop'lar (NIC, switch, router) aynı MTU'yu desteklemelidir; MTU uyuşmazlığı paket fragmentasyonu → jitter ↑. Ses ağında jumbo yalnızca LAN içinde (§4.1) etkinleştirilir.

### 10.4 Ölçüm Kaydı Şeması (Ağ)

| Alan | Tip | Örnek |
|------|-----|-------|
| timestamp | ISO 8601 | 2026-10-13T12:00:00Z |
| nic_link | enum | 1Gbps / 100Mbps |
| ptp_offset_us | float | 0.8 |
| mtu | int | 1500 / 9000 |
| dscp | int | 46 (EF) |
| throughput_mbps | float | 25.0 |
| verdict | enum | PASS / RED |

---

## 11. TCP vs UDP Ses Akışı Seçimi

### 11.1 Transport Seçim Matrisi

| Kriter | TCP | UDP |
|--------|-----|-----|
| Paket kaybı | Yeniden gönderir (§2.4) | Kaybeder |
| Zamanlama | Bozabilir (retransmit) | Korur |
| Gecikme | Değişken | Düşük/tutarlı |
| Kullanım | Kaydedilmiş/kayıpsız (Roon, dosya) | Gerçek zamanlı (WebRTC, senkron) |

### 11.2 UDP Buffer ve Kayıp Toleransı

```
UDP buffer (SO_RCVBUF) ≥ burst × jitter tamponu
24-bit/192kHz FLAC (§7.1 ≈5.53 Mbps) → 50 ms jitter:
  5.53e6/8 × 0.05 ≈ 34.6 KB (§7.2 wifi ile aynı)
Paket kaybı toleransı: ses akışında retransmit yok → jitter buffer emer (<0.1% §7.3)
```

**Seçim kuralı:** Gerçek zamanlı senkron (multi-room §5.4) → UDP + jitter buffer; dosya/Roon (kayıpsız) → TCP + `TCP_NODELAY` (§4.4). Nagle kapatma her ikisinde de geçerlidir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
