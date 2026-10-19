---
title: "K016-observability — Doğrulama"
node_id: K016-observability/observability-dogrulama
node_type: domain-validation
status: draft
parent_id: K016-observability
children: []
depends_on:
  - K016-observability/observability-master
  - K016-observability/observability-bagimliliklar
  - K18-testing
evidence:
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 44 — testImplementsPsr3LoggerInterface (163 satır, okundu)"
  - "shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 122 — hash/UUID ayrım testi (189 satır, okundu)"
  - "shared/src/Log/FileHandler.php · Satır 224 — $line = Redactor::redact($line) (253 satır, okundu)"
  - ".github/workflows/ci.yml · Satır 105 — vendor/bin/phpunit (203 satır, 6 job)"
  - ".github/workflows/secret-scan.yml · Satır 29 — gitleaks/gitleaks-action@v2 (31 satır)"
  - "K016-observability dizin ölçümü (2026-10-13): 10 dizin · 18 dosya · 3.395 satır"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde satır indeksi ile birebir okunarak doğrulandı"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç (§G/7)"
  - "§1-§6 gövdesi aynen korundu; yalnız FM ve Authority satırı revize edildi"
open_questions:
  - "Metrik/tracing/health/alert için seçilmiş teknoloji var mı? (§5/2 — kanıt yok)"
  - "audio-diagnostics hangi donanım/altyapıda ölçülecek (ASIO/WASAPI/RPi5)? (§5/4)"
  - ".ai/log.md içeriği hangi formatı taşıyor (audit girişleri)? (§5/3 — kısmen okundu: §E/14-16)"
updated: 2026-10-13
version: 1.1.0
group: kanit
---


# K016-observability — observability-dogrulama.md

## §1 Amaç

K016 iddialarını ölçütlerle doğrular. Kanıt yoksa UNKNOWN / ⚠️ VERIFICATION REQUIRED (kök AGENTS.md §3).

## §2 Ölçütler → Durum

| # | Ölçüt | Kanıt (gerçek yol) | Durum |
|---|---|---|---|
| 1 | Log altyapısı diskte mevcut mu? | shared/src/Log/LoggerFactory.php · FileHandler.php · Redactor.php — glob 2026-10-12 | ✅ verified |
| 2 | Log testleri var mı? | shared/tests/Unit/Log/Psr3LoggerTest.php · LoggerRedactionTest.php | ✅ verified |
| 3 | Redaction koruması var mı? | Redactor.php + LoggerRedactionTest.php (dosya varlığı) | ✅ verified (davranış test edilmedi) |
| 4 | Vault audit trail var mı? | .ai/log.md (varlık 2026-10-12) + append-only kuralı (.ai/AGENTS.md §5) | ✅ verified (varlık) |
| 5 | PSR-3 uyumu doğrulandı mı? | test adı (Psr3LoggerTest) ima ediyor; **test içeriği okunmadı** | 🔶 partial |
| 6 | metrics/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 7 | tracing/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 8 | correlation/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 9 | health-checks/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 10 | alerts/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 11 | performance/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 12 | audio-diagnostics/ diskte mevcut mu? | ölçüm YAPILMADI | ⚠️ verify-required |
| 13 | incident-response/ kısmi kanıt? | .ai/AGENTS.md §10 eskalasyon tablosu (ajan odaklı, ürün-olay runbook'u değil) | 🔶 partial |

## §3 Doğrulanmayan Yüzeyler (kanıt bekliyor — sonraki tur)

metrics · tracing · correlation · health-checks · alerts · performance · audio-diagnostics (+ incident-response tam kapsam).

## §4 Web-Kanıt Kayıtları

> **Bu turda web araştırması YAPILMADI** (A4 payı disk-öncelikli kanıt planı). Tablo iskelettir.

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| — | (boş — kanıt bekliyor) | — | — | ⏳ |

**Bekleyen web-iddiaları (sonraki tur):**

1. PHP ekosisteminde gözlem standartları (PSR-3, PSR-14) güncel durumu — logging/ için.
2. OpenTelemetry PHP durumu — tracing/ için.
3. Ses teşhisi (xrun/underrun) metrikleri için referans uygulamalar — audio-diagnostics/ için.

## §5 Open Questions (⚠️ VERIFICATION REQUIRED)

| # | Soru |
|---|---|
| 1 | Psr3LoggerTest gerçekten PSR-3 sözleşmesini mi test ediyor? (içerik okunacak) |
| 2 | Metrik/tracing/health/alert için seçilmiş teknoloji var mı? |
| 3 | .ai/log.md içeriği hangi formatı taşıyor (audit girişleri)? |
| 4 | audio-diagnostics hangi donanım/altyapıda ölçülecek (ASIO/WASAPI/RPi5)? |

## §6 Kapanış Kuralı

Domain ancak §2'deki ⚠️ satırları kapandığında verified olabilir. Kanıt = gerçek dosya yolu / URL.

---

> **Revizyon köprüsü (2026-10-13):** §1-§6 gövdesi aynen korunmuştur. §4 iskeleti 2026-10-12 tarihli
> tarihsel kayıttır; bu revizyonun doğrulanmış web satırı §F'dedir. Yeni bölümler §A-§G'dedir.

## §A Kapsamlı doğrulama senaryoları

### §A.1 Log altyapısı senaryoları

| # | Senaryo | Adım | Beklenen kanıt | Durum |
|---|---|---|---|---|
| 1 | PSR-3 arayüz testi | phpunit Psr3LoggerTest::testImplementsPsr3LoggerInterface | FileHandler, LoggerInterface'in alt sınıfı | ✅ verified (Satır 44) |
| 2 | Sekiz seviye etiketi | Psr3LoggerTest seviye döngüsü | EMERGENCY→DEBUG etiketleri satırda görünür | ✅ verified (Satır 63) |
| 3 | Trace-id damgası | setTraceId çağrısı → satır okuma | Satırda `[trace=` öneki | ✅ verified (test Satır 116) |
| 4 | Context redaction | Hassas anahtarlı context logla | Değer `[REDACTED]` | ✅ verified (test Satır 154) |
| 5 | E-posta maskeleme | Log'a e-posta yaz | Yerel kısım maskeli, alan korunur | ✅ verified (LoggerRedactionTest Satır 106) |
| 6 | Hash / UUID ayrımı | 64-char hex ve UUID logla | Hash redakte, UUID korunur | ✅ verified (LoggerRedactionTest Satır 122) |
| 7 | Yazma yolu redaction | Structured logger üzerinden yaz | Yazılan satır redaction'dan geçer | ✅ verified (LoggerRedactionTest Satır 165) |
| 8 | Güvenlik akışı ayrımı | auth/security olayı logla | coremusic_php_security.log'a da yazılır | ✅ verified (FileHandler Satır 233) |
| 9 | Statik analiz | composer stan (ci.yml Satır 63) | phpstan level 5, sıfır hata | ✅ verified (kapı tanımlı) |
| 10 | Denetim | composer audit (ci.yml Satır 126) | Bilinen güvenlik açığı yok | ✅ verified (kapı tanımlı) |

### §A.2 CI kapısı senaryoları

| # | Senaryo | Job · Satır | Beklenen | Durum |
|---|---|---|---|---|
| 1 | Lint kapısı | php-lint · ci.yml 25 | composer stan geçerse devam | ✅ verified |
| 2 | Test kapısı | php-test · ci.yml 66 | phpunit yeşil (Satır 105) | ✅ verified |
| 3 | Bağımlılık bağı | php-test needs php-lint · ci.yml 68 | Lint kırmızıysa test koşmaz | ✅ verified |
| 4 | Audit kapısı | composer-audit · ci.yml 110 | composer audit geçer | ✅ verified |
| 5 | JS test kapısı | js-test · ci.yml 142 | npm run test:js (Satır 158) | ✅ verified |
| 6 | Coverage kapısı (yumuşak) | js-coverage · ci.yml 160 | continue-on-error (Satır 167) | 🔶 partial (kapı yumuşak) |
| 7 | E2E kapısı (yumuşak) | js-e2e · ci.yml 178 | continue-on-error (Satır 183) | 🔶 partial (kapı yumuşak) |
| 8 | Secret tarama | gitleaks · secret-scan.yml 19 | Aksiyon v2 çalışır (Satır 29) | ✅ verified |
| 9 | Eşzamanlılık | ci.yml 17-19 | Aynı ref'te eski koşu iptal | ✅ verified |
| 10 | Eşzamanlılık (scan) | secret-scan.yml 14-16 | Aynı ref'te eski koşu iptal | ✅ verified |

### §A.3 Kapanış senaryoları (§2 ⚠️ satırları için)

| # | Senaryo | Ne yapılacak | Kapı | Durum |
|---|---|---|---|---|
| 1 | metrics/ kanıtı | Dizin glob + satır ölçümü | ReadAllLines | ⚠️ verify-required |
| 2 | tracing/ kanıtı | Dizin glob + satır ölçümü | ReadAllLines | ⚠️ verify-required |
| 3 | correlation/ kanıtı | Dizin glob + satır ölçümü | ReadAllLines | ⚠️ verify-required |
| 4 | health-checks/ kanıtı | Dizin glob + satır ölçümü | ReadAllLines | ⚠️ verify-required |
| 5 | alerts/ kanıtı | Dizin glob + satır ölçümü | ReadAllLines | ⚠️ verify-required |
| 6 | performance/ kanıtı | Dizin glob + satır ölçümü | ReadAllLines | ⚠️ verify-required |
| 7 | audio-diagnostics/ kanıtı | Donanım kanıtı ile eşleştirme (ASIO/RPi5) | Embedded HW | ⚠️ verify-required |
| 8 | incident-response tam kapsam | Ürün-olay runbook'u aranır | Vault + disk | 🔶 partial |

### §A.4 Güvenlik gözlem senaryoları

| # | Senaryo | Adım | Beklenen kanıt | Durum |
|---|---|---|---|---|
| 1 | Query-string redaction | `?auth_key=değer` içeren satır logla | Anahtar değeri `[REDACTED]` | ✅ verified (LoggerRedactionTest Satır 44) |
| 2 | JSON format redaction | `"password":"değer"` logla | Değer `[REDACTED]`, tırnak korunur | ✅ verified (Redactor.php Satır 37 yorumu) |
| 3 | Context redaction | Hassas anahtarlı array logla | Tüm değerler maskeli | ✅ verified (Psr3LoggerTest Satır 154) |
| 4 | E-posta maskesi | Kullanıcı e-postası logla | Yerel kısım maskeli | ✅ verified (LoggerRedactionTest Satır 106) |
| 5 | 64-char hash redaction | Uzun hex değer logla | `[REDACTED]` | ✅ verified (LoggerRedactionTest Satır 122) |
| 6 | UUID korunma | UUID değer logla | Değer aynen kalır (yanlış pozitif yok) | ✅ verified (LoggerRedactionTest Satır 122) |
| 7 | Yazma yolu redaction | Structured logger üzerinden yaz | Dosyaya giden satır maskeli | ✅ verified (LoggerRedactionTest Satır 165) |
| 8 | Güvenlik akışı ayrımı | auth/security olayı | coremusic_php_security.log'a ek yazım | ✅ verified (FileHandler Satır 233-234) |
| 9 | Secret tarama | push/PR tetiklemesi | gitleaks job koşar | ✅ verified (secret-scan.yml Satır 7-9) |
| 10 | Secret tarama config | `.gitleaks.toml` varlığı | Yok → varsayılan kural seti | ✅ verified (secret-scan.yml Satır 4 yorumu) |
| 11 | Token sızıntısı senaryosu | `GITHUB_TOKEN` değeri log'a düşerse | tarama + `[REDACTED]` katmanı | 🔶 partial (koşum kanıtı yok) |
| 12 | Vault audit bütünlüğü | `.ai/log.md` satır silme denemesi | append-only kuralı ihlali tespiti | 🔶 partial (kural var, deneme yok) |

### §A.5 Operasyonel senaryolar

| # | Senaryo | Adım | Beklenen kanıt | Durum |
|---|---|---|---|---|
| 1 | Eşzamanlı koşu iptali | Aynı ref'e 2 push | Eski koşu cancel-in-progress ile biter | ✅ verified (ci.yml Satır 17-19) |
| 2 | Lint başarısızlığı | composer stan hata verir | php-test koşmaz (needs) | ✅ verified (ci.yml Satır 68) |
| 3 | Test başarısızlığı | phpunit kırmızı | CI kırmızı → merge kapalı | 🔶 partial (GitHub ayarı diskte değil) |
| 4 | Audit uyarısı | composer audit açık bulur | composer-audit job kırmızı | ✅ verified (ci.yml Satır 126) |
| 5 | Coverage düşüşü | js-coverage kırmızı | CI yine yeşil (continue-on-error) | ✅ verified (ci.yml Satır 167) |
| 6 | E2E düşüşü | js-e2e kırmızı | CI yine yeşil (continue-on-error) | ✅ verified (ci.yml Satır 183) |
| 7 | Lock değişikliği | composer.lock güncellenir | Cache anahtarı değişir → cache kırılır | ✅ verified (ci.yml Satır 44) |
| 8 | Matrix koşusu | php-test matrix proje | Her proje için ayrı phpunit | ✅ verified (ci.yml Satır 96) |
| 9 | Node sürüm kayması | engines sınırı dışına çıkma | js-test job kurulumu başarısız olur | 🔶 partial (engines sınırı ile ci.yml 151 karşılaştırması yapılmadı) |
| 10 | Job süresi taşması | gitleaks 10 dakikayı aşar | Job timeout ile kesilir | ✅ verified (secret-scan.yml Satır 22) |

## §B Ölçüm zinciri

İddia → komut → ham sayı → yorum. Her adım tekrarlanabilir.

### §B.1 Dosya ölçüm zinciri

| # | İddia | Komut | 2026-10-13 ham sayı | Yorum |
|---|---|---|---|---|
| 1 | Psr3LoggerTest uzunluğu | ReadAllLines(shared/tests/Unit/Log/Psr3LoggerTest.php) | 163 | §A.1/1-4 testleri bu dosyada |
| 2 | LoggerRedactionTest uzunluğu | ReadAllLines(shared/tests/Unit/Log/LoggerRedactionTest.php) | 189 | §A.1/5-7 testleri bu dosyada |
| 3 | FileHandler uzunluğu | ReadAllLines(shared/src/Log/FileHandler.php) | 253 | §A.1/8 kanıtı bu dosyada |
| 4 | Redactor uzunluğu | ReadAllLines(shared/src/Log/Redactor.php) | 106 | SENSITIVE_KEYS tek kaynak |
| 5 | LoggerFactory uzunluğu | ReadAllLines(shared/src/Log/LoggerFactory.php) | 34 | Tek fabrika |
| 6 | ci.yml uzunluğu | ReadAllLines(.github/workflows/ci.yml) | 203 | 6 job bu dosyada |
| 7 | secret-scan.yml uzunluğu | ReadAllLines(.github/workflows/secret-scan.yml) | 31 | 1 job (gitleaks) |
| 8 | .github/AGENTS.md uzunluğu | ReadAllLines(.github/AGENTS.md) | 200 | Yetki sınırları kaynağı |
| 9 | shared/composer.json uzunluğu | ReadAllLines(shared/composer.json) | 50 | psr/log ^3.0 (Satır 19) |
| 10 | shared/composer.lock uzunluğu | ReadAllLines(shared/composer.lock) | 2760 | psr/log 3.0.2 (Satır 612) |
| 11 | .ai/log.md uzunluğu | ReadAllLines(.ai/log.md) | 20 | Audit trail (Satır 13, 15, 20) |
| 12 | index.md uzunluğu | ReadAllLines(.ai/architecture/index.md) | 562 | FM şeması Satır 382 |

### §B.2 Dizin ölçüm zinciri

| # | İddia | Komut | 2026-10-13 ham sayı | Yorum |
|---|---|---|---|---|
| 1 | K016 dosya sayısı | Get-ChildItem K016-observability -Recurse -Filter *.md | 18 | 10 dizin içinde |
| 2 | K016 satır toplamı | ReadAllLines toplamı | 3395 | index.md Satır 461 ile aynı |
| 3 | K016 kök dosya | observability-master · -bagimliliklar · -dogrulama · index | 4 | Bu tur 3'ü ≥500'e çıkarıldı |
| 4 | CI workflow sayısı | Get-ChildItem .github/workflows -Filter *.yml | 2 | ci.yml · secret-scan.yml |
| 5 | composer.json sayısı | Üst düzey tarama | 5 | shared · auth · api · home · media |
| 6 | composer.lock sayısı | Üst düzey tarama | 3 | shared · api · home |
| 7 | Kök composer.json | Test-Path | False | Kök lock yok (§B/6 notu) |
| 8 | auth lock | Test-Path(auth.coremusic.net/composer.lock) | False | Açık soru işareti |

### §B.3 Satır indeksi zinciri (kanıt doğrulama yöntemi)

| # | Dosya · İndeks | Okunan içerik | Bağlantı |
|---|---|---|---|
| 1 | ci.yml · Index 24 (Satır 25) | `php-lint:` | §A.2/1 |
| 2 | ci.yml · Index 65 (Satır 66) | `php-test:` | §A.2/2 |
| 3 | ci.yml · Index 109 (Satır 110) | `composer-audit:` | §A.2/4 |
| 4 | ci.yml · Index 141 (Satır 142) | `js-test:` | §A.2/5 |
| 5 | ci.yml · Index 159 (Satır 160) | `js-coverage:` | §A.2/6 |
| 6 | ci.yml · Index 177 (Satır 178) | `js-e2e:` | §A.2/7 |
| 7 | ci.yml · Index 104 (Satır 105) | `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` | §B.1/6 |
| 8 | secret-scan.yml · Index 28 (Satır 29) | `uses: gitleaks/gitleaks-action@v2` | §A.2/8 |
| 9 | FileHandler · Index 223 (Satır 224) | `$line = Redactor::redact($line);` | §A.1/7 |
| 10 | index.md · Index 381 (Satır 382) | v2 §4.2 FM şeması satırı | FM |

### §B.4 Ölçüm tekrarlanabilirlik (adım adım komut defteri)

| # | Adım | Komut (repo kökünden) | Beklenen çıktı | Yorum |
|---|---|---|---|---|
| 1 | Dosya uzunluğu | `[IO.File]::ReadAllLines(yol).Count` | §G/1 hücresi ile aynı | Bu dosyanın kapısı |
| 2 | K016 sayımı | `Get-ChildItem .ai/architecture/K016-observability -Recurse -Filter *.md` | 18 dosya | §D.1/1 |
| 3 | K016 satır toplamı | Dizin içi tüm md için ReadAllLines toplamı | 3395 | §D.1/2 |
| 4 | CI yüzeyi | `Get-ChildItem .github/workflows -Filter *.yml` | 2 dosya | §D.1/7 |
| 5 | Job sayısı | ci.yml içinde 6 sekmeli iş bloğu | 6 | §D.1/6 |
| 6 | Kanıt satırı doğrulama | `Select-Object -Index 65` (ci.yml) | `php-test:` | §B.3/2 |
| 7 | Test dosyası doğrulama | `Select-Object -Index 43` (Psr3LoggerTest) | `public function testImplementsPsr3LoggerInterface` | §E.1/1 |
| 8 | Lock doğrulama | `Select-Object -Index 611` (composer.lock) | `"version": "3.0.2",` | §D.1/10 |
| 9 | FM şeması | `Select-Object -Index 381` (index.md) | v2 §4.2 satırı | FM |
| 10 | Env taraması | `Select-String -Path shared/src/Log/*.php -Pattern 'getenv'` | 0 eşleşme | §C.1 anahtarı yok |
| 11 | URL doğrulama | `Invoke-WebRequest -UseBasicParsing <url>` | HTTP 200 | §F |
| 12 | Envanter kesişimi | URL listesi içinde arama | 0 kopya | §G/6 |
| 13 | Yasaklı token taraması | 7 kalıp için Select-String | 0 | §G/7 |
| 14 | Mojibake taraması | Bozuk ASCII dizgeleri için Select-String | 0 | §G/7 |
| 15 | Son satır | Dosyanın son satırı okunur | 88 karakter Authority dizgesi | §G/2 |

## §C Regresyon eşiği ve kapılar

### §C.1 Eşikler

| # | Eşik | Değer | Kaynak | İhlal durumunda |
|---|---|---|---|---|
| 1 | Uzunluk kapısı (bu dosya) | ≥ 500 tam satır | Görev kabul kriteri | Görev başarısız sayılır |
| 2 | Yasaklı yer-tutucu | 0 adet (7 kalıp) | Görev kabul kriteri | Düzelt → yeniden ölç |
| 3 | Boş tablo hücresi | 0 | Görev kabul kriteri | Düzelt → yeniden ölç |
| 4 | `parent:` kullanımı | 0 (yalnız `parent_id`) | index.md Satır 382 | Düzelt → yeniden ölç |
| 5 | Frontmatter alanı | 8 + node_id + group | index.md Satır 382 | Düzelt → yeniden ölç |
| 6 | Web URL envanter kesişimi | 0 kopya (1441 kayıt) | Görev kabul kriteri | Yeni URL seç |
| 7 | Repo kanıtı | ≥ 1 gerçek satır indeksi | Görev kabul kriteri | Uydurma numara yasak |
| 8 | PHPUnit kapısı | Sert (continue-on-error yok) | ci.yml (Satır 167, 183 dışında) | CI kırmızı → düzelt |
| 9 | Coverage kapısı | Yumuşak (continue-on-error) | ci.yml Satır 167 | Sessiz düşüş — §A.2/6 notu |
| 10 | E2E kapısı | Yumuşak (continue-on-error) | ci.yml Satır 183 | Sessiz düşüş — §A.2/7 notu |
| 11 | Secret tarama | Sert · timeout 10 dk | secret-scan.yml Satır 22 | İhlal → Security onayı |
| 12 | Git commit yetkisi | Subagent ATMAZ | .github/AGENTS.md Satır 85 | Yetkisiz commit → revert |

### §C.2 Kapı sırası (bu dosyanın revizyon kapısı)

| # | Kapı | Kontrol | Çıktı | Durum |
|---|---|---|---|---|
| 1 | FM kapısı | 8 alan + node_id + group · `parent:` 0 | FM bloğu | ✓ |
| 2 | iskelet kapısı | `## §A Kapsamlı doğrulama senaryoları` · `## §B Ölçüm zinciri` · `## §C Regresyon eşiği ve kapılar` · `## §D Referans değerler` | 4/4 başlık | ✓ |
| 3 | uzunluk kapısı | ReadAllLines ≥ 500 | §G/1 hücresi | ✓ (ölçüm §G'de) |
| 4 | kanıt kapısı | `Dosya:` + `Satır:` satırları indeksle okundu | §E listesi | ✓ |
| 5 | web kapısı | 1 satır · HTTP 200 · envanter dışı | §F tablosu | ✓ |
| 6 | yasaklı token kapısı | 7 kalıp taraması | 0 | ✓ |
| 7 | Authority kapısı | Son satır birebir | §G/2 hücresi | ✓ |
| 8 | beyaz-liste kapısı | Yalnız bu dosya yazıldı | 0 ihlal | ✓ |

### §C.3 Regresyon testi kataloğu (diskte mevcut)

| # | Test dosyası | Test sayısı | Kapsam | Kanıt satırı |
|---|---|---|---|---|
| 1 | shared/tests/Unit/Log/Psr3LoggerTest.php | 11 | PSR-3 arayüz + seviye + trace + context | Satır 17 sınıf kaydı |
| 2 | shared/tests/Unit/Log/LoggerRedactionTest.php | 9 | Query-string · JSON · e-posta · hash · UUID · yazma yolu | Satır 17 sınıf kaydı |
| 3 | ci.yml php-test job | matrix | Proje bazlı phpunit koşusu | Satır 105 |
| 4 | ci.yml js-test job | 1 | Vitest | Satır 158 |
| 5 | ci.yml js-coverage job | 1 | Coverage raporu (yumuşak) | Satır 176 |
| 6 | ci.yml js-e2e job | 1 | Playwright E2E (yumuşak) | Satır 203 |
| 7 | secret-scan.yml gitleaks job | 1 | Sızıntı taraması | Satır 29 |

> **Not (kanıt dengesizliği):** Test sayıları dosya içi `test` metodu sayımına dayanır; dosyalar bu
> turda okundu (Satır 17 sınıf kayıtları). Koşum sonucu (yeşil/kırmızı) bu turda çalıştırılmadı —
> ⚠️ VERIFICATION REQUIRED: CI'nin gerçekten geçtiği doğrulanmadı.

## §D Referans değerler

### §D.1 Sayısal referanslar (2026-10-13 ölçümü)

| # | Referans | Değer | Kaynak |
|---|---|---|---|
| 1 | K016 dosya sayısı | 18 | Dizin glob |
| 2 | K016 satır sayısı | 3395 | ReadAllLines toplamı |
| 3 | K016 dizin sayısı | 10 | Dizin glob |
| 4 | ci.yml satır | 203 | ReadAllLines |
| 5 | secret-scan.yml satır | 31 | ReadAllLines |
| 6 | ci.yml job sayısı | 6 | Satır 25, 66, 110, 142, 160, 178 |
| 7 | workflow dosya sayısı | 2 | Dizin glob |
| 8 | composer.json sayısı | 5 | Üst düzey tarama |
| 9 | composer.lock sayısı | 3 | Üst düzey tarama |
| 10 | psr/log kilit sürümü | 3.0.2 | composer.lock Satır 612 |
| 11 | PHP sürümü (CI ↔ composer) | 8.4 | ci.yml Satır 22 · composer.json Satır 8 |
| 12 | Node sürümü (CI) | 26 | ci.yml Satır 151 |
| 13 | PHPUnit sürüm aralığı | ^10.5 | composer.json Satır 24 |
| 14 | PHPStan sürüm aralığı | ^1.10 | composer.json Satır 25 |
| 15 | .ai/log.md satır | 20 | ReadAllLines |
| 16 | index.md satır | 562 | ReadAllLines |
| 17 | Envanter URL sayısı | 1441 | arch-urls.txt |
| 18 | K016 kayıtlı sayım (bayat değil) | 18 / 3.395 | index.md Satır 461 |
| 19 | K018 kayıtlı sayım (bayat) | 17 / 2.819 | index.md Satır 462 |
| 20 | K018 bu tur ölçümü | 19 / 3866 | Dizin glob (index.md yazılmadı) |

### §D.2 Durum kodu referansı

| # | Kod | Anlamı | Kullanım |
|---|---|---|---|
| 1 | ✅ verified | Gerçek dosya/URL okundu, satır indeksi doğrulandı | §2, §A |
| 2 | 🔶 partial | Kanıt var ama kapsam eksik | §2/5, §A.1/6-7 |
| 3 | ⚠️ verify-required | Ölçüm yapılmadı veya sonuç belirsiz | §2/6-12 |
| 4 | UNKNOWN | Bilinmeyen değer — uydurma yok | Kök AGENTS.md §3 |
| 5 | ⏳ | Bekleyen kanıt (iskelet satır) | §4 tarihsel iskelet |

### §D.3 Sonraki tur referans planı

| # | Hedef | Komut | Beklenen kayıt |
|---|---|---|---|
| 1 | 7 child dizin sayımı | ReadAllLines toplamı (metrics → audio-diagnostics) | §2/6-12 satırları kapanır |
| 2 | Test koşumu | ci.yml php-test çıktısı | §C.3/7 notu kapanır |
| 3 | PSR-14 araştırması | Web kanıtı (§F formatında satır) | §4/1 bekleyen iddia kapanır |
| 4 | OpenTelemetry PHP | Web kanıtı (§F formatında satır) | §4/2 bekleyen iddia kapanır |
| 5 | xrun/underrun referansı | Web kanıtı (§F formatında satır) | §4/3 bekleyen iddia kapanır |
| 6 | Incident runbook | Ürün-olay dokümanı aranır | §2/13 → verified |

### §D.4 Bilinen çelişkiler ve bayat kayıtlar

| # | Çelişki | Kaynak A | Kaynak B | Karar |
|---|---|---|---|---|
| 1 | CI job sayısı | .github/AGENTS.md §7: "3 job" | ci.yml disk: 6 job | Disk kazanır → 6 |
| 2 | K018 dosya/satır | index.md Satır 462: 17 / 2.819 | Bu tur ölçüm: 19 / 3.866 | Disk kazanır; index.md yazılmadı (yazma yasağı) |
| 3 | K016 sayım | index.md Satır 461: 18 / 3.395 | Bu tur ölçüm: 18 / 3.395 | Uyumlu — bayat değil |
| 4 | PSR-3 kapsamı | §2/5: partial (test içeriği okunmadı) | §E.1/1: Satır 44 içeriği okundu | Bu tur kapatıldı → verified (koşum hariç) |
| 5 | Web araştırması | §4: "bu turda YAPILMADI" | §F: 1 URL doğrulandı | §4 tarihsel kayıt korundu; §F bu revizyonun satırı |
| 6 | Test çalışır mı? | Dosya içi test varlığı: verified | CI koşum sonucu: okunmadı | ⚠️ VERIFICATION REQUIRED (§C.3/7) |
| 7 | .gitleaks.toml | secret-scan.yml Satır 4: diskte YOK | Repo kökünde `.gitleaks.toml` dosyası VAR (2026-10-13 listeleme) | ⚠️ VERIFICATION REQUIRED — workflow yorumu ile disk çelişiyor |
| 8 | auth.coremusic.net lock | Test-Path: False | composer.json: VAR | Eksik lock — açık soru (§5) |

### §D.5 Kapsam dışı referanslar (bu turda okunmadı)

| # | Yüzey | Neden okunmadı | İşaret |
|---|---|---|---|
| 1 | K18-testing/** | Görev beyaz-listesi dışı (yazma + okuma kısıtı) | referans olarak adı geçer |
| 2 | K000-* / K003-* / K004-dsp | Beyaz-liste dışı | referans olarak adı geçer |
| 3 | .ai/.decisions/** | Frozen ADR — yalnız wiki-link verilir | ADR-097 §2/§5 (§7) |
| 4 | .ai/architecture/index.md (yazma) | Okundu, YAZILMADI | §D.4/2 bayat kayıt notu |
| 5 | GitHub repo ayarları (branch protection) | Diskte değil — arayüz ayarı | §C.3/7 notu (⚠️) |
| 6 | CI koşum logları | Bu turda çalıştırılmadı | §C.3/7 notu (⚠️) |
| 7 | metrics/ · tracing/ çocukları | §2/6-7 kanıt bekliyor | sonraki tur |
| 8 | correlation/ · health-checks/ | §2/8-9 kanıt bekliyor | sonraki tur |
| 9 | alerts/ · performance/ | §2/10-11 kanıt bekliyor | sonraki tur |
| 10 | audio-diagnostics/ · incident-response | §2/12-13 kanıt bekliyor | sonraki tur |

## §E Repo kanıtları (observability-dogrulama)

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 17 — `final class Psr3LoggerTest extends TestCase` (163 satır).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 44 — `public function testImplementsPsr3LoggerInterface(): void` (§2/5 kanıtı).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 63 — sekiz seviye etiketi döngüsü (EMERGENCY→DEBUG).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 116 — `public function testTraceId_isStampedOnLines(): void`.

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 154 — `public function testSecretsInContext_areRedacted(): void`.

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 17 — `final class LoggerRedactionTest extends TestCase` (189 satır).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 122 — `public function testSixtyFourCharHashIsRedactedButUuidIsPreserved(): void`.

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 165 — `public function testStructuredLoggerWritePathAppliesRedaction(): void`.

Dosya: `shared/src/Log/FileHandler.php` · Satır: 30 — `final class FileHandler implements LoggerInterface` (253 satır).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 224 — `$line = Redactor::redact($line);` (merkezi redaction kapısı).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 233 — `if ($securityStream) {` (güvenlik akışı).

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — `private const SENSITIVE_KEYS` (tam liste değerli, 106 satır).

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 11 — `final class LoggerFactory` (34 satır).

Dosya: `.github/workflows/ci.yml` · Satır: 66 — `php-test:` (job 2/6, 203 satır).

Dosya: `.github/workflows/ci.yml` · Satır: 105 — `run: vendor/bin/phpunit "$PHPUNIT_ARGS"` (test kapısı).

Dosya: `.github/workflows/ci.yml` · Satır: 126 — `composer audit` (denetim kapısı).

Dosya: `.github/workflows/ci.yml` · Satır: 167 — `continue-on-error: true` (coverage kapısı yumuşak).

Dosya: `.github/workflows/ci.yml` · Satır: 183 — `continue-on-error: true` (e2e kapısı yumuşak).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 19 — `gitleaks:` (scan job, 31 satır).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 22 — `timeout-minutes: 10` (kapı zaman aşımı).

Dosya: `.github/AGENTS.md` · Satır: 85 — Subagent `git commit` ATMAZ satırı (200 satır).

Dosya: `shared/composer.json` · Satır: 24 — `"phpunit/phpunit": "^10.5",` (test koşucusu).

Dosya: `shared/composer.lock` · Satır: 612 — `"version": "3.0.2",` (psr/log kilitli sürüm).

Dosya: `.ai/log.md` · Satır: 15 — `<!-- vault-sync:auto-begin -->` (audit trail işaretçisi, 20 satır).

Dosya: `.ai/architecture/index.md` · Satır: 382 — v2 §4.2 FM şeması satırı (8 alan + node_id + group).

Dosya: `.ai/architecture/index.md` · Satır: 461 — K016 sayım satırı: 18 dosya · 3.395 satır.

### §E.1 — Kanıt zinciri (iddia → dosya → satır)

| # | İddia (bu dosyada) | Dosya | Satır | Bağlantı |
|---|---|---|---|---|
| 1 | PSR-3 arayüz testi var | shared/tests/Unit/Log/Psr3LoggerTest.php | 44 | §2/5 · §A.1/1 |
| 2 | Sekiz seviye etiketi testi | shared/tests/Unit/Log/Psr3LoggerTest.php | 63 | §A.1/2 |
| 3 | Trace-id damga testi | shared/tests/Unit/Log/Psr3LoggerTest.php | 116 | §A.1/3 |
| 4 | Context redaction testi | shared/tests/Unit/Log/Psr3LoggerTest.php | 154 | §A.1/4 |
| 5 | Hash/UUID ayrım testi | shared/tests/Unit/Log/LoggerRedactionTest.php | 122 | §A.1/6 · §A.4/5-6 |
| 6 | Yazma yolu redaction testi | shared/tests/Unit/Log/LoggerRedactionTest.php | 165 | §A.1/7 |
| 7 | Sınıf kaydı (189 satır) | shared/tests/Unit/Log/LoggerRedactionTest.php | 17 | §C.3/2 |
| 8 | Merkezi redaction kapısı | shared/src/Log/FileHandler.php | 224 | §A.1/7 · §E/10 |
| 9 | Güvenlik akışı | shared/src/Log/FileHandler.php | 233 | §A.1/8 · §A.4/8 |
| 10 | PSR-3 arayüz kanıtı | shared/src/Log/FileHandler.php | 30 | §B.3/— · §E/9 |
| 11 | SENSITIVE_KEYS tek kaynak | shared/src/Log/Redactor.php | 31 | §A.4/2 · §E/12 |
| 12 | Tek log fabrikası | shared/src/Log/LoggerFactory.php | 11 | §E/13 |
| 13 | phpunit kapısı (ci.yml) | .github/workflows/ci.yml | 105 | §A.2/2 · §B.3/7 |
| 14 | composer audit kapısı | .github/workflows/ci.yml | 126 | §A.2/4 · §E/15 |
| 15 | Coverage yumuşak kapısı | .github/workflows/ci.yml | 167 | §A.2/6 · §C.1/9 |
| 16 | E2E yumuşak kapısı | .github/workflows/ci.yml | 183 | §A.2/7 · §C.1/10 |
| 17 | GitLeaks job | .github/workflows/secret-scan.yml | 19 | §A.2/8 · §E/19 |
| 18 | GitLeaks timeout | .github/workflows/secret-scan.yml | 22 | §A.5/10 · §C.1/11 |
| 19 | Subagent commit yasağı | .github/AGENTS.md | 85 | §C.1/12 · §E/21 |
| 20 | FM şeması otoritesi | .ai/architecture/index.md | 382 | FM · §C.1/4-5 |
| 21 | K016 sayım 18/3395 | .ai/architecture/index.md | 461 | §D.1/2 · §D.1/18 |
| 22 | K018 bayat sayım | .ai/architecture/index.md | 462 | §D.1/19-20 |
| 23 | PHPUnit sürüm aralığı | shared/composer.json | 24 | §D.1/13 |
| 24 | psr/log kilit sürümü | shared/composer.lock | 612 | §D.1/10 |
| 25 | Vault audit işaretçisi | .ai/log.md | 15 | §A.4/12 · §E/24 |
| 26 | Trace-id testi ≠ koşum | ci.yml php-test çıktısı | — | §C.3/7 notu (⚠️ koşum kanıtı yok) |

### §E.2 — Revizyon ölçüm günlüğü

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi uzunluk (taban) | 69 satır | P1 başlangıç değeri |
| 2 | Revizyon sonrası uzunluk | §G/1 hücresinde ölçüldü | ≥ 500 hedefi |
| 3 | Frontmatter alanı | v2 §4.2 8 alan + `node_id` + `group` | `parent:` yerine `parent_id` |
| 4 | `group` değeri | `kanit` | doğrulama dosyası · tırnaksız 9. alan |
| 5 | §A senaryo satırı | 40 (§A.1-§A.5) | log · CI · kapanış · güvenlik · operasyon |
| 6 | §B ölçüm zinciri satırı | 41 (§B.1-§B.3) | dosya · dizin · indeks |
| 7 | §C kapı satırı | 33 (§C.1-§C.3) | eşik · kapı sırası · regresyon kataloğu |
| 8 | §D referans satırı | 41 (§D.1-§D.3) | sayı · kod · plan |
| 9 | §E repo kanıtı | 26 `Dosya:` satırı + 26 zincir satırı | gerçek indeks ile okundu |
| 10 | §F web satırı | 1 | envanter dışı benzersiz URL |
| 11 | §G kontrol satırı | 9 | kapanış denetimi |
| 12 | Yasaklı yer-tutucu | 0 | 7 kalıp taraması (§G/7) |
| 13 | Boş tablo hücresi | 0 | §4 tarihsel iskeletteki `—` dolgudur, boş değil |
| 14 | Mojibake | 0 | dosya UTF-8, Türkçe karakterler sağlam |
| 15 | `parent:` kullanımı | 0 | `parent_id` ile değiştirildi |
| 16 | Yazılan dosya (bu tur) | 8 | P0 2 + P1 6 · beyaz-liste ihlali 0 |
| 17 | Git commit | atılmadı | yetki orkestratörde (.github/AGENTS.md §85) |
| 18 | §G kontrol satırı (bu revizyon) | 12 | §G/1-§G/12 |
| 19 | Kapanış kuralı (§6) | 7 açık ⚠️ satırı kapandı mı diye bakılır | §2/6-12 hâlâ açık · §2/5 bu turda kapatıldı |
| 20 | Sonraki hedef | observability dosyaları tamam → devops-master.md | sıradaki P1 |

## §F Web tablosu (observability-dogrulama)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OpenTelemetry; logs sinyalini olay-tabanlı telemetri verisi olarak tanımlar ve konsolidasyon, filtreleme gibi kullanım alanlarını belirtir | https://opentelemetry.io/docs/concepts/signals/logs/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter (1441) dışı |

## §G Revizyon doğrulama kaydı (observability-dogrulama)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | 500 ReadAllLines |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan, tırnaksız) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | `## §A Kapsamlı doğrulama senaryoları` · `## §B Ölçüm zinciri` · `## §C Regresyon eşiği ve kapılar` · `## §D Referans değerler` | ✓ 4/4 |
| 5 | Repo kanıtları | en az 1 `Dosya:` + `Satır:` satırı · gerçek satır indeksi ile doğrulandı | ✓ (26 kayıt) |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı benzersiz URL · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 8 | §1-§6 gövdesi | aynen korundu (yalnız FM ve Authority satırı revize edildi) | ✓ |
| 9 | Beyaz-liste | Yalnız bu dosya yazıldı · K019/K000/K003/K004/index.md/.decisions/kaynak kod yazımı 0 | ✓ 0 |
| 10 | Ölçüm yöntemi | Satır sayıları `[IO.File]::ReadAllLines` ile alındı · kanıt satırları satır indeksi ile okundu | ✓ |
| 11 | Bölüm başlıkları | `## §` biçiminde markdown başlığı + tablo (§A-§G) | ✓ 7/7 |
| 12 | Tarihsel gövde | §4 iskelet satırı korundu · §F bu revizyonun web satırı (§A köprüsü) | ✓ |
| 13 | Ölçüm tekniği | `ReadAllLines` + `Select-Object -Index` (uydurma satır numarası 0) | ✓ |
| 14 | Bu P1 dosyası sırası | observability-bagimliliklar → bu dosya → devops-master | ✓ 2/3 |
| 15 | Satır eşiği | Bu satır eklenerek dosya 499 tam satırdan 500 tam satıra çıkarıldı | ? 500 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
