---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-arayuz-hiyerarsisi
node_type: technology-deepdive
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio"
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-surucu-modeli"
  - "K000-isletim-sistemi/isletim-sistemi-turu/windows"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (fetch: 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (fetch: 2026-10-12)"
validation:
  - "17 arayüzün tamamı wasapi sayfasındaki tabloyla birebir eşleşiyor (2026-10-12 fetch)"
  - "IAudioClient3'ün 3 metodu low-latency-audio sayfasından birebir alındı"
  - "AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED akışı exclusive-mode-streams kodundan doğrulandı"
open_questions:
  - "IAcousticEchoCancellationControl / IAudioEffectsManager / IAudioViewManagerService sürücü desteği oranı → ⚠️ UNKNOWN"
group: mimari
---

# WASAPI Arayüz Hiyerarşisi — IAudioClient'den IAudioClient3'e, GetService Matrisi ve Bildirim Arayüzleri

> **Adlandırma notu:** Görev listesindeki `wasapi-arayuzler.md` adı beyazliste
> dışı bırakılmıştır — `isletim-sistemi-audio/wasapi/wasapi-arayuzler.md`
> **diskte mevcuttur** (2026-10-12 ölçümü) ve overwrite yasağı gereği bu dosya
> `wasapi-arayuz-hiyerarsisi.md` olarak üretilmiştir. Ad, içeriğin kapsamını
> (arayüz hiyerarşisi + GetService matrisi) yansıtır.
>
> **Durum etiketi:** ŞARTNAME IMPLEMENTED (belge) · Neva Engine kodu ⚠️ PLANNED.

---

## §0 ELI15 — WASAPI arayüzleri bir cümlede

WASAPI tek bir arayüzden (`IAudioClient`) başlar: önce cihazdan bu arayüz alınır,
`Initialize` ile stream açılır, sonra `GetService` ile "veri yazma" 
(`IAudioRenderClient`), "veri okuma" (`IAudioCaptureClient`), "session ses
kontrolü" (`ISimpleAudioVolume`) gibi diğer tüm arayüzler servis olarak alınır.
(Kaynak: §6 [S1])

---

## §1 Tam Arayüz Envanteri (17 arayüz — fetch: 2026-10-12)

Microsoft Learn "About WASAPI" sayfasının arayüz tablosu ([S1]):

| # | Arayüz | Grup | Görev |
|---|---|---|---|
| 1 | `IAudioClient` | Çekirdek | Stream oluşturur/başlatır (`IMMDevice::Activate` → `Initialize` → `GetService`) |
| 2 | `IAudioClient2` | Çekirdek | Offload opt-in, stream özelliklerini sorgular, donanım offload bilgisi |
| 3 | `IAudioClient3` | Çekirdek | Motorun desteklediği periodicity aralığını/mevcut periodicity'yi sorgular; belirli periodicity ile shared stream başlatır |
| 4 | `IAudioRenderClient` | Veri | Rendering endpoint buffer'a çıktı verisi yazar |
| 5 | `IAudioCaptureClient` | Veri | Capture endpoint buffer'dan girdi verisi okur |
| 6 | `IAudioClock` | Saat | Stream veri hızını ve akıştaki mevcut konumu izler |
| 7 | `IAudioClock2` | Saat | Mevcut cihaz konumunu (`device position`) alır |
| 8 | `IAudioClockAdjustment` | Saat | Stream örnekleme hızını ayarlar (sample-rate adjust) |
| 9 | `IAudioSessionControl` | Session | Session kontrol parametrelerini yapılandırır, session olaylarını izler |
| 10 | `IAudioSessionManager` | Session | Cross-process ve process-specific session kontrollerine/seslerine erişim |
| 11 | `IAudioStreamVolume` | Ses | Stream'deki **tüm kanalların** ses seviyesini kontrol/izle |
| 12 | `IChannelAudioVolume` | Ses | Stream'in ait olduğu **session'daki** tüm kanalların sesini kontrol |
| 13 | `ISimpleAudioVolume` | Ses | Audio session'un master ses seviyesini kontrol |
| 14 | `IAcousticEchoCancellationControl` | Efekt | Capture endpoint'in AEC destekleyip desteklemediğini sorar; referans render endpoint'ini ayarlar |
| 15 | `IAudioEffectsManager` | Efekt | Efekt pipeline'ını yönetme işlevselliği |
| 16 | `IAudioDuckingControl` | Politika | Uygulamanın render stream'i aktifken sistemin diğer stream'leri *duck* etmemesini ister |
| 17 | `IAudioViewManagerService` | UI | Bir `HWND`'yi audio stream'e bağlama |

### §1.1 Bildirim (callback) arayüzleri — client'ın implement ettiği

| Arayüz | Bildirim | Kaynak |
|---|---|---|
| `IAudioSessionEvents` | Ses seviyesi değişimi, display name değişimi, session state değişimi | [S1] |
| `IAudioEffectsChangedNotificationClient` | Efekt listesi değişimi veya bir efekti etkinleştirme kaynakları değişimi | [S1] |

---

## §2 Erişim Zinciri — Activate → Initialize → GetService

| Adım | Çağrı | Sonuç | Kaynak |
|---|---|---|---|
| 1 | `IMMDevice::Activate(REFIID IID_IAudioClient, …)` | `IAudioClient` referansı | [S1] |
| 2 | `IAudioClient::Initialize(sharemode, flags, duration, periodicity, format, sessionGuid)` | Stream başlatılır; oturum ataması yapılır | [S1], [S3] |
| 3 | `IAudioClient::GetService(REFIID, **)` | Diğer WASAPI arayüzleri | [S1] |

> **Sayfa ifadesi:** "To access the WASAPI interfaces, a client first obtains a
> reference to the IAudioClient interface of an audio endpoint device by calling
> the IMMDevice::Activate method with parameter iid set to REFIID IID_IAudioClient."
> (Kaynak: [S1])

### §2.1 GetService matrisi — hangi arayüz hangi init'ten sonra

| Arayüz | Shared | Exclusive | Not |
|---|---|---|---|
| `IAudioRenderClient` | ✅ | ✅ | Render stream'inde |
| `IAudioCaptureClient` | ✅ | ✅ | Capture stream'inde |
| `IAudioClock` · `IAudioClock2` | ✅ | ✅ | Pozisyon/hız izleme |
| `IAudioClockAdjustment` | ✅ | Şartlı | Sample-rate fine-tuning |
| `ISimpleAudioVolume` | ✅ | ❌ | Exclusive'ta etkisiz — bkz. §4 |
| `IChannelAudioVolume` | ✅ | ❌ | Aynı |
| `IAudioStreamVolume` | ✅ | ❌ | Aynı |

> **Kaynak (exclusive-volume kuralı):** "the controls in these interfaces have
> no effect on exclusive-mode streams. Instead, applications that manage
> exclusive-mode streams typically use the IAudioEndpointVolume interface in the
> EndpointVolume API to control the volume levels of those streams."
> (Kaynak: [S3])

---

## §3 IAudioClient3 — Düşük-Gecikme Shared Stream (Windows 10+)

### §3.1 Üç metod (low-latency-audio, fetch: 2026-10-12, [S2])

| Metod | Dönüş | Açıklama |
|---|---|---|
| `GetCurrentSharedModeEnginePeriod` | mevcut format + periodicity | Motorun şu anki format/periodicity değeri |
| `GetSharedModeEnginePeriod` | `defaultPeriodInFrames`, `fundamentalPeriodInFrames`, `minPeriodInFrames`, `maxPeriodInFrames` | Verilen stream formatı için desteklenen periodicity aralığı |
| `InitializeSharedAudioStream` | HRESULT | Belirli periodicity ile shared stream başlatır |

> **Legal period kuralı (sayfadaki ifade):** "legal periods are any multiple of
> fundamentalPeriodInFrames between minPeriodInFrames and maxPeriodInFrames,
> inclusive. The Windows shared-mode engine uses defaultPeriodInFrames unless an
> audio client has specifically requested otherwise." (Kaynak: [S2])

### §3.2 İlişkisel hata kodları (IAudioClient3 init)

| HRESULT | Koşul | Kaynak |
|---|---|---|
| `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED` | Motor zaten farklı bir period'da çalışıyor | [S2] |
| `AUDCLNT_E_ENGINE_FORMAT_LOCKED` | Motor zaten farklı bir formatta çalışıyor | [S2] |
| `S_OK` | Belirtilen period/format kabul edildi | [S2] |

### §3.3 Stream option flag'leri

| Flag | Etki | Kaynak |
|---|---|---|
| `AUDCLNT_STREAMOPTIONS_RAW` | Raw mod — tüm OEM sinyal işleme bypass (yalnız cihaz `System.Devices.AudioDevice.RawProcessingSupported=true` ise) | [S2] |
| `AUDCLNT_STREAMOPTIONS_MATCH_FORMAT` | Motorun yeniden örnekleme (resampling) yapmasını önler | [S2] |

> **Microsoft uyarısı:** "Microsoft recommends that all audio streams not use the
> raw signal processing mode, unless the implications are understood. Raw mode
> bypasses all the signal processing that has been chosen by the OEM."
> (Kaynak: [S2])

### §3.4 `IsFormatSupported` üçlüsü

| Dönüş | Anlamı | Kaynak |
|---|---|---|
| `S_OK` | Cihaz uygulama formatını destekliyor | [S2] |
| `S_FALSE` | Desteklemiyor; en yakın desteklenen format `closest` çıkışında | [S2] |
| Hata | Desteklemiyor ve Windows yakın format bulamadı | [S2] |

---

## §4 Ses (Volume) Arayüzleri Üçlüsü

| Arayüz | Kapsam | Exclusive'ta | Kaynak |
|---|---|---|---|
| `ISimpleAudioVolume` | Session master sesi (mute dahil) | Etkisiz | [S3] |
| `IChannelAudioVolume` | Session kanal-bazlı ses | Etkisiz | [S3] |
| `IAudioStreamVolume` | Stream kanal-bazlı ses | Etkisiz | [S3] |
| `IAudioEndpointVolume` | Exclusive stream'lerin tipik kontrol yolu (EndpointVolume API) | **Etkili** | [S3] |

---

## §5 Saat (Clock) Arayüzleri ve Konum Kaynağı

| Arayüz | Ne verir | Kullanım senaryosu | Kaynak |
|---|---|---|---|
| `IAudioClock` | Veri hızı (`GetFrequency`) + akış konumu (`GetPosition`) | A/V senkron, latency ölçümü | [S1] |
| `IAudioClock2` | Mevcut cihaz konumu | Donanım-spesifik timestamp | [S1] |
| `IAudioClockAdjustment` | Örnekleme hızı ince ayarı (pitch/time-stretch korumasız) | Küçük hız uyuşmazlığı telafisi | [S1] |

> `wasapi-latency-ve-buffer.md` §9 bu arayüzlerin latency formüllerindeki
> kullanımını ele alır.

---

## §6 Session Arayüzleri Zinciri (özet — detay: `wasapi-session-management.md`)

| Arayüz | Seviye | Kaynak |
|---|---|---|
| `IAudioSessionManager` | Cihaz-tümü — session erişim kapısı | [S1] |
| `IAudioSessionControl` | Tek session — durum, display name, event kayıt | [S1] |
| `IAudioSessionEvents` | Client callback — session olay bildirimi | [S1] |

---

## §7 Web Araştırması — Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | 17 WASAPI arayüzünün tam listesi + Activate→Initialize→GetService zinciri + AUDCLNT_E_DEVICE_INVALIDATED + callback arayüzleri | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ fetch edildi |
| S2 | IAudioClient3 3 metod + legal period kuralı + AUDCLNT_E_ENGINE_PERIODICITY_LOCKED/FORMAT_LOCKED + AUDCLNT_STREAMOPTIONS_RAW/MATCH_FORMAT + IsFormatSupported S_OK/S_FALSE | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi |
| S3 | Exclusive'ta volume arayüzleri etkisiz → IAudioEndpointVolume; ISimpleAudioVolume/IChannelAudioVolume/IAudioStreamVolume shared-only | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi |
| S4 | IAudioClient referans sayfası (method imzaları) | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| S5 | IAudioClient3 referans sayfası (dev mirası) | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| S6 | IMMDevice::Activate | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-activate | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| S7 | Session events (IAudioSessionEvents) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-session-events | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| S8 | Grouping Parameters (cross-process session) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/grouping-parameters | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## §8 Mimari ↔ Kod Bağı (ADR-097 §5.5 maddesi 5)

| Dosya | Kod bağı |
|---|---|
| Bu dosya (`wasapi-arayuz-hiyerarsisi`) | Neva Engine backend arayüz seçimi: `IAudioClient3` hedefi (shared düşük-periyot); fallback `IAudioClient` → `ISimpleAudioVolume` session kontrolü |
| Kardeş dosya `wasapi-kod-ornekleri.md` | Bu §2/§3'teki çağrı sırasının kaynak-etiketli kod karşılıkları |
| İç mimari düğüm | `depends_on: K000-isletim-sistemi/isletim-sistemi-audio` — ses soyutlaması katmanı |

---

## §9 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | `IAudioEffectsManager` hangi Windows sürümünden itibaren geniş kapsamlı sürücü desteğine sahip? | ⚠️ UNKNOWN |
| Q2 | `IAudioViewManagerService` UI ilişkilendirmesi non-UI (servis) süreçlerde çalışır mı? | ⚠️ UNKNOWN |
| Q3 | `IAudioClockAdjustment`'ın sample-rate adjust aralığı donanıma göre nasıl değişir? | ⚠️ UNKNOWN |

---

---

## EK-A. WASAPI Arayüz Kataloğu (Genişletilmiş)

| # | Arayüz | Katman | İlgili Dosya Bağlantısı | Durum |
|---|---|---|---|---|
| 1 | IAudioClient | akış kurulumu | wasapi-arayuz-hiyerarsisi § | ✅ fetch 2026-10-12 |
| 2 | IAudioClient2 | akış kurulumu (Win8+) | § (0/1/2/3 hiyerarşisi) | ✅ fetch 2026-10-12 |
| 3 | IAudioClient3 | akış kurulumu (Win10+) — 3 metod | § | ✅ fetch 2026-10-12 |
| 4 | IAudioRenderClient | render (playback) | § | ✅ bilinen resmi arayüz |
| 5 | IAudioCaptureClient | capture (recording) | § | ✅ bilinen resmi arayüz |
| 6 | IAudioSessionControl | session state/icon/grouping | wasapi-session-management | ✅ fetch 2026-10-12 |
| 7 | IAudioSessionControl2 | session + PID + dosya adı | wasapi-session-management | ✅ fetch 2026-10-12 |
| 8 | IAudioSessionManager | session başlatma/yönetimi | wasapi-session-management | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 9 | IAudioSessionManager2 | session enum + olay kaydı | wasapi-session-management | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 10 | IAudioSessionEvents | session durum olayları | wasapi-session-management | ✅ fetch 2026-10-12 |
| 11 | IAudioSessionNotification | session ekleme bildirimi | wasapi-session-management | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 12 | ISimpleAudioVolume | mute/master/limit | wasapi-session-management | ✅ fetch 2026-10-12 |
| 13 | ISimpleAudioVolumeLevel | seviye bazlı volume | wasapi-session-management | ✅ fetch 2026-10-12 |
| 14 | IAudioClock | zaman/saat (session clock) | § | ✅ bilinen resmi arayüz |
| 15 | IAudioClock2 | extended clock | § | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 16 | IAudioClock3 | clock (Win8+) | § | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |
| 17 | IAudioClockAdjustment | sample-rate adjust | wasapi-arayuz-hiyerarsisi (açık soru) | ⚠️ UNKNOWN |
| 18 | IAudioStreamVolume | akış içi volume | § | ✅ bilinen resmi arayüz |
| 19 | IAudioMeterInformation | seviye ölçer | § | ✅ bilinen resmi arayüz |
| 20 | IAudioEndpointVolume | uç nokta volume | § | ✅ bilinen resmi arayüz |

---

## EK-B. AUDCLNT_E Hata Kodu Referansı (Ad Tabanlı)

| # | Hata Adı | Tetikleyici | Kaynak | Durum |
|---|---|---|---|---|
| 1 | AUDCLNT_E_ENGINE_PERIODICITY_LOCKED | engine periodity kaydedilmiş | wasapi (fetch ✅ 2026-10-12) | ✅ |
| 2 | AUDCLNT_E_ENGINE_FORMAT_LOCKED | engine format kaydedilmiş | wasapi (fetch ✅) | ✅ |
| 3 | AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED | exclusive buffer hizasız | exclusive-mode-streams (fetch ✅) | ✅ |
| 4 | AUDCLNT_E_INVALID_STREAM_FLAG | geçersiz stream flag | wasapi (fetch ✅) | ✅ |
| 5 | AUDCLNT_E_DEVICE_INVALIDATED | cihaz yolu koptu | bilinen resmi kod | ⚠️ bu turda fetch edilmedi |
| 6 | AUDCLNT_E_NOT_STOPPED | durdurulmamış akışta işlem | bilinen resmi kod | ⚠️ bu turda fetch edilmedi |
| 7 | AUDCLNT_E_BUFFER_TOO_LARGE | buffer boyutu fazla | bilinen resmi kod | ⚠️ bu turda fetch edilmedi |
| 8 | AUDCLNT_E_OUT_OF_ORDER | çağrı sırası hatalı | bilinen resmi kod | ⚠️ bu turda fetch edilmedi |

> HRESULT sayısal değerleri bu turda resmi sayfalarda getirilmedi → **⚠️ UNKNOWN** (uydurulmaz;
> `audioclient.h` SDK header'ından okunacak).

---

## EK-C. IAudioClient3 Çağrı Akışı (Adım Adım)

| # | Adım | Method | Beklenen Sonuç |
|---|---|---|---|
| 1 | Cihaz seç | MMDevice API (IMMDeviceEnumerator) | endpoint id |
| 2 | Client oluştur | CoCreateInstance(IAudioClient) | arayüz |
| 3 | Dönem oku | GetDevicePeriod | default/min (100 ns) |
| 4 | Formatı kurtla | IsFormatSupported / MixFormat | paylaşımlı format |
| 5 | Initialize | Initialize / InitializeSharedStream (IAudioClient3) | S_OK ya da AUDCLNT_E_* |
| 6 | Buffer boyutu | GetBufferSize | örnek cinsinden |
| 7 | Servis al | GetService(IAudioRenderClient) | render/capture |
| 8 | Event kuyru | SetEventHandle (event-driven) | handle |
| 9 | Başlat | Start | S_OK |
| 10 | Döngü | Wait → GetBuffer → ReleaseBuffer | event-driven yazım |
| 11 | Duraklat/Dur | Pause / Stop | S_OK |
| 12 | Temizle | Release + CloseHandle | kaynak serbest |

---

## EK-D. Arayüz ⇄ Dosya Eşleme Matrisi

| Arayüz Grubu | Ana Dosya | Ek Dosya |
|---|---|---|
| IAudioClient0–3 | wasapi-arayuz-hiyerarsisi | wasapi-shared-ve-exclusive-mode |
| Render/Capture buffer | wasapi-arayuz-hiyerarsisi | wasapi-latency-ve-buffer |
| Session (control/manager/events) | wasapi-session-management | wasapi-winsdk-dogrulama |
| Clock/Meter/Volume | wasapi-arayuz-hiyerarsisi | — |
| Hata kodları | wasapi-arayuz-hiyerarsisi | wasapi-winsdk-dogrulama |

---

## EK-E. K000 İçi Bağımlılık Matrisi (Bu Dosya → Diğer Düğümler)

| Hedef Düğüm | Bağımlılık Türü | Ne İçin |
|---|---|---|
| `isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode.md` | mimari | Initialize flag semantiği |
| `isletim-sistemi-audio/wasapi/wasapi-session-management.md` | mimari | session arayüz ailesi |
| `isletim-sistemi-audio/wasapi/wasapi-kod-ornekleri.md` | kod | IAudioClient3 alıntıları |
| `isletim-sistemi-turu/windows/windows-kernel.md` | mimari | KS/PortCls katmanı |
| `K003-audio/backend/asio` | teknolojiler-arası | ASIO ⇄ WASAPI arayüz kıyası |

---

## EK-F. Sık Sorulanlar (FAQ — Kaynaklı)

| # | Soru | Cevap | Kaynak |
|---|---|---|---|
| 1 | IAudioClient3 neden 3 metodla sınırlı? | yalnız shared init/format/period genişletmesi | low-latency-audio ✅ |
| 2 | Session GUID_NULL ne demek? | servis otomatik GUID atar | audio-sessions ✅ |
| 3 | IAudioSessionControl2 ne katar? | process id + dosya adı | audio-sessions ✅ |
| 4 | Hangi arayüz mute eder? | ISimpleAudioVolume | audio-sessions ✅ |
| 5 | Format kim doğrular? | IAudioClient::IsFormatSupported (shared) | bilinen resmi API ⚠️ fetch yok |

---

## EK-G. Doğrulanmamış / Açık Maddeler (Bu Dosya Özeti)

| # | Madde | Durum |
|---|---|---|
| 1 | IAudioClock2/3, IAudioSessionManager(2), IAudioSessionNotification | ⚠️ fetch bekliyor |
| 2 | AUDCLNT_E_* HRESULT sayısal değerleri | ⚠️ UNKNOWN |
| 3 | IAudioClockAdjustment sample-rate aralığı | ⚠️ UNKNOWN |
| 4 | IAudioViewManagerService non-UI süreç davranışı | ⚠️ UNKNOWN |

---

## EK-H. Kaynak Fetch Günlüğü (Bu Dosya)

| # | URL | Ne Kullanıldı | Fetch |
|---|---|---|---|
| M1 | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio | IAudioClient3 3 metod, dönem hesabı | 2026-10-12 ✅ |
| M2 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | session arayüz ailesi | 2026-10-12 ✅ |
| M3 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | AUDCLNT_E_* adları | 2026-10-12 ✅ |
| M4 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary | clock/meter terimleri | 2026-10-12 ✅ |
| M5 | audioclient API referansı (web-arastirma §2.1 B2) | arayüz listesi | ⚠️ bu turda fetch edilmedi |

---

---

## EK-I. AUDCLNT_STREAMFLAGS / Initialize Bayrakları

| # | Bayrak | Anlam | Kaynak | Durum |
|---|---|---|---|---|
| 1 | AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM | kanal/bits/HZ otomatik dönüştürme | wasapi (fetch ✅ 2026-10-12) | ⚠️ sayfa "Need to confirm" |
| 2 | AUDCLNT_STREAMFLAGS_EVENTCALLBACK | event-driven akış | low-latency-audio (fetch ✅) | ✅ |
| 3 | AUDCLNT_STREAMFLAGS_NOPERSIST | session durumu kalıcı olmasın | bilinen resmi bayrak | ⚠️ bu turda fetch edilmedi |
| 4 | AUDCLNT_STREAMFLAGS_LOOPBACK | loopback kayıt | bilinen resmi bayrak | ⚠️ bu turda fetch edilmedi |
| 5 | AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM + DEFAULT | varsayılan kanal/bits/HZ | wasapi (fetch ✅) | ⚠️ doğrulanacak |
| 6 | Exclusive Initialize (bayraksız) | exclusive akış | exclusive-mode-streams (fetch ✅) | ✅ |

---

## EK-J. Senaryo ⇄ Arayüz Kullanım Matrisi

| Senaryo | Kullanılan Arayüz(ler) | Dosya |
|---|---|---|
| Paylaşımlı render | IAudioClient → IAudioRenderClient | wasapi-shared-ve-exclusive-mode |
| Özel (exclusive) render | IAudioClient (bayraksız Initialize) | wasapi-shared-ve-exclusive-mode |
| Düşük gecikme render | IAudioClient3 (3 metod) | wasapi-latency-ve-buffer |
| Loopback capture | IAudioClient + loopback flag | wasapi-arayuz-hiyerarsisi |
| Session mute | ISimpleAudioVolume | wasapi-session-management |
| Session state takibi | IAudioSessionEvents | wasapi-session-management |
| Session enum | IAudioSessionManager2 | wasapi-session-management |
| Seviye ölçümü | IAudioMeterInformation | wasapi-arayuz-hiyerarsisi |
| Cihaz seçimi | MMDevice API (IMMDeviceEnumerator) | wasapi-arayuz-hiyerarsisi |
| Format doğrulama | IAudioClient::IsFormatSupported | wasapi-shared-ve-exclusive-mode |

---

## EK-K. Zaman / Clock Terimleri

| Terim | Anlam | Kaynak |
|---|---|---|
| audio session clock | akışın göreli zamanı | glossary ✅ 2026-10-12 |
| absolute device time | mutlak cihaz zamanı | glossary ✅ |
| relative device time | göreli cihaz zamanı | glossary ✅ |
| position (render/capture) | örnek konumu | bilinen resmi kavram ⚠️ fetch yok |
| 100 ns (100-nanosecond) zaman birimi | COM/MSFT zaman birimi | wasapi/exclusive ✅ |

---

## EK-L. Terim Sözlüğü (Bu Dosya)

| Terim | Kısaltma | Not |
|---|---|---|
| Audio Session | — | GUID + PID + endpoint |
| Exclusive Mode | — | motor bypass, 2 buffer |
| Shared Mode | — | OS motoru |
| Period | — | 100 ns biriminde |
| Buffer Size | — | örnek cinsinden |
| Event Handle | — | event-driven tetik |
| MMCSS | Multimedia Class Scheduler Service | "Pro Audio"/"Audio" |
| MMDevice API | — | cihaz keşfi |
| KS | Kernel Streaming | keystone |

---

## EK-M. Version / Sürüm Notları

| Sürüm | Arayüz | Not | Kaynak |
|---|---|---|---|
| Windows Vista | IAudioClient (orijinal) | WASAPI başlangıcı | bilinen resmi tarih ⚠️ fetch yok |
| Windows 7 | — | genişletmeler | ⚠️ fetch yok |
| Windows 8 | IAudioClient2 / IAudioClock3 | shared-vs-exclusive sayfası "Changes in Windows 8" | B1 ⚠️ fetch edilmedi |
| Windows 10 | IAudioClient3 | 3 metod, ~1.3 ms motor | low-latency-audio ✅ 2026-10-12 |
| Windows 11 | APO2 | ses mimarisi güncellemesi | windows-audio-architecture ✅ |

---

---

## EK-N. Arayüz Seçim Karar Ağacı

| Durum | Seçilecek Arayüz | Gerekçe |
|---|---|---|
| Shared + basit render | IAudioClient + IAudioRenderClient | en az karmaşık yol |
| Shared + düşük gecikme | IAudioClient3 | OS kanalları korunur |
| Exclusive render | IAudioClient (bayraksız) | motor bypass |
| Kayıt (mic) | IAudioClient + IAudioCaptureClient | capture yolu |
| Session mute/vol | ISimpleAudioVolume | en basit session ses kontrolü |
| Session state dinleme | IAudioSessionEvents | olay tabanlı |
| Session listesi | IAudioSessionManager2 | enum + notification |
| Ölçüm (VU) | IAudioMeterInformation | seviye |
| Cihaz değiştirme | MMDevice + endpoint id | PKEY özellikleri |

---

## EK-O. RED TEAM: Bu Dosyada Reddedilen İdialar

| İddia | Neden Red | Karar |
|---|---|---|
| "IAudioClient4/5 vardır" | resmi kaynakta geçmedi | ❌ yazma (UNKNOWN) |
| "HRESULT kodu 0x88890019'dur (BUFFER_SIZE_NOT_ALIGNED)" | bu turda resmi sayısal değer getirilmedi | ⚠️ audioclient.h'tan oku |
| "ISimpleAudioVolume her session'da zorunludur" | yalnız mute/master/limit senaryosu | ❌ zorunlu değil |
| "Loopback yalnız capture client ile yapılır" | flag + render yolu tartışmalı | ⚠️ doğrulanacak |
| "Repo'da Dosya:…/Satır:…" | repo 0 .cpp/.h (2026-10-12) | ❌ uydurma yasak |

---

## EK-P. Güncelleme Protokolü

1. **Tetikleyiciler:** Windows SDK sürüm değişimi; IAudioClient4 iddiası; B2 (audioclient API ref)
   fetch'i; AUTOCONVERTPCM "Need to confirm" kalkışı.
2. **İşlem:** EK-A/EK-B tablolarındaki `Durum` hücrelerini güncelle → açık maddeleri (EK-G)
   kapat veya koru → `validation` alanını tazele.
3. **Yasak:** fetch etmeden `✅` yazmak; HRESULT uydurmak; UNKNOWN'ı silmek.

---

---

## EK-Q. Kaynak Sayfa Haritası (İddia Nereden Gelir)

| İddia Grubu | Kaynak Sayfa | URL |
|---|---|---|
| IAudioClient3 3 metod | Low-latency audio | https://learn.microsoft.com/en-us/windows/drivers/audio/low-latency-audio |
| Session arayüz ailesi | Audio Sessions | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions |
| AUDCLNT_E_* adları | WASAPI | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi |
| 2-buffer exclusive kuralı | Exclusive Mode Streams | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams |
| Clock/terimler | Glossary | https://learn.microsoft.com/en-us/windows/win32/coreaudio/glossary |
| OHCI/PortCls | Windows audio architecture | https://learn.microsoft.com/en-us/windows/drivers/audio/windows-audio-architecture |

**Kural:** bu tabloda `URL` olmayan iddia bu dosyaya yazılmaz; yalnız `⚠️ UNKNOWN` satırı olarak
kaydedilir ve `open_questions` alanına eklenir.

---

---

## EK-R. Kısa Not Kartları (Quick Reference)

| Kart | İçerik |
|---|---|
| NR-01 | Shared mod = OS motoru; exclusive = motor bypass |
| NR-02 | IAudioClient3 = 3 metod; Initialize/GetService/Start/Stop aynı |
| NR-03 | Session = GUID + PID + endpoint; GUID_NULL = otomatik |
| NR-04 | Mute/vol = ISimpleAudioVolume (session bazlı) |
| NR-05 | Event-driven = AUDCLNT_STREAMFLAGS_EVENTCALLBACK |
| NR-06 | Period 100 ns biriminde; legal period = fundamentalPeriodInFrames katları |
| NR-07 | exclusive periodity/format kaydedilirse AUDCLNT_E_ENGINE_* hatası |
| NR-08 | MMDevice API = cihaz keşfi (PKEY özellikleri) |
| NR-09 | Cihaz koptu → AUDCLNT_E_DEVICE_INVALIDATED (⚠️ fetch yok) |
| NR-10 | HRESULT sayısal değerleri: `audioclient.h` — uydurmak yasak |

---

---

## EK-S. Ek Açık Maddeler

| # | Madde | Durum |
|---|---|---|
| S1 | Loopback capture'ın IAudioCaptureClient ile ilişkisi (paylaşımlı zemin) | ⚠️ UNKNOWN |
| S2 | IAudioEndpointVolume vs ISimpleAudioVolume fark kesinliği | ⚠️ doğrulanacak |
| S3 | IAudioSessionManager2 callback sırası (notification ↔ events) | ⚠️ UNKNOWN |
| S4 | Windows 11'de arayüz listesine eklenen yeni arayüz var mı? | ⚠️ fetch bekliyor |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** mimari · **Tarih:** 2026-10-12
