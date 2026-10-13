---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-arayuzler
node_type: architecture-deepdive
title: "WASAPI Arayüzleri — Envanter, Initialize, Hata Kodları, GetService Ağacı"
type: architecture-deepdive
status: draft
date: 2026-10-12
parent_id: K000-isletim-sistemi/isletim-sistemi-audio
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-anayasa"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/header-files-and-system-components (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording (2026-10-12)"
validation:
  - "Hata kodları ve flag hex değerleri Initialize sayfasından birebir alındı (2026-10-12)"
  - "Bilinmeyen hex/sürücü ayrıntısı uydurulmadı; ⚠️ işareti konuldu"
open_questions:
  - "AUDCLNT_STREAMFLAGS_AUDCLNT_PROCESS_LOOPBACK hex değeri bu turda doğrulanmadı → ⚠️ (Q4)"
group: mimari
---

# WASAPI Arayüzleri — Envanter · Initialize · Hata Kodları · GetService

> **Kullanım:** Bu dosya Neva Engine WASAPI backend **şartnamesi** için
> arayüz başvuru tablosudur. Kod yok (`*.cpp/*.h` = 0, 2026-10-12) — bu
> dosya yalnız hangi arayüzün ne yaptığı ve hangi hatanın ne anlama geldiğini
> kanıtla tanımlar.
>
> **Durum:** IMPLEMENTED (belge) · Kod ⚠️ PLANNED.

---

## §0 ELI15 — Arayüz nedir, WASAPI'de kaç tane vardır?

WASAPI, Windows'un "COM arayüzleri" denen sözleşme listeleriyle konuşulur;
her sözleşme bir görev tanımlar (ör. `IAudioClient` = "akışı başlat/durdur").
WASAPI sayfası **11 ana arayüz** sayar ([S1]); session ve cihaz tarafında
ek arayüzler bulunur (§6, §7). Toplam envanter bu dosyada **25 arayüz** olarak
listelenmiştir; her satır kaynakla eşleşir.

---

## §1 Arayüz Envanteri (Master Tablo)

### §1.1 WASAPI'nin kendi sayfasındaki 11 arayüz ([S1])

| # | Arayüz | Grup | Görev |
|---|---|---|---|
| 1 | `IAudioClient` | Çekirdek | Akış oluşturma/başlatma/durdurma — WASAPI'nin ana girişi |
| 2 | `IAudioCaptureClient` | Çekirdek | Yakalama (capture) akışından frame okuma |
| 3 | `IAudioRenderClient` | Çekirdek | Render akışına frame yazma |
| 4 | `IAudioClock` | Zaman | Akışın cihaz zamanlamasıyla senkronu (sample tazağı) |
| 5 | `IAudioClock2` | Zaman | Zaman servisinde ek yetenekler (Win7+) |
| 6 | `IAudioClockAdjustment` | Zaman | `SetPresentationClockSampleRate` — sunum saati sample-rate kaydırma (Win7+) |
| 7 | `IAudioSessionManager` | Session | Uygulamanın kendi session'ını kontrol etme |
| 8 | `IAudioSessionManager2` | Session | Session listeleme + yeni session bildirimi |
| 9 | `IAudioSessionControl` | Session | Session durumu (active/inactive/expired), ad, ayar |
| 10 | `IAudioSessionControl2` | Session | Kimlik, grouping param, PID, sistem-sesleri session'ı |
| 11 | `IAudioSessionEvents` | Session | Session olay geri çağrısı (ses seviyesi değişimi, bağlantı kopması) |

### §1.2 Ek arayüzler (session/ölçüm/topoloji aileleri)

