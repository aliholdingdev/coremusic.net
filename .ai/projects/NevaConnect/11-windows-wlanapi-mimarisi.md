---
title: "Neva Connect — Windows WLAN API (wlanapi.dll) Mimarisi (10 DLL PE import/export analizi + MS Learn katman modeli)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "pe_arch.py PE analizi (10 DLL, disk) + exports.json + nt5src XP + MS Learn (8 URL HEAD-200)"
updated: 2026-10-15
---

# Neva Connect — Windows WLAN API (wlanapi.dll) Mimarisi

> **Amaç:** `wlanapi.dll`'in (Native Wi-Fi API) Windows sürümleri boyunca **gerçek
> iç mimarisini** disk kanıtıyla (PE import/export/bölüm/mitigasyon analizi, 10 DLL)
> çıkarıp, Microsoft'un resmi katman modeliyle (MS Learn) harmanlamak. 01 (export
> matrisi), 05 (neva-wlan çekirdek), 07 (XP WZC) ve 10 (registry) dokümanlarının
> **mimari çatısını** oluşturur.
>
> **Zero-hallucination:** Her tablo satırı `pe_arch.py`/`exports.json` çıktılarına
> veya dosya:satır/URL kanıtına dayanır. Yorumlar ayrı paragraflarda; doğrulanamayan
> iddialar `⚠️ VERIFICATION REQUIRED` işaretlidir. **KOD YOKTUR** (01–10 ile aynı durum).
>
> **Analiz araçları (yeniden üretilebilir):**
> `C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\pe_arch.py` (pefile 2024.8.26)
> → `pe_arch.json` · `export_scan.py` → `exports.json` · DLL'ler `wlanapi-re\dlls\`
> (10 adet, MD5 önceki turlarda doğrulandı — 01 doc).

---

## §1 Kapsam & İlişki

| Bu doc | Kapsam dışı |
|---|---|
| wlanapi.dll katman konumu · PE iç yapısı (bölüm/import/mitigasyon) · sürüm bazlı yapısal değişiklikler · RPC/notify/security mimarisi · export aile mutabakatı | Export listesinin tam dökümü (01 doc) · feature-gate kuralları (03) · neva-wlan modül tasarımı (05) · XP WZC kaynak detayı (07) · registry değerleri (10) |

**Bağlantı:** 01 → 03 → **11 (bu doc)** → 05 → 07/08/09/10 · vault node
`ag-api/native-wifi-api-temelileri.md` (kavramsal API katmanı) ile tamamlayıcıdır:
o doc **kavram** (oturum, negotiated version, durumlar), bu doc **gerçek ikili yapı**.

---

## §2 Kavramsal Katman Mimarisi (MS Learn — HEAD-200 doğrulanmış)

### §2.1 Uygulamadan donanıma katmanlar (resmi model birleşimi)

```
┌─────────────────────────────────────────────────────────────────┐
│ Uygulama (neva-wlan.exe vb.)  — wlanapi.h / Wlanapi.lib         │
├─────────────────────────────────────────────────────────────────┤
│ wlanapi.dll  (Native Wi-Fi API)  ← BU DOC'un konusu             │
│   XP: WZCSAPI.DLL sarmalayıcısı (§4.1)  ·  Vista+: async RPC    │
├─────────────────────────────────────────────────────────────────┤
│ WlanSvc (WLAN AutoConfig, svchost + wlansvc.dll)                │
│   ACM — Auto Configuration Module (tarama/profil/bağlantı)       │
│   [Wcmsvc — Windows Connection Manager: bağlantı koordinasyonu] │
├─────────────────────────────────────────────────────────────────┤
│ MSM — Media Specific Module (güvenlik/assoc/auth durum makineleri)│
│   SecMgr · AuthMgr (802.1X)                                     │
├─────────────────────────────────────────────────────────────────┤
│ Native Wi-Fi filtresi (nativewifip) → 802.11 ↔ 802.3 çevirisi   │
├─────────────────────────────────────────────────────────────────┤
│ Sürücü modeli: Native 802.11 IM/miniport (eski) → WDI →         │
│ Win11: WiFiCx                                                   │
├─────────────────────────────────────────────────────────────────┤
│ Kablosuz miniport sürücüsü + donanım                            │
└─────────────────────────────────────────────────────────────────┘
```

**Kaynak eşlemesi (her katman resmi sayfadan):**

| Katman | MS Learn kaynağı (HEADE-200) | Alıntı özeti |
|---|---|---|
| ACM (Vista+) / WZC (XP) | [about-the-acm-architecture] | "Windows XP SP3 and Wireless LAN API for XP SP2 use the **Wireless Zero Configuration (WZC)** service instead" — **XP/modern ayrımı resmi ağızdan** |
| Native Wi-Fi API | [about-the-native-wifi-api] | API "wireless network connectivity and wireless profile management" sağlar; Ad Hoc API bunun üzerindedir |
| Native 802.11 yığını | [native-802-11-software-architecture] | UI → ACM → MSM → IM/miniport + IHV DLL; "Native 802.11 interface **deprecated in Windows 10** → use **WDI**" |
| Servis+görev listesi | [wireless-network-connectivity-issues] | Wcmsvc (koordinasyon) · WlanSvc (tarama/profil) · MSM (güvenlik durumu) · "Native Wi-Fi stack … user-mode WLAN AutoConfig service" · "current adapters use **WDI** or, on Windows 11, **WiFiCx**" |
| Profil deposu | [wlan-profileschema-schema] | Profil kaynağı: **GPO agent, netsh, UI, USB flash config**; "stored in the profile store"; kök `WLANProfile`, ns `https://www.microsoft.com/networking/WLAN/profile/v1` |
| WlanSetProfile | [nf-wlanapi-wlansetprofile] | Min: **Vista, XP SP3** (redistributable: Wireless LAN API for XP SP2); WPS/WPA-None/802.1X-guest profilleri **desteklenmez** |
| Otomatik konfig parametresi | [nf-wlanapi-wlansetautoconfigparameter] | Opcode'ler `show_denied_networks`/`allow_explicit_creds`/`block_period`/`allow_virtual_station_extensibility`; yazma izni **DACL içinde `WlanSetAutoConfigParameter` → WLAN_WRITE_ACCESS ACE** |

> **Mimari ders (05 doc ile):** neva-wlan'daki katmanlar bu modelin yansımasıdır:
> resolver+handle (wlanapi katmanı) · kuyruk+notify (RPC callback karşılığı) ·
> servis-bağımlılık kontrolü (WlanSvc ACPI).

### §2.2 XP katman farkı (resmi + disk kanıtı birlikte)

| | XP (+SP2 API/SP3) | Vista+ |
|---|---|---|
| Servis | **WZC** (`wzcsvc`, WZCSAPI RPC client) | **ACM** (`WlanSvc`/wlansvc.dll) |
| API DLL | wlanapi.dll → **WZCSAPI.DLL** (§4.1) | wlanapi.dll → **RPCRT4 async** (§4.2) |
| Profil deposu | registry **binary blob** (10 §2.3) | XML profile store (§2.1 profil kaynağı) |
| Destek ID'si | `WZCEnumInterfaces` … (§5.3) | `WlanEnumInterfaces` … |

---

## §3 Sürüm Bazlı PE Yapısı (10 DLL disk analizi)

### §3.1 Temel matris

| DLL | Tarih (PE TimeDateStamp) | Makine | Export (toplam) | Import (DLL/fn) | DllCharacteristics |
|---|---|---|---|---|---|
| xp-x86 5.1.2600.5512 | 2008-04-14 | 0x14c (x86) | **36** | 9 / 138 | **0x0000** |
| win7-x64 6.1.7600 | 2009-07-14 | 0x8664 | **48** | 14 / 76 | 0x0140 |
| win7-x86 6.1.7600 | 2009-07-14 | 0x14c | **48** | 15 / 75 | 0x0140 |
| win8-x64 6.2.9200 | 2012-07-25 | 0x8664 | **153** | 23 / 149 | 0x0140 |
| win8-x86 6.2.9200 | 2012-07-25 | 0x14c | **153** | 20 / 126 | 0x0140 |
| win81-x64 6.3.9600 | 2014-01-27 | 0x8664 | **173** | 20 / 125 | 0x0160 |
| win81-x86 6.3.9600 | 2014-01-27 | 0x14c | **173** | 19 / 123 | 0x0140 |
| win10-x64 10.0.10240 | 2015-07-10 | 0x8664 | **220** | 23 / 136 | 0x4160 |
| win10-x86 10.0.10240 | 2015-07-10 | 0x14c | **220** | 22 / 133 | 0x4140 |
| local 10.0.19041 x64 | 2004-08-17 ⚠️ | 0x8664 | **258** | 37 / 180 | 0x4160 |

> ⚠️ **19041 tarihi:** `2004-08-17` gerçek derleme tarihi değil, Microsoft'un
> **deterministik/reproducible build** sabit damgasıdır (yorum — ⚠️ VERIFICATION
> REQUIRED; 10240'da gerçek tarih 2015-07-10 görülür).
>
> **x86/x64 mutabakatı:** Export sayıları iki mimaride **birebir aynı** (36/48/153/
> 173/220) — API yüzeyi mimariden bağımsızdır; fark yalnız import/bölüm düzeninde.

### §3.2 Mitigasyon bayrakları (DllCharacteristics çözümlemesi)

| Değer | Bitler (winnt.h) | Yorum |
|---|---|---|
| XP 0x0000 | — | **ASLR/DEP bayrağı YOK** (XP dönemi normu) |
| Win7/8 x86-x64 0x0140 | `DYNAMIC_BASE(0x40)` + `NX_COMPAT(0x100)` | ASLR + DEP |
| Win8.1 x64 0x0160 | + `HIGH_ENTROPY_VA(0x20)` | 64-bit tam ASLR |
| Win10 x64 0x4160 | + `GUARD_CF(0x4000)` | **Control Flow Guard** |
| Win10 x86 0x4140 | `DYNAMIC_BASE+NX+GUARD_CF` | CFG x86'ta da var |

> **neva-wlan etkisi (E6):** XP hedefinde CFG/ASLR **beklenmez** — 02 doc v141_xp
> derleme ayarları bu ikili gerçekle çelişmez (XP altında /DYNAMICBASE:NO gerekmez,
> DLL zaten işaretsizdir; uygulama tarafı 06 §XP smoke'ta).

### §3.3 Bölüm düzeni (sürüm içi evrim)

| DLL | Bölümler |
|---|---|
| XP x86 | `.text, .data, .rsrc, .reloc` (`.rdata`/`.idata` YOK — eski MSVC düzeni) |
| Win7 x86 | `.text, .data, .rsrc, .reloc` |
| Win7 x64 | + `.pdata` (x64 unwind zorunlu) |
| Win8/8.1 | + `.idata` (açık import bölümü) |
| Win10 | + `.didat` (**delay-load import tanımlayıcıları**) |

> **`.didat` bulgusu:** Win10'da delay-load yoğun kullanılıyor → **import tablosu
> yalnız erken (eager) bağımlılıkları gösterir**; gecikmeli yüklenen DLL'ler tabloda
> görünmez (§3.4 dürüstlük notu).

### §3.4 Import tablosu dürüstlük notu

- Bu doc'un import sayıları **yalnız eager importlardır**; `ResolveDelayLoadedAPI`/
  `DelayLoadFailureHook` (Win8+, `api-ms-win-core-delayload-*`) ile yüklenen
  bağımlılıklar tabloda yoktur.
- Dolayısıyla "Win7'nin 14 DLL'si" ile "Win10'un 37 DLL'si" karşılaştırması **alt
  sınır** taşır (gerçek çalışma-zamanı yüzeyi daha geniş olabilir).
- XP'de delay-load altyapısı importta görünmüyor (9 DLL eager) — ama `LoadLibraryExA`
  + `GetProcAddress` (Win7) / `GetModuleHandleW` (Win10) importları **çalışma-zamanı
  yükleme** kapısı olduğunu gösterir (feature-gate 03 ile uyumlu).

---

## §4 İletişim (IPC) Mimarisi

### §4.1 XP: wlanapi → WZCSAPI → `\pipe\wzcsvc` (disk kanıtlı)

XP x86 (5.1.2600.5512) import tablosu — **9 DLL / 138 fn**:

| Import DLL | fn | Rol |
|---|---|---|
| **WZCSAPI.DLL** | **19** | **WZC RPC client — asıl servis teması** (WZCEnumInterfaces, WZCEapolQueryState, WZCEapolGetInterfaceParams, WZCGetSPResModule, ReadEapcfgList, EapcfgNodeFromKey, DestroyEapcfgNode, DtlDestroyList, WZCPassword2Key, WZCProviderCreateUserProperties, … + ordinal #60/#61) |
| KERNEL32 | 62 | senkron/süreç (QueryPerformanceCounter, CriticalSection ×n, CreateThread…) |
| ADVAPI32 | 17 | **registry** (RegOpenKeyExW/RegEnumKeyExW/RegQueryInfoKeyW/RegDeleteValueW) + **ETW** (RegisterTraceGuidsW) + **token** (OpenThreadToken/RevertToSelf/SetThreadToken) |
| msvcrt | 18 | C çalışma zamanı (+ C++ `??2/??3` operator new/delete, `_CxxThrowException`) |
| OLEAUT32 | 7 | **COM/variant** (VariantChangeType, SysAllocString, VarUI4FromStr) |
| ole32 | 7 | **COM** (CoInitializeEx, CoCreateInstance, StringFromGUID2) |
| RPCRT4 | **1** | yalnız `UuidFromStringW` — **RPC'nin kendisi WZCSAPI içinde** |
| USER32 | 2 | LoadStringW, CharNextW (kaynak metin) |
| ntdll | 5 | wcscpy/memset/… (CRT yedeği) |

> **Mimari sonuç:** XP wlanapi.dll **ince bir sarmalayıcıdır** — RPC'yi kendisi
> taşımaz; WZCSAPI'ye (07 doc `wzcsapi.dll` 33 export + `wzc.idl` UUID 621dff68,
> pipe `\pipe\wzcsvc`) delegasyon yapar. 07 §izomorfizm tablosunun **ikili kanıtı**.
>
> **XP'de COM (ole32/ole32) ne işe yarar?** → muhtemelen EAP yapılandırması
> (Provisioning/EAPConfig COM nesneleri; `EapcfgNodeFromKey` çağrısı eşlik eder).
> ⚠️ VERIFICATION REQUIRED (decompile değil — import seviyesi kanıt).

### §4.2 Vista+: doğrudan async RPC (RPCRT4)

| Sürüm | RPCRT4 fn | Kritik importlar |
|---|---|---|
| Win7 x64 | 14 | `NdrClientCall3` + `Ndr64AsyncClientCall` (x64 NDR64) · `RpcAsyncCompleteCall/GetCallStatus/InitializeHandle` · `RpcBindingSetAuthInfoExW` |
| Win7 x86 | 14 | `NdrAsyncClientCall`/`NdrClientCall2` (32-bit NDR) + aynı async set |
| Win8/8.1 | 16 | async set korunur; `RpcAsyncCancelCall` eklenir |
| Win10 10240 | 16 | `NdrClientCall3` + `Ndr64AsyncClientCall` (x64) |
| Win10 19041 | 16 | + `RpcBindingSetOption`, `RpcEpResolveBinding` … |

> **Çıkarım (kanıt: import listesi):** Vista+'da wlanapi **kendisi RPC istemcisidir**;
> WlanSvc'ye bağlanır (`RpcStringBindingComposeW` + `RpcBindingFromStringBindingW` +
> `RpcBindingSetAuthInfoExW` = kimlik doğrulamalı binding). **Asenkron RPC**
> (`RpcAsync*`) bildirim (notification) ve uzun süren çağrılar için kullanılır — §7.
>
> **x86/x64 NDR farkı:** 64-bit `Ndr64AsyncClientCall`, 32-bit `NdrAsyncClientCall`
> / `NdrClientCall2/4` — **aynı arayüzün iki farklı marshalling yolu** (neva-wlan
> XP x86 hedefinde yalnız 32-bit yolunu görür).

### §4.3 Servis denetimi importları (Win10+)

| Import | 10240 | 19041 | Anlam |
|---|---|---|---|
| `OpenSCManagerW`, `OpenServiceW`, `CloseServiceHandle` | ✓ | ✓ | **WlanSvc'ye servis denetimi** (açık/kapalı durum sorgu) |
| `ControlService` | ✓ | ✓ | Servise **kontrol komutu** (duraklat/durdur/başlat tetikleme) |
| `WTSGetActiveConsoleSessionId` | ✓ (legacy dll) | ✓ | **Oturum kimliği** — bildirimin hangi oturuma gideceği |

> Win7/8/8.1'de bu importlar **YOK** → servis denetimi katmanı Win10'da eklenmiş.
> ⚠️ Amaç yorumu (`ControlService` = WlanSvc talep-upon-start tetiklemesi olabilir)
> VERIFICATION REQUIRED (decompile gerekir).

---

## §5 Export Yüzeyi: Aileler ve Mutabakat (01 doc ile)

### §5.1 Toplam ↔ Wlan* mutabakatı (01 doc sayımları = `Wlan*` ailesi)

| Sürüm | Toplam export | `Wlan*` | Diğer | Diğerlerin kimliği |
|---|---|---|---|---|
| XP x86 | 36 | **36** | 0 | — (yalnız Native Wi-Fi ailesi) |
| Win7 x64/x86 | 48 | **48** | 0 | — (P2P/WFD export'u Win8'de başlar) |
| Win8 x64/x86 | 153 | **103** | 50 | 48 `WFD*` (Wi-Fi Direct) + 2 `QueryNetcon*` |
| Win8.1 x64/x86 | 173 | **108** | 65 | `WFD*` ailesi büyür + QueryNetcon |
| Win10 10240 x64/x86 | 220 | **121** | 99 | WFD* + … |
| Win10 19041 x64 (yerel) | 258 | **148** | 110 | WFD* (LowPriv/OOB/NFC dalı) + QueryNetcon×2 + `WiFiDisplay*`×3 |

> **01 doc ile mutabakat:** 01'ün matrisi (36→48→103→108→121→148) **`Wlan*` önekli**
> export'ları sayar ve `export_scan.py` birebir bunu üretir (103/108/121/148 doğrulandı).
> Bu doc'un **toplam** sayıları (153/173/220/258) **daha büyüktür** çünkü `WFD*` ailesi
> `Wlan*` önekinde değildir. İki sayı da doğrudur; **kullanım yeri farklıdır**:
> feature-gate (03) `Wlan*` tabanını, DLL yüzeyi/allowlist planı toplamı kullanır.

### §5.2 Export aile coğrafyası (yerel 19041, 258 export)

| Aile | Adet | İçerik örnekleri |
|---|---|---|
| `Wlan*` | 148 | Çekirdek API (§5.3) + `WlanRegisterDeviceServiceNotification`, `WlanRegisterVirtualStationNotification` |
| `WFD*` | 107 | Wi-Fi Direct: `WFDOpenHandle`, `WFDStartConnectorPairWithOOB`, `WFDParseOOBBlob`, `WFDPairWithDeviceAndOpenSessionInt`, `WFDLowPriv*` (18 adet!), `WFDConfigureFirewallForSessionInt` |
| `WiFiDisplay*` | 3 | `WiFiDisplayResetSinkStateInt`, `WiFiDisplaySetSinkClientHandleInt`, `WiFiDisplaySetSinkStateInt` |
| `QueryNetcon*` | 2 | `QueryNetconStatus`, `QueryNetconVirtualCharacteristic` |

> **`*Int` soneki:** iç (internal) sürümler — resmi MSDN belgesinde yer almayan,
> eşleştirilmiş `WFDOpenHandle`/`WFDOpenHandleInt` çiftleri gibi. **Allowlist
> stratejisi (03):** resmi MSDN beyaz listesi ile ikili tablo ayrışır; `*Int`
> export'ları **kullanılmaz** (belgesiz) → 03 §kural.

### §5.3 Çekirdek 36 (XP = başlangıç beyaz listesi, tam döküm — disk)

```
WlanAllocateMemory, WlanCloseHandle, WlanConnect, WlanDeleteProfile,
WlanDisconnect, WlanEnumInterfaces, WlanExtractPsdIEDataList, WlanFreeMemory,
WlanGetAvailableNetworkList, WlanGetFilterList, WlanGetInterfaceCapability,
WlanGetNetworkBssList, WlanGetProfile, WlanGetProfileCustomUserData,
WlanGetProfileList, WlanGetSecuritySettings, WlanIhvControl, WlanOpenHandle,
WlanQueryAutoConfigParameter, WlanQueryInterface, WlanReasonCodeToString,
WlanRegisterNotification, WlanRenameProfile, WlanSaveTemporaryProfile, WlanScan,
WlanSetAutoConfigParameter, WlanSetFilterList, WlanSetInterface, WlanSetProfile,
WlanSetProfileCustomUserData, WlanSetProfileEapUserData,
WlanSetProfileEapXmlUserData, WlanSetProfileList, WlanSetProfilePosition,
WlanSetPsdIEDataList, WlanSetSecuritySettings
```

> **Kritik:** `WlanRegisterNotification` **XP'de de vardır** (36 liste içinde) —
> bildirim API'si ilk günden beri çekirdektir. `WlanSaveTemporaryProfile`/
> `WlanRenameProfile` XP'de var, sonraki sürümlerde ailede kalır mı → 01 export
> diff'i (01 doc §geçişler) esastır.

### §5.4 Çekirdek 36'nın işlevsel gruplaması (mimari okuma)

| Grup | Export'lar (XP 36 içinden) | neva-wlan akışı (05) |
|---|---|---|
| Oturum/handle | `WlanOpenHandle`, `WlanCloseHandle`, `WlanAllocateMemory`, `WlanFreeMemory` | oturum aç/kapat + bellek (RAII) |
| Arayüz/tarama | `WlanEnumInterfaces`, `WlanGetInterfaceCapability`, `WlanScan`, `WlanGetAvailableNetworkList`, `WlanGetNetworkBssList`, `WlanQueryInterface`, `WlanSetInterface` | **wifi_list** + **wifi_scan** |
| Bağlantı | `WlanConnect`, `WlanDisconnect` | **wifi_connect** |
| Profil | `WlanGetProfile`, `WlanGetProfileList`, `WlanSetProfile`, `WlanDeleteProfile`, `WlanSetProfileList`, `WlanSetProfilePosition`, `WlanRenameProfile`, `WlanSaveTemporaryProfile`, `WlanGetProfileCustomUserData`, `WlanSetProfileCustomUserData`, `WlanSetProfileEapUserData`, `WlanSetProfileEapXmlUserData` | profil deposu (XML/XP binary ayrımı — 10 §2.3) |
| Bildirim | `WlanRegisterNotification` | **wifi_event** (05 §kuyruk) |
| Otomatik-konfig | `WlanQueryAutoConfigParameter`, `WlanSetAutoConfigParameter` | mod/açık-kapalı |
| Güvenlik/ayar | `WlanGetSecuritySettings`, `WlanSetSecuritySettings`, `WlanGetFilterList`, `WlanSetFilterList`, `WlanExtractPsdIEDataList`, `WlanSetPsdIEDataList` | IE/profil metadata |
| IHV | `WlanIhvControl` | kapsam dışı (04) |
| Yardımcı | `WlanReasonCodeToString` | hata metni (05 §Level tablosu) |

### §5.5 Export delta zinciri (01 doc ile birleşik matris)

| Geçiş | Wlan* delta | İçerik (01 doc §geçişler) |
|---|---|---|
| XP 36 → Win7 48 | **+12** | HostedNetwork ×11 (soft-AP/hotspot) + `WlanRegisterVirtualStationNotification` |
| Win7 48 → Win8 103 | **+55** | Wi-Fi Direct/PLAP/ConnectEx/LowPriv/AP ailesi (WFD export'ları bu sürümde **toplam tabloya** da girer — §5.1) |
| Win8 103 → Win8.1 108 | **+5** | P2G/oturum genişlemesi (01 doc diff) |
| Win8.1 108 → Win10 10240 121 | **+13** | ANQP (Hotspot 2.0) + Wcm köprüleri |
| Win10 10240 121 → 19041 148 | **+27** | FTM (ranging), device-service, movement — **her geçişte −0 export yok** (01 §matris kuralı) |

> **Toplam export zinciri (bu doc):** 36 → 48 → 153 → 173 → 220 → 258 (WFD ailesi
> Win8'de dahil olduğundan toplam atlaması büyüktür: +105 = WFD 48 + Wlan* 55 + QueryNetcon 2).

---

## §6 Yardımcı Bileşen Geçmişi (CRT + API set + alt DLL'ler)

| Sürüm | C çalışma zamanı | Dikkat çeken import |
|---|---|---|
| XP | `msvcrt.dll` (18 fn, C++ operator dahil) | **WZCSAPI.DLL** (§4.1) · ADVAPI32 registry+trace+token |
| Win7 | `msvcrt.dll` (13) + KERNEL32 doğrudan (10 fn) | **`wlanutil.dll` → `WlanStringToSsid`** (tek fn!) · `LoadLibraryExA`+`GetProcAddress` |
| Win8/8.1 | `msvcrt.dll` (34-35, `__CxxFrameHandler3` = C++11 EH) | API-set `api-ms-win-core-*` (20-23 dll) · `FindResourceExW`/`LoadResource` (Win8: resource yükleme) |
| Win10 10240 | `msvcrt.dll` (37) | `RegGetValueW` (1 registry fn) |
| Win10 19041 | **`api-ms-win-crt-*` (UCRT, 44 fn)** | CRT göçü tamamlandı: 39 fn `crt-private` + runtime/string |

> **`wlanutil.dll` (Win7+):** wlanapi'nin SSID dönüşümünü tek fonksiyonla
> (`WlanStringToSsid`) dışarı devrettiği yardımcı DLL. XP importunda **YOK**
> (XP'de SSID dönüşümü içte). neva-wlan 05 doc'taki SSID yardımcı katmanının
> Windows karşılığı.
>
> **API-set göçü:** Win8'de başlar (`ApiSetQueryApiSetPresence` her sürümde 1 fn),
> Win10 19041'te CRT de API-set'e taşınır. 02 doc derleme matrisindeki
> `_WIN32_WINNT` bu göçün **hedef SDK** tarafını belirler (doğrudan etkisi yok —
> importlar DLL'in kendi seçimidir, uygulamanınki değil).

---

## §7 Bildirim (Notification) Mimarisi

### §7.1 Kanıt zinciri

| Kanıt | Kaynak | Ne gösterir |
|---|---|---|
| `WlanRegisterNotification` export (XP → 19041, hep var) | §5.3 + 01 | Bildirim API'si kalıcı çekirdek |
| `RpcAsyncInitializeHandle` + `Ndr64AsyncClientCall` + `RpcAsyncCompleteCall/GetCallStatus` (Win7+) | §4.2 | **Asenkron RPC = bildirim taşıma kanalı** |
| `ntdll` → `RtlSubscribeWnfStateChangeNotification`, `NtQueryWnfStateData`, `RtlUnsubscribeWnf*` (**Win8+**; Win7'de YOK) | pe_arch.json | Win8+ **WNF (Windows Notification Facility)** kullanımı — olay yayını için ek kanal |
| `api-ms-win-core-threadpool-*` **10 fn** (yalnız 19041: `CreateThreadpoolWait/Timer`, `SetThreadpoolWait`, cleanup group…) | pe_arch.json | 19041'te **threadpool ile callback dağıtımı** |
| `WTSGetActiveConsoleSessionId` (Win7+ legacy dll) | §4.3 | Bildirimin oturum bağlamı |
| `EnterCriticalSection`×n + `CreateEventW`/`SetEvent` (tüm sürümler) | pe_arch.json | Klasik senkronizasyon (kullanıcı callback'i koruması) |

### §7.2 Sürüm özeti

| Sürüm | Ana kanal | Yardımcı |
|---|---|---|
| XP | WZCSAPI RPC bildirimi (WZC daemon → client) ⚠️ (WZC idl callback detayı 07 doc) | ETW (`RegisterTraceGuidsW`) |
| Win7 | async RPC callback | ETW |
| Win8/8.1 | async RPC callback | **+ WNF** |
| Win10 10240 | async RPC + WNF | ETW + oturum |
| Win10 19041 | async RPC + WNF | **+ threadpool dağınık dağıtımı** |

> **neva-wlan ile bağ (05 §lock-free kuyruk):** Windows'un kendi tasarımı da
> **"callback'i servis thread'inde çağırma → kuyruğa al → uygulama thread'inde
> boşalt"** modeline benzer (19041 threadpool kanıtı). 05'teki SPSC kuyruk +
> tek dispatch thread bu mimarinin uygulama tarafı karşılığıdır; `WlanCloseHandle`
> öncesi unregister+drain sırası (05) RPC async handle kapatış sırasıyla uyumludur.
>
> ⚠️ **VERIFICATION REQUIRED:** wnfnin wlanapi'de tam hangi olaylarda kullanıldığı
> ve callback thread'inin hangi havuzdan geldiği decompile gerektirir (04 RE
> kapsamı olabilir).

---

## §8 Güvenlik & Yetki Mimarisi

| Katman | Kanıt | Açıklama |
|---|---|---|
| ACE/DACL üretimi | Win7+ import: `AddAccessAllowedAce`, `AddAccessDeniedAce`, `InitializeAcl`, `GetSecurityDescriptorDacl`, `MakeSelfRelativeSD`, `SetSecurityDescriptorDacl`, `GetLengthSid`, `EqualSid`, `IsValidSid`, `CreateWellKnownSid` (Security-Base **15 fn**, tüm modern sürümler) | wlanapi **kendi güvenlik nesnelerini kurar** (secure object/ACL) |
| Yazma izni | MS Learn [wlansetautoconfigparameter] | Bazı opcode'ler için DACL'de **`WlanSetAutoConfigParameter` → WLAN_WRITE_ACCESS** ACE'si aksi halde `ERROR_ACCESS_DENIED` |
| GPO üstünlüğü | MS Learn [about-the-acm-architecture] + [wlansetautoconfigparameter] | GPO politikaları **okunabilir**, per-interface/per-user'ı geçer; politika varken değişiklik engellenir |
| Token yükseltme (XP) | XP import: `OpenThreadToken`, `RevertToSelf`, `SetThreadToken` | XP wlanapi **thread token'ını değiştirir** (impersonation) — EAP/registry erişimi için ⚠️ (amaç yorumu) |
| Kimlik doğrulamalı RPC | `RpcBindingSetAuthInfoExW` (Win7+) | Binding'e auth bilgisi bağlanır |
| Servis denetimi | `OpenSCManagerW`/`OpenServiceW`/`ControlService` (Win10+) | §4.3 |
| Düşük yetkili P2P | 19041 export: 18 adet `WFDLowPriv*` (§5.2) | Wi-Fi Direct'in **düşük yetkili** varyantı ayrı export ailesi |

> **neva-wlan etkisi (E4):** XP hedefinde impersonation (`SetThreadToken`) XP
> panel davranışının (08 doc) ötesinde API içi bir davranıştır — neva-wlan XP'de
> token manipulation **YAPMAZ**; standart çağrılarla yetinir. Düşük yetkili
> kullanıcı senaryoları 06 §negatif testlerde (N*) kapsanır.

---

## §9 Registry Etkileşimi (10 doc ile katmanlaşma)

| Sürüm | Registry import kanıtı | 10 doc ile ilişki |
|---|---|---|
| XP | ADVAPI32: `RegOpenKeyExW`, `RegEnumKeyExW`, `RegQueryInfoKeyW`, `RegDeleteValueW` (+13 fn liste) — **okuma + silme** | XP wlanapi EAP/registry okumalarını **doğrudan** yapar (Rasman/PPP/EAP — 10 §3.3) |
| Win7 | Registry import **YOK** | Her şey RPC'ye taşındı |
| Win8 x64 | **8 fn** (tam set: `RegCreateKeyExW`, `RegSetValueExW`, `RegDeleteValueW`, `RegEnumKeyExW`, `RegEnumValueW`, `RegQueryInfoKeyW`, `RegOpenKeyExW`, `RegCloseKey`) — **yazma dahil** | Win8'de doğrudan registry yazımı var ⚠️ (hedefi bilinmiyor — VERIFICATION REQUIRED) |
| Win8 x86 | Registry import **YOK** (x64 ile ayrışıyor!) | Mimari-özel davranış ⚠️ |
| Win8.1 | YOK | — |
| Win10 10240 | `RegGetValueW` (1) | salt okuma |
| Win10 19041 | `RegQueryValueExW`, `RegOpenKeyExW`, `RegCloseKey`, `RegGetValueW` (4) | salt okuma (daha açık set) |

> **İlginç ayrışma:** Win8 **x64** 8 fonksiyonluk tam registry setine sahip,
> **x86** sürümünde registry importu yok. İkisi aynı export listesine (153) sahip.
> ⚠️ VERIFICATION REQUIRED — farklı kod yolu mu, gecikmeli yüklenen bağımlılık mı
> (§3.4 notu) decompile ile ayrıştırılmalı.

---

## §10 OS Sürüm/Özellik Algılama (03 doc ile bağ)

| Import | Sürüm | Anlam |
|---|---|---|
| `GetVersionExW` | Win7, 8, 8.1, 10 (10240'ta `sysinfo-l1-2-1`) | **İç sürüm kontrolü** — DLL kendisi sürüm bazlı davranış uygular |
| `VerifyVersionInfoW` + `VerSetConditionMask` | Win7 (legacy), Win10 19041 (`kernel32-legacy-l1-1-1` + `sysinfo-l2`) | Manifest-siz sürüm kontrolünün modern yolu (manifest uydurması gerektirir) |
| `IsProcessorFeaturePresent` | 19041 | Donanım özelliği sorgusu |
| `GetProcAddress`/`GetModuleHandleW` | Win7 (LibraryLoader), 19041 | **Çalışma-zamanı sembol çözme** — feature-gate'in DLL içi hali |

> **03 doc ile ilişki:** neva-wlan'ın `GetProcAddress` + dönüş-kodu ikili kapısı,
> Windows'un wlanapi içinde yaptığı sürüm/özellik algılamanın **uygulama katmanındaki
> aynısıdır**. `VerifyVersionInfoW` 19041'te hâlâ import ediliyor — sürüm kontrolü
> çağdaş bir pratiktir (manifest ile birlikte; 02 doc derleme matrisi).

---

## §11 neva-wlan Mimari Etkileri (E1–E6)

| # | Etki | Hedef doc |
|---|---|---|
| E1 | XP = ince sarmalayıcı (WZCSAPI) → neva-wlan XP'de iki katman görür: kendi resolver'ı + OS'un WZC'si; WZCSAPI **yüklenen** bir DLL değil, OS bileşeni — XP'de yalnız `wlanapi.dll` yüklenir | 05 §2.1 · 07 §5 |
| E2 | Export mutabakatı: allowlist **`Wlan*` (01) + toplam sayı (bu doc §5.1)** birlikte test edilir — WFD ailesi kasıtlı olarak kapsam dışı (04) | 01 §4 · 03 §2 |
| E3 | Bildirim yolu XP'de RPC callback → 05'teki kuyruk+drain sırası XP'de de geçerli (`WlanRegisterNotification` XP'de var) | 05 §4 · 06 X-smoke |
| E4 | XP token impersonation (§8) uygulanmaz; DÜŞÜK yetkili çağrılar XP'de `ERROR_ACCESS_DENIED` beklenir (06 negatif N*) | 06 §negatif |
| E5 | Win8 x64 registry yazma + Win10 servis denetimi — modern Windows'ta beklenmedik yan etkiler; neva-wlan **registry yazmaz** (yalnız API çağrısı) | 09/10 kapsam dışı |
| E6 | XP DLL'inde ASLR/DEP/CFG yok (§3.2) — v141_xp ile XP'de güvenlik bayrağı farkı doğaldır; uygulama tarafı `/NXCOMPAT` ayarı 02 doc | 02 §derleme |

### §11.1 Üç-dönem mimari kartı (tek sayfa özet)

```
XP (5.1.2600.x)                    Vista/7/8/8.1                       Win10/11
─────────────────────              ─────────────────────               ─────────────────────
wlanapi.dll (36 export)            wlanapi.dll (48→173)                wlanapi.dll (220→258)
 ├─ WZCSAPI.DLL (19 fn) ──┐        ├─ RPCRT4 async (14-16 fn)          ├─ RPCRT4 async (16)
 │  RPC \pipe\wzcsvc      │        │  NdrClientCall3/Ndr64Async        │  + ControlService/OpenSCManager
 ├─ ADVAPI32 (reg+trace   │        ├─ Security-Base 15 fn (ACL)        ├─ UCRT (api-ms-win-crt, 44 fn)
 │   + token impersonate) │        ├─ GetVersionExW (sürüm algı)       ├─ Threadpool 10 fn (callback)
 ├─ ole32/ole32 (COM, EAP)│        ├─ (Win8+) WNF ntdll bildirimi      ├─ Registry 4 fn (salt okuma)
 └─ RPCRT4: UuidFromString │        └─ (Win8 x64) registry 8 fn ⚠️      └─ WFDLowPriv ×18 export
WZC servis (wzcsvc)               WlanSvc (ACM) + MSM                 WlanSvc + Wcmsvc; sürücü WDI
Profil: registry binary blob       Profil: XML profile store (GPO/netsh/UI/USB)
Mitigasyon: YOK (0x000)            ASLR+DEP (0x140) /8.1 x64 +HEA      +CFG (0x4160/0x4140)
```

**Karar kartı (neva-wlan):**
1. XP = 2 katman (kendi resolver + WZC sarmalayıcı); modern = 1 katman (async RPC).
2. Allowlist `Wlan*` (01) ile başlar; `WFD*`/`*Int` kapsam dışı (04/03).
3. Bildirim her üç dönemde de var (`WlanRegisterNotification`) → 05 kuyruğu 3 dönemde geçerli.

---

## §12 Açık Maddeler (⚠️ / UNKNOWN)

| # | Madde | Durum | Eylem |
|---|---|---|---|
| 1 | 19041 PE tarihi 2004-08-17 (deterministik build mi) | ⚠️ yorum | MS build altyapısı — düşük öncelik |
| 2 | XP'de COM (ole32) tam olarak ne işe yarıyor | ⚠️ VERIFICATION REQUIRED | Ghidra decompile (04 RE kapsamı) |
| 3 | Win10 `ControlService` çağrısının hedefi (WlanSvc tetik?) | ⚠️ VERIFICATION REQUIRED | decompile |
| 4 | Win8 x64 registry yazımının hedef anahtarı | ⚠️ VERIFICATION REQUIRED | decompile |
| 5 | WNF'nin wlanapi içindeki olay kapsamı | ⚠️ VERIFICATION REQUIRED | decompile + WNF dokümantasyonu |
| 6 | Win7 `wlanutil.dll` başka export kullanıyor mu (yalnız `WlanStringToSsid` eager) | ⚠️ kısmen | dumpbin /imports wlanutil.dll (XP VM/Win7 ortamı) |
| 7 | 01 doc matrisinin "toplam export" sütunu eklenmeli mi (§5.1 mutabakat) | ⚠️ vault kararı | 01 doc'a mutabakat notu (onayla) |
| 8 | Win8/8.1 `LoadResource`/`FindResourceExW` ne yüklüyor (WPS resource?) | ⚠️ VERIFICATION REQUIRED | decompile |

---

## §13 Yeniden Üretim (kanıt komutları)

```powershell
# 1) PE analizi (bu doc'un tabloları)
python C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\pe_arch.py
#    -> pe_arch.json (bölüm/import/mitigasyon) + konsol özeti

# 2) Export sayımı (01 doc'un matrisi)
python C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\export_scan.py
#    -> exports.json (yalnız Wlan* ailesi)

# 3) Toplam vs Wlan* mutabakatı (§5.1) — pefile ile tek satır:
python -c "import pefile,os;d=r'C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\dlls';\
[print(n, len([s for s in (lambda p:(p.parse_data_directories(directories=[pefile.DIRECTORY_ENTRY['IMAGE_DIRECTORY_ENTRY_EXPORT']]),p.DIRECTORY_ENTRY_EXPORT.symbols)(pefile.PE(os.path.join(d,n),fast_load=True))[1] if s.name])) ) for n in os.listdir(d)]"

# 4) Yerel makine sürümü
(Get-Item C:\Windows\System32\wlanapi.dll).VersionInfo.FileVersion   # 10.0.19041.4522
```

---

## §14 Kaynaklar

| # | Kaynak | Kanıt |
|---|---|---|
| [S1] | Disk: `wlanapi-re\pe_arch.py` + `pe_arch.json` (10 DLL) | §3, §4, §6-§10 tabloları |
| [S2] | Disk: `wlanapi-re\exports.json` + `export_scan.py` | §5.1 `Wlan*` sayıları (01 ile mutabakat) |
| [S3] | Disk: `wlanapi-re\dlls\` (10 DLL, 01 doc MD5 doğrulaması) | ham ikili analiz girdisi |
| [S4] | nt5src `net/layer2svc/zeroconf/` (07 doc) | XP WZC / WZCSAPI / pipe `\pipe\wzcsvc` |
| [S5] | nt5src + yerel registry (10 doc) | §9 registry etkileşimi karşılıkları |
| [W1] | https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-the-acm-architecture (HEAD 200) | ACM vs WZC ayrımı, GPO önceliği |
| [W2] | https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-the-native-wifi-api (HEAD 200) | API kapsamı |
| [W3] | https://learn.microsoft.com/en-us/previous-versions/windows/hardware/wireless/native-802-11-software-architecture (HEAD 200) | UI→ACM→MSM→IM/miniport+IHV, deprecation→WDI |
| [W4] | https://learn.microsoft.com/en-us/troubleshoot/windows-client/networking/wireless-network-connectivity-issues-troubleshooting (HEAD 200) | Wcmsvc/WlanSvc/MSM katman listesi, WDI/WiFiCx |
| [W5] | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-profileschema-schema (HEAD 200) | Profil deposu kaynakları + XML ns |
| [W6] | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlansetprofile (HEAD 200) | Min OS tablosu + desteklenmeyen profiller |
| [W7] | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlansetautoconfigparameter (HEAD 200) | Opcode'ler + WLAN_WRITE_ACCESS DACL |
| [W8] | https://learn.microsoft.com/en-us/windows-server/networking/technologies/wlan/wireless-networking (HEAD 200) | WLAN AutoConfig servis davranışı |

---

**Footer:** v1.0.0 · plan · KOD YOK · 10 DLL PE kanıtı (pefile) + 8 MS Learn URL
(HEAD-200) + nt5src/registry çapraz · 01 ile export mutabakatı §5.1'de · 8 açık
madde · 01–11 serisiyle tutarlı
