---
title: "K038.1 — CoreAudio HAL, AudioUnit ve Render Callback"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.1.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K038.1 — CoreAudio HAL ve AudioUnit

> **Bağlantılar:** [[index]] · [[dusuk-gecikme-yolu]] · [[../k036-asio-drivers/asio-exclusive-mode]] · [[../k030-driver-stack/driver-stack-mimari]] · [[../k031-buffer-management/kilitsiz-kuyruklar]] · [[../k032-latency-optimization/latency-optimization]]
>
> **Kaynak:** Tüm sayısal iddialar `k2-surucu/core-audio-macos.md` (290 satır, `_backup/arch-2026-10-06_1057/architecture/`) satırlarına dayanır — envanter için [[index]] §0.

---

## §1 Genel Bakış

Bu dosya K038'in **teknik çekirdeği**dir: cihaz listesi, AudioUnit kurulumu, format sabitleme, render callback, HAL property'leri, zamanlama ve `CoreAudioDriver` arayüzü. Tüm sayısal iddialar `k2-surucu/core-audio-macos.md` satırlarına dayanır.

---

## §2 Kapsam / Kapsam Dışı

| Kapsam içi | Kapsam dışı |
|-----------|-------------|
| `AudioDeviceID` listeleme | Sürücü öncelik kararı → [[index]] §4.2 |
| AudioUnit + ASBD kurulumu | Buffer veri yapısı → [[../k031-buffer-management/buffer-management]] |
| Render callback sözleşmesi | Gecikme zinciri → [[../k032-latency-optimization/latency-optimization]] |
| HAL property tablosu | USB sınıfı → [[../k034-usb-audio/usb-audio-class]] |
| Zamanlama / clock okuma | Hotplug olay akışı → [[../k034-usb-audio/usb-hotplug-enumerasyon]] |
| RT thread yasaklarının callback'e uygulanışı | Gecikme bütçesi hesabı → [[dusuk-gecikme-yolu]] |

---

## §3 Cihaz Listesi ve Seçim

### §3.1 Cihaz listesi adımları

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `kAudioHardwarePropertyDevices` adresini kur | `core-audio-macos.md L50–L54` |
| 2 | Boyutu sorgula (`GetPropertyDataSize`) | aynı dosya `L56–L58` |
| 3 | `deviceCount = dataSize / sizeof(AudioDeviceID)` | aynı dosya `L60` |
| 4 | Vektörü doldur (`GetPropertyData`) | aynı dosya `L61–L64` |

### §3.2 Varsayılan çıkış cihazı

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `kAudioHardwarePropertyDefaultOutputDevice` adresi | aynı dosya `L190–L194` |
| 2 | Mevcut `defaultDevice` oku | aynı dosya `L196–L200` |
| 3 | `AudioObjectSetPropertyData` ile değiştir | aynı dosya `L202–L208` |

### §3.3 Cihaz seçimi kuralları

1. Cihaz önce **UID** ile tanınır (`kAudioDevicePropertyDeviceUID`, `L155`).
2. Değişim sonrası SR aralığı yeniden doğrulanır (`L244–L245`).
3. Cihaz yoksa seçilemez; davranış `⚠️ VERIFICATION REQUIRED`.

---

## §4 AudioUnit Kurulumu

### §4.1 Bileşen tanımı

| Alan | Değer | Kanıt |
|------|-------|-------|
| `componentType` | `kAudioUnitType_Output` | `core-audio-macos.md L74` |
| `componentSubType` | `kAudioUnitSubType_HALOutput` | aynı dosya `L75` |
| `componentManufacturer` | `kAudioUnitManufacturer_Apple` | aynı dosya `L76` |
| Bulma | `AudioComponentFindNext(NULL, &desc)` | aynı dosya `L81` |
| Örnekleme | `AudioComponentInstanceNew` | aynı dosya `L82–L83` |

### §4.2 IO etkinleştirme

| Alan | Değer | Kanıt |
|------|-------|-------|
| Özellik | `kAudioOutputUnitProperty_EnableIO` | aynı dosya `L87` |
| Scope | `kAudioUnitScope_Input` | aynı dosya `L88` |
| Element | `1` (input element) | aynı dosya `L89` |

### §4.3 Format (ASBD) — kaynak örneği

| Alan | Değer | Kanıt |
|------|-------|-------|
| `mSampleRate` | `96000` | aynı dosya `L95` |
| `mFormatID` | `kAudioFormatLinearPCM` | aynı dosya `L96` |
| `mFormatFlags` | Float \| Packed | aynı dosya `L97–L98` |
| `mBitsPerChannel` | `32` | aynı dosya `L99` |
| `mChannelsPerFrame` | `2` | aynı dosya `L100` |
| `mFramesPerPacket` | `1` | aynı dosya `L101` |
| `mBytesPerFrame` | kanal × (bit/8) | aynı dosya `L102–L103` |
| Atama | `kAudioUnitProperty_StreamFormat` | aynı dosya `L106–L111` |

> **Not:** 96000/32/2 örneği kaynaktaki **örnek koddur**; zorunlu varsayılan değildir. SR aralığı 44.1k–384k (`L266–L274`).

---

## §5 Render Callback

### §5.1 İmza (kaynak)

| Parametre | Tip | Rol |
|-----------|-----|-----|
| `inRefCon` | `void*` | Bağlam (engine) |
| `ioActionFlags` | `AudioUnitRenderActionFlags*` | Bayraklar |
| `inTimeStamp` | `const AudioTimeStamp*` | Zaman damgası |
| `inBusNumber` | `UInt32` | Bar |
| `inNumberFrames` | `UInt32` | Kare sayısı |
| `ioData` | `AudioBufferList*` | Çıkış buffer'ı |

Kanıt: `core-audio-macos.md L120–L125`

### §5.2 Gövde adımları

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `float* buffer = (float*)ioData->mBuffers[0].mData` | aynı dosya `L127` |
| 2 | `engine->process(buffer, inNumberFrames)` | aynı dosya `L129–L130` |
| 3 | `return noErr` | aynı dosya `L132` |

### §5.3 Bağlama

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | `AURenderCallbackStruct` doldur | aynı dosya `L136–L138` |
| 2 | `kAudioOutputUnitProperty_SetInputCallback` | aynı dosya `L140–L145` |

### §5.4 Callback içinde yapılmayacaklar (guardrail türetmesi)

| # | Yasağı | Dayanak |
|---|--------|---------|
| 1 | Bloke eden kilit alınması | `k2-surucu/CLAUDE.md L18–L24` (RT blocking yasak) |
| 2 | Underrun'a yol açan uzun işlem | aynı guardrail (underrun koruması) |
| 3 | SR uyuşmazlığını gizleyen dönüşüm | aynı guardrail (SR mismatch reddi) |
| 4 | Bellek ayırma (malloc/new) ve dosya/IO erişimi | aynı guardrail'in 2. maddesinden **türetme** (blocking/underrun riski) — kaynakta doğrudan yazılmamıştır |

> 1–3 satırlar guardrail'den **türetilmiş** tasarım notudur, 4. satır ayrıca açıkça **türetme** olarak işaretlidir; hiçbiri doğrudan `core-audio-macos.md` alıntısı değildir. Tam liste: [[dusuk-gecikme-yolu]] §3.

---

## §6 HAL Device Properties

| # | Property | Açıklama | Kanıt |
|---|----------|----------|-------|
| 1 | `kAudioDevicePropertyDeviceNameCFString` | Cihaz adı | `L154` |
| 2 | `kAudioDevicePropertyDeviceUID` | Benzersiz tanımlayıcı | `L155` |
| 3 | `kAudioDevicePropertyTransportType` | Bağlantı türü | `L156` |
| 4 | `kAudioDevicePropertySupportedSampleRates` | Desteklenen hızlar | `L157` |
| 5 | `kAudioDevicePropertyAvailableNominalSampleRates` | Nominal hız aralığı | `L158` |
| 6 | `kAudioDevicePropertyBufferFrameSize` | Buffer boyutu | `L159` |

Kaynak: `k2-surucu/core-audio-macos.md L148–L159`

---

## §7 Zamanlama ve Senkronizasyon

| # | İşlem | Çağrı | Kanıt |
|---|-------|-------|-------|
| 1 | Zaman damgası al | `AudioDeviceGetCurrentTime(deviceID, &timestamp)` | `L167–L168` |
| 2 | Akışı başlat | `AudioOutputUnitStart(au)` | `L171` |
| 3 | Clock değişikliğini izle | `kAudioDevicePropertyNominalSampleRate` | `L175–L181` |

**Bağlantı:** Zamanlama hedefleri → [[../k032-latency-optimization/latency-optimization]] · kesme/DMA zamanlaması → [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] · genel RT zamanlama → [[../k025-threading-model/gercek-zamanli-zamanlama]]

---

## §8 CoreAudioDriver Arayüzü (kaynak sınıf)

| Grup | Üye | Kanıt |
|------|-----|-------|
| Yaşam döngüsü | `initialize()` · `shutdown()` | `L216–L217` |
| Cihaz | `listDevices()` · `getDefaultOutputDevice()` · `getDefaultInputDevice()` · `setOutputDevice(uint32_t)` | `L220–L223` |
| Akış | `setSampleRate(double)` · `setBufferSize(uint32_t)` · `setChannelCount(uint32_t)` | `L226–L228` |
| Kontrol | `start()` · `stop()` · `isRunning()` | `L231–L233` |
| Callback | `registerRenderCallback(RenderCallback, void*)` | `L236–L237` |
| Zamanlama | `getCurrentTime()` · `getLatency()` | `L240–L241` |
| Cihaz özellikleri | `getSupportedSampleRates(uint32_t)` · `getBufferSizeRange(uint32_t)` | `L244–L247` |

Köprü: `driver.setSampleRate(96000)` · `driver.setBufferSize(256)` → `driver.start()` (`L256–L263`).

---

## §9 Performans (kaynak tablosu)

| Metrik | Hedef | Gercek |
|--------|-------|--------|
| Latency | 1ms | 0.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.6% |
| Maks. Kanal | 128 | 128 |
| Desteklenen SR | 44.1k-384k | 44.1k-384k |

Kanıt: aynı dosya `L266–L274` · Bağımlılıklar `L276–L282` · Bu tablo ile `k2-surucu/CLAUDE.md L36–L43` latency hesabı arasındaki çelişki → [[dusuk-gecikme-yolu]] §8

---

## §10 Adım Adım: Düşük Gecikmeli Akış

| Adım | Girdi | İşlem | Çıktı |
|------|-------|-------|-------|
| 1 | — | Cihaz listesi | `vector<AudioDeviceID>` |
| 2 | — | Varsayılan çıkış | `AudioDeviceID` |
| 3 | SR/biti/kanal | ASBD kur + `StreamFormat` | Kabul |
| 4 | Buffer frames | `setBufferSize` | Atama |
| 5 | engine | `registerRenderCallback` | Kayıt |
| 6 | — | `start()` | Akış |

---

## §11 Kenar Durumlar

| # | Durum | Sonuç | Yönlendirme |
|---|-------|-------|-------------|
| 1 | Liste boş | Çıkış yok | `⚠️ VERIFICATION REQUIRED` |
| 2 | SR aralık dışı | Format reddi | Önceden `getSupportedSampleRates` |
| 3 | Buffer aralık dışı | Atama reddi | `getBufferSizeRange` |
| 4 | Cihaz kaybı | Akış kesilir | [[../k034-usb-audio/usb-hotplug-enumerasyon]] |
| 5 | UID değişti | Oturum kopuk | UID yeniden bağlama |
| 6 | Clock değişti | SR yeniden oku | §7 adım 3 |
| 7 | Callback overrun | Kopuk ses | [[../k032-latency-optimization/latency-optimization]] |
| 8 | Giriş+çıkış birlikte | IO ayrı açılmalı | §4.2 |

---

## §12 Hata Modları

| # | Hata modu | Belirti | Kök neden | Düzeltme |
|---|-----------|---------|-----------|----------|
| HM1 | ASBD eksik alan | Bozuk ses | `mBytesPerFrame` hesaplanmadı | `L102–L104` formülü |
| HM2 | IO kapalı | Giriş yok | `EnableIO` unutuldu | §4.2 |
| HM3 | Callback bağlanmadı | Sessizlik | `SetInputCallback` çağrılmadı | §5.3 |
| HM4 | Cihaz seçimi kalıcı değil | Eski cihaza yazım | Varsayılan geri alınmış | UID doğrulama |
| HM5 | SR mismatch | Pitch hatası | Uygulama/cihaz farkı | guardrail `CLAUDE.md L18–L24` |
| HM6 | Underrun | Kopuk/tıslama | Callback gecikmesi | [[../k031-buffer-management/kilitsiz-kuyruklar]] |

---

## §13 Bağımlılıklar

| # | Bağımlılık | Yön | Not |
|---|-----------|-----|-----|
| 1 | `CoreAudio.framework` | Alt | `L276–L282` |
| 2 | `AudioToolbox.framework` | Alt | `L276–L282` |
| 3 | K1 macOS Core | Alt | GCD/XPC/IOKit → `../k021-macos-core/macos-core` |
| 4 | K2 driver stack | Alt | HAL soyutlama şeması → `../k030-driver-stack/driver-stack-mimari` |
| 5 | K3 Neva Engine | Üst | `engine->process` hedefi → `../k072-neva-engine-core/index` |
| 6 | Metal GPU (opsiyonel) | Yan | `macos-core.md L209` |

---

## §14 Doğrulama / Test

| ID | Test | Beklenen | Durum |
|----|------|----------|-------|
| T1 | Cihaz listesi dolu | vektör boş değil | ⚠️ kod yok |
| T2 | Varsayılan çıkış değişimi | Property yazımı başarılı | ⚠️ kod yok |
| T3 | ASBD tam alanlar | `mBytesPerFrame` tutarlı | ⚠️ kod yok |
| T4 | Callback her dönem çağrılıyor | Sayaç artıyor | ⚠️ kod yok |
| T5 | SR aralık dışı reddi | Hata döner | ⚠️ kod yok |
| T6 | `getLatency()` ≈ 0.8ms | Kaynakla uyumlu | ⚠️ ölçüm yok |

---

## §15 Kanıt Satırları (bu dosya)

| # | İddia | Kaynak | Satır |
|---|-------|--------|-------|
| 1 | Cihaz listesi 4 adım | `k2-surucu/core-audio-macos.md` | L44–L65 |
| 2 | Varsayılan çıkış değiştirme | aynı | L184–L209 |
| 3 | Bileşen tanımı HALOutput | aynı | L73–L83 |
| 4 | EnableIO input element 1 | aynı | L86–L91 |
| 5 | ASBD örneği 96000/32/2 | aynı | L94–L104 |
| 6 | Callback gövdesi | aynı | L120–L133 |
| 7 | Callback bağlama | aynı | L136–L145 |
| 8 | HAL property tablosu | aynı | L148–L159 |
| 9 | Zamanlama çağrıları | aynı | L161–L182 |
| 10 | `CoreAudioDriver` üyeleri | aynı | L211–L248 |
| 11 | Kullanım örneği | aynı | L250–L263 |
| 12 | Performans tablosu | aynı | L266–L274 |
| 13 | Bağımlılıklar | aynı | L276–L282 |
| 14 | Durum: Implementasyon | aynı | L284 |
| 15 | Guardrail RT yasakları (§5.4) | `k2-surucu/CLAUDE.md` | L18–L24 |
| 16 | Dosya envanteri/yedek konumu | disk kontrolü (v4.1.0) | [[index]] §0 |

---

## §16 Wiki-Bağlantılar

| Tür | Hedef | Neden |
|-----|-------|-------|
| İç | [[index]] | Klasör indeksi |
| İç | [[dusuk-gecikme-yolu]] | Gecikme bütçesi + RT kuralları |
| Komşu | [[../k036-asio-drivers/asio-exclusive-mode]] | Exclusive kavramı karşılaştırması |
| Komşu | [[../k037-wasapi-exclusive/wasapi-exclusive-shared]] | Windows modları |
| Komşu | [[../k030-driver-stack/driver-stack-mimari]] | HAL katman şeması |
| Komşu | [[../k031-buffer-management/kilitsiz-kuyruklar]] | Buffer veri yapısı |
| Komşu | [[../k032-latency-optimization/latency-optimization]] | 1ms bütçe |
| Komşu | [[../k039-alsa-native/alsa-pcm-device]] | Linux karşılığı |
| Diğer | [[../k018-dma-kesinti-yonetimi/irq-kesinti-yoneticisi]] | Zamanlama/DMA |
| Diğer | [[../k034-usb-audio/usb-hotplug-enumerasyon]] | Cihaz kaybı |
| Platform | [[../k021-macos-core/core-audio-macos]] | Aynı yedeğe dayanan vault eşleniği (SSOT notu [[index]] §0) |

---

## §17 Risk Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|---------|------|---------|
| R1 | Kod implementasyonu yok | Yüksek | Yüksek | `⚠️ VERIFICATION REQUIRED` |
| R2 | Ölçüm yok ("Gerçek" alıntı) | Yüksek | Orta | Ölçüm protokolü → [[index]] §14 |
| R3 | UID kayması | Orta | Yüksek | UID ile yeniden bağlama |
| R4 | SR mismatch | Orta | Yüksek | Guardrail |
| R5 | Callback overrun | Düşük | Yüksek | Buffer/period bütçesi |
| R6 | latency iddiası ↔ K-B hesabı çelişkisi | Yüksek | Yüksek | [[dusuk-gecikme-yolu]] §8 |

---

## §18 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | CoreAudio kodu | ⚠️ VERIFICATION REQUIRED |
| 2 | Gerçek cihaz ölçümü | ⚠️ VERIFICATION REQUIRED |
| 3 | Hog mode / exclusive davranışı | ⚠️ VERIFICATION REQUIRED |
| 4 | HAL plugin özelleştirmesi | ⚠️ VERIFICATION REQUIRED |
| 5 | IOProc terimi | ⚠️ VERIFICATION REQUIRED |
| 6 | CoreMIDI / AVAudioEngine | `[UNKNOWN]` — kanıt yok ([[index]] §1.3) |

---

## §19 Kontrol Listesi

- [x] Frontmatter 7 alan · `version: 4.1.0` · `updated: 2026-10-06`
- [x] ≥500 satır
- [x] Wiki-link hedefleri **diskte mevcut dosyalara** (v4.1.0 doğrulaması)
- [x] Kanıt = yol + satır (§15: 16 satır)
- [x] Doğrulanmayan = `⚠️ VERIFICATION REQUIRED` · bilinmeyen = `[UNKNOWN]`
- [x] Yeni sayı/sürüm uydurulmadı
- [x] Başka dosyaya yazılmadı · commit yok
- [x] Kaynak bölümü → [[index]] §0 (yedek konumları birebir)

---

## §20 Ek Tablolar (K038.1 genişletme) — v4.1.0'da §19'dan ayrıldı (başlık çakışması giderildi)

### §20.1 Platform karşılaştırma matrisi (ses yolları)

> Karşılaştırma hücrelerinin yedek kaynakları: `asio-drivers.md L156–L163` · `wasapi-exclusive.md L53–L59` · `alsa-native.md L222–L230` (tam yollar `_backup/arch-2026-10-06_1057/architecture/k2-surucu/` — bkz. [[index]] §0 K-D/K-E/K-F).

| Özellik | CoreAudio (K038) | ASIO (K036) | WASAPI Exc. (K037) | ALSA (K039) |
|---------|------------------|-------------|--------------------|--------------|
| Öncelik sırası | #3 | #1 | #2 | #4 |
| Sahiplik modeli | ⚠️ VERIFICATION REQUIRED | Tek uygulama (K-B madde 1) | Exclusive / Shared (K-E) | MMAP / RW erişim (K-F) |
| Callback tipi | Render callback | bufferSwitch benzeri | Event-driven | ⚠️ VERIFICATION REQUIRED |
| Latency (kaynak) | 1ms/0.8ms | 1.34ms/1.33ms (round-trip) | Exclusive 1–3ms / Shared 10–40ms | MMAP 1ms/0.8ms · RW 5ms/4.2ms |
| Buffer aralığı | 64–256 (hedef) / 128 (gerçek) | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED | 64–256 (hedef) / 64 (gerçek) |
| SR aralığı | 44.1k–384k | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| Kanal | 128 | 64×64 | ⚠️ VERIFICATION REQUIRED | 128 |
| Kaynak | `core-audio-macos.md L266–L274` | `asio-drivers.md L156–L163` | `wasapi-exclusive.md L53–L59` | `alsa-native.md L222–L230` |

**Öncelik kaynağı:** `k2-surucu/CLAUDE.md L27–L34` · **WASAPI Shared latency (10–40ms) ve ALSA RW 4.2ms:** K-E `L53–L59` / K-F `L222–L230` ile bu revizyonda doğrulandı.

### §20.2 Callback yaşam döngüsü tablosu

| Evre | Çağrı/İşlem | Sonuç | Kanıt |
|------|------------|-------|-------|
| Kurulum | `AudioComponentFindNext` + `InstanceNew` | `AudioUnit` elde edilir | `L81–L83` |
| IO açma | `EnableIO` (input scope, element 1) | Giriş yönü etkin | `L86–L91` |
| Format | `kAudioUnitProperty_StreamFormat` | ASBD uygulanır | `L106–L111` |
| Callback kaydı | `kAudioOutputUnitProperty_SetInputCallback` | `renderCallback` bağlanır | `L140–L145` |
| Başlatma | `AudioOutputUnitStart` | Akış başlar | `L171` |
| Dönemsel | `renderCallback` → `engine->process` | Veri üretilir | `L120–L133` |
| Durdurma | `stop()` | Akış durur | `L232` |
| Kapanış | `shutdown()` | Kaynak serbest | `L217` |

### §20.3 Format alanları (ASBD) hesap kuralları

| Alan | Kural | Kanıt |
|------|-------|-------|
| `mBytesPerFrame` | `mChannelsPerFrame × (mBitsPerChannel / 8)` | `L102–L103` |
| `mBytesPerPacket` | `mBytesPerFrame` (kare = 1 paket) | `L104` |
| `mFramesPerPacket` | `1` (düz PCM) | `L101` |
| `mFormatID` | `kAudioFormatLinearPCM` | `L96` |
| `mFormatFlags` | Float \| Packed | `L97–L98` |

### §20.4 Zamanlama ve clock tablosu

| # | Ne | Neden | Kanıt |
|---|----|-------|-------|
| 1 | `AudioDeviceGetCurrentTime` | Cihaz zaman damgası | `L167–L168` |
| 2 | `AudioTimeStamp` | Senkron referansı | `L166–L168` |
| 3 | `AudioOutputUnitStart` | Akışı zamana bağlama | `L171` |
| 4 | `kAudioDevicePropertyNominalSampleRate` | Clock drift izleme | `L175–L181` |
| 5 | `getLatency()` | Gecikme okuma | `L241` |
| 6 | `getCurrentTime()` | Uygulama saati | `L240` |

### §20.5 Cihaz özellikleri — kullanım matrisi

| Property | Kim okur? | Ne için? | Kanıt |
|----------|-----------|----------|-------|
| `DeviceNameCFString` | UI | Gösterim | `L154` |
| `DeviceUID` | Kalıcılık katmanı | Oturum yeniden bağlama | `L155` |
| `TransportType` | Bağlantı sınıflandırma | USB/FireWire ayrımı | `L156` |
| `SupportedSampleRates` | Format pazarlığı | Uygunluk kontrolü | `L157` |
| `AvailableNominalSampleRates` | Clock yönetimi | Nominal aralık | `L158` |
| `BufferFrameSize` | Buffer yöneticisi | Period boyutu | `L159` |

> `TransportType` yorumu ("USB/FireWire ayrımı") kaynakta yalnız "Bağlantı türü" olarak geçer (`L156`); alt değerler `[UNKNOWN]`.

### §20.6 Hata modları — geniş matris

| # | Hata modu | Belirti | Kök neden | Düzeltme | İlgili bölüm |
|---|-----------|---------|-----------|----------|--------------|
| E1 | ASBD eksik alan | Bozuk ses | `mBytesPerFrame` hesaplanmadı | Formülü uygula | §4.3 |
| E2 | IO kapalı | Giriş yok | `EnableIO` unutuldu | §4.2 | §4.2 |
| E3 | Callback yok | Sessizlik | `SetInputCallback` çağrılmadı | Callback'ı bağla | §5.3 |
| E4 | Cihaz seçimi kayıp | Eski cihaz | UID eşleşmesi kopuk | UID yeniden bağla | §3.3 |
| E5 | SR mismatch | Pitch hatası | Uygulama/cihaz farkı | Guardrail reddi | §5.4 |
| E6 | Underrun | Kopuk ses | Callback gecikmesi | Buffer bütçesi | §12 |
| E7 | Clock drift | Zamanlama kayması | SR değişimi | Yeniden oku | §7 |
| E8 | Liste boş | Açılış başarısız | Cihaz yok | `⚠️ VERIFICATION REQUIRED` | §11 |

