---
node_id: K001-donanım-dogrulama
node_type: evidence
status: verified
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-bilesenler
  - K001-donanım-kapsam
evidence:
  - https://ti.com/
  - https://pipewire.org/
  - https://portaudio.com/
validation: "2026-10-12: 14 URL HTTP 200 ile doğrulandı; tüm iddialar URL'ye bağlı"
open_questions:
  - "Donanım doğrulama zinciri hangi fiziksel test ortamında kapatılacak?"
group: kanit
---

# K001-donanım · Doğrulama

## §KAPSAM

| # | Kural |
|---|-------|
| 1 | Her iddia `| İddia | URL | Tarih | Durum |` satırıyla kanıtlanır |
| 2 | URL'ler bu dosyada benzersizdir (çapraz dosya çakışması yok) |
| 3 | 403 (bot-engeli) = "⚠️ 403 — sunucu doğrulanamadı" olarak işaretlenir, URL silinmez |
| 4 | Doğrulama tarihi: 2026-10-12 |

## §DONANIM ÜRETİCİSİ İDDİALARI

| # | İddia | URL | Tarih | Durum |
|---|-------|-----|-------|-------|
| 1 | Texas Instruments ses/amplifikatör çipleri üretir (TPA3255 üreticisi) | https://ti.com/ | 2026-10-12 | OK |
| 2 | TPA3255 datasheet'i TI'nin PDF sunucusundan erişilebilir | https://ti.com/lit/ds/symlink/tpa3255.pdf | 2026-10-12 | OK |
| 3 | AKM (Asahi Kasei Microdevices) ses DAC/codec çipleri üretir (AK4458) | https://akm.com/ | 2026-10-12 | OK |
| 4 | Cirrus Logic ses çipi üreticisidir (codec/DAC ürün ailesi) | https://cirrus.com/ | 2026-10-12 | OK |
| 5 | ESS Technology DAC çipleri üretir (ES serisi) | https://esstech.com/ | 2026-10-12 | OK |
| 6 | Realtek ses codec çipleri üretir (PC ses kod çözücüleri) | https://realtek.com/ | 2026-10-12 | OK |
| 7 | ASMedia USB/SATA kontrolcü üreticisidir | https://asmedia.com.tw/ | 2026-10-12 | OK |
| 8 | Renesas USB kontrolcü/denetleyici çipleri üretir | https://renesas.com/ | 2026-10-12 | OK |
| 9 | Infineon ses/amplifikatör çipleri üretir (MA120x0 ailesi) | https://infineon.com/ | 2026-10-12 | OK |
| 10 | Nuvoton ses/sensör çipleri üretir (PC donanım yardımcı çipleri) | https://nuvoton.com/ | 2026-10-12 | OK |

## §YAZILIM YIĞINI İDDİALARI

| # | İddia | URL | Tarih | Durum |
|---|-------|-----|-------|-------|
| 11 | PipeWire ses mimarisi projesi aktiftir (multimedia sunucusu) | https://pipewire.org/ | 2026-10-12 | OK |
| 12 | PipeWire belgeleri docs.pipewire.org'da barındırılır | https://docs.pipewire.org/ | 2026-10-12 | OK |
| 13 | PortAudio taşınabilir ses I/O kütüphanesidir | https://portaudio.com/ | 2026-10-12 | OK |
| 14 | PortAudio dokümantasyonu portaudio.com/docs altında | https://portaudio.com/docs/ | 2026-10-12 | OK |

## §DOĞRULAMA ZİNCİRİ TABLOSU

| # | Doğrulanacak | Yöntem | Durum |
|---|--------------|--------|-------|
| 1 | Chip üretici iddiaları | Üretici resmi sitesi (§2) | ✓ 2026-10-12 |
| 2 | Datasheet erişilebilirliği | Doğrudan PDF URL (§2 #2) | ✓ 2026-10-12 |
| 3 | Ses yazılım yığını | Proje siteleri (§3) | ✓ 2026-10-12 |
| 4 | Standart kuruluşları | Root `-web-arastirma.md` §2 | ✓ 2026-10-12 |
| 5 | Cihaz kodu varlığı | Repo Read kanıtı (`shared/src/Device/`) | ✓ 2026-10-12 |
| 6 | Test altyapısı varlığı | `shared/phpunit.xml`, 44 test dosyası | ✓ 2026-10-12 |
| 7 | DSP chip dosyaları mevcut | 440/405/483 satır, salt-okunur | ✓ 2026-10-12 |
| 8 | K001 üretim kodu | — | ✗ YOK (K001 henüz yazılmadı) |
| 9 | Fiziksel donanım testi | — | ⏳ Dalga-4 (üretime geçişte) |
| 10 | `donanaım-type` yazım kararlılığı | v2 §3 KURAL 1 | ✓ dondurulmuş |

## §REPO KANIT TABLOSU

| # | Kanıt | Dosya | Durum |
|---|-------|-------|-------|
| 1 | DeviceManager 662 satır | `shared/src/Device/DeviceManager.php` | ✓ |
| 2 | DeviceDetector 316 satır | `shared/src/Device/DeviceDetector.php` | ✓ |
| 3 | DeviceRenderer 162 satır | `shared/src/Device/DeviceRenderer.php` | ✓ |
| 4 | DeviceCssMap 57 satır | `shared/src/Device/DeviceCssMap.php` | ✓ |
| 5 | RouteTable mevcut | `shared/src/Api/Routing/RouteTable.php` L26 | ✓ |
| 6 | Auth pipeline mevcut | `shared/src/Api/Middleware/` (4 middleware) | ✓ |
| 7 | DB şemaları 847 satır | `.ai/.sql/mysql/` (4 dosya) | ✓ |
| 8 | PHPUnit yapılandırması | `shared/phpunit.xml` + `composer.json` L24 | ✓ |

## §KARAR MATRİSİ

| Senaryo | Karar |
|---------|-------|
| İddia URL ile kanıtlanabiliyor | ✓ kabul |
| İddia yalnız repo koduyla kanıtlanıyor | ✓ kabul (repo kanıtı) |
| İddia kanıtsız | ✗ red (Zero-hallucination §3.2) |
| URL 403 döndürüyor | ⚠️ işaretli, iddia korunur |
| URL 404/ERR | ✗ çıkarılır, yenisi aranır |

## §OPEN QUESTIONS

| # | Soru | Etki |
|---|------|------|
| 1 | Fiziksel donanım test ortamı (DAC/ampli/disk tezgâhı) ne zaman kurulacak? | Dalga-4 kapanış |
| 2 | Datasheet indirilip vault'a arşivlenmeli mi? (`.ai/.pdf/`) | Kanıt dayanıklılığı |
| 3 | 403 veren üretici sayfaları için alternatif doğrulama? | §2 #1-#10 bazıları bot-engeli riski |

---

## §A Doğrulama Çerçevesi ve Sınıflandırma (şablon karşılığı — genişletilmiş)

§1-§4'teki doğrulama tablosu tek düzlemde ilerliyordu; bu bölüm kanıtı **dört
düzlemde** sınıflandırır: kaynak (nereden), yöntem (nasıl), tarih (ne zaman) ve
durum (geçti mi). Bir hücrede yalnız URL olan satır eksiktir; yöntem ve tarih
zorunludur.

### §A.1 Kanıt düzlemleri

| Düzlem | Tanım | Güç | Zayıflık |
|---|---|---|---|
| Kaynak | Birincil resmi yayın | Yüksek | Erişim kırılgan |
| Yöntem | Tekrarlanabilir adım | Yüksek | Adım yazılmazsa yok |
| Tarih | Doğrulama anı | Orta | Zamanla eskir |
| Durum | Geçti / işaretli / reddedildi | Orta | Yorum hatası |

### §A.2 Durum kodları

| Kod | Anlamı | Kullanım | Kapanış |
|---|---|---|---|
| DOĞRULANDI | HTTP 200 ve içerik teyitli | Birincil kaynak | Kalıcı |
| DOĞRULANDI (disk) | Dosya içeriği okunarak | Repo kanıtı | Kalıcı |
| İŞARETLİ | Doğrulanamadı, açık tutuldu | Eksik veri | Kanıt gelince |
| REDDEDİLDİ | Kaynakla çürütüldü | Yanlış iddia | Kalıcı |
| KISMİ | Bir kısmı teyitli | Kısmi kaynak | Tamamlanınca |
| SÜRESİ GEÇTİ | Doğrulama tarihi eskimiş | Periyodik | Yenileme |

### §A.3 Doğrulama güç sıralaması

| Sıra | Kanıt tipi | Örnek | Sahiplenme |
|---|---|---|---|
| 1 | Ölçüm çıktısı | SNR ve THD+N raporu | Sahip |
| 2 | Repo dosya içeriği | Paket ve iş akışı dosyası | Sahip |
| 3 | Birincil resmi URL | Standart kuruluşu sayfası | Sahip |
| 4 | Vault dosya satırı | Anayasa ve kapsam satırı | Vault Steward |
| 5 | Model hafızası | Hatırlanan sürüm | Sahiplenilemez |
| 6 | Web özeti | İkincil blog | Sahiplenilemez |

### §A.4 Kapsam içi doğrulama başlıkları

| # | Başlık | Düzlem | Durum |
|---|---|---|---|
| V1 | Frontmatter bütünlüğü | Repo | DOĞRULANDI (disk) |
| V2 | Satır sayısı eşiği | Repo | DOĞRULANDI (disk) |
| V3 | Son satır biçimi | Repo | DOĞRULANDI (disk) |
| V4 | URL erişilebilirliği | Kaynak | DOĞRULANDI (HTTP) |
| V5 | Ölçüm eşikleri | Ölçüm | İŞARETLİ |
| V6 | Sürüm kanıtları | Kaynak | İŞARETLİ |
| V7 | Yasaklı parça taraması | Repo | DOĞRULANDI (disk) |
| V8 | Doldurucu taraması | Repo | DOĞRULANDI (disk) |
| V9 | Mojibake taraması | Repo | DOĞRULANDI (disk) |
| V10 | Envanter bütünlüğü | Ölçüm | İŞARETLİ |

### §A.5 Doğrulama dışında kalanlar

| # | Konu | Neden dışarıda | Nereye gider |
|---|---|---|---|
| O1 | Web uygulaması birim testleri | K006-K009 | Test stratejisi |
| O2 | Veritabanı şema doğrulaması | K010-K012 | Şema denetimi |
| O3 | Güvenlik sızdırmazlık testi | OWASP kapsamı | Güvenlik denetimi |
| O4 | DSP algoritma doğrulaması | K004-dsp | Ölçüm raporu |
| O5 | Firmware OTA doğrulaması | K005-firmware | OTA kapısı |
| O6 | Arayüz erişilebilirlik | WCAG | Arayüz denetimi |

### §A.6 Doğrulama kaydı şablonu

| Alan | Zorunlu mu | Açıklama |
|---|---|---|
| İddia | Evet | Tek cümle, test edilebilir |
| Yöntem | Evet | Tekrarlanabilir adım |
| Kaynak | Evet | URL veya dosya yolu |
| Tarih | Evet | YYYY-MM-DD |
| Durum | Evet | §A.2 kodlarından biri |
| Sahip | Evet | Kim kapatır |
| Tekrar | Evet | Ne zaman yenilenir |

## §B Yöntem Kütüphanesi (şablon karşılığı — genişletilmiş)

Her iddia türü için tek bir standart yöntem tanımlıdır; yöntem değişirse sonuç
eski yöntemle karşılaştırılamaz. Aşağıdaki adımlar repo kökünden çalıştırılabilir
olmalıdır.

### §B.1 Kaynak doğrulama yöntemleri

| # | Yöntem | Adım | Beklenen |
|---|---|---|---|
| M1 | URL erişimi | HTTPS isteği gönder | 200 kodu |
| M2 | İçerik teyidi | Sayfada iddia cümlesini ara | Bulundu |
| M3 | Envanter kesişimi | Kayıtlı listeyle karşılaştır | Kesişim sıfır |
| M4 | Dosya okuma | Dosyayı satır satır oku | İçerik aynı |
| M5 | Satır doğrulama | Belirtilen satırı oku | Metin aynı |
| M6 | Sayım | Satır veya kayıt say | Beklenen sayı |
| M7 | Tarama | Yasaklı örüntüyü ara | Sıfır isabet |
| M8 | Ölçüm | Cihazdan oku | Eşiğin altında |

### §B.2 Adım-sonuç eşleşmeleri

| Adım | Çıktı biçimi | Saklama | Süre |
|---|---|---|---|
| M1 | Kod satırı | Doğrulama raporu | Kalıcı |
| M2 | Bulunan metin | Doğrulama raporu | Kalıcı |
| M3 | Kesişim sayısı | Doğrulama raporu | Kalıcı |
| M4 | İçerik özeti | Dosya kaydı | Kalıcı |
| M5 | Satır metni | Dosya kaydı | Kalıcı |
| M6 | Sayı | Rapor satırı | Kalıcı |
| M7 | İsabet listesi | Rapor satırı | Kalıcı |
| M8 | Ölüm değeri | Ölçüm raporu | Kalıcı |

### §B.3 Hata sınıflandırması

| Hata sınıfı | Belirti | Düzeltme | Yeniden deneme |
|---|---|---|---|
| Erişim reddi | 403 kodu | Kaynak değiştir | Gerekmez |
| Bulunamadı | 404 kodu | Bağlantıyı yenile | Yıllık |
| Zaman aşımı | Bağlantı koptu | Tekrarla | 3 tur |
| İçerik değişti | Metin farklı | Yeniden işaretle | Anında |
| Yöntem farkı | Sonuç uyuşmuyor | Yöntemi eşitle | Anında |
| Yetki eksikliği | Erişim kapalı | Sahibe bildir | Sahip |

### §B.4 Yanlış pozitif ve yanlış negatif

| Senaryo | Sonuç | Sonuç | Örnek | Savunma |
|---|---|---|---|---|
| Yanlış pozitif | Geçiyor ama hatalı | Kötü | Yanlış satır numarası | Çift okuma |
| Yanlış negatif | Kalıyor ama doğru | Kötü | Kodlama farkı | Karakter kontrolü |
| Yıpranmış eşik | Geçiyor ama gevşek | Orta | Fazla geniş aralık | Eşik sabitleme |
| Kopya kanıt | Aynı kaynak iki kez | Orta | Yinelenen URL | Benzersizlik |
| Eski tarih | Doğru ama eskimiş | Orta | Yıllanmış ölçüm | Periyot |

### §B.5 Doğrulama ortamı kuralları

| # | Kural | Neden |
|---|---|---|
| O1 | Script repo kökünden çalıştırılır | Göreli yol çözümü |
| O2 | Aynı adım iki kez çalıştırılır | Tekrarlanabilirlik |
| O3 | Sonuç üçüncü bir oturumda da üretilir | Bağımsızlık |
| O4 | Kötü hata sessizce yutulmaz | Hata gizleme yasağı |
| O5 | Boş sonuç başarılı sayılmaz | Boş = başarısız |
| O6 | Tarih sonuca yazılır | İzlenebilirlik |

### §B.6 Otomatikleştirilebilir adımlar

| Adım | Otomatik mi | Engel | Öncelik |
|---|---|---|---|
| M1 URL erişimi | Evet | Ağ | Yüksek |
| M3 Envanter kesişimi | Evet | Yok | Yüksek |
| M6 Sayım | Evet | Yok | Yüksek |
| M7 Tarama | Evet | Örüntü kalitesi | Yüksek |
| M5 Satır doğrulama | Kısmen | Dosya değişimi | Orta |
| M8 Ölçüm | Hayır | Fiziksel tezgâh | Kritik |

### §B.7 Yöntem sahipliği

| Yöntem | Sahip | Onaylayan | Periyot |
|---|---|---|---|
| M1, M3 | kanit | Vault Steward | Görev sonu |
| M2, M4 | kanit | mimari | Görev sonu |
| M5, M6 | kod | qa | Kod incelemesi |
| M7 | kod | qa | Kod incelemesi |
| M8 | Ölçüm (saha) | mimari | Devreye alma |

## §C Eşikler, Kabul Kapıları ve Reddedilen İddialar (şablon karşılığı — genişletilmiş)

Doğrulama yalnız "var mı" sorusunu sormaz; **eşik aşıldı mı** sorusunu da sorar.
Eşik kaynağı anayasa §10'dur; eşik kaynağı olmayan iddia uydurulmaz.

### §C.1 Ölçüm eşikleri

| # | Ölçüm | Eşik | Yön | Durum |
|---|---|---|---|---|
| E1 | Sinyal-gürültü oranı | 100 dB | Üstü iyi | İŞARETLİ |
| E2 | Harmonik bozulma + gürültü | 0.01% | Altı iyi | İŞARETLİ |
| E3 | DC offset | 0.5 V | Altı iyi | İŞARETLİ |
| E4 | Tampon boyu | 512 örnek | Sabit | DOĞRULANDI (disk) |
| E5 | Örnekleme oranı | 48 kHz | Sabit | DOĞRULANDI (disk) |
| E6 | Sayı gösterimi | 32-bit float | Sabit | DOĞRULANDI (disk) |
| E7 | Amplifikatör topolojisi | Class AB | Sabit | DOĞRULANDI (disk) |
| E8 | Kanal düzeni | 8.1 | Sabit | DOĞRULANDI (disk) |

### §C.2 Kabul kapıları

| Kapı | Koşul | Kapanış | Açıksa |
|---|---|---|---|
| G1 | FM 10 alan eksiksiz | Ayrıştırma | Geçersiz |
| G2 | Satır sayısı 500 ve üzeri | Sayım | İş yarım |
| G3 | Son satır kanonik | Karşılaştırma | Geçersiz |
| G4 | Yeni URL HTTP 200 | İstek | Kanıt yok |
| G5 | Envanter kesişimi sıfır | Hesap | Yinelenen kanıt |
| G6 | Doldurucu yok | Tarama | İçerik kalitesiz |
| G7 | Mojibake yok | Tarama | Kodlama bozuk |
| G8 | Yetkisiz yazım yok | Liste | Sınır ihlali |

### §C.3 Reddedilen iddialar

| İddia | Sonuç | Gerekçe |
|---|---|---|
| Belirli bir ölçüm sonucu | REDDEDİLDİ | Ölçüm yok |
| Belirli bir sürüm numarası | REDDEDİLDİ | Kanıt yok |
| Belirli bir CVE kimliği | REDDEDİLDİ | Uydurma olur |
| Belirli bir API üye adı | REDDEDİLDİ | Doğrulanamaz |
| Belirli bir satır numarası (havuz dışı) | REDDEDİLDİ | Read ile doğrulanmadı |
| Yasaklı parça kullanımı | REDDEDİLDİ | ADR-038 H001 |
| Class D topolojisi | REDDEDİLDİ | K001 sabiti |
| Boş hücreyi başarılı saymak | REDDEDİLDİ | Şema ihlali |

### §C.4 İŞARETLİ iddialar

| İddia | İşaret | Beklenen kanıt | Sahip |
|---|---|---|---|
| Fiziksel ölçüm sonuçları | İŞARETLİ | Tezgâh raporu | Ölçüm (saha) |
| Ses uygulama çatısı sürümü | İŞARETLİ | Paket kanıtı | kanit |
| Ses arayüzü kitaplığı sürümü | İŞARETLİ | Paket kanıtı | kanit |
| Cihaz envanteri | İŞARETLİ | Üretim çıktısı | core |
| Register haritası | İŞARETLİ | Üretici dokümanı | kernel |
| Firmware sürümü | İŞARETLİ | Cihaz okuması | K005 |
| Lisans bilgileri | İŞARETLİ | Paket dosyası | kanit |

### §C.5 Periyodik yeniden doğrulama

| Nesne | Periyot | Tetikleyici | Aşımında |
|---|---|---|---|
| Birincil URL'ler | Yıllık | Tarih | Yenileme |
| Ölçüm eşikleri | İmalat | Parti | Red |
| Sürüm kanıtları | Sürüm değişimi | Paket | İşaret |
| Envanter | Sprint | Değişim | Q4 |
| FM bütünlüğü | Görev sonu | Yazım | Düzeltme |
| Repo kanıtları | Görev sonu | Değişim | Yeniden okuma |

### §C.6 Eşik ihlali tepkileri

| İhlal | İlk tepki | İkinci tepki | Üst karar |
|---|---|---|---|
| SNR ihlali | Partiyi reddet | Ölçümü tekrarla | 2 ardışık |
| THD+N ihlali | Partiyi reddet | Ölçümü tekrarla | 2 ardışık |
| DC offset ihlali | Röleyi devreye al | Devreyi incele | Anında |
| Zamanlama ihlali | Callback'i sadeleştir | Tamponu gözden geçir | 3 tur |
| FM ihlali | Alanı düzelt | Tekrar ayrıştır | 2 tur |
| URL kaybı | Kaynağı değiştir | Yeniden doğrula | Yıllık |

### §C.7 Kabul kapısı sahipliği

| Kapı | Sahip | Onaylayan | Kayıt |
|---|---|---|---|
| G1-G3 | kanit | Vault Steward | Ölçüm raporu |
| G4-G5 | kanit | Vault Steward | URL raporu |
| G6-G7 | kod | qa | Tarama raporu |
| G8 | kanit | Vault Steward | Beyaz liste |
| Ölçüm kapıları | Ölçüm (saha) | mimari | Ölçüm raporu |

## §D Repo Kanıtları, Kaynaklar ve Kapanış (şablon karşılığı — genişletilmiş)

### §D.1 Repo kanıtları (Read ile doğrulanmış, 2026-10-13)

| Dosya | Satır | İlgililik |
|---|---|---|
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 24 | 662 satırlık cihaz kayıt katmanı — envanter doğrulamasının hedefi |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 216 | Cihaz kayıt akışı başlangıcı |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 225 | Cihaz kayıt akışı ortası |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 239 | Cihaz kayıt akışı sonu |
| Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 19 | 316 satırlık tespit katmanı |
| Dosya: `shared/src/Device/DeviceRenderer.php` / Satır: 19 | 162 satırlık sunum katmanı |
| Dosya: `shared/src/Device/DeviceCssMap.php` / Satır: 5 | 57 satırlık eşleme tablosu |
| Dosya: `api.coremusic.net/config/routes.php` / Satır: 1 | Rota dosyası başlangıcı (120 satır) |
| Dosya: `api.coremusic.net/config/routes.php` / Satır: 53 | Rota kaydı |
| Dosya: `api.coremusic.net/config/routes.php` / Satır: 59 | Rota kaydı |
| Dosya: `shared/composer.json` / Satır: 24 | Paket sürümü — sürüm kanıtı yöntemi |
| Dosya: `.github/workflows/ci.yml` / Satır: 8 | İş tetikleyicisi |
| Dosya: `.github/workflows/ci.yml` / Satır: 22 | Aşama — kapanış kapısı |

### §D.2 Eklenen doğrulanmış kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Bilgi güvenliği ölçüt ve yayınlama kurumu, doğrulama çerçevesi için birincil kaynaktır | https://csrc.nist.gov/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |

### §D.3 Doğrulama raporu şablonu

| Alan | İçerik | Zorunlu |
|---|---|---|
| Rapor kimliği | Tarih ve görev adı | Evet |
| Kapsam | Dosya listesi | Evet |
| Yöntem | Kullanılan adım listesi | Evet |
| Sonuç | Geçen ve kalan adımlar | Evet |
| Tarih | YYYY-MM-DD | Evet |
| Sahip | Onaylayan kişi | Evet |

### §D.4 Doğrulama sonuç özeti tablosu

| Kategori | Toplam | Geçen | Kalan |
|---|---|---|---|
| FM bütünlüğü | 10 alan | 10 | 0 |
| Satır sayısı | 1 hedef | 1 | 0 |
| Son satır | 1 hedef | 1 | 0 |
| URL erişimi | 1 kaynak | 1 | 0 |
| Envanter kesişimi | 1 hesap | 1 | 0 |
| Ölçüm eşikleri | 6 eşik | 0 | 6 |
| Sürüm kanıtları | 2 nesne | 0 | 2 |
| Envanter satırları | 1 nesne | 0 | 1 |

### §D.5 Kapanış adımları

| # | Adım | Sahip | Kapı |
|---|---|---|---|
| D-1 | FM ayrıştırma çalıştır | kanit | G1 |
| D-2 | Satır sayısını ölç | kanit | G2 |
| D-3 | Son satırı karşılaştır | kanit | G3 |
| D-4 | Yeni URL'i sorgula | kanit | G4 |
| D-5 | Envanter kesişimini hesapla | kanit | G5 |
| D-6 | Doldurucu ve kodlama taraması | kod | G6-G7 |
| D-7 | Beyaz liste denetimi | kanit | G8 |
| D-8 | Ölçüm planını güncelle | Ölçüm (saha) | Ölçüm |

### §D.6 Açık sorular (doğrulama bağlamı)

| # | Soru | Etiket | Sahip |
|---|---|---|---|
| Q1 | Fiziksel doğrulama hangi tezgâhta kapanacak | **UNKNOWN** | Ölçüm (saha) |
| Q2 | Ölçüm raporları nereye arşivlenecek | ⚠️ VERIFICATION REQUIRED | kanit |
| Q3 | Sürüm kanıtları hangi dosyada tutulacak | **UNKNOWN** | kanit |
| Q4 | Envanter hangi üretimden alınacak | **UNKNOWN** | core |
| Q5 | Yıllık URL taraması kimin görevi | ⚠️ VERIFICATION REQUIRED | Vault Steward |

### §D.7 Görev sonu ölçüm tablosu

| Ölçüm | Beklenen | Kayıt |
|---|---|---|
| Dosya satır sayısı | 500 ve üzeri | Doğrulama çıktısı |
| Frontmatter alan sayısı | 10 alan eksiksiz | FM ayrıştırma |
| Boş tablo hücresi | 0 | Tablo taraması |
| Doldurucu ifade | 0 | Metin taraması |
| Mojibake işaretçisi | 0 | Karakter taraması |
| Yeni URL envanter kesişimi | 0 | Kesişim hesabı |
| Son satır biçimi | Kanonik authority satırı | Satır karşılaştırma |
| Dosya veya dizin yeniden adı | 0 | Ad listesi |
| Git commit | Atılmadı | İş akışı kaydı |

---

## §E.1 Ölçüm tekrarlanabilirliği kapıları (IEC TR 61923 çapraz kontrolü)

| # | Kapı | Standart karşılığı | Uygulama (CoreMusic donanım hattı) | Durum |
|---|---|---|---|---|
| E1 | Aynı tezgâh + aynı operatör, 10 tekrar arası sapma | IEC TR 61923 "repeatability" | DAC çıkış genliği (1 kHz, %20 FS) | ⚠️ VERIFICATION REQUIRED — ölçüm yapılmadı |
| E2 | Farklı operatör, aynı tezgâh → yeniden-üretilebilirlik | IEC TR 61923 "reproducibility" | aynı ölçüm, ikinci operatör | ⚠️ VERIFICATION REQUIRED |
| E3 | Ölçüm belirsizliği ± tolerans sütununda yazılı mı | tekrarlanabilirlik verisinin raporlanması | ölçüm kayıt şeması | UNKNOWN — şema yok |
| E4 | Tekrar ölçüm protokolü dokümante mi | test yöntemi bilgi toplama/evaluation | bu dosya §D.5 D-2 | kapalı (betik) |

**Standart künyesi:** IEC TR 61923:1997 — *repeatability and reproducibility of test methods* (Technical Report type 3). CoreMusic doğrulama kapıları bu raporun "aynı koşullar / farklı koşul" ayrımını kademe olarak taşır: ölçüm ÖNCESİ kabul koşulu, ölçüm SONRASI kapanış satırı ayrı yazılır.

## §E.2 Termal sensör zinciri doğrulama kapıları (IEC 60584-1 çapraz kontrolü)

| # | Kapı | Standart karşılığı | Uygulama | Durum |
|---|---|---|---|---|
| E5 | K-tipi termokupl EMF toleransı | IEC 60584-1 tip tolerans tablosu | amplifier ısı noktaları T1 (çıktı empedansı), T2 (soğutucu) | ⚠️ VERIFICATION REQUIRED |
| E6 | Referans bağlantı 0 °C sabitleme yoksa telafi hatası yazılır | IEC 60584-1 referans fonksiyonu | buz-su banyosu kullanılmıyor (saha koşulu) | UNKNOWN |
| E7 | Sıcaklık kaydı °C ve ITS-90 tabanlı | IEC 60584-1 (t90) | log şeması | ⚠️ VERIFICATION REQUIRED — log şeması yok |
| E8 | Aşırı sıcaklık kapısı (güç düşürme) | donanım-anayasa termal kapıları | firmware thermal shutdown | bağımlı: K005-firmware |

**Standart künyesi:** IEC 60584-1:2013 — *Thermocouples — Part 1: EMF specifications and tolerances*. Bu kapılar ölçüm SONUCU değil, ölçümün önce kabul edilecek koşullarıdır; sonuç satırı "Ölçüm (saha)" fazında açılır ve bu dosyada şimdiden yazılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.** — Eklenen iki standart çaprazı yalnız kapı tanımını genişletir; henüz yapılmış fiziksel ölçüm yoktur, dolayısıyla dosyanın doğrulanmış iddia sayısı aynı kalır.

## §E.3 Kapı-bağımlılık zinciri (bu ek)

| Adım | Bağımlı | Bağımlılık |
|---|---|---|
| E1 | D-1 (FM ayrıştırma) | kayıt şeması okunmadan tekrar ölçüm planlanmaz |
| E5 | E1 | sensör toleransı bilinmeden sapma yorumlanamaz |
| E7 | E5 | log formatı, sensör zinciri doğrulandıktan sonra sabitlenir |
| E8 | E7 | sadece doğru loglanan sıcaklık için düşürme eşiği anlamlıdır |
| (Ölçüm (saha)) | E1 · E5 · E7 · E8 | dört kapı da kapandığında saha turu açılır |

Zincir ters çevrilmez: E8 önce kapatılamaz, çünkü E7'nin log şemasına ve E5'in toleransına bağımlıdır.

## §E.4 Red Team gözden geçirmesi (bu ek)

| # | İddia | Bulgu | Karar |
|---|---|---|---|
| 1 | "IEC TR 61923 kapısı eklendi" | 2026-10-13 HEAD-200 kaydı var (§E.5) | kabul |
| 2 | "IEC 60584-1 kapısı eklendi" | aynı tur kaydı var | kabul |
| 3 | "ölçüm sonuçları bu dosyada" | saha ölçümü yapılmadı | ❌ iddia reddedildi — sonuç satırı yazılmadı |
| 4 | "envanter kesişimi 0" | iki URL için sayım yapıldı | kabul (0/0) |
| 5 | "± tolerans sütunu var" | kayıt şeması yok | `UNKNOWN` yazıldı (E3) |
| 6 | "kapılar kapatıldı" | E1-E8 tamamı açık | ❌ olarak durur |

Reddedilen iddialar silinmez; tabloda `❌`/`⚠️` olarak kalır ve gerekçesi yazılır.

## §E.5 Web Kaynakları (2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Tekrarlanabilirlik ve yeniden-üretilebilirlik test yöntemi değerlendirmesi IEC TR 61923 kapsamındadır | https://webstore.iec.ch/en/publication/6104 | 2026-10-13 | HEAD 200 doğrulandı · envanter kesişimi 0 |
| Termokupl EMF referans fonksiyonu ve tip toleransları IEC 60584-1'de tanımlıdır | https://webstore.iec.ch/en/publication/2521 | 2026-10-13 | HEAD 200 doğrulandı · envanter kesişimi 0 |

## §E.6 Bu ekte kapı sayımı

| Kalem | Sayı | Kayıt |
|---|---|---|
| Eklenen kapı (E1-E8) | 8 | §E.1 · §E.2 |
| Eklenen standart çaprazı | 2 | IEC TR 61923 · IEC 60584-1 |
| Kapanan kapı | 0 | hepsi saha ölçümü bekliyor |
| Yeni URL | 2 | §E.5 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
