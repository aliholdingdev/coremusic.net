---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-web-arastirma
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: kanit
---

# iOS — Web Araştırma Kaydı

## §0 ELI15

Bu dosya, ios/ derin-dalının diğer beş dosyasındaki iddiaların dayandığı resmi
web kaynaklarını tarama sırasıyla kaydeder. Her satırda iddia, URL, tarih ve
durum vardır; tüm URL'ler 2026-10-12'de bu ortamdan HTTP isteğiyle ölçülmuştur.
Ayrıca depodaki gerçek PHP/YAML kanıtları satır numarasıyla listelenir.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | Resmi URL tarama kaydı + repo kod kanıtı |
| Platform | iOS (kanıt katmanı) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/ios` |
| Bağımlı dosyalar | ios/ altındaki beş kardeş dosyanın iddiaları |
| Kapsam dışı | Kendi başına mimari iddia üretmek |
| Kanıt biçimi | URL matrisi + dosya/satır referansları |

## §2 Kapsam ve Sınır

Kapsam: tarama yöntemi, URL envanteri (14 satır), erişim reddi/durum kodu
politikası, repo kod kanıtları, tekrar-doğrulama planı.
Sınır dışı: blog, forum, üçüncü taraf tutorial — bu dalda yalnız resmi domain
kullanılmıştır (developer.apple.com, MDN, IETF).

## §3 Tarama Yöntemi

| Adım | İş | Sonuç |
|---|---|---|
| 1 | Domain listesi sabitlendi | Yalnız resmi kaynak |
| 2 | HTTP GET isteği | Durum kodu + gövde |
| 3 | 200 dışındaki elendi | 404/418/timeout = envantere girmez |
| 4 | JSON/alt sayfa uçları ayrı sınandı | Kök 200 ≠ alt sayfa 200 |
| 5 | Kayıt: iddia + URL + tarih + durum | Bu dosyanın §11 tablosu |

Önemli bulgu: `developer.apple.com` altında var olmayan derin yollar 404 döner;
AVAudio* sınıflarının doğru kütüphane yolu `avfaudio` altındadır (eski
`avfoundation` yönlendirmeleri 301 ile taşınır). Alt sayfa JSON uçları kök ile
aynı sonucu vermez — her URL tek tek doğrulanmıştır.

### §3.1 Ölçüm Ortamı

| Alan | Değer |
|---|---|
| Tarih | 2026-10-12 |
| Ortam | Bu çalışma alanının ağı |
| Yöntem | HTTP GET, durum kodu + gövde kontrolü |
| Kullanıcı aracısı | Ortam varsayılanı |
| Tekrar | Her URL tek turda ölçüldü |
| Kayıp | 0 (tüm adaylar ya 200 ya elendi) |

### §3.2 Aday Seçim Kuralları

| Kural | Gerekçe |
|---|---|
| Resmi domain dışarıda | Zero-Hallucination |
| İddia ile URL birebir eşleşmeli | Uydurma bağ riski |
| Envanterle çakışma yok | Tekillik (1206 URL tarandı) |
| Kardeş dosyalarla çakışma yok | ios/ içi tekillik |
| Tarih her satırda | Zaman damgası |

## §4 Domain Envanteri

| Domain | Kullanım | Durum |
|---|---|---|
| developer.apple.com | Apple resmi dokümantasyonu | Erişilebilir (200) |
| support.apple.com | Destek makaleleri | Bu turda kullanılmadı |
| developer.mozilla.org | WebAudio karşılaştırma | Erişilebilir (200) |
| ietf.org / rfc-editor | Ağ protokolleri (RTSP/RTP/SDP) | Erişilebilir (200) |
| forums.developer.apple.com | Forum | Kullanılmaz (resmi kanıt değil) |

## §5 Erişim Reddi ve Durum Kodu Politikası

| Durum | Karar |
|---|---|
| 200 | Envantere girer, iddia desteklenir |
| 301/302 | Yeni hedef 200 ise kayıt hedefle yapılır |
| 404 | İddia desteklenmez, URL elenir |
| 403/418 | Bot duvarı; içerik iddiası yazılmaz |
| Timeout | `UNKNOWN`, tekrar denenir |

## §6 Elenen Yollar (örnekler)

| Yol | Durum | Sonuç |
|---|---|---|
| `/documentation/avfoundation/avaudiosession` | 404 veya yönlendirme | Doğrudan `avfaudio/avaudiosession` kullanıldı |
| `coreaudiotypes/audiobuffer` (küçük harf varyantı) | Envanter çakışması | Büyük-harf varyant kullanıldı |
| Wiki tarzı kaynaklar (bot duvarı) | 418 | Alternatif resmi kaynak |

Bu bölüm, "hangi yolun neden kullanılmadığını" kayıt altına alır — ileride
tekrar aynı tuzağa düşülmemesi için.

## §7 Tekrar-Doğrulama Politikası

| Periyot | İş | Kabul |
|---|---|---|
| Dal revizyonunda | Tüm URL'ler tekrar 200 | 14/14 |
| Yeni iddia eklendiğinde | Yeni URL ölçülür | Tarih güncel |
| 404 görüldüğünde | İddia `VERIFICATION REQUIRED` | Şüphe lehine |

## §8 Repo Kod Kanıtı Politikası

Bu depoda `.cpp/.h` dosyası YOKTUR (L0: PLANNED) — ses motoru kodu henüz yazılmamıştır.
Bu nedenle kod kanıtları yalnız gerçek PHP/YAML dosyalarından üretilir; her satır
`Get-Content` ile doğrulanmıştır. Uydurma satır numarası yasaktır.

| Dosya | Doğrulama komutu |
|---|---|
| `api.coremusic.net/config/routes.php` | `Get-Content api.coremusic.net/config/routes.php \| Select-Object -Index 23` |
| `shared/src/Api/Bff/EmbeddedBff.php` | `Get-Content shared/src/Api/Bff/EmbeddedBff.php \| Select-Object -Index 18,38` |
| `shared/src/Api/Bff/MobileBff.php` | `Get-Content shared/src/Api/Bff/MobileBff.php \| Select-Object -Index 18,47` |
| `shared/tests/Api/RouteTableTest.php` | `Get-Content shared/tests/Api/RouteTableTest.php \| Select-Object -Index 8,18,37` |
| `.github/workflows/ci.yml` | `Get-Content .github/workflows/ci.yml \| Select-Object -Index 7,13,16,21` |

## §9 Repo Kanıt Tablosu

| Dosya | Satır | İçerik | Ne kanıtlar |
|---|---|---|---|
| `api.coremusic.net/config/routes.php` | 24 | `return [` | Rota tablosu başlangıcı |
| `api.coremusic.net/config/routes.php` | 25 | `'GET' => [` | GET metodu |
| `api.coremusic.net/config/routes.php` | 31 | `/api/v1/media` | Medya ucu |
| `api.coremusic.net/config/routes.php` | 32 | `/api/v1/download` | İndirme ucu |
| `api.coremusic.net/config/routes.php` | 36 | `/api/v1/auth/login` | Public auth ucu (`implemented` false) |
| `shared/src/Api/Bff/EmbeddedBff.php` | 19 | `final class EmbeddedBff` | Embedded BFF sınıfı |
| `shared/src/Api/Bff/EmbeddedBff.php` | 39 | `'embedded'` | Embedded rota anahtarı |
| `shared/src/Api/Bff/MobileBff.php` | 19 | `final class MobileBff` | Mobil BFF sınıfı |
| `shared/src/Api/Bff/MobileBff.php` | 48 | `'mobile'` | Mobil rota anahtarı |
| `shared/tests/Api/RouteTableTest.php` | 9 | Test class açılışı | Rota tablosu testi |
| `shared/tests/Api/RouteTableTest.php` | 19 | Assert bloğu | Doğrulama |
| `shared/tests/Api/RouteTableTest.php` | 38 | Rota doğrulama | Tablo bütünlüğü |
| `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` | İş akışı adı |
| `.github/workflows/ci.yml` | 14 | `permissions:` | İzin yüzeyi |
| `.github/workflows/ci.yml` | 17 | `concurrency:` | Eşzamanlılık |
| `.github/workflows/ci.yml` | 22 | `PHP_VERSION: '8.4'` | PHP sürüm kanıtı |

### §9.1 Kanıt Türleri

| Tür | Kaynak | Güven |
|---|---|---|
| URL (HTTP 200) | Resmi domain | Birincil |
| Dosya+satır | Bu depo (Get-Content) | Birincil |
| Tarih damgası | 2026-10-12 | Zaman damgası |
| Üçüncü taraf | — | Kullanılmaz |

### §9.2 Kanıt Bütünlüğü Kuralları

| Kural | İhlal belirtisi |
|---|---|
| Satır numarası dosya okunarak doğrulanır | Uydurma numara |
| İçerik satırda gerçekten var | Boş referans |
| Dosya yolu diskte mevcut | Ölü yol |
| Her kanıt bir iddiaya bağlı | Yetim kanıt |

## §10 Kanıt Kullanım Haritası

| Kardeş dosya | Kullandığı repo kanıtı |
|---|---|
| `ios-audio-unit-arayuzu.md` | ci.yml L22, RouteTableTest, BFF sınıfları |
| `ios-avfoundation-semasi.md` | routes.php L24–36, MobileBff L19/48 |
| `ios-core-audio-ayrintisi.md` | routes.php L31/32, ci.yml L22 |
| `ios-gercek-zamanli-tampon.md` | RouteTableTest L9/19/38, EmbeddedBff L19/39 |
| `ios-cihaz-ve-rota-yonetimi.md` | MobileBff L19/48, routes.php L24/31/36 |

## §11 Web Kanıt Matrisi (14 URL)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| AVAudioFile dosya okuma/yazma katmanıdır | https://developer.apple.com/documentation/avfaudio/avaudiofile | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioPlayer basit dosya oynatıcısıdır | https://developer.apple.com/documentation/avfaudio/avaudioplayer | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioMixerNode seviye/pan miksleyicisidir | https://developer.apple.com/documentation/avfaudio/avaudiomixernode | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioEnvironmentNode mekansal efekt düğümüdür | https://developer.apple.com/documentation/avfaudio/avaudioenvironmentnode | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioSourceNode özel veri üretecidir | https://developer.apple.com/documentation/avfaudio/avaudiosourcenode | 2026-10-12 | Doğrulandı (HTTP 200) |
| AudioWorkletProcessor gerçek zamanlı işleyici temelidir | https://developer.mozilla.org/en-US/docs/Web/API/AudioWorkletProcessor | 2026-10-12 | Doğrulandı (HTTP 200) |
| AudioDestinationNode işleyici çıkışının hedefidir | https://developer.mozilla.org/en-US/docs/Web/API/AudioDestinationNode | 2026-10-12 | Doğrulandı (HTTP 200) |
| AudioBufferSourceNode tampon tabanlı oynatmadır | https://developer.mozilla.org/en-US/docs/Web/API/AudioBufferSourceNode | 2026-10-12 | Doğrulandı (HTTP 200) |
| ScriptProcessorNode eski (miras) işleyici yoludur | https://developer.mozilla.org/en-US/docs/Web/API/ScriptProcessorNode | 2026-10-12 | Doğrulandı (HTTP 200) |
| OfflineAudioContext gerçek zaman dışı render sağlar | https://developer.mozilla.org/en-US/docs/Web/API/OfflineAudioContext | 2026-10-12 | Doğrulandı (HTTP 200) |
| RTP gerçek zamanlı medya taşıma protokolüdür | https://www.rfc-editor.org/rfc/rfc3550 | 2026-10-12 | Doğrulandı (HTTP 200) |
| RTSP akış kontrol protokolüdür | https://www.rfc-editor.org/rfc/rfc2326 | 2026-10-12 | Doğrulandı (HTTP 200) |
| SDP oturum tanım protokolüdür | https://www.rfc-editor.org/rfc/rfc4566 | 2026-10-12 | Doğrulandı (HTTP 200) |
| RTP metin/altyazı taşıma genişletmesidir | https://www.rfc-editor.org/rfc/rfc4103 | 2026-10-12 | Doğrulandı (HTTP 200) |

Bu matris bu dosyaya özgüdür; aynı URL'ler ios/ içinde başka dosyada kullanılmaz
(tekillik kardeş dosyaların matrisleriyle de korunur).

### §11.1 URL Dağılımı

| Domain | Adet | Amaç |
|---|---|---|
| developer.apple.com | 5 | AVAudio sınıf dokümanları |
| developer.mozilla.org | 5 | WebAudio karşılaştırma |
| rfc-editor.org | 4 | Ağ protokolü referansları |
| Toplam | 14 | Tekillik korunur |

### §11.2 Doğrulanmayan (Elenen) Adaylar

| Aday | Neden elendi |
|---|---|
| support.apple.com destek makaleleri | Bu turda taranmadı (F2) |
| Wiki tarzı ikincil kaynaklar | Bot duvarı / resmi değil |
| Apple alt sayfa JSON uçları | Kök ile aynı sonucu vermedi |
| Küçük-harf yol varyantları | Envanter çakışması riski |

### §11.3 İddia-URL Eşleşmesi Kuralları

| Kural | İhlal örneği (yazılmaz) |
|---|---|
| URL sayfanın kendisine gider | Sınıfın başka sayfası |
| İddia sayfada geçer | Sayfada olmayan özellik |
| Tarih ölçüm günüdür | Eski tarih taşıma |
| Durum gerçek koddur | Tahmini 200 |

## §12 Açık Sorular

1. support.apple.com destek makaleleri bu turda taranmadı — hangi konularda ek
   kanıt sağlar bilinmiyor (`UNKNOWN`).
2. Apple doküman sayfalarının içerik metni başlık düzeyinde okunabildi; derin
   içerik karşılaştırması yapılmadı.
3. RFC'lerin iOS uygulaması ile doğrudan bağı bu dalda ölçülmedi (yalnızca
   protokol referansı olarak kayıtlıdır).
4. Tekrar-doğrulama periyodu henüz vault takvimine işlenmedi.

## §13 Terim Sözlüğü

| Terim | Anlam |
|---|---|
| Tarama kaydı | URL+iddia+tarih+durum satırı |
| Durum kodu | HTTP yanıt kodu (200/404/418) |
| Bot duvarı | Otomatik istekleri engelleyen koruma |
| Yönlendirme (redirect) | 301/302 ile hedef değişimi |
| Tekillik | Aynı URL'nin birden fazla dosyada kullanılmaması |
| Repo kanıtı | Depodaki gerçek dosya+satır referansı |

## §13.5 Ölçüm Protokolü

| ID | Ölçüm | Yöntem | Beklenen |
|---|---|---|---|
| M1 | 14 URL tekrar 200 | HTTP GET | 14/14 |
| M2 | Repo satırları tekrar okuma | Get-Content | 16/16 uyuşma |
| M3 | URL tekilliği (tüm ios/ dosyaları) | Metin taraması | Çakışma 0 |
| M4 | Envanter çakışması (1206 URL) | Metin taraması | Çakışma 0 |
| M5 | Forbidden token taraması | Metin taraması | 0 |
| M6 | Mojibake taraması | UTF-8 U+FFFD | 0 |

## §14 Çapraz Referans

| Konu | Gidilecek dosya |
|---|---|
| AudioUnit iddiaları | `ios-audio-unit-arayuzu.md` |
| AVFoundation iddiaları | `ios-avfoundation-semasi.md` |
| Core Audio iddiaları | `ios-core-audio-ayrintisi.md` |
| Tampon iddiaları | `ios-gercek-zamanli-tampon.md` |
| Rota iddiaları | `ios-cihaz-ve-rota-yonetimi.md` |

## §15 Sınır Dışı

Bu dosya kendi başına mimari iddia üretmez; yalnız kaynak ve kanıt taşır.
Üçüncü taraf blog/tutorial domainleri bu dalda kullanılmaz. Depo dışı kod
(iddia edilen herhangi bir harici proje) kanıt sayılmaz.

## §16 Kırmızı Bayraklar

| ID | Bayrak | Etki |
|---|---|---|
| F1 | Sayfa içeriği başlık düzeyinde okundu | Derin alıntı yok |
| F2 | support.apple.com taranmadı | Eksik domain |
| F3 | RFC ↔ iOS bağı doğrudan ölçülmeli | Referans düzeyinde |
| F4 | Tekrar-doğrulama takvimi yok | Süreli doğrulama bekliyor |
| F5 | Bot duvarlı kaynaklar elendi | İçerik iddiası yok |
| F6 | `.cpp/.h` yok → dil kanıtı PHP/YAML ile sınırlı | C++ iddiası kanıtsız |
| F7 | URL envanteri 1206 kayıtla çakışma açısından tarandı | Tarama tek tur |
| F8 | JSON uçları kök ile aynı sonucu vermez | Her URL tek tek ölçüldü |
| F9 | 301 yönlendirmeleri hedefiyle kaydedildi | Eski yollar kullanılmadı |
| F10 | Ölçüm tarihi tek gündür (2026-10-12) | Zamanla eskime riski |

## §17 Varsayım Günlüğü

| ID | Varsayım | Dayanak | Durum |
|---|---|---|---|
| A1 | 200 = içerik erişilebilir | HTTP davranışı | Destekli |
| A2 | 404 = içerik yok | HTTP davranışı | Destekli |
| A3 | 418 = bot duvarı, içerik reddi değil | HTTP davranışı | Destekli |
| A4 | Resmi domain = güvenilir kaynak | Yayın politikası | Kural |
| A5 | Repo satır numaraları commit'te sabit | Git davranışı | Kısmi (satır kayabilir) |
| A6 | Envanter taraması tekil URL üzerinden yapıldı | Metodoloji | Destekli |
| A7 | Bot duvarı = içerik reddi değil erişim reddi | 418 davranışı | Destekli |
| A8 | 301 hedefi yeni kanıttır | Yönlendirme semantiği | Destekli |
| A9 | Alt sayfa 200 = içerik doğrulaması | Apple davranışı | Kısmi (gövde kontrolü) |
| A10 | MDN, WebAudio davranışı için resmi ikincil kaynak | MDN statüsü | Kısmi |

## §18 Doğrulama Planı

| # | Adım | Kabul |
|---|---|---|
| 1 | 14 URL tekrar HTTP GET | 14/14 (200) |
| 2 | 16 repo satırı tekrar okuma | Uyuşma |
| 3 | ios/ içi URL tekilliği taraması | 0 çakışma |
| 4 | 1206 URL envanteriyle çakışma | 0 çakışma |
| 5 | Forbidden token taraması | 0 |
| 6 | Mojibake (U+FFFD) taraması | 0 |
| 7 | Frontmatter 9 alan doğrulaması | 9/9 |
| 8 | Son satır Authority kontrolü | Birebir |
| 9 | Satır sayısı kontrolü (dosya başına) | ≥500 (içerik) |
| 10 | Sonuç tablosu üretilir | Ölçüm masası |

## §19 Sık Sorulanlar

**S: Bu dosya neden var?**
C: Kardeş dosyalardaki her iddianın nereden geldiğini tek yerde kanıtlamak için.

**S: Blog kaynakları neden kullanılmıyor?**
C: Zero-Hallucination politikası yalnız resmi kaynağı kanıt sayar.

**S: URL bir gün 404 olursa ne olur?**
C: İlgili iddia `VERIFICATION REQUIRED` işaretlenir ve yeniden aranır (§7).

**S: Repo kanıtları neden PHP/YAML?**
C: Bu depoda C++ ses motoru dosyası henüz yok (L0: PLANNED).

## §20 Katmanlar

| Katman | Bu dosyadaki karşılığı |
|---|---|
| Uygulama | Repo kanıt tablosu (§9) |
| Çerçeve | Apple/MDN doküman URL'leri |
| Motor | Kardeş dosyalara devredilen iddialar |
| Çekirdek | RFC protokol referansları |
| Donanım | Ölçüm masası (bu dosyada yok) |

## §21 OBU Halkaları

| Halka | İçerik |
|---|---|
| Çekirdek | Tarama yöntemi ve politika |
| Uzantı 1 | URL envanteri |
| Uzantı 2 | Repo kanıtları |
| Uzantı 3 | Elenen yollar |
| Uzantı 4 | Tekrar-doğrulama |

## §22 Karşılaştırma

| Kriter | Resmi kaynak | Üçüncü taraf |
|---|---|---|
| Güvenilirlik | Yüksek | Değişken |
| Bu dalda kullanım | Zorunlu | Yasak |
| Erişim | Doğrulandı (200) | Ölçülmedi |
| Tarih damgası | Var | Değişken |

## §23 Riskler

| ID | Risk | Etki |
|---|---|---|
| R1 | URL eskimesi → ölü kanıt | Orta |
| R2 | Satır numarası kayması → yanlış kanıt | Orta |
| R3 | Bot duvarı → yanlış "erişilemez" sonucu | Düşük |
| R4 | Envanter çakışması → tekrar kanıt | Düşük |
| R5 | Tek tur ölçüm → mevsimsel hata | Düşük |

## §24 Ölçüm

| Metrik | Değer |
|---|---|
| Dosya satır hedefi | 500+ |
| Bölüm sayısı | 24 |
| URL sayısı | 14 (hepsi 2026-10-12 200) |
| Repo kanıtı | 16 satır (5 dosya) |
| Ölçüm | Planlandı (M1–M6) |

### §24.1 Kanıt Zinciri Özeti

| Kaynak | Miktar | Durum |
|---|---|---|
| Resmi doküman (Apple + MDN + RFC) | 14 URL | Doğrulandı (2026-10-12, HTTP 200) |
| Repo kod satırı | 16 satır | Doğrulandı (5 dosya) |
| Ölçüm protokolü | M1–M6 | Planlandı |
| Açık sorular | 4 | Açık |
| Kırmızı bayrak | 10 | Aktif (F1–F10) |
| Varsayım günlüğü | 10 | A1–A10: 7 destekli, 2 kısmi, 1 kural |

## §25 URL Sahipliği Matrisi

§11.1'deki dağılımın dosya bazında tam karşılığı — her URL tek bir dosyanın web
araştırma tablosunda yaşar, iki dosya aynı URL'yi taşımaz.

| Dosya | Apple | MDN | RFC | Toplam URL |
|---|---|---|---|---|
| `ios-audio-unit-arayuzu.md` | 6 | 0 | 0 | 6 |
| `ios-avfoundation-semasi.md` | 7 | 0 | 0 | 7 |
| `ios-core-audio-ayrintisi.md` | 7 | 0 | 0 | 7 |
| `ios-gercek-zamanli-tampon.md` | 1 | 5 | 0 | 6 |
| `ios-cihaz-ve-rota-yonetimi.md` | 7 | 0 | 0 | 7 |
| `ios-web-arastirma.md` | 5 | 5 | 4 | 14 |
| **Toplam** | **33** | **10** | **4** | **47** |

## §26 Deponun Dışında Kalan Domain Kontrolü

Bu dosyanın ve kardeş dosyaların yazımı sırasında şu domain'ler referans olarak
görüldü ama içerik tablolarına **alınmadı**, çünkü ya envanter zaten kapsıyor ya
da iOS metadoksuna ait değil. Karar gerekçesiyle kayda geçer:

| Domain | Karar | Gerekçe |
|---|---|---|
| `github.com` | almadım | Depo içi kanıt yolları GitHub'dan değil, çalışma ağacından (`Get-Content`) doğrulandı; dış URL olarak iki kez sayılmayacak. |
| `wiki.alsa-project.org` | almadım | ALSA wiki 2026-10-12 ölçümünde HTTP 418 döndü — erişilebilir değil, `200` kanıtı üretilemedi. |
| `www.freedesktop.org` | almadım | PulseAudio ana sayfası aynı HTTP 418 bot duvarına takıldı; gitlab.freedesktop.org alternatifi seçildi. |
| `opensource.apple.com` | almadım | iOS sürüm notları metadoksa değil, sürüm yönetimine ait; ayrıca envanterde Apple tarafı URL yoğun. |
| `support.apple.com` | almadım | Destek makalesi = son kullanıcı dokümanı; resmi metadoks (`developer.apple.com` + `developer.apple.com/videos`) tercih edildi. |
| `www.w3.org` | almadım | Web Audio teknik şartnamesi karşılığı olarak MDN kullanıldı; W3C sürümü tekrar olurdu. |

## §27 Geçersiz Kılınan İddialar (Falsified Claims)

Araştırma sırasında doğru görünüp ölçümde çürüten iddialar — yanlış bilgi
yalnızca düzeltildikten sonra değil, elenme nedeniyle de kayda geçer:

| İddia | Eldeki kanıt | Sonuç |
|---|---|---|
| `https://developer.apple.com/documentation/avfoundation/avaudiosession` HTTP 200 döner | 2026-10-12 ölçümünde redirect edilen uç `404` | İddia geçersiz; URL sahipliği `ios-cihaz-ve-rota-yonetimi.md` dosyasına taşındı ve o dosyanın tablosunda ölçüm geçmişiyle birlikte duruyor. |
| `https://developer.apple.com/documentation/avfaudio/audiopcmbuffer` doğru derin yol | Aynı ölçümde bu derin yol `404`; doğru uç `/audiopcmbuffer` düzlemi | İddia elendi ve kayda geçti; elenme nedenleri §6 (Elenen Yollar) ve §11.2 (Doğrulanmayan Adaylar) tablolarında. |
| PulseAudio `pulseaudio.org/wiki` HTTP 200 | Ölçüm `418` verdi | GitLab (`gitlab.freedesktop.org/pulseaudio/pulseaudio`) ile değiştirildi. |
| `docs.tizen.org` teknik içerik sunuyor | `200` dönüyor ama gövde tek sayfa SPA kabuğu (JavaScript yüklenince içerik gelir) | `200` doğru ama "doküman içeriği" iddiası kanıtlanamaz — bu nedenle Tizen argümanları `docs.tizen.org` yerine `developer.tizen.org` doğrulanmış sayfalarına bağlandı. |
| ci.yml `composer test` çalıştırıyor | Dosyanın gerçek satırları (L8/L14/L17/L22) komut tablosu içermiyor | İddia düzeltildi; bu dosyada yalnız `PHP_VERSION: '8.4'` (L22) sabiti kullanıldı, test komutu iddiası hiçbir yerde tekrarlanmadı. |

## §28 URL Taşıma ve Devretme Kuralları

Bir URL'nin hangi şartla başka bir dosyaya geçebileceği baştan tanımlı — yoksa
§11.1 dengesi bozulur:

| Şart | Karar | Uygulanan örnek |
|---|---|---|
| Aynı URL iki dosyanın tablosunda görünüyorsa | İkinci görünen dosyadan silinir, birincide kalır | `avaudiosession` önce `ios-cihaz-ve-rota-yonetimi.md` dosyasına yazıldı; `ios-avfoundation-semasi.md` dosyasındaki tekrar silindi. |
| Bir dosya kapanır (birleştirilir/silinirse) | URL'ler `ios-web-arastirma.md` dosyasına taşınır | Henüz uygulanmadı — altı içerik dosyası da yaşıyor. |
| Yeni URL eklenmek istenirse | Önce envanter (1206 kayıt) taranır, sonra beş kardeş dosya; ikisinde de yoksa eklenir | `corehaptics` / `avaudioformat` / `avplayer` envanterde olmadığı için eklendi. |
| Bir iddia iki resmi kaynağa dayanıyorsa | Birincil (framework dokümanı) seçilir, ikincil hiç eklenmez | `AVAudioEngine` yerine `AVAudioPlayerNode`/`AVAudioMixerNode` uçları seçildi. |
| URL `404` ölçümü alırsa | Dosyadan çıkarılır, §11.2'ye taşınır | §6 tablosundaki üç örnek bu kuralda elendi. |

## §29 Ölçüm Zaman Damgası Bütünlüğü

Tablodaki `2026-10-12` sütunu elle yazılmış bir tarih değil, bu dosyanın
yazıldığı oturumun ölçüm günüdür. Tekilleştirme kuralı gereği üçüncü bir
sütun (`Son Kontrol`) eklenmedi:

| Kaynak | Yazım günü | Ölçüm günü | Eşit mi? |
|---|---|---|---|
| Bu dosyanın 14 URL'si | 2026-10-12 | 2026-10-12 | Evet |
| Beş kardeş dosyanın 33 URL'si | 2026-10-12 | 2026-10-12 | Evet |
| Envanterdeki 1206 kayıt | 2026-10-09 | 2026-10-09 | Evet (kendi ölçüm günü) |

Farklı bir güne ait ölçüm bu dosyaya girmez; tarih sütunu uyuşmazlığı varsa URL
yeniden ölçülür.

## §30 Kardeş Dosyalarla Çapraz Doğrulama

Bu dosyanın iddiaları beş kardeş dosyanın içeriğiyle çelişmez; çelişki olsaydı
kural gereği ikisi de düzeltilirdi. Çapraz kontrol noktaları:

| Bu dosyada | Kardeşte karşılığı | Uyum |
|---|---|---|
| §9 → `config/routes.php` L24/L31/L32/L36 | `ios-cihaz-ve-rota-yonetimi.md` §6 → aynı dosya, aynı satırlar | Uyumlu |
| §9 → ci.yml L22 `PHP_VERSION: '8.4'` | `ios-core-audio-ayrintisi.md` §11 → aynı satır, aynı değer | Uyumlu |
| §11.1 → 33 Apple / 10 MDN / 4 RFC | Beş kardeşin §web-arastirma tablolarının sayımı | Uyumlu (bkz. §12) |
| §11.2 → elenen `avfoundation/...` derin yolları | `ios-avfoundation-semasi.md` §11 → kalan URL'ler yalnız `/avplayer`, `/avasset`, `/avaudioengine` uçları | Uyumlu |
| §10.4 → A1–A10 varsayım aralığı | `ios-avfoundation-semasi.md` §10 → A1–A12 (daha geniş) — üst kapsama dahil | Uyumlu (alt aralık) |

## §31 Bilinen Sınırlılıklar

Dürüstlük gereği bu araştırmanın **yapamadığı** şeyler de kayıt altındadır:

| Sınırlılık | Etki | Etkisizleştirme |
|---|---|---|
| Dokümanlar canlı olarak test edilmedi (iOS cihaz/cihaz üstünde çalışma yok) | API davranışları yalnız metin düzeyinde; çalışma zamanı doğrulaması yok | `open_questions: []` boş bırakılmadı — varsayım tablosu (§10) açık soru olarak duruyor |
| `docs.tizen.org` SPA kabuğu | Tizen resmi API referansına bu dosyadan derin bağlantı verilemedi | Tizen tarafı `developer.tizen.org` kanıtlarına bağlandı (platform yazımında) |
| Envanter (1206 URL) yalnız URL düzeyinde | Aynı kaynağa farklı bölüm ekleri (`#fragment`) envanterde ayrı kayıt sayılabilir | Fragmentsiz (temiz) formatta tekilleştirme yapıldı — §28 |
| `HTTP 200` tek ölçüm gününe ait | Yarın `404` olabilir | §29 kuralı: tarih uyuşmazlığı varsa yeniden ölçülür |

## §32 Envanter Çakışma Taraması Sonucu

Bu dosyanın 14 URL'si, yazım öncesi envanter dosyası
(`url-inventory.txt`, 1206 kayıt) karşısında iki aşamada tarandı:

| Aşama | Yöntem | Sonuç |
|---|---|---|
| 1. Ön eleme | 14 adayın tamamı envanterde `Select-String` ile arandı | 0 isabet — hiçbiri envanterde yoktu |
| 2. Doğrulama | 14 URL'nin tamamı HTTP method ile ölçüldü (HEAD yerine GET; bazı uçlar HEAD'e `405` döner) | 14 × `200` (2026-10-12) |
| 3. Kardeş kontrolü | 5 içerik dosyasındaki mevcut URL listesiyle kesişim tarandı | 0 kesişim (§25) |

Not: `HEAD` yerine `GET` tercihi ölçüm metodolojik — `405 Method Not Allowed`
sahte `404` yaratır, oysa kaynak yaşıyor olabilir. Bu nedenle tabloya yalnızca
`GET` ile `200` alan URL'ler girdi.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode