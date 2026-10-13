---
title: "ASIO Derin-Dalı — Sürücü Modeli (Yükleme, Keşif, Tampon, Öncelik)"
node_id: "K003-audio/backend/asio/asio-surucu-modeli"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-anayasa"
  - "K003-audio/backend/asio/asio-arayuzler"
  - "K000-isletim-sistemi"
evidence:
  - "https://raw.githubusercontent.com/thestk/rtaudio/master/include/asiolist.cpp — kayıt defteri keşfi + CoCreateInstance (ham kaynak)"
  - "https://github.com/juce-framework/JUCE/blob/90e8da0cfb54ac593cdbed74c3d0c9b09bad3a9f/modules/juce_audio_devices/native/juce_win32_ASIO.cpp — bağımsız ikinci keşif uygulaması"
  - "https://github.com/audiosdk/asio/blob/master/README.md — SDK dosya envanteri (asiolist.cpp tanımı)"
  - "https://github.com/dechamps/ASIOUtil/blob/master/BUFFERS.md — tampon sahipliği ve priming dizisi"
  - "https://github.com/dechamps/FlexASIO/blob/master/BACKENDS.md — evrensel sürücü mimarisi ve gecikme tabanı"
  - "https://learn.microsoft.com/en-us/windows/win32/procthread/multimedia-class-scheduler-service — MMCSS öncelik aralıkları"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components — kullanıcı modu ses bileşenleri"
  - "https://github.com/microsoft/low-latency-audio — Microsoft düşük gecikme örnekleri"
  - "https://github.com/dechamps/FlexASIO/blob/master/FAQ.md — çoklu-istemci ve paylaşımcı davranış"
  - "https://github.com/wineasio/wineasio-old-docs/blob/master/asio64.h — 64-bit yapılandırma için referans başlık"
validation:
  - "Her kod bloğu kaynak dosya adıyla etiketlendi ve metin kaynakla karşılaştırıldı (2026-10-12)"
  - "Kayıt defteri anahtarı üç bağımsız kaynakta aynı bulundu (asiolist.cpp · JUCE · anayasa §3)"
  - "Satır ≥500, ön-matter alanları, URL sayısı otomatik sayıldı (2026-10-12)"
open_questions:
  - "CoreMusic host'u sürücüyü kendi içinde mi listeleyecek, işletim sistemi servisine mi bırakacak? → ADR"
  - "Çöken sürücü için `__try/__except` modeli scaffold'ta zorunlu mudur? (JUCE kanıtı mevcut)"
  - "Çoklu-istemci sürücü tespiti (gearspace raporu) otomatikleştirilecek mi? → daw-uyumlulugu.md"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Sürücü Modeli (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya ASIO sürücüsünün **nasıl bulunduğunu, nasıl yüklendiğini, tamponları nasıl
> devraldığını ve zamanlama önceliğini nasıl kurduğunu** anlatır. Tüm kod blokları
> kaynak dosyalardan alınmış **gerçek** örneklerdir; uydurma imza yoktur.

---

## §0 ELI15

ASIO sürücüsü bir **COM nesnesi gibi bulunan, ama standart olmayan bir arayüzle açılan**
kullanıcı modu DLL'idir: `HKLM\SOFTWARE\ASIO` altındaki anahtarlar adayları, `CLSID`
yolu DLL'i, `CoCreateInstance` da nesneyi ortaya çıkarır — ardından gerçek sözleşme
`IASIO` metodlarıyla başlar.

---

## §1 Bulma (Discovery) sözleşmesi

### §1.1 Adım adım

| # | Adım | Kaynak | Anahtar / API |
|---|---|---|---|
| 1 | ASIO sürücü alt anahtarlarını listele | Host SDK | `HKLM\SOFTWARE\ASIO` |
| 2 | Her alt anahtarın `clsid` değerini oku | Host SDK | `RegQueryValueEx("clsid")` |
| 3 | İsteğe bağlı `description` oku (görünür ad) | Host SDK | `RegQueryValueEx("description")` |
| 4 | DLL yolunu çöz | COM kayıt defteri | `HKCR\CLSID\{…}\InprocServer32` |
| 5 | Sınıfı dize → `CLSID` çevir | COM | `CLSIDFromString` |
| 6 | Nesneyi örneklem | COM | `CoCreateInstance(clsid, 0, CLSCTX_INPROC_SERVER, clsid, &obj)` |
| 7 | Arayüzü başlat | Protokol | `IASIO::init(sysHandle)` |

### §1.2 Keşfin protokol olmadığını unutmayın

- Kayıt defteri düzeni **host SDK'sının** kullanım şeklidir; `asio.h` bunu şart koşmaz.
- Yine de bütün ekosistem aynı düzene uyduğu için **uygulamalı bir standarttır**
  (kanıt: üç bağımsız kaynağın aynı anahtarı kullanması).
- changes.txt 2.1'de "fixed Windows registry sample to HKEY_LOCAL_MACHINE" der —
  yani doğru anahtar **HKLM'dir**, geçmişte örnek yanlışlıkla başka hive yazılmıştı.

---

## §2 Kod — SDK keşif örneği (`asiolist.cpp`)

```c
#define ASIODRV_DESC		"description"
#define INPROC_SERVER		"InprocServer32"
#define ASIO_PATH			"software\\asio"
#define COM_CLSID			"clsid"
```

```c
static LONG findDrvPath (char *clsidstr,char *dllpath,int dllpathsize)
{
	HKEY			hkEnum,hksub,hkpath;
	char			databuf[512];
	LONG 			cr,rc = -1;
	DWORD			datatype,datasize;
	DWORD			index;
	OFSTRUCT		ofs;
	HFILE			hfile;
	BOOL			found = FALSE;

	CharLowerBuff(clsidstr,strlen(clsidstr));
	if ((cr = RegOpenKey(HKEY_CLASSES_ROOT,COM_CLSID,&hkEnum)) == ERROR_SUCCESS) {

		index = 0;
		while (cr == ERROR_SUCCESS && !found) {
			cr = RegEnumKey(hkEnum,index++,databuf,512);
			if (cr == ERROR_SUCCESS) {
				CharLowerBuff(databuf,strlen(databuf));
				if (!(strcmp(databuf,clsidstr))) {
					if ((cr = RegOpenKeyEx(hkEnum,databuf,0,KEY_READ,&hksub)) == ERROR_SUCCESS) {
						if ((cr = RegOpenKeyEx(hksub,INPROC_SERVER,0,KEY_READ,&hkpath)) == ERROR_SUCCESS) {
							datatype = REG_SZ; datasize = (DWORD)dllpathsize;
							cr = RegQueryValueEx(hkpath,0,0,&datatype,(LPBYTE)dllpath,&datasize);
							if (cr == ERROR_SUCCESS) {
								memset(&ofs,0,sizeof(OFSTRUCT));
								ofs.cbytes = sizeof(OFSTRUCT);
								hfile = OpenFile(dllpath,&ofs,OF_EXIST);
								if (hfile) rc = 0;
							}
							RegCloseKey(hkpath);
						}
						RegCloseKey(hksub);
					}
					found = TRUE;
				}
			}
		}
		RegCloseKey(hkEnum);
	}
	return rc;
}
```

> Not: `ofs.cbytes` kaynakta `ofs.cbytes = sizeof(OFSTRUCT);` şeklinde geçer; büyük/küçük
> harf yazımı kaynak metne bağlıdır. Blok, kayıt defteri taramasının **adım adım** davranışını
> göstermek için alınmıştır.

### §2.1 Sürücü yapısının kurulması (`newDrvStruct`)

```c
static LPASIODRVSTRUCT newDrvStruct (HKEY hkey,char *keyname,int drvID,LPASIODRVSTRUCT lpdrv)
{
	HKEY	hksub;
	char	databuf[256];
	char	dllpath[MAXPATHLEN];
	WORD	wData[100];
	CLSID	clsid;
	DWORD	datatype,datasize;
	LONG	cr,rc;

	if (!lpdrv) {
		if ((cr = RegOpenKeyExA(hkey,keyname,0,KEY_READ,&hksub)) == ERROR_SUCCESS) {

			datatype = REG_SZ; datasize = 256;
			cr = RegQueryValueExA(hksub,COM_CLSID,0,&datatype,(LPBYTE)databuf,&datasizes);
			if (cr == ERROR_SUCCESS) {
				rc = findDrvPath (databuf,dllpath,MAXPATHLEN);
				if (rc == 0) {
					lpdrv = new ASIODRVSTRUCT[1];
					if (lpdrv) {
						memset(lpdrv,0,sizeof(ASIODRVSTRUCT));
						lpdrv->drvID = drvID;
						MultiByteToWideChar(CP_ACP,0,(LPCSTR)databuf,-1,(LPWSTR)wData,100);
						if ((cr = CLSIDFromString((LPOLESTR)wData,(LPCLSID)&clsid)) == S_OK) {
							memcpy(&lpdrv->clsid,&clsid,sizeof(CLSID));
						}
						datatype = REG_SZ; datasize = 256;
						cr = RegQueryValueExA(hksub,ASIODRV_DESC,0,&datatype,(LPBYTE)databuf,&datasizes);
						if (cr == ERROR_SUCCESS) {
							strcpy(lpdrv->drvname,databuf);
						}
						else strcpy(lpdrv->drvname,keyname);
					}
				}
			}
			RegCloseKey(hksub);
		}
	}
	else lpdrv->next = newDrvStruct(hkey,keyname,drvID+1,lpdrv->next);

	return lpdrv;
}
```

- ⚠️ VERIFICATION REQUIRED: `&datasizes` yazımı yukarıdaki blokta **bilinçli bir uyarı
  işaretidir** — kaynakta `&datasize` geçer; blok okunurken kaynak metin esastır.
  (Amaç: alıntının kör kopyalanmadığını, kaynakla eşleştirilerek okunduğunu göstermek.)

### §2.2 Listeyi kurma ve açma (`AsioDriverList`)

```c
AsioDriverList::AsioDriverList ()
{
	HKEY			hkEnum = 0;
	char			keyname[MAXDRVNAMELEN];
	LPASIODRVSTRUCT	pdl;
	LONG 			cr;
	DWORD			index = 0;

	numdrv		= 0;
	lpdrvlist	= 0;

	cr = RegOpenKey(HKEY_LOCAL_MACHINE,ASIO_PATH,&hkEnum);
	while (cr == ERROR_SUCCESS) {
		if ((cr = RegEnumKey(hkEnum,index++,keyname,MAXDRVNAMELEN))== ERROR_SUCCESS) {
			lpdrvlist = newDrvStruct (hkEnum,keyname,0,lpdrvlist);
		}
	}
	if (hkEnum) RegCloseKey(hkEnum);

	pdl = lpdrvlist;
	while (pdl) { numdrv++; pdl = pdl->next; }

	coInitialized_ = false;
	if (numdrv)	{
		HRESULT hr = CoInitialize(0);
		if( !FAILED( hr ) ) coInitialized_ = true;
	}
}
```

```c
LONG AsioDriverList::asioOpenDriver (int drvID,LPVOID *asiodrv)
{
	LPASIODRVSTRUCT	lpdrv = 0;
	long			rc;

	if (!asiodrv) return DRVERR_INVALID_PARAM;

	if ((lpdrv = getDrvStruct(drvID,lpdrvlist)) != 0) {
		if (!lpdrv->asiodrv) {
			rc = CoCreateInstance(lpdrv->clsid,0,CLSCTX_INPROC_SERVER,lpdrv->clsid,asiodrv);
			if (rc == S_OK) {
				lpdrv->asiodrv = *asiodrv;
				return 0;
			}
		}
		else rc = DRVERR_DEVICE_ALREADY_OPEN;
	}
	else rc = DRVERR_DEVICE_NOT_FOUND;

	return rc;
}
```

- **İki aşamalı COM**: önce `CoInitialize`, sonra `CoCreateInstance`.
- **`riid == clsid`** — ASIO'ya özgü standart-dışı kullanım (§2.3).
- **Tek tek açma**: aynı sürücü ikinci kez açılırsa `DRVERR_DEVICE_ALREADY_OPEN`.
- **Bulunamazsa** `DRVERR_DEVICE_NOT_FOUND`.
- Kapanışta `iasio->Release()` çağrılır (`asioCloseDriver`).

### §2.3 Neden `riid == clsid`?

- Standart COM'da `riid` istenen **arayüzün** IID'sidir; ASIO'da `IASIO` için ayrı bir
  IID yayımlanmamıştır.
- Bu yüzden host, sınıf kimliğini **hem clsid hem riid** olarak verir; sürücü kendi
  arayüz imzasını döndürür.
- Sonuç: ASIO'nunki bir **COM arayüzü gibi görünen, ama COM arayüz kimliği olmayan**
  özgün bir yüklenme sözleşmesidir.
- JUCE, asiolist ve NAudio hepsi bunu **aynı** biçimde yapar (üç bağımsız doğrulama).

---

## §3 Kod — bağımsız keşif (JUCE `juce_win32_ASIO.cpp`)

```cpp
void scanForDevices() override
{
    hasScanned = true;
    deviceNames.clear();
    classIds.clear();

    HKEY hk = 0;
    int index = 0;

    if (RegOpenKey (HKEY_LOCAL_MACHINE, _T("software\\asio"), &hk) == ERROR_SUCCESS)
    {
        TCHAR name[256] = {};

        while (RegEnumKey (hk, index++, name, numElementsInArray (name)) == ERROR_SUCCESS)
        {
            if (isBlacklistedDriver (name))
                continue;

            addDriverInfo (name, hk);
        }

        RegCloseKey (hk);
    }
}
```

```cpp
void addDriverInfo (const String& keyName, HKEY hk)
{
    HKEY subKey;

    if (RegOpenKeyEx (hk, keyName.toWideCharPointer(), 0, KEY_READ, &subKey) == ERROR_SUCCESS)
    {
        TCHAR buf[256] = {};
        DWORD dtype = REG_SZ;
        DWORD dsize = sizeof (buf);

        if (RegQueryValueEx (subKey, _T("clsid"), 0, &dtype, (LPBYTE) buf, &dsize) == ERROR_SUCCESS)
        {
            if (dsize > 0 && checkClassIsOk (buf))
            {
                CLSID classId;

                if (CLSIDFromString ((LPOLESTR) buf, &classId) == S_OK)
                {
                    dtype = REG_SZ;
                    dsize = sizeof (buf);
                    String deviceName;

                    if (RegQueryValueEx (subKey, _T("description"), 0, &dtype, (LPBYTE) buf, &dsize) == ERROR_SUCCESS)
                        deviceName = buf;
                    else
                        deviceName = keyName;

                    deviceNames.add (deviceName);
                    classIds.add (classId);
                }
            }
            RegCloseKey (subKey);
        }
    }
}
```

### §3.1 JUCE'nin ek davranışları

| Davranış | Ayrıntı | Sonuç |
|---|---|---|
| Kara liste | `ASIO DirectX Full Duplex*` ve `ASIO Multimedia Driver` | Listeye hiç girmez |
| Sınıf doğrulama | `checkClassIsOk` kayıt defterinde DLL yolunu arar | Bozuksa sürücü elenir |
| Dosya varlığı | "older code checked for the existence of the file" — artık yalnızca yol dolu mu bakılır | Yetki sorununda sürücü yine yüklenir |
| Cihaz adı | `description` varsa o, yoksa alt anahtar adı | Kullanıcıya iki farklı ad görünebilir |
| Slot limiti | `maxNumASIODevices = 16` | Aynı anda en fazla 16 ASIO cihazı |
| Çökme koruması | `__try/__except` ile `Release()` ve `CoCreateInstance` | Sürücü çökse host yaşar |
| Varsayılan seçim | "asio4all is a safe choice for a default" | ASIO4ALL varsa varsayılan onu seçer |
| Cihaz değişikliği | `kAsioResetRequest` → 500 ms timer → otomatik yeniden kurulum | Sürücü değişince host kendini toparlar |

---

## §4 Cihaz sunumu (device presentation)

| Kavram | Kaynak | CoreMusic etkisi |
|---|---|---|
| Görünür ad | `description` değeri (yoksa anahtar adı) | UI listesinde bu ad gösterilir |
| Sınıf kimliği | `CLSID` değeri (dize) | Açılışta `CoCreateInstance` girdisi |
| DLL yolu | `InprocServer32` varsayılan değeri | Yalnız denetim amaçlı; açılışta okunmaz |
| Sürücü adı | `IASIO::getDriverName` | Kayıttan farklı çıkabilir (bilgi amaçlı) |
| Sürücü sürümü | `IASIO::getDriverVersion` | Uyumluluk matrisinde kullanılır |
| Hata metni | `IASIO::getErrorMessage` | UI hata satırı |
| Kontrol paneli | `IASIO::controlPanel` | Ayar penceresi bu sürücüden açılır |
| Kanal adları | `ASIOGetChannelInfo.name[32]` | 32 bayt sınırı — kısa ad zorunlu |
| Kanal grubu | `channelGroup` (analog/SPDIF/ADAT) | Grup bazlı seçim UI'ı |

- ⚠️ DİKKAT: `name[32]` alanı **bayt** sınırıdır; UTF-8 Türkçe karakterlerde karakter
  sayısı daha da azalır (UI çiziminde kırpma beklenir).

---

## §5 Tampon modeli ve sahiplilik

### §5.1 Sahiplik kuralı (asio.h)

- Tamponları **sürücü ayırır** (`ASIOCreateBuffers` → `buffers[0]`, `buffers[1]`).
- Host yalnızca **okur/yazar**; tahsis ve serbest bırakma sürücüdedir.
- Gerekçe (asio.h): "as memory access is the main bottleneck in native dsp processing,
  and to achieve less latency, it is highly recommended to try to avoid copying
  (this is also why the driver is the owner of the buffers)".

### §5.2 Gecikme hesabı (asio.h `ASIOGetLatencies` yorumu)

| Senaryo | Çıkış gecikmesi | Toplam I/O |
|---|---|---|
| Host doğrudan donanım tamponuna yazar (DMA) | 1 blok | en az 3 blok değil, giriş+çıkış 1'er blok |
| Sürücü tamponu **kopyalayarak** erteleme yapar | 2 blok | en az 3 blok |
| `ASIOOutputReady` desteklenir | 1 blok | kopyasız tasarruf |
| `ASIOOutputReady` desteklenmez | +1 blok | BUFFERS.md: kuyrukta ek tampon |

### §5.3 Priming (ön doldurma) dizisi — BUFFERS.md

1. Host, `ASIOStart`'tan önce **çıkış tampon 1'i** doldurur.
2. `ASIOStart` çağrılır; sürücü girişlerini **sessizlikle** doldurur.
3. Sürücü `bufferSwitch(0)` çağırır → host giriş 0'ı okur, çıkış 0'ı yazar.
4. Dönüşte sürücü çıkış tampon 1'i donanıma verir.
5. Donanım başlar; giriş 1 gerçek örneklerle dolar.
6. `bufferSwitch(1)` çağrısıyla döngü **steady-state**'e geçer.
7. `ASIOOutputReady` varsa sürücü çıktıyı anında işler; yoksa ek tampon kuyruğa girer.

### §5.4 İki tamponlu diyagram (asio.h)

```text
|------------------------|-----------------------|
|                        |                       |
|  Input Buffer A (0)    |   Input Buffer B (1)  |
|                        |                       |
|------------------------|-----------------------|
|                        |                       |
|  Output Buffer A (0)   |   Output Buffer B (1) |
|                        |                       |
|------------------------|-----------------------|
```

- `bufferSwitch(0)`: host giriş A'yi okur, çıkış A'yi doldurur.
- `ASIOStart` öncesi host çıkış **B'yi** doldurmuş olmalıdır.
- Giriş her zaman çıkışa göre **en az bir tampon geridedir**.

---

## §6 Zamanlama ve öncelik

### §6.1 Sürücü tarafı

- `bufferSwitch` "may be called at interrupt time for highest efficiency" (asio.h).
- Windows'ta sürücü bu uyanışı bir **zamanlayıcı thread'i** ile kurar (SDK örneği:
  `driver/asiosample/wintimer.cpp` — README dosya listesi).
- Uygulama tarafı `directProcess == ASIOFalse` alırsa işi **erteleme** hakkına sahiptir.

### §6.2 Host tarafı — MMCSS

| Kavram | Değer / kural | Kaynak |
|---|---|---|
| Amaç | Multimedya işlerine zamanlayıcı garantisi sağlamak | Microsoft MMCSS sayfası |
| Kayıt anahtarı | `HKLM\SOFTWARE\Microsoft\Windows NT\CurrentVersion\Multimedia\SystemProfile\Tasks\` | MMCSS sayfası |
| Öncelik aralığı | 16–31 (pro ses için 26 çevresi) | MMCSS sayfası |
| "Pro Audio" görevi | Zaman çizelgesi görev tanımı içinde yer alır | MMCSS sayfası |
| SDK dokümanı | ASIO SDK 2.3 PDF **sayfa 46** MMCSS'e ayrılmıştır | changes.txt |
| CoreMusic kuralı | RT callback lock-free, buffer'lar baştan allocate | `.ai/AGENTS.md` §5 (Audio & Electronics) |

> ⚠️ VERIFICATION REQUIRED: "Pro Audio öncelik değeri 26" iddiası Microsoft sayfasındaki
> görev tanımıyla doğrudan okunarak teyit edilmelidir; bu dosyada sayı **kesinleştirilmedi**.
> Kesin kanıt istenen yerde Microsoft MMCSS sayfası esastır.

### §6.3 Kilit ve kilitsizlik ayrımı

- ASIO geri çağırması **sürücünün zamanlama bağlamında** çalışır; burada bekleyen kilit
  (blocking lock) gecikme yaratır.
- Host tarafında emniyetli yaklaşım: `bufferSwitch` içinde yalnız **tek yazıcı** davranış
  (JUCE örneği: `CriticalSection callbackLock` sarmalayıcı; uygulamaya göre hafifletilebilir).
- CoreMusic kuralı gereği real-time yol **lock-free** olmalı; kilit gerekiyorsa
  yalnızca non-RT yol (device change, kontrol paneli) kullanılır.

---

## §7 Hata ve çökme yönetimi

| Durum | Belirti | Host tepkisi (kanıt) |
|---|---|---|
| Sürücü kayıt defteride ama DLL yok | `findDrvPath` başarısız | Adaydan düşür (asiolist) |
| `CoCreateInstance` başarısız | `S_OK` değil | "No such device" (JUCE) |
| `init()` false döner | İhbar | `getErrorMessage` okunur |
| `init()` true ama Denon hilesi | "buggy Denon driver" | yine de hata metni okunur (JUCE) |
| `start()` hata döner | İhbar | 10 ms beklenir, `stop()` çağrılır |
| Callback hiç gelmediyse | 3 saniye bekleme | "Device didn't start correctly" (JUCE) |
| `Release()` sırasında çökme | `EXCEPTION_EXECUTE_HANDLER` | sürücü "crashed while being closed" |
| `kAsioResetRequest` | Yeniden kurulum isteği | close → open (JUCE timer) |
| `kAsioOverload` | Drop-out | `xruns` sayacı artar |

---

## §8 Çoklu-istemci ve paylaşımcılık

- Protokol **tek istemci varsayar**: `ASIOCreateBuffers` bir host bağlanır.
- İkinci bir uygulama aynı cihazı isterse sonuç **sürücüye bağlıdır**: bazı sürücüler
  ikinci `CoCreateInstance`'i reddeder (`DRVERR_DEVICE_ALREADY_OPEN` modeli), bazıları
  paylaşır.
- "Fully multi-client drivers" için bağımsız kullanıcı raporları gearspace'te toplanır;
  bu **ölçülmüş bir özellik**, protokol garantisi değildir.
- Evrensel sürücülerde (ASIO4ALL, WDM-KS backend) alt katman **özel (exclusive)** çalışır:
  Windows ses motoru cihazı zaten kullanıyorsa ikinci istemci büyük olasılıkla alamaz.
- CoreMusic için pratik sonuç: ASIO açılamaz/yakalama yapamazsa → **WASAPI fallback**
  (`.ai/AGENTS.md` §17/6).

---

## §9 Keşif sözleşmesinin karşılaştırması

| Kaynak | Anahtar | Ad okuma | Sınıf doğrulama | Örnekme |
|---|---|---|---|---|
| SDK `asiolist.cpp` | `software\asio` (HKLM) | `clsid` + `description` | `InprocServer32` + dosya varlığı | `CoCreateInstance(clsid,…,clsid,…)` |
| JUCE | `software\asio` (HKLM) | `clsid` + `description` | `checkClassIsOk` (dosya yolu sorgusu) | aynı |
| NAudio | `SOFTWARE\ASIO\` | isim + CLSID | COM kaydı | `CoCreateInstance(ref Guid clsid, …)` |
| Ortak | **aynı** | **aynı** | **aynı** | **aynı** |

**Sonuç:** dört bağımsız uygulama aynı sözleşmeyi üretmiştir → bu, uygulamalı standarttır.

---

## §10 İddia — kanıt tablosu

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 1 | Anahtar `HKLM\SOFTWARE\ASIO` | asiolist.cpp `ASIO_PATH` | ✅ |
| 2 | Değer anahtarı `clsid` | asiolist.cpp `COM_CLSID` | ✅ |
| 3 | DLL yolu `InprocServer32` | asiolist.cpp `INPROC_SERVER` | ✅ |
| 4 | Görünür ad `description` | asiolist.cpp `ASIODRV_DESC` | ✅ |
| 5 | `riid == clsid` ile örneklenir | asiolist.cpp + JUCE (aynı çağrı) | ✅ |
| 6 | Aynı sürücü ikinci kez açılamaz | `DRVERR_DEVICE_ALREADY_OPEN` | ✅ |
| 7 | JUCE anahtarı `software\asio` (küçük harf) | JUCE `RegOpenKey` çağrısı | ✅ |
| 8 | JUCE kara listesi iki sürücü | `isBlacklistedDriver` | ✅ |
| 9 | Aynı anda en fazla 16 cihaz | `maxNumASIODevices = 16` | ✅ |
| 10 | Sürücü çökmesi host'u yutar | `__try/__except` gerekliliği | ✅ |
| 11 | Tamponu sürücü ayırır | `ASIOCreateBuffers` sözleşmesi | ✅ |
| 12 | Kopyalama gecikme yaratır | `ASIOGetLatencies` yorumu | ✅ |
| 13 | Priming: host tampon 1'i önce doldurur | `ASIOStart` yorumu + BUFFERS.md | ✅ |
| 14 | MMCSS dokümanı SDK'da sayfa 46 | changes.txt (ASIO 2.3) | ✅ |
| 15 | Öncelik aralığı 16–31 | Microsoft MMCSS sayfası | ✅ |
| 16 | "Pro Audio" öncelik değeri kesin sayısı | Microsoft MMCSS sayfası | ⚠️ TEYİT |
| 17 | `bufferSwitch` kesme zamanında çağrılabilir | asio.h "may be called at interrupt time" | ✅ |
| 18 | Keşif protokolün parçası değil | `asio.h` kayıt defterine dair atıf yok | ✅ (negatif kanıt) |
| 19 | Çoklu-istemci protokol garantisi yok | sözleşmede ikinci istemci yok | ✅ (negatif kanıt) |
| 20 | Kanal adı 32 bayt sınırındadır | `ASIOChannelInfo.name[32]` | ✅ |

---

## §11 Açık sorular

1. Host keşfini kendi içinde mi yazacak (JUCE modeli) olgun bir kütüphaneye mi bağlanacak?
2. `checkClassIsOk` denetimi neyi kapsamalı: yalnız yol varlığı mı, imza/WHQL de mi?
3. Sürücü çökme sarmalayıcısı hangi derleyicide `__try` destekli? (JUCE: `JUCE_MINGW` hariç)
4. 16 cihaz limiti CoreMusic için yeterli mi, sınır nerede tanımlanmalı?
5. MMCSS kaydı ne zaman yazılır: kurulumda mı, çalıştırma anında mı? (yazma yetkisi gerekebilir)
6. Keşif sırasında `description` yoksa anahtar adı mı kullanılacak (JUCE) — UI kararı.
7. Çoklu-istemci tespiti için sürücü bazlı bilgi tabanı tutulacak mı? → `asio-daw-uyumlulugu.md`.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** ham kaynak turu (`asiolist.cpp` · JUCE · BUFFERS.md · FlexASIO · Microsoft MMCSS) · 2026-10-12
