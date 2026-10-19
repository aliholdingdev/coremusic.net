---
type: electronic
category: device-integration
title: "CoreMusic — Device Integration & Hot-Plug"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Device Integration

**See also:** [[electronic/drivers/index]] · [[architecture/07-security/device-auth]] · [[electronic/drivers/usb-drivers]] · [[electronic/drivers/wifi-drivers]] · [[electronic/drivers/bluetooth-drivers]]

> **Repo gerçeği:** Bu depoda cihaz keşfi/hot-plug yönetimi yapan gerçek bir C/C++ kodu **YOK** (`.cpp`/`.c`/`.h` taraması 0 sonuç). Bu dosya, `.ai/electronic/**` vault mimarisinin cihaz entegrasyon tasarımını tanımlar — çalışan kod kanıtı değil, **tasarım şartnamesidir**.

---

## 1. Amaç & Cihaz Keşfi

Device Integration, CoreMusic platformunun cihaz algılama, eşleştirme ve yönetimi mekanizmalarını tanımlar. Bu katman, sürücü katmanlarının (usb/wifi/bluetooth/network/embedded) üzerinde çalışır ve cihaz yaşam döngüsünü (keşif → bağlanma → kullanma → çıkarma) yönetir.

### 1.1 Katman Konumu

```
┌─────────────────────────────────────────────┐
│  Uygulama (Player / CoreMusic Engine)       │
├─────────────────────────────────────────────┤
│  Device Integration (BU DOSYA)              │
│  Keşif · Auth · Hot-Plug · Yönetimi        │
├─────────────────────────────────────────────┤
│  Sürücü Katmanları                          │
│  usb · wifi · bluetooth · network · embedded│
├─────────────────────────────────────────────┤
│  OS Platform (udev/Windows Device Stage)    │
├─────────────────────────────────────────────┤
│  Donanım (USB/WiFi/BT/Ethernet/SoC)         │
└─────────────────────────────────────────────┘
```

### 1.2 Cihaz Türleri

| Cihaz | Bağlantı | Kullanım |
|-------|----------|----------|
| USB DAC | USB 2.0/3.0 | Stüdyo ses |
| Bluetooth Kulaklık | BT 5.0+ | Mobil |
| Bluetooth Hoparlör | BT 5.0+ | Ev |
| WiFi Receiver | DLNA/AirPlay | Ev medya |
| Roon Endpoint | Ethernet | Profesyonel |
| Araç Sistemi | USB/BT | Araç içi |

### 1.3 Cihaz Keşfi Yöntemleri (Discovery Methods)

| Bağlantı | Keşif Yöntemi | Tetikleyici | Gecikme |
|----------|---------------|-------------|---------|
| USB | Enumerasyon (device add) | Hot-plug IRQ | ms |
| Bluetooth | Inquiry / LE scan | Periyodik / tetikli | 1–10 sn |
| WiFi (DLNA) | SSDP M-SEARCH | Periyodik | 0.1–1 sn |
| WiFi (AirPlay/Roon) | mDNS PTR | Periyodik | 0.1–1 sn |
| Ethernet (Roon) | mDNS + RAIS | Periyodik | 0.1–1 sn |

**Keşif mekanizması notları:**

1. USB keşfi olay-güdümlüdür (hot-plug IRQ) — en düşük gecikme.
2. Bluetooth/WiFi keşfi tarama-tabanlıdır — tarama aralığı latency/power dengesini belirler.
3. Keşif sonuçları, cihaz kaydına (device registry) yazılır (§1.4).

### 1.4 Cihaz Kaydı (Device Registry)

| Alan | Açıklama | Örnek |
|------|----------|-------|
| Device ID | Benzersiz cihaz kimliği | USB VID:PID / BT MAC / mDNS instance |
| Cihaz türü | §1.2 eşlemesi | USB DAC |
| Bağlantı türü | USB/WiFi/BT/Ethernet | USB 2.0 |
| Durum | Bağlı/çıkarma/hata | Connected |
| Yetki durumu | Whitelist/Auth (§2) | Authorized |

**Device ID kuralları:**

