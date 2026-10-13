---
type: electronic
category: wifi-drivers
title: "CoreMusic — WiFi Audio Drivers (DLNA, AirPlay, Roon)"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — WiFi Audio Drivers

**See also:** [[electronic/drivers/index]] · [[electronic/drivers/network-drivers]] · [[electronic/drivers/device-integration]] · [[architecture/10-network/index]]

> **Repo gerçeği:** Bu depoda WiFi/DLNA/AirPlay/Roon çalıştıran gerçek bir C/C++ sürücü kodu **YOK** (`.cpp`/`.c`/`.h` taraması 0 sonuç). Bu dosya, `.ai/electronic/**` vault mimarisinin sürücü-katmanı tasarımını ve protokol gereksinimlerini tanımlar — çalışan kod kanıtı değil, **tasarım şartnamesidir**.

---

## 1. Amaç & İstemci/Denetleyici Katmanı

WiFi Audio Drivers, CoreMusic platformunun WiFi üzerinden ses akışı protokollerini (DLNA, AirPlay, Roon, UPnP) tanımlar. Bu katman, ağ sürücüsünün (network driver) üzerinde çalışır ve ses akışını ağ üzerinden taşır.

### 1.1 Katman Konumu

```
┌─────────────────────────────────────────────┐
│  Uygulama (Player / CoreMusic Engine)       │
├─────────────────────────────────────────────┤
│  WiFi Audio Protokol Katmanı (BU DOSYA)     │
│  DLNA · AirPlay · Roon · UPnP               │
├─────────────────────────────────────────────┤
│  Ağ Yığını (TCP/UDP/mDNS/SSDP)              │
├─────────────────────────────────────────────┤
│  WiFi Ağ Sürücüsü (network-drivers.md)      │
├─────────────────────────────────────────────┤
│  WiFi Donanımı (MAC/PHY — firmware katmanı) │
└─────────────────────────────────────────────┘
```

### 1.2 İstemci (Client) ve Denetleyici (Controller) Rollerindeki Dosyalar

| Rol | Dosya/Sınıf | Görev |
|-----|-------------|-------|
| Client | `WiFiAudioClient` | Bağlanılan cihazın kendisi (renderer) — akış alır |
| Controller | `WiFiController` | Cihazları keşfeder, akışı başlatır/durdurur |
| Discovery | `ServiceDiscovery` | SSDP (DLNA) + mDNS (AirPlay/Roon) tarar |
| Transport | `AudioTransport` | TCP/UDP üzerinden ses paketlerini taşır |

> ⚠️ VERIFICATION REQUIRED: Sınıf adları vault mimari tasarımından türetilmiştir; gerçek repo kodu yok (yukarıdaki repo gerçeği notu).

### 1.3 DLNA / UPnP

| Özellik | Değer |
|---------|-------|
| Protokol | DLNA 1.5 / UPnP AV |
| Transport | HTTP, TCP |
| Format | FLAC, WAV, MP3, AAC |
| Max Resolution | 24-bit/192kHz |
| Discovery | SSDP multicast |

**SSDP (Simple Service Discovery Protocol) detayları:**

| Parametre | Değer |
|-----------|-------|
| Multicast adresi | 239.255.255.250 |
| Port | 1900 (UDP) |
| Keşif mesajı | `M-SEARCH` |
| Duyuru mesajı | `NOTIFY` |
| Servis tipi | `urn:schemas-upnp-org:device:MediaRenderer:1` |

**DLNA rol üçgeni:** DMS (Digital Media Server — kaynak) → DMP (Digital Media Player) / DMR (Digital Media Renderer — hedef). CoreMusic bir **DMR** olarak çalışır (kaynak değil, akış alır).

**DLNA render zinciri (uçtan uca):**

```
DMS (kaynak: NAS/PC)
  │  AVTransport: SetAVTransportURI (medya URL'si)
  ▼
DMR (CoreMusic renderer)
  │  HTTP GET → medya verisi (FLAC/WAV paketleri)
  │  AVTransport: Play → decode → DAC
  ▼
Hoparlör çıkışı
```

**UPnP AV kontrol noktaları (control points):**

| Servis | İşlev | Temel eylemler |
|--------|-------|----------------|
| AVTransport | Akış kontrolü | `SetAVTransportURI`, `Play`, `Pause`, `Stop`, `Seek` |
| Rendering | Ses/görüntü ayarı | `SetVolume`, `SetMute`, `SetAudioOutputURI` |
| ConnectionManager | Format müzakeresi | `GetProtocolInfo`, `PrepareToConnection` |
| ContentDirectory | Kaynak gezinme | `Browse`, `Search` |

### 1.4 AirPlay

| Özellik | Değer |
|---------|-------|
| Protokol | AirPlay 2 |
| Transport | HTTP, mDNS |
| Format | ALAC, AAC |
| Max Resolution | 24-bit/48kHz |
| Latency | 2s (varsayılan) |
| Multi-room | Destekli |

**AirPlay keşif zinciri:** mDNS (RFC 6762) üzerinden `_airplay._tcp` ve `_raop._tcp` servis kayıtları yayınlanır; istemci bu kayıtları sorgulayarak cihazı bulur ve RTSP benzeri bir kontrol kanalıyla akışı başlatır.

**AirPlay mDNS servis kaydı örneği (tipik):**

```
_raop._tcp.local. PTR  "AABBCCDDEEFF@CoreMusic Renderer._raop._tcp.local."
CoreMusic Renderer._raop._tcp.local. SRV  host.local. 7000  (kontrol portu)
CoreMusic Renderer._raop._tcp.local. TXT  "features=0x5A7FFFF7,  "model=CoreMusic,  "pk=<public key>"
_airplay._tcp.local. PTR  "CoreMusic Renderer._airplay._tcp.local."
```

> ⚠️ VERIFICATION REQUIRED: TXT kayıtlarındaki `features` bitmask değeri Apple tarafından yayımlanmaz; yukarıdaki değer gösterim amaçlıdır (gerçek bitmask için ilgili cihazın mDNS yanıtına bakılır).

**AirPlay kontrol akışı (kavramsal):**

| Adım | Kanal | İşlem |
|------|-------|-------|
| 1 | mDNS | `_raop._tcp` keşfi (TXT kaydından cihaz özellikleri okunur) |
| 2 | TCP | Eşleştirme (SRP + pairing kodu — §3.3) |
| 3 | RTSP benzeri | `OPTIONS` / `ANNOUNCE` / `SETUP` |
| 4 | RTP/UDP veya TCP | Ses verisi akışı (ALAC/AAC) |
| 5 | RTSP benzeri | `TEARDOWN` (akışı sonlandırma) |

### 1.5 Roon

| Özellik | Değer |
|---------|-------|
| Protokol | Roon Ready |
| Transport | RAIS |
| Format | FLAC, WAV, DSD |
| Max Resolution | 32-bit/384kHz, DSD512 |
| DSP | Roon DSP Engine |
| Multi-room | Destekli |

> ⚠️ VERIFICATION REQUIRED: RAIS (Roon Advanced Internet Streaming) özel bir protokoldür; içeriği Roon Labs tarafından belgelenmiştir — bağımsız doğrulama kaynakla sınırlıdır.

**Roon Ready rolleri:**

| Rol | Görev | Not |
|-----|-------|-----|
| Roon Core | Kütüphane + DSP motoru | Tek bir Core ağda çalışır |
| Roon Ready endpoint | Akış alır, decode eder | CoreMusic renderer bu roldedir |
| Roon Remote | Kontrol istemcisi | iOS/Android/masaüstü |

**Roon DSP Engine katmanları (Core tarafında):**

| Katman | İşlem | Sıra |
|--------|-------|------|
| 1 | Upsampling | Decoder sonrası |
| 2 | EQ (parametric) | §6.4'e bkz. (dsp/equalizer.md) |
| 3 | Crossover | Aktif crossover (dsp/crossover.md) |
| 4 | Volume | Bit-perfect değilse |
| 5 | Dithering | Çıkış bit derinliğine göre |

### 1.6 Protokol Karşılaştırma Matrisi

| Kriter | DLNA/UPnP | AirPlay 2 | Roon Ready |
|--------|-----------|-----------|------------|
| Keşif | SSDP (multicast) | mDNS (multicast) | mDNS + proprietary |
| Transport | HTTP/TCP | TCP (RTSP benzeri kontrol) | RAIS (TCP) |
| Kontrol | UPnP AV Transport | RTSP benzeri | Roon proprietary |
| Çok-oda (multi-room) | Sınırlı (DMR gruplama) | Evet (AirPlay 2) | Evet (Roon gruplama) |
| Maks. çözünürlük | 24-bit/192kHz | 24-bit/48kHz | 32-bit/384kHz, DSD512 |
| Tipik latency | Ağ + decoder bağımlı | ~2s (varsayılan) | Düşük (RAIS hedefi) |
| DRM/şifreleme | Yok (DLNA 1.5) | AirFair (proprietary) | Yok |
| DSP | Renderer tarafında | Renderer tarafında | Roon DSP Engine (Core tarafında) |
| Bit-perfect | Destekli (FLAC/WAV) | Sınırlı (ALAC decode) | Destekli (doğrudan çıkış) |

### 1.7 Bağlantı Senaryoları (Connection Scenarios)

| Senaryo | Kullanım | Protokol tercihi | Not |
|---------|----------|------------------|-----|
| Ev/stüdyo yerel ağ | Yüksek kaliteli dinleme | Roon Ready | En yüksek çözünürlük |
| Misafir ağı | Basit paylaşım | AirPlay 2 | Kolay eşleştirme |
| Eski cihazlar | Uyumluluk | DLNA/UPnP | Geniş format desteği |
| Multi-room parti | Çoklu oda | AirPlay 2 veya Roon | Senkron kritik (§5.4) |

---

## 2. Tarama (Scan) ve Bağlanma (Association)

### 2.1 Keşif Akışı (Discovery Flow)

```
Başla ──▶ {Cihaz Keşfet}
              │
     DLNA ▼   ▼ AirPlay   ▼ Roon
  UPnP Discovery  mDNS Discovery  RAIS Discovery
        │              │              │
        │   M-SEARCH   │  PTR query   │  PTR query
        │  ──────────▶ │ ───────────▶ │ ──────────▶
        │  ◀────────── │ ◀─────────── │ ◀──────────
        │   200 OK     │  SRV/A/TXT   │  SRV/A/TXT
        └──────────────┴──────────────┘
                       │
                Cihaz Seç (render point)
                       │
                Bağlantı Kur (TCP/HTTP oturumu)
                       │
                Stream Başlat (SETURI / PLAY / RAIS setup)
```

### 2.2 mDNS / DNS-SD Kayıt Yapısı (RFC 6762, RFC 6763)

| Alan | Kayıt Tipi | Açıklama |
|------|-----------|----------|
| Servis adı | PTR | `_raop._tcp.local` → instance adayları |
| Instance | SRV | Host + port (akış kontrol kanalı) |
| Instance | TXT | Model, protokol versiyonu, MAC adresi |
| Host | A / AAAA | IPv4 / IPv6 adresi |

- **PTR sorgusu:** İstemci `_services._dns-sd._udp.local` ile başlayan servis kategorilerini sorgular.
- **Yanıt hızı:** mDNS yanıt gecikmesi keşif süresini doğrudan artırır; ses akışı başlangıcını geciktirir.
- **TTL değeri:** mDNS kayıtlarında TTL=7500 saniye (RFC 6762 §10.2); kayıt değişikliği 80% TTL'de duyurulur.

### 2.3 SSDP M-SEARCH Örneği (DLNA Keşfi)

```
M-SEARCH * HTTP/1.1
HOST: 239.255.255.250:1900
MAN: "ssdp:discover"
MX: 3
ST: urn:schemas-upnp-org:device:MediaRenderer:1
```

| Alan | Açıklama |
|------|----------|
| `MX` | Yanıt için beklenecek maksimum saniye (1–5 arası) |
| `ST` | Aranan servis tipi (device veya service tipi) |
| Yanıt | Renderer `HTTP/1.1 200 OK` + `LOCATION` (description URL) döner |

**Keşif performansı notu:** `MX=3` varsayılan olarak 3 saniyeye kadar yanıt gecikmesine izin verir; keşif süresini kısaltmak için daha küçük `MX` kullanılabilir, ancak yanıt kaçırma riski artar.

