---
title: "K014-network — Network Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K014-network
role: master
authority: "SSOT: v2 §2.14 + .ai/architecture/index.md §5/14"
related: ["network-bagimliliklar.md", "network-dogrulama.md", "index.md"]
group: mimari
---


# K014-network — network-master.md

> Bu dosya v2 §2.14'te adı geçen **master** dosyasıdır (ad kaynaktan birebir,
> ADR-097 K2/K3). Yeni rol dosyaları: network-bagimliliklar.md ·
> network-dogrulama.md.

## §1 Rol (ELI15)

v2 §2.14: *"Bağlantı kablolarının yazılımı — internet, evdeki cihazlar arası iletişim."*

K014, sistemin **konuşma kuralı**dır. Beş iş yapar:

1. **Konuş** — protokol setini taşır (comms → http · websocket · webrtc · tcp-ip ·
   device-communication).
2. **Bağlan** — bağlantıyı kurar ve sürer (connectivity), ulaşılacak noktayı bulur
   (service-discovery).
3. **Dayan** — bağlantı yokken çalışır, gelince eşitler (offline-sync).
4. **Devret** — çalmayı cihazlar arası taşır (handoff · multi-room).
5. **Sına** — bağlantı davranışını test eder (network-testing).

Ne yapmaz: donanım/sürücü **kurmaz** (K001-donanım), şifreleme **üretmez**
(K009-security — yalnız kullanır), medya dosyası **çözmez** (K003/K015),
dış API **anlamaz** (K013-integraion), iş paketi **kuyruklamaz** (K012/offline-sync
ile sınır §7/2).

## §2 Kapsam / Sınır

### §2.1 Kapsam içinde (bu odanın malı)

- Protokoller: comms/* (http · websocket · webrtc · tcp-ip · device-communication)
- Bağlantı hayatı: connectivity · service-discovery
- Çevrimdışı ve teslim: offline-sync · handoff · multi-room
- Ağ testi: network-testing

### §2.2 Kapsam dışında (kimin?) — sınır tablosu

| Konu | Ait olduğu düğüm | Not |
|---|---|---|
| Ağ donanımı, sürücü, radyo | K001-donanım | K014 yalnız kullanır |
| TLS/şifreleme/anahtar | K009-security | Politika orada yaşar |
| Cihaz kimliği / yetki | K010-identitiy | device-identity onun çocuğu |
| Dış API anlamı, adaptör | K013-integraion | K014 yalnız taşır |
| Servis keşfi/servis kaydı | K012/K016 vs K014 — belirsiz | §7/3 kapsam sorusu |
| Medya akışının içeriği/indirme mantığı | K015-media | Taşıma burada, içerik orada |
| Çevrimdışı veri **kuyruğu içeriği** | K015/offline · K012 | §7/2 sınır sorusu |
| Ağ gözlemi/metrği | K017-observability | K014 yalnız veri üretir |

## §3 Kademe-2 çocuk tablosu (v2 §2.14 birebir — 7 çocuk)

| # | Çocuk | ELI15 rol (TASARIM İDDİASI) | Ne içerir — kapsam notu | Durum |
|---|---|---|---|---|
| 1 | comms | İletişim protokolü ailesi | 5 torun (§3.1) — tek çatı altında | draft |
| 2 | connectivity | Bağlantı kurma/sürme | Bağlantı durumu, yeniden bağlanma (ayrıntı ⚠️) | draft |
| 3 | service-discovery | Ulaşılacak noktayı bulma | Yerel cihaz keşfi mi servis kaydı mı → §7/3 | draft |
| 4 | offline-sync | Çevrimdışı eşitleme | Kuyruk + bağlantı gelince eşitleme (içerik sahibi §7/2) | draft |
| 5 | handoff | Cihazlar arası teslim | Oynatmayı bir cihazdan diğerine verme | draft |
| 6 | multi-room | Çok odalı senkron | Odalar arası zaman hizası (ayrıntı ⚠️) | draft |
| 7 | network-testing | Ağ testi | Gecikme/kopma/simülasyon testleri (K019 ile sınır ⚠️) | draft |

### §3.1 Kademe-3 torun tablosu (comms/* — bounded-context, v2 birebir — 5)

| # | Torun | ELI15 rol (TASARIM İDDİASI) | Kanıt durumu | Durum |
|---|---|---|---|---|
| 1 | comms/http | İstek/yanıt üzerinden internet konuşması | — | draft |
| 2 | comms/websocket | Sürekli çift yönlü canlı bağlantı | — | draft |
| 3 | comms/webrtc | Doğrudan medya/veri yolu | IETF standardı iddiası — URL ⚠️ | draft |
| 4 | comms/tcp-ip | Yerel ağ düzeyi taşıma | — | draft |
| 5 | comms/device-communication | Evdeki cihazlar arası konuşma | — | draft |

> Bu beş düğümün **kendi index.md'leri** vardır (node_type: bounded-context);
> kademe-4 module listesi faz C'de üretilir (module'e inilmez).

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | WebRTC bir **standardtır** (IETF) — açık protokol olarak kabul edilir | ⚠️ VERIFY REQUIRED (görev beyanında URL yok) | 2026-10-12 | comms/webrtc kapsam onayı: standard protokol → sahiplik K014 |

**§4'e girmeyen hiçbir dil, framework, kütüphane veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| HTTP/WebSocket istemci kütüphaneleri | ⚠️ UNKNOWN |
| Taşıma katmanı dili/çatısı | ⚠️ UNKNOWN |
| Keşif protokolü (mDNS/DNS-SD vb. — hangisi) | ⚠️ UNKNOWN |
| Zaman hizalama yöntemi (multi-room) | ⚠️ UNKNOWN |
| Ağ testi aracı | ⚠️ UNKNOWN |

> "IETF" bir **standart kurumu** iddiasıdır; belge/metin URL'si tur-2'de toplanır.
> URL gelmeden §4/1 "verified" değil, **kayıtlı beyan**dır.

## §5 Bağımlılık özeti (tam metin: network-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K001-donanım | Ağ donanımı / sürücü | TASARIM İDDİASI |
| K009-security | TLS/şifreleme/anahtar | TASARIM İDDİASI |
| K010-identitiy | Cihaz kimliği ile yetki | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Arayüzün ağ çağrıları | TASARIM İDDİASI |
| K012-services | Servisler arası taşıma | TASARIM İDDİASI |
| K013-integraion | Dış API taşıması | TASARIM İDDİASI |
| K015-media | Akış/indirme taşıması | TASARIM İDDİASI |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011 · K012 · K013 · K015  ──(taşıma)──→  K014-network
K014-network
├─→ K001-donanım     (donanım/sürücü)
├─→ K009-security    (TLS/şifreleme)
└─→ K010-identitiy   (cihaz kimliği)
K014'ün yukarı dönme oku: YOK → döngü yok (2026-10-12)
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.14 ile birebir (7) + comms torunları (5) | metin karşılaştırma (12/12) | ✅ verified |
| 2 | Master dosyası adı registry §5/14 ile birebir (network-master.md) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırı yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt sütunu | ⚠️ kayıtlı beyan (URL eksik) |
| 5 | 5 dosyalık MD seti + 7 çocuk + 5 torun index'i | disk sayımı 2026-10-12 | ✅ verified |
| 6 | WebRTC standardı (IETF) resmi kaynağı | — | ⚠️ VERIFY REQUIRED (URL yok) |
| 7 | Kademe-4 module listesi | — | ⏳ faz C (ADR-097 K4) |

## §7 Açık sorular

1. handoff ile multi-room sınırı (teslim mi, senkron mu)? → ⚠️ VERIFICATION REQUIRED
2. offline-sync sahipliği: K014 mü, K015/offline mı, K012 mı? → ⚠️ VERIFICATION REQUIRED
3. service-discovery kapsamı: yerel cihaz keşfi mi, servis kaydı mı? → ⚠️ VERIFICATION REQUIRED
4. WebRTC standardı kanıtı için resmi belge URL'si → ⚠️ VERIFY REQUIRED
5. comms/beş torunun kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)
6. network-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED
7. device-communication hangi yerel protokolleri kullanır? → ⚠️ UNKNOWN
8. Zaman hizalama yöntemi (multi-room) nedir? → ⚠️ UNKNOWN

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.14 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk + torun listesi |
| Registry | .ai/architecture/index.md §5/14 · §8 | Master adı + isimlendirme |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| WebRTC = IETF standardı | ⚠️ URL yok | 2026-10-12 beyanı |

---

**Status:** draft · **Mode:** Red Team · Human Mode · Truth Mode · **Tarih:** 2026-10-12

---

## §9 comms Torun Ailesi Derinleştirmesi (TASARIM İDDİASI — v2 §2.14/§3 temelli)

> Aşağıdaki her satır **tasarım iddiasıdır**; yalnız `v2 §2.14` çocuk/torun adları
> (S2 kanıt) ve §3.1 tablosundaki roller doğrulanmıştır. Protokol ayrıntıları
> (port, timeout, retry, keepalive) için kaynak kod kanıtı **YOKTUR** — hepsi
> ⚠️ TASARIM İDDİASI etiketlidir ve kademe-4'te (faz C) netleşecektir.

### §9.1 comms/http — İstek/Yanıt Taşıması

| Boyut | Tasarım iddiası (⚠️) | Kanıt durumu |
|---|---|---|
| Rol | İstek/yanıt üzerinden internet konuşması | v2 §2.14 (S2 · birebir) |
| Taşıma katmanı | TLS üzerinden (K009-security politikası) | ⚠️ TASARIM İDDİASI |
| Tipik kullanım | uzak API çağrısı, katalog güncelleme, metadata çekme (K013-integraion taşıması) | ⚠️ TASARIM İDDİASI |
| Hata modeli | timeout · 4xx (istemci) · 5xx (sunucu) · retry politikası K012'de | ⚠️ TASARIM İDDİASI |
| Paket anlamı | K014 yalnız paketi taşır; JSON/protobuf anlamı K012/K013'te | §2.2 sınır tablosu |
| Bağlantı havuzu | yeniden kullanım (keep-alive) tasarım iddiası | ⚠️ UNKNOWN (boyut/süre) |
| Kanıt boşluğu | HTTP istemci kütüphanesi adı | ⚠️ UNKNOWN (§4 tablosu) |

### §9.2 comms/websocket — Sürekli Çift Yönlü Bağlantı

| Boyut | Tasarım iddiası (⚠️) | Kanıt durumu |
|---|---|---|
| Rol | Sürekli çift yönlü canlı bağlantı | v2 §2.14 (S2 · birebir) |
| Tipik kullanım | canlı çalma durumu yayını, oda senkron sinyalleri (multi-room), anlık bildirim | ⚠️ TASARIM İDDİASI |
| Keep-alive | ping/pong periyodu | ⚠️ UNKNOWN (değer yok) |
| Yeniden bağlanma | kopuşta exponential backoff | ⚠️ TASARIM İDDİASI (strateji) |
| Mesaj çerçevesi | JSON mu binary mu | ⚠️ UNKNOWN |
| Sınır | mesaj **içeriği** K012/K015'te; K014 yalnız kanalı sürer | §2.2 |
| Kanıt boşluğu | WebSocket kütüphanesi adı | ⚠️ UNKNOWN (§4 tablosu) |

### §9.3 comms/webrtc — Doğrudan Medya/Veri Yolu

| Boyut | Tasarım iddiası (⚠️) | Kanıt durumu |
|---|---|---|
| Rol | Doğrudan medya/veri yolu | v2 §2.14 (S2 · birebir) |
| Standard durumu | IETF standardı iddiası (§4/1) | ⚠️ VERIFY REQUIRED (URL yok — §7/4) |
| Tipik kullanım | ev içi cihazlar arası doğrudan ses/yayın (handoff · multi-room) | ⚠️ TASARIM İDDİASI |
| Traversal | STUN/TURN gerektirir (ağ NAT geçişi) | ⚠️ TASARIM İDDİASI — TURN sunucusu sahibi UNKNOWN |
| Medya codec'i | K015-media içerik biçimi; K014 yalnız yol | §2.2 |
| Güvenlik | DTLS/SRTP (K009 politika alanı) | ⚠️ TASARIM İDDİASI |
| Kanıt boşluğu | WebRTC kütüphanesi/sürümü | ⚠️ UNKNOWN (§4 tablosu) |

### §9.4 comms/tcp-ip — Yerel Ağ Düzeyi Taşıma

| Boyut | Tasarım iddiası (⚠️) | Kanıt durumu |
|---|---|---|
| Rol | Yerel ağ düzeyi taşıma | v2 §2.14 (S2 · birebir) |
| Tipik kullanım | evdeki cihazlar arası düşük gecikmeli bağlantı (device-communication tabanı) | ⚠️ TASARIM İDDİASI |
| Port aralığı | tanımlı port | ⚠️ UNKNOWN |
| Oturum yönetimi | socket yaşam döngüsü, reconnect | ⚠️ TASARIM İDDİASI |
| Sınır | donanım/sürücü K001'de; K014 yalnız socket mantığı | §2.2 |

### §9.5 comms/device-communication — Ev İçi Cihaz Konuşması

| Boyut | Tasarım iddiası (⚠️) | Kanıt durumu |
|---|---|---|
| Rol | Evdeki cihazlar arası konuşma | v2 §2.14 (S2 · birebir) |
| Alt protokoller | hangi yerel protokoller (mDNS · CoAP · özel binary?) | ⚠️ UNKNOWN (§7/7) |
| Cihaz keşfi | service-discovery ile kesişim | §9.6 |
| Kimlik | cihaz kimliği K010-identitiy'de; K014 yalnız taşır | §2.2 |
| Güvenlik | yerel trafik şifrelemesi K009 politikası | ⚠️ TASARIM İDDİASI |

### §9.6 comms ailesi özet matrisi (5 torun)

| Torun | Taşıma katmanı | Tipik tüketici (K014 içi) | Dış sınır |
|---|---|---|---|
| http | TCP+TLS | connectivity · service-discovery | K013-integraion |
| websocket | TCP+TLS | multi-room · handoff sinyalleri | K012-services |
| webrtc | UDP/SRTP | handoff · multi-room medya | K015-media codec |
| tcp-ip | TCP | device-communication · offline-sync yerel eşitleme | K001-donanım |
| device-communication | tcp-ip üstü | ev içi cihaz topluğu | K010-identitiy |

**§9'in kanıt oranı üzerinde etkisi YOKTUR:** yalnız mevcut v2 çocuk/torun adlarını
derinleştiren tasarım iddiası tablolarıdır; hiçbir satır "verified" statüsü kazandırmaz.

---

## §10 connectivity — Bağlantı Durum Makinesi (TASARIM İDDİASI)

### §10.1 Durumlar

| Durum | Tanım | Çıkış koşulu |
|---|---|---|
| DISCONNECTED | hiçbir bağlantı yok | ağ varlığı tespit edilirse → CONNECTING |
| CONNECTING | bağlantı kuruluyor (DNS · TCP handshake · TLS) | başarılı → CONNECTED · başarısız → DISCONNECTED |
| CONNECTED | bağlantı aktif, veri taşınabilir | bağlantı kopması → RECONNECTING |
| RECONNECTING | kopuş sonrası yeniden deneme | başarılı → CONNECTED · backoff doldu → DISCONNECTED |
| OFFLINE | kullanıcı/otomatik çevrimdışı modu | manuel/otomatik kural → CONNECTING |

### §10.2 Geçiş tablosu

| Kaynak \ Hedef | DISCONNECTED | CONNECTING | CONNECTED | RECONNECTING | OFFLINE |
|---|---|---|---|---|---|
| DISCONNECTED | — | ağ algılama | — | — | manuel |
| CONNECTING | handshake hatası | — | başarılı kurulum | — | — |
| CONNECTED | — | — | — | bağlantı kopması | manuel/otomatik |
| RECONNECTING | backoff doldu | yeniden deneme | başarılı | — | manuel |
| OFFLINE | — | kullanıcı açtı | — | — | — |

### §10.3 Bilinmeyen bağlantı parametreleri

| Parametre | Değer | Durum |
|---|---|---|
| Backoff başlangıç süresi | — | ⚠️ UNKNOWN |
| Backoff üst sınırı (max) | — | ⚠️ UNKNOWN |
| Backoff çarpanı (jitter dahil) | — | ⚠️ UNKNOWN |
| DNS timeout | — | ⚠️ UNKNOWN |
| TLS handshake timeout | — | ⚠️ UNKNOWN |
| Toplam bağlantı deneme limiti | — | ⚠️ UNKNOWN |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §11 service-discovery — Servis Keşfi (TASARIM İDDİASI · §7/3 açık soru ile)

### §11.1 İki aday kapsam (belirsiz — §7/3)

| Aday kapsam | Ne demek | Ait olabilir |
|---|---|---|
| Yerel cihaz keşfi | ağdaki hoparlör/cihazların bulunması (device-communication yakını) | K014/comms/device-communication |
| Servis kaydı | mikroservis/K012 servislerinin adres kaydı (ServiceRegistry deseni — K016'da kod var: ServiceRegistry.php) | K012/K016 |

### §11.2 Keşif mekanizması seçenekleri (ad yok — adlandırma yasak)

| Mekanizma | Tipik kullanım | Durum |
|---|---|---|
| mDNS / DNS-SD | yerel ağ cihaz keşfi | ⚠️ UNKNOWN (hangisi kullanılacak — §4 tablosu) |
| SSDP / UPnP | ev ağ cihaz keşfi | ⚠️ UNKNOWN |
| Merkezi registry | servis kaydı (K016 ServiceRegistry deseni) | ⚠️ UNKNOWN (K014 mü K012 mi) |
| Broadcast/Probe | özel yerel keşif paketi | ⚠️ UNKNOWN |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §12 offline-sync — Çevrimdışı Eşitleme Senaryoları (TASARIM İDDİASI · §7/2 açık soru ile)

### §12.1 Sahiplik belirsizliği (§7/2)

| Aday | Ne yapar | Durum |
|---|---|---|
| K014/offline-sync | bağlantı gelince kuyruğu eşitleme | v2 §2.14 çocuk (S2) |
| K015/offline | çevrimdışı medya kullanılabilirliği | v2 §2.15 çocuk (S2) |
| K012-services | iş paketi kuyruğu | v2 §2.12 (sınır §7/2) |

### §12.2 Eşitleme akışı (tasarım iddiası)

| Adım | Açıklama | Sorumlu (iddia) |
|---|---|---|
| 1 · çevrimdışı yazım | bağlantı yokken yapılan yerel yazım kuyruğa alınır | K014/offline-sync |
| 2 · kuyruk saklama | kuyruk yerelde saklanır (SQLite/queue — §17 edge case) | ⚠️ UNKNOWN depo |
| 3 · bağlantı algılama | connectivity CONNECTED durumuna geçiş | K014/connectivity |
| 4 · kuyruk boşaltma | sırayla gönder, onay bekle, işaretle | K014/offline-sync |
| 5 · çakışma çözümü | son-yazan-kazanır mı, uzlaşma mı | ⚠️ UNKNOWN (strateji) |
| 6 · hata | gönderim başarısızsa yeniden kuyruk | K014/offline-sync |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §13 handoff · multi-room — Teslim ve Senkron Senaryoları (TASARIM İDDİASI · §7/1 ile)

### §13.1 handoff — cihazlar arası teslim

| Adım | Açıklama | Durum |
|---|---|---|
| 1 · kaynak cihaz durumu | oynatma durumu (şarkı · pozisyon · ses seviyesi) toplanır | ⚠️ TASARIM İDDİASI |
| 2 · hedef keşfi | ağdaki uygun hedef cihazlar (device-communication) | ⚠️ TASARIM İDDİASI |
| 3 · devir sinyali | WebSocket/webrtc üzerinden "devral" mesajı | ⚠️ TASARIM İDDİASI |
| 4 · durum aktarımı | pozisyon/seviye hedefe uygulanır; kaynak duraklar | ⚠️ TASARIM İDDİASI |
| 5 · medya yolu | hedef kendi yerel kaynağından çalar (K015 playback) | §2.2 sınır |

### §13.2 multi-room — çok odalı senkron

| Boyut | Tasarım iddiası | Durum |
|---|---|---|
| Amaç | birden fazla odadaki cihazların zaman hizası | v2 §2.14 çocuk (S2) |
| Zaman hizalama yöntemi | NTP benzeri · PTS · master-slave clock | ⚠️ UNKNOWN (§7/8) |
| Hizalama toleransı | odalar arası kabul edilen sapma (ms) | ⚠️ UNKNOWN |
| Sinyal kanalı | WebSocket yayın · webrtc grup | ⚠️ TASARIM İDDİASI |
| Odak cihaz | bir cihaz "master" olur mu | ⚠️ UNKNOWN |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §14 network-testing — Ağ Testi Senaryo Matrisi (TASARIM İDDİASI · §7/6 ile)

### §14.1 Test senaryoları (K019-testing sınırında — §7/6)

| # | Senaryo | Ne ölçer | K014/K019 sınırı |
|---|---|---|---|
| 1 | bağlantı kopma simülasyonu | reconnect backoff davranışı | K014 (davranış) · K019 (altyapı) ⚠️ |
| 2 | yüksek gecikme (latency injection) | timeout/retry eşiği | ⚠️ sahiplik belirsiz |
| 3 | paket kaybı simülasyonu | UDP/webrtc dayanıklılığı | ⚠️ sahiplik belirsiz |
| 4 | bant daraltma (bandwidth throttle) | akış kalitesi düşüşü (adaptive) | ⚠️ sahiplik belirsiz |
| 5 | çevrimdışı → çevrimiçi geçiş | offline-sync kuyruk boşaltma | K014/offline-sync |
| 6 | ağ geçidi değişimi (Wi-Fi ↔ mobil) | bağlantı kesintisizliği | K014/connectivity |
| 7 | cihaz keşfi doğruluğu | service-discovery sonuçları | K014/service-discovery |

### §14.2 Ölçülebilir metrikler (tanımlanacak — değer yok)

| Metrik | Hedef | Durum |
|---|---|---|
| Bağlantı kurulum süresi (p50/p95) | — | ⚠️ UNKNOWN |
| Yeniden bağlanma süresi | — | ⚠️ UNKNOWN |
| Paket kaybı toleransı | — | ⚠️ UNKNOWN |
| Gecikme toleransı (multi-room) | — | ⚠️ UNKNOWN |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §15 Bilinmeyenler & VERIFICATION REQUIRED Envanteri (K014-network)

| # | Bilinmeyen | İlgili § | Durum |
|---|---|---|---|
| 1 | HTTP/WebSocket/WebRTC istemci kütüphane adları | §4 · §9 | ⚠️ UNKNOWN |
| 2 | TCP/IP port aralığı · keepalive değerleri | §9.4 | ⚠️ UNKNOWN |
| 3 | device-communication alt protokolleri (mDNS/CoAP/özel) | §9.5 · §7/7 | ⚠️ UNKNOWN |
| 4 | connectivity backoff/timeout parametreleri | §10.3 | ⚠️ UNKNOWN |
| 5 | service-discovery kapsam kararı (cihaz keşfi vs servis kaydı) | §11.1 · §7/3 | ⚠️ VERIFICATION REQUIRED |
| 6 | offline-sync sahipliği (K014/K015/K012) | §12.1 · §7/2 | ⚠️ VERIFICATION REQUIRED |
| 7 | multi-room zaman hizalama yöntemi | §13.2 · §7/8 | ⚠️ UNKNOWN |
| 8 | WebRTC = IETF standardı resmi kaynağı (URL) | §4/1 · §7/4 | ⚠️ VERIFY REQUIRED |
| 9 | network-testing ↔ K019-testing sahipliği | §14.1 · §7/6 | ⚠️ VERIFICATION REQUIRED |
| 10 | TURN sunucusu sahibi/konumu (WebRTC traversal) | §9.3 | ⚠️ UNKNOWN |
| 11 | offline-sync kuyruk depolama biçimi | §12.2/2 | ⚠️ UNKNOWN |
| 12 | handoff ↔ multi-room sınırı (teslim mi senkron mu) | §13 · §7/1 | ⚠️ VERIFICATION REQUIRED |

> Bu bölüm kanıt oranını DEĞİŞTİRmez — yalnız mevcut UNKNOWN/⚠️ kayıtlarını tek
> envanterde toplar; yeni iddia üretmez.

---

## §16 Ek Kaynaklar (§8'e ek — yalnız mevcut kaynaklar)

| Kaynak | Yer | Kullanım |
|---|---|---|
| §9–§15 (bu dosya) | network-master.md | tasarım iddiası derinleştirmesi (TASARIM İDDİASI) |
| §7 açık sorular | network-master.md §7 | 12 bilinmeyenin kök nedeni |
| Sınır tablosu | network-master.md §2.2 | komşu düğüm sahiplikleri |
| Bağımlılık detayı | network-bagimliliklar.md | giden/gelen bağımlılık ağacı |
| Kanıt defteri | network-dogrulama.md | verified/UNKNOWN ayrımı |

---

## §17 K014 Network — Bağlantı Katmanı Mimari Notları (TASARIM İDDİASI)

### §17.1 Katman sıralaması (bağlantı yığını — taslak)

| Katman | Görev | Sorumlu düğüm | Kanıt |
|---|---|---|---|
| Uygulama akışı | hangi veri taşınacak | K011/K012 | §2.2 |
| Protokol ailesi | http/ws/webrtc/tcp-ip/device-comm | K014/comms/* | §9 |
| Bağlantı hayatı | kurma · sürdürme · kopma | K014/connectivity | §10 |
| Keşif | ulaşılacak nokta | K014/service-discovery | §11 |
| Çevrimdışı | kuyruk · eşitleme | K014/offline-sync | §12 |
| Teslim/senkron | handoff · multi-room | K014/* | §13 |
| Donanım/radyo | fiziksel erişim | K001-donanım | §2.2 |
| Güvenlik | TLS/şifreleme/anahtar | K009-security | §2.2 |
| Kimlik | cihaz kimliği | K010-identitiy | §2.2 |

### §17.2 Bağlantı durumu veri modeli (tasarım iddiası)

| Alan | Tip | Açıklama | Durum |
|---|---|---|---|
| state | enum | §10.1'deki 5 durum | ⚠️ TASARIM İDDİASI |
| last_connected_at | timestamp | son başarılı bağlantı | ⚠️ TASARIM İDDİASI |
| retry_count | integer | aktif backoff deneme sayısı | ⚠️ TASARIM İDDİASI |
| endpoint | adres | hedef uç nokta | ⚠️ TASARIM İDDİASI |
| transport | enum | http/ws/webrtc/tcp-ip | ⚠️ TASARIM İDDİASI |

### §17.3 Hata sınıflandırması (bağlantı hataları)

| Sınıf | Örnek | Kategori | Davranış |
|---|---|---|---|
| Geçici | timeout · reset · congestion | recoverable | backoff + retry |
| Kalıcı | 404 · 403 · DNS NXDOMAIN | permanent | retry yok, hata bildir |
| Ağ yok | offline · no-route | offline | OFFLINE durumuna geç |
| Güvenlik | TLS handshake fail · cert expired | security | retry yok, K009'a rapor |

### §17.4 Çok-tenant / çok-cihaz senaryoları (tasarım iddiası)

| Senaryo | Zorluk | Tasarım iddiası |
|---|---|---|
| Aynı anda N cihaz keşfi | keşif kirliliği | tek keşif yöneticisi (singleton) |
| Aynı anda N WebSocket | kaynak tüketimi | bağlantı havuzu + tek writer |
| handoff sırasında kopma | yarım devir | transaction-like: doğrula → devret → onayla |
| multi-room + kopma | senkron kaybı | yeniden senkron clock alignment |

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §18 Bağlantı Katmanı Ölçüm Protokolü (TASARIM İDDİASI)

| # | Ölçüm | Yöntem (tasarım) | Eşik | Durum |
|---|---|---|---|---|
| 1 | bağlantı kurulum süresi | connect → first-byte zamanı | ⚠️ UNKNOWN eşik | ⚠️ TASARIM İDDİASI |
| 2 | yeniden bağlanma süresi | disconnect → reconnect başarılı | ⚠️ UNKNOWN eşik | ⚠️ TASARIM İDDİASI |
| 3 | çevrimdışı kuyruk derinliği | bekleyen yazım sayısı | ⚠️ UNKNOWN eşik | ⚠️ TASARIM İDDİASI |
| 4 | handoff devir süresi | "devral" sinyali → ilk paket | ⚠️ UNKNOWN eşik | ⚠️ TASARIM İDDİASI |
| 5 | multi-room sapma | cihazlar arası zaman farkı (ms) | ⚠️ UNKNOWN eşik | ⚠️ TASARIM İDDİASI |

> Ölçüm altyapısı K017-observability'de yaşar; K014 yalnız veri üretir (§2.2 sınırı).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §19 Bağlantı Güvenlik Notları (K009 sınırı — K014 yalnızca gözlem)

| # | Konu | K014'ün rolü | K009'un rolü | Not |
|---|---|---|---|---|
| 1 | TLS/şifreleme | yalnız çağırır | üretir/yönetir | K014'te şifreleme kodu YAZILMAZ |
| 2 | Anahtar yönetimi | dokunmaz | sahibi | K009 |
| 3 | Cihaz kimliği | taşır | doğrular | K010-identitiy |
| 4 | Yerel ağ güvenliği (device-communication) | paketleri taşır | politika K009'da | ⚠️ TASARIM İDDİASI |
| 5 | DTLS/SRTP (webrtc) | kanalı sürer | anahtar/negotiation | ⚠️ TASARIM İDDİASI |

> K014'te **şifreleme üreten** her kod satırı domain ihlalidir (§2.2 sınırı).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
