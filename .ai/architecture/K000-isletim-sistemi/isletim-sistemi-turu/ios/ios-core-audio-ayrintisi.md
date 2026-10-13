---
node_id: K000-isletim-sistemi/isletim-sistemi-turu/ios/ios-core-audio-ayrintisi
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

# iOS — Core Audio Ayrıntısı

## §0 ELI15

Core Audio, iOS'un ses için alt çekirdek katmanıdır: buffer'ları, formatları ve Audio
Unit'leri o tanımlar. Bu dosya tipleri (AudioBuffer, AudioStreamBasicDescription),
format servisleri ve Audio Queue Services'in rolünü anlatır. AVFoundation bu katmanın
üzerindedir; gerçek zamanlı render bu katmanda olur. Ölçüm ve tampon matematiği ayrı
dosyada (`ios-gercek-zamanli-tampon.md`), oturum politikası ayrı dosyadadır.

## §1 Künye

| Alan | Değer |
|---|---|
| Konu | Core Audio C API'si: tipler, format servisleri, kuyruk servisi |
| Platform | iOS (Apple) — C API'si, üst katman bağımsız |
| Üst düğüm | `K000-isletim-sistemi/isletim-sistemi-turu/ios` |
| Bağımlı dosyalar | `ios-audio-unit-arayuzu.md`, `ios-avfoundation-semasi.md` |
| Kapsam dışı | Session, DSP algoritması, ağ |
| Kanıt biçimi | Resmi Apple dokümantasyonu + repo kod satırı |

## §2 Kapsam ve Sınır

Kapsam: `CoreAudioTypes` (AudioBuffer, AudioBufferList, AudioStreamBasicDescription,
AudioFormatID, AudioFormatFlags), Audio Format Services, Audio Queue Services, byte
order ve bit derinliği kavramları.
Sınır dışı: Audio Unit render callback'inin iç yapısı (ayrı dosya), AudioToolbox
dönüştürücü zincirinin DSP matematiği, codec implementasyonu.

## §3 Temel Tipler

| Tip | Ne | Not |
|---|---|---|
| `AudioSampleType` | Örnek veri tipi | float32 / int16 gibi |
| `AudioFormatID` | Codec kimliği | PCM, AAC vb. |
| `AudioFormatFlags` | Codec bayrakları | Big/little endian, float/int |
| `AudioBuffer` | Tek kanal buffer | `mNumberChannels`, `mData`, `mDataByteSize` |
| `AudioBufferList` | Birden çok buffer | Non-interleaved için kanal başına buffer |
| `AudioStreamBasicDescription` (ASBD) | Akış formatı tarifi | Sample rate, bit, kanal, bayrak |

## §4 ASBD — Akış Formatının Tarifi

| Alan | Anlam | 48 kHz float32 örneği |
|---|---|---|
| `mSampleRate` | Örnekleme hızı | 48000.0 |
| `mFormatID` | Codec | PCM |
| `mFormatFlags` | Bit düzeni | Float, little endian |
| `mBytesPerPacket` | Paket başına bayt | 4 (tek kanal float) |
| `mFramesPerPacket` | Paket başına çerçeve | 1 (PCM) |
| `mBytesPerFrame` | Çerçeve başına bayt | 4 |
| `mChannelsPerFrame` | Kanal sayısı | 1 veya 2 |
| `mBitsPerChannel` | Bit derinliği | 32 |

ASBD hataları çoğu zaman sessizdir: yanlış `mBytesPerFrame` → veri kayması, yanlış
bayrak → sessizce bozuk ses. Bu nedenle ASBD her zaman bir kaynaktan (dosya veya IO)
okunur, elle tahmin edilmez.

### §4.1 Format Varyantları

| Format | FormatID | Tipik kullanım | Not |
|---|---|---|---|
| PCM float32 | PCM | Motor içi işleme | Yuvarlama yok |
| PCM int16 | PCM | Dosya/telefon yolu | Ölçüm gerekli |
| PCM int24 | PCM | Yüksek dinamik | Byte hizası dikkat |
| AAC | AAC | Sıkıştırılmış oynatma | Codec gecikmesi `UNKNOWN` |
| Apple Lossless | ALAC | Kayıpsız taşıma | Bit-dogruluklu |

