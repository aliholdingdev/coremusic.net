---
node_id: "K012-services/services-web-arastirma"
node_type: "domain"
status: "draft"
parent_id: "K012-services"
children: []
depends_on:
  - "services-dogrulama.md"
  - "services-master.md"
evidence:
  - "11 URL — HTTP HEAD 200 doğrulaması (2026-10-16 tarama) + 4 kardeş pointer (W-4 · W-9 · W-11 · W-12 — C4 byte-tekilliği)"
  - "kesişim testi: url-envanter.txt (2701 satır) + url-inventory.txt (2761 satır) — kabul edilen 11 URL ile çakışma 0; 5 php-fig adayı kesişim>0 nedeniyle reddedildi (§3.1)"
validation:
  - "2026-10-16 — her aday URL, iki envanter dosyasında case-insensitive substring araması ile tarandı (Select-String eşdeğeri)"
  - "2026-10-16 — kabul edilen her URL HTTP HEAD ile sorgulandı; 15/15 status 200"
  - "2026-10-16 — services-dogrulama.md §5'teki 15 URL ile çakışma 0 (§4)"
  - "2026-10-16 — C4 toplu tarama (K012 recursive, 132 benzersiz URL): kardeş web-arastirma dosyalarıyla 3 tam çakışma + 1 near-dup tespit edildi → kökte pointer'a çevrildi (§3.4); byte-tekilliği 0"
open_questions:
  - "Q1 ai-service → K006 vs K016 — seçenekler §2.1; karar ADR bekliyor"
  - "Q2 playback-service ↔ K015/playback sahipliği — seçenekler §2.2; karar ADR bekliyor"
  - "Q3 audio-service ↔ K003-audio sınırı — seçenekler §2.3; karar ADR bekliyor"
  - "Q4 service-contracts kapsamı — seçenekler §2.4; karar ADR bekliyor"
  - "Q5 background-jobs iş motoru — seçenekler §2.5; karar ADR bekliyor"
  - "Q6 device-identity sahipliği — seçenekler §2.6; karar ADR bekliyor"
  - "Q7 BC/DB ilişkisi — seçenekler §2.7; karar ADR bekliyor"
  - "Q8 swagger/PSR-7/PSR-15 otomasyonu — seçenekler §2.8; karar ADR bekliyor"
  - "Q9 servisler arası taşıyıcı — seçenekler §2.9; karar ADR bekliyor"
group: kanit
---

# K012-services — Web Araştırması (kök · Q1–Q9)

> Bu dosya K012 **kök seviyesinde** Q1–Q9 için **web kanıtlı seçenek matrisleri** üretir.
> **Karar vermez** — seçenek sunar; karar ADR'ye bırakılır (services-master.md §7/5).
> Doğrulama: **2026-10-16** (HTTP HEAD, 15/15 status 200) · envanter kesişimi kabul setinde **0** · C4 kardeş byte-tekilliği **0** (§3.4).
> Biçim precedansı: K008-domain/playback/playback-web-arastirma.md (W-x numaralama + kaynak tablosu).

## §1 KAYNAK TABLOSU

| # | URL | Hangi Q'yu besler | Durum |
|---|-----|-------------------|-------|
| W-1 | https://docs.anthropic.com/en/api/overview | Q1 (YZ API/serving yüzeyi) | HTTP 200 |
| W-2 | https://en.wikipedia.org/wiki/API_gateway | Q1 (gateway deseni), Q9 (HTTP taşıma) | HTTP 200 |
| W-3 | https://en.wikipedia.org/wiki/Session_(computer_science) | Q2 (durum/oturum sahipliği) | HTTP 200 |
| W-4 | *(pointer → playback-service §1 W-2 — Media_player disambiguation olduğu için orada RED edildi)* | Q2 (oynatıcı = durum sahibi kavramı) — kanıt kardeş dosyada | kardeş pointer |
| W-5 | https://en.wikipedia.org/wiki/Audio_signal_processing | Q3 (sinyal işleme = ayrı uzmanlık alanı) | HTTP 200 |
| W-6 | https://en.wikipedia.org/wiki/Digital_signal_processor | Q3 (sinyal motoru = ayrı katman) | HTTP 200 |
| W-7 | https://en.wikipedia.org/wiki/OpenAPI_Specification | Q4 (şema-öncelikli sözleşme), Q8 (swagger otomasyonu) | HTTP 200 |
| W-8 | https://en.wikipedia.org/wiki/Protocol_Buffers | Q4 (kanonik IDL), Q9 (gRPC sözleşmesi) | HTTP 200 |
| W-9 | *(pointer → background-jobs §1 W-5)* | Q5 (Redis kuyruk), Q9 (mesaj taşıyıcı) — URL kardeş dosyada | kardeş pointer |
| W-10 | https://www.rabbitmq.com/docs | Q5 (RabbitMQ broker), Q9 (mesaj taşıyıcı) | HTTP 200 |
| W-11 | *(pointer → background-jobs §1 W-4)* | Q5 (framework kurye seçeneği) — URL kardeş dosyada | kardeş pointer |
| W-12 | *(pointer → background-jobs §1 W-3)* | Q5 (framework messenger seçeneği) — URL kardeş dosyada | kardeş pointer |
| W-13 | https://www.rfc-editor.org/rfc/rfc8628 | Q6 (OAuth 2.0 device authorization grant) | HTTP 200 |
| W-14 | https://www.rfc-editor.org/rfc/rfc7591 | Q6 (dynamic client registration) | HTTP 200 |
| W-15 | https://en.wikipedia.org/wiki/Microservices | Q7 (BC/DB ilişkisi pratiği), Q5, Q9 (süreç sınırı) | HTTP 200 |

Kabul kriteri: (a) birincil/resmi kaynak, (b) iki envanter dosyasında kesişim 0, (c) HTTP HEAD 200.
Üçü de sağlanmayan aday §3'te reddedilenler olarak kayıtlıdır.

## §2 SEÇENEK MATRİLERİ (Q1–Q9)

### §2.1 Q1 — ai-service bağlılığı: K006-yapay-zeka mı K016-AI mı?

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: ai-service = YZ **serving/sunum katmanı** — K012'de servis olarak yaşar; model/agent altyapısı K006/K016'da kalır | W-1 (YZ API'sinin serving yüzeyi resmi dokümanda ayrı katman olarak tanımlanır) | servis sınırı net; master §1 "iş yapan sırtı" rolüyle uyumlu | YZ platformu sahipliği (K006 vs K016) yine açık kalır | ai-service/index.md · services-master.md §7/1 |
| B: ai-service = **K006-yapay-zeka'nın** K012'deki sunum yüzü (domain K006, servis K012) | W-1 | sahiplik tekilleşir; K006 vault içeriği mevcut | K006↔K016 ayrımı netleşmeden bağlama erken olabilir | ai-service · K006-yapay-zeka |
| C: ai-service = **K016-AI platformunun** K012'deki sunum yüzü | W-1 | "AI platform" tanımı K016'daysa doğal hizalanma | K016 kapsamı registry §6'da ayrıca doğrulanmalı | ai-service · K016-AI |
| D: **AI gateway deseni** — birden çok YZ sağlayıcısı/modeli tek arayüzde toplayan katman; ai-service bir gateway olarak konumlanır | W-2 (API gateway kavramı — sağlayıcı-özel API'yi tek kontratta toplama) | sağlayıcı bağımsızlığı; service-contracts ile uyum | tek sağlayıcıda ek katman; K014 gateway ile sınır tanımlanmalı | ai-service · service-contracts · K014-network |

öneri-seviyesi: A/D servis-katmanı vurgusu, B/C domain-sahipliği vurgusu taşır; tüm seçenekler tek bir vault gerçeğine (K006↔K016 ayrımı) bağlıdır — ADR bu ayrımdan önce yazılamaz.

### §2.2 Q2 — playback-service ↔ K015/playback sahipliği (oynatma durumu kimin?)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **oynatma durumu playback-service'indir** (sıra, konum, komut durumu) — K015 yalnız medya dosyası/metadata sahibidir | W-3 (sunucu tarafı oturum/durum sahipliği kavramı) | komut-durum döngüsü servis içinde kalır; master §3/3 kapsamıyla birebir | durumun kalıcılığı K007'ye bağımlı | playback-service/index.md · K015-media |
| B: **oynatma durumu medya kitaplığının (K015/playback) parçasıdır** — playback-service yalnız komut yönlendiricisidir | W-4 (oynatıcı yazılımı — durum oynatıcıda yaşar) | durum + medya tek yerde; K008 playback odak ile hizalı | master §3/3 kapsamı ("sıra, konum, durum") daralmış olur | K015-media · playback-service |
| C: **ayrık sahiplik + tek yönlü akış** — K012 durumu *tutar* (command/state servisi), K015 medya *bilgisini* tutar; veri K015→K012 tek yönlü akar | W-3 + W-4 (iki kavramın ayrıştırılması) | iki K'nin otonomisi korunur; master §5.3 DAG iddiası ile uyumlu | senkron sorumluluğu (kim kimi günceller) ADR'de tanımlanmalı | playback-service · K015-media · services-bagimliliklar.md |
| D: **oynatma durumu K008-domain'dedir** (iş kuralı), servisler yalnızca yazar/okur | W-3 | kural ile durum ayrımı net | K008'in "kural üretmez, servis uygular" ilkesiyle gerilim | K008-domain · playback-service |

öneri-seviyesi: C seçeneği vault DAG iddiası (§5.3) ile en uyumlu görünen seçenektir, ancak bu bir **gözlemdir, karar değildir** — sınır ADR ile sabitlenmezse her iki düğümde de kapsam sürüklenmesi riski kalır.

### §2.3 Q3 — audio-service ↔ K003-audio sınırı (servis mi, sinyal motoru mu?)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: audio-service bir **servistir** — sesle ilgili iş taleplerini (dönüştürme isteği, format sorgusu, ölçüm işi) karşılar; sinyal işlemeyi K003'e delege eder | W-5 (ses sinyali işleme ayrı bir uzmanlık/alan olarak tanımlanır) | K012 tanımıyla ("iş yapan servis") uyumlu; gerçek-zamanlı DSP servis dışına çıkar | K003 ile arayüz biçimi (süreç içi mi uzak mı) Q9'ya bağlı | audio-service/index.md · K003-audio |
| B: audio-service = **sinyal motorunun servis sarmalayıcısı** — K003 motorunu çağıran ince adaptör servis | W-6 (DSP ayrı bir işleyici katmanı olarak var) | motor tek kaynak; servis yalnız adapte eder | süreç-içi konuşulursa "servis" iddiası zayıflar (Q9 bağlantılı) | audio-service · K003-audio |
| C: **sınır yok sayılır** — ses işleri K003 içinde tek süreçte yaşar; K012'de audio-service bulunmaz | W-5 | gereksiz ağ/atma maliyeti yok | v2 §2.12 çocuk listesi (10/10 birebir kuralı) ile çelişir — çocuk silinemez | services-master.md §3 · v2 §2.12 |
| D: **iki katmanlı sınır** — audio-service = iş/dönüşüm servisi (ağ), K003 = motor (kitaplık); ikisi ayrı dağıtılır | W-5 + W-6 | sorumluluk ayrımı en net | dağınık süreç maliyeti; tek K012 süreci varsayımıyla gerilim | audio-service · K003 · K014 |

öneri-seviyesi: A ve D aynı fikri farklı yoğunlukta söyler; C vault sabitiyle (10 çocuk) çeliştiği için yalnız "silinmezlik" istisnasıyla gündeme gelebilir — karar ADR'ye aittir.

### §2.4 Q4 — service-contracts kapsamı (evrensel spec mi, K012'ye özel mi?)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **evrensel (hub-and-spoke) spec** — tüm K'lar için tek merkezî Playlist/Track sözleşmesi (master §4/1 iddiası) | W-7 (şema-öncelikli API sözleşmesi yaklaşımı) | tek kanonik model; K'lar arası tek dil | tüm K'lar tek sözleşmeye bağlanır; değişiklik maliyeti yüksek | service-contracts/index.md · services-master.md §4/1 |
| B: **K012'ye özel sözleşmeler** — yalnız bu odanın servisleri arası DTO/mesaj biçimi | W-7 | kapsam küçük ve hızlı evrim eder | K'lar arası paylaşılacak model için ikinci bir sözleşme yüzeyi doğar | service-contracts |
| C: **kanonik model IDL (Protobuf)** ile tanımlanır; HTTP yüzeyi JSON'a çevrilir (master §4/2 iddiası) | W-8 (Protobuf = dil/taşıma bağımsız şema/IDL) | sözleşme dili/taşımadan bağımsız; sürümleme güçlü | PHP tarafında ek üretim/adım; repo'da protobuf kanıtı YOK (S5) | service-contracts |
| D: **senkron + asenkron ikili spec** — REST için OpenAPI, olaylar için ayrı olay-sozleşmesi (AsyncAPI benzeri) | W-7 · ⚠️ AsyncAPI URL'leri bu turda HTTP 404 (§3.2) → kaynak doğrulanamadı | senkron/asenkron ayrımı net; background-jobs ile hizalı | doğrulanmamış kısım VERIFICATION REQUIRED statüsünde kalır | service-contracts · background-jobs |

öneri-seviyesi: A/C birbirini besler (kanonik model + şema), B/D kapsamı daraltır; vault'un tek kesin kanıtı (repo'da DTO vaadi, kısmi S1) hiçbirini elemine etmez — karar ADR.

### §2.5 Q5 — background-jobs iş motoru (PHP kurye seçenekleri)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **DB tabanlı kuyruk + cron** — iş tablosu + periyodik tarayıcı; ek altyapı yok (cron için resmi URL bu turda doğrulanamadı ⚠️) | W-15 (mikroservis pratiğinde bağımlı servis/kuyruk tartışması) | yeni bağımlılık yok; K007 MySQL ile doğal uyum | yüksek yükte DB kilit/performance baskısı; visibility-timeout elde yazılır | background-jobs/index.md · K007-data |
| B: **Redis kuyruk** (List veri tipi) | W-9 (Redis listelerin kuyruk olarak kullanımı — resmi doküman) | hafif, hızlı; PHP erişimi yaygın | ek süreç/altyapı; dayanıklılık yapılandırması gerekir | background-jobs · K007/K014 |
| C: **RabbitMQ (AMQP broker)** | W-10 (resmi doküman) | ack/retry/DLQ hazır; ola yayını güçlü | en ağır operasyon yükü; yönetim arayüzü ek yük | background-jobs · K014-network |
| D: **framework kurye sarmalayıcısı** — Laravel queue / Symfony Messenger (birden çok taşııcıyı tek API ile soyutlar) | W-11 (Laravel queues) · W-12 (Symfony Messenger) | taşııcı soyutlanır; PHP ekosistemi olgun | framework bağımlılığı, AGENTS §18/8 "framework = bağımlılık artışı" ilkesiyle gerilim yaratır | background-jobs |

öneri-seviyesi: repo S5 kanıtı (motor kaynağı YOK — dogrulama §7/3) hiçbir seçeneği dışlamaz; operasyonel olgunluk (C) ile hafiflik (A/B) arasındaki denge K018-devops koşullarına bağlıdır — karar ADR.

### §2.6 Q6 — device-identity sahipliği (K010-identitiy vs device-service)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **device-identity K010-identitiy'nindir** — cihaz kimlik kanıtı/token'ı K010 üretir; device-service yalnız kayıt+durum işini yapar | W-13 (RFC 8628 device authorization grant — cihaz-özel kimlik akışı standarttır, kimlik katmanında tanımlanır) | kimlik tek merkezde; standart akış uyumu | device-service'in "yetki" kapsamı (master §3/6) K010'a kayar | device-service/index.md · K010-identitiy |
| B: **device-identity device-service'indir** — cihaz kaydı + cihaz-özel kimlik servisin kendi dikey alanında | W-13 (aynı standart, farklı sahiplik yorumu) | dikey sahiplik; cihaz yaşam döngüsü servisle ölçeklenir | kimlik ikiye bölünür (user-identity K010 · device-identity K012) — tutarlılık zor | device-service/index.md · K010 |
| C: **DCR benzeri dinamik kayıt** — cihazlar dinamik client registration ile kayıt olur; sahiplik standart protokole bağlanır | W-14 (RFC 7591 dynamic client registration) | standart-uyumlu; dış ekosistem uyumu | CoreMusic cihaz modeli OAuth-client'a birebir oturmayabilir → uyarlama gerekir | device-service · K010 · K013-integraion |
| D: **bölünmüş sahiplik** — kimlik kanıtı K010'da, cihaz envanteri/durumu device-service'te; sınır ADR ile sabitlenir | W-13 + W-14 | net ayrım: "kim kim" (K010) vs "cihaz ne" (K012) | sınır ADR'siz sürüklenir | K010-identitiy · device-service |

öneri-seviyesi: A/D kimlik-merkezli, B/D cihaz-dikey dengesi kurar; K010-identitiy yazım hatası korunmalıdır (dogrulama §4/Q6) — karar ADR.

### §2.7 Q7 — BC/DB ilişkisi (database-per-service vs paylaşımlı DB)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **database-per-service** — her BC'nin kendi DB'si | W-15 (mikroservis olgunluğunda en yaygın benimsenen öneri) | BC otonomisi en güçlü biçimde; bağımsız ölçekleme | repo'daki 18 BCNF MySQL şemasıyla birebir uyumsuzluk riski; servisler arası join kaybı | K007-data · services-master.md §2.2 |
| B: **paylaşımlı tek DB** (şema bölünmeden) | W-15 (aynı kaynakta tartışılan alternatif) | mevcut 18 şemayla uyum; geçiş maliyeti düşük | servisler arası gizli bağımlılık; otonomi iddiası zayıf | K007-data |
| C: **paylaşımlı DB + katı şema-sahipliği kuralı** — her tablonun tek BC sahibi var; başka BC okuma bile sınır üzerinden | W-15 (otonomi ile mevcudiyet arası denge pratiği) | otonomiye en yakın mevcut-uyumlu denge | kural disiplinli denetlenmezse B'ye kayar | K007-data · service-contracts |
| D: **hybrid** — servis başına DB (yazma), raporlama/analiz için ayrı okuma kopyası | W-15 | ayrık yazma + tek okuma yüzeyi | replikasyon/operasyon yükü K007/K018-devops'a biner | K007-data · K018-devops |

öneri-seviyesi: repo gerçeği (tek şema, 18 BCNF) B/C lehine bir "başlangıç noktası" gösterir; A hedef mimari olarak kalabilir — geçiş sırası ve bugünkü seçim ADR konusu.

### §2.8 Q8 — swagger / PSR-7 / PSR-15 otomasyonu

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **OpenAPI (swagger) tek kaynak** — şemadan kod/DTO üretimi veya şema-ilk yazım | W-7 | sözleşme-öncelikli; X-API-Version sürümlemesi (S1) ile hizalı | repo'da OpenAPI dosyası kanıtı YOK (S5) | service-contracts · shared/src/Api |
| B: **PSR-7/15/18 el-yazımı** — mesaj/arayüz + middleware + HTTP istemcisi elle uygulanır (repo'da zaten PSR-15 middleware kanıtı var: AGENTS §25.2, Middleware ×11) | ⚠️ php-fig psr-7/15/18/17/10 URL'leri envanter kesişimi nedeniyle RED (§3.1); php-fig.org/psr/ dizini 200 doğrulandı ama 15 kontenjanı dışında (§3.3) → iddia VERIFICATION REQUIRED | framework'süz; mevcut shared/src/Middleware ile birebir | otomasyon yok; şema dokümantasyonu elle | shared/src/Api/Middleware · service-contracts |
| C: **hibrit** — mevcut PSR el-yazımı korunur; şema koddan/annotasyondan üretilir (üretim aracının adı kanıt yok, yazılmaz) | W-7 + php-fig.org/psr/ (§3.3) | mevcut kod korunur, şema eklenir | otomasyon aracının seçimi ayrı ADR konusu | shared/src/Api · service-contracts |
| D: **otomasyon yok** — şema vault'ta (markdown) yaşar; kod şemadan türetilmez | W-7'nin tersi iddia (şema-öncelikli akışın reddi) | sıfır araç bağımlılığı | şema-kod sürüklenmesi (drift) riski | service-contracts |

öneri-seviyesi: B'nin repo kanıtı (PSR-15 middleware ×11) en güçlü mevcut-durum işaretidir; A/C şimdilik TASARIM İDDİASIdır — karar ADR.

### §2.9 Q9 — servisler arası gerçek taşıyıcı (HTTP vs gRPC vs kuyruk vs tek-süreç)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **HTTP/REST** — servisler HTTP üzerinden çağırır (aynı veya farklı süreç) | W-2 (API gateway + HTTP taşıma) · §4 pointer: RFC 9110/9112 (dogrulama §5) | repo S1 kanıtı zaten REST yönlendirme (.htaccess:26 · web.config:14); araç olgunluğu | senkron kırılganlığı; ağ gecikmesi | services-master.md §22.2 · K014-network |
| B: **gRPC (Protobuf IDL)** — tip-güvenli süreçler arası çağrı | W-8 (Protobuf = gRPC sözleşmesi) | hızlı, sözleşmeli; Q4-C ile hizalı | PHP ekosistemi + mevcut HTTP katmanına köprü gerekir; repo kanıtı YOK (S5) | service-contracts · K014 |
| C: **mesaj kuyruğu (ola/yayın)** — senkron olmayan işler kuyrukla taşınır | W-9 · W-10 (Q5 kaynaklarıyla ortak altyapı) | gevşek bağlama; background-jobs ile ortak zemin | senkron sorgu-yanıt için uygun değil; izleme zor | background-jobs · K014 |
| D: **tek-süreç (in-process)** — servisler süreç içi nesne çağrılarıdır | W-15 (mikroservis tanımı ile tezat) + repo S1 (ServiceRegistry bellek-içi diziler: :19, :38) | sıfır ağ maliyeti; **mevcut repo durumuyla birebir** | "servis" kavramı süreç-içi sınıfa iner; ölçekleme tek süreçle sınırlı | services-master.md §10.2 · K012 tümü |

öneri-seviyesi: tek kesin S1 kanıtı D lehinedir (bellek-içi defter); A mevcut HTTP altyapısıyla uyumlu bir sonraki adımdır; B/C için repo kanıtı yoktur — taşıyıcı kararı Q4 sözleşmesinden ayrı yazılamaz, ADR ortak yürümelidir.

## §3 REDDEDİLEN ADAYLAR

### §3.1 Kesişim > 0 (envanter kuralı ihlali — aday REDDEDİLDİ)

| Aday URL | Kesişim kanıtı | Gerekçe |
|----------|----------------|---------|
| https://www.php-fig.org/psr/psr-7/ | url-envanter.txt:2430 · url-inventory.txt:2479 | envanterde mevcut — başka K dosyalarının hakkı |
| https://www.php-fig.org/psr/psr-15/ | url-envanter.txt:2423 · url-inventory.txt:2472 | aynı |
| https://www.php-fig.org/psr/psr-18/ | url-envanter.txt:2426 · url-inventory.txt:2475 | aynı |
| https://www.php-fig.org/psr/psr-17/ | url-envanter.txt:2425 · url-inventory.txt:2474 | aynı |
| https://www.php-fig.org/psr/psr-10/ | url-envanter.txt:2418 · url-inventory.txt:2467 | aynı |

### §3.2 HTTP ≠ 200 (HEAD doğrulaması başarısız)

| Aday URL | Amaç | Sonuç |
|----------|------|-------|
| https://12factor.net/stateless | Q2/Q7 — stateless fikri | HTTP 404 |
| https://12factor.net/stateless/ | aynı (sonlu eğik çizgi varyantı) | HTTP 404 |
| https://www.rabbitmq.com/docs/tutorials | Q5 — RabbitMQ tutorial yüzeyi | HTTP 404 |
| https://www.rabbitmq.com/tutorials/tutorials/tutorial-one-php | Q5 — PHP tutorial varyantı | HTTP 404 |
| https://en.wikipedia.org/wiki/AsyncAPI | Q4/D — AsyncAPI kaynağı | HTTP 404 |
| https://en.wikipedia.org/wiki/AsyncAPI_Specification | Q4/D varyantı | HTTP 404 |

> Not: 12factor.net alt sayfa 404'leri services-dogrulama.md §10'daki 2026-10-13 başarısızlıklarıyla **aynı desendir** (yalnız kök sayfa 200 döner). Bu turda rabbitmq.com/docs 200 döndü ve W-10 olarak kabul edildi.

### §3.3 Doğrulanmış ama bu tur matris kontenjanı (15 URL) dışında bırakılan adaylar

| Aday URL | HTTP | Ayrılmama nedeni |
|----------|------|------------------|
| https://en.wikipedia.org/wiki/Swagger_(software) | 200 | Q8'de W-7 karşıladı |
| https://www.php-fig.org/psr/ | 200 | Q8-B kaynak notu §3.1 ile karşılandı |
| https://www.php-fig.org/ | 200 | yedek |
| https://www.php.net/ | 200 | yedek |
| https://en.wikipedia.org/wiki/Cron | 200 | Q5-A cron kolu not ile geçti |
| https://en.wikipedia.org/wiki/Representational_state_transfer | 200 | Q9-A W-2 + RFC pointer ile karşılandı |
| https://12factor.net/backing-services | 200 | Q7-A W-15 karşıladı |

Bu adaylar ilerideki turlarda serbesttir; bu dosyada "HTTP 200" kanıtı olarak kullanılmazlar.

### §3.4 C4 kardeş dosya çakışması — çözülen 4 satır (2026-10-16 toplu tarama)

> K012 recursive tarama (132 benzersiz URL) 3 tam çakışma + 1 near-dup buldu. Kural: **URL kardeş (derin içerik) dosyada kalır; kökte W-x numarası korunarak pointer satırına çevrilir** (K008 C4 precedantı · §4 pointer tablosu ile aynı biçim). Matrislerdeki W-x referansları değişmedi.

| W-x | Çakışan aday (byte — tam metin kardeş dosyada) | Sahip dosya | Çözüm |
|-----|-----------|-------------|-------|
| W-4 | wikipedia `Media_player` | playback-service §3 RED (disambiguation → W-2'ye indirgendi) | kök satır pointer; kanıt playback §1 W-2'de |
| W-9 | redis.io `data-types/lists/` | background-jobs §1 W-5 | kök satır pointer |
| W-11 | laravel.com `docs/queues` ≈ `docs/12.x/queues` (near-dup) | background-jobs §1 W-4 (12.x biçimi) | kök satır pointer; kök adayı §3.3'te değil, doğrudan kardeş |
| W-12 | symfony.com `messenger.html` | background-jobs §1 W-3 | kök satır pointer |

> Not: `12factor.net/backing-services` (bu dosya §3.3) ile `12factor.net/backingservices` (bg §1 W-9) **farklı slug'lardır** — bg §3'teki 404 kaydı doğru; byte-tekilliği ihlali sayılmaz.

## §4 KAPSAM DIŞI — services-dogrulama.md §5'e POINTER (15 URL tekrar yazılmaz)

| Konu | Nereye |
|------|--------|
| HTTP semantiği/mesaj formatı (RFC 9110 · 9112 · 7230), TLS 1.3 (8446), Alt-Svc (7838), HTTP/2 (7540 · 8441), ek status kodları (6585), SMTP (5321), datatracker sürümü, IETF kök | services-dogrulama.md §5 (15 URL — HTTP 200, 2026-10-13) |
| W3C WebSockets · Media Capabilities, CNCF, 12factor.net kök | aynı §5 — bu dosyada tekrar yazılmaz |
| Playback odaklı MDN/Wikipedia kaynakları (AudioContext, HTMLMediaElement, Media Session, loudness, buffer-underrun vb.) | K008-domain/playback/playback-web-arastirma.md (K008 C4 havuzu) |
| HLS/akış protokolleri, ses formatı kodlama | K008 playlist/media web-arastirma dosyaları (K008 §3) |
| grpc.io · spec.openapis.org gibi izin listesi dışındaki kurumlar | bu görevin kaynak protokolü dışında — kullanılmadı |

## §5 DEĞİŞİKLİK KAYDI

| Tarih | Değişiklik |
|-------|------------|
| 2026-10-16 | İlk üretim (v1.0.0) — 15 kabul URL (W-1…W-15, hepsi HTTP HEAD 200); Q1–Q9 için 2–4 seçenekli 9 matris; 11 aday reddedildi (§3.1 kesişim 5 · §3.2 HTTP≠200 6); 7 doğrulanmış aday kontenjan dışı (§3.3). Karar verilmedi — tüm seçenekler ADR bekliyor. Index güncellemesi bu dosyanın kapsamı değildir (görev sahibine aittir). |
| 2026-10-16 | v1.1.0 — C4 toplu tarama: W-4 · W-9 · W-11 · W-12 kardeş dosya çakışması → pointer satırına çevrildi (§3.4); kabul seti 11 URL + 4 kardeş pointer; matris W-x referansları korundu. |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode