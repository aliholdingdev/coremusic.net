---
type: electronic
category: bluetooth-drivers
title: "CoreMusic — Bluetooth Audio Drivers"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Bluetooth Audio Drivers

**See also:** [[electronic/drivers/index]] · [[electronic/firmware/index]] · [[electronic/drivers/device-integration]]

> **Repo gerçeği:** Bu depoda Bluetooth çalıştıran gerçek bir C/C++ sürücü kodu **YOK** (`.cpp`/`.c`/`.h` taraması 0 sonuç). Bu dosya, `.ai/electronic/**` vault mimarisinin Bluetooth ses katmanı tasarımını ve profil gereksinimlerini tanımlar — çalışan kod kanıtı değil, **tasarım şartnamesidir**.

---

## 1. HCI & L2CAP Katmanı (Host Controller Interface)

Bluetooth Audio Drivers, CoreMusic platformunun Bluetooth kulaklık, hoparlör ve cihazlarıyla iletişimini tanımlar. Ses verisi, Bluetooth yığınının alt katmanlarından (HCI → L2CAP → SCO/AVDTP) geçerek cihaza ulaşır.

### 1.1 Bluetooth Yığını (Protocol Stack)

```
┌─────────────────────────────────────────────┐
│  Uygulama (Player / CoreMusic Engine)       │
├─────────────────────────────────────────────┤
│  Profiller: A2DP · HFP · LE Audio (LC3)     │
├─────────────────────────────────────────────┤
│  AVDTP (ses transport) / RFCOMM (HFP)       │
├─────────────────────────────────────────────┤
│  L2CAP (mantıksal kanal)                     │
├─────────────────────────────────────────────┤
│  HCI (Host ↔ Controller arayüzü)             │
├─────────────────────────────────────────────┤
│  Link Manager / Baseband (BR/EDR veya LE)   │
├─────────────────────────────────────────────┤
│  Radio (2.4 GHz ISM)                         │
└─────────────────────────────────────────────┘
```

### 1.2 HCI Komut/Seti (Ses İlişkili)

| HCI Event/Komut | Görev | Ses Bağlantısı |
|-----------------|-------|----------------|
| `HCI_Set_Connection_Encryption` | Şifreleme aç | Eşleştirme sonrası (§2) |
| `HCI_Setup_Synchronous_Connection` | SCO/ESCO kur | HFP görüşmesi (§3.3) |
| `HCI_Enhanced_Setup_Synchronous_Connection` | eSCO kur (mSBC) | 16 kHz genişbant ses |
| `HCI_LE_Create_Connection` | LE bağlantı kur | LE Audio (§4) |

### 1.3 L2CAP Mantıksal Kanalları

| CID Tipi | Kullanım | Not |
|----------|----------|-----|
| Signaling | Bağlantı kurulumu | Bağlantı parametreleri |
| Connectionless | İstemci → sunucu | Sınırlı kullanım |
| AVDTP | Ses transport (A2DP) | §3.1 |
| AVCTP | Kumanda (AVRCP) | Play/pause, volume |
| RFCOMM | Seri emülasyonu (HFP AT) | §3.3 |

### 1.4 MTU ve Paket Boyutları

| Parametre | BR/EDR | LE | Not |
|-----------|--------|-----|-----|
| L2CAP MTU (varsayılan) | 672 bayt | 27 bayt (LE data) | Kanal başına |
| HCI ACL paketi | 1021 bayt (tipik) | — | BR/EDR |
| SCO paketi | 60 bayt (HV3) | — | HFP sesi |
| LE ISO paketi | — | LC3 süresi × kanal | LE Audio (§4.2) |

---

## 2. Eşleştirme (Pairing) ve Güvenlik

### 2.1 Eşleştirme Yöntemleri

