---
title: "CoreMusic Vision"
type: product-vision
version: 1.0.0
status: active
authority: SSOT
---

# CoreMusic Vision

## 1. Genel Vizyon

CoreMusic; dijital müzik tüketiminin, arşiv yönetiminin, profesyonel ses işlemenin ve donanım entegrasyonunun parçalandığı modern ses ekosistemindeki temel yapısal problemleri çözmek amacıyla geliştirilmiş kurumsal seviyede bir **Dijital Ses Ekosistemi ve Merkezi Medya Yönetim Platformudur**.

### 1.1 Çözülen Temel Problemler

1. **Platform Bağımlılığı ve Veri Mülkiyeti Kaybı:** Günümüz müzik servisleri, kullanıcıları kapalı abonelik modellerine ve kayıplı (lossy) sıkıştırma formatlarına mahkûm etmekte; kullanıcıların yıllar içinde oluşturduğu kişisel arşiv ve çalma listeleri platformlar arası taşınamaz hale gelmektedir. CoreMusic, **Offline-First** ve kullanıcı mülkiyeti odaklı veri mimarisiyle koleksiyon bağımsızlığını güvence altına almayı hedefler.
2. **Donanım ve Yazılım Arasındaki Kopukluk:** Masaüstü oynatıcılar, araç içi bilgi-eğlence sistemleri, ev medya merkezleri ve stüdyo donanımları birbirinden kopuk, standart dışı ve senkronizasyondan yoksun yazılımlarla yönetilmektedir. CoreMusic; evden araca, gömülü sistemlerden profesyonel stüdyolara kadar uzanan birleşik bir yazılım ve donanım katmanı sağlamayı amaçlar.
3. **Yüksek Sadakatli (Hi-Fi) ve Düşük Gecikmeli Ses İhtiyacı:** Tüketici elektroniğinde kullanılan işletim sistemi mikserleri (Windows Audio Engine, Android AudioFlinger vb.) ses verisini yeniden örneklemekte (resampling), gecikmeye (latency) ve dinamik aralık kaybına yol açmaktadır. CoreMusic, C++20 tabanlı yerel ses motoru (Neva Engine) ve doğrudan donanım sürücüleriyle (ASIO, WASAPI Exclusive, I2S) stüdyo standardında kayıpsız ses iletimi sağlamak üzere tasarlanmıştır.

### 1.2 Mevcut Sistemlerden Temel Farklar

CoreMusic, basit bir medya oynatıcı (audio player) veya standart bir katalog servisi değildir:
- **Uçtan Uca Dikey Entegrasyon:** Veritabanı şemasından (18 BCNF DB) yerel C++ ses çekirdeğine, web arayüzünden (Vanilla JS ITCSS) özel tasarlanmış çok kanallı ses kartı donanımına (XMOS XU316 + PCM3168A) kadar tüm katmanlar entegre olarak kurgulanmıştır.
- **Hibrit İstemci-Sunucu ve Mikro Panel Mimarisi:** 10 bağımsız uzmanlık paneli ve 7 backend mikro servisi ile hem merkezi medya sunucusu hem de hafif istemciler olarak çalışabilen ölçeklenebilir bir yapı sunar.
- **Tavizsiz Güvenlik ve Veri Disiplini:** Katı katman bağımlılıkları (L0-L6), kurumsal düzeyde değişmez (immutable) 10 adımlı middleware hattı, sıfır ORM yaklaşımı ve ham PDO güvenliği ile yüksek performanslı veri koruması hedeflenmiştir.

### 1.3 Gelecek Hedefi

CoreMusic; gelecekte ev, mobil, araç ve stüdyo ses cihazlarının merkezi bir iletişim ve yönetim omurgası haline gelerek, yüksek çözünürlüklü dijital ses standartlarında referans bir açık-entegrasyonlu ekosistem olmayı hedeflemektedir.

---

## 2. Dijital Ses Ekosistemi Vizyonu

CoreMusic ekosistemi, kullanıcının bulunduğu ortamdan ve kullandığı donanımdan bağımsız olarak kesintisiz, yüksek sadakatli ve bağlam duyarlı bir ses deneyimi sunmak üzere tasarlanmıştır.

```
┌────────────────────────────────────────────────────────────────────────┐
│                   COREMUSIC CENTRAL CLOUD / HOME NAS                   │
│         (api.coremusic.net · media.coremusic.net · auth.coremusic.net)  │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
       ┌────────────────────────────┼────────────────────────────┐
       ▼                            ▼                            ▼
┌───────────────┐            ┌───────────────┐            ┌───────────────┐
│  HOME MEDIA   │            │  CAR AUDIO    │            │  STUDIO & PRO │
│    CENTER     │            │  INFOTAINMENT │            │  WORKSTATION  │
│(home.coremusic)│            │(car.coremusic)│            │(studio/pro)   │
│ RPi5 / PC     │            │ RPi5 / Auto   │            │ ASIO 8.1 DSP  │
└───────┬───────┘            └───────┬───────┘            └───────┬───────┘
        │                            │                            │
        ▼                            ▼                            ▼
┌───────────────┐            ┌───────────────┐            ┌───────────────┐
│ Multi-Room    │            │ Low-Latency   │            │ Class AB Amp  │
│ WiFi Streaming│            │ Touch UI      │            │ 8-ch PCM3168A │
└───────────────┘            └───────────────┘            └───────────────┘
```

### 2.1 Cihazlar Arası Entegrasyon Senaryoları

1. **Ev Medya Merkezi (Home Media Center — `home.coremusic.net`):**
   - Ev ağında NAS veya Raspberry Pi 5 üzerinde konumlanan merkezi kütüphane sunucusu.
   - WebRTC ve WebSocket tabanlı çok odalı (multi-room) kablosuz ses dağıtımı.
   - Dinamik ekran modları ve uzaktan kumanda desteği ile salon ve ev sinema sistemlerine tam uyum.
2. **Araç İçi Bilgi-Eğlence (Car Infotainment — `car.coremusic.net`):**
   - Dokunmatik ekranlara özel optimize edilmiş, dikkat dağıtmayan yüksek kontrastlı arayüz (minimum 48px dokunmatik hedefler).
   - Raspberry Pi 5 + PCM3168A donanımı üzerinden doğrudan araç ses sistemine düşük gecikmeli, çok kanallı çıkış.
   - Çevrimdışı önbellek (Offline Cache) ile bağlantı kesintilerinde dahi kesintisiz müzik deneyimi.
3. **Masaüstü İş İstasyonları (`music.coremusic.net`):**
   - Windows (Tier 1), Linux (Tier 2) ve macOS (Tier 3) sistemlerinde yerel ses sunucuları (WASAPI Exclusive, ALSA, CoreAudio).
   - Arşivleme, etiketleme ve kütüphane düzenleme için gelişmiş yönetim ekranları.
4. **Profesyonel Stüdyo (`studio.coremusic.net` & `pro.coremusic.net`):**
   - ASIO SDK entegrasyonu ile 32-bit float hassasiyetinde <10ms gecikmeli ses akışı.
   - 8.1 surround izleme (monitoring), 31-band parametrik ekolayzır ve donanımsal DSP matrisi kontrol panelleri.
5. **Gömülü (Embedded) Dokunmatik Cihazlar:**
   - 1024×600 çözünürlüğündeki dokunmatik ekranlar (Linux ARM / RPi5) için piksel hassasiyetinde özel render modu (Tier 1 Embedded Layout).

### 2.2 Cihaz Bağımsız Kullanıcı Deneyimi

Sistem; kullanıcının kimliğini, aktif oynatma sırasını, dinleme pozisyonunu ve ses tercihlerini merkezi veritabanı senkronizasyonu ile saklar. Kullanıcının evinde başlattığı bir albüm, aracına geçtiğinde kaldığı saniyeden devam edebilmekte; oturma odasındaki ses ayarları ve kişisel DSP profilleri tüm yetkili cihazlara kesintisiz aktarılmaktadır.

---

## 3. Merkezi Medya Yönetimi

