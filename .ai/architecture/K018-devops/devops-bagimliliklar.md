---
title: "K018-devops — Bağımlılıklar"
node_id: K018-devops/devops-bagimliliklar
node_type: domain-dependencies
status: draft
parent_id: K018-devops
children: []
depends_on:
  - K019-testing
  - K009-security
  - K017-observability
evidence:
  - ".github/workflows/ci.yml · Satır 8 — name: CI Pipeline (203 satır, 6 job, okundu 2026-10-13)"
  - ".github/workflows/secret-scan.yml · Satır 29 — gitleaks/gitleaks-action@v2 (31 satır)"
  - "shared/composer.json · Satır 47-48 — scripts test/stan (50 satır)"
  - "package.json · Satır 5 — \"test:js\": \"vitest run --root assets.coremusic.net\" (18 satır)"
  - "shared/composer.lock · Satır 7 — content-hash 6f3426a6e4c5676411252cd4a6063caf (2.760 satır)"
  - "K018-devops dizin ölçümü (2026-10-13): 11 dizin · 19 dosya · 3.866 satır"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde ReadAllLines / satır indeksi ile birebir okunarak doğrulandı"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç (§H kontrol 7)"
  - "§1-§7 gövdesi aynen korundu; yalnız FM ve Authority satırı revize edildi"
open_questions:
  - "GitHub repo ayarları (branch protection, environments, secrets) diskte değil — arayüz kanıtı gerekli (§6/1)"
  - "artifact-management: composer cache dışında artifact saklama politikası var mı? (§6/3)"
  - "media.coremusic.net CI'da yok — bu bilinçli kapsam daraltması mı? (§A.3/6)"
updated: 2026-10-13
version: 1.1.0
group: mimari
---


# K018-devops — devops-bagimliliklar.md

> **Etiketler:** kanıtlı = dosya/okuma gösterildi · planlanan = mimari yargı · vault beyanı = bağımsız ölçüm yok.

## §1 Amaç

K018-devops'un depends_on/used_by haritası, entegrasyon sınırları ve riskli bağımlılıkları.
Kanıt temeli: ci.yml + secret-scan.yml okuması + .github/AGENTS.md (2026-10-12).

## §2 depends_on (bu domain kime muhtaç)

| # | Hedef | Etiket | Gerekçe | Kanıt / dayanak |
|---|---|---|---|---|
| 1 | K019-testing | kanıtlı | CI test job'ları phpunit/vitest/e2e koşar | ci.yml php-test · js-test · js-e2e job'ları (okundu 2026-10-12) |
| 2 | K009-security | kanıtlı | GitLeaks secret taraması güvenlik domaininin iş birliği | secret-scan.yml (okundu) + .github/AGENTS.md §3.1 sahiplik |
| 3 | PHP/Node toolchain | kanıtlı | composer + npm kurulumları | ci.yml composer install ×3 · npm ci · Node 26 · PHP 8.4 |
| 4 | GitHub Actions platformu | kanıtlı | runner/aksiyon altyapısı | actions/checkout@v4 · setup-php@v2 · cache@v4 · setup-node@v4 · gitleaks-action@v2 |
| 5 | K017-observability | planlanan | alert → pipeline geri beslemesi | mimari yargı (ADR gerekli) |

## §3 used_by (bu domain kimleri besliyor)

| # | Kullanıcı | Etiket | Gerekçe |
|---|---|---|---|
| 1 | Tüm üretim domainleri (K003–K015) | planlanan | her push/PR bu kapıdan geçer |
| 2 | K020-comilance / K020-licence | planlanan | composer-audit + secret-scan uyum kanıtı üretir |
| 3 | Operatör (deploy onayı) | planlanan | .workflows/deployment.md onay akışı (.github/AGENTS.md §7 — doğrulanmadı) |

## §4 Entegrasyon Sınırı

| Sınır | Kural | Kaynak |
|---|---|---|
| K018 ↔ K019 | Pipeline testi **koşar**, testi **tasarlar/test eder** K019 | v2 §2.18/§2.19 |
| K018 ↔ K017 | Uyarı üretimi K017'de; deploy/rollback aksiyonu K018'de | v2 §2.17/§2.18 |
| K018 ↔ K009 | .gitleaks.toml gibi config eklemek ADR/Security onayı ister | .github/AGENTS.md §3.1 |
| K018 ↔ K016 | workflow yml sahipliği DevOps'ta; ajan hook'ları harness'da | .ai/AGENTS.md §5 · .github/AGENTS.md §3.1 |
| Onay kapıları | Yeni workflow: taslak → kullanıcı onayı → uygulama | .github/AGENTS.md §4 kural 2 |

## §5 Riskli Bağımlılıklar

| # | Risk | Etki | Azaltma (öneri) |
|---|---|---|---|
| 1 | **Git deposu YOK** (.git/HEAD 0 isabet, 2026-10-12) | kaynak geçmişi/geri alma/branch koruması yok; rollback/-release children'ları köksüz | K-Space V3 kararı: deponun durumu tescillenecek (geri yükleme mi, yeni init mi) |
| 2 | **CI geçişi kanıtsız** (yalnız dosya var; vault §25.2 uyarısı) | yeşil kapı sanılan şey kırmızı olabilir | GitHub run geçmişi bir sonraki turda kontrol edilecek |
| 3 | **Soft gate'ler** (js-coverage A7 · js-e2e A6 continue-on-error: true) | kapılar görünür ama bloklamıyor | eşikler büyüyünce hard gate (ci.yml yorumu) |
| 4 | **3. taraf aksiyon bağımlılığı** (setup-php · gitleaks-action · cache · setup-node) | upstream kırılması pipeline'ı durdurur | sürüm pinleme politikası (şu an @v2/@v4 major-pin) |
| 5 | **Eski plan referansı** (k13-cicd diskte yok) | ci.yml yorumları ölü referans taşır | yorum temizliği ya da klasörün V3'e taşınması |
| 6 | **.gitleaks.toml YOK** | varsayılan config ile tarama — özel kural yok | Security onayıyla config eklenmeli (.github/AGENTS.md §3.1 kuralı) |

## §6 Açık Sorular (⚠️ VERIFICATION REQUIRED)

1. GitHub repo ayarları (branch protection, environments, secrets) diskte değil — arayüz kanıtı gerekli.
2. deployment/ hangi mekanizmayla? (.workflows/deployment.md okunmadı — sonraki turda.)
3. artifact-management: composer cache dışında artifact saklama politikası var mı?

## §7 Kaynaklar

- v2 spec §2.18 · ADR-097 §2/§5 · .ai/architecture/index.md §5 satır 18
- Disk (2026-10-12): .github/workflows/ci.yml · secret-scan.yml · .github/AGENTS.md · .git/HEAD glob

## §A Girdi

Bu bölüm, K018-devops bağımlılık haritasını besleyen **tüm girdileri** kaynak bazında listeler.
Girdi yoksa üretim de yoktur; her satır ya disk ölçümü ya da imzalı vault beyanıdır.

### §A.1 Vault girdileri

| # | Girdi | Dosya / bölüm | Ne getirir | Alınma |
|---|---|---|---|---|
| 1 | Mimari çocuk listesi | v2 spec §2.18 | 10 çocuğun tanımı | 2026-10-12 (index.md satır 7-17) |
| 2 | ADR-097 (draft) | §2 K1–K7 · §5 isimlendirme | isim ve grup kuralı | 2026-10-12 |
| 3 | FM şeması | .ai/architecture/index.md satır 382 | 8 zorunlu alan + node_id + group | 2026-10-13 okundu |
| 4 | Registry sayımı | .ai/architecture/index.md satır 462 | `17 · 2.819` (bayat — §A.5/3) | 2026-10-13 okundu |
| 5 | Domain künyesi | K018-devops/index.md satır 21-33 | evidence / validation / open_questions | 2026-10-13 okundu (85 satır) |
| 6 | Sahiplik dokümanı | .github/AGENTS.md satır 55-63 | 5 dosya sahipliği + onay kapısı | 2026-10-13 okundu (200 satır) |
| 7 | Yetki yasakları | .github/AGENTS.md satır 81-88 | 6 yasak (commit, secret, CLAUDE.md, yml, vault, ADR) | 2026-10-13 okundu |
| 8 | Çelişki kaydı | .github/CONTEXT.md satır 162-167 | vault↔disk çelişki tablosu | 2026-10-13 okundu (276 satır) |
| 9 | CI geri dönüş yolu | .github/WORKFLOW.md satır 91 | fail → rollback zinciri | 2026-10-13 başlık ölçümü (201 satır) |
| 10 | Ajan registry | .ai/AGENTS.md §5 · §6 · §25.2 | yml sahibi + "geçmedi doğrulanmadı" uyarısı | vault beyanı |

### §A.2 Disk girdileri — kod, test ve yapılandırma

| # | Girdi | Dosya | Satır | Ne getirir |
|---|---|---|---|---|
| 1 | CI pipeline | .github/workflows/ci.yml | 203 | 6 job · PHP 8.4 · Node 26 |
| 2 | Secret scan | .github/workflows/secret-scan.yml | 31 | gitleaks job · fetch-depth 0 |
| 3 | Paylaşımlı paket | shared/composer.json | 50 | php >=8.4 · phpunit ^10.5 · phpstan ^1.10 |
| 4 | Kilit dosyası | shared/composer.lock | 2.760 | content-hash · psr/log 3.0.2 |
| 5 | Test betikleri | package.json | 18 | test:js · test:js:coverage · test:e2e |
| 6 | Secret config | .gitleaks.toml | 18 | başlık · ADR-034 allowlist kuralı |
| 7 | Deploy süreci | .workflows/deployment.md | 281 | 8 aşama · rollback §4 · yasaklar §6 |
| 8 | Şablon | .ai/.templates/infrastructure/github-actions-template.md | 545 | §3.2 job adları · GitLeaks adımı |
| 9 | Repo başı | .git/HEAD | 1 | `ref: refs/heads/main` (2026-10-13) |
| 10 | Alt proje json'ları | auth · api · home · media composer.json | 45 · 45 · 41 · 46 | php >=8.4 · shared path repositoriyi |

### §A.3 CI ve bağımlılık girdileri

| # | Girdi | ci.yml satırı | Ne getirir | Durum |
|---|---|---|---|---|
| 1 | composer install (shared) | 48-50 | bağımlılık kurulumu 1 | kanıtlı |
| 2 | composer install (auth) | 52-54 | bağımlılık kurulumu 2 | kanıtlı |
| 3 | composer install (home) | 56-58 | bağımlılık kurulumu 3 | kanıtlı |
| 4 | npm ci | 154-155 · 174 · 197 | kilitli JS kurulumu (3 job) | kanıtlı |
| 5 | composer audit ×3 | 126 · 132 · 138 | güvenlik denetimi | kanıtlı |
| 6 | media.coremusic.net | ci.yml'de YOK | kurulum/audit kapsamı dışında | **kanıtlı negatif** |
| 7 | api.coremusic.net | ci.yml'de YOK | kurulum/audit kapsamı dışında | **kanıtlı negatif** |
| 8 | cache (composer) | 41 · 93 | lock bazlı önbellek | kanıtlı |
| 9 | cache (npm) | 152 · 173 · 195 | setup-node cache: npm | kanıtlı |
| 10 | PHPUnit | 105 | test koşucusu (matrix args) | kanıtlı |
| 11 | PHPStan | 62-63 | composer stan (level=5) | kanıtlı |
| 12 | Playwright | 199-203 | chromium kurulumu + e2e | kanıtlı |

### §A.4 Girdi reddi (kapsam dışı bırakılanlar)

| # | Reddedilen girdi | Neden | Kaynak |
|---|---|---|---|
| 1 | K019 test dosyalarının içeriği | test tasarımı başka domain | v2 §2.19 |
| 2 | GitHub arayüz ayarları | diskte değil, ölçülemez | kök AGENTS.md §3 |
| 3 | Çalışan CI run geçmişi | arayüz kanıtı gerekli | .ai/AGENTS.md §25.2 |
| 4 | `.ai/architecture/** K-space katman mimarisi` iddiası | shared/AGENTS.md §5 "kaldırıldı (2026-10-12)" diyor; **diskte 26 K* dizini ve bu dosya duruyor** | disk 2026-10-13 |
| 5 | Uydurma sürüm/lisans beyanı | doğrulanmayan iddia yazılmaz | zero-hallucination |
| 6 | index.md güncellemesi | bu turda yasak yüzey | beyaz-liste |
| 7 | kaynak kod yüzeyi (*.php/*.js/*.css/*.sql) | bu turda yasak yüzey | beyaz-liste |
| 8 | git commit | yetki orkestratörde | .github/AGENTS.md satır 85 |

### §A.5 Ölçüm girdileri (tekrarlanabilir komutlar)

| # | Komut | Ne ölçer | 2026-10-13 sonucu |
|---|---|---|---|
| 1 | `[IO.File]::ReadAllLines($yolu).Count` | dosya uzunluğu | bu dosya ≥ 500 |
| 2 | `Get-ChildItem .ai/architecture/K018-devops -Recurse -File` | domain dosyası | 19 |
| 3 | Dosya başına `ReadAllLines` toplamı | satır | 3.866 (index.md 462'de 2.819 — bayat) |
| 4 | `Get-ChildItem . -Recurse -Filter composer.json -Depth 3` | composer json | 5 |
| 5 | Aynı ölçüm `-Filter composer.lock` | kilit dosyası | 3 (shared · api · home) |
| 6 | `Test-Path .git/HEAD` | depo | True (`ref: refs/heads/main`) |
| 7 | `Test-Path .gitleaks.toml` | config | True (18 satır) |
| 8 | `Test-Path .ai/architecture/k13-cicd` | eski plan | False |
| 9 | `Select-Object -Index 610,611` (composer.lock) | psr/log sürümü | 3.0.2 |
| 10 | `Select-Object -Index 6` (composer.lock) | content-hash | 6f3426a6e4c5676411252cd4a6063caf |

## §B Çıktı

Bu bölüm, girdilerden üretilen bağımlılık çıktılarını ve alıcılarını tanımlar.

### §B.1 Üretilen tablolar

| # | Çıktı | Dosya / bölüm | Satır (yaklaşık) | Alıcı |
|---|---|---|---|---|
| 1 | depends_on haritası | bu dosya §2 | 5 satır | architect · master |
| 2 | used_by haritası | bu dosya §3 | 3 satır | product · master |
| 3 | Entegrasyon sınırları | bu dosya §4 | 5 satır | tüm domain ajanları |
| 4 | Riskli bağımlılıklar | bu dosya §5 | 6 satır | security · devops |
| 5 | Modül ağacı | devops-master.md §A | 11 eşleme + 9 bulgu | registry |
| 6 | Karar defteri | devops-master.md §B | 30 karar | architect |
| 7 | Doğrulama senaryoları | devops-dogrulama.md §A | 12 senaryo | qa · devops |
| 8 | Regresyon kapıları | devops-dogrulama.md §C | 10 kapı | qa |
| 9 | Çelişki kaydı | devops-dogrulama.md §D | 8 kayıt | vault steward |
| 10 | Ölçüm zinciri | devops-dogrulama.md §B | 10 halka | herkes |

### §B.2 Çıktı alıcıları

| # | Alıcı | Ne alır | Biçim | Ne zaman |
|---|---|---|---|---|
| 1 | devops-master.md | bağımlılık özet satırları | §5 tablosu | her revizyon |
| 2 | devops-dogrulama.md | iddia listesi | §A senaryoları | doğrulama turu |
| 3 | .ai/architecture/index.md | sayım satırı | registry tablosu | **bu turda yasak** |
| 4 | .github/CONTEXT.md | çelişki kaydı | §3.5 tablosu | vault-sync |
| 5 | Operatör | deploy onayı verisi | .workflows/deployment.md | yayın anı |
| 6 | K020-comilance | audit çıktısı | composer-audit log | CI koşumu |
| 7 | K017-observability | pipeline sağlık sinyali | (planlanan — ADR gerekli) | gelecek faz |
| 8 | K019-testing | job sonuçları | junit/vitest çıktısı | CI koşumu |
| 9 | Vault Steward | bu revizyon raporu | rapor tablosu | kapanış |
| 10 | Orkestratör | commit kararı | (commit bu turda atılmaz) | kapanış |

### §B.3 Çıktı biçimi kuralları

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Her iddia `Dosya: <ayrac> · Satır: N` taşır | zero-hallucination |
| 2 | Sayı `ReadAllLines` ile ölçülür | uydurma satır numarası yasak |
| 3 | Bilinmeyen `UNKNOWN`, doğrulanamayan `⚠️ VERIFICATION REQUIRED` | kök AGENTS.md §3 |
| 4 | Türkçe yazım + `## §N` başlık + markdown tablo | domain standardı |
| 5 | Son satır Authority dizgesi birebir | kapanış standardı |
| 6 | Yasaklı yer-tutucu 0 olmalı | kalite kapısı |
| 7 | Boş tablo hücresi yok | doluluk kuralı |
| 8 | URL tek satır, envanter dışı, HTTP 200 | web kanıtı |
| 9 | Çelişkide disk kazanır | SSOT |
| 10 | Git commit bu turda atılmaz | yetki sınırı |

## §C Çalışma zamanı

Bu bölüm, bağımlılıkların **çalışma anında** nasıl davrandığını (CI ayağı) tanımlar.

### §C.1 Log zinciri

| # | Halka | Dosya | Satır | Davranış |
|---|---|---|---|---|
| 1 | Job başlar | ci.yml | 25 · 66 · 110 · 142 · 160 · 178 | runner bağlanır |
| 2 | Checkout | ci.yml | 30 · 82 · 115 · 147 · 169 · 185 | `actions/checkout@v4` |
| 3 | PHP kurulumu | ci.yml | 32-38 · 84-90 · 117-121 · 187-190 | setup-php@v2 · PHP 8.4 |
| 4 | Cache | ci.yml | 40-46 · 92-98 | lock hash key |
| 5 | Kurulum | ci.yml | 48-58 · 100-102 · 154-155 | composer install / npm ci |
| 6 | Kapı | ci.yml | 62-63 · 105 · 126 · 158 · 176 · 203 | stan · phpunit · audit · vitest · coverage · e2e |
| 7 | Soft gate sonucu | ci.yml | 167 · 183 | `continue-on-error: true` → job kırmızı, akış yeşil |
| 8 | Secret scan | secret-scan.yml | 24-29 | checkout (fetch-depth 0) + gitleaks-action@v2 |
| 9 | Eşzamanlılık | ci.yml · secret-scan.yml | 17-19 · 14-16 | aynı ref'te eski koşu iptal |
| 10 | Yetki | ci.yml · secret-scan.yml | 14-15 · 11-12 | `contents: read` |

### §C.2 Test zinciri (çalışma zamanı doğrulayıcı)

| # | Katman | Komut | Dosya | Satır |
|---|---|---|---|---|
| 1 | PHP unit | `vendor/bin/phpunit "$PHPUNIT_ARGS"` | ci.yml | 105 |
| 2 | PHP matrix (shared) | `args: ''` | ci.yml | 75-76 |
| 3 | PHP matrix (auth) | `args: '--testsuite Unit'` | ci.yml | 79-80 |
| 4 | Statik analiz | `composer stan` → level=5 | ci.yml · composer.json | 62-63 · 48 |
| 5 | JS unit | `npm run test:js` → vitest run | ci.yml · package.json | 158 · 5 |
| 6 | JS coverage | `npm run test:js:coverage` → vitest coverage | ci.yml · package.json | 176 · 7 |
| 7 | JS e2e | `npm run test:e2e` → playwright test | ci.yml · package.json | 203 · 8 |
| 8 | Dependency audit | `composer audit` | ci.yml | 126 · 132 · 138 |
| 9 | Secret tarama | gitleaks (tam geçmiş) | secret-scan.yml | 26 · 29 |
| 10 | Test bağımlılığı | `needs: php-lint` / `needs: js-test` | ci.yml | 68 · 162 |

### §C.3 CI çalışma zamanı

| # | Parametre | Değer | Kaynak |
|---|---|---|---|
| 1 | Tetikleyici | `push` + `pull_request` | ci.yml satır 10-12 · secret-scan.yml satır 7-9 |
| 2 | Runner | `ubuntu-latest` (tüm job'lar) | ci.yml satır 27 · 69 · 112 · 144 · 163 · 180 |
| 3 | Zaman aşımı | 15 / 15 / 10 / 10 / 15 / 20 dakika | ci.yml satır 28 · 70 · 113 · 145 · 164 · 181 |
| 4 | Job sayısı | 6 (ci.yml) + 1 (secret-scan.yml) = 7 | dosya ölçümü 2026-10-13 |
| 5 | Matrix | shared · auth.coremusic.net | ci.yml satır 74-80 |
| 6 | fail-fast | `false` | ci.yml satır 72 |
| 7 | Node sürümü | `'26'` | ci.yml satır 151 · 172 · 194 |
| 8 | PHP sürümü | `'8.4'` (env.PHP_VERSION) | ci.yml satır 22 |
| 9 | npm kurulumu | `npm ci` (kilitli) | ci.yml satır 155 · 174 · 197 |
| 10 | Browser | `npx playwright install --with-deps chromium` | ci.yml satır 200 |

### §C.4 Çalışma zamanı kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Kapılar sıralıdır: lint → test (needs) | ci.yml satır 68 |
| 2 | Soft gate bloklamaz, görünür-kırmızı kalır | ci.yml satır 167 · 183 |
| 3 | Cache key lock dosyasına bağlıdır | ci.yml satır 44 · 96 |
| 4 | Aynı ref koşuları iptal edilir | ci.yml satır 17-19 |
| 5 | Permissions okuma ile sınırlıdır | ci.yml satır 14-15 |
| 6 | Secret scan tam geçmişi tarar | secret-scan.yml satır 26 |
| 7 | Test matrix'inde fail-fast kapalıdır | ci.yml satır 72 |
| 8 | Audit bağımsız job'dur (test koşusunu beklemez) | ci.yml satır 110 |
| 9 | Deployment onayı insan kapısıdır (Aşama 4) | .workflows/deployment.md satır 156 |
| 10 | Dağıtım sonrası sağlık kontrolü zorunludur | .workflows/deployment.md satır 182 |

## §D Sürüm ve uyumluluk

Bu bölüm, bağımlılıkların sürüm pinlerini ve uyumluluk sınırlarını sabitler.

### §D.1 PHP tarafı sürümleri (composer.json yüzeyi)

| # | Dosya | Satır | İfade | Anlam |
|---|---|---|---|---|
| 1 | shared/composer.json | 6 | `"version": "2.0.0"` | paket sürümü |
| 2 | shared/composer.json | 8 | `"php": ">=8.4"` | taban dil sürümü |
| 3 | shared/composer.json | 9 | `"ext-apcu": "*"` | zorunlu eklenti |
| 4 | shared/composer.json | 13 | `"firebase/php-jwt": "^7.2"` | JWT |
| 5 | shared/composer.json | 19 | `"psr/log": "^3.0"` | log sözleşmesi |
| 6 | shared/composer.json | 24 | `"phpunit/phpunit": "^10.5"` | test çatısı |
| 7 | shared/composer.json | 25 | `"phpstan/phpstan": "^1.10"` | statik analiz |
| 8 | api.coremusic.net/composer.json | 15 | `"php": ">=8.4"` | hizmet tabanı |
| 9 | auth.coremusic.net/composer.json | 15 | `"php": ">=8.4"` | hizmet tabanı |
| 10 | home.coremusic.net/composer.json | 15 | `"php": ">=8.4"` | hizmet tabanı |

### §D.2 Kilitli sürümler ve paket künyesi

| # | Dosya | Satır | Değer | Not |
|---|---|---|---|---|
| 1 | shared/composer.lock | 7 | `content-hash: 6f3426a6e4c5676411252cd4a6063caf` | kilit bütünlüğü |
| 2 | shared/composer.lock | 10-11 | `firebase/php-jwt` `v7.2.1` | kilitli paket |
| 3 | shared/composer.lock | 611-612 | `psr/log` `3.0.2` | kilitli log sözleşmesi |
| 4 | shared/composer.lock | 1 | `{` (2.760 satır toplam) | dosya boyutu |
| 5 | Kilit dosyası sayısı | — | 3 (shared · api · home) | auth/media lock YOK |
| 6 | composer.json sayısı | — | 5 (shared · auth · api · home · media) | root'ta json YOK |
| 7 | package.json | 11 · 15 | `@playwright/test` / `playwright` `^1.63.0` | e2e sürümü |
| 8 | package.json | 16 | `vitest` `^5.0.3` | unit test sürümü |
| 9 | package.json | 12 | `@vitest/coverage-v8` `^5.0.3` | coverage sağlayıcı |
| 10 | package.json | 14 | `jsdom` `^30.1.1` | DOM taklidi |

### §D.3 Uyumluluk notları

| # | Uyumluluk | Durum | Kanıt |
|---|---|---|---|
| 1 | PHP 8.4 ↔ composer `>=8.4` | uyumlu | ci.yml satır 22 · composer.json satır 8 |
| 2 | Node 26 ↔ vitest engines `^22.12\|\|^24\|\|>=26` | uyumlu | ci.yml satır 151 yorumu |
| 3 | Node 26 ↔ jsdom engines `^22.22.2\|\|^24.15\|\|>=26` | uyumlu | ci.yml satır 151 yorumu |
| 4 | psr/log `^3.0` ↔ kilitli `3.0.2` | uyumlu | composer.lock satır 611-612 |
| 5 | phpunit `^10.5` ↔ PHP 8.4 | uyumlu (kapsam) | composer.json satır 24 |
| 6 | Aksiyon pinleri `@v2`/`@v4` | major-pin (SHA değil) | ci.yml · secret-scan.yml |
| 7 | 5 composer.json ↔ 3 composer.lock | **kilit eksik** (auth · media) | disk ölçümü |
| 8 | 5 composer.json ↔ ci.yml 3 hedef | **kapsam eksik** (api · media) | ci.yml satır 48-58 |
| 9 | `.gitleaks.toml` ↔ secret-scan.yml:4 yorumu | **çelişki** — yorum "YOK" diyor, dosya var | 2026-10-13 |
| 10 | `.git/HEAD` ↔ "depo YOK" kaydı | **çelişki** — depo var (`main`) | 2026-10-13 |

## §E Devre dışı bırakma

Bu bölüm, bağımlılıkların/ kapıların neyin kapatılabileceğini ve kapatılamayacağını tanımlar.

### §E.1 Kapatılabilir yüzeyler

| # | Yüzey | Kapatma koşulu | Etki | Yetki |
|---|---|---|---|---|
| 1 | js-coverage (soft) | zaten `continue-on-error: true` | görünür-kırmızı, akış devam | DevOps + QA (A7) |
| 2 | js-e2e (soft) | zaten `continue-on-error: true` | akış devam eder | DevOps + QA (A6) |
| 3 | composer cache | `actions/cache@v4` adımı kaldırılabilir | yavaşlama | DevOps (onaylı) |
| 4 | npm cache | setup-node `cache: npm` kapatılabilir | yavaşlama | DevOps (onaylı) |
| 5 | push tetikleyici | `on: push` kaldırılabilir | yalnız PR koşusu | DevOps (onaylı) |
| 6 | matrix satırı | auth satırı çıkarılabilir | kapsam daralır | QA + kullanıcı onayı |
| 7 | concurrency iptali | `cancel-in-progress: false` yapılabilir | kaynak israfı | DevOps (onaylı) |
| 8 | secret-scan job | kapatılabilir ama **kapatılmamalı** | sızıntı riski | Security onayı gerekli |
| 9 | composer-audit | kapatılabilir ama kapatılmamalı | bilinen CVE kaçar | Security onayı gerekli |
| 10 | php-lint/PHPStan | yalnız kullanıcı onayıyla | statik kapı kalkar | kullanıcı onayı |

### §E.2 Kapatılamaz / kapatılmamalı yüzeyler

| # | Yüzey | Neden kapatılamaz | Kaynak |
|---|---|---|---|
| 1 | php-test job | test kapısı domainin çekirdeği | v2 §2.18/§2.19 |
| 2 | secret-scan.yml | sızıntı kapısı (Security sahipliği) | .github/AGENTS.md satır 60 |
| 3 | `.gitleaks.toml` allowlist kısıtı | allowlist yalnız test sahte şifresi içerir | .gitleaks.toml satır 3-4 |
| 4 | permissions `contents: read` | en az yetki ilkesi | ci.yml satır 14-15 |
| 5 | commit yasağı | yetki orkestratörde | .github/AGENTS.md satır 85 |
| 6 | onay kapısı (yeni yml) | onaysız kapı çalışmaya başlar | .github/AGENTS.md satır 63 |
| 7 | deployment Aşama 4 onayı | insan kapısı | .workflows/deployment.md satır 156 |
| 8 | deployment §6 yasaklar | dağıtım yasağı listesi | .workflows/deployment.md satır 257 |
| 9 | Health kontrolü (Aşama 6) | yayın sağlığı | .workflows/deployment.md satır 182 |
| 10 | Authority son satır | dosya standardı | kapanış kuralı |

### §E.3 Kapatma prosedürü

| # | Adım | Aksiyon | Kanıt |
|---|---|---|---|
| 1 | Taslak yaz | devops-dogrulama.md §C kapısıyla eşleştir | kapı tablosu |
| 2 | Onay al | kullanıcı onayı (yeni/değişen workflow) | .github/AGENTS.md satır 63 |
| 3 | Değişikliği uygula | yalnız hedef `.yml` | beyaz-liste |
| 4 | Ölç | `ReadAllLines` + job sayısı | ölçüm komutları |
| 5 | Kapıyı çalıştır | ilgili job | ci.yml |
| 6 | Çelişkiyi kaydet | §D tablosuna satır ekle | disk kazanır |
| 7 | Raporla | orkestratöre teslim | rapor |
| 8 | Commit atma | yetki orkestratörde | .github/AGENTS.md satır 85 |

## §F Repo kanıtları (devops-bagimliliklar)

Dosya: `.github/workflows/ci.yml` · Satır: 8 — `name: CI Pipeline` (203 satır, 6 job).

Dosya: `.github/workflows/ci.yml` · Satır: 68 — `needs: php-lint` (kapı bağımlılığı).

Dosya: `.github/workflows/ci.yml` · Satır: 105 — `run: vendor/bin/phpunit "$PHPUNIT_ARGS"`.

Dosya: `.github/workflows/ci.yml` · Satır: 126 — `composer audit` (shared hedefi).

Dosya: `.github/workflows/ci.yml` · Satır: 158 — `run: npm run test:js`.

Dosya: `.github/workflows/ci.yml` · Satır: 176 — `run: npm run test:js:coverage`.

Dosya: `.github/workflows/ci.yml` · Satır: 203 — `run: npm run test:e2e` (son satır).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 29 — `uses: gitleaks/gitleaks-action@v2` (31 satır).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 4 — `diskte .gitleaks.toml YOK` yorumu (çelişki — §D.3/9).

Dosya: `.github/AGENTS.md` · Satır: 60 — `workflows/secret-scan.yml` sahipliği: Security (kapsam) · DevOps (yml).

Dosya: `.github/AGENTS.md` · Satır: 63 — `Yeni .yml (gelecek)` → Taslak → kullanıcı onayı → uygulama.

Dosya: `.github/AGENTS.md` · Satır: 85 — `Subagent git commit ATMAZ` (200 satır).

Dosya: `shared/composer.json` · Satır: 8 — `"php": ">=8.4"` (50 satır).

Dosya: `shared/composer.json` · Satır: 19 — `"psr/log": "^3.0"`.

Dosya: `shared/composer.json` · Satır: 47 — `"test": "phpunit"`.

Dosya: `shared/composer.json` · Satır: 48 — `"stan": "phpstan analyse src --level=5"`.

Dosya: `shared/composer.lock` · Satır: 7 — `"content-hash": "6f3426a6e4c5676411252cd4a6063caf"` (2.760 satır).

Dosya: `shared/composer.lock` · Satır: 611-612 — `"name": "psr/log"` / `"version": "3.0.2"`.

Dosya: `package.json` · Satır: 5 — `"test:js": "vitest run --root assets.coremusic.net"` (18 satır).

Dosya: `package.json` · Satır: 8 — `"test:e2e": "playwright test -c assets.coremusic.net/playwright.config.ts"`.

Dosya: `package.json` · Satır: 16 — `"vitest": "^5.0.3"`.

Dosya: `.gitleaks.toml` · Satır: 1 — `# CoreMusic - gitleaks config` (18 satır).

Dosya: `.workflows/deployment.md` · Satır: 156 — `### Aşama 4: Onay` (281 satır).

Dosya: `.workflows/deployment.md` · Satır: 212 — `## 4. Geri Dönüş (Rollback)`.

Dosya: `.git/HEAD` · Satır: 1 — `ref: refs/heads/main` (depo VAR — §D.3/10 çelişkisi).

Dosya: `api.coremusic.net/composer.json` · Satır: 15 — `"php": ">=8.4"` (45 satır).

Dosya: `auth.coremusic.net/composer.json` · Satır: 15 — `"php": ">=8.4"` (45 satır).

Dosya: `home.coremusic.net/composer.json` · Satır: 15 — `"php": ">=8.4"` (41 satır).

Dosya: `media.coremusic.net/composer.json` · Satır: 2 — `"name": "coremusic/media"` (46 satır).

Dosya: `.ai/architecture/K018-devops/index.md` · Satır: 20 — `K019-testing # CI'da koşulan test kapısı` (85 satır).

Dosya: `.ai/architecture/index.md` · Satır: 462 — `| K018-devops | 17 | 2.819 |` (bayat sayım — §A.5/3).

### §F.1 Kanıt zinciri (iddia → dosya → satır)

| # | İddia (bu dosyada) | Dosya | Satır | Bağlantı |
|---|---|---|---|---|
| 1 | CI test job'ları K019'u koşar | ci.yml | 105 · 158 · 203 | §2/1 |
| 2 | GitLeaks K009 iş birliği | secret-scan.yml | 29 | §2/2 |
| 3 | toolchain bağımlılığı | ci.yml · package.json | 22 · 151 · 5 | §2/3 |
| 4 | aksiyon pinleri | ci.yml · secret-scan.yml | 30 · 33 · 41 · 149 · 29 | §2/4 · §5/4 |
| 5 | composer json/lock 5/3 | dizin ölçümü | — | §D.2/5-6 |
| 6 | soft gate bağımlılığı | ci.yml | 167 · 183 | §5/3 |
| 7 | eski plan referansı | ci.yml | 3 | §5/5 |
| 8 | config çelişkisi | secret-scan.yml · .gitleaks.toml | 4 · 1 | §D.3/9 |
| 9 | depo çelişkisi | .git/HEAD | 1 | §D.3/10 |
| 10 | deploy onayı | .workflows/deployment.md | 156 | §3/3 |
| 11 | K-space kaldırıldı iddiası | shared/AGENTS.md §5 ↔ disk | — | §A.4/4 |
| 12 | FM şeması | index.md | 382 | FM |
| 13 | registry bayat sayım | index.md | 462 | §A.5/3 |
| 14 | sahiplik + onay | .github/AGENTS.md | 60 · 63 | §4 |
| 15 | commit yasağı | .github/AGENTS.md | 85 | §E.2/5 |

### §F.2 Revizyon ölçüm günlüğü

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi uzunluk | 73 satır | P1 başlangıç değeri |
| 2 | Revizyon sonrası uzunluk | §H/1 hücresinde ölçüldü | ≥ 500 hedefi |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` | `parent` anahtarı 0 |
| 4 | `group` değeri | `mimari` | tırnaksız 9. alan |
| 5 | §A girdi satırı | 10+10+12+8+10 = 50 | vault · disk · CI · red · ölçüm |
| 6 | §B çıktı satırı | 10+10+10 = 30 | tablo · alıcı · biçim |
| 7 | §C çalışma zamanı | 10+10+10+10 = 40 | log · test · CI · kural |
| 8 | §D sürüm | 10+10+10 = 30 | php · lock · uyumluluk |
| 9 | §E devre dışı | 10+10+8 = 28 | kapatılabilir · kapatılamaz · prosedür |
| 10 | §F repo kanıtı | `Dosya:` satırı §H/5 hücresinde | gerçek satır indeksi |
| 11 | §F.1 kanıt zinciri | 15 | iddia → dosya → satır |
| 12 | §G web tablosu | 1 satır | envanter dışı · HTTP 200 |
| 13 | Envanter kesişimi | 0 kopya | 1441 kayıt |
| 14 | Yasaklı yer-tutucu | 0 | §H/7 |
| 15 | K018 disk sayım | 19 dosya / 3.866 satır | 2026-10-13 |
| 16 | Git commit | atılmadı | yetki orkestratörde |
| 17 | Beyaz-liste | yalnız bu dosya | yasak yüzey yazımı 0 |

## §G Web tablosu (devops-bagimliliklar)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Azure Pipelines artifact'ları, bir aşamadan diğerine ve dağıtıma taşınmak için kullanılır; jobs arası paylaşım `PublishPipelineArtifact` ile sağlanır | https://learn.microsoft.com/en-us/azure/devops/pipelines/artifacts/artifacts-overview?view=azure-devops | 2026-10-13 | HTTP 200 · doğrulandı · envanter dışı |

## §H Revizyon doğrulama kaydı (devops-bagimliliklar)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 511 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan, tırnaksız) · `parent` anahtarı 0 | ✓ |
| 4 | Zorunlu bölümler | `## §A Girdi` · `## §B Çıktı` · `## §C Çalışma zamanı` · `## §D Sürüm ve uyumluluk` · `## §E Devre dışı bırakma` | ✓ 5/5 |
| 5 | Repo kanıtları | en az 1 `Dosya:` + `Satır:` kanıt satırı | ✓ (§F) |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 8 | §1-§7 gövdesi | aynen korundu (yalnız FM ve Authority satırı revize) | ✓ |
| 9 | Çelişki kaydı | depo · gitleaks config · K-space "kaldırıldı" iddiası | ✓ §D.3 · §A.4 |
| 10 | Yazılan dosya | bu dosya dahil 8 (P0 2 + P1 6) | ✓ |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
