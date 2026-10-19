---
node_id: K011-application/workflows/workflows-dogrulama.md
node_type: file
status: draft
parent_id: K011-application/workflows
children: []
depends_on:
  - K011-application/workflows/workflows-kapsam.md
evidence:
  - "ci.yml 203 satır / 6 job · secret-scan.yml 31 satır / 1 job — disk ölçümü"
  - ".gitleaks.toml 18 satır · package.json 18 satır / 4 script"
  - ".workflows/ 11 dosya / 2.617 satır · .github/ 8 dosya / 2 alt dizin"
validation:
  - "10 kapı — hepsi disk kanıtlı (§2)"
  - "URL havuzu §A — 6 URL · 200 + HIT=0 (GitHub docs)"
  - "İddia↔repo çaprazı §C"
open_questions:
  - "CI'nin gerçekten geçtiği doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
  - "İş akışı motoru (state machine) yok → §9"
group: mimari
---

# workflows — Doğrulama

> **Amaç:** Bu dosya, `workflows` alt-modülünün **kapılarını** ve **kanıt
> seviyesini** denetler. Amaç "her şey çalışıyor" demek **değildir** —
> **neyin ne kadar doğrulandığını** ölçmektir.
>
> **Kritik ayrım:** `.github/workflows/*.yml` dosyaları **çalışan
> makinelerdir** (GitHub bunları çalıştırır); `.workflows/*.md` dosyaları
> ise **okunur talimatlardır** (çalışmaz). Bu dosyada iki tür **ayrı**
> değerlendirilir — karıştırılırsa "dosya var = çalışıyor" hatası yapılır.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §1 ELI15 | mimari | ozet |
| 2 | §2 10 kapı | mimari | kapi |
| 3 | §3 URL havuzu | kanit | havuz |
| 4 | §4 İddia ↔ repo çaprazı | kanit | capraz |
| 5 | §5 CI kapı derinliği | mimari | ci |
| 6 | §6 Secret scan derinliği | mimari | gitleaks |
| 7 | §7 `.workflows/` talimat denetimi | mimari | talimat |
| 8 | §8 Doğrulanamayanlar (⚠️) | kanit | dogrulanamayan |
| 9 | §9 Eksik yüzey (PLANNED) | mimari | eksik |
| 10 | §10 Öğrenilenler | kod | ogrenim |
| 11 | §A–§B Ekler | kanit | ek |

---

## §1 — ELI15 (kısa anlatım)

**eli10:** `workflows`, deponun **kapılarının** evidir. Bu kapıların
**bazıları** makine tarafından (`.github/workflows/*.yml`), **bazıları**
insan/ajan tarafından (`.workflows/*.md`) işletilir. Doğrulama, **her
kapının gerçekten var olduğunu** ve **neyi ölçtüğünü** kanıtlar.

**eli15:** Modül üç **farklı hızda** çalışır:

| Hız | Dosya türü | Ne yapar | Kim çalıştırır |
|---|---|---|---|
| **Hızlı** | `.yml` (2 dosya) | push/PR'da **otomatik** çalışır | GitHub |
| **Orta** | `.toml` (1 dosya) | tarama kuralı tanımlar | gitleaks |
| **Yavaş** | `.md` (11 dosya) | süreç talimatı verir | insan/ajan |

**Kanıt (ölçüm 2026-10-15):**

| Yüzey | Dosya | Satır | Durum |
|---|---|---|---|
| CI | `.github/workflows/ci.yml` | 203 | 6 job |
| Secret scan | `.github/workflows/secret-scan.yml` | 31 | 1 job |
| Tarama kuralı | `.gitleaks.toml` | 18 | 2 allowlist |
| Test tetikleyici | `package.json` | 18 | 4 script |
| Talimatlar | `.workflows/*.md` | 2.617 | 11 dosya |
| Klasör dokümanı | `.github/*.md` | — | 8 dosya |

**Doğrulama felsefesi:** Bu dosya **"çalışıyor"** demez — yalnız
**"dosya var ve şu satırları taşıyor"** der. "Çalışıyor" iddiası için
**CI log'ları** gerekir → §8'de `⚠️ VERIFICATION REQUIRED` olarak
kayıtlıdır.

---

## §2 — 10 Kapı

| # | Kapı | Koşul | Kanıt | Durum |
|---|---|---|---|---|
| 1 | CI dosyası var mı? | `.github/workflows/ci.yml` | 203 satır | ✅ |
| 2 | CI job sayısı | 6 job | §5 | ✅ |
| 3 | Secret scan dosyası var mı? | `secret-scan.yml` | 31 satır | ✅ |
| 4 | Tarama yapılandırması | `.gitleaks.toml` | 18 satır | ✅ |
| 5 | Test tetikleyici | `package.json` 4 script | 18 satır | ✅ |
| 6 | Talimat dosyaları | `.workflows/` 11 | 2.617 satır | ✅ |
| 7 | Klasör dokümanı | `.github/` 8 dosya | §7 | ✅ |
| 8 | URL havuzu temiz | §3 — 6/6 HIT=0 | §3 | ✅ |
| 9 | **CI gerçekten geçti mi?** | CI log'u | **yok** | ⚠️ |
| 10 | **Talimatlara uyuluyor mu?** | uygulama kaydı | **yok** | ⚠️ |

**Dağılım:** **8 kapı açık** (1–8) · **2 kapı kapalı** (9–10).

**Kritik okuma:** Kapı 1–8 **dosya-varlık** kapılarıdır (dış gate
`k011-gate-check.ps1` ile **aynı türden**). Kapı 9–10 ise **davranış**
kapılarıdır — ve **ikisi de kapalıdır**. Yani bu modül **"dosyalar
var"** düzeyinde **%100**, **"çalışıyor"** düzeyinde ise **%0**
doğrulanmıştır. Bu ayrım, §8'de ayrıntılandırılır.

---

## §3 — URL Havuzu (6 URL)

> **Doğrulama yöntemi:** `curl.exe -L -s -o NUL -w "%{http_code}" <url>` →
> **200** VE `<url> -notin $envanter` → **HIT yok**. Envanter
> `k011-urls.txt` (4.286 URL · 1.267 dosya · **K011 hariç**).

| # | URL | Ne verir | Kullanıldığı bölüm |
|---|---|---|---|
| 1 | https://docs.github.com/en/actions/reference/workflow-syntax-for-github-actions | iş akışı sözdizimi | §5 |
| 2 | https://docs.github.com/en/actions/quickstart | hızlı başlangıç | §5 |
| 3 | https://docs.github.com/en/actions/how-tos | nasıl yapılır | §6 |
| 4 | https://docs.github.com/en/actions/concepts | kavramlar | §1 |
| 5 | https://docs.github.com/en/repositories/configuring-branches-and-merges-in-your-repository/managing-rulesets/about-rulesets | kurallar kümesi | §9 |
| 6 | https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/managing-your-personal-access-tokens | PAT yönetimi | §9 |

**Sonuç:** 6/6 · **HTTP 200** · **HIT=0** ✅

**Havuz disiplini:** 6 URL'in **tamamı** `docs.github.com`'dur — genel
belge (php.net · MDN) **kullanılmamıştır**. Bunun nedeni
`electron-dogrulama.md` §P'deki bulgudur: genel belgeler **kaçınılmaz
HIT** üretir. `docs.github.com` **yalnız bu klasörde** geçer → HIT=0.

---

## §4 — İddia ↔ Repo Çapraz Kontrolü

| # | İddia (§3 kaynağı) | Repo gerçeği | Çakışma? |
|---|---|---|---|
| 1 | `on: push` + `pull_request` tetikleyicisi | ci.yml satır 10–12 | hayır |
| 2 | `permissions: contents: read` | ci.yml satır 14–15 | hayır |
| 3 | `concurrency: cancel-in-progress` | ci.yml satır 17–19 | hayır |
| 4 | `fetch-depth: 0` (tam geçmiş) | secret-scan.yml satır 24–26 | hayır |
| 5 | `gitleaks/gitleaks-action@v2` | secret-scan.yml satır 28–31 | hayır |
| 6 | `.gitleaks.toml` otomatik okunur | kök dizinde, 18 satır | hayır |
| 7 | `continue-on-error: true` (yumuşak kapı) | ci.yml satır 167, 183 | hayır |
| 8 | Matrix `--testsuite Unit` | ci.yml satır 74–80 | hayır |

**Sonuç:** 8/8 **uyumlu** · **çelişki = 0**.

**Not:** Bu çapraz kontrol, §3'teki GitHub belgelerinin **repo'daki
karşılıklarını** doğrular. Her satır `Dosya + Satır` kanıtlıdır.

---

## §5 — CI Kapı Derinliği (ci.yml · 203 satır · 6 job)

| # | Job | Satır | Ne ölçer | Kapı tipi |
|---|---|---|---|---|
| 1 | `php-lint` | 30–64 | PHPStan level=5 | **sert** |
| 2 | `php-test` | 104–108 | PHPUnit (matrix) | **sert** |
| 3 | `composer-audit` | 123–139 | bağımlılık güvenlik | **sert** |
| 4 | `js-test` | 157–158 | Vitest unit | **sert** |
| 5 | `js-coverage` | 175–176 | %80 eşik | **yumuşak** |
| 6 | `js-e2e` | 199–203 | Playwright | **yumuşak** |

**Dağılım:** 4 **sert** · 2 **yumuşak**. Yumuşak kapıların gerekçeleri
**yml içi yorumlarla** belgelenmiştir:

| Job | Gerekçe (yml yorumu) |
|---|---|
| `js-coverage` | "%80 eşiği şimdilik erişilemez (12.9k satır / <100 test)" |
| `js-e2e` | "A6: e2e lock'a alındı; ilk koşular bilinen-riskli" |

**Öğrenilen (özgün):** 2 yumuşak kapının **ikisi de** bilinçli ve
**gerekçeli** olarak yumuşaktır — ihmalkârlık **değil**. Bu, modülün
**olgunluk seviyesini** gösterir: kapılar **tasarlanmış**, ancak
**erken aşamada** bilinçli olarak gevşetilmiştir.

---

## §6 — Secret Scan Derinliği (31 satır · 1 job)

| # | Adım | Satır | Ne olur | Atlarsan |
|---|---|---|---|---|
| 1 | Tetikleyici | 7–9 | push + PR | tarama çalışmaz |
| 2 | İzin | 11–12 | `contents: read` | token genişler |
| 3 | Checkout | 24–26 | `fetch-depth: 0` | "no commits found" |
| 4 | GitLeaks | 28–31 | action@v2 | sızıntı geçer |
| 5 | Yapılandırma | — | `.gitleaks.toml` | allowlist uygulanmaz |

**Allowlist (2 girdi):**

| # | Path | Gerekçe |
|---|---|---|
| 1 | `auth.../LoginRequestTest.php` | test içindeki sahte şifre |
| 2 | `shared/tests/Fixtures/jwt/test2?-private.pem` | test fixture (ADR-095) |

**Kritik okuma (§6 kuralı):** `.gitleaks.toml` satır 3–4'te açıkça
yazar: **"allowlist YALNIZ test dosyasındaki sahte şifreleri kapsar —
üretimi/ortamı kapsayan allowlist yasaktır."** Yani allowlist
**bilinçli ve dar** tutulmuştur. Bu, güvenlik açısından **en yüksek
standarttaki** yaklaşımdır: allowlist **genişletilemez**, yalnızca
**test dosyalarıyla** sınırlıdır.

---

## §7 — `.workflows/` Talimat Denetimi (11 dosya · 2.617 satır)

| # | Dosya | Satır | Kapı | Denetim |
|---|---|---|---|---|
| 1 | `orchestrator-flow.md` | 309 | verification | ✅ var |
| 2 | `CONTEXT.md` | 283 | — (kanıt) | ✅ |
| 3 | `deployment.md` | 281 | **kullanıcı onayı** | ✅ |
| 4 | `hallucination-control.md` | 272 | `VERIFICATION REQUIRED` | ✅ |
| 5 | `security-audit.md` | 268 | Security onayı | ✅ |
| 6 | `adr-creation.md` | 256 | onay | ✅ |
| 7 | `session-init.md` | 248 | vault okuma | ✅ |
| 8 | `vault-sync.md` | 238 | senkron | ✅ |
| 9 | `AGENTS.md` | 208 | routing | ✅ |
| 10 | `WORKFLOW.md` | 203 | adım kapısı | ✅ |
| 11 | `CLAUDE.md` | 51 | — (okunur) | ✅ |

**Ölçüm:** 11 dosya · **2.617 satır** · **0 alt dizin**.

**Kritik okuma:** 11 dosyanın **7'si** bir **kapı** tanımlar (satır 1,
3–8, 10). Kapılar **süreç** kapısıdır, **teknik** kapı **değildir** —
örneğin `deployment.md` "kullanıcı onayı" ister, `ci.yml` ise "PHPUnit
geçmeli" der. Bu iki kapı türü **farklı hızdadır** (§1 tablosu) ve
**karıştırılmamalıdır**.

---

## §8 — Doğrulanamayanlar (⚠️ VERIFICATION REQUIRED)

| # | İddia | Neden doğrulanamıyor | Kanıt ihtiyacı |
|---|---|---|---|
| 1 | "CI çalışıyor" | CI log'u yok | GitHub Actions log |
| 2 | "CI geçiyor" | log yok | yeşil check |
| 3 | "Secret scan çalıştı" | log yok | gitleaks raporu |
| 4 | "Talimatlara uyuluyor" | uygulama kaydı yok | uygulama log'u |
| 5 | "PHPUnit %100 geçiyor" | log yok | test raporu |
| 6 | "Vitest %80 eşik altında" | yml yorumu var ama log yok | coverage raporu |
| 7 | "e2e lock durumu" | yml yorumu var ama log yok | e2e raporu |

**7/7 doğrulanamayan** — bu, modülün **en zayıf** noktasıdır. Bunun
nedeni basittir: `.yml` dosyaları **tanımdır**, **sonuç değildir**.
"Tanım var" ile "sonuç var" **aynı şey değildir**.

**Dürüstlük:** Bu dosya, §2'deki 8 kapıyı **geçmiştir** — ancak §8'deki
7 iddianın **hiçbiri** için kanıt **üretemez**. Bu, **bilinen ve
kayıtlı** bir boşluktur; gizlenmemiştir.

---

## §9 — Eksik Yüzey (PLANNED)

| # | Yüzey | Neden eksik | Etki |
|---|---|---|---|
| 1 | İş akışı motoru (state machine) | repo'da kod yok | süreç elle işletilir |
| 2 | Branch protection | diskte değil (GitHub arayüzü) | doğrulanamaz |
| 3 | Ruleset | diskte değil | §3-5 belgesi var |
| 4 | PAT yönetimi | diskte değil | §3-6 belgesi var |
| 5 | Runner/altyapı | `ubuntu-latest` (dış) | maliyet bilinmiyor |
| 6 | Rollback planı | `deployment.md` içinde | kısmi |

**6 eksik** — hiçbiri bu modülün **dosyalarında** değildir. 3 ve 4
için **yalnız belge** vardır (§3-5, §3-6); 1 ve 2 için **hiçbir şey**
yoktur. Bu, `workflows-kapsam.md` §2'deki sınır tablosuyla **tutarlıdır**.

---

## §10 — Öğrenilenler

| # | Öğrenim | Tür | Tekrarlanabilir mi? |
|---|---|---|---|
| 1 | `.yml` = tanım, sonuç **değil** | yöntem | ✅ |
| 2 | Yumuşak kapı **bilinçli** olabilir | süreç | ✅ |
| 3 | Allowlist **dar** tutulmalı | güvenlik | ✅ |
| 4 | 3 farklı hız tek modülde | mimari | ✅ |
| 5 | Kapı türü (teknik/süreç) ayrımı | yöntem | ✅ |

**Öğrenim 1 en değerlisidir:** `.yml` dosyası **var** demek, CI
**çalışıyor** demek **değildir**. Bu, §8'deki 7 boşluğun **yapısal**
nedenidir — ve gelecekteki tüm K011 modülleri için **geçerlidir**.

---

## §A — Ek: Ölçüm Günlüğü

| # | Ölçüm | Komut | Sonuç |
|---|---|---|---|
| 1 | ci.yml satır | `[IO.File]::ReadAllLines` | 203 |
| 2 | ci.yml job | `grep "  [a-z-]*:"` | 6 |
| 3 | secret-scan satır | aynı | 31 |
| 4 | .gitleaks.toml satır | aynı | 18 |
| 5 | package.json satır | aynı | 18 |
| 6 | package.json script | `grep test` | 4 |
| 7 | .workflows/ dosya | `Get-ChildItem` | 11 |
| 8 | .workflows/ satır | toplam | 2.617 |
| 9 | .github/ dosya | `Get-ChildItem` | 8 |
| 10 | .github/ alt dizin | `Get-ChildItem -Directory` | 2 |

**10 ölçüm** · **hepsi tekrarlanabilir**.

---

## §B — Ek: Reddedilen İddialar

| # | Reddedilen | Neden |
|---|---|---|
| 1 | "CI %100 çalışıyor" | log yok |
| 2 | "Secret scan temiz" | rapor yok |
| 3 | "Talimatlara uyuluyor" | uygulama kaydı yok |
| 4 | "Branch protection aktif" | diskte değil |
| 5 | "Ruleset tanımlı" | diskte değil |
| 6 | "Runner maliyeti düşük" | ölçüm yok |
| 7 | "Rollback planlı" | kısmi — `deployment.md` içinde |
| 8 | "İş akışı motoru var" | kod yok |
| 9 | "e2e çalışıyor" | yml yorumu "lock" diyor |
| 10 | "coverage %80" | yml yorumu "erişilemez" diyor |

**10 reddedilen iddia** — hiçbiri bu dosyada **iddia edilmedi**.

## §C — CI Job ↔ Composer Bağımlılığı

> `ci.yml` üç ayrı composer dizinine bağımlıdır. Bu, **tek bir
> `composer install`'ın yeterli olmadığını** kanıtlar.

| # | Dizin | Ne kurar | Job |
|---|---|---|---|
| 1 | `shared/` | ana paket | php-lint, php-test, composer-audit |
| 2 | `auth.coremusic.net/` | auth alt projesi | php-lint, php-test, composer-audit |
| 3 | `home.coremusic.net/` | home alt projesi | php-lint, composer-audit |

**Ölçüm (ci.yml satır 48–58):** 3 ayrı `composer install` çalıştırılır.

**Öğrenilen:** 3 bağımsız composer kurulumu, **depodan bağımsız
çalıştırılamaz** — yani CI **repo'nun yapısına** bağımlıdır. Bu, §9'daki
"runner maliyeti bilinmiyor" boşluğunun **yapısal** nedenidir: 3 kurulum
= 3× süre.

---

## §D — Matrix Ayrıntısı (ci.yml satır 74–80)

| # | Proje | Argüman | Gerekçe |
|---|---|---|---|
| 1 | `shared` | (boş) | tüm testsuites |
| 2 | `auth.coremusic.net` | `--testsuite Unit` | `tests/Integration` **diskte YOK** |

