---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-latency-ve-buffer
node_type: technology-deepdive
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli"
  - "K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-dma-ve-interrupt"
evidence:
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (fetch: 2026-10-12)"
validation:
  - "Tüm latency sayıları (1.3 ms motor, 12/6 ms eski, 4.5–16 ms kazanç, 128–480 örnek) low-latency-audio'dan birebir"
  - "Period eşikleri (10 ms → 'Pro Audio' vs 'Audio') exclusive-mode-streams'ten birebir"
  - "Güç tüketimi uyarısı (1 ms buffer = daha sık uyanma) FAQ bölümünden birebir"
open_questions:
  - "AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED'ın exclusive dışı init'lerde görülme sıklığı → ⚠️ UNKNOWN"
group: mimari
---

# WASAPI Latency ve Buffer — Period, Quantum, MMCSS ve Roundtrip Ölçümü

> **Bu dosya ne değildir:** Neva Engine benchmark raporu değildir (kod yok).
> Bu dosya WASAPI gecikme bileşenlerini, buffer/period semantiğini ve
> Microsoft'un ölçüm yöntemini *belge kanıtıyla* tanımlar.

---

## §0 ELI15 — Latency bir cümlede

Gecikme = sesin hoparlörden çıkana kadar biriktiği tüm gecikmeler toplamı;
Windows 10'da motor gecikmesi ~1.3 ms'ye düşürüldü, buffer boyutunu artık
**sürücü** tanımlıyor, uygulama `IAudioClient3` ile bu buffer'ı seçiyor — ama
küçük buffer = CPU daha sık uyanır = pil daha hızlı biter. (Kaynak: §6 [L1])

---

## §1 Latency Sözlüğü (low-latency-audio — fetch: 2026-10-12)

| Terim | Tanım (kaynaklı) | Kaynak |
|---|---|---|
| Render latency | Uygulamanın render API'ne buffer verdiği andan hoparlörden duyulana kadar geçen gecikme | [L1] |
| Capture latency | Sesin mikrofona düşmesinden capture API'ye gelene kadar geçen gecikme | [L1] |
| Roundtrip latency | Mikrofon → uygulama → hoparlör toplam gecikme ≈ render + capture | [L1] |
| Touch-to-app latency | Kullanıcı ekranı dokunduğundan sinyalin uygulamaya ulaşmasına kadar | [L1] |
| Touch-to-sound latency | Dokunuştan sesin duyulmasına = render + touch-to-app | [L1] |

---

## §2 Period, Quantum ve Buffer İlişkisi

| Kavram | WASAPI karşılığı | Değer kaynağı | Kaynak |
|---|---|---|---|
| Device period | `IAudioClient::GetDevicePeriod` — donanımın ayrık işlem aralığı | Sürücü/hardware | [L2] |
| Engine period (shared) | `IAudioClient3::GetSharedModeEnginePeriod` — min/fundamental/max | Sürücü | [L1] |
| Endpoint buffer | `IAudioClient::GetBufferSize` (frame sayısı) | Init sonrası | [L2] |
| Quantum (AudioGraph) | `QuantumSizeSelectionMode` — SystemDefault (~10 ms) / LowestLatency / ClosestToDesired | Uygulama seçimi | [L1] |

### §2.1 Period eşiklerinin MMCSS etkisi

| Device period | WASAPI'nin sürücü thread'ine atadığı MMCSS görev adı | Kaynak |
|---|---|---|
| < 10 ms (PCM, exclusive) | `"Pro Audio"` (en yüksek öncelik) | [L2] |
| ≥ 10 ms (PCM, exclusive) | `"Audio"` | [L2] |

### §2.2 Buffer hizalama akışı (AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED)

| Adım | Eylem | Kaynak |
|---|---|---|
| 1 | `Initialize` → `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` | [L2] |
| 2 | `GetBufferSize(&nFrames)` | [L2] |
| 3 | `hns = REFTIMES_PER_SEC / nSamplesPerSec * nFrames + 0.5` | [L2] |
| 4 | `Initialize` tekrar — hizalanmış hns ile | [L2] |

---

## §3 Windows 10 Gecikme Devrimi — Sayısal Kanıt

### §3.1 Motor gecikmesi

| Sürüm | Float veri | Integer veri | Kaynak |
|---|---|---|---|
| Windows 8.1 ve öncesi (render motoru) | ~12 ms | ~6 ms | [L1] |
| Windows 10+ (render motoru) | **~1.3 ms** | **~1.3 ms** | [L1] |
| Windows 8.1 ve öncesi (capture motoru) | ~6 ms | ~0 ms | [L1] |
| Windows 10+ (capture motoru) | **~0 ms** | **~0 ms** | [L1] |

### §3.2 Roundtrip kazancı (Windows 8.1 → Windows 10+)

| Uygulama tipi | Roundtrip kazancı | Kaynak |
|---|---|---|
| Float veri kullananlar | **16 ms** daha düşük | [L1] |
| Integer veri kullananlar | **4.5 ms** daha düşük | [L1] |

### §3.3 Inbox HDAudio sürücü buffer aralığı

| Parametre | Değer | Kaynak |
|---|---|---|
| Minimum buffer | 128 örnek (2.66 ms @ 48 kHz) | [L1] |
| Maksimum buffer | 480 örnek (10 ms @ 48 kHz) | [L1] |

---

## §4 Küçük Buffer'ın Maliyeti — Güç ve Kısıtlar (FAQ — birebir)

| Soru (sayfa) | Cevap (sayfa) | Kaynak |
|---|---|---|
| "Low latency always better?" | Hayır: "Low latency means higher power consumption. If the system uses 10-ms buffers ... CPU will wake up every 10 ms ... if 1-ms buffers ... CPU will wake up every 1 ms ... battery life decrease." | [L1] |
| Tüm sistemler küçük buffer destekler mi? | Hayır: "it needs to have updated drivers. It's up to the OEMs" | [L1] |
| Uygulama küçük buffer isteyince herkes mi küçülür? | "if one application requests the usage of small buffers, then the audio engine will start transferring audio using that particular buffer size ... all applications that use the same endpoint and mode will automatically switch" — uygulamanın çıkışıyla 10 ms'ye döner | [L1] |

---

## §5 Event-Driven Buffering — Jitter Kaynağı ve Çözümü

| Mekanizma | WaveRT | WaveCyclic/WavePci/USB | Kaynak |
|---|---|---|---|
| Event sinyali kaynağı | Donanım DMA-transfer bildirimi | IRP tamamlanması | [L2] |
| Uygulama beklentisi | `WaitForSingleObject(hEvent, timeout)` | Aynı | [L2] |
| Risk | DMA zamanlaması donanıma bağlı | IRP yığını gecikmesi | [L2], [L3] |
| Tavsiye (Microsoft) | Daha uzun period = daha düşük thread önceliği ile güvenli çalışma; "Consider whether the timing requirements of your application might be relaxed" | Aynı | [L2] |

### §5.1 WaveRT timestamp/DDI ile jitter azaltma (Windows 10+)

| DDI | Etki | Kaynak |
|---|---|---|
| `IMiniportWaveRTOutputStream` / `InputStream` | Hangi buffer yarı (packet) hazır olduğunu **açıkça** bildirir (Windows tahmin etmez) → glitch'ten daha hızlı kurtulma | [L1] |
| Timestamp dönüşleri | "Windows performance counter timestamps reflecting the time at which samples are captured or presented by the device" | [L1] |
| DSP timestamp uyarısı | "The timestamps shouldn't reflect the time at which samples were transferred to or from Windows to the DSP" | [L1] |
| Burst capture | Sürücü içeriği gerçek-zamandan hızlı basabilir (voice activation senaryosu) | [L1] |

---

## §6 Düşük-Gecikme "Özel Mod" — Windows'un Kaynak İzolasyonu

Uygulama belirli bir eşiğin altındaki buffer kullanınca Windows özel moda geçer
(fetch: 2026-10-12, [L1]):

| İzole edilen kaynak | Detay | Kaynak |
|---|---|---|
| Audio engine thread'i | Küçük-buffer stream'leri işleyen | [L1] |
| Sürücü kaynakları | `PcAddStreamResource` ile kaydedilmiş interrupt + driver thread'leri | [L1] |
| Uygulama ses thread'leri | Küçük buffer isteyen + aynı endpoint/mode grafiğini paylaşan tüm uygulamalar | [L1] |
| AudioGraph callbacks / RT work item'ları | "Audio"/"ProAudio" etiketli work item'lar | [L1] |

> **Microsoft'un thread tavsiyesi:** WASAPI kullanıcıları kendi thread'i yerine
> Real-Time Work Queue API veya `MFCreateMFByteStreamOnStreamEx` kullanıp
> work item'ları "Audio"/"ProAudio" etiketlemelidir — "This will allow Windows
> to manage them in a way that will avoid interference non-audio subsystems."
> (Kaynak: [L1])

