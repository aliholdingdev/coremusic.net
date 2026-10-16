---
node_id: K014-network/connectivity/neva-connect-wlan-dallanma-mimarisi
node_type: subdomain
status: draft
parent_id: K014-network/connectivity
children: []
depends_on: []
evidence:
  - "01-wlanapi-sürüm-matrisi-ve-windows-plani (pconlife 10 DLL PE export analizi, .ai/projects/NevaConnect/)"
  - "Microsoft Learn Native Wifi API (learn.microsoft.com/en-us/windows/win32/nativewifi/)"
validation:
  - "2026-10-15 — export matrisi REA/Ghidra 12.1.4 + PE parser çift yöntem çapraz doğrulandı"
open_questions:
  - "Win11 wlanapi.dll export seti UNKNOWN (pconlife'ta yok)"
  - "BT sürücü değiştirme kapsamı ve sürücü yazımı `⚠️ VERIFICATION REQUIRED` (kullanıcı onayı)"
group: mimari
---

# neva-connect-wlan-dallanma-mimarisi — K014-network/connectivity (kademe-3 · subdomain · draft)

**ELI15:** CoreMusic'in RPi5/Linux üzerinde çalışan "Neva Connect" köprü sunucusunun (WebSocket 9742) **Windows WLAN dalı**: Windows makinelerinde wifi ağını listelemek, ağa bağlanmak ve bağlantı durumunu Web UI'a bildirmek için `wlanapi.dll` (Native Wifi API) üzerine kurulan çok-EXE'li C++ mimarisi.

**Sınır (ne DEĞİL):** Ses işleme (Neva Engine) · BT/USB/depolama EXE'lerinin iç tasarımı (kendi node'ları) · Web UI frontend tasarımı · kernel sürücü yazımı.

**Durum:** draft — bu dosyadaki satırlar **TASARIM İDDİASI**'dır (kod yoktur, kod kullanıcı onayıyla yazılacaktır). Her iddia ya disk kanıtına (§8) ya da açık `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretine dayanır.

**Kayıt notu:** `node_id` 2026-10-15'te `eva-connect-…` → `neva-connect-…` olarak düzeltildi (önceki değer orkestratör dispatch'indeki yazım hatasıydı; dosya adı = node_id eşitliği sağlandı).

---

## §1 Kapsam

### §1.1 Mevcut taban: neva-connect (RPi5 / Linux)

Mevcut `neva-connect` projesi Linux/RPi5 hedeflidir ve Windows WLAN katmanı **içermez**
(`.ai/projects/NevaConnect/01` §4: "Mevcut durum: neva-connect RPi5/Linux odaklı (L0-L3,
WebSocket 9742, I2C PCM5122); Windows WLAN katmanı yok"). Kanıt dosyası:
`E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md`.

| Öğe | Mevcut değer (CLAUDE.md kanıtı) |
|---|---|
| Katmanlar | L0 Altyapı (sysfs/I2C/dbus/spdlog) · L1 Sürücüler (I2C/I2S/GPIO/BLE/WiFi manager) · L2 Uygulama (WebSocket 9742, komut işleyici, ağ yöneticisi) · L3 Sunum (durum Web UI, LED) |
| WebSocket portu | 9742 — "Same protocol as Neva Engine" (uyumluluk katmanı) |
| Kalite hedefi | WiFi yeniden bağlanma < 5 s · WiFi/BLE stabilite · CPU < %20 |
| OS entegrasyonu | Linux: NetworkManager dbus, `/dev/i2c-1`, sysfs |

### §1.2 Windows WLAN dalı (bu node'un konusu)

Windows hedefinde Linux dbus/NetworkManager karşılığı **Native Wifi API**'dir
(`wlanapi.dll`); katman, mevcut L0-L3 sözleşmesini koruyarak Windows'a uyarlanır:

1. **ÖNCELİK — wlanapi.dll çekirdeği:** wifi listeleme (enum), ağa bağlanma (connect),
   durum bildirimi (status/notification). Bu üçü F1 fazının çekirdeğidir
   (`neva-wlan.exe`).
2. **ÇOKLU-EXE (kullanıcı direktifi, 2026-10-15):** tek monolitik exe yerine işlevsel
   olarak ayrılmış birden fazla exe — `neva-wlan.exe` (F1 çekirdek), `neva-bt.exe`,
   `neva-usb.exe`, `neva-storage.exe`. Ayrıştırma gerekçesi ve derleme matrisi
   `02` dokümanına atıflıdır (bkz §8 — 02/03/04 dokümanları 2026-10-15'te diskte
   mevcut ve ölçüldü: 769/678/1129 satır).
3. **Kapsam dışı kalanlar:** BT/USB/depolama EXE'lerinin iç katmanları bu node'da
   tasarlanmaz; yalnız IPC ve JSON sözleşmesi (§6) üzerinden bağlanırlar.

### §1.3 Node konumu ve bağımlılık

- **Parent:** `K014-network/connectivity` (bağlantıyı kurma/sürdürme/kopunca yeniden
  kurma — bağlantı durumunun tutulması). Bu node, "bağlantı durumu" tanımının Windows
  WLAN spesifik dallanmasıdır.
- **Bağımlılık zinciri (özet):** 01 export matrisi (disk kanıtı) → bu tasarım node'u →
  kod (kullanıcı onayı bekliyor, Faz 1-4).
- **Kod yazım durumu:** YOK. 01 dokümanı §6.2: "Kod onayı — §4 fazları kullanıcının
  açık onayıyla başlar." Bu node da salt tasarım/plan taşır.

### §1.4 Sınır kararları (ne DEĞİL, gerekçesiyle)

| Karar | Kapsam dışı yön | Gerekçe |
|---|---|---|
| Ses/I2S/PCM5122 yok | Windows dalı ses donanımı konuşmaz | neva-connect Linux dalının işi (L1 sürücü); Windows'ta WASAPI ayrı domen (K014 dışı) |
| Sürücü (kernel) yok | WDK/INF ile sürücü yazılmaz | 01 §6.2 kod onayı kapısı + dispatch: "BT sürücü değiştirme kapsamı ⚠️ VERIFICATION REQUIRED"; user-mode wlanapi yeterli |
| Forensics/pentest yok | Paket dinleme, handshake analizi | wireless-connect'in işi; bu node yalnız management-plane (WLAN API) kullanır |
| WinRT Wi-Fi API yok | `Windows.Devices.WiFi` yüzeyi | XP..Win10 tek binary hedefiyle çelişir; WinRT Vista-öncesi imkânsız |

---

## §2 Katman Diyagramı


### §2.1 ASCII katman şeması (L0-L4 + çok-EXE topolojisi)

```text
┌─────────────────────────────────────────────────────────────────┐
│ Web UI (CoreMusic SPA)                                          │
│   wifi listesi · bağlan · durum rozeti · (AP menüsü — degrade)  │
└──────────────────────────────┬──────────────────────────────────┘
                               │ WebSocket :9742 (neva-connect protokolü)
┌──────────────────────────────▼──────────────────────────────────┐
│ L4 — bridge                                                     │
│   WebSocket 9742 köprüsü: JSON komut/olay yönlendirme           │
│   (mevcut neva-connect L2 protokolüyle aynı şekil: {"cmd":…})   │
└──────────────────────────────┬──────────────────────────────────┘
                               │ yerel çağrı (aynı süreçte kütüphane ya da §6 IPC)
┌──────────────────────────────▼──────────────────────────────────┐
│ L3 — wlaprofile : profil XML yaşam döngüsü                     │
│   WlanSetProfile / GetProfile / DeleteProfile / GetProfileList  │
│   (WPA2-PSK/AES profil şablonu üretimi, düz metin anahtar       │
│    okuma bayrağıyla dikkatli okuma)                             │
├─────────────────────────────────────────────────────────────────┤
│ L2 — wlaconn : bağlantı kontrolü                                │
│   WlanConnect (WLAN_CONNECTION_PARAMETERS + profile XML)        │
│   WlanDisconnect · WlanReasonCodeToString (hata çevirisi)       │
├─────────────────────────────────────────────────────────────────┤
│ L1 — wlaenum : arayüz & ağ keşfi                                │
│   WlanEnumInterfaces → WlanGetAvailableNetworkList / List2      │
│   WlanGetNetworkBssList (SSID/BSSID/RSSI/PHY) · WlanScan        │
├─────────────────────────────────────────────────────────────────┤
│ L0 — wlabase : platform omurgası                                │
│   dynload resolver: LoadLibrary("wlanapi.dll")+GetProcAddress   │
│   handle RAII: WlanOpenHandle / WlanCloseHandle (sürüm nezaketi │
│     WLAN_API_VERSION_1 ↔ _2)                                    │
│   bildirim kuyruğu: WlanRegisterNotification callback →         │
│     kilitli SPSC kuyruk → ana thread poll                       │
│   (WlanCloseHandle race gotcha'sı — bkz §2.2, §4.2)             │
└─────────────────────────────────────────────────────────────────┘

Çok-EXE topolojisi (kullanıcı direktifi — iç tasarım 02/03 doc'larında):
  neva-wlan.exe   ← F1 çekirdek: L0-L4'ün bu node'daki sahibi
  neva-bt.exe     ← Bluetooth (kapsam dışı bu node; ⚠️ VERIFICATION REQUIRED)
  neva-usb.exe    ← USB (kapsam dışı bu node)
  neva-storage.exe← depolama (kapsam dışı bu node)
```

### §2.2 L0 wlabase — sorumluluklar

| # | Sorumluluk | Temel API / yapı | Not |
|---|---|---|---|
| 1 | Dinamik çözümleme | `LoadLibrary` + `GetProcAddress` (export tablosu) | Sabit `wlanapi.lib` import **yok** — 01 D1 kararı: export kümeleri sürüme göre büyür; tek import tablosu XP'de yükleme hatası verir |
| 2 | Handle yaşam döngüsü | `WlanOpenHandle` / `WlanCloseHandle` RAII sarmalayıcı | OpenHandle sürüm nezaketi: WLAN_API_VERSION_1 (XP) ↔ _2 (Vista+) |
| 3 | Bellek sözleşmesi | `WlanAllocateMemory` / `WlanFreeMemory` RAII | Native Wifi çıktısı yalnızca `WlanFreeMemory` ile serbest bırakılır (§5.2) |
| 4 | Bildirim kuyruğu | `WlanRegisterNotification` callback → kilitli SPSC kuyruk | Callback WLAN callback-thread'inde çalışır; ana thread poll eder (§4.2) |
| 5 | Kapanış sırası | Kuyruk drain → notification detach → handle close | `WlanCloseHandle` race gotcha'sı (§4.2) |

### §2.3 L1 wlaenum — sorumluluklar

- `WlanEnumInterfaces`: arayüz listesi (GUID + state).
- `WlanGetAvailableNetworkList` (v1) / `WlanGetAvailableNetworkList2` (List2 — sürüm
  kanıtı §3; ilk public sürümü `⚠️ VERIFICATION REQUIRED`): bilinen ağlar.
- `WlanGetNetworkBssList`: BSS girdileri — **SSID / BSSID / RSSI / PHY**
  (`WLAN_BSS_ENTRY` ve `WLAN_SIGNAL_QUALITY` 0-100 ölçeği; alanlar Native Wifi
  başlığından okunur).
- `WlanScan`: elle tetikli tarama (sonuç bekleme stratejisi §4.3).

### §2.4 L2 wlaconn — sorumluluklar

- `WlanConnect`: `WLAN_CONNECTION_PARAMETERS` — bağlantı modu (profil), hedef SSID
  listesi; profil yoksa opsiyonel bireysel parametre modu.
- `WlanDisconnect`: aktif bağlantıyı düşürme.
- `WlanReasonCodeToString`: `WLAN_REASON_CODE` → okunabilir Türkçe/İngilizce hata
  (tampon boyutu iki adımlı: önce boyut sor, sonra doldur).
- Bağlantı sonucu, L0 bildirim kuyruğundan `connection_complete` /
  `connection_attempt_fail` olaylarıyla tamamlanır (§4.1).

### §2.5 L3 wlaprofile — sorumluluklar

- `WlanSetProfile` (profile XML yaz, `bOverwrite`, reason-code çıkışı),
  `WlanGetProfile`, `WlanDeleteProfile`, `WlanGetProfileList`.
- Profil XML şeması: `WLANProfile` kök · `SSIDConfig/SSID` · `MSM/security`
  (authCipher: WPA2-PSK/AES) · `sharedKey/keyMaterial` (bkz §5.3).
- Düz metin anahtar okuma bayrağı yalnız profil doğrulama/gösterim için; log'a ve JSON
  yanıtına asla yazılmaz (§5.3).

### §2.6 L4 bridge — sorumluluklar

- Mevcut neva-connect WebSocket (9742) protokolünü korur: `{"cmd":"…"}` istek /
  `{"type":"status"|"error"}` yanıt şekli (CLAUDE.md kanıtı, §8).
- `wifi_list` / `wifi_status` isteklerini L1-L2 çağrılarına, L0 kuyruk olaylarını da
  Web UI'a `publish` mesajına çevirir (§4.4).
- Bu katman bilinmez: WLAN çağrısı yapmaz, yalnız JSON ⇄ yerel çağrı köprüsüdür.

---

## §3 Sürüm Uyumu Mimarisi (tek kaynak, çok OS)

### §3.1 Export matrisi özeti (kanıt: 01 dokümanı §3, çift yöntem doğrulama)

pconlife'tan indirilen 10 wlanapi.dll (MD5 doğrulanmış) + yerel Win10 19041 kopyası
üzerinden PE export dizini + REA/Ghidra çapraz kontrolü:

| Sürüm | Wlan* export | XP'ye göre |
|---|---|---|
| XP SP3 (5.1.2600.5512) | **36** | taban |
| Win7 (6.1.7600) x86/x64 | **48** | +12 / −0 |
| Win8 (6.2.9200) x86/x64 | **103** | +67 / −0 |
| Win8.1 (6.3.9600) x86/x64 | **108** | +72 / −0 |
| Win10 10240 x86/x64 | **121** | +85 / −0 |
| Win10 19041 (yerel) x64 | **148** | +112 / −0 |

**Ana bulgu (01 §3):** XP tabanı (36 export) 19041'e kadar **hiçbir export kaybetmiyor**
(her geçişte `−0`). Bu, "tek binary + runtime keşif" mimarisini mümkün kılar.

### §3.2 Çift katman gate — katman A: compile-time

| Yapı | Amaç | Anahtarlar |
|---|---|---|
| XP derlemesi | 36-export tabanına sabitlenmiş başlık/dilim yüzeyi | `_WIN32_WINNT=0x0501`, uyumlu `NTDDI_VERSION`, **v141_xp** araç takımı + SDK 7.1A |
| Modern derleme | Win10 SDK yüzeyi, geniş başlık seti | `_WIN32_WINNT=0x0A00` (veya hedefe göre daha yüksek), **v143** + Win10 SDK |
| Ortak kod | Platformdan bağımsız L0-L4 mantığı | NTDDI şartlandırmaları yalnız derleme-köprüsünde; iş mantığında sürüm testi yok |

Çoklu-toolset gerçeği (v141_xp ile v143 yan yana) 02 derleme dokümanının konusudur
(§8 atıfı).

### §3.3 Çift katman gate — katman B: runtime (GetProcAddress)

01 D1-D2 kararlarının mimari karşılığı:

1. `wlanapi.dll` yalnızca `LoadLibrary` ile yüklenir; gerekli her fonksiyon
   `GetProcAddress` ile çözülür ve tabloya kaydedilir.
2. **Özellik keşfi OS sürümüyle değil, export varlığıyla yapılır** (01 D2): sürüm
   numarası yanıltıcı olabilir (Update paketleri export ekler).
3. Export bulunamazsa çağrı **asla edilmez**; Web UI'a yapılandırılmış yanıt döner:
   `"bu özellik bu Windows sürümünde desteklenmiyor"` (kullanıcıya dönük net mesaj,
   çökme yok).
4. Tek binary XP..Win10 hattında çalışır; XP'ye giden daraltılmış profil yalnız 36
   export tabanına yaslanır.

### §3.4 Düşme (degradation) stratejisi

| Yok / bulunamayan | Düşme davranışı | UI etkisi |
|---|---|---|
| `WlanGetAvailableNetworkList2` | List1'e (`WlanGetAvailableNetworkList`) düş | Liste yine dolar, PHY alanı daralabilir |
| HostedNetwork export'ları (XP'de **yok** — 01 D3) | AP/ad-hoc özelliği yok sayılır | **AP menüsü gizli** |
| WFD / PLAP / StartAP (Win8+ export'ları) | İlgili komut grubu devre dışı | Menü gizli + "desteklenmiyor" mesajı |
| ANQP / hotspot-2.0 (Win10+), FTM/device-service (19041+) | Özellik bayrağı `features[]` dizilmez | UI o seçeneği hiç çizmez |
| Bildirim türü (örn. XP'de desteklenmeyen ACM olayı, §4.3) | Periyodik polling'e düş | Yenileme süresi artar, veri kaybı yok |

---

## §4 Veri Akışı

### §4.1 Bağlantı durumu akışı (wifi status)

```text
WlanRegisterNotification (ana callback kaydı — L0)
   │  wlan_notification_acm_connection_complete
   │  wlan_notification_acm_disconnection
   ▼
WLAN_NOTIFICATION_DATA (callback thread — hızlı, bloklamayan)
   │  yalnız kuyruğa yaz (lock-free SPSC enqueue)
   ▼
kilitli SPSC kuyruk (LRING — §4.2)
   ▼
ana thread poll (periyodik drain)
   ▼
L4 bridge: {"type":"wifi_status", …} publish
   ▼
WebSocket :9742 → Web UI durum rozeti
```

**XP kısıtı:** MSDN XP kısıt kaynaklarına göre `wlan_notification_acm` kümesi XP'de
**yalnızca** `connection_complete` ve `disconnection` ile sınırlıdır (dispatch
yönergesi; XP MSDN teyidi bu oturumda yapılmadı → `⚠️ VERIFICATION REQUIRED`).
Mimari bu iki olayı "en düşük ortak paydaya" kurar; fazlası §3.4 düşme zincirine girer.

### §4.2 Kilitli kuyruk + WlanCloseHandle race gotcha'sı

Bilinen tuzak (kanıt: `E:\…\wireless-connect\AGENTS.md` "Gotcha 2: Network Interface
Still in Use"): `WlanCloseHandle` çağrıldığında arayüz hâlâ serbest bırakılmamış
olabilir; eşzamanlı bildirim/callback akışı varken close + yeniden open yarışa girer.

Tasarım kuralı (bu node):

1. Callback **yalnız** kuyruğa yazar; WLAN handle'ına dokunmaz, bloklamaz.
2. Kuyruk tek yazarlı (callback thread) / tek okuyuculu (ana thread) **SPSC**'dir;
   `AcquireSRWLock` ailesi kuyruk baş/son meta verisi içindir — **SRWLock Vista+
   primitive'idir; XP derlemesinde CRITICAL_SECTION'a düşer** (compile-time gate §3.2).
3. Kapanış sırası zorunlu: `WlanRegisterNotification(..., NULL)` (notify disconnect) →
   kuyruk drain → callback'in biteceğini garantile (araç takımı seviyesinde join/
   senkron bekleme) → ancak `WlanCloseHandle` → handle=null.
4. wireless-connect kalıbı korunur: kilit altında thread join + gerekirse kısa
   bekleme (grace period), yeniden açılışta "önceki kapanış tamamlandı" kontrolü.
5. Hızlı start/stop döngüsü testi zorunlu (rapid scan-wifi döngüsü: asma/çökme yok).

### §4.3 Tarama akışı (wifi scan)

```text
kullanıcı/Web UI "scan" isteği (L4)
   ▼
L1: WlanScan(arayüz)                       ← istek atılır
   ▼
sonuç bekleme stratejisi:
   • Vista+ : wlan_notification_acm_scan_complete beklenebilir
   • XP     : scan_complete bildirimi XP'de var mı → ⚠️ VERIFICATION REQUIRED
              (MSDN XP teyidi gerek; "var" varsayılmaz)
   ▼
fallback (XP ve bildirimsiz tüm durumlar):
   periyodik WlanGetAvailableNetworkList polling (ör. 1-2 s aralık,
   üstel geri çekilme ile 8 s'e kadar; neva-connect < 5 s yeniden
   bağlanma hedefiyle uyumlu)
   ▼
L1: WlanGetNetworkBssList → SSID/BSSID/RSSI/PHY + connectable + reason
   ▼
L4: {"cmd":"wifi_list", networks:[…], features:[…]} publish
```

### §4.4 WebSocket publish akışı

- `wifi_status`: bağlantı olayları tetikler (push) + Web UI açılışında tek istekle
  snapshot.
- `wifi_list`: istek-cevap (snapshot) + scan sonrası publish.
- `features[]`: süreç başlangıcında bir kez keşfedilir (§3.3) ve her yanıta eklenir;
  Web UI bu diziye bakar, sürüm sormaz (D2 prensibi).

### §4.5 Kopma & yeniden bağlanma akışı (bağlantı durumu tutarlılığı)

```text
connection_complete bildirimi (L0 kuyruk)
   ▼
ana thread: aktif SSID/BSSID güncelle → "connected" durumu L4'e publish
   ▼
bekleme durumu: sinyal izleme (periyodik BSS listesi, §4.3 polling)
   ▼
disconnection bildirimi geldi (planlı ya da kopma):
   • planlı (wifi_disconnect): durum → "disconnected", publish, bekle
   • plansız kopma: durum → "reconnecting", neva-connect < 5 s hedefiyle
     eşleşen yeniden deneme (üstel geri çekilme 1 s → 8 s tavan)
   ▼
yeniden bağlama stratejisi: son bilinen profil (L3, varsa) ile
   otomatik WlanConnect → başarılıysa "connected" publish
   → başarısızsa "failed + reason" publish (WlanReasonCodeToString)
```

Tutarlılık kuralı: bağlantı durumu **tek kaynaktan** (ana thread durum makinesi)
yayınlanır; L0 kuyruk olayları bu makineye girdi verir, doğrudan publish etmez
(çift yayın/çelişki yok).

---

## §5 Güvenlik & Sağlamlık


### §5.1 Handle sızıntısı yok (RAII)

- Tüm WLAN handle'ları RAII sarmalayıcıdadır; erken `return`/exception yollarında
  `WlanCloseHandle` garantilenir.
- Kopyalama kapalı, taşıma serbest (neva-connect L1 RAII standardı: "No copy, only
  move").

### §5.2 WlanFreeMemory zorunlu

- Native Wifi'nin döndürdüğü tüm tampon listeler (`WLAN_INTERFACE_INFO_LIST`,
  `WLAN_AVAILABLE_NETWORK_LIST`, `WLAN_BSS_ENTRY` blokları, reason-code tamponu)
  **yalnız** `WlanFreeMemory` ile serbest bırakılır; `free`/`delete` yasak.
- XP SP2 bağlamında WLAN tampon serbest bırakma kaynaklı bilinen sızıntı senaryosu
  atıfı: **KB940541** (Microsoft KB/MSDN: WlanGetAvailableNetworkList sayfasındaki
  hotfix notu — "FIX: The private bytes of your application continuously increase…"
  makale 940541; 03 dokümanı §3'te çapraz doğrulandı 2026-10-15). İddia nezdinde
  geçerli olan mühendislik kuralı: sızıntıyı önlemek için tek giriş noktası
  (`wla_mem` RAII) ve XP test ucu zorunluluğudur.

### §5.3 Profil XML güvenliği

- Şablonlar yalnız **WPA2-PSK/AES** üretir (WEP/WTAP-açık yapılandırma üretilmez).
- `keyMaterial` (düz metin anahtar) yalnızca profil yazımı/okunması süreç içindedir:
  log dosyasına, `wifi_list` JSON'una ve hata mesajlarına **asla** yazılmaz
  (CoreMusic secret kuralı ile aynı prensip).
- Profil XML'i kullanıcı/SSID girdisi içerir → `WlanSetProfile` öncesi XML enjeksiyon
  yüzeyine karşı girdi doğrulama (SSID uzunluğu 0-32 bayt, karakter kaçışları).

### §5.4 Admin gerektiren işlemler

- `WlanSetProfile` ve bazı profil işlemleri yönetici ayrıcalığı ister; başarısızlıkta
  `ERROR_ACCESS_DENIED` döner → L4 bunu kullanıcıya çevrilmiş mesaja ("bu işlem için
  yönetici gerekli") çevirir, çökme yok.
- Vista+ profillerde tüm-kullanıcı (all-user) ile kullanıcı-kapsamı ayrımı vardır;
  XP'de yalnız sistem kapsamlı profil vardır (sürüm farkı §3.4 düşme zincirinde
  yorumlanır, kod yolu ayrılmaz).

### §5.5 Çoklu-EXE izolasyonu

- `neva-wlan.exe` çökerse yalnız WLAN köprüsü ölür; `neva-bt.exe` /
  `neva-usb.exe` / `neva-storage.exe` etkilenmez (kullanıcı direktifinin gerekçesi).
- Her exe kendi L0 resolver'ını yükler; wlanapi.dll handle'ları süreçler arası paylaşılmaz.
- Süreç-crash durumunda Web UI, bridge koptu hatası alır ve reconnect döngüsüne girer
  (neva-connect yeniden bağlanma kültürüyle uyumlu).

### §5.6 XP WZC (Wireless Zero Configuration) bağımlılığı

- XP'de WLAN yönetimi `wzcsvc` (WZC) servisine bağlıdır; üçüncü taraf sürücü
  yöneticileri WZC'yi devre dışı bırakabilir → `WlanEnumInterfaces` boş liste ya da
  `WlanOpenHandle` hatası döner.
- Tasarım: süreç başlangıcında servis/durum tespiti, kullanıcıya "WZC kapalı / sürücü
  yönetimi devralmış" mesajı; yeniden deneme gecikmeli.

### §5.7 Sağlamlık test kapıları (kod fazları öncesi kabul kriteri)

| # | Kapı | Ölçüt |
|---|---|---|
| 1 | Handle/bellek sızıntısı | XP SP3 + Win10 19041 uçlarında 1000× enum+scan döngüsünde WLAN handle/bellek sayımı sabit |
| 2 | Rapid start/stop | `wifi_scan` zincirleme 50×: asma, çökme, `ERROR_INVALID_HANDLE` yok |
| 3 | Kilitli kuyruk yarışı | Bilinçli hızlandırılmış close/open döngüsü altında sanitizer (ASan/DR) temiz |
| 4 | Degrade yolları | List2→List1, HostedNetwork gizleme, "desteklenmiyor" mesajı her XP/Win7 ucunda gözlemlenir |
| 5 | Admin reddi | Yöneticisiz süreçte `WlanSetProfile` → `ERROR_ACCESS_DENIED` → çevrilmiş kullanıcı mesajı |

---

## §6 Arayüz Sözleşmeleri


### §6.1 EXE'ler arası IPC seçenekleri (karar: AÇIK — §9)

| Seçenek | XP uyumu | Artıları | Eksileri |
|---|---|---|---|
| Named pipe (`\\.\pipe\neva-*`) | **XP'de var** (CreateNamedPipe ailesi) | Yerel, izin kontrolü (güvenlik tanımlayıcısı), streaming | Senkronizasyon/paketleme elle |
| Lokal WebSocket (127.0.0.1) | Uygulama katmanı bağımlılığı (XP'de WinHTTP WebSocket yok; kütüphane taşınır) | L4 protokolüyle aynı dil, Web UI ile aynı kod yolu | Ek kütüphane + TLS/handshake yükü gereksiz olabilir |
| Konsol çıktısı (stdout JSON) | En ilkel, tam uyumlu | Sıfır bağımlılık, hata ayıklama kolay | Gerçek zamanlı bidirectional zor; production'a dar |

Bu tablo kararı vermaz; seçim §9 açık maddesidir (named pipe'ın XP uyumu not edilmiştir).

### §6.2 JSON şeması taslağı (L4 ⇄ Web UI)

```json
{
  "cmd": "wifi_list",
  "networks": [
    {
      "ssid": "CoreMusic-Studio",
      "bssid": "00:11:22:33:44:55",
      "rssi": -52,
      "phy": "802.11ac",
      "connectable": true,
      "reason": 0
    }
  ],
  "os": "win7",
  "features": ["list2", "hostednetwork", "wfd"]
}
```

Alan sözleşmesi (taslak):

| Alan | Tip | Not |
|---|---|---|
| `ssid` | string | 0-32 bayt; JSON'a yazılırken UTF-8'e çevrilir |
| `bssid` | string | `AA:BB:CC:DD:EE:FF` biçimi (wireless-connect adres sırası gotcha'sı: parse sırası ≠ display sırası) |
| `rssi` | int | dBm (BSS girdisi) + varsa `quality` 0-100 |
| `phy` | string | PHY etiketi (`WLAN_BSS_ENTRY`/arayüz bilgisinden) |
| `connectable` | bool | `WLAN_AVAILABLE_NETWORK` bayrağı |
| `reason` | int | `WLAN_REASON_CODE` (0 = sorun yok; `WlanReasonCodeToString` çevirisi sunucu tarafında) |
| `os` | string | tespit edilen sürüm etiketi (`"xp"`/`"win7"`/`"win10"`) — yalnız gösterim, karar `features[]` ile verilir |
| `features[]` | string[] | runtime keşif sonucu (§3.3); Web UI menüleri buna göre çizilir |

### §6.3 Komut seti taslağı (L4)

| `cmd` | Yön | Etki |
|---|---|---|
| `wifi_list` | UI → bridge | Anlık ağ listesi snapshot'ı |
| `wifi_scan` | UI → bridge | `WlanScan` + §4.3 bekleme + publish |
| `wifi_connect` | UI → bridge | Profil seçimi/şifre → L3 profil + L2 `WlanConnect` |
| `wifi_disconnect` | UI → bridge | `WlanDisconnect` |
| `wifi_status` | UI → bridge | Bağlantı durumu snapshot'ı |
| `wifi_profile_list/add/delete` | UI → bridge | L3 profil yaşam döngüsü |
| `wifi_status` push | bridge → UI | `connection_complete`/`disconnection` olay sonrası publish |

### §6.4 Hata sözleşmesi (taslak)

| Seviye | Alan | Örnek |
|---|---|---|
| WLAN reason | `reason` (int) + `reasonText` | `4097` + "802.11 işlemsiz bağlantı reddi" |
| API hatası | `apiError` (int GetLastError) | `5` (ERROR_ACCESS_DENIED) → "yönetici gerekli" |
| Özellik yok | `unsupportedFeature` (string) | `"hostednetwork"` → "bu Windows sürümünde desteklenmiyor" |
| Bridge koptu | `type:"error"`, `source:"bridge"` | EXE çökmesi/kapanışı (§5.5) |

Prensip: hata **asla sessiz yutulmaz**; her negatif sonuç ya kullanıcıya çevrilmiş
metin ya `features[]` eksiltme üretir (CoreMusic zero-hallucination ruhuyla:
"desteklenmiyor" = bilinen durum, bilinmeyen durum ≠ hata).

---

## §7 OS Matrisi Özeti


| OS | wlanapi export (kanıt) | API sürümü | Derleme hedefi | Runtime keşif | Not |
|---|---|---|---|---|---|
| XP SP3 | **36** (01 §3) | v1 | v141_xp + SDK 7.1A | yalnız 36-export tabanı | HostedNetwork yok → AP gizli · WZC servis bağımlı · bildirimler `⚠️ VERIFICATION REQUIRED` kısıt altında |
| Win7 | 48 | v2 | v143 + Win10 SDK | + HostedNetwork (11 export) | İlk AP barındırma yeteneği |
| Win8 | 103 | v2 | v143 + Win10 SDK | + WFD/PLAP/StartAP/LowPriv… | List2 varlığı `⚠️ VERIFICATION REQUIRED` (ilk public sürüm) |
| Win8.1 | 108 | v2 | v143 + Win10 SDK | + 5 yardımcı export | Küçük artı |
| Win10 (10240 / 19041) | 121 / **148** | v2 | v143 + Win10 SDK | + ANQP, FTM, device-service, non-disruptive scan | "Win10 son" referansı = yerel 19041 kopyası (MD5 doğrulandı) |
| Win11 | **UNKNOWN** | UNKNOWN | UNKNOWN | UNKNOWN | pconlife OS listesinde yok; export seti bilinmiyor (§9) |

---

## §8 İlişkili Dokümanlar

| Doküman | Rol | Disk durumu (bu oturum ölçümü) |
|---|---|---|
| `.ai/projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani.md` | Export matrisi + Windows katman planı (bu node'un birincil kanıtı) | ✅ mevcut (okundu) |
| `02-coklu-exe-windows-sdk-derleme-mimarisi.md` (çok-exe + derleme matrisi) | EXE ayrıştırma gerekçesi, v141_xp/v143 derleme | ✅ mevcut (769 satır, 2026-10-15 yazımı — paralel oturum, bu node ile aynı gün) |
| `03-wlan-api-ozellik-kisitlama-tablosu.md` (feature-gate) | Export-varlığına dayalı özellik kapısı detayları | ✅ mevcut (678 satır) |
| `04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama.md` (kapsam yol haritası) | Faz planı kapsamlı hali | ✅ mevcut (1129 satır) |
| `05-neva-wlan-cekirdek-modul-tasarimi.md` (çekirdek modül) | L0-L4 akışlar, wlabase tablosu (felaket yolu dahil), WS 9742 JSON, Level hata tablosu | ✅ mevcut (666+ satır; §2.1/§9 2026-10-15 felaket yolu ile güncellendi) |
| `06-test-ve-kalite-plani.md` (test & kalite) | OS×modül matrisi, 45 birim, X-satırı, N1-N9 negatif, §7.3 T-01…T-12 felaket yolu | ✅ mevcut (560+ satır; §7.3/X4b/N9 2026-10-15 eklendi) |
| `07-xp-wzc-stack-kaynak-arastirmasi.md` (XP WZC kaynağı) | XP SP1'de wlanapi YOK → WZC stack; NDISUIO OID kanıtları; 09'un birincil kaynağı | ✅ mevcut (500 satır; §2.2 NDISUIO düzeltmesi 2026-10-15) |
| `08-xp-panel-davranis-sozlesmesi.md` (XP panel sözleşmesi) | lanui kaynağından XP panel durum/mesaj/akış çıkarımı; WS 9742 eşleme 22 satır | ✅ mevcut (595 satır) |
| `09-xp-ndisuio-felaket-yolu-tasarimi.md` (felaket yolu) | WZC yokken NDISUIO OID: `\\.\Ndisuio`, 5 IOCTL, karar akışı, T-01…T-12 | ✅ mevcut (554 satır) |
| `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` | `WlanCloseHandle` race gotcha'sı + parser/CLI standartları | ✅ mevcut (okundu) |
| `E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md` | Mevcut L0-L3 + WebSocket 9742 protokolü | ✅ mevcut (okundu) |
| `E:\…\cpp-projects\CLAUDE.md` (kök) | vcpkg + MSVC2022 + ctest standardı (01 §4.3 atfı) | ⚠️ okunmadı; 01 dokümanındaki atıfla anıldı |

---

## §9 Açık Sorular

1. **Win11 wlanapi.dll export seti** → `UNKNOWN` (pconlife'ta Win11 yok). Alternatif
   kaynak (kullanıcıdan DLL veya güvenilir arşiv) gerekiyor; §7 satırı dolana kadar
   Win11 "UNKNOWN" olarak işaretli kalır.
2. **BT sürücü değiştirme kapsamı ve sürücü yazımı** → `⚠️ VERIFICATION REQUIRED`
   (kullanıcı onayı). `neva-bt.exe` bu node'un içinde tasarlanmaz; sürücü katmanı
   ayrı vault node'u gerektirir.
3. **EXE'ler arası IPC seçimi** → açık karar (§6.1 tablosu): named pipe (XP uyumlu) ⇄
   lokal WebSocket ⇄ konsol JSON. Üretim kararı kullanıcı onayı ister.
4. **XP hedef onayı** → XP SP3 gerçekten hedef mi? (01 §6.4: 36-export dar kırpması iş
   yükü ekler; Win7+ başlangıç alternatifi.) Bu node XP'yi "mümkün" olarak tasarlar,
   "zorunlu" olarak varsaymaz.
5. **XP'de `wlan_notification_acm_scan_complete` var mı** → `⚠️ VERIFICATION REQUIRED`
   (MSDN XP teyidi). Var sayımaz; §4.3 fallback zinciri varsayılan yoldur.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynaklar:** 01 export matrisi (REA/Ghidra 12.1.4 + PE parser çift yöntem) · Microsoft Learn Native Wifi API · wireless-connect AGENTS.md · neva-connect CLAUDE.md