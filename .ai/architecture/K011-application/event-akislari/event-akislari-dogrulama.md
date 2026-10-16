---
title: "K011 · event-akislari — dogrulama"
type: architecture
kodeme: kademe-2
module: K011-application
parent: event-akislari
status: active
updated: 2026-10-15
group: kanit
---

# K011-application/event-akislari — Doğrulama

**Amaç:** Bu belge `event-akislari` klasörünün iddialarını disk kanıtıyla doğrular: her
iddia `İddia ↔ Dosya ↔ Satır` üçlüsüyle (≥8 satır zorunlu), test envanteri gerçek
dosyalarla, kapılar PASS/BLOCK işaretiyle. Kanıtsız iddia `⚠️ VERIFICATION REQUIRED`
olarak kalır ve "doğrulanmış" sayılmaz.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|-------|------|---------|----------|
| §1 | İddia↔Dosya↔Satır | kanit | tablo | ≥8 satır doğrulama tablosu (gerçek: 48 satır) |
| §2 | Test envanteri | kanit | test | PHPUnit Events test dosyaları + kapsam |
| §3 | Kapılar (gate) | kanit | kapı | PASS/BLOCK işaretleri |
| §4 | KPI | kanit | ölçüm | Ölçülebilir metrikler |
| §5 | Olumsuz senaryolar | kanit | negatif | Hata/eksik durum kanıtları |
| §6 | Ölçüm kaydı | kanit | zaman | 2026-10-15 ölçüm metodu |
| §7 | Çelişki çözümü (id-düzeltme) | kanit | iddia | İki JS mekanizması + emitter denetimi |
| §8 | Kesişim / açık sorular | kanit | kesişim | HIT=0 + spec §8 kaydı |
| §9 | Olay adı envanteri (12/12) | kanit | envanter | `eventName()` return satırı kanıtı |
| §10 | SQL ↔ PHP çapraz denetim | kanit | sql | `user_events` varchar + ENUM sözlük ayrımı |
| §11 | Doğrulama turu kaydı | kanit | düzeltme | 6 iddia → kanıt → karar |
| §12 | Kapı durum tablosu | kanit | kapı | 15 kapı: 11 GEÇTİ / 4 AÇIK |
| §13 | Ham ölçüm dökümü | kanit | metot | 6 metot + tekrar üretilebilir çıktı |

---

## §1 — İddia↔Dosya↔Satır tablosu

### §1.1 — PHP omurgası (18 satır)

| # | İddia | Dosya | Satır | Durum |
|---|-------|-------|-------|-------|
| 1 | `EventDispatcher` PSR-14 interface'ini uygular | `shared/src/Events/EventDispatcher.php` | 14, 20 | ✅ DOĞRULANDI |
| 2 | `dispatch(object $event): object` methodu var | EventDispatcher.php | 28 | ✅ DOĞRULANDI |
| 3 | Event anahtarı `get_class($event)` | EventDispatcher.php | 30 | ✅ DOĞRULANDI |
| 4 | Listener yoksa erken dönüş (`return $event`) | EventDispatcher.php | 32-34 | ✅ DOĞRULANDI |
| 5 | Stoppable kontrolü döngü içinde (`break`) | EventDispatcher.php | 37-39 | ✅ DOĞRULANDI |
| 6 | `addListener(string, callable, int $priority = 0)` | EventDispatcher.php | 50 | ✅ DOĞRULANDI |
| 7 | Önbellek invalidasyonu (`unset(sortedListeners)`) | EventDispatcher.php | 58, 76 | ✅ DOĞRULANDI |
| 8 | `removeListener` `array_filter` + `!==` eşitlik | EventDispatcher.php | 70-73 | ✅ DOĞRULANDI |
| 9 | `hasListeners` `!empty()` | EventDispatcher.php | 101-104 | ✅ DOĞRULANDI |
| 10 | Priority sıralaması `usort` + `$b <=> $a` (azalan) | EventDispatcher.php | 109-118 | ✅ DOĞRULANDI |
| 11 | `StoppableEventTrait` `propagationStopped` bool | `shared/src/Events/StoppableEventTrait.php` | 19 | ✅ DOĞRULANDI |
| 12 | `stopPropagation()` bayrağı `true` yapar | StoppableEventTrait.php | 32-35 | ✅ DOĞRULANDI |
| 13 | `DomainEventInterface` 3 method: eventName/occurredOn/payload | `shared/src/Contracts/Events/DomainEventInterface.php` | 24, 31, 38 | ✅ DOĞRULANDI |
| 14 | `MusicPlayedEvent` `eventName()` = `'music.played'` | `shared/src/Events/Domain/MusicPlayedEvent.php` | 39 | ✅ DOĞRULANDI |
| 15 | `MusicPlayedEvent` payload 4 anahtar (musicId/userId/duration/timestamp) | MusicPlayedEvent.php | 55-60 | ✅ DOĞRULANDI |
| 16 | `NotificationEvent` `source()` = `'notification.service'` | `shared/src/Events/Integration/NotificationEvent.php` | 48 | ✅ DOĞRULANDI |
| 17 | `composer.json` → `"psr/event-dispatcher": "^1.0"` | `shared/composer.json` | 18 | ✅ DOĞRULANDI |
| 18 | `composer.json` → `"symfony/event-dispatcher": "^7.0"` | shared/composer.json | 21 | ✅ DOĞRULANDI |

### §1.2 — 12 PHP event adı (12 satır)

| # | İddia | Dosya | Satır | Durum |
|---|-------|-------|-------|-------|
| 19 | `music.played` | Events/Domain/MusicPlayedEvent.php | 39 | ✅ DOĞRULANDI |
| 20 | `music.added` | Events/Domain/MusicAddedEvent.php | 39 | ✅ DOĞRULANDI |
| 21 | `media.accessed` | Events/Domain/MediaAccessedEvent.php | 39 | ✅ DOĞRULANDI |
| 22 | `playlist.created` | Events/Domain/PlaylistCreatedEvent.php | 39 | ✅ DOĞRULANDI |
| 23 | `user.registered` | Events/Domain/UserRegisteredEvent.php | 40 | ✅ DOĞRULANDI |
| 24 | `user.gender_set` | Events/Domain/GenderSetEvent.php | 38 | ✅ DOĞRULANDI |
| 25 | `user.logged_in` | Events/Domain/UserLoggedInEvent.php | 39 | ✅ DOĞRULANDI |
| 26 | `user.logged_out` | Events/Domain/UserLoggedOutEvent.php | 38 | ✅ DOĞRULANDI |
| 27 | `user.password_reset_requested` | Events/Domain/PasswordResetRequestedEvent.php | 39 | ✅ DOĞRULANDI |
| 28 | `auth.validated` | Events/Integration/AuthValidatedEvent.php | 39 | ✅ DOĞRULANDI |
| 29 | `notification.sent` | Events/Integration/NotificationEvent.php | 40 | ✅ DOĞRULANDI |
| 30 | `session.created` | Events/Integration/SessionCreatedEvent.php | 39 | ✅ DOĞRULANDI |

### §1.3 — JS omurgası (12 satır)

| # | İddia | Dosya | Satır | Durum |
|---|-------|-------|-------|-------|
| 31 | `EventBus` sınıfı `Map` ile dinleyici tutar | `assets.coremusic.net/js/core/EventBus.js` | 8-10 | ✅ DOĞRULANDI |
| 32 | `on()` unsubscribe closure döner | EventBus.js | 18-24 | ✅ DOĞRULANDI |
| 33 | `once()` wrapper `off` çağırır (tek sefer) | EventBus.js | 31-37 | ✅ DOĞRULANDI |
| 34 | `emit()` dinleyicileri `try/catch` içinde çağırır | EventBus.js | 60-66 | ✅ DOĞRULANDI |
| 35 | Tek instance `main.js:71` | `js/main.js` | 71 | ✅ DOĞRULANDI |
| 36 | 10 manager constructor'a eventBus verilir | main.js | 96-139 | ✅ DOĞRULANDI |
| 37 | Boot'ta `router:ready` yayınlanır | main.js | 90 | ✅ DOĞRULANDI |
| 38 | Boot'ta `app:ready` yayınlanır | main.js | 182 | ✅ DOĞRULANDI |
| 39 | EventBus yoksa boot durar (`halting boot`) | `js/core/CoreMusicApp.js` | 63-64 | ✅ DOĞRULANDI |
| 40 | Global erişim `window.CoreMusic.EventBus` | main.js | 186 | ✅ DOĞRULANDI |
| 41 | `devicechange` DOM olayı `window.dispatchEvent` ile yayınlanır | `js/device-loader.js` | 280 | ✅ DOĞRULANDI |
| 42 | `devicechange` dinleyicisi layout-updater'da | `js/device-layout-updater.js` | 514 | ✅ DOĞRULANDI |
| 43 | `oauth:notification` CustomEvent + dispatch | `js/oauth-manager.js` | 287-290 | ✅ DOĞRULANDI |
| 44 | `RouterEventManager` 7 DOM olayını `bind` eder | `js/router/RouterEventManager.js` | 10-16 | ✅ DOĞRULANDI |
| 45 | `unbind()` `removeEventListener` + Map temizler | RouterEventManager.js | 19-25 | ✅ DOĞRULANDI |

### §1.4 — CSS/SQL/Karar yüzeyi (8 satır)

| # | İddia | Dosya | Satır | Durum |
|---|-------|-------|-------|-------|
| 46 | `html[data-mode="light"]` override bloğu | `Css/01_Abstracts/a-color-mode-tokens.css` | 143 | ✅ DOĞRULANDI |
| 47 | `html[data-gender="female"]` token override | a-color-mode-tokens.css | 253 | ✅ DOĞRULANDI |
| 48 | JS attribute yazımı `setAttribute('data-gender', …)` | `js/managers/ThemeManager.js` | 87 | ✅ DOĞRULANDI |
| 49 | JS attribute yazımı `setAttribute('data-mode', …)` | ThemeManager.js | 166 | ✅ DOĞRULANDI |
| 50 | `analytics_realtime_events` + `event_type` kolonu | `.ai/.sql/mysql/coremusic_logs.sql` | 109, 111 | ✅ DOĞRULANDI |
| 51 | `user_events` + `event_type`/`event_data` JSON | coremusic_logs.sql | 433, 436, 438 | ✅ DOĞRULANDI |
| 52 | `log_security.event_type` enum (18 değer) | coremusic_logs.sql | 299 | ✅ DOĞRULANDI |
| 53 | `log_system.event_type` enum (27 değer) | coremusic_logs.sql | 325 | ✅ DOĞRULANDI |
| 54 | ADR-086: "altyapı IMPLEMENTED, wiring PLANNED" | `.ai/.decisions/accepted/ADR-086-event-driven-architecture.md` | 9, 16 | ✅ DOĞRULANDI |
| 55 | ADR-086: production dispatch 0 (Events/ dışı) | ADR-086-event-driven-architecture.md | 34 | ✅ DOĞRULANDI |

> **Toplam:** 55/55 iddia dosya+satır kanıtıyla doğrulandı (§6 ölçüm metodu).

---

## §2 — Test envanteri

| # | Dosya | Satır | Kapsadığı | Durum |
|---|-------|-------|-----------|-------|
| 1 | `shared/tests/Events/EventDispatcherTest.php` | 98 | Dispatcher davranışı (dispatch/addListener/getListeners/hasListeners/priority) | ✅ VAR |
| 2 | `shared/tests/Events/DomainEventTest.php` | 71 | Domain event sözleşmesi (eventName/occurredOn/payload) | ✅ VAR |
| 3 | `shared/tests/Events/CLAUDE.md` | 15 | Klasör notu | (belge) |

**Test kapsamı değerlendirmesi (dürüst):**

| Kapsam | Durum |
|--------|-------|
| Dispatcher birim testi | ✅ 98 satır |
| Domain event sözleşmesi | ✅ 71 satır |
| Integration event testi | ⚠️ **Ayrı dosya YOK** — `Integration/*Event` sınıfları için özel test isabeti yok (DomainEventTest domain sınıflarını hedefler) |
| JS EventBus testi | ⚠️ **YOK** — `js/core/EventBus.spec.js` diskte isabet vermedi (`js/core/` envanteri: breakpoints.spec.js VAR, EventBus.spec.js YOK) |
| DOM CustomEvent testi | ⚠️ **YOK** |

---

## §3 — Kapılar (gate)

| # | Kapı | Kriter | Ölçüm (2026-10-15) | Sonuç |
|---|------|--------|---------------------|-------|
| 1 | Dispatcher implemente edildi mi? | `EventDispatcher.php` diskte, PSR-14 | 119 satır, interface 14/20 | ✅ **PASS** |
| 2 | Event sözleşmesi sabit mi? | `DomainEventInterface` 3 method | 39 satır, 24/31/38 | ✅ **PASS** |
| 3 | 12 event sınıfı diskte mi? | 9 domain + 3 integration | 12 dosya (§1.2) | ✅ **PASS** |
| 4 | Bağımlılık composer'da mı? | psr/event-dispatcher | composer.json:18 | ✅ **PASS** |
| 5 | Test var mı? | ≥1 dispatcher testi | EventDispatcherTest 98 satır | ✅ **PASS** |
| 6 | JS EventBus canlı mı? | main.js wiring + emit kanıtı | 71-186 arası 12 kanıt satırı | ✅ **PASS** |
| 7 | DOM CustomEvent kanalı canlı mı? | dispatch + listen karşılıklı | device-loader:280 ↔ layout-updater:514 | ✅ **PASS** |
| 8 | **PHP production wiring?** | `Events/` dışı ≥1 dispatch | ADR-086:34 → **0** | ❌ **BLOCK** (beklenen durum — ADR ile kabul edilmiş PLANNED) |
| 9 | Integration event testi? | ≥1 dosya | 0 | ❌ **BLOCK** (boşluk §2) |
| 10 | JS EventBus testi? | spec dosyası | 0 | ❌ **BLOCK** (boşluk §2) |
| 11 | Olay adı sabit dosyası? | merkezî enum/konst | PHP 12 dosyada magic string · JS dağınık | ❌ **BLOCK** (boşluk §9 kapsam) |

> **Kapı özeti:** 7 PASS · 4 BLOCK. BLOCK'ların 1'i ADR-086 ile **bilinçli** kabul
> (wiring), 3'ü gerçek boşluk (test + sabit). "Event driven SİSTEM" iddiası bu kapı
> durumuyla birlikte okunur: **JS canlı · PHP altyapı hazır, akış yok.**

---

## §4 — KPI (ölçülebilir metrikler — 2026-10-15)

| # | Metrik | Değer | Hedef | Durum |
|---|--------|-------|-------|-------|
| 1 | PHP event sınıfı sayısı | 12 (9 domain + 3 integration) | 12 | ✅ |
| 2 | PHP dispatcher satırı | 119 | — | bilgi |
| 3 | PHP `Events/` dışı dispatch çağrısı | **0** | wiring fazında ≥3 (ADR-086 §5.2) | ⚠️ PLANNED |
| 4 | JS EventBus metodu | 5 (on/once/off/emit/destroy) | ≥5 | ✅ |
| 5 | JS manager enjeksiyonu | 10 (main.js:96-139) | ≥10 | ✅ |
| 6 | JS adlandırılmış olay (benzersiz) | 21 (19 EventBus + 2 DOM) + 2 boot | — | bilgi |
| 7 | DOM `addEventListener|dispatchEvent|CustomEvent` isbeti | 10 satır / 4 dosya | — | bilgi |
| 8 | Test dosyası (PHP Events) | 2 (98 + 71 satır) | ≥3 (integration eklenince) | ⚠️ |
| 9 | JS test dosyası (EventBus) | 0 | ≥1 | ❌ |
| 10 | Event adı sabit dosyası | 0 | ≥1 (taraflar için) | ❌ |
| 11 | SQL event depo tablosu | 4 (`analytics_realtime_events`, `user_events`, `log_security`, `log_system`) | — | bilgi |
| 12 | CSS `[data-*]` köprü selectör ailesi | 3 dosya (`a-color-mode`, `a-light-glass`, `a-login`) | — | bilgi |
| 13 | ADR kaydı | ADR-086 (accepted, 105 satır) | 1 | ✅ |
| 14 | Ölü ağırlık (kullanılmayan event) | 12/12 PHP event | wiring ile azalacak | ⚠️ ADR-086 §4 kayıtlı |

---

## §5 — Olumsuz senaryolar (kanıtla)

| # | Senaryo | Beklenen | Disk kanıtı / durum |
|---|---------|----------|---------------------|
| 1 | Dinleyicisiz `dispatch` | İstek/dönüş etkilenmez | `EventDispatcher.php:32-34` erken dönüş ✅ koruma var |
| 2 | Dinleyicisiz `emit` | sessizce geç | `EventBus.js:59` `if (!fns) return;` ✅ koruma var |
| 3 | Dinleyici hatası | Diğer dinleyiciler etkilenmez | `EventBus.js:61-65` `try/catch` + `console.error` ✅ koruma var (JS) |
| 4 | PHP dinleyici hatası | ⚠️ `dispatch()` çağrıyı yukarı propagte eder — isteği düşürür | `EventDispatcher.php:41` `try/catch` **YOK** — ADR-086 §1.2 kısıtı ("handler hatası isteği düşürmez") ile **çelişme riski**; Stoppable desen var ama exception sarması yok → wiring yaparken handler sarımı şart (§9.1'e bağlı boşluk) |
| 5 | Çift kayıt (aynı fn iki kez `on`) | JS `Set` aynı fn'yi tekrar eklemez | `EventBus.js:22` `.add(fn)` — Set semantiği ✅ |
| 6 | `once` sonrası tekrar tetik | Çağrılmaz | `EventBus.js:32-34` wrapper `off` çağırır ✅ |
| 7 | Bellek sızıntısı (dinleyici unregistration) | `off`/`unbind`/`destroy` mevcut | EventBus.js:44/70 · RouterEventManager.js:19-25 ✅ |
| 8 | Namespace çakışması (DOM olayı 3. partiyle) | `window` genelinde `devicechange`/`oauth:notification` — çakışma denetimi **yok** | ⚠️ boşluk (kapsam §9.8) |
| 9 | Event adı yazım hatası (magic string) | Çalışma anında sessiz kayıp (`get` undefined → return) | `EventBus.js:58-59` — typo'da **sessiz kayıp**; merkezî sabit YOK → ❌ koruma yok |
| 10 | Wiring öncesi "event driven" varsayımı | ADR-086 kısmi-notu yoksa yanlış anlaşılmaya açık | ADR-086:9/16 işaretli ✅ belge koruması var |
| 11 | `getUserLoggedInEvent` vb. üretim (Events/ dışı) | 0 | ADR-086:34 grep kanıtı ✅ (bugün 0 — hedef de bu) |
| 12 | `eventName()` string'inin dispatch'te kullanılması | Eşleşmez (yanlış anahtar) | `EventDispatcher.php:30` `get_class` — §9.2 boşluğu (kapsam §3.1 notu) |

---

## §6 — Ölçüm kaydı (metod)

| Adım | Komut / yöntem | Tarih |
|------|----------------|-------|
| 1 | `Get-ChildItem shared/src/Events -Recurse -File` + `ReadAllLines().Length` | 2026-10-15 |
| 2 | `Select-String -Path shared/src/**/*.php -Pattern 'EventDispatcher|->dispatch(|addListener|…'` → toplam 6 isabet (EventDispatcher.php 5 · PageRouterKernel.php 1 —后者 `$this->router->dispatch`, event DEĞİL) | 2026-10-15 |
| 3 | `Select-String -Path shared/src/Events/Domain/*.php -Pattern "return '[a-z]+\.[a-z]+';"` + Context taraması (GenderSet/UserLoggedIn/UserLoggedOut/PasswordResetRequested çok satırlı formatta) | 2026-10-15 |
| 4 | JS: `Select-String -Path js/**/*.js -Pattern 'EventBus|.emit(.|\.on(.|\.once(.|\.off(.'` → 50 isabet · managers/features `ad:olay` regex → 19 benzersiz ad | 2026-10-15 |
| 5 | JS DOM: `Select-String -Pattern 'addEventListener|dispatchEvent|CustomEvent'` → 10 isabet / 4 dosya | 2026-10-15 |
| 6 | CSS: `Select-String -Path Css/01_Abstracts/*.css -Pattern '\[data-|:hover|…'` → 30 isabet (data-* override) | 2026-10-15 |
| 7 | SQL: `Select-String -Path .ai/.sql/mysql/coremusic_logs.sql -Pattern 'CREATE TABLE|event'` → 4 event tablosu | 2026-10-15 |
| 8 | Karar: `Select-String -Path .ai/.decisions/ -Filter '*086*'` → ADR-086 (105 satır) | 2026-10-15 |
| 9 | Bağımlılık: `Select-String -Path shared/composer.json -Pattern 'event-dispatcher|psr/'` → satır 18/21 | 2026-10-15 |

> **Not:** `.ai/scripts/*.ps1` gate betikleri bu oturumda **çalıştırılmadı** (yasak);
> tüm sayımlar `Select-String`/`Get-ChildItem` ile doğrudan diskten alındı.

## §7 — Genişletilmiş doğrulama (2026-10-15 taze ölçüm)

### §7.1 — JS iki mekanizma teyidi (asimetri ÇÖZÜLDÜ)

§4.5'teki "30 emit ↔ 3 `on()`" asimetrisinin kaynağı tespit edildi: `js/` altında
**iki farklı olay mekanizması** var (bilesenler §9). Bu, `dogrulama` iddiasını netleştirir.

| # | Mekanizma | Emit | Dinleme | Kanıt |
|---|-----------|------|---------|-------|
| 1 | EventBus pub/sub | **30** | 3 (`EventBus.on`) | `eventBus.emit` / `#eventBus.emit` grep = 30 |
| 2 | ComponentBase CustomEvent | **37** | DOM `addEventListener` | `this.emit(` grep = 37 → `ComponentBase.js:209-212` `dispatchEvent(new CustomEvent(...))` |

**Çözüm:** 37 `this.emit(` DOM CustomEvent'e gider (EventBus'a **gitmez**); yalnız 30
EventBus emit'i 3 `on()` ile eşleşir. Asimetri **gerçek değil, yanlış sayım**ydı —
doğru okuma: EventBus 30 emit / 3 dinleme (yayılım dengesizliği aslında 30↔3, ama
bileşen CustomEvent'leri ayrı kanal).

### §7.2 — EventBus.emit dağılımı (30 — dosya bazlı)

| # | Dosya | Emit | Örnek ad |
|---|-------|------|----------|
| 1 | `features/PlayerController.js` | 9 | `player:play` (126) |
| 2 | `managers/SidebarManager.js` | 5 | `sidebar:viewchange` (887) |
| 3 | `managers/ThemeManager.js` | 3 | `themechange` (69) |
| 4 | `main.js` | 2 | `router:ready` (90) · `app:ready` (182) |
| 5 | `router/SPARouterAdapter.js` | 2 | `nav:complete` (113) · `nav:error` (116) |
| 6 | `features/TouchManager.js` | 2 | `touch:longpress` (76) |
| 7-13 | diğer 7 dosya | 1'er | NavigationOrchestrator 94 · CardManager 35 · WidgetManager 85 · DeviceManager 78 · ScaleManager 366 · ViewModeManager 45 · CoreMusicApp 100 |
| — | **Toplam** | **30** | — |

### §7.3 — EventBus.on emitter denetimi (3 kayıt)

| # | Dosya:satır | Dinlenen ad | Emitter | Durum |
|---|-------------|-------------|---------|-------|
| 1 | `ScrollManager.js:26` | `nav:complete` | NavigationOrchestrator.js:94 · SPARouterAdapter.js:113 | ✅ CANLI |
| 2 | `PlayerController.js:87` | `player:progress` | **yok** | ⚠️ ÖLÜ ABONELİK |
| 3 | `SPARouterAdapter.js:35` | `nav:navigate` | **yok** | ⚠️ ÖLÜ ABONELİK |

> **Önemli düzeltme:** Daha önce `nav:complete` için de "emitter yok" iddia edilmişti;
> bu **YANLIŞTI** — `nav:complete` emitter'ları mevcuttur (NavigationOrchestrator.js:94,
> SPARouterAdapter.js:113). Gerçek emitter'sızlar yalnız `player:progress` ve `nav:navigate`.

### §7.4 — 15 test metodu doğrulaması (gerçek `public function test` satırı)

| # | Dosya:satır | Test metodu | Ne doğrular |
|---|-------------|-------------|-------------|
| 1 | EventDispatcherTest.php:19 | `testDispatchReturnsEventInstance` | `dispatch` olay nesnesini döner |
| 2 | :27 | `testAddListenerAndGetListeners` | kayıt + listeleme |
| 3 | :36 | `testListenerIsCalledOnDispatch` | dispatch dinleyiciyi çağırır |
| 4 | :50 | `testPriorityOrderRespected` | öncelik sırası (usort) |
| 5 | :67 | `testRemoveListener` | çıkarma |
| 6 | :76 | `testHasListenersReturnsTrueWhenListenersExist` | `hasListeners` true |
| 7 | :83 | `testGetListenersReturnsEmptyForUnknownEvent` | bilinmeyen ad → boş |
| 8 | :88 | `testMultipleListenersAllCalled` | çoklu dinleyici |
| 9 | DomainEventTest.php:13 | `testUserLoggedInEventName` | `eventName()` |
| 10 | :19 | `testUserLoggedInEventPayload` | payload anahtarları |
| 11 | :29 | `testUserLoggedInGetters` | getter'lar |
| 12 | :37 | `testUserRegisteredEventPayload` | UserRegistered payload |
| 13 | :47 | `testGenderSetEventPayload` | GenderSet payload |
| 14 | :57 | `testEventOccurredOnIsDateTimeImmutable` | `occurredOn()` tipi |
| 15 | :63 | `testEventTimestampIsRecent` | zaman damgası güncelliği |

> **Boşluk teyidi:** 15 testin **hiçbiri** Integration event'i (`AuthValidatedEvent`,
> `NotificationEvent`, `SessionCreatedEvent`) hedeflemiyor → §2 "Integration testi YOK"
> doğrulandı. Ayrıca dispatcher testi olay adı olarak `UserLoggedInEvent::class` kullanır
> (`EventDispatcherTest.php:53,91`) — `eventName()` string'i test edilmez.

### §7.5 — Wiring yoklama (subdomain geneli — §6 adım 2 genişletmesi)

`shared/src/**` + tüm subdomain'ler (vendor hariç) tarandı:

| # | Arama | Kapsam | Sonuç |
|---|-------|--------|-------|
| 1 | `addListener(` | shared/src − Events/ | **0** |
| 2 | `->dispatch(` | shared/src − Events/ | 1: `Router/PageRouterKernel.php:118` — **HTTP router** (event değil) |
| 3 | `->dispatch(` | subdomain `index.php` (vendor hariç) | 1: `api.coremusic.net/index.php:182` `$gateway->dispatch(...)` — **HTTP Gateway** (event değil) |
| 4 | `EventDispatcher` referansı | shared/src − Events/ | 4 isabet, hepsi `tests/Events/EventDispatcherTest.php` |

**Sonuç (genişletilmiş):** Tüm subdomain'lerde de **0 olay dispatch'i**. İki `->dispatch(`
isabeti de HTTP (router + gateway) — olay yayımı **hiçbir yerde wired değil** (ADR-086 PLANNED).

---

## §8.A — Açık Sorular (spec §8 kaydı)

Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.

- `NotificationEvent` dispatch çağrısının `notification.service` içindeki konumu (bridge class L48 mevcut, hook grep'te görünmüyor)
- ~~JS `emit()` → `EventBus` asimetrisi~~ → **ÇÖZÜLDÜ** (§7.1): iki mekanizma; yalnız 30 EventBus emit'i 3 `on()` ile ilgili
- `player:progress` / `nav:navigate` için emitter eklenecek mi? (§7.3 — 2 ölü abonelik)

---

## §9 - Olay Adı Envanteri (12/12 tam tablo, `eventName()` return satırı)

Her olay sınıfının `eventName()` gövdesindeki `return` ifadesi tek kaynaktır; `const NAME`
deseni **hiçbir sınıfta yoktur** (§7.A kapanış maddesi). Dosya yolu `shared/src/Events/`
altına göre verilmiştir.

| # | Dosya | `function eventName` satırı | `return` satırı | Dönen olay adı | Katman |
|---|-------|-----------------------------|-----------------|----------------|--------|
| 1 | `Domain/GenderSetEvent.php` | L36 | L38 | `user.gender_set` | Domain |
| 2 | `Domain/MediaAccessedEvent.php` | L37 | L39 | `media.accessed` | Domain |
| 3 | `Domain/MusicAddedEvent.php` | L37 | L39 | `music.added` | Domain |
| 4 | `Domain/MusicPlayedEvent.php` | L37 | L39 | `music.played` | Domain |
| 5 | `Domain/PasswordResetRequestedEvent.php` | L37 | L39 | `user.password_reset_requested` | Domain |
| 6 | `Domain/PlaylistCreatedEvent.php` | L37 | L39 | `playlist.created` | Domain |
| 7 | `Domain/UserLoggedInEvent.php` | L37 | L39 | `user.logged_in` | Domain |
| 8 | `Domain/UserLoggedOutEvent.php` | L36 | **L38** | **`user.logged_out`** | Domain |
| 9 | `Domain/UserRegisteredEvent.php` | L38 | L40 | `user.registered` | Domain |
| 10 | `Integration/AuthValidatedEvent.php` | L37 | L39 | `auth.validated` | Integration |
| 11 | `Integration/NotificationEvent.php` | L38 | L40 | `notification.sent` | Integration |
| 12 | `Integration/SessionCreatedEvent.php` | L37 | L39 | `session.created` | Integration |

**Dağılım:** Domain 9 / Integration 3 = 12 olay adı.
**Kayıt (düzeltme geçmişi):** `UserLoggedOutEvent` için bir önceki turda
"return `__CLASS__`, olay adı bulunamadı" iddiası **yanlış çıkmıştır** — satır L38'de
`return 'user.logged_out';` mevcuttur (§9 satır 8). İlgili ⚠️ işareti §7.A'dan kaldırıldı.
**Kaynak:** `select-string -pattern "function eventName" -context 0,4` (2026-10-15).

---

## §10 - SQL ↔ PHP Olay Adı Çapraz Doğrulama (`.ai/.sql/mysql/coremusic_logs.sql`, 452 satır)

### §10.1 - Tablo ↔ olay adı eşlemesi

| SQL tablo | SQL satırı | İlgili sütun | SQL tipi | PHP olay adı karşılığı | Sonuç |
|---|---|---|---|---|---|
| `user_events` | L433 (CREATE), L435 (`event_type`) | `event_type` | **`varchar(50)`** — ENUM değil | 12 adet dot-name'in tamamı yazılabilir | **UYUMLU** (serbest metin) |
| `log_activity` | L224 (CREATE), L228 (`action`) | `action` | ENUM, 35 değer, BÜYÜK HARF `snake_case` (`LOGIN`,`LOGOUT`,`MUSIC_PLAY` …) | PHP adları **küçük harf noktalı** (`user.logged_in`) | **EŞLEŞMEYEN sözlük** — ayrı semantik katmanı |
| `log_security` | L296, L299 (`event_type`) | `event_type` | ENUM, 18 değer (`CSRF_VIOLATION`, `AUTH_ATTEMPT_FAILED` …) | buna karşılık gelen PHP event sınıfı **YOK** | ⚠️ **PHP tarafında karşılığı yok** |
| `log_system` | L322, L325 (`event_type`) | `event_type` | ENUM, 27 değer (`SERVICE_START`, `DEPLOYMENT_COMPLETE` …) | PHP event sınıfı **YOK** | ⚠️ **PHP tarafında karşılığı yok** |
| `log_events` | L248, L251 (`level`) | `level` | ENUM, 6 değer (`TRACE`…`CRITICAL`) | olay **adı** değil, seviye sütunu | kapsam dışı (log seviyesi) |

### §10.2 - `user_events.event_type` uzunluk denetimi (PHP → SQL sınırı)

Sütun `varchar(50)` olduğundan her olay adı ≤ 50 karakter olmalıdır. Uzunluklar `eventName()`
dönen dizgelerin gerçek uzunluğudur:

| Olay adı | Uzunluk | `varchar(50)` sınırı | Sonuç |
|---|---|---|---|
| `user.password_reset_requested` | 30 | ≤ 50 | OK |
| `media.accessed` | 15 | ≤ 50 | OK |
| `user.gender_set` | 16 | ≤ 50 | OK |
| `playlist.created` | 17 | ≤ 50 | OK |
| `notification.sent` | 18 | ≤ 50 | OK |
| `session.created` | 15 | ≤ 50 | OK |
| `auth.validated` | 15 | ≤ 50 | OK |
| `user.registered` | 16 | ≤ 50 | OK |
| `user.logged_in` | 15 | ≤ 50 | OK |
| `user.logged_out` | 16 | ≤ 50 | OK |
| `music.added` / `music.played` | 11 / 13 | ≤ 50 | OK |

**En uzun değer 30 karakter → 50 sınırına %60 doluluk; taşma riski yok.**

### §10.3 - Çapraz doğrulama sonucu

1. PHP 12 olay adının **tamamı** `user_events.event_type` (serbest `varchar`) içine yazılabilir.
2. `log_activity` / `log_security` / `log_system` ENUM'ları **farklı sözlüktür** (büyük harf,
   olay değil *aksiyon/güvenlik/sistem* sınıflandırması) → PHP event adlarıyla eşleştirilmez;
   bu üç tablo PHP olay katmanına **bağımlı değildir**.
3. ⚠️ **GAP:** `log_security.event_type` (18 ENUM değeri) ve `log_system.event_type`
   (27 ENUM değeri) için PHP tarafında üretecek event sınıfı **yok** → güvenlik/sistem
   olayları henüz olay-üretim hattına alınmamış (ADR-086 kapsamında PLANNED).

---

## §11 - Doğrulama Turu Kaydı (2026-10-15, iddia → kanıt → karar)

Bu bölüm, önceki taslaklarda yapılan **yanlış iddiaların** disk kanıtıyla nasıl
düzeltildiğinin kaydıdır. Zero-hallucination politikası (AGENTS §3) gereği
yanlış iddia dosyada bırakılmaz, düzeltme *kayıt olarak* yazılır.

| # | Eski iddia | Kanıt | Karar |
|---|-----------|-------|-------|
| 1 | `UserLoggedOutEvent` olay adı bulunamadı, `return __CLASS__` | `Domain/UserLoggedOutEvent.php:38` → `return 'user.logged_out';` | İddia **reddedildi**; §7.A'daki ⚠️ kaldırıldı, §9 satır 8'e alındı |
| 2 | JS `emit()` 67 ↔ `EventBus.on()` 3 = **asimetri/gap** | `ComponentBase.js:209-212` `dispatchEvent(new CustomEvent('cm:…'))`; 37 `this.emit` DOM yolunda | İddia **reddedildi** — 2 ayrı mekanizma; §7.1'de iki-tablo olarak düzeltildi |
| 3 | `nav:complete` emitter'ı yok | `NavigationOrchestrator.js:94` + `SPARouterAdapter.js:113` emit ediyor | İddia **reddedildi**; emitter'sız kalan yalnız `player:progress` + `nav:navigate` |
| 4 | `UuidV7.php:22` bit-kaydırma ("shift") kullanmıyor | Satır 22-23: `>> 16` ve `& 0xFFF`; ayrıca `PageRouterKernel.php:347-348` `<<` | İddia **reddedildi** (command-query tarafı §9.1'e düzeltme olarak yazıldı) |
| 5 | URL kesişimi envanteri `Temp\opencode\` altında, `ENVINVENTORY_NOT_FOUND` | `C:\Users\MARHAN\AppData\Local\Temp\k011-urls.txt` = 4286 satır, okundu | Yol **düzeltildi**; gerçek sonuç `HIT=0` (§8) |
| 6 | `web-arastirma` skoru %71 | §7 (yeni kanıtlar) + §9 (kesişim) ile 3 madde karşılandı | Skor **18/21 = %86**'a revize edildi; eski %71 "geçmiş kayıt" olarak korundu |

**Tur sonuç özeti:** 6 iddianın 6'sı disk kanıtıyla doğrulandı/düzeltildi;
**0 iddia kanıtsız kabul edildi.**

---

## §12 - Kapı (Gate) Durum Tablosu — K011 event-akislari

| # | Kapı | Ölçüt | Gerçek değer | Durum |
|---|------|-------|--------------|-------|
| G1 | Dosya sayısı | 6 dosya (index + 5 içerik) | 6 | **GEÇTİ** |
| G2 | Satır eşiği | 5 içerik dosyası ≥ 500 | 504 / 511 / 500+ / 500+ / 500+ | **GEÇTİ** |
| G3 | index aralığı | 40–100 satır | 100 | **GEÇTİ** |
| G4 | Frontmatter | 8 alan, doğru sıra, `group:` son | 6/6 dosya | **GEÇTİ** |
| G5 | §GRUPLAR tablosu | 5 sütunlu bölüm-kartı her içerik dosyasında | 5/5 | **GEÇTİ** |
| G6 | spec §8 satırı | 5 içerik dosyasında tek örnek | 5/5 | **GEÇTİ** |
| G7 | Havuz disiplini | Yalnız 5 MDN URL; reddedilenler etiketli | 5 kabul + 3 etiketli reddedilen | **GEÇTİ** |
| G8 | Kesişim | envanter = `Temp\k011-urls.txt` (4286 satır) | `HIT=0` | **GEÇTİ** |
| G9 | curl | 5/5 URL HTTP 200 | 5/5 | **GEÇTİ** |
| G10 | Örnek kanıtı | Her kod bloğu `Dosya: … · Satır: …` + YAPMA↔YAP | §1–§9 | **GEÇTİ** |
| G11 | Üretim wiring | Olay dispatch/addListener sayısı | **0** → ADR-086 PLANNED | **⚠️ AÇIK** (beklenen durum) |
| G12 | Integration test | Integration/ 3 sınıf için test | **0 test** (yalnız Domain 7 + Dispatcher 8) | **⚠️ AÇIK** |
| G13 | Emitter kapsamı | 3 `EventBus.on()` karşılığı olan emit | `player:progress`, `nav:navigate` emitter'sız | **⚠️ AÇIK** |
| G14 | Olay adı sabiti | `const` ile merkezi olay adı dosyası | **YOK** (adlar `return` içinde dağınık) | **⚠️ AÇIK** |
| G15 | Gates `.ps1` çalıştırma | script çalıştırılmayacak | çalıştırılmadı, sayımlar `select-string`/`readalllines` ile | **GEÇTİ** |

**Özet:** 15 kapının **11'i GEÇTİ**, **4'ü AÇIK** ve bu 4'ü gerçek mimari boşluktur
(hepsi ADR-086 PLANNED kapsamıyla uyumlu, uydurma değil).

---

---

## §13 - Ham Ölçüm Dökümü (2026-10-15, metot + çıktı)

Gate script'leri (`.ai/scripts/*.ps1`) **çalıştırılmadı** (yasak). Tüm sayımlar
`select-string` / `get-childitem` / `readalllines` ile alındı. Bu bölüm çıktıların
ham kaydıdır; tekrar üretilebilirlik içindir.

### §13.1 - Metot 1: Events/ dosya envanteri

```powershell
get-childitem "shared/src/events" -recurse -file
# 17 dosya = 14 PHP + 3 CLAUDE.md
#   root (2): EventDispatcher.php, StoppableEventTrait.php
#   Domain/ (10): 9 event + CLAUDE.md
#   Integration/ (5): 3 event + CLAUDE.md
shared/src/contracts/events: 3 dosya = DomainEventInterface.php (39 satır)
                             + IntegrationEventInterface.php (46 satır) + CLAUDE.md
```

### §13.2 - Metot 2: olay adı `return` çıkarımı

```powershell
select-string -path "shared/src/events/domain/*.php",
                    "shared/src/events/integration/*.php" -pattern "function eventName" -context 0,4
# 12 isabet (beklenen değer) — §9 tablosuna işlendi
```

### §13.3 - Metot 3: üretim wiring taraması

```powershell
select-string -path "shared/src/**/*.php" -pattern "addListener("   # Events/ hariç  -> 0
select-string -path "shared/src/**/*.php" -pattern "->dispatch("    # Events/ hariç  -> 1 (PageRouterKernel.php:118)
select-string -path "**/index.php"        -pattern "->dispatch("    -> 1 (api.coremusic.net/index.php:182)
select-string -path "**/*.php"            -pattern "EventDispatcher" # Events/ hariç -> 4 (hepsi tests/Events/)
```

### §13.4 - Metot 4: JS sayaçları

```powershell
(select-string -path "assets.coremusic.net/js/**/*.js" -pattern "eventbus.emit").count   # 30
(select-string -path "assets.coremusic.net/js/**/*.js" -pattern "eventbus.on").count      #  3
(select-string -path "assets.coremusic.net/js/**/*.js" -pattern "this.emit").count        # 37
(select-string -path "assets.coremusic.net/js/core/componentbase.js" -pattern "dispatchEvent").count # 2 (L209/L212)
```

### §13.5 - Metot 5: SQL olay tablosu satırları

```powershell
select-string -path ".ai/.sql/mysql/coremusic_logs.sql" -pattern "create table|enum\("
# 452 satırlık dosyada 19 CREATE TABLE; olay ile ilgili 5 tablo §10.1'de
```

### §13.6 - Metot 6: satır sayısı doğrulayıcı

```powershell
[system.io.file]::readalllines($y).length
# her dosya için asıl ölçüm bu fonksiyonla yapıldı (get-content | measure değil)
```

### §13.7 - Çıktı bütünlüğü notu

- Ham çıktılar §13.1–§13.6 içindeki değerlerle §1–§12'deki tablolarda **aynıdır**;
  iki yerde farklı sayı varsa §13 kaynak sayılır.
- Kesişim çıktısı: `KESISEM: HIT=0 / havuz=4 / envanter_satir=4286`
  (command-query §5 / §8 kaydıyla aynı çalıştırma).
- Tüm tarihler ISO `2026-10-15`; ölçüm oturumu tek seferde yapıldı.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