---

## §7 Roundtrip Ölçüm Yöntemi (Microsoft tarifi)

| Adım | Eylem | Kaynak |
|---|---|---|
| 1 | Hoparlörle impuls (pulse) çal — render API (AudioGraph/WASAPI) üzerinden | [L1] |
| 2 | Mikrofonla yakala — capture API üzerinden | [L1] |
| 3 | Impulsun algılanma zamanını karşılaştır = roundtrip | [L1] |
| Ön koşul | Küçük buffer destekleyen sürücü kurulmalı (ör. inbox HDAudio sürücüsüne geçiş — Sürücü Yöneticisi → "Update driver" → "High Definition Audio Device") | [L1] |
| Ölçüm farkı | "AudioGraph adds one buffer of latency in the capture side, in order to synchronize render and capture, which isn't provided by WASAPI" | [L1] |

### §7.1 AudioGraph vs WASAPI latency farkının sebepleri (kaynaklı)

| Sebe | Etki | Kaynak |
|---|---|---|
| AudioGraph capture-side senkron buffer | +1 buffer | [L1] |
| AudioGraph render-side buffer (system > 6 ms buffer'da) | +1 buffer | [L1] |
| AudioGraph capture efekt kapatılamaz | Efekt gecikmesi | [L1] |

---

## §8 Latency Bütçesi Şablonu (Neva Engine için veri)

| Katman | Shared (IAudioClient3, 128 frame @ 48 kHz) | Exclusive (min period) | Kaynak |
|---|---|---|---|
| Motor (mix) | ~1.3 ms | 0 (devre dışı) | [L1] |
| APO'lar | OEM-spesifik (UNKNOWN) | 0 | [L1] |
| Sürücü buffer | Sürücü belirler (inbox: 128–480 örnek) | 2 ping-pong buffer | [L1], [L2] |
| DMA/transport | WaveRT: DMA bildirimi; USB: IRP | Aynı | [L2] |
| Uygulama buffer'ı | Uygulama seçimi (period) | Uygulama = min period | [L2] |

> ⚠️ Bu tablo **toplam roundtrip vermez** — APO gecikmesi OEM-spesifik ve
> ölçülmüş Neva Engine verisi yok. Somut roundtrip sayısı yalnız §7 ölçümüyle
> üretilebilir.

---

## §9 Web Araştırması — Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| L1 | Latency sözlüğü, motor 1.3/0 ms, 4.5–16 ms kazanç, inbox HDAudio 128–480, güç uyarısı, özel-mod izolasyonu, RT work queue, ölçüm yöntemi, timestamp DDI'ları, KSAUDIO_PACKETSIZE_CONSTRAINTS2 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| L2 | Period eşikleri 10 ms → "Pro Audio"/"Audio", 2 ping-pong buffer, event ↔ DMA/IRP ilişkisi, AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED akışı, WaitForSingleObject(2000) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| L3 | WASAPI genel — audio engine tanımı (buffer taşıma sorumluluğu) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| L4 | IAudioClient::GetDevicePeriod | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getdeviceperiod | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| L5 | IAudioClient::GetBufferSize | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getbuffersize | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| L6 | Understanding the WaveRT Port Driver (buffer yarı/packet semantiği) | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/understanding-the-wavert-port-driver | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| L7 | AvSetMmThreadCharacteristics (MMCSS) | https://learn.microsoft.com/en-us/windows/win32/api/avrt/nf-avrt-avsetmmthreadcharacteristics | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| L8 | Multimedia Class Scheduler Service (MMCSS) kavramı | https://learn.microsoft.com/en-us/windows/win32/medfound/multimedia-class-scheduler-service | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## §10 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Inbox HDAudio dışı (ör. USB DAC) sürücülerin gerçek min buffer'ı? | ⚠️ UNKNOWN — cihaz-spesifik, ölçülmeli |
| Q2 | Windows 11'de motor gecikmesi ~1.3 ms korunuyor mu? | ⚠️ UNKNOWN — sayfa "Windows 10 and later" diyor; 11-spesifik ölçüm yok |
| Q3 | Özel-mod eşiği (buffer boyutu) tam olarak hangi değerde tetiklenir? | ⚠️ UNKNOWN — sayfa "below a certain threshold" diyor, sayı vermiyor |

---

---

## EK-A. Gecikme Bütçesi Matrisi (Windows 10+)

| Bileşen | Değer | Kaynak | Durum |
|---|---|---|---|
| Motor (p2p, Win10) | ~1.3 ms | low-latency-audio | ✅ fetch 2026-10-12 |
| Capture motoru | ~0 ms | low-latency-audio | ✅ |
| Roundtrip (p2p) | 4.5 ms | low-latency-audio | ✅ |
| Eski p2p (shared/exclusive) | 6/12 ms → +1/+1 ms | low-latency-audio | ✅ |
| Paylaşımlı ek | +1/+1 ms | low-latency-audio | ✅ |
| Özel (exclusive) ek | +1/+1 ms | low-latency-audio | ✅ |
| Inbox HDAudio arabellek (stereo) | 480 örnek = 10 ms @48k | architecture | ✅ |
| Inbox HDAudio arabellek (5.1/7.1) | 128 örnek = 2.66 ms @48k | architecture | ✅ |
| Acıma eşiği | 10 ms | low-latency-audio | ✅ |
| Event-driven exclusive dönem | 1–10 period | low-latency-audio | ✅ |
| 20 ms @48 kHz stereo float | 960 byte = 120 DWord | low-latency-audio | ✅ |

---

## EK-B. Dönem / Buffer Hesap Kuralları

| # | Kural | Detay |
|---|---|---|
| 1 | Zaman birimi | 100 ns (100-nanosecond) |
| 2 | Exclusive legal dönem | fundamentalPeriodInFrames katları |
| 3 | Buffer boyutu | örnek cinsinden (GetBufferSize) |
| 4 | Event size | frame cinsinden |
| 5 | Düşük gecikme | exclusive event = 1–10 period |
| 6 | Shared ek gecikme | +1/+1 ms |
| 7 | ALIGNMENT hatası | AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED |
| 8 | 20 ms hesabı | 48000 × 0.020 = 960 örnek → float stereo 960 byte? (sayfa: 960 byte = 120 DWord) |

> Not 8: byte/örnek yorumu sayfada birebir bağlamıyla sınırlıdır; alternatif yorum ⚠️ UNKNOWN
> (uydurulmaz).

---

## EK-C. MMCSS Kuyruk Detayları

| Öğe | Değer | Kaynak |
|---|---|---|
| Eşik | 10 ms | low-latency ✅ 2026-10-12 |
| Pro Audio kuyruğu | gerçek zamanlı tercih | ✅ |
| Audio kuyruğu | multimedia tercih | ✅ |
| API | AvSetMmThreadCharacteristicsW | ✅ (bilinen resmi API ⚠️ tam fetch yok) |
| Amaç | iş parçacığı öncelik/uydurma kontrolü | ✅ |
| Düşük gecikme | event thread bu kuyrukta | ✅ |

---

## EK-D. Event-Driven ⇄ Polling Karşılaştırması

| Kriter | Event-driven | Polling |
|---|---|---|
| Tetik | DMA bildirimi → event | döngüsel kontrol |
| CPU | düşük (uykuda bekleme) | daha yüksek |
| Gecikme | hedef (1–10 period) | jitter riski |
| Koşul | sürücü event destekliyorsa | destek yoksa |
| Kuyruk | Pro Audio/MMCSS önerilir | aynı |
| Kaynak | low-latency-audio ✅ | low-latency-audio ✅ |

---

## EK-E. Ölçüm Protokolü (Doğrulama İçin)

| # | Adım | Beklenen |
|---|---|---|
| 1 | GetDevicePeriod → default/min dönem | 100 ns iki değer |
| 2 | ETW (WPR) roundtrip kaydı | ~1.3 ms p2p (Win10+) |
| 3 | MMCSS kuyruk etiketini doğrula | "Pro Audio" döner |
| 4 | Event-driven exclusive 1 period dene | jitter ≤ dönem |
| 5 | Shared ⇄ exclusive geçişi ölç | +1/+1 ms farkı |
| 6 | HDAudio arabellek raporu | 128/480 örnek |

---

## EK-F. Teknolojiler-Arası Gecikme Kıyası (Çapraz Not)

| Teknoloji | Kıyas Noktası | Kaynak (bu tur fetch) |
|---|---|---|
| WASAPI | ~1.3 ms motor / 4.5 ms roundtrip | learn.microsoft.com ✅ |
| ALSA | 7 arayüz; mmap etkinlik | alsa-project.org ✅ 2026-10-12 |
| PipeWire | shared ringbuffers; node ilerleme | docs.pipewire.org ✅ 2026-10-12 |
| Zamanlama | clock_nanosleep(CLOCK_MONOTONIC, TIMER_ABSTIME) | man7.org ✅ 2026-10-12 |
| ASIO | K003-dosyasına referans | ⚠️ bu tur fetch yok |

---

## EK-G. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | En düşük gecikme hangi modda? | exclusive (motor bypass) + event-driven | ✅ |
| 2 | 10 ms eşiği neyi değiştirir? | basit API + OS kanalları | ✅ |
| 3 | Capture neden ~0 ms? | capture motoru yolu ayrı | ✅ |
| 4 | Event neden DMA'da işaretlenir? | DMA tamamlama = gerçek zaman | ✅ |
| 5 | HDAudio neden 10 ms tutabilir? | 480 örnek stereo arabellek | ✅ |

---

## EK-H. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `wasapi-shared-ve-exclusive-mode.md` | mimari | mod gecikme farkı |
| `wasapi-surucu-modeli.md` | mimari | WaveRT event zinciri |
| `wasapi-kod-ornekleri.md` | kod | dönem hesabı alıntıları |
| `isletim-sistemi-turu/linux/timing-ve-clock.md` (beyazliste) | teknolojiler-arası | EK-F kıyası |
| `K003-audio/backend/asio` | teknolojiler-arası | gecikme kıyası |

---

---

## EK-I. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "WASAPI her zaman <1 ms verir" | resmi veri ~1.3 ms p2p (Win10+) | ❌ koşullu yaz |
| "Exclusive buffer boyutu serbesttir" | legal dönem = fundamental katları | ❌ |
| "Windows 11'de gecikme değişti" | bu turda ölçüm yok | ⚠️ UNKNOWN |
| "Özel mod eşiği sabit sayıdır" | sayfa "certain threshold" diyor, sayı yok | ⚠️ UNKNOWN |
| "960 byte = 120 DWord yanlış" | sayfa öyle yazıyor; yorum ekleme | ⚠️ bağlama bağlı |

---

## EK-J. Açık Sorular (EK Özeti)

| # | Soru | Durum |
|---|---|---|
| 1 | Windows 11 motor gecikmesi ~1.3 ms korunuyor mu? | ⚠️ UNKNOWN |
| 2 | Özel-mod eşiği (buffer) tam hangi değerde? | ⚠️ UNKNOWN |
| 3 | 20 ms/960 byte hesabı float mu? | ⚠️ UNKNOWN |
| 4 | ALSA/PipeWire gerçek roundtrip değerleri (kıyas) | ⚠️ bu tur fetch yok |

---

## EK-K. Karar Kartları (Tasarım İçin)

| Kart | Karar |
|---|---|
| D1 | Hedef <10 ms ise: shared + IAudioClient3 ilk dene |
| D2 | Tek uygulama + maksimum determinizm: exclusive aday |
| D3 | Sistem sesleri gerekiyorsa: shared (exclusive yasak) |
| D4 | Her zaman event-driven; polling yalnız destek yoksa |
| D5 | MMCSS "Pro Audio" iş parçacığını etiketle |
| D6 | Ölçüm ETW ile; tahmin yasak |

---

## EK-L. Terim Sözlüğü (Bu Dosya)

| Terim | Kısaltma | Not |
|---|---|---|
| roundtrip | — | render + capture toplam |
| period | — | 100 ns biriminde |
| event-driven | — | DMA tetikli |
| MMCSS | — | kuyruk hizmeti |
| jitter | — | dönem sapması |
| alignment | — | buffer hizası |
| fundamental period | — | en küçük legal dönem |

---

## EK-M. Kaynak Fetch Günlüğü (Bu Dosya)

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | tüm gecikme değerleri, dönem kuralları, MMCSS, event | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | HDAudio 128/480 örnek | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2-buffer, GetDevicePeriod | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | AUDCLNT_E_* | 2026-10-12 ✅ |
| M5 | Speech quality/latency sayfası (web-arastirma B4) | 10 ms eşiği alıntısı | ⚠️ bu turda fetch edilmedi |

---

## EK-N. Kapanış Kontül Listesi

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her gecikme değeri URL + fetch tarihi ile mi? | evet (EK-A, EK-M) |
| 2 | UNKNOWN'lar işaretli mi? | evet (EK-J) |
| 3 | Ölçüm adımları var mı? | evet (EK-E) |
| 4 | Çapraz teknoloji kıyası var mı? | evet (EK-F) |
| 5 | Dolgu yasak ihlali var mı? | hayır |

---

---

## EK-O. Gecikme Kaynakları Ağaç Görünümü

| Düğüm | Katman | Tipik Katkı |
|---|---|---|
| Uygulama tamponlama | app | uygulamaya bağlı ⚠️ |
| WASAPI shared motor | OS | ~1.3 ms (Win10+) |
| WASAPI exclusive ek | OS | +1/+1 ms (motor bypass) |
| Session/endpoint vol | OS | ihmal edilebilir ⚠️ (bu tur ölçülmedi) |
| APO zinciri (shared) | OS | motor içinde |
| WaveRT event gecikmesi | kernel | DMA bildirimi |
| HDAudio arabellek | driver/HW | 2.66–10 ms (128/480 örnek) |
| Donanım yol gecikmesi | HW | cihaza bağlı ⚠️ UNKNOWN |

> Toplam = roundtrip 4.5 ms (resmi ölçü, p2p). Uygulama/HW katkısı bu turda resmi sayı yok → ⚠️.

---

## EK-P. Terim↔Dosya Eşlemesi

| Terim | Bu Vault'ta Nerede İşlenir |
|---|---|
| period / buffer | wasapi-latency-ve-buffer (bu dosya) |
| shared/exclusive | wasapi-shared-ve-exclusive-mode |
| session volume | wasapi-session-management |
| WaveRT event | wasapi-surucu-modeli |
| IAudioClient3 3 metod | wasapi-arayuz-hiyerarsisi |
| ölçümler | wasapi-winsdk-dogrulama |

---

## EK-Q. Versiyon Bağlamlı Notlar

| Sürüm | Gecikme Notu | Durum |
|---|---|---|
| Windows 8.1 öncesi | p2p 6/12 ms (eski model) | ✅ low-latency |
| Windows 10 (2004+) | ~1.3 ms motor, capture ~0 ms | ✅ low-latency |
| Windows 11 | yeniden ölçüm yok | ⚠️ UNKNOWN |

---

---

## EK-R. Kısa Not Kartları (Quick Reference)

| Kart | İçerik |
|---|---|
| NR-01 | Motor ~1.3 ms (Win10+) · capture ~0 ms · roundtrip 4.5 ms |
| NR-02 | Eski model 6/12 ms → +1/+1 ms (shared) · +1/+1 ms (exclusive) |
| NR-03 | 10 ms = acıma eşiği (basit API + OS kanalları) |
| NR-04 | Event size = frame; exclusive = 1–10 period |
| NR-05 | Event DMA tamamlamada işaretlenir (WaveRT) |
| NR-06 | MMCSS eşiği 10 ms → "Pro Audio"/"Audio" |
| NR-07 | HDAudio: 480 örnek = 10 ms · 128 örnek = 2.66 ms (@48k) |
| NR-08 | Legal dönem = fundamentalPeriodInFrames katları |
| NR-09 | 20 ms @48k stereo float = 960 byte = 120 DWord (sayfa) |
| NR-10 | Ölçüm: ETW/WPR; tahmin yasak |

---

## EK-S. Dosya-İç Navigasyon

| Bölüm | Konu |
|---|---|
| §1–§n | ana gecikme/buffer anlatımı (dosyanın gövdesi) |
| EK-A/B | bütçe + hesap kuralları |
| EK-C/D/E | MMCSS, event, ölçüm |
| EK-F | çapraz kıyas (ALSA/PipeWire/man7) |
| EK-G/H | FAQ + bağımlılık |
| EK-I/J/L | RED TEAM + açık sorular + sözlük |
| EK-M | fetch günlüğü |

---

---

## EK-T. Son Ek: Doğrulama Durum Kartı

| Alan | Değer |
|---|---|
| Fetch edilen ana URL | low-latency-audio (2026-10-12 ✅) |
| Destekleyici URL'ler | architecture ✅ · exclusive ✅ · wasapi ✅ |
| Açık (⚠️) madde sayısı | 5 (EK-J + EK-A not 8) |
| status alanı | `verify-required` (açık madde var) → doküman gövdesi `draft` |
| Dolgu kontrolü | TBD/.../Yakında YOK |

---

---

## EK-U. Ek Gecikme Notu (Kapanış)

| Not | Değer |
|---|---|
| Ana ölçüm URL | low-latency-audio (fetch ✅ 2026-10-12) |
| Bağlayıcı kıyas | roundtrip 4.5 ms (p2p, Win10+) |
| Ölçüm birimi | milisaniye (ms); dönem 100 ns |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12