1. USB: VID:PID + seri no (varsa) — seri no yoksa VID:PID.
2. Bluetooth: MAC adresi (kalıcı) — LE: public/random ayrımı.
3. WiFi (mDNS): instance adı + TXT kaydı (MAC).
4. Cihaz registry, yeniden bağlanmada (§3.2) önceki tercihleri (çözünürlük, mixer) yükler.

---

## 2. Cihaz Tanımlama & Probing

### 2.1 Probing (Cihaz Doğrulama) Adımları

| Adım | İşlem | Başarı | Hata |
|------|-------|--------|------|
| 1 | Keşif (§1.3) | Cihaz listesine ekle | Bulunamadı |
| 2 | Kimlik okuma (USB descriptor / BT name) | VID:PID/MAC | Okuma hatası |
| 3 | Sürücü eşleştirme | Sürücü yükle | Uyumsuz cihaz |
| 4 | Capability sorgu (desteklenen format) | Format listesi | Sınırlı capability |
| 5 | Yetkilendirme (§2.2) | Authorized | Bloklandı |
| 6 | Cihaz kaydı (§1.4) | Registry güncelle | Registry hatası |

### 2.2 Yetkilendirme (Authorization) Akışı

```
Cihaz Algıla ──▶ Cihaz Tanımla ──▶ {Whitelist'te mi?}
                                      │
                             Evet ▼    ▼ Hayır
                    Kimlik Doğrula   Blokle
                           │
                    Bağlan ──▶ Stream Başlat
```

**Yetkilendirme kuralları:**

1. Whitelist'te olmayan cihaz varsayılan olarak bloklanır (fail-safe, §2.3).
2. USB DAC, ilk bağlanmada kullanıcı onayı ile whitelist'e eklenir.
3. BT cihaz, eşleştirme (§3 bluetooth) tamamlanmadan yetkilendirilmez.
4. WiFi renderer (DLNA/AirPlay/Roon), ağ doğrulaması (§4 wifi) ile yetkilendirilir.

### 2.3 Güvenlik Notları (Device Auth)

| Kural | Açıklama |
|-------|----------|
| Fail-safe | Tanımsız cihaz → blok (fail-open değil) |
| Whitelist | Cihaz kimliği kalıcı liste |
| Auth | §2.2 akışı; USB/BT/WiFi protokollerine göre |
| Registry | Cihaz ID + yetki durumu; kalıcı |
| Eşleştirme | BT pairing (§3 bluetooth), WiFi ağ kimliği |

**Device Auth akış güvenlik notları:**

1. Whitelist, cihaz kimliğini (VID:PID/MAC) saklar — kullanıcı verisi değil.
2. Kimlik doğrulama (§2.2), bağlantı sırasında bir kez yapılır.
3. Blok olayı log'a yazılır (güvenlik denetimi).

---

## 3. Hot-Plug Mekanizması

### 3.1 Hot-Plug Olayları

| Olay | Davranış |
|------|----------|
| Cihaz takıldı | Otomatik algılama, driver yükle |
| Cihaz çıkarıldı | Graceful stop, kaynak serbest bırak |
| Cihaz yeniden takıldı | Otomatik reconnect |
| Cihaz değişti | Yeni driver ile devam |

### 3.2 Bağlantı Yaşam Döngüsü (Connection Lifecycle)

| Durum | Tetikleyici | Sonraki | Timeout |
|-------|-------------|---------|---------|
| Discovered | Keşif (§1.3) | Probing | 5 sn |
| Probing | Keşif sonucu | Authorized | 5 sn |
| Authorized | Yetkilendirme (§2.2) | Connected | 5 sn |
| Connected | Bağlantı kuruldu | Streaming | — |
| Streaming | Akış başlatıldı | Connected (pause) | — |
| Removed | Cihaz çıkarıldı | Discovered (yeniden) | — |
| Error | Hata | Discovered (retry) | 3 retry |

### 3.3 Kaynak Serbest Bırakma (Resource Cleanup)

| Durum | Serbest Bırakılan | Not |
|-------|-------------------|-----|
| Cihaz çıkarıldı | DMA buffer, IRQ, driver | Graceful stop |
| Akış duraklatma | Buffer (korunur) | Driver bağlı kalır |
| Hata | Tüm kaynaklar | Retry (§3.2) |
| Uygulama kapanış | Tüm cihazlar | Sıra ile |

**Kaynak kuralları:**

1. Hot-plug çıkarma (removal), akışı durdurur (graceful stop) — click/pop önleme.
2. DMA/IRQ (§2 embedded), cihaz çıkarıldığında serbest bırakılır.
3. Yeniden bağlanma (§3.2), önceki tercihleri (çözünürlük/mixer) Device Registry'den yükler (§1.4).

### 3.4 Hot-Plug Hata Modları

| Hata | Belirti | Kök Neden | Çözüm |
|------|---------|-----------|-------|
| Cihaz algılanmıyor | Keşfet boş | IRQ/pin hatası | §2.1 probing |
| Yanlış sürücü | Cihaz çalışmıyor | VID:PID uyuşmazlığı | §2.1 sürücü eşleştirme |
| Yetkilendirme başarısız | Bloklandı | Whitelist dışı | §2.2 onay |
| Graceful stop başarısız | Click/pop | DMA/IRQ serbest bırakılmadı | §3.3 cleanup |
| Reconnect başarısız | Cihaz dönmedi | Timeout (§3.2) | 3 retry → escalation |

---

## 4. Uyumluluk Matrisi (Compatibility Matrix)

### 4.1 Bağlantı × Cihaz Uyumluluğu

| Cihaz \ Bağlantı | USB | WiFi | BT | Ethernet |
|------------------|-----|------|-----|----------|
| USB DAC | ✓ (§2 usb) | — | — | — |
| BT Kulaklık | — | — | ✓ (§3 bt) | — |
| BT Hoparlör | — | — | ✓ (§3 bt) | — |
| WiFi Receiver | — | ✓ (§4 wifi) | — | ✓ (§5 net) |
| Roon Endpoint | — | ✓ (§4 wifi) | — | ✓ (§5 net) |
| Araç Sistemi | ✓ (§2 usb) | — | ✓ (§3 bt) | — |

### 4.2 Platform Uyumluluğu

| Platform | Bağlantı | Durum | Not |
|----------|----------|-------|-----|
| Windows | USB/WiFi/BT/Ethernet | PLANNED | ASIO Exclusive Lock (§4.3) |
| Linux (RPi5) | USB/WiFi/BT/Ethernet | PLANNED | §6 embedded |
| macOS | USB/WiFi/BT/Ethernet | PLANNED | §1.2 |

### 4.3 Exclusive Lock Kuralı (ASIO)

| Kural | Değer |
|-------|-------|
| ASIO Exclusive | Yalnızca BİR uygulama |
| Sahiplik | Player / CoreMusic Engine |
| Çakışma | Başka uygulama ASIO isterse → reddet |
| Serbest bırakma | Uygulama kapanışında |

**ASIO Exclusive Lock notu:** Windows'ta ASIO Exclusive Lock, ses cihazını tek uygulamaya bağlar; cihaz (USB DAC §2) başka bir uygulama tarafından kullanılamaz. Bu, düşük latency için gereklidir (ADR-017).

### 4.4 Çoklu Cihaz (Multi-Device) Yönetimi

| Senaryo | Davranış | Not |
|---------|----------|-----|
| İki USB DAC | Birincil aktif | Exclusive Lock (§4.3) |
| BT + WiFi | Aynı anda | Bağımsız akış |
| Multi-room WiFi | 32 oda | §5.4 wifi |

---

## 5. Ölçüm (Measurement)

### 5.1 Keşif/Hot-Plug Ölçümü

| Metod | Araç | Kabul |
|-------|------|-------|
| Keşif gecikmesi | Zaman damgası (§1.3) | USB ≤10 ms, WiFi/BT ≤10 sn |
| Hot-plug çıkarma | Cihaz çıkar → ölç | 0 click/pop |
| Hot-plug ekleme | Cihaz ekle → ölç | ≤5 sn (§3.2) |
| Reconnect | Cihaz yeniden ekle | ≤5 sn, tercihler yüklü |

### 5.2 Kaynak Ölçümü

| Metod | Araç | Kabul |
|-------|------|-------|
| Bellek sızıntısı (leak) | kmemleak / task manager | 0 leak (hot-plug sonrası) |
| DMA/IRQ (embedded) | §6 embedded | 0 underrun |
| Handle/descriptor (USB) | `handle` sayacı | Cihaz çıkarınca serbest |
| CPU kullanımı | `top`/`htop` | ≤%25 (48kHz/32-bit/stereo) |

### 5.3 Güvenlik Ölçümü

| Metod | Araç | Kabul |
|-------|------|-------|
| Whitelist reddi | Tanımsız cihaz tak | Bloklandı (§2.2) |
| Eşleştirme reddi | BT PIN hatalı | Blok (3 retry) |
| Auth reddi | Yetkisiz cihaz | Bloklandı |
| Registry tutarlılığı | Cihaz ID ↔ yetki | Tutarlı |

### 5.4 Ölçüm Adımları

| Adım | İşlem | Çıktı |
|------|-------|-------|
| 1 | Cihaz keşfet (§1.3) | Device registry (§1.4) |
| 2 | Probing (§2.1) | VID:PID/MAC OK |
| 3 | Yetkilendirme (§2.2) | Authorized |
| 4 | Bağlantı (§3.2) | Connected |
| 5 | Akış başlat | Streaming |
| 6 | Hot-plug test (§3.1) | 0 click/pop |
| 7 | Kaynak ölç (§5.2) | 0 leak, ≤%25 CPU |
| 8 | Güvenlik ölç (§5.3) | Whitelist/Auth reddi OK |
| 9 | Raporla | Ölçüm kaydı |

### 5.5 Kabul Kriterleri

| Kriter | Eşik | Test |
|--------|------|------|
| Keşif (USB) | ≤10 ms | §5.1 |
| Hot-plug ekleme | ≤5 sn | §5.1 |
| Click/pop (çıkarma) | 0 | §5.1 |
| Bellek leak | 0 (hot-plug sonrası) | §5.2 |
| CPU kullanımı | ≤%25 | §5.2 |
| Whitelist reddi | Blok (fail-safe) | §5.3 |
| Reconnect | ≤5 sn, tercihler yüklü | §5.1 |

---

## 6. Hot-Plug Akışı (Detaylı)

```
[Hot-Plug IRQ / Keşif olay]
        │
        ▼
[Keşif (§1.3)] ──▶ [Device Registry güncelle (§1.4)]
        │
        ▼
[Probing (§2.1)] ──▶ [VID:PID/MAC oku]
        │
        ▼
[Sürücü eşleştirme (§2.1)] ──▶ [Driver yükle]
        │
        ▼
[Yetkilendirme (§2.2)] ──▶ {Whitelist'te mi?}
        │                       │
        │              Evet ▼    ▼ Hayır
        │         [Kimlik Doğrula]  [Blok + log]
        │                │
        ▼                ▼
[Connection (§3.2)] ──▶ [Connected]
        │
        ▼
[Stream Başlat] ──▶ [Streaming]
        │
        ▼
[Cikarma (§3.1)] ──▶ [Graceful Stop (§3.3)] ──▶ [Kaynak serbest]
```

**Akış notları:**

1. Hot-plug olayı, keşif (§1.3) ile başlar; Device Registry güncellenir.
2. Yetkilendirme (§2.2), fail-safe (§2.3) — tanımsız cihaz blok.
3. Graceful stop (§3.3), click/pop önler; DMA/IRQ serbest bırakılır.

---

## 7. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |
| [[ADR-008-bypass-auth-middleware]] | Auth bypass |

**ADR-038 H001 hatırlatma:** PCM5122 **ABSOLUTELY FORBIDDEN** — yalnızca PCM3168A. USB DAC/WiFi Receiver seçimi ADR-038'e tabidir (§1.2).

---

## 8. Kaynakça (Sources)

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Linux kernel kmemleak (memory leak detector) | https://www.kernel.org/doc/html/latest/dev-tools/kmemleak.html | 2026-10-13 | HTTP 200 ✓ |
| Linux kernel driver-model index | https://www.kernel.org/doc/html/latest/driver-api/driver-model/index.html | 2026-10-13 | HTTP 200 ✓ |
| Linux kernel driver-api infrastructure | https://www.kernel.org/doc/html/latest/driver-api/infrastructure.html | 2026-10-13 | HTTP 200 ✓ |

**Vault içi referanslar:** [[electronic/drivers/index]] · [[electronic/drivers/usb-drivers]] · [[electronic/drivers/wifi-drivers]] · [[electronic/drivers/bluetooth-drivers]] · [[electronic/drivers/network-drivers]] · [[electronic/drivers/embedded-drivers]] · [[electronic/drivers/audio-drivers]] · [[architecture/07-security/device-auth]]

---

## 9. Enumerasyon Zamanlaması ve Yaşam Döngüsü Derinlemesi

### 9.1 Bağlantı Türüne Göre Keşif Gecikmesi

```
USB hot-plug:     IRQ → enumeration → sürücü → authorized
  Tipik: 50–500 ms (descriptor okuma + driver load)     (§2.1)
Bluetooth:        inquiry/LE scan periyodu → keşif
  Tipik: 1.28 sn (inquiry) / 10–100 ms (LE scan interval) (§1.3)
WiFi (mDNS/SSDP): tarama periyodu + yanıt
  Tipik: 0.1–1 sn (§1.3), MX=3 (§2.3 wifi)
Ethernet (Roon):  mDNS + RAIS handshake
  Tipik: 0.1–1 sn
```

### 9.2 Connection Lifecycle State Machine

```
Discovered ──probe OK──▶ Probed ──whitelist──▶ Authorized ──conn──▶ Connected
     ▲                       │                      │                    │
     │◀─────timeout(5sn)─────┘◀─────blok────────────┘◀─────removed───────┘
     │                                              (graceful stop §3.3)
     └───────────────── error (3 retry §3.2) ◀──────────────────────────┘
```

**Durum geçiş tablosu:**

| Durum | Çıkış Koşulu | Timeout |
|-------|--------------|---------|
| Discovered → Probed | VID:PID/MAC okundu | 5 sn |
| Probed → Authorized | Whitelist onayı (§2.2) | 5 sn |
| Authorized → Connected | TCP/ACL/USB oturum kuruldu | 5 sn |
| Connected → Streaming | Akış başlatıldı | — |
| Streaming → Connected | Pause | — |
| Connected → Discovered | Cihaz çıkarıldı (§3.1) | — |
| * → Discovered | Error (3 retry sonrası escalation) | — |

### 9.3 Registry Kalıcılık ve Yeniden Bağlanma

| Alan | Kaynak | Kalıcılık |
|------|--------|-----------|
| Device ID | §1.4 | Kalıcı (whitelist) |
| Yetki durumu | §2.2 | Kalıcı |
| Ses tercihi (çözünürlük/mixer) | Son oturum | Kalıcı |
| Bağlantı durumu | §3.2 | Geçici |

### 9.4 Graceful Stop Sırası (Click/Pop Önleme)

```
1. DSP akışını durdur (yeni örnek yok)
2. Son tamponu flush (kalan örnekleri gönder)
3. DAC mute (I2C register — §1.4 embedded)
4. DMA/IRQ serbest bırak (§3.3)
5. Sürücü detach
```

**Kural:** Mute, DMA serbest bırakmadan ÖNCE yapılır; ters sırada click/pop oluşur (§3.4).

### 9.5 Ölçüm Kaydı Şeması (Device Integration)

| Alan | Tip | Örnek |
|------|-----|-------|
| timestamp | ISO 8601 | 2026-10-13T12:00:00Z |
| device_id | string | VID:PID / BT MAC |
| connection | enum | USB / BT / WiFi / Ethernet |
| discover_ms | float | 120 (USB) |
| hotplug_pop | bool | false |
| reconnect_ms | float | 900 |
| leak_detected | bool | false (kmemleak §5.2) |
| whitelist_verdict | enum | AUTHORIZED / BLOCKED |
| verdict | enum | PASS / RED |

---

## 10. Whitelist Matrisi ve Yetkilendirme Derinlemesi

### 10.1 Bağlantı Türüne Göre Yetkilendirme Yöntemi

| Bağlantı | Yetki Anahtarı | Doğrulama Yöntemi | Fail-safe |
|----------|----------------|-------------------|-----------|
| USB DAC | VID:PID + seri no | Descriptor okuma (§2.1) | Blok (§2.3) |
| Bluetooth | MAC + Link Key/LTK | Eşleştirme (§2.1 bt) | Blok |
| WiFi (DLNA/AirPlay) | mDNS TXT (MAC) | Ağ doğrulama | Blok |
| WiFi (Roon) | Roon hesabı | RAIS handshake | Blok |
| Ethernet | MAC (statik IP §4.3 net) | Ağ + hesap | Blok |

### 10.2 Yetkilendirme Durumları ve Geçişleri

| Durum | Anlam | Sonraki |
|-------|-------|---------|
| UNKNOWN | Keşfedildi, henüz doğrulanmadı | PROBING |
| PROBING | Descriptor/MAC okunuyor (§2.1) | PENDING / BLOCKED |
| PENDING | Whitelist dışı — kullanıcı onayı bekliyor | AUTHORIZED / BLOCKED |
| AUTHORIZED | Whitelist'te / onaylandı | CONNECTED |
| BLOCKED | Reddedildi (fail-safe §2.3) | Discovered (yeniden dene) |
| AUTHORIZED_EXPIRED | Yetki süresi doldu (opsiyonel) | PENDING |

### 10.3 Yetki Karar Ağacı (Decision Tree)

```
Cihaz keşfedildi (§1.3)
    ↓
Whitelist'te mi? (Device ID — §1.4)
    ├── Evet → descriptor/MAC doğrula → AUTHORIZED → Connected (§3.2)
    └── Hayır → fail-safe: BLOCK + log (§2.3)
                  ↓
              Kullanıcıya sor (opsiyonel onay)
                  ├── Onay → AUTHORIZED (kalıcı whitelist'e ekle)
                  └── Red → BLOCKED (korumalı kalır)
```

**Karar kuralları:**

1. Whitelist'te olmayan cihaz varsayılan blok (fail-open değil — §2.3).
2. Onaylanan cihaz kalıcı whitelist'e eklenir (Device ID §1.4).
3. Her blok olayı log'a yazılır (güvenlik denetimi §5.3).

### 10.4 Çoklu Cihaz Çakışma Çözümü (Conflict Resolution)

| Çakışma | Belirti | Çözüm |
|---------|---------|-------|
| İki birincil USB DAC | Exclusive Lock (§4.3) | Whitelist sırası — birincil |
| Aynı MAC iki kayıt | Registry çakışması | Son kayıt kazanır (registry §9.3) |
| Cihaz değişti (aynı port) | Yeni VID:PID | Probing (§2.1) — yeni cihaz |
| Yetki süresi | AUTHORIZED_EXPIRED | Yeniden onay (§10.2) |

### 10.5 Cihaz Kaydı Şeması (Registry JSON — kavramsal)

| Alan | Tip | Örnek |
|------|-----|-------|
| device_id | string | "VID_1234:PID_5678:SERNR01" |
| connection | enum | "USB" |
| authorized | bool | true |
| last_seen | ISO 8601 | "2026-10-13T12:00:00Z" |
| preferred_format | string | "24-bit/96kHz" |
| whitelist_added | ISO 8601 | "2026-10-13T12:00:00Z" |

> ⚠️ VERIFICATION REQUIRED: Registry'nin gerçek dosya formatı (JSON/SQLite) vault'ta tanımlı değil; bu şema yalnızca alan setini tanımlar.

### 10.6 Yetkilendirme Performans Bütçesi

| Aşama | Hedef (USB) | Hedef (BT) | Hedef (WiFi) |
|-------|-------------|------------|--------------|
| Keşif (§1.3) | ≤10 ms | 10–100 ms / 1.28 sn | 0.1–1 sn |
| Descriptor/MAC okuma (§2.1) | ≤5 ms | ≤50 ms | ≤100 ms |
| Whitelist kontrolü (§10.3) | ≤1 ms | ≤1 ms | ≤1 ms |
| Yetki toplam | ≤15 ms (keşif hariç) | ≤150 ms | ≤1.2 sn |

**Kural:** Yetkilendirme (whitelist kontrolü §10.3) µs–ms seviyesindedir; toplam gecikme keşif katmanından (§1.3) gelir. Hot-plug (§3.2) ≤5 sn bütçesi, keşif + probing + yetkiyi kapsar (§9.1).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