### 2.4 Bağlantı Kurma (Connection Setup) Adımları

| Adım | İşlem | Hata Modu |
|------|-------|-----------|
| 1 | Servis keşfi (SSDP/mDNS) | Cihaz bulunamıyor (timeout) |
| 2 | Cihaz seçimi (render point) | Yanlış cihaza bağlanma |
| 3 | TCP oturumu açma (HTTP GET/POST veya RTSP) | Port reddedildi / firewall |
| 4 | Kodlayıcı (encoder) pazarlığı | Format uyuşmazlığı |
| 5 | Akış başlatma | İlk paket kaybı (underrun) |
| 6 | Kalp atışı (heartbeat / keep-alive) | Bağlantı zaman aşımı |

### 2.5 Bağlantı Süresi Bütçesi (Connection Time Budget)

48kHz/512-sample tampon (ADR-017) bir tampon = 10.67 ms sürer. Keşif + bağlantı kurma süresi bu tamponu aşarsa, akış başlangıcında underrun oluşur.

| Aşama | Tipik Süre | Bütçe Etkisi |
|-------|-----------|--------------|
| mDNS/SSDP keşfi | 100–1000 ms | Başlangıç gecikmesi (underrun değil — henüz akış yok) |
| TCP handshake | 1–10 ms (LAN) | Tampon erimesi |
| Format pazarlığı | 10–50 ms | Tampon erimesi |
| İlk paket gelene kadar bekleme | Ağ bağımlı | Underrun riski |

**Tasarım kuralı:** İlk paket gelmeden önce player, en az 2 tampon (≈21.3 ms) doldurmayı beklemelidir.

### 2.6 Keşif Hata Modları (Discovery Failure Modes)

| Hata | Belirti | Kök Neden | Çözüm |
|------|---------|-----------|-------|
| Cihaz bulunamıyor | Timeout, boş liste | Multicast engelli (AP/client isolation) | AP ayarında multicast izni |
| Yanlış cihaz | Hedef dışı bağlantı | Ağda birden fazla renderer | Cihaz adı/ID filtresi |
| Yavaş keşif | 1s+ gecikme | Ağ yükü / yavaş mDNS yanıtıcı | Tarama aralığını ayarla |
| Kayıt bayat (stale) | Bağlantı reddi | mDNS TTL doldu, kayıt silinmedi | Yeniden tarama (TTL §2.2) |
| Çakışan instance adı | Yanlış cihaz seçimi | Aynı adla iki renderer | instance adını MAC/seri no ile ayırt et |

---

## 3. Güvenlik (WPA2 / WPA3)

### 3.1 WiFi Bağlantı Katmanı Güvenliği

| Sürüm | Şifreleme | Kimlik Doğrulama | Çekirdek |
|-------|-----------|-----------------|----------|
| WPA2-Personal | CCMP (AES-128) | PSK (Pre-Shared Key) | 4-way handshake |
| WPA2-Enterprise | CCMP (AES-128) | 802.1X (RADIUS) | EAP |
| WPA3-Personal | CCMP + GCMP-256 | SAE (Simultaneous Auth of Equals) | Dragonfly |
| WPA3-Enterprise | GCMP-256 | 802.1X + CNSA | 192-bit mode |

### 3.2 Uygulama Katmanı Güvenliği (Protokol Özgü)

| Protokol | Güvenlik | Not |
|----------|----------|-----|
| DLNA/UPnP | Temelde **güvensiz** (LAN varsayımı) | Genişletilmiş ağda VPN/AES tunnel önerilir |
| AirPlay 2 | AirFair + SRP (Secure Remote Password) eşleştirme | Kod tabanlı eşleştirme (pairing) |
| Roon | TLS + Roon hesabı kimlik doğrulaması | RAIS üzerinde şifreli oturum |

### 3.3 Eşleştirme (Pairing) Akışları

| Protokol | Eşleştirme Tipi | Adımlar |
|----------|-----------------|---------|
| AirPlay 2 | SRP + PIN | İstemci PIN girer → SRP anahtar değişimi → oturum anahtarı |
| Roon | Hesap kimliği | Roon Core hesabı → endpoint yetkilendirme |
| DLNA | Yok (güvensiz) | LAN erişimi = yetki (tasarım riski) |

**Eşleştirme güvenlik kuralları:**

1. Pairing kodu asla log'a yazılmaz (§7.1'deki güvenlik kuralı).
2. SRP oturum anahtarı yenileme aralığı cihaza bağlıdır.
3. Başarısız eşleştirme sonrası bekleme (backoff) uygulanır (brute-force koruması).
4. DLNA renderer'larda WAN'a açık port açılması **yasaktır** (güvensiz protokol).

### 3.4 Sürücü Katmanı Güvenlik Notları

- **MAC adresi rastgeleleştirme:** WiFi tarama sırasında istemci gerçek MAC adresini ifşa etmemelidir (privacy).
- **PMF (Protected Management Frames, 802.11w):** WPA3'te zorunlu; WPA2'de güçlükle önerilir — deauth saldırılarına karşı koruma.
- **Rogue AP koruması:** İstemci, bilinen SSID için yanlış AP'ye bağlanmamalıdır (Enterprise ortamda EAP-TLS sertifika doğrulaması şart).

### 3.5 Sertifika ve PSK Yönetimi

| Öğe | Saklama | Yenileme | Not |
|-----|---------|----------|-----|
| WPA2-PSK | AP + istemci yapılandırması | Manuel (güvenlik nedeniyle) | 8+ karakter, WPA3'e geçiş öner |
| EAP-TLS sertifikası | Sertifika deposu | Süre dolumu öncesi | zincir doğrulaması |
| AirFair oturum anahtarı | Bellek (çalışma zamanı) | Oturum sonunda silinir | Kalıcı depolama yok |
| Roon hesap token'ı | Yapılandırma dosyası | Hesap ayarlarından | Dosya izinleri kısıtlı |

> ⚠️ VERIFICATION REQUIRED: AirFair/DRM mekanizmasının iç yapısı Apple tarafından yayımlanmaz; buradaki tasarım yalnızca genel SRP eşleştirme modeline dayanır.

### 3.6 Tehdit Modeli (WiFi Ses Akışı)

| Tehdit | Vektör | Etki | Azaltım |
|--------|--------|------|---------|
| Rogue AP | Sahte SSID | Akış ele geçirme | PMF + Enterprise sertifika |
| Deauth saldırısı | Management frame | Kesinti (dos) | 802.11w (PMF) |
| Replay | Yakalanan paket | Akış bozma | CCMP/GCMP sayaç |
| Eavesdropping | Pasif dinleme | Gizlilik kaybı | Şifreleme + DLNA'da VLAN |
| MITM (DLNA) | ARP spoofing | Akış yönlendirme | Statik ARP + izole VLAN |

---

## 4. Güç Yönetimi (Power Management)

### 4.1 WiFi Güç Modları ve Ses Etkisi

| Mod | Açıklama | Ses Etkisi |
|-----|----------|------------|
| Active (CAM) | Sürekli dinleme | En düşük latency, en yüksek güç |
| Power Save (PS) | Beacon aralarında uyku | Jitter artışı, paket kaybı riski |
| WMM-PS (UAPSD) | Trafik tipine göre tetiklenen uyanma | Dengeli |

**Tasarım kuralı:** Ses akışı sırasında istemci **CAM (Continuous Access Mode)**'da kalmalıdır; PS modu jitter yaratır ve 512-sample tampon taşma riskini artırır.

**Güç modu geçiş tablosu:**

| Geçiş | Tetikleyici | Ses Etkisi | Geçiş Süresi |
|-------|-------------|------------|--------------|
| PS → CAM | Akış başlatma | Jitter düşer | 1 beacon (≈100 ms) |
| CAM → PS | Akış duraklatma | Jitter artar | Hemen |
| PS → UAPSD | Trafik profili değişimi | Orta | Cihaza bağlı |

### 4.2 Roaming ve Bant Geçişi (Band Steering)

| Mekanizma | Protokol | Açıklama |
|-----------|----------|----------|
| Hızlı roaming | 802.11r (FT) | AP'ler arası anahtar değişimi hızlandırılır |
| Komşu ölçüm | 802.11k | İstemci komşu AP listesi alır |
| Hedef yönlendirme | 802.11v | AP istemciyi başka AP'ye yönlendirir |
| Bant steering | AP tarafı | 2.4 GHz istemciyi 5 GHz'e yönlendirme |

**Roaming kesinti bütçesi:** 48kHz/512-sample tampon (10.67 ms) bir roaming olayını tolere etmez; roaming, tampondan bağımsız bir jitter tamponu (≥50 ms önerilir) ile emilmelidir.

**Roaming test senaryosu:**

| Adım | İşlem | Kabul |
|------|-------|-------|
| 1 | Akış başlat, 5 sn bekle | Kesintisiz |
| 2 | İstemciyi AP-2'ye taşı (sinyal < -70 dBm) | ≤1 dropout |
| 3 | Akış 10 sn daha devam et | Kesintisiz |
| 4 | AP-1'e geri taşı | ≤1 dropout |
| 5 | Toplam dropout | ≤2 (kabul eşiği) |

### 4.3 Bantlar ve Kanallar

| Bant | Kanal Sayısı (tipik) | Menzil | Tavsiye |
|------|----------------------|--------|---------|
| 2.4 GHz | 3 (överlapseiz: 1, 6, 11) | Yüksek | Kalabalık — kaçının |
| 5 GHz | 20–25 | Orta | **Tercih edilen** (daha az girişim) |
| 6 GHz (WiFi 6E/7) | Yüksek | Düşük (ISM) | Gelecek-proof, erişim sınırlı |

**DFS (Dynamic Frequency Selection) notu:** 5 GHz'in bir kısmı (52–144 kanalları) radar tespitine tabidir; radar algılanırsa AP kanal değiştirir → 1–10 sn kesinti. Ses akışı için DFS dışı kanallar (36–48, 149–165) tercih edilir.

### 4.4 Güç Testi Prosedürü

| Test | Yöntem | Kabul |
|------|--------|-------|
| Uzun süre akış | 1 saat kesintisiz | 0 dropout |
| Güç geçişi | PS↔CAM senaryosu (§4.1) | 0 dropout |
| Roaming | §4.2 testi | ≤2 dropout |
| Sıcaklık | Cihaz sıcaklığını izle | Thermal throttle yok |

---

## 5. Ağ Yönetimi (Network Management)

### 5.1 QoS (Quality of Service) İşaretleme

| Trafik | DSCP | WMM AC | Öncelik |
|--------|------|--------|---------|
| Ses akışı (ses verisi) | EF (46) | AC_VO (Voice) | En yüksek |
| Ses kontrol (RTSP/HTTP) | AF41 (34) | AC_VI (Video) | Yüksek |
| Keşif (mDNS/SSDP) | CS0 (0) | AC_BE (Best Effort) | Normal |
| Arka plan (dosya indirme) | CS1 (8) | AC_BK (Background) | Düşük |

**QoS uygulama sırası (wireless → wired):**

| Katman | Mekanizma | Cihaz |
|--------|-----------|-------|
| WiFi erişim | WMM (802.11e) | AP + istemci |
| Ethernet | 802.1p CoS | Switch |
| IP | DSCP | Router |
| Uygulama | Uygulama önceliklendirme | Player |

### 5.2 Multicast Yönetimi

| Protokol | Port | Ağ Katmanı Notu |
|----------|------|-----------------|
| SSDP | 1900/UDP | Multicast — router/AP yönlendirmesi gerekir |
| mDNS | 5353/UDP | 224.0.0.251 — link-local multicast |
| IGMP snooping | — | AP'de açık olmalı (gereksiz multicast yayını önler) |

**Multicast hata modları:**

| Hata | Belirti | Çözüm |
|------|---------|-------|
| mDNS yanıtı alınmıyor | Keşif boş | IGMP snooping + multicast izni |
| SSDP yayını ulaşıyor ama yanıt yok | Renderer yanıt vermiyor | Firewall: UDP 1900 izni |
| Gereksiz multicast yükü | Ağ tıkanıklığı | IGMP snooping zorunlu |
| IPv6 multicast eksik | AAAA kaydı yok | IPv6 multicast (ff02::fb) izni |

### 5.3 Jitter Tamponu ve Akış Kararlılığı

| Parametre | Değer (tasarım) | Gerekçe |
|-----------|-----------------|---------|
| Jitter tamponu | ≥50 ms | Roaming + güç geçişlerini emmek için |
| Tampon tampon sayısı | 2 (ADR-017: 512 sample × 48kHz) | Başlangıçta underrun önleme |
| Paket kaybı eşiği | <0.1% (LAN) | Ses kalitesi (dropout) |
| Yeniden gönderme (retransmit) | Kapat (UDP) veya üst katmana bırak (TCP) | Zamanlama bozucu yeniden gönderme sesi bozar |

**Jitter hesabı (kavramsal):**

```
Jitter = max(paket arası gecikme) − min(paket arası gecikme)
Kabul: jitter ≤ jitter tamponu (§5.3)
Not: jitter > tampon → dropout; jitter ≤ tampon → emilir, kesinti yok
```

### 5.4 Multi-Room Senkronu (Multi-Room Sync)

| Protokol | Senkron yöntemi | Tipik tolerans |
|----------|-----------------|----------------|
| AirPlay 2 | Zaman damgası + tampon eşitleme | ≤10 ms (hedef) |
| Roon | RAIS zaman damgası | ≤1 ms (hedef) |
| DLNA | Yok (her renderer bağımsız) | Senkron yok |

**Senkron kuralı:** İki oda arasındaki gecikme farkı 20 ms'yi aşarsa (kulağa yakın iki hoparlörde) duyulur eko farkı oluşur; multi-room için senkrona özel bir jitter tamponu ayrılanmalıdır.

### 5.5 Ağ Topoloji Kuralları (Ses Ağları)

| Kural | Açıklama |
|-------|----------|
| SSID ayrımı | Ses ağı misafir ağından ayrı SSID'de |
| VLAN | DLNA renderer'ları izole VLAN'da |
| DHCP rezervasyonu | Renderer IP'si sabit (DHCP static mapping) |
| AP yerleşimi | Renderer'lar 5 GHz, kısa menzil |

---

## 6. Ölçüm (Measurement)

### 6.1 Akış Gereksinimi Hesabı (Throughput Requirement)

| Format | Ham bitrate (stereo) | Sıkıştırılmış (FLAC ≈ %60) | Tavsiye edilen bant genişliği |
|--------|---------------------|----------------------------|------------------------------|
| 16-bit/44.1kHz | 1.41 Mbps | ≈0.85 Mbps | ≥10 Mbps (kafa payı) |
| 24-bit/96kHz | 4.61 Mbps | ≈2.76 Mbps | ≥20 Mbps |
| 24-bit/192kHz | 9.22 Mbps | ≈5.53 Mbps | ≥30 Mbps |
| 32-bit/384kHz (Roon) | 24.58 Mbps | ≈14.75 Mbps | ≥50 Mbps |

**Hesap notu:** Ham bitrate = kanal sayısı × bit derinliği × örnekleme hızı. FLAC sıkıştırma oranı içerik bağımlıdır (%50–70 aralığı); tablo orta değer kullanır.

### 6.2 Latency Bütçesi (Latency Budget)

| Aşama | Tipik Değer (LAN) | Not |
|-------|-------------------|-----|
| WiFi MAC gecikmesi | 2–10 ms | Power save modunda ↑ |
| TCP/UDP yığın gecikmesi | 1–5 ms | Ağ yüküne bağlı |
| Akış tamponu (jitter) | 50–200 ms | Tasarım seçimi |
| Decoder (ALAC/FLAC) | 5–20 ms | Donanım hızlandırma bağımlı |
| **Toplam (tipik)** | **58–235 ms** | AirPlay 2 varsayılan ≈2s (buffer nedeniyle) |

### 6.3 Ölçüm Protokolü

| Metod | Araç | Kabul Kriteri |
|-------|------|---------------|
| Akış kesintisi (dropout) testi | 1 saat sürekli akış | 0 dropout |
| Throughput ölçümü | LAN iperf benzeri araç | Gereksinimin ≥2 katı |
| Roaming testi | AP'ler arası hareket | ≤1 dropout olayı |
| Jitter ölçümü | Paket zaman damgası analizi | ≤10 ms (p95) |
| Bant darboğaz simülasyonu | Ağ sınırlayıcı (shaper) | Gereksinimin %50'sinde 0 dropout |

### 6.4 Ölçüm Adımları (Step-by-Step)

| Adım | İşlem | Çıktı |
|------|-------|-------|
| 1 | Ağ topolojisini kur (§5.5) | SSID/VLAN aktif |
| 2 | Renderer keşfet (§2.1) | Cihaz listesi |
| 3 | Akış başlat (§1.7 senaryosu) | Play |
| 4 | Trafik etiketle (§5.1) | DSCP/WMM |
| 5 | Jitter ölç (§5.3) | Jitter değeri |
| 6 | Dropout say (§6.3) | 0 dropout |
| 7 | Raporla (§7.1) | Ölçüm kaydı |

### 6.5 Kabul Kriterleri Matrisi (Acceptance Criteria)

| Kriter | Eşik | Test |
|--------|------|------|
| Throughput | ≥2× gereksinim | §6.1 + iperf |
| Latency (toplam) | ≤250 ms | §6.2 |
| Jitter (p95) | ≤10 ms | §6.3 |
| Paket kaybı | <0.1% | Uzun akış |
| Dropout (1 saat) | 0 | §4.4 |
| Roaming dropout | ≤2 | §4.2 |
| Multi-room senkron | ≤10 ms fark | §5.4 |

---

## 7. Kaynakça (Sources)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| mDNS (Multicast DNS) protokolü — RFC 6762 | https://www.rfc-editor.org/rfc/rfc6762 | 2026-10-13 | HTTP 200 ✓ |
| DNS-SD (DNS Service Discovery) — RFC 6763 | https://www.rfc-editor.org/rfc/rfc6763 | 2026-10-13 | HTTP 200 ✓ |
| IEEE 802.11 WiFi standardı | https://standards.ieee.org/ieee/802.11/7372/ | 2026-10-13 | HTTP 200 ✓ |

**Vault içi referanslar:** [[electronic/drivers/index]] · [[electronic/drivers/network-drivers]] · [[electronic/drivers/device-integration]] · [[architecture/10-network/index]] · [[electronic/dsp/index]]

### 7.1 ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-026-download-service-architecture]] | Network mimarisi |

### 7.2 Jitter ve Buffer Matematiği (Derinlemesine)

**Packet jitter hesabı (RFC 3550 tarzı ortalama sapma):**

```
Jitter (J) = J + (|D(i−1,i)| − J) / 16
Burada D(i−1,i) = (R_i − S_i) − (R_(i−1) − S_(i−1))
R = alım zaman damgası, S = gönderim zaman damgası
Kabul: J ≤ jitter tamponu / 4 (kural payı)
```

**Buffer tampon hesabı (akış tarafı):**

```
Tampon boyutu (bayt) = bitrate (bps)/8 × tampon süresi (sn)
Örnek (24-bit/192kHz FLAC ≈5.53 Mbps, §6.1):
  5.53e6 / 8 × 0.05 (50 ms jitter tamponu) ≈ 34.6 KB
Kabul: tampon süresi ≥ 2 × jitter (emilim)
```

### 7.3 Multi-Room Gecikme Farkı (Eko) Eşiği

| Odalar arası fark | Kulağın algısı | Kabul |
|-------------------|----------------|-------|
| ≤10 ms | Senkron (hedef) | AirPlay 2 |
| 10–20 ms | Hafif eko farkı (yakın odalar) | Sınır |
| >20 ms | Belirgin eko (aynı mekân) | RED |

**Kural:** Aynı mekândaki iki hoparlör arasında >20 ms fark duyulur eko yaratır; multi-room senkronu (§5.4) bu eşiği aşmamalıdır.

### 7.4 Ölçüm Kaydı Şeması

| Alan | Tip | Örnek |
|------|-----|-------|
| timestamp | ISO 8601 | 2026-10-13T12:00:00Z |
| protokol | enum | Roon / AirPlay 2 / DLNA |
| throughput_mbps | float | 25.0 |
| jitter_ms_p95 | float | 8.2 |
| packet_loss_pct | float | 0.02 |
| dropout_count | int | 0 |
| verdict | enum | PASS / RED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
