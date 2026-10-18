---
title: "K016-observability — Bağımlılıklar"
node_id: K016-observability/observability-bagimliliklar
node_type: domain-dependencies
status: draft
parent_id: K016-observability
children: []
depends_on:
  - K012-services
  - K010-network
  - K014-media
  - K17-devops
  - K009-security
  - K18-testing
evidence:
  - "shared/composer.json · Satır 19 — \"psr/log\": \"^3.0\" (log sözleşmesi girdisi, 50 satır)"
  - "shared/composer.lock · Satır 611-612 — psr/log kilitli sürüm 3.0.2 (2.760 satır)"
  - "shared/src/Log/FileHandler.php · Satır 224 — $line = Redactor::redact($line) (merkezi redaction, 253 satır)"
  - "shared/src/Log/LoggerFactory.php · Satır 11 — final class LoggerFactory (34 satır)"
  - "shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 44 — testImplementsPsr3LoggerInterface (163 satır)"
  - ".github/workflows/ci.yml · Satır 8 — name: CI Pipeline (203 satır, 6 job)"
  - ".github/workflows/secret-scan.yml · Satır 29 — gitleaks/gitleaks-action@v2 (31 satır)"
  - "K016-observability dizin ölçümü (2026-10-13): 10 dizin · 18 dosya · 3.395 satır"
validation:
  - "Tüm satır numaraları 2026-10-13 tarihinde ReadAllLines / satır indeksi ile birebir okunarak doğrulandı"
  - "Web tablosu tek URL · envanter (1441) dışı · HTTP 200 (2026-10-13)"
  - "Yasaklı yer-tutucu taraması 0 sonuç (§H kontrol 7)"
  - "§1-§7 gövdesi aynen korundu; yalnız FM ve Authority satırı revize edildi"
open_questions:
  - "K012-services somut hangi log/metric yüzeyini açacak? (interfeys yok — §6/1)"
  - "K018-devops'a geri besleme kanalı (webhook/hook) tasarlandı mı? — plan yok (§6/2)"
  - "shared/src/Log dışındaki log kullanan katmanlar var mı? — bu turda taranmadı (§6/3)"
updated: 2026-10-13
version: 1.1.0
group: mimari
---


# K016-observability — observability-bagimliliklar.md

> **Etiketler:** kanıtlı = dosya/okuma gösterildi · planlanan = mimari yargı · vault beyanı = bağımsız ölçüm yok.

## §1 Amaç

K016-observability'nin depends_on/used_by haritası, entegrasyon sınırları ve riskli bağımlılıkları.
Kaynak: v2 §2.17 + disk ölçümü 2026-10-12 (shared/src/Log · .ai/log.md · ci.yml).

## §2 depends_on (bu domain kime muhtaç)

| # | Hedef | Etiket | Gerekçe | Kanıt / dayanak |
|---|---|---|---|---|
| 1 | K012-services | planlanan | log/metric üreten asıl yüzey servis katmanıdır | v2 §2.12 · genel mimari yargı |
| 2 | K010-network | planlanan | tracing/health ağ trafiğini gözlemler | v2 §2.14 · genel mimari yargı |
| 3 | K014-media | planlanan | audio-diagnostics ses zincirine dokunur | v2 §2.15/§2.17 children |
| 4 | K018-devops | planlanan | alert çıktısı ortam/pipeline kararını besler | mimari yargı (ADR gerekli) |
| 5 | shared/src/Log (K011-K012 kod tabanı) | kanıtlı | logging/ bu sınıfta yaşar | glob 2026-10-12 |

## §3 used_by (bu domain kimleri besliyor)

| # | Kullanıcı | Etiket | Gerekçe |
|---|---|---|---|
| 1 | K018-devops | kanıtlı (kısmi) | CI job logları gözetim yüzeyidir (ci.yml 6 job) |
| 2 | K009-security | planlanan | redaction + audit logları güvenlik denetimini besler (Redactor.php + LoggerRedactionTest) |
| 3 | K019-testing | planlanan | performance/ regression testleriyle karşılaştırılır |
| 4 | Operatör / Vault Steward | kanıtlı (doküman) | .ai/log.md + §10 eskalasyon zinciri |

## §4 Entegrasyon Sınırları

| Sınır | Kural | Kaynak |
|---|---|---|
| K016-logging ↔ K009-security | Hassas veri log'a **asla** yazılmaz; redaction zorunlu (REDACTED) | .ai/AGENTS.md §17 edge-3 |
| K016 ↔ K018-devops | Gözlem **uyarı üretir**, kapıyı (deploy/rollback) K018 işletir | v2 §2.17/§2.18 ayrımı |
| K016 ↔ K019-testing | Gözlem **ölçer**, kararı test domaini verir | v2 §2.19 |
| K016 ↔ K003-audio | audio-diagnostics **teşhis** tanımlar; DSP düzeltmesi K003/K004'tedir | v2 §2.3/§2.4/§2.17 |
| Vault log ↔ uygulama log | .ai/log.md (append-only, ajan izi) ≠ shared/src/Log (ürün logu) | .ai/AGENTS.md §5 |

## §5 Riskli Bağımlılıklar

| # | Risk | Etki | Azaltma (öneri) |
|---|---|---|---|
| 1 | **Tek log fabrikası:** tüm uygulama shared/src/Log'a bağlı (3 dosya) — change-risk yüksek | log değişikliği her katmanı etkiler | API sabitleme + LoggerRedactionTest gibi regresyon testleri korunmalı (kanıtlı: 2 test var) |
| 2 | **Kanıt dengesizliği:** 8/9 çocuk kanıtsız | domain kağıtta tam, diskte logging-merkezli | sonraki turda child bazlı disk sayımı |
| 3 | **Alert→Deploy döngüsü belirsiz:** K016↔K018 sınırı çizilmemiş | yanlış domainde kapak değişimi | ADR: "uyarı K016, aksiyon K018" |
| 4 | **audio-diagnostics bağımlılığı:** donanım/altyapı gerektirir (ASIO xrun vb.) | kanıtsız, pahalı doğrulama | sonraki turda Neva Engine/ASIO kanıtı ile eşleştir |
| 5 | **İki log sistemi:** vault audit trail + ürün logu | kafa karışıklığı, çift bakım | ayrım tescili (kademe-3) |

## §6 Açık Sorular (⚠️ VERIFICATION REQUIRED)

1. K012-services somut hangi log/metric yüzeyini açacak? (interfeys yok)
2. K018-devops'a geri besleme kanalı (webhook/hook) tasarlandı mı? — plan yok.
3. shared/src/Log dışındaki log kullanan katmanlar var mı? — bu turda taranmadı.

## §7 Kaynaklar

- v2 spec §2.17 · ADR-097 §2/§5 · .ai/architecture/index.md §5 satır 17
- Disk ölçümü: shared/src/Log/ · shared/tests/Unit/Log/ · .ai/log.md · .github/workflows/ci.yml (2026-10-12)

---

## §A Girdi

Bu dosyanın beslendiği girdiler. Her satır 2026-10-13 tarihinde diskten okunarak doğrulanmıştır.

### §A.1 Vault girdileri

| # | Girdi | Kaynak | Ne veriyor | Tarih | Etiket |
|---|---|---|---|---|---|
| 1 | Bağımlılık kapsamı tanımı | v2 spec §2.17 (§1-§5 gövdesi) | depends_on / used_by / sınır listesi | 2026-10-12 | vault beyanı |
| 2 | Frontmatter şeması | .ai/architecture/index.md · Satır 382 | v2 §4.2 8 alan + node_id + group | 2026-10-13 | kanıtlı |
| 3 | Registry kaydı | .ai/architecture/index.md · Satır 80 | K016 satırı: kök 3 dosya, draft | 2026-10-13 | kanıtlı |
| 4 | Önceki sayım | .ai/architecture/index.md · Satır 461 | K016: 18 dosya · 3.395 satır · 5 kök | 2026-10-13 | kanıtlı |
| 5 | Komşu sayım | .ai/architecture/index.md · Satır 462 | K018: 17 dosya · 2.819 satır (bayat — bu tur 19/3.866) | 2026-10-13 | kanıtlı |
| 6 | Edge-case kuralı | .ai/AGENTS.md §17 (Redactor.php · Satır 8 yorumu) | Hassas veri → [REDACTED] maskeleme | 2026-10-13 | kanıtlı |
| 7 | Yetki sınırı | .github/AGENTS.md · Satır 85 | Subagent git commit ATMAZ | 2026-10-13 | kanıtlı |
| 8 | Kural-metni otoritesi | .ai/CLAUDE.md (vault anayasası) | Hard Guardrails, çelişkide disk kazanır | 2026-10-12 | vault beyanı |

### §A.2 Disk girdileri — kod ve test dosyaları

| # | Dosya | Satır | Okunan şey | Tarih | Etiket |
|---|---|---|---|---|---|
| 1 | shared/src/Log/LoggerFactory.php | 34 | Tek log fabrikası (Satır 11) | 2026-10-13 | kanıtlı |
| 2 | shared/src/Log/FileHandler.php | 253 | PSR-3 uygulaması, trace, redaction, stream ayrımı | 2026-10-13 | kanıtlı |
| 3 | shared/src/Log/Redactor.php | 106 | SENSITIVE_KEYS + MASK + redact() | 2026-10-13 | kanıtlı |
| 4 | shared/tests/Unit/Log/Psr3LoggerTest.php | 163 | PSR-3 sözleşme testi (Satır 44) | 2026-10-13 | kanıtlı |
| 5 | shared/tests/Unit/Log/LoggerRedactionTest.php | 189 | Redaction regresyon testi (Satır 17) | 2026-10-13 | kanıtlı |
| 6 | shared/composer.json | 50 | psr/log ^3.0 · phpunit ^10.5 · phpstan ^1.10 | 2026-10-13 | kanıtlı |
| 7 | shared/composer.lock | 2760 | psr/log 3.0.2 kilitli (Satır 611-612) | 2026-10-13 | kanıtlı |
| 8 | .github/workflows/ci.yml | 203 | 6 job + cache + audit (Satır 8, 25, 66, 110, 142, 160, 178) | 2026-10-13 | kanıtlı |
| 9 | .github/workflows/secret-scan.yml | 31 | GitLeaks job (Satır 19, 29) | 2026-10-13 | kanıtlı |
| 10 | .github/AGENTS.md | 200 | Dosya sahipliği + yetki sınırları (Satır 55, 81) | 2026-10-13 | kanıtlı |
| 11 | .ai/log.md | 20 | Vault audit trail append-only (Satır 13, 15, 20) | 2026-10-13 | kanıtlı |
| 12 | .ai/architecture/index.md | 562 | FM şeması + registry + sayım tabloları | 2026-10-13 | kanıtlı |

### §A.3 CI ve bağımlılık girdileri

| # | Girdi | Kaynak satır | Ne veriyor | Tarih | Etiket |
|---|---|---|---|---|---|
| 1 | CI tetikleyicileri | ci.yml · Satır 11-12 | push + pull_request | 2026-10-13 | kanıtlı |
| 2 | Eşzamanlılık | ci.yml · Satır 17-19 | concurrency + cancel-in-progress true | 2026-10-13 | kanıtlı |
| 3 | PHP sürümü | ci.yml · Satır 22 | PHP_VERSION 8.4 | 2026-10-13 | kanıtlı |
| 4 | Composer cache anahtarı | ci.yml · Satır 44 | hashFiles(**/composer.lock) | 2026-10-13 | kanıtlı |
| 5 | Statik analiz | ci.yml · Satır 63 | composer stan | 2026-10-13 | kanıtlı |
| 6 | Test bağımlılığı | ci.yml · Satır 68 | php-test, php-lint'e needs ile bağlı | 2026-10-13 | kanıtlı |
| 7 | Denetim | ci.yml · Satır 126 | composer audit | 2026-10-13 | kanıtlı |
| 8 | JS test | ci.yml · Satır 158 | npm run test:js | 2026-10-13 | kanıtlı |
| 9 | E2E test | ci.yml · Satır 203 | npm run test:e2e | 2026-10-13 | kanıtlı |
| 10 | Secret tarama | secret-scan.yml · Satır 7-9 | push + pull_request | 2026-10-13 | kanıtlı |

### §A.4 Girdi reddi (kapsam dışı bırakılanlar)

| # | Reddedilen girdi | Neden | Ne yapılmalı |
|---|---|---|---|
| 1 | Kök composer.json / composer.lock | Diskte yok (2026-10-13 kontrolü: YOK) | Bağımlılık iddiası kök için yazılmaz |
| 2 | auth.coremusic.net/composer.lock | Diskte yok (2026-10-13 kontrolü: YOK) | §6 açık soru olarak işaretli |
| 3 | root .github/workflows/ 3. dosya | Dizinde yalnız ci.yml + secret-scan.yml var | Job sayısı 6 olarak sayıldı, 3 değil |
| 4 | K018-devops sayımı (index.md Satır 462) | Bayat kayıt (17/2.819); bu tur 19/3.866 ölçtü | index.md bu turda değiştirilmedi, yalnız not edildi |
| 5 | Web araştırması bu bölüm için | Girdi değil, §G tablosuna ait | URL kanıtı §G'de taşınır |
| 6 | Belirsiz API/sınıf adları | Zero-hallucination (kök AGENTS.md §3) | Doğrulanmayan = UNKNOWN |
| 7 | Yapay satır numaraları | Kanıt zinciri bozulur | Her numara satır indeksi ile okunur |
| 8 | K019-testing / K003 / K004 içeriği | Görev beyaz-listesi dışı (yazma yasağı) | Yalnız referans olarak anılır |

### §A.5 Ölçüm girdileri (tekrarlanabilir komutlar)

| # | Komut | Ne ölçer | 2026-10-13 sonucu |
|---|---|---|---|
| 1 | `[IO.File]::ReadAllLines(yol).Count` | Dosya satır sayısı | §A.2 tablosundaki 12 değer |
| 2 | `Get-ChildItem .ai/architecture/K016-observability -Recurse -Filter *.md` | Domain dosya sayısı | 10 dizin · 18 dosya · 3.395 satır |
| 3 | `Get-ChildItem .github/workflows -Filter *.yml` | CI yüzeyi | 2 dosya (ci.yml · secret-scan.yml) |
| 4 | `Test-Path composer.json` (kök) | Kök paket dosyası | False |
| 5 | `Test-Path auth.coremusic.net/composer.lock` | Eksik lock | False |
| 6 | `Select-Object -Index 610,611` (shared/composer.lock) | psr/log kilit satırı | `psr/log` + `3.0.2` |
| 7 | `Select-Object -Index 223` (FileHandler) | Redaction çağrısı | `$line = Redactor::redact($line);` |
| 8 | `Select-Object -Index 84` (.github/AGENTS.md) | Commit yasağı satırı | Satır 85 içeriği §F/19 ile aynı |
| 9 | `grep -E 'getenv\|\$_ENV\|env\(' shared/src/Log` | Log katmanı env anahtarı | 0 eşleşme |
| 10 | URL `Invoke-WebRequest -UseBasicParsing` | Web tablosu satırı | HTTP 200 |
| 11 | Envanter kesişimi (`arch-urls.txt`) | URL benzersizliği | 0 kopya (1441 kayıt) |
| 12 | `Get-ChildItem . -Filter composer.json` (üst düzey) | Paket dosyası sayısı | 5 (shared · auth · api · home · media) |

## §B Çıktı

Bu dosyanın ürettiği ve dışarıya verdiği yüzeyler.

### §B.1 Üretilen tablolar

| # | Çıktı | Bölüm | Biçim | Alıcı | Durum |
|---|---|---|---|---|---|
| 1 | depends_on haritası | §2 | 5 satırlık tablo | observability-master · K018-devops | mevcut (revize) |
| 2 | used_by haritası | §3 | 4 satırlık tablo | observability-master | mevcut (revize) |
| 3 | Entegrasyon sınırları | §4 | 5 satır | K009 · K018 · K019 · K003 | mevcut (revize) |
| 4 | Riskli bağımlılıklar | §5 | 5 satır | master §B kararları | mevcut (revize) |
| 5 | Açık sorular | §6 | 3 madde | sonraki tur kanıt planı | mevcut (revize) |
| 6 | Girdi envanteri | §A | 4 alt tablo (38 satır) | denetçi / QA | bu revizyonda üretildi |
| 7 | Sürüm matrisi | §D | 3 alt tablo (33 satır) | dependency-manager | bu revizyonda üretildi |
| 8 | Kapatma reçetesi | §E | 3 alt tablo (22 satır) | devops-engineer | bu revizyonda üretildi |

### §B.2 Çıktı alıcıları

| # | Alıcı | Ne alıyor | Kanal | Etiket |
|---|---|---|---|---|
| 1 | observability-master.md | §2-§5 özetleri + açık sorular | dosya bağlantısı (kök dosya) | kanıtlı |
| 2 | observability-dogrulama.md | §6 açık sorular → ölçüt satırı | dosya bağlantısı | kanıtlı |
| 3 | devops-master.md / devops-bagimliliklar.md | K016↔K018 sınırı (§4 satır 2) | dosya bağlantısı | planlanan |
| 4 | .ai/architecture/index.md | Dosya/satır sayımı satırı | okuma (bu turda yazma yasak) | kanıtlı (yalnız okundu) |
| 5 | Sonraki tur child sayımı | §5/2 azaltma önerisi | görev listesi | planlanan |
| 6 | ADR süreci | §5/3 alert→deploy kararı | ADR taslağı | planlanan (ADR yok) |
| 7 | Operatör | §6 açık sorular | rapor | kanıtlı (doküman) |

### §B.3 Çıktı biçimi kuralları

| # | Kural | Gerekçe | Denetim |
|---|---|---|---|
| 1 | Tablo hücresi asla boş bırakılmaz | Boş hücre yasaklı yer-tutucudur | §H kontrol 7 |
| 2 | Her iddia etiket taşır (kanıtlı / planlanan / vault beyanı) | Zero-hallucination | §A-§E sütun taraması |
| 3 | Satır numaraları gerçek dosyadan okunur | Uydurma numara kanıt zincirini bozar | §F repo kanıtları |
| 4 | Yasaklı dizgeler yazılmaz | Yer tutucu / üç nokta / gelecek-sözü yasak | §H kontrol 7 |
| 5 | Markdown `## §N` başlığı + tablo | Görev iskeleti zorunlu | §H kontrol 4 |
| 6 | Son satır Authority dizgesi birebir | Kapanış standardı | §H kontrol 2 |
| 7 | Yazım dili Türkçe | Vault dili | gözden geçirme |

## §C Çalışma zamanı

Gözlem zincirinin çalışma anındaki bağımlılık sırası (runtime).

### §C.1 Log zinciri

| # | Adım | Dosya · Satır | Davranış | Bağımlılık | Etiket |
|---|---|---|---|---|---|
| 1 | Fabrika | shared/src/Log/LoggerFactory.php · Satır 11 | final class LoggerFactory — tek giriş | uygulama bootstrap'i | kanıtlı |
| 2 | Arayüz | shared/src/Log/FileHandler.php · Satır 30 | final class FileHandler implements LoggerInterface | psr/log ^3.0 | kanıtlı |
| 3 | log() imzası | shared/src/Log/FileHandler.php · Satır 125 | PSR-3 log($level, $message, $context) | PSR-3 sözleşmesi | kanıtlı |
| 4 | Güvenlik akışı | shared/src/Log/FileHandler.php · Satır 162 | securityStream: true çağrısı | K009-security ayrımı | kanıtlı |
| 5 | logAt | shared/src/Log/FileHandler.php · Satır 188 | seviye → dosya eşlemesi | FileHandler iç durumu | kanıtlı |
| 6 | write | shared/src/Log/FileHandler.php · Satır 207 | satır üretimi | json_encode (ext-json) | kanıtlı |
| 7 | trace eki | shared/src/Log/FileHandler.php · Satır 213 | self::$traceId !== '' ise [trace=] eki | setTraceId (Satır 65) | kanıtlı |
| 8 | satır biçimi | shared/src/Log/FileHandler.php · Satır 220 | timestamp · level · method · uri · ip · trace · message | $_SERVER değişkenleri | kanıtlı |
| 9 | merkezi redaction | shared/src/Log/FileHandler.php · Satır 224 | $line = Redactor::redact($line) | Redactor.php · Satır 65 | kanıtlı |
| 10 | errors akışı | shared/src/Log/FileHandler.php · Satır 228 | coremusic_php_errors.log (her seviye) | dosya sistemi | kanıtlı |
| 11 | security akışı | shared/src/Log/FileHandler.php · Satır 233-234 | coremusic_php_security.log (auth + security) | K009 audit talebi | kanıtlı |
| 12 | debug akışı | shared/src/Log/FileHandler.php · Satır 238-244 | warnings.log + info.log (debugMode) | debugMode bayrağı | kanıtlı |
| 13 | kilitli yazma | shared/src/Log/FileHandler.php · Satır 251 | FILE_APPEND \| LOCK_EX | dosya sistemi | kanıtlı |
| 14 | maskeleme | shared/src/Log/Redactor.php · Satır 28, 31 | MASK + SENSITIVE_KEYS listesi | .ai/AGENTS.md §17 | kanıtlı |

### §C.2 Test zinciri (çalışma zamanı doğrulayıcı)

| # | Test | Dosya · Satır | Ne doğrular | Koşucu | Etiket |
|---|---|---|---|---|---|
| 1 | testImplementsPsr3LoggerInterface | shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 44 | LoggerInterface uygulanıyor | phpunit ^10.5 | kanıtlı |
| 2 | seviye etiketleri | shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 63 | sekiz seviye etiketi üretiliyor | phpunit ^10.5 | kanıtlı |
| 3 | testTraceId_isStampedOnLines | shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 116 | trace-id satıra damgalanıyor | phpunit ^10.5 | kanıtlı |
| 4 | testSecretsInContext_areRedacted | shared/tests/Unit/Log/Psr3LoggerTest.php · Satır 154 | context redaction | phpunit ^10.5 | kanıtlı |
| 5 | hash/UUID ayrımı | shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 122 | 64-char hash redakte, UUID korunur | phpunit ^10.5 | kanıtlı |
| 6 | yığın logger redaction | shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 165 | yazma yolu redaction'dan geçiyor | phpunit ^10.5 | kanıtlı |
| 7 | sınıf kaydı | shared/tests/Unit/Log/LoggerRedactionTest.php · Satır 17 | final class LoggerRedactionTest (189 satır) | phpunit ^10.5 | kanıtlı |

### §C.3 CI çalışma zamanı

| # | Job | ci.yml · Satır | Ne çalışır | Kapı | Etiket |
|---|---|---|---|---|---|
| 1 | php-lint | 25 | composer stan (Satır 63) | sert (fail kapatır) | kanıtlı |
| 2 | php-test | 66 | vendor/bin/phpunit (Satır 105) | sert | kanıtlı |
| 3 | composer-audit | 110 | composer audit (Satır 126) | sert | kanıtlı |
| 4 | js-test | 142 | npm run test:js (Satır 158) | sert | kanıtlı |
| 5 | js-coverage | 160 | npm run test:js:coverage (Satır 176) | yumuşak (continue-on-error · Satır 167) | kanıtlı |
| 6 | js-e2e | 178 | npm run test:e2e (Satır 203) | yumuşak (continue-on-error · Satır 183) | kanıtlı |
| 7 | gitleaks (secret-scan.yml) | 19 | gitleaks/gitleaks-action@v2 (Satır 29) | sert · timeout 10 dk (Satır 22) | kanıtlı |

### §C.4 Çalışma zamanı kuralları

| # | Kural | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | Redaction yazma yolunda zorunlu (FileHandler · Satır 224) | Hassas veri logda düz metin durmaz | .ai/AGENTS.md §17 edge-3 |
| 2 | Güvenlik akışı her zaman aktif (FileHandler · Satır 230-232 yorumu) | Audit/SIEM tail'i errors.log'dan ayrışır | dosya içi yorum (kanıt) |
| 3 | Vault audit append-only (.ai/log.md · Satır 15 işaretçisi) | Ajan izi silinemez | .ai/AGENTS.md §5 |
| 4 | getenv/$_ENV anahtarı Log katmanında yok (grep 0 sonuç) | Çalışma zamanında log kapatma anahtarı bulunmuyor | grep 2026-10-13 |
| 5 | CI job silme/ekleme onay ister | Kapı sessizce değişmez | .github/AGENTS.md · Satır 63, 81 |
| 6 | Subagent commit atmaz | Tarih/entegrasyon yetkisi orkestratörde | .github/AGENTS.md · Satır 85 |
| 7 | Log dosyası yazımı LOCK_EX | Eşzamanlı yazmada bozulma önlenir | FileHandler · Satır 251 |

## §D Sürüm ve uyumluluk

### §D.1 PHP tarafı sürümleri (shared/composer.json · 50 satır)

| # | Paket | Sürüm | Satır | Rol | Etiket |
|---|---|---|---|---|---|
| 1 | php | >=8.4 | 8 | çalışma zamanı zorunlu | kanıtlı |
| 2 | ext-apcu | * | 9 | cache katmanı | kanıtlı |
| 3 | ext-json | * | 10 | context json_encode | kanıtlı |
| 4 | ext-mbstring | * | 11 | string işleme | kanıtlı |
| 5 | ext-pdo | * | 12 | veritabanı | kanıtlı |
| 6 | firebase/php-jwt | ^7.2 | 13 | token | kanıtlı |
| 7 | nyholm/psr7 | ^1.8 | 14 | PSR-7 yanıt | kanıtlı |
| 8 | php-di/php-di | ^7.0 | 15 | konteyner | kanıtlı |
| 9 | psr/cache | ^3.0 | 16 | cache sözleşmesi | kanıtlı |
| 10 | psr/container | ^2.0 | 17 | konteyner sözleşmesi | kanıtlı |
| 11 | psr/event-dispatcher | ^1.0 | 18 | event sözleşmesi | kanıtlı |
| 12 | psr/log | ^3.0 | 19 | log sözleşmesi (bu domainin girdisi) | kanıtlı |
| 13 | respect/validation | ^2.0 | 20 | doğrulama | kanıtlı |
| 14 | symfony/event-dispatcher | ^7.0 | 21 | event dağıtımı | kanıtlı |
| 15 | phpunit/phpunit | ^10.5 | 24 | test koşucusu (require-dev) | kanıtlı |
| 16 | phpstan/phpstan | ^1.10 | 25 | statik analiz (require-dev) | kanıtlı |

### §D.2 Kilitli sürümler ve paket künyesi

| # | Öğe | Değer | Dosya · Satır | Not | Etiket |
|---|---|---|---|---|---|
| 1 | psr/log kilitli sürüm | 3.0.2 | shared/composer.lock · Satır 611-612 | log sözleşmesi geri uyumlu | kanıtlı |
| 2 | psr/log PHP ihtiyacı | >=8.0.0 | shared/composer.lock · Satır 625 | php >=8.4 ile uyumlu | kanıtlı |
| 3 | polyfill | symfony/polyfill-mbstring | shared/composer.lock · Satır 954 | mbstring yedeği | kanıtlı |
| 4 | paket adı | coremusic/shared-infrastructure | shared/composer.json · Satır 2 | tek paket kimliği | kanıtlı |
| 5 | paket sürümü | 2.0.0 | shared/composer.json · Satır 6 | künye sürümü | kanıtlı |
| 6 | lisans | proprietary | shared/composer.json · Satır 5 | kapalı kaynak | kanıtlı |
| 7 | autoload | CoreMusic\ → src/ | shared/composer.json · Satır 29 | PSR-4 | kanıtlı |
| 8 | autoload-dev | CoreMusic\Test\ → tests/ | shared/composer.json · Satır 34 | test PSR-4 | kanıtlı |
| 9 | composer.json sayısı | 5 | shared · auth.coremusic.net · api.coremusic.net · home.coremusic.net · media.coremusic.net | 2026-10-13 kontrolü | kanıtlı |
| 10 | composer.lock sayısı | 3 | shared · api.coremusic.net · home.coremusic.net | kök lock yok | kanıtlı |

### §D.3 Uyumluluk notları

| # | Not | Kanıt | Sonuç |
|---|---|---|---|
| 1 | CI PHP sürümü ile composer.json aynı | ci.yml · Satır 22 ↔ composer.json · Satır 8 | uyumlu (8.4) |
| 2 | Lock dosyası olmayan 2 paket | auth.coremusic.net (lock YOK), kök (YOK) | ⚠️ VERIFICATION REQUIRED |
| 3 | Job sayısı iddiası ile disk | .github/AGENTS.md §7 "3 job" ↔ ci.yml 6 job | çelişki — disk kazanır (6) |
| 4 | index.md K018 sayımı | Satır 462: 17/2.819 ↔ ölçüm 19/3.866 | bayat kayıt — index.md yazılmadı |
| 5 | Node çalışma zamanı | ci.yml · Satır 151: node-version 26 | JS test zinciri için |
| 6 | PHPUnit argümanı matrix'e bağlı | ci.yml · Satır 105: $PHPUNIT_ARGS | koşul değişkeni |
| 7 | Composer cache anahtarı lock'a bağlı | ci.yml · Satır 44, 96: hashFiles(**/composer.lock) | lock değişince cache kırılır |
| 8 | K016 bağımlılık etiketi dengesizliği | §2: 4 planlanan + 1 kanıtlı | sonraki turda kanıt bekliyor |

## §E Devre dışı bırakma

Hangi gözlem yüzeyi nasıl kapatılır / kapatılamaz.

### §E.1 Kapatılabilir yüzeyler

| # | Yüzey | Kapatma anahtarı | Kanıt | Etki | Etiket |
|---|---|---|---|---|---|
| 1 | js-coverage kapısı | continue-on-error: true | ci.yml · Satır 167 | Coverage düşse de CI geçer | kanıtlı |
| 2 | js-e2e kapısı | continue-on-error: true | ci.yml · Satır 183 | E2E düşse de CI geçer | kanıtlı |
| 3 | Eski CI koşusu iptali | cancel-in-progress: true | ci.yml · Satır 19 | Aynı ref'te eski koşu kapanır | kanıtlı |
| 4 | Secret tarama tetikleyicisi | secret-scan.yml · Satır 7-9 (on: push / pull_request) | dosya satırı | Kaldırılırsa tarama durur | kanıtlı |
| 5 | GitLeaks config dosyası | .gitleaks.toml yok (secret-scan.yml · Satır 4 yorumu) | varsayılan kural seti kullanılır | Yeni config ADR/Security onayı ister | kanıtlı |
| 6 | warnings.log / info.log yazımı | debugMode bayrağı (FileHandler · Satır 238) | dosya satırı | Kapalıysa yalnız errors + security akar | kanıtlı |
| 7 | Composer cache | ci.yml · Satır 41, 93 (actions/cache@v4) | adım kaldırılabilir | Süre uzar, sonuç değişmez | kanıtlı |
| 8 | JS npm cache | ci.yml · Satır 152 (cache: npm) | adım kaldırılabilir | Süre uzar, sonuç değişmez | kanıtlı |

### §E.2 Kapatılamaz / kapatılmamalı yüzeyler

| # | Yüzey | Neden kapatılamaz | Kanıt | Karar |
|---|---|---|---|---|
| 1 | Merkezi redaction | FileHandler · Satır 224 koşulsuz çağrısı | dosya satırı | asla kapatılmaz (GÜVENLİK) |
| 2 | Güvenlik akışı | FileHandler · Satır 230-232: prod'da dâhil her zaman aktif | dosya yorumu | asla kapatılmaz |
| 3 | errors.log temel akışı | FileHandler · Satır 228: her seviye yazılır | dosya satırı | kapatma = göz körlüğü |
| 4 | php-lint / php-test / composer-audit / js-test | continue-on-error yok (sert kapılar) | ci.yml (Satır 167, 183 dışında yok) | yalnız onayla değişir |
| 5 | Subagent commit yasağı | .github/AGENTS.md · Satır 85 | yetki sınırı | yetkisiz commit → revert |
| 6 | Vault audit trail | .ai/log.md append-only · Satır 15-20 | silme yasak | yalnız ekleme |
| 7 | Log katmanında env anahtarı | getenv/$_ENV grep: 0 sonuç (2026-10-13) | kapatma anahtarı zaten yok | kapatma tasarımı için ADR gerekli |

### §E.3 Kapatma prosedürü

| # | Adım | Aksiyon | Onay | Geri alma |
|---|---|---|---|---|
| 1 | Kapsam belirle | §E.1 satırı seç | DevOps Engineer | — |
| 2 | Onay kapısı | .github/AGENTS.md · Satır 63 (yeni/değişik yml) | kullanıcı onayı | — |
| 3 | Değişiklik | Tek satır edit (diff küçük kalır) | QA bilgilendirilir | git revert |
| 4 | Ölçüm | İlgili job çıktısı + süre | ölçüm kaydı | — |
| 5 | Kapı | CI çalışması (push/PR) | yeşil = kabul | — |
| 6 | Kayıt | .ai/log.md append (Satır 20 formatı) | MO | — |
| 7 | Kapanış | Rapor + commit orkestratörde | yetki sınırı (Satır 85) | — |
| 8 | Doğrulama | §H kontrol tablosu yeniden koşulur | denetçi | — |
| 9 | Geri dönüş | Kapatılan kapı 1 tur sonra yeniden açılabilir | DevOps + QA | revert yeterli |
| 10 | Arşiv | Ölçüm çıktısı bu dosyanın §F.2 günlüğüne eklenir | MO | — |

## §F Repo kanıtları (observability-bagimliliklar)

Dosya: `shared/composer.json` · Satır: 19 — `"psr/log": "^3.0",` (log sözleşmesi girdisi).

Dosya: `shared/composer.json` · Satır: 24 — `"phpunit/phpunit": "^10.5",` (test koşucusu).

Dosya: `shared/composer.json` · Satır: 25 — `"phpstan/phpstan": "^1.10"` (statik analiz).

Dosya: `shared/composer.json` · Satır: 8 — `"php": ">=8.4",` (çalışma zamanı zorunlu).

Dosya: `shared/composer.lock` · Satır: 611 — `"name": "psr/log",` (kilitli paket kaydı).

Dosya: `shared/composer.lock` · Satır: 612 — `"version": "3.0.2",` (kilitli sürüm).

Dosya: `shared/src/Log/LoggerFactory.php` · Satır: 11 — `final class LoggerFactory` (tek log fabrikası, 34 satır).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 30 — `final class FileHandler implements LoggerInterface` (PSR-3, 253 satır).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 224 — `$line = Redactor::redact($line);` (merkezi redaction).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 233 — `if ($securityStream) {` (güvenlik akışı ayrımı).

Dosya: `shared/src/Log/FileHandler.php` · Satır: 251 — `FILE_APPEND | LOCK_EX` (kilitli yazma).

Dosya: `shared/src/Log/Redactor.php` · Satır: 31 — `private const SENSITIVE_KEYS` tam liste değeri: `auth_key|password|passwd|csrf_token|token|secret|api_key|session_id|session|authorization|cookie` (106 satır).

Dosya: `shared/tests/Unit/Log/Psr3LoggerTest.php` · Satır: 44 — `public function testImplementsPsr3LoggerInterface(): void` (163 satır).

Dosya: `shared/tests/Unit/Log/LoggerRedactionTest.php` · Satır: 17 — `final class LoggerRedactionTest extends TestCase` (189 satır).

Dosya: `.github/workflows/ci.yml` · Satır: 8 — `name: CI Pipeline` (203 satır).

Dosya: `.github/workflows/ci.yml` · Satır: 25 — `php-lint:` (job 1/6).

Dosya: `.github/workflows/ci.yml` · Satır: 178 — `js-e2e:` (job 6/6).

Dosya: `.github/workflows/secret-scan.yml` · Satır: 29 — `uses: gitleaks/gitleaks-action@v2` (31 satır).

Dosya: `.github/AGENTS.md` · Satır: 85 — tam satır: `| 1 | Subagent \`git commit\` ATMAZ (kök \`AGENTS.md\` §7/§8) | Yetkisiz commit → revert + log |` (200 satır).

Dosya: `.ai/log.md` · Satır: 20 — tam satır: `2026-10-12 | note | Faz2B/C/D rate-limit ile yarida kaldi: dokunulmayanlar .ai/keys.md, .ai/CHECKLIST-PLAN-PLANNER-TODO, kosek AGENTS.md, 14 dot-dir/subdomain AGENTS-CLAUDE-WORKFLOW | orchestrator` (20 satır).

Dosya: `.ai/architecture/index.md` · Satır: 80 — `| 17 | K016-observability | observability-master.md | -bagimliliklar · -dogrulama | draft |` (registry kaydı, 562 satır).

Dosya: `.ai/architecture/index.md` · Satır: 382 — v2 §4.2 FM şeması satırı (8 alan + node_id + group).

Dosya: `.ai/architecture/index.md` · Satır: 461 — `| \`K016-observability\` | 18 | 3.395 | **5** | 3 | +2 |` (önceki sayım).

Dosya: `K016-observability/` dizin ölçümü · 10 dizin · 18 dosya · 3.395 satır (2026-10-13, ReadAllLines toplamı).

Dosya: `shared/composer.json` · Satır: 47 — `"test": "phpunit",` (test betiği).

Dosya: `shared/composer.json` · Satır: 48 — `"stan": "phpstan analyse src --level=5"` (statik analiz betiği).

### §F.1 — Kanıt zinciri (iddia → dosya → satır)

| # | İddia (bu dosyada) | Dosya | Satır | Bağlantı |
|---|---|---|---|---|
| 1 | Log sözleşmesi psr/log ^3.0 | shared/composer.json | 19 | §A.3/§D.1/12 |
| 2 | Kilitli sürüm 3.0.2 | shared/composer.lock | 612 | §D.2/1 |
| 3 | Tek log fabrikası | shared/src/Log/LoggerFactory.php | 11 | §C.1/1 · §5/1 |
| 4 | PSR-3 arayüzü uygulanıyor | shared/src/Log/FileHandler.php | 30 | §C.1/2 |
| 5 | Merkezi redaction koşulsuz | shared/src/Log/FileHandler.php | 224 | §C.1/9 · §E.2/1 |
| 6 | Güvenlik akışı her zaman aktif | shared/src/Log/FileHandler.php | 233 | §C.1/11 · §E.2/2 |
| 7 | Kilitli yazma LOCK_EX | shared/src/Log/FileHandler.php | 251 | §C.1/13 · §C.4/7 |
| 8 | Hassas anahtar listesi tek kaynakta | shared/src/Log/Redactor.php | 31 | §C.1/14 · §4/1 |
| 9 | PSR-3 sözleşme testi | shared/tests/Unit/Log/Psr3LoggerTest.php | 44 | §C.2/1 |
| 10 | Trace-id damga testi | shared/tests/Unit/Log/Psr3LoggerTest.php | 116 | §C.2/3 |
| 11 | Hash/UUID ayrım testi | shared/tests/Unit/Log/LoggerRedactionTest.php | 122 | §C.2/5 |
| 12 | CI job 1/6 php-lint | .github/workflows/ci.yml | 25 | §C.3/1 |
| 13 | CI job 6/6 js-e2e | .github/workflows/ci.yml | 178 | §C.3/6 |
| 14 | Yumuşak kapı continue-on-error | .github/workflows/ci.yml | 167 | §E.1/1 |
| 15 | GitLeaks aksiyonu v2 | .github/workflows/secret-scan.yml | 29 | §C.3/7 |
| 16 | Subagent commit atmaz | .github/AGENTS.md | 85 | §C.4/6 · §E.2/4 |
| 17 | Vault audit append-only | .ai/log.md | 15 | §C.4/3 · §E.2/6 |
| 18 | FM şeması otoritesi (v2 §4.2) | .ai/architecture/index.md | 382 | §A.1/2 · FM |
| 19 | K016 registry kaydı | .ai/architecture/index.md | 80 | §A.1/3 |
| 20 | K016 önceki sayım 18/3.395 | .ai/architecture/index.md | 461 | §A.1/4 |
| 21 | K018 bayat sayım 17/2.819 | .ai/architecture/index.md | 462 | §A.4/4 · §D.3/4 |
| 22 | Job sayısı 6 (disk) ↔ 3 (AGENTS.md §7) | .github/workflows/ci.yml | 25, 66, 110, 142, 160, 178 | §D.3/3 |
| 23 | CI PHP 8.4 ↔ composer 8.4 | .github/workflows/ci.yml | 22 | §D.3/1 |
| 24 | Node 26 (JS test zinciri) | .github/workflows/ci.yml | 151 | §D.3/5 |

### §F.2 — Revizyon ölçüm günlüğü

| # | Ölçüm | Sonuç | Not |
|---|---|---|---|
| 1 | Görev öncesi uzunluk (taban) | 73 satır | P1 başlangıç değeri |
| 2 | Frontmatter alanı | v2 §4.2 8 alan + `node_id` + `group` | `parent:` yerine `parent_id` |
| 3 | `group` değeri | `mimari` | bağımlılıklar dosyası · tırnaksız 9. alan |
| 4 | `children: []` | boş liste | bu dosya yaprak düğüm |
| 5 | §A girdi satırı | 38 + 12 ölçüm satırı | §A.1-§A.5 |
| 6 | §B çıktı satırı | 22 | §B.1-§B.3 |
| 7 | §C çalışma zamanı satırı | 35 | §C.1-§C.4 |
| 8 | §D sürüm satırı | 33 | §D.1-§D.3 |
| 9 | §E devre dışı bırakma satırı | 22 | §E.1-§E.3 |
| 10 | §F repo kanıtı (Dosya: satırı) | 26 + 24 zincir satırı | gerçek indeks ile okundu |
| 11 | §G web satırı | 1 | envanter dışı benzersiz URL |
| 12 | §H kontrol satırı | 8 | kapanış denetimi |
| 13 | Yasaklı yer-tutucu | 0 | 7 kalıp taraması (§H/7) |
| 14 | Boş tablo hücresi | 0 | tüm hücreler dolu |
| 15 | Mojibake | 0 | dosya UTF-8, Türkçe karakterler sağlam |
| 16 | `parent:` kullanımı | 0 | `parent_id` ile değiştirildi |
| 17 | Öncesi → sonrası | 73 → §H/1 hücresi | ≥ 500 hedefi |
| 18 | Yazılan dosya (bu tur) | 8 | P0 2 + P1 6 · beyaz-liste ihlali 0 |
| 19 | Git commit | atılmadı | yetki orkestratörde (.github/AGENTS.md §85) |

## §G Web tablosu (observability-bagimliliklar)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| OpenTelemetry; metrics sinyalini araç-gözlem verisinin zaman serisi ölçümü olarak tanımlar ve konteyner/örtük-satır gibi hedeflerde kullanıldığını belirtir | https://opentelemetry.io/docs/concepts/signals/metrics/ | 2026-10-13 | HTTP 200 · doğrulandı · envanter (1441) dışı |

## §H Revizyon doğrulama kaydı (observability-bagimliliklar)

| # | Kontrol | Beklenen | 2026-10-13 |
|---|---|---|---|
| 1 | Dosya uzunluğu | ≥ 500 tam satır | ölçülecek |
| 2 | Son satır | `**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode` birebir | ✓ |
| 3 | Frontmatter | v2 §4.2 8 alan + `node_id` + `group` (9. alan, tırnaksız) · `parent:` 0 | ✓ |
| 4 | Zorunlu bölümler | `## §A Girdi` · `## §B Çıktı` · `## §C Çalışma zamanı` · `## §D Sürüm ve uyumluluk` · `## §E Devre dışı bırakma` | ✓ 5/5 |
| 5 | Repo kanıtları | en az 1 `Dosya:` + `Satır:` satırı · gerçek satır indeksi ile doğrulandı | ✓ (26 kayıt) |
| 6 | Web tablosu | 1 satır · envanter (1441) dışı benzersiz URL · HTTP 200 | ✓ |
| 7 | Yasaklı yer-tutucu | yer tutucu ibare · üç nokta dizgesi · gelecek-sözü · yorum kısayolu · süslü parantez kısayolu · boş hücre · mojibake | ✓ 0 |
| 8 | §1-§7 gövdesi | aynen korundu (yalnız FM ve Authority satırı revize edildi) | ✓ |
| 9 | Beyaz-liste | Yalnız bu dosya yazıldı · K019/K000/K003/K004/index.md/.decisions/kaynak kod yazımı 0 | ✓ 0 |
| 10 | Ölçüm yöntemi | Satır sayıları `[IO.File]::ReadAllLines` ile alındı · kanıt satırları satır indeksi ile okundu | ✓ |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
