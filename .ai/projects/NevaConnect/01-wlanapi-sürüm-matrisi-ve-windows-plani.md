---
title: "Neva Connect — wlanapi.dll Sürüm Matrisi (XP→Win10) & Windows WLAN Katmanı Planı"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "pconlife.com indirilen DLL'ler (MD5 doğrulandı) + REA/Ghidra 12.1.4 export analizi + yerel Win10 19041 kanıtı + E:\Belgeler\coremusic.net.old\cpp-projects kuralları"
updated: 2026-10-15
---

# §1 Amaç & Kapsam

Kullanıcı direktifi (2026-10-15): **pconlife.com'dan Windows sürümlerine ait
wlanapi.dll'leri indir** → REA (Ghidra) ile analiz et → **"Neva Connect" sistemini
Windows WLAN API üzerine tasarla** → **önce vault'a yaz, kodlama kullanıcı onayıyla**.

> Kritik akış: bu doküman vault kaydı + plandır. **Kod yazımı YOKTUR** — kullanıcı
> onayı beklenmektedir. Zero-hallucination: aşağıdaki her satır dosya kanıtı
> (MD5/PE export tablosu) veya `UNKNOWN` işaretlidir.

## İlgili vault kayıtları

- `PROJECTS.md` §7.1.4 NevaConnect (Multi-Room Audio, C++20 + JS, WebSocket 9742)
- `E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md` (RPi5/L0-L3 mimarisi)
- `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` (Windows WiFi parser + WlanCloseHandle race gotcha)

# §2 DLL Envanteri (pconlife.com + yerel)

Kaynak sayfa: `https://www.pconlife.com/fileinfo/wlanapi.dll-info/` (TLS sertifikası
süresi dolmuş → `curl -k` zorunlu; captcha POST `/download/file/pefile/` akışı).
Analiz klasörü (salt-okunur, sistem dizinine kopyalanmadı):
`C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\dlls\`

| Dosya | Sürüm | Boyut | MD5 (pconlife beklenen = doğrulanan) |
|---|---|---|---|
| `xp-x86-wlanapi-5.1.2600.5512.dll` | 5.1.2600.5512 (XP SP3) | 69 120 | `9EEFE69139FDBB4A3C327630F8EB993A` |
| `win7-x86-wlanapi-6.1.7600.16385.dll` | 6.1.7600.16385 | 81 408 | `B010CF88…` ✓ |
| `win7-x64-wlanapi-6.1.7600.16385.dll` | 6.1.7600.16385 | 114 176 | `357BE883…` ✓ |
| `win8-x86-wlanapi-6.2.9200.16384.dll` | 6.2.9200.16384 | 202 240 | `206DC7CE…` ✓ |
| `win8-x64-wlanapi-6.2.9200.16384.dll` | 6.2.9200.16384 | 273 408 | `0753CB7B…` ✓ |
| `win81-x86-wlanapi-6.3.9600.dll` | 6.3.9600.16520 | 229 888 | `4178F024…` ✓ |
| `win81-x64-wlanapi-6.3.9600.dll` | 6.3.9600.16520 | 295 936 | `AF92E8AB…` ✓ |
| `win10-x86-wlanapi-10.0.10240.16384.dll` | 10.0.10240.16384 | 301 648 | `65FEA5F2…` ✓ |
| `win10-x64-wlanapi-10.0.10240.16384.dll` | 10.0.10240.16384 | 384 288 | `3DA4E9F8…` ✓ |
| `local-win10-19041-x64-wlanapi-10.0.19041.4522.dll` | 10.0.19041.4522 (yerel System32 kopyası) | 472 640 | `D3A96C2F…` ✓ |

- **Windows 11**: pconlife OS listesinde Win11 yok → **kaynak UNKNOWN, eksik**
  (kullanıcıya soruldu — §6 Açık Konular #1).
- Tüm MD5'ler pconlife sayfasındaki beklenen değerlerle eşleşti (XP/7/8/8.1/10).

# §3 Export Matrisi (PE export tablosu, `export_scan.py` + REA `list_names` çapraz kontrol)

Yöntem: PE32/PE32+ export dizini ayrıştırma (Python) → REA `open_binary` + `list_names`
(Ghidra 12.1.4) ile XP, Win7-x86 ve Win7-x64 için bağımsız doğrulama; iki yöntem
aynı listeyi verdi (XP 36, Win7 48 Wlan* export). Export'lar `Wlan*` önekiyle filtrelendi.

| Sürüm | Wlan* export | XP'ye göre |
|---|---|---|
| XP (5.1.2600.5512) | **36** | taban |
| Win7 (6.1.7600) x86/x64 | **48** | +12 / −0 |
| Win8 (6.2.9200) x86/x64 | **103** | +67 / −0 |
| Win8.1 (6.3.9600) x86/x64 | **108** | +72 / −0 |
| Win10 10240 (10.0.10240) x86/x64 | **121** | +85 / −0 |
| Win10 19041 (yerel) x64 | **148** | +112 / −0 |

### §3.1 Sürüm geçişleri (delta — hiçbir sürüm export silmiyor)

| Geçiş | Eklenen | Silinen |
|---|---|---|
| XP → Win7 | 12: `WlanHostedNetwork*` (11 adet: ForceStart/ForceStop/InitSettings/QueryProperty/QuerySecondaryKey/QueryStatus/RefreshSecuritySettings/SetProperty/SetSecondaryKey/StartUsing/StopUsing) + `WlanRegisterVirtualStationNotification` | **0** |
| Win7 → Win8 | 55: PLAP (`WlanCancelPlap`/`DoPlap`/`InitPlapParams`/`DeinitPlapParams`/`QueryPlapCredentials`), Wi-Fi Direct GO (`WlanWfdGOSetWCNSettings`/`WfdGetPeerInfo`/`WfdStartGO`/`WfdStopGO`), `WlanConnectEx`/`ConnectWithInput`/`QueryPreConnectInput`, `WlanEnumAllInterfaces`, `WlanGetAvailableNetworkList2`* , `WlanStartAP`/`StopAP`, `WlanLowPriv*` (6), `WlanPrivateGetAvailableNetworkList`, `WlanSsidToDisplayName`/`StringToSsid`, `WlanGetRadioInformation`, profil metadata/index/key, `WlanSendUIResponse`, `WlanWcm*` (3), `WlanHostedNetwork*WCNSettings`/`HlpQueryEverUsed` vb. | **0** |
| Win8 → Win8.1 | 5: `WlanDoesBssMatchSecurity`, `WlanNotifyVsIeProviderInt`, `WlanTryUpgradeCurrentConnectionAuthCipher`, `WlanUpdateProfileWithAuthCipher`, `WlanUtf8SsidToDisplayName` | **0** |
| Win8.1 → Win10 (10240) | 13: `WlanGetAvailableNetworkList2`, ANQP/hotspot-2.0 private (`WlanPrivateClearAnqpCache`/`GetAnqpCacheResponse`/`ParseAnqpRawData`/`RefreshAnqpCache` + `QueryInterface`/`SetInterface` private), `WlanWcmDisconnect`/`WcmSetProfile`, `WlanSetProfileListForOffload`, `WlanVMgrQueryCurrentScenariosInt`, `WlanNotifyVsIeProviderExInt`, `WlanLowPrivNotifyVsIeProviderInt` | **0** |
| Win10 10240 → 19041 | 27: FTM/802.11mc (`WlanInternalRequestFTM`/`CancelFTMRequest`/`GetNetworkBssListWithFTMData`), non-disruptive scan (`WlanInternalNonDisruptiveScan[Ex]`), device services (`WlanDeviceServiceCommand`/`GetSupportedDeviceServices`/`RegisterDeviceServiceNotification`), IP config profile (4), movement detector (`WlanStart/StopMovementDetector`), `WlanPrivate*` (profile/ANQP/11ad), `WlanStringToUtf8Ssid`, `WlanVerifyProfileIpConfiguration` vb. | **0** |

\* `WlanGetAvailableNetworkList2` Win8 satırında da görünür; kesin ilk sürüm
MSDN ile teyit edilmelidir → `VERIFICATION REQUIRED` (export'un binary'de varlığı
kanıtlı, "hangi ilk sürümde public文档landı" kanıtlanmadı).

> **Ana bulgu:** XP tabanı (36 export) Win10 19041'e kadar **hiçbir export kaybetmiyor**
> (her geçişte `−0`). Runtime resolver = `GetProcAddress` + sürüm kontrolü; XP'ye giden
> daraltılmış profil (yalnız 36 export) + modern özelliklerin feature-flag'li düşmesi
> yeterlidir.

# §4 Neva Connect — Windows WLAN Katmanı Planı (kod YOK, onay bekliyor)

Mevcut durum: `neva-connect` RPi5/Linux odaklı (L0-L3, WebSocket 9742, I2C PCM5122);
**Windows WLAN katmanı yok**. Windows dalı `wireless-connect`'in parser bilgisi +
bu export matrisi üzerine kurulur.

### §4.1 Katman tasarımı (L4'ün Windows karşılığı)

```
NevaConnect Windows WiFi Katmanı (öneri — ONAY BEKLIYOR)
├─ wlabase    : DynamikLoader — LoadLibrary("wlanapi.dll") + GetProcAddress tablosu
│                (sabit import YOK → XP..Win11 tek binary, §3 omurga)
├─ wlacore    : Handle yaşam döngüsü (WlanOpenHandle/CloseHandle, negotiated ver=1/2/3)
│                + WlanAllocateMemory/FreeMemory RAII sarmalayıcı
│                + notification kuyruğu (WlanRegisterNotification callback → lock-free queue)
├─ wlaenum    : WlanEnumInterfaces / WlanGetAvailableNetworkList(2) / GetNetworkBssList
├─ wlaprofile : profil XML (WlanSetProfile/GetProfile/DeleteProfile/List/Position)
├─ wlaconn    : WlanConnect/Disconnect + WlanReasonCodeToString hata çevirisı
└─ features   : runtime keşif — HostedNetwork (≥Win7), WFD/PLAP/AP (≥Win8),
                ANQP (≥Win10), FTM/device-service (≥19041); eksik export → graceful degrade
```

### §4.2 Kararlar (kanıt dayanaklı)

| # | Karar | Kanıt |
|---|---|---|
| D1 | **Dinamik yükleme** (`GetProcAddress`), sabit `wlanapi.lib` import değil | §3: export kümeleri sürümüne göre büyüyor; tek import tablosu XP'de link/loading hatası verir |
| D2 | Feature keşfi **export varlığıyla** yapılır, OS sürümüyle değil | §3.1: 19041'e kadar +27 export; sürüm numarası yanıltıcı olabilir (Update kb'ları export ekler) |
| D3 | XP dalı = 36 export tabanı; HostedNetwork/WFD/AP **yok sayılıp** düşer | §3.1 XP→Win7: HostedNetwork XP'de export YOK (REA + PE tablosu iki yöntemle doğrulandı) |
| D4 | `wireless-connect` WlanCloseHandle race gotcha'sı tekrar kullanılacak | `E:\...\wireless-connect\AGENTS.md` (mevcut kayıt) |
| D5 | Callback notification için lock-free kuyruk (RT koruma) | CoreMusic global kural: RT audio callback lock-free; neva-connect L3 kalite hedefi |
| D6 | Yerel 19041 DLL'i "Win10 son" referansı; Win11 export seti **UNKNOWN** | §2: Win11 kaynağı yok |

### §4.3 Uygulama fazları (onay sonrası)

1. **Faz 1**: `wlabase` (dynload + tablo) + `wlacore` (handle RAII) + XP/Win10 iki uç test
2. **Faz 2**: enum/scan/profile + notification kuyruğu
3. **Faz 3**: connect/disconnect + reason-code hata çevirişi
4. **Faz 4**: feature gate (HostedNetwork/WFD/ANQP) + NevaConnect L3'e bağlama (WebSocket 9742 köprüsü)

Test: vcpkg + MSVC2022, ctest (`E:\...\cpp-projects\CLAUDE.md` standardı) —
her faz XP SP3 (36 export) ve Win10 19041 (148 export) uçlarında koşar.

# §5 Kanıt Dosyaları

- Export çıktısı: `C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\exports.json`
- Tarama betiği: `C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\export_scan.py`
- DLL'ler: `C:\Users\MARHAN\AppData\Local\Temp\opencode\wlanapi-re\dlls\` (10 dosya, MD5 ✓)
- REA oturumları: XP (SHA256 `8b397f67…`, 446 procedure, 2276 symbol) · Win7-x86 (`cbcd032d…`) · Win7-x64 (`4ddb697f…`)

# §6 Açık Konular (onay bekleyenler)

1. **Win11 kaynağı** — pconlife'ta yok; alternatif kaynak ( kullanıcıdan DLL veya güvenilir arşiv) soruldu → `UNKNOWN`.
2. **Kod onayı** — §4 fazları kullanıcının açık onayıyla başlar; bu dokümanın yazımı onay KAPSAMINDA DEĞİLDİR.
3. `WlanGetAvailableNetworkList2`'nin ilk public sürümü → `VERIFICATION REQUIRED` (MSDN teyidi).
4. Faz 1 sürüm aralığı: XP SP3 hedefleniyor mu, yoksa Win7+ mi? (XP 36-export dar kırpması iş yükü ekler.)