| # | Arayüz | Grup | Görev | Kanıt |
|---|---|---|---|---|
| 12 | `IAudioClient2` | Çekirdek | Initialize öncesi parametre (shared/stream) ayarı | ⚠️ bu tur ayrı sayfa çekilmedi |
| 13 | `IAudioClient3` | Çekirdek | 3 metod ile shared-mode düşük gecikme (Win10 1607+) | [S7] |
| 14 | `IAudioMeterInformation` | Ölçüm | Uç nokta/nivel seviyesi ölçümü | [S1] (GetService) |
| 15 | `IAudioStreamVolume` | Ses | Akışın tüm kanal seviyeleri | [S10] |
| 16 | `IAudioChannelVolume` (`IChannelAudioVolume`) | Ses | Session bazlı kanal seviyeleri | [S10] |
| 17 | `IAudioVolumeDuckControl` | Ses | Otomatik kısma (ducking) kontrolü (Win7+) | [S10] |
| 18 | `IAudioSessionNotification` | Session | Endpoint'e yeni session eklendiğinde bildirim | [S10] |
| 19 | `IDeviceTopology` | Topoloji | Cihaz içi birim/jack zinciri (DeviceTopology API) | [S1] |
| 20 | `IAudioViewManagerService` | Örnek | Universal örnekte session görüntü yönetimi (SDK 10.0.22470.0+) | [S18] |
| 21 | `IMMDevice` | MMDevice | Tek bir ses cihazı nesnesi | [S11] |
| 22 | `IMMDeviceEnumerator` | MMDevice | Cihaz listeleme, varsayılan bulma | [S11] |
| 23 | `IMMDeviceCollection` | MMDevice | Cihaz koleksiyonu | [S11] |
| 24 | `IAudioSessionEvents`-uç `OnSessionDisconnected` | Session | Cihaz kopması neden bildirimi (§10) | [S16] |
| 25 | `IAudioSessionControl2::GetSessionIdentifier` vb. | Session | Session kimliği kalıcılığı (§7) | [S10] |

> **Sınır:** #21-#23 MMDevice API'nin arayüzleridir; WASAPI'nin
> `Activate` ile `IAudioClient` ürettiği geçiş noktası bu dosya §3'tedir.

---

## §2 IAudioClient Metotları (çekirdek 11)

| Metot | Ne yapar | Döndürür/not | Kanıt |
|---|---|---|---|
| `GetMixFormat` | Motorun cihaz için tercih ettiği paylaşım formatı | `WAVEFORMATEXTENSIBLE*` | [S1], [S14] |
| `IsFormatSupported` | İstenen format destekleniyor mu? (shared: motor yakınsayabilir) | `S_OK` / `S_FALSE`(yakınsama) / `AUDCLNT_E_UNSUPPORTED_FORMAT` | [S14] |
| `GetDevicePeriod` | Varsayılan + minimum sürücü dönemi (hns) | `hnsDefaultDevicePeriod`, `hnsMinimumDevicePeriod` | [S6] |
| `Initialize` | Akışı mode+format+session ile başlat | §3-§5'e bak; COM arayüzünü buradan alırsın | [S6] |
| `GetBufferSize` | Endpoint buffer kapasitesi (frame) | `UINT32` | [S5] |
| `GetCurrentPadding` | Doldurulmuş ama henüz tüketilmemiş frame sayısı | `UINT32` | [S5] |
| `GetStreamLatency` | Bu akışın toplam gecikmesi (format+period'a bağlı) | `REFERENCE_TIME` (hns) | [S6] |
| `GetService` | Session/ses/topoloji arayüzlerini iste (§6) | Arayüz pointer'ı | [S1], [S10] |
| `SetEventHandle` | Event-driven modda uyandırma olayı ver | `HANDLE` | [S6] |
| `Start` / `Stop` / `Reset` | Akışı başlat / durdur / sıfırla (Reset: capture'da bekleyen frame'leri at) | `HRESULT` | [S1], [S5] |

**Sıra kuralı (kanıtlı akış, [S5], [S6], [S18]):**

```
IMMDeviceEnumerator → IMMDevice → Activate(IID_IAudioClient) → IAudioClient
   → GetMixFormat / IsFormatSupported
   → Initialize(shareMode, flags, duration, format, sessionGUID)
   → GetService(IID_IAudioRenderClient / IID_IAudioCaptureClient)
   → [event-driven ise] SetEventHandle + CreateEvent
   → GetBufferSize + GetDevicePeriod
   → Start
   → döngü: GetCurrentPadding → GetBuffer → yaz/oku → ReleaseBuffer
   → Stop / Reset
```

---

## §3 Initialize Parametreleri

`IAudioClient::Initialize(SharedAudioMode, StreamFlags, hnsRequestedDuration,
hnsPeriodicity, AudioFormat, AudioSessionGuid)` ([S6]):

| Parametre | Tip | Anlamı | Kanıt |
|---|---|---|---|
| `ShareMode` | `AUDCLNT_SHAREMODE` | `SHARED` veya `EXCLUSIVE` (§ anayasa §5-§6) | [S6] |
| `StreamFlags` | `DWORD` | §4 bayrak tablosu | [S6] |
| `hnsRequestedDuration` | `REFERENCE_TIME` | İstenen buffer/period süresi; **100-nanosecond (hns) birimi** | [S6] |
| `hnsPeriodicity` | `REFERENCE_TIME` | Exclusive'de dönem (shared'de 0 bırakılır; event-driven'da dikkat, §9) | [S6] |
| `AudioFormat` | `const WAVEFORMATEX*` | İstenen akış formatı (shared'de motor SRC yapabilir) | [S6] |
| `AudioSessionGuid` | `const GUID*` | Session kimliği; NULL ise sistem atar; uygulama kalıcı kimlik verirse Sndvol ayarları korunur ([S10]) | [S6], [S10] |

---

## §4 Initialize Bayrakları (StreamFlags — hex değerleri kaynaklı)

| Bayrak | Hex | OS | Anlamı | Kanıt |
|---|---|---|---|---|
| `AUDCLNT_STREAMFLAGS_CROSSPROCESS` | `0x00010000` | Vista+ | Session'ı süreçler arası yap (başka süreç de bağlanabilsin) | [S6] |
| `AUDCLNT_STREAMFLAGS_LOOPBACK` | `0x00020000` | Vista+ | Cihazın çaldığını yakala (yalnız SHARED) | [S6], [S15] |
| `AUDCLNT_STREAMFLAGS_EVENTCALLBACK` | `0x00040000` | Vista+ | Uygulama olayla uyanır (polling yerine) | [S6] |
| `AUDCLNT_STREAMFLAGS_NOPERSIST` | `0x00080000` | Vista+ | Session ayarları Sndvol'da kalıcı olmasın (uygulama çıkınca silinsin) | [S6] |
| `AUDCLNT_STREAMFLAGS_RATEADJUST` | `0x00100000` | 8.1+ | Sample-rate kaydırma (clock adjustment) | [S6] |
| `AUDCLNT_STREAMFLAGS_SRC_DEFAULT_QUALITY` | `0x08000000` | 7+ | SRC kalitesini motora bırak (AUTOCONVERTPCM ile) | [S6] |
| `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` | `0x80000000` | 7+ | Motor PCM formatını otomatik dönüştürür (uygulama formatı garanti edilir) | [S6] |
| `AUDCLNT_STREAMFLAGS_AUDCLNT_PROCESS_LOOPBACK` | ⚠️ hex bu turda doğrulanmadı | 7+ | Süreç-bazlı loopback (tek uygulamanın sesini yakala) → Q4 | ⚠️ |

> **Kural (kanıtlı):** `EVENTCALLBACK` shared modda yalnız belirli
> koşullarda geçerlidir; exclusive event-driven'da `hnsPeriodicity` ile
> olay/tetikleme eşleşmelidir; eşleşmezse `AUDCLNT_E_EVENTHANDLE_NOT_SET`
> / `AUDCLNT_E_EVENTHANDLE_NOT_EXPECTED` alınır. (Kaynak: [S6] hata
> tanımları + low-latency sayfasının event-driven çerçevesi [S8].)

---

## §5 Initialize Hata Kodları (tam liste — anlam + ne yapılacak)

Kaynak: [S6] (IAudioClient::Initialize sayfasının hata listesi; hepsi bu
sayfadan alınmıştır, uydurma yok).

| # | Hata | Anlamı | Çözüm yönü |
|---|---|---|---|
| 1 | `E_POINTER` | Geçersiz pointer (NULL argüman) | Kontrol et |
| 2 | `E_INVALIDARG` | Geçersiz argüman (ör. sıfır süre) | Parametre doğrula |
| 3 | `E_NOTIMPL` | İstenen kombinasyon implemente edilmedi | Bayrak/mod kombinasyonunu sadeleştir |
| 4 | `E_OUTOFMEMORY` | Bellek yetersiz | Retry yok; kaynak serbest bırak |
| 5 | `AUDCLNT_E_ALREADY_INITIALIZED` | Akış zaten Initialize edilmiş | Yeni `IAudioClient` iste (Activate tekrar) |
| 6 | `E_BUFFER_TOO_LARGE` (render) | İstenen buffer fazla büyük | `hnsRequestedDuration` küçült |
| 7 | `AUDCLNT_E_BUFDURATION_PERIOD_TOO_SMALL` | Buffer süresi dönemden küçük | Süreyi `GetDevicePeriod` min değerine göre büyüt |
| 8 | `AUDCLNT_E_BUFFER_OPERATION_PENDING` (capture) | Capture'da bekleyen işlem var | Önce oku/durdur |
| 9 | `AUDCLNT_E_BUFFER_SIZE_ERROR` (8+) | Buffer/period boyutu desteklenmiyor | Dönemi sürücü min/max'ine göre ayarla |
| 10 | `AUDCLNT_E_BUFFER_TOO_SMALL` | Buffer fazla küçük | `GetBufferSize` + `GetDevicePeriod` ile hizala |
| 11 | `AUDCLNT_E_DEVICE_IN_USE` | Cihaz exclusive tarafından tutuluyor | Shared dene veya cihaz seç |
| 12 | `AUDCLNT_E_DEVICE_INVALIDATED` | Cihaz artık yok (koptu/değişti) | §10 recovery akışı |
| 13 | `AUDCLNT_E_ENDPOINT_CREATE_FAILED` | Endpoint buffer oluşturulamadı | Sürücü/period uyumsuz |
| 14 | `AUDCLNT_E_EVENTHANDLE_NOT_EXPECTED` | Event handle verildi ama event-callback yok | Flag/handle tutarlılığı |
| 15 | `AUDCLNT_E_FLAGS_CHANGE_PROHIBITED` | Bayraklar Initialize sonrası değiştirilemez | Yeni akış oluştur |
| 16 | `AUDCLNT_E_FLAGS_NOT_SUPPORTED` | Bayrak bu mod/OS'te desteklenmiyor | OS sürüm kontrolü (§ anayasa §4) |
| 17 | `AUDCLNT_E_SHARED_MODE_ONLY` | Exclusive desteklenmiyor | Shared'a düş |
| 18 | `AUDCLNT_E_EVENTHANDLE_NOT_SET` | Event-callback açık ama handle yok | `SetEventHandle` çağır |
| 19 | `AUDCLNT_E_EXCLUSIVE_MODE_ONLY` | Bu özellik yalnız exclusive | Exclusive dene veya bırak |
| 20 | `AUDCLNT_E_BUFDURATION_PERIOD_TOO_LARGE` | Süre fazla büyük | Küçült |
| 21 | `AUDCLNT_E_INVALID_DEVICE_PERIOD` | Geçersiz dönem değeri | `GetDevicePeriod` oku |
| 22 | `AUDCLNT_E_UNSUPPORTED_FORMAT` | Format bu cihazda/motorda desteklenmiyor | `GetMixFormat`/`IsFormatSupported` |
| 23 | `AUDCLNT_E_INVALID_STREAM_FLAG` | Geçersiz/çelişkili bayrak | §4 tablosu |
| 24 | `AUDCLNT_E_RAW_AUDIO_UNSUPPORTED` | Raw (bypass) bu cihazda yok | Mode'a göre düş |
| 25 | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` (7+) | Buffer boyutu hizalı değil | `GetBufferSize` değeriyle yeniden `Initialize` (retry kalıbı) |
| 26 | `AUDCLNT_E_INVALID_ENGINE_PERIOD` (10+) | Engine period tercihi geçersiz | IAudioClient3 tercihlerini sorgula |

> **Neva Engine şartname notu (PLANNED):** 26 kodun tamamı hata tablosuna
> alınacak; 12 (DEVICE_INVALIDATED) fallback zincirini (ASIO→WASAPI→
> devre dışı) tetikler. Kod yazımı ayrı görevdir.

---

## §6 GetService Ağacı — Initialize Sonrası Ne İstenir?

`GetService` yalnızca **Initialize başarılı olduktan sonra** çağrılır ([S1]).

| İstenen arayüz | Ne verir | Tipik kullanıcı | Kanıt |
|---|---|---|---|
| `IID_IAudioRenderClient` | Render buffer pointer'ı (GetBuffer/ReleaseBuffer) | Çalan uygulama | [S5] |
| `IID_IAudioCaptureClient` | Capture frame okuma | Kayıt yapan uygulama | [S1] |
| `IID_IAudioSessionManager` | Kendi session control + volume | Session seviyesi | [S10] |
| `IID_IAudioSessionManager2` | Session listeleme + notification | Session takibi | [S10] |
| `IID_IAudioSessionControl(2)` | Session durumu/identity/grouping/PID | Panel, routing | [S10] |
| `IID_IAudioStreamVolume` | Tüm kanalların stream seviyesi | Volume UI | [S10] |
| `IID_IAudioMeterInformation` | Seviye ölçümü (peak) | VU meter | [S1] |
| `IID_IAudioVolumeDuckControl` | Ducking başlat/sonlandır | Communications akışı | [S10] |
| `IID_IDeviceTopology` | Cihaz topolojisi | Jack/HD-audio routing | [S1] |

---

## §7 Session Arayüzleri — Derin Ayrım

Session, WASAPI'nin "uygulama birimi"dir; endpoint bazında gruplanır ve
Sndvol (ses mikseri) panelinde gösterilir ([S10]).

| Arayüz | Üyeler/anlam | Kanıt |
|---|---|---|
| `IAudioSessionControl` | Durum (active/inactive/expired), display name, icon path | [S10] |
| `IAudioSessionControl2` | `GetSessionIdentifier` · `GetSessionInstanceIdentifier` · `GetGroupingParam` · `SetGroupingParam` · `GetProcessId` · `IsSystemSoundsSession` | [S10] |
| `IAudioSessionEvents` | `OnDisplayNameChanged` · `OnIconPathChanged` · `OnChannelVolumeChanged` · `OnGroupingParamChanged` · `OnStateChanged` · `OnSessionDisconnected` | [S10], [S16] |
| `IAudioSessionNotification` | Endpoint'e yeni session geldiğinde `OnSessionCreated` | [S10] |
| `IAudioStreamVolume` | `GetChannelCount` · `GetChannelVolume` · `SetChannelVolume` (stream bazlı) | [S10] |
| `IChannelAudioVolume` | Session bazlı per-channel volume | [S10] |

**Session kuralları (kanıtlı, [S10]):**

1. Uygulama, `Initialize`'e kendi session GUID'ını verirse (veya sonradan
   sabit GUID kullanırsa) Sndvol ayarları **kalıcı** olur; NULL verilirse
   sistem atar ve uygulama kapanınca session "expired" olur.
2. `AUDCLNT_STREAMFLAGS_NOPERSIST` kalıcılığı kapatır.
3. Win7+ **gruplama**: `SetGroupingParam` ile session'lar gruplanır; grup
   bazlı cihaz yönlendirme (stream routing) ve grup bildirimleri mümkündür.
4. **Ducking** (Win7+): communications session aktifleşince diğer session'lar
   otomatik kısılır; `IAudioVolumeDuckControl` bunu yönetir.
5. Cross-process: başka bir uygulamanın session'ının seviyesi de session
   arayüzleriyle okunabilir/yazılabilir (mikser paneli eşdeğeri).

---

## §8 Header & Sistem Bileşenleri

Kaynak: [S12].

| Header | İçerdiği |
|---|---|
| `Mmdeviceapi.h` | MMDevice API (Enumerator, Device, Collection) |
| `Audioclient.h` | `IAudioClient(2/3)` · render/capture client · clock arayüzleri |
| `Audiopolicy.h` | Audio policy/session policy (session manager, ducking, barındırma) |
| `Devicetopology.h` | DeviceTopology API |
| `Endpointvolume.h` | EndpointVolume API |
| `Audiosessiontypes.h` | Session enum/tipleri (state, disconnect reason, disconnect enum) |

| Sistem bileşeni | Rol |
|---|---|
| `Mmdevapi.dll` | MMDevice API'nin Windows bileşeni |
| `Audioses.dll` | Ses oturumu/policy yönetimi bileşeni |
| `Audiodg.dll` | User-mode ses motoru (karışım) — [S4] |

**Eksik:** Core Audio API'lerinin **.NET/managed sarmalaması yoktur**
([S12]) — Neva Engine C++20 tarafında doğrudan COM çağıracaktır (PLANNED).

---

## §9 Render Döngüsü — GetBuffer/ReleaseBuffer Semantiği

Kaynak: [S5].

| Kavram | Kural |
|---|---|
| Frame | Bir örnekleme anı tüm kanallar (stereo 16-bit = 4 byte) |
| `GetBuffer(numFrames, &buffer)` | "Şu kadar frame yazabilirsin" — buffer tahsisi motor/sürücüden |
| `ReleaseBuffer(numFrames, flags)` | Yazdığın frame'leri teslim et; flags: `SILENT` (sessiz frame) dahil |
| Padding | `GetCurrentPadding` ile okunur; boşluk = `BufferSize − Padding` |
| Pull model | Motor uygulamadan okur; uygulama Sleep/olayla uyanır ([S13]) |
| Event-driven | `SetEventHandle` sonrası olayla uyanma; polling'den düşük gecikme ([S8]) |

**Döngü iskeleti (canonical, kod PLANNED):**

```text
Start()
while running:
    if eventDriven:
        WaitForSingleObject(hEvent, timeout)     # sürücü/motor uyarır
    else:
        Sleep(periodMs)                            # polling
    GetCurrentPadding(&padding)
    framesToWrite = GetBufferSize() - padding
    if framesToWrite > 0:
        IAudioRenderClient::GetBuffer(framesToWrite, &data)
        # ... DSP/render doldur (sıfır tahsis kuralı: hazır ring buffer'dan kopyala)
        IAudioRenderClient::ReleaseBuffer(framesToWrite, flags)
Stop(); Reset()
```

---

## §10 Cihaz Kaybı & Recovery (IAudioClient dışı, session olayları)

Kaynak: [S16].

| Gerçek | Ayrıntı |
|---|---|
| Ana hata | `AUDCLNT_E_DEVICE_INVALIDATED` — Start/Stop/GetBuffer dâhil **herhangi bir** çağrıda çıkabilir |
| Neden bildirimi | `IAudioSessionEvents::OnSessionDisconnected(DisconnectReason)` |
| `AudioSessionDisconnectReason` | `DeviceRemoval` · `ServerShutdown` · `FormatChange` · `SessionLogoff` · `SessionDisconnected` · `ExclusiveModeOverride` |
| Varsayılan cihaz değişimi | MMDevice device-change bildirimleri; uygulama **kategorisine** göre yeni varsayılanı izler (console/multimedia/communication ayrı ayrı) |
| "Communication" varsayılanı | Kullanıcı ayrı bir varsayılan communication cihazı seçebilir; VoIP tarafı bunu izler |
| CoreMusic bağlantısı | ASIO device loss → WASAPI fallback (`.ai/AGENTS.md` §17/6) bu recovery mantığının üstünde çalışır |

---

## §11 Loopback Arayüz Notları (özet; detay yığın dosyası)

Kaynak: [S15].

1. `AUDCLNT_STREAMFLAGS_LOOPBACK` yalnız **SHARED** modda geçerli.
2. `Initialize` çağrısına uygulamanın **format yerine cihazın mix formatı**
   verilir (loopback akışı motordan beslenir).
3. Win10 1703 öncesi event-driven loopback hatalıydı; 1703 düzeltti.
4. Loopback "cihazın çaldığını" yakalar: diğer uygulamaların sesleri dâhil
   (tek uygulama değil — session süzme için session arayüzleri gerekir).
5. Donanım loopback cihazları "loopback" adı taşır (ayrıntı: yığın dosyası §).

---

## §12 Örneklerin Öğrettiği Kalıplar (resmi örnekler)

Kaynak: [S18] (WASAPI-rendering), [S19] (WindowsAudioSession).

| Kalıp | Örnek | Ne kanıtlıyor |
|---|---|---|
| Shared + event-driven render | WASAPI-rendering | Event-driven buffer'ın resmi C++/WinRT sarmalayıcısı var |
| `IAudioViewManagerService` | WASAPI-rendering | Session görüntü yönetimi servisi (SDK 10.0.22470.0+ zorunlu) |
| Hardware offload oynatma | WindowsAudioSession | Düşük güç oynatma opt-in ile mümkündür |
| Background/media transport | WindowsAudioSession | Uygulama arka planda session durumunu yönetebilir |
| Düşük gecikmeli playback+capture | WindowsAudioSession | Duplex düşük gecikme resmi kalıp |

---

## §13 CoreMusic Uygulama Notları (şartname — kod PLANNED)

| Kural | Gerekçe | Kanıt |
|---|---|---|
| `Activate` → `Initialize` → `GetService` sırası zorunlu | Microsoft akış sırası | [S5], [S18] |
| Event-driven tercih (sharedLowLatency tarafı da) | Düşük gecikme + düşük CPU | [S8], [S21] |
| `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` → buffer değeriyle retry | Sayfadaki resmi retry kalıbı | [S6] |
| `AUDCLNT_E_DEVICE_INVALIDATED` → recovery/fallback zinciri | Cihaz kopması kaçınılmaz | [S16], `.ai/AGENTS.md` §17/6 |
| Session GUID = sabit uygulama GUID'ı | Sndvol kalıcılığı + panel tanınabilirliği | [S10] |
| `NOPERSIST` test build'lerde | Test temizliği | [S6] |
| Render döngüsünde tahsis yok | Zero-allocation kuralı (`.ai/AGENTS.md` §16 Embedded standardı) | Repo kuralı |

---

## §14 Kapsam Dışı

1. `IAudioClient2` üye listesi (bu tur sayfası çekilmedi → ⚠️, Q5).
2. COM/CoInitializeDetalları (Windows Software Engineer domaini; scaffold'ta ele alınacak).
3. Kernel sürücü arayüzleri (Portcls/WaveRT ioctl) — yığın dosyası §'de anılır.
4. WinRT `AudioGraph`/`AudioFrame` sarmalayıcıları — WASAPI'nin üstünde katman; kapsam dışı.

---

## §15 Açık Sorular (özet; tam: wasapi-dogrulama.md §4)

| # | Soru | Durum |
|---|---|---|
| Q4 | `AUDCLNT_STREAMFLAGS_AUDCLNT_PROCESS_LOOPBACK` hex değeri? | ⚠️ doğrulanmadı |
| Q5 | `IAudioClient2` tam üye listesi? | ⚠️ sayfa çekilmedi |
| Q6 | Neva Engine hangi session GUID politikasını seçer (sabit uygulama GUID'ı vs session-per-track)? | ADR kararı |

---

## § Kaynak Tablosu

| ID | URL | Fetch |
|---|---|---|
| S1 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 |
| S5 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/rendering-a-stream | 2026-10-12 |
| S6 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-initialize | 2026-10-12 |
| S7 | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclient3 | 2026-10-12 |
| S8 | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 |
| S10 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 |
| S11 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/mmdevice-api | 2026-10-12 |
| S12 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/header-files-and-system-components | 2026-10-12 |
| S13 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/what-s-new-for-core-audio-apis-in-windows-7 | 2026-10-12 |
| S14 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-formats | 2026-10-12 |
| S15 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/loopback-recording | 2026-10-12 |
| S16 | https://learn.microsoft.com/en-us/windows/win32/coreaudio/recovering-from-an-invalid-device-error | 2026-10-12 |
| S18 | https://learn.microsoft.com/en-us/samples/microsoft/windows-classic-samples/wasapi-rendering/ | 2026-10-12 |
| S19 | https://learn.microsoft.com/en-us/samples/microsoft/windows-universal-samples/windowsaudiosession/ | 2026-10-12 |
| S21 | https://portaudio.com/docs/v19-doxydocs-dev/pa__win__wasapi_8h_source.html | 2026-10-12 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
**Durum:** IMPLEMENTED (belge) · Kod: ⚠️ PLANNED (cpp=0)
**Sıradaki adım:** `wasapi-surucu-ve-yigin.md` — stack diyagramları, MMCSS/AVRT, WaveRT.
