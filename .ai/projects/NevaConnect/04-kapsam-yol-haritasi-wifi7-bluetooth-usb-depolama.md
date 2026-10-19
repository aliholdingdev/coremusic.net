---
title: "Neva Connect — Kapsam Yol Haritası: WiFi 7 · Bluetooth · USB · SATA/NVMe/M.2 (WLAN Öncelik)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "Microsoft Learn + web araştırması 2026-10-15 + pconlife export matrisi (01 doc)"
updated: 2026-10-15
---

# §1 Öncelik Sırası & Mimari Çerçeve

**Kullanıcı direktifi (2026-10-15):** Neva Connect Windows dalı için öncelik sırası
aşağıdaki gibidir. Her faz **bağımsız bir EXE** olarak geliştirilir (çoklu-exe mimarisi
— bkz. `02` doc, bu yazım anında vault'ta henüz yok: **UNKNOWN / beklenen**).

| Faz | Özellik | Birincil API ailesi | Bağımsız EXE | Sürücü gerekmez? |
|-----|---------|---------------------|--------------|------------------|
| **F1** | WLAN Native Wifi (`wifi list / connect / status / profile`) | `wlanapi.dll` (wlanapi.h) | `NevaWlan.exe` | Evet (user-mode) |
| **F2** | WiFi 7 (802.11be) raporlama + EHT PHY ayrıştırma | Native Wifi (`DOT11_PHY_TYPE`) | `NevaWifi7.exe` | Evet (user-mode) |
| **F3** | Bluetooth (radio/cihaz envanteri + BLE kontrol kanalı) | `bthprops.cpl` (bluetoothapis.h) + WinRT | `NevaBt.exe` | Evet (user-mode) |
| **F4** | USB arabirimleri (dongle + DSP donanım köprüsü) | `setupapi.dll` + `winusb.dll` | `NevaUsb.exe` | Evet (user-mode) |
| **F5** | SATA / mSATA / M.2 / NVMe depolama teşhisi | `ioctl` + `winioctl.h` | `NevaDisk.exe` | Evet (user-mode) |

**Mimari ilke (01 doc §4 ile aynı):**

1. Her faz tek başına çalışır, diğer fazlara `CreateProcess` ile bağımlı değildir.
2. Fazlar arası veri paylaşımı yalnız dosya (JSON/CSV) üzerinden olur — `GetProcAddress`
   ile **dinamik yükleme** (01 doc'deki dynload-resolver katmanı) tüm fazlarda ortaktır.
3. **Sürücü yazımı (WiFiCx / WDK / KMDF) KAPSAM DIŞI** — bu doküman yalnız uygulama
   (user-mode) katmanını planlar. Sürücü katmanı ayrı bir ADR + kullanıcı onayı gerektirir.
4. **XP hedefi:** 01 doc'de XP SP3 hedefleniyor (36 `Wlan*` export). Bu dokümandaki
   BT/USB/depolama fazları için XP min OS'ler §2-§5'te kaynaklı olarak verilmiştir.

> **Kritik akış:** bu doküman vault kaydı + kapsamlı plandır. **Kod yazımı YOKTUR** —
> kullanıcı onayı beklenmektedir. Zero-hallucination: her hücre ya kaynaklıdır ya da
> `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` işaretlidir.

## İlgili vault kayıtları

- `projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani.md` (10 DLL · export matrisi)
- `PROJECTS.md` §7.1.4 NevaConnect (Multi-Room Audio, C++20 + JS, WebSocket 9742)
- `E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md` (RPi5/L0-L3 mimarisi)
- `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` (WlanCloseHandle race gotcha)

---

# §2 WiFi 7 (802.11be)

## 2.1 İşletim Sistemi Desteği — Win11 24H2

**Doğrulanmış (Microsoft Support):** Wi-Fi 7, Windows 11 sürüm **24H2** ile başlar.

> "Note: Wi-Fi 7 is available starting with Windows 11, version 24H2."
> — *Faster and more secure Wi-Fi in Windows* (Microsoft Support)

Kaynak: `https://support.microsoft.com/en-us/topic/26177a28-38ed-1a8e-7eca-66f24dc63f09`
(güvenilirlik: **yüksek** — resmi Microsoft Support)

Aynı doğrulama IT pro sayfasında da yer alır:

> "Support for Wi-Fi 7 was added for consumer access points. Wi-Fi 7, also known as
> IEEE 802.11be Extremely High Throughput (EHT)..."
> — *What's new in Windows 11, version 24H2 for IT pros* (Microsoft Learn)

Kaynak: `https://learn.microsoft.com/en-us/windows/whats-new/whats-new-windows-11-version-24h2`
(güvenilirlik: **yüksek**)

**24H2 sürüm geçmişi** (Microsoft Support) de Wi-Fi 7'yi "Copilot+ PC exclusive features"
listesinde taşır:

> "Wi-Fi 7 support"
> — *Windows 11, version 24H2 update history*

Kaynak: `https://support.microsoft.com/en-us/servicing/os/windows-11/2024/09/windows-11-version-24h2-update-history`

## 2.2 Doğrulama Yöntemi — `netsh wlan show drivers`

**Doğrulanmış (Microsoft Support):** Adaptörün 802.11be destekleyip desteklemediği
komut satırından kontrol edilir:

> "You can also check to see what your network adapter supports by opening the Command
> Prompt (or Terminal), and then typing the command `netsh wlan show drivers`. Look next
> to **Radio types supported** and see if it includes **802.11be** (for Wi-Fi 7) or
> **802.11ax** (for Wi-Fi 6/6e) and look under **Authentication and cipher supported in
> infrastructure mode** and see if it includes WPA3 Personal and WPA3 Enterprise."
> — *Faster and more secure Wi-Fi in Windows*

Kaynak: `https://support.microsoft.com/en-us/topic/26177a28-38ed-1a8e-7eca-66f24dc63f09`

**Uygulama karşılığı (NevaWifi7.exe):** `netsh` çıktısını ayrıştırmak yerine Native Wifi
API üzerinden `WlanGetInterfaceCapability` + `WLAN_PHY_INFO.uPhyId` okunur; PHY türü
`DOT11_PHY_TYPE` enum'u ile karşılaştırılır (bkz. §2.5).

## 2.3 Müşteri Tarafı Gereksinimleri

**Doğrulanmış (Microsoft Support — 3 gereksinim):**

1. **Router:** Wi-Fi 7 (802.11be) + Wi-Fi 6/6E + WPA3 destekleyen yönlendirici.
2. **Windows 11** (24H2 ve üzeri) kurulu PC.
3. **Kablosuz ağ adaptörü:** Wi-Fi 7 + Wi-Fi 6/6E + WPA3 destekleyen adaptör.

> "A router that supports Wi-Fi 7, Wi-Fi 6/6E, and WPA3... Windows 11 installed on your
> PC... A wireless network adapter that supports Wi-Fi 7, Wi-Fi 6/6E, and WPA3."
> — *Faster and more secure Wi-Fi in Windows*

Kaynak: `https://support.microsoft.com/en-us/topic/26177a28-38ed-1a8e-7eca-66f24dc63f09`

## 2.4 Wi-Fi 7 Enterprise — Win11'de DESTEKLENMİYOR

**Doğrulanmış (Microsoft Support — birebir aktarım):**

> "Note: **Wi-Fi 7 (Enterprise) is not currently supported in Windows 11.**"
> — *Faster and more secure Wi-Fi in Windows*

Kaynak: `https://support.microsoft.com/en-us/topic/26177a28-38ed-1a8e-7eca-66f24dc63f09`

**Neva Connect etkisi:** Kurumsal (802.1X / RADIUS) Wi-Fi 7 senaryoları bu dalda
**planlanmaz**. WPA3-Enterprise 192-bit desteği (Microsoft Support'ta belgelenmiş olsa da)
Wi-Fi 7 PHY'siyle **kombine** edilemez — ayrı madde olarak §8'e yazılır.

## 2.5 Native Wifi API — EHT PHY Raporlaması

**Doğrulanmış (Microsoft Learn — `DOT11_PHY_TYPE` enum, Windot11.h):**

```c
typedef enum _DOT11_PHY_TYPE {
  dot11_phy_type_unknown   = 0,
  dot11_phy_type_any       = 0,
  dot11_phy_type_fhss      = 1,
  dot11_phy_type_dsss      = 2,
  dot11_phy_type_irbaseband= 3,
  dot11_phy_type_ofdm      = 4,
  dot11_phy_type_hrdsss    = 5,
  dot11_phy_type_erp       = 6,
  dot11_phy_type_ht        = 7,
  dot11_phy_type_vht       = 8,
  dot11_phy_type_dmg       = 9,
  dot11_phy_type_he        = 10,
  dot11_phy_type_eht       = 11,
  dot11_phy_type_IHV_start = 0x80000000,
  dot11_phy_type_IHV_end   = 0xffffffff
} DOT11_PHY_TYPE;
```

> "**dot11_phy_type_eht** — Specifies an Extremely High Throughput PHY type (EHT).
> 802.11be (Wi-Fi 7) devices can use HT." *(doc'daki "HT" ifadesi bir yazı hatasıdır;
> EHT kastedilmektedir.)*

Kaynak: `https://learn.microsoft.com/en-us/windows/win32/nativewifi/dot11-phy-type`
(güvenilirlik: **yüksek**)

**Uygulama karşılığı:** `WLAN_BSS_ENTRY` / `WLAN_PHY_FRAME_STATISTICS` üzerinden okunan
PHY türü `dot11_phy_type_eht == 11` ise Wi-Fi 7 bağlantısı raporlanır. Bu değer **kaynaklıdır
ve uydurulmamıştır**.

## 2.6 `wlanSignalValueToBarEx` — ⚠️ VERIFICATION REQUIRED

**Durum:** `WlanSignalValueToBarEx` sembolü **wlanapi.dll export tablosunda mevcuttur**
(01 doc'deki PE export analizi + üçüncü taraf DLL envanteri: `jesusninoc.com/dll/wlanapi/`,
`wlanutil.dll` bu sembolü `wlanapi`'ye forward eder — `https://www.jesusninoc.com/dll/wlanapi/`,
güvenilirlik: **orta**).

**Ancak:** MSDN/learn.microsoft.com sayfası bu araştırmada **çekilemedi**
(`CRAWL_NOT_FOUND`). **Minimum desteklenen sürüm (19041+ iddiası) DOĞRULANAMADI.**

> ⚠️ **VERIFICATION REQUIRED:** `wlanSignalValueToBarEx` için minimum OS sürümü
> bu dokümanda **UNKNOWN**'dur. 01 doc'deki Win10 19041 export matrisi bu sembolün
> varlığını doğrular, ancak **ilk hangi sürümde eklendiği kaynaklı DEĞİLDİR**.
> Kullanıcı onayında SDK header (`wlanapi.h`) üzerinden teyit edilecektir.

## 2.7 WiFiCx Sürücü Modeli (KAPSAM DIŞI — yalnız referans)

> **Bu alt bölüm sürücü katmanını tanımlar; Neva Connect kapsamı DIŞINDADIR.**
> Yalnızca, "sürücüleri değiştirecek" direktifinin user-mode ile karşılanıp
> karşılanamayacağının tartışması için referans olarak taşınmıştır.

**Doğrulanmış (Microsoft Learn — `WifiDeviceSetStationCapabilities`, wificx.h):**

> "To indicate support for MLO connections in Wi-Fi 7, the driver must set the
> **MaxMLOLinksSupported**, **MLOAddressesList**, **NumAkmsSupported**, and **AkmsList**
> fields of the **WIFI_STATION_CAPABILITIES** structure to non-zero values."

> "To support SAE connections using AKM 24 or AKM 8 with GCMP-256 cipher, the driver must
> add the following auth-cipher pairs in the WIFI_STATION_CAPABILITIES structure:
> - In **UnicastAlgorithmsList**: { **DOT11_AUTH_ALGO_WPA3_SAE**, **DOT11_CIPHER_ALGO_GCMP_256** }
> - In **MulticastMgmtAlgorithmsList**: { **DOT11_AUTH_ALGO_WPA3_SAE**, **DOT11_CIPHER_ALGO_GCMP_256** }"

> "To support OWE connections with GCMP-256 cipher, the driver must add the following
> auth-cipher pair: In **UnicastAlgorithmsList**: { **DOT11_AUTH_ALGO_OWE**,
> **DOT11_CIPHER_ALGO_GCMP_256** }"

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wificx/nf-wificx-wifidesvicesetstationcapabilities`
(güvenilirlik: **yüksek**)

**MLO / MLD MAC adresi kuralı (aynı sayfa, WiFiCx Wi-Fi 7):**

> "When the driver indicates management frames, use the **link MAC address**. For data
> frames, including 4-way handshake and 802.1x authentication frames, use the
> **MLD MAC address**."

> "Windows parses the beacon IEs for Multi-Link and RNR IEs. If present, it marks the AP
> as supporting MLO."

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/wificx-wi-fi-7`
(güvenilirlik: **yüksek**)

**Uygulama katmanı etkisi:** MLO bağlantısı olan bir AP'de **her link'in farklı bir
BSSID/MAC'si** olur. F1/F2 EXE'leri (`NevaWlan` / `NevaWifi7`) yalnız kullanıcı-modunda
olduğu için bu LOW-LEVEL MLO ayrıştırmasını **yapamaz** — yalnızca OS'in raporladığı
`WLAN_BSS_ENTRY` setini okur. MLO ayrıntılı ayrıştırma **sürücü katmanı** işidir → kapsam dışı.

## 2.8 WiFiCx Sürüm Tablosu & Runtime Check Deseni

**Doğrulanmış (Microsoft Learn — "Building a WiFiCx driver for multiple versions of Windows"):**

| İşletim Sistemi | KMDF | NetAdapterCx | WiFiCx |
|---|---|---|---|
| Windows 11, 21H2 ve 22H2 | 1.33 | 2.2 | **WiFiCx 1.0** |
| Windows 11 Insider Preview **Build 25330** ve sonrası | 1.33 | 2.2+ | **WiFiCx 1.0, 1.1, 1.2** |

> "WiFiCx allows you to build a driver once and use the resulting binary on multiple
> versions of Windows. Your driver can both run on future versions of Windows and run on
> past versions, **back to Windows 11, version 21H2**."

> "To run your driver on older versions of Windows, you must: 1. Specify the minimum
> version required. 2. **Perform a runtime check before invoking an API or accessing a
> structure or field that may or may not be present.**"

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/building-a-wificx-driver-for-multiple-versions-of-windows`
(güvenilirlik: **yüksek**)

**Runtime check makroları (`WifiFuncEnum.h` — aynı sayfa):**

```c
BOOLEAN WIFI_IS_FUNCTION_AVAILABLE ( FunctionName );
BOOLEAN WIFI_IS_STRUCTURE_AVAILABLE ( StructName );
BOOLEAN WIFI_IS_FIELD_AVAILABLE ( StructName, FieldName );
```

Sürüm kilidi: `WIFI_MINIMUM_VERSION_REQUIRED` makrosu ile kontrol edilir:

```make
C_DEFINES=$(C_DEFINES) -DRPC_NO_WINDOWS_H -DWIFI_MINIMUM_VERSION_REQUIRED=0
```

**NetAdapterCx sürüm tablosu** (`netadaptercx-version-overview`, aynı domain):

| İşletim Sistemi | KMDF | NetAdapterCx |
|---|---|---|
| Windows 11, 24H2 | 1.33 | **2.5** |
| Windows Server 2022 23H2 | 1.33 | 2.4 |
| Windows 11, 22H2 | 1.33 | 2.3 |
| Windows 11, 21H2 | 1.33 | 2.2 |
| Windows 10, 2004 | 1.31 | 2.0 |

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/netadaptercx-version-overview`

## 2.9 Uygulama Katmanı Karşılığı (NevaWifi7.exe — GetProcAddress deseni)

WiFiCx'ın `WIFI_IS_*` runtime-check deseninin **user-mode karşılığı** `GetProcAddress`'tır
(01 doc'deki dynload-resolver katmanı). Bu desen birden çok Windows sürümüne **tek derleme**
ile ulaşır:

```cpp
// 01 doc §4 dynload-resolver deseni — user-mode karşılık
using PFN_WlanGetAvailableNetworkList = DWORD (WINAPI*)(HANDLE, const GUID*, DWORD,
                                    PWLAN_AVAILABLE_NETWORK_LIST*);
auto pWlanGetAvailableNetworkList = reinterpret_cast<PFN_WlanGetAvailableNetworkList>(
    GetProcAddress(GetModuleHandleW(L"wlanapi.dll"), "WlanGetAvailableNetworkList"));
if (!pWlanGetAvailableNetworkList) {
    // XP SP3'te export VAR (01 doc: XP 36 export) — buraya düşerse sürüm tespiti hatalı
    // veya bağlam yanlış; graceful fallback uygula.
}
```

**Sürücü ↔ uygulama farkı:**

| Katman | Runtime check | Ne korur |
|--------|---------------|----------|
| WiFiCx (sürücü, KAPSAM DIŞI) | `WIFI_IS_FUNCTION_AVAILABLE` | Fonksiyon sembolünün o OS'ta var olup olmadığı |
| Neva Connect (uygulama) | `GetProcAddress != nullptr` | wlanapi.dll export'unun o sürümde var olup olmadığı (01 doc export matrisi) |

> **Önemli:** `wlanapi.dll` **export silmez** (01 doc: her sürüm geçişinde `−0`) — bu nedenle
> uygulama katmanında runtime-check zorunlu değil, **ergonomiktir**; sürücü katmanında ise
> WiFiCx yapı/alan eklediği için **zorunludur**.

---

# §3 Bluetooth

## 3.1 Windows Bluetooth API Aileleri

### 3.1.1 Win32 — `bluetoothapis.h` (`bthprops.cpl`)

**Doğrulanmış (Microsoft Learn — fonksiyon sayfası requirement blokları):**

| Fonksiyon | Min. desteklenen istemci | Başlık | Kütüphane | DLL |
|---|---|---|---|---|
| `BluetoothFindFirstRadio` | **Windows Vista, Windows XP with SP2** [desktop apps only] | bluetoothapis.h | Bthprops.lib | bthprops.cpl |
| `BluetoothGetDeviceInfo` | **Windows Vista, Windows XP with SP2** [desktop apps only] | bluetoothapis.h | Bthprops.lib | bthprops.cpl |
| `BluetoothGetRadioInfo` | **Windows Vista, Windows XP with SP2** [desktop apps only] | bluetoothapis.h | Bthprops.lib | bthprops.cpl |

> "Minimum supported client: **Windows Vista, Windows XP with SP2** [desktop apps only]
> — Minimum supported server: **None supported**"

Kaynaklar:
- `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/nf-bluetoothapis-bluetoothfindfirstradio`
- `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/nf-bluetoothapis-bluetoothgetdeviceinfo`
- `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/nf-bluetoothapis-bluetoothgetradioinfo`
(güvenilirlik: **yüksek** — hepsi resmi Microsoft Learn)

**Header fonksiyon listesi** (`https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/`,
2026-10-15 çekimi) — doğrulanmış tam liste şunları içerir:
`BluetoothAuthenticateDevice`, `BluetoothAuthenticateDeviceEx`,
`BluetoothAuthenticateMultipleDevices`, `BluetoothDisplayDeviceProperties`,
`BluetoothEnableDiscovery`, `BluetoothEnableIncomingConnections`,
`BluetoothEnumerateInstalledServices`, `BluetoothFindDeviceClose`,
`BluetoothFindFirstDevice`, `BluetoothFindFirstRadio`, `BluetoothFindNextDevice`,
`BluetoothFindNextRadio`, `BluetoothFindRadioClose`, `BluetoothGetDeviceInfo`,
`BluetoothGetRadioInfo`, `BluetoothIsConnectable`, `BluetoothIsDiscoverable`,
`BluetoothIsVersionAvailable`, `BluetoothRegisterForAuthentication`,
`BluetoothRegisterForAuthenticationEx`, `BluetoothRemoveDevice`,
`BluetoothSdpEnumAttributes`, `BluetoothSdpGetAttributeValue`,
`BluetoothSdpGetContainerElementData`, `BluetoothSdpGetElementData`,
`BluetoothSdpGetString`, `BluetoothSelectDevices`, `BluetoothSelectDevicesFree`,
`BluetoothSendAuthenticationResponse` (+ Ex), ...

### 3.1.2 ⚠️ `BluetoothGetHostedRadioInfo` — VERIFICATION REQUIRED

**Durum:** Bu fonksiyon **görev direktifinde** anılmıştır; ancak 2026-10-15 Microsoft Learn
`bluetoothapis.h` header fonksiyon listesi çekiminde **`BluetoothGetHostedRadioInfo`
GÖRÜNMEMEKTEDİR** (liste alfabetiktir: `BluetoothGetDeviceInfo` → doğrudan
`BluetoothGetRadioInfo`'ya geçer). MSDN sayfası da bu araştırmada çekilemedi
(`CRAWL_NOT_FOUND`).

> ⚠️ **VERIFICATION REQUIRED:** `BluetoothGetHostedRadioInfo` varlığı ve min OS'ü
> bu dokümanda **UNKNOWN**'dur. Kullanıcı onayında SDK header (`bluetoothapis.h`)
> üzerinden doğrudan teyit edilecektir. Mevcut listeye göre bu fonksiyon **kullanılamaz**;
> radio bilgisi için `BluetoothGetRadioInfo` (Vista/XP SP2+) kullanılır.

### 3.1.3 RFCOMM / SDP

**Doğrulanmış (Microsoft Learn — bluetoothapis.h):** SDP (Service Discovery Protocol)
işlevleri header'da mevcuttur: `BluetoothSdpEnumAttributes`, `BluetoothSdpGetAttributeValue`,
`BluetoothSdpGetContainerElementData`, `BluetoothSdpGetElementData`, `BluetoothSdpGetString`.
RFCOMM profile eşleşmesi, cihaz eşleştirmesi sırasında `Bth.inf`'teki profil listesiyle
karşılaştırılır (bkz. §3.3).

> "Windows supports vendor-supplied Bluetooth profiles. The GUIDs for those profiles
> standardized by the Bluetooth SIG are included in the in box INF file (Bth.inf). When
> users pair a Bluetooth device with a computer, the device's profiles are compared to the
> profiles that are listed in Bth.inf. If the device profile doesn't match one of those
> profiles, users receive a dialog box that asks them to provide appropriate vendor software."
> — *Bluetooth Host Radio Support*

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/bluetooth/bluetooth-host-radio-support`
(güvenilirlik: **yüksek**)

### 3.1.4 WinRT — `Windows.Devices.Bluetooth` (Windows 8+)

**Doğrulanmış (Microsoft Learn — `BluetoothLEAdvertisementWatcher`):**

| Gereksinim | Değer |
|---|---|
| **Device family** | **Windows 10** (introduced in **10.0.10240.0**) |
| **API contract** | Windows.Foundation.UniversalApiContract (**v1.0**) |
| **App capabilities** | `bluetooth` |

> "An object to receive Bluetooth Low Energy (LE) advertisements."

Kaynak: `https://learn.microsoft.com/en-us/uwp/api/windows.devices.bluetooth.advertisement.bluetoothleadvertisementwatcher`
(güvenilirlik: **yüksek**)

**Önemli not:** Görev direktifi "WinRT Windows.Devices.Bluetooth (Windows 8+)" dese de,
Microsoft Learn bu **BELİRLİ** sınıf için **Windows 10 (10.0.10240.0)** minimum göstermektedir.
Genel `Windows.Devices.Bluetooth` namespace'inin Win8/8.1'de kısmi varlığı **UNKNOWN** —
bu dokümanda **Windows 10** kabul edilir (kanıt: UniversalApiContract v1.0).

**Resmi örnek:** *Bluetooth advertisement sample* — `https://learn.microsoft.com/en-us/samples/microsoft/windows-universal-samples/bluetoothadvertisement/`
(güvenilirlik: **yüksek**)

## 3.2 Sürücü Katmanı — Microsoft Bluetooth Yığını (Bth.inf)

**Doğrulanmış (Microsoft Learn — "Bluetooth Host Radio Support"):**

> "A new Bluetooth radio might not match any of the device IDs in the **Bluetooth INF
> (Bth.inf)** that is included with Windows. A missing match prevents Windows from loading
> a Bluetooth stack for the device. Ensure that your radio works with the native Bluetooth
> stack in one of the following ways:
> - Create an INF for the radio that references Bth.inf...
> - Store an extended compatible ID OS descriptor in the device firmware...
> - Force the Bluetooth stack to load"

> "**It's no longer possible to add radios to the in-box Bth.inf file.**"

> "Use the Microsoft-defined class globally unique identifier (GUID)
> (`{e0cbf06c-cd8b-4647-bb8a-263b43f0f974}`) for Bluetooth devices only in those INF files
> that reference the in-box Bluetooth INF file (Bth.inf). The device uses the native Windows
> installer, services, and notification area icon. **If you implement your own Bluetooth
> stack, you must create a vendor-specific class GUID** and use the WLK test tools to ensure
> that the stack complies with the unclassified Windows Certification Program."

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/bluetooth/bluetooth-host-radio-support`
(güvenilirlik: **yüksek**)

> **"The Bluetooth stack included with Windows provides in-box support for only some
> Bluetooth profiles. Vendors must implement the required services to support any other
> Bluetooth profiles..."** (aynı kaynak)

**IOCTL katmanı (Windows drivers — "Bluetooth IOCTLs"):** Profile sürücüleri
`IOCTL_BTH_GET_LOCAL_INFO`, `IOCTL_BTH_GET_RADIO_INFO`, `IOCTL_BTH_GET_DEVICE_INFO`
kullanır. Min OS: **Windows Vista ve sonrası** (GitHub WindowsDriverDocs ddi kaynağı:
`IOCTL_BTH_GET_RADIO_INFO` — `req.target-min-winverclnt: Versions: Supported in Windows Vista, and later.`).

## 3.3 "Tüm Bluetooth sürücüleri değiştirilecek" Direktifi — OLASI OKUMALAR

**Kullanıcı direktifi (belirsiz):** *"tüm bluetooth sürücüleri değiştirilecek"*.
Bu direktifin **iki farklı okuması** vardır; hangisinin kastedildiği **UNKNOWN**'dur.

| Okuma | Anlam | Neva Connect üzerindeki etki | User-mode ile karşılanabilir mi? |
|---|---|---|---|
| **A — Yığın (stack) değişimi** | Windows'un kutu-içi (in-box) Microsoft Bluetooth yığını (Bth.inf + BthUSB.sys/BthLEEnum) **tamamen kaldırılıp** üçüncü taraf bir ISV/vendor Bluetooth yığını ile **değiştirilecek** | Vendor sürücü kurulumu + **farklı class GUID** + WLK/HLK sertifikasyonu gerekir; `bluetoothapis.h` fonksiyonlarının çoğu **Microsoft yığınına özeldir** ve ISV yığınında **çalışmayabilir** | **HAYIR** — user-mode ile karşılanamaz; **sürücü yazımı/kurulumu** gerekir (kapsam dışı) |
| **B — Uygulama katmanı soyutlaması** | Neva Connect'in **kendi** Bluetooth **erişim katmanı** (wrapper) yazacağı, altta hangi yığın olursa olsun tek bir arayüz sunacağı | Uygulama, `BluetoothFindFirstRadio` / WinRT `BluetoothLEAdvertisementWatcher` üzerinden erişir; yığın değişmez | **EVET** — bu tam olarak user-mode API ile karşılanır |

> ⚠️ **VERIFICATION REQUIRED / §8 madde #1:** Direktifin hangi okuma olduğu
> (A mı — yığın değişimi, B mi — uygulama katmanı soyutlaması) **kullanıcıya sorulacaktır.**
> Okuma A seçilirse **sürücü yazımı kapsamı** ayrı bir ADR + bütçe gerektirir ve bu
> dokümanın §7 faz planı **geçersizleşir** (user-mode EXE mimarisi yıkılır).

**Önerilen varsayılan (OKUMA B):** Mevcut 01 doc + bu doküman **user-mode** üzerine kuruludur.
Dolayısıyla OKUMA B, mevcut mimariyle **tutarlıdır** ve ek sürücü gerektirmez.

## 3.4 Sürüm Matrisi (BT Win32 — MSDN teyitli)

| Fonksiyon | XP SP2 | XP SP3 | Vista | Win7 | Win8 | Win8.1 | Win10 | Win11 |
|---|---|---|---|---|---|---|---|---|
| `BluetoothFindFirstRadio` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `BluetoothGetDeviceInfo` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `BluetoothGetRadioInfo` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| `BluetoothEnableDiscovery` | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| WinRT `BluetoothLEAdvertisementWatcher` | ✗ | ✗ | ✗ | ✗ | ✗ | ✗ | ✓ | ✓ |

> "Minimum supported client: Windows Vista, **Windows XP with SP2** [desktop apps only]"
> (bluetoothapis.h fonksiyonları). Win11 sütunu: Win10 ile aynı API seti devam eder —
> Win11'e özel BT API farkı **UNKNOWN** (kaynaklanmamıştır; 01 doc'deki Win11 WLAN
> eksikliği ile aynı neden: Win11 DLL/SDK doğrulaması yapılmamıştır).

## 3.5 Neva Connect Bağlantısı — BLE Beacon / Kontrol Kanalı

**Bağlam:** Mevcut neva-connect mimarisi **RPi5 + BLE** barındırır
(`E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md`, 01 doc §1 referansı).
RPi5 tarafı **BLE beacon** yayınlar; Windows tarafı (Neva Connect Windows dalı) bu beacon'ları
**dinleyip kontrol kanalı** olarak kullanır (multi-room senkronizasyon).

**Windows tarafı eşleşme:**

| Senaryo | Windows API | Faz |
|---|---|---|
| BLE beacon dinleme (RPi5'leri bulma) | WinRT `BluetoothLEAdvertisementWatcher` (Win10+) | F3 |
| Beacon filtreleme (manufacturer data + RSSI eşiği) | `BluetoothLEAdvertisementFilter` + `BluetoothSignalStrengthFilter` | F3 |
| Windows → RPi5 kontrol komutu | WinRT `BluetoothLEDevice` GATT (UUID **UNKNOWN** — neva-connect CLAUDE.md'de tanımlı mı? ⚠️ VERIFICATION REQUIRED) | F3 |
| Radio envanteri (kaç BT adaptörü var) | `BluetoothFindFirstRadio` + `BluetoothGetRadioInfo` | F3 |

**Gereksinim:** Bu senaryo **yalnız Windows 10+** çalışır (WinRT BLE Win10'da tanıtılmıştır).
XP/7/8.8.1 hedeflerinde BLE beacon dinleme **desteklenmez** → F3, XP/7'de yalnızca
Win32 radio envanteri (`BluetoothFindFirstRadio`) sağlar; BLE **kapsam dışı**.

> ⚠️ **VERIFICATION REQUIRED:** BLE GATT servis/characteristic UUID'leri neva-connect
> CLAUDE.md'de tanımlanıp tanımlanmadığı bu araştırmada **doğrulanamadı** (dosya
> `E:\Belgeler\` yolu erişim dışı). F3 kodlaması öncesi UUID'lerin vault'a taşınması gerekir.

---

# §4 USB Arabirimleri

## 4.1 SetupAPI — Cihaz Envanteri

**Doğrulanmış (Microsoft Learn):**

| API | Min. desteklenen istemci | Başlık | Kütüphane | DLL |
|---|---|---|---|---|
| `SetupDiGetClassDevsW` | **Microsoft Windows 2000 ve sonrası** | setupapi.h | SetupAPI.lib | SetupAPI.dll |
| `SetupDiGetDeviceRegistryPropertyW` | **Microsoft Windows 2000 ve sonrası** | setupapi.h | Setupapi.lib | Setupapi.dll |

> "Available in **Microsoft Windows 2000 and later versions of Windows**."
> — `SetupDiGetClassDevsW`

> "**API set:** ext-ms-win-setupapi-classinstallers-l1-1-0 (introduced in **Windows 8**)"
> — `SetupDiGetClassDevsW`

Kaynaklar:
- `https://learn.microsoft.com/en-us/windows/win32/api/setupapi/nf-setupapi-setupdigetclassdevsw`
- `https://learn.microsoft.com/en-us/windows/win32/api/setupapi/nf-setupapi-setupdigetdeviceregistrypropertyw`
(güvenilirlik: **yüksek**)

**SPDRP_* özellikleri (doğrulanmış):**

| Sabit | Ne getirir |
|---|---|
| `SPDRP_DEVICEDESC` | `REG_SZ` — cihaz tanımı |
| `SPDRP_HARDWAREID` | `REG_MULTI_SZ` — hardware ID listesi |
| `SPDRP_COMPATIBLEIDS` | `REG_MULTI_SZ` — uyumlu ID listesi |
| `SPDRP_SERVICE` | Sürücü servis adı |
| `SPDRP_CLASS` | Cihaz sınıfı |
| `SPDRP_CLASSGUID` | Sınıf GUID'ı |
| `SPDRP_MFG` | Üretici |

**Unified property model (Vista+):** `DEVPKEY_Device_DeviceDesc` ↔ `CM_DRP_DEVICEDESC` ↔
`SPDRP_DEVICEDESC`. Vista öncesi yalnız `SPDRP_`/`CM_DRP_` kullanılır.

> "In **Windows Vista and later** versions of Windows, the unified device property model
> supports the device instance properties that correspond to the `CM_DRP_Xxx` identifiers
> ... and the `SPDRP_Xxx` identifiers... Windows Server 2003, Windows XP, and Windows 2000
> also support these device properties. However, these earlier versions of Windows do not
> support the property keys of the unified device property model."
> — *Accessing device instance (SPDRP_XXX) properties*

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/install/accessing-device-instance-spdrp-xxx-properties`
(güvenilirlik: **yüksek**)

> ⚠️ **Önemli kısıt (Microsoft Learn):** "SetupApi is not supported on **all editions** of
> Windows. When possible, you should use lower layer APIs..."
> — *Using Device Installation Functions*
> Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/install/using-device-installation-functions`

**Neva Connect etkisi:** F4 (`NevaUsb.exe`) XP hedefinde SetupAPI'yi **kullanabilir**
(Windows 2000+), ancak Vista öncesi yalnız `SPDRP_*` yolunu kullanır; `DEVPKEY`
yolu **yalnız Vista+** derleme zamanı seçeneğidir (`GetProcAddress` ile).

## 4.2 WinUSB — `winusb.dll` / `WinUsb_Initialize`

**Doğrulanmış (Microsoft Learn):**

> "**WinUSB functions require Windows XP or later.** You can use these functions in your
> C/C++ application to communicate with your USB device. Microsoft does not provide a
> managed API for WinUSB."
> — *Access a USB Device by Using WinUSB Functions*

> "This information applies to **Windows 8.1, Windows 8, Windows 7, Windows Server 2008,
> Windows Vista** versions of Windows."

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/usbcon/using-winusb-api-to-communicate-with-a-usb-device`
(güvenilirlik: **yüksek**)

**`WinUsb_Initialize` (winusb.h):**

```cpp
BOOL WinUsb_Initialize(
  [in]  HANDLE                   DeviceHandle,
  [out] PWINUSB_INTERFACE_HANDLE InterfaceHandle
);
```

> "The handle to the device that `CreateFile` returned. **WinUSB uses overlapped I/O, so
> `FILE_FLAG_OVERLAPPED` must be specified** in the `dwFlagsAndAttributes` parameter of
> `CreateFile` call for DeviceHandle to have the characteristics necessary for
> `WinUsb_Initialize` to function properly."

Kaynak: `https://learn.microsoft.com/en-us/windows/win32/api/winusb/nf-winusb-winusb_initialize`
(güvenilirlik: **yüksek**)

**Çakışma notu (kaynaklar arası):** WinUSB fonksiyonlarının **"Windows XP veya sonrası"**
olduğu, ancak `WinUsb_Initialize` sayfasının "applies to Vista+" listesi verdiği
görülür. **XP + WinUSB kombinasyonu** (WinUSB XP'ye ayrı bir installer/XP DDK yoluyla
eklenir) bu araştırmada **kaynaklanmadı** → `⚠️ VERIFICATION REQUIRED`. F4, XP hedefinde
yalnız `SetupAPI` envanteri + `CreateFile` ile sınırlı kalır; `WinUsb_Initialize`
**Vista+** hedeflenir.

**Sürücü gerekmez:** WinUSB, `Winusb.sys`'i **kutu-içi** function driver olarak kullanır;
uygulamanın kendi sürücü yazması gerekmez (yalnız cihazın `Winusb.sys` bindirmesi
`INF` / `Microsoft OS Descriptors` ile tanımlanır).

> "Winusb.sys is installed as the device's function driver."
> — *Access a USB Device by Using WinUSB Functions*

## 4.3 IOCTL Katmanı — USB Hub Sorguları

**Doğrulanmış (Microsoft Learn — usbioctl.h):**

| IOCTL | Min. desteklenen istemci | Hedef |
|---|---|---|
| `IOCTL_USB_GET_NODE_CONNECTION_INFORMATION_EX` | **Windows XP, Windows Server 2003, ve sonrası** | USB hub device (`GUID_DEVINTERFACE_USB_HUB`) |
| `IOCTL_USB_GET_NODE_CONNECTION_INFORMATION_EX_V2` | **Windows 8** (+ Windows Server: none supported) | USB hub — protokol yetenekleri (`USB_PROTOCOLS`) |

> "The `IOCTL_USB_GET_NODE_CONNECTION_INFORMATION_EX` request retrieves information about
> a **USB port and the device that is attached** to the port, if there is one...
> This request **targets the USB hub device** (`GUID_DEVINTERFACE_USB_HUB`). **Do not send
> this request to the root hub.**"

Kaynaklar:
- `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/usbioctl/ni-usbioctl-ioctl_usb_get_node_connection_information_ex`
- `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/usbioctl/ns-usbioctl-_usb_protocols`
(güvenilirlik: **yüksek**)

**`USB_PROTOCOLS` union** (`IOCTL_..._EX_V2` ile): USB 3.0 SuperSpeed / 2.0 / 1.1
protokol bayrakları döner; **min Windows 8**.

## 4.4 Neden Gerekli — WiFi/BT Dongle + DSP Donanım Bağlantısı

**Bağlam (Neva Connect):**

| Kullanım | IOCTL/API | Neden |
|---|---|---|
| WiFi/BT dongle envanteri | `SetupDiGetClassDevs` + `SPDRP_HARDWAREID` | Hangi USB dongle'ın bağlı olduğunu tespit (§2-§3 öncesi keşif) |
| USB port topolojisi | `IOCTL_USB_GET_NODE_CONNECTION_INFORMATION_EX` | Dongle hangi hub/portta? (güç/enerji yönetimi) |
| DSP donanım bağlantısı | `SetupDiGetClassDevs` (GUID_DEVINTERFACE_USB_DEVICE) + `WinUsb_Initialize` | Neva Engine **PCM5122** DAC'ının USB üzerinden köprüsü (I2S yerine USB Audio Class 2.0 / vendor-specific) |

> **PCM5122 uyarısı (CoreMusic §17 #4):** Neva Engine'de **PCM3168A** ADR-038 ile
> seçilmiştir; **PCM5122 eski/alternatif** bir çiptir. Bu dokümanda PCM5122 yalnız
> **neva-connect mevcut donanım bağlamı** olarak anılır; yeni tasarım PCM3168A'ya
> uyacaktır. USB köprüsü her iki DAC için de aynı `SetupAPI`/IOCTL katmanını kullanır.

> ⚠️ **VERIFICATION REQUIRED:** PCM5122'nin USB üzerinden mi yoksa doğrudan I2S üzerinden
> mi bağlandığı neva-connect CLAUDE.md'de **doğrulanamadı**. F4 tasarımı bu noktada
> **USB Audio Class 2.0** (varsayım) vs **I2S-over-USB** (vendor) ayrımını netleştirmelidir.

---

# §5 SATA / mSATA / M.2 / NVMe Depolama

## 5.1 Bus Tipi Tespiti — `IOCTL_STORAGE_QUERY_PROPERTY` + `STORAGE_BUS_TYPE`

**Doğrulanmış (Microsoft Learn — `STORAGE_BUS_TYPE` enum, winioctl.h):**

```c
typedef enum _STORAGE_BUS_TYPE {
  BusTypeUnknown         = 0x00,
  BusTypeScsi,        // 0x01
  BusTypeAtapi,       // 0x02
  BusTypeAta,         // 0x03
  BusType1394,        // 0x04
  BusTypeSsa,         // 0x05
  BusTypeFibre,       // 0x06
  BusTypeUsb,         // 0x07
  BusTypeRAID,        // 0x08
  BusTypeiScsi,       // 0x09
  BusTypeSas,         // 0x0A
  BusTypeSata,        // 0x0B
  BusTypeSd,          // 0x0C
  BusTypeMmc,         // 0x0D
  BusTypeVirtual,     // 0x0E
  BusTypeFileBackedVirtual, // 0x0F
  BusTypeSpaces,      // 0x10
  BusTypeNvme,        // 0x11
  BusTypeSCM,         // 0x12
  BusTypeUfs,         // 0x13
  BusTypeNvmeof,      // 0x14
  BusTypeMax,         // BusTypeMaxReserved = 0x7F
  BusTypeMaxReserved  = 0x7F
} STORAGE_BUS_TYPE;
```

> `BusTypeSata` — "SATA bus. **Windows Server 2003: This is not supported before Windows
> Server 2003 with SP1.**"
> `BusTypeNvme` — "Indicates an NVM Express (NVMe) bus."

Kaynak: `https://learn.microsoft.com/en-us/windows/win32/api/winioctl/ne-winioctl-storage_bus_type`
(güvenilirlik: **yüksek**)

> **⚠️ HEX DÜZELTMESİ (görev direktifi ile kaynak çelişkisi):**
> Görev direktifi `BusTypeSata = 0x0A` ve `BusTypeNvme = 0x11` yazıyordu.
> Enum sıralamasından (`BusTypeUnknown = 0x00` + ardışık):
> - **`BusTypeSata = 0x0B` (11)** — direktifteki `0x0A` **YANLIŞTIR**; `0x0A = BusTypeSas`'tır.
> - **`BusTypeNvme = 0x11` (17)** — direktifle **UYUMLUDUR** ✓.
>
> Doğrulama: `BusTypeUnknown=0x00` → Scsi 01, Atapi 02, Ata 03, 1394 04, Ssa 05,
> Fibre 06, Usb 07, RAID 08, iScsi 09, **Sas 0A**, **Sata 0B**, Sd 0C, Mmc 0D,
> Virtual 0E, FileBackedVirtual 0F, Spaces 10, **Nvme 11**.
>
> ⚠️ **VERIFICATION REQUIRED:** Sabit değerler derlenmiş SDK'da (`winioctl.h`)
> **teyit edilmelidir** (enum sırası zamanla değişmişse fark çıkar).

## 5.2 Cihaz Numarası — `IOCTL_STORAGE_GET_DEVICE_NUMBER`

**Durum:** `IOCTL_STORAGE_GET_DEVICE_NUMBER` (winioctl.h) — disk/partition/lun
eşleştirmesi için standart IOCTL. Bu araştırmada **özel bir MSDN requirement sayfası
çekilemedi** → fonksiyonun **varlığı** winioctl.h üzerinden **bilinmektedir**, ancak
**minimum desteklenen sürüm** bu dokümanda **UNKNOWN**.

> ⚠️ **VERIFICATION REQUIRED:** `IOCTL_STORAGE_GET_DEVICE_NUMBER` min OS'ü bu dokümanda
> **UNKNOWN**'dur. Uygulama katmanında `IOCTL_STORAGE_QUERY_PROPERTY` ile **kombine**
> kullanılır (bus tipi + cihaz numarası eşleşmesi).

**Kullanım:** `STORAGE_DEVICE_NUMBER` yapısı { `DeviceType`, `DeviceNumber`, `PartitionNumber` }
— disk numarasını `\\.\PhysicalDriveN` handle'ı ile eşler. Böylece F5, hangi fiziksel
diskin hangi bus'ta (SATA/NVMe/USB) olduğunu raporlar.

## 5.3 NVMe Passthrough — Windows 8.1+ / Windows 10

**Doğrulanmış (Microsoft Learn — "Working with NVMe Drives"):**

> "Device access is enabled via **StorNVMe.sys**, the in-box driver first introduced in
> **Windows Server 2012 R2 and Windows 8.1**. It's also available to Windows 7 devices
> through a **KB hot fix**. In Windows 10, several new features were introduced, including
> a **pass-through mechanism** for vendor-specific NVMe commands and updates to existing
> IOCTLs."

Kaynak: `https://learn.microsoft.com/en-us/windows/win32/fileio/working-with-nvme-devices`
(güvenilirlik: **yüksek**)

**IOCTL / Property ID matrisi (aynı kaynak):**

| IOCTL | Yapı | Amaç | Min OS |
|---|---|---|---|
| `IOCTL_STORAGE_QUERY_PROPERTY` | `STORAGE_PROPERTY_QUERY` | Cihaz bilgisi (Identify, sıcaklık) | Windows 8.1+ (StorNVMe) |
| `IOCTL_STORAGE_PROTOCOL_COMMAND` | `STORAGE_PROTOCOL_COMMAND` | **NVMe passthrough** — vendor-specific komutlar | **Windows 10** |

**`STORAGE_PROPERTY_ID` yeni değerleri:**

> "StorageAdapterProtocolSpecificProperty: If `ProtocolType` = `ProtocolTypeNvme` and
> `DataType` = `NVMeDataTypeLogPage`, callers should request **512 byte chunks** of data.
> StorageDeviceProtocolSpecificProperty — Use one of these protocol-specific property IDs
> in combination with `STORAGE_PROTOCOL_SPECIFIC_DATA` to retrieve protocol-specific data
> in the `STORAGE_PROTOCOL_DATA_DESCRIPTOR` structure.
> StorageAdapterTemperatureProperty / StorageDeviceTemperatureProperty"

**`STORAGE_PROTOCOL_NVME_DATA_TYPE` değerleri:**

> "Use **NVMeDataTypeIdentify** to get Identify Controller data or Identify Namespace data.
> Use **NVMeDataTypeLogPage** to get log pages (including **SMART/health data**).
> Use **NVMeDataTypeFeature** to get features of the NVMe drive."

**Passthrough kısıtı (aynı kaynak):**

> "This pass-through mechanism **requires use of the Command Effects Log**. Moreover,
> StorNVMe.sys requires **all commands, not just pass-through commands**, to be described
> in the Command Effects Log."

> "Finally, this pass-through IOCTL is intended for **vendor-specific commands**. To send
> other admin or non-vendor specific NVMe commands such as Identify, **this pass-through
> IOCTL should not be used**. For example, **IOCTL_STORAGE_QUERY_PROPERTY should be used**
> for Identify or..."

> ⚠️ **İSİM DÜZELTMESİ:** Görev direktifinde `NVMeGetLogPage` adı geçiyordu; MSDN'de
> bu adla bir IOCTL/yapı **YOKTUR**. Doğru tanım **`NVMeDataTypeLogPage`**
> (`STORAGE_PROTOCOL_NVME_DATA_TYPE` enum değeri) ve `IOCTL_STORAGE_QUERY_PROPERTY`
> üzerinden sorgulanır. `NVMeGetLogPage` uydurulmamış, **düzeltme** olarak kaydedilmiştir.

**Sürüm notu (özet):**

| Özellik | Min OS |
|---|---|
| StorNVMe.sys (in-box NVMe sürücüsü) | **Windows 8.1** / Server 2012 R2 |
| NVMe Identify/SMART via `IOCTL_STORAGE_QUERY_PROPERTY` | Windows 8.1+ |
| Vendor-specific NVMe passthrough (`IOCTL_STORAGE_PROTOCOL_COMMAND`) | **Windows 10** |
| NVMe sıcaklık (`STORAGE_TEMPERATURE_DATA_DESCRIPTOR`) | Windows 10 |

## 5.4 SCSI Passthrough — Eski Yol (IOCTL_SCSI_PASS_THROUGH)

**Doğrulanmış (Microsoft Learn — ntddscsi.h):**

> "Allows an application to send almost any SCSI command to a target device, with the
> following restrictions:
> - If a class driver for the target type of device exists, the request must be sent to
>   that class driver... **if an application bypasses a class driver that has claimed a
>   device and sends a pass-through request for that device directly to the port driver,
>   a conflict for control of the device can occur**... If a pass-through request is sent
>   to an adapter device object and if it originates from user mode and targets an LU that
>   is claimed by a class driver, **Storport fails the request with
>   STATUS_INVALID_DEVICE_REQUEST**.
> - This request **cannot be used** if the CDB might require the underlying miniport driver
>   to **access memory directly**. If the caller's CDB might require direct access to memory,
>   use **IOCTL_SCSI_PASS_THROUGH_DIRECT** instead.
> - **Applications must not attempt to send a pass-through request asynchronously.** All
>   pass-through requests must be **synchronous**.
> - Applications **do not require administrative privileges** to send a pass-through
>   request to a device, but they **must have read/write access** to the device."

Kaynak: `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/ntddscsi/ni-ntddscsi-ioctl_scsi_pass_through`
(güvenilirlik: **yüksek**)

> "IOCTL_SCSI_PASS_THROUGH is a **buffered** device control request. To bypass buffering
> in system memory, callers should use **IOCTL_SCSI_PASS_THROUGH_DIRECT**."

**Neva Connect etkisi (F5):** SCSI passthrough, SATA diskler için **eski bir yoldur**;
modern Windows'ta (8.1+) tercih edilen yol `IOCTL_STORAGE_QUERY_PROPERTY`'dir.
`IOCTL_SCSI_PASS_THROUGH`, yalnız **XP/7** hedeflerinde (StorNVMe yokken) geri-düşüş
olarak değerlendirilir. M.2 ve mSATA, bus tipi olarak **SATA** veya **NVMe** olarak
raporlanır (`STORAGE_BUS_TYPE`) — form faktörü IOCTL ile **determine edilemez**
(`STORAGE_DEVICE_DESCRIPTOR` yalnız `BusType` verir).

## 5.5 Kullanım Senaryosu — Depolama Teşhis Aracı

**Senaryo (Neva Connect depolama köprüsü):**

| Katman | Platform | Rol |
|---|---|---|
| **RPi5 (yerel depolama)** | Linux | Ana depolama köprüsü — müzik arşivi, multi-room cache (mevcut neva-connect) |
| **Windows dalı (NevaDisk.exe)** | Windows | **Opsiyonel** disk teşhisi — envanter (bus tipi, model, SMART) |

**F5 çıktı formatı (öneri):**

```json
{
  "device_number": 0,
  "bus_type": "BusTypeNvme",
  "bus_type_hex": "0x11",
  "model": "...",
  "serial": "...",
  "nvme_identify": { "firmware": "...", "temperature_c": 41 },
  "smart_health": "OK"
}
```

> **Kapsam notu:** F5, Neva Connect'in **Windows dalında disk teşhisi opsiyoneldir**.
> RPi5'teki yerel depolama köprüsü bu EXE'ye **bağımlı değildir**. F5, yalnız Windows
> tarafında donanım envanteri/teşhisi sağlar.

---

# §6 OS × Özellik Matrisi

**Gösterim:** `✓` = kaynaklı destek · `✗` = kaynaklı olmayan/eksik · `UNKNOWN` =
bu araştırmada doğrulanamayan · `⚠️` = kısmi/not

| OS | WLAN-temel | HostedNetwork | WFD (Wi-Fi Direct) | WiFi6-ax | WiFi7-be | BT-Win32 | BT-WinRT | WinUSB | NVMe-passthrough |
|---|---|---|---|---|---|---|---|---|---|
| **XP SP3** | ✓ ¹ | ✗ ² | ✗ ³ | ✗ ⁴ | ✗ ⁴ | ✓ ⁵ | ✗ ⁶ | ⚠️ ⁷ | ✗ ⁸ |
| **Win7** | ✓ ¹ | ✓ ⁹ | ✗ ³ | ✗ ⁴ | ✗ ⁴ | ✓ ⁵ | ✗ ⁶ | ✓ ¹⁰ | ⚠️ ¹¹ |
| **Win8** | ✓ ¹ | ✓ ⁹ | ✓ ¹² | ✗ ⁴ | ✗ ⁴ | ✓ ⁵ | ✗ ⁶ | ✓ ¹⁰ | ✗ ¹³ |
| **Win8.1** | ✓ ¹ | ✓ ⁹ | ✓ ¹² | ✗ ⁴ | ✗ ⁴ | ✓ ⁵ | ✗ ⁶ | ✓ ¹⁰ | ✓ ¹⁴ |
| **Win10** | ✓ ¹ | ✓ ¹⁵ | ✓ ¹² | ✓ ¹⁶ | ✗ ⁴ | ✓ ⁵ | ✓ ¹⁷ | ✓ ¹⁰ | ✓ ¹⁸ |
| **Win11** | ✓ ¹⁹ | UNKNOWN | UNKNOWN | ✓ ¹⁶ | ✓ ²⁰ | UNKNOWN | UNKNOWN | UNKNOWN | UNKNOWN |

**Ayak notları (kaynaklı):**

1. **WLAN-temel** — `WlanEnumInterfaces` / `WlanGetAvailableNetworkList` / `WlanConnect`:
   "Minimum supported client: **Windows Vista, Windows XP with SP3** [desktop apps only]" +
   "Redistributable: **Wireless LAN API for Windows XP with SP2**".
   `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanenuminterfaces`
   · `.../wlangetavailablenetworklist` · `.../wlanconnect`
2. **HostedNetwork @ XP** — `WlanHostedNetwork*` "added to support the wireless Hosted Network
   on **Windows 7** and Windows Server 2008 R2". XP'de yok → 01 doc export matrisi: XP **36**
   export, HostedNetwork **yok** (Win7 +12 = HostedNetwork x11).
   `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanhostednetworkstartusing`
3. **WFD @ XP/7** — "The WFDOpenHandle function is part of **Wi-Fi Direct, a new feature in
   Windows 8** and Windows Server 2012." Min: **Windows 8**.
   `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wfdopenhandle`
4. **WiFi6-ax / WiFi7-be @ XP/7/8/8.1** — Microsoft Support yalnızca Windows 10 ve 11 için
   802.11ax/802.11be destekler; daha eski OS'lerde desteklenmez (kaynak: Microsoft Support
   "Faster and more secure Wi-Fi in Windows" — Win10 ve Win11 sürümleri).
5. **BT-Win32** — `BluetoothFindFirstRadio` / `BluetoothGetDeviceInfo` /
   `BluetoothGetRadioInfo`: "Minimum supported client: **Windows Vista, Windows XP with SP2**".
6. **BT-WinRT** — `BluetoothLEAdvertisementWatcher`: "Device family: **Windows 10**
   (introduced in **10.0.10240.0**)". `https://learn.microsoft.com/en-us/uwp/api/windows.devices.bluetooth.advertisement.bluetoothleadvertisementwatcher`
7. **WinUSB @ XP** — "WinUSB functions require **Windows XP or later**" ancak
   `WinUsb_Initialize` "applies to Vista+" listesi verir → **⚠️ VERIFICATION REQUIRED**
   (XP + WinUSB kombinasyonu kaynaklanmadı).
8. **NVMe @ XP** — StorNVMe Windows 8.1+'da tanıtıldı; XP'de NVMe passthrough **yok**.
9. **HostedNetwork @ Win7/8/8.1** — Windows 7+ (kaynak: WlanHostedNetworkStartUsing).
   **⚠️** 8.1 ve sonrası için API **deprecated** olabilir (kaynaklanmadı → `UNKNOWN`),
   ancak 01 doc export matrisi Win8 (103) / Win8.1 (108) sürümlerinde `WlanHostedNetwork*`
   export'larının **varlığını** doğrular (export silinmiyor).
10. **WinUSB @ Win7/8/8.1** — "WinUSB functions require Windows XP or later" +
    "applies to Windows 8.1, 8, 7, Server 2008, Vista".
11. **NVMe @ Win7** — "It's also available to Windows 7 devices through a **KB hot fix**"
    → in-box değil, **⚠️** opsiyonel (KB gerekir).
12. **WFD @ Win8+** — WFDOpenHandle min: Windows 8 (kaynak: nf-wlanapi-wfdopenhandle).
13. **NVMe @ Win8** — StorNVMe **8.1**'de tanıtıldı → Win8'de **✗** (kaynak: "first
    introduced in Windows Server 2012 R2 and Windows 8.1").
14. **NVMe @ Win8.1** — StorNVMe in-box + `IOCTL_STORAGE_QUERY_PROPERTY` protocol-specific.
15. **HostedNetwork @ Win10** — 01 doc export matrisi (Win10 19041 = 148 export). API
    **deprecated** edilmiş olabilir; Microsoft Win10'da `WlanHostedNetwork*` yerine
    **WinRT Wi-Fi Direct GO**'yu önerir (`https://learn.microsoft.com/en-us/windows-hardware/drivers/partnerapps/wi-fi-direct`:
    "replace the deprecated `WlanHostedNetwork*` APIs with the new WinRT APIs").
16. **WiFi6-ax @ Win10/11** — Microsoft Support: "This article explains how **Windows 10**
    supports Wi-Fi 6 and WPA3..." · Win11: "Windows 11 supports... Wi-Fi 6, Wi-Fi 7, and WPA3".
17. **BT-WinRT @ Win10** — Windows 10 10.0.10240.0+ (kaynak: UniversalApiContract v1.0).
18. **NVMe passthrough @ Win10** — "In **Windows 10**, several new features were introduced,
    including a **pass-through mechanism** for vendor-specific NVMe commands" +
    `IOCTL_STORAGE_PROTOCOL_COMMAND` "introduced in Windows 10".
19. **WLAN-temel @ Win11** — Win11, Native Wifi API'yi (wlanapi.dll) devam ettirir.
    **⚠️** Win11 wlanapi.dll'inin export matrisi **UNKNOWN** (01 doc: pconlife OS listesinde
    Win11 yok → kaynak eksik).
20. **WiFi7-be @ Win11** — "Note: Wi-Fi 7 is available starting with **Windows 11, version 24H2**."
    (Microsoft Support). 24H2 öncesi Win11 sürümlerinde (21H2/22H2) **✗**.

> **Matris bütünlüğü kuralı:** Bilinmeyen her hücre `UNKNOWN` yazmıştır; uydurma `✓`
> veya `✗` **yoktur**. Win11 sütunundaki `UNKNOWN` hücrelerin çoğu, 01 doc'deki Win11
> WLAN DLL eksikliği ile aynı kök nedenedir (Win11 sürüm doğrulaması yapılmamıştır).

---

# §7 Faz Planı (Uygulama Katmanı — Sürücü YOK)

> **KAPSAM SINIRI (tekrar):** Bu faz planı **yalnız user-mode uygulama katmanı** içindir.
> **Sürücü yazımı (WiFiCx / WDK / KMDF / NDIS) KAPSAM DIŞIDIR.** Aşağıdaki her faz,
> herhangi bir sürücü kurulumu **olmadan** çalışır — Windows'un kutu-içi sürücüleri
> (wlanapi.dll, bthprops.cpl, winusb.dll, storahci/stornvme) kullanılır.

## F1 — WLAN Native Wifi (`NevaWlan.exe`)

**Kapsam:** `wifi list` (ağ tarama), `wifi connect` (profil ile bağlanma), `wifi status`
(bağlantı durumu), `wifi profile` (profil yönetimi).

| Öğe | Değer |
|---|---|
| **Bağımsız EXE** | `NevaWlan.exe` |
| **API** | `wlanapi.dll` (`WlanOpenHandle`, `WlanEnumInterfaces`, `WlanGetAvailableNetworkList`, `WlanConnect`, `WlanGetProfile`, `WlanSetProfile`) |
| **Dinamik yükleme** | 01 doc dynload-resolver (`GetProcAddress`) — tek derleme XP→Win11 |
| **Sürücü gerekmez?** | **Evet** — user-mode; kutu-içi wlanapi.dll kullanılır |
| **Belge** | 01 doc (export matrisi) + 03 doc (**beklenen** — bu yazım anında vault'ta YOK) |
| **Min OS** | XP SP3 (Wireless LAN API SP3'te yerleşik; SP2'de redistributable) |
| **Hedef** | Multi-room senkronizasyonda Windows'un ağ bağlamını raporlamak |

**Kısıtlar:** XP SP3'te `WLAN_CONNECTION_PARAMETERS` kısıtları geçerlidir
(kaynak: WlanConnect — "There are some constraints on the WLAN_CONNECTION_parameters
members... structures that are valid for Windows Server 2008 and Windows Vista may not be
valid for Windows XP with SP3"). Profile şeması kısıtları: `https://learn.microsoft.com/en-us/windows/win32/nativewifi/wireless-profile-compatibility`

## F2 — WiFi 7 Raporlama (`NevaWifi7.exe`)

**Kapsam:** 802.11be (EHT) PHY tespiti, MLO AP raporlama, WPA3 doğrulama.

| Öğe | Değer |
|---|---|
| **Bağımsız EXE** | `NevaWifi7.exe` |
| **API** | Native Wifi (`DOT11_PHY_TYPE` — `dot11_phy_type_eht = 11`) |
| **Doğrulama** | `netsh wlan show drivers` çıktısı ile cross-check (opsiyonel) |
| **Sürücü gerekmez?** | **Evet** — user-mode |
| **Min OS** | **Win11 24H2** (WiFi 7 için); EHT PHY raporlaması OS + adaptöre bağlı |
| **Bağımlılık** | F1'e bağımsız (aynı `wlanapi.dll` kullanır, EXE ayrı) |

**Kısıt:** "Wi-Fi 7 (Enterprise) is not currently supported in Windows 11" → 802.1X/RADIUS
senaryoları **kapsam dışı** (§2.4).

**⚠️ Uygulama katmanı kısıtı:** MLO/MLD LOW-LEVEL ayrıştırma **yapılamaz** (sürücü işi);
yalnızca OS'in raporladığı PHY türü + BSS listesi okunur (§2.7).

## F3 — Bluetooth (`NevaBt.exe`)

**Kapsam:** Radio/cihaz envanteri (Win32) + BLE beacon dinleme (WinRT, Win10+).

| Öğe | Değer |
|---|---|
| **Bağımsız EXE** | `NevaBt.exe` |
| **Win32 API** | `bthprops.cpl` — `BluetoothFindFirstRadio`, `BluetoothGetRadioInfo`, `BluetoothGetDeviceInfo` |
| **WinRT API** | `Windows.Devices.Bluetooth.Advertisement` — `BluetoothLEAdvertisementWatcher` |
| **Sürücü gerekmez?** | **Evet (OKUMA B)** — user-mode; kutu-içi MS Bluetooth yığını (Bth.inf) kullanılır |
| **Min OS** | Win32: XP SP2+ · WinRT BLE: **Win10+** |
| **Bağımlılık** | F1'den bağımsız |

**Senaryo:** RPi5 BLE beacon'larını dinle (multi-room discovery) → kontrol kanalı.
**⚠️** GATT UUID'leri doğrulanamadı (§3.5).

**⚠️ Direktif riski (§3.3):** "Tüm BT sürücüleri değiştirilecek" OKUMA A ise (ISV yığını),
bu faz **user-mode ile karşılanamaz** ve sürücü yazımı gerekir → kapsam değişikliği + ADR.

## F4 — USB Arabirimleri (`NevaUsb.exe`)

**Kapsam:** Dongle/DAC envanteri + USB topolojisi + WinUSB veri yolu.

| Öğe | Değer |
|---|---|
| **Bağımsız EXE** | `NevaUsb.exe` |
| **API** | `setupapi.dll` (`SetupDiGetClassDevs`, `SPDRP_*`) + `winusb.dll` (`WinUsb_Initialize`) |
| **IOCTL** | `IOCTL_USB_GET_NODE_CONNECTION_INFORMATION_EX` (+ V2 — Win8+) |
| **Sürücü gerekmez?** | **Evet** — `Winusb.sys` kutu-içi function driver; uygulama yalnızca cihazın `Winusb.sys` bindirmesini bekler |
| **Min OS** | SetupAPI: 2000+ · IOCTL_EX: XP+ · IOCTL_EX_V2: Win8+ · WinUSB: Vista+ (XP ⚠️) |
| **Bağımlılık** | F1-F3'ten bağımsız |

**Kullanım:** WiFi/BT dongle tespiti (F1/F3 öncesi) + DSP donanım bağlantısı (PCM5122/PCM3168A köprüsü — §4.4).

## F5 — SATA / mSATA / M.2 / NVMe (`NevaDisk.exe`)

**Kapsam:** Depolama cihazı envanteri/teşhisi — bus tipi, model, SMART/sağlık.

| Öğe | Değer |
|---|---|
| **Bağımsız EXE** | `NevaDisk.exe` |
| **IOCTL** | `IOCTL_STORAGE_QUERY_PROPERTY` (StorageDeviceProperty + StorageDeviceProtocolSpecificProperty) · `IOCTL_STORAGE_GET_DEVICE_NUMBER` · `IOCTL_STORAGE_PROTOCOL_COMMAND` (Win10+, vendor passthrough) |
| **Yapılar** | `STORAGE_BUS_TYPE` (Sata 0x0B, Nvme 0x11) · `STORAGE_PROTOCOL_SPECIFIC_DATA` · `STORAGE_TEMPERATURE_DATA_DESCRIPTOR` |
| **Eski yol** | `IOCTL_SCSI_PASS_THROUGH` (XP/7 geri-düşüş) |
| **Sürücü gerekmez?** | **Evet** — kutu-içi `storahci.sys` / `stornvme.sys` kullanılır |
| **Min OS** | Bus tipi: XP+ · NVMe Identify/SMART: **Win8.1+** · NVMe vendor passthrough: **Win10+** |
| **Bağımlılık** | Diğer fazlardan bağımsız |

**Kapsam notu:** **Opsiyonel** — RPi5 yerel depolama köprüsü buna bağımlı değildir (§5.5).

## 7.x Sürücü Yazımının Kapsam Dışı Olmasının Gerekçesi

**Tartışma:** Kullanıcı direktifindeki "sürücüleri değiştirecek" ifadesi, **user-mode API
ile karşılanabilir mi?**

| Senaryo | User-mode ile karşılanır mı? | Kanıt |
|---|---|---|
| WiFi/BT adaptörünün mevcut sürücüsünü **okuma/raporlama** | **EVET** — wlanapi.dll / bluetoothapis.h yığını **değişmez**, yalnızca sorgulanır | §2.2 (`netsh wlan show drivers`), §3.1 (radio bilgisi) |
| WiFi/BT sürücüsünü **değiştirme** (kendi yığını kurma) | **HAYIR** — INF + WLK/HLK sertifikası + farklı class GUID gerekir | §3.3 OKUMA A, §3.2 ("If you implement your own Bluetooth stack, you must create a vendor-specific class GUID") |
| WiFiCx tabanlı **yeni** sürücü yazma | **HAYIR** — WDK/KMDF/NetAdapterCx gerekir (kapsam dışı, §2.7-§2.8) | §2.8 (WiFiCx sürüm tablosu) |

> ⚠️ **VERIFICATION REQUIRED / §8 madde #2:** "Sürücüleri değiştirecek" direktifinin
> **OKUMA B** (user-mode raporlama/soyutlama) ile karşılanıp karşılanamayacağı **kullanıcı
> onayına bağlıdır**. OKUMA B seçiliyorsa, bu dokümanın §7 faz planı **geçerlidir** ve
> **hiçbir sürücü yazımı gerekmez**. OKUMA A seçiliyorsa, sürücü yazımı **ayrı bir ADR +
> bütçe + kapsamlı bir WDK projesi** gerektirir ve bu doküman **yeniden yazılmalıdır**.

---

# §8 Açık Konular (Kullanıcıya Sorulacaklar)

| # | Madde | Neden açık | Etkilediği faz |
|---|---|---|---|
| **1** | **"Tüm Bluetooth sürücüleri değiştirilecek" direktifinin okuması:** OKUMA A (ISV yığın kurulumu — sürücü yazımı gerekir) mı, OKUMA B (user-mode uygulama katmanı soyutlaması — mevcut yığın korunur) mı? | Direktif belirsiz; OKUMA A mevcut user-mode mimariyi yıkardı. §3.3'te iki okuma da kaynaklı olarak tartışıldı. | **F3** (kritik) |
| **2** | **"Sürücü yazımı" kapsamın mı?** OKUMA B seçilse bile, WiFiCx/WDK tabanlı **herhangi bir** sürücü yazımı kapsamda mı? | §7.x'te gerekçelendirildi: OKUMA B, user-mode ile karşılanır; OKUMA A, sürücü yazar. | F1-F5 (tümü) |
| **3** | **`BusTypeSata` hex düzeltmesi:** direktifte `0x0A` yazıyordu; enum sıralamasından **`0x0B`** çıkıyor (`0x0A = BusTypeSas`). Derlenmiş SDK'da teyit edilecek mi? | §5.1'de kaynaklı düzeltme. Yanlış hex kullanılırsa bus tipi yanlış raporlanır. | F5 |
| **4** | **`NVMeGetLogPage` isim düzeltmesi:** MSDN'de bu ad yok; doğru tanım `NVMeDataTypeLogPage` + `IOCTL_STORAGE_QUERY_PROPERTY`. Kabul? | §5.3'te düzeltildi. | F5 |
| **5** | **`wlanSignalValueToBarEx` min OS:** sembol export'ta var, ama MSDN sayfası çekilemedi → min OS **UNKNOWN**. SDK header'dan teyit? | §2.6. | F2 |
| **6** | **`BluetoothGetHostedRadioInfo` varlığı:** 2026-10-15 `bluetoothapis.h` listesinde **GÖRÜNMÜYOR**. Var mı, min OS ne? | §3.1.2. Yoksa `BluetoothGetRadioInfo` kullanılır. | F3 |
| **7** | **Win11 wlanapi.dll export matrisi:** 01 doc'de Win11 kaynağı **UNKNOWN** (pconlife'da Win11 yok). Win11 hedefleniyor mu? | §6 matrisi Win11 sütununda birçok `UNKNOWN`. | F1, F2 |
| **8** | **Win11 BT/USB/NVMe OS matrisi:** Win11 sütunu için bu araştırmada doğrulama yapılmadı (yalnız WLAN odaklı). Genişletilecek mi? | §6 matrisi. | F3, F4, F5 |
| **9** | **BLE GATT UUID'leri:** neva-connect CLAUDE.md'de RPi5 BLE beacon/GATT UUID'leri tanımlı mı? | §3.5 — dosya `E:\Belgeler\` yolu erişim dışı. | F3 |
| **10** | **PCM5122 bağlantı yolu:** USB Audio Class 2.0 (varsayım) mı, I2S-over-USB (vendor) mı? | §4.4 — neva-connect CLAUDE.md doğrulanamadı. Ayrıca §17 #4: PCM3168A tercih edilmeli. | F4 |
| **11** | **XP hedef aralığı (BT/USB/depolama):** 01 doc XP SP3 hedefliyor; ancak BT WinRT (✗), WinUSB (⚠️), NVMe (✗) XP'de kısıtlı. F3/F4/F5 XP'de **desteklenecek mi?** | §6 matrisinde XP sütunu kısıtlı. | F3, F4, F5 |
| **12** | **`02` doc (çoklu-exe mimarisi):** §1'de referans verildi ancak bu yazım anında vault'ta **YOK**. Oluşturulacak mı? | §1 — mimari çerçeveyi tanımlayacak. | Tüm fazlar |

---

# §9 Kaynaklar (Tüm URL'ler)

> **Güvenilirlik notu:** Aşağıdaki kaynakların tamamı **resmi Microsoft** (learn.microsoft.com /
> support.microsoft.com) veya **birincil DLL envanteri** dir. Üçüncü taraf yalnız iki madde
> (DLL export teyidi) için kullanılmıştır ve güvenilirliği **orta** olarak işaretlenmiştir.

## Microsoft Support (güvenilirlik: yüksek)

| # | Kaynak | URL | Kullanıldığı bölüm |
|---|---|---|---|
| S1 | Faster and more secure Wi-Fi in Windows | `https://support.microsoft.com/en-us/topic/26177a28-38ed-1a8e-7eca-66f24dc63f09` | §2.1, §2.2, §2.3, §2.4 |
| S2 | Faster and more secure Wi-Fi in Windows (experience) | `https://support.microsoft.com/en-us/windows/experience/connectivity-networking/faster-and-more-secure-wi-fi-in-windows` | §2.2, §2.3 |
| S3 | Windows 11, version 24H2 update history | `https://support.microsoft.com/en-us/servicing/os/windows-11/2024/09/windows-11-version-24h2-update-history` | §2.1 |
| S4 | Windows 11 24H2 — What's new for IT pros | `https://learn.microsoft.com/en-us/windows/whats-new/whats-new-windows-11-version-24h2` | §2.1 |

## Microsoft Learn — Windows Drivers / NetAdapterCx (güvenilirlik: yüksek)

| # | Kaynak | URL | Kullanıldığı bölüm |
|---|---|---|---|
| D1 | WiFiCx Wi-Fi 7 | `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/wificx-wi-fi-7` | §2.7 |
| D2 | WifiDeviceSetStationCapabilities (wificx.h) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/wificx/nf-wificx-wifidesvicesetstationcapabilities` | §2.7 |
| D3 | Building a WiFiCx driver for multiple Windows versions | `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/building-a-wificx-driver-for-multiple-versions-of-windows` | §2.8 |
| D4 | NetAdapterCx version overview | `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/netadaptercx-version-overview` | §2.8 |
| D5 | WDI_TLV_LINK_QUALITY_BAR_MAP | `https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/wdi-tlv-link-quality-bar-map` | §2.6 (ilgili) |
| D6 | Considerations and requirements for Wi-Fi device manufacturers (IHV) | `https://learn.microsoft.com/en-us/windows-hardware/design/device-experiences/wireless-projection-wifi-device-manufacturers` | §2.9 (2x2 anten) |
| D7 | Wi-Fi Direct (Windows drivers) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/partnerapps/wi-fi-direct` | §6 (nota 15) |
| D8 | Bluetooth Host Radio Support | `https://learn.microsoft.com/en-us/windows-hardware/drivers/bluetooth/bluetooth-host-radio-support` | §3.2, §3.3 |
| D9 | Bluetooth IOCTLs | `https://learn.microsoft.com/en-us/windows-hardware/drivers/bluetooth/bluetooth-ioctls2` | §3.3 |
| D10 | Accessing device instance (SPDRP_XXX) properties | `https://learn.microsoft.com/en-us/windows-hardware/drivers/install/accessing-device-instance-spdrp-xxx-properties` | §4.1 |
| D11 | Using Device Installation Functions | `https://learn.microsoft.com/en-us/windows-hardware/drivers/install/using-device-installation-functions` | §4.1 |
| D12 | Access a USB Device by Using WinUSB Functions | `https://learn.microsoft.com/en-us/windows-hardware/drivers/usbcon/using-winusb-api-to-communicate-with-a-usb-device` | §4.2 |
| D13 | Windows desktop app for a USB device | `https://learn.microsoft.com/en-us/windows-hardware/drivers/usbcon/windows-desktop-app-for-a-usb-device` | §4.2 |
| D14 | IOCTL_USB_GET_NODE_CONNECTION_INFORMATION_EX | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/usbioctl/ni-usbioctl-ioctl_usb_get_node_connection_information_ex` | §4.3 |
| D15 | USB_PROTOCOLS union (usbioctl.h) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/usbioctl/ns-usbioctl-_usb_protocols` | §4.3 |
| D16 | Usbioctl.h header | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/usbioctl/` | §4.3 |
| D17 | IOCTL_SCSI_PASS_THROUGH (ntddscsi.h) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/ntddscsi/ni-ntddscsi-ioctl_scsi_pass_through` | §5.4 |
| D18 | Scsi.h header | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/scsi/` | §5.4 |
| D19 | STORAGE_BUS_TYPE (ntddstor.h) | `https://learn.microsoft.com/en-us/windows-hardware/drivers/ddi/ntddstor/ne-ntddstor-storage_bus_type` | §5.1 |
| D20 | IOCTL_BTH_GET_RADIO_INFO (bthioctl.h) — WindowsDriverDocs DDI | `https://github.com/MicrosoftDocs/windows-driver-docs-ddi/blob/staging/wdk-ddi-src/content/bthioctl/ni-bthioctl-ioctl_bth_get_radio_info.md` | §3.3 |

## Microsoft Learn — Win32 APIs (güvenilirlik: yüksek)

| # | Kaynak | URL | Kullanıldığı bölüm |
|---|---|---|---|
| W1 | DOT11_PHY_TYPE enumeration (Windot11.h) | `https://learn.microsoft.com/en-us/windows/win32/nativewifi/dot11-phy-type` | §2.5 |
| W2 | Native Wifi — API overview | `https://learn.microsoft.com/en-us/windows/win32/api/_nwifi/` | §2.5 |
| W3 | WlanEnumInterfaces (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanenuminterfaces` | §6 nota 1 |
| W4 | WlanGetAvailableNetworkList (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetavailablenetworklist` | §6 nota 1 |
| W5 | WlanConnect (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanconnect` | §6 nota 1, §7 F1 |
| W6 | WlanQueryInterface (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanqueryinterface` | §2.2 |
| W7 | wlanapi.h header (fonksiyon listesi) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/` | §2.5, §3 |
| W8 | WFDOpenHandle (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wfdopenhandle` | §6 nota 3 |
| W9 | WFDCloseHandle (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wfdclosehandle` | §6 nota 3 |
| W10 | WlanHostedNetworkStartUsing (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanhostednetworkstartusing` | §6 nota 2, 9 |
| W11 | Wireless Profile Compatibility | `https://learn.microsoft.com/en-us/windows/win32/nativewifi/wireless-profile-compatibility` | §7 F1 |
| W12 | About Wireless LAN API for Windows XP SP2 | `https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-wireless-lan-api-for-windows-xp-service-pack-2` | §6 nota 1 |
| W13 | BluetoothFindFirstRadio (bluetoothapis.h) | `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/nf-bluetoothapis-bluetoothfindfirstradio` | §3.1.1 |
| W14 | BluetoothGetDeviceInfo (bluetoothapis.h) | `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/nf-bluetoothapis-bluetoothgetdeviceinfo` | §3.1.1 |
| W15 | BluetoothGetRadioInfo (bluetoothapis.h) | `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/nf-bluetoothapis-bluetoothgetradioinfo` | §3.1.1 |
| W16 | BLUETOOTH_RADIO_INFO (bluetoothapis.h) | `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/ns-bluetoothapis-bluetooth_radio_info` | §3.1.1 |
| W17 | bluetoothapis.h header (fonksiyon listesi) | `https://learn.microsoft.com/en-us/windows/win32/api/bluetoothapis/` | §3.1.1, §3.1.2 |
| W18 | BluetoothLEAdvertisementWatcher (WinRT) | `https://learn.microsoft.com/en-us/uwp/api/windows.devices.bluetooth.advertisement.bluetoothleadvertisementwatcher` | §3.1.4 |
| W19 | Bluetooth advertisement sample | `https://learn.microsoft.com/en-us/samples/microsoft/windows-universal-samples/bluetoothadvertisement/` | §3.1.4 |
| W20 | SetupDiGetClassDevsW (setupapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/setupapi/nf-setupapi-setupdigetclassdevsw` | §4.1 |
| W21 | SetupDiGetDeviceRegistryPropertyW (setupapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/setupapi/nf-setupapi-setupdigetdeviceregistrypropertyw` | §4.1 |
| W22 | WinUsb_Initialize (winusb.h) | `https://learn.microsoft.com/en-us/windows/win32/api/winusb/nf-winusb-winusb_initialize` | §4.2 |
| W23 | Winusb.h header | `https://learn.microsoft.com/en-us/windows/win32/api/winusb/` | §4.2 |
| W24 | STORAGE_BUS_TYPE (winioctl.h) | `https://learn.microsoft.com/en-us/windows/win32/api/winioctl/ne-winioctl-storage_bus_type` | §5.1 |
| W25 | Working with NVMe Drives | `https://learn.microsoft.com/en-us/windows/win32/fileio/working-with-nvme-devices` | §5.3 |
| W26 | WLAN_REALTIME_CONNECTION_QUALITY (wlanapi.h) | `https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_realtime_connection_quality` | §2.6 (ilgili) |

## Üçüncü taraf (güvenilirlik: orta — yalnız export teyidi)

| # | Kaynak | URL | Kullanıldığı bölüm |
|---|---|---|---|
| T1 | wlanapi.dll export envanteri | `https://www.jesusninoc.com/dll/wlanapi/` | §2.6, §3 |
| T2 | wlanutil.dll (forward'lar) | `https://www.jesusninoc.com/dll/wlanutil/` | §2.6 |

## Vault içi referanslar

| # | Dosya | Konu |
|---|---|---|
| V1 | `projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani.md` | 10 DLL · PE export matrisi (XP 36 → Win10 148) · dynload-resolver · Win11 eksik |
| V2 | `PROJECTS.md` §7.1.4 | NevaConnect Multi-Room Audio (C++20 + JS, WebSocket 9742) |
| V3 | `E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md` | RPi5/L0-L3 mimarisi, BLE (⚠️ erişim dışı — doğrulanamadı) |
| V4 | `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` | Windows WiFi parser + WlanCloseHandle race |

---

## Notlar (Researcher Agent — 2026-10-15)

**Doğrulama metodolojisi:** Her bölüm için `exa.web_search_exa` + `exa.web_fetch_exa`
ile Microsoft Learn / Support sayfaları çekildi; requirement blokları (min OS, header,
lib, DLL) birebir aktarıldı. Çekilemeyen sayfalar (`wlanSignalValueToBarEx`,
`BluetoothGetHostedRadioInfo`) `CRAWL_NOT_FOUND` hatası verdi → ilgili iddialar
`⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretlendi; **uydurulmadı**.

**Düzeltmeler (görev direktifi ↔ kaynak çelişkisi):**

1. `BusTypeSata = 0x0A` (direktif) → **`0x0B`** (enum sıralaması; `0x0A = BusTypeSas`). §5.1.
2. `NVMeGetLogPage` (direktif) → MSDN'de **yok**; doğru: `NVMeDataTypeLogPage` +
   `IOCTL_STORAGE_QUERY_PROPERTY`. §5.3.
3. "WinRT Windows.Devices.Bluetooth (Windows 8+)" (direktif) → belgelenen sınıf
   (`BluetoothLEAdvertisementWatcher`) **Windows 10 (10.0.10240.0)** minimum gösterir. §3.1.4.

**Belirsizlikler (§8'e taşındı):** BT sürücü direktifi okuması (§3.3) · sürücü yazımı kapsamı (§7.x) ·
Win11 DLL eksikliği (§6) · BLE GATT UUID (§3.5) · PCM5122 bağlantı yolu (§4.4) ·
`wlanSignalValueToBarEx` min OS · `BluetoothGetHostedRadioInfo` varlığı.

**Kod durumu:** **KOD YOK** — bu doküman yalnızca vault kaydı + kapsamlı plandır.
Kullanıcı onayı beklenmektedir. `git commit` subagent tarafından ATILMAMIŞTIR (orkestratöre aittir).
