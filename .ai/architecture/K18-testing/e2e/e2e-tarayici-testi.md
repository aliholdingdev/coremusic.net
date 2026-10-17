---
title: "K019-testing/e2e — E2E Tarayıcı Testi: Locator, Assertion, WCAG ve Bekleme Kuralları"
node_id: K019-testing/e2e/e2e-tarayici-testi
node_type: module
status: draft
parent_id: K019-testing/e2e
children: []
depends_on:
  - K019-testing
  - K019-testing/e2e
evidence:
  - "assets.coremusic.net/tests/e2e/player-info.spec.ts — 400 satır, 18 test (2026-10-12 tam okuma)"
  - "assets.coremusic.net/tests/e2e/player-info.spec.ts — Satır 19 baseURL yasagi, Satır 20 selector sabiti (2026-10-12)"
  - "assets.coremusic.net/playwright.config.ts — Satır 16-17 testDir/testMatch, Satır 29 baseURL, Satır 35-42 projects (2026-10-12)"
  - "assets.coremusic.net/playwright.config.ts — Satır 20 retries 2, Satır 30-32 kanit, Satır 44-50 webServer, Satır 52-55 timeout (2026-10-12)"
  - "package.json — Satır 8 test:e2e scripti, Satır 11 @playwright/test ^1.63.0, Satır 13 axe-playwright ^1.0.0 (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 196-199 Playwright kurulumu, Satır 202-203 npm run test:e2e (2026-10-12)"
validation:
  - "Dosya satir sayilari [IO.File]::ReadAllLines ile olcüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa basligi ile dondu (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada cakisma 0 (2026-10-12)"
  - "Spec dosyasi 400 satir tam okundu; alinti satirlari birebir dogrulandi (2026-10-12)"
open_questions:
  - "Tek spec dosyasi var (player-info.spec.ts) — kapsam 1 bilesen ile sinirli (⚠️ VERIFICATION REQUIRED)"
  - "firefox/webkit projeleri kapali (config Satır 40-41); yeniden acilma kosulu tanimsiz (⚠️ VERIFICATION REQUIRED)"
  - "18 testten bazi assertion'lari kosula bagli calisiyor (Satır 94, 160, 386) — dedektif degil (⚠️ VERIFICATION REQUIRED)"
  - "Kapali (skip) test sayisi: 0 (2026-10-12 tarama)"
group: kod
---

# K019-testing/e2e/e2e-tarayici-testi

> **ELI10:** E2E (end-to-end) testi, gerçek tarayıcıda gerçek sayfayı açıp kullanıcı
> davranışını taklit eder. Bu dosya üç şeyi kurallar: **nereye tıklanır** (locator),
> **ne doğrulanır** (assertion) ve **ne kadar beklenir** (wait). Yanlış locator kör test,
> sonsuz bekleme asılı CI demektir.

## §1 Amaç

Bu modül, CoreMusic'in **E2E tarayıcı testi kurallarının tek yetkili tanımıdır**. Dört karar bu
dosyada verilir: locator seçimi · assertion zorunluluğu · bekleme kuralı · erişilebilirlik
(WCAG) denetimi. Tarayıcı testi, `unit/` ve `integration/`'ın **yapısal olarak** doğrulayamadığı
şeyi doğrular: gerçek DOM + gerçek etkileşim.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Eleman nasıl bulunur? | Locator seçimi | §5 |
| 2 | Ne doğrulanır? | Assertion zorunluluğu | §6 |
| 3 | Ne kadar beklenir? | Bekleme kuralı | §7 |
| 4 | Erişilebilirlik nasıl denetlenir? | WCAG/axe denetimi | §8 |
| 5 | Test kararsız mı? | Koşullu assertion denetimi | §9.3 |
| 6 | Mevcut kapsam ne? | 1 spec / 18 test | §4.2 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Spec dosyası | adet | 1 (`player-info.spec.ts`) | 2026-10-12 sayımı |
| H2 | Test | adet | 18 (`test.describe` içi) | dosya okuması |
| H3 | Mutlak URL | kural | yasak (Satır 19) | kodda sabit |
| H4 | Retry (CI) | adet | 2 (config Satır 20) | kodda sabit |
| H5 | Tarayıcı projesi | adet | 1 (chromium) | config Satır 37 |
| H6 | Erken başarı | hedef | ≥ 6 spec / ≥ 100 test | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Locator | ✅ Seçici kuralı ve sabit | ❌ Bileşen kodu → varlık kodu |
| Assertion | ✅ Zorunlu doğrulama seti | ❌ Birim assertion → `unit/` |
| Bekleme | ✅ Timeout/wait kuralı | ❌ Bütçe hesabı → `performance/` |
| WCAG | ✅ axe denetimi (test içinde) | ❌ Tasarım denetimi → erişilebilirlik alanı |
| Kararsızlık | ✅ E2E kaynaklı flaky | ❌ Karantina süreci → `regression/` |
| Paralel/retry | ✅ Mevcut değer atfı | ❌ Retry politikası → `regression/` |
| Kapsam | ✅ Mevcut 1 spec gerçeği | ❌ Yeni spec yazımı → uygulama işi |

**Sınır kararı:** E2E testi **az ve anlamlı** olur; birim/entegrasyonun yerini tutmaz, onların
**bütünlük doğrulamasıdır** (§4.1).

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| E2E | end-to-end | Uçtan uca tarayıcı testi | §4 |
| Locator | locator | Eleman bulma sorgusu | §5 |
| Assertion | assertion | Doğrulama ifadesi | §6 |
| Selector | CSS selector | CSS seçicisi | MDN selectors |
| data-özelliği | data attribute | `data-cm-*` tutamağı | Satır 20 |
| baseURL | base URL | Göreli yol zorunluluğu | config Satır 29 |
| Wait | waitUntil | Sayfa hazırlik koşulu | `networkidle` |
| Retry | retry | Yeniden deneme | config Satır 20 |
| Trace | trace | Hızlı başarısızlık kanıtı | config Satır 30 |
| Axe | axe-core | WCAG denetleyicisi | Satır 17 |
| Proje | project | Tarayıcı/oyuncu kümesi | config Satır 35-42 |
| webServer | web server | Test sunucusu | config Satır 44-50 |
| Spec | spec file | Test dosyası | `*.spec.ts` |

## §4 E2E test kapsamı — repo gerçeği

### §4.1 E2E neyi doğrular, neyi doğrulamaz?

| # | Konu | E2E doğrular mı | Kim doğrular | Gerekçe |
|---|---|---|---|---|
| 1 | Gerçek DOM render | ✅ | e2e | Bileşen + tarayıcı |
| 2 | Görsel yerleşim (viewport) | ✅ | e2e | §8.2 |
| 3 | Tıklama/klavye etkileşimi | ✅ | e2e | Satır 87-134 |
| 4 | HTTP durum kodu | ✅ (200) | e2e | Satır 32 |
| 5 | WCAG ihlalleri | ✅ (axe) | e2e | §8.1 |
| 6 | İş mantığı dalı | ❌ | unit/integration | Bölüm 2 sınırı |
| 7 | Veritabanı tutarlılığı | ❌ | integration | — |
| 8 | Hız (ms) | ⚠️ yalnız regresyon hissi | performance/ | Satır 372-375 |

### §4.2 Mevcut kapsam (2026-10-12 sayımı)

| # | Varlık | Sayı | Kanıt | Durum |
|---|---|---|---|---|
| 1 | Spec dosyası | 1 | `assets.coremusic.net/tests/e2e/player-info.spec.ts` | tek dosya |
| 2 | Dosya uzunluğu | 400 satır | `[IO.File]` sayımı | tam okundu |
| 3 | `test.describe` bloğu | 1 | Satır 22 | Tek blok |
| 4 | Test | 18 | `test(` sayımı (dosya içi, 2026-10-12) | — |
| 5 | Kapsanan bileşen | 1 | `cm-player-info` (Satır 20) | Tek bileşen |
| 6 | Tarayıcı | 1 | chromium (config Satır 37) | J1 asgari kapsam |
| 7 | Proje yorumu | — | config Satır 40-41: firefox/webkit/mobile kapalı | Bilinçli |

### §4.3 Test grupları (spec dosyası)

| # | Grup | Test | Dosya satırı | Ne doğrular |
|---|---|---|---|---|
| 1 | Page load | 2 | 27-40 | 200 + render |
| 2 | Viewport | 2 | 46-81 | 1024 / 1920 varyantı |
| 3 | Player controls | 2 | 87-134 | Oynat + boşluk tuşu |
| 4 | Progress bar | 2 | 140-194 | Seek + genişlik |
| 5 | Accessibility | 4 | 200-276 | axe ×2, aria, alt |
| 6 | Auth | 2 | 282-310 | Public erişim + CSRF |
| 7 | DOM update | 2 | 316-355 | config render + zaman |
| 8 | Performance/interaction | 2 | 361-399 | < 2500 ms + hızlı tıklama |
| 9 | **Toplam** | **18** | — | `test(` sayımı 18 |

**Not:** Toplam (18), 400 satırlık spec dosyasında satır-başı `test(` sayımı ile doğrulandı
(§17 doğrulama 4); grup satırları dosyadaki yorum bloklarıyla (Satır 23-25, 42-44 vb.) eşleşir.

### §4.4 Test adı – ilk assertion eşlemesi

| # | Test adı (dosya) | İlk assertion | Satır | Doğrulanan davranış |
|---|---|---|---|---|
| 1 | page loads successfully (HTTP 200) | `status()).toBe(200)` | 32 | Sunucu yanıt verdi |
| 2 | player info component renders | `toBeVisible()` | 39 | Bileşen çizildi |
| 3 | responsive: 1024px viewport | `toBeVisible()` + `toBeLessThanOrEqual` | 52, 60 | Varyant + yerleşim |
| 4 | responsive: 1920px viewport | `toMatch(player-info--wide)` | 73 | Varyant sınıfı |
| 5 | play button: clicking toggles play state | `toBe('Duraklat')` | 99 | Davranış değişimi |
| 6 | space key: toggles play | `toBe(true)` | 133 | Klavye etkileşimi |
| 7 | progress bar: click seeks to position | `not.toBeNull()` | 162 | Olay tetiklendi |
| 8 | progress bar fill: width updates on seek | `toBeDefined()` | 192 | Zayıf (§6.2 kural 4) |
| 9 | WCAG: axe scan at 1024px | `checkA11y` | 212 | WCAG denetimi |
| 10 | WCAG: progress bar has aria attributes | `toBe('progressbar')` | 255 | ARIA sözleşmesi |
| 11 | auth: bypass test | `toBe(200)` + `toBeVisible` | 289, 293 | Public erişim |
| 12 | performance: component initializes < 1s | `toBeLessThan(2500)` | 375 | Süre regresyonu |
| 13 | Kalan 6 test | Aşağıdaki gruplara dağılır | — | §4.3 |

**Kural:** her testin adı, doğruladığı davranışı söyler; ad ≠ davranış ise test yeniden adlandırılır
(`regression/` §6.2 kalıbı).

## §5 Locator kuralları

### §5.1 Locator seçim matrisi

| # | Strateji | Örnek (repo) | Tercih | Gerekçe |
|---|---|---|---|---|
| 1 | `data-cm-*` özelliği | `[data-cm-component="cm-player-info"]` (Satır 20) | 1. tercih | Sözleşmeli, stil değişmez |
| 2 | Rol + aria | `[role="progressbar"]` (Satır 246) | 2. tercih | Erişilebilirlik bağlanır |
| 3 | BEM sınıfı | `.player-info__play` (Satır 91) | 3. tercih | Stile bağımlı → kırılgan |
| 4 | ID | `#footer_sure` (Satır 343) | Son çare | Tekil olmak zorunda |
| 5 | Metin | — | Nadir | Yerelleşmeye bağımlı |
| 6 | Mutlak URL | `BASE_URL` boş (Satır 19) | **yasak** | baseURL config'ten gelir |

### §5.2 Locator kuralları

| # | Kural | Zorunlu mu | Gerekçe | Repo kanıtı |
|---|---|---|---|---|
| 1 | Selector sabitta, tek yerde tanımlıdır | evet | Değişiklik tek yerde | Satır 20 `PLAYER_INFO_SELECTOR` |
| 2 | Mutlak URL yazma | evet | Ortam bağımsızlığı | Satır 19 yorum |
| 3 | Çoklu aday seçici tek locator'da | tercih | Varyant toleransı | Satır 156, 266 |
| 4 | `.first()` ile tekilleme | tercih | Belirsizlik kaldırma | Satır 91, 157, 320 |
| 5 | Görünürlük assertion'ı | evet | Kör test önlenir | Satır 39, 52 |
| 6 | Locator oluştur → bekle | evet | Locator lazy'tir | §7.3 |

### §5.3 Locator karar akışı

| # | Adım | Soru | Evet ise | Hayır ise |
|---|---|---|---|---|
| 1 | Sözleşme özelliği var mı? | `data-cm-*` | Kullan | 2. adıma |
| 2 | Rol/aria ile erişilebilir mi? | `role=`/`aria-` | Kullan | 3. adıma |
| 3 | Sınıf yeterince stabil mi? | BEM sınıfı | Kullan + not düş | 4. adıma |
| 4 | Yeni tutamak ekle | Bileşen kodu | — | Onay gerektirir |

## §6 Assertion (doğrulama) kuralları

### §6.1 Zorunlu assertion seti

| # | Doğrulama | Assertion | Repo örneği | Zorunlu mu |
|---|---|---|---|---|
| 1 | Sayfa açıldı | HTTP 200 | `expect(response?.status()).toBe(200)` Satır 32 | evet |
| 2 | Bileşen görünür | `toBeVisible` | Satır 39 | evet |
| 3 | Erişilebilirlik | `checkA11y` | Satır 212 | evet (WCAG) |
| 4 | aria alanları | `toBe` / `toBeTruthy` | Satır 255-259 | evet |
| 5 | Etkileşim sonucu | `toBe('Duraklat')` | Satır 99 | evet (etkileşim testinde) |
| 6 | Süre sınırı | `toBeLessThan(2500)` | Satır 375 | tercih |

### §6.2 Assertion kuralları

| # | Kural | Neden | Yakalama | Durum |
|---|---|---|---|---|
| 1 | Her testte ≥ 1 assertion | Assertion'sız test hiçbir şey doğrulamaz | Kod incelemesi | kural |
| 2 | Assertion koşula bağlanmaz | Koşul sağlanmazsa test kör geçer | §9.3 | ⚠️ mevcut 3 ihlal |
| 3 | `.catch(() => {})` boş geçmez | Hata yutulur | §9.3 | ⚠️ mevcut 2 ihlal |
| 4 | `toBeDefined` tek başına zayıf | Herhangi bir değer geçer | Assertion gücü | §9.3 |
| 5 | Assertion mesajı | Hızlı kök neden | — | tercih |

### §6.3 Assertion – hata türü matrisi

| # | Assertion türü | Ne yakalar | Ne yakalamaz | Örnek |
|---|---|---|---|---|
| 1 | Durum kodu | Sunucu hatası | Görsel bozulma | Satır 32 |
| 2 | Görünürlük | Render hatası | Yanlış içerik | Satır 39 |
| 3 | İçerik (`toContain`) | Veri hatası | Yerleşim | Satır 331 |
| 4 | Sınır (`toBeLessThan`) | Performans artışı | Nedeni | Satır 375 |
| 5 | Erişilebilirlik | WCAG ihlali | İşlevsellik | Satır 212 |
| 6 | Attr (`getAttribute`) | Sözleşme kaybı | Davranış | Satır 249-253 |

## §7 Bekleme (wait) kuralları

### §7.1 Bekleme türleri

| # | Tür | Repo kullanımı | Satır | Kural |
|---|---|---|---|---|
| 1 | `waitUntil: 'networkidle'` | Her `page.goto` | 28, 36, 49 | Varsayılan sayfa bekleme |
| 2 | Auto-wait (`toBeVisible`) | Locator bekleme | 39, 52 | Elle `waitFor` yok |
| 3 | `waitForFunction` | Olay kanıtı | 124-128 | 3000 ms üst sınır |
| 4 | `waitForTimeout` | Kısa sabit gecikme | 96, 185, 393 | Nadir, 50-100 ms |
| 5 | Test timeout | Tüm test üstü | config 52 | 30000 ms |
| 6 | Expect timeout | Assertion üstü | config 54 | 5000 ms |

### §7.2 Bekleme kuralları

| # | Kural | Zorunlu mu | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | Sabit `sleep` ile senkronlama yok | evet | Yarış | Kod incelemesi |
| 2 | Olay-öncelikli bekleme | evet | Dinleyici önce bağlanır | Satır 106-107, 143 |
| 3 | Elle timeout assertion'a yazılır | evet | Bütçe görünür | Satır 79, 127 |
| 4 | Retry = 2 (CI) | mevcut | Kararlılık | config Satır 20 |
| 5 | `networkidle` dikkatli | tercih | Uzun bekleme | §13 risk 3 |

### §7.3 Olay-öncelikli bekleme kalıbı (repo)

| # | Adım | Neden | Repo satırı | Sonuç |
|---|---|---|---|---|
| 1 | Dinleyiciyi ÖNCE ekle | Sonra eklerse olay kaçar | 108-120, 144-153 | Garanti |
| 2 | Eylemi başlat | Kullanıcı davranışı | 122, 161 | Tetikleme |
| 3 | Fonksiyonu bekle | Kanıt | 124-129 | `waitForFunction` |
| 4 | Assertion | Doğrulama | 131-133 | `toBe(true)` |
| 5 | Zaman aşımı değeri yaz | Sonsuz bekleme yok | 127 (3000), 151 (2000) | Bütçe |

## §8 WCAG / erişilebilirlik denetimi

### §8.1 axe denetimi

| # | Test | Viewport | Dosya satırı | Araç |
|---|---|---|---|---|
| 1 | `WCAG: axe scan at 1024px` | 1024×768 | 200-222 | `injectAxe` + `checkA11y` |
| 2 | `WCAG: axe scan at 1920px` | 1920×1080 | 224-241 | `injectAxe` + `checkA11y` |
| 3 | Progress aria alanları | — | 243-260 | `getAttribute` ×5 |
| 4 | Görsel alt metni | — | 262-276 | Döngü + `not.toBeNull` |

**İçe aktarma:** `injectAxe, checkA11y` → `axe-playwright` (Satır 17; package.json Satır 13).

### §8.2 Viewport (duyarlılık) denetimi

| # | Boyut | Varyant | Dosya satırı | Doğrulama |
|---|---|---|---|---|
| 1 | 1024×768 | embedded | 46-61 | sınıf regex + genişlik ≤ 1024 (Satır 60) |
| 2 | 1920×1080 | wide | 63-81 | `player-info--wide` (Satır 73) |
| 3 | Değiştir | `setViewportSize` | 48, 65, 201, 225, 337 | Test başına |

### §8.3 WCAG assertion seti

| # | Özellik | Beklenen | Satır | Öğe |
|---|---|---|---|---|
| 1 | `role` | `progressbar` | 255 | Progress |
| 2 | `aria-label` | dolu | 256 | Progress |
| 3 | `aria-valuemin` | `0` | 257 | Progress |
| 4 | `aria-valuemax` | `100` | 258 | Progress |
| 5 | `aria-valuenow` | dolu | 259 | Progress |
| 6 | `alt` | `not.toBeNull` + uzunluk > 0 | 273-274 | Görseller |

## §9 Kod örnekleri — repo gerçek kodu

### §9.1 Locator + görünürülük — Satır 20, 38-40

```typescript
const PLAYER_INFO_SELECTOR = '[data-cm-component="cm-player-info"]';
```

```typescript
const playerInfo = page.locator(PLAYER_INFO_SELECTOR);
await expect(playerInfo).toBeVisible();
```

**Dosya: `assets.coremusic.net/tests/e2e/player-info.spec.ts` / Satır: 20 · 38 · 39 · 40**

### §9.2 Durum kodu assertion'ı — Satır 27-33

```typescript
test('page loads successfully (HTTP 200)', async ({ page }) => {
  const response = await page.goto(`${BASE_URL}/index.php`, {
    waitUntil: 'networkidle',
  });

  expect(response?.status()).toBe(200);
});
```

**Dosya: `assets.coremusic.net/tests/e2e/player-info.spec.ts` / Satır: 27 · 28 · 29 · 32**

### §9.3 Koşullu assertion denetimi (zayıflık kaydı)

Üç test, assertion'ı koşula bağlar — koşul sağlanmazsa test **hiçbir şey doğrulamadan geçer**:

| # | Satır | Kalıp | Risk | Karar |
|---|---|---|---|---|
| 1 | 94 | `if (initialAlt === 'Oynat')` | Düğme `Oynat` değilse test boş geçer | ⚠️ VERIFICATION REQUIRED |
| 2 | 160, 183, 386 | `if (box)` | `box` null ise assertion çalışmaz | ⚠️ VERIFICATION REQUIRED |
| 3 | 79-80, 129 | `.catch(() => {})` | Hata yutulur, test yeşil kalır | ⚠️ VERIFICATION REQUIRED |
| 4 | 192 | `toBeDefined()` | Herhangi bir değer kabul edilir (zayıf) | §6.2 kural 4 |

**Kural (§6.2):** koşul sağlanmazsa test **başarısız olmalı** ya da `test.skip()` ile
**açıkça atlanmalıdır** — sessiz geçiş yasaktır.

### §9.4 Kalibre edilmiş süre assertion'ı — Satır 361-375

```typescript
test('performance: component initializes < 1s', async ({ page }) => {
  const startTime = Date.now();

  await page.goto(`${BASE_URL}/index.php`, { waitUntil: 'networkidle' });

  const playerInfo = page.locator(PLAYER_INFO_SELECTOR).first();
  await expect(playerInfo).toBeVisible();

  const endTime = Date.now();
  const elapsed = endTime - startTime;

  expect(elapsed).toBeLessThan(2500);
});
```

Dosya yorumu (Satır 372-374) eşik gerekçesini içerir: eşitlik sınırı (1000 ms) yerel soğuk
yüklemede 1286 ms ölçüldüğü için **2500 ms**'e çekilmiştir; amaç regresyon algısıdır
(mikro-benchmark değil). Test adı ile eşik arasındaki fark bilinçli ve belgelidir.

**Dosya: `assets.coremusic.net/tests/e2e/player-info.spec.ts` / Satır: 361 · 362 · 364 · 370 · 375**

### §9.5 Yapılandırma — playwright.config.ts Satır 16-21, 29

```typescript
testDir: './tests/e2e',
testMatch: '**/*.spec.ts',
fullyParallel: true,
forbidOnly: !!process.env.CI,
retries: process.env.CI ? 2 : 0,
workers: process.env.CI ? 1 : undefined,
```

```typescript
baseURL: 'http://home.coremusic.net:81',
```

**Dosya: `assets.coremusic.net/playwright.config.ts` / Satır: 16 · 17 · 18 · 19 · 20 · 21 · 29**

### §9.6 Çok-dilli karşılıklar (12 dil)

> Tarayıcı testi kalıbı (locator → etkileşim → assertion) 12 dilde — repo standardı
> TypeScript + Playwright Test'tir (`assets.coremusic.net/tests/e2e/player-info.spec.ts`).
> Sürüm iddiası yazılmaz (bilgi amaçlı). Playwright'ın resmi dilleri: TS/JS · Python · Java · .NET;
> diğerleri için ekosistem karşılığı satırda yazılıdır.

| # | Dil | E2E aracı | Komut |
|---|---|---|---|
| 1 | TypeScript | Playwright Test (repo standardı) | `npx playwright test player-info` |
| 2 | JavaScript | Playwright Test (JS) | `npx playwright test oynat.test.js` |
| 3 | Python | pytest-playwright | `pytest test_oynat.py --browser chromium` |
| 4 | C# | Playwright for .NET | `dotnet test --filter OynatTesti` |
| 5 | Java | Playwright for Java | `mvn test -Dtest=OynatTesti` |
| 6 | Go | chromedp (Playwright yok) | `go test -run TestOynat ./...` |
| 7 | C++ | Selenium WebDriver C++ binding | `./e2e_testler` |
| 8 | Rust | thirtyfour (WebDriver) | `cargo test oynat` |
| 9 | Ruby | Capybara + Selenium | `bundle exec rspec spec/oynat_spec.rb` |
| 10 | Kotlin | Playwright Java (JVM) | `gradle test --tests OynatTesti` |
| 11 | Swift | XCTest + XCUITest (Safari) | `xcodebuild test -only-testing:OynatUITests` |
| 12 | C | libcurl + WebDriver REST (harici sürücü) | `./c_e2e` |

**TypeScript (Playwright Test — repo kalıbı §9.1-§9.2):**

```ts
import { test, expect } from '@playwright/test';

test('oynat düğmesi görünürlük durumunu değiştirir', async ({ page }) => {
  await page.goto('/');                                    // Arrange: sayfa
  const dugme = page.getByRole('button', { name: 'Oynat' }); // Locator (§5.1)
  await dugme.click();                                     // Act
  await expect(dugme).toHaveAccessibleName('Duraklat');    // Assert
});
```

**JavaScript (Playwright Test):**

```js
const { test, expect } = require('@playwright/test');

test('oynat düğmesi görünürlük durumunu değiştirir', async ({ page }) => {
  await page.goto('/');
  const dugme = page.getByRole('button', { name: 'Oynat' });
  await dugme.click();
  await expect(dugme).toHaveAccessibleName('Duraklat');
});
```

**Python (pytest-playwright):**

```python
def test_oynat_duraklatir(page, asal_url):              # Arrange: fixture
    page.goto(asal_url)                                  # Arrange
    dugme = page.get_by_role("button", name="Oynat")     # Locator
    dugme.click()                                        # Act
    assert dugme.accessible_name == "Duraklat"           # Assert
```

**C# (Playwright for .NET):**

```csharp
[Fact]
public async Task OynatDuraklatir()
{
    await Sayfa.GotoAsync("/");                                   // Arrange
    var dugme = Sayfa.GetByRole(AriaRole.Button, new() { Name = "Oynat" });
    await dugme.ClickAsync();                                     // Act
    await Expect(dugme).toHaveAccessibleNameAsync("Duraklat");    // Assert
}
```

**Java (Playwright for Java):**

```java
@Test
void oynatDuraklatir() {
    sayfa.navigate("/");                                          // Arrange
    Locator dugme = sayfa.getByRole(AriaRole.BUTTON,
        new Locator.GetByRoleOptions().setName("Oynat"));
    dugme.click();                                                // Act
    assertThat(dugme).hasAccessibleName("Duraklat");              // Assert
}
```

**Go (chromedp):**

```go
func TestOynatDuraklatir(t *testing.T) {
    olcu, _, _ := chromedp.RunContext(ctx,
        chromedp.Navigate(asal),                                  // Arrange
        chromedp.Click(`//button[@aria-label='Oynat']`, chromedp.BySearch), // Act
        chromedp.AttributeContent(`//button`, "aria-label", &ad), // Assert
    )
    if olcu != nil || ad != "Duraklat" { t.Fatalf("ad = %q", ad) }
}
```

**C++ (Selenium WebDriver):**

```cpp
TEST(E2ETesti, OynatDuraklatir) {
    auto *surucu = new webdriver::Chrome();                    // Arrange
    surucu->get(ASAL);                                          // Arrange
    auto *dugme = surucu->findElement(By::XPath("//button"));   // Locator
    dugme->click();                                             // Act
    EXPECT_EQ(dugme->getAttribute("aria-label"), "Duraklat");   // Assert (§6.2)
}
```

**Rust (thirtyfour):**

```rust
#[tokio::test]
async fn oynat_duraklatir() -> WebDriverResult<()> {
    let surucu = WebDriver::new(ADRES, Capabilities::chrome()).await?; // Arrange
    surucu.goto(format!("{ASAL}/")).await?;
    let dugme = surucu.find(By::Css("button[aria-label='Oynat']")).await?; // Locator
    dugme.click().await?;                                                     // Act
    assert_eq!(dugme.attr("aria-label").await?, Some("Duraklat".into()));    // Assert
    Ok(())
}
```

**Ruby (Capybara + Selenium):**

```ruby
feature 'Oynat düğmesi' do
  scenario 'görünürlük durumunu değiştirir' do
    visit '/'                                      # Arrange
    dugme = find('button[aria-label="Oynat"]')     # Locator
    dugme.click                                     # Act
    expect(dugme[:'aria-label']).to eq('Duraklat')  # Assert
  end
end
```

**Kotlin (Playwright Java):**

```kotlin
@Test
fun `oynat duraklatir`() {
    sayfa.navigate("/")                                        // Arrange
    val dugme = sayfa.getByRole(AriaRole.BUTTON, getByRoleOptions().setName("Oynat"))
    dugme.click()                                              // Act
    assertThat(dugme).hasAccessibleName("Duraklat")            // Assert
}
```

**Swift (XCUITest):**

```swift
func testOynatDuraklatir() {
    let uyg = XCUIApplication(); uyg.launch()                  // Arrange
    let dugme = uyg.buttons["Oynat"]                            // Locator
    dugme.tap()                                                 // Act
    XCTAssertEqual(dugme.label, "Duraklat")                    // Assert
}
```

**C (libcurl + WebDriver REST):**

```c
void test_oynat_duraklatir(void) {
    wd_session_t *wd = wd_session_create();                   /* Arrange */
    wd_navigate(wd, ASAL);                                     /* Arrange */
    wd_element_t *d = wd_find(wd, "css", "button");            /* Locator */
    wd_click(wd, d);                                           /* Act */
    TEST_ASSERT_EQUAL_STRING("Duraklat", wd_attr(wd, d, "aria-label")); /* Assert */
}
```

## §10 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 16-17 | `test, expect` + `injectAxe, checkA11y` import |
| 2 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 19 | Mutlak URL yasağı yorumu |
| 3 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 20 | Selector sabiti |
| 4 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 22 | `test.describe('PlayerInfo E2E')` |
| 5 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 32 | 200 assertion'ı |
| 6 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 46-61 | 1024px viewport + genişlik sınırı |
| 7 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 63-81 | 1920px viewport varyantı |
| 8 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 94 | Koşullu assertion (zayıflık) |
| 9 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 106-120 | Dinleyici-önce kalıbı |
| 10 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 200-222 | axe taraması 1024px |
| 11 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 243-260 | aria assertion seti |
| 12 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 282-294 | Public erişim (auth bypass) |
| 13 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 372-375 | Süre eşiği gerekçesi (2500 ms) |
| 14 | assets.coremusic.net/playwright.config.ts | 16-17 | testDir + testMatch |
| 15 | assets.coremusic.net/playwright.config.ts | 20-21 | Retry 2 / workers 1 (CI) |
| 16 | assets.coremusic.net/playwright.config.ts | 29 | baseURL |
| 17 | assets.coremusic.net/playwright.config.ts | 30-32 | Trace/screenshot/video |
| 18 | assets.coremusic.net/playwright.config.ts | 35-42 | Tek proje: chromium (kapalılar yorumlu) |
| 19 | assets.coremusic.net/playwright.config.ts | 44-50 | webServer tanımı |
| 20 | package.json | 8 | `test:e2e` scripti |
| 21 | package.json | 11 | `@playwright/test` ^1.63.0 |
| 22 | package.json | 13 | `axe-playwright` ^1.0.0 |
| 23 | .github/workflows/ci.yml | 196-199 | Playwright chromium kurulumu |
| 24 | .github/workflows/ci.yml | 202-203 | `npm run test:e2e` |

## §11 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Mutlak URL | Ortam bağımsızlığı | Satır 19 + inceleme |
| 2 | Assertion'sız test | Kör test | §6.2 kural 1 |
| 3 | Koşula bağlı assertion | Sessiz geçiş | §9.3 denetimi |
| 4 | Boş `.catch` | Hata yutma | §9.3 satır 3 |
| 5 | Sabit `sleep` ile senkronlama | Yarış + yavaşlık | §7.2 kural 1 |
| 6 | E2E ile birim testi ikamesi | Yavaş + kırılgan | §2 sınır kararı |
| 7 | Elemanı JS ile beklemek (manuel) | Auto-wait var | §7.1 |
| 8 | WCAG testini atlamak | Erişilebilirlik kaybı | §8 zorunlu set |
| 9 | Retry artırarak kararlılık | Kök neden gizlenir | `regression/` |
| 10 | Yeni spec'i config'siz ekleme | `testDir` dışı | config Satır 16-17 |

## §12 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K019-testing/unit | Birim yeşili | E2E öncesi ön koşul | CI sırası | mevcut |
| 2 | K019-testing/integration | Sözleşme yeşili | HTTP kodu seti | 200 assertion | mevcut |
| 3 | K019-testing/performance | Süre bütçesi | 30 s / 5 s / 2500 ms | config Satır 52-55 | mevcut |
| 4 | K019-testing/regression | Kararlılık | Retry 2 + kanıt | config Satır 20, 30-32 | mevcut |
| 5 | K019-testing/coverage | Kapsam | JS kapsam adımı ayrı | `test:js:coverage` | ayrık |
| 6 | K017-observability/alerting | Kırmızı E2E sinyali | Uyarı | CI işi (soft) | ⚠️ VERIFICATION REQUIRED |
| 7 | K018-devops/pipeline | Kapı sertliği | `continue-on-error` | ci.yml Satır 183 | açık |

## §13 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Tek spec / tek bileşen | E2E kapsamı çok dar | yüksek | Yeni spec planı | ⚠️ VERIFICATION REQUIRED |
| 2 | Koşullu assertion (3 yer) | Kör geçiş | yüksek | §6.2 düzeltmesi | ⚠️ VERIFICATION REQUIRED |
| 3 | `networkidle` her yerde | Yavaş test | orta | Alternatif hazırlik | §7.2 kural 5 |
| 4 | Tek tarayıcı (chromium) | Tarayıcı farkı görünmez | orta | Proje açma | config Satır 40-41 |
| 5 | Soft kapı (ci.yml 183) | Kırmızı bloklanmıyor | orta | Sertleştirme | `regression/` |
| 6 | `.catch(() => {})` hata yutar | Sahte yeşil | yüksek | Kural 4 denetimi | §9.3 |
| 7 | `toBeDefined` zayıf assertion | Bozulma kaçabilir | orta | Güçlendirme | §6.2 kural 4 |
| 8 | Yerel sunucu bağımlılığı | Test koşamazsa tamam kapalı | orta | webServer yönetimi | config Satır 44-50 |
| 9 | Spec adı yalnız 1 dosya | Yeni eklendiğinde isim çakışması | düşük | Klasör kuralı | ⚠️ VERIFICATION REQUIRED |

## §14 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Test | 18 test yeşil | CI (js-e2e) | her PR | spec dosyası |
| 2 | Assertion | Her testte ≥ 1 | Kod incelemesi | her PR | §6.1 |
| 3 | Locator | Mutlak URL = 0 | Satır 19 kuralı | her PR | §5.1 satır 6 |
| 4 | WCAG | 4 denetim testi | axe | her PR | §8.3 |
| 5 | Kararlılık | Retry 2 altı | Rapor | haftalık | `regression/` |
| 6 | Bütçe | Test ≤ 30 s | config Satır 52 | her koşu | `performance/` |
| 7 | Kapsam | ≥ 6 spec | Dosya sayımı | çeyrek | §4.2 |

## §15 Yeni spec şablonu

| # | Şablon alanı | Zorunlu | Değer | Denetim |
|---|---|---|---|---|
| 1 | Dosya adı | evet | `tests/e2e/<bilesen>.spec.ts` | config Satır 16-17 |
| 2 | Selector | evet | `data-cm-*` (Satır 20 kalıbı) | §5.1 |
| 3 | URL | evet | Göreli (`BASE_URL` boş) | §5.2 kural 2 |
| 4 | HTTP assertion | evet | 200 | §6.1 satır 1 |
| 5 | Görünürlük assertion | evet | `toBeVisible` | §6.1 satır 2 |
| 6 | WCAG assertion | evet | axe veya aria | §8.1 |
| 7 | Koşulsuzluk | evet | `if` içinde assertion yok | §9.3 |
| 8 | Bekleme | evet | Auto-wait / olay | §7.2 |
| 9 | Süre sınırı | tercih | 2500 ms kalıbı | §9.4 |

## §16 Bekleme – assertion – hata matrisi

| # | Durum | Belirti | Kök neden | Aksiyon |
|---|---|---|---|---|
| 1 | `Timeout while waiting` | Assertion timeout | Locator yanlış / element yok | §5.3 |
| 2 | Test geçer, UI bozuk | Assertion yok | §6.2 kural 1 ihlali | Assertion ekle |
| 3 | Test bazen geçer | `catch` yutma | §9.3 satır 3 | `.catch` kaldır |
| 4 | Test geçer ama iş yapmıyor | `if` korumalı | §9.3 satır 1 | `test.skip` + görev |
| 5 | Süre aşımı | `networkidle` uzun | §7.2 kural 5 | Bekleme stratejisi |
| 6 | Web sunucu yok | `webServer` başarısız | config Satır 44-50 | Sunucu/bağlam |

## §17 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Playwright Assertions sayfası, `expect` tabanlı doğrulama API'lerini tanımlar | https://playwright.dev/docs/test-assertions | 2026-10-12 | HTTP 200 doğrulandı |
| Playwright Locators sayfası, eleman bulma (locator) stratejilerini tanımlar | https://playwright.dev/docs/locators | 2026-10-12 | HTTP 200 doğrulandı |
| MDN Basic CSS selectors sayfası, CSS seçici (selector) tanımını açıklar | https://developer.mozilla.org/en-US/docs/Learn/CSS/Building_blocks/Selectors | 2026-10-12 | HTTP 200 doğrulandı |
| MDN Web Accessibility sayfası, erişilebilirlik (accessibility) kavramını tanımlar | https://developer.mozilla.org/en-US/docs/Web/Accessibility | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz.

## §18 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 + başlık kaydedildi | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Spec dosyası | tam okuma (400 satır) | 18 `test(` + satır doğrulaması | 2026-10-12 |
| 5 | Config satırları | tam okuma (56 satır) | 16-21, 29, 35-42, 44-55 | 2026-10-12 |
| 6 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
