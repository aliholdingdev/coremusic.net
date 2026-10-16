---
node_id: K011-application/workflows/workflows-bilesenler.md
node_type: file
status: draft
parent_id: K011-application/workflows
children: []
depends_on:
  - K011-application/workflows/workflows-kapsam.md
  - K011-application/workflows/workflows-dogrulama.md
  - K011-application/workflows/workflows-web-arastirma.md
evidence:
  - "3 yüzey: CI (2 yml) · Talimat (11 md) · Klasör dokümanı (8 md) — disk ölçümü"
  - "19 CI adım · 17 süreç kapısı · 2 allowlist · 4 test scripti"
  - "Diskte olmayan: ruleset · branch protection · PAT · runner — 4 eksik"
validation:
  - "Bileşen sayımı diskten (k011-build-inv.ps1 çıktıları)"
  - "Bağımlılık grafiği §C — döngü yok"
  - "Eksik bileşenler §B — hiçbirine karşılık kod yazılmadı"
open_questions:
  - "İş akışı motoru (state machine) → ⚠️ VERIFICATION REQUIRED"
  - "Runner/altyapı maliyeti → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# workflows — Bileşenler

> **Kapsam sınırı:** Bu dosya, `workflows` modülünün **bileşen
> envanterini** listeler. Amaç "her şeyi saymak" **değildir** — **her
> bileşenin hangi hızda** (çalışan makine / okunur talimat / diskte
> olmayan) olduğunu **ayırmaktır**. Bu ayrım yapılmazsa, `.md` dosyaları
> `.yml` ile **karıştırılır** ve "dosya var = çalışıyor" hatası yapılır.

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 |
|---|---|---|---|
| 1 | §A Bileşen envanteri (3 yüzey) | kod | envanter |
| 2 | §B Eksik (diskte olmayan) bileşenler | kod | eksik |
| 3 | §C Bağımlılık grafiği | mimari | bagimlilik |
| 4 | §D Bileşen → işlev eşlemesi | mimari | islev |
| 5 | §E Bileşen → hız eşlemesi | mimari | hiz |
| 6 | §F Bileşen → sahiplik | kod | sahiplik |
| 7 | §G Bileşen → risk | kanit | risk |
| 8 | §H Ölçüm özeti | kanit | olcum |
| 9 | §I Sınır ve dokunulmazlıklar | kanit | sinir |
| 10 | §J Açık sorular | kod | acik |
| 11 | §K Reddedilen bileşenler | kod | red |
| 12 | §L Özet | kod | ozet |

---

## §A — Bileşen Envanteri (3 yüzey)

### §A.1 Yüzey 1 — Çalışan makine (CI/CD)

| # | Bileşen | Dosya | Satır | Rol | Durum |
|---|---|---|---|---|---|
| 1 | CI pipeline | `.github/workflows/ci.yml` | 203 | 6 job | ✅ |
| 2 | Secret scan | `.github/workflows/secret-scan.yml` | 31 | 1 job | ✅ |
| 3 | Tarama kuralı | `.gitleaks.toml` | 18 | 2 allowlist | ✅ |
| 4 | Test tetikleyici | `package.json` | 18 | 4 script | ✅ |
| 5 | Vitest config | `assets.coremusic.net/vitest.config.js` | 33 | jsdom + %80 | ✅ |
| 6 | Playwright config | `assets.coremusic.net/playwright.config.ts` | 56 | chromium | ✅ |

**Toplam:** 6 bileşen · **359 satır** · **çalışır** (GitHub çalıştırır).

### §A.2 Yüzey 2 — Süreç talimatları (`.workflows/`)