### §20.7 Doğrulama testleri — geniş matris

| ID | Test | Adım | Beklenen | Durum |
|----|------|------|----------|-------|
| T7 | UID kalıcılık | Yeniden açılış | Aynı cihaz seçilir | ⚠️ kod yok |
| T8 | Giriş+çıkış | İki akış | İkisi de akar | ⚠️ kod yok |
| T9 | Clock izleme | SR değişimi | Yeniden okuma tetiklenir | ⚠️ kod yok |
| T10 | 128 kanal sınırı | Aşım isteği | Red | ⚠️ kod yok |
| T11 | 384k SR | Destekli cihaz | Çalışır | ⚠️ kod yok |
| T12 | Callback sayacı | Zaman penceresi | Dönem sayısı = beklenti | ⚠️ kod yok |
| T13 | Dur/sür | stop→start | Akış sürer | ⚠️ kod yok |
| T14 | Cihaz adı | Okuma | Boş değil | ⚠️ kod yok |
| T15 | Buffer aralık okuma | `getBufferSizeRange` | 64–256 (kaynak) | ⚠️ ölçüm yok |
| T16 | Latency tekrarı | 3 ölçüm | ≈0.8ms (kaynak) | ⚠️ ölçüm yok |
| T17 | RT thread'de bloke çağrı yok | callback profili | lock/IO/allocate yok | ⚠️ kod yok |

### §20.8 Bağımlılık matrisi (geniş)

| # | Bağımlılık | Yön | Tür | Kanıt |
|---|-----------|-----|-----|-------|
| B1 | `CoreAudio.framework` | Alt | Apple framework | `L276–L282` |
| B2 | `AudioToolbox.framework` | Alt | Apple framework | `L276–L282` |
| B3 | K1 macOS Core | Alt | OS | `L276–L282` |
| B4 | K2 driver stack | Alt | Mimari | `k2-surucu/driver-stack-mimari.md` (yedek) |
| B5 | K2 buffer manager | Alt | Veri yapısı | `k2-surucu/buffer-management.md` (yedek) |
| B6 | K2 latency | Alt | Metrik | `k2-surucu/latency-optimization.md` (yedek) |
| B7 | K3 Neva Engine | Üst | Tüketici | `L129–L130` |
| B8 | Metal GPU (opsiyonel) | Yan | Hızlandırma | `k0/macos-core.md L209, L531` (yedek) |

### §20.9 Açık konular — kapanış planı

| # | Konu | Durum | Kapanış koşulu |
|---|------|-------|----------------|
| 1 | CoreAudio kodu | ⚠️ VERIFICATION REQUIRED | `shared/src` altında derlenen kod |
| 2 | Gerçek ölçüm (0.8ms) | ⚠️ VERIFICATION REQUIRED | Ölçüm raporu |
| 3 | Hog mode | ⚠️ VERIFICATION REQUIRED | Kaynak kanıtı veya test |
| 4 | HAL plugin özelleştirmesi | ⚠️ VERIFICATION REQUIRED | Tasarım kararı |
| 5 | IOProc terimi | ⚠️ VERIFICATION REQUIRED | Kaynak doğrulaması |
| 6 | Metal GPU <1ms | Hedef var, gerçek "Belirlenecek" | `macos-core.md L531` ölçümü |
| 7 | CoreMIDI / AVAudioEngine | `[UNKNOWN]` | Kaynak + satır aralığı |
| 8 | k021 ↔ k038 SSOT ilişkisi | ⚠️ VERIFICATION REQUIRED | Üst merci kararı ([[index]] §0 madde 3) |

### §20.10 Okuma sırası (K038.1)

| Sıra | Ne okunur | Amaç |
|------|-----------|------|
| 1 | [[index]] §0–§4 | Kaynaklar, kapsam, öncelik, RT kuralları |
| 2 | Bu dosya §3–§5 | Cihaz, AudioUnit, callback |
| 3 | Bu dosya §6–§8 | HAL property, zamanlama, arayüz |
| 4 | [[dusuk-gecikme-yolu]] | Gecikme hesabı + çelişki kaydı |
| 5 | [[../k030-driver-stack/driver-stack-mimari]] | Alt katman |
| 6 | [[../k031-buffer-management/kilitsiz-kuyruklar]] | Buffer |
| 7 | [[../k032-latency-optimization/latency-optimization]] | 1ms bütçe |
| 8 | [[../k034-usb-audio/usb-hotplug-enumerasyon]] | Cihaz kaybı |

### §20.11 Hızlı başvuru kartı (K038.1)

| Soru | Cevap | Kaynak |
|------|-------|--------|
| Cihaz listesi nasıl alınır? | `kAudioHardwarePropertyDevices` → boyut → vektör | `L50–L64` |
| Varsayılan çıkış nasıl değişir? | `kAudioHardwarePropertyDefaultOutputDevice` + `SetPropertyData` | `L190–L208` |
| AudioUnit nasıl kurulur? | `AudioComponentFindNext` + `InstanceNew` | `L81–L83` |
| IO nasıl açılır? | `EnableIO`, scope input, element 1 | `L86–L91` |
| Format nasıl sabitlenir? | `kAudioUnitProperty_StreamFormat` + ASBD | `L106–L111` |
| Callback nereye bağlanır? | `kAudioOutputUnitProperty_SetInputCallback` | `L140–L145` |
| Veri nereye yazılır? | `ioData->mBuffers[0].mData` | `L127` |
| Latency nasıl okunur? | `getLatency()` | `L241` |
| SR aralığı nasıl okunur? | `getSupportedSampleRates(deviceId)` | `L244–L245` |
| Buffer aralığı nasıl okunur? | `getBufferSizeRange(deviceId)` | `L246–L247` |
| Callback'de ne yasak? | lock/IO/allocate (guardrail türetmesi) | `k2-surucu/CLAUDE.md L18–L24` |

### §20.12 Sözlük tamamlama (K038.1)

| Terim | Tanım | Kanıt |
|-------|-------|-------|
| Render callback | Dönemsel veri üretimi | `L118–L133` |
| ASBD | Format tanım yapısı | `L94–L104` |
| Element | Bar üstü giriş/çıkış indeksi | `L89`, `L109` |
| Scope | Property yönü (input/output/global) | `L88`, `L108` |
| UID | Cihaz kalıcı kimliği | `L155` |
| Transport type | Bağlantı türü | `L156` |
| Nominal SR | Cihaz ana hızı | `L177` |
| BufferFrameSize | Kare cinsinden buffer | `L159` |
| HAL | Donanım soyutlama katmanı | `L32–L36` |
| AudioUnit | Temel işlev birimi | `L69` |
| IOProc | ⚠️ VERIFICATION REQUIRED | — |
| Hog mode | ⚠️ VERIFICATION REQUIRED | — |

### §20.13 Bu dosyanın sınırı

| Sınır | Açıklama |
|-------|----------|
| Yalnız CoreAudio | Diğer platform yolları kendi klasörlerinde |
| Sayı yok | Kaynakta olmayan hiçbir metrik üretilmedi |
| Kod yok | İddialar tasarım düzeyindedir; implementasyon `⚠️ VERIFICATION REQUIRED` |
| Ölçüm yok | "Gerçek" sütunları kaynak alıntısıdır |
| Taşıma yok | Dosya adı değişmedi |

---

## §21 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — CoreAudio HAL/AudioUnit/callback dosyası | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §19.1–§19.13 ek tablolar (platform karşılaştırma, callback yaşam döngüsü, ASBD, clock, hata/test matrisi, okuma sırası) | Vault Documentation Specialist |
| 2026-10-06 | 4.1.0 | İç bağlantılar diskteki gerçek dosyalara çevrildi (k041→k030, k042→k031, k043→k032, k044→k034, k048→k018, k050→k034) · §19 başlık çakışması giderildi (Ek Tablolar → §20, Değişiklik Geçmişi → §21) · §5.4'e 4. yasağı (türetme etiketiyle) eklendi · §20.1 karşılaştırma hücreleri yedekten doğrulandı · §15 kanıt 16 satır · açık konulara CoreMIDI/AVAudioEngine (`[UNKNOWN]`) ve k021↔k038 SSOT notu eklendi | Vault Documentation Specialist |
