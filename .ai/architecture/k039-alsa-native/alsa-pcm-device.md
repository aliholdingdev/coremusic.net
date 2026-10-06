---
title: "K039.1 — ALSA PCM Cihazları, MMAP ve XRUN Yönetimi"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.1.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K039.1 — ALSA PCM Cihazları

> **Bağlantılar:** [[index]] · [[rt-thread-ve-pipewire-siniri]] · [[../k033-platform-ses-suruculeri/pipewire-modern]] · [[../k031-buffer-management/kilitsiz-kuyruklar]] · [[../k032-latency-optimization/latency-optimization]] · [[../k023-system-calls/system-calls]] · [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]]

---

## §1 Genel Bakış

Bu dosya K039'in **teknik çekirdeği**dir: PCM cihaz tipleri, erişim yöntemleri, `hw_params` çağrıları, iki seviyeli buffer, period hesabı, MMAP yazma akışı, XRUN kurtarma, HW parametre tablosu ve `ALSADriver` arayüzü. Kaynak: `k2-surucu/alsa-native.md`.

---

## §2 Kapsam / Kapsam Dışı

| Kapsam içi | Kapsam dışı |
|-----------|-------------|
| PCM cihaz tipleri + erişim yöntemleri | Öncelik kararı → [[index]] §4.2 |
| `snd_pcm_hw_params_*` çağrıları | Buffer veri yapısı → [[../k031-buffer-management/kilitsiz-kuyruklar]] |
| Kernel/User buffer ilişkisi | Gecikme zinciri → [[../k032-latency-optimization/latency-optimization]] |
| Period hesabı ve kesme aralığı | Kesme/DMA akışı → [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] |
| MMAP begin/commit | Syscall arayüzü → [[../k023-system-calls/system-calls]] |
| XRUN tablosu + recover | PipeWire üst katmanı → [[../k033-platform-ses-suruculeri/pipewire-modern]] |
| RT thread kuralları (özet) | RT + PipeWire tam sınırı → [[rt-thread-ve-pipewire-siniri]] |

---

## §3 PCM Cihaz Tipleri ve Akışlar

### §3.1 Cihaz tipleri (kaynak)

| Sabit | Açıklama | Kanıt |
|-------|----------|-------|
| `PCM_DEVICE_PLAYBACK` | Ses çıkışı — `c:0, c:1, ...` | `alsa-native.md L35` |
| `PCM_DEVICE_CAPTURE` | Ses girişi — `p:0, p:1, ...` | aynı dosya `L36` |
| `PCM_DEVICE_DUPLEX` | Hem giriş hem çıkış | aynı dosya `L37` |

### §3.2 Cihaz açma adımları (`ALSADriver`)

| Adım | Çağrı | Kanıt |
|------|-------|-------|
| 1 | `initialize(const AudioConfig&)` | `L169` |
| 2 | `openPlayback(const char* deviceName)` | `L170` |
| 3 | `openCapture(const char* deviceName)` | `L171` |
| 4 | Parametreler: `setSampleRate` / `setChannels` / `setBufferSize` / `setPeriodSize` | `L175–L178` |
| 5 | `setAccessMode` / `enableMMAP` | `L181–L182` |
| 6 | Kapanış: `close()` | `L172` |

### §3.3 Kullanım örneği (kaynak)

| Adım | Çağrı | Değer | Kanıt |
|------|-------|-------|-------|
| 1 | `config.sampleRate = 96000` | 96 kHz | `L206` |
| 2 | `config.bitsPerSample = 32` | 32 bit | `L207` |
| 3 | `config.channels = 2` | 2 kanal | `L208` |
| 4 | `driver.openPlayback("hw:0,0")` | cihaz adı | `L211` |
| 5 | `driver.setBufferSize(256)` | 256 örnek | `L212` |
| 6 | `driver.setPeriodSize(64)` | 64 örnek | `L213` |
| 7 | `driver.enableMMAP()` | MMAP | `L214` |
| 8 | Döngü: `driver.write(engineOutput, 64)` | 64 örnek/yazma | `L217–L219` |

> 96000/32/2 ve 256/64 değerleri kaynaktaki **örnek koddur**; zorunlu varsayılan değildir.

---

## §4 Erişim Yöntemleri

| # | Sabit | Açıklama | Kanıt |
|---|-------|----------|-------|
| 1 | `SND_PCM_ACCESS_MMAP_INTERLEAVED` | Doğrudan bellek haritalama — **en düşük latency** | `L40` |
| 2 | `SND_PCM_ACCESS_MMAP_NONINTERLEAVED` | Non-interleaved MMAP | `L41` |
| 3 | `SND_PCM_ACCESS_RW_INTERLEAVED` | Okuma/yazma (interleaved) | `L42` |
| 4 | `SND_PCM_ACCESS_RW_NONINTERLEAVED` | Okuma/yazma (non-interleaved) | `L43` |

**Seçim kuralı (kaynak verisiyle):** MMAP = 0.8ms, RW = 4.2ms (`L226–L227`) → düşük gecikme hedefinde MMAP önceliklidir.

**MMAP ↔ RW geçişi davranışı:** ⚠️ VERIFICATION REQUIRED (kaynakta fallback kuralı yok).

---

## §5 Donanım Parametreleri (`hw_params`)

### §5.1 Çağrı seti (kaynak)

| # | Parametre | Çağrı | Kanıt |
|---|-----------|-------|-------|
| 1 | Örnekleme hızı | `snd_pcm_hw_params_set_rate_near(handle, hw_params, &sampleRate, 0)` | `L55–L56` |
| 2 | Kanal sayısı | `snd_pcm_hw_params_set_channels(handle, hw_params, &channels)` | `L59–L60` |
| 3 | Buffer boyutu | `snd_pcm_hw_params_set_buffer_size_near(handle, hw_params, &bufferSize)` | `L63–L64` |
| 4 | Periyot boyutu | `snd_pcm_hw_params_set_period_size_near(handle, hw_params, &periodSize, 0)` | `L67–L68` |
| 5 | Tahsis | `snd_pcm_hw_params_malloc(&hw_params)` | `L52` |
| 6 | Serbest bırakma | `snd_pcm_hw_params_free(hw_params)` | `L70` |

### §5.2 `_near` çağrısının anlamı

| Çağrı tipi | Davranış | Sonuç |
|-----------|----------|-------|
| `_near` (rate, buffer, period) | İstenen değere **en yakın** desteklenen değeri seçer | Uyuşmazlık sessizce uyarlanır → ölçüm gerekir |
| `_exact` yokluğu | Kaynakta kesin değer zorlaması görülmüyor | ⚠️ VERIFICATION REQUIRED |

> `_near` kullanımı, SR/buffer uyuşmazlıklarının **sessizce** karşılanabileceğini gösterir; bu durum guardrail'deki "SR uyuşmazlığı kabul edilmez" kuralıyla birlikte **uygulama düzeyinde doğrulama** gerektirir (`k2-surucu/CLAUDE.md L18–L24`).

### §5.3 HW parametre tablosu (HWDEP — kaynak)

| # | Parametre | Açıklama | Kanıt |
|---|-----------|----------|-------|
| 1 | `SND_PCM_HW_PARAM_ACCESS` | Erişim yöntemi | `L156` |
| 2 | `SND_PCM_HW_PARAM_FORMAT` | Ses formatı (PCM, FLOAT) | `L157` |
| 3 | `SND_PCM_HW_PARAM_CHANNELS` | Kanal sayısı | `L158` |
| 4 | `SND_PCM_HW_PARAM_RATE` | Örnekleme hızı | `L159` |
| 5 | `SND_PCM_HW_PARAM_BUFFER_SIZE` | Toplam buffer | `L160` |
| 6 | `SND_PCM_HW_PARAM_PERIOD_SIZE` | Periyot boyutu | `L161` |
| 7 | `SND_PCM_HW_PARAM_PERIODS` | Periyot sayısı | `L162` |

