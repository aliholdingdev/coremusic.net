---
title: "Neva Connect — XP SP1 Kaynak Arşivi WZC Stack Araştırması (nt5src · wzcsvc · NDISUIO · XP Kablosuz Paneli)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "tongzx/nt5src (XP SP1 source, commit daad8a087) disk analizi 2026-10-15 + Microsoft Learn (01-06 dokümları)"
updated: 2026-10-15
---

# 07 — XP SP1 Kaynak Arşivi: WZC Stack Araştırması

> **TL;DR:** Kullanıcı yönergesiyle `tongzx/nt5src` (2020 /g/ sızıntısı, XP SP1 + SRP ağaçları,
> 277.868 dosya · 1.52 GiB pack) `Temp\opencode\nt5src` konumuna klonlandı ve tarandı.
> **Büyük bulgu: XP SP1 kaynağında `wlanapi.dll` YOK** (Native Wifi API SP2'de eklendi) —
> XP'nin kablosuz yığınının kaynağı `NT/net/layer2svc/` altında **WZC (Wireless Zero
> Configuration)** adıyla tam mevcut: servis (`wzcsvc`), istemci DLL (`wzcsapi`),
> RPC IDL, komut satırı aracı, MMC snap-in ve **orijinal XP kablosuz ağ paneli UI kodu**
> (`net/config/shell/lanui/`). 01 export matrisimiz (XP SP3 = 36 Wlan*) ile kaynağın
> ilişkisi §5'te: **wlanapi.dll SP2/SP3'te WZC üzerine ince bir sarmalayıcıdır; NDISUIO
> OID katmanı ortaktır.** Neva Connect XP bacağı için bu, iki stratejik sonuç üretir:
> (1) XP UI davranışı artık "kara kutu" değil — orijinal panel kodu referans alınabilir;
> (2) wlanapi bulunmayan/çöken durumlarda IOCTL_NDISUIO doğrudan yolu belgelendi.

> **Yasal/etik not:** Bu ağaç sızıntı bir arşivdir (README: "Leaks are not from me").
> Vault'ta yalnızca **yapı, isim ve mimari kanıt** tutulur; kaynak kod gövdesi kopyalanmaz,
> üretim koduna satır taşınmaz. Amaç: interoperate/uyumluluk araştırması (WZC'nin
> davranışını anlama), kopyalama değil.

---

## §1 Arşiv Künyesi ve Doğrulama

| Alan | Değer | Kanıt |
|---|---|---|
| Repo | `https://github.com/tongzx/nt5src` | klon 2026-10-15 |
| Commit | `daad8a087a4e75422ec96b7911f1df4669989611` ("Add source files", 2020) | `git rev-parse HEAD` |
| Ağaç | `Source/XPSP1/NT/` (21 üst dizin: admin·base·com·developer·drivers·ds·enduser·inetcore·inetsrv·loc·mergedcomponents·multimedia·**net**·printscan·public·published·sdktools·shell·termsrv·tools·windows) | disk ölçümü |
| Boyut | pack 1.52 GiB · 255.506 nesne · 1.52 GiB checkout (XPSP1/NT tek ağaç) | `git count-objects -vH` |
| Kapsam | XP SP1 kaynak ağacı + SRP bileşenleri; sızıntı ~%70 eksiksiz (kripto/aktivasyon eksik — harici kaynak notu, `⚠️ VERIFICATION REQUIRED`) | README + web (rentry/wxp, xieby1 notları) |
| Kurulum yeri | `C:\Users\MARHAN\AppData\Local\Temp\opencode\nt5src` — SİSTEM DİZİNİNE kopyalanmadı, salt analiz | 01 doc kuralı korundu |

**Kurulum notları (tekrar için):** `git clone --depth 1` timeout'a takılırsa pack iner ama
checkout yarım kalır → `git status` her dosyayı `D` gösterir → `.git/index.lock` silinir +
`git reset --hard HEAD` (checkout -- . DEĞİL; index boşken pathspec '.').

---

## §2 Ana Bulgu: XP SP1'de wlanapi.dll Yok, WZC Var

### §2.1 Arama sonuçları (disk kanıtı)

| Arama | Sonuç |
|---|---|
| `WlanOpenHandle` (grep, `XPSP1/NT/net` içi) | **0 eşleşme** |
| Dosya adı `wlanapi*` (tüm ağaç) | **0 dosya** |
| Dosya adı `wzc|wlan` (tüm ağaç) | **60+ dosya** — hepsi WZC (§2.2) |

Tutarlılık: Microsoft, Native Wifi API'yi (wlanapi.dll) **Windows XP SP2** ile "Wireless LAN
API for Windows XP with SP2" olarak çıkardı (01/03 doc'larında MSDN kaynaklı). SP1 ağacında
wlanapi'nin olmaması = beklenen durum, sızıntı bütünlüğü lehine kanıt.

### §2.2 WZC stack envanteri (bulunan kaynak yolları — hepsi diskte doğrulandı)

| Bileşen | Yol (`NT/` göreli) | İçerik |
|---|---|---|
| **Servis (RPC sunucu)** | `net/layer2svc/zeroconf/server/` | `wzcsvc.c/.h/.def/.rc` + `deviceio.c` (37 KB) + `hash.c` (20 KB) + `dialog.c` … |
| **İstemci DLL** | `net/layer2svc/zeroconf/client/` | `wzcsapi.c` (18 KB) + `wzcsapi.def` + `wzcdbapi.c` + `rpcbind.c` + `util.c` (28 KB) |
| **RPC IDL** | `net/layer2svc/zeroconf/idl/` | `wzc.idl` (UUID `621dff68-3c39-4c6c-aae3-e68e2c6503ad`, endpoint `mscn_np:[\\pipe\\wzcsvc]`) + `wzccli.acf`/`wzcsrv.acf` |
| **Komut satırı** | `net/layer2svc/zeroconf/cmdtool/` | `wzctool.c` + `cmds.c`/`cmdq.c`/`cmde.c` (XP içinde netsh benzeri iç araç) |
| **Orta katman sürücü** | `net/ndis/uio/` | NDISUIO sürücü kaynağı **VAR** (2026-10-15 düzeltme — önceki "YOK" iddiası yanlıştı): `sys/ndisuio.h` (15.8 KB), `sys/ndisbind.c` (53.4 KB), `sys/debug.c`, `inf/ndisuio.inx`, `uiosample.htm` (22.6 KB örnek!) + kullanıcı başlığı `net/inc/nuiouser.h` — felaket yolu (09 doc) için birincil kanıt |
| **Genel başlık** | `net/inc/wzcsapi.h` (31.7 KB) | `WZC_WLAN_CONFIG`, `INTF_ENTRY`, `INTF_BSSID*` flag'leri, EAPOL API prototipleri |
| **Servis INF** | `net/config/netcfg/inf/netwzc.inx` | `Service=wzcsvc`, `AddService=WZCSVC`, `ServiceType=0x20`, binary `svchost.exe -k netsvcs` |
| **XP kablosuz paneli (UI)** | `net/config/shell/lanui/` | `wzcui.cpp` (72 KB), `lanui.cpp` (150 KB!), `util.cpp` (67 KB), `wzcpage.cpp` (22 KB), `chklist.cpp`, `eapolpage.cpp`, `saui.cpp` (WPA UI), `lanwiz.cpp` (sihirbaz) |
| **EAPOL/802.1X** | `net/layer2svc/eapol/` (55 dosya) | kimlik doğrulama yığını |
| **Profil deposu** | `net/layer2svc/wastore/` (23), `wlstore/` (23) | yapılandırma saklama (DS store), bağlantı UI yardımcıları |
| **Politika** | `net/layer2svc/wlpolicy/` (28) | EAPOL politika motoru |
| **MMC snap-in** | `net/layer2svc/wlsnp/` (97 dosya) | yönetimsel konsol |
| **Olay günlüğü monitörü** | `net/layer2svc/monitor/` (11) | `wzclog.c`, `database.c` |
| **WZC bileşen tanımı** | `net/layer2svc/componentdef/`, `net/config/shell/res/wzcui.bmp` | Add/Remove Program bileşeni |
| **Yayılım başlıkları** | `net/published/inc/wzcsvc.w`, `wzcdlg.w`; `public/internal/net/inc/wzcsvc.h`, `wzcdlg.h` + i386/ia64 `.lib` | servis/diálog export'ları |

### §2.3 Servis yaşam döngüsü (INF kanıtı)

```
wzcsvc = SERVICE_WIN32_SHARE_PROCESS (0x20), svchost.exe -k netsvcs içinde
AddService = WZCSVC · DisplayName = %WZCSVC_Desc% · ayrı EventLog kaydı
```

Neva Connect etkisi (03 doc §6 "WZC yok" kapısı ile aynı konu, artık **kaynak kanıtlı**):
XP'de kablosuz, `WZCSVC` servisi ayaktaysa anlamlıdır; `WlanOpenHandle` → sunucu tarafı bu
servise RPC atar (§3.3). Serviz durdurulmuşsa Vista öncesi yığın **ERROR_NOT_FOUND benzeri**
döner ve panel "kablosuz ağ yok" gösterir — 05 doc §6 Level-1 mesajımızla birebir örtüşüyor.

---

## §3 Mimari: XP Kablosuz Yığınının Katmanları (kaynak kanıtlı)

### §3.1 Katman diyagramı

```
[XP Kablosuz Paneli — lanui/wzcui.cpp]        [bizim neva-wlan.exe karşılığı: 05 doc §2 L4]
        │  WZCSAPI fonksiyonları (§3.4)                │  wlanapi.dll (SP3, 36 export — 01 doc)
        ▼                                              ▼
   wzcsapi.dll ──── RPC client (rpcbind.c, wzc.idl) ────┐
        │                                               │
        │          \\pipe\\wzcsvc (ncalrpc/np)          │  ← SP2/SP3'te wlanapi de bu hizmete
        ▼                                               │     düşer (şema aynı, `⚠️` §5.3)
   wzcsvc (svchost -k netsvcs)  [zeroconf/server/] ◄────┘
        │  deviceio.c: IOCTL_NDISUIO_QUERY/SET_OID_VALUE
        ▼
   NDISUIO miniport/intermediate sürücü ── OID_802_11_* (§3.5) ──► NIC firmware
```

### §3.2 WZC API yüzeyi (`wzcsapi.def` export listesi — tam, disk kanıtı)

```
WZCSvcMain · WZCGetSPResModule · WZCGetDlgResModule · WZCDeleteIntfObj
WZCEnumInterfaces · WZCQueryInterface · WZCSetInterface · WZCRefreshInterface
WZCQueryContext · WZCSetContext
WZCEapolGetCustomAuthData · WZCEapolSetCustomAuthData · WZCEapolGetInterfaceParams
WZCEapolSetInterfaceParams · WZCEapolReAuthenticate · WZCEapolQueryState · WZCEapolUIResponse
DtlDestroyList · CreateEapcfgNode · DestroyEapcfgNode · EapcfgNodeFromKey · ReadEapcfgList
WZCGetEapUserInfo · WZCEapolFreeState
OpenWZCDbLogSession · CloseWZCDbLogSession · AddWZCDbLogRecord · EnumWZCDbLogRecords
FlushWZCDbLog · GetSpecificLogRecord
MIDL_user_allocate(@60) · MIDL_user_free(@61)
```

**Gözlem:** `WZCEnumInterfaces / WZCQueryInterface / WZCSetInterface / WZCRefreshInterface`
isim ailesi, SP2'deki `WlanEnumInterfaces / WlanQueryInterface / WlanSetInterface` ile aynı
soyadı taşır — wlanapi bir **yeni protokol değil, yeni imzadır** (aynı veri modeli: arayüz
listesi → sorgu/set → BSS listesi).

### §3.3 RPC IDL (`wzc.idl` — winwzc arayüzü)

| Alan | Değer |
|---|---|
| UUID | `621dff68-3c39-4c6c-aae3-e68e2c6503ad` (v1.0) |
| Endpoint | `mscn_np:[\\pipe\\wzcsvc]` (named pipe) |
| Metotlar | `RpcEnumInterfaces` · `RpcQueryInterface` · `RpcSetInterface` · `RpcRefreshInterface` · `RpcQueryContext` · `RpcSetContext` + EAPOL + DBLog metotları |
| Context handle | `WZC_DBLOG_SESSION_HANDLE` (dblog oturumu) |
| Dönüş | her metot `DWORD` (0 = ERROR_SUCCESS; WZC kodları §3.6) |

**Neva Connect etkisi:** named pipe tabanlı — bizim WS 9742 köprüsümüz (05 §5) ile aynı
model; XP'te ek bağımlılık yok (rpcrt4 XP'te var).

### §3.4 Veri modeli (`net/inc/wzcsapi.h` — 31.7 KB, seçilmiş kanıtlar)

| Tip/konu | Kanıt (satır no → içerik) |
|---|---|
| `WZC_WLAN_CONFIG` | L54-61: "like NDIS_WLAN_BSSID, but contains all the additional fields" → `NDIS_802_11_MAC_ADDRESS MacAddress; // BSSID` |
| `WZC_802_11_CONFIG_LIST` | L80: "like NDIS_802_11_BSSID_LIST but indexes…" |
| `INTF_ENTRY` | L196-197: `RAW_DATA rdBSSID` (BSSID ham binary), `RAW_DATA rdBSSIDList` (`WZC_802_11_CONFIG_LIST`) |
| Sorgu flag'leri | L153-154: `INTF_BSSID 0x02000000`, `INTF_BSSIDLIST 0x04000000` (bitmask sorgu/doldurma) |
| SSID alanları | L642/L690: `BYTE bSSID[MAX_SSID_LEN]` (+NUL varyantı) |
| Prototip | L727+: `IN BYTE *pbSSID` (isteğe bağlı SSID bağlama) |

**Değerlendirme:** XP veri modeli NDIS 802.11 yapıları üzerine kuruludur; SP2+ wlanapi'nin
`DOT11_*` modeline geçişi = **imza dönüşümü** (05 doc §4.3 profil XML'i üretirken XP'ye özel
alan daraltmaları bu yapıdan türetilebilir).

### §3.5 Alt katman: NDISUIO + OID (`zeroconf/server/deviceio.c`, 37 KB)

Diskten seçilmiş IOCTL/OID izleri (satır → ad):

| Kategori | Kanıtlar |
|---|---|
| IOCTL çerçevesi | `IOCTL_NDISUIO_BIND_WAIT` (L38) · `IOCTL_NDISUIO_QUERY_BINDING` (L132, L201) · `IOCTL_NDISUIO_QUERY_OID_VALUE` (L891) · `IOCTL_NDISUIO_SET_OID_VALUE` (L939) |
| Kimlik | `OID_802_3_CURRENT_ADDRESS` (L293) |
| Mod/auth | `OID_802_11_INFRASTRUCTURE_MODE` (L430, L706) · `OID_802_11_AUTHENTICATION_MODE` (L476, L690) |
| Şifreleme | `OID_802_11_ADD_WEP` (L491) · `OID_802_11_REMOVE_WEP` (L511) · `OID_802_11_WEP_STATUS` (L530, L722) |
| SSID/BSSID | `OID_802_11_SSID` (L570, L782) · `OID_802_11_BSSID` (L595, L740) |
| Tarama | `OID_802_11_BSSID_LIST_SCAN` (L673) · `OID_802_11_BSSID_LIST` (L824) |

**Stratejik sonuç:** wlanapi.dll XP'de bileşiminde aynı OID setini sürer. Neva Connect XP
bacağında (a) normal yol: `wlanapi.dll` (01 doc 36 export, 03 doc gate) — (b) felaket
yolu: `\\.\NdisUio` handle + `IOCTL_NDISUIO_QUERY_OID_VALUE` ile doğrudan tarama/durum
(wzc yoksa bile çalışabilir; sürücü izni `⚠️ VERIFICATION REQUIRED` — test edilmeli).
Bu (b) yolu 05 doc §9'da "felaket yolu" olarak açık maddeydi → artık **uygulanabilirliği
kaynakla desteklendi** (kapsam kararı kullanıcıya: yalnız XP mi, XP+7 fallback mi).

### §3.6 XP UI paneli referansı (`net/config/shell/lanui/`)

Kullanıcının "wifi list paneli" direktifi için orijinal XP davranışı:

| Dosya | Boyut | Rol |
|---|---|---|
| `lanui.cpp` | 150 KB | ağ listesi sayfası çekirdeği (CMultiSlotEnumerator vs.) |
| `wzcui.cpp` | 72 KB | WZC sayfa arayüzü |
| `util.cpp` | 67 KB | ortak yardımcılar (liste/sıralama/filtre) |
| `wzcpage.cpp` | 22 KB | özellik sayfası |
| `chklist.cpp` | 26 KB | kullanılabilir ağ kontrol listesi |
| `eapolpage.cpp` | 20 KB | 802.1X sayfası |
| `saui.cpp` | 10 KB | **WPA UI** (saui = secure auth UI) |
| `lanwiz.cpp` | 10 KB | kablosuz sihirbazı |
| `devdatatip.cpp` | 9 KB | veri ipucu |

**Ne için değer:** (1) XP'de panelin hangi durumda hangi metni gösterdiği (erişilebilirlik +
yerelleştirme: `net/config/netcfg/inf/usa/netwzc.txt`), (2) "bağlanılabilir değil" durumlarının
sınıflandırması (`chklist.cpp`), (3) sahne akışı — 05 doc §4 akışlarımız bu davranışla
hizalanabilir. **Kopyalama yasak** (§1 yasal not) — yalnız davranış referansı.

---

## §3.7 Tarama/Bağlanma Akışının Kaynaktan Çıkarımı (deviceio.c + wzcsapi.c + hash.c)

Kaynak dosyaların sırası, XP'de bir ağın listelenip bağlanma akışının **gerçek** yolunu
verir (satır numaraları disk ölçümü):

### §3.7.1 Bind & tarama (adım adım)

| # | Adım | Kanıt (dosya/satır) | Neva Connect karşılığı (05 §4) |
|---|---|---|---|
| 1 | NDISUIO binding bekleme | `IOCTL_NDISUIO_BIND_WAIT` (deviceio.c L38) | servis başlangıcında bir kez — 05 §2 `wlabase` init |
| 2 | Binding sorgu/enum | `IOCTL_NDISUIO_QUERY_BINDING` (L132, L201) | `WlanEnumInterfaces` karşılığı XP iç katmanı |
| 3 | MAC okuma | `OID_802_3_CURRENT_ADDRESS` (L293) | arayüz kimliği doğrulama |
| 4 | Tarama tetikleme | `OID_802_11_BSSID_LIST_SCAN` (L673) | `WlanScan` |
| 5 | Sonuç çekme | `OID_802_11_BSSID_LIST` (L824) + `WZC_802_11_CONFIG_LIST` (wzcsapi.h L80) | `WlanGetAvailableNetworkList` / `WlanGetNetworkBssList` |
| 6 | Sıralama/dedup | `hash.c` (20 KB) — WZC iç hash tablosu, BSSID-SSID indeksleme | 05 §4.1 "dedup (aynı SSID çok BSS)" — XP kendi de dedup yapar |

### §3.7.2 Bağlanma (adım adım)

| # | Adım | Kanıt | Not |
|---|---|---|---|
| 1 | Mod/auth hazırlama | `OID_802_11_INFRASTRUCTURE_MODE` (L430, L706) + `OID_802_11_AUTHENTICATION_MODE` (L476, L690) | açık/WEP/WPA ayrımı burada |
| 2 | Şifreleme kurma | `OID_802_11_ADD_WEP` (L491) / `OID_802_11_WEP_STATUS` (L530, L722) / `OID_802_11_REMOVE_WEP` (L511) | XP = WEP + WPA (saui.cpp); WPA2-PSK XP SP3'te sınırlı — `⚠️ VERIFICATION REQUIRED` |
| 3 | SSID bağlama | `OID_802_11_SSID` (L570, L782) + `INTF_ENTRY pbSSID` (wzcsapi.h L727) | `WlanConnect` profil yokken |
| 4 | BSSID kilidi | `OID_802_11_BSSID` (L595, L740) | spesifik AP'ye bağlanma (05 §4.3'te opsiyonel) |
| 5 | Sonuç/hata | RPC `DWORD` (wzc.idl §3.3) → wzctool `errctrl.c` çevirisi | Level-2 hata şeması (02 §5) |

### §3.7.3 Hash & cache notu (`hash.c`, 20 KB)

WZC sunucu tarafı BSSID listesini hash'te tutar (dosya adı + boyut kanıt). Bu, XP'de
`WlanGetAvailableNetworkList`'in **anlık** (kopya) olmasının kaynağına işaret eder:
sonuçlar servis cache'inden gelir, tarama beklemez — 05 §4.1'te "listele = cache, tara =
tetikle" ayrımını doğrular.

---

## §3.8 WZC'ye Giden Yolun Anatomisi (istemci + sunucu + izomorfizm)

### §3.8.1 İstemci tarafı (`zeroconf/client/`) — WZC'ye giden yolun anatomisi

| Dosya | Boyut | Rol | Neva Connect dersi |
|---|---|---|---|
| `rpcbind.c` | 3.6 KB | `\\pipe\\wzcsvc` named pipe bağlanma + RPC binding handle | pipe açma → binding → çağrışım sırası; bizim WS 9742 köprüsü (05 §5) aynı yaşam döngüsü |
| `wzcsapi.c` | 18.5 KB | WZCEnumInterfaces/Query/Set/Refresh + EAPOL sarmalayıcıları | XP'de "WlanXxx karşılığı" = buradaki imzalar (§3.2 soyad gözlemi) |
| `wzcdbapi.c` | 8.8 KB | DBLog oturum API'si (Open/Close/Add/Enum/Flush/Get) | olay günlüğü — bizim notification kuyruğu (05 §3) karşılığı |
| `util.c` | 28.0 KB | yapı dönüşüm/saflaştırma yardımcıları | XP struct'ları ↔ API struct'ları çevirisi |
| `midluser.c` | 1.2 KB | `MIDL_user_allocate/free` (def @60/@61) | RPC bellek sözleşmesi |

### §3.8.2 Sunucu tarafı (`zeroconf/server/`) dosya-envanteri

| Dosya | Boyut | Rol |
|---|---|---|
| `wzcsvc.c` | — | `ServiceMain` + RPC sunucu dispatcher |
| `deviceio.c` | 37.1 KB | NDISUIO IOCTL/OID katmanı (§3.5) |
| `hash.c` | 20.5 KB | BSSID/SSID cache hash (§3.7.3) |
| `dialog.c` | 3.6 KB | UI yönlendirme (UIResponse zinciri §3.8) |
| `wzcsvc.def` | — | `WZCSvcMain` + `WZCQueryGUIDNCSState` + `WZCTrayIconReady` export'ları |

> `WZCQueryGUIDNCSState` (wzcsvc.def): NCS (Network Connectivity Status) GUID sorgusu —
> XP'de "bu ağ internete çıkar mı" sorusunun kaynağı; neva-wlan status akışına (05 §4.2)
> XP'ye özel "internet erişimi var/yok" alanı eklenebilir (düşük öncelikli açık madde).

### §3.8.3 WZC ↔ modern wlanapi izomorfizm tablosu

| WZC (XP SP1 kaynağı) | Native Wifi (XP SP3+ / Vista+) | Not |
|---|---|---|
| `WZCEnumInterfaces` | `WlanEnumInterfaces` | aynı soyağacı (§3.2) |
| `WZCQueryInterface` | `WlanQueryInterface` | INTF_ENTRY bitmask ↔ opcode |
| `WZCSetInterface` | `WlanSetInterface` | yapısal karşılık |
| `WZCRefreshInterface` | `WlanScan` | tarama tetikleme |
| `WZCQueryContext/SetContext` | `WlanGetProfile`/`WlanSetProfile` (kısmi) | XP'de context = aktif yapılandırma |
| `WZCEapol*` (7 export) | WlanSetProfileEapUserData / Enterprise XML | 802.1X — modern yüzey profile taşındı |
| WZC DBLog API (6 export) | — (modernde yok) | XP'ye özgü olay günlüğü |
| — | `WlanRegisterNotification` | XP'de yok: olaylar dialog.c/UIResponse hattından akar |

**Sonuç:** XP WZC, modern wlanapi'nin **birebir atasıdır**; 05 doc'taki L0-L4 katmanları
XP'de aynı sorumlulukları paylaşır. Fark: bildirim modeli (XP = polling/sync, modern =
callback + kuyruk) — 05 §3 SPSC kuyruğu yalnız modern bacakta; XP'de 2s polling (05 §4.2
zaten böyle planlanmıştı → kaynağıyla doğrulandı).

## §3.9 EAPOL/802.1X Katmanı (net/layer2svc/eapol/ — 55 dosya)

| Alt birim | Kanıt | Rol |
|---|---|---|
| `eapol/` ana | `eapol.inx` + dirs/makefile | 802.1X kimlik doğrulama servis bileşeni |
| `wlpolicy/` (28) | `eapolpol.c/.h` | EAPOL politika motoru (hangi ağda 802.1X zorunlu) |
| `wastore/` (23) | `dsstore.c/.h` | DS (directory service) profili deposu |
| `wlstore/` (23) | `api.c`, `connui.h` | bağlantı UI veri katmanı |
| `wzcsapi.def` EAPOL export'ları | `WZCEapolGet/SetCustomAuthData`, `Get/SetInterfaceParams`, `ReAuthenticate`, `QueryState`, `UIResponse` | Enterprise akışının tam API yüzeyi |
| UI | `lanui/eapolpage.cpp` (20 KB) + `saui.cpp` (10 KB, WPA) | XP panelinde 802.1X/WPA sekmeleri |

**Neva Connect etkisi:** F1 kapsamı PSK ise bu katmana girilmez (04 §1). Fakat "neden
XP'de WPA2-Enterprise bağlanmıyor?" tipi destek sorularında kaynak artık okunabilir —
EAPOL UIResponse zinciri (UI → wzcsapi → wzcsvc → NIC) tek bakışta görünür.

---

## §4 Export Matrisi ile İlişki (01 doc'un XP satırı güncelleme ışığı)

| Sürüm | Wlan* export (01) | Aynı işi yapan WZC kaynağı (07 bulgusu) |
|---|---|---|
| XP SP1 | — (yok) | WZCEnumInterfaces/WZCQuery/… (wzcsapi.dll) — **kaynak tam** |
| XP SP3 | 36 (wlanapi.dll) | wlanapi.dll = WZC hizmeti üzerine ince katman; alt OID seti §3.5 ile aynı |

§5.3 (01 doc'un "winapi SP3 36 export'unun SP2 eki olduğu" iddiası): **kaynak yönünden
desteklendi** — SP1 ağacında wlanapi yok, WZC var; SP2 "Wireless LAN API" ekinin WZC'ye
istemci olması tutarlı. wlanapi'nin kendi kaynak ağacı bu sızıntıda YOK (SP2 sonrası) →
`⚠️ VERIFICATION REQUIRED` (binary analizimiz 01'de zaten var: 36 export).

---

## §5 Neva Connect XP Bacağı — Eyleme Geçirilebilir Sonuçlar

1. **XP panel davranışı referanslandı** (§3.6): wifi list/status/connect ekran metinleri ve
   durum sınıfları artık tahmin değil, orijinal koddan okunabilir davranış.
2. **Felaket yolu belgelendi** (§3.5): WZC yokken/bozukken NDISUIO OID yolu — XP'ye özel
   fallback tasarıma girebilir (kapsam kararı açık madde §6).
3. **RPC/pipe modeli** (§3.3): WZC'nin named pipe + DWORD hata sözleşmesi, bizim Level 0-3
   şemamızla (02 §5) uyumlu — XP hata kodu çevirileri için `wzctool` kaynak kodu referans.
4. **Profil modeli** (§3.4): `INTF_ENTRY` bitmask sorgu deseni → `WlanQueryInterface` opcode
   karşılıkları (03 §3) XP'de aynı bit haritasıyla çalışır.
5. **EAPOL kapsamı** (§2.2): WPA/WPA2-Enterprise XP'de WZC EAPOL katmanından geçer;
   04 doc F1 kapsamı "PSK yeterli"yse dokunma; Enterprise gerekiyorsa WZC EAPOL + `saui`
   davranışı referans alınır.

---

## §6 XP Sınırlarının Kaynakla Teyidi (MSDN beyaz listesi ↔ disk)

03 doc §2.5'te "XP'de MSDN 20 fonksiyon beyan ediyor, export 36" farkı vardı. Kaynak
ağacı bu farkı yapısal olarak açıklıyor:

| MSDN XP beyanı (03 §3) | Kaynak karşılığı (07) | Açıklama |
|---|---|---|
| 20 fonksiyon "destekleniyor" | wzcsapi.def'te 33 export + MIDL | MSDN listesi yalnız Native Wifi imzalarını kapsar; WZC'nin kendi yüzeyi kapsam dışı |
| `WlanGetNetworkBssList` XP'de yok (03 §2.5 reddi) | `OID_802_11_BSSID_LIST` (deviceio.c L824) ALT KATMANDA VAR | wlanapi XP'de BSS listesini `AvailableNetworkList`'e indirger; BSS düzeyine NDISUIO'dan geçilir → **XP felaket yolu (§3.5b) BSS verisi verebilir** |
| Yalnız 2 bildirim (connection_complete/disconnected) | wzcsvc dialog.c + monitor/ (wzclog.c) | XP sunucu tarafı olay üretir ama istemciye yalnız ikisi aynalanır |
| Profil adı = SSID, tek SSID | `INTF_ENTRY rdBSSID` tek binary + wzcsapi.h L642 `bSSID[MAX_SSID_LEN]` | yapı tek SSID'ye göre tasarlanmış |
| PHY türleri yok | OID listesinde `OID_802_11_*` PHY query YOK | `WLAN_INTERFACE_PHY_TYPE` XP'de hiç gelmez (03 §3 tablosu doğrulandı) |
| IHV extension yok | layer2svc altında IHV dizini YOK | doğrulandı |

---

## §7 wzctool (CLI) — XP WZC'nin Kendi Komut Satırı

`net/layer2svc/zeroconf/cmdtool/`: Microsoft'un kendi XP WZC test/CLI aracı (dosya kanıtı:

| Dosya | Boyut | Rol |
|---|---|---|
| `wzctool.c` | 2.4 KB | ana / komut yönlendirici |
| `cmds.c` | 6.7 KB | komut işleme |
| `cmdq.c` / `cmde.c` | 3.2 KB / 0.9 KB | kuyruk / hata kontrol yardımcıları |
| `errctrl.c` | 1.2 KB | hata kontrol |
| `wzcutil.c/.h` | 2.2 KB | yardımcılar |

**Ne için değer:** (1) XP üzerinde WZC'ye RPC atmanın kanıtlanmış istemci kalıbı
(`rpcbind.c` 3.6 KB — pipe bağlanma), (2) 06 doc §2'deki birim testlerine XP-specific
senaryo: "WZC RPC hattı kopukken List1'e düşme", (3) XP VM smoke testlerinde (06 §4)
referans CLI: WZC durumu wzctool ile doğrulanabilir, sonra neva-wlan.exe koşulur.

---

## §8 Vault Etki Matrisi (bu dokümanın diğer doc'lara girdisi)

| Doc | Etki | Aksiyon |
|---|---|---|
| `01` (export matrisi) | XP satırı bağlamlandı (wlanapi = WZC sarmalayıcısı, SP2 eki) | 01'e §5.4 notu önerilir (opsiyonel) |
| `03` (feature gate) | XP beyaz liste ↔ 36 export farkı yapısal açıklandı (§9) | 03 §2.5'teki `⚠️` 15 maddesinin bir kısmı gerekçelendi |
| `05` (çekirdek modül) | XP felaket yolu (NDISUIO) artık kaynaklı (§3.5b, §3.7) | 05 §9 "felaket yolu" maddesi güncellenebilir |
| `06` (test planı) | XP uç satırına WZC-servis-kapalı negatifi + wzctool doğrulama eklenebilir | 06 §4 XP satırına 2 adım |
| `04` (kapsam) | F1 PSK kapsamı korunur; EAPOL katmanı referanslandı (§3.9) | değişiklik yok |
| mimari node (K014 connectivity) | XP katmanı "kara kutu" olmaktan çıktı; L0 altına "XP WZC köprüsü" notu düşülebilir | opsiyonel |

---

## §9 Açık Konular (bu tur)

| # | Madde | Durum |
|---|---|---|
| 1 | XP NDISUIO doğrudan yolu sürücü izniyle çalışır mı (WZC kapalıyken)? | `⚠️ VERIFICATION REQUIRED` — XP VM'de test |
| 2 | wlanapi.dll (SP2+) kendi kaynağı sızıntıda var mı (Win2K3 ağacı)? | bu repo XPSP1 tek ağaç — `UNKNOWN` |
| 3 | Felaket yolu kapsamda mı (yalnız XP / XP+7)? | kullanıcı kararı |
| 4 | lanui.cpp'den durum metni/sınıflandırma çıkarımı (vault'a ikinci doc)? | talep gelirse `08-xp-panel-davranis-sozlesmesi` |
| 5 | XP hedef onayı hâlâ açık (02 doc v141_xp kurulumu) | kullanıcı kararı |
| 6 | ~~NDISUIO sürücü kaynağı repo'da YOK~~ | ~~`⚠️ VERIFICATION REQUIRED`~~ **KAPANDI (2026-10-15)**: `net/ndis/uio/` diskte mevcut (09 doc §2 doğruladı) |
| 7 | XP WPA2-PSK desteği sınırı (saui.cpp referansı, §3.7.2 notu) | `⚠️ VERIFICATION REQUIRED` |

---

## §9.1 XP Panel Davranış Sınıfları (lanui kaynağı — çıkarım listesi)

`net/config/shell/lanui/` ağacı, XP'nin kablosuz panelinin durum sınıflandırmasını
tanımlar. Vault'a tam çıkarım ayrı doc işidir (§9.4); bu turda **sınır çıkarımları**:

| Sınıf | Kaynak ipucu | neva-wlan status (05 §4.2) karşılığı |
|---|---|---|
| Arayüz "not_ready" | WLAN_INTERFACE_STATE dökümü (03/native-wifi doc §2) + lanui durum metinleri | `state:"not_ready"` + kullanıcı metni |
| Bağlı + sinyal | `chklist.cpp` (26 KB) + `devdatatip.cpp` (9 KB) — satır içi veri ipucu | `rssi` + `signalQuality` (0-100, 03 §2) |
| Bağlanılabilir değil | `chklist.cpp` "connectable" flag işlemenin kaynağı | `bNetworkConnectable=false` + `wlanNotConnectableReason` (05 §6 Level-2) |
| Gizli ağ | lanui'da elle SSID girişi akışı (sihirbaz `lanwiz.cpp`) | `wifi_connect` elle SSID (05 §4.3) |
| 802.1X/WPA sekmesi | `eapolpage.cpp` + `saui.cpp` | XP Enterprise: kapsam dışı (§3.9) |
| Sıralama/gruplama | `util.cpp` (67 KB) — list sort yardımcıları | 05 §4.1 dedup + sıralama referansı |

---

## §9.2 Güvenlik Notları (kaynak ışığında)

| # | Not | Kanıt/kaynak |
|---|---|---|
| 1 | XP WZC'nin WEP'yi "ADD_WEP/REMOVE_WEP" OID'leriyle yönettiği görülüyor — **WEP üretime alınmaz**; yalnız miras AP'ye bağlanma fallback'i olarak düşünülebilir | deviceio.c L491/L511 |
| 2 | WZC RPC endpoint named pipe: yerel makine, ACL'li (`mscn_np`) — Uzak erişim varsayılan yok; neva-wlan XP bacağı da yalnız yerel WS 127.0.0.1 (05 §5) | wzc.idl endpoint + RPC kısıtı |
| 3 | wzcsvc `svchost -k netsvcs` içinde — servis öldürme diğer ağ servislerini çökertmez ama XP'de **WZC'yi durdurmak ağ panelini kilitler**; 05 §6 Level-1 mesajı "kablosuz servis durdurulmuş olabilir" ile hizalı | netwzc.inx |
| 4 | Sızıntı kaynağı üretime **kod taşımaz** (§1 yasal not) — yalnız davranış/veri modeli referansı | vault politikası |
| 5 | `psk` metni log'a yazılmaz (06 §6.d) — XP kaynak referansı: wzctool komut geçmişi (`cmdq.c` kuyruğu) da gizli veri saklar, aynı kural uygulanır | cmdq.c 3.2 KB |

---

## §9.3 Sözlük (bu doc jargonu)

| Terim | Anlam |
|---|---|
| WZC | Wireless Zero Configuration — XP'nin otomatik kablosuz yapılandırma servisi (wzcsvc) |
| NDISUIO | NDIS User-mode I/O — user-mode'dan sürücüye OID gönderen miniport/intermediate katman |
| OID | Object Identifier — NDIS sürücü sorgu/ayar kimliği (OID_802_11_*) |
| BSS | Basic Service Set — erişim noktası düzeyi kayıt (BSSID + SSID + sinyal) |
| EAPOL | Extensible Authentication Protocol over LAN — 802.1X taşıma |
| WZC context | Aktif yapılandırma bağlamı (INTF_ENTRY üzerinden sorgulanır) |
| DBLog | WZC olay günlüğü oturum API'si (wzcdbapi.c) |
| saui | Secure Auth UI — XP panelinin WPA arayüzü |
| lanui | LAN UI — XP ağ bağlantıları MMC/panel bileşeni (K014 connectivity doc'una atıf) |
| isomorfizm | WZC ↔ wlanapi fonksiyon eşlemesi (§3.8.3) |

---

| # | Madde | Durum |
|---|---|---|
| 1 | XP NDISUIO doğrudan yolu sürücü izniyle çalışır mı (WZC kapalıyken)? | `⚠️ VERIFICATION REQUIRED` — XP VM'de test |
| 2 | wlanapi.dll (SP2+) kendi kaynağı sızıntıda var mı (Win2K3 ağacı?)? | bu repo XPSP1 tek ağaç — `UNKNOWN` |
| 3 | Felaket yolu kapsamda mı (yalnız XP / XP+7)? | kullanıcı kararı |
| 4 | lanui.cpp'den durum metni/sınıflandırma çıkarımı (vault'a ikinci doc)? | talep gelirse `08-xp-panel-davranis-sozlesmesi` |
| 5 | XP hedef onayı hâlâ açık (02 doc v141_xp kurulumu) | kullanıcı kararı |

---

## §10 Kanıt Dosyaları (disk yolları, salt analiz)

```
C:\Users\MARHAN\AppData\Local\Temp\opencode\nt5src\          (clone, commit daad8a087)
└─ Source\XPSP1\NT\
   ├─ net\layer2svc\zeroconf\{server,client,idl,cmdtool}\    (§2.2 servis/istemci/IDL/CLI)
   ├─ net\layer2svc\{eapol,wastore,wlstore,wlpolicy,wlsnp,monitor}\
   ├─ net\inc\wzcsapi.h                                       (31.7 KB — §3.4)
   ├─ net\config\netcfg\inf\netwzc.inx                        (§2.3 servis INF)
   ├─ net\config\shell\lanui\                                 (§3.6 XP paneli)
   ├─ net\published\inc\wzcsvc.w, wzcdlg.w
   └─ public\internal\net\inc\wzcsvc.h, wzcdlg.h (+ i386/ia64 .lib)
```

İlişkili vault: `01` (export matrisi) · `03` (feature gate) · `05` (çekirdek modül) ·
`06` (test planı — XP uç satırına NDISUIO negatif testi eklenebilir) ·
`architecture/…/neva-connect-wlan-dallanma-mimarisi` (L0-L4).

---

## §11 Kaynaklar

| # | Kaynak |
|---|---|
| 1 | tongzx/nt5src — https://github.com/tongzx/nt5src (README: leak atıfı, MEGA + 4chan) |
| 2 | xieby1 notları — https://xieby1.github.io/Kernel/Windows/compilation.html (sızıntı tarihi 2020-09-23, ~%70 kapsam, NOTREPACKED hash'leri) |
| 3 | rentry.co/wxp — XP/2003 leak rehberi (nt5src.7z MD5 94DEA413…, derleme notları) |
| 4 | JackeyLea/winxp — https://github.com/JackeyLea/winxp (XPSP1 277.863 dosya / 24.666 klasör sayımı — bizim checkout ile tutarlı) |
| 5 | Microsoft Learn — WlanOpenHandle (01/03 doc kaynakçısı; wlanapi = SP2 eki) |
| 6 | Yerel disk kanıtı — §7 yolları (bu oturum ölçüldü) |

---

---

## §12 Kapanış Tablosu (07 turunun çıktıları)

| Çıktı | Durum |
|---|---|
| nt5src klon + tam checkout (277.868 dosya) | ✅ `Temp\opencode\nt5src`, commit daad8a087 |
| XP SP1'de wlanapi YOK / WZC tam kaynak | ✅ §2 disk kanıtlı |
| WZC RPC sözleşmesi (UUID + pipe + metotlar) | ✅ §3.3 |
| NDISUIO/OID alt katman kanıtı (felaket yolu) | ✅ §3.5 |
| XP panel UI kod envanteri (davranış referansı) | ✅ §3.6, §9.1 |
| WZC ↔ wlanapi izomorfizm (XP bacağının atası) | ✅ §3.8.3 |
| MSDN XP beyaz liste ↔ 36 export farkının açıklaması | ✅ §6 (önceki §9) |
| Kod | ❌ YOK — kullanıcı "kodlama daha yok" direktifi korundu |
| Karar bekleyen | XP NDISUIO felaket yolu kapsamı · XP hedef onayı · 08 panel doc talebi (§9) |

---

*07 doc · 2026-10-15 · nt5src daad8a087 disk analizi · KOD YOK — XP bacağı kararları (§9 maddeleri 1/3/4/5) kullanıcı onayı bekliyor.*
