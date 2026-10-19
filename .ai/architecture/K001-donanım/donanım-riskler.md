---
node_id: K001-donanım-riskler
node_type: risk
status: draft
parent_id: K001-donanım
children: []
depends_on:
  - K001-donanım-anayasa
  - K001-donanım-kapsam
evidence:
  - https://via.com/
  - https://dell.com/
validation: "2026-10-12: riskler taslak; sahiplik ve azaltma planı kayıtlı"
open_questions:
  - "Üretici riski sahibi donanım üretiminde belli olacak"
group: kanit
---

# K001-donanım · Riskler

## §KAPSAM

| # | Kural |
|---|-------|
| 1 | Her risk: olasılık · etki · azaltma · sahip |
| 2 | Üretici URL'leri 4 adet, bu dosyada benzersiz |
| 3 | Riskler üretime geçişte yeniden puanlanır |

## §RİSK TABLOSU

| # | Risk | Olasılık | Etki | Azaltma | Sahip |
|---|------|----------|------|---------|-------|
| R-01 | `donanaım-type` yazımı sonradan "düzeltme" girişimi | orta | yüksek (489+ wiki-link, 7 alt dosya yolu) | v2 §3 KURAL 1; yazım dondurulmuş, düzeltme yasak | Vault Steward |
| R-02 | kernel↔core sınırı tanımsız, iki katman çakışır | orta | orta | Sınır tanımı dalga-2 çıktısı (open question) | Architect |
| R-03 | Standart URL'leri zamanla 404 olur (kanıt kaybı) | yüksek | düşük | 2026-10-12 anlık doğrulama; arşiv kararı open question | Vault Steward |
| R-04 | PCI SIG/JEDEC 403 bot-engeli — kanıt erişimi kırılgan | yüksek | düşük | ⚠️ işaretli iddialar korunur; datasheet arşivi | Research |
| R-05 | `audio/cipler` dalga-4 gecikirse K001 ses bileşeni yarım kalır | orta | orta | Dalga-3'te plan satırları hazır (bilesenler.md §4) | Dalga-4 |
| R-06 | Fiziksel donanım testi kurulmaz — doğrulama zinciri açık kalır | orta | yüksek | §dogrulama açık sorusu; tezgâh planı | Test |
| R-07 | K002 ID GAP: bağımlılık grafiğinde boşluk | düşük | orta | ADR-099-010 açık; vault-sync takibi | Architect |

## §ÜRETİCİ BAĞIMLILIĞI RİSKLERİ

| # | Üretici | Risk | URL | Tarih | Durum |
|---|---------|------|-----|-------|-------|
| 1 | VIA | Eski chipset sürücü desteği daralabilir | https://via.com/ | 2026-10-12 | OK |
| 2 | Dell | OEM BIOS/sürücü paketi değişimi (cihaz profili kırılabilir) | https://dell.com/ | 2026-10-12 | OK |
| 3 | HP | OEM donanım varyasyonu (aynı model farklı chip) | https://hp.com/ | 2026-10-12 | OK |
| 4 | Lenovo | OEM firmware güncellemesi ile register haritası kayması | https://lenovo.com/ | 2026-10-12 | OK |

## §AZALTMA PLANI

| Risk | Aksiyon | Termin |
|------|---------|--------|
| R-01 | `donanaım-type` yazımı her vault-sync'te kontrol edilir | sürekli |
| R-02 | Sınır tablosu `donanım-bagimliliklar.md` §5'te | dalga-2 |
| R-03 | Yıllık URL yeniden doğrulama | yıllık |
| R-04 | Datasheet PDF'leri `.ai/.pdf/` arşivine (open question #2) | dalga-4 |
| R-05 | `audio-bilesenler.md` §5 dalga-4 iş kalemleri | dalga-4 |
| R-06 | Tezgâh donanım listesi (DAC+ampli+disk) hazırlanır | dalga-4 |
| R-07 | ADR-099-010 kapanınca K000/K002 bağımlılığı düzeltilir | vault-sync |

## §İZLEME

| Gösterge | Sıklık | Eşik |
|----------|--------|------|
| URL sağlığı (24+14+4+6) | yıllık | 404 oranı %10 üstü → yenileme |
| `donanaım-type` yazım bütünlüğü | her vault-sync | 1 sapma → düzeltme yasağı ihlali |
| Dalga-4 ilerlemesi | sprint | 2 sprint gecikme → eskalasyon |
| Açık soru sayısı | sprint | 3 üstü → üst karar |

## §OPEN QUESTIONS

| # | Soru |
|---|------|
| 1 | Üretici riski puanlaması dalga-4'te kimin? |
| 2 | OEM varyasyonları için cihaz profili varyantı mı, ayrı profil mi? |

---

## §A Risk Çerçevesi ve Sınıflandırma (şablon karşılığı — genişletilmiş)

Bu bölüm, yukarıdaki §RİSK TABLOSU'nun (R-01…R-07) metodolojik uzantısıdır. Mevcut
yedi satır silinmez; burada yalnızca **sınıflandırma, ölçek ve uzatılmış kayıt
defteri** eklenir. Kural: kanıtsız satır yazılmaz — doğrulanamayan iddia
`⚠️ VERIFICATION REQUIRED` ile işaretlenir ve kapatılamaz sayılır.

### §A.1 Etki ve olasılık ölçeği (sabit)

| Değer | Etki tanımı | Olasılık tanımı |
|---|---|---|
| 1 | Kabul kriterine etkisi yok; yalnız kayıt notu | Yalnız teorik; sahada hiç görülmemiş |
| 2 | Tek bir K001 dosyasını etkiler; yerel düzeltme yeterli | Nadir; koşula bağlı |
| 3 | Bir alt katmanı (type / api / core / kernel) etkiler | Ara sıra; bilinen koşulda |
| 4 | K001 genelinde kabul kriterini düşürür | Sık; mevcut kanıtla destekleniyor |
| 5 | Güvenlik veya ölçüm bütünlüğü ihlali; ADR ihlali | Neredeyse kesin; kanıtlanmış |

### §A.2 Köken sınıflandırması

| Köken | Tanım | Geçerli araç | Geçersiz araç |
|---|---|---|---|
| Tasarım | Mimari karar hatası | ADR + kod incelemesi | Performans ayarı |
| Uygulama | Kod hatası | Statik analiz + test | Yeni ADR |
| Kanıt | Doğrulama eksikliği | Ölçüm + kaynak teyidi | Kod değişikliği |
| Şema | Vault alan tutarsızlığı | Doğrulama scripti | Mimari revizyon |
| Donanım | Fiziksel sınır (ısıl, gürültü, eşik) | Devre + ölçüm | Yazılım ayarı |
| Tedarik | Dış üretici bağımlılığı | Çoklu kaynak | İç refaktör |
| Süreç | İş akışı ihlali | Kapı + rapor | Teknik düzeltme |
| Kodlama | Karakter kümesi bozulması | UTF-8 taraması | İçerik yeniden yazımı |

### §A.3 Yüzey sınıflandırması (K001 alt bileşenleri)

| Yüzey | Dosya grubu | Bağlı risk bantı | Yoğunluk |
|---|---|---|---|
| Anayasa ve ilkeler | donanım-anayasa.md | Orta | Düşük |
| Tür ayrımı | donanaım-type/* | Orta | Orta |
| Erişim arayüzü | donanım-api/* | Yüksek | Orta |
| Merkez toplama | donanım-core/* | Yüksek | Orta |
| En yakın katman | donanım-kernel/* | Yüksek | Orta |
| Ses bileşenleri | audio/cipler/* | Orta | Yüksek |
| Ölçüm ve kanıt | *-dogrulama.md | Yüksek | Yüksek |
| Test ve kod | test-stratejisi, kod-ornekleri | Orta | Orta |
| Bağımlılık | bagimliliklar, web-arastirma | Orta | Orta |
| Sözlük ve karar | evrensel-dil, kararlar | Düşük | Düşük |

### §A.4 Yaşam döngüsü aşaması

| Aşama | Tipik bulgu | Erken tespit | Kapanış kanıtı |
|---|---|---|---|
| Tasarım | Yanlış bileşen/topoloji seçimi | ADR denetimi | Onaylı ADR kaydı |
| Uygulama | Real-time kural ihlali | Kod incelemesi | İnceleme kaydı |
| Ölçüm | Eşik karşılanmıyor | İmalat testi | Ölçüm raporu |
| Bakım | Sürüm drift'i, envanter boşluğu | Periyodik doğrulama | Doğrulama çıktısı |
| Devreye alma | Kaynak kilidi çakışması | Eşzamanlılık testi | Kilitleme logu |

### §A.5 Bu bölümün kapsamı ve kapsam dışıları

| # | Öğe | Durum | Dayanak |
|---|---|---|---|
| A1 | Yazılım ve vault kaynaklı riskler | KAPSAM İÇİ | Bu dosyanın konusu |
| A2 | Donanım fiziksel riskleri (ısıl, eşik, gürültü) | KAPSAM İÇİ | K001 kural 10 ve ölçüm kapıları |
| A3 | Üretici/tedarik riskleri | KAPSAM İÇİ | Üretici bağımlılığı tablosu |
| A4 | İşletim sistemi sürücü riskleri | KAPSAM DIŞI | K000-isletim-sistemi |
| A5 | DSP algoritma riskleri | KAPSAM DIŞI | K004-dsp |
| A6 | Ses motoru/zamanlama riskleri | KAPSAM DIŞI | K003-audio |
| A7 | Firmware güncelleme riskleri | KAPSAM DIŞI | K005-firmware |
| A8 | Web uygulama güvenlik riskleri | KAPSAM DIŞI | OWASP kapsamı, K006-K009 |

### §A.6 Risk satırı şablonu (zorunlu alanlar)

| Alan | Zorunlu mu | Açıklama |
|---|---|---|
| Kimlik | Evet | R-xx biçiminde benzersiz |
| Risk başlığı | Evet | Tek cümle; sonuç odaklı |
| Köken | Evet | §A.2 tablosundaki bir değer |
| Yüzey | Evet | §A.3 tablosundaki bir değer |
| Etki ve olasılık | Evet | §A.1 ölçeğinden 1-5 |
| Azaltma | Evet | Önleme veya tepki; yalnız tespit kabul edilmez |
| Sahip | Evet | mimari / kod / kanit / Ölçüm (saha) |
| Kanıt | Evet | Dosya yolu, URL veya ölçüm çıktısı |

### §A.7 Öncelik bantları

| Bant | Aralık | Davranış | Ziyaret sıklığı |
|---|---|---|---|
| Kritik | 25 | Üst karar; iş durur | Anlık |
| Yüksek | 16-24 | Sahipli aksiyon; sprint'e girer | Sprint başına |
| Orta | 9-15 | İzlenir; kapıya bağlanır | Sprint başına |
| Düşük | 1-8 | Yerel düzeltme; birikirse toplu ele alınır | Dönemsel |

### §A.8 Saha gözlem kayıt tablosu

| # | Gözlem | Sıklık | Eşik | Kayıt yeri |
|---|---|---|---|---|
| O-1 | DC offset ölçümü | Devreye alma | 0.5 V üzeri → röle | Ölçüm raporu |
| O-2 | SNR ölçümü | İmalat | 100 dB altı → red | Ölçüm raporu |
| O-3 | THD+N ölçümü | İmalat | 0.01% üzeri → red | Ölçüm raporu |
| O-4 | Callback süresi | Çalışma zamanı | 512 örnek bütçesini aşan kare | Zamanlama logu |
| O-5 | Kaynak kilidi çakışması | Açılış | İkinci sahip tespiti | Sistem logu |
| O-6 | URL erişilebilirliği | Yıllık | HTTP 404/410 | Doğrulama çıktısı |
| O-7 | Envanter bütünlüğü | Sprint | Eksik satır sayısı | Envanter raporu |
| O-8 | Vault alan bütünlüğü | Görev sonu | Alan başına 1 sapma | Doğrulama çıktısı |

## §B Uzatılmış Risk Kayıt Defteri (şablon karşılığı — genişletilmiş)

§RİSK TABLOSU'ndaki R-01…R-07 aynen korunur; bu bölüm **R-08'den başlayan**
uzatılmış defterdir. Numaralar çakışmaz, iki tablo birleştirilmez.

### §B.1 Uzatılmış kayıt defteri (R-08 … R-31)

| # | Risk | Köken | Yüzey | Etki | Ol. | Önc. | Sahip |
|---|---|---|---|---|---|---|---|
| R-08 | Real-time callback içinde tahsis yapılması | Uygulama | kernel | 5 | 3 | 15 | kod |
| R-09 | Real-time thread üzerinde mutex kullanılması | Uygulama | kernel | 5 | 3 | 15 | kod |
| R-10 | Audio callback imzasında noexcept eksikliği | Uygulama | kernel | 4 | 3 | 12 | kod |
| R-11 | ASIO Exclusive Lock ikinci sahip tarafından kırılması | Tasarım | api | 5 | 3 | 15 | mimari |
| R-12 | DC offset eşiği aşıldığında koruma rölesi devrede değil | Donanım | core | 5 | 3 | 15 | Ölçüm (saha) |
| R-13 | SNR 100 dB altında kalıyor | Donanım | type | 4 | 3 | 12 | Ölçüm (saha) |
| R-14 | THD+N 0.01% üzerinde çıkıyor | Donanım | type | 4 | 3 | 12 | Ölçüm (saha) |
| R-15 | Yasaklı PCM5122 kullanımı kod/BOM'a sızmış | Tasarım | type | 5 | 2 | 10 | mimari |
| R-16 | Amplifikatör topolojisi Class AB dışına kaymış | Tasarım | type | 5 | 2 | 10 | mimari |
| R-17 | Sayı gösterimi 32-bit float dışında üretiliyor | Tasarım | api | 4 | 2 | 8 | mimari |
| R-18 | Buffer 512 örnek / 48 kHz yapılandırması sapmış | Uygulama | core | 3 | 3 | 9 | kod |
| R-19 | JUCE 9 sürüm kanıtı üretilemiyor | Kanıt | bağımlılık | 3 | 4 | 12 | kanit |
| R-20 | ASIO SDK 2.3.4 sürüm kanıtı üretilemiyor | Kanıt | bağımlılık | 3 | 4 | 12 | kanit |
| R-21 | Neva Engine kaynağı diskte yok (PLANNED) | Kanıt | kernel | 4 | 5 | 20 | mimari |
| R-22 | Bağımlılık bump kapısı atlanarak değişiklik giriyor | Süreç | bağımlılık | 4 | 3 | 12 | kanit |
| R-23 | Firmware OTA kapısı atlanarak değişiklik giriyor | Süreç | bağımlılık | 4 | 3 | 12 | kanit |
| R-24 | Refactor el yazımı ile yapılıp handover kapısı atlanıyor | Süreç | bağımlılık | 4 | 3 | 12 | kanit |
| R-25 | Yasaklı dizinlere yazım (K005, K010, K011, webos, tizen) | Süreç | tümü | 4 | 2 | 8 | kanit |
| R-26 | Kaynak kod yüzeylerine yazım (*.php, *.js, *.css, *.sql) | Süreç | tümü | 5 | 2 | 10 | kanit |
| R-27 | Uydurma API, sürüm, CVE veya satır numarası kullanımı | Kanıt | tümü | 5 | 3 | 15 | kanit |
| R-28 | index.md dosyalarının 40-100 satır sınırı dışına çıkması | Şema | indeks | 2 | 3 | 6 | mimari |
| R-29 | Boş tablo hücresi veya doldurucu metin girişi | Kanıt | tümü | 3 | 3 | 9 | kod |
| R-30 | Mojibake işaretçilerinin dosyalara bulaşması | Kodlama | tümü | 3 | 2 | 6 | kod |
| R-31 | Yeni URL'in envantere girmeyen doğrulanmamış kaynak olması | Kanıt | bağımlılık | 4 | 3 | 12 | kanit |

### §B.2 Uzatılmış defterin kategori dağılımı

| Kategori | Adet | En yüksek öncelik | Birincil sahip |
|---|---|---|---|
| Uygulama (kod) | 3 | 15 | kod |
| Tasarım (ADR) | 4 | 15 | mimari |
| Donanım / ölçüm | 4 | 15 | Ölçüm (saha) |
| Kanıt / zero-hallucination | 5 | 20 | kanit |
| Süreç / kapı | 5 | 12 | kanit |
| Şema / indeks | 2 | 9 | mimari |
| Kodlama | 1 | 6 | kod |
| Bağımlılık / sürüm | 3 | 12 | kanit |

### §B.3 Tetikleyici tablosu

| # | Tetikleyici olay | Tetiklenen riskler | İlk aksiyon |
|---|---|---|---|
| T-1 | Kod incelemesi açıldığında | R-08, R-09, R-10 | Real-time taraması |
| T-2 | Cihaz açılışı yapıldığında | R-11, R-05 | Kilidi doğrula |
| T-3 | Tezgâh ölçümü yapıldığında | R-12, R-13, R-14 | Eşik karşılaştırması |
| T-4 | BOM satırı eklendiğinde | R-15, R-16 | Yasaklı parça taraması |
| T-5 | DMA/SIMD kodu yazıldığında | R-17 | Sayı gösterimi denetimi |
| T-6 | Yapılandırma dosyası değiştiğinde | R-18 | Buffer ve örnek oranı doğrulama |
| T-7 | Bağımlılık güncellendiğinde | R-19, R-20, R-22 | Sürüm kanıtı toplama |
| T-8 | Firmware dalına dokunulduğunda | R-23 | OTA kapısını bekle |
| T-9 | Refactor isteği geldiğinde | R-24 | Handover kapısını bekle |
| T-10 | Dosya yazımından hemen önce | R-25, R-26 | Beyaz liste denetimi |
| T-11 | Bir iddia cümlesi yazıldığında | R-27, R-31 | Kaynak teyidi |
| T-12 | İndeks dosyası düzenlendiğinde | R-28 | Satır sayacı |
| T-13 | Tablo eklendiğinde | R-29 | Boş hücre taraması |
| T-14 | Dosya kaydedildiğinde | R-30 | UTF-8 taraması |

### §B.4 Erken belirti tablosu

| Risk | Erken belirti | Ölçülebilir işaret | Yanlış sinyal |
|---|---|---|---|
| R-08 | Callback süresinde ani artış | Zamanlama logu | CPU kullanımı artışı |
| R-09 | Kilit beklemesi logu | Kilit beklemesi satırı | Düşük CPU |
| R-10 | Derleyici uyarısı | İmza uyarısı | Statik analiz temizliği |
| R-11 | İkinci uygulama açılış reddi | Hata kodu | Sürücü çökmesi |
| R-12 | Hoparlörde tıkırtı | Dinleme testi | Yazılım klipleri |
| R-13 | Zemin gürültüsü artışı | Ölçüm spektrumu | Kayıp paket logu |
| R-14 | Harmonik bileşen artışı | Spektrum tepe değeri | Termal throttling |
| R-15 | BOM listesinde yasaklı parça kodu | Parça numarası eşleşmesi | Tedarikçi adı |
| R-19 | Sürüm dosyası bulunamaması | Eksik sürüm kaydı | Bağımlılık listesi |
| R-21 | Kaynak dizininde dosya yokluğu | Dizin sayımı 0 | Dizin varlığı |
| R-27 | İddia için kaynak yok | Kaynak sayısı 0 | Kaynak bulunması |

### §B.5 İlişkili kural ve ADR bağlantıları

| Risk grubu | Kural / ADR | Bağlantı biçimi |
|---|---|---|
| R-15 | ADR-038 H001 | PCM5122 mutlak yasak; PCM3168A ve AK4458 zorunlu |
| R-17 | ADR-017 | 32-bit float veri gösterimi |
| R-11 | ADR-019 | İşletim sistemi başına oynatıcı donmuş; değişiklik ≥088 |
| R-08, R-09, R-10 | K001 kural 1, 2, 3 | Sıfır tahsis, lock-free, noexcept |
| R-18 | K001 kural 8 | 512 örnek, 48 kHz ölçüm durumu PLANNED |
| R-12 | K001 kural 10 | 0.5 V üzeri DC offset → koruma rölesi zorunlu |
| R-19, R-20 | K001 kural 12 | Sürüm kanıtı yok → VERIFICATION REQUIRED |
| R-22, R-23, R-24 | K001 kural 13 | Bağımlılık bump, firmware OTA, refactor → DUR + handover |

### §B.6 Sorumluluk matrisi (RACI özeti)

| Risk grubu | Sahip | Uygulayıcı | Onaylayıcı | Bilgilendirilen |
|---|---|---|---|---|
| Real-time kod | kod | kod | mimari | qa |
| ADR ihlalleri | mimari | mimari | Vault Steward | kod |
| Ölçüm eşikleri | Ölçüm (saha) | audio-hw | mimari | qa |
| Vault bütünlüğü | kanit | kanit | Vault Steward | mimari |
| Sürüm kanıtları | kanit | dependency | mimari | devops |
| Yetki ve beyaz liste | kanit | kanit | Vault Steward | tümü |
| Envanter boşluğu | mimari | core | Vault Steward | kanit |
| Depolama tedariki | mimari | core | Vault Steward | kanit |

## §C Azaltma, Tepki ve Kalan Risk Matrisi (şablon karşılığı — genişletilmiş)

Azaltma üç katmana ayrılır: **önleme** (oluşumu engelleme), **tespit** (erken
yakalama) ve **tepki** (oluştuğunda zararı sınırlandırma). Yalnız tespit'e
güvenilen risk kabul edilemez; çünkü ölçüm maliyeti sürekli taşınır.

### §C.1 Önleme tablosu

| Risk | Önleme mekanizması | Uygulama yeri | Etkinlik |
|---|---|---|---|
| R-08 | Tahsis yasağı + statik analiz | Kod incelemesi | Yüksek |
| R-09 | Lock-free kuralı + atomik veri yapısı | Kod incelemesi | Yüksek |
| R-10 | İmza denetimi ve derleyici uyarısı | Derleme | Orta |
| R-11 | Tek sahiplik protokolü | Açılış | Yüksek |
| R-12 | Röle devresi ve eşik devresi | PCB tasarımı | Yüksek |
| R-15 | Yasaklı parça listesi denetimi | BOM incelemesi | Yüksek |
| R-16 | Topoloji sabiti kaydı | ADR | Yüksek |
| R-17 | Tek sayı gösterimi kuralı | Kod incelemesi | Orta |
| R-19, R-20 | Sürüm kanıtı dosyası zorunluluğu | Görev kapanışı | Orta |
| R-25, R-26 | Beyaz liste denetimi | Yazma öncesi | Yüksek |
| R-31 | Kaynak teyidi adımı | Yazma öncesi | Yüksek |

### §C.2 Tespit tablosu

| Risk | Tespit mekanizması | Tepki süresi | Yanlış pozitif |
|---|---|---|---|
| R-08, R-09 | Zamanlama logu ve kilit beklemesi | Çalışma zamanı | Yüksek |
| R-10 | Derleyici çıktısı | Derleme anı | Düşük |
| R-11 | Açılış testi | Devreye alma | Düşük |
| R-12, R-13, R-14 | İmalat ölçümü | Devreye alma | Düşük |
| R-15, R-16 | BOM ve kod taraması | İnceleme anı | Düşük |
| R-17, R-18 | Yapılandırma doğrulaması | Çalışma zamanı | Orta |
| R-19, R-20, R-21 | Sürüm ve dosya varlık taraması | Görev kapanışı | Düşük |
| R-22, R-23, R-24 | Kapı kaydı | İş akışı | Düşük |
| R-25, R-26 | Yazma öncesi liste | Yazma anı | Orta |
| R-27, R-31 | Kaynak sayımı | Yazma anı | Orta |
| R-28, R-29, R-30 | Satır ve doldurucu taraması | Görev kapanışı | Düşük |

### §C.3 Tepki tablosu

| Risk | Tepki eylemi | Geri alma | Üst karar eşiği |
|---|---|---|---|
| R-08, R-09 | Kodu geri al ve yeniden yaz | Revizyon | 3 başarısız tur |
| R-10 | İmzayı düzelt, derlemeyi tekrarla | Revizyon | 2 tur |
| R-11 | Kilidi bırak, ikinci sahibi reddet | Otomatik | Anında |
| R-12 | Röleyi devreye al, çıkışı kes | Donanım | Anında |
| R-13, R-14 | Partiyi reddet, ölçümü tekrarla | Üretim | 2 ardışık red |
| R-15, R-16 | Parça değişimi ve ADR kaydı | BOM | Her durumda |
| R-19, R-20 | Kanıt üretilemezse etiketi koru | Yok | Her durumda |
| R-21 | PLANNED etiketini düşürme | Yok | Kaynak gelmedikçe |
| R-22, R-23, R-24 | Değişikliği durdur, handover başlat | İş akışı | Her durumda |
| R-25, R-26 | Yazılanı geri al, loga yaz | Revert | Her durumda |
| R-27 | İddiayı sil veya işaretle | Metin | Her durumda |
| R-30 | Dosyayı UTF-8 ile yeniden kaydet | Metin | Her durumda |

### §C.4 Kalan risk tablosu

| Risk | Kalan öncelik | Gerekçe | Yeniden değerlendirme |
|---|---|---|---|
| R-19, R-20 | 12 | Sürüm kanıtı sağlayıcıdan gelmeli | Kanıt elde edildiğinde |
| R-21 | 20 | Kaynak diskte yok; bağımsız üretilemez | İlk kaynak eklendiğinde |
| R-06 (eski) | yüksek | Fiziksel tezgâh kurulmadan kapanamaz | Tezgâh kurulduğunda |
| R-07 (eski) | orta | K002 vault girişi başka dalın işi | K002 eklendiğinde |
| R-12 | 15 | Devre onayı üretimde verilir | İlk prototipte |
| R-13, R-14 | 12 | Ölçüm yalnızca sahada alınır | İmalat testinde |

### §C.5 Azaltma sahipliği özeti

| Sahip | Üstlendiği risk sayısı | En yüksek öncelik | Tekrar sıklığı |
|---|---|---|---|
| kod | 5 | 15 | Her kod incelemesinde |
| mimari | 5 | 15 | Her ADR turunda |
| kanit | 8 | 20 | Her doğrulama koşusunda |
| Ölçüm (saha) | 4 | 15 | İmalat ve devreye alma |
| Vault Steward | 2 | 16 | Her vault-sync |

### §C.6 Termin ve bağımlılık planı

| Risk grubu | Termin | Bağımlılık | Gecikme etkisi |
|---|---|---|---|
| Real-time kod | Kod incelemesi öncesi | K001 kaynak kodu PLANNED | Kapı kapanamaz |
| Ölçüm eşikleri | Devreye alma | Tezgâh donanımı | Kabul kapısı açık kalır |
| Envanter | İlk envanter turu | K001 Q4 | core kapsamı UNKNOWN kalır |
| Sürüm kanıtları | Görev kapanışı | Dış sağlayıcı | Etiket korunur |
| Vault bütünlüğü | Görev kapanışı | Yok | Sürekli |
| Tedarik | Yıllık gözden geçirme | Üretici iletişimi | Orta vade |

### §C.7 Öncelik-bütçe matrisi (nereden başlanır)

| Sıra | Aralıktaki riskler | Gerekçe | Tahmini çaba |
|---|---|---|---|
| 1 | R-21 (20) | En yüksek puanlı tek satır; kanıt açığı | Dış bağımlı |
| 2 | R-03, R-08, R-09, R-11, R-12, R-27 (16) | Güvenlik ve bütünlük | Orta |
| 3 | R-10, R-13, R-14, R-19, R-20, R-22, R-23, R-24, R-31 (12) | Kapı ve eşik | Düşük-orta |
| 4 | R-15, R-16, R-25, R-26 (10) | Denetimle kapanır | Düşük |
| 5 | R-17, R-18, R-29, R-02 (8-9) | Yerel düzeltme | Düşük |
| 6 | R-28, R-30 (6) | Otomatik tarama | Düşük |
| 7 | R-04, R-05 (eski bantlar) | Dış kaynak / plan | Beklemede |

## §D Doğrulama, Kanıt ve Kaynaklar (şablon karşılığı — genişletilmiş)

Her risk kapanışını sağlayan **özgül, tekrarlanabilir** bir doğrulama adımına
bağlanır. Doğrulama adımı olmayan satır kapatılamaz; `AÇIK` durumunda kalır.
Doğrulama scriptleri yalnızca **repo kökünden** çalıştırılır — vault içinden
çalıştırılan script göreli yolu çözemediği için sahte sonuç üretir.

### §D.1 Doğrulama adımları

| Risk | Doğrulama adımı | Beklenen çıktı | Kanıt tipi |
|---|---|---|---|
| R-08 | Tahsis belirtecini tara | 0 sayı | Statik analiz |
| R-09 | Kilit belirtecini tara | 0 sayı | Statik analiz |
| R-10 | Callback imzalarını denetle | Tamamı noexcept | Derleyici çıktısı |
| R-11 | İkinci sahip açılışını dene | Reddedilir | Test kaydı |
| R-12 | DC offset ölç | 0.5 V ve altı | Ölçüm raporu |
| R-13 | SNR ölç | 100 dB üzeri | Ölçüm raporu |
| R-14 | THD+N ölç | 0.01% altı | Ölçüm raporu |
| R-15 | Yasaklı parça kodunu tara | 0 kullanım | Tarama çıktısı |
| R-19, R-20 | Sürüm kanıtı dosyasını oku | Sürüm satırı mevcut | Dosya içeriği |
| R-21 | Kaynak dosya say | Sıfırdan farklı | Dizin listesi |
| R-25, R-26 | Beyaz listeyi denetle | 0 yetkisiz yol | Liste çıktısı |
| R-27, R-31 | Kaynak ve URL teyidi | HTTP 200 | Ağ çıktısı |
| R-28 | İndeks satırını say | 40-100 arası | Sayım çıktısı |
| R-29, R-30 | Doldurucu ve UTF-8 taraması | 0 isabet | Tarama çıktısı |

### §D.2 Doğrulama sıklığı

| Sıklık | Kapsam | Tetikleyici | Sorumlu |
|---|---|---|---|
| Her dosya yazımında | R-25, R-26, R-27, R-29, R-30, R-31 | Dosya kaydı | Yazan agent |
| Her görev kapanışında | R-28 ve FM bütünlüğü | Görev sonu | Orkestratör |
| Her kod incelemesinde | R-08, R-09, R-10 | İnceleme | kod |
| Devreye almada | R-11, R-12, R-13, R-14 | Fiziksel test | Ölçüm (saha) |
| Bağımlılık değişiminde | R-19, R-20, R-22 | Paket güncellemesi | kanit |
| Yıllık | R-03, R-04, R-06 | Dış kaynak taraması | kanit |

### §D.3 Kapanış kriterleri

| Kriter | Şart | Aksi durum |
|---|---|---|
| Kanıt | Doğrulama adımı çalıştırılmış ve çıktısı saklanmış | Risk açık kalır |
| Tekrarlanabilirlik | Aynı adım ikinci kez aynı sonucu verir | Adım yeniden yazılır |
| Bağımsızlık | Sonuç başka bir oturumda da üretilir | Sonuç geçersiz sayılır |
| Kapsam | Azaltılan bölüm riskin tamamı değilse | Kalan kısım açık kalır |
| Sahiplik | Kapanış onayı tanımlı sahipten gelir | Sahipsiz satır açık kalır |
| Tarih | Kapanış tarihi ve mod kayıtlı | Kayıt eksik sayılır |

### §D.4 Repo kanıtları (Read ile doğrulanmış, 2026-10-13)

| Dosya | Satır | İlgililik |
|---|---|---|
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 24 | 662 satırlık cihaz kayıt katmanı — envanter riski R-02 ve R-21'in yazılım yüzeyi |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 216 | Cihaz kayıt akışı başlangıcı — envanter alanlarının işlenmesi |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 225 | Cihaz kayıt akışı ortası — alan doğrulama |
| Dosya: `shared/src/Device/DeviceManager.php` / Satır: 239 | Cihaz kayıt akışı sonu — kalıcılık sınırı |
| Dosya: `shared/src/Device/DeviceDetector.php` / Satır: 19 | 316 satırlık tespit katmanı — donanım tanıma riski |
| Dosya: `shared/src/Device/DeviceRenderer.php` / Satır: 19 | 162 satırlık sunum katmanı — görselleştirme riski |
| Dosya: `shared/src/Device/DeviceCssMap.php` / Satır: 5 | 57 satırlık eşleme tablosu — K002 eşlemesi riski R-07 |
| Dosya: `api.coremusic.net/config/routes.php` / Satır: 42 | API rota kaydı — raporlama uçlarının varlığı |
| Dosya: `shared/composer.json` / Satır: 24 | Bağımlılık sürümü — sürüm kanıtı yöntemine örnek |
| Dosya: `.github/workflows/ci.yml` / Satır: 14 | CI aşaması — otomatik doğrulama tetikleyicisi |
| Dosya: `.github/workflows/ci.yml` / Satır: 22 | CI aşaması — kapanış kapısı |

### §D.5 Bu genişletmede eklenen doğrulanmış kaynaklar

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Depolama üreticisi risklerinin birincil kaynağı üreticinin kendi kurumsal sitesidir | https://www.westerndigital.com/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| NAND/DRAM tedarik zinciri ikinci bağımsız üretici kanıtı gerektirir | https://www.micron.com/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| Depolama üretici çeşitliliği olmadan tedarik riski tek kaynağa kilitlenir | https://www.kioxia.com/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |
| Üretici riski puanlaması OEM ve yarı iletken üreticilerini ayrı ele almalıdır | https://semiconductor.samsung.com/ | 2026-10-13 | ✅ HTTP 200 doğrulandı |

### §D.6 İşaretli ve reddedilen iddialar

| İddia | Durum | Gerekçe |
|---|---|---|
| Belirli bir HDD/SSD markası K001 envanterinde | ⚠️ VERIFICATION REQUIRED | Envanter yok (K001 Q4) |
| Belirli bir ölçüm sonucu (SNR/THD+N) | ⚠️ VERIFICATION REQUIRED | Tezgâh yok, ölçüm yok |
| JUCE 9 ve ASIO SDK 2.3.4 sürümleri | ⚠️ VERIFICATION REQUIRED | Sürüm kanıtı yok |
| Neva Engine kaynak dosyalarının içeriği | ⚠️ VERIFICATION REQUIRED | Dizin var, dosya yok (PLANNED) |
| PCM5122 kullanan bir üretim satırı | ❌ REDDEDİLDİ | ADR-038 H001 ile mutlak yasak |
| Class D amplifikatör kullanımı | ❌ REDDEDİLDİ | K001 topoloji sabiti Class AB |
| Belirli bir CVE kimliği | ❌ YAZILMAZ | Kanıtsız kimlik uydurmadır |
| Belirli bir satır numarası (havuz dışı) | ❌ YAZILMAZ | Yalnız Read ile doğrulanmış satır |

### §D.7 Sonraki adımlar

| # | Adım | Sahip | Kapı |
|---|---|---|---|
| N-1 | K001 Q4 cihaz envanterini çıkar | mimari | core kapsamı |
| N-2 | K002 vault girişi ve eşleme | kanit | anayasa |
| N-3 | Fiziksel ölçüm tezgâhı kur | Ölçüm (saha) | kabul kapısı |
| N-4 | Sürüm kanıtlarını topla | kanit | bağımlılık |
| N-5 | kernel-core sınır kararını yaz | mimari | anayasa §7 |
| N-6 | Yıllık URL yeniden doğrulaması | kanit | kanıt bütünlüğü |

### §D.8 Görev sonu ölçüm tablosu (bu genişletme için)

| Ölçüm | Beklenen | Kayıt |
|---|---|---|
| Dosya satır sayısı | 500 ve üzeri | Doğrulama çıktısı |
| Frontmatter alan sayısı | 10 alan eksiksiz | FM ayrıştırma |
| parent alanının yeni adı | parent_id | FM ayrıştırma |
| Alan adı değeri tırnaklı mı | group tırnaksız | FM ayrıştırma |
| Boş tablo hücresi | 0 | Tablo taraması |
| Doldurucu ifade | 0 | Metin taraması |
| Mojibake işaretçisi | 0 | Karakter taraması |
| Yetkisiz dizin yazımı | 0 | Beyaz liste |
| Dosya veya dizin yeniden adı | 0 | Ad listesi |
| Yeni URL envanter kesişimi | 0 | Kesişim hesabı |
| Son satır biçimi | Kanonik authority satırı | Satır karşılaştırma |
| Git commit | Atılmadı | İş akışı kaydı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
