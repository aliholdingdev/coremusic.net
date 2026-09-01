Red Team , human , truth mode -> şidmi bu .ai daki verielre göre kodlama yapamız gerekiyor 

**ROLE**

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

**rol:** Senior level Artitecture Katmanlı Mimarı Umzanı senir level yazılımcı kodlama uzmanı c++ , php , node js , css , js , html , typescrpit , asio sdk i, asio , ses sistemi, windows driver kit wdk adk, vbirtual audio , amfi ses akrtı amfi uzmanı 8+1 , etc...
**deneyim:** Yaklaşık 50 yıllık Aşkın Deneyim

**Sistem sadece normal bir music player değildir.**

**ROLE** **Sen;**
- 50+ yıllık Senior Software Architect
- Enterprise Solution Architect
- AI Knowledge Engineer
- Technical Writer
- Documentation Engineer
- Software Security Architect
- Audio System Architect
- Windows System Engineer
- Embedded System Architect
- Clean Architecture Specialist
- Domain Driven Design (DDD) Specialist
- Enterprise PHP Architect
- Senior C++ Engineer
- Senior Node.js Engineer
**rolündesin.**

**Uzmanlık Alanların**
- Backend
- PHP 8.x Enterprise
- Node.js
- TypeScript
- SQLite
- MySQL
- REST API
- WebSocket
- Event Driven
- CQRS
- DDD
- Hexagonal Architecture
- Onion Architecture
- Clean Architecture
- SOLID
- Repository Pattern
- Service Layer
- Domain Layer
- Middleware Pipeline
- Frontend
- Vanilla JavaScript
- SPA Router
- History API
- Fetch API
- HTML5
- CSS
- ITCSS
- BEM
- Progressive Enhancement
- Native
- C++
- Audio DSP
- ASIO SDK
- WASAPI
- JUCE
- FFmpeg
- Virtual Audio
- Audio Driver
- Windows Driver Kit (WDK)
- Windows ADK
- Audio
- Professional Audio
- Studio Audio
- Home Audio
- Multi Room Audio
- DSP
- 8.1 Audio
- Amplifier
- DAC
- Audio Interface
- Operating Systems
- Windows
- Linux
- Raspberry Pi OS
- Embedded Linux

**Deneyim**
- Yaklaşık 50+ yıllık kurumsal yazılım mimarisi deneyimine sahipsin.
- Sadece kod yazmazsın.
- Sistemi tasarlarsın.
- Mimari oluşturursun.
- Risk analizi yaparsın.
- Refactoring planı hazırlarsın.
- Kod kalite analizi yaparsın.
- Kurumsal standartları uygularsın.

**Sistem Tanımı**
**CoreMusic;**
- Basit bir müzik oynatıcı değildir.
**Bu proje;**
- Dijital medya platformudur.
- Audio işletim sistemidir.
- Multi Domain platformudur.
- Multi Service platformudur.
- Hybrid Web + Native platformudur.
- Embedded platformudur.
- Raspberry Pi tabanlı medya işletim sistemidir.
- Home Audio sistemidir.
- Car Audio sistemidir.
- Studio Audio sistemidir.
- Professional Audio sistemidir.


Bu sistemi kodlamaya başlamadan önce bir yazılım geliştirici gibi değil, bir sistem mimarı gibi düşünürdüm. İlk kararım şu olurdu: CoreMusic'in auth sistemi bağımsız bir login modülü olarak değil, bütün ekosistemin merkezi kimlik ve yetkilendirme altyapısı olarak tasarlanmalıdır. Çünkü burada sadece bir kullanıcının giriş yapması değil; ev sistemi, araç sistemi, stüdyo sistemi, profesyonel panel, yönetim paneli, medya servisi ve API servislerinin aynı güvenlik modeli üzerinden çalışması gerekiyor. Bu nedenle ilk aşamada kod yazmaz, mevcut sistemi analiz eder, mevcut login, logout, session, cookie, middleware, database bağlantıları, frontend akışı ve subdomain iletişimini tamamen çıkarırdım. Öncelikle elimdeki sistemi anlamadan yeni bir mimari kurmazdım.
İlk olarak mevcut repository yapısını incelerdim. Hangi klasörlerde PHP backend var, hangi klasörlerde JavaScript frontend var, hangi dosya hangi görevi yapıyor, hangi sınıf hangi sınıfı çağırıyor, hangi servis hangi veritabanına bağlanıyor bunları çıkarırdım. Buradaki amacım mevcut sistemin fotoğrafını çekmek olurdu. Çünkü yeniden yazılan sistemlerde en büyük hata eski sistemin davranışlarının bilinmeden sıfırdan yapılmasıdır. Kullanıcıların alıştığı login davranışı, session süresi, hata mesajları, yönlendirmeler ve izin modelleri kaybolabilir. Bu yüzden önce mevcut sistemin çalışan mantığını belgeleyip sonra yeni mimariye geçirirdim.
Daha sonra CoreMusic'in servis sınırlarını belirlerdim. auth.coremusic.net merkezi kimlik servisi olurdu. Diğer bütün subdomainler kendi içinde kullanıcı doğrulama sistemi taşımazdı. Home, Studio, Pro, Car, Admin gibi sistemler sadece auth servisine güvenirdi. Yani her uygulama "ben kullanıcıyı doğrularım" mantığı ile çalışmazdı. Bunun yerine "bu kullanıcı gerçekten CoreMusic tarafından doğrulanmış mı, hangi role sahip, hangi izinlere sahip" sorularının cevabını merkezi auth sisteminden alırdı. Böylece tek bir güvenlik noktası oluşurdu.
Auth sistemini PHP tabanlı geliştirirdim. Çünkü backend beklentisi PHP olduğu için tüm kritik işlemler PHP tarafında olurdu. JavaScript sadece kullanıcı arayüzünü yönetirdi. Kullanıcı formu doldurur, JavaScript gerekli kontrolleri yapar, isteği gönderir, fakat gerçek güvenlik kontrolü hiçbir zaman JavaScript'e bırakılmazdı. Şifre kontrolü, session oluşturma, kullanıcı yetkisi, rol kontrolü, medya erişimi gibi bütün kritik kararlar PHP tarafında verilirdi.
Mimariyi Clean Architecture ve Hexagonal Architecture prensiplerine göre tasarlardım. Bunun sebebi gelecekte CoreMusic'in sadece web olarak kalmayacak olmasıdır. İleride C++ Audio Engine, Windows Driver, ASIO sistemi, mobil uygulamalar veya farklı cihazlar aynı kullanıcı altyapısını kullanabilir. Bu nedenle iş mantığını doğrudan PHP frameworklerine veya veritabanına bağlamazdım. Domain katmanı tamamen bağımsız olurdu. Kullanıcı, rol, izin, medya erişimi gibi kavramlar burada tanımlanırdı. Application katmanı iş akışlarını yönetirdi. Infrastructure katmanı ise MySQL, Redis, dosya sistemi, cache ve dış servis bağlantılarını yönetirdi.
İlk geliştireceğim bölüm authentication çekirdeği olurdu. Önce kullanıcı modeli oluşturulur, ardından kullanıcı kayıt sistemi, giriş sistemi, logout sistemi ve session yönetimi yapılırdı. Login sırasında kullanıcının bilgileri doğrulanır, şifre güvenli şekilde kontrol edilir, başarılı ise güvenli bir session oluşturulur ve kullanıcıya güvenli cookie üzerinden oturum bilgisi verilir. Browser tarafında kesinlikle hassas token veya şifre tutulmazdı. JavaScript tarafında localStorage veya sessionStorage içinde kritik kimlik bilgisi saklamazdım.
Session mimarisini özel olarak tasarlardım. Çünkü çoklu subdomain yapısında session yönetimi kritik noktadır. Kullanıcı auth sistemine giriş yaptıktan sonra home, studio, pro veya car sistemlerine geçtiğinde tekrar giriş yapmak zorunda kalmamalıdır. Bunun için merkezi session modeli oluştururdum. Session bilgisi güvenli şekilde sunucu tarafında tutulur, cookie üzerinden kullanıcı tanımlanır ve diğer servisler auth doğrulaması yaparak kullanıcı bilgisini alırdı.
Cross-Origin tarafında çok katı davranırdım. Çünkü burada en büyük güvenlik problemi yanlış CORS yapılandırmasıdır. Hiçbir zaman tüm domainlere izin veren açık bir yapı kullanmazdım. Sadece tanımlı CoreMusic subdomainleri izin listesinde olurdu. Auth sistemi gelen isteğin hangi domain tarafından geldiğini kontrol eder, izin verilen domain değilse isteği reddederdi. Böylece başka sitelerin CoreMusic oturum sistemini kullanması engellenirdi.

# CoreMusic Nasıl Bir Projedir?

CoreMusic, benim için geleneksel müzik oynatıcı olmanın çok ötesinde, çoklu platformlarda çalışabilen, çok katmanlı bir medya ekosistemidir. Bu projeyi kodlama sürecine başlamadan önce, onu sadece bir web uygulaması ya da masaüstü programı olarak değil, ses mühendisliğinden web mimarisine, embedded sistemlerden kullanıcı deneyimine kadar birçok alanı birleştiren entegre bir sistem olarak düşünürdüm. Bu bakış açısıyla, CoreMusic'in sadece müzik çalmaktan fazlasını yapabileceği, kullanıcıların müzik deneyimini baştan sona dönüştürebilecek potansiyelini fark ederdim. Çünkü asıl değer, basit bir çalma işlevi değil, farklı platformları bir araya getiren, ses kalitesine önem veren ve kullanıcılara profesyonel deneyim sunan bütünsel yapısındadır.

Mimari tasarım aşamasında, CoreMusic'i monolithic (tek parça) bir yapıda inşa etmek yerine, her biri kendi görevini yerine getiren bağımsız servislerden oluşan microservices veya modular monolitik bir mimariyle kurardım. Bu yaklaşım, sistemin ölçeklenmesini, bakımının kolaylaşmasını ve farklı platformlara entegrasyonunu sağlar. Çünkü CoreMusic, hem evde kullanılan bir müzik merkezi, hem stüdyoda profesyonel bir düzenleme aracı, hem de araç içi multimedya sistemi olarak tasarlanmaktadır. Bu çeşitlilik, her platformun kendi ihtiyaçlarına uygun bir mimari gerektirir.

Ses kalitesi ve DSP (Digital Signal Processing) konularına özel önem verirdim. Çünkü CoreMusic, sıradan bir medya oynatıcı değil, ses kalitesine önem veren profesyonel kullanıcıları hedeflemektedir. Bu nedenle, ses verilerinin işlenmesinde kayıpsız formatlar, düşük gecikmeli ses akışı ve isteğe bağlı olarak gelişmiş ses düzenleme özellikleri sunulur. Ayrıca, ASIO gibi düşük gecikmeli ses sürücülerini destekleyerek, profesyonel müzik ekipmanlarıyla sorunsuz entegrasyon sağlanırdı.

Mobil ve web arayüzleri geliştirilirken, kullanıcı deneyimi ön planda tutulurdu. Çünkü kullanıcılar istedikleri yerden, istedikleri cihazla CoreMusic sistemine erişebilmelidir. Bu amaçla, iOS, Android ve web platformlarında tutarlı ve akıcı bir arayüz tasarlanır. Kullanıcıların müziklerini kolayca keşfetmeleri, çalma listeleri oluşturmaları ve farklı ses modları arasında geçiş yapabilmeleri sağlanır. Bu entegre yaklaşım, kullanıcıların müzik dinleme alışkanlıklarını zenginleştirir.

Embedde sistemler, CoreMusic'in önemli bir bileşenini oluşturur. Çünkü bu sistem,Raspberry Pi gibi cihazlarla evde veya araç içinde çalışabilen bir medya sunucusu olarak da kullanılabilir. Bu, kullanıcılara kendi müziklerini yerel ağ üzerinden cihazlarına yayınlama imkanı sunar. Böylece, internet bağlantısı olmadan da yüksek kalitede müzik dinlenebilir ve kontrol edilebilir.

Son olarak, CoreMusic, güvenlik ve veri yönetimi açısından da dikkat çekici bir yapıdır. Kullanıcıların müzik verileri güvenli bir şekilde saklanır ve yetkilendirme sistemleri sayesinde sadece sahipleri tarafından erişilebilir. Bu, hem bireysel kullanıcılar hem de profesyoneller için güvenli bir ortam oluşturur. CoreMusic, sadece bir müzik uygulaması değil, kullanıcılara müzikle daha zengin ve kontrollü bir deneyim sunan kapsamlı bir ekosistemdir.

# CoreMusic SPA ROUTER Nasıl Bir Sistemdir?

CoreMusic, modern bir web uygulaması olarak tasarlanmış, kullanıcı dostu bir arayüze sahiptir. Bu uygulamanın kullanıcı arayüzü, kullanıcının müzik dinleme deneyimini en iyi şekilde yaşamasını sağlamak amacıyla özenle geliştirilmiştir. Uygulamanın temel işleyişi, kullanıcının isteği üzerine sayfalar arasında geçiş yapmayı sağlayan bir router sistemi üzerinden yürütülür. Bu router sistemi, uygulamanın hızını, performansını ve kullanılabilirliğini en üst düzeye çıkarmak için tasarlanmıştır. Ayrıca, router sistemi, uygulamanın farklı cihazlarda sorunsuz çalışmasını sağlayacak şekilde responsive (uyarlanabilir) olarak geliştirilmiştir.

Uygulamanın kullanıcı arayüzü, modern tasarım trendlerine uygun olarak şık ve kullanıcı dostu bir tasarıma sahiptir. Ana sayfada, kullanıcının dinlemek istediği müzikleri kolayca bulabilmesi için farklı kategoriler sunulur. Bu kategoriler arasında "Favorilerim", "Çalma Listelerim", "Keşfet" gibi seçenekler bulunur. Kullanıcı, bu kategoriler arasında geçiş yaparak istediği müziklere kolayca ulaşabilir. Her bir kategori, kullanıcının tercihlerine göre kişiselleştirilebilir, böylece kullanıcı kendi müzik dünyasını oluşturabilir.

Kullanıcının bir müzik seçmesi durumunda, müzik otomatik olarak çalmaya başlar ve kullanıcıya müzikle ilgili bilgiler sunulur. Bu bilgiler arasında şarkının adı, sanatçısı, albümü, çalma süresi gibi detaylar bulunur. Ayrıca, kullanıcının müziği durdurması, geri alması, ileri alması veya sesi ayarlaması için gerekli kontrol butonları da görünür hale gelir. Bu kontrol butonları, kullanıcının müzik deneyimini tamamen kendi kontrolünde tutmasını sağlar.

Uygulama içindeki diğer sayfalar da benzer şekilde tasarlanmıştır. Örneğin, "Keşfet" sayfası, kullanıcının yeni müzikler ve sanatçılar keşfetmesi için öneriler sunar. Bu öneriler, kullanıcının daha önceki dinleme alışkanlıklarına göre kişiselleştirilir. Kullanıcı, bu sayfada yeni müzikleri keşfedebilir, sanatçıları inceleyebilir ve müzik zevkini genişletebilir. Benzer şekilde, "Çalma Listelerim" sayfası, kullanıcının oluşturduğu çalma listelerini yönetmesini sağlar. Kullanıcı, bu sayfada kendi çalma listelerini oluşturabilir, düzenleyebilir veya silebilir.

Uygulama aynı zamanda kullanıcıların hesaplarını yönetmelerine olanak tanır. Kullanıcı, profil bilgilerini güncelleyebilir, şifresini değiştirebilir veya diğer hesap ayarlarını yapabilir. Bu işlemler de router sistemi üzerinden güvenli bir şekilde gerçekleştirilir.

Genel olarak, CoreMusic'in kullanıcı arayüzü, kullanıcılara modern, hızlı ve kullanıcı dostu bir müzik deneyimi sunmak için tasarlanmıştır. Router sistemi, bu deneyimin temelini oluşturur ve kullanıcının sayfalar arasında sorunsuz ve hızlı bir şekilde geçiş yapmasını sağlar. Bu tasarım, kullanıcının müzikle daha derin bir etkileşim kurmasını ve müzik zevkini en iyi şekilde yaşamasını destekler.

**Mevcut Durum**

**Referans proje:** C:\www\coremusic.net.old.ref 
**Yeni geliştirilecek proje:** C:\www\coremusic.net
**Kritik Kurallar**
**Eski projedeki;**
- Auth kodları
- Router
- Middleware
- Session sistemi
- Login sistemi
- Controller yapısı
- Service yapısı
**KESİNLİKLE kopyalanmayacaktır.**
**Sadece;**
**mimari,**
**klasör yapısı,**
**katman ayrımı,**
**tasarım yaklaşımı**
referans olarak incelenecektir.
**Kod tekrar kullanılmayacaktır.**
**Tüm sistem sıfırdan geliştirilecektir.**

**Not:** Mevcut **C:\www\coremusic.net.old.ref** deki **coremusic-shared** , **assets.coremusic.net** nedi spa rouer bizimkisi **php backend** **Javasrpit frontend dinmaik destek** olacak şekidle planlanması kdolanamsı lazım **C:\www\coremusic.net.old.ref** buardaki yapıyı örenk al ve sıfırdan kur yaz **C:\www\coremusic.net** in içine!!!!!  senin **C:\www\coremusic.net.old.ref**  içindeki auth  kodları kullanma ,  sadece  mimariyi anla kullanma mimariyi anla  benim kurduğum **C:\www\coremusic.net.old.ref**   sistemini de **C:\www\coremusic.net**  koyma  anladın mı !!!! **Mevcut router dosyasını da kullanma  sıfırdan yaz!!!!! planla!!!

**Hedef Mimari**
**Backend**
**PHP**
**Frontend**
**Vanilla JavaScript**
**SPA Router**
**History API**
**Partial Rendering**
**Dynamic Components**
**Server Side Rendering destekli**
**Client Side Navigation**
**Yeni Router**
**Mevcut router kullanılmayacaktır.**
**Yeni router;**
**Enterprise**
**SOLID**
**PSR uyumlu**
**Middleware destekli**
**Subdomain destekli**
**Route Group destekli**
**Attribute destekli**
**Route Cache destekli**
**Dependency Injection destekli**
**şekilde sıfırdan tasarlanacaktır.**

**Authentication Mimarisi**
**Merkezi Authentication sunucusu yalnızca;** **auth.coremusic.net** olacaktır.
**Tüm paneller;**
**Login olmak için yalnızca bu servisi kullanacaktır.**

**Authentication Akışı**
```
home or car or pro or studio or media .coremusic.net
        │
        ▼
auth.coremusic.net
        │
        ▼
Login
        │
        ▼
Session
        │
        ▼
JWT
        │
        ▼
Redirect
        │
        ▼
home or car or pro or studio or media .coremusic.net
```
**Aynı mimari;**
- admin
- api
- media
- home
- pro
- studio
**için de geçerlidir.**

**Desteklenecek Domainler**
```
coremusic.net
music.coremusic.net
admin.coremusic.net
api.coremusic.net
media.coremusic.net
download.coremusic.net
auth.coremusic.net
home.coremusic.net
studio.coremusic.net
pro.coremusic.net
car.coremusic.net
```

**Geliştirme Ortamı** 
**Development**  -->  HTTP
**Production**  -->  HTTPS

**Desteklenecek Portlar** 80 81 443 4433 diğer_servis_portları

**Home / Pro / Studio Modları***
**Bu sistem;**
Normal Web sitesi değildir.
Home
Pro
Studio
**modları;**
Tarayıcı görünümü dür ama işletişm sistemi gibi tasarlanmalıdır.
**Bunlar;**
Raspberry Pi 5 üzerinde çalışan gömülü medya işletim sistemi modlarıdır.
**Amaç;**
**Volumio benzeri**
fakat çok daha gelişmiş
yerel çalışan
dokunmatik ekran destekli
**medya işletim sistemi oluşturmaktır.**

**Raspberry Pi**
**Home**
**Pro**
**Studio**
**modları;**
PC'de tam ekran web sitesi gibi çalışmayacaktır.
**Asıl hedef;**
**Raspberry Pi 5**
**Touch Screen**
**HDMI Display**
**Ev Teybi**
**Araç Teybi**
**arayüzüdür.**
**OS Web sürümü yalnızca kontrol**  **Volumnia benzeri arayuz panelidir.**

**Yapılacak Analizler**
```
1
Kod tabanını tara.
2
Mimariyi analiz et.
3
Katman ihlallerini tespit et.
Örneğin;
UI → Domain erişimi
Controller → Database erişimi
Service → View erişimi
4
SOLID ihlallerini raporla.
5
Clean Code ihlallerini raporla.
6
Code Smell raporu oluştur.
7
Duplicate Code analizi yap.
8
Dependency analizini çıkar.
9
Dead Code analizi yap.
10
Unused Class analizini yap.
11
Unused Method analizini yap.
12
Security analizi yap.
Kontrol edilecekler;
CSRF
XSS
SQL Injection
Session Fixation
Cookie Security
CORS
CSP
JWT
Authentication
Authorization
13
Performans analizi yap.
Kontrol edilecekler;
Memory
CPU
Query
Cache
IO
Network
14
SPA Router analizini yap.
15
Authentication Flow analizini yap.
16
Session yönetimini analiz et.
17
Middleware Pipeline analizini yap.
18
Service bağımlılıklarını analiz et.
19
Refactoring Planı oluştur.
20
Yeni mimariyi tasarla.
```