| Yöntem | Kullanım | Güç |
|--------|----------|-----|
| Just Works | Ekranlı olmayan cihazlar | Düşük (MITM koruması yok) |
| Numeric Comparison | Ekran + tuş takımı | Orta (6 haneli PIN doğrulama) |
| Passkey Entry | Tuş takımı olan cihaz | Orta-Yüksek |
| Out of Band (OOB) | NFC/Nearby ile eşleştirme | Yüksek |

### 2.2 Bağlantı Kurulumu ve Güvenlik Seviyeleri

| Adım | İşlem | Sonuç |
|------|-------|-------|
| 1 | Inquiry / LE scan | Cihaz keşfi |
| 2 | Bağlantı kurulumu | ACL/LE bağlantısı |
| 3 | Eşleştirme (§2.1) | Bağlantı anahtarı (Link Key / LTK) |
| 4 | Şifreleme aç | `HCI_Set_Connection_Encryption` |
| 5 | Profil bağlantısı | A2DP/HFP/LE Audio |

### 2.3 Güvenlik Katmanları

| Katman | Mekanizma | Açıklama |
|--------|-----------|----------|
| Link | E0/AES-CCM | BR/EDR link şifrelemesi / LE AES-CCM |
| Uygulama | SDP/auth | Profil erişim kontrolü |
| Eşleştirme | SMP (LE) | LTK üretimi, MITM koruması |

**Güvenlik kuralları:**

1. Yeniden bağlanma, bilinen cihaz için `Just Works` yerine güvenli yeniden bağlanma (bağlantı anahtarı) kullanır.
2. Bilinmeyen cihaz eşleştirmesi, kullanıcı onayı olmadan yapılmaz.
3. HFP AT komutları yalnızca şifreli bağlantıda işlenir.
4. Eşleştirme bilgileri (Link Key / LTK) kalıcı depolama alanında saklanır; uygulama günlüğüne yazılmaz.

---

## 3. A2DP ve SCO Profilleri

### 3.1 A2DP Profile (Advanced Audio Distribution)

| Özellik | Değer |
|---------|-------|
| Direction | Tek yönlü (audio streaming) |
| Codec | SBC, AAC, aptX, LDAC |
| Kanal | Stereo |
| Max Bitrate | 990kbps (LDAC) |

**A2DP rolleri:**

| Rol | Görev | Not |
|-----|-------|-----|
| Source (SNK) | Ses verir | CoreMusic (player) |
| Sink (SNK) | Ses alır | Kulaklık/hoparlör |

**AVDTP (Audio/Video Distribution Transport Protocol):**

| Aşama | İşlem | Not |
|-------|-------|-------|
| 1 | Signaling (L2CAP CID) | Codec müzakeresi |
| 2 | Transport (L2CAP CID) | Ses verisi akışı |
| 3 | Stream setup | `SET_CONFIGURATION` → `OPEN` → `START` |
| 4 | Streaming | Codec'lenmiş ses paketleri |

### 3.2 Bluetooth Ses Codec'leri

| Codec | Bit Derinliği | Örnekleme | Bitrate | Latency |
|-------|--------------|-----------|---------|---------|
| SBC | 16-bit | 48kHz | 345kbps | 100-200ms |
| AAC | 16-bit | 44.1kHz | 256kbps | 80-200ms |
| aptX | 16-bit | 48kHz | 352kbps | 60-80ms |
| aptX HD | 24-bit | 48kHz | 576kbps | 60-80ms |
| aptX Adaptive | 24-bit | 96kHz | 420kbps | 50-80ms |
| LDAC | 24-bit | 96kHz | 990kbps | 100-200ms |
| LC3 | 16-24-bit | 48kHz | 345kbps | 20-30ms |

**Codec seçimi matrisi:**

| Senaryo | Tercih edilen codec | Gerekçe |
|---------|---------------------|---------|
| Yüksek kaliteli dinleme | LDAC / aptX Adaptive | Yüksek bitrate |
| Düşük latency (video) | aptX Adaptive / LC3 | ≤80 ms |
| Uyumluluk (eski cihaz) | SBC / AAC | Geniş destek |
| LE Audio cihaz | LC3 | §4.2 |

### 3.3 HFP Profile (Hands-Free)

| Özellik | Değer |
|---------|-------|
| Direction | Çift yönlü |
| Kullanım | Telefon görüşmesi |
| Quality | 8kHz mono (CVSD), 16kHz mono (mSBC) |

**SCO/eSCO (Synchronous Connection-Oriented) bağlantısı:**

| Bağlantı Tipi | Bitrate | Kullanım |
|---------------|---------|----------|
| HV3 (SCO) | 64 kbps | CVSD (8 kHz) |
| eSCO (2-EV3) | 64 kbps | mSBC (16 kHz) |

**HFP AT komutları (sesle ilgili):**

| AT Komutu | İşlev |
|-----------|-------|
| `AT+VGM` / `AT+VGS` | Mikrofon/hoparlör seviyesi |
| `AT+CIEV` | Arama durumu bildirimi |
| `AT+BCS` | Codec seçimi onayı (CVSD/mSBC) |

### 3.4 AVRCP Profile (Kumanda)

| Özellik | Değer |
|---------|-------|
| Version | 1.6+ |
| Kullanım | Play/pause, volume knob, track change |
| Kanal | AVCTP (L2CAP) |

---

## 4. LE Audio & Bluetooth 5.3

### 4.1 Bluetooth 5.3 Desteği

| Özellik | Değer |
|---------|-------|
| Range | 240m (maksimum) |
| Latency | 2-8ms (bağlantı) |
| Throughput | 2Mbps |
| Multi-device | 2 cihaz aynı anda |
| LE Audio | Destekli |

### 4.2 LE Audio (LC3 Codec)

| Özellik | Değer | Not |
|---------|-------|-----|
| Codec | LC3 (Low Complexity Communication) | Zorunlu LE Audio codec'i |
| Örnekleme | 8–48 kHz | Esnek |
| Bitrate | 16–320 kbps | Bant genişliğine göre |
| Latency | 20–30 ms (tipik) | A2DP'den düşük |
| Kanal | Stereo / çoklu | `ISO` kanalı |

**LE Audio katmanları:**

| Katman | Görev | Not |
|--------|-------|-----|
| LC3 | Codec | §4.2 tablosu |
| ISO (Isochronous) | Zaman sinkronlu transport | LE Audio'a özgü |
| CIS/BIS | Bağlantılı/Broadcast | Tekil / çok-oda |

### 4.3 Broadcast (Auracast) Kavramı

| Özellik | Açıklama | Durum |
|---------|----------|-------|
| BIS (Broadcast Isochronous) | Tek kaynak → çoklu sink | LE Audio |
| Senkron | Zaman damgası | Multi-room (§5.4 wifi) |
| Auracast | Genel yayın (public) | Sınırlı cihaz desteği |

> ⚠️ VERIFICATION REQUIRED: Auracast ticari cihaz desteği 2026 itibarıyla sınırlıdır; vault'ta doğrulanmış referans yok.

### 4.4 LE Audio vs A2DP Karşılaştırma

| Kriter | A2DP (BR/EDR) | LE Audio (LC3) |
|--------|---------------|----------------|
| Codec | SBC/AAC/aptX/LDAC | LC3 |
| Latency | 50–200 ms | 20–30 ms |
| Güç | Orta | Düşük (LE tabanlı) |
| Çok-oda | Yok (tek sink) | BIS broadcast |
| Stereo | Tekil sink | Çoklu sink |

---

## 5. Güç Yönetimi (Power Management)

### 5.1 Bluetooth Güç Modları

| Mod | Açıklama | Ses Etkisi |
|-----|----------|------------|
| Active | Sürekli dinleme | En düşük latency, en yüksek güç |
| Sniff | Periyodik dinleme | Orta (görsel: ses orta) |
| Hold/Park | kısa/uzun bekleme | Akış kesintisi riski |

**Tasarım kuralı:** A2DP/LE Audio akışı sırasında istemci **Active/Sniff** modunda kalmalıdır; Hold/Park modu ses akışını kesintiye uğratır.

### 5.2 Sürüklenme (Siyah Kutu) ve G/Ü Oranı

| Parametre | Tipik Değer | Not |
|-----------|-------------|-----|
| Sürüklenme (drift) | ±20 ppm | Sağlayıcıya bağlı |
| G/Ü oranı (duty cycle) | Aktif ≈ %10–30 | LE Audio'da düşük |
| Sniff aralığı | 100–500 ms | Latency etkisi |

### 5.3 LE Audio Güç Optimizasyonu

| Mekanizma | Açıklama | Kullanım |
|-----------|----------|----------|
| LC3 düşük bitrate | Bandwidth bilinçli codec | Düşük güç senaryosu |
| CIS aralıklı | Bağlantılı izokronous | Periyodik uyanma |
| BIS broadcast | Alıcı sürekli dinler | Düşük güç yayın |

---

## 6. Bluetooth Driver Akışı

```
Cihaz Eşleştir ──▶ Bağlantı Kur ──▶ {Codec Seç}
                                        │
                           LDAC/Yüksek ▼ aptX HD ▼ SBC/Standart
                                   │         │         │
                                   └─────────┴─────────┘
                                             │
                                     Stream Başlat
```

### 6.1 Bağlantı Kurulumu Adımları

| Adım | İşlem | Hata Modu |
|------|-------|-----------|
| 1 | Cihaz keşfi (inquiry/LE scan) | Cihaz bulunamıyor |
| 2 | Eşleştirme (§2.1) | Eşleştirme başarısız |
| 3 | Bağlantı kurulumu (ACL/LE) | Bağlantı reddi |
| 4 | Codec müzakeresi (AVDTP/ISO) | Codec uyuşmazlığı |
| 5 | Stream başlatma | İlk paket kaybı (dropout) |

### 6.2 Hata Modları (Failure Modes)

| Hata | Belirti | Kök Neden | Çözüm |
|------|---------|-----------|-------|
| Cihaz bulunamıyor | Scan boş | Menzil dışı / yanlış mod | LE/BR-EDR tarama |
| Eşleştirme hatası | PIN reddi | Eşleştirme yöntemi uyuşmazlığı | Just Works ↔ Numeric |
| Ses kesilmesi (dropout) | Periyodik kesinti | RF girişim / güç modu | 2.4 GHz temizleme, Active mod |
| Codec düşüşü (SBC) | Kalite kaybı | Uyumsuz cihaz | Codec pazarlığı (§3.2) |
| Gecikme artışı (latency) | A/V senkron kayması | Buffer taşması / sniff | Buffer genişletme, sniff kapatma |

---

## 7. Ölçüm (Measurement)

### 7.1 Throughput Gereksinimi

| Codec | Bitrate | Gereken RF (başlık) | Not |
|-------|---------|---------------------|-----|
| SBC | 345 kbps | ≥1 Mbps | BR/EDR |
| AAC | 256 kbps | ≥1 Mbps | BR/EDR |
| aptX Adaptive | 420 kbps | ≥1.5 Mbps | BR/EDR |
| LDAC | 990 kbps | ≥2 Mbps | BR/EDR |
| LC3 (LE Audio) | 320 kbps | ≥500 kbps | LE |

### 7.2 Latency Bütçesi

| Aşama | Değer (tipik) | Not |
|-------|---------------|-----|
| BT MAC gecikmesi | 2–10 ms | BR/EDR veya LE |
| Codec encode/decode | 5–20 ms | Codec bağımlı |
| Buffer (jitter) | 20–100 ms | Tasarım seçimi |
| **Toplam (A2DP)** | **50–200 ms** | LDAC/aptX Adaptive |
| **Toplam (LE Audio)** | **20–30 ms** | LC3 (§4.2) |

