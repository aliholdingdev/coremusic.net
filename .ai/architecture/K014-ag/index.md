---
title: "K014 NETWORK «ÇARK» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K014-ag/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: ag
ssot: true
risk: medium
owner: backend
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K014 NETWORK «ÇARK» — Katman Index

> **Authority:** Bu dosya K014 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md` §A.1 K014 kartı > `.ai/CLAUDE.md` §5/§22 > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md` §2 (hibrit kayıt · dizin deseni · çift uçak).
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Vault'a yazılmadı (staging).
> **Uçak:** SOFTWARE PLANE (anayasa §A.0 anahtar tablosu: `K014 · NETWORK · «ÇARK» · SOFTWARE`).

## Künye

| Alan | Değer |
|---|---|
| K-ID | K014 |
| Kanonik Ad | NETWORK |
| Teatral Epitet | «ÇARK» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2 — SOFTWARE PLANE K000→K15+) |
| Dizin deseni | `.ai/architecture/K014-ag/index.md` (R2.2 — dizin henüz üretilmedi; `.ai/architecture/` altında yalnız 00-kspace-anayasa · 00-master-index · rules · context var, ls 2026-10-08) |
| Tier / Domain | 3 / ag |
| Owner (`.ai/AGENTS.md` §4 registry) | backend (Backend Architect — `backend`, AGENTS.md L81 ls-okundu) |
| Risk | medium — ağ yüzeyi geniş (protokol/keşif) ama doğrudan kullanıcı-güvenlik bypass'ı K006/K007'de; high değil (R4.4: risk=high yalnız kritik-kart) |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-031 · ADR-037 · ADR-039 · ADR-084 · ADR-086 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K014-K020) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K014 NETWORK «ÇARK», CoreMusic'in ağ iletişim standardıdır: anayasa §5 K14 satırı bu katmanı
"Ağ iletişim standardı (K15, K10-13)" olarak tanımlar — yani K015 MEDIA akışı ile K010-K13
uygulama/izleme/dağıtım katmanlarının iletişim kuracağı tek meşru ağ yüzeyidir. Kapsamı
taşıma/protokol/keşif (TCP/IP → HTTP/S → WebSocket → WebRTC → DNS/TLS → LAN/WAN → DLNA/UPnP/
mDNS/AirPlay) ile çevrimdışı davranış (Offline Sync · Handoff · Multi-Room) katmanlarıdır.
Repo durumu bu kapsamın büyük bölümünü PLANNED olarak işaretler: `grep -ril "webrtc|dlna|upnp|
airplay|mdns|websocket"` (shared/src + assets.coremusic.net, 2026-10-08) **0 isabet** verdi; buna
karşılık HTTP taşıma ayağı (gateway/routing) ve kablosuz-senkron veri şeması disktedir.
Kapsam dışı: güvenlik kararı (K006), istek hattı kademeleri (K007), medya kodlama/akış (K015),
donanım-kablosuz radyo (K001).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K014 | NETWORK | «ÇARK» | ag | POSIX/OS ağ yığını (K000) + PHP 8.4 gateway (K009 köprüsü) · hedef: HTTP/3 + WebRTC + keşif (mDNS/UPnP-DLNA) | TCP/IP · HTTP/S · WebSocket · WebRTC · DNS · TLS · LAN/WAN · Offline Sync · Handoff · Multi-Room (EK A §A.1 · 40 bileşen — anayasa §5 K14) | paket/bağlantı katmanı girdisi (OS socket) · K009/K010 istek akışı · K015 medya akış talebi · ADR-037 keşif olayları | doğrulanmış oturum/tünel (TLS) · keşif kaydı (peer/profile) · senkron olayları · offline-kuyruk olayı | K000-K013 (EK A aralık: OS ağ yığını + sürücü/K002 + port/adapter) | K014 → K015-K020 üst/sağ katmanlara doğrudan erişim (H20) · K014 → medya kodlama/akış (K015 işi) · H19 doğrudan veri paylaşımı · K006/K007 kararlarını atlamak | yalnız bağlantı envanteri + senkron geçmişi (`coremusic_wireless.sql`: wifi_networks · network_profiles · bluetooth_peers · bluetooth_audio_profiles · sync_history — ls 2026-10-08); iş verisi K005'te kalır | TLS/zaman aşımı politikaları K006 ile hizalı; keşif/eşleştirme güvenliği ADR-037 (güvenlik bölümü); istek doğrulama K006/K007'de — K014 geçersizleştiremez | fail-over (EK A) · ağ kesintisi → Offline-First + SQLite kuyruk (anayasa §22) · keşif_timeout → protokol fallback (ADR-037 çoklu-protokol) | bağlantı/senkron olay logu + `sync_history` satırları + gateway erişim logları; merkezi metrik (Prometheus) PLANNED | `shared/tests/Api/GatewayMethodRoutingTest.php` (ls 2026-10-08) — HTTP method/routing sözleşmesi; protokol katmanı testi YOK (hedef test tanımı §6.4) | repo: `shared/src/Api/Gateway.php` + `shared/tests/Api/GatewayMethodRoutingTest.php` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K14 + §22` + `00-kspace-anayasa.md §A.1 K014` · ADR: ADR-031/037/039/084/086 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B research kapısı) |

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K014 |
| 2 | KANONİK_AD | NETWORK |
| 3 | TEATRAL_EPİTET | «ÇARK» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | ag |
| 5 | SUBDOMAIN | transport-tcp-ip · http-websocket · realtime-webrtc · discovery-mdns-upnp-dlna · naming-tls-dns · lan-wan · offline-sync · handoff · multi-room |
| 6 | BOUNDED_CONTEXT | Connectivity & Discovery — bağlantı, keşif ve senkron boru hattı; medya içeriği üretmez, kimlik kararı vermez |
| 7 | RUNTIME | OS ağ yığını (K000: TCP/IP stack, socket) · PHP 8.4 gateway tarafı (K009 köprü) · hedef çalışma zamanları: QUIC/HTTP-3, WebRTC, mDNS/UPnP-DLNA keşif süreçleri (PLANNED) |
| 8 | SORUMLULUK | TCP/IP · HTTP/S · WebSocket · WebRTC · DNS · TLS · LAN/WAN · Offline Sync · Handoff · Multi-Room (EK A §A.1 K014 sorumluluk satırı) |
| 9 | GIRDI | OS socket/paket girdisi (K000) · K009 gateway istek akışı · K015 medya akış/teslim talebi (yalnızca K14 üzerinden — anayasa §5 K15 kısıtı) · ADR-037 keşif/eşleştirme tetikleyicileri · K010 senkron tetikleyicisi (offline mod) |
| 10 | CIKTI | TLS ile doğrulanmış bağlantı oturumu · keşif kaydı (peer/profile — `coremusic_wireless`) · senkron/akış olayları · offline-kuyruk olayı · üst katmana yalnız olay (event) ve arayüz (API) çıktısı |
| 11 | IZINLI_BAGIMLILIK | K000 OS (socket/DNS/TLS yığını), K001-K002 donanım/sürücü (radyo/ethernet — driver/API sınırı), K003-K013 alt katman aralığı + port/adapter (EK A §A.1 "izinli=K000-K013"), PSR/event köprüsü (K008 Event Bus — K014 yalnız yayın/abone) |
| 12 | YASAK_BAGIMLILIK | K014 → K015-K020 doğrudan erişim / geri çağrı (H20) · medya kodlama-akış içeriğine dokunmak (K015) · K006/K007 doğrulama/CSRF/CSP kararlarını atlamak · katmanlar arası doğrudan veri paylaşımı (H19) · istek hattı kademelerini (anayasa §6) yeniden uygulamak |
| 13 | DATA_BOUNDARY | Yalnız bağlantı envanteri + senkron geçmişi: `wifi_networks` · `network_profiles` · `bluetooth_peers` · `bluetooth_audio_profiles` · `sync_history` (`.ai/.sql/mysql/coremusic_wireless.sql` — ls 2026-10-08). İş/katalog/medya verisine yazmaz; transfer edilen payload'un sahibi K005/K015'tir |
| 14 | SECURITY_BOUNDARY | TLS sonlandırma politikası + zaman aşımı K006 ile hizalı (K014 politika üretmez, K006 kararını uygular) · eşleştirme/keşif güvenliği ADR-037 kapsamında (BLE pairing + çoklu-protokol) · istek doğrulama (auth/CSRF/CORS) K006/K007'de — K014 ATLANAMAZ · port/servis kaydı anayasa §11 (80/81/3001/3306/5000/6000/9741/9742) |
| 15 | FAILURE_MODE | fail-over (EK A §A.1 K014 sınır satırı) · ağ kesintisi → Offline-First + SQLite kuyruk (anayasa §22 edge case) · keşif protokolü başarısız → bir sonraki protokole fallback (ADR-037 çoklu-protokol otomatik seçim) · kesinti-sonrası yeniden bağlanma + `sync_history` ile yakalama |
| 16 | OBSERVABILITY | bağlantı/senkron olayları + `sync_history` satırları · gateway erişim/latency logları (K012'ye aktarım) · keşif timeout sayaçları; merkezi metrik/trace (Prometheus/Grafana) PLANNED — anayasa §5 K12 "yalnız K8 servislerinden okuma" |
| 17 | TEST | `shared/tests/Api/GatewayMethodRoutingTest.php` (ls 2026-10-08 — HTTP method/routing sözleşmesi); protokol/keşif/senkron testleri YOK → hedef test tanımı §6.4 (fail-over, offline-queue, discovery-fallback) · hedef ≥80% (anayasa §17) |
| 18 | KANIT | repo: `shared/src/Api/Gateway.php` · `shared/tests/Api/GatewayMethodRoutingTest.php` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K14 satırı` + §11 port kaydı + §22 satırı + `00-kspace-anayasa.md §A.1 K014` · ADR: ADR-031/037/039/084/086/096 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (R9 — F1 EK B research kapısı) |
| 19 | KANIT_TARIHI | 2026-10-08 (repo grep/ls + vault read + accepted/ ls) |
| 20 | EPİTET_KALİTE_NOTU | «ÇARK» — hareketi/iletimi temsil eden fiziksel metafor; 1 epitet, K-ID'nin yanında (R2.3); EK A §A.0 anahtar satırı: `K014 · NETWORK · «ÇARK» · SOFTWARE` |

**R4.4 kart kapıları:**
(a) 20 alanın tamamı dolu — GEÇTİ · (b) IZINLI ∩ YASAK = ∅ — GEÇTİ (IZINLI aralığı K000-K013 + port/adapter;
YASAK kümesi K015-K020'e erişim + güvenlik-atlama + H19; aralık kesişimi yok) · (c) KANIT 3'lü format
(repo | vault/ADR | web ⚠️) — GEÇTİ; `⚠️` web ayağı R14 research kapısında doldurulur · (d) veri sınırı
tek katmana ait (yalnız bağlantı/senkron şeması; iş verisi K005) — GEÇTİ.

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K014 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: TCP/IP · HTTP/S · WebSocket · WebRTC · DNS ·
TLS · LAN/WAN · Offline Sync · Handoff · Multi-Room".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| TCP/IP | Paket/oturum taşıma katmanı (OS yığını üzerinden) | IMPLEMENTED (OS kaynağı) — K014'in kendi kodu değil, K000 taşıması | `00-kspace-anayasa.md §A.1 K000` (OS Networking) + `.ai/CLAUDE.md §5 K0` |
| HTTP/S | Uygulama katmanı taşıma + gateway geçidi | IMPLEMENTED (repo: gateway/routing) | `shared/src/Api/Gateway.php` (ls 2026-10-08) · `.ai/CLAUDE.md §6A.1` (api.coremusic.net) |
| WebSocket | Tam-çift yönlü kanal (Audio Service 9742 WS — anayasa §11) | PLANNED (grep: shared/src + assets 0 isabet, 2026-10-08) | `.ai/CLAUDE.md §10 #3` + §11 port 9742 · repo grep 0 → ⚠️ durum PLANNED |
| WebRTC | P2P gerçek-zamanlı akış/multi-room | PLANNED (grep 0 isabet) | `.ai/CLAUDE.md §5 K14 satırı` (WebRTC P2P) · §14 "WebRTC/P2P" (Network Audio servisi) |
| DNS | Ad çözümleme / keşif adlandırması | PLANNED (platform işletimi — deplomanda) | `.ai/CLAUDE.md §5 K14 satırı` · somut yapılandırma kanıtı YOK → ⚠️ |
| TLS | Uçtan-uca şifreleme; sertifika/sonlandırma politikası | PLANNED (sunucu yapılandırması repo'da değil) | `.ai/CLAUDE.md §5 K14 satırı` · `.ai/CLAUDE.md §12` (encryption AES-256-GCM/Argon2id — veri seviyesi, TLS ayrı) · ⚠️ |
| LAN/WAN | Yerel/geniş alan ağı senaryoları (NAS, araç, stüdyo) | PLANNED (deployment senaryosu — anayasa §14) | `.ai/CLAUDE.md §14 Deployment Modes` · §13 tier tablosu |
| Offline Sync | Çevrimdışı kuyruk + bağlantı dönünce yakalama | DESIGN (şema VAR: `sync_history`) — uygulama kodu yok | `.ai/.sql/mysql/coremusic_wireless.sql` `sync_history` (ls 2026-10-08) · `.ai/CLAUDE.md §22` (Offline-First + SQLite queue) |
| Handoff | Cihazlar arası kesintisiz teslim ("Aynı Müzik Her Yerde Seninle") | PLANNED (kod kanıtı YOK; grep "handoff" 0 isabet) | `.ai/CLAUDE.md §1` (felsefe/handoff) · `.ai/VISION.md` (bibliyografik referans — okunmadı → ⚠️) |
| Multi-Room | Çoklu-oda senkron çalma (Network Audio) | PLANNED (grep "multi-room" 0 isabet) | `.ai/CLAUDE.md §10 #5` (Network Audio — WebRTC/P2P) · §4.2 (NAS multi-room yeteneği) |

### §4.2 Anayasa §5 K-Matrix Satırı (K14) — 40 bileşen hattının açılımı

Kaynak: `.ai/CLAUDE.md §5` K0-K20 tablosu satırı: **K14 Ağ & İletişim | HTTP/3, WebRTC P2P, DLNA,
UPnP, AirPlay, mDNS | 40 bileşen | Ağ iletişim standardı (K15, K10-13).**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| HTTP/3 | QUIC tabanlı taşıma (düşük gecikme, çoklu-akış) | PLANNED (repo 0 isabet; hedef protokol) | `.ai/CLAUDE.md §5 K14 satırı` · grep (2026-10-08) 0 → ⚠️ |
| WebRTC P2P | Cihazlar arası doğrudan akış (Network Audio, multi-room) | PLANNED | `.ai/CLAUDE.md §5 K14` + §10 #5 · grep 0 |
| DLNA | ağ-üzeri medya keşif/teslim (yerel ağ cihazları) | PLANNED (ADR-037 kapsamı: UPnP-DLNA keşif) | ADR-037 başlığı (accepted/ ls) · grep 0 |
| UPnP | ağ-üzeri keşif/kontrol protokolü | PLANNED | ADR-037 (accepted/ ls) · grep 0 |
| AirPlay | Apple ekosistem teslim yolu | PLANNED | `.ai/CLAUDE.md §5 K14 satırı` · grep 0 |
| mDNS | yerel ağ ad çözümleme/keşif (cihaz bulma) | PLANNED | ADR-037 (accepted/ ls) · grep 0 |
| "40 bileşen" sayımı | §5 K14 bileşen sayısı (envanter sayımı — HEDEF) | HEDEF (anayasa envanteri) · alt-bileşen dökümü ⚠️ | `.ai/CLAUDE.md §5 K14 satırı` · H10: hedef ≠ kanıt — repo'da protokol implementasyonu gözlenmedi |
| Kısıt: "Ağ iletişim standardı (K15, K10-13)" | K015 ve K010-K13 bu katman üzerinden iletişim kurar | BAĞLAYICI (anayasa kısıtı) | `.ai/CLAUDE.md §5 K14 + K15 satırları` ("K15 Yalnızca K14 üzerinden iletişim") |

### §4.3 Protokol/Çağrı Bazlı Derinlik (10 kalem)

#### §4.3.1 Taşıma · HTTP/S + Gateway

| Boyut | İçerik |
|---|---|
| Kural | Tüm istemciler tek giriş noktasından geçer: `api.coremusic.net` (anayasa §6A.1); routing/auth/rate-limit/validation gateway'de |
| Repo durumu | `shared/src/Api/Gateway.php` + `shared/tests/Api/GatewayMethodRoutingTest.php` (ls 2026-10-08) — IMPLEMENTED (gateway ayağı) |
| Edge case | Yönlendirme zinciri (HTTP→HTTPS), port kaydı (§11: 80/81/3001/3306/5000-6000/9741/9742), host ayrımı (10 panel alt-domain) |
| Kapsam dışı | BFF/orkestrasyon (K009), CORS/CSRF (K006/K007) — K014 yalnız taşıma-yüzeyi |
| Kanıt | `shared/src/Api/Gateway.php` (ls) · ADR-084 (accepted/ ls) · `.ai/CLAUDE.md §6A.1` |

#### §4.3.2 Gerçek Zamanlı · WebSocket

| Boyut | İçerik |
|---|---|
| Kural | Audio Service ikinci kanalı WS: port 9742 (anayasa §11 Port Register) |
| Durum | PLANNED — `grep -ril "websocket"` shared/src + assets.coremusic.net → 0 isabet (2026-10-08) |
| Edge case | yeniden-bağlanma, kalp atışı (keep-alive), düşen istemci kuyruğu, WS↔REST ikiliği (9741/9742) |
| Hedef test | bağlantı kesme/yeniden kurma + mesaj sırası bozulmazlık (§6.4) |
| Kanıt | `.ai/CLAUDE.md §10 #3` + §11 port 9742 · repo grep 0 → ⚠️ |

#### §4.3.3 P2P · WebRTC

| Boyut | İçerik |
|---|---|
| Kural | Cihazlar arası doğrudan akış: Network Audio servisi (WebRTC/P2P — anayasa §10 #5) |
| Durum | PLANNED (grep 0 isabet) |
| Edge case | NAT geçişi (STUN/TURN) — TURN sunucu gerekliği gözardı edilemez; güvenli-oda doğrulaması K006'ya bağlı |
| Kapsam dışı | akış codec'i/çözünürlüğü (K015/K003), cihaz radyosu (K001) |
| Kanıt | `.ai/CLAUDE.md §5 K14` + §10 #5 · ⚠️ (TURN/STUN yapısı için research kapısı — R14) |

#### §4.3.4 Keşif · mDNS / UPnP / DLNA

| Boyut | İçerik |
|---|---|
| Kural | Yerel ağda cihaz keşfi; cihaz tipine göre otomatik protokol seçimi (ADR-037 çoklu-protokol) |
| Durum | DESIGN (ADR-037 accepted, tartışmalı karar 3 tur/20 persona) — repo uygulaması yok (grep 0) |
| Edge case | keşif timeout → sıradaki protokol · izole ağ (guest Wi-Fi) · çok-aynı-ismi (mDNS çakışması) |
| Güvenlik | eşleştirme/kaynak doğrulama ADR-037 §güvenlik kapsamındadır; K014 keşif paketini K006 politikasız yorumlamaz |
| Kanıt | ADR-037 (accepted/ ls · başlık: "Ağ mDNS/UPnP-DLNA · çoklu-protokol, cihaz tipine göre otomatik seçim") · grep 0 → ⚠️ |

#### §4.3.5 Keşif · AirPlay

| Boyut | İçerik |
|---|---|
| Kural | §5 K14 kalemi; Apple ekosistem yerel-teslim hedefi |
| Durum | PLANNED — repo 0 isabet; ADR-037 adlandırmıyor (ADR-037 başlığında BLE/Classic + mDNS/UPnP-DLNA var) |
| Edge case | lisans/protokol kısıtları ⚠️ (research kapısı — R14.4: web kaynağı olmadan iddia yazılmaz) |
| İlişki | DLNA/UPnP ile aynı "yerel teslim" ailesi; K015 teslim politikasıyla hizalı |
| Kanıt | `.ai/CLAUDE.md §5 K14 satırı` · ⚠️ VERIFICATION REQUIRED (ADR-037 kapsamı dışında) |

#### §4.3.6 Adlandırma · DNS + TLS

| Boyut | İçerik |
|---|---|
| Kural | DNS çözümleme ve TLS sonlandırma platform düzeyinde; alt-domain mimarisi 11 alan (§9: coremusic.net + 10 panel) |
| Durum | PLANNED (repo/ yapılandırma kanıtı yok) · TLS+HTTPS varsayımı mimicari belge düzeyinde |
| Edge case | sertifika süresi/rotasyonu, TLS sürüm alt sınırı, alt-domain wildcard sertifikası |
| Kapsam dışı | uygulama-seviyesi şifreleme (AES-256-GCM/Argon2id → K006, anayasa §12) |
| Kanıt | `.ai/CLAUDE.md §5 K14` + §9 panel tablosu + §12 (encryption satırı) · ⚠️ |

#### §4.3.7 Çevrimdışı · Offline Sync

| Boyut | İçerik |
|---|---|
| Kural | Ağ kesintisinde Offline-First davranış: SQLite kuyruk + bağlantı dönünce yakalama (anayasa §22) |
| Durum | DESIGN — veri modeli diskte (`coremusic_wireless.sql` → `sync_history`, `wifi_networks`, `network_profiles`; ls 2026-10-08); kuyruk uygulaması repo'da gözlenmedi |
| Edge case | çakışma çözümü (son-yazan mı, sıralı mı) ⚠️ · kuyruk taşması · kısmi senkron |
| İlişki | ADR-081 (çoklu-provider senkron — MySQL SSOT + outbox) senkron desenini tanımlar (accepted/ ls) |
| Kanıt | `.ai/.sql/mysql/coremusic_wireless.sql` (ls) · anayasa §22 · ADR-081 (accepted/ ls) |

#### §4.3.8 Teslim · Handoff

| Boyut | İçerik |
|---|---|
| Kural | Çalma durumunun cihazlar arası devri ("Aynı Müzik Her Yerde Seninle" — anayasa §1) |
| Durum | PLANNED (grep "handoff" 0 isabet; yalnız anayasa/VISION metni) |
| Edge case | devir sırasında çalma konumu/queue senkronu · yetki devri (cihaz oturumu K006) · ağ kesintisinde devir iptali |
| Kapsam dışı | queue içeriği sahipliği (K015), cihaz kimliği (K001/K006) |
| Kanıt | `.ai/CLAUDE.md §1` (felsefe satırı) · `.ai/CLAUDE.md §4.2` (çoklu-cihaz senkronizasyonu) · ⚠️ |

#### §4.3.9 Çoklu-Oda · Multi-Room

| Boyut | İçerik |
|---|---|
| Kural | Birden fazla odada senkron çalma; Network Audio servisi (WebRTC/P2P — §10 #5) |
| Durum | PLANNED (grep 0 isabet) |
| Edge case | saat/jitter senkronizasyonu ⚠️ · oda-üyesi yetkilendirme (K006) · tek oda düşerse grubun davranışı (fail-over) |
| İlişki | NAS/multi-room yeteneği anayasa §4.2'de yetenek olarak listelenir |
| Kanıt | `.ai/CLAUDE.md §4.2` + §10 #5 · ⚠️ |

#### §4.3.10 Port/Servis Yüzeyi (anayasa §11 kaydı — K014'in taşıma sorumluluğu)

| Port | Servis | Protokol | K014 ilişkisi |
|---|---|---|---|
| 80 | admin.coremusic.net | HTTP | taşıma kaydı (§11) |
| 81 | music.coremusic.net (Control) | HTTP | taşıma kaydı (§11) |
| 3001 | download.coremusic.net | HTTP/WS | WS ayağı PLANNED (§4.3.2 ile aynı gap) |
| 3306 | MySQL 18 BCNF DB | TCP | veri katmanı bağlantısı (K005 sahibi; K014 yalnız taşıma) |
| 5000/6000 | media.coremusic.net | HTTP | K015 köprüsü — medya akışı K14 üzerinden |
| 9741/9742 | Audio Service REST/WS | HTTP/WS | gerçek-zamanlı kanal (WS gap §4.3.2) |

### §4.4 Kapsam Dışı / Sınır Tanımı (K014'in YAPMADIĞI)

| Aday konu | Neden K014 değil | Asıl sahip | Kanıt |
|---|---|---|---|
| Medya kodlama (FLAC/MP3/HLS segmentleri) | içerik üretimi, taşıma değil | K015 MEDIA | `.ai/CLAUDE.md §5 K15` |
| Fen protokolü/çözücü (DSP, codec) | sinyal işleme | K003 AUDIO ENGINE | anayasa §A.1 K003 |
| Kimlik/oturum kararı (JWT, session) | güvenlik kararı | K006 SECURITY | `.ai/CLAUDE.md §5 K6` |
| CORS/CSRF/rate-limit kademeleri | istek hattı (ayrı katman) | K007 MIDDLEWARE | `.ai/CLAUDE.md §6` |
| Radyo/ethernet donanımı (BLE/anten) | donanım-yazılım kesişimi | K001/K002 (driver/API sınırı) | ADR-096 §2.2 |
| HTTPS sertifika dağıtımı/CI sırrı | dağıtım otomasyonu | K013 CI/CD | `.ai/CLAUDE.md §5 K13` |
| Port kaydı/mimari kural | anayasa düzeyi sabit | `.ai/CLAUDE.md §11` (kural kaynağı) | Guardrail #8 (81 = music) |

### §4.5 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K14 hedefi | 40 bileşen (envanter sayımı — HEDEF) |
| Repo kanıtı (grep/ls 2026-10-08) | gateway/routing dosyaları + 1 routing testi; protokol implementasyonu (WebRTC/DLNA/UPnP/AirPlay/mDNS/WebSocket/HTTP-3) 0 isabet |
| Şema kanıtı | `coremusic_wireless.sql` 5 tablo (ls 2026-10-08) — offline-sync veri modeli hazır |
| Durum dağılımı | IMPLEMENTED: 2 (TCP/IP üzerinden HTTP taşımada gateway; routing testi) · DESIGN: 1 (offline-sync şeması) · PLANNED: 8 (kalem) |
| Web research | 0 URL bu dosyada → ⚠️ (R9: 1/3 → yalnız vault+repo ayağı; F1 EK B kapısı) |

### §4.6 K014 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti (dosya başlığı) | K014 etkisi |
|---|---|---|
| ADR-031-mobile-strategy-pwa-flutter | Mobil strateji — PWA birincil, Flutter opsionel, API-öncelikli | offline/PWA hedefi → Offline Sync & Handoff girdisi |
| ADR-037-wirelessconnect-integration | Cihaz eşleştirme + bağlantı yönetimi + keşif + güvenlik (BLE/Classic · mDNS/UPnP-DLNA · çoklu-protokol) | keşif kalemlerinin (mDNS/UPnP/DLNA) karar kaynağı |
| ADR-039-7-service-platform-architecture | 7-Service Platform Architecture (11 servis + PLANNED katmanlar) | Network Audio servisinin platform içindeki yeri |
| ADR-084-api-gateway-architecture | API Gateway mimarisi | HTTP taşıma ayağının giriş noktası sözleşmesi |
| ADR-086-event-driven-architecture | Event-driven mimari (PSR-14 Event Bus) | K014 → yukarı olay yayını yollarının taşıyıcısı |
| ADR-096-kspace-5000-boundary-model | K-Space V2 rejimi · dizin deseni · çift uçak (§2.2) | bu dosyanın format/bağımlılık yönü kaynağı |

### §4.7 K014 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K014'in verdiği | K014'in beklendiği | Kanıt |
|---|---|---|---|---|
| K013 CI/CD | yapılandırma/dağıtım | ağ yapılandırması girdisi (port/host) | ağ yapılandırmasının dağıtımı (deploy env) | `.ai/CLAUDE.md §11` · §25 |
| K015 MEDIA | medya akışı/teslim kanalı | taşıma kanalı (HTTP/WS/P2P — "Yalnızca K14 üzerinden iletişim") | yalnız K14 üzerinden istek (doğrudan ağ erişimi yok) | `.ai/CLAUDE.md §5 K15 kısıtı` |
| K009 API | gateway sözleşmesi | taşıma oturumu + port kaydı | routing/auth/validation kararları (K006/K007) | `.ai/CLAUDE.md §6A.1` · ADR-084 |
| K008 SERVICES | olay yayını | bağlantı/senkron olayları (Event Bus) | servis-ler-arası doğrudan çağrı yerine event (§5 K8 kısıtı) | ADR-086 · `.ai/CLAUDE.md §5 K8` |
| K012 OBSERVABILITY | log/metrik akışı | erişim/latency/senkron logları | merkezi izleme (PLANNED) | `.ai/CLAUDE.md §5 K12` |
| K001/K002 | sürücü/radyo sınırı | ağ istekleri (OS üzerinden) | donanım radyo/ethernet erişimi (driver/API) | anayasa §A.1 K001/K002 · ADR-096 §2.2 |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000 OS | aşağı | socket/DNS/TLS yığını ve ağ yığını OS'ta (EK A K000 "Networking") | `00-kspace-anayasa.md §A.1 K007→K014 izinli aralığı` + K000 kartı |
| K001-K002 | aşağı | radyo/ethernet erişimi sürücü/API üzerinden (donanım-yazılım köprüsü) | anayasa §A.1 K001/K002 · ADR-096 §2.2 (yalnız driver/API sınırı) |
| K003-K013 | aşağı | EK A "izinli=K000-K013 (alt katmanlar)" | `00-kspace-anayasa.md §A.1 K014 Sınır satırı` |
| K009 gateway | yan (sınır) | HTTP taşıma ayağının somut uygulaması port/adapter + API sınırıdır | `shared/src/Api/Gateway.php` (ls) · ADR-084 |
| K008 Event Bus | yan (yayın) | olay yayını yukarı serbest (R6.2) — senkron geri çağrı değil | `rules.md R6.2` · ADR-086 |
| port/adapter | yan | EK A istisnası (sıçrama kuralı — R6.3) | `00-kspace-anayasa.md §A.1 K014` |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| K014 → K015-K020 doğrudan erişim / geri çağrı (H20) | klasik yön: izinli = alt + port/adapter; senkron çağrı yukarı yasak | `rules.md R6.1` · `ADR-096 §2 (Dependency yönü)` · `.ai/CLAUDE.md §5.1` |
| medya kodlama/akış içeriğine dokunmak (K015 işi) | karışık sorumluluk — K014 yalnız taşıma/keşif | `.ai/CLAUDE.md §5 K15` ("Yalnızca K14 üzerinden iletişim" — yön tek yönlü) |
| K006/K007 doğrulamasını atlamak (auth/CSRF/CORS/CSP) | güvenlik katmanı atlanamaz; K014 politika üretmez | `.ai/CLAUDE.md §5 K6` + §6 · Guardrail #7 |
| Katmanlar arası doğrudan veri paylaşımı (H19) | veri sınırı ihlali (R4.4d) | `rules.md R6.1` · `ADR-096 §2` |
| Anayasa §6 pipeline kademelerinin yeniden uygulanması | K007'nin münhasır alanı (sıra immutable) | `.ai/CLAUDE.md §6` + Guardrail #7 |
| `SELECT *` / ORM / framework kullanımı | ADR-001/002 mutlak yasakları | `rules.md R17` · ADR-001/002 (accepted/ ls) |

### §5.3 Boundary Matrisi

| Boundary | K014 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | bağlantı envanteri + `sync_history` (coremusic_wireless 5 tablo); iş verisi yok | K005 (DB) · K015 (medya verisi) |
| SECURITY_BOUNDARY | TLS/keşif politikası K006 ile hizalı; istek doğrulaması K006/K007'de; eşleştirme ADR-037 | K006 SECURITY · K007 MIDDLEWARE |
| FAILURE_MODE | fail-over · kesinti → Offline-First + SQLite kuyruk · keşif fallback (çoklu-protokol) | K000 (OS/timer) · K012 (olay) · K015 (kuyruk sahipliği — download_queue) |
| RUNTIME boundary | OS ağ yığını + gateway istek yaşam döngüsü; WS/QUIC süreçleri PLANNED | K000 (runtime) · K009 (gateway yaşam döngüsü) |
| PROTOCOL boundary | protokol sürümü/alt sınırı (HTTP/3, TLS) platform kararıdır; katman-içi kalır | K013 (dağıtım yapılandırması) |
| Olay (event) yukarı serbest | bağlantı/senkron/keşif olayları K012/K008'e yukarı yayınlanır; senkron geri çağrı yasak | K008 (Event Bus) · K012 (izleme) |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.5 Kademe / Zıplama Notu (R6.3 — 9 kademeli hiyerarşi)

| Kademe | K014 karşılığı | Not |
|---|---|---|
| K-Layer → Domain → Subdomain | Bant 1 / K014 / transport-tcp-ip · discovery · offline-sync | SUBDOMAIN alanı §3/5 |
| Module → Component | gateway modülü (`shared/src/Api/Gateway.php`), routing bileşeni | repo kanıtı (ls) |
| Service → Adapter | Network Audio servisi (§10 #5 — PLANNED) · keşif adaptörleri (mDNS/UPnP — PLANNED) | hedef |
| Adapter/Implementation sıçraması | yalnız port/adapter ile (R6.3) — K014 doğrudan K015'e sıçramaz | `.ai/CLAUDE.md §5 K15 kısıtı` |
| Döngü toleransı | sıfır (R6.6) — K014 bağımlılık grafiğinde geri kenar yok | `rules.md R6.6` |

### §5.6 K014 Risk Güvenlik Notları (anayasa §23 hizası)

| Risk | Etki | Azaltma | Kanıt |
|---|---|---|---|
| Açık port/yanlış port kullanımı | servis yanlış panelde (Guardrail #8 ihlali) | §11 port kaydı sabit; 81 = music.coremusic.net | `.ai/CLAUDE.md §23 #5` + §11 |
| Keşif protokollerinin kötüye kullanımı (LAN) | yetkisiz cihaz keşfi | eşleştirme güvenliği ADR-037 kapsamı; K006 kararı | ADR-037 (accepted/ ls) |
| Kesintide veri kaybı (senkron) | offline kuyruk/çakışma | Offline-First + `sync_history` | `.ai/CLAUDE.md §22` |
| Manuel TLS/protokol iddialarının kanıtsızlığı | yanlış güvenlik varsayımı | `⚠️` + research kapısı (R9/R14) | `rules.md R9.2` |


### §5.4 Olay (Event) Akışı — yukarı serbest, aşağı senkron yasak (R6.2)

```text
K012 OBSERVABILITY  ← (olay/log yayını, yukarı SERBEST)  ←  K014 bağlantı/senkron/keşif olayları
      ↑                                                          │
      │ (okuma)                                        [senkron çağıramaz — H20]
      └──────────── K008 SERVICES (Event Bus) ────────────┘
                         │
   K014 yalnız alttan beslenir: K000 ağ yığını + K001/K002 sürücü  (aşağı ↓ izinli)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| K014 → K012 log/olay | yukarı | olay yayını yukarı serbest (R6.2) | `rules.md R6.2` |
| K014 → K008 senkron çağrı | — | YASAK (H20 geri çağrı) | `rules.md R6.1` · `ADR-096 §2` |
| K015 → K014 akış talebi | aşağı (K15→K14) | medya ağı yalnız K14 üzerinden (anayasa kısıtı) | `.ai/CLAUDE.md §5 K15` |
| K014 → K000 socket | aşağı | ağ yığını OS'ta | anayasa §A.1 K000 |
| K014 → K006 güvenlik kararı | aşağı (karar) | K014 uygular, üretmez | `.ai/CLAUDE.md §5 K6` · ADR-037 güvenlik |

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı / Devir | Sınır notu |
|---|---|---|---|
| Taşıma (TCP/IP) | OS paket/oturum | uygulamaya teslim edilmiş byte akışı | K000 sahipliği |
| HTTP/S + gateway | istek (host/port/body) | routing sonucu (K009'a devir) | auth/validation K006/K007 |
| WebSocket (hedef) | istemci bağlanışı | çift-yönlü kanal (9742) | PLANNED |
| WebRTC (hedef) | peer keşfi + oturum | P2P medya yolu | PLANNED — codec K015/K003 |
| Keşif (mDNS/UPnP/DLNA) | ağ varlığı soruları | peer kaydı (`bluetooth_peers`/`network_profiles` benzeri) | ADR-037; eşleştirme güvenliği |
| Offline Sync | yerel kuyruk + bağlantı olayı | `sync_history` + kuyruk boşaltma | veri sınırı: wireless şeması |
| Handoff (hedef) | cihaz-devir tetiği | çalma devri olayı | PLANNED; yetki K006 |
| Multi-Room (hedef) | oda-üyesi senkron tetiği | senkron çalma komutu | PLANNED |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Ağ yığını | OS (K000) — TCP/IP, socket, DNS | anayasa §A.1 K000 |
| Gateway | `api.coremusic.net` tek giriş (PHP 8.4) | `.ai/CLAUDE.md §6A.1` · `shared/src/Api/Gateway.php` |
| Portlar | 80 · 81 · 3001 · 3306 · 5000/6000 · 9741 · 9742 | `.ai/CLAUDE.md §11 Port Register` |
| Zaman aşımları | rate-limit 60s / session 3600s (K007) — ağ-özel timeout ⚠️ | `.ai/CLAUDE.md §6 #3/#5` · K014 timeout tanımı yok → ⚠️ |
| Hedef protokoller | HTTP/3 · WebRTC · WS · mDNS/UPnP/DLNA/AirPlay | `.ai/CLAUDE.md §5 K14` (PLANNED — grep 0) |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Gateway erişim/latency logu | `shared/src/Api/` + log altyapısı (`shared/src/Log`) | IMPLEMENTED (log altyapısı) · K014'e özel alanlar ⚠️ |
| Senkron geçmişi | `coremusic_wireless.sql → sync_history` | IMPLEMENTED (şema) · üretim kodu PLANNED |
| Keşif sayaçları (timeout/fallback) | ADR-037 davranışı | PLANNED |
| Metrik/trace | Prometheus/Grafana (anayasa §5 K12) | PLANNED — K012 kapsamı |
| Kesinti olayı (offline mode) | frontend "offline" sinyalleri (grep 4 dosya, assets router) | gözlemlendi — içerik okunmadı → ⚠️ |

### §6.4 Test

| Katman | Test | Durum | Hedef |
|---|---|---|---|
| HTTP method/routing sözleşmesi | `shared/tests/Api/GatewayMethodRoutingTest.php` (ls 2026-10-08) | IMPLEMENTED (repo) | ≥80% (anayasa §17) |
| Fail-over / yeniden-bağlanma | K014 hedef testi (kesit → recovery → kuyruk boşaltma) | TANIMLI, YAZILMADI (hedef test — "yapıldı" DEĞİL) | anayasa §17 ≥80% |
| Offline kuyruk tutarlılığı | `sync_history` + kuyruk replay senaryosu | TANIMLI, YAZILMADI | çakışma/parçalı senkron kapsamı |
| Keşif fallback | protokol sırası (mDNS → UPnP-DLNA) timeout davranışı | TANIMLI, YAZILMADI | ADR-037 davranışı |
| P2P/WS/kablosuz E2E | Playwright/P2P entegrasyon testi | TANIMLI, YAZILMADI | KAPI 9 · §11 kapıları |

### §6.5 Failure Mode Senaryoları (failure=fail-over)

| # | Senaryo | K014 davranışı | Kullanıcı etkisi | Kanıt |
|---|---|---|---|---|
| 1 | Ağ kesintisi (offline) | Offline-First + SQLite kuyruk → bağlantı dönünce yakalama | kesintisiz yerel dinleme, ertelenmiş senkron | `.ai/CLAUDE.md §22` (Network outage satırı) |
| 2 | Keşif protokolü timeout | sıradaki protokole fallback (çoklu-protokol otomatik seçim) | cihaz bulma gecikmesi/alternatif yol | ADR-037 (accepted/ ls) |
| 3 | WS/HTTP kanalı düşer | yeniden bağlanma (kayıp/TELAFİ politikası ⚠️) | canlı özellik geçici kesinti | ⚠️ VERIFICATION REQUIRED (WS uygulaması yok) |
| 4 | TLS sertifika süresi dolar | bağlantı reddi (fail-closed) | erişilememe; sertifika rotasyonu K013 görevi | ⚠️ (yapılandırma kanıtı yok) |
| 5 | Port çakışması (80/81/3001/…) | servis başlatılamaz/yanlış panel | panel erişim hatası | `.ai/CLAUDE.md §11` + Guardrail #8 (81 = music) |
| 6 | Senkron çakışması (çift yazma) | çakışma çözümü kuralı ⚠️ tanımsız | veri belirsizliği | ⚠️ (ADR-081 outbox deseni var; K014 uygulaması yok) |
| 7 | Çok-oğa join (multi-room) düğüm kaybı | grubun davranışı (fail-over) ⚠️ tanımsız | oda-üyesinde çalma durması | ⚠️ (PLANNED katman) |
| 8 | Handoff sırasında kesinti | devir iptali/eskalasyon ⚠️ tanımsız | devir başarısız | ⚠️ (PLANNED katman) |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K014 durumu |
|---|---|
| KAPI 1 vault oku | TAM — anayasa §A.1 K014 + §5/§11/§22 + rules.md + ADR-096 okundu (bu üretim) |
| KAPI 9 hallucination damgası | `⚠️` işaretli ayağlar §7.1'de listelendi (WS/WebRTC/keşif/TLS/handoff/multi-room uygulaması, çakışma kuralı, web kaynağı) |
| KAPI 10 kullanıcı onayı | BEKLİYOR — `status: draft`, bant onayı 👤 (R10) |
| Guardrail #3 (Zero-Hallucination) | Uygun — repo 0 isabet sonuçları PLANNED olarak yazıldı, IMPLEMENTED sayılmadı |
| H10 (hedef ≠ kanıt) | Uygun — "40 bileşen" hedef ile repo kanıtı ayrı satırlarda |
| R9 3'lü kanıt | Kısmi (2/3: repo + vault/ADR; web ayağı ⚠️) → R14 research kapısı |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | dolu (§2) · kesişim yok |
| K2 | EK C 20 alan kart | tam dolu · KANIT 3'lü | dolu (§3) |
| K3 | K14 kısıtının kavranması | "Ağ iletişim standardı (K15, K10-13)" bağı yazılı | dolu (§1/§4.2/§5) |
| K4 | Veri sınırı | wireless şeması tek kaynak | dolu (5 tablo ls) |
| K5 | Protokol kapsamı | 10 kalemin durumu | 2 IMPLEMENTED · 1 DESIGN · 7 PLANNED |
| K6 | Web research (R9 3'lü) | iddiaların ≥%80'i kaynaklı | değil → ⚠️ (G7, F1 EK B kapısı) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | bekliyor (Kapı 10) |

### §6.8 Edge Cases (anayasa §22 hizası)

| Durum | Çözüm | Sahip | Kanıt |
|---|---|---|---|
| Network outage | Offline-First + SQLite queue | K014 (senkron) + K005 (queue altyapısı) | `.ai/CLAUDE.md §22` |
| USB/cihaz çıkarma (ağ adaptörü) | fallback (WASAPI/yerel) — K002/K003 davranışı | K002 DRIVERS | `.ai/CLAUDE.md §22` (ADR-017 satırı) |
| Çoklu-sekme/oturum çakışması | session-bound tek token | K006/K007 | `.ai/CLAUDE.md §22` (ADR-010 satırı) |
| Port 81 ihlali | Guardrail #8 — yanlış port yasak | `.ai/CLAUDE.md §11` | `.ai/CLAUDE.md §7 #8` |
| Keşif/protokol timeout | çoklu-protokol fallback | K014 | ADR-037 (accepted/ ls) |
| Çakışma/çift senkron yazma | outbox deseni (ADR-081) — K014 uygulaması yok | K005/K014 sınırı | ADR-081 (accepted/ ls) · ⚠️ |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9.3-lü) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K014 - NETWORK «ÇARK»` (+ §A.0 anahtar satırı) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt satırları — ana kaynak |
| 2 | `.ai/CLAUDE.md §5` (K14 + K15 + K12 satırları) · `§11` (port) · `§10 #5` · `§14` · `§22` (Network outage) · `§6A.1` | dosya yolu (vault read 2026-10-08) | K-matrix + kısım + edge case |
| 3 | `shared/src/Api/Gateway.php` · `shared/tests/Api/GatewayMethodRoutingTest.php` · grep (webrtc/dlna/upnp/airplay/mdns/websocket → 0) | repo grep/ls (2026-10-08) | durum etiketleri (IMPLEMENTED/PLANNED) |
| 4 | `.ai/.sql/mysql/coremusic_wireless.sql` (wifi_networks · network_profiles · bluetooth_peers · bluetooth_audio_profiles · sync_history) | dosya yolu (ls 2026-10-08) | DATA_BOUNDARY |
| 5 | `.ai/.decisions/accepted/` ls (2026-10-08): ADR-031 · ADR-037 · ADR-039 · ADR-084 · ADR-086 · ADR-096 | ADR (ls teyitli) | karar atıfları |
| 6 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2.4/§2.5 + §2.2 (çift uçak)` | dosya yolu (vault read) | format + bağımlılık yönü |
| 7 | F1 (`2026-10-08-master-prompt-v2.2.0-f1.md`) EK B research defteri (37 URL, 2026-10-08) | URL (vault arşivi) | web research üssü |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri — R4.4c / R9.2)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | WebSocket uygulaması/grep 0 | §4.3.2 · KANIT | repo grep (9742 WS sunucusu) + varsa test |
| G2 | WebRTC/DLNA/UPnP/AirPlay/mDNS implementasyonu yok | §4.2 · EK C 8/16 | hedef doğrulama → research (R14) veya PHASE planı |
| G3 | TLS/HTTP-3 yapılandırması kanıtı yok | §4.3.6 · EK C 14 | deploy/sunucu yapılandırması denetimi (K013) |
| G4 | Handoff/Multi-Room uygulaması yok | §4.3.8/§4.3.9 | ürün/teknik plan + test tanımı |
| G5 | Offline kuyruk (SQLite) uygulaması yok — yalnız şema | §4.3.7 · EK C 15 | kod kanıtı / taslak ADR |
| G6 | Senkron çakışma çözümü kuralı tanımsız | §6.5 #6 | ADR-081 outbox ile hizalama kararı 👤 |
| G7 | Web kanıtı (URL+tarih) — protokol/domain iddiaları | KANIT web ayağı | F1 EK B research kapısı (R14) → yeni iddialar EK B'ye eklenir |
| G8 | Frontend "offline" sinyalleri içeriği okunmadı (grep 4 dosya) | §6.3 | ilgili JS dosyalarının okunması |

**Kural hatası:** Bu boşluklar dosyayı geçersiz kılmaz (R4.4c: `⚠️` R14'te doldurulur); ancak
KAPI 9/10'dan önce kapatılmadan katman `ACTIVE` olamaz (R16.2).

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE» · K009 API «HÜCRE» · K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA».
Üst/sağ (H20 yasak yönü): K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» ·
K018 THERMAL «MEZİT» · K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL».
İlişki türü: kardeş katmanlar arası yalnız `refers-to` (doküman linki), `depends-on` DEĞİL (R6.4).

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):**

| Dosya | Katman | Durum |
|---|---|---|
| b1-K014-ag.md | K014 NETWORK «ÇARK» | bu dosya (draft) |
| b1-K015-medya.md | K015 MEDIA «MÜHÜR» | band-1 setinin parçası |
| b1-K016-amplifikator.md | K016 AMPLIFIER «ZAR» | band-1 setinin parçası |
| b1-K017-guc-kaynagi.md | K017 POWER «KANTAR» | band-1 setinin parçası |
| b1-K018-termal.md | K018 THERMAL «MEZİT» | band-1 setinin parçası |
| b1-K019-pcb.md | K019 PCB «ALEV» | band-1 setinin parçası |
| b1-K020-uretim.md | K020 MANUFACTURING «BUZUL» | band-1 setinin parçası |

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.
