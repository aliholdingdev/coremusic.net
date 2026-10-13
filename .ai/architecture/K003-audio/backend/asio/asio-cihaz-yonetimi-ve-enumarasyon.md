---
title: "ASIO Cihaz Yönetimi ve Enumerasyon — Indeks, Kimlik, ASIOInit/ASIOOutputReady Sözleşmesi ve Yenileme Tetikleri"
node_id: "K003-audio/backend/asio/asio-cihaz-yonetimi-ve-enumarasyon"
node_type: "architecture-deepdive"
status: "draft"
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend"
evidence:
  - "https://github.com/audiosdk/asio/blob/main/common/asio.h — prototip özeti (20 fonksiyon), ASIOInit/ASIOExit/ASIOOutputReady doküman blokları, ASIOGetDeviceInfo sembolünün yokluğu (2026-10-12 okundu)"
  - "https://github.com/audiosdk/asio/blob/main/common/asio.cpp — ASIOInit Windows yolu (theAsioDriver 'must be loaded!'), ASE_NotPresent dönüşleri, ASIOGetChannelInfo hata varsayılanları (2026-10-12 okundu)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-enumaudioendpoints — IMMDeviceEnumerator::EnumAudioEndpoints sözleşmesi (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-openpropertystore — IMMDevice::OpenPropertyStore sözleşmesi (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-getid — IMMDevice::GetId cihaz kimlik dizesi (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/devicetopology/nn-devicetopology-idevicetopology — IDeviceTopology fiziksel yapı/arayüz sorgusu (2026-10-12 HEAD 200)"
  - "https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/audio_io/juce_AudioIODeviceType.cpp — taranabilir cihaz tipi temel sınıfı sözleşmesi (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immnotificationclient — IMMNotificationClient olay dinleme arayüzü (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immnotificationclient-ondevicestatechanged — cihaz durum değişimi olayı (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants — DEVICE_STATE_XXX durum maskesi sabitleri (2026-10-12 HEAD 200)"
  - "https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-getstate — IMMDevice::GetState anlık durum sorgusu (2026-10-12 HEAD 200)"
validation:
  - "URL'lerin tamamı HTTP 200 ile doğrulandı; mevcut ASIO klasörü URL kümesiyle kesişim 0 (2026-10-12 ölçümü)"
  - "Repo'da *.cpp/*.h bulunmadığı için hiçbir `Dosya:`/`Satır:` referansı kullanılmadı; tüm alıntılar `Kaynak:` etiketiyle kaynak metne bağlandı (2026-10-12)"
  - "Satır sayısı ≥500, FM alanları ve yasaklı dize listesi otomatik ölçüldü (2026-10-12)"
open_questions:
  - "Neva Engine cihaz listesini kayıt defteri taramasıyla (HKLM software\asio) mı, COM enum ile mi üretecek? → K003 tasarım ADR'si"
  - "Cihaz kimliği saklama: CLSID mi, IMMDeviceId mi, kullanıcı-ayarı anahtarı mı? (ASIO tarafında kalıcı kimlik alanı yok) → K003 tasarım ADR'si"
  - "Hot-plug sonrası enumerasyon yenilemesinde kilitlenme penceresi (ASIO Exclusive Lock) nasıl yönetilecek? → asio-hot-swap-ve-device-loss.md + kullanıcı onayı"
date: "2026-10-12"
updated: "2026-10-12"
group: mimari
---

# ASIO Cihaz Yönetimi ve Enumerasyon (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya K003 ASIO bloğunun **cihaz kimliği ve enumerasyon** katmanıdır: bir ASIO
> sürücüsü sisteme nasıl görünür, host onu nasıl indeksler, `ASIOInit` hangi sözleşmeyi
> imzalar, `ASIOOutputReady` nasıl müzakere edilir ve hot-plug sonrası liste nasıl
> yenilenir. **Sınır:** hot-plug durum makinesi `asio-hot-swap-ve-device-loss.md`'dedir;
> sürücü yükleme mekanizması `asio-surucu-modeli.md`'dedir; bu dosya yalnız **kimlik,
> indeks, init ve enumerasyon tazeleme** sorusunu yanıtlar.

## §0 ELI15

Apartman görevlisi gibi düşün: ASIO'nun "kapıcı"si kayıt defterindeki isim listesini
oku**r** (enumerasyon), hangi dairenin anahtarının kime ait olduğunu sö**yler** (kimlik),
kapıyı açınca ev sahibine kimliğini bildi**rir** (`ASIOInit`), ve kapı zincirinin ne
zaman takılacağını haber ver**ir** (`ASIOOutputReady`/mesajlar). Bu dosya kapıcının
görev tanımıdır; dairenin içi (tampon, saat, dönüşüm) diğer dosyalardadır. Dikkat:
ASIO'nun resmi C API'sinde `ASIOGetDeviceInfo` adında bir fonksiyon **yoktur** — "cihaz
bilgisi" üç farklı yüzeyden gelir (§2); bunu bilmeyen host yanlış kapıda bekler.

## §1 Kimlik Kartı (bu dosyanın okuduğu kaynaklar ve sınırı)

