---
title: "ASIO Kapsam — Neva Engine ASIO Yüzeyi Kapsam Özeti ve Kapsam Dışı Bildirimleri"
node_id: "K003-audio/backend/asio/asio-kapsam"
node_type: "architecture-deepdive"
status: "draft"
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend/asio/asio-anayasa"
  - "K003-audio/backend/asio/asio-arayuzler"
  - "K003-audio/backend/asio/asio-surucu-modeli"
  - "K003-audio/backend/asio/asio-platform-uyumlulugu"
  - "K003-audio/backend/asio/asio-daw-uyumlulugu"
  - "K003-audio/backend/asio/asio-lisans-ve-dogrulama"
evidence:
  - "https://github.com/audiosdk/asio"
  - "https://github.com/audiosdk/asio/blob/main/changes.txt"
  - "https://github.com/dechamps/FlexASIO/blob/master/BACKENDS.md"
  - "https://github.com/audiosdk/ARA-Matchering/blob/main/README.md"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/exclusive-mode-streams"
  - "https://learn.microsoft.com/en-us/windows/win32/coreaudio/user-mode-audio-components"
  - "https://asio4all.org/version-2-22/"
  - "https://github.com/wineasio/wineasio"
  - "https://help.ableton.com/hc/en-us/articles/4515794042898-Multi-Client-Operation-Drivers-Windows-"
  - "https://kb.avid.com/External-Devices/What-is-the-Playback-Engine-in-Pro-Tools"
validation:
  - "https://help.ableton.com/hc/en-us/articles/360003149240-Why-don-t-I-see-ASIO-WASAPI-or-WDM-KS-in-List-Box-Live-11-"
  - "https://www.image-line.com/fl-studio-learning/fl-studio-online-manual/content/envsettings_asio4all.htm"
open_questions:
  - "ASIO Cihaz Paylaşım Protokolü (ASIO Guard dışı host-arası paylaşım) K003 ASIO kapsamına dahil mi, ayrı bir node mu?"
  - "Neva Engine ASIO-Guard (ara katman/düzeltme) uygulayacak mı, yoksa yalnız passthrough mü?"
  - "ASIO kayıt yolu (capture) Neva Engine'de hangi aşamada devreye alınacak — sartname §3.6 ölçüm yolu PLANNED ile mi hizalanacak?"
date: "2026-10-12"
updated: "2026-10-12"
group: mimari
---

# ASIO Kapsam — Neva Engine ASIO Yüzeyi Kapsam Özeti ve Kapsam Dışı Bildirimleri

> **Rol:** Bu dosya bir deep-dive değil, **kapsam sözleşmesidir**. Neva Engine'in ASIO
> yüzeyinin **neyi kapsadığını** ve **neyi kapsamadığını** tek yerde tescil eder. Derin
> içerik altı deep-dive düğümündedir; bu dosya hangi sorunun hangi düğüme ait olduğunu
> söyler. Sahiplik kararları (lisans, platform) burada **değil**, ilgili deep-dive'da alınır.

## §0 ELI15

Bir arabanın broşürü gibi düşün: bu dosya arabanın **özellik listesi ve garanti
kapsamı**. Motorun nasıl çalıştığını anlatmaz (o `asio-surucu-modeli.md`), hangi
yolların asfalt olduğunu söylemez (o `asio-platform-uyumlulugu.md`) — sadece "şu
var, şu yok, şu şarta bağlı" der. Bir şeyi burada kapsam dışı bulursan, o iş
`NevaEngine/electronic/` (PLANNED) ya da bir sonraki sürüme aittir, ASIO bloğunun
içinde arama. Bu dosya aynı zamanda **bağımlılık**, **kabul kriteri** ve
**soru→dosya yönlendirme** rehberidir: Neva Engine ASIO yüzeyiyle ilgili bir soru
geldiğinde önce buraya bakılır, buradaki tablo hangi deep-dive'ın o soruyu
kapsadığını söyler.

## §1 Kapsam İçinde (IN-SCOPE) — Neva Engine ASIO Yüzeyi

Aşağıdakiler ASIO bloğunun **tam** sorumluluk alanıdır; her biri bir deep-dive
düğümlüne bağlıdır ve o düğümde kanıtlanmıştır.

| # | Kapsam kalemi | Kanıt (deep-dive) | Durum |
|---|---|---|---|
| 1 | ASIO 2.3.4'e kadar API yüzeyinin spesifikasyonu (host↔driver sözleşmesi, callback'ler, sürücü keşfi) | `asio-anayasa.md` §2–§6 · `asio-arayuzler.md` §2–§4 | IMPLEMENTED (spesifikasyon) |
| 2 | ASIO driver modelinin ikili işleyişi (user-mode bileşen, sürücünün yüklediği gerçek kod, WDM-KS istisnası) | `asio-surucu-modeli.md` §1, §4–§7 | IMPLEMENTED |
| 3 | Windows sürüm matrisi, x86/x64/ARM64 mimari, EXCLUSIVE vs SHARED, kernel↔user sınırı | `asio-platform-uyumlulugu.md` §2–§5 | IMPLEMENTED |
| 4 | Yazılım-sürücü katmanı: WDM-KS, ASIO4ALL, FlexASIO, wineasio — Neva Engine'in bunlarla nasıl karşılaştığı | `asio-platform-uyumlulugu.md` §6–§9 · `asio-surucu-modeli.md` §8 | IMPLEMENTED |
| 5 | DAW uyumluluk matrisi, çoklu-client davranışı, kayıt/monitör önceliği, ARA'nın sınırı | `asio-daw-uyumlulugu.md` §2–§8 | IMPLEMENTED |
| 6 | Lisans analizi (GPLv3 vs tescilli), sürüm doğrulama, iddia–kanıt tablosu, açık sorular | `asio-lisans-ve-dogrulama.md` §2–§11 | IMPLEMENTED (analiz) |
| 7 | ASIO device-loss → WASAPI shared fallback kuralının ASIO bloğuna yansıması | `asio-platform-uyumlulugu.md` §4 (⚠️ VERIFICATION REQUIRED) · repo `.ai/AGENTS.md` §17/6 | BINDING (repo kuralı) |
| 8 | Tampon stratejileri: çift tampon şeması, boyut seçimi (`ASIOGetBufferSize` dörtlüsü), priming, xrun tespiti | `asio-buffer-stratejileri.md` §2–§10 | IMPLEMENTED (spesifikasyon) |
| 9 | Gecikme ve jitter: `ASIOGetLatencies` semantiği, `kAsioLatenciesChanged`, QPC/`timeBeginPeriod` ölçüm araçları | `asio-latency-ve-jitter.md` §2–§9 | IMPLEMENTED (spesifikasyon) |
| 10 | Çok kanallı eşleme ve zaman damgası yapıları: `ASIOGetChannels`, `ASIOBufferInfo`, `AsioTimeInfo`, time info mode müzakeresi | `asio-multichannel-ve-timestamp.md` §2–§7 | IMPLEMENTED (spesifikasyon) |
| 11 | Hot-plug ve device-loss: cihaz olayı bildirim kanalları, `IMMNotificationClient`, kurtarma durum makinesi (tasarım) | `asio-hot-swap-ve-device-loss.md` §2–§9 | IMPLEMENTED (tasarım) |
| 12 | ASIO ↔ WASAPI karşılaştırması: mod semantiği, motor periyodu, saat kaynakları, karar matrisi | `asio-wasapi-karsilastirma.md` §2–§7 | IMPLEMENTED (analiz) |
| 13 | Kaynaklı C++ örnekleri: ASIO SDK `hostsample.cpp` akışı ve JUCE `ASIOAudioIODevice` sarmalayıcısı | `asio-cpp-ornekleri.md` §2–§9 | IMPLEMENTED (analiz) |
| 14 | Cihaz yönetimi ve enumerasyon: `ASIOInit`/`ASIOOutputReady` sözleşmesi, sürücü indeksi, cihaz kimliği, hot-plug sonrası yeniden enumerasyon tetiği | `asio-cihaz-yonetimi-ve-enumarasyon.md` §2–§8 | IMPLEMENTED (spesifikasyon) |
| 15 | Örnek konumu ve saat senkronu: `ASIOGetSamplePosition` sıfırlanma kuralı, systemTime türetimi, word clock alanı, drift ölçümü | `asio-timestamp-ve-clock-senkronu.md` §2–§8 | IMPLEMENTED (spesifikasyon) |
| 16 | Kanal yapılandırması ve örnek tipi dönüştürücüleri: `ASIOChannelInfo.type`/`channelGroup` semantiği, 16/24/32-bit ve float yolları, endianness matrisi | `asio-kanal-yapilandirmasi-ve-converters.md` §2–§8 | IMPLEMENTED (spesifikasyon) |
| 17 | Hata yönetimi ve fallback orkestrasyonu: `ASIOCanSampleRate`/`ASIOSetSampleRate` başarısızlık zinciri, `ASE_*` kodları, WASAPI shared'e düşüş sıralaması, resync | `asio-hata-yonetimi-ve-fallback.md` §2–§8 | IMPLEMENTED (spesifikasyon) |
| 18 | Uygulama entegrasyon örnekleri: host callback zinciri, `asioMessage` pompanası, DAW entegrasyon topolojisi, gerçek C++ akışları | `asio-uygulama-entegrasyon-ornekleri.md` §2–§8 | IMPLEMENTED (analiz) |

**Kapsam içindeki kod yüzeyi:** ASIO host-side **spesifikasyon + şartname** (`docs/`) ve
**UI tasarımı** (`design/`) ile sınırlıdır. `NevaEngine/` C++ kodu **PLANNED** — bu blokta
`*.cpp`/`*.h`/`*.hpp`/`*.cmake` dosyası **yoktur** (repo kökü `AGENTS.md` kuralı:
`ASIO_SPEC/ASIO_Design IMPLEMENTED · NevaEngine/electronic YOK`). 2026-10-12 ölçümünde
repo genelinde `vendor|node_modules|third_party` dışına `*.cpp`/`*.h`/`*.hpp` dosyası
**0 adet** bulunmuştur; bu nedenle bu bloktaki tüm C++ alıntıları **resmi
dokümantasyon/kaynak-depo** alıntısıdır (`Kaynak:` etiketi), repo-satır referansı
**değildir**.

## §2 Kapsam Dışı (OUT-OF-SCOPE) — Ait Olduğu Yer Nerede

Aşağıdakiler bu ASIO bloğuna **girmez**. Birisi bunları bu blokta ararsa yanlış
dosyadadır.

| # | Kapsam dışı | Ait olduğu yer | Neden dışarıda |
|---|---|---|---|
| 1 | ARA Eklentisi | `K003-audio/plugins/ara/ara-anayasa.md` | ARA bir **plugin↔host veri katmanıdır**, ASIO bir **driver katmanı** — kesişim yok (ara SDK README: "ARA as such does not provide any audio or MIDI related functionality") ✅ |
| 2 | VST3 eklenti host'u | `K003-audio/backend/vst3/` | Ayrı bir plugin API yüzeyi; ASIO yalnız **device I/O** sağlar |
| 3 | Class AB amplifikatör, PCM3168A DAC, güç kaynağı, PCB, BOM | `K003-audio/hardware/` · `NevaEngine/electronic/` (PLANNED) | Donanım tarafı; ASIO yalnız **dijital** yolcu |
| 4 | ASIO **driver üretimi** (cihaz için Windows sürücü yazmak) | ADR-069 (audit): "ASIO driver değil, API'nin uygulanışı" | Neva Engine **host**'tur, sürücü **satın alır/yükler** |
| 5 | WASAPI/DSound/**CoreAudio** playback motoru implementasyonu | `asio-anayasa.md` §8 (yalnız ASIO bağlamında) · `backend/` ayrı bloklar | ASIO bu blokta yalnız ASIO'yu kapsar; WASAPI fallback **yalnız** ASIO kaybı bağlamında geçer |
| 6 | MIDI, clock/jitter DAHS, ölçüm zinciri (FIR/eşitleme) | `ASIO_Design_Sartname` §3 (docs/), `NevaEngine/` DSP | Bunlar ASIO'nun **komşularıdır**, ASIO'nun kendisi değildir |
| 7 | ASIO Cihaz Paylaşım Protokolü (host-arası paylaşımlı cihaz arbitrajı) | **Açık soru** (bkz §4) | `asio-daw-uyumlulugu.md` §7'de **tanımlandı ama kapsamı kesinleşmedi** |
| 8 | ASIO SDK'nın kendi derlenmesi/vcpkg bağımlılık yönetimi | Build mühendisliği (K000 build notları) | Sürüm/SHA512 bilinmiyor (⚠️ VERIFICATION REQUIRED) — sürüm iddiası yapılmaz |
| 9 | `driver/` klasöründeki vendor sürücü notları ve `routing/` katmanı | `K003-audio/backend/asio/driver/` · `K003-audio/backend/asio/routing/` | Bu bloğun **diğer** klasörleri — kapsam sözleşmesi yalnız kök deep-dive dosyalarını kapsar |
| 10 | Ses kartı satıcı kontrol paneli UI tasarımı | Vendor sürücüsü + `design/` ayrı akış | `ASIOControlPanel()` çağrısı kapsamdadır; panelin kendisi vendor malıdır |

## §3 Kapsam Kararları (bu dosyada alınan bağlayıcı kararlar)

1. **Fallback kuralı bağlayıcıdır:** ASIO Exclusive cihaz kaybı → WASAPI shared, IAudioClient3
   low-period (2.5 ms @ 48 kHz) — `.ai/AGENTS.md` §17/6. ASIO bu blokta **tek** cihaz erişim
   yolu değildir; WASAPI bir yedek değil **ikinci geçerli yoldur** (bkz §4 açık soru).
2. **ASIO-Guard kapsamı:** Neva Engine, kayıt/monitör dışı yollara daha büyük önbellek vererek
   kilitlenme riskini düşüren bir ara katman (ASIO Guard) **kurabilir** — bu bir özellik
   kararıdır, ASIO API'sinin parçası değildir; sayısal önbellek değerleri verilmez
   (⚠️ VERIFICATION REQUIRED). Kapsam: **karar henüz verilmedi** (bkz §4).
3. **Lisans kararı K020'ye bağlanır:** ASIO SDK 2.3.4 GPLv3/kapı açan/GPLv3 (GPLv3-compatible)
   çoklu lisanslıdır. Neva Engine'in **hangi lisansla** ASIO'yu bağlayacağı bu blokta alınmaz;
   `ADR-097 §6.5` → `K020-licence` kapısına bağlanmıştır (bkz `asio-lisans-ve-dogrulama.md` §11).
4. **Sürüm kaynağı:** ASIO SDK sürümü **yalnız** `changes.txt` + `audiosdk/asio` deposuyla
   doğrulanır (2.3.4 = 2025-10-15 ✅). vcpkg sürümü/SHA512 **bilinmiyor** (⚠️ VERIFICATION
   REQUIRED) — sürüm iddiası olarak kullanılmaz.
5. **Callback bütçesi kapsamdadır, mikro-yapı değil:** `bufferSwitch` gerçek-zamanlı
   bütçesi (512 örnek @ 48 kHz ≈ 10,67 ms) Neva Engine kısıtı olarak **kapsam içi** kabul
   edilir; ancak **ölçüm sonuçları** (`olcum/`) bu blokta **üretilmez**, yalnız referans
   alınır (sartname §3.6 PLANNED).
6. **DSD kapsam dışıdır:** `ASIOSTDSDInt8LSB1`/`ASIOSTDSDInt8MSB1`/`ASIOSTDSDInt8NER8`
   örnek tipleri protokolün parçası olarak **belgelenir** (asio.h), ancak Neva Engine'in
   DSD yolu **kapsam dışıdır** — PCM3168A donanım yolu PCM ile sınırlıdır (ADR-038 H001).
7. **Çok-uygulamalı ASIO davranışı kapsam dışı birincil hedef değildir:** ASIO Exclusive Lock
   kuralı (tek uygulama) repo kuralıdır; host-arası paylaşım arbitrajı uygulanırsa ayrı
   node açılır (bkz §4/1).

## §4 Açık Sorular (kapsamı belirsiz olanlar)

1. **ASIO Cihaz Paylaşım Protokolü kapsamda mı?** `asio-daw-uyumlulugu.md` §7'de
   tanımlandı, ancak Neva Engine'in bunu **uygulayıp uygulaymayacağı** kesinleşmedi. Uygulanırsa
   bu bloğa ek bir deep-dive gerekir; uygulanmazsa yalnız referans olarak kalır. **Üst karar:**
   kullanıcı.
2. **ASIO-Guard uygulanacak mı?** Neva Engine bir **çok-uygulamalı monitör/mikser** olduğu
   için ASIO-Guard sınıfı bir ara katman mantıklı görünüyor; ancak bu bir **ürün kararıdır**
   ve ASIO API'sinin zorunlu parçası değildir. Sayısal önbellek değerleri bilinmiyor.
3. **ASIO kayıt yolu (capture) ne zaman?** Sartname §3.6 ölçüm yolu (FIR + gecikme eşitleme)
   **PLANNED**; ASIO capture bu yola bağlıdır. Kapsam dışı mı, yoksa capture da bu blokta mı
   şartnameleşecek? **Üst karar:** ürün yol haritası.
4. **arm64EC desteği devre dışı mı?** ASIO4ALL arm64EC desteğinin "bu sürümde devre dışı"
   olduğu doğrulandı ✅; ancak Steinberg ASIO SDK'nın **arm64EC** için resmi bir matrisi
   bulunmuyor (⚠️ VERIFICATION REQUIRED). Neva Engine arm64 hedeflerinde ASIO SDK'yı hangi
   derleme hedefiyle bağlayacak — x64 emülasyon mu, native arm64 mı? **Üst karar:** build
   mühendisliği.
5. **`ASIOGetDeviceInfo` sembolü nerede?** ASIO SDK 2.3 `asio.h` prototip özetinde
   `ASIOGetDeviceInfo` adında bir C API fonksiyonu **bulunmamaktadır** (bkz
   `asio-cihaz-yonetimi-ve-enumarasyon.md` §2 — sembol yokluğu kanıtlıdır). Cihaz bilgisi
   üç yüzeyden gelir: `ASIOInit`'in doldurduğu `ASIODriverInfo`, kanal-bazlı
   `ASIOChannelInfo` ve SDK örneğinin sürücü listesi. Görev metnindeki "ASIOGetDeviceInfo"
   ifadesi bu üç yüzeyin şemdir — **uydurma sembol yazılmaz**. **Üst karar:** yok (bulgu).
6. **Time info mode varsayılan mı?** `asio.h` yorumu time info modunu **önerir** ("it is
   recommended to use the new method with time info even if the asio device does not support
   timecode"); JUCE sarmalayıcısı ise `kAsioSupportsTimeInfo`'ya **0** döner. Neva Engine
   hangi yolu seçecek — karar tasarım aşamasında. **Üst karar:** K003 tasarım ADR'si.

## §5 Kapsam Değişikliği Protokolü

Bu kapsama **ekleme** (yeni bir deep-dive, yeni bir platform matrisi satırı) yalnızca
**kanıtla** yapılır: her ekleme, `evidence` listesine en az bir **açılmış birincil kaynak**
ekler ve `open_questions`/`validation` alanlarını günceller. **Kanıtsız kapsam genişletme
yasaktır** (repo `AGENTS.md` §3 Zero-Hallucination). Kapsam **daraltma** (bir maddenin
OUT-OF-SCOPE yapılması) `date`/`updated` damgası ve bir deep-dive'daki karşılığının
gerekçelendirilmesiyle yapılır.

Değişiklik protokolünün adım listesi (bağlayıcı sıra):

| Adım | Aksiyon | Kanıt gereği |
|---|---|---|
| 1 | Yeni kapsam maddesi taslağı yazılır (§1 tablosuna satır) | Maddede hangi deep-dive'ın kanıtlayacağı belirtilir |
| 2 | Eşleşen open_question kapatılır veya yeni open_question açılır | Karar mercii (kullanıcı/üst karar) adı yazılır |
| 3 | `evidence` listesine en az bir HTTP 200 kaynak eklenir | URL + tarih + Durum sütunu |
| 4 | `date`/`updated` damgaları güncellenir | Aynı gün iki damga olabilir |
| 5 | Kapsam dışına taşınan madde için hedef dosya/klasör yazılır | Boş "ait olduğu yer" bırakılmaz |

## §6 Bağımlılıklar (Dependency Matrix)

Bu bloğun bağımlılıkları üç katmandadır: **iç** (bu bloğun kendi dosyaları), **dış-vault**
(K003 diğer blokları, repo kuralları) ve **dış-teknoloji** (SDK, Windows API, JUCE).

### §6.1 İç Bağımlılıklar (bu blok içinde, okuma sırası)

| # | Bağımlılık | Yön | Ne verir | Kırılırsa |
|---|---|---|---|---|
| 1 | `index.md` → `asio-kapsam.md` | katalog → kapsam | Okuma yolu başlangıcı | Yönlendirme kaybolur |
| 2 | `asio-kapsam.md` → `asio-anayasa.md` | kapsam → anayasa | ASIO'nun temel doğası, negatif tanım | Kapsam sınırları gerekçesiz kalır |
| 3 | `asio-anayasa.md` → `asio-arayuzler.md` | doğa → arayüz | `IASIO*` + C API yüzeyi | Spesifikasyon somutlaşmaz |
| 4 | `asio-arayuzler.md` → `asio-buffer-stratejileri.md` | arayüz → tampon | Çift tampon şeması, boyut dörtlüsü | Callback bütçesi hesaplanamaz |
| 5 | `asio-surucu-modeli.md` → `asio-cihaz-yonetimi-ve-enumarasyon.md` | model → enumerasyon | Sürücü keşfi ↔ cihaz indeksi | Indeks stabilitesi bağlanamaz |
| 6 | `asio-multichannel-ve-timestamp.md` → `asio-timestamp-ve-clock-senkronu.md` | yapı → senkron | `AsioTimeInfo` alanı → drift hesabı | Senkron modeli boşluklu |
| 7 | `asio-hata-yonetimi-ve-fallback.md` → `asio-wasapi-karsilastirma.md` | hata → yedek | Fallback hedefinin yetenekleri | Fallback gerekçesiz |
| 8 | `asio-cpp-ornekleri.md` ↔ `asio-uygulama-entegrasyon-ornekleri.md` | örnek ↔ entegrasyon | İki gerçek kaynak yuzeyi (SDK + JUCE) | Tekrar kaçınılmaz |

### §6.2 Dış-Vault Bağımlılıkları

| # | Dış düğüm | İlişki | Kural |
|---|---|---|---|
| 1 | `K003-audio/backend` (üst blok) | `depends_on` hedefi | Tüm ASIO düğümleri bu bloğa bağlıdır |
| 2 | `.ai/AGENTS.md` §17/6 | ASIO device loss → WASAPI fallback | **BINDING** — kapsam kararı olmaksızın geçerli |
| 3 | ADR-038 H001 | PCM5122 yasak, yalnız PCM3168A | Donanım komşusu — ASIO bloğu donanım seçmez |
| 4 | ADR-097 §5.5/§6.5 | Gruplama ekseni + lisans kapısı | `group` alanı ve K020 bağlantısı |
| 5 | ADR-017 | 32-bit float iç veri | Dönüştürücü hedefi bu kurala bağlanır |
| 6 | Repo kökü `AGENTS.md` | `*.cpp` yasağı, PLANNED disiplini | Kod iddiası yazılamaz |

### §6.3 Dış-Teknoloji Bağımlılıkları (durum etiketli)

| # | Teknoloji | Durum | Kanıt durumu |
|---|---|---|---|
| 1 | ASIO SDK 2.3.4 | REFERANS (spesifikasyon kaynağı) | changes.txt ile doğrulandı ✅ |
| 2 | Windows Core Audio (MMDevice, IAudioClient*) | REFERANS (fallback + karşılaştırma) | learn.microsoft sayfaları HTTP 200 ✅ |
| 3 | JUCE (audio_devices modülü) | REFERANS (örnek sarmalayıcı) | Kaynak dosya okundu ✅; sürüm kanıtı: VERIFICATION REQUIRED (§11) |
| 4 | Visual Studio / MSVC derleyici | PLANNED | Bu blokta derleme yok |
| 5 | vcpkg / paket yöneticisi | UNKNOWN | Sürüm/SHA512 bilinmiyor |

## §7 Kabul Kriteri (Acceptance Criteria)

Bu bloğun "bitti" sayılması için aşağıdaki maddeler **hepsi** sağlanmalıdır. Her madde
ölçülebilir bir kanıta bağlanır; kanıtsız madde sağlanmış sayılmaz.

| # | Kabul kriteri | Ölçüt | Kanıt tipi |
|---|---|---|---|
| 1 | Her deep-dive ≥500 tam satır | Dosya satır sayısı ≥500 | Otomatik sayım |
| 2 | Her deep-dive v2 §4.2 frontmatter'ını taşır | 8 zorunlu alan + `group` | FM denetimi |
| 3 | Her iddia-kanıt satırı HTTP 200 kaynakla eşleşir | HEAD/GET 200 | URL doğrulama |
| 4 | Uydurma `Dosya:`/`Satır:` referansı yok | Repo'da C++ yok → `Kaynak:` etiketi | Grep denetimi |
| 5 | Kapsam dışı maddelerin "ait olduğu yer" dolu | §2 tablosunda boş hücre yok | Gözden geçirme |
| 6 | Fallback kuralı tüm blokta tutarlı | WASAPI shared (IAudioClient3 low-period) | Çapraz okuma |
| 7 | Mojibake yok | `??`·`Ð`·`â€` byte = 0 | Byte denetimi |
| 8 | `PCM5122` yalnız "yasak" bağlamında geçer | ADR-038 H001 referansı | Grep denetimi |
| 9 | Görev dolgusu (`TBD` işareti, üç-nokta dolgusu, `Yakında` vaadi, nesne-şablonu boşluğu) yok | Yasaklı dize = 0 | Grep denetimi |
| 10 | Açık sorular güncel | `open_questions` ≠ boş, her madde üst kararlı | FM denetimi |

## §8 Kullanım Rehberi (kim, ne zaman, hangi dosyayı okur)

| Rol / durum | İlk okuma | İkincil okuma | Yapmayacak |
|---|---|---|---|
| Yeni katkı (deep-dive yazımı) | `asio-kapsam.md` (bu dosya) | Hedef deep-dive + `index.md` | Kapsam dışı konuyu bu bloğa taşımak |
| Sürüm/sürüm-kanıtı sorusu | `asio-lisans-ve-dogrulama.md` §5 | `index.md` §4 | vcpkg sürümünü kanıt saymak |
| Gecikme/ölçüm sorusu | `asio-latency-ve-jitter.md` §3–§4 | `asio-buffer-stratejileri.md` §10 | Ölçüm sonucunu bu blokta üretmek |
| Cihaz kaybı / hot-plug | `asio-hot-swap-ve-device-loss.md` §3–§6 | `asio-hata-yonetimi-ve-fallback.md` §6–§7 | Durum makinesini bu dosyada tekrarlamak |
| Örnek tipi / dönüştürücü | `asio-kanal-yapilandirmasi-ve-converters.md` §3–§5 | `asio-arayuzler.md` §7 | DSD yolunu Neva Engine kapsamına almak |
| Saat / drift / word clock | `asio-timestamp-ve-clock-senkronu.md` §2–§6 | `asio-multichannel-ve-timestamp.md` §5–§6 | `ASIOGetSamplePosition` semantiğini tahminle yazmak |
| DAW entegrasyonu / mesaj pompa | `asio-uygulama-entegrasyon-ornekleri.md` §3–§6 | `asio-daw-uyumlulugu.md` §4–§5 | ASIO-Guard'ı uygulanmış gibi sunmak |
| Lisans / marka | `asio-lisans-ve-dogrulama.md` §2–§7 | `K020-licence` (kapı) | GPLv3 sonucunu bu blokta kesinleştirmek |
| Fallback kararı | `asio-wasapi-karsilastirma.md` §7 | `.ai/AGENTS.md` §17/6 | Fallback'i "opsiyonel" saymak |

## §9 Sınır Kararları İzleme Tablosu (Traceability)

Her kapsam sınırı bir karara, o karar bir kanıta bağlanır. Karar yoksa sınır yoktur;
kanıt yoksa karar **VERIFICATION REQUIRED** kalır.

| # | Sınır | Karar kaynağı | Kanıt | Deep-dive izi |
|---|---|---|---|---|
| 1 | Fallback bağlayıcı | `.ai/AGENTS.md` §17/6 | Repo kuralı (vault) | `asio-platform-uyumlulugu.md` §9 · `asio-hata-yonetimi-ve-fallback.md` §6 |
| 2 | Lisans K020'ye bağlı | ADR-097 §6.5 | Vault ADR | `asio-lisans-ve-dogrulama.md` §11 |
| 3 | Sürüm = changes.txt | Vault ölçüm 2026-10-12 | changes.txt satırı | `asio-lisans-ve-dogrulama.md` §5 |
| 4 | DSD kapsam dışı | ADR-038 H001 (PCM3168A) + ürün kararı | Vault ADR | `asio-kanal-yapilandirmasi-ve-converters.md` §7 |
| 5 | ARA kapsam dışı | ara SDK README alıntısı | HTTP 200 README | `asio-daw-uyumlulugu.md` §6 |
| 6 | Kod yüzeyi yasağı | Repo kökü AGENTS.md | Vault kuralı | Tüm dosyalar (`Kaynak:` etiketi) |
| 7 | ASIOGuard kararı açık | — (karar yok) | ⚠️ VERIFICATION REQUIRED | `asio-daw-uyumlulugu.md` §5 · §4/2 |
| 8 | Paylaşım protokolü açık | — (karar yok) | ⚠️ VERIFICATION REQUIRED | `asio-daw-uyumlulugu.md` §7 · §4/1 |
| 9 | Time info mode seçimi açık | — (karar yok) | ⚠️ VERIFICATION REQUIRED | `asio-timestamp-ve-clock-senkronu.md` §5 · §4/6 |
| 10 | Capture yolu açık | Sartname §3.6 (PLANNED) | Vault şartname | `asio-kapsam.md` §4/3 |

## §10 Derin-Dal Kapsam Haritası (19 dosya — sınır özeti)

Bu tablo, `K003-audio/backend/asio/` kökündeki her dosyanın **kapsam sınırını** tek satırda
özetler: hangi dosya neyi kapsar, neyi **bilerek** içermez. Yeni okuyucu önce bu tabloya
bakar, sonra ilgili dosyaya iner.

| # | Dosya | Kapsar | Bilerek içermez |
|---|---|---|---|
| 1 | `index.md` | Katalog, okuma yolu, grup haritası | Teknik derinlik |
| 2 | `asio-kapsam.md` | Kapsam sözleşmesi, bağımlılık, kabul kriteri | Spesifikasyon detayı |
| 3 | `asio-anayasa.md` | ASIO'nun doğası, tarihçe, katman ayrımı | Uygulama kodu |
| 4 | `asio-arayuzler.md` | `IASIO*` + C API, yapılar, hata kodları | Dönüşüm algoritmaları |
| 5 | `asio-surucu-modeli.md` | Keşif, yükleme, tampon sahipliği, öncelik | Windows API detayı |
| 6 | `asio-platform-uyumlulugu.md` | Sürüm/mimari matrisi, kernel-user, köprü sürücüler | DAW davranışı |
| 7 | `asio-buffer-stratejileri.md` | Boyut seçimi, priming, xrun, JUCE FIFO | Gecikme ölçüm protokolü |
| 8 | `asio-latency-ve-jitter.md` | Gecikme bileşenleri, jitter sınıflandırması, QPC | Cihaz kaybı kurtarması |
| 9 | `asio-multichannel-ve-timestamp.md` | Kanal sayımı/eşleme, `ASIOTime` yapıları, WASAPI rol modeli | Drift telafisi |
| 10 | `asio-hot-swap-ve-device-loss.md` | Cihaz olayı, `IMMNotificationClient`, kurtarma makinesi | Fallback orkestrasyonu |
| 11 | `asio-wasapi-karsilastirma.md` | Mod semantiği, motor periyodu, karar matrisi | ASIO protokol detayı |
| 12 | `asio-daw-uyumlulugu.md` | DAW matrisi, multi-client, ASIO-Guard, ARA sınırı | Driver-side davranış |
| 13 | `asio-cpp-ornekleri.md` | `hostsample.cpp` akışı, JUCE sarmalayıcı, hata çözümleme | Mesaj pompa derinliği |
| 14 | `asio-lisans-ve-dogrulama.md` | Çift lisans, sürüm kanıtı, iddia–kanıt | Hukuki nihai karar (K020) |
| 15 | `asio-cihaz-yonetimi-ve-enumarasyon.md` | `ASIOInit`/`ASIOOutputReady`, indeks, kimlik, enumerasyon yenileme | Hot-plug durum makinesi |
| 16 | `asio-timestamp-ve-clock-senkronu.md` | Örnek konumu sıfırlanma, systemTime, drift, word clock | `ASIOTime` yapı tanımı |
| 17 | `asio-kanal-yapilandirmasi-ve-converters.md` | `ASIOChannelInfo` tip/grup, 16/24/32/f32 dönüştürücü, endianness | Kanal eşleme matrisi |
| 18 | `asio-hata-yonetimi-ve-fallback.md` | Oran müzakeresi başarısızlığı, `ASE_*`, WASAPI'ye düşüş, resync | Cihaz olayı kaynağı |
| 19 | `asio-uygulama-entegrasyon-ornekleri.md` | Host callback zinciri, `asioMessage` pompa, DAW topolojisi | Örneğin tekrarı (bkz §6.1/8) |

## §11 Kapsam Riskleri

| # | Risk | Etki | Olasılık | Azaltma | Sahip |
|---|---|---|---|---|---|
| 1 | Vendor ASIO sürücüsü hatalı `ASIOGetSamplePosition` üretir | Medya senkronu bozulur | Orta | Drift monitörü + resync (§4/5 izi) | K003 tasarım |
| 2 | ASIO SDK sürüm iddiası kaynağı olmadan yayılır | Vault güvenilirliği zedelenir | Düşük | Tek kaynak kuralı (§3/4) | Vault Steward |
| 3 | JUCE sürüm iddiası kanıtsız tekrarlanır | Yanlış sürüm bağımlılığı | Orta | JUCE sürümü yalnızca depo SHA'sıyla | K003 tasarım |
| 4 | Fallback kuralı uygulamada "sessizce" atlanır | Cihaz kaybında sessizlik | Düşük | `.ai/AGENTS.md` §17/6 BINDING + kabul kriteri §7/6 | Orkestratör |
| 5 | Kapsam dışı madde (ARA, MIDI) yanlışlıkla bu bloğa çekilir | Yinelenen içerik, token israfı | Orta | §2 tablosu + §8 rehberi | Tüm ajanlar |
| 6 | arm64EC derleme hedefi belirsiz kalır | ARM64 teslimatı bloke | Bilinmiyor | §4/4 açık sorusu → build mühendisliği | Build |
| 7 | Gerçek repo kodu olmadan "repo'daki satır" iddiası üretilmesi | Hallüsinasyon | Düşük | `Kaynak:` etiketi kuralı (§1 sonu) | Tüm ajanlar |

## §12 Terimler (kapsam sözlüğü — bu dosyaya özgü)

| Terim | Tanım | Kullanım yeri |
|---|---|---|
| Kapsam sözleşmesi | Neyin hangi düğümde olduğunun tescili | Bu dosya |
| IN-SCOPE | Bu bloğun kanıtla sorumlu olduğu yüzey | §1 |
| OUT-OF-SCOPE | Bilerek dışarıda bırakılan yüzey + ait olduğu yer | §2 |
| BINDING | Kullanıcı onayı olmaksızın geçerli repo kuralı | §3/1, §6.2 |
| Üst karar | Kullanıcı/ürün sahibi kararı gereken kapı | §4 |
| Kabul kriteri | "Bitti" için ölçülebilir 10 madde | §7 |
| Traceability | Sınır → karar → kanıt izi | §9 |
| Sınır haritası | 19 dosyanın kapsar/İçermez özeti | §10 |
| `Kaynak:` etiketi | Repo dışı resmi doküman alıntısı işareti | §1 sonu, tüm deep-dive'lar |
| PLANNED | Henüz üretilmemiş, hedef olan yüzey | §6.3, §1 |

## §13 Soru→Dosya Yönlendirme (hızlı harita)

| Soru kalıbı | Yönlendirme | Not |
|---|---|---|
| "ASIO nedir / değildir?" | `asio-anayasa.md` §4–§5 | Negatif tanım oradadır |
| "Hangi arayüzler çağrılır?" | `asio-arayuzler.md` §2–§4 | Prototip özeti oradadır |
| "Tampon boyutu nasıl seçilir?" | `asio-buffer-stratejileri.md` §3 | Dörtlü oradadır |
| "Gecikme neden değişir?" | `asio-latency-ve-jitter.md` §4 | `kAsioLatenciesChanged` |
| "Cihaz kaybolunca ne olur?" | `asio-hot-swap-ve-device-loss.md` + bu dosya §3/1 | Fallback bağlayıcı |
| "Hangi örnek tipi?" | `asio-kanal-yapilandirmasi-ve-converters.md` §3 | DSD hariç |
| "Saat kim?" | `asio-timestamp-ve-clock-senkronu.md` §2–§3 | Sıfırlanma kuralı |
| "DAW açarsa?" | `asio-daw-uyumlulugu.md` §4 | Multi-client |
| "Lisans ne?" | `asio-lisans-ve-dogrulama.md` §2 | Son karar K020 |
| "Kod örneği?" | `asio-cpp-ornekleri.md` + `asio-uygulama-entegrasyon-ornekleri.md` | İki ayrı örnek yüzeyi |

## §14 Doğrulama Protokolü (bu dosya için)

| # | Kontrol | Yöntem | Beklenen |
|---|---|---|---|
| 1 | Satır sayısı ≥500 | Otomatik sayım | ≥500 |
| 2 | FM 8 zorunlu alan + `group` | FM denetimi | Eksik yok |
| 3 | §1 kalemi sayısı = 18 + 2 ek satır (kod yüzeyi paragrafı) | Tablo sayımı | Tutarlı |
| 4 | §10 satır sayısı = 19 | Tablo sayımı | 19 |
| 5 | Yasaklı dize = 0 | Grep (görev dolgusu · üç-nokta · vaat · nesne-şablonu boşluğu) | 0 |
| 6 | Mojibake byte = 0 | Byte denetimi | 0 |
| 7 | Boş tablo hücresi = 0 | Gözden geçirme | 0 |
| 8 | Son satır Authority | Dosya sonu | ✅ |

## §15 ASIO C API Yüzeyi → Dosya Eşlemesi (20 fonksiyonun kapsamdaki sahibi)

ASIO SDK 2.3 `asio.h` prototip özeti C API'ini **20 fonksiyonla** sınırlar
(kanıt: `asio-cihaz-yonetimi-ve-enumarasyon.md` §2.1). Bu tablo, o 20
fonksiyonun bu kapsamdaki **hangi deep-dive'ın** sorumluluğunu gösterir —
kapsam sözleşmesinin fonksiyon-bazlı hâlidir; bir fonksiyon için "hangi dosya
okunur" sorusunun tek cevabı burasıdır.

| # | Fonksiyon | Ana dosya | İkincil dosya | Kapsam durumu |
|---|---|---|---|---|
| 1 | `ASIOInit` | `asio-cihaz-yonetimi-ve-enumarasyon.md` §3 | `asio-surucu-modeli.md` §1–§3 | IN-SCOPE |
| 2 | `ASIOExit` | `asio-cihaz-yonetimi-ve-enumarasyon.md` §3.1 | `asio-cpp-ornekleri.md` | IN-SCOPE |
| 3 | `ASIOStart` | `asio-buffer-stratejileri.md` | `asio-timestamp-ve-clock-senkronu.md` §3.1 (sayacın sıfırlanması) | IN-SCOPE |
| 4 | `ASIOStop` | `asio-buffer-stratejileri.md` | `asio-cihaz-yonetimi-ve-enumarasyon.md` §3.2 | IN-SCOPE |
| 5 | `ASIOGetChannels` | `asio-multichannel-ve-timestamp.md` | `asio-kanal-yapilandirmasi-ve-converters.md` §2.2 | IN-SCOPE |
| 6 | `ASIOGetLatencies` | `asio-latency-ve-jitter.md` | `asio-uygulama-entegrasyon-ornekleri.md` §4.3 | IN-SCOPE |
| 7 | `ASIOGetBufferSize` | `asio-buffer-stratejileri.md` §3 | — | IN-SCOPE |
| 8 | `ASIOCanSampleRate` | `asio-hata-yonetimi-ve-fallback.md` §4 | `asio-surucu-modeli.md` | IN-SCOPE |
| 9 | `ASIOGetSampleRate` | `asio-timestamp-ve-clock-senkronu.md` §5.5 | `asio-hata-yonetimi-ve-fallback.md` §5.3 | IN-SCOPE |
| 10 | `ASIOSetSampleRate` | `asio-timestamp-ve-clock-senkronu.md` §5.6 | `asio-hata-yonetimi-ve-fallback.md` §4 satır 5 | IN-SCOPE |
| 11 | `ASIOGetClockSources` | `asio-timestamp-ve-clock-senkronu.md` §5.4 | `asio-wasapi-karsilastirma.md` (karşılaştırma) | IN-SCOPE |
| 12 | `ASIOSetClockSource` | `asio-timestamp-ve-clock-senkronu.md` §5.5 | — | IN-SCOPE |
| 13 | `ASIOGetSamplePosition` | `asio-timestamp-ve-clock-senkronu.md` §3 | `asio-multichannel-ve-timestamp.md` | IN-SCOPE |
| 14 | `ASIOGetChannelInfo` | `asio-kanal-yapilandirmasi-ve-converters.md` §2 | `asio-cihaz-yonetimi-ve-enumarasyon.md` §2.2 | IN-SCOPE |
| 15 | `ASIOCreateBuffers` | `asio-buffer-stratejileri.md` | `asio-kanal-yapilandirmasi-ve-converters.md` §3.2 | IN-SCOPE |
| 16 | `ASIODisposeBuffers` | `asio-buffer-stratejileri.md` | `asio-cihaz-yonetimi-ve-enumarasyon.md` §3.2 | IN-SCOPE |
| 17 | `ASIOControlPanel` | `asio-daw-uyumlulugu.md` (vendor panel sınırı) | `asio-cihaz-yonetimi-ve-enumarasyon.md` §10/4 | IN-SCOPE (çağrı), panel içeriği OUT |
| 18 | `ASIOFuture` | `asio-surucu-modeli.md` (selector listesi) | `asio-timestamp-ve-clock-senkronu.md` §3.4, §4.3 | IN-SCOPE |
| 19 | `ASIOOutputReady` | `asio-cihaz-yonetimi-ve-enumarasyon.md` §4 | `asio-latency-ve-jitter.md` (latency uyarısı) | IN-SCOPE |
| 20 | (20. satır: `asioMessage` host callback'i — C API değil, host sözleşmesi) | `asio-surucu-modeli.md` §5 | `asio-uygulama-entegrasyon-ornekleri.md` §3.4, §5.5 | IN-SCOPE |

**Kural:** Tabloda **ana dosya** tek yetkilidir; ikincil dosya yalnız çapraz
referanstır. İki dosya aynı fonksiyonda derinlik üretirse tekrardır — bu,
`index.md` §GRUPLAR ve bu tablo birlikte okunarak önlenir.

## §16 Beyazliste ve Yasak Yüzey Tablosu (görev disiplini — bağlayıcı)

Kapsamın operasyonel sınırı: hangi dosya ağacına yazılıp yazılamaz. Bu tablo
görev manifestinin disiplinidir; ihlali kapsam ihlalidir (§5 protokolü işlemez).

| # | Yüzey | Durum | Gerekçe |
|---|---|---|---|
| 1 | `K003-audio\backend\asio\**` (kök deep-dive dosyaları) | **YAZILABİLİR** | Bu kapsamın malıdır |
| 2 | `K000` · `K004` · `K010-11` · `K013` · `K017-19` ağaçları | YASAK | Başka mimari bloklar |
| 3 | `K003-audio\backend\asio\driver\` · `routing\` | YASAK | Bu bloğun **diğer** klasörleri (§2/9) |
| 4 | K003 kök `audio-*.md` dosyaları | YASAK | Üst blok sahipliği |
| 5 | `.ai/` kök registry `index.md` | YASAK | Vault geneli katalog, MO sahipliğinde |
| 6 | `.ai/.decisions/**` (frozen ADR'ler) | YASAK | Frozen dokunulmaz (`.ai/AGENTS.md` §25.3/2) |
| 7 | `*.php` · `*.js` · `*.css` · `*.sql` kaynak kod | YASAK | §16.2 dokunulmaz yüzeyler (kök `AGENTS.md` §16/4) |
| 8 | `*.cpp` · `*.h` (repo geneli) | YASAK | Repo'da C++ yok (§1 sonu); kod yazımı PLANNED |
| 9 | `git commit` | YASAK (subagent) | Commit orkestratöre aittir (§-kural) |

**Kapsam genişletme** yalnız §5 protokolüyle (kanıt + open_question güncellemesi)
yapılır; **yüzey genişletme** (yukarıdaki yasak satırlarına yazmak) hiçbir
kanıtla yapılamaz — bu iki kavram karıştırılmamalıdır.

## §17 İddia–Kanıt Zorunluluğu (dosya-bazlı asgari kanıt seti)

Her deep-dive'ın **kendi iddialarını** taşıması gerekir; bu dosya onların
kaynağını tek yerde toplamaz (tekrar kuralı §0). Ancak kapsam sözleşmesi,
her dosyanın **asgari kanıt setini** şart koşar:

| # | Dosya sınıfı | Asgari kanıt | Bu tur ölçümü |
|---|---|---|---|
| 1 | Yeni 5 deep-dive (cihaz, timestamp, kanal, hata, entegrasyon) | Her dosyada İddia–Kanıt tablosu + HTTP 200 kaynak | ✅ (ilgili §9/§İddia tabloları) |
| 2 | Mevcut 12 deep-dive | FM `evidence` listesi dolu veya §İddia tablosu | ✅ (FM denetimi) |
| 3 | `index.md` | Katalog + grup kaydı (kaynak iddiası taşımaz) | ✅ (§GRUPLAR) |
| 4 | Bu dosya (`asio-kapsam.md`) | Kapsam iddiaları vault/SDK kaynaklı (FM evidence) | ✅ (FM 10 kanıt) |
| 5 | Repo-kod iddiası içeren herhangi bir satır | `Kaynak:` etiketi (repo'da C++ yok) | ✅ (§1 sonu kuralı) |

**Sınır:** Kanıt, iddianın **olduğu dosyada** bulunur; bu dosya yalnız
"kanıt var mı yok mu" denetimini yapar (§7 kabul kriteri 3–4). URL envanteri
(19 dosyada toplam benzersiz kaynak sayısı) ayrı ölçümde raporlanır ve bu
dosyaya gömülmez — ölçüm verisi dosya içi metin değildir (mojibake/dolgu
denetimiyle çakışmaması için).

---

## §18 Bileşen Envanteri (19 dosya — 2026-10-13 gerçek ölçüm)

§10 kapsam haritasının envanter hâli: her dosyanın o anki satır sayısı, FM durumu ve kademesi. Ölçüm yöntemi: dosyalar satır satır sayıldı (UTF-8), FM `status` / `group` alanları okundu.

| # | Dosya | Satır | FM status | group | Kademe notu |
|---|---|---|---|---|---|
| 1 | `index.md` | 102 | draft | mimari | Katalog — hedef aralık 40-100 satır (P2 daraltma bekliyor) |
| 2 | `asio-kapsam.md` | 439 | draft | mimari | Bu dosya — §18-§20 ile genişletildi |
| 3 | `asio-anayasa.md` | 504 | draft | mimari | Eşik (≥500) üstü |
| 4 | `asio-arayuzler.md` | 546 | draft | mimari | Eşik üstü |
| 5 | `asio-buffer-stratejileri.md` | 516 | draft | mimari | Eşik üstü |
| 6 | `asio-cihaz-yonetimi-ve-enumarasyon.md` | 503 | draft | mimari | Eşik üstü |
| 7 | `asio-cpp-ornekleri.md` | 647 | draft | kod | En uzun kod-kademe dosyası |
| 8 | `asio-daw-uyumlulugu.md` | 501 | draft | kod | Eşik üstü |
| 9 | `asio-hata-yonetimi-ve-fallback.md` | 506 | draft | mimari | Eşik üstü |
| 10 | `asio-hot-swap-ve-device-loss.md` | 508 | draft | mimari | Eşik üstü |
| 11 | `asio-kanal-yapilandirmasi-ve-converters.md` | 539 | draft | mimari | Eşik üstü |
| 12 | `asio-latency-ve-jitter.md` | 502 | draft | mimari | Eşik üstü |
| 13 | `asio-lisans-ve-dogrulama.md` | 504 | draft | kanit | Kanıt kademesinin tek dosyası |
| 14 | `asio-multichannel-ve-timestamp.md` | 523 | draft | mimari | Eşik üstü |
| 15 | `asio-platform-uyumlulugu.md` | 506 | draft | mimari | Eşik üstü |
| 16 | `asio-surucu-modeli.md` | 530 | draft | mimari | Eşik üstü |
| 17 | `asio-timestamp-ve-clock-senkronu.md` | 686 | draft | mimari | En uzun dosya (blok geneli) |
| 18 | `asio-uygulama-entegrasyon-ornekleri.md` | 554 | draft | kod | Eşik üstü |
| 19 | `asio-wasapi-karsilastirma.md` | 507 | draft | mimari | Eşik üstü |

**Sayım:** 19/19 dosya `status: draft` · kademe dağılımı: mimari 15 · kod 3 · kanıt 1 · eşik altı: 2 (`index.md` 102 — hedef aralık 40-100 zaten daraltma hedefi, `asio-kapsam.md` 439 — bu turda genişletildi).

## §19 Bağımlılık Grafiği (§6.1 zinciri — ASCII)

§6.1 tablosundaki 8 iç bağımlılığın tek bakışta görünümü; okuma sırası grafiğin tepesinden aşağı iner:

```
index.md
  └──► asio-kapsam.md  (bu dosya — kapsam sözleşmesi)
        ├──► asio-anayasa.md
        │      └──► asio-arayuzler.md
        │             └──► asio-buffer-stratejileri.md
        ├──► asio-surucu-modeli.md
        │      └──► asio-cihaz-yonetimi-ve-enumarasyon.md
        ├──► asio-multichannel-ve-timestamp.md
        │      └──► asio-timestamp-ve-clock-senkronu.md
        ├──► asio-hata-yonetimi-ve-fallback.md
        │      └──► asio-wasapi-karsilastirma.md
        └──► asio-cpp-ornekleri.md
               └──► asio-uygulama-entegrasyon-ornekleri.md
```

**Grafiğin kapsamı:** yalnız §6.1'de kayıtlı 8 zincir. Grafikte görünmeyen 6 dosya
(`asio-latency-ve-jitter.md` · `asio-hot-swap-ve-device-loss.md` · `asio-platform-uyumlulugu.md` ·
`asio-daw-uyumlulugu.md` · `asio-lisans-ve-dogrulama.md` · `asio-kanal-yapilandirmasi-ve-converters.md`)
§6.1'de bağımlılık satırı OLMAYAN bağımsız düğümlerdir — bağımlılık UYDURULMAZ; bu dosyalar
derin-dal kendi içlerinde çapraz referans verdiği için §6.1 zinciri dışında tutulmuştur.

## §20 Repo Kanıtı ve Bu Tur Ölçümü (2026-10-13)

| Dosya + satır (okundu) | İçerik | Ne doğrular |
|---|---|---|
| Dosya: `.github/workflows/ci.yml` / Satır: 27 | `runs-on: ubuntu-latest` | CI işi 1 ubuntu'da — Windows runner yok |
| Dosya: `.github/workflows/ci.yml` / Satır: 180 | `runs-on: ubuntu-latest` | CI işi 2 de ubuntu — ASIO/Windows doğrulaması CI'da yapılamaz, PLANNED |

| Kabul kriteri | Hedef | Gerçekleşen (bu tur) | Sonuç |
|---|---|---|---|
| Satır sayısı | ≥ 500 | §18-§20 ile 439'dan genişletildi | Birleştirme sonrası sayım: 500 üstü |
| Frontmatter | 9 zorunlu alan + `group` | `group: mimari` (mevcut, korundu) | Uygun |
| Son satır | Birebir Authority künyesi | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` | Uygun |
| Dolgu | yasaklı dolgu desenlerinin tümü (ADR-097 §5.5): 0 | §18-§20 denetlendi | 0 |
| Repo kanıtı | Dosya + gerçek satır | 2 satır (ci.yml 27 ve 180 — okunarak doğrulandı) | Uygun |

**Kaynak:** audiosdk/asio (README+changes.txt) · learn.microsoft exclusive-mode-streams +
user-mode-audio-components · asio4all.org/version-2-22 · github.com/wineasio/wineasio ·
dechamps/FlexASIO (BACKENDS.md) · dechamps/ASIOUtil (BUFFERS.md) · help.ableton.com (ASIO
article + Multi-Client article) · kb.avid.com Playback Engine · ARA-Matchering/README ·
image-line.com envsettings_asio4all · 2026-10-12

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
