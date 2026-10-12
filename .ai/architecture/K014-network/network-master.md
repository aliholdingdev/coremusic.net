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
