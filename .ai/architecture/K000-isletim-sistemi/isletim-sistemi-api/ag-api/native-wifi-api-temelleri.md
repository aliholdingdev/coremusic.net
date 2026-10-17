---
node_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api/native-wifi-api-temelleri
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-api/ag-api
children: []
depends_on: []
evidence:
  - https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-wireless-lan-api-for-windows-xp-service-pack-2
  - https://learn.microsoft.com/en-us/windows/win32/nativewifi/what-s-new-in-native-wifi
  - https://learn.microsoft.com/en-us/windows/win32/nativewifi/native-wifi-functions
  - https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-reason-code
  - https://learn.microsoft.com/en-us/windows/win32/nativewifi/dot11-phy-type
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanopenhandle
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanenuminterfaces
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanscan
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetavailablenetworklist
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_available_network
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetnetworkbsslist
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_bss_entry
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanconnect
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_connection_parameters
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_connection_mode
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanregisternotification
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_notification_acm-r1
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_interface_state-r1
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_intf_opcode
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanqueryinterface
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlansetprofile
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetprofile
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanreasoncodetostring
  - https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanfreememory
  - https://learn.microsoft.com/en-us/windows/win32/com/com-error-codes-7
  - https://learn.microsoft.com/en-us/windows-server/networking/technologies/wlan/wireless-networking
  - https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/netsh-wlan
  - https://learn.microsoft.com/en-us/windows/win32/nativewifi/dual-sta-query-config
  - https://learn.microsoft.com/en-us/windows/whats-new/whats-new-windows-11-version-24h2
  - https://learn.microsoft.com/en-us/uwp/api/windows.devices.wifi.wifiadapter
validation: "2026-10-15 — ilk yazım: 30 resmi URL HEAD 200 (Learn search API ile çapraz sorgulanarak bulundu); enum/struct/sabit alıntıları hedef sayfadan birebir çekildi (DOT11_PHY_TYPE · WLAN_INTERFACE_STATE · WLAN_NOTIFICATION_ACM · WLAN_CONNECTION_PARAMETERS · WLAN_CONNECTION_MODE · WLAN_BSS_ENTRY · WlanOpenHandle sürüm tablosu · ERROR_NDIS_DOT11_POWER_STATE_INVALID 0x80342002 · wlanSignalQuality 0-100 · keyMaterial) · NevaConnect 01/03/04 doc disk kesişimi"
open_questions:
  - "WMI MSFT_Wlan* sınıf ailesi için resmi Microsoft Learn sayfası bulunamadı → ⚠️ VERIFICATION REQUIRED"
  - "IWLAN* (eski OEM/IHV arayüzü) iddiası kaynaklanamadı → ⚠️ VERIFICATION REQUIRED"
  - "XP'de export'u olan ama MSDN XP beyaz listesinde olmayan 16 fonksiyonun gerçek davranışı (NevaConnect 03 §7.1 #2) → ⚠️ VERIFICATION REQUIRED"
  - "Win11 24H2 Wi-Fi 7 desteğinin Native Wifi / DOT11_PHY_TYPE eht üzerinden raporlama zinciri (04 doc F2) bütünüyle teyitli değil → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# native-wifi-api-temelleri

**ELI15:** Windows'ta kablosuz ağı yönetmek (liste, bağlan, kopunca haber ver)
**programdan** yapılır; program bu işi doğrudan sürücüye değil, sistem DLL'si
`wlanapi.dll` (**Native Wifi API**) üzerinden **hizmete** (WLAN AutoConfig /
`wlansvc`) yaptırır. Bu dosya bu API'nin temelini belgeler: handle yaşam
döngüsü, arayüz/durum kavramı, tarama-liste-bağlantı-bildirim arayüzleri,
profil XML'i, sürüm/destek modeli ve güvenlik ilkeleri.

| Künye | Değer |
|---|---|
| node_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api/native-wifi-api-temelleri |
| parent_id | K000-isletim-sistemi/isletim-sistemi-api/ag-api |
| Kademe | 3 — modül |
| Grup | mimari |
| Durum | draft |

## §1 — ELI15 ve sınır (bu dosya ne DEĞİL)

### §1.1 Tek cümlelik konum

Native Wifi API, Windows Vista ve sonrası ile Windows XP SP2/SP3 (Wireless LAN
API yeniden dağıtımı) üzerinde **kablosuz yönetim düzlemi**ni (management plane)
sunan, **user-mode**, `wlanapi.h` başlıklı, istemci-sunucu mimarili Win32
API'sidir.

### §1.2 Sınır tablosu (ne DEĞİL — nereye gider)

| Kapsam dışı konu | Nereye gider | Gerekçe |
|---|---|---|
| Sürücü / WDI / dot11wificx / NDIS miniport iç yapısı | `K001-donanım` + `K010-network` (sürücü katmanı) | Bu node yalnız user-mode API yüzeyini belgeler; OID_WDI vb. sürücü DDI'si radyo katmanındadır (NevaConnect 03 §3.6 F3: FTM'nin gerçek katmanı `OID_WDI_TASK_REQUEST_FTM`) |
| Neva Connect uygulama tasarımı (çoklu-EXE, resolver, fazlar) | `.ai/projects/NevaConnect/01..04` | 01 §4.1 katman tasarımı ve 03 §4 resolver bu projenin dokümanıdır; burada **kavramsal API katmanı** vardır, kod/plan yoktur |
| Bağlantı durumu dallanması (WebSocket 9742 köprüsü, IPC) | `K010-network/connectivity/neva-connect-wlan-dallanma-mimarisi` | Uygulama mimarisi — bu node'un tüketicisidir, parçası değildir |
| Soket / TCP / UDP / TLS / POSIX ↔ Winsock | Kardeş `ag-api` node'ları | Ağ sırtı ayrı katman (bkz §7) |
| Kernel ağ yığını (TCP durum makinesi, yönlendirme) | `isletim-sistemi-kernel` | Üst dal `isletim-sistemi-api` §4 sınırı |
| Linux wifi (NetworkManager/dbus) | `neva-connect` L0-L3 (Linux dalı) | Bu node Windows API'sine aittir |

### §1.3 Bu dosyanın taşıdığı şey

| # | Kapsam başlığı | Karşılığı |
|---|---|---|
| 1 | API'nin konumu ve istemci/sunucu mimarisi | §2 |
| 2 | Temel kavram arayüzleri (arayüz, tarama, bağlantı, bildirim, profil, opcode, PHY) | §3 |
| 3 | Sürüm ve destek modeli (XP alt kümesi → Win11) | §4 |
| 4 | Güvenlik ve sağlamlık ilkeleri | §5 |
| 5 | Alternatifler (netsh, WMI, WinRT) ve neden Native Wifi | §6 |
| 6 | Vault ilişkileri, açık sorular, kaynaklar | §7-§9 |

## §2 — Native Wifi API'nin konumu (wlanapi.dll · istemci/sunucu · handle)

### §2.1 Katman diyagramı

~~~text
[Uygulama — neva-wlan.exe / herhangi bir Win32 süreç]
        │  Native Wifi API (wlanapi.h · Wlan* çağrıları)
        ▼
[wlanapi.dll]  ← sistem bileşeni, RPC istemcisi (user-mode)
        │  RPC (yerel)
        ▼
[WLAN AutoConfig servisi — "wlansvc"]  ← yapılandırma/tarama/bağlantı sahibi
        │  OID / dot11 DDI
        ▼
[Kablosuz adaptör sürücüsü — NDIS/dot11 miniport (windot11.h)] → radyo
~~~

> Kaynak: WLAN AutoConfig servisi tanımı — "Once enabled, the WLAN AutoConfig
> service dynamically scans and selects which wireless network your device can
> automatically connect to using a wireless network adapter."
> — Microsoft Learn, *Wireless LAN overview in Windows Server*.

### §2.2 İstemci/sunucu kanıtları (dokümantasyondan)

| Kanıt | Ne gösterir | Kaynak |
|---|---|---|
| `WlanOpenHandle` hata listesinde `RPC_STATUS` | Çağrılar RPC üzerinden yerel sunucuya gider | WlanOpenHandle sayfası (Return value) |
| `ERROR_REMOTE_SESSION_LIMIT_EXCEEDED` — "Too many handles have been issued by the **server**" | Handle sayısını **sunucu** kotalar; süreç içi değil | WlanOpenHandle sayfası |
| XP notu: "WlanOpenHandle will return an error message if the **Wireless Zero Configuration (WZC)** service has not been started or if the WZC service is not responsive" | XP'de işi yapan servis WZC'dir; servis yoksa API çalışmaz | WlanOpenHandle sayfası (XP SP3/SP2 notu) |
| Vista+ servis adı WLAN AutoConfig (`wlansvc`) | WZC'nin ardılı | Wireless LAN overview (Windows Server) |

### §2.3 `WlanOpenHandle` — sürüm pazarlığı

| Parametre | Anlam | Doğrulanmış değerler |
|---|---|---|
| `dwClientVersion` | İstemcinin desteklediği **en yüksek** WLAN API sürümü | **1** = "Client version for Windows XP with SP3 and Wireless LAN API for Windows XP with SP2" · **2** = "Client version for Windows Vista and Windows Server 2008" |
| `pReserved` | Gelecek için ayrılmış | `NULL` olmalı (`ERROR_INVALID_PARAMETER` aksi halde) |
| `pdwNegotiatedVersion` | Oturumda kullanılacak sürüm — "usually the highest version supported by both the client and server" | [out] DWORD |
| `phClientHandle` | Oturum handle'ı — sonraki tüm `Wlan*` çağrıları bunu kullanır | [out] HANDLE |

| Sürüm bilgisi kuralı | Ayrıntı (MSDN) |
|---|---|
| Bileşik sürüm | "composite version number made up of both major and minor versions" |
| Major/minor yerleşimi | **major = low-order word**, **minor = high-order word** |
| Üretim makrosu | `WLAN_API_MAKE_VERSION(_major, _minor)` |
| Okuma makroları | `WLAN_API_VERSION_MAJOR(_v)` · `WLAN_API_VERSION_MINOR(_v)` |
| XP davranışı | Sürüm 1 müzakeresi başarısız olursa WZC servis hatası görünür |

> Not: sürüm numarası **karar kaynağı değildir**; Neva Connect resolver'ında
> yalnız telemetri/rapor içindir (NevaConnect 03 §4.5, 01 §4.2 D2 — export
> kapısı karar verir).

### §2.4 Handle yaşam döngüsü

| # | Adım | Çağrı | Not |
|---|---|---|---|
| 1 | Aç | `WlanOpenHandle(dwClientVersion=2, NULL, &negotiated, &h)` | Başarı = `ERROR_SUCCESS` |
| 2 | Enum / tarama / bağlan / sorgula / bildirim kaydı | `WlanEnumInterfaces` · `WlanScan` · `WlanConnect` · `WlanQueryInterface` · `WlanRegisterNotification` | Hepsi `h` ile |
| 3 | Bildirimi kaldır | `WlanRegisterNotification(h, WLAN_NOTIFICATION_SOURCE_NONE, NULL, NULL, NULL)` | §5.3 sırasının ilk adımı |
| 4 | Kapat | `WlanCloseHandle(h, NULL)` | Kapandığında kayıt "automatically undone" (MSDN WlanRegisterNotification) |
| 5 | Belleği boşalt | `WlanFreeMemory(p)` | API'nin döndürdüğü her buffer için — §2.5 |

| Kural | Ayrıntı |
|---|---|
| Handle oturum-scopeludur | Birden çok handle açılabilir; her biri bağımsız oturum/sürüm müzakeresi taşır |
| Handle ≠ API unload | `wlanapi.dll` süreç kapanana kadar **boşaltılmaz** (NevaConnect 03 §4.6: asla `FreeLibrary` — callback unload riski) |
| Hatalı handle | `ERROR_INVALID_HANDLE` sınıfı hatalar — use-after-close; RAII sarmalayıcı gerekir (01 §4.1 `wlacore`) |

### §2.5 Bellek sahipliği (buffer kuralı)

| Kural | Ayrıntı | Kaynak |
|---|---|---|
| API allocate eder | `WlanEnumInterfaces` · `WlanGetAvailableNetworkList` · `WlanGetNetworkBssList` · `WlanQueryInterface` · `WlanGetProfile` gibi çağrılar out-buffer döndürür | İlgili MSDN sayfaları |
| Kullanıcı free eder | "The memory used for the buffer pointed to by ppAvailableNetworkList parameter should be released by calling the **WlanFreeMemory** function after the buffer is no longer needed." | WlanGetAvailableNetworkList (Remarks) |
| Kendi allocate'ı | `WlanAllocateMemory` — API içinde/uygulamada blok ayırır; serbestleştirme yine `WlanFreeMemory` | Native Wifi functions |
| Çifte free / use-after-free | Sarmalayıcı sahiplik verir (RAII `unique_ptr` benzeri deleter = `WlanFreeMemory`) | 01 §4.1 — "WlanAllocateMemory/FreeMemory RAII sarmalayıcı" |

## §3 — Temel kavram arayüzleri

### §3.1 Arayüz (GUID) ve durum makinesi

**Ne:** `WlanEnumInterfaces` kablosuz arayüzleri (adaptör sanal arayüzleri)
**GUID** ile listeler; her arayüzün kimliği `WLAN_INTERFACE_INFO.InterfaceGuid`'dir
(açıklama metni `strInterfaceDescription`). Tarama/bağlantı/bildirim çağrıları
bu GUID'yi hedef alır.

**Durum makinesi — `WLAN_INTERFACE_STATE`** (MSDN sayfasından birebir, 8 değer):

| Değer | Anlam | Tipik geçiş |
|---|---|---|
| `wlan_interface_state_not_ready` | Arayüz hazır değil (servis/driver) | İlk durum → `connected`/`disconnected` |
| `wlan_interface_state_connected` | Bağlı | Hedef durum |
| `wlan_interface_state_ad_hoc_network_formed` | Ad-hoc ağ kuruldu | Ad-hoc modda |
| `wlan_interface_state_disconnecting` | Kopuyor | `connected` → `disconnected` |
| `wlan_interface_state_disconnected` | Kopuk | Yeniden tarama tetikler |
| `wlan_interface_state_associating` | Birleştiriliyor (association) | `disconnected` → `connected` |
| `wlan_interface_state_discovering` | Keşif (tarama/kabul bekliyor) | Ad-hoc/provisioning |
| `wlan_interface_state_authenticating` | Kimlik doğrulama (802.1X/PSK) | `associating` sonrası |

> Durum okuması: `WlanQueryInterface(h, guid, wlan_intf_opcode_interface_state,
> ...)` → `WLAN_INTERFACE_INFO.isState` · Bildirim tarafı: ACM olayları
> (`wlan_notification_acm_connection_complete` / `_disconnected` vb.) — §3.4.

**Durum makinesi akışı (metin):**

~~~text
not_ready ──servis+driver──▶ disconnected ──scan/connect──▶ associating
associating ──auth ok──▶ authenticating ──ok──▶ connected
connected ──kapat/kaçır──▶ disconnecting ──▶ disconnected
(ad-hoc kurulumunda: discovering / ad_hoc_network_formed durumları)
~~~

### §3.2 Tarama ve liste

#### §3.2.1 `WlanScan` — isteğe bağlı tetikleyici

| Parametre | Anlam |
|---|---|
| `pInterfaceGuid` | Hedef arayüz (GUID) |
| `pDot11Ssid` | Belirli SSID için tarama; `NULL` = tümü (passive/active politikası sürücü/hizmete ait) |
| `pIeData` | Ek istek IE'leri (ör. vendor-specific) — çoğu uygulamada `NULL` |
| Dönüş | `ERROR_SUCCESS` tetiklendi anlamında; **tarama sonucu ayrı çağrıyla alınır** |

> Tarama tamamlanması bildirimle izlenir: `wlan_notification_acm_scan_complete`
> / `_scan_fail` (MSDN WLAN_NOTIFICATION_ACM). `WlanScan` XP beyaz listesindedir.

#### §3.2.2 `WlanGetAvailableNetworkList` — ağ listesi (seviye: SSID)

| Konu | Ayrıntı |
|---|---|
| Çıktı | `WLAN_AVAILABLE_NETWORK_LIST` (dizge + `dwIndex` ile elemanlar) — buffer `WlanFreeMemory` ile serbest (§2.5) |
| Bayraklar | `dwFlags` ör. tüm ad-hoc profilleri / yalnız bağlı SSID filtreleri (MSDN sayfasındaki `WLAN_AVAILABLE_FLAG_*`) |
| XP uyumu | XP beyaz listesinde (NevaConnect 03 §3.1) |

**`WLAN_AVAILABLE_NETWORK` alanları** (MSDN struct sayfasından birebir):

| Alan | Tip | Anlam |
|---|---|---|
| `strProfileName[WLAN_MAX_NAME_LENGTH]` | WCHAR | Ağa bağlı profil adı; profil yoksa **boş** |
| `dot11Ssid` | `DOT11_SSID` | Ağ SSID'si |
| `dot11BssType` | `DOT11_BSS_TYPE` | Infrastructure / ad-hoc |
| `uNumberOfBssids` | ULONG | Gözlenen BSSID sayısı |
| `bNetworkConnectable` | BOOL | Bağlanılabilir mi |
| `wlanNotConnectableReason` | `WLAN_REASON_CODE` | Bağlanamama nedeni (kod) — `WlanReasonCodeToString` ile metne çevrilir (§3.5) |
| `uNumberOfPhyTypes` · `dot11PhyTypes[]` · `bMorePhyTypes` | — | Desteklenen PHY türleri (§3.7) |
| `wlanSignalQuality` | ULONG | **0–100 yüzde**; MSDN: "A value of 0 implies an actual RSSI signal strength of -100 dbm. A value of 100 implies ... -50 dbm." |
| `bSecurityEnabled` | BOOL | Güvenlik açık mı |
| `dot11DefaultAuthAlgorithm` · `dot11DefaultCipherAlgorithm` | — | Varsayılan doğrulama/şifreleme |
| `dwFlags` | DWORD | Ek bayraklar (ör. profil bağlantılı bayraklar) |

#### §3.2.3 `WlanGetNetworkBssList` — BSS düzeyi liste

| Konu | Ayrıntı |
|---|---|
| Ne | Erişim noktasının (AP) **BSS** düzeyinde kaydı: aynı SSID'nin birden fazla BSSID'si ayrı satır |
| Çıktı | `WLAN_BSS_LIST` → `WLAN_BSS_ENTRY` |
| **Kritik destek notu** | **XP'de `ERROR_NOT_SUPPORTED`**: "if this function was called from a Windows XP with SP3 or Wireless LAN API for Windows XP with SP2 client" — export XP'de **var**, destek **yok** (NevaConnect 03 §2.4, MSDN min Vista) |

**`WLAN_BSS_ENTRY` alanları** (MSDN struct sayfasından):

| Alan | Anlam |
|---|---|
| `dot11Ssid` | BSS'in SSID'si |
| `uPhyId` | PHY indeksi |
| `dot11Bssid` | **BSSID** (6 bayt MAC) |
| `dot11BssPhyType` | PHY türü (§3.7) |
| `lRssi` | Sinyal gücü (dBm, `LONG`) |
| `uLinkQuality` | Bağlantı kalitesi (%) |
| `bInRegDomain` | Bölge kuralına uygun mu |
| `usBeaconPeriod` · `ullTimestamp` · `ullHostTimestamp` | Beacon zaman bilgisi |
| `usCapabilityInformation` | Kapasite bayrakları |
| `ulChCenterFrequency` | **Merkez kanal frekansı** (kHz) — kanal numarası buradan türetilir |
| `wlanRateSet` | Desteklenen hız seti |
| `ulIeOffset` · `ulIeSize` | Vendor IE verisinin offset/boyutu (IE verisi buffer içinde ayrı yerde) |

> `WlanGetNetworkBssList` XP beyaz listesinde **değildir**; Neva Connect
> siyah-liste kuralı (03 §2.5 madde 1) bu fonksiyonu XP'de **çağırmaz**.

### §3.3 Bağlantı (`WlanConnect` · profil · `WlanDisconnect`)

#### §3.3.1 Bağlantı parametreleri — `WLAN_CONNECTION_PARAMETERS`

| Alan | Tip | Anlam |
|---|---|---|
| `wlanConnectionMode` | `WLAN_CONNECTION_MODE` | Bağlantı modu (aşağıdaki tablo) |
| `strProfile` | `LPCWSTR` | Kullanılacak **profil adı** (mod `profile` ise zorunlu) |
| `pDot11Ssid` | `PDOT11_SSID` | Hedef SSID (profilsiz modlarda) |
| `pDesiredBssidList` | `PDOT11_BSSID_LIST` | Belirli BSSID'ye bağlanma; `NULL` = serbest |
| `dot11BssType` | `DOT11_BSS_TYPE` | Infrastructure / ad-hoc |
| `dwFlags` | DWORD | Örn. `WLAN_CONNECTION_FORCE_...` bayrakları |

**`WLAN_CONNECTION_MODE` değerleri** (MSDN enum sayfasından):

| Değer | Anlam |
|---|---|
| `wlan_connection_mode_profile = 0` | Profil üzerinden bağlan — **XP'de tek desteklenen mod** |
| `wlan_connection_mode_temporary_profile` | Geçici profil (kaydedilmeden) |
| `wlan_connection_mode_discovery_secure` | Güvenli keşif (bağlantısız tarama-sınıfı) |
| `wlan_connection_mode_discovery_unsecure` | Güvenli olmayan keşif |
| `wlan_connection_mode_auto` | Otomatik seçim |
| `wlan_connection_mode_invalid` | Geçersiz |

> MSDN `WLAN_CONNECTION_PARAMETERS` notu: "Windows XP with SP3 and Wireless LAN
> API for Windows XP with SP2: **Only the wlan_connection_mode_profile value is
> supported**."

#### §3.3.2 Bağlantı akışı

| # | Adım | Çağrı | Not |
|---|---|---|---|
| 1 | Profil hazırla (yoksa) | `WlanSetProfile` (XML) | §3.5 |
| 2 | Bağlan | `WlanConnect(h, &guid, &params, NULL, NULL)` | Sonuç **asenkron**: ACM bildirimi ile tamamlanır |
| 3 | İzle | `wlan_notification_acm_connection_start` → `_connection_complete` / `_connection_attempt_fail` | §3.4 |
| 4 | Kopar | `WlanDisconnect(h, &guid, NULL)` | Aynı zamanda hizmet tarafı kopmalarında da bildirim gelir |

| Konu | Kural |
|---|---|
| Başarı = tetikleme | `WlanConnect` `ERROR_SUCCESS` dönünce bağlantı **garanti değildir**; `connection_complete` beklenir |
| Neden kodu | Başarısızlıkta arayüz durumu + ACM verisi; profil yazım hataları `WlanSetProfile`'in `pstrReasonCode` çıktısındadır |
| İkinci çağrı | Zaten bağlıyken tekrar `WlanConnect` davranışa göre yön değiştirir/reddedilir — uygulama durumu önceden sorgular (`interface_state` opcode) |

### §3.4 Bildirim (`WlanRegisterNotification`)

**Ne:** Tek **opt-in** callback kaydı — olaylar ilgili handle üzerinde bildirilir.
Kayıt yoksa olay gözlenmez (varsayılan sessizlik).

