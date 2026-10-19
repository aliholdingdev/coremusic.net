---
title: "K18-testing/regression — Regresyon Test Seçim Kuralları: Golden Reference, Flaky ve Karantina"
node_id: K18-testing/regression/regression-test-secim-kurallari
node_type: module
status: draft
parent_id: K18-testing/regression
children: []
depends_on:
  - K18-testing
  - K18-testing/regression
evidence:
  - "assets.coremusic.net/playwright.config.ts — Satır 19 forbidOnly (CI), Satır 20 retries (CI: 2, yerel: 0), Satır 25 json rapor dosyasi (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 17-19 concurrency + cancel-in-progress, Satır 68 needs php-lint, Satır 162 needs js-test (2026-10-12)"
  - ".github/workflows/ci.yml — Satır 167 ve 183 continue-on-error (soft gate) (2026-10-12)"
  - "auth.coremusic.net/phpunit.xml — Satır 6 failOnRisky, Satır 7 failOnWarning (2026-10-12)"
  - "shared/phpunit.xml — Satır 6 stopOnFailure false (tam rapor), Satır 8-33 testsuite seçimi (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa başlığı ile döndü (2026-10-12)"
  - "URL envanteri: 1206 benzersiz URL, bu dosyada çakışma 0 (2026-10-12)"
  - "Repo alıntıları dosyanın tam okunmasıyla doğrulandı (2026-10-12)"
open_questions:
  - "Depoda flaky test listesi/karantina mekanizması yok — soft gate bundan farklıdır (⚠️ VERIFICATION REQUIRED)"
  - "Golden reference (referans taban) dosyası yok; json rapor üretiliyor fakat karşılaştırılmıyor (⚠️ VERIFICATION REQUIRED)"
  - "Regresyon testi seçimi için otomatik kural (değişen dosya → test) tanımlı değil (⚠️ VERIFICATION REQUIRED)"
group: kod
---

# K019-testing/regression/regression-test-secim-kurallari

> **ELI10:** Regresyon, "dün çalışıyordu, bugün bozuldu" durumudur. Bu dosya üç soruyu yanıtlar:
> hangi test tekrar çalıştırılır (seçim), hangi sonuç referans kabul edilir (golden reference) ve
> kararsız (flaky) test karantinaya nasıl alınır. Amaç: bozulma erken yakalanır, kararsızlık
> kör etki yapmaz.

## §1 Amaç

Bu modül, CoreMusic'in **regresyon testi kurallarının tek yetkili tanımıdır**. Üç karar bu
dosyada verilir: seçim kuralı · referans taban (golden reference) · flaky/karantina. Regresyon
kontrolü, birim ve entegrasyon testlerinin **yeşilliğinin sürdürülebilirliğini** sınar.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | Hangi test regresyon için koşulur? | Seçim kuralları | §6 |
| 2 | Referans sonuç nedir? | Golden reference | §5 |
| 3 | Kararsız test nasıl ele alınır? | Flaky tanımı + ölçümü | §7 |
| 4 | Kararsız test nereye konur? | Karantina (quarantine) | §8 |
| 5 | Yeniden deneme kim sınırı? | Retry politikası | §9 |
| 6 | Mevcut kapılar ne durumda? | Soft gate + needs zinciri | §9.3 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | CI retry (E2E) | adet | 2 (playwright.config.ts Satır 20) | kodda sabit |
| H2 | CI retry (PHP unit) | adet | tanımsız (PHPUnit varsayılanı) | ⚠️ VERIFICATION REQUIRED |
| H3 | Karantina listesi | dosya | depoda yok | ⚠️ VERIFICATION REQUIRED |
| H4 | Golden reference | dosya | yok (json rapor var, karşılaştırma yok) | ⚠️ VERIFICATION REQUIRED |
| H5 | İptal edilen paralel koşu | davranış | `cancel-in-progress: true` | ci.yml Satır 19 |
| H6 | Risky/warning kapısı | bayrak | `failOnRisky=true` (auth) | auth phpunit.xml Satır 6 |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Seçim kuralı | ✅ Değişiklik → test eşlemesi | ❌ Test yazımı → `unit/` |
| Golden reference | ✅ Referans saklama ve karşılaştırma | ❌ Referans üretimi → `coverage/` |
| Flaky | ✅ Tanım, ölçüm, karar | ❌ Yeniden deneme sayısı → §9 |
| Karantina | ✅ Karantina kuralı ve çıkışı | ❌ Süreç sahipliği → K17-devops |
| Retry | ✅ Politika ve sınır | ❌ Performans ölçümü → `performance/` |
| Shard | ✅ Seçim/parçalama etkisi | ❌ Paralellik hesabı → `performance/` |
| E2E kararsızlığı | ✅ Tarayıcı kaynaklı flaky | ❌ E2E yazımı → `e2e/` |

**Sınır kararı:** kararın ta kendisi buradadır: **flaky test silinmez, ölçülür ve karantinaya
alınır**; karantina süresiz değildir (§8.4).

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili kanıt |
|---|---|---|---|
| Regresyon | regression | Geriye dönük bozulma | §4 |
| Golden reference | golden reference | Referans sonuç tabanı | §5 |
| Flaky | flaky test | Aynı kodla bazen geçen/bazen geçen test | §7 |
| Karantina | quarantine | Flaky testin geçici olarak dışarıda tutulması | §8 |
| Retry | retry | Testin tekrar denenmesi | config Satır 20 |
| Shard | sharding | Testlerin dilimlere bölünmesi | test-sharding |
| Needs | needs | Job bağımlılığı (zincir) | ci.yml Satır 68, 162 |
| Soft gate | soft gate | Görünür-kırmızı, bloklamayan kapı | ci.yml Satır 167, 183 |
| İptal | cancel-in-progress | Aynı ref için eski koşuyu iptal | ci.yml Satır 19 |
| Metadata | annotation | Teste ek bilgi veren işaret | phpunit annotations |
| Hata yönetimi | error handling | İstisna/hata davranışının tanımları | phpunit error-handling |
| Tam rapor | stopOnFailure=false | İlk hatada durmadan koşma | phpunit.xml Satır 6 |

## §4 Regresyon türleri

| # | Tür | Belirti | Yakalayıcı | Repo karşılığı |
|---|---|---|---|---|
| 1 | Davranış regresyonu | Eski test kırmızı | Unit/integration | php-test job |
| 2 | Sözleşme regresyonu | Durum kodu/başlık değişti | Api suite | RouteTableTest |
| 3 | Performans regresyonu | p95 arttı | Ölçüm (performance/) | ⚠️ VERIFICATION REQUIRED |
| 4 | Kapsam regresyonu | Yeni kod testsiz | Kapsam raporu | js-coverage (soft) |
| 5 | Kararsızlık regresyonu | Yeni flaky çıktı | Retry/rapor okuması | ⚠️ VERIFICATION REQUIRED |
| 6 | Ortam regresyonu | Sürüm değişti, test kırıldı | CI matrisi | PHP_VERSION (ci.yml Satır 22) |

### §4.1 Regresyon kaynakları

| # | Kaynak | Örnek | Risk | Önleme |
|---|---|---|---|---|
| 1 | Kod değişikliği | Refactor | Davranış kaybı | §6 seçim kuralı |
| 2 | Bağımlılık değişikliği | Sürüm bump | Uyumsuzluk | composer.lock / npm ci |
| 3 | Ortam değişikliği | PHP 8.4 (Satır 22) | Farklı davranış | Ortam sabitleme |
| 4 | Yapılandırma değişikliği | phpunit.xml | Kapı zayıflaması | §11 kanıt 4-6 |
| 5 | Veri değişikliği | Route tablosu | Eşleşme kaybı | RouteTableTest |

### §4.2 Semptom → aksiyon matrisi

| # | Semptom | İlk şüphe | Aksiyon | Escalation |
|---|---|---|---|---|
| 1 | Kırmızı E2E (2 retry sonrası) | Kod veya kararsızlık | §7 ölçümü | Kod sahibi |
| 2 | Kırmızı PHP testi | Gerçek regresyon | Diff + geri alma | Bloke et |
| 3 | Soft gate kırmızı | Eşik/altyapı | §8.3 (ci.yml 165-166) | Pipeline sahibi |
| 4 | Yeni flaky (2/5 koşum) | Kararsızlık | Karantina (§8) | Regresyon sahibi |
| 5 | Tüm job iptal | Paralel koşu yarışı | ci.yml Satır 19 | Kabul edilmiş davranış |

## §5 Golden reference (referans taban) kuralları

Golden reference, regresyonun **ölçüldüğü sabit referanstır**; referans yoksa "geriye dönük
bozulma" tespit edilemez. Bu depoda referans **üretilmiyor** (json rapor var, karşılaştırma yok:
⚠️ VERIFICATION REQUIRED); kurallar burada tanım olarak verilir.

| # | Kural | Neden | Biçim | Zorunlu mu |
|---|---|---|---|---|
| 1 | Referans tek kaynak | Çoklu referans tartışması | Tek dosya/depo | evet |
| 2 | Referans sürümle etiketlenir | Hangi koda ait belli olsun | Commit/PR kimliği | evet |
| 3 | Referans elle değiştirilmez | Referans bozulursa ölçüm bozulur | Onay akışı | evet |
| 4 | Fark raporu zorunlu | Fark yoksa regresyon yok | Tablo | evet |
| 5 | Kararsızlıkta referans yenilenmez | Flaky sabitlenir | §7 kararı | evet |

### §5.1 Bu depodaki mevcut durum

| # | Varlık | Mevcut mu | Kanıt | Sonuç |
|---|---|---|---|---|
| 1 | E2E json raporu | ✅ | config Satır 25 `test-results/e2e-results.json` | Üretiliyor |
| 2 | Rapor karşılaştırması | ❌ | Karşılaştırma adımı yok | ⚠️ VERIFICATION REQUIRED |
| 3 | Kapsam raporu (JS) | ✅ | ci.yml Satır 175-176 Vitest coverage | Üretiliyor (soft) |
| 4 | Eşik (kapsam) | ⚠️ | %80 eşiği erişilemez, soft (Satır 165-167) | Yumuşak |
| 5 | Snapshot (UI) | ❌ | config'te screenshot: yalnız hata anı (Satır 31) | Kıyaslama yok |
| 6 | SQL/beklenen çıktı referansı | ❌ | Depoda tespit edilmedi | ⚠️ VERIFICATION REQUIRED |

### §5.2 Fark (diff) değerlendirme matrisi

| # | Fark tipi | Kabul mu | Aksiyon | Kayıt |
|---|---|---|---|---|
| 1 | Test adı değişti | İnceleme | Beklenen mi? | PR notu |
| 2 | Süre arttı (budget içi) | Kabul | İzle | §5.1 satır 1 |
| 3 | Yeni kırmızı | Red | Regresyon | Geri alma |
| 4 | Yeşil → flaky | Karantina | §8 | Karantina kaydı |
| 5 | Rapor dosyası kayboldu | Red | Altyapı | ci.yml denetimi |

## §6 Seçim kuralları — hangi test regresyon için koşulur

Seçim kuralı, **her koşuda neyin çalıştırılacağını** belirler. Bu depoda seçim statiktir: CI
her push ve pull_request'te aynı işleri çalıştırır (ci.yml Satır 10-12).

| # | Seçim stratejisi | Kural | Bu depoda | Kanıt |
|---|---|---|---|---|
| 1 | Tam çalıştırma | Her koşuda tüm suitler | ✅ PHP: tam suit (shared) | ci.yml Satır 76 |
| 2 | Değişiklik-bazlı | Değişen dizin → ilgili suit | ❌ yok | ⚠️ VERIFICATION REQUIRED |
| 3 | Etiket-bazlı | `@group` ile seçme | Yapılandırma var, kullanım belirsiz | phpunit annotations |
| 4 | Sharding | Testler dilimlere bölünür | ❌ yapılandırma yok | ⚠️ VERIFICATION REQUIRED |
| 5 | Kritik yol | Bloklayan işler (lint → test) | ✅ `needs` zinciri | ci.yml Satır 68 |

### §6.1 Mevcut seçim — CI tetikleyicileri

| # | Tetikleyici | Kapsam | ci.yml satırı | Not |
|---|---|---|---|---|
| 1 | `push` | Tüm işler | 11 | Her commit |
| 2 | `pull_request` | Tüm işler | 12 | PR |
| 3 | `concurrency` | Aynı ref için tek koşu | 17-19 | Eski koşu iptal |
| 4 | Zamanlanmış (schedule) | Yok | — | ⚠️ VERIFICATION REQUIRED |
| 5 | Manuel tetik (workflow_dispatch) | Yok | — | ⚠️ VERIFICATION REQUIRED |

### §6.2 Seçim karar matrisi (yeni test eklerken)

| # | Soru | Evet | Hayır | Not |
|---|---|---|---|---|
| 1 | Davranış doğruluyor mu? | Regresyon adayı | Test değil | §4.1 |
| 2 | Deterministik mü? | Doğrudan suit | Önce `unit/` kararlılığı | unit dosyası §7 |
| 3 | Ağ içeriyor mu? | `e2e/` ya da soft kapı | Sert kapı | §9.3 |
| 4 | Uzun sürüyor mu (> 5 dk)? | Shard/sharding düşün | Doğrudan suit | test-sharding |
| 5 | Flaky geçmişi var mı? | Karantina (§8) | Suit | §7 |

### §6.3 PHPUnit seçimi — etiket ve suit

| # | Seçim aracı | Biçim | Kullanım durumu | Not |
|---|---|---|---|---|
| 1 | Testsuite | `--testsuite Unit` | ✅ auth'ta | ci.yml Satır 80 |
| 2 | Dosya yolu | `vendor/bin/phpunit tests/Api` | Manuel | phpunit textui (önceki dosya) |
| 3 | `@group` etiketi | `--group` | ⚠️ kullanım doğrulanmadı | phpunit annotations |
| 4 | `@depends` | Bağımlı test | ⚠️ kullanım doğrulanmadı | phpunit annotations |
| 5 | `--filter` | Ad filtresi | Manuel | Komut satırı çalıştırıcı |

## §7 Flaky (kararsız) test — tanım ve ölçüm

Flaky: **kod değişmeden**, art arda koşumlarda farklı sonuç veren test. Flaky, regresyon
değildir; ikisini karıştırmak hem gerçek bozulmayı saklar hem de güveni yok eder.

| # | Flaky kaynağı | Belirti | Ölçüm | Önleme |
|---|---|---|---|---|
| 1 | Zaman bağımlılığı | Saniye/tarih assert | Farklı sonuç | Sabit zaman |
| 2 | Sıra bağımlılığı | Test sırası değişince kırılır | Sıralama denemesi | İzolasyon |
| 3 | Ağ/gecikme | E2E timeout | Retry sayısı | Bütçe (performance/) |
| 4 | Dosya/ortam | Yerel yeşil, CI kırmızı | İki ortam karşılaştırma | Ortam sabitleme |
| 5 | Paralellik yarışı | Az sayıda kırmızı | Çoklu koşum | concurrency (ci.yml Satır 17-19) |
| 6 | Tarayıcı kararsızlığı | E2E tutarsız | retry = 2 (Satır 20) | Kanıt toplama (trace) |

### §7.1 Flaky ölçüm kuralı

| # | Kural | Eşik | Aksiyon | Durum |
|---|---|---|---|---|
| 1 | Aynı test 5 koşumda | 1-2 kırmızı | Flaky ilan et | §8 karantina |
| 2 | 5 koşumda ≥ 3 kırmızı | Gerçek hata | Regresyon | §4.2 satır 2 |
| 3 | Retry ile geçen test | 1. denemede kırmızı | Flaky kaydı | §9.2 |
| 4 | CI ile yerel fark | Tek ortamda yeşil | Ortam flaky | §7 satır 4 |
| 5 | Ölçüm yoksa | — | Flaky iddiası yazılmaz | §12 yasak 5 |

### §7.2 Retry — flaky'ı saklar mı, gösterir mi?

| # | Davranış | Efekt | Risk | Karşı önlem |
|---|---|---|---|---|
| 1 | Retry = 2 (CI) | Geçici kararsızlık yutulur | Kalıcı flaky gizlenir | Rapor okuması |
| 2 | Retry = 0 (yerel) | Hızlı geri bildirim | Yerel/CI farkı | §7 satır 4 |
| 3 | Retry + kanıt | Trace/screenshot/video | Depolama | config Satır 30-32 |
| 4 | Retry sonsuz | Asla kırmızı | — | Üst sınır: 2 |

### §7.3 Flaky kayıt tablosu

| # | Alan | Zorunlu | Değer örneği | Amaç |
|---|---|---|---|---|
| 1 | Test adı | evet | `js-e2e / login.spec.ts` | Tanı |
| 2 | İlk görülme | evet | Tarih | Trend |
| 3 | Koşum oranı | evet | 2/5 kırmızı | §7.1 eşiği |
| 4 | Kaynak tahmini | evet | Ağ / sıra / zaman | §7 tablosu |
| 5 | Karar | evet | Karantina / düzelt | §8 |
| 6 | Karantina bitişi | evet | Tarih | §8.4 |

## §8 Karantina (quarantine) kuralları

Karantina, flaky testin **geçici olarak** kapıyı bloklamasını önler; test silinmez, çalıştırma
sırasından çıkarılır. Bu depoda karantina mekanizması **yoktur**; soft gate (§9.3) farklı bir
araçtır.

| # | Kural | Zorunlu mu | Gerekçe | Denetim |
|---|---|---|---|---|
| 1 | Karar gerekçesi yazılır | evet | Görünürlük | Karantina kaydı |
| 2 | Sahip atanır | evet | Sahiplik | Kayıt alanı |
| 3 | Süre sınırlıdır | evet | Sonsuz karantina = silinmiş test | §8.4 |
| 4 | Haftalık gözden geçirme | evet | Erken çıkarma | Süreç |
| 5 | Çıkış koşulu tanımlıdır | evet | Objetif çıkış | §8.3 |

### §8.1 Karantina vs soft gate

| # | Özellik | Karantina | Soft gate (mevcut) | Fark |
|---|---|---|---|---|
| 1 | Kapsam | Tek test/grup | Tüm job | Kapsam farkı |
| 2 | Efekt | Test koşmaz | Job koşar ama bloklamaz | Efekt farkı |
| 3 | Kanıt | Karantina kaydı | `continue-on-error: true` | ci.yml Satır 167/183 |
| 4 | Süre | Sınırlı | Eşik büyüyünce kapanır | ci.yml Satır 166 |
| 5 | Bu depoda | ❌ yok | ✅ var | ⚠️ VERIFICATION REQUIRED |

### §8.2 Karantina girişi — koşullar

| # | Koşul | Ölçüm | Sonuç | Not |
|---|---|---|---|---|
| 1 | 5 koşumda 1-2 flaky | §7.1 | Karantina adayı | Gerekçe yazılır |
| 2 | Kod sahibi onayı | Süreç | Karantina | §8 kural 2 |
| 3 | Gerçek hata şüphesi | İnceleme | Karantina DEĞİL | §4.2 satır 2 |
| 4 | Performans aşımı | Ölçüm | Karantina değil | performance/ |

### §8.3 Karantina çıkışı — koşullar

| # | Koşul | Ölçüm | Sonuç | Not |
|---|---|---|---|---|
| 1 | 10 koşumda 0 flaky | Tekrar ölçüm | Çıkış | Ana çıkış |
| 2 | Kök neden düzeltildi | Diff | Çıkış | Kod kanıtı |
| 3 | Ortam düzeltildi | Ortam kaydı | Çıkış | §7 satır 4 |
| 4 | Süre doldu, neden yok | — | Çıkış + uyarı | §8.4 |

### §8.4 Karantina süresi kuralı

| # | Süre | Karar | Aksiyon |
|---|---|---|---|
| 1 | 2 hafta | İlk gözden geçirme | Düzeltme planı |
| 2 | 1 ay | İkinci gözden geçirme | Sahip değişimi |
| 3 | 6 ay | Üst sınır | Test silinir ya da yeniden yazılır (onayla) |
| 4 | Sonsuz | Yasak | §12 yasak 6 |

## §9 Retry ve zincir politikası (repo mevcut durumu)

### §9.1 Retry — E2E (Playwright)

| # | Alan | Yerel | CI | Kanıt |
|---|---|---|---|---|
| 1 | Retry sayısı | 0 | 2 | playwright.config.ts Satır 20 |
| 2 | Kanıt (retry'da) | — | trace: on-first-retry | Satır 30 |
| 3 | Ekran görüntüsü | — | yalnız hata anı | Satır 31 |
| 4 | Video | — | Hata anında sakla | Satır 32 |
| 5 | CI tekelleştirme | — | workers = 1 | Satır 21 |

### §9.2 Retry — PHP testleri

| # | Alan | Durum | Kanıt | Sonuç |
|---|---|---|---|---|
| 1 | PHPUnit retry | Yapılandırma tespit edilmedi | phpunit.xml (Satır 2-7) | ⚠️ VERIFICATION REQUIRED |
| 2 | İlk hatada durma | Kapalı (`stopOnFailure=false`) | shared/phpunit.xml Satır 6 | Tam rapor |
| 3 | Risky kapısı | Açık (auth) | auth phpunit.xml Satır 6 | Bloklar |
| 4 | Uyarı kapısı | Açık (auth) | auth phpunit.xml Satır 7 | Bloklar |

### §9.3 needs zinciri ve soft gate

| # | İş | needs | Soft | ci.yml satırı | Efekt |
|---|---|---|---|---|---|
| 1 | php-lint | — | hayır | 25-28 | Zincir başlangıcı |
| 2 | php-test | php-lint | hayır | 67-68 | Lint kırmızıysa test çalışmaz |
| 3 | composer-audit | — | hayır | 111-113 | Bağımsız |
| 4 | js-test | — | hayır | 142-145 | JS birimi |
| 5 | js-coverage | js-test | evet (167) | 160-162 | Görünür-kırmızı |
| 6 | js-e2e | — | evet (183) | 178-183 | Görünür-kırmızı |

### §9.4 Paralel koşu yarışı

| # | Davranış | Kanıt | Efekt | Kabul |
|---|---|---|---|---|
| 1 | Aynı ref için tek koşu | ci.yml Satır 17-19 | Eski koşu iptal | ✅ |
| 2 | `concurrency.group` | Satır 18 | Workflow + ref anahtarı | ✅ |
| 3 | `cancel-in-progress` | Satır 19 | Eski koşu iptal edilir | ✅ |
| 4 | push + PR aynı anda | Satır 10-12 | İki koşu olur, group ayırır | ✅ |

## §10 Kod örnekleri — repo gerçek kodu

### §10.1 Retry + kanıt — playwright.config.ts Satır 19-32

```typescript
forbidOnly: !!process.env.CI,
retries: process.env.CI ? 2 : 0,
workers: process.env.CI ? 1 : undefined,
reporter: [
  ['html'],
  ['list'],
  ['json', { outputFile: 'test-results/e2e-results.json' }],
],

use: {
  baseURL: 'http://home.coremusic.net:81',
  trace: 'on-first-retry',
  screenshot: 'only-on-failure',
  video: 'retain-on-failure',
},
```

**Dosya: `assets.coremusic.net/playwright.config.ts` / Satır: 19 · 20 · 21 · 22-26 · 30 · 31 · 32**

### §10.2 İptal edilen koşu — ci.yml Satır 17-19

```yaml
concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true
```

**Dosya: `.github/workflows/ci.yml` / Satır: 17 · 18 · 19**

### §10.3 Zincir — ci.yml Satır 67-68 ve 160-162

```yaml
  php-test:
    name: PHP Tests (${{ matrix.project }})
    needs: php-lint
```

```yaml
  js-coverage:
    name: JS Coverage (gate — soft, A7)
    needs: js-test
```

**Dosya: `.github/workflows/ci.yml` / Satır: 67-68 · 160-162**

### §10.4 Soft gate — ci.yml Satır 182-183

```yaml
    # A6: e2e lock'a alındı; ilk koşular bilinen-riskli → soft job.
    continue-on-error: true
```

**Dosya: `.github/workflows/ci.yml` / Satır: 182 · 183**

### §10.5 Kapılar — auth phpunit.xml Satır 6-8

```xml
failOnRisky="true"
failOnWarning="true"
failOnPhpunitWarning="false"
```

**Dosya: `auth.coremusic.net/phpunit.xml` / Satır: 6 · 7 · 8**

### §10.6 Çok-dilli karşılıklar (12 dil)

> Regresyon = "daha önce çalışan bozuldu mu" → **golden/snapshot karşılaştırma** kalıbı 12 dilde.
> Repo'da golden dosyası YOK (§5.1 tescilli) — bu bölüm ekosistem karşılığıdır.
> Sürüm iddiası yazılmaz (bilgi amaçlı — testing-dogrulama.md §4).

| # | Dil | Karşılaştırma aracı | Komut |
|---|---|---|---|
| 1 | PHP | phpunit-snapshot-assertions / elle golden okuma | `vendor/bin/phpunit --testsuite Regression` |
| 2 | TypeScript | Vitest `toMatchSnapshot()` | `npx vitest run -u` (bilinçli güncelleme) |
| 3 | Python | syrupy / pytest-snapshot | `pytest --snapshot-update` (bilinçli) |
| 4 | C# | Verify.Xunit / ApprovalTests | `dotnet test --filter Regression` |
| 5 | Java | ApprovalTests | `mvn test -Dtest=RegressionTest` |
| 6 | Go | elle golden dosya + `-update` bayrağı | `go test -run TestGolden -update` |
| 7 | C++ | Google Test + golden dosya (`--gtest_update`) | `./testler --gtest_filter=Golden.*` |
| 8 | Rust | insta crate | `cargo insta review` (fark onayı) |
| 9 | Ruby | rspec-snapshot / VCR kaset | `bundle exec rspec spec/regresyon_spec.rb` |
| 10 | Kotlin | approval/elle golden + JUnit 5 | `gradle test --tests GoldenTest` |
| 11 | Swift | swift-snapshot-testing (Point-Free) | `swift test --filter SnapshotTests` |
| 12 | C | elle golden + `memcmp` (Update bayrağı) | `./testler -u` (yalnız bilinçli) |

**PHP (elle golden — §5.1-§5.2 deseni):**

```php
public function testSarkiHtmlAltiniDegistirmedi(): void
{
    $html = $this->motor->ciz();                              // Act
    $altin = file_get_contents(__DIR__ . '/golden/sarki.html'); // Arrange: referans
    $this->assertSame($this->farkBul($altin, $html), '',       // §5.2 diff matrisi
        'Golden farkı — bilinçli ise golden güncellenir (§6.3)');
}
```

**TypeScript (Vitest snapshot):**

```ts
it('şarkı HTML golden değişmedi', () => {
  const html = motor.ciz();                       // Act
  expect(html).toMatchSnapshot();                 // __snapshots__/ ile karşılaştır
});
// Bilinçli güncelleme: npx vitest run -u → diff raporu commit edilir (§6.3)
```

**Python (syrupy snapshot):**

```python
def test_sarki_html_altinda_degisiklik_yok(sarki, snapshot):
    html = motor.ciz(sarki)                       # Act
    assert html == snapshot                       # golden karşılaştırma
# Güncelleme: pytest --snapshot-update (yalnız bilinçli — §6.3)
```

**C# (Verify):**

```csharp
[Fact]
public Task SarkiHtmlAltiniDegistirmedi()
{
    var html = motor.Ciz();                       // Act
    return Verify(html);                          // .verified dosyası = golden
}
// Fark: .received dosyası oluşur — verify.tool ile onaylanır (§6.3)
```

**Java (ApprovalTests):**

```java
@Test
void sarkiHtmlAltiniDegistirmedi() throws Exception {
    String html = motor.ciz();                          // Act
    Approvals.verifyHtml(html);                         // .approved = golden
}
// .received/.approved farkı CI'da fail — yeniden approve bilinçli (§8.2)
```

**Go (elle golden + -update):**

```go
var guncelle = flag.Bool("update", false, "golden dosyayı güncelle")

func TestSarkiHtmlAltiniDegistirmedi(t *testing.T) {
    html := motor.Ciz()                                  // Act
    yol := filepath.Join("testdata", "sarki.html")
    if *guncelle {                                       // §6.3 yalnız bilinçli
        require.NoError(t, os.WriteFile(yol, html, 0o644))
        return
    }
    altin, err := os.ReadFile(yol)                       // Arrange
    require.NoError(t, err)
    if !bytes.Equal(altin, html) {                       // §5.2 diff
        t.Errorf("golden değişti:\n--- beklenen\n%s\n--- gelen\n%s", altin, html)
    }
}
```

**C++ (Google Test golden):**

```cpp
TEST(GoldenTest, SarkiHtmlAltiniDegistirmedi) {
    std::string html = motor.Ciz();                        // Act
    std::string altin = DosyaOku("golden/sarki.html");     // Arrange
    EXPECT_EQ(altin, html) << FarkRaporu(altin, html);     // §5.2 diff raporu
}
// Güncelleme: ./testler --gtest_update (yalnız yerel, bilinçli — §6.3)
```

**Rust (insta):**

```rust
#[test]
fn sarki_html_altini_degistirmedi() {
    let html = motor.ciz();                  // Act
    insta::assert_snapshot!(html);           // .snap dosyası = golden
}
// Farkta: cargo insta review → onaylanana kadar CI kırmızı (§8.2)
```

**Ruby (VCR kaset / snapshot):**

```ruby
it 'şarkı HTML golden değişmedi' do
  html = motor.ciz                       # Act
  golden = File.read('golden/sarki.html') # Arrange
  expect(golden).to eq(html)             # §5.2 — fark diff raporu
end
# HTTP regresyonu: VCR kaset açmak yerine kaset -record=none ile sabit (§6.3)
```

**Kotlin (elle golden):**

```kotlin
@Test
fun `sarki html altini degistirmedi`() {
    val html = motor.ciz()                                       // Act
    val altin = File("golden/sarki.html").readText()             // Arrange
    assertEquals(altin, html) { "golden değişti — -update ile onayla" } // §5.2
}
```

**Swift (swift-snapshot-testing):**

```swift
func testSarkiHtmlAltiniDegistirmedi() {
    let html = motor.ciz()                          // Act
    assertSnapshot(matching: html, as: .text)       // __Snapshots__ = golden
}
// Güncelleme: isRecording = true (yalnız bilinçli — §6.3)
```

**C (elle golden + memcmp):**

```c
void test_sarki_html_altini_degistirmedi(void) {
    char *html = motor_ciz(&sarki);                 /* Act */
    char *altin = dosya_oku("golden/sarki.html");    /* Arrange */
    TEST_ASSERT_EQUAL_STRING(altin, html);          /* §5.2 — fark diff */
    /* Güncelleme: ./testler -u → golden yeniden yazılır (yalnız bilinçli) */
}
```

## §11 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | .github/workflows/ci.yml | 10-12 | Tetikleyiciler: push + pull_request |
| 2 | .github/workflows/ci.yml | 17-19 | Concurrency + cancel-in-progress |
| 3 | .github/workflows/ci.yml | 22 | PHP 8.4 sabitlemesi |
| 4 | .github/workflows/ci.yml | 25-28 | php-lint job + 15 dk |
| 5 | .github/workflows/ci.yml | 67-68 | php-test needs php-lint |
| 6 | .github/workflows/ci.yml | 76 | shared tam suit (boş args) |
| 7 | .github/workflows/ci.yml | 79-80 | auth yalnız `--testsuite Unit` |
| 8 | .github/workflows/ci.yml | 142-145 | js-test job + 10 dk |
| 9 | .github/workflows/ci.yml | 160-162 | js-coverage needs js-test |
| 10 | .github/workflows/ci.yml | 165-167 | A7 soft gate gerekçesi + continue-on-error |
| 11 | .github/workflows/ci.yml | 178-183 | js-e2e job + A6 soft gate |
| 12 | assets.coremusic.net/playwright.config.ts | 19 | forbidOnly (CI) |
| 13 | assets.coremusic.net/playwright.config.ts | 20 | retries: CI 2 / yerel 0 |
| 14 | assets.coremusic.net/playwright.config.ts | 25 | json rapor dosyası |
| 15 | assets.coremusic.net/playwright.config.ts | 30-32 | retry kanıtı: trace/screenshot/video |
| 16 | shared/phpunit.xml | 6 | stopOnFailure=false |
| 17 | shared/phpunit.xml | 8-33 | Testsuite seçimi (8 suit) |
| 18 | auth.coremusic.net/phpunit.xml | 6-7 | failOnRisky + failOnWarning |
| 19 | auth.coremusic.net/phpunit.xml | 10-12 | Integration suite kaldırma (dizin yok) |
| 20 | shared/composer.json | 47 | PHP test komutu |

## §12 Yasaklar

| # | Yasak | Gerekçe | Yakalama yöntemi |
|---|---|---|---|
| 1 | Flaky testi silmek | Kayıp kapsam | Karantina kaydı |
| 2 | Retry sayısını artırarak düzeltme | Kök neden gizlenir | §9.1 üst sınır |
| 3 | Soft gate'i "yeşil" saymak | Sahte güven | §9.3 |
| 4 | Kararsız testi regresyon saymak | Yanlış eskalasyon | §7.1 eşiği |
| 5 | Kanıtsız "flaky" iddiası | Önyargı | §7.1 ölçüm zorunlu |
| 6 | Süresiz karantina | Testin fiilen silinmesi | §8.4 |
| 7 | Karantinasız soft job | Süreç belirsizliği | §8.1 |
| 8 | Referansı elle oynatmak | Ölçüm bozulur | §5 kural 3 |
| 9 | Retry'ı 0 yapıp CI'a genellemek | Yerel/CI farkı | §9.1 |
| 10 | `cancel-in-progress`'i kaldırıp yarışı görmek | Gereksiz iş | §9.4 |

## §13 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K18-testing/unit | Determinism | Flaky azaltır | unit §7 | mevcut |
| 2 | K18-testing/integration | Sözleşme yeşili | Regresyon sinyali | Durum kodu seti | mevcut |
| 3 | K019-testing/performance | p95 baseline'ı | Performans regresyonu | Ölçüm kaydı | ⚠️ VERIFICATION REQUIRED |
| 4 | K18-testing/e2e | Retry + kanıt | Flaky kaynağı | config Satır 20, 30-32 | mevcut |
| 5 | K18-testing/coverage | Kapsam düşüşü sinyali | Yeni kod testsize kalır | js-coverage (soft) | mevcut |
| 6 | K17-devops/pipeline | Soft gate kararı | Kapı sertleştirme | ci.yml Satır 166 | açık |
| 7 | K017-observability/alerting | Kırmızı job uyarısı | Alarm | — | ⚠️ VERIFICATION REQUIRED |

## §14 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | Karantina mekanizması yok | Flaky ya bloklar ya gizlenir | yüksek | Karantina tasarımı | ⚠️ VERIFICATION REQUIRED |
| 2 | Golden reference yok | Regresyon kanıtı zayıf | yüksek | Referans tasarımı | ⚠️ VERIFICATION REQUIRED |
| 3 | Soft gate'ler süresiz | Sahte yeşil | orta | Eşik döngüsü | açık |
| 4 | PHP retry tanımı yok | Kararsızlık doğrudan kırmızı | orta | Retry kararı | ⚠️ VERIFICATION REQUIRED |
| 5 | Değişiklik-bazlı seçim yok | Gereksiz iş + eksik kapsam | orta | Seçim stratejisi | ⚠️ VERIFICATION REQUIRED |
| 6 | Zamanlanmış koşu yok | Uzun süreli flaky görünmez | orta | Schedule ekleme | ⚠️ VERIFICATION REQUIRED |
| 7 | Sharding yok | E2E büyüdükçe yavaşlar | düşük | Sharding kararı | ⚠️ VERIFICATION REQUIRED |
| 8 | Flaky ölçümü elle | Önyargı | orta | Otomatik sayım | ⚠️ VERIFICATION REQUIRED |

## §15 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | Koşu | push + PR koşusu yeşil | CI hattı | her PR | ci.yml Satır 10-12 |
| 2 | Zincir | Lint → test sırası | `needs` | her PR | Satır 68 |
| 3 | Retry | E2E ≤ 2 | config | her koşu | Satır 20 |
| 4 | Flaky | Flaky oranı ≤ %10 | §7.1 ölçümü | haftalık | §7.3 |
| 5 | Karantina | Süresiz karantina = 0 | Karantina kaydı | aylık | §8.4 |
| 6 | Kapı | Soft job sayısı azalıyor | ci.yml taraması | aylık | §9.3 |
| 7 | Referans | Fark raporu üretiliyor | Referans sistemi | her PR | §5 |

## §16 Karar şablonu — yeni regresyon testi seçimi

| # | Şablon alanı | Zorunlu | Değer | Denetim |
|---|---|---|---|---|
| 1 | Regresyon türü | evet | §4 tablosu | Kod incelemesi |
| 2 | Seçim stratejisi | evet | §6.1 tablosu | CI yapılandırması |
| 3 | Determinism | evet | unit §7 | Kod incelemesi |
| 4 | Kararsızlık planı | evet | §7-§8 | Süreç |
| 5 | Retry bütçesi | evet | §9.1 | config |
| 6 | Referans (golden) | evet | §5.1 (mevcut durum) | ⚠️ VERIFICATION REQUIRED |
| 7 | Kapı etkisi | evet | §9.3 | ci.yml |

## §17 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Playwright Retries sayfası, test yeniden deneme yapılandırmasını tanımlar | https://playwright.dev/docs/test-retries | 2026-10-12 | HTTP 200 doğrulandı |
| Playwright Sharding sayfası, testlerin parçalara bölünerek koşulmasını tanımlar | https://playwright.dev/docs/test-sharding | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 Error Handling sayfası, testlerde hata ve istisna davranışını tanımlar | https://docs.phpunit.de/en/10.5/error-handling.html | 2026-10-12 | HTTP 200 doğrulandı |
| PHPUnit 10.5 Annotations sayfası, test metadata işaretlerini (etiket/bağımlılık) tanımlar | https://docs.phpunit.de/en/10.5/annotations.html | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1206 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz.

## §18 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1206 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 + başlık kaydedildi | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya | 2026-10-12 |
| 4 | Repo kanıtları | dosya tam okuma + satır doğrulama | 20 satır eşleşti | 2026-10-12 |
| 5 | Soft gate satırları | ci.yml 167, 183 | birebir alındı | 2026-10-12 |
| 6 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 7 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 8 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 9 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 10 | group alanı | frontmatter | `kod` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
