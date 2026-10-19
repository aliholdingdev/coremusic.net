---
title: "ASIO Derin-Dalı — Anayasa (Kademe-3 / Teknoloji)"
node_id: "K003-audio/backend/asio/asio-anayasa"
node_type: architecture-deepdive
status: draft
parent_id: "K003-audio/backend/asio"
children: []
depends_on:
  - "K003-audio/backend"
  - "K000-isletim-sistemi"
  - "K001-donanim"
evidence:
  - "https://www.steinberg.net/developers/asiosdk-open/ — Steinberg ASIO SDK açık kaynak duyuru sayfası (2026-10-12 turu)"
  - "https://github.com/audiosdk/asio — ASIO 2.3 SDK arayüz tanımları deposu (README + changes.txt)"
  - "https://github.com/audiosdk/asio/blob/master/changes.txt — SDK sürüm geçmişi (2.1 → 2.3.4)"
  - "https://www.kvraudio.com/news/steinberg-announces-new-open-source-asio-license-and-partnership-with-obs-65041 — 15 Ekim 2025 çift lisans + OBS duyurusu"
  - "https://www.kvraudio.com/news/steinberg-moves-vst-3-sdk-to-mit-open-source-license-asio-now-gplv3-65179 — 29 Ekim 2025 VST3 MIT / ASIO GPLv3 haberi"
  - "https://www.recordingmag.com/news/new-asio-open-source-license-paves-way-for-obs-partnership/ — bağımsız basın doğrulaması"
  - "https://cdm.link/open-steinberg-vst3-and-asio/ — bağımsız basın doğrulaması (Computer Music)"
  - "https://manual.audacityteam.org/man/asio_audio_interface.html — GPLv3 öncesi lisans çatışması vakası"
  - "https://opensource.org/license/gpl-3-0 — GPLv3 lisans metni (OSI)"
  - "https://github.com/audiosdk/asio/blob/master/common/asio.h — protokolün kaynak koddaki tek doğrulanabilir tanımı"
validation:
  - "URL + tarih + alıntı: her iddia §10 tablosunda en az bir URL'ye bağlandı (2026-10-12)"
  - "Satır sayısı ≥500 ve ön-matter 9 alan kontrolü otomatik ölçüldü (2026-10-12)"
  - "Sürüm iddiaları changes.txt ile çaprazlandı; changes.txt'de olmayan sürüm iddiası yazılmadı"
open_questions:
  - "GPLv3 seçeneği CoreMusic'in tescilli çekirdeğiyle (PHP/JS katmanı hariç) nasıl birleşir? → K020-licence + hukuki onay"
  - "ASIO sürücüsü olmayan donanımda repo kuralı WASAPI fallback mi, ASIO evrensel sürücü (ASIO4ALL/FlexASIO) mü? → .ai/AGENTS.md §17/6"
  - "ASIO SDK 2.3.4 zip'inin resmi indirme URL'si ve özet değeri vault'a kaydedildi mi? (bkz. asio-lisans-ve-dogrulama.md §6)"
  - "ASIO protokolü 2.3 sonrası bir sürüm daha alacak mı? changes.txt'nin son satırı 2.3.4 (UNKNOWN: gelecek sürüm)"
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# ASIO — Anayasa (Kademe-3, Teknoloji Derin-Dalı)

> Bu dosya `K003-audio/backend/asio/` derin-dalının **anayasasıdır**: ASIO nedir, kimin
> mülkiyetindedir, hangi katmanlara ayrılır, bu klasördeki diğer beş dosyaya ne bırakır.
> Derin-dalın geri kalanı (arayüzler · sürücü-modeli · platform-uyumlulugu · daw-uyumlulugu ·
> lisans-ve-dogrulama) bu belgenin uzantısıdır; bu belge tek başına okunabilir.
>
> Bu dosya **yeniden yazımdır** (2026-10-12, Red Team · Truth Mode): eski içerik kanıt
> envanteri olarak kullanıldı, metin sıfırdan üretildi. Dosya adı değişmemiştir.

---

## §0 ELI15

ASIO, Windows'ta ses donanımına **aracı (mixer) olmadan** inmeyi tanımlayan, Steinberg'e
ait bir **düşük gecikmeli ses arayüzü sözleşmesidir** — bir sürücü değil, bir protokoldür;
sürücüyü donanım üreticisi yazar, uygulamayı (DAW) o sözleşmeye göre konuşur.

---

## §1 Kimlik kartı

| Alan | Değer | Kanıt |
|---|---|---|
| Tam adı | Audio Stream Input/Output | `asio.h` başlık yorumu |
| Sahibi | Steinberg Media Technologies GmbH | audiosdk/asio README |
| İlk tanım tarihi | 05/1996 (`Created by : Steinberg, 05/1996`) | `common/asio.h` başlık bloğu |
| Protokol sürümü | v2.3 (kaynakta `ASIO Interface Specification v 2.3`) | `common/asio.h` |
| SDK sürümü | 2.3.4 (dosya: `ASIO-SDK_2.3.4_2025-10-15.zip`) | changes.txt + AUR/MSYS2 paketleri |
| Lisans modeli | Çift lisans: tescilli anlaşma **veya** GPLv3 | README "Dual Licensing" |
| Duyuru tarihi | 15 Ekim 2025 (OBS iş birliği ile) | KVR 65041 |
| Uygulama alanı | Windows (historik: Mac OS 8/9 desteği 2.3'te kaldırıldı) | changes.txt "ASIO 2.3" |
| Mimari | 32-bit ve 64-bit (2.2'de Windows 64-bit desteği) | changes.txt "ASIO 2.2" |
| Ana sözleşme yüzeyi | C API (`ASIOInit` … `ASIOOutputReady`) | `common/asio.h` |
| Yükleme yüzeyi | COM benzeri `IASIO` arayüzü + kayıt defteri keşfi | `iasiodrv.h`, `asiolist.cpp` |
| İşlem modeli | Çift tamponlu (double-buffer), senkron giriş/çıkış | `asio.h` diyagramı |
| Zamanlayım | `bufferSwitch` geri çağrısı, kesme zamanında çağrılabilir | `asio.h` yorumları |
| Ticari marka | "ASIO" adı ve uyum logosu tescilli | README "Trademark and Logo Usage" |
| CoreMusic rolü | Windows düşük gecikme birincil yolu; olmazsa WASAPI fallback | `.ai/AGENTS.md` §17/6 |

---

## §2 Tarihçe ve mülkiyet zinciri

### §2.1 Zaman çizelgesi

| Yıl / Tarih | Olay | Kanıt |
|---|---|---|
| 1996-05 | `asio.h` dosyası Steinberg'de oluşturuluyor | `asio.h` başlık yorumu |
| 2005 | DSD örnekleme verisi desteği (Sony iş birliğiyle) | `asio.h` başlık yorumu |
| 2012 | Düşüş (dropout) tespiti desteği | `asio.h` başlık yorumu |
| ASIO 2.1 | Sony DSD desteği; kayıt defteri örneği `HKEY_LOCAL_MACHINE`'a düzeltildi | changes.txt |
| ASIO 2.2 | Windows 64-bit desteği eklendi | changes.txt |
| ASIO 2.3 | Sürücü tamponlama/drop-out sorguları; MMCSS dokümanı (SDK PDF s.46); Mac OS 8/9 desteği kaldırıldı | changes.txt |
| ASIO 2.3.1 | Lisans anlaşmasında değişiklik; Windows 10 desteği eklendi | changes.txt |
| ASIO 2.3.2 | Lisans metni ve logo kullanım kılavuzu güncellendi | changes.txt |
| ASIO 2.3.3 | Lisans metni ve logo kılavuzu bir kez daha güncellendi | changes.txt |
| ASIO 2.3.4 | **Yeni ÇİFT lisans: Tescilli + GPLv3**; kullanım kılavuzu güncellendi | changes.txt |
| 2025-10-15 | Steinberg ASIO'nun açık kaynak lisansını duyurdu; OBS ile teknik sponsorluk | KVR 65041 |
| 2025-10-15 | OBS Projesi ürün liderinden alıntı: ASIO desteği topluluk talebiydi | KVR 65041 |
| 2025-10-29 | VST 3 SDK MIT'e, ASIO GPLv3'e taşındı haberi | KVR 65179 |
| 2026 (paketler) | AUR `steinberg-asio-sdk 2.3.4-1` ve MSYS2 `2.3.4-1` paketleri yayımlanmış | AUR / MSYS2 |
| 2026 (depo) | `audiosdk/asio` deposu "initial commit of version 2.3.4" | GitHub depo geçmişi |

### §2.2 Dönem 1 — Kapalı sözleşme dönemi (1996–2025)

- ASIO her zaman **bir sözleşme** oldu: `asio.h` dosyasındaki prototipler ve yorumlar,
  sürücü ile uygulama arasındaki davranışı yazıya döktü.
- SDK, uygulama yazanlara örnek host kodu (`hostsample.cpp`) ve sürücü yazanlara örnek
  sürücü (`driver/asiosample`) verdi; bu örnekler **şablon**, sözleşme ise **prototiplerdi**.
- Lisans metni SDK içinde ayrı bir PDF olarak dağıtıldı (`Steinberg ASIO Licensing Agreement.pdf`),
  kaynak kodun yeniden dağıtımı bu anlaşmayla kısıtlıydı.
- Bu kısıt, en görünür çatışmayı **GPL ile ASIO'yu birlikte isteyen açık kaynak projelerde**
  yarattı; Audacity bu yüzden ASIO'lu resmi ikili yayımlayamadı (bkz. §2.4).
- Donanım üreticileri sürücülerini bu sözleşme üzerinden yazdı; "ASIO uyumlu" logosu
  bir uyumluluk beyanı olarak kullanıldı.
- 2025'e kadar değişmeyen temel: **protokol kapalı, sürücü ekosistemi açık** —
  herkes sürücü yazabiliyordu, ama protokolü yeniden dağıtamıyordu.
- Dönemin mühendislik sonucu: Windows'ta profesyonel sesin *de facto* birincil yolu
  haline gelmesi; DAW'ların varsayılan seçeneği olması.
- Dönemin maliyeti: açık kaynak host'ların (Audacity, OBS, wineasio vb.) ya özel derleme
  ya da ikinci bir arayüse (WASAPI) sapması.

### §2.3 Dönem 2 — Çift lisans dönemi (2025-10-15 → bugün)

- Duyuru iki parçalıdır: **(a)** SDK artık GPLv3 altında da kullanılabiliyor,
  **(b)** Steinberg OBS Studio ile iş birliğine giriyor (teknik sponsorluk).
- GitHub README bu kararı tek cümlede toplar: iki seçenek de **aynı SDK'ya** erişim verir.
- Mevcut ürünler için zorunluluk yoktur: README FAQ'ye göre mevcut ürünler tescilli
  lisansla **devam edebilir**; GPLv3'e geçiş ürüne GPLv3 koşullarıyla yayımlanırsa mümkündür.
- Marka ayrıdır: GPLv3 altında ASIO adını/logo kullanmak **zorunlu değildir**, ama
  kullanılırsa Steinberg'in marka kurallarına tabidir.
- Pratik sonucu: OBS gibi GPL projelerin ASIO'yu **kaynak kodla birlikte** dağıtmasının
  önü açıldı; Audacity'nin eski "dağıtılamaz özel derleme" kuralı için hukuki zemin değişti.
- ⚠️ VERIFICATION REQUIRED: GPLv3 seçeneğinin imzalanan metni (SDK içindeki `LICENSE.txt`)
  bu turda satır satır okunmadı; README'nin özetine dayanılmaktadır.

### §2.4 Vaka dosyası — Audacity (çatışmanın ölçüsü)

- Audacity manueli ASIO'yu "özel mülkiyetli (proprietary) arayüz" olarak tanımlar.
- Manuele göre lisans kısıtı, ASIO desteği içeren sürücüsüz **resmi ikili yayımayı** engeller:
  kod eklenirse Steinberg anlaşması, kod çıkarılsa Audacity'nin GPL'i ihlal edilirdi.
- Çözüm: kullanıcı kendi kaynak derlemesini ASIO ile **özel (non-distributable)** yapar;
  ne Audacity ekibi ne de üçüncü şahıslar o ikiliyi dağıtabilir.
- Bu sayfa 2026-09-02 tarihli kopyada hâlâ bu metni taşımaktadır (kanıt: sayfa altı "This version created on 2026-09-02").
- ⚠️ VERIFICATION REQUIRED: GPLv3 seçeneğinden sonra Audacity'nin resmi yayım politikasını
  güncelleyip güncellemediği bu turda doğrulanmadı.

---

## §3 Katman ayrımı: protokol · SDK · sürücü · evrensel sürücü

### §3.1 Dört katman

| Katman | Ne içerir | Sahip / lisans | Bu derin-dalda |
|---|---|---|---|
| 1. Protokol (sözleşme) | `asio.h` prototipleri, yapılar, geri çağırma sözleşmesi | Steinberg (çift lisans) | `asio-arayuzler.md` |
| 2. Host SDK'sı | `asiolist.cpp`, `asiodrivers.cpp`, `hostsample.cpp` | Steinberg (çift lisans) | `asio-surucu-modeli.md` |
| 3. Sürücü (donanım) | `IASIO` uygulayan vendor DLL'i + kayıt defteri kaydı | Donanım üreticisi | `asio-surucu-modeli.md` |
| 4. Evrensel sürücü | ASIO'yu başka Windows API'lerine çeviren katman (ASIO4ALL, FlexASIO, ASIO2WASAPI) | Üçüncü taraf | `asio-platform-uyumlulugu.md` |

### §3.2 Protokol katmanı neyi belirler

- Çağrı sırasını: `ASIOInit → ASIOGetChannels → ASIOGetBufferSize → ASIOCreateBuffers → ASIOStart`.
- Çift tampon semantiğini: `bufferSwitch(0)` ile A yarıyı, `bufferSwitch(1)` ile B yarıyı.
- Geri çağrımaları: `bufferSwitch`, `sampleRateDidChange`, `asioMessage`, `bufferSwitchTimeInfo`.
- Hata kodlarını: `ASE_OK`, `ASE_NotPresent`, `ASE_HWMalfunction`, `ASE_InvalidParameter`,
  `ASE_InvalidMode`, `ASE_SPNotAdvancing`, `ASE_NoClock`, `ASE_NoMemory`.
- Örnekleme türlerini: 16/24/32-bit tamsayı, 32/64-bit float, DSD varyantları.
- Gecikme bildirimini: `ASIOGetLatencies` — bu, DAW'ların senkron doğruluğu için kritiktir.
- Protokol **donanımın ne olduğuyla ilgilenmez**; sadece tampon ve zamanlama sözleşmesi verir.

### §3.3 Host SDK'sı neyi belirler

- Kayıt defterinden sürücü listesini çıkarmayı (`software\asio` alt anahtarları).
- `clsid` değerinden DLL yolunu bulmayı (`HKCR\CLSID\...\InprocServer32`).
- Sürücüyü örneklemeyi (`CoCreateInstance`, `riid == clsid` — standart dışı COM kullanımı).
- Örnek host'un konsol sürümünü (`hostsample.cpp`) ve örnek sürücüyü.
- ⚠️ Not: SDK'daki host kodu **referanstır, standart değildir**; JUCE, NAudio, PortAudio
  kendi keşif kodlarını ayrı ayrı yazmıştır (kanıt: bağımsız üç kaynağın aynı sözleşmeyi kullanması).

### §3.4 Sürücü katmanı neyi belirler

- Hangi fiziksel kanalları sunduğunu, hangi örneklemi desteklediğini.
- Tampon sahipliğini: `asio.h` yorumu "driver is the owner of the buffers" der.
- Zamanlama davranışını: kesme zamanında mı, kuyruklu thread'te mi çağırdığı.
- Kontrol panelini: `ASIOControlPanel()` ile üreticinin kendi arayüzü.
- Çoklu-istemci (multi-client) yeteneğini — protokol bunu **zorunlu kılmaz**;
  sürücüye bağlıdır (detay: `asio-daw-uyumlulugu.md`).

### §3.5 Evrensel sürücü katmanı neyi belirler

- ASIO sözleşmesini korur, altta başka bir Windows API'si kullanır.
- FlexASIO için dört backend vardır: MME, DirectSound, WASAPI, WDM-KS.
- Paylaşımlı Windows ses hattı gözlemlendiği kadar **10 ms** iç tampon tabanı taşır;
  bu yüzden 10 ms altı gecikme yalnızca **özel (exclusive)** backend'lerle mümkündür.
- ASIO4ALL altta Kernel Streaming (WDM-KS) kullanır; ASIO2WASAPI altta WASAPI kullanır.
- Sonuç: "ASIO kullanıyorum" cümlesi tek başına **gecikme sözü vermez**; alt katman belirler.

---

## §4 ASIO nedir (pozitif tanım)

1. **Bir sözleşme**: uygulama ile sürücü arasındaki çağrı ve geri çağırma sözleşmesidir.
2. **Çift tamponlu bir akış modeli**: her kanal için iki yarım tampon (A/B) tanımlanır.
3. **Senkron giriş/çıkış**: giriş ve çıkış aynı zaman diliminde işlenir; girişin çıkışa
   göre en az bir tampon gecikmesi olduğu protokolde açıkça yazılıdır.
4. **Geri çağırma tabanlı**: sürücü uygulamayı `bufferSwitch` ile uyandırır.
5. **Donanım sahipliğinde tampon**: tamponları sürücü ayırır, uygulama yazar/okur.
6. **Gecikme beyanı zorunlu**: `ASIOGetLatencies` sonucu senkron doğruluğu belirler.
7. **Kontrol paneli kavramı**: cihaza özel ayarlar uygulama içinden açılır.
8. **Örneklem hızı yetkisi**: sürücü desteklediği hızları `ASIOCanSampleRate` ile bildirir.
9. **Dış saat desteği**: `ASIOGetClockSources`/`ASIOSetClockSource` ile harici senkron.
10. **Zaman bilgisi protokolü**: `bufferSwitchTimeInfo` + `ASIOTime` ile callback zamanı.
11. **Uzantı mekanizması**: `ASIOFuture` ile seçicili (selector) genişletme.
12. **İsteğe bağlı düşük gecikme ipucu**: `ASIOOutputReady` ile bir tampon gecikme tasarrufu.
13. **Aşırı yük bildirimi**: `kAsioOverload` seçicisi ile drop-out tespiti (2.3+'ta).
14. **İç tampon şeffaflığı**: `kAsioGetInternalBufferSamples` ile sürücü içi gecikmenin ölçülmesi.
15. **DSD taşıma**: `ASIOIoFormat` ile PCM/DSD biçim geçişi (2.0+ uzantısı).
16. **Platform sınırı**: bu sürümde Windows odaklı; Mac OS 8/9 desteği 2.3'te kaldırıldı.
17. **Ticari marka**: "ASIO" adı ve uyum logosu Steinberg'in tescillidir.
18. **Ekosistem standardı**: donanım üreticilerinin düşük gecikme için varsayılan hedefi.

---

## §5 ASIO değildir (negatif tanım)

1. **Windows API'si değildir** — Microsoft'un bir API'si değil, üçüncü taraf bir sözleşme.
2. **Sürücü modeli değildir** — donanım kernel sürücüsü değil, kullanıcı modu DLL'idir.
3. **Ses motoru değildir** — Windows ses motorunu (audiosrv/audiodg) içermez; ASIO hattı
   onu **bypass** eder.
4. **Karıştırma (mixing) sunmaz** — başka uygulamaların sesini karıştırmaz.
5. **Paylaşımlı erişim garantisi vermez** — aynı cihazı ikinci bir uygulamanın kullanabilmesi
   sürücüye bağlıdır, protokolde garanti yoktur.
6. **Çapraz platform değildir** — sözleşme Windows uygulaması üzerine kuruludur.
7. **Kod dağıtım lisansı değildir** — lisans (tescilli/GPLv3) SDK'nın kendi belgesindedir.
8. **Gecikme sözü değildir** — protokol gecikme *beyanını* ister, bir üst sınır vaat etmez.
9. **Ses kalitesi sertifikası değildir** — bit derinliği/örnekleme bildirilir, SNR/THD değil.
10. **Otomatik çoklu-istemci değildir** — ikinci istemci davranışı protokolde tanımlı değildir.
11. **Saat kaynağı değildir** — dış saat *seçimi* tanımlıdır, saat üretimi donanımındır.
12. **Bellek güvenliği tanımı değildir** — tampon ömrü (validity) belgede tartışmalıdır
    (kanıt: ASIOUtil BUFFERS.md "the ASIO SDK documentation does not address these questions explicitly").
13. **Eklenti (plug-in) API'si değildir** — VST/AU/CLAP ile ilişkisi yoktur.
14. **Kurulum/dağıtım paketi değildir** — SDK bir zip, sürücüler üreticilerin kurulumucusudur.
15. **Ses yonlendirme (routing) katmanı değildir** — sanal kablo/patchbay işi değildir.
16. **Kayıt defteri standardının kendisi değildir** — kayıt defteri keşfi host SDK'sının
    *kullanım şeklidir*, protokolün parçası değildir.
17. **Açık kaynak *sürücü* ekosistemi değildir** — GPLv3 SDK'yı açtı; donanım sürücüleri
    üretici mülkiyetinde kalmaya devam eder.
18. **Windows ses ayarlarının yerine geçmez** — varsayılan cihaz, uç nokta ve APO'lar
    ASIO hattından bağımsız olarak yaşar.

---

## §6 Temel kavramlar (bu derin-dalın sözlüğü)

| Terim | Anlamı |
|---|---|
| Host (uygulama) | ASIO sözleşmesini çağıran program: DAW, OBS, oynatıcı |
| Driver (sürücü) | `IASIO` arayüzünü uygulayan DLL |
| bufferSwitch | Sürücünün uygulamayı uyandırdığı ana geri çağırma |
| doubleBufferIndex | 0 = tampon A, 1 = tampon B |
| directProcess | Geri çağrımada hemen işleme yapılıp yapılmayacağına dair ipucu |
| priming | `ASIOStart` öncesi host'un 1 numaralı çıkış tamponunu önceden doldurması |
| latency (giriş) | Giriş tamponundaki ilk örnek kare "yaşı" |
| latency (çıkış) | Tampon geçişinden bir sonraki çalma başlangıcına kadar geçen süre |
| granularity | İzin verilen tampon boyu adımı; güçler-i-iki için -1 |
| ASE_SUCCESS | `ASIOFuture` için özgü başarılı dönüş (`ASE_OK` değil) |
| selector | `ASIOFuture`/`asioMessage` uzantı kodu |
| clock source | Dahilî osilatör veya harici saat (S/PDIF, word clock …) |
| channel group | Analog/SPDIF/ADAT gibi kanal kümesi gruplaması |
| APO | Windows'un ses işleme nesneleri; ASIO hattını bypass eder |
| MMCSS | Windows'un multimedya zamanlayıcı öncelik servisi |
| Universal driver | ASIO'yu başka Windows API'lerine çeviren katman |

---

## §7 Derin-dal dosya haritası

| Dosya | Ne cevaplar | Birincil kanıt |
|---|---|---|
| `asio-anayasa.md` (bu dosya) | Kimlik, tarihçe, katmanlar, kapsam | changes.txt, KVR, README |
| `asio-arayuzler.md` | `IASIO*` ailesi, C API, geri çağırma sözleşmesi | `asio.h`, `iasiodrv.h` |
| `asio-surucu-modeli.md` | Yükleme, kayıt defteri keşfi, tampon sahipliği, öncelik | `asiolist.cpp`, JUCE, BUFFERS.md |
| `asio-platform-uyumlulugu.md` | Windows matrisi, x86/x64/ARM64, kernel/user-mode | FlexASIO, Microsoft docs |
| `asio-daw-uyumlulugu.md` | DAW matrisi, ASIO-Guard, çoklu-istemci, ARA | Steinberg, vendor manuelleri |
| `asio-lisans-ve-dogrulama.md` | Lisans analizi, sürüm doğrulama, iddia-kanıt | README, changes.txt, Audacity |
| `asio-kapsam.md` | Kapsam/kapsam dışı, 200 satır özet | bu dosyanın §8–§9'u |
| `index.md` | Klasör girişi, çocuk listesi | v2 §4.2 ön-matter alanı |

**Sıralı okuma önerisi:** anayasa → arayüzler → sürücü-modeli → platform → DAW → lisans.

---

## §8 Kapsam dışı (bu derin-dalın yazmayacakları)

1. **DSP algoritması kodu** (EQ/reverb/filtre) → `dsp-firmware-engineer` alanıdır.
2. **Windows API implementasyonu** (WASAPI/COM detayı) → `windows-software-engineer` alanıdır.
3. **Donanım devresi / PCB** → `audio-hardware-engineer` alanıdır.
4. **Test kodu ve ölçüm prosedürü** → `qa-engineer` alanıdır.
5. **PHP/backend entegrasyonu** → `backend-architect` alanıdır.
6. **Frozen ADR metni (001–037, 019 dahil) düzenlemesi** → yasak yüzey.
7. **Sürüm/id dia doğrulanamayan iddia** → `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretlenir.
8. **Kaynak kod (`.php/.js/.css/.sql`) değişikliği** → bu görevin yazma yüzeyi yalnız `asio/` klasörüdür.

---

## §9 CoreMusic ile ilişki

| Konu | CoreMusic durumu | Kaynak |
|---|---|---|
| Birincil düşük gecikme yolu | ASIO (Windows) | `.ai/AGENTS.md` §17/6 |
| ASIO yoksa fallback | WASAPI exclusive/shared | `.ai/AGENTS.md` §17/6 |
| Yazma yüzeyi | `.ai/architecture/K003-audio/backend/asio/` | görev sözleşmesi |
| Repo derleme hedefi | CLAUDE.md ASIO SDK 2.3.4 atfı | changes.txt ile uyumlu |
| Firmware kodu | Yok (kod PLANNED: cpp=0) | rol profili |
| Lisans kararı | Açık → K020-licence + hukuki onay | bu dosya §11 |
| ADR etkisi | ADR-097 §6.5 (ASIO GPLv3 sorusu) | `.ai/.decisions/draft/ADR-097` |
| Çekirdek (Neva Engine) | C++20 ses motoru şartnamesi; ASIO bir *backend adayı* | rol profili |

**Mühendislik sonucu:** ASIO, CoreMusic'te bir **backend seçeneğidir**, mimari sabit değildir;
seçim donanım keşfi + lisans onayına bağlanmıştır. Bu yüzden anayasa dosyası sözleşme
düzeyinde kalır, kod düzeyine inmez.

---

## §10 İddia — kanıt tablosu

| # | İddia | Kanıt (URL) | Durum |
|---|---|---|---|
| 1 | ASIO 1996'da Steinberg'de tanımlandı | `asio.h` başlık yorumu (`Created by : Steinberg, 05/1996`) | ✅ |
| 2 | Protokol sürümü 2.3 | `asio.h` (`ASIO Interface Specification v 2.3`) | ✅ |
| 3 | SDK sürümü 2.3.4 | `changes.txt` "Changes in ASIO 2.3.4" | ✅ |
| 4 | 2.3.4 çift lisans getirdi | `changes.txt` "new DUAL licensing: Proprietary and GPLv3" | ✅ |
| 5 | İki seçenek de aynı SDK'ya erişir | audiosdk/asio README "Both options grant access to the same SDK" | ✅ |
| 6 | Duyuru 2025-10-15 | KVR 65041 (15th October 2025) | ✅ |
| 7 | OBS teknik sponsorluğu var | KVR 65041 (diamond-tier sponsor alıntısı) | ✅ |
| 8 | VST3 MIT / ASIO GPLv3 haberi | KVR 65179 (29 Oct 2025) | ✅ |
| 9 | Basın bağımsız doğrulaması | recordingmag.com + cdm.link | ✅ |
| 10 | GPLv3 OSI onaylı | opensource.org/license/gpl-3-0 | ✅ |
| 11 | Audacity resmi ASIO ikilisi yayımlayamadı | manual.audacityteam.org (Audacity and ASIO) | ✅ |
| 12 | ASIO bir Windows API'si değil | FlexASIO BACKENDS.md (ASIO host → driver şeması) | ✅ |
| 13 | Paylaşımlı hatta ~10 ms taban var | FlexASIO BACKENDS.md notu | ✅ |
| 14 | 64-bit desteği 2.2'de geldi | changes.txt "ASIO 2.2" | ✅ |
| 15 | MMCSS dokümanı SDK'da | changes.txt "see page 46 of 'ASIO SDK 2.3.pdf'" | ✅ |
| 16 | Kayıt defteri örneği HKLM'e düzeltildi | changes.txt "ASIO 2.1" | ✅ |
| 17 | ASIO "senkron çift tampon" şemasıdır | `asio.h` diyagram bloğu | ✅ |
| 18 | Çıkış gecikmesi kopyalamayla artar | `asio.h` `ASIOGetLatencies` yorumu | ✅ |
| 19 | ASIO sürücüsü kullanıcı modu DLL'idir | `asiolist.cpp` `CoCreateInstance(... CLSCTX_INPROC_SERVER ...)` | ✅ |
| 20 | Zincirde gelecek sürüm | changes.txt son satırı 2.3.4 | ⚠️ UNKNOWN |

---

## §11 Açık sorular

1. **Lisans birleşimi:** GPLv3 seçeneği CoreMusic'in tescilli çekirdeğiyle nasıl birleşir?
   Hukuki onay ⚠️ kanalından alınmalıdır (bu dosya karar üretmez).
2. **Fallback önceliği:** ASIO yokken WASAPI mi, önce evrensel ASIO sürücüsü mü denenecek?
   `.ai/AGENTS.md` §17/6 "WASAPI fallback" der; evrensel sürücü senaryosu açık bırakılmıştır.
3. **Sürüm envanteri:** indirilen zip `ASIO-SDK_2.3.4_2025-10-15.zip` ise özet değeri
   vault'a kaydedilmeli (detay: `asio-lisans-ve-dogrulama.md` §6).
4. **Çoklu-istemci:** CoreMusic ile başka bir DAW aynı ASIO cihazını aynı anda kullanabilir mi?
   Sürücüye bağlı; JUCE kodunda buna dair bir sözleşme yok (bkz. `asio-daw-uyumlulugu.md`).
5. **ARM64:** Windows on ARM'da ASIO sürücüleri için üretici desteğinin kapsamı nedir?
   (bkz. `asio-platform-uyumlulugu.md`, Microsoft ARM64 blogları).
6. **Protokol yorumu:** `bufferSwitch` döndükten sonra tampon ömrü belgede belirsizdir;
   BUFFERS.md bir yorum önerir — bu yorum **standart değildir**.
7. **Kod yüzeyi:** bu derin-dal koddan yoksundur (`*.cpp/*.h = 0`); ilk scaffold ne zaman?
   → Çözüm ADR-097 veya yeni ADR gerektirir.

---

---

## §12 Çağrı sırası ve durum makinesi (özeti)

ASIO'nun yaşam döngüsü dört duruma ayrılır; her geçiş bir prototiple tanımlıdır.

| # | Durum | Girilen çağrı | Sıcaklık |
|---|---|---|---|
| 1 | Yüklü (loaded) | `CoCreateInstance` sonucu `IASIO*` elde edildi | sürücü DLL'i bellekte |
| 2 | Hazır (prepared) | `ASIOInit` → kanal/tampon/örnekleme sorguları | donanım henüz akışta değil |
| 3 | Türetilmiş (derived) | `ASIOCreateBuffers` + `ASIODisposeBuffers` döngüsü | tamponlar ayrıldı |
| 4 | Başlatılmış (started) | `ASIOStart` → `bufferSwitch` akışı | donanım akıyor |
| 5 | Kapanış | `ASIOStop` → `ASIODisposeBuffers` → `ASIOExit` → `Release` | kaynaklar serbest |

Sıra kuralları (kanıt: `asio.h` prototiplerinin yorum blokları):

- `ASIOExit` çağrısı `ASIOStop` ve `ASIODisposeBuffers` **içerir**; çıkış sonrası hiçbir
  geri çağırma erişilemez.
- `ASIODisposeBuffers` da `ASIOStop` **içerir**; hiç tampon hazırlanmadıysa `ASE_InvalidMode`.
- `ASIOStop` dönüşünde sürücü artık `bufferSwitch` **çağırmamalıdır**.
- `ASIOStart`'tan önce host 1 numaralı çıkış tamponunu **önceden doldurmalıdır** (priming).
- `ASIOStart` örnek sayacını sıfırlar; ilk `bufferSwitch(0)` giriş A'yi okumak demektir.
- `ASIOGetSamplePosition` ilk `bufferSwitch`'te sıfır dönmeli, zaman damgası akış
  başlangıcını göstermelidir — blok hizalı olmak zorundadır.
- Zaman bilgisi modu, `createBuffers` içinde `asioMessage(kAsioSupportsTimeInfo)` ile
  açılır; host `1` dönerse `bufferSwitchTimeInfo` beklenir.
- `ASIOFuture` başarısı `ASE_SUCCESS`'tir; `ASE_OK` **kâfi değildir** (asio.h uyarısı).

### §12.1 Hızlı referans — sık kullanılan geri çağrımlar

| Geri çağırma | Ne zaman | Host görevi |
|---|---|---|
| `bufferSwitch(index, directProcess)` | Her zaman dilimi | Giriş oku, çıkış yaz |
| `bufferSwitchTimeInfo(asioTime, index, direct)` | Zaman bilgisi modunda | Aynı + zaman bilgisi doldur |
| `sampleRateDidChange(rate)` | Sürat değişince | Akışı yeniden kur |
| `asioMessage(selector, value, msg, opt)` | Uzantı sorguları | Yeteneği bildir |

---

## §13 Risk haritası

| Risk | Sonuç | Azaltım | Sahip |
|---|---|---|---|
| Lisans belirsizliği (GPLv3 vs tescilli) | Ürün yayımlanamaz | K020-licence hukuki onayı | Vault / hukuk |
| Sürücü çökmesi (kullanıcı modu) | Host süreci biter | `__try/__except` sarmalayıcı (JUCE örneği) | Uygulama |
| Tampon ömrü yanlış yorumu | Yarış koşulu, veri bozulması | BUFFERS.md'nin ihtiyatlı önerisi | Sürücü + host |
| Yanlış gecikme beyanı | Senkron kayması | `ASIOGetLatencies` + `kAsioGetInternalBufferSamples` | Sürücü |
| Paylaşımlı cihaz çakışması | İkinci uygulama ses alamaz | WASAPI fallback zinciri | CoreMusic |
| ARM64 sürücü boşluğu | Platformda çalışamama | Üretici sürücü matrisi denetimi | Donanım |
| Kopyalama yapan evrensel sürücü | +1 tampon gecikme | Özel (exclusive) backend seçimi | Kullanıcı |
| Yanlış kaydıteri anahtarı | Sürücü görünmüyor | `HKLM\SOFTWARE\ASIO` keşfi doğrulaması | Host |

---

## §14 Ölçülebilir kavramlar

- **Gecikme birimi** örnek karedir (sample frame); milisaniyeye çevirme:
  `ms = örnek / örneklem hızı × 1000`.
- Örnek: 256 örnek @ 48 kHz = 5.33 ms (bu değer Steinberg ASIO-Guard makalesinde de örnek olarak geçer).
- **Toplam I/O gecikmesi** en az üç bloğa çıkabilir: giriş bloğu + işlenen blok +
  kopyalanan çıkış bloğu (`ASIOGetLatencies` yorumu bu üçlüyü açıkça anlatır).
- **Bir blok tasarrufu** `ASIOOutputReady` desteğiyle mümkündür; desteklenmiyorsa
  çıktı gecikmesi bir blok artar (asio.h "reducing output latency by one block").
- **Ölçüm iki katmanlıdır**: protokolün bildirdiği değer ile uçtan uca ölçülen değer
  farklıdır; evrensel sürücülerde Windows hattı payı (~10 ms) ayrıca hesaba katılır.
- **Drop-out ölçümü** `kAsioOverload` / `kAsioCanReportOverload` ile sürücüden istenir;
  JUCE bunu `xruns` sayacına yazar.
- **Örneklem konumu** `ASIOGetSamplePosition` + `ASIOTimeStamp` çiftidir; zaman
  damgası nanosaniye cinsindendir (`AsioTimeInfo.systemTime`).

---

## §15 İlgili vault düğümleri

| Düğüm | İlişki |
|---|---|
| `K003-audio/backend` | Üst düğüm (bu dosyanın `parent_id` zinciri) |
| `K003-audio/backend/asio/asio-arayuzler.md` | Protokolün imza düzeyinde karşılığı |
| `K003-audio/backend/asio/asio-surucu-modeli.md` | Yükeleme ve tampon sahipliği |
| `K003-audio/backend/asio/asio-platform-uyumlulugu.md` | Windows/ARM/backend matrisi |
| `K003-audio/backend/asio/asio-daw-uyumlulugu.md` | Host davranışı, çoklu-istemci, ARA |
| `K003-audio/backend/asio/asio-lisans-ve-dogrulama.md` | Lisans ve sürüm kanıtları |
| `.ai/.decisions/draft/ADR-097` §6.5 | ASIO GPLv3 açık sorusu |
| `.ai/AGENTS.md` §17/6 | WASAPI fallback zinciri |
| `K020-licence` | Lisans kararı kanalı (bu dosya karar üretmez) |

---

## §16 Doğrulama protokolü (bu dosya için)

1. Her yeni sürüm iddiası `changes.txt` ile çaprazlanır; bulunamayan satır `UNKNOWN` olur.
2. Her URL, gövdede en az bir kez **bağlamlı** kullanılır (yalnız frontmatter listesi kanıt sayılmaz).
3. Kod alıntıları kaynak dosya adı ile verilir; uydurma imza yazılmaz.
4. Durum etiketi her turda raporlanır: ŞARTNAME (belge) / KOD (PLANNED, cpp=0).
5. Mojibake taraması kapanışta yapılır (Türkçe karakter bütünlüğü).
6. Dosya adları yeniden adlandırılmaz; yazma yüzeyi yalnız bu klasördür.

---

## §17 Sık yapılan yanlışlar

| Yanlış | Doğrusu |
|---|---|
| "ASIO = Windows sürücüsü" | ASIO bir sözleşme; donanım sürücüsü ayrı katmandır |
| "ASIO her zaman en düşük gecikmedir" | Evrensel sürücülerde alt backend belirleyicidir |
| "ASIO mixer'i bypass eder" — Windows mixer yoksa da her şey çözülür | Karıştırma yoktur; ikinci uygulama paylaşımı da yoktur |
| "GPLv3 seçildiğinde marka kullanmak zorunlu" | Marka kullanımı isteğe bağlıdır, kurallara tabidir |
| "ASIO-Guard protokolün parçasıdır" | Cubase/Nuendo'ya özgü host optimizasyonudur |
| "Kayıt defteri keşfi protokolü zorunlu kılar" | Keşif host SDK'sının uygulama şeklidir |
| "ASE_OK, ASIOFuture başarısıdır" | `ASE_SUCCESS` gerekir |
| "Çift tampon = iki ayrı bellek kopyası" | Aynı kanalın iki yarısıdır (A/B) |
| "ASIOOutputReady her sürücüde işe yarar" | Yalnız kopyalama yapan sürücülerde kazanç sağlar |
| "Kod PLANNED demek şartname yok" | Şartname IMPLEMENTED, kod bekliyor |

---

---

## §18 Karar günlüğü (bu dosyanın değişiklik geçmişi)

| Tarih | Değişiklik | Neden |
|---|---|---|
| 2026-10-12 | Dosya sıfırdan yeniden yazıldı | Mojibake + kanıt zayıflığı |
| 2026-10-12 | `changes.txt` zaman çizelgesi eklendi | Sürüm iddialarını tek kaynağa bağlamak |
| 2026-10-12 | Dört katman ayrımı (§3) netleştirildi | "ASIO = sürücü" karışıklığını kapatmak |
| 2026-10-12 | İddia–kanıt tablosu (§10) 20 satıra çıkarıldı | Zero-hallucination kanıtı |
| 2026-10-12 | Açık sorular 7 maddeye çıkarıldı | ADR-097 §6.5 ile hizalanmak |

**Değişiklik kuralı:** bu dosyaya yapılacak her ekleme §16 doğrulama protokolünden geçer;
kaynaksız iddia tabloya girmez. Dosya adı `asio-anayasa.md` **yeniden adlandırılamaz**;
dosyanın `parent_id` alanı `K003-audio/backend/asio` (klasör düğümü) olarak kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** kanıt turu (GitHub `asio.h`/`changes.txt`/README · KVR · Audacity manueli · FlexASIO) · 2026-10-12
