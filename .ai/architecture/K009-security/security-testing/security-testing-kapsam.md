---
node_id: K009-security/security-testing/security-testing-kapsam
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing/security-testing-anayasa
  - K009-security/security-testing/security-testing-surucu
evidence:
  - C:\www\coremusic.net\.ai\architecture\K009-security\security-testing\index.md·12-33
  - C:\www\coremusic.net\shared\tests\·45 dosya (glob)
  - C:\www\coremusic.net\.github\workflows\ci.yml·44-186
validation: Faz 7 ön draft — kabul kriteri K1-K12 (§7)
open_questions:
  - "Kapsam dışı K011-application'ın testi kimde? ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · security-testing · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Kapsam Tanımı

Bu modül, güvenlik testi alanının **sınırlarını** çizer: ne bizde, ne başka vault
alanında, nerede biter kapıyı devredesin. Kapsam = kapsam dışındakini de bilmektir;
`security-test-stratejisi.md` T-01..T-20 bu sınırların İÇİNDE kalır, disiplin
`security-testing-anayasa.md` §1-§4 bağlar.

**TT-08 · Kapsam KK:** Kapsam satırı olmayan test hedefi = stratejide yok sayılır;
kapsam dışı hedefe yazılan test = yanlış domain = iade (§6 C-01).

---

## §1. Kapsam İçi — security-testing'in sahibi olduğu yüzeyler

`index.md` §4 domain eşlemesi: "Güvenlik testi disiplini — security-* node'larının
test kanıtı katmanı" (`index.md·12-33`).

### §1.1 Kod yüzeyi (repo kanıtı)

| Yüzey | Kanıt (Dosya·Satır) | Kapsam |
|---|---|---|
| Middleware testleri | `shared/tests/Middleware/` · 9 dosya (glob) | İçeri |
| Security testleri | `shared/tests/Security/` · 2 dosya (glob) | İçeri |
| Unit testleri | `shared/tests/Unit/` · 16 dosya (glob) | İçeri |
| Api testleri | `shared/tests/Api/` · 8 dosya (glob) | İçeri |
| Repository testleri | `shared/tests/Repository/` · 4 dosya (glob) | İçeri |
| Events testleri | `shared/tests/Events/` · 2 dosya (glob) | İçeri |
| OAuth testleri | `shared/tests/OAuth/` · 1 dosya (glob) | İçeri |
| Component testleri | `shared/tests/Component/` · 1 dosya (glob) | İçeri |
| Test fixture'ları | `shared/tests/Fixtures/` · 2 dosya (glob) | İçeri (destek) |

Ölçüm notu: `shared/tests/` kökünde +9 `*Test.php` dosyası daha vardır; toplam
**45 dosya / 41 test sınıfı / 9 dizin** (2026-10-15 glob). Kapsam tablosu
dizin bazlıdır, dosya bazlı tam liste `security-testing-bilesenler.md` §1'dedir.

### §1.2 Test altyapısı yüzeyi

| Yüzey | Kanıt (Dosya·Satır) | Kapsam |
|---|---|---|
| PHPUnit kurulumu | `shared/composer.json·46-49` (`"phpunit/phpunit": "^10.5"`) | İçeri |
| PHPStan kurulumu | `shared/composer.json·47` (`"phpstan/phpstan": "^1.10"`) | İçeri |
| Test scripti | `shared/composer.json·23-26` (`"test"`, `"stan"`) | İçeri |
| CI test job'ları | `.github/workflows/ci.yml·44-186` | İçeri |
| Secret tarama | `.github/workflows/secret-scan.yml·1-45` | İçeri |

### §1.3 Süreç yüzeyi

| Yüzey | Kanıt (Dosya·Satır) | Kapsam |
|---|---|---|
| Strateji T-01..T-20 | `.ai/architecture/K009-security/security-test-stratejisi.md·51-75` | İçeri (girdi) |
| Red-team disiplini | `security-testing-anayasa.md` §1-§2 | İçeri (otorite) |
| Sürücü zinciri | `security-testing-surucu.md` §1-§3 | İçeri (motor) |
| Kanıt satırları | `security-testing-dogrulama.md` §3 | İçeri (çıktı) |
| Örnek alıntılar | `security-testing-ornekler.md` §1-§2 | İçeri (kanıt) |

---

## §2. Kapsam Dışı — başka domainin sahibi olduğu yüzeyler

`index.md` §4'ün dışarıda bıraktıkları (`index.md·12-33` — "scope: security test
disiplini ... authentication / session / cryptography / ... test kapsamı farklı
alanlarda belgelenmiş"):

| Dışarıda kalan | Sahip domain | Gerekçe |
|---|---|---|
| Kimlik doğrulama testi (login/MFA/lockout senaryoları) | `K009-security/authentication` | Kimlik test senaryoları authentication alanında belgelenir; biz yalnız middleware davranışını test ederiz |
| İşletim sistemi güvenliği testi | `K000-isletim-sistemi/security` | OS/altyapı katmanı web uygulama testinin dışındadır |
| Uygulama iş mantığı testi | `K011-application` | İşlevsel doğrulama güvenlik testi değildir |
| Genel QA stratejisi, test kapsamı metrikleri, bağımlılık test politikası | `K019-testing` | Genel test domaini (diskte: `testing-master`, `dogrulama`, `bagimliliklar`, `index`) — güvenlik DIŞI test süreci oradadır |
| Sızma taraması için canlı hedef | **Üretim** — yasak | `security-testing-anayasa.md` §2.1 A-01/A-06: üretim hedefe aktif test yasak, yalnız izleme |

**TT-09 · Kapsam dışı KK:** Kapsam dışı hedefe güvenlik testi talebi gelirse
(ör. "OS firewall'u test et") → domain sahibine iade edilir; bu node red-team
bakış açısıyla **not alır, test etmez**.

---

## §3. Sınır Matrisi — kim ne yapar

| Durum | security-testing | K18-testing | authentication | Üretim/DevOps |
|---|---|---|---|---|
| Middleware negatif testi (401/403/419/429) | **Yazar** | — | — | — |
| CSRF/Origin/RateLimit assertion'ı | **Yazar** | — | — | — |
| Test altyapısı (phpunit/phpstan sürüm) | Kullanır | **Sahip** | — | — |
| Test coverage metriği politikası | Referans verir | **Sahip** | — | — |
| Login/MFA senaryo testi | — | — | **Sahip** | — |
| CI job çalıştırma | Koşullu tanımlar | — | — | **Çalıştırır** |
| Production'a aktif tarama | **Yasak** (§2.1 anayasa A-06) | — | — | İzleme sahibi |
| Secret sızıntısı müdahalesi | Kanıt üretir | — | — | **Müdahale sahibi** |

Kanıt: CI job sahipliği `.github/workflows/ci.yml·44-186` (test/lint/improved-code,
build, test-reporter, codacy-security-scan, codacy-analysis-cli, sourcery);
üretim yasağı `security-testing-anayasa.md` §2.1 A-01/A-06.

---

## §4. Kapsam İçi Ayrıştırma — ne İLE ne DEĞİL

| Kapsam İÇİNDE | Kapsam DIŞINDA (aynı kelime farklı anlam) |
|---|---|
| "Test" = güvenlik doğrulayıcı test (negatif senaryo, assertion) | "Test" = genel QA kapsam/coverage politikası (K18-testing) |
| "Middleware testi" = davranış assertion'ı (status/header/regex) | "Middleware üretimi" = `K002-backend` / PHP kod yazarlığı |
| "Secret tarama" = tarama tanımı + kanıt okuma | "Secret sızıntısı kurtarma" = olay müdahalesi (üretim sahibi) |
| "Staging'de DAST" = kapsam İÇİ, koşul `ci.yml` gate | "Prod'da DAST" = kapsam DIŞI, yasak |
| "SAST (phpstan)" = statik analiz kanıtı | "SAST aracı kurulumu/sürüm politikası" = K18-testing `bagimliliklar` |

---

## §5. Girdi / Çıktı Sınırı

```text
GİRDİ (bu node okur, SAHİP DEĞİL):
  security-test-stratejisi.md   T-01..T-20  (plan)
  K009-security/*.md            güvenlik disiplinleri (alan tanımları)
  K18-testing/*                test altyapısı politikası (sürüm/coverage)
  shared/composer.json          phpunit/phpstan sürümleri (gerçek)

ÇIKTI (bu node üretir, TEK SAHİP):
  güvenlik test assertion'ları     shared/tests/** (test dosyaları)
  kanıt satırları                  security-testing-dogrulama.md
  örnek alıntılar                  security-testing-ornekler.md
  gate zinciri tanımı              security-testing-surucu.md
```

Sınır kuralı: `shared/tests/**` içinde **güvenlik** assertion'ının sahibi bu node;
test altyapısının (sürüm, runner, coverage) sahibi K019-testing. Aynı dosyada
farklı satırlar farklı sahip olabilir — sahiplik satır bazlıdır.

---

## §6. Çakışma Halleri

| # | Çakışma | Çözüm |
|---|---|---|
| C-01 | Kapsamı belirsiz hedef (satır yok) | Stratejide yok say → kapsam satırı açılıncaya kadar test yazma |
| C-02 | Güvenlik testi + K019 coverage politikası çatışıyor | Güvenlik assertion'ı bu node'da; coverage yüzdesi K019'da — ikisi ayrı satır |
| C-03 | authentication test senaryosu ile middleware testi örtüşüyor | Senaryo/authentication alanı; middleware davranış assertion'ı burada. Örtüşen satır = iki dosyada da referans, tek assertion |
| C-04 | Üretim hedefe test talebi | Red-team A-01/A-06: **DUR**, yalnız izleme öner, kanıt üret |
| C-05 | Kapsam dışı dosya `shared/tests/` içine yazıldı | Domain sahibine iade (K011/K019/K000) — taşınacak dosya değil, sahibine devir |

---

## §7. Kabul Kriterleri (KK)

| # | Kriter | Doğrulama |
|---|---|---|
| K1 | Kapsam içi her yüzeyin `Dosya·Satır` kanıtı var | §1 tabloları — glob/read |
| K2 | Kapsam dışı her satırın sahip domaini yazılı | §2 — index.md §4 |
| K3 | Sınır matrisinde çelişen sahiplik yok | §3 — tek sahip sütunu |
| K4 | Girdi/çıkış sınırı ayrılmış | §5 — girdi node'ları okur, çıktıyı üretmez |
| K5 | Çakışma halleri en az 5 | §6 C-01..C-05 |
| K6 | Üretim yasağı kapsam içinde tekrarlandı | §2 + §6 C-04 (anayasa A-01/A-06 ile uyumlu) |
| K7 | `shared/tests/` ölçümü disk ile tutarlı | §1.1 not: 45 dosya / 41 sınıf / 9 dizin |
| K8 | K18-testing varlığı disk kanıtlı | `.ai/architecture/K18-testing/` (index/dogrulama/bagimliliklar/testing-master) |
| K9 | Yeni hedef eklendiğinde kapsam satırı önce gelir | §1/§2 akışı |
| K10 | Kapsam dışı iade C-05 ile işleme alınır | §6 |
| K11 | Strateji T-01..T-20 kapsam İÇİNDE | §1.3 (strateji satır aralığı: `security-test-stratejisi.md·51-75`) |
| K12 | Kapsam satırı olmayan test = iade (TT-08) | §1 başı |

**Durum:** K1-K12 TASLAK kontrol listesi — `security-testing-dogrulama.md` §3'te
AD-xx satırlarına bağlanır (Faz 7'de çalıştırılacak; bu dosyada PASS iddiası YOK).

---

## §8. Sınır Dışı Bilinçli Boşluklar

| # | Boşluk | Neden kapsam dışı | ⚠️ |
|---|---|---|---|
| B-01 | K011-application güvenlik testinin tam sahibi kim? | `index.md·12-33` yalnız "test kapsamı farklı alanlarda" der, alan adı vermez | ⚠️ VERIFICATION REQUIRED |
| B-02 | Canlı DAST'ın sahibi (staging gate'i kim işletir?) | `ci.yml` gate'i kodda var ama işletim sahibi belirsiz | ⚠️ VERIFICATION REQUIRED |
| B-03 | Secret sızıntısı müdahale adımları | Yalnız "revoke" bayrağı var (`ci.yml·167`), tam müdahale planı yok | ⚠️ VERIFICATION REQUIRED |
| B-04 | Test flakiness politikası (güvenlik testi flake olursa?) | K019-testing kapsamı olabilir ama bu node'da tanımlı değil | ⚠️ VERIFICATION REQUIRED |
| B-05 | `shared/tests/` kök +9 dosyanın domain sahibi | Glob'ta dizin dışı dosyalar var; sahiplik okunmadı | ⚠️ VERIFICATION REQUIRED |

---

## §BAĞLANTILAR

- [[K009-security/security-testing/index]] — giriş, §3 dosya haritası (kapsam satırı)
- [[K009-security/security-testing/security-testing-anayasa]] — R/A/W kriterler, §2.1 üretim yasağı
- [[K009-security/security-testing/security-testing-surucu]] — G-01..G-13 gate zinciri (kapsamın motoru)
- [[K009-security/security-testing/security-testing-dogrulama]] — AD-xx kanıt satırları (KK bağlandığı yer)
- [[K009-security/security-test-stratejisi]] — T-01..T-20 planı (girdi)
- [[authentication-kapsam]] — kardeş kapsam örneği (paralel yapı)
- [[K18-testing/index]] — genel test domaini (kapsam dışı sahip)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