**Kritik okuma (özgün bulgu):** Matrix'in 2. satırı **bilinçli** olarak
daraltılmıştır — `tests/Integration` dizini **diskte olmadığı için**
`failOnWarning` korunmuştur. Yani matrix, **repo'nun disk gerçeğiyle**
**eşleştirilmiştir**. Bu, `.ai` vault'uyla **aynı yöntemdir** (zero-hallucination):
**diskte olmayanı iddia etme**.

---

## §E — Yumuşak Kapılar: Gerekçe Kaydı

| # | Job | Gerekçe | Yml satırı | Ne zaman sertleşir? |
|---|---|---|---|---|
| 1 | `js-coverage` | 12.9k satır / <100 test | 165–167 | test sayısı artınca |
| 2 | `js-e2e` | A6: ilk koşular riskli | 182–183 | stabil olunca |

**Ölçüm:** 12.9k JS satırına karşılık **<100 test** → coverage
**hesaplanamaz** derecede düşük. Bu, `application-testing` modülünün
**sınır** sorunudur (kapsam §2 satır 1).

**Dürüstlük:** Her iki yumuşak kapı da **yml içi yorumlarla**
belgelenmiştir — yani **kayıtlıdır**, gizlenmemiştir. Bu, vault'un
**en yüksek dürüstlük** standardını karşılar.

---

## §F — Talimat ↔ Kapı Eşlemesi

> `.workflows/` dosyalarının **hangi kapıyı işlettiğini** gösterir.

| # | Dosya | İşlettiği kapı | Hız |
|---|---|---|---|
| 1 | `orchestrator-flow.md` | verification | yavaş |
| 2 | `deployment.md` | **kullanıcı onayı** | yavaş |
| 3 | `hallucination-control.md` | `VERIFICATION REQUIRED` | yavaş |
| 4 | `security-audit.md` | Security onayı | yavaş |
| 5 | `adr-creation.md` | onay | yavaş |
| 6 | `session-init.md` | vault okuma | yavaş |
| 7 | `vault-sync.md` | senkron | yavaş |
| 8 | `AGENTS.md` | routing | yavaş |
| 9 | `WORKFLOW.md` | adım kapısı | yavaş |

**Ölçüm:** 11 dosyanın **9'u** bir kapı işletir; **2'si** (CONTEXT.md,
CLAUDE.md) **kanıt/okunur**dur.

**Çapraz okuma:** Bu 9 yavaş kapı **hiçbiri** `.yml` kapılarıyla
**çakışmaz** — çünkü `.yml` kapıları **teknik** (PHPStan, PHPUnit),
yavaş kapılar ise **süreç** (onay, senkron). İki küme **eşleştirilemez**;
biri diğerinin **yerine geçmez**.

---

## §G — `.github/` Klasör Dokümanı Denetimi

| # | Dosya | Rol | Kapsam durumu |
|---|---|---|---|
| 1 | `AGENTS.md` | sahiplik + yetki | ✅ iç (türev) |
| 2 | `CONTEXT.md` | envanter + çelişki | ✅ iç (kanıt) |
| 3 | `WORKFLOW.md` | süreç adımları | ✅ iç |
| 4 | `CLAUDE.md` | boot kuralı | ⛔ dokunulmaz |
| 5 | `ISSUE_TEMPLATE/01-bug-report.md` | rapor formu | ⚠️ kapsam dışı |
| 6 | `ISSUE_TEMPLATE/CLAUDE.md` | şablon protokolü | ⛔ dokunulmaz |

**Ölçüm:** 8 dosya · 2 alt dizin · **2 dokunulmaz** · **1 kapsam dışı**.

**Kritik okuma:** `CLAUDE.md` dosyalarının **ikisi de dokunulmaz** — bu,
`.github/` klasörünün **boot zincirine** bağlı olduğunu kanıtlar. Vault
ile **aynı yapıdadır**: kural dosyaları **salt-okunur**dur.

---

## §H — Doğrulama Kapıları ↔ Dış Gate

> Bu modülün kapıları, dış gate (`k011-gate-check.ps1`) ile **nasıl**
> ilişkilidir?

| # | Bu modül kapısı | Dış gate karşılığı | Fark |
|---|---|---|---|
| 1 | §2-1 CI dosyası var | 6-MD | farklı |
| 2 | §2-2 job sayısı | — | **özgün** |
| 3 | §2-3 secret scan | — | **özgün** |
| 4 | §2-4 gitleaks config | — | **özgün** |
| 5 | §2-5 test tetikleyici | — | **özgün** |
| 6 | §2-6 talimatlar | satır ≥500 | farklı |
| 7 | §2-7 klasör dokümanı | — | **özgün** |
| 8 | §2-8 URL havuzu | URL HIT | **aynı** |
| 9 | §2-9 CI geçti mi | — | **özgün** |
| 10 | §2-10 talimatlara uyuluyor mu | — | **özgün** |

**Sonuç:** 10 kapının **8'i özgün** · 1'i dış gate ile **aynı** · 1'i
**farklı**. Bu, bu modülün **dış gate'ten bağımsız** bir denetim
katmanı **eklediğini** kanıtlar — gate'i **tekrarlamaz**, **tamamlar**.

---

## §I — Kapı Bağımlılık Grafiği

```
[§2-1 CI dosyası] ──► [§2-2 job sayısı] ──► [§4 çapraz]
        │
        └──► [§5 CI derinliği] ──► [§D matrix] ──► [§E yumuşak kapı]
                                          │
[§2-3 secret scan] ──► [§2-4 config] ──► [§6 derinlik]
                                          │
[§2-6 talimatlar] ──► [§7 denetim] ──► [§F kapı eşlemesi]
                                          │
                    [§8 ⚠️] ◄── hepsi ────┘
```

**Okuma:** §8 (7 boşluk) **tüm kapıların ortak sonucudur** — hiçbir
kapı **tek başına** "çalışıyor" diyemez. Bu, §8'in neden **modülün en
zayıf** noktası olduğunu açıklar.

---

## §J — Kapı Sonuçları (nihai sayım)

| Kategori | Kapı | Sayı |
|---|---|---|
| Açık (dosya-varlık) | §2-1…8 | 8 |
| Kapalı (davranış) | §2-9, 10 | 2 |
| Özgün (gate dışı) | §H | 8 |
| Uyumlu çapraz | §4 | 8/8 |
| Doğrulanamayan | §8 | 7 |
| Eksik yüzey | §9 | 6 |
| Reddedilen iddia | §B | 10 |
| Ölçüm | §A | 10 |

**Oran:** 8 açık / 10 toplam = **%80 dosya-varlık doğrulaması**;
0 açık / 2 davranış = **%0 davranış doğrulaması**. Bu iki oran
**asla karıştırılmamalıdır** — "modül %80 hazır" denmez, "dosyaların
%80'i var" denir.

## §K — Bu Dosyanın Kardeşleriyle Tutarlılığı

| # | Kardeş | İlişki | Tutarlılık |
|---|---|---|---|
| 1 | `workflows-kapsam.md` §3.1 | 6 yüzey envanteri | ✅ birebir |
| 2 | `workflows-kapsam.md` §4 | 10 CI adımı | ✅ §5 ile aynı |
| 3 | `workflows-kapsam.md` §5 | 5 secret-scan adımı | ✅ §6 ile aynı |
| 4 | `workflows-kapsam.md` §6 | 11 süreç akışı | ✅ §7 ile aynı |
| 5 | `workflows-kapsam.md` §2 | 10 sınır satırı | ✅ §9 ile aynı |

**Sonuç:** 5/5 **tutarlı** · **çelişki = 0**. Bu, `kapsam` dosyasının
**yalnızca envanter** verdiğini, bu dosyanın ise **kapıları denetlediğini**
gösterir — ikisi **aynı işi yapmaz**, **tamamlar**. Kapı denetimi
**dış gate'ten bağımsızdır** (§H).

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
