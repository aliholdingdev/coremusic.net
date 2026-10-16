---
node_id: K013-integraion/integration-testing/ittest-dogrulama
node_type: technology-deepdive
title: "Entegrasyon Test Doğrulaması — Sözleşme Doğrulama, Mock Tutarlılığı, Eski Kayıt, Secret Tarama, CI Kapısı"
type: technology-deepdive
status: draft
date: 2026-10-16
parent_id: K000–K5000 Architecture Registry
children: []
depends_on:
  - K013-integraion/integration-testing/ittest-sozlesme
  - K013-integraion/integration-testing/ittest-anayasa
  - K013-integraion/api-contracts/openapi
evidence:
  - "https://docs.pact.io/ (2026-10-16)"
  - "https://martinfowler.com/bliki/ContractTest.html (2026-10-16)"
  - "https://playwright.dev/docs/mock (2026-10-16)"
  - "https://docs.phpunit.de/en/10.5/ (2026-10-16)"
  - ".github/workflows/secret-scan.yml — 32 satır, gitleaks/gitleaks-action@v2 (2026-10-16 disk)"
  - ".github/workflows/ci.yml — satır 77, 80, 167, 183 (2026-10-16 disk)"
validation:
  - "Her iddia §Kaynak Tablosu'nda URL ile eşleştirilmiştir (2026-10-16 fetch)"
  - "Rapor biçimi TÜREV (öneri) olarak etiketlidir; kaynak metinde birebir rapor şeması yoktur"
  - "Uydurma test komutu/süit adı yok; komutlar diskteki dosyalardan okunmuştur"
open_questions:
  - "Doğrulama hangi aşamada bağlayıcı (PR mı merge mi) → ⚠️ VERIFICATION REQUIRED"
  - "Eski kayıt (stale stub) otomatik mi raporlanır → UNKNOWN"
  - "GitLeaks config (.gitleaks.toml) neden diskte YOK → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# Entegrasyon Test Doğrulaması — Sözleşme · Mock · Eski Kayıt · Secret · CI

> **Bu dosya ne değildir:** Testin *kendisi* veya ortam kurulumu değildir.
> Bu dosya, entegrasyon testi ve stub'ların **nasıl yakalanıp geçeceği**
> kurallarını ve CI kapısının rapor biçimini tanımlar.
>
> **Durum etiketi:** Doğrulama kuralları TASLAK · CI yüzeyi kısmen
> IMPLEMENTED (`.github/workflows/` diskte mevcut; geçiş durumu doğrulanmadı).
>
> **Okuma sırası:** `ittest-anayasa.md` (ne) → `ittest-sozlesme.md` (neyle,
> kimin izniyle) → **bu dosya** (nasıl yakalanır).

---

## §0 ELI15 — Bu doğrulama bir cümlede

Test yeşil olsa bile **taklit gerçekten sadık mı, kayıt bayatlamış mı,
koda secret sızmış mı** sorularını ayrıca ve otomatik olarak yoklayan
katmandır; bu üçü aynı kapıdan geçmeden "entegrasyon testi geçti" denemez.
(Kaynak: §Kaynak Tablosu [S1], [S2], [S3] · repo [R1])

---

## §1 Sözleşme Doğrulama (Contract Validation)

### §1.1 İki yön (Pact dokümanındaki ayrım)

| Yön | Ne sınar | Neyi yakalar | Kanıt |
|---|---|---|---|
| **Consumer contract test** | Tüketicinin ihtiyaç duyduğu her şey sağlayıcıda var mı | Sağlayıcının beklentiyi karşılamaması | [S1] |
| **Provider contract test** | Sağlayıcının **gerçek davranışı** dokümante edilmiş sözleşmeye (ör. OpenAPI belgesi) uyuyor mu | Sağlayıcı kodu ile dokümanın senkron kayması | [S1] |

> **⚠️ Önemli sınır (kaynaklı):** Pact dokümanı, provider contract
> testinin **tek başına** "tüketici doğru mu çağırıyor" ve "tüketici
> beklentilerini karşılayabiliyor mu" garantisi **vermediğini** açıkça
> yazar — yalnızca sağlayıcı-kod/belge senkronunu sağlar. (Kaynak: [S1])

### §1.2 Doğrulama Maddeleri

| # | Madde | Ne kontrol eder | Kanıt |
|---|---|---|---|
| V1 | Belge var mı ve geçerli mi | Sözleşme kaynağı (OpenAPI/şema) | [S1] (OAS = statik belge örneği) |
| V2 | Sağlayıcı davranışı belgeye uyuyor mu | Kod ↔ belge senkronu | [S1] |
| V3 | Tüketici çağrıları sağlananla aynı sonucu veriyor mu | Consumer ↔ provider uyumu | [S1], [S2] |
| V4 | Sözleşme testi **periyodik** çalışıyor mu | Dış servis değişim ritmi | [S2] |
| V5 | Başarısızlık → tutarlılık görevi + sahiplerle iletişim | Bozulma yönetimi | [S2] |

### §1.3 "Contract by example" ≠ statik şema

| | Statik şema (ör. OpenAPI) | Contract test (örnek tabanlı) |
|---|---|---|
| Ne tarif eder | Olası **tüm** durumları belge | Her biri **somut** istek/yanıt çifti olan test durumları |
| Nasıl uygulanır | Belge geçerliliği (doğrulama) | Test çalıştırılır (davranış) |
| Kapsam | Her şey yazılabilir | **Tüketici tarafından kullanılan** parçalar test edilir | 

(Kaynak: [S1] — "contract by example" ve "schema vs contract" ayrımı.)

---

### §1.4 Doğrulama Aşamaları — Ne Zaman Hangisi

| Aşama | Ne doğrulanır | Sıklık | Kanıt |
|---|---|---|---|
| Belge geçerliliği (V1) | Belge kendisi (sözdizimi/şema) | Her PR (öneri) | [S1] türev |
| Sağlayıcı davranışı (V2) | Kod ↔ belge | Her PR (öneri) | [S1] |
| Tüketici-üretici uyumu (V3) | Consumer ↔ provider örnekleri | Periyodik (M4/M5) | [S1], [S2] |
| Canlı temas (§sozlesme §4) | Gerçek servis davranışı | Onaylı, sınırlı | [S2], [S5] |

> **Ayrım özü (kaynaklı):** Statik doğrulama **her değişiklikte** çalışabilir
> çünkü ucuzdur; örnek-tabanlı consumer/provider testi **periyodik**tir
> çünkü dış servisin ritmine bağlıdır. İkisi aynı kapıda birleştirilirse
> ya yavaşlar ya da yanlış yere sertleşir → önerilen ayrım §5.2'dedir
> (TÜREV).

---

## §2 Mock (Taklit) Tutarlılığı

### §2.1 Kaynaklı kural — Taklit koşmaya devam, ayrıca periyodik sözleşme testi

| Kural | Metin özeti | Kanıt |
|---|---|---|
| M1 | Kendi testlerin **taklit ile** koşmaya devam eder | [S2] |
| M2 | Ayrı bir set **contract test** periyodik çalışır: taklitten yapılan çağrılar gerçek servisten döndürenle aynı mı? | [S2] |
| M3 | Uyuşmazlık → taklit (ve muhtemelen kod) güncellenir | [S2] |
| M4 | Ritim: dış servisin değişim ritmi; **günde bir kez çoğu zaman yeterli** | [S2] |
| M5 | Normal deploy pipeline'ına zorunlu dahil edilmez | [S2] |

### §2.2 Tutarlılık Denetim Listesi

| # | Kontrol | "Yeşil" şartı |
|---|---|---|
| C1 | Taklit, sözleşmedeki **tüm** tüketici yollarını kapsıyor mu? | Eksik yol varsa kırmızı |
| C2 | Hata durumları (4xx/5xx) taklitte tanımlı mı? | Yoksa kırmızı |
| C3 | Son periyodik sözleşme testi ne zaman? | Süre dolmuşsa uyarı |
| C4 | Taklit tek kaynaklı mı (kod içinde tekrar eden kopyalar)? | Kopya varsa kırmızı → ⚠️ kural özü öneri |
| C5 | Gerçek servis sürümü ile taklit sürümü kayıtlı mı? | Bilinmiyorsa `UNKNOWN` |

### §2.3 CoreMusic'te mock tutarlılığının bugünkü kanıtı

| Bulgu | Kanıt | Durum |
|---|---|---|
| Tek mock kaynağı: `createMockFetch` + senaryolar | `assets.coremusic.net/tests/mocks/player-api.mock.js` (184 satır) — `success/notfound/unauthorized/servererror/network` | ✅ var |
| Bu mock için periyodik sözleşme testi | Repo'da **tespit edilmedi** (2026-10-16 tarama) | ⚠️ YOK |
| Mock ile "gerçek servis" karşılaştırması | Tespit edilmedi | ⚠️ YOK |

---

## §3 Eski Kayıt Tespiti (Stale Record Detection)

### §3.1 "Bayat" nedir? (kaynaklı)

Fowler: contract test'i dış servis **değişim ritmine** göre çalıştır;
başarısızlık "tutarlılığı onaran görev" açar; contract test'ler genelde
belirli bir tarihte **snapshot** alan stub'larla çalıştığı için **biçimin
aynı kalıp verinin değiştiği** durumları da ayrıca anlamak gerekir
(contract test biçim ile ilgilenir, gerçek veri ile değil). (Kaynak: [S2])

### §3.2 Bayat Kaynaklar Envanteri (bu depo için)

| # | Kayıt türü | Bayatlaşma belirtisi | Tespit yolu | Kanıt |
|---|---|---|---|---|
| 1 | HAR ile kayıtlı yanıtlar | Gerçek servis yanıtı değişti, HAR eskisi | `update: true` ile yeniden kayıt (Playwright) | [S3] |
| 2 | Kod içinde sabitlenmiş stub gövdeleri | Sözleşme değişimi | Periyodik contract test (M2) | [S2] |
| 3 | Belgede (OpenAPI) eski sürüm | Kod yeni, belge eski (ya tersi) | Provider contract test | [S1] |
| 4 | Vault'taki doğrulama kayıtları (tarihli URL/ölçüm) | Kaynak 404 / içerik değişmiş | Yeniden fetch + tarih güncelleme | Bu serinin `web-arastirma` kuralı |
| 5 | Suite notları (ör. "Integration suite kaldırıldı") | Dizin geri geldi ama not güncellenmedi | Disk ölçümü ile karşılaştırma | [R1] |

### §3.3 Otomatiklik Derecesi (durum)

| # | Kayıt | Otomatik mi | Durum |
|---|---|---|---|
| 1 | HAR replay | Playwright `update` bayrağı ile **yarı otomatik** | [S3] — ⚠️ CoreMusic'te Playwright config kullanılabilir mi → UNKNOWN |
| 2 | Stub gövdeleri | Sözleşme testiyle **periyodik** | [S2] — repo'da YOK (§2.3) |
| 3 | Belge-kod | Provider contract test ile | [S1] — repo'da YOK → ⚠️ |
| 4 | Vault URL kayıtları | Manuel yeniden fetch | Bu seride elle yapıldı (2026-10-16) |
| 5 | Suite notları | Manuel disk ölçümü | 2026-10-16 ölçümü |
| 6 | CI yumuşak kapı yorumları | CI metni ile karşılaştırma | 2026-10-16 ölçümü (`ci.yml` satır 167, 183) |

---

## §4 Secret (Sızıntı) Tarama

### §4.1 Repo Kanıtı — Mevcut Kapı

| Kanıt | Satır | İçerik |
|---|---|---|
| Workflow var | `.github/workflows/secret-scan.yml` — **32 satır** | `name: Secret Scan`, `on: push` + `pull_request` |
| Araç | satır 28-29 | `uses: gitleaks/gitleaks-action@v2` |
| Checkout derinliği | satır 24-26 | `fetch-depth: 0` (yorum: "no commits found" hatası için) |
| Config durumu | satır 4 (yorum) | **"diskte .gitleaks.toml YOK → varsayılan gitleaks config"** |
| Plan referansı | satır 3 | `.ai/architecture/k13-cicd/security-scanning.md` (K13.4.3) + README §2.2 |

### §4.2 Bu Seriye Özel Secret Kuralları (TÜREV — güvenlik kuralıyla birlikte uygulanır)

| # | Kural | Neden |
|---|---|---|
| T1 | Stub/mock içine gerçek secret/anahtar **gömülmez** | `secret-scan` + kod incelemesi sızıntısı |
| T2 | Ortam sözleşmesi dosyasında canlı uç listesi olsa bile **anahtar yazılmaz** | `.env` / env substitution kuralı (vault) |
| T3 | Doğrulama raporuna gerçek yanıt gövdesi değil **özet** (hash/başlık) yazılır | Raporda veri sızıntısı |
| T4 | HAR kayıtları commit edilecekse önce sızıntı taramasından geçer | Header/çerez sızıntısı olasılığı → ⚠️ kesin hüküm güvenlik kuralında |
| T5 | `.gitleaks.toml` yokluğu **boşluk** olarak raporlanır, "kapı var" sanılmaz | Repo satır 4 kanıtı |

> **⚠️ DOĞRULUK:** T1-T5 bu serinin **türev** kurallarıdır; bağlayıcı
> otorite vault'taki güvenlik kuralıdır. `.gitleaks.toml` neden yok →
> `⚠️ VERIFICATION REQUIRED`.

### §4.3 Tarama Senaryoları — Ne, Nerede, Ne Zaman

| # | Senaryo | Nerede taranır | Ne zaman | Bulgu ne olur |
|---|---|---|---|---|
| 1 | Git geçmişi (commit içerikleri) | GitLeaks (`fetch-depth: 0`) | push + PR | Job sonucu (§4.1) |
| 2 | Stub/mock dosyaları | Secret taraması + kod incelemesi | PR | Kapı sonucu |
| 3 | Ortam sözleşmesi dosyası (gelecek) | Aynı tarama | PR | Kapı sonucu |
| 4 | HAR kayıtları (gelecek) | Commit öncesi | Manuel/onaylı | ⚠️ kural T4 |
| 5 | Doğrulama raporu içeriği | Rapor üretimi sırasında maskeleme | Her çalıştırma | `[REDACTED]`/özet → vault kuralı |

> **Senaryo 5 kanıtı:** Maskeleme kuralı `.ai/AGENTS.md` §17 (Sensitive data
> log'da → `[REDACTED]`) türetilmiştir; bu dosyada yeniden otorite kurulmaz.

---

## §5 CI Kapısı ve Rapor Biçimi

### §5.1 Mevcut CI Yüzeyi (`.github/workflows/ci.yml` — 203 satır, gözlem)

| Yüzey | Satır | Davranış | Kapı sertliği |
|---|---|---|---|
| `php-lint` | (job) | Sözdizimi/statik | Sert (varsayım) → ⚠️ |
| `php-test` | 67-108 | `vendor/bin/phpunit` + matrix; auth `--testsuite Unit` (satır 80) | Sert |
| `composer-audit` | (job) | Bağımlılık denetimi | Sert (varsayım) → ⚠️ |
| `js-test` (Vitest) | 151-157 | unit | Sert (varsayım) → ⚠️ |
| `js-coverage` | 167-175 | `continue-on-error: true` | **Yumuşak** |
| `js-e2e` (Playwright) | 179-202 | `continue-on-error: true` (A6) | **Yumuşak** |
| `secret-scan` | ayrı dosya 32 satır | GitLeaks push+PR | Sert (varsayım) → ⚠️ |

> **Doğruluk:** "Sert" etiketleri `continue-on-error` bulunmayan job'lar
> için **türetilmiştir**; GitHub'da job'un gerçekten zorunlu olup olmadığı
> branch koruma kurallarına bağlıdır → `⚠️ VERIFICATION REQUIRED`.

### §5.2 Önerilen Entegrasyon-Test Kapısı (TÜREV — uygulanmadı)

```text
PR
 ├─ [1] Sözleşme doğrulama (V1-V2)        → sert
 ├─ [2] Mock tutarlılık (C1-C3)           → sert
 ├─ [3] Eski kayıt denetimi (§3.2)        → uyarı (rapor)
 ├─ [4] Secret tarama (GitLeaks)          → sert
 └─ [5] Canlı temas var mı?               → varsa §ittest-sozlesme §4 onayı
```

### §5.3 Rapor Biçimi (TÜREV — önerilen özet şeması)

| Alan | Tip | Açıklama |
|---|---|---|
| `run_id` | string | CI çalıştırma kimliği |
| `contract_validation` | `{checked: n, failed: n}` | §1 maddeleri |
| `mock_consistency` | `{checked: n, failed: n, last_periodic: date}` | §2 maddeleri |
| `stale_records` | `{count: n, items: [...]}` | §3 tespitleri |
| `secret_scan` | `{tool: "gitleaks", findings: n}` | §4 |
| `live_contact` | `{used: bool, approved: bool}` | Canlı temas + onay |
| `verdict` | `pass \| fail \| warn` | Kapı sonucu |
| `date` | ISO tarih | Rapor tarihi |

> **UYARI:** Şema **öneridir**; kaynak metinlerde birebir rapor şeması
> **yoktur**. Mevcut CI'da bu şemada bir rapor dosyası tespit **edilmedi**
> → `⚠️ VERIFICATION REQUIRED`.

### §5.4 Mevcut Eşdeğer Raporlar

| Rapor | Nerede | Durum |
|---|---|---|
| PHPUnit çıktısı | CI job logu | ✅ (koşum logu) |
| Vitest coverage | `js-coverage` job (yumuşak) | ✅ ama `continue-on-error` |
| GitLeaks sonucu | `secret-scan` job | ✅ |
| Entegrasyon-test raporu | — | ❌ Yok (dizin yok) |

### §5.5 Kapı Sertliği Matrisi (durum → önerilen sertlik)

| Bulgu tipi | Bugünkü durum (gözlem) | Önerilen sertlik (TÜREV) |
|---|---|---|
| Sözleşme doğrulama hatası | Yok (yüzey yok) | Sert (PR engelleyici) |
| Mock tutarlılık hatası | Yok | Sert |
| Eski kayıt uyarısı | Yok | Yumuşak (rapor) |
| Secret bulgusu | GitLeaks job var → ⚠️ sertlik ADR | Sert |
| Canlı onaysız temas | Yok | **Sert + DUR** (fail-safe) |
| Coverage eşiği | `js-coverage` yumuşak | UNKNOWN (eşik yok → ADR) |

### §5.6 Raporu Kim Okur? (rol kesişimi)

| Rol | Hangi alan | Eylem |
|---|---|---|
| QA Engineer | `contract_validation`, `mock_consistency`, `stale_records` | Kırmızıysa düzeltme |
| DevOps Engineer | `verdict`, job sertliği | Kapıyı yönetir |
| Security Engineer | `secret_scan`, `live_contact` | Veto/rapor |
| Vault Steward | `date` + `log.md` kaydı | Onay/append |

---

## §6 Kapsam Dışı (bu dosyada işlenmez)

1. Testin **kendisi** (kod) → `ittest-kod-ornekleri.md`
2. Ortam kurulumu / izolasyon → **K019-testing** (⚠️ sınır)
3. OpenAPI belgesi doğrulamasının ayrı serisi → `api-contracts/openapi/openapi-dogrulama.md`
4. Güvenlik kuralının tam metni (yetkili otorite) → `.ai/.decisions` + güvenlik kuralları
5. Branch koruma ayarları → ⚠️ UNKNOWN (GitHub ayarları diskte değil)

---

## §7 Açık Sorular (özet — tam liste: `ittest-web-arastirma.md` §Açık Sorular)

> Bu tablo FM `open_questions` alanının özetidir; yeni iddia eklemez.
> Tamamı `ittest-web-arastirma.md` §Açık Sorular'da tek listede toplanır.

| # | Soru | İşaret |
|---|---|---|
| Q1 | Sözleşme doğrulaması PR'da mı merge'de mi bağlayıcı? | ⚠️ VERIFICATION REQUIRED |
| Q2 | Eski kayıt tespiti otomatikleşecek mi? | UNKNOWN |
| Q3 | `.gitleaks.toml` neden yok (kasten mi)? | ⚠️ VERIFICATION REQUIRED |
| Q4 | `js-e2e`/`js-coverage` yumuşak kapıları ne zaman sertleşecek? | UNKNOWN |
| Q5 | Rapor şeması hangi biçimde standart olacak? | UNKNOWN (ADR) |

---

## §8 Okuma Haritası — 5 Dosyalık Seri

| # | Dosya | Ne verir |
|---|---|---|
| 1 | `ittest-anayasa.md` | Tanım, piramit, live vs mock, K019 sınırı |
| 2 | `ittest-sozlesme.md` | Ortam, stub, canlı onay kapısı |
| 3 | **`ittest-dogrulama.md`** (bu dosya) | Doğrulama, tutarlılık, eski kayıt, secret, CI + rapor |
| 4 | `ittest-kod-ornekleri.md` | 12 dilde kalıp |
| 5 | `ittest-web-arastirma.md` | URL kanıtları |

---

## § Kaynak Tablosu (bu dosyadaki tüm URL'ler)

| # | İddia (kısa) | URL | Tarih | Durum |
|---|---|---|---|---|
| S1 | Consumer/provider contract test tanımı; provider test = kod↔doküman senkronu; tek başına yetersizliği; "contract by example" vs şema | https://docs.pact.io/ | 2026-10-16 | ✅ çekildi |
| S2 | Periyodik contract test, günde bir ritim, snapshot/stub biçimi, başarısızlık → tutarlılık görevi, build'i kırma esnekliği | https://martinfowler.com/bliki/ContractTest.html | 2026-10-16 | ✅ çekildi |
| S3 | HAR kayıt/replay, `update` ile yenileme, route engelleme | https://playwright.dev/docs/mock | 2026-10-16 | ✅ çekildi |
| S4 | PHPUnit 10.5 resmi kılavuzu (test çalıştırma/raporlama referansı) | https://docs.phpunit.de/en/10.5/ | 2026-10-16 | ✅ çekildi |
| S5 | Piramit makalesi — taklit uç + konfigürasyonla uç değiştirme, üretimde test yasağı | https://martinfowler.com/articles/practical-test-pyramid.html | 2026-10-16 | ✅ çekildi |
| R1 | Integration suite kaldırma notu | `auth.coremusic.net/phpunit.xml` satır 10-12 | 2026-10-16 | ✅ disk |
| R2 | Secret tarama workflow (GitLeaks v2, `.gitleaks.toml` YOK notu) | `.github/workflows/secret-scan.yml` (32 satır) | 2026-10-16 | ✅ disk |
| R3 | CI job yüzeyi + yumuşak kapılar | `.github/workflows/ci.yml` (203 satır; satır 77, 80, 167, 183) | 2026-10-16 | ✅ disk |
| R4 | Tek mock kaynağı | `assets.coremusic.net/tests/mocks/player-api.mock.js` (184 satır) | 2026-10-16 | ✅ disk |
| R5 | API suite tek `Unit` | `api.coremusic.net/phpunit.xml` | 2026-10-16 | ✅ disk |

> **Kapsam beyanı:** Bu dosyada listelenmeyen hiçbir URL dayanak alınmamıştır.
> `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` işaretli satırlar için dayanak yoktur.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Kaynak:** v2 §2.13 · ADR-097 §5.5 · registry §8.1 · docs.pact.io · martinfowler.com · **Tarih: 2026-10-16**