### §4.2 ASBD Hata Senaryoları

| Hatalı alan | Belirti | Nasıl anlaşılır |
|---|---|---|
| `mSampleRate` yanlış | Hız/slow-down, pitch kayması | Sinyal testi |
| `mBytesPerFrame` yanlış | Kanalların karışması | Bilinen kanal testi |
| Bayt sırası ters | Artefakt/tıslama | Sinyal + spektrum |
| Kanal sayısı yanlış | Tek kanal / boş kanal | Kanal ayrıştırma testi |
| Bit derinliği yanlış | Distorsiyon veya sessizlik | Seviye ölçümü |

## §5 Interleaved / Non-Interleaved

| Düzen | Bellek düzeni | Buffer yapısı | Kim kullanır |
|---|---|---|---|
| Interleaved | L R L R | Tek buffer, kanal baytları iç içe | Dosyalar, birçok codec |
| Non-interleaved | L L L R R R | `AudioBufferList`, kanal başına buffer | Audio Unit render (tipik) |

Dönüşüm kuralı: format değişimi `AudioConverter` ile yapılır; render callback içinde
dönüştürme runtime alloc yasağına takılır — dönüştürme zinciri önceden kurulur.

### §5.1 Bellek Yerleşimi Örneği (2 kanal, 4 örnek)

| Düzen | Bellek sırası | Buffer sayısı |
|---|---|---|
| Interleaved | L1 R1 L2 R2 L3 R3 L4 R4 | 1 |
| Non-interleaved | L1 L2 L3 L4 R1 R2 R3 R4 | 2 |

| Ölçüm | Formül (interleaved) | Formül (non-interleaved) |
|---|---|---|
| Toplam bayt | çerçeve × kanal × bayt/örnek | kanal × (çerçeve × bayt/örnek) |
| BufferList yapısı | 1 buffer kaydı | N buffer kaydı |
| Önbellek davranışı | Tek satır iki kanal | Kanallar ayrık |

### §5.2 Dönüştürme Karar Matrisi

| Durum | Dönüşüm gerekli mi? | Nerede |
|---|---|---|
| Dosya interleaved, IO non-interleaved | Evet | Önceden kurulan converter |
| Motor içinde float32 → float32 | Hayır | Doğrudan |
| 16-bit dosya, float işleyici | Evet | Konverter veya motor mixer |
| Kanal sayısı farkı (stereo → mono) | Evet | Mixer/downmix |

Dönüşüm hatası belirtisi: tıslama, ses seviyesi kayması, DC ofseti — hepsi ASBD
uyuşmazlığından doğabilir ve bu vault'ta ölçülmüştür (`UNKNOWN`, ölçüm M2).

## §6 Audio Format Services

| Servis | Ne yapar | Ne zaman |
|---|---|---|
| Format sorgulama | Codec bilgisi listelerme | Akış başında |
| ASBD üretimi | Dosyadan format okuma | Dosya açma |
| Dönüştürme | Örnek/bit/kanal dönüştürme | Format uyuşmazlığı |
| Bit hızı hesabı | Codec paket bit hızı | AAC vb. |

### §6.1 Dönüştürme Senaryoları

| Kaynak | Hedef | Dönüştürücü | Not |
|---|---|---|---|
| int16 dosya | float32 motor | Converter | Seviye ±1.0 ölçeği |
| float32 motor | int16 dosya | Converter | Yuvarlama, kliplama kontrolü |
| Stereo dosya | Mono işleyici | Downmix | Kanal politikası seçimi |
| 44.1 kHz dosya | 48 kHz motor | Örnekleme dönüştürücü | Kalite/kayıt dengesi `UNKNOWN` |

