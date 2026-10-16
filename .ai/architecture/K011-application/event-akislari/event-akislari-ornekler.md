---
title: "K011 · event-akislari — ornekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: event-akislari
status: active
updated: 2026-10-15
group: kod
---

# K011-application/event-akislari — Örnekler

**Amaç:** Bu belge `event-akislari` konusunu 4 dilde (PHP · Vanilla JS · SQL · CSS)
**gerçek repo bloklarıyla** gösterir. Her blok `Dosya: … · Satır: …` künyesi taşır
(uydurma blok yok); her blok grubu bir **YAPMA ↔ YAP** karşılaştırmasıyla biter.
`PLANNED` işaretli bloklar diskte YOKTUR, üretimi hedeftir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | PHP — dispatcher & event | kod | sunucu | Gerçek satırlar + PLANNED handler |
| §2 | JS — EventBus & DOM | kod | istemci | Gerçek satırlar + YAPMA↔YAP |
| §3 | SQL — event depoları | kod | veri | Gerçek şema + PLANNED yazım |
| §4 | CSS — attribute köprüsü | kod | stil | Gerçek token blokları + YAPMA↔YAP |
| §5 | Diller arası omurga | kod | köprü | 4 dilin bir olay akışında kesişimi |

---

## §1 — PHP (dispatcher & event)

### §1.1 — Dispatcher çekirdeği (GERÇEK)

```php
// Dosya: shared/src/Events/EventDispatcher.php · Satır: 28-45
public function dispatch(object $event): object
{
    $eventName = get_class($event);

    if (!isset($this->listeners[$eventName])) {
        return $event;
    }

    foreach ($this->getListeners($eventName) as $listener) {
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            break;
        }

        $listener($event);
    }

    return $event;
}
```

**Künye:** `Dosya: shared/src/Events/EventDispatcher.php · Satır: 28-45` · PSR-14
`dispatch(object): object` · anahtar `get_class($event)` (satır 30) · erken dönüş
(satır 32-34) · Stoppable kontrolü (satır 37-39).

### §1.2 — Listener kaydı + priority (GERÇEK)

```php
// Dosya: shared/src/Events/EventDispatcher.php · Satır: 50-59
public function addListener(string $eventName, callable $listener, int $priority = 0): void
{
    $this->listeners[$eventName][] = [
        'listener' => $listener,
        'priority' => $priority,
    ];

    // Invalidate sorted listeners cache
    unset($this->sortedListeners[$eventName]);
}
```

```php
// Dosya: shared/src/Events/EventDispatcher.php · Satır: 109-118
private function sortListeners(string $eventName): array
{
    $listeners = $this->listeners[$eventName];

    usort($listeners, function ($a, $b) {
        return $b['priority'] <=> $a['priority'];
    });

    return $listeners;
}
```

**Künye:** `Dosya: shared/src/Events/EventDispatcher.php · Satır: 50-59, 109-118` ·
priority azalan sıralama (`$b <=> $a`) · önbellek invalidasyonu satır 58.

### §1.3 — Domain event sözleşmesi (GERÇEK)

```php
// Dosya: shared/src/Contracts/Events/DomainEventInterface.php · Satır: 17-39
interface DomainEventInterface
{
    /** Get the event name. */
    public function eventName(): string;

    /** Get the timestamp when the event occurred. */
    public function occurredOn(): \DateTimeImmutable;

    /** Get the event payload data. */
    public function payload(): array;
}
```

### §1.4 — Somut domain event (GERÇEK)

```php
// Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · Satır: 20-61 (özet)
final class MusicPlayedEvent implements DomainEventInterface
{
    use StoppableEventTrait;

    private readonly \DateTimeImmutable $occurredOn;

    public function __construct(
        private readonly string $musicId,
        private readonly string $userId,
        private readonly int $duration
    ) {
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function eventName(): string
    {
        return 'music.played';                       // satır 39
    }

    public function payload(): array
    {
        return [
            'musicId'  => $this->musicId,
            'userId'   => $this->userId,
            'duration' => $this->duration,
            'timestamp'=> $this->occurredOn->format('c'),
        ];
    }
}
```

**Künye:** `Dosya: shared/src/Events/Domain/MusicPlayedEvent.php · Satır: 20-61` ·
`eventName()` satır 39 · payload 4 anahtar satır 55-60 · `readonly` + `strict_types`.

### §1.5 — Stoppable trait (GERÇEK)

```php
// Dosya: shared/src/Events/StoppableEventTrait.php · Satır: 17-36
trait StoppableEventTrait
{
    private bool $propagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
```

### §1.6 — PLANNED: wiring + handler (⚠️ diskte YOK — hedef)

```php
// ⚠️ PLANNED — diskte DEĞİL (ADR-086 §5.2 hedefi). Örnek: audit handler.
// Dosya (hedef): shared/src/Events/Handlers/AuditLoginHandler.php (ÜRETİLECEK)
final class AuditLoginHandler
{
    public function __construct(private readonly IDatabaseManager $db) {}

    public function __invoke(UserLoggedInEvent $event): void
    {
        // Hedef tablo kanıtı: .ai/.sql/mysql/coremusic_logs.sql → user_events (satır 433)
        // event_type kolonu satır 436 · event_data JSON satır 438
        // INSERT parametreli (ADR-002: PDO prepared, SELECT * yasak)
    }
}
```

> **Durum:** `Events/` dışı 0 dispatch/`new …Event(` (ADR-086:34) — bu blok **hedeftir**,
> bugün var değildir.

### §1.7 — YAPMA ↔ YAP (PHP)

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | `$dispatcher->addListener('music.played', $fn)` — dispatcher `get_class` ile anahtarlar, string ad **eşleşmez** (EventDispatcher.php:30) | `$dispatcher->addListener(MusicPlayedEvent::class, $fn)` — tip anahtarı kullan |
| 2 | Event sınıfına iş mantığı gömmek (DB yazımı constructor'a) | Event taşıyıcıdır; iş handler'da (ADR-086 §1.2) |
| 3 | Handler'da exception'ı yaymak (`dispatch` çağrıyı düşürür — dogrulama §5.4) | Handler'ı `try/catch` ile sar; isteği düşürme (fail-safe) |
| 4 | `eventName()` string'ini `addListener` anahtarı yapmak | `eventName()` teşhis/log içindir; anahtar `::class` |
| 5 | `SELECT *` ile log yazımı/okuma | Sütun listesi (`ApiKeyRepository.php:40` deseni — `SELECT_COLUMNS` const) |

---

## §2 — JS (EventBus & DOM)

### §2.1 — EventBus çekirdeği (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 8-24 (on)
export default class EventBus {
    /** @type {Map<string, Set<Function>>} */
    #listeners = new Map();

    on(event, fn) {
        if (!this.#listeners.has(event)) {
            this.#listeners.set(event, new Set());
        }
        this.#listeners.get(event).add(fn);
        return () => this.off(event, fn);   // unsubscribe closure
    }
```

```js
// Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 52-67 (emit)
emit(event, data) {
    const fns = this.#listeners.get(event);
    if (!fns) return;
    for (const fn of fns) {
        try {
            fn(data);
        } catch (err) {
            console.error(`[EventBus] Listener error on "${event}":`, err);
        }
    }
}
```

**Künye:** `Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 8-24, 52-67` ·
`Map<string, Set<fn>>` · `once` 31-37 · `off` 44-50 · `destroy` 70-72.

### §2.2 — Enjeksiyon (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/main.js · Satır: 71-84, 96-139 (özet)
const eventBus = new EventBus();                          // 71 — TEK instance
const app = new CoreMusicApp({ eventBus });               // 72
router = new Router({ ...routerConfig, guardFunctions, eventBus });  // 84
eventBus.emit('router:ready', { router });                 // 90
const deviceManager  = new DeviceManager(eventBus);        // 96
const scaleManager   = new ScaleManager(eventBus);         // 100
const themeManager   = new ThemeManager(eventBus);         // 106
const viewModeManager= new ViewModeManager(eventBus);      // 110
const sidebarManager = new SidebarManager(eventBus, {…});  // 115
const player  = new PlayerController(eventBus);            // 123
const widgets = new WidgetManager(eventBus);               // 127
const cards   = new CardManager(eventBus);                 // 131
const scroll  = new ScrollManager(eventBus);               // 135
const touch   = new TouchManager(eventBus);                // 139
// …
eventBus.emit('app:ready');                                // 182
window.CoreMusic.EventBus = eventBus;                      // 186
```

### §2.3 — Üretim + dinleme (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/features/PlayerController.js · Satır: 122-126
this.#eventBus.emit('player:pause');      // 122
// …
this.#eventBus.emit('player:play');       // 126
```

```js
// Dosya: assets.coremusic.net/js/features/PlayerController.js · Satır: 87
this.#eventBus.on('player:progress', (data) => { /* UI güncelle */ });
```

```js
// Dosya: assets.coremusic.net/js/features/CardManager.js · Satır: 35
this.#eventBus.emit('card:click', data);
```

### §2.4 — DOM CustomEvent (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/device-loader.js · Satır: 280
window.dispatchEvent(new CustomEvent('devicechange', { detail: /* cihaz verisi */ }));
```

```js
// Dosya: assets.coremusic.net/js/oauth-manager.js · Satır: 287-290
const event = new CustomEvent('oauth:notification', { /* detail */ });
window.dispatchEvent(event);
```

```js
// Dosya: assets.coremusic.net/js/device-layout-updater.js · Satır: 514
window.addEventListener('devicechange', function (e) { /* layout güncelle */ });
```

### §2.5 — Router DOM köprüsü (GERÇEK)

```js
// Dosya: assets.coremusic.net/js/router/RouterEventManager.js · Satır: 7-30
bind(callbacks) {
    const { onNavigate, onPrefetch, onOffline, onOnline, onError, onUnhandledRejection } = callbacks;
    this.#addListener(window,   'popstate',           () => onNavigate(window.location.pathname + window.location.search, false));  // 10
    this.#addListener(document, 'click',              (e) => this.#handleClick(e, onNavigate));                                    // 11
    this.#addListener(document, 'mouseenter',         (e) => this.#handleHover(e, onPrefetch), true);                              // 12
    this.#addListener(window,   'offline',            () => onOffline());                                                          // 13
    this.#addListener(window,   'online',             () => onOnline());                                                           // 14
    this.#addListener(window,   'error',              (e) => onError(e));                                                          // 15
    this.#addListener(window,   'unhandledrejection', (e) => onUnhandledRejection(e));                                             // 16
}
// …
#addListener(target, type, handler, options) {
    target.addEventListener(type, handler, options);   // 28
    this.#listeners.set(handler, { target, type, handler, options });   // 29 — referans kaydı (MDN claim §2.2-2)
}
```

### §2.6 — YAPMA ↔ YAP (JS)

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | Döngüde anonymous fn + `removeEventListener` denemesi (MDN claim: referans yok) | Map'te referans sakla + `unbind()` (RouterEventManager.js:19-25) |
| 2 | `emit` içinde `await`/ağ çağrısı (bus senkron — ana thread bloklanır) | Ağ işini fetch katmanına bırak, bus yalnız haber verir |
| 3 | Dinleyici hatasını `try/catch`'siz bırakmak (DOM `dispatchEvent` — istisna yayılmaz ama uncaught logu) | EventBus deseni: `try/catch` + `console.error` (EventBus.js:61-65) |
| 4 | Olay adını her seferinde ham string (`'player:play'` yazım hatası sessiz kayıp — EventBus.js:58-59) | Merkezî sabit (ör. `EVENTS.PLAYER_PLAY`) — ⚠️ PLANNED, diskte yok |
| 5 | Her modülde `new EventBus()` | TEK instance + enjeksiyon (main.js:71) |

---

## §3 — SQL (event depoları)

### §3.1 — Gerçek zamanlı olay tablosu (GERÇEK)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 109-122 (özet)
CREATE TABLE `analytics_realtime_events` (
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,          -- satır 111
  …
  KEY `idx_realtime_events_type` (`event_type`),                          -- 118
  KEY `idx_realtime_events_user_id` (`user_id`),                          -- 119
  KEY `idx_realtime_events_created_at` (`created_at`),                    -- 120
  KEY `idx_realtime_events_type_created` (`event_type`,`created_at`)      -- 121
) ENGINE=InnoDB … COMMENT='Real-time event tracking for live dashboards and monitoring';  -- 122
```

### §3.2 — Kullanıcı olay tablosu (GERÇEK)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 433-446 (özet)
CREATE TABLE `user_events` (
  `event_type`  varchar(50) … NOT NULL,                                   -- 436
  `event_value` varchar(255) … COMMENT 'music_id, album_id, arama terimi …', -- 437
  `event_data`  json DEFAULT NULL COMMENT 'Ekstra baglamsal veri',        -- 438
  …
  KEY `idx_ue_type` (`event_type`),                                       -- 445
  KEY `idx_ue_value` (`event_value`)                                      -- 446
) …
```

### §3.3 — Güvenlik olay enum'u (GERÇEK)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 299 (özet)
`event_type` enum('CSRF_VIOLATION','AUTH_ATTEMPT_SUCCESS','AUTH_ATTEMPT_FAILED',
 'AUTH_BYPASS_DETECTED','RATE_LIMIT_EXCEEDED','BRUTE_FORCE_DETECTED',
 'SESSION_HIJACK_ATTEMPT','XSS_ATTEMPT','SQL_INJECTION_ATTEMPT','PERMISSION_DENIED',
 'CREDENTIAL_VAULT_ACCESS','API_KEY_USED','TOKEN_REFRESH','SESSION_CREATED',
 'SESSION_DESTROYED','CORS_VIOLATION','CONTENT_SECURITY_POLICY_VIOLATION',
 'SUSPICIOUS_REQUEST') … COMMENT 'Guvenlik olay tipi'
```

### §3.4 — Sistem olay enum'u (GERÇEK — parça)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_logs.sql · Satır: 325 (özet)
`event_type` enum('SERVICE_START','SERVICE_STOP','SERVICE_ERROR','CRON_JOB_START',
 'CRON_JOB_COMPLETE','CRON_JOB_FAILED','DEPLOYMENT_START','DEPLOYMENT_COMPLETE',
 'DEPLOYMENT_FAILED','CONFIG_CHANGE','SCHEMA_MIGRATION','BACKUP_START', …
 'GRACEFUL_SHUTDOWN','EMERGENCY_STOP') … COMMENT 'Sistem olay tipi'
```

### §3.5 — PLANNED: PHP event → SQL yazımı (⚠️ diskte YOK — hedef)

```sql
-- ⚠️ PLANNED (hedef): ADR-086 §5.2 audit handler'ının yazacağı satır şeması.
-- Hedef tablo: user_events (coremusic_logs.sql:433)
-- event_type  = 'user.logged_in'  (PHP eventName — UserLoggedInEvent.php:39)
-- event_value = user_id
-- event_data  = JSON { ipAddress, userAgent }  (UserLoggedInEvent payload — satır 55+)
-- Yazım: PDO prepared (ADR-002), ENUM'a takılmamak için PHP ad → SQL enum eşlemesi netleşmeli (§5 boşluğu)
```

### §3.6 — YAPMA ↔ YAP (SQL)

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | `INSERT INTO user_events SELECT * …` / sütunsüz yazım | Sütun listesi + prepared (ADR-002) |
| 2 | PHP event adını (`user.logged_in`) doğrudan `log_security.event_type` enum'una yazmak (enum'da yok → hataya/düşmeye) | Doğru tabloyu seç: `user_events` (varchar) vs `log_security` (enum) — eşleme tablosu ADR |
| 3 | `event_data` string birleştirme (`json_encode` + interpolation) | Parametreli JSON (`event_data` json kolonu — 438) |
| 4 | Olay tablosuna `SELECT *` ile rapor | Sütun + indeks kullan (`idx_ue_type` 445, `idx_realtime_events_type_created` 121) |
| 5 | ENUM'u PHP'de ham string çoğaltmak | Tek noktada sabit (Data Engineer sahibi) |

---

## §4 — CSS (attribute köprüsü)

### §4.1 — Mod override (GERÇEK)

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-color-mode-tokens.css · Satır: 141-143 */
/* html[data-mode="light"] = en yüksek öncelik */
html[data-mode="light"] {
    /* … token override … */
}
```

```css
/* Dosya: a-color-mode-tokens.css · Satır: 365-374 (özet) */
/* html[data-mode="dark"] = explicit dark */
html[data-mode="dark"] { /* … */ }
```

```css
/* Dosya: a-color-mode-tokens.css · Satır: 253-259 (özet) */
html[data-gender="female"] { /* … */ }
```

### §4.2 — JS'in attribute yazımı (GERÇEK — köprünün diğer ucu)

```js
// Dosya: assets.coremusic.net/js/managers/ThemeManager.js · Satır: 87
document.documentElement.setAttribute('data-gender', this.#currentTheme);
// Dosya: ThemeManager.js · Satır: 166
document.documentElement.setAttribute('data-mode', this.#currentMode);
// Dosya: ThemeManager.js · Satır: 164, 246
document.documentElement.removeAttribute('data-mode');
```

```js
// Dosya: assets.coremusic.net/js/managers/ScaleManager.js · Satır: 381-383
root.setAttribute('data-scale-tier', tier);
root.setAttribute('data-dpr', dpr);
root.setAttribute('data-aspect', aspect);
```

### §4.3 — Diğer token dosyaları (GERÇEK — paralel kümeler)

```css
/* Dosya: Css/01_Abstracts/a-light-glass-tokens.css · Satır: 120, 133, 146 */
[data-gender="female"] { /* … */ }
[data-gender="male"]   { /* … */ }
[data-gender="neutral"]{ /* … */ }
```

```css
/* Dosya: Css/01_Abstracts/a-login-tokens.css · Satır: 176, 184, 192 */
[data-gender="female"] { /* … */ }
[data-gender="male"]   { /* … */ }
[data-gender="neutral"]{ /* … */ }
```

> **Köprü özeti:** JS olay/manager kararı → `setAttribute` (ThemeManager.js:87,166 ·
> ScaleManager.js:381-383) → CSS `[data-*]` cascade (3 token dosyası). CSS'te JS event
> **tanımı yoktur**; CSS yalnız **tepki** katmanıdır (kapsam §3.4).

### §4.4 — YAPMA ↔ YAP (CSS)

| # | YAPMA | YAP |
|---|-------|-----|
| 1 | CSS'e sabit hex/px yazmak (katman dosyasına) | Token `01_Abstracts` içinde `--cm-*`; katman `var()` okur (kural §10) |
| 2 | JS'de `style.x = …` ile inline theme (CSP uyumsuz) | Attribute yaz + CSS cascade (ThemeManager.js:87 deseni) |
| 3 | `@media` içinde `var()` okumak | Kural gereği yasak (§10 token kuralı) |
| 4 | Yeni `[data-*]` selectörü için JS tarafını unutmak | Attribute yazımı + selectör birlikte eklenir (ScaleManager 381-383 deseni) |
| 5 | `!important` ile override zinciri kırmak | Cascade sırası: `:root` → `[data-gender]` → `html[data-mode]` (a-color-mode-tokens.css:21-23 dokümantasyonu) |

---

## §5 — Diller arası omurga (bir olay akışının 4 dili)

| Adım | Dil | Dosya · Satır | Ne |
|------|-----|---------------|----|
| 1 (PLANNED) | PHP | `Events/Domain/MusicPlayedEvent.php` · 39 | `eventName() = 'music.played'` — olay nesnesi hazır |
| 2 (PLANNED) | PHP | `Events/EventDispatcher.php` · 28 | `dispatch($event)` — dinleyicilere dağıtım |
| 3 (PLANNED) | SQL | `.ai/.sql/mysql/coremusic_logs.sql` · 433 | `user_events` satırı (handler) |
| 4 (GERÇEK) | JS | `js/main.js` · 71, 96-139 | EventBus kurulur, manager'lara dağıtılır |
| 5 (GERÇEK) | JS | `js/features/PlayerController.js` · 126 | `emit('player:play')` |
| 6 (GERÇEK) | JS | `js/core/EventBus.js` · 57-66 | Dinleyiciler senkron çağrılır |
| 7 (GERÇEK) | CSS | `a-color-mode-tokens.css` · 143 vb. | Attribute tepkisi (tema/ölçek olaylarında) |

> **Okuma:** 1-3 PHP/SQL ucu **hazır ama bağlı değil** (ADR-086 wiring PLANNED);
> 4-7 JS/CSS ucu **canlı**. Bu iki uç bugün birbirini **kesmez** — HTTP bridge
> (auth_key, ADR-043) dışında servisler arası akış yok (kapsam §5.3).

## §9.A — Açık Sorular (spec §8 kaydı)

Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

- §2 YAPMA↔YAP çiftlerinin PHP tarafı production wiring'a taşınacak mı? (ADR-086: listeners PLANNED)
- §8.1 JS `player:progress` (PlayerController.js:87) ve `nav:navigate` (SPARouterAdapter.js:35) emitter'ları repo'ya eklenecek mi? (`nav:complete` dinleyicisinin emitter'ı MEVCUT: NavigationOrchestrator.js:94, SPARouterAdapter.js:113 — bu ikisi hariç 2 `on()` kaydının emitter'ı yok)

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