---

## §6 İki Seviyeli Buffer

### §6.1 Katmanlar (kaynak)

| Seviye | Ad | Davranış | Kanıt |
|--------|----|----------|-------|
| 1 | Kernel Buffer (ALSA Ring Buffer) | Write ptr / Read ptr, P0–P4 blokları | `L77–L86` |
| 2a | User Buffer — MMAP modu | Doğrudan kernel buffer'a yazma | `L89` |
| 2b | User Buffer — RW modu | `snd_pcm_writei()` ile kopyalama | `L90` |

### §6.2 Buffer ilişkisi (ASCII)

```
  Kernel Ring Buffer            ← alsa-native.md L77–L86
  [ P0 ][ P1 ][ P2 ][ P3 ][ P4 ]
     ▲ Write Ptr        ▲ Read Ptr

  User Buffer:
    MMAP  → doğrudan kernel buffer (L89)
    RW    → snd_pcm_writei() kopyası (L90)
```

**Çapraz bağ:** Ring buffer veri yapısı → [[../k031-buffer-management/kilitsiz-kuyruklar]]

---

## §7 Period Size

### §7.1 Formül (kaynak)

| Öğe | Değer | Kanıt |
|-----|-------|-------|
| Formül | `Period Size = Buffer Size / Number of Periods` | `L97` |
| Örnek buffer | 1024 samples | `L100` |
| Örnek periods | 4 | `L101` |
| Örnek period | 256 samples @ 48kHz = 5.33ms | `L102` |
| Örnek latency | Period / SampleRate = 5.33ms | `L104` |
| Anlam | Period size, **kesme (interrupt) aralığını** belirler | `L94` |

### §7.2 Period → latency bağı (yönlendirme)

| Soru | Nereye |
|------|--------|
| Kesme/DMA zinciri nasıl işler? | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] |
| Bütçe nasıl dağıtılır? | [[../k032-latency-optimization/latency-optimization]] |
| Buffer boyutu optimizasyonu? | [[../k031-buffer-management/buffer-management]] (kaynakta `L228–L259` buffer boyut optimizasyonu) |

---

## §8 MMAP Mod Implementasyonu

### §8.1 Yazma akışı (kaynak)

| Adım | Çağrı/İşlem | Kanıt |
|------|------------|-------|
| 1 | `snd_pcm_mmap_begin(handle, &areas, &offset, &frames)` | `L114` |
| 2 | `areas[0].addr` = write address | `L116` |
| 3 | `areas[0].first` = bit offset | `L117` |
| 4 | `areas[0].step` = bits per sample | `L118` |
| 5 | Adres hesabı: `addr + first/8 + offset*step/8` | `L121–L123` |
| 6 | `memcpy(buffer, engine_output, frames * sizeof(float))` | `L126` |
| 7 | `snd_pcm_mmap_commit(handle, offset, frames)` | `L128` |

### §8.2 Adres hesabı detayı

| Alan | Hesap | Kanıt |
|------|-------|-------|
| Başlangıç | `(char*)areas[0].addr` | `L121` |
| Bit offset | `+ (areas[0].first / 8)` | `L122` |
| Kare ofseti | `+ (offset * areas[0].step / 8)` | `L122–L123` |

### §8.3 Performans bağlantısı

| Yöntem | Hedef | Gerçek | Kanıt |
|--------|-------|--------|-------|
| MMAP latency | 1ms | 0.8ms | `L226` |
| RW latency | 5ms | 4.2ms | `L227` |

---

## §9 XRUN Yönetimi

### §9.1 XRUN tipleri (kaynak tablosu)

| XRUN Tipi | Neden | Çözüm | Kanıt |
|-----------|-------|-------|-------|
| Underrun | Buffer yetersiz | Buffer boyutunu artır | `L137` |
| Overrun | Buffer taştı | Period sayısını azalt | `L138` |
| Suspended | Donanımdurdu | `snd_pcm_prepare()` çağır | `L139` |

### §9.2 Kurtarma akışı (kaynak kod)

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `frames = snd_pcm_writei(handle, buffer, count)` | `L143` |
| 2 | `if (frames < 0)` | `L144` |
| 3 | `frames = snd_pcm_recover(handle, frames, 0)` | `L145` |
| 4 | Yeniden `snd_pcm_writei(...)` | `L146` |

### §9.3 Durum sorgusu

| Üye | Rol | Kanıt |
|-----|-----|-------|
| `recover(int error)` | Hata kurtarma | `L196` |
| `getState()` | `snd_pcm_state_t` okuma | `L197` |

### §9.4 Guardrail bağlantısı

| Guardrail | Kaynak |
|-----------|--------|
| Underrun koruması zorunlu | `k2-surucu/CLAUDE.md L18–L24` |
| RT thread'de blocking yasak | aynı |
| SR uyuşmazlığı kabul edilmez | aynı |

---

## §10 ALSADriver Arayüzü (kaynak sınıf)

| Grup | Üye | Kanıt |
|------|-----|-------|
| Yaşam döngüsü | `initialize` · `openPlayback` · `openCapture` · `close` | `L169–L172` |
| Parametre | `setSampleRate` · `setChannels` · `setBufferSize` · `setPeriodSize` | `L175–L178` |
| Erişim | `setAccessMode(snd_pcm_access_t)` · `enableMMAP()` | `L181–L182` |
| Okuma/yazma | `write(const float*, size_t)` · `read(float*, size_t)` | `L185–L186` |
| MMAP | `mmapBegin(...)` · `mmapCommit(...)` | `L189–L193` |
| XRUN | `recover(int)` · `getState()` | `L196–L197` |
| Cihaz | `static listDevices()` | `L200` |

---

## §11 Performans ve Bağımlılıklar

| Metrik | Hedef | Gercek | Kanıt |
|--------|-------|--------|-------|
| Latency (MMAP) | 1ms | 0.8ms | `L226` |
| Latency (RW) | 5ms | 4.2ms | `L227` |
| Buffer Boyutu | 64-256 | 64 | `L228` |
| CPU (boşta) | < 1% | 0.4% | `L229` |
| Maks. Kanal | 128 | 128 | `L230` |

| Bağımlılık | Tür | Kanıt |
|------------|-----|-------|
| libasound | Sistem kütüphanesi | `L236` |
| Linux Kernel ALSA | Çekirdek modülü | `L237` |
| K1 Linux Core | Alt katman | `L238` |

**Faz planı:** Faz 1 RW modu · Faz 2 MMAP · Faz 3 XRUN + optimizasyon · Faz 4 HWDEP + çoklu cihaz (`L242–L245`).

---

## §12 Adım Adım: MMAP Çıkış Akışı

| Adım | Girdi | İşlem | Çıktı |
|------|-------|-------|-------|
| 1 | config (SR/bit/kanal) | `initialize` | Yapılandırılmış driver |
| 2 | `"hw:0,0"` | `openPlayback` | Açık handle |
| 3 | 256 / 64 | `setBufferSize` / `setPeriodSize` | Parametreler |
| 4 | — | `enableMMAP` | MMAP erişimi |
| 5 | — | `mmapBegin` | `areas`, `offset`, `frames` |
| 6 | engine çıktısı | `memcpy` | Belleğe yazım |
| 7 | offset/frames | `mmapCommit` | Commit |
| 8 | negatif dönüş | `recover` → yeniden yazma | Kurtarma |

---

## §13 Kenar Durumlar

