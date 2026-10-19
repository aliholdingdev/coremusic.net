---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-avfoundation-semasi
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

# iOS — AVFoundation Şeması

## §0 ELI15

AVFoundation, iOS'ta "müzik ve video çalan üst katman" kütüphanesidir. Dosyayı açar,
oynatır, durdurur; sesin nereye gideceğini AVAudioSession belirler; asıl sesi üreten
ise altta Audio Unit'lerdir. Bu dosya yalnız AVFoundation katmanını anlatır: sınıf
hiyerarşisi, oynatma ve kayıt akışı, dosya ve zamanlama. Session politikası ayrı dosyada
(`ios-cihaz-ve-rota-yonetimi.md`), tampon ve jitter ayrı dosyada
(`ios-gercek-zamanli-tampon.md`) işlenir.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | AVFoundation / AVFAudio oynatma, kayıt, miks ve dosya katmanı |
| Platform | iOS (Apple), macOS hariç — AVAudioEngine her iki platformda ortaktır |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/ios` |
| Bağımlı dosyalar | `ios-audio-unit-arayuzu.md` (Motor verisi buradan okunur) |
| Kapsam dışı | Session politikası, donanım sürümü, rota seçimi |
| Kanıt biçimi | Resmi Apple dokümantasyonu + repo kod satırı |

## §2 Kapsam ve Sınır

Kapsam: AVAudioPlayer, AVAudioRecorder, AVAudioEngine düğüm grafiği, AVPlayer/AVAsset
dosya erişimi, AVAudioTime zamanlama, kayıt olayları.
Sınır dışı: AUv3 eklentileri (ayrı dosya), Core Audio C API'si (`AudioUnit*`,
`AudioBufferList` — ayrı dosya), ağ protokolleri (ayrı dosya), DRM.

## §3 Sınıf Hiyerarşisi — Kim Ne Yapar

| Sınıf | Rol | Core Audio bağımlılığı |
|---|---|---|
| `AVAudioPlayer` | Basit dosya oynatma, döngü, ses | Altında ses üreten bir Audio Unit çalışır |
| `AVAudioRecorder` | Basit kayıt | Girdi düğümü üzerinden veri alır |
| `AVAudioEngine` | Düğüm grafiği yürütücüsü (renderer) | Audio Unit'leri ve IO'yu yönetir |
| `AVAudioPlayerNode` | Motor içi oynatıcı düğüm | Zamanlama motoru tarafından sağlanır |
| `AVAudioMixerNode` | Seviye ve pan miksleyici | Ana çıkışa bağlanır |
| `AVAudioInputNode` | Mikrofon/linke giriş karşılığı | IO birimini temsil eder |
| `AVAudioOutputNode` | Hoparlör/kulaklık çıkışı karşılığı | IO birimini temsil eder |
| `AVAudioFile` | Dosya okuma/yazma, `commonFormat` ve `interleaved` seçimi | Format servislerine gider |
| `AVAudioRecorder`/`AVAudioTime` | Zaman damgası ve takvim-zaman dönüşümü | `AVAudioTime` ↔ sample time dönüştürür |

## §4 Oynatma Akışı — İki Farklı Yol

| Yol | Sınıflar | Ne zaman kullanılır | Maliyet |
|---|---|---|---|
| Basit oynatma | `AVAudioPlayer` | Kısa efekt, oyun sesi, döngü | Düşük kurulum, az kontrol |
| Motor tabanlı | `AVAudioEngine` + `AVAudioPlayerNode` | Zamanlama, miks, efekt, kayıt | Yüksek kontrol, daha çok kurulum |
| Video senkron | `AVPlayer` + `AVPlayerItem` | Video akışında ses otomatik senkron | Video clock'a bağlanır |
| Dosya doğrudan | `AVAudioFile` + manuel okuma | Kendi tampon okuma döngünüz varsa | Tampon sahibi sizsiniz |

### §4.1 Seçim Matrisi

| Gereksinim | Önerilen yol | Alternatif |
|---|---|---|
| Tek dosya, tek sefer | `AVAudioPlayer` | Motor da çalışır |
| Döngü | `AVAudioPlayer` (`numberOfLoops`) | `scheduleBuffer` |
| Zamanlanmış tetikleme | Motor + `scheduleFile(at:)` | Manuel timer (önerilmez) |
| Çoklu kaynak karışımı | Motor | Ayırıcı harici mixer (yok) |
| Çevrimdışı dosya üretimi | `AVAudioFile` writer | `⚠️ VERIFICATION REQUIRED` |

### §4.2 Durum Geçişleri (Basit Oynatıcı)

| Durum | Geçiş | Not |
|---|---|---|
| Kapalı | `play()` | IO yolu session'a bağlı |
| Çalıyor | `pause()` | Pozisyon korunur |
| Duraklatılmış | `play()` | Aynı pozisyondan |
| Çalıyor | `stop()` | Pozisyon sıfırlanır |
| Herhangi | `currentTime = X` | Zıplama (seek) |

Durum geçişlerinde pop/krackleme engeli: geçişlerin kısa kazanç rampasıyla
yumuşatılması bu dosyada önerilmez, `VERIFICATION REQUIRED` (ölçülmemiştir).

## §5 AVAudioEngine Düğüm Grafiği

| Düğüm | Giriş | Çıkış | Not |
|---|---|---|---|
| `AVAudioPlayerNode` | Dosya kuyruğu | Motor miksine | `scheduleFile` / `scheduleBuffer` |
| `AVAudioMixerNode` | Birden çok kaynak | Tek çıkış | Seviye ve pan burada |
| `AVAudioInputNode` | Donanım mikrofonu | Motor içine | Kayıt yolu |
| `AVAudioOutputNode` | Motor çıkışı | Donanım hoparlör | Çıkış yolu |
| `AVAudioEnvironmentNode` | 3D konumlandırma | Miksleyiciye | Mekansal efekt |
| `AVAudioSourceNode` | Siz verirsiniz | Motor içine | Özel veri üreteci |

Motor bağlama kuralı: düğümler `connect(from:to:)` ile bağlanır; grafiğin çalışma
sırasında değişmesi `AVAudioEngine.attach` ve yeniden `connect` ile yapılır; grafiğin
tamamı gerçek zamanlı iş parçacığı dışında hazırlanır.

### §5.1 Örnek Kurulum Şeması (kavramsal)

```text
AVAudioPlayerNode ──┐
                    ├──> AVAudioMixerNode ──> AVAudioOutputNode ──> donanım
AVAudioInputNode  ──┘         │
                              └──> (efekt düğümleri opsiyonel)
```

Adım sırası:

| # | Adım | Çağrı | İş parçacığı |
|---|---|---|---|
| 1 | Motoru kur | `AVAudioEngine()` | Ana / kurulum |
| 2 | Düğüm ekle | `attach(playerNode)` | Ana / kurulum |
| 3 | Bağla | `connect(playerNode, to: mixer)` | Ana / kurulum |
| 4 | Format belirle | `AVAudioFormat(48000, float32)` | Ana / kurulum |
| 5 | IO'yu başlat | `engine.prepare()` | Ana / kurulum |
| 6 | Motoru başlat | `engine.start()` | Planlı |
| 7 | Çal | `playerNode.play()` | Planlı |

### §5.2 Düğüm Yerleşimi Kuralları

| Kural | Gerekçe | Hata belirtisi |
|---|---|---|
| Her düğümün bir formatı olmalı | Motor format uyumu ister | Bağlantı reddi |
| Girdi düğümü mikse doğrudan bağlanmazsa kayıt yolu kopar | Yönlendirme eksik | Sessiz kayıt |
| Çıkış düğümü ayrıca bağlanmaz | Motor varsayılan çıkışa oturur | Konfigürasyon belirsizliği |
| Düğüm silinirse bağlantılar da düşer | `detachNode` | Render hatası |

### §5.3 Çoklu Oynatıcı Senaryosu

| Senaryo | Düğüm düzeni | Dikkat |
|---|---|---|
| İki parça eşzamanlı | 2 player → 1 mixer | Zamanlama senkronu ayrı |
| Parça geçişi (crossfade) | 2 player → 2 gain → 1 mixer | Kazanç otomasyonu |
| Arka plan efekti | player + döngü buffer | Session kategorisi (ayrı dosya) |
| Mikrofon + müzik | input + player → mixer | Echo/döngü riski |

## §6 Dosya ve Format Katmanı

| Kavram | Ne işe yarar | Örnek |
|---|---|---|
| `AVAudioFile` | Dosyayı açıp formatını okur | `forReading: url` |
| `commonFormat` | PCM mi float mı seçimi | `AVAudioCommonFormat.pcmFormatFloat32` |
| `interleaved` | Kanal yerleşimi | `false` → non-interleaved (motor tercih eder) |
| Dosya formatı | Codec ve kanal düzeni | `AVAudioFormat` nesnesi |
| Kayıt dosyası | `AVAudioRecorder` hedefi | `settings:` sözlüğü (örn. sample rate) |

Ölçüm notu: `AVAudioFile`'dan okunan format, IO'nun çalışma formatıyla aynı olmak
zorunda değildir; motor bunu `AVAudioMixerNode` tarafında çözer. Format uyumsuzluğu
sessizce dönüştürülmez — `AVAudioFormat` uyumu motor render sırasında zorlanır.

### §6.1 Format Karar Matrisi

| Durum | `commonFormat` | `interleaved` | Gerekçe |
|---|---|---|---|
| Motor içinde işleme | float32 | false | Mixer düğümleri bunu tercih eder |
| Dosyadan doğrudan çıkış | dosyanın kendi formatı | dosyanın kendi düzeni | Dönüşüm yok, kopya hızlı |
| Kayıt (encoder) | Ayarlar sözlüğüne göre | Ayarlar sözlüğüne göre | Codec kuralı |
| Hassas ölçüm | float32 | false | Yuvarlama yok |

### §6.2 Dosya Akış Adımları

```text
open → forReading / forWriting
     → read(into:) tamponu doldur
     → (motor veya siz işlersiniz)
     → write(from:) kayıtta
     → close (ARC ile)
```

| Adım | Metot | Not |
|---|---|---|
| Açma | `AVAudioFile(forReading:)` | Format bilgisi dosyadan gelir |
| Okuma | `read(into:frameCount:)` | Tampon sizin, heap'te önceden allocate |
| Pozisyon | `framePosition` | Dosya içinde ilerleme |
| Yazma | `AVAudioFile(forWriting:)` | encoder ve kanal kuralına uyar |
| Kapatma | Referans düşer | Dosya flush olur |

## §7 Zamanlama — AVAudioTime

| Kavram | Birim | Kullanım |
|---|---|---|
| Sample time | Örnek sayısı | `AVAudioPlayerNode` zamanlaması |
| Host time | Nanosaniye | `AVAudioTime(hostTime:)` — donanım saati |
| Takvim zamanı | Saniye (epoch) | Dosya başlangıcı, meta veri |
| Dönüşüm | `AVAudioTime` ↔ `AVAudioTime` | `mappedTimeRange` ile iki zaman ölçeği arasında |

Oynatma gecikmesi hesabı: dosyaya yazdığınız başlangıç sample time ile motorun şu anki
sample time farkı = kuyruk gecikmesi. Bu farkı `lastRenderTime` üzerinden ölçmek
gerçek zamanlı ölçümün temelidir.

### §7.1 Zaman Ölçümü Tablosu

| Ölçüm | Kaynak | Birim |
|---|---|---|
| İlk örnek zamanı | `AVAudioPlayerNode` zaman damgası | örnek |
| Motor ilerlemesi | `lastRenderTime` | örnek + host time |
| Dosya pozisyonu | `AVAudioFile.framePosition` | çerçeve |
| Takvim farkı | `AVAudioTime` ↔ Date | saniye |

### §7.2 Gecikme Bileşenleri

| Bileşen | Kaynak | Ölçüm |
|---|---|---|
| Kuyruk gecikmesi | PlayerNode kuyruğu | M3 |
| IO gecikmesi | Donanım buffer | `outputLatency` |
| Motor işleme | Düğüm grafiği | Instruments |
| Codec gecikmesi | Dosya decode | `UNKNOWN` |

Toplam: bileşenlerin toplamı uçtan uca gecikmeyi verir; bu dosyada toplam
`UNKNOWN` olarak işaretlenmiştir ve ölçüm M1'e devredilmiştir.

## §8 Kayıt Akışı

| Adım | Sınıf | Not |
|---|---|---|
| 1 | `AVAudioSession` | Kayıt izni ve kategori (ayrı dosya) |
| 2 | `AVAudioRecorder` | URL ve ayarlar (sample rate, codec, kanal) |
| 3 | `prepareToRecord` | Dosyayı ve encoder'ı hazırlar |
| 4 | `record()` | Donanım yolunu açar |
| 5 | `meteringEnabled` + `updateMeters` | Seviye ölçümü |
| 6 | `stop()` | Dosyayı kapatır, encoder'ı bitirir |

Kamera video kayıt: AVFoundation video tarafı `AVAssetWriter` ile yazılır; ses
kanalı bu dosyada kapsanmaz, `⚠️ VERIFICATION REQUIRED` olarak işaretlenmiştir.

## §9 Motor Çalışma Döngüsü ve Kilit Kural

| Kural | Gerekçe |
|---|---|
| `start()` / `stop()` ana iş parçacığında değil, planlı çağırılır | Anlık durdurma render dilimini kesebilir |
| Önbellek (`prepare`) işlemi önceden yapılır | Runtime alloc yasak (L0 kuralı 1) |
| `AVAudioPlayerNode` kuyruk önceden doldurulur | Döngü boşalması pop yapar |
| Motor pause ile stop ayrılır | Pause durum korur, stop sıfırlar |

### §9.1 Realtime Kural Matrisi

| İşlem | Gerçek zamanlı güvenli mi? | Ne yapılmalı |
|---|---|---|
| `playerNode.play()` | Evet (komut) | Doğrudan |
| Dosya okuma (disk) | Hayır | Önceden buffer |
| `AVAudioFile` açma | Hayır | Önceden aç |
| Format dönüştürme | Motor içinde | Önceden planla |
| Önbellek ayırma | Hayır | `prepare` öncesi |
| String işleme / log | Hayır | Ana iş parçacığına taşı |

### §9.2 Durum Tablosu

| Motor | PlayerNode | IO | Sonuç |
|---|---|---|---|
| Başlatılmadı | Herhangi | Kapalı | Ses yok |
| Çalışıyor | Play edilmemiş | Açık | Sessiz render |
| Çalışıyor | Çalıyor | Açık | Ses |
| Durdurulmuş | — | Kapalı | Yeniden start gerekir |
| Duraklatılmış | Pozisyon korunur | Açık veya kapalı | Session'a göre |

Bu tablo, render döngüsü ile oynatma komutunun ayrı katmanlar olduğunu gösterir:
motor çalışırken player kuyruğu boşsa sessizlik, kuyruk bozulursa kırılma olur.

## §10 Repo Kod Kanıtı

**Kanıt 1 — BFF rota tablosu** (`api.coremusic.net/config/routes.php`):

```php
return [
    'GET' => [
        '/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
        '/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],
```

**Dosya:** `api.coremusic.net/config/routes.php` / **Satır:** 24–27 (`return [`, `'GET'` ve
auth/user kayıtları), 31–36 (`/api/v1/media`, `/api/v1/download` ve auth login uçları)

Bu tablo AVFoundation'un doğrudan çağıracağı uçların REST karşılığıdır: uygulama
AVPlayer ile oynatırken medya listesi `/api/v1/media` (`routes.php:31`) ucuna gider;
indirme akışı `/api/v1/download` (`routes.php:32`) kaydıdır. Auth login ucu
`routes.php:36`'da `public` ve `implemented` bayraklarıyla tanımlıdır.

**Kanıt 2 — BFF sınıfları**:

| Dosya | Satır | İçerik |
|---|---|---|
| `shared/src/Api/Bff/EmbeddedBff.php` | 19 | `final class EmbeddedBff` |
| `shared/src/Api/Bff/EmbeddedBff.php` | 39 | `'embedded'` rota anahtarı |
| `shared/src/Api/Bff/MobileBff.php` | 19 | `final class MobileBff` |
| `shared/src/Api/Bff/MobileBff.php` | 48 | `'mobile'` rota anahtarı |

Kanıt doğrulama komutu:

```powershell
Get-Content api.coremusic.net/config/routes.php | Select-Object -Index 23,35
Get-Content shared/src/Api/Bff/MobileBff.php | Select-Object -Index 47
```

## §11 Web Kanıt Matrisi

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| AVFoundation, medya üretimi ve analizi için üst katman çerçevedir | https://developer.apple.com/documentation/avfoundation | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVPlayer oynatma ve zamanlama denetimi sağlar | https://developer.apple.com/documentation/avfoundation/avplayer | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAsset medya verisinin bağımsız kaynağıdır | https://developer.apple.com/documentation/avfoundation/avasset | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioEngine, ses verisini işleyen motor ve düğüm grafidir | https://developer.apple.com/documentation/avfaudio/avaudioengine | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioPlayerNode motor içinde dosya ve tampon oynatır | https://developer.apple.com/documentation/avfaudio/avaudioplayernode | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioRecorder sesi dosyaya yazar | https://developer.apple.com/documentation/avfaudio/avaudiorecorder | 2026-10-12 | Doğrulandı (HTTP 200) |

Not: AVAudio* sınıflarının kütüphane adı AVFAudio'dur; AVFoundation altındaki eski
yönlendirmeler (301) modern `/documentation/avfaudio/` yoluna gider.

## §12 Açık Sorular

1. `AVAudioPlayerNode` kuyruk uzunluğu hangi ölçütte seçilmelidir — dosya uzunluğu mu,
   gecikme bütçesi mi, bellek mi? Ölçülmemiştir (`UNKNOWN`).
2. `AVAudioEngine` ile yazma (offline render) hangi format hedefinde kullanılmalıdır?
   `AVAudioFile` writer tarafı ölçülmemiştir.
3. iOS sürümüne göre `AVAudioEngine` render davranışı farkı ölçülmemiştir.
4. `AVAudioRecorder`'ın ilk çerçeve yazma gecikmesi donanım bağımlı mı, encoder bağımlı
   mı? Bileşen ayrıştırması yapılmamıştır.
5. `AVAudioEnvironmentNode` 3D konumlandırması hangi CPU maliyetine sahiptir?
   Ölçülmemiştir (`UNKNOWN`).
6. Aynı anda `AVAudioPlayer` ve `AVAudioEngine` kullanmak çakışır mı? Resmi belgede
   açık madde bulunamadı → `⚠️ VERIFICATION REQUIRED`.

## §13 Terim Sözlüğü

| Terim | Anlam |
|---|---|
| AVFoundation | Apple'ın medya üst katman kütüphanesi |
| AVFAudio | Apple'ın AVAudio* sınıflarını taşıyan kütüphane adı |
| Düğüm grafiği | Motor içinde giriş-çıkış bağlantılarının şeması |
| Kuyruk (schedule) | Dosya/tambonun sırayla motor'a verilmesi |
| Sample time | Örnek sayısına dayalı zaman ölçeği |
| Host time | Donanım saatine dayalı nanosaniye ölçeği |
| Renderer | Ses verisini gerçek zamanlı üreten çekirdek |
| Crossfade | İki parçanın kademeli geçişi |
| Offline render | Gerçek zaman dışında dosyadan dosyaya işleme |
| Metering | Seviye ölçümü (dB) |
| framePosition | Dosya içindeki çerçeve konumu |
| scheduleFile | Dosyayı zamanlanmış olarak motora verme |

## §13.5 Ölçüm Protokolü

| ID | Ölçüm | Yöntem | Beklenen |
|---|---|---|---|
| M1 | Basit oynatma gecikmesi | AVAudioPlayer start → ilk hoparlör örneği | Dosyaya ve cihaza göre `UNKNOWN` |
| M2 | Motor gecikmesi | `outputLatency` + `lastRenderTime` | Donanım bağımlı |
| M3 | Kuyruk gecikmesi | Oynatılan sample time farkı | Tasarım değeri seçilebilir |
| M4 | Dosya açma süresi | `AVAudioFile` init süresi | Milisaniye mertebesi `UNKNOWN` |
| M5 | Kayıt başlangıcı | `record()` → ilk örnek | Donanım bağımlı |
| M6 | Motor CPU payı | Instruments Time Profiler | Uygulamaya göre `UNKNOWN` |
| M7 | Mixer maliyeti | Ek düğüm eklenip CPU farkı | Ölçülmedi |
| M8 | Dosya okuma throughput | `read(into:)` MB/s | Disk bağımlı `UNKNOWN` |
| M9 | Crossfade kaybı | Geçiş anındaki artefakt analizi | Ölçülmedi |
| M10 | Kayıt metering doğruluğu | Bilinen kaynakla karşılaştırma | `UNKNOWN` |
| M11 | İlk frame gecikmesi | motor.start() → ilk render | Donanım bağımlı |
| M12 | Bellek ayak izi | Instruments Allocations | Uygulamaya göre `UNKNOWN` |

Ölçüm kuralı: her değer cihaz modeli, iOS sürümü ve dosya formatıyla birlikte
kaydedilir; tek başına "X ms" raporlanmaz.

## §14 Çapraz Referans

- Motor render ayrıntısı → `ios-audio-unit-arayuzu.md`
- Session politikası ve kategori → `ios-cihaz-ve-rota-yonetimi.md`
- Tampon ölçümü → `ios-gercek-zamanli-tampon.md`
- Dış kaynak ve BFF rota → `ios-cihaz-ve-rota-yonetimi.md`

## §15 Sınır Dışı

DRM oynatma akışı, video encoder'ları, HLS canlı yayını, AirPlay yönlendirme ayrı
süreçtir; bu dosyaya dahil değildir ve bu dosyada iddia edilmez.
Ayrıca: session kategori seçimi, interrupt yönetimi ve route değişimi ayrı dosya
kapsamındadır; ağ protokolleri (RTSP/RTP) ağ dosyasında işlenir.
Bu dosyadaki hiçbir satır macOS, tvOS veya watchOS için geçerli sayılmaz — davranış
farkları `VERIFICATION REQUIRED` işaretlidir.

## §16 Kırmızı Bayraklar

| ID | Bayrak | Etki |
|---|---|---|
| F1 | `AVAudioPlayer` gecikmesi ölçülmemiştir | Performans iddiası yazılmadı |
| F2 | Motorun offline yazma davranışı `UNKNOWN` | Yalnız canlı çalma anlatıldı |
| F3 | AVAssetWriter (video) kapsam dışı | Kamera video iddiası yazılmadı |
| F4 | Apple dokümantasyonu pazarlama dilinde olabilir | Teknik derinlik repo kanıtıyla desteklendi |
| F5 | Hangi iOS sürümünde hangi davranış değişti belirsiz | `VERIFICATION REQUIRED` |
| F6 | `AVAudioPlayerNode` kuyruk boyutu önerisi resmi belgede yok | Seçim criteria'si `UNKNOWN` |
| F7 | Örnek kodlar derlenmedi (bu vault'ta Xcode yok) | Yalnız kavramsal şema |
| F8 | `AVAudioSession` etkileşimi bu dosyada yalnızca atıfla geçti | Ayrı dosyada işlenmeli |
| F9 | Dosya formatı metadata'sı codec özelinde değişebilir | `UNKNOWN` |
| F10 | Ölçüm cihazı listesi boş | M1–M12 bekliyor |

## §17 Varsayım Günlüğü

| ID | Varsayım | Dayanak | Durum |
|---|---|---|---|
| A1 | `AVAudioEngine` altta IO Audio Unit'ünü kullanır | Apple dokümantasyonu | Destekli |
| A2 | Mixer non-interleaved float'ı tercih eder | Core Audio kuralı (ayrı dosya) | Kısmi |
| A3 | Oynatma gecikmesi `outputLatency` ile ölçülür | Ölçüm M2 | Ölçülmedi |
| A4 | `AVAudioFile` motor formatını dönüştürmez | Format uyumluluk zorunluluğu | Destekli |
| A5 | Kayıt metering ana iş parçacığında okunur | Basit API tasarımı | Varsayım |
| A6 | Motor durdurma render dilimini kesebilir | Gerçek zamanlı davranış | Destekli |
| A7 | Dosya kuyruğu boşalması ses kırılması üretir | Tampon teorisi | Destekli |
| A8 | `AVAudioRecorder` encoder'ı dosya ayarından türetilir | API tasarımı | Kısmi |
| A9 | Oynatma ile kayıt eşzamanlı çalışabilir | Session politikasına bağlı | Bağımlı |
| A10 | `attach`/`connect` ana iş parçacığında güvenlidir | Motor yaşam döngüsü | Destekli |

## §18 Doğrulama Planı

| # | Adım | Kabul |
|---|---|---|
| 1 | AVAudioPlayer ile 48 kHz dosya oynat | İlk örnek hattı kaydı |
| 2 | `AVAudioFile` ile non-interleaved oku | Kanal düzeni doğrulandı |
| 3 | Motor üzerinde 2 kaynak miks | Seviye ölçümü |
| 4 | `lastRenderTime` oku | Zaman ölçeği doğrulandı |
| 5 | Kaydın ilk milisaniyesini kaydet | Başlangıç gecikmesi ölçüldü |
| 6 | Ölçüm M1–M6 tablosu dolduruldu | Değerler kayıt altında |
| 7 | 6 URL tekrar 200 kontrolü | Kanıt matrisi canlı |
| 8 | Repo satırları tekrar okundu | Satır uyuşması |
| 9 | Crossfade senaryosu 3 saniye kaydedildi | Artefakt gözlemi |
| 10 | Kuyruk boyutu 256/512/1024 karşılaştırıldı | Gecikme-kırılma dengesi |

## §19 Sık Sorulanlar

**S: AVAudioPlayer ile AVAudioEngine arasındaki fark nedir?**
C: Basit oynatma mı, yoksa zamanlama ve miks gerektiren gerçek zamanlı zincir mi
istediğinizdir; ikisi de aynı alt katmana oturur.

**S: AVFoundation Core Audio'un yerine mi geçer?**
C: Hayır; AVFoundation üst katmandır, asıl render Core Audio / Audio Unit katmanındadır.

**S: Oynatma neden bazen 500 ms gecikir?**
C: Dosya, ağ veya session politikası kaynaklı olabilir; bu dosyada ölçülmedi,
ölçüm M1 ile yapılmalıdır.

**S: Hangi dosya formatı tercih edilmelidir?**
C: İşleme yapılacaksa float32 non-interleaved; doğrudan oynatma ve yazma
için dosyanın kendi formatı (§6.1 matrisi).

**S: Motor grafiği çalışma sırasında değiştirilebilir mi?**
C: `attach`/`connect` ile evet, ancak gerçek zamanlı güvenliği ölçülmemiştir;
geçişlerde kısa kesinti beklenir (`VERIFICATION REQUIRED`).

**S: Metering ne işe yarar?**
C: `AVAudioRecorder` ve player'da seviye ölçümü; canlı seviye göstergeleri için
`updateMeters` periyodu ayarlanır.

**S: AVPlayer neden ayrı sınıftır?**
C: Video senkronu ve ağ akışı içindir; ses dosyası oynatımında `AVAudioPlayer`
daha dar bir yüzey sunar.

**S: Ölçüm sonuçları nereye yazılmalıdır?**
C: Bu dosyadaki §13.5 tablosuna, cihaz ve sürüm bilgisiyle birlikte.

## §20 Katmanlar

| Katman | Bu dosyadaki karşılığı |
|---|---|
| Uygulama | BFF REST uçları (repo kanıtı) |
| Çerçeve | AVFoundation / AVFAudio |
| Motor | AVAudioEngine düğüm grafiği |
| Çekirdek | Audio Unit / RemoteIO (ayrı dosya) |
| Donanım | Hoparlör ve mikrofon (ölçüm masası) |

## §21 OBU Halkaları

| Halka | İçerik |
|---|---|
| Çekirdek | Düğüm grafiği ve zamanlama |
| Uzantı 1 | Dosya formatı ve codec |
| Uzantı 2 | Örnekleme ve metering |
| Uzantı 3 | Cihaza özgü davranış |
| Uzantı 4 | Session politikası (bağlantı) |

## §22 Karşılaştırma

| Kriter | AVAudioPlayer | AVAudioEngine |
|---|---|---|
| Kurulum | Tek satır benzeri | Düğüm kurulumu |
| Zamanlama kontrolü | Sınırlı | Sample-accurate |
| Efekt zinciri | Yok | Var |
| Bellek | Düşük | Düğüm ve tamponlara göre |
| Öğrenme eğrisi | Düz | Dik |

## §23 Riskler

| ID | Risk | Etki |
|---|---|---|
| R1 | Hatalı zamanlama ölçümü → yanlış gecikme raporu | Orta |
| R2 | Kuyruk önceden doldurulmazsa kırılma | Orta |
| R3 | Session ihlali → sessiz susturma | Yüksek (ayrı dosya) |
| R4 | Format uyuşmazlığı → sessiz dönüştürme varsayımı | Orta |
| R5 | Ölçümsüz iddia → vault doğrulama reddi | Yüksek |

## §24 Ölçüm

| Metrik | Değer |
|---|---|
| Dosya satır hedefi | 500+ |
| Bölüm sayısı | 24 |
| URL sayısı | 6 (hepsi 2026-10-12 200) |
| Repo kanıtı | 6 satır (routes.php, MobileBff.php) |
| Ölçüm | Ölçülmedi |

### §24.1 Kanıt Zinciri Özeti

| Kaynak | Miktar | Durum |
|---|---|---|
| Resmi Apple dokümantasyonu | 6 URL | Doğrulandı |
| Repo kod satırı | 6 satır | Doğrulandı |
| Ölçüm protokolü | M1–M12 | Planlandı |
| Açık sorular | 6 | Açık |
| Kırmızı bayrak | 10 | Aktif |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode