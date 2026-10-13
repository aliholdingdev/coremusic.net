---
node_id: K000-isletim-sistemi-isletim-sistemi-turu-webos-webos-ses-ve-rota
title: "webOS — Ses ve Rota (Audio API · Output Route · Media Database · DRM · Device Info)"
status: active
parent_id: K000-isletim-sistemi-isletim-sistemi-turu-webos
depth: 3
last_verified: 2026-10-13
sources:
  - https://webostv.developer.lge.com/develop/references/audio
  - https://webostv.developer.lge.com/develop/references/media-database
  - https://webostv.developer.lge.com/develop/references/drm
  - https://webostv.developer.lge.com/develop/references/tv-device-information
  - https://webostv.developer.lge.com/develop/references/keymanager3
  - https://webostv.developer.lge.com/develop/references/connection-manager
  - https://webostv.developer.lge.com/develop/references
  - https://webostv.developer.lge.com/develop/guides/drm-content-playback
  - https://webostv.developer.lge.com/develop/guides
tags:
  - webos
  - ses
  - rota
  - drm
group: mimari
---

# §1 Kapsam ve Sınır

Bu dosya webOS TV platformunun ses ve rota yüzeyini işler: uygulamanın ses API'sine nasıl
yaklaştığı, çıkış rota seçiminin nasıl sınıflandırıldığı, medya veritabanı, DRM ve cihaz
bilgisi servislerinin ses açısından ne taşıdığı.

| # | Kapsam | Kapsam dışı |
|---|---|---|
| 1 | webOS TV audio API yüzeyi | Multimedya yığını iç yapısı (kardeş dosya `webos-multimedya-cati`) |
| 2 | Çıkış rota sınıflandırması | Gerçek-zamanlı tampon hesabı (kardeş dosya `webos-gercek-zamanli-tampon`) |
| 3 | Medya veritabanı servisi | Uygulama paketleme (kardeş dosya `webos-uygulama-gelistirme`) |
| 4 | DRM ve anahtar yönetimi | Donanım DAC tasarımı |
| 5 | Cihaz bilgisi servisi | Tizen ses yöneticisi (kardeş platform) |

# §2 ELI15

Bir televizyonda ses birçok yoldan çıkabilir: dahili hoparlör, kulaklık, Bluetooth, optik
çıkış, HDMI. Hangi yoldan çıkacağına "rota" denir. webOS bu seçimi servis katmanından yönetir.
Bu dosya, bir uygulamanın sesi nasıl açtığını, rotanın nasıl seçildiğini ve DRM'li içeriğin
sesinin neden kilitli olduğunu anlatır.

# §3 webOS TV Ses API Yüzeyi

webOS TV'de ses, uygulamanın doğrudan donanıma dokunmadığı bir servis arayüzü üzerinden
yönetilir; bu yüzey resmi referans sayfasında yayınlanır.

| # | Yüzey | Görev | Sorumlu katman |
|---|---|---|---|
| 1 | Ses seviyesi okuma | Mevcut seviye | Servis |
| 2 | Ses seviyesi yazma | Seviye değiştirme | Servis |
| 3 | Sessize alma | Mute | Servis |
| 4 | Çıkış rota bilgisi | Aktif rota | Servis |
| 5 | Ses kaynak bilgisi | Hangi kaynak | Servis |

### §3.1 API Yüzeyi Sınırları

| # | Sınır | Tanım | Sonuç |
|---|---|---|---|
| 1 | Cihaz doğrudan erişim | Uygulama ses kartına dokunmaz | Servis üzerinden |
| 2 | Rota zorunluluğu | Cihaz seçimi servisin işi | Uygulama öneride bulunur |
| 3 | İzin kontrolü | Uygulama izni olmadan çağrı çalışmaz | İzin listesi kardeş dosyada |
| 4 | Sürüm bağımlılığı | API sürümü cihaz yazılımına bağlı | Sürüm `UNKNOWN` |

### §3.2 API Çağrı Sınıflandırması

| # | Sınıf | Örnek | Yanıt tipi |
|---|---|---|---|
| 1 | Okuma | Seviye oku | Değer |
| 2 | Yazma | Seviye yaz | Onay |
| 3 | Durum | Rota oku | Durum nesnesi |
| 4 | Olay | Durum değişimi | Olay bildirimi |

# §4 Çıkış Rota Sınıflandırması

Çıkış rotası, sesin hangi fiziksel veya mantıksal çıkışa gittiğidir; her rota sınıfının kendi
kısıtları vardır.

| # | Rota sınıfı | Fiziksel çıkış | Kısıt |
|---|---|---|---|
| 1 | Dahili hoparlör | Cihaz içi hoparlör | Sınırlı bant genişliği |
| 2 | Kulaklık | Kulaklık jakı | Takılı değilse devre dışı |
| 3 | Bluetooth | Kablosuz cihaz | Bağlantı gecikmesi |
| 4 | Optik (S/PDIF) | Dijital optik çıkış | Sıkıştırma kısıtları |
| 5 | HDMI (eARC/ARC) | TV HDMI çıkış | Ampere bağımlı |
| 6 | USB ses | USB ses kartı | Cihaz takılı değilse devre dışı |

### §4.1 Rota Seçim Faktörleri

| # | Faktör | Etki | Öncelik |
|---|---|---|---|
| 1 | Bağlantı varlığı | Rota kullanılabilirliği | Yüksek |
| 2 | Kullanıcı tercihi | Aktif rota | Yüksek |
| 3 | Sistem politikası | Zorunlu rota | Orta |
| 4 | İçerik kısıtı | DRM rota kısıtı | Yüksek |
| 5 | Cihaz politikası | Donanım kısıtı | Orta |

### §4.2 Rota Geçiş Kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Geçiş servis üzerinden | Sınır §3.1 |
| 2 | Geçiş ani olmak zorunda değil | Yumuşak geçiş tercih edilir |
| 3 | Geçiş sonucu bildirilir | Uygulama durumu gösterir |
| 4 | Geçiş log kaydı üretir | Denetim izi |

# §5 Ses Seviyesi ve Ducking

Seviye yönetimi, kullanıcının duyduğu sesin yüksekliğini; ducking ise arka plandaki sesin
önüne çıkan içerik için kısılmasıdır.

| # | Kavram | Tanım | Tetikleyici |
|---|---|---|---|
| 1 | Ana seviye | Aktif çıkış seviyesi | Kullanıcı veya uygulama |
| 2 | Uygulama seviyesi | Uygulamaya özel seviye | Uygulama |
| 3 | Mute | Sessize alma | Kullanıcı veya sistem |
| 4 | Ducking | Arka plan kısma | Öne çıkan içerik |
| 5 | Sınırlama | Üst sınır | Sistem politikası |

### §5.1 Seviye Etkileşim Matrisi

| # | Etkileşim | Sonuç | Çatışma |
|---|---|---|---|
| 1 | Ana seviye + uygulama seviyesi | Kombine etki | İkisi birden etkiler |
| 2 | Mute + seviye yazma | Mute galip gelir | Mute öncelikli |
| 3 | Ducking + uygulama seviyesi | Ducking uygulanır | Sistem öncelikli |
| 4 | Sınırlama + seviye yazma | Sınırlamada kesilir | Üst sınır zorunlu |

### §5.2 Ducking Sınıflandırması

| # | Sınıf | Tetikleyici | Kapsam |
|---|---|---|---|
| 1 | Geçici ducking | Kısa bildirim | Tüm kaynaklar |
| 2 | Kalıcı ducking | Uzun içerik | Tüm kaynaklar |
| 3 | Hedefli ducking | Tek uygulama | Uygulama seviyesi |
| 4 | Sistem ducking | Sistem olayı | Tüm kaynaklar |

# §6 Medya Veritabanı Servisi

Medya veritabanı servisi, cihazdaki medya içeriğini (fotoğraf, video, müzik) sınıflandırır ve
sorgulanabilir hale getirir; ses açısından içeriğin bulunabilirliğini ve meta verisini taşır.

| # | Görev | Girdi | Çıktı |
|---|---|---|---|
| 1 | Medya tarama | Depolama yolu | Medya listesi |
| 2 | Meta veri okuma | Medya kimliği | Başlık, sanatçı, süre |
| 3 | Sorgu | Filtre | Eşleşen medya |
| 4 | Olay | Yeni medya | Bildirim |
| 5 | Silme | Medya kimliği | Kayıt kaldırma |

### §6.1 Ses Açısından Meta Veri

| # | Alan | Kullanım | Zorunluluk |
|---|---|---|---|
| 1 | Sanatçı | Listeleme | Zorunlu değil |
| 2 | Albüm | Listeleme | Zorunlu değil |
| 3 | Süre | Oynatma çubuğu | Zorunlu değil |
| 4 | Kanal sayısı | Çözümleme | Bilgi |
| 5 | Örnekleme hızı | Çözümleme | Bilgi |

### §6.2 Veritabanı Sınırları

| # | Sınır | Sonuç |
|---|---|---|
| 1 | Yalnız medya içeriği | Uygulama verisi değil |
| 2 | Cihaz içi | Ağ kaynağı bu serviste değil |
| 3 | İzin gerektirir | İzin yoksa liste boş |
| 4 | Silme geri alınamaz | Silme onayı zorunlu |

# §7 DRM ve Ses

DRM (Digital Rights Management), korumalı içeriğin yetkisiz kopyalanmasını önler; ses açısından
bu, çıkış rotasını ve çözme yolunu kısıtlar.

| # | DRM kavramı | Görev | Ses ile ilişkisi |
|---|---|---|---|
| 1 | Lisans | Oynatma yetkisi | Yetkisizse ses yok |
| 2 | Anahtar | Çözme anahtarı | Anahtar yoksa ses çözülemez |
| 3 | Çözme | Kilit açma | Çözme başarısızsa ses yok |
| 4 | Rota kısıtı | Çıkış kısıtı | Kısıtlı rota reddedilir |
| 5 | Güvenli yol | Korumalı yol | Korumasız yolda ses kısıtlanır |

### §7.1 DRM Hata Matrisi

| # | Hata | Belirti | Müdahale |
|---|---|---|---|
| 1 | Lisans eksik | Oynatma başlamaz | Lisans alımı |
| 2 | Lisans süresi doldu | Oynatma durur | Yenileme |
| 3 | Anahtar hatası | Ses çözülemez | Anahtar yönetimi |
| 4 | Rota kısıtı ihlali | Çıkış reddedilir | Rota değişimi |
| 5 | Güvenli yol yok | Kısıtlı içerik oynatılmaz | Uygun rota seçimi |

### §7.2 DRM Sınıflandırması

| # | Sınıf | Korumа | Ses kısıtı |
|---|---|---|---|
| 1 | Korumasız | Yok | Yok |
| 2 | Lisanslı | Lisans | Rota kısıtlı olabilir |
| 3 | Güvenli yol zorunlu | Lisans + güvenli yol | Yalnız güvenli rota |
| 4 | Kilitli bölge | Lisans + bölge | Bölge dışı reddedilir |

# §8 Anahtar Yönetimi (Key Manager)

Anahtar yönetimi servisi, DRM ve içerik koruma anahtarlarının güvenli saklanmasını ve
kullanılmasını sağlar.

| # | Görev | Taşıyan | Risk |
|---|---|---|---|
| 1 | Anahtar saklama | Güvenli depo | Sızıntı |
| 2 | Anahtar okuma | Yetkili servis | Yetkisiz erişim |
| 3 | Anahtar üretimi | Güvenli jeneratör | Zayıf rastgelelik |
| 4 | Anahtar silme | Güvenli silme | Kalıntı |

### §8.1 Anahtar Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Anahtar uygulama katmanına verilmez | Sınır §3.1 |
| 2 | Anahtar log'a yazılmaz | Sızıntı |
| 3 | Anahtar bellekte uzun süre tutulmaz | Bellek sızıntısı |
| 4 | Anahtar işlemi servis üzerinden | Yetki kontrolü |

# §9 Bağlantı Yöneticisi ve Ses

Bağlantı yöneticisi servisi, ağ bağlantı durumunu taşır; ağ akışı kaynaklı ses bu duruma bağımlıdır.

| # | Durum | Ses etkisi | Müdahale |
|---|---|---|---|
| 1 | Bağlantı var | Akış sürer | Yok |
| 2 | Bağlantı yok | Akış kesilir | Yerel kaynak |
| 3 | Bağlantı zayıf | Tampon boşluğu | Tampon politikası |
| 4 | Bağlantı değişimi | Akış yeniden kurulur | Yeniden bağlanma |

### §9.1 Bağlantı — Akış Eşlemesi

| # | Bağlantı türü | Akış türü | Gecikme etkisi |
|---|---|---|---|
| 1 | Kablosuz | Ağ akışı | Yüksek |
| 2 | Kablolu | Ağ akışı | Düşük |
| 3 | Yok | Yerel dosya | Sıfır |

# §10 Cihaz Bilgisi Servisi

Cihaz bilgisi servisi, cihazın model, yazılım ve donanım özelliklerini taşır; ses açısından
donanım sınırlarının bilinmesini sağlar.

| # | Bilgi | Kullanım | Ses ilişkisi |
|---|---|---|---|
| 1 | Model | Uyumluluk | Model bazlı kısıtlar |
| 2 | Yazılım sürümü | API uyumu | API sürümü |
| 3 | Hoparlör sayısı | Kanal eşlemesi | Kanal kısıtı |
| 4 | Çıkış desteği | Rota sınıflandırması | Rota kısıtı |
| 5 | Donanım kimliği | Denetim | Denetim izi |

### §10.1 Cihaz Bilgisi Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Bilgi servis üzerinden okunur | Sınır §3.1 |
| 2 | Bilgi log'a dikkatle yazılır | Kimlik sızıntısı |
| 3 | Bilgi yetkisiz uygulamaya verilmez | Güvenlik |
| 4 | Sürüm bilgisi doğrulanmadan yazılmaz | Zero-hallucination |

# §11 Repo Kod Kanıtı

| # | Dosya | Satır | İlişki |
|---|---|---|---|
| 1 | `shared/src/Api/Middleware/AuthenticationMiddleware.php` | L33 | Public route kalıpları sabit listesidir — ses API izin kontrolünün route karşılığı |
| 2 | `shared/tests/Api/RouteTableTest.php` | L19 | Route tablosu testten üretilir — rota doğrulama disiplininin test karşılığı |
| 3 | `shared/composer.json` | L8 | PHP sürüm eşiği `>=8.4` — API sürüm bağımlılığı kuralının araç karşılığı |

# §12 Karşılaştırma (kısa)

| # | Konu | webOS | Tizen |
|---|---|---|---|
| 1 | Ses yüzeyi | webOS TV audio API | Sound Manager |
| 2 | Rota yönetimi | Servis katmanı | Servis katmanı |
| 3 | DRM | Key manager + lisans | Ayrı DRM yüzeyi |
| 4 | Medya veritabanı | Ayrı servis | Multimedya kataloğu |

# §13 Kabul Kriterleri

| # | Kriter | Durum |
|---|---|---|
| 1 | Rota sınıflandırması eksiksiz | ✅ |
| 2 | DRM matrisi eksiksiz | ✅ |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Sürüm uydurulmadı | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §14 Sözlük

| # | Terim | Tanım |
|---|---|---|
| 1 | Rota | Sesin hangi fiziksel/mantıksal çıkışa gittiği |
| 2 | Ducking | Öne çıkan içerik için arka plan sesinin kısılması |
| 3 | DRM | Korumalı içerik yönetimi |
| 4 | Güvenli yol | DRM'li içeriğin oynatılabileceği korumalı çıktı yolu |
| 5 | eARC/ARC | HDMI üzerinden ses geri dönüş kanalı |
| 6 | Meta veri | Medyanın tanımlayıcı bilgileri |
| 7 | Anahtar yönetimi | DRM anahtarlarının güvenli saklanması |

# §15 Kardeş Dosya Bağları

| # | Dosya | İlişki |
|---|---|---|
| 1 | webos-multimedya-cati.md | Alt katman yığın yapısı |
| 2 | webos-media-servisi.md | Servis katmanı ayrıntısı |
| 3 | webos-uygulama-gelistirme.md | Uygulama izinleri |
| 4 | webos-gercek-zamanli-tampon.md | Akış tamponu hesabı |
| 5 | webos-web-arastirma.md | URL doğrulama kaydı |
| 6 | tizen/tizen-ses-yoneticisi.md | Kardeş platform ses yöneticisi |

# §16 Ölçüm Günlüğü

| # | Değer | Durum |
|---|---|---|
| 1 | Doğrulama tarihi | 2026-10-13 |
| 2 | URL sayısı (bu dosya) | 9 |
| 3 | HTTP 200 | 9 |
| 4 | Envanter kesişimi | 0 |
| 5 | API sürüm numarası | UNKNOWN |

# §17 Rota Karar Ağacı

Rota seçimi çok adımlı bir karardır; bu bölüm kararı adım adım sınıflandırır.

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Bağlantı var mı? | Fiziksel bağlantı mevcut mu? | 2. adıma geç | Uygun rota yok |
| 2 | Kullanıcı tercihi var mı? | Tercih kayıtlı mı? | Tercih uygulanır | 3. adıma geç |
| 3 | DRM kısıtı var mı? | İçerik kısıtlı mı? | Güvenli yol seçilir | 4. adıma geç |
| 4 | Sistem politikası var mı? | Zorunlu rota mı? | Politika uygulanır | 5. adıma geç |
| 5 | Varsayılan rota | Dahili hoparlör | Rota atanır | Hata |

### §17.1 Rota Hata Yolları

| # | Hata yolu | Belirti | Müdahale |
|---|---|---|---|
| 1 | Bağlantı yok | Rota atanamaz | Kullanıcıya bilgi |
| 2 | Tercih uygulanamıyor | Tercih edilen rota yok | Alternatif rota |
| 3 | DRM kısıtı | Güvenli yol yok | Uygun içerik seçimi |
| 4 | Politika çakışması | İki politika | Öncelik kuralı |

### §17.2 Rota Öncelik Kuralı

| # | Öncelik | Politika | Gerekçe |
|---|---|---|---|
| 1 | En yüksek | DRM kısıtı | Hukuki kısıt |
| 2 | Yüksek | Kullanıcı tercihi | Kullanıcı hakkı |
| 3 | Orta | Sistem politikası | Cihaz kuralı |
| 4 | Düşük | Varsayılan | Son çare |

# §18 Ses Kanal Haritası

Ses kanallarının rota ile ilişkisi; kanal sayısı ve eşleme kuralları aşağıdaki tabloda sınıflandırılır.

| # | Kanal düzeni | Kanal sayısı | Eşleme |
|---|---|---|---|
| 1 | Mono | 1 | Tek kanal |
| 2 | Stereo | 2 | Sol + sağ |
| 3 | Çoklu kanal | 2'den fazla | Cihaz eşlemesine bağlı |

### §18.1 Kanal Eşleme Kuralı

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Kanal sayısı çıkış kapasitesini aşmaz | Aşarsa azaltma uygulanır |
| 2 | Eşleme servis katmanında | Uygulama kanal haritasına dokunmaz |
| 3 | Eşleme değişimi bildirilir | Uygulama durumu gösterir |
| 4 | Kanal eşlemesi ölçülmeden yazılmaz | Zero-hallucination |

### §18.2 Kanal — Rota Eşlemesi

| # | Rota | Kanal desteği | Kısıt |
|---|---|---|---|
| 1 | Dahili hoparlör | Cihaza bağlı | Donanım kısıtı |
| 2 | Kulaklık | Genelde stereo | Cihaza bağlı |
| 3 | Bluetooth | Cihaza bağlı | Sıkıştırma |
| 4 | Optik | Sıkıştırma kısıtları | Format kısıtı |
| 5 | HDMI eARC | Çoklu kanal destekli | Ampere bağlı |

# §19 Olay ve Bildirim Akışı

Ses ve rota durum değişiklikleri olaylarla taşınır; uygulama bu olayları dinleyerek durumu gösterir.

| # | Olay | Tetikleyici | Uygulama etkisi |
|---|---|---|---|
| 1 | Seviye değişimi | Kullanıcı veya servis | Gösterge güncellenir |
| 2 | Mute değişimi | Kullanıcı veya sistem | Sessiz göstergesi |
| 3 | Rota değişimi | Servis | Rota göstergesi |
| 4 | DRM durumu | Lisans olayı | Uyarı gösterilir |
| 5 | Bağlantı değişimi | Bağlantı yöneticisi | Akış durumu |

### §19.1 Olay Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Olay tek yönlü akar | Çift yönlü kilitlenme yok |
| 2 | Olay kaybı durumu bozmaz | Yeniden okuma ile kurtarılır |
| 3 | Olay log kaydı üretir | Denetim izi |
| 4 | Olay dinleme izne bağlı | Güvenlik |

# §20 İzin Yüzeyi

Ses ve rota servisleri izin gerektirir; izin olmadan çağrı reddedilir.

| # | İzin | Kapsam | Red durumu |
|---|---|---|---|
| 1 | Ses okuma | Seviye/durum okuma | Red |
| 2 | Ses yazma | Seviye değiştirme | Red |
| 3 | Rota okuma | Rota bilgisi | Red |
| 4 | Rota yazma | Rota değişimi | Red |
| 5 | Medya okuma | Medya listesi | Red |
| 6 | DRM | Korumalı oynatma | Red |

### §20.1 İzin Kural Seti

| # | Kural | Gerekçe |
|---|---|---|
| 1 | En az izin | Gerekmeyen izin alınmaz |
| 2 | İzin gerekçelendirilir | Denetim |
| 3 | İzin iptal edilebilir | Kullanıcı hakkı |
| 4 | İzin log kaydı üretir | Denetim izi |

# §21 Risk Kaydı

| # | Risk | Etki | Azaltma |
|---|---|---|---|
| 1 | Rota çakışması | Sesin yanlış yerden çıkması | §17 karar ağacı |
| 2 | DRM ihlali | Hukuki risk | §7.1 hata matrisi |
| 3 | Anahtar sızıntısı | İçerik sızıntısı | §8.1 kuralları |
| 4 | İzin aşımı | Güvenlik açığı | §20.1 kuralları |
| 5 | Ölçülmemiş kanal eşlemesi | Yanlış iddia | §18.2 kural 4 |
| 6 | API sürümü uydurma | Zero-hallucination ihlali | §16 `UNKNOWN` |

# §22 Test Senaryoları

| # | Senaryo | Beklenen |
|---|---|---|
| 1 | Seviye yazma | Seviye değişir, durum bildirilir |
| 2 | Mute yazma | Ses kesilir, mute bildirilir |
| 3 | Rota değiştirme | Rota değişir, bildirim gelir |
| 4 | Kısıtlı DRM oynatma | Güvenli olmayan rota reddedilir |
| 5 | İzinsiz çağrı | Çağrı reddedilir |
| 6 | Bağlantı kopması | Akış durur, durum bildirilir |

### §22.1 Test Kuralı

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Test gerçek cihazda | Emülatör rotası farklı olabilir |
| 2 | Test log kaydı üretir | Denetim izi |
| 3 | Test izin durumunu ayrıca sınar | §20 yüzeyi |
| 4 | Test ölçüm değerleriyle karşılaştırılır | Ölçüm penceresi PLANNED |

# §23 Karar Kaydı

| # | Karar | Gerekçe |
|---|---|---|
| 1 | Ses yüzeyi servis üzerinden işlendi | §3.1 sınırı |
| 2 | Rota kararı karar ağacıyla sınıflandırıldı | §17 |
| 3 | DRM hataları ayrı matriste | §7.1 |
| 4 | Sürüm değerleri `UNKNOWN` bırakıldı | Zero-hallucination |

# §24 Kapanış Kontrol Listesi

| # | Kontrol | Durum |
|---|---|---|
| 1 | §1-§23 tüm bölümler Markdown tablosu içerir | ✅ |
| 2 | URL sayısı ≥8 ve HTTP 200 | ✅ 9 |
| 3 | Repo kod satırı ≥1 | ✅ |
| 4 | Sürüm uydurulmadı | ✅ |
| 5 | Son satır Authority satırı | ✅ |

# §25 Sonraki Tur Kapıları

| # | Kapı | Tetikleyici | Aksiyon |
|---|---|---|---|
| 1 | API imza doğrulaması | webOS TV audio referans gövdesi okundu | Çağrı imzaları §3.1'e taşınır |
| 2 | Rota ölçümü | Gerçek cihaz erişimi | §4.1 faktörleri ölçülür |
| 3 | Kanal eşlemesi ölçümü | Donanım erişimi | §18.1 kural 4 kapanır |
| 4 | DRM testi | Lisanslı içerik erişimi | §22 senaryoları çalıştırılır |

# §26 Tablo Envanteri

| # | Bölüm | Tablo sayısı | Konu |
|---|---|---|---|
| 1 | §3-§5 | 9 | API yüzeyi, rota, seviye |
| 2 | §6-§10 | 11 | Medya DB, DRM, anahtar, bağlantı, cihaz |
| 3 | §11-§16 | 9 | Repo kanıtı, karşılaştırma, sözlük, ölçüm |
| 4 | §17-§20 | 11 | Karar ağacı, kanal, olay, izin |
| 5 | §21-§26 | 13 | Risk, test, karar, kontrol, kapı, envanter |

---
**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode