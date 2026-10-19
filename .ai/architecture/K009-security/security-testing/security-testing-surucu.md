---
node_id: K009-security/security-testing/security-testing-surucu
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing/security-testing-anayasa
  - K009-security/security-test-stratejisi
  - K17-devops
evidence:
  - ".github/workflows/ci.yml:24-203 — 6 job (php-lint, php-test, composer-audit, js-test, js-coverage, js-e2e)"
  - ".github/workflows/secret-scan.yml:18-31 — gitleaks job"
  - "shared/composer.json:46-49 — `test` ve `stan` scriptleri"
validation:
  - "2026-10-15 — gate zinciri §3 · lokal sürüş §4 · CI sürüş §5 · duruş akışı §6"
open_questions:
  - "CI job geçiş kanıtı bu oturumda yok → ⚠️ VERIFICATION REQUIRED"
  - "js-coverage ve js-e2e `continue-on-error: true` → kapı YUMUŞAK ⚠️"
group: mimari
---

# K009 · security-testing · Süreç Sürücüsü

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Rol: test akışı ve gate zinciri — lokal → CI → red (mimari).

## §1 AMAÇ VE KAPSAM

| Alan | Değer |
|---|---|
| node_id | K009-security/security-testing/security-testing-surucu |
| node_type | module |
| Grup | mimari |
| Kapsam | sürüş akışı · gate zinciri · hangi test hangi katmanda · başarısızlıkta durma |
| Kapsam dışı | red kriterlerinin tanımı (anayasa §3) · gerçek kod alıntıları (`ornekler`) |
| Kural | "geçti" iddiası yalnız çalıştırma çıktısıyla yazılır; okuma ile değil |

## §2 SÜRÜŞ SÖZLÜĞÜ

| Türkçe | English | Açıklama |
|---|---|---|
| Gate zinciri | Gate chain | Kapıların sırayla geçilmesi |
| Sürüş | Drive | Bir testin/kapının adım adım çalıştırılması |
| Durma | Halt | Kapı kırmızıysa zincirin kesilmesi |
| Soft gate | Soft gate | Görsel kırmızı ama bloklamayan job |
| Katman | Layer | Lokal / CI / regresyon ayrımı |
| Ekolayzer | Fallback | Kapı olmadığında davranışı belirsiz dal |

## §3 GATE ZİNCİRİ (lokal → CI → red)

| Sıra | Kapı | Nerede | Çıktı | Başarısızlıkta |
|---|---|---|---|---|
| G-1 | Kod yazımı öncesi keşif | lokal | hedef dosya okundu | DUR |
| G-2 | `composer test` (PHPUnit) | lokal | PASS/FAIL | **DUR** — commit yok |
| G-3 | `composer stan` (PHPStan lvl 5) | lokal | 0 hata | **DUR** |
| G-4 | Secret taraması (yerel gitleaks/beğeni) | lokal | 0 bulgu | **DUR** |
| G-5 | `php-lint` job | CI | yeşil | CI RED → merge kapıda |
| G-6 | `php-test` job (matrix) | CI | yeşil | CI RED |
| G-7 | `composer-audit` job | CI | 0 CVE | CI RED |
| G-8 | `Secret Scan` job | CI | 0 bulgu | CI RED |
| G-9 | JS unit (`js-test`) | CI | yeşil | CI RED |
| G-10 | JS coverage (`js-coverage`) | CI | soft | ⚠️ `continue-on-error: true` — bloklamaz |
| G-11 | JS e2e (`js-e2e`) | CI | soft | ⚠️ `continue-on-error: true` — bloklamaz |
| G-12 | Vault doğrulama (dosya hedefi/kanıt) | vault | ölçüm | düzelt |
| G-13 | Commit | orkestratör | — | subagent ATMAZ |

| Zincir Kuralı | |
|---|---|
| Sıra değişmez | G-2/G-3 atlanarak CI'a gidilmez (lokal kapı öncelikli) |
| CI geçti mi | bu oturumda doğrulanmadı → `⚠️ VERIFICATION REQUIRED` |
| Soft kapılar | G-10/G-11 kırmızı görünür ama merge'i durdurmaz (ci.yml:167, 183) |

## §4 LOKAL SÜRÜŞ (hangi komut hangi kapı)

| Kapı | Komut | Dizin | Kanıt |
|---|---|---|---|
| Unit test | `vendor/bin/phpunit` (`composer test`) | `shared/` | `shared/composer.json:47` |
| Statik analiz | `composer stan` → `phpstan analyse src --level=5` | `shared/` | `shared/composer.json:48` |
| Auth unit test | `vendor/bin/phpunit --testsuite Unit` | `auth.coremusic.net/` | `ci.yml:79-80` |
| JS unit | `npm run test:js` | kök | `ci.yml:158` |
| JS coverage | `npm run test:js:coverage` | kök | `ci.yml:176` |
| JS e2e | `npm run test:e2e` | kök | `ci.yml:203` |

| Lokal Sürüş Notu | |
|---|---|
| `phpstan.neon` YOK | composer scripti esaslıdır (`ci.yml:60-61` yorum satırı) |
| Level | disk kazanır: **5** (plan 8 değil) |
| Bu oturumda | shell erişimi YOK → komutlar ÇALIŞTIRILMADI ⚠️ |

## §5 CI SÜRÜŞÜ (`.github/workflows/ci.yml`)

| Job | Satır | Ne sürer | Gate tipi |
|---|---|---|---|
| `php-lint` | 25-64 | composer install ×3 + PHPStan | sert |
| `php-test` | 66-108 | PHPUnit matrix (shared · auth Unit) | sert |
| `composer-audit` | 110-139 | `composer audit` ×3 proje | sert |
| `js-test` | 142-158 | Vitest unit | sert |
| `js-coverage` | 160-176 | Vitest coverage | **soft** (`continue-on-error: true`, satır 167) |
| `js-e2e` | 178-203 | Playwright | **soft** (`continue-on-error: true`, satır 183) |

| Tetikleyici / Ortak | |
|---|---|
| `on: push` + `pull_request` | `ci.yml:10-12` |
| `permissions: contents: read` | `ci.yml:14-15` — en az yetki |
| `cancel-in-progress: true` | `ci.yml:17-19` — eski koşu iptal |
| `php-test` → `needs: php-lint` | `ci.yml:68` — lint geçmeden test yok |
| `js-coverage` → `needs: js-test` | `ci.yml:162` |
| A7 kararı (yumuşak coverage) | `ci.yml:165-167` — %80 eşiği şimdilik erişilemez |

## §6 BAŞARISIZLIKTA DURMA AKIŞI

| # | Tetikleyici | Duruş | Sonraki adımı kim açar |
|---|---|---|---|
| D-1 | PHPUnit kırmızı | lokal kapı durur; commit yok | QA Engineer |
| D-2 | PHPStan hata | lokal kapı durur | QA Engineer |
| D-3 | `php-lint` CI'da kırmızı | `php-test` hiç çalışmaz (`needs`) | DevOps + QA |
| D-4 | `php-test` kırmızı | merge RED | QA Engineer |
| D-5 | `composer-audit` CVE | merge RED | Security Engineer |
| D-6 | `Secret Scan` bulgu | merge RED + anahtar iptali | Security Engineer |
| D-7 | js-coverage kırmızı | **durmez** — log'a yazılır | QA Engineer (takip) |
| D-8 | js-e2e kırmızı | **durmez** — log'a yazılır | QA Engineer (takip) |
| D-9 | Kapı atlandı (elle) | Vault kaydı + gerekçe | Vault Steward |
| D-10 | 3 başarısız tur | dur + şüpheli varsayımı yaz | üst karar |

| Durma Kuralı | |
|---|---|
| Fail-open yasak | Güvenlik job'ı `continue-on-error` ile **kapatılamaz** |
| Bilinen boşluk | SAST/DAST ayrık araç kapıları yok → §7 |

## §7 KATMAN × TEST EŞLEMESİ (hangi test hangi katmanda)

| Test türü | Katman | Örnek (repo) | Dosya · Satır |
|---|---|---|---|
| Unit — CSRF reddi | lokal + CI | `CsrfMiddlewareTest` | `shared/tests/Middleware/CsrfMiddlewareTest.php:85-97` |
| Unit — rate limit 429 | lokal + CI | `RateLimiterMiddlewareTest` | `shared/tests/Middleware/RateLimiterMiddlewareTest.php:52-74` |
| Unit — pipeline sırası | lokal + CI | `MiddlewarePipelineTest` | `shared/tests/Middleware/MiddlewarePipelineTest.php:176-221` |
| Unit — CSP header | lokal + CI | `MiddlewarePipelineTest` | `shared/tests/Middleware/MiddlewarePipelineTest.php:254-264` |
| Unit — CORS fail-closed | lokal + CI | `ApiPipelineSecurityTest` | `shared/tests/Api/ApiPipelineSecurityTest.php:52-63` |
| Unit — origin fail-closed | lokal + CI | `OriginCheckMiddlewareTest` | `shared/tests/Middleware/OriginCheckMiddlewareTest.php:67-74` |
| Unit — log redaksiyonu | lokal + CI | `LoggerRedactionTest` | `shared/tests/Unit/Log/LoggerRedactionTest.php:70-88` |
| Unit — open redirect | lokal + CI | `ReturnUrlPolicyTest` | `shared/tests/Unit/Security/ReturnUrlPolicyTest.php:86` |
| Statik analiz | lokal + CI | PHPStan level 5 | `shared/composer.json:48` |
| Bağımlılık CVE | CI | `composer audit` | `ci.yml:123-127` |
| Secret tarama | CI | gitleaks | `secret-scan.yml:28-31` |
| Integration — middleware zinciri | **boşluk** | — | ⚠️ `security-test-stratejisi.md` §2 |
| DAST (WSTG dinamik) | **boşluk** | — | ⚠️ `index.md` §2 |
| E2E güvenlik (Playwright) | soft kapı | `js-e2e` | `ci.yml:178-203` |

## §8 SECRET-SCAN SÜRÜŞÜ

| Adım | Eylem | Satır |
|---|---|---|
| 1 | Tetik: `push` + `pull_request` | `secret-scan.yml:7-9` |
| 2 | `permissions: contents: read` | `secret-scan.yml:11-12` |
| 3 | checkout `fetch-depth: 0` (tüm geçmiş) | `secret-scan.yml:24-26` |
| 4 | `gitleaks/gitleaks-action@v2` | `secret-scan.yml:28-29` |
| 5 | `GITHUB_TOKEN` env | `secret-scan.yml:30-31` |
| 6 | Bulgu → job kırmızı → merge durur | anayasa R-03 |

| Sürüş Notu | |
|---|---|
| `.gitleaks.toml` | diskte YOK → varsayılan config (`secret-scan.yml:4`) |
| `.gitleaks.toml` eklemek | Security onayı ister (`.github/AGENTS.md` §3.1) |
| Çalışma durumu | job dosyası mevcut; **sonuç doğrulanmadı** ⚠️ |

## §9 BYPASS / RED AKIŞI SÜRÜŞÜ

| Sıra | Sürüş | Beklenen | Test |
|---|---|---|---|
| 1 | credential eksik → `loadBypassConfig` `[]` döner | bypass yok | `BypassAuthMiddlewareFailClosedTest` |
| 2 | whitelist dışı rota | 401/403 | T-18 (P0) ⚠️ plan |
| 3 | CSRF'siz POST | 403 + `csrf_invalid` | `CsrfMiddlewareTest:85-97` |
| 4 | bilinmeyen origin OPTIONS | header yok (fail-closed) | `ApiPipelineSecurityTest:52-63` |
| 5 | boş allowlist + prod | 403 | `OriginCheckMiddlewareTest:67-74` |
| 6 | limit aşıldı | 429 + `Retry-After` | `RateLimiterMiddlewareTest:52-74` |

## §10 SÜRÜŞ SENARYOLARI

| # | Senaryo | Sürüş adımları | Sonuç |
|---|---|---|---|
| SD-1 | Temiz commit | G-2 → G-3 → push → G-5..G-9 | yeşil (iddia — kanıt yok ⚠️) |
| SD-2 | Test kırmızı | G-2 durur | commit yok |
| SD-3 | Secret sızdı | G-4/G-8 durur | anahtar iptali |
| SD-4 | CVE çıktı | G-7 durur | paket güncellemesi |
| SD-5 | Lint kırmızı | G-6 hiç başlamaz (`needs`) | lint düzelt |
| SD-6 | Coverage düşük | G-10 kırmızı ama geçer | takip kaydı |
| SD-7 | E2E flaky | G-11 kırmızı ama geçer | takip kaydı |
| SD-8 | Bypass testi silindi | G-2 yeşil kalır (!) → **insan kapısı** R-07 | review RED |

| SD-8 Notu | |
|---|---|
| Otomatik kapı test SİLİNMEZ | yalnız anayasa R-07 (birlikte değişiklik) review'da yakalar |

## §11 HATA TÜRÜ × SÜRÜŞ

| Hata türü | Kapı | Duruş | Sahip |
|---|---|---|---|
| Assertion hatası | G-2/G-6 | sert | QA |
| Tip/statik hata | G-3/G-5 | sert | QA |
| CVE | G-7 | sert | Security |
| Sızan anahtar | G-4/G-8 | sert | Security |
| Coverage eşiği | G-10 | yumuşak | QA |
| Flaky e2e | G-11 | yumuşak | QA |
| Dosya hedefi/satır uyuşmazlığı | G-12 | sert (vault) | Vault Steward |

## §12 KAPI DURUM MATRİSİ

| Durum | Anlamı | Davranış |
|---|---|---|
| GREEN | tüm sert kapılar geçti | devam |
| RED | sert kapıda hata | DUR + düzelt |
| SOFT-RED | yumuşak kapıda hata | devam + kaydet |
| SKIPPED | kapı hiç çalışmadı | `⚠️` — yeşil sayılmaz |
| UNKNOWN | çalıştırma kanıtı yok | `⚠️ VERIFICATION REQUIRED` |

## §13 ORTAM MATRİSİ

| Ortam | Kapılar | Not |
|---|---|---|
| Lokal (geliştirici) | G-2, G-3, G-4 | komutlar `shared/composer.json` scriptleri |
| CI (GitHub Actions) | G-5..G-11 | 2 workflow dosyası (`ci.yml`, `secret-scan.yml`) |
| staging/DAST | — | YOK ⚠️ |
| Production | deploy kapısı | `K17-devops` — bu çocukta değil |

## §14 ÖNCELİKLİ MÜDAHALE

| Öncelik | Durum | Müdahale |
|---|---|---|
| CRITICAL | secret sızıntısı | anahtar iptali + tarama + Vault kaydı |
| CRITICAL | bypass açık (prod) | bayrak kapat + test ekle |
| HIGH | güvenlik testi kırmızı | düzelt; merge yok |
| HIGH | audit CVE | paket güncelle |
| MEDIUM | soft gate kırmızı | takip kaydı aç |
| LOW | coverage eşiği | iş planı (A7 kararı, `ci.yml:165-167`) |

## §15 DOĞRULAMA ADIMLARI (sürüş kanıtı)

| # | Adım | Yöntem | Kriter |
|---|---|---|---|
| V-1 | Gate zinciri yazıldı mı? | §3 | 13 kapı |
| V-2 | Job listesi diskle eşleşiyor mu? | `ci.yml` okundu | 6 job |
| V-3 | Secret job diskte mi? | `secret-scan.yml` okundu | gitleaks |
| V-4 | Komutlar composer scriptinden mi? | `composer.json:46-49` | `test` + `stan` |
| V-5 | `needs` bağımlılıkları | `ci.yml:68`, `ci.yml:162` | lint → test |
| V-6 | Soft kapılar işaretli mi | `ci.yml:167`, `:183` | `continue-on-error` |
| V-7 | En az yetki | `ci.yml:14-15` | `contents: read` |
| V-8 | Çalıştırma kanıtı | — | **YOK** → ⚠️ |

## §16 SÜRÜŞ KONTROL LİSTESİ

| # | Kontrol | Geçerse | Kalırsa |
|---|---|---|---|
| C-1 | Lokal test yeşil | devam | DUR |
| C-2 | Statik analiz temiz | devam | DUR |
| C-3 | Secret tarama temiz | devam | DUR |
| C-4 | Sert CI job'ları yeşil | devam | merge RED |
| C-5 | Yumuşak job kaydedildi | devam | takip kaydı |
| C-6 | Kanıt satırları Dosya·Satır | devam | `⚠️` |
| C-7 | Test çalıştırma kanıtı | devam | `PASS` YAZMA |
| C-8 | Commit orkestratörde | devam | yasak ihlali |

## §17 BİLİNMEYENLER

| # | Soru | Etiket |
|---|---|---|
| U-1 | CI job'ları en son yeşil mi? | ⚠️ VERIFICATION REQUIRED |
| U-2 | Coverage ölçümü (PHP) var mı? | UNKNOWN |
| U-3 | DAST ortamı/kapısı | UNKNOWN |
| U-4 | Deploy öncesi güvenlik kapısı (`K018-devops`) | ⚠️ VERIFICATION REQUIRED |
| U-5 | Test paralelliği/timeout değerleri | UNKNOWN |

## §18 GATE SAHİP × YAPTIRIM MATRİSİ

> Her kapı kırmızıya döndüğünde **kim ne yapar** — sürüşün insan/rol ayağı.
> Rol kaynakları: `.ai/AGENTS.md` §4 (agent tablosu) · §9 (handover).

| Gate | Kırmızı olduğunda | Sahip rol | Eskalasyon | Yaptırım |
|---|---|---|---|---|
| G-01..G-04 lokal test/stan/lint | DUR — commit yok | QA Engineer | L1 → Tech Lead (`.ai/AGENTS.md` §10.1) | düzelt + yeniden çalıştır |
| G-05 `composer test` (CI) | merge RED | QA Engineer | DevOps'a bildir | test yeşilmeden merge yok |
| G-06 `composer stan` (CI) | merge RED | QA Engineer | — | PHPStan hatası düzeltilir |
| G-07 `php-lint` job | G-05 sıradaki işe geçmez (`ci.yml·68` needs) | DevOps Engineer | — | lint temizlenir |
| G-08 kod/secret tarama job'ları | bulgu listesi + RED | Security Engineer | CRITICAL ise MO (§9.3) | bulgu kapanır |
| G-09..G-11 gitleaks | push/PR RED | Security Engineer | sızıntı → anahtar iptali (anayasa S-06) | revoke + temiz commit |
| G-12 bağımlılık (`composer audit`) | paket RED | DevOps Engineer | — | sürüm güncelle (anayasa R-08) |
| G-13 DAST | **kapı tanımsız** | — | — | ⚠️ UNKNOWN (index §2 satır 49) |
| Soft kapılar (`ci.yml·167`,`:183`) | mergei engellemez | DevOps Engineer | takip kaydı | bilinçli istisna (§19) |

| Matris Kuralı | |
|---|---|
| Sahipsiz kapı olmaz | G-13'ün sahibi yok → kapı da yok demektir, "geçti" DEĞİL |
| Eskalasyon zinciri | L1 Domain Lead → L2 Tech Lead → L3 Arch Lead → İnsan (`.ai/AGENTS.md` §10) |
| Manuel atlatma | Yalnız Vault Steward onayı + `log.md` kaydı (anayasa §3 Gate Kuralı) |

## §19 BİLİNÇLİ YUMUŞAK KAPILAR VE BYPASS RİSKİ

| # | Soft kapı | Satır | Neden soft | Risk değerlendirmesi |
|---|---|---|---|---|
| S-1 | `js-coverage` | `ci.yml·167` (`continue-on-error: true`) | coverage eşiği iş planında (A7 kararı) | Düşük — güvenlik job'ını etkilemez |
| S-2 | `js-e2e` | `ci.yml·183` (`continue-on-error: true`) | E2E kararlılığı olgunlaşma aşamasında (A6 kararı) | Orta — JS regresyonu yakalanmaz; güvenlik kapıları etkilenmez |
| S-3 | Sert kapılar (test, stan, lint, audit, secret) | `ci.yml·62-64,104-108,123-127` · `secret-scan.yml·24-31` | **soft DEĞİL** | ✅ anayasa R-09 ile uyumlu |

| Bypass Risk Kontrolü | |
|---|---|
| Yasak | Güvenlik job'una `continue-on-error: true` koymak = anayasa Y-05 |
| Denetim | `ci.yml` değişikliğinde `continue-on-error` satırları sayılır — artışı RED (R-09) |
| Mevcut sayım | 2 soft kapı, ikisi de JS tarafı (`ci.yml·167`, `ci.yml·183`) — güvenlik job'larında 0 |
| Bilinmeyen | Job içi `if:` koşullu atlatma olup olmadığı okunmadı → ⚠️ VERIFICATION REQUIRED |

### §19.1 Kapı × Zaman Çizelgesi

| Kapı grubu | Anı | Sıra |
|---|---|---|
| G-01..G-04 (lokal test/stan/lint) | commit öncesi | ilk |
| G-05..G-06 (CI test + stan) | PR açılışında | lint'ten sonra (`ci.yml·68`) |
| G-07 (php-lint job) | PR'da, G-05'ten önce | `needs` bağımlılığı |
| G-08..G-11 (tarama + gitleaks) | her push/PR | paralel |
| G-12 (`composer audit`) | her PR (`ci.yml·123-127`) | bağımlılık kurulumuyla |
| G-13 (DAST) | yayına yakın ortam | **tanımsız** ⚠️ |
| Soft kapılar | PR (bilinçli istisna) | mergei engellemez |

| Çizelge Kuralı | |
|---|---|
| Sıra bozulamaz | lint → test (G-07 → G-05) `needs` ile kilitli |
| Zaman atlaması | Kapı zamanı atlanırsa kapı da atlanmıştır = kapı yok |

## §20 BAĞLANTILAR

| Bağ | Yön | Not |
|---|---|---|
| [[security-testing/security-testing-anayasa]] | öncesi | kapıların RED kriterleri burada |
| [[security-testing/security-testing-ornekler]] | sonrası | kapıların kod kanıtları |
| [[security-testing/security-testing-dogrulama]] | sonrası | AD-xx kontrol satırları |
| [[security-test-stratejisi]] | öncesi | T-01..T-20 vakaları |
| [[security-master]] | üst | K009 künyesi |
| [[security-kararlar]] | kaynak | ADR-010/013/020 kapı kararları |
| [[secrets/secrets-kapsam]] | kardeş | secret-scan kapsamı |
| `K17-devops` | sahip | workflow dosyaları (`ci.yml`, `secret-scan.yml`) |
| `K18-testing` | dışı | genel QA test kapsamı |

## §21 REVİZYON

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-15 | İlk yazım — 13 kapı · 6 CI job · 8 doğrulama adımı | Vault Steward (subagent) |
| 2026-10-15 | `git commit` ATILMADI (yasak) | — |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
