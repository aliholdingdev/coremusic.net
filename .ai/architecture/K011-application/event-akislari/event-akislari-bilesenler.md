---
title: "Event Akışları — Bileşen Envanteri ve Bağımlılık Grafiği"
type: mimari
kodeme: kademe-2
module: K011-application
parent: event-akislari
status: active
updated: 2026-10-15
group: mimari
---

# Event Akışları — Bileşen Envanteri ve Bağımlılık Grafiği

**Amaç:** Olay akışı bileşenlerinin (PHP dispatcher + event sınıfları, JS EventBus,
tüketiciler, SQL depo tabloları, CSS yüzeyi) disk envanterini sayımlar, bağımlılık
graf çizer ve boşlukları raporlar; her sayı 2026-10-15'te `Get-ChildItem` /
`Select-String` ile diskten ölçülmüştür.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | PHP olay çekirdeği envanteri | mimari | 118 agent · 351 skill | Komutlar |
| §2 | Event adı kayıt tablosu (12 ad) | mimari | 381 ADR · 43 C4 | Komutlar |
| §3 | Dispatcher, sözleşme, test envanteri | kanıt | 694 prompt · 50 kural | Komutlar |
| §4 | JS tarafı: EventBus + tüketiciler | mimari | 4 hook · 24 command | Komutlar |
| §5 | SQL/CSS olay yüzeyi | mimari | 24 workflow · 28 workflow | Komutlar |
| §6 | Bağımlılık grafiği ve sayısal özet | mimari | 6 domain prompt · 12 example | Sorgular |
| §7 | Boşluk ve tutarsızlık raporu | kanıt | 151 ekran · 37 flow | Sorgular |
| §8 | Kaynak kanıt dizini | kanıt | 227 wiki-link · 5 test | Sorgular |
| §9 | İki JS olay mekanizması (asimetri) | mimari | EventBus · ComponentBase | Komutlar |
| §10 | SQL olay yüzeyi (tablo detayı) | mimari | coremusic_logs.sql | Sorgular |
| §11 | JS alt dizin envanteri (95 dosya) | mimari | components · router · features | Komutlar |

---

## §1 PHP Olay Çekirdeği Envanteri

### §1.1 `shared/src/Events/**` — 17 dosya (gerçek glob, 2026-10-15)

| Dosya (göreli `shared/src/Events/`) | Satır | Rol |
|---|---|---|
| `CLAUDE.md` | 67 | Olay kataloğu dokümanı |
| `EventDispatcher.php` | 119 | Merkez dispatcher (PSR-14) |
| `StoppableEventTrait.php` | 36 | Durdurulabilir event mixin |
| `Domain/CLAUDE.md` | 38 | Domain event kılavuzu |
| `Domain/GenderSetEvent.php` | 76 | Domain event |
| `Domain/MediaAccessedEvent.php` | 86 | Domain event |
| `Domain/MusicAddedEvent.php` | 86 | Domain event |
| `Domain/MusicPlayedEvent.php` | 86 | Domain event |
| `Domain/PasswordResetRequestedEvent.php` | 86 | Domain event |
| `Domain/PlaylistCreatedEvent.php` | 86 | Domain event |
| `Domain/UserLoggedInEvent.php` | 86 | Domain event |
| `Domain/UserLoggedOutEvent.php` | 76 | Domain event |
| `Domain/UserRegisteredEvent.php` | 96 | Domain event |
| `Integration/CLAUDE.md` | 38 | Integration event kılavuzu |
| `Integration/AuthValidatedEvent.php` | 86 | Integration event |
| `Integration/NotificationEvent.php` | 104 | Integration event |
| `Integration/SessionCreatedEvent.php` | 94 | Integration event |

**Toplam: 17 dosya = 14 PHP + 3 Markdown.** Dağılım: kök 3 · Domain 10 (9 event + 1 md)
· Integration 4 (3 event + 1 md).

### §1.2 Olay sözleşmeleri — `shared/src/Contracts/Events/**` (3 dosya)

| Dosya | Satır | İçerik |
|---|---|---|
| `CLAUDE.md` | (kılavuz) | Sözleşme dokümanı |
| `DomainEventInterface.php` | 39+ | `eventName()` 24 · `occurredOn()` 31 · `payload()` 38 |
| `IntegrationEventInterface.php` | 46 | `eventName()` 24 · `source()` 31 · `payload()` 38 · `occurredOn()` 45 |

**Event yüzeyi toplamı (index §2'deki "20 dosya" iddiasının kaynağı):**
`Events/**` 17 + `Contracts/Events/**` 3 = **20 dosya**. (Tek `Events/**` globu 17 verir;
iddia yalnız ikisi birlikte sayıldığında doğrudur.)

### §1.3 Sınıf ↔ sözleşme kenarları (gerçek `implements` satırı)

| Sınıf | `implements` satırı | Sözleşme |
|---|---|---|
| 9 Domain event'in tamamı (`final class …`) | satır 20 | `DomainEventInterface` |
| 3 Integration event'in tamamı | satır 20 | `IntegrationEventInterface` |

`Select-String -Pattern 'class |implements '` → 12/12 isabet, hepsi satır 20.

### §1.4 Composer kaydı (gerçek satır)

| Satır | İçerik | Etki |
|---|---|---|
| `shared/composer.json:18` | `"psr/event-dispatcher": "^1.0"` | PSR-14 arayüz paketi |
| `shared/composer.json:21` | `"symfony/event-dispatcher": "^7.0"` | Olay uygulaması paketi (kurulu, wired değil) |
| `shared/composer.json:29` | `"CoreMusic\\": "src/"` (PSR-4) | `CoreMusic\Events\*` autoload'u |

**Not (düzeltme):** `Events\` için ayrı PSR-4 anahtarı **yoktur**; event sınıfları
`CoreMusic\` => `src/` üzerinden `CoreMusic\Events\…` olarak yüklenir. `Psr\EventDispatcher`
arayüzleri `EventDispatcher.php:14-15`'te `use` edilir. `symfony/event-dispatcher` (^7.0)
**kurulu fakat kullanılmıyor** — kendi `EventDispatcher` sınıfı (§3.1) kullanılıyor;
Symfony dispatcher'a referans üretim kodunda 0 (yalnız vendor autoload).

**Wiring kapsam genişletmesi (2026-10-15):** §3.3 yalnız `shared/src/**` taradı;
tüm subdomain'ler (vendor hariç) tarandığında ek isabet:
`api.coremusic.net/index.php:182` → `$result = $gateway->dispatch($pipelineRequest);`
— bu da **HTTP Gateway** dispatch'idir (event değil). Sonuç değişmez: **0 olay dispatch'i**.

---

## §2 Event Adı Kayıt Tablosu (12 ad — gerçek `return` satırı)

> Event adı `const NAME` ile değil, `eventName()` metodunun `return` satırında taşınır.

| # | Sınıf (`shared/src/Events/`) | `return` satırı | Event adı | Tür |
|---|---|---|---|---|
| 1 | `Domain/GenderSetEvent.php` | 38 | `user.gender_set` | Domain |
| 2 | `Domain/MediaAccessedEvent.php` | 39 | `media.accessed` | Domain |
| 3 | `Domain/MusicAddedEvent.php` | 39 | `music.added` | Domain |
| 4 | `Domain/MusicPlayedEvent.php` | 39 | `music.played` | Domain |
| 5 | `Domain/PasswordResetRequestedEvent.php` | 39 | `user.password_reset_requested` | Domain |
| 6 | `Domain/PlaylistCreatedEvent.php` | 39 | `playlist.created` | Domain |
| 7 | `Domain/UserLoggedInEvent.php` | 39 | `user.logged_in` | Domain |
| 8 | `Domain/UserLoggedOutEvent.php` | 38 | `user.logged_out` | Domain |
| 9 | `Domain/UserRegisteredEvent.php` | 40 | `user.registered` | Domain |
| 10 | `Integration/AuthValidatedEvent.php` | 39 | `auth.validated` | Integration |
| 11 | `Integration/NotificationEvent.php` | 40 | `notification.sent` | Integration |
| 12 | `Integration/SessionCreatedEvent.php` | 39 | `session.created` | Integration |

**İkinci `return` satırları (kaynak adları):** `NotificationEvent.php:48` →
`notification.service` · `SessionCreatedEvent.php:47` → `auth.service`. Bunlar
`source()` (Integration sözleşmesi satır 31) döndürür; event adı değildir.

**Ölçüm metodu:** `Select-String -Path Domain/*.php,Integration/*.php -Pattern "return '[a-z_.]+'"`
→ 14 isabet (12 event adı + 2 source adı).

---

## §3 Dispatcher, Sözleşme, Test Envanteri

### §3.1 `EventDispatcher.php` metot haritası (119 satır)

| Metot | Satır | Not |
|---|---|---|
| `use Psr\EventDispatcher\EventDispatcherInterface` | 14 | PSR-14 |
| `use Psr\EventDispatcher\StoppableEventInterface` | 15 | Durdurma desteği |
| `final class EventDispatcher implements EventDispatcherInterface` | 20 | |
| `dispatch(object $event): object` | 28 | Olayı yayınlar |
| `addListener(string, callable, int $priority = 0)` | 50 | Öncelik destekli |
| `removeListener(string, callable)` | 64 | |
| `getListeners(string): array` | 82 | |
| `hasListeners(string): bool` | 101 | |
| `sortListeners(string): array` | 109 | `usort` 113 — öncelik sıralaması |

`StoppableEventTrait.php`: `isPropagationStopped()` 24 · `stopPropagation()` 32 (36 satır).

### §3.2 Test envanteri — `shared/tests/Events/**` (2 dosya, 15 test)

| Dosya | Satır | Test metodu (satır) |
|---|---|---|
| `EventDispatcherTest.php` | 98 | `testDispatchReturnsEventInstance` 19 · `testAddListenerAndGetListeners` 27 · `testListenerIsCalledOnDispatch` 36 · `testPriorityOrderRespected` 50 · `testRemoveListener` 67 · `testHasListenersReturnsTrueWhenListenersExist` 76 · `testGetListenersReturnsEmptyForUnknownEvent` 83 · `testMultipleListenersAllCalled` 88 |
| `DomainEventTest.php` | 71 | `testUserLoggedInEventName` 13 · `testUserLoggedInEventPayload` 19 · `testUserLoggedInGetters` 29 · `testUserRegisteredEventPayload` 37 · `testGenderSetEventPayload` 47 · `testEventOccurredOnIsDateTimeImmutable` 57 · `testEventTimestampIsRecent` 63 |

**Toplam: 8 + 7 = 15 test metodu** (grep `public function test` = 15).

**Test'in sınırları:** dispatcher testi olay adı olarak hem string hem
`UserLoggedInEvent::class` kullanır (`EventDispatcherTest.php:53,91`) — kayıt anahtarı
sınıf adıdır; `eventName()` dönüş değeri test edilmez (bkz §7-G5).

### §3.3 Wiring yoklama (ADR-086 `wiring=PLANNED` kanıtı)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `EventDispatcher` referansı | `shared/src/**` − `src/Events/**` | **4 isabet — hepsi `tests/Events/EventDispatcherTest.php` (7, 10, 12, 16)** |
| `addListener(` çağrısı | `shared/src/**` − `src/Events/**` | **0** |
| `->dispatch(` çağrısı | `shared/src/**` − `src/Events/**` | 1 isabet: `Router/PageRouterKernel.php:118` — bu **HTTP router** dispatch'idir (`$this->router->dispatch`), olay dispatch'u **değildir** |

**Sonuç:** üretim kodunda `EventDispatcher`'a bağlanmış **0** listener, **0** olay
`dispatch()`'i var. Olay altyapısı **IMPLEMENTED**, wiring **PLANNED** — ADR-086 ile
uyumlu; ayrıntı `command-query/` kardeş dosyada değil, bu dosyanın §7-G8'inde.

---

## §4 JS Tarafı: EventBus + Tüketiciler

### §4.1 Ölçüm çerçevesi

| Parametre | Değer |
|---|---|
| Kapsam | `assets.coremusic.net/**` recursive `*.js` |
| Dosya sayısı | **95** |
| `Bus.emit(` / `Bus?.emit(` isabeti | **30** |
| `Bus.on(` / `Bus?.on(` / `Bus.once(` isabeti | **3** |
| `new CustomEvent(` isabeti | **5** (2 sabit ad + 2 fabrika + 1 ek) |

### §4.2 `core/EventBus.js` API satırları (73 satır)

| Metot | Satır |
|---|---|
| `on(event, fn)` (unsubscribe closure döner) | 18 |
| `once(event, fn)` | 31 |
| `off(event, fn)` | 44 |
| `emit(event, data)` | 57 |
| `destroy()` | 70 |

### §4.3 `main.js` wiring satırları

`import EventBus` 13 · `new EventBus()` **71** · `new CoreMusicApp({ eventBus })` 72 ·
`new Router({ …, eventBus })` 84 · `emit('router:ready')` 90 · **10 manager enjeksiyonu
96–139** (Device 96 · Scale 100 · Theme 106 · ViewMode 110 · Sidebar 115 · Player 123 ·
Widget 127 · Card 131 · Scroll 135 · Touch 139) · `emit('app:ready')` 182 ·
`window.CoreMusic.EventBus` **186**.

### §4.4 Emit envanteri — 26 benzersiz ad / 30 çağrı (gerçek satır)

| Grup | Adlar (satır) | Dosya |
|---|---|---|
| uygulama | `router:ready` 90 · `app:ready` 182 | `main.js` |
| uygulama | `app:ready` 100 | `CoreMusicApp.js` |
| kart | `card:click` 35 | `CardManager.js` |
| player | `player:volume` 113,158 · `player:pause` 122 · `player:play` 126 · `player:stop` 135 · `player:prev` 138 · `player:next` 139 · `player:shuffle` 143 · `player:repeat` 149 | `PlayerController.js` |
| touch | `touch:longpress` 76 · `touch:swipe` 91 | `TouchManager.js` |
| widget | `widget:folders:click` 85 | `WidgetManager.js` |
| ölçek | `scale:applied` 366 | `ScaleManager.js` |
| tema | `themechange` 69 · `modechange` 130,221 | `ThemeManager.js` |
| görünüm | `viewmodechange` 45 | `ViewModeManager.js` |
| router | `nav:complete` 113 · `nav:error` 116 | `SPARouterAdapter.js` |
| sidebar | `sidebar:resize` 664 · `sidebar:ready` 696 · `sidebar:toggle` 731 · `sidebar:viewchange` 887 · `sidebar:sort` 896 | `SidebarManager.js` |

**Toplam benzersiz ad: 26** (`app:ready` 2 dosyada = 1 ad; çağrı 30).

### §4.5 Dinleyici (on/once) envanteri — yalnız 3 kayıt

| Dosya | Satır | Dinlenen ad |
|---|---|---|
| `features/PlayerController.js` | 87 | `player:progress` |
| `managers/ScrollManager.js` | 26 | `nav:complete` |
| `router/SPARouterAdapter.js` | 35 | `nav:navigate` |

**Kritik ölçüm:** 30 emit çağrısına karşılık repo genelinde yalnız **3** `on()` kaydı
vardır. `player:progress` ve `nav:navigate` adları emit eden **hiçbir dosya yok**
(`emit('player:progress'` / `emit('nav:navigate'` → 0 isabet; tüm repo, vendor dışı,
`*.js|*.php|*.html` taraması). Bu iki ad ya `window.CoreMusic.EventBus` (satır 186)
üzerinden dış/köprü kodu tarafından yayınlanıyordur ya da eksiktir — bkz §7-G6.

### §4.6 DOM CustomEvent akışı (5 `new CustomEvent(` isabeti)

| # | Olay | Dosya:satır | Dağıtım |
|---|---|---|---|
| 1 | `devicechange` | `device-loader.js:280` | `window.dispatchEvent` |
| 2 | `oauth:notification` | `oauth-manager.js:287` (dispatch 290) | `window` |
| 3 | `cm:player:trackchange` | `footer.init.js:126` | `document.dispatchEvent` |
| 4 | (fabrika — ad parametreden) | `components/base/ComponentBase.js:212` | bileşen içi |
| 5 | (fabrika — ad parametreden) | `components/…/ComponentEvents.js:79` | bileşen içi |

### §4.7 Router event köprüsü

`js/router/RouterEventManager.js` — 48 satır: `#listeners = new Map()` 4 ·
`bind(callbacks)` 7 · `unbind()` 19 · `#addListener(target, type, handler, options)` 27 ·
`#handleClick` 32 · `#handleHover` 42. DOM olaylarını (`popstate`, `click`, `mouseenter`,
`offline`, `online`, `error`, `unhandledrejection`) `bind` içindeki callback'lere bağlar.

---

## §5 SQL/CSS Olay Yüzeyi

### §5.1 Olay tabloları — `.ai/.sql/mysql/coremusic_logs.sql` (452 satır)

| Tablo | `CREATE` satırı | `event_type` satırı | Tip | Değer kümeleri (satır) |
|---|---|---|---|---|
| `analytics_realtime_events` | 109 | 111 | `varchar(50)` | ENUM yok — serbest metin; `KEY idx_realtime_events_type` 118, `…_type_created` 121 |
| `log_security` | 296 | 299 | `ENUM(…)` | `CSRF_VIOLATION, AUTH_ATTEMPT_SUCCESS, AUTH_ATTEMPT_FAILED, AUTH_BYPASS_DETECTED, RATE_L…` (kısmi okuma) — index 313, 318 |
| `log_system` | 322 | 325 | `ENUM(…)` | `SERVICE_START, SERVICE_STOP, SERVICE_ERROR, CRON_JOB_START, CRON_JOB_COMPLETE, CRON_JO…` (kısmi okuma) — index 333, 337 |
| `user_events` | 433 | 436 | `varchar(50)` | ENUM yok; `KEY idx_ue_type` 445 |

**Korelasyon (düzeltme):** `log_security.event_type` değerleri `login_success` vb.
** değil;** ENUM uzayı güvenlik-sınıflandırma (`CSRF_VIOLATION`, `AUTH_*`) eksenindedir.
`user_events` / `analytics_realtime_events` serbest metin olduğu için PHP event adı
(`user.logged_in`) bu iki tabloya **doğrudan yazılabilir** — ENUM çakışması yalnız
`log_security` / `log_system` için geçerlidir (bkz §7-G1).

### §5.2 Diğer olay-güdümlü tablolar (aynı dosya)

`audit_logs` 151 · `log_events` 248 · `log_performance` 275 · `page_views` 341 ·
`search_logs` 398 · `user_activity_logs` 416 · `rate_limit_logs` 382. (Sayılar `CREATE
TABLE` satırlarıdır; 2026-10-15 ölçümü.)

### §5.3 CSS yüzeyi

| Ölçüm | Değer |
|---|---|
| `Css/**` genelinde `[data-` isabeti | **213** |
| `01_Abstracts/a-color-mode-tokens.css` | 379 satır |
| `html[data-mode="light"]` bloğu | satır 143 |
| `html:not([data-mode])` fallback (`@media prefers-color-scheme` içinde) | satır 39 |
| `[data-gender="female"|"male"|"neutral"]` kombinasyonları | 217, 228, 239, 253, 259 |

**Akış:** JS `ThemeManager.js:87` (`data-gender`) ve `:166` (`data-mode`) attribute
yazar → CSS attribute selector'ları token'ı değiştirir. Olayın **gözlenebilir yüzeyi**
budur; olay adı CSS'e taşınmaz.

---

## §6 Bağımlılık Grafiği ve Sayısal Özet

```text
[PHP — IMPLEMENTED, wiring PLANNED]
  Domain/Integration event sınıfı (14)
      implements → Contracts/Events/{Domain,Integration}EventInterface
      eventName() return → §2'deki 12 ad
  EventDispatcher (PSR-14, 119 satır)
      ├─ usort/priority (109-113)
      ├─ StoppableEventTrait (stopPropagation 32)
      └─ composer: psr/event-dispatcher ^1.0 (composer.json:18)
  BAĞLANTI: src/** içinde addListener/dispatch = 0  → ADR-086 wiring PLANNED
  TEST: tests/Events/ (2 dosya, 15 test) dispatcher'ı kapsar

[JS — GERÇEK, canlı]
  emit kaynakları (10 manager + main/CoreMusicApp) ──30 emit──► EventBus.js (73)
  EventBus.js ──3 on──► PlayerController / ScrollManager / SPARouterAdapter
  EventBus ──global──► window.CoreMusic.EventBus (main.js:186)
  DOM: devicechange / oauth:notification / cm:player:trackchange ──► window|document
  RouterEventManager (48) ──DOM→callback köprüsü──► router

[Veri katmanı — bağımsız yazım]
  log_security (296) / log_system (322)   ◄── ENUM event_type (299/325)
  user_events (433) / analytics_realtime (109) ◄── varchar event_type (436/111)
  (dispatcher ile doğrudan bağlantı YOK — bkz §7-G7)

[CSS — gözleme]
  ThemeManager data-* yazımı ──► a-color-mode-tokens.css (213 [data-] isabeti)
```

### §6.1 Sayısal özet

| Metrik | Değer | Ölçüm |
|---|---|---|
| PHP event dosyası | 17 (`Events/**`) + 3 (`Contracts/Events/**`) = 20 | glob |
| Event adı | 12 (+2 source adı) | `return` grep |
| Dispatcher metodu | 8 (satır 28–113) | okuma |
| Test | 2 dosya / 15 metot | grep |
| Üretim `addListener(` | **0** | grep |
| JS dosyası | 95 | glob |
| Emit çağrısı / benzersiz ad | 30 / 26 | grep |
| `on()` kaydı | **3** | grep |
| DOM CustomEvent | 5 isabet (3 sabit ad) | grep |
| Olay SQL tablosu (ana) | 4 (+7 ilişkili) | `CREATE TABLE` |
| `[data-` CSS isabeti | 213 | grep |

---

## §7 Boşluk ve Tutarsızlık Raporu

| # | Boşluk | Kanıt | Etki | Telafi |
|---|---|---|---|---|
| G1 | PHP event adı (`user.logged_in`) ile `log_security`/`log_system` ENUM (`AUTH_*`, `SERVICE_*`) **farklı uzayda**; `user_events`/`analytics_realtime_events` ise `varchar` | `coremusic_logs.sql:299,325` vs `:436,111` | Güvenlik/sistem tablolarına PHP adıyla yazılamaz | `PLANNED` — ad haritası ADR gerekli |
| G2 | Event adı `eventName()` içinde gömülü; tek merkez sabit sınıfı yok | §2 — 12 ayrı dosya | Ad değişimi 12 dosyaya yayılır | `PLANNED`: `EventNames` sabitleri |
| G3 | `Events/CLAUDE.md` (67 satır) kataloğu ile `Contracts/Events/` ayrı paketler; kayıt anahtarı testte **sınıf adı**, üretimde **ad string** — iki eşleştirme biçimi | `EventDispatcherTest.php:53` (`::class`) vs `:79` (`'test.event'`) | Dinleyici kayıt kafa karışıklığı | Dokümante edildi |
| G4 | Üretim wiring **0** (`addListener` / event `dispatch()` = 0) | §3.3 | Olay akışı üretimde başlamıyor | ADR-086 `wiring=PLANNED` — kapatılana dek `PLANNED` |
| G5 | `player:progress` ve `nav:navigate` dinleniyor, emit eden dosya **yok** | §4.5 + tüm repo grep = 0 | İki kolda ölü abonelik ya da gizli emitter | `⚠️ VERIFICATION REQUIRED` |
| G6 | Emit 30 / on 3 — büyük asimetri | §4.1 | Olayların çoğu tüketicisiz ("haber uçurup dinleyen yok") | `⚠️ VERIFICATION REQUIRED` — kasıtlı pub/sub genişletme mi, eksik mi? |
| G7 | `log_*` / `user_events` / `analytics_*` tablolarına **hangi servisin** yazdığı `shared/src/**` içinde grep ile bulunamadı (dispatcher zaten yazmaz) | §3.3 + §5.1 | Kalıcı olay kaydı sahipsiz görünüyor | `⚠️ VERIFICATION REQUIRED` |
| G8 | `Integration/` event'leri için test **yok** (testler yalnız `Domain` + dispatcher) | §3.2 | Integration contract'ı doğrulanmamış | `PLANNED` test ekleme |
| G9 | 2 JS DOM olayı (`devicechange`, `oauth:notification`) + 3. `cm:player:trackchange` — bunlar EventBus dışı, iki ayrı yayın sistemi yan yana | §4.6 | Olay yüzeyi parçalı (EventBus ↔ window/document) | Dokümante edildi; birleştirme ADR gerekli |
| G10 | `notification.service` / `auth.service` adları `source()` dönüşü, event adı değil — katalogda karıştırılabilir | `NotificationEvent.php:48`, `SessionCreatedEvent.php:47` | Yanlış "3+2=14 event" sayımı | Bu dosya §2'de 12+2 olarak ayrıştırdı |

---

## §8 Kaynak Kanıt Dizini

| Kanıt | Konum | Tür |
|---|---|---|
| Olay çekirdeği 17 dosya + satırları | `shared/src/Events/**` | glob + `ReadAllLines` |
| Sözleşmeler 3 dosya | `shared/src/Contracts/Events/**` | glob + `Select-String` |
| Event adları (14 `return`) | `Domain/*Event.php`, `Integration/*Event.php` | gerçek satır |
| Dispatcher metotları | `shared/src/Events/EventDispatcher.php:14,20,28,50,64,82,101,109,113` | gerçek satır |
| Stoppable | `shared/src/Events/StoppableEventTrait.php:24,32` | gerçek satır |
| Composer | `shared/composer.json:18,29` | gerçek satır |
| Wiring yoklama | `Select-String` (`EventDispatcher`, `addListener(`, `->dispatch(`) | 2026-10-15 ölçümü |
| Testler 15 metot | `shared/tests/Events/{EventDispatcherTest,DomainEventTest}.php` | gerçek satır |
| JS sayıları (95/30/3/5) | `assets.coremusic.net/**` recursive grep | 2026-10-15 ölçümü |
| EventBus API | `js/core/EventBus.js:18,31,44,57,70` | gerçek satır |
| Manager wiring | `js/main.js:13,71,84,90,96-139,182,186` | gerçek satır |
| DOM CustomEvent | `device-loader.js:280` · `oauth-manager.js:287,290` · `footer.init.js:126` · `ComponentBase.js:212` · `ComponentEvents.js:79` | gerçek satır |
| Router köprüsü | `js/router/RouterEventManager.js:4,7,19,27,32,42` | gerçek satır |
| SQL tabloları | `.ai/.sql/mysql/coremusic_logs.sql:109,111,151,248,275,296,299,322,325,341,382,398,416,433,436` | gerçek satır |
| CSS yüzeyi | `Css/01_Abstracts/a-color-mode-tokens.css:39,143,217,228,239,253,259` + `Css/**` 213 isabet | gerçek satır |
| Wiring otoritesi | `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` (105 satır) | gerçek satır |
| Örnek gruplar | `js/features/PlayerController.js` · `js/managers/SidebarManager.js` · `js/auth/gender-select.js` | gerçektir |

---

## §9 İki JS Olay Mekanizması (asimetri açıklaması)

§4.5'teki "30 emit ↔ 3 `on()`" asimetrisi **yalnız EventBus** içindir. `js/` altında
**iki farklı olay mekanizması** vardır; birbirine karıştırılmamalıdır (2026-10-15 ölçümü).

### §9.1 Mekanizma karşılaştırması

| # | Mekanizma | Emit sayısı | Dinleme | Taşıma | Örnek |
|---|-----------|-------------|---------|--------|-------|
| 1 | **EventBus pub/sub** | **30** (`eventBus.emit` / `#eventBus.emit`) | 3 (`EventBus.on`) | Uygulama-içi, ad-tabanlı Map | `PlayerController.js:126` |
| 2 | **ComponentBase CustomEvent** | **37** (`this.emit(` → `dispatchEvent`) | DOM `addEventListener` (ComponentBase.on) | DOM `CustomEvent`, `cm:` önekli | `ButtonComponent.js:75` |

### §9.2 ComponentBase.emit mekanizması (DOM CustomEvent — EventBus DEĞİL)

| Satır | Kod |
|-------|-----|
| `ComponentBase.js:209` | `emit(eventName, detail) {` |
| `ComponentBase.js:211` | `this.#el?.dispatchEvent(` |
| `ComponentBase.js:212` | `new CustomEvent(name, { ... })` |

> **Kritik ayrım:** `this.emit('select')` → `cm:select` (L21 notu; `cm:` öneki otomatik,
> tam ad isteyen `cm:button:click` verir). Bu **DOM CustomEvent**'tir — EventBus Map'ine
> **gitmez**. Yani bileşen içi olaylar (37) EventBus sayımına **dahil değildir**; asimetri
> gerçek ve kasıtlıdır (bileşenler DOM, manager'lar EventBus).

### §9.3 EventBus emit dağılımı (30 — kime ait)

| # | Dosya | EventBus emit | Örnek satır |
|---|-------|---------------|-------------|
| 1 | `features/PlayerController.js` | 9 | 126 `player:play` |
| 2 | `managers/SidebarManager.js` | 5 | 887 `sidebar:viewchange` |
| 3 | `managers/ThemeManager.js` | 3 | 69 `themechange` |
| 4 | `main.js` | 2 | 90 `router:ready` · 182 `app:ready` |
| 5 | `router/SPARouterAdapter.js` | 2 | 113 `nav:complete` · 116 `nav:error` |
| 6 | `features/TouchManager.js` | 2 | 76 `touch:longpress` |
| 7 | `router/NavigationOrchestrator.js` | 1 | 94 `nav:complete` |
| 8 | `features/CardManager.js` | 1 | 35 `card:click` |
| 9 | `features/WidgetManager.js` | 1 | 85 `widget:folders:click` |
| 10 | `managers/DeviceManager.js` | 1 | 78 `devicechange` |
| 11 | `managers/ScaleManager.js` | 1 | 366 `scale:applied` |
| 12 | `managers/ViewModeManager.js` | 1 | 45 `viewmodechange` |
| 13 | `core/CoreMusicApp.js` | 1 | 100 `app:ready` |
| — | **Toplam** | **30** | — |

### §9.4 EventBus.on kayıtları (3 — emitter durumu)

| # | Dosya | Satır | Dinlenen ad | Emitter var mı? |
|---|-------|-------|-------------|-----------------|
| 1 | `features/ScrollManager.js` | 26 | `nav:complete` | ✅ EVET — NavigationOrchestrator.js:94 · SPARouterAdapter.js:113 |
| 2 | `features/PlayerController.js` | 87 | `player:progress` | ❌ HAYIR — emit yok (⚠️ §7-G5) |
| 3 | `router/SPARouterAdapter.js` | 35 | `nav:navigate` | ❌ HAYIR — emit yok (⚠️ §7-G5) |

> **Ölçüm notu:** 37 `this.emit(` (ComponentBase/DOM) + 30 `eventBus.emit` = 67 toplam
> `.emit(` isabeti; 2 ek isabet doc-comment (`ComponentEvents.js:8,62`) → grep ham 69.

---

## §10 SQL Olay Yüzeyi (tablo detayı)

`.ai/.sql/mysql/coremusic_logs.sql` — olay içeren tablolar (gerçek `CREATE TABLE` satırı).

### §10.1 Olay tabloları

| # | Tablo | `CREATE TABLE` satırı | Olay sütunu | Satır |
|---|-------|----------------------|-------------|-------|
| 1 | `analytics_realtime_events` | 109 | `event_type varchar(50)` | 111 · KEY 118-121 |
| 2 | `log_events` | 248 | `level enum(TRACE…CRITICAL)` | 251 |
| 3 | `log_security` | 296 | `event_type enum(CSRF_VIOLATION…)` | 299 · `severity` 300 · KEY 313,318 |
| 4 | `log_system` | 322 | `event_type enum(SERVICE_START…)` | 325 · `severity` 326 · KEY 333,337 |
| 5 | `user_events` | 433 | `event_type varchar(50)` | 436 · `event_value` 437 · `event_data json` 438 · KEY 445,446 |

### §10.2 Olay enum'ları (PHP event adlarıyla çapraz)

| # | SQL enum tablosu | Örnek değerler | PHP karşılığı |
|---|------------------|----------------|---------------|
| 1 | `log_security.event_type` (L299) | `AUTH_ATTEMPT_SUCCESS` · `SESSION_CREATED` · `SESSION_DESTROYED` · `CSRF_VIOLATION` | `AuthValidatedEvent` (`auth.validated`) · `SessionCreatedEvent` (`session.created`) |
| 2 | `log_activity.action` (L228) | `LOGIN` · `LOGOUT` · `MUSIC_PLAY` · `PLAYLIST_CREATE` | `UserLoggedInEvent` · `UserLoggedOutEvent` · `MusicPlayedEvent` · `PlaylistCreatedEvent` |
| 3 | `user_activity_logs.activity_type` (L419) | `login` · `logout` · `play` · `playlist_add` | domain event adları (§2) |
| 4 | `log_system.event_type` (L325) | `SERVICE_START` · `DEPLOYMENT_COMPLETE` | — (sistem; domain event yok) |

> **Eşleşme notu:** SQL enum'ları ile PHP `eventName()` dönüş değerleri (`user.logged_in`,
> `music.played` …) **aynı kavramı** farklı biçinde tutar (SQL `LOGIN`/`login` vs PHP
> `user.logged_in`). Bu, event'lerin **log tablolarına yazımının henüz wired olmadığını**
> gösterir (ADR-086 wiring PLANNED) — SQL yüzeyi **hazır**, köprü yok.

### §10.3 Olay içermeyen log tabloları (bilgi)

| # | Tablo | Satır | Not |
|---|-------|-------|-----|
| 1 | `audit_logs` | 151 | Denetim (event sütunu yok) |
| 2 | `error_logs` | 199 · `error_level` 201 | Hata seviyesi enum |
| 3 | `log_performance` | 275 · `metric_type` 278 | Performans metriği |
| 4 | `page_views` | 341 | Sayfa görüntüleme |
| 5 | `rate_limit_logs` | 382 | Rate-limit |
| 6 | `search_logs` | 398 | Arama |

---

## §11 JS Alt Dizin Envanteri (95 dosya)

`assets.coremusic.net/js/` — 2026-10-15 recursive sayım.

| # | Alt dizin | Dosya | Olay rolü |
|---|-----------|-------|-----------|
| 1 | `components/` | 37 | Bileşen CustomEvent (§9.2) |
| 2 | `router/` | 28 | SPA router + nav olayları |
| 3 | `features/` | 7 | Player/Touch/Scroll/Card/Widget (EventBus) |
| 4 | `core/` | 6 | EventBus · CoreMusicApp |
| 5 | `coreplayer/` | 5 | Çalar çekirdeği |
| 6 | `managers/` | 5 | Theme/Sidebar/Scale/Device/ViewMode (EventBus) |
| 7 | `auth/` | 2 | gender-select vb. |
| 8 | kök (`js/*.js`) | 5 | main.js · device-loader vb. |
| — | **Toplam** | **95** | — |

> **Dağılım notu:** 95 dosyanın 37'si bileşen (DOM CustomEvent), 30 EventBus emit'i
> features/managers/core/router'da yoğunlaşır (§9.3).

---

> **Spec §8 kaydı:** Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı
> v2 §2.11 dışıdır; belge seviyesinde kayıt.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
