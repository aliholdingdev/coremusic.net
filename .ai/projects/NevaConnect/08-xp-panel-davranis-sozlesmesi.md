---
title: "Neva Connect — XP Kablosuz Paneli Davranış Sözleşmesi (lanui kaynağından çıkarım)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "nt5src XP SP1 net/config/shell/lanui disk analizi + 07 doc (WZC stack)"
updated: 2026-10-15
---

# Neva Connect — XP Kablosuz Paneli Davranış Sözleşmesi

> Bu doküman, XP SP1 kaynak ağacındaki `net/config/shell/lanui/` (kablosuz özellik
> sayfaları) ve `net/layer2svc/dialog/` (hızlı yapılandırma paneli) bileşenlerinin
> **gözlemlenebilir davranışını** sözleşmeye bağlar. Amaç: neva-wlan XP bacağının
> kullanıcı yüzeyi, bu panelin yerleşik davranışını bozmadan (veya kasıtlı olarak
> iyileştirerek) eşleştirebilsin.
>
> **KOD YOK:** Bu doküman kaynak kod üretmez, kaynak kod kopyalamaz. Yalnızca
> dosya/satır referanslı kanıt ve çıkarım taşır. Kaynak gövde alıntılanmaz;
> alıntılar 1–3 satırlık, satır numarası verilmiş kısa bölmelerdir (07 §1 yasal notu
> ile aynı kural).
>
> **Zero-Hallucination:** Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED`
> ile işaretlidir; bilinmeyen değer `UNKNOWN` yazılır. Uydurma string ID, enum,
> süre veya eşik yoktur.

## §1 Kapsam

**Kapsam içi:**

1. XP SP1 WZC (Wireless Zero Configuration) kablosuz panelinin gözlemlenebilir
   davranışı: durum sınıflandırması, liste davranışı, bağlanma akışı, hata/durum
   mesajları, 802.1X/WPA yüzeyi.
2. Bu davranışın neva-wlan WS 9742 JSON sözleşmesine (05 doc §5) eşlemesi.
3. Panelin gerçek kaynak evi ve dosya envanteri (disk ölçümü).

**Kapsam dışı:** Vista+ `wlanapi.dll` UI (`WLAN_PROFILE_GET_FLAG` vb.) — 01/03
doc'lar; WZC servisinin kendisi (`wzcsvc`) — 07 doc §4; sürücü/NDIS katmanı —
02 doc yasağı (L0).

**Görev varsayımlarının kaynakla düzeltmesi (dürüstlük kaydı):** Bu araştırmada üç
varsayım disk kanıtıyla **yanlış** çıktı; düzeltmeler §9.1'dedir:

| # | Görev varsayımı | Disk gerçeği |
|---|---|---|
| 1 | `chklist.cpp` = kablosuz durum sınıflandırması | **YANLIŞ** — INetCfg binding/checkbox listesi (§9.1.1) |
| 2 | `saui.cpp` = "secure auth / WPA" paneli | **YANLIŞ** — Shared Access (ICS) UI (§9.1.2) |
| 3 | Panel `lanui/` içinde | **KISMEN** — asıl "Connect to Wireless Network" paneli `net/layer2svc/dialog/` içinde (§9.1.3) |

## §2 Dosya Envanteri (disk ölçümü)

### §2.1 `net/config/shell/lanui/` — 37 dosya (tam sayım)

Satır sayıları bu araştırmada diskten ölçülmüştür (read offset tekniği — §10.3).
KB değerleri 07 §3.6 envanterinden alınmıştır; dosya başına yalnız bir ölçüme
sahip olunduğu belirtilir (S = bu turda satır ölçümü, K = 07 §3.6 KB değeri).

| # | Dosya | Satır (S) | KB (K) | Rol |
|---|---|---|---|---|
| 1 | `lanui.cpp` | 5074 | 202 | COM arayüz sığdırıcısı — tüm LAN UI nesneleri (§2.3) |
| 2 | `util.cpp` | 2251 | 106 | genel yardımcılar (buffer, metin, hata) — **sıralama rutini YOK** (§4.1) |
| 3 | `wzcui.cpp` | 2077 | 92 | **`CWirelessZeroConfPage`** — Wireless Networks özellik sayfası çekirdeği |
| 4 | `lanuiobj.cpp` | 991 | 49 | LAN UI nesne/prop-page kayıt altyapısı |
| 5 | `chklist.cpp` | 828 | 40 | INetCfg binding checkbox listesi — **kablosuz durum DEĞİL** (§9.1.1) |
| 6 | `eapolpage.cpp` | 762 | 38 | **`CWLANAuthenticationPage`** — 802.1X/EAPOL sayfası (§7) |
| 7 | `wzcpage.cpp` | 598 | 29 | **`CWZCPropertyPage`** — "Wireless Network Properties" WEP sayfası (§5.3) |
| 8 | `lanui.rc` | 597 | 14 | diyalog/string kaynakları (§4.4, §6.2) |
| 9 | `lanui.h` | 523 | 21 | genel arayüz/sınıf bildirimleri |
| 10 | `sauiobj.cpp` | 400 | 18 | Shared Access UI nesne altyapısı |
| 11 | `eapolui.cpp` | 396 | 18 | EAPOL UI yardımcıları (EAPMSCHAPv2 vs.) |
| 12 | `saui.cpp` | 366 | 16 | `CSharedAccessPage` — **ICS UI, güvenlik paneli DEĞİL** (§9.1.2) |
| 13 | `lanwiz.cpp` | 366 | 17 | ağ bileşeni sihirbazı (LAN) |
| 14 | `devdatatip.cpp` | 288 | 14 | cihaz veri tipi / tooltip |
| 15 | `wzcui.h` | 193 | — | `CWirelessZeroConfPage` + `CWZCConfig` bildirimi |
| 16 | `util.h` | 192 | — | yardımcı bildirimleri |
| 17 | `lanhelp.h` | 185 | — | yardım bağlama tanımları |
| 18 | `lanwizfn.cpp` | 186 | 9 | sihirbaz fonksiyonları |
| 19 | `chklist.h` | 172 | — | binding listesi bildirimi (L407-411: `BPOBJ_ENABLED/DISABLED/UNSET`, `NCF_FIXED_BINDING`) |
| 20 | `lanuiobj.h` | 162 | — | nesne altyapısı bildirimi |
| 21 | `advpage.cpp` | 132 | 6 | "Advanced" (Networks to access / fallback) sayfası (§4.5) |
| 22 | `wzcprops.cpp` | 129 | 6 | WZC property sheet koordinasyonu |
| 23 | `wzcpage.h` | 118 | — | WZC properties sayfası bildirimi |
| 24 | `lanwiz.h` | 115 | — | sihirbaz bildirimi |
| 25 | `sauiobj.h` | 113 | — | SA nesne bildirimi |
| 26 | `eapolpage.h` | 82 | — | 802.1X sayfası bildirimi |
| 27 | `saui.h` | 70 | — | CSharedAccessPage bildirimi |
| 28 | `resource.h` | 213 | 7 | ID tanımları (IDS_WZC_* 16100–16129 — §6.1) |
| 29 | `wzcprops.h` | 48 | — | props bildirimi |
| 30 | `eapolui.h` | 26 | — | EAPOL UI bildirimi |
| 31 | `sources` | 31 | 1 | BUILD makefile parçası |
| 32 | `lanui.rgs` | 18 | 1 | COM registry betiği |
| 33 | `saui.rgs` | 18 | 1 | COM registry betiği |
| 34 | `advpage.h` | 30 | — | advanced sayfası bildirimi |
| 35 | `conutil.rgs` | 14 | 1 | COM registry betiği |
| 36 | `makefile` | 6 | 1 | BUILD yönlendirici |
| 37 | `devdatatip.h` | 8 | — | cihaz veri tipi bildirimi |

### §2.2 `net/layer2svc/dialog/` — panelin gerçek evi (dosya adları + satır)

Bu dizin XP'nin gerçek "Connect to Wireless Network" diyalog panelini üretir
(`netshell.dll` çağırır — 07 §9.1). Satır sayıları bu turda ölçülmüştür:

| Dosya | Satır | Rol |
|---|---|---|
| `quickcfg.cpp` | 1432 | **`CWZCQuickCfg`** — panele ait görünüm/davranış çekirdeği (§4.3, §5.1) |
| `wzcdlg.cpp` | 212 | diyalog penceresi mesaj döngüsü |
| `wzccore.cpp` | 107 | WZC çekirdek yardımcıları |
| `wzcdata.cpp` | 130 | **`CWZCEntry`** — SSID buffer, `Match()`, `Weaker()` (§3.3) |
| `wzcdata.h` | 45 | **`WZC_DESCR_*` flag'leri + `WZCIMG_*` ikon enum'ları** (§3.1–3.2) |
| `wzcdlg.rc` | 149 | panel kaynakları — CAPTION "Connect to Wireless Network" (L73) |
| `resource.h` | 33 | `IDS_WZCDLG_FAILED 5001` … `IDS_CANTACCESSNET_ADHOC 5005` (§6.3) |

> Not: dizinde `eldefs.h`, `eapolcfg.*`, `eluiuser.c/h` gibi 802.1X EAP
> altyapı dosyaları da vardır; satır ölçümleri bu turda yapılmamıştır
> (`UNKNOWN`) — §9.5.

### §2.3 `net/config/shell/dll/` ve INF (tamamlayıcı kanıt)

| Dosya | Kanıt | Rol |
|---|---|---|
| `netshell.rc` | L602–607 | `IDS_802_11_LEVEL0..5` sinyal metinleri (§6.4) |
| `netshell.rc` | L264, L529 | "View Available Wireless Networks" menü metinleri |
| `folder/contray.rc` | L58, L78 | tray menüsü kablosuz girdileri |
| `folder/oncommand.cpp` | L450, L980, L993, L1014–1015 | `WZCDLG_FAILED 0x00010001`, `WZCDLG_DATA` — panel çağrısı veri aktarımı |
| `net/config/netcfg/inf/usa/netwzc.txt` | L1–4 | `WZCSVC_Desc` = "Wireless Zero Configuration" servis metni |

## §3 Durum Sınıfları

### §3.1 Tanımlayıcı flag seti (`wzcdata.h` L8–12, panel/çekirdek ortak)

| Flag | Değer | Anlam | Kaynak |
|---|---|---|---|
| `WZC_DESCR_PREFRD` | 0x1 | AP bu arayüzün tercih listesinde | `wzcdata.h` L8 |
| `WZC_DESCR_VISIBLE` | 0x2 | AP son taramada görünür (beacon/probe) | `wzcdata.h` L10 |
| `WZC_DESCR_ACTIVE` | 0x4 | AP ile şu an bağlı/etkin | `wzcdata.h` L12 |

> `WLAN_AVAILABLE_NETWORK`/`WLAN_INTF_OPCODE` sembolleri `lanui/` ve
> `layer2svc/dialog/` içinde **0 eşleşmededir** — XP SP1 kaynak ağacında
> `wlanapi.dll` UI yoktur (07 §2 ile tutarlı). XP'nin eşdeğer kavramı bu üç
> bayraktır.

### §3.2 Görsel durum sınıfları (`wzcdata.h` L15–22, ikon enum'ları)

Panel satır başına bir durum sınıfı atar; sınıf → ikon eşlemesi `wzcdata.cpp`
L108–117'de okunur (ACTIVE → `WZCIMG_INFRA_ACTIVE`/`WZCIMG_ADHOC_ACTIVE`;
beacon yayımlayan → AIRING; susturulmuş → SILENT). `wzcui.cpp` L121/126 aynı
kuralı özellik sayfasına uygular:

| Sınıf | Koşul (kanıt) | Kullanıcıya görünen |
|---|---|---|
| **Etkin bağlı** | `WZC_DESCR_ACTIVE` set (`wzcui.cpp` L121, L727, L730) | aktif ikon + "Connect" durumu |
| **Tercihli, bağlı değil** | `WZC_DESCR_PREFRD`, ACTIVE yok (`wzcui.cpp` L452, L612) | tercih listesinde gri ikon |
| **Görünür, tercih değil** | `WZC_DESCR_VISIBLE` | "diğer ağlar" havuzu (§4.2) |
| **Susturulmuş (silent)** | SSID gizli / beacon yok — AIRING/SILENT ayrımı (`wzcdata.cpp` L108–117) | SILENT ikonu, liste dibinde |
| **Ad-hoc** | `InfrastructureMode = Ndis802_11IBSS` | `WZCIMG_ADHOC_*` ikon ailesi |

### §3.3 Eşitlik ve öncelik (`wzcdata.cpp`)

| Rutin | Kural | Kanıt |
|---|---|---|
| `Match()` | InfraMode eşit + SSID uzunluğu eşit + SSID bayt eşit → aynı AP | `wzcdata.cpp` L36–53 |
| `Weaker()` | Privacy (WEP) ve AuthMode üzerinden öncelik karşılaştırması — korumalı ağ korumasızdan "daha güçlü" | `wzcdata.cpp` L58–70 |
| SSID buffer | 33 WCHAR (32 + NUL); `MultiByteToWideChar` SSID dönüştürmesi | `wzcdata.cpp` L78–93 |

## §4 Liste Davranışı

### §4.1 Sıralama kuralı (`wzcui.cpp` L438–510)

Kod yorumu kuralı tek cümlede verir: ağlar **InfrastructureMode azalan sırayla**
sıralanır — "This way the Infrastructure [networks are listed first]" (`wzcui.cpp`
L438–439, dosyadan alınan kısa alıntı). Sonrasında tercih (PREFRD) bayrağı sıralamayı
ikincil derecelendirir (L452, L612).

| # | Kural | Kanıt |
|---|---|---|
| 1 | Infrastructure (AP) ağları ad-hoc'tan önce | `wzcui.cpp` L438–439, L482–510 |
| 2 | Tercihli (`PREFRD`) ağlar kendi bloklarında üstte | `wzcui.cpp` L452, L553–559 |
| 3 | **Sinyal gücüne göre sıralama kanıtı YOK** — `util.cpp` içinde Sort/Compare/qsort **0 eşleşme** | §9.4 |

### §4.2 Liste döküm sırası (`wzcui.cpp` L593–655)

Panel listeyi dört grupta döker (bu sırayla):

| Sıra | Grup | Kaynak |
|---|---|---|
| 1 | VPI — Visible + Preferred + Infrastructure | `wzcui.cpp` L593–655 döngüsü |
| 2 | VI — Visible + Infrastructure (tercih değil = "diğer ağlar") | aynı |
| 3 | VPA — Visible + Preferred + Ad-hoc | aynı |
| 4 | VA — Visible + Ad-hoc | aynı |

> "Diğer ağlar" kavramı (modern panellerdeki "diğer ağlar" grubu) XP'de ayrı bir
> başlık değil; **PREFRD bayrağı taşımayan görünür ağların** gruptaki doğal
> yeridir.

### §4.3 Yenileme timer'ı (3,5 saniye)

| Bileşen | Değer | Kanıt |
|---|---|---|
| Panel (quickcfg) timeout | `RFSH_TIMEOUT = 3500` ms | `quickcfg.cpp` L8 |
| Panel timer kurulumu | `SetTimer(g_TimerID, RFSH_TIMEOUT)` | `quickcfg.cpp` L938 |
| Panel yorumu | "Tr is 3secs" (zeroconf\server\state.h'a atıfla) — **değer 3500'dir, yorum yuvarlanmış** | `quickcfg.cpp` L936–937 |
| Panel tek-döngü | `OnTimer` → tek refresh → `KillTimer` | `quickcfg.cpp` L985, L994 |
| Özellik sayfası timeout | `RFSH_TIMEOUT 3500` (aynı değer) | `wzcui.cpp` L146–147 |
| Özellik sayfası timer ID | `UINT g_TimerID = 371` | `wzcui.cpp` L147 |
| Özellik sayfası kurulum | `SetTimer` iki yerde | `wzcui.cpp` L1101, L1637 |
| Özellik saypası durdurma | `KillTimer` | `wzcui.cpp` L266, L1262 |
| Yenileme butonu | tıklanınca `WZCRefreshInterface(NULL, INTF_LIST_SCAN, ...)` + buton disable + timer başlat | `wzcui.cpp` L1611–1655 (L1621, L1637) |

### §4.4 SSID kuralları

| Kural | Kanıt |
|---|---|
| SSID en fazla **32 karakter** — edit kontrolü bunu kullanıcıya söyler ("SSID can't exceed 32 chars") | `wzcpage.cpp` L325 |
| SSID buffer 33 WCHAR (32 + NUL) | `wzcdata.cpp` L78–93 |
| **Null SSID'li AP'ler (gizli SSID) görünür listeden atılır** | `wzcui.cpp` L437–445 |
| Tercih listesinde aynı SSID+tip iki kez olamaz — `IDS_WZC_ERR_DUPSSID` mesajı | `lanui.rc` L555 (metin §6.2) |

### §4.5 Advanced diyalog davranışı (`lanui.rc` L324–337, `advpage.cpp`)

| Kontrol | Davranış |
|---|---|
| "Any available network (access point preferred)" | radio — varsayılan her ikisi de görünür |
| "Access point (infrastructure) networks only" | radio — ad-hoc listelenmez |
| "Computer-to-computer (ad hoc) networks only" | radio — AP listelenmez |
| "Automatically connect to non-preferred networks" | checkbox → fallback biti (`INTFCTL_FALLBACK`, `wzcui.cpp` L237) |

## §5 Bağlanma Akışı

### §5.1 Panel bağlanma akışı (`quickcfg.cpp` OnConnect L1057–1327)

| Adım | Davranış | Kanıt |
|---|---|---|
| 1 | Seçili AP infra/adhoc kısıtına uygun mu? Uygun değilse `IDS_CANTACCESSNET_INFRA`/`IDS_CANTACCESSNET_ADHOC` | `quickcfg.cpp` L1093–1109 |
| 2 | WEP gerekiyorsa key doğrulama (uzunluk/hex/index) | `quickcfg.cpp` L1115–1163 |
| 3 | Bağlanılacak AP tercih listesinde değilse **görünür listeden tercih listesine taşınır** | `quickcfg.cpp` L1170–1221 |
| 4 | 802.1X: privacy yoksa 802.1X zorla kapalı | `quickcfg.cpp` L1263–1276 |
| 5 | Tercih listesi diske: `SavePreferredConfigs` | `quickcfg.cpp` L1279–1286 |
| 6 | WZC'ye uygulama: `WZCSetInterface(NULL, INTF_PREFLIST, ...)` | `quickcfg.cpp` L1293–1297 |
| 7 | Kısmi kopya hatası (`ERROR_PARTIAL_COPY`) → `IDS_WZC_PARTIAL_APPLY` uyarısı | `quickcfg.cpp` L1299–1308 |

### §5.2 Özellik sayfası uygulama akışı (`wzcui.cpp`)

| Adım | Davranış | Kanıt |
|---|---|---|
| 1 | Sayfa açılışı: OID okuması `INTF_BSSIDLIST | INTF_PREFLIST | INTF_ALL_FLAGS` + `INTFCTL_OIDSSUPP` kontrolü (OID desteklemeyen sürücüde kontroller kilitlenir) | `wzcui.cpp` L913–940; L316 |
| 2 | WZC etkin checkbox varsayılanları: `INTFCTL_ENABLED | INTFCTL_FALLBACK | Ndis802_11AutoUnknown` | `wzcui.cpp` L237 |
| 3 | Checkbox toggle: `OnChkWZCEnable` — `INTFCTL_ENABLED` biti set/clear | `wzcui.cpp` L1404–1416 |
| 4 | Apply: yalnız `bDirty` ise `WZCSetInterface(NULL, INTF_ALL_FLAGS | INTF_PREFLIST, ...)` | `wzcui.cpp` L1137–1212 (L1184) |
| 5 | Kısmi uygulama → `IDS_WZC_PARTIAL_APPLY` (metin §9.5 — kaynakta bulunamadı) | `wzcui.cpp` L1197 |

### §5.3 WEP şifre penceresi vs 802.1X (kullanıcı yüzeyi)

| Senaryo | XP davranışı | Kanıt |
|---|---|---|
| Açık ağ (privacy yok) | WEP alanı pasif; "The key is provided for me automatically" checkbox'ı devrede | `lanui.rc` L268–269; `quickcfg.cpp` L1379–1425 |
| WEP-128 açık auth | "Network key" edit + Key format (Hex/ASCII) + Key length + Key index | `lanui.rc` L257–267 |
| WEP-128 shared auth | aynı pencere + "Network Authentication (Shared mode)" checkbox | `lanui.rc` L255–256 |
| Ad-hoc seçili | `IDS_CANTACCESSNET_ADHOC` ile reddedilebilir (üst kısıt varsa) | `quickcfg.cpp` L1093–1109 |
| 802.1X / EAP | ayrı "Authentication" sayfası (`CWLANAuthenticationPage`) — §7 | `eapolpage.cpp` L18 |

> **WPA şifre penceresi XP SP1 KAYNAK AĞACINDA YOKTUR:** `WPA`, `TKIP`, `AES`,
> `RSNA` aramaları hem `lanui/` hem `layer2svc/dialog/` içinde **0 gerçek
> eşleşme** vermiştir (yalnızca `WPARAM`/ikon adı gibi alt dizgi gürültüsü).
> XP SP1 WZC'si WEP-only bir UI sunar (bkz. 07 §9.7 — bu bulgu maddeyi güçlendirir).


### §4.6 Panel kontrol ve etkinlik (enable) kuralları

`lanui.rc` IDD_LAN_WZEROCONF (L292–322) panelindeki kontroller ve
`wzcui.cpp`'deki etkinlik mantığı:

| Kontrol | Başlangıç | Etkinlik kuralı | Kanıt |
|---|---|---|---|
| "Use Windows to configure my wireless network settings" (`IDC_WZC_CHK_EnableWZC`) | WZC servisi çalışıyorsa işaretli | her zaman etkin (servis anahtarı) | `lanui.rc` L297–299 |
| Available networks listesi (`IDC_WZC_LVW_BSSIDList`, `LVS_REPORT | LVS_SINGLESEL`) | WS_DISABLED | WZC etkin + OID destekli | `lanui.rc` L302–305; `wzcui.cpp` L880 |
| "Configure" / "Refresh" butonları | WS_DISABLED | liste dolduysa | `lanui.rc` L306–307 |
| Preferred networks listesi (`IDC_WZC_LVW_StSSIDList`) | WS_DISABLED | WZC etkin | `lanui.rc` L310–313 |
| "Move up"/"Move down"/"Add"/"Remove"/"Properties" | WS_DISABLED | tercih listesinde seçim varsa | `lanui.rc` L314–318 |
| "Advanced" | her zaman etkin | — | `lanui.rc` L321 |

Etkinlik mantığı (kanıt satırları):

| Kural | Kanıt |
|---|---|
| TÜM kontroller: `bEnabled = (m_dwCtlFlags & INTFCTL_ENABLED)` — WZC kapalıysa panel salt-okunur | `wzcui.cpp` L852 |
| Liste + Refresh: ayrıca `m_dwOIDFlags & INTF_BSSIDLIST` OID bayrağı aranır | `wzcui.cpp` L880 |
| OIDSSUPP olmayan sürücü: `dwErr == ERROR_SUCCESS && (m_IntfEntry.dwCtlFlags & INTFCTL_OIDSSUPP)` kontrolü başarısızsa WZC checkbox'ı devre dışı | `wzcui.cpp` L316 |
| "bEnableAll" iki yerde hesaplanır (apply öncesi ve çevrimdışı kontrol) | `wzcui.cpp` L1069–1073, L1303 |

## §6 Hata / Durum Mesajları

### §6.1 `lanui/resource.h` — WZC string grubu (L154–179)

| ID | No | ID | No |
|---|---|---|---|
| `IDS_WZC_AUTH_OPEN` | 16100 | `IDS_WZC_KLEN_40X` | 16112 |
| `IDS_WZC_AUTH_SHARED` | 16101 | `IDS_WZC_KLEN_104X` | 16113 |
| `IDS_WZC_AUTH_AUTO` | 16102 | `IDS_WZC_KLEN_40A` | 16114 |
| `IDS_WZC_KLEN_104A` | 16115 | `IDS_WZC_LEARNCMD` | 16129 |

(Grup `IDS_WZC_AUTH_OPEN` (16100) ile `IDS_WZC_LEARNCMD` (16129) arasında 30
ID'lik bir bloktur; `IDS_LANUI_*` grubu 16008–16047 arasındadır —
`lanui/resource.h` L154–179.)

### §6.2 Gerçek metinler (`lanui.rc` L552–580) ve neva-wlan eşlemesi

| IDS | Metin (dosyadan birebir) | neva-wlan WS 9742 karşılığı |
|---|---|---|
| `IDS_WZC_AUTH_OPEN` | "Open" | `security` alanı: WEP-Open iması — v1 `security:["WEP"]` |
| `IDS_WZC_AUTH_SHARED` | "Shared" | WEP-Shared — XP profili ayrı ayrılmaz, §9.6 |
| `IDS_WZC_AUTH_AUTO` | "AutoSwitch" | yok — v1 kapsam dışı |
| `IDS_WZC_PRIV_ABS` | "Absent" | `security` içinde koruma yok (açık ağ) |
| `IDS_WZC_PRIV_REQ` | "Required" | `security` içeriği dolu |
| `IDS_WZC_WEP_DISABLED` / `_ENABLED` | "Disabled" / "Enabled" | WEP bayrağının kullanıcı metni |
| `IDS_WZC_KLEN_40X` | "40 bits (10 digits)" | WEP-40 hex — `psk` uzunluk ön-kontrolüne (05 §6 #12) uyar |
| `IDS_WZC_KLEN_104X` | "104 bits (26 digits)" | WEP-104 hex |
| `IDS_WZC_KLEN_40A` | "40 bits (5 characters)" | WEP-40 ASCII |
| `IDS_WZC_KLEN_104A` | "104 bits (13 characters)" | WEP-104 ASCII |
| `IDS_WZC_KFMT_X` / `_A` | "Hexadecimal digits" / "ASCII characters" | psk kodlama biçimi |
| `IDS_WZC_KERR_SHORT` | "The length of the WEP key material you entered is %1. The required length is %2. ..." (L571) | `E_WLAN_PSK_INVALID` (05 §6 #12) — ama XP WEP uzunlukları 8–63 değil; **eşleme v1 dışı**, §9.6 |
| `IDS_WZC_KERR_LARGE` | (SHORT ile aynı şablon, L572) | `E_WLAN_PSK_INVALID` |
| `IDS_WZC_KERR_NONX` | "The character on position %1 ... is not a hexadecimal digit. ..." (L573) | yerel ön-kontrol (bağlantı başlamaz) |
| `IDS_WZC_KERR_IDX` | "The WEP key index you entered is outside the permitted range. ..." (L574) | XP-only alan — WS 9742'de karşılığı YOK |
| `IDS_WZC_INFRA_IBSS` / `_INFRA` | "computer-to-computer" / "access point" (L576–577) | `bssType`: "ad_hoc" / "infrastructure" |
| `IDS_WZC_ERR_DUPSSID` | "The %1 network '%2' is already configured. ..." (L555) | `wifi_connect`/profil ekleme tekrarı — v1'de profil zaten varsa WlanSetProfile idempotent |

### §6.3 Panel diyalog string'leri (`layer2svc/dialog/resource.h` L16–20 + `wzcdlg.rc`)

| ID | No | Metin/kullanım | Kanıt |
|---|---|---|---|
| `IDS_WZCDLG_FAILED` | 5001 | panel açılamadı → `netshell` hata yolu (`WZCDLG_FAILED 0x00010001`, `folder/oncommand.cpp` L450, L980, L993) | `resource.h` L16; `oncommand.cpp` L450 |
| `IDS_WZCERR_INVALID_WEPK` | 5002 | geçersiz WEP anahtarı | `resource.h` L17 |
| `IDS_WZCERR_CAPTION` | 5003 | hata penceresi başlığı | `resource.h` L18 |
| `IDS_CANTACCESSNET_INFRA` | 5004 | infra kısıtında ad-hoc seçimi reddi (§5.1 adım 1) | `resource.h` L19; `quickcfg.cpp` L1093–1109 |
| `IDS_CANTACCESSNET_ADHOC` | 5005 | ad-hoc kısıtında AP seçimi reddi | `resource.h` L20; `quickcfg.cpp` L1093–1109 |

Panel başlığı: `wzcdlg.rc` L73 `CAPTION "Connect to Wireless Network"`.


### §6.6 Tray ve klasör menü yüzeyi

| Yüzey | Metin/girdi | Kanıt |
|---|---|---|
| Network Connections klasörü menü | "View Available Wireless Networks" | `netshell.rc` L264, L529 |
| Sistem tepsisi (tray) menüsü | kablosuz girdileri (WZC panelini açar) | `folder/contray.rc` L58, L78 |
| Tray komut akışı | `WZCDLG_FAILED 0x00010001` bayrağı + `WZCDLG_DATA` yapısı panel başlatılırken aktarılır | `folder/oncommand.cpp` L450, L980, L993, L1014–1015 |

> neva-wlan eşlemesi: bu üç yüzeyin WS 9742 karşılığı **yoktur** — paneli
> açma eylemi istemciye (SPA) aittir; neva-wlan yalnız veri ve komut sağlar.

### §6.7 Bağlantı kesme (disconnect) davranışı

| XP | Kanıt | neva-wlan |
|---|---|---|
| Tercih listesindeki bir AP'den ayrılmak panelde "Connect"/"Disconnect" bağlam eylemiyle olur; tercih listesinden **çıkmak** ≠ **bağlantıyı kesmek** | `wzcui.cpp` L1273–1286 (`WZC_DESCR_ACTIVE` temizliği + tercih listesi yazımı ayrışır) | `wifi_disconnect` (tek komut, profil KALIR) |
| Tercih listesinden Remove: profil silinir + bağlantı varsa kopar | `wzcui.cpp` L1273–1281 | `wifi_profiles_delete` (bağlantı üzerindekisi koparıp koparmayacağı 05 doc'da `UNKNOWN`) |

### §6.4 Sinyal seviyesi metinleri (`netshell.rc` L602–607)

| ID | Metin | neva-wlan eşlemesi |
|---|---|---|
| `IDS_802_11_LEVEL0` | "No Signal" | `signalQuality ≈ 0` |
| `IDS_802_11_LEVEL1` | "Very Low" | düşük `signalQuality` |
| `IDS_802_11_LEVEL2` | "Low" | orta-düşük |
| `IDS_802_11_LEVEL3` | "Good" | orta |
| `IDS_802_11_LEVEL4` | "Very Good" | yüksek |
| `IDS_802_11_LEVEL5` | "Excellent" | `signalQuality ≈ 100` |

> **Eşikler `⚠️ VERIFICATION REQUIRED`dir:** 6 kademenin `signalQuality`
> (0–100) karşılık gelen sayısal sınırları kaynak ağaca bu araştırmada
> **bulunamamıştır**; uydurulmaz. neva-wlan v1'de kademeler yerine 0–100 alan
> taşınır (05 §5.3), kademe çevirimi istemciye bırakılır.
> Panel etiketi: "Signal Strength" (`netshell.rc` L362–363).

### §6.5 Genel uygulama hatası

| IDS | Metin | Eşleme |
|---|---|---|
| `IDS_LANUI_APPLYFAILED` | "An unexpected condition occurred. Not all of your requested changes in settings could be made." (`lanui.rc` L470) | `ok:false` genel zarf (05 §5.2) — `E_WLAN_*` kodu atanamazsa |


### §5.4 Uygulama sırası — WZCQueryInterface → düzenleme → WZCSetInterface

Her iki yüzey de aynı üçlü deseni kullanır (davranış sözleşmesinin çekirdeği):

| Adım | Panel (quickcfg) | Özellik sayfası (wzcui) |
|---|---|---|
| 1. OKU | `WZCQueryInterface` (mevcut BSSID list + tercih list + flag) | `WZCQueryInterface(NULL, INTF_BSSIDLIST | INTF_PREFLIST | INTF_ALL_FLAGS)` — `wzcui.cpp` L913–940 |
| 2. DÜZENLE | bellek içi tercih listesi (`SavePreferredConfigs` öncesi) | liste + kontrol düzenlemeleri (`bDirty` set) |
| 3. YAZ | `WZCSetInterface(NULL, INTF_PREFLIST, ...)` — `quickcfg.cpp` L1293–1297 | `WZCSetInterface(NULL, INTF_ALL_FLAGS | INTF_PREFLIST, ...)` — `wzcui.cpp` L1184 (yalnız `bDirty` ise) |

> **Kritik fark:** panel yalnız `INTF_PREFLIST` yazar (bağlanma = liste
> manipülasyonu); özellik sayfası `INTF_ALL_FLAGS` da yazar (kullanıcı
> checkbox/radio değiştirebilir). neva-wlan bu ikisini `wifi_connect`
> (liste manipülasyonu) ve profil yazımı (flags dahil) olarak ayırmalıdır (§8 #4–9).

## §7 802.1X / WPA

### §7.1 802.1X sayfası (`eapolpage.cpp`)

`CWLANAuthenticationPage` (`eapolpage.cpp` L18) XP'nin 802.1X/EAPOL
yapılandırma sayfasıdır:

| İşlev | Davranış | Kanıt |
|---|---|---|
| `OnEapSelection` | EAP türü seçimi (PEAP, EAP-TLS, MSCHAPv2 ...) | L433 |
| `OnEapPackages` | EAP paket (alt tip) yapılandırması | L522 |
| `OnApply` | 802.1X ayarlarını WZC'ye yazar | L642 |
| `RefreshControls` | durum → kontrol durumu | L739 |

### §7.2 802.1X erişilebilirlik kuralları

| Kural | Kanıt |
|---|---|
| Gizli/privacy olmayan ağda veya ad-hoc'ta 802.1X kontrolü **kilitli** (`EAPOL_CTL_LOCKED`) | `wzcpage.cpp` L218–243 |
| Kilit/bayrak durumu `WZCDLG_PROPS_ONEX_CHECK` + `EAPOL_ENABLED` üzerinden okunur | `wzcpage.cpp` L238–243 |
| IBSS (ad-hoc) seçiliyken 802.1X checkbox'ı **kapalı + kilitli** | `quickcfg.cpp` L719–747 |
| Tercih olmayan infra ağda checkbox default checked | `quickcfg.cpp` L719–747 |
| Privacy yoksa 802.1X **force disable** (key gerekmez → EAPOL anlamsız) | `quickcfg.cpp` L1263–1276 |

### §7.3 WPA durumu — bu araştırmanın en önemli negatif bulgusu

| Arama | Kapsam | Sonuç |
|---|---|---|
| `WPA`, `TKIP`, `AES`, `RSNA` | `net/layer2svc/dialog/` | **0 gerçek eşleşme** (yalnız `WPARAM` alt dizgisi — false positive) |
| `WPA`, `TKIP` | `net/config/shell/lanui/` | **0 gerçek eşleşme** |
| `WLAN_AVAILABLE_NETWORK` | her iki dizin | **0 eşleşme** (wlanapi yok — 07 §2) |

**Sonuç:** XP SP1 WZC UI **yalnızca WEP** sunar. WPA-PSK/WPA2-PSK profili ne
görselleştirilir ne de düzenlenebilir; XP SP1 üzerinde WPA ancak üçüncü parti
yardımcı programlarla (veya SP2+ WLAN AutoConfig ile — kapsam dışı) mümkündür.
07 §9.7 maddesi bu disk kanıtıyla **güçlendirilmiştir**.

## §8 neva-wlan XP UI Eşlemesi (WS 9742 — 05 doc §5)

XP paneli ile neva-wlan JSON sözleşmesi arasındaki eşleme. JSON alan adları 05
doc §5.3–§5.5'ten alınmıştır; "XP karşılığı" sütunu bu dokümanın §3–§7
kanıtlarına dayanır.

| # | XP panel davranışı (kanıt) | neva-wlan WS 9742 karşılığı | Not |
|---|---|---|---|
| 1 | Görünür ağlar listesi (§4.2 grupları) | `wifi_list` → `data.interfaces[].networks[]` (`ssid`, `bssid`, `signalQuality`, `bssType`, `security[]`) | Sıralama neva-wlan'da server'da **yapılmaz** — istemci sıralar (05 §5.3) |
| 2 | "Refresh" butonu → `WZCRefreshInterface(INTF_LIST_SCAN)` (`wzcui.cpp` L1621) | `wifi_scan` + sonrası `wifi_list` | XP'de bildirim yok; neva-wlan Vista+ `scan_complete` push (05 §5.4); XP'de 4 sn sabit bekleme |
| 3 | 3,5 sn tek-döngü timer (§4.3) | karşılığı **YOK** — neva-wlan status polling 2 sn boştayken (05 §7) | Bilinçli fark: panel timer'ı agresif refresh; neva-wlan event-driven |
| 4 | Tercihli ağlar listesi | `wifi_profiles_list` | Her tercihli AP = bir profil |
| 5 | "Move up"/"Move down" (`lanui.rc` L314–315) | `wifi_profiles_reorder` `{name, position}` | XP `INTF_PREFLIST` sırası = profil sırası |
| 6 | "Add..." (`lanui.rc` L316) — el ile SSID/key girişi | `wifi_connect` `{ssid, psk}` (profil otomatik yazılır) | XP ayrı Add diyalog; neva-wlan tek komut |
| 7 | "Remove" (`lanui.rc` L317) | `wifi_profiles_delete` `{name}` | — |
| 8 | "Properties" WEP penceresi (§5.3) | `wifi_connect` `{ssid, psk}` + `wifi_profiles_get` | WEP XP-only ayrıntı (key index) WS 9742'de YOK (§6.2) |
| 9 | Panel "Connect" (§5.1) | `wifi_connect` `{ssid}` (açık ağ) / `{ssid, psk}` | ACK < 100 ms asenkron (05 §7) |
| 10 | `WZC_DESCR_ACTIVE` (§3.1) | `network.connected: true` + `state: "connected"` | `state` enum 05 §5.5 |
| 11 | `WZC_DESCR_PREFRD` | profil varlığı (`wifi_profiles_list` içindeki `ssid`) | v1'de liste elemanında "preferred" bayrağı **YOK** — profil sorgulanır |
| 12 | `WZC_DESCR_VISIBLE` | elemanın `wifi_list` içinde olması | Tarama sonucu = görünürlük |
| 13 | Infra/adhoc ayrımı (`bssType`) | `bssType: "infrastructure" | "ad_hoc"` | Panel: IDS_WZC_INFRA_* (§6.2) |
| 14 | 6 kademeli sinyal (§6.4) | `signalQuality` 0–100 + `rssi` türetilmiş | Eşikler `⚠️ VERIFICATION REQUIRED` (§6.4 notu) |
| 15 | Hata mesajları IDS_* (§6) | `error: {code, level, message, tr}` (05 §5.2) | `IDS_WZCERR_INVALID_WEPK` → `E_WLAN_PSK_INVALID`; `IDS_CANTACCESSNET_*` → `E_WLAN_CONNECT_REFUSED` |
| 16 | "Use Windows to configure my wireless network settings" checkbox (`lanui.rc` L297) | karşılığı **YOK** — WZC servis anahtarı, WS 9742 kapsam dışı | `⚠️ VERIFICATION REQUIRED`: neva-wlan WZC'yi açıp kapamaz |
| 17 | Advanced: infra/adhoc kısıt + fallback (§4.5) | v1'de **YOK** — `wifi_connect` profil XML'ine taşınır (nonBroadcast/fallback v1.1) | 05 §9 #12 |
| 18 | `IDS_WZC_PARTIAL_APPLY` (§5.2 adım 5) | v1 eşlemesi `⚠️ VERIFICATION REQUIRED` — `WlanSetProfile` tek profil atomiktir, `ERROR_PARTIAL_COPY` yolu WLAN API'de yok | XP WZC'ye özgü toplu uygulama hatası |
| 19 | `IDS_WZC_ERR_DUPSSID` (§4.4) | profil zaten varsa idempotent `WlanSetProfile` (overwrite) — çakışma zarfı **YOK** | Davranış farkı: XP reddeder, neva-wlan ezer |
| 20 | `IDS_WZC_AUTH_*` (Open/Shared/AutoSwitch, §6.2) | v1 `security[]` sadeleştirilmiş: `["WEP"]`, `["WPA2-PSK"]` vb. — Open/Shared ayrımı taşınmaz | `⚠️ VERIFICATION REQUIRED`: WEP-Open/Shared ayrıştırması v1 şemasında yok (05 §5.3) |
| 21 | `IDS_WZC_LEARNCMD` yardım bağlantısı (`lanui.rc` L560, `hcp://...`) | karşılığı **YOK** | Yardım istemciye (SPA) ait |
| 22 | Panel "queue_overflow" durumu (kaynak: WZC bildirim yığını) | `wifi_event: queue_overflow` `{droppedEvents}` (05 §5.4) | neva-wlan kuyruğu 256 (05 §7) |

## §9 Açık Konular

### §9.1 Görev varsayımlarının disk düzeltmeleri

#### §9.1.1 `chklist.cpp` — kablosuz durum sınıflandırması DEĞİL

**Gerçek rol:** `INetCfg` binding yolu checkbox listesi. Kanıt:
`chklist.cpp` L407–411 — `BPOBJ_ENABLED`/`BPOBJ_DISABLED`/`BPOBJ_UNSET`
durumları ve `NCF_FIXED_BINDING` bayrağı; `SELS_*` seçim işaretleri. Dosya,
bağlantı (binding) sırasını ve hangi bileşenin etkin olduğunu gösteren standart
 ağ bileşeni listesidir — WZC durum sınıflandırmasıyla **ilgisi yoktur**.
Kablosuz durum sınıflandırması §3'teki `WZC_DESCR_*` + ikon enum'larıdır
(`wzcdata.h` L8–22, `wzcdata.cpp` L108–117).

#### §9.1.2 `saui.cpp` — "secure auth / WPA" DEĞİL

**Gerçek rol:** Shared Access (Internet Connection Sharing) UI. Kanıt:
`saui.cpp` L8 başlık yorumu — "Shared Access connection object UI";
sınıf adı `CSharedAccessPage`. İlgili string'ler `lanui.rc` L510
(`IDS_SHAREDACCESSUI_LOCK_DESC` "Remote ICS Connection Properties"), L515–519
(`IDS_SHAREDACCESSUI_CONNECTING/CONNECTED/ACCESSDENIED`,
`IDS_SHAREDACCESS_SELECTADAPTER`). "sa" kısaltması "secure auth" değil
**"shared access"**tır.

#### §9.1.3 Panelin gerçek evi `net/layer2svc/dialog/`

XP'nin "Connect to Wireless Network" paneli (`wzcdlg.rc` L73 CAPTION)
`lanui/` içinde **üretilmez**; `net/layer2svc/dialog/` (quickcfg.cpp 1432
satır) panelin kendisidir. `lanui/` ise ağ bağlantıları klasöründeki
Wireless Networks özellik sayfasıdır. İkisi farklı iki UI yüzeyidir; bu doküman
her ikisini de kapsar (§2.1–§2.2).

### §9.2 WPA UI yokluğu (§7.3) — 07 §9.7 ile ilişki

07 doc §9.7 "XP SP1'de WPA UI yok" iddiası bu araştırmayla **disk kanıtıyla
doğrulanmıştır** (0 eşleşme, iki dizin). Madde kapatılabilir; artakalan soru
"XP SP3'te WPA var mı" (SP2+ WLAN AutoConfig) — kapsam dışı, §9.7.

### §9.3 Sinyale göre sıralama — kanıt yok

Görev varsayımı "sinyale göre sıralama" idi. `util.cpp` (2251 satır) içinde
Sort/Compare/qsort **0 eşleşme**; `wzcui.cpp` sıralaması yalnız
InfrastructureMode + PREFRD üzerinden (§4.1). **Sonuç: XP SP1 paneli sinyal
gücüne göre sıralamaz** — "kanıt yok" olarak kaydedilir; uydurma eşik/sıralama
yazılır.

### §9.4 `IDS_WZC_PARTIAL_APPLY` metni bulunamadı

Kullanım `wzcui.cpp` L1197'de (`quickcfg.cpp` L1299–1308'deki
`ERROR_PARTIAL_COPY` yoluyla ilişkili) kanıtlanmıştır; ancak string metni
`lanui.rc`/`netshell.rc` içinde bu araştırmada **bulunamamıştır** (muhtemelen
`netshell.rc`'in başka bir STRINGTABLE bloğu veya `wzcui`'a özgü kaynakta).
Metin **`⚠️ VERIFICATION REQUIRED`** olarak işaretli kalır; uydurulmaz.

### §9.5 Ölçülmemiş dosyalar

`layer2svc/dialog/` içindeki `eldefs.h`, `eluiuser.c/h`, `eapolcfg.*`
dosyaları satır ölçülmemiştir (`UNKNOWN`). EAP altyapısının derinlemesine
incelemesi gerekirse sonraki turda yapılır.

### §9.6 WEP-Open/Shared ayrımı ve WS 9742 şeması

neva-wlan v1 `security[]` alanı (05 §5.3) WEP-Open ve WEP-Shared'ı
ayırmaz; XP bu ayrımı (`IDS_WZC_AUTH_OPEN/SHARED`) yapar ve WZC profillerinde
`Authentication` alanında saklar. Ayrım şemaya taşınacaksa 05 doc revizyonu
gerekir (Açık Madde).

### §9.7 XP SP3 (SP2+) sınırı

Bu doküman **yalnızca XP SP1 kaynak ağacına** dayanır. SP2+ WLAN AutoConfig
(WPA-PSK desteği, `wlanui.dll`) kapsam dışıdır; XP hedef sürüm onayı 07 §9.2
açık maddesine bağlıdır.

### §9.8 Tek-döngü timer davranışı — panel vs özellik sayfası

Panel (`quickcfg.cpp` L985–994) timer'ı tek refresh sonrası **durdurur**
(`KillTimer`); özellik sayfası (`wzcui.cpp` L1637) refresh butonuyla tetiklenen
timer'ı sürdürür. neva-wlan tasarımında bu davranışın karşılığı: `wifi_scan`
**tek atımlı**dır (05 §7) — XP panelinin agresif 3,5 sn döngüsü bilinçli olarak
**kopyalanmaz**.

## §10 Kaynaklar

### §10.1 Kaynak kod ağacı (disk analizi — nt5src XP SP1)

Tüm satır numaraları şu kök altındaki dosyalardadır:
`C:\Users\MARHAN\AppData\Local\Temp\opencode\nt5src\Source\XPSP1\NT\net\`

| Dosya | Kullanıldığı bölümler |
|---|---|
| `config/shell/lanui/wzcui.cpp` (2077 satır) | §3.2, §4.1–4.3, §5.2, §8 |
| `config/shell/lanui/wzcpage.cpp` (598) | §4.4, §5.3, §7.2 |
| `config/shell/lanui/lanui.rc` (597) | §4.5, §5.3, §6.2 |
| `config/shell/lanui/resource.h` (213) | §6.1 |
| `config/shell/lanui/eapolpage.cpp` (762) | §7.1 |
| `config/shell/lanui/chklist.cpp` (828) | §9.1.1 |
| `config/shell/lanui/saui.cpp` (366) | §9.1.2 |
| `config/shell/lanui/util.cpp` (2251) | §9.3 (negatif kanıt) |
| `config/shell/lanui/advpage.cpp` (132) | §4.5 |
| `config/shell/dll/netshell.rc` | §6.4, §6.2 (sinyal) |
| `config/shell/folder/oncommand.cpp` | §2.3, §6.3 |
| `config/netcfg/inf/usa/netwzc.txt` | §2.3 |
| `layer2svc/dialog/quickcfg.cpp` (1432) | §4.3, §5.1, §7.2 |
| `layer2svc/dialog/wzcdata.h` (45) | §3.1–3.2 |
| `layer2svc/dialog/wzcdata.cpp` (130) | §3.3, §4.4 |
| `layer2svc/dialog/wzcdlg.rc` (149) | §6.3 |
| `layer2svc/dialog/resource.h` (33) | §6.3 |

### §10.2 Vault dokümanları

| Doküman | Kullanım |
|---|---|
| `07-xp-wzc-stack-kaynak-arastirmasi.md` (500 satır) | §2 KB envanteri, §2.2 panel evi, §7.3/§9.2 WPA, yasal not |
| `05-neva-wlan-cekirdek-modul-tasarimi.md` §5–§7 | WS 9742 JSON alan adları, hata seviyeleri, performans (§8) |
| `03-wlan-api-ozellik-kisitlama-tablosu.md` | XP beyaz liste bağlamı (atıf) |

### §10.3 Ölçüm yöntemi ve sınırlar

| Konu | Açıklama |
|---|---|
| Satır ölçümü | `read` tool'a büyük offset verilerek dosyanın satır sayısı okundu ("out of range for this file (N lines)") |
| Bayt boyutu | filesystem MCP yalnız repo köküne izinli olduğundan bayt ölçümü yapılamadı; KB değerleri 07 §3.6'dan alınmıştır (§2.1 K sütunu) |
| Kod alıntısı | Yasal kısıt (07 §1): kaynak gövde kopyalanmaz; yalnız 1–3 satırlık, satır numaralı kısa alıntı |
| Tarama | `grep` + `read` ile `net/config/shell/lanui/` (37 dosya) + `net/layer2svc/dialog/` (14 dosya) + `netshell.rc` + `folder/` + INF |

---

**Authority:** 07 doc §9.4 talebi · disk kanıtı nt5src XP SP1
**Mode:** Zero-Hallucination — `⚠️ VERIFICATION REQUIRED` işaretleri korunur
**Sonraki adım:** 09 doc'ta XP profil XML → WZC tercih listesi geri dönüşümü (bu doküman §5.1 adım 3–6 üzerine inşa edilir)