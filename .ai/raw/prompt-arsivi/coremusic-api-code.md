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

Bunu ben olsam şu şekilde kurardım.
Önce hiçbir satır kod yazmazdım. Çünkü böyle bir projede en pahalı hata yanlış kod değildir; yanlış mimaridir. Kod her zaman yeniden yazılabilir, fakat yanlış mimari üzerine kurulmuş yüz binlerce satır kodu değiştirmek aylar sürebilir. Bu nedenle ilk hedefim çalışan bir uygulama değil, doğru bir sistem tasarlamak olurdu. Projenin ilk haftaları tamamen analiz, domain modelleme, servis sınırlarını belirleme, API sözleşmelerini hazırlama, güvenlik kurallarını tanımlama ve ADR (Architecture Decision Record) oluşturma ile geçerdi. Kodlama ise ancak bu kararlar onaylandıktan sonra başlardı.
CoreMusic'i hiçbir zaman "PHP ile yazılmış bir müzik oynatıcı" olarak düşünmezdim. Benim gözümde CoreMusic; onlarca servis, farklı istemciler, farklı işletim sistemleri ve gelecekte donanıma kadar genişleyecek büyük bir dijital medya platformudur. Bu nedenle bütün sistemi tek uygulama gibi değil, birbirleriyle konuşan bağımsız servislerden oluşan bir ekosistem olarak tasarlardım. Her servis yalnızca kendi işinden sorumlu olurdu. Auth servisi yalnızca kimlik doğrulama yapar, Media servisi yalnızca medya yönetir, Download servisi yalnızca indirme yapar, Audio servisi yalnızca ses motorunu yönetir. Bir servisin başka bir servisin veritabanına erişmesine veya iç sınıflarını kullanmasına izin vermezdim.
Daha ilk günden "Code First" yaklaşımını tamamen yasaklardım. Bunun yerine "Architecture First", "Contract First" ve "Documentation First" yaklaşımını kullanırdım. Önce sistem nasıl çalışacak, hangi servis hangi veriyi üretecek, hangi endpoint hangi DTO'yu döndürecek, hata kodları nasıl olacak, versiyonlama nasıl yapılacak, güvenlik nasıl işleyecek, bunların tamamını yazarım. Kod ise yalnızca bu sözleşmeyi uygular. Böylece geliştiriciler mimariyi tahmin etmek zorunda kalmaz.
İlk oluşturacağım şeylerden biri API Gateway olurdu. Çünkü bütün istemcilerin tek giriş noktası olmasını isterdim. SPA, mobil uygulama, masaüstü uygulaması, Raspberry Pi, araç içi sistem, üçüncü parti geliştiriciler veya gelecekte çıkacak yeni istemciler hiçbir zaman doğrudan servislerle konuşmazdı. Her istek önce API Gateway'e gelir, burada doğrulanır, loglanır, yetkilendirilir, rate limit kontrolünden geçer ve daha sonra ilgili servise yönlendirilirdi. Böylece güvenlik kuralları bütün sistemde tek merkezden yönetilmiş olurdu.
SPA Router'ın hiçbir zaman veritabanını bilmesine izin vermezdim. SPA yalnızca ApiClient sınıfını tanırdı. ApiClient yalnızca API Gateway'i tanırdı. Gateway yalnızca servisleri tanırdı. Servis ise yalnızca kendi Domain katmanını bilirdi. Domain ise Repository Interface'i bilirdi. Repository Interface'in arkasında hangi veritabanının çalıştığını Domain hiçbir zaman bilmezdi. MySQL mi kullanılıyor, SQLite mı kullanılıyor, Redis mi devrede, bunların hiçbiri üst katmanları ilgilendirmezdi. Böylece yarın veritabanını değiştirmek istersem uygulamanın geri kalanına dokunmadan yalnızca Infrastructure katmanını değiştirirdim.
Kod yazmaya başladığımda ilk oluşturacağım proje API olmazdı. İlk oluşturacağım proje coremusic-shared olurdu. Çünkü bütün servislerin ortak konuştuğu dil burada bulunmalı. DTO'lar, Contract'lar, Request sınıfları, Response sınıfları, Validation kuralları, Exception sınıfları, Event tanımları, Logger arayüzleri, ApiClient, HttpClient ve ortak güvenlik bileşenleri burada yer alırdı. Böylece hiçbir servis aynı DTO'yu veya aynı yardımcı sınıfı tekrar yazmazdı. Bir sözleşme değiştiğinde bütün servisler aynı paketten güncellenirdi.
Sonrasında API Gateway'i geliştirirdim. Gateway aslında iş mantığı içermezdi. Sadece yönlendirme, güvenlik, loglama, doğrulama ve gözlemlenebilirlik (observability) görevini üstlenirdi. Burada Authentication, Authorization, JWT doğrulama, API Key doğrulama, OAuth, Rate Limit, Audit Log, Correlation ID, Request Validation, Response Standardization gibi bütün çapraz kesen (cross-cutting concern) bileşenler bulunurdu.
Gateway tamamlandıktan sonra ilk servis olarak Auth Service'i geliştirirdim. Çünkü bütün sistemin güvenliği buna bağlıdır. Kullanıcı yönetimi, oturum yönetimi, token üretimi, refresh token mekanizması, RBAC, Permission sistemi ve API anahtarları burada bulunurdu. Auth tamamlanmadan diğer servisleri üretmezdim.
Daha sonra Media, Music, Playlist, Library ve Search servislerini geliştirirdim. Bunların hiçbiri birbirlerinin tablolarına erişmezdi. Eğer Media servisi bir kullanıcı bilgisine ihtiyaç duyarsa Auth veritabanına bağlanmazdı. Bunun yerine Auth API'yi çağırırdı. Böylece servis sınırları hiçbir zaman bozulmazdı.
Kodun tamamında CQRS kullanırdım. Yazma işlemleri ile okuma işlemleri birbirinden tamamen ayrılırdı. Command tarafı yalnızca veri değiştirirdi. Query tarafı ise yalnızca veri okurdu. Okuma tarafında Redis, Replica Database veya Cache kullanabilirdim. Yazma tarafı ise doğrudan Master Database üzerinde çalışırdı. Böylece yüksek trafikte sistem çok daha rahat ölçeklenebilirdi.
Servislerin birbirini doğrudan çağırmasını da istemezdim. Bunun yerine Event Driven Architecture kullanırdım. Örneğin kullanıcı yeni bir müzik eklediğinde Media servisi Download servisini doğrudan çağırmazdı. Bunun yerine "MusicAdded" adında bir Domain Event yayınlardı. Download servisi bu olayı dinler, kendi işini yapardı. AI servisi aynı olayı dinler, öneri sistemini güncellerdi. Analytics servisi aynı olayı dinler, istatistik oluştururdu. Böylece servisler birbirine bağımlı olmadan birlikte çalışabilirdi.
Harici servisleri de doğrudan kullanmazdım. Spotify, Deezer, YouTube, Discogs veya MusicBrainz gibi sistemlerin her biri için Adapter katmanı oluştururdum. Uygulamanın geri kalanı yalnızca ortak bir MusicProviderInterface bilirdi. Yarın Deezer yerine başka bir servis eklenirse yalnızca yeni Adapter yazılır, sistemin geri kalanı değişmezdi.
Depolama tarafını da soyutlardım. Uygulama hiçbir zaman doğrudan Local Disk, NAS veya S3 kullanmazdı. Bunun yerine Storage Interface oluştururdum. Local Storage, NAS, SMB, NFS, S3, Azure Blob veya Cloudflare R2 bu arayüzü uygularlardı. Böylece depolama altyapısı değişse bile iş mantığı aynı kalırdı.
Önbellek (cache) için de aynı yaklaşımı kullanırdım. Kod Redis'e bağımlı olmazdı. Cache Interface oluştururdum. Redis, APCu, File Cache veya Memory Cache bu arayüzü uygularlardı. Böylece geliştirme ortamında Memory Cache kullanırken üretim ortamında Redis'e geçmek yalnızca konfigürasyon değişikliği olurdu.
Sistemin tamamını sürekli ölçerdim. Her isteğin Correlation ID'si olurdu. Her API çağrısı loglanırdı. Performans metrikleri toplanırdı. Trace bilgileri saklanırdı. Audit kayıtları tutulurdu. Böylece bir hata oluştuğunda yalnızca log'a bakmak yerine isteğin sistem içinde geçtiği bütün servisleri takip edebilirdim.
Kod yazma sürecinde ise hiçbir geliştiricinin doğrudan yeni dosya oluşturmasına izin vermezdim. Önce Architecture Review yapılırdı. Ardından ADR kontrolü yapılırdı. Daha sonra katman bağımlılıkları incelenirdi. SOLID kontrolü yapılırdı. DDD kurallarına bakılırdı. Güvenlik analizi gerçekleştirilirdi. Performans etkisi değerlendirilirdi. Test edilebilirliği incelenirdi. Dokümantasyonu hazırlanırdı. Risk analizi yapılırdı. Refactoring planı çıkarılırdı. Bütün bunlar tamamlandıktan sonra geliştirme onayı verilirdi. Kod yazımı bu onaydan sonra başlardı.
Son olarak yapay zekâ ajanlarını yalnızca kod üreten araçlar olarak kullanmazdım. Onları mimari denetçiler olarak konumlandırırdım. Her ajan belirli bir uzmanlık alanından sorumlu olurdu. Bir ajan SOLID ihlallerini incelerdi, bir diğeri güvenliği denetlerdi, başka biri performans analizini yapardı, başka biri DDD kurallarını kontrol ederdi. Kod ancak bütün bu denetimlerden geçtikten sonra kabul edilirdi. Böylece yapay zekâ geliştirme sürecinin merkezinde yer alır, ancak hiçbir zaman mimari kararların yerine geçmezdi. Kod üretmek en son adım olurdu; asıl amaç, yıllar boyunca büyüyebilecek, ölçeklenebilir, test edilebilir ve sürdürülebilir bir platform inşa etmek olurdu.


# CoreMusic Nasıl Bir Projedir?

CoreMusic, ilk bakışta bir müzik oynatıcı gibi görünse de, gerçekte bundan çok daha büyük ve kapsamlı bir yazılım ekosistemidir. Bu proje yalnızca ses dosyalarını oynatmayı amaçlayan klasik bir uygulama değildir; kendi servis mimarisine, güvenlik altyapısına, API katmanına, ses motoruna, yapay zekâ bileşenlerine ve gömülü sistem desteğine sahip kurumsal seviyede geliştirilen hibrit bir dijital medya platformudur.

CoreMusic'in temel amacı yalnızca müzik dinletmek değildir. Amaç; tek bir mimari üzerinde çalışan, web, masaüstü, mobil, Raspberry Pi, araç içi bilgi-eğlence sistemleri (Car Infotainment), ev medya merkezleri (Home Audio), profesyonel stüdyo sistemleri (Studio Audio) ve yüksek kaliteli ses sistemlerini ortak bir platform altında birleştirmektir. Bu nedenle proje en başından itibaren ölçeklenebilir, modüler ve servis tabanlı olarak tasarlanmaktadır.

Bu sistem monolitik bir uygulama değildir. Her bileşenin kendi sorumluluğu vardır. Kimlik doğrulama, medya yönetimi, ses işleme, yapay zekâ, cihaz yönetimi, indirme sistemi, bildirim sistemi ve analiz sistemi birbirinden bağımsız servisler olarak çalışır. Her servis yalnızca kendi görevinden sorumludur ve diğer servislerle yalnızca tanımlanmış API sözleşmeleri üzerinden iletişim kurar. Böylece sistem büyüdükçe yeni özellikler eklemek veya mevcut servisleri değiştirmek diğer bileşenleri etkilemez.

CoreMusic'in merkezinde API tabanlı bir mimari bulunmaktadır. Sistemin tüm istemcileri, ister web uygulaması, ister mobil uygulama, ister masaüstü istemcisi, ister gömülü cihaz olsun, aynı mimari prensiplere göre çalışan API Gateway üzerinden sisteme bağlanır. Böylece güvenlik, yetkilendirme, doğrulama, sürüm yönetimi, hata yönetimi ve kayıt mekanizmaları tek merkezden yönetilebilir. Bu yaklaşım sistemin hem bakımını kolaylaştırır hem de farklı platformların ortak kurallar altında çalışmasını sağlar.

Frontend tarafında doğrudan veritabanı erişimi veya iş mantığı bulunmaz. SPA Router yalnızca API Client üzerinden çalışır. Kullanıcı arayüzü hiçbir zaman Repository, PDO, SQL veya veritabanı nesnelerini bilmez. Bu yaklaşım sayesinde kullanıcı arayüzü ile iş mantığı tamamen birbirinden ayrılır. Böylece kullanıcı arayüzü değişse bile sistemin çekirdeği etkilenmez.

İş mantığı tamamen Domain katmanında yer alır. Burada müzik, albüm, sanatçı, oynatma listesi, kullanıcı, cihaz, ses motoru ve diğer tüm iş kuralları tanımlanır. Domain katmanı altyapıyı bilmez. Verilerin MySQL'de mi, SQLite'ta mı, Redis'te mi veya farklı bir depolama sisteminde mi tutulduğu Domain açısından önemsizdir. Domain yalnızca iş kurallarını bilir. Böylece sistem teknolojiden bağımsız hâle gelir.

Application katmanı ise sistemin kullanım senaryolarını yönetir. Kullanıcı giriş yapma, müzik arama, çalma listesi oluşturma, medya indirme, cihaz ekleme veya ses ayarlarını değiştirme gibi işlemler burada gerçekleştirilir. Bu katman Domain ile dış dünya arasında köprü görevi görür ve CQRS yaklaşımı kullanılarak okuma ve yazma işlemleri birbirinden ayrılır.

Infrastructure katmanı sistemin dış dünyayla iletişim kurduğu bölümdür. Veritabanları, Redis, dosya sistemi, FFmpeg, harici servisler, bulut depolama sistemleri, üçüncü taraf API'ler ve işletim sistemi bileşenleri bu katmanda bulunur. İş kuralları hiçbir zaman doğrudan bu bileşenlere bağımlı değildir. Bu bağımlılıkların tamamı arayüzler (Interfaces) üzerinden yönetilir. Böylece altyapı teknolojisi değiştiğinde üst katmanların değiştirilmesine gerek kalmaz.

CoreMusic yalnızca yazılım geliştirmeye odaklanan bir proje değildir. Aynı zamanda profesyonel ses sistemleri için tasarlanmış bir platformdur. Sistem, ASIO, WASAPI, FFmpeg, JUCE ve gelecekte geliştirilecek Neva Audio Engine gibi bileşenlerle düşük gecikmeli ses işleme yeteneklerine sahip olacak şekilde planlanmaktadır. Araç içi bilgi-eğlence sistemleri, ev ses sistemleri, profesyonel kayıt stüdyoları ve çok odalı ses sistemleri aynı mimariyi kullanarak çalışabilecektir.

Yapay zekâ da bu projenin temel bileşenlerinden biridir. AI yalnızca öneri sistemi olarak kullanılmayacaktır. Aynı zamanda medya sınıflandırma, otomatik etiketleme, öneri motoru, analiz, davranış tahmini ve sistem optimizasyonu gibi birçok farklı görev üstlenecektir. Ayrıca geliştirme sürecinde mimari doğrulama, güvenlik analizi, kod kalite denetimi ve refactoring önerileri için de yapay zekâ ajanlarından yararlanılacaktır.

Projede güvenlik en baştan tasarımın bir parçası olarak ele alınmaktadır. Güvenlik sonradan eklenen bir özellik değildir. Kimlik doğrulama, yetkilendirme, JWT yönetimi, API anahtarları, OAuth, CSRF, CSP, Rate Limit, Audit Log, Request Validation ve güvenlik politikaları sistemin temel bileşenleri arasında yer alır. Her API çağrısı doğrulanır, kayıt altına alınır ve yetki kontrolünden geçirilir.

CoreMusic'in en önemli özelliklerinden biri de servisler arasında sıkı bağımlılık oluşturmamasıdır. Servisler birbirlerini doğrudan çağırmak yerine API sözleşmeleri, olaylar (Events) ve mesajlaşma mekanizmaları üzerinden haberleşir. Böylece sistem büyüdükçe yeni servisler eklemek veya mevcut servisleri değiştirmek çok daha kolay hâle gelir. Gelecekte farklı ekipler aynı anda farklı servisler üzerinde bağımsız olarak çalışabilir.

Bu projenin bir diğer önemli hedefi de uzun yıllar boyunca geliştirilebilecek sürdürülebilir bir yazılım mimarisi oluşturmaktır. Kod tekrarını azaltmak, bağımlılıkları kontrol altında tutmak, test edilebilirliği artırmak, modülerliği korumak ve gelecekte oluşabilecek teknik borçları en aza indirmek temel tasarım hedefleri arasında yer alır. Bu nedenle SOLID, Clean Architecture, Domain Driven Design (DDD), Hexagonal Architecture, CQRS, Event Driven Architecture ve Repository Pattern gibi kurumsal yazılım prensipleri projenin temelini oluşturur.

Sonuç olarak CoreMusic; tek bir uygulama değil, çok katmanlı, çok servisli, API merkezli, güvenlik odaklı, yüksek performanslı ve uzun vadeli geliştirilebilirliği hedefleyen kurumsal bir dijital medya platformudur. Web uygulamalarından gömülü sistemlere, profesyonel ses işleme motorlarından yapay zekâ destekli servis mimarisine kadar birçok farklı teknolojiyi ortak bir mimari altında birleştiren bu proje, ölçeklenebilirlik, sürdürülebilirlik ve yüksek yazılım kalitesi esas alınarak tasarlanmaktadır.


# CoreMusic API Nasıl Bir Sistemdir?

CoreMusic API, klasik anlamda yalnızca REST endpoint'lerinden oluşan bir web servisi değildir. Bu API, CoreMusic ekosisteminin merkezinde yer alan ve sistemdeki tüm bileşenlerin ortak iletişim dili olarak çalışan merkezi servis katmanıdır. Sistemin tamamı API odaklı (API-First) olarak tasarlanmıştır. İster web arayüzü, ister masaüstü uygulaması, ister mobil istemci, ister Raspberry Pi tabanlı medya sistemi, ister araç içi bilgi-eğlence sistemi olsun, bütün istemciler sistemle yalnızca API üzerinden iletişim kurar. Hiçbir istemci doğrudan veritabanına, Repository katmanına veya iş mantığına erişemez.

CoreMusic API'nin temel amacı yalnızca veri döndürmek değildir. API; kimlik doğrulama, yetkilendirme, iş kurallarını çalıştırma, veri doğrulama, servisler arası haberleşme, olay yönetimi (Event Management), güvenlik, loglama, izlenebilirlik (Observability), sürüm yönetimi ve sistem orkestrasyonunun tamamını yöneten merkezi katmandır. Başka bir ifadeyle API, sistemin sinir sistemi gibi çalışır ve tüm bileşenler arasındaki veri akışını kontrol eder.

CoreMusic mimarisinde API doğrudan Controller mantığıyla tasarlanmamıştır. Her API isteği önce API Gateway'e ulaşır. Gateway üzerinde Authentication, Authorization, Rate Limit, API Key doğrulama, JWT doğrulama, Request Validation, Audit Logging ve Version kontrolü yapılır. İstek gerekli kontrollerden geçtikten sonra Middleware Pipeline üzerinden Application katmanına yönlendirilir. Application katmanı ilgili Use Case'i çalıştırır. Use Case gerekli Domain servislerini kullanır ve yalnızca Repository Interface üzerinden veri erişimi sağlar. Repository'nin hangi veritabanını kullandığını veya verinin nasıl saklandığını üst katmanlar bilmez. Böylece API tamamen teknoloji bağımsız bir mimari üzerine kurulmuş olur.

CoreMusic API, tek bir büyük API değildir. Sistem servis tabanlıdır ve her servis kendi API alanına sahiptir. Kimlik doğrulama işlemleri Auth API tarafından yürütülür. Kullanıcı yönetimi User API içerisinde yer alır. Müzik yönetimi Music API tarafından gerçekleştirilir. Medya işlemleri Media API üzerinden yapılır. İndirme sistemi Download API tarafından yönetilir. Arama işlemleri Search API üzerinden gerçekleştirilir. Oynatma sistemi Player API içerisinde bulunur. Ses motoru Audio API ve DSP API tarafından yönetilir. Bildirim sistemi Notification API içerisinde yer alırken, analiz işlemleri Analytics API tarafından yürütülür. Yönetim paneli yalnızca Admin API ile haberleşir. Yapay zekâ işlemleri AI API tarafından sağlanır. Sistem konfigürasyonu ise System API üzerinden gerçekleştirilir. Böylece her servis yalnızca kendi sorumluluğunu taşır ve diğer servislerden bağımsız olarak geliştirilebilir.

API'nin en önemli tasarım prensiplerinden biri "API First" yaklaşımıdır. Sistemde hiçbir endpoint doğrudan kodlanmaz. Önce OpenAPI sözleşmesi hazırlanır. Daha sonra Request ve Response DTO'ları oluşturulur. Ardından Validation kuralları yazılır. Daha sonra API Contract hazırlanır. Bu sözleşmeler tamamlandıktan sonra Use Case tasarlanır ve en son kod yazılır. Böylece kod, mimariyi tanımlayan unsur değil; mimarinin uygulaması hâline gelir. Bu yaklaşım sayesinde istemciler ile sunucu arasında oluşabilecek uyumsuzluklar daha geliştirme aşamasında ortadan kaldırılır.

CoreMusic API aynı zamanda Public API ve Internal API olarak ikiye ayrılır. Public API; mobil uygulamalar, web istemcileri, masaüstü uygulamaları, üçüncü taraf geliştiriciler ve SDK'lar tarafından kullanılır. Internal API ise yalnızca sistem servisleri arasında kullanılır. Download Service, AI Service, Audio Engine, Notification Service veya diğer arka plan servisleri Internal API üzerinden haberleşir. Böylece dış dünyaya açılan endpoint'ler ile sistem içi iletişim tamamen birbirinden ayrılmış olur.

Sistemde BFF (Backend for Frontend) mimarisi de kullanılacaktır. SPA, masaüstü uygulaması, mobil uygulama ve gömülü sistemler aynı API'yi doğrudan kullanmayacaktır. Her istemci tipi için kendi Backend for Frontend katmanı bulunacaktır. Böylece masaüstü istemcisi ihtiyaç duymadığı verileri almayacak, mobil istemci daha küçük veri paketleri kullanacak, gömülü cihazlar ise düşük donanım kaynaklarına uygun optimize edilmiş cevaplar alacaktır. Bu yaklaşım istemci performansını artırırken gereksiz veri transferini de azaltacaktır.

CoreMusic API yalnızca HTTP isteklerinden oluşmayacaktır. Gerçek zamanlı iletişim gereken durumlarda WebSocket altyapısı kullanılacaktır. Ses spektrumu, oynatma durumu, canlı cihaz bilgileri, bildirimler ve gerçek zamanlı olaylar WebSocket API üzerinden aktarılacaktır. Arka planda çalışan servisler arasında ise Event Driven Architecture kullanılacaktır. Bir servis başka bir servisi doğrudan çağırmak yerine Event yayınlayacaktır. Örneğin yeni bir medya dosyası sisteme eklendiğinde Media Service "MediaCreated" olayını yayınlar. AI Service öneri sistemini günceller, Search Service arama indeksini oluşturur, Notification Service gerekli bildirimleri gönderir ve Analytics Service istatistikleri günceller. Bu servislerin hiçbiri birbirine doğrudan bağımlı değildir.

API'nin tamamı ortak bir Composer paketi olan **coremusic-shared** üzerine kurulacaktır. Bu paket yalnızca yardımcı fonksiyonlardan oluşmayacaktır. Sistemde kullanılan bütün DTO'lar, Request nesneleri, Response modelleri, Validation sınıfları, API sözleşmeleri, ortak Exception yapıları, Event tanımları, HttpClient, ApiClient, Logger, Security bileşenleri, Serializer, Pagination sistemi, OpenAPI modelleri ve SDK altyapısı bu paket içerisinde bulunacaktır. Böylece bütün servisler aynı sözleşmeleri kullanacak ve kod tekrarının önüne geçilecektir.

CoreMusic API güvenlik odaklı olarak tasarlanacaktır. Her istek Correlation ID ile takip edilecek, Audit Log oluşturulacak, Request ve Response standartlaştırılacak, JWT doğrulaması yapılacak, API Key kontrolü uygulanacak, OAuth desteği sağlanacak ve Rate Limit politikaları merkezi olarak yönetilecektir. Endpoint bazında Permission sistemi çalışacak ve her işlem ayrıntılı olarak loglanacaktır. Güvenlik kuralları yalnızca Public API için değil, Internal API için de geçerli olacaktır.

API geliştirme sürecinde doğrudan kod yazılmayacaktır. Her yeni endpoint önce mimari açıdan değerlendirilir. Endpoint'in hangi Domain'e ait olduğu belirlenir. Kullanılacak DTO'lar hazırlanır. API sözleşmesi yazılır. Güvenlik analizi yapılır. Katman bağımlılıkları kontrol edilir. SOLID prensiplerine uygunluğu incelenir. DDD kuralları doğrulanır. Performans analizi gerçekleştirilir. Test senaryoları hazırlanır. OpenAPI dokümantasyonu oluşturulur. Ancak bu aşamalar tamamlandıktan sonra gerçek implementasyon geliştirilir. Böylece sistem büyüdükçe mimari bütünlüğünü koruyabilir ve teknik borç oluşmasının önüne geçilir.

Sonuç olarak CoreMusic API, yalnızca veri sağlayan klasik bir REST servisi değildir. Bu API; istemciler, servisler, ses motoru, yapay zekâ bileşenleri, gömülü sistemler ve yönetim panelleri arasında çalışan merkezi iletişim omurgasıdır. Tüm platformlar aynı mimari prensiplere bağlı olarak bu API üzerinden haberleşir ve sistemin tamamı bu katman üzerinden yönetilir. API, CoreMusic ekosisteminin kalbi ve ana entegrasyon noktası olarak tasarlanmıştır.

# CoreMusic API Neyi Çözmek İçin Oluşturulmuştur?

CoreMusic API'nin temel amacı yalnızca istemcilere veri sağlamak değildir. Bu API, CoreMusic ekosistemindeki tüm yazılım bileşenlerinin ortak iletişim dili, ortak güvenlik katmanı ve merkezi entegrasyon noktası olarak tasarlanmıştır. Sistemin tamamı API merkezli (API-First) bir mimari üzerine kurulmuştur. Böylece farklı platformlar, servisler ve uygulamalar aynı kurallar çerçevesinde birlikte çalışabilir.

Geleneksel uygulamalarda kullanıcı arayüzü, iş mantığı ve veritabanı birbirine sıkı şekilde bağlıdır. Bu yaklaşım küçük projelerde yönetilebilir olsa da, proje büyüdükçe bakım maliyetini artırır, yeni özellik eklemeyi zorlaştırır ve teknik borç oluşturur. CoreMusic API bu sorunu ortadan kaldırmak için tasarlanmıştır. API, istemci katmanı ile sistemin çekirdeği arasında standart ve kontrollü bir iletişim katmanı oluşturur.

CoreMusic API'nin çözmeyi hedeflediği en önemli problemlerden biri farklı platformların aynı iş mantığını tekrar yazmasını engellemektir. Web uygulaması, masaüstü uygulaması, mobil uygulama, Raspberry Pi cihazı, araç içi sistem ve gelecekte geliştirilecek diğer istemciler aynı servisleri kullanır. Böylece her platform için ayrı iş mantığı geliştirilmez. İş kuralları yalnızca API içerisinde bulunur ve bütün istemciler aynı kuralları kullanır.

API'nin çözmeyi amaçladığı diğer önemli problem katman bağımlılığıdır. Kullanıcı arayüzü hiçbir zaman doğrudan veritabanına bağlanmaz, SQL sorgusu çalıştırmaz veya Repository sınıflarını kullanmaz. Bunun yerine bütün işlemler API üzerinden gerçekleştirilir. Bu sayede kullanıcı arayüzü ile iş mantığı tamamen birbirinden ayrılır. Böylece arayüz değişse bile sistemin çekirdeği değişmez.

CoreMusic API aynı zamanda güvenliği merkezi hâle getirmek için oluşturulmuştur. Kimlik doğrulama, yetkilendirme, API anahtarları, JWT doğrulaması, OAuth, Rate Limit, Request Validation, Audit Log ve güvenlik politikaları tek bir merkezden yönetilir. Böylece her servisin kendi güvenlik mekanizmasını tekrar yazmasına gerek kalmaz ve bütün sistem ortak güvenlik standartlarını kullanır.

Bir diğer amaç servisler arasındaki bağımlılığı azaltmaktır. Büyük projelerde servislerin birbirlerinin veritabanına doğrudan erişmesi veya birbirlerinin iç sınıflarını kullanması zamanla ciddi mimari problemlere neden olur. CoreMusic API bu sorunu çözmek için her servisin yalnızca tanımlanmış API sözleşmeleri üzerinden haberleşmesini sağlar. Böylece servisler birbirinden bağımsız olarak geliştirilebilir, test edilebilir ve dağıtılabilir.

CoreMusic API, sistem büyüdükçe yeni özelliklerin mevcut yapıyı bozmadan eklenebilmesini hedefler. Yeni bir servis geliştirildiğinde mevcut istemciler değiştirilmez. Yeni endpoint'ler, yeni API sürümleri veya yeni servisler mevcut mimariyi bozmadan sisteme entegre edilebilir. Bu yaklaşım uzun yıllar boyunca sürdürülebilir bir yazılım geliştirme süreci sağlar.

API'nin çözmeyi amaçladığı önemli konulardan biri de ortak standart oluşturmaktır. Sistemdeki bütün servisler aynı Request ve Response yapısını, aynı hata kodlarını, aynı doğrulama kurallarını, aynı güvenlik politikalarını ve aynı veri sözleşmelerini kullanır. Böylece farklı ekipler tarafından geliştirilen servisler bile ortak bir mimari dil konuşur.

CoreMusic API ayrıca performans ve ölçeklenebilirlik problemlerini çözmek amacıyla tasarlanmıştır. İstemciler yalnızca ihtiyaç duydukları veriyi alır. Servisler bağımsız olarak ölçeklenebilir. Cache, Queue, Event Bus, WebSocket ve farklı depolama sistemleri API mimarisine entegre edilebilir. Böylece kullanıcı sayısı veya veri miktarı artsa bile sistem yeniden tasarlanmadan büyüyebilir.

API'nin bir diğer amacı geliştirici deneyimini iyileştirmektir. Tüm servisler ortak bir `coremusic-shared` paketi kullandığından DTO'lar, sözleşmeler, doğrulama kuralları, istemciler ve ortak bileşenler tekrar yazılmaz. Bu durum kod tekrarını azaltır, bakım maliyetini düşürür ve geliştiricilerin aynı standartlarla çalışmasını sağlar.

CoreMusic API aynı zamanda geleceğe yönelik genişlemeyi kolaylaştırmak için tasarlanmıştır. Bugün yalnızca müzik oynatma işlemlerini yöneten bir servis olan sistem, gelecekte video, podcast, canlı yayın, yapay zekâ servisleri, profesyonel ses işleme, IoT cihazları veya farklı donanımlar eklense bile aynı mimari üzerinde çalışmaya devam edebilir. API bu genişlemeyi destekleyen temel entegrasyon katmanıdır.

Sonuç olarak CoreMusic API; istemcileri iş mantığından ayırmak, güvenliği merkezileştirmek, servisler arası bağımlılığı azaltmak, ortak standart oluşturmak, kod tekrarını önlemek, ölçeklenebilirliği artırmak, farklı platformları ortak bir mimari altında toplamak ve uzun yıllar sürdürülebilecek kurumsal bir yazılım altyapısı oluşturmak amacıyla geliştirilmektedir. Bu nedenle API, yalnızca veri sağlayan bir servis değil; CoreMusic ekosisteminin merkezi iletişim, entegrasyon ve yönetim katmanıdır.

**KATMANI MİAMRİ**
```
                                        INTERNET
                                            │
                                            │
                        ┌──────────────────────────────────┐
                        │        Public Clients            │
                        │──────────────────────────────────│
                        │ SPA                             │
                        │ Mobile                          │
                        │ Desktop                         │
                        │ Embedded                        │
                        │ Raspberry                       │
                        │ Third Party                     │
                        │ SDK                             │
                        └──────────────────────────────────┘
                                            │
                                            │ HTTPS
                                            ▼
                    ┌────────────────────────────────────────────┐
                    │             API GATEWAY                    │
                    │────────────────────────────────────────────│
                    │ Routing                                   │
                    │ Versioning                                │
                    │ Authentication                            │
                    │ Authorization                             │
                    │ Rate Limit                                │
                    │ API Key                                   │
                    │ JWT                                       │
                    │ OAuth                                     │
                    │ Logging                                   │
                    │ Request Validation                        │
                    │ Response Normalization                    │
                    └────────────────────────────────────────────┘
                                            │
         ┌──────────────────────────────────┼────────────────────────────────────┐
         │                                  │                                    │
         ▼                                  ▼                                    ▼

 Public API                        Internal API                         WebSocket API

         │                                  │                                    │
         └──────────────────────────────────┼────────────────────────────────────┘
                                            │
                                            ▼
                          ┌──────────────────────────────────┐
                          │        Middleware Pipeline        │
                          ├──────────────────────────────────┤
                          │ Request Id                       │
                          │ Logger                           │
                          │ Security                         │
                          │ CSP                              │
                          │ CSRF                             │
                          │ Session                          │
                          │ JWT                              │
                          │ Permission                       │
                          │ Validation                       │
                          │ Rate Limit                       │
                          │ Audit                            │
                          └──────────────────────────────────┘
                                            │
                                            ▼
                          ┌──────────────────────────────────┐
                          │      Application Layer           │
                          ├──────────────────────────────────┤
                          │ Use Cases                       │
                          │ Command                         │
                          │ Query                           │
                          │ CQRS                            │
                          │ DTO Mapping                     │
                          └──────────────────────────────────┘
                                            │
                                            ▼
                          ┌──────────────────────────────────┐
                          │        Domain Layer              │
                          ├──────────────────────────────────┤
                          │ Entities                         │
                          │ Value Objects                    │
                          │ Domain Services                  │
                          │ Business Rules                   │
                          │ Domain Events                    │
                          └──────────────────────────────────┘
                                            │
                                            ▼
                          ┌──────────────────────────────────┐
                          │      Repository Contracts        │
                          └──────────────────────────────────┘
                                            │
                                            ▼
                          ┌──────────────────────────────────┐
                          │      Infrastructure Layer        │
                          ├──────────────────────────────────┤
                          │ PDO                             │
                          │ MySQL                           │
                          │ SQLite                          │
                          │ Redis                           │
                          │ Queue                           │
                          │ Cache                           │
                          │ Filesystem                      │
                          │ FFmpeg                          │
                          │ External API                    │
                          └──────────────────────────────────┘
```

**GENEL SİSTEM MİAMRİSİ**
```
                    Client Layer
                         │
                         ▼
                  Presentation Layer
                         │
                         ▼
                    SPA Router
                         │
                         ▼
                     Api Client
                         │
                         ▼
                    API Gateway
                         │
                         ▼
                 Middleware Pipeline
                         │
                         ▼
                 Application Layer
                         │
                         ▼
                    Domain Layer
                         │
                         ▼
               Repository Contracts
                         │
                         ▼
              Infrastructure Layer
                         │
      ┌──────────────────┼──────────────────────┐
      ▼                  ▼                      ▼
   MySQL             Redis/Cache           External Services
                                              │
                                              ├── Auth
                                              ├── Download
                                              ├── Media
                                              ├── AI
                                              ├── Audio
                                              ├── FFmpeg
                                              ├── Neva Engine
                                              └── Device
```

**KATMANI MİAMRİ DEVAMNI**
```
API Servis Mimarisi
API Gateway

│

├── Auth API

├── User API

├── Music API

├── Playlist API

├── Album API

├── Artist API

├── Download API

├── Media API

├── Library API

├── Search API

├── Recommendation API

├── Streaming API

├── Device API

├── Audio API

├── DSP API

├── Player API

├── Notification API

├── Analytics API

├── Admin API

├── AI API

└── System API
Servisler
music.coremusic.net

↓

API Gateway

↓

Auth Service

↓

Media Service

↓

Download Service

↓

Audio Service

↓

Device Service

↓

Search Service

↓

AI Service

↓

Notification Service

↓

Analytics Service

↓

System Service
SPA Akışı
SPA

↓

ApiClient

↓

Gateway

↓

Middleware

↓

Use Case

↓

Domain

↓

Repository Interface

↓

Infrastructure

↓

Database
Yasaklanan Bağımlılıklar
SPA

×

PDO

×

MySQL

×

Repository

×

Entity

×

Infrastructure

×

Filesystem

×

FFmpeg

×

Redis

×

Cache

×

SQL
SPA yalnızca ApiClient kullanır.
coremusic-shared
coremusic-shared

│

├── Contracts

├── DTO

├── Request

├── Response

├── Enums

├── ValueObjects

├── Exceptions

├── Validation

├── Events

├── EventDispatcher

├── Security

├── Cryptography

├── Auth

├── Jwt

├── OAuth

├── Permission

├── HttpClient

├── ApiClient

├── RetryPolicy

├── CircuitBreaker

├── Cache

├── Queue

├── Serializer

├── Logger

├── Configuration

├── Pagination

├── Filtering

├── Sorting

├── OpenAPI

├── SDK

└── Helpers
Panel → API İlişkisi
coremusic.net
        │
        ▼
API Gateway

music.coremusic.net
        │
        ▼
API Gateway

admin.coremusic.net
        │
        ▼
API Gateway

car.coremusic.net
        │
        ▼
API Gateway

home.coremusic.net
        │
        ▼
API Gateway

studio.coremusic.net
        │
        ▼
API Gateway

pro.coremusic.net
        │
        ▼
API Gateway

download.coremusic.net
        │
        ▼
Download API

media.coremusic.net
        │
        ▼
Media API

auth.coremusic.net
        │
        ▼
Auth API
```

```
1. BFF (Backend For Frontend)
Şu an tüm istemciler aynı API'yi kullanıyor.
Ben bunu ayırırdım.
SPA
        │
        ▼
 SPA BFF API

Desktop
        │
        ▼
 Desktop BFF API

Mobile
        │
        ▼
 Mobile BFF API

Embedded
        │
        ▼
 Embedded BFF API

                │
                ▼

          Internal APIs
Avantajları
Her istemci kendi DTO'sunu alır.
Mobile gereksiz veri çekmez.
SPA'ya özel endpoint olur.
Embedded cihazlar hafif JSON kullanır.
2. API Gateway tek başına çalışmasın
Araya Service Discovery eklerdim.
Client

↓

Gateway

↓

Service Registry

↓

Auth

↓

Media

↓

Audio

↓

AI

↓

Download
İleride servis arttığında Gateway hiçbir şeyi hardcode bilmez.
3. API Version Registry
Sadece
v1
yeterli değil.
/api/v1

/api/v2

/api/internal/v1

/api/public/v1

/api/admin/v1
Ayrıca
Deprecated

Experimental

Stable

Legacy
etiketleri olmalı.
4. API Contract First
Kod yazılmadan önce
OpenAPI
↓
DTO
↓
Contract
↓
Validation
↓
Kod
sırası izlenmeli.
Kod hiçbir zaman Contract'tan önce yazılmamalı.
5. Async Event Bus
Bence çok eksik.
API

↓

Command

↓

Event

↓

Event Bus

↓

Notification

↓

Logger

↓

Analytics

↓

AI

↓

Download

↓

Search Index
Her servis birbirini doğrudan çağırmamalı.
6. CQRS tamamen ayrılmalı
Şu an sadece yazılmış.
Ama mimariye de yansımalı.
Write API

↓

Command

↓

Use Case

↓

Repository

-------------------

Read API

↓

Query

↓

Read Model

↓

Cache

↓

Response
7. Read Database
İleride lazım olacak.
Master

↓

Replica 1

↓

Replica 2

↓

Analytics
Okuma yükü master'a binmez.
8. Cache Layer
Şu an Redis yazıyor.
Ben Cache Abstraction isterim.
Application

↓

Cache Interface

↓

Redis

↓

APCu

↓

File Cache

↓

Memory Cache
Kod Redis'e bağımlı olmaz.
9. Storage Layer
Bunu kesin eklerdim.
Storage

↓

Local

↓

NAS

↓

SMB

↓

NFS

↓

S3

↓

Azure Blob

↓

Backblaze

↓

Cloudflare R2
İleride storage değiştirmek kolay olur.
10. Adapter Layer
External API'ler direkt kullanılmamalı.
Spotify Adapter

YouTube Adapter

Deezer Adapter

MusicBrainz Adapter

LastFM Adapter

Discogs Adapter
Hepsi
MusicProviderInterface
uygular.
11. Plugin System
Bu projeye çok uygun.
Plugin

↓

Manifest

↓

Permission

↓

Sandbox

↓

Lifecycle

↓

API

↓

Events
Sonradan eklenecek özellikler çekirdeği bozmaz.
12. Feature Flag
Enterprise projelerde mutlaka bulunmalı.
Feature

↓

Enabled

↓

Disabled

↓

Beta

↓

Experimental

↓

Internal
13. API Policy Engine
API

↓

Policy

↓

Permission

↓

Rate Limit

↓

Quota

↓

Subscription

↓

License

↓

Audit
14. Audit Pipeline
Sadece log yetmez.
Request

↓

Audit

↓

Logger

↓

Metrics

↓

Tracing

↓

Alert

↓

Dashboard
15. Domain Ayrımı
Ben Domain'i daha da bölerdim.
Identity

Media

Player

Audio

Device

Network

Library

Playlist

Streaming

Download

Search

Recommendation

Notification

Analytics

Administration

AI

System
Her biri ayrı bounded context olur.
16. Shared Library Katmanları
coremusic-shared tek paket yerine modüler olabilir.
coremusic-contracts

coremusic-http

coremusic-auth

coremusic-security

coremusic-cache

coremusic-events

coremusic-sdk

coremusic-openapi

coremusic-validation

coremusic-support
Bu yapı bağımlılıkları küçültür ve paketlerin bağımsız sürümlenmesini sağlar.
17. Architecture Governance
Bunu .claude ve .ai içine mutlaka eklerdim.
Kod yazmadan önce zorunlu kontroller

↓

Architecture Review

↓

ADR Check

↓

Layer Check

↓

Dependency Check

↓

SOLID Check

↓

DDD Check

↓

Security Check

↓

Performance Check

↓

Testability Check

↓

Documentation Check

↓

Risk Analysis

↓

Refactoring Plan

↓

Approval

↓

Implementation
```

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

api projsi kodlaaycğız bu api hem dışarıya acıalcxka hemde ieöçridne de kullaıclak api iel gili herş şey baurdan olacak ve **coremusci shared** cosmposer pakerti ile kodlancak shared kaolr cosmpsoer de oalcka am guvenlik önemdlir spa rouetr de api ihticaı avrsa baurdan auth d api ihticaı varsa buardan istek atıp çalışamaıldır.

**CHATGPT CIKTISI KARAR PLANI**

```
Ben olsam yeni dosyayı şöyle planlarım.
.ai/
└── architecture/
    └── 03-contracts/
        ├── api-architecture-master.md
        ├── api-design-rules.md
        ├── api-endpoints.md
        ├── api-versioning.md
        ├── api-security.md
        ├── api-authentication.md
        ├── api-error-codes.md
        ├── api-event-system.md
        ├── api-websocket.md
        ├── api-rate-limit.md
        ├── api-pagination.md
        ├── api-filtering.md
        ├── api-validation.md
        ├── api-idempotency.md
        ├── api-internal-contract.md
        ├── api-public-contract.md
        ├── api-sdk.md
        ├── api-testing.md
        ├── api-observability.md
        └── api-roadmap.md
Ben promptun amacını da değiştirirdim.
Şu an prompt
API kodla

diyor.
Bu yanlış.
Doğrusu
API Architecture Design
API Governance
API Contract Design
API Standards
API Development Rules
API Refactoring
API Security Audit

olmalı.
Bir de önemli bir karar alınması gerekiyor.
Bence CoreMusic'te tek API olmayacak.
Çünkü mimariye göre zaten birçok servis bulunuyor.
Dolayısıyla API de servis bazlı ayrılmalı.
```

```
Client
      │
      ▼
API Gateway
      │
 ┌────┴──────────────────────┐
 │                           │
 ▼                           ▼

Auth API

Media API

Music API

Playlist API

Library API

Download API

Streaming API

Search API

Notification API

Device API

Network API

Player API

Audio API

DSP API

Admin API

Analytics API

System API

AI API

Public API

↓

Mobil

SPA

Desktop

Third Party

SDK

Webhook

OAuth


-------------------------

Internal API

↓

SPA Router

Background Worker

Queue

Cron

Downloader

Media

Auth

Shared Library

Neva Engine

WebSocket

IPC

CLI
```

**API doğrudan Controller çağırmayacak.**
Onun yerine

```
SPA

↓

API Client

↓

HTTP

↓

API Gateway

↓

Middleware

↓

Application Layer

↓

Use Case

↓

Domain

↓

Repository

↓

Infrastructure
```

```
SPA Router
hiçbir zaman
Repository
görmeyecek.
hiçbir zaman
PDO
görmeyecek.
hiçbir zaman
Database
görmeyecek.
Her şey API üzerinden olacak.
Bir diğer önemli karar.
Sen yazmışsın
coremusic-shared composer paketi

Ben bunu büyütürdüm.
coremusic-shared

├── Contracts

├── DTO

├── Value Objects

├── Enums

├── Exceptions

├── Validators

├── Response

├── Request

├── Events

├── Event Dispatcher

├── Auth Client

├── Http Client

├── Api Client

├── Logger

├── Cache

├── Queue

├── Serializer

├── Configuration

├── Security

├── Cryptography

├── RateLimiter

├── Pagination

├── OpenAPI

└── SDK
Bu paketin amacı yalnızca ortak yardımcı sınıflar değil, tüm servisler arasında paylaşılan sözleşmelerin (contracts), DTO'ların, istemcilerin ve standartların merkezi olması olmalıdır.
Ben olsam AI promptunun sonunda şu çıktıları zorunlu yaparım.
ÇIKTI SIRASI

1.
API Analizi

2.
Mevcut Codebase Analizi

3.
SOLID İhlalleri

4.
Clean Code İhlalleri

5.
Katman İhlalleri

6.
DDD İhlalleri

7.
Security İhlalleri

8.
Refactoring Planı

9.
API Mimarisi

10.
API Gateway Tasarımı

11.
Internal API

12.
Public API

13.
Composer Shared Library

14.
OpenAPI Tasarımı

15.
Versioning

16.
Authentication

17.
Authorization

18.
Rate Limit

19.
Caching

20.
Queue

21.
Events

22.
WebSocket

23.
Observability

24.
Monitoring

25.
Logging

26.
Testing

27.
Migration Planı

28.
Risk Analizi

29.
Implementation Roadmap

30.
Kodlama başlamadan önce nihai Architecture Review
Bu yapı, mevcut Vault'taki SSOT, Zero Code Before Plan, Hard Gate, Red Team / Truth Mode ve katmanlı mimari yaklaşımıyla da uyumludur.
```

Bu alanlarda mümkün olduğunca **PSR uyumlu** ve **Composer tabanlı** çözümler tercih edilecektir.
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


# CoreMusic Composer Package Standard
## Red Team • Human Mode • Truth Mode

> Bu doküman CoreMusic ekosisteminde kullanılacak Composer paket standartlarını tanımlar.

---

# Temel Kural

CoreMusic içerisinde;

- Güvenlik
- Authentication
- Authorization
- HTTP
- Validation
- Logging
- Cache
- Queue
- Serialization
- OpenAPI
- Rate Limit
- Retry
- Event
- UUID
- Encryption
- Configuration
- Testing

gibi tekrar eden problemler mümkün olduğunca **kurumsal seviyede kabul görmüş Composer paketleri** ile çözülmelidir.

Hiçbir geliştirici;

- kendi JWT kütüphanesini,
- kendi Logger'ını,
- kendi Validation sistemini,
- kendi HTTP Client'ını,
- kendi Rate Limiter'ını,
- kendi UUID üreticisini,
- kendi Cryptography katmanını

sıfırdan yazmamalıdır.

Öncelik sırası:

1. Enterprise Composer Package
2. PSR Standardı
3. PHP-FIG Standardı
4. Kurumsal Mimari
5. Kendi Kodumuz

---

# Kullanılacak Composer Paketleri

## HTTP

| Amaç | Paket |
|------|--------|
| HTTP Client | guzzlehttp/guzzle |
| PSR-18 Discovery | php-http/discovery |
| PSR-7 | nyholm/psr7 |

---

## Dependency Injection

| Amaç | Paket |
|------|--------|
| Container | php-di/php-di |

---

## Routing

| Amaç | Paket |
|------|--------|
| Fast Router | nikic/fast-route |

---

## UUID

| Amaç | Paket |
|------|--------|
| UUID | ramsey/uuid |

---

## Date Time

| Amaç | Paket |
|------|--------|
| Date API | nesbot/carbon |

---

## Logging

| Amaç | Paket |
|------|--------|
| Logger | monolog/monolog |

---

## Validation

| Amaç | Paket |
|------|--------|
| Validation | symfony/validator |

---

## Serializer

| Amaç | Paket |
|------|--------|
| Serializer | symfony/serializer |

---

## Property Access

| Amaç | Paket |
|------|--------|
| Property Access | symfony/property-access |

---

## Event Dispatcher

| Amaç | Paket |
|------|--------|
| Event Dispatcher | symfony/event-dispatcher |

---

## Messenger / Queue

| Amaç | Paket |
|------|--------|
| Queue | symfony/messenger |

---

## Cache

| Amaç | Paket |
|------|--------|
| Cache | symfony/cache |

---

## Lock

| Amaç | Paket |
|------|--------|
| Distributed Lock | symfony/lock |

---

## Rate Limit

| Amaç | Paket |
|------|--------|
| Rate Limiter | symfony/rate-limiter |

---

## Workflow

| Amaç | Paket |
|------|--------|
| Workflow Engine | symfony/workflow |

---

## Expression Language

| Amaç | Paket |
|------|--------|
| Policy Rules | symfony/expression-language |

---

## Configuration

| Amaç | Paket |
|------|--------|
| Config | symfony/config |
| DotEnv | vlucas/phpdotenv |

---

## Security

| Amaç | Paket |
|------|--------|
| JWT | firebase/php-jwt |
| OAuth2 Server | league/oauth2-server |
| OAuth2 Client | league/oauth2-client |
| CSRF | symfony/security-csrf |
| Password Hash | symfony/password-hasher |

---

## Authorization

| Amaç | Paket |
|------|--------|
| ACL / Permission | symfony/security-core |

---

## Encryption

| Amaç | Paket |
|------|--------|
| Crypto | paragonie/halite |
| Random | paragonie/random_compat (gerekirse eski sürümler için) |

---

## OpenAPI

| Amaç | Paket |
|------|--------|
| OpenAPI | zircote/swagger-php |

---

## JSON

| Amaç | Paket |
|------|--------|
| JSON Schema | opis/json-schema |

---

## Retry

| Amaç | Paket |
|------|--------|
| Retry | caseyamcl/guzzle_retry_middleware |

---

## CLI

| Amaç | Paket |
|------|--------|
| Console | symfony/console |

---

## Process

| Amaç | Paket |
|------|--------|
| Process | symfony/process |

---

## Filesystem

| Amaç | Paket |
|------|--------|
| Filesystem | league/flysystem |

Desteklenen Storage:

- Local
- NAS
- SMB
- NFS
- Amazon S3
- Azure Blob
- Cloudflare R2
- Backblaze B2

---

## Image

| Amaç | Paket |
|------|--------|
| Image Processing | intervention/image |

---

## Markdown

| Amaç | Paket |
|------|--------|
| Markdown | league/commonmark |

---

## HTML Sanitizer

| Amaç | Paket |
|------|--------|
| HTML Purifier | ezyang/htmlpurifier |

---

## MIME

| Amaç | Paket |
|------|--------|
| MIME Detection | symfony/mime |

---

## Mail

| Amaç | Paket |
|------|--------|
| Mail | symfony/mailer |

---

## Internationalization

| Amaç | Paket |
|------|--------|
| Translation | symfony/translation |

---

## Testing

| Amaç | Paket |
|------|--------|
| PHPUnit | phpunit/phpunit |
| Mock | mockery/mockery |
| Static Analysis | phpstan/phpstan |
| Code Style | friendsofphp/php-cs-fixer |
| Rector | rector/rector |
| Infection | infection/infection |

---

# coremusic-* Composer Paketleri

CoreMusic tek bir shared paketten oluşmamalıdır.

```
coremusic/contracts

coremusic/http

coremusic/auth

coremusic/security

coremusic/cache

coremusic/events

coremusic/openapi

coremusic/sdk

coremusic/logger

coremusic/support

coremusic/validation

coremusic/queue

coremusic/storage

coremusic/config

coremusic/monitoring

coremusic/testing

coremusic/api-client

coremusic/websocket

coremusic/observability
```

---

# Yasaklar

Aşağıdaki bileşenler sıfırdan yazılmayacaktır.

- JWT
- OAuth2
- Logger
- Validation
- HTTP Client
- UUID
- Rate Limiter
- Serializer
- Event Dispatcher
- Cache Engine
- Queue Engine
- OpenAPI Generator
- Cryptography
- Password Hashing
- CSRF Engine

---

# Mimari Kural

Her CoreMusic Composer paketi:

- PSR-1
- PSR-3
- PSR-4
- PSR-6
- PSR-7
- PSR-11
- PSR-12
- PSR-14
- PSR-15
- PSR-16
- PSR-17
- PSR-18

standartlarına uygun olacaktır.

---

# Sonuç

CoreMusic'in temel hedefi mevcut problemi yeniden çözmek değildir.

Amaç;

- battle-tested,
- güvenlik denetimlerinden geçmiş,
- aktif geliştirilen,
- uzun dönem desteklenen,
- kurumsal projelerde kullanılan

Composer paketlerini kullanarak yalnızca CoreMusic'e özgü iş kurallarını geliştirmektir.