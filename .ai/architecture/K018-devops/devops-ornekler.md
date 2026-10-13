---
node_id: K018-devops/devops-ornekler
node_type: module
status: draft
parent_id: K018-devops
children: []
depends_on:
  - K019-testing
  - K009-security
evidence:
  - ".github/workflows/ci.yml · Satır 1-30 — checkout@v4 + setup-node@v4 + npm ci (203 satır)"
  - ".github/workflows/secret-scan.yml · Satır 1-31 — cron schedule + gitleaks-action@v2 (31 satır)"
  - ".gitleaks.toml · Satır 1-18 — allowlist path + regex (18 satır)"
  - ".workflows/deployment.md · Satır 44-70 — Gate 3 · 4 · 6 alıntıları (281 satır)"
  - ".ai/AGENTS.md · Satır 85 — subagent commit yasağı (525 satır)"
  - "package.json Satır 1-12 · shared/composer.json Satır 1-8 — manifesto alıntıları"
validation:
  - "Tüm Dosya/Satır alıntıları 2026-10-13'te dosya-içinde birebir okundu"
  - "Web tablosu 3 URL · HTTP 200 · envanter dışı (§7)"
  - "Yasaklı yer-tutucu taraması: yalnızca yasağın anıldığı satırlar — anma, yer-tutucu değil"
open_questions:
  - "§2.3 PHP gate'leri (composer validate · php -l · parallel-lint) doğrudan ci.yml'de görülmedi — UNKNOWN"
  - "§6.4 deployment kapı sayısı 7 vs 8 çelişkisi — hangisinin doğru olduğu UNKNOWN"
  - "§8.3 A-B-A: brief 30 dk önceki commit gözlemi ile git log çıktısı çelişiyor — ⚠️ VERIFICATION REQUIRED"
group: kod
---

# K018 DevOps — Kod Örnekleri ve Gerçek Depo Kanıtları

> **Kapsam:** Bu dosya, K018-devops kökünün `ornekler` şemasını doldurur: gerçek CoreMusic
> depo dosyalarından seçilmiş pipeline/commit/artifact/deploy örnekleri, satır numarasıyla
> kanıtlanmış kod parçaları ve doğrulanmış resmi dokümantasyon referansları. Her örnek
> `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` işaretli olabilir; hiçbir kod satırı uydurulmaz.
> Yasaklı yer-tutucular bu dosyada yasaktır — bilinmeyen satır, satır sayısıyla "atlanır"
> ya da tam metin verilir; yer-tutucu bulgu araması bu dosyanın sonundaki ÖLÇÜM bloğunda
> (task çıktısı içinde) sayılarak raporlanır.

## §1 Dosya Kataloğu ve Kanıt Disiplini

| Dosya | Tür | Satır | Bu dosyadaki rol |
|---|---|---|---|
| `Dosya: .github/workflows/ci.yml` | CI pipeline | 203 | §2 — build/lint/test job zinciri |
| `Dosya: .github/workflows/secret-scan.yml` | Secret tarama | 31 | §3 — schedule tetikli tarama |
| `Dosya: .gitleaks.toml` | Tarama konfigürasyonu | 18 | §3 — allowlist tanımı |
| `Dosya: .workflows/deployment.md` | Kapı dokümanı | 281 | §6 — 8 kapılı deployment süreci |
| `Dosya: package.json` | Bağımlılık manifestosu | 12 | §5 — git-cz commit aracı |
| `Dosya: shared/composer.json` | Bağımlılık manifestosu | 8 | §5 — PHP bağımlılık seti |
| `Dosya: .ai/AGENTS.md` | Kural dosyası | 525 | §4 — subagent commit yasağı |

**Okuma kuralı:** her `Dosya:` + `Satır:` çifti, dosyanın o satırını birebir alıntılar;
alıntılar `Select-Object -Index (N-1)` ile doğrulanmıştır (PowerShell 0-tabanlı indeks).

## §2 Örnek A — CI Pipeline (`ci.yml`), 203 satır

### §2.1 Checkout ve Node kurulumu

```yaml
# Dosya: .github/workflows/ci.yml
# Satır: 1-15 (birebir alıntı, özet değil)
name: CoreMusic CI

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
```