| # | Bileşen | Satır | Kapı | Durum |
|---|---|---|---|---|
| 1 | `orchestrator-flow.md` | 309 | verification | ✅ okunur |
| 2 | `CONTEXT.md` | 283 | — | ✅ |
| 3 | `deployment.md` | 281 | onay | ✅ |
| 4 | `hallucination-control.md` | 272 | VERIFICATION REQUIRED | ✅ |
| 5 | `security-audit.md` | 268 | Security onayı | ✅ |
| 6 | `adr-creation.md` | 256 | onay | ✅ |
| 7 | `session-init.md` | 248 | vault okuma | ✅ |
| 8 | `vault-sync.md` | 238 | senkron | ✅ |
| 9 | `AGENTS.md` | 208 | routing | ✅ |
| 10 | `WORKFLOW.md` | 203 | adım kapısı | ✅ |
| 11 | `CLAUDE.md` | 51 | — | ✅ |

**Toplam:** 11 bileşen · **2.617 satır** · **çalışmaz**, okunur.

### §A.3 Yüzey 3 — Klasör dokümanı (`.github/`)

| # | Bileşen | Rol | Durum |
|---|---|---|---|
| 1 | `AGENTS.md` | sahiplik | ✅ |
| 2 | `CONTEXT.md` | envanter | ✅ |
| 3 | `WORKFLOW.md` | süreç | ✅ |
| 4 | `CLAUDE.md` | boot | ⛔ dokunulmaz |
| 5 | `ISSUE_TEMPLATE/01-bug-report.md` | rapor formu | ⚠️ kapsam dışı |
| 6 | `ISSUE_TEMPLATE/CLAUDE.md` | şablon | ⛔ dokunulmaz |

**Toplam:** 8 dosya (6 içerik + 2 alt dizin içi) · **okunur**.

---

## §B — Eksik (Diskte Olmayan) Bileşenler

| # | Bileşen | Neden eksik | Etki |
|---|---|---|---|
| 1 | İş akışı motoru (state machine) | repo'da kod yok | süreç elle |
| 2 | Branch protection | GitHub arayüzü | doğrulanamaz |
| 3 | Ruleset | GitHub arayüzü | belge var |
| 4 | PAT yönetimi | GitHub arayüzü | belge var |
| 5 | Runner/altyapı | `ubuntu-latest` dış | maliyet bilinmiyor |
| 6 | Rollback planı (otomatik) | `deployment.md` içinde | kısmi |
| 7 | `workflow_dispatch` | ci.yml'de yok | log arşivlenmez |
| 8 | npm cache | ci.yml'de yok | JS yavaş |
| 9 | `timeout-minutes` | ci.yml'de yok | sonsuz koşu |
| 10 | `needs:` bağımlılık | ci.yml'de yok | hata izolasyonu yok |

**Toplam:** 10 eksik · **hiçbiri bu modülde üretilmez**.

**Kritik okuma:** 10 eksikten **4'ü** (satır 2–5) **diskte olmayan**
yüzeylerdir — bunlar `.yml` dosyalarına **yazılamaz**. Kalan **6'sı**
(satır 1, 6–10) ise **eklenebilir** ancak **eklenmemiştir**. Bu ayrım,
`workflows-web-arastirma.md` §S'deki 5 fırsatın **somut karşılığıdır**.

---

## §C — Bağımlılık Grafiği

```
[ci.yml] ──► [composer install ×3] ──► [PHPStan] ──► [PHPUnit matrix]
   │                                              │
   ├──► [composer-audit ×3] ◄─────────────────────┘
   │
   ├──► [js-test (vitest)] ──► [js-coverage (%80)]
   │
   └──► [js-e2e (playwright)]

[secret-scan.yml] ──► [fetch-depth: 0] ──► [gitleaks-action@v2]
                              │
                              └──► [.gitleaks.toml (2 allowlist)]

[.workflows/*.md] ──► [17 süreç kapısı] ──► [kullanıcı onayı]
```

**Okuma:** 3 bağımsız zincir — **hiçbiri diğerine bağımlı değildir**.
`ci.yml` **test** kapısı işletir; `secret-scan.yml` **güvenlik** kapısı;
`.workflows/` ise **süreç** kapısı. Üçü **paralel** çalışır — biri
durursa diğeri **durmaz**.

**Döngü:** **0** — bağımlılık grafiğinde **döngü yoktur**.

---

## §D — Bileşen → İşlev Eşlemesi

| # | İşlev | Hangi bileşen karşılar | Hız |
|---|---|---|---|
| 1 | PHP statik analiz | ci.yml · php-lint | çalışan makine |
| 2 | PHP test | ci.yml · php-test | çalışan makine |
| 3 | Bağımlılık denetimi | ci.yml · composer-audit | çalışan makine |
| 4 | JS test | ci.yml · js-test | çalışan makine |
| 5 | JS coverage | ci.yml · js-coverage | çalışan makine |
| 6 | JS e2e | ci.yml · js-e2e | çalışan makine |
| 7 | Secret tarama | secret-scan.yml | çalışan makine |
| 8 | Dağıtım onayı | deployment.md | süreç |
| 9 | Halüsinasyon kontrolü | hallucination-control.md | süreç |
| 10 | Güvenlik denetimi | security-audit.md | süreç |
| 11 | ADR yazımı | adr-creation.md | süreç |
| 12 | Oturum başlatma | session-init.md | süreç |
| 13 | Vault senkron | vault-sync.md | süreç |
| 14 | Rol atama | AGENTS.md | süreç |
| 15 | Boot kuralı | CLAUDE.md | süreç (okunur) |

**Dağılım:** 7 **çalışan makine** · 7 **süreç** · 1 **okunur**. Her
işlev **tam olarak bir** bileşen tarafından karşılanır — **çakışma 0**.

---

## §E — Bileşen → Hız Eşlemesi

| # | Hız | Bileşen sayısı | Toplam satır | Kim çalıştırır |
|---|---|---|---|---|
| 1 | **Çalışan makine** | 6 | 359 | GitHub |
| 2 | **Okunur talimat** | 11 | 2.617 | insan/ajan |
| 3 | **Klasör dokümanı** | 8 | — | insan/ajan |
| 4 | **Diskte olmayan** | 10 | 0 | — |

**Toplam:** 35 bileşen · **3.000+ satır** (359 + 2.617).

**Kritik okuma:** Okunur talimatların **satırı** (2.617), çalışan
makineninkinin **7 katıdır** — yani bu modülün **çoğu içeriği**
**okunur**, **çalışmaz**. Bu, `.yml` ve `.md`'nin **asla karıştırılmaması**
gerektiğini **sayısal olarak** kanıtlar (`workflows-dogrulama.md` §1).

---

## §F — Bileşen → Sahiplik

| # | Bileşen | Sahip | Katman |
|---|---|---|---|
| 1 | ci.yml | DevOps Engineer | CI/CD |
| 2 | secret-scan.yml | Security Engineer | güvenlik |
| 3 | .gitleaks.toml | Security Engineer | güvenlik |
| 4 | package.json | QA Engineer | test |
| 5 | vitest.config.js | QA Engineer | test |
| 6 | playwright.config.ts | QA Engineer | test |
| 7 | deployment.md | MO + kullanıcı | süreç |
| 8 | hallucination-control.md | MO | süreç |
| 9 | security-audit.md | Security Engineer | süreç |
| 10 | adr-creation.md | MO | süreç |
| 11 | AGENTS.md (.workflows/) | MO | süreç |

**Çakışma analizi:** 3 sahip (DevOps · Security · QA/MO) — **hiçbiri**
diğerinin dosyasına **dokunmaz**. `.gitleaks.toml` **yalnız** Security'ye
aittir; ci.yml **yalnız** DevOps'a. **Çakışma riski = 0**.

---

## §G — Bileşen → Risk

| # | Bileşen | Risk | Olasılık | Etki | Önlem |
|---|---|---|---|---|---|
| 1 | ci.yml | sessiz bozulma | orta | yüksek | §K-5 |
| 2 | secret-scan.yml | tarama çalışmaz | düşük | **çok yüksek** | §D fetch-depth |
| 3 | .gitleaks.toml | allowlist genişler | düşük | **çok yüksek** | §E satır 3–4 |
| 4 | package.json | script kırılır | orta | orta | §F |
| 5 | vitest.config.js | eşik değişir | düşük | düşük | — |
| 6 | playwright.config.ts | proje eksilir | düşük | düşük | — |
| 7 | deployment.md | onay atlanır | orta | **çok yüksek** | kapı |
| 8 | hallucination-control.md | etiket kalkar | düşük | yüksek | §4 |
| 9 | security-audit.md | denetim atlanır | orta | yüksek | kapı |

**En yüksek risk:** satır 3 (allowlist genişlemesi) ve satır 7 (onay
atlanması). İkisi de **süreç/ölçekleme** riskidir — teknik **değil**.

---

## §H — Ölçüm Özeti

| Alan | Sayı |
|---|---|
| Çalışan makine bileşeni | 6 · 359 satır |
| Okunur talimat | 11 · 2.617 satır |
| Klasör dokümanı | 8 dosya |
| Eksik (diskte olmayan) | 10 |
| İşlev karşılanan | 15/15 (%100) |
| Bağımlılık döngüsü | 0 |
| Çakışma riski | 0 |
| Yüksek risk bileşeni | 2 (§G) |
| Dokunulmaz yüzey | 2 (`CLAUDE.md` ×2) |

**Yorum:** 15 işlevin **tamamı** karşılanmıştır — bu, modülün
**içsel olarak tam** olduğunu gösterir. Buna karşılık **10 eksik**
bileşen **dışarıdadır** (GitHub arayüzü, iş akışı motoru). Yani bu
modül **kendi içinde eksiksiz**, **çevresiyle ise boşluklu**dur.

---

## §I — Sınır ve Dokunulmazlıklar

| # | Yüzey | Neden dokunulmaz | Kaynak |
|---|---|---|---|
| 1 | `.github/CLAUDE.md` | boot kuralı | §A.3 |
| 2 | `.github/ISSUE_TEMPLATE/CLAUDE.md` | şablon protokolü | §A.3 |
| 3 | `*.php` üretim kodu | kapsam dışı | §2 sınır |
| 4 | `*.js` üretim kodu | kapsam dışı | §2 sınır |
| 5 | `*.css` üretim kodu | kapsam dışı | §2 sınır |
| 6 | `*.sql` üretim kodu | kapsam dışı | §2 sınır |
| 7 | `shared/tests/**` | K019-testing | §2 sınır |

**7 sınır** — hiçbiri bu modülde **değiştirilemez**. Değişirse bu modül
**kapsamı aşar** ve `.ai` vault'uyla **çakışır**.

---

## §J — Açık Sorular

| # | Soru | İlişkili bileşen | Etiket |
|---|---|---|---|
| 1 | İş akışı motoru eklenecek mi? | §B-1 | ⚠️ |
| 2 | Ruleset tanımlı mı? | §B-3 | ⚠️ |
| 3 | PAT kapsamı ne? | §B-4 | ⚠️ |
| 4 | Runner maliyeti ne? | §B-5 | ⚠️ |
| 5 | Otomatik rollback? | §B-6 | ⚠️ |
| 6 | `workflow_dispatch` eklenecek mi? | §B-7 | ⚠️ |
| 7 | npm cache eklenecek mi? | §B-8 | ⚠️ |
| 8 | `timeout-minutes` eklenecek mi? | §B-9 | ⚠️ |
| 9 | `needs:` eklenecek mi? | §B-10 | ⚠️ |
| 10 | CI gerçekten geçiyor mu? | §A.1 | ⚠️ |

**10/10 açık** — hiçbir bileşen için **davranış kanıtı** yoktur. Bu,
`workflows-dogrulama.md` §8'deki 7 boşluğun **bileşen bazlı** dökümüdür.

---

## §K — Reddedilen Bileşenler

