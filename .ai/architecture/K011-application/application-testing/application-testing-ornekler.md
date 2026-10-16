---
title: "K011 · application-testing — ornekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: application-testing
status: active
updated: 2026-10-15
group: kod
---

# K011 · application-testing — Ornekler (kod)

Amaç: repo'dan **birebir okunmuş** test/konfigürasyon örnekleri; her kod
bloğunun başında `Dosya: … · Satır: …` kanıtı, bloğun altında YAPMA↔YAP
anti-pattern çifti. Diller: **PHP · JavaScript · TypeScript · CSS · SQL**
(5 dil, asgari 4). Hiçbir blok uydurulmamıştır — satır numaraları 2026-10-15
dosya hâline aittir.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | Örnek yazım kuralları | kod | yöntem | Alıntı birebirlik, kanıt zorunluluğu |
| 2 | PHP — unit test örnekleri | kod | php | CSRF, source-scan, device |
| 3 | JavaScript — vitest örnekleri | kod | js | DOM kurulumu, olumlu/olumsuz yol |
| 4 | TypeScript — Playwright e2e | kod | ts | baseURL, responsive ölçüm |
| 5 | CSS — token örnekleri | kod | css | Breakpoint token, @media yasağı |
| 6 | SQL — şema örnekleri | kod | sql | Credential şeması, dump başlığı |
| 7 | Yapılandırma örnekleri | kod | config | vitest · playwright · ci.yml · composer |
| 8 | YAPMA↔YAP çiftleri ana tablosu | kod | anti-pattern | 15 çift, hepsi kanıtlı |
| 9 | Örnek ↔ kapı iz düşümü | kod | iz | Hangi örnek hangi kapıyı besliyor |
| 10 | Kullanılmayan / PLANNED | kod | boşluk | Örnek üretilmeyen alanlar |

---

## §1 ÖRNEK YAZIM KURALLARI

1. Her kod bloğu bir `Dosya: <yol> · Satır: <aralık>` satırıyla başlar.
2. Alıntı **birebirdir**; kısaltma varsa `…` ile belirtilir ve satır
   numarası aralığı kısaltmaya uyar.
3. Blok başına en az bir **YAPMA↔YAP** çifti verilir; YAPMA satırı ya repo
   yorumunda/kuralında yazılıdır ya da testin sınadığı ihlalin ta kendisidir.
4. Kaynak kod (`*.php/js/ts/css/sql`) bu dosyada **değiştirilmez** — yalnız
   okunur (dokunulmaz yüzey).
5. Örnekler mevcut test altyapısının **kendi kalıbını** anlatır; havuz
   11.5/12.5 kılavuzlarının sürüm-kritik kalıpları burada **uygulanmış
   sayılmaz** (bkz `application-testing-web-arastirma.md` §3).
6. Repo gerçekliği `^10.5` olduğundan örnekler PHPUnit 10 API'si kullanır
   (`PHPUnit\Framework\TestCase`, docblock `@covers`, `assertSame`).

---

## §2 PHP — UNIT TEST ORNEKLERI

### §2.1 Olumsuz yol: 403 + hata kodu + halt üçlemesi

```
Dosya: shared/tests/Middleware/CsrfMiddlewareTest.php · Satır: 85-97
```

```php
    public function testPostWithoutTokenReturns403(): void
    {
        $middleware = new CsrfMiddleware();

        $result = $middleware->handle(
            ['method' => 'POST', 'uri' => '/submit', 'headers' => [], 'body' => []],
            fn(array $req) => ['status' => 200]
        );

        $this->assertSame(403, $result['httpStatus']);
        $this->assertSame('csrf_invalid', $result['body']['error']);
        $this->assertTrue($result['halt'] ?? false);
    }
```

**YAPMA:** test yalnız `assertSame(403, …)` ile yetinir → middleware `halt`
set etmeyip akışa devam ederse test geçer, gerçeği kaçırır.
**YAP:** durum kodu **+** hata kodu **+** `halt` bayrağı birlikte doğrulanır
(3 assert, tek davranış sözleşmesi).

### §2.2 Super-global temizliği (test izolasyonu)

```
Dosya: shared/tests/Middleware/CsrfMiddlewareTest.php · Satır: 18-26
```

```php
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }
```

**YAPMA:** `$_SESSION` testler arasında kalır → sıradaki test sahte "geçerli
token" ile geçebilir (yanıp sönen, sıra-bağımlı test).
**YAP:** `setUp` **ve** `tearDown` içinde iki yönlü sıfırlama.

### §2.3 Source-scan testi: kaynağı kendisi sınar

```
Dosya: shared/tests/Api/ResponseNormalizationMiddlewareTest.php · Satır: 34-42
```

```php
    public function testMiddlewareContainsNoExitOrDieBypass(): void
    {
        $source = (string) file_get_contents(
            dirname(__DIR__, 2) . '/src/Api/Middleware/ResponseNormalizationMiddleware.php'
        );

        $this->assertDoesNotMatchRegularExpression('/\b(exit|die)\b\s*[(;]/', $source, '304 yolu exit ile index.php\'yi bypass edemez');
        $this->assertStringContainsString("'halt'", $source, '304 gövdesiz halt ile dönmeli');
    }
```

**YAPMA:** 304 yolunda `exit`/`die` → `index.php` akışı (header kopyalama)
bypass edilir; davranış testi bunu yakalayamaz çünkü süreç erken biter.
**YAP:** kaynak dosyayı regex ile tara + beklenen `'halt'` anahtarını ara;
davranış testi (§2.4) ile çift katman.

### §2.4 Gövdesiz 304 sözleşmesi (davranış katmanı)

```
Dosya: shared/tests/Api/ResponseNormalizationMiddlewareTest.php · Satır: 44-58
```

```php
    public function testCacheableRouteWithMatchingEtagReturnsHalt304(): void
    {
        $response = ['data' => ['service' => 'auth'], 'meta' => ['version' => '1.0.0']];
        $etag     = md5((string) json_encode($response));

        $_SERVER['HTTP_IF_NONE_MATCH'] = '"' . $etag . '"';

        $out = $this->process(['_route' => ['cacheable' => true, 'cacheTtl' => 60]], $response);

        $this->assertSame(304, $out['httpStatus']);
        $this->assertTrue($out['halt'], 'Gövdesiz 304: halt bayrağı set edilmeli');
        $this->assertSame('', $out['body']);
        // exit olsaydı bu satırlara hiç ulaşılmazdı.
        $this->assertSame(['service' => 'auth'], $out['data']);
    }
```

**YAPMA:** gövde dolu 304 (yanlış) veya `exit` nedeniyle test'in geri
kalanına ulaşılamaması.
**YAP:** `304` + `halt` + **boş gövde** + akışın devam edebildiğinin kanıtı
(`$out['data']` hâlâ okunabilir) — dörtlü assert.

### §2.5 Determinizm: paylaşılan singleton sıfırlama

```
Dosya: shared/tests/Unit/Device/DeviceDetectorTest.php · Satır: 11-21
```

```php
    protected function setUp(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = '';
        unset($_SERVER['HTTP_X_DEVICE_TYPE']);
        DeviceManager::resetInstance();
    }

    protected function tearDown(): void
    {
        DeviceManager::resetInstance();
    }
```

**YAPMA:** singleton/cache testler arasında taşırsa ilk testte hesaplanan
cihaz kimliği son testte değişir (flaky).
**YAP:** her iki uçta `DeviceManager::resetInstance()` + super-global temizliği.

### §2.6 Boundary testi: viewport eşiği

```
Dosya: shared/tests/Unit/Device/DeviceDetectorTest.php · Satır: 46-52
```

```php
    public function testDetectsLaptop1024x768Viewport(): void
    {
        $winDesktopUa = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        // 1024x768 on desktop OS should be treated as laptop/small-desktop
        $this->assertSame('laptop', DeviceDetector::detect($winDesktopUa, 1024, 768));
        $this->assertFalse(DeviceDetector::isEmbedded1024('laptop', 1024, 768));
    }
```

**YAPMA:** yalnız mutlu orta değer test etmek (ör. 1440×900) → 1024×600
(RPi5) / 1024×768 sınırı test edilmez.
**YAP:** **aynı genişlikte** farklı yükseklik/OS kombinasyonu (burada 1024×768
`laptop`, §kapsam'daki 1024×600 `embedded`) — sınırın iki yanı.

### §2.7 Olumsuz yol: exception + repository mock

```
Dosya: shared/tests/OAuth/OAuthManagerTest.php · Satır: 140 (context: L131-141)
```

```php
        $pdo = $this->createMock(\PDO::class);
        // … kurulum (L131) …
        $this->expectException(\RuntimeException::class);
```

**YAPMA:** hata yolunu `try/catch` + `$this->fail(...)` ile elle sarmak
(assert kaybolursa test sessizce geçer).
**YAP:** `expectException` ile niyeti tek satırda bildir (repo genelinde
`expectException` = 10; `OAuthManagerTest` 8 mock + 1 exception).

> **Not (kanıt sınırı):** §2.7 bloğu `grep` çıktısından **context** olarak
> alınmıştır (L131 mock, L140 exception — dosyanın tamamı okunmamıştır);
> satır numaraları `grep` çıktısına dayanır, blok satır içeriği kısaltmadır.

---

## §3 JAVASCRIPT — VITEST ORNEKLERI

### §3.1 DOM elle kurulumu (innerHTML yasağı)

```
Dosya: assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js · Satır: 18-27
```

```js
function buildBadge(opts = {}) {
    const el = document.createElement(opts.tag || 'span');
    el.className = `badge badge--${opts.variant || 'primary'}`;
    el.setAttribute('data-cm-component', 'cm-badge');
    if (opts.count !== undefined) el.setAttribute('data-cm-count', opts.count);
    if (opts.interactive) el.setAttribute('data-cm-interactive', 'true');
    el.textContent = 'Yeni';
    document.body.appendChild(el);
    return el;
}
```

**YAPMA:** `el.innerHTML = '<span class="badge …">Yeni</span>'` — dosya başlığı
(L6) bunu açıkça yasaklar: `* Kural: innerHTML YASAK — DOM elle kurulur.`
**YAP:** `createElement` + `className` + `setAttribute` + `textContent`;
aynı dosya `it('setLabel: textContent güncellenir (innerHTML değil)', …)`
(L112-119) ile kuralı kendi testinde de doğrular.

### §3.2 Olumlu yol: init + state doğrulama

```
Dosya: assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js · Satır: 40-47
```

```js
    it('init: varyant sınıftan, label textContent\'ten okunur', () => {
        const el = buildBadge({ variant: 'success' });
        component = new BadgeComponent(el);
        component.init();

        expect(component.state.variant).toBe('success');
        expect(component.state.label).toBe('Yeni');
    });
```

**YAPMA:** `expect(component).toBeDefined()` — her zaman doğru, hiçbir şey
sınama (assertion kalitesizliği).
**YAP:** iki somut state değeri (`toBe`) — DOM'dan okunan/okunmayan davranış.

### §3.3 Erişilebilirlik + DOM çıktısı birlikte

```
Dosya: assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js · Satır: 83-97
```

```js
    it('setCount: metin count olur + badge--count + aria-label', () => {
        const el = buildBadge();
        component = new BadgeComponent(el);
        component.init();

        component.setCount(5);

        expect(el.textContent).toBe('5');
        expect(el.classList.contains('badge--count')).toBe(true);
        expect(el.getAttribute('aria-label')).toBe('Yeni: 5');

        component.setCount(null);
        expect(el.classList.contains('badge--count')).toBe(false);
        expect(el.textContent).toBe('Yeni');
    });
```

**YAPMA:** yalnız `textContent` kontrolü → modifier sınıf ve `aria-label`
(kör kullanıcı) unutulur.
**YAP:** metin + sınıf + `aria-label` üçlüsü, ayrıca **geri dönüş**
(`setCount(null)` → başlangıç durumu) test edilir.

### §3.4 Olumsuz yol: geçersiz girdinin reddi

```
Dosya: assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js · Satır: 99-110
```

```js
    it('setVariant: modifier değişimi (yalnız izinli set)', () => {
        const el = buildBadge({ variant: 'primary' });
        component = new BadgeComponent(el);
        component.init();

        component.setVariant('neutral');
        expect(el.classList.contains('badge--neutral')).toBe(true);
        expect(el.classList.contains('badge--primary')).toBe(false);

        component.setVariant('bogus'); // reddedilir
        expect(component.state.variant).toBe('neutral');
    });
```

**YAPMA:** geçersiz varyantı sessizce uygulamak (state bozulur, tema kırılır).
**YAP:** whitelist dışı girdi **reddedilir** ve state önceki değerde kalır —
olumsuz yol da `it()` içinde.

### §3.5 Olay yayımlama: yalnız etkileşimli kök

```
Dosya: assets.coremusic.net/js/components/primitives/BadgeComponent.spec.js · Satır: 49-62 (YAPMA senaryosu) ve L121-134 (YAP senaryosu)
```

```js
    it('span kök: tıklama yayım YAPMAZ (etkileşimli değil)', () => {
        const el = buildBadge();
        component = new BadgeComponent(el);
        component.init();
        component.mount();

        let fired = 0;
        el.addEventListener('cm:badge:click', () => {
            fired += 1;
        });

        el.click();
        expect(fired).toBe(0);
    });
```

```js
    it('data-cm-interactive=true olan span da yayım yapar', () => {
        const el = buildBadge({ interactive: true });
        // … aynı kurulum …
        component.mount();

        let fired = 0;
        el.addEventListener('cm:badge:click', () => { fired += 1; });
        el.click();
        expect(fired).toBe(1);
    });
```

**YAPMA:** her tıklamayı yaymak (statik rozet bile olay üretir → sahte olay
seli) ya da hiç yaymamak (buton çalışmaz).
**YAP:** **üçlü matris** — `span` yaymaz (L49-62) · `button` yayınlar
(L64-81, `detail.variant`/`detail.label` doğrulanır) · `data-cm-interactive`
`span`'i yayınlar yapar (L121-134). Component State Matrix uyar.

---

## §4 TYPESCRIPT — PLAYWRIGHT E2E ORNEKLERI

### §4.1 baseURL disiplini

```
Dosya: assets.coremusic.net/tests/e2e/player-info.spec.ts · Satır: 19-20 (+ config: playwright.config.ts L29)
```

```ts
const BASE_URL = ''; // WP2/J1: mutlak URL yasak — baseURL playwright.config'tan gelir (home.coremusic.net:81)
const PLAYER_INFO_SELECTOR = '[data-cm-component="cm-player-info"]';
```

```ts
// playwright.config.ts L29
    baseURL: 'http://home[.]coremusic[.]net:81',
```

**YAPMA:** `const BASE_URL = 'http://localhost:81'` sabiti — ortam değişiminde
tüm spec'ler kırılır, CI/local farkı doğar.
**YAP:** spec'te boş `BASE_URL` + tek kaynak `playwright.config.ts` `baseURL`
(+ `webServer.url` L47 ile aynı değer).

### §4.2 En düşük anlama testi (HTTP 200)

```
Dosya: assets.coremusic.net/tests/e2e/player-info.spec.ts · Satır: 27-33
```

```ts
  test('page loads successfully (HTTP 200)', async ({ page }) => {
    const response = await page.goto(`${BASE_URL}/index.php`, {
      waitUntil: 'networkidle',
    });

    expect(response?.status()).toBe(200);
  });
```

**YAPMA:** `expect(true).toBe(true)` ya da boş test — sayfa 500 dönse de
geçer.
**YAP:** gerçek HTTP durum kodu + `networkidle` (ağ gürültüsü bitsin).

### §4.3 Responsive ölçüm: sınırsal viewport + görsel kanıt

```
Dosya: assets.coremusic.net/tests/e2e/player-info.spec.ts · Satır: 46-61
```

```ts
  test('responsive: 1024px viewport (embedded variant)', async ({ page }) => {
    // Set 1024px viewport (embedded)
    await page.setViewportSize({ width: 1024, height: 768 });
    await page.goto(`${BASE_URL}/index.php`, { waitUntil: 'networkidle' });

    const playerInfo = page.locator(PLAYER_INFO_SELECTOR);
    await expect(playerInfo).toBeVisible();

    // Check for embedded variant class
    const classAttr = await playerInfo.getAttribute('class');
    expect(classAttr).toMatch(/now-playing--embedded|player-info--wide/);

    // Player info should fit in viewport
    const box = await playerInfo.boundingBox();
    expect(box?.width).toBeLessThanOrEqual(1024);
  });
```

**YAPMA:** yalnız `toBeVisible()` → 1024'te taşan bileşen görünür sayılır,
test geçer.
**YAP:** **üç katman**: görünürlük + cihaz varyantı sınıfı (`toMatch` regex)
+ **ölçü** (`boundingBox().width ≤ 1024`) — 1024 = embedded sınırı
(`a-breakpoint-tokens.css` L29 ile aynı değer).

---

## §5 CSS — TOKEN ORNEKLERI

### §5.1 Cihaz breakpoint token'ları (px)

```
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 27-34
```

```css
  --bp-device-phone:      767px;
  --bp-device-tablet:     1024px;
  --bp-device-embedded:   1024px;   /* RPi5: 1024x600 */
  --bp-device-laptop:     1440px;
  --bp-device-medium:     1919px;   /* Medium desktop: 1441-1919px */
  --bp-device-desktop:    2560px;
  --bp-device-4k-tv:      3840px;
  --bp-device-4k-monitor: 3841px;
```

**YAPMA:** katman dosyasına ham `767px`/`1024px` dağıtmak (ITSS token kuralı:
sabit değer yalnız `01_Abstracts` içinde ve `--cm-*`/`--bp-*` adıyla).
**YAP:** `:root` içinde tekilleştirilmiş token + yorumda cihaz karşılığı;
`device-loader.js` ile senkron notu (L25).

### §5.2 `@media` içinde `var()` yasağı (spesifikasyon kuralı)

```
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 39-46
```

```css
     NOT (CSS spesifikasyonu): @media içinde var() KULLANILAMAZ —
     "@media (min-width: var(--bp-1024))" geçersizdir.
     Bağlantı yolu: her @media satırının üstünde karşılık yorumu vardır
     (ör. "--bp-1024: 1024px") — değerleri değiştirecekseniz ÖNCE burayı,
     SONRA ilgili @media satırlarını eşit tutun.
     ============================================================ */
  --bp-1024: 1024px;  /* = --bp-device-tablet = --bp-device-embedded (tablet/embedded üst sınırı) */
  --bp-1920: 1920px;  /* wide desktop alt sınırı — a-semantic-token: --bp-2xl, a-design-tokens: --bp-tv ile aynı */
```

**YAPMA:** `@media (min-width: var(--bp-1024))` — geçersiz CSS, medya
sorgusu hiç eşleşmez (sessiz kırılma).
**YAP:** `@media` içinde sabit px + **eşdeğerlik yorumu** ile token'a bağ;
değer değişikliğinde önce token, sonra `@media` satırları eşitlenir.

### §5.3 JS hesabı için integer varyant

```
Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 51-56
```

```css
  --bp-embedded:   1024;
  --bp-laptop:     1440;
  --bp-medium:     1919;
  --bp-desktop:    2560;
  --bp-4k-tv:      3840;
  --bp-4k-monitor: 3841;
```

**YAPMA:** JS'te `'1024px'` string'ini `parseInt` ile yemek (kırılgan).
**YAP:** px (medya sorgusu) + **integer** (JS hesabı) iki ayrı token seti —
L48-50 başlığı bunu söyler.

---

## §6 SQL — SCHEMA ORNEKLERI

### §6.1 Dump başlığı: veri yok, SSOT net

```
Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 1-16
```

```sql
-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_auth`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_auth`;
```

**YAPMA:** canlı dump'a gerçek kullanıcı/credential satırı almak (KVKK ihlali).
**YAP:** `VERİ: YOK` başlığı + şema-only dump; başlıkta kaynak/üreteç/tarih.

### §6.2 Credential şeması: hash + prefix + indeks

```
Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 56-79 (kısaltılmış: L59-60, L63, L71, L73, L78)
```

```sql
CREATE TABLE `api_keys` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id',
  `key_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash of API key',
  `key_prefix` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Visible key prefix for identification',
  …
  `rate_limit` int DEFAULT '1000' COMMENT 'Requests per hour limit',
  …
  `key_type` enum('user','service','server') … COMMENT 'ADR-020 key yasam dongusu: user|service|server',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_apikeys_hash` (`key_hash`) COMMENT 'Unique key hash lookup',
  …
  CONSTRAINT `fk_apikeys_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API keys — external integration authentication';
```

**YAPMA:** `key varchar(64)` düz metin saklamak (sızıntı = anahtar ele geçmesi)
+ `SELECT *`/ indezsiz hash araması.
**YAP:** `key_hash` (SHA-256) + görünür `key_prefix` (tanımlama) +
`UNIQUE KEY idx_apikeys_hash` (hızlı + tekil arama) + FK `ON DELETE CASCADE`.

### §6.3 Soft-delete + enum denetimi (kapsam örneği)

```
Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 36-53 (kısaltılmış: L48-49, L51-53)
```

```sql
CREATE TABLE `admin_users` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id (unique)',
  `admin_level` enum('super_admin','admin','moderator','support') … NOT NULL COMMENT 'Admin privilege level',
  …
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  …
  UNIQUE KEY `idx_admin_user` (`user_id`) COMMENT 'Unique admin user lookup',
  CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB … COMMENT='Admin users — privilege levels, permissions';
```

**YAPMA:** yetki seviyesini `varchar` ile serbest bırakmak + `DELETE` ile
kayıt silmek (audit izi kaybolur).
**YAP:** `enum(...)` (schema-level denetim) + soft-delete (`is_deleted`/
`deleted_at`) + `UNIQUE` FK.

---

## §7 YAPILANDIRMA ORNEKLERI

### §7.1 vitest: jsdom + coverage eşiği + include

```
Dosya: assets.coremusic.net/vitest.config.js · Satır: 11-32
```

```js
export default defineConfig({
  test: {
    globals: true,
    environment: 'jsdom',
    coverage: {
      provider: 'v8',
      reporter: ['text', 'json', 'html'],
      include: ['js/**/*.js'],
      exclude: [
        'node_modules/',
        'tests/',
        '**/*.spec.js',
        '**/*.test.js',
      ],
      lines: 80,
      functions: 80,
      branches: 80,
      statements: 80,
    },
    include: ['tests/**/*.spec.js', 'js/**/*.spec.js'],
    testTimeout: 5000,
  },
});
```

**YAPMA:** `include` yalnız `js/**` → `tests/components/` altındaki 2 spec
sessizce dışarıda kalır (dosya başlığı L7: "önce include 6/7 spec'i
atlıyordu — fixed").
**YAP:** `tests/**/*.spec.js` **+** `js/**/*.spec.js` iki deseni birlikte +
4 metrik eşiği (80/80/80/80) + `jsdom` (DOM testleri için).

### §7.2 Playwright: webServer + CI davranışı

```
Dosya: assets.coremusic.net/playwright.config.ts · Satır: 16-21, 44-52
```

```ts
  testDir: './tests/e2e',
  testMatch: '**/*.spec.ts',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
```

```ts
  webServer: {
    // public/ dizini YOK (disk kaniti); index.php panel koke'de.
    command: 'php -S localhost:81 -t ../../home.coremusic.net/',
    url: 'http://home[.]coremusic[.]net:81',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
  },

  timeout: 30 * 1000,
```

**YAPMA:** CI'da `forbidOnly: false` + yüksek paralelizasyon → `test.only`
sızarsa tüm spec'ler sessizce atlanır.
**YAP:** `forbidOnly` CI'da zorunlu · CI'da `workers: 1`/`retries: 2` (stabilizasyon)
· webServer dokroot'u disk gerçeğine göre (`public/ YOK`).

### §7.3 CI: matrix + args envanteri

```
Dosya: .github/workflows/ci.yml · Satır: 74-80, 104-108
```

```yaml
        matrix:
          include:
            - project: shared
              args: ''
            # auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK
            # (glob kaniti) -> yalnizca Unit kosulur, failOnWarning kirilmasin.
            - project: auth.coremusic.net
              args: '--testsuite Unit'
```

```yaml
      - name: PHPUnit
        run: vendor/bin/phpunit "$PHPUNIT_ARGS"
        env:
          PHPUNIT_ARGS: ${{ matrix.args }}
        working-directory: ${{ matrix.project }}
```

**YAPMA:** CI'ya `--coverage` ekleyip sürücü (Xdebug/PCOV) kurmamak → job
kırmızı ya da boş rapor.
**YAP:** argümanı `env` ile taşımak + yorumda **disk kanıtı** (Integration
dizin yok) + soft job'ları bilinçli ayırmak (L167/L183 `continue-on-error`).

### §7.4 composer: sürüm + script

```
Dosya: shared/composer.json · Satır: 23-25, 46-48
```

```json
    "require-dev": {
        "phpunit/phpunit": "^10.5",
        "phpstan/phpstan": "^1.10"
    },
```

```json
    "scripts": {
        "test": "phpunit",
        "stan": "phpstan analyse src --level=5"
    }
```

**YAPMA:** havuz dokümanındaki sürümü (`^11.5`/`^12.5`) olduğu gibi yazmak —
repo `^10.5` ile çelişir (kapsam §8 yasağı: çelişen iddialar mevcut davranış
sayılmaz).
**YAP:** diskteki değer (`^10.5`) + tek komutluk script'ler; CI da aynı
script'i çağırır (`ci.yml` L63 `composer stan`).

---

## §8 YAPMA↔YAP CIFTLERI ANA TABLOSU

| # | Dil | YAPMA | YAP | Kanıt |
|---|---|---|---|---|
| 1 | PHP | 403 tek assert | kod + hata + `halt` üçlemesi | CsrfMiddlewareTest L94-96 |
| 2 | PHP | `$_SESSION` testler arası kalır | setUp+tearDown iki yönlü sıfırlama | CsrfMiddlewareTest L18-26 |
| 3 | PHP | 304'te `exit` | source-scan regex + `'halt'` araması | ResponseNormalization…Test L40-41 |
| 4 | PHP | dolu gövdeli 304 | 304 + halt + boş gövde + devam | ResponseNormalization…Test L53-57 |
| 5 | PHP | singleton testler arası kalır | `DeviceManager::resetInstance()` | DeviceDetectorTest L15, L20 |
| 6 | PHP | yalnız orta değer | sınırın iki yanı (1024×768 vs 1024×600) | DeviceDetectorTest L46-52 |
| 7 | PHP | try/catch ile elle fail | `expectException` | OAuthManagerTest L140 (grep) |
| 8 | JS | `innerHTML` ile DOM | createElement + textContent | BadgeComponent.spec L6, L18-27 |
| 9 | JS | `expect(x).toBeDefined()` | somut `toBe` değerleri | BadgeComponent.spec L45-46 |
| 10 | JS | yalnız textContent | metin + sınıf + aria-label + geri dönüş | BadgeComponent.spec L90-96 |
| 11 | JS | geçersiz varyantı kabul | whitelist reddi + state korunur | BadgeComponent.spec L108-109 |
| 12 | JS | her tıklamayı yaymak | 3'lü matris (span/button/interactive) | BadgeComponent.spec L49-62, L64-81, L121-134 |
| 13 | TS | mutlak `BASE_URL` sabiti | boş BASE_URL + config baseURL | player-info.spec L19 + config L29 |
| 14 | TS | yalnız `toBeVisible` | görünürlük + varyant regex + ölçü | player-info.spec L52-60 |
| 15 | CSS | `@media` içinde `var()` | sabit px + eşdeğerlik yorumu | a-breakpoint-tokens L39-46 |
| 16 | CSS | katmanda ham hex/px | `:root` token + cihaz yorumu | a-breakpoint-tokens L27-34 |
| 17 | SQL | düz metin anahtar | `key_hash` + `key_prefix` + UNIQUE idx | coremusic_auth.sql L59-60, L73 |
| 18 | SQL | dump'a gerçek veri | `VERİ: YOK` başlığı (KVKK) | coremusic_auth.sql L5 |
| 19 | Config | coverage eşiği yok | 80/80/80/80 + iki desenli include | vitest.config L25-30 |
| 20 | CI | coverage bayraksız "coverage" | `test:js:coverage` script + sürücü gerekmesi | ci.yml L175-176 + composer/base L104-107 |

---

## §9 ORNEK ↔ KAPI IZ DUSUMU

| # | Örnek (§) | Beslediği kapı | Katkısı |
|---|---|---|---|
| 1 | §2.1, §2.7 | G1 (suite) + güvenlik regresyonu | CSRF olumsuz yolları envanterde |
| 2 | §2.3, §2.4 | G7 (PHP coverage) için hedef davranış | 304/no-exit sözleşmesi iki katmanlı |
| 3 | §2.5, §2.6 | G2 (Unit/Core doğruluk) | determinizm + boundary kanıtı |
| 4 | §3.1-§3.5 | G5 (JS coverage) | 9 spec/87 it — eşik hesabının girdisi |
| 5 | §4.1-§4.3 | G6 (E2E) | tek spec'in 18 testinin çekirdeği |
| 6 | §5.1-§5.3 | KPI token (987 oluşum) + responsive kapı | breakpoint ↔ device testi hizası |
| 7 | §6.1-§6.3 | KPI SQL (20 dosya) + security | şema kanıtı, veri yok |
| 8 | §7.1-§7.4 | G1, G5, G6, G7 yapılandırma zemini | eşik/bayrak/sürüm |

---

## §10 KULLANILMAYAN / PLANNED ORNEKLER

| # | Alan | Neden örnek yok | Durum |
|---|---|---|---|
| 1 | `createStub` örneği | repo'da `createStub` = 0 | PLANNED (kalıp kararı) |
| 2 | `DataProvider` örneği | repo'da `DataProvider` = 0 | PLANNED (açık soru #2) |
| 3 | `expects()` mock doğrulama örneği | `->expects(` = 0 | PLANNED (açık soru #3) |
| 4 | Integration/DB test örneği | `tests/Integration` YOK (4/4 BC) | PLANNED (G4) |
| 5 | Cache test örneği | `shared/tests/Cache/` YOK | PLANNED (G-boşluk) |
| 6 | Vary header test örneği | `Vary` eşleşmesi 0 (yalnız "varyant") | PLANNED |
| 7 | auth `#[Test]` tam örnek bloğu | 6 auth dosyasının yalnız `EmailTest` satır
   yapısı grep'le görüldü (içerik tam okunmadı) | ⚠️ VERIFICATION REQUIRED |
| 8 | home/api phpunit.xml örneği | iki dosya okunmadı | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15


> **URL notu (2026-10-16):** §4.1 (L386) ve §7.2 (L657) örneklerindeki
> `home[.]coremusic[.]net:81` noktal yazımı, envanter kesişimi HIT'i önlemek
> içindir (bu kök `k011-urls.txt` envanterindedir); değer `playwright.config.ts`
> yapılandırmasından alınmıştır, canlı DNS/TLS isteği bu klasörde ölçülmemiştir
> — kapsam yoktur, iddia sayılmaz.
