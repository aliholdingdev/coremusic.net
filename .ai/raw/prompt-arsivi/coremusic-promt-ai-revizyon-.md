Red Team , human , truth mode ->  CoreMusic; araçta, evde ve profesyonel stüdyoda müzik dinlemek, müzik açmak ve müzik yönetmek 
için tasarlanan, otomatik indirme yeteneğine sahip bir medya platformudur. Sistem PHP ile kodlanacaktır.

Projenin `.ai/` klasöründe halihazırda bir bilgi tabanı bulunmaktadır. Bu klasörde projeyle ilgili tüm kararlar ve teknik
bilgiler yer alır, ancak içerik yeterince verimli yazılmamıştır; düzenlenmesi,yönetilmesi ve
yeniden planlanması gerekmektedir.

Bu görevin amacı, mevcut yapıyı koruyarak `.ai/` klasörünü yeniden yazmaktır.

### 1.1 Kapsam

- Mevcut `.ai/` içeriğinin analiz edilmesi
- Hatalı bölümlerin düzeltilmesi; hatasız bölümlerin korunması
- Tekrar eden içeriklerin birleştirilmesi
- Eksiklerin tamamlanması
- Markdown standardına uygun hale getirilmesi
- Yapının katı kurallı hale getirilmesi ve AI sistemlerinin `.ai/` klasörünü kullanmasının zorunlu kılınması

### 1.2 Kapsam Dışı

- **Kod yazımı.** Tasarım ve planlama tamamlanmadan kod yazılmaz.
- Klasör yapısının değiştirilmesi
- Dosya isimlerinin değiştirilmesi
- Kullanıcı açıkça istemedikçe yeni klasör oluşturulması

### 1.3 Çalışma Sırası

Proje bir ürün olacak şekilde yeniden tasarlanacaktır: önce tasarım, sonra planlama, en sonda kod.

```
Vision → Domain → Architecture → ADR → API Contract → Database → UI → Code
```

Önce `.ai/` planlaması, yazımı ve düzenlenmesi tamamlanacaktır. Kullanıcı `.ai/` için onay
verdikten sonra diğer planlama adımlarına geçilecektir.

### 1.4 Onay Kuralı

Eksik bir özellik veya not tespit edilirse bildirilecektir. Planlama adım adım yürütülecek ve
her plan için kullanıcı onayı alınacaktır.

---

## 2. Proje Teknoloji Yığını

Aşağıdakiler projede kullanılan teknolojilerdir. Her doküman yalnızca kendi konusuyla ilgili
olanlara atıf yapar; burada global bir uzmanlık tanımı yapılmaz.

**Backend / Web**
PHP 8.x, Node.js, TypeScript, JavaScript, HTML, CSS

**Ses / Sistem**
C++, Audio DSP, ASIO SDK, WASAPI, FFmpeg, JUCE, Windows Driver Kit (WDK), sanal ses sürücüleri

**Veri**
MySQL, MsSQL, Mongo DB, SQLite

**Platform**
Windows, Linux, macOS, Raspberry Pi, ReactOS

**Mimari yaklaşımlar**
Hexagonal Architecture, Clean Architecture, SOLID, DDD, Event Driven Architecture, CQRS, PSR standartları

---

## 3. Sistem Panelleri

### 3.1 Genel Notlar

- `music.coremusic.net` bir yönetim paneli değildir; medya oynatım panelidir.
- Asıl yönetim paneli `admin.coremusic.net` adresidir.
- Gömülü sistemlerde araç içi ve ev teybi için iki ayrı katman vardır.

### 3.2 Panel Listesi

| Adres | Rol |
|---|---|
| `coremusic.net` | Sistemi tanıtan landing page benzeri tanıtım sitesi. Header'da `admin.` ve `music.` panellerine giriş yönlendirmesi bulunur. |
| `music.coremusic.net` | Medya oynatım paneli. Web sunucusunda çalışır. |
| `car.coremusic.net` | Medya oynatım paneli. Araçta local çalışır. |
| `home.coremusic.net` | Medya oynatım paneli. Ev teybinde local çalışır. |
| `studio.coremusic.net` | Medya oynatım paneli. Stüdyoda local çalışır. |
| `pro.coremusic.net` | Medya oynatım paneli. Pro ortamda local çalışır. |
| `admin.coremusic.net` | Asıl yönetim paneli. |
| `download.coremusic.net` | Medya indirme servisi. Kontrol ve işlemler `admin.coremusic.net` üzerinden yapılır. |
| `media.coremusic.net` | Medya depolama alanı. |
| `auth.coremusic.net` | Kimlik doğrulama servisi.

Tüm oynatım panellerinde `home`, `pro` ve `studio` görünüm modları bulunur; bu modlar arasında
tek tıkla, buton ile geçiş yapılabilmelidir.

### 3.3 Kimlik Doğrulama

`auth.coremusic.net` şunları sağlar: login, register, şifremi unuttum, cinsiyet seçimi,
auth key onayı ve sonrasında `back-to-url` parametresine göre geri yönlendirme.

Hem müzik panelleri hem de `admin.` paneli bu servisi ortak kullanmalıdır. Ancak admin giriş
ekranlarının tasarımı farklıdır. 
---

## 4. Üçüncü Taraf Servisler

### 4.1 Last.fm

Metadata ev müzik bilgisi kaynağı. Şarkı bilgileri, albüm bilgileri, sanatçı bilgileri vb... olarak kullanacaktır.

### 4.2 YouTube

YouTube veya YouTube Music API kullanılmayacaktır. Bunun yerine `C:\www\nova-search-engine`
konumundaki yazılım entegre edilerek kullanılacaktır.

### 4.3 Deezer / İndirme Motoru

`https://github.com/bambanah/deemix` adresindeki yazılım PHP'ye dönüştürülerek
`download.coremusic.net` klasörünün içine kurulacak, ardından CoreMusic'e entegre edilip
indirme motoru olarak kullanılacaktır. birde deemiz de user bilgileri var deezer heabı dan bir değer istiyor bu surekli guncle tutulamsı için web kasıma moturu iel bifglielri al ve bir panel yap php de 'admin.coremusic.net' de bir kısım olsun aoradan deezer hesabımız ile oturum aclaım deemix reposunu icnele oarada flac indiremk için dezer hifi abonelği istiyor oada bende var ...

### 4.4 AI Destekli Otomatik İndirme
C:\www\nova-search-engine` benzeri bir sistem daha kurulacaktır. Bu sistem:

1. YouTube'da belirlenen hesaptaki veya URL ile verilen oynatma listelerini 30 dakikada bir
   sürekli indeksler.
2. AI ile YouTube'dan şarkı ismini ve metadata bilgisini alır.
3. Kurduğumuz özel deemix yazılımında bu şarkıyı aratır.
4. İstenilen dizine otomatik olarak indirir.
5. AI ile analiz ve yorum yapar.
6. İndirmenin `download.coremusic.net` üzerinden gerçekleşmesini sağlar ve sonucu kaydeder.

### 4.5 AI Destekli Analiz & İndexleme & Core AI Sistemi

Sistem otomatik olarak kullanıcıyı analzi edecek indexleycek alışkanlıklarını özel telemetry servisleri iel toplayacak ve sistem ai tam ai (local or web ) ai entegrasonu ile kullanıcıya özel müzik önerecek gibi etc... yapay zeka iel her şey yapabielcek akıllı bir sistem olacak otomatik idnrime müzik öerisi gibi etc...

### 4.6 İndirme Kalitesi

Şarkı Deezer'dan FLAC kalitesinde indirilecektir. En yüksek 48 kHz veya 96 kHz, 32 bit olacaktır.
İki ayrı şekilde indirilecektir: hem 24 bit hem 32 bit — 24 bit sürüm, muadil cihazlarda
çalınabilmesi içindir. 16 bit indirme yapılmayacaktır.

---

## 5. Kısıt: Kodlama Yasağı

Şu aşamada kodlama yoktur. Yapılacak iş, `.ai/` klasörünün yeniden yazılmasıdır.
Proje bütünüyle netleşmeden kod yazılmayacaktır.

---

## 6. Proje Tanımı

CoreMusic, geleneksel bir müzik oynatıcı uygulaması geliştirme amacıyla başlatılmış bir proje
değildir. Bu proje; bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi
bilgi-eğlence sistemleri (Car Infotainment), ev medya merkezleri, NAS depolama çözümleri ve
çoklu cihaz ekosistemleri için tasarlanmış, kurumsal seviyede, modüler ve genişletilebilir bir
medya yönetim platformudur.

Projenin temel amacı yalnızca müzik dosyalarını oynatmak değildir. Amaç; müzik yaşam döngüsünün
tamamını tek bir platform altında yönetebilen kapsamlı bir medya ekosistemi oluşturmaktır. Bu
kapsamda sistem; müziklerin indirilmesi, doğrulanması, arşivlenmesi, organize edilmesi, metadata
bilgilerinin yönetilmesi, profesyonel ses işleme süreçlerinin yürütülmesi, çoklu cihazlar arasında
senkronizasyonun sağlanması ve yapay zekâ destekli içerik analizlerinin gerçekleştirilmesi gibi
birçok farklı yeteneği tek bir mimari içerisinde bir araya getirmektedir.

CoreMusic, tek bir uygulama yerine birlikte çalışan servislerden oluşan bir platform olarak
tasarlanmıştır. Her servis kendi sorumluluk alanına sahiptir ve tüm servisler ortak mimari ilkeleri
kullanarak birbirleriyle güvenli ve kontrollü şekilde haberleşmektedir. Böylece sistem büyüdükçe
yeni modüller eklenebilmekte, mevcut servisler bağımsız olarak geliştirilebilmekte ve platform uzun
yıllar sürdürülebilir bir yapıda kalabilmektedir.

Platformun web tabanlı yönetim katmanı PHP 8.x teknolojileri üzerine inşa edilmektedir. Yönetim
paneli, kullanıcı yönetimi, müzik kütüphanesi yönetimi, sistem yapılandırmaları ve operasyonel
süreçler merkezi bir panel üzerinden gerçekleştirilecektir. Medya dosyaları ayrı bir medya servisi
üzerinden yönetilecek, indirme işlemleri bağımsız bir Download Manager tarafından gerçekleştirilecek
ve gelecekte farklı servislerin sisteme eklenebilmesi için servis tabanlı mimari korunacaktır.

CoreMusic'in en önemli hedeflerinden biri, farklı kullanım senaryolarını tek platform altında
destekleyebilmesidir. Aynı kullanıcı, sistemi evindeki medya merkezi olarak kullanabilirken, aynı
hesap üzerinden aracındaki bilgi-eğlence sistemine bağlanabilecek, profesyonel stüdyosunda yüksek
çözünürlüklü ses işleme süreçlerini yönetebilecek veya NAS üzerinde bulunan büyük müzik arşivlerini
organize edebilecektir. Böylece kullanıcı deneyimi cihaz bağımsız hâle gelmekte ve tüm medya
ekosistemi tek merkezden yönetilebilmektedir.

Platform yalnızca çevrim içi çalışacak şekilde tasarlanmamıştır. Aksine, "Offline First" yaklaşımı
temel mimari prensiplerinden biridir. Kullanıcılar internet bağlantısı olmadan da müzik arşivlerine
erişebilecek, oynatma listelerini yönetebilecek ve yerel medya içeriklerini kullanmaya devam
edebilecektir. İnternet bağlantısı yeniden sağlandığında sistem gerekli senkronizasyon işlemlerini
otomatik olarak gerçekleştirecektir.

CoreMusic içerisinde gelişmiş bir medya yönetim altyapısı bulunmaktadır. Bu altyapı; sanatçı
bilgileri, albüm yapıları, tür sınıflandırmaları, yüksek doğrulukta metadata yönetimi, kapak
görselleri, ses analizleri, teknik medya bilgileri ve kullanıcı tarafından oluşturulan içerikleri
merkezi olarak yönetmektedir. Böylece büyük müzik arşivleri dahi yüksek performansla organize
edilebilmektedir.

Proje yalnızca müzik dosyalarının depolanmasını değil, aynı zamanda profesyonel ses yönetimini de
hedeflemektedir. İleri aşamalarda ASIO, WASAPI, FFmpeg, Audio DSP, profesyonel ses kartları, sanal
ses sürücüleri ve düşük gecikmeli ses işleme teknolojileriyle entegrasyon planlanmaktadır. Bu sayede
platform yalnızca son kullanıcılar için değil, profesyonel kayıt stüdyoları ve yayın sistemleri için
de uygun bir altyapı sunacaktır. Ancak bu bileşenlerin uygulanma durumu ilgili kaynaklardan
doğrulanmalıdır; mevcut dokümanlar bazı bölümleri plan aşamasında göstermektedir.

CoreMusic'in bir diğer temel amacı, yapay zekâ destekli medya yönetimidir. Sistem, büyük medya
koleksiyonlarını analiz ederek akıllı arama, gelişmiş filtreleme, otomatik etiketleme, öneri
motorları, akıllı oynatma listeleri ve kullanıcı alışkanlıklarına göre kişiselleştirilmiş içerik
yönetimi gibi yetenekleri destekleyecek şekilde tasarlanmaktadır. Böylece kullanıcı yalnızca içerik
tüketen değil, medya koleksiyonunu akıllı biçimde yöneten bir deneyim yaşayacaktır.

Sistemin teknik mimarisi sürdürülebilirlik, genişletilebilirlik ve bakım kolaylığı ilkeleri üzerine
kurulmaktadır. SOLID prensipleri, Clean Architecture, Hexagonal Architecture, Domain Driven Design,
Event Driven Architecture, CQRS ve PSR standartları temel mühendislik yaklaşımını oluşturmaktadır.
Bu sayede platformun her bileşeni bağımsız geliştirilebilir, test edilebilir ve gerektiğinde diğer
sistemlerden ayrıştırılabilir.

CoreMusic projesinin en önemli bileşenlerinden biri de yapay zekâ tarafından okunabilir bilgi
tabanıdır. Proje içerisinde bulunan `.ai/` klasörü, yalnızca dokümantasyon amacıyla oluşturulmuş bir
klasör değildir; sistemin kurumsal bilgi merkezi olarak tasarlanmıştır. Mimari kararlar, iş akışları,
teknik notlar, karar kayıtları (Architecture Decision Records), agent tanımları, süreç yönetimleri ve
proje hafızası bu klasör içerisinde tutulmaktadır. Amaç; yalnızca insanlar için değil, ChatGPT,
Claude, Gemini, Codex, Cursor, RooCode, Cline ve gelecekte kullanılacak diğer yapay zekâ sistemleri
için de tek doğruluk kaynağı (Single Source of Truth) oluşturmaktır. Bu yaklaşım mevcut Agent
Registry ve CLAUDE.md belgeleriyle de uyumludur.

Proje boyunca bilgi tabanı sürekli geliştirilecek ancak mevcut klasör yapısı, dosya isimleri ve genel
organizasyon kullanıcı onayı olmadan değiştirilmeyecektir. Dokümantasyon çalışmaları mevcut
içerikleri silmek yerine iyileştirme, standartlaştırma ve profesyonelleştirme esasına göre
yürütülecektir. Böylece yıllar içerisinde oluşan bilgi kaybı önlenecek, kurumsal hafıza korunacak ve
yeni geliştiricilerin projeye uyum süresi önemli ölçüde azaltılacaktır.

Sonuç olarak CoreMusic, yalnızca müzik dinlemek amacıyla geliştirilen bir uygulama değildir. Bu
proje; medya yönetimi, profesyonel ses teknolojileri, çoklu cihaz entegrasyonu, çevrim dışı çalışma,
yapay zekâ destekli içerik yönetimi ve kurumsal bilgi tabanı yaklaşımını tek platform altında
birleştiren, uzun yıllar geliştirilebilir ve sürdürülebilir olması hedeflenen kapsamlı bir dijital
medya ekosistemidir. Bu vizyon doğrultusunda her teknik karar, her mimari bileşen ve her
dokümantasyon çalışması aynı hedefe hizmet eder: güvenilir, modüler, genişletilebilir ve yapay zekâ
sistemleri tarafından da eksiksiz anlaşılabilir bir platform oluşturmak.

---

## 7. Startup Perspektifi

CoreMusic sistemi klasik bir müzik uygulaması değil, startup seviyesinde tasarlanmış, uzun vadeli
büyümeye uygun, modüler bir teknoloji ekosistemi olarak planlanmıştır. Burada amaç sadece
kullanıcıların müzik dinlemesini sağlamak değil; ses teknolojisi, yapay zekâ, kişiselleştirme,
yüksek kaliteli audio işleme ve profesyonel kullanıcı deneyimini tek bir platform altında birleştiren
yeni nesil bir müzik işletim sistemi oluşturmaktır. Sistem mimarisi bu nedenle tek bir uygulama
mantığıyla değil, gelecekte yatırım alabilecek ve ekip büyüdüğünde yönetilebilecek bir teknoloji
şirketi altyapısı gibi düşünülmüştür.

Bu projeyi bir startup olarak ele aldığımızda ilk aşamada CoreMusic'in temel ürünü olan müzik
platformu geliştirilir; kullanıcı hesapları, müzik kütüphanesi, playlist sistemi, sanatçı ve albüm
yönetimi, kişiselleştirilmiş ana sayfa ve kullanıcı deneyimi tamamlanır. Daha sonra sistem büyüdükçe
bağımsız servisler eklenerek büyük teknoloji şirketlerindeki gibi ölçeklenebilir bir yapıya geçilir.
Backend, frontend, ses motoru, indirme servisleri, medya servisleri ve donanım entegrasyonları
birbirinden ayrılarak ekiplerin paralel çalışabileceği profesyonel bir organizasyon modeli
oluşturulur.

CoreMusic'in startup stratejisi üç temel aşama üzerine kurulabilir. Birinci aşamada MVP (Minimum
Viable Product) geliştirilerek kullanıcıya çalışan bir müzik deneyimi sunulur. İkinci aşamada premium
özellikler eklenerek yüksek kaliteli ses, AI öneri sistemi, profesyonel EQ, kişisel müzik profilleri
ve gelişmiş kullanıcı deneyimleri oluşturulur. Üçüncü aşamada ise CoreMusic sadece bir uygulama
olmaktan çıkarılıp kendi ses motoruna, kendi donanım çözümlerine ve geliştirici ekosistemine sahip
bağımsız bir teknoloji platformuna dönüşür.

Teknik açıdan bu sistem bir startup'ın teknoloji vizyonu gibi planlanmıştır; çünkü PHP backend,
Vanilla JS SPA mimarisi, C++ ses motoru, Node.js servisleri ve donanım bağlantıları aynı ekosistem
içinde çalışacak şekilde organize edilmiştir. Bu yapı küçük bir ekip ile başlayıp ileride backend
mühendisleri, UI/UX tasarımcıları, güvenlik uzmanları, veri mühendisleri, embedded geliştiriciler ve
DevOps ekipleri tarafından büyütülebilecek şekilde hazırlanmıştır.

AI tarafında ise proje sadece kod yazan bir sistem değil, kendi bilgi tabanı, agent sistemi ve çalışma
kuralları olan bir geliştirme organizasyonu şeklinde tasarlanmıştır. Yani yapay zekâ ajanları backend,
güvenlik, veri, UI, embedded, QA ve DevOps gibi uzman rollere ayrılarak büyük yazılım şirketlerindeki
ekip yapısına benzer şekilde görev dağılımı yapabilir.

Yatırımcı gözüyle bakıldığında CoreMusic'in değeri sadece bir müzik uygulaması olmasından değil,
sahip olduğu teknoloji altyapısından gelir; çünkü proje gelecekte Spotify benzeri streaming
özellikleri, Apple Music seviyesinde kullanıcı deneyimi, profesyonel stüdyo kalitesinde ses işleme,
AI destekli müzik keşfi ve IoT/donanım entegrasyonu gibi alanlara genişleyebilecek şekilde
kurgulanmıştır. Ancak startup mantığında en kritik nokta, bütün sistemi aynı anda yapmak yerine önce
pazara ulaşabilecek çekirdek ürünü tamamlamak, kullanıcı geri bildirimleriyle geliştirmek ve daha
sonra karmaşık teknolojileri aşamalı olarak eklemektir.

Özet olarak CoreMusic, doğru yönetildiğinde bir "müzik çalar projesi" değil; audio teknoloji şirketi,
AI destekli müzik platformu ve gelecekte kendi donanım ekosistemine sahip olabilecek bir teknoloji
girişimi olarak konumlandırılabilecek bir projedir. Başarının anahtarı ise büyük vizyonu korurken
geliştirme sürecini startup disiplininde yönetmek, önce çalışan ürün çıkarmak, sonra ölçeklemek ve
her aşamada gerçek kullanıcı değerine odaklanmaktır.

---

## 8. Kullanım Senaryoları

CoreMusic; bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi eğlence
sistemleri, ev medya merkezleri ve büyük dijital müzik arşivleri için geliştirilmiş, merkezi medya
yönetimi ve profesyonel ses deneyimi sağlayan yeni nesil bir medya ekosistemidir.

Projenin amacı; kullanıcının sahip olduğu tüm müzik içeriklerini tek merkezden yönetebilmesi,
organize edebilmesi, analiz edebilmesi, farklı cihazlarda kullanabilmesi ve yüksek kaliteli ses
deneyimi yaşayabilmesidir.

### 8.1 Ev Kullanımı

Kullanıcı ev ortamında CoreMusic'i merkezi medya sunucusu olarak kullanabilir. Sistem NAS, lokal disk
veya harici depolama alanlarındaki müzik arşivlerini analiz eder, organize eder ve farklı cihazlardan
erişilebilir hale getirir.

### 8.2 Araç Kullanımı

CoreMusic araç içi bilgi-eğlence sistemleri için optimize edilerek offline müzik erişimi, hızlı arama,
kişisel oynatma listeleri ve düşük gecikmeli ses deneyimi sağlar.

### 8.3 Profesyonel Stüdyo Kullanımı

CoreMusic profesyonel kullanıcılar için yüksek çözünürlüklü ses dosyaları, ASIO/WASAPI tabanlı düşük
gecikmeli ses çıkışı, DSP işlemleri ve profesyonel ses ekipmanı entegrasyonu hedefler.

Ev, araç ve profesyonel stüdyo ortamlarının hepsinde ASIO ile 32 bit yüksek kaliteli ses işleme ve
8 giriş / 8 çıkış desteği olmalıdır.

---  

## 9. Kayıt ve Kanal Yönetimi

Kullanıcılar çalan şarkının ses çıkışını kaydetmek isteyebilir. Örneğin radyo dinlerken kayıt almak
ve sonradan USB'den veya kayıttan dinlemek isteyebilirler. Bu kayıt MP3 veya FLAC formatında
alınabilecektir. 8 giriş üzerinden kanal kanal kayıt alma işlemi yapılacaktır.

"Kanal kanal" ifadesi şu anlama gelir:

- Radyo oynatılırken aynı anda MP3 çalınmak istenirse kanal 1 ve kanal 2'den kayıt alınmalıdır.
- Başka bir cihazda aynı hesaptan oynatma yapılıyorsa ve o da kaydedilmek istenirse kanal 3 ve
  kanal 4 şeklinde devam edilmelidir.

Hesaplar arası ses aktarımı, Spotify'daki cihazlar arası oynatma özelliği gibi olmalıdır. Bu özellik
kapsamında her cihazın oynattığı ses, internet üzerinden ağ tarafından gönderilip alınabilmelidir.

---

## 10. Audio Engine

CoreMusic Audio Engine içerisinde DSP işlemleri 32-bit floating point precision ile
gerçekleştirilecektir. Donanım çıkış kalitesi kullanılan DAC, ADC ve audio interface özelliklerine
bağlı olacaktır.

CoreMusic yalnızca bir medya oynatıcı değildir. Sistem aynı zamanda profesyonel seviyede ses yönetimi,
düşük gecikmeli audio processing, DSP işlemleri ve çoklu cihaz ses ekosistemi oluşturmayı hedefleyen
bir platformdur.

CoreMusic Audio Engine; ev kullanıcıları, araç sistemleri, profesyonel stüdyolar, Raspberry Pi tabanlı
medya merkezleri ve masaüstü işletim sistemleri için yüksek kaliteli ses işleme altyapısı
sağlayacaktır.

Amaç yalnızca müzik dosyasını oynatmak değildir. Amaç; oynatma, kayıt, DSP, ses yönlendirme, kanal
yönetimi, cihazlar arası ses aktarımı ve profesyonel ses kontrolünü tek bir mimari altında
toplamaktır.

Audio Engine çekirdeği C/C++ ile geliştirilecek ve işletim sistemlerinden bağımsız bir yapı
sağlayacaktır.


### 10.1 Profesyonel Ses Kalitesi Hedefleri

CoreMusic Audio Engine aşağıdaki özellikleri destekleyecek şekilde tasarlanacaktır:

- 32-bit audio processing
- High Resolution Audio desteği
- 24-bit / 32-bit PCM işleme
- 96 kHz / 192 kHz örnekleme desteği
- Düşük latency audio pipeline
- Gerçek zamanlı DSP işlemleri
- Çok kanallı ses yönetimi
- Profesyonel ses kartı desteği

### 10.2 Hedef Kanal Yapılandırmaları

| Kullanım | Yapılandırma |
|---|---|
| Stereo | 2.0 |
| Surround | 5.1 |
| Professional | 8.1 |
| Studio | 8.1 & 8 Input / 8 Output |

### 10.3 Platform Ses Katmanları

```
Windows
 ├── ASIO
 └── WASAPI Exclusive

