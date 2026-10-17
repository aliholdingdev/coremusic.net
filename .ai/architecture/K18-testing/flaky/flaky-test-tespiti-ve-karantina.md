---
title: "K019-testing/flaky — Flaky Test Tespiti ve Karantina: Tekrar Oranı, Retry, Determinism"
node_id: K019-testing/flaky/flaky-test-tespiti-ve-karantina
node_type: module
status: draft
parent_id: K019-testing/flaky
children: []
depends_on:
  - K019-testing
  - K019-testing/flaky    # parent subdomain (sibling deseni ile tutarlı — 2026-10-17)
evidence:
  - "assets.coremusic.net/playwright.config.ts — Satır 19 forbidOnly (CI), Satır 20 retries: process.env.CI ? 2 : 0, Satır 21 workers: process.env.CI ? 1 : undefined (2026-10-13, Select-Object -Index 18/19/20 ile doğrulandı)"
  - "assets.coremusic.net/playwright.config.ts — Satır 30 trace: on-first-retry, Satır 52 timeout: 30 * 1000, Satır 54 timeout: 5 * 1000 (2026-10-13)"
  - ".github/workflows/ci.yml — Satır 19 cancel-in-progress: true, Satır 68 needs: php-lint, Satır 72 fail-fast: false (2026-10-13)"
  - ".github/workflows/ci.yml — Satır 167 ve Satır 183 continue-on-error: true (soft gate, 2026-10-13)"
  - "shared/phpunit.xml — Satır 6 stopOnFailure=false; auth.coremusic.net/phpunit.xml — Satır 6 failOnRisky=true, Satır 7 failOnWarning=true (2026-10-13)"
  - "assets.coremusic.net/vitest.config.js — Satır 31 testTimeout: 5000 (2026-10-13)"
  - "shared/tests/Repository/ApiKeyCreateCommandTest.php — Satır 106-107 assertEqualsWithDelta(time() + $seconds, (int) strtotime($result), 3, (string) $ttl) zaman payı yorumu ile birlikte (2026-10-13)"
  - "shared/tests/Events/DomainEventTest.php — Satır 63-69 1 saniyelik zaman penceresi assertion'ı (2026-10-13)"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php — Satır 170-172 koşullu markTestSkipped (2026-10-13)"
validation:
  - "Dosya satır sayısı [IO.File]::ReadAllLines ile ölçüldü (2026-10-13)"
  - "22 aday URL Invoke-WebRequest -Method Head ile HTTP 200 olarak doğrulandı (2026-10-13)"
  - "URL envanteri: 1441 benzersiz URL (2026-10-13 temel ölçüm), bu dosyada kesişim 0"
  - "Repo satır alıntıları Select-Object -Index (N-1) ile birebir doğrulandı (2026-10-13)"
  - "2026-10-17: depends_on parent satırı eklendi · group mimari → kod (tutarlılık) · §11.6 çok-dilli karşılıklar eklendi · Kademe-2 index.md üretildi"
open_questions:
  - "Depoda flaky tekrar oranı ölçümü (retry sayacı veya raporu) yok — Playwright json raporu karşılaştırılmıyor (⚠️ VERIFICATION REQUIRED)"
  - "Karantina listesi dosyası (quarantine manifest) depoda yok (⚠️ VERIFICATION REQUIRED)"
  - "PHP testleri için retry tanımlı değil; PHPUnit retry bayrağı yapılandırmada görünmüyor (⚠️ VERIFICATION REQUIRED)"
  - "Vitest yapılandırmasında retry alanı yok; yalnız testTimeout var (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K019-testing/flaky/flaky-test-tespiti-ve-karantina

> **ELI10:** Flaky test, aynı kod aynı şekilde koşulduğunda bazen geçip bazen geçen testtir.
> Sorun testin kendisi değil, **güvenilmezliğidir**: gerçek bir hatayı da geçebilir, sahte
> kırmızı da üretebilir. Bu dosya dört soruyu yanıtlar: flaky nasıl **tespit** edilir (tekrar
> oranı), tespit edilene ne yapılır (**retry** mı **karantina** mı), flaky olmayan test nasıl
> **deterministic** tutulur (zaman/test bağlantısı) ve karantinadan nasıl **çıkılır**
> (quarantine gate).

## §1 Amaç

Bu modül, CoreMusic'in **kararsız test (flaky test) tespiti ve karantina kurallarının tek
yetkili tanımıdır**. Dört karar bu dosyada verilir: tekrar oranı eşiği · retry politikası ·
karantina listesi ve çıkışı · determinism kuralları. Kararsız yeşil, kırmızıdan tehlikelidir:
yanlış güven üretir.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Test ne zaman flaky sayılır? | Tekrar oranı (repeat rate) tanımı | §4 |
| 2 | Flaky nasıl ölçülür? | Ölçüm yöntemi ve örneklem | §4.3 |
| 3 | Yeniden deneme kaç kez? | Retry politikası | §7 |
| 4 | Flaky test nereye konur? | Karantina listesi | §8 |
| 5 | Karantinadan çıkış nasıl? | Quarantine gate | §9 |
| 6 | Flaky olmaz için ne yapılır? | Determinism + zaman/test bağlantısı | §5 · §6 |
| 7 | Mevcut kapılar ne durumda? | Repo mevcut durum özeti | §9.3 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | E2E retry (CI) | adet | 2 (playwright.config.ts Satır 20) | kodda sabit |
| H2 | E2E retry (yerel) | adet | 0 (playwright.config.ts Satır 20) | kodda sabit |
| H3 | PHP unit retry | adet | tanımsız | ⚠️ VERIFICATION REQUIRED |
| H4 | Vitest retry | adet | yapılandırmada yok | ⚠️ VERIFICATION REQUIRED |
| H5 | Karantina listesi | dosya | depoda yok | ⚠️ VERIFICATION REQUIRED |
| H6 | Tekrar oranı raporu | rapor | depoda yok | ⚠️ VERIFICATION REQUIRED |
| H7 | CI iptal (eski koşu) | davranış | `cancel-in-progress: true` | ci.yml Satır 19 |
| H8 | Risky/warning kapısı | bayrak | `failOnRisky=true` (auth) | auth phpunit.xml Satır 6 |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Flaky tanımı | ✅ Tekrar oranı eşiği | ❌ Test yazımı → `unit/` |
| Tespit | ✅ Ölçüm yöntemi ve örneklem | ❌ Rapor altyapısı → K017-observability |
| Retry | ✅ Politika, tavan, neyi maskeler | ❌ Süre bütçesi → `performance/` |
| Karantina | ✅ Liste, giriş, çıkış, sahiplik | ❌ CI job tanımı → K018-devops/pipeline |
| Determinism | ✅ Kaynak listesi ve denetim | ❌ Assertion kalitesi → `unit/` |
| Zaman/test bağlantısı | ✅ Saat, timeout, bekleyiş yasağı | ❌ Zaman sunucusu → altyapı |
| Regresyon etkisi | ✅ Karantinanın seçime etkisi | ❌ Seçim kuralı → `regression/` |
| E2E kararsızlığı | ✅ Tarayıcı kaynaklı flaky | ❌ Locator kuralı → `e2e/` |

**Sınır kararı:** kararın ta kendisi buradadır — **flaky test silinmez ve retry ile
sürekli başarıya zorlanmaz**; ölçülür, kök nedeni sınıflandırılır ve süreli karantinaya
alınır (§8.4). Retry bir **maske**, karantina bir **teşhis** aracıdır.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Flaky test | flaky test | Aynı durumda farklı sonuç üreten test | §4 |
| Tekrar oranı | repeat rate | Aynı sürümde tekrar koşumundaki tutarsızlık oranı | §4.2 |
| Retry | retry / re-run | Aynı testi yeniden koşma | §7 |
| Karantina | quarantine | Flaky testin geçici olarak dışlanması | §8 |
| Determinism | determinism | Aynı girdi → aynı çıktı garantisi | §5 |
| Saat bağımlılığı | clock dependency | `time()` / `DateTimeImmutable` ile davranış | §6.1 |
| Sahte yeşil | false green | Retry ile geçen ama gerçekte bozuk test | §7.3 |
| Soft gate | soft gate | Görünür-kırmızı, bloklamayan kapı | ci.yml Satır 167 |
| Quarantine gate | quarantine gate | Karantinaya giriş/çıkış kapısı | §9 |
| Kök neden | root cause | Flaky'ın kaynağı (zaman, sıra, kaynak, ağ) | §16 |
| Örneklem | sample | Tekrar koşumunun kaç örneklemde yapıldığı | §4.3 |
| Maskeleme | masking | Retry ile kök nedeni gizleme | §7.3 |

## §4 Flaky tanımı ve tekrar oranı

### §4.1 Flaky tanımı

| # | Koşul | Flaky mı? | Gerekçe |
|---|---|---|---|
| 1 | Aynı commit, aynı ortam, art arda koşumda sonuç değişiyor | evet | Tanımın kendisi |
| 2 | İlk koşum geçti, ikinci koşum aynı kodda geçmedi | evet | Kararsız yeşil |
| 3 | Retry sonrası geçiyor | evet (gizlenmiş) | §7.3 maskeleme |
| 4 | Test ortam değişince (zaman dilimi, makine) sonuç değişiyor | evet | §6 ortam bağımlılığı |
| 5 | Kod değişmeden yalnız sırayla değişen test sonucu değişiyor | evet | §5.3 sıra bağımlılığı |
| 6 | Kod değişince tutarlı şekilde kırmızıya döndü | hayır | Bu bir regresyon, flaky değil → `regression/` |
| 7 | Test koşulmuyor (skip) | hayır | §5.4 atlanan test |

### §4.2 Tekrar oranı (repeat rate) tanımı

| # | Kavram | Tanım | Ölçüm |
|---|---|---|---|
| 1 | Örneklem (sample) | Tek bir sürüm için aynı testin N kez koşumu | §4.3 |
| 2 | İstisnasız yeşil | N/100 yeşil → sağlıklı | tekrar oranı %0 |
| 3 | Flaky eşik altı | İstisnasız değil, ama oranı düşük | oran kaydedilir |
| 4 | Flaky eşik üstü | Oran eşiği aşarsa karantina tetiklenir | §9.1 |
| 5 | Kararsız kırmızı | Bazı koşumlarda geçip bazılarında geçmeyen | aynı tanım, ters yön |

**Eşik kararı (bu dosyanın kararı):**

| # | Eşik | Değer | Karar | Durum |
|---|---|---|---|---|
| E1 | Tekrar oranı eşiği | %1 ve üzeri tutarsızlık | karantina adayı | bu dosyada tanımlandı (repo uygulaması yok — ⚠️ VERIFICATION REQUIRED) |
| E2 | Tekrar örneklemi | ≥ 10 koşum | oran ancak öyle okunur | bu dosyada tanımlandı |
| E3 | Retry tavanı (E2E) | 2 | ci.yml E2E job'ında Playwright devralır | playwright.config.ts Satır 20 |
| E4 | Retry tavanı (PHP) | tanımsız | PHPUnit yapılandırmasında yok | ⚠️ VERIFICATION REQUIRED |
| E5 | Retry tavanı (Vitest) | yapılandırmada yok | `vitest.config.js` içinde retry alanı yok | ⚠️ VERIFICATION REQUIRED |

### §4.3 Ölçüm yöntemi ve örneklem

| # | Adım | Nasıl | Çıktı | Repo durumu |
|---|---|---|---|---|
| 1 | Aynı sürümü tekrar koş | N ≥ 10 kez | sonuç matrisi | mevcut değil (⚠️ VERIFICATION REQUIRED) |
| 2 | Sonuçları test-adına göre grupla | test id → sonuç dizisi | flaky skoru | mevcut değil |
| 3 | Oranı hesapla | tutarsız koşum / toplam | tekrar oranı | mevcut değil |
| 4 | Eşiği uygula | §4.2 E1 | karantina adayı listesi | mevcut değil |
| 5 | Kök nedeni sınıflandır | §16 matrisi | sınıf etiketi | el ile inceleme |
| 6 | Karantinaya al | §8 kayıt şablonu | karantina girdisi | mevcut değil |

**Ölçüm notu:** Playwright json raporu `test-results/e2e-results.json` dosyasına yazılır
(playwright.config.ts Satır 25); rapor **üretiliyor fakat tekrar oranı için karşılaştırılmıyor**
— bu karşılaştırma bu dosyanın açığıdır ve §9.3'te durumu işaretlenmiştir.

## §5 Determinism (kararlılık) kuralları

### §5.1 Determinism kaynakları

| # | Kaynak | Belirti | Sınıf | Önleme |
|---|---|---|---|---|
| 1 | Saat (`time()`) | Saniye sınırında değişen sonuç | zaman | §6.1 pay / sabitleme |
| 2 | Sıra (test order) | Yalnız belirli sırada geçer | paylaşım | §5.3 |
| 3 | Paylaşılan durum | `$_SERVER`, dosya, cache | ortak kaynak | her testte kur/sök |
| 4 | Rastgelelik | `random_bytes` / uniqid | rastgele | tohum sabitleme veya enjeksiyon |
| 5 | Ağ / dosya sistemi | Yokluğa bağlı sonuç | ortam | çift taraflı stub |
| 6 | Zaman aşımı (timeout) | Makine yüküne bağlı sonuç | kaynak | §6.3 bütçe |
| 7 | Koşula bağlı assertion | `if` içinde assertion | kör geçiş | koşulsuz assertion |

### §5.2 Repo determinism kanıtları (gerçek kod)

| # | Dosya | Satır | Ne kanıtlıyor | Determinism etkisi |
|---|---|---|---|---|
| 1 | shared/tests/Middleware/RateLimiterMiddlewareTest.php | 29-33 | `RateLimiterMiddleware(maxRequests: 60, windowSeconds: 60, cache: $cache)` — cache mock ile kurulur | gerçek saat/cache dokunulmaz → deterministic |
| 2 | shared/tests/Repository/ApiKeyCreateCommandTest.php | 106-107 | `assertEqualsWithDelta(time() + $seconds, (int) strtotime($result), 3, (string) $ttl)` — 3 saniye payı | zaman payı bilinçli, ama kalan risk §6.1 |
| 3 | shared/tests/Events/DomainEventTest.php | 63-69 | `new \DateTimeImmutable('-1 second')` / `'+1 second'` penceresi | dar pencere → sistem saatine bağımlı |
| 4 | shared/tests/Unit/Log/LoggerRedactionTest.php | 170-172 | `ini_set('error_log', $logFile) === false` ise `markTestSkipped` | ortama bağlı koşullu skip → kapsam dalgalanır |
| 5 | shared/tests/Unit/Security/JwtServiceTest.php | 72 | `'iat' => time(), 'nbf' => time(), 'exp' => time() + 3600` | test verisi canlı saatle üretilir |
| 6 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 94-96 | `if (initialAlt === 'Oynat')` bloğu içinde `click` + assertion | koşula bağlı assertion → kör geçiş riski |
| 7 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 393 | `await page.waitForTimeout(50);` sabit bekleyiş | §6.2 yasağı ihlali (mevcut kod) |

**Doğrulama (§5.2 satır kanıtı):** her satır `Select-Object -Index (N-1)` ile okundu;
örneğin `RateLimiterMiddlewareTest.php` için `Select-Object -Index 28` çıktısı
`$middleware = new RateLimiterMiddleware(` satırıdır (2026-10-13).

### §5.3 Sıra (order) bağımlılığı

| # | Kural | Gerekçe | Denetim |
|---|---|---|---|
| 1 | Test, kendi durumunu kendisi kurar | Başka testten devralınan durum | kod incelemesi |
| 2 | Test, kendi durumunu kendisi söker | Sonraki teste sızan durum | `tearDown` denetimi |
| 3 | `$_SERVER` / global değiştirilmişse geri yüklenir | Süreç içi sızıntı | ApiPipelineSecurityTest Satır 30-32 örneği |
| 4 | Paralel koşumda yazılan dosya adı benzersizdir | Çakışma | `bin2hex(random_bytes(6))` kalıbı (LoggerRedactionTest Satır 167) |
| 5 | `stopOnFailure=false` ile tüm rapor toplanır | Tek hata diğer sonuçları gizlemesin | shared/phpunit.xml Satır 6 |

### §5.4 Atlanan (skip) test kuralı

| # | Durum | Karar | Sonuç |
|---|---|---|---|
| 1 | Koşullu `markTestSkipped` | izinli, ama **gerekçesi zorunlu** | LoggerRedactionTest Satır 171 gerekçesi mevcut |
| 2 | Kalıcı skip (artık geçerli değil) | test silinir veya düzeltilir | atıl test = sahte kapsam |
| 3 | Skip oranı artış gösterirse | kapsam raporu ile birlikte okunur | `coverage/` düğümü |
| 4 | Skip, flaky'i gizlemek için kullanılırsa | yasak | §12 |

## §6 Zaman / test bağlantısı

### §6.1 Saat bağımlılığı kuralları

| # | Kural | Doğru | Yanlış | Denetim |
|---|---|---|---|---|
| 1 | Zaman duyarlı eşitlemede pay (delta) kullan | `assertEqualsWithDelta(time() + $seconds, (int) strtotime($result), 3, (string) $ttl)` | birebir `assertEquals` | ApiKeyCreateCommandTest Satır 107 |
| 2 | Pay değeri gerekçelendirilir | dosyada yorum satırı | sihirli sayı | ApiKeyCreateCommandTest Satır 106 yorumu |
| 3 | Pencere testleri geniş yazılır | ±1 sn ve üzeri | milisaniye penceresi | DomainEventTest Satır 65-67 |
| 4 | Saat enjeksiyonu mümkünse test saati kullanılır | `ClockInterface` benzeri enjeksiyon | doğrudan `time()` | mimari tercih — ⚠️ VERIFICATION REQUIRED (depoda saat enjeksiyonu görünmüyor) |
| 5 | Tarih formatı sabitlenir | ISO 8601 (`Date.toISOString`) | yerel locale formatı | ortam bağımsızlığı |

**Not:** `time()` kullanımı tek başına flaky yapmaz; flaky yapan, **sınırdaki** (saniye
kavisi, TTL dolması) beklemedir. Sınır testlerinde pay zorunludur.

### §6.2 Bekleyiş (sleep) kuralı

| # | Kural | Gerekçe | Repo durumu |
|---|---|---|---|
| 1 | Test içinde sabit `sleep` ile senkronlama yasaktır | Yavaş + yarış | player-info.spec.ts Satır 96, 185, 393'te `waitForTimeout` mevcut → kayıt altında |
| 2 | Yerine olay/koşul bekleme kullanılır | Deterministik | `e2e/` §7 kalıbı |
| 3 | Kalan sabit bekleyiş gerekçelendirilir | Gerekçesiz = borç | kod incelemesi |
| 4 | Bekleyiş toplamı süre bütçesine sayılır | Bütçe aşımı | `performance/` |

### §6.3 Timeout ve kaynak bütçesi

| # | Kalem | Değer | Kaynak | Flaky etkisi |
|---|---|---|---|---|
| 1 | Test timeout (E2E) | 30 000 ms | playwright.config.ts Satır 52 | düşük |
| 2 | Assertion timeout (E2E) | 5 000 ms | playwright.config.ts Satır 54 | orta (daralırsa flaky) |
| 3 | Test timeout (Vitest) | 5 000 ms | vitest.config.js Satır 31 | orta |
| 4 | Job timeout (php-test) | 15 dk | ci.yml Satır 70 | düşük |
| 5 | Job timeout (js-test) | 10 dk | ci.yml Satır 145 | düşük |
| 6 | Job timeout (js-e2e) | 20 dk | ci.yml Satır 181 | düşük |
| 7 | Web sunucu hazırlik | 120 000 ms | playwright.config.ts Satır 49 | yüksek (hazır olmazsa tüm E2E kırmızı) |

### §6.4 Ortam bağımlılığı

| # | Bağımlılık | Belirti | Karar |
|---|---|---|---|
| 1 | `ini_set` başarısız | Koşullu skip | LoggerRedactionTest Satır 170-172 — gerekçeli, izinli |
| 2 | Yerel sunucu portu dolu | E2E başlamaz | `reuseExistingServer` (playwright.config.ts Satır 48) |
| 3 | CI'da `CI` ortam değişkeni | retry/workers değişir | playwright.config.ts Satır 19-21 |
| 4 | Node sürümü | vitest engines sınırı | ci.yml Satır 151 yorumu |
| 5 | Eksik `tests/Integration` dizini | Suite hatası | auth phpunit.xml Satır 10-12 yorumu |

## §7 Retry politikası

### §7.1 Retry karar matrisi

| # | Katman | Retry var mı? | Değer | Kaynak |
|---|---|---|---|---|
| 1 | Playwright (CI) | evet | 2 | playwright.config.ts Satır 20 |
| 2 | Playwright (yerel) | hayır | 0 | playwright.config.ts Satır 20 |
| 3 | PHPUnit (shared/auth) | tanımsız | yok | ⚠️ VERIFICATION REQUIRED |
| 4 | Vitest | yapılandırmada yok | 0 varsayımı | vitest.config.js (retry alanı yok) |
| 5 | CI job tekrarı | hayır | job başına tek koşum | ci.yml (retry alanı yok) |
| 6 | GitHub Actions workflow_dispatch tekrarı | el ile | manuel | K018-devops |

### §7.2 Retry kuralları

| # | Kural | Değer | Gerekçe |
|---|---|---|---|
| 1 | Retry tavanı | E2E için 2 (CI) | trace ilk tekrarda alınır (Satır 30) |
| 2 | Retry yerel koşumda | 0 | Yerelde sorun gizlenmesin |
| 3 | Retry, kök nedeni kapatmaz | zorunlu | Her retry olayı §4.3 ölçümüne girdi olur |
| 4 | Retry sonrası başarısız | kırmızı → §8 karantina tetikleyicisi | Kalıcı kırmızı flaky değildir |
| 5 | Retry ile geçen test | flaky kabul edilir | Retry üzerinden geçen = tekrar oranı > 0 |
| 6 | Retry, yalnız birim/entegrasyonda | Kural olarak yok; tanımlanırsa tavan 1 | Aşırı retry sahte yeşil üretir |

### §7.3 Maskeleme (retry'ın zararı)

| # | Durum | Ne olur | Sonuç |
|---|---|---|---|
| 1 | Retry 2 → test geçti | Gerçek flaky gizlenir | Kararsızlık görünmez kalır |
| 2 | Retry sonsuz | Gerçek hata da geçebilir | Sahte yeşil |
| 3 | Retry ölçülmez | Tekrar oranı okunamaz | §4.3 ölçümü devre dışı |
| 4 | Retry ile kırmızı maskelenir | Regresyon erken yakalanmaz | `regression/` etkisi |

**Karar:** retry, **ölçüm eşlik etmeden** artırılmaz. Retry varsa json raporundan
"ilk deneme başarısız / tekrarla başarılı" ayrımı §9.3'te zorunlu bir izleme kalemidir.

### §7.4 Retry – karantina ayrımı

| Sinyal | İlk deneme | Retry sonrası | Karar |
|---|---|---|---|
| Sürekli kırmızı | kırmızı | kırmızı | Regresyon — karantina DEĞİL, düzelt (`regression/`) |
| Ara sıra kırmızı | kırmızı/yeşil karışık | yeşil | Flaky — §8 karantina |
| İstisnasız yeşil | yeşil | yeşil | Sağlıklı — eylem yok |
| İstisnasız yeşil ama assertion yok | yeşil | yeşil | Kör test — `unit/` güçlendirme |

## §8 Karantina listesi (quarantine)

### §8.1 Karantina girişi

| # | Tetikleyici | Koşul | Aksiyon |
|---|---|---|---|
| T1 | Tekrar oranı | ≥ %1 tutarsızlık (§4.2 E1) | Karantina adayı |
| T2 | Retry'la geçme | İlk deneme kırmızı, retry yeşil | Karantina adayı |
| T3 | Ortam bağımlılığı | Koşullu skip tekrarı artıyor | İnceleme → karantina |
| T4 | Sabit bekleyiş flaky | `waitForTimeout` ile geçen test | İnceleme → karantina |
| T5 | Sürekli kırmızı | 3 koşumda da kırmızı | Regresyon — karantina DEĞİL |

### §8.2 Karantina kaydı şablonu (zorunlu alanlar)

| # | Alan | Zorunlu | Örnek değer |
|---|---|---|---|
| 1 | Test kimliği | evet | `tests/e2e/player-info.spec.ts :: test adı` |
| 2 | Giriş tarihi | evet | ISO 8601 |
| 3 | Giriş tetikleyicisi | evet | T1 / T2 / T3 / T4 |
| 4 | Ölçülen tekrar oranı | evet | `n / n` biçiminde örneklem |
| 5 | Sınıflandırma (§16) | evet | zaman / sıra / ortam / bekleyiş / bilinmiyor |
| 6 | Kök neden notu | evet | tek cümle |
| 7 | Sorumlu | evet | rol (QA) |
| 8 | Çıkış koşulu | evet | §9.2 |
| 9 | Son tarih | evet | karantina süresi |
| 10 | Kapanış kanıtı | kapanışta | N/100 istisnasız yeşil |

### §8.3 KarantinaListesi dosyası

| # | Nitelik | Değer |
|---|---|---|
| 1 | Dosya adı | `quarantine-manifest.md` (ad önerisi) |
| 2 | Konum | `.ai/architecture/K019-testing/flaky/` altı veya test kökü — ⚠️ VERIFICATION REQUIRED (depoda yok) |
| 3 | Biçim | Markdown tablo (§8.2 alanları) |
| 4 | Erişim | Salt-okunur CI; düzenleme el ile |
| 5 | Depo durumu | **yok** — karantina mekanizması henüz kurulmamıştır |

### §8.4 Karantina süresi ve çıkışı

| # | Kural | Değer |
|---|---|---|
| 1 | Karantina süresiz değildir | Azami süre atanır (öneri: 30 gün) |
| 2 | Süre dolunca | Otomatik çıkış değil, **gözden geçirme** |
| 3 | Çıkış | §9.2 gate şartları sağlanınca |
| 4 | Süre dolup düzeltilmeyen | Kalıcı karar: düzelt veya sil — iki ucu açık bekletme yasak |
| 5 | Karantinadaki test | Çalıştırılır ve oranı izlenir; sessizce atlanmaz |

## §9 Quarantine gate (karantina kapısı)

### §9.1 Giriş kapısı

| # | Koşul | Doğrulama | Karar |
|---|---|---|---|
| 1 | Tekrar oranı ≥ %1 | §4.3 ölçümü | karantina adayı |
| 2 | Retry ile geçiyor | rapor ayrımı | karantina adayı |
| 3 | Regresyon değil (§7.4) | 3 koşum analizi | flaky ise devam |
| 4 | Kayıt eksiksiz (§8.2) | 10 alan | eksikse giriş yok |

### §9.2 Çıkış kapısı (release gate)

| # | Şart | Değer | Kanıt |
|---|---|---|---|
| 1 | Örneklem | ≥ 10 ardışık koşum | koşum kaydı |
| 2 | Tekrar oranı | %0 (istisnasız yeşil) | rapor |
| 3 | Kök neden | giderilmiş ve sınıf etiketi kapatılmış | §16 |
| 4 | Retry | test retry olmadan geçiyor | ilk deneme yeşil |
| 5 | Bekleyiş | sabit `sleep` kalmamış | kod incelemesi |
| 6 | Onay | QA | kayıt imzası |

### §9.3 Mevcut kapı durumu (repo gerçeği — 2026-10-13)

| # | Kapı | Değer | Dosya / Satır | Durum |
|---|---|---|---|---|
| 1 | E2E retry (CI) | 2 | playwright.config.ts / Satır 20 | mevcut |
| 2 | E2E ilk deneme iptali | `cancel-in-progress: true` | .github/workflows/ci.yml / Satır 19 | mevcut |
| 3 | Matrix fail-fast | `false` (tüm matris koşulur) | ci.yml / Satır 72 | mevcut |
| 4 | Risky kapısı (auth) | `failOnRisky=true` | auth.coremusic.net/phpunit.xml / Satır 6 | mevcut |
| 5 | Warning kapısı (auth) | `failOnWarning=true` | auth.coremusic.net/phpunit.xml / Satır 7 | mevcut |
| 6 | Durma davranışı (shared) | `stopOnFailure=false` | shared/phpunit.xml / Satır 6 | mevcut |
| 7 | JS kapsam kapısı | `continue-on-error: true` | ci.yml / Satır 167 | soft |
| 8 | E2E kapısı | `continue-on-error: true` | ci.yml / Satır 183 | soft |
| 9 | Tekrar oranı raporu | yok | — | ⚠️ VERIFICATION REQUIRED |
| 10 | Karantina listesi | yok | — | ⚠️ VERIFICATION REQUIRED |
| 11 | Retry–rapor ayrımı (ilk deneme/tekrar) | yok | json raporu karşılaştırılmıyor | ⚠️ VERIFICATION REQUIRED |

## §10 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | assets.coremusic.net/playwright.config.ts | 19 | `forbidOnly: !!process.env.CI` (CI'da `.only` yasağı) |
| 2 | assets.coremusic.net/playwright.config.ts | 20 | `retries: process.env.CI ? 2 : 0` |
| 3 | assets.coremusic.net/playwright.config.ts | 21 | `workers: process.env.CI ? 1 : undefined` |
| 4 | assets.coremusic.net/playwright.config.ts | 25 | json rapor dosyası tanımı |
| 5 | assets.coremusic.net/playwright.config.ts | 30 | `trace: 'on-first-retry'` — trace ilk tekrarda |
| 6 | assets.coremusic.net/playwright.config.ts | 52 | `timeout: 30 * 1000` |
| 7 | assets.coremusic.net/playwright.config.ts | 54 | `expect` timeout `5 * 1000` |
| 8 | .github/workflows/ci.yml | 19 | `cancel-in-progress: true` |
| 9 | .github/workflows/ci.yml | 68 | `needs: php-lint` (sıra zinciri) |
| 10 | .github/workflows/ci.yml | 70 | php-test `timeout-minutes: 15` |
| 11 | .github/workflows/ci.yml | 72 | `fail-fast: false` |
| 12 | .github/workflows/ci.yml | 145 | js-test `timeout-minutes: 10` |
| 13 | .github/workflows/ci.yml | 167 | js-coverage `continue-on-error: true` |
| 14 | .github/workflows/ci.yml | 181 | js-e2e `timeout-minutes: 20` |
| 15 | .github/workflows/ci.yml | 183 | js-e2e `continue-on-error: true` |
| 16 | shared/phpunit.xml | 6 | `stopOnFailure="false"` |
| 17 | auth.coremusic.net/phpunit.xml | 6-7 | `failOnRisky` + `failOnWarning` |
| 18 | auth.coremusic.net/phpunit.xml | 10-12 | Integration suite kaldırma gerekçesi |
| 19 | assets.coremusic.net/vitest.config.js | 31 | `testTimeout: 5000` (retry alanı yok) |
| 20 | shared/tests/Repository/ApiKeyCreateCommandTest.php | 106-107 | 3 sn zaman payı + gerekçe |
| 21 | shared/tests/Events/DomainEventTest.php | 63-69 | ±1 sn zaman penceresi |
| 22 | shared/tests/Unit/Security/JwtServiceTest.php | 72 | `time()` ile test verisi |
| 23 | shared/tests/Repository/ApiKeyRepositoryTest.php | 115, 128 | `time() - 60` / `time() + 3600` TTL verisi |
| 24 | shared/tests/Unit/Log/LoggerRedactionTest.php | 167, 170-172 | benzersiz dosya adı + koşullu skip |
| 25 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 94-96 | koşula bağlı assertion + `waitForTimeout(100)` |
| 26 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 372-375 | süre assertion'ı gerekçesi (2500 ms) |
| 27 | assets.coremusic.net/tests/e2e/player-info.spec.ts | 393 | `waitForTimeout(50)` sabit bekleyiş |
| 28 | package.json | 8 | `test:e2e` scripti (tekrar koşum giriş kapısı) |

## §11 Kod örnekleri — repo gerçek kodu

### §11.1 Retry kararı — `Dosya: assets.coremusic.net/playwright.config.ts / Satır: 19-21`

```ts
forbidOnly: !!process.env.CI,
retries: process.env.CI ? 2 : 0,
workers: process.env.CI ? 1 : undefined,
```

Okuma: CI'da 2 tekrar, yerelde 0; tek worker ile E2E paralelliği kısılmış (sıra bağımlı
testleri flaky yapmaması için değil, kaynak payı için — ⚠️ VERIFICATION REQUIRED, gerekçe
dosyada yazılmamıştır).

### §11.2 Zaman payı kalıbı — `Dosya: shared/tests/Repository/ApiKeyCreateCommandTest.php / Satır: 106-107`

```php
// `date()` + `time()` aynı saniyede kalmayabilir - 3 sn payı.
$this->assertEqualsWithDelta(time() + $seconds, (int) strtotime($result), 3, (string) $ttl);
```

Okuma: sınır (kavisi) testinde birebir eşitlik yerine **pay** kullanılmış; payın gerekçesi
yorum satırında. Bu kalıp §6.1 kural 1-2'nin repo karşılığıdır.

### §11.3 Zaman penceresi — `Dosya: shared/tests/Events/DomainEventTest.php / Satır: 63-69`

```php
public function testEventTimestampIsRecent(): void
{
    $before = new \DateTimeImmutable('-1 second');
    $event = new UserLoggedInEvent('u1', '127.0.0.1', 'Test');
    $after = new \DateTimeImmutable('+1 second');
    $this->assertGreaterThanOrEqual($before, $event->occurredOn());
    $this->assertLessThanOrEqual($after, $event->occurredOn());
}
```

Okuma: olay zaman damgası ±1 sn penceresinde doğrulanır. Pencere kapanırsa (sistem saati
atlarsa) test flaky olur — §6.1 kural 3'ün sınır örneği.

### §11.4 Koşullu skip — `Dosya: shared/tests/Unit/Log/LoggerRedactionTest.php / Satır: 170-172`

```php
if (ini_set('error_log', $logFile) === false) {
    $this->markTestSkipped('error_log ini bu ortamda değiştirilemiyor');
}
```

Okuma: ortam engeli gerekçesiyle atlanır. §5.4 kural 1'e uygundur; atlanan testin kapsam
raporuna etkisi `coverage/` üzerinden izlenir.

### §11.5 Soft gate — `Dosya: .github/workflows/ci.yml / Satır: 167`

```yaml
continue-on-error: true
```

Okuma: js-coverage işi kırmızı görünür fakat akışı bloklamaz. Flaky bir E2E benzeri
biçimde `continue-on-error` (Satır 183) ile kırmızı maskelenirse §7.3 uyarısı geçerlidir:
görünür-kırmızı kuralı kapı sertliğiyle birlikte okunmalıdır (`regression/` §9.3).

### §11.6 Çok-dilli karşılıklar (12 dil)

> Flaky mücadele kalıbı (determinism kaynağında düzeltme + retry eklentisi envanteri) 12 dilde.
> Kural §7.2-§7.3: retry **maskeler**, determinism **düzeltir** — her dilde önce kaynak fix, sonra eklenti.
> Sürüm iddiası yazılmaz (bilgi amaçlı).

| # | Dil | Determinism aracı | Retry eklentisi |
|---|---|---|---|
| 1 | PHP | PHPUnit `--repeat` + `Clock` enjeksiyonu | `symfony/phpunit-bridge` `SYMFONY_PHPUNIT_RETRY_AFTER_FAILURES` |
| 2 | TypeScript | Vitest `vi.useFakeTimers()` | Vitest config `retry` alanı |
| 3 | Python | `time-machine` / `freezegun` | `pytest-rerunfailures` (`--reruns 3`) |
| 4 | C# | `TimeProvider` (.NET) enjeksiyonu | NUnit `[Retry(3)]` attribute |
| 5 | Java | sahte `Clock` + sabit `UUID` seed | maven-surefire `rerunFailingTestsCount` |
| 6 | Go | `t.Setenv` + `time.Now` interface enjeksiyonu | **yok** — Go kültürü: determinism kaynağında |
| 7 | C++ | `std::chrono` sabit saat + GTest `--gtest_repeat` | Google Test `--gtest_repeat=N` |
| 8 | Rust | `proptest` + `std::time::SystemTime` enjeksiyonu | **yok** (yerleşik determinism kültürü) |
| 9 | Ruby | `Timecop` (zaman dondurma) | `rspec-retry` gem (`--retry 3`) |
| 10 | Kotlin | sabit `Clock` + `kotlinx.datetime` test modu | JUnit5 + surefire `rerunFailingTestsCount` |
| 11 | Swift | `Date` enjeksiyonu (protocol) | XCTest `repeatCount` (XCTRepeat) |
| 12 | C | `time()` fonksiyon pointer enjeksiyonu | CMocka + koşum betiği döngüsü |

**PHP (zaman enjeksiyonu — repo deseni §11.2-§11.3):**

```php
public function testTtlSiniriAsmaz(): void
{
    $sabit = new SabitClock(1_700_000_000);           // Arrange: determinism
    $komut = new ApiKeyCreateCommand($this->db, $sabit);
    $sonuc = $komut->calistir(kullaniciId: 1, saniye: 60);
    $this->assertEqualsWithDelta(1_700_000_060, (int) strtotime($sonuc), 1);
}
```

**TypeScript (Vitest fake timers + retry):**

```ts
import { vi, it, expect } from 'vitest';

it('TTL sınırı aşmaz', () => {
  vi.useFakeTimers();                                // Arrange: determinism
  vi.setSystemTime(new Date('2023-11-14T22:13:20Z'));
  const sonuc = ttlHesapla(60);
  expect(sonuc).toBe(1_700_000_060);
  vi.useRealTimers();
}); // vitest.config: { test: { retry: 2 } } — retry YALNIZCA debugging içindir (§7.3)
```

**Python (time-machine + pytest-rerunfailures):**

```python
import time_machine
from datetime import datetime, timezone

@time_machine.travel(datetime(2023, 11, 14, 22, 13, 20, tzinfo=timezone.utc))
def test_ttl_siniri_asmaz():
    assert ttl_hesapla(60) == 1_700_000_060          # determinism
# CI: pytest --reruns 3 --reruns-delay 1 (yalnız CI'da; yerelde flaky görünür — §7.4)
```

**C# (TimeProvider + NUnit Retry):**

```csharp
[Test]
[Retry(3)]                                            // CI retry — maskeler, §7.3 uyarısı
public void TtlSiniriAsmaz()
{
    var saat = new SabitTimeProvider(DateTimeOffset.FromUnixTimeSeconds(1_700_000_000));
    var komut = new ApiKeyCreateCommand(db, saat);    // Arrange: determinism
    Assert.AreEqual(1_700_000_060, komut.Calistir(60).UnixSeconds);
}
```

**Java (sabit Clock + surefire rerun):**

```java
@Test
void ttlSiniriAsmaz() {
    Clock sabit = Clock.fixed(Instant.ofEpochSecond(1_700_000_000), ZoneOffset.UTC);
    var komut = new ApiKeyCreateCommand(db, sabit);   // Arrange: determinism
    assertEquals(1_700_000_060L, komut.calistir(60)); // Assert
}
// pom.xml: <rerunFailingTestsCount>2</rerunFailingTestsCount> — yalnız CI
```

**Go (interface enjeksiyonu — retry yok, determinism zorunlu):**

```go
type Saat interface { Now() time.Time }              // determinism seam (§5.2)

func TestTtlSiniriAsmaz(t *testing.T) {
    sabit := sabitSaat{t: time.Unix(1_700_000_000, 0)}
    komut := YeniKomut(db, sabit)                    // Arrange
    if got := komut.Ttl(60); got != 1_700_000_060 {  // Assert — retry yok: düzelt kaynağı
        t.Fatalf("Ttl = %d, want 1700000060", got)
    }
}
```

**C++ (GTest repeat — tekrar oranı ölçümü):**

```cpp
TEST(FlakyTarama, TtlSiniriAsmaz) {
    auto sabit = SabitClock{std::chrono::seconds{1'700'000'000}};  // determinism
    ApiKeyKomut komut(db, sabit);                                  // Arrange
    EXPECT_EQ(komut.Ttl(60), 1'700'000'060);                       // Assert
}
// ./testler --gtest_repeat=100 --gtest_brief=1 → 100/100 = flaky değil (§4.3 ölçüm)
```

**Rust (proptest — property-based determinism):**

```rust
#[test]
fn ttl_siniri_asmaz() {
    let sabit = SabitSaat::new(1_700_000_000);              // determinism
    let komut = Komut::yeni(db, sabit);                      // Arrange
    assert_eq!(komut.ttl(60), 1_700_000_060);                // Assert
}
// proptest: rastgele saniye aralığında sınır asla aşılmaz (§4.3 örneklem genişletme)
```

**Ruby (Timecop + rspec-retry):**

```ruby
it 'TTL sınırı asmaz', retry: 2 do        # retry: yalnız CI — §7.3 maskesi
  Timecop.freeze(Time.at(1_700_000_000)) do    # Arrange: determinism
    expect(ttl_hesapla(60)).to eq(1_700_000_060)
  end
end
```

**Kotlin (sabit Clock):**

```kotlin
@Test
fun `ttl siniri asmaz`() {
    val sabit = Clock.fixed(Instant.ofEpochSecond(1_700_000_000), ZoneOffset.UTC)
    val komut = ApiKeyCreateCommand(db, sabit)            // Arrange: determinism
    assertEquals(1_700_000_060L, komut.calistir(60))      // Assert
}
```

**Swift (XCTRepeat — tekrar oranı ölçümü):**

```swift
func testTtlSiniriAsmaz() {
    let sabit = SabitTarih(unix: 1_700_000_000)           // determinism
    let komut = ApiKeyKomut(db: db, saat: sabit)          // Arrange
    XCTAssertEqual(komut.ttl(60), 1_700_000_060)          // Assert
}
// XCTRepeat { testTtlSiniriAsmaz() } — N tekrar = flaky ölçümü (§4.3)
```

**C (fonksiyon pointer enjeksiyonu):**

```c
static time_t sabit_zaman(time_t *t) { if (t) *t = 1700000000; return 1700000000; }

void test_ttl_siniri_asmaz(void) {
    komut_t k; k.zaman = sabit_zaman;                    /* Arrange: determinism */
    TEST_ASSERT_EQUAL_INT64(1700000060, komut_ttl(&k, 60)); /* Assert */
}
/* Flaky ölçümü: koşum betiği testi 100x koşar, geçiş sayısını loglar (§4.3) */
```

## §12 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Flaky testi silmek | Kök neden kaybolur | Karantina kaydı denetimi |
| 2 | Retry artırarak kararlılık | Maskeleme (§7.3) | Retry değişikliği incelemesi |
| 3 | Karantinasız `continue-on-error` | Sahte yeşil | Kapı denetimi (§9.3) |
| 4 | Sabit `sleep` ile senkronlama | Yavaş + yarış | `waitForTimeout` taraması |
| 5 | Gerekçesiz `markTestSkipped` | Kör skip | Kod incelemesi |
| 6 | Koşula bağlı assertion | Sessiz geçiş | `if` içinde assertion taraması |
| 7 | Karantinayı süresiz bırakmak | Test çürür | §8.4 süresi denetimi |
| 8 | Karantinadaki testi tamamen atlamak | Ölçüm kaybı | §8.4 kural 5 |
| 9 | Retry'ı ölçmeden etkinleştirmek | Ölçüm devre dışı | §7.2 kural 3 |
| 10 | Ortam bağımlı testi yeşil saymak | Taşınabilirlik kaybı | §6.4 denetimi |

## §13 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K019-testing/regression | Flaky tanımı | Karantina seçime etkisi | §4-§8 | uyumlu |
| 2 | K019-testing/e2e | Bekleyiş yasağı | Kararlı E2E kalıbı | §6.2 | uyumlu |
| 3 | K019-testing/coverage | Skip etkisi | Kapsam dalgalanması | §5.4 | ayrık rapor |
| 4 | K019-testing/performance | Süre bütçesi | Timeout değerleri | §6.3 | uyumlu |
| 5 | K019-testing/integration | Ortam bağımlılığı | Çift taraflı stub kuralı | §5.1 | uyumlu |
| 6 | K018-devops/pipeline | Kapı sertliği | `continue-on-error` kararı | ci.yml Satır 167/183 | açık |
| 7 | K017-observability/alerting | Karantina/tekrar sinyali | Uyarı | rapor akışı | ⚠️ VERIFICATION REQUIRED |
| 8 | K013-integraion | Dış kaynaklı flaky (ağ) | Sınıf etiketi (§16) | dış çağrı stub'ı | tasarım iddiası |
| 9 | K0100-identitiy | Auth testleri | Karantina girdisi | test kimliği | ayrık |

## §14 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Tekrar ölçümü yok | Flaky görünmez | yüksek | §4.3 ölçüm hattı | ⚠️ VERIFICATION REQUIRED |
| 2 | Karantina listesi yok | Kararlar dosyada kalır | yüksek | §8.3 manifest | ⚠️ VERIFICATION REQUIRED |
| 3 | PHP retry tanımsız | Farklı davranım | orta | §7.1 karar | ⚠️ VERIFICATION REQUIRED |
| 4 | Vitest retry yok | Tek koşum kırılganlığı | orta | §7.1 karar | ⚠️ VERIFICATION REQUIRED |
| 5 | E2E soft gate | Kararsızlık akmaya devam eder | orta | Sertleştirme kararı | açık (`regression/`) |
| 6 | Sabit bekleyiş (3 yer) | Zamanlama flaky | orta | §6.2 temizliği | kayıt altında |
| 7 | Koşula bağlı assertion | Kör geçiş | yüksek | §5.1 kural 7 | kayıt altında |
| 8 | Skip oranı bilinmiyor | Kapsam sahte yüksek olabilir | orta | Skip raporu | ⚠️ VERIFICATION REQUIRED |
| 9 | Süre assertion'ı (2500 ms) | Makine hızına bağlı | orta | Bütçe gözden geçirme | kayıt altında |

## §15 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Ölçüm | ≥ 10 örneklem / test | Koşum raporu | her sürüm | §4.3 |
| 2 | Eşik | Tekrar oranı %1 | Karantina adayı listesi | her sürüm | §4.2 E1 |
| 3 | Retry | E2E ≤ 2 | config Satır 20 | her PR | §7.1 |
| 4 | Karantina | Kayıt 10/10 alan | Manifest denetimi | girişte | §8.2 |
| 5 | Çıkış | ≥ 10 koşum %0 | Release gate | çıkışta | §9.2 |
| 6 | Determinism | `sleep` taraması = 0 yeni | Kod incelemesi | her PR | §6.2 |
| 7 | Skip | Gerekçesiz skip = 0 | Kod incelemesi | her PR | §5.4 |
| 8 | Kapı | Soft kapı sayısı azalıyor | ci.yml denetimi | çeyrek | §9.3 |

## §16 Flaky sınıflandırma matrisi (kök neden)

| # | Sınıf | Tipik belirti | Repo örneği | Düzeltme |
|---|---|---|---|---|
| 1 | Zaman (clock) | Saniye kavisinde değişen sonuç | ApiKeyCreateCommandTest Satır 107 payı | Pay veya saat enjeksiyonu |
| 2 | Bekleyiş (timing) | Makine hızına bağlı | player-info.spec.ts Satır 375 | Olay tabanlı bekleme |
| 3 | Sıra (order) | Yalnız belirli sırada geçer | Global `$_SERVER` kullanımı | Kur/sök |
| 4 | Ortam (env) | Ortamda değişen sonuç | LoggerRedactionTest Satır 170 | Şartlı kurgu / konteyner |
| 5 | Paylaşılan kaynak | Paralelde çakışma | Port / dosya adı | Benzersiz ad |
| 6 | Ağ / dosya | Dış kaynağa bağlı | dış sunucu (E2E webServer) | Stub / çift taraflı |
| 7 | Kaynak (CPU/bellek) | Yük altında timeout | `waitForTimeout` | Bütçe revizyonu |
| 8 | Bilinmiyor | Sınıflandırılamıyor | — | Sınıf `bilinmiyor` olarak kaydedilir; **tahmin edilmez** |

## §17 Karantina kayıt örneği (doldurulabilir kalıp)

| Alan | Değer |
|---|---|
| Test kimliği | `<dosya> :: <test adı>` |
| Giriş tarihi | ISO 8601 tarih |
| Tetikleyici | T1 / T2 / T3 / T4 |
| Tekrar oranı | `<tutarsız> / <örneklem>` |
| Sınıf | §16 sınıfı (1-8) |
| Kök neden | tek cümlelik not |
| Sorumlu | rol |
| Çıkış koşulu | §9.2 beş şartı |
| Son tarih | tarih |
| Kapanış kanıtı | `<0 hata> / <örneklem>` koşum kaydı |

## §18 Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Playwright test timeouts sayfası, test ve beklenti timeout değerlerinin nasıl ayarlandığını tanımlar | https://playwright.dev/docs/test-timeouts | 2026-10-13 | HTTP 200 doğrulandı |
| Playwright test projects sayfası, koşum birimlerinin (proje) yapılandırmasını tanımlar | https://playwright.dev/docs/test-projects | 2026-10-13 | HTTP 200 doğrulandı |
| Playwright reporters sayfası, sonuç raporlayıcılarını (list/json/html) tanımlar | https://playwright.dev/docs/test-reporters | 2026-10-13 | HTTP 200 doğrulandı |
| Playwright CLI sayfası, `--repeat-each` ve `--retries` gibi komut satırı bayraklarını listeler | https://playwright.dev/docs/test-cli#test-command-line | 2026-10-13 | HTTP 200 doğrulandı |
| Playwright release notes sayfası, sürüm bazlı davranış değişikliklerinin kaydıdır | https://playwright.dev/docs/release-notes | 2026-10-13 | HTTP 200 doğrulandı |
| Playwright test fixtures sayfası, testlerin paylaştığı kurulum/söküm (fixture) mekanizmasını tanımlar | https://playwright.dev/docs/test-fixtures | 2026-10-13 | HTTP 200 doğrulandı |
| Playwright test configuration sayfası, config içindeki timeout/retry alanlarını tanımlar | https://playwright.dev/docs/test-configuration | 2026-10-13 | HTTP 200 doğrulandı |
| Vitest config retry alanı, test tekrar (retry) seçeneğini yapılandırmaya izin eder | https://vitest.dev/config/#retry | 2026-10-13 | HTTP 200 doğrulandı |
| Vitest config testTimeout alanı, test başına varsayılan timeout değerini tanımlar | https://vitest.dev/config/#testtimeout | 2026-10-13 | HTTP 200 doğrulandı |
| Vitest CLI sayfası, `vitest run` komutunun bayraklarını listeler | https://vitest.dev/guide/cli | 2026-10-13 | HTTP 200 doğrulandı |
| Vitest migration sayfası, sürüm geçişlerinde test davranışı farklarını açıklar | https://vitest.dev/guide/migration | 2026-10-13 | HTTP 200 doğrulandı |
| Vitest `vi` API sayfası, test yardımcıları (mock/sahte zaman) için API'yi tanımlar | https://vitest.dev/api/vi | 2026-10-13 | HTTP 200 doğrulandı |
| Vitest mocking sayfası, bağımlılık sahteleme (mocking) kalıplarını tanımlar | https://vitest.dev/guide/mocking | 2026-10-13 | HTTP 200 doğrulandı |
| PHPUnit assertions sayfası, assertion API ailesini (delta dahil) tanımlar | https://docs.phpunit.de/en/10.5/assertions.html | 2026-10-13 | HTTP 200 doğrulandı |
| PHPUnit 10.5 belgeleri, test çalıştırma ve yapılandırma seçeneklerini tanımlar | https://docs.phpunit.de/en/10.5/ | 2026-10-13 | HTTP 200 doğrulandı |
| PHPUnit 11.0 belgeleri, sürüm bazlı davranış ve bayrak değişikliklerini listeler | https://docs.phpunit.de/en/11.0/ | 2026-10-13 | HTTP 200 doğrulandı |
| Microsoft Learn test alanı, pipeline test aşamaları ve sonuç raporlama seçeneklerini tanımlar | https://learn.microsoft.com/en-us/azure/devops/pipelines/test/ | 2026-10-13 | HTTP 200 doğrulandı |
| Microsoft Learn ASP.NET entegrasyon testleri sayfası, entegrasyon testi ortamı ve güvenilirlik önerilerini açıklar | https://learn.microsoft.com/en-us/aspnet/core/test/integration-tests | 2026-10-13 | HTTP 200 doğrulandı |
| MDN `Date.now()` sayfası, sayısal zaman damgası (epoch ms) üretiminin tanımıdır | https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now | 2026-10-13 | HTTP 200 doğrulandı |
| MDN `Date.prototype.toISOString()` sayfası, ISO 8601 biçiminde tarih üretiminin tanımıdır | https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/toISOString | 2026-10-13 | HTTP 200 doğrulandı |
| IETF RFC 2119, şart derecelendirme kelimelerinin (MUST/SHOULD) tanımıdır | https://datatracker.ietf.org/doc/html/rfc2119 | 2026-10-13 | HTTP 200 doğrulandı |
| IETF RFC 3339, tarih ve saat internet profili (ISO 8601 uyumlu) tanımıdır | https://datatracker.ietf.org/doc/html/rfc3339 | 2026-10-13 | HTTP 200 doğrulandı |

**Not:** 22 URL `.ai/architecture/**` envanterinde (1441 benzersiz URL) bulunmaz;
başka bir bu görevde üretilen dosyayla da kesişmez (kesişim 0).

## §19 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1441 benzersiz URL (temel ölçüm) | 2026-10-13 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest -Method Head` | 22/22 HTTP 200 | 2026-10-13 |
| 3 | Envantere kesişim | küme karşılaştırması | 0 | 2026-10-13 |
| 4 | Repo satırları | `Select-Object -Index (N-1)` | 28 kanıt satırı doğrulandı | 2026-10-13 |
| 5 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-13 |
| 6 | Mojibake | UTF-8 okuma | 0 | 2026-10-13 |
| 7 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-13 |
| 8 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-13 |
| 9 | `parent:` alanı | frontmatter taraması | 0 (yalnız `parent_id`) | 2026-10-13 |
| 10 | group alanı | frontmatter | `mimari` (yazılmış, tırnaksız) | 2026-10-13 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode