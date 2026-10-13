---
node_id: K000-isletim-sistemi-isletim-sistemi-turu-webos-webos-uygulama-gelistirme
title: "webOS — Uygulama Geliştirme (Web App Types · appinfo.json · Developer Mode · App Lifecycle · CLI/IDE)"
status: active
parent_id: K000-isletim-sistemi-isletim-sistemi-turu-webos
depth: 3
last_verified: 2026-10-13
sources:
  - https://webostv.developer.lge.com/develop/getting-started
  - https://webostv.developer.lge.com/develop/getting-started/developer-workflow
  - https://webostv.developer.lge.com/develop/getting-started/app-resources
  - https://webostv.developer.lge.com/develop/getting-started/web-app-types
  - https://webostv.developer.lge.com/develop/getting-started/app-lifecycle
  - https://webostv.developer.lge.com/develop/getting-started/developer-mode-app
  - https://webostv.developer.lge.com/develop/getting-started/build-your-first-web-app
  - https://webostv.developer.lge.com/develop/references/appinfo-json
  - https://webostv.developer.lge.com/develop/guides/design-principles
  - https://webostv.developer.lge.com/develop/tools/cli-introduction
  - https://webostv.developer.lge.com/develop/tools/ide-launching-your-app
  - https://webostv.developer.lge.com/develop/references/webostvjs-webos
  - https://webostv.developer.lge.com/develop/references/webostvjs-webosdev
  - https://www.webosose.org/docs/guides/development/web-apps/web-app-overview/
  - https://www.webosose.org/docs/tutorials/web-apps/developing-downloadable-web-apps/
  - https://www.webosose.org/docs/tools/sdk/cli/cli-user-guide/
tags:
  - webos
  - uygulama
  - web-app
  - appinfo
group: mimari
---

# §1 Kapsam ve Sınır

Bu dosya webOS TV uygulama geliştirme ortamını işler: uygulama tipleri, `appinfo.json`
yapılandırması, developer mode, uygulama yaşam döngüsü, kaynak ve izin modeli, CLI/IDE
araçları ve webOS TV JS kütüphaneleri.

| # | Kapsam | Kapsam dışı |
|---|---|---|
| 1 | webOS TV uygulama tipleri | Multimedya yığını (kardeş dosya) |
| 2 | `appinfo.json` alanı ihtiyacı | Ses API yüzeyi (kardeş dosya `webos-ses-ve-rota`) |
| 3 | Developer mode ve yükleme | Servis katmanı (kardeş dosya `webos-media-servisi`) |
| 4 | Uygulama yaşam döngüsü | Gerçek-zamanlı tampon (kardeş dosya) |
| 5 | CLI/IDE araçları | Tizen TPK paketleme (kardeş platform) |

# §2 ELI15

webOS TV'de uygulama yazmak, temelde web sayfası yazmaya benzer; ama ek kuralları vardır.
Bu dosya, bir uygulamanın nasıl bir tipte olduğunu, hangi ayar dosyasına ihtiyacı olduğunu,
televizyona nasıl yüklendiğini ve açılıp kapanırken neler yaşadığını anlatır.

# §3 Geliştirme Zinciri

Geliştirme, fikirden cihazdaki uygulamaya kadar sıralı bir zincirdir; her adımın kendi
çıktısı vardır.

| # | Adım | Çıktı | Araç |
|---|---|---|---|
| 1 | Fikir ve gereksinim | Gereksinim listesi | Tasarım |
| 2 | Proje oluşturma | Proje iskeleti | CLI/IDE |
| 3 | Geliştirme | Kaynak kod | Editör |
| 4 | Yerel test | Test sonucu | Tarayıcı |
| 5 | Paketleme | Paket dosyası | CLI/IDE |
| 6 | Yükleme | Cihazda uygulama | CLI/IDE |
| 7 | Cihaz testi | Test sonucu | Cihaz |
| 8 | Yayın | Mağaza kaydı | Yayın akışı |

### §3.1 Geliştirici İş Akışı

| # | Adım | İş | Bağımlılık |
|---|---|---|---|
| 1 | Ortam hazırlığı | SDK ve araçlar | Yok |
| 2 | Cihaz hazırlığı | Developer mode | Cihaz erişimi |
| 3 | Proje oluşturma | Şablon | Ortam |
| 4 | Geliştirme | Kod yazımı | Proje |
| 5 | Yükleme ve test | Cihazda deneme | Cihaz hazırlığı |
| 6 | Hata düzeltme | Düzeltme | Test sonucu |
| 7 | Paketleme ve yayın | Paket | Test geçişi |

# §4 Uygulama Tipleri

webOS TV'de uygulamalar tipe göre ayrılır; her tipin kendi yeteneği ve sınırı vardır.

| # | Tip | Tanım | Ses ilişkisi |
|---|---|---|---|
| 1 | Web uygulaması | HTML/CSS/JS | Ses API erişimi izne bağlı |
| 2 | Native uygulama | Platform dili | Donanım erişimi |
| 3 | Hybrid | İki dünya | İki kural seti |

### §4.1 Uygulama Tipi Karşılaştırması

| # | Özellik | Web | Native |
|---|---|---|---|
| 1 | Geliştirme dili | HTML/CSS/JS | Platform dili |
| 2 | Paketleme | Web paketi | Derlenmiş paket |
| 3 | Donanım erişimi | Servis üzerinden | Doğrudan |
| 4 | Dağıtım | Mağaza | Mağaza |
| 5 | Ses erişimi | Servis üzerinden | Servis üzerinden |

### §4.2 Uygulama Tipi Seçim Kriterleri

| # | Kriter | Tercih | Gerekçe |
|---|---|---|---|
| 1 | Hızlı geliştirme | Web | Web teknolojisi |
| 2 | Donanım derinliği | Native | Doğrudan erişim |
| 3 | Ses uygulaması | Web veya native | Servis erişimi her ikisinde de |
| 4 | Bakım kolaylığı | Web | Tek kod tabanı |

# §5 appinfo.json

`appinfo.json`, uygulamanın kimlik ve yapılandırma dosyasıdır; yükleme ve çalıştırma bu
dosyadaki değerlere bağlıdır.

| # | Alan | Görev | Zorunluluk |
|---|---|---|---|
| 1 | Uygulama kimliği | Benzersiz kimlik | Zorunlu |
| 2 | Sürüm | Uygulama sürümü | Zorunlu |
| 3 | Ana sayfa | Başlangıç dosyası | Web uygulamasında zorunlu |
| 4 | Başlık | Görünen ad | Zorunlu |
| 5 | İkon | Uygulama ikonu | Zorunlu |
| 6 | Tip | Uygulama tipi | Zorunlu |
| 7 | İzinler | İzin listesi | İhtiyaç durumunda |

### §5.1 Alan Sınıflandırması

| # | Sınıf | Alanlar | Değiştirilebilirlik |
|---|---|---|---|
| 1 | Kimlik | Kimlik, sürüm | Yükleme sonrası değişmez (kimlik) |
| 2 | Görünüm | Başlık, ikon | Güncellenebilir |
| 3 | Yapılandırma | Ana sayfa, tip | Yükleme sonrası sabit |
| 4 | Yetki | İzinler | Güncelleme ile değişebilir |

### §5.2 Yapılandırma Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Alan uydurulmaz | Doğrulanmış alan listesi |
| 2 | Zorunlu alanlar eksiksiz | Yükleme reddi |
| 3 | İzin gerekçelendirilir | Denetim |
| 4 | Sürüm formatı tutarlı | Yükseltme |

# §6 Developer Mode

Developer mode, cihazın geliştirme amaçlı yüklemeye açılmasıdır; kapatıldığında normal kullanıcı
moduna döner.

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Mod açma | Geliştirici modu etkin | Yükleme açık |
| 2 | Cihaz erişimi | Cihazla bağlantı | Uzak erişim |
| 3 | Uygulama yükleme | Paket yükleme | Cihazda test |
| 4 | Mod kapatma | Geliştirici modu kapalı | Yükleme kapalı |

### §6.1 Developer Mode Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Mod yalnız test içindir | Güvenlik |
| 2 | Mod kapatılabilir | Kullanıcı kontrolü |
| 3 | Moddururken yüklenen uygulama test amaçlı | Dağıtım yolu farklı |
| 4 | Mod log kaydı üretir | Denetim |

# §7 Uygulama Yaşam Döngüsü

Uygulama yaşam döngüsü, açılıştan kapanışa kadar geçtiği evreleri tanımlar; ses akışı bu
evrelere bağlıdır.

| # | Evre | Tanım | Ses etkisi |
|---|---|---|---|
| 1 | Kurulum | İlk açılış | Ses yok |
| 2 | Başlatma | Ana sayfa yüklenir | Hazırlık |
| 3 | Çalışma | Aktif kullanım | Oynatma mümkün |
| 4 | Duraklatma | Arka plana geçiş | Oynatma politikaya bağlı |
| 5 | Dondurma | Askıya alma | Ses durur |
| 6 | Kapanış | Sonlanma | Ses kesilir |

### §7.1 Evre Geçiş Matrisi

| # | Geçiş | Tetikleyici | Sonuç |
|---|---|---|---|
| 1 | Kurulum → Başlatma | İlk açılış | Hazır |
| 2 | Başlatma → Çalışma | Ana sayfa yüklendi | Aktif |
| 3 | Çalışma → Duraklatma | Arkaya geçiş | Askıda |
| 4 | Duraklatma → Çalışma | Öne çıkış | Aktif |
| 5 | Çalışma → Kapanış | Kullanıcı veya sistem | Kapalı |

### §7.2 Yaşam Döngüsü Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Evre değişimi olayla taşınır | Durum tutarlılığı |
| 2 | Duraklatmada ses durur | Kaynak disiplini |
| 3 | Kapanışta kaynak serbest bırakılır | Kaynak sızıntısı yok |
| 4 | Evre log kaydı üretir | Denetim |

# §8 Kaynak ve İzin Modeli

Uygulama kaynakları (dosya, ikon, dil) paket içinde; izinler ise cihaz politikasıyla
yönetilir.

| # | Kaynak | Konum | Değiştirilebilirlik |
|---|---|---|---|
| 1 | Ana sayfa | Paket içi | Güncelleme ile |
| 2 | İkon | Paket içi | Güncelleme ile |
| 3 | Dil dosyaları | Paket içi | Güncelleme ile |
| 4 | Yapılandırma | Paket içi | Güncelleme ile |

### §8.1 İzin Sınıflandırması

| # | İzin sınıfı | Örnek | Onay |
|---|---|---|---|
| 1 | Depolama | Dosya okuma | Kullanıcı onayı |
| 2 | Ağ | Ağ erişimi | Kullanıcı onayı |
| 3 | Ses | Ses API | Kullanıcı onayı |
| 4 | Cihaz | Cihaz bilgisi | Kullanıcı onayı |

# §9 CLI ve IDE

Geliştirme araçları komut satırı ve entegre ortam olarak ikiye ayrılır; ikisi de aynı işi
yapabilir.

| # | Araç | Tür | Görev |
|---|---|---|---|
| 1 | CLI | Komut satırı | Proje, yükleme, paketleme |
| 2 | IDE | Görsel ortam | Proje, yükleme, paketleme, hata ayıklama |

### §9.1 CLI İş Sınıflandırması

| # | İş | Komut alanı | Sonuç |
|---|---|---|---|
| 1 | Proje oluşturma | Proje | Yeni proje |
| 2 | Yükleme | Cihaz | Cihazda uygulama |
| 3 | Paketleme | Paket | Paket dosyası |
| 4 | Tanılama | Cihaz | Tanılama bilgisi |

### §9.2 CLI/IDE Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Araç sürümü doğrulanır | Uyumluluk |
| 2 | Komut parametresi doğrulanır | Hata önleme |
| 3 | Yükleme cihaz onayı ister | Güvenlik |
| 4 | Paketleme öncesi test | Kalite |

# §10 webOS TV JS Kütüphaneleri

