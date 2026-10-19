---
title: "K17-devops/pipeline — CI/CD Aşamaları, Kapılar ve İrtibat (Concurrency) Politikası"
node_id: K17-devops/pipeline/cicd-asamalari
node_type: module
status: draft
parent_id: K17-devops/pipeline
children: []
depends_on:
  - K17-devops
  - K17-devops/pipeline
evidence:
  - ".github/workflows/ci.yml — ReadAllLines 203 satır, jobs bloğu satır 24, PHP Lint satır 26 (2026-10-12)"
  - ".github/workflows/ci.yml — continue-on-error satır 167 (JS Coverage) ve satır 183 (JS E2E) (2026-10-12)"
  - ".github/workflows/secret-scan.yml — ReadAllLines 31 satır, concurrency satır 14-16 (2026-10-12)"
  - "shared/composer.json — satır 47 test, satır 48 stan (phpstan level 5) (2026-10-12)"
  - "package.json — satır 5 test:js, satır 7 test:js:coverage, satır 8 test:e2e (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 döndürdü (2026-10-12)"
  - "URL envanteri ölçümü: .ai/architecture/** genelinde 1081 benzersiz URL, bu dosyada 0 kopya (2026-10-12)"
  - "626 mevcut .md dosyasında taban-URL çakışması 0 ölçüldü (2026-10-12)"
open_questions:
  - "ci.yml içinde artefakt yükleme (upload-artifact) adımı tespit edilmedi, kapsam dışı bırakıldı (⚠️ VERIFICATION REQUIRED)"
  - "Ortam bazlı dağıtım (deployment) işleri bu repo workflow dosyalarında yok; dağıtım nereden tetikleniyor UNKNOWN"
  - "JS Coverage ve JS E2E kapılarının ne zaman sert kapıya döneceği için tarih belirlenmedi (⚠️ VERIFICATION REQUIRED)"
group: mimari
---

# K17-devops/pipeline/cicd-asamalari

> **ELI10:** CI/CD hattı bir fabrika bandıdır: kod önce ucuz kontrolden (lint) geçer, sonra pahalı
> kontrolden (test, tarama), en sonda yayına gider. Bu dosya bandın duraklarını, hangi durağın
> yayını durdurabileceğini (sert kapı) hangisinin sadece uyarabileceğini (yumuşak kapı) ve işlerin
> ne zaman birbirini ezeceğini (concurrency) tek yerde tanımlar.

## §1 Amaç

Bu modül, CoreMusic depo'sunun **CI/CD hattının tek yetkili tanımıdır**. Dört karar bu dosyada
verilir: aşama sırası · kapı sertliği · paralelleme kuralı · iptal politikası. Yeni bir iş (job)
eklenirken önce bu dosyadaki tabloya satır eklenir, sonra workflow dosyasına yazılır.

| # | Bu dosyanın cevapladığı soru | Kararın adı | Karar nerede uygulanır |
|---|---|---|---|
| 1 | İşler hangi sırayla koşar? | Aşama sırası (stage order) | `ci.yml` `needs:` satırları |
| 2 | Hangi kapı yayını durdurur? | Kapı sertliği (gate strictness) | `continue-on-error` satırları |
| 3 | Matrix nasıl paralelleşir? | Matrix stratejisi | `ci.yml` `strategy.matrix` |
| 4 | Aynı ref için ne koşar? | concurrency grubu | `secret-scan.yml` satır 14-16 |
| 5 | Bağımlılık indirimi tekrarlanır mı? | Cache politikası | `actions/cache@v4` satır 41 ve 93 |
| 6 | Sürüm açığı denetimi kimde? | Audit aşaması | `Composer Audit` işi satır 111 |

**Hedefler (ölçülebilir):**

| # | Hedef | Ölçüm birimi | Hedef değer | Durum |
|---|---|---|---|---|
| H1 | Toplam iş (job) sayısı | adet | 6 (php-lint, php-test, composer-audit, js-test, js-coverage, js-e2e) | `ci.yml` jobs bloğunda mevcut |
| H2 | Sert kapı sayısı | adet | 4 (php-lint, php-test, composer-audit, js-test) | `continue-on-error` yalnız iki işte var |
| H3 | Yumuşak kapı sayısı | adet | 2 (js-coverage satır 167, js-e2e satır 183) | kodda kanıtlanmış |
| H4 | Composer cache etkinliği | iş | php-lint ve php-test içinde `actions/cache@v4` | satır 41 ve 93 |
| H5 | Secret tarama ayrı workflow | adet | 1 (`secret-scan.yml`) | diskte mevcut |
| H6 | Artefakt (artifact) saklama süresi | gün | bu depoda iş tanımı tespit edilmedi | ⚠️ VERIFICATION REQUIRED |

## §2 Kapsam sınırı

| Alan | Bu dosyada | Bu dosyada DEĞİL |
|---|---|---|
| Aşama sırası ve `needs` grafiği | ✅ 6 işin sırası ve bağımlılıkları | ❌ Dağıtım stratejisi (blue/green) → `deployment/` |
| Kapı sertliği (sert/yumuşak) | ✅ `continue-on-error` kararı ve gerekçesi | ❌ Sürüm yayını adımları → `deployment/` |
| Cache politikası | ✅ Composer ve npm önbelleği | ❌ Görüntü (image) önbelleği → `containers/` |
| concurrency / iptal | ✅ grup adı ve `cancel-in-progress` | ❌ Sırada bekleyen iş kuyruğu → UNKNOWN |
| Sürüm açığı denetimi | ✅ `composer audit` işi | ❌ Bağımlılık güncelleme otomasyonu → UNKNOWN |
| Secret tarama | ✅ `secret-scan.yml` referans olarak | ❌ Tarama kural seti ve politikası → `secrets/` |
| Kod kalitesi (statik analiz) | ✅ PHPStan level 5 kapısı | ❌ Kural seviyesi ayarları → K18-testing |

**Sınır kararı:** dağıtım (deployment) işleri bu depoda workflow dosyası olarak bulunmadığı için
`deployment/` alt dosyası yalnız strateji tanımını içerir; mevcut hattın devamı olarak sunulmaz.

## §3 Terimler ve kısaltmalar

| Terim | İngilizce karşılığı | Bu dosyadaki anlamı | İlgili satır |
|---|---|---|---|
| Aşama | stage | İşlerin mantıksal grubu (lint, test, audit) | `ci.yml` satır 24 |
| İş | job | Tek makinede koşan adım kümesi | `ci.yml` satır 26, 67, 111 |
| Adım | step | İş içinde tek komut veya action | `ci.yml` satır 40, 154 |
| Sert kapı | blocking gate | Başarısızlık tüm koşuyu düşürür | `php-lint` işi (satır 26) |
| Yumuşak kapı | soft gate | Başarısızlık koşuyu düşürmez, sadece işaretler | `continue-on-error: true` (satır 167, 183) |
| Matrix | matrix | Aynı işin farklı değerlerle çoğaltılması | `strategy.matrix` (satır 71-73) |
| İrtibat | concurrency | Aynı gruptan yeni iş başlayınca eskisini iptal | `secret-scan.yml` satır 14-16 |
| Önbellek | cache | Bir sonraki koşuda yeniden indirilmeyen bağımlılık | `actions/cache@v4` (satır 41, 93) |
| Artefakt | artifact | İşler arası dosya aktarımı | bu depoda tespit edilmedi |
| Denetim | audit | Bilinen sürüm açığı taraması | `Composer Audit` (satır 111) |

## §4 Aşama modeli — mevcut hat

CoreMusic'in hattı **dört aşamalıdır**; her aşama bir öncekinin çıktısına güvenir. Sıra
`needs:` anahtarlarıyla sabitlenmiştir, kendiliğinden paralel koşmaz.

| # | Aşama | İşler (job) | Sertlik | Bağımlılık | Başarı ölçütü |
|---|---|---|---|---|---|
| A1 | Statik kontrol | php-lint | sert | yok (ilk) | PHPStan level 5 ihlali yok |
| A2 | Birim test | php-test (matrix), js-test | sert | A1 (`needs: php-lint` satır 68) | PHPUnit ve Vitest yeşil |
| A2b | Sürüm açığı | composer-audit | sert | bağımsız (satır 111) | bilinen CVE ihlali yok |
| A3 | Ölçüm (yumuşak) | js-coverage | yumuşak (satır 167) | js-test (`needs: js-test` satır 162) | rapor üretilir, kapı değil |
| A4 | Uçtan uca (yumuşak) | js-e2e | yumuşak (satır 183) | bağımsız (satır 179) | Playwright koşusu tamamlanır |
| A5 | Sızıntı taraması | gitleaks (ayrı workflow) | sert | yok (push/PR tetikli) | secret-bulunmadı |

**Aşama kuralı 1:** yeni iş, mevcut aşamalardan birine aittir; yeni aşama (A6+) yalnız kapı
tanımı ile birlikte eklenir (§10).

**Aşama kuralı 2:** `needs` bağımlılığı olmayan işler paralel koşar; bu bilinçli bir hız
kazancıdır, hat sırası değildir. `composer-audit` (satır 111) bu yüzden `php-lint` beklemez.

**Aşama kuralı 3:** yumuşak kapı aşaması (A3, A4) hiçbir zaman bir sonraki aşamanın girdisi
değildir; yumuşak kapı başarısızlığı yayını engelleyemez.

### §4.1 Aşama grafiği — okuma kuralı

Aşama bağımlılıkları şu oklarla okunur; ok yönü tersine çevrilemez:

| # | Kaynak iş | Hedef iş | Bağ | Anlamı | Satır |
|---|---|---|---|---|---|
| 1 | php-lint | php-test | `needs` | Statik kontrol yeşil olmadan test koşmaz | 68 |
| 2 | js-test | js-coverage | `needs` | Ölçüm, testin çıktısını ölçer | 162 |
| 3 | composer-audit | bağımsız | yok | Kimseyi beklemez, paralel koşar | 111 |
| 4 | js-e2e | bağımsız | yok | Kimseyi beklemez, yumuşak kapıdır | 179-183 |

**Grafik kuralı:** döngü (cycle) yasaktır; bir iş aynı koşuda iki kez tetiklenemez ve hat sonucu
tek adımda üretilir (başarı: tüm sert kapılar yeşil).

**Tek sonuç kuralı:** yumuşak kapılar iş sonucu tablosuna "başarısız" olarak düşebilir; bu, koşunun
genel sonucunu değiştirmez (§9). Bu yüzden hat sonu yeşili tek başına yayın izni değildir.

## §5 Mevcut iş envanteri — ci.yml

Altı işin tamamı `.github/workflows/ci.yml` içinde tanımlıdır. Toplam dosya uzunluğu 203
satırdır (`[IO.File]::ReadAllLines`).

| # | İş adı (id) | Görünen ad | Satır | Çalışma sistemi | Komut özeti | Sertlik |
|---|---|---|---|---|---|---|
| 1 | php-lint | PHP Lint | 26 | ubuntu-latest (satır 27) | PHPStan `composer stan` (satır 62) | sert |
| 2 | php-test | PHP Tests (matrix.project) | 67 | ubuntu-latest (satır 69) | PHPUnit `--testsuite Unit` (satır 80) | sert |
| 3 | composer-audit | Composer Audit | 111 | ubuntu-latest (satır 112) | `composer audit` üç projede (satır 123, 129, 135) | sert |
| 4 | js-test | JS Unit Tests | 143 | ubuntu-latest (satır 144) | Vitest unit (satır 157) | sert |
| 5 | js-coverage | JS Coverage (gate — soft, A7) | 161 | ubuntu-latest (satır 163) | Vitest coverage (satır 175) | yumuşak (satır 167) |
| 6 | js-e2e | JS E2E (Playwright — soft, A6) | 179 | ubuntu-latest (satır 180) | `npm run test:e2e` (satır 203) | yumuşak (satır 183) |

**Matrix detayı:** `php-test` işi `strategy.matrix` (satır 71) ile iki projede çoğaltılır —
`shared` ve `auth.coremusic.net`. PHPUnit adımı satır 104, auth projesinde `--testsuite Unit`
seçicisi satır 80'de geçer.

**Tetikleyiciler:** workflow `on:` bloğu satır 10'dadır; `push` (satır 11) ve `pull_request`
(satır 12) — yani hem dal itimi hem PR açılışı aynı hattı çalıştırır.

## §6 Paralelleme, bağımlılık ve matrix kuralları

| # | Kural | Gerekçe | Uygulama | İhlal sonucu |
|---|---|---|---|---|
| 1 | Statik kontrol testten önce koşar | Hatalı kod pahalı testi boşa tüketmesin | `needs: php-lint` (satır 68) | gereksiz koşu süresi |
| 2 | Matrix yalnız bağımlılığı değişen işlerde | Aynı komutun iki kez koşması maliyet | `matrix.project` (satır 73) | çift iş gücü tüketimi |
| 3 | Ölçüm işi test işinden sonra | Ölçüm anlamsızsa test önce bilgi verir | `needs: js-test` (satır 162) | yanlış öncelik |
| 4 | Bağımsız işler beklemez | Toplam süreyi kısaltır | composer-audit bağımsız (satır 111) | yavaş hat |
| 5 | Aynı ref için eski koşu iptal edilir | Aynı commit iki kez koşmasın | concurrency grubu (`secret-scan.yml` satır 14) | çift maliyet |
| 6 | Yeni iş eklenirken kapı sertliği belirtilir | Kapı belirsizliği sessiz risk üretir | §10 şablonu | tanımsız kapı |

**Paralel üst sınır:** Runner başına eşzamanlılık sağlayıcı (GitHub) tarafından belirlenir; bu
depoda açık bir eşzamanlılık sınırı tanımı yoktur → UNKNOWN.

## §7 Cache (önbellek) ve artefakt politikası

| # | Nesne | Anahtar | Nerede | Satır | Karar |
|---|---|---|---|---|---|
| 1 | Composer bağımlılıkları | PHP sürümü + lock dosyası | php-lint işi | 40-41 (`actions/cache@v4`) | etkin |
| 2 | Composer bağımlılıkları | PHP sürümü + lock dosyası | php-test işi | 92-93 (`actions/cache@v4`) | etkin |
| 3 | Composer cache yolu | `~/.cache/composer` | php-test işi | 95 | yolu sabit |
| 4 | npm bağımlılıkları | npm yapılandırması | js-e2e işi | 195 (`cache: npm`) | etkin |
| 5 | npm bağımlılıkları (js-test) | npm yapılandırması | js-test işi | cache adımı tespit edilmedi | ⚠️ VERIFICATION REQUIRED |
| 6 | Test raporu artefaktı | yok | tüm işler | upload-artifact adımı tespit edilmedi | ⚠️ VERIFICATION REQUIRED |

**Cache kuralı:** cache anahtarı lock dosyasına bağlıdır; lock değişmediği sürece bağımlılık
indirimi atlanır. Lock değişirse eski anahtar devre dışı kalır ve yeniden indirme başlar.

**Artefakt kuralı (henüz uygulanmadı):** coverage raporu ve Playwright izleri (trace) bir
sonraki işe taşınacaksa `upload-artifact` adımı zorunludur; bu depoda böyle bir adım
tespit edilmediği için coverage raporu koşu sonunda kaybolur → kapsam dışı ve açık soru.

## §8 concurrency (irtibat) ve iptal politikası

İptal politikası bu depoda **tek bir workflow içinde tanımlıdır**: `secret-scan.yml`.

| Alan | Değer | Satır | Anlamı |
|---|---|---|---|
| `concurrency.group` | workflow adı + ref adı | 15 | Aynı dal/PR için tek grup |
| `cancel-in-progress` | `true` | 16 | Yeni itiş, gruptaki eski koşuyu iptal eder |
| `permissions.contents` | `read` | 12 | Tarama işi yazma yetkisi almaz |
| `timeout-minutes` | 10 | 22 | Süresiz koşu engellenir |

**Neden `ci.yml` içinde yok:** `ci.yml` içinde `concurrency` bloğu tespit edilmedi. Sonuç: aynı
dal üzerine art arda iki itiş, CI hattını iki kez koşturur → bu bir optimizasyon açığıdır,
doğru davranış garantisi değildir (⚠️ VERIFICATION REQUIRED: kasıtlı mı, eksik mi).

**İptal kuralı tablosu:**

| # | Durum | Davranış | Karar | Dayanak |
|---|---|---|---|---|
| 1 | Aynı ref, secret-scan | eski koşu iptal | istenen | `cancel-in-progress: true` (satır 16) |
| 2 | Aynı ref, ci.yml | iki koşu da devam | iyileştirme adayı | concurrency tanımı yok |
| 3 | PR kapanınca koşu | sağlayıcı davranışı | UNKNOWN | bu depoda tanımlanmamış |
| 4 | Manuel iptal | çalıştırıcı yetkisi | her koşuda mevcut | platform davranışı |

## §9 Kapılar (gates) — sertlik matrisi

Kapı sertliği tek bir anahtarla belirlenir: `continue-on-error`. `true` yazılan iş **yumuşak**
kapıdır; yazılmayan iş **serttir** ve başarısızlık tüm koşuyu düşürür.

| # | İş | `continue-on-error` | Kapı | Başarısızlık etkisi | Dayanak satır |
|---|---|---|---|---|---|
| 1 | php-lint | yok (false) | sert | hat durur, PR birleşemez | 26 |
| 2 | php-test | yok (false) | sert | hat durur, PR birleşemez | 67 |
| 3 | composer-audit | yok (false) | sert | hat durur, PR birleşemez | 111 |
| 4 | js-test | yok (false) | sert | hat durur, PR birleşemez | 143 |
| 5 | js-coverage | `true` | yumuşak | koşu yeşil kalır, sadece işaret | 167 |
| 6 | js-e2e | `true` | yumuşak | koşu yeşil kalır, sadece işaret | 183 |

**Kapı isimleri kendi tanımını taşıyor:** js-coverage işinin adı `"JS Coverage (gate — soft, A7)"`
(satır 161), js-e2e işinin adı `"JS E2E (Playwright — soft, A6)"` (satır 179) — yani yumuşaklık
kodda değil, iş adında da beyan edilmiştir.

**Risk:** yumuşak kapılar süresiz yumuşak kalırsa ölçüm ve E2E geri bildirimi üretir ama hiç
engellemez; kapı olmaktan çıkar. §14 bu riski açık soru olarak kaydeder.

## §10 Yeni aşama / yeni iş ekleme kuralı

Yeni bir iş, şu şablonla eklenir. Şablon zorunludur; eksik alanlı iş kabul edilmez.

| # | Şablon alanı | Zorunluluk | Örnek değer | Denetim |
|---|---|---|---|---|
| 1 | Aşama (A1-A5) | zorunlu | A2 | §4 tablosunda karşılığı var mı |
| 2 | Kapı sertliği | zorunlu | sert / yumuşak | `continue-on-error` bilinçli mi |
| 3 | `needs` bağımlılığı | zorunlu (yoksa gerekçe) | php-lint | döngü (cycle) yok mu |
| 4 | Cache adımı | gerekiyorsa | `actions/cache@v4` | anahtar lock dosyasına bağlı mı |
| 5 | concurrency | aynı ref tekrarlıysa | grup + cancel | §8'e uygun mu |
| 6 | Zaman aşımı | zorunlu | `timeout-minutes` | sınırsız koşu yok mu |

**Yasaklar:**

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | Yumuşak kapıyı `needs` girdisi yapmak | Engellemeyen iş, sonraki işi garanti edemez (§4 kural 3) |
| 2 | Sırasız yeni iş eklemek | Sıra grafiği okunamaz hale gelir |
| 3 | Cache anahtarını lock dosyasından koparmak | Bayat bağımlılık kullanılır |
| 4 | Sert kapıyı sessizce yumuşatmak | Kapı sertliği onaysız değiştirilemez |
| 5 | Parola/token'ı workflow'a gömmek | `secrets/` dosyasının konusudur, burada yasak |

## §11 Kod örnekleri — repo gerçek kodu

### §11.1 Kapı sertliği — js-coverage (ci.yml satır 161-167)

```yaml
    name: JS Coverage (gate — soft, A7)
    needs: js-test
    runs-on: ubuntu-latest
    continue-on-error: true
```

`needs: js-test` ölçümün testten sonra geldiğini, `continue-on-error: true` ise başarısızlık
yolunun açık (yumuşak) olduğunu gösterir.

### §11.2 concurrency — secret-scan.yml satır 14-16

```yaml
concurrency:
  group: ${{ github.workflow }}-${{ github.ref }}
  cancel-in-progress: true
```

Grup adı workflow + ref birleşimidir; yeni itiş aynı gruptaki eski koşuyu iptal eder.
(Aynı kalıp `ci.yml` içinde tespit edilmedi — §8.)

### §11.3 Cache adımı — ci.yml satır 40-41

```yaml
      - name: Cache Composer
        uses: actions/cache@v4
```

`php-lint` işinde bağımlılık indirimi önbelleklenir; `php-test` işinde ikinci kez (satır 92-93)
aynı koruma tekrarlanır.

### §11.4 Yerel eşdeğerler — composer.json ve package.json

```json
"scripts": {
    "test": "phpunit",
    "stan": "phpstan analyse src --level=5"
}
```

`shared/composer.json` satır 46-48. CI'daki PHPStan adımı (satır 62) bu yerel scripti çağırır;
aynı komut hem yerelde hem hatta koşar, iki ayrı tanım yoktur.

```json
"scripts": {
    "test:js": "vitest run --root assets.coremusic.net",
    "test:js:coverage": "vitest run --root assets.coremusic.net --coverage",
    "test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"
}
```

`package.json` satır 4-8. js-test (satır 157), js-coverage (satır 175) ve js-e2e (satır 203)
işleri bu scriptlere bağlıdır.

## §12 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlıyor |
|---|---|---|---|
| 1 | .github/workflows/ci.yml | 10-12 | Tetikleyiciler: `on:` + `push` + `pull_request` |
| 2 | .github/workflows/ci.yml | 24 | `jobs:` bloğu başlangıcı, 6 iş |
| 3 | .github/workflows/ci.yml | 26-27 | php-lint işi, `runs-on: ubuntu-latest` |
| 4 | .github/workflows/ci.yml | 40-41 | Cache Composer, `actions/cache@v4` |
| 5 | .github/workflows/ci.yml | 62 | PHPStan adımı: `composer stan` |
| 6 | .github/workflows/ci.yml | 67-68 | php-test işi + `needs: php-lint` |
| 7 | .github/workflows/ci.yml | 71-73 | `strategy.matrix` — proje çoğaltması |
| 8 | .github/workflows/ci.yml | 80 | `--testsuite Unit` — auth projesi seçicisi |
| 9 | .github/workflows/ci.yml | 104 | PHPUnit adımı |
| 10 | .github/workflows/ci.yml | 111-112 | Composer Audit işi |
| 11 | .github/workflows/ci.yml | 143-144 | JS Unit Tests işi |
| 12 | .github/workflows/ci.yml | 161-167 | js-coverage başlığı + `continue-on-error: true` |
| 13 | .github/workflows/ci.yml | 179-183 | js-e2e başlığı + `continue-on-error: true` |
| 14 | .github/workflows/ci.yml | 203 | `npm run test:e2e` son adımı |
| 15 | .github/workflows/secret-scan.yml | 14-16 | concurrency grubu + `cancel-in-progress: true` |
| 16 | .github/workflows/secret-scan.yml | 22 | `timeout-minutes: 10` |
| 17 | .github/workflows/secret-scan.yml | 26 | `fetch-depth: 0` — tam geçmiş taraması |
| 18 | .github/workflows/secret-scan.yml | 29 | `gitleaks/gitleaks-action@v2` |
| 19 | shared/composer.json | 46-48 | `test` ve `stan` scriptleri |
| 20 | package.json | 4-8 | `test:js`, `test:js:coverage`, `test:e2e` |

**Dosya uzunlukları (2026-10-12, `[IO.File]::ReadAllLines`):** `ci.yml` 203 ·
`secret-scan.yml` 31 · `shared/composer.json` 50 · `package.json` 18.

## §13 Entegrasyon ve bağımlılıklar

| # | Komşu düğüm | Ne alır | Ne verir | Arayüz | Durum |
|---|---|---|---|---|---|
| 1 | K16-observability/logging | Log formatı kararı | Hat loglarının yazılacağı format | standart yok (workflow logu) | ⚠️ VERIFICATION REQUIRED |
| 2 | K16-observability/alerting | Uyarı kuralı | CI kapısının uyarı üretmesi | uyarı akışı yok | açık |
| 3 | K17-devops/deployment | Kapı sonucu | Yayına yalnız sert kapı geçince çıkılır | manuel onay yok | ⚠️ VERIFICATION REQUIRED |
| 4 | K17-devops/containers | Görüntü adı | CI içinde build tetiklenmez | workflow'da build yok | tespit edilmedi |
| 5 | K17-devops/secrets | gitleaks sonucu | Sızıntı varsa kapı düşer | ayrı workflow | mevcut (`secret-scan.yml`) |
| 6 | K18-testing/performance | Coverage ve E2E sonucu | Ölçüm kapılarının girdisi | `continue-on-error` | kısmen (yumuşak) |
| 7 | K18-testing/regression | Seçici (testsuite) tanımı | PHPUnit testsuite seçicileri | `--testsuite Unit` | mevcut (satır 80) |

**Yukarı akış:** kod → PR → `push`/`pull_request` tetikleyicisi (satır 10-12).
**Aşağı akış:** sert kapılar yeşilse birleşme (merge) serbest; dağıtım tetikleyicisi bu depoda
tanımlı değildir → UNKNOWN.

## §14 Riskler ve açık sorular

| # | Risk / Soru | Etki | Olasılık | Eylem | Durum |
|---|---|---|---|---|---|
| 1 | `ci.yml` içinde concurrency yok | Aynı ref iki kez koşar, maliyet | orta | §8'e göre grup ekleme | açık |
| 2 | js-coverage yumuşak kapı | Kapsam düşüşü engellenmez | yüksek | Sertleştirme kararı (onay gerekli) | açık |
| 3 | js-e2e yumuşak kapı | Regresyon yayına girebilir | yüksek | Sertleştirme kararı (onay gerekli) | açık |
| 4 | Artefakt adımı yok | Coverage raporu kaybolur | orta | upload-artifact ekleme | ⚠️ VERIFICATION REQUIRED |
| 5 | js-test için cache adımı yok | Her koşuda yeniden indirme | düşük | Cache ekleme | ⚠️ VERIFICATION REQUIRED |
| 6 | Dağıtım işi yok | Hat yayında biter | orta | deployment düğümü ile entegrasyon | UNKNOWN |
| 7 | Runner eşzamanlılık sınırı bilinmiyor | Kapasite planı yapılamaz | orta | Platform kılavuzu okunması | UNKNOWN |
| 8 | PHPStan sürüm doğrulanmadı | Kapı davranışı değişebilir | düşük | `composer.json` satır 25 okunur | açık (satır mevcut) |

## §15 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı | Frekans | Kanıt |
|---|---|---|---|---|---|
| 1 | İş (job) | 6 iş tanımlı | `jobs:` sayımı | her PR | `ci.yml` satır 24-183 |
| 2 | Kapı | 2 yumuşak, 4 sert | `continue-on-error` taraması | her PR | satır 167, 183 |
| 3 | Cache | Composer cache 2 işte | `actions/cache@v4` sayımı | haftalık | satır 41, 93 |
| 4 | İrtibat | secret-scan'te grup aktif | `concurrency` bloğu | aylık | `secret-scan.yml` satır 14 |
| 5 | Süre | İş başına süre | platform koğuş süresi | her koşu | bu depoda kayıt yok → UNKNOWN |
| 6 | Başarı oranı | Yeşil koşu oranı | platform geçmişi | aylık | bu depoda kayıt yok → UNKNOWN |

## §16 Yerel eşdeğer komutlar (hat öncesi)

| # | Aşama | Yerel komut | Nerede | Karşılık |
|---|---|---|---|---|
| 1 | Statik kontrol | `composer stan` | `shared/` | php-lint işi (satır 62) |
| 2 | Birim test (PHP) | `composer test` | `shared/` | php-test işi (satır 104) |
| 3 | Birim test (JS) | `npm run test:js` | repo kökü | js-test işi (satır 157) |
| 4 | Kapsam ölçümü | `npm run test:js:coverage` | repo kökü | js-coverage işi (satır 175) |
| 5 | Uçtan uca | `npm run test:e2e` | repo kökü | js-e2e işi (satır 203) |
| 6 | Sürüm açığı | `composer audit` | `shared/` | composer-audit işi (satır 111) |

**Kural:** yerel komut ile CI komutu aynı scripti çağırmalıdır; iki ayrı komut yazılırsa
lokalde yeşil, hatta kırmızı sonuç doğar (§11.4 bu eşitliği kanıtlar).

### §16.1 Yerel ön-koşullar — hangi araç hangi sürümle

| # | Araç | Sürüm kaynağı | Sürüm (repo kanıtı) | Hattaki karşılığı | Not |
|---|---|---|---|---|---|
| 1 | PHP | `ci.yml` satır 32-33 (`setup-php@v2`) | iş içinde seçilir | PHP Lint / PHP Tests | yerel sürüm UNKNOWN |
| 2 | Composer | PHP ile birlikte | kurulum adımı satır 48 | `composer install` | lock dosyası zorunlu |
| 3 | PHPStan | `shared/composer.json` satır 25 | `phpstan/phpstan ^1.10` | `composer stan` | level 5 (satır 48) |
| 4 | PHPUnit | `shared/composer.json` satır 24 | `phpunit/phpunit ^10.5` | `composer test` | testsuite seçici satır 80 |
| 5 | Node.js | workflow yapılandırması | sürüm tanımı `ci.yml` içinde ad olarak geçmiyor | js-test/js-coverage | sürüm UNKNOWN |
| 6 | Vitest | `package.json` satır 16 | `vitest ^5.0.3` | `npm run test:js` | coverage eklentisi satır 12 |
| 7 | Playwright | `package.json` satır 11 | `@playwright/test ^1.63.0` | `npm run test:e2e` | tarayıcı satır 199'da kurulur |
| 8 | gitleaks | `secret-scan.yml` satır 29 | `gitleaks/gitleaks-action@v2` | ayrı workflow | config dosyası diskte yok |

**Sürüm doğrulama notu:** GitHub Actions `@v2`/`@v4` gibi etiketleri sabitleyen etiketlerdir;
etiket arkasındaki kesin sürüm bu depoda doğrulanmadı → ⚠️ VERIFICATION REQUIRED.

## §17 Hata sınıflandırması ve yeniden deneme (retry) politikası

Hattaki her başarısızlık bir sınıfa girer. Sınıf, yeniden deneme hakkını ve sorumluyu belirler;
sınıfsız hata, aynı hatanın iki farklı büyüklükteki sonucunu aynı muameleyle karşılar.

| # | Hata sınıfı | Tipik belirti | Yeniden deneme | Sorumluluk | Kapı etkisi |
|---|---|---|---|---|---|
| 1 | Altyapı (runner) | Job hiç başlamadı, bağlantı hatası | evet, 2 kez | platform | sert kapıda 2. denemede geçebilir |
| 2 | Bağımlılık indirme | Composer/npm fetch hatası | evet, 1 kez | bağımlılık yönetimi | sert kapı düşer |
| 3 | Kod statik hata | PHPStan ihlali | hayır, düzeltme gerekir | geliştirici | sert kapı düşer |
| 4 | Test başarısız | PHPUnit/Vitest kırmızı | hayır, test veya kod düzeltmesi gerekir | geliştirici | sert kapı düşer |
| 5 | Sürüm açığı (CVE) | `composer audit` ihlali | hayır, sürüm yükseltmesi gerekir | bağımlılık yönetimi | sert kapı düşer |
| 6 | Zaman aşımı | Job `timeout-minutes` sınırında kesildi | hayır, kök neden gerekir | ilgili sahip | iş sonucu başarısız |
| 7 | Test kararsızlığı (flaky) | Aynı kod, farklı sonuç | hayır, test sabitlenir | test sahibi | yumuşak kapıda görünmez kalır |

### §17.1 Yeniden deneme kuralları

| # | Kural | İzin verilen sınıf | Üst sınır | Kayıt |
|---|---|---|---|---|
| 1 | Yeniden deneme yalnız altyapı sınıfında | sınıf 1-2 | 2 deneme | koğuş logu |
| 2 | Kod/test hataları otomatik tekrarlanmaz | sınıf 3-7 yasak | 0 | — |
| 3 | Manuel yeniden deneme yetkisi | çalıştırıcı sahibi | sınırsız (insan kararı) | koğuş geçmişi |
| 4 | İki ardışık altyapı hatası | sınıf 1-2 | olay kaydı açılır | olay kaydı |
| 5 | Yeniden deneme sonucu eski sonuçla çelişirse | kararsız test | flaky raporu | §18 tablosu |

**Gerekçe:** otomatik tekrar, kod kaynaklı hatayı gizleyerek yeşil görünüm üretir; bu depoda
`composer audit` ve test adımları için tekrar tanımlanmamıştır (workflow'da retry bloğu yok →
yeniden deneme yalnız platform varsayılanına bağlıdır: UNKNOWN).

### §17.2 Sınıf → sahip eşlemesi

| # | Hata sınıfı | Birincil sahip | İkincil sahip | Yanıt süresi hedefi |
|---|---|---|---|---|
| 1 | Altyapı | platform ekibi | devops | aynı iş günü |
| 2 | Bağımlılık indirme | devops | platform ekibi | aynı iş günü |
| 3 | Statik hata | kod sahibi | code-reviewer | PR içi |
| 4 | Test başarısız | kod sahibi | test sahibi | PR içi |
| 5 | Sürüm açığı | bağımlılık yönetimi | güvenlik | 7 gün (kritik CVE için 24 saat) |
| 6 | Zaman aşımı | ilgili sahip | performance-engineer | 2 iş günü |
| 7 | Kararsız test | test sahibi | QA | 5 iş günü |

## §18 Hattın sağlığı — izlenen ölçüler

Hat sağlıklıysa **kısa, tekrarlanabilir ve gürültüsüzdür**. Bu ölçüler bu depoda otomatik
toplanmamaktadır; tablo, neyin ölçülmesi gerektiğini tanımlar ve mevcut durumu dürüstçe işaretler.

| # | Ölçü | Tanım | Toplama | Hedef | Mevcut durum |
|---|---|---|---|---|---|
| 1 | Koşu süresi | İş başına dakika | platform koğuşu | php-test ≤ 5 dk | bu depoda kayıt yok → UNKNOWN |
| 2 | Yeşil oranı | Başarılı koşu / toplam | platform geçmişi | ≥ %95 | bu depoda kayıt yok → UNKNOWN |
| 3 | İlk yeşil süresi | Push → yeşil | platform koğuşu | ≤ 10 dk | bu depoda kayıt yok → UNKNOWN |
| 4 | Yumuşak kapı başarısızlığı | js-coverage/js-e2e kırmızı sayısı | iş sonucu | haftada ≤ 1 | `continue-on-error` nedeniyle görünür değil |
| 5 | Sert kapı düşüş nedeni | Sınıf dağılımı (§17) | manuel sınıflandırma | sınıf 3-4 dominant | kayıt yok → UNKNOWN |
| 6 | Cache isabet oranı | Cache hit / toplam | `actions/cache` logu | ≥ %80 | loglarda → elde okunur |
| 7 | Ortalama onarım süresi | Yeşil → yeşil (aynı PR) | PR geçmişi | ≤ 1 iş günü | bu depoda kayıt yok → UNKNOWN |

### §18.1 Ölçüm toplama kararı

| # | Karar | Dayanak | Sonuç | Durum |
|---|---|---|---|---|
| 1 | Ölçüler platform logundan gelir | runner koğuşu okunur | okuma elle yapılır | ⚠️ VERIFICATION REQUIRED |
| 2 | Ölçüm için ayrı workflow gerekir | `ci.yml` içinde metrik adımı yok | teklif, uygulanmadı | açık |
| 3 | Yumuşak kapı metriği ayrı raporlanır | `continue-on-error` sonucu kaybolur | kapıyı sertleştirmeden görünür yapılamaz | açık |
| 4 | Ölçüm sahibi devops | kimse atanmadı | atama gerekli | açık |
| 5 | Hedefler ilk ölçüm sonrası revize edilir | taban ölçüm yok | hedefler tahmin değil, beyan | ⚠️ VERIFICATION REQUIRED |

### §18.2 Hat davranışı kararları

| # | Karar | Kural | Yürürlük | Değişiklik hakkı |
|---|---|---|---|---|
| 1 | Sert kapı onaysız yumuşatılamaz | §9 | yürürlükte | Vault Steward onayı |
| 2 | Yeni iş varsayılan olarak serttir | §10 | yürürlükte | mimari onay |
| 3 | Aynı ref için eski koşu iptali | §8 | yalnız secret-scan | devops onayı |
| 4 | Cache anahtarı lock dosyasına bağlı | §7 | yürürlükte | devops onayı |
| 5 | Dağıtım tetikleyicisi bu depoda yok | §13 | UNKNOWN | üst karar |

## §19 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| GitHub Actions bağımlılık önbelleği (caching dependencies) ayrı bir rehberle belgelenir | https://docs.github.com/en/actions/guides/caching-dependencies-to-speed-up-workflows | 2026-10-12 | HTTP 200 doğrulandı |
| Workflow verisi/artefakt saklama, işler arası dosya aktarımının resmi yoludur | https://docs.github.com/en/actions/using-workflows/storing-workflow-data-as-artifacts | 2026-10-12 | HTTP 200 doğrulandı |
| Workflow sözdizimi belgesi `jobs.<job_id>.concurrency` bölümünü tanımlar | https://docs.github.com/en/actions/writing-workflows/workflow-syntax-for-github-actions#jobsjob_idconcurrency | 2026-10-12 | HTTP 200 doğrulandı |
| Azure Pipelines başlangıç rehberi CI/CD hattı kavramını tanımlar | https://learn.microsoft.com/en-us/azure/devops/pipelines/get-started/pipelines-get-started?view=azure-devops | 2026-10-12 | HTTP 200 doğrulandı |

**Not:** Dört URL `.ai/architecture/**` envanterinde (1081 benzersiz URL) ve diğer 11 yeni
derin-branch dosyasında bulunmaz; 626 mevcut .md dosyasında taban-URL çakışması 0'dır.

## §20 Doğrulama kaydı — bu dosya

| # | Ne ölçüldü | Araç | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | URL envanteri | regex + hashtable | 1081 benzersiz URL (temel ölçüm) | 2026-10-12 |
| 2 | Bu dosyanın URL'leri | `Invoke-WebRequest` | 4/4 HTTP 200 | 2026-10-12 |
| 3 | Envantere kopya | taban-URL karşılaştırması | 0 kopya (626 dosya) | 2026-10-12 |
| 4 | Repo kanıtları | numaralı döküm | 20 satır eşleşti | 2026-10-12 |
| 5 | Dosya uzunluğu | `[IO.File]::ReadAllLines` | bu dosya ≥ 500 | 2026-10-12 |
| 6 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 7 | Yer tutucu ibare | metin taraması (yer tutucu yasaklı listesi) | 0 | 2026-10-12 |
| 8 | Boş tablo hücresi | gözden geçirme | 0 | 2026-10-12 |
| 9 | group alanı | frontmatter | `mimari` (yazılmış, tırnaksız) | 2026-10-12 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
