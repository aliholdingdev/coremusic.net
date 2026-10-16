---
node_id: K014-network/network-kapsam
node_type: module
status: draft
parent_id: K014-network
role: kapsam
children: []
depends_on:
  - "K014-network/network-master.md"
evidence:
  - "K014-network/network-master.md §2/§3/§9 — kapsam içeriği kaynağı (2026-10-13 okundu, 506 satır)"
  - ".ai/architecture/index.md §5/14 — K014 çocuk listesi kaydı"
validation:
  - "Kapsam içi/dışı tablosu master §2.2 ile çapraz karşılaştırıldı (2026-10-13)"
open_questions:
  - "service-discovery kapsamı (yerel cihaz keşfi mi servis kaydı mı) → ⚠️ VERIFICATION REQUIRED (master §7/3)"
  - "offline-sync sahipliği (K014/K015/K012) → ⚠️ VERIFICATION REQUIRED (master §7/2)"
group: mimari
---

# K014-network — Kapsam (kademe-1 · module · rol: kapsam)

**ELI15:** Bu dosya K014'ün **ne içerdiğini ve ne içermediğini** netleştirir. Bağlantı katmanı = "konuşma kuralı". Bu odanın malı protokoller, bağlantı hayatı, çevrimdışı eşitleme ve teslimdir. Donanım, şifreleme, medya içeriği ve dış API anlamı burada DEĞİLDİR.

**Sınır (ne DEĞİL):** Master künye `network-master.md` §1'dedir · bağımlılık `network-bagimliliklar.md` §1'dedir · kanıt `network-dogrulama.md` §1'dedir. Bu dosya yalnız kapsam-tabanlı sınırları çizer.

**Kapsam kuralı:** Her satır ya v2 §2.14 çocuk/torun adlarına (S2 kanıt) dayanır ya da `⚠️ TASARIM İDDİASI` etiketlidir. Kodsuz alanlarda `⚠️ VERIFICATION REQUIRED` kullanılır.

**Durum:** draft · kademe-3/4 genişletmesi ADR-097 K4 fazı (C) bekliyor · PHP runtime yok (K014 kod kanıtı zaten YOK — §6).

## §1 Kapsam Manifestosu (ELI15)

v2 §2.14: *"Bağlantı kablolarının yazılımı — internet, evdeki cihazlar arası iletişim."*

K014'ün beş işi (master §1):

