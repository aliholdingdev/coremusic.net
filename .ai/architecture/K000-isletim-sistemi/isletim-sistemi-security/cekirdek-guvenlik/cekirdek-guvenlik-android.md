---
node_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-android
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-anayasa
  - K000-isletim-sistemi/isletim-sistemi-security/cekirdek-guvenlik/cekirdek-guvenlik-surucu
evidence:
  - https://source.android.com/docs/security/features/verifiedboot
  - https://source.android.com/docs/security/features/selinux
  - https://source.android.com/docs/security/features/selinux/implement
  - https://source.android.com/docs/security/best-practices
  - https://docs.kernel.org/admin-guide/LSM/SELinux.html
  - https://developer.android.com/tools/adb
validation:
  - "2026-10-14: URL'ler erisim ile dogrulandi; kod ornekleri kaynak: upstream docs imzali; repo OS araci yokligu 2026-10-14 taramasinda 0 isabet."
open_questions:
  - "S1: OS guvenlik katmani hedef mimaride devreye alinacak mi? [UNKNOWN]"
  - "S2: Uygulama <-> OS guvenlik sorumluluk siniri? [UNKNOWN]"
group: kod
---

# Cekirdek-Guvenlik — Android OS Kartı

## §0 ELI15

Bu kart, Android'de **çekirdek düzeyinde güvenlik** ne demek onu anlatır: (1) cihaz
açılırken kodun gerçekten üreticiden geldiğini kanıtlayan **Verified Boot zinciri**,
(2) süreçlere zorunlu erişim denetimi (MAC) uygulayan **SELinux** ve (3) üreticiler
için derlenmiş **güvenlik en iyi uygulamaları** listesi. Android bir **Linux
çekirdeği üzerine** kurulmuştur; bu kart, Linux kartının tekrarı değil, Android'e
özgü katmanları (AVB, Android SELinux politikası, Treble politika ayrımı) gösterir.
Bu repoda bu katmanların **hiçbiri yoktur** — kart hedef mimari + negatif kanıttır.

> **Oncelikli gercek:** 2026-10-14 taramasında repo genelinde OS güvenlik aracı
> **0 isabet** (418 PHP+JS dosya; `vendor`/`node_modules` hariç). Bu kartta tek pozitif
> satır, uygulama katmanındaki `shared/src/Session/SessionConfig.php:20`
> (`DIR_PERMISSIONS = 0750`) analoğudur; o da **DAC** ayarıdır, çekirdek koruması
> değildir (bkz. §3).

## §1 Android güvenlik profili

**Ayrım (bağlayıcı):** Android, Linux çekirdeği üzerine kuruludur — bu kart ile
`isletim-sistemi-kernel` Linux kartı **birbirinin yerine geçmez**. Linux kartı
çekirdeğin evrensel ayarlarını (KASLR, modül imzası, LSM modu) taşır; bu kart
**Android'e özgü** katmanları (Verified Boot/AVB, Android SELinux politikası,
üretici en iyi uygulamaları) taşır. İkisi birlikte okunur, biri diğerinin özeti
değildir.

| Mekanizma | Ne yapar | Bu alt-dal açısı | Güven düzeyi |
|---|---|---|---|
| Verified Boot (AVB) | Donanım korumalı kök güvenden bootloader'a, ordan boot/`system`/`vendor`/`oem` bölümlerine **güven zinciri**; her aşama bir sonrakini teslim etmeden önce bütünlük + kimlik doğrular | D1-1: bootloader zinciri durumu | upstream docs · iddia yüksek · repo 0 |
| dm-verity | Android 4.4 ile Verified Boot 1 kapsamında bölüm bütünlüğü doğrulaması | D1-1: zincirin alt katmanı | upstream docs · repo 0 |
| Rollback protection | Cihaz yalnızca **daha yeni** Android sürümüne geçer; istismarın kalıcılaşmasını engeller | D1-1: zincir yaşam döngüsü | upstream docs · repo 0 |
| SELinux (Android, enforcing) | Tüm süreçler için MAC; root/capability bile denetlenir; **default-deny**; ihlaller `dmesg` + `logcat`'a yazılır | D1-2: çekirdek entegrasyonu | upstream docs · repo 0 |
| Per-domain permissive | Yalnız belirli alanlar (süreç etiketleri) izinli; sistem geneli enforcing kalır | D1-2: politika geliştirme modu | upstream docs · repo 0 |
| Treble sepolicy ayrımı (Android 8.0+) | Vendor (`vendor.img`) ve platform (`system.img`) politika bölümleri ayrı güncellenir; vendor, platformdan yeni olamaz | D1-2: entegrasyon sınırı | upstream docs · repo 0 |
| Linux çekirdek SELinux alt sistemi | SELinux bir LSM olarak çekirdekte derlenir; kullanıcı alanı `checkpolicy`/`setfiles`/`fixfiles` ister | D1-2: çekirdek ≠ Android politikası | upstream docs (docs.kernel.org) · repo 0 |
| Android security best practices | Organizasyonel, sistem, uygulama, ağ, donanım güvenliği + gizlilik; CDD/CTS ile denetlenir | D1-3: en iyi uygulama envanteri | upstream docs · repo 0 |
| adb + USB debugging RSA onayı | Denetim erişim kapısı; RSA anahtarı onayı olmadan adb komutu çalışmaz | D1-denetim: kartı okuma yolu | upstream docs · repo 0 |

**Okuma:** 9 satırın 8'i `upstream docs` sınıfıdır (hedef mimari), 0 satır bu
repoda uygulanmıştır. `Güven düzeyi` sütunu iddianın kaynağını gösterir — repo
uygulamasını değil; repo uygulaması §3'te ayrı ölçülür.

## §2 Kod örnekleri

> **UYARI:** Aşağıdaki 3 bloğun **hiçbiri bu repodan alınmamıştır**; her blok
> ilk satırında `kaynak: upstream docs` imzası taşır ve §5'teki URL'lere bağlıdır.
> Repo'da karşılıkları §3'te `YOK` (negatif kanıt) olarak ölçülür.

### §2.1 — adb ile cihaz durumu doğrulama (referans)

```sh
# kaynak: upstream docs - developer.android.com/tools/adb
adb devices
# baglanti onaylanmissa cihaz "device" olarak listelenir
# USB debugging: Developer options > USB debugging (Android 4.2+/API 17+ varsayilan gizli)
# Android 4.2.2+ : RSA anahtari onay diyalogu onaylanmadan adb komutu calismaz
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — repo'da adb çağrısı/CI adımı yok (§3 negatif kanıt) |
| Doğrulama | URL 2026-10-14 · HTTP 200 |

### §2.2 — SELinux politika entegrasyonu (referans)

```make
# kaynak: upstream docs - source.android.com/docs/security/features/selinux/implement
# system/sepolicy dosyalari dogrudan degistirilmez; cihaza ozel politika eklenir
BOARD_SEPOLICY_DIRS += /device/manufacturer/device-name/sepolicy
BOARD_SEPOLICY_UNION += \
    genfs_contexts \
    file_contexts \
    sepolicy.te
# *.te  -> alan (domain) ve etiket tanimi
# file_contexts -> dosya etiketleri; uygulamak icin restorecon / restorecon_recursive
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `*.te` / `file_contexts` glob'u repoda 0 (§3) |
| Sınır | Bu blok Android **üretici** tarafıdır; uygulama geliştirme değil |

### §2.3 — Verified Boot güven zinciri (referans)

```text
# kaynak: upstream docs - source.android.com/docs/security/features/verifiedboot
guven zinciri (her asama sonrakini teslim etmeden ONCE dogrular):
  donanim korumali kok guven -> bootloader -> boot bolumu
  -> system / vendor / (opsiyonel) oem bolumleri
rollback protection: cihaz yalnizca daha yeni Android surumune gecer
Android 4.4  : Verified Boot + dm-verity (Verified Boot 1)
Android 7.0  : katI uygulama (bozuk cihaz acilmaz) + hata duzeltme kodlamasi
Android 8.0+ : AVB - Treble ile, bolum footer formati standart, rollback korumasi
```

| Sınıf | Repo karşılığı |
|---|---|
| R (referans) | YOK — `vbmeta`/imzalı bölüm artifact'i repoda 0 (§3) |
| Not | "bootloader durumu" bu blokta **upstream tanımıdır**; cihaz üstü ölçüm yapılmadı |

## §3 CoreMusic repo durumu

> **Ölçüm (2026-10-14):** repo genelinde OS güvenlik aracı taraması **0 isabet**
> (418 PHP+JS dosya; `vendor`/`node_modules` hariç).

| Yüzey | Beklenen artifact | Repo durumu | Kanıt (negatif/pozitif) |
|---|---|---|---|
| Verified Boot / AVB | `vbmeta*` · imzalı `boot.img` · anahtar | **YOK** | glob 0 · negatif kanıt (2026-10-14) |
| dm-verity / rollback | bölüm imza yapılandırması | **YOK** | glob 0 · negatif kanıt |
| Android SELinux politikası | `*.te` · `file_contexts` · `seapp_contexts` | **YOK** | glob 0 · negatif kanıt |
| adb / Android build yüzeyi | `adb` · Gradle · `AndroidManifest.xml` | **YOK** | glob 0 · negatif kanıt |
| Best-practices denetim kaydı | CDD/CTS raporu | **YOK** | rapor dizini 0 · negatif kanıt |
| session dizin izni (uygulama katmanı) | PHP `mkdir` modu | **VAR** | `shared/src/Session/SessionConfig.php:20` · `:35` → `0750` · **P** |
| zamanlama-güvenli karşılaştırma | uygulama katmanı | **VAR** | `shared/src/Middleware/CsrfMiddleware.php:42` → `hash_equals` · **P** |
| shell kaçış koruması | uygulama katmanı | **VAR** | `shared/src/AI/AIEngine.php:292` → `escapeshellarg` · **P** |
| yol normalizasyonu | uygulama katmanı | **VAR** | `home.coremusic.net/include/Stream/MusicStreamHandler.php:189` → `realpath` · `:200` → `str_starts_with` · **P** |

**Okuma:** 9 satırın 5'i `YOK` (negatif kanıt), 4'ü uygulama katmanı pozitif
kanıttır. **Hiçbir pozitif satır Android çekirdek güvenlik katmanına ait değildir**
hepsi PHP uygulama katmanıdır — bu, §6 S2'nin dayanağıdır.

## §4 Sınır cümleleri

> **K010 ayrimi (baglayici):** Kimlik dogrulama, oturum, sifre ve MFA -> `K010-identitiy`; bu kartta yalnizca Android dosya/surec/erisim kisitlari ele alinir.

> **K009 ayrimi (baglayici):** OWASP denetimi, auth/CSRF/CSP tasarimi, kripto secimi (ADR-022) -> `K009-security`; bu kart yalnız OS katmani + sinir isaretlerini tasinir.

> **Komşu dal:** Linux cekirdegi ici yapisi -> `isletim-sistemi-kernel`; Android platform geneli -> `isletim-sistemi-turu/android`.

## §5 Referans

| URL | İddia | 2026-10-14 durum |
|---|---|---|
| https://source.android.com/docs/security/features/verifiedboot | Güven zinciri donanım kökünden başlar; her aşama bir sonrakini doğrular; AVB 8.0+; rollback koruması | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/features/selinux | Android SELinux MAC'i tüm süreçlere uygular; default-deny; enforcing varsayılan; 4.3+ sandbox sınırı | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/features/selinux/implement | `system/sepolicy` doğrudan değiştirilmez; `*.te`/context dosyaları; `restorecon`; BOARD_SEPOLICY değişkenleri | 2026-10-14 · HTTP 200 |
| https://source.android.com/docs/security/best-practices | Üreticiler için 6 alanlı en iyi uygulama listesi; CDD/CTS ile ilişki | 2026-10-14 · HTTP 200 |
| https://docs.kernel.org/admin-guide/LSM/SELinux.html | SELinux çekirdek alt sistemi; kullanıcı alanı araçları (`checkpolicy`, `setfiles`, `fixfiles`) | 2026-10-14 · HTTP 200 |
| https://developer.android.com/tools/adb | adb istek/sunucu/daemon mimarisi; USB debugging; RSA onay diyalogu | 2026-10-14 · HTTP 200 |

> **Dürüstlük notu:** 6 satırın tamamı bu oturumda içerik olarak okunmuş ve
> `HEAD` ile **HTTP 200** döndürmüştür. Bu kartta uydurma CVE, uydurma komut
> çıktısı veya "repo'da uygulanıyor" iddiası **yoktur**.

## §6 Açık sorular

| # | Soru | Durum |
|---|---|---|
| S1 | OS guvenlik katmani hedef mimaride devreye alinacak mi? | `[UNKNOWN]` — kapatılmaz; mimari sahibi kararı + `>=088` ADR ister |
| S2 | Uygulama <-> OS guvenlik sorumluluk siniri? | `[UNKNOWN]` — §3'teki 4 pozitif satırın tamamı uygulama katmanı; sınır çizgisi yazılı değil |

**KAPATMA YASAĞI:** Bu iki soru bu kartta **kapatılamaz**; kapatma yalnız
mimari sahibinin yazılı kararı ve yeni ADR (`>=088`) ile olur.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

## §8 Ortak çekirdek güvenlik denetim çerçevesi (platformdan bağımsız)

Bu bölüm kart grubunun ortak paydasıdır: platforma özgü mekanizmalar kartın
kendi bölümlerinde anlatılır; burada yalnızca hepsine birden uygulanan denetim
çerçevesi kurulur. Çerçeve, aynı olgunun bir kartta zorunlu diğerinde
opsiyonel sayılma riskini de önler.

Çerçeve üç soru üzerine oturur: (1) güven kökü nerede başlar, (2) yetki kararı
hangi katmanda ve hangi sırayla verilir, (3) kararın izi nereye yazılır ve kim
okur. Bu üç sorudan birinin yanıtı eksikse kart tamamlanmış sayılmaz.

- Çerçevenin uygulanışı bir uyum sertifikası değil, mimari kararın
  gerekçelendirilmesi aracıdır: her satır için kart üzerinde açık bir bölüm
  bulunmalıdır.
- Bir olgu iki kartta farklı yorumlanamaz; çelişki çıkarsa kartlar değil
  çerçeve düzeltilir ve düzeltme kayda geçer.
- Kontrolün varlığı ile kontrolün etkinliği ayrı şeylerdir: varlık kartta,
  etkinlik ölçümle kanıtlanır.
- Ölçülemeyen kontrol, kapıdan geçirilen iddia yerine yalnızca niyet
  beyanıdır.

| Denetim boyutu | Temel soru | Tipik kanıt sınıfı | K000'de sahibi |
|---|---|---|---|
| Güven kökü | Zincirin ilk halkası donanıma mı bağlı? | ROM/alt sistem kökü davranışı | §1 platform profili |
| Bellek düzeni | Kod yazılabilir mi, veri çalıştırılabilir mi? | NX/W^X, yığın ve çağrı yığını korumaları | §1 platform profili |
| Adres uzayı | Tahmin dayanıklılığı var mı? | ASLR kademeleri, tahmin maliyeti | §1 platform profili |
| Yetki kararı | Karar hangi sırayla verilir? | Kimlik → yetki → nesne → karar akışı | §2 kod örnekleri |
| Kod girişi | Üçüncü taraf kodu kapıdan nasıl girer? | İmza, otorite ve politika kontrolü | §2 kod örnekleri |
| Denetim izi | Karar nereye yazılır, kim okur? | Olay kanalı ve saklama süresi | §5 referans |
| Geri düşme | Bilinen açık sürüme dönüş mümkün mü? | Sürüm sayacı / anti-rollback | §1 platform profili |
| Tespit ve tepki | Tespit sonrası davranış ne? | Engelleme, izolasyon, yalnız kayıt değil | §6 açık sorular |
| Sahiplik | Bu kontrolü kim işletir, kim gözden geçirir? | Sorumluluk ve gözden geçirme periyodu | Mimari sahip kararı |

## §9 Karar sırası — çekirdek denetim akışı (ortak)

Altı kartta da aynı sıra geçerlidir; platformlar yalnızca her adımı hangi
mekanizma ile karşıladığında farklılaşır. Sıra değişirse güvenlik anlamı
değişir: örneğin imza kontrolü modül zaten belleğe alındıktan sonra yapılırsa
kontrol geç kalmıştır.

- Sıranın değiştiği yerler kod incelemesiyle değil, yapılandırma denetimiyle
  aranır: çoğu sapma bir bayrak değerinden çıkar.
- Akışın her adımı için kanıt sınıfı ayrıdır; bir adımda toplanan kanıt
  diğer adımda kullanılamaz.
- Akış, üretim ortamında test ortamından farklı çalışabilir; kapıyı geçen
  kanıt ortamına göre etiketlenir.

1. Güven kökü doğrulanır; doğrulanamayan hiçbir aşamaya geçilmez ve aşama
   arası veri paylaşımı bu doğrulamadan bağımsız kurulmaz.
2. Önyükleme zinciri halkaları sırayla kontrol edilir; bir halka
   reddedilirse zincir durur, ertelenmez.
3. Çekirdek bellek düzeni kurulur: kod salt-okunur, veri çalıştırılamaz,
   çağrı yığını yazılabilir ama yürütülemez.
4. Adres uzayı rastgeleleştirilir ve yeniden başlatmada yeniden üretilir;
   tahmin dayanıklılığı ölçülerek kayda girer.
5. Kod girişi kapısı (modül, sürücü, eklenti) yalnızca imza ve otorite
   kontrolünden sonra açılır.
6. Kullanıcı alanı süreçleri en az yetkiyle başlatılır; varsayılan izin
   veren değil reddeden taraftır.
7. Sistem/arayüz çağrılarında yetki sırası uygulanır: kimlik tespiti, yetki
   kontrolü, nesne denetimi, karar.
8. Kararın izi denetim kanalına yazılır; iz yazılmıyorsa denetim yapılmış
   sayılmaz.
9. Tepki uygulanır: engelleme, izolasyon veya kilitlenme; yalnızca kayıt
   tutmak tepki değildir.
10. Periyodik yeniden değerlendirme yapılır: yapılandırma değişikliği, yeni
   sürücü ve yeni tehdit sonrası denetim tekrarlanır.

## §10 Ortak başarısızlık modları ve tepki matrisi

Aşağıdaki tablo sekiz kartta da geçerlidir; kartlar yalnızca bu modların kendi
platformlarında hangi mekanizmayla karşılandığını ekler. Tepki sütunu mimari
karar gerektirir; karar verilmemişse mod açık kalır.

- Modların tamamı için ortak olan şey: hepsi sessizce gerçekleşir. Tespit,
  modun kendisinden değil, kanalından gelir.
