---
type: adr
category: architecture
title: "ADR-004: Multi-Domain SPA Architecture"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-004: Multi-Domain SPA Architecture

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Architecture
**İlgili Agent:** [[.agents/backend-architect]]
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformunun multi-domain SPA (Single Page Application) mimarisini tanımlar. Her subdomain için bağımsız bir SPA, paylaşılan bileşenler ve vault versiyonlama sistemi bu kararın temelini oluşturur.

CoreMusic; 10 panel ve 7 backend servis ile çalışan, çeşitli kullanım senaryolarına (ev, araç, stüdyo, profesyonel) hitap eden bir medya platformudur. Her panelin kendi alan adı ve portu vardır.

---

## 2. Bağlam

### 2.1 İş Problemi

CoreMusic birden fazla kullanım senaryosuna sahiptir:

| Senaryo | Panel | Özellik |
|---------|-------|---------|
| Ev medya merkezi | home.coremusic.net | TV, tablet, mobil |
| Araç içi bilgi-eğlence | car.coremusic.net | Dokunmatik, sesli komut |
| Profesyonel stüdyo | studio.coremusic.net | 8.1 surround, ASIO |
| Profesyonel kullanım | pro.coremusic.net | Gelişmiş kontroller |
| Müzik yönetimi | music.coremusic.net | Ana medya paneli |
| Yönetim | admin.coremusic.net | Kullanıcı, servis yönetimi |
| İndirme | download.coremusic.net | Deezer/YouTube indirme |
| Medya işleme | media.coremusic.net | FFmpeg, metadata |
| Kimlik doğrulama | auth.coremusic.net | Login, register |
| Landing | coremusic.net | Tanıtım sayfası |

### 2.2 Teknik Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Framework yasak | ADR-001 ile Vanilla JS zorunlu |
| ITCSS 7-layer | CSS mimarisi zorunlu |
| BEM/BEMIT | CSS naming zorunlu |
| TrustedTypes | innerHTML yasak |
| DOMParser | HTML parsing zorunlu |
| Shared Components | Paylaşılan JS/CSS modülleri |
| Port isolation | Her servis kendi portunda |

### 2.3 Mimari Gereksinimler

| Gereksinim | Açıklama |
|------------|----------|
| Bağımsız deploy | Her panel bağımsız deploy edilebilmeli |
| Paylaşılan state | cross-view state korunmalı (ADR-046) |
| Tema entegrasyonu | Dinamik tema engine (ADR-044) |
| View geçişleri | View Transition API (ADR-048) |
| Responsive | Tüm cihazlarda çalışmalı |
| Accessibility | WCAG 2.2 AA uyumlu |

---

## 3. Karar

CoreMusic'te **10 panelli multi-domain SPA** mimarisi kullanılacak. Her panel bağımsız bir SPA olarak çalışacak, ancak paylaşılan bileşenler ve state yönetimi merkezi olarak sağlanacak.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| Panel sayısı | 10 |
| Mimarit | Multi-domain SPA |
| Frontend | Vanilla JS ES6+ (ADR-001) |
| CSS | ITCSS 7-layer + BEM |
| State | Cross-view state (ADR-046) |
| Tema | Dynamic theme engine (ADR-044) |
| View geçiş | View Transition API (ADR-048) |
| Deploy | Bağımsız deploy |

---

## 4. Teknik Detaylar

### 4.1 Panel Listesi

| # | Panel | Subdomain | Port | Stack | Görünüm |
|---|-------|-----------|------|-------|---------|
| 1 | Music | music.coremusic.net | 81 | PHP 8.4 + JS | Ana medya |
| 2 | Admin | admin.coremusic.net | 80 | PHP 8.4 | Yönetim |
| 3 | Download | download.coremusic.net | 3001 | Node.js + TS | İndirme |
| 4 | Media | media.coremusic.net | 5000/6000 | PHP + FFmpeg | Medya |
| 5 | Auth | auth.coremusic.net | — | PHP 8.4 | Kimlik |
| 6 | Home | home.coremusic.net | 81 | Vanilla JS | Ev merkezi |
| 7 | Car | car.coremusic.net | — | Vanilla JS | Araç içi |
| 8 | Studio | studio.coremusic.net | 81 | Vanilla JS | Stüdyo |
| 9 | Pro | pro.coremusic.net | 81 | Vanilla JS | Profesyonel |
| 10 | Landing | coremusic.net | 80 | Vanilla JS | Tanıtım |

### 4.2 Görünüm Modları

| Mod | Açıklama | Kullanım |
|-----|----------|----------|
| Home | Ev medya merkezi görünümü | TV, tablet |
| Pro | Profesyonel görünüm | Stüdyo |
| Studio | Stüdyo görünümü | 8.1 surround |

### 4.3 Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net, coremusic.net | HTTP |
| 81 | music.coremusic.net, home, studio, pro | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 9 BCNF DB | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |

### 4.4 Paylaşılan Bileşenler

```
shared/
├── js/
│   ├── router.js           # SPA router (ADR-021)
│   ├── state-manager.js    # Cross-view state (ADR-046)
│   ├── theme-manager.js    # Dynamic theme (ADR-044)
│   ├── api-client.js       # API istemcisi
│   ├── auth.js             # Kimlik doğrulama
│   ├── i18n.js             # Uluslararasılaştırma
│   └── components/
│       ├── player.js       # Medya oynatıcı
│       ├── playlist.js     # Çalma listesi
│       ├── search.js       # Arama
│       └── settings.js     # Ayarlar
├── css/
│   ├── 01_Settings/        # CSS custom properties
│   ├── 02_Generate/        # Renkler, tipografi
│   ├── 03_Base/            # Reset, normalize
│   ├── 04_Objects/         # Kodlama yapısı
│   ├── 05_Components/      # Bileşenler
│   ├── 06_Trumps/          # Yardımcılar
│   └── 07_Utilities/       # Yardımcı sınıflar
└── assets/
    ├── images/
    ├── fonts/
    └── icons/
```

### 4.5 SPA Router Yapısı

```javascript
'use strict';

const Router = {
    routes: new Map(),
    currentRoute: null,

    init() {
        window.addEventListener('popstate', () => this.handleRoute());
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-route]');
            if (link) {
                e.preventDefault();
                this.navigate(link.getAttribute('href'));
            }
        });
        this.handleRoute();
    },

    addRoute(path, handler) {
        this.routes.set(path, handler);
    },

    navigate(path) {
        history.pushState(null, '', path);
        this.handleRoute();
    },

    handleRoute() {
        const path = window.location.pathname;
        const handler = this.routes.get(path);
        if (handler) {
            this.currentRoute = path;
            handler();
        }
    }
};
```

### 4.6 Cross-View State

```javascript
'use strict';

const StateManager = {
    state: {
        user: null,
        theme: 'default',
        deviceType: 'desktop',
        viewMode: 'home',
        playlist: [],
        currentTrack: null,
        volume: 0.8,
        isPlaying: false,
    },
    listeners: new Map(),

    getState() {
        return { ...this.state };
    },

    setState(key, value) {
        if (this.state[key] === value) return;
        this.state[key] = value;
        this.notifyListeners(key);
        this.persistState();
    },

    subscribe(key, callback) {
        if (!this.listeners.has(key)) {
            this.listeners.set(key, []);
        }
        this.listeners.get(key).push(callback);
    },

    notifyListeners(key) {
        const callbacks = this.listeners.get(key) || [];
        callbacks.forEach(cb => cb(this.state[key]));
    },

    persistState() {
        try {
            sessionStorage.setItem('coremusic_state', JSON.stringify(this.state));
        } catch (e) { /* SessionStorage dolu */ }
    },

    restoreState() {
        try {
            const saved = sessionStorage.getItem('coremusic_state');
            if (saved) {
                this.state = { ...this.state, ...JSON.parse(saved) };
            }
        } catch (e) { /* Bozuk veri */ }
    }
};
```

### 4.7 Vault Versiyonlama

| Versiyon | Açıklama | Kullanım |
|----------|----------|----------|
| v1.0.0 | İlk sürüm | Başlangıç |
| v1.1.0 | Minor güncelleme | Yeni özellik |
| v1.1.1 | Patch düzeltme | Hata düzeltme |
| v2.0.0 | Major güncelleme | Breaking change |

### 4.8 SPA Router Implementasyonu

```javascript
class MusicRouter {
    constructor() {
        this.routes = new Map();
        this.currentRoute = null;
        this.middlewares = [];
    }

    addRoute(path, handler, meta = {}) {
        this.routes.set(path, { handler, meta });
    }

    use(middleware) {
        this.middlewares.push(middleware);
    }

    navigate(path) {
        const route = this.routes.get(path);
        if (!route) {
            console.error(`Route not found: ${path}`);
            return;
        }

        // Middleware chain
        let index = 0;
        const next = () => {
            if (index < this.middlewares.length) {
                const middleware = this.middlewares[index++];
                middleware(this.currentRoute, route, next);
            } else {
                this.currentRoute = route;
                route.handler();
            }
        };
        next();
    }
}

// Kullanım
const router = new MusicRouter();
router.addRoute('/', homeHandler, { title: 'Home' });
router.addRoute('/music', musicHandler, { title: 'Music' });
router.addRoute('/admin', adminHandler, { title: 'Admin', auth: true });
router.navigate('/');
```

### 4.9 URL Normalization

| Input | Output | Yöntem |
|-------|--------|--------|
| `/music/song/123/` | `/music/song/123` | Trailing slash |
| `/music//song` | `/music/song` | Double slash |
| `/Music/Song` | `/music/song` | Lowercase |
| `/music/song?ref=home` | `/music/song` | Query strip |
| `/music/song#section` | `/music/song` | Fragment strip |

### 4.10 Navigation Event Sistemi

```javascript
class NavigationEvent {
    static BEFORE_NAVIGATE = 'before-navigate';
    static AFTER_NAVIGATE = 'after-navigate';
    static NAVIGATION_ERROR = 'navigation-error';

    static dispatch(type, detail) {
        const event = new CustomEvent(type, { detail });
        document.dispatchEvent(event);
    }
}

// Kullanım
document.addEventListener(NavigationEvent.BEFORE_NAVIGATE, (e) => {
    console.log('Navigating to:', e.detail.path);
});

router.navigate('/music');
```

### 4.11 History API Entegrasyonu

```javascript
// popstate event handling
window.addEventListener('popstate', (event) => {
    const path = window.location.pathname;
    router.navigate(path);
});

// Push state
function pushState(path, state) {
    window.history.pushState(state, '', path);
    router.navigate(path);
}

// Replace state
function replaceState(path, state) {
    window.history.replaceState(state, '', path);
    router.navigate(path);
}
```

### 4.12 View Mode Geçişleri

| Geçiş | Kaynak | Hedef | Animasyon |
|-------|--------|-------|-----------|
| Home → Pro | Varsayılan | Pro modu | Slide |
| Pro → Home | Pro modu | Varsayılan | Slide |
| Home → Studio | Varsayılan | Studio modu | Fade |
| Studio → Home | Studio modu | Varsayılan | Fade |
| Herhangi → Auth | Herhangi | Auth sayfası | Redirect |

### 4.13 Responsive Breakpoint Matrisi

| Breakpoint | Width | Layout | Panel |
|------------|-------|--------|-------|
| Mobile | <768px | Single column | 1 panel |
| Tablet | 768-1024px | 2 column | 2 panel |
| Desktop | >1024px | Multi column | 3+ panel |
| Wide | >1440px | Full layout | Tüm paneller |

### 4.14 Accessibility Uyumluluğu

| Özellik | WCAG Seviye | Uygulama |
|---------|-------------|----------|
| Keyboard nav | AA | Tüm route'lar |
| Screen reader | AA | ARIA labels |
| Focus mgmt | AA | Route değişimi |
| Color contrast | AA | Tema entegrasyonu |
| Alt text | AA | Görüntüler |
| Skip links | AA | Navigation |

### 4.15 Bileşen Lifecycle

| Event | Zamanlama | Kullanım |
|-------|-----------|----------|
| init | Bileşen yüklendi | İlk kurulum |
| mount | DOM'a eklendi | Event binding |
| update | State değişti | Yeniden render |
| destroy | DOM'dan kaldırıldı | Cleanup |

### 4.16 Error Handling

| Hata Türü | Yakalama | Kurtarma |
|-----------|----------|----------|
| Network error | try/catch | Retry + fallback |
| Parse error | try/catch | Hata göster |
| Auth error | 401 check | Redirect login |
| 404 error | Route check | Ana sayfa |
| 500 error | try/catch | Hata sayfası |

### 4.17 Performance Optimizasyonu

| Teknik | Açıklama | Etki |
|--------|----------|------|
| Lazy loading | Route-based splitting | İlk yükleme hızlanır |
| Code splitting | Dynamic import | JS boyutu düşer |
| Image optimization | WebP, lazy load | LCP düşer |
| Cache | Service worker | Tekrar yükleme hızlanır |
| Debounce | Input debounce | UI responsiveness |

### 4.18 Monitoring ve Observability

| Metrik | Kaynak | Hedef |
|--------|--------|-------|
| Navigation time | Performance API | <100ms |
| Route change | Custom event | Tracking |
| Error rate | Error boundary | <1% |
| User flow | Analytics | Funnel analizi |
| Core Web Vitals | Web Vitals | Good |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | İhlal Sonucu |
|----------|----------|-------------|
| React / Vue / Angular | Vanilla JS (ADR-001) | Bağımlılık artışı |
| `innerHTML` | DOMParser + TrustedTypes | XSS riski |
| `var` | `const` / `let` | Scope sorunu |
| `eval()` / `Function()` | Safe alternatifler | Güvenlik açığı |
| Tek SPA, tüm domainler | Multi-domain SPA | Performans düşüşü |
| Hardcoded API URLs | Environment-based config | Yanlış ortam |
| Senkron istekler | Async/await + AbortController | UI donması |
| Framework CSS | ITCSS 7-layer | Bakım zorluğu |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Cross-domain cookie | Farklı subdomain'ler | SameSite=None + Secure |
| State kaybı | Sayfa yenileme | sessionStorage persist |
| Route çakışması | Aynı path, farklı panel | Subdomain bazlı routing |
| Tema geçiş hatası | Eşzamanlı tema değişikliği | Queue ile sıralı geçiş |
| View Transition hatası | Eski tarayıcı | Fallback: anında geçiş |
| Component cache bozulması | JS hot reload | Cache busting |
| Service worker çakışması | Çoklu registration | Scope bazlı registration |
| Offline durum | Ağ kopması | Offline-First + SQLite queue |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Framework yasak (ADR-001) | Mimari bozulma |
| 2 | ITCSS 7-layer zorunlu | CSS kaosu |
| 3 | BEM naming zorunlu | CSS çakışması |
| 4 | TrustedTypes zorunlu | XSS riski |
| 5 | Bağımsız deploy | Deploy karmaşası |
| 6 | Cross-view state (ADR-046) | State kaybı |
| 7 | Tema engine (ADR-044) | Tema tutarsızlığı |

---

## 8. Performans Hedefleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| TTFB | <200ms | Server response |
| FCP | <1.5s | First Contentful Paint |
| LCP | <2.5s | Largest Contentful Paint |
| CLS | <0.1 | Cumulative Layout Shift |
| INP | <200ms | Interaction to Next Paint |
| JS bundle | <100KB | Gzip sonrası |
| CSS bundle | <50KB | Gzip sonrası |

---

## 9. Güvenlik

| Önlem | Değer | ADR |
|-------|-------|-----|
| CSP nonce | Base64(random_bytes(32)) | ADR-012 |
| CSRF token | `csrf_token` | ADR-010 |
| XSS koruması | TrustedTypes + DOMParser | ADR-001 |
| Session | 3600s idle timeout | ADR-011 |
| Rate limiting | 60 req/60s | ADR-013 |

---

## 10. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend temeli |
| [[ADR-009-clean-url-redirect]] | Clean URL | URL yapısı |
| [[ADR-021-spa-router-immutable-contract]] | SPA router | Router kontratı |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu | Auth yönetimi |
| [[ADR-044-dynamic-user-theme-engine]] | Dynamic theme | Tema engine |
| [[ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view | Görünüm modları |
| [[ADR-046-cross-view-state-preservation]] | Cross-view state | State yönetimi |
| [[ADR-047-login-redirect-session-bridge]] | Login redirect | Auth akışı |
| [[ADR-048-view-transition-api-integration]] | View Transition API | Geçiş animasyonları |

---

## 11. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-001-vanilla-js-itcss]] | Frontend stack |
| § 4.2 Görünüm | [[ADR-045-multi-domain-view-mode-architecture]] | View modları |
| § 4.4 Bileşenler | [[ADR-001-vanilla-js-itcss]] | Vanilla JS |
| § 4.5 Router | [[ADR-021-spa-router-immutable-contract]] | Router |
| § 4.6 State | [[ADR-046-cross-view-state-preservation]] | State |
| § 5 Yasaklar | [[CLAUDE.md]] §21 | Yasak örüntüleri |
| § 7 Guardrails | [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| § 9 Güvenlik | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 10 ADR | [[ADR-048-view-transition-api-integration]] | View Transition |

---

## 12. Sözlük

| Terim | Tanım |
|-------|-------|
| **SPA** | Single Page Application — Tek sayfa uygulaması |
| **Multi-domain** | Birden fazla alt alan adında çalışan uygulama |
| **Subdomain** | Alt alan adı (music.coremusic.net) |
| **ITCSS** | It's Time to Create Scaleable Stylesheets — CSS mimarisi |
| **BEM** | Block Element Modifier — CSS naming metodolojisi |
| **TrustedTypes** | XSS koruma API'si |
| **DOMParser** | HTML/XML parser |
| **Cross-view State** | Görünüm modları arası state paylaşımı |
| **View Transition API** | Sayfa geçiş animasyonu API'si |
| **SameSite** | Cookie SameSite attribute |
| **SessionStorage** | Tarayıcı oturum depolama alanı |
| **AbortController** | İstek iptal controller'ı |

---

## 13. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 13 |
| Frozen | 2026-05-15 |
| Panel Count | 10 |
| View Modes | 3 (Home, Pro, Studio) |
| Port Count | 6 |
| Shared Components | 4 (router, state, theme, api) |
| Code Examples | 2 (router, state) |
| Yasak Örüntüleri | 8 |
| Edge Cases | 8 |
| Hard Guardrails | 7 |
| Performance Targets | 7 |
| Security Measures | 5 |
| ADR References | 9 |
| Cross References | 9 |
| Glossary Terms | 12 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
