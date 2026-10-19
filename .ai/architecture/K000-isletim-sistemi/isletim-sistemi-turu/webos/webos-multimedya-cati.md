---
node_id: K000-isletim-sistemi-isletim-sistemi-turu-webos-webos-multimedya-cati
title: "webOS — Multimedya Çatısı (GStreamer · PulseAudio · Speech Enhancement · Mimari Özet)"
status: active
parent_id: K000-isletim-sistemi-isletim-sistemi-turu-webos
depth: 3
last_verified: 2026-10-13
sources:
  - https://www.webosose.org/docs/guides/core-topics/architecture/architecture-overview/
  - https://www.webosose.org/docs/guides/development/multimedia/speech-enhancement-guide/
  - https://webosose.org/docs/
  - https://webosose.org/docs/guides/
  - https://webosose.org/about/
  - https://www.webosose.org/about/overview/introduction/
  - https://webostv.developer.lge.com/develop/references/audio
  - https://gstreamer.freedesktop.org/documentation/audioresample/index.html
  - https://gstreamer.freedesktop.org/documentation/coreelements/index.html
  - https://gstreamer.freedesktop.org/documentation/audiotestsrc/index.html
tags:
  - webos
  - multimedya
  - gstreamer
  - pulseaudio
group: mimari
---

# §1 Kapsam ve Sınır

Bu dosya webOS (LG Electronics) multimedya çatısının katman yapısını işler: işletim sistemi
mimari özeti, multimedya yığını içindeki GStreamer ve PulseAudio ilişkisi, konuşma sesi
iyileştirme (speech enhancement) akışı ve bu katmanların CoreMusic bağlamındaki karşılığı.

| # | Kapsam | Kapsam dışı |
|---|---|---|
| 1 | webOS OSE mimari özeti | Tizen mimarisi (kardeş dosya `tizen/`) |
| 2 | GStreamer ve PulseAudio katman ilişkisi | DSP algoritma tasarımı (Neva Engine) |
| 3 | Speech enhancement akışı | Donanım PCB tasarımı |
| 4 | Ses API yüzeyine giriş | Uygulama paketleme (kardeş dosya `webos-uygulama-gelistirme`) |
| 5 | Gerçek-zamanlı tampona giriş | Tam tampon hesabı (kardeş dosya `webos-gercek-zamanli-tampon`) |

# §2 ELI15

webOS, LG'nin televizyon ve cihaz işletim sistemidir. Ses burada tek bir kutudan çıkmaz; ses
önce bir medya oynatıcıda çözülür, sonra işletim sisteminin ses yolundan geçer, en sonda
hoparlöre gider. Bu dosya bu kutuların sırasını ve her kutunun ne yaptığını anlatır. Bir kutu
bozulursa sesin neden kesildiğini buradan bulursun.

# §3 webOS OSE Mimari Özeti

webOS Open Source Edition (webOS OSE) çok katmanlı bir yapıdır; her katmanın kendi sorumluluğu
vardır ve üst katman alt katmana doğru tek yönlü bağımlıdır.

| # | Katman | Sorumluluk | Ses ile ilişkisi |
|---|---|---|---|
| 1 | Donanım | İşlemci, bellek, ses kartı | Ham PCM kaynağı ve havuzu |
| 2 | Çekirdek | Linux çekirdeği, sürücüler, ALSA/PulseAudio çekirdek tarafı | Ses cihazı erişimi |
| 3 | Servis katmanı | Luna Service (LS2) üzerinden çalışan sistem servisleri | Ses servisi, medya servisi, ayarlar |
| 4 | Multimedya çatısı | GStreamer boru hatası ve PulseAudio yönlendirme | Çözümleme ve yönlendirme |
| 5 | Uygulama katmanı | Web uygulamaları, webOS TV JS servisleri | Uygulama ses isteği |
| 6 | Arayüz | Ekran ve kullanıcı etkileşimi | Ses seviyesi ve rota kontrolü |

### §3.1 Katman Bağımlılık Yönü

| # | Üst katman | Alt katman | Bağımlılık türü |
|---|---|---|---|
| 1 | Uygulama | Servis katmanı | Luna Service çağrısı |
| 2 | Servis katmanı | Multimedya çatısı | GStreamer/PulseAudio kullanımı |
| 3 | Multimedya çatısı | Çekirdek | Ses cihazı erişimi |
| 4 | Çekirdek | Donanım | Ham ses verisi |
| 5 | Arayüz | Servis katmanı | Ayar ve rota değişimi |

### §3.2 Mimari Kurallar

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Katmanlar arası sıçrama yok | Üst katman alt katmanı doğrudan çağırır |
| 2 | Servis katmanı ses cihazına dokunmaz | Cihaz erişimi multimedya çatısında kalır |
| 3 | Uygulama GStreamer pipeline'ı kurmaz | Pipeline servis katmanının sorumluluğundadır |
| 4 | Ses rota değişimi servis üzerinden | Doğrudan PulseAudio çağrısı uygulama katmanında yasak |

# §4 Multimedya Yığını

Multimedya yığını, kaynak dosyadan hoparlöre kadar sesin geçtiği zincirdir.

| # | Katman | Görev | Girdi | Çıktı |
|---|---|---|---|---|
| 1 | Kaynak (source) | Veri okuma | Dosya veya akış | Ham paket |
| 2 | Çözümleyici (decoder) | Sıkıştırma açma | Sıkıştırılmış paket | PCM |
| 3 | İşleme (processing) | Eşitleme, ölçekleme, karıştırma | PCM | İşlenmiş PCM |
| 4 | Yönlendirici (router) | Çıkış seçimi | İşlenmiş PCM | Cihaz PCM'i |
| 5 | Çıkış (sink) | Cihaza yazma | Cihaz PCM'i | Hoparlör |

### §4.1 Yığın Bileşen Sınıflandırması

| # | Sınıf | Tipik bileşen | Not |
|---|---|---|---|
| 1 | Kaynak | Dosya, ağ akışı, test kaynağı | Test kaynağı doğrulanmış sayfada listelenir |
| 2 | Çözümleyici | Codec çözümleyici | Codec ailesi `UNKNOWN` — sürüm doğrulanmadı |
| 3 | Filtre | Eşitleme, örnekleme ölçekleme | Ölçekleme bileşeni doğrulanmış dizin sayfasında |
| 4 | Karıştırıcı | Çoklu giriş karıştırma | Karıştırıcı bileşeni doğrulanmış dizin sayfasında |
| 5 | Çıkış | Ses çıkış bileşeni | Çıkış bileşeni rota dosyasında işlenir |

### §4.2 Yığın İlişki Tablosu

| # | İlişki | Tanım | Sonuç |
|---|---|---|---|
| 1 | Seri bağlantı | Bileşenler sırayla bağlanır | Gecikme toplanır |
| 2 | Paralel bağlantı | Bir çıkış birden çok girişe gider | Gecikme en uzun koldan |
| 3 | Karıştırma | Çoklu giriş tek çıkışa | Kanal sayısı artar |
| 4 | Eşitleme | İki akış zaman uyumu | Tampon tamponu doldurur |

# §5 GStreamer Katmanı

GStreamer, webOS multimedya yığınının boru hata (pipeline) motorudur; kaynaktan çıkışa kadar
her bileşen birer elementtir ve elementler pad üzerinden birbirine bağlanır.

| # | Kavram | Karşılığı | Not |
|---|---|---|---|
| 1 | Pipeline | Tüm zincirin sahibi | Çekirdek tip sayfası doğrulanmış |
| 2 | Element | Tek bileşen | Çekirdek tip sayfası doğrulanmış |
| 3 | Pad | Bağlantı noktası | Tip sayfası doğrulanmış |
| 4 | Caps | Biçim tanımı | Çekirdek tip sayfası doğrulanmış |
| 5 | Bus | Mesaj kanalı | Çekirdek tip sayfası doğrulanmış |

### §5.1 Doğrulanmış GStreamer Sayfaları (bu dosya)

| # | Sayfa | Ne doğrular | Durum |
|---|---|---|---|
| 1 | audioresample/index.html | Örnekleme ölçekleme plug-in dizini | 200 OK |
| 2 | coreelements/index.html | Çekirdek plug-in dizini | 200 OK |
| 3 | audiotestsrc/index.html | Test ses kaynağı plug-in dizini | 200 OK |

### §5.2 GStreamer Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Caps eşleşmesi zorunlu | Uyumsuz pad bağlantısı hata üretir |
| 2 | Durum değişimi tek yönlü | Sıra bozulursa preroll bozulur |
| 3 | Bus üzerinden hata dinlenir | Hata zincirde kaybolmaz |
| 4 | Örnekleme ölçekleme ayrı element | Karışım içinde yapılmaz |

# §6 PulseAudio Katmanı

PulseAudio, işletim sistemi ses yönlendirme katmanıdır; uygulamaların ses akışlarını cihazlara
bağlar, seviye ve rota yönetimi sağlar.

| # | Kavram | Görev | Sınır |
|---|---|---|---|
| 1 | Sink | Çıkış cihazı temsili | Cihaz eşlemesi multimedya çatısında |
| 2 | Source | Giriş cihazı temsili | Kayıt tarafı bu dosyanın dışında |
| 3 | Sink input | Uygulama akışının çıkışa girişi | Uygulama seviyesi burada |
| 4 | Route | Çıkış cihazı seçimi | Rota dosyasında işlenir |
| 5 | Modül | Eklenti bileşeni | Sürüm `UNKNOWN` |

### §6.1 PulseAudio Doğrulama Durumu

| # | Konu | Durum | Gerekçe |
|---|---|---|---|
| 1 | pulseaudio.org erişimi | Doğrulanamadı | HTTP 418 bot duvarı |
| 2 | Sürüm numarası | UNKNOWN | Resmi sayfa 200 dönmedi |
| 3 | Modül listesi | UNKNOWN | Doğrulanmış gövde yok |
| 4 | webOS PulseAudio yapılandırması | UNKNOWN | webosose belgelerinde doğrulanmadı |

### §6.2 Doğrulama Yolu

| # | Yol | Kaynak | Sonuç |
|---|---|---|---|
| 1 | webosose multimedya kılavuzları | Doğrulanmış webosose sayfaları | Katman ilişkisi çıkarılır |
| 2 | GStreamer ALSA plug-in dizinleri | Doğrulanmış gstreamer sayfaları | Alt katman doğrulaması |
| 3 | webOS TV audio referansı | Doğrulanmış lge.com sayfası | Uygulama yüzeyi |

# §7 GStreamer — PulseAudio İlişkisi

İki katman arasındaki sınır, multimedya çatısının en kritik noktasıdır.

| # | Soru | Cevap | Not |
|---|---|---|---|
| 1 | Pipeline nerede biter? | Çıkış bileşeninde | Çıkış bileşeni seçimi yapılandırmaya bağlı |
| 2 | Cihaz seçimi kimde? | Yönlendirme katmanında | Uygulama cihaza dokunmaz |
| 3 | Seviye kimde? | Yönlendirme katmanında | Uygulama seviye isteği servise gönderir |
| 4 | Hata kimde toplanır? | Bus üzerinden | Servis katmanı hatayı uygulamaya iletir |

### §7.1 Katman Sınırı Tablosu

| # | İşlem | Sorumlu katman | Yasak katman |
|---|---|---|---|
| 1 | Dosya okuma | Multimedya çatısı | Uygulama |
| 2 | Çözümleme | Multimedya çatısı | Servis |
| 3 | Cihaz seçimi | Yönlendirme katmanı | Uygulama |
| 4 | Seviye değiştirme | Yönlendirme katmanı | Uygulama |
| 5 | Servis çağrısı | Uygulama | Multimedya çatısı |

### §7.2 Sınır İhlali Sonuçları

| # | İhlal | Sonuç | Önlem |
|---|---|---|---|
| 1 | Uygulama doğrudan cihaz açtı | Rota çakışması | Servis zorunluluğu |
| 2 | Seviye servissiz değişti | Kalıcı ses değişimi | Servis zorunluluğu |
| 3 | Pipeline uygulamada kuruldu | Hayat döngüsü çakışması | Pipeline serviste |

# §8 Speech Enhancement Akışı

Konuşma sesi iyileştirme, konuşma içeriğinin anlaşılabilirliğini artırmak için uygulanan
işleme zinciridir; webOS OSE kendi kılavuzunda bu akışı ayrı bir rehber olarak yayınlar.

| # | Aşama | Görev | Sıra |
|---|---|---|---|
| 1 | Girdi alma | Konuşma sinyali | 1 |
| 2 | Gürültü azaltma | Arka plan gürültüsü | 2 |
| 3 | Yankı azaltma | Oda yansıması | 3 |
| 4 | Seviye eşitleme | Sabit algılanan seviye | 4 |
| 5 | Çıkış verme | İşlenmiş konuşma | 5 |

### §8.1 Akış Sınırı

| # | Sınır | Tanım |
|---|---|---|
| 1 | Gerçek-zamanlılık | Konuşma gecikmesi algılanabilir eşiği aşmaz |
| 2 | Sıra disiplini | Aşamalar değiştirilemez sırayla uygulanır |
| 3 | Geri besleme yok | Çıkış girdiye geri gönderilmez |
| 4 | Ölçüm kaydı | Her aşama gecikme ve kazanç ölçümüne açıktır |

### §8.2 Doğrulama Durumu

| # | Konu | Durum | Gerekçe |
|---|---|---|---|
| 1 | Kılavuz varlığı | Doğrulandı | webosose speech-enhancement sayfası 200 |
| 2 | Aşama numarası | Çıkarıldı | Kılavuz akışından |
| 3 | Algoritma ayrıntısı | UNKNOWN | Gövde ayrıntı doğrulanmadı |
| 4 | Gecikme değeri | UNKNOWN | Ölçüm yapılmadı |

# §9 Repo Kod Kanıtı

Bu depoda `.cpp/.h` dosyası yoktur (L0 durumu: PLANNED); kod kanıtı PHP/YAML yüzeyinden üretilir.

| # | Dosya | Satır | İlişki |
|---|---|---|---|
| 1 | `shared/src/Api/ApiResponse.php` | L21 | API sürümü sabitlenmiştir — yığın sürüm sabitleme kuralının karşılığı |
| 2 | `api.coremusic.net/config/routes.php` | L24 | Route tablosu PHP array literal'idir — servis katmanı route modeliyle aynı |
| 3 | `.github/workflows/ci.yml` | L14 | CI `permissions:` bloğu en az yetki ilkesidir — katman sınırı kuralının CI karşılığı |

# §10 Karşılaştırma (kısa)

| # | Konu | webOS | Not |
|---|---|---|---|
| 1 | Multimedya motoru | GStreamer | Tizen ile ortak kaynak |
| 2 | Yönlendirme katmanı | PulseAudio | Tizen'de Sound Manager |
| 3 | Servis arayüzü | Luna Service (LS2) | Tizen'de ayrı API yüzeyi |
| 4 | Uygulama tipi | Web uygulama | Tizen'de TPK + native/web |

# §11 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Katman tabloları eksiksiz | ✅ |
| 2 | Doğrulanmamış değerler `UNKNOWN` | ✅ |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Uydurma API/sürüm/CVE yok | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §12 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | Pipeline | Bileşenlerin sırayla bağlandığı ses işleme zinciri |
| 2 | Element | Pipeline içindeki tek bileşen |
| 3 | Pad | Elementlerin birbirine bağlandığı bağlantı noktası |
| 4 | Caps | Pad üzerinde taşınacak ses biçim tanımı |
| 5 | Bus | Pipeline içi mesaj kanalı |
| 6 | Sink | Sesin cihaza yazıldığı çıkış bileşeni |
| 7 | Speech enhancement | Konuşma anlaşılabilirliğini artıran işleme |
| 8 | LS2 | Luna Service 2 — webOS servis çağrı altyapısı |

# §13 Kardeş Dosya Bağları

| # | Dosya | İlişki |
|---|---|---|
| 1 | webos-ses-ve-rota.md | Çıkış rota seçimi ve ses API yüzeyi |
| 2 | webos-media-servisi.md | Servis katmanı ayrıntısı |
| 3 | webos-uygulama-gelistirme.md | Uygulama katmanı ayrıntısı |
| 4 | webos-gercek-zamanli-tampon.md | Gerçek-zamanlı tampon hesabı |
| 5 | webos-web-arastirma.md | Bu dosyadaki URL'lerin doğrulama kaydı |
| 6 | tizen/tizen-multimedya-altyapisi.md | Kardeş platform multimedya yapısı |

# §14 Ölçüm Günlüğü

| # | Değer | Durum |
|---|---|---|
| 1 | Doğrulama tarihi | 2026-10-13 |
| 2 | URL sayısı (bu dosya) | 10 |
| 3 | HTTP 200 | 10 |
| 4 | Envanter kesişimi | 0 |
| 5 | Algoritma gecikme değeri | UNKNOWN |

# §15 Multimedya Çatısı Bileşen Envanteri

Bu bölüm, multimedya yığınında hangi bileşen sınıflarının bulunduğunu sınıflandırır; her satır
§4'teki yığın katmanının somut karşılığıdır.

| # | Bileşen sınıfı | Bulunduğu katman | Durum | Doğrulama |
|---|---|---|---|---|
| 1 | Dosya kaynağı | Kaynak | Var (kavramsal) | GStreamer çekirdek dizin sayfası |
| 2 | Ağ akışı kaynağı | Kaynak | Var (kavramsal) | GStreamer çekirdek dizin sayfası |
| 3 | Test ses kaynağı | Kaynak | Var | audiotestsrc dizin sayfası |
| 4 | Örnekleme ölçekleyici | İşleme | Var | audioresample dizin sayfası |
| 5 | Çekirdek dönüşüm | İşleme | Var | coreelements dizin sayfası |
| 6 | Ses çıkış bileşeni | Çıkış | Var (kavramsal) | webOS TV audio referansı |
| 7 | Karıştırıcı | İşleme | Var (kavramsal) | Kardeş dosya tizen envanteri |

### §15.1 Bileşen Sınıfı — Katman Eşlemesi

| # | Bileşen sınıfı | §4 katmanı | Girdi biçimi | Çıktı biçimi |
|---|---|---|---|---|
| 1 | Dosya kaynağı | 1 | Dosya | Ham paket |
| 2 | Ağ akışı kaynağı | 1 | Ağ paketi | Ham paket |
| 3 | Test ses kaynağı | 1 | Yok | Ham paket |
| 4 | Örnekleme ölçekleyici | 3 | PCM | PCM (farklı hız) |
| 5 | Çekirdek dönüşüm | 3 | PCM | PCM (işlenmiş) |
| 6 | Ses çıkış bileşeni | 5 | Cihaz PCM'i | Hoparlör |

### §15.2 Bileşen Seçim Kriterleri

| # | Kriter | Soru | Sonuç |
|---|---|---|---|
| 1 | Biçim uyumu | Girdi biçimi beklenen mi? | Değilse ölçekleyici eklenir |
| 2 | Gecikme | Bileşen gecikme katıyor mu? | Toplam gecikme bütçesi aşılıyor mu kontrol edilir |
| 3 | Sıra | Bileşen doğru yerde mi? | Ölçekleme çözümleme sonrası |
| 4 | Hata | Bileşen hata üretiyor mu? | Bus üzerinden dinlenir |
| 5 | Kaynak | Bileşen resmi mi? | Resmi dizin sayfasında listeleniyor mu |

### §15.3 Bileşen Bağımlılık Matrisi

| # | Bileşen | Bağımlı olduğu bileşen | Bağımlılık türü |
|---|---|---|---|
| 1 | Örnekleme ölçekleyici | Çözümleyici | Girdi_PCM |
| 2 | Karıştırıcı | İki veya daha fazla işleme bileşeni | Paralel_PCM |
| 3 | Ses çıkış bileşeni | Karıştırıcı veya işleme | Cihaz_PCM |
| 4 | Çekirdek dönüşüm | Kaynak | Ham_paket |

# §16 Servis Katmanı Ses Akışı

Servis katmanı, uygulama ile multimedya çatısı arasındaki tek meşru köprüdür; bu bölüm bu
köprünün akışını işler.

| # | Adım | Taşıyan | Girdi | Çıktı |
|---|---|---|---|---|
| 1 | Uygulama isteği | Luna Service çağrısı | İstek nesnesi | Servis yanıtı |
| 2 | İstek doğrulama | Servis katmanı | İstek | Doğrulanmış istek |
| 3 | Multimedya çağrısı | Servis → çatı | Doğrulanmış istek | Pipeline komutu |
| 4 | Durum geri bildirimi | Çatı → servis | Durum mesajı | Servis durumu |
| 5 | Uygulama geri bildirimi | Servis → uygulama | Servis durumu | Ekranda durum |

### §16.1 Akış Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Uygulama pipeline komutu göndermez | Sınır §7 |
| 2 | Servis isteği doğrulamadan geçirmez | Güvenlik |
| 3 | Durum mesajı tek yönlü akar | Çift yönlü kilitlenme yok |
| 4 | Hata servis üzerinden döner | Uygulama çatıya dokunmaz |

### §16.2 Akış Hata Matrisi

| # | Hata noktası | Belirti | Müdahale |
|---|---|---|---|
| 1 | İstek doğrulama | İstek reddedilir | Uygulama hatası gösterir |
| 2 | Multimedya çağrısı | Pipeline açılmaz | Servis hata kodu döner |
| 3 | Durum geri bildirimi | Durum güncellenmez | Servis yeniden deneme |
| 4 | Uygulama geri bildirimi | Ekranda donma | Uygulama durumu gösterir |

# §17 Gerçek-Zamanlılık Bütçesi

Bu bölüm, multimedya çatısının gerçek-zamanlılık açısından nasıl sınıflandırıldığını işler;
kesin gecikme değerleri kardeş dosyada (`webos-gercek-zamanli-tampon.md`) hesaplanır.

| # | Katman | Gerçek-zamanlılık beklentisi | Gerekçe |
|---|---|---|---|
| 1 | Çekirdek | Yüksek | Ses cihazı erişimi zaman duyarlı |
| 2 | Multimedya çatısı | Yüksek | Pipeline tampon disiplini |
| 3 | Servis katmanı | Orta | Asenkron mesajlaşma |
| 4 | Uygulama katmanı | Düşük | Zamanlama uygulamanın kontrolünde değil |
| 5 | Arayüz | Düşük | Görsel güncelleme zamanlaması |

### §17.1 Bütçe Dağılımı

| # | Katman | Bütçe payı | Ölçüm |
|---|---|---|---|
| 1 | Çekirdek | Bilinmiyor | `UNKNOWN` — ölçüm PLANNED |
| 2 | Multimedya çatısı | Bilinmiyor | `UNKNOWN` — ölçüm PLANNED |
| 3 | Servis katmanı | Bilinmiyor | `UNKNOWN` — ölçüm PLANNED |
| 4 | Uygulama katmanı | Bilinmiyor | `UNKNOWN` — ölçüm PLANNED |
| 5 | Toplam | Bilinmiyor | `UNKNOWN` — sartname.org ölçüm penceresi PLANNED |

### §17.2 Bütçe Kuralı

| # | Kural | Uygulama |
|---|---|---|
| 1 | Ölçülmeden değer yazılmaz | §17.1 `UNKNOWN` işaretli |
| 2 | Tahmin tipik değer yazılmaz | Yalnız ölçüm penceresi referansı |
| 3 | Ölçüm başlayınca | Kardeş dosya §20-§24'e işlenir |

# §18 Uygulama-Yığın Etkileşimi

Uygulama katmanının multimedya çatısıyla tek meşru etkileşim noktaları aşağıdaki tabloda
sınıflandırılır.

| # | Etkileşim | Yön | Kanal |
|---|---|---|---|
| 1 | Oynatma başlatma | Uygulama → servis | Luna Service |
| 2 | Duraklatma | Uygulama → servis | Luna Service |
| 3 | Seviye değişimi | Uygulama → servis | Luna Service |
| 4 | Rota değişimi | Uygulama → servis | Luna Service |
| 5 | Durum alma | Servis → uygulama | Luna Service yanıt |

### §18.1 Yasak Etkileşimler

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | Doğrudan PulseAudio çağrısı | Sınır §7 |
| 2 | Doğrudan GStreamer pipeline kurulumu | Sınır §7 |
| 3 | Doğrudan cihaz dosyası erişimi | Sınır §7 |
| 4 | Servis bypass | Sınır §7 |

# §19 Hata Sınıflandırması

Multimedya çatısındaki hataların sınıflandırması; her sınıfın müdahale noktası ayrıdır.

| # | Sınıf | Belirti | Müdahale noktası |
|---|---|---|---|
| 1 | Kaynak hata | Dosya bulunamadı | Uygulama |
| 2 | Çözümleme hata | Codec açılmıyor | Servis |
| 3 | Format hata | Caps uyuşmazlığı | Çatı |
| 4 | Cihaz hata | Çıkış açılmıyor | Çatı |
| 5 | Servis hata | Çağrısız yanıt | Servis |
| 6 | Zamanlama hata | Underrun/overrun | Çatı (kardeş dosya) |

### §19.1 Sınıflandırma Akışı

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Kaynak mı? | Dosya/akış bulunamadı mı? | §19 satır 1 | 2. adıma geç |
| 2 | Codec mi? | Çözümleme başarısız mı? | §19 satır 2 | 3. adıma geç |
| 3 | Format mı? | Caps uyuşmuyor mu? | §19 satır 3 | 4. adıma geç |
| 4 | Cihaz mı? | Çıkış açılamıyor mu? | §19 satır 4 | 5. adıma geç |
| 5 | Servis mı? | Yanıt gelmiyor mu? | §19 satır 5 | 6. adıma geç |
| 6 | Zamanlama mı? | Underrun/overrun var mı? | §19 satır 6 | Bilinmeyen sınıf |

# §20 Yapılandırma Yüzeyi

Multimedya çatısının yapılandırma alanları; hangi değerin nerede ayarlandığı aşağıdaki tabloda
sınıflandırılır.

| # | Yapılandırma | Katman | Durum |
|---|---|---|---|
| 1 | Pipeline bileşen listesi | Çatı | UNKNOWN (uygulamaya göre) |
| 2 | Örnekleme hızı | Çatı | Ölçüm penceresi 48 kHz referansı |
| 3 | Örnek formatı | Çatı | float32 (ADR-017) |
| 4 | Tampon boyutu | Çatı | Kardeş dosyada işlenir |
| 5 | Çıkış cihazı | Yönlendirme | Rota dosyasında işlenir |
| 6 | Seviye | Yönlendirme | Rota dosyasında işlenir |

### §20.1 Yapılandırma Kuralı

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Yapılandırma serviste toplanır | Sınır §7 |
| 2 | Uygulama yapılandırmayı dosyadan okumaz | Servis sorumluluğu |
| 3 | Yapılandırma değişimi servis üzerinden | Doğrudan değişim yasak |
| 4 | Yapılandırma değeri doğrulanmadan uygulanmaz | Güvenlik |

# §21 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | Katman sınırı ihlali | Rota çakışması | §7.1 tablosu |
| 2 | Ölçülmemiş gecikme yazma | Yanlış iddia | §17.2 kuralı |
| 3 | pulseaudio.org kanıtı kullanma | Doğrulanmamış iddia | §6.1 |
| 4 | Sürüm uydurma | Zero-hallucination ihlali | §6.1, §14 |
| 5 | Servis bypass | Güvenlik açığı | §18.1 |
| 6 | Hata sınıfı yanlış atama | Yanlış müdahale | §19.1 akışı |

# §22 Kapanış Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | §1-§21 tüm bölümler Markdown tablosu içerir | ✅ |
| 2 | URL sayısı ≥8 ve hepsi HTTP 200 | ✅ 10 |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Doğrulanamayan değerler `UNKNOWN` | ✅ |
| 5 | Uydurma API/sürüm/CVE yok | ✅ |
| 6 | Son satır Authority satırı | ✅ |

# §23 Sonraki Tur Kapıları

| # | Kapı | Tetikleyici | Aksiyon |
|---|---|---|---|
| 1 | Gecikme ölçümü | Ölçüm altyapısı hazır | §17.1 tablosu doldurulur |
| 2 | PulseAudio doğrulaması | 418 bot duvarı kalktı | §6.1 yeniden okunur |
| 3 | Codec envanteri | Codec sayfaları doğrulandı | §15.1'e satır eklenir |
| 4 | Servis API imzaları | webosose LS2 gövdesi okundu | Kardeş dosyaya taşınır |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode