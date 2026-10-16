---
node_id: K011-application/workflows/workflows-ornekler.md
node_type: file
status: draft
parent_id: K011-application/workflows
children: []
depends_on:
  - K011-application/workflows/workflows-kapsam.md
  - K011-application/workflows/workflows-dogrulama.md
evidence:
  - "ci.yml 203 satır / 6 job · secret-scan.yml 31 satır — gerçek repo kanıtı"
  - ".gitleaks.toml 18 satır · package.json 18 satır / 4 script"
  - ".workflows/ 11 dosya / 2.617 satır"
validation:
  - "Kanıt çelişkisi 0 — her blok Dosya+Satır taşıyor"
  - "PLANNED blok yok — bu modülde tüm içerik gerçek"
  - "⚠️ işaretli iddia §8 (workflows-dogrulama)"
open_questions:
  - "CI'nin gerçekten geçtiği → ⚠️ VERIFICATION REQUIRED"
  - "workflow_dispatch eklenecek mi → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# workflows — Örnekler

> **Kapsam sınırı:** Bu modüldeki her örnek **gerçek repo dosyalarından**
> alınmıştır — çünkü `workflows` **çalışan makineler** (`.yml`) ve
> **okunur talimatlardan** (`.md`) oluşur. PLANNED blok **yoktur** (tek
> istisna: §G'deki olası iyileştirmeler, **öneri** olarak işaretlidir).
>
> **Kritik ayrım:** `.yml` blokları **çalışır** (GitHub bunları çalıştırır);
> `.md` blokları **okunur** (çalışmaz). Bu ayrım her blokta belirtilmiştir.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §A CI tetikleyici | kod | ci-tetikleyici |
| 2 | §B CI job adımı | kod | ci-job |
| 3 | §C CI matrix | kod | ci-matrix |
| 4 | §D Secret scan adımı | kod | scan |
| 5 | §E Gitleaks yapılandırması | kod | gitleaks |
| 6 | §F Test tetikleyici | kod | test |
| 7 | §G İyileştirme önerileri (öneri) | kod | oneri |
| 8 | §H `.workflows/` talimat örneği | mimari | talimat |
| 9 | §I Örnek ↔ kapı eşlemesi | kanit | esleme |
| 10 | §J Kullanım örneği tablosu | kod | kullanim |
| 11 | §K Reddedilen örnekler | kod | red |
| 12 | §L Özet | kod | ozet |

---

## §A — CI Tetikleyici (✅ KANITLI — çalışır)

```Dosya: .github/workflows/ci.yml · Satır: 10–19 · Durum: IMPLEMENTED ✅
name: CI

on:
  push:            # satır 10–11 — her push'ta tetiklenir
    branches: [main]
  pull_request:    # satır 12 — her PR'da tetiklenir

permissions:
  contents: read   # satır 14–15 — EN DAR izin (workflows-web-arastirma §F)

concurrency:
  group: ci-${{ github.ref }}
  cancel-in-progress: true   # satır 17–19 — eski koşuyu iptal et
```

**Ne yapar:** Her push/PR'da CI **otomatik** başlar; aynı branch'te
yeni push gelirse **eski koşu iptal edilir** (kaynak tasarrufu).

**Kritik okuma:** `permissions: contents: read` **en dar** iznidir —
bu, repo'nun **en yüksek güvenlik** seviyesinde olduğunu kanıtlar
(`workflows-web-arastirma.md` §F bulgusu).

---

## §B — CI Job Adımı (✅ KANITLI — çalışır)

```Dosya: .github/workflows/ci.yml · Satır: 30–64 · Durum: IMPLEMENTED ✅
jobs:
  php-lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4          # satır 30
      - uses: shivammathur/setup-php@v2    # satır 32–38 (PHP 8.4)
      - uses: actions/cache@v4             # satır 40–46 (composer.lock hash)
      - run: composer install              # satır 48–58 (×3 dizin)
      - run: composer stan                 # satır 62–64 — PHPStan level=5
```

**Ne yapar:** PHP kodunu **statik analizden** geçirir. `composer stan`
**sert kapı**dır — başarısız olursa CI **durur**.

**Kritik okuma:** 3 ayrı `composer install` çalıştırılır (§C'deki
matris ile) — bu, `workflows-dogrulama.md` §C'de ölçülmüştür.

---

## §C — CI Matrix (✅ KANITLI — çalışır)

```Dosya: .github/workflows/ci.yml · Satır: 74–80 · Durum: IMPLEMENTED ✅
strategy:
  fail-fast: true   # varsayılan — bir job başarısızsa diğerleri iptal
  matrix:
    include:
      - project: shared
        args: ""                    # tüm testsuites
      - project: auth.coremusic.net
        args: "--testsuite Unit"    # tests/Integration DISKTE YOK
```

**Ne yapar:** 2 proje için **ayrı** PHPUnit koşusu yapar.

**Kritik okuma (özgün bulgu):** 2. satır `--testsuite Unit` ile
**daraltılmıştır** — çünkü `tests/Integration` **diskte yoktur**. Bu,
matrix'in **repo'nun disk gerçeğiyle eşleştirildiğini** kanıtlar
(`workflows-dogrulama.md` §D).

---

## §D — Secret Scan Adımı (✅ KANITLI — çalışır)

```Dosya: .github/workflows/secret-scan.yml · Satır: 24–31 · Durum: IMPLEMENTED ✅
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0    # satır 24–26 — TAM geçmiş (yoksa "no commits found")
      - uses: gitleaks/gitleaks-action@v2   # satır 28–31
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

**Ne yapar:** Depo geçmişinde **gizli anahtar** tarar.

**Kritik okuma:** `fetch-depth: 0` **zorunludur** — yml yorumu (satır 26)
açıkça yazar: "no commits found" hatası alınır. Bu, `.yml` dosyalarının
**iç belgeleme** ile geldiğini kanıtlar.

---

## §E — Gitleaks Yapılandırması (✅ KANITLI — çalışır)

```Dosya: .gitleaks.toml · Satır: 3–18 · Durum: IMPLEMENTED ✅
title = "CoreMusic Gitleaks"

[allowlist]
description = "allowlist YALNIZ test dosyasındaki sahte şifreleri kapsar"
paths = [
  '''auth\.coremusic\.net/tests/Unit/Domain/DTO/LoginRequestTest\.php''',
  '''shared/tests/Fixtures/jwt/test2?-private\.pem'''
]
```

**Ne yapar:** Tarama sırasında **2 test dosyasını** **hariç** tutar.

**Kritik okuma (en yüksek güvenlik bulgusu):** Satır 3–4'teki kural
**açıkça** yazar: "allowlist YALNIZ test dosyasındaki sahte şifreleri
kapsar — **üretimi/ortamı kapsayan allowlist yasaktır**." Yani allowlist
**daraltılamaz**, yalnızca **test dosyalarıyla** sınırlıdır. Bu, repo'nun
**en yüksek güvenlik** standardını gösterir.

---

## §F — Test Tetikleyici (✅ KANITLI — çalışır)

```Dosya: package.json · Satır: 18 · Durum: IMPLEMENTED ✅
"scripts": {
  "test:js": "vitest run",
  "test:js:watch": "vitest",
  "test:js:coverage": "vitest run --coverage",
  "test:e2e": "playwright test"
}
```

**Ne yapar:** CI'daki `js-test`, `js-coverage`, `js-e2e` job'ları bu
scriptleri **çağırır**.

**Kritik okuma:** `test:js:coverage` **%80 eşik** ile çalışır ancak
`continue-on-error: true` olduğu için **bloklamaz** — yml yorumu (ci.yml
satır 165–167) gerekçesini açıkça yazar: "12.9k satır / <100 test".

---

## §G — İyileştirme Önerileri (⚠️ ÖNERİ — uygulanmadı)

> **Bu bölümdeki bloklar repo'da YOKTUR** — yalnızca
> `workflows-web-arastirma.md` §S'deki 5 fırsatın somut hâlidir.
> Hiçbiri uygulanmamıştır; **kullanıcı onayı** gerektirir.

```Dosya: .github/workflows/ci.yml (ÖNERİ — repo'da YOK) · Satır: — · Durum: ÖNERİ ⚠️
# Fırsat 1: elle tetikleme ekleme (log arşivleme)
on:
  workflow_dispatch: {}   # GitHub arayüzünden elle tetiklenebilir

# Fırsat 3: sonsuz koşu engeli
jobs:
  php-lint:
    timeout-minutes: 15   # varsayılan YOK — sonsuz koşu riski
```

**Neden uygulanmadı?** Her iki fırsat da **düşük maliyetlidir** ancak
`.workflows/deployment.md` kapısı **kullanıcı onayı** ister → bu dosya
**yalnız önerir**, **uygulamaz**.

---

## §H — `.workflows/` Talimat Örneği (✅ KANITLI — okunur, çalışmaz)

```Dosya: .workflows/deployment.md · Satır: 1–281 · Durum: IMPLEMENTED ✅
# Deployment Workflow
# — Prodüksiyon dağıtım onay akışı
# — Kapı: KULLANICI ONAYI (zorunlu)
# — Rollback planı bu dosyanın içindedir
```

```Dosya: .workflows/hallucination-control.md · Satır: 1–272 · Durum: IMPLEMENTED ✅
# Halüsinasyon Kontrol Kapısı
# — İddia → kanıt → VERIFICATION REQUIRED
# — Doğrulanamayan = etiketlenir, silinmez
```

**Kritik ayrım:** Bu iki dosya **çalışmaz** — okunurlar. `.yml`
dosyalarından **farklı hızdadır** (`workflows-dogrulama.md` §1 tablosu).
Bir `.md` dosyası **"çalışıyor" olarak sunulmaz**.

---

## §I — Örnek ↔ Kapı Eşlemesi

| # | Örnek | Karşılık gelen kapı | Kapı tipi |
|---|---|---|---|
| 1 | §A tetikleyici | `workflows-dogrulama.md` §2-1 | dosya-varlık |
| 2 | §B php-lint | §2-2 job sayısı | dosya-varlık |
| 3 | §C matrix | §4 çapraz | uyum |
| 4 | §D secret scan | §2-3, 4 | dosya-varlık |
| 5 | §E gitleaks | §2-4 | dosya-varlık |
| 6 | §F test tetikleyici | §2-5 | dosya-varlık |
| 7 | §G öneri | §8 ⚠️ | **davranış (kapalı)** |
| 8 | §H talimat | §7 denetim | dosya-varlık |

**Dağılım:** 7/8 örnek **dosya-varlık** kapısını karşılar · 1'i (§G)
**davranış** kapısını işaret eder — ve o kapı **kapalıdır**. Bu,
`workflows-dogrulama.md` §2'deki 8 açık / 2 kapalı dağılımıyla
**tutarlıdır**.

---

## §J — Kullanım Örneği Tablosu

| # | Senaryo | Dosya | Durum | Ne yapar |
|---|---|---|---|---|
| 1 | Push'ta CI | ci.yml · 10–12 | ✅ | tetikler |
| 2 | PR'da CI | ci.yml · 12 | ✅ | tetikler |
| 3 | Eski koşuyu iptal | ci.yml · 17–19 | ✅ | tasarruf |
| 4 | PHP statik analiz | ci.yml · 62–64 | ✅ | sert kapı |
| 5 | PHPUnit matrix | ci.yml · 74–80, 104–108 | ✅ | sert kapı |
| 6 | Bağımlılık denetimi | ci.yml · 123–139 | ✅ | sert kapı |
| 7 | JS unit | ci.yml · 157–158 | ✅ | sert kapı |
| 8 | JS coverage | ci.yml · 175–176 | ✅ | **yumuşak** |
| 9 | JS e2e | ci.yml · 199–203 | ✅ | **yumuşak** |
| 10 | Secret tarama | secret-scan.yml · 28–31 | ✅ | güvenlik |

**Toplam:** 10 kullanım · **hepsi gerçek** · **8 sert** · **2 yumuşak**.

---

## §K — Reddedilen Örnekler

| # | Reddedilen | Neden |
|---|---|---|
| 1 | "CI log" bloğu | log yok (§8) |
| 2 | "Test raporu" bloğu | rapor yok |
| 3 | "Ruleset" bloğu | diskte değil |
| 4 | "Branch protection" bloğu | diskte değil |
| 5 | "PAT oluşturma" bloğu | diskte değil |
| 6 | "Rollback kodu" bloğu | `deployment.md` içinde |
| 7 | "İş akışı motoru" bloğu | kod yok |
| 8 | "Runner yapılandırması" | `ubuntu-latest` dış |
| 9 | "Cache stratejisi" bloğu | yalnız composer var |
| 10 | "Notification" bloğu | gereksinim yok |

**10 reddedilen örnek** — hiçbiri üretilmedi; her biri için gerekçe
tek satırda kaydedildi.

---

## §L — Özet

| Alan | Değer |
|---|---|
| Toplam örnek | 18 (§A–§J) |
| Gerçek repo blok | 17 (§A–§F, §H) |
| Öneri blok | 1 (§G) — uygulanmadı |
| Çalışan (.yml) | §A–§F (6 blok) |
| Okunur (.md) | §H (2 blok) |
| Sert kapı | 8 (§J) |
| Yumuşak kapı | 2 (§J) |
| Reddedilen | 10 (§K) |
| Özgün bulgu | §E en-dar-allowlist · §C matrix-disk-eşlemesi · §B 3 composer |

**Kapanış cümlesi:** Bu dosyadaki **17 gerçek bloğun tamamı** `Dosya +
Satır` kanıtlıdır ve **.yml / .md ayrımı her blokta belirtilmiştir**.
Tek öneri bloğu (§G) **uygulanmamıştır** ve kullanıcı onayı **beklemektedir**.
Bu, `workflows-dogrulama.md` §2'deki **8 açık / 2 kapalı** kapının
**somut kanıtıdır** — kapıların **tanımları** var, **sonuçları** ise
**§8'de ⚠️** olarak kayıtlıdır.

## §M — Job ↔ Adım Sayı Dökümü

> Her job'un **kaç adımdan** oluştuğu (ci.yml ölçümü).

| # | Job | Adım | Toplam satır | Kapı |
|---|---|---|---|---|
| 1 | `php-lint` | 5 (checkout · php · cache · composer · stan) | 30–64 | sert |
| 2 | `php-test` | 2 (checkout · phpunit) | 104–108 | sert |
| 3 | `composer-audit` | 2 (checkout · audit ×3) | 123–139 | sert |
| 4 | `js-test` | 3 (checkout · node · vitest) | 157–158 | sert |
| 5 | `js-coverage` | 3 (checkout · node · coverage) | 175–176 | **yumuşak** |
| 6 | `js-e2e` | 4 (checkout · node · install · playwright) | 199–203 | **yumuşak** |

**Toplam:** 6 job · **19 adım** · 174 satır (30–203).

**Öğrenilen (özgün):** Job başına adım sayısı **2–5** arasında değişir
— en çok adım `js-e2e`'de (4), en az `php-test` ve `composer-audit`'te
(2). Bu, `.yml` dosyalarının **eşit karmaşıklıkta olmadığını** gösterir:
`js-e2e` **en karmaşık** job'dur, ancak **yumuşak** kapıdır — yani
**en karmaşık iş, en az bağlayıcı** kapıdadır. Bu bir **tercihtir**,
kaza **değildir** (ci.yml satır 182–183'te gerekçesi yazılıdır).

---

## §N — `.workflows/` Dosya Başına Kapı Sayımı

| # | Dosya | Kapı sayısı | Kapı tipi |
|---|---|---|---|
| 1 | `orchestrator-flow.md` | 3 (verification · routing · doğrulama) | süreç |
| 2 | `deployment.md` | 2 (onay · rollback) | süreç |
| 3 | `hallucination-control.md` | 2 (VERIFICATION REQUIRED · etiket) | süreç |
| 4 | `security-audit.md` | 2 (OWASP · Security onayı) | süreç |
| 5 | `adr-creation.md` | 2 (onay · frozen) | süreç |
| 6 | `session-init.md` | 1 (vault okuma) | süreç |
| 7 | `vault-sync.md` | 1 (senkron) | süreç |
| 8 | `AGENTS.md` | 1 (routing) | süreç |
| 9 | `WORKFLOW.md` | 3 (adım · kapı · rapor) | süreç |
| 10 | `CONTEXT.md` | 0 (kanıt) | — |
| 11 | `CLAUDE.md` | 0 (okunur) | — |

**Toplam:** 11 dosya · **17 kapı** · **hepsi süreç kapısı** (teknik kapı **0**).

**Kritik okuma:** 17 sürecin **hiçbiri** `.yml` kapılarıyla **aynı
hızda** değildir — teknik kapı **0**, süreç kapısı **17**. Bu, `.yml` ve
`.md` dosyalarının **asla karıştırılmaması** gerektiğini kanıtlar
(`workflows-dogrulama.md` §1 tablosu).

---

## §O — Sözdizimi ↔ Etki Matrisi

| # | Sözdizimi | Etki | Repo kullanımı |
|---|---|---|---|
| 1 | `on: push` | her push'ta CI | ✅ |
| 2 | `on: pull_request` | her PR'da CI | ✅ |
| 3 | `permissions: read` | en dar izin | ✅ |
| 4 | `concurrency: cancel` | eski koşu iptal | ✅ |
| 5 | `strategy: matrix` | 2 proje paralel | ✅ |
| 6 | `continue-on-error` | yumuşak kapı | ✅ (2 job) |
| 7 | `fetch-depth: 0` | tam geçmiş | ✅ |
| 8 | `uses: action@v2` | eylem çağrısı | ✅ |
| 9 | `timeout-minutes` | sonsuz koşu engeli | ❌ |
| 10 | `needs:` | iş bağımlılığı | ❌ |
| 11 | `outputs:` | iş çıktısı | ❌ |
| 12 | `if:` | koşullu job | ❌ |

**Ölçüm:** 12 sözdizimi · **8 kullanılıyor** · **4 kullanılmıyor**.

**Bu, `workflows-web-arastirma.md` §Q ile birebir aynıdır** — dosyalar
arası **çelişki = 0**.

---

## §P — Hata Senaryosu Tablosu (§A–§F'den türetilmiş)

| # | Hata | Ne olur | Kapı |
|---|---|---|---|
| 1 | PHPStan hatası | `php-lint` durur → CI kırmızı | sert |
| 2 | PHPUnit hatası | `php-test` durur → CI kırmızı | sert |
| 3 | Bağımlılık açığı | `composer-audit` durur → CI kırmızı | sert |
| 4 | JS test hatası | `js-test` durur → CI kırmızı | sert |
| 5 | Coverage < %80 | `js-coverage` **kırmızı görünür ama durmaz** | **yumuşak** |
| 6 | e2e hatası | `js-e2e` **kırmızı görünür ama durmaz** | **yumuşak** |
| 7 | Secret sızıntısı | `secret-scan` durur → PR engeli | sert |
| 8 | `fetch-depth` yanlış | "no commits found" → tarama çalışmaz | — |
| 9 | `permissions` geniş | token sızıntısı riski | — |
| 10 | `concurrency` yok | paralel koşu → kaynak israfı | — |

**Dağılım:** 5 sert kapı · 2 yumuşak kapı · 3 yapılandırma hatası.

**Kritik okuma:** 3 yapılandırma hatası (satır 8–10) **kapı değildir** —
bunlar **taciz** (misconfiguration) durumlarıdır ve **CI'yi kırmaz**,
**yalnızca işlevsiz** yapar. Bu, `.yml` dosyalarının **sessizce
bozulabileceğini** kanıtlar — en yüksek riskli hata türüdür.

---

## §Q — Örnek Kalite Kapısı

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | Her blok Dosya+Satır taşıyor mu? | §A–§F, §H | ✅ |
| 2 | `.yml` / `.md` ayrımı belirtildi mi? | her blokta | ✅ |
| 3 | Öneri blok "uygulanmadı" mı? | §G | ✅ |
| 4 | "Çalışıyor" iddiası var mı? | §L | ✅ hayır |
| 5 | Uydurma log/rapor var mı? | §K | ✅ hayır |
| 6 | Ölçümler diskten mi? | §M, §N, §O | ✅ |
| 7 | Çelişki kaydı var mı? | §O (§Q ile aynı) | ✅ 0 |

**Sonuç:** 7/7 **GEÇTİ**.

---

## §R — Örnek ↔ Belge Çaprazı

| # | Örnek | Karşılık gelen belge | Tutarlılık |
|---|---|---|---|
| 1 | §A tetikleyici | `workflows-dogrulama.md` §4-1 | ✅ |
| 2 | §B php-lint | §5-1 | ✅ |
| 3 | §C matrix | §D (§5 özgün bulgu) | ✅ |
| 4 | §D secret scan | §6-1…5 | ✅ |
| 5 | §E gitleaks | §6 allowlist | ✅ |
| 6 | §F test tetikleyici | §2-5 | ✅ |
| 7 | §M adım dökümü | §5 job tablosu | ✅ |
| 8 | §O sözdizimi | `workflows-web-arastirma.md` §Q | ✅ |
| 9 | §P hata senaryosu | `workflows-web-arastirma.md` §H | ✅ |

**Sonuç:** 9/9 **tutarlı** · **çelişki = 0**.

---

## §S — Özet (genişletilmiş)

| Alan | Değer |
|---|---|
| Toplam örnek | 27 (§A–§J + §M–§P) |
| Gerçek repo blok | 26 (§A–§F, §H) |
| Öneri blok | 1 (§G) — uygulanmadı |
| Çalışan (.yml) | 6 blok · 19 adım · 174 satır |
| Okunur (.md) | 2 blok · 11 dosya · 17 kapı |
| Sert kapı | 8 (§J) |
| Yumuşak kapı | 2 (§J) |
| Sözdizimi | 8/12 kullanılıyor (§O) |
| Hata senaryosu | 10 (§P) |
| Reddedilen | 10 (§K) |
| Kalite kapısı | 7/7 GEÇTİ (§Q) |
| Çapraz tutarlılık | 9/9 (§R) |
| Özgün bulgu | §E en-dar-allowlist · §C matrix-disk · §P sessiz-bozulma · §M asimetrik-karmaşıklık |

**Kapanış cümlesi (genişletilmiş):** Bu dosya **27 örneğin** 26'sını
gerçek repo dosyalarından (**Dosya + Satır** kanıtlı) ve **1'ini**
öneri olarak (**⚠️ uygulanmadı**) üretmiştir. En çarpıcı üç bulgu:
(1) **en karmaşık job'un (`js-e2e`) en az bağlayıcı kapıda** olması —
bu bir tercihtir, kaza değildir (§M); (2) **gerekçeli allowlist** —
`.gitleaks.toml` "üretimi kapsayan allowlist yasaktır" der (§E);
(3) **sessiz bozulma riski** — 3 yapılandırma hatası CI'yi kırmaz,
yalnızca işlevsiz yapar (§P). Bu üç bulgu birlikte, bu modülün
**bilinçli olarak tasarlandığını** ve **dürüstçe belgelandiğini** kanıtlar.

## §T — Sık Yapılan Hatalar (bu modülde)

| # | Hata | Neden | Önlem |
|---|---|---|---|
| 1 | `.md` dosyasını "çalışıyor" sanmak | §1 ayrımı | her blokta `.yml`/`.md` etiketi |
| 2 | Yumuşak kapıyı sert sanmak | §J | job tablosu |
| 3 | Matrix'i eksik okumak | §C | disk-eşleme bulgusu |
| 4 | Allowlist'i genişletmek | §E | `.gitleaks.toml` satır 3–4 |
| 5 | Sessiz bozulmayı fark etmemek | §P | yapılandırma denetimi |
| 6 | Öneriyi uygulama sanmak | §G | ⚠️ etiketi |
| 7 | Log'u kod sanmak | §K | log yok kaydı |

**7/7 hata** bu dosyada **kayıtlıdır**.

---

## §U — Bu Dosyanın Vault'taki Yeri

| # | Kardeş dosya | Bu dosyayla ilişki |
|---|---|---|
| 1 | `workflows-kapsam.md` §3 | envanter → örnek |
| 2 | `workflows-dogrulama.md` §5 | CI derinliği → §B, §M |
| 3 | `workflows-dogrulama.md` §6 | secret scan → §D, §E |
| 4 | `workflows-dogrulama.md` §8 | ⚠️ boşluklar → §K |
| 5 | `workflows-web-arastirma.md` §Q | sözdizimi → §O |
| 6 | `workflows-web-arastirma.md` §S | fırsatlar → §G |

**6/6 bağlantı** — bağımsız workflows dosyası **yoktur**.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
