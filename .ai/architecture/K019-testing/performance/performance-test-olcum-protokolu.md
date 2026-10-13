---
title: "K019-testing/performance — Performance Test Ölçüm Protokolü: Latency Yüzdelikleri, Yük Aşamaları ve Eşikler"
node_id: K019-testing/performance/performance-test-olcum-protokolu
node_type: module
status: draft
parent_id: K019-testing/performance
children: []
depends_on:
  - K019-testing
  - K019-testing/performance
evidence:
  - "assets.coremusic.net/playwright.config.ts — Satır 52 timeout 30*1000, Satır 54 expect timeout 5*1000, Satır 49 webServer timeout 120*1000 (2026-10-12)"
  - "assets.coremusic.net/playwright.config.ts — Satır 18 fullyParallel, Satır 21 workers (CI: 1) (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 145 timeout-minutes 10 (js-test), Satır 164 15 (js-coverage), Satır 181 20 (js-e2e) (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 157-158 Vitest unit, Satır 175-176 Vitest coverage, Satır 202-203 Playwright e2e (2026-10-12)"
  - "shared/composer.json — Satır 47 composer test scripti (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa başlığı ile döndü (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada çakışma 0 (2026-10-12)"
  - "Repo zaman/değer alıntıları dosyanın tam okunmasıyla doğrulandı (2026-10-12)"
open_questions:
  - "Depoda yük testi (load test) aracı tanımı yok — kütüphane seçimi yapılmadı (⚠️ VERIFICATION REQUIRED)"
  - "Gerçek ölçüm sonuçları (p50/p95 değerleri) depoda saklanmıyor — geçmiş ölçüm yok (UNKNOWN)"
  - "E2E süresi için yayımlanmış bir bütçe (ci.yml üst sınırı altında) tanımlı değil (⚠️ VERIFICATION REQUIRED)"
  - "performance/* test dosyası glob'u testMatch ile eşleşmiyor; Playwright kapsamı e2e spec'leriyle sınırlı (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K019-testing/performance/performance-test-olcum-protokolu

> **ELI10:** "Hızlı mı?" sorusu ölçülmeden yanıtlanamaz. Bu dosya, performans testinin nasıl
> kurulacağını, hangi sayının (yüzdelik) hangi soruyu yanıtladığını, testin hangi süre
> bütçelerine sığması gerektiğini ve eşiği aşan bir ölçümün kapıya (gate) nasıl bağlanacağını
> tanımlar. Ölçüm yoksa iddia yoktur.

## §1 Amaç

Bu modül, CoreMusic'in **performans ölçüm kurallarının tek yetkili tanımıdır**. Beş karar bu
dosyada verilir: ölçüm adımları · metrik seçimi (yüzdelik) · süre bütçesi · yük aşamaları ·
eşik kararı. Ölçüm protokolüne uymayan sayı, performans kanıtı sayılmaz.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Ölçüm nasıl baştan sona yapılır? | Ölçüm protokolü | §4 |
| 2 | Hangi sayı okunur? | Yüzdelik metrikleri | §5 |
| 3 | Süre nereye sığmalı? | Süre bütçesi | §6 |
| 4 | Yük nasıl verilir? | Yük aşamaları | §7 |
| 5 | Eşik kim koyar, nasıl denetlenir? | Eşik karar matrisi | §8 |
| 6 | Mevcut kapılar ne durumda? | Soft gate (A6/A7) | §8.3 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Test başına üst süre | ms | 30000 (playwright.config.ts Satır 52) | kodda sabit |
| H2 | Assertion bekleme üstü | ms | 5000 (playwright.config.ts Satır 54) | kodda sabit |
| H3 | webServer başlangıç üstü | ms | 120000 (playwright.config.ts Satır 49) | kodda sabit |
| H4 | CI job bütçesi (JS test) | dakika | 10 (ci.yml Satır 145) | kodda sabit |
| H5 | CI job bütçesi (E2E) | dakika | 20 (ci.yml Satır 181) | kodda sabit |
| H6 | Yük testi aracı | araç | depoda tanımı yok | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Latency ölçümü | ✅ Yüzdelik seçimi ve okuma | ❌ Birim test doğruluğu → `unit/` |
| Yük testi | ✅ Aşama tasarımı ve metrikler | ❌ Dağıtım kapasitesi → K018-devops |
| Süre bütçesi | ✅ Test/suite/job üst sınırları | ❌ Kod içi çalışma süresi optimizasyonu |
| Eşikler | ✅ Eşik belirleme ve kapıya bağlama | ❌ Kapsam yüzdesi eşiği → `coverage/` |
| Kararsız test | ✅ Performans kaynaklı titreşim | ❌ Yeniden deneme → `regression/` |
| Ölçüm altyapısı | ✅ CI bütçeleri + Playwright süreleri | ❌ Monitoring → K017-observability |
| Tarayıcı metrikleri | ✅ Yok (sınır) | ❌ Core Web Vitals → web kalite alanı |

**Sınır kararı:** bu dosya yalnızca **test çalıştırmasının ve test edilen sistemin süresini**
ölçer; üretim trafiği metrikleri (istek/saniye, canlı p95) `K017-observability/metrics`
kapsamındadır.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Latency | latency | İstek/çalışma süresi | §5 |
| Yüzdelik | percentile | p50, p90, p95, p99 dağılım noktası | §5.1 |
| Ortalama | mean | Toplam/sayı — dağılım gizler | §5.2 (yasak) |
| Bütçe | budget | Üst süre sınırı | §6 |
| Yük aşaması | load stage | Isınma, sabit, tepe, soğuma | §7 |
| Soak | soak test | Uzun süreli sabit yük | §7.3 |
| Eşik | threshold | Kabul/ret sınırı | §8 |
| Soft gate | soft gate | Görünür ama bloklamayan kapı | §8.3 (ci.yml Satır 167, 183) |
| Tekrar | repeat | Aynı testin tekrar çalıştırılması | test-cli (komut satırı) |
| Paralellik | parallelism | Testlerin eşzamanlı koşulması | test-parallel (Satır 18-21) |
| CI job bütçesi | job timeout | İş üst çalışma süresi | ci.yml Satır 145/164/181 |

## §4 Ölçüm protokolü

Ölçüm, yedi adımda ve **sabit sırayla** yapılır; adımlardan biri atlanırsa sayı kanıt sayılmaz.

| # | Adım | Ne yapılır | Çıktı | Zorunlu mu |
|---|---|---|---|---|
| 1 | Ortam sabitleme | Donanım/sürüm/paralellik yazılır | Ortam kaydı | evet |
| 2 | Isınma (warm-up) | İlk koşu atılır, ölçüme girmez | Isınma notu | evet |
| 3 | Tekrar (repeat) | Aynı ölçüm ≥ 5 kez koşulur | Ham liste | evet |
| 4 | Yüzdelik hesabı | p50/p90/p95/p99 çıkarılır | Yüzdelik tablosu | evet |
| 5 | Bütçe karşılaştırması | §6 üst sınırları ile karşılaştırılır | Aşım var/yok | evet |
| 6 | Eşik kararı | §8 matrisi ile değerlendirilir | Kabul/ret | evet |
| 7 | Kayıt | Tarih + ortam + ham sayılar yazılır | Ölçüm kaydı | evet |

### §4.1 Ortam sabitleme alanları

| # | Alan | Neden sabitlenir | Örnek değer (bu depo) | Kaynak |
|---|---|---|---|---|
| 1 | Paralellik (workers) | Eşzamanlılık süreyi değiştirir | CI'da 1 | playwright.config.ts Satır 21 |
| 2 | Tarayıcı projesi | Tarayıcılar farklı hızdadır | chromium | playwright.config.ts Satır 37 |
| 3 | Node sürümü | Çalışma süresi değişir | 26 | ci.yml Satır 151 |
| 4 | PHP sürümü | Çalışma süresi değişir | `PHP_VERSION` değişkeni | ci.yml Satır 190 |
| 5 | CI ortamı | Yerel ≠ CI | ubuntu-latest | ci.yml Satır 144 |

### §4.2 Isınma kuralı

| # | Kural | Neden | Uygulama | Durum |
|---|---|---|---|---|
| 1 | İlk koşu ölçüme girmez | Önbellek/dosya sistemi ısınır | Manuel ilk koşu | ⚠️ VERIFICATION REQUIRED |
| 2 | Art arda koşu arası bekleme | Isı etkisi | Protokolde tanımlı değil | ⚠️ VERIFICATION REQUIRED |
| 3 | CI koşumu ayrı ortamdır | Yerel ölçüm CI'a genellenmez | §4.1 satır 5 | kural |
| 4 | Paralel koşu ölçümü | Eşzamanlılık yarışır | Workers = 1 (CI) | Satır 21 |

### §4.3 Tekrar ve örnekleme

| # | Soru | Minimum | Neden | Not |
|---|---|---|---|---|
| 1 | Kaç tekrar? | ≥ 5 | Tek sayı gürültü üretir | §4 adım 3 |
| 2 | Kaç ortam? | ≥ 1 CI + 1 yerel | Genelleme | §4.1 |
| 3 | Aykırı değer (outlier) | Elle değil, kural ile | Önyargı | ⚠️ VERIFICATION REQUIRED |
| 4 | Ölçüm penceresi | Dakika ölçeği | Saniyelik ölçüm gürültülü | §7 |

## §5 Latency metrikleri ve yüzdelikler

### §5.1 Yüzdelik tablosu

| # | Metrik | Tanımı | Ne yanıtlar | Ne yanıtlamaz |
|---|---|---|---|---|
| 1 | p50 | Yarısı bundan hızlı | Tipik durum | Tepe yavaşlık |
| 2 | p90 | %10 bundan yavaş | Kötü gecikme | Uç durum |
| 3 | p95 | %5 bundan yavaş | Kullanıcı şikâyeti eşiği | Mutlak tepe |
| 4 | p99 | %1 bundan yavaş | Uç durum (outlier) | Tipik durum |
| 5 | max | En yavaş tek ölçüm | Tek seferlik tepe | Yaygınlık |
| 6 | Ortalama | Toplam/sayı | — | Dağılım (§5.2) |

### §5.2 Ortalama (mean) neden tek başına yasak

| # | Örnek dağılım | Ortalama | p95 | Yanıltıcı taraf |
|---|---|---|---|---|
| 1 | 10×100 ms + 1×5000 ms | 545 ms | 5000 ms | Ortalama "kabul edilebilir" der |
| 2 | Hepsi 200 ms | 200 ms | 200 ms | Ortalama burada yeterlidir |
| 3 | 99×10 ms + 1×2000 ms | 30 ms | 2000 ms | Ortalama tepeyi saklar |

**Kural:** her ölçüm kaydı **hem ortalama hem p95** içerir; karar vericisi p95'tir, ortalama
yalnız kayıt bütünlüğü içindir.

### §5.3 Metrik–soru eşlemesi

| # | Soru | Doğru metrik | Yanlış metrik | Gerekçe |
|---|---|---|---|---|
| 1 | Kullanıcı ne kadar bekliyor? | p95 | Ortalama | §5.2 |
| 2 | Test bütçeye sığdı mı? | max + job süresi | Ortalama | Aşım tek seferde olur |
| 3 | Paralellik kazancı var mı? | Toplam süre (workers 1 vs N) | p95 | Paralel yarışır |
| 4 | Kararlı mı? | İki koşum p95 farkı | Tek ölçüm | §4.3 |
| 5 | Kaç test aşım yaptı? | Aşım sayacı | Yüzdelik | Aşım sayısı doğrudan |

### §5.4 Yüzdelik kayıt biçimi

Her ölçüm kaydı tek bir tablo satırıdır; üç sütun zorunludur (metrik, değer, bütçe).

| # | Sütun | Zorunlu | Değer örneği | Açıklama |
|---|---|---|---|---|
| 1 | Ölçüm adı | evet | `e2e-suite-toplam` | Ne ölçüldü |
| 2 | Ortam | evet | `CI · workers 1 · chromium` | §4.1 |
| 3 | Tekrar | evet | `5` | §4.3 |
| 4 | p50 | evet | Ölçüm değeri | §5.1 |
| 5 | p95 | evet | Ölçüm değeri | Karar metriği |
| 6 | Ortalama | evet | Ölçüm değeri | Yalnız kayıt |
| 7 | Bütçe | evet | 20 dk | §6.2 |
| 8 | Karar | evet | Kabul / ret | §8.2 |

**Kural 1:** p90 ve p99 isteğe bağlıdır; p50, p95, ortalama ve bütçe zorunludur.
**Kural 2:** ölçüm adı, neyin ölçüldüğünü tek sözcükte anlatır (test adı, suite adı, job adı).
**Kural 3:** bir önceki kayıttan p95 farkı da yazılır — regresyon farkla anlaşılır (`regression/`).

## §6 Süre bütçeleri (budgets)

Bütçe, üst sınırdır: aşan ölçüm **kabul edilmez**. Bütçeler üç katmandadır.

### §6.1 Katman 1 — test içi bütçeler

| # | Bütçe | Değer | Dosya / Satır | Ne korur |
|---|---|---|---|---|
| 1 | Tek test timeout'u | 30000 ms | playwright.config.ts / Satır 52 | Asılı kalan testi |
| 2 | Assertion timeout'u | 5000 ms | playwright.config.ts / Satır 54 | Beklemeyen assertion'ı |
| 3 | webServer başlangıcı | 120000 ms | playwright.config.ts / Satır 49 | Sunucu başlamazsa diye |
| 4 | Yorum satırı teyidi | "Timeout: 30s per test" | playwright.config.ts / Satır 7 | Belge-kod uyumu |

### §6.2 Katman 2 — CI job bütçeleri

| # | Job | Bütçe (dk) | ci.yml satırı | Soft mu |
|---|---|---|---|---|
| 1 | php-test | 15 | 70 | hayır |
| 2 | js-test | 10 | 145 | hayır |
| 3 | js-coverage | 15 | 164 | evet (Satır 167) |
| 4 | js-e2e | 20 | 181 | evet (Satır 183) |

### §6.3 Katman 3 — Suite/sıra bütçesi

| # | Bütçe | Değer | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Worker sayısı (CI) | 1 | playwright.config.ts Satır 21 | kodda sabit |
| 2 | Paralel test | `fullyParallel: true` | Satır 18 | kodda sabit |
| 3 | Şarding | yapılandırma yok | — | ⚠️ VERIFICATION REQUIRED |
| 4 | Suite üst süresi (JS unit) | tanımsız (yalnız job 10 dk) | ci.yml Satır 145 | dolaylı bütçe |
| 5 | Paylaşılan makine etkisi | CI = ortak makine | — | §13 risk 4 |

### §6.4 Bütçe aşım davranışı

| # | Aşım türü | Ne olur | Davranış | Kanıt |
|---|---|---|---|---|
| 1 | Test timeout'u | Playwright testi kırmızı | Retry (2) → başarısız | config Satır 20 |
| 2 | Job bütçesi | Job kill edilir | Kırmızı (soft ise görünür-kırmızı) | ci.yml Satır 145/181 |
| 3 | Soft gate aşımı | Job başarısız ama PR engellenmez | `continue-on-error: true` | Satır 167/183 |
| 4 | Assertion timeout | Test kırmızı | Retry → başarısız | config Satır 53-55 |

## §7 Yük aşamaları (load stages)

Yük testi, tek seferlik "hızlı mı" değil, **yük arttıkça davranış** sorusunu yanıtlar.
Depoda yük testi aracı **tanımlı değildir** (⚠️ VERIFICATION REQUIRED); bu bölüm protokolü
tanımlar, aracı seçmez.

| # | Aşama | Amaç | Süre (başlangıç) | Metrik |
|---|---|---|---|---|
| 1 | Isınma | Önbellek otursun | 60 s | — |
| 2 | Artış (ramp-up) | Kapasite eğrisi | 300 s | p95 vs yük |
| 3 | Sabit yük (steady) | Kararlılık | 600 s | p95, hata oranı |
| 4 | Tepe (peak) | Sınır davranışı | 60 s | Aşım/kayıp |
| 5 | Soğuma | Toparlanma | 120 s | Geri dönüş süresi |

**Not:** süreler başlangıç değeridir; gerçek pencere ortam kaydıyla birlikte yazılır (§4 adım 7).

### §7.1 Yük metrikleri

| # | Metrik | Tanım | Kabul ölçütü | Kayıt |
|---|---|---|---|---|
| 1 | İstek/saniye | Throughput | Yükselmeli | elde edilmeli |
| 2 | p95 latency | Yüzdelik | Bütçe içi | §6 |
| 3 | Hata oranı | Hatalı/Toplam | %0 hedefi | elde edilmeli |
| 4 | Zaman aşımı (timeout) sayısı | Bütçe aşan istek | 0 | sayım |
| 5 | Kuyruk birikmesi | Giden > gelen | Birikmemeli | ⚠️ VERIFICATION REQUIRED |

### §7.2 Yük – davranış matrisi

| # | Gözlem | Yorum | Eylem | Öncelik |
|---|---|---|---|---|
| 1 | Yük ×2 → p95 ×2 | Doğrusal bozulma | Kapasite planı | orta |
| 2 | Yük ×2 → p95 ×10 | Çöküş eşiği | Darboğaz analizi | yüksek |
| 3 | Yük sabit → p95 artıyor | Sızıntı/oturma | §4.2 isınma kontrolü | yüksek |
| 4 | Hata oranı yükseliyor | Red politikası | Eşiği gözden geçir | yüksek |
| 5 | Toparlanma yok | Kalıcı hasar | Bloke et | kritik |

### §7.3 Soak (uzun süreli) test kuralı

| # | Kural | Süre | Çıktı | Durum |
|---|---|---|---|---|
| 1 | Sabit yük altında bellek izleme | ≥ 30 dk | Bellek eğrisi | ⚠️ VERIFICATION REQUIRED |
| 2 | Dosya tutma izleme (log/dizin) | ≥ 30 dk | Disk eğrisi | ⚠️ VERIFICATION REQUIRED |
| 3 | Bağlantı sızıntısı | ≥ 30 dk | Açık bağlantı sayısı | ⚠️ VERIFICATION REQUIRED |
| 4 | Otomatik tekrar (scheduled) | Haftalık | Tarihli rapor | tanımlı değil |

### §7.4 Yük testi rapor tablosu (kayıt şablonu)

| # | Aşama | Süre | Yük | p95 | Hata oranı | Karar |
|---|---|---|---|---|---|---|
| 1 | Isınma | 60 s | Artış | Ölçülmez | Ölçülmez | Geç |
| 2 | Artış | 300 s | Artan | Ölçüm değeri | Ölçüm değeri | Eğri kaydı |
| 3 | Sabit | 600 s | Sabit | Ölçüm değeri | Ölçüm değeri | Karar metni |
| 4 | Tepe | 60 s | Tepe | Ölçüm değeri | Ölçüm değeri | Sınır tespiti |
| 5 | Soğuma | 120 s | Azalan | Ölçüm değeri | Ölçüm değeri | Toparlanma |

**Kural 1:** hücreler boş bırakılmaz; ölçülmediyse `Ölçülmez` (§7.1) ya da
`⚠️ VERIFICATION REQUIRED` yazılır.
**Kural 2:** rapor, §4 adım 7'deki kayıt alanında saklanır; tarih + ortam alanı zorunludur.
**Kural 3:** aracın adı da kayda girer; araç seçilene kadar bu tablo protokol olarak kalır (§7).

## §8 Eşik (threshold) karar matrisi

### §8.1 Eşik türleri

| # | Eşik | Değeri kim koyar | Denetim | Durum |
|---|---|---|---|---|
| 1 | Test timeout'u | Kod (30 s) | config Satır 52 | mevcut |
| 2 | Assertion timeout'u | Kod (5 s) | config Satır 54 | mevcut |
| 3 | Job bütçesi | Workflow (10-20 dk) | ci.yml | mevcut |
| 4 | Kapsam yüzdesi eşiği | A7 kararı | ci.yml Satır 165-166 | soft (§8.3) |
| 5 | p95 bütçe değeri | Tanımlı değil | — | ⚠️ VERIFICATION REQUIRED |
| 6 | Yük testi eşiği | Araç yok | — | ⚠️ VERIFICATION REQUIRED |

### §8.2 Karar akışı

| # | Adım | Soru | Evet | Hayır |
|---|---|---|---|---|
| 1 | Bütçe aşıldı mı? | §6 karşılaştırması | 2. adıma | Kabul |
| 2 | Tek sefer mi? | Tekrar koşu (§4.3) | 3. adıma | Kararsız → `regression/` |
| 3 | Ortam farkı mı? | §4.1 alanları | Ortamı sabitle, tekrarla | 4. adıma |
| 4 | Kod kaynaklı mı? | Diff incelemesi | Red + kök neden | Altyapı → K018-devops |
| 5 | Soft kapı mı? | ci.yml `continue-on-error` | Görünür-kırmızı, yaz | Bloke et |

### §8.3 Mevcut kapıların durumu (soft gate)

| # | Kapı | ci.yml satırı | Efekt | Gerekçe (repo yorumu) |
|---|---|---|---|---|
| 1 | js-coverage | 167 `continue-on-error: true` | Görünür-kırmızı, PR engellemez | Satır 165-166: %80 eşiği şimdilik erişilemez |
| 2 | js-e2e | 183 `continue-on-error: true` | Görünür-kırmızı, PR engellemez | Satır 182: A6 ilk koşular bilinen-riskli |
| 3 | php-test | (yok) | Sert kapı | Satır 67-80 matrix |
| 4 | js-test | (yok) | Sert kapı | Satır 142-158 |

**Kural:** soft kapı, **süreç hâli**dir (risk yönetimi), kalıcı çözümdür; kapıyı sertleştirmek
için eşik koşulu (§8.1 satır 4) gerekir ve karar `K018-devops/pipeline` dosyasıyla birlikte
gözden geçirilir.

### §8.4 Eşik güncelleme döngüsü

| # | Tetikleyici | Aksiyon | Sahibi | Sıklık |
|---|---|---|---|---|
| 1 | Soft kapı 3 aydır kırmızı | Eşik nedenini yaz, sertleştirme kararı | Pipeline sahibi | 3 ayda bir |
| 2 | Test sayısı arttı (A7 koşulu) | %80 eşiğini yeniden dene | Coverage sahibi | Her çeyrek |
| 3 | Bütçe aşımı 2 hafta üst üste | Bütçeyi artır veya kök neden bul | Performans sahibi | Sürekli |
| 4 | Yeni job eklendi | Bütçe ata (§6.2 tablosu) | Pipeline sahibi | Ekleme anında |
| 5 | Ortam değişti (donanım) | Tüm ölçümleri yeniden al | Ölçüm sahibi | Değişim anında |

**Kural:** eşik değişiklikleri yalnız bu döngüyle yapılır; kod içi sabit değer değiştirmek eşik
kararı değildir (§11 yasak 6).

## §9 Kod örnekleri — repo gerçek kodu

### §9.1 Süre bütçeleri — playwright.config.ts Satır 44-55

```typescript
webServer: {
    command: 'php -S localhost:81 -t ../../home.coremusic.net/',
    url: 'http://home.coremusic.net:81',
    reuseExistingServer: !process.env.CI,
    timeout: 120 * 1000,
},

timeout: 30 * 1000,
expect: {
    timeout: 5 * 1000,
},
```

*(Alıntı TypeScript dosyasından; blok repo satırlarının birebir kopyasıdır.)*

**Dosya: `assets.coremusic.net/playwright.config.ts` / Satır: 44 · 49 · 52 · 53 · 54**

### §9.2 Paralellik — playwright.config.ts Satır 18-21

```typescript
fullyParallel: true,
forbidOnly: !!process.env.CI,
retries: process.env.CI ? 2 : 0,
workers: process.env.CI ? 1 : undefined,
```

CI'da `workers = 1` → ölçüm koşulları tekilleşir (§4.1 satır 1); yerelde tanımsız → Playwright
çekirdek sayısına bırakılır, yerel ölçüm CI'a genellenmez (§4.2 satır 3).

**Dosya: `assets.coremusic.net/playwright.config.ts` / Satır: 18 · 20 · 21**

### §9.3 CI job bütçesi — ci.yml Satır 144-145 ve 180-181

```yaml
    runs-on: ubuntu-latest
    timeout-minutes: 10
```

```yaml
    runs-on: ubuntu-latest
    timeout-minutes: 20
```

**Dosya: `.github/workflows/ci.yml` / Satır: 144-145 (js-test) · 180-181 (js-e2e)**

### §9.4 Soft gate — ci.yml Satır 165-167

```yaml
    # A7 kararı: %80 eşiği şimdilik erişilemez (12.9k satır / <100 test) —
    # job GÖRÜNÜR-KIRMIZI olsun ama bloklamasın; eşikler büyüyünce kapatılır.
    continue-on-error: true
```

**Dosya: `.github/workflows/ci.yml` / Satır: 165 · 166 · 167**

## §10 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | assets.coremusic.net/playwright.config.ts | 7 | "Timeout: 30s per test" belge yorumu |
| 2 | assets.coremusic.net/playwright.config.ts | 18 | `fullyParallel: true` |
| 3 | assets.coremusic.net/playwright.config.ts | 20 | Retry: CI 2, yerel 0 |
| 4 | assets.coremusic.net/playwright.config.ts | 21 | Workers: CI 1 |
| 5 | assets.coremusic.net/playwright.config.ts | 30-32 | Trace/screenshot/video kanıtı (retry başına) |
| 6 | assets.coremusic.net/playwright.config.ts | 44-50 | webServer tanımı + 120 s başlangıç |
| 7 | assets.coremusic.net/playwright.config.ts | 52 | Test timeout 30 s |
| 8 | assets.coremusic.net/playwright.config.ts | 53-55 | Expect timeout 5 s |
| 9 | .github/workflows/ci.yml | 70 | php-test job 15 dk |
| 10 | .github/workflows/ci.yml | 145 | js-test job 10 dk |
| 11 | .github/workflows/ci.yml | 151 | Node 26 (sürüm sabitleme) |
| 12 | .github/workflows/ci.yml | 164 | js-coverage job 15 dk |
| 13 | .github/workflows/ci.yml | 167 | Soft gate: `continue-on-error` |
| 14 | .github/workflows/ci.yml | 181 | js-e2e job 20 dk |
| 15 | .github/workflows/ci.yml | 183 | Soft gate: `continue-on-error` |
| 16 | .github/workflows/ci.yml | 199-200 | Playwright chromium kurulumu |
| 17 | .github/workflows/ci.yml | 202-203 | `npm run test:e2e` çalıştırması |
| 18 | shared/composer.json | 47 | PHP test çalıştırma komutu |

## §11 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Tek ölçümle karar | Gürültü | §4.3 (≥ 5 tekrar) |
| 2 | Ortalama ile kabul | Tepe saklanır | §5.2 |
| 3 | Isınma koşumunu ölçüme katmak | Önbellek yanlılığı | §4.2 |
| 4 | Yerel ölçümü CI kanıtı saymak | Ortam farkı | §4.1 satır 5 |
| 5 | Workers > 1 ile süre ölçmek | Yarış | config Satır 21 |
| 6 | Eşiği kod içine gömmek | Karar sahibi kaybolur | §8.1 |
| 7 | Soft gate'i unutup "yeşil" demek | Sahte güven | §8.3 |
| 8 | Aşımı elle elemek (outlier silmek) | Önyargı | §4.3 satır 3 |
| 9 | Yük testi aracını kod içine sabitlemek | Araç seçimi mimari karar | §7 (araç yok) |

## §12 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K019-testing/unit | Birim yeşili | Ölçüm öncesi ön koşul | CI sırası | mevcut |
| 2 | K019-testing/integration | Sözleşme sürekliliği | Yük altı davranış | Durum kodu seti | kısmen |
| 3 | K019-testing/regression | p95 baseline'ı | Regresyon eşiği | Ölçüm kaydı | ⚠️ VERIFICATION REQUIRED |
| 4 | K019-testing/e2e | Süre bütçeleri | Tarayıcı testi süresi | config süreleri | mevcut (§6.1) |
| 5 | K019-testing/coverage | Aşım testinin kapsamı | Kapsam girdisi | Vitest coverage | mevcut (ci.yml Satır 175) |
| 6 | K018-devops/pipeline | Job bütçeleri | CI kapısı | `timeout-minutes` | mevcut (§6.2) |
| 7 | K017-observability/metrics | Üretim metriği | Ölçüm referansı | p95 tanımı | üretim kapsamı ayrı |

## §13 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Yük testi aracı yok | Kapasite sorusu yanıtsız | yüksek | Araç seçimi kararı | ⚠️ VERIFICATION REQUIRED |
| 2 | p95 bütçesi tanımsız | Aşım ölçütü yok | yüksek | Bütçe tanımı | ⚠️ VERIFICATION REQUIRED |
| 3 | Ölçüm geçmişi yok | Regresyon tespiti imkânsız | yüksek | Ölçüm kaydı | UNKNOWN |
| 4 | CI ortak makine | Ölçüm gürültüsü | orta | Tekrar + ortam kaydı | §4.3 |
| 5 | Workers = 1 | E2E yavaşlar | orta | Bütçe 20 dk (ci.yml 181) | kabul edilmiş |
| 6 | Soft gate'ler kalıcılaşabilir | Sahte yeşil | orta | Eşik büyüyünce kapatma | açık (ci.yml 166) |
| 7 | `performance/*` glob'u e2e kapsamı dışında | Performans testi hiç koşulmaz olabilir | yüksek | testMatch kararı | ⚠️ VERIFICATION REQUIRED |
| 8 | Şarding yapılandırması yok | E2E süresi büyüdükçe paralelleşmez | orta | Sharding kararı | ⚠️ VERIFICATION REQUIRED |

## §14 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Test | ≤ 30 s | Playwright timeout | her koşu | config Satır 52 |
| 2 | Assertion | ≤ 5 s | Expect timeout | her koşu | config Satır 54 |
| 3 | Job | ≤ 10/15/20 dk | GitHub Actions | her PR | ci.yml 145/164/181 |
| 4 | Ölçüm | ≥ 5 tekrar | Protokol | her ölçüm | §4 adım 3 |
| 5 | Metrik | p95 + ortalama kaydı | Rapor | her ölçüm | §5.2 |
| 6 | Eşik | Soft kapı sayısı azalıyor | ci.yml taraması | aylık | §8.3 |
| 7 | Kapasite | Yük testi raporu | Araç + protokol | — | ⚠️ VERIFICATION REQUIRED |

## §15 Ölçüm raporu şablonu

| # | Alan | Zorunlu | Değer örneği | Denetim |
|---|---|---|---|---|
| 1 | Tarih | evet | 2026-10-12 | §4 adım 7 |
| 2 | Ortam (OS/CPU/Node/PHP) | evet | ubuntu-latest · Node 26 · PHP_VERSION | §4.1 |
| 3 | Paralellik | evet | workers = 1 | config Satır 21 |
| 4 | Tekrar sayısı | evet | 5 | §4.3 |
| 5 | p50/p90/p95/p99 | evet | Ölçüm değerleri | §5.1 |
| 6 | Ortalama + max | evet | Ölçüm değerleri | §5.2 |
| 7 | Bütçe karşılaştırması | evet | Aşım: var/yok | §6 |
| 8 | Eşik kararı | evet | Kabul / ret / soft | §8 |
| 9 | Sonraki adım | evet | Aksiyon | §8.2 |

## §16 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Playwright paralellik (Parallelism) sayfası, testlerin eşzamanlı koşulmasını ve worker ayarını tanımlar | https://playwright.dev/docs/test-parallel | 2026-10-12 | HTTP 200 doğrulandı |
| Playwright komut satırı (Command line) sayfası, test çalıştırma seçeneklerini listeler | https://playwright.dev/docs/test-cli | 2026-10-12 | HTTP 200 doğrulandı |
| MDN Performance API sayfası, tarayıcıda performans ölçümü için Web API'lerini tanımlar | https://developer.mozilla.org/en-US/docs/Web/API/Performance_API | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 The Command-Line Test Runner sayfası, komut satırı test çalıştırıcısını ve seçeneklerini tanımlar | https://docs.phpunit.de/en/10.5/textui.html | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz.

## §17 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 + başlık kaydedildi | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | dosya tam okuma + satır doğrulama | 18 satır eşleşti | 2026-10-12 |
| 5 | Süre değerleri | 30 s / 5 s / 120 s / 10-15-20 dk | kodla birebir | 2026-10-12 |
| 6 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