| Parametre | Anlam |
|---|---|
| `dwNotifSource` | `WLAN_NOTIFICATION_SOURCE_*` maskesi (ACM · MSM · SECURITY · HNWK · IHV · ALL ...) |
| `bIgnoreDuplicate` | Aynı olay tekrarında yutma bayrağı — **XP'de yok sayılır** |
| `pCallback` | `WLAN_NOTIFICATION_CALLBACK` — `VOID CALLBACK(NOTIFY, CONTEXT)` |
| `pCallbackContext` | Uygulama bağlamı (callback'e taşınır) |

**Olay kaynağı: `WLAN_NOTIFICATION_ACM`** (MSDN enum sayfasından seçilmiş
değerler — tam liste daha uzun):

| Değer | Ne zaman gelir |
|---|---|
| `wlan_notification_acm_start` | Bildirim akışı başlangıcı (aynı grubun ilk vuruşu) |
| `wlan_notification_acm_scan_complete` / `_scan_fail` | `WlanScan` sonucu |
| `wlan_notification_acm_connection_start` | Bağlantı denemesi başladı |
| `wlan_notification_acm_connection_complete` | Bağlantı **tamamlandı** (XP'de de gelir) |
| `wlan_notification_acm_connection_attempt_fail` | Deneme başarısız |
| `wlan_notification_acm_network_available` / `_network_not_available` | Ağ erişilebilirlik değişimi |
| `wlan_notification_acm_disconnecting` / `_disconnected` | Kopma başladı / tamamlandı (XP'de yalnız `_disconnected`) |
| `wlan_notification_acm_interface_arrival` / `_interface_removal` | Arayüz eklendi/çıkarıldı (dongle tak/çıkar) |
| `wlan_notification_acm_profile_change` / `_profile_name_change` | Profil değişti |
| `wlan_notification_acm_autoconf_enabled` / `_disabled` | WLAN AutoConfig açma/kapama |

**XP kısıtları** (MSDN WlanRegisterNotification + XP sayfası):

| # | Kısıtlama |
|---|---|
| 1 | Yalnız `wlan_notification_acm_connection_complete` ve `wlan_notification_acm_disconnected` bildirimleri verilir (XP beyaz kapsamı — NevaConnect 03 §3.1 madde 8) |
| 2 | Bildirimler Netman üzerinden taşınır; Netman kapalıysa bildirim **gelmez** → timeout-ile-arama yedeği gerekir (NevaConnect 03 §3.1, MSDN WlanRegisterNotification) |
| 3 | `SOURCE_ALL` ≡ `SOURCE_ACM` sayılır; MSM/SECURITY/IHV kaynakları XP'de desteklenmez |
| 4 | `bIgnoreDuplicate` yok sayılır |
| 5 | Handle kapatılınca kayıt "automatically undone" — açıkça NONE ile kapatmak yine de doğru sıradır (§5.3) |
| 6 | **Callback içinden** `WlanRegisterNotification` çağrısı deadlock üretir ("the call never completing") — MSDN WlanRegisterNotification |

### §3.5 Profil (XML) ve neden kodları

#### §3.5.1 Profil arayüzleri

| Çağrı | Ne yapar | Not |
|---|---|---|
| `WlanSetProfile` | XML profilini yaz/üzerine yaz | `strProfileXml` · `strProfileName` · `pSecurityDescriptor` · `bOverwrite` · `dwFlags` · `pbGrantedAccess` · `pstrReasonCode` (out) |
| `WlanGetProfile` | Profil XML'ini oku | Yetki varsa **düz metin anahtar** döner (§5.5) |
| `WlanDeleteProfile` | Profili sil | XP beyaz listesinde |
| `WlanGetProfileList` · `WlanSetProfileList` · `WlanSetProfilePosition` | Sıralı profil listesi | XP beyaz listesinde (NevaConnect 03 §3.1) |
| `WlanSetProfileEapXmlUserData` | 802.1X/EAP kullanıcı verisi | XP beyaz listesinde |
| `WlanRenameProfile` | Profil adı değiştirme | XP'de export var **ama** MSDN XP listesinde yok → §4.2 ikili kapı |

**Profil nedenleri:** `WlanSetProfile` başarısız olduğunda `pstrReasonCode`
`WLAN_REASON_CODE` değeri döndürür; bu kod insan-okunur metne çevrilir:

| Çağrı | Ne yapar |
|---|---|
| `WlanReasonCodeToString(reason, size, buffer, reserved)` | Kod → metin; dil etkili (kullanıcıya gösterilebilir mesaj) |
| Kod kaynağı | `WLAN_REASON_CODE` enum (Native Wifi · Wlanapi.h) |
| Ayrıca | `WLAN_AVAILABLE_NETWORK.wlanNotConnectableReason` da bu kod ailesindendir (§3.2.2) |

> Neden kodu akışı (Neva Connect, 01 §4.1 `wlaconn`): `WlanConnect`/
> `WlanSetProfile` hatası → `WlanReasonCodeToString` → UI metni. Kodu ham
> göstermek kullanıcı deneyimi değildir.

### §3.6 Opcode sorguları (`WlanQueryInterface` · `WlanSetInterface`)

**Ne:** Arayüz başına **anahtar/değer** sorgusu. `WlanQueryInterface` okur,
`WlanSetInterface` yazar; ikisi de `WLAN_INTF_OPCODE` kullanır.

**Örnek opcode'lar** (MSDN enum sayfasından — liste örnektir, tam değildir):

| Opcode | Tipik kullanım | Yazılabilir mi |
|---|---|---|
| `wlan_intf_opcode_autoconf_enabled` | WLAN AutoConfig açık mı | Evet |
| `wlan_intf_opcode_background_scan_enabled` | Arka plan taraması | Evet |
| `wlan_intf_opcode_interface_state` | Durum makinesi okuması (§3.1) | Hayır (okuma) |
| `wlan_intf_opcode_current_connection` | Mevcut bağlantı attributeleri (`WLAN_CONNECTION_ATTRIBUTES`) | Hayır |
| `wlan_intf_opcode_radio_state` | Radyo açık/kapalı (`WLAN_RADIO_STATE`) | Evet |
| `wlan_intf_opcode_bss_type` | Altyapı/ad-hoc tercihi | Evet |
| `wlan_intf_opcode_channel_number` | Bulunulan kanal | Hayır |
| `wlan_intf_opcode_hosted_network_capable` | Hosted Network yeteneği (Win7+) | Hayır |
| `wlan_intf_opcode_secondary_sta_interfaces` | İkincil STA (dual-STA, Win11 SDK) | Hayır |

**XP kısıtı:** yalnız **4 opcode** geçerli — `autoconf_enabled` ·
`bss_type` · `interface_state` · `current_connection`
(MSDN WlanQueryInterface — NevaConnect 03 §3.1).

| Hata modu | Anlam | Tepki |
|---|---|---|
| `ERROR_INVALID_PARAMETER` | Desteklenmeyen opcode / boyut uyuşmazlığı | Opcode beyaz listesi kontrolü (XP: 4) |
| `ERROR_NOT_SUPPORTED` | Sürüm/özellik reddi (ör. Win8.1-öncesi `secondary_sta_interfaces`) | `FeatureNotSupported` (03 §4.4) |
| `ERROR_NDIS_DOT11_POWER_STATE_INVALID` | Radyo kapalı (§5.2) | "Radyo kapalı" durumu — hata değil kullanıcı durumu |

### §3.7 PHY türleri (`DOT11_PHY_TYPE`)

**Değer tablosu** (MSDN *DOT11_PHY_TYPE enumeration (Windot11.h)* sayfasından
birebir):

| Değer | Sayı | IEEE karşılığı |
|---|---|---|
| `dot11_phy_type_unknown` | 0 | Bilinmiyor |
| `dot11_phy_type_any` | 0 | Herhangi |
| `dot11_phy_type_fhss` | 1 | FHSS (802.11-1997) |
| `dot11_phy_type_dsss` | 2 | DSSS — 802.11b |
| `dot11_phy_type_irbaseband` | 3 | IR baseband |
| `dot11_phy_type_ofdm` | 4 | OFDM — 802.11a |
| `dot11_phy_type_hrdsss` | 5 | HR/DSSS — 802.11b genişleme |
| `dot11_phy_type_erp` | 6 | ERP — 802.11g |
| `dot11_phy_type_ht` | 7 | HT — 802.11n |
| `dot11_phy_type_vht` | 8 | VHT — 802.11ac |
| `dot11_phy_type_dmg` | 9 | DMG — 60 GHz (802.11ad) |
| `dot11_phy_type_he` | 10 | HE — 802.11ax (Wi-Fi 6/6E) |
| `dot11_phy_type_eht` | 11 | **EHT — 802.11be (Wi-Fi 7)** |
| `dot11_phy_type_IHV_start` / `_IHV_end` | 0x80000000 / 0xffffffff | IHV özel aralığı |

| Kural | Ayrıntı |
|---|---|
| Sayfa gereksinimi | "Minimum supported client: **Windows Vista, Windows XP with SP3**" + "Redistributable: **Wireless LAN API for Windows XP with SP2**" |
| **XP'de PHY yok** | XP kısıtlamaları: "PHY types desteklenmez" (MSDN XP sayfası — NevaConnect 03 §3.1 madde 4) → XP'de `dot11PhyTypes` alanı doldurulmaz |
| EHT tespiti | `dot11_phy_type_eht == 11` ise Wi-Fi 7 bağlantısı raporlanır (NevaConnect 04 §2.6: "Bu değer kaynaklıdır") |
| EHT/SDK notu | EHT değeri SDK'ya **sonradan** eklenmiştir; eski SDK başlıkları `eht`'i tanımaz → derleme zamanı SDK sürümü kontrolü gerekir (04 doc: Wi-Fi 7 min OS = **Win11 24H2**) |
| Kullanım yerleri | `WLAN_AVAILABLE_NETWORK.dot11PhyTypes[]` (§3.2.2) · `WLAN_BSS_ENTRY.dot11BssPhyType` (§3.2.3) · bağlantı PHY tercihleri |

## §4 — Sürüm ve destek modeli

### §4.1 XP alt kümesi (SP2/SP3 — "Wireless LAN API for Windows XP")

**MSDN XP sayfasının desteklediği 20 fonksiyon** (beyaz liste — NevaConnect
03 §3.1, kaynak: MSDN *Native Wifi API Support on Windows XP*):

| # | Fonksiyon | # | Fonksiyon |
|---|---|---|---|
| 1 | `WlanAllocateMemory` | 11 | `WlanOpenHandle` |
| 2 | `WlanCloseHandle` | 12 | `WlanQueryInterface` |
| 3 | `WlanConnect` | 13 | `WlanReasonCodeToString` |
| 4 | `WlanDeleteProfile` | 14 | `WlanRegisterNotification` |
| 5 | `WlanDisconnect` | 15 | `WlanScan` |
| 6 | `WlanEnumInterfaces` | 16 | `WlanSetInterface` |
| 7 | `WlanFreeMemory` | 17 | `WlanSetProfile` |
| 8 | `WlanGetAvailableNetworkList` | 18 | `WlanSetProfileEapXmlUserData` |
| 9 | `WlanGetProfile` | 19 | `WlanSetProfileList` |
| 10 | `WlanGetProfileList` | 20 | `WlanSetProfilePosition` |

**XP kısıtlamaları** (MSDN XP sayfası — belgelendirilmiş, "düzeltilebilir"
değil):

| # | Kısıtlama | Pratik etkisi |
|---|---|---|
| 1 | Profile başına en fazla 1 SSID | Çok-SSID profili yok |
| 2 | Infrastructure ağlar her zaman ad-hoc'tan önce listelenir | Sıralama sabit |
| 3 | **Profil adı = SSID** (kullanıcı serbest ad veremez) | UI profil adlandırma kuralı |
| 4 | **PHY types desteklenmez** | §3.7 — PHY sorgusu XP'de anlamsız |
| 5 | PMK caching desteklenmez | 802.1X performans özelliği yok |
| 6 | IHV extensibility fonksiyonları desteklenmez | `WlanIhvControl` çağrılmaz |
| 7 | Profile permissions desteklenmez | Güvenlik ayarı profilde değil |
| 8 | **Yalnız 2 bildirim**: `connection_complete` + `disconnected` | §3.4 — yedek sorgu döngüsü gerekir |
| 9 | Global 802.1X/EAPOL yapılandırması desteklenmez | Sistem geneli EAP ayarı yok |

### §4.2 36 export ↔ 20 destek: **ikili kapı kuralı**

| Ölçüm | Değer | Kaynak |
|---|---|---|
| XP `wlanapi.dll` (5.1.2600.5512) `Wlan*` export sayısı | **36** | NevaConnect 01 §3 (pconlife MD5 ✓ + PE parse + REA/Ghidra çapraz kontrol) |
| MSDN XP beyaz listesi | **20** fonksiyon + 1 callback tipi | NevaConnect 03 §3.1 (MSDN XP sayfası) |
| Fark | **16** — export'u olan ama XP'de desteklenmeyen/belgelenmemiş | NevaConnect 03 §2.5 |
| Bilinen kanıt: `WlanGetNetworkBssList` | export **VAR**, MSDN min Vista, XP'de `ERROR_NOT_SUPPORTED` | NevaConnect 03 §2.4 (MSDN) |

**İkili kapı (Neva Connect bağlayıcı kuralı — 03 §1.1 Y2, §2.4):**

1. **Export kapısı:** `GetProcAddress` null ise çağrı yapılmaz (crash güvenliği).
2. **Dönüş kodu kapısı:** `DWORD` kontrol edilir; `ERROR_NOT_SUPPORTED` /
   `ERROR_INVALID_FUNCTION` → `FeatureNotSupported` (destek güvenliği).

> Gerekçe: "export olması = desteklenmesi demek **değildir**" — kanıt §2.4.
> Ayrıntı ve siyah liste: `03-wlan-api-ozellik-kisitlama-tablosu.md` (bu dosya
> matrisi **kopyalamaz**, atıf verir).

### §4.3 Sonraki katmanlar — zaman çizelgesi

| Sürüm | Eklenen yetenek | Wlan* export | Kaynak |
|---|---|---|---|
| XP SP2/SP3 | Çekirdek 20 fonksiyon (liste §4.1) | 36 | MSDN XP sayfası · 01 §3 |
| **Windows 7** | **Hosted Network** (SoftAP — 11 `WlanHostedNetwork*`) + `WlanRegisterVirtualStationNotification` | 48 (+12/−0) | MSDN *What's New in Native Wifi* · 01 §3.1 |
| **Windows 8** | **Wi-Fi Direct** (`WFDOpenHandle`, Win8 yeni özellik) · **PLAP** (`Wlan*Plap*) · `WlanConnectEx` · `WlanEnumAllInterfaces` · `WlanStartAP/StopAP` · LowPriv/Private grupları | 103 (+55/−0) | 01 §3.1 · MSDN *What's New* · MSDN `WFDOpenHandle` |
| **Windows 8.1** | `WlanDoesBssMatchSecurity` · `WlanUpdateProfileWithAuthCipher` vb. | 108 (+5/−0) | 01 §3.1 |
| **Windows 10 (10240)** | **`WlanGetAvailableNetworkList2`** (min Win10 — MSDN arşiv) · ANQP/Hotspot 2.0 profil şeması · `WlanWcm*` | 121 (+13/−0) | 01 §3.1 · MSDN arşiv List2 · *What's New* |
| **Windows 10 (19041)** | FTM/802.11mc **export'ları** (public API yok) · non-disruptive scan · device services · movement detector | 148 (+27/−0) | 01 §3.1 · MSDN Q&A (FTM public API yok) |
| **Windows 11 (22000)** | **Dual-STA** (geliştirme: **Windows 11 SDK 10.0.22000.194+**) · `WlanGet/SetExtensibilityInfo` | 149 (+3/−2 private) | MSDN *Dual-STA query and configuration* · 03 §2.3 |
| **Windows 11 24H2** | **Wi-Fi 7 (802.11be/EHT)** desteği — "Support for Wi-Fi 7 was added for consumer access points" | — | MSDN *What's new in Windows 11, version 24H2 for IT pros* · 04 doc §2.1 |

**Export omurgası (01 doc ana bulgusu):** XP (36) → Win10 19041 (148) arasında
**hiçbir sürüm export silmiyor** (her geçişte `−0`); Win11 22000'de 2 *private*
export silinir (03 §2.3). Pratik sonuç: **tek binary + runtime resolver**
(`GetProcAddress`) XP..Win11 çalıştırılabilir (01 §4.2 D1).

### §4.4 Sürüm karar akışı (özet)

~~~text
Yeni özellik çağrısı
  ├─ (1) export var mı? (GetProcAddress)  ──hayır──▶ FeatureNotSupported
  ├─ (2) dönüş kodu == ERROR_SUCCESS?     ──değil──▶ ERROR_NOT_SUPPORTED vb. → FeatureNotSupported
  ├─ (3) opcode/beyaz liste ihlali mi?    ──evet──▶ programlama hatası (XP: 4 opcode, 20 fn)
  └─ (4) success → sonuç; sürüm/OS yalnız kullanıcı mesajı ve telemetri
~~~

## §5 — Güvenlik ve sağlamlık ilkeleri

### §5.1 Yetki

| Durum | Davranış | Kaynak |
|---|---|---|
| Profil yazma yetkisi yok | `WlanSetProfile` → **`ERROR_ACCESS_DENIED`**: "The caller does not have sufficient permissions to set the profile." (all-user profile yazımında DACL kontrol edilir) | MSDN `WlanSetProfile` |
| Profil okuma yetkisi kısıtlı | `WlanGetProfile` anahtarı **şifreli** döndürür (yetki tam ise düz metin) | MSDN `WlanGetProfile` |
| Handle kotası | `ERROR_REMOTE_SESSION_LIMIT_EXCEEDED` — çok sayıda açık handle | MSDN `WlanOpenHandle` |
| Tepki şeması | Yetki hatası uygulamaya **durum** olarak döner; çökme yok (03 §1.1 Y1) | NevaConnect 03 §1.1 |

### §5.2 Radyo kapalı

| Kod | Değer | Anlam |
|---|---|---|
| `ERROR_NDIS_DOT11_POWER_STATE_INVALID` | **0x80342002** | "The wireless local area network interface is **powered down** and doesn't support the requested operation." |

> Kaynak: Microsoft Learn, *COM Error Codes (FWP, WS, NDIS, HyperV) (Winerror.h)*.
> **Kural:** radyo kapalıyken yapılan tarama/bağlantı çağrıları bu kodu döndürür —
> uygulama bunu "hata" değil **"radyo kapalı" durumu** olarak gösterir (uçuş-mod /
> klavye radyo anahtarı senaryosu).

### §5.3 Sızıntısız kapanış sırası

| # | Adım | Çağrı | Neden sıra |
|---|---|---|---|
| 1 | Bildirimi kaldır | `WlanRegisterNotification(h, WLAN_NOTIFICATION_SOURCE_NONE, NULL, NULL, NULL)` | Yeni callback çağrısı durur |
| 2 | **Drain** — kuyrukta kalan bildirimleri tüket | Uygulama kuyruğu (lock-free — 01 §4.2 D5) | Callback'in taşıdığı son olaylar işlenir |
| 3 | Handle'ı kapat | `WlanCloseHandle(h, NULL)` | Kayıt "automatically undone" (MSDN) — yine de açıkça 1. adım |
| 4 | Buffer'ları boşalt | `WlanFreeMemory` (her out-buffer) | §2.5 |
| 5 | DLL boşaltma | **YOK** — asla `FreeLibrary` | 03 §4.6: unload edilmiş kodu çağıran callback → `0xC0000139` |

| Bilinen yarış | Kaynak |
|---|---|
| **`WlanCloseHandle` race gotcha** — kapatma sırasında aktif bildirim/dönüş | `E:\...\wireless-connect\AGENTS.md` (NevaConnect 01 D4 · 04 §V4 — "Windows WiFi parser + WlanCloseHandle race gotcha") |
| Callback'ten kayıt/iptal çağrısı → **deadlock** | MSDN WlanRegisterNotification ("the call never completing") |
| Eşzamanlı `WlanCloseHandle` + bildirim bekleme | Kilit altında thread join kalıbı (K014 node — "wireless-connect kalıbı korunur") |

### §5.4 Bellek disiplini

| Kural | Ayrıntı |
|---|---|
| Her out-buffer için zorunlu `WlanFreeMemory` | "should be released by calling the WlanFreeMemory function" (MSDN) |
| Sızıntı belirtisi | Süreç belleği monoton artar — her `WlanGetAvailableNetworkList` çağrısında |
| Çifte free | `WlanFreeMemory` sonrası işaretçi null'lanır (RAII deleter — 01 §4.1) |
| Callback stack buffer'ları | Callback'e gelen veri callback dönüşünde geçersizleşir — **kopyalanmadan saklanmaz** |

### §5.5 PSK / profil anahtarı loglama yasağı

| Kural | Gerekçe (kaynaklı) |
|---|---|
| `WlanGetProfile` çıktısı (profil XML) **log'a yazılmaz** | MSDN: yetki varsa "returns the **plain text key** in the **keyMaterial** element of the profile" — yani XML **anahtarı açık taşır** |
| Yetkisiz okumada bile dikkat | "returns the **encrypted key**" — şifreli olsa bile profil içeriği hassas sayılır |
| `netsh wlan ...` key=clear çıktıları da loglanmaz | MSDN `netsh wlan`: `keyMaterial` — "Specifies the network key or passphrase" |
| Uygulama log formatı | Yalnız SSID/profil adı/id (MAC opsiyonel) — anahtar, EAP kimliği, RADIUS sırrı **asla** |
| Karşılaştırma | Sızıntı sınıfı: log → tarama (CoreMusic kuralı: secret asla `.log`'a yazılmaz) |

### §5.6 Sağlamlık denetim listesi

| # | Denetim | İhlal belirtisi |
|---|---|---|
| 1 | Her `Wlan*` dönüş kodu kontrol ediliyor mu? | Sessiz başarısızlık → UI'da "bağlı" yanılgısı |
| 2 | XP'de beyaz liste dışına çıkış var mı? | §4.2 — 16 fonksiyon/4 opcode ihlali |
| 3 | Handle/pointer RAII ile mi? | Kapanış yolu unutulmuş → handle sızıntısı (`ERROR_REMOTE_SESSION_LIMIT_EXCEEDED`) |
| 4 | Bildirim kuyruğu lock-free + drain var mı? | Yarış (§5.3) |
| 5 | PSK/profil XML loglandı mı? | §5.5 ihlali — güvenlik olayı |

## §6 — Alternatifler ve komşular (neden Native Wifi?)

| Alternatif | Ne yapar | Artı | Eksi | Durum |
|---|---|---|---|---|
| **`netsh wlan`** (CLI) | `netsh wlan show networks / show interfaces / show drivers` — çıktı **metin** ayrıştırılır | Sıfır bağımlılık, her Windows'ta | Çıktı biçimi dile/sürüme göre değişir; kırılgan parse; task/child-process maliyeti | Kaynaklı (MSDN *netsh wlan*) · Proje kararı: "**`netsh` çıktısını ayrıştırmak yerine Native Wifi**" (NevaConnect 04 §2.2) · wireless-connect CLI/parser notu: `E:\...\wireless-connect\AGENTS.md` (04 §V4) |
| **WMI `MSFT_Wlan*`** | WMI sınıf ailesiyle sorgu (iddia) | Declarative sorgu | Resmi Microsoft Learn sayfası **bulunamadı** (2026-10-15 Learn search = 0 isabet) | ⚠️ VERIFICATION REQUIRED — doğrulanana kadar **kullanılmaz** |
| **IWLAN* (eski OEM/IHV arayüzü)** | (iddia) eski/sahipli arayüz | — | Kaynaklanamadı; MSDN'de karşılık bulunamadı | ⚠️ VERIFICATION REQUIRED — kapsam dışı |
| **WinRT `Windows.Devices.WiFi`** (`WiFiAdapter`) | Modern uygulamalar için WinRT yüzeyi | UWP/WinUI için doğal | **Windows 10'dan önce yok**: MSDN "Windows 10 (introduced in **10.0.10240.0**)" → **Win8+ DEĞİL** (dispatch tahmini düzeltildi); XP..Win11 tek binary hedefiyle çelişir; WinRT çağırmak ayrı ABI/dual-use senaryosu | Kaynaklı (MSDN UWP `WiFiAdapter`) · K014 node: "WinRT Wi-Fi API yok" sınır kararı |
| **Natif sürücü/DDI (OID_WDI)** | Sürücü seviyesi kontrol | Tam yetki | Kernel kodu, WHQL, Win11-only WiFiCx vb. | K001/K014 sınırı — bu node dışı (§1.2) |

### §6.1 Neden Native Wifi kazanır (karar)

| Gerekçe | Kanıt |
|---|---|
| **XP → Win11 tek API** | 01 doc export matrisi: 36 → 149; XP..19041 arası **−0 export** (§4.3) |
| User-mode, dokümante, imzalı sistem DLL'si | `wlanapi.dll` Windows bileşeni — §2 |
| Export matrisi ile feature gate mümkün | 01 §4.2 D1-D2 · 03 §4 resolver |
| netsh parse kırılganlığından kurtulur | 04 §2.2 kararı |
| WinRT/WMI XP'yi eleyip iki kod tabanı yaratır | §6 tablosu — tek binary hedefi (01 §4.2 D1) |
| Neva Connect zaten bu API üzerine tasarlandı | 01 §4.1 katmanlar (`wlabase`→`wlaconn`) |

## §7 — İlişkiler (bağımlılık / gittiği yer)

### §7.1 Vault bağımlılık tablosu

| Yön | Düğüm / dosya | İlişki |
|---|---|---|
| parent | `K000-isletim-sistemi/isletim-sistemi-api/ag-api` | Bu node'un alt-dalı (kademe-3 modül) |
| kardeş | `ag-api/socket-api-temelleri` | Soket temelleri — ağ sırtı; bu node onun **üstünde** bir yönetim düzlemi taşır |
| kardeş | `ag-api/udp-tcp-ve-tls-api` | Transport + TLS; WLAN bağlantısı kurulduktan sonra trafik bu katmandadır |
| kardeş | `ag-api/posix-socket-ve-winsock-karsilastirmasi` | Windows API yüzeyi karşılaştırması (Winsock ↔ bu: ikisi de Win32 user-mode) |
| kardeş | `ag-api/index.md` | Alt-dal künyesi — children listesi bu node'u içerir |
| gider → | `.ai/projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani` | Export matrisi (36→148), katman planı (§4.2/§4.3) |
| gider → | `.ai/projects/NevaConnect/02-coklu-exe-windows-sdk-derleme-mimarisi` | Çoklu-EXE + SDK derleme matrisi (bu dosya kapsam dışı) |
| gider → | `.ai/projects/NevaConnect/03-wlan-api-ozellik-kisitlama-tablosu` | İkili kapı, XP 20 fn + 9 kısıtlama, resolver (§4.2) |
| gider → | `.ai/projects/NevaConnect/04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama` | Wi-Fi 7/EHT kapsamı, netsh karşılaştırması (§3.7/§6) |
| gider → | `K010-network/connectivity/neva-connect-wlan-dallanma-mimarisi` | Bu API'nin uygulama mimarisi tüketicisi (WebSocket 9742) |
| gider → | `K001-donanım` | Sürücü/WDI katmanı — sınır (§1.2) |
| dış referans | `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` | Parser + `WlanCloseHandle` race gotcha (disk dışı, 01 §2 / 04 §V4 üzerinden) |

> Not: bu node yazılırken (paralel oturum) disk ölçümü 4 dosya (01-04) gösteriyordu;
> **05-neva-wlan-cekirdek-modul-tasarimi.md (666 satır) ve 06-test-ve-kalite-plani.md
> (560 satır) aynı günün paralel üretiminde diskte oluştu** — 2026-10-15 doğrulaması:
> NevaConnect altında artık 6 dosya (01-06).

### §7.2 Veri akışı özeti

~~~text
[socket/tcp-tls kardeşleri]        [bu node — Native Wifi temelleri]
   uygulama verisi yolunda             yönetim düzlemi
        │                                    │
        └──────────┬─────────────────────────┘
                   ▼
   [Neva Connect (01..04) → K014 neva-connect-wlan-dallanma-mimarisi]
                   │  kod (onay bekliyor)
                   ▼
              wlanapi.dll → wlansvc → sürücü (K001/K014 sınırı)
~~~

## §8 — Açık sorular

| # | Soru | Etki | Etiket |
|---|---|---|---|
| Q-N1 | WMI `MSFT_Wlan*` gerçek mi (resmi belge yok)? | §6 alternatif satırı | ⚠️ VERIFICATION REQUIRED |
| Q-N2 | IWLAN* iddiasının kaynağı ne? | §6 satırı | ⚠️ VERIFICATION REQUIRED |
| Q-N3 | XP'deki 16 "export var/destek belirsiz" fonksiyonun davranışı (03 §2.5) | XP siyah liste güvenliği | ⚠️ VERIFICATION REQUIRED (03 §7.1 #2 ile aynı) |
| Q-N4 | `WlanGetAvailableNetworkList2`'nin ilk export sürümü (MSDN=Win10, disk=Win8 delta) | List2 fallback frekansı | ⚠️ VERIFICATION REQUIRED (03 §7.1 #3) |
| Q-N5 | XP hedefi: XP SP3 dahil mi, Win7+ mi? (01 açık konu #4) | §4.1 beyaz listenin önemi | Beklemede (kullanıcı kararı) |
| Q-N6 | Win11 x86 export dump'ı (Win11 −2 private kuralı yalnız x64 dump ile doğrulandı) | §4.3 satırı | ⚠️ VERIFICATION REQUIRED (03 §7.1 #1) |
| Q-N7 | Wi-Fi 7 raporlamasının Native Wifi zinciri (EHT opcode/alanları) | 04 F2 kapsamı | ⚠️ VERIFICATION REQUIRED |

## §9 — Kaynaklar

### §9.1 Microsoft Learn (2026-10-15 · HEAD 200 doğrulandı)

| # | Kaynak | URL | Kullanıldığı yer |
|---|---|---|---|
| S1 | Native Wifi API Support on Windows XP (20 fn + 9 kısıtlama) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-wireless-lan-api-for-windows-xp-service-pack-2 | §4.1 |
| S2 | What's New in Native Wifi (Win7 HN · Win8 WFD · Win10 HS2.0) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/what-s-new-in-native-wifi | §4.3 |
| S3 | Native Wifi functions (fonksiyon indeksi) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/native-wifi-functions | §2.5 |
| S4 | WLAN_REASON_CODE (Wlanapi.h) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-reason-code | §3.5 |
| S5 | DOT11_PHY_TYPE enumeration (Windot11.h) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/dot11-phy-type | §3.7 |
| S6 | WlanOpenHandle (sürüm 1/2 · composite · WLAN_API_MAKE_VERSION · XP WZC) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanopenhandle | §2.3 |
| S7 | WlanEnumInterfaces | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanenuminterfaces | §3.1 |
| S8 | WlanScan | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanscan | §3.2.1 |
| S9 | WlanGetAvailableNetworkList (buffer → WlanFreeMemory) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetavailablenetworklist | §3.2.2 · §5.4 |
| S10 | WLAN_AVAILABLE_NETWORK (struct) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_available_network | §3.2.2 |
| S11 | WlanGetNetworkBssList (min Vista · XP ERROR_NOT_SUPPORTED) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetnetworkbsslist | §3.2.3 · §4.2 |
| S12 | WLAN_BSS_ENTRY (struct) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_bss_entry | §3.2.3 |
| S13 | WlanConnect | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanconnect | §3.3 |
| S14 | WLAN_CONNECTION_PARAMETERS (XP: yalnız profile modu) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_connection_parameters | §3.3.1 |
| S15 | WLAN_CONNECTION_MODE (enum) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_connection_mode | §3.3.1 |
| S16 | WlanRegisterNotification (XP/Netman · deadlock · auto-undo) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanregisternotification | §3.4 · §5.3 |
| S17 | WLAN_NOTIFICATION_ACM (enum) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_notification_acm-r1 | §3.4 |
| S18 | WLAN_INTERFACE_STATE (enum — 8 değer) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_interface_state-r1 | §3.1 |
| S19 | WLAN_INTF_OPCODE (enum) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ne-wlanapi-wlan_intf_opcode | §3.6 |
| S20 | WlanQueryInterface (XP: 4 opcode) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanqueryinterface | §3.6 |
| S21 | WlanSetProfile (ERROR_ACCESS_DENIED) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlansetprofile | §3.5 · §5.1 |
| S22 | WlanGetProfile (keyMaterial düz metin/şifreli) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetprofile | §5.5 |
| S23 | WlanReasonCodeToString | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanreasoncodetostring | §3.5 |
| S24 | WlanFreeMemory | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanfreememory | §2.5 |
| S25 | COM Error Codes (Winerror.h) — `ERROR_NDIS_DOT11_POWER_STATE_INVALID = 0x80342002` | https://learn.microsoft.com/en-us/windows/win32/com/com-error-codes-7 | §5.2 |
| S26 | Wireless LAN overview in Windows Server (WLAN AutoConfig / wlansvc) | https://learn.microsoft.com/en-us/windows-server/networking/technologies/wlan/wireless-networking | §2.1 |
| S27 | netsh wlan (CLI) | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/netsh-wlan | §5.5 · §6 |
| S28 | Dual-STA query and configuration (Win11 SDK 22000.194) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/dual-sta-query-config | §4.3 |
| S29 | What's new in Windows 11, version 24H2 for IT pros (Wi-Fi 7) | https://learn.microsoft.com/en-us/windows/whats-new/whats-new-windows-11-version-24h2 | §4.3 |
| S30 | WinRT `WiFiAdapter` (introduced 10.0.10240.0) | https://learn.microsoft.com/en-us/uwp/api/windows.devices.wifi.wifiadapter | §6 |

### §9.2 Vault (disk) kaynakları

| # | Dosya | Ne verir |
|---|---|---|
| D1 | `.ai/projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani.md` | Export matrisi (36→148, −0 omurgası), katman planı, D1-D6 kararları |
| D2 | `.ai/projects/NevaConnect/03-wlan-api-ozellik-kisitlama-tablosu.md` | XP 20 fn beyaz liste + 9 kısıtlama, ikili kapı (Y1-Y3), 16 fonksiyon siyah listesi |
| D3 | `.ai/projects/NevaConnect/04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama.md` | Wi-Fi 7 (Win11 24H2), EHT=11, netsh karşılaştırma kararı |
| D4 | `.ai/architecture/K010-network/connectivity/neva-connect-wlan-dallanma-mimarisi.md` | Uygulama mimarisi, WinRT kapsam dışı kararı, gotcha kayıtları |
| D5 | `.ai/architecture/K000-isletim-sistemi/isletim-sistemi-api/ag-api/` kardeşleri | Format/ton + ağ sırtı bağlamı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Commit:** yok — commit kararı orkestratöre aittir.