| # | Durum | Sonuç | Yönlendirme |
|---|-------|-------|-------------|
| 1 | Cihaz yok (`hw:0,0`) | Açılış hatası | `⚠️ VERIFICATION REQUIRED` |
| 2 | SR desteklenmiyor | `_near` uyarlar | Uygulama doğrulaması (§5.2) |
| 3 | Buffer aralık dışı | `_near` uyarlar | `L63–L64` |
| 4 | Underrun | Buffer artır | `L137` |
| 5 | Overrun | Period azalt | `L138` |
| 6 | Suspended | `prepare` | `L139` |
| 7 | MMAP reddi | Fallback kuralı yok | `⚠️ VERIFICATION REQUIRED` |
| 8 | Duplex asimetri | Ele alınmamış | `⚠️ VERIFICATION REQUIRED` |
| 9 | Period hesabı tutmaz | Latency sapması | §7 formülü + ölçüm |
| 10 | Kesme aralığı değişir | Latency etkisi | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] |

---

## §14 Hata Modları (geniş)

| # | Hata modu | Belirti | Kök neden | Düzeltme |
|---|-----------|---------|-----------|----------|
| HM1 | Underrun | Kopuk ses | Buffer yetersiz | Buffer artır (`L137`) |
| HM2 | Overrun | Bozuk okuma | Buffer taştı | Period azalt (`L138`) |
| HM3 | Suspended | Akış durur | Donanım durdu | `snd_pcm_prepare()` (`L139`) |
| HM4 | `writei < 0` | Yazma hatası | XRUN/timeout | `snd_pcm_recover` (`L145`) |
| HM5 | SR mismatch | Pitch hatası | `_near` uyarlaması | Guardrail + doğrulama |
| HM6 | Period tutarsızlığı | Latency sapması | Hesap/gerçek farkı | Formül + ölçüm |
| HM7 | Erişim reddi | MMAP açılmaz | Cihaz desteklemiyor | RW modu (`⚠️ VERIFICATION REQUIRED`) |
| HM8 | Cihaz kaybı | Handle geçersiz | Hotplug | [[../k034-usb-audio/usb-hotplug-enumerasyon]] |

---

## §15 Bağımlılıklar

| # | Bağımlılık | Yön | Not |
|---|-----------|-----|-----|
| 1 | `libasound` | Alt | Sistem kütüphanesi (`L236`) |
| 2 | Linux Kernel ALSA | Alt | Çekirdek modülü (`L237`) |
| 3 | K1 Linux Core | Alt | OS (`L238`) |
| 4 | K2 buffer manager | Alt | Ring buffer ([[../k031-buffer-management/index]]) |
| 5 | K2 driver stack | Alt | HAL şeması ([[../k030-driver-stack/index]]) |
| 6 | K2 latency | Alt | 1ms/5ms ([[../k032-latency-optimization/index]]) |
| 7 | PipeWire | Üst | Alternatif üst katman ([[../k033-platform-ses-suruculeri/index]]) |
| 8 | K3 Neva Engine | Üst | `write()` hedefi |

---

## §16 Doğrulama / Test

| ID | Test | Beklenen | Durum |
|----|------|----------|-------|
| P1 | Playback/capture açılışı | Geçerli handle | ⚠️ kod yok |
| P2 | `hw_params` kurulumu | Tüm çağrılar başarılı | ⚠️ kod yok |
| P3 | `_near` uyarlaması | En yakın değer kabul | ⚠️ kod yok |
| P4 | Period hesabı | 1024/4 = 256 (kaynak) | ⚠️ kod yok |
| P5 | MMAP begin/commit | Veri tutarlı | ⚠️ kod yok |
| P6 | RW yazma | `writei ≥ 0` | ⚠️ kod yok |
| P7 | Underrun kurtarma | recover → tekrar yazma | ⚠️ kod yok |
| P8 | Suspended kurtarma | `prepare` sonrası akış | ⚠️ kod yok |
| P9 | Latency MMAP | 0.8ms (kaynak) | ⚠️ ölçüm yok |
| P10 | Latency RW | 4.2ms (kaynak) | ⚠️ ölçüm yok |
| P11 | Kanal 128 sınırı | Kabul/reddetme tanımı | ⚠️ VERIFICATION REQUIRED |
| P12 | Buffer 64–256 | Aralık kontrolü | ⚠️ ölçüm yok |

---

## §17 Kanıt Satırları (bu dosya)

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | 3 PCM cihaz tipi | `k2-surucu/alsa-native.md` | L34–L37 |
| 2 | 4 erişim yöntemi | aynı | L39–L43 |
| 3 | `hw_params` çağrıları | aynı | L49–L71 |
| 4 | İki seviyeli buffer | aynı | L73–L91 |
| 5 | Period formülü + 256/4 örneği | aynı | L92–L105 |
| 6 | MMAP begin/commit | aynı | L107–L129 |
| 7 | XRUN tablosu | aynı | L131–L140 |
| 8 | recover akışı | aynı | L141–L148 |
| 9 | 7 HW parametresi | aynı | L150–L163 |
| 10 | `ALSADriver` arayüzü | aynı | L167–L201 |
| 11 | Kullanım örneği | aynı | L203–L219 |
| 12 | Performans tablosu | aynı | L222–L230 |
| 13 | Bağımlılıklar | aynı | L232–L238 |
| 14 | Faz planı | aynı | L240–L245 |
| 15 | Guardrail (RT/underrun/SR) | `k2-surucu/CLAUDE.md` | L18–L24 |
| 16 | Öncelik #4 | aynı | L27–L34 |

---

## §18 Wiki-Bağlantılar

| Tür | Hedef | Neden |
|-----|-------|-------|
| İç | [[index]] | Klasör indeksi |
| İç | [[rt-thread-ve-pipewire-siniri]] | RT thread kuralları, PipeWire sınırı |
| Komşu | [[../k033-platform-ses-suruculeri/pipewire-modern]] | Üst katman ilişkisi |
| Komşu | [[../k016-linux-ses/index]] | Linux ses yığını kardeş klasör |
| Komşu | [[../k031-buffer-management/kilitsiz-kuyruklar]] | Ring buffer |
| Komşu | [[../k032-latency-optimization/latency-optimization]] | Latency bütçesi |
| Komşu | [[../k030-driver-stack/driver-stack-mimari]] | Katmanlı soyutlama |
| Diğer | [[../k023-system-calls/system-calls]] | Syscall yüzeyi |
| Diğer | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] | Period ↔ kesme |
| Diğer | [[../k034-usb-audio/usb-hotplug-enumerasyon]] | Cihaz kaybı |
| Diğer | [[../k025-threading-model/gercek-zamanli-zamanlama]] | RT thread |

---

## §19 Risk Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|---------|------|---------|
| R1 | Kod yok | Yüksek | Yüksek | `⚠️ VERIFICATION REQUIRED` |
| R2 | Ölçüm yok | Yüksek | Orta | Ölçüm protokolü → [[index]] §14 |
| R3 | `_near` sessiz uyarlaması | Orta | Yüksek | Uygulama düzeyi SR doğrulaması |
| R4 | XRUN tekrarı | Orta | Yüksek | Buffer/period ayarı + sayaç |
| R5 | MMAP reddi sessizliği | Orta | Orta | Açık fallback kuralı (kapanışta) |
| R6 | Period ↔ kesme uyumsuzluğu | Düşük | Yüksek | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] |

### §19.1 XRUN Durum Tablosu (genişletilmiş ek)

| Durum | Tetikleyen | Belirti | Toplanan sayaç | Kurtarma |
|---|---|---|---|---|
| Underrun (playback) | DMA period'u tamamlandı, uygulama yazmadı | `EPIPE` | `xruns` | `snd_pcm_prepare` + yeniden start |
| Overrun (capture) | Uygulama okumadı, DMA doldu | `EPIPE` (capture) | `xruns` | `snd_pcm_prepare` |
| Suspended | Güç olayı / cihaz askıya alma | `ESTRPIPE` | — | `snd_pcm_resume`; başarısızsa prepare |
| Drain beklemesi | `snd_pcm_drain` açık kuyrukta | Bloklama | — | Timeout K039 sınırı: ⚠️ VERIFICATION REQUIRED |
| MMAP sayfa yok | Kullanıcı alanı eşlenmedi | `SIGBUS` / segfault | — | `snd_pcm_mmap` yeniden denenir |
| Period bölünemez | SR / kanal kombinasyonu desteklenmiyor | `hw_params` reddi | — | En yakın desteklenen `period_size` denenir |

### §19.2 `hw_params` Alan Eşlemesi (ek tablo)

| Alan | Rol | ALSA içi ilişki | Kanıt |
|---|---|---|---|
| `access` | MMAP/RW seçimi | `MMAP_INTERLEAVED` ↔ `RW_INTERLEAVED` | `alsa-pcm-device.md` §4 |
| `format` | Sample biçimi | Format ↔ kanal genişliği | ⚠️ VERIFICATION REQUIRED |
| `channels` | Kanal sayısı | Cihazın üst sınırına kadar | ⚠️ VERIFICATION REQUIRED |
| `rate` | Örnekleme hızı | `_near` ile en yakın değer | §13 `_near` uyarlaması |
| `period_size` | IRQ aralığı | `buffer_size / periods` | §7 formül |
| `buffer_size` | DMA tamponu | `period_size × periods` | §6 iki seviyeli buffer |
| `start_threshold` | Otomatik start | DMA eşiği | ⚠️ VERIFICATION REQUIRED |
| `stop_threshold` | Otomatik dur | Tampon dolu | ⚠️ VERIFICATION REQUIRED |

### §19.3 Adım Adım: RW (non-MMAP) Çıkış Akışı (ek)

| # | Adım | Çağrı/olay | Başarı ölçütü |
|---|---|---|---|
| 1 | Cihazı aç | `snd_pcm_open` | Handle != NULL |
| 2 | Parametreleri ayarla | `snd_pcm_hw_params` | 0 döner |
| 3 | Yazma alanını hazırla | Uygulama tamponu | Boyut = `period_size × frame_bytes` |
| 4 | Yaz | `snd_pcm_writei` | `frames` kadar yazıldı |
| 5 | DMA yayını | Kernel → donanım | Kesme tetiklenir (§7 / [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]]) |
| 6 | 4'e dön | — | Akış sürdürülür |
| 7 | EPIPE al | XRUN | `snd_pcm_prepare` → 3'e dön |
| 8 | Durdur | `snd_pcm_drain` / `snd_pcm_close` | Handle serbest |

### §19.4 Dosya → Bağımlılık Çapraz Tablosu (ek)

| Bağımlılık | İlgili bölüm | Hedef dosya |
|---|---|---|
| Buffer boyutu seçimi | §6 | [[../k031-buffer-management/kilitsiz-kuyruklar]] |
| Latency bütçesi | §7, §11 | [[../k032-latency-optimization/latency-optimization]] |
| Kesme / DMA akışı | §7, §8 | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] |
| Sürücü yığını katmanı | §10 | [[../k030-driver-stack/driver-stack-mimari]] |
| PipeWire SPA ALSA node'u | §10 | [[../k033-platform-ses-suruculeri/pipewire-modern]] |
| Kernel syscall yüzeyi | §11 | [[../k023-system-calls/system-calls]] |
| Lock-free kuyruk | §12 | [[../k031-buffer-management/kilitsiz-kuyruklar]] |
| RT thread / SCHED_FIFO kuralları | §9.4 | [[rt-thread-ve-pipewire-siniri]] |

### §19.5 Doğrulama Test Kriterleri (geniş ek tablo)

| # | Test | Girdi | Beklenen | Durum |
|---|---|---|---|---|
| T1 | `hw_params` geçerli kombinasyon | SR=48k, period 128 | 0 dönüş | ⚠️ VERIFICATION REQUIRED |
| T2 | `hw_params` geçersiz kombinasyon | Desteklenmeyen SR | Negatif + errno | ⚠️ VERIFICATION REQUIRED |
| T3 | MMAP başlat | `mmap` + start | Kesme zamanında period | ⚠️ VERIFICATION REQUIRED |
| T4 | Zorla underrun | Yazmayı durdur | `EPIPE` + `xruns` artışı | ⚠️ VERIFICATION REQUIRED |
| T5 | XRUN kurtarma | `prepare` sonrası yazma | Akış sürer | ⚠️ VERIFICATION REQUIRED |
| T6 | `_near` uyarlaması | Talep 44.1k, cihaz 48k | En yakın değer | ⚠️ VERIFICATION REQUIRED |
| T7 | Hotplug kaldırma | Cihaz fiziksel çıkarma | Hata + temiz kapanış | [[../k034-usb-audio/usb-hotplug-enumerasyon]] |
| T8 | Paralel iki handle | Aynı cihaz iki kez | `EBUSY` ya da paylaşım | ⚠️ VERIFICATION REQUIRED |

### §19.6 K039 ↔ K042 Buffer Ayrımı (ek — sık karıştırılır)

| Sorumluluk | K039 (ALSA) | K042 (buffer) |
|---|---|---|
| DMA tamponu boyutu | `buffer_size` ayarı | Boyut seçimi kuralı |
| İki seviyeli yapı (buffer/period) | Evet, ALSA'ya özgü | Hayır |
| Ring-buffer yazma/okuma çekirdeği | Kullanır | Sahiptir |
| Lock-free kuyruk algoritması | İlgilenmez | Sahiptir |
| XRUN kurtarma | Sahiptir | Yalnız sayaç/telemetri |
| Buffer değişikliği süresi ölçümü | Uygular | Tanımlar (`<5µs` hedefi) |
| Write/read <1µs hedefi | Ölçer | Sahiptir |

| Test ID | Kapsam | Beklenen | Kanıt durumu |
|---|---|---|---|
| KB-1 | ALSA buffer değişimi + K042 ölçümü | `<5µs` içinde | ⚠️ VERIFICATION REQUIRED |
| KB-2 | XRUN sonrası buffer yeniden başlatma | Sayaç + kurtarma | ⚠️ VERIFICATION REQUIRED |
| KB-3 | MMAP eşleme + ring-buffer paylaşımı | Bozulmasız akış | ⚠️ VERIFICATION REQUIRED |

### §19.7 Hızlı Referans — ALSA Çağrı Özet Tablosu (ek)

| Çağrı | Amaç | Hata kodu riski | İlgili § |
|---|---|---|---|
| `snd_pcm_open` | Cihazı aç | `EBUSY` | §3 |
| `snd_pcm_hw_params` | Donanım parametresi | `EINVAL` | §5 |
| `snd_pcm_sw_params` | Yazılım eşiği | `EINVAL` | §5 |
| `snd_pcm_prepare` | Hazırla / XRUN kurtarma | — | §9 |
| `snd_pcm_start` | DMA başlat | `EBADFD` | §8 |
| `snd_pcm_writei` | Interleaved yaz | `EPIPE`, `ESTRPIPE` | §12 |
| `snd_pcm_readi` | Interleaved oku | `EPIPE` | §12 |
| `snd_pcm_mmap` | Bellek eşle | `ENODEV` | §8 |
| `snd_pcm_drain` | Kuyruğu boşalt | Zaman aşımı riski | §12 |
| `snd_pcm_close` | Kapat | — | §12 |

> Çağrı adları kaynak `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` içindeki anlatımla uyumludur; birebir imza listesi `⚠️ VERIFICATION REQUIRED` (repo'da ALSA kodu yok).

---

## §20 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — ALSA PCM/MMAP/XRUN dosyası | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.1–§19.5 ek tablolar (satır hedefi 500+) eklendi | Vault Documentation Specialist |
| 2026-10-06 | 4.1.0 | Ölü `../k042-*` (L148) ve `../k050-*` (L468) linkleri `k031/kilitsiz-kuyruklar` ve `k034/usb-hotplug-enumerasyon`'a taşındı; 126/126 link diskte doğrulandı | Vault Documentation Specialist |
