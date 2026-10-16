---
title: "Neva Connect — Windows Sürüm Bazlı WLAN API Özellik Kısıtlama Tablosu"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "pconlife.com DLL export analizi (01 doc) + Microsoft Learn Native Wifi + web araştırması"
updated: 2026-10-15
---

# Neva Connect — Windows Sürüm Bazlı WLAN API Özellik Kısıtlama Tablosu

> **Kapsam:** Neva Connect Windows WLAN dalının (`neva-wlan.exe`) hangi WLAN API
> özelliklerini hangi Windows sürümünde kullanıp kullanmayacağını belirleyen kısıtlama
> tablosu ve tek girişli (single-entry) runtime resolver tasarımı.
>
> **Kardeş dokümanlar:**
> [[01-wlanapi-sürüm-matrisi-ve-windows-plani]] (export matrisi — MD5 kanıtlı DLL'ler)
> · [[02-coklu-exe-windows-sdk-derleme-mimarisi]] (çoklu-EXE + derleme matrisi)
> · **03 = bu dosya** (runtime feature gate + resolver tasarımı)
>
> **Kod notu:** §4'teki C++20 kodu **yalnızca bu dokümandaki örnektir**; diske kod
> dosyası yazılmaz, kod yazımı kullanıcı onayıdır (01 §1 kuralı).
>
> **Zero-Hallucination:** her iddia ya disk kanıtı (01 doc, MD5 doğrululmuş DLL export
> tabloları) ya da §7.2'deki kaynak URL'ine dayanır; doğrulanamayan satır
> `⚠️ VERIFICATION REQUIRED`, bilinmeyen değer `UNKNOWN` ile işaretlidir.

---

## §1 Amaç ve Kapsam

### §1.1 Üç bağlayıcı yasak

| # | Yasak | Gerekçe |
|---|---|---|
| Y1 | **Çökme YASAK.** Özellik yoksa uygulama çökmez; `FeatureNotSupported` istisnası + kullanıcıya dönülebilir mesaj. | XP..Win11 tek kod tabanı hedefi (01 §4.2 D1); import-trambolin hatası (`0xC0000139`) statically-linked tek binary'de kaçınılmaz |
| Y2 | **Karar (feature gate) OS sürümü ile VERİLMEZ.** Karar = hedef export'un `GetProcAddress` ile varlığı. | 01 §4.2 D2: sürüm numarası yanıltıcı olabilir (Update/paket DLL'leri export ekler); §2.4: export olması = desteklenmesi demek değil, tersi de |
| Y3 | **Çözümleyiciye (resolver) tek giriş.** kod tabanında doğrudan `GetProcAddress("Wlan*")` veya sabit `wlanapi.lib` importu YASAK; tüm çağrılar §4 resolver üzerinden. | İkinci bir çözümleme yolu = iki ayrı gerçeklik; test edilebilirlik ve denetim tek noktada toplanır |

### §1.2 Karar felsefesi: neden export?

Export varlığı, `wlanapi.dll`'in o makinedeki **gerçek çalışma zamanı imzasıdır**:

- **Necessary (gerekli):** export yoksa `GetProcAddress` null döner → çağrı imkânsız →
  özellik kapalı. (01 §3: sürüm geçişlerinin tamamında export **eklenir**, 19041'e kadar
  hiçbir sürüm export **silmez** — silme olsa bile kapı doğru çalışır.)
- **Not sufficient (yeterli değil):** export **vardır ama OS desteklemez**. Kanıt §2.4:
  `WlanGetNetworkBssList` XP'de export listesinde, MSDN ise "Minimum supported client:
  Windows Vista" der ve XP'de `ERROR_NOT_SUPPORTED` döner. Bu yüzden kapı **ikili**:
  1) export kapısı (crash güvenliği), 2) çağrı sonucu kontrolü (destek güvenliği).
- **OS sürümü yalnızca mesaj içindir** (§4.5): `VersionHelpers` / `RtlGetVersion` sadece
  kullanıcıya gösterilecek "Bu özellik Windows 10 2004+ gerektirir" metnini üretir;
  karar akışına girmez (manifest version-lie riski — §7.1 #6).

### §1.3 Kapsam dışı

- Derleme matrisi, çoklu-EXE dağılımı → 02 doc (§§1-2).
- Export sayısal matrisinin türetilmesi → 01 doc (§§2-3); bu doc matrisi **kopyalar,
  yeniden saymaz**.
- Linux/RPi5 L0-L3 katmanı → neva-connect CLAUDE.md.

---

## §2 Export Matrisi (01 doc'tan alınan disk kanıtı)

### §2.1 Sürüm → Wlan* export sayısı

| Sürüm | Sürüm no | Wlan* export | XP'ye göre | Geçiş farkı |
|---|---|---|---|---|
| XP SP3 | 5.1.2600.5512 | **36** | taban | — |
| Win7 | 6.1.7600 | **48** | +12 | **+12 / −0** |
| Win8 | 6.2.9200 | **103** | +67 | **+55 / −0** |
| Win8.1 | 6.3.9600 | **108** | +72 | **+5 / −0** |
| Win10 10240 | 10.0.10240 | **121** | +85 | **+13 / −0** |
| Win10 19041 | 10.0.19041 | **148** | +112 | **+27 / −0** |
| Win11 22000 | 10.0.22000 | **149** (arşiv dump) | +113 | **+3 / −2** |

Kaynak: XP..19041 satırları = 01 doc §3 (pconlife'tan indirilen, MD5 doğrulanmış 10 DLL;
`export_scan.py` PE parse + REA/Ghidra `list_names` çapraz kontrolü — XP/Win7 için iki
yöntem aynı listeyi verdi). Win11 satırı = §2.3.

### §2.2 Geçiş delta özeti (01 doc §3.1)

| Geçiş | Eklenen (başlıca) | Silinen |
|---|---|---|
| XP → Win7 | 11 adet `WlanHostedNetwork*` + `WlanRegisterVirtualStationNotification` | **0** |
| Win7 → Win8 | PLAP (`WlanCancelPlap/DoPlap/DeinitPlapParams`), WFD GO, `WlanConnectEx`, `WlanEnumAllInterfaces`, `WlanStartAP/StopAP`, `WlanGetRadioInformation`, `WlanGetAvailableNetworkList2`* , LowPriv/Private grupları | **0** |
| Win8 → Win8.1 | 5: `WlanDoesBssMatchSecurity` vb. | **0** |
| Win8.1 → Win10 10240 | 13: ANQP/hotspot-2.0 private (`WlanPrivate*Anqp*`), `WlanGetAvailableNetworkList2`, `WlanWcm*` | **0** |
| Win10 10240 → 19041 | 27: FTM/internal (`WlanInternal*FTM`), non-disruptive scan, device services, movement detector | **0** |

*`WlanGetAvailableNetworkList2` ilk sürümü için 01 doc kendi içinde `VERIFICATION
REQUIRED` taşır (§3.7 — bu doc'da MSDN teyidi yapıldı: **Windows 10**).

**Bağımsız çapraz kontrol (bu doc araştırması):**

- XP: NirSoft XP DLL sayfası **36 export** ve dosya sürümü **5.1.2600.5512** — 01 doc ile
  birebir (§7.2 [K11]).
- Win7: NirSoft Win7 DLL sayfası **48 export** — isim listesi 36 XP ismi + 11
  `WlanHostedNetwork*` + `WlanRegisterVirtualStationNotification` = 48; 01 doc delta
  (§2.2 satır 1) ile birebir (§7.2 [K12]).
- Win10/Win11: `WindowsDllsExport` arşivinden ham dump — 19043 = **148**, 22000 = **149**
  (§2.3).

### §2.3 Win11 (01 doc'un UNKNOWN maddesi kapatıldı)

01 doc §6 #1: "Win11 kaynağı yok → UNKNOWN". Bu doc'da arşiv dump ile kapatıldı:

| Karşılaştırma | Değer |
|---|---|
| Win10 19043 dump | 148 `Wlan*` (01 doc'un 19041 sayımı = 148 ile eşleşiyor) |
| Win11 22000 dump | **149** |
| Eklenen (+3) | `WlanGetExtensibilityInfo` · `WlanSetExtensibilityInfo` · `WlanPrivateGetBssHi`* |
| Silinen (−2) | `WlanPrivateEnableAnqpOsuRegistration` · `WlanPrivateGetAnqpOsuRegistrationStatus` |

\* İsimler ham dump metninden okunmuştur; dump **x64** disturb (§7.1 #1: XP→19041 "hiç
export silinmez" kuralı **19041'de biter** — Win11'de 2 private export silinmiş; resolver
için pratik etkisi yok çünkü Neva Connect `WlanPrivate*` çağırmaz).

Sonuç: **XP tabanı (36) → Win10 19041: −0** (01 doc ana bulgusu korunur) · **Win11: −2 private.**

### §2.4 Export ≠ destek: `WlanGetNetworkBssList` kanıtı

| Bilgi | Değer | Kaynak |
|---|---|---|
| XP export listesinde mi? | **Evet** (NirSoft 36 isim arasında) | [K11] |
| MSDN minimum sürüm | **Windows Vista** | [K5] |
| XP'de davranış | **`ERROR_NOT_SUPPORTED`** — "if this function was called from a Windows XP with SP3 or Wireless LAN API for Windows XP with SP2 client" | [K5] |

**Karar:** kapı yalnızca export'a dayanmaz; `DWORD` dönüş kodu her çağır'da kontrol edilir,
`ERROR_NOT_SUPPORTED` → `FeatureNotSupported` (§4.4).

### §2.5 XP'de export'u olan ama MSDN'in XP listesinde yer almayan 16 fonksiyon

MSDN XP SP2/SP3 sayfası 20 fonksiyon + 1 callback tipi (`WLAN_NOTIFICATION_CALLBACK`)
listeler [K1]; NirSoft XP listesi 36 isim [K11]. Fark = **16**:

| # | Export (XP'de var) | MSDN XP listesinde | Durum |
|---|---|---|---|
| 1 | `WlanGetNetworkBssList` | Hayır | **Belgelendirilmiş reddedilme** — min Vista, XP'de ERROR_NOT_SUPPORTED [K5] |
| 2 | `WlanExtractPsdIEDataList` | Hayır | ⚠️ VERIFICATION REQUIRED (XP davranışı bilinmiyor) |
| 3 | `WlanGetFilterList` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 4 | `WlanGetInterfaceCapability` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 5 | `WlanGetProfileCustomUserData` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 6 | `WlanGetSecuritySettings` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 7 | `WlanIhvControl` | Hayır | ⚠️ VERIFICATION REQUIRED (XP kısıtlamaları listesi "IHV extensibility functions are not supported" der [K1] → muhtemel reddedilme, teyit gerek) |
| 8 | `WlanQueryAutoConfigParameter` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 9 | `WlanRenameProfile` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 10 | `WlanSaveTemporaryProfile` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 11 | `WlanSetAutoConfigParameter` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 12 | `WlanSetFilterList` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 13 | `WlanSetProfileCustomUserData` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 14 | `WlanSetProfileEapUserData` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 15 | `WlanSetPsdIEDataList` | Hayır | ⚠️ VERIFICATION REQUIRED |
| 16 | `WlanSetSecuritySettings` | Hayır | ⚠️ VERIFICATION REQUIRED |

**Resolver etkisi:** bu 16 fonksiyon XP'de **çağrılmaz** (beyaz liste = MSDN'in 20 XP
fonksiyonu, §3.1); siyah liste ihlal edilirse bile kapı + dönüş kodu kontrolü (Y2/§2.4)
hatayı `FeatureNotSupported`'a çevirir, çökmez.

---

## §3 Sürüm Bazlı API Özellik Tabloları

> Her tablonun "İlk sürüm" sütunu ya MSDN "Minimum supported client" satırıdır (kaynaklı)
> ya 01 doc export delta'sıdır (disk kanıtı). "Neva Connect kararı" = §1.1 yasaklarına bağlı
> bağlayıcı karar.

### §3.1 Çekirdek Native Wifi (XP SP2/SP3+)

**Desteklenen 20 fonksiyon** (MSDN XP sayfası [K1] — beyaz liste):

`WlanAllocateMemory · WlanCloseHandle · WlanConnect · WlanDeleteProfile · WlanDisconnect ·
WlanEnumInterfaces · WlanFreeMemory · WlanGetAvailableNetworkList · WlanGetProfile ·
WlanGetProfileList · WlanOpenHandle · WlanQueryInterface · WlanReasonCodeToString ·
WlanRegisterNotification · WlanScan · WlanSetInterface · WlanSetProfile ·
WlanSetProfileEapXmlUserData · WlanSetProfileList · WlanSetProfilePosition`

**XP kısıtlamaları** (MSDN [K1] — hepsi `⚠️ VERIFICATION REQUIRED` değil, **belgelendirilmiş**):

| # | Kısıtlama |
|---|---|
| 1 | Profile başına en fazla 1 SSID |
| 2 | Infrastructure ağlar her zaman ad hoc'tan önce listelenir |
| 3 | Profile adı SSID'den türetilir, kullanıcı serbest ad veremez |
| 4 | PHY types desteklenmez |
| 5 | PMK caching desteklenmez |
| 6 | IHV extensibility fonksiyonları desteklenmez |
| 7 | Profile permissions desteklenmez |
| 8 | Yalnız `wlan_notification_acm_connection_complete` ve `wlan_notification_acm_disconnected` bildirimleri |
| 9 | Global 802.1X/EAPOL yapılandırması desteklenmez |

**Ek XP davranış kanıtları:**

| Konu | Davranış | Kaynak |
|---|---|---|
| `WlanOpenHandle` XP | WZC (Wireless Zero Configuration) servisi başlamamışsa/duyarsızsa hata döner | [K4] |
| `WlanOpenHandle` sürüm parametresi | `dwClientVersion` 1 = XP istemci sürümü; 2 = sonraki sürümler; `WLAN_API_MAKE_VERSION` makrosu ile üretilir; müzakere edilen sürüm `pdwNegotiatedVersion` ile döner | [K4] |
| `WlanQueryInterface` XP | Yalnız 4 opcode geçerli: `autoconf_enabled` · `bss_type` · `interface_state` · `current_connection` | [K9] |
| `WlanRegisterNotification` XP | Bildirimler Netman servisi üzerinden taşınır; Netman kapalıysa bildirim gelmez; `SOURCE_ALL` ≡ `SOURCE_ACM`; `MSM/SECURITY/IHV` kaynakları XP'de desteklenmez; `bIgnoreDuplicate` yok sayılır | [K7] |
| `WlanGetNetworkBssList` XP | export var + destek **yok** (§2.4) | [K5] |

**Neva Connect kararı:** XP dalı yalnız bu 20 fonksiyonu çağırır; bildirim için
`WlanRegisterNotification` (ACM) + timeout-ile-arama yedeği (Netman riski [K7]).

### §3.2 Hosted Network (Win7+)

| İ | Özellik | Fonksiyonlar (11) | İlk sürüm | Kaynak | Neva Connect kararı |
|---|---|---|---|---|---|
| H1 | SoftAP durum kontrolü | `WlanHostedNetworkQueryStatus` | **Windows 7** | [K2] What's New Win7 + [K3] MSDN min "Windows 7 / Server 2008 R2" | **VAR:** export kapısı + başarı kontrolü |
| H2 | Property get/set | `QueryProperty` · `SetProperty` · `InitSettings` · `RefreshSecuritySettings` | Windows 7 | [K3] (QueryProperty sayfası; diğerleri What's New listesi [K2]) | VAR |
| H3 | Anahtar yönetimi | `QuerySecondaryKey` · `SetSecondaryKey` | Windows 7 | [K2] | VAR |
| H4 | Başlatma/yaygınlaştırma | `ForceStart` · `ForceStop` · `StartUsing` · `StopUsing` | Windows 7 | [K2] | VAR |
| H5 | Bildirim kaynağı | `WLAN_NOTIFICATION_SOURCE_HNWK` | Windows 7 (Server: Wireless LAN Service kurulu) | [K7] | VAR — ama HNWK yoksa ACM'e düş |
| H6 | Sanal adaptör bağımlılığı | Hosted Network capable adaptör yoksa sanal cihaz hiç oluşmaz | Windows 7+ | [K3] Remarks | **Çift koşul:** export VAR ∧ adaptör VAR; ikincisi yoksa → "donanım desteklemiyor" mesajı, çökme yok |

**Not:** Win7'ye +12 export = 11 `HostedNetwork*` + `WlanRegisterVirtualStationNotification`
(§2.2 — NirSoft isim listesi ile birebir teyitli [K12]).

### §3.3 Wi-Fi Direct / PLAP / AP (Win8+)

| İ | Özellik | Export/fonksiyon | İlk sürüm | Kaynak | Neva Connect kararı |
|---|---|---|---|---|---|
| W1 | Wi-Fi Direct public API | `WFDOpenHandle` (↑ `WFD_API_VERSION`) · `WFDStartOpenSession` · `WFDCloseSession` · `WFDCancelOpenSession` · `WFDUpdateDeviceVisibility` · `WFDParseOOBBlob` vb. | **Windows 8** | [K8] "part of Wi-Fi Direct, a new feature in Windows 8 and Windows Server 2012" | **VAR:** `WFDOpenHandle` export kapısı |
| W2 | PLAP (persistent legacy AP) | `WlanCancelPlap` · `WlanDoPlap` · `WlanDeinitPlapParams` | Win8 delta (01 doc) + modern dump'ta varlık | 01 §3.1 (disk) + [K13] (varlık çapraz kontrol) | VAR — **ama** MSDN public sayfası doğrulanmadı → ⚠️ VERIFICATION REQUIRED (§7.1 #4); kapalı sürümde çağrılmaz |
| W3 | Wi-Fi Direct GO yardımcıları | `WlanWfdGOSetWCNSettings` · `WlanWfdGetPeerInfo` · `WlanWfdStartGO` · `WlanWfdStopGO` | Win8 delta | 01 §3.1 (disk) | VAR — isimler disk kanıtlı, MSDN belgesi ⚠️ VR |
| W4 | AP başlat/durdur | `WlanStartAP` · `WlanStopAP` (export adı) | Win8 delta | 01 §3.1 (disk) | VAR — ⚠️ VR (public API belgesi doğrulanmadı; isim 01 doc export listesinden) |
| W5 | Bağlantı genişletmeleri | `WlanConnectEx` · `WlanConnectWithInput` · `WlanQueryPreConnectInput` | Win8 delta | 01 §3.1 + [K13] (`WlanConnectEx`/`ConnectWithInput` modern dump'ta) | VAR |

**Kapsam kararı (bağlayıcı):** Neva Connect Win8 dalında **yalnız W1 (MSDN belgeli WFD
public API)** + temel bağlantıyı kullanır; W2-W4 (export'u diskte kanıtlı ama public
belgesi olmayanlar) **kullanılmaz** — sebep: desteklenmeyen/gizli API çağrısı
belgelendirilmiş hata sözleşmesi üretmez, §1.1 Y1 ihlal edilebilir.

### §3.4 Dual-STA (Windows 11 + SDK 22000.194)

| İ | Konu | Değer | Kaynak |
|---|---|---|---|
| D1 | Geliştirme gereksinimi | **Windows 11 SDK (10.0.22000.194) ve sonrası** | [K6] "To develop for the features described in this topic, the Windows 11 SDK (10.0.22000.194), and later, is needed." |
| D2 | Arayüz modeli | Primary STA (tüm adaptörlerde) + secondary STA (yalnız destekleyen adaptörlerde) | [K6] |
| D3 | Runtime tespit | Çift STA arayüzünün tespiti için arayüz listesi sorgusu; **spesifik sorgu fonksiyonu/opcode adı doğrulanmadı** | ⚠️ VERIFICATION REQUIRED (§7.1 #5) |
| D4 | Export kapısı | Win11 dump'ında 149 `Wlan*` (§2.3); dual-STA'ya özgü export adı ayırt edilemedi | ⚠️ VERIFICATION REQUIRED |

**Neva Connect kararı:** dual-STA, 02 doc derleme matrisinde **Win11-only varyant**
(`neva-wlan` Win11 derlemesi) olarak ayrılır; runtime'da D3 doğrulanana kadar özellik
**kapalı** tutulur (istemci tarafı hayal kırıklığı yaratmaz).

### §3.5 Hotspot 2.0 / ANQP (Windows 10+)

| İ | Katman | Değer | Kaynak | Karar |
|---|---|---|---|---|
| A1 | **Public yol:** WLAN_profile şeması | HS2.0 (`ANQP❱` işletme) Windows 10'da profile şemasına eklendi | [K2] What's New: "Support for Hotspot 2.0 has been added to the WLAN_profile schema" (Windows 10) | **VAR:** Win10+ `WlanSetProfile` ile HS2.0 profili; alt sürümde şema reddedilir → kullanıcıya mesaj |
| A2 | Private ANQP export'ları | `WlanPrivateClearAnqpCache` · `GetAnqpCacheResponse` · `ParseAnqpRawData` · `RefreshAnqpCache` + `QueryInterface/SetInterface` private varyantları | 01 §3.1 Win8.1→Win10 delta (disk, +13) | **KULLANILMAZ** (private/belgesiz → §3.3 kapsam kararı gerekçesi) |

### §3.6 FTM / 802.11mc (export 19041+ — public API YOK)

| İ | Konu | Değer | Kaynak |
|---|---|---|---|
| F1 | Export varlığı | `WlanInternalRequestFTM` · `WlanInternalCancelFTMRequest` · `WlanGetNetworkBssListWithFTMData` — Win10 10240→19041 +27 delta içinde | 01 §3.1 (disk) |
| F2 | Public user-mode API | **YOK.** Microsoft Q&A: "There is no API that supports this feature... no public plan for current or future support for WiFi RTT" | [K10] |
| F3 | FTM'nin gerçek katmanı | Sürücü seviyesi: `OID_WDI_TASK_REQUEST_FTM` (WDI/dot11wificx) | [K14] |
| F4 | İsim öneki | Fonksiyonlar `Internal` önekli → özel/imzalanmamış | 01 §3.1 (disk) |

**Neva Connect kararı:** FTM **sunulmaz**. Export 19041+ kapısını kodda bile tanımlamak
gereksizdir (YAGNI); ileride Microsoft public API açarsa §4'teki `Feature` kaydına eklenir.
Bu satır, "export var ⇒ kullanılır" zihniyetinin ne zaman **çürütüleceğinin** örneğidir:
F1+F2 birlikte, kapının yalnız export olmadığını bir kez daha kanıtlar.

### §3.7 `WlanGetAvailableNetworkList2` kararı (01 doc açık konusu #3 kapatıldı)

| Bilgi | Değer | Kaynak |
|---|---|---|
| MSDN "Minimum supported client" | **Windows 10** (arşiv sayfası) | [K16] |
| Export disk kanıtı | 01 doc Win8 delta'sında görünür (`*` işaretli) **ve** Win8.1→Win10 +13 listesinde tekrar geçer | 01 §3.1 (kendi dipnotu: "kesin ilk sürüm MSDN ile teyit edilmelidir") |
| Çelişki | Export Win8 imzasında olabilir, **belgelenmiş** destek Win10'dan | 01 dipnotu + [K16] |

**Karar (çift katmanlı):**

1. **Derleme zamanı:** `WLAN_AVAILABLE_NETWORK_LIST_V2` tipleri yalnız Win10 SDK hedefinde
   bağlanır (02 doc derleme matrisine göre); XP/Win7/Win8 derlemeleri List1'e bağlı.
2. **Runtime:** `GetProcAddress("WlanGetAvailableNetworkList2")` → null ise **List1'e düş**
   (zorunlu yedek, hata değil normal yol).
3. Export varsa **çağır**; `DWORD` hata dönerse (`ERROR_NOT_SUPPORTED` dahil) sessizce
   List1'e düş + `⚠️ VERIFICATION REQUIRED` telemetrisi (Win8/8.1'de belgelenmemiş davranış
   gözetlenir).
4. **Kullanıcı sözleşmesi:** List2 yokluğunda UI farkı **görünmez** — v2 alanları
   (varsa) doldurulmaz, liste V1 alanlarıyla sunulur.

Gerekçe: kapı export + dönüş kodu ikilisidir (Y2/§2.4); OS sürümü okuyup "Win10 üstü
List2" demek hem version-lie'e hem 01 D2'ye aykırı olurdu.

### §3.8 Özet kısıtlama matrisi (özellik × OS)

| Özellik | XP SP3 (36) | Win7 (48) | Win8 (103) | Win8.1 (108) | Win10 10240 (121) | Win10 19041 (148) | Win11 (149) |
|---|---|---|---|---|---|---|---|
| Çekirdek (§3.1, 20 fn) | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ |
| `WlanGetNetworkBssList` | ✖ (export var, destek yok) | ✔ | ✔ | ✔ | ✔ | ✔ | ✔ |
| Hosted Network (§3.2) | ✖ | ✔* | ✔* | ✔* | ✔* | ✔* | ✔* |
| Wi-Fi Direct public (§3.3 W1) | ✖ | ✖ | ✔ | ✔ | ✔ | ✔ | ✔ |
| PLAP/AP private (§3.3 W2-W4) | ✖ | ✖ | ✔† | ✔† | ✔† | ✔† | ✔† |
| HS2.0 profil (§3.5 A1) | ✖ | ✖ | ✖ | ✖ | ✔ | ✔ | ✔ |
| ANQP private (§3.5 A2) | ✖ | ✖ | ✖ | ✖ | ✔† | ✔† | ✔† |
| List2 (§3.7) | ✖ | ✖ | ‡ | ‡ | ✔ | ✔ | ✔ |
| FTM public (§3.6) | ✖ | ✖ | ✖ | ✖ | ✖ | ✖ (export var ama public API yok) | ✖ |
| Dual-STA (§3.4) | ✖ | ✖ | ✖ | ✖ | ✖ | ✖ | ‡ (SDK 22000.194, runtime ⚠️ VR) |

✔ = kullanılır · ✖ = kullanılmaz (kapalı) · * = export ∧ donanım/servis koşulu (§3.2 H6)
· † = export var ama **kullanılmaz** (belgesiz — §3.3/§3.5 kapsam kararı) ·
‡ = export'a bağlı, List1'e yedekli (§3.7) / doğrulanana kadar kapalı (§3.4 D3).

---

## §4 Resolver Tasarımı (tek giriş — §1.1 Y3)

### §4.1 Mimari

~~~text
[Uygulama kodu — wlacore/wlaenum/wlaprofile/wlaconn (01 §4.1)]
        │  yalnızca §4.4 sarmalayıcılarını çağırır
        ▼
[wlan::Resolver — TEK GİRİŞ (singleton)]
  ├─ LoadLibraryW("wlanapi.dll")  — bir kez, asla FreeLibrary (§4.6)
  ├─ GetProcAddress tablosu        — export kapısı (Y2)
  ├─ negotiated version           — WlanOpenHandle müzakeresi (yalnız rapor/afacan*)
  └─ require()/get<Fn>()           — null → FeatureNotSupported (Y1)
        ▼
[wlanapi.dll — sürücü/otomatik yapılandırma ile konuşan sistem DLL'si]
~~~
\* negotiated version §4.5.

### §4.2 resolver.h (örnek — yalnız bu doc'da)

~~~cpp
// resolver.h — Neva Connect WLAN feature resolver (ÖRNEK, diske yazılmaz)
#pragma once
#include <windows.h>
#include <string>
#include <string_view>
#include <stdexcept>
#include <mutex>

namespace neva::wlan {

// §1.1 Y1: özellik yoksa ÇÖKME yok — geri dönülebilir istisna
class FeatureNotSupported : public std::runtime_error {
public:
    FeatureNotSupported(std::string_view feature, std::string_view reason)
        : std::runtime_error(makeMessage(feature, reason)), feature_(feature) {}
    const std::string& feature() const noexcept { return feature_; }
private:
    static std::string makeMessage(std::string_view f, std::string_view r);
    std::string feature_;
};

// §3.8 matrisinin kod karşılığı — özellik kümeleri
enum class Feature {
    Core,              // §3.1 (XP beyaz listesi)
    BssList,           // §2.4/§3.8: XP'de export var, destek yok
    HostedNetwork,     // §3.2
    WifiDirect,        // §3.3 W1
    Hs20Profile,       // §3.5 A1
    DualSta,           // §3.4 (runtime kapalı — §3.4 kararı)
    NetworkListV2      // §3.7
};

class Resolver {
public:
    static Resolver& instance() noexcept;   // tek giriş, magic-static (C++11/20)
    Resolver(const Resolver&) = delete;
    Resolver& operator=(const Resolver&) = delete;

    bool loaded() const noexcept;           // wlanapi.dll yüklendi mi
    bool has(std::string_view exportName) const noexcept;    // GETPROCADDRESS kapısı
    void require(std::string_view exportName, Feature f) const; // null → FeatureNotSupported

    template <class Fn>
    Fn get(std::string_view exportName, Feature f) const {
        require(exportName, f);
        return reinterpret_cast<Fn>(address(exportName));
    }

    bool featureAvailable(Feature f) const noexcept;   // §3.8'in runtime aynası

    DWORD negotiatedVersion() const noexcept;          // §4.5: yalnız rapor/mesaj
    void setNegotiatedVersion(DWORD v) noexcept;

private:
    Resolver() noexcept;                    // LoadLibraryW + GetProcAddress tablosu
    ~Resolver() = default;                  // asla FreeLibrary (§4.6)
    FARPROC address(std::string_view n) const noexcept;

    HMODULE module_ = nullptr;
    mutable std::mutex lock_;
    DWORD negotiated_ = 0;
    bool corePresent_[20] = {};             // §3.1 beyaz liste bayrakları
    bool hnPresent_ = false;                // §3.2 (WlanHostedNetworkQueryStatus)
    bool wfdPresent_ = false;               // §3.3 W1 (WFDOpenHandle)
    bool v2Present_ = false;                // §3.7 (WlanGetAvailableNetworkList2)
};

} // namespace neva::wlan
~~~

### §4.3 resolver.cpp (örnek — yalnız bu doc'da)

~~~cpp
// resolver.cpp — ÖRNEK: tek yükleme, tek tablo, asla boşaltma
#include "resolver.h"
#include <array>

namespace neva::wlan {

namespace {
// §3.1 beyaz liste — MSDN XP sayfasının 20 fonksiyonu [K1]
constexpr std::array<const char*, 20> kCoreExports = {{
    "WlanAllocateMemory", "WlanCloseHandle", "WlanConnect", "WlanDeleteProfile",
    "WlanDisconnect", "WlanEnumInterfaces", "WlanFreeMemory",
    "WlanGetAvailableNetworkList", "WlanGetProfile", "WlanGetProfileList",
    "WlanOpenHandle", "WlanQueryInterface", "WlanReasonCodeToString",
    "WlanRegisterNotification", "WlanScan", "WlanSetInterface", "WlanSetProfile",
    "WlanSetProfileEapXmlUserData", "WlanSetProfileList", "WlanSetProfilePosition"
}};
constexpr const char* kHnAnchor = "WlanHostedNetworkQueryStatus"; // §3.2 H1
constexpr const char* kWfdAnchor = "WFDOpenHandle";               // §3.3 W1
constexpr const char* kV2Anchor  = "WlanGetAvailableNetworkList2"; // §3.7
} // namespace

std::string FeatureNotSupported::makeMessage(std::string_view f, std::string_view r) {
    std::string m(f);
    m += " desteklenmiyor: ";
    m += r;
    return m;
}

Resolver& Resolver::instance() noexcept {
    static Resolver r;   // tek iş parçacığı güvenceli inicializasyon
    return r;
}

Resolver::Resolver() noexcept {
    // Tek yükleme; referans sayacı YOK, FreeLibrary YOK (§4.6)
    module_ = LoadLibraryW(L"wlanapi.dll");
    if (!module_) return;   // yükleme yok → tüm feature'lar false → graceful degrade

    for (size_t i = 0; i < kCoreExports.size(); ++i)
        corePresent_[i] = ::GetProcAddress(module_, kCoreExports[i]) != nullptr;
    hnPresent_ = ::GetProcAddress(module_, kHnAnchor) != nullptr;
    wfdPresent_ = ::GetProcAddress(module_, kWfdAnchor) != nullptr;
    v2Present_ = ::GetProcAddress(module_, kV2Anchor) != nullptr;
}

FARPROC Resolver::address(std::string_view n) const noexcept {
    if (!module_) return nullptr;
    // GetProcAddress daraltmaz (NUL-sondaki string alır); std::string ara kopya üretir
    std::string tmp(n);
    return ::GetProcAddress(module_, tmp.c_str());
}

bool Resolver::loaded() const noexcept { return module_ != nullptr; }

bool Resolver::has(std::string_view exportName) const noexcept {
    return address(exportName) != nullptr;
}

void Resolver::require(std::string_view exportName, Feature f) const {
    if (!module_)
        throw FeatureNotSupported(exportName, "wlanapi.dll yuklenemedi");
    if (address(exportName) == nullptr)
        throw FeatureNotSupported(exportName, "bu isletim sisteminde export yok");
    (void)f;   // feature etiketi yalnızca telemetri/log içindir
}

bool Resolver::featureAvailable(Feature f) const noexcept {
    if (!module_) return false;
    switch (f) {
    case Feature::Core:            for (bool b : corePresent_) if (!b) return false; return true;
    case Feature::BssList:         return has("WlanGetNetworkBssList"); // §2.4: destek ayrıca kontrol
    case Feature::HostedNetwork:   return hnPresent_;    // §3.2 H6 adaptör koşulu çağrı içinde
    case Feature::WifiDirect:      return wfdPresent_;
    case Feature::Hs20Profile:     return has("WlanSetProfile");   // şema sürümü profil XML'inde
    case Feature::DualSta:         return false;         // §3.4 kararı: doğrulanana kadar KAPALI
    case Feature::NetworkListV2:   return v2Present_;
    }
    return false;
}

void Resolver::setNegotiatedVersion(DWORD v) noexcept { negotiated_ = v; }
DWORD Resolver::negotiatedVersion() const noexcept { return negotiated_; }

} // namespace neva::wlan
~~~

### §4.4 Sarmalayıcılar: dönüş kodu kapısı (ikinci katman)

~~~cpp
// wlan_wrappers.h — ÖRNEK: uygulamanın gördüğü tek arayüz
#pragma once
#include "resolver.h"
#include <wlanapi.h>

namespace neva::wlan {

// §2.4 ikili kapı: export VAR + dönüş kodu KONTROL
inline DWORD openHandle(DWORD clientVersion, DWORD& negotiated, HANDLE& h) {
    using Fn = DWORD (WINAPI*)(DWORD, PVOID, PDWORD, PHANDLE);
    auto fn = Resolver::instance().get<Fn>("WlanOpenHandle", Feature::Core);
    DWORD rc = fn(clientVersion, nullptr, &negotiated, &h);
    if (rc == ERROR_SUCCESS)
        Resolver::instance().setNegotiatedVersion(negotiated);  // §4.5
    return rc;   // XP: WZC servisi yoksa hata burada görünür [K4]
}

// §2.4 örneği: XP'de export var, destek yok → istisna, çökme değil
inline DWORD getNetworkBssList(HANDLE h, const GUID* g, DWORD flags,
                               PWLAN_BSS_LIST* out) {
    using Fn = DWORD (WINAPI*)(HANDLE, const GUID*, DWORD, PVOID, PWLAN_BSS_LIST*);
    auto fn = Resolver::instance().get<Fn>("WlanGetNetworkBssList", Feature::BssList);
    DWORD rc = fn(h, g, flags, nullptr, out);
    if (rc == ERROR_NOT_SUPPORTED)   // MSDN: XP SP3/SP2 client [K5]
        throw FeatureNotSupported("WlanGetNetworkBssList",
                                  "bu surumde desteklenmiyor (ERROR_NOT_SUPPORTED)");
    return rc;
}

// §3.7: List2 → hata → List1 yedeği (kullanıcı sözleşmesi değişmez)
inline DWORD getAvailableList(HANDLE h, const GUID* g, DWORD flags,
                              PWLAN_AVAILABLE_NETWORK_LIST** outV1,
                              PWLAN_AVAILABLE_NETWORK_LIST_V2** outV2);

} // namespace neva::wlan
~~~

§3.2 H6 (donanım koşulu) ve §3.4 D3 (runtime tespit) sarmalayıcı seviyesinde ek koşul
olarak uygulanır: export kapısı **yalnızca** ilk elevatördür.

### §4.5 OS sürümü nerede? (yalnız mesaj)

| Kullanım | Araç | Karar akışına girer mi? |
|---|---|---|
| Export kapısı | `GetProcAddress` | **EVET** — tek karar kaynağı (Y2) |
| Müzakere edilen API sürümü | `WlanOpenHandle` `pdwNegotiatedVersion` (§4.4) | Hayır — telemetri/rapor; XP=1 müzakeresi başarıyla açıldığını gösterir [K4] |
| Kullanıcı mesajı ("Bu özellik Windows 10 2004+ gerektirir") | `IsWindowsVersionOrGreater` (VersionHelpers, RtlGetVersion tabanlı) veya doğrudan `RtlGetVersion` | **Hayır** — yalnız metin üretimi [K15][K17] |

Sebep: `GetVersionEx` bilinen biçimde manifest'siz "6.2" döner (version-lie); VersionHelpers
manifest koşuluna takılmadan doğru sürümü verir — ama yine de **karar için kullanılmaz**,
çünkü sürüm > export eşlemesi her zaman doğru değildir (§1.1 Y2 gerekçesi, 01 D2).

### §4.6 DLL yaşam döngüsü

| Kural | Gerekçe |
|---|---|
| `LoadLibraryW` **bir kez** (ilk `instance()` çağrısında) | İlk çağrıdan sonra tablo salt-okunur hale gelir |
| **Asla `FreeLibrary`** | [K7]: kayıtlı bildirim callback'i "until the client unregisters or closes the handle" çağrılır; handle kapatılmadan DLL boşaltılırsa callback ASLA unload edilmiş kodu çağırır → `0xC0000139/AV` |
| Referans sayacı (ref-count) **yok** | Eşzamanlı ilk çağrı + son kullanıcı kapanışı arasında sayacın sıfırlanması yarışı; tek süreçte bellek maliyeti ihmal edilebilir (01 D4/WlanCloseHandle race gotcha'sı ailesi) |
| Bildirimler için sıra: `WlanRegisterNotification(NONE)` → `WlanCloseHandle` → (DLL boşaltma **yok**) | [K7]: handle kapatılınca kayıt "automatically undone" |
| Callback içinden `WlanRegisterNotification` **çağrılmaz** | [K7]: deadlock — "the call never completing" |

---

## §5 Karar Ağacı (runtime, her API çağrısında)

~~~text
Çağrı örneği: getAvailableList(...) / hostedNetworkQueryStatus(...)
│
├─ (0) Resolver instance ilk kez açılıyor mu?
│     ├─ LoadLibraryW("wlanapi.dll") başarısız
│     │     → module_ = null → TÜM feature'lar false
│     │     → FeatureNotSupported("wlanapi.dll yuklenemedi") — ÇÖKME YOK
│     └─ başarılı → §4.3 tablosu doldurulur (bir kez)
│
├─ (1) EXPORT KAPISI  [Y2 — karar burada verilir]
│     GetProcAddress(hedef) == null ?
│     ├─ EVET → FeatureNotSupported("export yok")  → UI: "Bu Windows sürümünde özellik yok"
│     └─ HAYIR ↓
│
├─ (2) ÖZEL KOSUL KATMANI  (özellik bazlı — §3)
│     ├─ Core(BssList)   : XP → çağrı yapılır, rc == ERROR_NOT_SUPPORTED ? → istisna (§4.4)
│     ├─ HostedNetwork   : export ∧ adaptör capable? (§3.2 H6)
│     │                    └─ hayır → "Donanım/hosted network kurulu değil" (istisna değil, durum)
│     ├─ WifiDirect      : WFDOpenHandle var mı (§3.3 W1)
│     │                    └─ W2-W4 (PLAP/AP private) → her koşulda KAPALI (kapsam kararı)
│     ├─ Hs20Profile     : şema Win10+ profile ait → WlanSetProfile hata verirse geri çekil
│     ├─ NetworkListV2   : export null → List1'e DÜŞ (yedek, hata değil — §3.7)
│     │                    export var → çağır → hata → List1'e DÜŞ + telemetri
│     ├─ DualSta         : false (§3.4 D3 doğrulanana kadar)
│     └─ FTM             : TANIMLI DEĞİL (§3.6 — public API yok)
│
├─ (3) ÇAĞRI + DÖNÜŞ KODU KONTROLU  [ikinci katman — §2.4]
│     rc != ERROR_SUCCESS ?
│     ├─ ERROR_NOT_SUPPORTED / ERROR_INVALID_FUNCTION → FeatureNotSupported (sürüm reddi)
│     ├─ ERROR_NOT_READY / ERROR_SERVICE_NOT_ACTIVE    → durum kodu (servis/WZC — §3.1, §3.4 H6)
│     └─ ERROR_SUCCESS ↓
│
└─ (4) BAŞARI → sonuç UI'a; negotiated version + OS banner yalnız LOG/telemetri (§4.5)
~~~

---

## §6 Test Matrisi

### §6.1 Pozitif smoke (her uçta çalışır, §01 faz planı ile uyumlu)

| # | Ortam (export) | Test | Beklenen |
|---|---|---|---|
| P1 | XP SP3 x86 (36) | §3.1 beyaz liste: openHandle→enumInterfaces→scan→availableList→connect (test AP) | 20 fonksiyonun tamamı `ERROR_SUCCESS`; liste dolu |
| P2 | XP SP3 x86 | `WlanRegisterNotification(ACM)` + bağlantı kesme | 2 bildirim gelir (§3.1 #8); Netman durumlu sürümde log'a uyarı |
| P3 | Win7 x86/x64 (48) | Hosted Network: `QueryStatus` + `ForceStart` | export kapısı açık; adaptör capable ise aktif durum (§3.2 H6) |
| P4 | Win8 (103) | `WFDOpenHandle` + oturum başlat/iptal | `ERROR_SUCCESS`, `WFD_API_VERSION` müzakeresi (§3.3 W1) |
| P5 | Win10 10240 (121) | HS2.0 profile `WlanSetProfile` | profil kabul (§3.5 A1) |
| P6 | Win10 19041 (148) | List2 yolu | `WLAN_AVAILABLE_NETWORK_LIST_V2` döner; List1 fallback tetiklenmez |
| P7 | Win11 22000 (149) | Win11 derlemesi açılış smoke | derleme SDK 22000.194 (§3.4 D1); dual-STA kapalı → hata üretmez |

### §6.2 Negatif (çökme yasağı §1.1 Y1'in kanıtı)

| # | Senaryo | Yöntem | Beklenen |
|---|---|---|---|
| N1 | XP + `WlanGetNetworkBssList` | §4.4 sarmalayıcı doğrudan çağırılır | `FeatureNotSupported`; süreç yaşar; olay logunda "ERROR_NOT_SUPPORTED" (§2.4) |
| N2 | XP + HostedNetwork/WFD/HS20/List2 | `featureAvailable()` → çağırma bile denenmez | false; `FeatureNotSupported`; UI'da "Bu Windows sürümünde yok" |
| N3 | XP + §2.5 siyah listeden çağrı (bilinçli ihlal) | 16 export'tan biri sarmalayıcısız çağrılırsa (statik analiz/RULE engellemeli) | kural ihlali CI'da yakalanır; runtime'da dönüş kodu kapısı yine de `FeatureNotSupported` üretir |
| N4 | List2 export'u olmayan sürüm (Win7) | §3.7 yedeği | sessiz List1; UI farkı 0 (§3.7 madde 4) |
| N5 | List2 export'u var, çağrı hata döner (Win8/8.1 tahmini) | §3.7 madde 3 | List1 + telemetri; çökme yok |
| N6 | `wlanapi.dll` eksik/engelli (kurulum bozuk testi) | dizinden kaldırıp başlat | "yuklenemedi" istisnası; tüm P* testleri yerine N-duruma düşer |
| N7 | Bildirim yarışı | kayıt → `WlanCloseHandle` → süreç kapanışı sırasında bildirim beklenir | §4.6 sırası ile callback unload edilmiş kodu **asla** çağırmaz (kilitli test: ASan/HWBP ile 10⁴ döngü) |
| N8 | Çift giriş ihlali | kod tabanında `GetProcAddress("Wlan` aranır (CI grep) | yalnız `resolver.cpp` içinde hits; aksi → pipeline FAIL (Y3) |
| N9 | Eşzamanlı ilk çağrı | 32 thread aynı anda `instance()` | magic-static tek instance; tablo tekrar doldurulmaz (data race yok) |

**Kabul kriteri:** hiçbir testte `0xC0000139` (import trambolini), `0xC0000005` veya
process-kill gözlenmez; tüm olumsuz durumlar ya `FeatureNotSupported` ya dönüş kodu
durumundadır.

---

## §7 Açık Konular ve Kaynaklar

### §7.1 Açık konular

| # | Açık | Etki | İşaret |
|---|---|---|---|
| 1 | Win11 149 export dump'ı **x64**; XP..19041 matrisi "export silinmez" kuralını korur, Win11 −2 private export silmiştir | Mimarî not: kural 19041'de biter | ⚠️ VERIFICATION REQUIRED (x86 Win11 dump'ı ile teyit) |
| 2 | §2.5'teki 15 fonksiyonun XP'deki gerçek davranışı (bilinen: #1 BssList reddi, #7 IHV ihmal) | Siyah liste güvenliği | ⚠️ VERIFICATION REQUIRED |
| 3 | `WlanGetAvailableNetworkList2` Win8 imzasında mı? (MSDN = Win10, 01 disk = Win8 delta) | Yalnız List1 fallback frekansı | MSDN Win10 teyitli; export ilk sürümü ⚠️ VERIFICATION REQUIRED |
| 4 | PLAP/StartAP/WfdGO export'larının public MSDN belgesi | W2-W4 kapsam dışı kararının kalıcılığı | ⚠️ VERIFICATION REQUIRED (belge bulunursa karar yeniden açılır) |
| 5 | Dual-STA runtime tespit fonksiyonu/opcode adı | §3.4 D3 — özellik kapalı kalıyor | ⚠️ VERIFICATION REQUIRED (SDK 22000.194 header taraması) |
| 6 | Manifest version-lie etkisi yalnız `GetVersionEx` içindir; yine de VersionHelpers/RtlGetVersion karara sokulmuyor | Bilgi | Karar zaten export'a dayalı — kapanış gerekmez |
| 7 | XP hedefi kapsam sorusu (01 açık konu #4: XP SP3 mü, Win7+ mi?) | §6'daki P1/P2/N1/N3 testlerinin çalışacağı laboratuvar | Beklemede (kullanıcı kararı) |

### §7.2 Kaynaklar

| # | Kaynak | URL | Güvenilirlik |
|---|---|---|---|
| K1 | MS Learn — Wireless LAN API for Windows XP SP2 (20 fonksiyon + kısıtlamalar) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-wireless-lan-api-for-windows-xp-service-pack-2 | Yüksek (resmi) |
| K2 | MS Learn — What's New in Native Wifi (Win7 HN/VS · Win8 WFD · Win10 HS2.0) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/what-s-new-in-native-wifi | Yüksek (resmi) |
| K3 | MS Learn — WlanHostedNetworkQueryProperty (min Windows 7 / Server 2008 R2 + Wireless LAN Service) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanhostednetworkqueryproperty | Yüksek (resmi) |
| K4 | MS Learn — WlanOpenHandle (sürüm 1/2, WLAN_API_MAKE_VERSION, XP WZC notu) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanopenhandle | Yüksek (resmi) |
| K5 | MS Learn — WlanGetNetworkBssList (min Vista; XP'de ERROR_NOT_SUPPORTED) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetnetworkbsslist | Yüksek (resmi) |
| K6 | MS Learn — Dual-STA query and configuration (Windows 11 SDK 10.0.22000.194) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/dual-sta-query-config | Yüksek (resmi) |
| K7 | MS Learn — WlanRegisterNotification (XP/Netman, HNWK Win7+, auto-undo, deadlock) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanregisternotification | Yüksek (resmi) |
| K8 | MS Learn — WFDOpenHandle (Wi-Fi Direct = Win8 yeni özellik) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wfdopenhandle | Yüksek (resmi) |
| K9 | MS Learn — WlanQueryInterface (XP'de yalnız 4 opcode) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanqueryinterface | Yüksek (resmi) |
| K10 | MS Learn Q&A — Wi-Fi RTT/802.11mc için public API yok | https://learn.microsoft.com/en-us/answers/questions/309831/support-for-wi-fi-round-trip-time-(wi-fi-rtt)-ieee | Orta-Yüksek (MS moderatörlü Q&A) |
| K11 | NirSoft — Windows XP wlanapi.dll (36 export, 5.1.2600.5512) | https://xpdll.nirsoft.net/wlanapi_dll.html | Orta (tanınmış araç sitesi; 01 doc MD5/ürün sürümü ile birebir) |
| K12 | NirSoft — Windows 7 wlanapi.dll (48 export, isim listesi) | https://win7dll.nirsoft.net/wlanapi_dll.html | Orta (01 doc delta ile birebir) |
| K13 | jesusninoc — wlanapi.dll export dump (PLAP/ConnectEx/List2/HN varlık çapraz kontrolü) | https://www.jesusninoc.com/dll/wlanapi/ | Orta (üçüncü taraf dump; yalnız varlık kanıtı, sürüm atfı kullanılmadı) |
| K14 | MS Learn — OID_WDI_TASK_REQUEST_FTM (FTM sürücü seviyesi) | https://learn.microsoft.com/en-us/windows-hardware/drivers/netcx/oid-wdi-task-request-ftm | Yüksek (resmi, WDK) |
| K15 | MS Learn — VersionHelpers (IsWindowsVersionOrGreater, RtlGetVersion tabanlı) | https://learn.microsoft.com/en-us/windows/win32/api/versionhelpers/ | Yüksek (resmi) |
| K16 | MS Learn (arşiv) — WlanGetAvailableNetworkList2: min client **Windows 10** | https://learn.microsoft.com/en-us/previous-versions/mt595902(v=vs.85) | Yüksek (resmi arşiv) |
| K17 | MS Learn — RtlGetVersion (manifest'ten bağımsız sürüm sorgusu) | https://learn.microsoft.com/en-us/windows/win32/api/winternl/nf-winternl-rtlgetversion | Yüksek (resmi) |
| K18 | MS Learn — GetProcAddress (export kapısı mekanizması) | https://learn.microsoft.com/en-us/windows/win32/api/libloaderapi/nf-libloaderapi-getprocaddress | Yüksek (resmi) |
| K19 | 01 doc (disk) — pconlife DLL envanteri + export matrisi (MD5 ✓, REA/Ghidra çapraz kontrol) | [[01-wlanapi-sürüm-matrisi-ve-windows-plani]] | Yüksek (disk kanıtı) |
| K20 | WindowsDllsExport arşiv dump — Win10-19043 (148) | https://raw.githubusercontent.com/Mr-Un1k0d3r/WindowsDllsExport/main/Win10-19043/wlanapi.dll.txt | Orta (üçüncü taraf arşiv; 19041 sayımıyla eşleşti) |
| K21 | WindowsDllsExport arşiv dump — Win11-22000 (149, +3/−2) | https://raw.githubusercontent.com/Mr-Un1k0d3r/WindowsDllsExport/main/Win11-22000/wlanapi.dll.txt | Orta (üçüncü taraf arşiv) |

---

**Authority:** 01 doc export matrisi (disk) + Microsoft Learn Native Wifi (K1-K18) + arşiv dump (K20-K21)
**Kod durumu:** §4 kodları örnek amaçlıdır — diske yazılmaz, uygulama kullanıcı onayıyla başlar (01 §4.3).
**Commit:** bu dokümanın commit'i orkestratöre aittir (subagent atmaz).