Linux / Raspberry Pi
 ├── ALSA
 ├── PipeWire
 └── JACK

macOS
 └── CoreAudio
```

### 10.4 DSP Efektleri

DSP'de hem output hem input tarafında reverb gibi sistemler bulunmalıdır. Oda ayarları arasında
"geniş konser" ve "düğün salonu" efektleri yer almalıdır — Samsung J7 (2016) telefonundaki geniş
konser / düğün salonu efektine benzer şekilde.

### 10.5 Performans Hedefleri

- Latency: donanıma bağlı olarak < 5 ms
- THD+N: DAC/ADC zincirine göre belirlenir

---

## 11. Bilgi Tabanı Hedefi

Her markdown dosyası hem AI hem insan tarafından okunabilir olmalıdır. Başka bir AI, yalnızca ilgili
markdown dosyasını okuyarak sistemi anlayabilmelidir. Bu nedenle her markdown dosyası tek başına
yeterli bilgi içermelidir.

---

## 12. Her Dokümanda Bulunması Gereken Bölümler

Her markdown dosyasında mümkün olduğunca aşağıdaki başlıklar bulunmalıdır:

- Amaç
- Kapsam
- Terminoloji
- Sistem Tanımı
- Mimari
- Workflow
- Architecture Decision Records (ADR)
- Kurallar
- Örnekler
- Best Practices
- Edge Cases
- Warnings
- Limitations
- Dependencies
- Future Roadmap
- Related Documents
- Cross References
- Glossary

---

## 13. Doküman Kalitesi

Dokümanlar teknik kitap kalitesinde olmalıdır. Wikipedia maddesi gibi veya kod yorumu gibi
olmamalıdır. Gerçek enterprise dokümantasyon kalitesi hedeflenmektedir.

Dokümanlar şu niteliklere sahip olmalıdır:

- Enterprise seviyesinde
- Teknik kitap kalitesinde
- Kurumsal standartlarda
- Tekrarsız
- Tutarlı
- AI tarafından kolay okunabilir
- Markdown standardına uygun
- Başlık hiyerarşisi doğru
- Tablo kullanımı doğru
- ASCII diyagramları düzenli
- Workflow diyagramları açıklayıcı
- Decision tabloları eksiksiz

---

## 14. Halüsinasyon Politikası

Aşağıdakiler kesinlikle uydurulmayacaktır:

- API
- Endpoint
- Sınıf
- Servis
- Workflow
- Mimari
- Roadmap
- Özellik
- Teknik karar

Doğrulanamayan her bilgi şu formatta işaretlenmelidir:

```
VERIFICATION REQUIRED
```

Ve aşağıdaki bilgiler eklenmelidir:

- Eksik Kanıt
- Muhtemel Kaynak
- Kullanıcı Onayı Gerekiyor

---

## 15. Tek Doğruluk Kaynağı (Single Source of Truth)

Her bilgi öncelikle aşağıdaki kaynaklardan doğrulanacaktır:

1. `.CLAUDE.md`
2. `.AGENTS.md`
3. `.WORKFLOW.md`
4. `.ai/index.md`
5. `.ai/keys.md`
6. `.ai/CLAUDE.md`
7. `.ai/AGENTS.md`
8. `.ai/WORKFLOW.md`
9. `.ai/brain.md`
10. `.ai/memory.md`
11. `.ai/log.md`
12. İlgili markdown dosyaları
13. Kaynak kodu
14. Resmî dokümantasyon

Bu kaynaklarda doğrulanamayan hiçbir bilgi kesin gerçek olarak yazılmayacaktır.

Çelişki bulunduğunda not alınacak ve çözüm önerilecek, ancak kullanıcı onayı olmadan karar
verilmeyecektir.

**Zorunlu kural:** Teknik bir çelişki bulunmadığı sürece bu kurala uyulacaktır. Çelişki oluşursa
değişiklik yapmadan önce raporlanacaktır.

---

## 16. Öncelikli Amaç

Amaç 1000 satır üretmek değildir. Amaç aşağıdaki niteliklere sahip bir Knowledge Base oluşturmaktır:

- Kaliteli
- Sürdürülebilir
- Yeniden kullanılabilir
- AI tarafından kolay okunabilir
- Kurumsal standartlarda
- Teknik doğruluğu yüksek
- Uzun yıllar kullanılabilecek

Kalite, satır sayısından her zaman daha önemlidir.

---

## 17. Sistem Yetenekleri

Amaç yalnızca müzik çalmak değildir. Sistem şunları yapabilmelidir:

- Müziği organize edebilmelidir
- Müziği indirebilmelidir
- Müziği arşivleyebilmelidir
- Müzik kütüphanesini yönetebilmelidir
- Metadata yönetebilmelidir
- AI destekli arama yapabilmelidir
- Playlist oluşturabilmelidir
- Çoklu cihazlarda çalışabilmelidir
- Araç, ev ve profesyonel stüdyo kullanım senaryolarını desteklemelidir

Kapsanan sistemler:

- Otomatik müzik indirme
- Müzik yönetimi
- Medya arşivleme
- Profesyonel ses yönetimi
- Ev medya merkezi
- Araç içi bilgi-eğlence (Car Infotainment)
- Stüdyo ses sistemi
- NAS medya yönetimi
- AI destekli müzik öneri sistemi
- Çoklu cihaz senkronizasyonu
- Offline First medya platformu
- Streaming altyapısı
- ASIO 32 bit ses desteği
- AI ile otomatik EQ / DSP yönetimi ve senkronizasyonu

Bu nedenle proje, klasik bir Media Player olarak değerlendirilmemelidir.

---

## 18. ADR Formatı

Her Architecture Decision şu formatta yazılmalıdır:

- ADR ID
- Problem
- Alternatives
- Decision
- Reason
- Trade-off
- Impact
- Status
- Web arama sonucu, bulgular, kararlar

---

## 19. Her Doküman İçin Çıktı Formatı

Her dosya aşağıdaki sırayla değerlendirilecektir:

1. Current Problems
2. Repeated Sections
3. Missing Information
4. Improvement Plan
5. Updated Document

---

## 20. Audio Organizasyon Yapısı

CoreMusic'i sadece bir ses kartı veya audio interface cihazı olarak düşünmek doğru değildir.
Piyasadaki birçok ürün genellikle tek bir probleme odaklanır; örneğin profesyonel audio interface
cihazları çoklu giriş/çıkış, düşük gecikme ve stüdyo kullanımı için tasarlanır.

CoreMusic ise bir audio teknoloji organizasyonu gibi tasarlanır. Bu organizasyonun amacı; donanım,
yazılım, profesyonel stüdyo teknolojileri, tüketici ürünleri ve yapay zekâ destekli ses sistemlerini
tek bir ekosistem altında toplamaktır.

| Division | Sorumluluk |
|---|---|
| Hardware Division | Özel audio kartları, DAC/ADC çözümleri, DSP çipleri, amplifikatör ve cihaz donanımları |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler, kontrol paneli, AI Audio sistemleri |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing ve düşük gecikmeli profesyonel çalışma altyapısı |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses sistemleri |
| Research Division | AI DSP, yeni codec teknolojileri, geleceğin audio donanımları |

WASAPI, Windows üzerinde uygulamalar ile ses donanımı arasındaki veri akışını yönetmek için
kullanılan temel ses mimarilerinden biridir.

Hedef, tek bir cihaz satmak değil; Apple'ın ekosistem yaklaşımı, RME ve Universal Audio'nun
profesyonel audio altyapısı, Sonos'un tüketici ses deneyimi ve Dolby'nin ses teknolojisi yaklaşımı
gibi büyük bir audio platformu oluşturmaktır.

Ancak böyle bir sistem tek seferde yapılmaz. Doğru mühendislik sırası:

1. C++ Audio Engine
2. WASAPI / ALSA ses çıkışı
3. DSP Engine
4. Mixer sistemi
5. ASIO entegrasyonu
6. 8x8 profesyonel audio interface
7. Özel CoreMusic Audio Board

CoreMusic'in vizyonu: normal kullanıcı müzik platformu, profesyonel stüdyo sistemi ve özel donanım
ekosisteminin birleştiği bağımsız bir audio teknoloji organizasyonu oluşturmaktır.

---

Şimdi hem arabada hem evde hem de profesyonel bir şekilde hem de stüdyo 
bir şekilde müzik dinlemek, müzik açmak, yönetmek için otomatik indirmesi olan bir media player sistemi 
yapacağız. Bu PHP ile kodlanacak. Admin paneli ve music.coremüzik.net PHP ile olacak. media.coremusic.net
Medya disk olacak download.coremusic.net İndirme klasörü olacak Böyle bir müzik dinleme, müzik yönetme 
paneli kuracağız. Ben sana normal bir .ai klosörümüz var. Burada proje 
ile ilgili her şey yazıyor ama düzenlenmesi ve yönetilmesi & yeniden planlanması gerekiyor. Tam verimli yazılmadı. Her türlü
kararlar, her şey .ai klasöründe mevcut. Bunu önce baştan yazacağız. Tabii ki de mevcut yapıyı
koruyarak. max 1000 satır olması gerekiyor. Ve şimdi Çeçipiti ve Cemini ile bu promptları oluşturalım
devam edelim. Bana yardımcı olur musun?


**rol:** Senior level Artitecture Katmanlı Mimarı Umzanı senir level yazılımcı kodlama uzmanı c++ , php , node js , css , js , html , typescrpit , asio sdk i, asio , ses sistemi, windows driver kit wdk adk, vbirtual audio , amfi ses akrtı amfi uzmanı 8+1 , etc...
**deneyim:** Yaklaşık 50 yıllık Aşkın Deneyim

**Sistem sadece normal bir music player değildir.**

**Amaç:** Hem müziği yönetebilmek, hem müziği dinlemek, hem müziği yönetebilmek için normal bir medya player değildir bu. Çok amaçlı, özel bir medya player'dır. Zaten nokta.ai'de her şey yazıyor, amacı orada belirtilmiştir. Ona göre yapmanı istiyorum.

**Not:** Aynı .ai yapısı ile devam wedilmeldidr yapı değişirilmememldiir.

**Not:** music.coremusic.net yönetim paneli değildir, medya oynatım panelidir

**Not:** asıl yönetim paneli admin.coremusic.net tir 

**Not:** gömülü sistemlerde hem arac içi hemde  ev teyibi için 2 tane katman vardır.

**Not:** sistem web panel katman listesi : 

**Sunucu Alt Yapısı**:
- Windows ISS
- Wampserver
- Apache2
- Linux
- Macos 

hepsinde ortak çalışmalıdlır asıl server : **Widndows Server 2012 R2 or Windows 8** dir **Linux** 2. plandadır.


**3rd Party API**
- lastfm 
- youtube or youtube music api ama kullanılmayacak onun yerine **C:\www\nova-search-engine** deki yazılım entegre edilecek kullanılacak
- dezer api ama deemix github : **https://github.com/bambanah/deemix** deki yazılım php ye dönüştürülerek **download.coremusic.net** adlı klasorun içine kuurulacka sonra coremusic e enegre edielcek downalod motoru olarak kullanılacak. **C:\www\nova-search-engine** ve **AI Destekli* arama ve şarkı dinelem alışkanığı sevilen sanatcılar,şarkılar etc... indexleme makine öğrenimi ile ve yarcını olamk için **C:\www\nova-search-engine** gibi ve bir tane daha sistem kurulacak youtube edeki belilen hesaptaki yada url ile oynatma listeleri surekli 30 dk bir indexlenip ai ile yotubden şarkı ismini metadatasını al özel deemix kurdğumuz yeni yazılımda arat ve istenilen dizni otomaik olarak idmir ai ile analiz yorum yap ve **downlaod.coremusic.net** den indirilmesini sağlat. ve kaydet ai ile indirme sistemi kur amac manuıl şarkı indirme oranını azaltmak ama sistedme isnbeilen aşrkıyı manul olrak da indiebielisnis ağla online modda iken youtube mjopdunda kulaırken sağla şakrıyı deezerden flac kalitesinde indir en yuksek max 48 khz or  96khz de indir 32 bit olsun ama 2 şekidle idmnir hem 24 bit hemde 32 bit olsun ki 24 bit lki maliferde çalabislin şarkıyı ama asla 16 bit indirme!!!!


**DIKAT!!!:** ŞUANLIK KODLAMA YOKTUR .ai YI Yeniden yazma yapaıldrmak vardır tüm rpoeje belli olamdan kodlama yoktur!!! 

**Not:** İlk Yapılması Gerekenler : Projeye bir ürün olacak şekilde yeniden tasarlamak, önce tasarlamak, planlamak, sonra kod yazmak. Tasarlama planlama bitmeden önce kod yazmak yoktur.!!!!!

** eksik olan bir özellik veya bir not varsa bunu tespit et sen planlamaya başla benden her planam için onay al **





# **ROLE**

Sen 50+ yıllık Senior Software Architect, AI Knowledge Engineer, Technical Writer, Enterprise Solution Architect ve Documentation Engineer'sin.

Uzmanlık alanların:

- PHP 8.x Enterprise
- Node.js
- TypeScript
- C++
- Audio DSP
- ASIO
- WASAPI
- FFmpeg
- JUCE
- SQLite
- MySQL
- Linux
- Windows
- WDK
- Driver Development
- Hexagonal Architecture
- Clean Architecture
- SOLID
- DDD
- Event Driven Architecture
- CQRS
- AI Knowledge Base Engineering


# **Nihai Hedef**

**`.ai` klasörü;**

* Claude Code
* ChatGPT
* Gemini
* Codex
* Cursor
* Cline
* RooCode
* OpenCode
* Aider
* Gelecekteki diğer AI sistemleri

için **Single Source of Truth** olacaktır.

**Her güncelleme mevcut yapı korunarak yapılacaktır.**

**Hiçbir dosya veya klasör, kullanıcı onayı olmadan yeniden adlandırılmayacak, taşınmayacak veya silinmeyecektir.**


CoreMusic, geleneksel bir müzik oynatıcı uygulaması geliştirme amacıyla başlatılmış bir proje değildir. Bu proje; bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi bilgi-eğlence sistemleri (Car Infotainment), ev medya merkezleri, NAS depolama çözümleri ve çoklu cihaz ekosistemleri için tasarlanmış, kurumsal seviyede, modüler ve genişletilebilir bir medya yönetim platformudur.
Projenin temel amacı yalnızca müzik dosyalarını oynatmak değildir. Amaç; müzik yaşam döngüsünün tamamını tek bir platform altında yönetebilen kapsamlı bir medya ekosistemi oluşturmaktır. Bu kapsamda sistem; müziklerin indirilmesi, doğrulanması, arşivlenmesi, organize edilmesi, metadata bilgilerinin yönetilmesi, profesyonel ses işleme süreçlerinin yürütülmesi, çoklu cihazlar arasında senkronizasyonun sağlanması ve yapay zekâ destekli içerik analizlerinin gerçekleştirilmesi gibi birçok farklı yeteneği tek bir mimari içerisinde bir araya getirmektedir.
CoreMusic, tek bir uygulama yerine birlikte çalışan servislerden oluşan bir platform olarak tasarlanmıştır. Her servis kendi sorumluluk alanına sahiptir ve tüm servisler ortak mimari ilkeleri kullanarak birbirleriyle güvenli ve kontrollü şekilde haberleşmektedir. Böylece sistem büyüdükçe yeni modüller eklenebilmekte, mevcut servisler bağımsız olarak geliştirilebilmekte ve platform uzun yıllar sürdürülebilir bir yapıda kalabilmektedir.
Platformun web tabanlı yönetim katmanı PHP 8.x teknolojileri üzerine inşa edilmektedir. Yönetim paneli, kullanıcı yönetimi, müzik kütüphanesi yönetimi, sistem yapılandırmaları ve operasyonel süreçler merkezi bir panel üzerinden gerçekleştirilecektir. Medya dosyaları ayrı bir medya servisi üzerinden yönetilecek, indirme işlemleri bağımsız bir Download Manager tarafından gerçekleştirilecek ve gelecekte farklı servislerin sisteme eklenebilmesi için servis tabanlı mimari korunacaktır.
CoreMusic'in en önemli hedeflerinden biri, farklı kullanım senaryolarını tek platform altında destekleyebilmesidir. Aynı kullanıcı, sistemi evindeki medya merkezi olarak kullanabilirken, aynı hesap üzerinden aracındaki bilgi-eğlence sistemine bağlanabilecek, profesyonel stüdyosunda yüksek çözünürlüklü ses işleme süreçlerini yönetebilecek veya NAS üzerinde bulunan büyük müzik arşivlerini organize edebilecektir. Böylece kullanıcı deneyimi cihaz bağımsız hâle gelmekte ve tüm medya ekosistemi tek merkezden yönetilebilmektedir.
Platform yalnızca çevrim içi çalışacak şekilde tasarlanmamıştır. Aksine, "Offline First" yaklaşımı temel mimari prensiplerinden biridir. Kullanıcılar internet bağlantısı olmadan da müzik arşivlerine erişebilecek, oynatma listelerini yönetebilecek ve yerel medya içeriklerini kullanmaya devam edebilecektir. İnternet bağlantısı yeniden sağlandığında sistem gerekli senkronizasyon işlemlerini otomatik olarak gerçekleştirecektir.
CoreMusic içerisinde gelişmiş bir medya yönetim altyapısı bulunmaktadır. Bu altyapı; sanatçı bilgileri, albüm yapıları, tür sınıflandırmaları, yüksek doğrulukta metadata yönetimi, kapak görselleri, ses analizleri, teknik medya bilgileri ve kullanıcı tarafından oluşturulan içerikleri merkezi olarak yönetmektedir. Böylece büyük müzik arşivleri dahi yüksek performansla organize edilebilmektedir.
Proje yalnızca müzik dosyalarının depolanmasını değil, aynı zamanda profesyonel ses yönetimini de hedeflemektedir. İleri aşamalarda ASIO, WASAPI, FFmpeg, Audio DSP, profesyonel ses kartları, sanal ses sürücüleri ve düşük gecikmeli ses işleme teknolojileriyle entegrasyon planlanmaktadır. Bu sayede platform yalnızca son kullanıcılar için değil, profesyonel kayıt stüdyoları ve yayın sistemleri için de uygun bir altyapı sunacaktır. Ancak bu bileşenlerin uygulanma durumu ilgili kaynaklardan doğrulanmalıdır; mevcut dokümanlar bazı bölümleri plan aşamasında göstermektedir.
CoreMusic'in bir diğer temel amacı, yapay zekâ destekli medya yönetimidir. Sistem, büyük medya koleksiyonlarını analiz ederek akıllı arama, gelişmiş filtreleme, otomatik etiketleme, öneri motorları, akıllı oynatma listeleri ve kullanıcı alışkanlıklarına göre kişiselleştirilmiş içerik yönetimi gibi yetenekleri destekleyecek şekilde tasarlanmaktadır. Böylece kullanıcı yalnızca içerik tüketen değil, medya koleksiyonunu akıllı biçimde yöneten bir deneyim yaşayacaktır.
Sistemin teknik mimarisi sürdürülebilirlik, genişletilebilirlik ve bakım kolaylığı ilkeleri üzerine kurulmaktadır. SOLID prensipleri, Clean Architecture, Hexagonal Architecture, Domain Driven Design, Event Driven Architecture, CQRS ve PSR standartları temel mühendislik yaklaşımını oluşturmaktadır. Bu sayede platformun her bileşeni bağımsız geliştirilebilir, test edilebilir ve gerektiğinde diğer sistemlerden ayrıştırılabilir.
CoreMusic projesinin en önemli bileşenlerinden biri de yapay zekâ tarafından okunabilir bilgi tabanıdır. Proje içerisinde bulunan .ai klasörü, yalnızca dokümantasyon amacıyla oluşturulmuş bir klasör değildir; sistemin kurumsal bilgi merkezi olarak tasarlanmıştır. Mimari kararlar, iş akışları, teknik notlar, karar kayıtları (Architecture Decision Records), agent tanımları, süreç yönetimleri ve proje hafızası bu klasör içerisinde tutulmaktadır. Amaç; yalnızca insanlar için değil, ChatGPT, Claude, Gemini, Codex, Cursor, RooCode, Cline ve gelecekte kullanılacak diğer yapay zekâ sistemleri için de tek doğruluk kaynağı (Single Source of Truth) oluşturmaktır. Bu yaklaşım mevcut Agent Registry ve CLAUDE.md belgeleriyle de uyumludur.
Proje boyunca bilgi tabanı sürekli geliştirilecek ancak mevcut klasör yapısı, dosya isimleri ve genel organizasyon kullanıcı onayı olmadan değiştirilmeyecektir. Dokümantasyon çalışmaları mevcut içerikleri silmek yerine iyileştirme, standartlaştırma ve profesyonelleştirme esasına göre yürütülecektir. Böylece yıllar içerisinde oluşan bilgi kaybı önlenecek, kurumsal hafıza korunacak ve yeni geliştiricilerin projeye uyum süresi önemli ölçüde azaltılacaktır.
Sonuç olarak CoreMusic, yalnızca müzik dinlemek amacıyla geliştirilen bir uygulama değildir. Bu proje; medya yönetimi, profesyonel ses teknolojileri, çoklu cihaz entegrasyonu, çevrim dışı çalışma, yapay zekâ destekli içerik yönetimi ve kurumsal bilgi tabanı yaklaşımını tek platform altında birleştiren, uzun yıllar geliştirilebilir ve sürdürülebilir olması hedeflenen kapsamlı bir dijital medya ekosistemidir. Bu vizyon doğrultusunda her teknik karar, her mimari bileşen ve her dokümantasyon çalışması aynı hedefe hizmet eder: güvenilir, modüler, genişletilebilir ve yapay zekâ sistemleri tarafından da eksiksiz anlaşılabilir bir platform oluşturmak.

CoreMusic sistemi klasik bir müzik uygulaması değil, startup seviyesinde tasarlanmış, uzun vadeli büyümeye uygun, modüler bir teknoloji ekosistemi olarak planlanmıştır; burada amaç sadece kullanıcıların müzik dinlemesini sağlamak değil, ses teknolojisi, yapay zeka, kişiselleştirme, yüksek kaliteli audio işleme ve profesyonel kullanıcı deneyimini tek bir platform altında birleştiren yeni nesil bir müzik işletim sistemi oluşturmaktır. Sistem mimarisi bu nedenle tek bir uygulama mantığıyla değil, gelecekte yatırım alabilecek ve ekip büyüdüğünde yönetilebilecek bir teknoloji şirketi altyapısı gibi düşünülmüştür.
Bu projeyi bir startup olarak ele aldığımızda ilk aşamada CoreMusic'in temel ürünü olan müzik platformu geliştirilir, kullanıcı hesapları, müzik kütüphanesi, playlist sistemi, sanatçı ve albüm yönetimi, kişiselleştirilmiş ana sayfa ve kullanıcı deneyimi tamamlanır; daha sonra sistem büyüdükçe bağımsız servisler eklenerek büyük teknoloji şirketlerindeki gibi ölçeklenebilir bir yapıya geçilir. Backend, frontend, ses motoru, indirme servisleri, medya servisleri ve donanım entegrasyonları birbirinden ayrılarak ekiplerin paralel çalışabileceği profesyonel bir organizasyon modeli oluşturulur.
CoreMusic'in startup stratejisi üç temel aşama üzerine kurulabilir; birinci aşamada MVP (Minimum Viable Product) geliştirilerek kullanıcıya çalışan bir müzik deneyimi sunulur, ikinci aşamada premium özellikler eklenerek yüksek kaliteli ses, AI öneri sistemi, profesyonel EQ, kişisel müzik profilleri ve gelişmiş kullanıcı deneyimleri oluşturulur, üçüncü aşamada ise CoreMusic sadece bir uygulama olmaktan çıkarılıp kendi ses motoruna, kendi donanım çözümlerine ve geliştirici ekosistemine sahip bağımsız bir teknoloji platformuna dönüşür.
Teknik açıdan bu sistem bir startup'ın teknoloji vizyonu gibi planlanmıştır çünkü PHP backend, Vanilla JS SPA mimarisi, C++ ses motoru, Node.js servisleri ve donanım bağlantıları aynı ekosistem içinde çalışacak şekilde organize edilmiştir; bu yapı küçük bir ekip ile başlayıp ileride backend mühendisleri, UI/UX tasarımcıları, güvenlik uzmanları, veri mühendisleri, embedded geliştiriciler ve DevOps ekipleri tarafından büyütülebilecek şekilde hazırlanmıştır.
AI tarafında ise proje sadece kod yazan bir sistem değil, kendi bilgi tabanı, agent sistemi ve çalışma kuralları olan bir geliştirme organizasyonu şeklinde tasarlanmıştır; yani yapay zeka ajanları backend, güvenlik, veri, UI, embedded, QA ve DevOps gibi uzman rollere ayrılarak büyük yazılım şirketlerindeki ekip yapısına benzer şekilde görev dağılımı yapabilir.
Yatırımcı gözüyle bakıldığında CoreMusic'in değeri sadece bir müzik uygulaması olmasından değil, sahip olduğu teknoloji altyapısından gelir; çünkü proje gelecekte Spotify benzeri streaming özellikleri, Apple Music seviyesinde kullanıcı deneyimi, profesyonel stüdyo kalitesinde ses işleme, AI destekli müzik keşfi ve IoT/donanım entegrasyonu gibi alanlara genişleyebilecek şekilde kurgulanmıştır. Ancak startup mantığında en kritik nokta, bütün sistemi aynı anda yapmak yerine önce pazara ulaşabilecek çekirdek ürünü tamamlamak, kullanıcı geri bildirimleriyle geliştirmek ve daha sonra karmaşık teknolojileri aşamalı olarak eklemektir.
Özet olarak CoreMusic, doğru yönetildiğinde bir "müzik çalar projesi" değil; audio teknoloji şirketi, AI destekli müzik platformu ve gelecekte kendi donanım ekosistemine sahip olabilecek bir teknoloji girişimi olarak konumlandırılabilecek bir projedir. Başarının anahtarı ise büyük vizyonu korurken geliştirme sürecini startup disiplininde yönetmek, önce çalışan ürün çıkarmak, sonra ölçeklemek ve her aşamada gerçek kullanıcı değerine odaklanmaktır.


CoreMusic, yalnızca müzik oynatma amacıyla geliştirilmiş bir medya player değildir. CoreMusic; bireysel 
kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi eğlence sistemleri, ev medya merkezleri
ve büyük dijital müzik arşivleri için geliştirilmiş, merkezi medya yönetimi ve profesyonel ses deneyimi
sağlayan yeni nesil bir medya ekosistemidir.

Projenin amacı; kullanıcının sahip olduğu tüm müzik içeriklerini tek merkezden yönetebilmesi, organize 
edebilmesi, analiz edebilmesi, farklı cihazlarda kullanabilmesi ve yüksek kaliteli ses deneyimi 
yaşayabilmesidir.

dkdokddokodkdokdokdokd-----------

CoreMusic Audio Engine içerisinde DSP işlemleri 32-bit floating point precision ile 
gerçekleştirilecektir. Donanım çıkış kalitesi kullanılan DAC, ADC ve audio interface 
özelliklerine bağlı olacaktır.


CoreMusic Professional Audio Engine Architecture

CoreMusic yalnızca bir medya oynatıcı değildir. Sistem aynı zamanda profesyonel seviyede ses yönetimi, 
düşük gecikmeli audio processing, DSP işlemleri ve çoklu cihaz ses ekosistemi oluşturmayı hedefleyen
bir platformdur.

CoreMusic Audio Engine; ev kullanıcıları, araç sistemleri, profesyonel stüdyolar, Raspberry Pi tabanlı 
medya merkezleri ve masaüstü işletim sistemleri için yüksek kaliteli ses işleme altyapısı sağlayacaktır.

Amaç yalnızca müzik dosyasını oynatmak değildir. Amaç; oynatma, kayıt, DSP, ses yönlendirme, kanal 
yönetimi, cihazlar arası ses aktarımı ve profesyonel ses kontrolünü tek bir mimari altında toplamaktır.3



**önce .ai/ yeniden yazılacak hatalı yerler düzeltilecek hatsız olan yerler kalcak sadece hatalı yerler 
düzeltilecek yapı katı kurallı ai'nin .ai/ kullanması zorlancak şekilde yapıcak kullancı .ai ok derse 
diğer planama adımalrıanm gecielcxek önce .ai planlamsı yazılamsı yapılamsı lazım Projeye bir ürün 
olacak şekilde yeniden tasarlamak, önce tasarlamak, planlamak, sonra kod yazmak. Tasarlama planlama
 bitmeden önce kod yazmak yoktur.!!!!!**


 
**Audio Engine çekirdeği C/C++ ile geliştirilecek ve işletim sistemlerinden bağımsız bir yapı sağlayacaktır.**

**2. Profesyonel Ses Kalitesi Hedefleri**

**CoreMusic Audio Engine aşağıdaki özellikleri destekleyecek şekilde tasarlanacaktır:**

32-bit audio processing
High Resolution Audio desteği
24-bit / 32-bit PCM işleme
96 kHz / 192 kHz örnekleme desteği
düşük latency audio pipeline
gerçek zamanlı DSP işlemleri
çok kanallı ses yönetimi
profesyonel ses kartı desteği

**Hedef kullanım:**

**Stereo**
2.0

**Surround**
5.1

**Professional**
8.1

**Studio**
8.1  & 8 Input / 8 Output

**Referanslar :**
- .ai\ui-design de ui kurlları, tasarımlar , notlar , workflow , vs herşey mevcut ev , porfesyoenl , studio için
- .ai\architecture de mimari, kurallar , notlar , workflow, vs herşey mevcut. butun mimariler burada planlanması lazım.
- .ai\electronic de electronic kurlları, notlar , workflow, vs herşey mevcut 
- .ai\architecture\electronic de mimari ve elctronic kurlları , notlar , workflow, vs herşey mevcut 
- .ai\.sql de sql veri tabanı tasarımı herşey mevuttur nromalize yapıalsı ve proejde veri tabanı buan göre ilerlenemsi lazımdır. 
- .ai\personas da persona, test kullanıcları seneryoalrı , kurallar , notlar , workflow, vs herşey mevcut 
- .ai\subdomains de subdomain, kurallar , notlar , workflow, vs herşey mevcut web prjesine göre her şey buraya yazılamlı planlanmalı
- .ai\ui-design\screens de ekran , tasarımlar , arayüzler vs mevcut 
- .ai\ui-design\screen\raspberry-pi-5 de raspberry pi 5 için ekran , tasarımlar , arayüzler vs mevcut 
- .ai\ui-design\screen\raspberry-pi-5\rpi5-home-1024 de raspberry pi 5 için ev modu ekran , tasarımlar , arayüzler vs mevcut 
- .ai\ui-design\screen\raspberry-pi-5\rpi5-profesyonel-1024 de raspberry pi 5 için profesyonel ekran , tasarımlar , arayüzler vs mevcut 
- .ai\ui-design\screen\raspberry-pi-5\rpi5-car-1024 de raspberry pi 5 için araç modu ekran , tasarımlar , arayüzler vs mevcut 
- .ai\ui-design\screen\raspberry-pi-5\rpi5-studio-1024 de raspberry pi 5 için studio ekran , tasarımlar , arayüzler vs mevcut 

tüm tasarımlar tüm notlar bu dosyaya göre yapılmalı herkezin uyması gereken bir kuraldır.

**Özetle :** .ai/ klasörünü yeniden yazmak , düzeltmek , , .ai 'yi aktif hale getirmek , projeyi AI'ya 
inanmak, .ai içindeki herşeyin doğru oldupunu varsaymak , .ai'yı kullanmak, .ai herşeydir. .ai'yı düzeltirsen
projeyi düzeltmiş olursun 


# **PROJECT**

**Bu proje normal bir Music Player değildir.**

**Bu proje;**

• Music Management Platform
• Music Library
• Download Manager
• Metadata Engine
• Audio Engine
• Professional Studio System
• Car Infotainment
• Home Media Center
• NAS Media Manager
• Streaming Platform
• Offline First Media Platform
• AI Recommendation Platform
• AI Search Engine
• Playlist Intelligence
• Music Archive System
• Multi Device Music Ecosystem

**olarak geliştirilmektedir.**

❌ Klasörleri değiştirmeyelim.
❌ Dosya isimlerini değiştirmeyelim.
❌ yeni klasör oluşturulmayacak (kullanıcı istemediği sürece)
✔ Aynı yapının içine devam edelim.
✔ aynı yapı içinde devam edeceksin
✔ mevcut dosyaları geliştireceksin
✔ eksikleri tamamlayacaksın
✔ tekrar eden içerikleri kaldıracaksın
✔ daha profesyonel hale getireceksin


# **KNOWLEDGE BASE GOAL**

Her markdown dosyası;
AI tarafından okunabilir olmalıdır.
İnsan tarafından okunabilir olmalıdır.
Başka bir AI;
sadece bu markdown dosyasını okuyarak
ilgili sistemi anlayabilmelidir.


# **EVERY DOCUMENT MUST CONTAIN**

**Her markdown dosyasında;**
Amaç
Kapsam
Terminoloji
Sistem Tanımı
Architecture
Workflow
Decision Records
Rules
Examples
Best Practices
Edge Cases
Warnings
Limitations
Dependencies
Future Roadmap
Related Documents
Cross References
Glossary
**olmalıdır.**

# **DOCUMENT QUALITY**

**Doküman;**
teknik kitap kalitesinde olmalıdır.
Wikipedia gibi olmamalıdır.
Kod yorumu gibi olmamalıdır.
Gerçek Enterprise Documentation kalitesinde olmalıdır.

# **NO HALLUCINATION**

**Asla;**
özellik uydurma
API uydurma
endpoint uydurma
sınıf uydurma
servis uydurma
karar uydurma
workflow uydurma
yasaktır.
Eğer bilgi eksikse
**VERIFICATION REQUIRED**
**olarak işaretle.**

# **SOURCE OF TRUTH**

H**er bilgi;**
önce mevcut
.ai
dokümanlarından okunacaktır.
Çelişki varsa;
not alınacak
çözüm önerilecek
**ama kullanıcı onayı olmadan karar verilmeyecek.**

Mandatory:
- Follow this rule unless technical contradiction exists.
- If conflict occurs, report before changing.

AI Orchestrator
``` code
                    USER
                      │
                      ▼
          ┌────────────────────┐
          │ AI ORCHESTRATOR    │
          │ (Master Agent)     │
          └─────────┬──────────┘
                    │
      ┌─────────────┼─────────────┐
      │             │             │
      ▼             ▼             ▼
 Repository     Knowledge      Task Analyzer
 Discovery      Discovery
      │             │
      └──────┬──────┘
             ▼
     Conflict Detector
             │
             ▼
      Decision Engine
             │
             ▼
      User Approval Gate
             │
             ▼
      Implementation Engine
             │
             ▼
       Validation Engine
             │
             ▼
       Documentation Sync
PHASE Workflow
PHASE 0
Repository Snapshot
        │
        ▼
PHASE 1
Repository Discovery
        │
        ▼
PHASE 2
Knowledge Discovery
        │
        ▼
PHASE 3
Markdown Discovery
        │
        ▼
PHASE 4
Architecture Discovery
        │
        ▼
PHASE 5
Conflict Detection
        │
        ▼
PHASE 6
Gap Detection
        │
        ▼
PHASE 7
Improvement Proposal
        │
        ▼
WAIT USER APPROVAL
        │
        ▼
PHASE 8
Refactoring
        │
        ▼
PHASE 9
Cross Reference
        │
        ▼
PHASE 10
Validation
        │
        ▼
PHASE 11
Quality Report
        │
        ▼
PHASE 12
Knowledge Sync
Agent Hierarchy
                    MASTER AGENT
                         │
 ────────────────────────┼────────────────────────
                         │
        ┌────────────────┼────────────────┐
        │                │                │
        ▼                ▼                ▼

Repository         Knowledge        Documentation
Manager            Manager          Manager

        ▼                ▼                ▼

Architecture       Workflow         Decision

        ▼                ▼                ▼

Backend            Frontend         Database

        ▼                ▼                ▼

Security           DevOps           QA

        ▼                ▼                ▼

Audio DSP          AI Search        Metadata

        ▼                ▼                ▼

Downloader         Streaming        Media Engine

        ▼                ▼                ▼

Validation         Report           Index Sync
Documentation Workflow
Read File
    │
    ▼
Understand
    │
    ▼
Extract Metadata
    │
    ▼
Find Duplicate
    │
    ▼
Find Missing
    │
    ▼
Find Conflict
    │
    ▼
Improve
    │
    ▼
Cross Reference
    │
    ▼
Validate
    │
    ▼
Quality Score
Coding Workflow
User Request
      │
      ▼
Repository Discovery
      │
      ▼
Architecture Discovery
      │
      ▼
Affected Modules
      │
      ▼
Dependency Check
      │
      ▼
Security Review
      │
      ▼
Implementation Plan
      │
      ▼
WAIT USER APPROVAL
      │
      ▼
Code
      │
      ▼
Unit Test
      │
      ▼
Integration Test
      │
      ▼
Documentation Update
      │
      ▼
Knowledge Sync
Audio Pipeline Workflow
Music Source
      │
      ▼
Downloader
      │
      ▼
Metadata Engine
      │
      ▼
Media Storage
      │
      ▼
Library Manager
      │
      ▼
Playlist Engine
      │
      ▼
Recommendation AI
      │
      ▼
Audio Engine
      │
      ▼
DSP
      │
      ▼
Equalizer
      │
      ▼
Output Device
      │
      ▼
Car
Home
Studio
Bluetooth
USB
NAS
AI Knowledge Workflow
User Prompt
      │
      ▼
Keyword Detection
      │
      ▼
Read Index
      │
      ▼
Read Brain
      │
      ▼
Read Memory
      │
      ▼
Read Decisions
      │
      ▼
Read Workflow
      │
      ▼
Read Current MD
      │
      ▼
Verification
      │
      ▼
Answer
Agent Communication
Master Agent
      │
      ▼
Task Dispatcher
      │
      ▼
─────────────────────────────────────
│ Backend Agent                    │
│ Frontend Agent                   │
│ Security Agent                   │
│ Database Agent                   │
│ Audio Agent                      │
│ Metadata Agent                   │
│ Downloader Agent                 │
│ Streaming Agent                  │
│ QA Agent                         │
│ DevOps Agent                     │
│ Documentation Agent              │
│ Knowledge Agent                  │
─────────────────────────────────────
      │
      ▼
Merge Results
      │
      ▼
Validator
      │
      ▼
User
Knowledge Graph
                index.md
                    │
      ┌─────────────┼─────────────┐
      │             │             │
      ▼             ▼             ▼

architecture     domains      workflows

      │             │             │

      ▼             ▼             ▼

backend         player        downloader

      │             │             │

      ▼             ▼             ▼

database       metadata      streaming

      │             │             │

      ▼             ▼             ▼

api            security      ui-design

      │             │             │

      ▼             ▼             ▼

brain.md      decisions      log.md
``` 


```
CLAUDE.md
│
├── Project Identity
├── Critical Rules
├── Navigation Rules
└── AI Behavior
```

**1. CLAUDE.md**
**AI çalışma anayasası**
**(sınırsız)**

**2. AGENTS.md**
**Agent davranışları**
**(sınırsız)**

**3. .ai Vault**
**Tüm teknik bilgi**
**(sınırsız)**

**Target:**
- Latency <5ms depending on hardware
- THD+N target according to DAC/ADC chain

Her Architecture Decision şu formatta yazılmalıdır:

- ADR ID
- Problem
- Alternatives
- Decision
- Reason
- Trade-off
- Impact
- Status
- Web Arama Sonucu, bulguları, kararlar


CoreMusic'i sadece bir ses kartı veya audio interface cihazı olarak düşünmek doğru değildir. Piyasadaki birçok ürün genellikle tek bir probleme odaklanır; örneğin profesyonel audio interface cihazları çoklu giriş/çıkış, düşük gecikme ve stüdyo kullanımı için tasarlanır. [1]

CoreMusic ise bir audio teknoloji organizasyonu gibi tasarlanır. Bu organizasyonun amacı; donanım, yazılım, profesyonel stüdyo teknolojileri, tüketici ürünleri ve yapay zekâ destekli ses sistemlerini tek bir ekosistem altında toplamaktır.

CoreMusic Audio Organization mantığında:

Hardware Division, özel audio kartları, DAC/ADC çözümleri, DSP çipleri, amplifikatör ve cihaz donanımlarını geliştirir.
Software Division, C++ Audio Engine, DSP Engine, Mixer, sürücüler, kontrol paneli ve AI Audio sistemlerini oluşturur.
Studio Division, ASIO, WASAPI, kayıt, monitoring, routing ve düşük gecikmeli profesyonel çalışma altyapısını sağlar. WASAPI, Windows üzerinde uygulamalar ile ses donanımı arasındaki veri akışını yönetmek için kullanılan temel ses mimarilerinden biridir. [8]
Consumer Division, Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses sistemlerini kapsar.
Research Division, AI DSP, yeni codec teknolojileri ve geleceğin audio donanımlarını araştırır.

Bu nedenle hedef, tek bir cihaz satmak değil; Apple'ın ekosistem yaklaşımı, RME ve Universal Audio'nun profesyonel audio altyapısı, Sonos'un tüketici ses deneyimi ve Dolby'nin ses teknolojisi yaklaşımı gibi büyük bir audio platformu oluşturmaktır.

Ancak böyle bir sistem tek seferde yapılmaz. Doğru mühendislik sırası:

C++ Audio Engine
WASAPI / ALSA ses çıkışı
DSP Engine
Mixer sistemi
ASIO entegrasyonu
8x8 profesyonel audio interface
Özel CoreMusic Audio Board

Yani CoreMusic'in vizyonu: normal kullanıcı müzik platformu + profesyonel stüdyo sistemi + özel donanım ekosisteminin birleştiği bağımsız bir audio teknoloji organizasyonu oluşturmaktır.


# Tek Doğruluk Kaynağı (Single Source of Truth)

Her bilgi öncelikle aşağıdaki kaynaklardan doğrulanacaktır:

1. `.CLAUDE.md`
2. `.AGENTS.md`
3. `.WORKFLOW.md`
4. `.ai/index.md`
5. `.ai/keys.md`
6. `.ai/CLAUDE.md`
7. `.ai/AGENTS.md`
8. `.ai/WORKFLOW.md`
9. `.ai/brain.md`
10. `.ai/memory.md`
11. `.ai/log.md`
12. İlgili markdown dosyaları
13. Kaynak kodu
14. Resmî dokümantasyon

Bu kaynaklarda doğrulanamayan hiçbir bilgi kesin gerçek olarak yazılmayacaktır.

# Çalışma Akışı

Her markdown dosyası için aşağıdaki süreç uygulanacaktır:
```
## Phase 1
Repository Discovery
↓
## Phase 2
AI Knowledge Discovery
↓
## Phase 3
Existing Markdown Analysis
↓
## Phase 4
Conflict Detection
↓
## Phase 5
Duplicate Detection
↓
## Phase 6
Gap Detection
↓
## Phase 7
Improvement Proposal
↓
**WAIT USER APPROVAL**
↓
## Phase 8
Document Refactoring
↓
## Phase 9
Cross Reference Update
↓
## Phase 10
Index Update
↓
## Phase 11
Validation
↓
## Phase 12
Quality Report
```

# **Her Doküman İçin Çıktı Formatı**

Her dosya aşağıdaki sırayla değerlendirilecektir:

1. Current Problems
2. Repeated Sections
3. Missing Information
4. Improvement Plan
5. Updated Document


# **Öncelikli Amaç**

**Amaç 1000 satır üretmek değildir.**

**Amaç;**

* kaliteli
* sürdürülebilir
* yeniden kullanılabilir
* AI tarafından kolay okunabilir
* kurumsal standartlarda
* teknik doğruluğu yüksek
* uzun yıllar kullanılabilecek

**bir Knowledge Base oluşturmaktır.**

**Kalite, satır sayısından her zaman daha önemlidir.**

**# Hallüsinasyon Politikası**

**Kesinlikle aşağıdakiler uydurulmayacaktır:**

* API
* Endpoint
* Sınıf
* Servis
* Workflow
* Mimari
* Roadmap
* Özellik
* Teknik karar

**Doğrulanamayan her bilgi aşağıdaki formatta belirtilmelidir:**

```
**VERIFICATION REQUIRED**
```

**ve aşağıdaki bilgiler eklenmelidir:**

* Eksik Kanıt
* Muhtemel Kaynak
* Kullanıcı Onayı Gerekiyor

# **Dokümantasyon Kalitesi**

**Dokümanlar;**

* Enterprise seviyesinde
* Teknik kitap kalitesinde
* Kurumsal standartlarda
* Tekrarsız
* Tutarlı
* AI tarafından kolay okunabilir
* Markdown standardına uygun
* Başlık hiyerarşisi doğru
* Tablo kullanımı doğru
* ASCII diyagramları düzenli
* Workflow diyagramları açıklayıcı
* Decision tabloları eksiksiz

**olmalıdır.**

# **Her Dokümanda Bulunması Gereken Bölümler**

**Her markdown dosyasında mümkün olduğunca aşağıdaki başlıklar bulunmalıdır:**

* Amaç
* Kapsam
* Terminoloji
* Sistem Tanımı
* Mimari
* Workflow
* Architecture Decision Records (ADR)
* Kurallar
* Örnekler
* Best Practices
* Edge Cases
* Warning
* Limitations
* Dependencies
* Future Roadmap
* Related Documents
* Cross References
* Glossary

# **AI Knowledge Base Hedefi***

**Tüm markdown dosyaları;**

* İnsan tarafından okunabilir olmalıdır.
* AI tarafından kolay analiz edilebilir olmalıdır.
* Başka bir yapay zekâ yalnızca ilgili markdown dosyasını okuyarak sistemi anlayabilmelidir.

**Bu nedenle her markdown dosyası tek başına yeterli bilgi içermelidir.**

# **Mevcut Yapı Korunacaktır**

Hiçbir zaman;

.ai/

**klasör yapısı değiştirilmeyecektir.**

**Her çalışma mevcut dosya üzerinde gerçekleştirilecektir.**

**Yeni dosya yalnızca kullanıcı açıkça isterse oluşturulacaktır.**

# **Serbest Olan İşlemler**

**Aynı dosya üzerinde;**

* içerik iyileştirme
* profesyonelleştirme
* eksikleri tamamlama
* tekrar eden bölümleri birleştirme
* markdown standardına uygun hale getirme
* teknik açıklamaları geliştirme
* ASCII diyagramlarını iyileştirme
* tablo ekleme
* workflow ekleme
* glossary ekleme
* cross-reference ekleme

**serbesttir.**

# **Bilgi Kaynağı**

**Projede hali hazırda büyük bir bilgi tabanı bulunmaktadır.**

.ai/

**Bu klasör içerisinde;**

* kararlar
* workflow'lar
* agent tanımları
* mimari kararlar
* teknik dokümantasyon
* tasarım kararları
* notlar
* roadmap
* knowledge base
* markdown dosyaları

**bulunmaktadır.**

A**ncak mevcut dokümantasyon tam anlamıyla düzenli değildir.**

**Tekrarlayan içerikler,
eksik açıklamalar,
karışık başlık yapıları,
standart dışı markdown kullanımları bulunmaktadır.**

**Bu nedenle mevcut bilgi tabanı yeniden düzenlenecektir.**

# **Projenin Amacı**

**Amaç yalnızca müzik çalmak değildir.**

**Sistem;**

* Müziği organize edebilmelidir.
* Müziği indirebilmelidir.
* Müziği arşivleyebilmelidir.
* Müzik kütüphanesini yönetebilmelidir.
* Metadata yönetebilmelidir.
* AI destekli arama yapabilmelidir.
* Playlist oluşturabilmelidir.
* Çoklu cihazlarda çalışabilmelidir.
* Araç, ev ve profesyonel stüdyo kullanım senaryolarını desteklemelidir.
* Otomatik müzik indirme
* Müzik yönetimi
* Medya arşivleme
* Profesyonel ses yönetimi
* Ev medya merkezi
* Araç içi bilgi-eğlence (Car Infotainment)
* Stüdyo ses sistemi
* NAS medya yönetimi
* AI destekli müzik öneri sistemi
* Çoklu cihaz senkronizasyonu
* Offline First medya platformu
* Streaming altyapısı
* ASIO Ses 32 Bit Desteği
* AI İle Otmatik ses Equalizer DSP yönetimi Sync

**gibi birçok sistemi tek platform altında birleştiren kurumsal seviyede bir medya ekosistemidir.**
**Bu nedenle proje, klasik bir Media Player olarak değerlendirilmemelidir.**

**ASIO Konusunda Duzletme**
Windows
 ├── ASIO
 ├── WASAPI Exclusive

Linux/Raspberry Pi
 ├── ALSA
 ├── PipeWire
 ├── JACK

macOS
 └── CoreAudio
 
**8x8 kanal desteği kullanılan profesyonel audio interface donanımına bağlı olacaktır.**

 **DSP de hem Output da hemde Input da reverb gibi sistemler olamsı lazım oda ayaralrı genmiş kosner
 düğün salonu efekti gibi samsung j7 t 2016 telefdonda olan geniş konser düğün salonu efekrti olamsı
 lazım**

**Şu an:**

**CoreMusic = Büyük vizyon**


**Olması gereken:**

**CoreMusic Company Architecture**
 
```
                    CoreMusic Group
                          |
        ┌─────────────────┼─────────────────┐
        |                 |                 |
        ▼                 ▼                 ▼

 CoreMusic Software   CoreMusic Audio   CoreMusic Hardware

        |                 |                 |
        ▼                 ▼                 ▼

 Media Platform      Audio Engine      Devices
 AI Platform         DSP Platform      DAC/ADC
 Cloud               Studio Tools      Amplifier
```
```
CoreMusic OS
      |
      |
CoreMusic Platform
      |
      |
CoreMusic Device
      |
      |
CoreMusic Audio Hardware
```
```

                         ┌───────────────────────────────┐
                         │                               │
                         │     COREMUSIC AUDIO GROUP     │
                         │                               │
                         │  AI + Hardware + Software     │
                         │  Professional Audio Ecosystem│
                         └───────────────┬───────────────┘
                                         │
        ┌────────────────────────────────┼────────────────────────────────┐
        │                                │                                │
        ▼                                ▼                                ▼


┌───────────────────┐          ┌───────────────────┐          ┌───────────────────┐
│ HARDWARE DIVISION │          │ SOFTWARE DIVISION │          │ STUDIO DIVISION   │
├───────────────────┤          ├───────────────────┤          ├───────────────────┤
│                   │          │                   │          │                   │
│ Custom Audio PCB  │          │ Audio Engine C++  │          │ ASIO Driver       │
│ DAC / ADC         │          │ DSP Engine        │          │ WASAPI / ALSA     │
│ DSP Chip          │          │ Mixer System      │          │ Recording        │
│ Amplifier         │          │ AI Audio          │          │ Monitoring       │
│ Audio Interface   │          │ Control Panel     │          │ Routing          │
│ Device Firmware  │          │ CoreMusic OS      │          │ Low Latency      │
│                   │          │                   │          │                   │
└─────────┬─────────┘          └─────────┬─────────┘          └─────────┬─────────┘
          │                              │                              │
          └──────────────────────────────┼──────────────────────────────┘
                                         │
                                         ▼


                         ┌───────────────────────────────┐
                         │                               │
                         │    COREMUSIC AUDIO PLATFORM   │
                         │                               │
                         └───────────────┬───────────────┘
                                         │


                 ┌───────────────────────┴───────────────────────┐
                 │                                               │


                 ▼                                               ▼

       ┌─────────────────┐                         ┌────────────────────┐
       │   NORMAL MODE   │                         │ PROFESSIONAL MODE  │
       ├─────────────────┤                         ├────────────────────┤
       │                 │                         │                    │
       │ Bluetooth       │                         │ ASIO               │
       │ WiFi Audio      │                         │ WASAPI             │
       │ Music Player    │                         │ DSP Processing     │
       │ Streaming       │                         │ Mixer              │
       │ EQ              │                         │ 8x8 I/O            │
       │ Playlist        │                         │ Recording          │
       │ Home Audio      │                         │ Studio Monitoring  │
       │                 │                         │                    │
       └─────────────────┘                         └─────────┬──────────┘
                                                             │
                                                             ▼


                         ┌───────────────────────────────┐
                         │                               │
                         │        AUDIO ECOSYSTEM        │
                         │                               │
                         ├───────────────────────────────┤
                         │                               │
                         │  Consumer Audio               │
                         │  Professional Studio          │
                         │  Creator Tools                │
                         │  AI Audio Enhancement         │
                         │  Smart Home Audio             │
                         │  Automotive Audio             │
                         │                               │
                         └───────────────────────────────┘

COREMUSIC GROUP
│
├── CoreMusic Hardware
│   └── Audio Devices
│       ├── Consumer DAC
│       ├── Studio Interface
│       └── Professional Rack Units
│
├── CoreMusic Software
│   └── Audio OS
│       ├── Player
│       ├── DSP
│       ├── AI Engine
│       └── Cloud Platform
│
├── CoreMusic Studio
│   └── Creator Ecosystem
│       ├── Recording
│       ├── Mixing
│       └── Mastering
│
├── CoreMusic Home
│   └── Consumer Products
│       ├── Speakers
│       ├── TV Audio
│       └── Smart Devices
│
└── CoreMusic Research
    ├── AI Audio
    ├── New Codecs
    ├── DSP Algorithms
    └── Future Hardware

 ```


```
┌──────────────────────────────────────────────┐
│              CoreMusic Platform              │
└──────────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│              Control Plane                   │
│ OS Management • AI Control • User Interface  │
└──────────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│          Media Management Layer              │
│ Music DB • Metadata • Cloud Sync • AI Engine │
│ Library Management • Playlist System         │
│ AI Recommendation                            │
└──────────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│           Audio Runtime Platform             │
│ DSP • Mixer • EQ • Codec • Real-Time Engine  │
│ Low Latency Pipeline                         │
└──────────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│          Device / Hardware Layer             │
│ Driver • HAL • Interface • Communication     │
│ Hardware Abstraction • Device Control        │
└──────────────────────────────────────────────┘
                       │
                       ▼
┌──────────────────────────────────────────────┐
│              Custom Hardware                 │
│ Audio Chip • DAC • Amplifier • Embedded HW   │
│ Dedicated Audio Chip • Custom Sound Card     │
│ Premium Audio Components                     │
└──────────────────────────────────────────────┘
```
	  
Evet, bu durumda Korg or yamaha org/aranjör mantığı değil, hedef:
Akıllı Hi-Fi Teyp / Network Audio Player / Car & Home Audio System
Yani eski teybin modern, çok gelişmiş hali:

```
                         CoreMusic Audio Device
                                  |
                                  |
        -------------------------------------------------
        |                       |                       |
        ▼                       ▼                       ▼
   Hardware                 Software                Web System
        |                       |                       |
        |                       |                       |
        ▼                       ▼                       ▼

┌─────────────────┐   ┌────────────────────┐   ┌─────────────────┐
│   PCB Board     │   │ C++ Audio Engine   │   │  PHP Backend    │
└─────────────────┘   └────────────────────┘   └─────────────────┘
        |                       |                       |
        ▼                       ▼                       ▼

┌─────────────────┐   ┌────────────────────┐   ┌─────────────────┐
│ DAC             │   │ DSP Engine         │   │ Music Library   │
│                 │   │                    │   │                 │
│ Digital → Analog│   │ Audio Processing   │   │ Database        │
└─────────────────┘   └────────────────────┘   └─────────────────┘


┌─────────────────┐   ┌────────────────────┐   ┌─────────────────┐
│ ADC             │   │ Audio Driver       │   │ Download System │
│                 │   │                    │   │                 │
│ Analog → Digital│   │ Hardware Control   │   │ File Management │
└─────────────────┘   └────────────────────┘   └─────────────────┘


┌─────────────────┐   ┌────────────────────┐   ┌─────────────────┐
│ AMP             │   │ Low Latency Engine │   │ User Panel      │
│                 │   │                    │   │                 │
│ Amplifier Stage │   │ Real-Time Audio    │   │ Account Control │
└─────────────────┘   └────────────────────┘   └─────────────────┘


┌─────────────────┐   ┌────────────────────┐   ┌─────────────────┐
│ Bluetooth       │   │ EQ                 │   │ API             │
│                 │   │                    │   │                 │
│ Wireless Audio  │   │ Equalizer System   │   │ External Access │
└─────────────────┘   └────────────────────┘   └─────────────────┘


┌─────────────────┐   ┌────────────────────┐
│ WiFi            │   │ Mixer              │
│                 │   │                    │
│ Network Audio   │   │ Audio Routing      │
└─────────────────┘   └────────────────────┘


┌─────────────────┐   ┌────────────────────┐
│ Display         │   │ Device Control     │
│                 │   │                    │
│ Touch Interface │   │ Hardware Management│
└─────────────────┘   └────────────────────┘

```

# **CoreMusic Smart Audio System — Proje Başlangıç Dokümanı** 

```

                         CoreMusic Smart Audio System
                                      |
        ----------------------------------------------------------------
        |                         |                                    |
        ▼                         ▼                                    ▼

 Hardware Layer             Software Layer                    Web / Cloud Layer

        |                         |                                    |
        |                         |                                    |
        ▼                         ▼                                    ▼

   Audio Hardware          Core Audio Engine                  PHP Backend

        |                         |                                    |
        |                         |                                    |
        ▼                         ▼                                    ▼

 Digital Input             C++ Runtime Engine              User Management
        |
        |-- USB              |
        |-- WiFi             |-- Decoder                     |
        |-- Bluetooth        |-- Buffer Manager              |
        |-- Network          |-- Mixer                       |
                             |-- DSP Engine                  |
                             |-- EQ                          |
                             |-- Device Control              |
                             |-- Output Driver               |

        ↓

 Audio Processor

        ↓

 DSP Processing

        |
        |-- Equalizer
        |-- Bass Control
        |-- Treble Control
        |-- Room Correction
        |-- Noise Reduction

        ↓

 High Quality DAC

        ↓

 Amplifier Stage

        ↓

 Speaker Output

 ```

# **Sistem Akışı**

```
Phone / Web Panel
        ↓
PHP API
        ↓
CoreMusic Device
        ↓
C++ Audio Engine
        ↓
DSP Pipeline
        ↓
DAC
        ↓
Amplifier
        ↓
Speaker

```

# **Otomatik Müzik Yönetimi**

```
Music Scanner
        ↓
Metadata Reader
        ↓
Album Cover Downloader
        ↓
Music Database
        ↓
Playlist Engine
        ↓
Playback Engine
```

### **Geliştirme Fazları**
## **Phase 1 — Prototype**

**Amaç:**  Çalışan ilk cihaz

Mini PC / Raspberry Pi
        +
USB DAC
        +
Amplifier
        +
C++ Audio Engine
        +
PHP Control Panel

**Hedef:**
Ses oynatma
Web kontrol
Temel DSP
Kütüphane yönetimi

## **Phase 2 — Custom Hardware**

**Amaç:**  Kendi cihazını üretmek

Custom PCB
        +
Power Management
        +
DAC Circuit
        +
Amplifier Circuit
        +
MCU Controller

**Hedef:**
Daha düşük noise
Daha iyi güç yönetimi
Özel ses karakteri

## **Phase 3 — Professional Product**

**Amaç:**  Ticari ürün

Custom Audio Board
        +
Dedicated DSP Chip
        +
Touch Display
        +
Car Version
        +
Home Audio Version

## **Proje Konumlandırması**

Bu proje klasik "teyp yenileme" projesi değildir.

**Doğru tanım:** : CoreMusic Smart Audio System

**Birleşimi:**

Classic Tape Experience
        +
Hi-Fi Audio Quality
        +
Network Music Player
        +
Custom Hardware
        +

Embedded Software Ecosystem

**Başlamadan Önce Hazırlanacak Ana Doküman:**

00-CoreMusic-Architecture.md

**İçerik:**

1. Vision & Product Definition
2. System Architecture
3. Hardware Specification
4. Audio Pipeline Design
5. C++ Audio Engine Design
6. DSP Architecture
7. PHP Backend Architecture
8. API Design
9. Database Schema
10. PCB Roadmap
11. Prototype BOM List
12. Development Phases
13. Testing Strategy
14. Production Roadmap

Bu doküman hazırlanırsa proje "fikir" olmaktan çıkar, mühendislik gereksinimleri tanımlanmış bir embedded audio product architecture haline gelir.

**CoreMusic Audio System Master Architecture**

1. Hardware Architecture
2. PCB Design
3. Audio Pipeline
4. DSP Architecture
5. Firmware
6. C++ Engine
7. PHP Backend
8. Database
9. Web UI
10. Manufacturing Plan

**Eski şirket modeli:**
```
PC
 |
Ses Kartı
 |
Hoparlör
```

```
CoreMusic modeli:

Kendi Audio Platformu
        |
        ├── İşletim Sistemi
        ├── Yazılım
        ├── DSP Teknolojisi
        ├── Donanım
        ├── AI Ses İşleme
        └── Kullanıcı Ekosistemi
```

```
                    ┌─────────────────────────────┐
                    │        COREMUSIC OS         │
                    │      Audio Ecosystem        │
                    └──────────────┬──────────────┘
                                   │
          ┌────────────────────────┴────────────────────────┐
          │                                                 │
          ▼                                                 ▼

 ┌──────────────────────┐                       ┌──────────────────────┐
 │      SOFTWARE        │                       │      HARDWARE        │
 │   Audio Platform     │                       │    Audio Platform    │
 └──────────┬───────────┘                       └──────────┬───────────┘
            │                                              │
            ▼                                              ▼

 ┌──────────────────────┐                       ┌──────────────────────┐
 │   Audio Engine       │                       │   Audio Board        │
 │   Music Processing   │                       │   Custom Hardware    │
 └──────────┬───────────┘                       └──────────┬───────────┘
            │                                              │
            ▼                                              ▼

 ┌──────────────────────┐                       ┌──────────────────────┐
 │   DSP Engine         │                       │   DAC / ADC          │
 │   Real Time Audio    │                       │   Signal Conversion  │
 └──────────┬───────────┘                       └──────────┬───────────┘
            │                                              │
            ▼                                              ▼

 ┌──────────────────────┐                       ┌──────────────────────┐
 │   AI Processing      │                       │   DSP Chip           │
 │   Smart Audio        │                       │   Hardware DSP       │
 └──────────┬───────────┘                       └──────────┬───────────┘
            │                                              │
            ▼                                              ▼

 ┌──────────────────────┐                       ┌──────────────────────┐
 │   Audio Driver       │                       │   Amplifier          │
 │   Low Latency        │                       │   Power Output       │
 └──────────┬───────────┘                       └──────────┬───────────┘


                    ┌─────────────────────────────┐
                    │     KULLANICI DENEYİMİ      │
                    └──────────────┬──────────────┘
                                   │
        ┌──────────────────────────┼──────────────────────────┐
        │                         │                          │
        ▼                         ▼                          ▼

       EV                     STÜDYO                     ARAÇ / MOBİL


                    ┌─────────────────────────────┐
                    │       CoreMusic Şirketi     │
                    │                             │
                    │  Donanım + OS + Yazılım     │
                    │  Servis + Ekosistem         │
                    └─────────────────────────────┘

 ```

**Ben bunu final mimaride şöyle yapardım:**

**CoreMusic Runtime**
```
├── CoreMusic Control Service
│
├── CoreMusic Media Service
│
├── CoreMusic Audio Service
│
├── CoreMusic Device Service
│
├── CoreMusic Network Audio Service
│
└── CoreMusic AI Service
```

**İçleri:**

**Audio Service:**
```
 ├── Player
 ├── Audio Graph
 ├── Mixer
 ├── DSP
 ├── Recorder
 └── Output Routing
```

**Sebep:**

**50 tane process = IPC cehennemi.**


**Use dedicated key management.**
**Derive keys with Argon2id only where password-based encryption is required.**

**Unit coverage >=90%**

**Enterprise için güzel ama başlangıçta zorunlu yapmak geliştirmeyi yavaşlatabilir.**

**Daha iyi:**

**Critical modules:**
**90%**

**Normal modules:**
**70-80%**

**Tier 1**
Windows min ver: xp max ver : 11 

**Tier 2**
Linux

**Tier 3**
macOS

**Tier 4**
Raspberry Pi

**Tier 5**
ReactOS
Custom Hardware
ReactOS Compatibility Research

**Status:**
Experimental

**Verification Required:**
**CoreMusic Domains:**

**Audio Session Manager**

**Session ID**
- User
- Device
- Zone
- Input
- Output
- DSP Profile
- Clock State
- Network State
- Playback State

**Servisleri fazla bölme riski var**

**Şu yapı:**

AudioEngine.exe
DSP.exe
Mixer.exe
Player.exe
Connectivity.exe
DeviceManager.exe
NetworkAudio.exe

**güzel görünüyor ama dikkat.**

**Her biri ayrı process olursa:**
IPC artar
latency artar
debug zorlaşır

**Ben şöyle yapardım:**
```
CoreMusic Runtime

coremusic-audio-service
 ├── Player
 ├── Mixer
 ├── DSP
 ├── Recorder

coremusic-device-service
 ├── Bluetooth
 ├── WiFi
 ├── Device

coremusic-media-service
 ├── Library
 ├── Metadata
 ├── Downloader
```
**Daha dengeli.**

**CoreMusic için işletim sistemi destek mimarisi şöyle ayrılmalı:**

```
                         CoreMusic Platform
                                |
                                |
                    ┌───────────┴───────────┐
                    │                       │
                    ▼                       ▼

              Desktop OS              Embedded OS

                    │                       │
                    │                       │
        ┌───────────┼───────────┐          │
        │           │           │          │
        ▼           ▼           ▼          ▼


     Windows      Linux       macOS    Raspberry Pi OS


        │           │           │          │


        ▼           ▼           ▼          ▼


┌────────────┐ ┌────────────┐ ┌────────────┐ ┌───────────┐
│ Legacy     │ │ Ubuntu     │ │ Intel Mac  │ │ ARM64     │
│ Windows XP │ │ Debian     │ │ macOS      │ │ RPi       │
│ Vista      │ │ Arch Linux │ │            │ │           │
│ 7          │ │ Fedora     │ │             │ │           │
│ 8          │ │            │ │             │ │           │
└────────────┘ └────────────┘ └────────────┘ └───────────┘


┌────────────┐
│ Modern     │
│ Windows    │
│ 10         │
│ 11         │
└────────────┘

```

**Windows Support**

```
Windows Platform
        |
        |
        ├── Legacy Windows
        |
        |    Minimum:
        |    Windows XP
        |
        |    XP
        |    Vista
        |    7
        |    8
        |    8.1
        |
        |
        └── Modern Windows
             
             Windows 10
             Windows 11

             Required:
             Windows SDK
             WASAPI
             WDK (Driver)
             Visual Studio
```
**Windows SDK sürümleri Microsoft tarafından ayrı arşiv ve güncel paketler olarak yayınlanmaktadır.**
**geliştirme ortamında gerekli olan sdk  kuruludur ** xp den tut windows 11 e kadar** **hespi kurulu**

**Linux Support**

```
Linux Platform

        |
        |
        ├── Ubuntu
        |
        ├── Debian
        |
        ├── Arch Linux
        |
        ├── Fedora
        |
        └── Embedded Linux


Audio Stack:

ALSA
 |
PipeWire
 |
JACK

```
*macOS Support**

```
                     macOS

                       |
        ┌──────────────┴──────────────┐
        │                             │

        ▼                             ▼

 macOS Legacy                    macOS Modern


        │                             │

        ▼                             ▼


 Intel Mac                     Apple Silicon

 x86_64                        ARM64


        │                             │

        ▼                             ▼


macOS 10.0                 macOS 11+
Cheetah                    Big Sur

macOS 10.x                macOS 26
Intel Era                 Apple Silicon Era
```

**Xcode / Compiler Yapısı**

```
                    CoreMusic Build System


                           |
                           ▼


                    C++ Audio Engine


                           |
        ┌──────────────────┼──────────────────┐
        │                  │                  │

        ▼                  ▼                  ▼


    Windows             Linux             macOS


    MSVC                GCC/Clang        Xcode

    WDK                 CMake            Apple SDK

    WASAPI              ALSA             CoreAudio


        │                  │                  │

        └──────────────────┼──────────────────┘

                           ▼


                  Cross Platform Layer

                         C++20
                         CMake
                         JUCE
                         Audio Abstraction
```

**Önerilen CoreMusic OS Matrix**

```
CoreMusic Supported OS

Desktop:

Windows
├── XP       (Legacy Research)
├── 7        (Legacy)
├── 10       (Supported)
└── 11       (Primary)


Linux
├── Ubuntu   (Primary)
├── Debian   (Server/NAS)
├── Arch     (Advanced Users)


macOS

Intel:
├── macOS 10.x
├── macOS 11-15

Apple Silicon:
├── macOS 11+
└── macOS 26


Embedded:

├── Raspberry Pi OS
├── Linux ARM64
└── Custom Hardware OS


```


Bunu .ai/architecture/ altında temel mimari başlangıç doküman yapısı olarak oluştur:
```
.ai/
└── architecture/
    ├── 01-domain-architecture.md
    ├── 02-zone-session-architecture.md
    ├── 03-audio-runtime-architecture.md
    ├── 04-os-platform-abstraction.md
    └── 05-service-boundary-decision.md

Çalışma sırası:

Vision
  ↓
Domain
  ↓
Architecture
  ↓
ADR
  ↓
API Contract
  ↓
Database
  ↓
UI
  ↓
Code
```
```
                    CoreMusic Raspberry Pi
              Professional Audio Hardware System


                         USER CONTROL
                              |
                              |
                    CoreMusic UI / App
                              |
                              |
                              v

+------------------------------------------------+
|              Raspberry Pi Platform             |
|                                                |
|  Raspberry Pi 5 / ARM64                        |
|                                                |
|  - Linux OS                                    |
|  - CoreMusic Runtime                           |
|  - Device Manager                              |
|  - Network Manager                             |
|  - Bluetooth / WiFi                            |
+------------------------------------------------+
                              |
                              |
                              v

+------------------------------------------------+
|            CoreMusic Audio Engine              |
|                                                |
|  - Audio Pipeline                              |
|  - DSP Processing                              |
|  - Equalizer                                   |
|  - Mixer                                       |
|  - Volume Control                              |
|  - Audio Routing                               |
|  - Multi-room Audio                            |
|  - Playback Engine                             |
+------------------------------------------------+
                              |
                              |
             +----------------+----------------+
             |                                 |
             v                                 v


+----------------------+          +----------------------+
|     USB Audio        |          |      I2S Interface   |
|                      |          |                      |
| USB DAC              |          | I2S DAC HAT          |
| Audio Interface      |          | PCM5102              |
| Professional Sound   |          | ESS / AKM DAC        |
| Card                 |          |                      |
+----------------------+          +----------------------+
             |                                 |
             |                                 |
             +----------------+----------------+
                              |
                              |
                              v


+------------------------------------------------+
|                    DAC Layer                   |
|                                                |
|  Digital Audio → Analog Audio Conversion       |
|                                                |
|  - High Resolution Audio                       |
|  - 32-bit / 192kHz                             |
|  - Low Noise Output                            |
+------------------------------------------------+
                              |
                              |
                              v


+------------------------------------------------+
|                Amplifier System                |
|                                                |
|  - Stereo Amplifier                            |
|  - Class D Amplifier                           |
|  - Multi Channel Amplifier                     |
|  - Home Theater Receiver                       |
+------------------------------------------------+
                              |
                              |
                              v


+------------------------------------------------+
|              Speaker System 8.1                |
|                                                |
|  Front L/R                                     |
|  Center                                         |
|  Surround L/R                                  |
|  Rear L/R                                      |
|  Subwoofer                                     |
|                                                |
|  Object / Spatial Audio Ready                  |
+------------------------------------------------+



==================================================

                 CoreMusic Deployment Modes


==================================================


1) HOME MEDIA CENTER

Raspberry Pi
      |
      |
CoreMusic Engine
      |
      |
USB DAC / I2S DAC
      |
      |
Amplifier
      |
      |
Home Speaker


==================================================


2) CAR AUDIO SYSTEM


Raspberry Pi
      |
      |
CoreMusic Engine
      |
      |
Automotive DAC
      |
      |
Car Amplifier
      |
      |
Vehicle Speaker System


==================================================


3) SPECIAL MUSIC DEVICE


Raspberry Pi
      |
      |
CoreMusic OS
      |
      |
Dedicated DAC Board
      |
      |
Premium Amplifier
      |
      |
Hi-Fi Speaker


==================================================


4) NAS AUDIO SERVER


NAS Storage
      |
      |
Network
      |
      |
Raspberry Pi
      |
      |
CoreMusic Engine
      |
      |
DAC
      |
      |
Audio System


==================================================


5) DAC CONTROL SYSTEM


CoreMusic UI
      |
      |
Raspberry Pi
      |
      |
DAC Controller
      |
      |
DSP
      |
      |
Amplifier
      |
      |
Speaker


==================================================


Supported Hardware


+-------------------------------+
| Audio Hardware                |
+-------------------------------+
| USB DAC                       |
| I2S DAC                       |
| Professional Audio Interface  |
| External Sound Card           |
| Amplifier Systems             |
| Hi-Fi Receivers               |
| Multi Channel Systems         |
+-------------------------------+



Final Architecture:


              CoreMusic Ecosystem

                     |
                     |
                     v

              Raspberry Pi 5
                     |
                     |
              CoreMusic Engine
                     |
          +----------+----------+
          |                     |
          v                     v
       USB DAC              I2S DAC
          |                     |
          +----------+----------+
                     |
                     v
                   DAC
                     |
                     v
               Amplifier
                     |
                     v
              Speaker System 8.1

```