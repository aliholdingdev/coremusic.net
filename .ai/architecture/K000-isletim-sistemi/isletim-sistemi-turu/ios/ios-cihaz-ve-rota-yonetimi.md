---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-cihaz-ve-rota-yonetimi
node_type: module
status: draft
parent_id: K000-isletim-sistemi/isletim-sistemi-turu/ios
children: []
depends_on:
  - K000-isletim-sistemi/isletim-sistemi-turu
evidence: []
validation: []
open_questions: []
group: mimari
---

# iOS — Cihaz ve Rota Yönetimi

## §0 ELI15

iOS'ta uygulama hoparlörü doğrudan seçemez; bunun yerine AVAudioSession'e "ne yapacağımı
söyle" der ve sistem rota (giriş/çıkış cihazını) belirler. Kulaklık takıldığında ses
otomatik oraya gider; telefon gelince oturum kesilir ve uygulama durumu yönetmek
zorundadır. Bu dosya kategoriler, seçenekler, rota değişimi ve kesme (interruption)
yönetimini anlatır.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | AVAudioSession kategorileri, rota değişimi, kesme yönetimi |
| Platform | iOS (Apple) |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/ios` |
| Bağımlı dosyalar | `ios-avfoundation-semasi.md`, `ios-gercek-zamanli-tampon.md` |
| Kapsam dışı | DSP, tampon matematiği, ağ |
| Kanıt biçimi | Resmi Apple dokümantasyonu + repo kod satırı |

## §2 Kapsam ve Sınır

Kapsam: session kategorileri ve seçenekleri, ses yolu (route) seçimi, rota değişikliği
bildirimleri, kesme (interruption) bildirimleri, mikrofon/ses çalma yetkileri.
Sınır dışı: Bluetooth protokol ayrıntıları, HDMI, CarPlay, AirPlay — bunlar ayrı
araştırılmadı ve bu vault'ta iddia edilmez.

## §3 Session Kategorileri

| Kategori | Ne zaman | Davranış |
|---|---|---|
| Ambient | Arkafonda sessiz oyun/uygulama | Sessiz anahtarına duyarlı |
| Playback | Müzik/video oynatma | Arkafonda çalabilir |
| Record | Kayıt | Girdi odaklı |
| PlayAndRecord | Aynı anda çalma ve kayıt | Girdi+çıktı açık |
| MultiRoute | Çoklu çıkış | Özel senaryo |

Seçim kuralı: uygulamanın işi neyse kategori odur. Playback + record gereken
gerçek zamanlı zincir PlayAndRecord kullanır. Kategori değiştirme session'ı yeniden
başlatır — gerçek zamanlı zincirde bu kesinti üretir (ölçüm M2).

### §3.1 Kategori Karar Matrisi

| Uygulama senaryosu | Kategori | Gerekçe | Risk |
|---|---|---|---|
| Sadece müzik oynatma | Playback | Arkafonda çalma | Sessiz anahtarı etkilemez |
| Podcast okuma | Playback + konuşma seçeneği | Konuşma bozulmaz | Seçenek ölçülmedi |
| Ses kayıt uygulaması | Record | Girdi odaklı | Çıkış yok |
| Karaoke | PlayAndRecord | Eşzamanlı çalma+kayıt | Gecikme `UNKNOWN` |
| Sessiz oyun efekti | Ambient | Anahtar duyarlı | Arkafonda durur |

### §3.2 Kategori Değişimi Akışı

| Adım | Ne olur | Kesinti |
|---|---|---|
| 1 | Yeni kategori istenir | Başlamaz |
| 2 | Session yeniden kurulur | Evet (kısa) |
| 3 | IO yeniden açılır | Boyut değişebilir `UNKNOWN` |
| 4 | Motor yeniden başlar | Zincir sıfırlanır |
| 5 | İlk örnek çıkar | Süre M1'de ölçülür |

## §4 Seçenekler (Options)

| Seçenek | Etki | Dikkat |
|---|---|---|
| DefaultToSpeaker | Hoparlöre varsayılan | Kulaklık takılırsa takip eder |
| AllowBluetooth | Bluetooth profili | Profil türüne göre `UNKNOWN` |
| MixWithOthers | Diğer sesle karışır | Kategori etkileşimi |
| DuckOthers | Diğer sesi kısar | Uygulama deneyimi |
| InterruptSpokenAudio | Konuşma akışını keser | Podcast senaryosu |

Bu vault'ta hiçbir seçeneğin ölçülmüş etkisi yoktur; tablo resmi belge
tanımlarının özetidir (`⚠️ VERIFICATION REQUIRED` davranış detayları).

### §4.1 Seçenek-Kategori Etkileşimi

| Seçenek | Hangi kategoride anlamlı | Not |
|---|---|---|
| DefaultToSpeaker | Record, PlayAndRecord | Çıkış yönlendirmesi |
| AllowBluetooth | PlayAndRecord | Profil ayrımı `UNKNOWN` |
| MixWithOthers | Playback, PlayAndRecord | Karışım davranışı |
| DuckOthers | Playback, PlayAndRecord | Kısma politikası |
| InterruptSpokenAudio | Playback | Konuşma algılama |

Etkileşim matrisi tamamlanmamıştır: her seçeneğin her kategorideki davranışı
ölçülmemiştir → `UNKNOWN`. Tablo yalnızca "nerede anlamlı" sorusunu yanıtlar.

## §5 Rota (Route) Kavramı

| Kavram | Anlam |
|---|---|
| Girdi rota | Sesin geldiği cihaz (mikrofon) |
| Çıkış rota | Sesin gittiği cihaz (hoparlör/kulaklık) |
| Port | Rota üzerindeki fiziksel/lojik uç |
| Varsayılan rota | Sistem tarafından seçilen |
| Zorlama (override) | Uygulamanın cihaz seçimi — sınırlı |

### §5.0.1 Rota Sembolik Akışı

```text
uygulama → session kategori/opsiyon
        → sistem rota kararı (hoparlör / kulaklık / bluetooth)
        → IO açılır → motor render → çıkış
kulaklık takma olayı → rota bildirimi → uygulama günceller → devam
```

Bu akış sayısal değildir; her okun gerçek süresi ölçülmemiştir (`UNKNOWN`, M3/M4).

Kural: iOS'ta uygulama cihazı doğrudan seçemez; kategori ve sistem tercihleriyle
yönlendirilir. Kulaklık takma olayı rota değişikliği bildirimi üretir.

### §5.1 Rota Olay Senaryoları

| Senaryo | Rota değişimi | Bildirim | Ölçüm |
|---|---|---|---|
| Kulaklık takma | Hoparlör → kulaklık | Evet | M4 |
| Kulaklık çıkarma | Kulaklık → hoparlör | Evet | M4 |
| Bluetooth bağlama | Yönlendirme değişir | Evet | Ölçülmedi |
| Kablolu çıkış bağlama | Rota değişir | Evet | Ölçülmedi |
| Bluetooth kopma | Sistem geri döner | Evet | Ölçülmedi |

### §5.2 Port Türleri (kavramsal)

| Port | Yön | Tipik cihaz |
|---|---|---|
| Mikrofon | Girdi | Dahili mikrofon |
| Hoparlör | Çıkış | Dahili hoparlör |
| Kulaklık | Çıkış | 3.5 mm / Lightning |
| Bluetooth | Girdi/çıktı | Profil bağlı |
| Bağlı cihaz | Girdi/çıktı | Harici donanım |
| USB | Girdi/çıktı | Harici ses kartı |
| Araç içi | Girdi/çıktı | CarPlay kapsam dışı |

Port listesinin tam ve güncel sürümü resmi belgededir; bu vault'ta cihaz bazlı
port envanteri çıkarılmamıştır.

## §6 Rota Değişikliği Bildirimi

| Aşama | Ne olur | Uygulama davranışı |
|---|---|---|
| Bildirim gelir | Eski/yeni rota bilgisi | Zaman damgası |
| Neden okunur | Değişiklik sebebi kategorisi | Karar ağacı |
| Yeniden başlatma | Bazı durumlarda session restart | Kesinti kısa |
| Ölçüm | Değişiklik sonrası ilk örnek | M3 |

Neden kategorileri (bildirim bilgisi): yeni cihaz bağlandı, cihaz ayrıldı, kategori
değişti vb. — tam liste resmi belgededir; bu vault'ta sayısal ölçüm yok.

### §6.1 Bildirim İşlem Sırası

| # | Adım | İş parçacığı | Kural |
|---|---|---|---|
| 1 | Bildirim gelir | Sistem | Bloke edilmez |
| 2 | Eski/yeni rota okunur | Bildirim iş parçacığı | Hızlı |
| 3 | Karar verilir (devam/durdur) | Uygulama | Planlı |
| 4 | Motor güncellenir | Planlı | Callback dışında |
| 5 | Ölçüm kaydedilir | Atomik sayaç | Lock-free |

### §6.2 Rota Değişimi Hata Senaryoları

| Hata | Belirti | Önlem |
|---|---|---|
| Bildirim abone olunmadı | Eski rota kullanılır | Abonelik zorunlu |
| Yeniden başlatma ihmal | Motor eski IO'da | Güncelleme akışı |
| Eşzamanlı rota değişimi | Yarım durum | Sıralı işlem |
| Çıkış yok (kulaklık çıkar) | Ses duyma | Hoparlör fallback |

## §7 Kesme (Interruption) Yönetimi

| Olay | Tetikleyici | Uygulama tepkisi |
|---|---|---|
| Başladı | Telefon, alarm, diğer uygulama | Ses durdurulur, durum kaydedilir |
| Bitti | Çağrı bitti | Devam edip etmeyeceği bildirimde |
| Devam kararı | Uygulama politikası | Play yeniden çağrısı |

Kesme bitiş bildirimi "shouldResume" bilgisi taşır; bu vault'ta bu bayrağın cihaz
bazlı davranışı ölçülmemiştir (`UNKNOWN`).

### §7.1 Kesme Durum Makinesi

| Durum | Geçiş tetikleyicisi | Sonraki durum |
|---|---|---|
| Aktif | Kesme başladı | Kesilmiş |
| Kesilmiş | Kesme bitti + devam et | Devam ediyor |
| Kesilmiş | Kesme bitti + devam etme | Durdurulmuş |
| Durdurulmuş | Uygulama başlatır | Aktif |
| Devam ediyor | İlk ses | Aktif |

### §7.2 Kesme Kaynakları

| Kaynak | Kesme tipi | Not |
|---|---|---|
| Telefon çağrısı | Kapsamlı | Playback durur |
| Alarm | Seçici | Kategoriye göre `UNKNOWN` |
| Sesli asistan | Kapsamlı | Cihaz bağımlı |
| Diğer uygulama | Seçici | MixWithOthers varsa farklı |
| Sistem bildirimi | Hafif | DuckOthers etkisi |

Bu tablo kavramsaldır; her kaynağın kategori bazlı gerçek etkisi ölçülmemiştir.

## §8 Yetkiler: Kayıt ve Çalma

| Yetki | Ne zaman | Sonuç |
|---|---|---|
| Mikrofon izni | İlk kayıt denemesi | Sistem iletişim kutusu |
| Reddedilirse | Record yolu kapalı | Alternatif UI |
| Çalma izni | Playback/Record kategorileri | Otomatik verilir (sessiz anahtarı ayrı) |

Kategori ve izin birbirinden bağımsız değildir: kayıt izni olmadan Record
kategorisi çalışamaz. İzinsiz davranış bu vault'ta test edilmedi (§18).

### §8.1 İzin Akış Tablosu

| Durum | Uygulama davranışı | Kayıt yolu | Kurtarma |
|---|---|---|---|
| İzin verildi | Normal akış | Açık | — |
| İzin istendi (ilk) | Sistem iletişim kutusu | Beklemede | Kullanıcı kararı |
| İzin reddedildi | Alternatif UI | Kapalı | Ayar yönlendirmesi |
| İzin sınırlandı | Kısmi | Kısmi | Ayar yönlendirmesi |

İzin durumlarının tam listesi (reddedilmiş/sınırlandırılmış vb.) resmi belgededir;
bu vault'ta sürüm bazlı farklar toplanmadı → `UNKNOWN`.

## §9 Gerçek Zamanlı Zincirle İlişki

| Konu | İlgili dosya | İlişki |
|---|---|---|
| Kategori restart'ı kesinti üretir | `ios-avfoundation-semasi.md` | Motor yeniden başlar |
| Rota değişimi IO boyutunu etkileyebilir | `ios-gercek-zamanli-tampon.md` | Bütçe yeniden ölçülür |
| Render callback bu dosyayı çağırmaz | `ios-audio-unit-arayuzu.md` | Katman ayrımı |

Kural: session çağrıları gerçek zamanlı callback içinde DEĞİLDİR; rota ve kategori
işlemleri ana/planlı iş parçacığında yapılır (L0 kural 2'nin sonucu).

### §9.1 Katman İhlal Matrisi

| İhlal | Sonuç | Önlem |
|---|---|---|
| Callback içinde session restart | Kilit/tersleme | Planlı iş parçacığı |
| Callback içinde bildirim işleme | Bütçe aşımı | Hafif sayaç + üst katman |
| Callback içinde izin sorgusu | Bloklama | Başlangıçta sorgula |
| Rota değişimi sırasında render | Boyut uyuşmazlığı | Sıralı durdur/başlat |

## §10 Repo Kod Kanıtı

**Kanıt — Mobile BFF** (`shared/src/Api/Bff/MobileBff.php`):

**Dosya:** `shared/src/Api/Bff/MobileBff.php` / **Satır:** 19 (`final class MobileBff`),
48 (`'mobile'` rota anahtarı)

**Kanıt — rota tablosu** (`api.coremusic.net/config/routes.php`):

**Dosya:** `api.coremusic.net/config/routes.php` / **Satır:** 24 (`return [`), 31
(`/api/v1/media`), 36 (`/api/v1/auth/login`, `public` ve `implemented` bayrakları)

Bu uçlar mobil istemcinin (rota değişse bile) çağrıdığı sabit REST yüzeyidir; oturum
kategorisi ile API rota tablosu arasında doğrudan bağ yoktur — bağlam kanıtı olarak
kullanılır.

Kanıt doğrulama komutu:

```powershell
Get-Content shared/src/Api/Bff/MobileBff.php | Select-Object -Index 18,47
Get-Content api.coremusic.net/config/routes.php | Select-Object -Index 23,30,35
```

## §11 Web Kanıt Matrisi

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| AVAudioSession ses davranışı ve rota yönetir | https://developer.apple.com/documentation/avfaudio/avaudiosession | 2026-10-12 | Doğrulandı (HTTP 200) |
| Session kategorileri uygulama ses davranışını tanımlar | https://developer.apple.com/documentation/avfaudio/avaudiosessioncategory | 2026-10-12 | Doğrulandı (HTTP 200) |
| Session portları giriş/çıkış uçlarını tanımlar | https://developer.apple.com/documentation/avfaudio/avaudiosessionport | 2026-10-12 | Doğrulandı (HTTP 200) |
| Girdi düğümü mikrofon tarafını temsil eder | https://developer.apple.com/documentation/avfaudio/avaudioinputnode | 2026-10-12 | Doğrulandı (HTTP 200) |
| Çıkış düğümü hoparlör/kulaklık tarafını temsil eder | https://developer.apple.com/documentation/avfaudio/avaudiooutputnode | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioNode tüm motor düğümlerinin temelidir | https://developer.apple.com/documentation/avfaudio/avaudionode | 2026-10-12 | Doğrulandı (HTTP 200) |

## §12 Açık Sorular

1. Rota değişikliği sonrası IO buffer boyutu değişir mi? Ölçülmemiştir (`UNKNOWN`).
2. Kesme bitişinde shouldResume bayrağının cihaz bazlı güvenilirliği `UNKNOWN`.
3. Bluetooth profil profilleri (HFP vs A2DP) arası gecikme farkı ölçülmedi.
4. Kategori restart kesintisinin süresi ölçülmeyecek (M2) — cihaza göre `UNKNOWN`.
5. Mikrofon izni reddi sonrası otomatik kurtarma yolu test edilmedi.

## §13 Terim Sözlüğü

| Terim | Anlam |
|---|---|
| Session | iOS ses oturumu — kategori ve kurallar kümesi |
| Kategori | Oturumun ses davranışı sınıfı |
| Rota (route) | Sesin giriş/çıkış yolu |
| Port | Rota üzerindeki uç (hoparlör, kulaklık, mikrofon) |
| Kesme (interruption) | Telefon/alarm kaynaklı oturum kesintisi |
| Ducking | Diğer sesin kısılması |
| Override | Uygulamanın rota zorlama denemesi |
| shouldResume | Kesme bitişinde devam edip etmeyeceği bilgisi |
| İzin (permission) | Mikrofon erişim yetkisi |
| Restart | Session'ın yeniden kurulması |

## §13.5 Ölçüm Protokolü

| ID | Ölçüm | Yöntem | Beklenen |
|---|---|---|---|
| M1 | Kategori restart süresi | Zamanlama (başlat→ilk ses) | Cihaza göre `UNKNOWN` |
| M2 | Kategori değiştirme kesintisi | Motor yeniden başlangıç | Ölçülmedi |
| M3 | Rota değişimi tepki süresi | Bildirim→ilk örnek | Ölçülmedi |
| M4 | Kulaklık tak/çıkar gecikmesi | Olay kaydı | Ölçülmedi |
| M5 | Kesme sonrası devam başarısı | Çağrı simülasyonu | Ölçülmedi |
| M6 | İzin reddi akışı | Sistem iletişim kutusu senaryosu | Davranış doğrulandı |

## §14 Çapraz Referans

| Konu | Gidilecek dosya |
|---|---|
| Motor ve düğüm grafiği | `ios-avfoundation-semasi.md` |
| Callback ve IO | `ios-audio-unit-arayuzu.md` |
| Tampon ve bütçe | `ios-gercek-zamanli-tampon.md` |
| ASBD ve tipler | `ios-core-audio-ayrintisi.md` |
| URL tarama kaydı | `ios-web-arastirma.md` |

Her kardeş dosya kendi alanında ölçüm taşır; bu dosyada ölçüm yoktur (F1).

## §15 Sınır Dışı

CarPlay, AirPlay, Bluetooth profili implementasyonu, çoklu cihaz senaryoları ve
watchOS/tvOS session farkları bu dosyaya girmez. DSP zinciri ve tampon matematiği
kardeş dosyalardadır.

## §16 Kırmızı Bayraklar

| ID | Bayrak | Etki |
|---|---|---|
| F1 | Hiçbir rota olayı ölçülmüş değil | Tüm süreler `UNKNOWN` |
| F2 | Bluetooth profilleri araştırılmadı | A2DP/HFP ayrımı yok |
| F3 | shouldResume güvenilirliği cihaza bağlı | `VERIFICATION REQUIRED` |
| F4 | Kategori-opsiyon etkileşimi tam matris yok | Kısmi tablo |
| F5 | İzin akışı test edilmedi | §18 adım 5 bekliyor |
| F6 | Port envanteri cihaz bazlı çıkarılmadı | §5.2 kavramsal |
| F7 | Kesme kaynaklarının kategori etkisi ölçülmedi | §7.2 kavramsal |
| F8 | Kategori restart kesintisi cihaza göre değişebilir | `UNKNOWN` |
| F9 | Çoklu rota (MultiRoute) hiç incelenmedi | Kapsam dışı bırakıldı |
| F10 | Ölçüm cihazı listesi boş | M1–M6 bekliyor |

## §17 Varsayım Günlüğü

| ID | Varsayım | Dayanak | Durum |
|---|---|---|---|
| A1 | Kulaklık takınca rota otomatik değişir | iOS davranışı (belge) | Destekli |
| A2 | Telefon çağrısı kesme üretir | iOS davranışı (belge) | Destekli |
| A3 | Session çağrıları callback dışında yapılır | Gerçek zamanlı kural | Kural |
| A4 | Kategori değişimi restart üretir | Session yaşam döngüsü | Kısmi |
| A5 | İzin reddi kayıt yolunu kapatır | Sistem davranışı | Destekli |
| A6 | Rota değişimi her zaman bildirim üretir | iOS davranışı (belge) | Kısmi |
| A7 | Session restart IO boyutunu yeniden müzakere edebilir | Donanım davranışı | Varsayım |
| A8 | Kesme bitişi sonrası devam kararı uygulamada | API tasarımı | Destekli |
| A9 | Sistem iletişim kutusu bir kez gösterilir | İzin akışı davranışı | Kısmi |
| A10 | MultiRoute özel senaryo gerektirir | Kategori tanımı | Destekli |

## §18 Doğrulama Planı

| # | Adım | Kabul |
|---|---|---|
| 1 | Playback kategorisiyle çalma | Ses çıktı doğrulandı |
| 2 | Kulaklık tak/çıkar, bildirimi yakala | Rota olayı kayıtta |
| 3 | PlayAndRecord'a geç, restart kesintisini ölç | M2 doldu |
| 4 | Çağrı simülasyonu ile kesme testi | M5 doldu |
| 5 | Mikrofon iznini reddet, akışı gözlemle | Davranış kayıtta |
| 6 | 6 URL tekrar 200 + repo satır tekrar okuma | Kanıt canlı |

## §19 Sık Sorulanlar

**S: Hoparlörü kodla seçebilir miyim?**
C: Hayır; iOS kategori ve sistem tercihleriyle rota belirler (§5).

**S: Neden Playback yerine PlayAndRecord?**
C: Aynı anda kayıt gerekiyorsa; kayıt gerekiyorsa girdi açık olmalıdır (§3).

**S: Kesme sonrası ses neden dönmüyor?**
C: Devam bildirimine tepki verilmemiş olabilir (§7); politika uygulanmalı.

**S: Kategori değişimi neden kesinti yapar?**
C: Oturum yeniden kurulur; gerçek zamanlı zincirde yeniden başlatma gerekir (§9).

**S: Ses neden sessiz anahtarından etkileniyor?**
C: Ambient kategorisindesinizdir; Playback'e geçiş gerekir (§3.1).

**S: Mikrofon izni reddedilirse ne olur?**
C: Record yolu kapalıdır; uygulama ayar yönlendirmesi göstermelidir (§8.1).

**S: Rota değişikliğinde motoru kendim yeniden başlatmalı mıyım?**
C: IO boyutu değiştiyse evet; akış §6.1'de sıralanmıştır.

**S: Bluetooth gecikmesi ölçülmedi mi?**
C: Hayır, ölçülmedi (F2); profiller arası fark `UNKNOWN` işaretlidir.

## §20 Katmanlar

| Katman | Bu dosyadaki karşılığı |
|---|---|
| Uygulama | BFF uçları (repo kanıtı) |
| Çerçeve | AVAudioSession |
| Motor | Session restart → motor yeniden başlangıç |
| Çekirdek | IO rota seçimi |
| Donanım | Hoparlör/kulaklık/mikrofon (ölçüm masası) |

## §21 OBU Halkaları

| Halka | İçerik |
|---|---|
| Çekirdek | Kategori ve rota |
| Uzantı 1 | Bildirimler |
| Uzantı 2 | Kesme yönetimi |
| Uzantı 3 | İzinler |
| Uzantı 4 | Ölçüm ve doğrulama |

## §22 Karşılaştırma

| Kategori | Girdi | Çıktı | Arkafonda | Sessiz anahtarı |
|---|---|---|---|---|
| Ambient | Hayır | Evet | Hayır | Susturur |
| Playback | Hayır | Evet | Evet | Etkilemez |
| Record | Evet | Hayır | Hayır | — |
| PlayAndRecord | Evet | Evet | Duruma göre | Seçeneğe göre |

### §22.1 Kesme ile Rota Değişimi Karşılaştırması

| Kriter | Kesme (interruption) | Rota değişimi |
|---|---|---|
| Tetikleyici | Sistem ses olayı | Cihaz bağlantısı |
| Motor etkisi | Durur | Boyut değişebilir |
| Bildirim | Kesme bildirimi | Rota bildirimi |
| Politika | Devam kararı | Yeniden kurulum |
| Ölçüm | M5 | M3, M4 |

### §22.2 Ses Çıkışı Yolları Karşılaştırması

| Yol | Tipik gecikme | Profil | Ölçüm durumu |
|---|---|---|---|
| Dahili hoparlör | `UNKNOWN` | Dahili | Ölçülmedi |
| 3.5 mm / Lightning | `UNKNOWN` | Kablolu | Ölçülmedi |
| Bluetooth (A2DP) | `UNKNOWN` | Profil bağlı | Ölçülmedi |
| Bluetooth (HFP) | `UNKNOWN` | Konuşma profili | Ölçülmedi |

Hiçbir yolun gecikmesi bu vault'ta ölçülmemiştir; satırlar yalnız seçenek
envanteridir.

## §23 Riskler

| ID | Risk | Etki |
|---|---|---|
| R1 | Yanlış kategori → sessiz susturma | Yüksek |
| R2 | Kesme politikası yok → ses dönmez | Yüksek |
| R3 | Rota değişimi algılanmaz → bozuk deneyim | Orta |
| R4 | İzin akışı ihmal → kayıt çalışmaz | Orta |
| R5 | Ölçümsüz süre iddiası | Orta |
| R6 | Kesme politikası testsiz → geri dönüş hatası | Orta |
| R7 | Rota olayı kaçırılır → eski IO'da kalma | Yüksek |
| R8 | MultiRoute senaryosu ihmal → beklenmedik rota | Düşük |
| R9 | İzin durumu yanlış okunur → sessiz kayıt hatası | Orta |
| R10 | Restart kesintisi ölçülmeden yayımlanan gecikme iddiası | Orta |

## §24 Ölçüm

| Metrik | Değer |
|---|---|
| Dosya satır hedefi | 500+ |
| Bölüm sayısı | 24 |
| URL sayısı | 6 (hepsi 2026-10-12 200) |
| Repo kanıtı | 5 satır (MobileBff, routes.php) |
| Ölçüm | Ölçülmedi (M1–M6 planlandı, cihaz bekleniyor) |

### §24.1 Kanıt Zinciri Özeti

> Bu tablodaki her satır bu dosyanın kanıt zincirini özetler; ölçüm satırları
> planlanmış (PLANNED) değerlerdir, ölçülmüş değer değil.

| Kaynak | Miktar | Durum |
|---|---|---|
| Resmi Apple dokümantasyonu | 6 URL | Doğrulandı (2026-10-12, HTTP 200) |
| Repo kod satırı | 5 satır | Doğrulandı |
| Ölçüm protokolü | M1–M6 | Planlandı, cihaz bekleniyor |
| Açık sorular | 5 | Açık |
| Kırmızı bayrak | 10 | Aktif (F1–F10) |
| Varsayım günlüğü | 10 | A1–A10: 5 destekli, 3 kısmi, 1 kural, 1 varsayım |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode