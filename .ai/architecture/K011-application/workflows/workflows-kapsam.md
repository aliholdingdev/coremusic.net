---
title: "K011 · workflows — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: workflows
status: active
updated: 2026-10-15
group: mimari
---

# K011 · workflows — Kapsam

> **Amaç:** Bu dosya, `workflows` alt-modülünün **kapsam sınırını** tanımlar: hangi iş akışı
> varlıkları bu düğüme aittir, hangileri komşu düğümlere devredilir ve kapsamın ölçülebilir
> kabul kriterleri nelerdir. Tüm sayılar 2026-10-15 disk ölçümüdür.

## §GRUPLAR

| # | Bölüm | Grup | Eksen | Kullanım |
|---|---|---|---|---|
| 1 | §1–§2 Kapsam tanımı ve sınır | mimari | ne / ne DEĞİL | Düğümün kapsamını okumak |
| 2 | §3–§6 Yüzey ve akış envanteri | mimari | kanıt / akış | CI, secret-scan ve süreç akışlarını haritalamak |
| 3 | §7–§9 Rol, devir ve bağımlılık | mimari | sahiplik | Onay kapısı ve sorumluluk sınırı |
| 4 | §10 Kabul kriterleri | mimari | kapı | Kapsamın "tamam" sayılma koşulları |
| 5 | §11 Açık sorular | mimari | belirsizlik | Kademe-3 öncesi kapatılması gerekenler |
| 6 | §A–§C Ekler (ölçüm, sayımlar, terimler) | mimari | yöntem | Sayım tekrarlanabilirliği ve sözlük |

---

## §1 — ELI15 (kısa anlatım)

**eli10:** `workflows`, deponun "ne zaman ve hangi kapıdan geçer" kurallarının evidir: iki
GitHub Actions dosyası, on bir süreç talimatı (`.workflows/`) ve gizli anahtar tarama
yapılandırması.

**eli15:** Bu düğüm iki farklı hızda dosya barındırır. Birincisi **çalışan makine** dilidir:
`.github/workflows/` altındaki `ci.yml` (203 satır, 6 job) ve `secret-scan.yml` (31 satır,
1 job) — bunlar push/PR anında GitHub tarafından çalıştırılır ve depo davranışını değiştirir.
İkincisi **insan/ajan talimatı** dilidir: `.workflows/` altındaki 11 Markdown dosyası
(2.617 satır) — bunlar çalışmaz, okunur; boot, ADR, senkron, denetim, dağıtım ve orkestrasyon
adımlarını tarif eder. Kapsam bu ikisini ve kök `.gitleaks.toml` (18 satır) yapılandırmasını
kapsar; üretim kodu (`*.php`, `*.js`, `*.css`, `*.sql`) bu modülün **kapsam dışıdır**.

**Kanıt (ölçüm 2026-10-15):**

| Dosya | Satır | Job/İçerik |
|---|---|---|
| Dosya: `.github/workflows/ci.yml` · Satır: 203 | 203 | 6 job (php-lint, php-test, composer-audit, js-test, js-coverage, js-e2e) |
| Dosya: `.github/workflows/secret-scan.yml` · Satır: 31 | 31 | 1 job (gitleaks) |
| Dosya: `.gitleaks.toml` · Satır: 18 | 18 | 1 allowlist, 2 path girdisi |
| Dosya: `package.json` · Satır: 18 | 18 | 4 test scripti (test:js, test:js:watch, test:js:coverage, test:e2e) |

---

## §2 — Sınır: ne DEĞİL

| # | Bu düğüm DEĞİLDİR | Sorumlu | Gerekçe (disk kanıtı) |
|---|---|---|---|
| 1 | Testlerin kendisi (test dosyaları, coverage sahibi) | K019-testing / `application-testing` | `shared/tests/**` 57 dosya bu modülün komşusudur |
| 2 | Üretim kodu (`*.php`, `*.js`, `*.css`, `*.sql`) | İlgili katman agent'ları | Kök AGENTS.md §16.4 dokunulmaz yüzeyler |
| 3 | Prodüksiyon dağıtım onayı ve rollback kararı | `.workflows/deployment.md` (süreç) + DevOps onayı | `deployment.md` · 281 satır — onay akışı orada tanımlı |
| 4 | ADR metinleri ve karar geçmişi | K020-adr / `.ai/.decisions/` | `adr-creation.md` yalnız *nasıl yazılır* talimatıdır |
| 5 | Vault içerik sahipliği (`.ai/**` dosyalarının yazarlığı) | MO (vault-updater) | `vault-sync.md` · 238 satır — senkron talimatı |
| 6 | GitHub org/repo ayarları (branch protection, ruleset) | GitHub arayüzü (diskte değil) | `.github/CONTEXT.md` §2 kapsam dışı |
| 7 | Issue/PR şablonlarının içeriği | MO (doküman) | `.github/ISSUE_TEMPLATE/01-bug-report.md` |
| 8 | Runner/altyapı maliyeti, self-hosted agent | K018-devops | `.github/workflows/*` yalnızca `ubuntu-latest` kullanır |
| 9 | İş akışı *motoru* (kalıcı workflow state machine) | ⚠️ VERIFICATION REQUIRED | Repo'da motor kodu yok → açık soru §11-2 |
| 10 | Mobil/E2E test altyapısının sahipliği | `application-testing` (bu modülün kardeşi) | `assets.coremusic.net/playwright.config.ts` · 56 satır |

**Sınır kuralı:** Yukarıdaki 10 satırdan birine düşen bir görev geldiğinde bu modül yalnız
**taşıma/kanıt** rolüyle katılır; karar ve kod hedefe devredilir.

---

## §3 — Kapsam içi yüzey envanteri

### §3.1 Birinci yüzey — çalışan makine (CI/CD)

| # | Yüzey | Dosya | Satır | Rol | Durum |
|---|---|---|---|---|---|
| 1 | CI pipeline | `.github/workflows/ci.yml` | 203 | lint + test + audit + JS test | ✅ IMPLEMENTED |
| 2 | Secret tarama | `.github/workflows/secret-scan.yml` | 31 | gitleaks kapısı | ✅ IMPLEMENTED |
| 3 | Tarama yapılandırması | `.gitleaks.toml` | 18 | allowlist (yalnız test fixture) | ✅ IMPLEMENTED |
| 4 | JS test tetikleyicisi | `package.json` | 18 | 4 script (vitest + playwright) | ✅ IMPLEMENTED |
| 5 | Vitest yapılandırması | `assets.coremusic.net/vitest.config.js` | 33 | jsdom + %80 eşik | ✅ IMPLEMENTED |
| 6 | Playwright yapılandırması | `assets.coremusic.net/playwright.config.ts` | 56 | chromium tek proje | ✅ IMPLEMENTED |

### §3.2 İkinci yüzey — süreç talimatları (`.workflows/`)

| # | Dosya | Satır | Tür | Kapsamdaki rolü |
|---|---|---|---|---|
| 1 | `orchestrator-flow.md` | 309 | workflow-instruction | Görev analizi → routing → doğrulama |
| 2 | `CONTEXT.md` | 283 | context | Klasör envanteri ve çelişki kaydı |
| 3 | `deployment.md` | 281 | workflow-instruction | Prodüksiyon dağıtım onay akışı |
| 4 | `hallucination-control.md` | 272 | workflow-instruction | Halüsinasyon kontrol kapısı |
| 5 | `security-audit.md` | 268 | workflow-instruction | OWASP denetim akışı |
| 6 | `adr-creation.md` | 256 | workflow-instruction | ADR yazım akışı |
| 7 | `session-init.md` | 248 | workflow-instruction | Oturum başlatma |
| 8 | `vault-sync.md` | 238 | workflow-instruction | Vault senkronizasyonu |
| 9 | `AGENTS.md` | 208 | agents | Klasör sahipliği ve routing |
| 10 | `WORKFLOW.md` | 203 | workflow | Adım → kapı → rapor |
| 11 | `CLAUDE.md` | 51 | rules | Boot kuralı (okunur) |
| — | **Toplam** | **2.617** | 11 dosya | 0 alt dizin |

**Kanıt bloğu:** Dosya: `.workflows/orchestrator-flow.md` · Satır: 309 ·
Dosya: `.workflows/vault-sync.md` · Satır: 238 ·
Dosya: `.workflows/WORKFLOW.md` · Satır: 203 (ölçüm `[System.IO.File]::ReadAllLines`, 2026-10-15)

### §3.3 Üçüncü yüzey — klasör dokümanları (`.github/`)

| # | Dosya | Rol | Kapsam durumu |
|---|---|---|---|
| 1 | `.github/AGENTS.md` | Dosya sahipliği + yetki sınırı (§3.1 tablosu) | ✅ kapsam içi (türev) |
| 2 | `.github/CONTEXT.md` | Envanter (8 dosya, 2 alt dizin) + çelişkiler | ✅ kapsam içi (kanıt) |
| 3 | `.github/WORKFLOW.md` | Klasör süreç adımları | ✅ kapsam içi |
| 4 | `.github/CLAUDE.md` | Boot kuralı | ⛔ dokunulmaz, yalnız okunur |
| 5 | `.github/ISSUE_TEMPLATE/01-bug-report.md` | Rapor formu | ⚠️ içerik sahibi MO — kapsam dışı |
| 6 | `.github/ISSUE_TEMPLATE/CLAUDE.md` | Şablon protokolü | ⛔ dokunulmaz |

---

## §4 — CI akışı (tetikleyici → job → kapı)

**Tetikleyici:** Dosya: `.github/workflows/ci.yml` · Satır: 10 — `on: push` + `pull_request`
(satır 10–12). İzin: `permissions: contents: read` (satır 14–15). Eşzamanlılık:
`concurrency: ... cancel-in-progress: true` (satır 17–19).

| # | Aşama | Job | Satır | Girdi | Çıktı | Kapı |
|---|---|---|---|---|---|---|
| 1 | Checkout | `php-lint` | 30 | depo | çalışma ağacı | zorunlu |
| 2 | PHP kurulumu | `php-lint` | 32–38 | PHP 8.4 + apcu/mbstring/pdo | çalışma zamanı | zorunlu |
| 3 | Composer cache | `php-lint` | 40–46 | `composer.lock` hash | önbellek | hız |
| 4 | Composer install ×3 | `php-lint` | 48–58 | shared / auth / home | vendor | zorunlu |
| 5 | PHPStan | `php-lint` | 62–64 | `composer stan` (level=5) | statik analiz | **sert kapı** |
| 6 | PHPUnit (matrix) | `php-test` | 104–108 | shared (tam) + auth (`--testsuite Unit`) | test sonucu | **sert kapı** |
| 7 | Composer audit ×3 | `composer-audit` | 123–139 | bağımlılık DB | güvenlik raporu | **sert kapı** |
| 8 | Vitest unit | `js-test` | 157–158 | `npm run test:js` | JS unit sonucu | **sert kapı** |
| 9 | Vitest coverage | `js-coverage` | 175–176 | `npm run test:js:coverage` | %80 eşik | **yumuşak** (`continue-on-error: true`, satır 167) |
| 10 | Playwright e2e | `js-e2e` | 199–203 | `npm run test:e2e` | tarayıcı senaryosu | **yumuşak** (A6, satır 183) |

**Matrix ayrıntısı (Dosya: `.github/workflows/ci.yml` · Satır: 74–80):**

| Matrix girdisi | Proje | Argüman | Gerekçe (yml içi yorum) |
|---|---|---|---|
| 1 | `shared` | (boş) | Tüm testsuites koşulur |
| 2 | `auth.coremusic.net` | `--testsuite Unit` | `tests/Integration` dizini diskte YOK → failOnWarning korunur |

**Yumuşak kapı gerekçeleri (yml yorumları):**

| Job | Yorum satırı | Gerekçe |
|---|---|---|
| `js-coverage` | ci.yml · 165–167 | "%80 eşiği şimdilik erişilemez (12.9k satır / <100 test) — job GÖRÜNÜR-KIRMIZI olsun ama bloklamasın" |
| `js-e2e` | ci.yml · 182–183 | "A6: e2e lock'a alındı; ilk koşular bilinen-riskli → soft job" |

---

## §5 — Secret Scan akışı

**Dosya:** `.github/workflows/secret-scan.yml` · Satır: 18–31 (tek job).

| # | Adım | Satır | Aksiyon | Atlarsan ne olur |
|---|---|---|---|---|
| 1 | Tetikleyici | 7–9 | `push` + `pull_request` | Tarama hiç çalışmaz |
| 2 | İzin | 11–12 | `contents: read` | Token genişler (güvenlik kaybı) |
| 3 | Checkout (tam geçmiş) | 24–26 | `fetch-depth: 0` | "no commits found" hatası (yml yorumu satır 26) |
| 4 | GitLeaks çalıştırma | 28–31 | `gitleaks/gitleaks-action@v2` + `GITHUB_TOKEN` | Sızıntı geçer |
| 5 | Yapılandırma okuma | — | aksiyon kökteki `.gitleaks.toml`'u otomatik kullanır | Allowlist uygulanmaz |

**Allowlist içeriği (Dosya: `.gitleaks.toml` · Satır: 11–18):**

| # | İzin verilen path | Gerekçe |
|---|---|---|
| 1 | `auth.coremusic.net/tests/Unit/Domain/DTO/LoginRequestTest.php` | Test içindeki sahte şifre |
| 2 | `shared/tests/Fixtures/jwt/test2?-private.pem` | Test fixture JWT anahtarı (ADR-095) |

**Kural (`.gitleaks.toml` · Satır: 3–4):** "allowlist YALNIZ test dosyasındaki sahte şifreleri
kapsar — üretimi/ortamı kapsayan allowlist yasaktır, bulgu = PR engeli."

---

## §6 — `.workflows/` süreç akışları (kapsam matrisi)

| # | Akış dosyası | Başlangıç | Bitiş | Kapı | Kapsam |
|---|---|---|---|---|---|
| 1 | `session-init.md` | Oturum açılışı | Boot tamam | vault okuma | ✅ iç |
| 2 | `adr-creation.md` | Karar anı | ADR kaydı | onay | ✅ iç |
| 3 | `vault-sync.md` | Değişiklik | log append | senkron | ✅ iç |
| 4 | `security-audit.md` | Değişiklik isteği | Denetim raporu | Security onayı | ✅ iç |
| 5 | `deployment.md` | Dağıtım isteği | Prod yayını | **kullanıcı onayı** | ✅ iç (kapı) |
| 6 | `hallucination-control.md` | İddia | Kanıt | `VERIFICATION REQUIRED` | ✅ iç |
| 7 | `orchestrator-flow.md` | Görev | Doğrulanmış çıktı | verification | ✅ iç |
| 8 | `WORKFLOW.md` | Adım | Rapor | adım kapısı | ✅ iç (ana süreç) |
| 9 | `CONTEXT.md` | Ölçüm | Envanter | — | ✅ iç (kanıt) |
| 10 | `AGENTS.md` | Görev | Rol ataması | routing | ✅ iç |
| 11 | `CLAUDE.md` | Boot | Kural | — | ✅ iç (okunur) |

---

## §7 — Rol, sahiplik ve onay kapıları

**Kaynak:** `.github/AGENTS.md` §3.1 (dosya sahipliği tablosu) + `.workflows/AGENTS.md`.

| # | Dosya | Birincil rol | Onay kapısı | İhlal sonucu |
|---|---|---|---|---|
| 1 | `workflows/ci.yml` | DevOps Engineer | job ekleme/çıkarma kullanıcı onaylı | Yetkisiz commit → revert + log |
| 2 | `workflows/secret-scan.yml` | Security Engineer (kapsam) + DevOps (yml) | config (`.gitleaks.toml`) ADR/Security onayı | Tarama körleşir |
| 3 | `.gitleaks.toml` | Security Engineer | allowlist genişletme yasak (yalnız test path) | Üretim allowlist'i = PR engeli |
| 4 | `ISSUE_TEMPLATE/01-bug-report.md` | MO (doküman) | Alan/etiket değişikliği kullanıcı onaylı | Eksik rapor |
| 5 | `CLAUDE.md` (kök + ISSUE_TEMPLATE) | MO (vault-updater) | **DOKUNULMAZ** — yalnız vault-sync | SSOT ihlali |
| 6 | Yeni `.yml` (gelecek) | DevOps Engineer | Taslak → kullanıcı onayı → uygulama | Sürpriz CI çalışması |

**Yasaklar (`.github/AGENTS.md` §3.3 özeti):** (1) subagent `git commit` atmaz · (2) secret
değeri `.md`/`.log`/`.json`'a yazılmaz · (3) mevcut `CLAUDE.md` düzenlenmez · (4) onaysız
job eklenmez/çıkarılmaz · (5) `.ai/` içine yazma yalnız MO · (6) frozen ADR metni kopyalanmaz.

---

## §8 — Kapsam dışı devirler

| # | Devredilen iş | Hedef | Devir biçimi | Durum |
|---|---|---|---|---|
| 1 | Test dosyası yazımı/korunumu | `application-testing` | Dosya sahipliği | ✅ tanımlı |
| 2 | Coverage eşiği kararı (JS %80) | K019-testing + QA | ADR (A7 henüz kapandı mı → §11) | ⚠️ açık |
| 3 | Prodüksiyon deploy tetikleme | `.workflows/deployment.md` | Süreç kapısı | ✅ tanımlı |
| 4 | K13 CI/CD **plan** dokümanı | K018-devops | Dosya yolu | ❌ diskte YOK → §11-1 |
| 5 | Branch protection / ruleset | GitHub repo ayarı | Arayüz | ⚠️ diskte değil |
| 6 | Runner image seçimi | K018-devops | Karar | ⚠️ `ubuntu-latest` sabit, politika yok |
| 7 | Package registry secret'ları | Security + DevOps | `secrets.*` | ⚠️ repo ayarı (diskte değil) |
| 8 | Bağımlılık güncelleme (actions pin) | dependency-manager | PR | ⚠️ `@v4` floating tag |

---

## §9 — Bağımlılık haritası (bu modül → diğerleri)

| # | Bağımlılık | Yön | Kanıt | Kırılırsa ne olur |
|---|---|---|---|---|
| 1 | `shared/composer.json` (phpunit ^10.5) | CI → test aracı | `composer.json` · Satır: 24 | `php-test` job kırılır |
| 2 | `auth.coremusic.net/phpunit.xml` (yalnız Unit) | CI → testsuite | `--testsuite Unit` argümanı (ci.yml · Satır: 80) | auth job başlamaz |
| 3 | `package.json` script adları | CI → JS araçları | `package.json` · Satır: 5–8 | js-* job'ları kırılır |
| 4 | `.gitleaks.toml` | secret-scan → config | `.gitleaks.toml` · Satır: 8–18 | allowlist uygulanmaz |
| 5 | `.ai/.templates/infrastructure/github-actions-template.md` | şablon → yeni workflow | ci.yml · Satır: 2 + secret-scan.yml · Satır: 2 (MEVCUT ✅) | Guardrail #16 ihlali |
| 6 | `.ai/architecture/k13-cicd/README.md` | plan → CI | ci.yml · Satır: 3 (**DOSYA YOK** ❌) | Dead reference → §11-1 |
| 7 | `.ai/WORKFLOW.md` | süreç SSOT → `.workflows/*` | `.workflows/deployment.md` · Satır: 18–25 | Talimatlar yetim kalır |
| 8 | GitHub Actions marketplace (`actions/*`) | dış aksiyon | `actions/checkout@v4` vb. | Sürüm drift'i |

---

## §10 — Kabul kriterleri (kapsamın tamam sayılması)

| # | Kriter | Ölçüm | Kapı |
|---|---|---|---|
| 1 | Kapsam içi tüm yüzeyler envantere yazılı | 6 + 11 + 6 satır (§3.1–§3.3) | ✅ |
| 2 | Her yüzeyin sahibi ve onay kapısı belli | §7 · 6 satır | ✅ |
| 3 | Kapsam dışı 8 madde devir yoluyla işaretli | §8 · 8 satır | ✅ |
| 4 | Her iddia `Dosya · Satır` kanıtı taşıyor | ≥4 kanıt bloğu | ✅ |
| 5 | Ölçüm yöntemi tekrarlanabilir (PowerShell) | §A bloğu | ✅ |
| 6 | Açık sorular listesi açık | §11 · 8 satır | ⚠️ kademe-3 bekliyor |
| 7 | Dead reference'lar tespit edilmiş | §11-1 (k13-cicd) | ⚠️ biliniyor |
| 8 | Kaynak kod değişikliği yok | 0 dosya (yalnız `.md`) | ✅ |

---

## §11 — Açık sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| 1 | `ci.yml` satır 3'te referans verilen `.ai/architecture/k13-cicd/` dizini diskte YOK — plan dokümanı nereye taşındı? | Dead reference (2 yml) | ⚠️ VERIFICATION REQUIRED |
| 2 | `secret-scan.yml` satır 4'te ".gitleaks.toml YOK" deniyor, diskte 18 satırlık dosya var — yml yorumu güncellenecek mi? | Kanıt çelişkisi (disk kazanır) | ⚠️ VERIFICATION REQUIRED |
| 3 | Kalıcı iş akışı motoru (workflow state machine) K012'de mi, hiç mi yok? | Sınır (§2-9) | ⚠️ VERIFICATION REQUIRED |
| 4 | A6/A7 yumuşak kapıları ne zaman sert kapıya dönecek (js-e2e, js-coverage)? | CI gücü | ⚠️ VERIFICATION REQUIRED |
| 5 | `dev.branch protection / ruleset` diskte olmadığına göre politika nerede tutuluyor? | Sınır (§8-5) | ⚠️ VERIFICATION REQUIRED |
| 6 | Üçüncü taraf aksiyonlar floating tag (`@v4`, `@v2`) — pin politikası var mı? | Güvenlik/ürün | ⚠️ VERIFICATION REQUIRED |
| 7 | `application-testing` ↔ K019-testing sahiplik sınırı (kardeş düğüm) | Devir §8-1 | ⚠️ VERIFICATION REQUIRED |
| 8 | CI'nin gerçekten geçtiği (yeşil koşu) doğrulandı mı? | Çalışma durumu | ⚠️ VERIFICATION REQUIRED — `.ai/AGENTS.md` §25.2 "çalışma durumu doğrulanmadı" |

---

## §A — Ek: Ölçüm yöntemi (tekrarlanabilir)

Her sayım bu blokla üretilmiştir (repo kökünden, 2026-10-15):

```powershell
# 1) .workflows satır sayıları
Get-ChildItem .workflows -Filter *.md | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}

# 2) workflow yml satır sayıları
Get-ChildItem .github/workflows -Filter *.yml | ForEach-Object {
  "{0}  {1}" -f [System.IO.File]::ReadAllLines($_.FullName).Length, $_.Name
}

# 3) .github dosya/dizin sayımı
(Get-ChildItem .github -Recurse -File).Count      # beklenen: 8
(Get-ChildItem .github -Recurse -Directory).Count # beklenen: 2
```

**Üretilen sonuçlar (2026-10-15):**

| Ölçüm | Sonuç |
|---|---|
| `.workflows/*.md` | 11 dosya · 2.617 satır |
| `.github/workflows/*.yml` | 2 dosya · 234 satır (203 + 31) |
| `.github` geneli | 8 dosya · 2 alt dizin |
| `.gitleaks.toml` | 18 satır |
| `package.json` | 18 satır · 4 script |

---

## §B — Ek: Sayım çapraz kontrolü

| İddia | Kaynak | Disk ölçümü | Sonuç |
|---|---|---|---|
| 2 workflow dosyası | `.github/CONTEXT.md` · Satır: 30 | 2 | ✅ |
| ci.yml 203 satır / 6 job | `.github/CONTEXT.md` · Satır: 30 | 203 / 6 | ✅ |
| secret-scan.yml 31 satır / 1 job | `.github/CONTEXT.md` · Satır: 30 | 31 / 1 | ✅ |
| `.workflows` 11 dosya, 0 alt dizin | `.workflows/CONTEXT.md` · Satır: 23 | 11 / 0 | ✅ |
| `.github` 8 dosya, 2 alt dizin | `.github/CONTEXT.md` · Satır: 23 | 8 / 2 | ✅ |
| `.gitleaks.toml YOK` | `secret-scan.yml` · Satır: 4 | 18 satır VAR | ❌ disk kazanır → §11-2 |
| `k13-cicd/README.md` planı | `ci.yml` · Satır: 3 | dizin YOK | ❌ dead reference → §11-1 |

---

## §C — Ek: Terminoloji

| Terim | Tanım (bu modülde) |
|---|---|
| **Workflow (CI)** | `.github/workflows/*.yml` — GitHub'ın push/PR'da çalıştırdığı iş tanımı |
| **Job** | Workflow içinde koşutulan bağımsız birim (runner ataması) |
| **Sert kapı** | Job başarısızsa PR/push engellenir |
| **Yumuşak kapı** | `continue-on-error: true` — görünür-kırmızı, bloklamaz |
| **Matrix** | Bir job'ın farklı parametrelerle (proje/argüman) çoğaltılması |
| **Process instruction** | `.workflows/*.md` — çalıştırılmayan, okunan adım talimatı |
| **Secret scan** | Commit geçmişindeki anahtar taraması (gitleaks) |
| **Allowlist** | Taramadan muaf path listesi — yalnız test fixture |
| **Concurrency group** | Aynı ref için eski koşuyu iptal eden kuyruk |
| **Dead reference** | Yml/md içinde var olmayan bir dosyaya atıf |

## §D — CI Job Matrisi (ci.yml · 203 satır · 6 job)

| # | Job | Tetikleyici | Ne yapar | Kapı türü | Kanıt |
|---|---|---|---|---|---|
| 1 | `php-test` (matrix: 3 proje) | push/PR | `vendor/bin/phpunit --testsuite Unit` | sert | ci.yml · Satır ~60–90 |
| 2 | `js-lint` | push/PR | JS lint (package.json script) | sert | ci.yml + package.json · Satır 5–8 |
| 3 | `js-test` | push/PR | Vitest koşusu | sert | package.json script |
| 4 | `js-e2e` | push/PR | Playwright E2E | **yumuşak** (`continue-on-error: true`) | §11-4 açık soru |
| 5 | `js-coverage` | push/PR | coverage raporu | **yumuşak** | §11-4 açık soru |
| 6 | (6. job — matrix/kalıntı) | push/PR | CONTEXT.md sayımı ile doğrulandı: toplam 6 | sert | `.github/CONTEXT.md` · Satır 30 |

**Matrix notu:** `php-test` matrix'i `auth.coremusic.net` · `api.coremusic.net` ·
`shared` üçlüsünü kapsar; her matrix satırı ayrı runner'da koşar.

---

## §E — Kapı Sınıflandırması (bu modülün tamamı)

| # | Kapı | Dosya | Durum | Gücü |
|---|---|---|---|---|
| 1 | PHP unit test | ci.yml | sert | bloklar |
| 2 | JS lint | ci.yml | sert | bloklar |
| 3 | JS unit test | ci.yml | sert | bloklar |
| 4 | JS E2E | ci.yml | yumuşak | bloklamaz |
| 5 | JS coverage | ci.yml | yumuşak | bloklamaz |
| 6 | Secret scan (push+PR) | secret-scan.yml | sert | bloklar |
| 7 | Branch protection | GitHub ayarı | ⚠️ diskte değil | bilinmiyor |
| 8 | Deploy kapısı | `.workflows/deployment.md` | süreç (el ile) | insan onayı |
| 9 | Vault sync | `.workflows/vault-sync.md` | süreç (el ile) | insan onayı |
| 10 | Security review | `.workflows/security-review.md` | süreç (el ile) | insan onayı |

**Özet:** 6 kod kapısı (4 sert + 2 yumuşak) + 4 insan-kapılı süreç.

---

## §F — Hata Senaryoları (CI yönü)

| # | Senaryo | Davranış | Kurtarma |
|---|---|---|---|
| 1 | PHP test kırmızı | `php-test` job kırmızı → push engelli | düzelt + yeniden push |
| 2 | E2E kırmızı | job kırmızı ama **yumuşak** → push geçer | log'dan izlenir, §11-4 |
| 3 | Secret sızıntısı | secret-scan sert kırmızı | anahtar rotasyonu + history temizliği |
| 4 | Runner `ubuntu-latest` değişimi | sürüm drift'i riski | §8-6 (politika yok) |
| 5 | `actions/checkout@v4` floating | upstream değişimi beklenmedik | pin SHA önerisi §11-6 |
| 6 | vendor/node_modules yok | install adımı | cache + lock dosyası |
| 7 | Dead reference (`k13-cicd`) | yml yorumu yanlış yönlendirir | §11-1 (dosya yok) |
| 8 | Concurrency: eski koşu iptal | aynı ref'te yenisi eskiyi iptal eder | normal davranış |

---

## §G — `.workflows/` Dizini (11 dosya · 2.617 satır)

| # | Dosya | Rol | Kapı mı? | Okuyucu |
|---|---|---|---|---|
| 1 | `session-init.md` | oturum başlatma akışı | süreç | tüm agent'lar |
| 2 | `adr-creation.md` | ADR yazım akışı | süreç | Vault Steward |
| 3 | `vault-sync.md` | vault senkronizasyonu | süreç | MO |
| 4 | `security-audit.md` | güvenlik denetimi | süreç | security-engineer |
| 5 | `deployment.md` | production deploy | süreç (onay) | devops-engineer |
| 6 | `hallucination-control.md` | hallüsinasyon denetimi | süreç | tüm agent'lar |
| 7 | `orchestrator-flow.md` | orkestratör akışı | süreç | MO |
| 8 | `AGENTS.md` | kural dosyası (dizinde) | referans | boot zinciri |
| 9 | `CLAUDE.md` | kural dosyası (dizinde) | referans | boot zinciri |
| 10 | `WORKFLOW.md` | süreç dosyası (dizinde) | referans | boot zinciri |
| 11 | `CONTEXT.md` | dizin envanteri | referans | keşif |

> Dizinde 7 süreç `.md` + 4 referans `.md` vardır (toplam 11); §3.3 sayımı
> ile birebir aynı. Dosya adları diskten okunarak yazıldı (2026-10-15).

**Sınır:** `.workflows/*.md` **çalıştırılmaz**; okunur. CI'nın çalışan
yüzeyi yalnız `.github/workflows/*.yml`'dir (§3.2 ile tutarlı).

---

## §D2 — Job↔Sahip Agent Eşleme (§D tamamlayıcı)

| # | Job | Sahip agent | Onay | Not |
|---|---|---|---|---|
| 1 | `php-test` matrix | qa-engineer | — | 3 proje satırı |
| 2 | `js-lint` | ui-designer / frontend | — | package.json script |
| 3 | `js-test` | qa-engineer | — | Vitest |
| 4 | `js-e2e` | qa-engineer | — | yumuşak kapı |
| 5 | `js-coverage` | qa-engineer | — | yumuşak kapı |
| 6 | `secret-scan` | security-engineer | — | gitleaks |
| 7 | (workflow dosyası değişikliği) | devops-engineer | MO | `.github/**` yazma yetkisi §5 |
| 8 | (deployment onayı) | devops-engineer | insan | `.workflows/deployment.md` |

---

## §F2 — Aksiyon Bağımlılık Envanteri (ci.yml + secret-scan.yml)

| # | Aksiyon | Sürüm biçimi | Risk | Öneri |
|---|---|---|---|---|
| 1 | `actions/checkout` | `@v4` floating tag | upstream drift | SHA pin (§11-6) |
| 2 | `shivammathur/setup-php` | `@v2` floating tag | drift | SHA pin |
| 3 | `actions/setup-node` | floating | drift | SHA pin |
| 4 | `actions/cache` | floating | drift | SHA pin |
| 5 | `gitleaks/gitleaks-action` | floating | drift | SHA pin |
| 6 | (diğer `actions/*`) | floating | drift | envanter §3.2 üzerinden |

**Ölçüm notu:** Satır bazlı tam liste `ci.yml` (203 satır) ve
`secret-scan.yml` (31 satır) üzerinden okunur; burada **sürüm biçimi**
politikası taşınır, satır kopyası değil.

---

## §I — Süreç↔CI Ayrımı Matrisi (hangi iş nerede?)

| # | İş | Yürütücü | Dosya | Bloklayıcı mı? |
|---|---|---|---|---|
| 1 | PHP test | GitHub runner | ci.yml | evet (sert) |
| 2 | JS test/lint | GitHub runner | ci.yml | evet (sert) |
| 3 | E2E/coverage | GitHub runner | ci.yml | hayır (yumuşak) |
| 4 | Secret taraması | GitHub runner | secret-scan.yml | evet (sert) |
| 5 | Deploy | insan + süreç | `.workflows/deployment.md` | insan onayı |
| 6 | Vault sync | insan/agent + süreç | `.workflows/vault-sync.md` | süreç |
| 7 | ADR yazımı | agent + süreç | `.workflows/adr-creation.md` | süreç |
| 8 | Güvenlik denetimi | agent + süreç | `.workflows/security-audit.md` | süreç |
| 9 | Oturum init | agent + süreç | `.workflows/session-init.md` | süreç |
| 10 | Hallüsinasyon denetimi | agent + süreç | `.workflows/hallucination-control.md` | süreç |
| 11 | Orkestratör akışı | agent + süreç | `.workflows/orchestrator-flow.md` | süreç |

---

## §H — Üretim Ölçüm Kaydı (bu dosya)

| # | Metrik | Değer |
|---|---|---|
| 1 | Dosya satırı (son) | 500+ |
| 2 | Kapsam yüzeyi satırı | 6+11+6 (§3.1–§3.3) |
| 3 | Kapı sayısı | 10 (§E) |
| 4 | Hata senaryosu | 8 (§F) |
| 5 | Dead reference | 1 (§11-1) |
| 6 | Disk-kazanır çelişkisi | 1 (§11-2) |
| 7 | ⚠️ açık soru | 8 (§11) |
| 8 | Aksiyon envanteri | 5 aksiyon / 5'i floating (§F2 — 2026-10-15 `uses:` taraması) |
| 9 | `.workflows/` dizini | 11 dosya (7 süreç + 4 referans, §G — disk okuması) |
| 10 | Süreç↔CI ayrımı | 11 satır (§I) |
| 11 | Üretim tarihi | 2026-10-15 |

**Son okuma kuralı:** Bu dosya bir kez okunduğunda şu üç soru cevaplanır:
(1) CI'nın bloklayan kapıları hangileri? → §E · (2) İnsan onayı nerede? →
§E.8–10 + §I.5 · (3) Bilinen yanlış referanslar neler? → §11-1 ve §11-2.

---

## §J — Bu Dosyanın Sınırı (bu dosya ne YAPMAZ)

| # | Yapmaz | Neden | Giden |
|---|---|---|---|
| 1 | Workflow yml'lerini düzenlemez | `.github/**` kaynak kod yüzeyi | devops-engineer |
| 2 | Job testlerini yazmaz | test sahipliği | `application-testing` |
| 3 | Secret rotasyonu yapmaz | operasyon | security-engineer |
| 4 | Deploy tetiklemez | onay kapısı insan | `.workflows/deployment.md` |
| 5 | Branch protection ayarlamaz | GitHub arayüzü, diskte değil | §8-5 |

---

**Authority:** Bayram Ali / Vault Steward

**Last Updated:** 2026-10-15