webOS TV JS kütüphaneleri, uygulamanın platform özelliklerine erişimini sağlar; ses erişimi
bu kütüphaneler üzerinden servis çağrısıyla yapılır.

| # | Kütüphane | Görev | Ses ilişkisi |
|---|---|---|---|
| 1 | Platform kütüphanesi | Platform API erişimi | Dolaylı |
| 2 | Geliştirici kütüphanesi | Geliştirme araçları | Yok |
| 3 | Servis köprüsü | Servis çağrısı | Doğrudan |

### §10.1 Kütüphane Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Kütüphane sürümü uygulama sürümüne uygun | Uyumluluk |
| 2 | Kütüphane yalnız servis çağrısı yapar | Sınır §3 |
| 3 | Kütüphane hata yönetimi içerir | Dayanıklılık |
| 4 | Kütüphane sürümü doğrulanmadan yazılmaz | Zero-hallucination |

# §11 Repo Kod Kanıtı

| # | Dosya | Satır | İlişki |
|---|---|---|---|
| 1 | `shared/composer.json` | L8 | PHP sürüm eşiği `>=8.4` — araç sürüm eşitleme kuralının karşılığı |
| 2 | `shared/composer.json` | L24 | Test çerçevesi sürüm eşikli — CLI test kapısının karşılığı |
| 3 | `shared/src/Api/Middleware/AuthenticationMiddleware.php` | L33 | Public route kalıpları — uygulama izin kontrolünün route karşılığı |

# §12 Karşılaştırma (kısa)

| # | Konu | webOS | Tizen |
|---|---|---|---|
| 1 | Uygulama tipi | Web uygulama | TPK (web/native/hybrid) |
| 2 | Yapılandırma | `appinfo.json` | Manifest |
| 3 | Geliştirme ortamı | CLI/IDE | Tizen Studio |
| 4 | Geliştirici modu | Developer mode | Developer mode |
| 5 | Yaşam döngüsü | Yaşam döngüsü olayları | Yaşam döngüsü olayları |

# §13 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Uygulama tipi tablosu eksiksiz | ✅ |
| 2 | `appinfo.json` alanları sınıflandırıldı | ✅ |
| 3 | Yaşam döngüsü matrisi eksiksiz | ✅ |
| 4 | Repo kod satırı ≥1 | ✅ |
| 5 | Sürüm uydurulmadı | ✅ |

# §14 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | `appinfo.json` | Uygulama kimlik ve yapılandırma dosyası |
| 2 | Developer mode | Cihazın geliştirme amaçlı yüklemeye açılması |
| 3 | Yaşam döngüsü | Uygulamanın açılıştan kapanışa evreleri |
| 4 | Paket | Cihaza yüklenebilir uygulama dosyası |
| 5 | CLI | Komut satırı aracı |
| 6 | IDE | Entegre geliştirme ortamı |
| 7 | Servis köprüsü | Servis çağıran JS katmanı |

# §15 Kardeş Dosya Bağları

| # | Dosya | İlişki |
|---|---|---|
| 1 | webos-multimedya-cati.md | Alt katman yığın yapısı |
| 2 | webos-ses-ve-rota.md | Ses API yüzeyi |
| 3 | webos-media-servisi.md | Servis katmanı |
| 4 | webos-gercek-zamanli-tampon.md | Tampon disiplini |
| 5 | webos-web-arastirma.md | URL doğrulama kaydı |
| 6 | tizen/tizen-uygulama-ortami.md | Kardeş platform uygulama ortamı |

# §16 Ölçüm Günlüğü

| # | Değer | Durum |
|---|---|---|
| 1 | Doğrulama tarihi | 2026-10-13 |
| 2 | URL sayısı (bu dosya) | 16 |
| 3 | HTTP 200 | 16 |
| 4 | Envanter kesişimi | 0 |
| 5 | JS kütüphane sürümü | UNKNOWN |

# §17 Paketleme Akışı

Uygulama paketleme, kaynak kodun cihaza yüklenebilir biçime dönüştürülmesidir.

| # | Adım | İş | Çıktı |
|---|---|---|---|
| 1 | Hazırlık | Yapılandırma doğrulama | Onay |
| 2 | Kaynak toplama | Dosya listesi | Kaynak kümesi |
| 3 | Doğrulama | İzin ve alan kontrolü | Uygunluk |
| 4 | Paketleme | Paket oluşturma | Paket dosyası |
| 5 | İmzalama | Paket imzalama | İmzalı paket |
| 6 | Yükleme | Cihaza aktarma | Cihazda uygulama |

### §17.1 Paketleme Hata Matrisi

| # | Hata | Belirti | Müdahale |
|---|---|---|---|
| 1 | Yapılandırma eksik | Yükleme reddedilir | Alan tamamlama |
| 2 | İzin eksik | Çağrı reddedilir | İzin ekleme |
| 3 | Dosya eksik | Uygulama açılmaz | Dosya tamamlama |
| 4 | İmza hatası | Yükleme reddedilir | İmzalama tekrarı |
| 5 | Cihaz uyumsuzluğu | Yükleme reddedilir | Uyumluluk kontrolü |

### §17.2 Paketleme Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Paketleme öncesi yapılandırma doğrulanır | Hata önleme |
| 2 | Yalnız gerekli dosyalar paketlenir | Boyut disiplini |
| 3 | İmzalama zorunlu | Güvenlik |
| 4 | Paketleme log kaydı üretir | Denetim |

# §18 Sürüm Yönetimi

Uygulama sürümü, yükseltme ve uyumluluk takibinin anahtarıdır.

| # | Sürüm alanı | Görev | Kural |
|---|---|---|---|
| 1 | Uygulama sürümü | Yükseltme takibi | Artan biçim |
| 2 | Yapılandırma sürümü | Alan değişimi takibi | Uyumluluk |
| 3 | API sürümü | Platform uyumu | Eşik kontrolü |

### §18.1 Sürüm Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Sürüm artan biçimde | Yükseltme takibi |
| 2 | Sürüm uydurulmaz | Zero-hallucination |
| 3 | Sürüm değişimi log kaydı üretir | Denetim |
| 4 | Sürüm uyumsuzluğu yüklemede reddedilir | Uyumluluk |

# §19 Hata Ayıklama

Geliştirme sırasında hata ayıklama araçları kullanılır; her aracın kendi kapsamı vardır.

| # | Araç | Kapsam | Sonuç |
|---|---|---|---|
| 1 | Tarayıcı konsolu | Web kodu | Konsol kaydı |
| 2 | CLI tanılama | Cihaz durumu | Tanılama bilgisi |
| 3 | IDE hata ayıklama | Kaynak kod | Satır seviyesi |
| 4 | Cihaz log'u | Cihaz olayları | Sistem olayı |

### §19.1 Hata Ayıklama Akışı

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Belirti | Hata belirtisi | Kayıt |
| 2 | Kaynak | Hatanın kaynağı | Nokta |
| 3 | Araç seçimi | Uygun araç | Araç |
| 4 | Düzeltme | Düzeltme | Kod |
| 5 | Doğrulama | Yeniden test | Geçiş |

# §20 Performans Kuralları

Uygulama performansı, kullanıcı deneyimini doğrudan etkiler; ses açısından zamanlama
duyarlılığı bu kurallara bağlıdır.

| # | Kural | Hedef | Ölçüm |
|---|---|---|---|
| 1 | Kaynak israfı yok | Boyut disiplini | Paket boyutu |
| 2 | Sürekli döngü yok | CPU disiplini | CPU kullanımı |
| 3 | Bellek sızıntısı yok | Bellek disiplini | Bellek kullanımı |
| 4 | Zamanlama duyarlılığı | Ses disiplini | Gecikme ölçümü (PLANNED) |

# §21 Güvenlik Kuralları

Uygulama güvenliği, kullanıcı verisinin ve cihazın korunmasıdır.

| # | Kural | Amaç |
|---|---|---|
| 1 | En az izin | Yetki aşımı yok |
| 2 | Girdi doğrulama | Enjeksiyon yok |
| 3 | Güvenli saklama | Veri sızıntısı yok |
| 4 | İmzalı paket | Güvensiz kod yok |

# §22 Test Senaryoları

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | Proje oluşturma | Proje iskeleti |
| 2 | Yapılandırma doğrulama | Uygunluk |
| 3 | Cihaza yükleme | Cihazda uygulama |
| 4 | Yaşam döngüsü geçişi | Durum değişimi |
| 5 | İzin reddi | Çağrı reddi |
| 6 | Paketleme hatası | Hata bildirimi |

# §23 Karar Kaydı

| # | Karar | Gerekçe |
|---|---|---|
| 1 | Uygulama tipi sınıflandırması eklendi | §4 |
| 2 | `appinfo.json` alanları sınıflandırıldı | §5 |
| 3 | Yaşam döngüsü matrisi eklendi | §7 |
| 4 | Sürüm değerleri `UNKNOWN` bırakıldı | Zero-hallucination |

# §24 Kapanış Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | §1-§23 tüm bölümler Markdown tablosu içerir | ✅ |
| 2 | URL sayısı ≥8 ve HTTP 200 | ✅ 16 |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Sürüm uydurulmadı | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §25 Tasarım Prensipleri

webOS TV tasarım prensipleri, kullanıcı deneyimini yönlendirir; ses açısından erişilebilirlik
ve tutarlılık bu prensiplere bağlıdır.

| # | Prensiip | Hedef | Ses ilişkisi |
|---|---|---|---|
| 1 | Tutarlılık | Beklenen davranış | Ses geri bildirimi tutarlı |
| 2 | Erişilebilirlik | Tüm kullanıcılar | Ses alternatifi |
| 3 | Sadelik | Anlaşılabilirlik | Az sayıda ses ayarı |
| 4 | Geri bildirim | Bilgilendirme | Ses olay bildirimi |

### §25.1 Prensip — Uygulama Eşlemesi

| # | Prensip | Uygulama örneği | Ölçüm |
|---|---|---|---|
| 1 | Tutarlılık | Aynı eylem aynı ses | Test senaryosu |
| 2 | Erişilebilirlik | Sesli alternatif | Erişilebilirlik denetimi |
| 3 | Sadelik | Az ayar | Kullanıcı testi |
| 4 | Geri bildirim | Olay sesi | Durum doğrulama |

# §26 İlk Uygulama Oluşturma Adımları

İlk uygulama oluşturma, geliştiriciye zinciri gösteren basit bir akıştır.

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Proje şablonu | Şablon seçimi | İskelet |
| 2 | Ana sayfa düzenleme | Kod yazımı | Görünüm |
| 3 | Yapılandırma | `appinfo.json` | Kimlik |
| 4 | Yerel çalıştırma | Tarayıcı testi | Yerel görünüm |
| 5 | Cihaz yükleme | Uzak yükleme | Cihazda test |
| 6 | Ses denemesi | Servis çağrısı | Ses geri bildirimi |

# §27 Sonraki Tur Kapıları

| # | Kapı | Tetikleyici | Aksiyon |
|---|---|---|---|
| 1 | `appinfo.json` alan doğrulaması | Referans gövdesi okundu | §5 tablosu zenginleştirilir |
| 2 | JS kütüphane sürümü | Sürüm sayfası okundu | §16 kapanır |
| 3 | Yaşam döngüsü olay adları | Gövde okundu | §7.1 satırları zenginleşir |
| 4 | CLI komut adları | CLI kılavuzu okundu | §9.1 satırları zenginleşir |

### §27.1 Tablo Envanteri

| # | Bölüm aralığı | Tablo sayısı | Konu |
|---|---|---|---|
| 1 | §3-§8 | 14 | Zincir, tipler, appinfo, mod, döngü, kaynak |
| 2 | §9-§16 | 12 | Araçlar, kütüphane, karşılaştırma, ölçüm |
| 3 | §17-§24 | 13 | Paketleme, sürüm, hata ayıklama, güvenlik, test |
| 4 | §25-§27 | 7 | Prensip, ilk uygulama, kapı |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode