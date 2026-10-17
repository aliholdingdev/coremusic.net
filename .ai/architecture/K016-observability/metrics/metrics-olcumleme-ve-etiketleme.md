---
title: "K016-observability/metrics — Ölçümleme ve Etiketleme Kuralları"
node_id: K016-observability/metrics/metrics-olcumleme-ve-etiketleme
node_type: module
status: draft
parent_id: K016-observability/metrics
children: []
depends_on:
  - K016-observability
  - K016-observability/metrics
evidence:
  - "shared/src/Middleware/MiddlewarePipeline.php — ReadAllLines 54 satır, hrtime ölçümü satır 42-48 (2026-10-12)"
  - "shared/src/Security/CacheRateLimiter.php — ReadAllLines 41 satır, increment satır 26 (2026-10-12)"
  - "shared/src/Api/Middleware/RateLimitMiddleware.php — ReadAllLines 72 satır, sabitler satır 22-23 (2026-10-12)"
  - "shared/src/Api/Registry/ServiceHealth.php — ReadAllLines 100 satır, sayım satır 44-50 (2026-10-12)"
  - "grep 'metric|histogram|gauge|prometheus|counter|percentile' shared/src/**/*.php — yalnız AIEngine::predictFault(array \$metrics) isabeti (2026-10-12)"
validation:
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri: 1081 benzersiz URL ölçülüp kopyalanmadı (2026-10-12)"
  - "Metrik toplayıcı (collector/exporter) repo'da YOK — glob ile doğrulandı (2026-10-12)"
open_questions:
  - "Metrik toplayıcı seçimi (Prometheus exporter vs yapılandırılmış log) henüz karara bağlı değil (⚠️ VERIFICATION REQUIRED)"
  - "Etiket kümesi ve cardinality üst sınırı tanımlı değil (⚠️ VERIFICATION REQUIRED)"
  - "Ölçüm noktalarının hangi frekansta örneklenacağı bilinmiyor (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-observability/metrics/metrics-olcumleme-ve-etiketleme

> **ELI10:** Log "ne oldu" der, metrik "ne kadar" der. Bu dosya hangi olayın sayılacağını, hangi
> etiketlerle gruplanacağını ve hangi etiketlerin tabloyu patlatacağını belirler. Ölçüm noktaları
> önceden sabitlenir; metrik üretimi sonradan eklenen bir düşünce değildir.

## §1 Amaç

Bu modül, CoreMusic metrik modelinin tek yetkili tanımıdır. Üç karar içerir: **tip seçimi**
(counter/gauge/histogram), **etiket kümesi** (cardinality bütçesi) ve **adlandırma** (etiket
isimlendirme kuralı). Yanlış tip seçimi geri dönülemez veri kaybına yol açar: sayım yerine
"gauge" yazılırsa geçmiş değeri yeniden kurmak mümkün olmaz.

| # | Bu dosyanın cevapladığı soru | Karar | Nerede uygulanır |
|---|---|---|---|
| 1 | Bu olay artan mı, anlık mı, dağılım mı? | Tip seçimi | Ölçüm noktası tanımı |
| 2 | Hangi boyutta gruplanır? | Etiket kümesi | Etiket listesi |
| 3 | Kaç farklı seri kabul edilebilir? | Cardinality bütçesi | Üretim kodu incelemesi |
| 4 | Metrik adı nasıl yazılır? | Adlandırma kuralı | `namespace_subsystem_name_unit` |
| 5 | Percentil nasıl hesaplanır? | Histogram bucket | Bucket kenarları |
| 6 | Ne sıklıkla örneklenir? | Örnekleme periyodu | Toplayıcı yapılandırması |
| 7 | Hangi metrik uyarı üretir? | Eşik eşlemesi | `alerting/` |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Toplam etiketli seri sayısı | adet | ⚠️ üst sınır tanımlı değil | açık |
| H2 | Bir metrikteki etiket anahtarı sayısı | adet | en fazla 5 önerilir | tasarım önerisi |
| H3 | Etiket değerinde değişken (user id, path) kullanımı | adet | 0 | henüz üretim yok |
| H4 | Ölçüm noktasının audio callback içinde olmaması | kural | zorunlu | L0 kuralı |
| H5 | Ölçüm hattının tahsis yapmaması | kural | zorunlu | L0 kuralı |
| H6 | Histogram bucket sayısı | adet | 12-30 arası | tasarım önerisi |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Metrik tipi seçimi | ✅ Dört tip ve karar ağacı | ❌ Toplayıcı kurulumu → K018-devops |
| Etiket kümesi | ✅ İzinli/banned etiket listesi | ❌ Dashboard tasarımı → ayrı konu |
| Cardinality | ✅ Bütçe kuralı ve hesaplaması | ❌ Depolama kapasitesi → K018-devops |
| Adlandırma | ✅ `namespace_subsystem_name_unit` | ❌ Etiket dili çevirisi |
| Percentil | ✅ Histogram ile hesap kuralı | ❌ Özet (summary) dağıtımı |
| Uyarı eşiği | ✅ Metrikten türeyen eşik adayı | ❌ Uyarı kuralı ve kanalı → `alerting/` |
| Süre ölçümü | ✅ `hrtime` tabanlı süre örneği | ❌ Ses gecikme ölçümü → `performance/` + K015 |
| Repo kanıtı | ✅ Mevcut üç ölçüm örneği | ❌ Yeni metrik kodu yazımı → onay ile |

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılık | Tanım | Bu dosyadaki karşılığı |
|---|---|---|---|
| Sayım | counter | Yalnız artan (artı sıfırlanabilir) değer | `increment()` çağrıları |
| Gösterge | gauge | Yukarı/aşağı hareket eden anlık değer | `ServiceHealth` sayaçları |
| Histogram | histogram | Değer dağılımı + bucket sayaçları | Süre ölçümleri |
| Özet | summary | Client tarafında percentil hesabı | Seçilmedi (gerekçe §7.4) |
| Etiket | label | Seriyi ayıran anahtar-değer | `module`, `event` |
| Seri | time series | Ad + etiket kümesi benzersiz tanımlar | Cardinality birimi |
| Cardinality | cardinality | Bir metrikteki farklı seri sayısı | Bütçe kuralı §5 |
| Bucket | bucket | Histogram aralık sınırı | §7.2 |
| Percentil | percentile | Dağılımın sıralı konumu (p50, p95, p99) | §7.3 |
| Örnekleme | sampling | Toplama sıklığı | Toplayıcı periyodu |
| Toplayıcı | collector | Ölçümü toplayan bileşen | Repo'da yok |
| Seri etiketi | series label | Zaman içinde değişmeyen etiket | Kural §5.1 |
| Olay etiketi | event label | Olaya bağlı ama sınırlı etiket | Kural §5.2 |
| Ölçüm noktası | instrumentation point | Kodda ölçüm yazılan yer | §8 |
| Adlandırma | naming | Metrik adı deseni | §6 |
| Eşik | threshold | Uyarı tetikleyen metrik değeri | `alerting/` |
| Exposition | exposition | Metriğin dışarı sunulması | §12 |

## §4 Metrik tipleri ve karar ağacı

### §4.1 Dört temel tip

| # | Tip | Hareket | Örnek soru | Repo karşılığı | Karar |
|---|---|---|---|---|---|
| 1 | counter | yalnız artan | Kaç istek işlendi? | `CacheRateLimiter::increment()` | ✅ kullanılır |
| 2 | gauge | iki yönlü | Kaç servis sağlıklı? | `ServiceHealth` sayıları | ✅ kullanılır |
| 3 | histogram | dağılım | Süre ne kadar? | `MiddlewarePipeline` süre örneği | ⚠️ veri üretiliyor, depolanmıyor |
| 4 | summary | client percentile | p95 nedir? | repo'da karşılığı yok | ❌ seçilmedi |

### §4.2 Karar ağacı

| # | Soru | Evet | Hayır | Çıktı tip |
|---|---|---|---|---|
| 1 | Değer zamanla azalabilir mi? | → 2 | → 1 | gauge / counter |
| 2 | Değerin sıfırlanması beklenir mi? | → 1 (reset) | → 1 | counter |
| 3 | Değerin dağılımı gerekli mi? | → 3 | → 1 veya 2 | histogram |
| 4 | Yüzdelikler client tarafında mı hesaplanacak? | → 4 | → 3 | summary / histogram |
| 5 | Değer örnekleme ile mi elde edilir? | → 2 | → 1 veya 3 | gauge / counter / histogram |
| 6 | Değer süre (ms) cinsinden mi? | → 3 | devam | histogram |
| 7 | Değer hacim (byte) cinsinden mi? | → 3 veya 2 | devam | histogram / gauge |
| 8 | Sadece "oldu/olmadı" mı? | → 1 | → 3 | counter |

### §4.3 Sık yapılan yanlışlar

| # | Yanlış | Doğru | Sonuç |
|---|---|---|---|
| 1 | Sayaç olarak gauge kullanmak | counter | Artış grafiği kırılır |
| 2 | Etikette başarısızlık nedenini taşımak | Ayrı counter | Cardinality patlar |
| 3 | Percentili counter'dan hesaplamak | histogram | Hesaplanamaz |
| 4 | Sayaç değerini logdan okumak | Toplayıcı | Gecikme |
| 5 | Histogram yerine tek gauge ile süre izlemek | histogram | Dağılım kaybolur |
| 6 | Etiketi istek gövdesinden doldurmak | Sabit küme | Saldırgan cardinality enjeksiyonu |
| 7 | Ölçümü `finally` bloğu dışında bırakmak | `finally` benzeri yerleşim | Hata yolunda ölçüm kaybı |
| 8 | Sayaçı sıfırlayan kod yazmak | Restart davranışı | Tarihi veri bozulur |

## §5 Etiketleme ve cardinality

### §5.1 İzinli etiket anahtarları

Etiket anahtarı, seriyi ayıran bileşendir. Anahtar sayısı ve değer varyansı birlikte cardinality
belirler. Aşağıdaki liste bu dosyanın **beyaz listesidir**; dışına çıkılmaz.

| # | Anahtar | İzinli değer aralığı | Tahmini seri | Kaynak |
|---|---|---|---|---|
| 1 | `method` | HTTP yöntemleri (sınırlı küme) | en fazla 8 | İstek katmanı |
| 2 | `status_class` | 2xx, 3xx, 4xx, 5xx (4 değer) | 4 | Yanıt katmanı |
| 3 | `route_pattern` | Kayıtlı route şablonu (sınırlı) | RouteTable kayıt sayısı | `RouteTable` |
| 4 | `module` | Kod modülü adı (sınırlı) | onlarca | Kod sabiti |
| 5 | `event` | Olay adı (sınırlı) | yüzlerce | Kod sabiti |
| 6 | `level` | PSR-3 sekiz seviye | 8 | `FileHandler::LEVELS` |
| 7 | `target` | Hedef servis adı (kayıtlı) | ServiceRegistry kaydı | `ServiceRegistry` |
| 8 | `outcome` | `ok` / `fail` (2 değer) | 2 | Ölçüm noktası |

### §5.2 Yasaklı etiket anahtarları

| # | Yasaklı anahtar | Neden | Karşılığı |
|---|---|---|---|
| 1 | `user_id` | Tekil kimlik → cardinality patlar | Yanlışlık düzeltilemez, müşteriye özel seri |
| 2 | `session_id` | Tekil kimlik | Aynı neden |
| 3 | `request_id` / `traceId` | Tekil kimlik | Bu bilgi log/trace içindedir |
| 4 | `ip` | Yüksek varyans | Üstel dağılım |
| 5 | `url` (ham) | Sınırsız varyans | `route_pattern` kullanın |
| 6 | `query_string` | Sınırsız varyans | `route_pattern` kullanın |
| 7 | `email` | PII + varyans | Yasak (KVKK) |
| 8 | `api_key` | Gizli + varyans | Yasak (sızıntı) |
| 9 | `error_message` (ham) | Hata metni sınırsız | `error_class` sabiti kullanın |
| 10 | `timestamp` | Her örnek farklı | Zaten eksen |

### §5.3 Cardinality bütçesi

Bütçe, sistemin toplam seri sayısına tavan koyar. Hesap, çarpım kuralıyla yapılır.

| # | Bileşen | Tahmini değer | Kaynak | Not |
|---|---|---|---|---|
| 1 | `method` serisi | 8 | Sınırlı küme | Üst sınır |
| 2 | `status_class` serisi | 4 | Sınırlı küme | Üst sınır |
| 3 | `route_pattern` serisi | RouteTable kayıt sayısı | Repo | Ölçüldü |
| 4 | `module` serisi | onlarca | Kod | Sınırlı |
| 5 | `event` serisi | yüzlerce | Kod | Sınırlı |
| 6 | Kombine üst sınır | Çarpım | Hesap | ⚠️ gerçek sayı bilinmiyor |
| 7 | Onaylı toplam bütçe | ⚠️ tanımlı değil | — | Açık soru |

**Çarpım uyarısı:** dört anahtarın 8 × 4 × 40 × 50 değeri tek başına 64.000 seri eder. Bu yüzden
anahtarlar **bir metrikte bir arada kullanılmaz**; her metrik kendi alt kümesini alır.

## §6 Adlandırma kuralı

| # | Kural | Şekil | Örnek | Karşı örnek |
|---|---|---|---|---|
| 1 | Ad dört parçadır | `namespace_subsystem_name_unit` | `http_server_request_duration_ms` | `reqTime` |
| 2 | Küçük harf + alt çizgi | `snake_case` | `auth_login_attempts_total` | `authLoginAttempts` |
| 3 | Sayımlar `_total` ile biter | suffix | `auth_rate_limited_total` | `auth_rate_limited` |
| 4 | Sistem birimi ad sonunda | `_ms`, `_bytes`, `_ratio` | `cache_hit_ratio` | `cacheHit` |
| 5 | Oranlar 0-1 aralığındadır | `_ratio` | `error_ratio` | `error_percent` |
| 6 | Anlık durum `state` sözcüğü | suffix | `service_health_state` | `serviceHealth` |
| 7 | Etiket adı da `snake_case` | etiket | `status_class` | `statusClass` |
| 8 | Yeni ad = yeni metrik | değişmezlik | — | Aynı adla davranış değişimi |

## §7 Histogram ve percentil

### §7.1 Neden histogram

| Kriter | Histogram | Özet (summary) |
|---|---|---|
| Cluster geneli sorgu | ✅ mümkün | ❌ client başına hesap |
| Bucket kenarı değişimi | ✅ veri yeniden kurulur | ❌ geriye dönük düzeltilemez |
| Client kaynak kullanımı | orta | düşük |
| Percentil doğruluğu | bucket içi yaklaşım | client'ta tam |
| Ölçüm noktası kodu | ek sayımlar | ek sayımlar |

### §7.2 Bucket seçimi

| # | Kural | Gerekçe |
|---|---|---|
| B1 | Kenarlar logaritmik artar | Hem düşük hem yüksek kuyruklar görünür |
| B2 | İş hedefi değeri bir kenara hizalanır | p95 doğrudan okunur |
| B3 | En üst kenar, en kötü kabul edilen süreden büyüktür | Taşma görünür |
| B4 | Kenar sayısı 12-30 arası | Bellek ve doğruluk dengesi |
| B5 | Kenarlar birimle yazılır (`_ms`) | Karışıklık önlenir |
| B6 | Sıfır altı değer yok | Süre negatif olamaz |

### §7.3 Percentil kuralı

| # | Kural | Gerekçe |
|---|---|---|
| P1 | p50 merkezi eğilim | Ortalama yerine medyan |
| P2 | p95 kuyruk hedefi | Kullanıcı etkisi |
| P3 | p99 nadir ama maliyetli olay | Kaynak tıkanması |
| P4 | p100 (en kötü) ayrı olarak izlenir | Bucket üst sınırı |
| P5 | Toplama **karıştırılmadan** yapılır | Bucket'lar birleştirilemez |
| P6 | Sayımlar toplanır, percentiller ayrıca hesaplanır | Doğruluk |

### §7.4 Özet (summary) neden seçilmedi

| # | Gerekçe | Sonuç |
|---|---|---|
| 1 | Cluster geneli sorgu gerekli | histogram seçildi |
| 2 | Depolama maliyeti karşılaştırması yapılmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | Toplayıcı seçimi yapılmadı | ⚠️ VERIFICATION REQUIRED |

## §8 Ölçüm noktaları — CoreMusic

### §8.1 Mevcut üç ölçüm örneği (repo kanıtı)

| # | Ölçüm | Kod karşılığı | Tip karşılığı | Durum |
|---|---|---|---|---|
| 1 | Ara katman süresi | `hrtime(true)` farkı | histogram adayı | ölçülüyor, loga yazılıyor |
| 2 | Sınır aşım sayacı | `increment()` | counter | sayaç çalışıyor |
| 3 | Servis sağlık durumu | `match` sayaçları | gauge | anlık durum |
| 4 | Sınır penceresi değeri | `DEFAULT_WINDOW_SECONDS` | gauge adayı | sabit |
| 5 | İstek sayacı | repo'da yok | counter | ⚠️ ölçülmüyor |
| 6 | Hata oranı | repo'da yok | counter | ⚠️ ölçülmüyor |
| 7 | Sürüm bilgisi | repo'da yok | gauge | ⚠️ ölçülmüyor |
| 8 | Çalışma süresi (uptime) | repo'da yok | gauge | ⚠️ ölçülmüyor |

### §8.2 Ölçüm noktası yerleşim kuralları

| # | Kural | Neden | İstisna |
|---|---|---|---|
| N1 | Ölçüm, middleware sınırında yapılır | Tek noktadan görünürlük | Yok |
| N2 | `hrtime` kullanılır, saat (`time()`) değil | Çözünürlük | Uzun süreler hariç |
| N3 | Ölçüm, hata yolunda da çalışır | `finally` benzeri yerleşim | Yok |
| N4 | Audio callback içinde metrik yazılmaz | Gerçek zamanlı kısıt | Yok |
| N5 | Ölçüm kodu tahsis yapmaz | Gerçek zamanlı kısıt | Yok |
| N6 | Ölçüm, dış çağrıdan önce/sonra sarılır | Gecikme ayrıştırması | Yok |
| N7 | Log ile metrik aynı anda zorunlu değil | Ayrı sorular | Yok |

### §8.3 Ölçüm noktası envanteri (planlanan)

| # | Nokta | Metrik | Tip | Etiketler |
|---|---|---|---|---|
| 1 | Middleware sarımı | `http_server_request_duration_ms` | histogram | `route_pattern`, `method`, `status_class` |
| 2 | Rate limit | `http_rate_limited_total` | counter | `route_pattern`, `outcome` |
| 3 | Servis kontrolü | `service_health_state` | gauge | `target` |
| 4 | Kimlik doğrulama | `auth_login_attempts_total` | counter | `outcome` |
| 5 | Önbellek | `cache_hit_total` | counter | `target` |
| 6 | Olay yayımı | `event_dispatch_duration_ms` | histogram | `module` |
| 7 | Log hattı | `log_records_total` | counter | `level` |
| 8 | Kanal | `process_uptime_seconds` | gauge | yok |

## §9 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor | Doğrulama |
|---|---|---|---|---|
| 1 | `shared/src/Middleware/MiddlewarePipeline.php` | 42 | `hrtime(true)` başlangıç ölçümü | Numaralı döküm |
| 2 | `shared/src/Middleware/MiddlewarePipeline.php` | 46 | Milisaniye dönüşümü | Numaralı döküm |
| 3 | `shared/src/Middleware/MiddlewarePipeline.php` | 48 | `timing()` yayını | Numaralı döküm |
| 4 | `shared/src/Security/CacheRateLimiter.php` | 16 | `isLimited()` eşik okuması | Numaralı döküm |
| 5 | `shared/src/Security/CacheRateLimiter.php` | 26 | `increment()` sayaç artırımı | Numaralı döküm |
| 6 | `shared/src/Security/CacheRateLimiter.php` | 33 | `cache->increment()` atomik artış | Numaralı döküm |
| 7 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 22 | `DEFAULT_MAX_REQUESTS = 60` | Numaralı döküm |
| 8 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 23 | `DEFAULT_WINDOW_SECONDS = 60` | Numaralı döküm |
| 9 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 39 | Eşik kontrolü | Numaralı döküm |
| 10 | `shared/src/Api/Middleware/RateLimitMiddleware.php` | 49 | Sayaç artırma çağrısı | Numaralı döküm |
| 11 | `shared/src/Api/Registry/ServiceHealth.php` | 44 | `match` ile durum sayımı | Numaralı döküm |
| 12 | `shared/src/Api/Registry/ServiceHealth.php` | 60 | Toplu durum çıktısı | Numaralı döküm |
| 13 | `shared/src/PageRouter/StructuredLogger.php` | 47 | `timing()` imzası | Numaralı döküm |
| 14 | `shared/src/PageRouter/StructuredLogger.php` | 51 | `round($durationMs, 3)` yuvarlama | Numaralı döküm |
| 15 | `shared/src/AI/AIEngine.php` | 161 | `predictFault(array $metrics)` — donanım metriği, uygulama metriği değil | `Select-String` eşleşti |

**Doğrulama komutu:**

```powershell
Get-Content 'C:\www\coremusic.net\shared\src\Middleware\MiddlewarePipeline.php' -Encoding UTF8 |
  Select-Object -Index 41
```

Bu komut 1-based satır 42'yi, yani `$start = hrtime(true);` satırını döndürür.

**Negatif kanıt (metrik altyapısının yokluğu):**

```powershell
Select-String -Path 'C:\www\coremusic.net\shared\src\**\*.php' -Pattern 'histogram|gauge|prometheus|percentile'
```

Sorgu yalnız `AIEngine::predictFault(array $metrics)` isabeti verdi; bu metodun parametresi donanım
ölçümüdür (sıcaklık, SNR, THD), uygulama metriği değildir. Yani repo'da **metrik toplayıcı yoktur**.

## §10 Entegrasyon ve bağımlılıklar

| # | Yön | Muhatap | Arayüz | Bu dosyanın verdiği |
|---|---|---|---|---|
| 1 | Giriş | `MiddlewarePipeline` | `hrtime` farkı | Süre örneği |
| 2 | Giriş | `CacheRateLimiter` | `increment()` | Sayım olayı |
| 3 | Giriş | `ServiceHealth` | Durum sayımı | Anlık sağlık |
| 4 | Çıkış | `StructuredLogger::timing()` | JSON kayıt | Ham ham veri |
| 5 | Çıkış | `alerting/` | Eşik eşlemesi | Uyarı adayı |
| 6 | Dikey | `logging/` | Aynı etiket adları | Tutarlılık |
| 7 | Dikey | `tracing/` | Aynı `route_pattern` | Köprü |
| 8 | Dikey | K018-devops | Toplayıcı kurulumu | Ortam |

## §11 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Toplayıcı seçimi yok | Metrik üretilse bile toplanamaz | yüksek | Seçim kararı | ⚠️ VERIFICATION REQUIRED |
| 2 | Cardinality bütçesi tanımsız | Bellek taşması | orta | Bütçe yazılacak | ⚠️ VERIFICATION REQUIRED |
| 3 | `route_pattern` etiketi RouteTable'a bağlı | Kayıt artınca seri artar | orta | Sayaç takibi | açık |
| 4 | Süre ölçümü yalnız loga gidiyor | Dağılım kaybı | yüksek | Histogram kararı | açık |
| 5 | Ölçüm noktaları ses callback'ine kayabilir | Gerçek zamanlı ihlâl | düşük | N4 kuralı | açık |
| 6 | `timing()` yuvarlaması p99 etkiler mi | 0.001 ms hassasiyet | düşük | Ölçülmedi | ⚠️ VERIFICATION REQUIRED |
| 7 | Zaman kaynağı (`hrtime`) taşması | Uzun çalışma süresi | düşük | `hrtime(true)` farkı kullanılıyor | az risk |
| 8 | Oran metriklerinin paydası sıfır | Bölme hatası | orta | Tanımlı değil | açık |

## §12 Ölçüm senaryoları — uçtan uca

Her satır bir iş sorusunu metrik modeline çevirir. "Soru" sütunu, metriğin varlık nedenidir; soru
yoksa metrik de yoktur.

| # | İş sorusu | Tip | Metrik | Etiketler | Uyarı adayı |
|---|---|---|---|---|---|
| 1 | İstek hacmi ne? | counter | `http_request_total` | `method`, `status_class` | hacim düşüşü |
| 2 | İstek ne kadar sürüyor? | histogram | `http_request_duration_ms` | `route_pattern`, `method` | p95 eşiği |
| 3 | Hata oranı ne? | counter | `http_error_total` | `status_class`, `route_pattern` | `error_ratio` |
| 4 | Kaç istek sınırlandı? | counter | `rate_limited_total` | `route_pattern` | artış hızı |
| 5 | Kaç kimlik denemesi başarısız? | counter | `auth_login_failed_total` | `outcome` | brute-force eşiği |
| 6 | Kaç servis sağlıklı? | gauge | `service_healthy_count` | yok | sıfırdan büyük |
| 7 | Servis durumu nedir? | gauge | `service_health_state` | `target` | 0 değeri |
| 8 | Önbellek isabet oranı? | ratio | `cache_hit_ratio` | `target` | alt eşik |
| 9 | Olay yayımı ne kadar sürüyor? | histogram | `event_dispatch_duration_ms` | `module` | p95 eşiği |
| 10 | Log hacmi ne? | counter | `log_records_total` | `level` | `error` artışı |
| 11 | Süreç ne kadar çalışıyor? | gauge | `process_uptime_seconds` | yok | sıfırlanma |
| 12 | Sürüm hangisi? | gauge | `build_info` | `version` | — |
| 13 | Kuyruk (varsa) kaç öğe? | gauge | `queue_depth` | `name` | üst eşik |
| 14 | Bağlantı havuzu dolu mu? | gauge | `db_pool_in_use` | `target` | üst eşik |
| 15 | Zaman kaynağı ne? | gauge | `clock_offset_ms` | yok | mutlak eşik |
| 16 | Disk ne kadar dolu? | gauge | `disk_used_ratio` | `mount` | üst eşik |

## §13 Etiket değeri kataloğu

İzinli anahtarların **değerleri** de sabittir; serbest dize kabul edilmez. Katalog, cardinality
bütçesinin kaynağıdır.

| # | Anahtar | İzinli değer | Adet | Değişir mi? |
|---|---|---|---|---|
| 1 | `method` | `GET` | 1 | kod sabiti |
| 2 | `method` | `POST` | 1 | kod sabiti |
| 3 | `method` | `PUT` | 1 | kod sabiti |
| 4 | `method` | `DELETE` | 1 | kod sabiti |
| 5 | `method` | `HEAD` | 1 | kod sabiti |
| 6 | `method` | `OPTIONS` | 1 | kod sabiti |
| 7 | `status_class` | `2xx` | 1 | yanıttan |
| 8 | `status_class` | `3xx` | 1 | yanıttan |
| 9 | `status_class` | `4xx` | 1 | yanıttan |
| 10 | `status_class` | `5xx` | 1 | yanıttan |
| 11 | `outcome` | `ok` | 1 | ölçüm noktasından |
| 12 | `outcome` | `fail` | 1 | ölçüm noktasından |
| 13 | `level` | PSR-3 sekiz ad | 8 | kod sabiti |
| 14 | `module` | Kod modülü adları | onlarca | kod sabiti |
| 15 | `event` | Olay adları | yüzlerce | kod sabiti |
| 16 | `route_pattern` | `RouteTable` kayıtları | kayıt sayısı | tabloya bağlı |
| 17 | `target` | `ServiceRegistry` kayıtları | kayıt sayısı | tabloya bağlı |
| 18 | `version` | Sürüm dizesi | bir elin parmakları | sürüm değişiminde |

**Değer kaydı kuralı:** `route_pattern` ve `target`, çalışma zamanında tablodan okunur; bu iki
anahtar için değer kümesi **dinamiktir** ve tablo büyüdükçe cardinality artar. Diğer anahtarların
değeri derleme anında bilinir.

## §14 Örnek sergileme (exposition) çıktısı

Aşağıdaki blok, bir ölçüm noktasının dışarı sunulma **şeklidir**; değerler temsilidir ve gerçek
üretim çıktısı değildir.

```text
# TYPE http_request_total counter
http_request_total{method="GET",status_class="2xx"} 14821
http_request_total{method="GET",status_class="4xx"} 213
http_request_total{method="POST",status_class="5xx"} 7
# TYPE http_request_duration_ms histogram
http_request_duration_ms_bucket{route_pattern="/api/v1/auth/me",method="GET",le="5"} 9021
http_request_duration_ms_bucket{route_pattern="/api/v1/auth/me",method="GET",le="25"} 14610
http_request_duration_ms_bucket{route_pattern="/api/v1/auth/me",method="GET",le="100"} 14818
http_request_duration_ms_bucket{route_pattern="/api/v1/auth/me",method="GET",le="+Inf"} 14821
http_request_duration_ms_sum{route_pattern="/api/v1/auth/me",method="GET"} 36112.5
http_request_duration_ms_count{route_pattern="/api/v1/auth/me",method="GET"} 14821
# TYPE service_healthy_count gauge
service_healthy_count 3
```

**Okuma kuralları:**

| # | Satır | Ne der | Neden önemli |
|---|---|---|---|
| 1 | `# TYPE … counter` | Tip beyanı | Toplayıcı tipi buradan öğrenir |
| 2 | `_bucket{…le="5"}` | 5 ms altındaki istek sayısı | Kümülatiftir |
| 3 | `_bucket{…le="+Inf"}` | Toplam örnek | `_count` ile eşit olmalı |
| 4 | `_sum` | Toplam süre | Ortalama = `_sum / _count` |
| 5 | `_count` | Örnek sayısı | Doğrulama anahtarı |
| 6 | `service_healthy_count 3` | Etiketsiz gauge | Anlık durum |

## §15 Metrik – uyarı eşlemesi

Bu tablo, `alerting/` altının hangi metriklere bağlanacağını şimdiden sabitler. Eşik değerleri
**tasarım adayıdır**; gerçek eşik ölçüm sonrası belirlenir.

| # | Metrik | Önerilen eşik | Süre penceresi | Öncelik | Eşik durumu |
|---|---|---|---|---|---|
| 1 | `http_error_ratio` | 0.01 | 5 dakika | yüksek | ⚠️ ölçülmedi |
| 2 | `http_request_duration_ms` p95 | 250 ms | 5 dakika | orta | ⚠️ ölçülmedi |
| 3 | `service_healthy_count` | 0 | 1 dakika | kritik | ⚠️ ölçülmedi |
| 4 | `rate_limited_total` artış hızı | ⚠️ tanımlı değil | 5 dakika | orta | ⚠️ ölçülmedi |
| 5 | `auth_login_failed_total` | ⚠️ tanımlı değil | 1 dakika | yüksek | ⚠️ ölçülmedi |
| 6 | `cache_hit_ratio` | 0.5 altı | 15 dakika | düşük | ⚠️ ölçülmedi |
| 7 | `process_uptime_seconds` sıfırlanması | 0 | — | orta | doğrudan okunur |
| 8 | `log_records_total{level="error"}` | ⚠️ tanımlı değil | 5 dakika | orta | ⚠️ ölçülmedi |
| 9 | `disk_used_ratio` | 0.9 | 15 dakika | orta | ⚠️ ölçülmedi |
| 10 | `event_dispatch_duration_ms` p95 | ⚠️ tanımlı değil | 5 dakika | düşük | ⚠️ ölçülmedi |

**Öncelik tanımı:**

| Öncelik | Müdahale süresi | Örnek |
|---|---|---|
| kritik | anında | servis yok |
| yüksek | 15 dakika içinde | hata oranı |
| orta | aynı iş günü | gecikme |
| düşük | planlı | önbellek oranı |

## §16 Cardinality hesabı — somut örnek

Hesap, **metrik başına** yapılır; metriklerin toplanması bütçeyi patlatır. Aşağıdaki örnek tek bir
metriği ele alır.

| # | Adım | Değer | Açıklama |
|---|---|---|---|
| 1 | `method` değeri | 6 | Sınırlı HTTP kümesi |
| 2 | `status_class` değeri | 4 | Dört sınıf |
| 3 | `route_pattern` değeri | RouteTable kayıt sayısı | Dinamik |
| 4 | Çarpım (1 × 2 × 3) | 6 × 4 × kayıt | Üst sınır |
| 5 | Örneğin 40 kayıt | 960 seri | Kabul edilebilir |
| 6 | `user_id` eklenirse | 960 × kullanıcı sayısı | Kabul edilemez |
| 7 | `ip` eklenirse | üstel büyüme | Kabul edilemez |
| 8 | Sonuç | 2-3 anahtar sınırı | Kural §5.1 |

**Bütçe kuralı:** bir metrik için izinli anahtar sayısı **en fazla 3'tür**. Dördüncü anahtar
gerekiyorsa ayrı bir metrik açılır. Bu kural, `route_pattern` gibi dinamik anahtarlar için
zorunludur.

## §17 Ölçüm doğrulama protokolü

Metrik eklemek kadar, metriğin doğru ölçtüğünü kanıtlamak da bu modülün sorumluluğudur.

| # | Adım | Yöntem | Kabul kriteri |
|---|---|---|---|
| 1 | Tip doğruluğu | Karar ağacı §4.2 ile karşılaştırma | Aynı tip |
| 2 | Adlandırma | §6 kuralları ile denetim | 8/8 kural |
| 3 | Etiket kümesi | §5.1 beyaz listesi ile karşılaştırma | 0 yabancı anahtar |
| 4 | Cardinality | §16 hesabı | 3 anahtar sınırı |
| 5 | Artış doğruluğu | İki örnekleme farkı | ≥ 0 (counter) |
| 6 | Histogram eşitliği | `_bucket` sonu = `_count` | eşitlik |
| 7 | Oran aralığı | 0 ≤ oran ≤ 1 | aralıkta |
| 8 | Sıfır payda | Tanımlı davranış | hata değil, tanımsız |
| 9 | Zaman kayması | Sistem saati ile karşılaştırma | ⚠️ ölçülmedi |
| 10 | Tahsis | Bellek ölçümü | 0 tahsis |

## §18 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Prometheus adlandırma uygulamaları, `namespace_subsystem_name_unit` desenini ve `_total` son ekini öne sürer | https://prometheus.io/docs/practices/naming/ | 2026-10-12 | HTTP 200 doğrulandı |
| Prometheus histogram rehberi, percentillerin toplanamayacağını ve toplanabilen hesapların toplanması gerektiğini vurgular | https://prometheus.io/docs/practices/histograms/ | 2026-10-12 | HTTP 200 doğrulandı |
| Prometheus ölçümleme uygulamaları, yüksek cardinality etiketlerinden kaçınmayı ve etiketleri önceden bilinir tutmayı öğütler | https://prometheus.io/docs/practices/instrumentation/ | 2026-10-12 | HTTP 200 doğrulandı |
| Prometheus sergileme biçimi (exposition format), metriklerin düz metin ikili nokta virgül ile sunulduğunu tanımlar | https://prometheus.io/docs/instrumenting/exposition_formats/ | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Bu dosyadaki dört URL `.ai/architecture/**` envanterinde mevcut değildir; ölçüm sırasıyla
yapılmıştır.

## §19 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1081 benzersiz URL | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest -Method Head` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | anahtar karşılaştırma | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | `Select-String` + numaralı döküm | 15 satır eşleşti | 2026-10-12 |
| 5 | Metrik altyapısı yokluğu | çoklu desen `Select-String` | yalnız 1 ilgisiz isabet | 2026-10-12 |
| 6 | Satır sayısı | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | metin taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | gözden geçirme | 0 | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