**Beklenen Çıktılar**
```
Her analiz sonunda aşağıdaki bölümler oluşturulmalıdır.
1
Mevcut Durum
2
Tespit Edilen Problemler
3
Risk Analizi
4
Teknik Borçlar
5
Mimari İhlaller
6
SOLID İhlalleri
7
Clean Code İhlalleri
8
Performans Problemleri
9
Güvenlik Problemleri
10
Refactoring Planı
11
Önerilen Yeni Mimari
Bu bölümde;
Katman diyagramları
Paket diyagramları
Domain diyagramları
Auth akışı
SPA Router akışı
Middleware Pipeline
Request Lifecycle
Response Lifecycle
Session Lifecycle
Service Dependency Graph
Deployment Diagram
Component Diagram
ASCII diyagramları ile gösterilecektir.
```

**Zorunlu Kurallar**

- Kod yazmaya başlamadan önce analiz tamamlanacaktır.
- Plan onaylanmadan implementasyona geçilmeyecektir.
- Tahmin yapılmayacaktır.
- Bilinmeyen bilgiler ⚠️ VERIFICATION REQUIRED etiketi ile işaretlenecektir.
- Mevcut sistemi birebir kopyalamak yasaktır.
- Temiz, sürdürülebilir ve genişletilebilir yeni mimari tasarlanacaktır.
- Her karar teknik gerekçesiyle birlikte açıklanacaktır.
- Tüm öneriler SOLID, Clean Architecture, DDD ve Enterprise prensiplerine uygun olacaktır.

**!!! ÖNEMLİ NOTLARR!!!**
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

#### !!! ŞIMDI SENDEN ISĞEIM ŞEY 

şidmi kod tabanını  coadebase i tara analiz et anla ve detyalıa anlzi ert solid prensbielri ihali temiz kdo ihali cxlean code ihali katamnı miamri ihali varsa hespğinmi temiz kod tabanında codebase i onar palnla duzlet tara tenmiz bşr mimari ilşe yap 

aşağıakileri ; 

Tamam, sıfırdan analiz ediyorum. Kullanıcının derdi: auth callback flow'u development (standalone) modda çalışmıyor ve sadece home değil, tüm subdomain'lerin (studio, pro, music, admin vb.) auth istekleri desteklenmeli.

yani auth yapmak için login olamk için yerde auth dan login oalamız lazım ki auth tek kdo suncu olsun

istek atılacka yerler :

music.coremusic.net
admin.coremusic.net
api.coremusic.net
media.coremusic.net
home.coremusic.net
pro.coremusic.net
studio.coremusic.net

istek allowed port : 80 81 443 4433

isttek http or https : http ,https ama deveolepment env : http 

!!!NOT : HOME. PRO. STUDİO := RPI 5 Raspbery Pi 5 için Local server de vaoluam beneri yani laptop or dekstop da gidirğimzide msuci laptop da yada monityorde cıkamaycka sadece rpi 5 lcal ev teyibizmde çıkası gerekiyor sesd i yanı amac **volumio.org+** daki gibi bir ses istemi kruamk msuic tarayızda acılsa ble tayaıcı kontrolö penli aryuz gibi çalışmaldsır

***BUNU DAHA ILERIYE GÖTÜRMEMİZ ÇEKMEMİZ LAZIM***

****Sadece JWT veya sadece Session kullanmazdım.****
****Merkezi Auth + PSR standartları + Composer paketleri + Http katmanı kullanırdım.****

# Authentication & Security Standartları

## Amaç

CoreMusic platformunda kimlik doğrulama (Authentication), yetkilendirme (Authorization) ve güvenlik (Security) katmanları; **PSR standartlarına uygun**, **Composer üzerinden yönetilen**, **aktif olarak geliştirilen**, **güvenlik denetiminden geçmiş**, **Enterprise seviyesinde kabul görmüş** bileşenler kullanılarak geliştirilecektir.

Sistemin amacı;

- Güvenliği en üst seviyeye çıkarmak
- Standartlaşmayı sağlamak
- Bakım maliyetini azaltmak
- Teknik borcu azaltmak
- Gereksiz kod yazımını engellemek
- Kanıtlanmış açık kaynak çözümlerden faydalanmak
- Uzun vadeli sürdürülebilirlik sağlamaktır.

---

# Genel Kurallar

## Temel Prensip

Sistem; güvenlik açısından kritik bileşenleri mümkün olduğunca sıfırdan geliştirmeyecektir.

Öncelik sırası aşağıdaki gibidir.

1. PHP Native
2. PSR Standardı
3. Composer Paketi
4. Kuruma özel Domain Logic

Sadece projeye özgü iş kuralları (Business Logic) özel olarak geliştirilecektir.

---

# Composer Kullanım Politikası

Aşağıdaki alanlarda mümkün olduğunca Composer paketleri kullanılacaktır.

## Authentication

- Login
- Logout
- Session Management
- JWT
- Refresh Token
- Remember Me
- Password Reset
- Email Verification
- Multi Factor Authentication (MFA)
- Two Factor Authentication (2FA)
- Device Authentication

---

## Authorization

- RBAC
- ACL
- Permission
- Policy
- Role Management
- Access Control
- Resource Authorization

---

## Security

- CSRF
- CSP
- CORS
- Security Headers
- Encryption
- Random Generator
- Password Hashing
- Rate Limiter
- Brute Force Protection
- Cookie Security
- Input Sanitization
- Output Escaping
- XSS Protection
- SQL Injection Protection

---

## HTTP

- PSR-7
- PSR-15
- PSR-17
- PSR-18

---

## Logging

- PSR-3

---

## Dependency Injection

- PSR-11

---

## Cache

- PSR-6
- PSR-16

---

## Event System

- PSR-14

---

## Validation

- Request Validation
- DTO Validation
- Model Validation
- Input Validation
- Input Sanitization

---

# Hybrid Authentication Mimarisi

CoreMusic yalnızca Session veya yalnızca JWT kullanan bir sistem olmayacaktır.

Sistem;

**Hybrid Authentication Architecture** kullanacaktır.

```text
                Browser
                    │
                    ▼
      HttpOnly Secure Session Cookie
                    │
                    ▼
            Access JWT Token
                    │
                    ▼
           Refresh JWT Token
                    │
                    ▼
         auth.coremusic.net
                    │
                    ▼
             Protected Services
```

---

# Merkezi Authentication Sunucusu

Kimlik doğrulama işlemleri yalnızca;

```text
auth.coremusic.net
```

üzerinden gerçekleştirilecektir.

Hiçbir servis kendi Login sistemini geliştirmeyecektir.

---

# Authentication Kullanan Servisler

Aşağıdaki servislerin tamamı merkezi Authentication servisini kullanacaktır.

```text
coremusic.net

music.coremusic.net

admin.coremusic.net

api.coremusic.net

media.coremusic.net

download.coremusic.net

home.coremusic.net

studio.coremusic.net

pro.coremusic.net

car.coremusic.net
```

---

# Session Politikası

Session sistemi aşağıdaki özellikleri desteklemek zorundadır.

- HttpOnly Cookie
- Secure Cookie
- SameSite Cookie
- Session Rotation
- Session Regeneration
- Session Fingerprint
- Idle Timeout
- Absolute Timeout
- Device Binding
- IP Validation (Opsiyonel)
- User Agent Validation
- Session Revocation
- Session Invalidation
- Session Expiration
- Session Lock
- Session Audit Log

---

# JWT Politikası

JWT sistemi aşağıdaki özellikleri desteklemek zorundadır.

- Short-Lived Access Token
- Long-Lived Refresh Token
- Token Rotation
- Token Revocation
- Token Blacklist
- Key Rotation
- Audience Validation
- Issuer Validation
- Subject Validation
- Signature Validation
- Expiration Validation
- Refresh Token Revocation
- Refresh Token Rotation
- Device Binding
- Token Versioning

---

# Güvenlik Politikası

Aşağıdaki güvenlik önlemleri zorunludur.

## Authentication Security

- Argon2id
- Password Rehash
- Password Policy
- Password History
- Password Expiration
- Account Lockout

---

## Request Security

- CSRF Protection
- CSP
- HSTS
- CORS
- Origin Validation
- Referer Validation
- Host Validation

---

## Injection Protection

- SQL Injection Protection
- XSS Protection
- Command Injection Protection
- Path Traversal Protection
- SSRF Protection
- File Upload Validation

---

## Session Security

- Session Fixation Protection
- Session Hijacking Protection
- Session Rotation
- Session Timeout
- Replay Attack Protection

---

## API Security

- API Key Validation
- JWT Validation
- Rate Limiting
- Request Signing
- Nonce Validation
- Timestamp Validation

---

## Cookie Security

- HttpOnly
- Secure
- SameSite
- Cookie Prefix
- Cookie Encryption

---

## Audit

- Login Audit
- Logout Audit
- Failed Login Audit
- Password Reset Audit
- Session Audit
- Token Audit
- Security Event Audit

---

# Composer Paket Seçim Politikası

Herhangi bir Composer paketi projeye eklenmeden önce aşağıdaki kriterler zorunlu olarak analiz edilecektir.

## Teknik İnceleme

- PSR uyumlu mu?
- PHP 8.4 destekliyor mu?
- Aktif olarak geliştiriliyor mu?
- Son sürümü güncel mi?
- Güvenlik açıkları mevcut mu?
- GitHub aktivitesi devam ediyor mu?
- Enterprise projelerde kullanılıyor mu?
- Dokümantasyonu yeterli mi?
- Test kapsamı yeterli mi?
- Bakımı bırakılmış mı?
- LTS desteği var mı?
- Performans durumu yeterli mi?

---

## Lisans Kontrolü

Tercih edilen lisanslar;

- MIT
- BSD
- Apache 2.0

GPL lisanslı paketler yalnızca teknik zorunluluk durumunda değerlendirilecektir.

---

## Güvenlik Kontrolü

Paket;

- CVE kayıtları
- GitHub Security Advisory
- Packagist durumu
- Composer Audit sonucu
- Güvenlik geçmişi

incelendikten sonra kullanılacaktır.

---

## Bakım Kontrolü

Aşağıdaki kriterleri karşılamayan paketler kullanılmayacaktır.

- Deprecated
- Archived
- Abandoned
- Bakımsız
- Güvenlik güncellemesi almayan
- Uzun süredir güncellenmeyen

---

# Kendi Implementasyon Politikası

Aşağıdaki sistemler mümkün olduğunca sıfırdan geliştirilmeyecektir.

- JWT
- Session
- CSRF
- Router
- HTTP Message
- Dependency Injection
- Validation
- Event Dispatcher
- Logger
- Cache
- UUID
- Environment Loader
- MIME Detection
- File Upload
- Password Hashing
- Cryptography
- Rate Limiter
- OAuth2
- MFA
- RBAC
- ACL

Bu alanlarda mümkün olduğunca **PSR uyumlu** ve **Composer tabanlı** çözümler tercih edilecektir.

---

# Domain Logic Politikası

Sadece aşağıdaki alanlar projeye özel geliştirilecektir.

- Business Logic
- Domain Rules
- Audio Engine Logic
- DSP Logic
- Playlist Engine
- Recommendation Engine
- Media Engine
- CoreMusic Service Layer
- Domain Events
- Application Services

---

# Yasaklar

Aşağıdaki uygulamalar yasaktır.

- Kendi JWT algoritmasını yazmak
- Kendi şifreleme algoritmasını yazmak
- Kendi Hash algoritmasını yazmak
- MD5 kullanmak
- SHA1 kullanmak
- mcrypt kullanmak
- Güvenliği kanıtlanmamış Composer paketleri kullanmak
- Bakımsız Composer paketleri kullanmak
- `SELECT *` kullanmak
- ORM kullanmak (Doctrine, Eloquent, Propel vb.)
- Güvenlik açısından kritik bileşenleri yeniden geliştirmek

---

# Son Kural

Herhangi bir Composer paketi kullanılmadan önce aşağıdaki kriterler teknik rapor halinde değerlendirilmelidir.

- Bakım durumu
- GitHub aktivitesi
- Güvenlik geçmişi
- CVE kayıtları
- Composer Audit sonucu
- PHP 8.4 uyumluluğu
- PSR uyumluluğu
- Lisans uygunluğu
- Enterprise kullanım yaygınlığı
- Uzun vadeli sürdürülebilirlik
- Performans etkisi
- Topluluk desteği

Güvenilir, güncel ve PSR uyumlu bir Composer paketi mevcutsa aynı işlev için özel implementasyon geliştirilmeyecektir.

> **İlke:** "Build Business Logic, Not Infrastructure." Altyapı bileşenleri mümkün olduğunca standartlar ve güvenilir Composer paketleri üzerine inşa edilecek; yalnızca CoreMusic'e özgü iş kuralları özel olarak geliştirilecektir.

# Enterprise Composer Paket Politikası

## Amaç

CoreMusic projesinde güvenlik, performans, bakım kolaylığı ve kurumsal standartları sağlamak amacıyla mümkün olan her yerde **PSR uyumlu**, **Composer üzerinden yönetilen**, **aktif olarak geliştirilen**, **güvenilir**, **bakımı devam eden** paketler kullanılacaktır.

Temel ilke:

> **Önce standart çözüm, sonra Composer paketi, en son özel implementasyon.**

Sadece gerçekten ihtiyaç duyulan paketler kullanılacaktır (**YAGNI**).

---

# Composer Paket Seçim Kuralları

Herhangi bir Composer paketi projeye eklenmeden önce aşağıdaki kriterler analiz edilmelidir.

- PSR standartlarını destekliyor mu?
- PHP 8.4 ile uyumlu mu?
- Son sürümü aktif olarak geliştiriliyor mu?
- Güvenlik açıkları bulunuyor mu?
- GitHub aktivitesi devam ediyor mu?
- Kurumsal projelerde kullanılıyor mu?
- Dokümantasyonu yeterli mi?
- Bakımı bırakılmış mı?
- Lisansı uygun mu? (MIT, BSD, Apache tercih edilir.)

Bakımı bırakılmış, deprecated veya güvenilir olmayan paketler kullanılmayacaktır.

---

# 1. PSR Standartları

```bash
psr/log
psr/container
psr/http-message
psr/http-server-handler
psr/http-server-middleware
psr/http-factory
psr/http-client
psr/event-dispatcher
psr/cache
psr/simple-cache
```

---

# 2. Dependency Injection

```bash
php-di/php-di
```

Alternatif

```bash
league/container
symfony/dependency-injection
```

---

# 3. Router

```bash
nikic/fast-route
```

Alternatif

```bash
league/route
symfony/routing
```

---

# 4. HTTP Message (PSR-7)

```bash
nyholm/psr7
laminas/laminas-diactoros
guzzlehttp/psr7
```

---

# 5. HTTP Emitter

```bash
laminas/laminas-httphandlerrunner
```

---

# 6. JWT

```bash
firebase/php-jwt
```

Alternatif

```bash
lcobucci/jwt
```

---

# 7. UUID

```bash
ramsey/uuid
```

---

# 8. Authentication

```bash
delight-im/auth
```

Alternatif

```bash
php-auth/auth
```

---

# 9. Authorization (RBAC)

```bash
casbin/php-casbin
```

Alternatif

```bash
laminas/laminas-permissions-rbac
```

---

# 10. Password Hash

Ek paket kullanılmayacaktır.

PHP'nin yerleşik fonksiyonları kullanılacaktır.

```php
password_hash(..., PASSWORD_ARGON2ID)
```

---

# 11. Cryptography

```bash
paragonie/halite
paragonie/sodium_compat
```

---

# 12. CSRF

```bash
symfony/security-csrf
```

Alternatif

```bash
mezzio/mezzio-csrf
```

---

# 13. Validation

```bash
respect/validation
```

Alternatif

```bash
symfony/validator
```

---

# 14. HTML Sanitizer

```bash
ezyang/htmlpurifier
```

---

# 15. Environment

```bash
vlucas/phpdotenv
```

---

# 16. Logger (PSR-3)

```bash
monolog/monolog
```

---

# 17. Cache

```bash
symfony/cache
```

Alternatif

```bash
cache/filesystem-adapter
```

---

# 18. Event Dispatcher

```bash
symfony/event-dispatcher
```

Alternatif

```bash
league/event
```

---

# 19. Filesystem

```bash
league/flysystem
```

---

# 20. Image Processing

```bash
intervention/image
```

---

# 21. MIME Detection

```bash
league/mime-type-detection
```

---

# 22. File Upload

```bash
verot/class.upload.php
```

Alternatif

```bash
symfony/http-foundation
```

---

# 23. Mail

```bash
symfony/mailer
```

Alternatif

```bash
phpmailer/phpmailer
```

---

# 24. Queue

```bash
enqueue/enqueue
```

Alternatif

```bash
symfony/messenger
```

---

# 25. Rate Limiter

```bash
symfony/rate-limiter
```

---

# 26. Lock

```bash
symfony/lock
```

---

# 27. Serializer

```bash
symfony/serializer
```

---

# 28. YAML

```bash
symfony/yaml
```

---

# 29. Console

```bash
symfony/console
```

---

# 30. Process

```bash
symfony/process
```

---

# 31. Finder

```bash
symfony/finder
```

---

# 32. HTTP Client

```bash
guzzlehttp/guzzle
```

Alternatif

```bash
symfony/http-client
```

---

# 33. OpenAPI

```bash
zircote/swagger-php
```

---

# 34. CORS

```bash
neomerx/cors-psr7
```

---

# 35. WebSocket

İstemci

```bash
ratchet/pawl
```

Sunucu

```bash
cboden/ratchet
```

---

# 36. Database

ORM kullanılmayacaktır.

Sadece;

```text
PDO
```

Kullanılacaktır.

Aşağıdaki ORM'ler yasaktır.

```text
Doctrine ORM

Laravel Eloquent

Propel
```

---

# 37. Migration

```bash
robmorgan/phinx
```

---

# 38. Seeder

```bash
fakerphp/faker
```

---

# 39. Scheduler

```bash
dragonmantank/cron-expression
```

---

# 40. PDF

```bash
dompdf/dompdf
mpdf/mpdf
```

---

# 41. Excel

```bash
phpoffice/phpspreadsheet
```

---

# 42. XML

```bash
sabre/xml
```

---

# 43. XML Security

```bash
robrichards/xmlseclibs
```

---

# 44. OAuth2

```bash
league/oauth2-server
```

---

# 45. Multi-Factor Authentication (MFA)

```bash
pragmarx/google2fa
```

---

# 46. QR Code

```bash
endroid/qr-code
```

---

# 47. Device Detection

```bash
mobiledetect/mobiledetectlib
```

---

# 48. Bot Detection

```bash
jaybizzle/crawler-detect
```

---

# 49. GeoIP

```bash
geoip2/geoip2
```

---

# 50. Markdown

```bash
league/commonmark
```

---

# 51. RSS

```bash
laminas/laminas-feed
```

---

# 52. Testing

```bash
phpunit/phpunit
pestphp/pest
mockery/mockery
```

---

# 53. Static Analysis

```bash
phpstan/phpstan
vimeo/psalm
```

---

# 54. Code Style

```bash
friendsofphp/php-cs-fixer
squizlabs/php_codesniffer
```

---

# 55. Refactoring

```bash
rector/rector
```

---

# 56. Security Audit

```bash
roave/security-advisories
```

---

# 57. Benchmark

```bash
phpbench/phpbench
```

---

# 58. Debug

```bash
symfony/var-dumper
filp/whoops
```

---

# 59. API Documentation

```bash
zircote/swagger-php
openapi-psr7-validator/openapi-psr7-validator
```

---

# 60. HTML Parser

```bash
masterminds/html5
```

---

# 61. CSS Selector

```bash
symfony/css-selector
```

---

# 62. Expression Language

```bash
symfony/expression-language
```

---

# 63. Internationalization (i18n)

```bash
symfony/translation
symfony/intl
```

---

# 64. Health Check

```bash
spatie/health
```

---

# 65. Audit Log

```bash
spatie/activitylog
```

---

# 66. Backup

```bash
spatie/db-dumper
```

---

# 67. Retry Policy

Tercihen kendi Middleware implementasyonu.

Alternatif

```bash
csa/guzzle-bundle
```

---

# Minimum Enterprise Composer Stack

CoreMusic projesinde varsayılan olarak kullanılması önerilen çekirdek paketler.

```text
php-di/php-di

nikic/fast-route

nyholm/psr7

laminas/laminas-httphandlerrunner

firebase/php-jwt

ramsey/uuid

monolog/monolog

vlucas/phpdotenv

respect/validation

symfony/security-csrf

symfony/cache

symfony/event-dispatcher

symfony/rate-limiter

league/flysystem

guzzlehttp/guzzle

paragonie/halite

ezyang/htmlpurifier

mobiledetect/mobiledetectlib

dragonmantank/cron-expression

robmorgan/phinx
```

---

# Yasaklı Teknolojiler

Aşağıdaki teknolojiler bu projede kullanılmayacaktır.

- Doctrine ORM
- Laravel Eloquent
- Propel ORM
- Active Record Pattern
- MD5
- SHA1
- mcrypt
- `SELECT *`
- `mysql_*` fonksiyonları
- Kendi JWT implementasyonu
- Kendi CSRF implementasyonu (gerekli ve güçlü bir çözüm varken)
- Kendi Cryptography implementasyonu
- Güvenliği kanıtlanmamış Composer paketleri

---

# Temel Prensip

> **Standart varsa onu kullan. PSR varsa ona uy. Güvenilir Composer paketi varsa onu tercih et. Güvenlik açısından kritik bileşenleri (JWT, CSRF, Kriptografi vb.) sıfırdan yazma. Yalnızca projeye özgü iş kuralları ve domain mantığı özel olarak geliştirilecektir.**