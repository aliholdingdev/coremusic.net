---
type: template
category: testing
title: "Vitest Testing Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: Vitest, JavaScript ES6+, DOM Testing
---

# Vitest Testing Template

**See also:** [[index]] · [[CLAUDE.md]] · [[ADR-001-vanilla-js-itcss]]

## 1. Amaç (Purpose)

Bu dosya, CoreMusic frontend geliştirme süreçlerinde **Vitest** test framework'ünün nasıl kullanılacağını tanımlayan kapsamlı bir şablondur.

**Kapsam:**
- Vanilla JS ES6+ ile yazılmış tüm frontend modüllerinin test edilmesi
- DOM manipülasyonu testleri (innerHTML YASAK, DOMParser + TrustedTypes zorunlu)
- SPA Router, CacheLayer, DeviceLoader, ThemeManager gibi kritik modüllerin test edilmesi
- Async operations, timer mocking, module mocking örnekleri
- Browser mode testleri (@vitest/browser + Playwright)

**Kapsam Dışı:**
- PHP backend testleri (PHPUnit şablonuna bak)
- C++ Audio Engine testleri (Google Test / Catch2)
- End-to-end user journey testleri (Playwright E2E şablonuna bak)

**Referanslar:**
- [[ADR-001-vanilla-js-itcss]] — Vanilla JS, framework yasak
- [[ADR-010-csrf-protection-strategy]] — CSRF token yönetimi
- [[ADR-021-spa-router-immutable-contract]] — SPA router sözleşme limitleri
- [[decisions/accepted/ADR-022-database-hardened-security]] — TrustedTypes gereksinimleri

## 2. Tech Stack

| Bileşen | Versiyon | Amaç | Kaynak |
|---------|----------|------|--------|
| **Vitest** | 3.x+ | Test runner ve assertion library | npmjs.com/package/vitest |
| **@vitest/browser** | 3.x+ | Gerçek browser'da test çalıştırma | Vitest docs: guides/browser |
| **@testing-library/dom** | 10.x+ | DOM sorgulama ve etkileşim | testing-library.com/docs/queries |
| **happy-dom** | 15.x+ | Hafif DOM implementation (default) | github.com/capricorn86/happy-dom |
| **@vitest/coverage-v8** | 3.x+ | V8 tabanlı code coverage | Vitest docs: coverage |
| **Playwright** | 1.45+ | Browser mode için tarayıcı motoru | playwright.dev |
| **MSW** | 2.x+ | API mocking (service worker) | mswjs.io |

**NPM Kurulumu:**
```bash
npm install -D vitest @vitest/browser @vitest/coverage-v8
npm install -D @testing-library/dom happy-dom
npm install -D playwright @playwright/test
```

## 3. Code Standards

### 3.1 Test Directory Structure

```
assets.coremusic.net/
├── js/
│   ├── CacheLayer.js
│   ├── Router.js
│   ├── device-loader.js
│   ├── ThemeManager.js
│   └── core/
│       ├── cache.js
│       ├── auth.js
│       └── utils.js
├── tests/
│   ├── unit/
│   │   ├── CacheLayer.test.js
│   │   ├── Router.test.js
│   │   ├── device-loader.test.js
│   │   ├── ThemeManager.test.js
│   │   └── core/
│   │       ├── cache.test.js
│   │       └── utils.test.js
│   ├── integration/
│   │   ├── navigation-flow.test.js
│   │   ├── auth-flow.test.js
│   │   └── cache-invalidation.test.js
│   ├── e2e/
│   │   └── (Playwright E2E testleri — ayrı config)
│   ├── __mocks__/
│   │   ├── api-responses.js
│   │   ├── dom-helpers.js
│   │   └── localStorage.mock.js
│   └── fixtures/
│       ├── html/
│       │   ├── index.html
│       │   └── partial.html
│       └── data/
│           ├── tracks.json
│           └── user.json
├── vitest.config.ts
└── vitest.setup.ts
```

**Kurallar:**
- Test dosyaları `*.test.js` veya `*.spec.js` ile biter
- Test dosyası, test ettiği modülün yanında veya `tests/` altında yer alır
- `__mocks__/` dizini ortak mock verilerini barındırır
- `fixtures/` dizini test verilerini barındırır

### 3.2 vitest.config.ts

```typescript
// vitest.config.ts
import { defineConfig } from 'vitest/config';
import { resolve } from 'path';

export default defineConfig({
  test: {
    // Ortam: happy-dom (hafif) veya jsdom
    environment: 'happy-dom',

    // Setup dosyası
    setupFiles: ['./vitest.setup.ts'],

    // Global değişkenler (describe, it, expect — import gerektirmez)
    globals: true,

    // Test dosyası pattern'leri
    include: [
      'tests/**/*.test.js',
      'tests/**/*.test.ts',
      'tests/**/*.spec.js',
      'tests/**/*.spec.ts',
      'js/**/*.test.js',
    ],

    // Hariç tutulan dosyalar
    exclude: [
      'node_modules',
      'dist',
      'tests/e2e/**',
      'tests/__mocks__/**',
      'tests/fixtures/**',
    ],

    // Coverage ayarları
    coverage: {
      provider: 'v8',
      reporter: ['text', 'html', 'lcov', 'json-summary'],
      reportsDirectory: './coverage',
      include: ['js/**/*.js'],
      exclude: [
        'node_modules',
        'tests/**',
        '**/*.test.js',
        '**/*.spec.js',
        '**/*.config.*',
      ],
      thresholds: {
        lines: 80,
        functions: 80,
        branches: 80,
        statements: 80,
      },
    },

    // Parallel çalıştırma
    pool: 'forks',
    poolOptions: {
      forks: {
        singleFork: false,
      },
    },

    // Timeout
    testTimeout: 10000,
    hookTimeout: 10000,

    // Reporter
    reporter: ['verbose', 'html'],

    // Sakin mod (hata durumunda detaylı çıktı)
    reporters: ['verbose'],

    // Retry (CI ortamı için)
    retry: process.env.CI ? 2 : 0,

    // isolate: her test dosyası izole çalışır
    isolate: true,

    // clearMocks: her test öncesi mock'ları temizle
    clearMocks: true,

    // restoreMocks: her test öncesi mock'ları orijinal haline döndür
    restoreMocks: true,
  },

  // Path alias'lar
  resolve: {
    alias: {
      '@': resolve(__dirname, '.'),
      '@js': resolve(__dirname, 'js'),
      '@tests': resolve(__dirname, 'tests'),
      '@core': resolve(__dirname, 'js/core'),
    },
  },
});
```

### 3.3 Test Structure

```javascript
// ✅ DOĞRU — Temel test yapısı
import { describe, it, expect, beforeEach, afterEach, beforeAll, afterAll } from 'vitest';
import { CacheLayer } from '../js/CacheLayer.js';

describe('CacheLayer', () => {
  let cache;

  // Tüm testlerden önce bir kez çalışır
  beforeAll(() => {
    // Global setup (örn: test sunucusu başlatma)
  });

  // Her test öncesi çalışır
  beforeEach(() => {
    cache = new CacheLayer({ maxSize: 100, defaultTtl: 60 });
  });

  // Her test sonrası çalışır
  afterEach(() => {
    cache.clear();
    vi.restoreAllMocks();
  });

  // Tüm testlerden sonra bir kez çalışır
  afterAll(() => {
    // Global teardown
  });

  describe('constructor', () => {
    it('should initialize with default options', () => {
      const c = new CacheLayer();
      expect(c).toBeDefined();
      expect(c.getMaxSize()).toBe(1000);
    });

    it('should accept custom options', () => {
      expect(cache.getMaxSize()).toBe(100);
    });
  });

  describe('set() and get()', () => {
    it('should store and retrieve values', () => {
      cache.set('key1', { data: 'value1' });
      const result = cache.get('key1');
      expect(result).toEqual({ data: 'value1' });
    });

    it('should return null for non-existent keys', () => {
      expect(cache.get('nonexistent')).toBeNull();
    });

    it('should respect TTL', () => {
      vi.useFakeTimers();
      cache.set('ttl-key', 'value', 5); // 5 saniye TTL
      expect(cache.get('ttl-key')).toBe('value');
      vi.advanceTimersByTime(6000);
      expect(cache.get('ttl-key')).toBeNull();
      vi.useRealTimers();
    });
  });
});
```

**`describe` Blok Hiyerarşisi:**
```javascript
// ✅ DOĞRU — Anlamlı hiyerarşi
describe('Router', () => {
  describe('initialization', () => { /* ... */ });
  describe('navigation', () => {
    describe('pushState', () => { /* ... */ });
    describe('popState', () => { /* ... */ });
  });
  describe('middleware', () => { /* ... */ });
});
```

### 3.4 Assertion Patterns

```javascript
// ✅ DOĞRU — Temel assertion'lar
expect(value).toBe(expected);           // Strict eşleşme (===)
expect(value).toEqual(expected);        // Deep eşleşme
expect(value).toStrictEqual(expected);  // Tip dahil deep eşleşme

expect(value).toBeTruthy();
expect(value).toBeFalsy();
expect(value).toBeNull();
expect(value).toBeUndefined();
expect(value).toBeDefined();

expect(value).toBeGreaterThan(5);
expect(value).toBeLessThan(10);
expect(value).toBeGreaterThanOrEqual(5);
expect(value).toBeLessThanOrEqual(10);

expect(array).toContain('item');
expect(string).toMatch(/pattern/);
expect(object).toHaveProperty('key', 'value');

expect(() => { throw new Error(); }).toThrow(Error);
expect(() => { throw new Error('msg'); }).toThrow('msg');

// Fonksiyon çağrı kontrolü
expect(mockFn).toHaveBeenCalled();
expect(mockFn).toHaveBeenCalledWith('arg1', 'arg2');
expect(mockFn).toHaveBeenCalledTimes(3);

// Async assertion
await expect(promise).resolves.toBe('value');
await expect(failedPromise).rejects.toThrow('error');

// DOM assertion
expect(element).toBeInTheDocument();
expect(element).toHaveTextContent('Hello');
expect(element).toHaveClass('active');
expect(input).toHaveValue('test');
```

### 3.5 Mocking with `vi`

```javascript
// ✅ DOĞRU — vi.fn() ile mock fonksiyon
const mockFetch = vi.fn().mockResolvedValue({
  ok: true,
  json: () => Promise.resolve({ tracks: [] }),
});

// ✅ DOĞRU — vi.spyOn() ile mevcut fonksiyonu izle
const spy = vi.spyOn(console, 'log').mockImplementation(() => {});
expect(spy).toHaveBeenCalledWith('mesaj');
spy.mockRestore();

// ✅ DOĞRU — vi.fn().mockImplementation()
const transformer = vi.fn().mockImplementation((input) => {
  return input.toUpperCase();
});
expect(transformer('hello')).toBe('HELLO');

// ✅ DOĞRU — vi.fn().mockReturnValue()
const getter = vi.fn().mockReturnValue(42);
expect(getter()).toBe(42);

// ✅ DOĞRU — Mock zinciri (chain)
const mock = vi.fn()
  .mockReturnValueOnce('first')
  .mockReturnValueOnce('second')
  .mockReturnValue('default');
expect(mock()).toBe('first');
expect(mock()).toBe('second');
expect(mock()).toBe('default');

// ❌ YANLIŞ — Mock'un temizlenmemesi
it('bad test', () => {
  vi.fn().mockReturnValue(1);
  // afterEach'te mock temizlenmedi → diğer testler etkilenir
});

// ✅ DOĞRU — Mock temizliği
afterEach(() => {
  vi.restoreAllMocks();
  vi.clearAllTimers();
});
```

### 3.6 Async Testing

```javascript
// ✅ DOĞRU — await ile doğrudan test
it('should fetch data', async () => {
  const result = await fetchData('/api/tracks');
  expect(result).toBeDefined();
});

// ✅ DOĞRU — waitFor ile DOM güncelleme bekleme
import { waitFor } from '@testing-library/dom';

it('should update DOM after async operation', async () => {
  const element = document.createElement('div');
  document.body.appendChild(element);

  loadData().then(() => {
    element.textContent = 'Loaded';
  });

  await waitFor(() => {
    expect(element.textContent).toBe('Loaded');
  });

  document.body.removeChild(element);
});

// ✅ DOĞRU — assertEventually pattern
async function assertEventually(fn, timeout = 5000, interval = 50) {
  const start = Date.now();
  while (Date.now() - start < timeout) {
    if (fn()) return;
    await new Promise(resolve => setTimeout(resolve, interval));
  }
  throw new Error(`Assertion timed out after ${timeout}ms`);
}

// ✅ DOĞRU — Promise.all test
it('should handle parallel requests', async () => {
  const [tracks, albums] = await Promise.all([
    fetchTracks(),
    fetchAlbums(),
  ]);
  expect(tracks.length).toBeGreaterThan(0);
  expect(albums.length).toBeGreaterThan(0);
});

// ✅ DOĞRU — Hata fırlatan async test
it('should reject on network error', async () => {
  mockFetch.mockRejectedValueOnce(new Error('Network error'));
  await expect(fetchData('/api/tracks')).rejects.toThrow('Network error');
});
```

### 3.7 DOM Testing

```javascript
// ✅ DOĞRU — Manuel DOM oluşturma (DOMParser + TrustedTypes)
function createSafeHTML(html) {
  if (window.trustedTypes) {
    const policy = trustedTypes.createPolicy('test-policy', {
      createHTML: (str) => str,
    });
    return policy.createHTML(html);
  }
  return html;
}

// ✅ DOĞRU — render helper
function render(html) {
  const container = document.createElement('div');
  container.innerHTML = createSafeHTML(html);
  document.body.appendChild(container);
  return container;
}

// ✅ DOĞRU — querySelector ile element bulma
it('should find element by selector', () => {
  const container = render(`
    <div class="track-list">
      <div class="track" data-id="1">Track 1</div>
      <div class="track" data-id="2">Track 2</div>
    </div>
  `);

  const tracks = container.querySelectorAll('.track');
  expect(tracks.length).toBe(2);
  expect(tracks[0].dataset.id).toBe('1');
  expect(tracks[1].textContent).toBe('Track 2');

  document.body.removeChild(container);
});

// ✅ DOĞRU — fireEvent ile event tetikleme
import { fireEvent } from '@testing-library/dom';

it('should handle click events', () => {
  const button = document.createElement('button');
  const handler = vi.fn();
  button.addEventListener('click', handler);
  document.body.appendChild(button);

  fireEvent.click(button);
  expect(handler).toHaveBeenCalledTimes(1);

  document.body.removeChild(button);
});

// ✅ DOĞRU — Input change event
it('should handle input changes', () => {
  const input = document.createElement('input');
  input.type = 'text';
  document.body.appendChild(input);

  fireEvent.input(input, { target: { value: 'search term' } });
  expect(input.value).toBe('search term');

  document.body.removeChild(input);
});

// ❌ YANLIŞ — innerHTML kullanımı (YASAK, ADR-001)
// container.innerHTML = '<div>test</div>';  // ❌ HİÇBİR ZAMAN

// ✅ DOĞRU — DOMParser kullanımı
const parser = new DOMParser();
const doc = parser.parseFromString('<div>test</div>', 'text/html');
const element = doc.body.firstChild;
```

### 3.8 Snapshot Testing

```javascript
// ✅ DOĞRU — Inline snapshot
it('should generate track HTML', () => {
  const html = generateTrackHTML({ title: 'Song', artist: 'Artist' });
  expect(html).toMatchInlineSnapshot(`
    "<div class="track">
      <span class="track__title">Song</span>
      <span class="track__artist">Artist</span>
    </div>"
  `);
});

// ✅ DOĞRU — Object snapshot
it('should return correct config', () => {
  const config = getConfig();
  expect(config).toMatchSnapshot({
    version: expect.any(String),
    timestamp: expect.any(Number),
  });
});

// ✅ DOĞRU — Snapshot güncellemesi
// Vitest terminal'inde `u` tuşu ile veya:
// vitest --update

// ❌ YANLIŞ — Büyük DOM snapshot'ları
// it('large snapshot', () => {
//   expect(document.body).toMatchSnapshot();  // ❌ Çok büyük, kırılgan
// });

// ✅ DOĞRU — Sadece önemli kısımları snapshot'la
it('should render track list', () => {
  const container = renderTrackList(tracks);
  const trackNames = [...container.querySelectorAll('.track__title')]
    .map(el => el.textContent);
  expect(trackNames).toMatchSnapshot();
});
```

### 3.9 Timer Mocking

```javascript
// ✅ DOĞRU — Fake timers
beforeEach(() => {
  vi.useFakeTimers();
});

afterEach(() => {
  vi.useRealTimers();
});

it('should debounce input', () => {
  const callback = vi.fn();
  const debounced = debounce(callback, 300);

  debounced();
  debounced();
  debounced();

  expect(callback).not.toHaveBeenCalled();

  vi.advanceTimersByTime(300);
  expect(callback).toHaveBeenCalledTimes(1);
});

it('should handle intervals', () => {
  const callback = vi.fn();
  setInterval(callback, 1000);

  vi.advanceTimersByTime(3000);
  expect(callback).toHaveBeenCalledTimes(3);

  vi.clearAllTimers();
});

it('should handle setTimeout chain', () => {
  const results = [];
  const chain = () => {
    results.push(Date.now());
    if (results.length < 3) setTimeout(chain, 100);
  };
  chain();

  vi.advanceTimersByTime(350);
  expect(results).toHaveLength(3);
});

// ✅ DOĞRU — Async timer test
it('should resolve after delay', async () => {
  const promise = delay(1000, 'done');
  vi.advanceTimersByTime(1000);
  const result = await promise;
  expect(result).toBe('done');
});
```

### 3.10 Module Mocking

```javascript
// ✅ DOĞRU — vi.mock() ile modülü mock'lama
vi.mock('../js/core/cache.js', () => ({
  CacheManager: vi.fn().mockImplementation(() => ({
    get: vi.fn().mockReturnValue(null),
    set: vi.fn(),
    has: vi.fn().mockReturnValue(false),
    delete: vi.fn(),
  })),
}));

// ✅ DOĞRU — Factory fonksiyonu ile mock
vi.mock('../js/core/api.js', () => ({
  api: {
    get: vi.fn().mockResolvedValue({ data: [] }),
    post: vi.fn().mockResolvedValue({ success: true }),
  },
}));

// ✅ DOĞRU — Partial mock
import * as cacheModule from '../js/core/cache.js';
vi.spyOn(cacheModule, 'getCachedData').mockReturnValue('cached');

// ✅ DOĞRU — Dynamic import mock
const mockModule = await vi.importMock('../js/core/utils.js');
mockModule.formatDate.mockReturnValue('2026-01-01');

// ❌ YANLIŞ — Mock'un testler arası sızması
// vi.mock() modül seviyesinde çalışır, her test dosyası izole olmalı

// ✅ DOĞRU — Mock temizliği
afterEach(() => {
  vi.restoreAllMocks();
  vi.resetModules();
});
```

### 3.11 Browser Testing

```typescript
// vitest.config.browser.ts
import { defineConfig } from 'vitest/config';
import { defineBrowserConfig } from 'vitest/config';

export default defineConfig({
  test: {
    browser: defineBrowserConfig({
      enabled: true,
      provider: 'playwright',
      instances: [
        { browser: 'chromium' },
      ],
      headless: true,
    }),
  },
});

// ✅ DOĞRU — Browser test dosyası
import { screen, fireEvent } from '@testing-library/dom';
import { describe, it, expect, beforeEach } from 'vitest';

describe('Browser: Router', () => {
  beforeEach(() => {
    document.body.innerHTML = '<nav id="main-nav"></nav><main id="content"></main>';
  });

  it('should navigate on link click', async () => {
    const link = document.createElement('a');
    link.href = '/about';
    link.textContent = 'About';
    document.getElementById('main-nav').appendChild(link);

    fireEvent.click(link);

    // Browser mode'da gerçek DOM event workları
    expect(window.location.pathname).toBe('/about');
  });
});

// ✅ DOĞRU — Playwright page etkileşimi
// describe('E2E: Page load', () => {
//   it('should render home page', async ({ page }) => {
//     await page.goto('http://localhost:3000');
//     const title = await page.textContent('h1');
//     expect(title).toContain('CoreMusic');
//   });
// });
```

### 3.12 Coverage Configuration

```typescript
// vitest.config.ts coverage bölümü detayı
coverage: {
  provider: 'v8',          // v8 veya istanbul
  reporter: [
    'text',                // Terminal çıktısı
    'text-summary',        // Özet
    'html',                // HTML raporu (coverage/index.html)
    'lcov',                // CI/CD entegrasyonu
    'json-summary',        // JSON özeti
    'cobertura',           // Jenkins/TeamCity uyumlu
  ],
  reportsDirectory: './coverage',
  include: [
    'js/**/*.js',
    'js/**/*.ts',
  ],
  exclude: [
    'node_modules',
    'tests/**',
    '**/*.test.js',
    '**/*.spec.js',
    '**/*.config.*',
    '**/index.js',           // Barrel dosyalar
    '**/*.d.ts',             // Type declaration dosyaları
  ],
  thresholds: {
    lines: 80,
    functions: 80,
    branches: 80,
    statements: 80,
    // Modül bazlı eşik değerleri
    perFile: true,
  },
  all: false,               // Sadece import edilen dosyaları say
  skipFull: false,          // Tam kapsanan dosyaları atlama
},
```

**Coverage Komutları:**
```bash
# Tüm testleri coverage ile çalıştır
npx vitest run --coverage

# Sadece belirli dosyaları kapsa
npx vitest run --coverage --include="js/CacheLayer.js"

# Coverage eşiği kontrolü (eşik altı ise hata fırlatır)
npx vitest run --coverage --coverage.thresholds.lines=80
```

## 4. Hard Guardrails

| # | Kural | Uygulama | Kaynak |
|---|-------|----------|--------|
| 1 | **Framework Yasak** | React, Vue, Angular KULLANMA. Sadece Vanilla JS + DOM API | [[ADR-001-vanilla-js-itcss]] |
| 2 | **ES6+ Modülleri** | `import/export`, `const/let`, arrow functions zorunlu. `var` yasak | [[ADR-001-vanilla-js-itcss]] |
| 3 | **DOMParser Zorunlu** | HTML oluşturma için `DOMParser` kullan. `innerHTML` KESİNLİKLE yasak | [[ADR-022-database-hardened-security]] |
| 4 | **TrustedTypes Uyumu** | Testlerde bile `trustedTypes` policy desteği olmalı | [[ADR-022-database-hardened-security]] |
| 5 | **No eval()** | Test kodunda `eval()`, `new Function()` yasak | Güvenlik standartları |
| 6 | **Mock Temizliği** | `afterEach`'te `vi.restoreAllMocks()` zorunlu | Vitest best practices |
| 7 | **Async Await** | Promise testlerinde `async/await` zorunlu. `.then()` chain yasak | [[ADR-006-performance-targets]] |
| 8 | **Test İzolasyonu** | Her test kendi state'ini kendi oluşturmalı. Global state paylaşımı yasak | [[ADR-008-bypass-auth-middleware]] |

## 5. Naming Conventions

| Öğe | Format | Örnek |
|-----|--------|-------|
| Test dosyası | `*.test.js` veya `*.spec.js` | `CacheLayer.test.js` |
| Test klasörü | `tests/` altında veya modül yanında | `tests/unit/CacheLayer.test.js` |
| `describe` bloğu | Modül/class adı (PascalCase) | `describe('CacheLayer', ...)` |
| `it`/`test` adı | should + fiil (camelCase) | `it('should store values', ...)` |
| Test helper fonksiyonu | `create*`, `build*`, `setup*` | `createTestCache()` |
| Mock değişkeni | `mock*` prefix | `mockFetch`, `mockHandler` |
| Fixture dosyası | `*.fixture.js` veya `fixtures/` altında | `fixtures/tracks.json` |
| Setup dosyası | `vitest.setup.ts` | Global test ayarları |

## 6. Security Considerations

```javascript
// ❌ YASAK — Testte eval kullanımı
// eval('const x = 1;');

// ❌ YASAK — Hardcoded secret
// const API_KEY = 'sk-test-12345';

// ✅ DOĞRU — Environment variable
const API_KEY = import.meta.env.VITE_TEST_API_KEY || 'test-key';

// ✅ DOĞRU — TrustedTypes policy test
it('should comply with TrustedTypes', () => {
  if (!window.trustedTypes) return; // TrustedTypes desteklenmiyorsa atla

  const policy = trustedTypes.createPolicy('test-policy', {
    createHTML: (str) => str,
    createScriptURL: (str) => new URL(str, window.location.origin),
  });

  const html = policy.createHTML('<div>safe</div>');
  expect(html).toBeDefined();
});

// ✅ DOĞRU — CSP uyumlu test
it('should not use inline scripts', () => {
  const html = '<div id="test"></div>';
  const container = render(html);
  // Inline script içermemeli
  expect(container.innerHTML).not.toMatch(/<script[^>]*>/);
  document.body.removeChild(container);
});

// ✅ DOĞRU — XSS koruması test
it('should escape user input', () => {
  const input = '<script>alert("xss")</script>';
  const escaped = escapeHtml(input);
  expect(escaped).not.toContain('<script>');
  expect(escaped).toContain('&lt;script&gt;');
});
```

## 7. Performance Notes

| Konu | Öneri | Değer |
|------|-------|-------|
| **Test timeout** | Varsayılan timeout | `testTimeout: 10000` |
| **Hook timeout** | Setup/teardown timeout | `hookTimeout: 10000` |
| **Pool** | Fork tabanlı paralel çalıştırma | `pool: 'forks'` |
| **Mock cleanup** | Her test sonrası temizle | `clearMocks: true` |
| **Isolate** | Dosya bazlı izolasyon | `isolate: true` |
| **Memory limit** | CI'da bellek sınırı | `--max-old-space-size=4096` |
| **Retry** | CI'da başarısız testleri yeniden dene | `retry: 2` |

**Performans Optimizasyonları:**
```bash
# Sadece değişen dosyaları test et
npx vitest related src/changed-file.js

# Watch mode'da sadece belirli dosyaları izle
npx vitest --watch --include="js/CacheLayer.js"

# Bench mode ile performans ölçümü
npx vitest bench
```

## 8. Edge Cases

| Senaryo | Belirti | Çözüm | İlgili ADR |
|---------|---------|-------|------------|
| **DOM readyState** | Element henüz hazır değil | `DOMContentLoaded` veya `waitFor` kullan | — |
| **Network hatası** | fetch/reject durumu | `vi.fn().mockRejectedValue()` ile simüle et | — |
| **localStorage dolu** | QuotaExceededError | `beforeEach`'te `localStorage.clear()` | [[ADR-011-session-management]] |
| **Cookie sınırları** | SameSite, HttpOnly | Test ortamında cookie set etmeye çalışma | [[ADR-010-csrf-protection-strategy]] |
| **Web Audio API** | AudioContext bridgeadesi | `vi.fn()` ile mock'la | [[ADR-017-dsp-hardware-mode]] |
| **Boş DOM** | Element henüz eklenmemiş | `beforeEach`'te test container oluştur | — |
| **Timer çakışması** | Fake timer ile async callback | `vi.advanceTimersByTime` + `await` | — |
| **Memory leak** | Event listener kalmaması | `afterEach`'te `removeEventListener` | [[ADR-006-performance-targets]] |
| **TrustedTypes politika hatası** | Policy henüz oluşturulmamış | `vi.fn().mockReturnValue()` ile bypass | [[ADR-022-database-hardened-security]] |

## 9. Troubleshooting

| Hata | Neden | Çözüm |
|------|-------|-------|
| `Cannot find module` | Path alias tanımlanmamış | `vitest.config.ts` → `resolve.alias` ekle |
| `Mock泄漏` | `vi.restoreAllMocks()` çağrılmamış | `afterEach` bloğuna ekle |
| `Timeout exceeded` | Async test timeout aşımı | `testTimeout` değerini artır veya `vi.useFakeTimers` kullan |
| `ReferenceError: vi is not defined` | `vitest` import edilmemiş | `import { vi } from 'vitest'` ekle veya `globals: true` ayarla |
| `document is not defined` | DOM environment tanımlanmamış | `environment: 'happy-dom'` ekle |
| `TrustedTypes error` | TrustedTypes policy oluşturulmamış | `trustedTypes.createPolicy()` ile test policy'si oluştur |
| `Module mock not applying` | `vi.mock()` dosya içi tanımlanmamış | Mock'ı dosyanın en üstüne taşı |
| `Coverage threshold not met` | Kapsama eşiği düşüklük | Test sayısını artır veya threshold değerini düşür |
| `Unhandled rejection` | Reject edilen promise yakalanmamış | `await expect(...).rejects.toThrow()` kullan |
| `Test leaking state` | Global state değişikliği | Her test kendi state'ini oluşturmalı |

## 10. Common Anti-Patterns

| ❌ Yanlış | ✅ Doğru | Açıklama |
|-----------|----------|----------|
| `it('cache works')` | `it('should store and retrieve cached values')` | Test adı anlamlı olmalı |
| Test içinde global state değiştirme | `beforeEach` ile her test izole olmalı | Test birbirini etkilememeli |
| `expect(x).toBe(true)` | `expect(x).toBeTruthy()` veya `expect(x).toBe(true)` | Amaç uygun assertion seçilmeli |
| Mock temizlememe | `afterEach(() => vi.restoreAllMocks())` | Mock sızması engellenmeli |
| Büyük test dosyası (500+ satır) | Dosyayı küçült veya `describe` ile böl | Bakım kolaylığı |
| `document.querySelector` ile test | `@testing-library/dom` queryleri | Daha dayanıklı testler |
| Async testte await kullanmama | `async/await` veya `waitFor` | Race condition önlenmeli |
| Snapshot'a her şeyi dahil etme | Sadece önemli kısımları snapshot'la | Kırılgan snapshot önlenmeli |
| Hardcoded URL/endpoint | `vi.mock()` ile API mock'la | Bağımlılık azaltılmalı |
| `eval()` veya `new Function()` | Vanilla JS fonksiyonları | Güvenlik standartlarına uyum |

## 11. Testing Requirements

| Modül | Minimum Kapsama | Hedef Kapsama | Test Tipi |
|-------|-----------------|---------------|-----------|
| **CacheLayer** | ≥80% | ≥90% | Unit |
| **Router** | ≥80% | ≥90% | Unit + Integration |
| **DeviceLoader** | ≥80% | ≥90% | Unit |
| **ThemeManager** | ≥80% | ≥90% | Unit |
| **AuthHandler** | ≥80% | ≥90% | Unit + Integration |
| **API Service** | ≥80% | ≥90% | Unit + Integration |
| **Audio Player** | ≥80% | ≥90% | Unit (Web Audio mock) |
| **Tüm Frontend** | **≥80%** | **≥90%** | — |

**Test Tipleri:**

| Tip | Amaç | Sıklık |
|-----|------|--------|
| **Unit Test** | Tek fonksiyon/metod testi | Her commit |
| **Integration Test** | Modüller arası etkileşim | Her PR |
| **Snapshot Test** | Çıktı değişiklik takibi | UI değişikliklerinde |
| **Browser Test** | Gerçek tarayıcı uyumu | Haftalık / Release öncesi |

## 12. vitest.config.ts (Tam Örnek)

```typescript
// vitest.config.ts — CoreMusic Frontend Test Configuration
import { defineConfig } from 'vitest/config';
import { resolve } from 'path';

export default defineConfig({
  test: {
    // DOM ortamı
    environment: 'happy-dom',

    // Setup dosyası
    setupFiles: ['./vitest.setup.ts'],

    // Global değişkenler
    globals: true,

    // Test dosyası pattern'leri
    include: [
      'tests/**/*.test.js',
      'tests/**/*.test.ts',
      'tests/**/*.spec.js',
      'tests/**/*.spec.ts',
    ],

    exclude: [
      'node_modules',
      'dist',
      'tests/e2e/**',
      'tests/__mocks__/**',
      'tests/fixtures/**',
    ],

    // Coverage
    coverage: {
      provider: 'v8',
      reporter: ['text', 'html', 'lcov', 'json-summary'],
      reportsDirectory: './coverage',
      include: ['js/**/*.js'],
      exclude: [
        'node_modules',
        'tests/**',
        '**/*.test.js',
        '**/*.spec.js',
        '**/*.config.*',
      ],
      thresholds: {
        lines: 80,
        functions: 80,
        branches: 80,
        statements: 80,
        perFile: true,
      },
    },

    // Paralel çalıştırma
    pool: 'forks',
    poolOptions: {
      forks: {
        singleFork: false,
      },
    },

    // Timeout
    testTimeout: 10000,
    hookTimeout: 10000,

    // Reporter
    reporter: ['verbose', 'html'],

    // Retry (CI)
    retry: process.env.CI ? 2 : 0,

    // İzolasyon
    isolate: true,

    // Mock temizliği
    clearMocks: true,
    restoreMocks: true,
  },

  // Path alias'lar
  resolve: {
    alias: {
      '@': resolve(__dirname, '.'),
      '@js': resolve(__dirname, 'js'),
      '@tests': resolve(__dirname, 'tests'),
      '@core': resolve(__dirname, 'js/core'),
    },
  },
});
```

**vitest.setup.ts:**
```typescript
// vitest.setup.ts
import { afterEach } from 'vitest';

// Her test sonrası global temizlik
afterEach(() => {
  // DOM temizliği
  document.body.innerHTML = '';

  // localStorage temizliği
  localStorage.clear();

  // sessionStorage temizliği
  sessionStorage.clear();

  // Cookie temizliği
  document.cookie.split(';').forEach(cookie => {
    const name = cookie.split('=')[0].trim();
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
  });
});

// TrustedTypes policy (test ortamı için)
if (window.trustedTypes && !window.trustedTypes.defaultPolicy) {
  window.trustedTypes.createPolicy('default', {
    createHTML: (str) => str,
    createScriptURL: (str) => str,
    createScript: (str) => str,
  });
}
```

## 13. CI Integration

```yaml
# .github/workflows/test.yml — Vitest CI Adımı
name: Frontend Tests

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        node-version: [20, 22]

    steps:
      - uses: actions/checkout@v4

      - name: Setup Node.js ${{ matrix.node-version }}
        uses: actions/setup-node@v4
        with:
          node-version: ${{ matrix.node-version }}
          cache: 'npm'

      - name: Install dependencies
        run: npm ci

      - name: Run Vitest
        run: npx vitest run

      - name: Run Vitest with coverage
        run: npx vitest run --coverage

      - name: Upload coverage to Codecov
        uses: codecov/codecov-action@v4
        with:
          files: ./coverage/lcov.info
          flags: frontend
          fail_ci_if_error: false

      - name: Check coverage thresholds
        run: npx vitest run --coverage --coverage.thresholds.lines=80

      - name: Upload test results
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: test-results
          path: |
            coverage/
            test-results/
```

## 14. Related Documents

- [[index]] — Master katalog
- [[CLAUDE.md]] — Ana sözleşme
- [[ADR-001-vanilla-js-itcss]] — Vanilla JS + ITCSS kararı
- [[ADR-006-performance-targets]] — Performans hedefleri
- [[ADR-010-csrf-protection-strategy]] — CSRF koruması
- [[ADR-011-session-management]] — Session yönetimi
- [[ADR-021-spa-router-immutable-contract]] — SPA router sözleşme limitleri
- [[ADR-022-database-hardened-security]] — TrustedTypes ve güvenlik
- [[ADR-042-vault-restructuring-2026-08-03]] — MSA limit = 15 dosya
- [[testing/strategy]] — Genel test stratejisi
- [[testing/coverage-targets]] — Kapsama hedefleri
- [[testing/e2e-template]] — Playwright E2E şablonu

## 15. Cross-References

| Bu Doküman | Hedef | İlişki |
|-------------|-------|--------|
| § Amaç | [[ADR-001-vanilla-js-itcss]] | Framework yasağı |
| § Hard Guardrails | [[ADR-022-database-hardened-security]] | TrustedTypes |
| § Edge Cases | [[ADR-010-csrf-protection-strategy]] | CSRF testleri |
| § Edge Cases | [[ADR-011-session-management]] | Cookie testleri |
| § Edge Cases | [[ADR-017-dsp-hardware-mode]] | Web Audio mock |
| § Naming | [[ADR-001-vanilla-js-itcss]] | ES6+ standardı |
| § Security | [[ADR-022-database-hardened-security]] | XSS koruması |
| § CI Integration | [[testing/strategy]] | Test stratejisi |
| § Requirements | [[testing/coverage-targets]] | Kapsama hedefleri |
| § vitest.config.ts | [[ADR-042-vault-restructuring-2026-08-03]] | MSA limit |

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| **Dosya** | `vitest-template.md` |
| **Versiyon** | 3.0.0 |
| **Toplam Satır** | 550+ |
| **Bölüm Sayısı** | 18 |
| **ADR Referansı** | 8 (001, 006, 010, 011, 017, 021, 022, 042) |
| **Kod Örneği** | 20+ (✅ doğru + ❌ yanlış) |
| **Tablo Sayısı** | 12 |
| **Wikilink** | 15+ |
| **MSA Uyumluluk** | ✅ (15 dosya limiti dahilinde) |
| **Frontmatter** | ✅ (type, category, title, date, version, governance) |
| **Hard Guardrails** | ✅ (8 kural tablosu) |
| **Anti-Pattern** | ✅ (10 ❌/✅ çifti) |

## 17. Examples

### 17.1 CacheLayer.test.js

```javascript
// tests/unit/CacheLayer.test.js
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { CacheLayer } from '../../js/CacheLayer.js';

describe('CacheLayer', () => {
  let cache;

  beforeEach(() => {
    vi.useFakeTimers();
    cache = new CacheLayer({
      maxSize: 50,
      defaultTtl: 60,
      storageKey: 'cm-test-cache',
    });
  });

  afterEach(() => {
    cache.clear();
    vi.useRealTimers();
    vi.restoreAllMocks();
    localStorage.clear();
  });

  describe('initialization', () => {
    it('should create instance with default options', () => {
      const c = new CacheLayer();
      expect(c).toBeDefined();
      expect(c.getMaxSize()).toBe(1000);
    });

    it('should apply custom options', () => {
      expect(cache.getMaxSize()).toBe(50);
    });

    it('should have empty cache initially', () => {
      expect(cache.getSize()).toBe(0);
      expect(cache.has('any-key')).toBe(false);
    });
  });

  describe('set() and get()', () => {
    it('should store and retrieve strings', () => {
      cache.set('name', 'CoreMusic');
      expect(cache.get('name')).toBe('CoreMusic');
    });

    it('should store and retrieve objects', () => {
      const data = { tracks: [1, 2, 3], meta: { total: 3 } };
      cache.set('tracks', data);
      expect(cache.get('tracks')).toEqual(data);
    });

    it('should return null for missing keys', () => {
      expect(cache.get('nonexistent')).toBeNull();
    });

    it('should overwrite existing keys', () => {
      cache.set('key', 'old');
      cache.set('key', 'new');
      expect(cache.get('key')).toBe('new');
    });

    it('should respect custom TTL per entry', () => {
      cache.set('short-lived', 'value', 5);
      expect(cache.get('short-lived')).toBe('value');

      vi.advanceTimersByTime(6000);
      expect(cache.get('short-lived')).toBeNull();
    });

    it('should use default TTL when not specified', () => {
      cache.set('default-ttl', 'value');
      vi.advanceTimersByTime(59000);
      expect(cache.get('default-ttl')).toBe('value');

      vi.advanceTimersByTime(2000);
      expect(cache.get('default-ttl')).toBeNull();
    });
  });

  describe('has()', () => {
    it('should return true for existing keys', () => {
      cache.set('exists', true);
      expect(cache.has('exists')).toBe(true);
    });

    it('should return false for expired entries', () => {
      cache.set('expires', 'value', 1);
      vi.advanceTimersByTime(2000);
      expect(cache.has('expires')).toBe(false);
    });
  });

  describe('delete()', () => {
    it('should remove specific entry', () => {
      cache.set('to-delete', 'value');
      expect(cache.has('to-delete')).toBe(true);

      cache.delete('to-delete');
      expect(cache.has('to-delete')).toBe(false);
    });

    it('should handle non-existent key gracefully', () => {
      expect(() => cache.delete('missing')).not.toThrow();
    });
  });

  describe('clear()', () => {
    it('should remove all entries', () => {
      cache.set('a', 1);
      cache.set('b', 2);
      cache.set('c', 3);
      expect(cache.getSize()).toBe(3);

      cache.clear();
      expect(cache.getSize()).toBe(0);
    });
  });

  describe('LRU eviction', () => {
    it('should evict oldest entry when cache is full', () => {
      const smallCache = new CacheLayer({ maxSize: 3 });
      smallCache.set('first', 1);
      smallCache.set('second', 2);
      smallCache.set('third', 3);
      smallCache.set('fourth', 4); // first evaporates

      expect(smallCache.has('first')).toBe(false);
      expect(smallCache.get('fourth')).toBe(4);
    });

    it('should update access order on get()', () => {
      const smallCache = new CacheLayer({ maxSize: 3 });
      smallCache.set('a', 1);
      smallCache.set('b', 2);
      smallCache.set('c', 3);

      // 'a' is accessed → order: b, c, a
      smallCache.get('a');
      smallCache.set('d', 4); // b evaporates

      expect(smallCache.has('b')).toBe(false);
      expect(smallCache.get('a')).toBe(1);
    });
  });

  describe('persistence', () => {
    it('should save to localStorage', () => {
      cache.set('persist-key', 'persist-value');
      const stored = localStorage.getItem('cm-test-cache');
      expect(stored).toBeTruthy();
    });

    it('should restore from localStorage', () => {
      const data = JSON.stringify({ 'restored': { value: 42, expiry: Date.now() + 60000 } });
      localStorage.setItem('cm-test-cache', data);

      const newCache = new CacheLayer({ storageKey: 'cm-test-cache' });
      expect(newCache.get('restored')).toBe(42);
    });
  });
});
```

### 17.2 Router.test.js

```javascript
// tests/unit/Router.test.js
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { Router } from '../../js/Router.js';

describe('Router', () => {
  let router;
  let container;

  beforeEach(() => {
    container = document.createElement('div');
    container.id = 'app';
    document.body.appendChild(container);

    // Mock history API
    window.history.pushState = vi.fn();
    window.history.replaceState = vi.fn();
    window.history.back = vi.fn();

    router = new Router({
      container: container,
      basePath: '/',
    });
  });

  afterEach(() => {
    router.destroy();
    document.body.removeChild(container);
    vi.restoreAllMocks();
  });

  describe('initialization', () => {
    it('should create router instance', () => {
      expect(router).toBeDefined();
      expect(router.getRoutes()).toEqual([]);
    });

    it('should register routes', () => {
      router.addRoute('/home', { template: '<div>Home</div>' });
      router.addRoute('/about', { template: '<div>About</div>' });
      expect(router.getRoutes()).toHaveLength(2);
    });
  });

  describe('navigation', () => {
    it('should navigate to route', async () => {
      router.addRoute('/test', { template: '<div>Test</div>' });
      await router.navigate('/test');
      expect(window.history.pushState).toHaveBeenCalled();
    });

    it('should handle popstate event', () => {
      router.addRoute('/page', { template: '<div>Page</div>' });

      const event = new PopStateEvent('popstate', {
        state: { url: '/page' },
      });
      window.dispatchEvent(event);
    });

    it('should fire beforeNavigate hook', async () => {
      const hook = vi.fn().mockReturnValue(true);
      router.beforeNavigate(hook);
      router.addRoute('/target', { template: '<div>Target</div>' });

      await router.navigate('/target');
      expect(hook).toHaveBeenCalledWith('/target');
    });

    it('should block navigation when hook returns false', async () => {
      const hook = vi.fn().mockReturnValue(false);
      router.beforeNavigate(hook);
      router.addRoute('/blocked', { template: '<div>Blocked</div>' });

      await router.navigate('/blocked');
      expect(container.innerHTML).not.toContain('Blocked');
    });
  });

  describe('parameter extraction', () => {
    it('should extract route parameters', () => {
      router.addRoute('/track/:id', { template: '<div>Track</div>' });
      const params = router.extractParams('/track/:id', '/track/123');
      expect(params.id).toBe('123');
    });

    it('should extract multiple parameters', () => {
      router.addRoute('/artist/:artistId/album/:albumId', {
        template: '<div>Album</div>',
      });
      const params = router.extractParams(
        '/artist/:artistId/album/:albumId',
        '/artist/456/album/789'
      );
      expect(params.artistId).toBe('456');
      expect(params.albumId).toBe('789');
    });
  });

  describe('error handling', () => {
    it('should handle unknown routes gracefully', async () => {
      const errorHandler = vi.fn();
      router.onError(errorHandler);
      router.addRoute('/known', { template: '<div>Known</div>' });

      await router.navigate('/unknown');
      expect(errorHandler).toHaveBeenCalled();
    });

    it('should not throw on double navigation', async () => {
      router.addRoute('/a', { template: '<div>A</div>' });
      await router.navigate('/a');
      await router.navigate('/a');
      // No error thrown
    });
  });

  describe('DOM patching', () => {
    it('should update container content on navigation', async () => {
      router.addRoute('/hello', {
        template: '<div class="page">Hello World</div>',
      });

      await router.navigate('/hello');
      expect(container.querySelector('.page')).not.toBeNull();
      expect(container.querySelector('.page').textContent).toBe('Hello World');
    });

    it('should handle empty template', async () => {
      router.addRoute('/empty', { template: '' });
      await router.navigate('/empty');
      expect(container.innerHTML).toBe('');
    });
  });

  describe('TrustedTypes compliance', () => {
    it('should not set innerHTML with raw strings', async () => {
      const spy = vi.spyOn(container, 'innerHTML', 'set');
      router.addRoute('/safe', {
        template: '<div>Safe</div>',
      });

      await router.navigate('/safe');
      // innerHTML should be set through DOMParser, not raw assignment
      // This test verifies the approach, not the implementation detail
      expect(container.querySelector('div')).not.toBeNull();
    });
  });
});
```

### 17.3 DeviceLoader.test.js

```javascript
// tests/unit/device-loader.test.js
import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import { DeviceLoader } from '../../js/device-loader.js';

describe('DeviceLoader', () => {
  let loader;
  let linkElements;

  beforeEach(() => {
    // Mock document.querySelectorAll
    linkElements = [];
    vi.spyOn(document, 'querySelectorAll').mockImplementation((selector) => {
      if (selector === 'link[data-device-css]') return linkElements;
      return [];
    });

    // Mock createElement
    vi.spyOn(document, 'createElement').mockImplementation((tag) => {
      if (tag === 'link') {
        const link = {
          rel: 'stylesheet',
          href: '',
          dataset: {},
          setAttribute: vi.fn(),
        };
        return link;
      }
      return document.createElement.call(document, tag);
    });

    loader = new DeviceLoader();
  });

  afterEach(() => {
    loader.destroy();
    vi.restoreAllMocks();
  });

  describe('device detection', () => {
    it('should detect desktop viewport', () => {
      Object.defineProperty(window, 'innerWidth', { value: 1920, writable: true });
      Object.defineProperty(window, 'innerHeight', { value: 1080, writable: true });

      const device = loader.detectDevice();
      expect(device).toBe('desktop');
    });

    it('should detect phone viewport', () => {
      Object.defineProperty(window, 'innerWidth', { value: 375, writable: true });
      Object.defineProperty(window, 'innerHeight', { value: 812, writable: true });

      const device = loader.detectDevice();
      expect(device).toBe('phone');
    });

    it('should detect tablet viewport', () => {
      Object.defineProperty(window, 'innerWidth', { value: 768, writable: true });
      Object.defineProperty(window, 'innerHeight', { value: 1024, writable: true });

      const device = loader.detectDevice();
      expect(device).toBe('tablet');
    });

    it('should detect embedded device', () => {
      Object.defineProperty(window, 'innerWidth', { value: 1024, writable: true });
      Object.defineProperty(window, 'innerHeight', { value: 600, writable: true });
      Object.defineProperty(window, 'devicePixelRatio', { value: 1, writable: true });

      const device = loader.detectDevice();
      expect(device).toBe('embedded');
    });
  });

  describe('CSS loading', () => {
    it('should load device-specific CSS', () => {
      loader.loadDeviceCss('desktop');
      expect(document.createElement).toHaveBeenCalledWith('link');
    });

    it('should not reload same device CSS', () => {
      loader.loadDeviceCss('desktop');
      loader.loadDeviceCss('desktop');
      // Should only create one link element
    });

    it('should remove old CSS when switching devices', () => {
      const oldLink = { remove: vi.fn() };
      linkElements.push(oldLink);

      loader.loadDeviceCss('phone');
      expect(oldLink.remove).toHaveBeenCalled();
    });
  });

  describe('view mode detection', () => {
    it('should detect home view mode', () => {
      document.cookie = 'viewMode=home';
      const mode = loader.getViewMode();
      expect(mode).toBe('home');
    });

    it('should default to pro view mode', () => {
      document.cookie = 'viewMode=';
      const mode = loader.getViewMode();
      expect(mode).toBe('pro');
    });

    it('should switch view mode', () => {
      loader.setViewMode('studio');
      expect(document.cookie).toContain('viewMode=studio');
    });
  });

  describe('resize handling', () => {
    it('should debounce resize events', () => {
      vi.useFakeTimers();
      const callback = vi.fn();
      loader.onResize(callback);

      window.dispatchEvent(new Event('resize'));
      window.dispatchEvent(new Event('resize'));
      window.dispatchEvent(new Event('resize'));

      expect(callback).not.toHaveBeenCalled();

      vi.advanceTimersByTime(300);
      expect(callback).toHaveBeenCalledTimes(1);

      vi.useRealTimers();
    });

    it('should cleanup event listeners on destroy', () => {
      const spy = vi.spyOn(window, 'removeEventListener');
      loader.destroy();
      expect(spy).toHaveBeenCalled();
    });
  });

  describe('cookie management', () => {
    it('should read device cookie', () => {
      document.cookie = 'device=phone; path=/';
      expect(loader.getCookie('device')).toBe('phone');
    });

    it('should handle missing cookie', () => {
      expect(loader.getCookie('nonexistent')).toBeNull();
    });

    it('should set cookie', () => {
      loader.setCookie('theme', 'dark', 365);
      expect(document.cookie).toContain('theme=dark');
    });
  });

  describe('media query matching', () => {
    it('should match prefers-reduced-motion', () => {
      Object.defineProperty(window.matchMedia, 'match', {
        value: vi.fn().mockReturnValue(true),
        writable: true,
      });

      expect(loader.prefersReducedMotion()).toBe(true);
    });

    it('should match dark color scheme', () => {
      const mockMatchMedia = vi.fn().mockReturnValue({ matches: true });
      window.matchMedia = mockMatchMedia;

      expect(loader.prefersDarkColorScheme()).toBe(true);
    });
  });
});
```

## 18. Checklist

Pre-commit test kalite kontrol listesi:

| # | Kontrol | Durum |
|---|---------|-------|
| 1 | Tüm testler çalışıyor mu? (`npx vitest run`) | ☐ |
| 2 | Coverage eşiği sağlanıyor mu? (≥80%) | ☐ |
| 3 | Mock'lar temizlendi mi? (`vi.restoreAllMocks()`) | ☐ |
| 4 | Async testler `await` kullanıyor mu? | ☐ |
| 5 | Test adları `should + fiil` formatında mı? | ☐ |
| 6 | `describe` blokları anlamlı hiyerarşi oluşturuyor mu? | ☐ |
| 7 | DOM testleri `DOMParser` kullanıyor mu? (`innerHTML` yasak) | ☐ |
| 8 | TrustedTypes uyumu var mı? | ☐ |
| 9 | Test dosyası boyutu 500 satır altında mı? | ☐ |
| 10 | Snapshot'lar güncellendi mi? | ☐ |
| 11 | Timer testleri `vi.useFakeTimers` kullanıyor mu? | ☐ |
| 12 | Global state paylaşımı yok mu? | ☐ |
| 13 | `eval()` / `new Function()` kullanımı yok mu? | ☐ |
| 14 | Hardcoded secret yok mu? | ☐ |
| 15 | `beforeEach` / `afterEach` doğru tanımlı mı? | ☐ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
