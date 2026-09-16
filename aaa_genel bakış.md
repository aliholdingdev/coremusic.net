# CoreMusic — Profesyonel Proje Tanıtımı

## 1. Proje Genel Bakış ve Varoluş Amacı

**CoreMusic**, modern müzik ve ses yönetimi ihtiyaçlarını tek bir merkezi platform altında çözmek amacıyla geliştirilmiş **profesyonel seviyede bir dijital medya ekosistemidir**. Sistem; kişisel kullanıcıların müzik deneyimini geliştirmek, ev ve araç içi ses sistemlerini yönetmek, profesyonel ses ortamlarında tam kontrol sağlamak ve farklı cihazlar arasında **kesintisiz, kayıpsız ve bütünleşik bir medya deneyimi** oluşturmak için tasarlanmıştır.

Günümüzde müzik teknolojileri pazarı derin bir parçalanmışlık (fragmentation) yaşamaktadır. Kullanıcılar kişisel bilgisayarlarında farklı, cep telefonlarında farklı, araçlarında farklı ve ev sinema sistemlerinde bambaşka bağımsız uygulamalar ve donanım protokolleri kullanmak zorunda kalmaktadır. Bu durum; **müzik arşivlerinin dağınık ve kontrolsüz kalmasına**, **cihaz yönetiminin imkânsızlaşmasına**, **ses kalitesinin işletim sistemi mikserlerinde bozulmasına** ve kişisel dinleme deneyiminin kiralık abonelik servislerinin kısıtlı algoritmalarına sıkışmasına yol açmaktadır.

**CoreMusic bu yapısal problemlere kurumsal ve köklü bir çözüm olarak geliştirilmiştir.** Platform; müzik içeriklerini, ses cihazlarını, medya servislerini ve kullanıcı tercihlerini tek bir mimari omurga içerisinde birleştirerek **merkezi, akıllı, güvenli ve yönetilebilir bir müzik altyapısı** sunar.

---

## 2. Neden Sadece Bir Müzik Oynatıcısı Değil?

Bir müzik uygulamasını düşündüğümüzde genellikle akla sadece bir şarkıyı listeden seçip başlatan basit oynatıcılar (player) gelir. Ancak CoreMusic'in vizyonu ve mimari kapsamı bundan kıyaslanamayacak kadar geniştir. 

CoreMusic, sadece ses dosyalarını çalan bir yazılım değil; **evde, arabada, bilgisayarda veya profesyonel stüdyolarda** kullanılan tüm müzik ve ses sistemlerini tek bir merkezden yönetmeyi amaçlayan **büyük ölçekli bir dijital medya omurgasıdır**. Sistem şu temel yetenekleri eşzamanlı olarak bünyesinde barındırır:

- **Merkezi Kütüphane ve Arşivleme:** Dağınık ses dosyalarını ilişkisel bir veri varlığı olarak ele alır; 24-bit/32-bit **kayıpsız FLAC**, ALAC, WAV ve yüksek bit oranlı MP3 dosyalarını akustik parametreleriyle birlikte eksiksiz kataloglar.
- **Otonom Medya Edinimi ve Zenginleştirme:** Eksik albüm kapaklarını, yüksek çözünürlüklü görselleri, sanatçı biyografilerini ve zaman damgalı senkronize şarkı sözlerini (lyrics) otomatik olarak bulur, doğrular ve arşive entegre eder.
- **Akustik ve Ses Kalitesi İyileştirme:** Sıradan tüketici elektroniğinin maruz kaldığı işletim sistemi yeniden örnekleme (resampling) kayıplarını baypas eder; doğrudan donanım sürücüleriyle stüdyo referansında ses iletimi sağlar.
- **Cihazlar Arası Akıllı Yönlendirme:** Çalınan medyayı ağ üzerindeki herhangi bir hoparlöre, araç ünitesine veya stüdyo monitörüne tek dokunuşla kayıpsız olarak aktarır.
- **Kullanıcı Alışkanlıklarını Öğrenen Zekâ:** Kullanıcının günün farklı saatlerindeki enerji seviyesini, dinleme ortamını ve tercihlerini analiz ederek **kişiselleştirilmiş akustik ve müzikal deneyimler** inşa eder.

---

## 3. Cihaz Bağımsız Dijital Ses Ekosistemi

CoreMusic ekosistemi, kullanıcının bulunduğu ortam neresi olursa olsun müzik deneyiminin kesintisiz sürmesini garanti altına alır:

1. **Ev Medya Merkezi (`home.coremusic.net`):**
   - Raspberry Pi 5 veya yerel ağdaki bir NAS sunucusu üzerinde çalışan merkezi medya beyni.
   - WebRTC ve WebSocket altyapısıyla evin farklı odalarındaki hoparlörlere senkronize kablosuz ses dağıtımı (**Multi-Room Audio**).
   - Salon ekranlarına ve televizyonlara tam uyumlu, uzaktan kumanda ve mobil kontrol destekli geniş ekran arayüzü.

2. **Araç İçi Bilgi-Eğlence (`car.coremusic.net`):**
   - Araç içi dokunmatik panellere özel olarak tasarlanmış, sürüş güvenliğini ön planda tutan yüksek kontrastlı ve büyük dokunma hedeflerine (minimum 48px) sahip özel arayüz.
   - Doğrudan gömülü donanım (Raspberry Pi 5 + PCM3168A) üzerinden araç amfisine sıfır gecikmeli çok kanallı ses iletimi.
   - **Offline-First Mimari:** İnternet bağlantısının koptuğu tünellerde veya kırsal yollarda dahi yerel önbellek sayesinde müziğin milisaniye bile duraklamadan çalması.

3. **Masaüstü ve Yönetim İstasyonları (`music.coremusic.net` & `admin.coremusic.net`):**
   - Windows (Tier 1), Linux (Tier 2) ve macOS (Tier 3) işletim sistemlerinde çalışan yerel istemciler.
   - Müzik arşivini organize etmek, etiketleri düzenlemek, çalma listeleri oluşturmak ve ses çıkış parametrelerini yapılandırmak için geliştirilmiş zengin masaüstü paneli.

4. **Profesyonel Stüdyo ve Pro Ses (`studio.coremusic.net` & `pro.coremusic.net`):**
   - Ses mühendisleri ve müzik üreticileri için tasarlanmış profesyonel izleme (monitoring) ve yönlendirme arayüzleri.
   - **8.1 surround ses desteği (7.1 + LFE)**, kanal yönlendirme matrisi ve gerçek zamanlı spektrum analizörleri.

---

## 4. Ses Teknolojileri ve Donanım Bütünleşmesi: Neva Engine

CoreMusic'i geleneksel medya oynatıcılardan ayıran en kritik mühendislik farkı, kendi tescilli ses motoru olan **Neva Engine** ve dikey donanım entegrasyonudur:

- **C++20 Gerçek Zamanlı Ses Motoru:** Ses işleme döngüsünde (Audio Thread) bellek tahsisi yasaklanmış (**Zero-Allocation**) ve kilitlenmesiz dairesel tamponlar (**Lock-Free ring buffer**) kullanılmıştır. Bu sayede işlemci yükü ne olursa olsun seste en ufak bir takılma veya çıtırtı (glitch) oluşmaz.
- **Ultra Düşük Gecikmeli Sürücü Desteği:** Windows ortamında doğrudan Steinberg ASIO SDK 2.3.4 ile **<10ms**, WASAPI Exclusive modu ile **<15ms** gecikme sürelerinde stüdyo sınıfı veri iletimi.
- **31-Band Profesyonel Parametrik Ekolayzır (EQ):** İnsan işitme aralığının tamamını (20Hz – 20kHz) 1/3 oktav hassasiyetle kontrol eden, her bandın frekans, kazanç ve Q faktörünün ayarlanabildiği stüdyo seviyesi filtreleme katmanı.
- **Entegre DSP Efekt Zinciri:** Parametrik EQ → Gerçek Zamanlı Oda/Konser Reverb Simülatörü → Dinamik Kompresör → True Peak Tuğla Duvar Sınırlayıcı (Brickwall Limiter).
- **Özel Donanım ve Amplifikatör Entegrasyonu:** XMOS XU316 çok çekirdekli USB ses işlemcisi, 8 kanallı Texas Instruments PCM3168A (24-bit 192kHz DAC, 112dB SNR) ses dönüştürücüsü ve kanal başına 100W @ 8Ω Class AB analog amplifikasyon devresi (>0.5V DC offset röle korumalı) ile fiziksel cihaz düzeyinde üstün ses sadakati.

---

## 5. Yapay Zekâ Destekli Kişisel Medya Deneyimi

CoreMusic'in yapay zekâ katmanı (**`coremusic_ai`**), kullanıcının yerine karar veren zorba bir mekanizma olmak yerine, kullanıcıyı anlayan ve deneyimi kusursuzlaştıran **akıllı bir asistan** olarak çalışır:

- **Akustik Parmak İzi ve Müzikal Modelleme:** Parçaların tempo (BPM), müzikal ton, enerji seviyesi, dinamik aralık ve spektral dağılımını analiz ederek kütüphaneyi akustik özelliklerine göre sınıflandırır.
- **Kullanıcı Dinleme Alışkanlıkları Analizi:** Hangi saatlerde hangi tür müziklerin tercih edildiğini, hangi parçaların tekrarlandığını veya atlandığını izleyerek bağlama uygun akıllı akışlar üretir.
- **Otonom Kütüphane Zenginleştirme:** Kullanıcının kütüphanesindeki eksik etiketleri tamamlar, tür uyuşmazlıklarını düzeltir ve sanatçı diskografilerindeki boşlukları tespit ederek otonom indirme kuyruğu oluşturur.
- **Adaptif Akustik Ayar Önerileri:** Kullanıcının bağlandığı ses cihazına (kulaklık, araba hoparlörü, stüdyo monitörü veya oda ses sistemi) ve dinlenen müzik türüne göre en ideal EQ eğrilerini ve DSP profillerini kullanıcı onayına sunar.

---

## 6. Kurumsal Yazılım ve Veri Güvenliği Mimarisi

CoreMusic; geleceğe hazır, yüksek ölçekli ve kurumsal seviyede güvenli bir yazılım mimarisi üzerine inşa edilmiştir:

- **L0-L6 Katmanlı Mimari:** Altyapıdan donanıma kadar 7 katmanlı, bağımlılıkların yalnızca tek yönlü aktığı, katman ihlallerinin (Layer Violation) engellendiği katı bir Clean Architecture disiplini.
- **18 BCNF İzole Veritabanı (156 Tablo):** Veri anomalilerini sıfırlayan Boyce-Codd Normal Formu şeması; ORM yükünü ortadan kaldıran yüksek performanslı ham PDO sorguları ve UUID v7 zaman indeksli birincil anahtarlar.
- **Değişmez 10 Adımlı Güvenlik Hattı:** CSRF koruması, CSP nonce tabanlı `strict-dynamic` içerik güvenliği, NIST SP 800-38D uyumlu **AES-256-GCM Credential Vault** şifreli anahtar kasası ve Argon2id parola koruması.
- **Sıfır Çerçeve (No-Framework) Hızlı Frontend:** React/Vue sanal DOM gecikmeleri olmadan doğrudan **Vanilla JS ES6+** ve 9 katmanlı **ITCSS/BEM** ile ultra hızlı arayüz tepkisi; 19 orijinal PNG mockup referansıyla piksel sadakati.

---

## 7. Sonuç ve Proje Hedefi

CoreMusic; kullanıcının sahip olduğu tüm müzik parçalarını, ses donanımlarını ve ortamlarını tek bir akıllı çatı altında birleştiren, kiralık abonelik bağımlılığı yerine **kullanıcı mülkiyetini ve saf ses kalitesini** merkeze koyan devrim niteliğinde bir platformdur. 

Amacı; müzik dinleme eylemini pasif bir tüketimden çıkarıp, evden stüdyoya ve otomobile kadar uzanan **kesintisiz, akıllı, yüksek sadakatli ve prestijli bir ses yaşam biçimine** dönüştürmektir.
