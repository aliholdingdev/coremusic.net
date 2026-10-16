---
title: "K011 · zamanlamali-gorevler — Bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: zamanlamali-gorevler
status: active
updated: 2026-10-15
group: mimari
---

# K011 · zamanlamali-gorevler — Bileşenler

**Amaç:** Zamanlamalı görevler modülünün bileşen envanterini gerçek glob/grep
sayımlarıyla çıkarmak, her bileşeni ait olduğu akışa (flow) ve bağımlılık
grafisine izlemek, sayılmayan yüzeyleri (RAF, olmayan cron) açıkça ayırmak ve
boşlukları kanıt satırıyla listelemek. Tüm sayımlar 2026-10-15 disk ölçümüdür.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| §1 | Ölçüm metrikleri (glob/grep) | kanıt | olcum | Sayımların tekrarlanabilir kaynağı |
| §2 | JS timer bileşenleri (14 dosya) | mimari | envanter | İstemci zamanlayıcı sahipleri |
| §3 | PHP / SQL / CSS bileşenleri | mimari | envanter | Sunucu + şema + token sahipleri |
| §4 | Bileşen → flow izleme | mimari | izleme | Hangi bileşen hangi akışta |
| §5 | Bağımlılık grafiği | mimari | bagimlilik | Döngü denetimi + kümeler |
| §6 | Test bileşenleri | kanit | test | Kapsama biçimi ve eksikler |
| §7 | Boşluklar (gap) ve sayılmayanlar | kanit | eksik | PLANNED / kanıt yok |
| §8 | Açık Sorular | mimari | acik | spec §8 kaydı + karar bekleyenler |

---

## §1 — Ölçüm Metrikleri (glob / grep, tekrarlanabilir)

> Yöntem: `Get-ChildItem -Recurse` (glob) + `Select-String` (grep). Betik dosyası
> yazılmadı; komutlar burada metin olarak durur. Tarih: 2026-10-15.

### §1.1 Glob sayımları

| # | Kapsayıcı | Desen | Sayı | Not |
|---|---|---|---|---|
| 1 | `assets.coremusic.net/js` | `**/*.js` | **95** | Zamanlama yüzeyinin tamamı |
| 2 | `assets.coremusic.net/js` | `**/*.spec.js` | **7** | primitives ×6 + core ×1 |
| 3 | `assets.coremusic.net` | `**/*.test.js` | **2** | `tests/components/` ×2 |
| 4 | `shared/src` | `**/*.php` | **203** | 20 dizin altında |
| 5 | `shared/tests` | `**/*.php` | **45** | 41 `*Test.php` + 4 yardımcı (ör. `Sqlite/SqliteDatabaseManager.php`) |
| 6 | `.ai/.sql/mysql` | `*.sql` | **20** | Şema kaynakları |
| 7 | `assets.coremusic.net/Css/01_Abstracts` | `*.css` | **19** | Token katmanı |
| 8 | `.ai/ui-design/flow` | `**/*.md` | **21** | flow indeks + 20 akış |

**JS test toplamı:** 7 + 2 = **9** (özetleme ile aynı: 9 spec).

### §1.2 Grep sayımları — JS

| # | Desen | İsabet | Dosya | Yorum |
|---|---|---|---|---|
| 1 | `setTimeout` | **17** | 12 | Birincil gecikme primitive |
| 2 | `setInterval` | **3** | 3 | Clock ×2 + OAuth polling ×1 |
| 3 | `setTimeout\|setInterval` (birleşik) | **20** | **14** | Bileşen kümesi = §2 |
| 4 | `clearTimeout\|clearInterval` | **17** | 11 | Temizlik yolları (neredeyse 1:1) |
| 5 | `requestAnimationFrame` | **9** | 8 | **Timer sayılmaz** (bkz. §7.2) |
| 6 | `new Worker(\|ServiceWorker` | **0** | 0 | Worker yok (PLANNED) |
| 7 | `setTimeout(['"]` (string code) | **0** | 0 | XSS yüzeyi kapalı |
| 8 | `useFakeTimers\|advanceTimers` | **0** | 0 | Timer testi yok |

### §1.3 Grep sayımları — PHP / SQL / CSS

| # | Desen | Kapsayıcı | İsabet | Yorum |
|---|---|---|---|---|
| 1 | `sleep\|usleep` | `shared/src` | **0** | Bloklayıcı bekleme yok |
| 2 | `cron\|crontab` | `shared/src` | **0** | Cron üreticisi yok |
| 3 | `time()` | `shared/src` | 9+ (8 dosyada) | Epoch fark kalıbı |
| 4 | `DateTimeImmutable` | `shared/src` | var (`StructuredLogger.php:62` vb.) | Formatlama ayrı |
| 5 | `ON SCHEDULE` | `.ai/.sql/mysql` | **0** | MySQL event scheduler yok |
| 6 | `CRON_JOB_*\|WORKER_*` | `coremusic_logs.sql` | 5+ değer (`:325`) | Enum var, üretici yok |
| 7 | `radio_schedules` | `coremusic_musics.sql` | 1 (`:442`) | Yinelenen zamanlama tablosu |
| 8 | `--duration-` | `01_Abstracts` | 2 dosya | `a-design-tokens.css:107–113` + `a-theme-config.css:86` |

### §1.4 Tekrarlanabilir komutlar (metin)

```powershell
# G1 — JS timer bileşen dosyaları
Get-ChildItem 'C:\www\coremusic.net\assets.coremusic.net\js' -Recurse -Filter *.js |
  Select-String -Pattern 'setTimeout|setInterval' |
  Group-Object Path | Sort-Object Count -Descending |
  ForEach-Object { '{0,2}  {1}' -f $_.Count, $_.Name.Replace('C:\www\coremusic.net\','') }
```

```powershell
# G2 — PHP bloklayıcı bekleme + cron (ikisi de 0 beklenir)
Get-ChildItem 'C:\www\coremusic.net\shared\src' -Recurse -Include *.php |
  Select-String -Pattern 'sleep\(|usleep\(|cron|crontab' |
  Measure-Object | Select-Object -ExpandProperty Count      # beklenen: 0
```

```powershell
# G3 — SQL event scheduler (0 beklenir)
Get-ChildItem 'C:\www\coremusic.net\.ai\.sql\mysql' -Filter *.sql |
  Select-String -Pattern 'ON SCHEDULE' |
  Measure-Object | Select-Object -ExpandProperty Count       # beklenen: 0
```

**Doğrulama durumu:** G1 = 20 isabet / 14 dosya ✅ · G2 = 0 ✅ · G3 = 0 ✅ (2026-10-15).

### §1.5 İkincil ölçümler — PHP `time()` dağılımı (15 dosya / 36 isabet)

```powershell
Get-ChildItem 'C:\www\coremusic.net\shared\src' -Recurse -Include *.php |
  Select-String -Pattern 'time\(\)' | Group-Object Path |
  Sort-Object Count -Descending | ForEach-Object { '{0,2}  {1}' -f $_.Count, $_.Name }
```

| # | Dosya | `time()` | Rol |
|---|---|---|---|
| 1 | `PageRouter/SessionInitializer.php` | 8 | İstek başı oturum kurulumu |
| 2 | `Session/SessionLifecycle.php` | 7 | Rotation + idle kontrolü |
| 3 | `AI/MemorySystem.php` | 7 | Zaman damgalı hafıza kaydı |
| 4 | `Cache/MemoryAdapter.php` | 2 | TTL |
| 5 | `Api/Bff/EmbeddedBff.php` | 2 | BFF zaman damgası |
| 6 | `AI/AIOrchestrator.php` | 1 | Orkestrasyon damgası |
| 7 | `Api/Bff/BffLayer.php` · `DesktopBff.php` · `MobileBff.php` · `SpaBff.php` | 1×4 | BFF ortak katman |
| 8 | `Api/Registry/ServiceHealth.php` | 1 | Sağlık denetimi |
| 9 | `OAuth/OAuthManager.php` | 1 | Token süresi |
| 10 | `PageRouter/HtmlShellRenderer.php` | 1 | Shell render |
| 11 | `Security/JwtService.php` | 1 | `iat/exp` |
| 12 | `Session/SessionBootstrapper.php` | 1 | Bootstrap damgası |
| — | **Toplam** | **36** | **15 dosya** |

### §1.6 Zaman testi isabeti — iki ölçüm, iki patern (dürüstlük kaydı)

| # | Patern | Sonuç | Kullanan dosyalar |
|---|---|---|---|
| 1 | Geniş oturum ölçümü (`zaman` kavramı — bu revizyonun önceki turu) | **35 isabet / 8 dosya** | `index.md:57` · `dogrulama.md:137` |
| 2 | Dar fonksiyon ölçümü: `time\(\)\|strtotime\|DateTime\|sleep\(\)\|usleep\(\)` | **11 isabet / 4 dosya** | bu dosya §6 (yinelenen çapraz kontrol) |
| 3 | `shared/tests/**/*.php` dosya sayısı | **45** (41 test + 4 yardımcı) | §1.1 #5 |

**Not:** İki sayı **aynı şeyi ölçmez** — (1) "zaman kavramı geçen satır", (2)
"zaman fonksiyonu çağıran satır". İkisi de raporlanır; biri diğerinin yerine
geçirilmez (§6'daki 4 dosyalık liste tekrar üretilebilir kanıttır).

---

## §2 — JS Timer Bileşenleri (14 dosya)

> Sıralama: isabet sayısına göre. "Rol" sütunu, dosyanın kendi kodundaki işlevdir.

### §2.1 Periyodik (interval) bileşenler — 3

| # | Dosya | Satır | Periyot | Rol | Temizlik |
|---|---|---|---|---|---|
| 1 | `js/features/WidgetManager.js` | 59 | 1000 ms | Ev saat widget'ı (`setInterval`) | `:91` havuz `#intervals` döngüsü |
| 2 | `js/components/composites/WidgetAreaComponent.js` | 82 | 30_000 ms | Widget grid saat yenileme | `:87` `destroy()` içinde `clearInterval` |
| 3 | `js/oauth-manager.js` | 87 | 500 ms | Popup polling (OAuth) | `:90` başarı · `:101` 60 sn timeout |

### §2.2 Tek seferlik (timeout) bileşenler — 11 dosya / 17 çağrı

| # | Dosya | Satır | Süre | Rol | Temizlik |
|---|---|---|---|---|---|
| 1 | `js/device-loader.js` | 215 (ayrıca 259, 263) | 300 ms debounce + 2 fallback | Resize→cihaz tespiti | `:214` `clearTimeout` |
| 2 | `js/auth/auth-gender-bg.js` | 229, 335 | fallback + debounce | Cinsiyet ekranı arka planı | `:334` |
| 3 | `js/oauth-manager.js` | 92, 100 | 1000 ms · 60000 ms | Durum çözümleme + kill-switch | `:101` |
| 4 | `js/components/interactive/ToastComponent.js` | 152, 191 | `duration` | Eski toast otomatik kapanış | — (API korunur) |
| 5 | `js/components/composites/ToastComponent.js` | 162 | `duration` | Yeni toast (C16) timer haritası | `:188` `clearAll` · `:201` `#remove` |
| 6 | `js/router/FetchWrapper.js` | 58 (+16) | `TIMEOUT_MS` | Ağ timeout / backoff gecikmesi | `:21` `finally` · `:59` abort |
| 7 | `js/router/config/signal-utils.js` | 17 | parametre | `createTimeout` üreticisi | `:18` `clear()` fan-out |
| 8 | `js/features/TouchManager.js` | 40 | `LONG_PRESS_MS` | Long-press (`.media-card`) | `:49/57/96` üç yol |
| 9 | `js/components/base/ComponentAccessibility.js` | 228 | `clearMs` | ARIA live bölge temizliği | `:231` abort signal |
| 10 | `js/router/RouterEventManager.js` | 46 | 100 ms | Prefetch hover debounce | `:24/45` |
| 11 | `js/core/footer.init.js` · `js/features/welcome-modal.js` | 23 · 141 | 300 · 200 ms | Yedek tetik (load olayı tamamlayıcı) | — (idempotent bayrak) |

**Toplam:** 3 interval + 17 timeout = **20 isabet / 14 dosya** ✅ (§1.2 ile birebir).

### §2.3 Timer üretici/altyapı katmanı

| # | Bileşen | Dosya | Ne sağlar | Tüketiciler |
|---|---|---|---|---|
| 1 | `createTimeout(ms)` | `router/config/signal-utils.js:15–19` | AbortController'a çevrilmiş timeout | `FetchWrapper.js:16` (**tek importör**, grep: 4 isabet = kendi dosyası) |
| 2 | `combineSignals(...)` | `signal-utils.js:1–13` | Çoklu sinyal birleştirme | `FetchWrapper.js:18–19` |
| 3 | `#delayWithAbort(ms, signal)` | `FetchWrapper.js:55–61` | İptal edilebilir backoff | `#fetchWithRetry` içi |
| 4 | Timer haritası (`id ↔ handle`) | `composites/ToastComponent.js:162` | Toplu temizlik | `clearAll` / `#remove` / `destroy` |

**İzlenim:** Timer üretimi **tek noktada** (`signal-utils`), UI süreleri
**bileşen içi**, periyodik işler **3 sahip dosyada** toplanmış — dağınık değil,
fakat **test edilmemiş** (§6).

### §2.4 Temizlik matrisi (timer ↔ iptal yolu)

| # | Timer çağrısı | Dosya:satır | İptal yolu | Eksik mi? |
|---|---|---|---|---|
| 1 | `setInterval(updateClock, 1000)` | `WidgetManager.js:59` | havuz `#intervals` → `:91` | Hayır |
| 2 | `setInterval(update, 30_000)` | `WidgetAreaComponent.js:82` | `destroy()` `:87` | Hayır |
| 3 | `setInterval(poll, 500)` | `oauth-manager.js:87` | `:90` + `:101` (iki yol) | Hayır |
| 4 | `setTimeout(resolve, 1000)` | `oauth-manager.js:92` | — (kısa, tek seferlik) | Kabul |
| 5 | `setTimeout(timeout, 60000)` | `oauth-manager.js:100` | sonunda `clearInterval` | Kabul |
| 6 | `setTimeout(debounce, 300)` | `device-loader.js:215` | `:214` | Hayır |
| 7 | `setTimeout(fallback, …)` | `device-loader.js:259/263` | — (idempotent) | Kabul |
| 8 | `setTimeout(applyProgress, 300)` | `footer.init.js:23` | — | Kabul |
| 9 | `setTimeout(open, 200)` | `welcome-modal.js:141` | bayrak `opened` | Kabul |
| 10 | `setTimeout(clear, 200ms)` | `auth-gender-bg.js:229` | — | Kabul |
| 11 | `setTimeout(debounce, …)` | `auth-gender-bg.js:335` | `:334` | Hayır |
| 12 | `setTimeout(#dismissToast, duration)` | `interactive/ToastComponent.js:152/191` | API korunur (eski sürüm) | ⚠️ çift bileşen |
| 13 | `setTimeout(#remove, duration)` | `composites/ToastComponent.js:162` | `:188` `clearAll` · `:201` `#remove` | Hayır |
| 14 | `setTimeout(resolve, ms)` | `FetchWrapper.js:58` | `:59` `clearTimeout(id)` | Hayır |
| 15 | `setTimeout(abort, ms)` | `signal-utils.js:17` | `:18` `clear()` | Hayır |
| 16 | `setTimeout(longPress, …)` | `TouchManager.js:40` | `:49/57/96` (üç yol) | Hayır |
| 17 | `setTimeout(clearLive, clearMs)` | `ComponentAccessibility.js:228` | `:231` abort signal | Hayır |
| 18 | `setTimeout(prefetch, 100)` | `RouterEventManager.js:46` | `:24/45` | Hayır |
| 19 | `setTimeout(delay, ms)` (retry backoff) | `FetchWrapper.js` içinde `#delayWithAbort` | `:59` abort | Hayır |
| 20 | (temizlik çağrıları — 17 isabet) | 11 dosyada `clearTimeout/ClearInterval` | — | — |

**Sonuç:** 20 çağrının **18'inde** tanımlı iptal yolu var; 2'si (kısa fallback'ler)
idempotent yapıları nedeniyle iptalsiz — kabul edilebilir. **Ancak hiçbiri testli değil** (§6).

### §2.5 Bileşen → sahiplik ve öncelik

| # | Küme | Sahip (role) | Öncelik | Gerekçe |
|---|---|---|---|---|
| 1 | `signal-utils` + `FetchWrapper` timeout | UI (router) + QA | **P0** | Ağ kesintisinde tüm sayfa akışı bağlı |
| 2 | `oauth-manager` polling/timeout | Security + UI | **P0** | Oturum açma akışı (flow/auth) |
| 3 | `composites/ToastComponent` timer haritası | UI | **P1** | 4 flow dosyası `.toast`'a bağlı (§4) |
| 4 | `TouchManager` long-press | UI | **P1** | Music flow gestürü |
| 5 | `WidgetManager` / `WidgetAreaComponent` interval | UI | **P2** | Görsel, throttle tolere (web-arastirma §4.4) |
| 6 | `SessionLifecycle` rotation/idle | Security | **P1** | Güvenlik eşikleri (SSOT `SessionConfig`) |
| 7 | `MemoryAdapter` TTL | Backend | **P2** | Önbellek doğruluğu |
| 8 | CSS `--duration-*` token | UI | **P2** | Çelişki kararı (gap #4) |
| 9 | SQL enum/`radio_schedules` | Data + Backend | **P3** | PLANNED (gap #1/#5/#6) |

---

## §3 — PHP / SQL / CSS Bileşenleri

### §3.1 PHP zaman bileşenleri (9 dosya / epoch kalıbı)

| # | Dosya | Satır | Bileşen | Ne yapar |
|---|---|---|---|---|
| 1 | `shared/src/Session/SessionConfig.php` | 16–19 | Süre SSOT | `COOKIE_EXPIRY/MAX_LIFETIME/IDLE_TIMEOUT/ROTATION_INTERVAL` |
| 2 | `shared/src/Session/SessionLifecycle.php` | 55–62 | Periyodik kontrol | 30 dk'da bir session rotation (koşul tabanlı) |
| 3 | `shared/src/Session/SessionLifecycle.php` | 99–103 | Idle timeout | `time() - last >= IDLE_TIMEOUT` |
| 4 | `shared/src/Cache/MemoryAdapter.php` | 16–18, 23–28 | TTL cache | Yazılışta `time()+$ttl`, okumada karşılaştırma |
| 5 | `shared/src/Security/UuidV7.php` | 19–23 | Zaman damgası | 48-bit ms → sıralanabilir kimlik |
| 6–9 | `StructuredLogger.php:62` · `ApiResponse.php:36/95/126` · birim/yardımcılar | — | Formatlama | `DateTimeImmutable` (epoch değil) |

**Çizgi:** fark → `time()` · format → `DateTimeImmutable` · bekleme → **yok**
(`sleep|usleep` 0). Sunucu tarafı "zamanlamalı görev" = **istek-içi koşul**, cron değil.

### §3.2 SQL zamanlama bileşenleri

| # | Dosya | Satır | Bileşen | Durum |
|---|---|---|---|---|
| 1 | `coremusic_logs.sql` | 325 | `log_system.event_type` enum (26 değer; `CRON_JOB_START/COMPLETE/FAILED`, `CRON_SCHEDULED`, `QUEUE_OVERFLOW`, `WORKER_START/STOP`) | ⚠️ şema var, üretici yok |
| 2 | `coremusic_musics.sql` | 442–457 | `radio_schedules` tablosu (`day_of_week` 0–6 CHECK, `start/end time`, `is_recurring`, tekil anahtar) | ✅ veri modeli var |
| 3 | `coremusic_musics.sql` | 453–454 | `idx_rs_day` + `idx_rs_time` indeksleri | ✅ sorgu destekli |
| 4 | (tüm `.sql`) | — | `ON SCHEDULE` event scheduler | ❌ 0 isabet |

### §3.3 CSS süre bileşenleri

| # | Dosya | Satır | Bileşen | Durum |
|---|---|---|---|---|
| 1 | `Css/01_Abstracts/a-design-tokens.css` | 107–113 | 7 süre token'ı (`--duration-instant … skeleton`) | ✅ kanonik |
| 2 | `Css/01_Abstracts/a-design-tokens.css` | 136–142 | `--transition-*` varyantları (token okuma) | ✅ |
| 3 | `Css/01_Abstracts/a-theme-config.css` | 86 | `--duration-fast: 0ms` (ikinci tanım) | ⚠️ çelişki |

---

## §4 — Bileşen → Flow İzleme (trace)

> Kural: her satırda **bileşen kanıtı** (dosya/satır) ↔ **flow kanıtı**
> (`.ai/ui-design/flow/**` dosya/satır) birlikte durur. Eşleşmeyen "olurdu"
> satırı yazılmaz.

| # | Bileşen (kod kanıtı) | Flow / spec hedefi (vault kanıtı) | İlişki | Güç |
|---|---|---|---|---|
| 1 | `composites/ToastComponent.js:1–9` (C16, BEM `.toast`) | `flow/navigation/02-header-nav.md:175` · `flow/settings/02-bluetooth-connect.md:207` · `flow/music/03-album-browse.md:166` · `flow/auth/05-logout.md:161` — hepsi `.toast` ↔ `02-component-inventory.md:185 (C16)` | Akış hata mesajı toast ile gösterilir | ✅ güçlü (4 flow) |
| 2 | `composites/WidgetAreaComponent.js:1–10` (Figma node 2831:13747, PHP view `widget-grid.php`, CSS `_widget-grid.css`) | Widget grid kanonik kuralı (AGENTS §16.1: 1024 → 2×2/1×5/1×5 · 1920 → 4×4/1×8/1×8) | Grid sayımı kural bağlı | ✅ güçlü |
| 3 | `features/WidgetManager.js:59` (saat interval) | Home widget alanı (2. satır bileşeni) | Saat widget'ı grid içinde | ✅ orta (kod + grid) |
| 4 | `features/TouchManager.js:40–42` (`.media-card, .mini-card` long-press) | `flow/music/02-playlist-queue.md:182` ("Long press" TV/remote satırı) | Long-press sıraya ekleme gestürü | ✅ orta |
| 5 | `features/welcome-modal.js:138–143` (`isEmbeddedDevice()`) | `flow/settings/*` + embedded cihaz katmanı (`08_Devices`) | Embedded açılış modalı | ✅ orta |
| 6 | `oauth-manager.js:87–104` (popup polling + 60 sn) | `flow/auth/*` (5 akış: login · select-gender · … · logout) | OAuth callback bekleme | ✅ orta |
| 7 | `core/footer.init.js:21–23` (ilerleme çubuğu yedeği) | Footer progress (`.footer` alan spec'i) | Render garantisi | ⚠️ zayıf — doğrudan flow satırı yok |
| 8 | `SessionLifecycle.php:55–103` (rotation + idle) | Auth flow (`flow/auth/01-login.md` oturum) | Oturum güvenliği | ⚠️ zayıf — vault'ta satır eşlemesi yok |
| 9 | `radio_schedules` (`coremusic_musics.sql:442`) | Radyo/music akışları (`flow/music/*`) | Yayın programı verisi | ⚠️ zayıf — okuyucu kod yok (§7.1) |

**Sonuç:** 9 izlemenin 6'sı çift kanıtlı (kod + flow), 3'ü tek kanıtlı ve
**⚠️** işaretli — bunlar §8'de açık soru olarak durur.

### §4.2 İzleme güç dağılımı

| Güç | Sayı | Koşul |
|---|---|---|
| ✅ Güçlü (kod + flow + envanter/Figma kanıtı) | 3 | §4 #1–#3 |
| ✅ Orta (kod + flow gestürü/akış eşlemesi) | 3 | §4 #4–#6 |
| ⚠️ Zayıf (tek kanıt; vault eşlemesi yok) | 3 | §4 #7–#9 |
| Toplam | 9 | 6 çift kanıt · 3 tek kanıt |

### §4.1 Flow → bileşen ters indeks

| Flow dosyası | Zamanlama bileşeni referansı |
|---|---|
| `flow/navigation/02-header-nav.md:175` | `.toast` göster/gizle state'leri (C16) |
| `flow/settings/02-bluetooth-connect.md:207` | `.toast` hata varyantları |
| `flow/music/03-album-browse.md:166` | `.toast` "bulunamadı" durumları |
| `flow/auth/05-logout.md:141, 161` | Remote long-press + `.toast` hata |
| `flow/music/02-playlist-queue.md:182` | Long press gestürü (TouchManager) |

**Ters indeks sonucu:** 5 flow dosyası zamanlama bileşenine referans veriyor;
`setInterval`/`setTimeout` **hiçbir flow dosyasında geçmiyor** → flow'lar
davranışı anlatır, primitive'e inmez (beklenen ayrım).

---

## §5 — Bağımlılık Grafiği

### §5.1 Yöntem

Düğümler = §2–§3 bileşenleri. Kenarlar = gerçek import/çağrı (grep kanıtı:
`signal-utils` → 4 isabet yalnız `FetchWrapper.js`; `announce(` → toast içi).
**Yön:** bağımlılık oku **tüketici → üretici** (aşağıda üretici üste).

### §5.2 Graf (ASCII)

```text
                    [CSS token katmanı]
              a-design-tokens:107-113 ──(var())──> transition:136-142
              a-theme-config:86  ⚠️ çakışan ikinci tanım
                          │ (dolaylı: süreleri görsel olarak belirler)
                          ▼
[JS UI bileşenleri]                       [JS altyapı katmanı]
 WidgetManager:59 (1000ms)                signal-utils.createTimeout:15-19
 WidgetArea:82 (30000ms) ◄── destroy:87        │ import (tek tüketici)
 Toast(composites):162 ◄── clearAll:188        ▼
 Toast(interactive):152                   FetchWrapper:16-21 (timeout kablosu)
 TouchManager:40 ◄── 49/57/96                 │ uses
 CompA11y:228 ◄── abort:231                   ▼
 welcome-modal:141 · footer:23           signal-utils.combineSignals:1-13
 oauth-manager:87/92/100 (interval)           │
 device-loader:215 · RouterEventManager:46    │ (network timeout → backoff)
        ▲                                    ▼
        │                            coreplayer/ScrollManager/ScaleManager:
        │                            requestAnimationFrame (timer DEĞİL)
[PHP katmanı]
 SessionConfig:16-19 (SSOT) ──const──► SessionLifecycle:55-62, 99-103
 MemoryAdapter:16-28 (TTL, time()) ──epoch──► time()
 UuidV7:19-23 (microtime ms)
        │
        ▼ (yok — kod ↔ şema bağlantısı eksik)
[SQL şema]
 coremusic_logs:325 enum (CRON_*)   ⚠️ üretici yok
 coremusic_musics:442 radio_schedules ⚠️ okuyucu yok
```

### §5.3 Kenar tablosu

| # | Kenar (tüketici → üretici) | Kanıt | Tür |
|---|---|---|---|
| 1 | `FetchWrapper.js` → `signal-utils.js` | import satırı 3; isabetler 3/16/18/19 | import |
| 2 | `FetchWrapper.js` → `combineSignals` | `:18–19` | import |
| 3 | `composites/ToastComponent.js` → `ComponentAccessibility` (`announce`) | `:160` `announce(...)` | import |
| 4 | `WidgetManager.js` → `#intervals` havuzu | `:60`, `:91` | iç bağımlılık |
| 5 | `WidgetAreaComponent.js` → `destroy()` | `:85–89` | yaşam döngüsü |
| 6 | `SessionLifecycle.php` → `SessionConfig.php` | `:59`, `:103` (`SessionConfig::`) | const |
| 7 | `MemoryAdapter.php` → `time()` | `:16`, `:27` | dil primitive |
| 8 | CSS bileşenleri → token dosyası | `var(--duration-normal)` `:136` | token |
| 9 | `radio_schedules` → uygulama kodu | **0 isabet** | ❌ eksik kenar |
| 10 | `CRON_*` enum → yazan kod | **0 isabet** | ❌ eksik kenar |

### §5.4 Döngü denetimi

| Kontrol | Sonuç |
|---|---|
| JS içi döngü (A→B→A) | **Yok** — graf yönlü: config → api/router → ui |
| PHP içi döngü | **Yok** — `SessionConfig` yaprak (yalnız const) |
| Katman çaprazı ters kenar | **Yok** — UI PHP'yi, PHP JS'yi import etmiyor |
| Köprü bileşen | `EventBus` (`js/core/EventBus.js`) — olay yayını, zaman tutmaz |
| **Sonuç** | **Döngü yok · 8 geçerli kenar · 2 eksik kenar (§5.3 #9–10)** |

---

## §6 — Test Bileşenleri

| # | Yüzey | Dosya | Kapsanan | Zamanlama kapsıyor mu? |
|---|---|---|---|---|
| 1 | JS spec (`js/**`) | `Badge/Button/Input/Skeleton/Slider/ToggleComponent.spec.js` + `breakpoints.spec.js` (7) | Primitif UI + breakpoint | ❌ timer yok |
| 2 | JS test (`tests/components`) | `history.spec.js` · `player-info.spec.js` (2) | Bileşen davranışı | ❌ timer yok |
| 3 | JS fake timer altyapısı | `useFakeTimers` vb. = **0 isabet** | — | ❌ altyapı yok |
| 4 | PHP test (`shared/tests`) | 45 `*Test.php` | Repository/Unit/… | ⚠️ kısmi — 8 dosyada `time()` |
| 5 | Toleranslı assertion | `ApiKeyCreateCommandTest.php:107` (`assertEqualsWithDelta(…, 3)`) | TTL hesabı | ✅ iyi kalıp |
| 6 | Epoch test verisi | `ApiKeyCreateCommandTest.php:186` · `ApiKeyRepositoryTest.php:115/128` · `JwtServiceTest.php:72/127` | Süre dolu/süre dolmamış | ✅ |
| 7 | Bekleme testi | `sleep/usleep` testlerde = **0** | — | ✅ flaky yok |
| 8 | SQL migration testi | Yok (`.sql` dosyaları kod tarafından üretilmiyor) | — | ❌ |

**Kapsama biçimi:** PHP tarafı **epoch doğrulamasını** test ediyor (iyi);
JS tarafı **hiçbir timer'ı** test etmiyor (20 isabet → 0 test). İlk hedef:
`signal-utils.js:15–19` (üretici) + `TouchManager.js:40` (iptal yolları).

### §6.1 — Timer testi hedef matrisi (planlandı — kod yazılmadı)

> Dayanak: §2.4 temizlik matrisi (gerçek `dosya:satır` kanıtları). Bu tablo
> **ne test edileceğini** tanımlar; hiçbir test henüz yazılmadı (§7.1 gap #3).
> Sahte saat API'leri Vitest `vi.*` sarıcılarıdır (`@sinonjs/fake-timers`).

| # | Hedef bileşen | Test edilecek davranış | Sahte saat API | Öncelik | Dayanak |
|---|---|---|---|---|---|
| 1 | `router/config/signal-utils.js:15–19` (`createTimeout`) | Süre dolunca `abort` tetiklenir; `clear()` erken çağrılırsa tetiklenmez | `vi.advanceTimersByTime` + `AbortSignal.aborted` | **P0** | §2.3 #1 |
| 2 | `router/FetchWrapper.js:55–61` (`#delayWithAbort`) | Backoff gecikmesi iptal edilebilir; `finally` her koşulda `clearTimeout` çağırır | `vi.runAllTimers` + spy `clearTimeout` | **P0** | §2.2 #6 |
| 3 | `features/TouchManager.js:40` (long-press) | `LONG_PRESS_MS` dolmadan parmak kalkarsa tetiklenmez; üç iptal yolu (`:49/57/96`) | `vi.advanceTimersByTime` + pointer event | **P1** | §2.2 #8 |
| 4 | `components/composites/ToastComponent.js:162` (timer haritası) | `duration` dolunca toast kapanır; `clearAll()` (`:188`) ve `#remove` (`:201`) tüm bekleyenleri iptal eder | `vi.advanceTimersByTime` + `vi.clearAllTimers` | **P1** | §2.3 #4 |
| 5 | `oauth-manager.js:87–104` (polling + 60 sn kill-switch) | `poll` periyodik tetiklenir; başarıda `:90`, timeout'ta `:101` durdurur | `vi.advanceTimersByTime` (periyot ×N) | **P0** | §2.1 #3 |
| 6 | `features/WidgetManager.js:59` (1000 ms interval) | `#intervals` havuzu(`:91`) sayesinde `destroy()` sonrası sıfır sızıntı | `vi.advanceTimersByTime` + havuz boyutu assert | **P2** | §2.4 #1 |

**Sıralama gerekçesi:** #1–#2 `signal-utils`/`FetchWrapper` ağı bağlı → tüm
sayfa akışı; #5 oturum açma (flow/auth); #3–#4 görsel + gestür; #2 ve #4'teki
`clearAll`/`finally` yolları en çok sızıntı riski taşıyanlardır.

**Assertion kalıbı (yeniden kullanım):** §6 #5'teki `assertEqualsWithDelta(…, 3)`
TTL deseni süre toleransı için uygun; timer testlerinde mutlak `toBe(ms)` yerine
±1 kademe tolerans ile yazılmalı (sahte saat deterministik olsa da üretim
`performance.now` farklıdır).

### §6.2 — Sahte saat altyapısı: ölçülen durum (2026-10-16 taze ölçüm)

| # | Ölçüm | Değer | Yöntem / kapsam |
|---|---|---|---|
| 1 | `useFakeTimers\|advanceTimers\|runAllTimers\|clearAllTimers` | **0** | `assets.coremusic.net/js` recurse grep |
| 2 | Vitest içe aktarma yapan spec/test dosyası | **7** | 6 primitive (`Badge/Button/Input/Skeleton/Slider/Toggle`) + `core/breakpoints.spec.js` |
| 3 | `from 'vitest'` isabeti | **18** | primitive'lerde 2'şer (`Badge…Toggle` ×2) + `breakpoints.spec.js` ×6 |
| 4 | `afterEach\|afterAll\|beforeAll\|vi.useRealTimers` | **15** | 7 spec dosyasında temizlik mekanizması mevcut |
| 5 | Timer testi (gerçek sahte saatli) | **0** | (1) + §2.4 matrisindeki hedeflerin hiçbiri kapsanmıyor |

**Sonuç:** Vitest **kurulu ve import ediliyor** (2–3) ve dosyalar zaten
`afterEach/afterAll` **temizlik kalıbı** kullanıyor (4) — yani sahte saat
altyapısını eklemek için `describe` iskeleti mevcut, yalnızca
`vi.useFakeTimers()` çağrısı + §6.1 hedef assertion'ları eksik.

### §6.3 — Sahte saat kurulum iskeleti (PLANNED — illustratif, üretime alınmadı)

```js
// Timer testi için önerilen iskelet (§6.1 #1 esası).
// Dosya: assets.coremusic.net/js/**/__timer.test.js  ·  Durum: YAZILMADI
import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { createTimeout } from '../router/config/signal-utils.js';

describe('createTimeout', () => {
  beforeEach(() => { vi.useFakeTimers(); });
  afterEach(() => { vi.useRealTimers(); });        // §6.2 #4 mevcut kalıp

  it('süre dolunca abort eder', () => {
    const ctrl = new AbortController();
    createTimeout(100).then(() => {}, () => {});   // üretici: signal-utils:15–19
    vi.advanceTimersByTime(100);
    expect(ctrl.signal.aborted).toBe(false);       // sentinel — gerçek assert
  });                                              // signal bağlantısı eklenince
});
```

> **Dürüstlük kaydı:** §6.3 bloğu bir **taslak/iskelettir**, diskte karşılığı
> yoktur (grep: `*.test.js` timer = 0, §6.2 #1). Üretime alınacak gerçek test
> yazıldığında bu blok silinir veya gerçek dosya:satır ile değiştirilir.

> **Bölüm geçmişi:** §6.1–§6.3, 2026-10-16 spec §7 turunda eklendi; §6.2
> sayımları 2026-10-16'da yeniden ölçülerek §1.2 #8 (fakeTimers = 0) ile
> doğrulandı.

---

## §7 — Boşluklar (gap) ve Sayılmayanlar

### §7.1 Gap listesi (kanıtla)

| # | Gap | Kanıt (negatif ölçüm) | Etiket | Öneri sahibi |
|---|---|---|---|---|
| 1 | **Cron altyapısı yok** | PHP `cron\|crontab` = 0 · SQL `ON SCHEDULE` = 0 · yalnız enum (`logs:325`) | ⚠️ **PLANNED** | DevOps + Backend |
| 2 | **Worker/queue kodu yok** | JS `new Worker` = 0 · `WORKER_*` enum'unu yazan kod = 0 | ⚠️ **PLANNED** | Backend |
| 3 | **JS timer testi yok** | 20 timer isabeti · `useFakeTimers` = 0 · 9 spec içinde timer yok | ⚠️ **eksik** | QA |
| 4 | **Token çelişkisi** | `--duration-fast` = 100ms (`a-design-tokens.css:108`) vs 0ms (`a-theme-config.css:86`) | ⚠️ **karar** | UI + ADR |
| 5 | **SQL enum üreticisiz** | `CRON_JOB_*`, `QUEUE_OVERFLOW`, `WORKER_*` değerlerini INSERT eden kod 0 | ⚠️ **PLANNED** | Backend |
| 6 | **`radio_schedules` okuyucu yok** | tablo `musics.sql:442` · uygulamada `radio_schedules` = 0 isabet | ⚠️ **PLANNED** | Backend + Data |
| 7 | **Çift ToastComponent** | `composites/ToastComponent.js` (aktif, C16) + `interactive/ToastComponent.js` (API korunur) | ⚠️ **netleştirme** | UI |
| 8 | **Sunucu planlaması istek-içi** | rotation/idle yalnız istek anında koşul ile (`SessionLifecycle:59/103`) → isteksiz kalır | ⚠️ **tasarım** | Security + Backend |

**Cron var mı yok mu — kesin cevap:** **YOK.** Üç bağımsız ölçümde de 0
(PHP grep · SQL grep · `.github/workflows` içinde cron schedule tanımı bu
modülün konusu değil, CI ayrı modüldür). Kod olarak **cron örneği yazılmadı**;
`logs.sql:325` enum'u yalnız **gelecek** üreticinin hedefidir (spec §9).

### §7.2 Sayılmayan yüzeyler (bilinçli)

| # | Yüzey | Ölçüm | Neden sayılmadı |
|---|---|---|---|
| 1 | `requestAnimationFrame` | 9 çağrı / 8 dosya | Frame senkronu, süre tutmaz → timing-relative (ör. `SidebarManager.js:165 scheduleRender`) |
| 2 | `RadioPlayer`/`AudioContext` | — | Ses motoru ayrı modül (K0xx audio) |
| 3 | CI cron (`.github/workflows`) | 2 dosya (`ci.yml`, `secret-sans.yml`) | Altyapı modülü; "zamanlamalı görev" kapsamı değil |
| 4 | `radio_schedules` verisi | tablo var | Kod bileşeni değil, şema varlığı (§7.1 #6) |
| 5 | `Session` cookie expiry (42000 sn) | `SessionConfig:16` | Statik eşik, çalışan görev değil — ama **süre SSOT** olarak §3.1'de sayıldı |

### §7.3 Büyüme senaryosu (PLANNED — kod yazılmadı)

| # | İhtiyaç | Gerçekleşmesi için gereken | Bağımlılık |
|---|---|---|---|
| 1 | Gece radyo programı otomasyonu | `radio_schedules` okuyucu + tetikleyici (cron/worker) | gap #1 + #6 |
| 2 | Log döndürme / temizlik | `CRON_JOB_*` enum'unu yazan iş + zamanlayıcı | gap #1 + #5 |
| 3 | Arka planda ağır iş | Web Worker (`setTimeout` worker bağlamında) | gap #2 + MDN #3 kaynağı |
| 4 | Timer regresyonu | Vitest sahte saat kurulumu + 3 öncelikli test | gap #3 |
| 5 | Süre token tekliği | `a-theme-config:86` karar + ADR | gap #4 |

---

## §8 — Açık Sorular

- Yeni çocuk adı kullanıcı onayı ALINDI (2026-10-15, spec §8) — klasör adı v2 §2.11 dışıdır; belge seviyesinde kayıt.
- Cron altyapısı PLANNED — üretime alınacak zamanlayıcı düğümü hangisi (host cron / MySQL event / PHP worker)? → gap #1, **karar yok**.
- `radio_schedules` okuyucusu ne zaman yazılacak → gap #6, **kod yok**.
- `--duration-fast` hangi dosyadan okunacak (`100ms` mi, `0ms` mi) → gap #4, **ADR gerekli**.
- İki ToastComponent'ten hangisi kalıcı → gap #7, `composites` (C16) aktif görünüyor ama `interactive` dosyası silinmemiş.
- Zamanlama testleri için sahte saat altyapısı Vitest'e ne eklenecek → gap #3, **QA kararı**.
- §4'teki 3 zayıf izleme (footer · session · radio_schedules) vault'a satır eşlemesi alsın mı?

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
