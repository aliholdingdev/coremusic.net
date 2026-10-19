---
title: "Neva Connect — neva-wlan.exe Çekirdek Modül Tasarımı (wifi list · connect · status · profile)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "01/02/03 NevaConnect dokümları + Microsoft Learn Native Wifi + neva-connect/wireless-connect proje kuralları"
updated: 2026-10-15
---

# Neva Connect — neva-wlan.exe Çekirdek Modül Tasarımı (wifi list · connect · status · profile)

> **Bu doküman kod değil, tasarımdır (status: plan).** Her Windows API iddiası §10'da
> kaynaklıdır; doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED`, bilinmeyen değer
> `UNKNOWN` olarak işaretlenmiştir (Zero-Hallucination — kök AGENTS.md §3).
> Commit bu subagent'a ait DEĞİLDİR — commit kararı orkestratöre aittir.

---

## §1 Kapsam

**neva-wlan.exe**, Neva Connect Windows çekirdek modulüdür; Native Wifi API
(wlanapi.dll) üzerinden dört temel işi WebSocket 9742 üzerinden JSON sözleşmesiyle
sunar: **wifi list · wifi connect · wifi status · wifi profile** (artı bağlı
komutlar `wifi_scan`, `wifi_disconnect`, `wifi_profiles_*`).

### §1.1 Kapsam içi / kapsam dışı

| Kapsam içi | Kapsam dışı (04 doc F3–F5) |
|---|---|
| `wifi_list` → WlanEnumInterfaces + WlanGetAvailableNetworkList | Bluetooth (neva-bt, 04 §2) |
| `wifi_scan` → WlanScan + bildirim/timing | USB depolama (neva-usb, 04 §2) |
| `wifi_status` → WlanQueryInterface (current_connection) | Hotspot/HostedNetwork (01 §K33 — ayrı F2) |
| `wifi_connect` / `wifi_disconnect` → profil + WlanConnect | Wi-Fi 7 (F2 EXE, 02 §5.2 L1 satırı) |
| `wifi_profiles_list/get/delete/reorder` → profil listesi işlemleri | NDIS/kernel sürücü (02 §5.1 **Level 0 yasağı**) |
| WS 9742 JSON kapı + Level 0–3 hata eşlemesi | RADIUS/802.1X enterprise kimlik doğrulama (yalnız profil XML geçişi) |

### §1.2 İşletim sistemi kapsamı

| OS | Konum | Dayanak |
|---|---|---|
| Windows 10 / 11 | Birincil hedef | 01 doc export matrisi (Win10 19041 = 148 export) |
| Windows 7 / 8.1 | İkincil | 01 doc; `WLAN_PROFILE_GET_PLAINTEXT_KEY` Win7+ [S11] |
| Windows XP SP3 / SP2 Wireless LAN API | Kısıtlı (beyaz liste 20 fonksiyon) | 03 §3.1 [K1] · MS kısıtlama listeleri [S*] |
| Windows Server 2008 (WLANSVC kurulu değilse) | `WlanEnumInterfaces` örnekleri başarısız — L2 | [S13], [S14] |

### §1.3 Görev brief'indeki 3 DÜZELTME (kaynaklı, bağlayıcı)

Brief'teki şu ifadeler Microsoft dokümanlarıyla **çelişmektedir**; bu tasarım
aşağıdaki doğrulanmış hali kullanır:

1. **`WLAN_CONNECTION_PARAMETERS` içinde auth/cipher alanı YOKTUR.** Yapı yalnızca
   `wlanConnectionMode · strProfile · pDot11Ssid · pDesiredBssidList · dot11BssType · dwFlags`
   taşır [S9]; authentication/encryption değerleri **profil XML'inin** içindedir
   (`authEncryption`) [S16]. Brief'teki "dot11AUTH auto / dot11Ciph auto" ifadesi
   geçersizdir.
2. **`WLAN_PROFILE_PLAINTEXT_KEY_*` bayrağı `WlanSetProfile` için YOKTUR.** Doğru
   isim **`WLAN_PROFILE_GET_PLAINTEXT_KEY`** olup girdi bayrağıdır ve
   `WlanGetProfile`'de kullanılır (Win7+; yönetici varsayılanı; verilmezse hata
   değil, şifreli anahtar döner) [S11]. `WlanSetProfile`'de `dwFlags` XP'de 0 olmak
   zorundadır [S3]; XP'de yazılan düz metin anahtar **hiçbir zaman şifrelenmez** [S3].
3. **XP'de `dwClientVersion`** 1'dir (XP istemci sürümü), 2 = Vista/Server 2008 [S12].

---

## §2 EXE ASCII Mimarisi

```text
                         neva-wlan.exe  (tek süreç · MSVC2022 · x86/x64)
 ┌───────────────────────────────────────────────────────────────────────────┐
 │  app  ── main(): COMBI-init → WS dinle(9742) → komut döngüsü → shutdown   │
 │    │                                                                     │
 │    │  [Ana iş parçacığı — WS reader/çözücü]                               │
 │    │   wslbridge ── WebSocket 9742 (handshake → cmd → result → push)     │
 │    │        │ JSON → Command struct → dispatcher                          │
 │    │        ▼                                                             │
 │    │   wlaconn   : wifi_connect / wifi_disconnect / reason-code çevirisi │
 │    │   wlaenum   : wifi_list / wifi_scan (+ arayüz seçimi)               │
 │    │   wlaprofile: profil XML üretimi + set/get/delete/position          │
 │    │   wlacore   : HANDLE RAII (Open/Close, negotiated version)          │
 │    │   wlabase   : DynamikLoader — LoadLibrary("wlanapi.dll")            │
 │    │                + GetProcAddress tablosu + XP beyaz liste kapısı      │
 │    ▼                                                                     │
 │  [Bildirim iş parçacığı]                                                 │
 │   WlanRegisterNotification(ACM|...) → callback → SPSC kuyruk (≤256)     │
 │        └─ yalnız kuyruğa yazar; WS üzerine ana iş parçacığı yayınlar    │
 │  [Zamanlayıcı iş parçacığı]                                             │
 │   scan timeout(4s) · connect timeout(config) · 2s status polling         │
 │  [Kapanış sırası]                                                        │
 │   unregister → kuyruk drain → join → WlanCloseHandle → 100ms grace       │
 └───────────────────────────────────────────────────────────────────────────┘
        ▲ JSON over WebSocket 127.0.0.1:9742
        │  (handshake {"type":"handshake","apiVersion":2,"clientType":"spa"}
        │   neva-connect/CLAUDE.md §WebSocket — protokol uyumu zorunlu)
   CoreMusic SPA / Neva Connect daemon
```

### §2.1 Modül sorumlulukları (01 doc §katman planı ile birebir)

| Modül | Dosya (plan) | Sorumluluk | Doğrudan çağırdığı API'ler |
|---|---|---|---|
| **wlabase** | `wlabase/{dynload,tablo,xp_gate,xp_ndisuio_fallback}.*` | `LoadLibraryW(L"wlanapi.dll")` + `GetProcAddress`; XP beyaz liste (20 fn) + dönüş kodu kapısı (03 §2.4); WZC yokken NDISUIO OID **felaket yolu** (09 doc §5.2-A) | — (yalnız loader + XP felaket transport'u 09) |
| **wlacore** | `wlacore/handle_raii.*` | Handle yaşam döngüsü, `dwClientVersion` seçimi (XP=1), negotiatedVersion saklama | WlanOpenHandle · WlanCloseHandle · WlanFreeMemory |
| **wlaenum** | `wlaenum/{interfaces,scan,list}.*` | Arayüz enum, tarama, ağ listesi | WlanEnumInterfaces · WlanScan · WlanGetAvailableNetworkList |
| **wlaprofile** | `wlaprofile/{xml_builder,ops}.*` | WLANProfile XML üretimi (şema sırası), CRUD + sıra | WlanSetProfile · WlanGetProfile · WlanGetProfileList · WlanDeleteProfile · WlanSetProfilePosition |
| **wlaconn** | `wlaconn/{connect,disconnect,reason}.*` | Bağlantı kurma/koparma, reason-code çevirisi | WlanConnect · WlanDisconnect · WlanReasonCodeToString |
| **wslbridge** | `wslbridge/{server,proto,json}.*` | WS 9742, JSON sözleşmesi, kuyruk → push | — |
| **app** | `app/{main,dispatch,notify,shutdown}.*` | İş parçacıkları, dispatcher, kapanış sırası | WlanRegisterNotification (bildirim) |

---

## §3 İş Parçacığı Modeli

### §3.1 İş parçacıkları

| # | İş parçacığı | Görev | Yasaklar |
|---|---|---|---|
| T1 | **main / WS** | WS accept, JSON çözümleme, komut yürütme, yanıt/push yazma | WLAN API'yi bu iş parçacığı tekelleştirir (handle sahipliği T1'de) |
| T2 | **notify** | `WlanRegisterNotification` callback → SPSC kuyruğa olay yazma | **Bloklayan çağrı yok**: ağ API'si, allocation'ı sınırlı, WS yazma yok |
| T3 | **timer** | scan timeout (4s), connect timeout (config `connectTimeoutMs`, varsayılan 15000 — tasarım parametresi, API iddiası değildir), 2s status polling | Bağımsız tetik, kuyruğa "timeout" olayı atar |

### §3.2 Bildirim callback kuralları (kaynaklı)

- **Deadlock yasağı:** `WlanRegisterNotification` sayfası, callback içinden bu fonksiyonun
  tekrar çağrılmasının deadlock'a yol açabileceğini belirtir → callback içinde Native Wifi
  çağırmak **yasak** [S1].
- **XP kapsamı:** XP'de yalnız `wlan_notification_acm_connection_complete` ve
  `wlan_notification_acm_disconnected` bildirimleri gelir (03 §3.1 K8; [S1], [S2]) →
  XP'de `scan_complete` beklentisi kurulamaz.
- **Callback hangi iş parçacığında çalışır?** MSDN bunu açıkça "farklı iş parçacığı"
  diye nitelemiyor; eldeki kanıt yalnız `WlanRegisterNotification` deadlock notu +
  Microsoft Q&A anlatımıdır → **⚠️ VERIFICATION REQUIRED** (tasarım, callback'i her
  koşulda farklı bir yığın/iş parçacığı varsayar ve T2'yi accordingly isolates eder).
- **Kapanışta otomatik unregister:** `WlanCloseHandle` çağrıldığında kayıtlı
  bildirimler geri alınır (auto-undo) [S1] → yine de açıkça unregister edilir
  (`WLAN_NOTIFICATION_SOURCE_NONE` + NULL callback) [S1].

### §3.3 SPSC kuyruk (lock-free, tek üretici T2 → tek tüketici T1)

| Parametre | Değer | Gerekçe |
|---|---|---|
| Kapasite | **256 olay** (power-of-two) | §7 bütçe; taşma → en eski olay düşür + `droppedEvents` sayacı |
| Desen | SPSC ring buffer (head/tail atomik, `memory_order_release/acquire`) | Kilit yok; callback içinde bile güvenli |
| Olay tipleri | `acm_connection_complete` · `acm_disconnected` · `scan_complete` · `timeout` · `interface_removed` | XP'de ilk ikisi zorunlu [S1] |
| Payload | Sabit boy `WLAN_NOTIFICATION_DATA` kopyası (bellek kopyalama, işaretçi DEĞİL) | Callback döndükten sonra kaynak geçersizleşir — **kaynak: MSDN callback parametresi bildirim sırasında geçerli** [S2] |

### §3.4 Kapanış sırası (yarış engeli — zorunlu)

```text
1. T3 timer'ı durdur (timeout bayrakları kapat)
2. WlanRegisterNotification(SOURCE_NONE, NULL)  → auto-undo ile çakışmasın diye açıkça [S1]
3. Kuyruk drain (T1 son olayları işlesin) → drain bayrağı
4. T2/T3 join()  (wireless-connect Gotcha 2: lock + join + close) [D2]
5. WlanCloseHandle(handle); handle = nullptr
6. 100 ms grace (Windows arayüzü bırakması için) [D2]
```

> **Kaynak (disk):** `wireless-connect/AGENTS.md` Gotcha 2 — "Network Interface Still in
> Use (Race Condition)": kilit altında `join` → `WlanCloseHandle` → 100 ms bekleme
> doğru kalıptır [D2]. Hatalı kalıp: senkronizasyonsuz `WlanCloseHandle` + hemen yeniden
> open → tarama/bağlantı hataları [D2].

---

## §4 Çekirdek Akışlar (Adım Adım)

> Her akışta **BAŞARI** tablosu (adım · API · beklenen dönüş · kanıt) ve
> **ÇÖKME-KAPANIŞ** tablosu (adım · hata · seviye · süreç davranışı) vardır.
> Seviye sözlüğü: 02 doc §5.1 (Level 0–3) — süreç yalnız Level 3'te kapanır (exit 70).

### §4.1 wifi_list

**Adımlar:**

1. **Yükleme (wlabase):** `LoadLibraryW(L"wlanapi.dll")` → `GetProcAddress` tablosu.
   XP'de çağrı yalnız 03 §3.1 beyaz listesindeki 20 fonksiyona izin verir; kapı = export
   VAR + dönüş kodu 0 değilse `FeatureNotSupported` (03 §2.4).
2. **Handle (wlacore):** `WlanOpenHandle(dwClientVersion = 2, NULL, &negotiatedVersion, &h)`;
   XP derlenmiş artefaktta `dwClientVersion = 1` [S12]. `pdwNegotiatedVersion` yanıtın
   `negotiatedVersion` alanına yazılır [S12].
3. **Arayüz enum (wlaenum):** `WlanEnumInterfaces(h, NULL, &pIfList)` → GUID +
   `strInterfaceDescription` + `isState` [S13]; dönüşte bellek `WlanFreeMemory` [S13].
4. **Ağ listesi:** `WlanGetAvailableNetworkList(h, &guid, 0, &pList)` — **`dwFlags = 0`
   XP'de zorunlu** (adhoc/gizli ağ bayrakları XP'de `ERROR_INVALID_PARAMETER` verir) [S4].
   Hata `ERROR_NDIS_DOT11_POWER_STATE_INVALID` → **radyo kapalı** [S4].
5. **Dönüştür:** her `WLAN_AVAILABLE_NETWORK` → `{ssid, bssid?, signalQuality, bssType,
   securityFlags, connected}`; `wlanSignalQuality` (0–100) RSSI gerektiren alanlar için
   `RSSI = -100 + (q/2)` formülüyle türetilir [S4].
6. **Serbest bırak:** `WlanFreeMemory` (Native Wifi dönüşlerinin tamamı zorunlu) [S5].

| BAŞARI | Adım | API | Beklenen | Kanıt |
|---|---|---|---|---|
| ✅ | 1 | LoadLibrary/GetProcAddress | tüm semboller ≠ NULL | [S*] §10 (03 gate) |
| ✅ | 2 | WlanOpenHandle | `ERROR_SUCCESS`, negotiated ≥ 1 | [S12] |
| ✅ | 3 | WlanEnumInterfaces | `ERROR_SUCCESS`, ≥1 arayüz | [S13] |
| ✅ | 4 | WlanGetAvailableNetworkList | `ERROR_SUCCESS`, `dwNumberOfItems ≥ 0` | [S4] |
| ✅ | 5–6 | map + WlanFreeMemory | JSON `ok:true`, liste boşsa `data.networks:[]` | [S5] |

| ÇÖKME-KAPANIŞ | Adım | Hata | Seviye | Süreç |
|---|---|---|---|---|
| 🔴 | 1 | `wlanapi.dll` yok | **L1** "Kablosuz arayüz kütüphanesi bulunamadı" (02 §5.2) | **Ayakta** — sürüm bilgisi yazılır |
| 🔴 | 2 | `WlanOpenHandle` başarısız (XP: WZC çalışmıyor) [S12] | **L2** retry 3×2 sn → durum raporu (02 §5.2) | Ayakta |
| 🔴 | 3 | 0 arayüz | **L2** "Kablosuz adaptör bulunamadı" — 10 sn sonra bilgi (02 §5.2) | Ayakta |
| 🔴 | 4 | `ERROR_NDIS_DOT11_POWER_STATE_INVALID` | **L2** "Kablosuz radyo kapalı" [S4] | Ayakta |
| 🔴 | 4 | `ERROR_INVALID_PARAMETER` (XP, bayraklı istek) | **L2** — retry `dwFlags=0` ile | Ayakta |
| 🔴 | 6 | serbest bırakma unutulursa | L2 bellek sayacı → L3 | Ayakta / log |
| ⚫ | herhangi | C++/SEH istisna | **L3** log + **exit 70** (02 §5.2) | **Kapanır** (yalnız bu EXE) |

### §4.2 wifi_scan

**Adımlar:**

1. `WlanScan(h, &guid, pDot11Ssid, pIeData, NULL)` — **XP'de `pDot11Ssid` ve
   `pIeData` NULL olmalıdır** [S13a].
2. **Tarama tamamlanma bekleme:**
   - **Vista+:** ACM `wlan_notification_acm_scan_complete` gelene kadar bekle —
     **sınır 4 sn** (logo zorunluluğu: sürücü taramayı 4 sn'de bitirmek zorunda) [S13a].
   - **XP:** "WlanScan returns immediately and **does not provide a notification** when
     the scan is complete" [S13a] → `scan_complete` YOK; tasarım T3 timer ile **4 sn
     sabit bekleme** uygular, XP tarama süresi `UNKNOWN`'dır (açık konu §9.3).
3. `WlanGetAvailableNetworkList(2)` (XP'de yoksa normal `WlanGetAvailableNetworkList`
   kullanılır — `...List2` XP beyaz listesinde DEĞİL, 03 §2.5/§3.1) [S6].
4. JSON `wifi_list` ile aynı şema + `scanId`, `elapsedMs`.
5. Not: WLANSVC varsayılan 60 sn'de bir tarama tetikler; bağlıyken hiç taramayabilir —
   uygulama tarama peşindeyse `WlanScan` + ACM kaydı kullanmalıdır [S13a].

| BAŞARI | Adım | Beklenen | Kanıt |
|---|---|---|---|
| ✅ | 1 | `ERROR_SUCCESS` | [S13a] |
| ✅ | 2 | `scan_complete` ≤ 4000 ms (Vista+) / 4000 ms sabit bekleme (XP) | [S13a] |
| ✅ | 3–4 | taze liste, `ok:true` | [S6] |

| ÇÖKME-KAPANIŞ | Adım | Hata | Seviye | Süreç |
|---|---|---|---|---|
| 🔴 | 1 | `ERROR_INVALID_PARAMETER` (XP'de SSID verilmişse) | **L2** — parametreleri XP kuralına göre düzelt, retry ×1 | Ayakta |
| 🔴 | 1 | `ERROR_INVALID_HANDLE` | **L2** handle yeniden aç (wlacore) | Ayakta |
| 🔴 | 2 | 4 sn dolu, bildirim yok | **L2** "Tarama zaman aşımına uğradı" — kısmi/eski liste ile devam | Ayakta |
| 🔴 | 3 | RPC/bellek hataları | **L2** (Liste hata satırı) | Ayakta |
| ⚫ | herhangi | istisna | **L3** exit 70 | Kapanır |

### §4.3 wifi_status

**Adımlar:**

1. `WlanQueryInterface(h, &guid, wlan_intf_opcode_current_connection, NULL, &size, &data, NULL)`.
   **XP'de yalnız 4 opcode geçerli:** `autoconf_enabled · bss_type · interface_state ·
   current_connection` [S15] → `wlan_intf_opcode_rssi`, `radio_state`, `statistics`
   XP'de **L1** "Bu özellik bu Windows sürümünde yok" (02 §5.1 Level 1 satırı).
2. `WLAN_CONNECTION_ATTRIBUTES` → `isState · wlanConnectionMode · strProfileName` [S10].
3. `wlanAssociationAttributes` → SSID, BSSID, `dot11PhyType`, `wlanSignalQuality`,
   `ulRxRate`, `ulTxRate` [S15 örnek].
4. `wlanSecurityAttributes` → `dot11AuthAlgorithm`, `dot11CipherAlgorithm`,
   `bSecurityEnabled`, `bOneXEnabled` [S15 örnek]. **XP durum kümesi** yalnız
   `connected / disconnected / authenticating` (03 K; [S10]).
5. XP `rssi` yoksa: son `wifi_list` önbelleğindeki `wlanSignalQuality` → türetilmiş
   RSSI (`-100 + q/2`) [S4], `source:"derived"` etiketiyle.
6. `WlanFreeMemory` [S15].

| BAŞARI | Adım | Beklenen | Kanıt |
|---|---|---|---|
| ✅ | 1–4 | `ERROR_SUCCESS`, tüm zorunlu alanlar dolu | [S15], [S10] |
| ✅ | 5 | XP'de `rssi:null` veya `derived` | [S4] |
| ✅ | 6 | bellek serbest | [S5] |

| ÇÖKME-KAPANIŞ | Adım | Hata | Seviye | Süreç |
|---|---|---|---|---|
| 🔴 | 1 | XP'de geçersiz opcode | **L1** alan `null` + `features`'tan düşür | Ayakta |
| 🔴 | 1 | `ERROR_INVALID_PARAMETER` / arayüz yok | **L2** | Ayakta |
| 🔴 | 4 | yapı boyutu beklenenden farklı (sürüm farkı) | **L2** — `pdwDataSize` ile doğrula, alanı truncate et | Ayakta |
| ⚫ | herhangi | istisna | **L3** exit 70 | Kapanır |

### §4.4 wifi_connect  (en kritik akış)

**Adımlar:**

1. **Girdi doğrulama:** `ssid` 1–32 byte; `psk` varsa **8–63 ASCII karakter**
   (netsh hostednetwork passphrase kuralı [S17]; ihlali WLAN_REASON_CODE
   `MSMSEC_PROFILE_PSK_LENGTH` = "Invalid PSK length" [S7]). `psk` yoksa → açık/WEP
   profili akışı (yalnız mevcut profille bağlanma).
2. **Profil XML üret (wlaprofile):** şema **sırası zorunludur** [S16]:

```xml
<WLANProfile xmlns="https://www.microsoft.com/networking/WLAN/profile/v1">
  <name>Mynet</name>
  <SSIDConfig><SSID><name>Mynet</name></SSID></SSIDConfig>
  <connectionType>ESS</connectionType>
  <connectionMode>auto</connectionMode>
  <MSM><security>
    <authEncryption>
      <authentication>WPA2PSK</authentication>
      <encryption>AES</encryption>
      <useOneX>false</useOneX>
    </authEncryption>
    <sharedKey>
      <keyType>passPhrase</keyType>
      <protected>false</protected>
      <keyMaterial>ornek-sifre-123</keyMaterial>
    </sharedKey>
  </security></MSM>
</WLANProfile>
```

   - `authentication`/`encryption` değerleri **yalnız burada** tanımlıdır (bkz §1.3-1)
     [S16], [S18]; netsh karşılıkları: `open|shared|WPA|WPA2|WPAPSK|WPA2PSK` /
     `none|WEP|TKIP|AES` [S17].
   - **XP:** profil adı SSID'den türetilir (ad hoc = `SSID + "-adhoc"`) [S11], [S19];
     profile başına 1 SSID (03 K1); `FIPSMode` XP'de yok sayılır [S16].
3. **`WlanSetProfile(h, &guid, name, xml, 0, NULL, &dwReasonCode)`** — XP'de
   `dwFlags = 0` zorunlu [S3]; düz metin anahtar XP'de **asla şifrelenmez** [S3].
   Hata durumunda `dwReasonCode` → `WlanReasonCodeToString` (XP beyaz listesinde) [S3],
   [S7], [D1 §satır "WlanReasonCodeToString hata çevirisı"].
   - `ERROR_ALREADY_EXISTS` → `WlanDeleteProfile` + yeniden yaz (tasarım kararı).
   - `ERROR_ACCESS_DENIED` → L2 "Yönetici yetkisi gerekli".
4. **`WlanConnect(h, &guid, &params, NULL)`** — `params`:

| Alan | Değer | Kanıt |
|---|---|---|
| `wlanConnectionMode` | `wlan_connection_mode_profile` (XP'de yalnız profile modu) | [S8], [S9] |
| `strProfile` | profil adı (= SSID) | [S9] |
| `pDot11Ssid` | SSID (XP: yalnız profile modu, isteğe bağlı) | [S9] |
| `pDesiredBssidList` | **NULL** (XP kısıtı) | [S9] |
| `dot11BssType` | `dot11_BSS_type_infrastructure` (ESS) | [S9] |
| `dwFlags` | **0** (XP kısıtı) | [S9] |
| auth/cipher | **YOK — yapıda böyle alan yok (§1.3-1)** | [S9] |

5. **Sonuç bekleme (asenkron):** `WlanConnect` hemen döner; sonuç bildirimden okunur
   [S8] → T2 `wlan_notification_acm_connection_complete` →
   `WLAN_NOTIFICATION_DATA.uWin32ReasonCode == 0` → BAŞARI; ≠0 → `WlanReasonCodeToString`
   [S7]. Bekleme sınırı: `connectTimeoutMs` (tasarım parametresi).
   - **Vista+ ayırt edici reason kodları:** `WLAN_REASON_CODE_KEY_MISMATCH` ("profile key
     does not match network key" → şifre hatalı), `WLAN_REASON_CODE_MSMSEC_PSK_MISMATCH_SUSPECTED`,
     `MSMSEC_PROFILE_PSK_LENGTH` [S7].
   - **XP:** desteklenen reason kodu **daraltması** belgelidir (profil: `INVALID_PROFILE_SCHEMA ·
     PROFILE_MISSING · PROFILE_SSID_INVALID`; MSMSEC: `INVALID_KEY_INDEX · KEY_LENGTH ·
     PSK_LENGTH · INVALID_AUTH_CIPHER · ONEX_* · CAPABILITY_NETWORK · CAPABILITY_NIC ·
     KEYMATERIAL_CHAR · WRONG_KEYTYPE`; 802.1X: 4 kod) [S7] → `KEY_MISMATCH` **XP
     listesinde YOK**; XP'de yanlış şifre genellikle **genel bağlantı hatası** olarak
     döner → mesaj Level 2 genel "Bağlantı kurulamadı" + `reasonText` (kısmi).
6. **Doğrulama:** bağlantı sonrası `wifi_status` tekrar okunur, `ssid` eşleşmesi + `isState=connected`
   doğrulanır → `wifi_event` push (bkz §5.4).

| BAŞARI | Adım | Beklenen | Kanıt |
|---|---|---|---|
| ✅ | 1 | `psk` 8–63 ASCII | [S17], [S7] |
| ✅ | 2 | `ERROR_SUCCESS` / overwrite yolu | [S16] |
| ✅ | 3 | `WlanSetProfile` → 0, `dwReasonCode=0` | [S3] |
| ✅ | 4 | `WlanConnect` → `ERROR_SUCCESS` (anında dönüş) | [S8] |
| ✅ | 5 | `connection_complete` + reason 0 ≤ timeout | [S1], [S7] |
| ✅ | 6 | `wifi_status` → connected | [S15] |

| ÇÖKME-KAPANIŞ | Adım | Hata | Seviye | Süreç |
|---|---|---|---|---|
| 🔴 | 1 | `psk` 8–63 dışı | **L2** "Wi-Fi şifresi 8–63 karakter olmalı" (ön yakalama) | Ayakta |
| 🔴 | 3 | `ERROR_BAD_PROFILE` + reason | **L2** "Profil yazılamadı: {reasonText}" | Ayakta |
| 🔴 | 3 | `ERROR_ACCESS_DENIED` | **L2** "Yönetici yetkisi gerekli" | Ayakta |
| 🔴 | 3 | `ERROR_ALREADY_EXISTS` | akış §4.4-3 (delete+retry ×1) → tekrar hata: L2 | Ayakta |
| 🔴 | 4 | `ERROR_INVALID_PARAMETER` | **L2** "Bağlantı parametresi geçersiz" — tablo §1.3-1'e göre düzelt | Ayakta |
| 🔴 | 5 | reason ≠ 0 (KEY_MISMATCH — Vista+) | **L2** "Bağlantı reddedildi: şifre hatalı" (02 §5.2 satırı) | Ayakta |
| 🔴 | 5 | timeout | **L2** "Bağlantı zaman aşımı" → disconnect + rapor | Ayakta |
| ⚫ | herhangi | SEH | **L3** log + **exit 70** | Kapanır |

### §4.5 wifi_disconnect

**Adımlar:** (1) `WlanDisconnect(h, &guid, NULL)` [S9a] → (2) `acm_disconnected`
bildirimi bekleme (XP'de mevcut [S1]) → (3) `wifi_status` ile
`isState=disconnected` doğrulama → (4) push `wifi_event`.

| BAŞARI | Beklenen | Kanıt |
|---|---|---|
| ✅ (1) | `ERROR_SUCCESS` | [S9a] |
| ✅ (3) | disconnected | [S15] |

| ÇÖKME-KAPANIŞ | Hata | Seviye | Süreç |
|---|---|---|---|
| 🔴 (1) | `ERROR_INVALID_PARAMETER` / `ERROR_INVALID_HANDLE` | **L2** handle yeniden aç | Ayakta |
| 🔴 (1) | `ERROR_ACCESS_DENIED` | **L2** yetki mesajı | Ayakta |
| 🔴 (1) | RPC hataları | **L2** "Ağ servisiyle iletişim kurulamadı" | Ayakta |
| 🔴 (1) | `ERROR_NOT_ENOUGH_MEMORY` | **L2** retry ×1 → L3 (02 §5.2) | Ayakta |
| 🔴 (2) | bildirim gelmez | **L2** "Koparma doğrulanamadı" (timeout) | Ayakta |
| 🔴 | **XP yan etkisi:** disconnect sonrası profil "on-demand" olur (auto-connect kapanmaz değil, yeniden istenir) [S9a] | bilgi niteliğinde | Ayakta |

### §4.6 wifi_profiles_list / get / delete / reorder

**List:** `WlanGetProfileList(h, &guid, NULL, &pList)` → tercih sırası;
`dwFlags` içinde `WLAN_PROFILE_GROUP_POLICY` / `WLAN_PROFILE_USER` ayrımı [S14].
**XP:** guest/WPS/WPA-None profilleri listeye **gelmez** [S14].

**Get:** `WlanGetProfile(h, &guid, name, flags, &xml, &grantedAccess, NULL)` [S11] —
- `WLAN_PROFILE_GET_PLAINTEXT_KEY` **yalnız istemci (Vista+) ve Win7+** [S11]; XP'de
  şifreli anahtar döner (düz metin mümkün değil, §1.3-2).
- `ERROR_ACCESS_DENIED` → şifreli anahtar (hata değil) [S11].
- `ERROR_NOT_FOUND` → L2 "Profil bulunamadı" [S11].

**Delete:** `WlanDeleteProfile(h, &guid, name, NULL)` — XP beyaz listesinde [D1 §3.1];
dönüş kodları bu araştırmada sayfa sayfa doğrulanmadı → **ham DWORD korunur, kesin kod
listesi ⚠️ VERIFICATION REQUIRED** (§9.5).

**Reorder:** `WlanSetProfilePosition(h, &guid, name, dwPosition, NULL)` — `dwPosition=0`
listedeki ilk sıradır [S19]; XP'de profil adı kuralı SSID / `SSID-adhoc` [S19];
`ERROR_ACCESS_DENIED` (DACL `wlan_secure_all_user_profiles_order`) [S19]; XP'de ad hoc
profiller her zaman infrastructure sonrasına itilir [S19]; grup politikası profili
sırası değiştirilemez [S19].

| BAŞARI | Adım | Beklenen | Kanıt |
|---|---|---|---|
| ✅ | list | tercih sıralı liste + flags | [S14] |
| ✅ | get | XML + `hasPlaintextKey` | [S11] |
| ✅ | delete | `ERROR_SUCCESS` | [D1] |
| ✅ | reorder | `ERROR_SUCCESS`, 0-tabanlı sıra | [S19] |

| ÇÖKME-KAPANIŞ | Adım | Hata | Seviye | Süreç |
|---|---|---|---|---|
| 🔴 | get | `ERROR_ACCESS_DENIED` | **L2** anahtar şifreli döner, `hasPlaintextKey:false` | Ayakta |
| 🔴 | get | `ERROR_NOT_FOUND` | **L2** "Profil bulunamadı" | Ayakta |
| 🔴 | reorder | `ERROR_ACCESS_DENIED` | **L2** "Profil sırası değiştirilemedi (yetki)" | Ayakta |
| 🔴 | reorder | grup politikası profili | **L2** "Grup politikası profili sabittir" | Ayakta |
| 🔴 | delete | bulamama / yetki | **L2** (ham DWORD loglanır) | Ayakta |
| ⚫ | herhangi | istisna | **L3** exit 70 | Kapanır |

---

## §5 Veri Modeli & JSON Sözleşmesi

### §5.1 Kapı (handshake) — neva-connect protokolüyle uyumlu

```json
{"type":"handshake","apiVersion":2,"clientType":"spa"}
```
(protokol kaynağı: neva-connect/CLAUDE.md §WebSocket (Port 9742) [D3])

### §5.2 Zarf (yanıt)

```json
{
  "type": "result",
  "id": "req-42",
  "ok": true,
  "os": "windows-11-23h2",
  "negotiatedVersion": 2,
  "features": ["wifi_list", "wifi_scan", "wifi_status", "wifi_connect",
                "wifi_disconnect", "wifi_profiles_list", "wifi_profiles_get",
                "wifi_profiles_delete", "wifi_profiles_reorder"],
  "data": { },
  "error": null
}
```

Hatalı yanıtta `ok:false` ve `error` zarfı:

```json
{
  "type": "result", "id": "req-43", "ok": false,
  "os": "windows-xp-sp3", "negotiatedVersion": 1,
  "features": ["wifi_list", "wifi_status", "wifi_connect",
               "wifi_disconnect", "wifi_profiles_list"],
  "data": null,
  "error": { "code": "E_WLAN_CONNECT_REFUSED", "level": 2,
             "message": "WlanConnect reason=0x...",
             "tr": "Bağlantı reddedildi: şifre hatalı" }
}
```

> `features` XP'de daralır: `wifi_scan` bildirimsiz olduğu için **XP'de vardır ama
> `scanCompleteNotification:false`** ile; `wifi_profiles_reorder` vardır (XP beyaz
> listesinde) [S19]. `negotiatedVersion` XP = 1 [S12].

### §5.3 Komutlar (istemci → neva-wlan)

```json
{"cmd":"wifi_list","id":"req-1"}
{"cmd":"wifi_scan","id":"req-2"}
{"cmd":"wifi_status","id":"req-3","interface":"{GUID-opsiyonel}"}
{"cmd":"wifi_connect","id":"req-4","ssid":"Mynet","psk":"ornek-sifre-123"}
{"cmd":"wifi_connect","id":"req-5","ssid":"Mynet"}
{"cmd":"wifi_disconnect","id":"req-6"}
{"cmd":"wifi_profiles_list","id":"req-7"}
{"cmd":"wifi_profiles_get","id":"req-8","name":"Mynet","plaintextKey":false}
{"cmd":"wifi_profiles_delete","id":"req-9","name":"Mynet"}
{"cmd":"wifi_profiles_reorder","id":"req-10","name":"Mynet","position":0}
```

`wifi_list` / `wifi_status` veri şeması (`data`):

```json
{
  "interfaces": [
    { "guid": "{GUID}", "description": "Intel Wi-Fi 6 AX201",
      "state": "connected",
      "networks": [
        { "ssid": "Mynet", "bssid": "aa:bb:cc:dd:ee:ff",
          "signalQuality": 86, "rssi": -57, "rssiSource": "derived",
          "bssType": "infrastructure", "security": ["WPA2-PSK"],
          "connected": true }
      ] },
    { "guid": "{GUID2}", "description": "Realtek 802.11n",
      "state": "disconnected", "radioOff": false, "networks": [] }
  ]
}
```

`wifi_status` ek alanları: `profile`, `authAlgorithm`, `cipherAlgorithm`,
`phyType`, `rxRateMbps`, `txRateMbps`.

### §5.4 Push (sunucu → istemci)

```json
{"type":"wifi_event","event":"connected","ssid":"Mynet","reason":0}
{"type":"wifi_event","event":"disconnected","reason":0}
{"type":"wifi_event","event":"scan_complete","scanId":7,"elapsedMs":1840}
{"type":"wifi_event","event":"connect_failed","reasonText":"...","level":2}
{"type":"wifi_event","event":"queue_overflow","droppedEvents":3}
```

### §5.5 Alan sözlüğü

| Alan | Tip | Anlamı | Kaynak |
|---|---|---|---|
| `negotiatedVersion` | int | `pdwNegotiatedVersion` (XP=1) | [S12] |
| `features` | string[] | resolver'ın açtığı komutlar | 03 §2.4 kapısı |
| `signalQuality` | 0–100 | WLAN sinyal kalitesi | [S4] |
| `rssi` | dBm | `-100 + quality/2` (türetilmiş; doğrudan opcode XP'de yok) | [S4], [S15] |
| `state` | enum | `not_ready|connected|ad_hoc_network_formed|disconnecting|disconnected|associating|discovering|authenticating` | [S13] |
| `error.level` | 0–3 | 02 doc §5.1 seviyeleri | [D1 §5.1] |

---

## §6 Hata Seviyeleri (Level 0–3 — 02 doc §5.1/§5.2 ile uyumlu)

Seviye sözlüğü: **L0** kernel katmanına dokunulmaz (tasarım kısıtı) · **L1** API/export
yok → graceful degrade · **L2** API var ama `ERROR_*` / reason code → retry/alternatif ·
**L3** beklenmeyen C++/SEH → log + **exit 70** (02 §5.1).

| # | Kod | Seviye | Tetikleyici (kaynak) | TR mesaj | Süreç davranışı |
|---|---|---|---|---|---|
| 1 | `E_WLAN_LEVEL0` | **0** | NDIS/sürücü koduna dokunma denemesi | "Beklenmeyen iç hata" | **Tasarım yasağı** — kodda yer yok (02 §5.1) |
| 2 | `E_WLAN_LOADER` | **1** | `wlanapi.dll` yüklenemedi | "Kablosuz arayüz kütüphanesi bulunamadı (wlanapi.dll)" | Ayakta — sürüm bilgisi yaz (02 §5.2) |
| 3 | `E_WLAN_EXPORT` | **1** | `GetProcAddress` NULL (export yok) | "Bu Windows sürümünde bu özellik desteklenmiyor" | Ayakta — komut L1 döner |
| 4 | `E_WLAN_OPCODE` | **1** | XP'de `wlan_intf_opcode_rssi/radio_state` vb. | "Bu özellik bu Windows sürümünde yok" | Ayakta — alan null + features'tan düş (02 §5.2) |
| 5 | `E_WLAN_OPEN_HANDLE` | **2** | `WlanOpenHandle` başarısız / XP WZC [S12] | "Kablosuz ağ servisi başlatılamadı (WZC / WLAN AutoConfig)" | Retry 3×2 sn → durum raporu (02 §5.2) |
| 6 | `E_WLAN_NO_INTERFACE` | **2** | `WlanEnumInterfaces` → 0 arayüz | "Kablosuz adaptör bulunamadı" | Enum tekrar → 10 sn bilgi (02 §5.2) |
| 7 | `E_WLAN_RADIO_OFF` | **2** | `ERROR_NDIS_DOT11_POWER_STATE_INVALID` [S4] | "Kablosuz radyo kapalı" | Kullanıcı eylemi; süreç ayakta |
| 8 | `E_WLAN_SCAN_TIMEOUT` | **2** | 4 sn içinde `scan_complete` yok [S13a] | "Tarama zaman aşımına uğradı" | Eski listede devam / retry ×1 |
| 9 | `E_WLAN_PROFILE_SET` | **2** | `WlanSetProfile` → `ERROR_BAD_PROFILE` + `dwReasonCode` [S3] | "Profil yazılamadı: {reasonText}" | Retry yok; XML düzeltmesi gerekir |
| 10 | `E_WLAN_ACCESS_DENIED` | **2** | `ERROR_ACCESS_DENIED` (set/position/get plaintext) [S3], [S11], [S19] | "Yönetici yetkisi gerekli" | Ayakta — şifreli alan fallback |
| 11 | `E_WLAN_CONNECT_REFUSED` | **2** | `connection_complete` reason ≠ 0 → `WlanReasonCodeToString` [S7] | "Bağlantı reddedildi: {reasonText}" | Alternatif profil → L1'e düşür (02 §5.2) |
| 12 | `E_WLAN_PSK_INVALID` | **2** | ön kontrol: psk 8–63 ASCII dışı [S17] | "Wi-Fi şifresi 8–63 karakter olmalı" | Bağlantı başlatılmaz |
| 13 | `E_WLAN_CONNECT_TIMEOUT` | **2** | `connectTimeoutMs` doldu (tasarım param.) | "Bağlantı zaman aşımı" | Disconnect + rapor |
| 14 | `E_WLAN_PROFILE_NOT_FOUND` | **2** | `WlanGetProfile` → `ERROR_NOT_FOUND` [S11] | "Profil bulunamadı" | Ayakta |
| 15 | `E_WLAN_MEMORY` | **2→3** | bellek ayrılamadı (retry ×1) | "Bellek ayrılamadı" | L3'e düşer (02 §5.2) |
| 16 | `E_WLAN_SHUTDOWN_RACE` | **2** | kapanış sırası ihlali (Gotcha 2) [D2] | "Kapanış sırası ihlali — tekrar denendi" | Sıra uygulanır, süreç ayakta |
| 17 | `E_WLAN_EXCEPTION` | **3** | C++/SEH | "Beklenmeyen hata — ayrıntılar log dosyasında" | Log + **exit 70** (02 §5.2) |

`level=3` mesajlarında STACK TRACE **asla** JSON'a gömülmez (yalnız log dosyası).

---

## §7 Performans

| Metrik | Hedef | Ölçüm / dayanak |
|---|---|---|
| `wifi_list` (warm, handle açık) | **< 500 ms** | lokal ölçüm; kaynak yok → hedef (tasarım) |
| `wifi_list` (cold, handle açma dahil) | < 1200 ms | WlanOpenHandle + enum + liste [S12], [S13], [S4] |
| `wifi_status` | **< 50 ms** | tek `WlanQueryInterface` [S15] |
| `wifi_scan` (Vista+) | **≤ 4000 ms** + okuma | MS logo zorunluluğu 4 sn [S13a] |
| `wifi_scan` (XP) | 4000 ms sabit bekleme + okuma | bildirim yok [S13a] — gerçek süre `UNKNOWN` |
| `wifi_connect` ACK | **< 100 ms** (asenkron kabul) | `WlanConnect` anında döner [S8] |
| `wifi_connect` sonuç | `connectTimeoutMs` (varsayılan 15000 — tasarım) | bildirim bekleyişi [S1] |
| Status polling | **2 sn** (boşta), event gelince iptal | WLANSVC 60 sn tarama ritmi biliniyor [S13a] |
| Kuyruk | **≤ 256 olay**, taşma → en eski düşür + sayaç | §3.3 |
| WS 9742 reconnect | **< 5 sn** | neva-connect/CLAUDE.md [D3] |
| Bellek | `WlanFreeMemory` her dönüşte; RSS hedefi **< 64 MB** | [S5]; hedef tasarımsal |
| CPU (boşta) | < 5 % | polling kapatılınca ≈ 0 |

---

## §8 Test Tasarımı

**Zemin:** MSVC2022 + CTest (02 doc derleme/zincir kuralları [D1]) · XP ayrı artefakt
(02 doc XP kuralı — XP derlemesi bağımsız çıktı olarak üretilir) [D1].

| Katman | Kapsam | Yöntem | Geçiş kriteri |
|---|---|---|---|
| **Unit — wlabase** | resolver: `GetProcAddress` tablosu, XP beyaz liste (20 fn) | **Sahte `wlanapi.dll`** (test dizinine kopya) + sembol eksiltme → L1 yolları | 20/20 beyaz liste + 16 siyah/dışlı [D1 §2.5] |
| **Unit — wlaprofile** | XML üretici: şema sırası, 8–63 psk, XP ad kuralı (SSID / `SSID-adhoc`) | Golden XML dosyaları + negatif durumlar | 100% şablon eşleşmesi [S16] |
| **Unit — wlaconn** | reason-code eşleme (XP destekli alt küme [S7]) | Sabit reason listesi → TR mesaj | XP/Vista küme ayrımı doğrulanır |
| **Unit — SPSC** | 256 kapasite, taşma, concurrency | 2 iş parçacığı × 10⁶ olay + TSan/ASAN (wireless-connect ASAN kültürü) | 0 veri kaybı (taşma sayacı doğru) [D2] |
| **Contract** | §5 JSON zarfı | Golden `*.json` + şema doğrulama; `level` 0–3 alanı zorunlu | alan adları değişmez |
| **Integration (gerçek donanım)** | list/scan/status/connect/disconnect Win10+11 | CTest `[hw]` etiketli, gerçek AP + mobil hotspot | 10/10 döngü |
| **Failure injection** | radyo kapat → `ERROR_NDIS_DOT11_POWER_STATE_INVALID` [S4]; adaptör çıkar → `ERROR_INVALID_PARAMETER` [S19]; profil sil → bağlantı ortasında | manuel/CI senaryosu | mesaj Level 2, süreç ayakta |
| **Shutdown race** | start/stop ×100 | Gotcha 2 kalıbı: join → close → 100 ms [D2] | 0 crash, 0 "interface in use" |
| **Timeout** | scan 4 sn, connect timeout | sahte bildirim saatleri (timer sanallaştırma) | `E_WLAN_*_TIMEOUT` üretimi |
| **XP** | beyaz liste + XP mesaj kümeleri | XP sanal makine + USB Wi-Fi (donanım yoksa §9.8) | L1/L2 yolları, süreç ayakta |

- **Coverage hedefi:** ≥ %80 (CoreMusic QA standardı — kök .ai/AGENTS.md §16).
- **Yasak:** testler üretim `wlanapi.dll` çağrılarını XP dışı ortamlarda bile beyaz
  liste ihlaliyle yapamaz (03 §2.4 kapısı test edilir).

---

## §9 Açık Konular

| # | Konu | Durum | Öneri |
|---|---|---|---|
| 1 | **WS 9742 port çakışması** — neva-connect daemon (Linux) ile aynı port ismi [D3] | Tasarım kararı gerekiyor | Tek daemon + `neva-wlan` yan-servis; port sahipliği ADR |
| 2 | **psk düz metin WS üzerinden** (komut zarfında) | Güvenlik notu — 03/04 kapsamı | `127.0.0.1` bind + istemci eşleşmesi; log'da `[REDACTED]` |
| 3 | **XP `WlanScan` gerçek süresi** (bildirim yok) [S13a] | `UNKNOWN` | 4 sn sabit bekleme + ölçümleme; XP'de liste önbelleği |
| 4 | **WLAN_REASON_CODE sayısal değerleri** MS sayfasında yok (yalnız örnek `0x00050007` verilmiş) [S7] | Uydurmak yasak | Runtime `WlanReasonCodeToString` + Appendix E tablosu [S7] |
| 5 | **`isUserControllable`** terimi WLAN_profile şemasının resmî eleman listesinde **YOK** [S16] | **⚠️ VERIFICATION REQUIRED** | Tasarımda kullanılmayacak; brief'ten çıkarılmalı |
| 6 | **Callback "farklı iş parçacığı" iddiası** — MSDN'de açık cümle yok; yalnız deadlock notu [S1] | **⚠️ VERIFICATION REQUIRED** | Tasarım her koşulda izole T2 varsayar |
| 7 | **"Changes to API behavior for Wi-Fi access and location" (fall 2024)** — `WlanScan`/`WlanQueryInterface` sayfalarında uyarı var [S13a], [S15] | Teyit gerek | Yeni Windows'ta konum izni etkisi araştırılacak |
| 8 | **XP test donanımı** (USB Wi-Fi + XP VM) | Bulunmadı | Yoksa XP kapsamı yalnız resolver/unit ile sınırlı |
| 9 | **`WlanDeleteProfile` dönüş kodu listesi** | ⚠️ bu araştırmada doğrulanmadı | Kod listesi sonraki turda Microsoft Learn'dan çekilecek |
| 10 | **`WLAN_PROFILE_ALL_USER` bayrağı değeri** (Vista+ all-user yazım) | ⚠️ doğrulanmadı | `dwFlags=0` ile başla; all-user gereksinimi ADR'ye taşınır |
| 11 | **Çoklu adaptör seçimi** | Politika yok | Varsayılan: ilk bağlı arayüz, `interface` alanı isteğe bağlı |
| 12 | **Gizli SSID (`nonBroadcast`)** [S16] | Tasarım dışı | `wifi_connect` v1.1'e |
| 13 | **XP felaket yolu** (WZC durdurulmuşken tarama/durum — NDISUIO OID) | Kapsam **onaylandı** (2026-10-15) → tasarım **09 doc**'a taşındı | Uygulama `wlabase/xp_ndisuio_fallback` (09 §5.2-A); tarama aralığı 09 §7 T-05'ten gelir |

---

## §10 Kaynaklar

### §10.1 Web (Microsoft Learn — hepsi bu araştırmada çekildi)

| # | Kaynak | URL |
|---|---|---|
| S1 | WlanRegisterNotification (XP ACM kapsamı, deadlock, auto-undo, unregister) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanregisternotification |
| S2 | WLAN_NOTIFICATION_CALLBACK (XP bildirim kümesi, Netman notu) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nc-wlanapi-wlan_notification_callback |
| S3 | WlanSetProfile (XP dwFlags=0, ERROR_* listesi, düz metin anahtar) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlansetprofile |
| S4 | WlanGetAvailableNetworkList (XP bayrak yasağı, radyo-hatası, RSSI formülü) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetavailablenetworklist |
| S5 | WlanFreeMemory (her dönüş serbest) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanfreememory |
| S6 | WlanGetAvailableNetworkList2 (arsayiv MSDN; KB940541 referansı) | https://learn.microsoft.com/en-us/previous-versions/mt595902(v=vs.85) |
| S7 | WLAN_REASON_CODE (KEY_MISMATCH · PSK_LENGTH · XP destekli alt küme · Appendix E) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-reason-code |
| S8 | WlanConnect (anında dönüş → bildirim gerekir; XP yalnız profile modu) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanconnect |
| S9 | WLAN_CONNECTION_PARAMETERS (alan yokluğu; XP: NULL BSSID, dwFlags=0) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_connection_parameters |
| S9a | WlanDisconnect (hata kodları + XP on-demand yan etkisi) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlandisconnect |
| S10 | WLAN_CONNECTION_ATTRIBUTES (isState/mode/XP durum kümesi) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_connection_attributes |
| S11 | WlanGetProfile (XP ad türetme, WLAN_PROFILE_GET_PLAINTEXT_KEY, ERROR_*) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetprofile |
| S12 | WlanOpenHandle (dwClientVersion 1/2, negotiatedVersion, XP WZC) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanopenhandle |
| S13 | WlanEnumInterfaces (enum + WlanFreeMemory + durum enumu) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanenuminterfaces |
| S13a | WlanScan (XP: SSID/IE NULL + bildirimsiz; 4 sn logo kuralı; WLANSVC 60 sn) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanscan |
| S14 | WlanGetProfileList (tercih sırası, flags, XP dışlama) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlangetprofilelist |
| S15 | WlanQueryInterface (opcode tablosu; XP yalnız 4 opcode; serbest bırakma) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanqueryinterface |
| S16 | WLAN_profile schema elements (sıra zorunlu; isUserControllable YOK) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-profileschema-elements |
| S17 | netsh wlan (authentication/encryption değerleri; 8–63 passphrase) | https://learn.microsoft.com/en-us/windows-server/administration/windows-commands/netsh-wlan |
| S18 | authEncryption (security) element (XP'de FIPSMode yok sayılır) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-profileschema-authencryption-security-element |
| S19 | WlanSetProfilePosition (dwPosition 0, XP ad kuralı, DACL, ad hoc sıralama) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlansetprofileposition |
| S20 | MS-GPWL — XML tabanlı WLAN profili mesaj sözdizimi | https://learn.microsoft.com/en-us/openspecs/windows_protocols/ms-gpwl/843bff53-3a60-4763-a04b-faf61fe79e36 |
| S21 | WlanReasonCodeToString (sayısal → metin; XP beyaz listesinde) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanreasoncodetostring |
| S22 | Wi-Fi access and location davranış değişikliği (S13a/S15 sayfalarındaki uyarı) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wi-fi-access-location-changes |

### §10.2 Disk dosyaları (bu araştırmada okunan vault/proje dosyaları)

| Dosya | Kullanım |
|---|---|
| `.ai/projects/NevaConnect/01-wlanapi-sürüm-matrisi-ve-windows-plani.md` | Export matrisi, katman planı (wlabase…wlaconn, satır 95–120) |
| `.ai/projects/NevaConnect/02-coklu-exe-windows-sdk-derleme-mimarisi.md` | Level 0–3 seviye tablosu (§5.1) + hata mesaj kataloğu (§5.2, satır 440–457), MSVC/XP artefakt kuralı |
| `.ai/projects/NevaConnect/03-wlan-api-ozellik-kisitlama-tablosu.md` | Resolver kapısı (§2.4), XP 20 fonksiyon beyaz listesi (§3.1), XP kısıtlama tablosu (§3.1 K1–K9) |
| `.ai/projects/NevaConnect/04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama.md` | Kapsam dışı (BT/USB/depolama, Wi-Fi 7) |
| `E:\Belgeler\coremusic.net.old\cpp-projects\neva-connect\CLAUDE.md` | WS 9742 protokolü (handshake/cmd/response), L0–L3 katmanları, reconnect < 5 sn |
| `E:\Belgeler\coremusic.net.old\cpp-projects\wireless-connect\AGENTS.md` | Gotcha 2 kapanış sırası (lock+join+close+100 ms), ASAN/fuzz kültürü |

**Okunan NevaConnect vault dosya sayısı:** 4 (01 · 02 · 03 · 04) — `05-` bu dosyadır.

---

*neva-wlan.exe Çekirdek Modül Tasarımı v1.0.0 · status: plan · 2026-10-15 ·
kaynak: §10 (S1–S22 web + D1–D3 disk) · commit: orkestratöre aittir.*