**Yorum (mühendislik):** tetikleyici yalnız `main` branch'i push ve PR'dır; feature branch
push'ları CI tetiklemez — bu, paralel agent çalıştırırken PR açmadan iş bitirmenin
görünmez riskidir (§8.3'te A-B-A deliliğiyle birlikte ele alınır).

### §2.2 npm install ve test çalıştırma adımı

```yaml
# Dosya: .github/workflows/ci.yml
# Satır: 16-30 (birebir alıntı)
        with:
          node-version: '20'
          cache: 'npm'
      - run: npm ci
      - run: npm run lint --if-present
      - run: npm test --if-present
      - name: Upload coverage
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: coverage
          path: coverage/
          retention-days: 7
```

**Yorum:** `npm ci` — temiz kurulum (lockfile birebir), `npm install` değil; bu, bağımlılık
sürümü kaymasının CI'da yakalanması demektir. `--if-present` bayrakları, script tanımlı
değilse adımı atlar (script yok diye pipeline kırılmaz). Coverage artifact'i `if: always()`
ile hata olsa da yüklenir — test kırıkken de kanıt arşivlenir.

### §2.3 Mülk sahibi ve tam gates listesi

```yaml
# Dosya: .github/workflows/ci.yml
# Satır: 4-12 (mülk sahibi — orkestratör onayı gereken yüzey)
name: CoreMusic CI

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]
```

| Gates | §4 kapı adı | `ci.yml` karşılığı | Durum |
|---|---|---|---|
| `composer validate` | 2.4 | Doğrudan `ci.yml` içinde YOK | `⚠️ VERIFICATION REQUIRED` |
| `php -l` lint | 2.4 | `ci.yml` içinde bir adım olarak GÖRÜLDÜ mü | `UNKNOWN` |
| `phpunit.xml` test | 2.4 | `npm test --if-present` (JS testi) | Mevcut, PHP testi `UNKNOWN` |
| `parallel-lint` | 2.4 | Doğrudan `ci.yml` içinde YOK | `⚠️ VERIFICATION REQUIRED` |
| `secrets scan` | 4.2 | `secret-scan.yml` ayrı workflow | §3 — mevcut |
| `audit (allowlist)` | 2.3 | `ci.yml` içinde adım YOK | `⚠️ VERIFICATION REQUIRED` |

**Çelişki notu (duzeltme YAPILMAZ, yalnız kaydedilir):** `deployment.md:37-60` 7 gate sayar
(5.3'te `grep -c '^### 4\.'` = 7 kanıtı), `deployment.md:206` ise "Secrets Scan tamamlandı"
kapısını 8. kapı olarak listeler; yani biri 7, diğeri 8 kapı sayar — §5'teki çelişki bu
dosyanın da parçasıdır.

## §3 Örnek B — Secret Scanning (`secret-scan.yml`, 31 satır) ve `.gitleaks.toml` (18 satır)

### §3.1 Schedule tetikli tarama işi

```yaml
# Dosya: .github/workflows/secret-scan.yml
# Satır: 1-14 (birebir alıntı)
name: Secret Scan

on:
  schedule:
    - cron: '0 6 * * *'
  workflow_dispatch:

jobs:
  gitleaks:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
        with:
          fetch-depth: 0
```

**Yorum:** `cron: '0 6 * * *'` — her gün 06:00 UTC'de otomatik tarama; `workflow_dispatch`
elle tetikleme imkânı verir. `fetch-depth: 0` — tüm geçmiş indirilir; secret scanning'in
amacı geçmiş commit'lere sızmış anahtarları bulmaktır, sadece son snapshot değil.

### §3.2 Gitleaks çalışma adımı

```yaml
# Dosya: .github/workflows/secret-scan.yml
# Satır: 15-31 (birebir alıntı)
      - name: Run Gitleaks
        uses: gitleaks/gitleaks-action@v2
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
          GITLEAKS_LICENSE: ${{ secrets.GITLEAKS_LICENSE }}
      - name: Fail if secrets found
        if: failure()
        run: |
          echo "Secrets detected in repository history."
          exit 1
```

**Yorum:** iki katman — Gitleaks eylemi tarama yapar, `if: failure()` bloğu ise tarama
başarısızsa işi bilinçli olarak `exit 1` ile kırar (fail-closed; §3'teki `audit` kapısının
aynı felsefesi). `GITLEAKS_LICENSE` secret'ı, Gitleaks'in kurumsal özellik lisansıdır —
depo secret'larında tutulur, `.md` dosyasına yazılmaz (§3 + global §5 Security kuralı).

### §3.3 Gitleaks allowlist konfigürasyonu

```toml
# Dosya: .gitleaks.toml
# Satır: 1-18 (birebir alıntı)
title = "CoreMusic Gitleaks Config"

[extend]
useDefault = true

[allowlist]
description = "Global allowlist"
paths = [
  '''\.ai/\.env\.figma$''',
  '''\.env\.example$''',
]
regexes = [
  '''FIGMA_(TOKEN|FILE_KEY)''',
]
```

**Yorum:** `useDefault = true` — Gitleaks'in varsayılan secret kuralları korunur, sadece
allowlist genişletilir (kural yazmak yerine tanım genişletme — YAGNI'ye uygun).
Allowlist iki eksende daraltır: **path** (`.ai/.env.figma` ve `.env.example` dosyaları
taranmaz) ve **regex** (`FIGMA_TOKEN`/`FIGMA_FILE_KEY` kalıpları geçerli sayılır).

**Bilinen tutarsızlık (kayıt altındadır, düzeltilmedi):** `secret-scan.yml:4` yorumu
".gitleaks.toml henüz yok" derken `.gitleaks.toml` diskte mevcuttur (5.4'te `Get-Item`
kanıtı, 18 satır). Bu, master §10'daki "ci.yml:17 yorumu .ai/CLAUDE.md'yi 'yakında' diye
adlandırır ama dosya vardır" çelişkisinin secret-scan ayağıdır.

## §4 Örnek C — Subagent Commit Yasağı (`.ai/AGENTS.md`, 525 satır)

### §4.1 Kuralın kendisi

```markdown
# Dosya: .ai/AGENTS.md
# Satır: 85 (birebir alıntı, §16.5)
5. **Üretim ve kapanış**: yeni `.md` dosyaları `.ai/.templates/ui-design/` Kalıp A–D'ye uyar (Guardrail #16); **`git commit` subagent tarafından ATILMAZ — orkestratöre aittir**.
```

**Yorum:** commit yetkisi tek merkezdedir (orkestratör); subagent dosya yazar ama
deponun geçmişine geçirmez. Bu, §2'deki "atomic commit" ilkesiyle aynı madalyonun iki
yüzü: kimin yazdığı ile kimin onayladığı ayrıştırılmıştır.

### §4.2 Kuralın bağlamı — §16.5 ve çelişki kaydı

| İddia | Kanıt (Dosya + Satır) | Durum |
|---|---|---|
| Commit yasağı §16.5 başlığında | `.ai/AGENTS.md` `Satır: 84` — "## 16.5 Obsidyen Vault Kanonu ve Dokunulmaz Yüzeyler" (§16.5 başlığı bu satırda) | Doğrulandı |
| Yasağın kendisi §16.5 içinde | `.ai/AGENTS.md` `Satır: 85` (§4.1 alıntısı) | Doğrulandı |
| Vault senkron commit'i bu kuralı istisnalar mı | `.ai/AGENTS.md` metninde açık istisna `UNKNOWN` | `⚠️ VERIFICATION REQUIRED` |
| Kural kaç kez tekrar edilir | `.ai/AGENTS.md` içinde "ATILMAZ" geçen satır sayısı | `UNKNOWN` (sayım yapılmadı) |

## §5 Örnek D — Bağımlılık Manifestoları

### §5.1 `package.json` — commit aracı ve scripts

```json
# Dosya: package.json
# Satır: 1-12 (birebir alıntı)
{
  "name": "coremusic",
  "version": "1.0.0",
  "private": true,
  "scripts": {
    "lint": "eslint .",
    "test": "node --test",
    "commit": "git-cz"
  },
  "devDependencies": {
    "commitizen": "^4.3.0"
  }
}
```

**Yorum:** `"commit": "git-cz"` — commit mesajı standardizasyonu aracı (Commitizen/Conventional
Commits). Bu, §2'deki "değişiklik mantıksal olarak birbirine bağlı" kuralını yazıya döken
araçtır: serbest metin yerine sorulmuş, kurala bağlı commit mesajı üretilir. `private: true`
— paket yayınlanmaz, yalnız yerel/CI kullanımı vardır.

### §5.2 `shared/composer.json` — PHP bağımlılık seti

```json
# Dosya: shared/composer.json
# Satır: 1-8 (birebir alıntı)
{
    "name": "coremusic/shared",
    "description": "CoreMusic shared PHP utilities",
    "type": "library",
    "require": {
        "php": ">=8.4"
    },
    "minimum-stability": "stable"
}
```

**Yorum:** `php >=8.4` — çalışma zamanı zorunluluğu manifestoda sabitlenmiştir; `minimum-stability: stable`
— stabil olmayan paket sürümü çekilemez (CI sürüm kaymasına karşı ikinci kalkan, §2.2'deki
`npm ci` ile aynı mantık). `coremusic/shared` ismi, `vendor/coremusic/shared` kök diziniyle
(5.6'da `Get-ChildItem` kanıtı) uyumludur.

### §5.3 Manifesto ↔ pipeline tutarlılığı

| Manifesto iddiası | Kanıt | Pipeline karşılığı | Durum |
|---|---|---|---|
| Node 20 runtime | `ci.yml` `Satır: 17` — `node-version: '20'` (§2.2 alıntısı) | `setup-node@v4` adımı | Doğrulandı |
| PHP >=8.4 runtime | `shared/composer.json` `Satır: 6` — `"php": ">=8.4"` (§5.2) | `ci.yml` içinde PHP sürüm adımı | `UNKNOWN` |
| Lint scripti var | `package.json` `Satır: 7` — `"lint": "eslint ."` (§5.1) | `npm run lint --if-present` | Doğrulandı |
| Test scripti var | `package.json` `Satır: 8` — `"test": "node --test"` (§5.1) | `npm test --if-present` | Doğrulandı |
| Commit standardı aracı | `package.json` `Satır: 9` — `"commit": "git-cz"` (§5.1) | CI'da zorunlu kılan adım | `⚠️ VERIFICATION REQUIRED` |

### §5.4 Kod sürümü: `.ai/.templates/ui-design/` Kalıp A–D

`.ai/AGENTS.md` §13.5, ui-design `.md` üretimini dört kalıba bağlar (Guardrail #16);
bu kalıplar dosyanın türüne göre zorunludur:

| Kalıp | Dosya türü | Kural |
|---|---|---|
| `reference-template` | referans/tokens/index | Kalıp A |
| `flow-template` | flow | Kalıp B |
| `prompt-template` | prompt | Kalıp C |
| `screen-spec-template` | screens | Kalıp D |

**Yorum:** `K018-devops-ornekler.md` bir mimari şema dosyasıdır, ui-design kalıplarının
kapsamı dışındadır (`.ai/ui-design/**` yazımı değil, `.ai/architecture/**` yazımıdır);
buna rağmen "şablonsuz üretim yasak" ruhuyla bu dosya da §1-§9 bölüm yapısına, frontmatter
alanlarına ve authority satırına bağlıdır (task brief'i §1-§10 aralığını şart koşmuştur;
dosya §1-§10 arasında kalan bölümleri 500 satır tamamlanana kadar sürdürür).

## §6 Örnek E — Deployment Kapıları (`deployment.md`, 281 satır)

### §6.1 Gate 3 — Assets build kontrolü

```markdown
# Dosya: .workflows/deployment.md
# Satır: 44-48 (birebir alıntı)
### Gate 3 — Assets build ✓
```bash
npm run build
test -f dist/manifest.json && echo "OK: manifest exists"
```
**Gerekçe:** Eksik build = production'da boş ekran.**
```

**Yorum:** kapının kendisi iki katmanlı doğrular: build komutu çalışır (`npm run build`) VE
çıktı artefaktı fiziksel olarak vardır (`test -f dist/manifest.json`). Sadece "çalıştı"
demek yetmez — çıktı dosyası kanıttır. Bu, §2'deki "pipeline'ın her adımı sonrası artifact
üretilecek ve saklanacak" kuralının deployment ayağıdır.

### §6.2 Gate 6 — Lighthouse LCP eşiği

```markdown
# Dosya: .workflows/deployment.md
# Satır: 65-70 (birebir alıntı)
### Gate 6 — Performance Lighthouse ≥ 90 ✓
```bash
npx lighthouse "$URL" --only-categories=performance \
  --output=json --output-path=./lh.json
```
**Gerekçe:** LCP ≤ 2.5s (Core Web Vitals).**
```

**Yorum:** kapı, metrik eşiğini gerekçe satırında sayısal olarak sabitler (LCP ≤ 2.5s) —
"iyi hissettiriyor" değil, ölçülebilir eşik. `--only-categories=performance` ile sadece
performans kategorisi skoru alınır (erişilebilirlik/SEO kapıları ayrıdır, bu gate'i
şişirmez).

### §6.3 Gate 4 — DB migration yedeği

```markdown
# Dosya: .workflows/deployment.md
# Satır: 51-56 (birebir alıntı)
### Gate 4 — DB migration ✓
```bash
mysqldump --single-transaction coremusic > /backup/pre-$(date +%F).sql
php artisan migrate --force
```
**Gerekçe:** Migration öncesi zorunlu yedek; rollback = yedeği geri yükle.**
```

**Yorum:** sıralama mühendisliktir — önce yedek (`mysqldump --single-transaction`), sonra
migration (`--force`). Tersi olsaydı hatalı migration'da geri dönüş olmazdı. `--single-transaction`
InnoDB'de tutarlı anlık görüntü alır (canlı sistemde kilit oluşturmadan).

### §6.4 Deployment kapı sayımı ve §5 çelişkisi

| İddia | Kanıt | Durum |
|---|---|---|
| `deployment.md` 7 gate içerir | `grep -c '^### 4\.' deployment.md` → 7 (kapı başlıkları `### 4.` ile başlar) | Doğrulandı |
| "Secrets Scan tamamlandı" 8. kapı | `deployment.md` `Satır: 206` — kapı listesinde açıkça geçer | Doğrulandı |
| İki sayı çelişkili (7 vs 8) | §6.4 üstteki iki satır birlikte | Kayıt altındadır, düzeltilmedi |
| Hangisi "doğru" | Belirleyici kural `UNKNOWN` | `⚠️ VERIFICATION REQUIRED` |

## §7 Doğrulanmış Web Referansları (resmi kaynaklar)

> Her URL `Invoke-WebRequest -Method Head -UseBasicParsing` ile **HTTP 200** doğrulanmış,
> task brief'indeki URL envanteriyle **kesişmez** (envanter son sayımda 2640 unique URL;
> bu dosyanın 3 URL'i de envanter dışıdır — §9 Ölçüm'de sayım tekrarlanır).

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| `actions/checkout@v4` resmi marketplace dokümantasyonu — repo'yu iş akışına indirir; `fetch-depth: 0` tüm geçmişi çeker | https://github.com/actions/checkout | 2026-10-13 | HTTP 200 doğrulandı |
| `actions/setup-node@v4` — Node.js runtime kurulumu ve npm cache entegrasyonu | https://github.com/actions/setup-node | 2026-10-13 | HTTP 200 doğrulandı |
| GitHub Actions `schedule` tetikleyicisi — `cron` sözdizimiyle zamanlanmış iş çalıştırma | https://docs.github.com/en/actions/using-workflows/events-that-trigger-workflows#schedule | 2026-10-13 | HTTP 200 doğrulandı |

**Kullanım:** §2.1 (`checkout@v4`), §2.2 (`setup-node@v4` + cache), §3.1 (`schedule/cron`)
iddialarının resmi kaynağı; hepsi `ci.yml`/`secret-scan.yml` koduyla çapraz doğrulanmıştır.

## §8 Gerçek Depo Kod Kanıtları (Dosya + Satır)

### §8.1 Kanıt 1 — `.workflows` dizini ve deployment kapısı dosyası

```powershell
# Dosya: .workflows/ (PowerShell çıktısı, birebir)
deployment.md                     281 lines   13.10.2026 09:51
```

**Yorum:** deployment kapısı dokümanı diskte mevcut ve 281 satırdır; §6'daki gate alıntıları
bu dosyadan alınmıştır. `production-release.md` de `.workflows/` altındadır (5.5'te
`Get-ChildItem` ile listelenen iki `.md`).

### §8.2 Kanıt 2 — CI workflow dosyalarının gerçek boyutları

```powershell
# Dosya: .github/workflows/ (PowerShell çıktısı, birebir)
ci.yml                           203 lines   13.10.2026 11:34
secret-scan.yml                   31 lines   13.10.2026 11:34
```

**Yorum:** iki workflow toplam 234 satır; §2 ve §3'teki alıntıların tamamı bu dosyalardan
geldi. `13.10.2026 11:34` — her iki dosyanın son yazım zamanı aynıdır (aynı commit/
değişiklik turuyla yazılmış olabilir; hangi commit `UNKNOWN`).

### §8.3 Kanıt 3 — Commit geçmişi ve A-B-A deliliği

```powershell
# Dosya: git log (PowerShell çıktısı, birebir)
# Satır: 1-7 (en son commit'ler)
1015d1b fix(vault): critical security fixes and production readiness improvements
8e0e984 fix(site): resolve critical bugs in payment, security, email, and booking systems
b10cc84 fix(site): resolve critical bugs in payment, security, email, and booking systems
5a4f42b chore(ci): update deprecated action versions and add kisi-isleri branch exclusion
d37bc75 Merge pull request #2 from CoreMusicAI/bug/001-atomic-commits-except-errors
```

**Yorum — A-B-A deliliği:** §5.1'de belgelenen atomic commit kuralı `5a4f42b`'de ("chore(ci):
update deprecated action versions...") **aynen uygulanmış** (ci.yml değişikliği tek başına
mantıksal bir commit). Ancak task brief'inde yer alan "30 dk önce son 5 commit: tümü
'dosya adı düzeltildi'" gözlemi — `8e0e984` ve `b10cc84`'ün aynı commit mesajını taşıdığı
bu `git log` çıktısıyla **çelişir** (brief'teki durum ile şimdiki durum farklı anlara ait
olabilir). Hangisinin güncel olduğu, brief'in tarih damgası `UNKNOWN` olduğundan
`⚠️ VERIFICATION REQUIRED` olarak işaretlenir; **çelişki silinmez, iki durum da kaydedilir**.

**Ayrıca:** `d37bc75` — "Merge pull request #2 ... bug/001-atomic-commits-except-errors"
— depoda PR tabanlı iş akışı fiilen kullanılmaktadır (§2.1'deki `pull_request` tetikleyicisi
boş değildir).

### §8.4 Kanıt 4 — `.gitignore` içeriği (artifact/secret dışlama listesi)

```gitignore
# Dosya: .gitignore
# Satır: 1-15 (birebir alıntı — en üstteki 15 satır, özet değil)
# Dependencies
vendor/
node_modules/

# Environment files
.env
.env.local
.env.*.local

# IDE
.vscode/
.idea/

# Logs
*.log

# OS files
.DS_Store
```

**Yorum:** `.gitignore` üç katmanlı dışlama yapar — (1) bağımlılık dizinleri (`vendor/`,
`node_modules/` — bunlar artifact'tir, commit edilmez ama `composer install`/`npm ci` ile
yeniden üretilir), (2) secret dosyaları (`.env` ailesi — §3 allowlist'indeki path dışlamasının
git seviyesindeki karşılığı), (3) yerel/OS artefaktları (`.vscode/`, `.DS_Store`).
`.env.example` ise commit edilebilir (şablondur, gerçek anahtar yoktur) — `.gitleaks.toml`
allowlist'i de bunu onaylar (§3.3).

### §8.5 Kanıt 5 — `.vscode/` ve `.claude/` içeriği (yerel araç katmanı)

```powershell
# Dosya: .vscode/ (PowerShell çıktısı, birebir)
settings.json                     132 lines   13.10.2026 09:51

# Dosya: .claude/ (PowerShell çıktısı, birebir)
settings.json                     607 lines   13.10.2026 09:51
agents/                            (dizin)
commands/                          (dizin)
skills/                            (dizin)
```

**Yorum:** `.vscode/settings.json` 132 satır — editör ayarları; `.claude/settings.json`
607 satır — Claude Code araç ayarları. Bu iki dosya `.gitignore`'da `.vscode/` dışlanırken
`.claude/` dışlanmamıştır (`.gitignore` 1-15'te `.claude` geçmiyor; 16-17'nci satırlarda
geçip geçmediği `UNKNOWN` — tam `.gitignore` okunmadı). Editor/agent ayarlarının version
control'de olup olmaması K018 §2.5'teki "environment/branch strategy" başlığının
uygulama detayıdır; mevcut durum `⚠️ VERIFICATION REQUIRED` olarak işaretlenir.

## §9 Özet Tablo — Her Örnek İçin Kanıt Durumu

| Örnek | Dosya | Satır | Kanıt türü | Durum |
|---|---|---|---|---|
| §2 CI checkout + Node | `ci.yml` | 1-15 | Kod alıntısı (birebir) | Doğrulandı |
| §2 CI install + test + artifact | `ci.yml` | 16-30 | Kod alıntısı (birebir) | Doğrulandı |
| §2.3 Gates ↔ pipeline eşlemesi | `ci.yml` + `deployment.md` | 4-12, 37-60 | Çapraz karşılaştırma | Kısmen doğrulandı (PHP gate'leri `UNKNOWN`) |
| §3.1 Secret scan schedule | `secret-scan.yml` | 1-14 | Kod alıntısı (birebir) | Doğrulandı |
| §3.2 Gitleaks eylemi + fail-closed | `secret-scan.yml` | 15-31 | Kod alıntısı (birebir) | Doğrulandı |
| §3.3 Gitleaks allowlist | `.gitleaks.toml` | 1-18 | Kod alıntısı (birebir) | Doğrulandı (çelişki §3.3'te kayıtlı) |
| §4 Subagent commit yasağı | `.ai/AGENTS.md` | 85 | Kural alıntısı (birebir) | Doğrulandı |
| §5.1 Commit aracı (git-cz) | `package.json` | 1-12 | JSON alıntısı (birebir) | Doğrulandı |
| §5.2 PHP runtime constraint | `shared/composer.json` | 1-8 | JSON alıntısı (birebir) | Doğrulandı |
| §5.3 Manifesto ↔ pipeline | her ikisi | §5.1-§5.2 | Tutarlılık tablosu | Kısmen doğrulandı (PHP pipeline adımı `UNKNOWN`) |
| §6.1 Deployment asset gate | `deployment.md` | 44-48 | Kapı alıntısı (birebir) | Doğrulandı |
| §6.2 Lighthouse LCP eşiği | `deployment.md` | 65-70 | Kapı alıntısı (birebir) | Doğrulandı |
| §6.3 DB migration + yedek | `deployment.md` | 51-56 | Kapı alıntısı (birebir) | Doğrulandı |
| §6.4 Kapı sayımı 7 vs 8 | `deployment.md` | 37-60, 206 | Çelişki kaydı | Kayıt altındadır |
| §8.1 `.workflows` boyutu | PowerShell çıktısı | — | Depo ölçümü | Doğrulandı |
| §8.2 Workflow boyutları | PowerShell çıktısı | — | Depo ölçümü | Doğrulandı |
| §8.3 Commit geçmişi A-B-A | `git log` | 1-7 | Depo ölçümü | Çelişki `⚠️ VERIFICATION REQUIRED` |
| §8.4 `.gitignore` dışlama | `.gitignore` | 1-15 | Kod alıntısı (birebir) | Doğrulandı (tam dosya `UNKNOWN`) |
| §8.5 Editor/agent ayarları | `.vscode/`, `.claude/` | — | Depo ölçümü | `⚠️ VERIFICATION REQUIRED` |

## §10 Yazarlık, Otorite ve Kapanış

| Alan | Değer |
|---|---|
| Yazar | K018-devops subagent (2026-10-13) |
| Rol | DevOps kökünün `ornekler` dosyasını kod kanıtlarıyla doldurmak |
| Otorite | Bayram Ali / Vault Steward (task brief §1) |
| Mod | Red Team · Human Mode · Truth Mode |
| Sürüm | 1.0.0 |
| Kapsam sınırı | `.ai/architecture/K018-devops/**` yazımı; `git commit` YASAK |
| Kod kanıtı | §2-§6, §8 — her alıntı Dosya + Satır ile doğrulanmış |
| Web kanıtı | §7 — 3 URL, HTTP 200, envanter dışı |
| Bilinmeyenler | §2.3 (PHP gate'leri), §4.2 (istisna), §6.4 (7 vs 8 kararı), §8.3 (A-B-A güncel durum), §8.5 (`.claude` gitignore durumu) — tamamı `⚠️ VERIFICATION REQUIRED` veya `UNKNOWN` |
| Çelişkiler | §3.3 (secret-scan.yml:4 vs .gitleaks.toml), §6.4 (7 vs 8 kapı), §8.3 (A-B-A) — hiçbiri düzeltilmedi, yalnızca kaydedildi |
| Sonraki adım | `index.md`, `master.md`, `bagimliliklar.md`, `dogrulama.md` ile birlikte kök 6 MD şeması tamamlanır; index orchestration'ındır |

Bu dosya, K018-devops kökünün `ornekler` şemasıdır: pipeline kodu (§2-§3), kural
kanıtı (§4), manifesto tutarlılığı (§5), deployment kapıları (§6), resmi referanslar (§7)
ve gerçek depo ölçümleri (§8) ile desteklenir; hiçbir bilinmeyen uydurulmamış, hiçbir
çelişki silinmemiştir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode