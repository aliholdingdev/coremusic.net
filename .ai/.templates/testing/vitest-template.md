---
title: "Vitest Frontend Test Template — JS/React Test Şablonu"
type: template
category: testing
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# Vitest Frontend Test Template — JS/React Test Şablonu

**Zorunlu Bağlantılar:** [[../../index]] · [[../../brain]] · [[../../.templates/index]] · [[../../.templates/frontend/js-template]]

---

## §1 Amaç ve Honest Scope

Bu şablon **hedef/kurulum rehberidir** — çünkü disk kanıtı şunu gösterir:

| Kanıt (glob) | Değer |
|---|---|
| `vitest` bağımlılığı | `package.json`'da **YOK** (yalnız `playwright ^1.62.1`) |
| `vitest.config.*` | YOK |
| `*.test.js` / `*.spec.js` | `assets.coremusic.net/js/**` içinde YOK |
| Mevcut frontend testi | Playwright E2E (`frontend/`, `shared/tests/browser/`) |
| Test hedefi JS | `assets.coremusic.net/js/` (core/router/features/…) |

```bash
# doğrulama (kurulum öncesi boş döner)
Get-ChildItem -Recurse -Include vitest.config.*,*.test.js | Measure-Object   # 0
```

⚠️ Vitest ADR-086 kararında "unit test" başlığı altında anılmıştır; paket kurulana kadar `VERIFICATION REQUIRED`.

---

## §2 Frontmatter / Değişkenler

| Değişken | Açıklama | Örnek |
|---|---|---|
| `{{MODULE_PATH}}` | test edilen modül | `../src/core/helper.js` |
| `{{FUNCTION_NAME}}` | dışa aktarılan fonksiyon | `formatDuration` |
| `{{SPEC_NAME}}` | `{ad}.test.js` | `helper.test.js` |

---

## §3 Kurulum (hedef durum)

```bash
npm i -D vitest @vitest/coverage-v8    # + jsdom/happy-dom isteğe bağlı
```

```ts
// vitest.config.ts
import { defineConfig } from 'vitest/config';

export default defineConfig({
  test: {
    environment: 'jsdom',          // DOM yoksa 'node'
    include: ['assets.coremusic.net/js/**/*.test.js', 'src/**/*.test.{js,ts}'],
    coverage: { provider: 'v8', reporter: ['text', 'html'] },
  },
});
```

```json
// package.json scripts (hedef)
"test": "vitest run",
"test:watch": "vitest",
"test:coverage": "vitest run --coverage"
```

---

## §4 Dosya İskeleti

```js
// {{SPEC_NAME}}
import { describe, it, expect, beforeEach, vi, afterEach } from 'vitest';
import { {{FUNCTION_NAME}} } from '{{MODULE_PATH}}';

describe('{{FUNCTION_NAME}}', () => {
  beforeEach(() => { vi.useFakeTimers(); });
  afterEach(() => { vi.useRealTimers(); vi.restoreAllMocks(); });

  it('verilen girdiyle bekleneni döndürür', () => {
    expect({{FUNCTION_NAME}}('01:30')).toBe('1:30');
  });

  it('geçersiz girdide fallback döner', () => {
    expect({{FUNCTION_NAME}}('')).toBe('0:00');
  });

  it('yayın (publish) çağrısını yakalar', () => {
    const spy = vi.fn();
    // EventBus benzeri: EventBus.on('route:change', spy)
    expect(spy).toHaveBeenCalledTimes(1);   // Arrange-Act sonrası
  });
});
```

---

## §5 API Tablosu

| API | Amaç | Not |
|---|---|---|
| `describe/it` | grup + durum | `it.only` geçici |
| `expect().toBe` | katı eşitlik | nesnelerde `toEqual` |
| `vi.fn()` | spy/mock | `mockResolvedValue` async |
| `vi.spyOn(obj,'m')` | mevcut metodu sarmala | `restoreAllMocks` |
| `vi.useFakeTimers()` | zaman/raf kontrolü | `advanceTimersByTime` |
| `vi.mock(path)` | modül mock | factory zorunlu |
| `expect(...).toThrow()` | hata yolu | mesaj assert |

---

## §6 Doğrulama & Hata Masası

| # | Adım | Beklenen |
|---|---|---|
| 1 | `npx vitest run` | 0 fail |
| 2 | Coverage (js alt hedef) | kritik `core/` için eşik (ADR-086 %80 api; js için hedef belirlenecek) |
| 3 | DOM testi | `environment: 'jsdom'` |
| 4 | CI | .github/workflows YOK — CI kurulana kadar yerel koş |

| Hata | Neden | Çözüm |
|---|---|---|
| `document is not defined` | node ortamı | `environment: 'jsdom'` + paket kur |
| modül bulunamadı | yol/ESM | `vi.mock` + uzantı/alias |
| sahte zaman sızması | restore unutuldu | `afterEach` restore |
| E2E ile karışıklık | Playwright ayrı katman | unit = vitest, e2e = playwright |

---

## §7 Test Kütüphanesi Galerisi (hedef — js/ yapısına göre)

### §7.1 EventBus (`js/core/EventBus.js`)

```js
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { EventBus } from '../../assets.coremusic.net/js/core/EventBus.js';

describe('EventBus', () => {
  let bus;
  beforeEach(() => { bus = new EventBus(); });

  it('publish, abone fonksiyonunu çağırır', () => {
    const spy = vi.fn();
    bus.on('route:change', spy);
    bus.publish('route:change', { path: '/x' });
    expect(spy).toHaveBeenCalledTimes(1);
    expect(spy).toHaveBeenCalledWith({ path: '/x' });
  });

  it('off sonrası çağrı durur', () => {
    const spy = vi.fn();
    const id = bus.on('e', spy);
    bus.off(id);
    bus.publish('e', {});
    expect(spy).not.toHaveBeenCalled();
  });
});
```

### §7.2 Router (`js/router/` — 29 dosya)

```js
import { describe, it, expect, vi } from 'vitest';

describe('router matcher', () => {
  it('dinamik segment yakalar', () => {
    const match = matchRoute('/user/:id', '/user/42');
    expect(match).toEqual({ id: '42' });
  });

  it('eşleşmeyen yolda null döner', () => {
    expect(matchRoute('/user/:id', '/about')).toBeNull();
  });
});
```

### §7.3 Helper (`js/core/helper.js`)

```js
describe('formatDuration', () => {
  it.each([
    ['01:30', '1:30'],
    ['0:05', '0:05'],
    ['', '0:00'],
  ])('%s → %s', (input, expected) => {
    expect(formatDuration(input)).toBe(expected);
  });
});
```

### §7.4 DOM Bileşeni (jsdom)

```js
/**
 * @vitest-environment jsdom
 */
import { describe, it, expect, beforeEach } from 'vitest';

describe('footer init', () => {
  beforeEach(() => {
    document.body.innerHTML = '<footer id="site-footer"></footer>';
  });

  it('yıl damgasını yazar', () => {
    initFooter(document.getElementById('site-footer'));
    expect(document.querySelector('#site-footer').textContent)
      .toContain(String(new Date().getFullYear()));
  });
});
```

### §7.5 localStorage / fetch Sahtesi

```js
it('token saklar', () => {
  const store = {};
  vi.spyOn(Storage.prototype, 'setItem').mockImplementation((k, v) => { store[k] = v; });
  saveToken('abc');
  expect(store.token).toBe('abc');
});

it('API 401'de hata fırlatır', async () => {
  vi.stubGlobal('fetch', vi.fn().mockResolvedValue({ ok: false, status: 401 }));
  await expect(fetchMe()).rejects.toThrow();
});
```

---

## §8 Dosya Adlandırma & Konumlandırma

| Kural | Değer |
|---|---|
| Dosya adı | `{modül}.test.js` — modülle yan yana veya `__tests__/` |
| Konum | `assets.coremusic.net/js/**/__tests__/` (ci tarafından include) |
| İçerik | 1 dosya = 1 modül; her `describe` = bir kamu davranışı |
| İsim | `{verilen}/{beklenen}` Türkçe veya `{behaviour}_when_{}_{}` |
| `it.only` | geçici — commit öncesi kaldır (CI fail) |
| Süre | realtime `setTimeout` yasak — fake timer |

```bash
npx vitest run assets.coremusic.net/js/router      # klasör filtresi
npx vitest --reporter=verbose                       # tek tek durum
```

---

## §9 CI Entegrasyonu (hedef — workflows YOK)

```yaml
# .github/workflows/ci.yml içine (onay sonrası — §4.1 #4)
  js-unit:
    name: JS Unit (Vitest)
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
      - run: npm ci || npm install
      - run: npx vitest run --coverage
```

| Bağımlılık | Durum |
|---|---|
| vitest kurulumu | YAPILMADI (`package.json` yalnız playwright) |
| coverage eşik | hedef belirlenecek (ADR-086 api %80; js için karar) |
| CI job | `.github/workflows/` YOK — onay akışı §4.1 |

---

## §10 Doğrulama Checklist (geniş)

| # | Adım | Beklenen |
|---|---|---|
| 5 | `npx vitest run` | 0 fail, 0 timeout |
| 6 | `--coverage` | dosya bazlı rapor üretiliyor |
| 7 | `only` temizliği | `grep -r "it.only"` boş |
| 8 | Fake timer temizliği | suite sonu `useRealTimers` |
| 9 | Playwright çakışması | unit ≠ e2e; ikisi ayrı script |
| 10 | Node sürümü | ≥ 20 (ci.yml `NODE_VERSION`) |

---

## §11 Hata Masası (geniş)

| Hata | Konum | Neden | Çözüm |
|---|---|---|---|
| `document is not defined` | DOM testi | ortam node | `@vitest-environment jsdom` + paket |
| `Cannot find module` | import yolu | uzantı/alias | `resolve.alias` config |
| `Leak fake timer` (sahte zaman sızması) | timer testi | restore yok | `afterEach` restore |
| `expect(...).rejects` fail | async | await yok | `await expect(p).rejects` |
| Test koşmuyor | include deseni | yanlış glob | vitest.config `include` |
| `window.matchMedia` yok | jsdom | eksik API | `vi.stubGlobal` |
| E2E de tetiklenir | vitest config | `include` Playwright dosyalarını yuttu | include sadece `*.test.js` |

---

## §12 Test Verisi & İzolasyon

| Kural | Uygulama |
|---|---|
| Sıfır durum | `beforeEach` yeni EventBus/Router |
| Saat | `vi.useFakeTimers()` + `setSystemTime` |
| Global pencere | `vi.stubGlobal` + `vi.unstubAllGlobals` |
| fetch/XHR | `vi.fn()` sahte, gerçek ağ YASAK |
| localStorage | `Storage.prototype` spy (§7.5) |
| Modül durumu | `vi.resetModules()` (modül içi state için) |

```js
beforeEach(() => {
  vi.useFakeTimers();
  vi.setSystemTime(new Date('2026-09-23T12:00:00Z'));
});
afterEach(() => {
  vi.useRealTimers();
  vi.unstubAllGlobals();
  vi.restoreAllMocks();
});
```

### §12.1 Parametrize (it.each) deseni

```js
it.each`
  input        | expected
  ${'01:30'}   | ${'1:30'}
  ${'0:05'}    | ${'0:05'}
  ${''}        | ${'0:00'}
  ${'bad'}     | ${'0:00'}
`('$input → $expected', ({ input, expected }) => {
  expect(formatDuration(input)).toBe(expected);
});
```

---

## §13 Performans & Bütçe

| Metrik | Hedef | Ölçüm |
|---|---|---|
| Suite süresi | < 20 sn | vitest özeti |
| Tek test | < 500 ms | timeout uyarısı |
| Coverage (js) | hedef belirlenecek | `--coverage` html |
| `only` kalıntısı | 0 | grep (§10 #7) |
| Sahte oranı | makul | kod incelemesi |

```bash
npx vitest run --coverage --reporter=json --outputFile=build/vitest.json
```

---

## §14 CI Köprüsü (bağlantı)

| Konu | Referans |
|---|---|
| `js-unit` job taslağı | [[../infrastructure/github-actions-template]] §12 (CI §8.4 artefakt) |
| JS statik kontrol (şu an) | `node --check` — `package.json` yalnız playwright |
| Backend birimi | [[phpunit-template]] |
| Şablon envanteri | [[../index]] |

```
kod → vitest run (yerel) → [workflows kurulunca] js-unit job → PR check
```

⚠️ `.github/workflows/` diskte YOK (§9) — bu bağlantı **hedef** akıştır.

---

## §15 Öncelik Sırası (yazım sırası)

```
1. Regresyon (yaşanan JS hatası)     → ilk yazılır
2. Event/akış (publish → DOM/durum)
3. Kenar durum (boş, tek, uzun)
4. Mutlu yol
5. Timer/async zamanlama
```

| Kural | Aksiyon |
|---|---|
| `core/`, `router/` her modül | en az 1 dosya test |
| Bug fix | o hata için test zorunlu |
| Bileşen render | jsdom + `document` assert |
| Test adı | `{konu}_{durum}_{beklenen}` |

---

## §16 Sık Yazılan Test Parçaları

### §16.1 Süre / tıklama (jsdom)

```js
it('tıklama sayaacı 1 artırır', async () => {
  document.body.innerHTML = '<button id="b">0</button>';
  wireCounter(document.getElementById('b'));
  const btn = document.getElementById('b');
  btn.click(); btn.click();
  expect(btn.textContent).toBe('2');
});
```

### §16.2 Debounce (fake timer)

```js
it('debounce 200 ms sonra tek çalışır', () => {
  const spy = vi.fn();
  const d = debounce(spy, 200);
  d(); d(); d();
  expect(spy).not.toHaveBeenCalled();
  vi.advanceTimersByTime(200);
  expect(spy).toHaveBeenCalledTimes(1);
});
```

### §16.3 Async render / sıradaki mikrotask

```js
it('YANIT sonrası listeyi doldurur', async () => {
  vi.stubGlobal('fetch', vi.fn().mockResolvedValue({
    ok: true, json: async () => [{ id: 1, title: 'A' }],
  }));
  await loadTracks();
  await Promise.resolve();               // state commit
  expect(document.querySelectorAll('li')).toHaveLength(1);
});
```

### §16.4 Router entegrasyonu (history)

```js
it('navigate sonrası path güncellenir', () => {
  router.navigate('/playlist/9');
  expect(location.pathname).toBe('/playlist/9');
  expect(router.current().name).toBe('playlist-detail');
});
```

---

## §17 İsimlendirme Sözlüğü

| Türkçe kalıp | İngilizce kalıp | Örnek |
|---|---|---|
| `{konu}_durumBeklenen` | `{subject}_{case}_{expected}` | `format_boşSifirDoner` |
| `{konu}_yayinlar{Olay}` | `{bus}_publishes{Event}` | `bus_yayinlarRouteChange` |
| `{konu}_{sinirde}` | `{subject}_boundary{N}` | `limit_tekFazlaRed` |

| Kural | Değer |
|---|---|
| tek davranışı anlatır | “and/ve” yok — iki durum iki `it` |
| `it.only` | yalnız lokal debug, commit YOK |
| Sübjektif ad yok | “works fine” yasak; ne doğruladığı yazılır |
| Selamlayıcı (HelloWorld) | üretim kodunda yok, şablonda da yok |

---

## §18 Son Kabul Listesi (definition of done)

| # | Kriter | İşaret |
|---|---|---|
| 1 | `npx vitest run` 0 fail | ☐ |
| 2 | `--coverage` raporu üretiliyor | ☐ |
| 3 | `it.only` / `describe.only` yok | ☐ |
| 4 | `afterEach` restore sızıntısız | ☐ |
| 5 | Gerçek ağ/saat yok (stub) | ☐ |
| 6 | Regresyon testi eklendi (varsa hata) | ☐ |
| 7 | `core/`, `router/` modülleri örnekleniyor | ☐ |
| 8 | CI job (hedef) ile eşleşen script adı | ☐ |

---

## §19 SSS (Shall/Should Must — hızlı başvuru)

| Soru | Cevap |
|---|---|
| Test dosyası nereye? | `__tests__/` veya modül yanına; glob §3 `include` ile eşleşmeli |
| React gerekli mi? | Şu an REACT YOK (disk); DOM testleri saf JS + jsdom |
| Playwright ile fark | vitest = birim/davranış, playwright = E2E tarayıcı |
| `beforeEach` mi `beforeAll` mı? | durum ortaksa `beforeAll`, değilse `beforeEach` (varsayılan) |
| Hangi assertion? | değer `toBe/toEqual`, varlık `toBeInTheDocument` (jsdom) |
| Asenkron nerede biter? | `await` + microtask flush (§16.3); `waitFor` DOM için |
| Sahte mi gerçek mi? | dış sınır (fetch/time/storage) daima sahte (§12) |

### §19.1 Hızlı kontrol listesi (PR öncesi)

```bash
npx vitest run                                  # 0 fail
npx vitest run --coverage                       # rapor
grep -rn "\.only(" assets.coremusic.net/js      # boş olmalı
node --check <değişen dosyalar>                 # sözdizimi (CI §3.2 js-check)
```

### §19.2 Mini sözlük

| Terim | Anlamı |
|---|---|
| spy | çağrıyı gözlemleyen sarmalayıcı (`vi.fn`) |
| stub | dönüş değeri sabitlenen sahte |
| fake timer | `setTimeout/interval` sahte saatle koşturulur |
| jsdom | Node içinde DOM (tarayıcı değil) |
| coverage | satır/branş kapsama oranı |
| regresyon | tekrarlayan eski hata için eklenen test |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
