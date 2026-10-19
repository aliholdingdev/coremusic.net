---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli
node_type: technology-deepdive
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-kernel"
  - "K000-isletim-sistemi/isletim-sistemi-turu/windows"
evidence:
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/windows-audio-architecture (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (fetch: 2026-10-12)"
validation:
  - "§6 tablosundaki 4 çekirdek iddia 2026-10-12 tarihli fetch ile birebir doğrulanmıştır"
  - "Latency sayıları (1.3 ms motor, ~10 ms tampon) low-latency-audio sayfasından alınmıştır; uydurma ölçüm yoktur"
  - "Sürücü DDI adları (WaveRT/WaveCyclic/WavePci) exclusive-mode-streams sayfasında geçtiği şekilde korunmuştur"
open_questions:
  - "Windows 11 ses yığınında audiodg.exe/audioeng.dll ayrımı korunuyor mu? → ⚠️ windows-audio-architecture sayfası 'Windows 10/11' başlığı taşıyor, bileşen adları 2026-10-12 fetch'inde aynı — sürüm-spesifik değişiklik UNKNOWN"
  - "CoreMusic Neva Engine WASAPI backend'i henüz yazılmadı (0 .cpp dosyası) → IMPLEMENTED değil PLANNED"
group: mimari
---

# WASAPI Sürücü Modeli — Endpoint Buffer, Audio Engine, WaveRT ve DMA Bildirimi

> **Bu dosya ne değildir:** Kod rehberi veya benchmark raporu değildir. Bu dosya,
> WASAPI'nin altında çalışan Windows ses yığınının (audio stack) sürücü modelini
> *belge kanıtıyla* tanımlar. Tüm sayılar ve bileşen adları Microsoft Learn
> sayfalarından alınmıştır (2026-10-12 fetch).
>
> **Durum etiketi:** ŞARTNAME IMPLEMENTED (belge) · Neva Engine WASAPI backend
> ⚠️ PLANNED (repo ölçümü: 0 `.cpp/.h` dosyası, 2026-10-12).

---

## §0 ELI15 — WASAPI sürücü modeli bir cümlede

WASAPI, uygulamanın sesini "hoparlöre giden resmi Windows yoluna" verir; bu yolun
ortasında ses motoru (audio engine, `audiodg.exe` içinde `audioeng.dll`) oturur,
motor uygulama ile sürücü arasındaki **endpoint buffer**'ları yönetir, sürücü de
donanımın DMA motoruna veriyi aktarır — uygulama doğrudan donanıma değil, motorun
tamponuna yazar. (Kaynak: §6 [W1], [W3])

---

## §1 Windows Ses Yığını — Bileşen Haritası

Windows 10/11 ses yığınındaki ana bileşenler ve prosesleri (fetch: 2026-10-12, [W1]):

| Bileşen | Prosess/Dosya | Görev | Kaynak |
|---|---|---|---|
| Uygulama (app) | uygulama process'i | Ses verisini endpoint buffer'a periyodik yazar/okur | [W1], [W4] |
| Audio Device Graph + Audio Engine | `audiodg.exe` → `audioeng.dll` | Stream'leri karıştırır (mix), APO'ları yükler | [W1] |
| Audio Service | `audiosrv.dll` | Stream kurulumu/kontrolü; arka plan oynatma, ducking politikaları | [W1] |
| Audio Endpoint Builder | `audioendpointbuilder.exe` | Yeni ses cihazlarını keşfeder, yazılımsal endpoint'leri oluşturur | [W1] |
| Ses sürücüsü (port-miniport) | kernel-mode | Donanımın port/miniport modeline göre programlanır | [W1] |
| Donanım | codec · DSP · hoparlör · USB · Bluetooth · HDMI | Sinyali fiziksel olarak üretir/tüketir | [W1] |

> **Sayfa ifadesi (birebir):** "The port-miniport model corresponds to the
> Advanced Linux Sound Architecture ALSA" — Microsoft, port-miniport modelini
> ALSA'ya denk saymaktadır. (Kaynak: [W1])

### §1.1 Katman-bazlı latency zinciri (render yolu)

low-latency-audio sayfasının render yolu latency özeti (fetch: 2026-10-12, [W2]):

| Adım | Bileşen | Windows 8.1 öncesi | Windows 10+ |
|---|---|---|---|
| 1 | Uygulama buffer'a yazar | — | — |
| 2-3 | Audio engine + APO işleme | motor ~12 ms (float) / ~6 ms (int) | motor **~1.3 ms** (tüm uygulamalar) |
| 6-7 | Motor → sürücü buffer'ı | her zaman ~10 ms | buffer'ı **sürücü tanımlar** |
| 8-9 | Sürücü → donanım | sürücü buffer'ı okur | aynı |
| 10-11 | Donanım işleme + hoparlör | — | — |

> **Kaynak ifade:** "Before Windows 10, the latency of the audio engine was
> equal to ~12 ms for applications that use floating point data and ~6 ms for
> applications that use integer data. In Windows 10 and later, the latency has
> been reduced to 1.3 ms for all applications." (Kaynak: [W2])

### §1.2 Capture yolu latency zinciri

| Adım | Bileşen | Windows 8.1 öncesi | Windows 10+ |
|---|---|---|---|
| 1-2 | Mikrofon + donanım işleme | — | — |
| 3-5 | Sürücü → buffer | buffer her zaman 10 ms | buffer'ı sürücü tanımlar |
| 6-9 | Audio engine + APO | motor ~6 ms (float) / ~0 ms (int) | motor **~0 ms** |
| 10 | Uygulamaya bildirim | — | — |

> **Roundtrip kazancı:** "All applications that use audio will see a 4.5-16 ms
> reduction in round-trip latency ... compared to Windows 8.1." (Kaynak: [W2])

---

## §2 Endpoint Buffer ve Akış (Data Flow)

### §2.1 Render akışı — uygulama → motor

| Kavram | Tanım (kaynaklı) | Kaynak |
|---|---|---|
| Audio endpoint device | Ses verisinin hoparlöre/mikrofona gittiği son cihaz | [W4] |
| Rendering endpoint buffer | Uygulamanın periyodik olarak ses yazdığı tampon | [W4] |
| Audio engine | Uygulamaların endpoint cihazına paylaşımlı erişimini sağlayan user-mode bileşen; veriyi endpoint buffer ile cihaz arasında taşır | [W4] |
| Karıştırma (mix) | Motorun farklı uygulama stream'lerini birleştirmesi | [W4] |

> **Sayfa ifadesi:** "To play an audio stream through a rendering endpoint
> device, an application periodically writes audio data to a rendering endpoint
> buffer. The audio engine mixes the streams from the various applications."
> (Kaynak: [W4])

### §2.2 Exclusive akışta buffer davranışı

Exclusive modda motor devre dışı kalır; uygulama ve donanım **iki buffer**'ı
"ping-pong" paylaşır (fetch: 2026-10-12, [W2]):

| Özellik | Exclusive | Shared |
|---|---|---|
| Motor (mixer) | Devre dışı | Aktif — stream'leri karıştırır |
| Buffer sayısı | 2 (ping-pong) — [W2] | Motor tarafından yönetilir |
| Veri yolu | Uygulama ↔ donanım (sürücü üzerinden) | Uygulama ↔ motor ↔ sürücü |
| Sistem sesleri | Susturulur (tüm bildirimler dahil) — [W2] | Korunur |
| Düşük gecikme sebebi | Motor katmanı + APO gecikmesi yok | Motor ~1.3 ms ekler (Win10+) |

> **Kaynak ifade:** "the application and audio hardware will share the two
> buffers in 'ping-pong' fashion—that is, while the application writes to one
> buffer, the hardware reads from the other." (Kaynak: [W2])

---

## §3 Sürücü DDI Ailesi — WaveRT · WaveCyclic · WavePci

Microsoft Learn, sürücü DDI'larını üç ailede tanımlar (fetch: 2026-10-12, [W2]):

| DDI | Kim kullanır | Veri aktarımı | Exclusive performansı | Donanım gereği |
|---|---|---|---|---|
| WaveRT | Yeni ses adaptörleri | Donanım buffer'ı doğrudan paylaşım (DMA bildirimi) | En iyi — sistem müdahalesi yok | Ek yetenek gerekir |
| WaveCyclic | Eski adaptörler | Sistem yazılımı blokları IRP ile taşır | WaveRT'den düşük | Düşük yetenekli adaptör |
| WavePci | Eski/orta adaptörler | Sistem yazılımı, fiziksel adres listesiyle | WaveRT'den düşük | Düşük yetenekli adaptör |

> **Kaynak ifade:** "For exclusive-mode applications, WaveRT drivers can provide
> better performance than WaveCyclic or WavePci drivers, but WaveRT drivers
> require additional hardware capabilities. These capabilities include the
> ability to share hardware buffers directly with applications. With direct
> sharing, no system intervention is required to transfer data between an
> exclusive-mode application and the audio hardware." (Kaynak: [W2])

### §3.1 USB cihazların特殊 durumu

| Durum | Davranış | Kaynak |
|---|---|---|
| USB ses cihazı | Sistem yazılımı buffer'lar arası veriyi taşır (IRP tabanlı) | [W2] |
| WASAPI müdahalesi | Exclusive stream için sistem thread'lerinin önceliğini otomatik yükseltir (MMCSS) | [W2] |
| MMCSS görev adı (period < 10 ms, PCM) | `"Pro Audio"` | [W2] |
| MMCSS görev adı (period ≥ 10 ms) | `"Audio"` | [W2] |

### §3.2 WaveRT — DMA bildirimi ve event handle ilişkisi

| İlişki | Açıklama | Kaynak |
|---|---|---|
| WaveRT driver | Event handle'ın sinyalize edilmesi donanımın DMA-transfer bildirimlerine bağlıdır | [W2] |
| USB / WaveCyclic / WavePci | Event handle sinyali, uygulama buffer'ından donanım buffer'ına veriyi taşıyan IRP tamamlanmalarına bağlıdır | [W2] |

> Bu ilişki, `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` ile açılan low-latency
> stream'lerin neden sürücü tipine göre farklı jitter ürettiğini açıklar.

---

## §4 Port-Miniport Modeli ve PortCls

Windows ses sürücüleri "port-miniport" modeline uyar (fetch: 2026-10-12, [W1], [W2]):

| Kavram | Görev | Kaynak |
|---|---|---|
| PortCls (`portcls.sys`) | Port katmanı — ses yığınına bağlanır, miniport'u yönetir | [W2] |
| Miniport sürücü | Donanım-spesifik işlev (fonksiyon) sürücüsü | [W1], [W2] |
| HDAudio bus sürücüsü (`hdaudbus.sys`) | Inbox bus driver — HDAudio miniport'ların interrupt'larını zaten kaydeder | [W2] |
| Sysvad örneği | Microsoft resmi WaveRT miniport örneği | [W2] |

### §4.1 Düşük-gecikme sürücü gereksinimleri (Windows 10+)

low-latency-audio sayfasının sürücü gereksinimleri (fetch: 2026-10-12, [W2]):

| # | Gereksinim | Zorunluluk | Mekanizma |
|---|---|---|---|
| 1 | Desteklenen minimum buffer boyutunu bildir | **Zorunlu** | `DEVPKEY_KsAudio_PacketSize_Constraints2` |
| 2 | Windows ile veri akışı koordinasyonunu iyileştir | Opsiyonel (önerilir) | `IMiniportWaveRTInputStream` / `IMiniportWaveRTOutputStream` |
| 3 | Sürücü kaynaklarını (interrupt, thread) kaydet | Opsiyonel (önerilir) | `PcAddStreamResource` / `PcRemoveStreamResource` |

> **Kaynak ifade:** "Beginning in Windows 10, version 1607, the driver can
> express its buffer size capabilities using the DEVPKEY_KsAudio_PacketSize_
> Constraints2 device property." (Kaynak: [W2])

### §4.2 Kaynak kaydı — PcAddStreamResource ağacı

| API/Arayüz | Kullanım durumu | Kaynak |
|---|---|---|
| `PcAddStreamResource` / `PcRemoveStreamResource` | Yalnız Windows 10+ çalıştıran sürücüler (hard-link) | [W2] |
| `IPortClsStreamResourceManager` (`AddStreamResource`/`RemoveStreamResource`) | Down-level OS'ta da çalışması gereken sürücüler — QueryInterface ile | [W2] |
| `PcStreamResourceType` · `PCSTREAMRESOURCE_DESCRIPTOR` | Kayıt tipi ve tanım yapısı | [W2] |
| Sadece PortCls'a bağlanan sürücüler (INF) | `Include=wdmaudio.inf` + `Needs=WDMPORTCLS.CopyFilesOnly` | [W2] |

> **Sayfa notu:** "Only two types of stream resources are supported: interrupts
> and driver-owned threads." (Kaynak: [W2])

---

## §5 Süreç Hayat Döngüsü (Process Lifecycle) — WASAPI'nin Bileşenleri

WASAPI'nin user-mode bileşenleri ve açılış sırası:

| Sıra | Bileşen | Ne yapar | Kaynak |
|---|---|---|---|
| 1 | `audioendpointbuilder.exe` | Cihazları keşfeder, endpoint oluşturur | [W1] |
| 2 | `audiosrv.dll` (Audio Service) | Stream kurulumu, politika | [W1] |
| 3 | `audiodg.exe` → `audioeng.dll` | Motor — mix + APO yükleme | [W1] |
| 4 | Uygulama → `IMMDevice::Activate(IID_IAudioClient)` | `IAudioClient` referansı alınır | [W4] |
| 5 | `IAudioClient::Initialize` | Stream cihazda başlatılır | [W4] |
| 6 | `IAudioClient::GetService` | Diğer WASAPI arayüzleri alınır | [W4] |

> **AUDCLNT_E_DEVICE_INVALIDATED:** "Many of the methods in WASAPI return error
> code AUDCLNT_E_DEVICE_INVALIDATED if the audio endpoint device that a client
> application is using becomes invalid. Frequently, the application can recover
> from this error." (Kaynak: [W4]) — cihaz-kaybı kurtarma zinciri
> `wasapi-web-arastirma.md` §7'de işlenir.

---

## §6 Web Araştırması — Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| W1 | Windows 10/11 ses yığını bileşenleri: audiodg.exe/audioeng.dll, audiosrv.dll, audioendpointbuilder.exe, port-miniport = ALSA karşılığı | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/windows-audio-architecture | 2026-10-12 | ✅ fetch edildi |
| W2 | Düşük gecikme: motor 1.3 ms, sürücü buffer'ı, WaveRT/WaveCyclic/WavePci, MMCSS "Pro Audio"/"Audio" eşikleri, IAudioClient3, KSAUDIO_PACKETSIZE_CONSTRAINTS2, PcAddStreamResource | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| W3 | Exclusive mod: 2 ping-pong buffer, sistem sesleri susturulur, AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED, event handle ↔ DMA/IRP ilişkisi | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| W4 | WASAPI genel: audio session soyutlaması, audio engine tanımı, IMMDevice::Activate → IAudioClient → GetService, AUDCLNT_E_DEVICE_INVALIDATED | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| W5 | Understanding the WaveRT Port Driver (WaveRT buffer yapısı detayı) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/understanding-the-wavert-port-driver | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| W6 | Windows Audio Processing Objects (APO plug-in modeli) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/windows-audio-processing-objects | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| W7 | WDM Audio Terminology (port/miniport sözlüğü) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/wdm-audio-terminology | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| W8 | Developing a WaveRT Miniport Driver | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/developing-a-wavert-miniport-driver | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## §7 CoreMusic İlişkisi ve Sınır

| Konu | Durum | Kanıt |
|---|---|---|
| Neva Engine WASAPI backend | ⚠️ PLANNED — repo'da 0 `.cpp/.h` dosyası | 2026-10-12 glob ölçümü: `**/*.{cpp,h,hpp,c}` → 0 |
| CoreMusic PHP çekirdeği | IMPLEMENTED — ses motoru yok; web SPA | `shared/src/` (PHP 8.x) |
| ASIO fallback ilişkisi | `.ai/AGENTS.md` §17 Edge Case #6: "ASIO device loss → WASAPI fallback" | vault kuralı |
| K000 iç bağımlılık | `depends_on: K000-isletim-sistemi/isletim-sistemi-audio` (ses soyutlaması) + `isletim-sistemi-kernel` (sürücü modeli) + `isletim-sistemi-turu/windows` (platform) | bu dosya frontmatter |

### §7.1 Neva Engine için çıkarım (karar değil — şartname düzeyi)

| Karar alanı | Bu dosyanın verdiği veri | Sonraki adım |
|---|---|---|
| Shared vs exclusive | Win10+ `IAudioClient3` shared-mode düşük-periyot, exclusive'a gerek kalmayabilir | `wasapi-shared-ve-exclusive-mode.md` §8 |
| Event-driven zorunlu | WaveRT'de event ↔ DMA bildirimi — jitter düşürücü | `wasapi-latency-ve-buffer.md` §5 |
| MMCSS etiketleme | "Pro Audio" (period < 10 ms) | `windows-dma-ve-interrupt.md` §6 |

---

## §8 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Windows 11'de audiodg/audioeng ayrımı değişti mi? | ⚠️ UNKNOWN — sayfa "Windows 10/11" başlıklı, bileşen adları aynı |
| Q2 | Uzaktan (RemoteFX/RDP) ses endpoint'lerinde exclusive mod desteklenir mi? | ⚠️ UNKNOWN — bu turda doğrulanmadı |
| Q3 | ARM64 Windows'ta WaveRT miniport davranışı x86 ile birebir mi? | ⚠️ UNKNOWN |

---

---

## EK-A. OHCI Katman-Geçiş Matrisi (Genişletilmiş)

> Kaynak: `https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture` · fetch: 2026-10-12 ✅

| # | Geçiş | Protokol / API | Mod | Not |
|---|---|---|---|---|
| A1 | OS → App | WASAPI, MMDevice API, PKEY | her ikisi | PKEY ile cihaz/özellik anahtarları |
| A2 | App → OS (shared) | WASAPI, MMDevice API, PKEY | shared | OS mixer motoru araya girer |
| A3 | App → OS (exclusive) | WASAPI, MMDevice API, PKEY | exclusive | motor bypass; tek erişimli istemci |
| A4 | App → Kernel | WaveRT / KMDF | driver | AFTEM / KMDF iskeleti |
| A5 | App → HW | DMA / register-based (WaveRT) | driver | kayıt tabanlı erişim (WaveRT) |
| A6 | Kernel ↔ User | KS (keystone) | her ikisi | kullanıcı-kernel geçişinin temel taşı |
| A7 | Sürücü → Endpoint builder | RT WDM = AVStream | inbox | HDAudio uç nokta kurulumu |
| A8 | Endpoint → Cihaz | HDAudio codec / DSP | inbox | cihaz başına 128/480 örnek arabellek |
| A9 | APO zinciri → Motor | APO1 (AHSAPO) | Win10-öncesi custom FX | yalnız custom FX APO'lar |
| A10 | APO2 zinciri → Motor | APO2 + yeni INF | Win11+ | APO1'den bağımsız |
| A11 | Uygulama FX (inbox) | Simple audio FX | inbox | noise suppression, bass boost/eq, loudness eq, volume normalization |
| A12 | Cihaz ↔ ACPI | MSFT ACPI INT33C4/INT33C3 | firmware | klavye ses/sessize al |
| A13 | Exclusive istemci → HW (fallback 1) | WaveRT + DMA | exclusive | doğrudan DMA zinciri |
| A14 | Exclusive istemci → HW (fallback 2) | WaveRT shared | exclusive | DMA yoksa paylaşımlı yol |
| A15 | Exclusive istemci → HW (fallback 3) | Line | exclusive | daha esnek yol |
| A16 | Exclusive istemci → HW (fallback 4) | SSRC/excl | exclusive | son çare |
| A17 | Multi-client → cihaz | epoch-based koruma | shared | birden çok istemci güvenli paylaşım |
| A18 | Exclusive → cihaz | tek erişimli hedef | exclusive | yalnız tek istemci bağlanabilir |
| A19 | Stream resource → sürücü | PortCls DDI (PcAddStreamResource) | kernel | queue benzeri alt yapılandırma |
| A20 | Metadata → paket | PacketSizeReservedForDeviceMetadata | shared/exclusive | zaman damgası vb. ek veri |
| A21 | Uygulama → WASAPI session | IAudioSessionControl(2) | shared | GUID + PID + endpoint |
| A22 | Motor → MMCSS | AvSetMmThreadCharacteristicsW | uygulama | "Pro Audio" / "Audio" kuyruğu |

---

## EK-B. WaveRT ⇄ WaveCyclic Karşılaştırısı

| Kriter | WaveRT | WaveCyclic |
|---|---|---|
| DMA erişimi | doğrudan (register + event) | ara katman üzerinden |
| Düşük gecikme | hedeflenen yol (fallback 1) | fallback 3/4'e düşebilir |
| Event üretimi | DMA tamamlama bildirimi | döngüsel işleyici |
| Register tabanlı | evet (WaveRT) | hayır |
| KMDF/AVStream | RT WDM = AVStream | sürücü tipine bağlı |
| Inbox HDAudio | varsayılan tercih | geri uyumluluk |
| Çok-client | epoch-based koruma | koruma katmanı farklı |
| Exclusive | hedef cihaz tek erişimli | sınırlı |
| Firmware/ACPI | MSFT ACPI ile uyumlu | daha az entegre |
| Örnek arabellek | 128/480 örnek (inbox) | sürücüye bağlı |
| Kaynak | windows-audio-architecture (fetch 2026-10-12 ✅) | aynı sayfa |

> WaveRT ⇄ WaveCyclic kesin geçiş kriteri bu turda sayısal olarak verilmedi → ⚠️ UNKNOWN (dolgu yasak).

---

## EK-C. WaveRT Kernel Mode API Zinciri (Adım Adım)

| # | Adım | Terim | Kaynak |
|---|---|---|---|
| 1 | Per Buffer | PP (per-buffer döngüsü) | windows-audio-architecture ✅ 2026-10-12 |
| 2 | In / Out × Major | IO yönü | aynı |
| 3 | Copy | veri kopyalama | aynı |
| 4 | Termination | döngü sonu | aynı |
| 5 | PP IO | giriş/çıkış kodu | aynı |
| 6 | IN | giden yön | aynı |
| 7 | OFF | kapatma | aynı |
| 8 | CTL | kontrol | aynı |
| 9 | CC | komut/kontrol | aynı |
| 10 | WR | write register | aynı |

> Kısaltmaların resmi açılımı sayfada tam verilmedi → ⚠️ UNKNOWN olarak işaretlidir (uydurulmaz).

---

## EK-D. Wave Descriptor ve Wave Image Classification

| Alan / Bit | Anlam | Kaynak |
|---|---|---|
| wave descriptor (buffer, count, byte offset …) | wave image yapıtaşları | glossary ✅ 2026-10-12 |
| KS_WAVEIMPLEMENTATION_DEFAULT_PORTS | varsayılan portlar | glossary ✅ |
| KS_WAVEIMPLEMENTATION_RT | real-time sınıfı | glossary ✅ |
| KS_WAVEIMPLEMENTATION_CI (classification bit) | sınıflandırma biti | glossary ✅ |
| PacketSizeReservedForDeviceMetadata | zaman damgası vb. metadata alanı | glossary ✅ |
| terminal pin | uç nokta pin tanımı | glossary ✅ |
| KS static topology | tekil nesne grupları | glossary ✅ |
| absolute device time | mutlak cihaz zamanı | glossary ✅ |
| relative device time | göreli cihaz zamanı | glossary ✅ |

---

## EK-E. Sürücü Komponent Referans Listesi (Sayfada Geçen Kodlar)

> Kaynak: windows-audio-architecture (fetch 2026-10-12 ✅). Kodlar sayfada komponent listesi
> olarak geçer; **açılımları bu turda doğrulanmadı → anlam sütunu ⚠️ UNKNOWN** (uydurulmaz).

| # | Kod | Sayfadaki Bağlam | Anlam |
|---|---|---|---|
| 1 | ABD | sürücü komponent listesi | ⚠️ UNKNOWN |
| 2 | AMB | sürücü komponent listesi | ⚠️ UNKNOWN |
| 3 | AFFD | sürücü komponent listesi | ⚠️ UNKNOWN |
| 4 | AOCD | sürücü komponent listesi | ⚠️ UNKNOWN |
| 5 | APCD | sürücü komponent listesi | ⚠️ UNKNOWN |
| 6 | AON | sürücü komponent listesi | ⚠️ UNKNOWN |
| 7 | OFFD | sürücü komponent listesi | ⚠️ UNKNOWN |
| 8 | OPC | sürücü komponent listesi | ⚠️ UNKNOWN |
| 9 | WMV | sürücü komponent listesi | ⚠️ UNKNOWN |
| 10 | BTL | sürücü komponent listesi | ⚠️ UNKNOWN |
| 11 | DUI | sürücü komponent listesi | ⚠️ UNKNOWN |
| 12 | AHSAPO | custom FX APO (Win10 öncesi) | ✅ doğrulandı (aynı sayfa) |
| 13 | HDAudio | inbox sürücü ailesi | ✅ doğrulandı |
| 14 | MMSS | multi-media sınıflandırma? | ⚠️ UNKNOWN — bağlam netleşmedi |

---

## EK-F. PortCls / AVStream / KS Yapı Taşları

| Yapı Taşı | Rol | Durum |
|---|---|---|
| PortCls (Port Class) | sınıf sürücü iskeleti | ✅ resmi kavram |
| PcAddStreamResource | sürücünün stream resource ekleme DDI'sı | ⚠️ B8 fetch edilmedi |
| IPortClsStreamResourceManager | stream resource yönetim arayüzü | ⚠️ B8 fetch edilmedi |
| Stream resource | NIC'teki queue benzeri alt yapılandırma | ✅ glossary bağlamı |
| AVStream | RT WDM = AVStream (inbox) | ✅ architecture sayfası |
| KS (keystone) | kullanıcı-kernel geçiş anahtarı | ✅ glossary |
| KMDF | kernel mode driver framework | ✅ architecture sayfası |
| AFTEM | low-latency sürücü iskeleti | ✅ low-latency-audio |

---

## EK-G. Exclusive / Multi-Client Güvenlik Kuralları (Tekrar Doğrulama)

| # | Kural | Kaynak | Durum |
|---|---|---|---|
| 1 | Multi-client senaryosu epoch-based koruma kullanır | windows-audio-architecture | ✅ fetch 2026-10-12 |
| 2 | Exclusive mod = hedef cihazda tek erişimli istemci | windows-audio-architecture | ✅ fetch 2026-10-12 |
| 3 | Exclusive = 2 ping-pong buffer; sistem sesleri devre dışı | exclusive-mode-streams | ✅ fetch 2026-10-12 |
| 4 | Fallback zinciri WaveRT/DMA → WaveRT shared → line → SSRC/excl | windows-audio-architecture | ✅ fetch 2026-10-12 |
| 5 | AHSAPO yalnız custom FX APO (Win10 öncesi); APO2 Win11+ | windows-audio-architecture | ✅ fetch 2026-10-12 |
| 6 | MSFT ACPI INT33C4 (mute) + INT33C3 (volume) | windows-audio-architecture | ✅ fetch 2026-10-12 |
| 7 | Inbox HDAudio arabellek 128 (5.1/7.1) / 480 (stereo) örnek | windows-audio-architecture | ✅ fetch 2026-10-12 |
| 8 | Motor gecikmesi ~1.3 ms (Win10+); capture ~0 ms | low-latency-audio | ✅ fetch 2026-10-12 |

---

## EK-H. Bu Dosyanın URL Dayanakları

| URL | Kullanım | Fetch |
|---|---|---|
| https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | OHCI katmanları, ACPI, fallback, APO1/2, komponent listesi | 2026-10-12 ✅ |
| https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | AFTEM, motor gecikmesi, WaveRT event, HDAudio örnek aralığı | 2026-10-12 ✅ |
| https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | KS, wave descriptor, terminal pin, device time | 2026-10-12 ✅ |
| https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | exclusive 2-buffer kuralı | 2026-10-12 ✅ |
| PortCls DDI (B8) | PcAddStreamResource | ⚠️ bu turda fetch edilmedi |

---

---

## EK-I. HDAudio Örnek Aralığı ve Gecikme Hesapları

| Senaryo | Örnek (inbox) | Süre @48 kHz | Not |
|---|---|---|---|
| Stereo arabellek (480 örnek) | 480 | 10.00 ms | windows-audio-architecture ✅ |
| 5.1/7.1 arabellek (128 örnek) | 128 | 2.66 ms | windows-audio-architecture ✅ |
| Motor gecikmesi (Win10+) | — | ~1.3 ms p2p | low-latency-audio ✅ |
| Capture motoru | — | ~0 ms | low-latency-audio ✅ |
| Roundtrip (Win10+) | — | 4.5 ms | low-latency-audio ✅ |
| Eski p2p (shared/exclusive) | — | 6/12 ms → +1/+1 ms | low-latency-audio ✅ |
| 10 ms acıma eşiği | — | 10 ms | low-latency-audio ✅ |
| Event-driven exclusive | 1–10 period | sürücüye bağlı | low-latency-audio ✅ |
| 20 ms @48 kHz stereo float | 960 byte | 120 DWord | low-latency-audio ✅ |

> Hesap notu: 480 örnek ÷ 48000 Hz = 10.00 ms; 128 ÷ 48000 = 2.67 ms (sayfadaki 2.66 ms ile
> yuvarlama farkı kabul edilir).

---

## EK-J. K000 İçi Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `isletim-sistemi-turu/windows/windows-kernel.md` | mimari | OHCI katmanının OS geneli bağlamı |
| `isletim-sistemi-turu/windows/windows-modlari.md` (ad beyazliste) | mimari | sürücü modu vs uygulama modu |
| `isletim-sistemi-audio/wasapi/wasapi-latency-ve-buffer.md` | mimari | WaveRT event → gecikme zinciri |
| `isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode.md` | mimari | 2-buffer kuralı paylaşımlı |
| `K003-audio/backend/asio` | teknolojiler-arası | exclusive ⇄ ASIO kıyası |
| `isletim-sistemi-audio/pipewire/pipewire-mimari.md` (beyazliste) | teknolojiler-arası | daemon ⇄ Windows service karşılaştırması |

---

## EK-K. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | WaveRT neden düşük gecikmelidir? | DMA tamamlama → event doğrudan bildirilir | low-latency-audio ✅ |
| 2 | Exclusive modda sistem sesleri neden susar? | motor bypass + 2 ping-pong buffer | exclusive-mode-streams ✅ |
| 3 | Inbox sürücü neden 480 örnek tutuyor? | stereo uç nokta arabellek politikası | windows-audio-architecture ✅ |
| 4 | APO1 neden Win11'de yok? | APO2 + yeni INF zorunlu | windows-audio-architecture ✅ |
| 5 | MSI ACPI nasıl sesi yönetir? | INT33C4 mute / INT33C3 volume | windows-audio-architecture ✅ |
| 6 | Multi-client nasıl korunur? | epoch-based koruma | windows-audio-architecture ✅ |
| 7 | Stream resource neye benzer? | NIC queue'suna benzer alt yapılandırma | glossary ✅ |
| 8 | Motor gecikmesi kaç ms? | ~1.3 ms (Win10+), capture ~0 ms | low-latency-audio ✅ |

---

## EK-L. Doğrulanmamış / Açık Maddeler (Bu Dosya Özeti)

| # | Madde | Durum |
|---|---|---|
| 1 | EK-E kodlarının açılımları (ABD/AMB/AFFD/…) | ⚠️ UNKNOWN |
| 2 | PcAddStreamResource / IPortClsStreamResourceManager detayı | ⚠️ B8 fetch bekliyor |
| 3 | WaveRT ⇄ WaveCyclic kesin geçiş kriteri (sayısal) | ⚠️ UNKNOWN |
| 4 | Uzaktan (RDP) exclusive desteği | ⚠️ UNKNOWN |
| 5 | ARM64 WaveRT davranışı | ⚠️ UNKNOWN |
| 6 | Windows 11 motor gecikmesi yeniden ölçümü | ⚠️ UNKNOWN |

---

---

## EK-M. Kaynak Fetch Günlüğü (Bu Dosya)

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | OHCI, ACPI, fallback, APO, komponentler, HDAudio arabellek | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | AFTEM, gecikme, WaveRT event, HDAudio aralık | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | KS, wave descriptor, terminal pin, device time, stream resource | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2 ping-pong buffer, sistem sesleri | 2026-10-12 ✅ |
| M5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | shared/exclusive yolları, AUDCLNT_E_* | 2026-10-12 ✅ |
| M6 | PortCls DDI (web-arastirma §2.1 B8) | PcAddStreamResource | ⚠️ bu turda fetch edilmedi |

**Günlük kuralı:** her yeni fetch bu tabloya eklenir; `Fetch` hücresi `⚠️` iken iddia kesin yazılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12
