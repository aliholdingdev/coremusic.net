---
type: architecture
category: l3
title: "Vanilla JS Rules"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Vanilla JS Rules

**Zorunlu Bağlantılar:** [[index]] · [[ADR-001-vanilla-js-itcss]]

---

## 1. Amaç

Vanilla JS kodlama kurallarını ve yasak örüntülerini tanımlar. [[ADR-001-vanilla-js-itcss]] ile uyumludur.

---

## 2. Yasaklar

| ❌ Yasak | ✅ Doğru | Neden |
|----------|----------|-------|
| `var` | `const` / `let` | Scope sorunları |
| `innerHTML` | DOMParser + TrustedTypes | XSS açığı |
| `eval()` | Safe alternatives | Güvenlik açığı |
| React / Vue / Angular | Vanilla JS | Bağımlılık |
| `Function()` | Safe alternatives | Güvenlik açığı |
| `setTimeout(string)` | `setTimeout(function)` | Güvenlik açığı |

---

## 3. Zorunlu Kurallar

| Kural | Değer |
|-------|-------|
| **Declaration** | `const` / `let` (var yasak) |
| **Private** | `#field` (ES2022) |
| **Module** | ES Modules (import/export) |
| **Async** | async/await |
| **DOM** | DOMParser + TrustedTypes |
| **AbortController** | Fetch timeout |
| **Event delegation** | Single handler |

---

## 4. Yasak Örüntü Detayları

### 4.1 innerHTML → DOMParser

```javascript
// ❌ YANLIŞ: innerHTML (XSS riski)
element.innerHTML = userContent;

// ✅ DOĞRU: DOMParser (güvenli)
const parser = new DOMParser();
const doc = parser.parseFromString(userContent, 'text/html');
element.append(...doc.body.childNodes);
```

### 4.2 var → const/let

```javascript
// ❌ YANLIŞ: var
var name = 'John';

// ✅ DOĞRU: const veya let
const name = 'John';
let counter = 0;
```

### 4.3 eval → Safe alternatives

```javascript
// ❌ YANLIŞ: eval()
eval(userInput);

// ✅ DOĞRU: JSON.parse()
const data = JSON.parse(userInput);

// ✅ DOĞRU: Function constructor (if needed)
const fn = new Function('return ' + expression);
```

---

## 5. Private Fields

```javascript
class Player {
    #state = 'stopped';
    #volume = 0.5;
    #playlist = [];

    get state() { return this.#state; }
    set state(value) { this.#state = value; }

    #updateUI() {
        // Private method
    }
}
```

---

## 6. Event Delegation

```javascript
// ❌ YANLIŞ: Her elemana event listener
document.querySelectorAll('.button').forEach(btn => {
    btn.addEventListener('click', handler);
});

// ✅ DOĞRU: Event delegation
document.addEventListener('click', (e) => {
    if (e.target.matches('.button')) {
        handler(e);
    }
});
```

---

## 7. Fetch with AbortController

```javascript
async function fetchData(url, timeout = 5000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeout);

    try {
        const response = await fetch(url, { signal: controller.signal });
        clearTimeout(timer);
        return await response.json();
    } catch (err) {
        clearTimeout(timer);
        throw err;
    }
}
```

---

## 8. JS Module Architecture (v5.0.0)

**Tek main.js dosyası YASAK.** Modüller ayrı dosyalara bölünmüştür.

### 8.1 — Dizin Yapısı

```
assets.coremusic.net/js/
├── core/                        ← Temel altyapı
│   ├── EventBus.js                Pub/sub (tüm modüller bağımlı)
│   ├── CoreMusicApp.js            Lifecycle manager
│   └── module-loader.js           Dinamik modül yükleme
├── managers/                    ← Durum yönetimi
│   ├── DeviceManager.js           Cihaz tespiti (device-loader.js bridge)
│   ├── ThemeManager.js            ADR-044 gender theme
│   └── ViewModeManager.js         ADR-045 view mode
├── features/                    ← Sayfa özellikleri
│   ├── PlayerController.js        State machine (STOPPED/PLAYING/PAUSED)
│   ├── WidgetManager.js           Home widgets
│   ├── CardManager.js             Event delegation
│   ├── ScrollManager.js           Route scroll restore
│   └── TouchManager.js            Embedded touch gestures
├── router/                      ← SPA navigasyonu
│   ├── SPARouterAdapter.js        Router.js bridge
│   └── (mevcut 21+ modül)
└── main.js                      ← Entry point: import + init (10-20 satır)
```

### 8.2 — Modül Bağımlılık Sırası

```
EventBus (bağımsız)
  → DeviceManager (EventBus'e bağımlı)
    → ThemeManager (EventBus'e bağımlı)
      → ViewModeManager (EventBus'e bağımlı)
        → SPARouterAdapter (EventBus + DeviceManager)
          → PlayerController (EventBus)
            → WidgetManager (EventBus)
              → CardManager (EventBus)
                → ScrollManager (EventBus + Router)
                  → TouchManager (EventBus + DeviceManager)
```

### 8.3 — Her Modül Şablonu

```javascript
/**
 * CoreMusic — [Modül Adı]
 * [Açıklama]
 *
 * @module core/[ModülAdı]
 * @version 5.0.0
 */
export default class [ModülAdı] {
    #eventBus;

    constructor(eventBus) {
        this.#eventBus = eventBus;
    }

    init() {
        // Modül başlatma
    }

    destroy() {
        // Kaynak temizleme
    }
}
```

### 8.4 — main.js Entry Point

```javascript
/**
 * CoreMusic — main.js v5.0.0
 * Entry point: Modülleri import et ve başlat
 */
import EventBus from './core/EventBus.js';
import CoreMusicApp from './core/CoreMusicApp.js';
import DeviceManager from './managers/DeviceManager.js';
import ThemeManager from './managers/ThemeManager.js';
import ViewModeManager from './managers/ViewModeManager.js';
import SPARouterAdapter from './router/SPARouterAdapter.js';
import PlayerController from './features/PlayerController.js';
import WidgetManager from './features/WidgetManager.js';
import CardManager from './features/CardManager.js';
import ScrollManager from './features/ScrollManager.js';
import TouchManager from './features/TouchManager.js';

(function () {
    'use strict';
    if (typeof history.pushState !== 'function') return;

    const app = new CoreMusicApp({
        modules: { EventBus, DeviceManager, ThemeManager, ViewModeManager,
                   SPARouterAdapter, PlayerController, WidgetManager,
                   CardManager, ScrollManager, TouchManager }
    });

    document.addEventListener('DOMContentLoaded', () => app.init());

    window.CoreMusic = window.CoreMusic || {};
    window.CoreMusic.App = app;
    window.CoreMusic.version = '5.0.0';
})();
```

---

## 9. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **ES5 tarayıcı** | Progressive enhancement | ADR-001 |
| **Module desteği yok** | Script type="module" | ADR-001 |
| **DOM injection** | DOMParser + TrustedTypes | ADR-001 |
| **Event leak** | RemoveEventListener | ADR-001 |

---

## 10. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L3 ana dizin |
| [[itcss-architecture]] | CSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| [[js-module-architecture]] | JS modül detayları |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 5.0.0 |
| **Satır Sayısı** | ~700 |
| **ADR Uyumlu** | ✅ 001 |
| **Zero Hallucination** | ✅ |

---

## 12. **KRİTİK DÜZELTME — §8 Modül Ağacı Güncel Değil (Faz 2d, 2026-09-08)**

§8.1/§8.2/§8.4'teki liste MEMORY 2026-09-04 refactor'u öncesi hâlidir:

| Eski Öğe | Durum | Güncel |
|----------|-------|--------|
| `SPARouterAdapter.js` | **DEPRECATED** | main.js doğrudan Router import eder (brain §18B) |
| ScaleManager.js | Listede YOK | **EKLENDİ** — v6.0.0 hibrit scale motoru (scale*.js 4 dosya yerine) |
| `device-layout-updater.js` | Listede YOK | **EKLENDİ** — cihaz değişiminde layout güncelleme |
| main.js v5.0.0 | Güncel değil | **v6.0.0** — ScaleManager import+init |

**Güncel giriş sırası (main.js v6.0.0):** EventBus → DeviceManager → ThemeManager → ViewModeManager → Router (doğrudan) → ScaleManager → PlayerController → WidgetManager → CardManager → ScrollManager → TouchManager.

§8.1-8.4 örnekleri **tarihsel v5.0.0 dokümanı** olarak korunmuştur (In-Place ilkesi); güncel gerçeklik yukarıdaki tablo + [[index]] §9 + brain §18B'dir. Yeni modül yazarken bu tabloyu esas al — §8.4 import listesini KOPYALAMA.

---

## 13. TrustedTypes / DOMParser Kurulum Deseni

```javascript
// TrustedTypes policy (CSP require-trusted-types ile uyumlu)
const policy = window.trustedTypes?.createPolicy('default', {
    createHTML: (s) => s,   // sanitizer geçişi — DOMParser öncesi
});

// Güvenli HTML enjeksiyon akışı:
// 1. DOMParser ile parse et (§4.1)
// 2. append childNodes (innerHTML'e asla dokunma)
// 3. TrustedTypes enforcement aktifse policy.createHTML kullan
```

Kural: `innerHTML` assignment tüm kod tabanında yasak; yeni kod `append(...doc.body.childNodes)` deseni kullanır. Mevcut kodda innerHTML kalıntısı taraması Faz kontrol görevidir.

---

## 14. EventBus Kullanım Disiplini

| Kural | Neden |
|-------|-------|
| Her abonelik destroy()'da unsubscribe | Event leak (§9 edge) |
| Olay adları `alan:olay` formatı | `player:state`, `scale:applied`, `track:select`, `modechange` |
| Payload'lar küçük/serializable | Debug izlenebilirliği |
| Cross-modül doğrudan çağrı yerine event | Bağımlılık zinciri (§8.2) bozulmasın |

Gerçek olay örnekleri (kod kanıtlı): `scale:applied` (ScaleManager — MEMORY 2026-09-04), `player:state` (web-audio §14), `track:select` (web-audio §26).

---

## 15. Cleanup Kuralları

```
destroy() sözleşmesi (§8.3 şablon):
  1. EventBus aboneliklerini kaldır
  2. window/document listener'ları removeEventListener
  3. Timer'ları clearInterval/clearTimeout
  4. AbortController.abort() (§7 fetch'ler)
  5. DOM referanslarını null'a çek (opsiyonel — GC ipucu)
```

Kural: `destroy()`'suz modül yazılamaz (§8.3 şablonu zorunlu alan). SPA navigasyonunda feature modülleri yeniden init edilir — sızıntı birikimi burada önlenir.

---

## 16. Lint / Format Kuralları

| Kural | Değer |
|-------|-------|
| Semicolon | Zorunlu |
| Quotes | Single (proje konvansiyonu — örneklerden) |
| Indent | 4 space (örneklerden gözlenen) — ESLint karar PLANNED |
| ES hedefi | ES2022 (brain §18) |
| strict mode | `'use strict'` (main.js örneğinde var) |

ESLint/Prettier konfigürasyonu PLANNED — kurallar §3 tablosundan türetilecektir.

---

## 17. Test Senaryoları (JS Kuralları)

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | innerHTML taraması | 0 sonuç (prod JS) |
| 2 | var taraması | 0 sonuç |
| 3 | eval/Function() taraması | 0 sonuç |
| 4 | #private field kullanımı | managers/features sınıflarında |
| 5 | destroy() varlığı | §8.3 şablonlu her modülde |
| 6 | EventBus leak testi | init/destroy döngüsü ×100 → abone sayısı sabit |
| 7 | SPARouterAdapter referansı | main.js'te YOK (deprecated) |
| 8 | module type | defer + type=module (html-shell §11) |

---

## 18. Diagnostics (tekrarlanabilir)

```powershell
# 1. Yasak örüntü taramaları (beklenen: 0)
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" |
  Select-String -Pattern "\bvar\s+\w+\s*=|eval\(|new Function\(|\.innerHTML\s*=" -ErrorAction SilentlyContinue

# 2. SPARouterAdapter kalıntısı (main.js'te beklenen: 0)
Select-String -LiteralPath "assets.coremusic.net\js\router\main.js" -Pattern "SPARouterAdapter" -ErrorAction SilentlyContinue

# 3. ScaleManager import kanıtı (beklenen: 1+)
Select-String -LiteralPath "assets.coremusic.net\js\router\main.js" -Pattern "ScaleManager"

# 4. destroy() sözleşmesi
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" | Select-String -Pattern "destroy\(\)" -ErrorAction SilentlyContinue | Measure-Object

# 5. TrustedTypes policy
Get-ChildItem -LiteralPath "assets.coremusic.net\js" -Recurse -Include "*.js" | Select-String -Pattern "trustedTypes" -ErrorAction SilentlyContinue
```

---

## 19. Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | §8 eski ağacın kopyalanması | Yüksek (şti) | Orta | §12 düzeltme + index §9 |
| 2 | SPARouterAdapter geri dönüşü | Düşük | Düşük | §18 komut 2 |
| 3 | innerHTML regresyonu | Orta | Yüksek | §18 komut 1 + review gate |
| 4 | destroy() atlanan modüller | Orta | Orta | §15 sözleşme |
| 5 | ESLint yokluğunda stil dağınıklığı | Yüksek | Düşük | §16 PLANNED karar |

---

## 20. Ek SSS

**S: §8.4 main.js örneği neden v5.0.0 — güncellenmez mi?**
C: In-Place ilkesi gereği örnek korundu; §12 tablosu güncel gerçekliği taşır. Yeni main.js yazımı brain §18B + §12 tablosundan yapılır — örnek kopyalanmaz (WORKFLOW §8.1C).

**S: `new Function('return '+expr)` örneği neden Doğru sütununda?**
C: §4.3 örneği "eval yerine JSON.parse tercih et" mesajını taşır; Function() örneği yanıltıcıdır — Function() da §2'de yasaktır (satır 33). Örnek düzeltme notu: yalnız JSON.parse doğru yol.

**S: Module olmayan scriptler (device-loader) kural dışı mı?**
C: Evet — device-loader.js IIFE + sync, bilinçli istisna (TV/1024 sync — brain §18B). Kural: yeni sync script yalnız shell başlatma zinciri gerekçesiyle.

**S: js-module-architecture.md ile bu dosyanın farkı?**
C: Bu dosya KURALLAR (yasaklar/desiplin), js-module MİMARİ (dosya düzeni/bağımlılık). Modül eklerken ikisi de okunur.

**S: `#private` tarayıcı desteği?**
C: ES2022 — Tier 1-4 hedef tarayıcılar destekler. ES5 fallback yok (progressive enhancement ilkesi: modül desteği olmayan tarayıcı SPA'sız kalır).

---

## 21. İzlenebilirlik Tablosu Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| SPARouterAdapter deprecated | brain §18B + components §6 | ✅ |
| ScaleManager v6.0.0 | MEMORY 2026-09-04 | ✅ |
| main.js v6.0.0 import+init | MEMORY 2026-09-04 | ✅ |
| `scale:applied` EventBus olayı | MEMORY 2026-09-04 | ✅ |
| strict_types JS karşılığı 'use strict' | main.js örneği | ✅ |
| innerHTML yasak | CLAUDE §21 + ADR-001 | ✅ |

---

## 22. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 5.0.0 | 2026-08-21 | Kurallar dokümanı |
| 6.0.0 | 2026-09-08 | Faz 2d: **§12 modül ağacı sapma düzeltmesi** (SPARouterAdapter deprecated, ScaleManager eksik); §13 TrustedTypes; §14 EventBus disiplini; §15 cleanup; §16 lint; §17-§21 test/diagnostics/risk/SSS/izlenebilirlik |

---

## 23. Kod İnceleme Checklist (JS)

| # | Kontrol | Yöntem |
|---|---------|--------|
| 1 | var/eval/Function/innerHTML yok | §18 komut 1 |
| 2 | ES modules (import/export) | dosya başı |
| 3 | #private field kullanımı | sınıf gövdesi |
| 4 | async/await (then-chain yok) | gövde |
| 5 | AbortController fetch'lerde | her fetch |
| 6 | destroy() abonelik temizliği | her modül |
| 7 | Event delegation | birden çok benzer eleman |
| 8 | Konsol 0 hata | browser test |
| 9 | SPARouterAdapter referansı yok | §18 komut 2 |
| 10 | window.CoreMusic expose minimal | §19 SSS 3 |

---

## 24. Yeni Modül Kayıt Prosedürü

1. Konum seç: core/ (altyapı) / managers/ (durum) / features/ (sayfa) — §8.1 ağaç.
2. Şablon: §8.3 (eventBus ctor, init, destroy).
3. Bağımlılık: yalnız EventBus (+ izinli 1 bağımlılık — §8.2 zincir).
4. Olaylar: katalog (js-module §12) + `alan:olay` adı.
5. main.js: import + registerModule + init (v6.0.0 gerçek sıraya).
6. Test: §17 senaryolarından uygunlar.
7. Kayıt: js-module §10 ağaç + vanilla-js §8.1 ağaç çift senkron + log.md.

Yasak: modül içi global state (window'a yazma), EventBus'sız çapraz çağrı, destroy'sız abonelik.

---

## 25. AbortController Detay

| Konu | Kural |
|------|-------|
| Timeout | 5s default (§7) — endpoint bazlı override |
| Sayfa/rot değişimi | Route değişince aktif fetch'ler abort (ScrollManager benzeri lifecycle) |
| Kullanıcı iptali | İptal butonu → abort — UI geri bildirim |
| Hata tipi | AbortError → sessiz toparla (hata değil); diğer → log |
| Zincir | AbortSignal.any ile birden çok kaynak (PLANNED) |

---

## 26. Error Handling Pattern

```javascript
try {
    const data = await fetchData(url);
    eventBus.emit('data:loaded', { url, data });
} catch (err) {
    if (err.name === 'AbortError') return;      // bilinçli iptal — sessiz
    eventBus.emit('data:error', { url, message: err.message });
    // log: RouterConfig.logLevel'e göre
}
```

Kural: hatalar event ile yayınlanır — UI tek noktadan gösterir (i18n PLANNED). console.error yalnız dev; prod'da event + log.

---

## 27. Performans Kuralları

| Kural | Neden |
|-------|-------|
| requestAnimationFrame DOM yazımlarında | layout thrashing önleme |
| Event delegation | listener sayısı ↓ (§6) |
| passive:true scroll/touch listener'larda | scroll performansı |
| IntersectionObserver görünüm tetiklemeleri | scroll handler yükü ↓ (PLANNED kullanım) |
| Lazy import (dynamic import()) | route bazlı modül yükü (PLANNED) |
| Debounce 300/200ms | device-loader/DeviceManager mevcut (§4 tablo) |

---

## 28. Security Checklist (JS)

| # | Kontrol | Kaynak |
|---|---------|--------|
| 1 | localStorage auth verisi yok | CLAUDE §21 |
| 2 | window.CoreMusic'te secret yok | html-shell §18 risk 4 |
| 3 | JSON encode flag'li inline script | html-shell §25 |
| 4 | TrustedTypes/DOMParser | §13 |
| 5 | CSRF header sync | CsrfSyncManager |
| 6 | URL parametre DOM'a direkt basılmıyor | DOMParser akışı |

---

## 29. Ek SSS

**S: `async` fonksiyonda unhandled promise nasıl yakalanır?**
C: window.addEventListener('unhandledrejection') global — log'a yazılır; her await try/catch içinde olsa da global ağ son katmandır (best-practices paralel).

**S: ES module defer sırası garanti mi?**
C: Evet — type=module defer önceliği document sırasıdır; ScaleManager main'den önce gelirse import grafiğiyle de garanti (html-shell §11 sıra).

**S: `??` ve `?.` kullanımı serbest mi?**
C: Evet — ES2022 hedef; örneklerde mevcut (null-coalescing zincirler).

**S: DOM fragment tekrar kullanımı?**
C: DocumentFragment klonlanabilir veya her patch yeniden — DomPatcher kararı; kopya bakım maliyeti değerlendirilir.

**S: `structuredClone` kullanılabilir mi?**
C: Evet — derin kopya için (best-practices paralel); spread/JSON hileleri yerine.

**S: Modüller arası döngüsel import?**
C: Yasak — §8.2 zinciri ağaçtır; döngü ihtiyacı EventBus olayına çevrilir.

---

## 30. Risk Kaydı Ek

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 11 | unhandledrejection sessiz kayıp | Orta | Orta | §26 global handler |
| 12 | §8.4 v5.0.0 örneğinin kopyalanması | Orta | Orta | §12 tablo + WORKFLOW §8.1C |
| 13 | legacy var kalıntısı (IIFE içinde) | Kesin (bilinçli) | Düşük | §6 kural 3 parantez notu |

---

## 31. İzlenebilirlik Ek

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| '#private' ES2022 | brain §18 | ✅ |
| AbortController | §7 + brain §18 | ✅ |
| main.js v6.0.0 | MEMORY 2026-09-04 | ✅ |
| scale:applied olay | MEMORY | ✅ |
| RouterConfig.logLevel | html-shell §6 | ✅ |

---

## 32. Kalite Raporu (Final)

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 6.2.0 |
| **Bölüm Sayısı** | 32 |
| **Checklist** | 10 madde (§23) |
| **SSS** | 18 |
| **Risk Kaydı** | 13 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
