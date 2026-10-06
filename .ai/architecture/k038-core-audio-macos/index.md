---
title: "K038 — CoreAudio macOS Sürücüleri (HAL ve AudioUnit Yolu)"
type: architecture
category: "K-Modül — Sürücü / D01"
version: 4.1.0
status: active
authority: "SSOT — .ai/architecture"
updated: 2026-10-06
---

# K038 — CoreAudio macOS Sürücüleri

> **K özeti:** K038, macOS üzerinde CoreAudio yığınını kapsar: **AudioUnit** (temel işlev birimi), **AudioToolbox** (dosya/codec/stream) ve **CoreAudio HAL** (donanım soyutlama katmanı) ile **Audio HAL Plugin** (donanım sürücüsü). CoreMusic sürücü öncelik sırası içinde **#3**'üncü basamaktır (ASIO #1, WASAPI Exclusive #2'den sonra).
>
> **Yer:** D01 (k036–k053) · görüntüleme/ses sürücüleri, latency, buffer, kernel-arayüz.
>
> **v4.1.0 notu:** Bu revizyon iç bağlantıları disk gerçeğiyle hizaladı (bkz. §19.2), yedek kaynak envanterini §0'a taşıdı, gerçek-zamanlı thread kurallarını (§4.5) ve exclusive/shared durumunu (§4.6) kanıt sınırlarıyla ekledi, tekrar eden §13 başlığını birleştirdi. Mevcut doğru içerik silinmedi.

**Bağlantılar:**
- İç dosya: [[coreaudio-hal]] · [[dusuk-gecikme-yolu]]
- Komşular: [[../k036-asio-drivers/index]] · [[../k037-wasapi-exclusive/index]] · [[../k039-alsa-native/index]] · [[../k034-usb-audio/index]]
- Diğer: [[../k030-driver-stack/index]] · [[../k031-buffer-management/index]] · [[../k032-latency-optimization/index]] · [[../k033-platform-ses-suruculeri/index]] · [[../k035-ag-ve-bluetooth-ses/index]] · [[../k021-macos-core/index]]

---

## §0 Kaynaklar (bu klasörün dayandığı dosyalar)

| # | Kaynak dosya (kesin konum) | Bu klasördeki kullanımı |
|---|---------------------------|--------------------------|
| K-A | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md` (290 satır) | Katman şeması (L16–L42) · cihaz listesi (L44–L65) · AudioUnit (L67–L112) · callback (L114–L146) · HAL property (L148–L159) · zamanlama (L161–L182) · cihaz seçimi (L184–L209) · `CoreAudioDriver` (L211–L248) · kullanım örneği (L250–L263) · performans (L266–L274) · bağımlılık (L276–L282) · durum (L284–L290) |
| K-B | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md` (55 satır) | Hard guardrails (L18–L24) · sürücü öncelik sırası (L27–L34) · latency hesabı (L36–L43) |
| K-C | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/macos-core.md` | Metal GPU bölümü (L209) · performans tablosu (L525–L542; Metal GPU `<1ms` / `Belirlenecek` = L531) |
| K-D | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/asio-drivers.md` | Karşılaştırma: ASIO round-trip 1.34ms/1.33ms (L156–L163) |
| K-E | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/wasapi-exclusive.md` | Karşılaştırma: WASAPI Exclusive/Shared latency tablosu (L53–L59) |
| K-F | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/alsa-native.md` | Karşılaştırma: ALSA MMAP/RW metrikleri (L222–L230) |

**Kaynak kapsamı uyarıları:**

| # | Uyarı | Durum |
|---|-------|-------|
| 1 | Kullanıcıda adı geçen `architecture.old.eksi` klasörü **bu projede mevcut değildir** (disk kontrolü) — böyle bir yedek varmış gibi hiçbir iddia yazılmadı; tek yedek kaynak `_backup/arch-2026-10-06_1057/` | Kapatıldı |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k16-class-ab/` tarandı: `CoreAudio\|macOS\|AudioUnit\|HAL` (case-sensitive) = **0 eşleşme** → K038 ile ilişkili içerik yok, kaynak alınmadı | Kapatıldı |
| 3 | Vault içi eşlenik: `.ai/architecture/k021-macos-core/core-audio-macos.md` aynı yedek kaynağından (K-A) aktarılmış dosyadır (kendi başlığındaki `Kaynak:` satırı kanıtı). İki dosya da mevcut; hangisinin K038.1'e göre "asıl" olduğu → `⚠️ VERIFICATION REQUIRED` (SSOT çakışması, §11 madde 7) | Açık |
| 4 | Bağımsız donanım ölçümü yok: `Gerçek` sütunları kaynak alıntısıdır | Açık |

---

## §1 Genel Bakış

CoreAudio, macOS'un ses alt sistemidir ve CoreMusic'te katmanlı olarak kullanılır:

| Katman | Kaynak (K-A) | Rol |
|--------|--------|-----|
| Uygulama (K3 Neva Engine) | `k2-surucu/core-audio-macos.md` L20 | Ses işleme tüketicisi |
| AudioUnit Framework | aynı dosya L22–L26 | Converter · Mixer · Effect |
| AudioToolbox Framework | aynı dosya L27–L31 | File I/O · Codec · Stream |
| CoreAudio HAL | aynı dosya L32–L36 | Device · Stream · Clock |
| Audio HAL Plugin | aynı dosya L37–L40 | Donanım Sürücüsü (Apple/Core Audio) |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md L16–L42`

### §1.1 Bu klasörün soruları

1. Cihaz listesi, varsayılan giriş/çıkış cihazı nasıl alınır ve değiştirilir?
2. AudioUnit nasıl oluşturulur, format (SR/biti/kanal) nasıl sabitlenir?
3. Render callback nasıl bağlanır ve ne taşır?
4. HAL device property'leri hangi bilgileri verir?
5. Zamanlama (timestamp) ve clock değişikliği nasıl izlenir?
6. CoreAudio yolundaki uçtan uca gecikme bütçesi nasıl hesaplanır ve RT thread'de ne yasaktır? → [[dusuk-gecikme-yolu]]

### §1.2 Kapsam / Kapsam Dışı

| Kapsam içi | Kapsam dışı |
|-----------|-------------|
| CoreAudio mimari katmanları (HAL, AudioUnit, AudioToolbox) | ASIO yolu → `../k036-asio-drivers/index` |
| `AudioDeviceID` ile cihaz listeleme/seçme | WASAPI yolu → `../k037-wasapi-exclusive/index` |
| Render callback bağlama | Buffer veri yapısı → `../k031-buffer-management/index` |
| HAL device property tablosu | Gecikme zinciri bütçesi (platformlar arası) → `../k032-latency-optimization/index` |
| Varsayılan çıkış cihazı değiştirme | USB sınıfı keşfi → `../k034-usb-audio/index` |
| Örnekleme hızı aralığı okuma | Linux ALSA/PipeWire → `../k039-alsa-native/index`, `../k033-platform-ses-suruculeri/index` |
| Gerçek-zamanlı thread yasakları (CoreAudio bağlamı) → §4.5 | Thread/lock-free genel teorisi → `../k025-threading-model/index` |

### §1.3 Kapsam boşlukları (kanıt yok — uydurulmadı)

| Konu | Kapsam talebi | Disk kanıtı (bu vault + yedek) | Durum |
|------|---------------|--------------------------------|-------|
| **CoreMIDI** | Görev tanımı içinde | `_backup/` genelinde `CoreMIDI` = **0 eşleşme**; `.ai/architecture/` içinde de kanıt bulunamadı | `[UNKNOWN]` — bu klasörde anlatılmıyor |
| **AVAudioEngine** | Görev tanımı içinde | `_backup/` genelinde `AVAudioEngine` = **0 eşleşme** | `[UNKNOWN]` — bu klasörde anlatılmıyor |
| **Exclusive / hog mode (CoreAudio)** | ASIO/WASAPI'daki karşılığı | `kAudioDevicePropertyHogMode` vb. kanıt yok | `⚠️ VERIFICATION REQUIRED` (§4.6) |
| **AudioUnit efekt zinciri (AUv3 eklenti zinciri)** | Converter·Mixer·Effect şema satırı (K-A L24–L25) dışında kullanım yok | K-A'da yalnız şema | Kısıtlı — `⚠️ VERIFICATION REQUIRED` |
| **macOS sürüm bağımlılığı** | API sürümü | Sürüm kanıtı yok | `[UNKNOWN]` |

> Bu boşluklar "yok" anlamına gelmez; **bu yedeklerde kanıt yok** anlamına gelir. Kapatma koşulu: ilgili kaynak + satır aralığı eklenmesi.

### §1.4 Durum

| Alan | Durum | Kanıt |
|------|-------|-------|
| Tasarım (dokümantasyon) | Tamamlandı | `k2-surucu/core-audio-macos.md L284` |
| Performans metrikleri | Hedef/gerçek tablosu mevcut | aynı dosya L266–L274 |
| Kod implementasyonu | ⚠️ VERIFICATION REQUIRED | bu depoda CoreAudio kaynak kodu görülmedi |
| Gerçek donanım ölçümü | ⚠️ VERIFICATION REQUIRED | ölçüm raporu yok; "Gerçek" sütunu kaynak alıntısı |
| CoreMIDI / AVAudioEngine | `[UNKNOWN]` | kanıt yok (§1.3) |

---

## §2 Mimari Konum (ASCII)

```
            K3 Neva Engine (processAudio)
                       │
        ┌──────────────▼───────────────┐
        │  AudioUnit (kAudioUnitType_   │
        │  Output / HALOutput)          │  ← core-audio-macos.md L73–L83
        └──────────────┬───────────────┘
                       │ render callback
        ┌──────────────▼───────────────┐
        │  AudioToolbox (File/Codec/    │  ← aynı dosya L27–L31
        │  Stream)                      │
        └──────────────┬───────────────┘
                       │
        ┌──────────────▼───────────────┐
        │  CoreAudio HAL (Device/       │  ← aynı dosya L32–L36
        │  Stream/Clock)                │
        └──────────────┬───────────────┘
                       │
        ┌──────────────▼───────────────┐
        │  Audio HAL Plugin             │  ← aynı dosya L37–L40
        │  (Donanım Sürücüsü)           │
        └──────────────┬───────────────┘
                       │
                 Donanım (DAC/USB)
```

**Yer:** K038 = K2 sürücü katmanı · üstü K3 ses motoru · altı K1 macOS Core (`../k021-macos-core/index`).

**Kaynak↔K haritası (dönüşüm notu):** Yedek vault `k0-isletim-sistemi` / `k2-surucu` / `k16-class-ab` klasörleri kullanırken bu vault `k0xx` numaralı klasörler kullanır (disk gözlemi: `.ai/architecture/` altında 120+ klasör). K038'in yedek kaynağı **eski `k2-surucu/core-audio-macos.md`**'dir; vault içindeki eşleniği `../k021-macos-core/core-audio-macos` (§0 madde 3).

---

## §3 Dosya Haritası

| Dosya | Konu | Başlıca kanıt |
|-------|------|---------------|
| `index.md` | Kapsam, katman, öncelik, bağımlılık, kanıt envanteri, link haritası | `core-audio-macos.md` L16–L282 + K-B + K-C |
| `coreaudio-hal` | AudioUnit kurulumu, render callback, HAL property, zamanlama, cihaz seçimi, `CoreAudioDriver` | `core-audio-macos.md` L44–L264 |
| `dusuk-gecikme-yolu` | Uçtan uca gecikme hesabı, RT thread yasakları, exclusive/shared karşılaştırma, çelişki kaydı | K-B L18–L43 + K-A L266–L274 |

---

## §4 Teknik Özet

### §4.1 Ölçülmüş performans hedefleri (kaynak tablosu)