### 7.3 Ölçüm Protokolü

| Metod | Araç | Kabul |
|-------|------|-------|
| Dropout testi | 1 saat sürekli akış | 0 dropout |
| Latency ölçümü | Ses geri besleme (loopback) | ≤250 ms (A2DP), ≤50 ms (LE) |
| Codec doğrulama | AVDTP/ISO negotiated codec | Tercih edilen codec aktif |
| RF girişim testi | 2.4 GHz kalabalık ortam | ≤2 dropout / saat |

### 7.4 Kabul Kriterleri

| Kriter | Eşik | Test |
|--------|------|------|
| Throughput | ≥1.5× codec bitrate | §7.1 |
| Latency (toplam) | ≤250 ms (A2DP), ≤50 ms (LE) | §7.2 |
| Dropout (1 saat) | 0 | §7.3 |
| Codec | LDAC/aptX Adaptive/LC3 (destekleniyorsa) | §3.2 |
| Güç (görsel) | ≤2× SBC (LDAC) | §7.1 |

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-020-api-public-security]] | API güvenlik |

---

## 9. Kaynakça (Sources)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| IEEE 802.15 Working Group (Bluetooth PHY/MAC) | https://standards.ieee.org/ieee/802.15/7419/ | 2026-10-13 | HTTP 200 ✓ |

**Vault içi referanslar:** [[electronic/drivers/index]] · [[electronic/drivers/wifi-drivers]] · [[electronic/drivers/device-integration]] · [[electronic/firmware/index]] · [[electronic/drivers/audio-drivers]]

---

## 10. SCO/eSCO ve LE ISO Bant Genişliği Matematiği

### 10.1 SCO/eSCO Paket Hesabı

```
SCO (HV3): tek 30-byte slot, 64 kbps → CVSD 8 kHz mono
eSCO (2-EV3): 2 slot, 64 kbps → mSBC 16 kHz mono
Bant genişliği = slot başına byte × slot süresi × tekrar
eSCO (2-EV3): 60 byte / 1.25 ms slot → ≈48 kbps yararlı (mSBC)
```

### 10.2 LE ISO (CIS/BIS) Hesabı

```
LE ISO paket boyutu = LC3 frame süresi × kanal × (bitrate/8)
Örnek (LC3, 10 ms frame, 48 kHz, 96 kbps, stereo):
  96e3/8 × 0.010 × 2 kanal ≈ 240 byte / ISO interval
ISO interval: 7.5 ms veya 10 ms (LC3 §4.2)
```

### 10.3 SCO vs LE ISO Karşılaştırması

| Kriter | SCO/eSCO (BR/EDR) | LE ISO (LC3) |
|--------|-------------------|--------------|
| Bant genişliği | 64 kbps (tekil) | 96–345 kbps (esnek) |
| Kanal | Mono | Stereo / çoklu |
| Latency | ~50 ms (tipik) | 20–30 ms (§4.2) |
| Güç | Orta | Düşük (LE) |
| Kullanım | HFP görüşme (§3.3) | LE Audio (§4) |

### 10.4 Sürüklenme (Drift) Emme Matematiği

```
Sürüklenme emme = jitter tamponu / clock drift farkı
Örnek: iki cihaz ±20 ppm drift (§5.2) → fark ≤40 ppm
  40 ppm = 40 µs/sn; jitter tamponu 50 ms
  Emme süresi = 50 ms / (40 µs/sn) ≈ 1250 sn ≈ 20 dk
Kabul: emme süresi > oturum süresi → dropout olmaz
```

### 10.5 Ölçüm Kaydı Şeması (Bluetooth)

| Alan | Tip | Örnek |
|------|-----|-------|
| timestamp | ISO 8601 | 2026-10-13T12:00:00Z |
| codec | enum | LDAC / LC3 / SBC |
| bitrate_kbps | float | 990 |
| latency_ms | float | 120 |
| rssi_dbm | int | −55 |
| dropout_count | int | 0 |
| verdict | enum | PASS / RED |

