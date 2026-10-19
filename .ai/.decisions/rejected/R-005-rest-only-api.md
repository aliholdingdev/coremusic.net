---
title: "CoreMusic — R-005: REST-Only API (REDDEDİLMİŞ — WebSocket Gerekli)"
type: "architecture-decision"
category: "backend"
date: "2026-10-02"
updated: "2026-10-02"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-005 red kararı: CoreMusic gerçek zamanlı yüzeyinde 'REST-Only / istek-bazlı HTTP tek taşıma' KABUL EDİLMEZ. Gerekçe: karar dizini index.md:130 'REST-Only | WebSocket gerekli' + ADR-029:64 (dead-link tespiti) + ADR-029:233 (alternatif 2 'Long polling / REST-only' ret satırı) + ADR-029:216-218 (WS → SSE → polling fallback zinciri). Yerini alan: ADR-029-listening-rooms-social + ADR-020-api-public-security (ikisi de diskte). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-005: REST-Only API (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI — RED DOĞRULANDI**) — **Tarih:** 2026-10-02 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona — 19/1/0)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-005-rest-only-api` (dizin otoritesi: [[../index]] **satır 130** — dosya adı ile birebir hizalı ✅; 2026-10-02 glob doğrulaması: `rejected/` içinde `R-005*` = **0 dosyaydı** → bu işlemde yazıldı)
> **Dizin satırı:** `| [[R-005-rest-only-api]] <!-- dead-link: R-005-rest-only-api no source 2026-09-24 --> | REST-Only | WebSocket gerekli |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/1)
> **İlgili kararlar:** [[../accepted/ADR-029-listening-rooms-social]] (yerini alan — WebSocket + Redis pub/sub + SSE fallback; `:64` bu red'i "REST-Only" diye sayar, `:233` alternatif 2'yi ret eder, `:216-218` fallback zinciri) · [[../accepted/ADR-020-api-public-security]] (yerini alan — REST API yüzeyi + versiyonlama + rate limit sözleşmesi) · karar dizini [[../index]] §5.
> **R-001/R-002/R-003/R-004 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — her hedef `Test-Path` / glob ile doğrulandı (ADR-005/007/011/013/016/018/020/024/029/030/032 + brain + VISION + R-001…R-004 = diskte VAR).

---

## 1. Bağlam (Context)

CoreMusic'in sosyal katmanı (dinleme odaları, oda senkronu, sohbet) **gerçek zamanlı**dır; buna karşılık karar dizini "REST-Only" seçeneğini çoktan **reddetmiş** (`index.md:130` — "REST-Only | WebSocket gerekli") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu + ADR-029 içindeki üç parmak izi vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-02 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:130` → `[[R-005-rest-only-api]]` + "REST-Only" + "WebSocket gerekli" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`…`R-004-*` ; `R-005*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | Red gerekçesi başka yerde yazılı mı? | ADR-029 `:64` (§1.1 — "REST-Only" kararı dead-link, kaynak dosya yok) · ADR-029 `:233` (§3 alternatif 2 — "Long polling / REST-only (mevcut HTTP)" ret satırı) · ADR-029 `:356` (§6 — `R-005-rest-only-api` dead-link notu) | ✅ **3 referans** — ama hiçbiri red'in kendi metni değil, **gerekçe ailesi** |
| 4 | Yerini alan kararlar diskte? | [[../accepted/ADR-029-listening-rooms-social]] **VAR** (`status: accepted`) · [[../accepted/ADR-020-api-public-security]] **VAR** (`status: accepted`) | ✅ **IMPLEMENTED** (2026-10-02 `Test-Path` = True/True — glob ile doğrulandı) |
| 5 | ADR-029'un uzun gerekçesi diskte mi? | `:18` karar cümlesi (WebSocket + Redis pub/sub + SSE fallback + sunucu saati) · `:179-180` "neden WS + Redis" · `:216-218` fallback zinciri (WS → SSE + POST → kısa polling) · `:233` REST-only ret gerekçesi ("50 kişilik odada her poll isteği sunucuya biner") | ✅ **DISKTE** (bağlayıcı gerekçe) |
| 6 | Kod yüzeyinde gerçek zamanlı taşıma izi? | `WebSocket\|Ratchet\|Swoole\|pusher\|socket.io\|EventSource` → `assets.coremusic.net/**` = **4** (hepsi tek dosya: `tests/mocks/player-api.mock.js:130/:134/:136/:183` — EventSource **mock**'u) · `shared/**` = **0** · `home.coremusic.net/**` = **0** · `.github/**` = **0** · `public.coremusic.net` = **dizin YOK** · `composer.json` ×5 (api/auth/home/media/shared) `ratchet\|swoole\|pusher\|reactphp\|mercure` = **0** | ✅ **WS sunucusu 0, SSE sunucusu 0** — üretim kodunda **hiçbir** gerçek zamanlı taşıma yok (ADR-029 `:35-40` bulgusunu **yeniden doğruladı**) |
| 7 | EventSource izi ne? | `createMockEventSource` → yalnız `player-api.mock.js:136` (tanım) + `:183` (export); **çağıran = 0** | ⚠️ **TEST MOCK, bağımlılık DEĞİL** — üretilen SSE/WS akışı **0**; mock test fixture'ıdır (PLANNED test yüzeyi) |
| 8 | Vault spec yüzeyi? | `.ai/architecture/**` WebSocket eksenli tarama = **112 isabet / 21 dosya** (en büyüğü `k14-ag/README.md` 46, `k14-ag/websocket-realtime.md` 21, `k10-uygulama/notification-panel.md` 8, `k8-servis/notification-service.md` 7) · `k14-ag/websocket-realtime.md:16` "RFC: 6455 (WebSocket), 8441" ✅ doğrulandı · `:111` `room.sync` ✅ · `k10-uygulama/home-panel.md:182` "Her oda ve cihaz durumu WebSocket üzerinden real-time güncellenir" ✅ · `VISION.md:96` "WebRTC/WebSocket multi-room audio", `:209` "WebSocket desteği" ✅ | ✅ **PLANNED spec** (kod **0** — §1.1/6 ile birlikte: karar mimari, uygulama yok) |
| 9 | `rejected/index.md` durumu? | Dosya **VAR** (757 bayt, v1.0.1, `total: 12`) ama § tablosu **BOŞ** — 12 red'in hiçbiri satırlanmamış | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |
| 10 | Debate sonucu? | 3 tur / 20 persona — Tur 3 oyu: 19 kabul / 1 çekimser / 0 red | ✅ **RED DOĞRULANDI** (kayıt §7; 3 bağlayıcı şart §5.3) |
| 11 | Araştırma protokolü diskte? | `.claude/skills/prompt-maker/references/10-web-research-protocol.md` → `Test-Path` = **True** | ✅ OKUNDU (§1.3 bu protokolle üretildi) |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — üretim kodunda WS/SSE/long-polling **0** (§1.1/6); "reddedildi" = "REST-only taşıma **kurulmadı** ve gerçek zamanlı ihtiyaç ADR-029 ile **WS + Redis pub/sub**'a bağlandı". Tek EventSource izi bir **test mock**'udur (§1.1/7) — red'i "SSE de yok" diye savunmak için kullanılamaz, "SSE üretimi de yok" denir.

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:130` bir sonuç cümlesi ("WebSocket gerekli") ama **ne 2025-26 ekosistem kanıtı (WebSocket vs SSE vs polling karar matrisi, HTTP/2 push'inin ölümü) ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "REST-only neden yok, SSE tek başına yeterli olur muydu, bugün de mi yok, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı + satır no uyuşmazlığı var.** Uzun gerekçe ADR-029 `:64/:233/:356` içinde dağınık; ayrıca ADR-029 `:64` ve `:356` dizini **`:127`** olarak gösterirken disk ölçümü **`:130`**'dur (§7.1/6) — red'in kendi dosyası olmadığı için hem satır no hem gerekçe başka ADR'nin satırına sıkışmış durumda.
3. **Güncellik sorunu + ters kanıt riski.** 2025-26'da **SSE trendi güçlü** (AI akışı, "çoğu uygulama WS'a ihtiyaç duymuyor" anlatısı — §1.3): "REST-only" reddinin bugün **SSE lehine mi yoksa WS lehine mi** olduğu açıkça yazılmazsa karar yanlış gerekçeyle savunulur. Dürüst ayrım §1.3 ve §2.1/3'tedir: red, **"sunucuya istek at, push kanalı olmasın"** modelini reddeder; SSE'yi de reddetmez — SSE ADR-029'da **fallback**tir.
4. **Koşul tanımsız.** "Hangi durumda REST-only/polling'e geri dönülür?" (ölçülmüş oda ölçeği/gecikme kanıtı, Redis adapter'ın hiç kurulmaması, ADR-029 revizyonu) **hiç belgelenmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-02'de yapıldı — **5 sorgu**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "WebSocket vs SSE vs long polling 2025 2026 when to use real-time API patterns" → (2) "HTTP/2 server push deprecated removed browser Chrome 2026 HTTP/3 no push" → (3) "SSE streaming AI agents 2026 trend server-sent events replace WebSocket" → (4) "Cloudflare Workers Durable Objects WebSocket pricing vs Pusher Ably cost 10k concurrent connections 2026" → (5) "WebSocket connection limits reverse proxy nginx idle timeout buffering SSE proxy problems 2025" |
| Web Search **Konusu** | **(1)** gerçek zamanlı API taşıma seçimi (WS / SSE / long-polling) 2025-26 karar matrisi · **(2)** HTTP/2 server push'un tarayıcı kaderi (REST-only'nin "push'u HTTP ile hallederiz" varsayımı geçerli mi?) · **(3)** AI-agent akışında SSE trendi (SSE tek başına neyi çözer, neyi çözmez) · **(4)** WebSocket'in barındırma/ölçek **maliyeti** (harici servis fiyatlandırması vs kendi sunucusu) · **(5)** proxy/geçit engelleri: nginx idle timeout, buffering, SSE ve WS'nin üretim kırılganlıkları |
| Web Search **Bağlam** | CoreMusic: ADR-029 (WebSocket + Redis pub/sub + SSE fallback — diskte; Redis adapter ADR-007'de **PLANNED**), ADR-020 (REST API yüzeyi + versiyonlama + rate limit), ADR-013 (APCu rate limit IMPLEMENTED), ADR-011 (oturum rotasyonu 1800 sn), ADR-032 (sözleşme versiyonlama), ADR-030 (öneri motoru "Real-time Learning" spec'i) · üretim kodunda WS/SSE **0** (§1.1/6) · hedef soru — *"REST-only red'i bugün hâlâ doğru mu, SSE tek başına yeterli olur muydu, HTTP push hayali mi, maliyet/proxy/ölçek riskleri ne?"* |
| Web Search **Kısa Açıklama** | **(1)** Karar matrisi netleşti: **tek yönlü** (sunucu → istemci) ise **SSE**, **çift yönlü** ise **WebSocket**, düşük/görece seyrek güncelleme ise **polling** (websocket.org karşılaştırma rehberi, Ably, Vercel, GetStream, Design Gurus "Stop Defaulting to WebSockets"); SSE'yi atlayıp WS'a başlamak 2026'da **tanınan bir hata** (interviewnoodle, Medium "brutally honest comparison") sayılırken, **sohbet/oyun/işbirliği** (yani oda komutu + chat) hâlâ WS sütununda durur (hinterbuild 2026 rehberi). **(2)** HTTP/2 server push **ölü**: Chrome 106'da varsayılan kapatıldı, Google 2020-11'de removal niyetini açıkladı, Firefox da kaldırdı (developer.chrome.com, Wikipedia, flaviocopes, OneUptime 2026-01-25, zhul.in 2025-11-05) → **"REST + HTTP push ile gerçek zamanlı" yolu tarayıcıda yok**; 103 Early Hints yalnız ön-sorgu ipucudur. **(3)** SSE trendi **AI akışında** güçlü (2026: "SSE WebSockets'u sessizce eziyor" — DataSoft; "çoğu uygulama WS'a ihtiyaç duymaz" — Medium; Laramateo, codercops), ama websocket.org rehberi **aynı anda** ters yönde de yazar: tool-call/interrupt/çok-turlu agent için WS geri geliyor → trend **iki yönlü**. **(4)** Maliyet: harici servisler bağlantı-bazlı (Pusher free ~100-200 eşzamanlı, $49/ay 500; Ably $29/ay 10k eşzamanlı; Cloudflare Durable Objects **$5/ay minimum** + compute süre faturası, açık WS ile idle eden DO örneğinde ~$412/ay hesap örneği) → **kendi sunucusunda WS + Redis pub/sub** (ADR-029 seçimi) harici faturayı keser ama **Redis bağımlılığını** şart kılar (ADR-007 adapter bugün YOK). **(5)** Proxy: nginx SSE'de **buffering + idle timeout** ayarı şart (OneUptime 2025-12-16; CopilotKit #6980 sessiz SSE stream'i proxy'nin kapatması), WS'de `proxy_read_timeout` + ping/pong şart (websocket.org nginx rehberi; nginx topluluğu ~9000 bağlantıda dengesiz dağılım raporu) → **her iki protokol de düzgün yapılandırılmadan ölçeklenmez**. |
| Web Search **Uzun Açıklama** | **(1)** Vickybytes, Pristren, Hinterbuild (2026), InterviewNoodle, Medium, SystemDR ve WebSocket.org aynı karar ağacında uzlaşıyor: *istemci bir şey gönderiyor mu?* → evet ise tek başına WS; hayır ve sıkıştırılmış tek yönlü akış ise SSE (otomatik reconnect, CDN/proxy dostu, sticky session yok); istek başına güncelleme düşükse polling yeter. Design Gurus ve GetStream, SSE'nin **sticky session gerektirmemesini** ve **otomatik yeniden bağlanmasını** ana avantaj sayar; Pristren, düşük frekansta SSE/polling'in **daha ucuz**, yüksek frekansta WS'ın **mesaj başına daha verimli** olduğunu yazar — yani "REST-only"nın maliyet avantajı **frekansa bağlıdır**, oda senkronu (500 ms heartbeat, anlık komut) yüksek frekans tarafındadır. Streamkap, gerçek zamanlı UI vakalarının **çoğunun** çift yönlü iletişim istemediğini; çift yönlü büyüyen her özelliğin ikinci bir taşıma modeli (SSE + POST) bakımına mal olduğunu vurgular. **(2)** Push'un kaderi: Chrome blog'u "Remove HTTP/2 Server Push" (dev 106'da disable → removal), Wikipedia removal maddesi, TestMu "server push is dead in browsers", Cloudflare topluluğu "HTTP/2 Push is deprecated and no longer used" → **HTTP/3'te de push yok**; bu, red'in "push kanalı istek-bazlı HTTP'de kurulamaz" dayanağını **kuvvetlendirir**: sunucunun inisiyatifle veri gönderebilmesi ya uzun-ömürlü bir kanal (WS/SSE) ya da istemcinin tekrar tekrar sorması (polling) ile mümkündür. **(3)** SSE/AI trendi: DataSoft, Medium (Coders.Stop), Laramateo, Codercops, Reddit r/dotnet (agent SSE + state persistence), Tianpan (SSE/WS/gRPC backpressure) — hepsi **sunucu→istemci tek yönlü akışta** SSE'yi tercih eder; ancak websocket.org "WebSockets and AI" rehberi **tool call, interrupt ve çok-turlu agent** için WS'ın geri geldiğini yazar. CoreMusic karşılığı: **senkron yayın** (konum/duraklat) SSE'ye yatar, **komut + chat** (kick/mute/msg) WS'a — ADR-029 `:232`'nin "SSE tek başına yetersiz, fallback" hükmü bu araştırma ile **doğrulandı**. **(4)** Maliyet tarafında Ably/Cloudflare karşılaştırma sayfaları, BlazingCDN, Vask ("WebSockets on Cloudflare 2026"), BuildMVPFast (Pusher vs Ably), PkgPulse (Socket.io vs Ably vs Pusher) aynı resmi veriyor: **eşzamanlı bağlantı ve mesaj hacmi faturalanır**; "ücretsiz tier" 100-500 bağlantıda biter. CoreMusic'in kendi sunucusunda kuracağı WS + Redis pub/sub modeli harici fatura üretmez (ADR-029 §1.3-1 deseni) ama **Redis adapter PLANNED** olduğundan bugün **kurulum yapılmamıştır** → red, "ucuz diye REST-only" gerekçesiyle **savunulamaz**, "maliyet harici servise değil, kendi altyapımıza" olarak savunulur. **(5)** Proxy/üretim kırılganlığı: Nginx SSE yapılandırması (OneUptime), WebSocket + nginx (dev.to, Stack Harbor, websocket.org), nginx topluluk başlıkları (uzun ömürlü bağlantı dağılımı, `proxy_read_timeout` idle-timer tartışması, "SSE with nginx holds client connection") ve CopilotKit issue #6980 → **yeniden bağlanma ve idle-timeout her iki protokolün de ortak üretim riskidir**; SSE otomatik reconnect getirir, WS'te ping/pong elle yazılır (ADR-029 heartbeat'i bunun içindir). |
| Web Search **Paragraf Veri Uzun** | 5 sorgu: **(1)** vickybytes.com websockets-vs-sse-vs-long-polling · pristren.com (maliyet/frekans) · interviewnoodle.com (2026: "SSE daha fazla problemi çözer") · hinterbuild.com 2026 guide · designgurus.substack.com "Stop Defaulting to WebSockets" · medium.com "brutally honest comparison 2026" · medium.com FastAPI real-time 2026 · systemdr.systemdrd.com · websocket.org/comparisons · streamkap.com streaming patterns · linkedin (Ashish P.S.) — **11**. **(2)** developer.chrome.com/blog/removing-push · en.wikipedia.org HTTP/2_Server_Push · flaviocopes.com http2-http3 · oneuptime.com 2026-01-25 http2-server-push · zhul.in 2025-11-05 · testmuai.com http-2 browser support · navanathjadhav.medium.com · community.cloudflare.com "HTTP/2 Push is deprecated" — **8**. **(3)** datasofttechnologies.com (SSE AI 2026) · medium.com coders.stop 2026 · laramateo.com SSE/WS/WebTransport 2026 · blog.codercops.com 2026 · nimbleway.com 2026 guide · getstream.io websocket-sse · vercel.com websocket-vs-server-sent-events · tianpan.co (backpressure/proxy) · websocket.org/guides/websockets-and-ai (ters kanıt) · reddit.com/r/dotnet agent SSE — **10**. **(4)** developers.cloudflare.com durable-objects pricing · developers.cloudflare.com workers pricing · ably.com compare cloudflare-durable-objects-vs-pusher · ably.com compare ably-vs-cloudflare-durable-objects · ably.com compare ably-vs-pusher pricing · blog.blazingcdn.com · vask.dev cloudflare-websockets 2026 · buildmvpfast.com pusher-vs-ably · pkgpulse.com best-realtime-2026 — **9**. **(5)** oneuptime.com 2025-12-16 SSE-nginx · dev.to nginx websocket connection issues · community.nginx.org (9000 bağlantı dağılımı) · community.nginx.org SSE holds connection · reddit.com/r/nginx proxy_read_timeout idle-timer · github.com/CopilotKit/CopilotKit issues/6980 (sessiz SSE kapanması) · websocket.org guides nginx · stackharbor.com nginx websocket · medium.com FastAPI proxy timeouts — **9**. **Toplam ~47 benzersiz kaynak** (başlık/özet düzeyi; sayfa-içi derin tur yapılmadı — §5.1/6). |
| Web Search **Sonucu** | **(1) Red destekleniyor — ama gerekçe netleştirilmeli:** karar matrisi "çift yönlü → WS" der; ADR-029'un ihtiyacı (play/pause/kick/mute + chat) **çift yönlü** → REST-only/polling ret'i **doğru** (kaynak 11). **(2) Red kesinleşiyor:** HTTP/2/3 server push tarayıcıda **ölü** → "REST + push" hayali yok; sunucu-inisiyatifi ya WS ya SSE ya polling (kaynak 8). **(3) Dürüst gerilim — SSE trendi:** tek yönlü akışta SSE 2026'da **varsayılan** hâline geliyor (kaynak 10) → "REST-only" reddi **SSE'yi reddetmez**; reddettiği şey **"push kanalı olmasın, hep istemci sorsun"** modelidir. ADR-029'da SSE'nin **fallback** konumu bugün de doğru; **chat/komut** için SSE tek başına yetersiz (kaynak 3 + ADR-029 `:232`). (4) **Maliyet/ölçek:** harici WS servisleri eşzamanlı bağlantıya göre faturalanır (100-500 ücretsiz eşik) → kendi sunucusu + Redis pub/sub seçimi maliyeti düşürür ama **Redis adapter PLANNED** (ADR-007) → bugün ölçek maliyeti **kuruş değil, kurulum işçiliği** olarak durur (kaynak 9). **(5) Proxy/çökme riski her iki tarafta var:** SSE buffering/idle-timeout, WS proxy_read_timeout + ping/pong → **yeniden bağlanma tasarımı şart** (kaynak 9). **İtiraz/karşıt bulgu (dürüst):** (i) "WebSocket gerekli" ifadesi **tek başına fazla geniş** — asıl gerekli olan **sunucu-inisiyatifiyle çift yönlü kanaldır**; yalnız yayın yapsaydı SSE yeterli olurdu (§2.1/3); (ii) **"Suna"** başlığı için kaynak **bulunamadı** (UNKNOWN — §7.1/7); (iii) sayfa-içi derin tur yapılmadı → RFC 6455 handshake ve Cloudflare fiyatlama satır içi doğrulanmadı (§5.1/6). |
| Web Search **Alınan Karar** | **R-005 RED (REST-Only) YÜRÜRLÜKTE KALIR.** (a) **CoreMusic gerçek zamanlı yüzeyinde "yalnız istek-bazlı HTTP / long-polling" tek taşıma olarak KABUL EDİLMEZ** — dinleme odası senkronu, anlık komut (play/pause/kick/mute) ve sohbet akışı için **sunucu-inisiyatifiyle çift yönlü kanal** (WebSocket) + dağıtım katmanı (Redis pub/sub) gereklidir; bunu ADR-029 `:18/:179-180/:216-218` kilitler ve `:233` REST-only'yi açıkça ret eder. (b) **HTTP/2-3 server push argümanı kullanılmaz** (tarayıcıda ölü — §1.3-2); **SSE reddedilmez**: ADR-029 fallback zincirinde (WS engelliyse tek yönlü senkron + ayrı HTTP POST komutu) **doğru konumdadır**; "REST-only"nın bugünkü karşılığı yalnız **"push kanalı olmasın, istemci sorgulayın"** modelidir ve o model reddedilmiştir. (c) **Maliyet gerekçesi harici servise dayandırılmaz** (§1.3-4): kendi sunucusu + Redis pub/sub; Redis adapter (ADR-007) PLANNED olduğu için red'in dayanağı "maliyet" değil, **ADR-029'un taşıma kararı + çift yönlü ihtiyaç**dır. (d) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; "SSE popüler" veya "WS pahalı" argümanları tek başına koşul sayılmaz. |
| Web Search **Sonuç** | **5/5 araştırmada red desteklendi** (taşıma matrisi + push ölümü + SSE/AI trendi + maliyet + proxy); **1 ters/iki yönlü kanıt dürüstçe yazıldı**: SSE tek yönlüde 2026'nın varsayılanı ve "birçok uygulama WS'a ihtiyaç duymaz" — red buna göre **"SSE'yi değil, istek-bazlı-only modeli reddet"** olarak yazıldı; ayrıca websocket.org "WS AI'a geri dönüyor" notu chat/komut tarafını destekler. **Üç açık işaretlendi:** (i) sayfa-içi derin tur yok (§5.1/6) · (ii) "Suna" kaynağı bulunamadı (UNKNOWN — §7.1/7) · (iii) maliyet rakamları **vendor fiyat sayfası** düzeyi, CoreMusic'e uyarlanmış ölçek ölçümü **değil** (§4.3/2). **Kaynak sayısı: 5 sorgu; §1.3'te adı geçen benzersiz kaynak ~47.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-029 accepted (taşıma kararı) | `:18` teknoloji = **WebSocket (RFC 6455) + Redis pub/sub**, SSE **fallback**, polling **son çare**; `:179-180` "WS bağlantı, Redis dağıtım" ikilisi; `:216-218` fallback zinciri bağlayıcı → REST-only bu kararla **doğrudan çakışır** |
| ADR-029 §3 alternatif 2 (`:233`) | "Long polling / REST-only (mevcut HTTP)" satırı: "50 kişilik odada her poll isteği sunucuya biner → ölçek/tecrübe kötü; literatür polling'i 'son çare' sayar → yalnız 3. kademe fallback" — **bu red'in en somut gerekçe satırıdır** |
| ADR-020 accepted (API yüzeyi) | REST API yüzeyi (auth, `api_keys`, rate limit, `/api/v1` versiyonlama) **korunur** — bu red REST'i **API taşıma** olarak reddetmez, yalnız **gerçek zamanlı tek taşıma** olarak reddeder |
| Kod yüzeyi gerçeği | Üretimde WS/SSE/polling **0** (§1.1/6) → red bir "kaldırma" değil, **girişi engelleme** kararıdır; geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| ADR-007 (Redis adapter PLANNED) | Redis adapter **diskte yok** (`ApcuAdapter`/`MemoryAdapter`/`PageCacheAdapter`) → ADR-029'un dağıtım katmanı henüz **kurulmadı**; red bugün "REST-only mi WS mi" sorusunu kesin biçimde **WS** diye yanıtlar ama **kurulum durumu PLANNED** |
| ADR-011 / ADR-013 | Oturum rotasyonu (1800 sn) ve rate limit (429/`Retry-After`) uzun-ömürlü kanal üzerinde **yeniden doğrulama** ister; polling modeli bu ikisini aynen çalıştırır (ADR-029 `:233` artısı) — bu, red'i **zayıflatmaz**, yalnız fallback'i kıymetli kılar |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; **"SSE yeterli" tezi chat/komut için REDDEDİLDİ** (ters/iki-yönlü kanıt) — red gerekçesi buna göre yazıldı (§2.1/3) |

---

## 2. Karar (Decision)

**R-005 REDDEDİLMİŞTİR: CoreMusic gerçek zamanlı katmanında "REST-Only / yalnız istek-bazlı HTTP (long-polling) tek taşıma" modeli KABUL EDİLMEZ.** Karar `index.md:130`'da bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-029'un taşıma kararının (`:18` WS + Redis pub/sub) + fallback zincirinin (`:216-218`) + alternatif-ret satırının (`:233`) **gerçek-zamanlı ayağıdır** ve 2026-10-02 web araştırması (§1.3 — 5 sorgu, ~47 kaynak) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — "WebSocket sihirli kelime" olduğu için değil, **sunucu-inisiyatifiyle çift yönlü kanal gereksinimi** (oda komutu + sohbet + anlık senkron) olduğu için; HTTP/2-3 server push'un tarayıcıda ölmüş olması da "REST + push" hayalini kapatır.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-029 `:18` taşıma kararını WS + Redis pub/sub olarak kilitler; `:233` "REST-only/long polling" alternatifini **açıkça ret eder**; `:64/:356` bu red'i dizin slug'ıyla sayar; `index.md:130` gerekçesi "WebSocket gerekli" — ayrı bir karar değil, **aynı ilkenin red-kayıt ayağı**.
2. **İhtiyaç kanıtı uç birimde ölçülmüş, modelde yazılmış (kanıtlı):** ADR-029 kapsamı (a)-(g) — senkron oynatma, host komutları (play/pause/kick/mute), sohbet akışı, davet/üyelik, çevrimiçi durum — **hepsi çift yönlü ve olay bazlı**; ADR-029 `:233` "50 kişilik odada her poll isteği sunucuya biner" der (istek fırtınası). Kod tarafında taşıma **0** (§1.1/6) → red bugün "hiç kurulmamış" bir modeli engeller.
3. **SSE gerilimi dürüstçe çözüldü (kanıtlı + ters kanıt):** §1.3-3'te SSE'nin 2026'da tek yönlü varsayılan hâline geldiği **kabul edildi**; buna rağmen red **SSE'yi hedef almaz** — çünkü (i) komut/chat kanalı SSE ile değil ayrı HTTP POST ile taşınır ki bu **iki taşıma modeli** demektir (ADR-029 `:232`: "SSE senkron yayın için doğru, etkileşimli oda + sohbet için tek başına yetersiz"), (ii) ADR-029 SSE'yi zaten **fallback** konumuna koymuştur. Red'in hedefi "push kanalı olmasın, istemci sorgulayın" modelidir.
4. **Alternatifin kapanması (kanıtlı):** tarayıcıda HTTP/2-3 server push **ölü** (§1.3-2, 8 kaynak: Chrome 106 removal, Wikipedia, Cloudflare "no longer used") → "REST-only + HTTP push" üçüncü bir yol olarak masada değil.
5. **Maliyet/ölçek argümanı tersine çevrildi (dürüst):** "WS pahalı" itirazı **harici servis fiyatlarına** dayanır (Pusher/Ably/Cloudflare DO — §1.3-4); CoreMusic'in seçimi **kendi sunucusunda WS + Redis pub/sub** olduğundan red "ucuzluk" ile değil, **ADR-029 ile** savunulur; Redis adapter'ın PLANNED olması red'i değil, **kurulum işini** işaretler (ADR-007).

### 2.2 Teknik Detaylar

- **Reddedilen yüzey (REST-only / istek-bazlı tek taşıma):** oda durumu/senkron için **periyodik GET** (`/api/v1/rooms/{id}/state` poll'i) · olayları yalnız istemci sorgulayarak aldığı her tasarım (chat, presence, komut onayı) · `text/event-stream` **üretmeyen** sunucu tarafı · uzun-polling loop'ları (ADR-029 `:233` ret satırı) · "HTTP/2 push ile bildirim" argümanı (§1.3-2 — tarayıcıda ölü).
- **İzinli yüzey (yerini alan uygulama):** (a) **WebSocket (RFC 6455/8441)** birincil taşıma — `room.sync/join/leave`, `player.command`, `chat.msg` (ADR-029 `:216`); (b) **SSE + HTTP POST** fallback — WS engelli ağlarda tek yönlü senkron, komutlar POST (ADR-029 `:217`); (c) **kısa polling** son çare — oran + jitter dahilinde (ADR-029 `:218`, ADR-013); (d) **Redis pub/sub** çok-FPM/process dağıtım (ADR-029 `:179`; ADR-007 adapter **PLANNED**); (e) normal **REST API** yüzeyi (auth/CRUD/versiyonlama) **ayręt durur** — ADR-020 kapsamı; bu red REST'i API taşıma olarak **yasaklamaz**.
- **Kod yüzeyi ölçümü (2026-10-02):** `WebSocket|Ratchet|Swoole|pusher|socket.io|EventSource` → üretim dizinlerinde **0** (yalnız `tests/mocks/player-api.mock.js` EventSource mock'u, çağıran 0); `composer.json` ×5 → WS/SSE paketi **0**; `.ai/architecture/**` = **112 isabet / 21 dosya** (**PLANNED spec**) → sürüm/not **yok** çünkü taşıma hiç kurulmamış.
- **Uzun-ömürlü bağlantı hijyeni (önceden yazılmış kural):** WS heartbeat/ping-pong + reconnect (ADR-029 `:117-140` spec'i — sayılar **ölçülmemiş**, `⚠️ VERIFICATION REQUIRED`), SSE'de otomatik reconnect + `Last-Event-ID` (§1.3-5), her ikisinde de proxy timeout yapılandırması (§1.3-5).

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **ADR-029'un taşıma kararı (`:18/:216-218/:233`) yeni bir ADR ile değiştirilirse** (mevcut ADR metinleri düzenlenmez — yeni ADR `superseded by` ile bağlar); (2) **ölçülmüş bir karşı-kanıt** Backend Architect + QA raporuyla belgelenirse — oda senkronu/sohbet için **SSE + HTTP POST** (veya polling) kombinasyonunun, çift yönlü kanal olmadan **ölçülebilir biçimde** yeterli kaldığı **sayılarla** yazılmalı (metrik: oda başına mesaj gecikmesi p95, oda başına istek/sn, bağlantı kopyası, 50 kişilik oda senaryosunda sunucu yükü) — ölçüm yoksa "SSE yeterli" iddiası kurulamaz; bu durumda önce **ADR-029 fallback zinciri** (zaten var) gözden geçirilir, "REST-only" **son çare** olarak yeni ADR'de tartılır; (3) **Redis pub/sub (ADR-007 adapter) hiç kurulmaz** ve gerçek zamanlı özellik tek düğüm / SSE fallback ile taşınırsa → red **SSE-only revizyonu** için debate ile yeniden açılır (bu durumda "REST-only" ifadesi "REST + SSE-only" olarak daraltılır); (4) **debate tamamlanıp red'i kuran koşullar değişirse** (§5.3) yeni debate + yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ADR-029 diskte, yürürlükte), 2 = ÖLÇÜLMEDİ (karşı-kanıt 0 — §1.1/6), 3 = SAĞLANMADI (Redis adapter PLANNED), 4 = debate TAMAMLANDI (19/1/0 RED DOĞRULANDI — §7) ama red'i kuran koşullar DEĞİŞMEDİ → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Long polling / REST-only (istemci sorgular)** | 0 yeni altyapı; ADR-013 rate limit aynen çalışır; proxy dostu; en az karmaşıklık | Her oda durumu için istek fırtınası; poll aralığı kadar gecikme; çift yönlü komut yok; bağlantı başına maliyet yüksek | Dizin `:130` gerekçesi (**WebSocket gerekli**) + ADR-029 `:233` açık ret ("50 kişilik odada her poll isteği sunucuya biner; literatür polling'i 'son çare' sayar") — red'in **doğrudan hedefi** |
| 2 | **Yalnız SSE (tek yönlü yayın)** | Basit; düz HTTP; otomatik reconnect; sticky session yok; proxy dostu; 2026 trendi (§1.3-3) | Komutlar (play/pause/kick/chat) ayrı POST kanalı ister → iki taşıma modeli; chat etkileşimi dağınık; oda içi anlık his zayıf | Senkron **yayın** için doğru ama **etkileşimli oda + sohbet** için tek başına yetersiz (ADR-029 `:232`) → **fallback** olarak korundu (§2.2/b); bu red SSE'yi **reddetmez** |
| 3 | **Harici WebSocket servisi (Pusher/Ably/Cloudflare DO)** | Sunucu işletimi yok; hızlı başlangıç; hazır ölçek | Eşzamanlı bağlantı/mesaj **faturalı** (100-500 ücretsiz eşik — §1.3-4); veri üçüncü taraf; vendor lock-in | CoreMusic zaten **kendi sunucusu + Redis pub/sub** modelini seçti (ADR-029 `:18`); harici fatura + veri sahipliği reddi **aynı gerekçe ailesi** (veri/altyapı sahipliği) |
| 4 | **PHP'de tek süreçli WS sunucusu (Ratchet/Swoole/FrankenPHP)** | Tek dille tam-stack | Mevcut FPM istek/yanıt modeliyle uyuşmaz; uzun süreç/opsiyonel runtime; `composer.json` ×5'te **0** bağımlılık (§1.1/6) | Bu red **runtime seçmez** — ADR-029 `:69` taşıma kararını sabitler, uygulayıcı runtime'ı §5.1/2'ye bırakır (`⚠️ VERIFICATION REQUIRED`); **taşıma kararı ayrı, runtime kararı ayrı** |
| 5 | **WebSocket + Redis pub/sub + SSE fallback (çok katmanlı)** | Çift yönlü kanal + dağıtım + proxy-engeli aşımı + son çare polling; literatür standardı (§1.3-1) | İki+ taşıma modeli bakım maliyeti; Redis bağımlılığı (PLANNED); bağlantı yönetimi/heartbeat işçiliği | **Reddedilmedi — bu, yerini alan yaklaşımdır** (ADR-029, §2.2); bedeli §4.2/§4.3'te yazılı, örtbas edilmedi |

*(Kabul edilen uygulama alternatifi — WS + Redis pub/sub + SSE fallback — §3'te "reddedilmedi" olarak ayrılmadı; o, ADR-029 kapsamındaki **yerini alan** yaklaşımdır ve bu red'in gerekçe kaynağıdır.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Gerçek zamanlı taşıma kararı tek yerde kilitli:** `grep R-005` → bu dosya; ADR-029 `:64/:233/:356` + `index.md:130` ile gerekçe ailesi toplandı; "REST-only neden yok" sorusunun yanıtı vault'ta yazılı.
- **Red 2026 verisiyle yeniden sınandı:** 5 sorgu / ~47 kaynak — taşınma matrisi + push ölümü + maliyet + proxy lehine çalıştı; **iki-yönlü kanıt (SSE trendi)** dürüstçe yazıldı ama red'i değiştirmedi (§1.3-3).
- **Karar ile uygulama ayrımı netleşti:** REST API yüzeyi (ADR-020) **korunur**; yalnız "gerçek zamanlı tek taşıma = istek-bazlı" modeli reddedildi → Backend ekibi REST'e devam eder, gerçek zamanlı özellik **ADR-029'a** bağlanır.
- **Geri dönüş temiz:** taşıma hiç kurulmadığı için `git revert` edilecek değişiklik **0** (§5.2).
- **Dizin satırı artık kaynağa sahip:** `index.md:130` "no source" iddiası fiilen geçersiz (bu dosya kaynaktır) → bayrak temizliği §5.1/3'te ertelendi, §7.1/1'de raporlandı.

### 4.2 Olumsuz Sonuçlar

- **Gerçek zamanlı özellik beklemeye alındı:** WS sunucusu, SSE endpoint'i, Redis pub/sub, oda servisleri, sohbet şeması = **hepsi PLANNED** (ADR-029 §1.1) → "odalar çalışıyor" iddiası bugün **yanlıştır**; ürün tarafında oda özelliği **yoktur**.
- **Gerçek zamanlı taşıma sıfırdan kurulacak işçilik:** runtime seçimi (`⚠️ VERIFICATION REQUIRED` — PHP uzun süreç vs ayrı Node servisi), heartbeat/ACK/drift sayıları **ölçülmemiş**, sohbet şeması **yok** (ADR-029 §1.1-`⚠️`).
- **Redis bağımlılığı kapıda:** adapter diskte yok (ADR-007) → çok-FPM yayını **kurulamıyor**; tek düğüm/ fallback'e düşülürse §2.3/3 kapısı devreye girer.
- **Yeniden bağlanma/proxy riski iki protokolde de var:** SSE buffering/idle-timeout + WS `proxy_read_timeout`/ping-pong (§1.3-5) → her ikisi için de operasyonel ayar gerekir (§4.3/4).
- **Dizin/seri tutarsızlığı sürüyor:** `rejected/index.md` boş (12 red'in hiçbiri satırlanmadı) + `index.md:130` dead-link bayrağı duruyor → §7.1/1-2.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Bağlantı yönetimi:** WS/SSE uzun ömürlü bağlantıların kopması/yeniden bağlanma yükü, düğüm başına bağlantı tavanı | 3 (olası) | 4 (yüksek — oda dağılırsa özellik çöker) | Heartbeat/ping-pong + otomatik reconnect (SSE) / yeniden bağlanma (WS); bağlantı tavanı **ölçümle** (ADR-029 §5.1/10); düşerse zincir SSE → polling |
| **Ölçek maliyeti:** oda başına mesaj hacmi arttıkça sunucu CPU/bağlantı maliyeti; harici servis tercih edilirse fatura (§1.3-4) | 3 (olası) | 3 (orta) | Kendi sunucusu + Redis pub/sub (harici fatura yok); oda başı kapasite/heartbeat **sayıyla** kalibre edilir (ADR-029 §5.1/5, /10) — uydurma eşik yazılmaz |
| **Proxy engeli:** nginx/CDN buffering + idle timeout SSE'yi, `proxy_read_timeout` WS'i keser; kurumsal ağ WS'ı engeller | 3 (olası) | 3 (orta) | Fallback zinciri (WS → SSE → polling) **zaten kural** (ADR-029 `:216-218`); proxy ayarları deploy reçetesi (§1.3-5) |
| **Yeniden bağlanma/orijin tutarsızlığı:** WS ve SSE olayları farklı yorumlarsa oda "iki gerçeğe" bölünür | 2 (mümkün) | 3 (orta) | Tek olay sözleşmesi + tek epoch kaynağı (sunucu saati); fallback yalnız **taşıma** değiştirir, **sözdizimi değil** (ADR-029 §4.3/5) |
| **"SSE trendi → WS gereksiz" dış baskısı** (yeni ekip, 2026 anlatısı) | 3 (olası) | 2 (düşük) | Bu dosya + §1.3-3 (SSE yalnız **yayın** için; komut/chat WS) + §2.3/2 ölçüm kapısı — yanıt vault'ta yazılı |
| **Redis adapter'ın hiç kurulmaması** → gerçek zamanlı özellik ya hiç açılmaz ya SSE'ye düşer | 3 (olası) | 3 (orta) | ADR-007 şartı + §2.3/3 kapısı (SSE-only revizyonu debate ile) — **kandırma yok, özellik açılmaz** |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-005-rest-only-api.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-005 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Debate** (3 tur / 20 persona — **✅ TAMAMLANDI**, kayıt §7) + Tech Lead onayı ✅ | MO + Tech Lead | 2026-10-02 |
| 3 | **`index.md:130` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | **Ölçüm adımı (§2.3/2 için kanıt):** oda senkronu + sohbet prototipinde p95 mesaj gecikmesi, oda başına istek/sn, 50 kişilik oda senaryosunda sunucu yükü — **SSE+POST yeterliliği bu ölçümle** tartılır; sayı yoksa red değişmez | Backend Architect + QA Engineer | ADR-029 §5.1/10 ile birlikte |
| 5 | Periyodik denetim: üretim dizinlerinde `WebSocket\|Ratchet\|Swoole\|pusher\|socket.io\|new EventSource` = **0** korunur (gerçek zamanlı taşıma **yalnız ADR-029 üzerinden** girer) + `composer.json` ×5 WS/SSE paketi = 0 | Backend Architect + DevOps Engineer | her sprint |
| 6 | Sayfa-içi derin doğrulama turu: RFC 6455 handshake ayrıntıları + Cloudflare/Pusher fiyat satırları (§1.3 başlık/özet düzeyi kaldı) | Researcher | üretim öncesi |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (WS/SSE/polling hiç kurulmadı → `git revert` edilecek değişiklik **0**; §1.1/6). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ADR-029 taşıma kararı değişirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) ölçülmüş karşı-kanıt (SSE+POST yeterlilik metrikleri) → §2.3/2 kapısı; (3) Redis adapter hiç kurulmazsa → §2.3/3 (SSE-only revizyonu debate); (4) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI** (2026-10-02 — 3 tur / 20 persona, R-001…R-004 formatı). **Sonuç: 19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** (Tur 3 oyu — tam kayıt §7) · durum `rejected` (kullanıcı onaylı red) olarak korunur.

**Planlanan akış:**

- **Tur 1 (20 persona — bulgu):** üretim kodu WS/SSE/polling **0** + EventSource **yalnız test mock** (§1.1/6-7) · red kaynağı `index.md:130` (dead-link **dokunulmadı** → §5.1/3) · yerini alan [[../accepted/ADR-029-listening-rooms-social]] + [[../accepted/ADR-020-api-public-security]] `Test-Path` doğrulandı · gerekçe ailesi ADR-029 `:64/:233/:356` · 5 sorgu / ~47 kaynak · `rejected/index.md` boş → dokunulmadı.
- **Tur 2 (itiraz → çözüm → şart):** (i) "SSE 2026'da yeter, WS gereksiz" → komut/chat çift yönlü ihtiyacı + iki-taşıma maliyeti → **ölçüm kapısı (§2.3/2)**; (ii) "Redis yok, o zaman REST/SSE" → adapter PLANNED gerçeği → **§2.3/3 SSE-only revizyon kapısı**; (iii) "proxy engeller" → fallback zinciri zaten kural → **operasyonel ayar adımı (§5.1/6 / §4.3/3)**.
- **Tur 3 (oy):** sonuç + bağlayıcı şartlar buraya ve §7'ye yazılacak.

**Planlanan bağlayıcı şartlar (Tur 2 çıktısı olacak — henüz kesinleşmedi):**

1. **Şart 1 — Ölçüm kapısı:** "SSE/polling yeterli" iddiası yalnız §2.3/2 metrikleriyle (p95 gecikme, istek/sn, 50 kişilik oda yükü) kurulabilir; ölçüm yoksa red değişmez.
2. **Şart 2 — Taşıma sözleşme bütünlüğü:** WS/SSE/polling üçü de **aynı olay sözdizimi + aynı sunucu epoch'unu** paylaşır; fallback yalnız taşıma değiştirir (ADR-029 §4.3/5).
3. **Şart 3 — Redis/gerçek-zamanlı ADR kapısı:** Redis adapter (ADR-007) kurulmadan gerçek zamanlı özellik **açılmaz**; taşıma/runtime değişikliği talebi bu dosya değil **yeni ADR** ile açılır.

**Kesinleşen bağlayıcı şartlar (debate Tur 2 itiraz→çözüm + Tur 3 oyu — 2026-10-02; yukarıdaki plan bloğunu GÜNCELLER):**

1. **Şart 1 — Kapsam sabitleme (anti-SSE değil):** red kapsamı **"REST-only / yalnız istek-bazlı HTTP tek taşıma"** olarak sabitlenir; **SSE fallback** (ADR-029 `:217`) açıkça **reddin dışındadır** — red "SSE'yi değil, istek-bazlı-only modeli" reddeder.
2. **Şart 2 — "Suna" → ⚠️ VERIFICATION REQUIRED:** görevde adı geçen **"Suna"** için 5 sorguda da kaynak bulunamadı (UNKNOWN — §7.1/7); anlamı bilinmiyorsa §1.3'ten **çıkarılır**, biliniyorsa **açıklanır** — uydurulmaz.
3. **Şart 3 — Fiyat kaynağı:** §1.3-4 fiyat rakamları (Pusher/Ably/Cloudflare) **vendor sayfası** düzeyindedir → **bağımsız fiyat kaynağı** eklenir ya da `⚠️ VERIFICATION REQUIRED` **kalıcı** işaret olarak kalır (CoreMusic'e uyarlanmış ölçek ölçümü yoksa sayı savunulmaz).

> **Debate sonucu:** ✅ **RED DOĞRULANDI** (3 tur / 20 persona — 19 kabul / 1 çekimser / 0 red) · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · kesinleşen şartlar §5.3 (yukarıda) · tam kayıt §7.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:130`, slug + "WebSocket gerekli" + dead-link bayrağı §5.1/3) |
| [[../accepted/ADR-029-listening-rooms-social]] | **Yerini alan (birincil):** WebSocket + Redis pub/sub + SSE fallback + sunucu saati; `:64/:356` bu red'i sayar, `:233` REST-only'yi ret eder, `:216-218` fallback zinciri |
| [[../accepted/ADR-020-api-public-security]] | **Yerini alan (ikincil):** REST API yüzeyi — auth (`api_keys`), rate limit, `/api/v1` versiyonlama; bu red REST'i **API taşıma** olarak yasaklamaz |
| [[../accepted/ADR-007-cache-namespace]] | Redis adapter **YOK** (Apcu/Memory/PageCache) → ADR-029 dağıtım katmanı PLANNED; §2.3/3 kapısının dayanağı |
| [[../accepted/ADR-013-rate-limiting-apcu]] | Polling/istek fırtınasının çalıştığı oran sınırı (fail-open, 429/`Retry-After`) — fallback'in koruyucu katmanı |
| [[../accepted/ADR-011-session-management]] | Oturum rotasyonu (1800 sn) → uzun-ömürlü WS bağlantısında yeniden doğrulama kuralı |
| [[../accepted/ADR-018-footer-player-vaporwave]] | Oynatıcı durum makinesi (`PlayerController.js:12-13`) — senkron oynatmanın **IMPLEMENTED** hook'u |
| [[../accepted/ADR-016-url-normalization]] | `/api/v1/*` yolu — REST API yüzeyinin URL disiplini (red kapsamı dışı) |
| [[../accepted/ADR-032-ipc-contract-versioning]] | **Çapraz referans:** sözleşme versiyonlama disiplini — gerçek zamanlı olay sözdizimi (`room.sync` vb.) de versiyonlanabilir sözleşmedir (IPC yüzeyi ≠ HTTP yüzeyi; **kıyaslama disiplini**, taşımada doğrudan bağlayıcılığı **UNKNOWN**) |
| [[../accepted/ADR-030-ai-strategy-core]] | **Çapraz referans:** öneri motorunda "Real-time Learning" spec'i (`k4-yapay-zeka/recommendation-engine.md:279`) — **içerik/Model "real-time"**, taşımaya dair ADR-030'da **taşıma kararı YOK** (`⚠️ VERIFICATION REQUIRED`) |
| [[../../architecture/k14-ag/websocket-realtime]] | WS spec'i — `:16` RFC 6455/8441 ✅, `:111` `room.sync` ✅, `:117-140` heartbeat/drift sayıları (**ölçülmemiş**) → **PLANNED**, bu ADR'nin taşıma dayanağı |
| [[../../architecture/k10-uygulama/home-panel]] | Oda paneli spec'i — `:182` "Her oda ve cihaz durumu WebSocket üzerinden real-time güncellenir" (**PLANNED**) |
| [[../../VISION]] | Vizyon — `:96` "WebRTC/WebSocket multi-room audio", `:209` "WebSocket desteği" |
| [[../../brain]] | Mimari karar özeti (gerçek zamanlı / API satırı) |
| [[../accepted/ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1, §1.3, §2.2, §2.3) |
| [[../accepted/ADR-024-ecosystem-modular-docs]] | Wiki-link disk kanıtı kuralı |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı |
| [[R-002-mongodb-document-store]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (kanıt tablosu/dürüst etiket deseni) |
| [[R-003-jquery-ui-framework]] | Seri kardeşi — format referansı (§1.3 9 alan, §7.1 rapor deseni) |
| [[R-004-webpack-bundle-system]] | Seri kardeşi — format referansı (§7.1 rapor deseni + satır-no düzeltmesi usulü) |
| Dizin satırı | `index.md:130` — slug otoritesi + dead-link bayrağı (§5.1/3) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ **TAMAMLANDI** (3 bağlayıcı şart **kesinleşti**: 1 kapsam sabitleme (anti-SSE değil) · 2 "Suna" ⚠️ VERIFICATION REQUIRED · 3 fiyat kaynağı) + debate kaydı **§7** (19/1/0 RED DOĞRULANDI) |
| Düz metin | Eski seri R-006…R-012 (`rejected/index.md` tablosu boş → §7.1/2) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-02 | ✅ |
| Tech Lead | — | 2026-10-02 | ✅ |
| Arch Lead | — | — | ⏳ |

**Debate kaydı:** ✅ **TAMAMLANDI** — 2026-10-02 · **3 tur / 20 persona** · **Sonuç: 19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** · **Tech Lead:** ✅ · **Arch Lead:** ⏳ (bu debate işleminde onay vermedi).

| Tur | Katılım | Çıktı |
|-----|---------|-------|
| **1 — bulgu** | 20 persona (17 kabul/neutral · 3 uyarı: Critic kapsam + "Suna" şartı, Data fiyat işareti) | R-005 grep **4 isabet** (`index.md:130` slug · ADR-029 `:64` · ADR-029 `:356` · R-004 `:207`), kodda `R-005` = **0** · yerini alanlar `Test-Path` **True** ([[../accepted/ADR-029-listening-rooms-social]] · [[../accepted/ADR-020-api-public-security]]) · üretim WS/SSE yüzeyi **0** (shared 0 · home 0 · .github 0 · composer Ratchet/Swoole/Pusher 0), tek iz `tests/mocks/player-api.mock.js` EventSource mock **4 satır / çağıran 0** · `.ai/architecture/**` **112 isabet / 21 dosya** → PLANNED · ADR-029 `:127` ≠ disk `:130` satır sapması §7.1/6'ya raporlandı · 5 sorgu / ~47 benzersiz kaynak (karar matrisi 11 · Chrome push removal 8 · SSE/AI trendi 10 · fiyat 9 · nginx/proxy 9) · **5/5 red destekledi** · ters kanıt dürüst: SSE tek yönlüde 2026 varsayılanı → red "SSE'yi değil, istek-bazlı-only'i" reddeder · 3 açık işaretli (ders turu yok · "Suna" UNKNOWN · fiyat vendor-süzme) · `index.md:130` dead-link **dokunulmadı**, `rejected/index.md` boş **dokunulmadı** · **21/21 wiki-link diskte** |
| **2 — itiraz→çözüm** | 3 itiraz → 3 şart | (1) red kapsamı "REST-only istek-bazlı tektir" olarak sabitlendi, SSE fallback açıkça reddin dışında → **Şart 1** · (2) "Suna" UNKNOWN → V.R. düzeltmesi (anlamı bilinmiyorsa sil ya da açıkla) → **Şart 2** · (3) fiyat vendor-süzme → bağımsız fiyat kaynağı ya da ⚠️ kalıcı → **Şart 3** |
| **3 — oy** | 19 kabul · 1 çekimser · 0 red | **RED DOĞRULANDI** — 3 bağlayıcı şart §5.3'e bağlandı |

**Bağlayıcı şartlar (Tur 2/3):** (1) kapsam sabitleme (anti-SSE değil) · (2) "Suna" ⚠️ VERIFICATION REQUIRED · (3) fiyat kaynağı (bağımsız kaynak ya da kalıcı ⚠️) — tam metin §5.3.

> **Sonuç:** R-005 **REDDEDİLDİ (kullanıcı onaylı) — debate ✅ TAMAMLANDI: RED DOĞRULANDI (19/1/0)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · kesinleşen bağlayıcı şartlar §5.3.

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:130` `<!-- dead-link: R-005-rest-only-api no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (757 bayt, v1.0.1, `total: 12`) ama tablo **BOŞ** (başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only.
3. **Slug hizası:** dosya adı `R-005-rest-only-api` = `index.md:130` slug **birebir** ✅ (R-001…R-004 dersi: tahmin yok, `rejected/` glob'u ile doğrulandı — bu işlem öncesi `R-005*` = 0 dosya).
4. **Debate:** ✅ **TAMAMLANDI (2026-10-02, 3 tur / 20 persona — 19/1/0 RED DOĞRULANDI)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳ (bu debate işleminde onay vermedi) · **status: rejected** (kullanıcı onaylı red) · kayıt §7.
5. **Kod yüzeyi kanıtı:** `WebSocket|Ratchet|Swoole|pusher|socket.io|EventSource` → üretim dizinlerinde **0**; tek iz `assets.coremusic.net/tests/mocks/player-api.mock.js` (4 satır, EventSource **mock**, çağıran **0**); `composer.json` ×5 WS/SSE paketi **0**; `.ai/architecture/**` **112 isabet / 21 dosya** = **PLANNED spec** (ADR-029 `:35-40` bulgusu yeniden doğrulandı).
6. **Satır-no düzeltmesi (dürüst):** ADR-029 `:64` ve `:356` dizini **`decisions/index.md:127`** olarak gösterir; **disk ölçümü `index.md:130`**'dur (Select-String). Doğru satır **130**'dur; ADR-029'daki `:127` **eski/sıkışmış referanstır** ve bu işlemde **dokunulmadı** (frozen okunur) → ADR-029 bir sonraki vault revizyonunda düzeltilebilir (rapor-only).
7. **"Suna/SSE trendi" araştırması:** görevde adı geçen **"Suna"** için 5 sorguda da kaynak **bulunamadı** → **UNKNOWN** (uydurulmadı); başlık **"AI-agent SSE streaming trendi"** olarak araştırıldı ve §1.3-3'te işlendi.
8. **Şablon yolu notu:** görev `templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md`'dir (`Test-Path` = **True**, 25.503 bayt) — bu dosya o şablonun §1-§7 iskeletiyle (§1.3 9 alan, §7 Onay) yazıldı.
9. **Kaynak derinliği:** §1.3 ~47 benzersiz kaynak **başlık/özet düzeyinde** derlendi (sayfa-içi tur yok) → §5.1/6'ya bırakıldı; sayısal iddialar (Pusher/Ably/Cloudflare fiyat eşikleri) **vendor sayfası** düzeyindedir, CoreMusic'e uyarlanmış ölçüm **değildir**.

---

*R-005 v1.0.0 | 2026-10-02 | Created — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri)*
*Authority: R-005 Red Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