CoreMusic, basit bir çalma listesi yürütücüsü değil; büyük ölçekli ve yüksek çözünürlüklü dijital medya koleksiyonlarını organize etmek üzere geliştirilmiş kapsamlı bir **Merkezi Medya Yönetim Platformudur**.

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      MERKEZİ MEDYA YÖNETİMİ                             │
├───────────────────┬───────────────────┬─────────────────────────────────┤
│ KÜTÜPHANE VE DİZİN│ EDİNİM VE ARŞİV   │ ZENGİNLEŞTİRME VE İLİŞKİLENDİRME│
├───────────────────┼───────────────────┼─────────────────────────────────┤
│ • 18 BCNF DB      │ • Otonom İndirme  │ • Dinamik Akustik Analiz        │
│ • UUID v7 Zamanlı │ • FLAC (24/32-bit)│ • Çok Katmanlı Sanatçı/Grup     │
│ • Çok Diskli Yapı │ • Hash Bütünlüğü  │ • Zaman Kodlu Sözler (Lyrics)   │
│ • Kayıpsız Depo   │ • Çift Depolama   │ • Akıllı Benzerlik Matrisi      │
└───────────────────┴───────────────────┴─────────────────────────────────┘
```

### 3.1 Neden Sadece Bir Oynatıcı Değil?

Geleneksel medya oynatıcılar yerel dosya sistemindeki etiketleri yüzeysel biçimde okurken, CoreMusic medyayı ilişkisel bir veri varlığı olarak ele alır:
- **Kapsamlı Medya Kütüphanesi:** 24-bit/32-bit FLAC, ALAC, WAV gibi kayıpsız formatların yanı sıra yüksek bit oranlı MP3 (320kbps) ve AAC dosyalarını akustik özellikleriyle birlikte indeksler.
- **Çok Boyutlu Albüm Yönetimi:** Tekli parçalar, çok diskli stüdyo albümleri, derlemeler ve özel sürümler hiyerarşik olarak modellenmiştir (`coremusic_albums` veritabanı).
- **Ayrıntılı Sanatçı Organizasyonu:** Sanatçılar; grup üyeleri, yapımcılar, besteciler, icracılar ve konuk müzisyenler düzeyinde ilişkisel olarak kataloglanır (`coremusic_musics` ve `coremusic_catalog` veritabanları).
- **Zengin Metadata ve Görsel Yönetimi:** Şarkı sözleri (zaman damgalı senkronize sözler dahil), albüm kapak sanatları, sanatçı biyografileri ve müzik türü hiyerarşisi otomatik olarak doğrulanıp ilişkilendirilir.
- **Otonom İndirme ve Arşivleme Pipeline'ı (`download.coremusic.net`):** YouTube ve harici kaynaklardan kayıpsız FLAC/MP3 formatlarında medya edinimi, metadata zenginleştirmesi ve kütüphaneye otonom katılım mimari olarak kurgulanmıştır.
- **Çift Modlu Depolama Stratejisi (Dual-Mode Storage — ADR-027):** Sık erişilen parçalar için ultra hızlı yerel SSD/NVMe önbelleği, devasa koleksiyonlar için ise NAS ve nesne depolama entegrasyonu planlanmıştır.

---

## 4. Yazılım Mimari Vizyonu

CoreMusic yazılım mimarisi; kurumsal yazılım mühendisliğinin en katı prensipleri olan **Clean Architecture**, **SOLID**, **Hexagonal Architecture (Ports & Adapters)** ve **Domain-Driven Design (DDD)** esas alınarak tasarlanmıştır.

```
┌────────────────────────────────────────────────────────────────────────┐
│  L6 — Electronics (Hardware, Firmware, C++20 Neva Audio Engine)        │
├────────────────────────────────────────────────────────────────────────┤
│  L5 — Services    (Application Services, CQRS Handlers, Event Bus)     │
├────────────────────────────────────────────────────────────────────────┤
│  L4 — Domain      (Business Rules, Pure Entities, Value Objects, Agg.) │
├────────────────────────────────────────────────────────────────────────┤
│  L3 — Presentation(Vanilla JS ES6+, ITCSS 9-Layer, Component Inventory)│
├────────────────────────────────────────────────────────────────────────┤
│  L2 — Routing     (PHP 8.4 PageRouter, Subdomain Routing, API Gateway) │
├────────────────────────────────────────────────────────────────────────┤
│  L1 — Security    (Immutable 10-Step Middleware, AES-256-GCM, CSP)     │
├────────────────────────────────────────────────────────────────────────┤
│  L0 — Infrastructure (18 BCNF DB, PDO MySQL 9, APCu Cache, Filesystem) │
└────────────────────────────────────────────────────────────────────────┘
```

### 4.1 Katmanlı Mimari Modeli (L0-L6)

Sistem 7 hiyerarşik katmandan oluşur. Bağımlılık kuralı kesin ve tek yönlüdür:
`L6 → L5 → L4 → L3 → L2 → L1 → L0`. Ters yönlü bağımlılıklar (**Layer Violation**) mimari olarak yasaklanmıştır.

1. **L0 — Altyapı (Infrastructure):** MySQL 9 veri tabanları (raw PDO), APCu önbellekleme, disk dosya sistemleri, şifreli anahtar kasası (Credential Vault).
2. **L1 — Güvenlik (Security):** Oturum yönetimi, değişmez middleware hattı, CSRF koruması, CSP nonce yönetimi, Argon2id ve AES-256-GCM kriptografik servisleri.
3. **L2 — Yönlendirme (Routing):** Merkezi `PageRouter`, subdomain ayrımı, istemci SPA yönlendirmesi, URL normalizasyonu ve API Gateway sözleşmeleri.
4. **L3 — Sunum (Presentation):** Vanilla JS ES6+, ITCSS 9-katmanlı CSS mimarisi, TrustedTypes, DOMParser tabanlı güvenli UI render zinciri.
5. **L4 — Alan (Domain):** Çerçeve bağımsız saf iş kuralları; `User`, `Media`, `Device`, `Playlist`, `Session` ve `DSPPresets` agregatları.
6. **L5 — Servisler (Services):** Uygulama servisleri, CQRS komut ve sorgu işleyicileri, PSR-14 uyumlu Event Bus.
7. **L6 — Elektronik & Ses (Electronics):** C++20 ses çekirdeği, XMOS USB kontrolü, PCM3168A donanım sürücüleri ve ses filtreleme algoritmaları.

### 4.2 Mimari İlkeler ve İletişim Standartları

- **API-First & Contract-First Yaklaşımı (ADR-084):** Hiçbir servis veya uç nokta sözleşmesiz kodlanmaz. Tasarım akışı `OpenAPI Sözleşmesi → DTO → Arayüz Sözleşmesi → Doğrulama → Use Case → Kod` sırasını takip eder.
- **BFF (Backend for Frontend) Deseni:** Farklı istemcilerin veri gereksinimlerini optimize etmek amacıyla `SPA BFF` (tam veri), `Mobile BFF` (hafif veri), `Embedded BFF` (ultra minimal, gzip sıkıştırmalı) ve `Car BFF` (dokunma odaklı veri) modelleri öngörülmüştür.
- **CQRS (Command Query Responsibility Segregation):**
  - *Yazma Hattı:* `Command → Use Case → Repository → MySQL Master`
  - *Okuma Hattı:* `Query → Read Model → Cache / Read Replica → Yanıt`
- **Olay Güdümlü Mimari (Event-Driven Architecture — ADR-086):** Servisler arası doğrudan sıkı bağımlılıklar yerine PSR-14 Event Bus üzerinden gevşek bağlı (loosely coupled) olay tabanlı haberleşme kullanılır.
- **Sıfır ORM, Ham PDO Disiplini (ADR-002):** Performans kaybını ve gizli N+1 sorgu problemlerini engellemek için ORM kütüphaneleri yasaklanmış; parametrik sorgularla ham PDO kullanımı zorunlu tutulmuştur.

---

## 5. Ses Teknolojileri Vizyonu (Neva Engine)

CoreMusic'in ses kalbinde, yüksek çözünürlüklü ve sıfır gecikmeli ses işleme için özel olarak tasarlanan **Neva Engine** yer almaktadır.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        NEVA AUDIO ENGINE (C++20)                       │
├────────────────────────────────────────────────────────────────────────┤
│ Real-Time Callback (noexcept, lock-free, zero-allocation, alignas(64)) │
│                                                                        │
│   Input PCM (Float32, 48kHz / 192kHz)                                  │
│         │                                                              │
│         ▼                                                              │
│   ┌────────────────────────────────────────────────────────────────┐   │
│   │ 31-Band Parametric EQ (Linkwitz-Riley / Biquad Filters)        │   │
│   └───────────────────────────────┬────────────────────────────────┘   │
│                                   ▼                                    │
│   ┌────────────────────────────────────────────────────────────────┐   │
│   │ Studio Reverb Simulator (Concert Hall, Room, Studio, Arena)    │   │
│   └───────────────────────────────┬────────────────────────────────┘   │
│                                   ▼                                    │
│   ┌────────────────────────────────────────────────────────────────┐   │
│   │ Dynamic Range Compressor & Soft-Knee Processor                 │   │
│   └───────────────────────────────┬────────────────────────────────┘   │
│                                   ▼                                    │
│   ┌────────────────────────────────────────────────────────────────┐   │
│   │ True Peak Brickwall Limiter & DC Offset Filter                 │   │
│   └───────────────────────────────┬────────────────────────────────┘   │
│                                   ▼                                    │
│   8.1 Surround Routing Matrix (Bass Management Crossover @ 80Hz)       │
│                                   │                                    │
│         ┌─────────────────────────┴─────────────────────────┐          │
│         ▼                                                   ▼          │
│   ASIO 2.3.4 Driver (<10ms)                           WASAPI Exclusive │
└────────────────────────────────────────────────────────────────────────┘
```

### 5.1 C++20 Ses Motoru Mimarisi ve Standartları

Neva Engine, C++20 standardında modern RAII ilkeleri ve JUCE 9 altyapısı temel alınarak kurgulanmıştır.

- **Sıfır Bellek Tahsisi Kuralı (Zero-Allocation):** Gerçek zamanlı ses döngüsü (Audio Thread) içerisinde `malloc()`, `free()`, `new`, `delete`, dinamik dizi büyütme (`std::vector::push_back`) ve bloklayıcı G/Ç (I/O) işlemleri kesinlikle yasaktır. Tüm bellek önden tahsis edilir (pre-allocated).
- **Kilitsiz Eşzamanlılık (Lock-Free Thread Model):** Ses akışında önbellek hatası ve işlemci takılmalarını engellemek için mutex kullanımı yasaklanmıştır. Veri aktarımı `std::atomic` değişkenler ve dairesel tamponlar (circular ring buffer) üzerinden yürütülür.
- **İşlemci Seviyesinde Optimizasyon:** Çok çekirdekli sistemlerde false sharing olgusunu engellemek amacıyla okuma/yazma işaretçileri 64-bayt önbellek satırına hizalanır (`alignas(64)`).
- **Sinyal Hassasiyeti:** Dahili ses işleme zincirinde 32-bit kayan noktalı PCM (`Float32`) formatı ve 48kHz temel (192kHz'e kadar genişletilebilir) örnekleme frekansı kullanılır.

### 5.2 Profesyonel DSP İşleme Zinciri (ADR-025)

1. **31-Band Parametrik Ekolayzır:** 20Hz ile 20kHz aralığında 1/3 oktav aralıklı tam parametrik frekans kontrolü, Q-faktörü ve kazanç ayarı.
2. **Akustik Reverb İşlemcisi:** Oda, profesyonel stüdyo, geniş konser salonu ve açık alan akustik modellerini simüle eden gecikme matrisi.
3. **Dinamik Kompresör:** Sinyalin dinamik aralığını kontrol eden, yumuşak geçişli (soft-knee) stüdyo kompresörü.
4. **Peak Limiter:** Çıkış katında dijital distorsiyonu (clipping) engelleyen ultra hızlı tepkili tepe sınırlayıcı.
5. **8.1 Surround ve Aktif Bass Yönetimi:** 8 uydu kanal ve 1 bağımsız LFE subwoofer kanalı. 4. derece Linkwitz-Riley 80Hz aktif crossover filtreleri ile bas frekanslarının subwoofer'a yönlendirilmesi.

### 5.3 Düşük Gecikmeli Sürücü ve Donanım Hattı

Neva Engine, Windows üzerinde Steinberg ASIO SDK 2.3.4 ile doğrudan donanıma bağlanarak **<10ms** gecikme hedefini; WASAPI Exclusive modunda ise **<15ms** gecikmeyi karşılayacak biçimde mimarilendirilmiştir. Linux ortamında ALSA/PipeWire, macOS ortamında CoreAudio, gömülü sistemlerde ise doğrudan SoC I2S arayüzü hedeflenmiştir.

---

## 6. Donanım ve Embedded Vizyonu

CoreMusic vizyonu, salt bir yazılım projesi olmanın ötesinde, tescilli donanım bileşenleri ile yazılımın dikey entegrasyonunu sağlamayı amaçlamaktadır.

```
┌────────────────────────────────────────────────────────────────────────┐
│               COREMUSIC ÖZEL DONANIM VE GÖMÜLÜ MİMARİSİ                │
├────────────────────────────────────────────────────────────────────────┤
│                                                                        │
│   HOST (Raspberry Pi 5 / PC)                                           │
│         │                                                              │
│         ├── USB Audio Class 2.0 (High-Speed 480 Mbps)                  │
│         ▼                                                              │
│   XMOS XU316 Multi-Core Audio MCU                                      │
│   ├── Asenkron USB Audio Alıcı                                         │
│   ├── Donanımsal DSP / I2S & TDM Sinyal Yönlendirici                   │
│   └─────┬───────────────────────────────┬────────────────────────────┘ │
│         │ TDM / I2S (8 Kanal Çıkış)     │ I2S (6 Kanal Giriş)          │
│         ▼                               ▲                              │
│   TI PCM3168A Yüksek Performanslı Codec ┴                              │
│   ├── 8-Kanal DAC (24-bit, 192kHz, SNR 112dB, THD+N -93dB)             │
│   └── 6-Kanal ADC (24-bit, 96kHz, SNR 107dB, THD+N -93dB)              │
│         │                                                              │
│         ▼                                                              │
│   Analog Filtreleme & Tam Dengeli (Balanced) Tampon Katı               │
│         │                                                              │
│         ▼                                                              │
│   8.1 Kanal Class AB Amplifikatör Katı                                 │
│   ├── 100W RMS @ 8Ω Bağımsız Kanal Gücü                                │
│   ├── THD+N <0.01% @ 1kHz, SNR >100dB Dinamik Aralık                   │
│   ├── Simetrik ±42V DC Besleme Rayları                                 │
│   └── Aktif Koruma: >0.5V DC Offset Röleli Hoparlör Koruma Devresi     │
└────────────────────────────────────────────────────────────────────────┘
```

### 6.1 Gömülü Sistemler ve İşlemci Altyapısı

- **Raspberry Pi 5 (ARM64):** Ev medya sunucusu ve araç içi gömülü ünitelerde temel hesaplama platformu olarak planlanmıştır. Düşük güç tüketimi, doğrudan I2S/TDM ses yolları ve yüksek G/Ç performansı sunar.
- **XMOS XU316 USB Ses İşlemcisi:** 16 çekirdekli XCore mimarisi üzerinde çalışan, sıfır gecikmeli USB Audio Class 2.0 desteği sunan, donanımsal ses paketleme ve DSP yönlendirme kontrolcüsü.

### 6.2 Codec ve DAC Seçimi (ADR-038)

- **Texas Instruments PCM3168A:** 8 kanallı çıkış (DAC) ve 6 kanallı giriş (ADC) yeteneğine sahip, 24-bit 192kHz destekli, 112dB SNR değerine sahip profesyonel ses dönüştürücüsü ana ses kartı standardı olarak belirlenmiştir.
- **PCM5122 Reddi (H001 Kararı):** Yalnızca 2 kanal (stereo) çıkış desteği sunduğu ve 8.1 surround hedefini karşılayamadığı için mimariden kesin olarak elenmiştir.
- **AKM AK4458 (Gelecek Vizyon Alternatifi):** 32-bit 768kHz desteği sunan, audiophile sınıfı üst düzey stüdyo sürümleri için opsiyonel bir DAC adayı olarak dokümante edilmiştir.

### 6.3 Özel Ses Donanımı ve Amplifikatör Vizyonu

Planlanan CoreMusic ses donanımı;
- Çok kanallı Class AB güç amplifikatörlerini (100W @ 8Ω, THD+N <0.01%, SNR >100dB, ±42V besleme),
- DC Offset algılamalı (>0.5V DC anında kesme) elektromekanik röle koruma devrelerini,
- Elektromanyetik parazit (EMI/RFI) yalıtımlı PCB tasarımlarını
bünyesinde barındıran entegre bir donanım cihazı olarak hedeflenmektedir.

---

## 7. Yapay Zekâ Vizyonu

CoreMusic platformundaki yapay zekâ bileşenlerinin temel varlık sebebi; kullanıcının yerine kararlar almak ya da kontrolü ele geçirmek değil, **kullanıcı deneyimini güçlendirmek, akustik konforu maksimize etmek ve kütüphane yönetimini akıllı kılmaktır**.

```
┌────────────────────────────────────────────────────────────────────────┐
│                        COREMUSIC AI PLATFORM                           │
├────────────────────────────────────────────────────────────────────────┤
│                           GİRDİ KATMANI                                │
│  • Dinleme Geçmişi ve Frekansı        • Zaman, Mekan ve Cihaz Türü     │
│  • Parça Atlama/Tekrar Sinyalleri     • Parça Akustik Özellikleri      │
├────────────────────────────────────────────────────────────────────────┤
│                           İŞLEME KATMANI                               │
│  ┌─────────────────────────┐             ┌──────────────────────────┐  │
│  │ KULLANICI MODELLEME     │             │ AKUSTİK VE İÇERİK ANALİZİ│  │
│  │ Dinleme tercih profili  │             │ BPM, ton, enerji, valans │  │
│  │ (coremusic_ai)          │             │ Spektral yoğunluk        │  │
│  └────────────┬────────────┘             └────────────┬─────────────┘  │
│               │                                       │                │
│               └───────────────────┬───────────────────┘                │
│                                   ▼                                    │
├────────────────────────────────────────────────────────────────────────┤
│                           ÇIKTI VE FAYDA                               │
│  • Kişiselleştirilmiş Dinamik Akış ve Çalma Listeleri                  │
│  • Eksik Metadata / Tür / Ruh Hali Otomatik Etiketleme                 │
│  • Ortam ve Cihaz Tipine Göre Adaptif EQ / DSP Ön Ayar Önerileri       │
│  • Otonom İndirme ve Kütüphane Zenginleştirme Kuyruğu                  │
└────────────────────────────────────────────────────────────────────────┘
```

### 7.1 Kullanıcı Davranışı ve Akustik Özellik Analizi

`coremusic_ai` veritabanı altında 6 ilişkisel tablo (`user_preference_profiles`, `listening_features`, `recommendation_history`, `audio_features`, `model_versions`, `training_jobs`) üzerinden yürütülen AI mimarisi:
- Kullanıcının günün farklı saatlerindeki enerji seviyesi tercihlerini,
- Belirli cihazlardaki (araba, ev, kulaklık) dinleme eğilimlerini,
- Parçaların akustik parmak izlerini (BPM, ton, mod, akustik yoğunluk, spektral denge)
analiz ederek yüksek isabetli bağlamsal modeller üretmeyi amaçlar.

### 7.2 Akıllı Medya Yönetimi ve Adaptif Deneyim

1. **Otonom Katalog Düzenleme:** Eksik albüm kapaklarını tamamlama, sanatçı isimlerindeki karmaşaları çözme, doğru müzik türlerini ve ruh hallerini (mood) eşleme.
2. **Akıllı Öneri ve Kesintisiz Çalma:** Çalma listesi sona erdiğinde kullanıcının müzikal zevkine ve anlık atmosfere tam uyum sağlayan otonom parça devamlılığı.
3. **Akustik Profil Adaptasyonu:** Dinlenen cihazın özelliklerine ve müzik türüne göre Neva Engine DSP parametrelerini (EQ eğrileri, dinamik aralık sıkıştırma seviyeleri) kullanıcı onayına sunarak önerme.

---

## 8. Güvenlik ve Veri Yönetimi Vizyonu

CoreMusic, büyük medya arşivlerini ve kullanıcı verilerini en yüksek endüstri standartlarında korumak üzere tasarlanmış tavizsiz bir güvenlik ve veri mimarisine sahiptir.

```
┌────────────────────────────────────────────────────────────────────────┐
│                  IMMUTABLE 10-ADIMLI MIDDLEWARE HATTI                  │
├────────────────────────────────────────────────────────────────────────┤
│ 1. OriginCheckMiddleware()     — Köken doğrulama (whitelist CORS)      │
│ 2. CorsMiddleware()            — CORS başlık yönetimi (strict)         │
│ 3. RateLimiterMiddleware()     — APCu tabanlı hız kısıtı (60 req/60s)  │
│ 4. SecurityHeadersMiddleware() — Nonce üret, strict-dynamic CSP, HSTS  │
│ 5. SessionManagerMiddleware()  — Session başlat, nonce'u session'a yaz │
│ 6. CsrfMiddleware()            — csrf_token ve hash_equals doğrulaması │
│ 7. BypassAuthMiddleware()      — Test bypass hattı (üretimde pasif)    │
│ 8. AuthMiddleware()            — Kimlik bilgisi enjeksiyonu (RS256/Sess│
│ 9. PermissionMiddleware()      — Rol bazlı yetki kontrolü (RBAC)       │
│ 10.ValidationMiddleware()      — Request / DTO katı şema doğrulaması   │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                        18 BCNF VERİTABANI VE VERİ GÜVENLİĞİ            │
│  • Raw PDO (Sıfır ORM)          • SELECT * Kesinlikle Yasak            │
│  • UUID v7 Zaman Sıralı PK      • AES-256-GCM Credential Vault         │
│  • Argon2id Parola Koruması     • Güvenli HTTPOnly Cookie Oturumları   │
└────────────────────────────────────────────────────────────────────────┘
```

### 8.1 18 BCNF İzole Veritabanı Mimarisi (ADR-040)

Veri bütünlüğünü mutlak kılmak, veri tekrarını ve güncelleme anomalilerini engellemek amacıyla sistem 18 adet Boyce-Codd Normal Formu (BCNF) standardında normalize edilmiş veritabanı (toplam 156 tablo) üzerinde çalışır:
- `coremusic_auth` (13 tablo), `coremusic_user` (7 tablo), `coremusic_musics` (22 tablo), `coremusic_albums` (5 tablo), `coremusic_playlist` (5 tablo), `coremusic_catalog` (8 tablo), `coremusic_logs` (22 tablo), `coremusic_media` (8 tablo), `coremusic_system` (17 tablo), `coremusic_social` (9 tablo), `coremusic_wireless` (5 tablo), `coremusic_ai` (6 tablo), `coremusic_api` (4 tablo), `coremusic_cms` (8 tablo), `coremusic_download` (4 tablo), `coremusic_neva` (4 tablo), `coremusic_studio` (6 tablo), `coremusic_patch` (3 tablo).

### 8.2 Güvenlik Standartları ve Savunma Mekanizmaları

- **OWASP Top 10:2025 Uyumluluğu:** En güncel güvenlik risklerine karşı proaktif önlemler.
- **Değişmez (Immutable) Middleware Sırası:** CSP nonce üretimi `SecurityHeadersMiddleware` (#4) içinde yapılır; `SessionManagerMiddleware` (#5) bu değeri oturuma bağlar. Sıranın değiştirilmesi CSP korumasını çökerteceğinden bu akış sabittir.
- **Kriptografik Güvenlik:**
  - *Hassas Veriler ve API Anahtarları:* NIST SP 800-38D standardında AES-256-GCM (96-bit IV, 16-bayt doğrulama etiketi).
  - *Kullanıcı Parolaları:* Argon2id (64MB bellek maliyeti, 4 iterasyon, 2 iş parçacığı).
  - *CSRF Koruması:* Güvenli `csrf_token` anahtarı ve zamanlama saldırılarına karşı `hash_equals()` doğrulaması.
  - *İçerik Güvenliği:* Katı nonce tabanlı Content Security Policy (`strict-dynamic`), X-Frame-Options DENY, HSTS zorunluluğu.
- **Performans ve Büyük Veri Optimizasyonu:**
  - BCNF tablolarında `UUID v7` kullanımı ile zaman sıralı indeks optimizasyonu sağlanarak büyük medya koleksiyonlarında sorgu gecikmesi engellenir.
  - L0 APCu önbellek adaptörü ile oturum ve rate limit doğrulamaları mikrosaniye düzeyinde tamamlanır.

---

## 9. Kullanıcı Deneyimi Vizyonu (UI/UX)

CoreMusic kullanıcı arayüzü; yüksek görsel derinlik, sıfır çerçeve yükü (no framework overhead), piksel sadakati ve çoklu cihaz uyumunu tek bir sistemde birleştiren özgün bir tasarım mühendisliğine dayanır.

```
┌────────────────────────────────────────────────────────────────────────┐
│             COREMUSIC FRONTEND MİMARİSİ VE UI DENEYİMİ                 │
├────────────────────────────────────────────────────────────────────────┤
│ TEKNOLOJİ     │ Vanilla JS ES6+ (Framework YASAK) · ITCSS 9-Katman· BEM│
│ TASARIM DİLİ  │ Glassmorphism (Cam Derinliği) · Katmanlı Gölgeler      │
│ RENK & TEMA   │ Dinamik Tema Motoru (ThemeEngine) · Gender & Dark/Light│
│ BİLEŞENLER    │ C01-C16 Kanonik Envanter · 19 Orijinal PNG Mockup SSOT │
├────────────────────────────────────────────────────────────────────────┤
│                  4-TIER RESPONSIVE RENDERING SİSTEMİ                   │
│                                                                        │
│   Tier 4: Phone        │ ≤767px      │ Kompakt tek sütun, touch 48px   │
│   Tier 1: Embedded     │ 1024×600    │ 42/58 Split Panel (RPi5 Referans│
│   Tier 1: Tablet       │ 768-1024px  │ Dokunmatik tablet uyumu         │
│   Tier 2: Wide Desktop │ 1025-2560px │ 3-Sütunlu zengin medya paneli   │
│   Tier 3: 4K Monitor/TV│ ≥2561px     │ No-Center (ortalamasız fluid)   │
│                        │             │ CSS zoom kademeli ölçekleme     │
└────────────────────────────────────────────────────────────────────────┘
```

### 9.1 Teknoloji ve Tasarım İlkeleri

- **Harici Framework Bağımsızlığı (ADR-001):** React, Vue veya Angular gibi sanal DOM (Virtual DOM) katmanları ve ağır bağımlılıklar reddedilmiştir. Doğrudan Vanilla JS ES6+, `DOMParser` ve `TrustedTypes` kullanılarak ultra hafif ve hızlı bir DOM manipülasyonu sağlanır.
- **ITCSS (Inverted Triangle CSS) & BEM Mimarisi:** CSS kuralları 9 katmanda (Settings, Tools, Generic, Elements, Objects, Components, Pages, Devices, Trumps) organize edilerek özgüllük (specificity) çakışmaları sıfıra indirilmiştir.
- **19 PNG Mockup ve C01-C16 Bileşen Otoritesi:** Tasarım kararlarında `.ai/.png/` altındaki 19 orijinal mockup görseli nihai referanstır (Guardrail #11). C01 (Navbar) ile C16 (Modal/Panel) arasındaki 16 kanonik bileşen BEM kurallarıyla kodlanır.
- **Tek Bileşen İlkesi (Guardrail #17):** Farklı cihazlar için ayrı HTML şablonları üretmek yasaktır. Tek bir anlamsal HTML yapısı, CSS özel değişkenleri (`var(--token)`) ve ortam sorguları (media queries) ile tüm ekranlara adapte olur.

### 9.2 Glassmorphism ve Modern Estetik

Arayüz; şeffaf cam paneller (`--glass-bg`), arka plan bulanıklığı (`backdrop-filter: blur()`), katmanlı derinlik gölgeleri (`--shadow-*`, `--card-shadow`) ve okunabilirliği artıran metin gölgeleri (`--ts-*`) ile modern ve etkileyici bir görsel dil sunar.

### 9.3 Dinamik Tema Motoru (ThemeEngine — ADR-044)

Kullanıcı tercihine ve profil verilerine göre sayfa yenilenmeden anında değişen tema altyapısı:
- **Cinsiyet / Kimlik Temaları:** Pembe/Magenta vurgulu (`female`), Mavi/Cyan vurgulu (`male`), Nötr/Gri vurgulu (`neutral`).
- **Karanlık / Aydınlık Mod:** Koyu cam derinliğinden açık renkli temiz cam arayüzüne tam uyumlu renk ve kontrast geçişleri.

### 9.4 4K No-Center ve Geriye Dönük Uyumluluk Standartları

4K (3840×2160) ve üzeri ultra geniş ekranlarda arayüzü merkeze sıkıştıran geleneksel `max-width: 1200px; margin: 0 auto;` deseni yasaklanmıştır (**4K No-Center Kuralı**). Arayüz, sol kenardan hizalı olarak akışkan (fluid) biçimde genişler ve kademeli CSS zoom/buffer oranlarıyla ölçeklenir.

---

## 10. Gelecek Yol Haritası

CoreMusic'in stratejik yol haritası, hedeflenen vizyona kararlı ve metodolojik adımlarla ulaşmak üzere 3 ana faza ayrılmıştır.

```
┌────────────────────────────────────────────────────────────────────────┐
│                      COREMUSIC VİZYON YOL HARİTASI                     │
├────────────────────────────────────────────────────────────────────────┤
│ FAZ 1 — MVP (Temel Platform ve Yazılım Altyapısı)                     │
│ • PC / Laptop üzerinde çalışan hibrit SPA (PHP 8.4 + Vanilla JS)      │
│ • 18 BCNF Veritabanı ve güvenli altyapı servisleri                    │
│ • Temel medya kütüphanesi, oynatıcı ve 10 web paneli iskeleti         │
│ • Standart Web Audio ve masaüstü işletim sistemi ses çıkışları         │
├────────────────────────────────────────────────────────────────────────┤
│ FAZ 2 — PREMIUM (Donanım ve DSP Entegrasyonu)                          │
│ • C++20 Neva Engine optimizasyonu ve düşük gecikmeli sürücü hattı      │
│ • ASIO ve WASAPI Exclusive modları ile profesyonel ses işleme          │
│ • XMOS XU316 ve PCM3168A prototip donanım kartı entegrasyonu          │
│ • 31-Band Parametrik EQ, Reverb, Compressor DSP efekt zinciri          │
├────────────────────────────────────────────────────────────────────────┤
│ FAZ 3 — PROFESSIONAL (Tam Ekosistem ve Stüdyo Yayılımı)                │
│ • 8.1 Surround ses stüdyo iş istasyonu ve Class AB amfi donanımı      │
│ • Raspberry Pi 5 tabanlı Araç İçi Bilgi-Eğlence (Car Infotainment)     │
│ • Cihazlar arası WebRTC / P2P çok odalı (multi-room) ses senkronizasyonu│
│ • Gelişmiş yapay zekâ dinleme analitiği ve otonom arşiv yönetimi       │
└────────────────────────────────────────────────────────────────────────┘
```

### 10.1 Faz 1: Temel Platform ve Yazılım Çekirdeği (MVP)

- Mevcut kişisel bilgisayar ve yerel sunucu ortamlarında çalışan, hibrit SPA mimarisine sahip ana platformun olgunlaştırılması.
- PHP 8.4 PageRouter, merkezi kimlik yönetimi (`auth.coremusic.net`) ve 18 BCNF veritabanının eksiksiz devreye alınması.
- 10 web panelinin (music, admin, download, media, auth, home, car, studio, pro, landing) kullanıcı arayüzü sözleşmelerinin ve kanonik bileşenlerinin tamamlanması.
- Temel yerel dosya yönetimi ve akış altyapısının test edilmesi.

### 10.2 Faz 2: Ses Teknolojileri ve Donanım Entegrasyonu (Premium)

- Neva Engine (C++20) gerçek zamanlı çekirdeğinin masaüstü sistemlerde devreye alınması; ASIO SDK ve WASAPI Exclusive sürücü entegrasyonlarının tamamlanması.
- 31-band parametrik ekolayzır ve stüdyo efekt zincirinin gerçek zamanlı çalışmasının doğrulanması.
- XMOS XU316 USB ses arabirimi ve TI PCM3168A 8-kanal codec donanım prototipinin yazılımla haberleşmesinin sağlanması.
- İndirme servisinin (`download.coremusic.net`) otonom FLAC/MP3 edinim hattının optimize edilmesi.

### 10.3 Faz 3: Ekosistem Genişlemesi ve Profesyonel Yayılım (Professional)

- 8.1 surround ses sisteminin Class AB amplifikatör donanımıyla birlikte profesyonel stüdyo ve ev sinema ortamlarında konumlandırılması.
- Raspberry Pi 5 gömülü donanımının araç içi bilgi-eğlence (Car Infotainment) ünitesi olarak tescilli dokunmatik kabin içine entegre edilmesi.
- Çok odalı (multi-room) ev ortamları için WebRTC ve P2P tabanlı düşük gecikmeli kablosuz ses akış protokolünün hayata geçirilmesi.
- `coremusic_ai` modellerinin kullanıcı akustik tercihlerine göre dinamik olarak kendini eğiten adaptif bir yapıya kavuşturulması.

---

*Bu belge, CoreMusic mimari belgeleri, ADR kararları ve mühendislik anayasası (`.ai/CLAUDE.md`, `.ai/brain.md`, `.ai/architecture/`) temel alınarak hazırlanmış bağlayıcı vizyon sözleşmesidir.*
