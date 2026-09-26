---
title: "CoreMusic — ADR-029: Listening Rooms Social (Tam Senkron Dinleme Odası + Sohbet · WebSocket + Redis Pub/Sub · Sunucu Sahipli Saat / Epoch Senkron · SSE Fallback · Oda Sahibi Yetkileri · Davet/Üyelik Modeli · Kapasite · Opsiyonel Kayıt/Arşiv)"
type: adr
category: social
date: 2026-09-25
updated: 2026-09-25
version: 1.1.0
status: accepted
authority: ADR-029 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)"
---

# CoreMusic — ADR-029: Listening Rooms Social (Tam Senkron Oda + Sohbet · WebSocket + Redis Pub/Sub · Sunucu Sahipli Saat · SSE Fallback)

**Durum:** accepted (kullanılabilir — **frozen YOK**)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-029'u sıfırdan yaz. Sosyal/gerçek zamanlı alan."; kapsam **kullanıcı onaylı**, teknoloji seçimi **mimari karar (senior architect)**: **(a)** oda kurma/davet (link) · **(b)** senkron oynatma (herkes aynı anda — **sunucu saati = referans**, drift düzeltme) · **(c)** sohbet (mesaj akışı, rate limit ADR-013) · **(d)** oda sahibi yetkileri (play/pause/kick/mute) · **(e)** max kapasite (örn. 50) · **(f)** üyelik/davet modeli · **(g)** kayıt/arşiv (oda geçmişi — opsiyonel) · **teknoloji: WebSocket + Redis pub/sub** (çok-fpm/process yayınlama; Redis bağımlılığı ADR-007 PLANNED ile hizalı ve bu ADR'de **şart**), **SSE fallback** (WS engelli ağlar için tek yönlü senkron), **sunucu sahipli saat + epoch-based senkron** (istemci saati güvenilmez), **WebRTC P2P reddedildi** (moderasyon/kayıp riski §3-3) · debate **✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL)**, Tech Lead **✅**)
**İlgili ADR'ler:** [[ADR-007-cache-namespace]] (cache katmanı — `shared/src/Cache/` = **Apcu/Memory/PageCache, Redis adapter YOK** (`ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php`), `engine.md:302` "Redis cache iddiası kodda yok" → bu ADR **Redis pub/sub bağımlılığını şart koşar** (PLANNED adapter'ın üstünde koşar; dosya diskte VAR ✅) · [[ADR-011-session-management]] (oturum — WS handshake'te çerez/doğrulama dayanağı; `SessionLifecycle.php:54-62` **30 dk'da bir `session_regenerate_id(true)`** → uzun ömürlü WS bağlantısında yenileme/tutanak kuralı §2.2'de; `:160` "Redis oturum deposu" reddi "ileride supersede" notuyla bu ADR'nin Redis'e geçişiyle kesişir; dosya diskte VAR ✅) · [[ADR-013-rate-limiting-apcu]] (sohbet mesaj kotası — `RateLimiterMiddleware.php:30-32,48-56` fail-open + 429/`Retry-After` **IMPLEMENTED**, ADR-013'e dokunulmaz; dosya diskte VAR ✅) · [[ADR-018-footer-player-vaporwave]] (oynatıcı — durum makinesi `assets.coremusic.net/js/features/PlayerController.js:12-13` `STOPPED/PLAYING/PAUSED` **IMPLEMENTED** → senkron oynatmanın bağlandığı hook; dosya diskte VAR ✅) · [[ADR-005-ultrathink-protocol]] (`⚠️ VERIFICATION REQUIRED` kanıt standardı) · [[ADR-024-ecosystem-modular-docs]] (wiki-link disk kanıtı kuralı) · karar dizini [[../index]] **satır 66** `[[ADR-029-listening-rooms-social]]` (slug eşleşmesi ✅).

> **Numara notu:** "Yeni ADR ≥ 088" kuralı bu yazımda uygulanmaz — `ADR-029-listening-rooms-social` karar dizini `../index.md:66`'da **rezerve boş slottur** (ADR-026/027/028 aynı istisnayı `:63`/`:64`/`:65`'te kaydetmişti). Ek kanıt: `keys.md:264` "ADR-029 | listening rooms, social | Social", `.ai/index.md:646` "decisions/accepted/ADR-029-listening-rooms-social | Listening rooms social | Social", `brain.md:984` "ADR-029 | Sosyal dinleme odaları", `.templates/adr/adr-index.md:100` "29 | ADR-029 | Sosyal dinleme odaları | 🔵 feature" — dört katalog kaydı bu numarayı bağlar.

---

## 1. Bağlam (Context)

CoreMusic'in sosyal katmanında **gerçek zamanlı paylaşımlı dinleme** (dinleme odası) ve **sohbet** özelliği, vault'ta şema ve spec olarak var ama **tek bir kod parçası bile yok**: `coremusic_social.sql` oda/üye/kuyruk tablolarını tanımlar (`listening_rooms`, `listening_room_members`, `listening_room_queue`), `k14-ag/websocket-realtime.md` WebSocket protokolünü ve `room.sync`/`room.join`/`room.leave` mesaj tiplerini tarif eder — ama PHP tarafında WebSocket, SSE, pub/sub, oda veya sohbet kodu **0 eşleşmedir**. Aynı anda gerçek zamanlı yayınlama altyapısı da yoktur: cache dizini APCu/Memory/PageCache ile sınırlıdır, Redis adapter **yoktur** (ADR-007 bulgusu). Bu ADR; **oda + senkron oynatma + sohbet** üçlüsünün kapsamını, **WebSocket + Redis pub/sub** taşıma kararını, **sunucu sahipli saat** kuralını, SSE/polling fallback zincirini ve riskleri tek belgede bağlayıcılaştırır. Karar **mimari çerçevedir, kod taahhüdü değildir** (kod kanıtı §1.1).

### 1.1 Mevcut Durum

**A) KOD KATMANI — gerçek zamanlı/oda/sohbet kodu YOK (tümü PLANNED):**

| Tarama (grep) | Sonuç | Dosya:Ssatır |
|---|---|---|
| `WebSocket\|EventSource\|\bSSE\b\|pub/sub\|pubsub` (tüm `*.php`) | **0 eşleşme** | repo geneli PHP |
| `listening_room\|room_code\|room.sync\|room.join\|room_invite` (`*.php`,`*.js`) | **0 eşleşme** | repo geneli |
| `room\|chat\|message\|realtime\|socket` (`shared/src/`) | **yalnız alakasız eşleşmeler** — AI EQ **room correction** (`AIEngine.php:24,128,352-356`, `AIWorkflow.php:155` — oda **akustiği**, sosyal oda değil) + `SnapchatOAuth.php` ("chat" false-positive) | `shared/src/AI/*`, `shared/src/OAuth/*` |
| `listening_room\|Room\|chat\|WebSocket\|EventSource` (`home.coremusic.net/**/*.php`) | **0 oda/sohbet eşleşmesi** — çıkanlar yalnız header/footer/player asset satırları | `header.php`, `footer.php`, `pages/home.php` vb. |
| `WebSocket\|EventSource\|socket.io` (`*.js`) | **0** — çıkanlar `ASSETS_ORIGIN`/`res-` false-positive | `assets.coremusic.net/js/*` |
| WS sunucu kodu (`WebSocketServer\|socket.io\|require('ws')`, JS/TS) | **0** (WS sunucusu yok) | repo geneli |
| `Redis\|redis` (`*.php`) | **0 eşleşme** (client yok) | repo geneli PHP |
| Sohbet/mesaj tablosu `CREATE TABLE \w*(chat\|message\|conversation\|dm)` | **0** — `message` yalnız log/bildirim TEXT kolonları | `.ai/.sql/mysql/*` |

**B) ŞEMA KATMANI — oda tabloları IMPLEMENTED, sohbet tablosu YOK:**

| Nesne | İçerik | Durum | Dosya:Ssatır |
|---|---|---|---|
| `listening_rooms` | `room_code` (benzersiz), `room_type ENUM('public','private','invite_only')`, **`max_members DEFAULT 10`**, `current_music_id`, `current_position_sec`, `is_playing`, `member_count` | **IMPLEMENTED (şema)** | `.ai/.sql/mysql/coremusic_social.sql:129-153` |
| `listening_room_members` | `role ENUM('host','co_host','member','listener')`, `is_muted`, `is_online`, `last_active_at` | **IMPLEMENTED (şema)** | `coremusic_social.sql:161-182` |
| `listening_room_queue` | `music_id`, `added_by`, `position`, `is_playing`, `played_at` | **IMPLEMENTED (şema)** | `coremusic_social.sql:190-207` |
| `social_notifications` | `notification_type ENUM(…,'room_invite',…)` + `message TEXT` | **IMPLEMENTED (şema)** | `coremusic_social.sql:245-267` |
| Yorum/akış | `comments:23`, `comment_likes:58`, `shares:77`, `activity_feed:101` (`now_playing` aktivitesi `:104`) | **IMPLEMENTED (şema)** | `coremusic_social.sql` |
| **Sohbet mesaj tablosu** | **YOK** — oda içi anlık mesajlaşmaya ait satır yok | **PLANNED (yeni tablo gerekir)** | grep 0 |
| **Oda/üye/kuyruk yazan kod** | Controller/Service/Repository **0 dosya** | **PLANNED** | grep 0 |

**C) ALTYAPI KATMANI — yayınlama/gerçek zamanlı:**

| İddia | Vault kanıtı | Kod karşılığı | Etiket |
|---|---|---|---|
| WebSocket protokolü + `room.sync`/`room.join`/`room.leave`/`heartbeat` mesaj tipleri | `architecture/k14-ag/websocket-realtime.md:16` (RFC 6455/8441), `:106-115` (mesaj tipleri), `:117-140` (multi-room sync: master heartbeat 500 ms, slave ACK 200 ms, **drift < 5 ms**) | WS kodu **0** | **PLANNED** |
| Gerçek zamanlı oda durumu güncellemesi | `architecture/k10-uygulama/home-panel.md:182` "Her oda ve cihaz durumu WebSocket üzerinden real-time güncellenir"; `:157-159` same-source/independent senkronizasyon | kod **0** | **PLANNED** |
| WebSocket altyapısı (vizyon) | `VISION.md:96` "WebRTC/WebSocket multi-room audio", `:132`, `:209` "WebSocket desteği"; `ecosystem/service-integration.md:19` "WebSocket · Real-time"; `.opencode/CLAUDE.md:317` "9742 · Audio Service (WebSocket) · WS" (⚠️ `VERIFICATION REQUIRED`: eski `archives/prompt0-…2026-08-15.md:154` alıntısı diskte YOK — glob boş; içerik `.opencode/CLAUDE.md:317` ile karşılaştırıldı) | kod **0** | **PLANNED** |
| Redis (pub/sub dahil) | `engine.md:302` "Redis cache iddiası kodda yok", `:343-347` (S-03), `brain.md:80-81` (`predis/predis` hedefte) | `shared/src/Cache/` = `ApcuAdapter`/`MemoryAdapter`/`PageCacheAdapter` — **Redis adapter YOK** | **PLANNED (ADR-007 ile hizalı)** |
| "REST-Only" kararı | `decisions/index.md:127` `R-005-rest-only-api` **dead-link** (kaynak dosya yok — glob boş; bu ADR'de bağlantı **kullanılmaz**, düz metin) | — | ⚠️ `VERIFICATION REQUIRED` (bağlantı kırık, metin "WebSocket gerekli") |
| Oda senkron hook'u (oynatıcı) | ADR-018: `assets.coremusic.net/js/features/PlayerController.js:12-13` `#status = 'STOPPED'` durum makinesi (`:3` "Footer player state machine"), `home.coremusic.net/footer.php:66` footer player | **IMPLEMENTED** | **IMPLEMENTED** (bağlanılacak hook) |
| Rate limit (sohbet için) | ADR-013: `RateLimiterMiddleware.php:30-32` fail-open, `:48-56` 429/`Retry-After` | **IMPLEMENTED** | **IMPLEMENTED** (kullanılır) |
| Oturum (WS handshake doğrulaması) | ADR-011: `SessionLifecycle.php:31-66` timeout + 30 dk rotasyon | **IMPLEMENTED** | **IMPLEMENTED** (kullanılır) |

**Sonuç etiketi:** **IMPLEMENTED:** oda/üye/kuyruk/bildirim/yorum/aktivite **şeması**, footer player durum makinesi (ADR-018), fail-open rate limit + 429/`Retry-After` (ADR-013), oturum yaşam döngüsü + rotasyon (ADR-011), karar dizini slotu (`index.md:66`) + 4 katalog kaydı. **PLANNED:** WebSocket sunucusu ve istemcisi, Redis pub/sub (adapter dahil), SSE fallback, oda controller/servisleri, senkron protokolü (epoch + drift düzeltme), **tüm sohbet kodu ve sohbet şeması**. **`⚠️ VERIFICATION REQUIRED`:** (i) kapasite — kullanıcı "örn. 50" derken şema `max_members DEFAULT 10` (`coremusic_social.sql:136`) → değer/config hizası §5.1/5'te; (ii) sohbet mesaj şeması yok → yeni tablo (Data Engineer); (iii) WS'yi kimin taşıdığı (PHP uzun ömürlü süreç vs ayrı Node servisi) henüz kararlı değil (§5.1/2); (iv) spec drift/sync sayıları (500 ms / 200 ms / <5 ms) **ölçülmemiş**; (v) sunucu başına WS bağlantı kapasitesi ölçülmüş değil (§5.1/10).

### 1.2 Sorun Tanımı

1. **Gerçek zamanlı taşıma yok:** `WebSocket|EventSource|SSE|pub/sub` kodda **0** → oda içinde "aynı anda dinleme" ve anlık mesaj akışı **imkânsız**; bugün yalnız istek-bazlı HTTP var (ADR-013 rate limit'inin çalıştırdığı yol).
2. **Çok-process yayınlama problemi yazılmamış:** PHP FPM'de istekler farklı worker'lara dağılır; bir worker'daki "oynatma durdu" olayı diğer worker'ların bağlı istemcilerine **ulaşmaz** → ortak yayın kanalı (pub/sub) şart, bu kural hiçbir belgede yok.
3. **Saat/drift kuralı yok:** istemci saati (tarayıcı/OS) güvenilmez; oda içinde herkes "aynı anda" dinlemiyorsa özellik anlamsızlaşır. Kimin referans saat olduğu (sunucu mu istemci mi) yazılı değil.
4. **Sohbet şeması yok:** oda içi mesajlaşma için tablo yok (`CREATE TABLE chat|message|conversation` → 0); rate limit (ADR-013) hazır ama sohbet için nasıl uygulanacağı yazılmamış.
5. **Yetki modeli şemada var, kararda yok:** `role ENUM('host','co_host','member','listener')` ve `is_muted` şemada (**IMPLEMENTED**); play/pause/kick/mute yetkilerinin **kimde** olduğu, kapasitenin ne olduğu ve davet linkinin nasıl üretildiği kararlı değil.
6. **Kapasite çelişkisi:** özellik "örn. 50 kişi" ile anlatılırken şema `DEFAULT 10` (`:136`) → iki farklı sayı hiçbir yerde bağdaştırılmamış.
7. **Fallback yok:** WS engelli ağlar (kurumsal proxy/güvenlik duvarı — §1.3-3) için ne yapılacağı bilinmiyor → özellik bu ağlarda tamamen kapanır.
8. **Moderasyon/kayıt yok:** oda içi kötüye kullanım (spam mesaj, istenmeyen katılımcı) ve oda geçmişi (arşiv) için neyin saklanacağı/silineceği yazılmamış.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (diskte VAR ✅) — resmi/anahtar kaynak önce (RFC 6455, OWASP, NTP dokümanları, ürün resmi yardımı), **her ana iddia ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) gerçek zamanlı senkron 2025-26, (b) WebSocket ölçekleme (Redis pub/sub + sticky session), (c) listen-along/watch-together drift düzeltme, (d) sosyal oda UX, (e) SSE vs WS, (f) WebRTC P2P sınırları, (g) sohbet rate limit/moderasyon, (h) PHP'de WS sunucusu seçenekleri.** Erişim: **8 websearch sorgusu**; sonuçlar başlık/özet düzeyinde derlendi — derin sayfa-içi tur ayrı doğrulamaya bırakıldı (açıkça işaretli).

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "WebSocket scaling Redis pub/sub sticky sessions best practice 2025" · (2) "SSE vs WebSocket 2025 when to use Server-Sent Events fallback" · (3) "watch together listen along synchronized playback drift correction algorithm NTP-like server clock" · (4) "client server clock skew synchronization authoritative server timestamp drift compensation real-time systems" · (5) "Spotify Jam collaborative listening room UX design host controls 2025 how synchronized playback works" · (6) "PHP WebSocket server production options Ratchet Swoole vs separate Node service architecture 2025" · (7) "WebRTC mesh vs SFU multi-party rooms scaling limits moderation server-side control" · (8) "real-time chat rate limiting abuse prevention moderation design 2025 server side" |
| Web Search **Konusu** | (1) Çok-uçlu WS yayınlamasında sticky session + **Redis pub/sub fan-out** ve paylaşım durumu (shared state) deseni; (2) SSE'nin tek yönlü/otomatik yeniden bağlanma/güvenlik duvarı avantajı vs WS'nin çift yönlülüğü ve karmaşıklığı; (3) senkron oynatmada **saat drift'i** ve zaman damgalı parça/hub tabanlı düzeltme; (4) **sunucu otoriter zaman** — clock skew vs clock drift, istemci zamanına güvenmeme; (5) sosyal oda UX'i (Spotify Jam: host yetkileri, ortak kuyruk, katılım yolları, kapasite); (6) PHP'de WS sunucusu (Ratchet/ReactPHP, Swoole, FrankenPHP/Mercure) ve ayrı servis seçeneği; (7) WebRTC mesh'in O(N²) bant genişliği ve ~4 katılımcı sınırı → moderasyon/kayıt için sunucu kontrolü; (8) gerçek zamanlı sohbet için kota/istismar önleme ve sunucu tarafı moderasyon. |
| Web Search **Bağlam** | **~52 benzersiz adlandırılmış kaynak / 8 sorgu**: **Ably** (pub/sub + WS ölçekleme), **OneUptime** (WS ölçekleme 2026-01), **websocket.org guides** (WebSockets at Scale — sticky session ↔ shared state + Redis pub/sub örnek kod), **Stack Overflow #78660481** (Redis 7 sharded pub/sub), **Praeclarum** (NestJS + Redis), **Codelit** (sticky + fan-out), **VideoSDK** (sticky session kritik), **Reddit r/node** (8) · **Ably** (WS vs SSE karar matrisi), **RxDB** (4 teknoloji karşılaştırma), **Stack Overflow #5195452**, **Design Gurus** ("Stop Defaulting to WebSockets"), **Nimbleway 2026**, **OneUptime** (SSE vs WS), **GetStream** (SSE'de sticky yok + otomatik reconnect), **Svix** (8) · **Roon Community** (gruplu oynatma clock drift), **Lund Univ. tez PDF** (akışlı ses senkronizasyonu — "initial sync olur, saatler sonra yine drift"), **SoftwareEngineering SE** (zaman damgalı parça senkronu), **NTP FAQ (UDel)** (NTP hata tahmini), **Handmade Network** (NTP/saat sürüklenmesi), **Linode Community**, **diyAudio** (çoklu hoparlör senkronu), **ServerFault** (slew) (8) · **Baeldung** (clock offset vs skew), **Cambridge Dist-Sys notları**, **Arpit Bhayani** (clock sync nightmare), **Jerry Shah Medium** (client time trust ≠ güvenlik), **CommVault docs** (client clock skew ölçümü/remediation), **Security StackExchange** (SSL + saat), **ResearchGate**, **LinkedIn Dist-Sys** (8) · **Spotify Support (Jam)**, **Spotify Newsroom 2023**, **Spotify Community**, **Medium PM vaka analizi** (Jam → 32 kullanıcı), **UX Planet** (Jamroom tasarımı), **Reddit r/truespotify**, **YouTube** (7) · **websocket.org PHP guide** (Ratchet/Swoole), **Reddit r/PHP**, **Accesto** (long-running PHP), **DeployHQ 2025** (FrankenPHP/RoadRunner/Swoole), **StackShare** (Ratchet vs Swoole), **Reintech**, **TSH.io**, **Ably PHP** (8) · **Ant Media** (mesh/SFU/MCU), **Fora Soft 2026** (P2P ~4 katılımcıda biter, SFU 10K+), **Betadrix** (mesh pratik sınır 3-4), **Trembit**, **GetStream (SFU)**, **bloggeek**, **DEV (WebRTC at Scale)**, **Medium (Tosh Velaga)** (8) · **Zuplo 2026** (rate limit best practices), **Gravitee** (ölçekte rate limit), **Solo.io**, **Speakeasy** (429 + `RateLimit` başlıkları), **Databox**, **Medium**, **saascustomdomains**, **Reddit r/devops** (8) |
| Web Search **Kısa Açıklama** | **(1) Ölçekleme:** WS uzun ömürlü bağlantı olduğundan yük dengeleyicide **sticky session** gerekir; kalıcı çözüm **durumu dışarıda tutup (shared state) çoklu sunucuyu Redis pub/sub ile beslemektir** — bir sunucudaki olay diğerinin bağlı istemcilerine ancak böyle ulaşır (websocket.org, Ably, OneUptime, Codelit; Redis 7'de **sharded pub/sub** ile kanal ölçeği — SO #78660481). **(2) SSE vs WS:** istemci **bir şey göndermiyorsa** SSE daha basit + otomatik reconnect + düz HTTP (güvenlik duvarı/proxy dostu); **çift yönlü** gerekiyorsa WS (Ably karar matrisi: "istemci geri bir şey gönderiyor mu? → evet ise tek başına WS"); SSE'nin HTTP/1.1 altında origin başına ~6 bağlantı tavanı var. **(3) Drift:** senkron başlasa da cihaz saatleri farklı hızda işler → **zaman içinde yine ayrışırlar** (Lund tezi, Roon, NTP FAQ) → sürekli düzeltme + referans saat şart. **(4) Otoriter saat:** güvenlik/senkar kararlarda **istemci zamanına güvenmek zafiyettir** (Baeldung, Jerry Shah, CommVault) → referans **sunucu saati** olmalı, istemci ofseti ölçülmeli. **(5) Sosyal oda UX'i:** Spotify Jam'de host katılımcıyı/kuyruğu yönetir, şarkı çıkarabilir, ortak kuyruk herkese açıktır; katılmak bağlantı yollarıyla (link/tap) kolaylaştırılmıştır → **host yetkileri + kolay davet + ortak kuyruk** kanonik. **(6) PHP WS:** Ratchet/ReactPHP (klasik), Swoole (olay tabanlı, binlerce bağlantı), FrankenPHP+Mercure — mevcut FPM istek modeliyle WS **uyuşmaz**, ya uzun süreç ya ayrı servis gerekir. **(7) WebRTC P2P:** mesh'de herkes herkese gönderir → **O(N²)**, pratikte **~3-4 katılımcıda biter**; sunucu tarafı kayıt/moderasyon da zorlaşır (Fora Soft, Betadrix, Ant Media). **(8) Sohbet kotası:** kota kullanıcı/IP/API-key bazlı sayılır, dinamik ayarlanır, **rate limit sağlayıcısının kendisi darboğaz olmamalı** (Zuplo, Gravitee, Speakeasy — 429 + `Retry-After`/`RateLimit` başlıkları). |
| Web Search **Uzun Açıklama** | **(a) WebSocket + Redis pub/sub (kaynak 1-8):** 2025-26 ölçek rehberleri üç adımda uzlaşıyor: (i) yük dengeleyicide **sticky session** (bağlantı ömrü boyunca aynı düğüm), (ii) durumu **paylaşımlı depoda** tutma (reconnect'te durumu geri yükleme), (iii) sunucular arası **Redis pub/sub fan-out** — publisher `publish(channel, msg)`, her WS düğümü kendi abonesi olan istemcilere yayar (websocket.org örnek kodu bunu birebir gösterir). Yalnız sticky session'a güvenmek "tek doğru" değil, uzun vadede shared-state + pub/sub tercih edilir; Redis 7 **sharded pub/sub** ile tek shard kanal tıkanması da aşılır. Bu, ADR-007'de PLANNED olan Redis adapter'ının **neden bu ADR için şart** olduğunu açıklar: FPM worker'ları birbirini duymaz, kanal ortak olmalıdır. **(b) SSE fallback (kaynak 9-16):** SSE tek yönlüdür ve bu **senkron yayınlama için yeterlidir** (sunucu → odadakiler: "konum 82. sn, duraklatıldı"); komutlar (play/pause/kick) ayrı HTTP POST ile taşınır. SSE düz HTTP üzerinden gider → WS'yi engelleyen proxy/güvenlik duvarlarında çalışır (Ably, RxDB, GetStream), otomatik reconnect vardır ve sticky session gerektirmez. Eksisi: çift yönlü büyüyen her özellik iki taşıma modeli bakımına mal olur → bu yüzden SSE **fallback** (birincil değil), uzun polling **son çare** olarak sınırlandırılır. **(c) Senkron oynatma + drift (kaynak 17-24):** literatür net: başlangıç senkronu kolay, **süreklilik zordur** — cihaz saatleri farklı hızda ilerlediği için (drift) ve anlık ofset taşıdığı için (skew) zamanla tekrar ayrışırlar (Lund tezi; Roon'da gruplu oynatma drift raporları; NTP "tüm saatlerin hatasını tahmin eder" yaklaşımı). Çözüm üçlüsü: (i) **sunucu saati otoriter** (epoch tabanlı referans), (ii) istemcide **ofset ölçümü** (round-trip ile) + düzenli düzeltme, (iii) **periyodik yeniden senkron** (drift toleransı aşınca konumu zorla). (d) Saat güveni (kaynak 25-32): istemci zamanına dayalı kararlar manipüle edilebilir (Baeldung, Jerry Shah, Security SE) → oda konumu/davet süresi gibi alanlarda **sunucu zamanı** esastır; istemci sahi "gösterim" için kullanılır, doğrulama için değil. **(e) Sosyal oda UX'i (kaynak 33-39):** Spotify Jam kanonu — host kimin katıldığını/kuyruğu yönetir ve şarkı çıkarabilir, ortak kuyruk herkese açıktır, katılım tek dokunuş/link ile; 32 kullanıcıya kadar örneklenir. Bu, kullanıcının onayladığı (d) "oda sahibi yetkileri" ve (f) "üyelik/davet" maddelerini bağımsız kaynakla doğrular; kapasite için endüstri örneği 32 (Jam) iken biz "örn. 50" diyoruz → şema `DEFAULT 10` ile birlikte §5.1/5'te tek değere bağlanır. **(f) PHP + WS (kaynak 40-47):** mevcut FPM istek/yanıt modeli uzun ömürlü WS taşıyamaz; seçenekler Ratchet/ReactPHP (tek süreç), Swoole/OpenSwoole (olay tabanlı, binlerce bağlantı/worker), FrankenPHP+Mercure, ya da **WS'yi ayrı servise (ör. Node) bırakıp PHP'nin REST kalmasını** ayırmaktır. Bu ADR taşıma kararını (WebSocket + Redis pub/sub) sabitler, **uygulayıcı runtime'ı §5.1/2'de ölçümle seçer** (bu ADR'de sayısal/nihai runtime vaadi yok → `⚠️ VERIFICATION REQUIRED`). **(g) WebRTC P2P reddi (kaynak 48-52):** mesh'te bant genişliği O(N²) → pratik sınır **3-4 katılımcı**; 50 kişilik odada matematiksel olarak imkânsız, ayrıca medya P2P aktığı için **sunucu tarafı moderasyon/kayıt/kalite kontrolü kaybolur** (Fora Soft, Betadrix, Ant Media, Trembit). Bizim ihtiyacımız **medya yayını değil, kontrol/konum/msg akışı** → WS + pub/sub yeterli; WebRTC yalnız ileride "sesli sohbet" için SFU ile değerlendirilebilir (ayrı ADR). **(h) Sohbet kotası (kaynak 53-60):** mesaj kotası ADR-013 gibi **kullanıcı/IP bazlı + pencere** ile sayılır, 429 + `Retry-After` ile cevaplanır; kota **dinamik** ayarlanabilir (yükseğe göre), kota motorunun kendisi darboğaz yapılmaz (Zuplo, Gravitee, Speakeasy). |
| Web Search **Paragraf Veri Uzun** | Gerçek zamanlı paylaşımlı dinleme 2025-26'da üç katmanlı bir mimariyle kuruluyor: **taşıma** — çift yönlü gerekiyorsa WebSocket (RFC 6455), engelli ağlarda **SSE'ye düşen tek yönlü senkron**, son çare kısa polling; **yayın** — istekler FPM'de farklı worker'lara dağıldığı için sunucular arası olay dağıtımı **Redis pub/sub fan-out** ile yapılır, yük dengeleyicide sticky session + dışarıda tutulan bağlantı durumu kullanılır (websocket.org, Ably, OneUptime, Codelit, SO #78660481); **zaman** — odadaki "aynı anda" hissi **sunucu otoriter saati** ile sağlanır: epoch tabanlı referans gönderilir, istemcide round-trip ofset ölçülür ve düzenli olarak yeniden senkronize edilir; çünkü cihaz saatleri farklı hızda ilerler ve zamanla tekrar ayrışır (Lund tezi, Roon, NTP FAQ), istemci zamanına güvenmek ayrıca bir güvenlik zafiyetidir (Baeldung, Jerry Shah). Ürünsel taraf Spotify Jam ile kanonikleşmiştir: **host yetkileri** (katılım, kuyruk sırası, şarkı çıkarma), **ortak kuyruk**, **tek dokunuşla davet** — bu üçü sosyal oda deneyiminin çekirdeğidir ve CoreMusic kapsamıyla birebir örtüşür. WebRTC P2P ise 3-4 katılımcı ötesinde O(N²) bant genişliğiyle çöker ve sunucu tarafı moderasyonu/kayıp denetimini ortadan kaldırır → bu ADR'de reddedilir. Sohbet tarafında istismar önleme, kullanıcı/IP bazlı oran sınırı (ADR-013 ruhu, 429 + `Retry-After`) ve sunucu tarafı moderasyon ile sağlanır; kapasite (örn. 50) ve drift toleransı gibi sayısal eşikler **ölçümle** kalibre edilir (bu ADR'de kesin sayı vaat edilmez). |
| Web Search **Sonucu** | 1) **WebSocket + Redis pub/sub + sticky/shared-state deseni doğrulandı** (websocket.org, Ably, OneUptime, Codelit, Praeclarum, VideoSDK, SO sharded pub/sub) → **§2 madde (h)** ve ADR-007 bağımlılığı. 2) **SSE'nin fallback olarak.sessizce doğru olduğu** doğrulandı (Ably karar matrisi, GetStream, Svix, RxDB, Design Gurus, Nimbleway) → **§2.2 fallback zinciri** (WS → SSE → polling). 3) **Sürekli drift düzeltmesi gerektiği** doğrulandı (Lund tezi, Roon, NTP FAQ, diyAudio, Handmade) → **§2 madde (b) + §2.2 periyodik yeniden senkron**. 4) **Sunucu otoriter saati** doğrulandı (Baeldung, Arpit, Jerry Shah, CommVault, Security SE) → **§2 madde (b)** "sunucu sahi = referans". 5) **Host yetkileri + ortak kuyruk + kolay davet** UX kanonu doğrulandı (Spotify Support/Newsroom/Community/PM vaka/UX Planet) → **§2 maddeleri (a), (d), (f)**. 6) **PHP'de FPM'in WS taşıyamadığı** doğrulandı (websocket.org PHP guide, Reddit r/PHP, Accesto, DeployHQ, StackShare) → **§5.1/2 runtime seçimi açık bırakıldı** (`⚠️`). 7) **WebRTC P2P'nin ~4 katılımcı sınırı + moderasyon kaybı** doğrulandı (Fora Soft, Betadrix, Ant Media, Trembit, DEV) → **§3-3 red gerekçesi**. 8) **Sohbet rate limit'i** (kullanıcı/IP bazlı, 429 + `Retry-After`, dinamik) doğrulandı (Zuplo, Gravitee, Speakeasy, Solo.io) → **§2 madde (c)** ADR-013'e bağlandı. **Toplam ~52 benzersiz adlandırılmış kaynak, 8 sorgu**; çapraz doğrulama ≥2 kaynak altı ana iddiada karşılanır (işaretli gerilim yok; tek açık nokta **ölçülmemiş spec değerleri** → §1.1-C `⚠️`). **⚠️ iki sınır:** (i) sayfa-içi tur yapılmadı → RFC 6455 handshake detayları, NTP ofset formülü ve Spotify Jam kapasite resmî sayfası üretim öncesi derin okunur (§5.1/10); (ii) "WS'yi hangi runtime taşıyacak" (PHP uzun süreç vs Node) bu araştırmada **karara dönüşmedi** → §5.1/2'ye bırakıldı. **Vault tarafı aynı resmi verdi:** oda/üye/kuyruk şeması + rate limit + oturum + player hook **IMPLEMENTED**, WS/SSE/Redis pub/sub/sohbet **PLANNED** → bu ADR **mimari karardır, kod taahhüdü değildir**. |
| Web Search **Alınan Karar** | **ADR-029 KABUL EDİLİR — YEDİ KAPSAM MADDESİ + TEKNOLOJİ KARARI:** **(a) Oda kurma/davet:** `room_code` benzersiz kodu + davet linki (`room_type`: public/private/invite_only şemada hazır); **(b) Senkron oynatma:** **sunucu saati = referans**, epoch-based senkron, istemcide ofset ölçümü + **periyodik drift düzeltme**; istemci saati yalnız gösterim, doğrulama değil; **(c) Sohbet:** oda içi mesaj akışı; **rate limit ADR-013** (kullanıcı/IP bazlı, 429 + `Retry-After`, fail-open korunur); yeni mesaj şeması §5.1/4; **(d) Oda sahibi yetkileri:** play/pause/kick/mute (+ kuyruk sırası/şarkı çıkarma — `role`/`is_muted` şemada hazır); **(e) Kapasite:** üst sınır yapılandırma (örn. 50) — şema `DEFAULT 10` ile §5.1/5'te tek değere bağlanır; **(f) Üyelik/davet modeli:** host/co_host/member/listener rolleri, link ile katılım, çevrimiçi durumu; **(g) Kayıt/arşiv (opsiyonel):** oda geçmişi (kim, ne zaman, ne çaldı) — silme/süre kuralları §5.1/9'da, REDACTED yok. **Teknoloji (mimari karar):** **WebSocket (RFC 6455) + Redis pub/sub** çok-fpm/process yayınlama — **Redis pub/sub bağımlılığı bu ADR'de şarttır** (ADR-007 PLANNED adapter'ın üstünde koşar); **SSE fallback** (WS engelliyse, tek yönlü senkron + ayrı HTTP komutu); **polling son çare** (kısa aralık, oran sınırı dahilinde); **sunucu sahipli saat + epoch senkron**; **WebRTC P2P reddedildi** (§3-3: O(N²) + moderasyon kaybı). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi**: WS ölçekleme/pub-sub (8), SSE fallback (8), drift (8), otoriter saat (8), sosyal oda UX (7), PHP WS (8), WebRTC sınırları (8), sohbet kotası (7) → **~52 benzersiz adlandırılmış kaynak, 8 sorgu**; çapraz doğrulama ≥2 kaynak tüm ana iddialarda karşılanır. **Vault tarafı aynı resmi verdi:** oda/üye/kuyruk/bildirim **şeması** + rate limit + oturum + player hook **IMPLEMENTED**; WS sunucusu, SSE, Redis pub/sub, oda servisleri, sohbet şeması **PLANNED** → bu ADR **mimari karardır**; "odalar çalışıyor" iddiası şu an **yalandır** (§1.1). Uygulama §5.1 adımlarına bağlıdır. **Kaynak listesi (~52):** 1) ably.com — scaling pub/sub with WebSockets and Redis · 2) oneuptime.com — how to scale WebSocket connections (2026-01) · 3) websocket.org — WebSockets at Scale (sticky ↔ shared state + Redis örnek) · 4) stackoverflow.com/q78660481 — Redis 7 sharded pub/sub · 5) praeclarumtech.com — NestJS + Redis pub/sub · 6) codelit.io — sticky sessions, Redis fan-out · 7) videosdk.live — WebSocket scale in 2025 (sticky) · 8) reddit.com/r/node — scaling websockets to 1000s of users · 9) ably.com — WebSockets vs SSE (karar matrisi) · 10) rxdb.info — WS/SSE/Polling/WebRTC karşılaştırma · 11) stackoverflow.com/q5195452 — WS vs SSE · 12) designgurus.substack.com — Stop Defaulting to WebSockets · 13) nimbleway.com — SSE vs WebSockets 2026 guide · 14) oneuptime.com — SSE vs WebSockets (2026-01) · 15) getstream.io — WebSocket vs SSE · 16) svix.com — websocket vs SSE · 17) community.roonlabs.com — grouped playback clock drift · 18) lup.lub.lu.se — synchronized streamed audio tezi (drift) · 19) softwareengineering.stackexchange.com — timestamped chunks senkron · 20) eecis.udel.edu — NTP FAQ (hata tahmini) · 21) handmade.network — NTP/saat sürüklenmesi · 22) linode.com/community — clock drift · 23) diyaudio.com — çoklu hoparlör senkronu · 24) serverfault.com — NTP slew · 25) baeldung.com — clock offset vs skew · 26) cam.ac.uk (dist-sys notları) — clock synchronisation · 27) arpitbhayani.me — clock sync nightmare · 28) shahjerry33.medium.com — clock skew/güven · 29) documentation.commvault.com — client clock skew detection · 30) security.stackexchange.com — clock sync & SSL · 31) researchgate.net — clock skew vs drift · 32) linkedin.com — distributed time lies · 33) support.spotify.com — Start or join a Jam · 34) newsroom.spotify.com — Jam duyurusu (2023) · 35) community.spotify.com — Jam · 36) medium.com/@curtisdave86 — Spotify Jam PM case (32 kullanıcı) · 37) uxplanet.org — Spotify Jamroom tasarımı · 38) reddit.com/r/truespotify — Jam açıklaması · 39) youtube.com — Jam ile birlikte dinleme · 40) websocket.org — PHP WebSocket guide (Ratchet/Swoole) · 41) reddit.com/r/PHP — Laravel WS verimliliği · 42) accesto.com — long-running PHP WebSocket · 43) deployhq.com — FrankenPHP/RoadRunner/Swoole 2025 · 44) stackshare.io — Ratchet vs Swoole · 45) reintech.io — PHP + Ratchet · 46) tsh.io — Swoole WS sunucusu · 47) ably.com — building realtime apps with PHP · 48) antmedia.io — mesh vs SFU vs MCU · 49) forasoft.com — WebRTC production (P2P ~4 katılımcı) · 50) betadrix.tech — mesh/SFU/MCU (pratik 3-4) · 51) trembit.com — SFU/MCU/P2P · 52) dev.to/karanpratapsingh — WebRTC at scale · (+ getstream.io SFU, bloggeek.me, medium.com/tosh-velaga · zuplo.com — rate limit 2026 · gravitee.io — rate limiting at scale · speakeasy.io — 429/RateLimit başlıkları · solo.io, databox.com, saascustomdomains.com, reddit.com/r/devops) |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| [[ADR-007-cache-namespace]] | **Redis bağımlılığı şart, adapter bugün YOK:** `shared/src/Cache/` yalnız `ApcuAdapter`/`MemoryAdapter`/`PageCacheAdapter` (ADR-007 bulgusu, `engine.md:302`). Bu ADR **Redis pub/sub'u zorunlu kılar** → Redis adapter + pub/sub kanalı olmadan oda canlı yayını **açılmaz** (açılırsa "çalışıyor" iddiası yalandır). APCu tabanlı sayaçlarda olduğu gibi TTL zorunlu; Redis yoksa **fallback zinciri** (§2.2) devreye girer, özellik kapanmaz. |
| [[ADR-011-session-management]] | **WS handshake oturuma bağlıdır:** bağlantı açılışında oturum doğrulanır; `SessionLifecycle.php:54-62` **30 dk'da bir `session_regenerate_id(true)`** döndüğü için uzun ömürlü WS bağlantısında **yeniden doğrulama/zaman aşımı** kuralı gerekir (bağlantı açık kalır ama oturum düşerse WS kapatılır veya yeniden bağlanır). ADR-011'in "Redis oturum deposu reddedildi, ileride supersede" notu (`:160`) bu ADR'nin Redis'e ilk gerçek bağımlılığıdır → **Redis kararı bu ADR ile netleşir, oturum deposu için ayrı ADR gerekir** (§5.1/11). |
| [[ADR-013-rate-limiting-apcu]] | **Sohbet kotası bu ADR'yi değiştirmez:** kendi API'mizin 429/`Retry-After` üretimi, fail-open davranışı ve auth 5/900s penceresi **korunur**; sohbet mesaj akışı aynı rate limiter'dan geçer (WS yolunda mesaj başına sayaç eklenir). Fail-open: kota bilgisi yoksa mesaj **durdurulmaz**, yalnız işaretlenir (süreklilik ilkesi). |
| [[ADR-018-footer-player-vaporwave]] | **Senkron oynatma mevcut player hook'una bağlanır:** durum makinesi (`STOPPED/PLAYING/PAUSED`) ve footer player durumları **değiştirilmez**; oda senkronu yalnız **durum + konum** yayınlar ve mevcut hook'u **besler** (In-Place — `PlayerController.js` ve `coreplayer.*.js` dosya adları değişmez). |
| [[ADR-005-ultrathink-protocol]] | Kod kanıtı olmayan her iddia etiketli: WS **0**, SSE **0**, pub/sub **0**, Redis adapter **0**, oda servisi **0**, sohbet şeması **0** → `⚠️ VERIFICATION REQUIRED` / **PLANNED**. Ölçülmemiş spec değerleri (500 ms/200 ms/<5 ms, kapasite 50 vs 10) sayısal kesinlik olarak yazılmaz. |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı: bu ADR'deki her wiki-link diskte var (§6'da doğrulanır); şablon zorunluluğu (Guardrail #16) — `.templates/adr/adr-template.md` iskeleti (7 bölüm + §1.3 9 alan). |
| In-Place Refactoring | Dosya adları **değiştirilmez**: `coremusic_social.sql`, `assets.coremusic.net/js/features/PlayerController.js`, `footer.php`, `RateLimiterMiddleware.php`, `SessionLifecycle.php`, `k14-ag/websocket-realtime.md` yalnız okunur; mevcut satırlara **ekleme** yazılır (§5.1/6), silme yok. |
| Frozen ADR-001-037 | Yalnız okunur + referanslanır (`AGENTS.md` §25.3 kural 2) — bu ADR frozen **değil**. |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme (`vault-utf8-writer append`). |
| REDACTED | Oda davet token'ları, oturum kimlikleri, Redis URL/parolaları bu ADR'ye **kopyalanmaz**; tüm sırlar yalnız `.env`'de yaşar. |
| Ölçüm-göre-kalibrasyon | Kapasite (50 vs `DEFAULT 10`), heartbeat (500 ms), ACK (200 ms), drift toleransı (<5 ms), polling aralığı ve WS bağlantı tavanları **ölçümle** belirlenir — bu ADR'de kesin sayı **yazılmaz** (`⚠️ VERIFICATION REQUIRED`). |

---

## 2. Karar (Decision)

**CoreMusic dinleme odaları + sohbet alanı YEDI kapsam maddesi ve BİR teknoloji kararıyla bağlayıcı ilan edilir:**

**(a) ODA KURMA / DAVET (link):**

| Öğe | Karar |
|---|---|
| Kurma | Yetkili kullanıcı oda kurar: ad, açıklama, tip (`public`/`private`/`invite_only`) — hepsi şemada hazır (`coremusic_social.sql:132-135`) |
| Davet | Benzersiz **`room_code`** (6-10 karakter, `:133` unique) üzerinden **davet linki** üretilir; linki olan (tipine göre) katılabilir |
| Bildirim | Davet, `social_notifications.notification_type='room_invite'` ile gider (`:249` **IMPLEMENTED şema**) |
| Katılım yolu | Link + (public odaya) arama/liste; `invite_only` yalnız davetle |

**(b) SENKRON OYNATMA (herkes aynı anda — sunucu saati referans):**

- **Referans = sunucu saati.** Oda durumu (`current_music_id`, `current_position_sec`, `is_playing` — şema `:137-139`) **sunucu epoch'u** ile yayınlanır; istemci saati **yalnız gösterim** içindir, karar/güven için kullanılmaz.
- **Epoch-based senkron:** bağlantı açılışında istemci `now` → sunucu epoch + round-trip ile **ofset** ölçer; oynatma konumu `sunucu_epoch - oda_başlangıcı` türetilir.
- **Drift düzeltme:** periyodik (heartbeat ile) yeniden senkron; ofset/drift toleransı aşılırsa konum **zorla düzeltilir** (§1.3-3: saatler yine ayrışır → sürekli düzeltme şart).
- **Hook:** sonuç, mevcut oynatıcı durum makinesine **durum + konum** olarak bağlanır (ADR-018 — dosyalara dokunulmadan).

**(c) SOHBET (mesaj akışı):**

- Oda içi mesaj akışı **WS kanalından** (fallback'te SSE + POST) taşınır; **kalıcılık** için ayrı mesaj şeması gerekir (**şema bugün YOK** → §5.1/4, `⚠️`).
- **Rate limit:** ADR-013'ün mevcut limiter'ı mesaj başına da çalışır (kullanıcı/IP bazlı, 429 + `Retry-After`, **fail-open korunur**); WS yolunda ek sayaç APCu (TTL zorunlu — ADR-007).
- **Moderasyon:** oda sahibi/kurucu mesajı kaldırabilir + kullanıcıyı susturabilir (`is_muted` şemada `:166`); spam'e karşı kota + raporlama (§4.3/4).

**(d) ODA SAHİBİ YETKİLERİ (play/pause/kick/mute):**

| Yetki | Kim | Dayanak |
|---|---|---|
| play / pause / seek | **host** (+ `co_host` istisnası yapılandırılabilir) | `role ENUM('host','co_host',…)` `:165` |
| kick (odadan çıkarma) | **host** | `listening_room_members` silme/soft-delete |
| mute (susturma) | **host/co_host** | `is_muted` `:166` |
| kuyruğa ekleme / sıralama | üye+ (host çıkarabilir) | `listening_room_queue:190` |
| Yetkisiz deneme | Red + olay kaydı (log/audit) | ADR-013/AUDIT ruhu |

**(e) KAPASİTE:** Oda üst sınırı **yapılandırma değeridir (örn. 50)**; şema `max_members DEFAULT 10` (`:136`) → **tek bir bağlayıcı değer** §5.1/5'te hizalanır (şema In-Place korunur, config güncellenir — bu ADR'de iki sayı birden "geçerli" değildir).

**(f) ÜYELİK / DAVET MODELİ:** Roller `host / co_host / member / listener` (şema `:165`); katılım link ile, çıkış serbest, **çevrimiçi durumu** `is_online`/`last_active_at` (`:167,169`) ile izlenir; üyesiz oda silinmeye (soft delete) bırakılabilir (`:143-144`).

**(g) KAYIT / ARŞİV (oda geçmişi — OPSİYONEL):** Oda kapanınca **özet geçmiş** (oda adı, süre, katılımcı sayısı, çalınanlar) tutulabilir; **tam mesaj arşivi varsayılan değildir** (gizlilik/bant) — açık hale getirilmesi ayrı karar + silme süresi ile (§5.1/9). `activity_feed.now_playing` (`:104`) mevcut akışa yazılabilir.

**(h) TEKNOLOJİ (mimari karar — senior architect):**

```
[Tarayıcı] ⇄ WebSocket (RFC 6455) ⇄ [WS Hub] ⇄ Redis pub/sub ⇄ (diğer WS düğümleri / FPM olayları)
     │  WS engelliyse: SSE (tek yönlü senkron) + HTTP POST (komut)
     │  o da yoksa:    kısa polling (son çare, oran sınırı dahilinde)
     └─ senkron zaman: sunucu epoch'u (sunucu saati = referans; istemci ofset ölçer)
```

| Karar | Değer | Durum |
|---|---|---|
| **Birincil taşıma** | **WebSocket (RFC 6455)** — çift yönlü (komut + durum), `room.sync/join/leave`, `player.command`, `chat.msg` | **PLANNED** (kod 0) |
| **Yayın (fan-out)** | **Redis pub/sub** — çok-FPM/process arası ortak kanal; sticky session + dışarıda tutulan bağlantı durumu ile | **PLANNED — BAĞIMLILIK ŞART** (ADR-007 adapter YOK) |
| **Fallback** | **SSE** (tek yönlü senkron; komutlar ayrı HTTP POST) — WS engelli ağlar için | **PLANNED** |
| **Son çare** | **Kısa polling** (oran sınırı + jitter; ADR-013 ruhu) | **PLANNED** |
| **Zaman** | **Sunucu sahipli saat + epoch-based senkron + periyodik drift düzeltme**; istemci saati doğrulama için **kullanılmaz** | Karar (PLANNED kod) |
| **Red** | **WebRTC P2P** — moderasyon/kayıp riski + O(N²) sınırı (§3-3) | Reddedildi |
| **Uygulayıcı runtime** | WS hub'unun PHP uzun ömürlü süreci mi ayrı servis mi olduğu **ölçümle** seçilir (§1.3-f) | ⚠️ `VERIFICATION REQUIRED` (§5.1/2) |

### 2.1 Neden Bu Seçenek?

- **WebSocket + Redis pub/sub tek başına çözüm çünkü** iki farklı sorunu ayrı ele alır: WS **bağlantıyı** (istemciye giden çift yönlü yol), Redis pub/sub **dağıtımı** (FPM worker'ları/düğümler arası ortak kanal). Literatür bu ikiliyi standart sayar (§1.3-1: websocket.org, Ably, OneUptime); tek düğüm/sticky-only tasarım büyüyünce kırılır.
- **SSE fallback olarak çünkü** senkron **yayın** esasen tek yönlüdür (sunucu → odadakiler: konum/duraklat/kick); SSE düz HTTP ile güvenlik duvarı/proxy engellerini aşar ve otomatik reconnect taşır (§1.3-2). Çift yönlü büyüyen komutlar için HTTP POST yanında kalır — ikinci taşıma modeli **yalnız fallback** olduğu için bakım yükü sınırlıdır.
- **Sunucu saati referans çünkü** istemci saati güvenilmez ve manipüle edilebilir (§1.3-4); "herkes aynı anda" iddiası ancak otoriter saat + sürekli drift düzeltmesiyle ayakta kalır (§1.3-3: başlangıç senkronu yetmez).
- **WebRTC P2P reddedildi çünkü** mesh O(N²) ile ~4 katılımcıda çöker, 50 kişilik odada imkânsızdır ve medya P2P gittiği için **sunucu tarafı moderasyon/kayıp denetimi kaybolur** (§1.3-7, §3-3). İhtiyaç medya yayını değil, **kontrol/konum/mesaj** akışıdır.
- **Redis bağımlılığı "şart" yazıldı çünkü** alternatifi (tek süreçte bellek içi hub) yatay ölçeklenemez ve bugünden yarına geçiş maliyeti çıkar; ADR-007 PLANNED yönüyle uyumlu tek yol pub/sub'tır. Redis yoksa özellik **açılmaz** (kandırma yok), fallback zinciri devreye girer.
- **Kapasite ve drift eşikleri sayı olarak sabitlenmedi çünkü** ikisi de ölçülmemiş (şema `DEFAULT 10` vs "örn. 50"; spec'te 500 ms/200 ms/<5 ms) → yanlış kesinlik yazmak yerine §5.1/5 ve §5.1/10'a bağlandı (ADR-005).

### 2.2 Teknik Detaylar

**a) Olay/kanal modeli (PLANNED):**

| Kanal (örnek) | Yazar | Okuyan | İçerik |
|---|---|---|---|
| `room:{id}:state` | WS hub (sunucu epoch'u ile) | Odadaki tüm WS/SSE | müzik, konum, `is_playing`, epoch |
| `room:{id}:chat` | WS hub (rate limit'ten sonra) | Odadaki tüm WS/SSE | mesaj (id, kullanıcı, ts-sunucu, metin) |
| `room:{id}:presence` | WS hub | Odadaki tüm WS/SSE | join/leave, `is_online`, rol |
| `room:{id}:control` | WS hub | Yalnız hedef üye | kick/mute bildirimi |

> Kanal adları **örnektir** (vault'ta tanım yok → `⚠️ VERIFICATION REQUIRED`); kanonik adlandırma §5.1/3'te sabitlenir.

**b) Senkron protokolü (epoch + drift):**

```
1) Bağlantı: istemci t0 gönderir → sunucu epoch E0 + ofset Δ (round-trip) döner
2) Konum  : pozisyon = (sunucu_now - oda_epoch_başlangıcı) ± Δ   [sunucu saati referans]
3) Heartbeat: sunucu periyodik epoch yayınlar; istemci Δ'yı yeniden ölçer
4) Tolerans: |Δ_değişim| > eşik → konum zorla düzeltilir (periyodik yeniden senkron)
5) Komut  : play/pause/seek istemciden → sunucu onaylar → yeni epoch tüm odaya yayınlanır
6) Eşikler: heartbeat/ACK/drift toleransı ÖLÇÜMLE belirlenir (spec: 500/200/<5 ms — doğrulanmadı)
```

**c) WS oturum doğrulaması (ADR-011 ile):** handshake'te oturum çerezi doğrulanır (ADR-010/011 zinciri); **30 dk rotasyon** (`SessionLifecycle.php:54-62`) ve idle/absolute timeout nedeniyle bağlantı **yeniden doğrulama** ile yaşar — oturum düşerse WS kapatılır/yeniden bağlanır; yetki değişimi (kick/rol) anlık olarak yayın ile düşer.

**d) Fallback zinciri (bağlantı denemesi sırayla):**

| Sıra | Taşıma | Ne taşınır | Koşul |
|---|---|---|---|
| 1 | WebSocket | durum + komut + chat (tek bağlantı) | kullanılabilir |
| 2 | **SSE** + HTTP POST | senkron yayın (tek yönlü) + komut POST'u | WS engelli (proxy/güvenlik duvarı) |
| 3 | **Kısa polling** | son durum + mesaj sayacı (delta) | SSE de yoksa — **son çare**; oran sınırı + jitter (ADR-013) |

**e) Sohbet ve rate limit (ADR-013'e dokunulmadan):** mesaj → kullanıcı/IP penceresi sayacı (APCu TTL / Redis pub/sub'dan sonra Redis sayaç opsiyonel) → aşımda **429 + `Retry-After`** (WS yolunda olay tipiyle) → fail-open (bilgi yoksa durdurma, işaretleme). Moderasyon: `mute`/`sil`/`kick` host/co_host yetkisi; her deneme olay kaydı.

**f) Kapasite ve üyelik:** katılma `room_code` + tip kontrolü → `member_count`/`max_members` karşılaştırması → aşımda ret; roller ve `is_online` şemadan okunur. Kapasite değeri **config** (§5.1/5).

**g) Kayıt/arşiv (opsiyonel):** özet geçmiş satırı (oda, süre, katılımcı sayısı, çalınan sayısı) — tam mesaj arşivi **kapalı varsayılan**; açıkça karar + silme süresi gerekir (§5.1/9).

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Yalnız SSE (tek yönlü yayın)** | Basit; düz HTTP; otomatik reconnect; sticky session yok; proxy dostu (§1.3-2) | Komutlar (play/pause/kick/chat) ayrı POST kanalı ister → iki taşıma modeli; chat etkileşimi gecikmeli/dağınık; oda içi anlık his zayıf | Senkron **yayın** için doğru, **etkileşimli oda + sohbet** için tek başına yetersiz → **fallback** olarak §2.2'ye alındı (birincil değil) |
| 2 | **Long polling / REST-only (mevcut HTTP)** | Sıfır yeni altyapı; ADR-013 rate limit aynen çalışır; en az karmaşık | Her oda durumu için istek fırtınası; gecikme (poll aralığı); WS'nin çift yönlülüğü yok; bağlantı başına maliyet yüksek | 50 kişilik odada her poll isteği sunucuya biner → ölçek/tecrübe kötü; literatür polling'i "son çare" sayar (§1.3-2) → yalnız **3. kademe fallback** |
| 3 | **WebRTC P2P (medya doğrudan katılımcılar arası)** | Düşük gecikme; medya sunucudan geçmez (bant avantajı 1:1) | Mesh O(N²) → pratikte **~3-4 katılımcı** (§1.3-7); 50 kişilik oda imkânsız; **sunucu tarafı moderasyon/kayıp/kayıt denetimi kaybolur**; NAT/ICE karmaşası | **Kapsam medya yayını değil** (kontrol/konum/chat) + moderasyon kaybı → reddedildi; ileride sesli sohbet için SFU ayrı ADR ile değerlendirilir |
| 4 | **Yönetilen real-time servisi (Firebase / Ably / Supabase Realtime)** | Hızlı başlangıç; ölçek onların sorunu; pub/sub hazır | Veri/olay üçüncü tarafın elinden çıkar (gizlilik + REDACTED yüzeyi); aylık maliyet; kilitlenme (vendor lock-in); ADR-007/011 "kendi yığınımız" ilkesiyle gerilim | Kendi vault/stack ilkesi (SSOT + kendi servislerimiz) + veri egemenliği → reddedildi; yalnız **karşılaştırma/benchmark** referansı olarak kullanıldı (§1.3) |
| 5 | **Redis pub/sub'sız tek süreçte WS hub (bellek içi kanal)** | Tek bağımlılık yok; basit başlangıç | Yatay ölçeklenemez (ikinci düğüm olayı görmez); süreç çökünce tüm odalar gider; FPM ile aynı "worker birbirini duymaz" sorunu yalnız daraltılır | Bu ADR'nin yayın şartını karşılamaz → **Redis pub/sub şart** (§2-h); tek süreç seçeneği yalnız **geliştirme/tek düğüm** yedeği olarak fallback zincirinde kalır |
| 6 | **İstemci saati referans (herkes kendi saatiyle oynatır)** | Sunucu işi yok; gecikme görünmez | İstemci saati manipüle edilebilir/saatsiz (§1.3-4); drift zamanla dağıtır → "aynı anda" iddiası çöker | **Sunucu sahipli saat + epoch + drift düzeltme** (§2-b) benimsendi; istemci sahi yalnız gösterim |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Şema hazırdır:** oda/üye/kuyruk/bildirim tabloları **IMPLEMENTED** (`coremusic_social.sql:129,161,190,245`) → yeni alan yaratmadan (In-Place) uygulamaya başlanır; `role`/`is_muted`/`room_code` karar maddeleriyle birebir örtüşür.
- **Taşıma kararı tek belgede kilitlenir:** WS + Redis pub/sub + SSE fallback + sunucu saati + WebRTC red — bugüne kadar parçalı olan spec'ler (`k14-ag/websocket-realtime.md`, `k10-uygulama/home-panel.md:182`, `VISION.md:96,132,209`) ilk kez tek karar altında toplanır.
- **Mevcut güvenceler korunur:** rate limit (ADR-013), oturum (ADR-011) ve player hook'u (ADR-018) **değiştirilmeden** kullanılır → sohbet kotası ve senkron oynatma mevcut güvenlik/kalıp sıfırdan yazılmaz.
- **Erişilebilirlik/düşük ağ:** SSE fallback sayesinde WS engelli kurumsal ağlarda özellik **tamamen kapanmaz** (yalnız senkron kalır); polling son çare olarak sınırlıdır.
- **Ölçek yolu net:** sticky session + dışarıda tutulan durum + Redis pub/sub → düğüm ekleme (yatay ölçek) önceden tasarlanmış olur (§1.3-1).
- **Ölçüm disiplini:** kapasite/drift/heartbeat eşikleri sayı olarak uydurulmadı → §5.1/5 ve /10 ile gerçek ölçüme bağlandı (ADR-005).

### 4.2 Olumsuz Sonuçlar

- **Kod işi tamamen önümüzde:** WS sunucusu **0**, SSE **0**, Redis pub/sub **0**, oda servisleri **0**, sohbet şeması **0** → bu ADR **mimari karardır**; "dinleme odaları çalışıyor" iddiası bugün **yalandır** (§1.1).
- **Yeni zorunlu altyapı:** Redis adapter + pub/sub, WS hub'u (uzun süreç veya ayrı servis) ve fallback katmanı → **üç yeni çalışma yüzeyi**; Redis artık opsiyonel değil (bağımlılık şart).
- **İki taşıma modeli bakımı:** WS (birincil) + SSE/POST (fallback) + polling (son çare) → test yüzeyi büyür; her ikisi de aynı olay sözleşmesine uymazsa tutarsızlık doğar (§4.3/5).
- **Sohbet moderasyon yükü:** anlık mesaj = spam/istismar yüzeyi; rate limit tek başına yetmez → susturma/kaldırma/rapor akışı da yazılmalı (§4.3/4).
- **Zaman karmaşıklığı:** epoch + ofset + periyodik yeniden senkron, hata yapılırsa kullanıcı "atlama/zıplama" görür; eşikler ölçülmemiş (§4.3/2).
- **Kapasite/şema çelişkisi:** "örn. 50" vs `DEFAULT 10` → hizalanana kadar iki farklı gerçeğe işaret riski (§1.1-B, §5.1/5).

### 4.3 Riskler

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|---------|------|-----------|
| 1 | **WS bağlantı sayısı / ölçek:** düğüm başına bağlantı tavanı aşılırsa bağlantı kopması, oda dağılması | 3 (Olası) | 4 (Yüksek) | Sticky session + dışarıda tutulan durum + Redis pub/sub ile yatay düğüm (§2-h); heartbeat/ping-pong ile ölü bağlantı temizliği; bağlantı tavanı **ölçümle** (§5.1/10); düşerse SSE'ye düş |
| 2 | **Drift/atlama:** yanlış eşik veya ölçülmemiş heartbeat/ACK → kullanıcılar arasında konum farkı ya da zıplama | 4 (Çok olası) | 3 (Orta) | Sunucu epoch + round-trip ofset + periyodik yeniden senkron (§2.2b); eşikler ölçümle (spec 500/200/<5 ms doğrulanmadı → `⚠️`); her düzeltme olay kaydı |
| 3 | **Redis tek nokta / bağımlılık:** Redis yoksa canlı yayını yok; ADR-007'de adapter henüz PLANNED | 3 (Olası) | 4 (Yyüksek) | Redis **açılış öncesi** şart (§1.4); bağlantı yoksa özellik **açılır ama yayın düşer** → SSE/polling fallback zinciri (§2.2d) + `log.md` alarmı; Redis kararı bu ADR'de netleşir (§5.1/11) |
| 4 | **Sohbet istismarı / moderasyon:** spam, taciz, istenmeyen davet | 4 (Çok olası) | 3 (Orta) | ADR-013 kotası (kullanıcı/IP, 429 + `Retry-After`, fail-open korunur) + mute/kick/kaldırma (§2-c,d) + rapor olayı; mesaj şeması §5.1/4 ile kayıt altına alınır |
| 5 | **Taşıma tutarsızlığı:** WS ve SSE/POST aynı olayı farklı yorumlarsa odalar "iki gerçeğe" bölünür | 3 (Olası) | 3 (Orta) | Tek olay sözleşmesi + tek epoch kaynağı (sunucu) — fallback yalnız **taşıma** değiştirir, **sözdizimi değil**; sözleşme testi §5.1/12 |
| 6 | **Kapasite/şema çelişkisi + ölçüm eksikliği:** 50 vs `DEFAULT 10`; WS bağlantı kapasitesi bilinmiyor | 4 (Çok olası) | 2 (Düşük) | §5.1/5 tek değer kararı; §5.1/10 ölçüm; bu ADR'de kesin sayı yok (`⚠️ VERIFICATION REQUIRED`) |
| 7 | **Oturum-WS çakışması:** 30 dk `session_regenerate_id` + timeout, uzun ömürlü WS bağlantısını düşürebilir | 3 (Olası) | 3 (Orta) | Handshake yeniden doğrulama + yeniden bağlanma akışı (§2.2c); bağlantı yaşam döngüsü testi §5.1/12 |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Oda servis katmanı:** `ListeningRoomService` (kur/katıl/ayrıl/liste) — `coremusic_social.sql` oda/üye/kuyruk tablolarını **okuma/yazma** (In-Place, şema değişmez); `room_code` ile davet linki üretimi | Backend Architect + Data Engineer | 3 gün |
| 2 | **WS hub + runtime seçimi:** WS sunucusu (RFC 6455 handshake, heartbeat/ping-pong) ve **uygulayıcı runtime kararı** (PHP uzun ömürlü süreç vs ayrı servis — §1.3-f, `⚠️ VERIFICATION REQUIRED`) → küçük PoC + ölçüm sonrası karar | Backend Architect + DevOps | 3 gün (PoC) |
| 3 | **Redis pub/sub + kanal sözleşmesi:** Redis adapter + `room:{id}:*` kanal adları/kalıpları (kanonik adlandırma burada sabitlenir — §2.2a `⚠️`), sticky session + dışarıda tutulan bağlantı durumu; **Redis yoksa devreye girmez** (açılış kapalı) | Backend Architect + DevOps | 2.5 gün |
| 4 | **Sohbet şeması + akışı:** oda mesaj tablosu (yeni — `CREATE TABLE` **bugün YOK**), mesaj gönderimi WS üzerinden, **ADR-013 rate limit** entegrasyonu (kullanıcı/IP, 429 + `Retry-After`, fail-open korunur), mute/kick/kaldırma eylemleri | Data Engineer + Backend Architect + Security Engineer | 3 gün |
| 5 | **Kapasite kararı (tek değer):** "örn. 50" vs şema `max_members DEFAULT 10` (`:136`) → config değeri + şema hizası (In-Place ekleme, varsayılan **dokunulmaz**); üye sayım/ret davranışı | Vault Steward + Data Engineer | 0.5 gün |
| 6 | **Senkron protokolü (epoch + drift):** sunucu epoch üretimi, istemcide ofset ölçümü (round-trip), heartbeat ile periyodik yeniden senkron, tolerans aşımında zorla düzeltme; **ADR-018 player hook**'una durum+konum bağlanması (`PlayerController.js` durum makinesi **değiştirilmez**) | Backend Architect + UI Designer | 3 gün |
| 7 | **Fallback zinciri:** WS → SSE (+HTTP POST komut) → kısa polling (son çare, oran + jitter); bağlantı deneme sırası ve olay sözleşmesi **aynı** kalır | Backend Architect | 2.5 gün |
| 8 | **Yetki + davet akışı:** host/co_host yetkileri (play/pause/kick/mute/kuyruk), `invite_only` kontrolü, `room_invite` bildirimi (`:249`), yetkisiz deneme olay kaydı | Backend Architect + Security Engineer | 2 gün |
| 9 | **Kayıt/arşiv (opsiyonel) kararı:** özet geçmiş mi, tam mesaj arşivi mi; silme süresi + gizlilik (REDACTED yok) → ayrı bir ADR (yeni numara **ADR-088+**) açılmadıkça bu madde **kapalı kalır** (varsayılan: yalnız özet) | Vault Steward + Data Engineer | 0.5 gün |
| 10 | **Ölçüm + kalibrasyon:** WS bağlantı tavanı/düğüm, heartbeat/ACK gecikmesi, drift ofset dağılımı, SSE/polling kullanım oranı, oda başına mesaj hızı → §2.2b eşikleri ve §4.3/1 bağlantı tavanı ölçümle belirlenir | QA Engineer + DevOps | 1.5 gün |
| 11 | **Redis oturum deposu ayrımı:** ADR-011 `:160` "Redis oturum deposu reddedildi / ileride supersede" notu ile bu ADR'nin Redis pub/sub bağımlılığı **iki ayrı karar** olarak ayrıştırılır (pub/sub = bu ADR; oturum deposu = ayrı ADR) | Vault Steward + Security Engineer | 0.5 gün |
| 12 | **Test paketi (debate şartı adayı):** epoch/ofset determinizm testi (tekrarlanabilir tohum), fallback sözleşmesi (WS=SSE=polling aynı olay), yetki reddi (kick/mute yetkisiz), rate limit 429 yolu, oturum düşüşünde WS yeniden bağlanma | QA Engineer + Backend Architect | 2 gün |
| 13 | **Doğrulama:** şablon tutamağı (`ADR-029`, hedef 0) · wiki-link disk kontrolü (§6) · `vault-utf8-writer scan` (mojibake 0) · `index.md:66` slug eşleşmesi · placeholder 0 · `log.md` append 1 satır | Vault Steward | 0.5 gün |
| 14 | **Şart 1a — Kapasite şema hizası (debate şartı):** `coremusic_social.sql:136` `max_members DEFAULT 10 → DEFAULT 50` + satır içi not `-- ADR-029 kapasite 50 (eski DEFAULT 10)` — sütun/ID **silinmez** (In-Place), tek bağlayıcı kapasite **50**; §1-§4'teki "DEFAULT 10" ifadeleri debate öncesi kanıt kaydıdır (uygulandı 2026-09-25) | Vault Steward + Data Engineer | 0.25 gün |
| 15 | **Şart 1b — WS auth ADR-011 hizası (debate şartı):** ADR-011 **3-kill** uyumlu WS auth — handshake'te oturum doğrulama, 30 dk `session_regenerate_id` rotasyonunda yeniden doğrulama, oturum düştüğünde WS kapatma/yeniden bağlanma (§2.2c, §4.3/7); uyumsuz WS auth ile oda **açılmaz** | Security Engineer + Backend Architect | 1 gün |
| 16 | **Şart 2 — Senkron drift + reconnect test paketi (debate şartı; §5.1/12'yi bağlar):** epoch/ofset determinizm testi (tekrarlanabilir tohum), periyodik yeniden senkron, tolerans aşımında zorla düzeltme ve oturum düşüşünde WS yeniden bağlanma — test paketi olmadan §5.1/6 ve §5.1/12 **kapanmaz** | QA Engineer + Backend Architect | 1.5 gün |
| 17 | **Şart 3 — Sohbet moderasyonu (debate şartı):** mute/kick/report + mesaj kaldırma akışı ve ADR-013 rate limit (kullanıcı/IP, 429 + `Retry-After`, **fail-open korunur**) — paket olmadan §5.1/4 ve §5.1/8 **kapanmaz** (§4.3/4) | Backend Architect + Security Engineer | 1.5 gün |

### 5.2 Geri Dönüş Planı

**Vazgeçme (madde bazlı):** (a) oda kurma/davet kapanırsa → `room_code` üretimi durdurulur, şema **dokunulmadan** boş kalır (In-Place korunur); (b) senkron oynatma vazgeçilirse → epoch yayını bayrakla kapatılır, oda **yalnız kuyruk/sohbet** ile devam eder (tek tek oynatma — hiçbir dosya adı değişmez); (c) sohbet kapanırsa → mesaj kanalı kapanır, rate limit ve tablo **dokunulmaz** (veri kalır, gönderim durur); (d) oda sahibi yetkileri sadeleşirse → host yetkileri `role` alanından okunmaya devam eder (ek kod bayrakla devre dışı); (e) kapasite düşürülürse → config değeri değiştirilir (şema varsayılanı zaten 10); (f) **Redis pub/sub kapanırsa** → özellik **tek düğüm** moduna (bellek içi hub) veya SSE/polling'e düşer; Redis adapter hiç kurulmazsa oda canlı yayını **açılmaz** (bilinçli: kandırma yok); (g) kayıt/arşiv → kapalı varsayılan olduğu için geri alınacak birikim yok.

**Tam geri dönüş:** dosya adları/şema değiştirilmediği için (In-Place korundu) geri dönüş = (1) WS hub süreci durdurulur/kaldırılır, (2) Redis pub/sub kanalları bırakılır (adapter kurulduysa kaldırılır — oturum deposu **etkilenmez**, ayrı karar §5.1/11), (3) fallback bayrakları kapatılır → istemciler yalnız HTTP (bugünkü hâl), (4) epoch yayını durur → oda tekil oynatmaya döner, (5) sohbet gönderimi durur (tablo kalır), (6) `vault-utf8-writer` yedeği (`<file>.bak`) eski içeriği verir, (7) `.ai/log.md`'ye tek satır revert append'i, (8) `.ai/.decisions/index.md:66` satırı `status: reverted` olur, (9) **bu ADR düzenlenmez** — `superseded by ADR-NNN` ile yeni ADR yazılır (şablon §6.3).

**Korunan geri dönüş güvencesi:** oda/üye/kuyruk **şeması**, rate limit (ADR-013), oturum yaşam döngüsü (ADR-011), player durum makinesi (ADR-018) ve `log.md` append-only geçmiş **bozulmaz**; geri döndürülecek olan **canlı yayın/oda/sohbet katmanıdır**.

### 5.3 Debate Kaydı

| Tur | Persona | Durum | Sonuç |
|---|---|---|---|
| — | **Debate başlamadı** | **⏳ PENDING** | Kanıt taraması (§1.1) + §1.3 web araştırması (8 sorgu / ~52 kaynak) tamamlandı; 3 tur / 20 persona debate **bu ADR'nin ilk turu bekleniyor** → sonuç bu tabloya `vault-utf8-writer append` ile eklenir (mevcut satırlara dokunulmaz) |
| 1 | **20 persona** | ✅ TUR 1 | Kanıt taraması sunuldu — kod: WS/SSE/Redis/room kodu **0** → PLANNED; şema `listening_rooms` (`coremusic_social.sql:129`, `max_members DEFAULT 10:136`), `listening_room_members:161`, `listening_room_queue:190` **IMPLEMENTED**; player hook `PlayerController.js:3`; rate limit fail-open (ADR-013); oturum `ROTATION_INTERVAL` 1800 sn (ADR-011) → **15 kabul/neutral + 4 uyarı** (QA: drift testi · Security: moderasyon · Critic: kapasite 10 vs 50 çelişkisi + WS auth ADR-011 hizası şart) |
| 2 | **İtiraz→çözüm (4 madde)** | ✅ TUR 2 | (1) `max_members DEFAULT 10` ↔ ADR kapasite 50 çelişkisi → şema düzeltmesi/migration → **şart 1a** · (2) WS auth oturum kilidi → ADR-011 3-kill uyumlu WS auth → **şart 1b** · (3) Drift testi yok → senkron drift + reconnect test paketi → **şart 2** · (4) Sohbet abuse → moderasyon (mute/kick/report) + rate limit → **şart 3** |
| 3 | **20 persona** | ✅ TUR 3 — **KABUL** | Oy: **19 kabul / 1 çekimser / 0 red** → KABUL; 3 bağlayıcı şart §5.5'e eklendi; Tech Lead §7 **⏳ → ✅** |

### 5.4 Açık PLANNED Kalemleri (kabul ≠ tamamlandı)

| Kalem | Durum | Kapanış |
|---|---|---|
| WebSocket sunucusu + istemcisi | ❌ PLANNED (kod 0 eşleşme) | §5.1/2 |
| Redis adapter + pub/sub (bağımlılık şart) | ❌ PLANNED (ADR-007: Redis adapter yok) | §5.1/3 |
| SSE fallback + polling son çare | ❌ PLANNED (EventSource/SSE 0) | §5.1/7 |
| Oda servisleri (kur/katıl/davet/yetki) | ❌ PLANNED (kod 0; **şema IMPLEMENTED**) | §5.1/1, §5.1/8 |
| Sohbet şeması + mesaj akışı | ❌ PLANNED (**tablo yok**) | §5.1/4 |
| Epoch senkron + drift düzeltme | ❌ PLANNED (kod 0; spec 500/200/<5 ms doğrulanmadı → `⚠️`) | §5.1/6, §5.1/10 |
| Kapasite hizası (50 vs `DEFAULT 10`) | ✅ Şart 1a uygulandı (2026-09-25) — şema `max_members DEFAULT 50` + yorum; §1-§4'teki "DEFAULT 10" ifadeleri debate öncesi kanıt kaydı | §5.1/5, §5.5 |
| WS hub runtime (PHP uzun süreç vs ayrı servis) | ⚠️ `VERIFICATION REQUIRED` (karar verilmedi) | §5.1/2 |
| Kayıt/arşiv (oda geçmişi) | 🔒 OPSİYONEL (varsayılan kapalı) | §5.1/9 |
| Debate / Tech Lead | ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 KABUL — §5.3) · Tech Lead ✅ (§7) · şartlar §5.5 (3 madde) | §5.3, §5.5, §7 |

---

### 5.5 Debate Şartları

Debate **✅ TAMAMLANDI (3 tur / 20 persona → 19 kabul / 1 çekimser / 0 red = KABUL)** — bağlayıcı **3 şart** (1a-1b, 2, 3) şablon §6.3 uyarınca bu alt başlığa eklendi (mevcut metin korunur):

**Şart 1 — Şema kapasite + WS auth hizası (§5.1/14, §5.1/15):**
- **1a)** `max_members DEFAULT 10` (`coremusic_social.sql:136`) ↔ ADR kapasite 50 çelişkisi → şema düzeltmesi/migration: sütun **silinmeden** `DEFAULT 10 → DEFAULT 50` + satır içi not `-- ADR-029 kapasite 50 (eski DEFAULT 10)` (**uygulandı 2026-09-25**; yedek `C:/temp/opencode/vault-backups/coremusic_social.sql.bak`) → tek bağlayıcı kapasite **50**.
- **1b)** WS auth oturum kilidi → **ADR-011 3-kill uyumlu WS auth** şarttır: handshake'te oturum doğrulama · 30 dk `session_regenerate_id` rotasyonunda yeniden doğrulama · oturum düştüğünde WS kapatma/yeniden bağlanma (§2.2c, §4.3/7); uyumsuz auth ile oda **açılmaz**.

**Şart 2 — Senkron drift + reconnect test paketi (§5.1/16 → §5.1/6, §5.1/10, §5.1/12):** epoch/ofset determinizm testi (tekrarlanabilir tohum), periyodik yeniden senkron, tolerans aşımında zorla düzeltme ve oturum düşüşünde WS yeniden bağlanma testleri QA ile yazılır — test paketi olmadan §5.1/6 ve §5.1/12 adımları **kapanmaz**.

**Şart 3 — Sohbet moderasyonu (§5.1/17 → §5.1/4, §5.1/8):** mute/kick/report + mesaj kaldırma akışı ve ADR-013 rate limit (kullanıcı/IP, 429 + `Retry-After`, **fail-open korunur**) birlikte uygulanır — moderasyon olmadan sohbet (madde c) **açılmaz** (§4.3/4).

> **Not (2026-09-25):** Şart 1a bu turda uygulandı; §1.1-B, §2-e, §4.2 ve §4.3/6'daki "DEFAULT 10" ifadeleri debate **öncesi** kanıt/risk kaydıdır (silinmedi — In-Place Refactoring).

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail'ler (bu ADR'nin yazım usulü) |
| [[../../AGENTS.md]] | Agent registry — §5 domain (`*.php` → Backend, `*.sql` → Data), §25.3 kural 2/3 (frozen + log append-only) |
| [[../../WORKFLOW.md]] | Süreçler — uygulama adımlarının faz bağlamı |
| [[../index]] | Karar dizini — **satır 66** `[[ADR-029-listening-rooms-social]]` (slug ✅); `:127` `R-005-rest-only-api` dead-link notu ("WebSocket gerekli") |
| [[../../index]] | Master katalog — `:646` "decisions/accepted/ADR-029-listening-rooms-social \| Listening rooms social \| Social" |
| [[../../brain]] | Mimari karar özeti — `:984` "ADR-029 \| Sosyal dinleme odaları" |
| [[../../keys]] | Keyword haritası — `:264` "ADR-029 \| listening rooms, social" |
| [[ADR-007-cache-namespace]] | Cache katmanı — Redis adapter **YOK** (Apcu/Memory/PageCache) → bu ADR'nin Redis pub/sub bağımlılığı onun PLANNED yönüyle hizalı (§1.4, §2h, §4.3/3) |
| [[ADR-011-session-management]] | WS handshake oturum doğrulaması + 30 dk `session_regenerate_id` rotasyonu → uzun ömürlü bağlantı kuralı (§1.4, §2.2c); `:160` Redis oturum deposu notu (§5.1/11) |
| [[ADR-013-rate-limiting-apcu]] | Sohbet rate limit dayanağı — 429/`Retry-After` + fail-open **IMPLEMENTED** (`RateLimiterMiddleware.php:30-32,48-56`) (§1.4, §2c, §2.2e) |
| [[ADR-018-footer-player-vaporwave]] | Player durum makinesi (`assets.coremusic.net/js/features/PlayerController.js:12-13`) — senkron oynatmanın bağlandığı **IMPLEMENTED hook** (§1.1-C, §1.4, §5.1/6) |
| [[ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1, §1.3, §4.3) |
| [[ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı + şablon zorunluluğu + UTF-8 tek arayüz (§1.4, §5.1/13) |
| [[../../architecture/k14-ag/websocket-realtime]] | WS spec — `:16` RFC 6455/8441 · `:106-115` `room.sync/join/leave` + heartbeat · `:117-140` multi-room sync (heartbeat 500 ms, ACK 200 ms, drift <5 ms) → **PLANNED**, bu ADR'nin taşıma dayanağı (§1.1-C, §4.3/2) |
| [[../../architecture/k10-uygulama/home-panel]] | Oda paneli spec — `:157-159` same-source/independent senkron · `:182` "oda durumu WebSocket ile real-time" (PLANNED) |
| [[../../.templates/adr/adr-template]] | İskelet — 7 bölüm + §1.3 9 alan (Guardrail #16) |
| [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol]] | §1.3 web araştırma protokolü (diskte VAR ✅) |
| `.ai/.sql/mysql/coremusic_social.sql` (kod yolu, wiki-link değil) | `listening_rooms:129` (`room_code:133`, `max_members DEFAULT 50:136` (şart 1a — eski `DEFAULT 10`), `is_playing:139`) · `listening_room_members:161` (`role:165`, `is_muted:166`, `is_online:167`) · `listening_room_queue:190` · `social_notifications:245` (`room_invite:249`) · `comments:23` · `activity_feed:101` (**IMPLEMENTED şema**) |
| `shared/src/Cache/` (kod yolu) | `ApcuAdapter.php`, `MemoryAdapter.php`, `PageCacheAdapter.php` — **Redis adapter 0** (§1.1-C) |
| `assets.coremusic.net/js/features/PlayerController.js` (kod yolu) | `:3` "Footer player state machine" · `:12-13` `#status = 'STOPPED'` durum makinesi (IMPLEMENTED hook) |
| `shared/src/Middleware/RateLimiterMiddleware.php` (kod yolu) | `:30-32` fail-open · `:48-56` 429 + `Retry-After` (sohbet kotasının dayanağı) |
| Debate şartları (§5.5 · §5.1/14-17) | **3 bağlayıcı şart** — (1a) şema kapasite 50 hizası (uygulandı) · (1b) ADR-011 3-kill WS auth · (2) drift + reconnect test paketi · (3) moderasyon (mute/kick/report) + ADR-013 rate limit → debate 3 tur / 20 persona **19/1/0 KABUL** (2026-09-25) |

> **Durum özeti:** debate **✅ TAMAMLANDI (3 tur / 20 persona → 19/1/0 KABUL, §5.3)** · Tech Lead **✅ (§7)** · Arch Lead **⏳ PENDING** · şartlar **§5.5 — 3 bağlayıcı madde (1a-1b, 2, 3)** · frozen **YOK** · kod: oda/üye/kuyruk/bildirim **şeması** + rate limit + oturum + player hook **IMPLEMENTED**, WS/SSE/Redis pub/sub/oda servisleri/sohbet şeması/senkron protokolü **PLANNED** (§1.1) · **Redis pub/sub bu ADR'de BAĞIMLILIK ŞARTTIR** (ADR-007 adapter YOK ile hizalı).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar içeriği — 7 kapsam maddesi) |
| Tech Lead | — | 2026-09-25 | ✅ (debate ✅ — 3 tur / 20 persona → 19 kabul / 1 çekimser / 0 red = KABUL; 3 şart §5.5) |
| Arch Lead | — | 2026-09-25 | ⏳ PENDING |

**Debate Kaydı (§5.3 · §5.5):** 3 tur / 20 persona → **19 kabul / 1 çekimser / 0 red = KABUL** · 3 bağlayıcı şart: **(1a)** şema kapasite hizası `max_members DEFAULT 10 → 50` (uygulandı) · **(1b)** ADR-011 3-kill uyumlu WS auth · **(2)** senkron drift + reconnect test paketi · **(3)** moderasyon (mute/kick/report) + ADR-013 rate limit · Tech Lead **✅** · Arch Lead **⏳**.

---

**1.0.0 | 2026-09-25 | Created**
**1.1.0 | 2026-09-25 | Debate 3/20 (19/1/0 KABUL) + Tech Lead ✅ + 3 şart (§5.5; 1a-1b/2/3) + şema max_members 10→50**

*ADR-029 — Listening Rooms Social (Tam Senkron Oda + Sohbet · WebSocket + Redis Pub/Sub · Sunucu Sahipli Saat/Epoch · SSE Fallback · Oda Sahibi Yetkileri · Davet/Üyelik · Kapasite · Opsiyonel Kayıt)*
*Authority: ADR-029 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