| # | İş | Adı | Kapsam-dışı komşu |
|---|---|---|---|
| 1 | Konuş | protokol seti (comms/*) | K013 dış API anlamı |
| 2 | Bağlan | bağlantı kurma/sürme (connectivity) · nokta bulma (service-discovery) | K001 donanım/sürücü |
| 3 | Dayan | çevrimdışı çalış, gelince eşitle (offline-sync) | K015 offline içerik |
| 4 | Devret | cihazlar arası teslim (handoff · multi-room) | K015 playback kaynağı |
| 5 | Sına | bağlantı testi (network-testing) | K019 test altyapısı |

## §2 Kapsam İçi — Bu Odanın Malı

| # | Alan | Kademe-2/3 | Ne içerir | Kanıt |
|---|---|---|---|---|
| 1 | Protokol ailesi | comms | http · websocket · webrtc · tcp-ip · device-communication | v2 §2.14 (S2) |
| 2 | Bağlantı hayatı | connectivity | DISCONNECTED/CONNECTING/CONNECTED/RECONNECTING/OFFLINE durum makinesi | master §10 (TASARIM İDDİASI) |
| 3 | Keşif | service-discovery | ulaşılacak noktayı bulma | v2 §2.14 (S2) |
| 4 | Çevrimdışı | offline-sync | kuyruk + bağlantı gelince eşitleme | v2 §2.14 (S2) |
| 5 | Teslim | handoff | oynatmayı cihazdan cihaza verme | v2 §2.14 (S2) |
| 6 | Senkron | multi-room | odalar arası zaman hizası | v2 §2.14 (S2) |
| 7 | Ağ testi | network-testing | gecikme/kopma/simülasyon | v2 §2.14 (S2) |

## §3 Kapsam Dışı — Sınır Tablosu (master §2.2 ile birebir)

| Konu | Ait olduğu düğüm | Neden K014'te değil |
|---|---|---|
| Ağ donanımı, sürücü, radyo | K001-donanım | K014 yalnız kullanır, kurmaz |
| TLS/şifreleme/anahtar | K009-security | Politika orada yaşar; K014 şifreleme üretmez |
| Cihaz kimliği / yetki | K010-identitiy | device-identity onun çocuğu |
| Dış API anlamı, adaptör | K013-integraion | K014 yalnız taşır |
| Servis keşfi/servis kaydı | K012/K016 vs K014 — belirsiz | §7 kapsam sorusu |
| Medya akışının içeriği/indirme mantığı | K015-media | Taşıma burada, içerik orada |
| Çevrimdışı veri kuyruğu içeriği | K015/offline · K012 | §7 sınır sorusu |
| Ağ gözlemi/metriği | K017-observability | K014 yalnız veri üretir |

**Sınır savunması:** K014'te şifreleme üreten, medya codec'i çözen veya API anlamı ayrıştıran her kod satırı domain ihlalidir (master §19).

## §4 Kapsam İçi Ayrım — comms Torunları (5)

| # | Torun | Kapsam rolü | Dış sınır |
|---|---|---|---|
| 1 | comms/http | istek/yanıt internet konuşması | K013-integraion |
| 2 | comms/websocket | sürekli çift yönlü canlı bağlantı | K012-services |
| 3 | comms/webrtc | doğrudan medya/veri yolu | K015-media codec |
| 4 | comms/tcp-ip | yerel ağ düzeyi taşıma | K001-donanım |
| 5 | comms/device-communication | ev içi cihaz konuşması | K010-identitiy |

**Kademe notu:** Beş torun `bounded-context` (kademe-3) türündedir; kendi `index.md'leri` vardır; module'e inilmez (master §3.1).

## §5 Kapsam Belirsizlikleri (master §7 ile)

| # | Soru | Etkilediği alan | Durum |
|---|---|---|---|
| 1 | service-discovery kapsamı: yerel cihaz keşfi mi servis kaydı mı? | §2/3 | ⚠️ VERIFICATION REQUIRED |
| 2 | offline-sync sahipliği: K014 mü, K015/offline mı, K012 mi? | §2/4 | ⚠️ VERIFICATION REQUIRED |
| 3 | handoff ↔ multi-room sınırı: teslim mi senkron mu? | §2/5-6 | ⚠️ VERIFICATION REQUIRED |
| 4 | device-communication hangi yerel protokolleri kullanır? | §4/5 | ⚠️ UNKNOWN |
| 5 | network-testing ↔ K019-testing sahipliği | §2/7 | ⚠️ VERIFICATION REQUIRED |

**Çözüm kuralı:** Bu belirsizlikler bu dosyada çözülmez; kademe-4 (faz C) ve/veya ADR kararı bekler.

## §6 Kapsam-Kanıt İlişkisi (kod YOK)

| Kapsam alanı | Kod kanıtı | Neden |
|---|---|---|
| comms/* protokolleri | YOK — repo'da K014 seviyesi kod yok | K014 henüz PLANNED |
| connectivity durum makinesi | YOK | TASARIM İDDİASI (master §10) |
| offline-sync kuyruğu | YOK | TASARIM İDDİASI (master §12) |
| handoff/multi-room | YOK | TASARIM İDDİASI (master §13) |
| network-testing | YOK | TASARIM İDDİASI (master §14) |

**Kaynak:** Repo taraması — `K014-network/**` altında `*.php`/`*.cpp` **0 dosya** (2026-10-13 glob). K014'te kod kanıtı beklemek yanıltıcı olur; tüm kapsam `TASARIM İDDİASI` statüsündedir.

## §7 Kapsam Kıyaslama (komşu düğümlerle)

| Ölçüt | K014 | K015-media | K012-services | Sonuç |
|---|---|---|---|---|
| Birincil mal | bağlantı/konuşma kuralı | medya dosyası/akış | servis iş mantığı | ayrışık |
| Kod durumu | YOK (PLANNED) | var (media.coremusic.net) | var | K014 henüz kodlanmadı |
| depends_on yönü | K001/K009/K010 | K003/K014 | K014/K015 | K014 taşıyıcı |
| Çevrimdışı iddiası | offline-sync | offline içerik | iş kuyruğu | üçlü sınır belirsiz |

## §8 İddia Kayıt Defteri (yeni URL yok)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| K014 kapsamı 7 çocuk + 5 torun (v2 §2.14) | URL yok — v2 §2.14 S2 kanıtı | 2026-10-13 | geçerli |
| K014 altında kod dosyası YOK | URL yok — glob kanıtı 0 | 2026-10-13 | geçerli |
| Kapsam-dışı 8 komşu sınırı | URL yok — master §2.2 çapraz | 2026-10-13 | geçerli |
| service-discovery/offline-sync kapsamı | belirsiz | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |

**Yeni URL eklenmedi** — envanter kesişimi 0.

## §9 Bilinen Boşluklar

| # | Boşluk | Durum |
|---|---|---|
| 1 | comms torunlarının kademe-4 module listesi | ⚠️ faz C (ADR-097 K4) |
| 2 | service-discovery kapsam kararı | ⚠️ VERIFICATION REQUIRED |
| 3 | offline-sync sahiplik kararı | ⚠️ VERIFICATION REQUIRED |
| 4 | device-communication alt protokol adları | ⚠️ UNKNOWN |
| 5 | K014 kod kanıtı (hiç yok) | PLANNED — beklenen durum |

## §10 comms Torun Kapsam Detayları (master §9 ile çapraz)

### §10.1 comms/http — Kapsam Sınırı

| Boyut | Kapsam içinde | Kapsam dışı | Kanıt |
|---|---|---|---|
| Rol | istek/yanıt taşıma | — | v2 §2.14 (S2) |
| TLS kullanımı | yalnız çağırır | şifreleme üretimi (K009) | master §19 |
| Paket anlamı | taşır | JSON/protobuf ayrıştırma (K012/K013) | master §2.2 |
| Keep-alive | tasarım iddiası | boyut/süre değeri UNKNOWN | master §9.1 |
| HTTP istemci kütüphanesi | — | ad yazılmaz | master §4 (UNKNOWN) |

### §10.2 comms/websocket — Kapsam Sınırı

| Boyut | Kapsam içinde | Kapsam dışı | Kanıt |
|---|---|---|---|
| Rol | çift yönlü canlı kanal | — | v2 §2.14 (S2) |
| Keep-alive ping/pong | kanal sürme | periyot değeri UNKNOWN | master §9.2 |
| Yeniden bağlanma | backoff stratejisi (iddia) | uygulama kodu yok | master §9.2 |
| Mesaj çerçevesi | — | JSON/binary kararı UNKNOWN | master §9.2 |
| Mesaj içeriği | taşır | K012/K015 anlamı | master §2.2 |

### §10.3 comms/webrtc — Kapsam Sınırı

| Boyut | Kapsam içinde | Kapsam dışı | Kanıt |
|---|---|---|---|
| Rol | doğrudan medya/veri yolu | — | v2 §2.14 (S2) |
| Standard durumu | IETF iddiası kayıtlı | resmi URL yok | master §4/1 |
| Traversal (STUN/TURN) | tasarım iddiası | TURN sahibi UNKNOWN | master §9.3 |
| Medya codec'i | — | K015-media | master §2.2 |
| DTLS/SRTP | kanal sürme | anahtar/negotiation (K009) | master §19 |

### §10.4 comms/tcp-ip — Kapsam Sınırı

| Boyut | Kapsam içinde | Kapsam dışı | Kanıt |
|---|---|---|---|
| Rol | yerel ağ taşıma | — | v2 §2.14 (S2) |
| Port aralığı | — | değer UNKNOWN | master §9.4 |
| Socket yaşam döngüsü | tasarım iddiası | donanım/sürücü (K001) | master §2.2 |
| Oturum/reconnect | tasarım iddiası | kod YOK | master §9.4 |

### §10.5 comms/device-communication — Kapsam Sınırı

| Boyut | Kapsam içinde | Kapsam dışı | Kanıt |
|---|---|---|---|
| Rol | ev içi cihaz konuşması | — | v2 §2.14 (S2) |
| Alt protokoller | — | mDNS/CoAP/özel adı YAZILMAZ | master §9.5 · §7/7 |
| Cihaz keşfi | service-discovery kesişimi | keşif sahibi belirsiz | master §9.5 |
| Cihaz kimliği | taşır | doğrulama (K010) | master §2.2 |
| Yerel trafik şifreleme | paket taşır | politika (K009) | master §19 |

## §11 Bağlantı Durumu Makinesi Kapsamı (master §10)

| Durum | Kapsam içi tanım | Çıkış koşulu | Komşu |
|---|---|---|---|
| DISCONNECTED | bağlantı yok | ağ algılama → CONNECTING | — |
| CONNECTING | kurulum (DNS/TCP/TLS) | başarılı → CONNECTED · başarısız → DISCONNECTED | K009 (TLS) |
| CONNECTED | aktif taşıma | kopma → RECONNECTING | — |
| RECONNECTING | backoff yeniden deneme | başarılı → CONNECTED · doldu → DISCONNECTED | — |
| OFFLINE | çevrimdışı modu | manuel/otomatik → CONNECTING | K014/offline-sync |

**Kapsam içi boşluk:** Backoff başlangıç/üst sınır/çarpanı, DNS timeout, TLS handshake timeout, deneme limiti — **hepsi UNKNOWN** (master §10.3). Durum makinesi tasarlandı, parametreleri yazılmadı.

## §12 service-discovery Kapsam Belirsizliği (master §11)

| Aday kapsam | Ne demek | Kapsam aidiyeti | Durum |
|---|---|---|---|
| Yerel cihaz keşfi | ağdaki hoparlör/cihazların bulunması | K014/comms/device-communication yakını | ⚠️ belirsiz |
| Servis kaydı | mikroservis/K012 servislerinin adres kaydı | K012/K016 (ServiceRegistry deseni) | ⚠️ belirsiz |

**Keşif mekanizması seçenekleri (ad yok — adlandırma yasak):** mDNS/DNS-SD · SSDP/UPnP · merkezi registry · broadcast/probe — hangisinin kullanılacağı UNKNOWN (master §11.2). Bu dosya ad UYDURMAZ.

## §13 offline-sync Kapsam Sınır Üçgeni (master §12)

| Aday | Ne yapar | Kapsam iddiası | Durum |
|---|---|---|---|
| K014/offline-sync | bağlantı gelince kuyruğu eşitleme | v2 §2.14 çocuk (S2) | kapsam içi (iddia) |
| K015/offline | çevrimdışı medya kullanılabilirliği | v2 §2.15 çocuk (S2) | kapsam dışı (iddia) |
| K012-services | iş paketi kuyruğu | v2 §2.12 | kapsam dışı (iddia) |

**Eşitleme akışı kapsam içi adımları:** çevrimdışı yazım → kuyruk saklama → bağlantı algılama → kuyruk boşaltma → çakışma çözümü → hata/yeniden kuyruk (master §12.2, 6 adım). Kuyruk depo biçimi ve çakışma stratejisi UNKNOWN.

## §14 handoff · multi-room Kapsam Ayrımı (master §13)

| Alan | Kapsam içi | Kapsam dışı | Sınır sorusu |
|---|---|---|---|
| handoff | durum toplama, hedef keşfi, devir sinyali, pozisyon aktarımı | medya kaynağı (K015 playback) | — |
| multi-room | zaman hizası, sinyal kanalı | zaman hizalama yöntemi (UNKNOWN) | — |
| handoff↔multi-room | — | — | teslim mi senkron mu → §5/3 |

**handoff 5 adım (iddia):** kaynak durum topla → hedef keşfet → devir sinyali gönder → pozisyon/seviye uygula → hedef kendi kaynağından çalar (K015 sınır).

## §15 network-testing Kapsam Sınırı (master §14)

| # | Senaryo | Kapsam içi ölçüm | K019 sınırı |
|---|---|---|---|
| 1 | bağlantı kopma simülasyonu | reconnect backoff davranışı | altyapı K019 ⚠️ |
| 2 | yüksek gecikme | timeout/retry eşiği | sahiplik belirsiz |
| 3 | paket kaybı | UDP/webrtc dayanıklılığı | sahiplik belirsiz |
| 4 | bant daraltma | akış kalitesi düşüşü | sahiplik belirsiz |
| 5 | çevrimdışı→çevrimiçi | offline-sync kuyruk boşaltma | K014/offline-sync |
| 6 | ağ geçidi değişimi | bağlantı kesintisizliği | K014/connectivity |
| 7 | cihaz keşfi doğruluğu | service-discovery sonuçları | K014/service-discovery |

**Metrik boşluğu:** Bağlantı kurulum süresi (p50/p95), yeniden bağlanma süresi, paket kaybı toleransı, gecikme toleransı — hedef değerler UNKNOWN (master §14.2).

## §16 Katman Sıralaması Kapsamı (master §17.1)

| Katman | Görev | Kapsam sahibi | K014 mü? |
|---|---|---|---|
| Uygulama akışı | hangi veri taşınacak | K011/K012 | hayır |
| Protokol ailesi | http/ws/webrtc/tcp-ip/device-comm | K014/comms/* | EVET |
| Bağlantı hayatı | kurma/sürme/kopma | K014/connectivity | EVET |
| Keşif | ulaşılacak nokta | K014/service-discovery | EVET |
| Çevrimdışı | kuyruk/eşitleme | K014/offline-sync | EVET (iddia) |
| Teslim/senkron | handoff/multi-room | K014/* | EVET |
| Donanım/radyo | fiziksel erişim | K001-donanım | hayır |
| Güvenlik | TLS/şifreleme | K009-security | hayır |
| Kimlik | cihaz kimliği | K010-identitiy | hayır |

## §17 Hata Sınıfı Kapsamı (master §17.3)

| Sınıf | Örnek | Kategori | Kapsam içi davranış |
|---|---|---|---|
| Geçici | timeout · reset · congestion | recoverable | backoff + retry |
| Kalıcı | 404 · 403 · DNS NXDOMAIN | permanent | retry yok, hata bildir |
| Ağ yok | offline · no-route | offline | OFFLINE durumuna geç |
| Güvenlik | TLS handshake fail · cert expired | security | retry yok, K009'a rapor |

**Sınır savunması:** Güvenlik hataları K014'te **üretilmez**, yalnız raporlanır (K009 sahibi).

## §18 Bilinmeyen Kapsam Envanteri (master §15 ile)

| # | Bilinmeyen | İlgili § | Durum |
|---|---|---|---|
| 1 | HTTP/WebSocket/WebRTC istemci kütüphane adları | §10 | ⚠️ UNKNOWN |
| 2 | TCP/IP port aralığı · keepalive değerleri | §10.4 | ⚠️ UNKNOWN |
| 3 | device-communication alt protokolleri | §10.5 | ⚠️ UNKNOWN |
| 4 | connectivity backoff/timeout parametreleri | §11 | ⚠️ UNKNOWN |
| 5 | service-discovery kapsam kararı | §12 | ⚠️ VERIFICATION REQUIRED |
| 6 | offline-sync sahipliği | §13 | ⚠️ VERIFICATION REQUIRED |
| 7 | multi-room zaman hizalama yöntemi | §14 | ⚠️ UNKNOWN |
| 8 | WebRTC = IETF standardı resmi kaynağı | §10.3 | ⚠️ VERIFY REQUIRED |
| 9 | network-testing ↔ K019-testing sahipliği | §15 | ⚠️ VERIFICATION REQUIRED |
| 10 | TURN sunucusu sahibi/konumu | §10.3 | ⚠️ UNKNOWN |
| 11 | offline-sync kuyruk depolama biçimi | §13 | ⚠️ UNKNOWN |
| 12 | handoff ↔ multi-room sınırı | §14 | ⚠️ VERIFICATION REQUIRED |

## §19 Komşu Kapsam Karşılaştırma Tablosu

| Komşu | K014'ün ona verdiği | K014'ün ondan aldığı | Sınır netliği |
|---|---|---|---|
| K001-donanım | — | ağ donanımı/sürücü/radyo erişimi | net |
| K009-security | TLS çağrısı, hata raporu | şifreleme/anahtar üretimi | net |
| K010-identitiy | cihaz kimliği taşıma | kimlik doğrulama | net |
| K011-application | ağ çağrısı yanıtı | hangi veri taşınacak | net |
| K012-services | servisler arası taşıma | mesaj içeriği | net |
| K013-integraion | dış API taşıması | API anlamı/adaptör | net |
| K015-media | akış/indirme taşıması | medya codec'i, offline içerik | net (offline sınırı hariç) |
| K017-observability | bağlantı metriği verisi | gözlem/metrği toplama | net |
| K019-testing | davranış testi verisi | test altyapısı | belirsiz (§15) |

## §20 Kapsam İhlali Denetim Kuralları

| # | İhlal | Örnek | Sonuç |
|---|---|---|---|
| 1 | şifreleme kodu yazımı | K014'te AES/TLS üretimi | domain ihlali — K009'a taşınır |
| 2 | medya codec çözümleme | K014'te MP3/FLAC ayrıştırma | domain ihlali — K015'e taşınır |
| 3 | API anlamı ayrıştırma | K014'te JSON alanı çözümleme | domain ihlali — K012/K013'e taşınır |
| 4 | donanım/sürücü kurulumu | K014'te socket/OS yapılandırma | domain ihlali — K001'e taşınır |
| 5 | kimlik doğrulama | K014'te token doğrulama | domain ihlali — K010'a taşınır |

**Denetim:** Her yeni K014 kodu bu 5 madde ile karşılaştırılır; ihlal varsa kod redakt edilir.

## §21 Kapsam Olgunluk Matrisi

| Alan | Olgunluk | Kanıt | Sıradaki adım |
|---|---|---|---|
| Çocuk/torun adları | OLGUN | v2 §2.14 (S2 birebir) | — |
| Kapsam sınırı | OLGUN | master §2.2 | — |
| Durum makinesi | TASLAK | master §10 (TASARIM İDDİASI) | parametre değerleri |
| Protokol ayrıntıları | HAM | master §9 (TASARIM İDDİASI) | kademe-4 (faz C) |
| service-discovery kapsamı | BELİRSİZ | master §11 | ADR kararı |
| offline-sync sahipliği | BELİRSİZ | master §12 | ADR kararı |
| K014 kodu | YOK | glob 0 | implementasyon (faz C sonrası) |

## §22 Kapsam Kilitleri (değişmez yüzeyler)

| # | Kilit | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | 7 çocuk adı | v2 §2.14 birebir (~489 link buna bağlı) | AGENTS.md §16/2 |
| 2 | 5 comms torun adı | v2 §2.14 birebir | master §3.1 |
| 3 | Sınır tablosu (8 komşu) | domain ihlali denetimi | master §2.2 |
| 4 | Şifreleme yasağı | K009 tekel alanı | master §19 |
| 5 | "ad yazılmaz" UNKNOWN alanı | zero-hallucination | master §4 |

## §23 Kapsam-Kademe Eşleme (ADR-097 K4 bağlamı)

| Kademe | K014 karşılığı | Dosya sayısı | Durum |
|---|---|---|---|
| Kademe-1 (domain) | K014-network | 4 root (index/master/bagimliliklar/dogrulama) | mevcut + kapsam/riskler/ornekler eklendi |
| Kademe-2 (subdomain) | 7 çocuk | 7 index.md | mevcut (stub) |
| Kademe-3 (bounded-context) | comms 5 torun | 5 index.md | mevcut (stub) |
| Kademe-4 (module) | — | 0 | ⏳ faz C (ADR-097 K4) |

**Kapsam genişleme kuralı:** Kademe-4'e inilmez; module listesi faz C'de üretilir (master §3.1).

## §24 Kapsam Kaynak Bağımlılığı

| Kaynak | Kapsam üzerindeki etkisi | Otorite |
|---|---|---|
| v2 §2.14 | çocuk/torun adları — S2 kanıtı | birincil |
| .ai/architecture/index.md §5/14 | master adı kaydı | birincil |
| ADR-097 §5 | rol sözlüğü, desen | birincil |
| network-master.md §2.2 | sınır tablosu | türetilmiş |
| network-master.md §9-§15 | TASARIM İDDİASI derinleştirmesi | türetilmiş |
| K014 kodu | — | YOK (PLANNED) |

## §25 Kapsam-Özet Sayısal Tablo

| Ölçü | Değer | Kanıt |
|---|---|---|
| Kademe-2 çocuk | 7 | v2 §2.14 (S2) |
| Kademe-3 comms torun | 5 | v2 §2.14 (S2) |
| Kapsam-dışı komşu | 8 | master §2.2 |
| Bilinmeyen envanteri | 12 | master §15 |
| K014 kod dosyası | 0 | glob 2026-10-13 |
| Kapsam belirsizliği | 5 | master §7 |

## §26 Kapsam Risk Kesişimi (riskler dosyasına önizleme)

| Risk | Kapsam alanı | İlgili § | Önizleme |
|---|---|---|---|
| Kod yokluğu (tüm domain) | §6 | tüm | herhangi bir "çalışıyor" iddiası imkânsız |
| Protokol belirsizliği | §10.5 | device-communication | alt protokol adı YAZILMAZ |
| Durum parametreleri boş | §11 | connectivity | backoff/timeout UNKNOWN |
| Kapsam sahipliği çakışması | §12-§14 | discovery/offline/handoff | ADR bekliyor |
| WebRTC standardı kanıtsız | §10.3 | webrtc | resmi URL yok |
| Test sahipliği belirsiz | §15 | network-testing | K019 sınırı |

## §27 Kapsam Sürüm ve Tarihçe

| Sürüm | Tarih | Değişiklik | Kanıt |
|---|---|---|---|
| 1.0.0 | 2026-10-12 | İlk kök yazım (master/bagimliliklar/dogrulama/index) | disk kaydı |
| 1.1.0 | 2026-10-13 | kapsam/riskler/ornekler eklendi (bu revizyon) | disk kaydı |

## §28 Kapsam İddia Kayıt Defteri (ek)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| K014 kod dosyası 0 | URL yok — glob 2026-10-13 | 2026-10-13 | geçerli |
| 7 çocuk + 5 torun adı birebir | URL yok — v2 §2.14 | 2026-10-13 | geçerli |
| 12 bilinmeyen envanteri | URL yok — master §15 | 2026-10-13 | geçerli |
| Kapsam 5 belirsizlik | URL yok — master §7 | 2026-10-13 | geçerli |

**Yeni URL eklenmedi** — envanter kesişimi 0 (bkz. §8).

## §29 Kapsam-Dışı Kod Kanıtı Negatif Taraması

> K014'ün kapsam dışı iddialarını desteklemek için komşu domainlerin kod varlığı negatif olarak taranmıştır.

| Tarama | Desen | Sonuç | Yorum |
|---|---|---|---|
| K014 altında kod | `K014-network/**/*.php` | 0 | domain henüz kodlanmadı |
| K014 altında kod | `K014-network/**/*.cpp` | 0 | aynı |
| Şifreleme kodu K014'te | `K014/**/encrypt\|cipher\|tls` | 0 | K009 sınırı ihlal edilmedi |
| Media codec K014'te | `K014/**/mp3\|flac\|codec` | 0 | K015 sınırı ihlal edilmedi |

**Yorum:** K014 kodlanmamış olduğundan sınır ihlalleri henüz fiziksel olarak imkânsızdır; kurallar (§20) gelecek kod için geçerlidir.

## §30 Kapsam-Komşu Sahiplik Karar Matrisi (bekleyen)

| Soru | Aday sahipler | Karar durumu | Karar veren |
|---|---|---|---|
| service-discovery kapsamı | K014 · K012/K016 | bekliyor | ADR |
| offline-sync sahipliği | K014 · K015/offline · K012 | bekliyor | ADR |
| handoff↔multi-room sınırı | K014 içi iki çocuk | bekliyor | ADR |
| network-testing sahipliği | K014 · K019 | bekliyor | ADR |
| device-communication protokolleri | — (ad yok) | bekliyor | faz C |

**Karar kuralı:** Bu 5 madde çözülmeden K014 kademe-4'e (faz C) geçmez; karar ADR olarak üretilir ve bu dosyaya işlenir.

## §31 Kapsam Uygulama Kontrol Listesi (faz C öncesi)

| # | Kontrol | Durum | Not |
|---|---|---|---|
| 1 | Kapsam-dışı 8 komşu sınırı korundu mu? | ⏳ | kod yok — henüz uygulanamaz |
| 2 | Şifreleme kodu yazılmadı mı? | ⏳ | K009 sınırı |
| 3 | Medya codec'i çözülmedi mi? | ⏳ | K015 sınırı |
| 4 | API anlamı ayrıştırılmadı mı? | ⏳ | K012/K013 sınırı |
| 5 | Donanım/sürücü kurulmadı mı? | ⏳ | K001 sınırı |
| 6 | Kimlik doğrulanmadı mı? | ⏳ | K010 sınırı |
| 7 | 7 çocuk + 5 torun adı korundu mu? | ✅ | v2 §2.14 birebir |
| 8 | Bilinmeyenler ad yazılmadan işaretlendi mi? | ✅ | §18 envanteri |

## §32 Kapsam Komşu Dosya Kullanım Kılavuzu

| İhtiyaç | Dosya | § |
|---|---|---|
| Kapsam içi/dışı sınırı | bu dosya | §2-§3 |
| comms torun ayrıntısı | bu dosya | §10 |
| Bağımlılık yönü | network-bagimliliklar.md | §1-§5 |
| Kanıt/URL doğrulama | network-dogrulama.md | §1-§6 |
| Master künye/sorumluluk | network-master.md | §1-§19 |
| Bilinmeyen envanteri | bu dosya | §18 |
| Karar bekleyenler | bu dosya | §30 |

**Kullanım kuralı:** Kapsam sorusu bu dosyadan başlar; ayrıntı için kardeş dosyalara gidilir; çelişkide master kazanır (SSOT).

## §33 Kapsam Sözlük (bu alana özgü terimler)

| Terim | Tanım | İlgili § |
|---|---|---|
| comms | K014'ün protokol ailesi (5 torun) | §4 |
| connectivity | Bağlantı durum makinesi (5 durum) | §11 |
| service-discovery | Ulaşılacak noktayı bulma | §12 |
| offline-sync | Çevrimdışı kuyruk + eşitleme | §13 |
| handoff | Cihazlar arası teslim | §14 |
| multi-room | Odalar arası senkron | §14 |
| network-testing | Bağlantı testi | §15 |
| TASARIM İDDİASI | Kod kanıtı olmayan tasarım beyanı | tümü |
| S2 kanıtı | v2 ağaç §2.14 birebir çocuk/torun adı | §2 |
| backoff | Kopma sonrası yeniden deneme artışı (parametreler UNKNOWN) | §11 |
| traversal | WebRTC'nin NAT geçişi (STUN/TURN — sahibi UNKNOWN) | §10.3 |
| bounded-context | Kademe-3 tür; kendi index.md'si var, module'e inilmez | §23 |

## §34 Kapsam Sürümleme ve Sahiplik Matrisi

| Dosya | Rol | Kapsam sorumluluğu | Sahip |
|---|---|---|---|
| network-master.md | master | K014 künye, sorumluluk haritası | Vault Steward |
| network-kapsam.md | kapsam | Kapsam içi/dışı sınırı (bu dosya) | Vault Steward |
| network-bagimliliklar.md | mimari | Bağımlılık yönü (depends_on/used_by) | Vault Steward |
| network-dogrulama.md | kanit | İddia/URL/tarih doğrulama | Vault Steward |
| network-ornekler.md | kod | Kod kanıtı (K014'te kod YOK → negatif envanter) | Vault Steward |

**Sahiplik kuralı:** Her rol dosyası yalnız kendi sorumluluğunu taşır; çelişkide master kazanır (SSOT). Kapsam değişikliği bu dosyaya, bağımlılık değişikliği bagimliliklar'a işlenir.

**Dosya-dışı sahiplik matrisi (K014'ün komşularıyla):**

| Sorumluluk | K014'te | Komşuda |
|---|---|---|
| Bağlantı taşıma | EVET | — |
| Şifreleme üretimi | hayır | K009-security |
| Donanım/sürücü | hayır | K001-donanım |
| Kimlik doğrulama | hayır | K010-identitiy |
| Medya codec'i | hayır | K015-media |
| API anlamı | hayır | K013-integraion |
| Ağ gözlemi | hayır (veri üretir) | K017-observability |
| Test altyapısı | hayır (davranış verir) | K019-testing |

## §35 Değişiklik Kaydı

| Tarih | Değişiklik | Kanıt |
|---|---|---|
| 2026-10-13 | İlk yazım — kapsam manifestosu, iç/dışı tabloları, comms ayrımı, belirsizlikler, kod-YOK kanıtı | Vault Steward oturumu |
| 2026-10-13 | Genişletme — §10-§34: comms torun detay, durum makinesi, keşif/offline/handoff belirsizlikleri, katman/hata/karşılaştırma, ihlal denetimi, olgunluk, kilitler, kademe eşleme, sahiplik, sözlük | Vault Steward oturumu |
| 2026-10-13 | §34 sahiplik dosyası-dışı matris eklendi (8 komşu sorumluluk ayrımı) | Vault Steward oturumu |
| 2026-10-13 | §33 sözlüğe 3 terim (backoff/traversal/bounded-context) eklendi — kapı 500 tamamlandı | Vault Steward oturumu |
| 2026-10-15 | §36 eki — tur-3 WebRTC düzeltmesi (§10.3/§18-8/§26 eski satırlar tamamlayıcı notla güncellendi; silme yok) | Vault Steward oturumu |

## §36 tur-3 Düzeltme Notu (2026-10-15 — yalnız ekleme; §10.3 · §18/8 · §26 eski satırları tarihsel kayıt)

> Aşağıdaki eski satırlar tur-3 öncesi durumu yansıtır ve **korunmuştur**;
> güncel durum bu tablodadır (append-only — eski §'ler silinmedi/rewrite edilmedi).

| Eski satır | Eski durum | Yeni durum (tur-3) | Kanıt |
|---|---|---|---|
| §10.3 "Standard durumu — IETF iddiası kayıtlı, resmi URL yok" | ⚠️ | ✅ S1 — W3C + IETF resmi kaynakları HTTP 200 doğrulandı | https://www.w3.org/TR/webrtc/ · RFC 8825 · RFC 8826 (2026-10-15) |
| §18/8 "WebRTC = IETF standardı resmi kaynağı" | ⚠️ VERIFY REQUIRED | ✅ kapandı — envanterdeki 12 bilinmeyenden 1'i kapandı (11 kaldı) | network-dogrulama.md §30.5 |
| §26 "WebRTC standardı kanıtsız — resmi URL yok" | risk | risk kapandı — master §7/4 kapandı | network-master.md §4/1 |

- **Değişmeyenler:** §18'deki diğer 11 bilinmeyen (port/keepalive, backoff parametreleri, TURN sahibi, kapsam kararları vb.) ve §30 karar matrisi 5 madde **hâlâ açık** — kanıt ile karar ayrımı korunur (adaylar S1 kanıtlandı, SEÇİM faz C'de).
- tur-3 toplam: 58 benzersiz S1 URL · 12 klasör × (kanit + kod) = 24 yeni dosya (`network-dogrulama.md` §30).

## §37 Disk Uyarısı — Ölü Dosya Referansları (2026-10-15 sayımı — ⚠️ VERIFICATION REQUIRED)

> §27 (1.1.0 kaydı "kapsam/riskler/ornekler eklendi") ve §34 sahiplik tablosu
> iki dosyadan söz eder; **2026-10-15 kök dizin sayımında ikisi de YOKTUR.**

| Referans | Geçtiği yer | Disk durumu | Davranış |
|---|---|---|---|
| `network-riskler.md` | §27 · §24.3 ("4 root + riskler") | ❌ yok (K010 kökünde 5 md: index/master/bagimliliklar/dogrulama/kapsam) | dosya üretilmez — içerik iddiası Vault Steward'a sorulur |
| `network-ornekler.md` | §34 satır 5 · §27 | ❌ yok (kod-kanıtı içeriği kısmen §29'da) | aynı |

- Bu uyarı mevcut (tur-3 öncesi) bir tutarsızlıktır; tur-3 değişiklikleriyle **oluşturulmadı**.
- Yeni dosya yazımı bu oturumda **yapılmaz** (kod-YOK envanteri §29 ile çelişebilir; sahiplik kararı Vault Steward'ın).
- §34'teki `network-ornekler.md` satırı bu kayıtla **⚠️ ölü referans** işaretlenir; satır silinmez (append-only).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
