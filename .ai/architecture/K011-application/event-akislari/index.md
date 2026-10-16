---
title: "K011 · event-akislari — index"
type: architecture
kodeme: kademe-2
module: K011-application
parent: event-akislari
status: active
updated: 2026-10-15
group: mimari
---

# K011-application/event-akislari — Olay Akışları

**Amaç:** Bu klasör, CoreMusic'te olay (event) üretimi, taşınması ve tüketiminin disk
kanıtıyla tarifidir: PHP tarafında PSR-14 EventDispatcher + 12 event sınıfı (ADR-086 —
altyapı IMPLEMENTED, production wiring PLANNED), JS tarafında `EventBus` pub/sub +
DOM `CustomEvent` akışları (GERÇEK, canlı). CSS ve SQL katmanları olayın
gözlenebilir yüzeyleridir (attribute override + event depo tabloları).

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | ELI15 | mimari | kavram | Olay akışı tek cümlede |
| §2 | GRUPLAR | mimari | katalog | Bu dosyanın bölüm haritası |
| §3 | Omurga | kanit | disk | Dispatcher + EventBus + DOM + SQL gerçek satırları |
| §4 | Kardeş klasörler | mimari | sınır | command-query/usecase/routing ile fark |
| §5 | Açık sorular | kanit | boşluk | Wiring PLANNED dürüst kaydı + spec §8 |

## §1 — ELI15

Bir şey olur ("şarkı çalındı", "kullanıcı girdi") ve bu haberi ilgilenen herkese
iletmek gerekir. Olay akışı budur: haberi **üreten** bir nokta, haberi **taşıyan** bir
kanal, haberi **dinleyen** dinleyiciler. CoreMusic'te iki ayrı dünya var: **PHP**
(`shared/src/Events/` — `EventDispatcher` üzerinden dispatch edilen 12 sınıf;
üretim akışına henüz bağlı değil, ADR-086 wiring PLANNED) ve **JS**
(`assets.coremusic.net/js/core/EventBus.js` — `main.js:71`'de kurulur, 10+ manager
constructor'ına enjekte edilir, 21 adet `ad:olay` adlandırılmış olay yayınlanır;
buna ek `window.dispatchEvent(new CustomEvent(...))` ile 2 adet DOM olayı vardır ve
bunlar GERÇEKÇALIŞIR).

## §2 — GRUPLAR (dosya kataloğu)

| Dosya | Grup | İçerik |
|-------|------|--------|
| `event-akislari-kapsam.md` | mimari | ELI15, sınır (ne DEĞİL), iki omurga (PHP/JS), olay adlandırma, akış tabloları, kapsam dışı, açık sorular |
| `event-akislari-dogrulama.md` | kanit | İddia↔Dosya↔Satır (≥8 satır), test envanteri, kapı PASS/BLOCK, KPI, olumsuz senaryolar |
| `event-akislari-web-arastirma.md` | kanit | MDN havuzu 5 URL + claim↔repo matrisi + reddedenler + kesişim betiği |
| `event-akislari-ornekler.md` | kod | PHP + JS + SQL + CSS gerçek bloklar, her blokta Dosya/Satır kanıtı, YAPMA↔YAP |
| `event-akislari-bilesenler.md` | mimari | Events/ 20 dosya + js 31 dosya SAYIM envanteri, bağımlılık grafiği, boşluklar |

## §3 — Omurga (disk kanıtı, 2026-10-15 ölçümü)

| # | Katman | Dosya | Kanıt |
|---|--------|-------|-------|
| 1 | Sözleşme | `shared/src/Contracts/Events/DomainEventInterface.php` | `eventName()` satır 24 · `occurredOn()` 31 · `payload()` 38 (39 satır) |
| 2 | Sözleşme | `shared/src/Contracts/Events/IntegrationEventInterface.php` | `source()` + `payload()` (46 satır) |
| 3 | Dispatcher | `shared/src/Events/EventDispatcher.php` | `dispatch()` satır 28 · `addListener()` 50 · `sortListeners()` 109 (119 satır, PSR-14) |
| 4 | Stoppable | `shared/src/Events/StoppableEventTrait.php` | `isPropagationStopped()` 24 · `stopPropagation()` 32 (36 satır) |
| 5 | Domain event ×9 | `shared/src/Events/Domain/*Event.php` | `music.played` (MusicPlayedEvent:39) · `user.logged_in` (UserLoggedInEvent:39) · diğer 7 — §3.1 |
| 6 | Integration event ×3 | `shared/src/Events/Integration/*Event.php` | `auth.validated` (39) · `notification.sent` (40) · `session.created` (39) |
| 7 | JS EventBus | `assets.coremusic.net/js/core/EventBus.js` | `on()` 18 · `once()` 31 · `off()` 44 · `emit()` 57 · `destroy()` 70 (73 satır) |
| 8 | JS wiring | `assets.coremusic.net/js/main.js` | `new EventBus()` 71 · `router:ready` emit 90 · manager enjeksiyonu 96–139 · `app:ready` 182 · `window.CoreMusic.EventBus` 186 |
| 9 | DOM olayı | `assets.coremusic.net/js/device-loader.js` | `window.dispatchEvent(new CustomEvent('devicechange', …))` satır 280 |
| 10 | DOM olayı | `assets.coremusic.net/js/oauth-manager.js` | `new CustomEvent('oauth:notification', …)` 287 · `window.dispatchEvent(event)` 290 |
| 11 | Router olayları | `assets.coremusic.net/js/router/RouterEventManager.js` | `bind()` 7 · `#addListener()` 27 (`addEventListener` 28) · `unbind()` 19 (`removeEventListener` 21) — 48 satır |
| 12 | SQL depo | `.ai/.sql/mysql/coremusic_logs.sql` | `analytics_realtime_events` 109 (`event_type` 111) · `user_events` 433 (`event_type` 436) · `log_security` 296 · `log_system` 322 |
| 13 | CSS yüzeyi | `assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css` | `html[data-mode]` 143/367 · `[data-gender]` 217–259 (JS attribute yazımı ThemeManager.js:87,166) |
| 14 | Karar | `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | "altyapı IMPLEMENTED, wiring PLANNED" satır 9/16 · production dispatch 0 satır 34 |

### §3.1 — Olay adı listeleri (taşındı, bu dosyada tutulmaz)

| Liste | Adet | Nerede |
|-------|------|--------|
| PHP event adı (dosya→`eventName` satırı) | 12 | `event-akislari-dogrulama.md` §9 tam tablo |
| JS event adı (19 EventBus + 2 DOM) | 21 | `event-akislari-kapsam.md` §4.2 |

> **Neden taşındı:** index 40–100 satır aralığında kalmalı; ayrıntılı listelerin
> tekrarı yerine tek kaynak gösterimi yapılır (SSOT).

## §4 — Kardeş klasörler ile fark

| Klasör | Fark (bu klasör ne YAPMAZ) |
|--------|----------------------------|
| `command-query/` | HTTP method + veri yönü (yazma/okuma) eksenı — bu klasör **olay** ekseni (yayın/abone); bir istek hem komut hem event kaynağı olabilir |
| `usecase-akislari/` | Kullanıcı senaryosu zinciri — bu klasör senaryo değil, alt katman olay altyapısı |
| `application-services/` | Servis nesneleri — bu klasör servis içi değil, servisler arası/modüller arası haberleşme |
| `routing/` | Rota tablosu/matcher — bu klasör route'u yalnız olay üretici olarak ilgilendirir |

## §5 — Açık sorular (özet)

| # | Soru | Durum |
|---|------|-------|
| 1 | PHP wiring PLANNED: `Events/` dışı 0 dispatch (ADR-086:34) — hangi handler önce (audit → istatistik → bildirim)? | ⚠️ VERIFICATION REQUIRED |
| 2 | JS olay adları magic string — `config/events.js` envanterde var, diskte **YOK** (config/: 5 js); ad sabitleme ADR ile mi? | ⚠️ VERIFICATION REQUIRED |
| 3 | EventBus `emit` senkron + `try/catch` (EventBus.js:60-66) — cross-tab (Service Worker) olay aktarımı düşünülüyor mu? | ⚠️ VERIFICATION REQUIRED |
| 4 | `user_events` / `analytics_realtime_events` tablolarına PHP handler yazılacak mı (şu an JS/HTTP log akışı)? | ⚠️ VERIFICATION REQUIRED |

> Ayrıntı: `event-akislari-kapsam.md` §9 (8 madde).

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
