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

### §2.1 Q1 — ai-service bağlılığı: K006-yapay-zeka mı K015-AI mı?

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: ai-service = YZ **serving/sunum katmanı** — K012'de servis olarak yaşar; model/agent altyapısı K006/K016'da kalır | W-1 (YZ API'sinin serving yüzeyi resmi dokümanda ayrı katman olarak tanımlanır) | servis sınırı net; master §1 "iş yapan sırtı" rolüyle uyumlu | YZ platformu sahipliği (K006 vs K016) yine açık kalır | ai-service/index.md · services-master.md §7/1 |
| B: ai-service = **K006-yapay-zeka'nın** K012'deki sunum yüzü (domain K006, servis K012) | W-1 | sahiplik tekilleşir; K006 vault içeriği mevcut | K006↔K016 ayrımı netleşmeden bağlama erken olabilir | ai-service · K006-yapay-zeka |
| C: ai-service = **K015-AI platformunun** K012'deki sunum yüzü | W-1 | "AI platform" tanımı K016'daysa doğal hizalanma | K016 kapsamı registry §6'da ayrıca doğrulanmalı | ai-service · K015-AI |
| D: **AI gateway deseni** — birden çok YZ sağlayıcısı/modeli tek arayüzde toplayan katman; ai-service bir gateway olarak konumlanır | W-2 (API gateway kavramı — sağlayıcı-özel API'yi tek kontratta toplama) | sağlayıcı bağımsızlığı; service-contracts ile uyum | tek sağlayıcıda ek katman; K014 gateway ile sınır tanımlanmalı | ai-service · service-contracts · K010-network |

öneri-seviyesi: A/D servis-katmanı vurgusu, B/C domain-sahipliği vurgusu taşır; tüm seçenekler tek bir vault gerçeğine (K006↔K016 ayrımı) bağlıdır — ADR bu ayrımdan önce yazılamaz.

### §2.2 Q2 — playback-service ↔ K015/playback sahipliği (oynatma durumu kimin?)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **oynatma durumu playback-service'indir** (sıra, konum, komut durumu) — K015 yalnız medya dosyası/metadata sahibidir | W-3 (sunucu tarafı oturum/durum sahipliği kavramı) | komut-durum döngüsü servis içinde kalır; master §3/3 kapsamıyla birebir | durumun kalıcılığı K007'ye bağımlı | playback-service/index.md · K014-media |
| B: **oynatma durumu medya kitaplığının (K015/playback) parçasıdır** — playback-service yalnız komut yönlendiricisidir | W-4 (oynatıcı yazılımı — durum oynatıcıda yaşar) | durum + medya tek yerde; K008 playback odak ile hizalı | master §3/3 kapsamı ("sıra, konum, durum") daralmış olur | K014-media · playback-service |
| C: **ayrık sahiplik + tek yönlü akış** — K012 durumu *tutar* (command/state servisi), K015 medya *bilgisini* tutar; veri K015→K012 tek yönlü akar | W-3 + W-4 (iki kavramın ayrıştırılması) | iki K'nin otonomisi korunur; master §5.3 DAG iddiası ile uyumlu | senkron sorumluluğu (kim kimi günceller) ADR'de tanımlanmalı | playback-service · K014-media · services-bagimliliklar.md |
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
| C: **RabbitMQ (AMQP broker)** | W-10 (resmi doküman) | ack/retry/DLQ hazır; ola yayını güçlü | en ağır operasyon yükü; yönetim arayüzü ek yük | background-jobs · K010-network |
| D: **framework kurye sarmalayıcısı** — Laravel queue / Symfony Messenger (birden çok taşııcıyı tek API ile soyutlar) | W-11 (Laravel queues) · W-12 (Symfony Messenger) | taşııcı soyutlanır; PHP ekosistemi olgun | framework bağımlılığı, AGENTS §18/8 "framework = bağımlılık artışı" ilkesiyle gerilim yaratır | background-jobs |

öneri-seviyesi: repo S5 kanıtı (motor kaynağı YOK — dogrulama §7/3) hiçbir seçeneği dışlamaz; operasyonel olgunluk (C) ile hafiflik (A/B) arasındaki denge K018-devops koşullarına bağlıdır — karar ADR.

### §2.6 Q6 — device-identity sahipliği (K0100-identitiy vs device-service)

| Seçenek | Kaynak (URL W-x) | Artı | Eksi | Etkilenen dosya |
|---------|------------------|------|------|-----------------|
| A: **device-identity K0100-identitiy'nindir** — cihaz kimlik kanıtı/token'ı K010 üretir; device-service yalnız kayıt+durum işini yapar | W-13 (RFC 8628 device authorization grant — cihaz-özel kimlik akışı standarttır, kimlik katmanında tanımlanır) | kimlik tek merkezde; standart akış uyumu | device-service'in "yetki" kapsamı (master §3/6) K010'a kayar | device-service/index.md · K0100-identitiy |
| B: **device-identity device-service'indir** — cihaz kaydı + cihaz-özel kimlik servisin kendi dikey alanında | W-13 (aynı standart, farklı sahiplik yorumu) | dikey sahiplik; cihaz yaşam döngüsü servisle ölçeklenir | kimlik ikiye bölünür (user-identity K010 · device-identity K012) — tutarlılık zor | device-service/index.md · K010 |
| C: **DCR benzeri dinamik kayıt** — cihazlar dinamik client registration ile kayıt olur; sahiplik standart protokole bağlanır | W-14 (RFC 7591 dynamic client registration) | standart-uyumlu; dış ekosistem uyumu | CoreMusic cihaz modeli OAuth-client'a birebir oturmayabilir → uyarlama gerekir | device-service · K010 · K013-integraion |
| D: **bölünmüş sahiplik** — kimlik kanıtı K010'da, cihaz envanteri/durumu device-service'te; sınır ADR ile sabitlenir | W-13 + W-14 | net ayrım: "kim kim" (K010) vs "cihaz ne" (K012) | sınır ADR'siz sürüklenir | K0100-identitiy · device-service |

öneri-seviyesi: A/D kimlik-merkezli, B/D cihaz-dikey dengesi kurar; K0100-identitiy yazım hatası korunmalıdır (dogrulama §4/Q6) — karar ADR.

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
| A: **HTTP/REST** — servisler HTTP üzerinden çağırır (aynı veya farklı süreç) | W-2 (API gateway + HTTP taşıma) · §4 pointer: RFC 9110/9112 (dogrulama §5) | repo S1 kanıtı zaten REST yönlendirme (.htaccess:26 · web.config:14); araç olgunluğu | senkron kırılganlığı; ağ gecikmesi | services-master.md §22.2 · K010-network |
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

## §6 TUR-2 KAYNAK EKİ (2026-10-18)

> **Append-only ek** — §1–§5'e dokunulmamıştır; §5 DEĞİŞİKLİK KAYDI satırları korunmuştur.
> Her aday URL Code Mode `fetch` (GET · redirect follow) ile **2026-10-18**'de doğrulanmıştır.
> §1'deki 11 URL HTTP **HEAD** ile (2026-10-16) doğrulanmıştı; bu ekteki 7 URL HTTP **GET** ile
> doğrulanmıştır — iki yöntem de 200 durumunu doğrular; yöntem farkı bilinçli ve şeffaftır.
> **Benzersizlik:** adaylar K012 recursive tarama (50 dosya) + `services-dogrulama.md` §5 (15 URL)
> + kardeş 4 web-arastirma dosyası üzerinden elendi; kesişim **0** (§6.8 RED tablosu gerekçeli).

### §6.1 KAYNAK EKİ TABLOSU (T2-1 … T2-7)

| # | URL | Ne kanıtlar (iddia) | Etkilediği Q | HTTP (2026-10-18) |
|---|-----|---------------------|--------------|--------------------|
| T2-1 | https://martinfowler.com/bliki/CQRS.html | CQRS: komut (yazma) ve sorgu (okuma) modelinin ayrılması — tek yazma modeli, birden çok okuma modeli | Q1, Q4 | GET 200 ✅ |
| T2-2 | https://learn.microsoft.com/en-us/azure/architecture/patterns/cQRS | Kurumsal CQRS uygulaması: okuma/yazma modellerinin ayrı depolanması, senkronizasyon ve tutarlılık trade-off'ları | Q1, Q4, Q7 | GET 200 ✅ |
| T2-3 | https://microservices.io/patterns/saga.html | Saga: çok servisli dağıtık işlemi adım adım (yerel transaction + telafi adımı) yürütme deseni | Q7, Q9 | GET 200 ✅ |
| T2-4 | https://microservices.io/patterns/data/api-composition.html | API Composition: sorgu tarafında birden çok servis yanıtını birleştirme deseni (okuma modeli birleştirme) | Q7, Q1 | GET 200 ✅ |
| T2-5 | https://eaaCatalog.com/dataTransferObject.html | DTO: Enterprise Application Architecture kataloğu — veri transferinin sözleşme nesnesiyle taşınması | Q4 | GET 200 ✅ |
| T2-6 | https://learn.microsoft.com/en-us/azure/architecture/patterns/retry | Retry: hatalı servis çağrısında geri çekilme (backoff) ve circuit-breaker ile kırma — dayanıklılık kalıbı | Q9 | GET 200 ✅ |
| T2-7 | https://microservices.io/patterns/database-per-service.html | Database-per-Service: her servisin kendi veritabanı şemasına sahip olması — BC/DB sınırının mikroservis karşılığı | Q7, Q1 | GET 200 ✅ |

> §1'deki 11 URL + §6.1'deki 7 URL = **18 kanıt URL** (4 kardeş pointer hariç). T2-1…T2-7 bu
> dosyada yaşar; kardeş dosyalara **tekrar yazılmaz** (C4 byte-tekilliği korunur — §6.9).

### §6.2 T2-1 — CQRS bliki (martinfowler.com)

**Kanıt:** CQRS (Command Query Responsibility Segregation) bliki, yazma modeli (komutlar) ile
okuma modeli (sorgular) kavramlarını tek bir uygulama içinde ayrıştırır. Komutlar durumu
değiştirir; sorgular durumu yalnız okur ve yazma modelini bozmaz. Blkinin kendi ifadesiyle:
"okuma ve yazma için farklı nesne modelleri kullanmak" bir modelleme seçimi olarak sunulur.

**Q etkisi — Q1 (servis sınırları):** CoreMusic servis katmanı (services-master §7/1) komut/sorgu
ayırmayı bilinçli bir seçenek olarak taşımalı: `playback-service` yazma akışları (now-playing
güncelleme, sıra değişimi) ile okuma akışları (şu anki durum sorgusu) aynı modeli dayatmaz.
**Q etkisi — Q4 (service contracts):** Komut ve sorgu sözleşmeleri ayrı DTO yüzeyleriyle
sabitlenir (T2-5 DTO kanıtı ile birleşir).

**Kardeş bağ:** §1 W-7 (OpenAPI şema-öncelikli sözleşme) ile birlikte okunur — CQRS burada
şema ayrımı olarak, W-7 burada şema biçimi olarak kanıt taşır.

**Doğrulama:** GET → 200 (2026-10-18). Blkinin varlığı ve CQRS tanımı doğrulandı; sayfa içeriği
yalnızca iddia düzeyinde özetlendi (derin tarama bu görevin kapsamı dışı).

### §6.3 T2-2 — Kurumsal CQRS (learn.microsoft.com)

**Kanıt:** Azure Architecture Center'ın CQRS deseni sayfası, CQRS'nin pratik sonucunu açıkça
yazar: okuma ve yazma modelleri **ayrı depolanabilir**, bu durum **senkronizasyon zorunluluğu**
doğurur ve "eventual consistency" (gecikmeli tutarlılık) bir trade-off olarak kabul edilir.
Ayrıca CQRS'nin tek başına bir mimari olmadığını, event sourcing veya basit CRUD ile
birleştirilebileceğini belirtir.

**Q etkisi — Q7 (BC/DB ilişkisi):** CoreMusic'te BC↔DB eşlemesi (services-master §7/7) tek DB'ye
çoklu BC mi, yoksa DB-per-Service mi sorusunu CQRS ile birlikte düşünür: okuma modeli
ayrılaşabilir (ör. materialized view / read replica), yazma modeli BC aggregate'inde kalır.
**Q etkisi — Q1, Q4:** T2-1 ile aynı eksen; bu sayfa ek olarak "senkronizasyon bedeli"ni
kanıtlar — CQRS kararı verilirken maliyet kalemi olarak yazılmalıdır.

**Doğrulama:** GET → 200 (2026-10-18). Kurumsal perspektif + trade-off iddiaları doğrulandı.

### §6.4 T2-3 — Saga (microservices.io)

**Kanıt:** Saga deseni, çok servisli dağıtık bir işlemi **yerel transaction'lar zinciri** olarak
yönetir; her adım kendi servisinde transaction ile tamamlanır; başarısızlık durumunda
**telafi adımları** (compensating transactions) önceki adımları geri alır. İki yürütme
koordinasyonu tanımlanır: merkezi orkestratör (orchestration) ve olay tabanlı (choreography).

**Q etkisi — Q7 (BC/DB ilişkisi):** CoreMusic'te çok servisli iş akışları (ör. katalog
güncelleme + metadata + kuyruk) ACID transaction ile tek transaction'da toplanamaz; Saga
bu sınırın pratik karşılığıdır. services-master §7/7'deki "BC'ler arası tutarlılık" maddesine
doğrudan kanıt.
**Q etkisi — Q9 (servisler arası taşıyıcı):** Orkestrasyon modu, taşıyıcı olarak kuyruk
(background-jobs §2 B/C seçenekleri) veya HTTP (Q9) ile taşınır — koreografi modu olay
yayınını zorunlu kılar.

**Kardeş bağ:** [[background-jobs/background-jobs-web-arastirma]] §2 seçenek B (Redis Stream
consumer group) koreografi modunun taşıyıcı adayıdır; bu dosya Saga'nın kendisini kanıtlar,
taşıma kardeş dosyada.

**Doğrulama:** GET → 200 (2026-10-18). Desen tanımı + iki koordinasyon modu doğrulandı.

### §6.5 T2-4 — API Composition (microservices.io)

**Kanıt:** API Composition deseni, birden çok servisin verisini **sorgu sırasında** birleştirerek
okuma yanıtını üretir; her servis kendi verisini tutar (database-per-service ile uyumlu),
birleştirme istemci tarafında veya bir API gateway/sorgu servisinde yapılır. Desen ayrıca
durumsuz (stateless) birleştirme ve "tek servis sorgusu" seçeneğini kıyaslar.

**Q etkisi — Q7:** Database-per-Service (T2-7) seçildiğinde join'ler DB içinde yapılamaz;
API Composition bunun okuma tarafı çözümüdür — Q7 matrisinde "A — tek DB" ile "B — servis başına
DB" arasındaki farkın pratik sonucu.
**Q etkisi — Q1:** Sorgu yoğun servisler (katalog listeleme, arama) composition katmanını
ayrı bir servis/ateway olarak doğurabilir; bu, Q1'deki "servis sayısı" kararını etkiler.

**Doğrulama:** GET → 200 (2026-10-18). Desen tanımı + database-per-service ile ilişki doğrulandı.

### §6.6 T2-5 — Data Transfer Object (eaaCatalog.com)

**Kanıt:** Enterprise Application Analysis (Fowler) kataloğundaki DTO girdesi, verinin
katmanlar/servisler arasında **arayüz nesnesi** (interface object) olarak taşındığını; DTO'nun
veri taşıdığını ve davranış içermediğini tanımlar. Amaç, ağ/seri-alan maliyetini azaltmak ve
alan modelini dış yüzeyden ayırmaktır.

**Q etkisi — Q4 (service contracts):** Service-contracts/* dosyalarının temel taşıyıcı birimi
DTO'dur; Q4 şema-öncelikli sözleşme kararı (§1 W-7 OpenAPI) DTO şemasını IDL'den üretir.
DTO'nun "davranışsız" kuralı, CoreMusic domain nesneleri (aggregate) ile servis sözleşmesi
arasındaki sınırın kanıtıdır.

**Doğrulama:** GET → 200 (2026-10-18). Katalog girdisi + DTO tanımı doğrulandı.

### §6.7 T2-6 — Retry (learn.microsoft.com)

**Kanıt:** Azure Architecture Center Retry kalıbı, geçici (transient) hatalar için geri çekilme
stratejisini tanımlar: **artan aralıklı deneme** (exponential backoff + jitter), **deneme
sınırı** ve **circuit breaker ile kırma** kombinasyonu. Kalıp ayrıca retry'ın her hata tipinde
çalıştırılmaması gerektiğini (idempotent olmayan çağrılar) vurgular.

**Q etkisi — Q9 (servisler arası taşıyıcı):** Q9'daki taşıyıcı seçimi (HTTP vs kuyruk) retry
davranışını belirler: HTTP'de uygulama katmanında retry; kuyrukta broker'ın yeniden gönderimi.
**§1 W-8 Protocol Buffers + bu madde birlikte:** retry'ın idempotent çağrıda anlamlı olduğu
kuralı, CoreMusic servis sözleşmelerinde idempotency anahtarı zorunluluğunu doğurur (bkz.
[[background-jobs/background-jobs-web-arastirma]] §6 T2-B2 idempotency-keys).

**Not (dürüstlük):** `martinfowler.com/bliki/Retry.html` adresi **404** döndü (§6.8 RED);
kurumsal kanıt olarak learn.microsoft Retry kalıbı tercih edildi. Bu, içeriğin eksikliği
değil, birincil kaynak erişilemezliğinin dürüst kaydıdır.

**Doğrulama:** GET → 200 (2026-10-18). Backoff + circuit breaker + idempotency uyarısı doğrulandı.

### §6.8 REDDEDİLEN ADAYLAR (TUR-2 — dürüst kayıt, 2026-10-18)

| Aday URL | Neden (RED) | Tarih |
|----------|-------------|-------|
| https://martinfowler.com/bliki/DatabasePerService.html | HTTP **404** — bliki adresi erişilemez; yerine microservices.io/database-per-service (T2-7) kullanıldı | 2026-10-18 |
| https://martinfowler.com/bliki/Retry.html | HTTP **404** — bliki adresi erişilemez; yerine learn.microsoft/retry (T2-6) kullanıldı | 2026-10-18 |
| https://microservices.io/patterns/messaging/idempotent-consumer.html | HTTP **404** — desen sayfası mevcut değil; idempotent-consumer kanıtı background-jobs §6 T2-B2'de Azure kaynaklı alındı | 2026-10-18 |
| https://en.wikipedia.org/wiki/Queue-based_load_leveling | HTTP **404** — wiki maddesi yok (RED; uydurma içerik yazılmadı) | 2026-10-18 |

> Not: `https://www.rabbitmq.com/docs/confirms` bu turda yeniden sorgulandı (200); ancak
> byte-tekilliği kuralı gereği bg §1 W-8 (dlx) ile birlikte background-jobs dosyasında
> yaşamaktadır — bu dosyaya **tekrar yazılmadı** (kontenjan + tekrar_RED).

### §6.9 EKİN Q MATRİSİNE ETKİSİ (özet — KARAR DEĞİL)

| Q | TUR-1 kanıtı | TUR-2 ek kanıt (§6.2–§6.7) | Net etki |
|---|---|---|---|
| Q1 (ai servis sahipliği) | W-1, W-2, W-5, W-6 | T2-1, T2-2, T2-4, T2-7 | CQRS + API Composition + DB-per-Service: servis sınırı kararına üç yeni boyut |
| Q4 (service contracts) | W-7, W-8 | T2-1, T2-2, T2-5 | DTO + CQRS: sözleşmenin birimi ve ayrımı netleşti |
| Q7 (BC/DB ilişkisi) | (sınırlı) | T2-2, T2-3, T2-4, T2-7 | Saga + API Composition + DB-per-Service: BC↔DB matrisi artık dört kaynaklı |
| Q9 (servisler arası taşıyıcı) | W-2, W-8 | T2-3, T2-6 | Retry idempotency uyarısı + Saga taşıyıcı bağımlılığı |

> Q2 · Q3 · Q5 · Q6 · Q8 bu ekten etkilenmedi (TUR-1 kanıtları yeterli kaldı; kararlar ADR'ye
> aittir — services-master §7/1–§7/9).

### §6.10 TUR-2 KANIT SAYIMI VE DOĞRULAMA ÖZETİ

| Kalem | Değer |
|-------|-------|
| Aday URL (bu tur) | 11 |
| Kabul (T2-1…T2-7) | 7 — hepsi GET 200 (2026-10-18) |
| RED (§6.8) | 4 — hepsi dürüst gerekçeli (404 ×3 · tekrar/byte-tekillik ×1) |
| K012 recursive kesişim | 0 (50 dosya; kardeş 4 web-arastirma + services-dogrulama §5 hariç tutularak) |
| Byte-tekilliği ihlali | 0 (T2-1…T2-7 yalnız bu dosyada) |
| Önceden var olan bölüm silindi mi? | HAYIR — §1–§5 korundu; §6 append-only eklendi |
| Doğrulama yöntemi | Code Mode `fetch` — GET · redirect: follow · 2026-10-18 |

> **Eski envanter dosyaları:** `url-envanter.txt` / `url-inventory.txt` bu turda **diskte
> BULUNAMADI** (görev notu); kesişim kontrolü K012 recursive tarama (50 dosya) ile
> substitutes edildi. Bu durum DRIFT kartına taşındı.

### §6.11 DEĞİŞİKLİK KAYDI (TUR-2)

| Tarih | Değişiklik | Yapan |
|-------|------------|-------|
| 2026-10-18 | §6 TUR-2 KAYNAK EKİ — 7 kabul URL (GET 200) · 4 dürüst RED · Q1/Q4/Q7/Q9 matrisleri güncellendi · append-only (§1–§5 korundu) | Researcher subagent (git commit YASAK — orkestratöre aittir) |

### §6.12 TUR-2 KOMBİNE MİMARİ ÇERÇEVE (KARAR DEĞİL — ADR girdisi)

> Bu alt bölüm TUR-2'nin dört desenini (CQRS · Saga · API Composition · Database-per-Service)
> **tek çerçevede** birleştirir: her biri ayrı ayrı kanıtlanmıştı (§6.2–§6.7); burada
> birlikte okunduklarında CoreMusic servis mimarisine hangi **zorunlu kavramları** getirdikleri
> çizer. **Hiçbir satır karar değildir** — ADR girdisidir.

| Desen | K012'ye getirdiği kavram | Etkilediği dosya | Karşılıklı bağımlılık |
|-------|--------------------------|------------------|------------------------|
| CQRS (T2-1, T2-2) | okuma/yazma modeli ayrımı; senkronizasyon bedeli (eventual consistency) | service-contracts/* (Q4) | DB-per-Service ile birleşince okuma modeli **materialized** olur |
| Database-per-Service (T2-7) | her servisin kendi şeması; join DB içinde yapılamaz | services-master §7/7 (Q7) | API Composition bunun okuma çözümüdür |
| API Composition (T2-4) | sorgu sırasında çok-servis birleştirme; stateless | services-master §7/7 (Q7) | CQRS okuma modeli + DB-per-Service → composition katmanı doğar |
| Saga (T2-3) | çok-servisli dağıtık işlem; telafi adımı; orkestrasyon vs koreografi | background-jobs (Q5/Q9) | koreografi modu kuyruk taşır (bg §2 B/C) |
| Retry (T2-6) | idempotency zorunluluğu; backoff + circuit breaker | service-contracts/* (Q9) | Saga telafi adımı retry ile birlikte anlamlı |

### §6.13 "Q7 MATRİSİ" GENİŞLEMESİ (TUR-2 sonucu — KARAR DEĞİL)

> §6.9 özetinde Q7'nin "dört kaynaklı" olduğu belirtilmişti; bu tablo o dört kaynağın
> CoreMusic'e hangi **soruyu** dayattığını açar. Q7 kararı ADR'ye aittir.

| Q7 sorusu | TUR-2 öncesi durum | TUR-2 sonrası dayanak | Karar bekleyen satır |
|-----------|---------------------|------------------------|----------------------|
| Tek DB çoklu BC mi, DB-per-Service mi? | kanıt sınırlı | T2-7 (DB-per-Service resmi desen) + T2-4 (API Composition okuma çözümü) | DB-per-Service seçilirse join'ler composition'da |
| BC'ler arası tutarlılık nasıl? | kanıt yoktu | T2-3 (Saga — telafi adımı) | orkestrasyon mu koreografi mi? |
| Okuma modeli nerede yaşar? | CQRS kanıtsızdı | T2-1, T2-2 (CQRS + senkronizasyon bedeli) | read model materialized mı, live mı? |
| Çok-servis sorgu nasıl yanıtlanır? | kanıt yoktu | T2-4 (API Composition stateless) | composition katmanı ayrı servis mi, gateway mi? |

### §6.14 KARAR MATRİSİ ÖZETİ (Q1–Q9 — TUR-2 sonrası durum)

| Q | Konu | TUR-2 sonrası kanıt durumu | Karar |
|---|------|----------------------------|-------|
| Q1 | ai servis sahipliği | T2-1/2/4/7 (CQRS/composition/DB-per) | ADR |
| Q2 | playback durum sahipliği | (kardeş dosya §6.10) | ADR |
| Q3 | audio servis sınırı | (TUR-1 yeterli — §1 W-5/W-6) | ADR |
| Q4 | service contracts | T2-1, T2-2, T2-5 (DTO + CQRS ayrımı) — **en güçlüleşen** | ADR |
| Q5 | background motor | (kardeş dosya §6.11–§6.15) | ADR |
| Q6 | device-identity | (TUR-2 kapsamı dışında — kanıt sınırlı) | ADR |
| Q7 | BC/DB ilişkisi | T2-2, T2-3, T2-4, T2-7 (dört kaynak) — **en güçlüleşen** | ADR |
| Q8 | swagger/PSR otomasyonu | (TUR-1 yeterli — §1 W-7) | ADR |
| Q9 | servisler arası taşıyıcı | T2-3, T2-6 (retry idempotency + saga taşıyıcı) | ADR |

> **Şeffaflık:** Q6 (device-identity) bu turda **hiç** etkilenmedi — kanıt hâlâ sınırlı;
> "dört dosya derinleşti" iddiası Q6'ya yayılmaz.

### §6.15 CQRS + SAGA BİLEŞİMİ — ÇAPRAZ SENARYOLAR (KARAR DEĞİL)

> CQRS (T2-1/T2-2) ile Saga (T2-3) birlikte okunduğunda üç **çapraz senaryo** doğar; her biri
> CoreMusic servis katmanında farklı bir soru açar. Bu tablo senaryoları adlandırır — çözüm
> ADR'ye aittir.

| # | Senaryo | CQRS boyutu | Saga boyutu | Açtığı soru (hangi Q) |
|---|---------|-------------|-------------|------------------------|
| S1 | Saga adımı yazma modelini değiştiriyor, okuma modeli güncellenmiyor | okuma modeli **gecikmeli** (eventual) | telafi adımı gerekebilir | Q7 (okuma modeli ne zaman senkron?) |
| S2 | Saga telafi adımı CQRS okuma modelinde yanlış görünüm bırakıyor | okuma modeli telafiyi yansıtmalı | telafi = yeni telafi adımı (kaskad) | Q7, Q5 (telafi adımı kuyrukta mı?) |
| S3 | CQRS okuma modeli bir Composition (T2-4) üzerinden sunuluyor, Saga hâlâ sürüyor | Composition **eski** okuma modelini birleştirir | — | Q7 (composition snapshot'ı ne zaman?) |

> **Dürüstlük:** bu senaryolar TUR-2 kanıtlarının **türetilmiş** sonuçlarıdır (spesifik kaynak
> satırı yok) → her satır `⚠️ türetilmiş` işaretini taşır; birincil kaynak yok sayfalarında
> iddia edilmez.

### §6.16 DTO ↔ CQRS OKUMA/YAZMA AYRIMI (Q4 için sözleşme notu — KARAR DEĞİL)

> T2-5 (DTO) + T2-1/T2-2 (CQRS) birlikte Q4 sözleşmesine **iki ayrı DTO ailesi** dayatır:
> komut DTO'su (yazma) ile sorgu DTO'su (okuma). Bu tablo ayrımı çizer; alan listesi ADR'ye aittir.

| DTO ailesi | Yön | İçerik tipi | Kanıt |
|------------|-----|-------------|-------|
| Command DTO (komut) | istemci → servis | niyet (ne yapılacak) + idempotency anahtarı | T2-1, T2-5, T2-6 (retry) |
| Query DTO (sorgu) | servis → istemci | durum görünümü (read model) | T2-1, T2-2, T2-4 |
| Event DTO (olay) | servis → kuyruk (Saga koreografi) | ne oldu (telafi tetikleyicisi) | T2-3, T2-4 |

> **Sonuç (KARAR DEĞİL):** service-contracts/* dosyaları Q4 kararı alındığında **üç aile**
> olarak düzenlenmelidir (Command · Query · Event); TUR-1'de bu ayrım yoktu (tek "şema"
> kavramı vardı). Bu, Q4'ün "en güçlüleşen" olarak işaretlenmesinin gerekçesidir.

### §6.16a TUR-2 KAYNAK ÖZET SAYIMLARI (kanıt yoğunluğu — dürüst tablo)

| Dosya | TUR-1 kabul | TUR-2 kabul | TUR-2 RED | Toplam kabul (bu dosya) |
|-------|-------------|-------------|-----------|--------------------------|
| services-web-arastirma (kök) | 11 URL + 4 pointer | **7** (T2-1…T2-7) | 4 | 18 URL |
| background-jobs | 10 | **5** (T2-B1…T2-B5) | 4 | 15 URL |
| playback-service | 10 | **5** (T2-P1…T2-P5) | 7 | 15 URL |
| ai-service | 10 | **4** (T2-A1…T2-A4) | 4 | 14 URL |
| services-ornekler | — (kod dosyası) | §29 (9 dil) | — | — |
| **Toplam** | 31 | **21** | 19 | 52 URL (4 dosya) |

> **Doğrulama notu:** 21 kabulün tamamı 2026-10-18 GET ile 200 döndü; 19 RED'in 11'i 404,
> 5'i envanter tekrarı (K008), 1'i kontenjan (LLM), 2'si byte-tekilliği/tekrar. Uydurma
> URL/RED iddiası **0**.

### §6.17 DEĞİŞİKLİK KAYDI TAMAMLAYICI (TUR-2 · tur-3 ekleri)

| Tarih | Değişiklik | Yapan |
|-------|------------|-------|
| 2026-10-18 | §6.12–§6.16a eklendi (kombine çerçeve · Q7 genişlemesi · Q1–Q9 karar özeti · çapraz senaryolar · DTO ailesi · kaynak özet) · append-only | Researcher subagent (git commit YASAK) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode