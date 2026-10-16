---
title: "K011 · event-akislari — kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: event-akislari
status: active
updated: 2026-10-15
group: mimari
---

# K011-application/event-akislari — Kapsam

**Amaç:** Bu belge `event-akislari` klasörünün sınırını çizer: CoreMusic'te olay
(olay = "bir şey oldu" haberi) üretimi, taşıması ve tüketimi hangi disk yüzeyleridir,
hangileri DEĞİLDİR. PHP PSR-14 tarafı ile JS EventBus/DOM tarafı ayrı omurgalar olarak
tarif edilir; her iddia dosya+satır kanıtlıdır, kanıtsız iddia `⚠️ VERIFICATION
REQUIRED` ile işaretlenir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | ELI15 | mimari | kavram | Olay akışı basit dilde |
| §2 | Sınır — ne DEĞİL | mimari | negatif | Kapsam dışı yüzeyler |
| §3 | İki omurga | mimari | mimari | PHP PSR-14 + JS EventBus/DOM tabloları |
| §4 | Olay adlandırma | mimari | konvansiyon | 12 PHP + 21 JS ad haritası |
| §5 | Üretim→taşıma→tüketim | mimari | akış | Uçtan uca akış tabloları (4 senaryo) |
| §6 | Kardeş klasör farkı | mimari | komşuluk | command-query/usecase/routing ile ayrım |
| §7 | Kapsam dışı | mimari | red | Bu belgeyi aşan konular |
| §8 | Terimler | mimari | dil | Olay sözlüğü (repo-kaynaklı) |
| §9 | Açık sorular | mimari | boşluk | Wiring PLANNED + spec §8 kaydı |
| §10 | SQL olay yüzeyi | mimari | veri | 6 tablo eşlemesi + sınır kararı |
| §11 | JS dizin envanteri | mimari | frontend | 95 dosya, 8 dizin kapsam kararı |
| §12 | Kapsam satırı indeksi | kanit | dosya:satır | 20 dayanak (gerçek satır) |
| §13 | Bağımlılık grafiği | mimari | dışa açılım | üretici→yayıcı→tüketici + 4+4 kaynak |
| §14 | Kapsam KPI'ları | kanit | ölçüm | 12 KPI: 9 ✅ / 3 ⚠️ |

---

## §1 — ELI15 (olay akışı)

Birisi dükkânda "süt bitti" der ve herkes duyar — sütü isteyen markete koşar, aşçı
yemeği değiştirir. Kimse kimseyi tek tek aramaz: **haber yayınlanır, ilgilenen alır.**
Olay akışı (event flow) budur.

CoreMusic'te iki ayrı yayın dünyası var ve ikisi de diskte gerçek:

| Dünya | Kanal | Üreten | Dinleyen | Durum |
|-------|-------|--------|----------|-------|
| **PHP** (sunucu) | `EventDispatcher` (PSR-14) | Domain/Integration event sınıfları (12 adet) | Henüz production'da yok — handler'lar PLANNED | Altyapı ✅ IMPLEMENTED · wiring ⚠️ PLANNED (ADR-086) |
| **JS** (tarayıcı) | `EventBus` (pub/sub) + DOM `CustomEvent` | Manager'lar (Player, Card, Touch, Sidebar, Scale, Router) | 10+ manager + router + modül | ✅ GERÇEK — canlı akışta |

Kritik ayrım: JS tarafı **çalışıyor** (main.js sayfa açılışında EventBus kuruyor ve
tüm manager'lara dağıtıyor); PHP tarafında ise dispatcher + event sınıfları var ama
`Events/` dışından kimse `dispatch()` çağırmıyor — bu ADR-086'da dürüstçe "wiring
PLANNED" olarak kayıtlıdır ve bu klasör de aynı dili kullanır.

### §1.1 — Olay neden normal çağrıdan farklıdır?

| Özellik | Doğrudan çağrı | Olay yayını |
|---------|----------------|-------------|
| Bağlılık | Çağrıran, çağırdığı sınıfı bilir | Üreten, dinleyenleri bilmez (loose coupling) |
| Dinleyici sayısı | Tek hedef | 0..N dinleyici (EventBus.js:57-67 — `Set<Function>`) |
| Hata etkisi | Hata çağrıyı düşürür | JS: dinleyici hatası `try/catch` ile yalıtılır (EventBus.js:61-65); PHP: dispatch çağıranın akışında (handler hatası isteği düşürürse ADR-086 §1.2 kısıtı ihlal — Stoppable desen mevcut) |
| Zamanlama | Senkron | JS EventBus senkron (EventBus.js:57); DOM `dispatchEvent` senkron (MDN claim) — asenkron kuyruk YOK |
| Sıra | Tek hedef | JS: `Set` insertion sırası; PHP: priority `usort` (EventDispatcher.php:109-118) |

---

## §2 — Sınır: bu klasör NE DEĞİL

| # | Bu klasör DEĞİL | Neden / karşılığı nerede |
|---|------------------|---------------------------|
| 1 | HTTP istek/yanıt akışı | `command-query/` (method + veri yönü ekseni) |
| 2 | Kullanıcı senaryosu zinciri | `usecase-akislari/` (sayfa adım adım) |
| 3 | Rota tablosu / matcher | `routing/` |
| 4 | Servis nesnesi envanteri | `application-services/` |
| 5 | Kuyruk altyapısı (RabbitMQ, SQS, outbox) | Repo'da YOK — event'ler süreç-içi (in-process); ADR-086 §1.2 |
| 6 | WebSocket/SSE canlı yayın | Repo'da `EventSource`/`WebSocket` isabeti bu klasörün ölçümü DEĞİL (§7) |
| 7 | CSS event (hover/focus) kataloğu | CSS yalnız olayın **gözlenebilir yüzeyidir** (`[data-*]` attribute → §3.4); pseudo-class kataloglaması `k11-ux/` katmanının işi |
| 8 | Log tablosu şema tasarımı | `.ai/.sql/mysql/` sahibi (Data Engineer) — bu klasör yalnız event akışının SQL'e vurduğu yüzeyi işaretler |

---

## §3 — İki omurga (mimari)

### §3.1 — PHP omurgası (PSR-14 — altyapı IMPLEMENTED, wiring PLANNED)

| # | Bileşen | Dosya | Satır | Rol |
|---|---------|-------|-------|-----|
| 1 | `DomainEventInterface` | `shared/src/Contracts/Events/DomainEventInterface.php` | 17-39 | `eventName()` · `occurredOn()` · `payload()` — 3 zorunlu method |
| 2 | `IntegrationEventInterface` | `shared/src/Contracts/Events/IntegrationEventInterface.php` | 1-46 | `source()` ekler (servis kökeni) |
| 3 | `EventDispatcher` | `shared/src/Events/EventDispatcher.php` | 20-119 | `dispatch(object): object` (28) · `addListener(callable, int $priority=0)` (50) · `removeListener` (64) · `getListeners` (82) · `hasListeners` (101) · `sortListeners` (109) |
| 4 | `StoppableEventTrait` | `shared/src/Events/StoppableEventTrait.php` | 17-36 | `isPropagationStopped()` (24) · `stopPropagation()` (32) |
| 5 | Domain event ×9 | `shared/src/Events/Domain/*Event.php` | — | Kullanıcı/medya/çalma olayları (§4.1) |
| 6 | Integration event ×3 | `shared/src/Events/Integration/*Event.php` | — | Servisler arası olaylar (§4.1) |
| 7 | Bağımlılık | `shared/composer.json` | 18, 21 | `"psr/event-dispatcher": "^1.0"` · `"symfony/event-dispatcher": "^7.0"` |
| 8 | Karar | `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | 9, 16, 34 | "altyapı IMPLEMENTED, wiring PLANNED" · "grep dispatch usage = 0 (Events/ dışı)" |

PHP dispatcher davranışı (EventDispatcher.php kanıtı):

| Davranış | Satır | Not |
|----------|-------|-----|
| Event adı = sınıf adı | 30 | `get_class($event)` — `eventName()` methodu dispatch'te KULLANILMAZ (fark §9.2) |
| Listener yoksa erken dönüş | 32-34 | `return $event` — maliyetsiz |
| Stoppable kontrolü | 37-39 | `isPropagationStopped()` → `break` |
| Priority sıralaması | 113-115 | `usort` — büyük öncelik önce (`$b <=> $a`) |
| Önbellek invalidasyonu | 58, 76 | `unset($this->sortedListeners[$eventName])` |

> **Önemli ölçümlü not:** Dispatcher `get_class($event)` ile anahtarlarken (satır 30),
> event sınıfları `eventName()` döndürüyor (`'music.played'` vb.). Bu iki ad alanı
> **farklıdır**; `addListener('music.played', …)` çağrısı eşleşmez — `addListener('CoreMusic\Events\Domain\MusicPlayedEvent', …)` gerekir.
> Bu, wiring başlarken dikkat edilecek §9.1 boşluğudur.

### §3.2 — JS omurgası (EventBus pub/sub — GERÇEK)

| # | Bileşen | Dosya | Satır | Rol |
|---|---------|-------|-------|-----|
| 1 | `EventBus` sınıfı | `assets.coremusic.net/js/core/EventBus.js` | 8-73 | `#listeners = new Map()` (10) · `on()` (18, unsubscribe döner 23) · `once()` (31) · `off()` (44) · `emit()` (57) · `destroy()` (70) |
| 2 | Enjeksiyon | `assets.coremusic.net/js/main.js` | 71 | `const eventBus = new EventBus();` — TEK instance |
| 3 | App'e verilir | `main.js` | 72 | `new CoreMusicApp({ eventBus })` |
| 4 | Router'a verilir | `main.js` | 84 | `new Router({ ..., eventBus })` |
| 5 | Yayına geçiş | `main.js` | 90 | `eventBus.emit('router:ready', { router })` |
| 6 | Manager enjeksiyonu | `main.js` | 96-139 | DeviceManager(96) · ScaleManager(100) · ThemeManager(106) · ViewModeManager(110) · SidebarManager(115) · PlayerController(123) · WidgetManager(127) · CardManager(131) · ScrollManager(135) · TouchManager(139) — **10 manager, hepsi `eventBus` constructor argumanı** |
| 7 | Hazır bildirimi | `main.js` | 182 | `eventBus.emit('app:ready')` |
| 8 | Global erişim | `main.js` | 186 | `window.CoreMusic.EventBus = eventBus;` (debug/entegrasyon kapısı) |
| 9 | Zorunluluk | `js/core/CoreMusicApp.js` | 63-64 | `if (!eventBus) console.error('[CoreMusicApp] EventBus is critical - halting boot')` — EventBus YOKSA boot durur |
| 10 | Router DOM köprüsü | `js/router/RouterEventManager.js` | 7-30 | `bind()` popstate/click/mouseenter/offline/online/error/unhandledrejection (10-16) · `#addListener` Map kaydı (27-30) · `unbind()` temizliği (19-25) |

### §3.3 — DOM CustomEvent akışı (iki gerçek nokta)

| # | Olay | Üreten dosya | Satır | Dinleyen |
|---|------|--------------|-------|----------|
| 1 | `devicechange` | `js/device-loader.js` | 280 | `js/device-layout-updater.js:514` (`window.addEventListener('devicechange', …)`) |
| 2 | `oauth:notification` | `js/oauth-manager.js` | 287-290 | `js/` genelinde `window` dinleyicisi (kayıtlı 2. `addEventListener` — oauth-manager.js:237 `btn.addEventListener('click', …)` bir UI dinleyicisidir, olay yayını değil) |

Ayrıca saf DOM dinleyicileri (olay **üretmez**, tarayıcı olayını **dinler**):
`main.js:94` `DOMContentLoaded` · `main.js:190` `popstate` · `device-loader.js:395`
`resize` · `device-loader.js:406` `DOMContentLoaded` · `device-layout-updater.js:520`
`DOMContentLoaded` — toplam `addEventListener|dispatchEvent|CustomEvent` isabeti
**10 satır / 4 dosya** (2026-10-15 ölçümü).

### §3.4 — CSS/SQL yüzeyi (olayın iz düşüğü)

| Yüzey | Dosya | Kanıt | Rol |
|-------|-------|-------|-----|
| Attribute → stil | `Css/01_Abstracts/a-color-mode-tokens.css` | `html[data-mode="light"]` satır 143 · `html[data-gender="female"]` 253 · pink mode 303 · `html[data-mode="dark"]` 367/374 | JS olay sonrası attribute yazar (ThemeManager.js:87 `setAttribute('data-gender', …)` · :166 `setAttribute('data-mode', …)`) → CSS cascade tepki verir |
| Attribute → stil (glass) | `a-light-glass-tokens.css` | `[data-gender="female"]` 120 · male 133 · neutral 146 | Aynı köprü |
| Attribute → stil (login) | `a-login-tokens.css` | `[data-gender="female"]` 176 · male 184 · neutral 192 | Aynı köprü |
| Attribute → stil (ölçek) | JS: `ScaleManager.js` 381-383 | `setAttribute('data-scale-tier'/…'data-dpr'/…'data-aspect')` | Cihaz ölçek olayının CSS izi |
| SQL depo — gerçek zamanlı | `.ai/.sql/mysql/coremusic_logs.sql` | `analytics_realtime_events` 109 (`event_type` 111 · index 118-121) | Canlı panel olay kaydı |
| SQL depo — kullanıcı | `coremusic_logs.sql` | `user_events` 433 (`event_type` 436 · `event_value` 437 · `event_data` JSON 438) | Kullanıcı olay kaydı |
| SQL depo — güvenlik | `coremusic_logs.sql` | `log_security` 296 (`event_type` enum 299 — CSRF_VIOLATION, RATE_LIMIT_EXCEEDED, …) | Güvenlik olay enum'u |
| SQL depo — sistem | `coremusic_logs.sql` | `log_system` 322 (`event_type` enum 325 — SERVICE_START, CRON_JOB_*, …) | Sistem olay enum'u |

> **Dürüst sınır:** SQL tablolarına PHP event handler'ından **bugün** yazan kod
> **yoktur** (ADR-086:0 dispatch). Tablolar mevcut; akış PLANNED.

---

## §4 — Olay adlandırma (harita)

### §4.1 — PHP: 12 ad (dosya → eventName satırı)

| # | Ad | Dosya | Satır | Tip |
|---|----|-------|-------|-----|
| 1 | `music.played` | `Events/Domain/MusicPlayedEvent.php` | 39 | domain |
| 2 | `music.added` | `Events/Domain/MusicAddedEvent.php` | 39 | domain |
| 3 | `media.accessed` | `Events/Domain/MediaAccessedEvent.php` | 39 | domain |
| 4 | `playlist.created` | `Events/Domain/PlaylistCreatedEvent.php` | 39 | domain |
| 5 | `user.registered` | `Events/Domain/UserRegisteredEvent.php` | 40 | domain |
| 6 | `user.gender_set` | `Events/Domain/GenderSetEvent.php` | 38 | domain |
| 7 | `user.logged_in` | `Events/Domain/UserLoggedInEvent.php` | 39 | domain |
| 8 | `user.logged_out` | `Events/Domain/UserLoggedOutEvent.php` | 38 | domain |
| 9 | `user.password_reset_requested` | `Events/Domain/PasswordResetRequestedEvent.php` | 39 | domain |
| 10 | `auth.validated` | `Events/Integration/AuthValidatedEvent.php` | 39 | integration |
| 11 | `notification.sent` (+ source `notification.service`) | `Events/Integration/NotificationEvent.php` | 40, 48 | integration |
| 12 | `session.created` (+ source `auth.service`) | `Events/Integration/SessionCreatedEvent.php` | 39, 47 | integration |

Desen: `ozne.eylem` — Türkçe değil, İngilizce fiil; nokta ile ayrık; `user.`/`music.`/
`media.`/`playlist.`/`auth.`/`notification.`/`session.` önekleri (dosya adıyla birebir).

### §4.2 — JS: 21 ad (19 EventBus + 2 DOM)

| # | Grup/Ad | Dosya | Satır | Not |
|---|---------|-------|-------|-----|
| 1 | `player:progress` | `js/features/PlayerController.js` | 87 | `on()` — dinleme |
| 2 | `player:volume` | PlayerController.js | 113, 158 | emit + tekrar emit |
| 3 | `player:pause` | PlayerController.js | 122 | emit |
| 4 | `player:play` | PlayerController.js | 126 | emit |
| 5 | `player:stop` | PlayerController.js | 135 | emit |
| 6 | `player:prev` | PlayerController.js | 138 | emit |
| 7 | `player:next` | PlayerController.js | 139 | emit |
| 8 | `player:shuffle` | PlayerController.js | 143 | emit |
| 9 | `player:repeat` | PlayerController.js | 149 | emit |
| 10 | `sidebar:resize` | `js/managers/SidebarManager.js` | 664 | emit |
| 11 | `sidebar:ready` | SidebarManager.js | 696 | emit |
| 12 | `sidebar:toggle` | SidebarManager.js | 731 | emit |
| 13 | `sidebar:viewchange` | SidebarManager.js | 887 | emit |
| 14 | `sidebar:sort` | SidebarManager.js | 896 | emit |
| 15 | `touch:longpress` | `js/managers/TouchManager.js` | 76 | emit |
| 16 | `touch:swipe` | TouchManager.js | 91 | emit |
| 17 | `card:click` | `js/features/CardManager.js` | 35 | emit |
| 18 | `nav:complete` | `js/features/ScrollManager.js` | 26 | `on()` — dinleme (emit eden: NavigationOrchestrator.js:94, SPARouterAdapter.js:113) |
| 19 | `scale:applied` | `js/managers/ScaleManager.js` | 366 | emit |
| 20 | `devicechange` (DOM) | `js/device-loader.js` | 280 | `window.dispatchEvent(new CustomEvent(...))` |
| 21 | `oauth:notification` (DOM) | `js/oauth-manager.js` | 287 | `new CustomEvent` + 290 `window.dispatchEvent` |

Ek (main.js): `router:ready` (90) · `app:ready` (182 + CoreMusicApp.js:100) — bunlar
21 sayımının **dışında**, boot olaylarıdır (§4.3).

### §4.3 — Boot olayları (2 ek)

| Ad | Üreten | Satır | Dinleyen |
|----|--------|-------|----------|
| `router:ready` | `main.js` | 90 | Router modülü + debug (`window.CoreMusic.EventBus`) |
| `app:ready` | `main.js` 182 · `CoreMusicApp.js` 100 | — | Tüm erken başlatma bağımlıları |

### §4.4 — Adlandırma karşılaştırması

| Kriter | PHP | JS |
|--------|-----|-----|
| Ayırıcı | nokta `.` | iki nokta `:` |
| Büyük harf | yok (snake_case eylem) | yok (kebab-case eylem) |
| Tip öneki | `user.`/`music.`/… | grup öneki `player:`/`sidebar:`/… |
| Kaynak | `eventName()` methodu (magic string, dosya içi) | emit çağrısının içi (magic string, dağınık) |
| Merkezî sabit | **YOK** (12 dosyada tekrar) | **YOK** (dosyalar arası tekrar) |
| DOM ayrımı | — | DOM olaylarında `:` yok (`devicechange`) — Web API adıdır, değiştirilemez |

> **Boşluk:** İki sistemde de ad sabit dosyası YOK (§9.2-§9.3).

---

## §5 — Uçtan uca akışlar (4 senaryo)

### §5.1 — Senaryo A: JS olay akışı (GERÇEK — üretimde çalışır)

| Adım | Nerede | Satır | Ne olur |
|------|--------|-------|---------|
| 1 | `js/main.js` | 71 | `new EventBus()` — tek instance doğar |
| 2 | `js/main.js` | 96-139 | 10 manager constructor'a eventBus alır (bağımlılık enjeksiyonu) |
| 3 | `js/features/PlayerController.js` | 87 | `eventBus.on('player:progress', …)` — dinleyici kaydı |
| 4 | Kullanıcı şarkıya basar | UI | PlayerController iç durumu değiştirir |
| 5 | `PlayerController.js` | 126 | `this.#eventBus.emit('player:play')` — yayın |
| 6 | `EventBus.js` | 57-66 | `#listeners.get('player:play')` kümesindeki her fn çağrılır (try/catch korumalı) |
| 7 | Dinleyici manager | — | UI'ı günceller (progress bar, kart durumu) |

### §5.2 — Senaryo B: DOM CustomEvent akışı (GERÇEK)

| Adım | Nerede | Satır | Ne olur |
|------|--------|-------|---------|
| 1 | `js/device-loader.js` | 280 | `window.dispatchEvent(new CustomEvent('devicechange', { detail: … }))` |
| 2 | Tarayıcı | — | Event `window` hedefine senkron iletilir |
| 3 | `js/device-layout-updater.js` | 514 | `window.addEventListener('devicechange', function (e) { … })` — dinleyici çalışır |
| 4 | Layout updater | — | Cihaz değişimine göre CSS/layout güncellenir |

### §5.3 — Senaryo C: PHP event akışı (ALTYAPI VAR — wiring PLANNED)

| Adım | Nerede | Durum |
|------|--------|-------|
| 1 | Servis kodu bir domain event'i `new` eder (`new MusicPlayedEvent($musicId, $userId, $duration)`) | ⚠️ PLANNED — `Events/` dışında `new …Event(` isbeti 0 (ADR-086:34) |
| 2 | `$dispatcher->dispatch($event)` | ⚠️ PLANNED |
| 3 | `EventDispatcher::dispatch` (EventDispatcher.php:28) — `get_class($event)` anahtarıyla listener bulur | ✅ kod hazır (119 satır) |
| 4 | Priority sıralı listener çağrısı (satır 36-42) | ✅ kod hazır |
| 5 | Handler (audit/istatistik/bildirim) | ⚠️ PLANNED — handler sınıfı yok |
| 6 | SQL'e yazım (`user_events`, `analytics_realtime_events`) | ⚠️ PLANNED — §3.4 |

### §5.4 — Senaryo D: Router → EventBus köprüsü (GERÇEK)

| Adım | Nerede | Satır | Ne olur |
|------|--------|-------|---------|
| 1 | `js/router/RouterEventManager.js` | 10 | `popstate` listener → `onNavigate` |
| 2 | `js/router/RouterEventManager.js` | 11 | `click` listener → `#handleClick` → aynı origin + SPA linki ise `e.preventDefault()` (38) + `onNavigate` (39) |
| 3 | Router navigasyonu tamamlar | NavigationOrchestrator.js | (command-query kardeşinin omurgası) |
| 4 | `NavigationOrchestrator.js` / `SPARouterAdapter.js` | 94 / 113 | `nav:complete` olayı **emit edilir** (dinleyen: `ScrollManager.js:26`) |
| 5 | Dinleyiciler | — | Scroll konumu, odak yönetimi gibi tepkiler |

> **Not:** `RouterEventManager` DOM olaylarını **dinler** (addEventListener —
> Web API), EventBus ise uygulama-içi pub/sub'tur. İkisi aynı katmanda DEĞİLDİR;
> köprü yalnız `eventBus` parametresiyle kurulur (main.js:84).

---

## §6 — Kardeş klasörler ile fark

| Klasör | Bu klasörün farkı |
|--------|-------------------|
| `command-query/` | HTTP method + veri yönü. Bir `POST /music` hem komut (DB yazar) hem event kaynağı (müstakbel `music.added` yayını) olabilir — bu klasör ikinci yüzeyi tarif eder. |
| `usecase-akislari/` | Kullanıcı adımı zinciri (giriş → arama → çalma). Olay akışı bu adımların **altında** yatan gevşek bağlanma katmanıdır. |
| `routing/` | Rota eşleşme. Event akışı route'tan bağımsızdır (route eşleşmeden de event yayınlanabilir — mesela hata olayı). |
| `application-services/` | Servis nesneleri. Event **üreteci** olabilirler ama bu klasör onların iç yapısını değil, yayının yolunu tarif eder. |

---

## §7 — Kapsam dışı (bu belge aşmaz)

1. WebSocket / SSE / `EventSource` / BroadcastChannel kanal tasarımı (repo'da bu
   klasörün ölçümünde **isabet aranmadı** — ayrı görev).
2. Kuyruk/Outbox/Dead-letter desenleri (repo'da broker bağımlılığı YOK).
3. CSS pseudo-class (`:hover`, `:focus`) kataloğu — `k11-ux/` katmanı.
4. Log şeması normalizasyonu (BCNF işi — Data Engineer).
5. Test yazımı (QA Engineer) — bu klasör yalnız envanter/akış tarifidir.

---

## §8 — Terimler (repo-kaynaklı)

| Terim | Tanım | Kanıt |
|-------|-------|-------|
| **Event (olay)** | "Bir şey oldu" haberi — nesne/`CustomEvent` olarak taşınır | `MusicPlayedEvent` (PHP) · `new CustomEvent('devicechange')` (JS:280) |
| **Dispatcher** | Olayı dinleyicilere ileten PHP nesnesi (PSR-14) | `EventDispatcher.php:28` |
| **EventBus** | JS pub/sub kanalı (`Map<string, Set<fn>>`) | `EventBus.js:8-10` |
| **Listener (dinleyici)** | Olay geldiğinde çağrılacak callable/fn | PHP: `addListener` (EventDispatcher.php:50) · JS: `on` (EventBus.js:18) |
| **Domain event** | İş kuralı olayı (`music.played`) | `Events/Domain/` ×9 |
| **Integration event** | Servisler arası olay (+`source()`) | `Events/Integration/` ×3 |
| **Stoppable** | Dinleyicinin yayını durdurabilmesi | `StoppableEventTrait.php:24,32` |
| **Wiring** | Dispatcher'ın production akışına bağlanması | ADR-086 §5.2 — PLANNED |
| **Bridge** | DOM olayını EventBus'e (veya tersi) taşıyan kod | `main.js:84` · `RouterEventManager.js` |
| **Magic string** | Kodda ham yazılmış olay adı | `'player:play'` (PlayerController.js:126) |

---

## §9 — Açık sorular

| # | Soru | Etki | Durum |
|---|------|------|-------|
| 1 | Wiring sırası: audit (UserLoggedIn/Out → coremusic_logs) mi, istatistik (MusicPlayed) mi, bildirim (PasswordResetRequested) mi önce? ADR-086 §5.2 sıralı — onaylı mı? | Backend | ⚠️ VERIFICATION REQUIRED |
| 2 | Dispatcher `get_class($event)` (EventDispatcher.php:30) ile anahtarlarken `eventName()` (`'music.played'`) NE İÇİN var? String ad alanı da desteklenecek mi, yoksa handler tip-imzalı mı yazılacak? | Backend | ⚠️ VERIFICATION REQUIRED |
| 3 | `symfony/event-dispatcher ^7.0` composer'da var (composer.json:21) ama `EventDispatcher` kendi implementasyonu (PSR-14 interface) — Symfony sınıfı hangi noktada kullanılacak (ör. `EventSubscriberInterface`)? | Backend | ⚠️ VERIFICATION REQUIRED |
| 4 | JS olay adları için merkezî sabit dosya (ör. `js/router/config/events.js` — envanterde adı geçiyor ama **diskte YOK**, config/ içinde 5 js var: auth-routes, css-selectors, error-types, headers, signal-utils) üretilecek mi? | UI | ⚠️ VERIFICATION REQUIRED |
| 5 | `window.CoreMusic.EventBus` (main.js:186) production'da açık — CSP/güvenlik gözden geçirmesi + tree-shake/kaldırma kararı? | Security | ⚠️ VERIFICATION REQUIRED |
| 6 | `user_events` / `analytics_realtime_events` tablolarına PHP handler yazımı bu wiring fazına dahil mi (ADR-086 §5.2'de "audit" olarak geçiyor, hedef tablo net değil)? | Data | ⚠️ VERIFICATION REQUIRED |
| 7 | EventBus `emit` senkron — uzun süren dinleyiciler ana thread'i bloklar; `once`/`off` disiplini dışında bir koruma (performans bütçesi) var mı? | UI | ⚠️ VERIFICATION REQUIRED |
| 8 | `devicechange`/`oauth:notification` DOM olayları `window`'da — namespace çakışması denetimi (3. parti script) ve `isTrusted` kontrolü düşünülüyor mu? | Security | ⚠️ VERIFICATION REQUIRED |

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

---

## §10 — SQL olay yüzeyi (kapsamın veri katmanı)

Olayların **iz düşeceği** SQL yüzeyi `.ai/.sql/mysql/coremusic_logs.sql` (452 satır) içindedir.
Bu tablolar olay *üretmez*; PHP handler'ları yazıldığında (ADR-086 §5.2 wiring) bu
tablolar hedef olacaktır. Kapsam kararı: **bu dosya tablo şemasını değiştirmez** —
yalnız eşlemeyi kaydeder.

| # | Tablo | SQL satırı | İlgili sütun | Tip | Kapsam durumu |
|---|-------|-----------|--------------|-----|---------------|
| 1 | `user_events` | L433 / L435 | `event_type` | `varchar(50)` | **KAPSAM İÇİ** (hedef — 12 PHP olay adı sığar) |
| 2 | `analytics_realtime_events` | L109 | olay satırı | — | **KAPSAM İÇİ** (ADR-086 "audit/istatistik" hedef adayı) |
| 3 | `log_activity` | L224 / L228 | `action` | ENUM(35) | **kapsam dışı sözlük** (aksiyon ≠ olay) |
| 4 | `log_security` | L296 / L299 | `event_type` | ENUM(18) | **GAP** — PHP event sınıfı yok (dogrulama §10.3) |
| 5 | `log_system` | L322 / L325 | `event_type` | ENUM(27) | **GAP** — PHP event sınıfı yok (dogrulama §10.3) |
| 6 | `log_events` | L248 / L251 | `level` | ENUM(6) | kapsam dışı (log seviyesi, olay adı değil) |

### §10.1 - Sınır kararı

1. Wiring başladığında **önce** `user_events` (serbest `varchar`) hedef alınır —
   ENUM sözlüğü olmadığı için olay adı değişikliği migration gerektirmez.
2. `log_activity`/`log_security`/`log_system` ENUM'ları PHP event adlarıyla
   **eşleştirilmez** (farklı sözlük, dogrulama §10.1 §10.3).
3. Olay adı değişikliği = kod değişikliği (`return` satırı) + varsa SQL ENUM
   değişikliği → **her ikisi de ADR gerektirir** (frozen ADR kuralı).

### §10.2 - Uzunluk sınırı

`user_events.event_type` = `varchar(50)`; en uzun PHP olay adı
`user.password_reset_requested` = **30 karakter** (%60 doluluk) → kapsam dışı
taşma yok (dogrulama §10.2 tam tablosu).

---

## §11 - JS dizin envanteri (kapsamın frontend yüzeyi, 95 dosya)

`assets.coremusic.net/js/` altında **95 `.js` dosyası** (2026-10-15 recursive sayım).
Kapsam kararı: olay **akışı** bu 95 dosyanın hepsini değil, yalnız **üretici +
yayıcı + abone** olanları kapsar.

| # | Dizin | Dosya | Satır | Kapsamda mı? | Gerekçe |
|---|-------|-------|-------|--------------|---------|
| 1 | `js/core/` | 37 | (karakter/… EventBus, ComponentBase burada) | **EVET** | `EventBus.js` + `ComponentBase.js` = iki mekanizmanın çekirdeği |
| 2 | `js/router/` | 28 | 1206 (23 root 1143 + 5 config 63) | **EVET** | `nav:*` yayımı + abonelikleri |
| 3 | `js/player/` | 7 | — | **EVET** | `player:progress` yayımı (abonesiz — gap) |
| 4 | `js/components/` | 6 | — | **EVET** | `cm:*` CustomEvent üreticileri |
| 5 | `js/services/` | 5 | — | **EVET** | servisler arası `EventBus` kullanımı |
| 6 | `js/utils/` | 5 | — | **kısmi** | yalnız olay taşıyanlar (diğerleri kapsam dışı) |
| 7 | `js/config/` | 5 | 63 | **kısmi** | `events.js` **diskte YOK** (§9 madde 4) |
| 8 | diğer (kök + alt) | 5 | — | **duruma göre** | `main.js` (186 `window.CoreMusic.EventBus`) EVET |
| — | **Toplam** | **95** | — | — | bilesenler §11 tam envanter |

**Kapsam dışı JS (bilinçli):** `.min.js` bundle'ları, vendor, test dosyaları,
`js/**` dışındaki subdomain script'leri.

---

## §12 - Kapsam satırları: gerçek dosya:satır dayanakları

Bu bölüm §2/§3/§5'teki iddiaların **kanıt indeksidir** — her satır disk'te doğrulanmıştır
(2026-10-15). Yeni kapsam kararı verilirken önce bu tabloya bakılır.

| # | Kapsam iddiası | Dosya | Satır | Kanıt tipi |
|---|----------------|-------|-------|-----------|
| 1 | PHP dispatcher 6 method | `shared/src/Events/EventDispatcher.php` | 28, 50, 64, 82, 101, 109 | dosya+satır |
| 2 | Dispatcher 119 satır | aynı | 1–119 | sayım |
| 3 | `StoppableEventTrait` 36 satır | `shared/src/Events/StoppableEventTrait.php` | 24, 32 (`isPropagationStopped`) | dosya+satır |
| 4 | `get_class($event)` anahtarlama | EventDispatcher.php | 30 | dosya+satır |
| 5 | 12 olay adı `return` | `Events/Domain/*` + `Events/Integration/*` | §9.1 tablosu | dosya+satır |
| 6 | `UserLoggedOutEvent` = `user.logged_out` | `Events/Domain/UserLoggedOutEvent.php` | 38 | dosya+satır |
| 7 | 0 üretim `addListener` | `shared/src/**` (Events/ hariç) | — | grep = 0 |
| 8 | 2 HTTP `dispatch` | `shared/src/PageRouter/PageRouterKernel.php` · `api.coremusic.net/index.php` | 118 · 182 | dosya+satır |
| 9 | `psr/event-dispatcher` kurulu | `shared/composer.json` | 18 | dosya+satır |
| 10 | `symfony/event-dispatcher` kurulu ama wired değil | `shared/composer.json` | 21 | dosya+satır |
| 11 | PSR-4 `CoreMusic\` → `src/` | `shared/composer.json` | 29 | dosya+satır |
| 12 | 15 PHPUnit test methodu | `shared/tests/Events/EventDispatcherTest.php` (8) + `DomainEventTest.php` (7) | L19–L88 · L13–L63 | dosya+satır |
| 13 | Integration 3 sınıf = **0 test** | `Events/Integration/*` | — | grep = 0 |
| 14 | EventBus 30 emit / 3 on | `assets.coremusic.net/js/**` | — | grep |
| 15 | ComponentBase CustomEvent | `js/core/ComponentBase.js` | 209–212 | dosya+satır |
| 16 | 2 emit abonesiz | `js/player/PlayerController.js:87` · `js/router/SPARouterAdapter.js:35` | 87 · 35 | dosya+satır |
| 17 | `nav:complete` emit eden 2 nokta | `js/router/NavigationOrchestrator.js:94` · `SPARouterAdapter.js:113` | 94 · 113 | dosya+satır |
| 18 | `main.js` global EventBus | `assets.coremusic.net/js/main.js` | 186 | dosya+satır |
| 19 | `window.dispatchEvent(devicechange)` | `js/device-loader.js` | 280 | dosya+satır |
| 20 | `new CustomEvent('oauth:notification')` | `js/oauth-manager.js` | 287–290 | dosya+satır |

> **Kural:** Yukarıdaki 20 satırın herhangi biri çürürse (kod değişirse) bu belge
> §12'den güncellenir; **sayı uydurulmaz** (AGENTS §3).

---

## §13 - Bağımlılık grafiği (kapsamın dışa açılımları)

```text
[Olay Üretici]                    [Yayıcı]                      [Tüketici]
 Domain/Integration event  -->  EventDispatcher (PHP)     -->  (0 üretim dinleyici — ADR-086 PLANNED)
   eventName() return            get_class($event) L30            Listener: YOK
   12 sınıf                      PSR-14 interface                 Test: 15 method (yalnız Domain+Dispatcher)

 JS event adı string       -->  EventBus.emit (A)        -->  EventBus.on = 3 abone
   30 emit                          try/catch L61-65                player:progress / nav:navigate: YOK
 cm:* this.emit             -->  ComponentBase L209-212   -->  DOM addEventListener (B)
   37 çağrı                       dispatchEvent L212               112 addEventListener(
 window.dispatchEvent       -->  window ('devicechange'..) -->  device-layout-updater.js L514
```

### §13.1 - Dış bağımlılıklar (kapsam sınırları)

| # | Dış kaynak | Ne verir | Kapsam etkisi |
|---|-----------|----------|---------------|
| 1 | ADR-086 (`accepted/`) | wiring sırası + PLANNED otoritesi | kapsam **kod yazmaz**, planı referanslar |
| 2 | PSR-14 (php-fig) | interface sözleşmesi | havuz dışı URL → claim çekilemez (web-arastirma §3) |
| 3 | MDN 5 URL (spec §9) | DOM claim'leri | web-arastirma dosyasında işlenir |
| 4 | `coremusic_logs.sql` | hedef tablolar | §10 — şema değişikliği kapsam dışı |
| 5 | `command-query/` kardeş klasör | Gateway/RouteTable omurgası | §6 — ayrım tablosu |
| 6 | `.ai/.templates/` (Guardrail #16) | şablon | bu klasör `.templates/ui-design` değil → Kalıp A–D **geçerli değil**, mimari iskelet (H1+Amaç+§GRUPLAR+footer) uygulanır |

### §13.2 - Kapsam dışı bağımlılıklar (bilinçli olarak alınmayan)

| # | Kaynak | Neden alınmadı |
|---|--------|----------------|
| 1 | `symfony/event-dispatcher` sınıfları | kurulu ama wired değil (composer.json:21) — kullanım noktası §9.3 |
| 2 | Node `EventEmitter` | platform farklı (web-arastirma §3 satır 6) |
| 3 | Laravel/RxJS event desenleri | framework yasağı (ADR-001/002) |
| 4 | `.ai/scripts/*.ps1` gate'leri | bu görevde çalıştırma **yasak** (sayımlar elle) |

---

## §14 - Kapsam KPI'ları (ölçülebilir, 2026-10-15)

| # | KPI | Hedef | Gerçek | Durum |
|---|-----|-------|--------|-------|
| 1 | PHP event sınıfı (Domain) | 9 | 9 | ✅ |
| 2 | PHP event sınıfı (Integration) | 3 | 3 | ✅ |
| 3 | Olay adı (tek kaynak `return`) | 12 | 12 | ✅ |
| 4 | Üretim `addListener`/dispatch | wiring PLANNED → 0 beklenir | 0 | ✅ (beklenen) |
| 5 | Test methodu (Events) | ≥15 | 15 | ✅ |
| 6 | Integration test | 3 (sınıf başına ≥1) | **0** | ⚠️ GAPS |
| 7 | JS olay mekanizması | 2 (açıkça ayrı) | 2 (A + B) | ✅ |
| 8 | Abonesiz emit | 0 | **2** (`player:progress`, `nav:navigate`) | ⚠️ GAPS |
| 9 | Merkezi olay adı sabiti dosyası | 1 | **0** (`events.js` diskte yok) | ⚠️ GAPS |
| 10 | SQL ENUM ↔ PHP ad eşleşmesi | netleştirilmiş | 3 tablo ayrı sözlük + 2 GAP | ✅ (kayıtlı) |
| 11 | Havuz dışı URL erişimi | 0 | 0 | ✅ |
| 12 | Uydurma iddia | 0 | 0 (8 düzeltme kayıtlandı) | ✅ |

---

## §15 - Kapsam karar defteri (bu turda alınan 5 karar)

| # | Karar | Gerekçe | Dayanak |
|---|-------|---------|---------|
| 1 | SQL ENUM sözlükleri PHP event adlarıyla **eşleştirilmez** | Farklı semantik katmanı (aksiyon/güvenlik/sistem ≠ olay) | §10.1, dogrulama §10.1 |
| 2 | İlk wiring hedefi `user_events` (ENUM'sız) | `varchar(50)` = migration'sız olay adı özgürlüğü | §10.1, SQL L433/L435 |
| 3 | İki JS mekanizması **tek sistem değil, iki sistem** olarak belgelenir | DOM `CustomEvent` ≠ `EventBus` pub/sub; sayaçlar karıştırılırsa yanlış gap çıkar | §11, bilesenler §9, dogrulama §7.1 |
| 4 | `js/**` 95 dosyanın tamamı kapsam **değil** | Yalnız üretici/yayıcı/abone dosyalar akışın konusudur | §11 |
| 5 | `events.js` merkezî olay adı dosyası **üretilmez** | Üretim = yeni dosya = Faz 1 onayı gerektirir (AGENTS §4); açık soru olarak §9.4'te kalır | §9 madde 4, §14 KPI 9 |

> **Sonraki tur için devir:** Bu 5 karar onaylanmadan kapsam genişletilemez;
> özellikle karar 5 (`events.js`) yeni dosya üretimi demektir ve orkestratör onayı ister.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
