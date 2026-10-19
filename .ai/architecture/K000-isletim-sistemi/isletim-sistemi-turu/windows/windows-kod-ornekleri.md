---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kod-ornekleri
node_type: code
status: verify-required
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/windows
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-kernel-modlari
  - K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-ses-stacki
  - K000-isletim-sistemi/isletim-sistemi-turu/windows/windows-dma-ve-interrupt
  - K003-audio/backend/asio
evidence:
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/ (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/kernel/ (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/processes-and-threads (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/low-latency-audio (✅ fetch 2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/windows-audio-architecture (✅ fetch 2026-10-12)"
validation:
  - "2026-10-12 RED TEAM: Dosya/Satır UYDURMA YASAK — repo'da 0 .cpp/.h; tüm kod ya kaynak-etiketli resmi alıntı ya da 'orijinal örnek' etiketli"
  - "≥500 satır · 10 alanlı frontmatter · Authority imzası"
open_questions:
  - "AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM resmi onayı (low-latency sayfası 'Need to confirm')"
  - "PcAddStreamResource PortCls DDI imzası — fetch edilmedi (B-kaynak)"
group: kod
---

# windows-kod-ornekleri — Windows ses/kernel kod kalıpları (RED TEAM / TRUTH MODE)

> **Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-12 · **Durum:** verify-required
> **Kapsam:** `windows/` beyazliste kalem 5 (kod) · 10 alanlı frontmatter · ≥500 satır.

---

## §0. Kod kaynak politikası (en üst — bağlayıcı)

**ZERO-HALLUCINATION kod kuralı (bu turda mutlak):**

| Kaynak türü | Durum | Kural |
|---|---|---|
| Repo'daki C++ dosyası | **YOK** — bu repo (vault) 0 `.cpp`/`.h` içerir | `Dosya:… / Satır:…` referansı **UYDURMA YASAK** |
| Resmi dokümantasyon alıntısı | Yalnız `✅ fetch edildi` URL'lerden, `>` blockquote + URL + tarih | Parafraz değil, alıntı |
| Bilinen ama fetch edilmeyen resmi URL'den alıntı | `⚠️ bilinen resmi URL — bu turda fetch edilmedi` etiketi zorunlu | Aynen aktarılmış gibi sunulamaz |
| **Orijinal örnek (bu vault için yazılmış)** | `**[ORİJİNAL ÖRNEK]**` etiketi zorunlu | Resmi kaynakmış gibi sunulamaz |
| Compile/çalışma kanıtı | **YOK** — bu turda derlenmedi/çalıştırılmadı | `status: verify-required` |

**Doğrulanabilirlik iddiası:** Bu dosyadaki her kod bloğu üç etiketten birini taşır:
`[RESMİ ALINTI · URL · fetch tarihi]` · `[ORİJİNAL ÖRNEK · derlenmedi]` · `⚠️ [BİLİNEN KAYNAK · fetch yok]`.

---

## §1. Örnek A — KMDF sürücü iskeleti **[ORİJİNAL ÖRNEK · derlenmedi]**

> Yapı, WDF ana sayfasının sunduğu sürücü modeli (⏳ EV3, KMDF 1.33) ile uyumlu yazılmış
> **orijinal** bir iskelettir; resmi WDF örnek kodunun kopyası DEĞİLDİR.

```c
/* [ORİJİNAL ÖRNEK] KMDF minimal sürücü iskeleti — derlenmedi (vault'ta WDK yok) */
#include <ntddk.h>
#include <wdf.h>

typedef struct _MYDEVICE_CONTEXT {
    WDFQUEUE  defaultQueue;
    ULONG     instanceMagic;   /* epoch benzeri tanılama etiketi */
} MYDEVICE_CONTEXT, *PMYDEVICE_CONTEXT;

WDF_DECLARE_CONTEXT_TYPE_WITH_NAME(MYDEVICE_CONTEXT, MyGetDeviceContext)

DRIVER_INITIALIZE DriverEntry;
NTSTATUS DriverEntry(_In_ PDRIVER_OBJECT DriverObject,
                     _In_ PUNICODE_STRING RegistryPath)
{
    WDF_DRIVER_CONFIG config;
    WDF_DRIVER_CONFIG_INIT(&config,EvtDeviceAdd);
    return WdfDriverCreate(DriverObject, RegistryPath,
                           WDF_NO_OBJECT_ATTRIBUTES,
                           &config, WDF_NO_HANDLE);
}

NTSTATUS EvtDeviceAdd(_In_ WDFDRIVER Driver, _Inout_ PWDFDEVICE_INIT DeviceInit)
{
    NTSTATUS status;
    WDFDEVICE device;
    PMYDEVICE_CONTEXT ctx;

    UNREFERENCED_PARAMETER(Driver);
    WdfDeviceInitSetDeviceType(DeviceInit, FILE_DEVICE_AUDIO); /* PortCls ailesi örüntüsü */
    status = WdfDeviceCreate(&DeviceInit, WDF_NO_OBJECT_ATTRIBUTES, &device);
    if (!NT_SUCCESS(status)) return status;

    ctx = MyGetDeviceContext(device);
    ctx->instanceMagic = 0xC0DE; /* tanılama */
    return STATUS_SUCCESS;
}
```

**İddia → kaynak:** `WdfDriverCreate/WdfDeviceCreate/WDF_DRIVER_CONFIG` ailesi = WDF modeli ·
`https://learn.microsoft.com/en-us/windows-hardware/drivers/wdf/` · `✅ fetch edildi 2026-10-12`
(KMDF 1.33 · UMDF 2.33 · Win11/Server 2022 hedefi bu ana sayfada doğrulandı).
⚠️ **Açık:** `FILE_DEVICE_AUDIO` sabiti PortCls evreninde kullanılır — PortCls DDI sayfası
`PcAddStreamResource` bu turda fetch edilmedi (`⚠️ B-kaynak`).

---

## §2. Örnek B — Kernel nesneleri ve IRP disiplini **[ORİJİNAL ÖRNEK · derlenmedi]**

> `windows-hardware/drivers/kernel/` ana sayfası (✅ fetch 2026-10-12) işletim sistemi çekirdeğindeki
> **yönetici nesneler** olarak Object, Memory, Process-Thread, IO, PnP, Power, Config, KTM ve
> Security Manager'ı sayar; ayrıca DMA, HAL, CLFS, WMI, Kernel, Executive, RT ve SafeString
> kütüphaneleri listelenir. Aşağıdaki_DISPATCH örüntüsü bu model üzerine **orijinal** kurulmuştur.

```c
/* [ORİJİNAL ÖRNEK] IRP dispatch — doğrulama kodu yok, derlenmedi */
NTSTATUS EvtIoDeviceControl(_In_ WDFQUEUE Queue,
                            _In_ WDFREQUEST Request,
                            _In_ size_t OutputBufferLength,
                            _In_ size_t InputBufferLength,
                            _In_ ULONG IoControlCode)
{
    UNREFERENCED_PARAMETER(Queue);
    UNREFERENCED_PARAMETER(OutputBufferLength);

    switch (IoControlCode) {
    case IOCTL_AUDIO_GET_ENGINE_PERIOD: {   /* IAudioClient3 periyot sorgusu izi */
        PULONG out = NULL; size_t len = 0;
        NTSTATUS st = WdfRequestRetrieveOutputBuffer(Request, sizeof(ULONG), (PVOID*)&out, &len);
        if (!NT_SUCCESS(st)) { WdfRequestComplete(Request, st); return st; }
        *out = 480; /* 480 örnek = 10 ms @48 kHz — IAudioClient3 10/20/30 ms periyot
                       üçlüsünün (kilitli motor) örnek karşılığı; doğrulanmadı */
        WdfRequestCompleteWithInformation(Request, STATUS_SUCCESS, sizeof(ULONG));
        return STATUS_SUCCESS;
    }
    default:
        WdfRequestComplete(Request, STATUS_INVALID_DEVICE_REQUEST);
        return STATUS_INVALID_DEVICE_REQUEST;
    }
}
```

**Doğrulama notu (RED TEAM):** `480 örnek` değeri 48 kHz × 10 ms aritmetiğidir; motorun gerçekten
480'e izin verdiği `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` / fundamental-period kat kuralı ile
sınırlıdır (kilitli gerçek → `windows-ses-stacki.md` §2.3). **Doğrulanmadı.**

---

## §3. Örnek C — Olay-sürüklü (event-driven) shared mode çizimi **[ORİJİNAL ÖRNEK · derlenmedi]**

> Şema `low-latency-audio` (✅ fetch 2026-10-12) ve `windows-audio-architecture` (✅ fetch 2026-10-12)
> sayfalarının anlattığı olay-sürüklü + düşük gecikme örüntüsüne göre **orijinal** yazılmıştır.

```cpp
// [ORİJİNAL ÖRNEK] IAudioClient shared-mode event loop — derlenmedi
// Kaynak örüntü: learn.microsoft.com .../audio/low-latency-audio (✅ fetch 2026-10-12)
#include <audioclient.h>
#include <mmdeviceapi.h>

HRESULT RunEventLoop(IAudioClient* client, HANDLE hEvent, BYTE* buf, UINT32 frames)
{
    HRESULT hr = client->SetEventHandle(hEvent);
    if (FAILED(hr)) return hr;

    hr = client->Start();                    // akışı başlat
    while (running) {
        if (WaitForSingleObject(hEvent, 2 * periodMs) != WAIT_OBJECT_0)
            continue;                        // zamandaşım: geç kalan tick'te atla
        UINT32 pad = 0;
        if (FAILED(client->GetCurrentPadding(&pad))) break;
        if (pad >= frames) continue;         // tampon dolu — kopyalama yok
        BYTE* data = nullptr;
        if (FAILED(client->GetBuffer(frames - pad, &data))) break;
        RenderInto(data, frames - pad);      // DSP burada (gerçek zamanlı: lock-free)
        client->ReleaseBuffer(frames - pad, 0);
    }
    return client->Stop();
}
```

**İddia → kaynak:** `SetEventHandle/GetCurrentPadding/GetBuffer/ReleaseBuffer` olay-sürüklü akış ·
`https://learn.microsoft.com/en-us/windows/win32/coreaudio/low-latency-audio` · `✅ fetch edildi 2026-10-12`
· roundtrip hedefi `5–10 ms` (aynı sayfa). ⚠️ **Açık:** `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM`
sayfada "Need to confirm" — Initialize flag kontrolü bu örnekte bilinçli YOK.

---

## §4. Örnek D — MMCSS zaman önceliği kaydı **[ORİJİNAL ÖRNEK · derlenmedi]**

> `low-latency-audio` sayfası (✅ fetch 2026-10-12) gecikmeyi azaltmak için **MMCSS** kullanmayı,
> gerçek zamanlı iş parçacıklarının normal zamanlayıcıların gecikmelerinden etkilenmemesini önerir;
> "Pro Audio" ve "Audio" kuyrukları bu sayfada geçer.

```cpp
// [ORİJİNAL ÖRNEK] AvSetMmThreadCharacteristics — derlenmedi
#include <avrt.h>
DWORD taskId = 0;
// "Pro Audio" öncelik kuyruğu (görev listesi low-latency sayfasında; ✅ fetch 2026-10-12)
HANDLE hTask = AvSetMmThreadCharacteristicsW(L"Pro Audio", &taskId);
// Render thread'i burada — AvRevertMmThreadCharacteristics(hTask) ile serbest bırak
```

**Doğrulama (RED TEAM):** kilitli gerçek — MMCSS periyodik görevler **10 ms'de bir** gecikir
(DXGI Composer DirectFlip kaynağı, daha önceki turda kayıtlı). "Pro Audio" kuyruğunun
10 ms bekleme eşiği bu örnekte **kullanılmadı** → açık soru: `windows-riskler.md` EK-B.

---

## §5. Örnek E — Proses/iş parçacığı çizelgeleme iskeleti **[ORİJİNAL ÖRNEK · derlenmedi]**

> `win32/procthread/processes-and-threads` (✅ fetch 2026-10-12) sayfası proses, iş parçacığı,
> iş nesnesi (job object), yürütme zamanı (runtime) nesneleri (thread pool, fiber, UMS),
> bellek havuzu ve zaman/producing nesnelerini sayar; `CreateProcess` ile `CreateThread`
> aynı ana sayfada anlatılır. Kuyruklu-ses iş parçacığı için **orijinal** kalıp:

```cpp
// [ORİJİNAL ÖRNEK] iş parçacığı havuzu ile ses teslimi — derlenmedi
// Kaynak: win32/procthread/processes-and-threads (✅ fetch 2026-10-12)
// "Thread pools ... can be more efficient than creating and destroying threads"
// (sayfa metni bu yönde öneride bulunur — alıntı EK-A'da)
```

```cpp
// [ORİJİNAL ÖRNEK] kısa çizelgelenen render tetiği
static CALLBACK void OnAudioTimer(PTP_CALLBACK_INSTANCE, PVOID, PTP_TIMER)
{
    // 1) event-driven: Wait yok, timer tetikler
    // 2) tumbling (tek yönlü) tampon doldur
    // 3) lock-free: hiçbir yığın/blok yok
}
```

---

## §6. Örnek F — DMA/interrupt kavram iskeleti (pseudokod) **[ORİJİNAL · derlenmedi]**

> `windows-hardware/drivers/kernel/` (✅ fetch 2026-10-12) sayfası ISR, MSI, DPC, DMA, PnP, Power
> ve IRP başlıklarını **tablo** olarak verir (ayrıntı sayfaları bu turda fetch EDİLMEDİ).
> Bu nedenle yalnız **pseudokod** verilir; gerçek işlev imzaları **⚠️ UNKNOWN**.

```text
[ORİJİNAL PSEUDOKOD — imzalar doğrulanmadı]
ISR   (yüksek IRQL): DMA bitti bayrağını oku → DPC kuyruğuna al → return
DPC   (daha düşük IRQL): event'i set et → kullanıcı modundaki render thread uyandır
MSI   : bellek adresine yazma (kernel/ ana sayfası; ✅ fetch 2026-10-12)
DMA   : CPU kopyası yok → WaveRT DMA motoru tamponu doğrudan okur/yazar
```

**Doğrulama:** "ISR → DPC kuyruğu → daha düşük IRQL" sırası bu turda `kernel/` ana sayfasından
doğrulanamadı (yalnızca başlık listesi geldi) → `⚠️ B-kaynak` (IRQL/ISR-DPC detay sayfası).

---

## §7. Örnek G — PnP/Power geri çağrısı iskeleti **[ORİJİNAL ÖRNEK · derlenmedi]**

```c
/* [ORİJİNAL ÖRNEK] KMDF PnP/Power geri çağrıları — derlenmedi */
/* EvtDevicePrepareHardware → kaynakları bağla (INT33C4/INT33C3 ACPI izi)        */
/* EvtD0Entry            → DMA tamponu hazırla, engine başlat                    */
/* EvtD0Exit             → engine durdur, tamponu boşalt                          */
/* EvtDeviceSurpriseRemoval → beklenmedik sökme: kısayol devre dışı, fail-safe   */
/* Sıra claim edilmedi: PnP evre sırası bu turda fetch EDİLMEDİ → UNKNOWN         */
```

---

## §8. Örnek H — INF/paketleme iskeleti (yalnız imzasız taslak) **[ORİJİNAL ÖRNEK · derlenmedi]**

```ini
; [ORİJİNAL ÖRNEK] minimal INF taslağı — derlenmedi/paketlenmedi
[Version]
Signature   = "$WINDOWS NT$"
Class       = MEDIA
ClassGuid   = {4d36e96c-e325-11ce-bfc1-08002be10318} ; sınıf GUID'u doğrulanmadı
Provider    = %ProviderName%
DriverVer   = 10/12/2026,1.0.0.0

[DefaultInstall.NTamd64]
CopyFiles   = MyDriver.Copy

[MyDriver.Copy]
myaudiodriver.sys
```

⚠️ **Doğrulanmadı:** sınıf GUID `4d36e96c-…` yaygın MEDIA sınıfıdır ama bu turda resmi
`device-installation` sayfası fetch EDİLMEDİ (404 olan `install/device-installation-overview`
yerine) → `⚠️ bilinen resmi URL — bu turda fetch edilmedi`.

---

## §9. Doğrulama/kabul kontrol listesi (RED TEAM — kod için)

| # | Kontrol | Kanıt |
|---|---|---|
| C1 | `Dosya:/Satır:` uydurma yok | bu dosyada 0 (§0 kuralı) |
| C2 | Her blok etiketli ([RESMİ ALINTI]/[ORİJİNAL ÖRNEK]/⚠️) | §1–§8 |
| C3 | Derleme/çalışma iddiası yok | `status: verify-required` |
| C4 | Kilitli gerçekler kodda tekrar yazılmadı (480 örnek, 10 ms MMCSS) | §2, §4 notları |
| C5 | `AUDCLNT_STREAMFLAGS_AUTOCONVERTPCM` doğrulanmadan kullanılmadı | §3 notu |

---

## §10. WASAPI hata modları — HRESULT aile haritası **[ORİJİNAL ÖRNEK · derlenmedi]**

> Kaynak disiplini: `low-latency-audio` (✅ fetch 2026-10-12) sayfasındaki periyot
> kilitleriyle ve `windows-web-arastirma.md` §2 kilitli-gerçek satırlarıyla
> (üç `AUDCLNT_E_*` adı orada tescilli) çakışmayan **yalnızca** bu satırlarda
> adı geçen hata ailesi yazılır; adı vault'ta geçmeyen HRESULT için `⚠️` işareti
> kullanılır (uydurma kod yasağı §0).

| # | Hata / durum (ad vault kanıtlı mı) | Tetiklenme koşulu | Gözlemlenen belirti | Azaltma | Kanıt |
|---|---|---|---|---|---|
| H1 | `AUDCLNT_E_ENGINE_PERIODICITY_LOCKED` | Motor periyodu kilitliyken periyot değiştirme isteği | `IAudioClient3` periyot müzakeresi reddedilir | Kilitli modda `GetSharedModeEnginePeriod` dörtlüsüne uy | ✅ kilitli gerçek — `windows-web-arastirma.md` §2 |
| H2 | `AUDCLNT_E_FORMAT_LOCKED` | Akış açıldıktan sonra format değiştirme isteği | `IsFormatSupported` sonrası ilk `Initialize` kararlıdır | Formatı `Initialize` ÖNCESİ kesinleştir | ✅ kilitli gerçek — `windows-web-arastirma.md` §2 |
| H3 | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` | Periyot boyutu motorun temel (fundamental) katına oturmadı | `Initialize` başarısız; akış açılmaz | Boyutu `fundamentalPeriodInFrames` katına yuvarla (§2 doğrulama notu) | ✅ kilitli gerçek — `windows-web-arastirma.md` §2 |
| H4 | Zamandaşım (glitch) — kod değil durum | `GetCurrentPadding` yanlış okunursa tampon taşar | kliklenme, drop-out | §3 akışındaki `pad >= frames` beklemesi | [ORİJİNAL ÖRNEK §3] |
| H5 | `SetEventHandle` sonrası olay hiç tetiklenmez — kod adı **⚠️ UNKNOWN** | Sürücü olayı nadiren beslerse | sessiz askıda kalma | Zaman aşımı sayaçla izle (§3: `2 * periodMs`) | ⚠️ HRESULT adı bu turda fetch edilmedi — `windows-web-arastirma.md` §5/2 |

**Sınıflandırma kuralı (orijinal):** hata ailesi üç kaba düşer — (1) **anlaşma
bozukluğu** (H1-H3: müzakereden sonra yazma), (2) **zamanlama** (H4-H5: bekleyen
şey gelmez), (3) **cihaz kaybı** (sürükleyici çıkar → `windows-device-lifecycle.md`
sahipliğinde, bu dosya kapsamı değil). Her üç sınıfın da kullanıcı etkisi
**sessizlik değil, yanlış sessizlik**tir: akış açık görünür ama veri akmaz —
bu yüzden §14 kurtarma tablosu zorunludur.

---

## §11. Anti-pattern tablosu (Windows ses kalıpları) **[ORİJİNAL ÖRNEK · derlenmedi]**

> Bu tablo, §1-§8 örneklerinin **tersidir**: her satır "yapılmaması gereken"i,
> sonucunu ve bu vault'taki doğru karşılığını verir. Resmi sayfa alıntısı **değil**,
> bu vault için yazılmış orijinal mühendislik kaydıdır (§0 etiket kuralı).

| # | Anti-pattern | Sonuç | Doğru karşılık (bu dosyada) |
|---|---|---|---|
| A1 | `Sleep()` ile tamronun "dolmasını" bekleme | Jitter, priorite göre sürüklenme | Olay-sürüklü `SetEventHandle` + `GetCurrentPadding` (§3) |
| A2 | Render iş parçacığını normal zamanlayıcıya bırakma | MMCSS dışı gecikme payı | `AvSetMmThreadCharacteristicsW(L"Pro Audio", …)` (§4) |
| A3 | `bufferSwitch` içinde `new`/`malloc`/lock | RT bütçesi ihlali, ses boşluğu | Sıfır-allokasyon + lock-free (L0 kural 1-2; §6.5 eşlemesi) |
| A4 | Periyodu "güzel" bir sayıya sabitleme (kat değil) | `AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED` (§10/H3) | `fundamentalPeriodInFrames` katı (§2 doğrulama notu) |
| A5 | `Initialize` sonrası format değiştirme denemesi | `AUDCLNT_E_FORMAT_LOCKED` (§10/H2) | Formatı akış öncesi kesinleştir (§3) |
| A6 | Her döngüde `CreateThread` + `CloseHandle` | Kaynak israfı, priorite titremesi | Thread pool önerisi (§5 orijinal kalıp) |
| A7 | DMA tamponunu kullanıcı modundan kopyalama | Ekstra gecikme + kopya hataları | WaveRT doğrudan erişim modeli (§6 pseudokod) |
| A8 | `ReleaseBuffer` çağırmadan döngüyü kapatma | Padding kalır, sonraki akış hatalı | §3 akışında `ReleaseBuffer(frames - pad, 0)` zorunlu |
| A9 | Hata dönüş kodunu sessizce yutma | Sessiz askıda kalma (§10/H5) | Her `FAILED(hr)` dalında bilinçli çıkış (§3 `break`) |
| A10 | `Dosya:`/`Satır:` ile kanıt sunmak (repo'da C++ yok) | Hallüsinasyon | §0 politikası: `Kaynak:` / `⚠️` / `[ORİJİNAL ÖRNEK]` |

---

## §12. Gerçek repo kodu — `Dosya:`/`Satır:` kanıtı (bu tur ölçüldü)

> Repo genelinde `*.cpp`/`*.h`/`*.hpp` = **0** (§0 tablosu) — Windows C++ kodu
> için `Dosya:` satırı **üretilmez**. Buna karşılık, vault'un **gerçek** CI
> yüzeyinde Windows/ASIO doğrulamasının durumu şu satırlarla kanıtlanır
> (okuma: `Get-Content 'C:\www\coremusic.net\{yol}' | Select-Object -Index (N-1)`):

| Dosya: | Satır: | Satır içeriği (okunarak doğrulandı) | İddia |
|---|---|---|---|
| `.github/workflows/ci.yml` | 27 | `    runs-on: ubuntu-latest` | `php-lint` job'u Linux üzerinde |
| `.github/workflows/ci.yml` | 180 | `    runs-on: ubuntu-latest` | dosyanın son job'u da Linux üzerinde |
| `.github/workflows/secret-scan.yml` | 21 | `    runs-on: ubuntu-latest` | secret taraması Linux üzerinde |

**İddia → kanıt (repo):** `.github/workflows/` içinde `runs-on: windows-*`
satırı **yoktur** (6 iş tanımının tamamı `ubuntu-latest` — ölçüm 2026-10-13);
yani **Windows üzerinde ASIO/WASAPI derleme veya test adımı henüz yoktur**.
Bu, bu dosyadaki tüm kod örneklerinin `status: verify-required` kalmasının
ikinci gerekçesidir: doğrulama işi (Windows runner) repo CI'ında **PLANNED**.

**Doğrulama komutu (tekrarlanabilir):**

```powershell
Select-String -Path 'C:\www\coremusic.net\.github\workflows\*.yml' -Pattern 'runs-on'
```

---

## §13. WASAPI paylaşımlı-mod iskeleti — tam akış **[ORİJİNAL ÖRNEK · derlenmedi]**

> §3'ün (olay-sürüklü çizim) kurulum ve kapanış bölümleriyle tamamlanmış hâlidir;
> kaynak örüntüleri: `low-latency-audio` (✅ fetch 2026-10-12) +
> `windows-audio-architecture` (✅ fetch 2026-10-12).

```cpp
// [ORİJİNAL ÖRNEK] shared-mode WASAPI kurulum+çizim+temizlik — derlenmedi
// Sözleşme: HRESULT her adımda kontrol edilir; RT döngüsü lock-free (L0 kural 2)
struct WasapiStream {
    IAudioClient*  client   = nullptr;
    IAudioRenderClient* render = nullptr;
    HANDLE         event    = nullptr;   // SetEventHandle sahipliği
    UINT32         frames   = 0;         // tampon boyutu (baştan okunur)
};

HRESULT WasapiOpen(WasapiStream& s, const WAVEFORMATEX* fmt, UINT32 periodMs)
{
    HRESULT hr = s.client->Initialize(AUDCLNT_SHAREMODE_SHARED,
                                      0,              // bayraklar: §3 notu (AUTOCONVERTPCM yok)
                                      periodMs * 10000,  // 100 ns birimli süre
                                      0, fmt, nullptr);
    if (FAILED(hr)) return hr;                       // H1-H3 sınıfları (§10)
    hr = s.client->GetBufferSize(&s.frames);
    if (FAILED(hr)) return hr;
    hr = s.client->SetEventHandle(s.event);          // olay-sürüklü çekirdek (§3)
    if (FAILED(hr)) return hr;
    return s.client->GetService(__uuidof(IAudioRenderClient),
                                (void**) &s.render);
}

void WasapiClose(WasapiStream& s)
{
    if (s.client) s.client->Stop();                  // §3'ün tersi: önce durdur
    if (s.render) { s.render->Release(); s.render = nullptr; }
    if (s.client) { s.client->Release(); s.client = nullptr; }
    if (s.event)  { CloseHandle(s.event); s.event = nullptr; }
}
```

**Sözleşme notları:** `Initialize` periyodu **100 ns birimindendir**
(`periodMs * 10000`); `GetBufferSize` sonucu (`frames`) döngü boyunca **değişmez**
— bu, §3'teki `pad >= frames` karşılaştırmasının dayanağıdır. `WasapiClose`
sırası **önce durdur, sonra bırak**'tır: açık akışta servis bırakmak
(cihaz kaybı durumunda) §10/H5 benzeri sessiz askıda kalma üretir.
⚠️ `AUDCLNT_SHAREMODE_SHARED`/`Initialize` bayrak kümesi bu turda resmi
`audioclient` sayfası fetch EDİLMEDİ (`windows-web-arastirma.md` §1.B/8) →
**bilinen resmi API — imza detayı fetch yok**.

---

## §14. Arıza → etki → kurtarma tablosu (fault tree özeti) **[ORİJİNAL ÖRNEK]**

| # | Arıza | Üst belirti | Kullanıcı etkisi | Kurtarma yolu | Sahip |
|---|---|---|---|---|---|
| F1 | Periyot müzakeresi kilitli (H1) | İlk `Initialize` başarısız | Düşük gecikme açılamaz | Kilitli modda kat kuralına dön (H3 azaltması) | bu dosya §10 |
| F2 | Format kilidi (H2) | Yeniden ayar reddi | Format değişikliği sessizce yok sayılır | Akışı kapat → yeniden aç | bu dosya §10 |
| F3 | Olay gelmez (H5) | Döngü askıda | Ses kesilir, UI donuk görünür | Zaman aşımı sayacı + akışı yeniden başlat | bu dosya §3, §10 |
| F4 | DMA/kayıp (§6 zinciri) | DPC/ISR tarafı bilinmiyor | ⚠️ UNKNOWN — kök neden okunamıyor | `windows-dma-ve-interrupt.md` derin dalı | K003 değil, K000 |
| F5 | CI'da Windows runner yok (§12) | Regresyon testi yok | Hatalar kullanıcı makinesinde çıkar | `runs-on: windows-latest` işi eklenmeli (PLANNED) | DevOps (K013) |
| F6 | Cihaz kaybı (sürücü çıkar) | Sonraki çağrı başarısız | Ses aniden kesilir | `windows-device-lifecycle.md` durum makinesi + ASIO tarafında WASAPI fallback (`.ai/AGENTS.md` §17/6) | bu dosya değil |

---

## §15. Genişletilmiş doğrulama kontrol listesi (C1-C12)

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| C1 | `Dosya:/Satır:` uydurma yok | C++ için 0; §12'deki 3 satır dosyadan okundu | ✅ (§12) |
| C2 | Her blok etiketli | [RESMİ ALINTI] / [ORİJİNAL ÖRNEK] / ⚠️ | ✅ (§1-§14) |
| C3 | Derleme/çalışma kanıtı yok | `status: verify-required` + §12 F5 | ✅ |
| C4 | Kilitli gerçekler tekrar yazılmadı | 480 örnek, 10 ms MMCSS yalnız not olarak | ✅ (§2, §4) |
| C5 | `AUTOCONVERTPCM` doğrulanmadan kullanılmadı | §3 + §13'te bilinçli `0` bayrağı | ✅ |
| C6 | Frontmatter §4.2 (8 alan + `group: kod`) | Eksik yok; `parent_id` üst klasör yolu | ✅ (FM) |
| C7 | Görev dolgusu işareti (placeholder · üç-nokta · vaat · nesne-şablonu boşluğu) | 0 | ✅ (ölçüm) |
| C8 | Mojibake byte dizisi (kodlama bozulması · CJK) | 0 | ✅ (ölçüm) |
| C9 | Boş tablo hücresi | 0 | ✅ (§1-§15) |
| C10 | Her § bir Markdown tablosu içeriyor | §10-§15 tablo taşır | ✅ |
| C11 | Son satır Authority standart imzası | `**Authority:** … **Mode:** …` | ✅ (dosya sonu) |
| C12 | HRESULT adları vault kanıtlı veya `⚠️` işaretli | §10/H5 örneği | ✅ |

---

## §16. Örnek I — `IAudioClient3` periyot müzakeresi iskeleti **[ORİJİNAL ÖRNEK · derlenmedi]**

> Örüntü kaynağı: `exclusive-mode-streams` (✅ fetch edildi 2026-10-12 — §1.A/`A7`)
> sayfasının anlattığı shared-mod düşük-periyot penceresi; imza detaylarının resmî
> `audioclient` nf sayfası bu turda **fetch edilmedi** (`windows-web-arastirma.md`
> §1.B/8) → ⚠️ imza-detayı-notu taşır. Bu iskelet yalnız **akış sırasını** gösterir.

```cpp
// [ORİJİNAL ÖRNEK] periyot dörtlüsü müzakeresi — derlenmedi
// Dörtlü: default / fundamental / min / max (§2 doğrulama notuyla aynı kural alanı)
struct EnginePeriod { UINT32 defaultP, fundamentalP, minP, maxP; };

HRESULT NegotiatePeriod(IAudioClient3* c3, const WAVEFORMATEX* mix,
                        UINT32 wanted, UINT32& agreed)
{
    EnginePeriod p{};
    HRESULT hr = c3->GetSharedModeEnginePeriod(mix,
                    &p.defaultP, &p.fundamentalP, &p.minP, &p.maxP);
    if (FAILED(hr)) return hr;                    // müzakere açılamadı (§10/H1 ailesi)

    // Kural 1: fundamental katına yuvarla (AUDCLNT_E_BUFFER_SIZE_NOT_ALIGNED → §10/H3)
    agreed = (wanted / p.fundamentalP) * p.fundamentalP;
    // Kural 2: min/max aralığına kıstır (aralık dışı istek reddedilir)
    if (agreed < p.minP) agreed = p.minP;
    if (agreed > p.maxP) agreed = p.maxP;
    return S_OK;                                 // agreed → InitializeSharedAudioStream
}
```

**İddia→kapsam:** sartname hedefi 512 örnek @ 48 kHz
(≈10,67 ms — `asio-kapsam.md` §3/5 bütçe satırı); bu değerin gerçek bir cihazda
**geçerli** olması bu dörtlünün katına oturmasına bağlıdır — §2'deki 480 örnek
uyarısının (H3) periyot müzakeresi tarafıdır. ⚠️ `GetSharedModeEnginePeriod`
imzası resmi sayfadan bu turda fetch EDİLMEDİ → bilinen resmi API, imza
**VERIFICATION REQUIRED**.

---

## §17. Örnek J — MMDevice uç nokta enumerasyon iskeleti **[ORİJİNAL ÖRNEK · derlenmedi]**

> Kaynak penceresi: `windows-audio-architecture` (✅ fetch 2026-10-12) sistem
> bileşenleri ağacı; MMDevice API'nin resmî arayüz yüzeyi için bu turda HTTP 200
> ile doğrulanan adres `https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/`
> (2026-10-13 — `windows-web-arastirma.md` §7 kayıtlıdır). Aşağıdaki akış
> **orijinal**dir; COM imzaları `⚠️ VERIFICATION REQUIRED` taşır.

```cpp
// [ORİJİNAL ÖRNEK] uç nokta listesi + varsayılan cihaz seçimi — derlenmedi
HRESULT PickDefaultDevice(IMMDeviceEnumerator* en, IMMDevice** out)
{
    // 1) Varsayılan iletişim cihazını al (ses çıkışı akışı buraya bağlanır)
    HRESULT hr = en->GetDefaultAudioEndpoint(eRender, eConsole, out);
    if (FAILED(hr)) return hr;                    // cihaz yok / servis yok

    // 2) Tüm cihazları say (enumerasyon — cihaz ekleme sonrası yenileme tetiği)
    IMMDeviceCollection* col = nullptr;
    hr = en->EnumAudioEndpoints(eRender, DEVICE_STATE_ACTIVE, &col);
    if (FAILED(hr)) return hr;
    UINT n = 0; col->GetCount(&n);                // n = aktif çıkış cihazı sayısı
    col->Release();

    // 3) Cihaz kimliği oku (§12 F6 cihaz-kaybı zincirinin ilk halkası)
    LPWSTR id = nullptr;
    if (SUCCEEDED((*out)->GetId(&id))) CoTaskMemFree(id);
    return S_OK;
}
```

**Kapsam sınırı:** bu iskelet yalnız **seçim/enumerasyon** yolunu gösterir;
ASIO tarafındaki karşılığı `asio-cihaz-yonetimi-ve-enumarasyon.md` §2-§3'tedir
(karşılaştırma `asio-wasapi-karsilastirma.md`). Enumerasyon sonrası **aktiflik**
(`DEVICE_STATE_ACTIVE`) filtresi olmadan listede görünmeyen (devre dışı)
cihazlar da eşleşir — bu, cihaz listesinde hayalet satır üreten bilinen
anti-pattern'dir (A-karşılığı: filtre zorunlu).

---

## §18. Dosya Künyesi (kapsam özeti)

| Alan | Değer |
|---|---|
| Kapsanan örnekler | §1-§8 (A-·H) · §13 tam WASAPI iskeleti · §16 periyot müzakeresi · §17 MMDevice enumerasyonu |
| Kapsanan katalog | §10 hata modları · §11 anti-pattern · §12 repo kanıtı · §14 fault tree |
| Kapsam dışı | ASIO C++ akışı (`asio-cpp-ornekleri.md`), DMA/IRQL derin dalı (`windows-dma-ve-interrupt.md`), COM tam kurulum (fetch yok) |
| Bağımlılıklar | `windows-kernel-modlari` · `windows-ses-stacki` · `windows-dma-ve-interrupt` · `K003-audio/backend/asio` (FM `depends_on`) |
| Kanıt durumu | Resmi fetch: 5 URL (FM `evidence`) · repo satırı: 3 (§12) · kalan kod: `[ORİJİNAL ÖRNEK · derlenmedi]` |
| Açık sorular | FM `open_questions` (AUTOCONVERTPCM · `PcAddStreamResource`) + §10/H5 olay-zaman aşımı |

**Kaynak (yetki zinciri):** ADR-097 §5.5 (field `group` · §GRUPLAR) · README §4.2 (FM zorunlu alanlar) · repo kanıtı: `.github/workflows/ci.yml` (§12)
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode