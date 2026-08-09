---
type: adr
category: routing
title: "ADR-021: SPA Router Immutable Contract"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-021: SPA Router Immutable Contract

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Routing
**İlgili Agent:** [[.agents/backend-architect]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunun SPA (Single Page Application) router sözleşme yapısını tanımlar. Route'ların immutability'si, versioning stratejisi, URL normalizasyonu, fallback mekanizmaları ve multi-domain routing kurallarını kapsar. Tüm frontend ve backend routing bu sözleşmeyle bağlıdır.

Bu ADR şu alanları kapsar:
- Route contract formatı
- Immutability kuralları
- URL versioning stratejisi
- URL normalizasyonu
- SPA router yapısı
- History API kullanımı
- Fallback mekanizmaları
- Middleware entegrasyonu
- Guard mekanizmaları
- Lazy loading
- Preloading stratejisi
- Test senaryoları

---

## 2. Bağlam

CoreMusic 10 farklı subdomain'de çalışan bir platformdur:
- `music.coremusic.net` (port 81) — Ana medya paneli
- `admin.coremusic.net` — Yönetim paneli
- `download.coremusic.net` (port 3001) — İndirme servisi
- `media.coremusic.net` (port 5000/6000) — Medya servisi
- `auth.coremusic.net` — Kimlik doğrulama
- `home.coremusic.net` — Ev merkezi
- `car.coremusic.net` — Araç içi
- `studio.coremusic.net` — Stüdyo
- `pro.coremusic.net` — Profesyonel
- `coremusic.net` — Landing

Her panel için SPA routing gerekir. Route'lar değişirse mevcut URL'ler bozulur, kullanıcılar 404 hatası alır ve SEO sıralaması düşer.

### 2.1 Routing Gereksinimleri

| Gereksinim | Değer | Kaynak |
|------------|-------|--------|
| Immutability | Mevcut route'lar değişmez | ADR-021 |
| Versioning | URL'de versiyon bilgisi | ADR-016 |
| Fallback | 404 sayfası zorunlu | ADR-009 |
| History API | pushState kullanımı | ADR-004 |
| Clean URL | SEO uyumlu URL yapısı | ADR-009 |

---

## 3. Karar

CoreMusic'te **immutable contract** ile SPA router kullanılacak. Route tanımları bir kez oluşturulduktan sonra değiştirilemez; sadece yeni route'lar eklenebilir.

### 3.1 Temel Prensipler

| Prensip | Açıklama | ADR |
|---------|----------|-----|
| Immutability | Mevcut route'lar değişmez | Bu ADR |
| Versioning | URL'de versiyon bilgisi | ADR-016 |
| Fallback | 404 sayfası zorunlu | ADR-009 |
| History API | pushState kullanımı | ADR-004 |
| Clean URL | SEO uyumlu URL yapısı | ADR-009 |

### 3.2 Route Contract Formatı

```javascript
const routeContract = {
  path: '/songs/:id',
  method: 'GET',
  version: 'v1',
  params: { id: 'number' },
  query: { format: 'string', quality: 'string' },
  auth: true,
  roles: ['user', 'premium', 'admin'],
  cache: { ttl: 3600, scope: 'user' },
  middleware: ['session', 'auth', 'rate-limit'],
  immutable: true  // Değiştirilemez
};
```

---

## 4. Teknik Detaylar

### 4.1 Route Versiyonlama Stratejisi

#### 4.1.1 URL Path Versioning (Birincil)

```
/api/v1/songs/:id
/api/v2/songs/:id
```

| Versiyon | Durum | Süre | Aksiyon |
|----------|-------|------|---------|
| v1 | Active | 12 ay | Tam destek |
| v2 | Active | — | Güncel |
| v0 | Deprecated | 6 ay | Uyarı header'ı |
| — | Sunset | 3 ay | 410 Gone |

#### 4.1.2 Header Versioning (İkincil)

```
Accept: application/vnd.coremusic.v1+json
```

#### 4.1.3 Version Conflict Handling

| Durum | Çözüm |
|-------|-------|
| Desteklenmeyen version | 406 Not Acceptable |
| Deprecated version | 200 + `Sunset` header |
| Eksik version | Varsayılan version kullanılır |

### 4.2 URL Normalizasyonu

#### 4.2.1 Normalizasyon Kuralları

| Kural | Örnek | Sonuç |
|-------|-------|-------|
| Trailing slash | `/songs/123/` | `/songs/123` |
| Case insensitive | `/Songs/123` | `/songs/123` |
| Double slash | `/songs//123` | `/songs/123` |
| Encoded chars | `/songs/%31%32%33` | `/songs/123` |
| Query sırası | `?b=2&a=1` | `?a=1&b=2` |

#### 4.2.2 Redirect Kuralları

| Kaynak | Hedef | HTTP Code |
|--------|-------|-----------|
| `/songs/123/` | `/songs/123` | 301 |
| `/Songs/123` | `/songs/123` | 301 |
| `/songs//123` | `/songs/123` | 301 |
| `http://` | `https://` | 301 |

### 4.3 SPA Router Yapısı

#### 4.3.1 Router Katmanları

```
┌─────────────────────────────────────┐
│ Layer 1: Domain Router              │
│ Subdomain'e göre panel seçimi       │
├─────────────────────────────────────┤
│ Layer 2: Path Router                │
│ URL path'e göre sayfa seçimi        │
├─────────────────────────────────────┤
│ Layer 3: Component Router           │
│ Sayfa içinde component değişimi     │
├─────────────────────────────────────┤
│ Layer 4: Data Router                │
│ Query parametrelerine göre veri     │
└─────────────────────────────────────┘
```

#### 4.3.2 Route Tanımlama

```javascript
// Layer 1: Domain
const domainRoutes = {
  'music.coremusic.net': MusicPanel,
  'admin.coremusic.net': AdminPanel,
  'download.coremusic.net': DownloadPanel,
  // ...
};

// Layer 2: Path
const pathRoutes = {
  '/': HomePage,
  '/songs': SongsPage,
  '/songs/:id': SongDetailPage,
  '/albums': AlbumsPage,
  '/albums/:id': AlbumDetailPage,
  '/artists': ArtistsPage,
  '/artists/:id': ArtistDetailPage,
  '/playlists': PlaylistsPage,
  '/playlists/:id': PlaylistDetailPage,
  '/settings': SettingsPage,
  '/search': SearchPage,
};

// Layer 3: Component (sayfa içinde)
const componentRoutes = {
  'player': PlayerComponent,
  'sidebar': SidebarComponent,
  'modal': ModalComponent,
};
```

### 4.4 History API Kullanımı

#### 4.4.1 pushState Kullanımı

```javascript
// Navigasyon
history.pushState({ page: 'songs', id: 123 }, '', '/songs/123');

// Popstate dinleme
window.addEventListener('popstate', (event) => {
  if (event.state) {
    renderPage(event.state.page, event.state.id);
  }
});
```

#### 4.4.2 History Kuralları

| Kural | Değer | İhlal Sonucu |
|-------|-------|-------------|
| pushState zorunlu | Hash-based yasak | SEO düşüşü |
| State object | JSON serializable | Hata |
| Title parametresi | Boş string | Uyarı |
| ReplaceState | Sadece aynı sayfa için | Geçmiş bozulması |

### 4.5 Fallback Mekanizması

#### 4.5.1 404 Sayfası

```javascript
// Tanımsız route'lar için
const NotFoundPage = {
  template: '<h1>404</h1><p>Sayfa bulunamadı</p>',
  component: NotFoundComponent,
  meta: { title: '404 - Sayfa Bulunamadı' }
};
```

#### 4.5.2 Fallback Kuralları

| Durum | Aksiyon | HTTP Code |
|-------|---------|-----------|
| Tanımsız path | 404 sayfası | 404 |
| Yetkisiz erişim | Login redirect | 401 |
| Süresi dolmuş session | Auth sayfası | 401 |
| Rate limit | Uyarı sayfası | 429 |
| Sunucu hatası | 500 sayfası | 500 |

#### 4.5.3 404 Sayfası Gereksinimleri

| Gereksinim | Değer |
|-----------|-------|
| Animasyon | Yok (basit) |
| Arama kutusu | Evet |
| Ana sayfa linki | Evet |
| Son sayfalar | Son 5 sayfa |
| SEO meta | `noindex` |
| Geri sayım | 10sn sonra ana sayfa |

### 4.6 Middleware Entegrasyonu

#### 4.6.1 Route-Based Middleware

```javascript
const routeMiddleware = {
  '/admin/*': ['auth', 'rbac', 'audit'],
  '/api/*': ['rate-limit', 'auth', 'cors'],
  '/auth/*': ['rate-limit', 'csrf'],
  '/media/*': ['auth', 'cache'],
  '/download/*': ['auth', 'rate-limit', 'quota'],
};
```

#### 4.6.2 Middleware Sırası (Backend)

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

Bu sıra DEĞİŞTİRİLEMEZ. [[ADR-010-csrf-protection-strategy]] göreli.

### 4.7 Guarded Routes

#### 4.7.1 Auth Guard

```javascript
const authGuard = (to, from, next) => {
  if (requiresAuth(to) && !isAuthenticated()) {
    next({ path: '/login', query: { redirect: to.fullPath } });
  } else {
    next();
  }
};
```

#### 4.7.2 Role Guard

```javascript
const roleGuard = (to, requiredRoles) => {
  const userRoles = getUserRoles();
  const hasRole = requiredRoles.some(role => userRoles.includes(role));
  if (!hasRole) {
    return { path: '/forbidden' };
  }
};
```

#### 4.7.3 Guard Kuralları

| Guard | Kullanım | Reddurum |
|-------|----------|----------|
| Auth Guard | Login zorunlu sayfalar | `/login?redirect=` |
| Role Guard | Admin sayfaları | `/forbidden` |
| Subscription Guard | Premium içerikler | `/upgrade` |
| Rate Guard | Hassas endpoint'ler | 429 response |

### 4.8 Lazy Loading

#### 4.8.1 Route-Based Lazy Loading

```javascript
const routes = [
  {
    path: '/songs',
    component: () => import(/* webpackChunkName: "songs" */ './pages/SongsPage.vue')
  },
  {
    path: '/albums',
    component: () => import(/* webpackChunkName: "albums" */ './pages/AlbumsPage.vue')
  }
];
```

#### 4.8.2 Lazy Loading Kuralları

| Kural | Değer | İhlal Sonucu |
|-------|-------|-------------|
| Chunk isimlendirme | `webpackChunkName` zorunlu | Debug zorluğu |
| Prefetch | Above-the-fold olmayanlar | Performans düşüşü |
| Loading state | Skeleton screen | UX düşüşü |
| Error boundary | Chunk yükleme hatası | Sayfa bozulması |

### 4.9 Preloading Stratejisi

#### 4.9.1 Preload Tipleri

| Tip | Kullanım | Örnek |
|-----|----------|-------|
| Route preload | Kullanıcı yakınsa | Hover'da preload |
| Data preload | Sayfa açılmadan önce | API prefetch |
| Asset preload | Kritik kaynaklar | CSS, font preload |
| DNS preload | External kaynaklar | API domain'leri |

#### 4.9.2 Preload Kuralları

```javascript
// Route preloading
const preloadRoute = (path) => {
  if (isUserNearRoute(path)) {
    import(`./pages/${path}.js`);
  }
};

// Data preloading
const preloadData = (songId) => {
  prefetch(`/api/v1/songs/${songId}`);
  prefetch(`/api/v1/songs/${id}/lyrics`);
};
```

### 4.10 Test Senaryoları

```javascript
// Route contract testi
describe('SPA Router Contract', () => {
  test('immutable route cannot be modified', () => {
    const route = { path: '/songs/:id', immutable: true };
    expect(() => {
      route.path = '/changed';
    }).toThrow();
  });

  test('pushState updates URL without reload', () => {
    history.pushState({ page: 'songs' }, '', '/songs/123');
    expect(window.location.pathname).toBe('/songs/123');
  });

  test('404 fallback triggers for unknown routes', () => {
    const result = router.resolve('/unknown');
    expect(result.component).toBe(NotFoundPage);
  });

  test('auth guard redirects unauthenticated users', () => {
    const result = authGuard({ path: '/admin' }, null, () => {});
    expect(result.path).toBe('/login');
  });

  test('URL normalization removes trailing slash', () => {
    const normalized = normalizeUrl('/songs/123/');
    expect(normalized).toBe('/songs/123');
  });
});
```

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| Route değiştirme | Sadece ekleme (immutable) | URL kırılması |
| Hash-based routing (`#/`) | pushState (clean URL) | SEO düşüşü |
| Hardcoded URL'ler | Named routes | Bakım zorluğu |
| Sync route loading | Async lazy loading | Performans düşüşü |
| Console'da route bilgisi | Structured logging | Debug riski |
| Catch-all route | Tanımlı fallback | Güvenlik açığı |
| State mutation | Immutable state | Hata |
| Nested deep routes | Max 3 level | Karmaşıklık |
| Unprotected admin routes | Auth + Role guard | Yetkisiz erişim |
| Version olmayan API | URL'de version | Breaking change |

---

## 6. Edge Cases

| Edge Case | Tetikleyici | Çözüm |
|-----------|-------------|-------|
| Double back-button | Hızlı tıklama | Debounce (300ms) |
| Deep link refresh | Sayfa yenileme | Server-side render fallback |
| Concurrent navigations | Eşzamanlı route değişimi | Queue + debounce |
| Route change mid-fetch | API isteği devam ederken | AbortController |
| Browser back/forward | History değişimi | Popstate handler |
| Hash fragment | `#section` anchor | Scroll to element |
| Query param race | Hızlı query değişimi | Debounce (200ms) |
| Invalid route params | Yanlış parametre | 404 fallback |
| Cross-origin navigation | External link | Full page load |
| Service worker cache | Eski cache | Versioned cache keys |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Route immutable — Mevcut route'lar değişmez | URL kırılması |
| 2 | pushState zorunlu — Hash routing yasak | SEO düşüşü |
| 3 | 404 fallback zorunlu — Tanımsız route'lar için sayfa | Kullanıcı kaybı |
| 4 | Version URL'de — API versioning zorunlu | Breaking change |
| 5 | Clean URL — Trailing slash, double slash temizleme | SEO düşüşü |
| 6 | Auth guard — Protected route'lar için zorunlu | Yetkisiz erişim |
| 7 | Middleware sırası — Backend'de değişmez (ADR-010) | CSP/CSRF bozulması |
| 8 | Lazy loading — Tüm route'lar için async | Performans düşüşü |
| 9 | Error boundary — Her route için | Sayfa bozulması |
| 10 | State immutable — Route state değişmez | Hata |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA mimarisi | Subdomain routing |
| [[ADR-009-clean-url-redirect]] | Clean URL redirect | URL normalizasyonu |
| [[ADR-010-csrf-protection-strategy]] | CSRF koruma | Middleware sırası |
| [[ADR-011-session-management]] | Session yönetimi | Auth guard |
| [[ADR-016-url-normalization]] | URL normalizasyonu | Kurallar |
| [[ADR-020-api-public-security]] | API güvenlik | API versioning |
| [[ADR-022-database-hardened-security]] | DB güvenlik | Veri güvenliği |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma | Route contract standardı |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-004-multi-domain-spa]] | Multi-domain mimari |
| § 4.1 Version | [[ADR-016-url-normalization]] | URL kuralları |
| § 4.2 Normalizasyon | [[ADR-009-clean-url-redirect]] | Redirect stratejisi |
| § 4.3 Router | [[architecture/l2-routing]] | SPA PageRouter |
| § 4.5 Fallback | [[ADR-009-clean-url-redirect]] | 404 sayfası |
| § 4.6 Middleware | [[ADR-010-csrf-protection-strategy]] | Middleware sırası |
| § 4.7 Guard | [[ADR-011-session-management]] | Session-based auth |
| § 5 Yasak | [[ADR-001-vanilla-js-itcss]] | Frontend standartları |
| § 7 Guardrails | [[CLAUDE.md]] §7 | Hard guardrails |
| § 8 ADR | [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **SPA** | Single Page Application — Tek sayfa uygulaması |
| **pushState** | History API ile URL değiştirme (sayfa yenileme olmadan) |
| **Popstate** | Back/forward butonlarında tetiklenen olay |
| **Immutable** | Değiştirilemez, sabit |
| **Route Contract** | Route tanımlama sözleşmesi |
| **Lazy Loading** | İhtiyaç anında yükleme |
| **Prefetch** | Önceden veri çekme |
| **Guard** | Route erişim kontrol mekanizması |
| **Middleware** | İstek/yanıt aşama arası işleyici |
| **Fallback** | Varsayılan yedek mekanizma |
| **Clean URL** | SEO uyumlu, parametresiz URL |
| **Hash Routing** | `#/path` formatında routing (yasak) |
| **Debounce** | Çoklu tetiklemeyi tek'e indirgeme |
| **AbortController** | API isteğini iptal etme |
| **Error Boundary** | Hata yakalama mekanizması |
| **Preloading** | Önceden yükleme stratejisi |
| **Versioning** | Versiyonlama — API version yönetimi |
| **Domain Router** | Subdomain bazlı yönlendirme |
| **Path Router** | URL path bazlı yönlendirme |
| **Component Router** | Component bazlı yönlendirme |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| SSOT Authority | ADR-021 SPA Router Immutable Contract |
| Last Updated | 2026-08-08 |
| ADR References | 8 |
| Cross References | 10 |
| Edge Cases | 10 |
| Hard Guardrails | 10 |
| Forbidden Patterns | 10 |
| Glossary Terms | 20 |
| Router Layers | 4 (Domain, Path, Component, Data) |
| Version Lifecycle | 4 (Active, Deprecated, Sunset, Removed) |
| Guard Types | 4 (Auth, Role, Subscription, Rate) |
| Middleware Count | 6 (SessionManager → Csrf) |
| Fallback Scenarios | 5 |
| Preload Types | 4 |
| Lazy Loading Rules | 4 |
| Test Senaryosu | 5 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Last Updated | 2026-08-08 |
| Mode | Red Team · Human Mode · Truth Mode |
| Governance | Single Source of Truth (SSOT) |
| Immutability | Frozen — Değiştirilemez |
| İstisna | Sadece hayati güvenlik hatası |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode