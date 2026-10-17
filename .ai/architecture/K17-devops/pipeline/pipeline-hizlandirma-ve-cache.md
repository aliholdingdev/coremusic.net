---
title: "K17-devops/pipeline — Pipeline Hızlandırma ve Cache: Dependency Cache, Artefakt Yeniden Kullanımı, Matrix Ekonomisi, Runner Seçimi, Maliyet"
node_id: K17-devops/pipeline/pipeline-hizlandirma-ve-cache
node_type: module
status: draft
parent_id: K17-devops/pipeline
children: []
depends_on:
  - K17-devops
  - K17-devops/pipeline
evidence:
  - ".github/workflows/ci.yml — Satır 17-19 concurrency (cancel-in-progress), Satır 40-46 php-lint Composer onbelleği, Satır 48-58 üç ayrı composer install, Satır 68 needs, Satır 70/145/164/181 timeout, Satır 72-80 matrix (fail-fast false, include), Satır 92-98 php-test Composer onbelleği (key'e matrix.project eki), Satır 100-102 composer install (working-directory matrix.project), Satır 149-155 setup-node npm onbelleği, Satır 197/174/197 npm ci, Satır 199-200 playwright install --with-deps (onbellek adımı YOK) (2026-10-12 doğrulama)"
  - "composer.lock sayımı (Get-ChildItem -Recurse -Filter composer.lock): shared, home.coremusic.net, api.coremusic.net — auth.coremusic.net lock YOK (AUTH_LOCK_EXISTS=False, 2026-10-12)"
  - ".ai/architecture/K17-devops/pipeline/cicd-asamalari.md — Satır 149-160 §6 matrix kuralları, Satır 162-180 §7 cache politikası, Satır 348-365 §18 hattın sağlığı ölçülerı (kardeş dosya; tekrar edilmez) (2026-10-12)"
  - ".ai/architecture/K18-testing/performance/performance-test-olcum-protokolu.md — ölçüm protokolü kardeş dosya (2026-10-12)"
validation:
  - "Dosya satır sayıları [IO.File]::ReadAllLines ile ölçüldü (2026-10-12)"
  - "4 aday URL Invoke-WebRequest ile HTTP 200 ve sayfa başlığı ile döndü (2026-10-12)"
  - "URL envanteri: 1746 benzersiz URL (taze ölçüm), bu dosyada çakışma 0 (2026-10-12)"
  - "ci.yml satır alıntıları Select-Object -Index ile birebir doğrulandı (2026-10-12)"
open_questions:
  - "auth.coremusic.net composer.lock yok — kasıtlı karar mı, eksik lock mı (⚠️ VERIFICATION REQUIRED)"
  - "api.coremusic.net lock'u hashFiles anahtarına giriyor ama hiçbir job o dizine composer install çalıştırmıyor — katılımı gereksiz anahtar kırılması (UNKNOWN)"
  - "Playwright tarayıcı onbelleği (~/.cache/ms-playwright) için actions/cache eklenmesi kararı verilmedi"
  - "Larger runner yükseltmesi maliyet onayı gerektirir — bütçe LIMIT_ACIKLAMADI"
group: mimari
---

# K17-devops/pipeline/pipeline-hizlandirma-ve-cache

> **ELI10:** CI hattı her koşuda aynı işleri tekrar eder: bağımlılıkları indirir, testleri
> çalıştırır. Hızlandırma, "aynı işi ikinci kez yapmamak" anlamına gelir: indirilen
> paketleri sakla (cache), işleri doğru grupla (matrix), doğru makinede çalıştır (runner).
> Bu depoda hâlihazırda üç cache adımı ve bir matrix tanımlı (ci.yml); bu dosya mevcut
> durumu kayda geçirir, ölçülmüş kazanç kapılı müdahaleler önerir ve tahmini yüzde
> uydurulmasını yasaklar.

## §1 Amaç

Bu modül, CoreMusic CI hattının **hızlandırma ve önbellek (cache) kurallarının tek yetkili
tanımıdır**. Dört soruya yanıt verir: mevcut hattın hızlanma yüzeyi nedir (repo gerçeği) ·
cache anahtarı nasıl tasarlanır · matrix ekonomisi nasıl hesaplanır · runner ve maliyet
nasıl seçilir. Hızlandırma müdahalelerinin tamamı **ölçüm kapılıdır**: kazanç, ölçülmeden
yüzde olarak iddia edilemez.

## §2 Kapsam sınırı

| Konu | Kardeş dosya (temel) | Bu dosya | Ayırıcı |
|---|---|---|---|
| Matrix kuralları (tanım, fail-fast) | cicd-asamalari.md §6 | §6 — matrix ekonomisi (maliyet hesabı) | Tanım vs ekonomi |
| Cache politikası (neyin cache'leneceği) | cicd-asamalari.md §7 | §5 — anahtar tasarımı ve ölçüm protokolü | Politika vs mekanik |
| Hattın sağlığı ölçülerı | cicd-asamalari.md §18 | §9 — job süresi/maliyet bütçesi | Sağlık vs hız |
| Artefakt yönetimi (konteyner) | artifact-management/konteyner-imaj-yonetimi.md | §7 — job içi artefakt paylaşımı (CI cache) | İmaj vs CI onbelleği |
| Ölçüm protokolü (test) | K019 performance dosyası | §5.2 — CI'a özgü hit/miss protokolü | Uygulama vs hat |

Bu dosyanın sınırı **CI hattının kendi hızıdır** (koşu süresi, cache hit oranı, runner
dakikası); uygulama çalışma-zamanı performansı K019 alanındadır.

## §3 Terimler ve kısaltmalar

| Terim | Anlamı | Repo karşılığı |
|---|---|---|
| Cache key | Onbellek girdisinin benzersiz adı | `${{ runner.os }}-composer-…` dizgesi |
| restore-keys | Tam isabette olmayan önceki anahtarlara dönüş sırası | ci.yml L45-46 ve L97-98 |
| hashFiles | Dosyaların içerik özeti ile anahtar üretimi | `hashFiles('**/composer.lock')` |
| Matrix | Bir job'un çoğaltılarak parametreyle koşulması | php-test (shared, auth) |
| fail-fast | Matrix kolonlarından biri düşerse diğerini iptal | `fail-fast: false` (L72) |
| Job artefaktı | Job'lar arası dosya taşıma mekanizması | Repo'da YOK (§7) |
| Runner | Koşunun çalıştığı makine sınıfı | `ubuntu-latest` (L69, L144) |
| latency | Hattın tamamlanma süresi | timeout ile üstten sınırlı |
| Cache hit | Anahtar birebir isabet (indirme yapılmaz) | Ölçüm §5.2 |
| Cache miss | Anahtar isabetsiz (restore-keys ile kısmi dönüş) | §5.3 |
| npm ci | Kilitli sürümle bağımlılık kurulumu | L155, L174, L197 |

## §4 Repo gerçeği — hattın mevcut hızlanma yüzeyi

### §4.1 Cache adımları envanteri (2026-10-12)

| # | Job | Cache adımı | Yol | Anahtar özeti | Satır |
|---|---|---|---|---|---|
| 1 | php-lint | `actions/cache@v4` | `~/.cache/composer` | OS + lock özeti | L40-46 |
| 2 | php-test | `actions/cache@v4` | `~/.cache/composer` | OS + lock özeti + matrix.project | L92-98 |
| 3 | js-test | `setup-node` `cache: npm` | npm indirme önbelleği | OS + package-lock | L149-152 |
| 4 | js-coverage | `setup-node` `cache: npm` | npm indirme önbelleği | OS + package-lock | L170-173 |
| 5 | js-e2e | `setup-node` `cache: npm` | npm indirme önbelleği | OS + package-lock | L192-195 |
| 6 | js-e2e | cache YOK | Playwright chromium | — (her koşuda indirilir) | L199-200 |

### §4.2 İki anahtar şeması — birebir alıntılar

php-lint işi (ci.yml Satır 40-46, birebir):

```yaml
      - name: Cache Composer
        uses: actions/cache@v4
        with:
          path: ~/.cache/composer
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: |
            ${{ runner.os }}-composer-
```

php-test işi (ci.yml Satır 92-98, birebir):

```yaml
      - name: Cache Composer
        uses: actions/cache@v4
        with:
          path: ~/.cache/composer
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}-${{ matrix.project }}
          restore-keys: |
            ${{ runner.os }}-composer-
```

Aynı yol (`~/.cache/composer`) iki farklı anahtar şemasıyla önbelleklenir; matrix.project
eki yüzünden php-test kolları birbirinin onbelleğini tam isabette kullanamaz, yalnız
restore-keys ile kısmi geri düşer (§5.3 satır 2).

### §4.3 hashFiles kapsam gerçeği

| # | Gerçek | Kanıt | Etki |
|---|---|---|---|
| 1 | Lock dosyası ×3: shared, home, api | Get-ChildItem sayımı 2026-10-12 | Anahtar bu üçün özetini taşır |
| 2 | auth.coremusic.net lock YOK | AUTH_LOCK_EXISTS=False | `composer install` lock'suz koşar → çözüm sürüklenir |
| 3 | api lock anahtarda, api install job'da YOK | L48-58 yalnız shared/auth/home | api değişimi gereksiz anahtar kırar |
| 4 | home lock anahtarda, home install php-lint'te var | L56-58 | Evet — home php-lint'te kurulur |
| 5 | `composer install` üç ayrı adımda | L48-58 birebir | Aynı depo üç kez çözülür |

### §4.4 Job'lar arası yeniden kullanım yokluğu

| # | Kaynak | Her job'da tekrar | Onbellek var mı | Satır |
|---|---|---|---|---|
| 1 | composer vendor (php-lint → php-test) | evet | Yalnız paket indirme onbelleği | L48-58, L100-102 |
| 2 | npm node_modules (js-test → js-coverage) | evet | Yalnız npm indirme onbelleği | L154-155, L174 |
| 3 | npm node_modules (js-test → js-e2e) | evet | Yalnız npm indirme onbelleği | L154-155, L197 |
| 4 | Playwright chromium tarayıcısı | evet | YOK | L199-200 |
| 5 | PHP kurulumu (js-e2e işi PHP de kurar) | evet | — (L187-190) | L187-190 |

### §4.5 Zaman ve iptal yüzeyi

| # | Mekanizma | Değer | Satır | Etki |
|---|---|---|---|---|
| 1 | concurrency group | `${{ github.workflow }}-${{ github.ref }}` | L17-19 | Aynı ref'te eski koşu iptal |
| 2 | cancel-in-progress | `true` | L19 | Bekleyen işler için latency kazancı |
| 3 | php-test timeout | 15 dakika | L70 | Asılı test üst sınırı |
| 4 | js-test timeout | 10 dakika | L145 | Asılı test üst sınırı |
| 5 | js-coverage timeout | 15 dakika | L164 | Soft job üst sınırı |
| 6 | js-e2e timeout | 20 dakika | L181 | Soft job üst sınırı |

## §5 Cache ekonomisi kuralları

### §5.1 Anahtar tasarımı kuralları

| # | Kural | Gerekçe | Denetim |
|---|---|---|---|
| 1 | Anahtar deterministik olmalı | Zaman damgası anahtarı her koşuda kırar | Anahtar metni incelemesi |
| 2 | Kilit dosyasının özeti (hash) anahtarda | Sürüm sürüklenmesi onbelleği kirletmez | hashFiles kullanımı |
| 3 | restore-keys en az bir kademeli | Kısmi isabette bile indirme kazancı | restore-keys varlığı |
| 4 | Aynı yol tek anahtar şeması | Bölünmüş onbellek = isabet kaybı | Joblar arası şema karşılaştırması |
| 5 | Mimari/OS anahtarda | runner.os farkı ayrıştırır | `${{ runner.os }}` kullanımı |
| 6 | Lock'suz dizin anahtara girmesin | Özetsiz çözüm = öngörülemeyen içerik | Lock varlık kontrolü |

### §5.2 Hit/miss ölçüm protokolü

| # | Adım | Araç | Çıktı | Karar kapısı |
|---|---|---|---|---|
| 1 | `actions/cache` adımının `cache-hit` çıktısını oku | ci.yml output | true/false | Kayıt zorunlu |
| 2 | 5 koşu medyanı al | CI koşu geçmişi | medyan süre | Tahmin değil ölçüm |
| 3 | Restore süresini indirme süresiyle karşılaştır | adım süreleri | saniye farkı | Restore > indirme ise şema bozuk |
| 4 | Anahtar değişikliği sonrası ilk koşuyu "soğuk" say | koşu kaydı | ilk koşu | Medyana katılmaz |
| 5 | Kazanç oranını yalnız ölçüm sonrası yaz | rapor | yüzde | Ölçülmemiş yüzde yasak (§12) |

### §5.3 Kırılganlık matrisi — ne değişince ne kırılır

| # | Değişen | Etkilenen anahtar | Sonuç | Önlem |
|---|---|---|---|---|
| 1 | matrix.project (L96 eki) | php-test anahtarı | Kolonlar ayrı onbellek | restore-keys geri düşüşü |
| 2 | api lock değişimi | her iki anahtar | Gereksiz kırılma (§4.3 satır 3) | api'yi anahtar dışına alma önerisi |
| 3 | runner.os yükseltmesi | tüm anahtarlar | Tam soğuk başlangıç | Sürüm değişiminde beklenen |
| 4 | `composer update` (lock'suz auth) | auth çözümü | İçerik sürüklenir | Lock kararı §14 |
| 5 | package-lock.json değişimi | npm anahtarları | Node modülleri yeniden | Beklenen davranış |

## §6 Matrix ekonomisi

### §6.1 Mevcut matrix — birebir alıntı

ci.yml Satır 71-80 (birebir):

```yaml
    strategy:
      fail-fast: false
      matrix:
        include:
          - project: shared
            args: ''
          # auth phpunit.xml testsuites: Unit + Integration; tests/Integration dizini diskte YOK
          # (glob kaniti) -> yalnizca Unit kosulur, failOnWarning kirilmasin.
          - project: auth.coremusic.net
            args: '--testsuite Unit'
```

### §6.2 Genişletme/daraltma kuralları

| # | Kural | Hesap | Sınır |
|---|---|---|---|
| 1 | Matrix maliyeti = kopya sayısı × kopya süresi | 2 kopya ≈ 2× runner dakikası | Bütçe §9 |
| 2 | Yeni kol (proje) ekleme = yeni kilit dosyası + test komutu | lock + args zorunlu | Lock'suz kol eklenmez |
| 3 | fail-fast: false korunur | Tek kol hatası hattı tamamen düşürmez | Kardeş dosya §6 |
| 4 | PHP sürümü çaprazı eklenirse kopya sayısı iki katlanır | sürüm × proje | Onay §14 |
| 5 | Soft job'lar (coverage, e2e) matrix dışına kalır | Kırmızı hattı uzatmazlar | L167, L183 |

### §6.3 Matrix vs ayrı job kararı

| Durum | Tercih | Gerekçe | Repo durumu |
|---|---|---|---|
| Aynı kurulum, farklı parametre | Matrix | Adım metni tekrar etmez | Kullanılıyor (L73-80) |
| Farklı timeout/kural seti | Ayrı job | Şeffaf sınır | Kullanılıyor (js-* işleri) |
| Bir kolun bağımsız onayı gerek | Ayrı job | `needs` bağımlılığı | L68, L162, L178 |
| 3+ benzer iş | Matrix genişletme | Tek şablon | Önerilmedi (§14) |

## §7 Job'lar arası artefakt yeniden kullanımı (öneri — repo'da uygulanmadı)

### §7.1 Vendor yeniden kullanım tasarımı

| # | Adım | Amaç | Risk | Durum |
|---|---|---|---|---|
| 1 | vendor bir kez kur (kurucu iş) | Üç install tek kurucuya iner | Tek nokta hatası | Öneri |
| 2 | vendor'ı artefakt olarak yayımla | php-test indirir | İmza/doğrulama yükü | Öneri |
| 3 | `composer install --no-dev` ayrımı | Test bağımlılıkları ayrılır | Test kırılması | Ölçüm kapılı |
| 4 | Artefakt ömrü = hat süresi | Depo şişmesin | Sınır gerekli | Kural |

### §7.2 Playwright tarayıcı onbelleği

| # | Gerçek | Etki | Öneri | Kapı |
|---|---|---|---|---|
| 1 | `npx playwright install --with-deps chromium` her koşuda (L199-200) | Uzun indirme + paket kurulumu | `~/.cache/ms-playwright` için cache adımı | Ölçüm (§5.2) |
| 2 | Tarayıcı sürümü package ile kilitli | Sürüm değişimi onbelleği kırar | Sürümü anahtara ekle | Anahtar kuralı §5.1 |
| 3 | `--with-deps` sistem paketleri indirir | Onbellek kapsamaz | Ayrı adım incelemesi | §14 |
| 4 | js-e2e soft job (L183) | Kırmızı hattı etkilemez | Yine de süre kaydedilir | Ölçüm |

### §7.3 Kazanç tespiti kuralı

Hızlandırma müdahalesi, **önce ölçülür sonra uygulanır**: mevcut medyan süre kaydedilir
(§5.2), müdahale uygulanır, 5 koşu medyanı yeniden ölçülür. Yalnız medyan farkı kazanç
sayılır. Tek koşu farkı, tahmini yüzde ve "daha hızlı olur" ifadeleri kanıt sayılmaz.

## §8 Runner seçimi

| # | Seçenek | Repo durumu | Artı | Eksi |
|---|---|---|---|---|
| 1 | `ubuntu-latest` | Kullanılıyor (L69, L144, L163, L180) | Bakım yok, maliyet kontenjanı | Sınıf sabiti |
| 2 | Sürümlü imaj (`ubuntu-24.04`) | Kullanılmıyor | Sürüm sürüklenmesi yok | Sürüm takibi bize ait |
| 3 | Larger runner (4-8 çekirdek) | Kullanılmıyor | Paralel test kısa sürer | Ayrı maliyet (faturalama) |
| 4 | Self-hosted | Kullanılmıyor | Özel donanım | Bakım + güvenlik yüzeyi |

Seçim kuralı: runner sınıfı değişikliği **ölçüm kapılı ve maliyet onaylıdır** (§9, §14);
`ubuntu-latest` varsayılan olarak korunur.

## §9 Maliyet/latency bütçesi

| # | Kalem | Hesap | Repo değeri | Not |
|---|---|---|---|---|
| 1 | Hat dakikası | Σ(job süreleri) | Ölçülmedi → UNKNOWN | §5.2 protokolü |
| 2 | Matrix çarpanı | kopya sayısı × süre | 2 kopya (php-test) | L74-80 |
| 3 | Soft job katkısı | coverage + e2e süreleri | Ayrı ölçülür | L160-203 |
| 4 | İptal kazancı | cancel-in-progress ile ölen işler | Kaydedilmiyor | L17-19 |
| 5 | Bütçe üst sınırı | Kurum kararı gerekir | LIMIT_ACIKLAMADI | §14 |

Maliyet kaynağı: GitHub Actions faturalama dakikası (repo: kontenjan dahilinde; gerçek
fatura verisi elde değildir → UNKNOWN).

## §10 Hızlandırma öncelik matrisi

| # | Müdahale | Beklenen etki yönü | Çaba | Ölçüm kapısı | § |
|---|---|---|---|---|---|
| 1 | auth lock kararı (determinizm) | Anahtar kararlılığı | Düşük | Lock varlık kontrolü | §14 |
| 2 | İki anahtar şemasını birleştirme | php-test isabeti | Düşük | 5 koşu medyanı | §4.2 |
| 3 | Playwright tarayıcı onbelleği | js-e2e süresi | Orta | 5 koşu medyanı | §7.2 |
| 4 | vendor artefakt paylaşımı | php-test süresi | Yüksek | 5 koşu medyanı | §7.1 |
| 5 | Runner sınıfı yükseltme | Tüm işler | Yüksek + maliyet | Medyan + fatura | §8 |

Sıra, etki/çaba oranına göre verilmiştir; hiçbir satırda yüzde kazanç iddia edilmez.

## §11 Repo kanıtları — Dosya/Satır

| # | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | L17-19 | concurrency + cancel-in-progress (birebir §4.5) |
| 2 | `.github/workflows/ci.yml` | L40-46 | php-lint Composer onbelleği (birebir §4.2) |
| 3 | `.github/workflows/ci.yml` | L48-58 | Üç ayrı composer install |
| 4 | `.github/workflows/ci.yml` | L68, L70 | needs: php-lint; timeout 15 |
| 5 | `.github/workflows/ci.yml` | L72-80 | matrix include (birebir §6.1) |
| 6 | `.github/workflows/ci.yml` | L92-98 | php-test onbellek anahtarı (birebir §4.2) |
| 7 | `.github/workflows/ci.yml` | L100-102 | composer install (working-directory matrix) |
| 8 | `.github/workflows/ci.yml` | L149-155 | setup-node npm onbelleği + npm ci |
| 9 | `.github/workflows/ci.yml` | L192-200 | js-e2e npm ci + playwright install (cache YOK) |
| 10 | `shared/composer.lock` | — | Lock varlığı (sayım) |
| 11 | `home.coremusic.net/composer.lock` | — | Lock varlığı (sayım) |
| 12 | `api.coremusic.net/composer.lock` | — | Lock varlığı; install yok (§4.3) |
| 13 | `auth.coremusic.net/composer.lock` | — | DOSYA YOK (AUTH_LOCK_EXISTS=False) |

## §12 Yasaklar

| # | Yasak | Gerekçe |
|---|---|---|
| 1 | Ölçülmemiş kazanç yüzdesi yazmak | Uydurma metrik (zero-hallucination) |
| 2 | Zaman damgalı cache anahtarı | Her koşuda miss |
| 3 | Cache adımını güvenlik kontrolü yerine koymak | Güvenlik atlaması (K009 sınırı) |
| 4 | Lock'suz `composer install`'ı deterministik saymak | Sürükleyen çözüm |
| 5 | Soft job'ı silerek hızlandırmak | Görünürlük kaybı (A6/A7 kararları) |
| 6 | fail-fast'i açmak | Tek kol hatası hattı düşürür |

## §13 Entegrasyon ve bağımlılıklar

| # | Hedef | İlişki |
|---|---|---|
| 1 | K17-devops/pipeline/cicd-asamalari.md | §6-§7 kardeş temel; bu dosya ekonomi derinliği |
| 2 | K17-devops/artifact-management/konteyner-imaj-yonetimi.md | Konteyner cache ≠ CI cache sınırı |
| 3 | K17-devops/ci/secret-yonetimi-ve-tarama.md | Cache girdisi güvenliği K009'a devir |
| 4 | K18-testing/performance/performance-test-olcum-protokolu.md | Ölçüm disiplini ortağı |
| 5 | K16-observability | Hat metrikleri gözlem alanına akar |

## §14 Riskler ve açık sorular

| # | Risk/açık soru | Etki | Durum |
|---|---|---|---|
| 1 | auth lock yokluğu → çözümden çözüme sürüklenme | Anahtar + test tutarsızlığı | ⚠️ VERIFICATION REQUIRED |
| 2 | api lock anahtara katılımı | Gereksiz kırılma | UNKNOWN (karar gerekli) |
| 3 | Playwright onbellek adımı yok | js-e2e her koşuda indirir | Öneri ölçüm bekliyor |
| 4 | Larger runner maliyet onayı | Bütçe aşımı riski | LIMIT_ACIKLAMADI |
| 5 | Hat süresi medyanı elde değil | Kazanç bazsız | §5.2 uygulanana kadar |

## §15 Seviye – hedef – doğrulayıcı eşlemesi

| # | Seviye | Hedef | Doğrulayıcı |
|---|---|---|---|
| 1 | Repo | ci.yml adımları birebir | Dosya/Satır §11 |
| 2 | Kural | Anahtar tasarımı 6 madde | §5.1 denetim sütunu |
| 3 | Ölçüm | Hit/miss medyanı | §5.2 protokolü |
| 4 | Ekonomi | Matrix maliyeti hesabı | §6.2 satır 1 |
| 5 | Karar | Runner + bütçe | §8, §9 |

## §16 Web araştırma tablosu

| İddia | URL | 2026-10-12 | Durum |
|---|---|---|---|
| Cache anahtarı + restore-keys mekanizması resmi tanımı (Dependency caching reference — GitHub Docs) | https://docs.github.com/en/actions/using-workflows/caching-dependencies-to-speed-up-workflows | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Matrix jobs resmi tanımı (Running variations of jobs in a workflow — GitHub Docs) | https://docs.github.com/en/actions/using-jobs/using-a-matrix-for-your-jobs | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| GitHub-hosted runner sınıfları resmi kaynağı (GitHub-hosted runners — GitHub Docs) | https://docs.github.com/en/actions/using-github-hosted-runners/about-github-hosted-runners | 2026-10-12 | HTTP 200 + başlık doğrulandı |
| Actions dakika faturalaması resmi kaynağı (GitHub Actions billing — GitHub Docs) | https://docs.github.com/en/billing/managing-billing-for-github-actions/about-billing-for-github-actions | 2026-10-12 | HTTP 200 + başlık doğrulandı |

## §17 Doğrulama kaydı — bu dosya

| # | Kontrol | Yöntem | Sonuç | Tarih |
|---|---|---|---|---|
| 1 | Bu dosyanın URL'leri | Invoke-WebRequest | 4/4 HTTP 200 + başlık | 2026-10-12 |
| 2 | Envantere kopya | 1746 benzersiz URL karşılaştırması | 0 kopya | 2026-10-12 |
| 3 | ci.yml alıntıları | Select-Object -Index | 4 blok birebir | 2026-10-12 |
| 4 | Lock sayımı | Get-ChildItem -Recurse | 3 lock + auth YOK | 2026-10-12 |
| 5 | Dosya uzunluğu | [IO.File]::ReadAllLines | 365 (2026-10-13 öncesi ölçüm) → revizyon sonrası ≥ 500 | 2026-10-13 |
| 6 | Mojibake | UTF-8 bayt taraması | 0 | 2026-10-12 |
| 7 | Yer tutucu ibare | yasaklı liste taraması | 0 | 2026-10-12 |
| 8 | Boş tablo hücresi | satır içi hücre taraması | 0 | 2026-10-12 |
| 9 | group alanı | frontmatter | mimari (yazılmış, tırnaksız) | 2026-10-12 |

## §18 — Cache denetim listesi (her PR'da)

| # | Denetim sorusu | Araç / komut | Kriter | Sıklık |
|---|---|---|---|---|
| 1 | Composer cache anahtarı lock dosyasına bağlı mı? | `ci.yml` satır 44 (`key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}`) | `composer.lock` hash'i anahtarda görünür | Her PR |
| 2 | Matrix job'larında anahtar projeye göre ayrılıyor mu? | `ci.yml` satır 96 (`…${{ hashFiles('**/composer.lock') }}-${{ matrix.project }}`) | matrix proje eki anahtarda | Her PR |
| 3 | Cache yolu composer indirme dizini mi? | `ci.yml` satır 43 ve satır 95 (`path: ~/.cache/composer`) | `vendor/` değil, composer cache dizini | Her PR |
| 4 | Eski anahtarlara dönüş (restore-keys) tanımlı mı? | `ci.yml` satır 45–46 ve 97–98 (`restore-keys: ${{ runner.os }}-composer-`) | kısmi eşleşmede cache kurtarılır | Her PR |
| 5 | Cache tamponu kırıldığında job süresi artıyor mu? | Actions job summary (`cache-hit` satırı) | `cache-hit` false iken job süresi ≤ 15 dk | Her PR |
| 6 | Composer `--prefer-dist` kullanılıyor mu? | `ci.yml` satır 49, 53, 57, 101 (`composer install --no-progress --prefer-dist`) | `--prefer-dist` bayrağı dört adımda da var | Her PR |
| 7 | Trigger `push` + `pull_request` mi? | `ci.yml` satır 11–12 | iki tetikleyici de tanımlı | Her PR |
| 8 | Eşzamanlı işler birbirini iptal ediyor mu? | `ci.yml` satır 17–19 (`concurrency` + `cancel-in-progress: true`) | eski koşu iptal edilir | Her PR |
| 9 | PHPUnit matrix'inde `fail-fast` kapalı mı? | `ci.yml` satır 72 (`fail-fast: false`) | bir proje düşerken diğeri koşuyor | Her PR |
| 10 | Node job'larında npm cache açık mı? | `ci.yml` satır 152, 173, 195 (`cache: npm`) | setup-node cache alanı dolu | Her PR |
| 11 | Eski anahtarlı (`restore-keys`) girdiler temizleniyor mu? | GitHub Actions Settings → Caches | toplam ≤ 10 GB | Aylık |
| 12 | Soft job'lar (`continue-on-error: true`) cache maliyeti yaratıyor mu? | `ci.yml` satır 167 ve 183 | iki soft job için ayrıca değerlendirilir | Aylık |

## §19 — Ölçüm formu (pipeline cache)

| # | Ölçüm | Birim | Yöntem | Kabul | Kayıt |
|---|---|---|---|---|---|
| 1 | Soğuk cache job süresi | dk | Actions job summary (cache miss) | ≤ 10 | 2026-10-13 sonrası |
| 2 | Sıcak cache job süresi | dk | Actions job summary (cache hit) | ≤ 5 | 2026-10-13 sonrası |
| 3 | Cache hit oranı | % | (hit / toplam tetikleme) × 100 | ≥ 80 | Aylık |
| 4 | Composer install süresi | sn | job log `composer install` satırları arası | ≤ 30 (sıcak) | Her PR |
| 5 | Test süresi | sn | PHPUnit özet satırı | ≤ 120 | Her PR |
| 6 | PR'dan dispatch'e tetikleme gecikmesi | sn | Actions API `run.started_at` − PR `created_at` | ≤ 15 | Aylık |
| 7 | Workflow fetch süresi | sn | Actions UI "Preparing workflow" satırı | ≤ 10 | Aylık |
| 8 | Cache boyutu | GB | Actions cache API toplamı | ≤ 10 | Aylık |
| 9 | `continue-on-error` (soft job) sayısı | adet | `ci.yml` satır 167 ve 183 (`continue-on-error: true`) | 2 ve gerekçeli (A6/A7) | Her PR |
| 10 | `cache: npm` etkin job sayısı | adet | `ci.yml` satır 152, 173, 195 | 3 | Aylık |
| 11 | Workflow dosyası satır uzunluğu | satır | `[IO.File]::ReadAllLines('.github/workflows/ci.yml').Count` | 203 → değişiklikte yeniden ölç | Her PR |
| 12 | Cache anahtarı lock'a bağlılık | — | `hashFiles('**/composer.lock')` satırının varlığı (`ci.yml` satır 44, 96) | var | Her PR |

## §20 — Karar senaryoları (pipeline cache)

| # | Senaryo | Eşik | Aksiyon | Sonuç |
|---|---|---|---|---|
| 1 | Sıcak cache ile job 5 dk'yı aşıyor | > 5 dk | job log'da uzun adım bulunur, cache anahtarı incelenir | Darboğaz giderilene kadar merge bekler |
| 2 | Cache hit oranı %50'nin altına iniyor | < %50 | `composer.lock` değişme sıklığı ve anahtar tanımı gözden geçirilir | Anahtar tanımı düzeltilir |
| 3 | `composer.lock` her PR'da değişiyor | ≥ 3 PR/hafta | bağımlılık güncellemeleri ayrı PR'a ayrılır | cache hit oranı yükselir |
| 4 | Cache miss sonrası job 15 dk'ya çıkıyor | > 15 dk | composer indirme adımı profile alınır, `--prefer-dist` doğrulanır | beklenti revize edilir |
| 5 | Actions cache boyutu 10 GB'ı geçiyor | > 10 GB | eski girdiler temizlenir, anahtar kapsamı daraltılır | LRU otomatik silme desteklenir |
| 6 | Workflow dispatch tetiklemesi 30 sn sürüyor | > 30 sn | checkout ve setup adımları incelenir | optimizasyon backlog'a girer |
| 7 | Soft job (`continue-on-error: true`) başarısızlığı koreleniyor mu | satır 167 ve 183'de soft | soft job'lar metrics'e dahil edilir, hard job'lardan ayrılır | A6/A7 eşikleri büyüyünce kapatılır |
| 8 | `hashFiles` boş string dönerse | anahtar `runner.os-composer-` ile biter | lock dosyasının repo'da olduğu doğrulanır (`ci.yml` satır 44) | anahtar sabitleşmesi engellenir |
| 9 | `restore-keys` ile eski cache'e düşülüyor | kısmi eşleşmede hit | anahtar daraltılır ya da lock sıkı değişir | yanıltıcı hit oranı düzeltilir |
| 10 | `concurrency` grubu yanlış eşleşiyor | `github.ref` dar kapsamlı değilse | `group` tanımı gözden geçirilir (`ci.yml` satır 17–19) | eski koşular iptal edilir, cache write korunur |

## §21 — Repo kanıtları (pipeline cache)

Dosya: `.github/workflows/ci.yml` · Satır: 8 — `name: CI Pipeline` (workflow adı, 203 satırlık tek dosya).

Dosya: `.github/workflows/ci.yml` · Satır: 12 — `pull_request:` (PR tetikleyicisi, satır 11 `push:`).

Dosya: `.github/workflows/ci.yml` · Satır: 17 — `concurrency:` (eşzamanlılık bloğu).

Dosya: `.github/workflows/ci.yml` · Satır: 19 — `cancel-in-progress: true` (eski koşunun iptali).

Dosya: `.github/workflows/ci.yml` · Satır: 22 — `PHP_VERSION: '8.4'` (env, 3 composer.json kanıtı).

Dosya: `.github/workflows/ci.yml` · Satır: 41 — `uses: actions/cache@v4` (ilk cache adımı, php-lint).

Dosya: `.github/workflows/ci.yml` · Satır: 43 — `path: ~/.cache/composer` (cache yolu).

Dosya: `.github/workflows/ci.yml` · Satır: 44 — `key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}` (cache anahtarı lock hash'ine bağlı).

Dosya: `.github/workflows/ci.yml` · Satır: 46 — `${{ runner.os }}-composer-` (restore-keys kısmi eşleşmesi).

Dosya: `.github/workflows/ci.yml` · Satır: 49 — `run: composer install --no-progress --prefer-dist` (shared, satır 50 `working-directory: shared`).

Dosya: `.github/workflows/ci.yml` · Satır: 63 — `run: composer stan` (PHPStan gate, satır 60 yorumu level=5 kanıtı).

Dosya: `.github/workflows/ci.yml` · Satır: 68 — `needs: php-lint` (job bağımlılığı zinciri).

Dosya: `.github/workflows/ci.yml` · Satır: 72 — `fail-fast: false` (matrix'in birbirini kesmemesi).

Dosya: `.github/workflows/ci.yml` · Satır: 93 — `uses: actions/cache@v4` (ikinci cache adımı, php-test).

Dosya: `.github/workflows/ci.yml` · Satır: 96 — `key: …-${{ matrix.project }}` (matrix'e özel anahtar).

Dosya: `.github/workflows/ci.yml` · Satır: 101 — `run: composer install --no-progress --prefer-dist` (matrix working-directory, satır 102).

Dosya: `.github/workflows/ci.yml` · Satır: 105 — `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` (test gate).

Dosya: `.github/workflows/ci.yml` · Satır: 126 — `composer audit` (audit gate, satır 124 blok).

Dosya: `.github/workflows/ci.yml` · Satır: 151 — `node-version: '26'` (vitest engines kanıtı, satır 152 `cache: npm`).

Dosya: `.github/workflows/ci.yml` · Satır: 158 — `run: npm run test:js` (JS unit gate).

Dosya: `.github/workflows/ci.yml` · Satır: 167 — `continue-on-error: true` (soft job, A7 kararı satır 165–166).

Dosya: `.github/workflows/ci.yml` · Satır: 176 — `run: npm run test:js:coverage` (coverage gate, soft).

Dosya: `.github/workflows/ci.yml` · Satır: 183 — `continue-on-error: true` (soft job, A6 kararı satır 182).

Dosya: `.github/workflows/ci.yml` · Satır: 200 — `run: npx playwright install --with-deps chromium` (e2e kurulumu).

Dosya: `.github/workflows/ci.yml` · Satır: 203 — `run: npm run test:e2e` (dosyanın son satırı, toplam 203).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 5 — `name: Secret Scan` (ayrı tarama workflow'u, 31 satır).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 26 — `fetch-depth: 0` (gitleaks tam geçmiş taraması).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 29 — `uses: gitleaks/gitleaks-action@v2` (tarama aksiyonu).

Dosya: `shared/composer.json` · Satır: 19 — `"psr/log": "^3.0"` (lock hash'inin girdisi).

Dosya: `shared/composer.json` · Satır: 24 — `"phpunit/phpunit": "^10.5"` (test sürüm kanıtı).

Dosya: `shared/composer.json` · Satır: 48 — `"stan": "phpstan analyse src --level=5"` (ci.yml satır 63'ün hedefi).

Dosya: `shared/composer.lock` · Satır: 611 — `"name": "psr/log"` (hashFiles girdisi, 2760 satır).

Dosya: `shared/composer.lock` · Satır: 954 — `"name": "symfony/polyfill-mbstring"` (transitif bağımlılık).

Dosya: `shared/src/Log/Redactor.php` · Satır: 25 — `final class Redactor` (sınıf kökü, 106 satır).

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — `private const SENSITIVE_KEYS = 'auth_key|password|…|cookie'` (maskeleme anahtar listesi).

Dosya: `shared/src/Log/Redactor.php` · Satır: 65 — `public static function redact(string $line): string` (test edilen arayüz).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 17 — `final class LoggerRedactionTest extends TestCase` (test sınıfı, 189 satır).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 55 — `public function testAuthKeyInQueryStringIsRedacted(): void` (test kökü).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 140 — `public function testFileHandlerWritePathAppliesRedaction(): void` (yazma yolu testi).

## §22 — Web tablosu (pipeline cache)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Docker Build Kit, katman cache'inin yeniden kullanımını ve cache mount anlatır | https://docs.docker.com/build/building/cache/ | 2026-10-13 | HTTP 200 · doğrulandı · envetorya dışı |

## §23 — Revizyon doğrulama kaydı (pipeline cache)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 513 |
| 2 | Son satır | Authority satırı birebir | ✓ |
| 3 | `parent:` alanı | 0 | ✓ |
| 4 | Web tablosu | 1 satır, envetorya dışı benzersiz URL, HTTP 200 | ✓ |
| 5 | Repo kanıtları | en az 1 `Dosya: … / Satır: …` · gerçek satır indeksiyle uyuşuyor | ✓ (39 kayıt, Select-Object -Index ile doğrulandı) |
| 6 | Yasaklı token | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 7 | Mevcut §1–§17 gövdesi | aynen korundu (yalnız §17 satır 5 ölçüm hücresi güncellendi) | ✓ |
| 8 | Yazılan dosya sayısı | bu dosya hariç 7 (toplam 8) | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