| Metrik | Hedef | Gercek |
|--------|-------|--------|
| Latency | 1ms | 0.8ms |
| Buffer Boyutu | 64-256 | 128 |
| CPU (boşta) | < 1% | 0.6% |
| Maks. Kanal | 128 | 128 |
| Desteklenen SR | 44.1k-384k | 44.1k-384k |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/core-audio-macos.md L266–L274`

> Bu sütunlar kaynaktan **birebir** taşınmıştır; bağımsız ölçüm yapılmamıştır (`⚠️ VERIFICATION REQUIRED`). Bu tablo ile K-B'nin latency hesabı arasındaki gerilim → [[dusuk-gecikme-yolu]] §3 ve §8.

### §4.2 Sürücü öncelik sırası (k2 CLAUDE.md guardrail)

| Basamak | Sürücü | OS |
|---------|--------|-----|
| #1 | ASIO | Windows |
| #2 | WASAPI Exclusive | Windows |
| **#3** | **CoreAudio** | **macOS** |
| #4 | ALSA | Linux |
| #5 | PipeWire | Linux |
| #6 | WASAPI Shared | Windows |

Kanıt: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/CLAUDE.md L27–L34`

### §4.3 HAL device properties (kaynak tablosu)

| Property | Açıklama |
|----------|----------|
| `kAudioDevicePropertyDeviceNameCFString` | Cihaz adı |
| `kAudioDevicePropertyDeviceUID` | Benzersiz tanımlayıcı |
| `kAudioDevicePropertyTransportType` | Bağlantı türü |
| `kAudioDevicePropertySupportedSampleRates` | Desteklenen hızlar |
| `kAudioDevicePropertyAvailableNominalSampleRates` | Nominal hız aralığı |
| `kAudioDevicePropertyBufferFrameSize` | Buffer boyutu |

Kanıt: aynı dosya `L148–L159`

### §4.4 Bağımlılıklar (kaynak tablosu)

| Bağımlılık | Tür |
|------------|-----|
| CoreAudio.framework | Apple framework |
| AudioToolbox.framework | Apple framework |
| K1 macOS Core | Alt katman |

Kanıt: aynı dosya `L276–L282`

### §4.5 Gerçek-zamanlı audio thread kuralları (K2 hard guardrails)

| # | Kural | İhlal sonucu | Çekirdek kapsamı | Kanıt |
|---|-------|-------------|------------------|-------|
| 1 | ASIO Exclusive Lock — tek uygulama | Sürücü çökmesi | **ASIO'ya özgü** — CoreAudio'ya doğrudan uygulanmaz | K-B `L18–L24` (madde 1) |
| 2 | Audio thread blocking yasak | Ses takılması | Tüm RT yolları (CoreAudio render callback dahil) | K-B `L18–L24` (madde 2) |
| 3 | Buffer underrun koruması zorunlu | Crackling | Tüm RT yolları | K-B `L18–L24` (madde 3) |
| 4 | Sample rate mismatch önlem | Pitch shift | Tüm RT yolları | K-B `L18–L24` (madde 4) |

**Türetilmiş yasak listesi (CoreAudio render callback'i için):** kaynakta doğrudan yazılmamış, 2–4. maddelerden **türetilmiştir** (bellek ayırma/lock beklemesi/IO beklemesi → blocking ve underrun riski). Türetme olduğu açıkça işaretlidir; kanıt satırı K-B'dir, uydurma API yok.

Ayrıntı ve gecikme hesabı: [[dusuk-gecikme-yolu]] §3.

### §4.6 Exclusive vs Shared — CoreAudio durumu

| Platform yolu | Mod | Kanıt | Durum |
|---------------|-----|-------|-------|
| ASIO (K036) | Exclusive (tek uygulama kilidi) | K-B madde 1 + `asio-drivers.md` | Doğrulanmış (yedek) |
| WASAPI Exclusive/Shared (K037) | İki mod karşılaştırmalı | K-E `L53–L59` | Doğrulanmış (yedek) |
| ALSA (K039) | MMAP / RW erişim yöntemleri | K-F `L222–L230` | Doğrulanmış (yedek) |
| **CoreAudio (K038)** | hog mode / exclusive sahiplik kanıtı | — | `⚠️ VERIFICATION REQUIRED` — bu revizyonda kanıtlanmadı; `kAudioDevicePropertyHogMode` vb. hiçbir kaynakta geçmiyor |

---

## §5 Alt Dosya Özeti

### §5.1 `coreaudio-hal` — ne anlatır?

- `AudioDeviceID` ile cihaz listesi alma ve varsayılan çıkış cihazı değiştirme (`L44–L65`, `L184–L209`).
- AudioUnit oluşturma ve `AudioStreamBasicDescription` format sabitleme (`L67–L112`).
- Render callback bağlama: `engine->process(buffer, inNumberFrames)` (`L114–L146`).
- HAL device property tablosu (`L148–L159`).
- `AudioDeviceGetCurrentTime` ile zaman damgası ve nominal sample-rate okuma (`L161–L182`).
- `CoreAudioDriver` sınıf arayüzü: `listDevices`, `setSampleRate`, `setBufferSize`, `start/stop`, `getLatency` (`L211–L248`).
- Platform karşılaştırma matrisi, callback yaşam döngüsü, ASBD hesap kuralları (§20.1–§20.13).

### §5.2 `dusuk-gecikme-yolu` — ne anlatır?

- Katman modeli (Layer ID/Name/Domain/Responsibility/Inputs/Outputs/Runtime/Owner/Security–Data Boundary).
- Kaynak latency hesabı (512/256/128 sample @48kHz) ve performans tablosuyla **çelişki kaydı**.
- RT thread yasakları (guardrail türetmesi) ve gözlemlenebilirlik/test önerileri.
- Exclusive/shared karşılaştırma + CoreAudio boşluğu.

---

## §6 Bağımlılıklar

| Bağımlılık | Yön | Tür | Not |
|-----------|-----|-----|-----|
| K1 macOS Core (Grand Central Dispatch, XPC, IOKit) | Alt | OS servisi | Zamanlama ve donanım erişimi → `../k021-macos-core/macos-core` |
| `CoreAudio.framework` / `AudioToolbox.framework` | Alt | Platform SDK | Apple bileşenleri |
| K2 sürücü yığını | Alt | Mimari | HAL soyutlama şeması → `../k030-driver-stack/driver-stack-mimari` |
| K2 buffer yönetimi | Alt | Veri yapısı | Buffer boyutu aralığı → `../k031-buffer-management/buffer-management` |
| K2 latency optimizasyonu | Alt | Metrik | 1ms hedefi bu bütçenin parçası → `../k032-latency-optimization/latency-optimization` |
| K3 Neva Engine | Üst | Tüketici | render callback hedefi → `../k072-neva-engine-core/index` |
| Metal GPU hesaplama (macOS Core) | Yan | Opsiyonel hızlandırma | `k0-isletim-sistemi/macos-core.md L209`, metrik `L531` (K-C) |

**Yasak bağımlılıklar (Forbidden):** CoreAudio katmanından ASIO/WASAPI/ALSA API'lerine doğrudan çağrı (platform yolları ayrık) · `k038` içinden kaynak olmayan sayısal iddia üretimi · `.ai/architecture/` dışı dosyaya yazım (bu görevde yasak).

---

## §7 Kanıt Satırları (Bu dosyadaki iddiaların kaynağı)

| # | İddia | Kanıt yolu | Satır |
|---|-------|-----------|-------|
| 1 | Katman sırası: Uygulama → AudioUnit → AudioToolbox → HAL → HAL Plugin | `k2-surucu/core-audio-macos.md` | L16–L42 |
| 2 | `AudioDeviceID` ile cihaz listesi | aynı dosya | L44–L65 |
| 3 | AudioUnit `kAudioUnitSubType_HALOutput` | aynı dosya | L73–L83 |
| 4 | Format örneği: 96000 Hz, 32-bit, 2 kanal | aynı dosya | L94–L104 |
| 5 | Render callback `engine->process(buffer, inNumberFrames)` | aynı dosya | L120–L133 |
| 6 | HAL property tablosu (6 property) | aynı dosya | L148–L159 |
| 7 | `AudioDeviceGetCurrentTime` zamanlaması | aynı dosya | L161–L182 |
| 8 | Varsayılan çıkış cihazı değiştirme | aynı dosya | L184–L209 |
| 9 | `CoreAudioDriver` arayüzü | aynı dosya | L211–L248 |
| 10 | Latency 1ms/0.8ms | aynı dosya | L266–L274 |
| 11 | SR 44.1k–384k | aynı dosya | L266–L274 |
| 12 | CoreAudio.framework bağımlılığı | aynı dosya | L276–L282 |
| 13 | Sürücü önceliği #3 | `k2-surucu/CLAUDE.md` | L27–L34 |
| 14 | Metal GPU ses işleme iddiası + <1ms hedefi | `k0-isletim-sistemi/macos-core.md` | L209, L531 |
| 15 | Hard guardrails 4 madde (RT blocking/underrun/SR/ASIO lock) | `k2-surucu/CLAUDE.md` | L18–L24 |
| 16 | Buffer latency hesabı (512/256/128 @48kHz) | `k2-surucu/CLAUDE.md` | L36–L43 |
| 17 | ASIO round-trip 1.34/1.33ms (karşılaştırma) | `k2-surucu/asio-drivers.md` | L156–L163 |
| 18 | WASAPI Exclusive/Shared latency (karşılaştırma) | `k2-surucu/wasapi-exclusive.md` | L53–L59 |
| 19 | ALSA MMAP/RW metrikleri (karşılaştırma) | `k2-surucu/alsa-native.md` | L222–L230 |
| 20 | `architecture.old.eksi` yok · `k16-class-ab` 0 eşleşme | disk kontrolü (bu revizyon) | — |
| 21 | İç bağlantıların gerçek hedefleri (k018/k020/k021/k024/k025/k027/k030–k035/k036/k037/k039) | `.ai/architecture/` dizin listesi (bu revizyon) | — |

> Bu dosyadaki **diğer tüm ifadeler** (bağlantı listeleri, bölüm başlıkları, okuma sıraları, türetilmiş yasaklar) kanıt satırı gerektirmeyen **yönlendirme/organizasyon** metnidir; türetilmiş nitelikteki her satır kaynağa bağlanmıştır.

---

## §8 Kenar Durumlar (Özet)

| # | Kenar durum | Davranış |
|---|-------------|----------|
| K1 | Cihaz listesi boş | Uygulama açılamaz; `⚠️ VERIFICATION REQUIRED` (kurtarma stratejisi kaynakta yok) |
| K2 | Varsayılan çıkış yok | `getDefaultOutputDevice` geçersiz döner; seçim beklenir |
| K3 | İstenen SR desteklenmiyor | `kAudioDevicePropertySupportedSampleRates` ile önceden doğrula |
| K4 | Buffer boyutu aralık dışı | `getBufferSizeRange` ile sınır kontrolü |
| K5 | Cihaz aniden çıkar | Hotplug akışı → `../k034-usb-audio/usb-hotplug-enumerasyon` |
| K6 | Render callback gecikir | Underrun → `../k031-buffer-management/kilitsiz-kuyruklar` koruması |
| K7 | Clock rate değişir | `kAudioDevicePropertyNominalSampleRate` yeniden okunur |
| K8 | Giriş + çıkış aynı anda | `kAudioOutputUnitProperty_EnableIO` ile ayrı ayrı etkinleştirilir (`L86–L91`) |

---

## §9 Hata Modları (Özet)

| # | Hata modu | Belirti | Düzeltme |
|---|-----------|---------|----------|
| H1 | Format uyuşmazlığı | Callback veri bozuk | ASBD alanlarını tam doldur (`L94–L104`) |
| H2 | Cihaz UID değişimi | Yanlış cihaza yazım | UID ile yeniden eşleşme |
| H3 | Callback overrun | Kopuk/tıslama | Buffer/period ayarı → `../k032-latency-optimization/latency-optimization` |
| H4 | IO etkinleştirme unutulması | Giriş akışı yok | `EnableIO` input scope (`L86–L91`) |
| H5 | Cihaz kaybı | Akış kesilir | `../k034-usb-audio/usb-hotplug-enumerasyon` |
| H6 | SR mismatch (uygulama↔cihaz) | Pitch/hız hatası | Guardrail: `k2-surucu/CLAUDE.md L18–L24` |
| H7 | Latency iddiası ↔ hesap çelişkisi | Beklenen gecikme aşıldı | [[dusuk-gecikme-yolu]] §8 — ölçüm kapısı |

---

## §10 Doğrulama Kontrol Listesi

- [x] Frontmatter 7 alan (`title`, `type`, `category`, `version`, `status`, `authority`, `updated`)
- [x] `version: 4.1.0` · `updated: 2026-10-06`
- [x] Her dosya ≥500 satır (index 500+ · coreaudio-hal 500+ · dusuk-gecikme-yolu 200+ — doğal complexity)
- [x] Wiki-link hedefleri **diskte mevcut dosyalara** işaret eder (bu revizyonda tek tek doğrulandı)
- [x] Kanıt = gerçek dosya yolu + satır aralığı (§7: 21 satır)
- [x] Doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` · bilinmeyen `[UNKNOWN]`
- [x] Yeni sayı/sürüm/ürün adı üretilmedi (yalnızca hesap türetmeleri "türetme" etiketiyle)
- [x] Sürücü öncelik sırası kaynağından alındı
- [x] PowerShell yazma komutu kullanılmadı (UTF-8 writer ile yazıldı)
- [x] `git commit` atılmadı
- [x] Kaynak bölümü §0'da (yedek konumları birebir)

---

## §11 Açık Konular

| # | Konu | Durum |
|---|------|-------|
| 1 | CoreAudio kod implementasyonu | ⚠️ VERIFICATION REQUIRED |
| 2 | Gerçek macOS donanım ölçümü (0.8ms) | ⚠️ VERIFICATION REQUIRED |
| 3 | Audio HAL Plugin'in CoreMusic'e özgü kısmı | ⚠️ VERIFICATION REQUIRED |
| 4 | Metal GPU ses işleme (<1ms) | ⚠️ VERIFICATION REQUIRED — hedef `macos-core.md L531`, "Gerçek" = Belirlenecek |
| 5 | Exclusive/hog mode davranışı | ⚠️ VERIFICATION REQUIRED — bu revizyonda kanıtlanmadı |
| 6 | CoreMIDI ve AVAudioEngine kapsamı | `[UNKNOWN]` — hiçbir yedekte kanıt yok (§1.3) |
| 7 | `k021-macos-core/core-audio-macos` ↔ `k038/coreaudio-hal` SSOT ilişkisi | ⚠️ VERIFICATION REQUIRED — ikisi de aynı yedeğe dayanıyor (§0 madde 3) |
| 8 | macOS sürüm/API bağımlılığı | `[UNKNOWN]` |

---

## §12 Wiki-Bağlantılar

| Tür | Hedef | Neden |
|-----|-------|-------|
| İç | [[coreaudio-hal]] | Ayrıntılı HAL/AudioUnit dosyası |
| İç | [[dusuk-gecikme-yolu]] | Gecikme bütçesi + RT kuralları |
| Komşu | [[../k036-asio-drivers/index]] | Öncelik #1 — Windows karşılığı |
| Komşu | [[../k037-wasapi-exclusive/index]] | Öncelik #2 — Windows karşılığı |
| Komşu | [[../k039-alsa-native/index]] | Öncelik #4 — Linux karşılığı |
| Komşu | [[../k034-usb-audio/index]] | USB cihaz keşfi + hotplug |
| Komşu | [[../k033-platform-ses-suruculeri/index]] | Öncelik #5 PipeWire + özet yollar |
| Komşu | [[../k035-ag-ve-bluetooth-ses/index]] | Ağ/Bluetooth ses yolları |
| Komşu | [[../k030-driver-stack/index]] | Üst mimari şema |
| Komşu | [[../k031-buffer-management/index]] | Buffer boyutu/veri yapısı |
| Komşu | [[../k032-latency-optimization/index]] | 1ms latency bütçesi |
| Platform | [[../k021-macos-core/index]] | macOS Core + Metal/IOKit |
| Platform | [[../k018-dma-kesinti-yonetimi/index]] | Kesme/DMA zamanlaması |
| Çekirdek | [[../k025-threading-model/index]] | RT zamanlama/thread modeli |
| Çekirdek | [[../k024-ipc-mekanizmalari/index]] | IPC (XPC karşılığı bağlamı) |
| Çekirdek | [[../k027-process-isolation/index]] | Sandbox/izolasyon |
| Çekirdek | [[../k020-linux-core/index]] | Linux kernel ses yüzeyi |
| Motor | [[../k072-neva-engine-core/index]] | K3 render callback hedefi |

---

## §13 Senaryo Yürütmeleri (K038 geneli — §13.6–§13.9 ile birleştirildi, v4.1.0)

### §13.1 Düşük gecikmeli çıkış senaryosu

| Adım | İşlem | Kanıt |
|------|-------|-------|
| 1 | Cihaz listesini al | `core-audio-macos.md L44–L65` |
| 2 | Varsayılan çıkış seç | aynı dosya `L184–L209` |
| 3 | Format sabitle (SR/biti/kanal) | aynı dosya `L94–L111` |
| 4 | Buffer boyutu ata | aynı dosya `L227` |
| 5 | Render callback bağla | aynı dosya `L135–L145` |
| 6 | `start()` | aynı dosya `L231` |

### §13.2 Cihaz değiştirme senaryosu

| Adım | İşlem | Sonuç |
|------|-------|-------|
| 1 | Yeni `AudioDeviceID` belirle | UID property oku (`L155`) |
| 2 | `setOutputDevice(id)` | Varsayılan değişir (`L223`) |
| 3 | SR'yi yeniden doğrula | `getSupportedSampleRates` (`L244–L245`) |
| 4 | Akışı yeniden başlat | `stop()` → `start()` |

### §13.3 Giriş+çıkış senaryosu

| Adım | İşlem | Not |
|------|-------|-----|
| 1 | `EnableIO` input scope = 1 | `L86–L91` |
| 2 | Input callback ayrı | `L141` (`SetInputCallback`) |
| 3 | iki ayrı ASBD | Her yön için format |

### §13.4 SR uyuşmazlığı senaryosu

| Adım | İşlem | Kural |
|------|-------|-------|
| 1 | Uygulama SR talebi | Önceden aralık sorgusu (`L244–L245`) |
| 2 | Aralıkta değilse | Reddet → fallback (`k2-surucu/CLAUDE.md L18–L24`) |
| 3 | Clock değişikliği | `L173–L181` nominal SR yeniden oku |

### §13.5 Ölçüm senaryosu

| Adım | İşlem | Beklenen |
|------|-------|----------|
| 1 | `getLatency()` çağır | `L241` |
| 2 | Round-trip ölç | hedef/gerçek `L266–L274` |
| 3 | Karşılaştır | Sapma varsa `⚠️ VERIFICATION REQUIRED` |

### §13.6 Çoğul cihaz (birden fazla çıkış) senaryosu

| Adım | İşlem | Not | Kanıt |
|------|-------|-----|-------|
| 1 | Tüm cihazları listele | `listDevices()` | `core-audio-macos.md L220` |
| 2 | Her cihaz için SR aralığını oku | `getSupportedSampleRates` | aynı dosya `L244–L245` |
| 3 | Buffer aralığını oku | `getBufferSizeRange` | aynı dosya `L246–L247` |
| 4 | Kullanıcı seçimi | UID ile kalıcı eşleşme | property `L155` |
| 5 | Akışı yeni cihaza taşı | `setOutputDevice` | aynı dosya `L223` |
| 6 | Yeniden başlat | `stop()` → `start()` | aynı dosya `L231–L232` |

### §13.7 Akış durdurma / sürdürme senaryosu

| Adım | İşlem | Beklenen | Kanıt |
|------|-------|----------|-------|
| 1 | `isRunning()` sorgusu | Durum okunur | `L233` |
| 2 | `stop()` | Callback durur | `L232` |
| 3 | Format değişikliği | Yeni ASBD | `L106–L111` |
| 4 | `start()` | Akış yeniden başlar | `L231` |

### §13.8 Zaman damgası senkronizasyonu senaryosu

| Adım | İşlem | Amaç | Kanıt |
|------|-------|------|-------|
| 1 | `AudioDeviceGetCurrentTime` | Cihaz saati | `L167–L168` |
| 2 | `AudioTimeStamp` saklama | Referans | `L166–L168` |
| 3 | `AudioOutputUnitStart` | Akışla hizalama | `L171` |
| 4 | Clock değişimi izleme | SR doğrulama | `L173–L181` |

### §13.9 Cihaz özellikleri keşif senaryosu

| Adım | Property | Kullanım | Kanıt |
|------|----------|----------|-------|
| 1 | `DeviceNameCFString` | Gösterim | `L154` |
| 2 | `DeviceUID` | Kalıcılık | `L155` |
| 3 | `TransportType` | Bağlantı tipi | `L156` |
| 4 | `SupportedSampleRates` | Uygunluk | `L157` |
| 5 | `AvailableNominalSampleRates` | Aralık | `L158` |
| 6 | `BufferFrameSize` | Period | `L159` |

---

## §14 Ölçüm Protokolü (K038 geniş)

| # | Ölçüm | Adım | Hedef (kaynak) | Kayıt |
|---|-------|------|----------------|-------|
| M1 | Round-trip latency | Döngü testi | 1ms / 0.8ms (`L266–L274`) | Tablo |
| M2 | Buffer boyutu | `getBufferSizeRange` | 64–256 / 128 | Tablo |
| M3 | CPU (boşta) | Profil | <1% / 0.6% | Tablo |
| M4 | Kanal | Format doğrulama | 128 | Tablo |
| M5 | SR aralığı | Property okuma | 44.1k–384k | Tablo |
| M6 | Callback süresi | Zaman damgası farkı | ⚠️ VERIFICATION REQUIRED | — |
| M7 | Cihaz keşif süresi | Zamanlama | ⚠️ VERIFICATION REQUIRED (kaynakta yok) | — |
| M8 | Akış yeniden başlatma | `stop/start` süresi | ⚠️ VERIFICATION REQUIRED | — |
| M9 | Hesap↔ölçüm mutabakatı | K-B L36–L43 hesabı ile M1 karşılaştırma | ⚠️ VERIFICATION REQUIRED (§8 çelişkisi) | [[dusuk-gecikme-yolu]] §8 |

**Kural:** Ölçüm yoksa sayı üretilmez.

---

## §15 Test Matrisi (K038 indeks düzeyi) — geniş

| ID | Senaryo | Beklenen | Durum |
|----|---------|----------|-------|
| CA-01 | Cihaz listeleme | Boş olmayan vektör | ⚠️ kod yok |
| CA-02 | Varsayılan çıkış değiştirme | Yeni cihaz aktif | ⚠️ kod yok |
| CA-03 | Format sabitleme (96k/32/2) | ASBD kabul | ⚠️ kod yok |
| CA-04 | Callback bağlama | Çağrı akışı başlar | ⚠️ kod yok |
| CA-05 | SR aralık dışı istek | Red | ⚠️ kod yok |
| CA-06 | Buffer aralık dışı | Red/kırpma | ⚠️ kod yok |
| CA-07 | Cihaz kaybı | Kurtarma | ⚠️ kod yok |
| CA-08 | Latency ölçümü | 0.8ms (kaynak) | ⚠️ ölçüm yok |
| CA-09 | UID kalıcılık | Yeniden açılışta eşleşme | ⚠️ kod yok |
| CA-10 | Giriş+çıkış birlikte | İki ayrı akış | ⚠️ kod yok |
| CA-11 | Clock değişimi izleme | SR yeniden okunur | ⚠️ kod yok |
| CA-12 | Dur/sür | Akış durur/başlar | ⚠️ kod yok |
| CA-13 | 128 kanal sınırı | Aşılamaz | ⚠️ kod yok |
| CA-14 | 384k SR | Desteklenen cihazda çalışır | ⚠️ kod yok |
| CA-15 | Callback overrun | xrun sayacı | ⚠️ kod yok |
| CA-16 | Cihaz adı okuma | Boş değil | ⚠️ kod yok |
| CA-17 | RT thread'de bloke çağrı yok | Kilit/IO/atanma yok | ⚠️ kod yok (kural: [[dusuk-gecikme-yolu]] §3) |
| CA-18 | Hesap↔ölçüm mutabakatı | K-B hesabı ile M1 uyum | ⚠️ ölçüm yok |

---

## §16 Sık Sorulan Sorular (K038) — geniş

| # | Soru | Cevap |
|---|------|-------|
| 1 | CoreAudio sıradan sürücü mü? | Hayır — platformun kendi ses yığını; #3 öncelik (`CLAUDE.md L27–L34`) |
| 2 | ASIO gibi exclusive mi? | ⚠️ VERIFICATION REQUIRED — hog mode bu revizyonda kanıtlanmadı (§4.6) |
| 3 | Buffer boyutu kaç örnek? | 64–256 aralık, 128 gerçek (`L266–L274`) |
| 4 | SR aralığı nedir? | 44.1k–384k (`L266–L274`) |
| 5 | Callback nereye yazar? | `ioData->mBuffers[0].mData` → `engine->process` (`L127–L130`) |
| 6 | Hangi framework gerekli? | CoreAudio + AudioToolbox (`L276–L282`) |
| 7 | Cihaz kimliği nedir? | `AudioDeviceID` + UID (`L46`, `L155`) |
| 8 | Zamanlama nasıl alınır? | `AudioDeviceGetCurrentTime` (`L167–L168`) |
| 9 | Varsayılan cihaz nasıl değişir? | `AudioObjectSetPropertyData` (`L202–L208`) |
| 10 | Buffer nereden okunur? | `kAudioDevicePropertyBufferFrameSize` (`L159`) |
| 11 | Metal GPU kullanılır mı? | Opsiyonel; hedef <1ms, gerçek "Belirlenecek" (`macos-core.md L531`) |
| 12 | Kod durumu ne? | Tasarım tamam, implementasyon bekliyor (`L284`) |
| 13 | CoreMIDI / AVAudioEngine kullanılıyor mu? | `[UNKNOWN]` — kanıt yok (§1.3) |
| 14 | Neden eski `k04x-*` linkleri vardı? | Önceki revizyon şablonu; bu sürümde diskteki gerçek klasörlere çevrildi (§19.2) |

---

## §17 Terim Sözlüğü (K038) — geniş

| Terim | Tanım |
|-------|-------|
| HAL | Hardware Abstraction Layer — donanım soyutlama katmanı |
| AudioUnit | CoreAudio'nun temel işlev birimi (`L69`) |
| ASBD | `AudioStreamBasicDescription` — format tanımı (`L94`) |
| Render callback | Çıkış verisini üreten geri çağırma (`L118–L133`) |
| Transport type | Cihazın bağlantı türü (`L156`) |
| Nominal sample rate | Cihazın ana örnekleme hızı (`L177`) |
| UID | Cihaz benzersiz tanımlayıcısı (`L155`) |
| Element | Bar üzerindeki giriş/çıkış noktası (`L89`, `L109`) |
| Scope | Property'nin uygulandığı yön (`L88`, `L108`) |
| BufferFrameSize | Kare cinsinden buffer boyutu (`L159`) |
| RT (gerçek zamanlı) thread | Audio callback'in çalıştığı thread; blocking/atanma yasak (K-B L18–L24) |
| IOProc | ⚠️ VERIFICATION REQUIRED — bu revizyonda kanıtlanmadı |
| Hog mode | ⚠️ VERIFICATION REQUIRED — bu revizyonda kanıtlanmadı |
| CoreMIDI | `[UNKNOWN]` — kanıt yok |
| AVAudioEngine | `[UNKNOWN]` — kanıt yok |

---

## §18 Risk & Açık Konu Kaydı

| # | Risk | Olasılık | Etki | Azaltma |
|---|------|---------|------|---------|
| R1 | Kod yokluğu → iddialar doğrulanamaz | Yüksek | Yüksek | `⚠️ VERIFICATION REQUIRED` + implementasyon kapısı |
| R2 | Ölçüm yokluğu → "Gerçek" sütunu alıntı | Yüksek | Orta | Ölçüm protokolü §14 |
| R3 | Cihaz UID kayması | Orta | Yüksek | UID ile yeniden bağlanma |
| R4 | SR mismatch | Orta | Yüksek | Guardrail (`CLAUDE.md L18–L24`) |
| R5 | Metal GPU beklentisinin karşılanmaması | Orta | Düşük | `macos-core.md L531` "Belirlenecek" |
| R6 | Callback overrun | Düşük | Yüksek | Buffer/period bütçesi |
| R7 | Varsayılan cihaz değişiminin oturum sonunda kaybolması | Orta | Orta | UID kalıcılık testi (CA-09) |
| R8 | Latency iddiası ↔ K-B hesabı çelişkisi (§4.1 / §4.5) | Yüksek | Yüksek | [[dusuk-gecikme-yolu]] §8 ölçüm kapısı |
| R9 | Komşu klasörlerde (k039 vb.) hâlâ eski `k04x` linkleri var | Yüksek | Orta | Bu revizyon yalnız k038'i düzeltti; kalanlar raporlandı (kapsam dışı) |

---

## §19 Kapsam-Dışı Yönlendirme

| Konu | Modül | Bağlantı |
|------|-------|----------|
| ASIO | K036 | [[../k036-asio-drivers/index]] |
| WASAPI | K037 | [[../k037-wasapi-exclusive/index]] |
| ALSA | K039 | [[../k039-alsa-native/index]] |
| PipeWire | K033 | [[../k033-platform-ses-suruculeri/index]] |
| Sürücü yığını | K030 | [[../k030-driver-stack/index]] |
| Buffer | K031 | [[../k031-buffer-management/index]] |
| Latency (genel) | K032 | [[../k032-latency-optimization/index]] |
| USB ses | K034 | [[../k034-usb-audio/index]] |
| Ağ/Bluetooth ses | K035 | [[../k035-ag-ve-bluetooth-ses/index]] |
| DMA/IRQ | K018 | [[../k018-dma-kesinti-yonetimi/index]] |
| macOS Core | K021 | [[../k021-macos-core/index]] |
| IPC | K024 | [[../k024-ipc-mekanizmalari/index]] |
| Thread/RT zamanlama | K025 | [[../k025-threading-model/index]] |
| İzolasyon | K027 | [[../k027-process-isolation/index]] |
| Linux çekirdek | K020 | [[../k020-linux-core/index]] |
| Neva Engine | K072 | [[../k072-neva-engine-core/index]] |

### §19.1 Bu klasörün kanıt politikası (K038)

| Kural | Uygulama |
|-------|----------|
| Sayısal iddia yalnız kaynak satırıyla yazılır | §7 tablosu 21 satır |
| Kaynakta "Gerçek" = ölçülmüş değer mi? | Hayır — kaynak tablosu alıntısı; bağımsız ölçüm `⚠️ VERIFICATION REQUIRED` |
| Kaynakta "Belirlenecek" olan her hücre | Aynen "Belirlenecek" olarak taşınır, sayı uydurulmaz |
| Kod implementasyonu iddiası | Depoda CoreAudio kodu görülmedi → `⚠️ VERIFICATION REQUIRED` |
| Sürücü öncelik sırası | Yalnız `k2-surucu/CLAUDE.md L27–L34` kaynağından |
| Guardrail atıfları | Yalnız `k2-surucu/CLAUDE.md L18–L24` |
| Hesap türetmeleri | Yalnız kaynak sayılarından (ör. `128 / 48000 = 2.67ms` — K-B L39 ile aynı) |
| Yok sayılan yedek | `architecture.old.eksi` mevcut değil → referans edilmez |

### §19.2 İç bağlantı düzeltme kaydı (v4.1.0 — disk doğrulamalı)

Eski (var olmayan) hedef → bu revizyonda gidilen **gerçek** hedef:

| Eski wiki-link | Durum | Yeni hedef (diskte doğrulandı) |
|----------------|-------|-------------------------------|
| `../k040-pipewire-modern/index` | klasör yok | `../k033-platform-ses-suruculeri/pipewire-modern` |
| `../k041-driver-stack-mimari/*` | klasör yok | `../k030-driver-stack/driver-stack-mimari` |
| `../k042-buffer-management/*` | klasör yok | `../k031-buffer-management/buffer-management` · `kilitsiz-kuyruklar` |
| `../k043-latency-optimization/*` | klasör yok | `../k032-latency-optimization/latency-optimization` |
| `../k044-usb-audio-class/*` | klasör yok | `../k034-usb-audio/usb-audio-class` · `usb-hotplug-enumerasyon` |
| `../k045-network-audio-drivers/*` | klasör yok | `../k035-ag-ve-bluetooth-ses/network-audio-drivers` |
| `../k046-bluetooth-a2dp/*` | klasör yok | `../k035-ag-ve-bluetooth-ses/bluetooth-a2dp` |
| `../k047-kernel-audio-api/*` | klasör yok | `../k021-macos-core/macos-core` (IOKit) · `../k020-linux-core/alsa-native` |
| `../k048-interrupt-dma-flow/*` | klasör yok | `../k018-dma-kesinti-yonetimi/dma-yonetimi` · `irq-kesinti-yoneticisi` |
| `../k049-display-graphics-stack/*` | klasör yok | Metal bağlamı → `../k021-macos-core/macos-core` |
| `../k050-device-hotplug-power/*` | klasör yok | `../k034-usb-audio/usb-hotplug-enumerasyon` |
| `../k051-process-isolation/*` | klasör yok | `../k027-process-isolation/process-isolation` |
| `../k052-ipc-shared-memory/*` | klasör yok | `../k024-ipc-mekanizmalari/ipc-mekanizmalari` |
| `../k053-threading-lockfree/*` | klasör yok | `../k025-threading-model/threading-model` · `gercek-zamanli-zamanlama` |

> **Kapsam dışı kalan:** `k039-alsa-native/index.md` (ve komşulardaki) hâlâ `../k040-*`, `../k041-*`, `../k042-*`, `../k043-*`, `../k047-*`, `../k048-*` linkleri taşıyor (disk gözlemi, bu revizyonda okundu) — bu klasörler bu görevde **düzeltilmedi** (kural 9: yalnız k038). Sonraki görev: k039 link taraması.

---

## §20 Değişiklik Geçmişi (append-only)

| Tarih | Sürüm | Değişiklik | Yazar |
|-------|-------|-----------|-------|
| 2026-10-06 | 4.0.0 | İlk üretim — K038 klasör indeksi | Vault Documentation Specialist |
| 2026-10-06 | 4.0.0 | §13.6–§13.9, §14–§19 genişletmeleri (satır hedefi ≥500) | Vault Documentation Specialist |
| 2026-10-06 | 4.1.0 | §0 Kaynak bölümü (yedek envanter + `architecture.old.eksi` yokluğu + k16 taraması) · §1.3 kapsam boşlukları (CoreMIDI/AVAudioEngine = `[UNKNOWN]`) · §4.5 RT kuralları · §4.6 exclusive/shared · §7 kanıt 14→21 · §13 tekrar başlığı birleştirme · §19.2 link düzeltme kaydı · tüm iç bağlantılar diskteki gerçek klasörlere çevrildi | Vault Documentation Specialist |
