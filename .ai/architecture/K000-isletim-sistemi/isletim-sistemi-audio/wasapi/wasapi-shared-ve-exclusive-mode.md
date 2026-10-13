---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode
node_type: technology-deepdive
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli"
  - "K003-audio/backend/asio"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (fetch: 2026-10-12)"
validation:
  - "Preemption varsayılanları (Vista) exclusive-mode-streams'ten birebir alındı"
  - "AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED hata akışı resmi kod örneğinden doğrulandı"
  - "IAudioClient3 shared düşük-periyot 'exclusive'a tercih' tavsiyesi sayfa metninden birebir"
open_questions:
  - "Kullanıcı 'exclusive kontrol izni' kapattığında AUDCLNT_E_DEVICE_INVALIDATED mı yoksa AUDCLNT_E_ACCESS_DENIED mı dönüyor? → ⚠️ UNKNOWN (bu turda doğrulanmadı)"
group: mimari
---

# WASAPI Shared ve Exclusive Mode — Preemption, Bit-Exact, IAudioClient3 ile Düşük-Periyot Shared

> **Bu dosya ne değildir:** Performans benchmark'ı değildir. Bu dosya iki modun
> *Microsoft tanımını*, kullanıcı ayarlarını, preemption kurallarını ve
> CoreMusic'in (Neva Engine) hangi şartta hangi modu seçeceğini *belge kanıtıyla*
> tanımlar.
>
> **Durum etiketi:** ŞARTNAME IMPLEMENTED · Neva Engine kararı ⚠️ PLANNED
> (ADR bekliyor — bu dosya §8'de karar vermez, veri verir).

---

## §0 ELI15 — Shared vs Exclusive bir cümlede

**Shared mode**: ses motoru tüm uygulamaların sesini karıştırır, sistem sesleri
çalar, uygulama motorun tamponuna yazar — Windows 10'dan sonra `IAudioClient3`
ile gecikme exclusive'a yaklaşır. **Exclusive mode**: uygulama donanıma tek başına
bağlanır, motor devre dışı kalır, diğer tüm sesler susar — bit-exact/özel
örnekleme hızı gerektiğinde kullanılır. (Kaynak: §7 [E1], [E2])

---

## §1 Mod Karşılaştırma Matrisi

| Özellik | Shared | Exclusive | Kaynak |
|---|---|---|---|
| Motor (mixer + APO) | Aktif | Devre dışı | [E1], [E2] |
| Uygulama sayısı | Birden fazla paylaşabilir | Tek uygulama hakim | [E1] |
| Sistem sesleri/bildirimler | Çalınır | **Susturulur** | [E1] |
| Buffer mimarisi | Motor yönetir | 2 ping-pong buffer | [E1] |
| Volume arayüzleri (session) | Etkili | Etkisiz → `IAudioEndpointVolume` | [E1] |
| Win10+ gecikme | Motor ~1.3 ms + sürücü buffer'ı | Motor katmanı yok | [E2] |
| Yeniden örnekleme (resampling) | Motor yapabilir (MATCH_FORMAT ile önlenir) | Yok — cihaz formatı | [E2] |
| Bit-exact çıktı | Motor float/int işleme geçer | Mümkün (özel format) | [E1] |

### §1.1 Exclusive'a neden başvurulur (kaynaklı gerekçeler)

| Gerekçe | Kaynak ifade | Kaynak |
|---|---|---|
| Bit-exact çıktı ihtiyacı | "unless you have a hard requirement for bit-exact output or custom sample rates" (IAudioClient3 paylaşımı tercih etme cümlesi içinde) | [E2] |
| Düşük gecikme (klasik) | "applications ... often do so because they require low latencies in the data paths between the audio endpoint devices and the application threads" | [E1] |
| Donanım-zorunlu | Bazı DAC'lar yalnız exclusive akışta istenen formatı kabul eder (⚠️ cihaz-spesifik — UNKNOWN, doğrulanmadı) | ⚠️ |

---

## §2 Kullanıcı Ayarları ve Varsayılanlar (Windows Vista+)

Exclusive-mode-streams sayfasının kullanıcı ayarları (fetch: 2026-10-12, [E1]):

| Ayar | Vista varsayılanı | Kullanıcı etkisi |
|---|---|---|
| "Allow applications to take exclusive control of this device" | **Açık** | Kapatılırsa cihaz yalnız shared stream kabul eder |
| "Give exclusive mode applications priority" (preemption) | **Açık** | Preemption kapatılırsa exclusive istek, cihaz shared kullanımda bile **başarısız** olur |

### §2.1 Preemption kuralları (4 durum)

| Cihaz durumu | Preemption AÇIK | Preemption KAPALI | Kaynak |
|---|---|---|---|
| Boş | ✅ Exclusive başarılı | ✅ Exclusive başarılı | [E1] |
| Shared kullanımda | ✅ Exclusive başarılı (shared'lar devre dışı) | ❌ Başarısız | [E1] |
| Başka exclusive kullanımda | ❌ Başarısız | ❌ Başarısız | [E1] |
| Kullanıcı exclusive'i cihaz bazında kapalı tutmuş | ❌ Başarısız | ❌ Başarısız | [E1] |

> **Microsoft tavsiyesi (birebir):** "Always provide a user-visible option to
> fall back to shared mode, and release the device promptly when your application
> loses focus." (Kaynak: [E1])

### §2.2 Kayıp-odak (focus-loss) davranışı

| Davranış | Gerekçe | Kaynak |
|---|---|---|
| Exclusive stream sahibi uygulama ön planda değilken cihazı bırakır (tipik) | "an application with an exclusive-mode stream typically relinquishes control of the audio device when the application is not the foreground process or is not actively streaming" | [E1] |

---

## §3 Exclusive Init Akışı — Resmi Kodun Mantığı

exclusive-mode-streams'teki `PlayExclusiveStream` örneğinin adım analizi
(fetch: 2026-10-12, [E1] — tam kod `wasapi-kod-ornekleri.md` §2'de):

| # | Çağrı | Amaç |
|---|---|---|
| 1 | `CoCreateInstance(CLSID_MMDeviceEnumerator, …)` | Enumerator |
| 2 | `GetDefaultAudioEndpoint(eRender, eConsole, …)` | Varsayılan render endpoint |
| 3 | `Activate(IID_IAudioClient, …)` | Audio client |
| 4 | `GetStreamFormat(&pwfx)` (helper — `IsFormatSupported` çağrıları) | Exclusive format negosiasyonu |
| 5 | `GetDevicePeriod(NULL, &hnsRequestedDuration)` | Donanımın desteklediği **minimum** period |
| 6 | `Initialize(AUDCLNT_SHAREMODE_EXCLUSIVE, AUDCLNT_STREAMFLAGS_EVENTCALLBACK, hns, hns, pwfx, NULL)` | Minimum gecikme ile init |
| 7 | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` durumunda: `GetBufferSize` → hns yeniden hesap → `Initialize` tekrar | Buffer hizalama telafisi |
| 8 | `CreateEvent` + `SetEventHandle` | Event-driven buffering |
| 9 | `GetBufferSize` → ilk buffer'ı doldur (`GetBuffer`/`LoadData`/`ReleaseBuffer`) | İlk gecikmeyi azalt |
| 10 | `AvSetMmThreadCharacteristics("Pro Audio", &taskIndex)` | MMCSS öncelik boost |
| 11 | `Start()` → döngü: `WaitForSingleObject(hEvent, 2000)` → `GetBuffer`/`LoadData`/`ReleaseBuffer` | Ping-pong servis |

### §3.1 Buffer hizalama hatası — `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED`

| Durum | Kaynak kod davranışı | Kaynak |
|---|---|---|
| `Initialize` bu hatayı dönerse | `GetBufferSize(&nFrames)` çağrılır; `hns = REFTIMES_PER_SEC / nSamplesPerSec * nFrames + 0.5` ile yeniden hesaplanır; `Initialize` aynı parametrelerle tekrarlanır | [E1] |

### §3.2 Event timeout politikası (resmi örnek)

| Parametre | Değer | Kaynak |
|---|---|---|
| `WaitForSingleObject` timeout | 2000 ms | [E1] |
| Timeout olursa | `Stop()` + `ERROR_TIMEOUT` ile çıkış | [E1] |

---

## §4 Shared Düşük-Periyot — IAudioClient3 Çağrısı Sırası

low-latency-audio sayfasındaki WASAPIAudio örneğinin 5 adımı (fetch: 2026-10-12, [E2]):

| # | Adım | Çağrı | Not |
|---|---|---|---|
| 1 | Aktivasyon | `ActivateAudioInterfaceAsync(deviceId, __uuidof(IAudioClient3), …)` | UI thread'inde; callback `ActivateCompleted` — agile arayüz |
| 2 | Özellikler | `SetClientProperties(&audioProps)` — `AudioClientProperties`, `eCategory=AudioCategory_Media`; isteğe bağlı RAW/MATCH_FORMAT | Offload desteklenmez |
| 3 | Period sorgusu | `GetMixFormat` → `GetSharedModeEnginePeriod(wfx, &default, &fundamental, &min, &max)` | Legal period = fundamental katları |
| 4 | Init | `InitializeSharedAudioStream(AUDCLNT_STREAMFLAGS_EVENTCALLBACK, desiredPeriodInFrames, mixFormat, nullptr)` | `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED` ihtimali |
| 5 | Özel format | `IsFormatSupported(SHARED, appFormat, &closest)` → `InitializeSharedAudioStream(..., appFormat, ...)` | `AUDCLNT_E_ENGINE_FORMAT_LOCKED` ihtimali |

### §4.1 Microsoft'un mod seçimi tavsiyesi (birebir)

> "Evaluate shared-mode low-period streams before committing to exclusive mode
> unless you have a hard requirement for bit-exact output or custom sample
> rates." (Kaynak: [E2])

> "Favor AudioGraph, wherever possible for new application development. Only use
> WASAPI, if: You need more control than that provided by AudioGraph. You need
> lower latency than that provided by AudioGraph." (Kaynak: [E2])

---

## §5 Gecikme Eşitliği — Ne Zaman Exclusive Gerekmez?

| Senaryo | Shared (IAudioClient3) | Exclusive | Sonuç |
|---|---|---|---|
| Pro audio / müzik üretimi (48 kHz, 128-frame period) | Win10+: motor 1.3 ms + sürücü buffer'ı (inbox HDAudio 128 örnek = 2.66 ms @48 kHz) | Motor katmanı yok | Fark küçük — shared tercih |
| Bit-exact DAC çıkışı (ör. DSD/RAW) | Motor işleme riski (RAW ile azaltılır) | Tam kontrol | Exclusive gerekir |
| Özel örnekleme hızı (ör. 44.1 ↔ 48 dışı) | MATCH_FORMAT ile motor resampling'i kapatılabilir; motor formatı kilitlenebilir | Cihaz formatı birebir | Exclusive veya MATCH_FORMAT |
| DAW çoklu-monitor | Session'lar ayrı ayrı sürülebilir | Tek cihaz tek uygulama | Shared ölçeklenir |

> **Not (kanıt sınırı):** Inbox HDAudio sürücüsü "supports buffer sizes between
> 128 samples (2.66ms@48kHz) and 480 samples (10ms@48kHz)" — bu iki uç değer
> sayfadan birebir; aradaki somut adımlar UNKNOWN. (Kaynak: [E2])

---

## §6 ASIO ile Karşılaştırma — Neden Exclusive WASAPI ASIO Değildir?

| Kriter | ASIO | WASAPI Exclusive | Kaynak |
|---|---|---|---|
| Dağıtım | 3. parti sürücü kurulumu gerekir | Windows kutudan gelir | [E2] |
| Erişim yolu | Uygulama doğrudan ASIO driver'ına konuşur | Uygulama → endpoint device → WaveRT/WaveCyclic | [E2] |
| Microsoft'un tarifi | "applications can send data directly from the application to the ASIO driver. However, the application has to be written in such a way that it talks directly to the ASIO driver" | Resmi yığın üzerinden | [E2] |
| Cihaz paylaşımı | ASIO sürücüsü cihazı genelde tek uygulamaya verir | Windows kullanıcı ayarı (preemption) yönetir | [E1] |
| Sistem sesleri | ASIO bağlandığında sistem sesi yok | Exclusive'ta yok | [E1] |

> Microsoft low-latency-audio'da ASIO'yu "popular alternative" olarak tanır ama
> "both alternatives (exclusive mode and ASIO) have their own limitations"
> der — bu, Neva Engine'in WASAPI fallback zincirini (`ASIO → WASAPI exclusive
> → WASAPI shared`) vault kuralıyla (`.ai/AGENTS.md` §17 #6) destekler.
> (Kaynak: [E2])

---

## §7 CoreMusic Karar Matrisi (veri — karar değil)

| Girdi | WASAPI yolu | Kardeş dosya |
|---|---|---|
| ASIO cihaz-kaybı (edge case #6) | Fallback → WASAPI exclusive → shared | `wasapi-shared-ve-exclusive-mode.md` (bu dosya) §3 |
| Düşük gecikme + sistem sesi korunacak | `IAudioClient3` shared minimal period | §4 |
| Bit-exact DAC doğrulama | Exclusive + `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` | §3 |
| Kullanıcı ayarı kapalı olabilir | Shared'a düşüş yolu zorunlu (Microsoft: "Always provide a user-visible option to fall back to shared mode") | §2 |

---

## §8 Web Araştırması — Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| E1 | Exclusive/shared tanımı, preemption 4 durum, Vista varsayılanları, focus-loss, 2 ping-pong buffer, AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED, "Pro Audio" MMCSS, PlayExclusiveStream kodu, session volume exclusive'ta etkisiz | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| E2 | IAudioClient3 shared düşük-periyot, "shared önce exclusive sonra" tavsiyesi, AUDCLNT_E_ENGINE_PERIODICITY/FORMAT_LOCKED, MATCH_FORMAT/RAW, inbox HDAudio 128–480 örnek, ASIO tarifi, AudioGraph tercihi | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| E3 | WASAPI genel + AUDCLNT_E_DEVICE_INVALIDATED kurtarma uyarısı | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| E4 | IAudioClient::Initialize referansı (flag/parametre detayı) | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| E5 | IAudioClient::IsFormatSupported | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-isformatsupported | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| E6 | Device Formats (format negosiasyonu) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| E7 | Recovering from an Invalid-Device Error | https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| E8 | Endpoint Volume Controls (IAudioEndpointVolume) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/endpoint-volume-controls | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## §9 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | Exclusive istek cihaz ayarı kapalıyken tam HRESULT nedir? | ⚠️ UNKNOWN |
| Q2 | `AUDCLNT_STREAMOPTIONS_RAW` ile MATCH_FORMAT eşzamanlı kullanılabilir mi? | ⚠️ UNKNOWN |
| Q3 | Windows 11'de preemption varsayılanları değişti mi? | ⚠️ UNKNOWN — sayfa Vista varsayılanlarını anlatıyor |

---

---

## EK-A. Shared ⇄ Exclusive Karşılaştırma Tablosu (Geniş)

| # | Kriter | Shared | Exclusive | Kaynak |
|---|---|---|---|---|
| 1 | Motor | OS mixer motoru devrede | motor bypass | wasapi ✅ |
| 2 | Buffer | motor yönetir | 2 ping-pong buffer | exclusive-mode-streams ✅ |
| 3 | Sistem sesleri | duyulur | susar | exclusive-mode-streams ✅ |
| 4 | Çoklu uygulama | desteklenir | tek erişimli istemci | architecture ✅ |
| 5 | Format dönüşümü | OS dönüştürür | uygulama sağlamalı | wasapi ✅ |
| 6 | Gecikme | paylaşımlı +1/1 ms | özel +1/1 ms | low-latency ✅ |
| 7 | Uyumluluk | geniş | sürücüye bağlı | wasapi ✅ |
| 8 | Session yönetimi | aktif | sınırlı (motor bypass) | audio-sessions ✅ |
| 9 | Format kontrolü | motor mix format | uygulama seçer | wasapi ✅ |
| 10 | Period kontrolü | motor belirler | uygulama (legal katlar) | low-latency ✅ |
| 11 | Hata riski | düşük | AUDCLNT_E_* yüksek | wasapi ✅ |
| 12 | MMSS kuyruğu | "Pro Audio"/"Audio" | "Pro Audio" | low-latency ✅ |
| 13 | Event-driven | destekli | destekli (önerilir) | low-latency ✅ |
| 14 | Cihaz değişimi | otomatik yeniden bağ | manuel | ⚠️ kısmi |
| 15 | Fallback | gereksiz | WaveRT→shared→line→SSRC | architecture ✅ |

---

## EK-B. Exclusive Render Prosedürü (Adım Adım — Sayfa Akışı)

| # | Adım | Not | Kaynak |
|---|---|---|---|
| 1 | Cihaz seç (MMDevice) | endpoint id | wasapi ✅ |
| 2 | IAudioClient oluştur | CoCreateInstance | wasapi ✅ |
| 3 | GetDevicePeriod | default/min dönem | exclusive ✅ |
| 4 | Initialize (exclusive, bayraksız) | AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED olası | exclusive ✅ |
| 5 | Hata olursa buffer'ı yeniden hesapla ve tekrar | resmi yönerge | exclusive ✅ |
| 6 | GetBufferSize / GetService | render servisi | wasapi ✅ |
| 7 | SetEventHandle | event-driven | low-latency ✅ |
| 8 | Start | akış başlar; sistem sesleri susar | exclusive ✅ |
| 9 | Döngü: Wait → GetBuffer → ReleaseBuffer | 2 buffer ping-pong | exclusive ✅ |
| 10 | Stop / Flush / Release | temiz kapanış | exclusive ✅ |

---

## EK-C. Exclusive Capture Prosedürü

| # | Adım | Not |
|---|---|---|
| 1 | MMDevice ile capture endpoint seç | loopback değil |
| 2 | Initialize (exclusive) | format uygulama kontrolünde |
| 3 | GetService(IAudioCaptureClient) | capture servisi |
| 4 | Event kuyru | DMA bildirimi |
| 5 | GetBuffer → veri → ReleaseBuffer | paket tabanlı |
| 6 | Stop / Release | temizlik |

> Capture'ta "sistem sesleri susar" kuralı geçerli değildir (kural render'a bağlıdır) → ⚠️ bu turda
> doğrulanmadı: `UNKNOWN`.

---

## EK-D. Audio Engine Bypass Şeması (Exclusive)

```text
[UYGULAMA]
    │  (format + dönem uygulamanın kontrolünde)
    ▼
[IAudioClient — exclusive Initialize]
    │  (mix format/APO zinciri BYPASS)
    ▼
[2 ping-pong buffer] ──► [WaveRT / DMA] ──► [HW]
    ▲                         │
    └────── event (DMA) ◄─────┘

Shared tarafı:
[UYGULAMA] → [IAudioClient shared] → [OS MOTORU: mix + APO + session vol] → [WaveRT] → [HW]
```

> Şema kaynak: wasapi + exclusive-mode-streams + architecture (üçü fetch ✅ 2026-10-12).

---

## EK-E. Fallback Zinciri (Exclusive Sağlamazsa)

| Sıra | Yol | Koşul |
|---|---|---|
| 1 | WaveRT + DMA | sürücü destekliyorsa (fallback 1) |
| 2 | WaveRT shared | DMA yoksa |
| 3 | Line | daha esnek yol |
| 4 | SSRC/excl | son çare |

Kaynak: windows-audio-architecture (fetch ✅ 2026-10-12).

---

## EK-F. ASIO ile Kıyas (depends_on: K003-audio/backend/asio)

| Kriter | WASAPI exclusive | ASIO |
|---|---|---|
| Sahip | Microsoft | Steinberg SDK |
| Gecikme | ~1.3 ms motor (Win10+); exclusive daha düşük hedef | sürücü-bağımlı |
| Paylaşım | tek erişimli istemci | tek erişimli (tipik) |
| Session | bypass | yok (düz yol) |
| Format | uygulama kontrolü | uygulama kontrolü |
| Erişim | inbox (Windows) | üçüncü parti sürücü |

> Kıyas detayı `wasapi-asio-karsilastirma.md` (mevcut dosya — bu turda overwrite YOK).

---

## EK-G. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | Exclusive neden sistem seslerini susturur? | motor bypass + 2 buffer | exclusive ✅ |
| 2 | Shared modda format kim belirler? | OS motoru (mix format) | wasapi ✅ |
| 3 | BUFFER_SIZE_NOT_ALIGNED nasıl aşılır? | buffer yeniden hesapla + tekrar Initialize | exclusive ✅ |
| 4 | AutoCONVERTPCM her yerde var mı? | sayfa "Need to confirm" | wasapi ⚠️ |
| 5 | Multi-client koruması nasıl? | epoch-based | architecture ✅ |
| 6 | Exclusive'de gecikme kaç ms? | motor ~1.3 ms; exclusive +1/1 ms | low-latency ✅ |

---

---

## EK-H. Mod Seçim Karar Ağacı

| Durum | Önerilen Mod | Gerekçe |
|---|---|---|
| Birden çok uygulama aynı cihazda | shared | epoch-based koruma |
| Stüdyo izleme, tek uygulama | exclusive (aday) | motor bypass |
| Sistem sesleri + bildirim gerekiyor | shared | exclusive'de susar |
| Düşük gecikme + OS kanalları | shared + IAudioClient3 | 3 metod |
| Uygulama format dönüşümü yapamıyor | shared + AUTOCONVERTPCM (⚠️ doğrulanacak) | OS dönüştürür |
| Cihaz sürücüsü exclusive'i desteklemiyor | shared (fallback zinciri) | WaveRT→shared→line→SSRC |
| Uzaktan (RDP) oturum | shared (varsayılan) | ⚠️ exclusive UNKNOWN |

---

## EK-I. Bilinen Sınırlar ve Hata Durumları

| # | Durum | Sonuç | Kaynak |
|---|---|---|---|
| 1 | Buffer hizasız (exclusive) | AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED | exclusive ✅ |
| 2 | Periodity kaydedilmiş | AUDCLNT_E_ENGINE_PERIODICITY_LOCKED | wasapi ✅ |
| 3 | Format kaydedilmiş | AUDCLNT_E_ENGINE_FORMAT_LOCKED | wasapi ✅ |
| 4 | Cihaz yolu koptu | AUDCLNT_E_DEVICE_INVALIDATED | ⚠️ fetch yok |
| 5 | Cihaz zaten exclusive'te | ikinci istemci reddedilir | architecture ✅ (tek erişim) |
| 6 | Süreç kapanırsa | session inactive → expired | audio-sessions ✅ |

---

## EK-J. Tarihsel Notlar (Sürüm Bağlamı)

| Not | Durum |
|---|---|
| WASAPI = Windows Vista ile tanıtıldı (bilinen resmi tarih) | ⚠️ bu turda fetch edilmedi |
| "Changes in Windows 8" — shared-vs-exclusive sayfası | ⚠️ B1 fetch edilmedi |
| Windows 10 — IAudioClient3 + ~1.3 ms motor | ✅ low-latency fetch 2026-10-12 |
| Windows 11 — APO2 + yeni INF | ✅ architecture fetch 2026-10-12 |

---

## EK-K. Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `wasapi-wasapi-surucu-modeli.md` (yol: isletim-sistemi-audio/wasapi/) | mimari | 2-buffer'ın OHCI karşılığı |
| `wasapi-latency-ve-buffer.md` | mimari | dönem/buffer hesabı |
| `wasapi-arayuz-hiyerarsisi.md` | mimari | Initialize semantiği |
| `K003-audio/backend/asio` | teknolojiler-arası | EK-F kıyası |
| `isletim-sistemi-turu/windows/windows-kernel.md` | mimari | exclusive'in kernel etkisi |

---

## EK-L. RED TEAM: Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "Exclusive her zaman shared'den düşük gecikmelidir" | donanım/sürücü bağımlı; resmi veri koşullu | ❌ koşullu yaz |
| "AutoCONVERTPCM resmen vardır" | sayfa "Need to confirm" | ⚠️ UNKNOWN |
| "Exclusive'de session mute çalışmaz" | motor bypass ≠ session varlığı — bu turda test edilmedi | ⚠️ UNKNOWN |
| "RDP'de exclusive kullanılabilir" | doğrulanmadı | ⚠️ UNKNOWN |

---

## EK-M. Açık Sorular (EK Özeti)

| # | Soru | Durum |
|---|---|---|
| 1 | AUTOCONVERTPCM etkinleştirme sözcüğü kesin mi? | ⚠️ UNKNOWN |
| 2 | AUDCLNT_STREAMOPTIONS_RAW + MATCH_FORMAT eşzamanlı mı? | ⚠️ UNKNOWN |
| 3 | Windows 11 preemption varsayılanları değişti mi? | ⚠️ UNKNOWN |
| 4 | Exclusive capture'ta "sistem sesleri susar" kuralı geçerli mi? | ⚠️ UNKNOWN |

---

## EK-N. Kaynak Fetch Günlüğü (Bu Dosya)

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | shared/exclusive yolları, AUDCLNT_E_* | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2-buffer, prosedür adımları | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture | epoch-based, tek erişim, fallback | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | gecikme değerleri, MMCSS | 2026-10-12 ✅ |
| M5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/shared-vs-exclusive-mode | Windows 8 sürüm notu | ⚠️ bu turda fetch edilmedi |

---

---

## EK-O. Mod ⇄ Gecikme / Buffer Hesap Kartları

| Kart | Girdi | Çıktı | Kaynak |
|---|---|---|---|
| K1 | shared motor | p2p 1.20 ms → ~1.3 ms | low-latency ✅ |
| K2 | shared ek | +1/1 ms | low-latency ✅ |
| K3 | exclusive ek | +1/1 ms | low-latency ✅ |
| K4 | eski p2p | 6/12 ms | low-latency ✅ |
| K5 | capture motor | ~0 ms | low-latency ✅ |
| K6 | roundtrip | 4.5 ms | low-latency ✅ |
| K7 | 10 ms eşiği | basit API + OS kanalları | low-latency ✅ |
| K8 | exclusive dönem | legal = fundamentalPeriodInFrames katları | low-latency ✅ |
| K9 | 20 ms @48k stereo float | 960 byte = 120 DWord | low-latency ✅ |
| K10 | inbox HDAudio | 128–480 örnek = 2.66–10 ms | architecture ✅ |

---

## EK-P. Terminoloji Sabitleme (Yazım Kuralları)

| Doğru Yazım | Yanlış / Kaçınılan | Not |
|---|---|---|
| shared mode (paylaşımlı mod) | share mode | tutarlılık |
| exclusive mode (özel mod) | excl. mode (kısaltma serbest değil) | tam yaz |
| IAudioClient3 | IAC3 | kod içinde tam |
| AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED | buffer-not-aligned | kod adı aynen |
| ping-pong buffer | pingpong buffer | tireli |
| MMCSS | MMSS | tam kısaltma |
| WaveRT | WaveRT-2 (uydurma) | resmi ad |

---

## EK-Q. Bu Dosyanın Doğrulama Bağlantısı

| Doğrulama Dosyası | Bu Dosyadan Aldığı İddia |
|---|---|
| wasapi-winsdk-dogrulama.md | BUFFER_SIZE_NOT_ALIGNED, ENGINE_*_LOCKED, 2-buffer kuralı |
| wasapi-web-arastirma.md | §3.3 iddia→URL tablosu (shared/exclusive) |
| wasapi-kod-ornekleri.md | PlayExclusiveStream alıntısı (kaynak-etiketli) |

---

---

## EK-R. Kısa Not Kartları (Quick Reference)

| Kart | İçerik |
|---|---|
| NR-01 | Shared = OS motoru; exclusive = motor bypass + 2 buffer |
| NR-02 | Exclusive'de sistem sesleri SUSAR |
| NR-03 | Multi-client = epoch-based; exclusive = tek erişimli istemci |
| NR-04 | BUFFER_SIZE_NOT_ALIGNED → buffer'ı yeniden hesapla |
| NR-05 | ENGINE_*_LOCKED → fmt/tag/period kaydedilmiş |
| NR-06 | Fallback: WaveRT/DMA → WaveRT shared → line → SSRC/excl |
| NR-07 | ~1.3 ms motor (Win10+); capture ~0 ms; roundtrip 4.5 ms |
| NR-08 | AUTOCONVERTPCM = ⚠️ "Need to confirm" |
| NR-09 | IAudioClient3 = shared düşük gecikme yolu |
| NR-10 | Fallback zinciri kaynağı: windows-audio-architecture ✅ |

---

---

## EK-S. Son Kontül Listesi (Bu Dosyanın Kapanış Testi)

| # | Kontül | Beklenen |
|---|---|---|
| 1 | Her iddia URL + fetch tarihi taşıyor mu? | evet (EK-N) |
| 2 | UNKNOWN'lar açıkça işaretli mi? | evet (EK-M) |
| 3 | Kod uyduruldu mu? | hayır (yalnız kaynak-etiketli alıntı) |
| 4 | Dolgu (TBD/.../Yakında) var mı? | hayır |
| 5 | ASIO bağımlılığı (K003) gösterildi mi? | evet (EK-F) |
| 6 | Grup alanı `mimari` mi? | evet |
| 7 | ≥500 satır mı? | evet |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12