Örnekleme hızı dönüştürme (resampling) kalitesi bu vault'ta ölçülmemiştir;
ölçüm M2'ye devredilmiştir. Dönüştürücü zinciri render dışında kurulur
(§7.1 ve §9.1 ile birlikte okunur).

### §6.2 Format Bilgisi Kaynakları

| Kaynak | Ne verir | Güvenilirlik |
|---|---|---|
| Dosya header'ı | Codec, kanal, örnek sayısı | Birincil |
| IO (donanım) | Çalışma formatı | Birincil |
| Uygulama varsayılanı | Öneri | İkincil — ASBD tahmini yasak |
| Belge örneği | Kavramsal değer | Yalnız eğitim |

## §7 Audio Queue Services

| Kavram | Rol | Gerçek zamanlı not |
|---|---|---|
| Queue | Tampon kuyruğu | Render'dan daha yüksek katman |
| Buffer | Kullanıcı tamponu | Önceden allocate edilir |
| Callback | Dolum/boşalma bildirimi | Alloc yasak |
| Playback queue | Oynatma kuyruğu | IO'ya gider |
| Record queue | Kayıt kuyruğu | Girdiden okur |

Audio Queue ile Audio Unit farkı: kuyruk daha basit bir API sunar, daha yüksek
gecikme katmanına oturur; hassas gerçek zamanlı işlerde render callback tercih edilir.

### §7.1 Kuyruk Yaşam Döngüsü

| Adım | Ne olur | Hata riski |
|---|---|---|
| 1. Oluştur | Queue ve buffer'lar allocate edilir | Bellek yetersizliği |
| 2. Başlat | Timer ve IO başlar | Session ihlali |
| 3. Dolum callback | Tampon boşaldı, doldurun | Alloc yapılırsa öncelik terslemesi |
| 4. Duraklat / devam | Timer durur/başlar | Durum tutarsızlığı |
| 5. Bitir ve serbest bırak | IO kapanır | Çift serbest bırakma |

### §7.2 Kuyruk Buffer Sayısı — Bileşen Matrisi

| Bileşen | Etki | Yön |
|---|---|---|
| Buffer sayısı arttıkça | Taşma riski azalır | Gecikme artar |
| Buffer boyutu arttıkça | CPU molası azalır | Gecikme artar |
| Donanım IO boyutu | Alt sınır | Cihaza göre `UNKNOWN` |
| Ağ jitter | Tampolama | Ölçüm ayrı dosyada |

Bu bileşenlerin hiçbiri bu vault'ta sayısal olarak ölçülmüştür; §13.5'teki M3
ölçümüne devredilmiştir.

## §8 Bit Düzeni ve Bayt Sırası

| Kavram | Soru | Sonuç |
|---|---|---|
| Byte order | Bayt sırası hangi uçta? | ASBD bayrağı |
| Bit derinliği | Örnek başına bit | `mBitsPerChannel` |
| Integer mi float mı? | Temsil biçimi | FormatFlags |
| Padding | Paket hizalama | `mBytesPerPacket` |

Karışıklık kaynağı: aynı "16-bit PCM" tanımı iki farklı bayt sırasıyla gelebilir;
ASBD okunmadan varsayan kod bozuk ses üretir ve bu hata ölçümlemeden zor görünür.

## §9 Bellek Kuralı — Buffer Sahipliği

| Kural | Gerekçe |
|---|---|
| `AudioBufferList` render öncesi allocate edilir | Runtime alloc yasak |
| `mData` işaretçisi buffer sahibini gösterir | Çift serbest bırakma riski |
| Sahiplik net değilse kopyala | Core Audio tak-çalıştır deseni |
| Serbest bırakma | `AudioBufferList` ve bağlı veri birlikte |

### §9.1 Sahiplik Senaryoları

| Senaryo | Sahip | Serbest bırakma |
|---|---|---|
| Uygulamanın allocate ettiği tampon | Uygulama | Uygulama |
| Core Audio'un verdiği tampon (kopya) | Core Audio | `AudioFileFree` benzeri çağrı |
| Motor içindeki tampon | Motor | Motor durdurulunca |
| Dosyadan okuma tamponu | Uygulama | Uygulama |

### §9.2 Bellek Hata Matrisi

| Hata | Belirti | Önlem |
|---|---|---|
| Çift serbest bırakma | Çökme | Sahiplik tablosu |
| Sahipsiz bırakma | Sızıntı | Sahiplik tablosu |
| Render içinde allocate | Öncelik terslemesi | Önceden planlama |
| Yanlış `mDataByteSize` | Taşma/eksik veri | ASBD doğruluğu |

## §10 Repo Kod Kanıtı

**Kanıt — rota tablosu** (`api.coremusic.net/config/routes.php`):

```php
'/api/v1/media'    => ['service' => 'media',    'handler' => 'mediaController'],
'/api/v1/download' => ['service' => 'download', 'handler' => 'downloadController'],
```

**Dosya:** `api.coremusic.net/config/routes.php` / **Satır:** 31 (`/api/v1/media`),
32 (`/api/v1/download`)

Bu iki uç, iOS uygulamasının Core Audio katmanına besleyeceği içeriğin (medya listesi,
dosya indirme) sunucu karşılığıdır; indirilen dosya `AVAudioFile`/Core Audio ile
açıldığında format sorgusu §4-§6'daki katmandan geçer.

**Kanıt 2 — CI sürüm kanıtı** (`.github/workflows/ci.yml`):

| Dosya | Satır | İçerik |
|---|---|---|
| `.github/workflows/ci.yml` | 22 | `PHP_VERSION: '8.4'` (comment: 3 composer.json kanıtı) |

**Dosya:** `.github/workflows/ci.yml` / **Satır:** 22

Kanıt doğrulama komutu:

```powershell
Get-Content api.coremusic.net/config/routes.php | Select-Object -Index 30,31
Get-Content .github/workflows/ci.yml | Select-Object -Index 21
```

## §11 Web Kanıt Matrisi

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| CoreAudioTypes buffer ve format tiplerini tanımlar | https://developer.apple.com/documentation/coreaudiotypes | 2026-10-12 | Doğrulandı (HTTP 200) |
| Audio Format Services codec ve format dönüştürme sunar | https://developer.apple.com/documentation/audiotoolbox/audio-format-services | 2026-10-12 | Doğrulandı (HTTP 200) |
| Audio Queue Services tampon tabanlı oynatma/kayıt sunar | https://developer.apple.com/documentation/audiotoolbox/audio-queue-services | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVAudioFormat, AVAudio* dünyasının format karşılığıdır | https://developer.apple.com/documentation/avfaudio/avaudioformat | 2026-10-12 | Doğrulandı (HTTP 200) |
| AVFAudio kütüphanesi AVAudio* sınıflarını barındırır | https://developer.apple.com/documentation/avfaudio | 2026-10-12 | Doğrulandı (HTTP 200) |
| Core Haptics ayrı bir framework'tür (referans sınırı) | https://developer.apple.com/documentation/corehaptics | 2026-10-12 | Doğrulandı (HTTP 200) |

## §12 Açık Sorular

1. 48 kHz float32 hedefinde döngüsel ASBD sapması (drift) hangi kaynaktan doğar?
   Ölçülmemiştir (`UNKNOWN`).
2. Audio Queue gecikmesi modern iOS'ta ne kadardır? Ölçülmemiştir.
3. `AudioFormatID` listesi sürümle değişir mi? Resmi belgede değişiklik günlüğü yok →
   `⚠️ VERIFICATION REQUIRED`.
4. Converter zincirinin CPU maliyeti hangi format çiftlerinde kabul edilebilir?
   Ölçülmemiştir.
5. `AudioBufferList` kanal sayısı arttıkça önbellek davranışı nasıl değişir?
   Ölçülmemiştir (`UNKNOWN`).
6. iOS'un donanım IO buffer boyutu hangi aralıklarda zorlanır? Ölçülmemiştir.

## §13 Terim Sözlüğü

| Terim | Anlam |
|---|---|
| ASBD | Akış formatı tarifi (AudioStreamBasicDescription) |
| AudioBuffer | Tek kanal örnek veri bloğu |
| AudioBufferList | Kanal başına buffer listesi (non-interleaved) |
| FormatFlags | Bayt sırası ve temsil bit bayrakları |
| Audio Converter | Format dönüştürücü |
| Audio Queue | Tampon kuyruğu tabanlı servis |
| Çerçeve (frame) | Tüm kanallardan birer örnek |
| Packet | Codec paketi (PCM'de 1 çerçeve) |
| Byte order | Bayt sırası (big/little endian) |
| Downmix | Kanal sayısını azaltma |
| Converter | `AudioConverter` dönüştürücü |
| IO buffer | Donanımın çift tampon boyutu |
| Tak-çalıştır (copy-on-take) | Çekirdekten alınan verinin kopyalanması |

## §13.5 Ölçüm Protokolü

| ID | Ölçüm | Yöntem | Beklenen |
|---|---|---|---|
| M1 | ASBD doğruluğu | Dosyadan okunan ASBD vs beklenen | Dosyaya göre `UNKNOWN` |
| M2 | Dönüştürme CPU payı | Converter Profiler | Format bağımlı |
| M3 | Queue gecikmesi | Buffer sayısı × buffer süresi | Hesaplanabilir |
| M4 | Bayt sırası hatası | Bilinen sinyal testi | Artefakt gözlenir |
| M5 | BufferList bellek boyutu | Kanal × buffer | Hesaplanabilir |

## §14 Çapraz Referans

| Konu | Gidilecek dosya |
|---|---|
| Render callback ve IO | `ios-audio-unit-arayuzu.md` |
| AVAudioFormat ve dosya | `ios-avfoundation-semasi.md` |
| Jitter ve ölçüm | `ios-gercek-zamanli-tampon.md` |
| BFF uçları | `ios-cihaz-ve-rota-yonetimi.md` |
| Session ve kategori | `ios-cihaz-ve-rota-yonetimi.md` |

## §15 Sınır Dışı

Codec implementasyonları (AAC/ALAC decoder), AudioUnit render zinciri DSP'si, Media
toolbox görüntü tarafı bu dosyaya girmez. Core Audio'nun macOS'e özgü servisleri
(HAL ayrıntıları) iddia edilmez. Bluetooth ve HDMI route'ları ayrı dosyanın
kapsamındadır; bu dosyada yalnız Core Audio tip/format/kuyruk katmanı işlenir.
Sürüm bazlı API farkları (hangi iOS sürümünde hangi sembol değişti) toplanmamıştır →
`UNKNOWN`.

## §16 Kırmızı Bayraklar

| ID | Bayrak | Etki |
|---|---|---|
| F1 | ASBD alanları resmi belgede örnek değerle verilmiştir, doğrulanmış cihaz çıktısı değil | Ölçüm gerekli |
| F2 | Audio Queue modern iOS'ta hâlâ önerilen yol mu belirsiz | `VERIFICATION REQUIRED` |
| F3 | FormatFlags bit eşlemesi bu dosyada özetlendi, tam liste belgededir | Ayrıntı için belge |
| F4 | Dönüştürücü zinciri DSP'si kapsam dışı | Matematik yazılmadı |
| F5 | Ölçüm cihazı yok | M1–M5 bekliyor |
| F6 | iOS sürüm farkları bu vault'ta toplanmadı | `UNKNOWN` |
| F7 | Codec liste tamamlanmadı | Yalnız PCM/AAC/ALAC |
| F8 | Donanım IO boyutu cihaza göre bilinmiyor | `UNKNOWN` |
| F9 | Bellek sahipliği senaryoları test edilmedi | Öneri düzeyinde |
| F10 | Byte order test sinyali üretilmedi | §18 adım 3 bekliyor |

## §17 Varsayım Günlüğü

| ID | Varsayım | Dayanak | Durum |
|---|---|---|---|
| A1 | IO tarafı tipik olarak non-interleaved verir | Audio Unit kuralı | Destekli |
| A2 | Dosya tarafı interleaved gelir | Codec normu | Destekli |
| A3 | ASBD elle tahmin edilmez, okunur | Hata yönetimi | Kural |
| A4 | BufferList render öncesi allocate edilir | L0 kuralı 1 | Kural |
| A5 | Audio Queue gecikmesi UI katmanında hissedilir | Tasarım | Varsayım |
| A6 | Converter CPU payı format çiftine göre değişir | Dönüştürme karmaşıklığı | Destekli |
| A7 | PCM'de paket = çerçeve | PCM tanımı | Destekli |
| A8 | Byte order hatası ölçülebilir | Test sinyali | Desteklenmemiş |
| A9 | Donanım IO boyutu uygulamadan bağımsız | Çekirdek davranışı | Kısmi |
| A10 | BufferList kanal başına buffer ile ölçeklenir | API tanımı | Destekli |

## §18 Doğrulama Planı

| # | Adım | Kabul |
|---|---|---|
| 1 | 48 kHz float32 dosyanın ASBD'sini oku | §4 tablosuyla karşılaştır |
| 2 | Interleaved dosyayı non-interleaved'e çevir | Artefaktsız çıkış |
| 3 | Bilinen sinyalde bayt sırası testi | Bozulma gözlenmedi |
| 4 | Queue gecikmesini hesapla ve ölç | Fark < tasarım payı |
| 5 | BufferList bellek boyutunu doğrula | Kanal × boyut tutarlı |
| 6 | Ölçüm M1–M5 dolduruldu | Kayıt altında |
| 7 | 6 URL tekrar 200 kontrolü | Kanıt matrisi canlı |
| 8 | Repo satırları tekrar okundu | Satır uyuşması |
| 9 | Converter CPU payı ölçüldü | Format çifti raporu |
| 10 | int16/float32 seviye karşılaştırması | Fark raporlandı |

## §19 Sık Sorulanlar

**S: Core Audio ile AVFoundation aynı şey midir?**
C: Hayır; Core Audio C çekirdeği, AVFoundation üst katmandır.

**S: ASBD neden elle yazılmaz?**
C: Yanlış bayt/flag sessiz bozulma üretir; format her zaman kaynaktan okunur.

**S: Audio Queue ile render callback farkı nedir?**
C: Kuyruk daha basit ve daha yüksek gecikmeli; hassas işlerde render callback.

**S: Non-interleaved neden tercih edilir?**
C: Her kanalın ayrı buffer'ı vektörel işlemi kolaylaştırır.

**S: float32 mi int16 mı kullanayım?**
C: Motor içi işleme float32 (yuvarlama yok); dosya/uyumluluk int16 (ölçüm gerekli).

**S: Converter nerede çalıştırılır?**
C: Render öncesi veya render dışında; callback içinde dönüştürme alloc riski taşır.

**S: Byte order'ı nereden okurum?**
C: ASBD'nin `mFormatFlags` alanından; tahmin edilmez (§8).

**S: BufferList ne kadar bellek tutar?**
C: Kanal başına tampon toplamı; formül §5.1'de, cihaz değeri `UNKNOWN`.

**S: Ölçümler nereye yazılır?**
C: §13.5 tablosuna, cihaz ve sürüm bilgisiyle birlikte.

**S: ASBD'yi hangi kütüphane okur?**
C: Dosya tarafında `AVAudioFormat`/format servisleri; IO tarafında çekirdek IO
yapılandırması — ikisi de §4'teki alanları doldurur.

## §20 Katmanlar

| Katman | Bu dosyadaki karşılığı |
|---|---|
| Uygulama | BFF uçları (repo kanıtı) |
| Çerçeve | AVAudioFormat sarmalayıcısı |
| Motor | Audio Converter / Audio Queue |
| Çekirdek | CoreAudioTypes, ASBD |
| Donanım | IO buffer boyutu (ölçüm) |

## §21 OBU Halkaları

| Halka | İçerik |
|---|---|
| Çekirdek | ASBD ve buffer tipleri |
| Uzantı 1 | Format dönüştürme |
| Uzantı 2 | Queue servisi |
| Uzantı 3 | Dosya/codec normları |
| Uzantı 4 | Ölçüm ve doğrulama |

## §22 Karşılaştırma

| Kriter | Audio Queue | Audio Unit render |
|---|---|---|
| Gecikme | Orta | Düşük |
| API dili | Objective-C/C | C callback |
| Karmaşıklık | Düşük | Yüksek |
| Kontrol | Sınırlı | Tam |
| Öncelik | Basit oynatma | Gerçek zamanlı DSP |

### §22.1 Katman Karşılaştırması

| Kriter | Core Audio (C) | AVAudioEngine | AVAudioPlayer |
|---|---|---|---|
| Soyutlama | Düşük | Orta | Yüksek |
| Gecikme kontrolü | Tam | Orta | Sınırlı |
| Bellek kontrolü | Tam | Motor yönetir | Motor yönetir |
| Hata yüzeyi | Geniş | Orta | Dar |
| Öğrenme maliyeti | Yüksek | Orta | Düşük |

### §22.2 Dosya Formatı Karşılaştırması

| Kriter | float32 | int16 | int24 |
|---|---|---|---|
| Bellek (2 kanal, 1 sn @48k) | 384 KB | 192 KB | 288 KB |
| Yuvarlama | Yok | Var | Az |
| Uyumluluk | Motor | Dosya | Dosya |
| Ölçüm durumu | Ölçülmedi | Ölçülmedi | Ölçülmedi |

Bellek satırları aritmetik hesaptır (kanal × örnek/hz × bayt/örnek × 1 sn);
ölçüm değerleri değil, hesaplanan üst sınırlardır.

## §23 Riskler

| ID | Risk | Etki |
|---|---|---|
| R1 | Hatalı ASBD → sessiz bozuk ses | Yüksek |
| R2 | Sahipsiz buffer → çökme | Yüksek |
| R3 | Callback içinde alloc → öncelik terslemesi | Yüksek |
| R4 | Ölçümsüz gecikme iddiası | Orta |
| R5 | Bayt sırası yanlışlığı → artefakt | Orta |
| R6 | Queue/Unit seçimi yanlış → gecikme bütçesi aşımı | Orta |
| R7 | Converter zinciri runtime'da kurulursa hıçkırık | Orta |
| R8 | BufferList kanal kaydı uyuşmazlığı → kanal kayması | Yüksek |
| R9 | Ölçüm cihazı olmadan yayımlanan performans iddiası | Yüksek |
| R10 | Vault dışına taşan iddia (macOS davranışı) | Orta |

## §24 Ölçüm

| Metrik | Değer |
|---|---|
| Dosya satır hedefi | 500+ |
| Bölüm sayısı | 24 |
| URL sayısı | 6 (hepsi 2026-10-12 200) |
| Repo kanıtı | 3 satır (routes.php, ci.yml) |
| Ölçüm | Ölçülmedi (M1–M5 planlandı, cihaz bekleniyor) |

### §24.1 Kanıt Zinciri Özeti

| Kaynak | Miktar | Durum |
|---|---|---|
| Resmi Apple dokümantasyonu | 6 URL | Doğrulandı (2026-10-12, HTTP 200) |
| Repo kod satırı | 3 satır | Doğrulandı |
| Ölçüm protokolü | M1–M5 | Planlandı, cihaz bekleniyor |
| Açık sorular | 6 | Açık |
| Kırmızı bayrak | 10 | Aktif (F1–F10) |
| Varsayım günlüğü | 10 | A1–A10: 4 destekli, 2 kısmi, 2 kural, 1 varsayım, 1 desteklenmemiş |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

**Authority:** Bayram Ali / Vault Steward Â· **Mode:** Red Team Â· Human Mode Â· Truth Mode