---
title: "Neva Connect — XP NDISUIO Felaket Yolu Tasarımı (WZC yokken alt katman OID erişimi)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "nt5src deviceio.c disk analizi + Microsoft Learn (NDISUIO/OID) + 07 doc"
updated: 2026-10-15
---

# Neva Connect — XP NDISUIO Felaket Yolu Tasarımı (WZC yokken alt katman OID erişimi)

> **Kapsam (onaylı):** Yalnız **Windows XP** · yalnız **tarama (scan) + durum (status)** ·
> yalnız **felaket yolu** (WZC servisi dururken/öldükten sonra). Bağlantı kurma (connect),
> profil yönetimi ve Vista/Win7 fallback **bu dokümanın dışında**.
>
> **Doküman tipi:** Tasarım + sözleşme (design + contract). **Kod gövdesi YOKTUR** —
> yalnız kısa alıntı + satır numarası, tablo ve arayüz sözleşmeleri vardır.
>
> **Zero-hallucination:** Doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` ile
> işaretlenmiştir. IOCTL kodu, yapı (struct) veya izin **uydurulmamıştır**; hepsi
> diskteki XP SP1 kaynak ağacından veya alıntılanan URL'den gelir.
>
> **Yasaklar:** Bu görev yalnız bu dosyayı üretir. `git commit` ATILMAZ (orkestratöre aittir).
> Mevcut `01–07` dokümanları DEĞİŞTİRİLMEZ (düzeltme önerileri §2.5 ve §9'da kayıtlıdır).

---

## §1 Tetikleyici Koşul ve Servis Sorgusu

### §1.1 Normal yol ve felaket yolu tanımı

| Yol | Köprü | Durum |
|---|---|---|
| **(a) Normal yol** | `wlanapi.dll` → `WlanOpenHandle` → WZC (`wzcsvc`) | Her zaman denenir; 03 doc §2.4 ikili kapısı (export + dönüş kodu) ile |
| **(b) Felaket yolu (bu doküman)** | `\\.\\Ndisuio` → `IOCTL_NDISUIO_*` → OID_802_11_* | **Yalnız (a) kapandıysa** ve OS = XP ise |

03 doc K4 kanıtı: *"WlanOpenHandle XP → WZC servisi başlamamışsa/duyarsızsa hata döner"*
(03 §3.1, satır 198). 07 doc §3.5b bu yolu "uygulanabilir" bulmuş, 05 doc §9'daki
"felaket yolu" açık maddesini bu doküman kapatır.

### §1.2 Tetikleyici koşulları (kapı = HEPsi sağlanmalı)

| # | Koşul | Kanıt / gerekçe |
|---|---|---|
| T1 | OS = Windows XP (SP1+) | 05 §1.2 OS kapsamı; Vista+ `OID_802_11_*` → `NDIS_STATUS_INVALID_OID` (§3.4) |
| T2 | `WlanOpenHandle` başarısız (dönüş kodu 0 değil) | 03 §2.4 ikili kapı; hatanın kendisi tarama tetikleyicisidir |
| T3 | Aday hata kodları: `ERROR_SERVICE_NOT_ACTIVE` / `ERROR_NOT_READY` (03 §3.1, satır 592) | **⚠️ VERIFICATION REQUIRED:** XP VM'de WZC durdurulduğunda gerçek dönüş kodu seti ölçülmedi — bu yüzden kapı "hangi hata"ya göre değil, **(T4)+(T5) servis durumuna** göre açılır |
| T4 | `wzcsvc` durumu = `SERVICE_STOPPED` (veya servis yok) | §1.3 servis sorgusu |
| T5 | `ndisuio` durumu = `SERVICE_RUNNING` **veya** başlatılabilir (`SERVICE_STOPPED` → StartService) | §1.4 |

**T3 yerine servis durumu kullanılmasının gerekçesi (tasarım kararı):** hata kodu seti
doğrulanmadığı için kapı, OS + SCM durumu üzerinden **deterministik** kurulur;
`WlanOpenHandle` hatası yalnız "tara" tetikleyicisi olarak kullanılır.

### §1.3 Servis sorgusu sözleşmesi (SCM)

Çağıri sırası (kod gövdesi yok — yalnız sıra + karar tablosu):

1. `OpenSCManager` (yerel SCM veritabanı) → başarısızsa felaket yolu kapalı; normal yol hatası olduğu gibi raporlanır (L2).
2. `OpenService("wzcsvc")` + `QueryServiceStatus` → durum okunur.
3. `OpenService("ndisuio")` + `QueryServiceStatus` → durum okunur.
4. Aşağıdaki karar tablosu uygulanır.

| wzcsvc | ndisuio | Eylem | Seviye |
|---|---|---|---|
| RUNNING | herhangi | Felaket yolu **kapalı** — normal yol kullanılır | Ok / L2 (normal yol hatası) |
| STOPPED | RUNNING | **Felaket yolu AÇIK** → §6 akışı | Ok beklentisi |
| STOPPED | STOPPED | 1× `StartService("ndisuio")` dene + durum poll | Başarılıysa AÇIK; değilse **L2** |
| yok (silinmiş) | RUNNING | Felaket yolu AÇIK (WZC kurulu değilse normal yol zaten imkânsız) | Ok beklentisi |
| START_PENDING / STOP_PENDING | herhangi | **BEKLE** (poll, §1.3.1) → sonra tekrar değerlendir | — |
| herhangi | DISABLED / START_PENDING geçilemez | Felaket yolu **kapalı** → L2 "NDISUIO servisi kullanılamıyor" | L2 |

**§1.3.1 Poll sözleşmesi:** durum `*_PENDING` ise `QueryServiceStatus` tekrarlanır;
`dwWaitHint / 10` aralığıyla, `1s ≤ aralık ≤ 10s`, toplam üst sınır **30s** → üst sınır
aşılırsa L2 (Microsoft servis-başlatma prosedürü ile aynı desen, §9 [K7]).

### §1.4 Neden Ndisuio, WZC kapalıyken hâlâ ayakta olabilir (disk kanıtı)

| Kanıt | Dosya / satır | Anlamı |
|---|---|---|
| WZC bağımlılığı: `Dependencies = RpcSs, Ndisuio` | `net\config\netcfg\inf\netwzc.inx` L41–L54 (L50) | SCM, WZC'yi başlatırken Ndisuio'yu **önce** başlatır → WZC hiç başlamadıysa Ndisuio da başlamamış olabilir (StartService gerekir) |
| Ndisuio başlangıç tipi: `StartType=3` (DEMAND/manual) | `net\ndis\uio\inf\ndisuio.inx` | WZC **otomatik** olduğu sürece Ndisuio arka planda yaşar; `net stop wzcsvc` komutunun Ndisuio'yu da durdurup durdurmadığı **⚠️ VERIFICATION REQUIRED** (XP VM'de `sc query ndisuio` ile doğrulanacak) |
| `LoadOrderGroup=NDIS` · `ServiceType=1` (kernel) · `Characteristics=0x28` | aynı `.inx` | Kernel sürücüsüdür; user-mode kod asla yüklemez/UNLOAD etmez (02 §5.1 Level 0 ihlal edilmez) |

---

## §2 NDISUIO Cihaz Erişimi (satır numaralı disk kanıtı)

> Kaynak ağaç: `%TEMP%\opencode\nt5src\Source\XPSP1\NT` (XP SP1 kaynak arşivi —
> 07 doc §2.1 envanteri). Tüm satır numaraları bu ağaç aittir.

### §2.1 Cihaz yolu ve CreateFile sözleşmesi

**Cihaz yolu (gerçek, diskten):** çalışma zamanı yolu **`\\.\\Ndisuio`**.
(Kaynakta `CreateFileA` birinci argümanı: deviceio.c **L21** ve intfhdl.c **L66**;
ReactOS çapraz kontrolü de aynı DOS cihaz adını verir: `\DosDevices\Ndisuio` → `\\.\\Ndisuio`.)

| İstemci | Dosya / satır | Açık parametreleri (alıntı) |
|---|---|---|
| WZC genel handle | `net\layer2svc\zeroconf\server\deviceio.c` **L20–L27** | `CreateFileA(<yol>, GENERIC_READ \| GENERIC_WRITE, FILE_SHARE_READ \| FILE_SHARE_WRITE, NULL, OPEN_EXISTING, 0, NULL)` |
| WZC adapter handle | `net\layer2svc\zeroconf\server\intfhdl.c` **L65–L72** | aynı yol + `FILE_ATTRIBUTE_NORMAL \| FILE_FLAG_OVERLAPPED` ve `hEvent = INVALID_HANDLE_VALUE` (L72) |

**Felaket yolu kararı:** **senkron (non-overlapped)** handle seçilir (deviceio.c deseni;
L26'daki bayrak = 0). Gerekçe: fallback tek iş parçacığında (05 §3 T1/T2) eşzamanlı I/O
gerektirmez; overlapped yalnız WZC'nin kendi çok-iş parçacıklı yapısı içindir.

| Sözleşme maddesi | Değer | Kanıt |
|---|---|---|
| İstemci erişimi | `GENERIC_READ \| GENERIC_WRITE` | deviceio.c L22; IOCTL'ler `FILE_READ_ACCESS \| FILE_WRITE_ACCESS` istiyor (§2.2) — yalnız READ/WRITE verilmezse `ERROR_ACCESS_DENIED` |
| Paylaşım | `FILE_SHARE_READ \| FILE_SHARE_WRITE` | deviceio.c L23, intfhdl.c L68 — **tek handle yetmez, paylaşım zorunlu** (WZC varsa aynı cihazı açar) |
| Açık modu | `OPEN_EXISTING` | deviceio.c L25 — yaratma yok (sürücü sembolik bağlantısı zaten var) |
| Kapanış | RAII (05 §2.1 `wlacore/handle_raii` deseni) | sızıntı riski §8 R10 |

### §2.2 IOCTL envanteri (nuiouser.h — birebir, uydurma yok)

Dosya: `net\inc\nuiouser.h` (94 satır, XP SP1):

| IOCTL | Fonksiyon | Method | Erişim | Satır |
|---|---|---|---|---|
| `IOCTL_NDISUIO_OPEN_DEVICE` | 0x200 | METHOD_BUFFERED | FILE_READ \| FILE_WRITE | L33–L34 |
| `IOCTL_NDISUIO_QUERY_OID_VALUE` | 0x201 | METHOD_BUFFERED | FILE_READ \| FILE_WRITE | L36–L37 |
| `IOCTL_NDISUIO_SET_ETHER_TYPE` | 0x202 | METHOD_BUFFERED | FILE_READ \| FILE_WRITE | L42–L43 |
| `IOCTL_NDISUIO_QUERY_BINDING` | 0x203 | METHOD_BUFFERED | FILE_READ \| FILE_WRITE | L45–L46 |
| `IOCTL_NDISUIO_BIND_WAIT` | 0x204 | METHOD_BUFFERED | FILE_READ \| FILE_WRITE | L48–L49 |
| `IOCTL_NDISUIO_SET_OID_VALUE` | 0x205 | METHOD_BUFFERED | FILE_READ \| FILE_WRITE | L39–L40 |

Taban: `FSCTL_NDISUIO_BASE = FILE_DEVICE_NETWORK` (L28) · makro:
`_NDISUIO_CTL_CODE(_Function, _Method, _Access) = CTL_CODE(FSCTL_NDISUIO_BASE, ...)` (L30–L31).

**Bu dokümanın KULLANDIĞI IOCTL'ler:** `OPEN_DEVICE` · `QUERY_BINDING` · `BIND_WAIT` ·
`QUERY_OID_VALUE` · `SET_OID_VALUE` (yalnız `OID_802_11_BSSID_LIST_SCAN` tetiklemesi — §3.3).
`SET_ETHER_TYPE` **kullanılmaz** (paket okuma yolu, kapsam dışı).

**SDK gerçekliği (tasarım zorunluluğu):** `nuiouser.h` Windows **SDK'sında yoktur**
(DDK'ya aittir — §9 [K9]). Dolayısıyla Neva sabitlerini **`CTL_CODE` makrosuyla**
(winioctl.h, standart SDK) **tam yukarıdaki fonksiyon/metod/erişim değerleriyle** türetir;
değerler nuiouser.h L33–L49 ile birebir olmalıdır ve **derleme zamanı eşitlik kontrolü**
ile doğrulanır (§7 T-01).

### §2.3 XP SP1 erişim-hakları değişimi (error 50 tuzağı)

OSR/Thomas Divine (2002, §9 [K8]): *"Between XP RTM (2600) and XP SP1, the IOCTL values
that the in-box driver uses changed from FILE_ANY_ACCESS to (FILE_READ_ACCESS |
FILE_WRITE_ACCESS) inadvertently causing anyone using the old IOCTL values to talk to the
new SP1 in-box driver to fail with 'Error 50: The request is not supported' when issuing
IOCTL_NDISUIO_BIND_WAIT."*

**Sonuç (tasarım):** Sabitler `FILE_ANY_ACCESS` ile **asla** üretilmez. ReactOS (§9 [K5])
`FILE_ANY_ACCESS` kullanır — **ReactOS kanıtı XP için bağlayıcı değildir**, yalnız yapı/alan
düzeni çapraz kontrolüdür. `ERROR_NOT_SUPPORTED (50)` alınırsa §8 R4 devreye girer.

### §2.4 Handle → adapter bağlama sözleşmesi

| Adım | IOCTL | Girdi | Çıktı / kural | Kanıt |
|---|---|---|---|---|
| 1 | (yok) | — | `CreateFileA` §2.1 | deviceio.c L20 |
| 2 | `BIND_WAIT` | NULL/0 | NDISUIO binding tamamlanmasını bekler — **handle açıldıktan SONRA bir kez** | deviceio.c **L36–L44** (L38) |
| 3 | `QUERY_BINDING` | `NDISUIO_QUERY_BINDING` (`BindingIndex` 0-tabanlı) | + device adı (GUID) ve friendly name; **Offset/Length zinciri** (yalnız bu yapıda — §4.1 notu) | deviceio.c L132, L201 · nuiouser.h L84–L92 |
| 3b | — | dönen tampon | **Zorunlu sınır kontrolü**: uzunluk ≥ `sizeof(NDISUIO_QUERY_BINDING)`; `DeviceNameOffset` / `DeviceDescrOffset` ∈ [sizeof(struct), toplam]; `*Length ≤ toplam − *Offset` — ihlalde `ERROR_INVALID_DATA` | deviceio.c **L60–L92** (`DevioCheckNdisBinding`) |
| 4 | `OPEN_DEVICE` | adapter GUID wide string; uzunluk = `wcslen(wszGuid)*sizeof(WCHAR)` | handle bu adapter'a bağlanır → sonraki OID IOCTL'lerinde isim **gerekmez** | intfhdl.c **L84–L92** (L86 IOCTL, L88 uzunluk) |

**Sürüm farkı (kaynaklı):** Microsoft'un arşiv dokümantasyonu (§9 [K1], [K3])
`NDISUIO_QUERY_OID` içinde `ptcDeviceName` alanından söz eder — bu **Windows CE** düzenidir
(`#ifdef UNDER_CE`, §9 [K3]). **XP başlığındaki yapıda `ptcDeviceName` YOKTUR**
(nuiouser.h L57–L62): yalnız `Oid + Data[]`; isim bağlama `OPEN_DEVICE` ile yapılır.
Tasarım XP başlığını esas alır.

### §2.5 Sürücü tarafı disk kanıtları — 07 doc §2.2 DÜZELTMESİ

**07 doc §2.2 (satır 71) "NDISUIO kaynağı bu repo ağacında YOK" iddiası YANLIŞTIR.**
XP SP1 ağacında sürücü kaynağı **vardır**:

| Dosya | Satır | Bulgu |
|---|---|---|
| `net\ndis\uio\sys\ntdisp.c` | L91–L97 | `IoCreateDevice(FILE_DEVICE_NETWORK, FILE_DEVICE_SECURE_OPEN, FALSE/*non-exclusive*/)` + `\DosDevices\Ndisuio` sembolik bağlantısı + protokol adı "NDISUIO" (NDIS 5) |
| `net\ndis\uio\sys\ndisbind.c` | L1752–L1798 | `ndisuioQueryOidValue`: minimum tampon = `sizeof(NDISUIO_QUERY_OID)`; miniport'a `BufferLength - FIELD_OFFSET(NDISUIO_QUERY_OID, Data)` verilir; `*pBytesWritten += FIELD_OFFSET(...)`; küçük tamponda `NDIS_STATUS_BUFFER_TOO_SHORT` |
| `net\ndis\uio\inf\ndisuio.inx` | — | `Class=NetTrans` · `StartType=3` · `Characteristics=0x28` (NCF_HIDDEN\|NCF_NOT_USER_REMOVABLE) |
| `net\ndis\uio\test\uiotest.c` | — | Microsoft'un kendi konsol test istemcisi |
| `net\ndis\uio\sys\recv.c` · `send.c` · `disbind.c` · `ndisuio.h` | — | tam sürücü gövdesi |

**Etki:** 07 doc §9/§10'daki "sürücü tarafı ⚠️ VERIFICATION REQUIRED" maddesi bu kanıtla
**kısmen kapanır** (kalan doğrulama: XP runtime davranışı, §8 R7). 07'ye düzeltme önerisi
kaydı → §9 [D1].

### §2.6 Cihaz ACL / yetki belirsizliği

- `ntdisp.c` L91–L97 `IoCreateDevice` çağırır; **`IoCreateDeviceSecure` + SDDL görünmez**
  (dolayısıyla XP Ndisuio'nun gerçek DACL'i bu ağaçtan **okunamaz**).
- Microsoft "Controlling Device Access" varsayılanı: cihaz SD'si admin'lere `GENERIC_ALL`,
  diğerlerine `GENERIC_READ|WRITE|EXECUTE` — ancak bu genel kural, Ndisuio'nun legacy/non-PnP
  cihazı için **doğrudan kanıt değildir**.
- Çelişkili forum kanıtı: OSR 2009 (§9 [K12]) "non-admin → CreateFile error 5" — ancak bu
  başlık **farklı bir örnek sürücüye** (ndisprot örneği; `IoCreateDeviceSecure` SDDL
  `SDDL_DEVOBJ_SYS_ALL_ADM_ALL`) aittir; Ndisuio'ya genellenemez.

**Tasarım kararı (fail-safe):** `ERROR_ACCESS_DENIED (5)` yakalanırsa → **L2**
("Yönetici yetkisi gerekli"); sessiz retry yok. Gerçek ACL için **⚠️ VERIFICATION REQUIRED**
(XP VM'de `cacls` / Object Manager ile `\\.\\Ndisuio` denetlenecek — §7 T-08).

---

## §3 OID Seti (sözleşme)

### §3.1 Kullanılacak OID'ler (ntddndis.h — birebir)

Dosya: `public\sdk\inc\ntddndis.h`:

| OID | Değer | İşlem | Satır | Amaç (felaket yolu) |
|---|---|---|---|---|
| `OID_802_11_BSSID_LIST_SCAN` | 0x0D01011A | **SET** | L785 | Tarama tetikle (tek SET — §3.3) |
| `OID_802_11_BSSID_LIST` | 0x0D010217 | QUERY | L782 | Tarama sonucu (BSS listesi, §4) |
| `OID_802_11_BSSID` | 0x0D010101 | QUERY | L761 | Bağlı/mevcut AP MAC'i (durum) |
| `OID_802_11_SSID` | 0x0D010102 | QUERY | L762 | Mevcut SSID (durum) |
| `OID_802_11_INFRASTRUCTURE_MODE` | 0x0D010108 | QUERY | L768 | IBSS/infra (durum) |
| `OID_802_11_AUTHENTICATION_MODE` | 0x0D010118 | QUERY | L783 | open/shared/auto (durum) |
| `OID_802_11_WEP_STATUS` | 0x0D01011B | QUERY | L786 | Şifreleme durumu (durum) |

**KAPSAM DIŞI (bu dokümanda SET EDİLMEZ):** `OID_802_11_ADD_WEP` (L778),
`OID_802_11_REMOVE_WEP` (L779), `OID_802_11_RELOAD_DEFAULTS` (L787), SSID/BSSID/auth **SET**
— bunlar bağlantı kurma alanıdır (onaylı kapsam: scan/status only).

### §3.2 QUERY/SET boyut sözleşmesi

Yapılar (nuiouser.h): `NDISUIO_QUERY_OID = { NDIS_OID Oid; UCHAR Data[sizeof(ULONG)]; }`
(L57–L62) · `NDISUIO_SET_OID` aynı düzen (L69–L74). Yorum satırı (L54–L55): *"The Data part
is of variable length, determined by the input buffer length passed to DeviceIoControl."*

| Kural | Değer | Kanıt |
|---|---|---|
| Değişken veri boyutu | `dwBuffSize = FIELD_OFFSET(NDISUIO_QUERY_OID, Data) + tahmini_veri` | deviceio.c **L993** |
| Tahmin alt/üst sınırı | min 32 (`DATA_MEM_MIN`), max 65536 (`DATA_MEM_MAX`), artış 512 (`DATA_MEM_INC`) | deviceio.c **L960–L962** |
| Yeniden deneme | `ERROR_INSUFFICIENT_BUFFER` **veya** `ERROR_INVALID_USER_BUFFER` + tahmin < 64K ise +512 ile tekrar | deviceio.c **L1038–L1043** |
| Dönüş uzunluğu | veri = `dwBytesReturned - FIELD_OFFSET(NDISUIO_QUERY_OID, Data)` | deviceio.c **L1019** |
| Enum (4B) QUERY sabit boyut | `sizeof(NDISUIO_QUERY_OID)` in+out — ek tahmin yok | deviceio.c **L889–L897** (`DevioQueryEnumOID`) |
| Enum (4B) SET sabit boyut | `sizeof(NDISUIO_SET_OID)` in, out 0 | deviceio.c **L937–L945** (`DevioSetEnumOID`) |
| Sürücü tarafı eşik | miniport'a giden veri = `BufferLength - FIELD_OFFSET(...,Data)`; küçükse `NDIS_STATUS_BUFFER_TOO_SHORT` | ndisbind.c L1752–L1798 |

### §3.3 Tarama tetikleme ve okuma sırası (WZC'nin kendi deseni)

WZC'nin sırası (diskten):

1. **SET** `OID_802_11_BSSID_LIST_SCAN` → deviceio.c **L666–L676** (yorum: *"Querying for the
   BSSID_LIST in the same shot with forcing a rescan"* / *"indicate to the driver to rescan
   the BSSID_LIST for this adapter"*, L670).
2. **QUERY** `OID_802_11_BSSID_LIST` → deviceio.c **L824–L828**; tahmin boyutu
   `sizeof(NDIS_802_11_BSSID_LIST) + 19*sizeof(NDIS_WLAN_BSSID)` (L826) — yani WZC **`19 kayıt**
   tahminiyle başlar, `DATA_MEM_*` retry zinciriyle büyütür.
3. Dönüş `WzcNdisToWzc` ile ayrıştırılır (deviceio.c L836 → utils.c L142–L188).

**Felaket yolu sözleşmesi:**

- **Tetik → okuma gecikmesi:** SET sonrası hemen okumak boş liste verebilir. WZC bu aralığı
  kendi durum makinesinde yönetir (state.c L482 yorumu: *"indicate to the driver to rescan the
   BSSID_LIST for this adapter"*); **aralık süresi diskte sabitlenmemiştir** →
  **⚠️ VERIFICATION REQUIRED** (XP VM'de 0/250/500/1000 ms taramaları ölçülüp en iyi değer
  seçilecek — §7 T-05). Geçici varsayılan: 500 ms × en fazla 10 deneme (**kanıtlanana kadar geçici**).
- **SET yalnız WZC `SERVICE_STOPPED` iken** atılır (OSR uyarısı: WZC ayakta iken OID SET
  zincirleme arıza yapabilir — §9 [K8]; koşul zaten T4 ile sağlanır).

### §3.4 Sürüm/driver uyumluluk sınırları (kapsam gerekçesi)

| Sınır | Kanıt | Sonuç |
|---|---|---|
| XP SP1 auth enum `{Open, Shared, AutoSwitch}` — **WPA/WPA2 değeri YOK** | ntddndis.h `NDIS_802_11_AUTHENTICATION_MODE` (L924 sonrası) | Fallback durum çıktısında WPA bilgisi **yorumlanamaz** → `encryption: "unknown"` (§4.3); bağlantı kurma zaten kapsam dışı — **⚠️** SP2+ ek yapılarıyla doğrulanacak |
| Vista+ → Ndisuio `CreateFile` hata 5; OID'ler Native 802.11/WDI'ye taşındı | OSR izlenimleri (orta güven) + MS Learn Native 802.11 deprekasyonu (§9 [K10], [K11]) | **XP-only gate**; **Win7 fallback ⚠️ VERIFICATION REQUIRED** (onaylı kapsam dışında, GELİŞTİRİLMEZ) |
| NDIS6 miniport + eski OID → `NDIS_STATUS_INVALID_OID (0xC0010017)` | §9 [K10] + OSR izlenimleri (orta güvenilirlik) | §8 R7: `OID_802_11_BSSID_LIST` reddedilirse degrade: yalnız `OID_802_11_BSSID` + `OID_802_11_SSID` durum sorgusu |

### §3.5 IOCTL/OID hatası → seviye eşlemesi (02 §5.3 `Result` / `Level` uyumu)

| Sonuç | Level | Mesaj yönü (02 §5.2) |
|---|---|---|
| Bağarlı, veri döndü | Ok (0) | normal zarf |
| `CreateFile` hata 5 (ACCESS_DENIED) | **L2** | "Yönetici yetkisi gerekli" |
| IOCTL hata 50 (ERROR_NOT_SUPPORTED) | **L2** | "NDISUIO IOCTL sürümü uyuşmuyor" (§2.3) |
| OID → `NDIS_STATUS_INVALID_OID` / retry zinciri tükendi | **L2** | "Tarama desteklenmiyor/dönüştürülemiyor" |
| OS ≠ XP (runtime gate) veya Ndisuio yok + başlatılamıyor | **L1** | "Bu Windows sürümünde/sistemde bu yol yok" |
| C++/SEH istisnası / `bad_alloc` | **L3** | log + düzgün kapanış (02 §5.4) |

---

## §4 BSSID Listesi Ayrıştırma

### §4.1 Yapı düzeni (ntddndis.h L884–L915 — birebir)

| Yapı | Alanlar | Satır |
|---|---|---|
| `NDIS_802_11_MAC_ADDRESS` | `UCHAR[6]` | L886 |
| `NDIS_802_11_SSID` | `ULONG SsidLength` (0 olabilir) + `UCHAR Ssid[32]` | L888–L893 |
| `NDIS_WLAN_BSSID` | `ULONG Length` · `MacAddress` · `Reserved[2]` · `Ssid` · `ULONG Privacy` · `Rssi` · `NetworkTypeInUse` · `Configuration` · `InfrastructureMode` · `SupportedRates` | **L896–L909** |
| `NDIS_802_11_BSSID_LIST` | `ULONG NumberOfItems` ("in list below, at least 1") + `NDIS_WLAN_BSSID Bssid[1]` | **L911–L915** |

**⚠️ ÖNCEKİ YORUM DÜZELTMESİ (07 §3.5b taslağındaki "NumEntries/Offset zinciri" ifadesi):**
`NDIS_802_11_BSSID_LIST` içinde **offset zinciri YOKTUR**. Düz (flat) listedir:
`NumberOfItems` + her kayıt kendi `Length` alanı taşır. **Offset/Length zinciri yalnız
`NDISUIO_QUERY_BINDING` yapısındadır** (nuiouser.h L86–L90; doğrulama §2.4 adım 3b).

### §4.2 Yürüme (walk) algoritması — WZC'nin kanıtladığı biçim

WZC referans yürümesi (utils.c **L160–L183**):

- başlangıç: `prawList = (LPBYTE)&(pndList->Bssid[0])` (L160)
- döngü: `i < NumberOfItems` (L165)
- **adım: `prawList += pndBssid->Length` (L172)** — sabit stride DEĞİL, kayıt başına
  `Length` kadar ilerleme
- kopya alanlar (L175–L182): MacAddress · Ssid · Privacy · Rssi · NetworkTypeInUse ·
  Configuration · InfrastructureMode · SupportedRates

**Felaket yolu için ZORUNLU sınır kontrolleri (tasarım sözleşmesi):**

| # | Kural | Hata |
|---|---|---|
| B1 | `toplam_veri ≥ sizeof(NDIS_802_11_BSSID_LIST)` | `ERROR_INVALID_DATA` → L2 |
| B2 | `NumberOfItems ≤ (toplam_veri − offsetof(Bssid)) / sizeof(NDIS_WLAN_BSSID)` (üst sınır — `NumberOfItems = 0xFFFFFFFF` sahtesine karşı) | L2 |
| B3 | Her adımda: `Length ≥ sizeof(NDIS_WLAN_BSSID)` **ve** `kalan ≥ Length` | L2 (kırık/kesilmiş liste) |
| B4 | `SsidLength ≤ 32` | kaydı atla (L2 değil — kısmi sonuç) |
| B5 | `dwBytesReturned - FIELD_OFFSET(NDISUIO_QUERY_OID, Data)` = toplam_veri (§3.2) | L2 |
| B6 | Toplam tampon ≤ `DATA_MEM_MAX` (65536) | retry zinciri durdurulur, eldekiyle yetinilir |

> B1–B3, deviceio.c `DevioCheckNdisBinding` (L60–L92) ile **aynı doğrulama felsefesinin**
> BSSID listesine taşınmasıdır → tutarlılık kanıtı (offset/uzunluk sınırları içinde tut).

### §4.3 Çıktı eşlemesi (mevcut JSON sözleşmeye — 05 §5.5)

| NDIS alanı | Mevcut alan sözlüğü karşılığı | Not |
|---|---|---|
| `MacAddress[6]` | `bssid` (MAC metni) | doğrudan |
| `Ssid.Ssid` / `SsidLength` | `ssid` | `SsidLength` kadar okunur |
| `Rssi` | `signal` | ntddndis.h L903 yorumu: *"strength in dBm"* |
| `Privacy` (0/1) | `encrypted` bool | WEP iması — §3.4 nedeniyle yalnız bu güvenilir |
| `InfrastructureMode` | `mode` (infra/adhoc) | `NDIS_802_11_NETWORK_INFRASTRUCTURE` |
| `NetworkTypeInUse` | `networkType` | — |
| `Configuration` (frekans) | `channel` (varsa) | kanal hesabı **⚠️ VERIFICATION REQUIRED** (XP VM'de doğrulanacak) |
| `SupportedRates` | — (JSON'da yok) | atlanır |
| Auth/şifreleme (WPA) | `encryption: "unknown"` | §3.4 — enum'da WPA yok |
| `radioType` vb. modern alanlar | **yok** | alan üretmez (uydurma yok) |

---

## §5 Modül Yerleşimi (L0–L4 katmanı + Level 0–3 uyumu)

### §5.1 Katman tablosu (L0–L4)

| Katman | İçerik | Neva kodu var mı? |
|---|---|---|
| **L0** | `ndisuio.sys` kernel protokol sürücüsü + NIC miniport OID işleyicisi | **HAYIR** — 02 §5.1 "Kernel'den kaçınma"; sürücü yazılmaz/patch'lenmez |
| **L1** | Win32 geçidi: `CreateFile("\\\\.\\Ndisuio")` + `DeviceIoControl` + sabitler (`CTL_CODE` ile kendi tanımı, §2.2) | EVET (yeni) |
| **L2** | Felaket yolu modülü: gate (§1), bağlama (§2.4), OID (§3), ayrıştırma (§4), `Result{Level}` üretimi | EVET (yeni) |
| **L3** | Mevcut çekirdek: `wlabase` (loader/xp_gate) · `wlacore` (handle RAII) · `wlaenum` (scan/list semantiği) | VAR (05 §2.1) |
| **L4** | Komut + zarf: `wifi_scan` / `wifi_status` JSON (05 §5.2–§5.3) | VAR — **aynı sözleşme**, fallback'te zarf şekli DEĞİŞMEZ |

### §5.2 Dosya yerleşimi seçenekleri ve kararı

| Seçenek | Artılar | Eksileri | Karar |
|---|---|---|---|
| **A)** `wlabase/xp_ndisuio_fallback.{h,cpp}` | `xp_gate` zaten burada (05 §2.1: *"XP beyaz liste + dönüş kodu kapısı"*) — gate + alternatif transport aynı katmanda; `wlaenum` yalnız DTO tüketir | 05 §2.1 wlabase notu ("yalnız loader") genişlemeli (düzeltme önerisi §9 [D2]) | ✅ **SEÇİLDİ** |
| B) `wlaenum/xp_scan_ndisuio.*` | tarama-semantiği sahipliği doğal | transport + SCM sorgusu enum katmanına sızar; `wlaenum` driver IOCTL bilmez | ✗ |
| C) Yeni `wlafallback` modülü/yeni artefakt | izolasyon | 05 mimarisine yeni EXE/yüzey ekler — onaysız yapısal değişiklik (AGENTS §4 Faz 1) | ✗ |

**Sorumluluk dağılımı (sözleşme):**

| Bileşen | Sorumluluk |
|---|---|
| `wlabase/xp_gate` (mevcut) | OS/XP tespiti + `WlanOpenHandle` dönüş kodu → "felaket yolu adayı" bayrağı |
| `wlabase/xp_ndisuio_fallback` (yeni) | §1 servis sorgusu · §2 handle+IOCTL · §3 OID · §4 walk → `XpScanEntry` listesi DTO'su |
| `wlacore/handle_raii` (mevcut) | fallback handle için de RAII sargısı (tek doğruluk kaynağı) |
| `wlaenum/scan` (mevcut) | normal yol; fallback DTO'yu aynı `wifi_scan` zarfına çevirir (L4 tekliği) |

### §5.3 Kompil/zaman-kapıları

| Kapı | Tür | Açıklama |
|---|---|---|
| OS = XP | **runtime** | sürüm tespiti ile; XP dışı → L1, IOCTL kodlarına hiç dokunulmaz (Win7 ⚠️ — §3.4) |
| `WlanOpenHandle` başarısız | **runtime** | 03 §2.4 ikili kapı zincirinin devamı |
| SCM durumu (T4/T5) | **runtime** | §1.3 |
| IOCTL sabitleri | **derleme zamanı** | `CTL_CODE` sabit ifadeler + eşitlik kontrolü (§7 T-01) |
| XP başlıkları | — | `nuiouser.h` SDK'da yok (§2.2) → yeniden tanımlama zorunlu; **yalnız 5 IOCTL + 3 yapı**, satır numaralı künye ile |

### §5.4 İplik/model uyumu (05 §3)

- Fallback çağrıları **mevcut işçi ipliklerinde** (T1/T2) senkron çalışır; ek iplik yok.
- `DeviceIoControl` bloke edebilir (BIND_WAIT/OID) → **30s üst sınır bütçesi** (§1.3.1);
  XP'te senkron IOCTL'ta iptal mekanizması **yoktur** — en kötü durum blokajı §8 R9'da
  bilinçli olarak kabul edilir.
- Kapanış sırası (05 §3.4): fallback handle kapanışı `wlacore` RAII'sine devredilir.

---

## §6 ASCII Karar Akışı

```
[CLI: wifi_scan / wifi_status]
        │
        ▼
[L3] wlabase: LoadLibrary("wlanapi.dll") → beyaz liste (03 §2.4)
        │
        ▼
[L3] wlacore: WlanOpenHandle(dwClientVersion=1, ...)      ← XP
        │
   ┌────┴────────────────┐
   │ başarılı             │ başarısız
   ▼                      ▼
[NORMAL YOL]         [FELAKET YOLU KAPISI — bu doküman]
 (05 §4.1/§4.2)            │
                            ▼
                 OS == XP ?  ── hayır ──► L1 "yol yok"
                            │ evet
                            ▼
                 SCM sorgusu (§1.3): wzcsvc + ndisuio durumları
                            │
        ┌───────────────────┼───────────────────────┐
        │ wzcsvc RUNNING    │ STOPPED + ndisuio RUN  │ ndisuio STOPPED
        ▼                   ▼                        ▼
  normal yola GERİ         FELAKET AÇIK         StartService(ndisuio)
  (WZC uyanmış)            ↓                    + poll ≤30s (§1.3.1)
                            │                  ┌──────┴───────┐
                            │                başarısız      başarılı
                            │                    ▼              ▼
                            │                 L2 mesaj      FELAKET AÇIK
                            ▼
        CreateFile("\\.\Ndisuio", RW + SHARW + OPEN_EXISTING)   [§2.1]
                 │ hata 5 / hata 50 / diğer
                 └──────────────► L2 (§3.5) — log + kullanıcı mesajı
                            │ başarılı
                            ▼
        IOCTL_NDISUIO_BIND_WAIT                      (§2.4 adım 2)
                            ▼
        IOCTL_NDISUIO_QUERY_BINDING ×N               (§2.4 adım 3)
          + sınır kontrolleri DevioCheckNdisBinding birebir (L60–L92)
                            ▼
        her adapter için:
        IOCTL_NDISUIO_OPEN_DEVICE (GUID wide, §2.4 adım 4)
                            ▼
        SET OID_802_11_BSSID_LIST_SCAN               (§3.3 — yalnız WZC STOPPED)
                            ▼
        poll: QUERY OID_802_11_BSSID_LIST             (§3.3; ⚠️ aralık ayarlanacak)
                 │ boş / INVALID_OID                  │ veri döndü
                 ▼                                    ▼
        degrade: BSSID+SSID durum sorgusu           WALK (§4.2)
        (§3.4 R7) → kısmi sonuç               B1–B6 sınırları
                            │                          │
                            ▼                          ▼
        XpScanEntry DTO → wlaenum → AYNI wifi_scan/wifi_status ZARFI (L4)
                            │
                            ▼
        Handle RAII kapanış (05 §3.4 sırası) → CLI çıktısı
```

---

## §7 Testler (06 doc §7'ye EKLENMEK ÜZERE — 06 doc değiştirilmez)

> Kabul kriteri: her satır ya ölçülür ya da ⚠️ işaretiyle "açık" kalır.
> Ortamlar: **U** = unit (driver yok, sahte tampon) · **VM** = gerçek XP SP1 sanal makine.

| ID | Ortam | Senaryo | Beklenen |
|---|---|---|---|
| **T-01** | U | `CTL_CODE` ile üretilen 5 IOCTL sabitinin nuiouser.h L33–L49 değerleriyle eşitliği (derleme zamanı) | 5/5 eşit; `FILE_ANY_ACCESS` kullanımı **yok** (§2.3) |
| **T-02** | U | Yapay `NDIS_802_11_BSSID_LIST` tamponları: normal (3 kayıt), `NumberOfItems=0xFFFFFFFF`, `Length=0`, kesilmiş son kayıt, `SsidLength=33` | B1–B6 ihlalleri L2/atlama; sınır-ötesi okuma **YOK** ( bellek denetleyicisi temiz) |
| **T-03** | U | Sahte `NDISUIO_QUERY_BINDING` yanıtları: offset < sizeof, offset+len > toplam | `ERROR_INVALID_DATA` (deviceio.c L60–L92 ile birebir davranış) |
| **T-04** | VM | `net stop wzcsvc` → `sc query wzcsvc` = STOPPED → `wifi_scan` | Felaket yolu devreye girer; zarf 05 §5.2 ile **aynı şekil**; ağ listesi döner |
| **T-05** | VM | T-04 altında SET→QUERY aralığı 0/250/500/1000 ms × 10 tekrar | Boş-dönüş oranı ölçülür → varsayılan aralık kararı (**⚠️ açık madde** §9 [D3]) |
| **T-06** | VM | `net stop ndisuio` → `wifi_scan` | 1× StartService denemesi → başarısızsa **L2**; **çökme/sınırsız bekleme YOK** |
| **T-07** | VM | Eski erişim-haklı IOCTL değeri (FILE_ANY_ACCESS türevi) bilinçli kullanıldığında | `ERROR_NOT_SUPPORTED (50)` gözlenir → §2.3 regresyon kanıtı |
| **T-08** | VM | Yönetici olmayan kullanıcı ile `wifi_scan` (felaket yolu açıkken) | Gözlenen gerçek hata (5 vs başka) kaydedilir → §2.6 ⚠️ kapanır |
| **T-09** | VM | `net start wzcsvc` → `wifi_scan` | Kapı kapalı → **normal yol**; fallback IOCTL'lerine hiç çağrılmaz (log kanıtı) |
| **T-10** | VM | §1.3 karar tablosunun 6 satırı (WZC durumları × Ndisuio durumları) | Tablo davranışı birebir |
| **T-11** | U | `OID_802_11_BSSID_LIST` → sahte `NDIS_STATUS_INVALID_OID` | Degrade dalı (§3.4 R7): BSSID+SSID durum çıktısı, L2 DEĞİL |
| **T-12** | VM | Gerçek XP NIC ile `OID_802_11_BSSID_LIST` (WZC durdurulu halde) | Dönüş verisi §4.2 walk ile ayrışır; `NDIS_WLAN_BSSID.Length` değerleri kayıt-kayıt loglanır (§9 [D4] kanıtı) |

**Not:** T-04/T-06/T-09 **duman testidir** ve yalnız XP VM'de koşulur; XP VM tanımı 06 doc
ortam bölümündedir — bu doküman 06'ya yalnız **ek tablo** önerir.

---

## §8 Risk Tablosu

| # | Risk | Olasılık/etki | Önlem (tasarım) | Kalan belirsizlik |
|---|---|---|---|---|
| **R1** | WZC `START_PENDING` / `STOP_PENDING` yarışı: kapı açıkken WZC geri gelir | orta/yüksek | §1.3 poll + her OID öncesi **tekrar gate** (wzcsvc hâlâ STOPPED mı?) | — |
| **R2** | Ndisuio da ölü (WZC bağımlılığı yüzünden ikisi birlikte düşmüş) | orta/orta | 1× `StartService` + ≤30s poll → başarısızsa L2 (fail-safe) | — |
| **R3** | Vista/7/10'da çağrılmaya devam | düşük/yüksek | OS runtime gate → L1; IOCTL kodlarına hiç dokunulmaz | **Win7 fallback ⚠️ VERIFICATION REQUIRED** (kapsam dışı) |
| **R4** | IOCTL erişim-hakları yanlış türetilirse (RTM vs SP1) | düşük/yüksek | T-01 derleme-zamanı eşitlik + `FILE_ANY_ACCESS` yasağı (§2.3) | — |
| **R5** | `CreateFile` → `ERROR_ACCESS_DENIED` (ACL) | orta/orta | L2 "Yönetici yetkisi gerekli"; fail-safe, retry yok | **Gerçek ACL ⚠️** (§2.6, T-08) |
| **R6** | Sahte/bozuk BSSID listesi → bellek taşması / sınır-ötesi okuma | düşük/yüksek | B1–B6 sınırları (§4.2) + T-02 negatifleri | — |
| **R7** | Bazı driver'lar `OID_802_11_BSSID_LIST`'i reddeder (`NDIS_STATUS_INVALID_OID`) | orta/orta | Degrade: BSSID+SSID durum sorgusu (§3.4) + T-11 | XP driver envanteri ölçülecek (⚠️, T-12) |
| **R8** | SET→QUERY arası yarış: tarama bitmeden boş liste | yüksek/orta | Poll + tekrar-dene (§3.3) | **Aralık süresi ⚠️** (T-05) |
| **R9** | Senkron `DeviceIoControl` blokajı (BIND_WAIT/OID) → tık'lanamazlık | düşük/yüksek | 30s üst sınır bütçesi + tek iplik (§5.4); iptal mekanizması XP'te yok (bilinçli kabul) | Beklenmedik sürücü-kilitlenmesi kalırsa L3'e düşürme opsiyonu açık |
| **R10** | Handle sızıntısı (fallback aç-kapa sıklığı) | orta/düşük | `wlacore` RAII; adapter başına tek handle (intfhdl.c hash deseni hafifletir) | — |
| **R11** | WPA/WPA2 ağlarında yanlış durum raporu (enum'da kavram yok) | yüksek/düşük | `encryption:"unknown"` (§4.3); **bağlantı kurma yok** | ⚠️ SP2+ ek yapıları |
| **R12** | Başka sürecin WZC'yi yeniden başlatması (eşzamanlı erişim) | düşük/orta | R1 tekrar-gate + paylaşımlı `FILE_SHARE_*` (§2.1) | — |
| **R13** | 07 doc §2.2/§3.5b eski iddialarıyla çelişki | orta/düşük | Bu doküman §2.5 + §4.1 düzeltme kaydı; 07'ye dokunulmaz | 07'ye düzeltme ⚠️ onaya bağlı ([D1]) |

---

## §9 Kaynaklar

### §9.1 Web kaynakları (URL listesi)

| # | Kaynak | URL | Güvenilirlik |
|---|---|---|---|
| **K1** | MS Learn (arşiv) — `IOCTL_NDISUIO_QUERY_OID_VALUE` | https://learn.microsoft.com/en-us/previous-versions/ms898828(v=msdn.10) | Yüksek (Microsoft, arşiv) |
| **K2** | MS Learn (arşiv) — same IOCTL (Windows Embedded CE 6.0) | https://learn.microsoft.com/en-us/previous-versions/windows/embedded/ee485711(v=winembedded.60) | Yüksek (Microsoft, arşiv) |
| **K3** | MS Learn (arşiv) — `NDISUIO_QUERY_OID` yapısı (`#ifdef UNDER_CE ptcDeviceName` kanıtı) | https://learn.microsoft.com/en-us/previous-versions/ms901087(v=msdn.10) | Yüksek (Microsoft, arşiv) |
| **K4** | MS Learn (arşiv) — Network Driver IOCTLs indeksi | https://learn.microsoft.com/en-us/previous-versions/ms895627(v=msdn.10) | Yüksek (Microsoft, arşiv) |
| **K5** | ReactOS — `nuiouser.h` kaynak dosyası (IOCTL/yapı çapraz kontrol; `FILE_ANY_ACCESS` farkı) | https://doxygen.reactos.org/d0/da7/nuiouser_8h_source.html | Orta (3. taraf; XP ile farklılık var) |
| **K6** | ReactOS — NDISUIO `ioctl.c` (OPEN_DEVICE → bind; `FsContext==NULL` reddi) | https://doxygen.reactos.org/d6/d96/drivers_2network_2ndisuio_2ioctl_8c.html | Orta |
| **K7** | MS Learn — Starting a Service (poll / `QueryServiceStatusEx` / waitHint deseni) | https://learn.microsoft.com/en-us/windows/win32/services/starting-a-service | Yüksek (resmi) |
| **K7b** | MS Learn — `StartServiceA` (hata kodları, XP minimum sürüm) | https://learn.microsoft.com/en-us/windows/win32/api/winsvc/nf-winsvc-startservicea | Yüksek (resmi) |
| **K7c** | MS Learn — winsvc fonksiyon indeksi (`OpenService` / `QueryServiceStatus`) | https://learn.microsoft.com/en-us/windows/win32/api/winsvc/ | Yüksek (resmi) |
| **K8** | OSR — Thomas Divine (2002): XP RTM→SP1 IOCTL erişim değişimi, **error 50**, WZC = NDISUIO kullanır, WZC açıkken OID SET tehlikesi | https://community.osr.com/t/set-ndis-oid-under-winxp/13082 | Orta-Yüksek (uzman; dönemine yakın birincil yorum) |
| **K9** | Stack Overflow (2012) — `CreateFile` başarılı + `OPEN_DEVICE` error 50 + "nuiouser.h regular SDK'da yok" | https://stackoverflow.com/questions/10744452/ndis-oid-query-from-user-mode | Orta (topluluk) |
| **K10** | MS Learn (arşiv) — Native 802.11 OIDs (NDIS 6.0+; Win10'da deprekasyon) | https://learn.microsoft.com/en-us/previous-versions/windows/hardware/wireless/native-802-11-oids | Yüksek (resmi) |
| **K11** | MS Learn (arşiv) — Introduction to Native 802.11 (WDI geçiş notu) | https://learn.microsoft.com/en-us/previous-versions/windows/hardware/wireless/introduction-to-native-802-11 | Yüksek (resmi) |
| **K11b** | MS Learn (arşiv) — `OID_802_11_BSSID` davranış dokümanı | https://learn.microsoft.com/en-us/previous-versions/windows/hardware/network/ff559253(v=vs.85) | Yüksek (resmi) |
| **K12** | OSR (2009) — non-admin "Access Denied" tartışması (**farklı örnek sürücü**, Ndisuio'ya genellenemez) | https://community.osr.com/t/ndis-sample-driver-access-denied/37734 | Düşük-Orta (ilgisiz sürücü — yalnız §2.6 için) |
| **K13** | MS Learn — `WlanOpenHandle` (03 doc K4 ile aynı) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanopenhandle | Yüksek (resmi) |

### §9.2 Disk kaynakları (XP SP1 kaynak ağacı — satır numaralı)

| Dosya | Kullanılan satırlar | Kanıt konusu |
|---|---|---|
| `net\layer2svc\zeroconf\server\deviceio.c` | L20–L27 · L36–L44 · L60–L92 · L132 · L201 · L666–L676 · L824–L836 · L889–L897 · L937–L945 · L960–L962 · L993 · L1019 · L1038–L1043 | CreateFile · BIND_WAIT · binding doğrulama · QUERY_BINDING · SCAN tetik · BSSID_LIST okuma · enum OID · boyut/retry zinciri |
| `net\layer2svc\zeroconf\server\intfhdl.c` | L65–L72 · L84–L92 | Overlapped handle · `OPEN_DEVICE` (GUID, `wcslen*2`) |
| `net\layer2svc\zeroconf\server\utils.c` | L142–L188 (özellikle L160–L183) | BSSID listesi yürümesi (`prawList += pndBssid->Length`) |
| `net\layer2svc\zeroconf\server\state.c` | L482 | SCAN tetik zamanlaması yorumu |
| `net\inc\nuiouser.h` | L28–L49 · L54–L74 · L84–L92 | IOCTL envanteri · QUERY/SET_OID · QUERY_BINDING (offset zinciri) |
| `public\sdk\inc\ntddndis.h` | L761–L787 · L884–L915 · enumlar | OID değerleri · 802.11 yapıları |
| `net\ndis\uio\sys\ntdisp.c` | L91–L97 | `IoCreateDevice` + `\DosDevices\Ndisuio` (§2.5 düzeltmesi) |
| `net\ndis\uio\sys\ndisbind.c` | L1752–L1798 | Sürücü OID tampon eşikleri |
| `net\ndis\uio\inf\ndisuio.inx` | — | `StartType=3` · `Characteristics=0x28` |
| `net\config\netcfg\inf\netwzc.inx` | L41–L54 (L50) | **`Dependencies = RpcSs, Ndisuio`** |
| `net\ndis\uio\test\uiotest.c` | — | Microsoft konsol test istemcisi |

### §9.3 Vault içi bağımlılıklar

- `07-xp-wzc-stack-kaynak-arastirmasi.md` §2.2 / §3.5 / §9 — **iki düzeltme**: §2.5
  (sürücü kaynağı VAR) ve §4.1 (offset zinciri yalnız QUERY_BINDING) bu dokümanda kayıtlı;
  07'ye dokunulmadı.
- `05-neva-wlan-cekirdek-modul-tasarimi.md` §2.1 (modül sorumlulukları) · §3 (iplik) ·
  §4.1–§4.3 (akışlar) · §5 (JSON) · §9 (felaket yolu açık madde).
- `03-wlan-api-ozellik-kisitlama-tablosu.md` §2.4 (ikili kapı) · §3.1 (K4) · §5 (karar ağacı).
- `02-coklu-exe-windows-sdk-derleme-mimarisi.md` §5.1–§5.3 (Level 0–3, `Result` tipi).
- `06-test-ve-kalite-plani.md` §7 — §7'deki tablo **ek** niteliğindedir.

### §9.4 Açık düzeltilmesi gereken vault maddeleri (onay bekliyor — bu görevde DOKUNULMADI)

| # | Hedef | Öneri |
|---|---|---|
| **D1** | 07 doc §2.2 (satır 71) "kaynak YOK" + §9/§10 ⚠️ maddesi | `net\ndis\uio\` kanıtıyla güncelle (§2.5) |
| **D2** | 05 doc §2.1 wlabase notu ("yalnız loader") | `xp_ndisuio_fallback` sorumluluğunu ekle (§5.2-A) |
| **D3** | 05 doc §9 "felaket yolu" açık maddesi + T-05 aralık kararı | Bu dokümanla kapatıldı olarak işaretle; aralık T-05'ten gelir |
| **D4** | 06 doc §7 | T-01…T-12 tablosunu ekle |
