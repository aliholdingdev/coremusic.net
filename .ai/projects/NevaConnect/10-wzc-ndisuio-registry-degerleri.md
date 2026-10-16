---
title: "Neva Connect — WZC / EAPOL / NDISUIO Registry Değerleri (XP kaynağı nt5src + yerel Win10/11 doğrulaması)"
type: doc
category: connect-network
version: 1.2.0
status: plan
authority: "nt5src XP SP1 net/layer2svc + net/ndis/uio disk analizi + yerel HKLM registry okuması (2026-10-15)"
updated: 2026-10-15
---

# Neva Connect — WZC / EAPOL / NDISUIO Registry Değerleri

> **Amaç:** XP kablosuz yığınına (WZC + EAPOL + NDISUIO) ait registry anahtar ve
> değerlerini disk kaynağından (nt5src) çıkarıp, modern Windows (10/11) karşılıklarını
> yerel registry okuyarak doğrulamak. neva-wlan XP felaket yolu (09 doc) ve WZC oturum
> açma davranışının (07/08 doc) registry boyutunu tamamlar.
>
> **Zero-hallucination:** Aşağıdaki her XP anahtarı/değeri nt5src kaynağındaki
> dosya:satır kanıtıyla verilmiştir. Doğrulanamayan satırlar `⚠️ VERIFICATION
> REQUIRED` ile işaretlidir. Kod yazımı YOKTUR (01–09 ile aynı durum).
>
> **v1.1.0 (2026-10-15 ikinci tur):** WZC gerçek registry düzeni (`storage.h`
> sabitleri) ve EAPOL gerçek değer adları (`eapolutil.c` `cwsz*` tanımları)
> bulunarak §6 açık madde 1 ve 2 **KAPANDI**; §2.2/§2.3/§3.2/§4.2 yeni kanıtlarla
> yazıldı; §5'e modern `WlanSvc` LayoutVersion izomorfizmi eklendi.

---

#### §5.1.1 `ndisuio.inx` tam servis kaydı değerleri (v1.2.0 — madde 3 KAPANDI)

| Değer | XP (`net\ndis\uio\inf\ndisuio.inx`) |
|---|---|
| `ServiceType` | `1` (SERVICE_KERNEL_DRIVER) |
| `StartType` | `3` (SERVICE_MANUAL_START) |
| `ErrorControl` | `1` (SERVICE_ERROR_NORMAL) |
| `ServiceBinary` | `%12%\ndisuio.sys` |
| `LoadOrderGroup` | `NDIS` |
| `HKR,Ndi,Service` | `"Ndisuio"` |
| `UpperRange` | `noupper` |
| `LowerRange` | `ndis5,ndis4,ndis5_uio` |
| `Characteristics` | `0x28` (NCF_HIDDEN \| NCF_NOT_USER_REMOVABLE) |

> Yerel Win10/11 teyidi (§5.2): `Services\ndisuio` `Start=3` `Type=1` — **aynı** değerler.

## §1 Kapsam & İlişki

| Bu doc | Kapsam dışı |
|---|---|
| XP WZC servis registry ağacı · WZC profil saklama düzeni · EAPOL parametreleri · politika store anahtarları · NDISUIO sürücü kaydı · yerel Win10/11 karşılaştırma | Registry **yazma** (kurulum) kodu · INF üretimi · modern dot11/wificx registry (04 doc) · profil XML formatı (05 doc) |

**Bağlantı:** 07 (WZC stack kaynağı) · 08 (XP panel davranış) · 09 (NDISUIO felaket
yolu) · 03 (feature-gate) · 05 (wlabase çekirdek). Bu doc 09'daki `net stop wzcsvc`
kapısının ve 08'deki profil saklama davranışının registry karşılığını belgeler.

**Okuma sırası:** 07 → 08 → 10 (bu doc) → 09. Vault RAG indeksi: `@.ai/RAG.md` §3.

---

## §2 XP WZC Servis Registry Ağacı (nt5src kanıtlı)

### §2.1 Servis kaydı kökü

| Anahtar (XP) | Kaynak | Değer / not |
|---|---|---|
| `HKLM\SYSTEM\CurrentControlSet\Services\WZCSVC` | `wlpolicy/wirelessspd.c:159` (`gpszLocPolicyAgent = L"SYSTEM\\CurrentControlSet\\Services\\WZCSVC"`) | WZC servis kökü — **policy agent burayı gösterir** |
| Servis kısa adı `WZCSVC` | `eapol/service/eapolutil.c` (`WZCSVC_SERVICE_NAME L"WZCSVC"`) + `netwzc.inx` | 06 §4.2 X1 ile aynı kanıt |
| RPC pipe `\pipe\wzcsvc` | `zeroconf/client/rpcbind.c` (`L"wzcsvc" : L"\\pipe\\wzcsvc"`) | servis adı ile pipe adı aynı (küçük harf) |

> **Not (felaket yolu):** 09 doc `net stop wzcsvc` kullanır. Servis adı registry'de
> `WZCSVC`, RPC pipe `\pipe\wzcsvc` — `sc` kısa adı büyük/küçük harf duyarsızdır.

### §2.2 Servis kayıt değerleri (netwzc.inx — XP kurulum INF'i)

Kaynak: `net/layer2svc/.../netwzc.inx` (AddReg bölümleri):

| Satır | Kayıt | Değer |
|---|---|---|
| :48 | `StartType` | `2` = `SERVICE_AUTO_START` (XP wzcsvc **otomatik başlar**) |
| :53 | `ServiceBinary` | `%SystemRoot%\System32\svchost.exe -k netsvcs` |
| :59 | `HKR, Parameters, ServiceDll` | `0x00020000` (REG_EXPAND_SZ) → `%SystemRoot%\System32\wzcsvc.dll` |
| :60 | `HKR, Parameters, ServiceMain` | `0x00000000` (REG_SZ) → `WZCSvcMain` |
| :61-62 | Eventlog `Application\WZCSVC` | `EventMessageFile` = `wzcsvc.dll` · `TypesSupported` = `7` — **`;` ile yorum satırı (INF'te pasif)** |
| :64-68 | `Software\Microsoft\Tracing\WZCTrace` | 5 tracing değeri — **hepsi `;` ile yorum satırı (varsayılan pasif)** |

> **Yorum (K2 türü kanıt):** EventLog/Tracing kayıtları INF'te **yorumlu** olduğundan
> XP kurulumunda yazılmayabilir; EAPOL'ün aksine (`eapol.inx:51-59` aktif satırlar).
> Bu ayrım 06 §7.3 teşhis adımları için önemlidir: EAPOL tracing `reg query` ile
> görünür, WZCTrace görünmeyebilir → **modern teyitli (v1.2.0):** yerel `HKLM\Software\Microsoft\Tracing` **VAR**, altında `WZCTrace`/`wlan*` YOK. XP VM teyidi hâlâ ⚠️.
> §6 açık madde 4).

**Modern karşılık (yerel okuma, §5):** `WlanSvc` `svchost -k LocalSystemNetworkRestricted -p`
(farklı grup!) — XP `netsvcs` grubundan ayrışır.

### §2.3 WZC servis parametre/profil saklama düzeni (ANA BULGU)

Kaynak: `zeroconf/server/storage.h` (tam dosya okundu) + `zeroconf/server/storage.c`:

```
storage.h:21   #define WZCREGK_ABS_PARAMS  L"Software\\Microsoft\\WZCSVC\\Parameters"
storage.h:22   #define WZCREGK_REL_INTF    L"Interfaces"
storage.c:57   wsprintf(pKeyName, L"%s\\%s\\%s", WZCREGK_ABS_PARAMS, WZCREGK_REL_INTF, pIntfContext->wszGuid);
storage.c:59   wsprintf(pKeyName, L"%s\\%s",     WZCREGK_ABS_PARAMS, WZCREGK_REL_INTF);
storage.c:450  wsprintf(pKeyName, L"%s\\%s\\%s", WZCREGK_ABS_PARAMS, WZCREGK_REL_INTF, pIntfContext->wszGuid);
```

**Tam düğüm ağacı (kanıtlı):**

| Anahtar (XP) | Kaynak | İçerik |
|---|---|---|
| `HKLM\Software\Microsoft\WZCSVC\Parameters` | `storage.h:21` (`WZCREGK_ABS_PARAMS`) | WZC kök parametre |
| `...\Parameters\Interfaces` | `storage.h:22` + `storage.c:59,452` | Arayüz listesi |
| `...\Parameters\Interfaces\{GUID}` | `storage.c:57,450` (`wszGuid` ile) | **Arayüz başına profil/reglament** |

**Değer adları (storage.h:24-32, tür yorumları kaynakta yazılı):**

| Değer adı | Sabit | Anahtar konumu | Tür (kaynak yorumu) | Anlamı |
|---|---|---|---|---|
| `LayoutVersion` | `WZCREGV_VERSION` | `Interfaces` (üst düğüm) | `REG_DWORD` | Registry düzeni sürümü |
| `ControlFlags` | `WZCREGV_CTLFLAGS` | `Interfaces\<GUID>` | `REG_DWORD` | Arayüz kontrol bayrakları |
| `ActiveSettings` | `WZCREGV_INTFSETTINGS` | `Interfaces\<GUID>` | `REG_BINARY` = `WZC_WLAN_CONFIG` | **Son aktif kablosuz yapılandırması (binary)** |
| `Static#----` | `WZCREGV_STSETTINGS` | `Interfaces\<GUID>` | `REG_BINARY` = `WZC_WLAN_CONFIG` | Statik yapılandırma #N (`-` = numara ayracı) |
| `ContextSettings` | `WZCREGV_CONTEXT` | `Interfaces\<GUID>` | ⚠️ tür kaynakta yazılmamış | Servise özgü bağlam parametreleri |

**Düzen sürümü sabitleri (storage.h:4-8):**

| Sabit | Değer | Not |
|---|---|---|
| `REG_LAYOUT_VERSION` | `0x00000004` | XP SP1 güncel düzen |
| `REG_LAYOUT_LEGACY_3/2/1` | `3/2/1` | Geriye uyumluluk okuma |
| `REG_STSET_MAX` | `0xffff` | En fazla 65535 statik profil |
| `REG_STSET_DELIM` | `L'-'` | `Static#` adında numara ayracı |

> **Kritik çıkarım (08 doc ile):** XP'de kablosuz profiller **binary blob**
> (`WZC_WLAN_CONFIG`) halinde registry'de saklanır — modern XML profil
> formatının (05 doc `WlanSetProfile`) **önündeki XP modeli**. `Static#----`
> kalıbı 6 hane `-` içerir (`REG_STSET_MAX` = 5 hane + 1 pekiştirme notu,
> storage.h yorumu).
>
> **Yükleme/kaydetme çağrıları:** `StoLoadIntfConfig` (`state.c:295`) ·
> `StoSaveIntfConfig` (`intflist.c:1118,1137,1164,2359` · `storage.c:363,383`) —
> profil değişimi registry'ye senkron yazılır (bloklayıcı, servis içi).

### §2.4 WZC parametre/politika okuma yolları (wlpolicy)

| Yol | Kaynak | Amaç |
|---|---|---|
| `RegQueryValueEx` | `wlpolicy/init.c:59` | Servis başlangıç parametresi okuma |
| `RegOpenKeyExW` ×3 | `wlpolicy/pastore.c:212,305,1348` | Politika store açma |
| `RegQueryValueExW` | `wlpolicy/pastore.c:1357` | Politika değeri okuma |

> **v1.1.0 KAPANDI (açık madde 1):** `pastore.c` çağrılarının hedefi §4.2'deki
> `GPTWirelessPolicy` / `Policy\Cache` anahtarlarıdır (`wirelessspd.c:157-158`
> global değişkenleri + `regstore.c:117` `pszWirelessPolicyDN` göreli yol yapısı).

---

## §3 XP EAPOL Registry Parametreleri (eapolutil.c + eapol.inx)

### §3.1 EAPOL anahtar yolları (gerçek `#define` sabitleri — v1.1.0)

Kaynak: `eapol/service/eapolutil.c:29-41` (satır birebir):

| Satır | Sabit | Registry yolu (tam) |
|---|---|---|
| :29 | `cwszEapKeyEapolUser` | `HKLM\Software\Microsoft\EAPOL\UserEapInfo` |
| :32 | `cwszEapKeyEapolConn` | `HKLM\Software\Microsoft\EAPOL\Parameters\Interfaces` |
| :35 | `cwszEapKeyEapolServiceParams` | `HKLM\Software\Microsoft\EAPOL\Parameters\General` |
| :38 | `cwszEAPOLGlobalParams` | `HKLM\Software\Microsoft\EAPOL\Parameters\General\Global` |
| :41 | `cwszEAPOLPolicyParams` | `HKLM\Software\Policies\Microsoft\Windows\Network Connections\8021X` |

> Aynı yollar `zeroconf/client/util.c:23,34` içinde de tekrarlanır:
> `REGKEY_Eap[] = L"System\\CurrentControlSet\\Services\\Rasman\\PPP\\EAP"`
> (EAP sağlayıcı DLL listesi — EAPOL'ün ortak zemini).

### §3.2 EAPOL gerçek değer adları (v1.1.0 — açık madde 2 KAPANDI)

Kaynak: `eapolutil.c:46-55` (`#define cwsz*` — registry value name olarak
`RegQueryValueEx` çağrılarına giden sabitler):

| Satır | Sabit | Gerçek registry adı | Anlamı |
|---|---|---|---|
| :46 | `cwszEapolEnabled` | **`EapolEnabled`** | 802.1X etkinleştirme (arayüz bazlı) |
| :47 | `cwszDefaultEAPType` | **`DefaultEAPType`** | Varsayılan EAP tipi (DWORD) |
| :48 | `cwszLastUsedSSID` | **`LastUsedSSID`** | Son kullanılan SSID |
| :49 | `cwszInterfaceList` | **`InterfaceList`** | `REG_MULTI_SZ` arayüz GUID listesi (General altında) |
| :50 | `cwszAuthPeriod` | **`authPeriod`** (küçük harf a!) | Auth periyodu (saniye) |
| :51 | `cwszHeldPeriod` | **`heldPeriod`** | Held period (saniye) |
| :52 | `cwszStartPeriod` | **`startPeriod`** | Start period (saniye) |
| :53 | `cwszMaxStart` | **`maxStart`** | Maks. start denemesi |
| :54 | `cwszSupplicantMode` | **`SupplicantMode`** | Supplicant modu (0/1/2) |
| :55 | `cwszAuthMode` | **`AuthMode`** | Auth modu (0=auto / 1=manual) |

> **v1.1.0 düzeltmesi:** v1.0.0'da bu adlar `⚠️ VERIFICATION REQUIRED` idi; adların
> üçü (`authPeriod`/`heldPeriod`/`startPeriod`) **küçük harf baş harfle** yazılır —
> EAPOL kodu bu yazımla okur/yazar (harf duyarsız olsa da kanıt bu şekildedir).
>
> **Global vs arayüz ayrımı:** Dönem değerleri (`authPeriod` … `AuthMode`)
> `Parameters\General\Global` altında; `EapolEnabled`/`DefaultEAPType`/
> `LastUsedSSID` arayüz düğümü (`Parameters\Interfaces\<GUID>`) altında okunur —
> `ElGet/SetInterfaceParams` ile `ElReadGlobalRegistryParams` ayrımı kaynaktaki
> fonksiyon sınırlarıdır (§3.3).

### §3.3 Fonksiyon ↔ registry eşlemesi

| Fonksiyon (eapolutil.c) | Hedef | Not |
|---|---|---|
| `ElReadGlobalRegistryParams` | `Parameters\General\Global` | 6 global değer (§3.2) |
| `ElGetInterfaceParams` / `ElSetInterfaceParams` | `Parameters\Interfaces\<GUID>` | Arayüz başına EAPOL + SSID |
| `ElUpdateRegistryInterfaceList` / `ElEnumAndUpdateRegistryInterfaceList` | `Parameters\General` → `InterfaceList` | Arayüz GUID listesi senkronu |
| `ElGet/SetCustomAuthData` | `Interfaces\<GUID>` (EAP tipi verisi) | Özel EAP auth verisi + SSID |
| `ElSet/Get/DeleteEapUserInfo` | `UserEapInfo` | Kullanıcı bilgisi saklama |
| `ElLoadEapDlls` (`eleap.c`) | `Services\Rasman\PPP\EAP` | EAP sağlayıcı DLL listesi |

### §3.4 EAPOL EventLog & Tracing (eapol.inx — AKTİF satırlar)

Kaynak: `eapol/inf/eapol.inx:51-59`:

| Anahtar | Değer | Tür |
|---|---|---|
| `HKLM\SYSTEM\CurrentControlSet\Services\Eventlog\Application\EAPOL\EventMessageFile` | `%SystemRoot%\System32\eapol.exe` | EXPAND_SZ |
| `...\EAPOL\TypesSupported` | `7` | DWORD |
| `HKLM\Software\Microsoft\Tracing\EAPOL\ConsoleTracingMask` | `0xffff0000` | DWORD |
| `...\Tracing\EAPOL\EnableConsoleTracing` | `0x00000000` | DWORD (kapalı) |
| `...\Tracing\EAPOL\FileTracingMask` | `0xffff0000` | DWORD |
| `...\Tracing\EAPOL\EnableFileTracing` | `0x00000001` | DWORD (**açık**) |
| `...\Tracing\EAPOL\FileDirectory` | `%SystemRoot%\Tracing` | EXPAND_SZ |

> **Karşıtlık:** §2.2'de WZCTrace kayıtları `;` ile **yorumlu** (pasif), EAPOL
> tracing satırları **aktif** — XP'de EAPOL tracing'i açıktır, WZC tracing'i değil.

---

## §4 WZC Politika Store (Group Policy) Anahtarları

### §4.1 Anahtar listesi (gerçek `#define`/global sabitler)

| Anahtar (tam yol) | Kaynak | Amaç |
|---|---|---|
| `HKLM\SOFTWARE\Policies\Microsoft\Windows\Wireless\GPTWirelessPolicy` | `wlpolicy/wirelessspd.c:157` (`gpszWirelessDSPolicyKey`) | **Kablosuz GPT politikası** (domain/GPO) |
| `HKLM\SOFTWARE\Policies\Microsoft\Windows\Wireless\Policy\Cache` | `wirelessspd.c:158` (`gpszWirelessCachePolicyKey`) + `wastore/persist.c:20` (`gpszWirelessCacheKey`) | Politika **cache** (yeniden okumayı azaltır) |
| `HKLM\SOFTWARE\Policies\Microsoft\Windows\WiFi\GPTWiFiPolicy` | `wlstore/connui.c:22` (`gpszWirelessDSPolicyKey` — **farklı sabit, `WiFi` dalı**) | `wlstore.dll` (bağlantı arayüzü) politika yolu |
| `HKLM\SYSTEM\CurrentControlSet\Services\WZCSVC` | `wirelessspd.c:159` (`gpszLocPolicyAgent`) | Yerel politika ajanı (servis kökü) |
| `HKLM\Software\Policies\Microsoft\Windows\Network Connections\8021X` | `eapolutil.c:41` | 802.1X politikası (EAPOL) |

> **İki ayrı GPT dalı:** `Wireless\GPTWirelessPolicy` (wlpolicy/wastore — servis
> tarafı) ve `WiFi\GPTWiFiPolicy` (wlstore — UI/connui tarafı). Aynı politika
> ağacının **iki okuyucusu**; XP VM'de `reg query /s` ile teyit §6 açık madde 5.

### §4.2 `pastore.c` üç `RegOpenKeyExW` çağrısının eşlemesi (açık madde 1 kapanışı)

| Çağrı | Göreli yapı | Hedef |
|---|---|---|
| `pastore.c:212` | `pszWirelessPolicyName` (UUID'li politika adı) | `...Wireless\GPTWirelessPolicy\<ad>` |
| `pastore.c:305` | `*ppszDirectoryPolicyDN` (dizin DN — AD) | AD `GPTWirelessPolicy` karşılığı (LDAP) |
| `pastore.c:1348/1357` | `*pdwIncarnationNumber` okuma | Cache inkarnasyon numarası (`Policy\Cache`) |
| `regstore.c:13` | kayıt gövdesi (`persist.c:91-238` `RegCreateKeyExW` ×2 + `RegSetValueExW` ×8) | Cache yazma (8 değer) |

> `persist.c:91-238` kanıtı: politika cache'i **registry'ye yazılır**
> (`RegCreateKeyExW` ×2 · `RegSetValueExW` ×8). Değer adları tek tek okunmadı —
> **KAPANDI (v1.2.0):** 8 `RegSetValueExW` (L145·157·177·190·203·215·227·238) = `ClassName`(msieee80211-Policy)·`description`×2·`name`·`WirelessName`·`WirelessID`·`WirelessDataType`·`WirelessData`·`whenChanged` → §4.3 tam tablo.

---

#### §4.3 `wastore\persist.c` politika cache 8 değer adı (v1.2.0 — madde 6 KAPANDI)

Cache anahtarı `gpszWirelessCacheKey` (`persist.c:20`) =
`SOFTWARE\Policies\Microsoft\Windows\Wireless\Policy\Cache` (→ §4.1 Cache anahtarı).

| # | Değer adı | Satır | Tip / not |
|---|---|---|---|
| 1 | `ClassName` | 147 | REG_SZ — içerik `msieee80211-Policy` (L150) |
| 2 | `description` | 159 | (ilk tanım — §4.2 policy DN eşi) |
| 3 | `description` | 171 | (ikinci tanım — §4.2 policy DN eşi) |
| 4 | `name` | 179 | |
| 5 | `WirelessName` | 192 | |
| 6 | `WirelessID` | 205 | |
| 7 | `WirelessDataType` | 217 | |
| 8 | `WirelessData` | 229 | |
| 9 | `whenChanged` | 240 | (§4.2 L1348 `dwWhenChanged` ile ilişkili) |

> **Not:** `RegSetValueExW` çağrıları L145·157·177·190·203·215·227·238 = **8 çağrı**;
> `description` iki kez geçtiği için **9 değer adı** (L159+L171 aynı `description`).

## §5 NDISUIO Sürücü Registry Kaydı

### §5.1 XP kaynağı (net/ndis/uio)

> **BULGU (07 §2.2 düzeltmesinin teyidi):** `net/ndis/uio/sys/ntdisp.c` içinde
> `Reg*`/`IoOpenDeviceRegistryKey` **çağrısı YOK** — sadece
> `pIrpSp->Parameters.DeviceIoControl.*` (IRP parametresi, registry değil).
> NDISUIO sürücüsü XP'de **kendi registry ağacını yazmaz**; yüklenirken INF kaydı
> oluşturur.

| Öğe | Kaynak | Değer |
|---|---|---|
| Servis kaydı | XP INF (ndisuio.inx — `net/ndis/uio/inf/` diskte mevcut, 07 §2.2 düzeltmesi) | `HKLM\SYSTEM\CurrentControlSet\Services\ndisuio` |
| Cihaz adı | 09 §2.5 / deviceio.c | `\\.\Ndisuio` (DosDevices) |

### §5.2 Yerel makine (Windows 10/11) — registry OKUMASI (2026-10-15)

`HKLM:\SYSTEM\CurrentControlSet\Services\ndisuio` (doğrulandı):

| Değer | Veri | Yorum |
|---|---|---|
| `DisplayName` | `NDIS Usermode I/O Protocol` | — |
| `ImagePath` | `system32\drivers\ndisuio.sys` | — |
| `Type` | `1` (KERNEL_DRIVER) | — |
| `Start` | `3` (MANUAL / START_ON_DEMAND) | 09 §6'da `net start ndisuio` tetiklenmesi tutarlı |
| `ErrorControl` | `1` (NORMAL) | — |
| `Group` | `NDIS` | — |
| `NdisMajorVersion` | `6` | **Modern NDIS 6** — 09 §9 [D3] uyum riski (OID_802_11 desteklenmeyebilir) |
| `NdisMinorVersion` | `30` | — |
| `DriverMajorVersion` | `0` | — |
| `DriverMinorVersion` | `0` | — |
| `Parameters` | (boş) | Sürücü-özel parametre yok |

> **Kritik not (felaket yolu uyumu):** Yerel NDISUIO **NDIS 6.30** — `OID_802_11_*`
> OID'leri NDIS 6'da **kaldırılmıştır** (09 §9 [D3] uyarısı). XP felaket yolu yalnız
> XP üzerinde çalışır; modern Windows'ta bu OID'ler `NDIS_STATUS_INVALID_OID` döner
> (09 §3.4 R7 degrade dalı). Registry kanıtı bu riski **teyit eder**.

---

## §6 Modern Windows (10/11) Kablosuz Registry Karşılaştırması

Yerel registry okuması (2026-10-15):

### §6.1 Servis matrisi

| Servis | DisplayName | Start | Type | DependOnService | Durum |
|---|---|---|---|---|---|
| `WlanSvc` (=WLAN AutoConfig) | `@wlansvc.dll,-257` | `2` (AUTO) | `16` (SHARE_PROCESS, svchost) | `nativewifip;RpcSs;Ndisuio;wcmsvc` | **XP wzcsvc karşılığı** |
| `ndisuio` | NDIS Usermode I/O Protocol | `3` (MANUAL) | `1` (KERNEL) | — | 09 felaket yolu |
| `wcmsvc` | `@wcmsvc.dll,-4097` | `2` (AUTO) | `16` | `RpcSs;NSI` | Windows Connection Manager |
| `dot3svc` (802.1X) | `@dot3svc.dll,-1102` | `3` (MANUAL) | `32` (SHARE + interactive) | `RpcSs;Ndisuio;Eaphost` | **XP EAPOL karşılığı** |
| `Eaphost` | `@eapsvc.dll,-1` | `3` (MANUAL) | `32` | `RPCSS;KeyIso` | EAP host |
| `wzcsvc` | — | — | — | — | **Modern Windows'ta YOK** (WlanSvc ile değiştirildi) |

> **XP → Modern eşleme (registry kanıtlı):**
> `WZCSVC` → `WlanSvc` · `EAPOL` (dot3svc/Eaphost) → `dot3svc` + `Eaphost` ·
> `ndisuio` → `ndisuio` (aynı ad, NDIS 6).

### §6.2 Anahtar-varlık matrisi (yerel `Test-Path` sonuçları)

| XP anahtarı | Modern karşılık | Yerel durum (2026-10-15) |
|---|---|---|
| `HKLM\SOFTWARE\Microsoft\WZCSVC` | `HKLM\SOFTWARE\Microsoft\WlanSvc` | XP anahtarı **YOK** · `WlanSvc` **MEVCUT** (değerler: `LayoutVersion`, `AllowAPMode`) |
| `...\WZCSVC\Parameters\Interfaces` | `WlanSvc\Parameters\Interfaces` | Yerelde **YOK** (profiller farklı depoda — §6.3) |
| `HKLM\SOFTWARE\Microsoft\EAPOL` | (dot3svc/Eaphost config) | **YOK** |
| `SOFTWARE\Policies\Microsoft\Windows\Wireless` | (aynı yol, GPO kullanılırsa) | **YOK** (GPO kablosuz politikası uygulanmamış) |
| `SOFTWARE\Policies\...\Network Connections\8021X` | (aynı yol) | **YOK** |
| `HKLM\SYSTEM\CurrentControlSet\Services\WZCSVC` | `Services\WlanSvc` | `wzcsvc` **YOK** · `wlansvc` MEVCUT |

### §6.3 LayoutVersion izomorfizmi (BULGU)

| Kanıt | XP | Modern |
|---|---|---|
| Sabit | `storage.h:4` `#define REG_LAYOUT_VERSION 0x00000004` | `HKLM\SOFTWARE\Microsoft\WlanSvc` → değer **`LayoutVersion`** (yerel okuma) |
| Konum | `WZCSVC\Parameters\Interfaces` üstünde (`WZCREGV_VERSION`) | `WlanSvc` kökünde |

> **Çıkarım:** `LayoutVersion` adı XP'den modern WlanSvc'ye **taşınmış** tek
> registry değer adıdır (yerel kanıt: `WlanSvc` altında yalnız 2 değer var:
> `LayoutVersion` + `AllowAPMode`). Değer içeriği/ sürüm numarası XP 4 mü modern
> farklı mı → **KAPANDI (v1.2.0, yerel `reg query`):** XP `#define REG_LAYOUT_VERSION 4` ≠ modern **`LayoutVersion` REG_DWORD `1`**; `AllowAPMode` = `{1,0,0,0}`. Ad taşınmış ama değer XP (4) ile **farklı** — modern sürüm etiketi, profil deposu `ProgramData` XML.
>
> **Neden `WlanSvc\Parameters\Interfaces` yerelde yok?** Modern Windows profilleri
> `ProgramData` XML deposunda tutar (05 doc `WlanSetProfile` yolu) — XP'nin binary
> registry blob modelinin (§2.3) yerini alır. Yerel kanıt: `Parameters` düğümü var
> (boş), `Interfaces` altı yok.

### §6.4 WlanSvc Parameters

| Anahtar | Değerler |
|---|---|
| `HKLM\SYSTEM\CurrentControlSet\Services\WlanSvc\Parameters` | `ServiceDll`, `ServiceDllUnloadOnStop`, `ServiceMain` |

> Servis-DLL modeli (svchost) — XP wzcsvc da svchost `-k netsvcs` idi (netwzc.inx:53),
> modern `WlanSvc` `svchost -k LocalSystemNetworkRestricted -p`.

> **v1.2.0 yerel ölçüm:** `Parameters` altında tek alt düğüm **`VWiFiSettings`** var
> (XP `Parameters\Interfaces` karşılığı değil). `Interfaces` yok — doğrulandı.

### §6.5 Servis kayıt detayları — tam yerel döküm (2026-10-15)

**`WlanSvc`** (tam değer listesi, `Get-ItemProperty` kanıtı):

| Değer | Veri | Not |
|---|---|---|
| `ImagePath` | `C:\Windows\system32\svchost.exe -k LocalSystemNetworkRestricted -p` | `-p` = izole süreç (Vista+) |
| `ObjectName` | `LocalSystem` | — |
| `Type` | `16` (SHARE_PROCESS) | svchost ortak süreç |
| `Start` | `2` (AUTO) | — |
| `Group` | (mevcut) | — |
| `DependOnService` | `nativewifip;RpcSs;Ndisuio;wcmsvc` | **`Ndisuio` bağımlılığı = felaket yolu teması (E5)** |
| `ServiceSidType` | (mevcut) | servis SID izolasyonu (Vista+) |
| `RequiredPrivileges` | (mevcut) | ayrıcalık listesi |
| `FailureActions` | (mevcut) | yeniden başlatma politikası |
| `Description` | (mevcut) | — |
| `Parameters\*` | `ServiceDll`=`wlansvc.dll` · `ServiceDllUnloadOnStop` · `ServiceMain` | servis-DLL |

**`dot3svc`** (XP EAPOL karşılığı, tam değer listesi):

| Değer | Veri | Not |
|---|---|---|
| `ImagePath` | (svchost) | — |
| `Type` | `32` (SHARE + `SERVICE_INTERACTIVE_PROCESS`) | XP EAPOL `eapol.exe` (ayrı süreç) idi → **svchost'a taşındı** |
| `Start` | `3` (MANUAL) | talep üzerine |
| `DependOnService` | `RpcSs;Ndisuio;Eaphost` | **`Ndisuio` + `Eaphost`** |

> **XP→Modern hizmet modeli farkı (registry kanıtlı):**
> XP `EAPOL` = Type 1/10 bağımsız süreç (`eapol.exe`, EventMessageFile kanıtı §3.4) ·
> Modern `dot3svc` = Type 32 svchost servis-DLL + `Eaphost` ayrı servis.
> `Ndisuio` bağımlılığı **iki dönemde de** korunur (§9 E5).

**`ndisuio`** — §5.2'de; **`wcmsvc`** — §6.1'de (bağımlılık `RpcSs;NSI`).

---

## §7 Açık Maddeler (⚠️ / UNKNOWN) — v1.2.0

| # | Madde | Durum | Eylem |
|---|---|---|---|
| 1 | WZC politika store tam registry yolu (`pastore.c` RegOpenKeyExW ×3) | ✅ **KAPANDI (v1.1.0)** — `wirelessspd.c:157-158` + `regstore.c:117` + §4.2 eşleme | XP VM `reg query /s` ile teyit (kalan: AD/LDAP dalı) |
| 2 | EAPOL Global registry değer adları (`csz*` → gerçek ad) | ✅ **KAPANDI (v1.1.0)** — `eapolutil.c:46-55` gerçek adlar (§3.2) | XP VM teyidi opsiyonel |
| 3 | XP NDISUIO servis kaydının tam değerleri (INF) | ✅ **KAPANDI (v1.2.0)** — `ndisuio.inx:41-53` [Ndisuio_Service_Inst]: `ServiceType=1` (SERVICE_KERNEL_DRIVER) · `StartType=3` (SERVICE_MANUAL_START) · `ErrorControl=1` (SERVICE_ERROR_NORMAL) · `ServiceBinary=%12%\ndisuio.sys` · `LoadOrderGroup=NDIS` · [Inst_Ndi]: `HKR,Ndi,Service,,"Ndisuio"` + `UpperRange=noupper` + `LowerRange=ndis5,ndis4,ndis5_uio` · `Characteristics=0x28` (NCF_HIDDEN\|NCF_NOT_USER_REMOVABLE). Yerel Win10/11 teyidi: `Services\ndisuio` `Start=3` `Type=1` — **aynı** (madde 3 yerel + XP kaynak kesişimi) | XP VM `reg query /s` artık opsiyonel |
| 4 | XP `WZCSVC` servis kaydı `Start` değeri | ✅ **kaynak kanıtlı** `netwzc.inx:48` `StartType=2` (INF'te) | XP VM `sc qc wzcsvc` teyidi |
| 5 | İki GPT dalı (`Wireless` vs `WiFi`) tek politika mı | ⚠️ VERIFICATION REQUIRED | XP VM: iki yolu `reg query /s` karşılaştır |
| 6 | `persist.c` politika cache 8 değer adı | ✅ **KAPANDI (v1.2.0)** — `wastore\persist.c:145-240` 8 `RegSetValueExW` (aşağıdaki §4.3): `ClassName` (`msieee80211-Policy` · REG_SZ) · `description` ×2 (L159, L171) · `name` (L179) · `WirelessName` · `WirelessID` · `WirelessDataType` · `WirelessData` · `whenChanged` — cache anahtarı `gpszWirelessCacheKey` (L20) `SOFTWARE\Policies\Microsoft\Windows\Wireless\Policy\Cache` | — |
| 7 | Modern `WlanSvc\LayoutVersion` değeri (XP 4 ile aynı mı) | ✅ **KAPANDI (v1.2.0)** — yerel `reg query`: **`LayoutVersion = 1` (REG_DWORD)** + `AllowAPMode = {1,0,0,0}`; XP `#define REG_LAYOUT_VERSION 4`. **FARKLI: XP 4 ≠ modern 1** (aşağıdaki §6.3) | — |
| 8 | `OID_802_11_*` modern NDIS 6'da kaldırılma kesin kanıtı | ⚠️ kısmen (registry NDIS 6.30 teyidi §5.2) | 09 §9 [D3] — MSDN/dot11 kaynağı |
| 9 | XP `WZCTrace` tracing kayıtları gerçekten yazılmıyor mu (`;` yorumu) | ⚠️ **modern teyitli**: yerel `HKLM\Software\Microsoft\Tracing` **VAR**, altında `WZCTrace`/`wlanapi`/`wlan*` YOK (boş Tracing anahtarı) — XP'de INF `;` yorumlu, modernde tracing anahtarı yok; **XP VM teyidi hâlâ ⚠️** | XP VM: `reg query HKLM\Software\Microsoft\Tracing\WZCTrace` |

> **Kapananlar:** v1.0.0’daki 5 açık maddenin 2’si (pastore yolu + EAPOL adları)
> v1.2.0’da 3 madde daha **INF/registry disk kanıtıyla kapandı**: (3) NDISUIO servis kaydı tam değerleri (`ndisuio.inx`) · (6) persist.c politika cache 8 değer adı · (7) modern `LayoutVersion = 1` (≠ XP 4).
> **Kalan 2 madde** XP VM/driver gerektirir: (5) iki GPT dalı (`Wireless` vs `WiFi`) · (8) `OID_802_11_*` NDIS 6 kaldırma kesin kaynağı (09 §9 [D3]).

---

## §8 XP VM Doğrulama Komutları (bu doc'un kapanışı için)

```bat
:: 1. WZC servis kaydı (açık madde 4)
reg query "HKLM\SYSTEM\CurrentControlSet\Services\WZCSVC" /s
sc qc wzcsvc

:: 2. WZC profil saklama düzeni (§2.3 — ana düğüm)
reg query "HKLM\Software\Microsoft\WZCSVC\Parameters\Interfaces" /s
:: beklenen değer adları: LayoutVersion, ControlFlags, ActiveSettings, Static#000001..., ContextSettings

:: 3. WZC politika store (açık madde 1 teyidi + madde 5)
reg query "HKLM\SOFTWARE\Policies\Microsoft\Windows\Wireless" /s
reg query "HKLM\SOFTWARE\Policies\Microsoft\Windows\WiFi" /s

:: 4. EAPOL Global (açık madde 2 teyidi)
reg query "HKLM\Software\Microsoft\EAPOL\Parameters\General\Global" /s
reg query "HKLM\Software\Microsoft\EAPOL\Parameters" /s
:: beklenen: authPeriod, heldPeriod, startPeriod, maxStart, SupplicantMode, AuthMode, InterfaceList

:: 5. NDISUIO (açık madde 3)
reg query "HKLM\SYSTEM\CurrentControlSet\Services\ndisuio" /s

:: 6. WZCTrace tracing (açık madde 9)
reg query "HKLM\Software\Microsoft\Tracing\WZCTrace" /s
```

> Bu komutların çıktısı §7 açık maddelerini kapatır; sonuçlar bu doc'a ek satır olarak
> işlenir. **Yazma işlemi YAPILMAZ** (salt okuma).

---

## §9 neva-wlan'a Etkisi (05/09 doc ile bağ)

| # | Etki | Hedef doc |
|---|---|---|
| E1 | XP profil saklama = binary blob (`WZC_WLAN_CONFIG`) — neva-wlan XP'de profil yazımı isterse `StoSaveIntfConfig` modeliyle (veya WZC API üzerinden) çalışır; XML `WlanSetProfile` XP SP3 wlanapi'de vardır ama depo farklıdır | 05 §2.1 |
| E2 | `net stop wzcsvc` felaket yolu registry'den bağımsız — ama servis `Start=2` (auto) olduğundan **yeniden başlatma** senaryosu 06 §7.3'te | 06 §7.3 · 09 §6 |
| E3 | EAPOL `authPeriod`/`maxStart` değerleri XP 802.1X davranışını zamanlar (08 §IDS_* mesajlarının arkasındaki zamanlayıcılar) | 08 §4 |
| E4 | `WlanSvc\LayoutVersion` modern izomorfizmi — feature-gate (03 doc) XP/10 ayrımında ek sinyal olabilir | 03 §2 |
| E5 | `dot3svc`/`Eaphost` bağımlılığı (`Ndisuio`!) — modern 802.1X de NDISUIO'ya dayanır; felaket yolu teması modernde de var | 09 §9 [D3] |

---

## §10 Kaynaklar

| # | Kaynak | Kanıt |
|---|---|---|
| [S1] | nt5src `net/layer2svc/wlpolicy/wirelessspd.c:157-159` | 3 politika anahtarı sabiti (`gpszWirelessDSPolicyKey`, `gpszWirelessCachePolicyKey`, `gpszLocPolicyAgent`) |
| [S2] | nt5src `net/layer2svc/zeroconf/server/storage.h:1-32` | WZC registry düzeni sabitleri (`WZCREGK_ABS_PARAMS` … `WZCREGV_CONTEXT`, `REG_LAYOUT_VERSION=4`) |
| [S3] | nt5src `net/layer2svc/zeroconf/server/storage.c:47-59,231-260,335,439-452` | Yol kurulumu (`wsprintf` ×3) + `Static#` ad kalıbı |
| [S4] | nt5src `net/layer2svc/zeroconf/server/{state.c:295, intflist.c:1118-2359}` | `StoLoadIntfConfig` / `StoSaveIntfConfig` çağrıları |
| [S5] | nt5src `net/layer2svc/eapol/service/eapolutil.c:29-55` | EAPOL yolları + gerçek değer adları (`cwsz*`) |
| [S6] | nt5src `net/layer2svc/eapol/inf/eapol.inx:51-59` | EAPOL EventLog + Tracing (aktif satırlar) |
| [S7] | nt5src `netwzc.inx:20-76` | WZCSVC servis kaydı (`StartType=2`, `ServiceDll`, `ServiceMain`, yorumlu tracing) |
| [S8] | nt5src `net/layer2svc/wastore/persist.c:20,91-238` | Cache anahtarı + `RegCreateKeyExW`×2/`RegSetValueExW`×8 |
| [S9] | nt5src `net/layer2svc/wlstore/connui.c:22` | `WiFi\GPTWiFiPolicy` ikinci GPT dalı |
| [S10] | nt5src `net/layer2svc/zeroconf/client/util.c:23,34` | `Rasman\PPP\EAP` + UserEapInfo tekrarı |
| [S11] | nt5src `net/ndis/uio/sys/ntdisp.c` | Registry erişimi YOK (IRP Parameters) |
| [S12] | Yerel `HKLM:\SYSTEM\CurrentControlSet\Services\{ndisuio,WlanSvc,wcmsvc,dot3svc,Eaphost}` | 2026-10-15 registry okuması |
| [S13] | Yerel `HKLM:\SOFTWARE\Microsoft\WlanSvc` (+ `Parameters`) | `LayoutVersion`/`AllowAPMode` + XP anahtarlarının yokluğu |
| [S14] | 07 doc (WZC stack) · 08 doc (XP panel) · 09 doc (NDISUIO felaket yolu) · 05 doc (çekirdek) | Bağlantı |

---

## §11 Hızlı Başvuru Kartı (tek sayfa özet)

### §11.1 XP — hangi anahtar ne işe yarar

```
HKLM\SYSTEM\CurrentControlSet\Services\WZCSVC          servis kaydı (Start=2, ServiceDll=wzcsvc.dll)
HKLM\Software\Microsoft\WZCSVC\Parameters\Interfaces   profil/reglament kökü (storage.h:21)
  \{GUID}\ActiveSettings      REG_BINARY  son aktif yapılandırma (WZC_WLAN_CONFIG)
  \{GUID}\Static#-----        REG_BINARY  statik profiller (ad: Static# + - ayracı)
  \{GUID}\ControlFlags        REG_DWORD   arayüz kontrol bayrakları
  \Interfaces\LayoutVersion   REG_DWORD   düzen sürümü (=4)
HKLM\Software\Microsoft\EAPOL\Parameters\General       InterfaceList (MULTI_SZ GUID)
  \Global                     authPeriod/heldPeriod/startPeriod/maxStart/SupplicantMode/AuthMode
  \Interfaces\{GUID}          EapolEnabled/DefaultEAPType/LastUsedSSID + EAP verisi
HKLM\Software\Microsoft\EAPOL\UserEapInfo              kullanıcı EAP bilgisi
HKLM\Software\Policies\Microsoft\Windows\Wireless\GPTWirelessPolicy   GPO politikası
HKLM\Software\Policies\Microsoft\Windows\Wireless\Policy\Cache        politika cache (persist.c yazıyor)
HKLM\Software\Policies\Microsoft\Windows\WiFi\GPTWiFiPolicy           wlstore/UI GPT dalı
HKLM\Software\Policies\...\Network Connections\8021X                  802.1X politikası
HKLM\Software\Microsoft\Tracing\EAPOL                 tracing (AKTİF, EnableFileTracing=1)
HKLM\Software\Microsoft\Tracing\WZCTrace              tracing (INF'te yorumlu — açılabilir)
HKLM\SYSTEM\CurrentControlSet\Services\Rasman\PPP\EAP EAP sağlayıcı DLL listesi
HKLM\SYSTEM\CurrentControlSet\Services\ndisuio        NDISUIO sürücü (XP: INF; sürücü kendisi yazmaz)
```

### §11.2 Modern (10/11) — eşleme

```
WZCSVC  -> WlanSvc   (HKLM\SOFTWARE\Microsoft\WlanSvc: LayoutVersion, AllowAPMode — XP izomorfü)
                      Services\WlanSvc: Start=2, svchost -k LocalSystemNetworkRestricted -p
                      DependOnService: nativewifip;RpcSs;Ndisuio;wcmsvc
EAPOL   -> dot3svc + Eaphost  (dot3svc: Start=3, Type=32, DependOnService: RpcSs;Ndisuio;Eaphost)
                      Services\EAPOL\...\EAPOL  -> YOK
ndisuio -> ndisuio    (aynı ad; Start=3, NDIS 6.30 — OID_802_11 riski 09 [D3])
profil   -> registry binary blob YOK  -> ProgramData XML (WlanSetProfile)
```

### §11.3 neva-wlan için 3 kural

1. XP'de profil **binary** (`WZC_WLAN_CONFIG`), modernde **XML** — depo katmanı ayrı yazılır (05 §2.1).
2. Felaket yolu `net stop wzcsvc` registry'den bağımsız; servis `Start=2` → yeniden başlatma 06 §7.3'te.
3. `Ndisuio` bağımlılığı XP **ve** modern `WlanSvc`/`dot3svc` kayıtta görünür — NDISUIO teması her iki dönemde gerçek.

---

## §12 Sürüm Geçmişi

| Sürüm | Tarih | Değişim |
|---|---|---|
| 1.0.0 | 2026-10-15 | İlk yazım: WZCSVC kökü (wirelessspd.c:159), EAPOL yolları (fonksiyon kanıtlı), eapol.inx tracing, NDISUIO (Reg* YOK), yerel servis matrisi, 5 açık madde, XP VM komutları |
| 1.1.0 | 2026-10-15 | **Açık madde 1 KAPANDI**: politika store yolları (`wirelessspd.c:157-158` GPTWirelessPolicy/Cache + `wlstore/connui.c:22` GPTWiFiPolicy + `regstore.c:117`) · **Açık madde 2 KAPANDI**: EAPOL gerçek değer adları (`eapolutil.c:46-55`) · **yeni §2.2** netwzc.inx servis kaydı (Start=2, ServiceDll, yorumlu WZCTrace) · **yeni §2.3** WZC profil saklama düzeni (storage.h sabitleri, ActiveSettings/Static# REG_BINARY, REG_LAYOUT_VERSION=4) · **yeni §6.3** LayoutVersion izomorfizmi (yerel WlanSvc) · **yeni §6.5** tam servis dökümü (WlanSvc/dot3svc DependOnService) · **yeni §9** neva-wlan etkileri (E1-E5) · **yeni §11** başvuru kartı · açık madde 5→9 (2 kapandı + WZCTrace/AD dalı/modern LayoutVersion eklendi) |
| 1.2.0 | 2026-10-15 | **3 madde daha kapandı (NT5src INF + yerel registry)**: (3) NDISUIO servis kaydı `ndisuio.inx:41-53` (ServiceType=1/StartType=3/ErrorControl=1/ServiceBinary=%12%\ndisuio.sys/LoadOrderGroup=NDIS/Ndi,Service=Ndisuio/UpperRange=noupper/LowerRange=ndis5,ndis4,ndis5_uio/Characteristics=0x28) + yerel `Services\ndisuio` Start=3 Type=1 aynı · (6) `persist.c:145-240` politika cache 8 `RegSetValueExW` = `ClassName`(msieee80211-Policy)·`description`×2·`name`·`WirelessName`·`WirelessID`·`WirelessDataType`·`WirelessData`·`whenChanged` → yeni §4.3 · (7) yerel `reg query`: `WlanSvc\LayoutVersion` **REG_DWORD 1** ≠ XP 4, `AllowAPMode` {1,0,0,0} → §6.3 kesin · §6.4 `Parameters\VWiFiSettings` düğümü + §5.1.1 eklendi. |

---

**Footer:** v1.2.0 · plan · KOD YOK · WZC/EAPOL/NDISUIO gerçek registry düzeni disk kanıtlı (storage.h/eapolutil.c/ndisuio.inx/persist.c) · 5 madde kapandı (1,2,3,6,7) · 1 kaynaklı (4) · kalan 2 madde XP VM/driver ⚠️
(storage.h/eapolutil.c) · 2 açık madde kapandı · kalan 6 madde XP VM/reg okuma
bekliyor · 01–10 serisiyle tutarlı
