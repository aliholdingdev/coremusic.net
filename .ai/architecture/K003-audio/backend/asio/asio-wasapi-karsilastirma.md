---
title: "ASIO ve WASAPI Karşılaştırması — Mod, Gecikme, Saat ve Oturum Semantiği"
node_id: "K003-audio/backend/asio/asio-wasapi-karsilastirma"
node_type: "mimari"
status: "draft"
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient3-getsharedmodeengineperiod (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclockadjustment (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclockadjustment-setsamplerate (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/volume-controls (2026-10-12)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants — dört durum sabiti ve yalnız ACTIVE'te akış açılabilir kuralı (2026-10-12)"
  - "https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp (2026-10-12)"
  - "https://docs.juce.com/master/classjuce_1_1AudioIODeviceType.html (2026-10-12)"
validation: "Metin doğrulaması tamamlandı: 9 URL bu dosyada, 9 mevcut asio dosyasındaki 52 URL ile kesişim yok; kaynak dosya satır içeriği indirilerek karşılaştırıldı."
open_questions:
  - "Neva Engine ortak paydastratejisi ASIO öncelikli mi, yoksa WASAPI exclusive bir fallback mi? (ADR kararı bekliyor)"
  - "IAudioClockAdjustment::SetSampleRate cihaz-kipi bağımlılığı (V1/V2/V3 motor) için tam kaynak matrisi dotnet API'sinde tam olarak doğrulanmadı."
  - "WASAPI exclusive modda paylaşılan ses oturumu (audio session) semantiğinin uygulayıcı-yorumu JUCE tarafında nasıl çözümlendi? (kod okundu, MS sözleşmesi ile birebir eşleşme doğrulanmadı)"
date: "2026-10-12"
updated: "2026-10-12"
group: mimari
---

# ASIO ve WASAPI Karşılaştırması

> Bu düğüm, ASIO (Steinberg) ile WASAPI (Windows Core Audio) arasında **mod semantiği,
> gecikme yapılandırması, saat kaynakları ve oturum/volum kontrolü** eksenlerinde
> kaynak-kanıtlı bir karşılaştırma yapar. K000 WASAPI derinlemesine bu dosyadan
> bağımsızdır; burada yalnız ASIO ile karşılaştırma gerektiren yüzey işlenir.

---

## §0 — ELI15: İki kapı, aynı oda

Bir ses uygulaması müzik aletinin sesini bilgisayara sokup hoparlöre göndermek
istersen iki farklı kapı kullanabilir. **WASAPI**, Windows'un kendi kapısıdır;
içeride iki ayrı geçit vardır: **paylaşımlı (shared)** kipte ses sistemi araya
girer, tüm uygulamaların sesini karıştırır, sen kuyruğa yazılırsın — kolay ama
ekstra bir gecikme halkası ekler. **Özel (exclusive)** kipte kapı sadece sana
açılır, karışıma dokunmadan ham örnekler doğrudan sürücüye gider — daha hızlı,
ama oda artık senin, kimse başka ses çalamaz. **ASIO** ise bu kapıların ikisini
de geçer; doğrudan sürücünün tamponunu kendi belleğinle paylaşır, işletim
sisteminin karışım ve volum katmanını hiç görmez. Fiyatı: sürücünün sana verdiği
tamponu saatinde doldurmak zorundasın, aksi halde ses çatırdar.

İkinci fark **saat** meselesidir. WASAPI'ın saati işletim sistemi tarafından
kullanıma açıktır (`IAudioClock`, `IAudioClockAdjustment`); ASIO'nun saati ise
sürücünün içindedir ve sana yalnız `ASIOTime` yapısı içinde toptan bildirilir.
Üçüncü fark **oturum** meselesidir: WASAPI'ta her akış bir "ses oturumuna"
bağlıdır, sistem bu oturumun ses seviyesini tek tek veya topluca kısabilir;
ASIO'da böyle bir katman yoktur, ses seviyesini sen yazılımda üretirsin.

Aşağıdaki bölümler bu üç farkı da kaynak URL'lerle tek tek söküp, Neva Engine
için bir karar matrisi çıkarır.

---

## §1 — WASAPI nedir ve ASIO nerede durur

### §1.1 WASAPI'nin tanımı (Windows Core Audio ailesi)

| # | Konu | Kaynak-kanıt |
|---|------|--------------|
| 1 | WASAPI, Windows Core Audio API ailesinin bir parçasıdır | `coreaudio/wasapi` sayfası başlığı "WASAPI"yi Windows Core Audio API ailesinin bileşeni olarak tanımlar |
| 2 | İki kipi vardır: shared ve exclusive | Aynı sayfa shared mode ile exclusive mode akışlarını ayrı ayrı anlatır |
| 3 | Exclusive mod yalnız etkin (ACTIVE) cihazlarda açılır | `coreaudio/wasapi` exclusive-mode bölümü cihaz-state koşulunu şart koşar |
| 4 | ASIO bu ailenin parçası değildir | ASIO ayrı bir Steinberg SDK'sidir; K000 bu dosyanın kapsamı dışında ele alınır |

### §1.2 ASIO'nun karşılığı

| # | ASIO tarafı | WASAPI karşılığı | Not |
|---|-------------|------------------|-----|
| 1 | Sürücü-özel tampon (buffer) paylaşımı | `IAudioClient::Initialize` + `IAudioRenderClient/IAudioCaptureClient` | İkisi de doğrudan tampon erişimi verir |
| 2 | `ASIOCreateBuffers` ile çift tampon | Event/pull tabanlı akış tamponu | ASIO'da tampon çifti sabit; WASAPI'ta boyut kipe göre değişir |
| 3 | `bufferSwitch` zamanlayıcısı | `IAudioClient::SetEventHandle` olayı | İkisi de uygulamayı "sıradaki blok" diye uyandırır |
| 4 | `ASIOGetSampleRate` / `sampleRateDidChange` | `IAudioClock::GetFrequency` + `IAudioClockAdjustment::SetSampleRate` | WASAPI saat frekansını ayrıca ayarlanabilir kılar |
| 5 | Karışım/volum katmanı yoktur | Ses oturumu + sisteme özgü volum kontrolü | WASAPI'ta kısma katmanı vardır, ASIO'da yoktur |

> **Kritik ayrım:** ASIO uygulamaya *mutlak* tampon sahipliği verir; WASAPI
> exclusive bunu *yaklaştırır* ama ses oturumu ve cihaz-yönetimi nesneleri
> (`IMMDevice`) üzerinden işletim sistemi her zaman görünür durumdadır.

---

## §2 — Mod semantiği: shared, exclusive ve shared-low-latency

### §2.1 Üç kip

| # | Kip | Karışım (mix) | Gecikme | Kim kullanır |
|---|-----|---------------|---------|--------------|
| 1 | Shared | Evet, sistem karışıtır | Orta (varsayılan motor periyodu) | Normal uygulamalar |
| 2 | Exclusive | Hayır, doğrudan sürücü | En düşük | Pro-audio, bit-accurate çıkış |
| 3 | Shared low-latency (IAudioClient3) | Evet | Shared'e göre düşük (motor periyodu küçültülür) | Windows 10 1803+ modern uygulamalar |

### §2.2 JUCE kodunda kip eşlemesi (kaynak-kanıtlı)

Kaynak dosya `juce_WASAPI_windows.cpp` içinde kipler tek bir enum ile temsil
edilir ve her yardımcı fonksiyon bir kipi tek satırda test eder:

```cpp
// Kaynak: https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp (2026-10-12)
static bool isExclusiveMode (WASAPIDeviceMode deviceMode) noexcept
{
    return deviceMode == WASAPIDeviceMode::exclusive;
}

static bool isLowLatencyMode (WASAPIDeviceMode deviceMode) noexcept
{
    return deviceMode == WASAPIDeviceMode::sharedLowLatency;
}
```

Aynı dosyada kipin kullanıcıya görünen adı da sabittir:

```cpp
// Kaynak: https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp (2026-10-12)
if (mode == WASAPIDeviceMode::exclusive)         return "Windows Audio (Exclusive Mode)";
```

### §2.3 Exclusive modda blok bekleme semantiği

JUCE render thread'i exclusive ile shared arasında farklı bloklar:
non-exclusive modda örnek bulunamazsa olay bekleme yoluna gider ve kod bunu
yorum olarak açıkça belirtir:

```cpp
// Kaynak: https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp (2026-10-12)
if (samplesToDo == 0)
{
    // This can ONLY occur in non-exclusive mode
    if (! thread.threadShouldExit() && WaitForSingleObject (clientEvent, 1000) == WAIT_OBJECT_0)
        continue;

    break;
}

if (isExclusiveMode (deviceMode) && WaitForSingleObject (clientEvent, 1000) == WAIT_TIMEOUT)
    break;
```

**Yorum:** exclusive modda `1000 ms` zaman aşımı bir hata sayılır (thread
döngüsü kırılır); shared modda aynı durum "sistem henüz örnek göndermedi"
anlamına gelir ve bekleme devam eder. ASIO'da bu ayrım yoktur — `bufferSwitch`
sürücü tarafından çağrılmak zorundadır, çağrı gelmezse uygulama bekler
(bkz. `asio-latency-ve-jitter.md` §5).

### §2.4 Kip ile cihaz durumunun etkileşimi

| # | Cihaz durumu | Shared akış | Exclusive akış | ASIO karşılığı |
|---|--------------|-------------|----------------|----------------|
| 1 | `DEVICE_STATE_ACTIVE` | Açılabilir | Açılabilir | Sürücü mevcut (`ASIOInit` → `ASE_OK`) |
| 2 | `DEVICE_STATE_DISABLED` | Açılamaz | Açılamaz | Sürücü listelenmeyebilir |
| 3 | `DEVICE_STATE_NOTPRESENT` | Açılamaz | Açılamaz | `ASE_NotPresent` |
| 4 | `DEVICE_STATE_UNPLUGGED` | Açılamaz | Açılamaz | `ASE_NotPresent` / `ASE_HWMalfunction` |

> Durum sabitleri ve "yalnız `DEVICE_STATE_ACTIVE` cihazda akış açılabilir"
> kuralı `device-state-xxx-constants` sayfasındadır (kanıt §8). ASIO karşılık
> sütunu **kavramsal eşlemedir** — gerçek kod davranışının ölçümü
> `asio-hot-swap-ve-device-loss.md` §9.6 test planına bağlıdır ve
> **çalıştırılmamıştır**.

### §2.5 Exclusive modun bedeli: münhasırlık

| # | Bedel | Sonuç |
|---|-------|-------|
| 1 | O cihazda başka uygulama akış açamaz | Sistem bildirim sesleri o cihazdan çıkmaz |
| 2 | `Initialize` başarısız olabilir (diğer uygulama kipte) | Uygulama `AUDCLNT_E_DEVICE_IN_USE` benzeri hata ile karşılaşır |
| 3 | Kip değişimi akışı kapatmayı gerektirir | Yeniden `Initialize` zorunlu |
| 4 | ASIO'da buna karşılık zaten vardır (kural 9: exclusive lock) | İki API için de tek-uygulama kuralı |

---

## §3 — Gecikme yapılandırması: motor periyodu ve IAudioClient3

### §3.1 Shared mod periyodu

| # | API | Ne verir | Birim |
|---|-----|----------|-------|
| 1 | `IAudioClient::GetDevicePeriod` | varsayılan + minimum periyot | 100-ns birim (`REFERENCE_TIME`) |
| 2 | `IAudioClient3::GetSharedModeEnginePeriod` | default / fundamental / min / max periyot (paylaşımlı motor) | 100-ns birim |
| 3 | ASIO karşılığı | `ASIOGetBufferSize` + sürücünün kendi aralığı | sample |

> `GetSharedModeEnginePeriod` dört parametre döndürür: `defaultPeriod`,
> `fundamentalPeriod`, `minPeriod`, `maxPeriod`. `fundamentalPeriod` tüm
> periyotların katı olmak zorundadır — uygulama seçtiği boyut bu katta olmalıdır.

### §3.2 JUCE kodunda dört-değerli okuma

```cpp
// Kaynak: https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp (2026-10-12)
void querySupportedBufferSizes (WAVEFORMATEXTENSIBLE format, ComSmartPtr<IAudioClient>& audioClient)
{
    if (isLowLatencyMode (deviceMode))
    {
        if (auto audioClient3 = audioClient.getInterface<IAudioClient3>())
        {
            UINT32 defaultPeriod = 0, fundamentalPeriod = 0, minPeriod = 0, maxPeriod = 0;

            if (check (audioClient3->GetSharedModeEnginePeriod ((WAVEFORMATEX*) &format,
                                                                &defaultPeriod,
                                                                &fundamentalPeriod,
                                                                &minPeriod,
                                                                &maxPeriod)))
            {
                minBufferSize = (int) minPeriod;
                defaultBufferSize = (int) defaultPeriod;
                lowLatencyMaxBufferSize = (int) maxPeriod;
                lowLatencyBufferSizeMultiple = (int) fundamentalPeriod;
            }
        }

        return;
    }

    REFERENCE_TIME defaultPeriod, minPeriod;

    if (! check (audioClient->GetDevicePeriod (&defaultPeriod, &minPeriod)))
        return;
```

**Gözlem:** JUCE, low-latency shared kipte dört değeri doğrudan
`minBufferSize / defaultBufferSize / lowLatencyMaxBufferSize /
lowLatencyBufferSizeMultiple` alanlarına gömer; böylece UI boyut seçimini bu
kat kuralına zorlar. ASIO tarafında buna karşılık gelen zorlama sürücünün
`ASE_InvalidMode` hatasıyla geri döndüğü boyut aralığıdır
(bkz. `asio-buffer-stratejileri.md`).

### §3.3 Gecikme karşılaştırma tablosu (kavramsal)

| Ölçü | ASIO | WASAPI shared | WASAPI exclusive | WASAPI shared low-latency |
|------|------|---------------|------------------|---------------------------|
| Tampon sahibi | Sürücü + uygulama (çift tampon) | Motor + uygulama | Uygulama | Motor + uygulama |
| Boyut kısıtı | Sürücü aralığı | Varsayılan motor periyodu | Cihaz-özel | `fundamentalPeriod` katı |
| Karışım katmanı | Yok | Var | Yok | Var |
| Ölçü birimi | sample | 100-ns | 100-ns | 100-ns |
| Örnek gecikme ölçümü | Sürücü dokümanı | `GetDevicePeriod` + tampon | Cihaz-özel | `GetSharedModeEnginePeriod` |

> **⚠️ VERIFICATION REQUIRED:** tablodaki kavramsal sıralama (exclusive <
> shared-low-latency < shared) gerçek donanım üzerinde ölçülmemiştir; ölçüm
> protokolü `asio-latency-ve-jitter.md` §9'da tanımlıdır ve K003 kapsamında
> "olcum: PLANNED" statüsündedir.

### §3.4 Birim dönüşümü ve raporlama notu

| # | Kaynak birim | Hedef birim | Dönüşüm | Not |
|---|--------------|-------------|---------|-----|
| 1 | `REFERENCE_TIME` (100 ns) | saniye | değer × 10⁻⁷ | `GetDevicePeriod` / `GetSharedModeEnginePeriod` çıktısı |
| 2 | `REFERENCE_TIME` (100 ns) | örnek | değer × fs × 10⁻⁷ | fs = örneklem frekansı (Hz) |
| 3 | Motor periyodu | blok başına örnek | fs × periyot(s) | Blok boyutu değil, motor işleme geçişi |
| 4 | ASIO tampon boyutu | örnek | doğrudan | Karşılaştırma tablosundaki tek doğrudan-ölçüt |

> **Karmaşık nokta:** `GetSharedModeEnginePeriod` **motor** periyotunu verir;
> uygulamanın `IAudioClient::Initialize` için istediği tampon boyutu bunun
> katı olmak zorundadır (`fundamentalPeriod`). ASIO'da ise
> `ASIOGetBufferSize` doğrudan uygulamanın tamponundaki örnek sayısını verir.
> İki sayıyı doğrudan karşılaştırmak **hatalı** olur; her ikisi de örneklem
> frekansıyla saniyeye çevrildikten sonra gecikme olarak kıyaslanabilir.

---

## §4 — Saat kaynakları: IAudioClock ve IAudioClockAdjustment

### §4.1 Kaynak matrisi

| # | Konu | ASIO | WASAPI |
|---|------|------|--------|
| 1 | Saat okuma | `ASIOTime.timeInfo.samplePosition` (sürücü doldurur) | `IAudioClock::GetPosition` + `GetFrequency` |
| 2 | Saat değişimi bildirimi | `sampleRateDidChange` callback'i | `IAudioClockAdjustment::SetSampleRate` (uygulama ister) |
| 3 | Birim | sample (kümülatif) | 100-ns `MFTIME` + `u64Frequency` |
| 4 | Saat sahibi | Sürücü | İşletim sistemi / ses motoru |

### §4.2 IAudioClockAdjustment arayüzü

| # | Üye | Açıklama |
|---|-----|----------|
| 1 | `nn-audioclient-iaudioclockadjustment` | Arayüzün kendisi; yalnız `SetSampleRate` içerir |
| 2 | `nf-audioclient-iaudioclockadjustment-setsamplerate` | `double` ile yeni örneklem frekansı (Hz) |
| 3 | Erişim | `IAudioClient` üzerinden servis edilen bir arayüz (`GetService` ile alınır — bkz. K000) |
| 4 | ASIO karşılığı | Yok — ASIO'da frekansı değiştiremezsin, yalnız bildirim alırsın |

> **Önemli:** `SetSampleRate` ile frekansı değiştirmek, gerçek ses motorunun
> saatini değiştirir mi yoksa yalnız uygulamanın görüğünü mü değiştirir, bu
> ayrım bu dosyada **kanıtlanmamıştır** → `UNKNOWN`. K000 WASAPI düğümünde
> irdelenmesi gerekir (açık sorular §11).

### §4.3 ASIO tarafında saat kaybı

ASIO'da saat kaynağı kaybı tek bir semalle bildirilir: `sampleRateDidChange(0)`
çağrısı. Bu, `asio-zamanlama-ve-saat.md` (mevcut K003 dosyası) kapsamında işlenir;
burada yalnız WASAPI tarafının buna karşılığı aranmıştır: `IAudioClock`
neshil (invalidation) durumunda `GetPosition` hata döndürür ve akış
`AUDCLNT_STREAMFLAGS_RATE_ADAPTATION` davranışına düşebilir —
**⚠️ VERIFICATION REQUIRED** (K000'de doğrulanacak).

---

## §5 — Ses oturumları ve volum kontrolü

### §5.1 Ses oturumu (audio session) kavramı

| # | Konu | Kaynak-kanıt |
|---|------|--------------|
| 1 | Her WASAPI akışı bir ses oturumuna bağlanır | `coreaudio/audio-sessions` genel bakış |
| 2 | Oturum bazında ses seviyesi ayarlanabilir | `coreaudio/volume-controls` per-stream session volume |
| 3 | Cihaz-üstü toplu kısma ayrı bir katmandır | `coreaudio/volume-controls` master/device volume |
| 4 | ASIO'da karşılığı yoktur | ASIO spec'inde oturum kavramı geçmez (K003 mevcut dosyaları) |

### §5.2 Neden ASIO bunu dışlar

| # | Neden | Sonuç |
|---|-------|-------|
| 1 | ASIO doğrudan sürücü-özel tampona yazar | Sistemin volum uygulayacak bir katmanı kalmaz |
| 2 | Uygulama ses seviyesini DSP içinde üretir | Mixer/kontrol tamamen uygulamanın sorumluluğunda |
| 3 | Birden fazla ASIO uygulaması aynı cihazı paylaşamaz (exclusive lock) | Oturum karışımı zaten imkânsızdır |
| 4 | Sistem-üstü bildirim sesleri ASIO cihazından çıkmaz | Kullanıcı bildirimleri farklı (shared) cihazdan akar |

> **Neva Engine etkisi:** 8.1 surround çıkışta kanal-bazlı gain
> (`kural 7`: 32-bit float) tamamen DSP zincirinde tanımlanır; WASAPI'ın
> oturum-katmanı kullanılmadığı için ses seviyesi yalnız kodun kontrolündedir.
> Bu, `DC offset > 0.5V koruma rölesi` kuralıyla (kural 10) birlikte
> donanım-güvenlik açısından avantajdır: hiç kimse uygulamanın çıkışını
> gizlice kısabilir.

### §5.3 JUCE tarafında oturum

`juce_WASAPI_windows.cpp` render thread'i `IAudioRenderClient::GetBuffer` /
`ReleaseBuffer` çiftiyle çalışır; ses oturumu nesnesinin uygulanması (master
volume, ducking) bu dosyada görünmez — JUCE, oturum-volumü için ayrı bir API
sunmaz. **Kanıt:** indirilen 2123 satırlık dosyada oturum-volum çağrıları
görüntülenmedi (tarama: `SetVolume`/`IAudioSessionManager` eşleşmesi yok) →
JUCE WASAPI'ta oturum-volumü **uygulamanın sorumluluğundadır** (bu, kod
taramasına dayanan bir gözlemdir; dokümantasyon iddiası değildir).

---

## §6 — JUCE WASAPI mimarisi (kaynak-kanıtlı sınıf haritası)

### §6.1 Sınıf hiyerarşisi

| # | Sınıf | Satır (kaynak) | Rol |
|---|-------|----------------|-----|
| 1 | `IAudioClient3` (COM tanımı) | 252 | `GetSharedModeEnginePeriod` tanımı (satır 254) |
| 2 | `WASAPIOutputDevice` | ~1100-1210 | render thread + converter |
| 3 | `WASAPIAudioIODevice` | 1213 | `AudioIODevice` + `Thread` + `AsyncUpdater` |
| 4 | `WASAPIAudioIODeviceType` | 1805 | `AudioIODeviceType` alt sınıfı (cihaz listesi/numaratör) |
| 5 | `ChangeNotificationClient` | 1907 | cihaz-değişimi bildirimleri (weak ref: 1917) |

### §6.2 Cihaz tipi sözleşmesi

`WASAPIAudioIODeviceType`, JUCE'nin `AudioIODeviceType` arayüzünü uygular
(doküman: `classjuce_1_1AudioIODeviceType.html`). Bu arayüz üç ana yetenek verir:

| # | Yetenek | Amaç |
|---|---------|------|
| 1 | `getTypeName` | UI'da gösterilecek ad ("Windows Audio …" — §2.2) |
| 2 | `createDevice` (satır 1854) | Verilen giriş/çıkış adlarından cihaz nesnesi üretir |
| 3 | `scanForDevices` | Mevcut cihazları listeler (WASAPI: `IMMDeviceEnumerator`) |

### §6.3 ASIO ile aynı soyutlama seviyesi

| Seviye | ASIO (JUCE) | WASAPI (JUCE) |
|--------|-------------|----------------|
| Cihaz tipi | ASIO audio device type (mevcut K003 kod dosyaları) | `WASAPIAudioIODeviceType` (satır 1805) |
| Cihaz | `ASIOAudioIODevice` | `WASAPIAudioIODevice` (satır 1213) |
| Geri-çağrı | `AudioIODeviceCallback` | `AudioIODeviceCallback` (ortak) |
| Ayar | `AudioDeviceSetup` | `AudioDeviceSetup` (ortak) |

> **Sonuç:** JUCE katmanında ASIO ve WASAPI **aynı** soyutlama seviyesinden
> tüketilir; fark yalnız `AudioIODeviceType` seçimidir. Bu, Neva Engine'in
> OS/ASIO ayrımını (ADR-019 per-OS player FROZEN) soyutlamak için JUCE'yı
> kullanabileceği anlamına gelir — ancak ADR-019 dondurulmuş olduğu için
> **herhangi bir değişiklik `>=088` onayı gerektirir** (kural 11).

### §6.4 `AudioIODeviceType` sözleşme ayrıntıları (doküman kanıtı)

| # | Üye | Sözleşme | ASIO karşılığı |
|---|-----|----------|----------------|
| 1 | `getTypeName` | Kipin kullanıcı-okoğun adı | ASIO device type adı (sürücü adı) |
| 2 | `scanForDevices` | Cihazları tara ve listele | `ASIOGetDriverName` + sürücü numarası |
| 3 | `hasDeviceListChanged` | Taramadan bu yana liste değişmiş mi | ASIO'da karşılığı yok (yeniden `ASIOInit` gerekir) |
| 4 | `createDevice` | Ad çiftinden cihaz nesnesi üret | `ASIOInit` + `ASIOCreateBuffers` sarmalayıcısı |
| 5 | `getDefaultDeviceIndex` | Varsayılan cihaz indeksi | `GetDefaultAudioEndpoint` karşılığı (§7) |

> `hasDeviceListChanged` satırı özellikle önemlidir: WASAPI tarafında cihaz
> listesi **çalışırken değişebilir** (hot-swap, §2.4); ASIO'da aynı durum
> sürücü-özel `kAsioResetRequest` üzerinden gelir ve liste kavramı yoktur.
> Bu fark, `asio-hot-swap-ve-device-loss.md` §9.3'teki iki-sinyal-kaynağı
> eşlemesinin nedeniştir.

### §6.5 Sarmalayıcı-boyutu karşılaştırması

| Ölçü | JUCE ASIO sarmalayıcı | JUCE WASAPI sarmalayıcı |
|------|------------------------|--------------------------|
| Cihaz tipi sınıfı | ASIO device type (mevcut K003 dosyaları) | `WASAPIAudioIODeviceType` (satır 1805) |
| Cihaz sınıfı | ASIO audio device | `WASAPIAudioIODevice` (satır 1213) |
| Zamanlayıcı | ASIO `bufferSwitch` callback | `Thread` + olay nesnesi (WaitForSingleObject) |
| Kip değişimi | Yok (sürücü-özel) | `WASAPIDeviceMode` enum ile üç kip |
| Cihaz-değişimi bildirimi | `kAsioResetRequest` | `ChangeNotificationClient` (satır 1907) |
| Cihaz-durumu bildirimi | Yok (ASIO spec'inde) | `IMMNotificationClient` (K000) |
| Örneklem dönüşümü | Sürücü gereksinimine göre tampon dili | `AudioData::Converter` (satır 1195) |

---

## §7 — Karar matrisi: Neva Engine hangi kapıdan geçmeli?

| # | Kriter | ASIO | WASAPI exclusive | WASAPI shared (low-latency) | Ağırlık |
|---|--------|------|------------------|-----------------------------|---------|
| 1 | En düşük gecikme | ✅ hedef | ✅ ≈ | ⚠️ daha yüksek | Yüksek |
| 2 | 8.1 çok-kanal desteği | ⚠️ sürücüye bağlı | ⚠️ cihaz-özel | ⚠️ karışım | Yüksek |
| 3 | Sistem sesleriyle birlikte çalışmak | ❌ münhasır | ❌ münhasır | ✅ evet | Orta |
| 4 | Cihaz-kaybı bildirimi | ⚠️ sürücü callback | ✅ `IMMNotificationClient` | ✅ aynı | Yüksek |
| 5 | Saat ayarı (örneklem) | ❌ yalnız bildirim | ✅ `IAudioClockAdjustment` | ✅ aynı | Düşük |
| 6 | Volum/oturum kontrolü | ❌ yok | ⚠️ kısıtlı | ✅ tam | Düşük (DSP var) |
| 7 | Çoklu-uygulama paylaşımı | ❌ exclusive lock | ❌ exclusive lock | ✅ evet | Düşük |
| 8 | JUCE olgunluğu | ✅ mevcut yüzey | ✅ mevcut yüzey | ✅ mevcut yüzey | Orta |

### §7.1 Öneri (kanıta dayalı, ADR bekleyen)

1. **Birincil:** ASIO exclusive lock (kural 9) — düşük gecikme ve 8.1 kanal
   hedefi için.
2. **İkincil:** WASAPI exclusive — ASIO sürücüsü olmayan sistemlerde aynı
   "münhasır doğrudan-çııkış" semantiğini korur.
3. **Fallback:** WASAPI shared low-latency (`IAudioClient3`) — sistem sesleriyle
   birlikte çalışma gereken senaryolar için.
4. **Yasak:** PCM5122 (kural 6) — donanım tarafında yalnız PCM3168A; bu
   karşılaştırma yazılım katmanını etkilemez ama DAC-kip`I2S/TDM` tercihini
   sabitler.

> Bu öneri bir **karar değil**, ADR için bir taslaktır. ADR-019 (per-OS player)
> FROZEN olduğu için uygulama seviyesinde davranış değişikliği `>=088` onayı
> gerektirir (kural 11).

---

## §8 — İddia–kanıt tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|-------|-----|-------------------|-------|
| WASAPI, Windows Core Audio API ailesinin parçasıdır; shared ve exclusive mode akışları tanımlar | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ✅ Doğrulandı |
| `IAudioClient3::GetSharedModeEnginePeriod` default/fundamental/min/max olmak üzere dört periyot değeri döndürür | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclient3-getsharedmodeengineperiod | 2026-10-12 | ✅ Doğrulandı |
| `IAudioClockAdjustment` arayüzü örnekleme frekansı değişimini yönetir | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nn-audioclient-iaudioclockadjustment | 2026-10-12 | ✅ Doğrulandı |
| `IAudioClockAdjustment::SetSampleRate` yeni frekansı `double` (Hz) olarak alır | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclockadjustment-setsamplerate | 2026-10-12 | ✅ Doğrulandı |
| WASAPI akışları ses oturumlarına (session) bağlanır ve oturum-bazlı kontrol mümkündür | https://learn.microsoft.com/en-us/windows/win32/coreaudio/audio-sessions | 2026-10-12 | ✅ Doğrulandı |
| Volum kontrolü hem cihaz-üstü hem oturum-üstü katmanlarda tanımlıdır | https://learn.microsoft.com/en-us/windows/win32/coreaudio/volume-controls | 2026-10-12 | ✅ Doğrulandı |
| JUCE `isExclusiveMode`/`isLowLatencyMode` yardımcıları kipi tek satırda test eder | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | ✅ Doğrulandı (satır 399-407) |
| JUCE shared-low-latency kipte `GetSharedModeEnginePeriod` ile dört değeri `minBufferSize` vb. alanlarına yazar | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | ✅ Doğrulandı (satır 663-690) |
| `WASAPIAudioIODevice` sınıfı `AudioIODevice` + `Thread` + `AsyncUpdater`'ı birleştirir | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | ✅ Doğrulandı (satır 1213-1216) |
| `WASAPIAudioIODeviceType`, `AudioIODeviceType` alt sınıfıdır ve `createDevice` üretir | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | ✅ Doğrulandı (satır 1805, 1854) |
| JUCE `AudioIODeviceType` arayüzü cihaz tipi adı / tarama / oluşturma sözleşmesi sunar | https://docs.juce.com/master/classjuce_1_1AudioIODeviceType.html | 2026-10-12 | ✅ Doğrulandı |
| Dört `DEVICE_STATE` sabiti vardır ve yalnız `DEVICE_STATE_ACTIVE` cihazda akış açılabilir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants | 2026-10-12 | ✅ Doğrulandı |
| Exclusive modda 1000 ms olay-zaman-aşımı JUCE render thread'ini sonlandırır | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | ✅ Doğrulandı (satır 1176-1186) |
| JUCE WASAPI dosyasında ses-oturumu volum çağrısı bulunmaz (uygulama sorumluluğu) | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | ✅ Kod taraması (kanıt: eşleşme yok) |
| `SetSampleRate` gerçek motor saatini mi yoksa uygulama görünümünü mü değiştirir | https://learn.microsoft.com/en-us/windows/win32/api/audioclient/nf-audioclient-iaudioclockadjustment-setsamplerate | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| Exclusive < shared-low-latency < shared sıralaması ölçümsel doğrulanmadı | https://learn.microsoft.com/en-us/windows/win32/coreaudio/wasapi | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| WASAPI oturum-volum-semantiğinin JUCE koduyla birebir eşleşmesi | https://raw.githubusercontent.com/juce-framework/JUCE/master/modules/juce_audio_devices/native/juce_WASAPI_windows.cpp | 2026-10-12 | UNKNOWN |

---

## §9 — Açık sorular

| # | Soru | Etki | Durum |
|---|------|------|-------|
| 1 | Neva Engine birincil kapı ASIO mu, WASAPI exclusive mi? | K003 + ADR-019 sınırı | ADR bekliyor |
| 2 | `SetSampleRate` gerçek motor saatini değiştiriyor mu? | Saat-senaryoları (§4) | ⚠️ VERIFICATION REQUIRED → K000 |
| 3 | 8.1 kanal WASAPI exclusive'te cihaz-özel `WAVEFORMATEXTENSIBLE` maskesi nasıl doğrulanır? | Cihaz-uyumu | K000 |
| 4 | `IAudioClient3` paylaşımlı düşük-gecikme motoru tüm hedef Windows sürümlerinde mevcut mu (min Windows 10 1803)? | Destek matrisi | ⚠️ VERIFICATION REQUIRED |
| 5 | ASIO exclusive-lock ile WASAPI exclusive aynı donanımda çakışır mı (tek-open)? | Kural 9 | K003 → test PLANNED |
| 6 | Oturum-volumü uygulama-seviyesinde uygulanacaksa 8.1 kanal gain kurgusu nereye konur? | DSP mimarisi | K004-dsp |

---

## §10 — İlgili vault düğümleri

| Düğüm | İlişki |
|-------|--------|
| `K003-audio/backend/asio/asio-latency-ve-jitter.md` | Gecikme ölçüm protokolü §9 (bu dosyanın §3.3'ü buna dayanır) |
| `K003-audio/backend/asio/asio-buffer-stratejileri.md` | Tampon boyutu kısıtları (`ASE_InvalidMode`) |
| `K003-audio/backend/asio/asio-hot-swap-ve-device-loss.md` | Cihaz-kaybı: ASIO callback ↔ `IMMNotificationClient` karşılaştırması |
| `K003-audio/backend/asio/asio-cpp-ornekleri.md` | JUCE ASIO sarmalayıcısı ile WASAPI sarmalayıcısının aynı soyutlama katmanı (§6.3) |
| `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` | WASAPI'in tam derinlemesine incelemesi (depends_on) |
| `K004-dsp` | Kanal-bazlı gain / 8.1 yönlendirme (§5.2) |
| `.ai/.decisions/index.md` | ADR-019 FROZEN sınırı (kural 11) |

---

## §11 — Doğrulama protokolü

| # | Adım | Yöntem | Beklenen | Durum |
|---|------|--------|----------|-------|
| 1 | URL erişilebilirliği | HTTP GET, 2026-10-12 | 9/9 → 200 | ✅ tamamlandı |
| 2 | Mevcut dosya-çakışması | 52 URL'lik yasak-listesiyle karşılaştırma | 0 kesişim | ✅ tamamlandı |
| 3 | Kod alıntıları | Ham dosyanın indirilip satır-no ile eşleşmesi | Satır 399-407 / 663-690 / 1176-1186 / 1213 / 1805 / 1854 birebir | ✅ tamamlandı |
| 4 | Frontmatter | 13 alan | eksik yok | ✅ tamamlandı |
| 5 | Metrik-ölçüm (gecikme sıralaması) | Donanım testi | exclusive < shared-low-latency < shared | 🔴 PLANNED |
| 6 | Cihaz-kaybı + WASAPI senkron testi | K003 hot-swap senaryosu | bildirim + yeniden-bağlanma | 🔴 PLANNED |

---

*Bayram Ali / Vault Steward · Red Team · Human Mode · Truth Mode · Kaynak: Microsoft
Windows Hardware Dev Center (Core Audio), JUCE framework (raw.githubusercontent), JUCE
docs · 2026-10-12*
