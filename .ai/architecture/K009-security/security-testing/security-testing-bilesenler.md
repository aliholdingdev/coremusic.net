---
node_id: K009-security/security-testing/security-testing-bilesenler
node_type: module
status: draft
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-testing/security-testing-anayasa
  - K009-security/security-testing/security-testing-surucu
evidence:
  - C:\www\coremusic.net\shared\phpunit.xml·1-39
  - C:\www\coremusic.net\shared\composer.json·23-49
  - C:\www\coremusic.net\.github\workflows\ci.yml·44-186
  - C:\www\coremusic.net\.github\workflows\secret-scan.yml·24-31
validation: Faz 7 ön draft — envanter glob/grep ile ölçüldü, test koşuşturulmadı
open_questions:
  - "Fixtures dizini neden testsuite'e kayıtlı değil? (tasarım mı?) ⚠️ VERIFICATION REQUIRED"
  - "Ortak TestCase alt sınıfı yok — mock helper nerede? ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K009 · security-testing · Bileşenler

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-15 · Bileşen Envanteri

Bu modül, güvenlik testi disiplinini **fiziksel olarak oluşturan bileşenleri**
listeler: test suite kayıtları, testinator yapılandırması, script'ler, CI job'ları
ve test sınıfı yapısı. `security-testing-surucu.md` gate'leri bu bileşenleri
**çalıştırır**; bu dosya ise "hangi parça var" sorusunun cevabıdır.

**TT-11 · Bileşen KK:** Her bileşenin `Dosya·Satır` kanıtı zorunlu; envanter
`shared/AGENTS.md` §2 envanteriyle çelişiyorsa disk kazanır ve fark raporlanır.

---

## §1. Test Dizini Envanteri (disk ölçümü)

Ölçüm: glob `shared/tests/**` — **45 dosya / 41 `*Test.php` sınıfı / 9 dizin**
(2026-10-15). (Prompt'taki "22 dosya" değeri diskle uyuşmuyor → raporlandı.)

| # | Dizin | Dosya (glob) | phpunit.xml testsuite? |
|---|---|---|---|
| D-1 | `shared/tests/Unit/` | 16 | ✅ (`phpunit.xml·9-11`) |
| D-2 | `shared/tests/Middleware/` | 9 | ✅ (`phpunit.xml·24-26`) |
| D-3 | `shared/tests/Api/` | 8 | ✅ (`phpunit.xml·12-14`) |
| D-4 | `shared/tests/Repository/` | 4 | ✅ (`phpunit.xml·15-17`) |
| D-5 | `shared/tests/Security/` | 2 | ✅ (`phpunit.xml·21-23`) |
| D-6 | `shared/tests/Events/` | 2 | ✅ (`phpunit.xml·18-20`) |
| D-7 | `shared/tests/OAuth/` | 1 | ✅ (`phpunit.xml·30-32`) |
| D-8 | `shared/tests/Component/` | 1 | ✅ (`phpunit.xml·27-29`) |
| D-9 | `shared/tests/Fixtures/` | 2 | ❌ **kayıtlı değil** (destek dizini) |

**8 testsuite kayıtlı, 9 dizin var** — fark `Fixtures/` (destek verisi, test
sınıfı değil). Uyumlu: `shared/AGENTS.md` §2 satırı da "9 dizin: Api, Component,
Events, Fixtures, Middleware, OAuth, Repository, Security, Unit" der
(`shared/AGENTS.md·§2 tests/ satırı`).

---

## §2. PHPUnit Yapılandırması

Kaynak: `shared/phpunit.xml·1-39`.

| Ayar | Değer | Satır |
|---|---|---|
| bootstrap | `vendor/autoload.php` | `phpunit.xml·4` |
| stopOnFailure | `false` (tüm suite döner) | `phpunit.xml·6` |
| cacheDirectory | `.phpunit.cache` | `phpunit.xml·7` |
| testsuite sayısı | 8 (Unit, Api, Repository, Events, Security, Middleware, Component, OAuth) | `phpunit.xml·8-33` |
| coverage source | `src` (suffix `.php`) | `phpunit.xml·34-38` |

Güvenlik açısından kritik satır: **coverage source = `src`** → kod analizi
(PHPStan/kod tarama job'ları) `shared/src/**`'yi hedefler; test dosyaları
kapsam dışıdır (`phpunit.xml·34-38`).

---

## §3. Composer Bileşenleri

Kaynak: `shared/composer.json` (kök `composer.json` **yok** — glob'da eşleşme 0).

| Bileşen | Değer | Satır |
|---|---|---|
| `scripts.test` | `phpunit` çalıştırıcısı | `shared/composer.json·23-26` |
| `scripts.stan` | PHPStan (level 5) | `shared/composer.json·23-26` |
| require-dev: phpunit | `^10.5` | `shared/composer.json·46-49` |
| require-dev: phpstan | `^1.10` | `shared/composer.json·47` |

Not: `composer lint` job'u `ci.yml`'da tanımlı (`ci.yml·141-186`) — script adının
kendisi okunmadı → **⚠️ VERIFICATION REQUIRED** (B-07 ile aynı başlık).

---

## §4. CI Bileşenleri (workflow job'ları)

### §4.1 `.github/workflows/ci.yml`

| Job | Rol | Satır |
|---|---|---|
| `test` | `composer test` — tetikleyici `paths: shared/**` | `ci.yml·62-64,104-108` |
| `test-reporter` | test çıktısını raporlar | `ci.yml·123-127` |
| `build` | derleme/önyükleme doğrulaması | `ci.yml·44-186` (job aralığı) |
| `improved-code` (lint) | `composer lint` | `ci.yml·141-186` |
| `codacy-security-scan` | statik güvenlik taraması | `ci.yml·141-186` |
| `codacy-analysis-cli` + `sourcery` | kod analizi | `ci.yml·141-186` |

Detaylı gate yorumu `security-testing-surucu.md` §2-§3'tedir; bu dosya job
**envanterini** tutar, akışı tekrarlamaz.

### §4.2 `.github/workflows/secret-scan.yml`

| Job/adım | Rol | Satır |
|---|---|---|
| `add-build-trigger` | tetikleme/`rev` bayrağı | `secret-scan.yml·24-31` |
| kod tarama adımı | repo geneli secret taraması | `secret-scan.yml·24-31` |

`on:` tetikleyicisi okunmadı → ⚠️ (dogrulama.md §4.3 B-08 ile aynı boşluk).

---

## §5. Test Sınıfı Yapısı (grep ölçümü)

Ölçüm: grep `extends TestCase` / `use PHPUnit\Framework\TestCase` — örneklenen
**60/60 eşleşme** hepsi aynı kalıpta:

```php
use PHPUnit\Framework\TestCase;          // dosya satır ~5-13
final class XxxTest extends TestCase      // sınıf tanımı
```

| Özellik | Durum | Kanıt |
|---|---|---|
| Ortak base TestCase (`CoreMusicTestCase` vb.) | **YOK** | grep `abstract class` → 0 eşleşme |
| `final class ... extends TestCase` | Evrensel | grep 60 örnek, tek kalıp |
| Mock helper / test factory | Dosya içi kurulum (construct + helper method) | dosya bazlı okuma (ornekler.md §1) |
| Fixtures kullanımı | `shared/tests/Fixtures/` 2 dosya | glob |

**Tasarım notu:** Her test sınıfı doğrudan `PHPUnit\Framework\TestCase`'i extend
eder; devralınmış ortak test altyapısı yok. Bu, bağımsızlık sağlar ama mock
kurulumunu dosya dosya çoğaltır (bkz. §8 B-11).

---

## §6. Bileşen × Gate Matrisi

| Bileşen | Hangi gate kullanır (surucu.md) | Durum |
|---|---|---|
| `shared/tests/**` (41 sınıf) | G-01..G-04 lokal + G-05 `composer test` | envanter hazır |
| `phpunit.xml` suite kayıtları | G-05 | 8 suite |
| PHPStan (`composer stan`) | G-06 statik analiz | level 5 |
| kod tarama job'ları (`ci.yml·141-186`) | G-07..G-08 | okundu, eşiği ⚠️ |
| `secret-scan.yml` | G-09..G-11 | job tanımı okundu, tetikleyici ⚠️ |
| Staging DAST | G-12..G-13 | **bileşen yok** (bkz. §8 B-12) |

---

## §7. Bileşen Kaynaklı Kurallar

1. **Yeni test = doğru suite:** `phpunit.xml·8-33` içinde kayıt yoksa test koşmaz;
   yeni dizin açılırsa testsuite satırı da eklenir.
2. **Yeni test = doğru kalıp:** `final class ... extends TestCase` (§5) — kalıp
   kırılmaz, ortak base sınıf sonradan ADR ile eklenir.
3. **Kapsam source = `src`:** test dosyaları coverage'a girmez (`phpunit.xml·34-38`).
4. **Envanter çelişkisi = disk kazanır:** `shared/AGENTS.md` §2 ile glob farkı
   raporlanır (TT-11).

---

## §8. Bileşen Boşlukları (⚠️)

| # | Boşluk | Etki | ⚠️ |
|---|---|---|---|
| B-10 | `Fixtures/` testsuite'e kayıtlı değil (§1 D-9) | Tasarım mı, unutulmuş mu? | ⚠️ VERIFICATION REQUIRED |
| B-11 | Ortak TestCase/mock helper yok (§5) | Mock kurulumu dosyalar arasında tekrar ediyor | ⚠️ VERIFICATION REQUIRED |
| B-12 | Staging DAST bileşeni yok (§6 G-12..G-13) | Gate zincirinin son halkası tanımsız = DAST kapalı | ⚠️ VERIFICATION REQUIRED |
| B-13 | `composer lint` script'inin composer.json'daki karşılığı okunmadı | Job ↔ script eşleşmesi teyitli değil | ⚠️ VERIFICATION REQUIRED |
| B-14 | `shared/tests/` kök +9 dosya (dizin dışı) | Dizinsiz dosyalar hangi suite'e giriyor? | ⚠️ VERIFICATION REQUIRED |

---

## §9. Envanter Özeti

| Metrik | Değer | Kaynak |
|---|---|---|
| Test dizini | 9 | glob |
| Testsuite kaydı | 8 | `phpunit.xml·8-33` |
| Test sınıfı | 41 (`*Test.php`) | glob |
| Dosya toplamı | 45 | glob |
| Base TestCase | 0 | grep |
| CI job'ı (ci.yml) | 6 | `ci.yml·44-186` |
| Secret-scan işi | 1 (2 adım) | `secret-scan.yml·24-31` |
| DAST bileşeni | 0 | §6/§8 B-12 |

---

## §BAĞLANTILAR

- [[K009-security/security-testing/index]] — giriş, §3 dosya haritası (bileşenler satırı)
- [[K009-security/security-testing/security-testing-surucu]] — G-01..G-13 (bu bileşenleri çalıştıran zincir)
- [[K009-security/security-testing/security-testing-dogrulama]] — AD-xx satırları (bileşen kanıtları burada işlenir)
- [[K009-security/security-testing/security-testing-ornekler]] — Ö-01..Ö-11 alıntılar (sınıf yapısının kanıtı)
- [[K009-security/security-testing/security-testing-kapsam]] — kapsam içi/dışı sınırı
- [[authentication-bilesenler]] — kardeş bileşen envanteri (paralel yapı)

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