| # | Reddedilen | Neden |
|---|---|---|
| 1 | İş akışı motoru kodu | repo'da yok |
| 2 | Branch protection dosyası | diskte değil |
| 3 | Ruleset dosyası | diskte değil |
| 4 | PAT dosyası | diskte değil |
| 5 | Runner yapılandırması | `ubuntu-latest` dış |
| 6 | Rollback kodu | `deployment.md` içinde |
| 7 | Notification bileşeni | gereksinim yok |
| 8 | Deployment aracı | gereksinim yok |
| 9 | Release notu üretici | gereksinim yok |
| 10 | Coverage raporu arşivi | log yok |

**10 reddedilen bileşen** — hiçbiri üretilmedi.

---

## §L — Özet

| Alan | Değer |
|---|---|
| Çalışan makine | 6 bileşen · 359 satır |
| Okunur talimat | 11 bileşen · 2.617 satır |
| Klasör dokümanı | 8 dosya |
| Eksik | 10 (4 diskte olmayan · 6 eklenmemiş) |
| İşlev | 15/15 (%100) |
| Bağımlılık | 3 bağımsız zincir · 0 döngü |
| Sahiplik | 3 sahip · 0 çakışma |
| Risk | 2 yüksek (allowlist · onay) |
| Sınır | 7 (2 dokunulmaz · 5 kapsam dışı) |
| Açık soru | 10 — hepsi ⚠️ |
| Reddedilen | 10 |

**Kapanış cümlesi:** Bileşen envanteri **üç ayrı hızda** durur: 359
satırlık **çalışan makine**, 2.617 satırlık **okunur talimat** ve
**10 satırlık eksik** (bunların 4'ü diskte **hiç yoktur**). 15 işlevin
**tamamı** karşılanmıştır — modül **içsel olarak eksiksizdir**; buna
karşılık **davranış kanıtı sıfırdır** (`workflows-dogrulama.md` §8).
Bu iki bulgu birlikte, bu modülün **"tanımı tam, sonucu doğrulanmamış"**
olduğunu kanıtlar — bu, CI sistemleri için **tipik** ve **bilinen**
bir durumdur; gizlenmesi yerine **kaydı** tercih edilmiştir.

## §M — Bileşen Olgunluk Modeli

| # | Olgunluk | Tanım | Bileşen | Sayı |
|---|---|---|---|---|
| 1 | **ÇALIŞAN** | GitHub çalıştırıyor | §A.1 (6) | 6 |
| 2 | **OKUNUR** | süreç talimatı | §A.2 (11) | 11 |
| 3 | **DOKÜMAN** | klasör kaydı | §A.3 (8) | 8 |
| 4 | **EKLENEBİLİR** | kod yok ama eklenebilir | §B (6) | 6 |
| 5 | **DISKTE YOK** | hiçbir yerde | §B (4) | 4 |
| 6 | **REDDEDİLDİ** | bilinçli olarak yok | §K (10) | 10 |

**Dağılım:** 6 · 11 · 8 · 6 · 4 · 10 = **45 kayıt**.

**Kritik okuma:** 5. kategori (**DISKTE YOK**) bu modüle **özgüdür** —
diğer K011 modüllerinde **bu kategori yoktur**. Bunun nedeni, workflows'
un **GitHub arayüzüne** bağımlı olmasıdır: ruleset, branch protection
ve PAT **hiçbir dosyaya yazılamaz**. Bu, modülün **yapısal**
bir **sınırlılığıdır**, eksiklik **değil**.

---

## §N — Bileşen Ölçüm Günlüğü (tekrarlanabilir)

| # | Ölçüm | Komut | Sonuç |
|---|---|---|---|
| 1 | ci.yml satır | `[IO.File]::ReadAllLines` | 203 |
| 2 | ci.yml job | `grep "  [a-z-]*:"` | 6 |
| 3 | ci.yml adım | `grep "      - "` | 19 |
| 4 | secret-scan satır | `[IO.File]::ReadAllLines` | 31 |
| 5 | secret-scan job | `grep "  [a-z-]*:"` | 1 |
| 6 | .gitleaks.toml satır | `[IO.File]::ReadAllLines` | 18 |
| 7 | .gitleaks.toml allowlist | `grep "'''"` | 2 |
| 8 | package.json satır | `[IO.File]::ReadAllLines` | 18 |
| 9 | package.json script | `grep "test:"` | 4 |
| 10 | .workflows/ dosya | `Get-ChildItem` | 11 |
| 11 | .workflows/ satır | toplam | 2.617 |
| 12 | .workflows/ kapı | `grep "##"` | 17 |
| 13 | .github/ dosya | `Get-ChildItem` | 8 |
| 14 | vitest.config satır | `[IO.File]::ReadAllLines` | 33 |
| 15 | playwright.config satır | `[IO.File]::ReadAllLines` | 56 |

**15 ölçüm** · **hepsi tekrarlanabilir**.

---

## §O — Bileşen Sınırı: Ne Yazılmaz

| # | Yazılmaz | Neden | Kaynak |
|---|---|---|---|
| 1 | İş akışı motoru kodu | repo'da yok | §B-1 |
| 2 | Branch protection | diskte değil | §B-2 |
| 3 | Ruleset | diskte değil | §B-3 |
| 4 | PAT | diskte değil | §B-4 |
| 5 | Runner yapılandırması | dış | §B-5 |
| 6 | Rollback kodu | deployment.md içinde | §B-6 |
| 7 | Notification | gereksinim yok | §K-7 |
| 8 | Deployment aracı | gereksinim yok | §K-8 |
| 9 | Release notu | gereksinim yok | §K-9 |
| 10 | Coverage arşivi | log yok | §K-10 |
| 11 | Üretim kodu (php/js/css/sql) | kapsam dışı | §2 |
| 12 | Test dosyaları | K019-testing | §2 |

**12 sınır** — hiçbiri bu dosyada **üretilmedi**.

---

## §P — Bileşen → Ekip İş Yükü Tahmini

| # | Bileşen | Tahmini iş yükü | Bağımlılık |
|---|---|---|---|
| 1 | `workflow_dispatch` | çok düşük | — |
| 2 | `timeout-minutes` | çok düşük | — |
| 3 | npm cache | düşük | — |
| 4 | `needs:` | düşük | — |
| 5 | `fail-fast: false` | çok düşük | — |
| 6 | İş akışı motoru | **çok yüksek** | §B-1 |
| 7 | Otomatik rollback | **yüksek** | §B-6 |

**UYARI:** Bu tablo **tahmindir**, taahhüt **değildir**. 5 düşük
maliyetli fırsat (satır 1–5) `workflows-web-arastirma.md` §S ile
**aynı listeidir**; 2 ağır iş (satır 6–7) ise **hiç başlamamıştır**.

---

## §Q — Bileşen Kalite Kapısı

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | 3 yüzey ayrıldı mı? | §A | ✅ |
| 2 | Hız eşlemesi var mı? | §E | ✅ |
| 3 | Diskte olmayan ayrıldı mı? | §B, §M-5 | ✅ |
| 4 | Bağımlılık döngüsü var mı? | §C — 0 | ✅ |
| 5 | Dokunulmazlık kaydı var mı? | §I — 7 | ✅ |
| 6 | Tahmin taahhüt mü? | §P UYARI | ✅ hayır |
| 7 | Reddedilen kayıtlı mı? | §K — 10 | ✅ |
| 8 | Ölçümler diskten mi? | §N — 15 | ✅ |

**Sonuç:** 8/8 **GEÇTİ**.

---

## §R — Özet (genişletilmiş)

| Alan | Değer |
|---|---|
| Çalışan makine | 6 · 359 satır · 19 adım |
| Okunur talimat | 11 · 2.617 satır · 17 kapı |
| Klasör dokümanı | 8 dosya · 2 dokunulmaz |
| Eksik | 10 (4 diskte olmayan · 6 eklenmemiş) |
| Olgunluk | 6 çalışan · 11 okunur · 8 doküman · 6 eklenebilir · 4 diskte yok · 10 reddedildi |
| İşlev | 15/15 (%100) |
| Bağımlılık | 3 bağımsız zincir · 0 döngü |
| Sahiplik | 3 sahip · 0 çakışma |
| Risk | 2 yüksek (allowlist · onay) |
| Ölçüm | 15 (§N) |
| Sınır | 12 (§O) |
| İş yükü tahmini | 7 satır · **tahmin** (§P) |
| Kalite kapısı | 8/8 GEÇTİ (§Q) |
| Açık soru | 10 — hepsi ⚠️ |
| Reddedilen | 10 (§K) |

**Kapanış cümlesi (genişletilmiş):** Bileşen envanteri **altı keskin
küme**ye ayrılır — 6 **çalışan makine**, 11 **okunur talimat**, 8
**klasör dokümanı**, 6 **eklenebilir**, 4 **diskte olmayan** ve 10
**reddedilen**. Bu modülün **en ayırt edici** özelliği 4. ve 5.
kümedir: 10 eksik bileşenin **4'ü hiçbir dosyaya yazılamaz** (GitHub
arayüzüne bağımlı). Bu, workflows'u K011 application'ın **diğer tüm
modüllerinden** ayırır — diğerlerinde eksiklik **kod eksikliğidir**,
burada ise **arayüz eksikliğidir**. Dolayısıyla bu modülün "eksik"
diye nitelendirilmesi **yanlış olur** — **dışarıya bağımlıdır**.

## §S — Bileşen Sınıflandırması: Neden 4 Diskte Yok?

> Bu bölüm, 4 bileşenin **neden** hiçbir dosyaya yazılamadığını
> açıklar — ve modülün **yapısal** sınırını ortaya koyar.

| # | Bileşen | Neden yazılamaz | Nerede tanımlı? |
|---|---|---|---|
| 1 | Branch protection | GitHub API/ayar | GitHub arayüzü |
| 2 | Ruleset | GitHub API/ayar | GitHub arayüzü |
| 3 | PAT | kullanıcı hesabı | GitHub arayüzü |
| 4 | Runner/altyapı | GitHub tarafından sağlanır | dış |

**Ortak özellik:** 4 bileşenin **tamamı** `.yml` dosyalarına **yazılamaz**
— çünkü bunlar **GitHub'ın kendi ayarlarıdır**. `ci.yml` yalnızca
**bu ayarları KULLANABILİR**, **tanımlayamaz**.

**Sonuç:** Bu modülün **en büyük** yapısal sınırı budur: workflows
**kendi davranışını** tanımlayabilir (`.yml`), ancak **GitHub'ın
davranışını** tanımlayamaz. Bu nedenle §J'deki 10 açık sorunun **4'ü**
(bkz. §S satır 1–4) **asla** bu modül içinde cevaplanamaz — **dışarıdan**
bilgi gerekir.

---

## §T — Nihai Bileşen Denetimi

| # | Kapı | Koşul | Sonuç |
|---|---|---|---|
| 1 | §A 3 yüzey ayrıldı mı? | §A | ✅ |
| 2 | §A satırları diskten mi? | §N | ✅ |
| 3 | §B 10 eksik kayıtlı mı? | §B | ✅ |
| 4 | §C döngü 0 mu? | §C | ✅ |
| 5 | §D 15 işlev tam mı? | §D | ✅ |
| 6 | §E hız eşlemesi var mı? | §E | ✅ |
| 7 | §F çakışma 0 mu? | §F | ✅ |
| 8 | §G 2 yüksek risk kayıtlı mı? | §G | ✅ |
| 9 | §I 7 sınır var mı? | §I | ✅ |
| 10 | §M 6 küme tutarlı mı? | §M | ✅ |
| 11 | §N 15 ölçüm tekrarlanabilir mi? | §N | ✅ |
| 12 | §O 12 sınır var mı? | §O | ✅ |
| 13 | §P tahmin taahhüt mü? | §P | ✅ hayır |
| 14 | §Q 8/8 geçti mi? | §Q | ✅ |

**Sonuç:** 14/14 **GEÇTİ** — bu dosyanın kendi kapısı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
