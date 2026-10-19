---
title: "K011 · zamanlamali-gorevler — Kod Örnekleri"
type: architecture
kodeme: kademe-2
module: K011-application
parent: zamanlamali-gorevler
status: active
updated: 2026-10-15
group: kod
---

# Zamanlamalı Görevler — Kod Örnekleri

**Amaç:** Depodaki gerçek zamanlama kodunu (JS timer / PHP epoch / SQL zamanlama şeması / CSS duration token) kanıt satırıyla birlikte göstermek; her blok `Dosya: … · Satır: …` künyesi taşır ve YAPMA↔YAP anti-pattern eşlemesiyle bağlanır. Hiçbir örnek uydurulmamış, 2026-10-15 tarihli disk okumasından birebir alınmıştır.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| §1 | JS timer örnekleri (12 blok) | kanıt | implementasyon | Timer yaşam döngüsü kalıpları |
| §2 | PHP epoch/zaman örnekleri (5 blok) | kanıt | implementasyon | Süreç-içi zaman kontrolü |
| §3 | SQL zamanlama şeması örnekleri (3 blok) | kanıt | veri | Zamanlama tabloları / enum'lar |
| §4 | CSS duration token örnekleri (2 blok) | kanıt | sunum | Animasyon süreleri |
| §5 | YAPMA↔YAP anti-pattern tabloları (4 dil) | kural | kalite | Her dil için zıt kutuplar |
| §6 | Zamanlama testi örnekleri + envanter | kanıt | doğrulama | Toleranslı assertion + test sayımı |
| §7 | Açık Sorular | plan | risk | spec §8 kaydı + gap listesi |

---

## §1 — JS Timer Örnekleri (kanıt)

Depo genelinde JS timer envanteri (2026-10-15 sayımı): 95 `.js` dosyası → `setTimeout|setInterval` 20 isabet / 14 dosya (17 `setTimeout`, 3 `setInterval`), `requestAnimationFrame` 9 çağrı (timing-relative, timer sayılmaz).

### Örnek 1 — Saat widget'ı: interval kaydı ve havuza yazma

**Dosya:** `assets.coremusic.net/js/features/WidgetManager.js` · **Satır:** 59–61

```javascript
updateClock();
const id = window.setInterval(updateClock, 1000);
this.#intervals.push(id);
this.#widgets.set('clock', { el: clockWidget, update: updateClock });
```

**Yorum:** `id` doğrudan kaybolmaz; `this.#intervals` havuzuna yazılır. Temizlik `WidgetManager.js:91` (`clearInterval(id)` döngüsü) ile toplu yapılır. **YAPMA:** id'yi saklamadan `setInterval` çağırmak → sızan interval.

### Örnek 2 — Interval temizliği destroy içinde (bileşen yaşam döngüsü)

**Dosya:** `assets.coremusic.net/js/components/composites/WidgetAreaComponent.js` · **Satır:** 81–89

```javascript
update();
this.#clockIntervalId = window.setInterval(update, 30_000);
}

destroy() {
    if (this.#clockIntervalId !== null) {
        window.clearInterval(this.#clockIntervalId);
        this.#clockIntervalId = null;
    }
    super.destroy();
```

**Yorum:** 30 saniyelik yenileme (`30_000` literal) + `destroy()`'da null'a çekme. **YAP:** temizlik, bileşenin kendi yaşam döngüsüne bağlanır (mount→unmount eşleşir). **YAPMA:** `destroy()` timer'ı unutmak → DOM kaldırıldıktan sonra çalışan interval.

### Örnek 3 — Abortable timeout üreticisi (tek yerde tanımlı)

**Dosya:** `assets.coremusic.net/js/router/config/signal-utils.js` · **Satır:** 15–19

```javascript
export function createTimeout(ms) {
    const controller = new AbortController();
    const id = setTimeout(() => controller.abort(), ms);
    return { signal: controller.signal, clear: () => clearTimeout(id) };
}
```

**YORUM:** Timeout, `AbortController` sinyaline çevrilir; `clear()` fan-out'u tek imzada toplar. **YAP:** timer'ı sinyale normalize etmek. **YAPMA:** her modülün kendi `setTimeout` + `clearTimeout` çiftini yeniden yazması.

### Örnek 4 — Fetch timeout kablosu (try/finally garantisi)

**Dosya:** `assets.coremusic.net/js/router/FetchWrapper.js` · **Satır:** 16–21

```javascript
const { signal: timeoutSignal, clear: clearTimeoutFn } = createTimeout(FetchWrapper.CONFIG.TIMEOUT_MS);
const combinedSignal = options.signal
    ? combineSignals(options.signal, this.#controller.signal, timeoutSignal)
    : combineSignals(this.#controller.signal, timeoutSignal);
try { return await this.#fetchWithRetry(url, combinedSignal); }
finally { clearTimeoutFn(); this.#controller = null; }
```

**Yorum:** `finally` bloğu timeout timer'ını her yolda (başarı/hata/iptal) temizler. **YAPMA:** `clearTimeout`'u yalnız başarı dalında çağırmak.

### Örnek 5 — Abort ile iptal edilebilir gecikme (retry backoff iskeleti)

**Dosya:** `assets.coremusic.net/js/router/FetchWrapper.js` · **Satır:** 55–61

```javascript
#delayWithAbort(ms, signal) {
    return new Promise((resolve, reject) => {
        if (signal.aborted) { reject(new DOMException('Aborted', ABORT_ERROR_NAME)); return; }
        const id = setTimeout(() => { signal.removeEventListener('abort', onAbort); resolve(); }, ms);
        const onAbort = () => { clearTimeout(id); reject(new DOMException('Aborted', ABORT_ERROR_NAME)); };
        signal.addEventListener('abort', onAbort, { once: true });
    });
}
```

**Yorum:** Tek `setTimeout` iki yönlü bağlanır: süre dolunca `resolve`, iptal gelince `clearTimeout` + `reject`. `once: true` ve `removeEventListener` sızıntı önler. **YAPMA:** `signal` alıp `addEventListener`'ı `once`'sız kurmak.

### Örnek 6 — Long-press timer (dokunma iptali ile)

**Dosya:** `assets.coremusic.net/js/features/TouchManager.js` · **Satır:** 40–43 ve 48–51

```javascript
this.#longPressTimer = window.setTimeout(() => {
    this.#onLongPress(e, card);
}, TouchManager.LONG_PRESS_MS);
// …
#onTouchMove(e) {
    if (this.#longPressTimer !== null) {
        clearTimeout(this.#longPressTimer);
        this.#longPressTimer = null;
    }
}
```

**Yorum:** Sınıf sabiti `LONG_PRESS_MS` süre kaynağıdır (magic number yok). Hareket edilince timer iptal. Aynı temizlik `TouchManager.js:57` (touchend) ve `:96` (touchcancel) için de tekrarlanır. **YAPMA:** süreyi inline literal olarak yazmak + iptal yolunu unutmak (yanlış tetikleme).

### Örnek 7 — Canlı bölge temizleme timer'ı (AbortSignal bağlu)

**Dosya:** `assets.coremusic.net/js/components/base/ComponentAccessibility.js` · **Satır:** 227–232

```javascript
if (options.clearMs > 0) {
    const timer = setTimeout(() => {
        if (live) live.textContent = '';
    }, options.clearMs);
    options.signal?.addEventListener('abort', () => clearTimeout(timer), { once: true });
}
```

**Yorum:** Erişilebilirlik duyuru metni süre sonunda temizlenir; sahip bileşen iptal ederse timer da düşer. **YAP:** timer + sahiplenici sinyal birlikte. **YAPMA:** canlı bölgeyi temizlemeyi hiç zamanlamamak (ekran okuyucu döngüsü) veya süresiz bırakmak.

### Örnek 8 — Toast timer haritası (id ↔ handle)

**Dosya:** `assets.coremusic.net/js/components/composites/ToastComponent.js` · **Satır:** 162, 188, 201

```javascript
timers.set(id, globalThis.setTimeout(() => this.#remove(id), duration));
// clearAll():
timers.forEach((handle) => globalThis.clearTimeout(handle));
timers.clear();
// #remove(id):
const handle = timers.get(id);
if (handle !== undefined) { globalThis.clearTimeout(handle); timers.delete(id); }
```

**Yorum:** Her bildirimin `id`'si timer handle'ıyla eşlenir; `clearAll()` ve `#remove()` iki ayrı temizlik yolu. Aynı dosyanın eski sürümü `js/components/interactive/ToastComponent.js:148–152` (RAF + `setTimeout`) ile birlikte **iki ToastComponent** bulunduğu notu §7'dedir. **YAPMA:** handle'ı saklamamak → `clearAll()` imkânsız.

### Örnek 9 — OAuth popup polling (interval + sert timeout)

**Dosya:** `assets.coremusic.net/js/oauth-manager.js` · **Satır:** 87–97 ve 100–104

```javascript
const checkInterval = setInterval(() => {
    try {
        if (popup.closed || popup.location.href.includes('oauth-callback')) {
            clearInterval(checkInterval);
            setTimeout(() => resolve(getConnectionStatus(provider)), 1000);
        }
    } catch {
        // Cross-origin — henüz kapanmadı
    }
}, 500);

// 60 saniye timeout
setTimeout(() => {
    clearInterval(checkInterval);
    if (!popup.closed) popup.close();
    resolve({ success: false, message: 'Connection timeout' });
}, 60000);
```

**Yorum:** 500 ms polling + 60 sn kill-switch. `clearInterval` iki yerde (başarı ve timeout). **YAPMA:** timeout'suz polling (sonsuz döngü) veya `clearInterval`'ı yalnız başarı yolunda bırakmak. **Not:** buradaki süreler inline literal; token'a bağlı değil (bkz. §4 çelişkisi).

### Örnek 10 — Resize debounce (debounce kalıbının kanıtı)

**Dosya:** `assets.coremusic.net/js/device-loader.js` · **Satır:** 210, 214–215

```javascript
let resizeTimer = null;
// …
function onResize(state) {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
        const w = window.innerWidth || document.documentElement.clientWidth;
```

**Yorum:** Klasik debounce: yeni olayda eskisi temizlenir, süre sonunda tek iş çalışır (yorum satırı "debounced 300ms"). Aynı kalıp `js/auth/auth-gender-bg.js:334–335`. **YAPMA:** her resize'da doğrudan ağır iş yapmak.

### Örnek 24 — Hemen çalıştır + gecikmeli yedek (asenkron kanıtı)

**Dosya:** `assets.coremusic.net/js/core/footer.init.js` · **Satır:** 21–23

```javascript
applyProgressFills();
window.addEventListener('load', applyProgressFills);
setTimeout(applyProgressFills, 300);
```

**Yorum:** Aynı iş üç yolla güvence altında: anında çağrı, `load` olayı, 300 ms gecikmeli yedek — `setTimeout` satırı asla **bekleme** olarak kullanılmaz (MDN #1 asenkron claim'i §4 web-arastirma matrisinde). **YAPMA:** yalnız `setTimeout(..., 300)` ile ilk çizimi bekletmek (boş görünen alan).

### Örnek 25 — Yedek tetik yolu: load olayı + kısa timeout

**Dosya:** `assets.coremusic.net/js/features/welcome-modal.js` · **Satır:** 138–143

```javascript
window.addEventListener('load', () => {
    if (!opened && isEmbeddedDevice()) open();
});
setTimeout(() => {
    if (!opened && isEmbeddedDevice()) open();
}, 200);
```

**Yorum:** `opened` bayrağı çift açılışı engeller; `isEmbeddedDevice()` koşulu iki yolda da aynı. **YAP:** koşul + bayrak = idempotent açılış. **YAPMA:** bayraksız iki tetik (modal iki kez açılır).

---

## §2 — PHP Epoch / Zaman Örnekleri (kanıt)

PHP tarafı envanter (2026-10-15): `shared/src` 203 dosya / 20 dizin → `sleep|usleep` **0 isabet** (üretim kodunda bloklayıcı bekleme yok), `cron|crontab` **0 isabet**. Dolayısıyla PHP zamanlaması **epoch karşılaştırma** ile kurulur.

### Örnek 11 — Merkezi zaman sabitleri (SSOT)

**Dosya:** `shared/src/Session/SessionConfig.php` · **Satır:** 16–19

```php
public const COOKIE_EXPIRY = 42000;
public const MAX_LIFETIME = 1800;          // 30 dakika
public const IDLE_TIMEOUT = 3600;          // 1 saat
public const ROTATION_INTERVAL = 1800;     // 30 dakikada bir rotation
```

**Yorum:** Tüm süre kararları tek sınıfın `const`'larında; sihirli sayı dağılmamış. **YAP:** süreleri sabitlerde tutmak. **YAPMA:** `sleep(1800)` gibi süreç-içi bloklayıcı bekleme (depo içinde zaten 0 isabet — korunacak durum).

### Örnek 12 — Periyodik kontrol: koşul tabanlı rotation (planlayıcı değil)

**Dosya:** `shared/src/Session/SessionLifecycle.php` · **Satır:** 55–62

```php
$lastRotation = $_SESSION['_session_rotated_at'] ?? 0;
$now = time();
if ($lastRotation === 0) {
    $_SESSION['_session_rotated_at'] = $now;
} elseif (($now - $lastRotation) >= SessionConfig::ROTATION_INTERVAL) {
    session_regenerate_id(true);
    $_SESSION['_session_rotated_at'] = $now;
}
```

**Yorum:** "30 dakikada bir" kararı **her istekte** epoch farkıyla sınanır — ayrı bir cron/worker yok. **YAP:** talep-içi periyodik kontrol. **YAPMA:** sabit `sleep` ile periyot beklemek; ya da epoch'u `$_SESSION`'a yazmamak (kontrol her seferinde tetiklenir).

### Örnek 13 — Idle timeout kontrolü

**Dosya:** `shared/src/Session/SessionLifecycle.php` · **Satır:** 99–103

```php
$lastActive = $_SESSION['_session_last_active'] ?? $_SESSION['last_activity'] ?? null;
if ($lastActive === null) {
    return false;
}
return (time() - (int)$lastActive) >= SessionConfig::IDLE_TIMEOUT;
```

**Yorum:** `time()` tek zaman kaynağı; eşik `SessionConfig::IDLE_TIMEOUT`. **YAPMA:** `$_SESSION`'a yazılırken timezone/farklı bir saat kaynağı kullanmak (epoch farkı bozulur).

### Örnek 14 — TTL cache: yazılış anında son kullanma damgası

**Dosya:** `shared/src/Cache/MemoryAdapter.php` · **Satır:** 16–18 ve 23–28

```php
$entry = $this->store[$key];
if ($entry['expires'] > 0 && $entry['expires'] < time()) {
    unset($this->store[$key]);
    return false;
}
// set():
$this->store[$key] = [
    'value'   => $value,
    'expires' => $ttl > 0 ? time() + $ttl : 0,
];
```

**Yorum:** Süre sonu yazma anında hesaplanır (`time() + $ttl`), okuma anında karşılaştırılır; `expires = 0` sonsuz demektir. **YAP:** TTL'i tek seferde hesaplamak. **YAPMA:** okuma sırasında yeniden uzatmak (kaydırılmış ömür).

### Örnek 15 — Zamana sıralı kimlik: zaman damgası taşıyan tek okuma

**Dosya:** `shared/src/Security/UuidV7.php` · **Satır:** 19–23

```php
$ms = (int)(microtime(true) * 1000);

// 6-byte timestamp (48 bit)
$time = pack('N', ($ms >> 16) & 0xFFFFFFFF)
      . pack('n', $ms & 0xFFFF);
```

**Yorum:** Zaman kavramı kimlik üretiminde kullanılır (sıralanabilir UUID v7) — "zamanlama görevi" değil, zaman-bağımlı veri üretimi. **YAPMA:** sıralama için `rand()`/`uniqid()` kullanmak.

---

## §3 — SQL Zamanlama Şeması Örnekleri (kanıt)

SQL envanteri (2026-10-15): `.ai/.sql/mysql` 20 dosya → `ON SCHEDULE` **0 isabet** (MySQL event scheduler tanımı yok), `radio_schedules` tablosu + `CRON_*` enum değerleri **var**.

### Örnek 16 — Olay tipi enum'unda cron/queue/kworker değerleri (üreticisi yok)

**Dosya:** `.ai/.sql/mysql/coremusic_logs.sql` · **Satır:** 325

```sql
`event_type` enum('SERVICE_START','SERVICE_STOP','SERVICE_ERROR','CRON_JOB_START','CRON_JOB_COMPLETE','CRON_JOB_FAILED','DEPLOYMENT_START','DEPLOYMENT_COMPLETE','DEPLOYMENT_FAILED','CONFIG_CHANGE','SCHEMA_MIGRATION','BACKUP_START','BACKUP_COMPLETE','BACKUP_FAILED','HEALTH_CHECK','HEALTH_CHECK_FAILED','DISK_SPACE_WARNING','MEMORY_WARNING','CPU_WARNING','SSL_CERT_EXPIRING','CRON_SCHEDULED','QUEUE_OVERFLOW','WORKER_START','WORKER_STOP','GRACEFUL_SHUTDOWN','EMERGENCY_STOP') ... NOT NULL COMMENT 'Sistem olay tipi',
```

**Yorum:** Şema cron/queue/worker olaylarına **hazır**; ancak `cron|crontab` grep'i PHP/JS tarafında 0 → bu enum değerlerini yazan üretici kod **diskte yok** (PLANNED gap, §7). **YAPMA:** enum'da var diye "cron çalışıyor" sonucu çıkarmak.

### Örnek 17 — Haftalık yayın programı tablosu (gerçek zamanlama verisi)

**Dosya:** `.ai/.sql/mysql/coremusic_musics.sql` · **Satır:** 442–457 (CREATE bloğu)

```sql
CREATE TABLE `radio_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int unsigned NOT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rs_schedule` (`station_id`,`day_of_week`,`start_time`),
  KEY `idx_rs_day` (`day_of_week`),
  KEY `idx_rs_time` (`start_time`),
  ...
  CONSTRAINT `radio_schedules_chk_1` CHECK (((`day_of_week` >= 0) and (`day_of_week` <= 6)))
) ENGINE=InnoDB ... COMMENT='Radyo yayın programları — weekly schedules';
```

**Yorum:** Depodaki tek **yinelenen zamanlama** modeli: gün (0–6 CHECK) + `time` aralığı + tekil anahtar. `is_recurring` kolonu tekrar kuralını veriyle taşır; **uygulama tarafında bu tabloyu zamanlayıcıya bağlayan kod grep ile bulunamadı** (§7 gap). **YAPMA:** `start_time > end_time` gibi sınırı CHECK ile bile zorlamamış olmak (gece yarısını geçen yayınlar veriye bırakılmış) — dokümante edilir, uydurulmaz.

### Örnek 18 — Zaman indeksleri (sorgu tarafı zamanlama)

**Dosya:** `.ai/.sql/mysql/coremusic_musics.sql` · **Satır:** 453–454

```sql
KEY `idx_rs_day` (`day_of_week`),
KEY `idx_rs_time` (`start_time`),
```

**Yorum:** "Şu gün/saatte ne var" sorgusu için ikili indeks. **YAP:** aralık sorgulanacak `time` kolonuna indeks. **YAPMA:** yalnız `station_id` indeksleyip saat sorgusunu tam taramaya bırakmak.

---

## §4 — CSS Duration Token Örnekleri (kanıt)

CSS envanteri (2026-10-15): `Css/01_Abstracts` 19 dosya; süre token'ları tek dosyada toplanır, çelişkili ikinci tanım `a-theme-config.css:86`.

### Örnek 19 — Kanonik süre token'ları

**Dosya:** `assets.coremusic.net/Css/01_Abstracts/a-design-tokens.css` · **Satır:** 107–113

```css
--duration-instant: 0ms;
--duration-fast: 100ms;
--duration-normal: 200ms;
--duration-slow: 300ms;
--duration-slower: 500ms;
--duration-slowest: 700ms;
--duration-skeleton: 1500ms;
```

**Yorum:** Animasyon süreleri soyut adlandırılmış; `136–142` satırlarında `--transition-*` varyantları `var(--duration-normal)` ile okunur. **YAP:** sabit `ms` değeri yalnız `01_Abstracts` token dosyasında. **YAPMA:** katman dosyasına ham `200ms` yazmak.

### Örnek 20 — Çelişkili ikinci tanım (kanıt olarak korunur)

**Dosya:** `assets.coremusic.net/Css/01_Abstracts/a-theme-config.css` · **Satır:** 85–86

```css
/* --- Duration (used by components via fallback) --- */
--duration-fast: 0ms;
```

**Yorum:** Aynı token adı iki dosyada farklı değer: `100ms` (design-tokens:108) vs `0ms` (theme-config:86). Yüklenme sırası kazananı belirler → öngörüsüiz davranış riski. **YAPMA:** ikinci tanımı sessizce silmek; **YAP:** önce ADR/onay, sonra tek kaynağa indirme (§7 gap #4).

---

## §5 — YAPMA↔YAP Anti-Pattern Tabloları

### 5.1 JavaScript

| # | YAPMA (anti-pattern) | YAP (kanıt) | Kanıt satırı |
|---|---|---|---|
| 1 | Timer id'sini saklamamak | Havuza yazıp toplu temizlemek | `features/WidgetManager.js:59–60`, `:91` |
| 2 | `destroy()`'da `clearInterval` unutmak | mount↔unmount eşleşen temizlik | `components/composites/WidgetAreaComponent.js:85–89` |
| 3 | Her yerde inline `setTimeout` + temizlik | Tek `createTimeout(ms)` üreticisi | `router/config/signal-utils.js:15–19` |
| 4 | Başarı dalında `clearTimeout` | `finally`'de temizlik | `router/FetchWrapper.js:21` |
| 5 | Abort sinyalini dinlememek | `once: true` dinleyici + `removeEventListener` | `router/FetchWrapper.js:58–60` |
| 6 | Magic number süre | Sınıf sabiti (`LONG_PRESS_MS`) | `features/TouchManager.js:42` |
| 7 | Sonsuz polling (timeout'suz) | 500 ms polling + 60 sn kill-switch | `oauth-manager.js:87–104` |
| 8 | Timer handle'ı haritada tutmamak | `id ↔ handle` Map + iki temizlik yolu | `components/composites/ToastComponent.js:162/188/201` |

### 5.2 PHP

| # | YAPMA (anti-pattern) | YAP (kanıt) | Kanıt satırı |
|---|---|---|---|
| 1 | `sleep()` ile periyot beklemek | Epoch karşılaştırmalı koşul (PHP'te `sleep\|usleep` = 0 isabet) | `Session/SessionLifecycle.php:59` |
| 2 | Süreleri inline yaymak | Tek SSOT sınıf sabitleri | `Session/SessionConfig.php:16–19` |
| 3 | TTL'i okuma anında kaydırmak | Yazılış anında `time() + $ttl` | `Cache/MemoryAdapter.php:27` |
| 4 | Zamanı session dışındaki kaynaktan almak | Her yerde `time()` | `SessionLifecycle.php:56/103` |
| 5 | Sıralama için rastgele üretim | Zaman damgalı UUID v7 | `Security/UuidV7.php:19–23` |

### 5.3 SQL

| # | YAPMA (anti-pattern) | YAP (kanıt) | Kanıt satırı |
|---|---|---|---|
| 1 | Zamanlama verisini serbest metinde tutmak | `time` + CHECK + tekil anahtar | `coremusic_musics.sql:446–457` |
| 2 | "cron var" diye varsaymak (kod yokken) | `ON SCHEDULE` = 0, üretici kod = 0 → PLANNED | `.ai/.sql/mysql/*` grep 2026-10-15 |
| 3 | Saat sorgusunda indeks yok | `idx_rs_day` + `idx_rs_time` | `coremusic_musics.sql:453–454` |
| 4 | Enum'u üreticisiz doldurmak | Enum değerlerini üreticiye bağlamayan yerde dokümante etmek | `coremusic_logs.sql:325` + §7 gap #1 |

### 5.4 CSS

| # | YAPMA (anti-pattern) | YAP (kanıt) | Kanıt satırı |
|---|---|---|---|
| 1 | Ham `200ms` katman dosyasına yazmak | `--duration-*` token'ı | `a-design-tokens.css:107–113` |
| 2 | Aynı token'ı ikinci dosyada yeniden tanımlamak | Tek tanım (mevcut durum: çelişki var → gap #4) | `a-theme-config.css:86` |
| 3 | `var()` ile media query içinde okumak | Token yalnız tanımda, okuma katmanda | AGENTS.md §10 kuralı |

---

## §6 — Zamanlama Testi Örnekleri (kanıt)

Test envanteri (2026-10-15): PHP test **45 dosya**, zaman kavramlı **35 isabet / 8 dosya**; JS test **9 spec**, timer testi **0** (`useFakeTimers` 0 isabet).

### Örnek 21 — Toleranslı zaman assertion'ı (delta ile)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 106–107

```php
// `date()` + `time()` aynı saniyede kalmayabilir → 3 sn payı.
$this->assertEqualsWithDelta(time() + $seconds, (int) strtotime($result), 3, (string) $ttl);
```

**Yorum:** Zaman testinde sabit eşitlik yerine 3 sn delta. **YAP:** `assertEqualsWithDelta`. **YAPMA:** `assertEquals(time() + $seconds, strtotime(...))` (saniye sınırında flaky).

### Örnek 22 — Geçmiş/gelecek zaman üretimi (test verisi)

**Dosya:** `shared/tests/Repository/ApiKeyCreateCommandTest.php` · **Satır:** 186 (benzer: `ApiKeyRepositoryTest.php:115/128`)

```php
'past'   => date('Y-m-d H:i:s', time() - 60),
```

**Yorum:** Süre dolu/süre dolmamış senaryolar epoch farkıyla üretilir. **YAPMA:** sabit tarih string'i yazıp yaz saati (DST) kırılganlığı getirmek.

### Örnek 23 — Eksik alan: JS timer testi yok (kanıt olarak negatif)

**Ölçüm:** `assets.coremusic.net/js/**` + test spec'leri → `useFakeTimers|jest.advanceTimers|sinon.clock` **0 isabet** (2026-10-15).

**Yorum:** 20 timer isabetinin hiçbiri doğrudan test edilmiyor (ör. `signal-utils.createTimeout`, `TouchManager` long-press, `ToastComponent` timer haritası). **YAP:** sahte saat ile kural testleri (öncelik §7 gap #3).

### §6.1 Zaman testi envanteri (ölçüm tablosu)

| # | Yüzey | Sayım (2026-10-15) | Timing isabeti | Yorum |
|---|---|---|---|---|
| 1 | `shared/tests/**/*Test.php` | 45 dosya | 35 isabet / 8 dosya | `time()` fark testleri baskın |
| 2 | `assertEqualsWithDelta` kullanımı | `ApiKeyCreateCommandTest.php:107` | 3 sn tolerans | Doğru kalıp, yaygın değil |
| 3 | Test verisinde epoch (`time() ± n`) | `ApiKeyCreateCommandTest.php:186`, `ApiKeyRepositoryTest.php:115/128`, `JwtServiceTest.php:72/127` | 6 satır | Geçmiş/gelecek üretimi standart |
| 4 | `sleep`/`usleep` ile test beklemesi | 0 | — | Flaky bekleme yok (iyi) |
| 5 | JS test spec sayısı | 9 | — | Küçük yüzey |
| 6 | JS timer testi (`useFakeTimers` vb.) | **0** | 0 | 20 timer'ın hiçbiri korumasız |
| 7 | Vitest/Playwright sahte saat kurulumu | 0 | — | İlk eklenecek altyapı |
| 8 | PHPUnit sürümü (composer kanıtı) | ^10.5/^11.0 | — | QA agent kaydı ile uyumlu |

### §6.2 Hızlı kalite kontrol listesi (yeni timer kodu için)

| # | Kontrol | Referans örnek |
|---|---|---|
| 1 | Timer id'si bir yere yazılıyor mu? | Örnek 1, 6, 8 |
| 2 | İptal/yıkım yolu var mı (`clear*`)? | Örnek 2, 5, 6 |
| 3 | `finally`/`destroy` içinde temizlik mi? | Örnek 4, 2 |
| 4 | Süre sabitte mi, inline literal mi? | Örnek 6 ✗ / Örnek 9 ⚠️ |
| 5 | Callback ok fonksiyonu mu (`this` korunur)? | Örnek 1, 8 |
| 6 | String `code` kullanılmış mı? (0 olmalı) | §4-6 web-arastirma kanıtı: 0/20 |
| 7 | `AbortSignal`/sinyal var mı (ağ/touch)? | Örnek 3, 5, 7 |
| 8 | Test yazıldı mı (sahte saat)? | §6-6 → **henüz yok** |

---

## §7 — Açık Sorular

- Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.
- ⚠️ **GAP 1 — cron altyapısı YOK:** PHP `cron|crontab` 0 isabet, `ON SCHEDULE` 0 isabet; yalnız `coremusic_logs.sql:325` enum değerleri (`CRON_JOB_*`, `CRON_SCHEDULED`, `QUEUE_OVERFLOW`, `WORKER_*`) var → üretici kod yok. Durum: **PLANNED**.
- ⚠️ **GAP 2 — worker/queue kodu YOK:** timer envanteri yalnız istemci tarafı (17 `setTimeout` / 3 `setInterval` / 9 RAF). Sunucu tarafı periyodik iş = `SessionLifecycle` koşul kontrolleriyle sınırlı.
- ⚠️ **GAP 3 — JS timer testi 0:** 9 spec içinde timer kuralı yok; ilk hedef `signal-utils.js:15–19` ve `FetchWrapper.js:55–61`.
- ⚠️ **GAP 4 — token çelişkisi:** `--duration-fast` = `100ms` (`a-design-tokens.css:108`) vs `0ms` (`a-theme-config.css:86`) → ADR gerekiyor.
- ⚠️ **GAP 5 — SQL enum üreticisiz:** `radio_schedules` (442) ve `CRON_*` enum (325) veri/tanım olarak var, bunları yazan uygulama kodu yok.
- ⚠️ **Çift ToastComponent:** `components/composites/ToastComponent.js` (162–201, timer haritası) ve `components/interactive/ToastComponent.js` (148–152) birlikte duruyor — hangisi aktif, doğrulanmalı.
- Örneklerin tümü 2026-10-15 disk okumasından alınmıştır; URL araştırma kanıtı ayrı dosyadadır (`zamanlamali-gorevler-web-arastirma.md`).

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
