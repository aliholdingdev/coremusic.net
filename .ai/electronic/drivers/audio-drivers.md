---
type: electronic
category: audio-drivers
title: "CoreMusic — Audio Drivers (ASIO, WASAPI, ALSA)"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Audio Drivers

**See also:** [[electronic/drivers/index]] · [[ADR-017-dsp-hardware-mode]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Sürücü Katmanı (kernel↔HAL)

### 1.1 Dikey Katman

Ses sürücüsü dört katman üzerinden çalışır; her katmanın sorumluluğu ayrıdır ve bir katman diğerinin işini devralmaz:

```
Uygulama (Neva Engine, PLANNED)
    ↓  kullanıcı uzayı API çağrısı
HAL (Hardware Abstraction Layer)
    ↓  cihaz seçimi, format müzakeresi, buffer devri
OS Ses Çatısı (WASAPI / ALSA / CoreAudio)
    ↓  PCM tampon transferi
Kernel Sürücü (WDM-KS / ALSA-PCI / AppleUSBAudio)
    ↓  DMA kurulumu, IRQ yönetimi
Donanım (PCM3168A DAC, XMOS XU316 USB)
```

### 1.2 Katman Sorumluluk Tablosu

| Katman | Sorumluluk | Süreç/Konum | Bu dosyadaki bölüm |
|--------|-----------|-------------|--------------------|
| Uygulama | DSP zinciri, kullanıcı parametresi | Kullanıcı uzayı | §2.4 |
| HAL | Cihaz seçimi, format müzakeresi | Kullanıcı uzayı | §5.1-§5.4 |
| OS ses çatısı | Zamanlama, miksaj, akış durumu | Kullanıcı + çekirdek | §5.5 |
| Kernel sürücü | DMA, IRQ, register erişimi | Çekirdek uzayı | §1.3 |
| Donanım | PCM dönüşümü, saat (clock) | Donanım | [[electronic/hardware/index]] |

### 1.3 Kernel↔HAL Sözleşmesi

Kernel sürücü HAL'a üç şey teslim eder: (1) cihaz tanıtıcıları (device id, desteklenen format listesi), (2) aç/kapa/başlat/durdur komut yüzeyi, (3) veri aktarım tamponu. HAL bu üçü dışında çekirdek içine girmez. Çekirdek uzayı kodu zero-allocation ve lock-free kurallarına tabidir (bkz. [[electronic/dsp/index]] §9); kullanıcı uzayı HAL kodu bu kurallara tabi değildir ama ses gecikme bütçesini aşmamalıdır.

### 1.4 Windows HAL Zinciri

Windows'ta HAL rolü DirectSound/WASAPI katmanı üstünde durur; çekirdek tarafında WDM-KS (Kernel Streaming) sürücüleri yer alır. ASIO bir HAL'dir: uygulamayı doğrudan WDM-KS cihaz kanalına bağlar, işletim sistemi miksajıcısını atlar. Bu atlama Exclusive Lock kuralını doğurur — aynı WDM-KS kanalını ikinci bir ASIO uygulaması açamaz (kural 9).

### 1.5 Linux HAL Zinciri

Linux'ta ALSA-lib kullanıcı uzayında HAL'dir; çekirdek tarafında ALSA-PCI / ASoC (Audio SoC) sürücüleri bulunur. `snd_pcm_open` → `snd_pcm_hw_params` → `snd_pcm_prepare` → `snd_pcm_writei`/`readi` çağrışım dizisi HAL↔kernel sözleşmesini oluşturur. dmix eklentisi paylaşımlı erişimi kullanıcı uzayında çözer; exclusive `hw` erişimi doğrudan çekirdeğe gider.

### 1.6 macOS HAL Zinciri

macOS'ta CoreAudio hem HAL hem de ses çatısıdır; çekirdek tarafında AppleUSBAudio / IOAudioFamily sürücüleri bulunur. AudioDevice/Stream/IOProceduralCall zinciri HAL↔kernel sözleşmesini oluşturur; uygulama AudioUnit/AVAudioEngine üzerinden CoreAudio'ya bağlanır.

### 1.7 XMOS XU316 Üstü Sürücü

XMOS XU316 (ADR-038) USB Audio Class 2.0 sınıf-yönetici (class-compliant) cihaz olarak sunulur; özel sürücü kurulmaz, işletim sisteminin yerleşik UAC2 sürücüsü kullanılır. XU316 üzerinde DSP zinciri çalışır (ADR-017) — bu durum sürücü katmanını ikiye böler: USB taşıma sürücüsü (OS) + cihaz-içi DSP (firmware). bkz. [[usb-drivers]] §2, [[electronic/firmware/index]].

### 1.8 PCM3168A (DAC) Sürücü Yüzü

PCM3168A (ADR-038 H001; PCM5122 kesinlikle yasaktır) hardware modda I2S/TDM slave olarak sürülür; yani DAC için ayrı bir yazılım sürücüsü yoktur, sürücü I2S master'ını (SoC/XMOS) sürer. DAC pin-ataması, format (I2S/Left-justified) ve clocksing elektronik katmanındadır: [[electronic/hardware/index]] · [[embedded-drivers]] §1.4.

### 1.9 Virtual Audio Driver Katmanı

Sanal ses sürücüleri aynı katman sözleşmesini taklit eder: kernel tarafında bir ring buffer (sanal donanım), kullanıcı uzayında gerçek cihaza yazan bir köprü. Sanal cihaz hotplug olayını fiziksel cihazla aynı yolla üretir: [[device-integration]] §2.

### 1.10 Katman İhlali Durumları

| İhlal | Sonuç | Savunma |
|-------|-------|---------|
| HAL'ın çekirdek register'ı doğrudan yazması | Sürücü çökmesi | Yalnız sürücü register yazar |
| DSP'nin sürücü tamponunu doğrudan devretmesi | Yarış durumu (race) | Buffer devri kilitli protokolle |
| Uygulamanın Exclusive Lock kanalını ikinci kez açması | Ret (kural 9) | Exclusive Lock denetimi §5.1 |

---

## 2. Akış / DMA

### 2.1 Akış Yönü

| Akış | Yön | Tampon | Kullanım |
|------|-----|--------|----------|
| Playback (render) | Uygulama → donanım | Çıkış ring buffer | Dinleme, çıktı |
| Capture (record) | Donanım → uygulama | Giriş ring buffer | Kayıt, ölçüm mikrofonu |
| Duplex | Her iki yön | İki ring buffer eşzamanlı | Monitoring, ölçüm döngüsü |

### 2.2 DMA Mekanizması

Ses verisi PIO (programmed I/O) ile değil DMA ile taşınır: sürücü DMA motoruna tampon adresi + uzunluk verir, DMA bellek ile codec arasında veriyi CPU beklemeden kopyalar, tampon bittiğinde kesme (IRQ) ile sürücüye bildirim gelir. DMA sayesinde ses akışı CPU zaman çizelgesinden bağımsız ilerler; sürücü yalnızca tampon kenarlarında (boundary interrupt) uyanır.

### 2.3 Buffer Ayrımı (Double Buffering)

Tek tamponlu akışta DMA tamponu okurken uygulama aynı tamponu doldurursa yarış oluşur. İki tamponlu (double buffering) akışta DMA A'yı okurken uygulama B'yi doldurur; kenar kesmesinde roller değişir. Dört tamponlu (circular/quad) akış jitter toleransını daha da artırır, gecikme bedeliyle ödenir. CoreMusic 512 örnek (sample) buffer için double buffering taban kabulüdür.

### 2.4 DMA ↔ DSP Zinciri

DMA kenar kesmesi (boundary interrupt) sürücüye “tampon hazır” olayını verir; sürücü bu tamponu DSP zincirine devreder (bkz. [[electronic/dsp/index]] §2). DSP tamponu yerinde işler (in-place) — ek kopyalama yoktur. DMA tampon belleği önceden ayrılmıştır, çalışma anında allocate edilmez (zero-allocation kuralı).

### 2.5 Period Interrupt Zamanlaması

Her DMA kenar kesmesi bir “period” tamamlandığını bildirir; period süresi buffer boyutu ÷ örnekleme hızıdır. 48 kHz'te 512 örnek = 10.67 ms period. Zamanlama disiplini: sürücü her period kesmesinde hazır tampon sayısını kontrol eder, geriye kalmış (late) tampon sayısını ölçer ve xrun'a karşı savunma yapar (§6.1).

### 2.6 Xrun (Overrun/Underrun)

| Xrun Türü | Neden | Sonuç | Kurtarma |
|-----------|-------|-------|----------|
| Overrun (capture) | Uygulama geç okudu | Kayıt boşluğu | Tampon temizle, akış prepare |
| Underrun (playback) | Uygulama geç yazdı | Ses kesintisi (click) | Tampon doldur, akış prepare |

ALSA'da `-EPIPE` xrun kodudur; WASAPI Exclusive'te AUDCLNT_E_BUFFER_TOO_LARGE/AUDCLNT_E_DEVICE_INVALIDATED aile eşdeğeridir. Xrun kurtarma akışı §6.4'tedir.

### 2.7 Zaman Damgası (Timestamp)

DMA kenar kesmesi donanım zaman damgasıyla birlikte gelir; bu damga sürücünün gecikme ölçümüne ve çoklu cihaz senkronuna girdi olur. ALSA'da `snd_pcm_status_get_htstamp`, WASAPI'de `IAudioClock`/`IAudioClock2`, CoreAudio'da `AudioTimeStamp` bu damgaları sağlar.

### 2.8 Akış Durum Makinesi

```
CLOSED → OPEN → PREPARED → RUNNING → (xrun) → PREPARED → RUNNING → DRAINED → CLOSED
```

| Durum | Anlamı |
|-------|--------|
| OPEN | Cihaz tutuldu, format henüz müzakere edilmedi |
| PREPARED | Format sabitlendi, DMA hazır, henüz akıyor |
| RUNNING | DMA aktif, veri akıyor |
| DRAINED | Yazılacak veri kalmadı, kalan örnekler çalınıyor |

### 2.9 Duplex Senkronizasyon

Giriş ve çıkış ayrı DMA kanallarında çalışır; iki kanal arasında kayma (drift) oluşursa duplex uygulamada yavaş yavaş gecikme büyür. Çözüm: donanım saatini (MCLK) her iki kanal için ortaklama (bkz. [[embedded-drivers]] §1.4) veya yazılım kayma telafisi. CoreMusic hedefi: ortak saatli donanım zorunlu, yazılım telafisi yedek.

### 2.10 DMA Bellek Kuralları

| Kural | Değer |
|-------|-------|
| Tampon hizalama | DMA sınırına göre hizalı (típik 4 KB sayfa) |
| Bellek tipi | Kilitli (locked/pinned) — sayfa değiştirme (swap) yasak |
| Önceden ayırma | Akış başlamadan allocate, çalışma anında tahsis yok |
| 32-bit float | DMA veri biçimi 32-bit float'tur (ADR-017) |

---

## 3. Buffer & Period

### 3.1 Buffer Boyutu — Gecikme İlişkisi

```
Gecikme (ms) = Buffer Boyutu (örnek) ÷ Örnekleme Hızı (Hz) × 1000
```

| Buffer | 48 kHz gecikme (tek yön) | Değerlendirme |
|--------|--------------------------|---------------|
| 64 örnek | 1.33 ms | Monitör için, xrun riski yüksek |
| 128 örnek | 2.67 ms | Canlı monitör |
| 256 örnek | 5.33 ms | Düşük gecikme stüdyo |
| 512 örnek | 10.67 ms | **CoreMusic varsayılanı (sartname)** |
| 1024 örnek | 21.33 ms | Stabilite önceliği |

Duplex toplam gecikme giriş + çıkış buffer'ları toplamıdır (512+512 = 21.33 ms round-trip); tek yön ölçüm ile karıştırılmamalıdır.

### 3.2 Period vs Buffer

Buffer DMA'nın döndüreceği toplam halkadır; period buffer'ın bir kesme periyodunda işlenecek dilimidir. 512 örneklık buffer tek period ise uygulama her 10.67 ms'de bir uyanır; 2 period ise her 5.33 ms'de bir, daha küçük dilimlerle. Küçük period daha düşük jitter verir ama kesme sayısını (interrupt rate) artırır.

### 3.3 Buffer Hesabı (48 kHz / 512)

```
Örnekleme:        48000 Hz
Buffer:           512 örnek
Tek yön gecikme:  512 / 48000 = 10.67 ms
Duplex gecikme:   10.67 × 2 = 21.33 ms (yaklaşık, DMA kenar payı hariç)
Bit derinliği:    32-bit float (ADR-017)
Kanal (8.1):      9 kanal × 4 byte = 36 byte/örnek çerçevesi
Tampon boyutu:    512 × 36 = 18432 byte (tek yön, 8.1)
```

### 3.4 Bit Derinliği ve Kanal Sayısı (8.1)

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Örnekleme | 48 kHz (sartname), 96/192 kHz destek | sartname |
| Bit derinliği | 32-bit float (yol boyunca) | ADR-017 |
| Kanal | 8.1 surround = 9 kanal | ADR-038 |
| Çıkış DAC'ı | PCM3168A 8-kanal + subwoofer yolu | ADR-038 H001 |

### 3.5 Buffer Devri Protokolü

| Adım | İşlem |
|------|-------|
| 1 | Sürücü DMA tamponunu işaretler (işaretli/boş) |
| 2 | Period kesmesinde sürücü tamponu DSP'ye devreder |
| 3 | DSP yerinde işler, tamponu “işlendi” olarak işaretler |
| 4 | Sürücü işlenmiş tamponu donanıma verir |

Devir çift yönlü ve eşzamansızdır; her iki taraf da yalnızca kendi sahip olduğu tampona yazar.

### 3.6 Buffer Boyutu Seçim Kuralı

| Senaryo | Buffer | Gerekçe |
|---------|--------|---------|
| Canlı monitör | 64-128 | Gecikme önceliği |
| Stüdyo kaydı | 256-512 | Denge |
| CoreMusic varsayılan | 512 | sartname değeri |
| Kararlılık (yayın) | 1024+ | Kesinti yasağı |

### 3.7 Bellek Bütçesi (8.1 / 512 / 32-bit float)

```
Kanal × buffer × byte/örnek
9 × 512 × 4 = 18432 byte (tek yön)
Duplex: 2 × 18432 = 36864 byte ≈ 36 KB
DSP çalışma tamponları (§5.1, [[electronic/dsp/index]] §9): <%50 MB toplam bütçe
```

### 3.8 Zero-Allocation Kuralı ile Buffer

Tamponlar akış başlamadan allocate edilir (§2.10); akış sırasında buffer genişletme/serbest bırakma yasaktır. Format değişikliği = akış kapat + yeniden aç, buffer yeniden tahsisi yalnız bu geçişte yapılır.

### 3.9 Period Interrupt Sayısı ve CPU Bütçesi

48 kHz / 512 örnek / 1 period = 93.75 kesme/saniye. Her kesmede sürücü + DSP işi CPU bütçesinin bir parçasıdır; period küçüldükçe kesme sayısı artar, DSP başına düşen süre daralır. DSP CPU hedefi <%15 (8+1 kanal) — bkz. [[electronic/dsp/index]] §8.

### 3.10 Xrun'a Karşı Buffer Politikası

| Politika | Uygulama |
|----------|----------|
| Kenar toleransı | 2 period (double buffering) minimum |
| Erken uyarı | Hazır tampon sayısı eşiği aşılırsa CPU boost/uyarı |
| Kurtarma | Xrun → prepare → devam (§6.4) |

---

## 4. Cihaz Algılama

### 4.1 Algılama Tetikleyicileri

| Tetikleyici | OS | Kaynak |
|-------------|----|--------|
| Sistem açılışı | Tümü | Sabit sürücü taraması |
| USB takma olayı | Tümü | [[usb-drivers]] §4 |
| Bluetooth eşleştirme | Tümü | [[bluetooth-drivers]] §2 |
| HDMI/DisplayPort bağlama | Windows/macOS | [[device-integration]] §2 |
| Panel hot-swap (RPi HAT) | Linux | [[embedded-drivers]] §1 |

### 4.2 Algılama Akışı

```
Donanım olayı (tak/sistem açılışı)
    ↓
Kernel cihaz listesine ekle (enumerate)
    ↓
Sınıf doğrulama (ses cihazı mı?)
    ↓
Format/kanal yetenek okuma
    ↓
HAL cihaz listesine ekle
    ↓
Varsayılan cihaz seçimi (kural/tercih)
    ↓
Varsayılan cihaz kullanılamıyorsa fallback (§4.5)
```

### 4.3 Cihaz Tanıtıcıları

| Tanıtıcı | Örnek | Kullanım |
|----------|-------|----------|
| Vendor/Product id | USB VID:PID | Sürücü eşlemesi |
| Cihaz adı | “XMOS USB Audio 2.0” | Kullanıcıya gösterim |
| Kanal sayısı | 8/9 (8.1) | Uyumluluk doğrulama |
| Destek formatlar | 48k/96k/192k, 32-bit float | Format müzakeresi |
| Saat kaynağı | Donanım MCLK | Duplex senkron |

### 4.4 Cihaz Kimlik Doğrulaması

Yabancı bir ses cihazı sistemde varsayılan olabilir; CoreMusic varsayılan seçiminde whitelist mantığı kullanır (bkz. [[device-integration]] §4 uyumluluk matrisi). Bilinen cihaz (PCM3168A tabanlı XMOS, tanımlı USB audio) otomatik seçilir; tanımsız cihaz kullanıcı onayı ister.

### 4.5 Fallback Zinciri

```
ASIO cihazı kaybı / açılamadı
    → WASAPI Exclusive dene
        → başarısız → WASAPI Shared dene
            → başarısız → sistem uyar + sustur (mute)
```

Linux: `hw` açılamadı → `default` (PipeWire/Pulse) → uyarı. macOS: CoreAudio cihaz değişimi → otomatik yeniden bağlan. Fallback zinciri sessiz değildir: her geçiş kullanıcıya bildirilir.

### 4.6 Cihaz Kaybı Olayları

| Olay | Davranış |
|------|----------|
| USB çıkarıldı | Akışı durdur, cihaz listeden çıkar, fallback zinciri |
| Bluetooth bağlantısı koptu | Akışı durdur, yeniden bağlanmayı dene (3 retry) |
| Sürücü hatası (INVALIDATED) | Akışı durdur, sürücüyü yeniden yükle, fallback |
| Saat kaynağı kaybı | Akışı durdur — sesiz devam etmek yanlıştır |

### 4.7 Yeniden Bağlanma (Reconnect)

| Parametre | Değer |
|-----------|-------|
| Retry sayısı | 3 |
| Retry aralığı | 1 sn, 2 sn, 5 sn (artışlı) |
| Sonuç | Başarısız → kullanıcı uyar, fallback cihazla devam |

### 4.8 Varsayılan Cihaz Değişimi

İşletim sistemi varsayılan cihazı değiştirirse (kullanıcı ayarı, HDMI takma) CoreMusic olayı yakalar: yeni varsayılanı uygular (varsayılanı izleme modu açıksa) veya mevcut cihazda kalır (sabit seçim modu). Mod varsayılanı: sabit seçim — kullanıcı açıkça değiştirmediği sürece cihaz değişmez.

### 4.9 Cihaz Listesi Yönetimi

| Liste | İçerik | Güncelleme |
|-------|--------|-----------|
| Tüm cihazlar | Sistemdeki her ses cihazı | Hotplug olayında |
| Kullanılabilir | Format/kanal uyumlu cihazlar | Listeden çıkarılma olayında |
| Whitelist | Otomatik seçime izinli cihazlar | Kullanıcı yapılandırması |

### 4.10 Algılama Ölçümü

| Metrik | Hedef |
|--------|-------|
| Hotplug → hazır süre | <2 sn (USB ses) |
| Fallback geçişi | <500 ms (susturmadan) |
| Yanlış algılama (false positive) | 0 |
| Kaçırılan olay (false negative) | 0 |

---

## 5. Platform Sürücüleri

### 5.1 ASIO Driver

| Özellik | Değer |
|---------|-------|
| Sürüm | 2.3.4 (sartname; sürüm kanıtı: VERIFICATION REQUIRED — bkz. §8) |
| Platform | Windows XP-11, Server 2012 R2+ |
| Latency | <10 ms (512 sample) |
| Buffer | 64-1024 sample |
| Kanal | 2-8 (8+1 surround) |
| Bit | 32-bit float |
| Exclusive | Tek uygulama |
| Multi-client | Devre dışı (güvenlik) |

ASIO buffer hesabı (512 / 48 kHz): 512 ÷ 48000 = 10.67 ms (§3.3). ASIO Exclusive Lock: aynı cihazı ikinci uygulama açamaz — sessiz paylaşıma düşürme yok, ret vardır (kural 9).

### 5.2 WASAPI Driver

| Özellik | Değer |
|---------|-------|
| Mod | Shared + Exclusive |
| Platform | Windows 7-11 |
| Latency | <20 ms (Shared), <10 ms (Exclusive) |
| Format | 32-bit float, 48 kHz |
| Multi-client | Shared modda evet |
| Exclusive Lock | Tek uygulama |

### 5.3 ALSA Driver

| Özellik | Değer |
|---------|-------|
| Platform | Linux (Ubuntu, Debian, Fedora, Arch) |
| Latency | <15 ms |
| Format | 32-bit float, 48 kHz |
| Plugin | dmix (paylaşımlı), direct/hw (özel) |
| Xrun kodu | -EPIPE (§2.6) |

### 5.4 CoreAudio Driver

| Özellik | Değer |
|---------|-------|
| Platform | macOS (Monterey-Sonoma) |
| Latency | <10 ms |
| Format | 32-bit float, 48 kHz |
| Multi-client | Evet |

### 5.5 WASAPI vs ASIO Karşılaştırma

| Kriter | WASAPI | ASIO |
|--------|--------|------|
| Latency | <20 ms | <10 ms |
| Multi-client | Shared evet | Hayır |
| Exclusive | Var | Var |
| Desteği | Tüm Windows | Ses kartı |
| Kurulum | Otomatik | Manuel |

### 5.6 Virtual Audio Driver

| Kullanım | Açıklama |
|----------|----------|
| Audio Routing | Uygulamalar arası ses yönlendirme |
| Virtual Microphone | Yazılım mikrofonu (streaming, ölçüm döngüsü) |
| Virtual Speaker | Yazılım hoparlör alıcı |
| Screen Recording | Ekran kaydında ses yakalama |
| Broadcast | Yayın köprüsü |

### 5.7 Driver Seçim Akışı

```
Başla ──▶ {Platform Kontrol}
              │
     Windows ──▶ {Ses Kartı ASIO destekliyor mu?}
                    │
               Evet ▼ Hayır
          ASIO Kullan  WASAPI Exclusive Kullan

     Linux ────▶ ALSA Kullan
     macOS ────▶ CoreAudio Kullan
```

### 5.8 8.1 Kanal Yönlendirme (Sürücü Seviyesi)

| Kanal | Sürücü kanal indeksi | Not |
|-------|----------------------|-----|
| Front L | 0 | |
| Front R | 1 | |
| Center | 2 | |
| LFE (sub) | 3 | 8.1 |
| Surround L | 4 | |
| Surround R | 5 | |
| Rear L | 6 | |
| Rear R | 7 | |
| Subwoofer (8.1 yolu) | 8 | ADR-038 |

### 5.9 Platform Karşılaştırma Özeti

| Kriter | Windows ASIO | Windows WASAPI | Linux ALSA | macOS CoreAudio |
|--------|--------------|----------------|------------|-----------------|
| Gecikme | En düşük | Orta | Düşük | Düşük |
| Paylaşım | Yok | Shared'de var | dmix ile var | Var |
| Kurulum | Manuel | Otomatik | Otomatik | Otomatik |
| Sürüm bağımlılığı | Kart üreticisi | OS | Çekirdek | OS |

### 5.10 Sürücü Sürüm Yönetimi

Sürücü sürümü donanım uyumluluk matrisine yazılır ([[device-integration]] §4). Sürüm bump yalnız şu durumlarda yapılır: güvenlik açığı, yeni donanım desteği, xrun düzeltmesi. Bağımlılık bump / firmware OTA / refactor → DUR + handover (kural 13).

---

## 6. Hata Modları

### 6.1 Hata Modu Tablosu

| # | Hata Modu | Belirti | Kök Neden | Kurtarma |
|---|-----------|---------|-----------|----------|
| 1 | Xrun (underrun) | Tıkırtı, boşluk | CPU geç kaldı, buffer küçük | §6.4 |
| 2 | Cihaz kaybı | Akış aniden durur | USB/BT kopması | §4.6 |
| 3 | Exclusive Lock çakışması | İkinci uygulama açamaz | Kural 9 çalışır durumda | Kullanıcıya bildir |
| 4 | Format reddi | Açma başarısız | Cihaz formatu desteklemiyor | Müzakere + fallback |
| 5 | Saat kayması (drift) | Duplex'te artan gecikme | Ortak saat yok | §2.9 |
| 6 | DC offset | Koruma rölesi açılır | Donanım arızası | Röle + mute, [[electronic/amplifier/index]] |
| 7 | Sürücü çökmesi (OS) | Sistem sesi tamamen gider | Kernel sürücü arızası | Sürücü yeniden yükleme (OS) |

### 6.2 Xrun Teşhisi

| Belirti | Ölçüm | Yorum |
|---------|-------|-------|
| Periyodik tıkırtı | Kesme sayısının düzenli düşmesi | CPU yetmiyor / period çok küçük |
| Rastgele boşluk | Timer jitter ölçümü | OS zamanlayıcı sorunu |
| Sadece duplex'te | Giriş-çıkış kayma ölçümü | Saat kayması (§2.9) |

### 6.3 Hata Kodları (Platform)

| Platform | Kod | Anlam |
|----------|-----|-------|
| ALSA | -EPIPE | Xrun (§2.6) |
| ALSA | -ESTRPIPE | Akış askıya alındı (suspend) |
| WASAPI | AUDCLNT_E_BUFFER_TOO_LARGE | Buffer uygunsuz |
| WASAPI | AUDCLNT_E_DEVICE_INVALIDATED | Cihaz kaybı |
| ASIO | ASE_InvalidMode / ASE_DeviceLocked | Exclusive ihlali / mod hatası |

### 6.4 Xrun Kurtarma Akışı

```
Period kesmesinde hazır tampon yok
    ↓
Xrun tespiti (-EPIPE / ERESTARTSYS)
    ↓
snd_pcm_prepare / WASAPI reset
    ↓
Tamponu sıfırla (susturarak)
    ↓
Akışı RUNNING'a al
    ↓
Kaydı raporla (log, sayac)
```

Kurtarma sessiz değildir: her xrun sayacı artırılır ve kullanıcı istatistiğine yansır (§7.2).

### 6.5 Cihaz Kaybı Kurtarma

| Adım | İşlem |
|------|-------|
| 1 | DSP zincirine mute gönder (click yok) |
| 2 | Akışı durdur, kaynakları serbest bırak |
| 3 | Fallback zinciri (§4.5) |
| 4 | Yeniden bağlanma döngüsü (§4.7) — 3 retry |

### 6.6 DC Offset Koruma Etkileşimi

DC offset eşiği >0.5 V: donanım rölesi açılır (ADR-038 H001). Sürücü katmanı bu olayı mute ile karşılar ve röle durumunu kullanıcıya bildirir. Röle kapalıyken sürücüye çıkış açmak yasaktır — donanım durumu sürücü kararından üstündür.

### 6.7 Hata Yayılımının Sınırları

Bir sürücü hatası DSP zincirine yalnızca iki şey iletir: mute bayrağı ve hata sayacı. DSP sürücü hata kodunu çözmez, hatayı üst katmanlara taşımaz; karar (fallback, kullanıcıya sor) HAL'ındır.

### 6.8 Hata Modları — Sıklık Hedefleri

| Hata | Hedef sıklık |
|------|--------------|
| Xrun | <%0.01 period/saat |
| Cihaz kaybı | Donanım olayıdır — sürücü kaynaklı 0 |
| Format reddi | 0 (öntanımlı format listesi ile) |
| Röle tetiklenmesi | 0 (donanım sağlıklıysa) |

### 6.9 Telemetri ve Geri Bildirim

Her hata olayı şu alanlarla kaydedilir: olay türü, platform/hata kodu, örnekleme/buffer/kanal bağlamı, zaman damgası. Kayıt kullanıcı istatistik paneline ve destek kaydına düşer (§7.4).

### 6.10 Kurtarma Sonrası Doğrulama

Kurtarma sonrası doğrulama adımı zorunludur: akış RUNNING'a döndü mü, xrun sayacı düzeldi mi, ses mutlu (unmuted) ve tıkırtısız mı. Doğrulama başarısızsa bir üst kurtarma katmanına (OS sürücü yeniden yükleme, cihaz yeniden enumerate) geçilir.

---

## 7. Ölçüm

### 7.1 Gecikme Ölçüm Protokolü

| Adım | İşlem | Araç (platform) |
|------|-------|-----------------|
| 1 | Loopback fiziksel bağla (çıkış→giriş) | Fiziksel jumper |
| 2 | Impuls gönder | Click/impuls üretici |
| 3 | Girişte yakala, gecikmeyi ölç | Capture + cross-correlation |
| 4 | Örnek cinsinden raporla | ms değil **örnek** |

Protokol tekrarlanabilirlik gerektirir: 10 ölçümün medyanı raporlanır; tek ölçüm geçerli kanıt değildir.

### 7.2 Xrun Sayacı

| Metrik | Tanım | Hedef |
|--------|-------|-------|
| Xrun oranı | xrun / toplam period | <%0.01 |
| Kurtarma süresi | Xrun → RUNNING | <1 period |
| Tekrarlayan xrun | Aynı 10 sn içinde ≥2 | 0 (buffer ayarı değişmeli) |

### 7.3 CPU Bütçesi Ölçümü

| Katman | Hedef |
|--------|-------|
| Sürücü + DMA | Ölçüm: kesme başına iş süresi (µs) |
| DSP zinciri | <%15 (8+1 kanal, 48 kHz) — [[electronic/dsp/index]] §8 |
| Toplam ses yığını | <%25 CPU (ölçekli çekirdek) |

### 7.4 Ölçüm Işığındaki Hata Modları

| Hata | Ölçümle teyit |
|------|---------------|
| Xrun | §7.2 sayacı |
| Drift | Duplex kayma ölçümü (§2.9) |
| Format reddi | Açma sonucu kaydı |
| Röle | Donanım durum bildirimi (§6.6) |

### 7.5 Ölçüm Ortamı Koşulları

| Koşul | Değer |
|-------|-------|
| Örnekleme | 48 kHz |
| Buffer | 512 (varsayılan) + 64/128/256/1024 eğrisi |
| Kanal | 8.1 (8+1) |
| Bit | 32-bit float |
| Isı | Sistem stabil sıcaklıkta |

### 7.6 Ölçüm Tekrarı ve Kanıt Zinciri

Her ölçüm en az 3 ortamda (açılış sonrası soğuk, 1 saat sürekli çalışma, sıcak) tekrarlanır; sonuçlar medyan + maksimum ile raporlanır. Ölçüm kaydı dosya, tarih, platform, sürücü sürümü ile damgalanır.

### 7.7 Karşılaştırmalı Ölçüm (Platform)

| Platform | Ölçüm noktası | Beklenen sıra (gecikme) |
|----------|---------------|------------------------|
| Windows | ASIO vs WASAPI Ex vs Shared | ASIO < Ex < Shared |
| Linux | hw (ALSA) vs dmix vs PipeWire | hw < dmix ≤ PipeWire |
| macOS | CoreAudio (tek seçenek) | — |

### 7.8 Ölçüm Hataları

| Hata | Sonuç | Önlem |
|------|-------|-------|
| Yazılım döngü ölçümü | DMA/IRQ gecikmesi ölçülmez | Fiziksel loopback (§7.1) |
| Tek ölçüm | Örnekleme hatası | Medyan (§7.1) |
| ms raporu | Karşılaştırma zorluğu | Örnek cinsinden rapor |

### 7.9 Ölçüm Şartnamesi Karşılaştırması

| Şartname hedefi | Ölçüm bağlantısı |
|-----------------|------------------|
| Latency <10 ms (ASIO, 512) | §7.1 — 10.67 ms hesabı §3.3; ASIO iddiası §5.1 |
| CPU <%15 (DSP) | §7.3 |
| 8.1 / 48 kHz / 32-bit float | §5.8, §3.4 |

### 7.10 Ölçümün Sürücü Kararına Girdisi

Ölçüm sonucu sürücü seçim tablosunu (§5.7) besler: ölçülen xrun oranı >eşik ise buffer önerisi bir üst seviyeye kayar; ölçülen gecikme şartnameyi aşıyorsa donanım/OS araştırma kalemi açılır. Kararlar ölçüm kaydıyla birlikte vault'a yazılır.

---

## 8. Kaynakça

### 8.1 Web Araştırma

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| Linux ALSA ses tasarımı dokümantasyonu (tracepoint'ler dahil) §7 ölçüm/telemetri bölümünün resmi referansıdır | https://www.kernel.org/doc/html/latest/sound/designs/tracepoints.html | 2026-10-13 | HTTP 200 doğrulandı |
| Linux yüksek çözünürlüklü zamanlayıcı (hrtimers) dokümantasyonu §2.5 period interrupt zamanlamasının dayanağıdır | https://www.kernel.org/doc/html/latest/timers/highres.html | 2026-10-13 | HTTP 200 doğrulandı |
| Linux ftrace dokümantasyonu §7 profil/ölçüm altyapısı için resmi kaynaktır | https://www.kernel.org/doc/html/latest/trace/ftrace.html | 2026-10-13 | HTTP 200 doğrulandı |

### 8.2 Vault Referansları

| Konu | Dosya |
|------|-------|
| DSP pipeline ve zero-allocation | [[electronic/dsp/index]] · [[dsp-pipeline]] |
| USB enumeration ve UAC2 | [[usb-drivers]] |
| Hotplug, udev, uyumluluk | [[device-integration]] |
| Donanım seçimi (PCM3168A H001) | [[ADR-038-8.1-sound-card-chip-selection]] |
| DSP hardware modu (ADR-017) | [[ADR-017-dsp-hardware-mode]] |

### 8.3 Sürüm Kanıtı Durumu

| İddia | Durum |
|-------|-------|
| ASIO SDK 2.3.4 | ⚠️ VERIFICATION REQUIRED — sürüm kanıtı depoda yok (kural 12) |
| JUCE 9 | ⚠️ VERIFICATION REQUIRED — sürüm kanıtı depoda yok (kural 12) |

### 8.4 Repo Kod Durumu

**Repo'da sürücü/DSP kodu YOK — kaynak: .ai/electronic/**

Neva Engine `.cpp` yüzeyi PLANNED'dır; bu dosyadaki tüm tablolar spesifikasyon niteliğindedir.

---

## 9. xrun (Underrun/Overrun) Derinlemesine Analiz

### 9.1 xrun Tipi ve Kök Neden Matrisi

| Tip | Tanım | Tipik Kök Neden | Belirti |
|-----|-------|-----------------|---------|
| Capture overrun | Ring buffer taşma (uygulama yavaş okuyor) | Uygulama gecikmesi, scheduler | Click, tekrar eden örnekleme |
| Playback underrun | Ring buffer boşalma (uygulama hızlı yazıyor) | DSP aşırı yük, öncelik düşmesi | Click, sessizlik boşluğu |
| DMA underrun | Donanım DMA'ya veri bulamıyor | IRQ gecikmesi, DMA kanal çakışması | Hardware click |
| Period geç kalma | Period tam zamanında teslim edilmedi | Scheduler jitter | Zamanlama kayması |

### 9.2 Buffer ↔ Periyot ↔ xrun İlişkisi (Matematik)

```
Periyot süresi (T_period) = period_size / sample_rate
Örnek: 512 / 48000 = 10.667 ms          (ADR-017)

Buffer tampon süresi (T_buffer) = buffer_size / period_size × T_period
Örnek: 2 tampon × 10.667 = 21.33 ms

xrun toleransı = T_buffer − iş gecikmesi (jitter)
Kabul: xrun toleransı > p99 jitter → underrun olmaz
```

**Kabul örneği (48kHz / 512 sample):**

| Büyüklük | Değer | Not |
|----------|-------|-----|
| T_period | 10.667 ms | 512/48000 |
| T_buffer (2 tampon) | 21.33 ms | §3 |
| p99 scheduler jitter (hedef) | ≤2 ms | RT çekirdek varsayımı |
| Kalan pay | 19.33 ms | DSP işleme süresi için |

**Kural:** DSP işleme süresi (callback) p99 ≤2 ms hedefini aşarsa underrun riski doğar; ölçülen p99, §7.7 telemetrisiyle sürekli izlenir.

### 9.3 xrun Önleme Stratejileri (Katmanlı)

| Katman | Strateji | Etki |
|--------|----------|------|
| Buffer | ≥2 tampon (§9.2) | Jitter emilimi |
| Scheduler | SCHED_FIFO, çekirdek izolasyonu | Jitter ↓ |
| IRQ | IRQ affinity, coalescing (§2.5) | DMA gecikmesi ↓ |
| DSP | SIMD, sabit Tahsis (kural 1) | İşleme süresi ↓ |
| Gözlem | period_elapsed telemetri (§7.7) | Erken uyarı |

### 9.4 xrun Kurtarma Davranışı

| Durum | Davranış | Neden |
|-------|----------|-------|
| İlk xrun | Ring buffer doldur, devam | Tekrarlayan değilse |
| Tekrarlayan xrun (≥3) | Buffer bir seviye büyüt (§5.7) | Kalıcı yük |
| DMA underrun | IRQ/DMA yeniden senkron | Donanım kilidi |

**Kurtarma kuralı:** Tek xrun sessizce kurtarılır; ≥3 tekrarlayan xrun, buffer büyüme önerisini tetikler ve §7.7 telemetrisine işaret düşer.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode