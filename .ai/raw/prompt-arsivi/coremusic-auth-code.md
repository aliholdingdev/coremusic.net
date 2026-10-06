Red Team , human , truth mode -> şidmi bu .ai daki verielre göre kodlama yapamız gerekiyor 

**ROLE**

**Sen 50+ yıllık Senior Software Architect, AI Knowledge Engineer, Technical Writer, Enterprise Solution Architect ve Documentation Engineer'sin.**

# **Uzmanlık alanların:**

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

Bu sistemi kodlamaya başlamadan önce bir yazılım geliştirici gibi değil, bir sistem mimarı gibi düşünürdüm. İlk kararım şu olurdu: CoreMusic'in auth sistemi bağımsız bir login modülü olarak değil, bütün ekosistemin merkezi kimlik ve yetkilendirme altyapısı olarak tasarlanmalıdır. Çünkü burada sadece bir kullanıcının giriş yapması değil; ev sistemi, araç sistemi, stüdyo sistemi, profesyonel panel, yönetim paneli, medya servisi ve API servislerinin aynı güvenlik modeli üzerinden çalışması gerekiyor. Bu nedenle ilk aşamada kod yazmaz, mevcut sistemi analiz eder, mevcut login, logout, session, cookie, middleware, database bağlantıları, frontend akışı ve subdomain iletişimini tamamen çıkarırdım. Öncelikle elimdeki sistemi anlamadan yeni bir mimari kurmazdım.
İlk olarak mevcut repository yapısını incelerdim. Hangi klasörlerde PHP backend var, hangi klasörlerde JavaScript frontend var, hangi dosya hangi görevi yapıyor, hangi sınıf hangi sınıfı çağırıyor, hangi servis hangi veritabanına bağlanıyor bunları çıkarırdım. Buradaki amacım mevcut sistemin fotoğrafını çekmek olurdu. Çünkü yeniden yazılan sistemlerde en büyük hata eski sistemin davranışlarının bilinmeden sıfırdan yapılmasıdır. Kullanıcıların alıştığı login davranışı, session süresi, hata mesajları, yönlendirmeler ve izin modelleri kaybolabilir. Bu yüzden önce mevcut sistemin çalışan mantığını belgeleyip sonra yeni mimariye geçirirdim.
Daha sonra CoreMusic'in servis sınırlarını belirlerdim. auth.coremusic.net merkezi kimlik servisi olurdu. Diğer bütün subdomainler kendi içinde kullanıcı doğrulama sistemi taşımazdı. Home, Studio, Pro, Car, Admin gibi sistemler sadece auth servisine güvenirdi. Yani her uygulama "ben kullanıcıyı doğrularım" mantığı ile çalışmazdı. Bunun yerine "bu kullanıcı gerçekten CoreMusic tarafından doğrulanmış mı, hangi role sahip, hangi izinlere sahip" sorularının cevabını merkezi auth sisteminden alırdı. Böylece tek bir güvenlik noktası oluşurdu.
Auth sistemini PHP tabanlı geliştirirdim. Çünkü backend beklentisi PHP olduğu için tüm kritik işlemler PHP tarafında olurdu. JavaScript sadece kullanıcı arayüzünü yönetirdi. Kullanıcı formu doldurur, JavaScript gerekli kontrolleri yapar, isteği gönderir, fakat gerçek güvenlik kontrolü hiçbir zaman JavaScript'e bırakılmazdı. Şifre kontrolü, session oluşturma, kullanıcı yetkisi, rol kontrolü, medya erişimi gibi bütün kritik kararlar PHP tarafında verilirdi.
Mimariyi Clean Architecture ve Hexagonal Architecture prensiplerine göre tasarlardım. Bunun sebebi gelecekte CoreMusic'in sadece web olarak kalmayacak olmasıdır. İleride C++ Audio Engine, Windows Driver, ASIO sistemi, mobil uygulamalar veya farklı cihazlar aynı kullanıcı altyapısını kullanabilir. Bu nedenle iş mantığını doğrudan PHP frameworklerine veya veritabanına bağlamazdım. Domain katmanı tamamen bağımsız olurdu. Kullanıcı, rol, izin, medya erişimi gibi kavramlar burada tanımlanırdı. Application katmanı iş akışlarını yönetirdi. Infrastructure katmanı ise MySQL, Redis, dosya sistemi, cache ve dış servis bağlantılarını yönetirdi.
İlk geliştireceğim bölüm authentication çekirdeği olurdu. Önce kullanıcı modeli oluşturulur, ardından kullanıcı kayıt sistemi, giriş sistemi, logout sistemi ve session yönetimi yapılırdı. Login sırasında kullanıcının bilgileri doğrulanır, şifre güvenli şekilde kontrol edilir, başarılı ise güvenli bir session oluşturulur ve kullanıcıya güvenli cookie üzerinden oturum bilgisi verilir. Browser tarafında kesinlikle hassas token veya şifre tutulmazdı. JavaScript tarafında localStorage veya sessionStorage içinde kritik kimlik bilgisi saklamazdım.
Session mimarisini özel olarak tasarlardım. Çünkü çoklu subdomain yapısında session yönetimi kritik noktadır. Kullanıcı auth sistemine giriş yaptıktan sonra home, studio, pro veya car sistemlerine geçtiğinde tekrar giriş yapmak zorunda kalmamalıdır. Bunun için merkezi session modeli oluştururdum. Session bilgisi güvenli şekilde sunucu tarafında tutulur, cookie üzerinden kullanıcı tanımlanır ve diğer servisler auth doğrulaması yaparak kullanıcı bilgisini alırdı.
Cross-Origin tarafında çok katı davranırdım. Çünkü burada en büyük güvenlik problemi yanlış CORS yapılandırmasıdır. Hiçbir zaman tüm domainlere izin veren açık bir yapı kullanmazdım. Sadece tanımlı CoreMusic subdomainleri izin listesinde olurdu. Auth sistemi gelen isteğin hangi domain tarafından geldiğini kontrol eder, izin verilen domain değilse isteği reddederdi. Böylece başka sitelerin CoreMusic oturum sistemini kullanması engellenirdi.
Middleware yapısını merkezi güvenlik duvarı gibi düşünürdüm. Her istek sisteme girdiğinde önce kaynağını kontrol eder, sonra güvenlik kontrollerinden geçirir, session bilgisini yükler, CSRF kontrolünü yapar, kullanıcının kim olduğunu doğrular ve son olarak yetki kontrolünden sonra uygulamaya ulaşmasına izin verirdim. Böylece controller içerisinde dağınık güvenlik kontrolleri yazılmazdı. Güvenlik tek bir pipeline üzerinden yönetilirdi.
Role ve permission sistemini baştan tasarlardım. Çünkü CoreMusic sadece normal kullanıcıdan oluşmayacak. Normal kullanıcı, premium kullanıcı, studio kullanıcısı, araç sistemi kullanıcısı, admin ve sistem yöneticisi gibi farklı seviyeler olacaktır. Bu yüzden sadece "admin mi değil mi" kontrolü yapmazdım. Kullanıcının hangi işlemleri yapabileceğini izin sistemi ile yönetirdim.
Media servisini ayrı değerlendirirdim. Buradaki yaklaşım doğru: media.coremusic.net normal PHP web sitesi gibi davranmamalıdır. Burası dosya ve medya servisidir. Kullanıcıların doğrudan dosya yollarına erişmesine izin verilmez. Kullanıcı müzik dinlemek istediğinde önce auth sistemi kullanıcının hakkını kontrol eder, daha sonra kontrollü erişim mekanizması oluşturulur. Admin kullanıcılar medya yönetimi yapabilir, normal kullanıcılar ise sadece izin verilen medya içeriğini dinleyebilir. Böylece depolama alanı dışarıya açık hale gelmez.
Database tasarımında kullanıcı bilgilerini, session bilgilerini, rollerini, izinlerini, audit kayıtlarını ve medya erişim kayıtlarını ayrı modeller olarak düşünürdüm. Tek büyük kullanıcı tablosu içine bütün sistemi koymazdım. Çünkü ileride büyüyen sistemlerde bu yapı yönetilemez hale gelir. Kullanıcı kimliği, yetki sistemi ve servis erişimleri ayrı sorumluluklarda tutulurdu.
Frontend tarafında JavaScript SPA yapısı kullanılırdı. Ancak SPA router sadece sayfa geçişini yönetirdi. Authentication kararlarını vermezdi. Sayfa açıldığında kullanıcı durumunu auth servisinden kontrol eder, gelen cevaba göre ekranı gösterirdi. Böylece frontend sadece görünüm katmanı olarak kalırdı.
Kodlama sırasını şu şekilde planlardım: önce mevcut sistem analizi, sonra mimari dokümantasyon, sonra database tasarımı, sonra auth domain tasarımı, sonra session sistemi, sonra middleware sistemi, sonra API sözleşmeleri, sonra frontend entegrasyonu, sonra diğer servislerin bağlantısı ve en son eski sistemden yeni sisteme geçiş yapılırdı.
Refactor sürecinde eski sistemi bir anda silmezdim. Önce yeni auth sistemi paralel hazırlanır, test edilir, güvenlik kontrollerinden geçirilir, daha sonra servisler tek tek yeni sisteme bağlanırdı. Çünkü CoreMusic gibi büyük bir ekosistemde doğrudan değişim yapmak ciddi risk oluşturur.
Sonuç olarak bu sistemi bir "login ekranı" olarak değil, CoreMusic ekosisteminin Identity Provider + Authorization Platform + Security Gateway katmanı olarak tasarlardım. PHP burada merkezi backend olur, JavaScript sadece kullanıcı deneyimini yönetir, media servisi korunmuş ayrı bir kaynak olur, diğer tüm servisler auth altyapısına güvenerek çalışırdı. Böylece sistem ileride web, araç, ev sistemi, stüdyo ve C++ ses motoru gibi farklı platformlara büyüyebilecek sağlam bir temel kazanırdı.

# CoreMusic Nasıl Bir Projedir?

CoreMusic, benim için geleneksel müzik oynatıcı olmanın çok ötesinde, çoklu platformlarda çalışabilen, çok katmanlı bir medya ekosistemidir. Bu projeyi kodlama sürecine başlamadan önce, onu sadece bir web uygulaması ya da masaüstü programı olarak değil, ses mühendisliğinden web mimarisine, embedded sistemlerden kullanıcı deneyimine kadar birçok alanı birleştiren entegre bir sistem olarak düşünürdüm. Bu bakış açısıyla, CoreMusic'in sadece müzik çalmaktan fazlasını yapabileceği, kullanıcıların müzik deneyimini baştan sona dönüştürebilecek potansiyelini fark ederdim. Çünkü asıl değer, basit bir çalma işlevi değil, farklı platformları bir araya getiren, ses kalitesine önem veren ve kullanıcılara profesyonel deneyim sunan bütünsel yapısındadır.

Mimari tasarım aşamasında, CoreMusic'i monolithic (tek parça) bir yapıda inşa etmek yerine, her biri kendi görevini yerine getiren bağımsız servislerden oluşan microservices veya modular monolitik bir mimariyle kurardım. Bu yaklaşım, sistemin ölçeklenmesini, bakımının kolaylaşmasını ve farklı platformlara entegrasyonunu sağlar. Çünkü CoreMusic, hem evde kullanılan bir müzik merkezi, hem stüdyoda profesyonel bir düzenleme aracı, hem de araç içi multimedya sistemi olarak tasarlanmaktadır. Bu çeşitlilik, her platformun kendi ihtiyaçlarına uygun bir mimari gerektirir.

Ses kalitesi ve DSP (Digital Signal Processing) konularına özel önem verirdim. Çünkü CoreMusic, sıradan bir medya oynatıcı değil, ses kalitesine önem veren profesyonel kullanıcıları hedeflemektedir. Bu nedenle, ses verilerinin işlenmesinde kayıpsız formatlar, düşük gecikmeli ses akışı ve isteğe bağlı olarak gelişmiş ses düzenleme özellikleri sunulur. Ayrıca, ASIO gibi düşük gecikmeli ses sürücülerini destekleyerek, profesyonel müzik ekipmanlarıyla sorunsuz entegrasyon sağlanırdı.

Mobil ve web arayüzleri geliştirilirken, kullanıcı deneyimi ön planda tutulurdu. Çünkü kullanıcılar istedikleri yerden, istedikleri cihazla CoreMusic sistemine erişebilmelidir. Bu amaçla, iOS, Android ve web platformlarında tutarlı ve akıcı bir arayüz tasarlanır. Kullanıcının müziklerini kolayca keşfetmeleri, çalma listeleri oluşturmaları ve farklı ses modları arasında geçiş yapabilmeleri sağlanır. Bu entegre yaklaşım, kullanıcıların müzik dinleme alışkanlıklarını zenginleştirir.

Embedde sistemler, CoreMusic'in önemli bir bileşenini oluşturur. Çünkü bu sistem, Raspberry Pi gibi cihazlarla evde veya araç içinde çalışabilen bir medya sunucusu olarak da kullanılabilir. Bu, kullanıcılara kendi müziklerini yerel ağ üzerinden cihazlarına yayınlama imkanı sunar. Böylece, internet bağlantısı olmadan da yüksek kalitede müzik dinlenebilir ve kontrol edilebilir.

Son olarak, CoreMusic, güvenlik ve veri yönetimi açısından da dikkat çekici bir yapıdır. Kullanıcıların müzik verileri güvenli bir şekilde saklanır ve yetkilendirme sistemleri sayesinde sadece sahipleri tarafından erişilebilir. Bu, hem bireysel kullanıcılar hem de profesyoneller için güvenli bir ortam oluşturur. CoreMusic, sadece bir müzik uygulaması değil, kullanıcılara müzikle daha zengin ve kontrollü bir deneyim sunan kapsamlı bir ekosistemdir.

CoreMusic, sıradan bir müzik çalar veya standart bir web uygulamasının çok ötesinde, kapsamlı ve çok katmanlı bir teknoloji ekosistemidir. Temelinde, müzik deneyimini farklı platformlar ve kullanım senaryoları arasında kesintisiz, güvenli ve yüksek performanslı bir şekilde sunmayı hedefleyen devasa bir mimari yatmaktadır. Bu proje, kullanıcıların müziğe evde, arabada, profesyonel stüdyo ortamlarında veya gündelik yaşamlarında aynı ekosistem üzerinden ulaşmasını sağlayan bir köprü görevi görmektedir.

Sistemin en belirgin özelliklerinden biri, spesifik ihtiyaçlara göre bölünmüş olan geniş subdomain mimarisidir. Proje; home, pro, studio, car, admin, api, download ve media gibi her biri kendi özel amacına hizmet eden ayrı modüllerden oluşmaktadır. Bu yapı, mikroservis mantığına yakın bir izolasyon sağlayarak her bir uygulamanın kendi iş alanına odaklanmasına olanak tanırken, sistemin genel büyüme ve ölçeklenebilirlik potansiyelini en üst düzeye çıkarmaktadır.

CoreMusic ekosisteminin kalbinde, tüm bu dağıtık yapıyı bir arada tutan ve "Identity Provider" (Kimlik Sağlayıcı) ile "Security Gateway" (Güvenlik Geçidi) olarak görev yapan merkezi bir kimlik doğrulama servisi (auth.coremusic.net) yer almaktadır. Diğer hiçbir subdomain kendi içinde bağımsız bir login sistemi barındırmaz; kimlik doğrulama, oturum yönetimi ve güvenlik kararlarının tamamı bu merkezi otorite üzerinden yürütülür. Böylece kullanıcılar sisteme bir kez giriş yaptıklarında (Single Sign-On), tüm ekosistemde yetkileri dahilinde pürüzsüzce dolaşabilirler.

Güvenlik, projenin tasarım aşamasından itibaren en kritik yapı taşı olarak ele alınmıştır. Cross-Origin (CORS) politikaları son derece katı bir şekilde uygulanarak, yalnızca yetkilendirilmiş CoreMusic subdomain'lerinin iletişim kurmasına izin verilmektedir. Oturum bilgileri JavaScript tarafında, localStorage veya sessionStorage gibi güvensiz alanlarda tutulmak yerine; sunucu tarafında doğrulanmış ve yalnızca güvenli cookie'ler aracılığıyla taşınan, CSRF ve Rate Limit gibi korumalarla donatılmış sağlam bir middleware pipeline üzerinden yönetilmektedir.

Yazılım mimarisi açısından proje; Clean Architecture, Hexagonal Architecture ve SOLID gibi modern, sürdürülebilir mühendislik prensipleri üzerine inşa edilmektedir. Kod tabanında Presentation (Sunum), Application (Uygulama), Domain (Çekirdek/İş Mantığı) ve Infrastructure (Altyapı) katmanları birbirinden kesin çizgilerle ayrılmıştır. Bu sayede, iş mantığı framework'lerden, veritabanlarından veya arayüzden tamamen bağımsız hale getirilmiş, gelecekteki değişikliklere karşı son derece dirençli bir sistem tasarlanmıştır.

CoreMusic'in teknoloji yığını, yüksek güvenlik ve performans gereksinimlerini karşılayacak şekilde seçilmiştir. Sistemin backend (sunucu) tarafı, tüm kritik işlemleri, veritabanı yönetimini ve güvenlik kararlarını üstlenen Enterprise seviyesinde PHP 8.x ile geliştirilmektedir. Frontend (Önyüz) tarafında ise, sayfa yönlendirmelerini yöneten ancak güvenlik kararlarını tamamen backend'e bırakan, JavaScript tabanlı Single Page Application (SPA) mimarisi kullanılmaktadır.

Medya yönetimi, CoreMusic'in en hassas noktalarından biridir ve media.coremusic.net sıradan bir web sayfası olarak değil, kapalı bir "medya deposu" (vault) olarak tasarlanmıştır. Kullanıcıların doğrudan dosya yollarına erişimi kesinlikle engellenmiştir; bunun yerine, merkezi auth sisteminden gelen yetki anahtarlarına (key) sahip doğrulanmış istekler üzerinden medya akışı sağlanır. Bu yaklaşım, telif haklarının korunması, yetkisiz indirmelerin engellenmesi ve depolama alanının dış dünyaya tamamen kapatılmasını garanti eder.

Sistem, her kullanıcının aynı haklara sahip olmadığı, gelişmiş bir Rol Tabanlı Erişim Kontrolü (RBAC - Role Based Access Control) modeli kullanmaktadır. Standart bir dinleyici, premium abone, stüdyo (studio) kullanıcısı, araç (car) entegrasyonu kullanan biri veya sistem yöneticisi (admin) olmak üzere farklı kullanıcı profilleri bulunmaktadır. İzin sistemi sadece "kullanıcı giriş yapmış mı?" sorusunu değil, "bu kullanıcının mevcut kaynağa veya eyleme yetkisi var mı?" sorusunu her istekte titizlikle denetler.

CoreMusic sadece bugünün web teknolojileriyle sınırlı kalacak bir proje değildir; geleceğe dönük muazzam bir vizyon barındırır. Mimari altyapı; ilerleyen aşamalarda C++ tabanlı Audio Engine'ler, ASIO/WASAPI ses arayüzleri, donanım sürücüleri (Windows Driver Kit) ve özel DSP (Digital Signal Processing) entegrasyonları gibi çok daha düşük seviyeli ve yüksek performanslı ses teknolojilerini destekleyecek esneklikte tasarlanmaktadır. Domain katmanının izole edilmesi, web dışındaki bu platformların da aynı çekirdeği kullanabilmesini sağlar.

Özetle CoreMusic; müzik dinleme, üretme ve yönetme süreçlerini profesyonel bir ekosistem çatısı altında toplayan, üst düzey güvenlik standartlarına sahip, katmanlı mimarisiyle öne çıkan devasa bir platformdur. Baştan sona yeniden tasarlanıp kodlanacak olan bu sistem, hem son kullanıcılara kesintisiz bir deneyim sunacak hem de arkasında çalışan 50 yıllık mühendislik tecrübesi ve disipliniyle dijital müzik dünyasında yeni bir standart belirleyecektir.

# CoreMusic AUTH Nasıl Bir Sistemdir?

CoreMusic AUTH, sıradan bir giriş ekranı veya basit bir üyelik tablosu değil; tüm ekosistemin güvenliğini, kimlik yönetimini ve yetkilendirme süreçlerini tek noktadan kontrol eden, kurumsal (enterprise) standartlarda kurgulanmış merkezi bir "Security Gateway" (Güvenlik Geçidi) ve "Identity Provider" (Kimlik Sağlayıcı) platformudur. Projenin kalbi niteliğindeki bu servis, 50 yıllık bir mühendislik vizyonuyla, hataya tahammülü olmayan kritik bir altyapı olarak tasarlanmıştır.

Sistemin en belirgin felsefesi "Merkezi Otorite" ilkesine dayanır. Ekosistem içerisinde yer alan home, pro, studio, car, media gibi hiçbir subdomain, kendi başına bağımsız bir kimlik doğrulama mantığı (login sistemi) taşımaz. Sisteme giriş çıkışlar, şifre sıfırlamalar ve yetki atamaları tamamen auth.coremusic.net üzerinden yürütülür. Bu sayede Single Sign-On (SSO) mimarisi kurularak, kullanıcının bir kez giriş yaptığında tüm CoreMusic platformlarında yetkileri dahilinde pürüzsüzce dolaşabilmesi sağlanır.

Oturum yönetimi (Session Management) stratejisi, maksimum güvenlik ilkesiyle frontend (JavaScript) ortamından tamamen izole edilmiştir. Geleneksel sistemlerin aksine localStorage veya sessionStorage gibi dış müdahaleye ve XSS saldırılarına açık alanlarda hiçbir hassas kimlik verisi veya JWT token barındırılmaz. Oturum, tamamen backend (sunucu) tarafında üretilir ve tarayıcıya yalnızca HTTPOnly, Secure ve SameSite bayraklarıyla (flags) şifrelenmiş özel cookie'ler aracılığıyla iletilir.

Çoklu subdomain mimarisi doğası gereği ciddi Cross-Domain riskleri taşıdığından, Cross-Origin Resource Sharing (CORS) politikası son derece katı ve tavizsiz yapılandırılmıştır. Dışarıdan gelen rastgele sitelerin veya yetkisiz domainlerin bu auth servisine istek (request) yapması donanımsal ve yazılımsal güvenlik duvarlarıyla engellenir. Yalnızca sistem tarafından tanımlanmış beyaz listedeki (whitelist) resmi CoreMusic subdomain'leri bu API ile iletişim kurabilir.

Yazılım mimarisi katmanında; Presentation, Application, Domain ve Infrastructure katmanlarının birbirinden kesin çizgilerle ayrıldığı "Clean Architecture" ve "Hexagonal Architecture" prensipleri uygulanmaktadır. Kimlik doğrulama iş mantığı, framework'lerden (PHP çatılarından) veya veritabanı türlerinden (MySQL, Redis) tamamen bağımsızdır. Bu izolasyon, gelecekte sistemin farklı bir teknolojiye taşınması gerekse bile çekirdek auth mantığının bozulmadan kalmasını garanti eder.

Güvenlik süreci, istek henüz uygulamaya (controller katmanına) ulaşmadan önce devreye giren çok katmanlı bir "Middleware Pipeline" (Ara katman boru hattı) ile yönetilir. Sunucuya gelen her HTTP isteği sırasıyla; Origin Check (kaynak kontrolü), CORS denetimi, Rate Limit (hız/istek sınırlaması), CSRF doğrulama ve en son Session (oturum) kontrolünden geçirilir. Bu pipeline sayesinde kötü niyetli veya geçersiz istekler, sisteme hiçbir yük bindirmeden kapıda reddedilir.

Yetkilendirme tarafında, basit bir "kullanıcı ve admin" ayrımının çok ötesine geçen, gelişmiş bir Rol Tabanlı Erişim Kontrolü (RBAC - Role Based Access Control) modeli devrededir. Standart müzik dinleyicisi, premium abone, stüdyo prodüktörü, araç içi (car) sistem entegrasyonu kullanan cihaz veya sistem yöneticisi gibi birbirinden farklı profiller, son derece ince ayarlanmış bir izin matriksi ile yönetilir. Sistem sadece giriş yapılıp yapılmadığını değil, talep edilen eyleme yetki olup olmadığını da mikro saniyeler içinde hesaplar.

CoreMusic AUTH, aynı zamanda media.coremusic.net üzerinde depolanan fiziki müzik dosyalarının yegane bekçisidir. Kullanıcıların doğrudan bir MP3 veya FLAC dosyasının dizin yoluna ulaşması mimari olarak imkansızdır. Bir medya akışı (streaming) başlatılmak istendiğinde, media sunucusu arka planda yetki anahtarını AUTH servisine doğrulatır. Erişim hakkı yoksa, depolama alanı (vault) dış dünyaya kapılarını tamamen kilitler.

Platformun gelecekteki genişleme haritası (roadmap) da bu mimarinin içinde hazır beklemektedir. İlerleyen dönemlerde devreye alınacak olan İki Aşamalı Doğrulama (2FA), biyometrik girişler, API anahtarlarıyla çalışan harici geliştirici entegrasyonları ve C++ tabanlı ASIO/WASAPI donanım sürücüsü bağlantıları için gerekli tüm arayüz (interface) ve veri transfer objeleri (DTO) altyapıda yerini almıştır. Platform, web tabanlı olmanın ötesinde bir donanım/yazılım entegrasyonuna hazırdır.

Sonuç itibarıyla CoreMusic AUTH; PHP 8.x'in Enterprise (Kurumsal) gücünü, nesne yönelimli programlamanın (OOP) en saf halini ve katı tasarım desenlerini (Design Patterns) bir araya getiren bir güvenlik omurgasıdır. Yeniden yazım sürecinde, eski sistemin taşıdığı her türlü güvenlik ve performans açığı kapatılarak, sistemin kendi kendini iyileştiren, hataya geçit vermeyen ve 50 yıllık bir sistem mimarının imzasını taşıyan kusursuz bir yapıya dönüştürülmesi hedeflenmektedir.

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

## **Şimdi bizim bir projemiz var.Sadece adımlarla bu proje yapılması gerekmektedir.;**

### **Subdomainler:**
- auth.coremusic.net
- home.coremusic.net
- pro.coremusic.net
- studio.coremusic.net
- car.coremusic.net
- admin.coremusic.net
- download.coremusic.net
- media.coremusic.net
- api.coremusic.net
- coremusic.net

- Port İzin Verilen Portlar : 80 81 443 4433 ve dieğr servisler 

Şimdi giriş yaparken bu dizinlerde özellikle Cross Origin izin vermelidir. Başka dizinlere izin vermemelidir.

Şimdi mevcut yapıyı incele, mevcut login outyapsını incele ve yeniden yazmak için plan oluşturmanı istiyorum.

Auth sistemimiz hem CS hem PHP'dir ama Ama bekenti php'dir Frontend'i javascript'tir. Yani backend kodları PHP ile yazılmalıdır. Sıfırdan yeniden yapılmalıdır. SPR Out'la bilgisi yoktur. Sadece Out yönlendirmesi SPR Out'la yapılmalıdır.

**1. Sistem Genel Mimarisi**
```
                         Internet
                             │
                       coremusic.net
                             │
      ┌──────────────────────┴──────────────────────┐
      │                                             │
auth.coremusic.net                         music.coremusic.net
      │                                             │
      │                                             │
home.coremusic.net                       studio.coremusic.net
      │                                             │
      │                                             │
 car.coremusic.net                          pro.coremusic.net
      │                                             │
      └──────────────────────┬──────────────────────┘
                             │
                     api.coremusic.net
                             │
         ┌──────────────┬──────────────┬──────────────┐
         │              │              │
      MySQL         Redis        Media Service

```

**2. Login Sequence**
```
User
 │
 │ Login
 ▼
home or car or pro or studio or media .coremusic.net
 │
 │ Redirect
 ▼
auth.coremusic.net/login
 │
 │ Validate
 ▼
Auth Service
 │
 ▼
UserRepository
 │
 ▼
MySQL
 │
 ▼
Session
 │
 ▼
Cookie
 │
 ▼
302 Redirect
 │
 ▼
home or car or pro or studio or media .coremusic.net
 │
 ▼
SPA
 │
 ▼
Dashboard
```

**Not:** ama media.coremusic.net media deposudur medyaalr baurda metadalar buarda sakalnackatır ozyuden uath da buarda şuarad aişe yarayackatır emdyaı girş yapanalr eriebielisn özle bir şey key olsun bu keye sahip olan kualcıalr adminler bu dizine erişebeilsin ama normnal kualcıalr erişemesin ama nromal kullanıalr panelde mueizk dineleyebirlisn bu şekidelk koruma amaclı bir sistemdir oayj oyzuden oarda olackatır yani php web sayafsı değildir depodur sadece 

**3. HTTP Request Pipeline**
```
Browser
    │
    ▼
Windows ISS Server or Wamsperver or Apache 2
    │
    ▼
index.php
    │
    ▼
Bootstrap
    │
    ▼
Router
    │
    ▼
Middleware Pipeline
    │
    ├── Origin
    ├── CORS
    ├── RateLimit
    ├── Session
    ├── CSRF
    ├── Auth
    ├── RBAC
    └── Validation
    │
    ▼
Controller
    │
    ▼
Application Service
    │
    ▼
Repository
    │
    ▼
PDO
    │
    ▼
MySQL
```

**4. Katman Mimarisi**
```
+--------------------------------------------------+
|                 Presentation                     |
|--------------------------------------------------|
| SPA                                              |
| Router                                            |
| JS                                                |
| HTML                                              |
+--------------------------------------------------+

+--------------------------------------------------+
|                 Application                      |
|--------------------------------------------------|
| AuthService                                      |
| SessionService                                   |
| UserService                                      |
+--------------------------------------------------+

+--------------------------------------------------+
|                    Domain                        |
|--------------------------------------------------|
| User                                             |
| Session                                          |
| Permission                                       |
| Role                                             |
| ValueObjects                                     |
+--------------------------------------------------+

+--------------------------------------------------+
|                Infrastructure                    |
|--------------------------------------------------|
| PDO                                              |
| Redis                                            |
| Repository                                       |
| Config                                           |
+--------------------------------------------------+
```

**5. Middleware Pipeline**
```
HTTP Request
      │
      ▼
Origin Check
      │
      ▼
CORS
      │
      ▼
Rate Limit
      │
      ▼
Security Headers
      │
      ▼
Session
      │
      ▼
CSRF
      │
      ▼
Authentication
      │
      ▼
Authorization
      │
      ▼
Controller
```

**6. Authentication Lifecycle**
```
Login
 │
 ▼
Validate
 │
 ▼
Password Verify
 │
 ▼
Session Create
 │
 ▼
Cookie
 │
 ▼
Authenticated
 │
 ▼
Session Check
 │
 ▼
Refresh
 │
 ▼
Logout
 │
 ▼
Destroy Session
```

**7. Cross-Domain Authentication**
```
                 auth.coremusic.net
                         │
      ┌──────────────────┼──────────────────┐
      │                  │                  │
      ▼                  ▼                  ▼
music.coremusic.net   home.coremusic.net  studio.coremusic.net
      │                  │                  │
      ▼                  ▼                  ▼
car.coremusic.net     admin.coremusic.net  pro.coremusic.net
      │                  │                  │
      ▼                  ▼                  ▼
api.coremusic.net     media.coremusic.net  coremusic.net
      │                  │                  │
      ▼                  ▼                  ▼
      │          download.coremusic.net     │
      │                  │                  │
      └──────────────┬───┴──────────────────┘
                     ▼
             Session Validation API
```

**8. Class Diagram**
```
Controller
     │
     ▼
AuthService
     │
     ▼
SessionService
     │
     ▼
UserRepository
     │
     ▼
PDO
     │
     ▼
MySQL

Entity

User

Role

Permission

Session

DTO

LoginRequest

LoginResponse

SessionDTO
```

**9. Component Diagram**
```
Browser
   │
SPA
   │
Router
   │
API
   │
Controller
   │
Application
   │
Domain
   │
Repository
   │
Database
```

**10. Dependency Graph**
```
Router
 │
 ├────────► AuthController
 │               │
 │               ▼
 │        AuthService
 │               │
 │        ┌──────┴──────┐
 │        ▼             ▼
 │ SessionService   UserRepository
 │        │             │
 │        ▼             ▼
 │      Redis          PDO
 │                      │
 │                      ▼
 │                    MySQL
```

**11. CORS / Origin Flow**
```
Request
   │
   ▼
Origin Exists?
   │
   ├── No
   │      ▼
   │    Reject
   │
   └── Yes
          │
          ▼
Whitelist?
          │
     ├────┴────┐
     │         │
    No        Yes
     │         │
   403      Continue
```

**12. Reverse Engineering Master Diagram**
```
Repository
    │
    ▼
Folder Analysis
    │
    ▼
Dependency Analysis
    │
    ▼
Request Flow
    │
    ▼
Routing
    │
    ▼
Middleware
    │
    ▼
Authentication
    │
    ▼
Session
    │
    ▼
Database
    │
    ▼
Frontend
    │
    ▼
Security
    │
    ▼
SOLID Audit
    │
    ▼
Clean Architecture Audit
    │
    ▼
Refactor Plan
    │
    ▼
New Architecture
```

**Infrastructure**
Network Diagram
Deployment Diagram
Service Diagram
Port Diagram
Runtime Diagram

**Architecture**
Context Diagram
Container Diagram
Component Diagram
Package Diagram
Module Diagram
Layer Diagram
Folder Diagram

**Authentication**
Login
Logout
Session
Refresh
Cookie
Remember
Media Authorization
Admin Authorization
Cross Domain
API Authentication
Service Authentication
Device Authentication
RBAC
Permission
Policy

**Security**
Middleware Pipeline
Request Pipeline
Response Pipeline
CSP
CORS
CSRF
Threat Model
Trust Boundary

**Database**
ER Diagram
Repository Diagram
Entity Diagram
Aggregate Diagram
Session Schema

**Reverse Engineering**
Dependency Graph
Call Graph
Include Graph
Namespace Graph
Composer Graph
Package Graph
Folder Graph
Dead Code Graph
Circular Dependency Graph

**Refactoring**
Current Architecture
Target Architecture
Migration Diagram
Transition Diagram
Risk Diagram

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



# Composer Paket Politikası

## Genel Kural

CoreMusic projesinde kullanılacak üçüncü parti PHP kütüphaneleri **Composer** üzerinden yönetilecektir.

Sistem içerisinde ihtiyaç duyulan standart bileşenler (HTTP, Routing, Validation, Cache, Logging, Security, Authentication, Database vb.) mümkün olduğunca sıfırdan geliştirilmeyecek, bunun yerine;

- aktif olarak geliştirilen,
- uzun süreli bakım (LTS) desteğine sahip,
- güvenlik denetimlerinden geçmiş,
- geniş topluluk tarafından kullanılan,
- PSR standartlarına uyumlu,
- Composer üzerinden yönetilebilen,
- Enterprise seviyesinde kabul görmüş

paketler tercih edilecektir.

Framework bağımlılığı oluşturacak paketlerden kaçınılacak, yalnızca ihtiyaç duyulan bileşenler kullanılacaktır.

---

# Kullanılacak Composer Paketleri

## HTTP

| Paket | Amaç |
|--------|------|
| `guzzlehttp/guzzle` | HTTP Client |

---

## PSR-7

| Paket | Amaç |
|--------|------|
| `nyholm/psr7` | PSR-7 HTTP Message |
| `laminas/laminas-diactoros` | PSR-7 Implementasyonu |

---

## Routing

| Paket | Amaç |
|--------|------|
| `nikic/fast-route` | Router |

---

## Dependency Injection

| Paket | Amaç |
|--------|------|
| `php-di/php-di` | Dependency Injection Container |

---

## Validation

| Paket | Amaç |
|--------|------|
| `respect/validation` | Veri Doğrulama |

---

## Logging

| Paket | Amaç |
|--------|------|
| `monolog/monolog` | Log Yönetimi |

---

## UUID

| Paket | Amaç |
|--------|------|
| `ramsey/uuid` | UUID Üretimi |

---

## Date & Time

| Paket | Amaç |
|--------|------|
| `nesbot/carbon` | Tarih ve Saat İşlemleri |

---

## Environment

| Paket | Amaç |
|--------|------|
| `vlucas/phpdotenv` | Environment Yönetimi |

---

## Cache

| Paket | Amaç |
|--------|------|
| `symfony/cache` | Cache Yönetimi |

---

## Redis

| Paket | Amaç |
|--------|------|
| `predis/predis` | Redis Client |

---

## Filesystem

| Paket | Amaç |
|--------|------|
| `league/flysystem` | Dosya Sistemi Soyutlaması |

---

## MIME

| Paket | Amaç |
|--------|------|
| `symfony/mime` | MIME İşlemleri |

---

## Mail

| Paket | Amaç |
|--------|------|
| `symfony/mailer` | Mail Gönderimi |

---

## Serializer

| Paket | Amaç |
|--------|------|
| `symfony/serializer` | DTO / JSON / XML Dönüşümleri |

---

## Config

| Paket | Amaç |
|--------|------|
| `symfony/config` | Konfigürasyon Yönetimi |

---

## Finder

| Paket | Amaç |
|--------|------|
| `symfony/finder` | Dosya Arama |

---

## Process

| Paket | Amaç |
|--------|------|
| `symfony/process` | Process Yönetimi |

---

## Console

| Paket | Amaç |
|--------|------|
| `symfony/console` | CLI Komutları |

---

## Lock

| Paket | Amaç |
|--------|------|
| `symfony/lock` | Lock Mekanizması |

---

## Event

| Paket | Amaç |
|--------|------|
| `symfony/event-dispatcher` | Event Sistemi |

---

## Messenger

| Paket | Amaç |
|--------|------|
| `symfony/messenger` | Message Bus |

---

## Rate Limiting

| Paket | Amaç |
|--------|------|
| `symfony/rate-limiter` | Rate Limiting |

---

## CSRF

| Paket | Amaç |
|--------|------|
| `symfony/security-csrf` | CSRF Koruması |

---

## Password Hashing

| Paket | Amaç |
|--------|------|
| `symfony/password-hasher` | Şifre Hashleme |

---

## Translation

| Paket | Amaç |
|--------|------|
| `symfony/translation` | Çoklu Dil Desteği |

---

## YAML

| Paket | Amaç |
|--------|------|
| `symfony/yaml` | YAML Okuma/Yazma |

---

## Expression Language

| Paket | Amaç |
|--------|------|
| `symfony/expression-language` | Rule Engine |

---

## Database

| Paket | Amaç |
|--------|------|
| `doctrine/dbal` | Database Abstraction Layer |

> ORM kullanılmayacaktır. Tüm veritabanı işlemleri PDO + DBAL üzerinden yürütülecektir.

---

## Database Migration

| Paket | Amaç |
|--------|------|
| `robmorgan/phinx` | Migration Yönetimi |

---

## JWT

| Paket | Amaç |
|--------|------|
| `lcobucci/jwt` | JWT Yönetimi |

---

## OAuth2

| Paket | Amaç |
|--------|------|
| `league/oauth2-server` | OAuth2 Server |

---

## Encryption

| Paket | Amaç |
|--------|------|
| `paragonie/sodium_compat` | Modern Şifreleme |

---

## Security

| Paket | Amaç |
|--------|------|
| `paragonie/constant_time_encoding` | Timing Attack Koruması |

---

## HTML Sanitizer

| Paket | Amaç |
|--------|------|
| `ezyang/htmlpurifier` | XSS Koruması |

---

## Device Detection

| Paket | Amaç |
|--------|------|
| `mobiledetect/mobiledetectlib` | Cihaz Algılama |

---

## Image

| Paket | Amaç |
|--------|------|
| `intervention/image` | Görsel İşleme |

---

## QR Code

| Paket | Amaç |
|--------|------|
| `endroid/qr-code` | QR Kod Oluşturma |

---

## PDF

| Paket | Amaç |
|--------|------|
| `dompdf/dompdf` | PDF Oluşturma |

---

## Excel

| Paket | Amaç |
|--------|------|
| `phpoffice/phpspreadsheet` | Excel İşlemleri |

---

## Archive

| Paket | Amaç |
|--------|------|
| `maennchen/zipstream-php` | ZIP Stream |

---

## HTTP Discovery

| Paket | Amaç |
|--------|------|
| `php-http/discovery` | PSR HTTP Discovery |

---

## Async HTTP

| Paket | Amaç |
|--------|------|
| `amphp/http-client` | Asenkron HTTP |

---

## Async Runtime

| Paket | Amaç |
|--------|------|
| `amphp/amp` | Asenkron Programlama |

---

# Geliştirme Araçları (Development)

| Paket | Amaç |
|--------|------|
| `phpunit/phpunit` | Unit Test |
| `pestphp/pest` | Modern Test Framework |
| `mockery/mockery` | Mock |
| `phpstan/phpstan` | Static Analysis |
| `vimeo/psalm` | Static Analysis |
| `rector/rector` | Refactoring |
| `friendsofphp/php-cs-fixer` | Kod Standartları |
| `infection/infection` | Mutation Testing |
| `roave/security-advisories` | Güvenlik Danışmanı |
| `phpcompatibility/php-compatibility` | PHP Uyumluluk Kontrolü |
| `composer-unused/composer-unused` | Kullanılmayan Paket Analizi |
| `maglnet/composer-require-checker` | Dependency Doğrulama |
| `deptrac/deptrac` | Katman Bağımlılık Analizi |

---

# İzleme ve Gözlemlenebilirlik

| Paket | Amaç |
|--------|------|
| `open-telemetry/sdk` | OpenTelemetry |
| `open-telemetry/api` | Telemetry API |
| `promphp/prometheus_client_php` | Prometheus Metrics |
| `sentry/sentry` | Error Tracking |

---

# Queue ve Mesajlaşma

| Paket | Amaç |
|--------|------|
| `enqueue/enqueue` | Queue Abstraction |
| `php-enqueue/redis` | Redis Queue |
| `php-amqplib/php-amqplib` | RabbitMQ |

---

# API

| Paket | Amaç |
|--------|------|
| `opis/json-schema` | JSON Schema Validation |
| `league/openapi-psr7-validator` | OpenAPI Validation |

---

# CoreMusic İçin Zorunlu Çekirdek Paketler

Aşağıdaki paketler CoreMusic'in temel mimarisinin bir parçasıdır ve varsayılan olarak kullanılacaktır.

- `php-di/php-di`
- `nikic/fast-route`
- `guzzlehttp/guzzle`
- `nyholm/psr7`
- `respect/validation`
- `monolog/monolog`
- `ramsey/uuid`
- `vlucas/phpdotenv`
- `symfony/cache`
- `symfony/rate-limiter`
- `symfony/security-csrf`
- `symfony/password-hasher`
- `league/flysystem`
- `predis/predis`
- `doctrine/dbal`
- `robmorgan/phinx`
- `lcobucci/jwt`
- `league/oauth2-server`
- `paragonie/sodium_compat`
- `ezyang/htmlpurifier`
- `symfony/console`
- `symfony/lock`
- `symfony/event-dispatcher`
- `symfony/mailer`
- `symfony/mime`
- `symfony/serializer`

---

# Mimari Kurallar

- Framework kullanılmayacaktır.
- ORM kullanılmayacaktır.
- Active Record kullanılmayacaktır.
- Service Locator kullanılmayacaktır.
- Magic Method tabanlı mimari tercih edilmeyecektir.
- PDO temel veritabanı erişim katmanı olacaktır.
- Tüm paketler Composer üzerinden yönetilecektir.
- Paket sürümleri Semantic Versioning (SemVer) kurallarına uygun yönetilecektir.
- Güvenlik güncellemeleri düzenli olarak takip edilecek ve bağımlılıklar periyodik olarak güncellenecektir.
- Yeni bir Composer paketi eklenmeden önce güvenlik, bakım durumu, lisans, topluluk desteği ve CoreMusic mimarisiyle uyumluluğu değerlendirilecektir.