---
title: "K011 · zamanlamali-gorevler — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: zamanlamali-gorevler
status: active
updated: 2026-10-15
group: mimari
---

# K011 · zamanlamali-gorevler — Kapsam

> **Amaç:** Bu dosya, `zamanlamali-gorevler` alt-modülünün **kapsam sınırını** tanımlar:
> hangi zamanlama varlıkları bu düğüme aittir, hangileri komşu düğümlere devredilir ve
> kapsamın ölçülebilir kabul kriterleri nelerdir. Tüm sayılar 2026-10-15 disk ölçümüdür.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 ELI15 | mimari | ozet | Kapsamı hızlı anla |
| 2 | §2 Sınır: ne DEĞİL | mimari | sinir | Hangi iş bu düğümde değil |
| 3 | §3 Kapsam içi envanter (3 katman) | mimari | envanter | Zamanlama yüzeylerini haritala |
| 4 | §4 Akış tabloları | mimari | akis | Tetikleme akışlarını oku |
| 5 | §5 Kapsam dışı | mimari | dis | Komşu düğümlere devir |
| 6 | §6 Açık sorular | mimari | belirsizlik | Kademe-3 öncesi kapat |
| 7 | §A Ekler (ölçüm, terimler) | mimari | yontem | Sayım tekrarlanabilirliği |

---

## §1 — ELI15 (kısa anlatım)

**eli10:** Uygulama zaman geldiğinde bir şey yapar: saat widget'ı her saniye güncellenir,
toast 4 saniye sonra kaybolur, resize sonrası 300ms beklenir, oturum 30 dakikada bir
kimlik yeniler. Bu "zamanla tetiklenen işler" burada.

**eli15:** Zamanlama bu depoda **tek bir yerde** yaşamaz — **üç ayrı hızda** dağılır:

| Hız | Katman | Ne tür zamanlama | Sahip | Durum |
|---|---|---|---|---|
| **Milisaniye** | JS (istemci) | `setTimeout` · `setInterval` · RAF · debounce | UI Designer | ✅ IMPLEMENTED |
| **Saniye** | PHP (sunucu) | `time()` farkı · TTL · rotation · idle kontrolü | Backend Architect | ✅ IMPLEMENTED |
| **Dakika+/cron** | Sunucu zamanlayıcısı | Periyodik iş (backup, temizlik, rapor) | ⚠️ yok | ❌ PLANNED (boşluk) |

**Üçüncü hız kritiktir:** Repo'da **çalışan cron altyapısı YOKTUR**. PHP `shared/src`
içinde `cron` / `crontab` **0 isabet** (`Select-String`, 2026-10-15). SQL şemasında ise
CRON/QUEUE/WORKER **enum değerleri** vardır (`coremusic_logs.sql:325`) ama bu değerleri
**yazan/çalıştıran kod yok**. Bu, "eksiklik" değil **kasıtlı boşluktur** — kademe-2'de
belgelenir, uydurma zamanlayıcı kodu **üretilmez**.

**Zamanlamanın gerçek sahipliği (disk kanıtı):**

| # | Katman | Dosya sayısı | İsabet | Kanıt yöntemi |
|---|---|---|---|---|
| 1 | JS istemci zamanlayıcı | 14 dosya | 20 (`setTimeout\|setInterval`) | `Select-String`, 2026-10-15 |
| 2 | PHP istek-zamanı kontrolü | 12+ dosya | 60+ (`time()\|microtime\|DateTime`) | `Select-String`, 2026-10-15 |
| 3 | CSS süre tokenları | 3 dosya | 27 (`duration\|transition\|ease`) | `Select-String`, 2026-10-15 |
| 4 | SQL zamanlama şeması | 2 dosya | 5 (`radio_schedules` + enum) | `Select-String`, 2026-10-15 |
| 5 | PHP cron altyapısı | **0 dosya** | **0** | `Select-String` → **boşluk** |

---

## §2 — Sınır: ne DEĞİL

| # | Bu düğüm DEĞİLDİR | Sorumlu | Gerekçe (disk kanıtı) |
|---|---|---|---|
| 1 | Sunucu cron/queue/worker **altyapısının kendisi** | ⚠️ PLANNED — sahibi atanmadı | PHP `cron\|crontab` 0 isabet → §6-1 |
| 2 | Arka plan servis çalıştırma (daemon, supervisor) | K012-services | Repo'da supervisor/systemd tanımı yok → UNKNOWN |
| 3 | Web Worker implementasyonu | UI Designer (gelecek) | JS `Worker(` 0 isabet → §6-2 (havuzda MDN kaynağı var) |
| 4 | `radio_schedules` **iş mantığı** (yayın akışı kararı) | K008-domain | SQL tablosu var (`coremusic_musics.sql:442`) — CRUD burada değil |
| 5 | EventDispatcher'ın kendisi (event yayımı) | K011/event-akislari | `shared/src/Events/` 14 dosya — kardeş düğüm |
| 6 | Cache adaptörünün TTL **mekanizması** | K011/caching | `MemoryAdapter` TTL kodu var — kardeş düğüm |
| 7 | Rate limiter'ın pencere hesabı | K009-security | `RateLimiterMiddleware` — güvenlik katmanı |
| 8 | CSS animasyon **tasarımı** (görsel karar) | K11-ux / ui-design | Token **değerleri** burada, tasarım kararı orada |
| 9 | Uptime/health-check **altyapısı** | K016-observability | `HEALTH_CHECK` enum değeri var, üretici yok |
| 10 | Üretim kodu (`*.php` · `*.js` · `*.css` · `*.sql`) | İlgili katman agent'ları | Kök AGENTS.md §16.4 dokunulmaz yüzeyler |

**Sınır kuralı:** Yukarıdaki 10 satırdan birine düşen bir görev geldiğinde bu modül yalnız
**taşıma/kanıt** rolüyle katılır; karar ve kod hedefe devredilir.

**Özel durum (spec §9 — "çalışan cron altyapısı var mı" sorusu):**
Bu sorunun cevabı **keşifle doğrulanmıştır: HAYIR**. Kanıt üç yönden:

| # | Arama | Kapsam | Sonuç |
|---|---|---|---|
| 1 | `cron\|crontab` | `shared/src/**/*.php` (203 dosya) | **0 isabet** |
| 2 | `\bqueue\b\|\bworker\b\|crontab\|scheduler` | `assets.coremusic.net/js/**/*.js` (95 dosya) | **1 isabet** — `SidebarManager.js:163 scheduleRender()` (RAF sarmalayıcısı, kuyruk DEĞİL) |
| 3 | `EVENT\|SCHEDULE\|EVERY\|ON SCHEDULE` | `.ai/.sql/mysql/*.sql` (20 dosya) | **0 MySQL EVENT** — yalnız `radio_schedules` tablosu (statik veri) + enum değerleri |

Sonuç: **cron altyapısı YOK**. Bu bilgi PLANNED/boşluk olarak §6'da kayıtlıdır.

---

## §3 — Kapsam içi yüzey envanteri

### §3.1 Birinci yüzey — JS istemci zamanlayıcıları (✅ IMPLEMENTED)

| # | Dosya | Satır | Zamanlayıcı | Süre | Rol |
|---|---|---|---|---|---|
| 1 | `js/features/WidgetManager.js` | 95 | `setInterval` | 1000ms | Saat widget'ı dakika güncelleme |
| 2 | `js/components/composites/WidgetAreaComponent.js` | 92 | `setInterval` | 30_000ms | Saat alanı 30sn tazeleme |
| 3 | `js/oauth-manager.js` | 321 | `setInterval` + `setTimeout` | 500ms / 1000ms / 60000ms | Popup kapanış yoklama + timeout |
| 4 | `js/router/FetchWrapper.js` | 63 | `setTimeout` | parametre | Abort-sinyalli gecikme |
| 5 | `js/router/config/signal-utils.js` | 19 | `setTimeout` | parametre | `createTimeout()` sinyal fabrikası |
| 6 | `js/router/RouterEventManager.js` | 48 | `setTimeout` | 100ms | Hover-prefetch debounce |
| 7 | `js/features/TouchManager.js` | 101 | `setTimeout` | sabit | Long-press tanımı |
| 8 | `js/components/composites/ToastComponent.js` | 221 | `setTimeout` | 4000/6000ms | Toast otomatik kapanış |
| 9 | `js/components/interactive/ToastComponent.js` | 208 | `setTimeout` | duration | Toast otomatik kapanış (2. varyant) |
| 10 | `js/components/base/ComponentAccessibility.js` | 235 | `setTimeout` | `clearMs` | aria-live temizleme |
| 11 | `js/device-loader.js` | 423 | `setTimeout` | 300ms | Resize debounce |
| 12 | `js/auth/auth-gender-bg.js` | — | `setTimeout` | 300ms | Resize debounce (auth) |
| 13 | `js/core/footer.init.js` | 214 | `setTimeout` | 300ms | Progress fill gecikmeli uygulama |
| 14 | `js/features/welcome-modal.js` | — | `setTimeout` | 200ms | Embedded cihaz yarışı düzeltmesi |

**Toplam:** 14 dosya · 20 isabet (`setTimeout` 17 · `setInterval` 3).

**Kritik ayrım — RAF ≠ zamanlayıcı:** `SidebarManager.js:163 scheduleRender()`
`requestAnimationFrame` kullanır (bir sonraki çizim karesi, ~16ms) — `setTimeout`/`setInterval`
**değildir**; sayaçlara dahil edilmez, §3.1'de yalnız "zamanlama akrabası" olarak anılır.

### §3.2 İkinci yüzey — PHP istek-zamanı tembel kontrolü (✅ IMPLEMENTED)

PHP'de **uyuyan (sleep) kod YOKTUR** (`sleep()\|usleep()` 0 isabet — bu doğru kaçınmadır;
php.net sleep uyarısı: uyuma isteği bloklar ve `max_execution_time`'a girmez). Bunun yerine
her istekte **tembel (lazy) kontrol** yapılır — zamanlama, "iş yapıldığı anda" hesaplanır:

| # | Dosya | Satır | Desen | Süre sabiti |
|---|---|---|---|---|
| 1 | `shared/src/Session/SessionConfig.php` | 63 | Süre SSOT'u | `ROTATION_INTERVAL=1800` · `IDLE_TIMEOUT=3600` · `MAX_LIFETIME=1800` · `COOKIE_EXPIRY=42000` |
| 2 | `shared/src/Session/SessionLifecycle.php` | 128 | Rotation + idle + expiry | `time() - $last >= Sabit` (5 kullanımda) |
| 3 | `shared/src/PageRouter/SessionInitializer.php` | — | Aynı denetimler | `time()` 8 kullanım |
| 4 | `shared/src/Cache/MemoryAdapter.php` | 57 | TTL son kullanma | `expires < time()` (2 kullanım) |
| 5 | `shared/src/AI/MemorySystem.php` | 242 | TTL + `expires_at` | `time() + $ttl` (10+ kullanım) |
| 6 | `shared/src/Security/JwtService.php` | 137 | iat/exp saniyeleri | `time()` |
| 7 | `shared/src/Security/UuidV7.php` | 104 | Milisaniye damgası | `microtime(true)*1000` |
| 8 | `shared/src/AI/AIWorkflow.php` | 271 | Ölçüm (execution time) | `microtime(true)` farkı (10 kullanım) |
| 9 | `shared/src/AI/ToolCalling.php` | — | Ölçüm | `microtime(true)` |
| 10 | `shared/src/Log/FileHandler.php` | — | Damga üretimi | `date('Y-m-d H:i:s')` |
| 11 | `shared/src/PageRouter/StructuredLogger.php` | — | Damga üretimi | `DateTimeImmutable` |
| 12 | `shared/src/Api/ApiResponse.php` | — | Damga üretimi | `DateTimeImmutable` (3 kullanım) |

**İlkeler:**

| # | İlke | Kanıt |
|---|---|---|
| 1 | Süre değerleri **sabit (const)** olarak tek yerde tutulur | `SessionConfig` 4 sabit |
| 2 | Karar her istekte `time() - $olay >= $sabit` ile verilir | `SessionLifecycle` 5 denetim |
| 3 | Bloklama (sleep) **yasak** — PHP istek-süreli çalışır | `sleep()` 0 isabet |
| 4 | Milisaniye gereken yerde `microtime(true)` kullanılır | `UuidV7` · `AIWorkflow` |

### §3.3 Üçüncü yüzey — CSS süre tokenları (✅ IMPLEMENTED)

| # | Dosya | Satır | Token grubu | Adet |
|---|---|---|---|---|
| 1 | `Css/01_Abstracts/a-design-tokens.css` | 676 | `--duration-*` (7) + `--ease-*` (7) + `--transition-*` (5) | 19 |
| 2 | `Css/01_Abstracts/a-theme-config.css` | 170 | `--dur-*` (6) + `--duration-fast` fallback (1) | 7 |
| 3 | `Css/01_Abstracts/a-primitive-tokens.css` | — | `--cm-skeleton-duration` (1) | 1 |

**Değer tablosu (a-design-tokens.css:107–113):**

| Token | Değer | Kullanım |
|---|---|---|
| `--duration-instant` | `0ms` | anlık geçiş |
| `--duration-fast` | `100ms` | hızlı geri bildirim |
| `--duration-normal` | `200ms` | standart geçiş |
| `--duration-slow` | `300ms` | yavaş |
| `--duration-slower` | `500ms` | daha yavaş |
| `--duration-slowest` | `700ms` | en yavaş |
| `--duration-skeleton` | `1500ms` | iskelet animasyonu |

**⚠️ ÇELIŞKİ (kayıtlı):** `--duration-fast` iki dosyada **farklı** —
`a-design-tokens.css:108` = `100ms` · `a-theme-config.css:86` = `0ms` ("used by components
via fallback"). Hangisinin geçerli olduğu import zincirine bağlı → §6-3.

### §3.4 Dördüncü yüzey — SQL zamanlama şeması (🟡 YER TUTUCU)

| # | Dosya | Satır | Varlık | Tür | Durum |
|---|---|---|---|---|---|
| 1 | `coremusic_musics.sql` | 442–457 | `radio_schedules` | Tablo (`day_of_week` 0–6, `start_time`, `uq_rs_schedule`) | ✅ var (statik veri) |
| 2 | `coremusic_logs.sql` | 325 | `log_system_events.event_type` | Enum: `CRON_JOB_START/COMPLETE/FAILED`, `CRON_SCHEDULED`, `QUEUE_OVERFLOW`, `WORKER_START/STOP` | 🟡 yer tutucu |
| 3 | `coremusic_musics.sql` | 366 | `podcast_episodes` | CHECK `status='scheduled'` | 🟡 yer tutucu |

**Kritik okuma:** Enum'da CRON/QUEUE/WORKER değerleri **vardır** ama bu değeri **yazan
kod yoktur** (PHP cron 0). Şema **geleceğe hazır**, altyapı **kurulmamış** → PLANNED.

---

## §4 — Akış tabloları

### §4.1 Akış A — İstemci saat tazeleme (WidgetManager)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | `init()` | `#initClock()` | — | Saat widget'ı bulunur |
| 2 | İlk çalıştırma | `updateClock()` | 0 | Anlık çizim |
| 3 | Periyot | `setInterval(updateClock, 1000)` | 1000ms | Dakika güncellenir |
| 4 | Kayıt | `#intervals.push(id)` | — | Temizlik için saklanır |
| 5 | Kapanış | `destroy()` → `clearInterval` | — | Zamanlayıcı serbest |

### §4.2 Akış B — OAuth popup yoklama (oauth-manager)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | Popup açıldı | `setInterval(check, 500)` | 500ms | Her 500ms'de `popup.closed` kontrolü |
| 2 | Kapanma | `clearInterval` + `setTimeout(resolve, 1000)` | 1000ms | Sonuç gecikmeli çözülür |
| 3 | Zaman aşımı | `setTimeout(..., 60000)` | 60000ms | `popup.close()` + başarısız sonuç |
| 4 | Temizlik | her iki dal da `clearInterval` | — | Sızıntı yok |

### §4.3 Akış C — Resize debounce (device-loader)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | `resize` olayı | `onResize(state)` | — | Debounce başlar |
| 2 | Önceki iptal | `clearTimeout(resizeTimer)` | — | Eski zamanlayıcı yok edilir |
| 3 | Yeni planla | `resizeTimer = setTimeout(..., 300)` | 300ms | Sessizlik sonrası cihaz yeniden tespiti |
| 4 | Yürütme | `detect(w,h)` + cookie güncelle | — | Yeni cihaz uygulanır |

### §4.4 Akış D — Sunucu oturum denetimi (SessionLifecycle, istek-başına)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | Her istek | `start()` | — | Denetimler çalışır |
| 2 | Rotation | `$now - $lastRotation >= 1800` | 30dk | `session_regenerate_id(true)` |
| 3 | Idle | `time() - $lastActive >= 3600` | 1sa | Oturum reddedilir |
| 4 | Max life | `time() - $createdAt >= 1800` | 30dk | Oturum sonlanır |
| 5 | Damga | `last_activity = $now` | — | Sayaç yenilenir |

### §4.5 Akış E — Cache TTL (MemoryAdapter)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | `set($key,$v,$ttl)` | `'expires' => time()+$ttl` | $ttl | Bitiş damgası yazılır |
| 2 | `get($key)` | `$entry['expires'] < time()` | — | Süresi dolduysa `unset` |
| 3 | Süresiz | `$ttl=0` → `expires=0` | ∞ | Süre denetimi atlanır |

### §4.6 Akış F — Abort-sinyalli gecikme (signal-utils / FetchWrapper)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | `createTimeout(ms)` | `setTimeout(abort, ms)` | $ms | Sinyal zamanlayıcısı |
| 2 | Abort öncesi | `clearTimeout(id)` | — | Temiz iptal |
| 3 | `#delayWithAbort` | Promise + `signal.addEventListener('abort')` | $ms | İptal edilebilir bekleme |

---

## §5 — Kapsam dışı (komşu düğümlere devir)

| # | Devredilen | Hedef düğüm | Gerekçe |
|---|---|---|---|
| 1 | EventDispatcher yayımı | K011/event-akislari | Ayrı çocuk |
| 2 | Cache TTL mekanizması | K011/caching | Ayrı çocuk |
| 3 | Rate limit penceresi | K009-security | Güvenlik katmanı |
| 4 | Cron/queue/worker kurulumu | K012-services (olası) | §6-1 |
| 5 | CSS animasyon tasarımı | K11-ux / ui-design | Tasarım kararı |
| 6 | `radio_schedules` iş mantığı | K008-domain | Domain kararı |
| 7 | Health-check altyapısı | K016-observability | Gözlemlenebilirlik |
| 8 | Test altyapısı sahipliği | K18-testing | Test katmanı |

---

## §6 — Açık sorular

| # | Soru | Durum | Etki |
|---|---|---|---|
| 1 | Cron/queue/worker altyapısı kurulacak mı, hangi düğümde? | ⚠️ VERIFICATION REQUIRED | Kapsam genişlemesi |
| 2 | Web Worker zamanlaması (MDN kaynağı havuzda) kullanılacak mı? | ⚠️ VERIFICATION REQUIRED | JS mimarisi |
| 3 | `--duration-fast` çakışması (100ms vs 0ms) nasıl çözülecek? | ⚠️ VERIFICATION REQUIRED | CSS tutarlılığı |
| 4 | SQL enum CRON/QUEUE/WORKER değerlerini kim dolduracak? | PLANNED | K012 |
| 5 | `radio_schedules` verisi ne zaman üretilecek (radyo özelliği)? | ⚠️ VERIFICATION REQUIRED | K008/K015 |
| 6 | Toast iki varyant (composites/interactive) birleştirilecek mi? | ⚠️ VERIFICATION REQUIRED | JS refactor |

**Spec §8 satırı (bağlayıcı kayıt):**
Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır;
belge seviyesinde kayıt.

---

## §7 — Zamanlayıcı çağrısı envanteri (satır düzeyi)

### §7.1 Tüm JS isabetleri (20/20 — `Select-String`, 2026-10-15)

| # | Dosya | Satır | Çağrı | Süre | Desen |
|---|---|---|---|---|---|
| 1 | `js/device-loader.js` | 215 | `setTimeout` | 300 | debounce (resizeTimer) |
| 2 | `js/device-loader.js` | 259 | `setTimeout` | — | gecikmeli yeniden deneme |
| 3 | `js/device-loader.js` | 263 | `setTimeout` | — | iç içe gecikme |
| 4 | `js/oauth-manager.js` | 87 | `setInterval` | 500 | popup yoklama |
| 5 | `js/oauth-manager.js` | 92 | `setTimeout` | 1000 | sonuç gecikmesi |
| 6 | `js/oauth-manager.js` | 100 | `setTimeout` | 60000 | timeout güvenlik ağı |
| 7 | `js/router/FetchWrapper.js` | 58 | `setTimeout` | param | abort-sinyalli bekleme |
| 8 | `js/router/config/signal-utils.js` | 17 | `setTimeout` | param | `createTimeout` fabrikası |
| 9 | `js/router/RouterEventManager.js` | 46 | `setTimeout` | 100 | hover-prefetch debounce |
| 10 | `js/components/base/ComponentAccessibility.js` | 228 | `setTimeout` | `clearMs` | aria-live temizleme |
| 11 | `js/auth/auth-gender-bg.js` | 229 | `setTimeout` | — | fon geçiş gecikmesi |
| 12 | `js/auth/auth-gender-bg.js` | 335 | `setTimeout` | 300 | debounce (resizeTimer) |
| 13 | `js/components/interactive/ToastComponent.js` | 152 | `setTimeout` | `duration` | toast otomatik kapanış |
| 14 | `js/components/interactive/ToastComponent.js` | 191 | `setTimeout` | — | giriş animasyonu |
| 15 | `js/core/footer.init.js` | 23 | `setTimeout` | 300 | progress fill yeniden deneme |
| 16 | `js/features/TouchManager.js` | 40 | `setTimeout` | sabit | long-press |
| 17 | `js/components/composites/ToastComponent.js` | 162 | `setTimeout` | `duration` | toast otomatik kaldırma |
| 18 | `js/features/WidgetManager.js` | 59 | `setInterval` | 1000 | saat widget'ı |
| 19 | `js/features/welcome-modal.js` | 141 | `setTimeout` | 200 | embedded yarış düzeltmesi |
| 20 | `js/components/composites/WidgetAreaComponent.js` | 82 | `setInterval` | 30000 | saat alanı 30sn |

**Dağılım:** `setTimeout` 17 · `setInterval` 3 · debounce 4 · periyodik 3 · tek-atım 10.

### §7.2 Temizlik (cleanup) denetimi — her periyodik zamanlayıcıda zorunlu

| # | Dosya | Temizlik kodu | Durum |
|---|---|---|---|
| 1 | `WidgetManager.js:90–94` | `destroy() { #intervals.forEach(clearInterval) }` | ✅ |
| 2 | `WidgetAreaComponent.js:85–91` | `destroy() { clearInterval(#clockIntervalId) }` | ✅ |
| 3 | `oauth-manager.js:90,101` | `clearInterval(checkInterval)` (her iki dal) | ✅ |
| 4 | `TouchManager.js:48–59` | `clearTimeout` (move + end, iki yerde) | ✅ |
| 5 | `RouterEventManager.js:45` | `clearTimeout(#hoverTimer)` (öncekini iptal) | ✅ |
| 6 | `device-loader.js:214` | `clearTimeout(resizeTimer)` (öncekini iptal) | ✅ |
| 7 | `auth-gender-bg.js:334` | `clearTimeout(resizeTimer)` | ✅ |
| 8 | `ComponentAccessibility.js:231` | `signal.addEventListener('abort', clearTimeout)` | ✅ |

**Sonuç:** 8/8 periyodik/debounce zamanlayıcıda temizlik kanıtı **VAR** — sızıntı riski düşük.

---

## §8 — Olumsuz senaryolar ve riskler

| # | Senaryo | Sonuç | Mevcut koruma | Durum |
|---|---|---|---|---|
| 1 | `clearInterval` unutulursa | Bellek/DOM sızıntısı, arka planda çalışmaya devam | §7.2 — 8/8 temizlikli | ✅ korunaklı |
| 2 | Background tab throttling | `setInterval` 1/sn'den 1/dk'ya düşer (Chrome intensive) | Saat widget'ı tolere eder (dakika hassasiyeti) | ✅ tolere |
| 3 | PHP'de `sleep()` kullanımı | İstek bloklanır, worker kilitlenir | `sleep()` 0 isabet — kullanılmıyor | ✅ yok |
| 4 | `time()` yerine `microtime` karıştırılması | Saniye/ms birim hatası | `UuidV7` yalnız ms; `time()` yalnız saniye — ayrım net | ✅ ayrık |
| 5 | `--duration-fast` çakışması | 100ms vs 0ms — bazı geçişler anında kaybolur | Import zinciri sırası belirleyici → §6-3 | ⚠️ açık |
| 6 | SQL enum CRON_* değeri yazılırsa | Yarım kalmış cron özelliği görünür | Üreten kod yok → boşluk kayıtlı | 🟡 PLANNED |
| 7 | Toast süresi `0` girilirse | Bildirim hiç kaybolmaz | `Number.isFinite && >0` denetimi (`ToastComponent.js:146`) | ✅ korunaklı |
| 8 | OAuth popup hiç kapanmazsa | Promise hiç çözülmez | 60sn timeout + `popup.close()` (`oauth-manager.js:100–104`) | ✅ korunaklı |
| 9 | Abort sonrası timer tetiklenirse | Yabancı state'e yazma | `signal.aborted` erken denetimi (`FetchWrapper.js:57`) | ✅ korunaklı |
| 10 | Rotation `session_regenerate_id` çökmesi | Oturum kaybı | `true` parametresi (eskiyi siler) — kasıtlı güvenlik tercihi | ⚠️ kabul |

---

## §9 — Kapsam kabul kriterleri

| # | Kriter | Eşik | Ölçüm (2026-10-15) | Durum |
|---|---|---|---|---|
| 1 | JS zamanlayıcı envanteri tam | 20/20 isabet sınıflandırılmış | §7.1 — 20/20 | ✅ |
| 2 | Periyodik temizlik kanıtı | 8/8 | §7.2 — 8/8 | ✅ |
| 3 | PHP cron yokluğu belgelendi | 3 arama yolu | §2 — PHP/JS/SQL 3 yönlü | ✅ |
| 4 | URL havuzu 5/5 | HTTP 200 | web-arastirma §2 | ✅ |
| 5 | Envante kesişimi | HIT=0 | web-arastirma §3 | ✅ |
| 6 | Süre tokenları envantere alındı | 27 token | §3.3 | ✅ |
| 7 | Açık sorular etiketli | 6 madde | §6 | ✅ |
| 8 | Spec §8 satırı | 5 içerik dosyası | kapsam §6 · dogrulama §7 · web §6 · ornekler §10 · bilesenler §8 | ✅ |

---

## §10 — Derin akış tabloları (devam)

### §10.1 Akış G — Toast otomatik kapanış (composites ToastComponent)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | `show(message, opts)` | `duration` hesabı (error 6000 / normal 4000) | — | Süre seçimi |
| 2 | Geçerlilik | `Number.isFinite(opts.duration) && >0` | — | Kötü girdi reddedilir |
| 3 | Planlama | `timers.set(id, setTimeout(#remove, duration))` | 4000/6000 | Zamanlayıcı Map'e kaydedilir |
| 4 | Yürütme | `#remove(id)` | — | Bildirim DOM'dan kaldırılır |
| 5 | Manuel kapanış | `clearTimeout(timers.get(id))` | — | Erken iptal |

### §10.2 Akış H — Aria-live temizleme (ComponentAccessibility)

| Adım | Tetik | Kod | Süre | Sonuç |
|---|---|---|---|---|
| 1 | Duyuru | `announce(container, message, opts)` | — | `aria-live` bölgesi yazılır |
| 2 | Planlama | `clearMs > 0` ise `setTimeout(live.textContent='')` | `clearMs` | Metin temizlenir |
| 3 | Abort | `signal.addEventListener('abort', clearTimeout)` | — | Erken iptal koruması |
| 4 | Politika | `options.polite===false` → `assertive` | — | Öncelik seçimi |

### §10.3 Akış I — UuidV7 milisaniye damgası (PHP)

| Adım | Tetik | Kod | Birim | Sonuç |
|---|---|---|---|---|
| 1 | `generateBinary()` | `microtime(true)*1000` | ms | 48-bit damga |
| 2 | Paketleme | `pack('N', $ms>>16)` + `pack('n', $ms&0xFFFF)` | — | 6 byte |
| 3 | Versiyon | `$clockSeq[0] \|= 0x70` | — | v7 |
| 4 | Varyant | `$clockSeq[1] \|= 0x80` | — | variant 1 |

**Neden `time()` değil:** UUID v7 **milisaniye** ister; `time()` saniye döndürür → aynı
saniyede üretilen ID'ler sıralanamaz. `microtime(true)*1000` tek doğru seçimdir.

### §10.4 Akış J — AIWorkflow yürütme süresi ölçümü

| Adım | Tetik | Kod | Sonuç |
|---|---|---|---|
| 1 | Akış başı | `$startTime = microtime(true)` | Başlangıç anı |
| 2 | İş | Adım yürütme | — |
| 3 | Akış sonu | `$executionTime = microtime(true) - $startTime` | Süre (saniye, ondalık) |
| 4 | Tekrar | 5 akış × 2 (`ToolCalling` 2 kullanım daha) | Toplam 12 ölçüm noktası |

---

## §11 — Çapraz referans matrisi (bu düğüm ↔ komşular)

| # | Komşu düğüm | İlişki | Kanıt |
|---|---|---|---|
| 1 | K011/event-akislari | Olay yayımı zamanlama **taşımez** | `Events/` 14 dosya ayrı çocuk |
| 2 | K011/caching | TTL mekanizması orada, **kullanımı** burada | `MemoryAdapter` §3.2-4 |
| 3 | K011/server-runtime | PHP istek yaşam döngüsü | `SessionLifecycle` istek-başına |
| 4 | K011/application-services | Servis çağrısı zamanlaması | ⚠️ sınır → §6-1 |
| 5 | K009-security | Rotation/nonce güvenlik kararı | `SessionConfig` sabitleri |
| 6 | K008-domain | `radio_schedules` iş mantığı | `coremusic_musics.sql:442` |
| 7 | K014-media | Radyo yayın akışı | `radio_schedules` tüketicisi |
| 8 | K016-observability | `HEALTH_CHECK` enum değeri | `coremusic_logs.sql:325` |
| 9 | K18-testing | Zamanlayıcı testleri | 45 PHP test / 9 JS spec |
| 10 | K11-ux / ui-design | Animasyon tasarım kararı | token **değerleri** burada |
| 11 | K012-services | Cron/queue/worker altyapısı | ❌ yok → §6-1 |

---

## §12 — Kapsam içi/dışı karar akışı (karar ağacı)

```text
Görev geldi
  ├─ setTimeout/setInterval/RAF/debounce mu?        → KAPSAM İÇİ (§3.1)
  ├─ time()/microtime/DateTime saniye hesabı mı?     → KAPSAM İÇİ (§3.2)
  ├─ --duration-*/--ease-*/--transition-* token mı? → KAPSAM İÇİ (§3.3)
  ├─ SQL schedule/enum yapısı mı?                    → KAPSAM İÇİ (§3.4, yer tutucu)
  ├─ EventDispatcher yayımı mı?                      → K011/event-akislari
  ├─ Cache TTL mekanizması mı?                       → K011/caching
  ├─ Rate limit penceresi mi?                         → K009-security
  ├─ Cron/queue/worker kurulumu mu?                  → §6-1 (PLANNED, sahibi yok)
  ├─ Radio yayın kararı mı?                           → K008-domain
  └─ Animasyon görsel tasarımı mı?                   → K11-ux / ui-design
```

**Kural:** Ağacın hiçbir dalı "kod yaz" ile bitmez — bu düğüm **kanıt ve taşıma** modülüdür;
kod hedef düğüme devredilir.

**Devir mesajı standardı** (devir yapılırken taşınan alanlar):

| Alan | İçerik |
|---|---|
| Hedef | Komşu düğüm adı (örn. K012-services) |
| Konu | Ne devrediliyor (örn. cron altyapısı) |
| Kanıt | Dosya + satır (örn. `coremusic_logs.sql:325`) |
| Durum | PLANNED / IMPLEMENTED |
| Yasak | Bu düğümde kod üretilmeyeceği |

---

## §13 — Öğrenilenler (kapsam turu çıktıları)

| # | Öğrenim | Dayanak |
|---|---|---|
| 1 | Zamanlama depoda **3 hızda** dağılıyor: ms (JS) · sn (PHP) · cron (yok) | §1 tablo |
| 2 | PHP **bloklama yok** — `sleep()` 0 isabet, tembel desen baskın | §3.2 |
| 3 | Süre değerleri **SSOT**'ta: JS sabitleri kodda, PHP `SessionConfig`'te, CSS token'larda | §3.2-1 · §3.3 |
| 4 | Periyodik zamanlayıcıların **8/8'inde temizlik** var — sızıntı disiplini yüksek | §7.2 |
| 5 | SQL şeması cron/queue/worker'a **hazır** ama kod yok → en olgun boşluk | §3.4 · §6-4 |
| 6 | `--duration-fast` **çakışması** tek aktif CSS belirsizliği | §3.3 · §6-3 |
| 7 | En zengin zamanlayıcı dosyası `oauth-manager.js` (3 çağrı, 3 ölçek: 0.5s/1s/60s) | §7.1-4..6 |
| 8 | En küçük zamanlayıcı fabrikası `signal-utils.js` (19 satır, tamamı sinyal) | §7.1-8 |

---

## §A — Ekler

### §A.1 Ölçüm yöntemi (tekrarlanabilir)

| Sayım | Komut | Sonuç (2026-10-15) |
|---|---|---|
| JS dosya | `(Get-ChildItem js -Recurse -Filter *.js).Count` | 95 (7 spec) |
| JS zamanlayıcı | `Select-String 'setTimeout\|setInterval'` | 20 / 14 dosya |
| PHP dosya | `Get-ChildItem shared/src -Recurse -File \| Measure` | 203 / 20 dizin |
| PHP cron | `Select-String 'cron\|crontab' *.php` | **0** |
| PHP sleep | `Select-String 'usleep\|sleep(' *.php` | **0** |
| SQL dosya | `Get-ChildItem .ai/.sql/mysql -Filter *.sql` | 20 |
| CSS token | `Get-ChildItem Css/01_Abstracts -Filter *.css` | 19 |
| Süre satırı | `[System.IO.File]::ReadAllLines($y).Length` | bu dosya ≥500 |

### §A.2 Terimler

| Terim | Tanım |
|---|---|
| **Tembel (lazy) zamanlama** | Zamanlama kararının iş anında verilmesi (periyodik süreç yok) |
| **Debounce** | Yeni tetik gelene kadar öncekini iptal eden gecikme deseni |
| **RAF** | `requestAnimationFrame` — bir sonraki çizim karesi (~16ms); zamanlayıcı değil |
| **Yer tutucu (placeholder)** | Şemada var ama üreten kodun olmadığı değer (enum CRON_*) |
| **TTL** | Time-To-Live — kaydın geçerlilik süresi (saniye) |
| **Rotation** | Periyodik oturum kimliği yenileme (güvenlik) |
| **Throttling** | Tarayıcının background tab'da zamanlayıcıyı yavaşlatması (Chrome 1/dk) |
| **Abort sinyali** | `AbortController` ile iptal edilebilir bekleme deseni |
| **Long-press** | Basılı tutma süresi dolunca tetiklenen hareket |
| **Polling** | Periyodik durum yoklaması (OAuth popup `closed` kontrolü) |

### §A.3 Dosya → bölüm haritası

| Dosya | Bölümler |
|---|---|
| `index.md` | §1–§4 (katalog, kapı, ölçüm, açık soru özeti) |
| `zamanlamali-gorevler-kapsam.md` | §1–§13 + §A (bu dosya) |
| `zamanlamali-gorevler-dogrulama.md` | §1–§8 + §A (kapılar, KPI, olumsuz senaryo) |
| `zamanlamali-gorevler-web-arastirma.md` | §1–§6 + §A (havuz, kesişim, matris) |
| `zamanlamali-gorevler-ornekler.md` | §1–§10 (PHP/JS/SQL/CSS + YAPMA↔YAP) |
| `zamanlamali-gorevler-bilesenler.md` | §1–§8 (envanter, iz düşümü, bağımlılık) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
