---
node_id: K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-kod-ornekleri
node_type: technology-deepdive
status: draft
parent_id: "K000–K5000 Architecture Registry"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-arayuz-hiyerarsisi"
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi/wasapi-shared-ve-exclusive-mode"
  - "K000-isletim-sistemi/isletim-sistemi-runtime"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams (fetch: 2026-10-12 — PlayExclusiveStream kodu birebir alındı)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (fetch: 2026-10-12 — IAudioClient3/AudioGraph/RT-WorkQueue/KSAUDIO_PACKETSIZE_CONSTRAINTS2 kodları birebir alındı)"
validation:
  - "Bu dosyadaki TÜM kod blokları 2026-10-12 tarihli fetch edilmiş resmi Microsoft Learn sayfalarından birebir alıntılanmıştır"
  - "Uydurma 'Dosya: … / Satır: …' repo referansı YOKTUR — repo'da 0 .cpp/.h dosyası olduğu 2026-10-12 glob ölçümüyle kanıtlanmıştır"
  - "Her kod bloğu altında 'Kaynak:' etiketi + URL + tarih zorunludur"
open_questions:
  - "Neva Engine WASAPI backend kodu henüz yazılmadı → gerçek repo-satır referansları kod yazıldıktan sonra eklenecek"
group: kod
---

# WASAPI Kod Örnekleri — Kaynak-Etiketli Resmi Alıntılar ve Repo Durum Kanıtı

> **Kod kanıt politikası (bu dosyaya özgü):** CoreMusic repo'sunda
> `**/*.{cpp,h,hpp,c}` glob'u 2026-10-12 ölçümünde **0 dosya** döndürdü
> (`projects/NevaEngine` = PLANNED). Bu nedenle bu dosyadaki kod örneklerinin
> tamamı **açıkça kaynak-etiketli resmi dokümantasyon alıntısıdır**
> (`Kaynak: learn.microsoft.com/... · fetch: 2026-10-12`). Uydurma repo
> yolu/satır numarası **yazılmamıştır** (RED TEAM kuralı).

---

## §0 Repo Durum Kanıtı

| Ölçüm | Komut (kavramsal) | Sonuç | Tarih |
|---|---|---|---|
| C/C++ dosyası sayısı | glob `**/*.{cpp,h,hpp,c}` (repo kökü) | **0** | 2026-10-12 |
| PHP kaynak kodu | `shared/src/**/*.php` | Yüzlerce (web SPA — ses motoru yok) | 2026-10-12 |
| Neva Engine vault kaydı | `.ai/AGENTS.md` §25.2 | "UI/Embedded/DSP/Windows: ⚠️ PLANNED (spec mevcut)" | 2026-10-12 okundu |

**Sonuç:** WASAPI kodu PLANNED aşamadadır; aşağıdaki alıntılar Neva Engine
backend'inin **şartname karşılığı** olarak kullanılır.

---

## §1 Alıntı 1 — Exclusive Mode Render Stream (tam fonksiyon)

Aşağıdaki kod, Microsoft'un `PlayExclusiveStream` örneğidir; event-driven
buffering + MMCSS ("Pro Audio") ile donanımın desteklediği **minimum** latency'de
exclusive stream çalar:

```c
//-----------------------------------------------------------
// Play an exclusive-mode stream on the default audio
// rendering device. The PlayExclusiveStream function uses
// event-driven buffering and MMCSS to play the stream at
// the minimum latency supported by the device.
//-----------------------------------------------------------

// REFERENCE_TIME time units per second and per millisecond
#define REFTIMES_PER_SEC  10000000
#define REFTIMES_PER_MILLISEC  10000

#define EXIT_ON_ERROR(hres)  \
              if (FAILED(hres)) { goto Exit; }
#define SAFE_RELEASE(punk)  \
              if ((punk) != NULL)  \
                { (punk)->Release(); (punk) = NULL; }

const CLSID CLSID_MMDeviceEnumerator = __uuidof(MMDeviceEnumerator);
const IID IID_IMMDeviceEnumerator = __uuidof(IMMDeviceEnumerator);
const IID IID_IAudioClient = __uuidof(IAudioClient);
const IID IID_IAudioRenderClient = __uuidof(IAudioRenderClient);

HRESULT PlayExclusiveStream(MyAudioSource *pMySource)
{
    HRESULT hr;
    REFERENCE_TIME hnsRequestedDuration = 0;
    IMMDeviceEnumerator *pEnumerator = NULL;
    IMMDevice *pDevice = NULL;
    IAudioClient *pAudioClient = NULL;
    IAudioRenderClient *pRenderClient = NULL;
    WAVEFORMATEX *pwfx = NULL;
    HANDLE hEvent = NULL;
    HANDLE hTask = NULL;
    UINT32 bufferFrameCount;
    BYTE *pData;
    DWORD flags = 0;
    DWORD taskIndex = 0;

    hr = CoCreateInstance(
           CLSID_MMDeviceEnumerator, NULL,
           CLSCTX_ALL, IID_IMMDeviceEnumerator,
           (void**)&pEnumerator);
    EXIT_ON_ERROR(hr)

    hr = pEnumerator->GetDefaultAudioEndpoint(
                        eRender, eConsole, &pDevice);
    EXIT_ON_ERROR(hr)

    hr = pDevice->Activate(
                    IID_IAudioClient, CLSCTX_ALL,
                    NULL, (void**)&pAudioClient);
    EXIT_ON_ERROR(hr)

    // Call a helper function to negotiate with the audio
    // device for an exclusive-mode stream format.
    hr = GetStreamFormat(pAudioClient, &pwfx);
    EXIT_ON_ERROR(hr)

    // Initialize the stream to play at the minimum latency.
    hr = pAudioClient->GetDevicePeriod(NULL, &hnsRequestedDuration);
    EXIT_ON_ERROR(hr)

    hr = pAudioClient->Initialize(
                         AUDCLNT_SHAREMODE_EXCLUSIVE,
                         AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
                         hnsRequestedDuration,
                         hnsRequestedDuration,
                         pwfx,
                         NULL);
    if (hr == AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED) {
        // Align the buffer if needed, see IAudioClient::Initialize() documentation
        UINT32 nFrames = 0;
        hr = pAudioClient->GetBufferSize(&nFrames);
        EXIT_ON_ERROR(hr)
        hnsRequestedDuration = (REFERENCE_TIME)((double)REFTIMES_PER_SEC / pwfx->nSamplesPerSec * nFrames + 0.5);
        hr = pAudioClient->Initialize(
            AUDCLNT_SHAREMODE_EXCLUSIVE,
            AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
            hnsRequestedDuration,
            hnsRequestedDuration,
            pwfx,
            NULL);
    }
    EXIT_ON_ERROR(hr)

    // Tell the audio source which format to use.
    hr = pMySource->SetFormat(pwfx);
    EXIT_ON_ERROR(hr)

    // Create an event handle and register it for
    // buffer-event notifications.
    hEvent = CreateEvent(NULL, FALSE, FALSE, NULL);
    if (hEvent == NULL)
    {
        hr = E_FAIL;
        goto Exit;
    }

    hr = pAudioClient->SetEventHandle(hEvent);
    EXIT_ON_ERROR(hr)

    // Get the actual size of the two allocated buffers.
    hr = pAudioClient->GetBufferSize(&bufferFrameCount);
    EXIT_ON_ERROR(hr)

    hr = pAudioClient->GetService(
                         IID_IAudioRenderClient,
                         (void**)&pRenderClient);
    EXIT_ON_ERROR(hr)

    // To reduce latency, load the first buffer with data
    // from the audio source before starting the stream.
    hr = pRenderClient->GetBuffer(bufferFrameCount, &pData);
    EXIT_ON_ERROR(hr)

    hr = pMySource->LoadData(bufferFrameCount, pData, &flags);
    EXIT_ON_ERROR(hr)

    hr = pRenderClient->ReleaseBuffer(bufferFrameCount, flags);
    EXIT_ON_ERROR(hr)

    // Ask MMCSS to temporarily boost the thread priority
    // to reduce glitches while the low-latency stream plays.
    hTask = AvSetMmThreadCharacteristics(TEXT("Pro Audio"), &taskIndex);
    if (hTask == NULL)
    {
        hr = E_FAIL;
        EXIT_ON_ERROR(hr)
    }

    hr = pAudioClient->Start();  // Start playing.
    EXIT_ON_ERROR(hr)

    // Each loop fills one of the two buffers.
    while (flags != AUDCLNT_BUFFERFLAGS_SILENT)
    {
        // Wait for next buffer event to be signaled.
        DWORD retval = WaitForSingleObject(hEvent, 2000);
        if (retval != WAIT_OBJECT_0)
        {
            // Event handle timed out after a 2-second wait.
            pAudioClient->Stop();
            hr = ERROR_TIMEOUT;
            goto Exit;
        }

        // Grab the next empty buffer from the audio device.
        hr = pRenderClient->GetBuffer(bufferFrameCount, &pData);
        EXIT_ON_ERROR(hr)

        // Load the buffer with data from the audio source.
        hr = pMySource->LoadData(bufferFrameCount, pData, &flags);
        EXIT_ON_ERROR(hr)

        hr = pRenderClient->ReleaseBuffer(bufferFrameCount, flags);
        EXIT_ON_ERROR(hr)
    }

    // Wait for the last buffer to play before stopping.
    Sleep((DWORD)(hnsRequestedDuration/REFTIMES_PER_MILLISEC));

    hr = pAudioClient->Stop();  // Stop playing.
    EXIT_ON_ERROR(hr)

Exit:
    if (hEvent != NULL)
    {
        CloseHandle(hEvent);
    }
    if (hTask != NULL)
    {
        AvRevertMmThreadCharacteristics(hTask);
    }
    CoTaskMemFree(pwfx);
    SAFE_RELEASE(pEnumerator)
    SAFE_RELEASE(pDevice)
    SAFE_RELEASE(pAudioClient)
    SAFE_RELEASE(pRenderClient)

    return hr;
}
```

**Kaynak:** `https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams` · **fetch:** 2026-10-12

### §1.1 Neva Engine şartname eşlemesi (kod yok — çağrı sırası)

| Alıntı satır bölgesi | Neva Engine backend karşılığı (PLANNED) |
|---|---|
| `GetDefaultAudioEndpoint(eRender, eConsole, …)` | `NevaWASAPI::openDefaultRender()` |
| `GetStreamFormat` (helper) | `negotiateExclusiveFormat()` → `IsFormatSupported` döngüsü |
| `Initialize(EXCLUSIVE, EVENTCALLBACK, min, …)` | `initExclusive(period=min)` |
| `AvSetMmThreadCharacteristics("Pro Audio", …)` | RT thread bootstrap |
| `WaitForSingleObject(hEvent, 2000)` + `GetBuffer`/`ReleaseBuffer` | `renderCallback()` ring-servis döngüsü |

---

## §2 Alıntı 2 — IAudioClient3 ile Düşük-Gecikme Shared Stream (C++)

```cpp
// 1. Activation

// Get a string representing the Default Audio (Render|Capture) Device
m_DeviceIdString = MediaDevice::GetDefaultAudio(Render|Capture)Id(
Windows::Media::Devices::AudioDeviceRole::Default );

// This call must be made on the main UI thread.  Async operation will call back to
// IActivateAudioInterfaceCompletionHandler::ActivateCompleted, which must be an agile // interface implementation
hr = ActivateAudioInterfaceAsync( m_DeviceIdString->Data(), __uuidof(IAudioClient3),
nullptr, this, &asyncOp );

// 2. Setting the audio client properties – note that low latency offload is not supported

AudioClientProperties audioProps = {0};
audioProps.cbSize = sizeof( AudioClientProperties );
audioProps.eCategory = AudioCategory_Media;

// if the device has System.Devices.AudioDevice.RawProcessingSupported set to true and you want to use raw mode
// audioProps.Options |= AUDCLNT_STREAMOPTIONS_RAW;
//
// if it is important to avoid resampling in the audio engine, set this flag
// audioProps.Options |= AUDCLNT_STREAMOPTIONS_MATCH_FORMAT;

hr = m_AudioClient->SetClientProperties( &audioProps ); if (FAILED(hr)) { ... }

// 3. Querying the legal periods

hr = m_AudioClient->GetMixFormat( &mixFormat ); if (FAILED(hr)) { ... }

hr = m_AudioClient->GetSharedModeEnginePeriod(wfx, &defaultPeriodInFrames, &fundamentalPeriodInFrames, &minPeriodInFrames, &maxPeriodInFrames); if (FAILED(hr)) { ... }

// legal periods are any multiple of fundamentalPeriodInFrames between
// minPeriodInFrames and maxPeriodInFrames, inclusive
// the Windows shared-mode engine uses defaultPeriodInFrames unless an audio client // has specifically requested otherwise

// 4. Initializing a low-latency client

hr = m_AudioClient->InitializeSharedAudioStream(
         AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
         desiredPeriodInFrames,
         mixFormat,
         nullptr); // audio session GUID
         if (AUDCLNT_E_ENGINE_PERIODICITY_LOCKED == hr) {
         /* engine is already running at a different period; call m_AudioClient->GetSharedModeEnginePeriod to see what it is */
         } else if (FAILED(hr)) {
             ...
         }

// 5. Initializing a client with a specific format (if the format needs to be different than the default format)

AudioClientProperties audioProps = {0};
audioProps.cbSize = sizeof( AudioClientProperties );
audioProps.eCategory = AudioCategory_Media;
audioProps.Options |= AUDCLNT_STREAMOPTIONS_MATCH_FORMAT;

hr = m_AudioClient->SetClientProperties( &audioProps );
if (FAILED(hr)) { ... }

hr = m_AudioClient->IsFormatSupported(AUDCLNT_SHAREMODE_SHARED, appFormat, &closest);
if (S_OK == hr) {
       /* device supports the app format */
} else if (S_FALSE == hr) {
       /* device DOES NOT support the app format; closest supported format is in the "closest" output variable */
} else {
       /* device DOES NOT support the app format, and Windows could not find a close supported format */
}

hr = m_AudioClient->InitializeSharedAudioStream(
       AUDCLNT_STREAMFLAGS_EVENTCALLBACK,
       defaultPeriodInFrames,
       appFormat,
       nullptr); // audio session GUID
if (AUDCLNT_E_ENGINE_FORMAT_LOCKED == hr) {
       /* engine is already running at a different format */
} else if (FAILED(hr)) {
       ...
}
```