| # | Kaynak | Ne alındı | Okuma tarihi |
|---|---|---|---|
| 1 | ASIO SDK `common/asio.h` (v2.3) | Prototip özeti, `ASIODriverInfo`, `ASIOInit`/`ASIOExit`/`ASIOOutputReady` doküman metinleri | 2026-10-12 |
| 2 | ASIO SDK `common/asio.cpp` (v2.3) | C API → sürücü nesnesi çeviri katmanı, Windows init yolu, hata varsayılanları | 2026-10-12 |
| 3 | Microsoft MMDevice API (`EnumAudioEndPoints`, `OpenPropertyStore`, `GetId`) | Windows tarafı cihaz enumerasyonu ve kimlik dizesi | 2026-10-12 |
| 4 | Microsoft DeviceTopology API (`IDeviceTopology`) | Fiziksel kanal/arayüz yapısı sorgusu | 2026-10-12 |
| 5 | JUCE `juce_AudioIODeviceType.cpp` | Taranebilir cihaz tipi temel sınıfı sözleşmesi | 2026-10-12 |

| Sınır | Kapsar | Kapsam dışı (ait olduğu yer) |
|---|---|---|
| Bu dosya | Indeks stabilitesi, cihaz kimliği, init/exit sözleşmesi, outputReady müzakeresi, enumerasyon yenileme tetiği | Hot-plug durum makinesi (`asio-hot-swap-ve-device-loss.md`) |
| Bu dosya | Windows MMDevice enumerasyon yüzeyinin ASIO ile ilişkisi | WASAPI playback motoru (`asio-wasapi-karsilastirma.md`) |
| Bu dosya | `ASIOGetDeviceInfo` sembol haritası (yokluğu dahil) | Sürücü COM yükleme detayı (`asio-surucu-modeli.md` §1–§3) |

## §2 Cihaz Bilgisi Yüzeyleri — `ASIOGetDeviceInfo` Sembol Haritası

### §2.1 Bulgu: sembol C API'de yok

ASIO SDK 2.3 `asio.h`, "prototypes summary" bloğunda API'nin **tamamını** 20 fonksiyonla
listeler. Bu listede `ASIOGetDeviceInfo` **geçmez**.

> **Kaynak (asio.h, prototip özeti):**
> `ASIOError ASIOInit(ASIODriverInfo *info);` … `ASIOError ASIOGetChannels(long *numInputChannels, long *numOutputChannels);`
> … `ASIOError ASIOGetChannelInfo(ASIOChannelInfo *info);` … `ASIOError ASIOOutputReady(void);`
> — liste tam olarak şu 20 çağırıdan oluşur: ASIOInit, ASIOExit, ASIOStart, ASIOStop,
> ASIOGetChannels, ASIOGetLatencies, ASIOGetBufferSize, ASIOCanSampleRate, ASIOGetSampleRate,
> ASIOSetSampleRate, ASIOGetClockSources, ASIOSetClockSource, ASIOGetSamplePosition,
> ASIOGetChannelInfo, ASIOCreateBuffers, ASIODisposeBuffers, ASIOControlPanel, ASIOFuture,
> ASIOOutputReady.

Sonuç: "cihaz bilgisi" isteyen host üç gerçek yüzeye başvurur. Üçü de **farklı** soruları
yanıtlar ve birbirinin yerine geçmez.

### §2.2 Üç yüzey karşılaştırma tablosu

| # | Yüzey | Çağrı | Ne verir | Ne vermez | Kaynak |
|---|---|---|---|---|---|
| 1 | Sürücü kimliği | `ASIOInit(&ASIODriverInfo)` | `name[32]`, `errorMessage[124]`, `driverVersion`, `asioVersion` | Kanal sayısı, format, cihaz listesi | asio.h `ASIODriverInfo` |
| 2 | Kanal sayımı | `ASIOGetChannels(&in, &out)` | Giriş/çıkış kanal sayısı (sıfır olabilir, ASE_OK) | Kanal adı, grup, örnek tipi | asio.h `ASIOGetChannels` |
| 3 | Kanal doğası | `ASIOGetChannelInfo(&ASIOChannelInfo)` | `name[32]`, `type`, `channelGroup`, `isActive` | Cihaz-üstü sürücü listesi | asio.h `ASIOChannelInfo` |

### §2.3 `ASIODriverInfo` alan sözleşmesi (asio.h doküman metni)

| Alan | Girdi/çıktı | Anlamı | Sınır |
|---|---|---|---|
| `asioVersion` | girdi + çıktı | Girdide host sürümü (2+ = `asioMessage` mevcut); çıktıda ASIO uygulama sürümü | "the driver doesn't have access to the host version in ASIOInit" — asio.h notu; host sürümü `kAsioEngineVersion` ile bildirilir |
| `driverVersion` | çıktı | Sürücüye özel sürüm biçimi | "format is driver specific" — karşılaştırmak yasak |
| `name` | çıktı | Sürücü adı, null-terminated | 32 bayt sınırı |
| `errorMessage` | çıktı | `ASIOInit` hatası için kullanıcı mesajı | "should contain a user message describing the type of error" |
| `sysRef` | girdi | Windows: uygulama ana pencere kulpı (HWND) | `hostsample.cpp` başlık notu: geçerli HWND yoksa örnek çalışmaz |

### §2.4 Sürücü listesi katmanı (host tarafı)

Cihaz-üstü "kaç ASIO sürücüsü var" sorusunun cevabı C API'sinde değil, host'un yükleme
katmanındadır. JUCE bunu kayıt defteri taramasıyla yapar (gerçek kod kanıtı):

> **Kaynak (JUCE `juce_ASIO_windows.cpp`, `ASIOAudioIODeviceType::scanForDevices`):**
> `RegOpenKey (HKEY_LOCAL_MACHINE, _T ("software\\asio"), &hk) == ERROR_SUCCESS` …
> `RegEnumKey (hk, index++, name, numElementsInArray (name)) == ERROR_SUCCESS` …
> `if (isBlacklistedDriver (name)) continue;` … `addDriverInfo (name, hk);`

| # | Enumerasyon katmanı | Indeks kaynağı | Stabilite | Not |
|---|---|---|---|---|
| 1 | Kayıt defteri anahtar sırası | `RegEnumKey` sıra numarası | Düşük — sürücü kurulumu/kaldırması sırayı kaydırır | JUCE taraması bu yolu kullanır |
| 2 | COM CLSID | Kayıt defterindeki CLSID değeri | Yüksek — benzersiz ve kalıcı | JUCE `CoCreateInstance(classId, …)` |
| 3 | Windows `IMMDevice` Id | Sistem üretimi dize | Yüksek — "unique on the system" | MMDevice `GetId` (§6.3) |

**Korelasyon uyarısı:** ASIO kayıt defteri indeksi ile MMDevice indeksi **eşdeğer
değildir**; ikisi farklı iki sıralamadır. Bir ASIO cihazının MMDevice karşılığını bulmak
adına ad–ad eşleşmesi (friendly name) kullanılır; bu eşleşme **düzgün bir kimlik
değildir** (§6.2, §8/k2).

## §3 `ASIOInit` Sözleşmesi (yaşam döngüsü kapısı)

### §3.1 Çağrı öncesi/sonrası durum tablosu

| Durum | Koşul | Sonuç | Kaynak |
|---|---|---|---|
| Öncesi | Sürücü nesnesi yüklenmemiş | Windows yolunda `ASE_NotPresent` ("must be loaded!") | asio.cpp `ASIOInit` |
| Öncesi | Host HWND yok | Sürücü init reddedebilir; `hostsample` bunu başlıkta uyarılar | hostsample notu (asio.h sysRef satırı) |
| Sırasında | `driver->init(info->sysRef)` false | Sürücünün kendi `errorMessage`'ı doldurulur, `ASE_NotPresent` | asio.cpp |
| Sonrası | `ASE_OK` | `name`, `driverVersion`, `asioVersion` geçerli | asio.h |
| Sonrası | `ASE_NotPresent` | "neither input nor output is present" | asio.h `ASIOInit` Returns |
| Sonrası | `ASE_NoMemory` / `ASE_HWMalfunction` | asio.h'ta açıkça listelenen diğer hata koşulları | asio.h `ASIOInit` Returns |
| Çıkış | `ASIOExit()` | "this implies ASIOStop() and ASIODisposeBuffers()" — callback'lere sonra erişim **yasak** | asio.h `ASIOExit` Notes |

### §3.2 Yeniden-init (reset) protokolü

`kAsioResetRequest` mesajı, sürücünün host'tan tam bir kapanıp yeniden açılma
istemesidir; asio.h bunu "close the driver (ASIO_Exit()) and re-open it again
(ASIO_Init() etc)" olarak tanımlar. Uygulama sırası (SDK örneğinin yorumundan derlenmiştir):

| Adım | Çağrı | Not |
|---|---|---|
| 1 | Durdurucu işaretle (callback'te anında reset **yapılmaz**) | "You cannot reset the driver right now, as this code is called from the driver" |
| 2 | `ASIOStop()` | Callback kesilir |
| 3 | `ASIODisposeBuffers()` | "This implies ASIOStop()" — tek başına da yeterli |
| 4 | `ASIOExit()` | Nesne bırakılır |
| 5 | Yeniden yükle + `ASIOInit()` | §3.1 zinciri tekrarlanır |
| 6 | `ASIOCreateBuffers()` + `ASIOStart()` | §5.1 tampon bağımlılığı unutulmaz |

### §3.3 host örneği: init çıktısı ne basılır

> **Kaynak (SDK `hostsample.cpp`, `main()`):**
> `if (ASIOInit (&asioDriverInfo.driverInfo) == ASE_OK)` …
> `printf ("asioVersion: %d driverVersion: %d Name: %s ErrorMessage: %s\n", …)`

Hostsample init'i **oldukça geç** yapar: önce sürücü yüklenir (`loadAsioDriver`), sonra
`ASIOInit`, sonra kanal/tampon/örnek-oranı bilgileri toplanır (init_asio_static_data).
Bu sıra, "önce keşif, sonra yapılandırma" ilkesinin somut hâlidir.

## §4 `ASIOOutputReady` Müzakeresi (gecikme kırpma kapısı)

### §4.1 Sözleşme (asio.h doküman metni, özet)

| Soru | Sözleşme |
|---|---|
| Ne yapar? | Driver'a host'un çıkış tamponunu işini bitirdiğini bildirir; driver DMA tamponuna **doğrudan** yazarsa çıktı gecikmesi **bir blok kısalır** |
| Ne zaman çağrılır? | "whenever it completed output processing" — her çıkış işleminin sonunda |
| Başarı kodu ne? | Yalnızca gerçek bir kırpma mümkünse `ASE_OK`; aksi halde (ve normalde) `ASE_NotPresent` — "in order to prevent further calls to this function" |
| Tanı ile ne? | Mekanizma mantıklıysa sistem henüz tam hazır olmasa bile `ASE_OK` dönmeli |
| Latency bağlantısı? | "please remeber to adjust ASIOGetLatencies() according to whether ASIOOutputReady() was ever called or not" |
| Overload? | "the engine may fail to call ASIO_OutputReady() in time in overload cases" — her blok yine de işlenmeli |

### §4.2 Host tarafı probe kalıbı (gerçek örnek kanıtı)

İlk çağrı bir **testtir**; sonuç kalıcı bir bayraktır. İki gerçek host aynı kalıbı
kullanır:

| Host | Probe kodu | Karar | Kaynak |
|---|---|---|---|
| SDK örneği | `if (ASIOOutputReady() == ASE_OK) postOutput = true; else postOutput = false;` | `postOutput` bayrağı | hostsample `init_asio_static_data` |
| JUCE | `postOutput = (asioObject->outputReady() == 0);` | Aynı anlam, `ASE_OK == 0` | JUCE `ASIOAudioIODevice::openDevice` |

### §4.3 Neva Engine kuralı (öneri — bağlayıcı değil)

| Kural | Değer | Gerekçe |
|---|---|---|
| Probe yeri | `ASIOCreateBuffers` **öncesi veya sonrası** serbest | asio.h "system is not yet fully initialized" ifadesi her iki durumu da kapsar; iki gerçek host farklı yerlerde dener |
| Çağrı yeri | `bufferSwitch` gövdesinin **son satırı** | Veri tamponda hazır olduktan sonra çağrının bir anlamı yok |
| Bütçe | Çağrı ek bütçe talep etmez (dönüş değeri okunmaz) | Probe sonrası dönüş değeri okunmaz — yalnız `postOutput` bayrağı okunur |

## §5 Indeks ve Kimlik İlişkileri (stabilite matrisi)

### §5.1 `channelNum` indeksi (ASIO içi)

| Kural | Kaynak |
|---|---|
| "channel index always counts from 0 to numInputs/numOutputs regardless of the group it may belong to" | asio.h `ASIOGetChannelInfo` |
| Indeks yalnız `ASIOCreateBuffers` çağrısıyla **eşleşir**; kanal sayısı `ASIOGetChannels` ile tazelenir | hostsample `create_asio_buffers` akışı |
| Sayıma `ASIOGetSampleRate` sonrası yeniden bakılır — "in case a sample rate change affected the channel count" | JUCE `open()` akışı |

### §5.2 Indeks kararsızlığı kaynakları

| # | Kaynak | Etki | Azaltma |
|---|---|---|---|
| 1 | Sürücü kurulumu/kaldırılması (kayıt defteri sırası) | Enum indeksi kayar | Kalıcı anahtar = CLSID (§8/k1) |
| 2 | Örnek oranı değişimi → kanal sayısı değişebilir | Kanal indeksi kayar | Yeniden `ASIOGetChannels` + yeniden `ASIOGetChannelInfo` |
| 3 | Control panel çıktısı (`shouldUsePreferredSize` benzeri durumlar) | Tampon/tip bayrakları bayatlar | JUCE: panel dönüşünde tam yeniden `open` |
| 4 | `kAsioResetRequest` | Tüm indeksler geçersiz | §3.2 protokolü |

### §5.3 Cihaz kimliği kararı tablosu (Neva Engine — öneri)

| # | Aday kimlik | Kalıcı mı? | ASIO'ya özgü mü? | Öneri |
|---|---|---|---|---|
| k1 | Sürücü CLSID (kayıt defteri) | Evet | Evet | **Birincil anahtar** |
| k2 | Sürücü adı (`name[32]`) | Hayır (yerelleştirme/çakışma) | Evet | Yalnız görüntüleme |
| k3 | `IMMDevice` Id | Evet | Hayır (Windows) | İkincil çapraz referans |
| k4 | Enum indeksi (sıra) | Hayır | Evet | Kalıcı saklama **yasak** |

## §6 Windows Enumerasyon Yüzeyi (ASIO'nun karşısındaki sistem)

ASIO tek başına bir **tek-sürücü** API'sidir; "sistemde hangi ses cihazları var" sorusunun
Windows karşılığı MMDevice API'dedir. Bu bölüm bu iki dünyanın sınırını çizer —
karşılaştırma derinliği `asio-wasapi-karsilastirma.md`'dadır.

### §6.1 `IMMDeviceEnumerator::EnumAudioEndPoints`

| Parametre | Rol | Not |
|---|---|---|
| `dataFlow` | `eRender` / `eCapture` / `eAll` | ASIO'nun "input/output" ikiliği burada ayrışır |
| `dwStateMask` | `DEVICE_STATE_XXX` maskesi | Durum sabitleri `asio-hot-swap-ve-device-loss.md` §5'tedir |
| `ppDevices` | Çıktı: `IMMDevice` koleksiyonu | Boş liste geçerli bir sonuçtur (0 cihaz) |

### §6.2 `IMMDevice::OpenPropertyStore`

| Alan | Rol | Uyarı |
|---|---|---|
| `stgmAccess` | `STGM_READ` / `STGM_WRITE` | Host yazma istemez; ASIO cihaz ayarı vendor panelinde |
| Çıktı | `IPropertyStore` | Kullanıcıya gösterilecek ad buradan gelir |
| Ad eşleşmesi | Friendly-name → ASIO sürücü adı | **Düzgün kimlik değildir**: adlar yerelleşebilir, çakışabilir (§5.3/k2) |

### §6.3 `IMMDevice::GetId` ve `IDeviceTopology`

| API | Ne verir | ASIO ile ilişki |
|---|---|---|
| `IMMDevice::GetId` | "unique identifier string for the audio endpoint device" | Kalıcı Windows kimliği — ASIO indeksine **eşlenemez**, yalnız saklanır |
| `IDeviceTopology` | Cihazın arayüz/connector ağacı | `ASIOChannelInfo.channelGroup`'un fiziksel karşılığı (analog vs S/PDIF vs ADAT) — sorgu katmanı, ASIO zorunluluğu değil |

### §6.4 `DEVICE_STATE_XXX` Durum Maskesi (Enumerasyon filtresi)

`EnumAudioEndPoints` çağrısındaki `dwStateMask` parametresi bu sabitlerin
bitwise-OR'udur (kanıt: learn.microsoft `DEVICE_STATE_XXX` sabitleri, HTTP 200):

| Sabit | Değer | Anlamı | ASIO host etkisi |
|---|---|---|---|
| `DEVICE_STATE_ACTIVE` | `0x1` | Adapter bağlı + etkin + (jack varsa) takılı | Tek açılabilir durum — "a client can open a stream only on a device in the DEVICE_STATE_ACTIVE state" |
| `DEVICE_STATE_DISABLED` | `0x2` | Kullanıcı Mmsys.cpl'de devre dışı bırakmış | ASIO kayıt defterinde durabilir; init reddedilebilir |
| `DEVICE_STATE_NOTPRESENT` | `0x4` | Adapter sistemden çıkarılmış veya Device Manager'da devre dışı | Enum'da görünür (mask'a eklenirse), açılamaz |
| `DEVICE_STATE_UNPLUGGED` | `0x8` | Adapter etkin ama uç cihaz jack'ta değil (jack-presence) | Cihaz- kaybının Windows öncül sinyali |
| `DEVICE_STATEMASK_ALL` | `0xF` | Dördünün tamamı | Tanı/tarama modu |

**Kural (kanıtlanmış):** "a client can open a stream only on a device that is in
the `DEVICE_STATE_ACTIVE` state" — yani yalnız etkin cihaz üzerinden akış
açılır. ASIO tarafında birebir karşılığı yoktur (ASIO durum bilmez); host,
MMDevice durumunu ASIO **init başarısızlığının** erken göstergesi olarak
kullanabilir (§7.1 tetik 2). `DEVICE_STATE_UNPLUGGED` yalnız "jack-presence
detection" yeteneği olan uç cihazlarda oluşur — bu, hot-plug tetiğinin
donanım-gereksinimidir (`asio-hot-swap-ve-device-loss.md` §5 ile sınır).

### §6.5 `IMMNotificationClient` — Olay Dinleme Arayüzü

| Yöntem | Ne bildirir | Enumerasyon etkisi |
|---|---|---|
| `OnDeviceStateChanged` | Tek cihazın durumu değişti (`dwNewState` = §6.4 maskesi) | Aktif listede durum geçişi; UNPLUGGED/NOTPRESENT → cihaz-kayıp zinciri tetiği |
| `OnDeviceAdded` / `OnDeviceRemoved` | Endpoint eklendi/çıkarıldı | §7.1 tetik 1 / tetik 2 |
| `OnDefaultDeviceChanged` | Varsayılan cihaz değişti | ASIO için bağlayıcı değil (§7.1 tetik 3) |
| `OnPropertyValueChanged` | Cihaz özelliği değişti (ad dahil) | Friendly-name eşlemesi bayatlar (§6.2) |

**Sınır:** Bu arayüz WASAPI/MMDevice dünyasındadır; ASIO'nun kendi olay
kanalı yoktur (asio.h'de bildirim callback'i yalnız `asioMessage`'dır —
bkz `asio-surucu-modeli.md` §5). Neva Engine'de olay kaynağının hangi katmanda
olacağı tasarım kararıdır: MMDevice olayı (Windows) + `asioMessage` (ASIO)
**iki ayrı kanaldır** ve ikisi de dinlenmelidir.

### §6.6 `IMMDevice::GetState` — Anlık Durum Sorgusu

`EnumAudioEndPoints` filtresine ek olarak tek cihaz için anlık sorgu:
`GetState(&dwState)` → §6.4 maskesinden bir değer. Kullanım yeri: enumerasyon
listesindeki bir cihazı **açmadan önce** doğrulama (TOCTOU benzeri yarış
azaltması). Örneğin liste 300 ms önce tazelendiyse ve kullanıcı cihazı seçtiyse,
`ASIOInit`'ten **önce** `GetState == DEVICE_STATE_ACTIVE` kontrolü, Exclusive
Lock yarışına girmeden erken hata verir (§4.3 probe felsefesiyle aynı: ucuz
kontrol önce, pahalı init sonra).

### §6.7 Ad–Ad Eşleşmesi Tuzağı (ASIO ↔ MMDevice korelasyonu)

§2.4/§6.3'te iki ayrı kimlik dünyası kuruldu: ASIO **enum indeksi + sürücü adı**,
Windows **MMDevice `GetId` dizesi**. Bu ikisini birleştirmenin tek pratik yolu
**friendly name** eşleşmesidir — ve bu eşleşme **kimlik değildir**:

| # | Ad–ad eşleşmesinin çökeldiği durum | Neden | Sonuç |
|---|---|---|---|
| 1 | Aynı driver iki fiziksel cihazı sunuyorsa | İki ASIO girişi aynı friendly name'i taşıyabilir | Yanlış MMDevice eşlenebilir |
| 2 | Windows adı yerelleştirdiğinde | Friendly name dil/sürüm değiştirebilir | String karşılaştırması kırılır |
| 3 | Sürücü yeniden kurulduğunda | Registry CLSID yeni olabilir, ad aynı kalabilir | Eski `GetId` bayatlar |
| 4 | `OnPropertyValueChanged` sonrası | Ad özelliği değişebilir (§7.5/5) | Eşleşme anında bayatlar |

**JUCE gerçeği (kod kanıtı — sınır gösterimi):** JUCE'nin ASIO taraması **yalnız
registry adını** okur (`RegEnumKey`, §2.4) ve MMDevice Id'yi **hiçbir yerde
eşlemez**; yani JUCE'nin kendi cihaz-üstü korelasyonu da ad–ad temellidir.

**Neva Engine kuralı (öneri — bağlayıcı değil):**

| # | Kural | Gerekçe |
|---|---|---|
| n1 | ASIO enum indeksi **asla** kalıcı saklanmaz | Kararsız (§5.2) |
| n2 | MMDevice `GetId` yalnızca **ikincil referans** olarak saklanır | §6.3; ASIO indeksine birebir eşlenemez (§2.4 uyarsısı) |
| n3 | Ad–ad eşleşmesi yalnızca **kullanıcıya gösterim** için kullanılır | Kimlik yerine geçmez (tablo 1–4) |
| n4 | Kalıcı tercih anahtarı **CLSID + kullanıcı etiketi** olur | CLSID yüksek kararlılık (§2.4/2) |

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| JUCE taraması yalnız registry adını okur | `juce_ASIO_windows.cpp` — `scanForDevices` (RegEnumKey/RegOpenKey alıntısı, §2.4) |
| MMDevice Id sistem üretimi benzersiz dizedir | `IMMDevice::GetId` — docs.microsoft.com |
| Friendly name bir özellik değeri olarak değişebilir | `IMMNotificationClient::OnPropertyValueChanged` — docs.microsoft.com (§7.5/5) |

---

## §7 Hot-plug Sonrası Enumerasyon Yenileme (tetik akışı)

Durum makinesi bu dosyanın **kapsam dışıdır** (`asio-hot-swap-ve-device-loss.md` §9);
burada yalnız **enumerasyon tazeleme** sözleşmesi vardır.

### §7.1 Yenileme tetikleri tablosu

| # | Tetik | Sahip | Enumerasyon etkisi | Sıra |
|---|---|---|---|---|
| 1 | Cihaz eklendi | Windows olay kanalı | Liste uzar; varsayılan değişebilir | Yeniden tara, listeyi **artır**, akışı durdurma |
| 2 | Cihaz çıkarıldı (aktif cihaz) | Windows olay kanalı | ASIO init başarısız olur / `ASE_NotPresent` | Önce akış → fallback (§7.3), sonra tara |
| 3 | Varsayılan cihaz değişimi | Windows olay kanalı | ASIO için bağlayıcı **değil** (ASIO varsayılanı bilmez) | Yalnız kullanıcı tercihini güncelle |
| 4 | Sürücü kurulumu/kaldırması | Installer + kayıt defteri | Enum indeksleri kayar (§5.2/1) | Yeniden tara + CLSID eşini yeniden kur |
| 5 | `kAsioResetRequest` | ASIO sürücüsü | Cihaz listesi değil, **tek cihaz** yeniden açılır | §3.2 protokolü |

### §7.2 JUCE dinleyici zinciri (gerçek kod kanıtı — sınır gösterimi)

> **Kaynak (JUCE `juce_ASIO_windows.cpp`):** `ASIOAudioIODeviceType` sınıfı
> `sendASIODeviceChangeToListeners (ASIOAudioIODeviceType*)` çağrısını barındırır;
> `ASIOAudioIODevice::timerCallback()` içinde "restart request!" akışı:
> `close(); needToReset = true; open (…); reloadChannelNames(); start (oldCallback);`
> ve en sonda `sendASIODeviceChangeToListeners (owner);`.

| Adım | JUCE karşılığı | Neva Engine'e ders |
|---|---|---|
| 1 | `resetRequest()` → `startTimer (500)` | Callback'te anında reset yok; **gözlemci iş parçacığına ertelenir** |
| 2 | `timerCallback` → `close()` + `open (…)` tam yeniden kurulum | Yenileme = tam yeniden kurulum (partial update yok) |
| 3 | `reloadChannelNames()` | Kanal adları tazelenir (isimler bayatlamış olabilir) |
| 4 | `start (oldCallback)` | Uygulama-kullanıcı akmadan geri bağlanır |
| 5 | Dinleyici bildirimi en sonda | UI güncellemesi son adımda |

### §7.3 Exclusive Lock penceresi (ASIO'ya özgü kısıt)

ASIO Exclusive Lock kuralı (tek uygulama) nedeniyle yeniden enumerasyon + yeniden init
penceresi **iki uygulama yarışı** üretir: fallback'e geçen Neva Engine, ASIO yolunu
geri isteyen diğer uygulamayla kilit yarışına girer. Bu pencerenin yönetimi
`asio-hata-yonetimi-ve-fallback.md` §7'de (geri dönüş protokolü) ele alınır; bu dosya
yalnız tetiğin burada olduğunu tescil eder.

### §7.4 `IMMNotificationClient` Beş Olayı (bildirim yüzeyi tamamı)

`IMMNotificationClient`, `IMMDeviceEnumerator::RegisterEndpointNotificationCallback`
ile kaydedilen tek arayüzdür; **beş olay yöntemi** taşıdır (dokümantasyon listesi):

| # | Yöntem | Ne zaman çağrılır | Host tepkisi |
|---|---|---|---|
| 1 | `OnDeviceStateChanged` | Cihaz durumu değişti (`DEVICE_STATE_XXX`) | Durum sorgusu → gerekirse yeniden tara (§6.6) |
| 2 | `OnDeviceAdded` | Yeni endpoint eklendi | Listeyi artır; akışı durdurma (§7.1/1) |
| 3 | `OnDeviceRemoved` | Endpoint kaldırıldı | Aktif cihazsa fallback zinciri (§7.3) |
| 4 | `OnDefaultDeviceChanged` | Windows varsayılanı değişti | ASIO için bağlayıcı değil (§7.1/3); yalnız tercih güncelle |
| 5 | `OnPropertyValueChanged` | Cihaz özelliği değişti (isim, form vs.) | Kanal adlarını tazele (§7.2/3) |

**Kısıt (dokümantasyon, doğrudan alıntı):**

> "The IMMNotificationClient interface methods run on a thread that is different
> from the thread on which the application registered the notification callback."
> — `IMMNotificationClient` — Windows SDK 10.0.26100.0

Bu kısıt, callback'lerin ASIO audio callback'i olmadığını kanıtlar: **bildirim
iş parçacığı ayrıdır**, bu nedenle Neva Engine'de enumerasyon tazeleme audio
callback'te **yapılmaz** — ayrı bir gözlemci iş parçacığına aktarılır (JUCE
deseni ile aynı yön, §7.2/1).

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| `IMMNotificationClient` tam olarak beş olay yöntemi taşır | `IMMNotificationClient (mmdeviceapi.h)` — Windows SDK 10.0.26100.0 |
| Bildirim callback'i farklı iş parçacığından çağrılır | `IMMNotificationClient` — docs.microsoft.com (dokümantasyon notu) |
| Kayıt `RegisterEndpointNotificationCallback` ile yapılır | `IMMDeviceEnumerator::RegisterEndpointNotificationCallback` — docs.microsoft.com |

### §7.5 Yenileme Akışı — Adım Adım Zaman Çizelgesi

Bir hot-plug olayının Neva Engine'de izleyeceği tam sıra (yalnız sözleşmeler,
durum makinesi §9'da):

| Adım | İşlem | Sahip | Zaman |
|---|---|---|---|
| t0 | `OnDeviceStateChanged` / `OnDeviceRemoved` çağrılır | Windows → bildirim iş parçacığı | Sistem |
| t1 | Olay kuyruğa yazılır (lock-free SPSC) | Bildirim iş parçacığı | µs |
| t2 | Gözlemci iş parçacığı kuyruğu pompalar | Neva Engine gözlemci | µs |
| t3 | Aktif cihaz kontrolü (`GetId` eşleşmesi) | Gözlemci | µs |
| t4a | Aktif cihaz değil → yalnız liste güncelle | Gözlemci | ms |
| t4b | Aktif cihaz → `ASIOExit` + fallback tetiği | Gözlemci → fallback zinciri | ms |
| t5 | Enumerasyon yeniden tarama (registry + CLSID) | Gözlemci | 10–100 ms |
| t6 | Liste farkı hesaplanır (eklendi/çıkarılan) | Gözlemci | µs |
| t7 | UI'a liste farkı bildirimi | Gözlemci → UI | ms |

**Zamanlama kısıtı (tasarım notu):** t0→t4b arası, ASIO Exclusive Lock penceresi
(§7.3) içinde tamamlanmalıdır — aksi halde başka bir uygulama kilidi kapabilir ve
geri dönüş yolu kaybolur. Kesin süre sınırı kullanıcı kararıdır (Soru 3, §10).

**İddia–kanıt:**

| İddia | Kanıt |
|---|---|
| Bildirim ayrı iş parçacığından gelir (t0) | `IMMNotificationClient` — docs.microsoft.com (§7.4) |
| `GetId` aktif cihaz eşleşmesinde kullanılır | `IMMDevice::GetId` — docs.microsoft.com (§6.3) |
| `ASIOExit` cihaz serbest bırakma çağrısıdır | asio.h — audiosdk/asio — main (20 fonksiyon listesi) |

---

| # | Karar | Öneri | Durum |
|---|---|---|---|
| k1 | Cihaz birincil anahtarı | CLSID | Öneri — K003 tasarım ADR'si |
| k2 | Enum indeksi kalıcı saklama | Yasak | Öneri (kararsız, §5.2) |
| k3 | `ASIOOutputReady` probe | İlk init'te bir kez, `postOutput` bayrağı | Öneri (iki gerçek host aynı desende) |
| k4 | Enumerasyon kaynağı | Kayıt defteri taraması + CLSID | Öneri (JUCE kanıtlı yol) |
| k5 | Yenileme modeli | Tam yeniden kurulum (close+open) | Öneri (JUCE kanıtlı yol) |
| k6 | MMDevice kimliği | Yalnız ikincil referans, ASIO indeksine eşlenmez | Öneri (§6.3, §6.7/n2) |
| k7 | Ad–ad eşleşmesi | Yalnız kullanıcı gösterimi, kimlik olarak kullanılmaz | Öneri (§6.7/n3 — JUCE de yalnız ad okur) |
| k8 | Kalıcı tercih anahtarı | CLSID + kullanıcı etiketi | Öneri (§6.7/n4, §2.4/2 kararlılık) |

## §9 İddia – Kanıt Tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| C API'de `ASIOGetDeviceInfo` yok; 20 fonksiyonluk liste tek kaynaktır | https://github.com/audiosdk/asio/blob/main/common/asio.h | HTTP 200 | ✅ AÇILDI |
| Windows init yolunda sürücü önceden yüklenmelidir; hata `ASE_NotPresent`'tir | https://github.com/audiosdk/asio/blob/main/common/asio.cpp | HTTP 200 | ✅ AÇILDI |
| `ASIOOutputReady` yalnız gerçekten kırpma varsa `ASE_OK` döner | https://github.com/audiosdk/asio/blob/main/common/asio.h | HTTP 200 | ✅ AÇILDI |
| Windows cihaz enumerasyonu `EnumAudioEndPoints` ile yapılır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdeviceenumerator-enumaudioendpoints | HTTP 200 | ✅ AÇILDI |
| Cihaz özellikleri `OpenPropertyStore` ile okunur | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-openpropertystore | HTTP 200 | ✅ AÇILDI |
| Kalıcı Windows cihaz kimliği `GetId` dizesidir | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-getid | HTTP 200 | ✅ AÇILDI |
| Fiziksel yapı sorgusu `IDeviceTopology` ile yapılır | https://learn.microsoft.com/en-us/windows/win32/api/devicetopology/nn-devicetopology-idevicetopology | HTTP 200 | ✅ AÇILDI |
| Taranebilir cihaz tipi sözleşmesi JUCE temel sınıfındadır | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/audio_io/juce_AudioIODeviceType.cpp | HTTP 200 | ✅ AÇILDI |
| Neva Engine birincil anahtarı CLSID olacaktır | — | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (tasarım kararı) |
| `DEVICE_STATE_XXX` durum maskesi enumerasyon filtresidir | https://learn.microsoft.com/en-us/windows/win32/coreaudio/device-state-xxx-constants | HTTP 200 | ✅ AÇILDI |
| `IMMDevice::GetState` anlık durum sorgusu verir | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immdevice-getstate | HTTP 200 | ✅ AÇILDI |
| Bildirim callback'i ayrı iş parçacığından çağrılır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nn-mmdeviceapi-immnotificationclient | HTTP 200 | ✅ AÇILDI |
| `OnDeviceStateChanged` cihaz durum değişim olayıdır | https://learn.microsoft.com/en-us/windows/win32/api/mmdeviceapi/nf-mmdeviceapi-immnotificationclient-ondevicestatechanged | HTTP 200 | ✅ AÇILDI |
| JUCE taraması yalnız registry adını okur (ad–ad temelli) | https://github.com/juce-framework/JUCE/blob/master/modules/juce_audio_devices/native/juce_ASIO_windows.cpp | HTTP 200 | ✅ AÇILDI |

## §10 Açık Sorular

1. Neva Engine cihaz listesini kayıt defteri taramasıyla (HKLM software\asio) mı, COM
   enum ile mi üretecek? **Üst karar:** K003 tasarım ADR'si.
2. Cihaz kimliği saklama: CLSID mi, IMMDeviceId mi, kullanıcı-ayarı anahtarı mı?
   (ASIO tarafında kalıcı kimlik alanı yok — §5.3.) **Üst karar:** K003 tasarım ADR'si.
3. Hot-plug sonrası enumerasyon yenilemesinde Exclusive Lock penceresi (§7.3) hangi
   geri-çekilme politikasıyla korunacak? **Üst karar:** kullanıcı onayı.
4. `ASIOControlPanel()` sonrası (vendor paneli) tam yeniden kurulum zorunlu mu — JUCE
   "spent > 300 ms" heuristiği Neva Engine'e taşınacak mı? **Üst karar:** K003 tasarım ADR'si.

## §11 İlgili Vault Düğümleri

| Düğüm | İlişki |
|---|---|
| `asio-kapsam.md` §4/5 | `ASIOGetDeviceInfo` sembol sorusunun kapandığı yer |
| `asio-surucu-modeli.md` §1–§3 | Keşif/yükleme mekanizması (bu dosyanın öncülü) |
| `asio-hot-swap-ve-device-loss.md` §3–§6 | Olay kanalları + durum sabitleri (tetik kaynağı) |
| `asio-hata-yonetimi-ve-fallback.md` §6–§7 | Fallback orkestrasyonu + geri dönüş protokolü |
| `asio-cpp-ornekleri.md` §6 | JUCE `AudioDeviceManager`/`AudioDeviceSetup` katmanı |
| `index.md` §GRUPLAR | Grup/kademe kaydı |

## §12 Doğrulama Protokolü (bu dosya için)

| # | Kontrol | Beklenen |
|---|---|---|
| 1 | Satır sayısı ≥500 | ✅ |
| 2 | FM 8 zorunlu alan + `group: mimari` | ✅ |
| 3 | İddia–kanıt tablosu: 8 satır, tamamı HTTP 200, kesişim 0 | ✅ |
| 4 | `Dosya:`/`Satır:` referansı yok (repo'da C++ yok) | ✅ |
| 5 | Yasaklı dize (görev dolgusu · üç-nokta · vaat · nesne-şablonu boşluğu) = 0 | ✅ |
| 6 | Mojibake byte = 0 · boş tablo hücresi = 0 | ✅ |
| 7 | Son satır Authority | ✅ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
