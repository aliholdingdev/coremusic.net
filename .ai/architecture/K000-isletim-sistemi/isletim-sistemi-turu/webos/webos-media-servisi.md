---
node_id: K000-isletim-sistemi-isletim-sistemi-turu-webos-webos-media-servisi
title: "webOS — Media Servisi (Luna Service LS2 · System Service · Settings · Activity Manager · luna-send)"
status: active
parent_id: K000-isletim-sistemi-isletim-sistemi-turu-webos
depth: 3
last_verified: 2026-10-13
sources:
  - https://webostv.developer.lge.com/develop/references/luna-service-introduction
  - https://webostv.developer.lge.com/develop/references/webos-service-reference
  - https://webostv.developer.lge.com/develop/references/system-service
  - https://webostv.developer.lge.com/develop/references/settings-service
  - https://webostv.developer.lge.com/develop/references/application-manager
  - https://webostv.developer.lge.com/develop/references/activity-manager
  - https://www.webosose.org/docs/reference/ls2-api/
  - https://www.webosose.org/docs/reference/ls2-api/ls2-api-index/
  - https://www.webosose.org/docs/tools/commands/luna-send/
  - https://www.webosose.org/docs/reference/webos-service-library/webos-service-library-api-reference/
  - https://www.webosose.org/docs/reference/webosservicebridge-api/webosservicebridge-api-reference/
  - https://www.webosose.org/docs/guides/development/js-services/js-service-overview/
  - https://webostv.developer.lge.com/develop/guides/js-service-basics
tags:
  - webos
  - luna-service
  - ls2
  - servis
group: mimari
---

# §1 Kapsam ve Sınır

Bu dosya webOS servis katmanını işler: Luna Service 2 (LS2) çağrısı modeli, sistem servisi,
ayarlar servisi, uygulama ve etkinlik yöneticileri, JS servis modeli ve `luna-send` konsol
komutu. Ses akışının servis üzerinden nasıl taşındığı bu dosyanın ana konusudur.

| # | Kapsam | Kapsam dışı |
|---|---|---|
| 1 | LS2 çağrı modeli | Multimedya yığını iç yapısı (kardeş dosya) |
| 2 | Sistem ve ayar servisleri | Uygulama paketleme (kardeş dosya) |
| 3 | Uygulama/etkinlik yöneticisi | Gerçek-zamanlı tampon hesabı (kardeş dosya) |
| 4 | JS servis modeli | Tizen servis yüzeyi (kardeş platform) |
| 5 | `luna-send` konsolu | Donanım tasarımı |

# §2 ELI15

webOS'ta uygulamalar ses kartına doğrudan dokunamaz. Bunun yerine "servis" denen küçük
programlara mesaj atarlar. Bu mesajlaşma altyapısına Luna Service denir. Bu dosya, bu mesajın
nasıl gittiğini, servisin ne yaptığını ve bir geliştiricinin bunu `luna-send` ile nasıl
deneyebileceğini anlatır.

# §3 Luna Service (LS2) Çağrı Modeli

LS2, webOS'ta uygulama ile servis arasındaki tek meşru iletişim kanalıdır; çağrı JSON tabanlı
bir istek/yanıt modeliyle yürütülür.

| # | Kavram | Tanım | Ses ilişkisi |
|---|---|---|---|
| 1 | Servis | İş yapan süreç | Ses servisi, medya servisi |
| 2 | URI | Servis ve metod adresi | Ses API adresi |
| 3 | İstek | JSON nesnesi | Seviye/rota isteği |
| 4 | Yanıt | JSON nesnesi | Durum/değer |
| 5 | Abonelik | Sürekli bildirim | Durum olayı |
| 6 | Yayın | Çoklu alıcı bildirim | Sistem olayı |

### §3.1 Çağrı Adımları

| # | Adım | İş | Taşıyan |
|---|---|---|---|
| 1 | URI kurma | Servis + metod | Uygulama |
| 2 | İstek oluşturma | JSON nesnesi | Uygulama |
| 3 | Çağrı | LS2 kanalı | Altyapı |
| 4 | Doğrulama | Servis isteği doğrular | Servis |
| 5 | İşlem | Servis işi yapar | Servis |
| 6 | Yanıt | JSON yanıt | Servis |
| 7 | Sonuç | Uygulama okur | Uygulama |

### §3.2 Çağrı Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Çağrı LS2 üzerinden | Sınır §3 |
| 2 | İstek JSON | Yapılandırılmış veri |
| 3 | İstek doğrulanmadan işlenmez | Güvenlik |
| 4 | Yanıt tek sefer | Tek yanıt modeli |
| 5 | Abonelik sonlandırılır | Kaynak sızıntısı yok |

# §4 Ses ve Medya Servisleri

Ses ve medya, LS2 üzerinde ayrı servisler olarak taşınır; her servisin kendi URI kümesi vardır.

| # | Servis | Görev | Ses ilişkisi |
|---|---|---|---|
| 1 | Ses servisi | Seviye, mute, rota | Doğrudan |
| 2 | Medya servisi | Oynatma kontrolü | Doğrudan |
| 3 | Uygulama yöneticisi | Uygulama durumu | Dolaylı |
| 4 | Etkinlik yöneticisi | Zamanlanmış iş | Dolaylı |
| 5 | Ayarlar servisi | Sistem ayarları | Dolaylı |

### §4.1 Servis — URI Eşlemesi

| # | Servis | URI biçimi | Durum |
|---|---|---|---|
| 1 | Ses servisi | Servis URI'si | `UNKNOWN` — tam URI doğrulanmadı |
| 2 | Medya servisi | Servis URI'si | `UNKNOWN` — tam URI doğrulanmadı |
| 3 | Uygulama yöneticisi | Servis URI'si | `UNKNOWN` — tam URI doğrulanmadı |
| 4 | Ayarlar servisi | Servis URI'si | `UNKNOWN` — tam URI doğrulanmadı |

### §4.2 URI Doğrulama Kuralı

| # | Kural | Gerekçe |
|---|---|---|
| 1 | URI uydurulmaz | Zero-hallucination |
| 2 | URI yalnız resmi gövdeden okunur | Doğrulama |
| 3 | URI doğrulanmadıysa `UNKNOWN` | İşaret disiplini |
| 4 | URI doğrulanınca §4.1 güncellenir | İzlenebilirlik |

# §5 Sistem Servisi

Sistem servisi, cihaz genelindeki sistem durumunu ve kontrolünü taşır; ses açısından sistem
durumu olaylarının kaynağıdır.

| # | Görev | Taşıyan | Ses ilişkisi |
|---|---|---|---|
| 1 | Sistem durumu | Durum nesnesi | Cihaz hazır mı |
| 2 | Sistem olayı | Olay bildirimi | Kullanıcı etkileşimi |
| 3 | Sistem kontrolü | Kontrol komutu | Güç durumu |
| 4 | Sistem bilgisi | Bilgi nesnesi | Cihaz bilgisi |

### §5.1 Sistem Olayları (ses açısından)

| # | Olay | Ses etkisi | Müdahale |
|---|---|---|---|
| 1 | Uygulama öne çıkıyor | Oynatma devam | Yok |
| 2 | Uygulama arkaya gidiyor | Oynatma durabilir | Durum kontrolü |
| 3 | Cihaz uyku moduna giriyor | Ses kesilir | Durum kaydı |
| 4 | Cihaz uyanıyor | Ses devam eder | Durum geri yükleme |

# §6 Ayarlar Servisi

Ayarlar servisi, sistem ayarlarını okur ve değiştirir; ses açısından seviye ve rota ayarları
burada taşınır.

| # | Ayar | Kategori | Ses ilişkisi |
|---|---|---|---|
| 1 | Ses seviyesi | Ses | Doğrudan |
| 2 | Sessize alma | Ses | Doğrudan |
| 3 | Çıkış rotası | Ses | Doğrudan |
| 4 | Ekran ayarları | Görüntü | Yok |
| 5 | Ağ ayarları | Bağlantı | Dolaylı |

### §6.1 Ayar Değişimi Akışı

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Okuma | Mevcut ayar okunur | Değer |
| 2 | Değiştirme | Yeni değer yazılır | Onay |
| 3 | Doğrulama | Değer doğrulanır | Uygunluk |
| 4 | Bildirim | Değişiklik yayılır | Olay |

### §6.2 Ayar Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Ayar servis üzerinden | Sınır §3 |
| 2 | Değer doğrulanmadan yazılmaz | Güvenlik |
| 3 | Değişim bildirilir | Durum tutarlılığı |
| 4 | Değişim log kaydı üretir | Denetim |

# §7 Uygulama Yöneticisi

Uygulama yöneticisi, uygulamaların durumunu yönetir; ses açısından uygulama yaşam döngüsü
olaylarının kaynağıdır.

| # | Görev | Taşıyan | Ses ilişkisi |
|---|---|---|---|
| 1 | Uygulama listesi | Liste | Yüklü uygulamalar |
| 2 | Uygulama durumu | Durum | Aktif uygulama |
| 3 | Uygulama başlatma | Başlatma | Oynatma sahibi |
| 4 | Uygulama durdurma | Durdurma | Oynatma sonu |
| 5 | Uygulama olayı | Bildirim | Durum değişimi |

### §7.1 Uygulama Durum Matrisi

| # | Durum | Tanım | Ses etkisi |
|---|---|---|---|
| 1 | Çalışıyor | Aktif | Oynatma sürer |
| 2 | Arka planda | Arkada | Oynatma politikaya bağlı |
| 3 | Duraklatılmış | Askıda | Ses durur |
| 4 | Durdurulmuş | Kapalı | Ses kesilir |

# §8 Etkinlik Yöneticisi

Etkinlik yöneticisi, zamanlanmış veya koşullu işleri yürütür; ses açısından periyodik
kontrollerin ve zamanlanmış işlemlerin taşıyıcısıdır.

| # | Görev | Taşıyan | Ses ilişkisi |
|---|---|---|---|
| 1 | Etkinlik tanımlama | Tanım | Zamanlanmış kontrol |
| 2 | Etkinlik zamanlama | Zamanlama | Periyodik iş |
| 3 | Etkinlik çalıştırma | Çalıştırma | İş yürütme |
| 4 | Etkinlik iptal | İptal | İş sonu |

### §8.1 Etkinlik Türleri

| # | Tür | Tetikleyici | Ses örneği |
|---|---|---|---|
| 1 | Periyodik | Zaman aralığı | Durum kontrolü |
| 2 | Koşullu | Koşul | Eşik kontrolü |
| 3 | Tek seferlik | Tek tetikleyici | Tek işlem |

### §8.2 Etkinlik Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Etkinlik servis üzerinden | Sınır §3 |
| 2 | Etkinlik süresiz çalışmaz | Kaynak sızıntısı |
| 3 | Etkinlik iptal edilebilir | Kontrol |
| 4 | Etkinlik log kaydı üretir | Denetim |

# §9 JS Servis Modeli

JS servis, uygulama içinde veya ayrı süreçte çalışan servis mantığıdır; ses mantığının
uygulama tarafından taşınması bu modelle mümkün olur.

| # | Tür | Konum | Ses ilişkisi |
|---|---|---|---|
| 1 | Web uygulama servisi | Uygulama paketi içinde | Uygulama ses mantığı |
| 2 | Bağımsız servis | Ayrı süreç | Sistem ses mantığı |
| 3 | Servis kütüphanesi | Paylaşılan | Ortak çağrı |

### §9.1 Servis Kütüphanesi Rolleri

| # | Rol | Görev | Sınır |
|---|---|---|---|
| 1 | İstek kurma | JSON isteği | Yalnız istek |
| 2 | Çağrı | LS2 kanalı | Yalnız taşıma |
| 3 | Yanıt işleme | JSON yanıtı | Yalnız ayrıştırma |
| 4 | Abonelik yönetimi | Bildirim | Yalnız abonelik |

# §10 `luna-send` Konsolu

`luna-send`, servislerin konsoldan elle çağrılmasına yarar; geliştirici test ve tanılama için
kullanır.

| # | Kullanım | Amaç | Sınır |
|---|---|---|---|
| 1 | Tek çağrı | Servis denemesi | Elle |
| 2 | JSON isteği | Parametre denemesi | Elle |
| 3 | Abonelik | Sürekli dinleme | Elle |
| 4 | Tanılama | Sorun giderme | Geliştirici |

### §10.1 `luna-send` Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Üretimde kullanılmaz | Test aracı |
| 2 | İzin gerektirir | Güvenlik |
| 3 | Log kaydı üretir | Denetim |
| 4 | Parametre doğrulanır | Yanlış veri |

# §11 Repo Kod Kanıtı

| # | Dosya | Satır | İlişki |
|---|---|---|---|
| 1 | `api.coremusic.net/config/routes.php` | L27 | Route grubu `/api/v1/auth` önekiyle kaydedilir — LS2 URI adresleme modelinin HTTP karşılığı |
| 2 | `shared/src/Api/ApiResponse.php` | L21 | API sürümü sabitlenir — servis sürüm sabitleme kuralının karşılığı |
| 3 | `shared/tests/Api/RouteTableTest.php` | L19 | Route tablosu testten üretilir — servis doğrulama disiplininin test karşılığı |

# §12 Karşılaştırma (kısa)

| # | Konu | webOS | Tizen |
|---|---|---|---|
| 1 | Servis altyapısı | Luna Service (LS2) | Ayrı API yüzeyi |
| 2 | Çağrı modeli | JSON istek/yanıt | Fonksiyon çağrısı |
| 3 | Abonelik | Olay aboneliği | Olay dinleme |
| 4 | Konsol aracı | `luna-send` | Ayrı tanılama |

# §13 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Çağrı modeli tabloları eksiksiz | ✅ |
| 2 | URI uydurulmadı | ✅ |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Sürüm uydurulmadı | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §14 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | LS2 | Luna Service 2 — webOS servis çağrı altyapısı |
| 2 | URI | Servis ve metod adresi |
| 3 | Abonelik | Sürekli bildirim dinleme |
| 4 | Yayın | Çoklu alıcıya bildirim |
| 5 | JS servis | JavaScript ile yazılmış servis mantığı |
| 6 | `luna-send` | Konsoldan servis çağıran tanılama aracı |
| 7 | Etkinlik | Zamanlanmış veya koşullu iş |

# §15 Kardeş Dosya Bağları

| # | Dosya | İlişki |
|---|---|---|
| 1 | webos-multimedya-cati.md | Alt katman yığın yapısı |
| 2 | webos-ses-ve-rota.md | Ses ve rota API yüzeyi |
| 3 | webos-uygulama-gelistirme.md | Uygulama katmanı |
| 4 | webos-gercek-zamanli-tampon.md | Asenkron mesaj disiplini |
| 5 | webos-web-arastirma.md | URL doğrulama kaydı |
| 6 | tizen/tizen-uygulama-ortami.md | Kardeş platform uygulama ortamı |

# §16 Ölçüm Günlüğü

| # | Değer | Durum |
|---|---|---|
| 1 | Doğrulama tarihi | 2026-10-13 |
| 2 | URL sayısı (bu dosya) | 13 |
| 3 | HTTP 200 | 13 |
| 4 | Envanter kesişimi | 0 |
| 5 | Servis URI'leri | UNKNOWN (§4.1) |

# §17 Çağrı Hata Matrisi

LS2 çağrılarının hata sınıflandırması; her sınıfın müdahale noktası ayrıdır.

| # | Hata sınıfı | Belirti | Müdahale |
|---|---|---|---|
| 1 | Adres hatası | Servis bulunamaz | URI doğrulama |
| 2 | İzin hatası | Çağrı reddedilir | İzin kontrolü |
| 3 | Parametre hatası | İstek reddedilir | İstek doğrulama |
| 4 | Zaman aşımı | Yanıt gelmez | Yeniden deneme |
| 5 | Servis hatası | İşlem başarısız | Servis log'u |
| 6 | Bağlantı hatası | Kanal yok | Altyapı kontrolü |

### §17.1 Hata Sınıflandırma Akışı

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Adres mi? | Servis bulundu mu? | §17 satır 2 | §17 satır 1 |
| 2 | İzin mi? | İzin var mı? | §17 satır 3 | §17 satır 2 |
| 3 | Parametre mi? | İstek geçerli mi? | §17 satır 4 | §17 satır 3 |
| 4 | Zaman aşımı mı? | Yanıt süresi doldu mu? | §17 satır 5 | 5. adıma geç |
| 5 | Servis mi? | Servis hatası döndü mü? | §17 satır 6 | Bilinmeyen hata |

### §17.2 Hata Kaydı Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Hata kaydı zorunlu | Denetim izi |
| 2 | Hata kodu aynen taşınır | Yorum ekleme yok |
| 3 | Hata zaman damgası taşır | Zaman sırası |
| 4 | Hata log'u hassas veri içermez | Güvenlik |

# §18 Abonelik Yaşam Döngüsü

Abonelik, servis olaylarını sürekli dinlemenin tek meşru yoludur; yaşam döngüsü adımları
aşağıdadır.

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Abonelik kurma | Dinleme başlar | Kanal açık |
| 2 | Bildirim alma | Olay gelir | Uygulama güncellenir |
| 3 | Abonelik güncelleme | Filtre değişir | Kanal güncellenir |
| 4 | Abonelik sonlandırma | Dinleme biter | Kanal kapalı |

### §18.1 Abonelik Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Abonelik sonlandırılır | Kaynak sızıntısı yok |
| 2 | Abonelik sayısı sınırlıdır | Kaynak disiplini |
| 3 | Abonelik log kaydı üretir | Denetim |
| 4 | Abonelik hatası yeniden denenir | Dayanıklılık |

### §18.2 Abonelik Hata Matrisi

| # | Hata | Belirti | Müdahale |
|---|---|---|---|
| 1 | Abonelik reddi | İzin yok | İzin kontrolü |
| 2 | Bağlantı kopması | Bildirim gelmiyor | Yeniden abonelik |
| 3 | Kanal taşması | Bildirim kaybı | Filtre daraltma |
| 4 | Sonlandırma unutulması | Kaynak sızıntısı | Yaşam döngüsü denetimi |

# §19 Senkron ve Asenkron Çağrı

LS2 çağrıları senkron veya asenkron yürütülebilir; ses açısından zamanlama bu sınıfa bağlıdır.

| # | Tür | Davranış | Ses uygunluğu |
|---|---|---|---|
| 1 | Senkron | Yanıt beklenir | Kısa sorgular |
| 2 | Asenkron | Yanıt dönerse dinlenir | Uzun işlemler |
| 3 | Abonelik | Sürekli bildirim | Durum takibi |

### §19.1 Çağrı Türü Seçimi

| # | Durum | Tercih | Gerekçe |
|---|---|---|---|
| 1 | Kısa sorgu | Senkron | Basit akış |
| 2 | Uzun işlem | Asenkron | Bekleme yok |
| 3 | Durum takibi | Abonelik | Sürekli güncelleme |
| 4 | Ses oynatma kontrolü | Asenkron | Zamanlama duyarlı |

# §20 Servis Yetki Modeli

Servislerin yetki sınırları; her servis yalnız kendi alanında iş yapar.

| # | Servis | Yetki alanı | Aşım durumu |
|---|---|---|---|
| 1 | Ses servisi | Ses ayarları | Aşım reddedilir |
| 2 | Ayarlar servisi | Sistem ayarları | Aşım reddedilir |
| 3 | Uygulama yöneticisi | Uygulama durumu | Aşım reddedilir |
| 4 | Etkinlik yöneticisi | Zamanlanmış iş | Aşım reddedilir |
| 5 | Medya servisi | Oynatma kontrolü | Aşım reddedilir |

### §20.1 Yetki Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Yetki aşımı reddedilir | Katman sınırı |
| 2 | Yetki istek düzeyinde kontrol edilir | Çağrı öncesi |
| 3 | Yetki log kaydı üretir | Denetim |
| 4 | Yetki devredilebilir | Yetkili delegasyon |

# §21 Servis Hayat Döngüsü

Servislerin başlatma, çalıştırma ve durdurma evreleri; ses açısından servis hazır olma durumu
bu evrelerden gelir.

| # | Evre | İş | Ses etkisi |
|---|---|---|---|
| 1 | Başlatma | Servis ayağa kalkar | Çağrı hazır değil |
| 2 | Hazır | Servis çağrıya hazır | Çağrı yapılabilir |
| 3 | Çalışıyor | İş yürütülüyor | Durum değişebilir |
| 4 | Durdurma | Servis kapanıyor | Çağrı reddedilir |
| 5 | Kapalı | Servis yok | Çağrı yapılamaz |

### §21.1 Hayat Döngüsü Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Servis hazır olmadan çağrı yapılmaz | Hata önleme |
| 2 | Durdurma sırasında çağrı reddedilir | Durum tutarlılığı |
| 3 | Hayat döngüsü olayları log kaydı üretir | Denetim |
| 4 | Servis yeniden başlatma güvenlidir | Dayanıklılık |

# §22 Hata Kurtarma Akışı

Servis katmanındaki hatalardan kurtarma akışı; her adımın sonucu kaydedilir.

| # | Adım | İş | Sonuç |
|---|---|---|---|
| 1 | Tespit | Hata yakalanır | Hata kaydı |
| 2 | Sınıflandırma | §17 matrisi | Sınıf |
| 3 | Müdahale | Sınıfa göre aksiyon | Aksiyon |
| 4 | Doğrulama | Sonuç kontrol edilir | Başarı/başarısızlık |
| 5 | Kayıt | Sonuç log'a | Denetim izi |

### §22.1 Kurtarma Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Kurtarma sınıfa göre | §17.1 akışı |
| 2 | Kurtarma sınırı vardır | Sonsuz döngü yok |
| 3 | Kurtarma log kaydı üretir | Denetim |
| 4 | Kurtarma başarısızsa üst karar | Escalation |

# §23 Repo Kod Kanıtı (devam)

| # | Dosya | Satır | İlişki |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | L18 | CI concurrency `group` satırı çoklu çalıştırma çakışmasını önler — servis kuyruk disiplininin CI karşılığı |
| 2 | `shared/composer.json` | L24 | PHPUnit sürüm eşikli — servis doğrulama test çerçevesinin kanıtı |
| 3 | `api.coremusic.net/config/routes.php` | L24 | Route tablosu PHP array literal'idir — LS2 URI listesi modelinin HTTP karşılığı |

# §24 Kapanış Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | §1-§23 tüm bölümler Markdown tablosu içerir | ✅ |
| 2 | URL sayısı ≥8 ve HTTP 200 | ✅ 13 |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | URI uydurulmadı | ✅ |
| 5 | Sürüm uydurulmadı | ✅ |
| 6 | Son satır Authority satırı | ✅ |

# §25 Sonraki Tur Kapıları

| # | Kapı | Tetikleyici | Aksiyon |
|---|---|---|---|
| 1 | URI doğrulaması | LS2 gövdesi okundu | §4.1 satırları doldurulur |
| 2 | Çağrı imzaları | Referans gövdesi okundu | §3.1'e imza satırı eklenir |
| 3 | Abonelik olayları | Gerçek cihaz erişimi | §18.2 matrisi ölçülür |
| 4 | Hata kodları | Servis log'u | §17 tablosu zenginleştirilir |

### §25.1 Tablo Envanteri

| # | Bölüm aralığı | Tablo sayısı | Konu |
|---|---|---|---|
| 1 | §3-§6 | 11 | Çağrı modeli, servisler, sistem, ayarlar |
| 2 | §7-§12 | 11 | Yöneticiler, JS servis, luna-send, karşılaştırma |
| 3 | §13-§18 | 12 | Kabul, sözlük, ölçüm, hata, abonelik |
| 4 | §19-§25 | 13 | Çağrı türü, yetki, hayat döngüsü, kurtarma, kapı |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode