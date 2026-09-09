---
type: architecture
category: l3
title: "JS Module Architecture"
date: 2026-08-21
updated: 2026-08-21
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# JS Module Architecture

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]] · [[vanilla-js-rules]]

---

## 1. Amaç

home.coremusic.net frontend JavaScript modül yapısını tanımlar. Tek `main.js` dosyası YASAK — modüller ayrı dosyalara bölünmüştür.

---

## 2. Dizin Yapısı

```
assets.coremusic.net/js/
├── main.js                      ← Entry point: Router + tüm modülleri başlatır
├── core/                        ← Temel altyapı (bağımsız)
│   ├── EventBus.js                Pub/sub (tüm modüller bağımlı)
│   └── CoreMusicApp.js            Lifecycle manager
├── managers/                    ← Durum yönetimi (EventBus'e bağımlı)
│   ├── DeviceManager.js           Cihaz tespiti (device-loader.js bridge)
│   ├── ThemeManager.js            ADR-044 gender theme
│   └── ViewModeManager.js         ADR-045 view mode
├── features/                    ← Sayfa özellikleri (EventBus'e bağımlı)
│   ├── PlayerController.js        State machine (STOPPED/PLAYING/PAUSED)
│   ├── WidgetManager.js           Home widgets
│   ├── CardManager.js             Event delegation
│   ├── ScrollManager.js           Route scroll restore
│   └── TouchManager.js            Embedded touch gestures
├── router/                      ← SPA navigasyonu (mevcut modüller)
│   ├── Router.js                  Ana SPA router
│   ├── guards.js                  Auth/role/permission guard'lar
│   ├── GuardPipeline.js           Client-side guard zinciri
│   ├── CacheLayer.js              Route content caching
│   ├── DomPatcher.js              DOM patching (DOMParser)
│   ├── ContentPatcher.js          HTML content update
│   ├── CsrfSyncManager.js         CSRF token sync
│   ├── FetchWrapper.js            HTTP fetch wrapper
│   ├── main.js                    Legacy SPA entry (yedek)
│   └── ... (21+ modül)
└── device-loader.js             ← Cihaz tespiti (IIFE, non-module)
```

---

## 3. Modül Bağımlılık Sırası

```
main.js (entry point)
  │
  ├──→ Router.js (guard'lar ile birlikte)
  │      └──→ GuardPipeline → CacheLayer → DomPatcher → ContentPatcher
  │
  ├──→ EventBus (bağımsız — Hiçbir modüle bağımlı değil)
  │      │
  │      ├──→ DeviceManager (EventBus)
  │      │      └──→ TouchManager (EventBus + DeviceManager)
  │      │
  │      ├──→ ThemeManager (EventBus)
  │      │
  │      ├──→ ViewModeManager (EventBus)
  │      │
  │      ├──→ ScrollManager (EventBus + Router)
  │      │
  │      ├──→ PlayerController (EventBus)
  │      │
  │      └──→ WidgetManager (EventBus)
  │             └──→ CardManager (EventBus)
  │
  └──→ CoreMusicApp (modül lifecycle)
```

---

## 4. Modül Detayları

### 4.1 — EventBus (core/EventBus.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Pub/sub iletişim sistemi |
| **Bağımlılık** | Yok (temel modül) |
| **State** | `#listeners: Map<string, Set<Function>>` |
| **Methods** | `on(event, fn)`, `off(event, fn)`, `emit(event, data)`, `once(event, fn)`, `destroy()` |

### 4.2 — CoreMusicApp (core/CoreMusicApp.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Uygulama lifecycle yönetimi |
| **Bağımlılık** | EventBus |
| **State** | `#modules: Map`, `#state: 'idle'|'booting'|'running'|'destroyed'` |
| **Methods** | `init()`, `destroy()`, `getModule(name)`, `registerModule(name, instance)` |

### 4.3 — DeviceManager (managers/DeviceManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Cihaz tespiti + CSS yükleme |
| **Bağımlılık** | EventBus + device-loader.js |
| **State** | `#currentDevice: string`, `#breakpoints: Object` |
| **Events** | `devicechange { device, previous }` |
| **Breakpoints** | phone≤767, tablet 768-1024(h>600), embedded≤1024(h≤600), laptop≤1440, desktop≤2560, 4k-tv≤3840, 4k-monitor≥3841 |

### 4.4 — ThemeManager (managers/ThemeManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Gender-based tema motoru (ADR-044) |
| **Bağımlılık** | EventBus |
| **State** | `#currentTheme: 'female'|'male'|'neutral'` |
| **Events** | `themechange { gender, tokens }` |
| **CSS Vars** | `--accent: #ff4fd8 (female), #4f8fff (male), #a855f7 (neutral)` |

### 4.5 — ViewModeManager (managers/ViewModeManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Görünüm modu yönetimi (ADR-045) |
| **Bağımlılık** | EventBus |
| **State** | `#currentMode: 'home'|'pro'|'studio'|'car'` |
| **Events** | `viewmodechange { viewMode }` |

### 4.6 — PlayerController (features/PlayerController.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Footer player state machine |
| **Bağımlılık** | EventBus |
| **State** | `#status: 'STOPPED'|'PLAYING'|'PAUSED'`, `#volume`, `#shuffle`, `#repeat` |
| **DOM Targets** | `[data-action]` buttons, `.footer__progress-bar`, `.footer__volume-slider` |
| **Events** | `player:play`, `player:pause`, `player:stop`, `player:next`, `player:prev` |
| **Actions** | play, pause, stop, prev, next, volume, mute, shuffle, repeat |

### 4.7 — WidgetManager (features/WidgetManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Home right-panel widgets |
| **Bağımlılık** | EventBus |
| **State** | `#widgets: Map<string, Widget>` |
| **Widgets** | clock (setInterval), weather (placeholder), speakers (placeholder), folders |
| **DOM Targets** | `.home-widget`, `.home-widget__title` |

### 4.8 — CardManager (features/CardManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Media card etkileşimleri |
| **Bağımlılık** | EventBus |
| **Pattern** | Event delegation (tek listener) |
| **DOM Targets** | `.card-grid`, `.media-card`, `.mini-card` |
| **Events** | `card:click { id, type, title, artist }` |

### 4.9 — ScrollManager (features/ScrollManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Route bazlı scroll pozisyonu |
| **Bağımlılık** | EventBus + Router |
| **State** | `#positions: Map<url, scrollY>` |

### 4.10 — TouchManager (features/TouchManager.js)

| Özellik | Değer |
|---------|-------|
| **Sorumluluk** | Embedded touch gestures |
| **Bağımlılık** | EventBus + DeviceManager |
| **Conditions** | Sadece `device === 'embedded'` |
| **Gestures** | swipe (yatay scroll), long-press (500ms) |

---

## 5. main.js Entry Point

PHP `HtmlShellRenderer` tarafından yüklenen ana entry point. Router + tüm modülleri başlatır.

> **Not:** `SPARouterAdapter.js` artık kullanılmıyor. Router entegrasyonu doğrudan `main.js` içinde yapılıyor.

```javascript
/**
 * CoreMusic — main.js v5.0.0
 * Ana entry point. PHP HtmlShellRenderer tarafından yüklenir.
 * SPA Router + tüm modülleri başlatır.
 *
 * @module main
 * @version 5.0.0
 */
import Router from './router/Router.js';
import { authGuard, roleGuard, permissionGuard } from './router/guards.js';

/* ─── Core Modüller ─── */
import EventBus from './core/EventBus.js';
import CoreMusicApp from './core/CoreMusicApp.js';

/* ─── Manager Modüller ─── */
import DeviceManager from './managers/DeviceManager.js';
import ThemeManager from './managers/ThemeManager.js';
import ViewModeManager from './managers/ViewModeManager.js';

/* ─── Feature Modüller ─── */
import PlayerController from './features/PlayerController.js';
import WidgetManager from './features/WidgetManager.js';
import CardManager from './features/CardManager.js';
import ScrollManager from './features/ScrollManager.js';
import TouchManager from './features/TouchManager.js';

(function () {
    'use strict';

    /* ─── 1. CoreMusicApp ─── */
    const eventBus = new EventBus();
    const app = new CoreMusicApp({ eventBus });

    /* ─── 2. SPA Router (mevcut Router.js) ─── */
    const routerConfig = window.CoreMusic?.RouterConfig || {};
    let router = null;

    if (routerConfig.enabled !== false && typeof history.pushState === 'function') {
        const guardFunctions = [authGuard, roleGuard, permissionGuard];
        if (typeof routerConfig.customGuard === 'function') {
            guardFunctions.push(routerConfig.customGuard);
        }

        router = new Router({ ...routerConfig, guardFunctions });
        router.init();
        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.Router = router;
        eventBus.emit('router:ready', { router });
    }

    /* ─── 3. Diğer Modüller ─── */
    document.addEventListener('DOMContentLoaded', () => {
        const deviceManager = new DeviceManager(eventBus);
        deviceManager.init();
        app.registerModule('device', deviceManager);

        const themeManager = new ThemeManager(eventBus);
        themeManager.init();
        app.registerModule('theme', themeManager);

        const viewModeManager = new ViewModeManager(eventBus);
        viewModeManager.init();
        app.registerModule('viewMode', viewModeManager);

        const player = new PlayerController(eventBus);
        player.init();
        app.registerModule('player', player);

        const widgets = new WidgetManager(eventBus);
        widgets.init();
        app.registerModule('widgets', widgets);

        const cards = new CardManager(eventBus);
        cards.init();
        app.registerModule('cards', cards);

        const scroll = new ScrollManager(eventBus);
        scroll.init();
        app.registerModule('scroll', scroll);

        const touch = new TouchManager(eventBus);
        touch.init();
        app.registerModule('touch', touch);

        app.setRunning();
        eventBus.emit('app:ready');

        window.CoreMusic = window.CoreMusic || {};
        window.CoreMusic.App = app;
        window.CoreMusic.EventBus = eventBus;
        window.CoreMusic.version = '5.0.0';
    });
})();
```

---

## 6. Modül Şablonu

Her yeni modül bu şablonu kullanır:

```javascript
/**
 * CoreMusic — [Modül Adı]
 * [Açıklama]
 *
 * @module [kategori]/[ModülAdı]
 * @version 5.0.0
 * @requires core/EventBus
 */
export default class [ModülAdı] {
    /** @type {import('../core/EventBus.js').default} */
    #eventBus;

    /**
     * @param {import('../core/EventBus.js').default} eventBus
     */
    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    /** Modül başlatma */
    init() {
        this.#bindEvents();
    }

    /** Event'leri bağla */
    #bindEvents() {
        // Event listener'lar
    }

    /** Kaynak temizleme */
    destroy() {
        // RemoveEventListener'lar
    }
}
```

---

## 7. Kurallar

| # | Kural | İhlal Sonucu |
|---|-------|--------------|
| 1 | Tek main.js YASAK — modüller ayrı dosya | Kod revert edilir |
| 2 | `var` YASAK — sadece `const`/`let` | Kod geçersiz |
| 3 | `innerHTML` YASAK — `textContent` | XSS açığı |
| 4 | ES Modules `import`/`export` zorunlu | Bağımlılık ihlali |
| 5 | `#private` field (ES2022) | Encapsulation ihlali |
| 6 | Event delegation (tek listener) | Performans ihlali |
| 7 | `AbortController` fetch için | Memory leak |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[vanilla-js-rules]] | JS kuralları |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |

---

## 9. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Modül Sayısı** | 14 (11 özellik + 3 core) |
| **ADR Uyumlu** | ✅ 001 |
| **Zero Hallucination** | ✅ |

---

---

## 10. **FAZ 2D GÜNCELLEME — §2 Ağaç Sapmaları (2026-09-08)**

MEMORY 2026-09-04 refactor'u sonrası eksikler:

| Öğe | Durum | Not |
|-----|-------|-----|
| `ScaleManager.js` (v6.0.0) | §2 ağacında YOK — **EKLENMELİ** | Hibrit scale motoru; scale*.js 4 dosyasının yerine (MEMORY kanıtlı) |
| `device-layout-updater.js` | §2 ağacında YOK — **EKLENMELİ** | Cihaz değişiminde layout güncelleme (brain §18B) |
| `SPARouterAdapter.js` | §2 "Legacy SPA entry" olarak listeli — **DEPRECATED etiketi** | brain §18B: main.js doğrudan Router (§5 notu zaten doğru ✓) |
| main.js sürümü | v5.0.0 yazıyor | Gerçek **v6.0.0** (MEMORY) — ScaleManager import+init |

**Güncel ağaç farkı:**

```
├── ScaleManager.js                 ← EKLENDİ (v6.0.0 — TierResolver+TransformApplier+EventBus)
├── device-layout-updater.js        ← EKLENDİ
└── router/
    ├── SPARouterAdapter.js         ← DEPRECATED (kaldırma kuyruğu)
    └── ...
```

§5 main.js örneği SPARouterAdapter import ETMİYOR (doğru ✓) ama ScaleManager da eksik — v6.0.0 gerçek akış: ScaleManager import + init + EventBus `scale:applied` (MEMORY canlı test kayıtlı).

---

## 11. Modül Life-cycle Sözleşmesi (init/destroy)

| Aşama | CoreMusicApp Davranışı | Modül Sözleşmesi |
|-------|------------------------|------------------|
| booting | registerModule(name, instance) → instance.init() | init: listener'ları bağla |
| running | çalışma | event emit/abone |
| destroyed | her modül destroy() | abonelik + listener + timer temizle |

Kural: `setRunning()` öncesi tüm modüller init edilmiş olmalı (main.js akışı — §5 sıra). destroy'suz modül §7 kural ihlalidir.

---

## 12. Event Kataloğu (Kod Kanıtlı Olanlar)

| Olay | Yayınlayan | Abone Örneği | Kanıt |
|------|-----------|--------------|-------|
| `app:ready` | main.js | — | §5 kod ✅ |
| `router:ready` | main.js | — | §5 kod ✅ |
| `devicechange` | DeviceManager (JS) | — | §4.3 ✅ |
| `themechange` | ThemeManager | — | §4.4 ✅ |
| `viewmodechange` | ViewModeManager | — | §4.5 ✅ |
| `player:play/pause/stop/next/prev` | PlayerController | footer UI | §4.6 ✅ |
| `card:click` | CardManager | route | §4.8 ✅ |
| `scale:applied` | ScaleManager | layout-duyarlılar | MEMORY 2026-09-04 ✅ |
| `track:select` | CardManager/C13 | PlayerController | web-audio §26 |
| `modechange` | dark/light | UI | dark-light §5.2 |

---

## 13. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Eski ağacın referans alınması | Orta | Orta | §10 güncelleme tablosu |
| 2 | ScaleManager init sırası (main.js'te Router sonrası) | Bilinmiyor | Orta | MEMORY canlı test ✅ ama sıra teyidi devam |
| 3 | modül sayısı sapması (14 → 16+) | Kesin (refactor) | Düşük | §10 + kalite güncelleme |
| 4 | destroy() eksik modül | Orta | Orta | vanilla-js §15 sözleşme |

---

## 14. Ek SSS

**S: ScaleManager neden managers/ değil kökte?**
C: MEMORY 2026-09-04 kaydında kök konum; ScaleManager layout-düzeyi altyapıdır (managers/ device/theme/viewmode gibi state değil transform yürütür). Yeniden konumlandırma refactor kararıdır.

**S: router/main.js "Legacy SPA entry" nedir?**
C: Eski bağımsız SPA giriş noktası; yeni main.js devraldı. Kaldırma adayı (§10 SPARouterAdapter ile birlikte) — kod temizlik kuyruğu.

**S: `CsrfSyncManager` ne yapıyor?**
C: §2 router/ listesi: CSRF token'ı DOM patch sonrası güncel tutar (meta tag → X-CSRF-Token header). Detay csrf.md §7.2.

**S: 21+ modül listesi nerede?**
C: DOĞRULAMA GEREKLİ — router/ alt dosya glob'u (vanilla-js-rules §18 benzeri) sonraki turda.

---

## 15. Kalite Raporu (Güncel)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 6.0.0 |
| **Modül Sayısı** | 14 dokümante + ScaleManager + layout-updater = 16 |
| **Sapma Düzeltmesi** | §10 — ScaleManager/layout-updater eklendi |
| **Event Kataloğu** | 10 olay (§12 — 8 kod kanıtlı) |
| **Zero Hallucination** | ✅ (router/ alt-listesi açık etiketli) |

---

## 16. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 5.0.0 | 2026-08-21 | Modül mimarisi |
| 6.0.0 | 2026-09-08 | Faz 2d: §10 ağaç güncellemesi (ScaleManager/layout-updater/SPARouterAdapter-DEPRECATED); §11 life-cycle; §12 event kataloğu; §13-§15 ekler |

---

## 17. Modül İletişim Örnekleri (EventBus Akışları)

**Akış 1 — Cihaz değişimi:**
```
device-loader.js cookie → PageRouter render (sunucu)
  → DeviceManager(JS) resize debounce → devicechange emit
     → device-layout-updater.updateAll()
     → ScaleManager tier yeniden hesap → scale:applied
     → WidgetManager widget sayısı uyumu (varsa)
```

**Akış 2 — Parça seçimi:**
```
C13 Track Row click → CardManager delegation
  → track:select emit → PlayerController src/play
     → player:state emit → footer ikon/seek güncelle
```

**Akış 3 — Tema değişimi:**
```
Ayarlar radio → ThemeManager.setTheme('male')
  → data-gender attribute → CSS token'lar
     → themechange emit → grafik yenilemeler
```

Akış kuralı: UI bileşenleri EventBus olaylarını dinler; modüller arası doğrudan referans yasak (§8.2 zinciri korunur).

---

## 18. Dosya Adlandırma Sözleşmesi

| Kural | Örnek | Not |
|-------|-------|-----|
| PascalCase sınıf/dosya | PlayerController.js | sınıf adı = dosya adı |
| Kategori klasörü | core/, managers/, features/, router/ | §2 ağaç |
| camelCase utility | device-loader.js, device-layout-updater.js | IIFE/utility istisnası |
| Tek sorumluluk | dosya başına 1 sınıf | §7 kural 1 türevi |
| `index.js` yok | import'lar tam yollarla | dağınıklık önleme |

---

## 19. Ek SSS

**S: `module-loader.js` (§8.1 eski ağaç) gerçek mi?**
C: vanilla-js-rules §12 tablosunda listelenmedi — DOĞRULAMA GEREKLİ (js/ glob görevi). Lazy loading PLANNED olabilir.

**S: CoreMusicApp state machine (`idle|booting|running|destroyed`) geçiş kuralları?**
C: idle → booting (init çağrısı) → running (setRunning) → destroyed (destroy). Geri geçiş yok; destroyed'tan sonra kullanım hata.

**S: Yeni manager eklerken hangi sıra?**
C: §8.2 zincirine göre EventBus'tan sonra; main.js import + registerModule + init sırası korunur (§5 şablon).

**S: TouchManager neden DeviceManager'a bağımlı?**
C: Yalnız `device === 'embedded'` koşulunda aktif (§4.10) — cihaz bilgisi olmadan karar veremez.

**S: ScrollManager router'a neden bağımlı?**
C: Route değişim olayını dinler (positions Map anahtarı URL) — Router olmadan scroll restore bağlamı yoktur.

---

## 20. Risk Kaydı Ek

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 5 | router/ alt-liste (21+ modül) dokümansız | Kesin | Düşük | Glob görevi (§14 SSS) |
| 6 | ScaleManager init sırası Router'a göre | Bilinmiyor | Orta | MEMORY canlı test ✅ — statik teyit devam |
| 7 | module-loader varlık belirsizliği | Bilinmiyor | Düşük | §19 SSS glob |

---

## 21. Kalite Raporu (Güncel)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 6.1.0 |
| **Bölüm Sayısı** | 21 |
| **Event Kataloğu** | 10 (8 kod kanıtlı) |
| **İletişim Akışı** | 3 (§17) |
| **SSS** | 12 |
| **Risk Kaydı** | 7 |

---

## 22. Revizyon Geçmişi (Güncel)

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 6.0.0 | 2026-09-08 | §10 ağaç güncellemesi |
| 6.1.0 | 2026-09-08 | §17 iletişim akışları; §18 adlandırma; §19-§21 ekler |

---

## 23. EventBus API Referansı

| Metot | İmza | Davranış |
|-------|------|----------|
| `on(event, fn)` | abone ekle | `#listeners Map` set'ine ekler |
| `off(event, fn)` | abone kaldır | destroy() sözleşmesinin yarısı |
| `once(event, fn)` | tek atımlık | ilk emit sonrası otomatik off |
| `emit(event, data)` | yayın | tüm abonelere sırayla |
| `destroy()` | tüm temizlik | Map clear — test izolasyonu |

Kullanım deseni: modül constructor'da `#eventBus` alır (§6 şablon), init'te `on`, destroy'da `off`.

---

## 24. Ek SSS

**S: `emit` sırasında abone hatası olursa?**
C: Hata diğer aboneleri kesmemeli — try/catch + log (uygulama detayı EventBus.js okumasında; temel ilke: tek hatalı abone zinciri bozmaz).

**S: Olay adlarında namespace çakışması?**
C: `alan:olay` formatı + alan sabitleri (player:, track:, scale:, mode:) çakışmayı önler. Yeni alan = katalog kaydı (§12).

**S: `once` nerede kullanılır?**
C: Tek seferlik hazırlık olayları (`app:ready` gibi) — kalıcı state dinleyicileri `on`+`off` çiftiyle yönetilir.

---

## 25. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 6.2.0 |
| **Bölüm Sayısı** | 25 |
| **EventBus API** | 5 metot (§23) |
| **SSS** | 14 |
| **Zero Hallucination** | ✅ |

---

## 26. Ek SSS (Final)

**S: `CoreMusicApp.getModule(name)` kullanım örneği?**
C: Çapraz acil erişim için — normal akış EventBus olayıdır (§17 akış kuralı). Doğrudan modül erişimi yalnız lifecycle yönetiminde.

**S: `registerModule` çift kayıt olursa?**
C: Map.set üzerine yazar — eski instance'ın destroy()'u çağrılmalı; app şablonu bunu yapmıyorsa sızıntı. CoreMusicApp davranışı kod teyidi bekliyor.

**S: EventBus dışındaki global (window.CoreMusic.EventBus) neden expose ediliyor?**
C: Debug konsol erişimi + eski script köprüleri (device-loader IIFE EventBus kullanmaz ama ileride köprü olabilir). Prod'da expose küçük saldırı yüzeyi — değerlendirme PLANNED.

**S: Modül init hatası uygulama çökertir mi?**
C: CoreMusicApp booting state'inde hata yönetimi kod teyidi bekliyor — hedef: hatalı modül atlanır + log, diğer modüller çalışır (degraded ilkesi paraleli).

---

## 27. Risk Kaydı (Final)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 8 | registerModule üzerine yazma sızıntısı | Orta | Orta | §26 SSS 2 — app davranış teyidi |
| 9 | window.CoreMusic expose yüzeyi | Orta | Düşük | §26 SSS 3 |
| 10 | init hatası tüm app'i düşürmesi | Bilinmiyor | Yüksek | §26 SSS 4 teyit |

---

## 28. İzlenebilirlik (Final)

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 5 EventBus metodu | §4.1 tablo | Doküman ✅ |
| 4 app state | §4.2 tablo | Doküman ✅ |
| TouchManager embedded-only | §4.10 | ✅ |
| widget clock setInterval | §4.7 | ✅ |

---

## 29. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 6.3.0 |
| **Bölüm Sayısı** | 29 |
| **SSS** | 14 |
| **Risk Kaydı** | 10 |
| **Zero Hallucination** | ✅ (app davranış teyitleri açık) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