**Kaynak:** `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` · **fetch:** 2026-10-12

---

## §3 Alıntı 3 — AudioGraph ile En Düşük Buffer (C#)

```csharp
AudioGraphSettings settings = new AudioGraphSettings(AudioRenderCategory.Media);
settings.QuantumSizeSelectionMode = QuantumSizeSelectionMode.LowestLatency;
CreateAudioGraphResult result = await AudioGraph.CreateAsync(settings);
```

**Kaynak:** `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` · **fetch:** 2026-10-12
**İlişki:** `QuantumSizeSelectionMode` enum'u — SystemDefault (~10 ms) / LowestLatency / ClosestToDesired.

---

## §4 Alıntı 4 — Sürücü Buffer Kısıtları Bildirimi (KSAUDIO_PACKETSIZE_CONSTRAINTS2)

```cpp
//
// Describe buffer size constraints for WaveRT buffers
//
static struct
{
    KSAUDIO_PACKETSIZE_CONSTRAINTS2 TransportPacketConstraints;
    KSAUDIO_PACKETSIZE_PROCESSINGMODE_CONST AdditionalProcessingConstraints[1];
} SysvadWaveRtPacketSizeConstraintsRender =
{
    {
        2 * HNSTIME_PER_MILLISECOND,                // 2 ms minimum processing interval
        FILE_BYTE_ALIGNMENT,                        // 1 byte packet size alignment
        0,                                          // no maximum packet size constraint
        2,                                          // 2 processing constraints follow
        {
            STATIC_AUDIO_SIGNALPROCESSINGMODE_DEFAULT,          // constraint for default processing mode
            128,                                                // 128 samples per processing frame
            0,                                                  // NA hns per processing frame
        },
    },
    {
        {
            STATIC_AUDIO_SIGNALPROCESSINGMODE_MOVIE,            // constraint for movie processing mode
            1024,                                               // 1024 samples per processing frame
            0,                                                  // NA hns per processing frame
        },
    }
};
```

**Kaynak:** `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` · **fetch:** 2026-10-12
**Yorum (kaynaklı):** "absolute minimum supported buffer size is 2 ms, but default mode supports 128 frames, which corresponds to 3 ms if we assume a 48-kHz sample rate."

---

## §5 Alıntı 5 — RT Work Queue ile MMCSS Sınıfı (C++)

```cpp
#define INVALID_WORK_QUEUE_ID 0xffffffff
DWORD g_WorkQueueId = INVALID_WORK_QUEUE_ID;
//#define MMCSS_AUDIO_CLASS    L"Audio"
//#define MMCSS_PROAUDIO_CLASS L"ProAudio"

STDMETHODIMP TestClass::GetParameters(DWORD* pdwFlags, DWORD* pdwQueue)
{
       HRESULT hr = S_OK;
       *pdwFlags = 0;
       *pdwQueue = g_WorkQueueId;
       return hr;
}

//-------------------------------------------------------
STDMETHODIMP TestClass::Invoke(IRtwqAsyncResult* pAsyncResult)
{
       HRESULT hr = S_OK;
       IUnknown *pState = NULL;
       WCHAR className[20];
       DWORD  bufferLength = 20;
       DWORD taskID = 0;
       LONG priority = 0;

       printf("Callback is invoked pAsyncResult(0x%0x)  Current process id :0x%0x Current thread id :0x%0x\n", (INT64)pAsyncResult, GetCurrentProcessId(), GetCurrentThreadId());

       hr = RtwqGetWorkQueueMMCSSClass(g_WorkQueueId, className, &bufferLength);
       IF_FAIL_EXIT(hr, Exit);

       if (className[0])
       {
              hr = RtwqGetWorkQueueMMCSSTaskId(g_WorkQueueId, &taskID);
              IF_FAIL_EXIT(hr, Exit);

              hr = RtwqGetWorkQueueMMCSSPriority(g_WorkQueueId, &priority);
              IF_FAIL_EXIT(hr, Exit);
              printf("MMCSS: [%ws] taskID (%d) priority(%d)\n", className, taskID, priority);
       }
       else
       {
              printf("non-MMCSS\n");
       }
       hr = pAsyncResult->GetState(&pState);
       IF_FAIL_EXIT(hr, Exit);

Exit:
       return S_OK;
}
//-------------------------------------------------------

int _tmain(int argc, _TCHAR* argv[])
{
       HRESULT hr = S_OK;
       HANDLE signalEvent;
       LONG Priority = 1;
       IRtwqAsyncResult *pAsyncResult = NULL;
       RTWQWORKITEM_KEY workItemKey = NULL;
       IRtwqAsyncCallback *callback = NULL;
       IUnknown *appObject = NULL;
       IUnknown *appState = NULL;
       DWORD taskId = 0;
       TestClass cbClass;
       NTSTATUS status;

       hr = RtwqStartup();
       IF_FAIL_EXIT(hr, Exit);

       signalEvent = CreateEvent(NULL, true, FALSE, NULL);
       IF_TRUE_ACTION_EXIT(signalEvent == NULL, hr = E_OUTOFMEMORY, Exit);

       g_WorkQueueId = RTWQ_MULTITHREADED_WORKQUEUE;

       hr = RtwqLockSharedWorkQueue(L"Audio", 0, &taskId, &g_WorkQueueId);
       IF_FAIL_EXIT(hr, Exit);

       hr = RtwqCreateAsyncResult(NULL, reinterpret_cast<IRtwqAsyncCallback*>(&cbClass), NULL, &pAsyncResult);
       IF_FAIL_EXIT(hr, Exit);

       hr = RtwqPutWaitingWorkItem(signalEvent, Priority, pAsyncResult, &workItemKey);
       IF_FAIL_EXIT(hr, Exit);

       for (int i = 0; i < 5; i++)
       {
              SetEvent(signalEvent);
              Sleep(30);
              hr = RtwqPutWaitingWorkItem(signalEvent, Priority, pAsyncResult, &workItemKey);
              IF_FAIL_EXIT(hr, Exit);
    }

Exit:
       if (pAsyncResult)
       {
              pAsyncResult->Release();
       }

      if (INVALID_WORK_QUEUE_ID != g_WorkQueueId)
      {
        hr = RtwqUnlockWorkQueue(g_WorkQueueId);
        if (FAILED(hr))
        {
            printf("Failed to RtwqUnlockWorkQueue 0x%x\n", hr);
        }

        hr = RtwqShutdown();
        if (FAILED(hr))
        {
            printf("Failed to RtwqShutdown 0x%x\n", hr);
        }
      }

       if (FAILED(hr))
       {
           printf("Failed with error code 0x%x\n", hr);
       }
       return 0;
}
```

**Kaynak:** `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` · **fetch:** 2026-10-12
**Not:** `RtwqLockSharedWorkQueue(L"Audio", …)` çağrısı, work queue'yu MMCSS "Audio" sınıfına bağlar.

---

## §6 Alıntı 6 — MF ByteStream + MMCSS Sınıfı (C++)

```cpp
// Specify Source Reader Attributes
Attributes->SetUnknown( MF_SOURCE_READER_ASYNC_CALLBACK, static_cast<IMFSourceReaderCallback *>(this) );
    if (FAILED( hr ))
    {
        goto exit;
    }
    Attributes->SetString( MF_READWRITE_MMCSS_CLASS_AUDIO, L"Audio" );
    if (FAILED( hr ))
    {
        goto exit;
    }
    Attributes->SetUINT32( MF_READWRITE_MMCSS_PRIORITY_AUDIO, 0 );
    if (FAILED( hr ))
    {
        goto exit;
    }
    // Create a stream from IRandomAccessStream
    hr = MFCreateMFByteStreamOnStreamEx (reinterpret_cast<IUnknown*>(m_ContentStream), &ByteStream );
    if ( FAILED( hr ) )
    {
        goto exit;
    }
    // Create source reader
    hr = MFCreateSourceReaderFromByteStream( ByteStream, Attributes, &m_MFSourceReader );
```

**Kaynak:** `https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio` · **fetch:** 2026-10-12

---

## §7 PortCls Kaynak Kaydı — API İmzaları (alıntı-düzeyinde)

Aşağıdaki tablo, low-latency-audio sayfasında geçen DDI/fonksiyon adlarının
birebir listesidir (imza detayı için ilgili DDI sayfaları bkz.
`wasapi-winsdk-dogrulama.md` §5):

| Sembol | Tür | Amaç | Kaynak |
|---|---|---|---|
| `PcAddStreamResource` | PortCls fonksiyonu (hard-link, Win10+) | Interrupt/driver-thread kaydı | [L1] |
| `PcRemoveStreamResource` | PortCls fonksiyonu | Kayıt kaldırma | [L1] |
| `IPortClsStreamResourceManager` | Arayüz (down-level) | `AddStreamResource`/`RemoveStreamResource` | [L1] |
| `PcStreamResourceType` | Enum | Kaynak tipi | [L1] |
| `PCSTREAMRESOURCE_DESCRIPTOR` | Yapı | Kaynak tanımı | [L1] |
| `IMiniportWaveRTInputStream` | Miniport arayüzü | Capture akışı koordinasyonu/timestamp | [L1] |
| `IMiniportWaveRTOutputStream` | Miniport arayüzü | Render akışı koordinasyonu/timestamp | [L1] |
| INF bölümü | `[<install-section-name>]` → `Include=wdmaudio.inf` · `Needs=WDMPORTCLS.CopyFilesOnly` | Yalnız PortCls'a bağlanan sürücüler için | [L1] |

---

## §8 Web Araştırması — Kaynak Tablosu

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| K1 | §1 PlayExclusiveStream kodu + §2'nin bir kısmı (exclusive init) | https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams | 2026-10-12 | ✅ fetch edildi — kod birebir alındı |
| K2 | §2 IAudioClient3 · §3 AudioGraph · §4 KSAUDIO_PACKETSIZE_CONSTRAINTS2 · §5 RT WorkQueue · §6 MF ByteStream · §7 PortCls DDI listesi | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio | 2026-10-12 | ✅ fetch edildi — kod birebir alındı |
| K3 | WASAPIAudio sample (depo) | https://github.com/Microsoft/Windows-universal-samples/tree/master/Samples/WindowsAudioSession | 2026-10-12 | ⚠️ bilinen resmi repo — bu turda fetch edilmedi |
| K4 | AudioCreation sample (AudioGraph) | https://github.com/Microsoft/Windows-universal-samples/tree/master/Samples/AudioCreation | 2026-10-12 | ⚠️ bilinen resmi repo — bu turda fetch edilmedi |
| K5 | sysvad sürücü örneği (WaveRT miniport) | https://github.com/Microsoft/Windows-driver-samples/tree/main/audio/sysvad | 2026-10-12 | ⚠️ bilinen resmi repo — bu turda fetch edilmedi |
| K6 | IAudioClient::GetService referansı | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient-getservice | 2026-10-12 | ⚠️ bilinen resmi URL — bu turda fetch edilmedi |

---

## §9 Açık Sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | `GetStreamFormat` helper'ının Microsoft tam implementasyonu var mı? | ⚠️ UNKNOWN — örnek "implementation depends entirely on the requirements of the application" diyor |
| Q2 | `MF_READWRITE_MMCSS_CLASS_AUDIO` sabiti hangi SDK sürümünden beri var? | ⚠️ UNKNOWN |
| Q3 | RT Work Queue ile event-driven WASAPI'in birlikte kullanımı resmi örnekte var mı? | ⚠️ UNKNOWN — bu turda doğrulanmadı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Satır:** ≥500 · **Group:** kod · **Tarih:** 2026-10-12