---

## 11. Profil Durum Makinesi (A2DP State Machine)

### 11.1 A2DP Bağlantı Durumları

```
IDLE ──scan──▶ FOUND ──connect──▶ CONNECTED ──start──▶ STREAMING
  ▲                │                    │                    │
  │◀────timeout────┘◀────drop───────────┘◀────stop───────────┘
  │                                     (§3.1 AVDTP)
  └────────────── error / disconnect ───────────────────────┘
```

**Durum geçiş tablosu (A2DP):**

| Durum | Çıkış Koşulu | Tipik Süre |
|-------|--------------|------------|
| IDLE → FOUND | Keşif (inquiry/LE scan) | 1.28 sn / 10–100 ms |
| FOUND → CONNECTED | ACL + AVDTP signaling (§3.1) | 100–500 ms |
| CONNECTED → STREAMING | `START` (§3.1 stream setup) | 50–200 ms |
| STREAMING → CONNECTED | Pause / brief drop | anlık |
| CONNECTED → IDLE | Disconnect / timeout | 5 sn (tipik) |

### 11.2 Yeniden Bağlanma (Reconnection) Zamanlaması

```
Bilinen cihaz (Link Key / LTK §2.3 var):
  Page (BR/EDR) veya LE connect → hızlı yeniden bağlanma
  Tipik: 1–3 sn (Page timeout ≤5 sn)
Bilinmeyen cihaz:
  Tam eşleştirme (§2.1) → uzun: 5–15 sn
```

**Yeniden bağlanma kuralları:**

1. Bilinen cihaz için `Just Works` yerine güvenli yeniden bağlanma (bağlantı anahtarı) kullanılır (§2.3).
2. Page timeout (BR/EDR) tipik 5.12 sn; LE connection timeout ~30 sn (cihaza bağlı).
3. Codec yeniden pazarlığı (§3.2) yeniden bağlanmada tekrarlanır.

### 11.3 RSSI ↔ Menzil ve Güç İlişkisi

| RSSI (tipik) | Menzil | Beklenen |
|--------------|--------|----------|
| > −50 dBm | Yakın (<1 m) | Mükemmel |
| −50…−70 dBm | 1–3 m | İyi |
| −70…−80 dBm | 3–8 m | Sınır (dropout riski) |
| < −80 dBm | >8 m | Bağlantı kesilir |

**RSSI kuralı:** Ses akışında RSSI < −75 dBm eşiğine düşerse dropout riski artar; §5.4 güç testinde menzil sınırı bu eşikle tanımlanır.

### 11.4 Güç Modu ↔ Latency İlişkisi

| Güç modu | Latency etkisi | Kullanım |
|----------|----------------|----------|
| Active (§5.1) | En düşük | Ses akışı |
| Sniff | ↑ (sniff interval) | Bekleme |
| Hold/Park | Akış kesilir | Kullanılmaz (§5.1) |

**Sniff interval hesabı:** Sniff aralığı (§5.2, 100–500 ms) → uyanma gecikmesi ≤ sniff interval; ses akışında sniff devre dışı (Active).

### 11.5 Codec Pazarlık Sırası (AVDTP Capability Exchange)

```
Source (CoreMusic) → Sink (kulaklık):  capability listesi (§3.2)
Sink → Source:  desteklenen codec'ler + tercih sırası
Source:  ortak codec'ten yüksekten düşüğe seç (§3.2 matrisi)
  LDAC > aptX Adaptive > aptX HD > aptX > AAC > SBC (§3.2)
Ortak codec yoksa → SBC (zorunlu fallback, §3.2)
```

**Pazarlık kuralı:** SBC her A2DP cihazında zorunludur (§3.1); yüksek kalite codec (LDAC/aptX) desteklenmiyorsa sessizce SBC'ye düşülür, kullanıcıya bildirim (§3.2 senaryo tablosu).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
