---
title: "K010-network — Network Master"
type: architecture
category: k-space-v3
date: 2026-10-12
updated: 2026-10-12
status: draft
version: 1.0.0
node_id: K010-network
role: master
authority: "SSOT: v2 §2.14 + .ai/architecture/index.md §5/14"
related: ["network-bagimliliklar.md", "network-dogrulama.md", "index.md"]
group: mimari
---


# K010-network — network-master.md

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
| Cihaz kimliği / yetki | K0100-identitiy | device-identity onun çocuğu |
| Dış API anlamı, adaptör | K013-integraion | K014 yalnız taşır |
| Servis keşfi/servis kaydı | K012/K016 vs K014 — belirsiz | §7/3 kapsam sorusu |
| Medya akışının içeriği/indirme mantığı | K014-media | Taşıma burada, içerik orada |
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
| 3 | comms/webrtc | Doğrudan medya/veri yolu | ✅ S1 kanıtlandı (W3C TR + RFC 8825/8826, 2026-10-15 — §4/1) | draft |
| 4 | comms/tcp-ip | Yerel ağ düzeyi taşıma | — | draft |
| 5 | comms/device-communication | Evdeki cihazlar arası konuşma | — | draft |

> Bu beş düğümün **kendi index.md'leri** vardır (node_type: bounded-context);
> kademe-4 module listesi faz C'de üretilir (module'e inilmez).

## §4 Teknoloji / ekosistem (YALNIZCA DOĞRULANMIŞ KANIT — 2026-10-12 · tur-3 eki 2026-10-15)

| # | İddia (kanıtlanmış) | Kanıt (URL / uç nokta) | Tarih | Bu domain'de kullanımı |
|---|---|---|---|---|
| 1 | WebRTC bir **standardtır** — çift standart: W3C API + IETF protokol seti | ✅ https://www.w3.org/TR/webrtc/ · https://www.rfc-editor.org/rfc/rfc8825 · https://www.rfc-editor.org/rfc/rfc8826 (HTTP 200) | 2026-10-15 | comms/webrtc kapsam onayı: standard protokol → sahiplik K014 — **§7/4 KAPANDI** |
| 2 | HTTP/1.1 · HTTP/2 · HTTP/3 standartları | ✅ RFC 9110/9112/9113/9114 (HTTP 200) | 2026-10-15 | comms/http — ayrıntı `http-standart-kanit.md` §1 |
| 3 | WebSocket standartı (RFC 6455) + HTTP/2 açılışı (RFC 8441) | ✅ https://www.rfc-editor.org/rfc/rfc6455 · rfc8441 (HTTP 200) | 2026-10-15 | comms/websocket — `websocket-standart-kanit.md` §1 |
| 4 | TCP/IP + yerel keşif standartları (RFC 9293/8200/6762/6763/7252/3927) | ✅ rfc-editor.org (HTTP 200) | 2026-10-15 | comms/tcp-ip · device-communication · service-discovery |
| 5 | Zaman hizası araç standartları (NTP RFC 5905 · RTP RFC 3550) | ✅ rfc-editor.org (HTTP 200) | 2026-10-15 | multi-room — `multi-room-standart-kanit.md` §1–§2 |
| 6 | Ağ ölçüm metodolojisi (RFC 2544) | ✅ https://www.rfc-editor.org/rfc/rfc2544 (HTTP 200) | 2026-10-15 | network-testing — `network-testing-standart-kanit.md` §1 |

**§4'e girmeyen hiçbir dil, framework, kütüphane veya sürüm BU BELGEDE YAZILMAZ.**

| Aranıp da kanıt bulunmayan (UNKNOWN — ad yazılmaz) | Durum |
|---|---|
| HTTP/WebSocket istemci kütüphaneleri | ⚠️ UNKNOWN |
| Taşıma katmanı dili/çatısı | ⚠️ UNKNOWN |
| Keşif protokolü (mDNS/DNS-SD vb. — hangisi) | ⚠️ UNKNOWN (adaylar S1 — `service-discovery-standart-kanit.md`) |
| Zaman hizalama yöntemi (multi-room) | ⚠️ UNKNOWN (araçlar S1 — RFC 5905/3550) |
| Ağ testi aracı | ⚠️ UNKNOWN (çalışma zamanı adayları S1 — RFC 2544 · asyncio · pytest · tokio · warp) |

> **Tur-3 notu (2026-10-15):** §4/1 artık **kayıtlı beyan değil, S1 kanıttır**
> (§30.5 — network-dogrulama.md). "Hangi kütüphane/seçim" satırları faz C'de açık kalır.

## §5 Bağımlılık özeti (tam metin: network-bagimliliklar.md)

### §5.1 Giden (depends_on →)

| Hedef | Ne için | Etiket |
|---|---|---|
| K001-donanım | Ağ donanımı / sürücü | TASARIM İDDİASI |
| K009-security | TLS/şifreleme/anahtar | TASARIM İDDİASI |
| K0100-identitiy | Cihaz kimliği ile yetki | TASARIM İDDİASI |

### §5.2 Gelen (← used_by)

| Kaynak | Ne için | Etiket |
|---|---|---|
| K011-application | Arayüzün ağ çağrıları | TASARIM İDDİASI |
| K012-services | Servisler arası taşıma | TASARIM İDDİASI |
| K013-integraion | Dış API taşıması | TASARIM İDDİASI |
| K014-media | Akış/indirme taşıması | TASARIM İDDİASI |
| Diğerleri | — | ⚠️ VERIFICATION REQUIRED |

### §5.3 Graf özeti (özet iddia — DAG denetimi 2026-10-12)

~~~text
K011 · K012 · K013 · K015  ──(taşıma)──→  K010-network
K010-network
├─→ K001-donanım     (donanım/sürücü)
├─→ K009-security    (TLS/şifreleme)
└─→ K0100-identitiy   (cihaz kimliği)
K014'ün yukarı dönme oku: YOK → döngü yok (2026-10-12)
~~~

## §6 Doğrulama özeti

| # | Ölçüt | Yöntem | Sonuç |
|---|---|---|---|
| 1 | Çocuk listesi v2 §2.14 ile birebir (7) + comms torunları (5) | metin karşılaştırma (12/12) | ✅ verified |
| 2 | Master dosyası adı registry §5/14 ile birebir (network-master.md) | .ai/architecture/index.md okundu | ✅ verified |
| 3 | index.md 8 zorunlu alan | sayım | ✅ verified |
| 4 | §4 satırı yalnız 2026-10-12 beyanına dayanıyor | satır-başı kanıt sütunu | ✅ tur-3'te S1'e yükseldi (2026-10-15 — 6 satır URL'li) |
| 5 | 5 dosyalık MD seti + 7 çocuk + 5 torun index'i | disk sayımı 2026-10-12 | ✅ verified |
| 6 | WebRTC standardı resmi kaynağı | URL + HTTP 200 (W3C + RFC 8825/8826) | ✅ verified (2026-10-15 — §7/4 kapandı) |
| 7 | Kademe-4 module listesi | — | ⏳ faz C (ADR-097 K4) |

## §7 Açık sorular

1. handoff ile multi-room sınırı (teslim mi, senkron mu)? → ⚠️ VERIFICATION REQUIRED
2. offline-sync sahipliği: K014 mü, K015/offline mı, K012 mı? → ⚠️ VERIFICATION REQUIRED
3. service-discovery kapsamı: yerel cihaz keşfi mi, servis kaydı mı? → ⚠️ VERIFICATION REQUIRED
4. ~~WebRTC standardı kanıtı için resmi belge URL'si~~ → ✅ **KAPANDI (2026-10-15)**: W3C https://www.w3.org/TR/webrtc/ + IETF RFC 8825/8826 (HTTP 200 — network-dogrulama.md §30.5)
5. comms/beş torunun kademe-4 module listesi → ⚠️ VERIFICATION REQUIRED (faz C)
6. network-testing ↔ K019-testing sahipliği → ⚠️ VERIFICATION REQUIRED (araçlar S1 kanıtlandı — sahiplik kararı bekliyor)
7. device-communication hangi yerel protokolleri kullanır? → adaylar S1 kanıtlandı (mDNS/DNS-SD · CoAP · link-local — `device-communication-standart-kanit.md`); **seçim** ⚠️ UNKNOWN (faz C)
8. Zaman hizalama yöntemi (multi-room) nedir? → araçlar S1 kanıtlandı (RFC 5905 NTP · RFC 3550 RTP — `multi-room-standart-kanit.md`); **yöntem** ⚠️ UNKNOWN (faz C)

## §8 Kaynaklar

| Kaynak | Yer | Kullanım |
|---|---|---|
| v2 ağaç (§2.14 · §3 · §4.1–4.3) | G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md | Çocuk + torun listesi |
| Registry | .ai/architecture/index.md §5/14 · §8 | Master adı + isimlendirme |
| Karar | .ai/.decisions/draft/ADR-097-…-bant-rezervasyonu.md §5 | Rol sözlüğü, desen |
| WebRTC = IETF standardı | ✅ https://www.w3.org/TR/webrtc/ + RFC 8825/8826 (HTTP 200 — 2026-10-15, §4/1) | 2026-10-12 beyanı → 2026-10-15 S1'e yükseltildi |

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
| Standard durumu | çift standart: W3C API + IETF protokol seti (§4/1) | ✅ S1 (URL + HTTP 200 — 2026-10-15 · `webrtc-standart-kanit.md` §1) |
| Tipik kullanım | ev içi cihazlar arası doğrudan ses/yayın (handoff · multi-room) | ⚠️ TASARIM İDDİASI |
| Traversal | STUN/TURN gerektirir (ağ NAT geçişi) | ⚠️ TASARIM İDDİASI — TURN sunucusu sahibi UNKNOWN |
| Medya codec'i | K014-media içerik biçimi; K014 yalnız yol | §2.2 |
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
| Kimlik | cihaz kimliği K0100-identitiy'de; K014 yalnız taşır | §2.2 |
| Güvenlik | yerel trafik şifrelemesi K009 politikası | ⚠️ TASARIM İDDİASI |

### §9.6 comms ailesi özet matrisi (5 torun)

| Torun | Taşıma katmanı | Tipik tüketici (K014 içi) | Dış sınır |
|---|---|---|---|
| http | TCP+TLS | connectivity · service-discovery | K013-integraion |
| websocket | TCP+TLS | multi-room · handoff sinyalleri | K012-services |
| webrtc | UDP/SRTP | handoff · multi-room medya | K014-media codec |
| tcp-ip | TCP | device-communication · offline-sync yerel eşitleme | K001-donanım |
| device-communication | tcp-ip üstü | ev içi cihaz topluğu | K0100-identitiy |

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

## §15 Bilinmeyenler & VERIFICATION REQUIRED Envanteri (K010-network)

| # | Bilinmeyen | İlgili § | Durum |
|---|---|---|---|
| 1 | HTTP/WebSocket/WebRTC istemci kütüphane adları | §4 · §9 | ⚠️ UNKNOWN |
| 2 | TCP/IP port aralığı · keepalive değerleri | §9.4 | ⚠️ UNKNOWN |
| 3 | device-communication alt protokolleri (mDNS/CoAP/özel) | §9.5 · §7/7 | adaylar S1 (2026-10-15) — **seçim** ⚠️ UNKNOWN (faz C) |
| 4 | connectivity backoff/timeout parametreleri | §10.3 | ⚠️ UNKNOWN |
| 5 | service-discovery kapsam kararı (cihaz keşfi vs servis kaydı) | §11.1 · §7/3 | iki yol da S1 (2026-10-15) — **split kararı** ⚠️ VERIFICATION REQUIRED |
| 6 | offline-sync sahipliği (K014/K015/K012) | §12.1 · §7/2 | teknik yol S1 (2026-10-15) — **sahiplik** ⚠️ VERIFICATION REQUIRED |
| 7 | multi-room zaman hizalama yöntemi | §13.2 · §7/8 | araçlar S1 (RFC 5905/3550 — 2026-10-15) — **yöntem** ⚠️ UNKNOWN |
| 8 | ~~WebRTC = IETF standardı resmi kaynağı (URL)~~ | §4/1 · §7/4 | ✅ KAPANDI (2026-10-15 — W3C + RFC 8825/8826, HTTP 200) |
| 9 | network-testing ↔ K019-testing sahipliği | §14.1 · §7/6 | araçlar S1 (2026-10-15) — **sahiplik** ⚠️ VERIFICATION REQUIRED |
| 10 | TURN sunucusu sahibi/konumu (WebRTC traversal) | §9.3 | ⚠️ UNKNOWN |
| 11 | offline-sync kuyruk depolama biçimi | §12.2/2 | aday S1 (SQLite/IndexedDB — 2026-10-15) — **seçim** ⚠️ UNKNOWN |
| 12 | handoff ↔ multi-room sınırı (teslim mi senkron mu) | §13 · §7/1 | teslim yolu S1 (2026-10-15) — **sınır** ⚠️ VERIFICATION REQUIRED |

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
| Kimlik | cihaz kimliği | K0100-identitiy | §2.2 |

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
| 3 | Cihaz kimliği | taşır | doğrular | K0100-identitiy |
| 4 | Yerel ağ güvenliği (device-communication) | paketleri taşır | politika K009'da | ⚠️ TASARIM İDDİASI |
| 5 | DTLS/SRTP (webrtc) | kanalı sürer | anahtar/negotiation | ⚠️ TASARIM İDDİASI |

> K014'te **şifreleme üreten** her kod satırı domain ihlalidir (§2.2 sınırı).

> Bu bölüm kanıt oranını DEĞİŞTİRmez.

---

## §20 Ulaşım Katmanı Karşılaştırması: TCP · UDP · QUIC (2026-10-17 derinleştirme)

> **Kaynak:** RFC 9000 (QUIC, datatracker 200 · 2026-10-17) · RFC 9110 (HTTP Semantics, rfc-editor 200) ·
> RFC 9114 (HTTP/3, datatracker 200). Aşağıdaki tablolar yalnız bu belgelerin dayandığı
> özelliklerdir; şirket blogu/ikincil kaynak dayanağı YOKTUR.

### §20.1 Semantik Matris

| Özellik | TCP (RFC 9293) | UDP (RFC 768) | QUIC (RFC 9000) |
|---|---|---|---|
| Bağlantı durumu | sunucu-istemci, 3-way handshake | bağlantısız | bağlantılı ama uygulama katmanında (connection ID) |
| Güvenilirlik | sıralı, tekrarlı teslimat | YOK | isteğe bağlı stream başına güvenilirlik |
| Sıralama | tek byte akışı | yok | stream başına bağımsız sıralama |
| Bileşikleme (multiplexing) | yok (head-of-line) | yok | stream başına — HOL bloking YOK |
| Şifreleme | üst katmana devredilir (TLS ayrı) | yok (üst katmana devredilir) | entegre (TLS 1.3, RFC 8446) |
| Zaman uyumu (0-RTT) | yok | yok | RFC 9000 §4.1 — 0-RTT veri (tekrar saldırısı dikkati §21.6) |
| Kullanım alanı (K010) | TCP: HTTP/1.1, cihaz komutu | UDP: RTP, mDNS | HTTP/3, gelecekteki media aktarımı |

### §20.2 K010 Eşleme Kararı (TASARIM İDDİASI)

| Akış | Seçilen taşıma | Gerekçe (bağlı olduğu bölüm) |
|---|---|---|
| HTTP API (sunucu↔istemci) | TCP + TLS 1.3 (HTTP/2) | yerleşik ekosistem, araç olgunluğu (§22) |
| Web real-time (tarayıcı) | WebSocket over TCP | tarayıcı kısıtı (§28) |
| Yerel ağ cihaz keşfi | UDP 5353 (mDNS) | yay (multicast) zorunlu (§26) |
| Ses aktarımı (gelecek) | UDP + RTP (RFC 3550) | düşük gecikme önceliği (§25) |
| HTTP/3 | **PLANNED — değerlendirilecek** | QUIC faydası büyük ağda ölçülmeli (§27) |

> §20.2 **TASARIM İDDİASI**'dır; QUIC/HTTP3'ün K010'a alınması ölçüme (K017) bağlıdır.

---

## §21 TLS 1.3 El Sıkışması (RFC 8446 — derinlemesine)

> **Kaynak:** RFC 8446 (datatracker 200 · 2026-10-17). Bu bölüm el sıkışma akışını
> adım adım özetler; K009 (güvenlik) sahibidir — burada şifreleme KODU üretilmez (§19).

### §21.1 1-RTT El Sıkışma (normal akış)

```
İstemci                                Sunucu
  |  ClientHello (desteklenen sürümler,   |
  |  key_share, rastgele)                 |
  |--------------------------------------->|
  |                                  [sunucu key üretir]
  |  ServerHello (seçilen sürüm, key_share)|
  |  {encrypted extensions, sertifika,    |
  |   sertifika doğrulama, finished}      |
  |<---------------------------------------|
  |  {finished}                           |
  |--------------------------------------->|
  |  [uygulama verisi — şifreli]          |
```

### §21.2 0-RTT (yeniden bağlanma)

| Adım | Taşınan | Risk notu |
|---|---|---|
| 1 | İstemci `pre_shared_key` + `early_data` gönderir | tekrar saldırısı — RFC 8446 §4.1: 0-RTT verisi **idempotent** olmalı |
| 2 | Sunucu `hello_retry_request` ile eşleşmeyi reddedebilir | reddedilirse 1-RTT'ye düşer |

> **K010 kararı (TASARIM İDDİASI):** 0-RTT yalnız GET/幂等 (idempotent) istekte;
> POST/ödeme gibi isteklerde KAPALI (tekrar riski).

### §21.3 Şifre takımı seçimi

| RFC 8446 §4.2 kuralı | Uygulama |
|---|---|
| yalnız AEAD | AES-GCM · ChaCha20-Poly1305 |
| RC4, CBC-dizi, RSA anahtar değişimi | **kaldırıldı** (1.3'te yok) |
| imzalama | Ed25519 · ECDSA-P256 · RSA-PSS (≥3072) |

### §21.4 Sertifika doğrulama adımları (istemci tarafı)

1. Zincir çözümleme (root → intermediate → leaf)
2. Süre kontrolü (`notBefore` / `notAfter`)
3. İptal kontrolü — RFC 5280 CRL/OCSP; **RFC 9110 §4.3**: tarayıcılar OCSP'i sıkıştırılmış
   (stapling) ister
4. Host doğrulama (SNI + SubjectAltName)

### §21.5 K010'da TLS'nin rolü (özet — §19 ile aynı)

| K010 yüzeyi | TLS | Not |
|---|---|---|
| PHP sunucu | TLS sonlandırma (web sunucusu) | uygulama kodunda `openssl_*` ÇAĞRILMAZ |
| JS istemci | tarayıcı yerleşik | `wss://` zorunlu |
| C++ (Neva Engine) | platform kütüphanesi (Schannel/OpenSSL) | seçimi faz C |
| Cihaz (device-communication) | **TLS YOK** (yerel ağ) — DTLS planlanır | §19/§20.2 |

### §21.6 Yeniden kullanım tuzağı (replay)

| Koruma | Mekanizma | Kaynak |
|---|---|---|
| anti-replay penceresi | `record_number` + `early_data` sınırı | RFC 8446 §4.1.3 |
| sunucu politikası | `max_early_data_size` (0 önerilir — K010) | RFC 8446 §4.2.10 |

> Bu bölüm kanıt oranını DEĞİŞTİRmez; TLS kararları K009'dadır.

---

## §22 HTTP/2 vs HTTP/3 (RFC 9113 · RFC 9114)

> **Kaynak:** RFC 9113 (rfc-editor 200) · RFC 9114 (datatracker 200) · RFC 9204 (QPACK, rfc-editor 200) —
> tümü 2026-10-17 doğrulandı.

### §22.1 Çerçeve karşılaştırması

| Konu | HTTP/2 (RFC 9113) | HTTP/3 (RFC 9114) |
|---|---|---|
| taşıma | TCP + TLS 1.3 | QUIC (TLS 1.3 entegre) |
| akış | stream (HTTP mesajı) | stream (HTTP mesajı) |
| çoklama | TCP üstünde | QUIC stream başına — TCP HOL etkisi yok |
| başlık sıkıştırma | HPACK (RFC 7541) | QPACK (RFC 9204) |
| açılış | TCP handshake + TLS 1.3 (1-RTT + 1-RTT) | QUIC: 1-RTT (yeniden: 0-RTT) |
| bağlantı kaybı | tüm akışlar etkilenir | yalnız ilgili stream |

### §22.2 QPACK (RFC 9204) — kısa not

| QPACK kuralı | Açıklama |
|---|---|
| iki yönlü tablo | encoder/decoder ayrı — TCP sıralaması gerektirmez |
| stream sıralaması | HTTP/3'te stream'ler sıralı OLMAYABİLİR; QPACK bunu telafi eder (§4) |

### §22.3 K010 karar matrisi

| Durum | Karar | Gerekçe |
|---|---|---|
| Mevcut PHP API | HTTP/1.1 + keep-alive (TLS sonlandırma web sunucusunda) | en düşük karmaşıklık |
| Web UI ↔ API | HTTP/2 DEĞERLENDİRİLEBİLİR | araç olgunluğu yüksek |
| Gelecek (ölçüm sonrası) | HTTP/3 | RFC 9114 — QUIC faydası ancak ölçümle kanıtlanır (§18, §27) |

---

## §23 Socket Seçenekleri Kataloğu (platformlar arası)

> **Kaynak yok (belge değil):** Bu tablo bilinen POSIX/Windows socket seçeneklerinin
> K010 açısından özeti; her satır işletim sistemi belgeleriyle **çapraz doğrulanmalıdır**
> (⚠️ VERIFICATION REQUIRED — ölçümler K017'de).

### §23.1 POSIX (Linux/macOS) — `setsockopt` seviyesi

| Seçenek | Seviye | Amaç | K010 kullanımı |
|---|---|---|---|
| `SO_REUSEADDR` | SOL_SOCKET | adres yeniden kullanım | sunucu |
| `SO_REUSEPORT` | SOL_SOCKET | çoklu process bind | PLANNED (ölçüm) |
| `TCP_NODELAY` | IPPROTO_TCP | Nagle kapat | gerçek zamanlı komut |
| `SO_KEEPALIVE` | SOL_SOCKET | OS seviyesi keepalive | uzun bağlantı |
| `TCP_KEEPIDLE/INTVL/CNT` | IPPROTO_TCP | keepalive zamanlamarı | uzun bağlantı |
| `SO_RCVTIMEO/SNDTIMEO` | SOL_SOCKET | zaman aşımı | hata yönetimi |

### §23.2 Windows — karşılıkları

| POSIX | Windows karşılığı | Not |
|---|---|---|
| `SO_REUSEADDR` | `SO_REUSEADDR` | ⚠️ fark: Windows'ta `SO_EXCLUSIVEADDRUSE` daha güvenli |
| `TCP_KEEPIDLE/INTVL/CNT` | `SIO_KEEPALIVE_VALS` (ioctl) | ayrı API |
| `TCP_NODELAY` | `TCP_NODELAY` | aynı |

### §23.3 Cihaz tarafı (embedded) kısıtları

| Kısıt | Etki | Karar |
|---|---|---|
| bellek | tampondaki her bayt maliyet | `SO_RCVBUF` küçük tutulur (⚠️ ölçüm) |
| RTOS | `select/poll` desteği sınırlı | non-blocking I/O + tek döngü |

> §23 tamamı **TASARIM İDDİASI**'dır — somut sayılar ölçüm sonrası yazılır.

---

## §24 Bağlantı Havuzu Tasarımı (Connection Pooling)

### §24.1 Havuz parametreleri (TASARIM İDDİASI)

| Parametre | Açıklama | K010 değeri |
|---|---|---|
| max connections | hedef başına eşzamanlı bağlantı | ⚠️ UNKNOWN (ölçüm) |
| max idle | boşta bekleyen üst sınır | ⚠️ UNKNOWN |
| idle timeout | boşta bağlantı sonlandırma | ⚠️ UNKNOWN |
| acquire timeout | havuzdan bağlantı bekleme | ⚠️ UNKNOWN |

### §24.2 Dil başına yerleşik havuz

| Dil | Yerleşik havuz | Not |
|---|---|---|
| C# | `SocketsHttpHandler` (ConnectionPool) | `MaxIdleConnections` ayarlanır |
| Go | `http.Transport` + `MaxIdleConns` | yerleşik |
| Python | `aiohttp.TCPConnector` | `limit`, `ttl_dns_cache` |
| JS (Node) | `http.Agent` (`keepAlive: true`) | `maxSockets` |
| Rust | reqwest `Pool` (hyper) | `pool_max_idle_per_host` |
| PHP | PDO bağlantısı (istek başına değil) | PHP-FPM modeli havuz = worker sayısı |

### §24.3 Havuz tuzakları

| Tuzağ | Sonuç | Önlem |
|---|---|---|
| havuz kirliliği | socket/fd tükenmesi | idle timeout zorunlu |
| havuz + DNS değişimi | ölü IP'ye bağlanma | TTL sonunda yeniden çöz |
| havuz + retry | yığılma (thundering herd) | §25 backoff |

---

## §25 Keep-Alive · Retry · Backoff Politikaları

### §25.1 Üç katmanlı keepalive

| Katman | Mekanizma | Sahip |
|---|---|---|
| TCP | `SO_KEEPALIVE` (OS) | işletim sistemi |
| uygulama | ping/pong (WebSocket: RFC 6455 §5.5.2 — **⚠️ VERIFICATION REQUIRED: RFC 6455 doğrulanmadı**) | uygulama |
| HTTP | `Connection: keep-alive` + süre (RFC 9110 §9.3) | HTTP katmanı |

### §25.2 Backoff formülü (TASARIM — ölçümle doğrulanacak)

```
d_n = min(cap, base × multiplier^n) ± jitter
```

| Parametre | Öneri (geleneksel başlangıç — ⚠️ ölçüm) |
|---|---|
| base | 500 ms |
| multiplier | 2.0 |
| cap | 30 s |
| jitter | tam (full jitter — AWS rehberi, **kaynak doğrulanmadı** ⚠️) |
| max deneme | uygulamaya göre (5–10) |

> Backoff sayıları **TASARIM İDDİASI**'dır; RFC dayanağı YOKTUR — uygulamada ölçülür.

### §25.3 Hangi hata retry edilir?

| Hata | Retry? | Not |
|---|---|---|
| bağlantı kurulamadı (ECONNREFUSED) | evet (backoff) | sunucu iniyor olabilir |
| okuma zaman aşımı | dikkatli | yanıt kısmi olabilir |
| HTTP 503 | evet (Retry-After varsa) | RFC 9110 §15.6.9 |
| HTTP 4xx | hayır | istemci hatası |
| idempotent olmayan POST | **hayır** | tekrar yan etki riski |

---

## §26 mDNS Yerel Keşif (RFC 6762 — derinleştirme)

> **Kaynak:** RFC 6762 (rfc-editor 200 · 2026-10-17).

### §26.1 Multicast adresleri

| Protokol | Adres | Port |
|---|---|---|
| mDNS | 224.0.0.251 (IPv4) · ff02::fb (IPv6) | 5353 |
| DNS-SD | (aynı paket içinde) | 5353 |

### §26.2 Sorgu/zamanlama kuralları (RFC 6762 §5–§6)

| Kural | Değer (RFC 6762) |
|---|---|
| rastgele gecikme (query jitter) | 20–120 ms (§5.2) |
| unicast yanıt tercihi | `QU` biti (§5.4) |
| TTL (kayıt) | uygulama belirler; önerilen üst sınır 75 dk (§10.2) |
| probing (çakışma) | kayıttan önce 3 probe (§8.1) |

### §26.3 K010 uygulama notları (TASARIM İDDİASI)

| Not | Açıklama |
|---|---|
| tek keşif yöneticisi | §17'deki "keşif kirliliği" — mDNS de tek süreçte toplanır |
| yay kuralı | router çoğu yay trafiğini keser — LAN dışı keşim YOKTUR |
| fallback | mDNS başarısız → statik IP/manuel eşleşme (§27) |

---

## §27 Ağ Hata Modu Analizi (derinleştirme)

### §27.1 Hız kaybı → geri çekilme (TASARIM)

| Aşama | Eşik (⚠️ ölçüm) | Davranış |
|---|---|---|
| yavaş | ping RTT > taban × 2 | sıkıştırma denemesi (medya) |
| çok yavaş | ping timeout × 2 | akış durdur, yeniden bağlan |
| kesildi | ping timeout × 3 | §25 backoff ile reconnect |

### §27.2 Ağ tipi davranışı (TASARIM İDDİASI)

| Ağ | Tipik sorun | K010 davranışı |
|---|---|---|
| Wi-Fi (ev) | erişim noktası değişimi | reconnect + yeniden keşif (§26) |
| kablolu | kararlı, düşük kayıp | keepalive süresi uzatılabilir |
| taşınabilir (telefon hotspot) | IP değişimi, NAT | bağlantı kimliği yenilenir (QUIC connection ID faydası §20) |
| izole (LAN) | yay kısıtlı | §26 fallback |

> §27.1–27.2 **TASARIM İDDİASI**'dır; RFC dayanağı yoktur, ölçüm K017'dedir.

---

## §28 WebSocket vs SSE (karar matrisi — §28 derinleştirme)

> **Kaynak:** MDN WebSocket API (200) · MDN Server-sent events (200) — 2026-10-17 doğrulandı.

### §28.1 Karşılaştırma

| Konu | WebSocket | SSE |
|---|---|---|
| yön | çift yönlü | sunucu→istemci tek yönlü |
| taşıma | kendi protokolü (RFC 6455) | HTTP (text/event-stream) |
| veri tipi | metin + binary | yalnız metin (UTF-8) |
| proxy uyumu | bazı proxy'ler engeller | HTTP olduğu için uyumlu |
| reconnect | uygulama sorumlu | tarayıcı yerleşik (`EventSource`) |
| K010 kullanımı | cihaz komutu/ durum | olay akışı (öneri) |

### §28.2 K010 karar kuralı

| İhtiyaç | Seçim |
|---|---|
| istemciden sunucuya komut (mobile → server) | WebSocket (§28.1) |
| yalnız sunucu→istemci olay (güncelleme akışı) | SSE (daha basit, §28.1) |
| binary (ses verisi) | WebSocket (SSE text-only) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
