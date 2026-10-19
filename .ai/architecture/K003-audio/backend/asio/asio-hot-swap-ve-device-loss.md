---
title: "ASIO Derin-Dalı — Hot-Swap ve Cihaz Kaybı (Reset · Bildirim · Kurtarma)"
node_id: "K003-audio/backend/asio/asio-hot-swap-ve-device-loss"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K000-isletim-sistemi/isletim-sistemi-audio/wasapi"
evidence:
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-events — cihaz olayı türleri, kayıt/iptal akışı, örnek IMMNotificationClient uygulaması (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immnotificationclient — arayüzün istemci tarafından uygulandığı, uç nokta kimlik dizesinin opak olduğu (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immnotificationclient-ondevicestatechanged — dwNewState dört DEVICE_STATE_XXX değerinden biridir (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immnotificationclient-ondeviceremoved — uç noktanın kaldırıldığı bildirimi (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-registerendpointnotificationcallback — kayıt/Unregister yaşam döngüsü ve AddRef/Release sorumluluğu (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-getdefaultaudioendpoint — dataFlow/role parametreleri, E_NOTFOUND (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants — dört durum sabiti ve yalnız ACTIVE'te akış açılabilir kuralı (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis — kullanıcı kipi ses yığını, sürücü-üstü soyutlama, roller (2026-10-12 okundu)"
  - "https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h — kAsioResetRequest / kAsioResyncRequest / sampleRateDidChange / ASE_HWMalfunction (2026-10-12 okundu)"
validation:
  - "URL + tarih + alıntı: §11 kanıt-iddia tablosundaki her satır bu turda açılıp okunmuş bir URL'ye bağlandı (2026-10-12)"
  - "Kod blokları kaynak URL + tarih etiketi taşır; uydurma `Dosya:`/`Satır:` referansı yoktur (repo'da *.cpp/*.h bulunmaz)"
  - "Kurtarma zamanı ve hot-swap başarı oranı için ölçüm iddiası yapılmadı → UNKNOWN (2026-10-12)"
  - "Satır sayısı ≥500, ön-matter alanları ve URL çakışması otomatik ölçüldü (2026-10-12)"
open_questions:
  - "Neva Engine hot-swap tepki süresi hedefi kaç ms? (ölçüm yok → ⚠️ VERIFICATION REQUIRED)"
  - "ASIO sürücüsü kaybedilince otomatik WASAPI fallback mi, kullanıcı onayı mı? → `.ai/.decisions` ADR"
  - "`kAsioResetRequest` kabul edilmezse ikinci deneme var mı? Kaynak: kabulü o an belirlenemez → ADR"
  - "Bildirim iş parçacığı hangi öncelikte çalışacak; ses iş parçacığına dokunmadan durdurma nasıl sinyalize edilecek? → K003-audio/backend"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Hot-Swap ve Cihaz Kaybı (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya **cihaz kaybı anını** ve sonrasını tarif eder: ASIO tarafında
> `kAsioResetRequest` / `kAsioResyncRequest` / `sampleRateDidChange` /
> `ASE_HWMalfunction`, Windows tarafında `IMMNotificationClient` bildirimleri ve
> `DEVICE_STATE_XXX` durumları. Kurtarma stratejisi bir **tasarımdır**; tepki
> süresi ve başarı oranı ölçülmemiştir → `UNKNOWN`.

---

## §0 ELI15

Sürücü açıkken USB kablosunu çektiğinizi düşünün: ses bir anda kesilir ve
**hiç kimse size haber vermez** — ASIO'nun callback'i sadece çalışmaz. ASIO bu
durum için **iki kapı** bırakmıştır: sürücü "sıfırla" der (`kAsioResetRequest`),
ya da saat kaybını bildirir (`sampleRateDidChange`, `0` gelir). Windows tarafında
ise **kapı birden fazladır**: `IMMNotificationClient` arayüzünü
kaydettiyseniz sistem size "cihaz durumu değişti", "cihaz kaldırıldı",
"varsayılan cihaz değişti" der.

Bu dosyanın işi, bu iki kapıyı **tek bir kurtarma durum makinesi**nde
birleştirmektir. ASIO yalnız "istek" yapar (kabulü o an belirlenemez); Windows
ise "olay" verir. İkisini de beklemek gerekir — biri olmadan diğeri eksiktir.

---

## §1 Kimlik kartı

| Alan | Değer |
|---|---|
| Soru | Cihaz kaybolduğunda ne olur, kim haber verir, nasıl kurtarılır? |
| Sorumlu katman | Host (ASIO C API) + sürücü (reset isteği) + MMDevice API (olaylar) |
| Sözleşme yeri | `asio.h` (`ASIOCallbacks`, `asioMessage` seçicileri) · `mmdeviceapi.h` (`IMMNotificationClient`) |
| WASAPI karşılığı | `AUDCLNT_E_DEVICE_INVALIDATED` hata yolu · `IMMDeviceEnumerator::RegisterEndpointNotificationCallback` |
| Kesiştiği düğümler | `asio-surucu-modeli.md` (yükleme/boşaltma) · `asio-wasapi-karsilastirma.md` (fallback) · `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` |
| Kapsam dışı | Fiziksel röle/koruma devresi (donanım) · sürücü-üstü kayıt (`elog`/WDK) |
| Ölçüm birimi | Olay (adet) · durum (enum) · süre → ms (ölçülmedi) |

---

## §2 Cihaz olayı nedir?

Microsoft tanımı: **bir cihaz olayı, uç nokta cihazının durumundaki değişikliği
istemcilere bildirir.** Kaynak olay türlerini örneklerle verir:

| # | Örnek olay | Kaynak kategorisi |
|---|---|---|
| 1 | Kullanıcı cihazı Device Manager veya `Mmsys.cpl` üzerinden etkinleştirir/devre dışı bırakır | Durum değişikliği |
| 2 | Kullanıcı ses adaptörü takar veya çıkarır | Varlık değişikliği |
| 3 | Jack-varlığı algılama ile jak takılır/çıkarılır | Durum değişikliği |
| 4 | Kullanıcı cihaza atanan **rolü** değiştirir | Rol değişikliği |
| 5 | Cihazın bir özelliğinin değeri değişir | Özellik değişikliği |

| Kural | Kaynaktaki ifade |
|---|---|
| Adaptör ekleme/çıkarma | Adaptöre bağlı **tüm** uç noktalar için olay üretir |
| İlk dört madde | "cihaz durumu değişikliği" örnekleridir |
| Kullanıcı etkileşimi | Bildirim, uygulamanın cihazı **otomatik** değiştirmesine olanak tanır — kullanıcının yönlendirmesi gerekmez |
| Kayıt zorunlu değil | Kayıt yapılmazsa uygulama olayları **görmez** |

Kaynağın verdiği senaryo (USB hoparlör örneği):

| An | Olay | Uygulama tepkisi |
|---|---|---|
| Kullanıcı USB hoparlörü çeker | Cihaz olayı bildirimi alınır | Uygulama masaüstü hoparlörlerinin bağlı olup olmadığını denetler |
| Masaüstü hoparlörler varsa | — | Parça çalma **kesintisiz** devam eder |
| Kullanıcı müdahalesi | **Gerekmez** | Geçiş otomatiktir |

---

## §3 ASIO tarafı: dört bildirim kanalı

ASIO'da cihaz kaybı **tek bir olayla** temsil edilmez; farklı belirtiler için
farklı kanallar vardır.

### §3.1 `kAsioResetRequest` — "beni sıfırla"

| Alan | Kaynaktaki semantik |
|---|---|
| Ne yapar | Sürücü sıfırlama **ister** |
| Kabul edilirse | Sürücü kapanır (`ASIOExit()`) ve **yeniden açılır** (`ASIOInit()` vb.) |
| Neden tetiklenir | Örnek hız değişimi, `ASIOControlPanel()`'de temel ayar değişikliği — bazı sürücülerin yeniden yapılandırması gerekir |
| Dönüş | `1L` |
| **Kritik sınır** | İstek yalnızca **uygulamaya iletilir**; o anda kabul edilip edilmediğini belirlemenin **bir yolu yoktur** (kaynak: "note the request is merely passed to the application, there is no way to determine if it gets accepted at this time") |
| Pratik | "Genellikle kabul edilir" |

### §3.2 `kAsioResyncRequest` — "senkronu kaybettim"

| Alan | Kaynaktaki semantik |
|---|---|
| Ne olur | Sürücü senkronundan çıkmıştır; **zaman damgası artık geçerli değildir** |
| İstenen | Motorun ve slave cihazların (sequencer) **yeniden başlatılması** |
| Dönüş | 1 = tamam, 0 = desteklenmiyor |
| İlişki | Zaman damgası geçerliliğini doğrudan ilgilendirir → `asio-multichannel-ve-timestamp.md` §5.2 `kClockSourceChanged` |

### §3.3 `sampleRateDidChange` — "saat gitti"

| Alan | Kaynaktaki semantik |
|---|---|
| Ne zaman çağrılır | AudioStreamIO bir örnek hızı değişikliği algıladığında |
| **Önemli uç durum** | Örnek hız **bilinmiyorsa 0 gönderilir** — örn. harici senkronizasyonda **saat kaybı** |
| Kullanım | `0` değeri "saat yok" sinyalidir, gerçek bir oran değil |

### §3.4 `kAsioBufferSizeChange` — kullanılamaz

Kaynak bunu **henüz desteklenmiyor** olarak işaretler: her zaman `0L` döner ve
**"şimdilik `kAsioResetRequest` kullanın"** der. Yani canlı boyut değişimi
isteğinin tek yolu reset akışıdır.

### §3.5 Hata kodları — cihaz kaybının imzaları

| Kod | Anlamı | Kaybı işaret eder mi? |
|---|---|---|
| `ASE_NotPresent` | Donanım girişi/çıkışı mevcut değil veya kullanılamıyor | **Evet** — en tipik imza |
| `ASE_HWMalfunction` | Donanım arızalı (herhangi bir ASIO işlevi döndürebilir) | **Evet** |
| `ASE_NoClock` | Örnek saatı veya oranı belirlenemiyor ya da mevcut değil | **Evet** (saat kaybı) |
| `ASE_SPNotAdvancing` | Örnek konumu sorgulanırken donanım çalışmıyor | **Evet** (duraklama) |
| `ASE_InvalidMode` | Donanım kötü modda veya kötü modda kullanılıyor | Kısmen |
| `ASE_InvalidParameter` | Geçersiz girdi parametresi | Hayır |
| `ASE_NoMemory` | İstek tamamlanacak bellek yok | Hayır |
| `ASE_OK` | Başarı | Hayır |

> `ASE_HWMalfunction` yorumu açıktır: **herhangi bir** ASIO işlevi bunu
> döndürebilir — yani tek bir kontrol noktası yeterli değildir.

---

## §4 Windows tarafı: `IMMNotificationClient`

### §4.1 Kim kimi uygular?

| taraf | Rol |
|---|---|
| MMDevice API sistem bileşeni | `IMMNotificationClient` metodlarını **çağırır** |
| **İstemci (uygulama)** | `IMMNotificationClient` arayüzünü **uygular** |

Kayıt yolu:

| Adım | Çağrı |
|---|---|
| Kayıt | `IMMDeviceEnumerator::RegisterEndpointNotificationCallback(pClient)` |
| İptal | `IMMDeviceEnumerator::UnregisterEndpointNotificationCallback(pClient)` |

### §4.2 Referans sayımı kuralı (kritik)

Kaynak bu uyarıyı **açıkça** yapar:

| Kural | Neden |
|---|---|
| `RegisterEndpointNotificationCallback` çağrısı ile `UnregisterEndpointNotificationCallback` arasında nesne **serbest bırakılamaz** | Sistem işaretçiye erişmeye devam eder |
| Bu metodlar istemcinin `AddRef`/`Release`'ini **çağırmaz** | Referans sayımı **istemcinin** sorumluluğundadır |
| Kayıt başarılıysa sayaç artırılmalı; son referans yalnız `Unregister`'dan **sonra** bırakılmalı | Yoksa serbest bırakma sonrası kullanımda çökme |

### §4.3 Uç nokta kimlik dizesi — opaktır

| Kural | Kaynaktaki gerekçe |
|---|---|
| Her metod bir uç nokta kimlik dizesi alır | Olayın hangi cihazla ilgili olduğunu belirtir |
| Dize **opak** muamele görmelidir; **hiçbir metod ayrıştırmamalıdır** | Biçimi tanımsızdır ve MMDevice API sürümüne göre **değişebilir** |
| İki geçerli kullanım | (a) `IMMDeviceEnumerator::GetDevice` ile cihaz nesnesi oluşturmak · (b) `IMMDevice::GetId` ile mevcut cihazın dizesiyle **karşılaştırmak** — eşitse aynı cihazdır |

### §4.4 Yönlendirme kuralı

Kaynak, kayıtlı bir istemcinin **sistemdeki tüm uç noktalardaki tüm olay
türlerini** aldığını belirtir. Belirli olay veya cihaz isteniyorsa
`IMMNotificationClient` **içinde filtre yapılmalıdır** — kayıt katmanında
filtre yoktur.

### §4.5 `OnDeviceStateChanged`

| Parametre | Tip | Anlamı |
|---|---|---|
| `pwstrDeviceId` | `LPCWSTR` | Olayın ilişkin olduğu uç nokta kimlik dizesi; **çağrı süresince geçerlidir** |
| `dwNewState` | `DWORD` | Aşağıdaki dört değerden biri |
| Dönüş | `HRESULT` | Başarıda `S_OK` |

### §4.6 `OnDeviceRemoved`

| Parametre | Tip | Anlamı |
|---|---|---|
| `pwstrDeviceId` | `LPCWSTR` | Kaldırılan uç noktayı tanımlayan dize; çağrı süresince geçerli |
| Yorum | — | Uç noktanın **kaldırıldığı** bildirilir |
| Dönüş | `HRESULT` | Başarıda `S_OK` |

> `OnDeviceStateChanged` durum **geçişini**, `OnDeviceRemoved` ise varlık
> **kaldırılmasını** bildirir. İkisini aynı anda beklemek gerekir — kaynak
> ikisini ayrı metodlar olarak tanımlar.

---

## §5 `DEVICE_STATE_XXX` — dört durum

| Sabit | Değer | Anlamı |
|---|---|---|
| `DEVICE_STATE_ACTIVE` | `0x00000001` | Cihaz **etkin**: bağladığı adaptör mevcut ve etkin; jak'a takılıysa takılı |
| `DEVICE_STATE_DISABLED` | `0x00000002` | Kullanıcı `Mmsys.cpl` üzerinden cihazı devre dışı bırakmış |
| `DEVICE_STATE_NOTPRESENT` | `0x00000004` | Adaptör sistemden **çıkarılmış** veya Device Manager'da devre dışı bırakılmış |
| `DEVICE_STATE_UNPLUGGED` | `0x00000008` | Adaptör mevcut ve etkin ama cihaz **jak'a takılı değil** — yalnız **jack-varlığı algılama** özelliği olan cihaz bu durumda olabilir |
| `DEVICE_STATEMASK_ALL` | `0x0000000F` | Dört durumun tamamı |

| Kural | Kaynaktaki ifade |
|---|---|
| **Yalnız `DEVICE_STATE_ACTIVE` üzerinde akış açılabilir** | `IAudioClient` elde etmek dâhil |
| `Mmsys.cpl`'de devre dışı bırakma | Cihazı yüksek seviyeli API keşif mekanizmalarından **gizler**, ancak müşterinin o anda **var olan** akış nesnelerini **geçersiz kılmaz** |
| Kullanan metodlar | `IMMDeviceEnumerator::EnumAudioEndpoints` · `IMMDevice::GetState` · `IMMNotificationClient::OnDeviceStateChanged` |

---

## §6 Bildirim örneği (kaynak kod — Microsoft docs)

Kaynak `device-events` sayfasında tam bir `IMMNotificationClient` uygulaması
örneği verir. Örnek yapı şu sırayı izler:

```cpp
// Kaynak: learn.microsoft.com/en-us/windows/win32/coreaudio/device-events (2026-10-12)
// Example implementation of IMMNotificationClient interface.
// When the status of audio endpoint devices change, the
// MMDevice module calls these methods to notify the client.
class CMMNotificationClient : public IMMNotificationClient {
    LONG _cRef;
    IMMDeviceEnumerator *_pEnumerator;
    HRESULT _PrintDeviceName(LPCWSTR pwstrId);
public:
    CMMNotificationClient() : _cRef(1), _pEnumerator(NULL) { }
    // IUnknown methods -- AddRef, Release, and QueryInterface
    ULONG STDMETHODCALLTYPE AddRef()  { return InterlockedIncrement(&_cRef); }
    ULONG STDMETHODCALLTYPE Release() {
        ULONG ulRef = InterlockedDecrement(&_cRef);
        if (0 == ulRef) { delete this; }
        return ulRef;
    }
    HRESULT STDMETHODCALLTYPE QueryInterface(REFIID riid, VOID **ppvInterface);
    // Callback methods for device-event notifications.
    HRESULT STDMETHODCALLTYPE OnDefaultDeviceChanged(
        EDataFlow flow, ERole role, LPCWSTR pwstrDeviceId);
    HRESULT STDMETHODCALLTYPE OnDeviceAdded(LPCWSTR pwstrDeviceId);
    HRESULT STDMETHODCALLTYPE OnDeviceRemoved(LPCWSTR pwstrDeviceId);
    HRESULT STDMETHODCALLTYPE OnDeviceStateChanged(
        LPCWSTR pwstrDeviceId, DWORD dwNewState);
};
```

| Gözlem | Sonuç |
|---|---|
| Referans sayımı `InterlockedIncrement`/`InterlockedDecrement` ile yapılır | İstemci tarafı atomik tutulur |
| `Release` sıfıra düşünce `delete this` | Nesne yaşam döngüsü istemcinin kontrolünde |
| `_PrintDeviceName` ayrı bir yardımcı | Kimlik dizesi **ayrıştırılmaz**, yalnız gösterilir (§4.3 ile uyumlu) |

> Örnekteki `OnDefaultDeviceChanged` üç parametreyi birlikte
> alır: **akış yönü + rol + cihaz dizesi** — §7 varsayılan değişikliği ile
> doğrudan ilgilidir.

---

## §7 Varsayılan cihaz değişikliği: `GetDefaultAudioEndpoint`

| Parametre | Tip | Kabul edilen değerler |
|---|---|---|
| `dataFlow` | `EDataFlow` | `eRender` (çıkış) · `eCapture` (giriş) |
| `role` | `ERole` | `eConsole` · `eMultimedia` · `eCommunications` |
| `ppEndpoint` | `IMMDevice**` | Adresi yazılır; **çağıran `Release` ile bırakmalıdır** |

| Dönüş kodu | Anlamı |
|---|---|
| `S_OK` | Başarılı |
| `E_POINTER` | `ppDevice` NULL |
| `E_INVALIDARG` | `dataFlow` veya `role` aralık dışı |
| **`E_NOTFOUND`** | **Kullanılabilir cihaz yok** — kaybın en somut imzası |
| `E_OUTOFMEMORY` | Bellek yetersiz |

| Dikkat | Kaynaktaki not |
|---|---|
| Hata durumunda | `*ppDevice = NULL` yazılır |
| Windows Vista sınırı | MMDevice API cihaz rollerini destekler, ama Vista'nın sistem arayüzü programları **desteklemez** |

**Kurtarmada anlamı:** cihaz düştüğünde yeniden `GetDefaultAudioEndpoint`
çağırmak gerekir; `E_NOTFOUND` alınırsa **hiçbir cihaz yoktur** ve beklemekten
başka çare yoktur.

---

## §8 Core Audio'ya genel bakış — neden kullanıcı kipi?

| Özellik (kaynaktaki madde) | Hot-swap ile ilişkisi |
|---|---|
| Düşük gecikmeli, glitch'e dayanıklı akış | Kesinti anında tolerans |
| **Güvenilirlik artışı** — birçok ses işlevi çekirdek kipinden **kullanıcı kipine** taşındı | Bozuk sürücü tüm sistemi değil, süreci etkiler |
| Güvenlik — korumalı ses içeriği **daha düşük ayrıcalıklı** süreçte işlenir | Kurtarma kodu ayrıcalık gerektirmez |
| **Sistem geneli roller** (console, multimedia, communications) ataması | Varsayılan değişikliği olayı (§7) |
| Uç nokta cihazlarının **yazılım soyutlaması** | Cihaz değişse de uygulama aynı arayüzle devam eder |

Kaynak ayrıca şunu belirtir: bu API'ler **DirectSound**, **DirectMusic**,
`waveXxx`/`mixerXxx` ve **Media Foundation** gibi üst seviye API'lerin temelidir
ve çoğu uygulama doğrudan Core Audio'ya **gitmez**. ASIO bu yığının
**dışında** ayrı bir sürücü yoludur — bu yüzden cihaz olaylarını **paylaşmaz**.

---

## §9 Kurtarma durum makinesi (tasarım — uygulanmadı)

> Bu tablo bir **tasarımdır**; hiçbir adımı bu depoda çalıştırılmamıştır.

| Durum | Tetikleyici | Geçiş | Çıkış |
|---|---|---|---|
| `RUNNING` | `ASIOStart` başarılı | — | Ses iş parçacığı aktif |
| `SUSPECT` | `sampleRateDidChange(0)` **veya** `ASE_NoClock`/`ASE_SPNotAdvancing` | → `STOPPING` | Sessiz çıkış (klik yok) |
| `SUSPECT` | `kAsioResetRequest` | → `RESETTING` | Sürücü `ASIOExit` → `ASIOInit` |
| `SUSPECT` | `kAsioResyncRequest` | → `RESETTING` | Zaman damgası geçersiz sayılır |
| `STOPPING` | Ses iş parçacığı durdu | → `PROBING` | Tamponlar boşaltılır |
| `PROBING` | `IMMNotificationClient` olayı geldi | → `ENUMERATING` | Yeni cihaz aranır |
| `ENUMERATING` | `GetDefaultAudioEndpoint` → `S_OK` | → `REOPENING` | `IMMDevice` elde edildi |
| `ENUMERATING` | `E_NOTFOUND` | → `WAITING` | Cihaz yok; tekrar deneme |
| `REOPENING` | `ASIOInit` → `ASE_OK` | → `RUNNING` | Yeniden bağlandı |
| `REOPENING` | `ASE_NotPresent` / `ASE_HWMalfunction` | → `WAITING` | ASIO kullanılamaz → fallback |
| `WAITING` | Zamanlayıcı veya yeni olay | → `PROBING` | Tekrar dener |

### §9.1 Tasarım kuralları

| # | Kural | Dayanak |
|---|---|---|
| 1 | Ses iş parçacığı **asla** bloke edilmez; durdurma sinyali atomik bayrakla verilir | Kural 1–2 (sıfır tahsis / kilit yok) |
| 2 | Bildirim iş parçacığı **kilit kullanabilir** (ses iş parçacığı değildir) | §4.2 referans sayımı zaten istemci sorumluluğunda |
| 3 | `kAsioResetRequest` kabulü bilinemez → **zaman aşımı ile doğrulanır** | §3.1 kaynak uyarısı |
| 4 | Kimlik dizesi **ayrıştırılmaz**, yalnız karşılaştırılır | §4.3 |
| 5 | Yalnız `DEVICE_STATE_ACTIVE` cihazda akış açılır | §5 |
| 6 | `E_NOTFOUND` durumunda agresif tekrar deneme yapılmaz | §7 |
| 7 | ASIO fallback'i WASAPI'e geçerse bildirim kaydı **taşınır** | §4.1 |

### §9.2 Ölçüm alanları (boş)

| Alan | Değer |
|---|---|
| Olay → durdurma süresi | UNKNOWN |
| Olay → yeniden bağlanma süresi | UNKNOWN |
| Hot-swap başarı oranı | UNKNOWN |
| `E_NOTFOUND` bekleme süresi | UNKNOWN |
| Fallback sonrası gecikme farkı | UNKNOWN |

> Hiçbir hücre doldurulmamıştır; ölçüm altyapısı PLANNED'dir.

### §9.3 İki sinyal kaynağı arasında eşleme (tasarım)

Cihaz kaybı iki farklı yerden fark edilebilir: **sürücü** (ASIO callback/hata
kodu) ve **işletim sistemi** (MMDevice bildirimi). İkisi aynı fiziksel olayın
iki farklı yansımasıdır; tasarımda hangisinin öncelikli olduğu açıkça tanımlanır.

| # | Sinyal kaynağı | Tetikleyici | Öncelik | Eylem |
|---|----------------|-------------|---------|-------|
| 1 | ASIO callback (`sampleRateDidChange(0)`) | Sürücü saati kaybetti | Yüksek | `SUSPECT` → sessiz durdurma |
| 2 | ASIO hata kodu (`ASE_NoClock` / `ASE_SPNotAdvancing`) | Çağrı sırasında fark edildi | Yüksek | `SUSPECT` → durdurma |
| 3 | `kAsioResetRequest` | Sürücü sıfırlama istiyor | Orta | `RESETTING` zinciri |
| 4 | `kAsioResyncRequest` | Senkron kaybı | Orta | Zaman damgası geçersiz + yeniden başlatma |
| 5 | `OnDeviceStateChanged` → `DEVICE_STATE_UNPLUGGED`/`NOTPRESENT` | Sistem cihazı işaretledi | Orta | `PROBING` zinciri |
| 6 | `OnDeviceRemoved` | Cihaz fiziksel olarak kaldırıldı | Yüksek | `ENUMERATING` zinciri |
| 7 | `OnDefaultDeviceChanged` | Varsayılan cihaz değişti | Düşük | yalnız kullanıcı-tercih güncellemesi |
| 8 | Render thread `1000 ms` olay-zaman-aşımı | WASAPI fallback yolu (bkz. `asio-wasapi-karsilastirma.md` §2.3) | Yüksek | akışı kapat + `PROBING` |

**Öncelik kuralı (tasarım):** iki kaynak çakışırsa **erken gelen** işlenir,
ikinci sinyal aynı geçişte yok sayılır (idempotent geçiş). Böylece aynı olay
için iki kez `ASIOExit` çağrılmaz.

### §9.4 Kurtarma sırasındaki kaynak sorumlulukları (tasarım)

| # | Kaynak | Kimin sorumluluğu | Not |
|---|--------|------------------|-----|
| 1 | ASIO tamponları | Ses iş parçacığı **öncesi** sahipsiz kip | `ASIODisposeBuffers` → `ASIOStop` önkoşulu (asio.h) |
| 2 | `IMMNotificationClient` kaydı | Bildirim iş parçacığı | `Unregister` yaşam döngüsü §4.2 |
| 3 | `IMMDevice` referansı | ENUMERATING/REOPENING geçişi | Başarıda `Release` istemcinin işidir (§7) |
| 4 | Yedek (fallback) cihaz seçimi | Uygulama-seviyesi politika (ADR) | Otomatik/onay bekliyor (§11.2) |
| 5 | Telemetri sayaçları | K003-audio/backend | Her geçişte sayaç + zaman damgası |

### §9.5 Durum makinesi ile bağlantı noktaları

| Durum makinesi durumu | Karşılık gelen bu dosyanın bölümü | Karşılık gelen karşılaştırma dosyası |
|---|---|---|
| `SUSPECT` | §3.1–§3.3 (ASIO bildirimleri) | §4 (IAudioClock tarafı) |
| `PROBING` / `ENUMERATING` | §4.5–§4.6 + §5 (durumlar) | §2 (kip semantiği) |
| `REOPENING` | §7 (`GetDefaultAudioEndpoint`) | §6.3 (aynı soyutlama katmanı) |
| `WAITING` (fallback) | §11.2 (ADR sorusu) | §7 karar matrisi |

### §9.6 Test senaryoları (planlandı — çalıştırılmadı)

| # | Senaryo | Uygulama | Beklenen gözlem |
|---|---------|----------|-----------------|
| T1 | Çalışan akış sırasında USB ses cihazının fişinin çekilmesi | Fiziksel çıkarım | `OnDeviceRemoved` → `ENUMERATING` |
| T2 | Cihazın devre dışı bırakılması (`Mmsys.cpl`) | Sistem ayarı | `OnDeviceStateChanged(DISABLED)`; akış nesnesi geçersiz kalmayabilir (§5) |
| T3 | Varsayılan cihazın başka cihaza taşınması | Sistem ayarı | `OnDefaultDeviceChanged`; mevcut akış etkilenmez |
| T4 | ASIO sürücüsü içinde `sampleRateDidChange(0)` enjeksiyonu | Test kancası (uygulanmadı) | `SUSPECT` → sessiz durdurma |
| T5 | Cihazı yeniden takma | Fiziksel takma | `DEVICE_STATE_ACTIVE` → `REOPENING` → `RUNNING` |

> Beş senaryonun tamamı **çalıştırılmamıştır**; sonuç sütunu yalnız tasarımdaki
> beklenen davranışı yansıtır → ölçüm `UNKNOWN` (§9.2).

---

## §10 İddia — kanıt tablosu

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Cihaz olayı, uç nokta durumundaki değişikliği istemcilere bildirir; örnekler durum/rol/özellik değişikliği ve adaptör tak-çıkar'dır | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-events | 2026-10-12 | OK |
| Kayıt `RegisterEndpointNotificationCallback`, iptal `UnregisterEndpointNotificationCallback` ile yapılır; `pClient` bir `IMMNotificationClient*`'dir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-events | 2026-10-12 | OK |
| Kayıtlı istemci sistemdeki **tüm** uç noktaların **tüm** olay türlerini alır; filtre istemci içinde yapılmalıdır | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-events | 2026-10-12 | OK |
| Adaptör ekleme/çıkarma, o adaptöre bağlı tüm uç noktalar için olay üretir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-events | 2026-10-12 | OK |
| Sayfada tam bir `CMMNotificationClient` (`IMMNotificationClient`) uygulama örneği verilmiştir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-events | 2026-10-12 | OK |
| `IMMNotificationClient` **istemci tarafından** uygulanır; MMDevice API sistem bileşeni metodları çağırır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immnotificationclient | 2026-10-12 | OK |
| Uç nokta kimlik dizesi **opaktır**, biçimi tanımsızdır ve ayrıştırılmamalıdır; iki geçerli kullanım `GetDevice` ile nesne oluşturmak ve `GetId` ile karşılaştırmaktır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immnotificationclient | 2026-10-12 | OK |
| `OnDeviceStateChanged` yeni durumu `DEVICE_STATE_XXX` değerlerinden biriyle verir; kimlik dizesi çağrı süresince geçerlidir | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immnotificationclient-ondevicestatechanged | 2026-10-12 | OK |
| `OnDeviceRemoved` bir uç noktanın kaldırıldığını bildirir | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immnotificationclient-ondeviceremoved | 2026-10-12 | OK |
| `RegisterEndpointNotificationCallback` sistemin rolleri/varlığı/durumu/özellikleri değişiminde çağırmasını sağlar; `AddRef`/`Release` çağrılmaz ve referans sayımı istemcinin sorumluluğundadır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-registerendpointnotificationcallback | 2026-10-12 | OK |
| `GetDefaultAudioEndpoint(dataFlow, role, ppEndpoint)` alır; `E_NOTFOUND` "kullanılabilir cihaz yok" demektir ve başarıda `Release` istemcinin işidir | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-getdefaultaudioendpoint | 2026-10-12 | OK |
| `DEVICE_STATE_ACTIVE`/`DISABLED`/`NOTPRESENT`/`UNPLUGGED` sabitleri `0x1`/`0x2`/`0x4`/`0x8` değerlerindedir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants | 2026-10-12 | OK |
| İstemci yalnız `DEVICE_STATE_ACTIVE` durumundaki cihazda akış açabilir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants | 2026-10-12 | OK |
| `Mmsys.cpl`'de devre dışı bırakma cihazı keşif mekanizmalarından gizler ama mevcut akış nesnelerini geçersiz kılmaz | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants | 2026-10-12 | OK |
| `UNPLUGGED` durumu yalnız jack-varlığı algılama özelliğine sahip cihazda olabilir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants | 2026-10-12 | OK |
| Core Audio API'leri Vista'da tanıtıldı; birçok ses işlevi çekirdek kipinden kullanıcı kipine taşındı ve sistem rolleri (console/multimedia/communications) ataması içerir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 | OK |
| Core Audio, DirectSound/DirectMusic/`waveXxx`/`mixerXxx`/Media Foundation için temeldir ve çoğu uygulama doğrudan kullanmaz | https://learn.microsoft.com/en-us/windows/win32/coreaudio/about-the-windows-core-audio-apis | 2026-10-12 | OK |
| `kAsioResetRequest` kabul edilirse sürücü `ASIOExit()` ile kapanıp yeniden açılır; istek yalnız uygulamaya iletilir ve o an kabul edilip edilmediği **belirlenemez** | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `kAsioResyncRequest` sürücünün senkronunu kaybettiğini, zaman damgasının artık geçerli olmadığını ve motorun yeniden başlatılmasını istediğini bildirir | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `sampleRateDidChange` örnek hız bilinmiyorsa `0` gönderir (örn. harici senkronizasyonda saat kaybı) | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `kAsioBufferSizeChange` henüz desteklenmez, her zaman `0L` döner ve yerine `kAsioResetRequest` kullanılır | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| `ASE_HWMalfunction` herhangi bir ASIO işlevinden dönebilir | https://github.com/introlab/irl_audio/blob/master/rt_audio_ros/src/RTAudio/asio.h | 2026-10-12 | OK |
| Hot-swap tepki süresi / kurtarma başarısı | (ölçüm yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| Neva Engine fallback kararı (otomatik mi, onaylı mı) | (ADR yok) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |

---

## §11 Açık sorular

1. **Tepki süresi hedefi:** Olaydan durdurmaya kaç ms? Ölçülmedi → üst karar +
   ölçüm.
2. **Fallback politikası:** ASIO kaybında otomatik WASAPI'e mi geçilecek, yoksa
   kullanıcıya mı sorulacak? → `.ai/.decisions` ADR.
3. **`kAsioResetRequest` doğrulaması:** Kabul bilinemediği için hangi zaman
   aşımı sonrası "kabul edilmedi" sayılacak? → ADR.
4. **Bildirim iş parçacığı önceliği:** Ses iş parçıcığından bağımsız ama ne
   öncelikte? → `K003-audio/backend`.
5. **Olay filtresi:** Yalnız ilgili cihaz için mi dinlenecek (§4.4 — filtre
   istemci içinde)? Hangi karşılaştırma (`GetId` eşleşmesi) kullanılacak? → ADR.

---

## §12 İlgili vault düğümleri

| Düğüm | İlişki |
|---|---|
| `K003-audio/backend/asio/asio-surucu-modeli.md` | Sürücü yükleme/boşaltma bu durum makinesinin taşıyıcısı |
| `K003-audio/backend/asio/asio-wasapi-karsilastirma.md` | Fallback hedefi ve `AUDCLNT_E_DEVICE_INVALIDATED` yolu |
| `K003-audio/backend/asio/asio-latency-ve-jitter.md` | Yeniden bağlanma sonrası gecikme yeniden okunmalı |
| `K003-audio/backend/asio/asio-cpp-ornekleri.md` | Hata kodu çözümleme örnekleri |
| `K000-isletim-sistemi/isletim-sistemi-audio/wasapi` | `IMMNotificationClient` / `GetDefaultAudioEndpoint` sahibi katman |
| `K003-audio/backend` | Telemetri: olay sayaçları ve tepki süresi |

---

## §13 Doğrulama protokolü (bu dosya için)

1. Her iddia §10 tablosunda bir URL'ye bağlanır; açılmayan iddia yazılmaz.
2. Kod blokları `Kaynak: <url> (<tarih>)` etiketlidir; `Dosya:` / `Satır:`
   biçiminde **uydurma repo referansı yoktur** (repo'da `*.cpp`/`*.h` bulunmaz).
3. §9 durum makinesi açıkça **"tasarım — uygulanmadı"** olarak etiketlenir;
   hiçbir süre/suçlama değeri verilmez.
4. Ölçülmüş tepki süresi veya başarı oranı iddiası **yapılmaz** → `UNKNOWN`.
5. Yer tutucu etiketler kullanılmaz; bilinmeyen `UNKNOWN` /
   `⚠️ VERIFICATION REQUIRED` yazılır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Kaynak:** learn.microsoft Device Events · IMMNotificationClient · OnDeviceStateChanged ·
OnDeviceRemoved · RegisterEndpointNotificationCallback · GetDefaultAudioEndpoint ·
DEVICE_STATE_XXX Constants · About the Windows Core Audio APIs · introlab/irl_audio asio.h
(kAsioResetRequest · kAsioResyncRequest · sampleRateDidChange · kAsioBufferSizeChange · ASE_*) ·
2026-10-12