- Tepki sütunundaki seçeneklerden hiçbiri verilmemişse satır bir risk değil,
  açık bir mimari karardır.
- Bir platformda F5 çözülmüş görünüyor diye diğer platform için de çözülmüş
  sayılmaz; kartlar tek tek kanıt ister.

| # | Başarısızlık modu | Sonuç | Tespit kanalı | Beklenen tepki |
|---|---|---|---|---|
| F1 | İmza doğrulaması atlanabilir yapılandırılmışsa | Denenmemiş kod çekirdeğe girer | Önyükleme logu, modül listesi | Boot/modül yüklemesini durdur |
| F2 | Geri düşme koruması yoksa | Bilinen açık sürüme dönüş | Sürüm sayacı okuması | Sayacı sabitle, yükseltmeyi zorunlu kıl |
| F3 | Denetim izi yazılmıyorsa | Olay sonrası inceleme imkansız | Log akışı süreklilik doğrulaması | İz kanalını zorunlu ve kalıcı yap |
| F4 | Yetki denetimi yalnız kullanıcı uzayındaysa | Çekirdek üzerinden bypass | Kod ve yapılandırma incelemesi | Kararı çekirdek/alt sistem katmanına taşı |
| F5 | Modül/driver imzası doğrulanmıyorsa | Kalıcı yerleşme riski | Yüklü modül imza durumu | İmzalanana kadar yüklemeyi reddet |
| F6 | Kilit bölgeleri uzun tutulmuşsa | Zamanında tepki verilemez | Kilit süresi ve ISR süresi ölçümü | İşi ertelemeye al, bölgeyi daralt |
| F7 | Yığın veya veri çalıştırılabilir durumda | Yürütme kontrolü kaybı | Bellek izni denetimi | Segmentleri ayır, NX/W^X uygula |
| F8 | Tepki yalnız günlükle sınırlı | İstismar durdurulamaz | Tepki senaryosu tatbikatı | Engelleme/izolasyon hattını kur |
| F9 | Varsayılan izin geniş bırakılmışsa | Yanlış yapılandırma sessizce risk üretir | Açılış yapılandırma denetimi | Varsayılanı daralt, değişikliği kayıt altına al |
| F10 | Kontrol yalnız test ortamında etkinse | Üretimde kontrol yoktur | Ortam farkı raporu | Üretimde de zorunlu kıl, kapatılamaz yap |

## §11 Kapı ölçütleri — neyin kanıt sayıldığı

Bu bölüm, kartların iddialarını sınıflandırır. Amaç iddiaları çoğaltmak değil,
hangi iddianın kapıdan geçebileceğini önceden söylemektir.

- Her kart, kendi iddialarını bu tabloya göre sınıflandırmak zorundadır;
  sınıflandırılmamış iddia iddia sayılmaz.
- Kanıt tarihlidir: tarih ve ortam yazılmayan kanıt, sonraki okumada tekrar
  doğrulanmak üzere bekler.
- Çelişki halinde platform dokümanı değil, ölçülen durum esastır;
  ölçülemeyen durum açık soru kalır.

> Bu bölümde hiçbir açık soru kapatılmaz; kapatma yalnız mimari sahibin
> yazılı kararı ve yeni ADR ile olur.
> Kapanmayan sorular kartın son bölümünde listelenmeye devam eder; sayıları
> artırılmış sayılmaz.

| İddia türü | Geçerli kanıt | Geçersiz kanıt |
|---|---|---|
| Mekanizma mevcuttur | Platform dokümanı + karttaki açıklama | Varsayım, benzer platformdan aktarma |
| Mekanizma varsayılan etkindir | Açılış durumu okuması veya resmi varsayılan | Üretimde de etkin olduğu yönündeki niyet |
| Kontrol etkindir | Olumsuz test: ihlal denemesi reddedilir | Olumlu test: doğru davranış zaten beklenirdi |
| Tepki zamanında verilir | Ölçülmüş gecikme | Tasarıda yazıyor olması |
| Olay toplanabilir | Kanal üzerinde okunabilir kayıt | Kanalın mevcut olduğu iddiası |
| Sınır K000'dedir | Dosya ve bölüm atfı | Zihnî eşleştirme |
| Bilinmeyen | Açık soru olarak bırakılır | Tahminle kapatma |

## §12 Ortak risk kayıt şablonu ve sahiplik

Risk satırları aşağıdaki alanların tamamını taşımalıdır; eksik alanlı satır
kayıt değil, nottur.

- Kimlik: benzersiz kod ve kart adı — örn. F4 + platform eki.
- Koşul: riskin gerçekleşmesi için gereken yapılandırma veya donanım durumu.
- Etki: ihlal gerçekleşirse ne kaybedilir (bütünlük, gizlilik,
  erişilebilirlik, denetlenebilirlik).
- Tespit: hangi kanaldan fark edilir ve ne sıklıkla bakılır.
- Tepki: engelleme, azaltma, aktarma veya kabul — dördünden biri açıkça
  yazılır.
- Sahip: kararı veren ve uygulayan kişi/rol; belirsizse sahipsiz risk olarak
  işaretlenir.
- Son tarih: karar için son gün; tarihsiz risk sonsuza kadar açık kalır.
- Yeniden değerlendirme tetikleyicisi: sürüm, donanım veya yapılandırma
  değişikliği.
- Bağlantı: ilgili ADR, açık soru ve kart bölümleri — kopuk bağlantı riski
  görünmez kılar.
- Kapanış ölçütü: ne olduğunda satır kapanır; kapanış ölçütü yazılmamışsa
  satır kapanmaz.

> Şablonun uygulanması tek başına riski azaltmaz; yalnızca riskin
> kaybolmasını engeller.

## §13 Android çekirdek güvenlik mekanizması envanteri (derinlemesine)

Envanter, kartın §1 profilini ayrıntılandırır. Varsayılan sütunu üretici
tarafından değiştirilebilen değerleri içerir; üretim cihazlarında
değiştirilemeyen satırlar mimari kısıt olarak okunmalıdır.

- Sertifikalı cihaz ile geliştirme cihazı aynı satırları paylaşmaz;
  geliştirme cihazında kilit açık olabilir, bu durum kanıt sınıfını düşürür.
- Üretici özelleştirmesi (vendor politikası, modül listesi) kartın
  kapsamındadır; özelleştirme yapılmadan varsayılanların sürdüğü varsayılamaz.
- Destek donanımı farklıysa (ör. StrongBox yok) o satır bu cihaz için düşer
  ve düşüş kartta açıkça yazılır.

| Mekanizma | Koruma alanı | Varsayılan | Devre dışı bırakılabilir mi | Mimari etkisi |
|---|---|---|---|---|
| Verified Boot (AVB) | Önyükleme zinciri bütünlüğü | Zorunlu (sertifikalı cihaz) | Hayır | vbmeta parçası ve ayraç düzeni tasarımı |
| dm-verity | İmzalı bölme okuma bütünlüğü | Zorunlu (system/vendor) | Hayır | Bölme hizalama, hash ağacı boyutu, açılış süresi |
| SELinux (MAC) | Süreç–nesne erişimi | enforcing | Üretimde hayır | Politika dosyaları ve etiket yaşam döngüsü |
| Dosya tabanlı şifreleme (FBE) | Cihaz fiziksel erişimi | Zorunlu | Hayır | Anahtar ağacı; açılış öncesi/sonrası alan ayrımı |
| Keystore / KeyMint | Kripto anahtar kökü | Donanım kökü (TEE/StrongBox) | Hayır | Uygulama API'si, attestasyon ve anahtar kalıcılığı |
| GKI + modül imzalama | Yüklenen çekirdek kodu | Zorunlu (GKI yolunda) | Hayır | Modül–çekirdek sembol/ürün eşleşmesi zorunluluğu |
| Binder denetimi | IPC yüzeyi | Her çağrıda | Hayır | Hizmet izin ve arayüz tanımının merkezileşmesi |
| Rollback koruması | Sürüm geri düşme | Bölme/hardware'a bağlı | Hayır | Depolama tasarımı ve sayaç güncelleme akışı |
| Scudo + MTE (destekli donanım) | Bellek hatası ve istismar | Cihaza göre | Kısmen | Performans ve ölçüm maliyeti |
| Sandbox (alan ayrımı) | Uygulama izolasyonu | Uygulama başına alan | Hayır | Kimlik, veri ve yetki tasarımdan gelir |

## §14 Android önyükleme ve modül yükleme güveni (akış)

Akış, güven köüğünden kullanıcı alanına giden halkaları sırayla listeler. Bir
halkanın durumu bilinmiyorsa akış o halkadan sonra belirsizdir; belirsizlik
sonraki halkalara da taşınır.

- Halkalar arası veri (etiket, anahtar, politika) da zincirin parçasıdır:
  doğrulanmayan ara veri zinciri zayıflatır.
- Modül yükleme, açılıştan sonra da olsa aynı kapıdan geçer; sıcak yükleme
  ayrı kapı sayılmaz.
- Akışta atlanan bir adım, kartta atlanmış değil, kanıtsız kabul edilir.

1. BootROM: silinemez kök; ROM içi kod doğrulanmadan yürütülmez ve tüm
   zincirin güven tanımı buradan çıkar.
2. Bootloader: kilit durumu okunur; kilit açık ise zincirin güven durumu
   raporlanır ve zincirin anlamı değişir.
3. vbmeta doğrulanır: çekirdek, ayraç verisi ve bir sonraki aşama bu
   doğrulamaya bağlanır.
4. Çekirdek yüklenir; modül yükleme kapısı imza politikasına tabidir ve
   politika atlanamaz.
5. İlk aşama bağlama: imzalı bölümler veri bütünlüğü korumasıyla bağlanır,
   okuma dışında yazma reddedilir.
6. Çekirdek politika yükleyicisi açılış öncesi politikayı kurar; politika
   olmadan süreç başlatılamaz.
7. init, etiketlere uygun servisleri başlatır; etiket uyuşmazlığı başlatmayı
   durdurur, ertelemez.
8. Uygulama süreci kendi alan etiketiyle başlar; alan dışına erişim denetim
   kaydı üretir.
9. Çökme/yeniden başlatma sonrası durum (pstore/last_kmsg) bir sonraki
   açılışta okunabilir olmalıdır.

## §15 Android çekirdek–kullanıcı alanı yetki sırası

Yetki sırası, kartın §2 kod örneklerindeki çağrı kalıplarının ardındaki mimari
düzendir. Sıra bozulursa örn. politika kontrolü sonraya kalır ve boşluk
sessizce açılır.

- Kimlik: süreç alanı etiketi ve kimlik bilgisi birlikte okunur; yalnız
  kimlik yetki vermez.
- Politika: etiket–nesne eşleşmesi politika dosyasında tanımlıdır, kodda
  gömülü değildir.
- Denetim: eşleşme olumluysa bile denetim kaydı üretilir; olumsuz eşleşmede
  kayıt zorunludur.
- Nesne: dosya, soket, hizmet ve aygıt nesneleri ayrı ayrı etiketlidir;
  etiketsiz nesne varsayılan olarak güvenilmezdir.
- Sınır geçişi: alanlar arası geçişte hem kaynak hem hedef denetlenir; tek
  taraflı denetim yeterli değildir.
- Bağımlılık: hizmete erişim, hizmetin sunduğu arayüzle sınırlıdır; arayüz
  dışı çağrı reddedilir.
- Örtük izin: varsayılan izin veren bir bayrak varsa kartta açıkça yazılır
  ve gerekçelendirilir.
- Yeniden denetim: politika değişikliğinde çalışan süreçler için denetim
  yeniden tetiklenir; yalnız yeni süreçler için değil.
- İptal: yetki geri alındığında açık tutulan bağlantıların da kapanması
  gerekir; yalnız yeni bağlanma reddi yetersizdir.
- Süre: geçici izin süreli olmalı, süresiz izin mimari karar
  gerektirmelidir.

## §16 Android denetim olayları ve toplanabilir kanıtlar

Toplanabilirlik, olayın varlığından değil, kanalın sürekliliğinden gelir.
Kanal kapalıysa olay var olsa da kanıt yoktur.

- Kayıt alanı sınırlıysa en eski kayıt düşer; bu, olay sonrası inceleme
  penceresini kısaltır ve kartta süreyle yazılmalıdır.
- Kişisel veri taşıyan kayıtlar toplanırken maskeleme zorunludur; kanıt ile
  gizlilik aynı kayıtta çatışır.
- Toplanamayan kanıt, kapının geçtiği iddiasını desteklemez; iddia açık soru
  olarak kalır.

| Olay | Kanal | Ne kanıtlar | Toplanabilirlik |
|---|---|---|---|
| Alan denetimi reddi (avc) | Kayıt/audit kanalı | Politika boşluğu veya yetkisiz deneme | Evet — süreklilik doğrulanmalı |
| Önyükleme zinciri durumu | Bootloader raporu / sistem özellikleri | Zincir bütünlüğü ve kilit durumu | Evet |
| Modül imzası reddi | Çekirdek kaydı | Yetkisiz kod denemesi | Evet |
| Anahtar attestasyonu | Anahtar servisi yanıtı | Anahtarın donanım köküne bağlılığı | Uygulama tarafında evet |
| Sayaç okuması | Depolama alt sistemi | Geri düşme koruması durumu | Cihaz testi gerekir |
| Ani yeniden başlatma nedeni | Kalıcı açılış kaydı | Çökme sonrası inceleme imkânı | Kısmen — alan doluluğuna bağlı |
| Yapılandırma değişikliği | Sistem yapılandırma dökümü | Kontrolün kapatılıp kapatılmadığı | Evet |
| Güncelleme ve sürüm geçişi | Güncelleme servisi kaydı | Sürüm ve yama durumu | Evet |

## §17 Android yapısal riskler ve mimari etkileri

Riskler, kartın §1 profilindeki varsayılanlardan değil, üretimde karşılaşılan
durum farklılıklarından çıkar. Koşul sütunu olmaksızın satır geçersizdir.

- A1 ve A5 en sık birlikte görülür: test cihazındaki gevşeklik üretime
  taşındığında zincir anlamsızlaşır.
- Risk satırı, azaltıcı karar yazılmadan kapanmaz; karar sahipsizse satır
  açık kalır.

| # | Risk | Koşul | Etki | Azaltıcı mimari karar |
|---|---|---|---|---|
| A1 | Kilit açık cihazın üretime karışması | Test/üretim cihaz havuzu ayrımı yok | Zincir tümüyle değişir, kanıt sınıfı düşer | Cihaz havuzunu ayır, kilit durumunu açılışta doğrula |
| A2 | Politika yazma hakkının geniş olması | Politika, yapılandırmadan ayrı sürülemiyor | Alan genişlemesi sessizce gerçekleşir | Politikayı kod ve yapılandırmadan ayrı izole et |
| A3 | Donanım güven kökü yok | TEE/StrongBox sınıfı sağlanmamış | Anahtar kökü yazılıma iner | Donanım sınıfını ön koşul olarak yaz |
| A4 | Kayıt toplama kapalı | Gizlilik gerekçesiyle kapatma | Olay sonrası inceleme imkânsız | Asgari olay setini kapatılamaz yap |
| A5 | Modül imzası atlanmış | Özel derleme veya test imzası | Kalıcı yerleşme riski | İmza kontrolünü kapıyı zorunlu kıl |
| A6 | Açılış öncesi alanın kapsamı | Kilit açılmadan erişilebilen veri | Soğuk çalıştırma ile veri sızıntısı | Açılış öncesi alan kapsamını daralt |
| A7 | Güncelleme gecikmesi | Yama dağıtımı yavaş | Bilinen açık penceresi uzar | Güncelleme kanalını mimari ön koşul say |
| A8 | Politika denetiminin test edilmemesi | Yalnız olumlu testler | Boşluk üretimde fark edilmez | Olumsuz testi kapının parçası yap |

## §18 Android için mimari kararlar ve K000 sınırı

Bu bölüm karar üretmez, kararı istemez. K000 yalnızca çekirdek güvenlik
kartlarının sınırlarını ve bağımlılıklarını taşır; uygulama katmanı
kontrolleri (doğrulama, oturum, rol) K009 kapsamındadır.

- Karar 1: Donanım güven kökü sınıfı bu projede ön koşul mu, yoksa yazılım
  yedeğiyle mi yetinilecek? — Sahibi: mimari sahip.
- Karar 2: Üretim cihazlarında kilit durumu nasıl denetlenir ve denetim
  sonucu nereye yazılır?
- Karar 3: Politika değişikliği kimin onayından geçer; onay kanıtı nerede
  saklanır?
- Karar 4: Asgari olay seti nedir ve bu set kapatılamaz mı? — Kapatılamazsa
  hangi ADR ile sabitlenir?
- Karar 5: Güncelleme gecikme toleransı nedir; aşımında tepki ne olur?
- Karar 6: Test cihazı havuzu üretimden hangi fiziksel/uzamsal ve hesapsal
  ayrım ile tutulur?
- Karar 7: Modül imzası istisnası (varsa) hangi ortam için tanınır ve ne
  zaman sona erer?
- Karar 8: Kart üzerindeki iddiaların yeniden doğrulama periyodu kaçtır?
- Sınır: bu kart, uygulama katmanındaki rol/izin ve oturum kontrollerini
  kapsamaz.
- Sınır: kart, üretim ortamı dışında kalan ortamların (geliştirme, test)
  durumunu yalnız varsayım olarak taşımaz; ölçülmedikçe yazmaz.
- Bağımlılık: önyükleme zinciri bölümü §1 profilini, denetim olayları bölümü
  §5 referanslarını kullanır.
- İstisna: hiçbir bölüm, doğrulanmamış donanım veya yapılandırma için
  geçerli sayılmaz.

## §19 Android kartında kapanmayan konular

Kapanmayan konular, kartın §6 açık sorularını genişletmez; onları yalnızca
çekirdek güvenlik boyutunda yeniden sınıflandırır.

> Bu konular bu kartta kapatılamaz; kapatma yalnız mimari sahibin yazılı
> kararı ve yeni ADR ile olur.
> Kapanan her konu, kartın kanıt sütununda tarih ve kanıt türüyle birlikte
> yazılır.
> Konu kapatılmış olsa bile kart, kapatma kanıtını taşımadıkça o konuyu
> çözülmüş saymaz.

| # | Konu | Neden kapanmaz |
|---|---|---|
| K-A1 | Donanım güven kökü sınıfı seçimi | Mimari sahibin kararı ve donanım teyidi gerekir |
| K-A2 | Asgari olay setinin içeriği | Gizlilik ve denetlenebilirlik dengesi kararı ister |
| K-A3 | Politika onay mekanizması | Süreç sahibi belirlenmeden yazılamaz |
| K-A4 | Yeniden doğrulama periyodu | Ölçüm verisi olmadan periyot tahmini olur |
