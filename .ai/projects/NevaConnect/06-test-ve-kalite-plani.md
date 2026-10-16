---
title: "Neva Connect — Test & Kalite Planı (OS matrisi · feature-gate negatif test · çok-exe smoke)"
type: doc
category: connect-network
version: 1.0.0
status: plan
authority: "01-05 NevaConnect dokümları + Microsoft Learn + cpp-projects test standardı"
updated: 2026-10-15
---

# Neva Connect — Test & Kalite Planı (Windows dalı · `neva-wlan.exe` / F1)

> **Kapsam:** Neva Connect Windows WLAN dalının (`neva-wlan.exe`, F1) test ve kalite planı.
> **Bu dosya PLANDIR — hiçbir test bu yazım anında koşulmamıştır.** "Geçti", "başarılı",
> "%X coverage" gibi **sonuç iddiaları YOKTUR**; kapılar tanımlanmış, ölçümler boş bırakılmıştır.
> Kod üretimi bu planın dışındadır (01 §1 kuralı: kodlama kullanıcı onayıyla).
>
> **Kardeş dokümanlar:**
> [[01-wlanapi-sürüm-matrisi-ve-windows-plani]] (export matrisi — MD5 kanıtlı 10 DLL)
> · [[02-coklu-exe-windows-sdk-derleme-mimarisi]] (çoklu-EXE + derleme matrisi; §7 test/yayın)
> · [[03-wlan-api-ozellik-kisitlama-tablosu]] (feature gate; §6 test matrisi)
> · [[04-kapsam-yol-haritasi-wifi7-bluetooth-usb-depolama]] (kapsam öncelikleri)
> · **06 = bu dosya** (test & kalite planı)
>
> **Zero-Hallucination:** her iddia ya kardeş dokümanlarda dosya+satır kanıtlıdır
> (`02 §7.2`, `cpp-projects/CLAUDE.md:273` gibi) ya da §10'daki kaynak URL'idir;
> doğrulanamayan satır `⚠️ VERIFICATION REQUIRED`, bilinmeyen değer `UNKNOWN` ile işaretlidir.
> Araç/sürüm iddiası kaynaksız yazılmaz; **test sonucu uydurulmaz.**

---

## §1 Test Stratejisi

### §1.1 Test piramidi (dört katman)

| Katman | Pay hedefi | Kapsam | Ortam | Zaman bütçesi | Ne zaman koşar |
|---|---|---|---|---|---|
| **Birim** (unit) | **%70** | resolver, RAII, kuyruk, JSON şeması, profil XML üretimi, hata çevirisi, snapshot | Gerçek WLAN **yok** — sahte export tablosu, sentetik struct | Suite **< 5 sn** | Her commit / PR |
| **Entegrasyon** | **%20** | gerçek `wlanapi.dll` + `wlansvc`, WS 9742 köprüsü, profil yaz/sil, notification | Yerel Windows 10/11 makine (WLAN sürücülü) | Suite **< 5 sn** | PR sonrası yerel / gece |
| **OS-uç smoke** | **%10** | §4 checklist — her artefakt kendi OS'unda | Gerçek XP SP3 / Win7 / Win8.1 / Win10 19041 | Otomatik kısım **< 30 sn**; manuel adım için süre sınırı yok | Sürüm öncesi + faz kapanışı |
| **Regresyon** | (piramit payı yok — tekrar) | §2 + §3 setinin tamamı, OS matrisi uçlarında | Aynı ortamlar | Aynı bütçeler | Her release adayı |

- Pay hedefleri CoreMusic QA kuralıdır (test piramidi: Unit %70 · Integration %20 · E2E %10);
  **bu plan bu payları F1 dalı için bağlayıcı hedef olarak alır.**
- Zaman bütçeleri (birim <5 sn · entegrasyon <5 sn · E2E <30 sn) de aynı kaynaktan gelir;
  **manuel smoke adımları bu bütçenin dışındadır** (insan-gözlem adımı, süre ölçülmez).
- **Flaky yasak:** retry/tekrar-koşu (ortbas) yok; başarısız test = BLOCK, gizlenemez.

### §1.2 İki boyutlu test matrisi: OS × modül

Satırlar = OS hedefi (02 §2.3 derleme matrisinin satırları), sütunlar = F1 modülleri
(01 §4.1 katman karşılıkları). Hücre = o uçta **beklenen** davranış (henüz koşulmadı).

| OS hedefi (artefakt adı — 02 §3.3) | resolver | enum | connect | status | profile | bridge (WS 9742) |
|---|---|---|---|---|---|---|
| **XP SP3 x86** — `neva-wlan_x86_xp.exe` | 36 export tabanı; `HostedNetwork/WFD/List2` **kapalı** | `WlanGetAvailableNetworkList` (List1); BssList → `ERROR_NOT_SUPPORTED` → L1 | 20 fonksiyon beyaz liste (03 §3.1) | ACM 2 bildirim (03 §6.1 P2) | XP adı = SSID kuralı; WPA2-PSK | 9742 dinler (F4 fazı) |
| **Win7 SP1 x64** — `neva-wlan_x64_win7.exe` | 48 export; `HostedNetwork` **açık** (∧ adaptör koşulu) | List1 | connect + disconnect | ACM + HNWK | profil CRUD | aynı |
| **Win8.1 x64** — `neva-wlan_x64_win81.exe` | 108 export; WFD açık (`WFDOpenHandle`) | List1; List2 export varsa **List1'e yedekli** (03 §3.7) | connect | ACM + HNWK + MSM | profil CRUD | aynı |
| **Win10 19041 x64** — `neva-wlan_x64_win10.exe` | 148 export; HS2.0 profil şeması açık | **List2** tercih → hata olursa sessiz List1 | connect | tüm kaynaklar | profil CRUD + HS2.0 (kabul edilirse) | aynı |

**Matris notları:**

1. **Ek uçlar (matris dışı, koşulacak):** `neva-wlan_x64_xp.exe` (XP x64 SP2) ve
   `neva-wlan_x64_win11.exe` — 02 §7.1'de XP-x64 ortamı `⚠️ VERIFICATION REQUIRED`
   ("ortam mevcut değil") ve Win11 24H2 erişimi açık konudur → bu plan §9 #1/#2'ye yazar.
2. **Beş satır varsayımı:** CI matrisi §7'de 5 satır (x86_xp, x64_win7, x64_win81,
   x64_win10, x64_win11); `x64_xp` CI satırı **dışarıda** (02 §7.1 ortam yokluğu gerekçesiyle).
3. **Modül ↔ artefakt bağımsızlığı:** her hücre aynı kod tabanını (tek `src/` — 02 §6 kural 1)
   farklı define ile test eder; **kod kopyalama yok**, ayrışma yalnız `build/*.props`.
4. Bu matrisdeki hiçbir hücre için **sonuç yazılmamıştır**; dolu hücre = beklenti, boş =
   çalıştırılmadı.

### §1.3 Test altyapısı (proje standardı — disk kanıtı)

| Öğe | Değer | Kaynak (dosya+satır) |
|---|---|---|
| Test çatısı | **Google Test** (gtest ≥1.14) | `E:\Belgeler\coremusic.net.old\cpp-projects\CLAUDE.md:341` (vcpkg manifest `gtest 1.14.0`) · `:425-447` (Unit Testing / Google Test) |
| Test koşucusu | **ctest** (`ctest --output-on-failure -j 8`, `ctest -R <regex>`) | `cpp-projects/CLAUDE.md:117-124` |
| Derleme | **CMake 3.20+ / C++20 / MSVC 2022 / vcpkg toolchain** | `cpp-projects/CLAUDE.md:247-269` · `:32-39` |
| Uyarı seviyesi | **`/W4 /WX /permissive-`** (MSVC) | `cpp-projects/CLAUDE.md:273` |
| Ek araçlar | spdlog (log), nlohmann-json (JSON) | `cpp-projects/CLAUDE.md:334-338` |
| ctest referansı | — | https://cmake.org/cmake/help/latest/manual/ctest.1.html [K1] |
| gtest referansı | — | https://github.com/google/googletest [K2] · CMake quickstart: https://google.github.io/googletest/quickstart-cmake.html [K3] |
| vcpkg CMake entegrasyonu | toolchain dosyası | https://learn.microsoft.com/en-us/vcpkg/users/buildsystems/cmake-integration [K4] |

**Test dosya yapısı (öneri — 02 §6'daki `tests/` dizinine dayanır, kod YOK):**

```text
tests/
├─ unit/            # §2 — sahte export tablosu, gerçek wlanapi ÇAĞRISI YOK
│   ├─ resolver_table_test.cpp        (U01-U12)
│   ├─ handle_raii_test.cpp           (U13-U18)
│   ├─ notify_queue_test.cpp          (U19-U24)
│   ├─ cmd_json_schema_test.cpp       (U25-U30)
│   ├─ profile_xml_test.cpp           (U31-U36)
│   ├─ error_translate_test.cpp       (U37-U40)
│   └─ enum_snapshot_test.cpp         (U41-U45)
├─ integration/     # §3 — gerçek wlansvc, yerel makine
└─ fixtures/        # sentetik WLAN_AVAILABLE_NETWORK girdileri + beklenen JSON snapshot'ları
```

**Test çifti (double) politikası:** mock hell YASAK — sahte **export tablosu**
(isim → işaretçi eşlemesi) ve **sentetik `WLAN_*` struct'ları** kullanılır; hazır
mock framework'ü ile spec-mocking yapılmaz (gerçekçi test double kuralı).

### §1.4 Risk registerı (bu planın kendi riskleri)

| # | Risk | Olasılık | Etki | Azaltıcı önlem |
|---|---|---|---|---|
| R1 | XP laboratuvarı hiç kurulamaz | Orta | Yüksek — P1/P2/N1 + X-satırı koşulamaz | §9 #1 kararı; Plan B: Win7 = en eski **koşulabilir** uç |
| R2 | GHA `v141_xp` kurulamaz | Yüksek (kanıtlı [K28]/[K29]) | Orta — XP derlemesi CI'da yok | self-hosted XP/Win10 makine (02 §3.4 kararı) |
| R3 | Coverage aracı seçilmez | Orta | Düşük — §6.b kapı tanımsız kalır | §9 #3 kararı; sahte percentage YOK |
| R4 | Test AP'sı bulunamaz | Orta | Orta — I5/S9-S10 `UNKNOWN` | Telefon hotspot'u; AP yoksa adımlar `UNKNOWN` yazılır |
| R5 | gtest v141_xp'te derlenmez | Bilinmiyor (`UNKNOWN`) | Yüksek — XP'de birim test yok | §9 #10 teyidi; gerekirse XP'de yalnız smoke |
| R6 | Köprü (WS 9742) gecikirse | Orta | Düşük — I3/I4 bekler | I3/I4 faz bağımlı işaretlenir; F4'ten önce BLOCK sayılmaz |
| R7 | XP ağında modern AP (WPA2-only) | Orta | Orta — XP connect senaryosu | §9 #2; XP'nin WPA2 uyumu `⚠️ VERIFICATION REQUIRED` |
| R8 | Flaky stres testi (U19/U23) | Düşük | Yüksek (retry yasak) | Deterministik olay sayısı + sabit seed; zaman-sınırı testi YOK |

---

## §2 Birim Testleri (45 senaryo — hedef ≥ 40)

> Format: `ID | hedef | Arrange-Act-Assert (kısa) | beklenen`. Tümü §2'nin sonundaki
> ortak kabul kriterine bağlıdır. **Hiçbiri henüz yazılmamıştır/koşulmamıştır.**
> Aşağıdaki "beklenen"ler 01/02/03 dokümlerinden türetilmiş **taleplerdir**, sonuç değildir.

### §2.1 Resolver (sahte modül tablosu) — U01..U12

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U01 | 0 export tablosu | Arrange: sahte tablo = boş küme · Act: `featureAvailable(Core/HostedNetwork/WifiDirect/NetworkListV2)` · Assert: hepsi false | Tüm feature'lar **kapalı**; `loaded()` false ise istisna **yakalanabilir** (L1) |
| U02 | Kısmi XP seti (36 export) | Arrange: 36 isimlik tablo (01 §3) · Act: `featureAvailable` · Assert: Core **true**, HostedNetwork/WifiDirect **false** | 03 §3.8 XP satırı birebir: HN ✖ · WFD ✖ · çekirdek ✔ |
| U03 | Tam set (148 export, 19041) | Arrange: 148 isim tablosu · Act: feature sorgusu · Assert: Core/HN/WFD/List2 **true** | 03 §3.8 Win10-19041 satırı ✔ ✔ ✔ ✔ |
| U04 | MSDN beyaz listesi 20/20 | Arrange: 20 isim tam (03 §3.1) · Act: `featureAvailable(Core)` · Assert: true | 20/20 mevcut → Core açık |
| U05 | Kısmi beyaz liste 19/20 | Arrange: tek core export (`WlanScan`) tablodan çıkarılır · Act: Core sorgusu · Assert: **false** | Eksik tek core export Core'ı kapatır (03 §4.3 `for (bool b : corePresent_)`) |
| U06 | Bilinmeyen export `has()` | Arrange: dolu tablo · Act: `has("WlanDoesNotExist")` · Assert: false | `GetProcAddress` null → false; **çökme yok** |
| U07 | `require()` null çağrısı | Arrange: export'u olmayan tablo · Act: `require("WFDOpenHandle", Feature::WifiDirect)` · Assert: `FeatureNotSupported` fırlatır | **L1 istisna, çökme YOK** (01 §5.1 L1; 03 Y1) |
| U08 | null fonksiyon çağrısı (Y1) | Arrange: null işaretçiden çağrı yolu · Act: `get<Fn>()` üzerinden **çağrılmadan önce** kontrol · Assert: istisna öncesi yakalama | Null fn asla çağrılmaz → `0xC0000139`/AV **gözlenmez** (03 §6.2 kabul kriteri) |
| U09 | DualSta kapalı | Arrange: 149 export tablosu (Win11) · Act: `featureAvailable(DualSta)` · Assert: **false** | 03 §3.4 kararı: doğrulanana kadar KAPALI |
| U10 | BssList çift kapı | Arrange: XP tablosu + `WlanGetNetworkBssList` **var** · Act: feature true; sarmalayıcı `ERROR_NOT_SUPPORTED` döndürür · Assert: L1 istisna | Export ≠ destek (03 §2.4): kapı ikili, çökme yok |
| U11 | Eşzamanlı `instance()` | Arrange: 32 thread · Act: hepsi aynı anda `instance()` · Assert: tek örnek, tekrar tablo doldurma yok | magic-static tek instance; data race yok (03 §6.2 N9) |
| U12 | Loader başarısız | Arrange: `LoadLibraryW` sahtesi null · Act: tüm feature sorguları · Assert: hepsi false + L1 mesajı | `wlanapi.dll yok` durumu; süreç yaşar (03 §6.2 N6) |

### §2.2 RAII handle — U13..U18

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U13 | Erken return | Arrange: sayaçlı sahte `WlanOpenHandle`/`CloseHandle` · Act: fonksiyon **erken return** ile çıkar · Assert: close == open | Kapanış çağrısı = açılış çağrısı (1:1) |
| U14 | İstisna yayılımı (unwind) | Arrange: open sonrası istisna fırlatılır · Act: stack unwind · Assert: destructor close çağırır; sayı eşit | Aynen 1:1 — **sızıntı yok** |
| U15 | Çift kapanış | Arrange: aynı handle iki kez sarmalanır · Act: ikinci destructor · Assert: ikinci close **çağrılmaz** | Handle tekrar kapatılmaz (double-close guard) |
| U16 | Bellek RAII | Arrange: `WlanAllocateMemory`/`FreeMemory` sayacı · Act: hata yolunda çıkış · Assert: free == allocate | WLAN bellek havuzu dengeli (01 §4.1 wlacore) |
| U17 | Taşıma semantiği | Arrange: RAII sarmalayıcı taşınır · Act: kaynak yeni sahibe geçer · Assert: eski sahip kapanış **çağırmaz**, yeni sahip kapanır | Tek sahiplik korunur (cpp-projects `std::unique_ptr` kuralı — `CLAUDE.md:674`) |
| U18 | Kapanış sırası (bildirim) | Arrange: kayıt yapılmış handle · Act: yıkıcı · Assert: sıra = `WlanRegisterNotification(NONE)` → `WlanCloseHandle` → **DLL boşaltma YOK** | 03 §4.6 sırası; callback asla unload kodu çağırmaz |

### §2.3 Bildirim kuyruğu (SPSC) — U19..U24

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U19 | SPSC stres 100k | Arrange: tek producer + tek consumer, 100 000 olay · Act: eşzamanlı üret/tüket · Assert: alınan == üretilen | Kayıp **0**; sıra bozulmaz (fazla okuma yok) |
| U20 | Underflow | Arrange: boş kuyruk · Act: consumer `try_pop` · Assert: false döner, **bloklanmaz/çökmez** | Boş okuma güvenli (lock-free kural — 01 D5) |
| U21 | Overflow | Arrange: kapasite dolu · Act: producer `try_push` · Assert: false + drop sayacı artar | Düşen olay **sayılır** (sessiz kayıp yasak) |
| U22 | Kapanış sırası | Arrange: üretim sürüyor · Act: shutdown · Assert: unregister → drain → close sırası izlenir | 03 §4.6 sırası; drain sonrası kalan olay 0 |
| U23 | Ayrılma yarışı | Arrange: consumer kapanırken producer push · Act: 10⁴ döngü · Assert: use-after-free/AV **yok** | 01 D4 (WlanCloseHandle race gotcha'sı) nüksü yok |
| U24 | Callback içinden çağrı | Arrange: callback gövdesinden `WlanRegisterNotification` denemesi · Act: çağır · Assert: **engellenir** (kilit/Deadlock savunması) | 03 §4.6: callback içi kayıt = deadlock → kodda yasak, testte yakalanır |

### §2.4 Komut/JSON şeması — U25..U30

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U25 | Eksik alan | Arrange: `{"cmd":"connect"}` (ssid yok) · Act: ayrıştır · Assert: hata sonucu döner | L2 sınıfı hata mesajı; çökme yok |
| U26 | Bilinmeyen cmd | Arrange: `{"cmd":"teleport"}` · Act: ayrıştır · Assert: "bilinmeyen komut" sonucu | Geçersiz komut reddedilir; süreç yaşar |
| U27 | Unicode SSID (UTF-8) | Arrange: `Wi-Fi Kahve` (UTF-8, 12 bayt) · Act: ayrıştır → struct → tekrar serialize · Assert: byte-birebir korunur | UTF-8 kayıpsız (çift taraflı) |
| U28 | 32-byte sınırı | Arrange: SSID tam **32 bayt** · Act: doğrula · Assert: **kabul** | 802.11 SSID üst sınırı dolu ama geçerli |
| U29 | 33-byte taşma | Arrange: SSID **33 bayt** · Act: doğrula · Assert: **ret** (L2 mesaj) | Sınır aşılamaz; çökme yok |
| U30 | Emoji SSID | Arrange: SSID = 4-byte UTF-8 emoji ×8 (32 bayt) · Act: ayrıştır/serialize · Assert: bayt-bayt eşit, karakter sayısına göre **değil** | Sınır bayt cinsindendir; emoji kırpması bilinçli reddedilir |

### §2.5 Profil XML üretimi — U31..U36

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U31 | WPA2-PSK üretimi | Arrange: SSID + 8-63 karakterlik PSK · Act: XML üret · Assert: `authEncryption` WPA2/PSK, `keyType` passphrase | Şemaya uyar (03 §3.5 A1; şema §10 [K10]) |
| U32 | XML kaçış (özel karakter) | Arrange: SSID = `A&B<C>D"E` · Act: üret · Assert: `&amp;` `&lt;` `&gt;` `&quot;` | Bozuk XML **üretilmez**; ayrıştırıcı kabul eder |
| U33 | XP profil adı = SSID | Arrange: XP hedefi · Act: profil adı seç · Assert: ad == SSID | 03 §3.1 kısıtlama #3 (MSDEN XP listesi) |
| U34 | Boş SSID | Arrange: `""` · Act: üret/ayarla · Assert: ret + L2 mesaj | Geçersiz girdi reddedilir |
| U35 | Profil adı çakışması | Arrange: aynı adla mevcut profil · Act: `WlanSetProfile` (overwrite yok) · Assert: kontrollü ret veya overwrite bayrağı davranışı | Davranış **belgelenir** → `⚠️ VERIFICATION REQUIRED` (semantiği MSDN teyidi, §9 #6) |
| U36 | HS2.0 şeması (yalnız Win10+) | Arrange: HS2.0 `ANQP` alanlı XML · Act: Win10 hedefinde üret · Assert: şema kabul; alt sürümde **ret + mesaj** | 03 §3.5 A1: şema sürüm reddi = L1 kullanıcı mesajı |

### §2.6 Hata çevirisi (TR mesaj) — U37..U40

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U37 | Level 0-3 mesaj kataloğu | Arrange: her Level için anahtar · Act: kataloğu tara · Assert: her anahtarın TR metni var | Eksik anahtar = test hatası (katalog bütünlüğü) |
| U38 | L1 mesajları | Arrange: eksik export senaryoları (HN/WFD/List2) · Act: hata üret · Assert: metin "desteklenmiyor" içerir | 03 §4.3 `makeMessage` biçimi: `"<fn> desteklenmiyor: <sebep>"` |
| U39 | L2 servis hatası | Arrange: `ERROR_SERVICE_NOT_ACTIVE` / `ERROR_NOT_READY` · Act: çevir · Assert: "Kablosuz ağ servisi başlatılamadı (WZC / WLAN AutoConfig)" | 02 §5.2 satırı (kaynaklı mesaj) |
| U40 | Reason code çevirisi | Arrange: ≥5 `WLAN_REASON_CODE` örneği · Act: `WlanReasonCodeToString` karşılığı · Assert: boş/`0` dönmez | §10 [K11]; boş çeviri = fail |

### §2.7 Enum → JSON snapshot — U41..U45

| ID | Hedef | Arrange-Act-Assert | Beklenen |
|---|---|---|---|
| U41 | Sentetik girdi → JSON | Arrange: **sentetik** 3 `WLAN_AVAILABLE_NETWORK` (dolu/boş/hidden) · Act: enum→JSON · Assert: snapshot eşleşir | Şema kararlılığı (snapshot testi) |
| U42 | Gerçekçi alan dolgu | Arrange: struct alanları gerçekçi değerlerle (signal, security, BSS type) · Act: serialize · Assert: hiçbir alan `UNKNOWN` basılmaz | Eksik alan açıkça işaretli |
| U43 | Sıralama | Arrange: girdi sırası biliniyor · Act: serialize · Assert: çıktı sırası girdiyle aynı | Kararsız sıralama = flaky kaynağudur; sıralama sabitlenir |
| U44 | Boş liste | Arrange: 0 ağ · Act: serialize · Assert: `[]` + boş durum bayrağı | Boş durum UI tarafından ayırt edilebilir |
| U45 | Emoji/unicode round-trip | Arrange: UTF-8 SSID'li girdi · Act: JSON'a yaz → geri oku · Assert: bayt eşitliği | §2.4 U27 ile çapraz koruma |

> **Veri kaynaklı değil uyarısı (U41-U45):** bu satırlardaki girdiler **sentetiktir** —
> gerçek ortamdan yakalanmış `WLAN_AVAILABLE_NETWORK` örneği elde **edilmemiştir**
> (`UNKNOWN`). Gerçek-ortam snapshot'ı için §3 I-serisi gerekir; gerçek veriye dayanmayan
> hiçbir snapshot "gerçekçi" diye satılmaz.

**§2 ortak kabul kriteri (tümü için):** hiçbir senaryoda süreç öldürülmez; `0xC0000139`,
`0xC0000005`, `bad_alloc` sızması gözlenmez; her test kendi verisini üretir (PII/secret 0);
always-true assertion 0; suite < 5 sn.

---

## §3 Entegrasyon Testleri (gerçek wlansvc — yerel makine)

> Bu katman **GHA `windows-2022` runner'ında ÇALIŞMAZ** (§7 — [K30] wlansvc yok).
> Yerel Windows 10/11 makinede koşar; koşum sırasında **gerçek AP erişimi** §9 #2'ye bağlıdır.

| ID | Hedef | Arrange-Act-Assert | Beklenen / not |
|---|---|---|---|
| I1 | Gerçek handle aç/kapa | Arrange: gerçek `wlanapi.dll` · Act: open → enum → close ×100 · Assert: her seferinde `ERROR_SUCCESS`, close dengesi | Handle sızıntısı yok (§4 sütun: Process Explorer) |
| I2 | Gerçek tarama | Arrange: sürücülü adaptör · Act: `WlanScan` + `WlanGetAvailableNetworkList` · Assert: liste döner (ağ yoksa 0 + temiz durum) | 0 ağ ortamında da **çökme yok** |
| I3 | WS 9742 köprüsü — gönder | Arrange: köprü başlat (9742) · Act: test client `wifi_list` gönder · Assert: JSON yanıt gelir | Köprü kodu 01 §4.3 **F4 fazında**; bu test planı bekler (**kod YOK**) |
| I4 | WS 9742 köprüsü — al/yanıtla | Arrange: bağlı client · Act: `connect` + `status` komutları · Assert: sıra korunur, yanıt süresi ölçülmüştür (değer `UNKNOWN`) | Port tahsisi: ADR 031 tablosu (cpp-projects `CLAUDE.md:209-215`) |
| I5 | Notification (gerçek AP) | Arrange: bilinen AP'ye bağlan → kopar · Act: ACM bildirimlerini bekle · Assert: ≥2 olay (bağlantı tamam + kopma) | 03 §6.1 P2 ile aynı beklenti |
| I5n | Notification **AP yokken** | Arrange: AP'siz ortam · Act: bildirim yolu · Assert: — | **`⚠️ VERIFICATION REQUIRED`** — (a) bildirim enjeksiyonu için public API olup olmadığı **doğrulanmadı**; (b) XP'de Netman servisi kapalıysa bildirim hiç gelmez (03 §3.1 [K7]) → bu ortamda test **"bildirim gelmedi" diye FAIL sayılmaz**, sonuç `UNKNOWN` yazılır |
| I6 | Profil yaz | Arrange: test SSID **`NevaTest_XXXX`** (XXXX = koşu no) · Act: `WlanSetProfile` + listede ara | Profil listede görünür |
| I7 | Profil sil (cleanup zorunlu) | Arrange: I6 profili · Act: `WlanDeleteProfile` + tekrar liste · Assert: **artık yok** | **Temizlik garantisiz I6/I7 PASS sayılmaz**; temizlik adımı §4 S14'te zorunlu |
| I8 | Kirlenmiş ortam temizliği | Arrange: önceki koşudan kalmış `NevaTest_*` · Act: listeden bul → sil · Assert: hepsi silinir | Yarıda kalan koşu sonrası da ortam temizlenir |
| I9 | Yöneticisiz profil yazımı | Arrange: standart kullanıcı · Act: profil yaz · Assert: hata mesajı + çökme yok | Hangi `ERROR_*` döndüğü `⚠️ VERIFICATION REQUIRED` (§9 #7) |
| I10 | Radyo kapalı | Arrange: adaptör devre dışı · Act: enum/connect · Assert: L2 mesaj, süreç yaşar | 02 §5.2 "Kablosuz adaptör bulunamadı" hattı |

**Test-SSID kuralları:** ön ek `NevaTest_` sabit (§3 I6-I8), dört hane koşu numarası;
**her koşu kendi verisini üretir**; gerçek kullanıcı profiline/SSID'ine dokunulmaz;
PSK değeri test-only ve log'a yazılmaz (§6.d).

---

## §4 OS-Uç Smoke Testleri (checklist)

> Her satır = bir adım; her OS = kendi tablosu. **Tüm hücreler BOŞTUR** (plan durumu;
> doldurulması §8 raporu ile olur). Adımlar 02 §7.1 kabul kriterleriyle hizalıdır.

### §4.1 Ortak adım tablosu (tüm OS'lar — 15 adım)

| Adım | Eylem | Otomatik/Manuel | Beklenen kayıt |
|---|---|---|---|
| S1 | Doğru artefaktı kopyala (`neva-<mod>_<arch>_<os>.exe` — 02 §3.3) | Manuel | Dosya adı matristen okunur, tahmin edilmez |
| S2 | MD5/SHA-256 + manifest doğrula (02 §7.3) | Otomatik | Manifestteki hash ile eşit |
| S3 | `dumpbin /imports` + `/headers` (§6.c) | Otomatik | XP'de Vista+ import YOK; subsystem 5.01 |
| S4 | EXE'yi başlat | Otomatik | Çıkış kodu + ilk log satırı |
| S5 | WS 9742 yukarı | Manuel/Otomatik | Port dinlemede (02/ADR 031) |
| S6 | `wifi_list` (enum) | Otomatik | JSON liste döner |
| S7 | `scan` tetikle | Otomatik | Tarama tamamlanır / temiz hata |
| S8 | `status` | Otomatik | Çıkış kodu 0 (02 §7.1 kabul kriteri) |
| S9 | `connect` (bilinen AP — §9 #2'ye bağlı) | Manuel | Bağlantı kurulur veya L2 mesajı |
| S10 | `event` (notification gözlemi) | Manuel | ≥1 olay ya da §3 I5n `UNKNOWN` notu |
| S11 | `disconnect` | Otomatik | Kopma bildirimi / temiz durum |
| S12 | Profil CRUD (`NevaTest_XXXX` yaz/listele/**sil**) | Otomatik | Silme sonrası listede yok (§3 I7) |
| S13 | Hata enjeksiyonu: radyo kapalı | Manuel | L2 mesaj, **çökme 0** |
| S14 | Hata enjeksiyonu: yönetici olmayan | Manuel | L2 mesaj, **çökme 0** (kod `UNKNOWN` — §9 #7) |
| S15 | Temizlik + kapanış + handle leak gözlemi | Manuel | §4.4 |

### §4.2 XP SP3 x86 satırı (artefakt `neva-wlan_x86_xp.exe` — 15 adım + 4 ek)

| Adım | XP'ye özgü ek kontrol | Beklenen |
|---|---|---|
| X1 | **WZC servis kontrolü** — Wireless Zero Configuration servisinin durumu okunur | Servis çalışır değilse `WlanOpenHandle` L2 hatası **beklenidir** (03 §3.1 [K4]); servis kısa adı **`wzcsvc`** — disk kanıtı: nt5src `eapol/service/eapolutil.c` `#define WZCSVC_SERVICE_NAME L"WZCSVC"` + `netwzc.inx` (07 §2); XP üzerinde `sc query wzcsvc` ile teyit §9 #5 |
| X2 | **x86-only** — 64-bit artefakt XP satırına **konmaz** | 02 §3.3 kuralı: yalnız `_x86_xp` |
| X3 | **`/MT`** doğrulaması — `dumpbin /dependents` | `msvcp140.dll`/`vcruntime140.dll` **görünmez** (02 §7.2 tablo, CRT satırı) |
| X4 | **`v141_xp` + subsystem 5.01** — manifest `imports_gate` alanı | 02 §7.3 manifest: `toolset: v141_xp`, `subsystem: 5.01`, `imports_gate` değeri doldurulur |
| X4b | **Felaket yolu smoke (09 §7 T-04)** — `net stop wzcsvc` → `wifi_scan` | WZC yokken NDISUIO OID yolu devreye girer ya da L2 mesaj döner; **çökme 0** (09 §6 karar akışı) |
| X5.. | Ardından ortak S1-S15 (yalnız 36 export'un izin verdiği adımlar) | 02 §7.1: "36 export dışına çağrı yok · crash yok" |

> XP'de **giriş/çıkış sınırlaması:** v141_xp static analysis desteklemez (02 §2.1 #5) →
> X-satırında analiz adımı **yoktur**; analiz modern satırda koşar (§6.a).

### §4.3 OS tablosu (koşum kaydı — boş, plan durumu)

| OS | Artefakt | Sürüm/SP | Commit | Sonuç | Bulgu | Tarih | Koşan |
|---|---|---|---|---|---|---|---|
| Windows XP SP3 x86 | `neva-wlan_x86_xp.exe` | — | — | **— (koşulmadı)** | — | — | — |
| Windows 7 SP1 x64 | `neva-wlan_x64_win7.exe` | — | — | **— (koşulmadı)** | — | — | — |
| Windows 8.1 x64 | `neva-wlan_x64_win81.exe` | — | — | **— (koşulmadı)** | — | — | — |
| Windows 10 19041 x64 | `neva-wlan_x64_win10.exe` | — | — | **— (koşulmadı)** | — | — | — |
| Windows 11 24H2 x64 | `neva-wlan_x64_win11.exe` | — | — | **— (koşulmadı)** | — | — | — |

### §4.3b OS bazlı ek notlar (koşum öncesi okunur)

| OS | Ek kontrol | Beklenen |
|---|---|---|
| Win7 | HostedNetwork erişilebilirliği (02 §7.1 kabul kriteri): `QueryStatus` çağrısı | Adaptör capable ise aktif durum; değilse "donanım desteklemiyor" (03 §3.2 H6) — **çökme yok** |
| Win7 | List2 export'u **yok** → S6 otomatik List1'e düşmeli | UI farkı 0 (03 §3.7 madde 4) |
| Win8.1 | List2 export'u **var ama destek belgelenmemiş** → S6 sonucu kayıt altına alınır | List1 fallback + `⚠️ VERIFICATION REQUIRED` telemetrisi (03 §3.7 madde 3) |
| Win8.1 | `WFDOpenHandle` (`WFD_API_VERSION` müzakeresi) — 03 §6.1 P4 | `ERROR_SUCCESS`; oturum başlat/iptal temiz |
| Win10 19041 | List2 yolu — 03 §6.1 P6 | `WLAN_AVAILABLE_NETWORK_LIST_V2` döner; fallback tetiklenmez |
| Win10 19041 | HS2.0 profil (`ANQP` alanlı XML) — 03 §6.1 P5 | `WlanSetProfile` kabul; reddedilirse L1 mesajı |
| Win11 | Dual-STA **kapalı** — 03 §6.1 P7 | Hata üretmez; özellik menüde pasif |
| Win11 | F2 (`neva-wifi7.exe`) **bu planda kapsam dışı** (04 doc konusu) | WLAN smoke'u yalnız `neva-wlan_*` içerir |

### §4.4 Handle leak gözlemi (S15)

| Araç | Kullanım | Kaynak |
|---|---|---|
| **Process Explorer** | Süreç → handle sayısının 100× open/close döngüsü (I1) sonrası **artmadığını** gözle; DLL sekmesinde `wlanapi.dll` yüklü kalır (03 §4.6: asla unload edilmez — bu **normaldir**, sızıntı DEĞİL) | https://learn.microsoft.com/en-us/sysinternals/downloads/process-explorer [K12] |
| **Handle.exe** | Konsoldan handle dökümü: WLAN handle'ları döngü sonunda 0'a iner | https://learn.microsoft.com/en-us/sysinternals/downloads/handle [K13] |
| Sysinternals ana sayfa | Araç seti geneli | https://learn.microsoft.com/en-us/sysinternals/ [K14] |

**Yorum kuralı:** `wlanapi.dll`'in yüklü kalması **kasıtlı tasarım**dır (03 §4.6 — asla
`FreeLibrary`); sızıntı ölçütü yalnızca **WLAN handle sayısındaki artıştır**.

---

## §5 Negatif & Çökme Testleri (03 doc kapısı — Y1 çökme yasağının kanıtı)

### §5.1 Her desteklenmeyen özellik → mesaj + fallback + crash 0

| # | Desteklenmeyen özellik (ortam) | Tetikleme | Beklenen mesaj (TR) | Fallback | Crash |
|---|---|---|---|---|---|
| N1 | `WlanGetNetworkBssList` (XP) | Sarmalayıcı doğrudan çağrılır | "…desteklenmiyor (ERROR_NOT_SUPPORTED)" | Durum raporu; süreç yaşar | **0** |
| N2 | HostedNetwork (XP) | `featureAvailable` → çağrı **denenmez** | "Bu Windows sürümünde hotspot özelliği desteklenmiyor" | Menü gizle/pasif (02 §5.2) | **0** |
| N3 | Wi-Fi Direct (XP/Win7) | Aynı | "Bu Windows sürümünde Wi-Fi Direct yok" | Kullanıcıya bilgi, devam | **0** |
| N4 | HS2.0 profil şeması (Win8.1) | `WlanSetProfile` ret | "Profil şeması bu Windows sürümünde desteklenmiyor" | Geri çekil + mesaj | **0** |
| N5 | List2 yok (Win7) | Export null | (sessiz) List1'e düş | UI farkı 0 (03 §3.7 madde 4) | **0** |
| N6 | List2 var ama çağrı hata (Win8/8.1) | Dönüş kodu hatası | (sessiz) List1 + telemetri | List1 | **0** |
| N7 | Dual-STA (Win11) | Her koşulda `false` | "Bu özellik henüz desteklenmiyor" | Kapalı (03 §3.4 D3) | **0** |
| N8 | `wlanapi.dll` yok | Dizinden kaldır/blokla | "Kablosuz arayüz kütüphanesi bulunamadı (wlanapi.dll)" | Durum raporu (02 §5.2) | **0** |
| N9 | **WZC servisi durdurulmuş (`net stop wzcsvc`)** — XP | `wifi_scan` çağrısı | Felaket yolu devreye girer (09 §6) ya da "Kablosuz alt sistemi kullanılamıyor…" L2 mesajı | 09 §7 T-04/T-09 (normal yola dönüş log kanıtlı) | **0** |
| N9 | Siyah liste ihlali (03 §2.5 16 fn) | Bilinçli çağrı denemesi | `FeatureNotSupported` | CI grep (N10) + runtime kapı | **0** |
| N10 | Çift giriş ihlali (Y3) | `GetProcAddress("Wlan` grep'i | — | **CI FAIL** (yalnız `resolver.cpp`) | — |
| N11 | Eşzamanlı ilk çağrı | 32 thread `instance()` | — | Tek tablo, data race yok | **0** |
| N12 | Bildirim yarışı | kapanış sırasında bildirim | — | 03 §4.6 sırası korunur | **0** |

**Kabul kriteri (03 §6.2 aynen):** hiçbir negatif testte `0xC0000139` (import trambolini),
`0xC0000005` veya process-kill gözlenmez; tüm olumsuz durumlar `FeatureNotSupported`
ya da dönüş kodu durumundadır. **Çökme sayısı hedefi = 0.**

### §5.2 Sayfa hatası (AV) enjeksiyonu — neden yapılmıyor?

| Seçenek | Değerlendirme | Karar |
|---|---|---|
| Gerçek AV/sayfa hatası enjeksiyonu | Deterministik **değil** (zamanlama yarışı); XP/Win7 makinelerde ek araç kurulumu gerekir; neden-sonuç ilişkisi okunamaz | **YAPILMAZ** |
| Sanal/ölüm (kernel) crash aracı (ör. NotMyFault — **bilgi, kullanılmayacak**) | Bütün sistemi çökertir; birim/entegrasyon suite'i için aşırı | Kullanılmaz; https://learn.microsoft.com/en-us/sysinternals/downloads/notmyfault [K15] sadece **kaynak** |
| **Sahte export tablosu + null-fonksiyon kapısı (U01-U08, N8)** | Aynı kod yolunu kapsar: null/eksik → L1 istisna → süreç yaşar; **tamamen deterministik**, %100 tekrarlanabilir | **TERCİH EDİLİR — yeterli gerekçe:** hedeflenen risk (null çağrı = `0xC0000139`) sahte tabloyla **birebir** tetiklenir |
| SEH son savunma hattının (02 §5.4) ayrıca test edilmesi | Bilinçli `nullptr` okuma ile tetiklenebilir — ancak `#if`/toolset farkları XP'de davranışı değiştirebilir | Koşul: modern uçta 1 senaryo; XP'de `⚠️ VERIFICATION REQUIRED` |

---

## §6 Kalite Kapıları (Gates)

### §6.a Kod kalitesi

| Kapı | Araç / bayrak | Kaynak | Kabul | Not |
|---|---|---|---|---|
| Uyarı seviyesi | **MSVC `/W4 /WX /permissive-`** | `cpp-projects/CLAUDE.md:273` · https://learn.microsoft.com/en-us/cpp/build/reference/compiler-option-warning-level?view=msvc-170 [K5] | 0 uyarı (WX = hata) | Proje standardının birebir uygulanması |
| MSVC kod analizi | `/analyze` (Code Analysis) | https://learn.microsoft.com/en-us/cpp/build/reference/analyze-code-analysis?view=msvc-170 [K6] · genel: https://learn.microsoft.com/en-us/cpp/code-quality/code-analysis-for-c-cpp-overview?view=msvc-170 [K7] | 0 C6xxx (yüksek önem) | **XP satırında ÇALIŞMAZ** — v141_xp static analysis desteklemez (02 §2.1 #5, T4) → analiz **modern toolset'te** ayrı koşul |
| Bağımsız statik analiz | **cppcheck** | https://cppcheck.sourceforge.io/ [K8] · kılavuz: https://cppcheck.sourceforge.io/manual.pdf [K9] | 0 `error` sınıfı bulgu | Cross-platform; XP define'ları ile koşulur mu → `⚠️ VERIFICATION REQUIRED` |
| Statik analiz (stil/bug) | **clang-tidy** | https://clang.llvm.org/extra/clang-tidy/ [K10] · kontroller: https://clang.llvm.org/extra/clang-tidy/checks/list.html [K11] | 0 `bugprone-*` bulgu | MSVC header + `_WIN32_WINNT` ayarı gerekir; v141_xp hedefinde uyumu `⚠️ VERIFICATION REQUIRED` |
| Çift giriş denetimi (Y3) | CI grep: `GetProcAddress("Wlan` yalnız `resolver.cpp` | 03 §6.2 N10 | Hits yalnız izinli dosya | Aksi → pipeline FAIL |

### §6.b Test coverage hedefi

| Madde | Durum |
|---|---|
| **Proje standardı ne diyor?** | `cpp-projects/CLAUDE.md:126-129` coverage komutlarını verir (`ctest --coverage`, `lcov`, `genhtml`) **ama yüzde hedefi YAZMAZ**. Ayrıca `ctest --coverage` ctest'in resmi seçenek listesinde geçmiyor olabilir → **`⚠️ VERIFICATION REQUIRED`** (ctest.1.html teyidi: https://cmake.org/cmake/help/latest/manual/ctest.1.html [K1]) |
| **Önerilen teklif (bağlayıcı değil, onay bekliyor)** | **Satır ≥ %80 · dal ≥ %75** — CoreMusic QA kuralı (satır %80 altı = merge BLOCK). Kök cpp standardı yüzde vermediği için bu değer **tekliftir**; onay: kullanıcı |
| **Araç (Windows/MSVC)** | `lcov`'un Windows-MSVC native karşılığı **doğrulanmadı** (`UNKNOWN`). Aday: **OpenCppCoverage** (https://github.com/OpenCppCoverage/OpenCppCoverage [K12]) — MSVC/PDB tabanlı. Alternatif: **WSL** içinde lcov (https://learn.microsoft.com/en-us/windows/wsl/install [K13]) — **ama** WSL, Windows binary'sinin satır kapsamını doğrudan ölçemez → bu kombinasyon `⚠️ VERIFICATION REQUIRED` |
| **Ölçüm yok = kapı uygulanamaz** | İlk coverage ölçümü yapılana kadar kapı **"tanımsız"** durumdadır; sahte yüzde **yazılmaz** |

### §6.c Artefakt kapısı (02 §7.2/§7.3 — XP satırında ZORUNLU)

| Kapı | Komut | Kabul | Kaynak |
|---|---|---|---|
| Import temizliği | `dumpbin /imports dist\neva-wlan_x86_xp.exe > …imports.txt` | **Vista+ import YOK** (yalnız XP'de var olan DLL'ler) | https://learn.microsoft.com/en-us/cpp/build/reference/imports-dumpbin?view=msvc-170 [K14] · genel: https://learn.microsoft.com/en-us/cpp/build/reference/dumpbin-reference?view=msvc-170 [K15] |
| Subsystem sürümü | `dumpbin /headers … \| findstr SUBSYSTEM` | **5.01** (x86 XP) | https://learn.microsoft.com/en-us/cpp/build/reference/subsystem-specify-subsystem?view=msvc-170 [K16] |
| CRT statikliği | `dumpbin /dependents` | XP satırında CRT DLL'i **yok** (`/MT`) | https://learn.microsoft.com/en-us/cpp/build/reference/dependents?view=msvc-170 [K17] |
| MD5 + SHA-256 kaydı | `dist/manifest.json` append | 02 §7.3 şeması doldurulur; eski kayıt **üzerine yazılmaz** | 02 §7.3 (01 doc MD5 disiplini ile aynı yöntem) |
| Ad/kanıt eşleşmesi | Dosya adı `_x86_xp` → yukarıdaki 3 kapı **zorunlu** | 02 §3.3 kuralı | 02 §3.3 |

### §6.d Güvenlik kapıları

| # | Kapı | Test/kanıt | Durum |
|---|---|---|---|
| D1 | **PSK/şifre log'a ASLA yazılmaz** (redaction) | Bilinen test-only PSK değeri tüm log dosyalarında aranır → **0 isabet**; log alanı `keyMaterial/keyPassphrase` için daima `[REDACTED]` | Bağlayıcı; her koşuda |
| D2 | PSK konsola basılmaz | stderr/stdout yakalanır, test PSK'sı aranır → 0 isabet | Bağlayıcı |
| D3 | **Profil XML diske düz metin düşüyor mu?** | `NevaTest_XXXX` profili yazılır → XML nerede saklanıyor, PSK açık mı okunuyor? → **AÇIK MADDE (kullanıcı politikası)**: bu davranış ürün kararı olmadan "geçti" sayılmaz | `⚠️ VERIFICATION REQUIRED` + kullanıcı kararı (§9 #8) |
| D4 | Test verisi hijyeni | Test SSID/PSK'ları gerçek kullanıcı verisi **içermez**; PII/secret 0 | Bağlayıcı |
| D5 | Manifest'e secret yazılmaz | `manifest.json` yalnız hash/sürüm/commit içerir | Bağlayıcı |

---

## §7 CI (GitHub Actions)

> **⚠️ VERIFICATION REQUIRED — bu bölüm HİÇBİR canlı CI koşumu ile doğrulanmamıştır**
> (02 §3.4 aynı uyarıyı taşır). Aşağıdaki riskler kanıtlanmış, çözümü denenmemiştir.

### §7.1 Runner kısıtları (kanıtlı — 02 §9 atıfları)

| Risk | Kanıt (02 §9) | Etki bu planda |
|---|---|---|
| `windows-2022` görseli eski MSVC toolset'lerini artı kuramıyor | **[K28]** actions/runner-images #12764 — https://github.com/actions/runner-images/issues/12764 | `v141_xp` görselde hazır olmayabilir |
| Windows-2022 görselinden fazladan VC bileşenleri temizleniyor | **[K29]** #9701 — https://github.com/actions/runner-images/issues/9701 | XP bileşeni bulunmayabilir |
| **Windows Server görsellerinde `wlansvc` varsayılan ETKİN DEĞİL** | **[K30]** #9305 — https://github.com/actions/runner-images/issues/9305 | **GHA'da WLAN smoke ÇALIŞMAZ** → yalnız derleme + birim test + artefakt kapıları |
| `windows-2019` kaldırıldı; toolset talepleri | **[K31]** #12486 — https://github.com/actions/runner-images/issues/12486 | Matris/sürüm kırılması |
| Görsel içeriği (hangi VS/SDK kurulu) | Windows2022-Readme — https://github.com/actions/runner-images/blob/main/images/windows/Windows2022-Readme.md [K19] · repo: https://github.com/actions/runner-images [K20] | Kurulum adımı öncesi teyit gerekir |
| GitHub-hosted runner politikası | https://docs.github.com/actions/using-github-hosted-runners/about-github-hosted-runners [K21] | Self-hosted önerisi (02 §3.4 CI kararı) ile aynı yönde |

### §7.2 Beklenen akış: PR → build×5 → unit → artefakt → yerel smoke

```yaml
# .github/workflows/neva-connect-windows.yml — TASLAK (⚠️ VERIFICATION REQUIRED, hiç koşulmadı)
on: [pull_request]
jobs:
  build:
    runs-on: windows-2022
    strategy:
      fail-fast: false
      matrix:
        include:                      # 5 satır (x64_xp DIŞARIDA — 02 §7.1 ortam yok)
          - { target: x86_xp,   toolset: v141_xp, sdk: "7.1A",         winnt: 0x0501, crt: MT }
          - { target: x64_win7, toolset: v142,    sdk: "8.1",          winnt: 0x0601, crt: MD }
          - { target: x64_win81,toolset: v143,    sdk: "8.1",          winnt: 0x0603, crt: MD }
          - { target: x64_win10,toolset: v143,    sdk: "10.0.26100.x", winnt: 0x0A00, crt: MD }
          - { target: x64_win11,toolset: v143,    sdk: "10.0.22000.194+", winnt: 0x0A00, crt: MD }
    steps:
      - uses: actions/checkout@v4
      - uses: microsoft/setup-msbuild@v2        # https://github.com/microsoft/setup-msbuild [K18]
      - name: v141_xp var mı?                   # yoksa [K28]/[K29] gereği KURULAMAZ → job fail
        run: <vswhere ile bileşen sorgusu — DOĞRULANMADI>
      - name: configure + build (vcpkg toolchain)
        run: cmake --build ...                  # https://learn.microsoft.com/en-us/vcpkg/users/buildsystems/cmake-integration [K4]
      - name: unit test (ctest)
        run: ctest --output-on-failure -j 8     # https://cmake.org/cmake/help/latest/manual/ctest.1.html [K1]
      - name: artefakt kapisi (§6.c: dumpbin /imports, subsystem, MD5)
        run: <gate betiği — XP satırında ZORUNLU>
      - name: WLAN smoke
        if: false                               # wlansvc YOK [K30] → ATLANIR
      - uses: actions/upload-artifact@v4        # neva-wlan_<target>.exe + manifest
```

| Aşama | Ne yapar | Kapı |
|---|---|---|
| 1. PR tetikleme | `pull_request` | — |
| 2. **build × 5** | 02 §2.3 matrisinin 5 satırı (farklı toolset/SDK/define/CRT) | Derleme hatası → BLOCK |
| 3. **unit test** | §2 45 senaryo (gerçek WLAN **yok** — CI'da uygundur) | FAIL → BLOCK; retry YASAK |
| 4. **artefakt** | §6.c dumpbin + subsystem + MD5 manifest + upload | XP satırında `imports_gate != PASS` → BLOCK |
| 5. **yerel smoke** | §4 checklist — **manuel, CI dışı** | §8 raporu + QA PASS/BLOCK |

**Workflow sözdizimi kaynağı:** https://docs.github.com/actions/using-workflows/workflow-syntax-for-github-actions [K22] · https://docs.github.com/actions/writing-workflows [K23].

**CI kararı (öneri, 02 §3.4 ile aynı):** XP toolset kurulumu bulut runner'da
kırılgandır → `v141_xp` satırı için **self-hosted Windows makine** ya da önceden
hazırlanmış runner görseli; **WLAN smoke asla GHA'da değil, yerel makinede koşar.**

### §7.3 XP NDISUIO felaket yolu testleri (09 doc §7'den — 12 senaryo)

> Kaynak: [[09-xp-ndisuio-felaket-yolu-tasarımı]] §7 (D4 aktarımı). Kabul kriteri: her
> satır ya ölçülür ya da ⚠️ işaretiyle "açık" kalır. Ortamlar: **U** = unit (sürücü yok,
> sahte tampon) · **VM** = gerçek XP SP1 sanal makine.

| ID | Ortam | Senaryo | Beklenen |
|---|---|---|---|
| **T-01** | U | `CTL_CODE` ile üretilen 5 IOCTL sabitinin nuiouser.h L33–L49 değerleriyle eşitliği (derleme zamanı) | 5/5 eşit; `FILE_ANY_ACCESS` kullanımı **yok** (09 §2.3) |
| **T-02** | U | Yapay `NDIS_802_11_BSSID_LIST` tamponları: normal (3 kayıt), `NumberOfItems=0xFFFFFFFF`, `Length=0`, kesilmiş son kayıt, `SsidLength=33` | B1–B6 ihlalleri L2/atlama; sınır-ötesi okuma **YOK** (bellek denetleyicisi temiz) |
| **T-03** | U | Sahte `NDISUIO_QUERY_BINDING` yanıtları: offset < sizeof, offset+len > toplam | `ERROR_INVALID_DATA` (deviceio.c L60–L92 ile birebir davranış) |
| **T-04** | VM | `net stop wzcsvc` → `sc query wzcsvc` = STOPPED → `wifi_scan` | Felaket yolu devreye girer; zarf 09 §5.2 ile **aynı şekil**; ağ listesi döner |
| **T-05** | VM | T-04 altında SET→QUERY aralığı 0/250/500/1000 ms × 10 tekrar | Boş-dönüş oranı ölçülür → varsayılan aralık kararı (**⚠️ açık madde** 09 §9 [D3]) |
| **T-06** | VM | `net stop ndisuio` → `wifi_scan` | 1× StartService denemesi → başarısızsa **L2**; **çökme/sınırsız bekleme YOK** |
| **T-07** | VM | Erişim-haklı IOCTL değeri (FILE_ANY_ACCESS türevi) bilinçli kullanıldığında | `ERROR_NOT_SUPPORTED (50)` gözlenir → 09 §2.3 regresyon kanıtı |
| **T-08** | VM | Yönetici olmayan kullanıcı ile `wifi_scan` (felaket yolu açıkken) | Gözlenen gerçek hata (5 vs başka) kaydedilir → 09 §2.6 ⚠️ kapanır |
| **T-09** | VM | `net start wzcsvc` → `wifi_scan` | Kapı kapalı → **normal yol**; fallback IOCTL'lerine hiç çağrılmaz (log kanıtı) |
| **T-10** | VM | 09 §1.3 karar tablosunun 6 satırı (WZC durumları × Ndisuio durumları) | Tablo davranışı birebir |
| **T-11** | U | `OID_802_11_BSSID_LIST` → sahte `NDIS_STATUS_INVALID_OID` | Degrade dalı (09 §3.4 R7): BSSID+SSID durum çıktısı, L2 DEĞİL |
| **T-12** | VM | Gerçek XP NIC ile `OID_802_11_BSSID_LIST` (WZC durdurulu halde) | Dönüş verisi 09 §4.2 walk ile ayrışır; `NDIS_WLAN_BSSID.Length` değerleri kayıt-kayıt loglanır |

**Not:** T-04/T-06/T-09 **duman testidir** ve yalnız XP VM'de koşulur (09 §7 notu);
XP VM tanımı bu dokümanın ortam bölümündedir. X4b adımı bu tabloya kapı niteliğindedir.

---

## §8 Rapor Şablonu & Onay Kapısı

### §8.1 Smoke raporu (her koşu için doldurulur)

| OS | Sürüm/SP | Artefakt MD5 | Commit | Sonuç (PASS/FAIL/BLOCK) | Bulgu (dosya+satır kanıtı) | Tarih | Koşan |
|---|---|---|---|---|---|---|---|
| — | — | — | — | **(örnek satır BOŞ — sonuç yok)** | — | — | — |

**Bulgu satırı zorunlu formatı:** `dosya:satır` veya `adım#` + gözlem metni; **kanıtsız PASS
geçersizdir** (bulgu kanıtı olmayan satır raporda tutulmaz).

### §8.2 Onay kapısı: QA PASS / BLOCK

| Kriter | PASS şartı | BLOCK şartı |
|---|---|---|
| §2 birim | 45/45 senaryo tanımlı; koşulanlar yeşil; flaky 0 | Herhangi bir FAIL, retry, veya tanımsız senaryo |
| §3 entegrasyon | I1-I4, I6-I10 yeşil; I7 **silme garantisi** kanıtlanmış; I5n `UNKNOWN` ise notlu | Profil temizliği kanıtsız / kirlenmiş ortam |
| §4 OS smoke | Hedef OS satırının tüm adımları işaretli + §4.3 tablosu doldurulmuş | Boş adım / handle artışı (§4.4) / çökme |
| §5 negatif | Çökme **0**; her desteklenmeyen özellik mesaj+fallback veriyor | Tek bir crash (`0xC0000139`/`0xC0000005`) |
| §6 kapılar | a: 0 uyarı · c: `imports_gate: PASS` · d: log'da PSK 0 isabet | Uyarı/analiz bulgusu, import sızıntısı, log sızıntısı |
| §7 CI | PR matrisi 5/5 yeşil + unit + artefakt kapıları | Matris satırı kırmızı |
| **Karar** | **QA PASS** | **QA BLOCK** (gerekçe + bulgu kanıtı ile) |

---

## §9 Açık Konular

| # | Açık konu | Etki | İşaret |
|---|---|---|---|
| 1 | **XP makinesi erişimi var mı?** (01 §6 #4, 03 §7.1 #7 ile aynı soru) | §4.2 X-satırı ve 03 §6.1 P1/P2/N1 **koşulamaz** | `UNKNOWN` — kullanıcı |
| 2 | **Test AP erişimi var mı?** (bilinen WPA2 AP; XP'de WPA2 uyumu dahil) | I5/I9, S9-S10; AP yoksa connect/event adımları `UNKNOWN` yazılır | `UNKNOWN` — kullanıcı; XP WPA2 gereksinimi `⚠️ VERIFICATION REQUIRED` |
| 3 | **Coverage aracı (Windows/MSVC)** — lcov mu, OpenCppCoverage mı, WSL mi? | §6.b kapısı tanımsız | `⚠️ VERIFICATION REQUIRED` |
| 4 | WS 9742 köprüsü Windows dalı **henüz kodlanmadı** (01 §4.3 F4) | I3/I4 planı bekliyor | Kod onayı bekliyor |
| 5 | XP'de WZC servisinin **kısa adı** (`sc query` teyidi) | X1 adımı | **KAYNAK KANITI VAR (2026-10-15):** `WZCSVC` — nt5src `eapol/service/eapolutil.c` `WZCSVC_SERVICE_NAME` + `netwzc.inx` (07 §2, 06 X1); XP üzerinde `sc query wzcsvc` runtime teyidi kalır |
| 6 | `WlanSetProfile` çakışan profil adında davranış semantiği (overwrite/ret) | U35 | `⚠️ VERIFICATION REQUIRED` |
| 7 | Yöneticisiz ortamda profil yazımının döndürdüğü `ERROR_*` | S14/I9 beklenen kodu | `⚠️ VERIFICATION REQUIRED` |
| 8 | **Profil XML diske düz metin düşer mi** (PSK koruması) — kullanıcı politikası | §6.d D3 | AÇIK MADDE — kullanıcı kararı |
| 9 | GHA'da `v141_xp` kurulumunun canlı başarısı | §7.2 adım 3 | `⚠️ VERIFICATION REQUIRED` (02 §8 #7 ile aynı) |
| 10 | `gtest`/vcpkg paketlerinin **v141_xp ile** derlenmesi | §2 setinin XP satırında derlenmesi | `⚠️ VERIFICATION REQUIRED` (02 §2.2 T5) |
| 11 | `ctest --coverage` seçeneğinin gerçekliği | §6.b | `⚠️ VERIFICATION REQUIRED` |
| 12 | Win11 **x86** export dump'ı (03 §7.1 #1'den devralındı) | `neva-wlan` Win11-x86 derlemesi yok; etkisi yok | `⚠️ VERIFICATION REQUIRED` |
| 13 | Bu dosyanın `authority` satırı "01-05" der; **yazım anında diskte 01-04 vardı** (05 varsa §10'da linklenir) | Kardeş doküman link bütünlüğü | Gözlem — 05 doc doğrulanacak |

---

## §10 Kaynaklar

> Güvenilirlik: **Yüksek** = birincil otorite (Microsoft Learn / resmi doküman) ·
> **Orta** = resmi repo/vendor · **Düşük** = topluluk (kanıt olarak kullanılır, otorite değil).

| # | Kaynak | URL | Güvenilirlik | Kullanıldığı bölüm |
|---|---|---|---|---|
| K1 | CMake — ctest(1) manual | https://cmake.org/cmake/help/latest/manual/ctest.1.html | Yüksek | §1.3, §6.b, §7 |
| K2 | GoogleTest (resmi repo) | https://github.com/google/googletest | Yüksek | §1.3 |
| K3 | GoogleTest — Quickstart: Building with CMake | https://google.github.io/googletest/quickstart-cmake.html | Yüksek | §1.3 |
| K4 | Microsoft Learn — vcpkg in CMake projects (toolchain) | https://learn.microsoft.com/en-us/vcpkg/users/buildsystems/cmake-integration | Yüksek | §1.3, §7 |
| K5 | Microsoft Learn — Compiler warning levels (/W0../Wall, /WX) | https://learn.microsoft.com/en-us/cpp/build/reference/compiler-option-warning-level?view=msvc-170 | Yüksek | §6.a |
| K6 | Microsoft Learn — /analyze (Code analysis) | https://learn.microsoft.com/en-us/cpp/build/reference/analyze-code-analysis?view=msvc-170 | Yüksek | §6.a |
| K7 | Microsoft Learn — Code analysis for C/C++ overview | https://learn.microsoft.com/en-us/cpp/code-quality/code-analysis-for-c-cpp-overview?view=msvc-170 | Yüksek | §6.a |
| K8 | Cppcheck ana sayfa | https://cppcheck.sourceforge.io/ | Orta | §6.a |
| K9 | Cppcheck manual (PDF) | https://cppcheck.sourceforge.io/manual.pdf | Orta | §6.a |
| K10 | Clang-Tidy (LLVM) | https://clang.llvm.org/extra/clang-tidy/ | Yüksek | §6.a |
| K11 | Clang-Tidy checks listesi | https://clang.llvm.org/extra/clang-tidy/checks/list.html | Yüksek | §6.a |
| K12 | Sysinternals — Process Explorer (handle leak takibi) | https://learn.microsoft.com/en-us/sysinternals/downloads/process-explorer | Yüksek | §4.4 |
| K13 | Sysinternals — Handle | https://learn.microsoft.com/en-us/sysinternals/downloads/handle | Yüksek | §4.4 |
| K14 | Sysinternals ana sayfa | https://learn.microsoft.com/en-us/sysinternals/ | Yüksek | §4.4 |
| K15 | Sysinternals — NotMyFault (yalnız referans, kullanılmıyor) | https://learn.microsoft.com/en-us/sysinternals/downloads/notmyfault | Yüksek | §5.2 |
| K16 | Microsoft Learn — IMPORTS (DUMPBIN) (02 K11'den) | https://learn.microsoft.com/en-us/cpp/build/reference/imports-dumpbin?view=msvc-170 | Yüksek | §6.c |
| K17 | Microsoft Learn — DUMPBIN Reference / DEPENDENTS / SUBSYSTEM (02 K12/K13/K10) | https://learn.microsoft.com/en-us/cpp/build/reference/dumpbin-reference?view=msvc-170 · https://learn.microsoft.com/en-us/cpp/build/reference/dependents?view=msvc-170 · https://learn.microsoft.com/en-us/cpp/build/reference/subsystem-specify-subsystem?view=msvc-170 | Yüksek | §6.c |
| K18 | microsoft/setup-msbuild (GitHub Action) | https://github.com/microsoft/setup-msbuild | Orta (resmi MS repo) | §7.2 |
| K19 | actions/runner-images — Windows2022-Readme | https://github.com/actions/runner-images/blob/main/images/windows/Windows2022-Readme.md | Orta (resmi repo) | §7.1 |
| K20 | actions/runner-images (repo) | https://github.com/actions/runner-images | Orta (resmi repo) | §7.1 |
| K21 | GitHub Docs — About GitHub-hosted runners | https://docs.github.com/actions/using-github-hosted-runners/about-github-hosted-runners | Yüksek | §7.1 |
| K22 | GitHub Docs — Workflow syntax for GitHub Actions | https://docs.github.com/actions/using-workflows/workflow-syntax-for-github-actions | Yüksek | §7.2 |
| K23 | GitHub Docs — Writing workflows | https://docs.github.com/actions/writing-workflows | Yüksek | §7.2 |
| K24 | OpenCppCoverage (Windows/MSVC coverage adayı) | https://github.com/OpenCppCoverage/OpenCppCoverage | Orta | §6.b |
| K25 | Microsoft Learn — WSL install | https://learn.microsoft.com/en-us/windows/wsl/install | Yüksek | §6.b |
| K26 | Microsoft Learn — WLAN_profile schema (profil XML) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-profileschema-schema | Yüksek | §2.5 |
| K27 | Microsoft Learn — Profile XML schema | https://learn.microsoft.com/en-us/windows/win32/nativewifi/profile-xml-schema | Yüksek | §2.5 |
| K28 | Microsoft Learn — WLAN_PROFILE_INFO ("the name of the profile is the SSID") | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/ns-wlanapi-wlan_profile_info | Yüksek | §2.5 U33 |
| K29 | Microsoft Learn — WlanReasonCodeToString | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanreasoncodetostring | Yüksek | §2.6 U40 |
| K30 | Microsoft Learn — WLAN_REASON_CODE | https://learn.microsoft.com/en-us/windows/win32/nativewifi/wlan-reason-code | Yüksek | §2.6 |
| K31 | Microsoft Learn — WlanRegisterNotification (03 K7) | https://learn.microsoft.com/en-us/windows/win32/api/wlanapi/nf-wlanapi-wlanregisternotification | Yüksek | §2.3, §3 I5n |
| K32 | Microsoft Learn — Wireless LAN API for Windows XP SP2 (03 K1) | https://learn.microsoft.com/en-us/windows/win32/nativewifi/about-wireless-lan-api-for-windows-xp-service-pack-2 | Yüksek | §2.1, §4.2 |
| K33 | 01 doc (disk) — export matrisi (XP 36, 19041 148) | [[01-wlanapi-sürüm-matrisi-ve-windows-plani]] | Yüksek (MD5+REA kanıtı) | §1.2, §2 |
| K34 | 02 doc (disk) — derleme matrisi + §7 test/yayın (dumpbin, subsystem, adlandırma, CI riskleri K28-K31) | [[02-coklu-exe-windows-sdk-derleme-mimarisi]] | Yüksek (disk kanıtı) | §1.2, §4, §6.c, §7 |
| K35 | 03 doc (disk) — feature gate + §6 test matrisi (P1-P7, N1-N9) | [[03-wlan-api-ozellik-kisitlama-tablosu]] | Yüksek (disk kanıtı) | §2, §5 |
| K36 | cpp-projects CLAUDE.md (disk) — vcpkg + MSVC2022 + ctest + gtest + /W4 | `E:\Belgeler\coremusic.net.old\cpp-projects\CLAUDE.md:117-129, 247-273, 341, 425-447` | Yüksek (disk kanıtı) | §1.3, §6.a, §6.b |
| K37 | GitHub runner-images Issues (02 §9 K28-K31 — wlansvc/toolset riskleri) | https://github.com/actions/runner-images/issues/12764 · https://github.com/actions/runner-images/issues/9701 · https://github.com/actions/runner-images/issues/9305 · https://github.com/actions/runner-images/issues/12486 | Orta | §7.1 |

**Araştırmada doğrulanamayanlar (uydurulmadı):**

- **Windows/MSVC coverage aracı** ve **`ctest --coverage` seçeneğinin gerçekliği** → §6.b `⚠️ VERIFICATION REQUIRED`.
- **AP'siz notification simülasyonu** için public API varlığı → §3 I5n `⚠️ VERIFICATION REQUIRED`.
- **XP WZC servis kısa adı**, **yöneticisiz profil hatası kodu**, **profil adı çakışma semantiği** → §9 #5/#6/#7.
- **gtest'in v141_xp ile derlenmesi** → §9 #10.

---

**Authority:** 01-05 NevaConnect dokümları + Microsoft Learn + cpp-projects test standardı
**Kod durumu:** bu dosya **plandır** — test/üretim kodu içermez; hiçbir sonuç iddiası taşımaz.
**Commit:** bu dokümanın commit'i orkestratöre aittir (subagent atmaz).
